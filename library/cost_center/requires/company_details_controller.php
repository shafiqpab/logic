<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if ($action=="company_list_view")
{
	//id,group_id,company_name,company_short_name,service_cost_allocation,posting_pre_year,statutory_account,contract_person,cfo,company_nature,core_business,email,website,ac_code_length,profit_center_affected,plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,trade_license_no,incorporation_no,erc_no,irc_no,tin_number,vat_number,epb_reg_no,trade_license_renewal,erc_expiry_date,irc_expiry_date,graph_color,logo_location,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted,is_locked
	$group_name=return_library_array( "select group_name,id from lib_group", "id", "group_name"  );
    $arr=array (1=>$group_name);
    echo  create_list_view ( "list_view", "Company Name,Group Name,Short Name,Contact Person,Email", "130,200,200,100","1000","220",0, "select company_name,group_id,company_short_name,contract_person,email,id from lib_company where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,group_id,0,0,0", $arr , "company_name,group_id,company_short_name,contract_person,email", "../cost_center/requires/company_details_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,0' ) ;
	//function create_list_view( $table_id, $tbl_header_arr, $td_width_arr, $tbl_width, $tbl_height, $tbl_border, $query, $onclick_fnc_name, $onclick_fnc_param_db_arr, $onclick_fnc_param_sttc_arr,  $show_sl, $field_printed_from_array_arr,  $data_array_name_arr, $qry_field_list_array, $controller_file_path , $filter_grid_fnc, $fld_type_arr )

}
//id,group_id,company_name,company_short_name,service_cost_allocation,posting_pre_year,statutory_account,contract_person,cfo,company_nature,core_business,email,website,ac_code_length,profit_center_affected, plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, trade_license_no, incorporation_no, erc_no,irc_no, tin_number, vat_number, epb_reg_no, trade_license_renewal,erc_expiry_date,irc_expiry_date,graph_color,logo_location,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted,is_locked
if ($action=="load_php_data_to_form")
{	
	$nameArray=sql_select( "select id, group_id, company_name, company_short_name, service_cost_allocation, posting_pre_year,statutory_account, contract_person,ceo,cfo,company_nature, core_business, email,website,ac_code_length,profit_center_affected, plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, trade_license_no, incorporation_no, erc_no,irc_no, tin_number,contact_no, vat_number, epb_reg_no, trade_license_renewal,erc_expiry_date,irc_expiry_date,bang_bank_reg_no,bin_no,graph_color,logo_location,status_active,alter_standard_per,reject_standard_per,business_nature, rex_no, rex_reg_date from lib_company where id='$data'" );
 //echo  "select id, group_id, company_name, company_short_name, service_cost_allocation, posting_pre_year,statutory_account, contract_person,cfo,company_nature, core_business, email,website,ac_code_length,profit_center_affected, plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, trade_license_no, incorporation_no, erc_no,irc_no, tin_number, vat_number, epb_reg_no, trade_license_renewal,erc_expiry_date,irc_expiry_date,graph_color,logo_location from  lib_company where id='$data'" ;
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('cbo_group_name').value = '".($inf[csf("group_id")])."';\n";    
		echo "document.getElementById('txt_company_name').value  = '".($inf[csf("company_name")])."';\n";
		echo "document.getElementById('txt_company_short_name').value = '".($inf[csf("company_short_name")])."';\n";    
		echo "document.getElementById('cbo_service_cost_allocation').value  = '".($inf[csf("service_cost_allocation")])."';\n";
		echo "document.getElementById('cbo_posting_in_previous_yr').value  = '".($inf[csf("posting_pre_year")])."';\n";
		echo "document.getElementById('cbo_statutory_account').value = '".($inf[csf("statutory_account")])."';\n";
		echo "document.getElementById('txt_ceo').value = '".($inf[csf("ceo")])."';\n";    
    	echo "document.getElementById('txt_cfo').value = '".($inf[csf("cfo")])."';\n"; 
		echo "document.getElementById('cbo_company_nature').value = '".($inf[csf("company_nature")])."';\n";
		echo "document.getElementById('cbo_core_business').value = '".($inf[csf("core_business")])."';\n";
		echo "document.getElementById('txt_email').value = '".($inf[csf("email")])."';\n";    
		echo "document.getElementById('txt_website').value  = '".($inf[csf("website")])."';\n";
		echo "document.getElementById('txt_ac_code_length').value = '".($inf[csf("ac_code_length")])."';\n";    
		echo "document.getElementById('cbo_profit_center_affected').value  = '".($inf[csf("profit_center_affected")])."';\n";
		echo "document.getElementById('txt_contact_person').value  = '".($inf[csf("contract_person")])."';\n";
		echo "document.getElementById('txt_plot_no').value  = '".($inf[csf("plot_no")])."';\n";
		echo "document.getElementById('txt_level_no').value = '".($inf[csf("level_no")])."';\n";
		echo "document.getElementById('txt_road_no').value = '".($inf[csf("road_no")])."';\n";    
    	echo "document.getElementById('txt_block_no').value = '".($inf[csf("block_no")])."';\n"; 
		echo "document.getElementById('cbo_country').value = '".($inf[csf("country_id")])."';\n";
		echo "document.getElementById('txt_province').value = '".($inf[csf("province")])."';\n";
		echo "document.getElementById('txt_city_town').value  = '".($inf[csf("city")])."';\n";
		echo "document.getElementById('txt_zip_code').value  = '".($inf[csf("zip_code")])."';\n";
		echo "document.getElementById('txt_contact_no').value  = '".($inf[csf("contact_no")])."';\n";
		echo "document.getElementById('txt_trade_license').value = '".($inf[csf("trade_license_no")])."';\n";
		echo "document.getElementById('txt_incorporation_no').value = '".($inf[csf("incorporation_no")])."';\n";    
    	echo "document.getElementById('txt_erc_no').value = '".($inf[csf("erc_no")])."';\n"; 
		echo "document.getElementById('txt_irc_no').value = '".($inf[csf("irc_no")])."';\n";
		echo "document.getElementById('txt_bin_no').value = '".($inf[csf("bin_no")])."';\n";
		echo "document.getElementById('txt_rex_number').value = '".($inf[csf("rex_no")])."';\n";
		echo "document.getElementById('txt_rex_date').value = '".($inf[csf("rex_reg_date")])."';\n";
		
		echo "document.getElementById('txt_epb_reg_no').value = '".($inf[csf("epb_reg_no")])."';\n"; 
		if($inf[csf("trade_license_renewal")]!="")
		{ 
		echo "document.getElementById('txt_trade_license_renewal').value  = '".change_date_format(($inf[csf("trade_license_renewal")]))."';\n";
		}
		if($inf[csf("erc_expiry_date")]!="")
		{
		echo "document.getElementById('txt_erc_expiry_date').value = '".change_date_format(($inf[csf("erc_expiry_date")]))."';\n";
		}
		if($inf[csf("irc_expiry_date")]!="")
		{
		echo "document.getElementById('txt_irc_expiry_date').value = '".change_date_format(($inf[csf("irc_expiry_date")]))."';\n";  
		}
    	echo "document.getElementById('txt_tin_number').value = '".($inf[csf("tin_number")])."';\n"; 
		echo "document.getElementById('txt_vat_number').value = '".($inf[csf("vat_number")])."';\n";
		echo "document.getElementById('txt_bangladeh_bank_reg_no').value = '".($inf[csf("bang_bank_reg_no")])."';\n";  
		echo "document.getElementById('cbo_status').value = '".($inf[csf("status_active")])."';\n";
		
		echo "document.getElementById('txt_alter_standard').value = '".($inf[csf("alter_standard_per")])."';\n";
		echo "document.getElementById('txt_reject_standard').value = '".($inf[csf("reject_standard_per")])."';\n";
		
		echo "$('#update').removeClass('formbutton_disabled').addClass('formbutton');\n";  
		echo "document.getElementById('update_id').value = '".($inf[csf("id")])."';\n";    
		//echo "document.getElementById('upload').value = '".($inf[csf("upload")])."';\n";
		//echo "document.getElementById('files').value = '".($inf[csf("files")])."';\n";    
		//echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_company_details',1);\n";
		//cbo_business_nature
		 echo "set_multiselect('cbo_business_nature','8','1','".$inf[csf("business_nature")]."');\n;"; 
		   
	}
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{
		if (is_duplicate_field( "company_name", "lib_company", "company_name=$txt_company_name and is_deleted=0" ) == 1)
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
			
			$id=return_next_id( "id", "lib_company", 1 ) ;
			$field_array="id,group_id,company_name,company_short_name,service_cost_allocation,posting_pre_year,statutory_account,contract_person,ceo,cfo,company_nature,core_business,email,website,ac_code_length,profit_center_affected, plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, trade_license_no, incorporation_no, erc_no,irc_no, tin_number,contact_no, vat_number, epb_reg_no, trade_license_renewal, erc_expiry_date, irc_expiry_date, bang_bank_reg_no, graph_color, logo_location, alter_standard_per, reject_standard_per, business_nature, bin_no, rex_no, rex_reg_date, inserted_by, insert_date, status_active, is_deleted";
	
			$data_array="(".$id.",".$cbo_group_name.",".$txt_company_name.",".$txt_company_short_name.",".$cbo_service_cost_allocation.",".$cbo_posting_in_previous_yr.",".$cbo_statutory_account.",".$txt_contact_person.",".$txt_ceo.",".$txt_cfo.",".$cbo_company_nature.",".$cbo_core_business.",".$txt_email.",".$txt_website.",".$txt_ac_code_length.",".$cbo_profit_center_affected.",".$txt_plot_no.",".$txt_level_no.",".$txt_road_no.",".$txt_block_no.",".$cbo_country.",".$txt_province.",".$txt_city_town.",".$txt_zip_code.",".$txt_trade_license.",".$txt_incorporation_no.",".$txt_erc_no.",".$txt_irc_no.",".$txt_tin_number.",".$txt_contact_no.",".$txt_vat_number.",".$txt_epb_reg_no.",".$txt_trade_license_renewal.",".$txt_erc_expiry_date.",".$txt_irc_expiry_date.",".$txt_bangladeh_bank_reg_no.",'','',".$txt_alter_standard.",".$txt_reject_standard.",".$cbo_business_nature.",".$txt_bin_no.",".$txt_rex_number.",".$txt_rex_date.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",'0')";
			
			$rID=sql_insert("lib_company",$field_array,$data_array,1);
			//echo $rID; die;
			if($db_type==0)
			{
				if($rID ){
					mysql_query("COMMIT");  
					echo "0**".$rID;
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID;
				}
			}
			
			if($db_type==2 || $db_type==1 )
			{
				if($rID )
					{
						oci_commit($con);   
						echo "0**".$rID;
					}
				else{
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
		if (is_duplicate_field( "company_name", "lib_company", "company_name=$txt_company_name and id!=$update_id and is_deleted=0" ) == 1)
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
			
			$field_array="group_id*service_cost_allocation*posting_pre_year*statutory_account*contract_person*ceo*cfo*company_nature*core_business*email*website*ac_code_length*profit_center_affected* plot_no* level_no* road_no* block_no* country_id* province* city* zip_code* trade_license_no* incorporation_no* erc_no*irc_no* tin_number*contact_no* vat_number* epb_reg_no* trade_license_renewal*erc_expiry_date*irc_expiry_date*bang_bank_reg_no*graph_color*logo_location* alter_standard_per* reject_standard_per*business_nature*bin_no*rex_no*rex_reg_date*updated_by* update_date* status_active* is_deleted";//company_name*company_short_name* --dont open Kausar 08-08-22
	
			$data_array="".$cbo_group_name."*".$cbo_service_cost_allocation."*".$cbo_posting_in_previous_yr."*".$cbo_statutory_account."*".$txt_contact_person."*".$txt_ceo."*".$txt_cfo."*".$cbo_company_nature."*".$cbo_core_business."*".$txt_email."*".$txt_website."*".$txt_ac_code_length."*".$cbo_profit_center_affected."*".$txt_plot_no."*".$txt_level_no."*".$txt_road_no."*".$txt_block_no."*".$cbo_country."*".$txt_province."*".$txt_city_town."*".$txt_zip_code."*".$txt_trade_license."*".$txt_incorporation_no."*".$txt_erc_no."*".$txt_irc_no."*".$txt_tin_number."*".$txt_contact_no."*".$txt_vat_number."*".$txt_epb_reg_no."*".$txt_trade_license_renewal."*".$txt_erc_expiry_date."*".$txt_irc_expiry_date."*".$txt_bangladeh_bank_reg_no."*''*''*".$txt_alter_standard."*".$txt_reject_standard."*".$cbo_business_nature."*".$txt_bin_no."*".$txt_rex_number."*".$txt_rex_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*'0'";//."*".$txt_company_name."*".$txt_company_short_name --dont open Kausar  08-08-22
			
			$rID=sql_update("lib_company",$field_array,$data_array,"id","".$update_id."",1);
			
			if($db_type==0)
			{
				if($rID ){
					mysql_query("COMMIT");  
					echo "1**".$rID;
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID;
				}
			}
			if($db_type==2 || $db_type==1 )
			{
		       if($rID )
			    {
					oci_commit($con);   
					echo "1**".$rID;
				}
				else{
					oci_rollback($con);
					echo "10**".$rID;
				}
			}
			disconnect($con);
			die;
		}
		
	}
	
	else if ($operation==2)   // Update Here
	{
		if (is_duplicate_field( "b.tag_company", "lib_buyer a, lib_buyer_tag_company b", "a.id=b.buyer_id and b.tag_company=$update_id and a.is_deleted=0" ) == 1)
		{
			echo "13**0"; die;
		}
		if (is_duplicate_field( "b.tag_company", "lib_supplier a, lib_supplier_tag_company b", "a.id=b.supplier_id and b.tag_company=$update_id and a.is_deleted=0" ) == 1)
		{
			echo "13**0"; die;
		}
		if (is_duplicate_field( "company_id", "lib_location", "company_id=$update_id and is_deleted=0" ) == 1)
		{
			echo "13**0"; die;
		}
		if (is_duplicate_field( "company_id", "lib_profit_center", "company_id=$update_id and is_deleted=0" ) == 1)
		{
			echo "13**0"; die;
		}
		if (is_duplicate_field( "company_id", "lib_prod_floor", "company_id=$update_id and is_deleted=0" ) == 1)
		{
			echo "13**0"; die;
		}  
		if (is_duplicate_field( "company_id", "lib_standard_cm_entry", "company_id=$update_id and is_deleted=0" ) == 1)
		{
			echo "13**0"; die;
		}
		
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			
			$field_array="updated_by*update_date*status_active*is_deleted";
			$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
			
			$rID=sql_delete("lib_company",$field_array,$data_array,"id","".$update_id."",1);
			
			if($db_type==0)
			{
				if($rID ){
					mysql_query("COMMIT");  
					echo "2**".$rID;
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID;
				}
			}
			if($db_type==2 || $db_type==1 )
			{
				 if($rID )
			    {
					oci_commit($con);   
					echo "2**".$rID;
				}
				else{
					oci_rollback($con);
					echo "10**".$rID;
				}
			}
			disconnect($con);
			die;
		}
	}
}





?>