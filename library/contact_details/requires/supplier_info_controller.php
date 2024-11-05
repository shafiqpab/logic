<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="save_update_delete")
{
   $process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{
		if (is_duplicate_field( "supplier_name", "lib_supplier", "supplier_name=$txt_supplier_name and is_deleted=0" ) == 1)
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
			$id=return_next_id( "id", "lib_supplier", 1 );
			$field_array="id, supplier_name, short_name,contact_person, contact_no, party_type, designation, tag_company, country_id, web_site,email, address_1, address_2, address_3, address_4,remark,buyer, credit_limit_days, credit_limit_amount,credit_limit_amount_currency, discount_method, securitye_deducted, vat_to_be_deducted, ait_to_be_deducted, 	individual,supplier_nature,tag_buyer,supplier_ref,owner_name,owner_nid,owner_contact,owner_email,inserted_by, insert_date, status_active,is_deleted,tin_number,vat_number,source_id";
			$txt_supplier_name=str_replace("'", "", $txt_supplier_name);
			if (preg_match('/[\'^£$&*()#~>=_+]/', $txt_supplier_name))
			{
				echo "15**0"; die;
			}
			$txt_short_name=str_replace("'", "", $txt_short_name);
			if (preg_match('/[\'^£$&*()#~>=_+]/', $txt_short_name))
			{
				echo "15**0"; die;
			}
			$data_array="(".$id.",'".$txt_supplier_name."','".$txt_short_name."',".$txt_contact_person.",".$txt_contact_no.",".$txt_party_type_id.",".$txt_desination.",".$cbo_tag_company.",".$cbo_country.",".$txt_web_site.",".$txt_email.",".$txt_address_1st.",".$txt_address_2nd.",".$txt_address_3rd.",".$txt_address_4th.",".$txt_remark.",".$cbo_buyer.",".$txt_credit_limit_days.",".$txt_credit_limit_amount.",".$cbo_credit_limit_amount_curr.",".$cbo_discount_method.",".$cbo_security_deducted.",".$cbo_vat_to_be_deducted.",".$cbo_ait_to_be_deducted.",".$cbo_individual.",".$cbo_supplier_nature.",".$txt_tag_buyer_id.",".$txt_supplier_ref.",".$owner_name.",".$owner_nid.",".$owner_contact.",".$owner_email.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",'0',".$txt_tin_number.",".$txt_vat_number.",".$cbo_supplier_source.")";
		//	print_r($data_array);die;
			//$rID=sql_insert("lib_supplier",$field_array,$data_array,0);
			//Insert Data in  lib_supplier_party_type Table----------------------------------------

			$data_array1="";
			$party_type=explode(',',str_replace("'","",$txt_party_type_id));
			for($i=0; $i<count($party_type); $i++)
			{
				if($id_lib_supplier_party_type=="") $id_lib_supplier_party_type=return_next_id( "id", "lib_supplier_party_type", 1 ); else $id_lib_supplier_party_type=$id_lib_supplier_party_type+1;
				if($i==0) $add_comma=""; else $add_comma=",";
				$data_array1.="$add_comma(".$id_lib_supplier_party_type.",".$id.",".$party_type[$i].")";
			}
			$field_array1="id, supplier_id, party_type";
			//$rID=sql_insert("lib_supplier_party_type",$field_array1,$data_array1,0);
		
			//----------------------------------------------------------------------------------
			
			//Insert Data in  lib_supplier_tag_company Table----------------------------------------
			$data_array2="";
			$tag_company=explode(',',str_replace("'","",$cbo_tag_company));
			for($i=0; $i<count($tag_company); $i++)
			{
				if($id_lib_supplier_tag_company=="") $id_lib_supplier_tag_company=return_next_id( "id", "lib_supplier_tag_company", 1 ); else $id_lib_supplier_tag_company=$id_lib_supplier_tag_company+1;
				if($i==0) $add_comma=""; else $add_comma=",";
				$data_array2.="$add_comma(".$id_lib_supplier_tag_company.",".$id.",".$tag_company[$i].")";
			}
			$field_array2="id, supplier_id, tag_company";
			//----------------------------------------------------------------------------------
			//Insert Data in  lib_supplier_buyer Table----------------------------------------
			$data_array3="";
			$tag_buyer=explode(',',str_replace("'","",$txt_tag_buyer_id));
			for($i=0; $i<count($tag_buyer); $i++)
			{
				if($id_lib_supplier_tag_buyer=="") $id_lib_supplier_tag_buyer=return_next_id( "id", "lib_supplier_tag_buyer", 1 ); else $id_lib_supplier_tag_buyer=$id_lib_supplier_tag_buyer+1;
				if($i==0) $add_comma=""; else $add_comma=",";
				$data_array3.="$add_comma(".$id_lib_supplier_tag_buyer.",".$id.",".$tag_buyer[$i].")";
			}
			$field_array3="id, supplier_id, tag_buyer";
			
			//echo "10**insert into lib_supplier (".$field_array.") values".$data_array;die;
			// echo "10**insert into lib_supplier (".$field_array.") values".$data_array;die;
			$rID=sql_insert("lib_supplier",$field_array,$data_array,0);
			$rID1=sql_insert("lib_supplier_party_type",$field_array1,$data_array1,0);
			$rID2=sql_insert("lib_supplier_tag_company",$field_array2,$data_array2,1);
			$rID3=sql_insert("lib_supplier_tag_buyer",$field_array3,$data_array3,1);
			//----------------------------------------------------------------------------------
			 
			if($db_type==0)
			{
				if($rID && $rID1 && $rID2){
					mysql_query("COMMIT");  
					echo "0**".$rID."**".$rID1."**".$rID2."**".$rID3;
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID."**".$rID1."**".$rID2."**".$rID3;
				}
			}
			if($db_type==2 || $db_type==1 )
			{
				if($rID && $rID1 && $rID2)
					{
						oci_commit($con);   
						echo "0**".$rID."**".$rID1."**".$rID2."**".$rID3;
					}
				else
					{
						oci_rollback($con); 
						echo "10**".$rID."**".$rID1."**".$rID2."**".$rID3;
					}
			}
			disconnect($con);
			die;
		}
	}
	
	else if ($operation==1)   // Update Here
	{
		if (is_duplicate_field( "supplier_name", "lib_supplier", "supplier_name=$txt_supplier_name and id!=$update_id and is_deleted=0" ) == 1)
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
			if(str_replace("'", "", $is_posted_accounts)==1)
			{
				
				$sql_prev_data=sql_select("select tag_company from LIB_SUPPLIER where id=$update_id");
				$prev_tag_company=explode(",",$sql_prev_data[0][csf("tag_company")]);
				$current_tag_company=explode(",",str_replace("'","",$cbo_tag_company));
				$diff_tag_com=array_diff($prev_tag_company,$current_tag_company);
				if(count($diff_tag_com)>0)
				{
					echo "50** Already Posted Company Change Not Allow";oci_rollback($con);disconnect($con);die;
				}
				
				$field_array="contact_person*contact_no*party_type*designation*tag_company*country_id*web_site*email*address_1*address_2*address_3* address_4*remark*buyer*credit_limit_days*credit_limit_amount*credit_limit_amount_currency*discount_method*securitye_deducted*vat_to_be_deducted*ait_to_be_deducted*individual*supplier_nature*tag_buyer*supplier_ref*owner_name*owner_nid*owner_contact*owner_email*source_id*updated_by*update_date*status_active*is_posted_sql";

				$data_array="".$txt_contact_person."*".$txt_contact_no."*".$txt_party_type_id."*".$txt_desination."*".$cbo_tag_company."*".$cbo_country."*".$txt_web_site."*".$txt_email."*".$txt_address_1st."*".$txt_address_2nd."*".$txt_address_3rd."*".$txt_address_4th."*".$txt_remark."*".$cbo_buyer."*".$txt_credit_limit_days."*".$txt_credit_limit_amount."*".$cbo_credit_limit_amount_curr."*".$cbo_discount_method."*".$cbo_security_deducted."*".$cbo_vat_to_be_deducted."*".$cbo_ait_to_be_deducted."*".$cbo_individual."*".$cbo_supplier_nature."*".$txt_tag_buyer_id."*".$txt_supplier_ref."*".$owner_name."*".$owner_nid."*".$owner_contact."*".$owner_email."*".$cbo_supplier_source."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*'2'";
			}
			else
			{
				$field_array="supplier_name* short_name*tin_number*vat_number*contact_person*contact_no*party_type*designation*tag_company*country_id*web_site*email*address_1*address_2*address_3* address_4*remark*buyer*credit_limit_days*credit_limit_amount*credit_limit_amount_currency*discount_method*securitye_deducted*vat_to_be_deducted*ait_to_be_deducted*individual*supplier_nature*tag_buyer*supplier_ref*owner_name*owner_nid*owner_contact*owner_email*source_id*updated_by* update_date*status_active*is_deleted*is_posted_sql";
				$txt_supplier_name=str_replace("'", "", $txt_supplier_name);
				if (preg_match('/[\'^£$&*()#~>=_+]/', $txt_supplier_name))
				{
					echo "15**0"; die;
				}
				$txt_short_name=str_replace("'", "", $txt_short_name);
				if (preg_match('/[\'^£$&*()#~>=_+]/', $txt_short_name))
				{
					echo "15**0"; die;
				}
				$data_array="'".$txt_supplier_name."'*'".$txt_short_name."'*".$txt_tin_number."*".$txt_vat_number."*".$txt_contact_person."*".$txt_contact_no."*".$txt_party_type_id."*".$txt_desination."*".$cbo_tag_company."*".$cbo_country."*".$txt_web_site."*".$txt_email."*".$txt_address_1st."*".$txt_address_2nd."*".$txt_address_3rd."*".$txt_address_4th."*".$txt_remark."*".$cbo_buyer."*".$txt_credit_limit_days."*".$txt_credit_limit_amount."*".$cbo_credit_limit_amount_curr."*".$cbo_discount_method."*".$cbo_security_deducted."*".$cbo_vat_to_be_deducted."*".$cbo_ait_to_be_deducted."*".$cbo_individual."*".$cbo_supplier_nature."*".$txt_tag_buyer_id."*".$txt_supplier_ref."*".$owner_name."*".$owner_nid."*".$owner_contact."*".$owner_email."*".$cbo_supplier_source."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*'0'*'2'";
			}
			
			
			//$rID=sql_update("lib_supplier",$field_array,$data_array,"id","".$update_id."",0);
			//Insert Data in  lib_supplier_party_type Table----------------------------------------
			//$rID1=execute_query( "delete from lib_supplier_party_type where  supplier_id = $update_id",0);
			//$data_array="";
			$party_type=explode(',',str_replace("'","",$txt_party_type_id));
			for($i=0; $i<count($party_type); $i++)
			{
				if($id_lib_supplier_party_type=="") $id_lib_supplier_party_type=return_next_id( "id", "lib_supplier_party_type", 1 ); else $id_lib_supplier_party_type=$id_lib_supplier_party_type+1;
				if($i==0) $add_comma=""; else $add_comma=",";
				$data_array1.="$add_comma(".$id_lib_supplier_party_type.",".$update_id.",".$party_type[$i].")";
			}
			$field_array1="id, supplier_id, party_type";
			//$rID2=sql_insert("lib_supplier_party_type",$field_array1,$data_array1,0);
		
			//----------------------------------------------------------------------------------
			
			//Insert Data in  lib_supplier_tag_company Table----------------------------------------
			//$rID3=execute_query( "delete from lib_supplier_tag_company where  supplier_id = $update_id",0);
			//$data_array="";
			//if(str_replace("'", "", $is_posted_accounts)!=1)
			//{
				$tag_company=explode(',',str_replace("'","",$cbo_tag_company));
				for($i=0; $i<count($tag_company); $i++)
				{
					if($id_lib_supplier_tag_company=="") $id_lib_supplier_tag_company=return_next_id( "id", "lib_supplier_tag_company", 1 ); else $id_lib_supplier_tag_company=$id_lib_supplier_tag_company+1;
					if($i==0) $add_comma=""; else $add_comma=",";
					$data_array2.="$add_comma(".$id_lib_supplier_tag_company.",".$update_id.",".$tag_company[$i].")";
				}
				$field_array2="id, supplier_id, tag_company";
			//}
			
			//Insert Data in  lib_supplier_buyer Table----------------------------------------
			$data_array3="";
			$tag_buyer=explode(',',str_replace("'","",$txt_tag_buyer_id));
			for($i=0; $i<count($tag_buyer); $i++)
			{
				if($id_lib_supplier_tag_buyer=="") $id_lib_supplier_tag_buyer=return_next_id( "id", "lib_supplier_tag_buyer", 1 ); else $id_lib_supplier_tag_buyer=$id_lib_supplier_tag_buyer+1;
				if($i==0) $add_comma=""; else $add_comma=",";
				$data_array3.="$add_comma(".$id_lib_supplier_tag_buyer.",".$update_id.",".$tag_buyer[$i].")";
			}
			$field_array3="id,supplier_id,tag_buyer";
			
			//echo $data_array3;
			
			$rID=sql_update("lib_supplier",$field_array,$data_array,"id","".$update_id."",0);
			$rID1=execute_query( "delete from lib_supplier_party_type where  supplier_id = $update_id",0);
			$rID2=sql_insert("lib_supplier_party_type",$field_array1,$data_array1,0);
			$rID3=$rID4=1;
			//if(str_replace("'", "", $is_posted_accounts)!=1)
			//{
				$rID3=execute_query( "delete from lib_supplier_tag_company where  supplier_id = $update_id",0);
				$rID4=sql_insert("lib_supplier_tag_company",$field_array2,$data_array2,1);
			//}
			$rID5=execute_query( "delete from lib_supplier_tag_buyer where  supplier_id = $update_id",0);
			$rID6=sql_insert("lib_supplier_tag_buyer",$field_array3,$data_array3,1);
		//echo "$rID && $rID1 && $rID2 && $rID3 && $rID4";die;
			//----------------------------------------------------------------------------------
			if($db_type==0)
			{
				if($rID && $rID1 && $rID2 && $rID3 && $rID4 )
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
			if($db_type==2 || $db_type==1 )
			{
				if($rID &&$rID1 && $rID2 && $rID3 && $rID4 )
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
		{ echo "50**Already Posted In Accounts. Supplier Deleting Not Allowed";die;}
		$supplier_hidden_id=str_replace("'", "", $supplier_hidden_id);
		$order_library=return_library_array( "select id, po_number from  wo_po_break_down", "id", "po_number"  );

		$pre_costing=return_field_value("min(job_no) as job_no", "wo_pre_cost_trim_cost_dtls", "nominated_supp=$supplier_hidden_id  and status_active=1 and is_deleted=0","job_no");
		$main_fab_booking=return_field_value("min(booking_no) as booking_no", "wo_booking_mst", "supplier_id=$supplier_hidden_id and booking_type=1 and is_short=2  and status_active=1 and is_deleted=0","booking_no");
		$short_fab_booking=return_field_value("min(booking_no) as booking_no", "wo_booking_mst", "supplier_id=$supplier_hidden_id and booking_type=1 and is_short=1  and status_active=1 and is_deleted=0","booking_no");
		$sample_fab_booking_with=return_field_value("min(booking_no) as booking_no", "wo_booking_mst", "supplier_id=$supplier_hidden_id and booking_type=4 and is_short=2  and status_active=1 and is_deleted=0","booking_no");
		$sample_fab_booking_without=return_field_value("min(booking_no) as booking_no", "wo_non_ord_samp_booking_mst", "supplier_id=$supplier_hidden_id and booking_type=4 and status_active=1 and is_deleted=0","booking_no");
		$main_trim_booking=return_field_value("min(booking_no) as booking_no", "wo_booking_mst", "supplier_id=$supplier_hidden_id and booking_type=2 and is_short in(1,2) and status_active=1 and is_deleted=0","booking_no");
		$short_trim_booking=return_field_value("min(booking_no) as booking_no", "wo_booking_mst", "supplier_id=$supplier_hidden_id and booking_type=2 and is_short in(1) and item_category=4 and status_active=1 and is_deleted=0","booking_no");
		$sample_trim_booking_with=return_field_value("min(booking_no) as booking_no", "wo_non_ord_samp_booking_mst", "supplier_id=$supplier_hidden_id and booking_type=5 and is_short in(2) and item_category=4 and status_active=1 and is_deleted=0","booking_no");
		$sample_trim_booking_without=return_field_value("min(booking_no) as booking_no", "wo_non_ord_samp_booking_mst", "supplier_id=$supplier_hidden_id and booking_type=5 and  item_category=4 and status_active=1 and is_deleted=0","booking_no");
		$fab_service_booking=return_field_value("min(booking_no) as booking_no", "wo_booking_mst", "supplier_id=$supplier_hidden_id and booking_type=3 and status_active=1 and is_deleted=0","booking_no");
		$yarn_dyeing_wo=return_field_value("min(ydw_no) as ydw_no", "wo_yarn_dyeing_mst", "supplier_id=$supplier_hidden_id and status_active=1 and is_deleted=0","ydw_no");
		$embellishment_wo=return_field_value("min(booking_no) as booking_no", "wo_booking_mst", "supplier_id=$supplier_hidden_id and booking_type=6 and is_short in(2) and status_active=1 and is_deleted=0","booking_no");
		$pro_forma_invoice=return_field_value("min(pi_number) as pi_number", "com_pi_master_details", "supplier_id=$supplier_hidden_id  and status_active=1 and is_deleted=0","pi_number");
		$back_to_back_lc=return_field_value("min(btb_system_id) as btb_system_id", "com_btb_lc_master_details", "supplier_id=$supplier_hidden_id  and status_active=1 and is_deleted=0","btb_system_id");
		//$yarn_store=array(1=>"Yarn Issue",3=>"Yarn Received",8=>"Yarn Receive Return",9=>"Yarn Issue Return");
		$all_received_master=sql_select("select min(recv_number) as recv_number,min(entry_form) as entry_form from  inv_receive_master where supplier_id=$supplier_hidden_id  and status_active=1 and is_deleted=0 and  entry_form in (4,8,20,26,24)");
		$entry_form_name=$all_received_master[0][csf('entry_form')];
		$recv_number=$all_received_master[0][csf('recv_number')];
		$all_recv_menu_name=$entry_form[$entry_form_name].":";
		
		$all_issue_master=sql_select("select min(issue_number) as issue_number,min(entry_form) as entry_form from  inv_issue_master where supplier_id=$supplier_hidden_id  and status_active=1 and is_deleted=0 and  entry_form in (1,3,9)");
		$entry_form_issue=$all_issue_master[0][csf('entry_form')];
		$issue_number=$all_issue_master[0][csf('issue_number')];
		$all_issue_menu_name=$entry_form[$entry_form_issue].":";
		
		$production_recv_master=sql_select("select min(recv_number) as recv_number,min(entry_form) as entry_form from  inv_receive_master where knitting_company=$supplier_hidden_id  and status_active=1 and is_deleted=0 and  entry_form in (2,7) and knitting_source=3 ");
		$production_entry_form=$production_recv_master[0][csf('entry_form')];
		$production_recv_number=$production_recv_master[0][csf('recv_number')];
		$all_production_menu_name=$entry_form[$production_entry_form].":";
		

		$garments_production=sql_select("select min(production_type) as production_type,min(po_break_down_id) as po_break_down_id from  pro_garments_production_mst where serving_company=$supplier_hidden_id  and status_active=1 and is_deleted=0 and  production_type in (1,2,3,4,5,7,8,9) ");
		$garments_entry_form=$garments_production[0][csf('production_type')];
		$garments_po_number=$order_library[$garments_production[0][csf('po_break_down_id')]];
		$all_garments_menu_name=$production_type[$garments_entry_form].":";

		$sql_comparative_statement=sql_select("select sys_number as SYS_NUMBER,supp_id as SUPP_ID from req_comparative_mst where entry_form=481 and status_active=1 and is_deleted=0 order by id asc");
		$comparative_statement='';
		if(count($sql_comparative_statement)>0)
		{
			foreach($sql_comparative_statement as $row)
			{
				$all_supp_id=explode(',',$row['SUPP_ID']);
				foreach($all_supp_id as $val)
				{
					if($val==$supplier_hidden_id)
					{
						$comparative_statement=$row['SYS_NUMBER'];
					}
				}
			}
		}

		//echo $pre_costing;die;
		if($pre_costing!="" || $main_fab_booking!="" || $short_fab_booking!="" || $sample_fab_booking_with!="" || $sample_fab_booking_without!="" || $main_trim_booking!="" || $short_trim_booking!="" || $sample_trim_booking_with!="" || $sample_trim_booking_without!="" || $fab_service_booking!="" || $yarn_dyeing_wo!="" || $embellishment_wo!="" || $pro_forma_invoice!="" || $back_to_back_lc!="" || $recv_number!="" || $issue_number!="" || $production_recv_number!="" || $garments_po_number!="" ||$comparative_statement!="")
		{
		echo "50**Some Entries Found For This Supplier, Deleting Not Allowed, \n Pre Costing: ".$pre_costing."\n Main Fabric Booking:".$main_fab_booking."\n Short Fabric Booking:".$short_fab_booking."\n Sample Fabric Booking With Order: ".$sample_fab_booking_with."\n Sample Fabric Without Order: ".$sample_fab_booking_without."\n Main Trim Booking: ".$main_trim_booking."\n Short Trim Booking: ".$short_trim_booking."\n Sample Trim Booking With: ".$sample_trim_booking_with."\n Sample Trim Booking Without: ".$sample_trim_booking_without."\n Fabric Service Booking: ".$fab_service_booking."\n Yarn Dyeing WO: ".$yarn_dyeing_wo."\n Embellishment WO: ".$embellishment_wo."\n Pro Forma Invoice: ".$pro_forma_invoice."\n Back To Back LC: ".$back_to_back_lc."\n $all_recv_menu_name  ".$recv_number."\n $all_issue_menu_name ".$issue_number."\n $all_production_menu_name ".$production_recv_number."\n $all_garments_menu_name ".$garments_po_number."\n Comparative Statement: ".$comparative_statement;	
		
		 die;	
		}
		/*if (is_duplicate_field( "supllier", "lib_buyer", "supllier=$update_id and is_deleted=0" ) ==1)
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
			$rID=sql_delete("lib_supplier",$field_array,$data_array,"id","".$update_id."",1);
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
			if($db_type==2 || $db_type==1 )
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

if ($action=="show_supplier_list_view")
{
	$arr=array (8=>$currency,9=>$row_status,11=>$commission_particulars);
	echo  create_list_view ( "list_view", "ID,Supplier Name,Short Name,Supplier Type,Contact Person,Designation,Credit Limit(Days),Credit Limit (Amount),Currency, Status,Owner,Supplier Source", "50,150,100,150,100,120,80,80,70,70,70","1180","220",0, "select supplier_name,short_name,party_type,contact_person,designation,credit_limit_days,credit_limit_amount,credit_limit_amount_currency,status_active,id,owner_name,source_id,owner_nid,owner_contact,owner_email from lib_supplier where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,0,0,0,0,0,0,0,credit_limit_amount_currency,status_active,0,source_id", $arr , "id,supplier_name,short_name,party_type,contact_person,designation,credit_limit_days,credit_limit_amount,credit_limit_amount_currency,status_active,owner_name,source_id", "../contact_details/requires/supplier_info_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,0,0,1,1,0,0') ;
    
}
if ($action=="load_php_data_to_form")
{
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );

	$nameArray=sql_select( "select id, supplier_name, short_name, contact_person, contact_no, party_type, designation, tag_company, country_id, web_site, email, address_1, address_2, address_3,address_4,remark,buyer,credit_limit_days, credit_limit_amount, credit_limit_amount_currency, discount_method, securitye_deducted, vat_to_be_deducted, ait_to_be_deducted,individual,supplier_nature,tag_buyer,supplier_ref, source_id,status_active,is_posted_account,owner_name,owner_nid,owner_contact,owner_email,vat_number,tin_number from lib_supplier  where id='$data'" );
	foreach ($nameArray as $inf)
	{
		$party_name='';
			$party_id_array=explode(",",$inf[csf("party_type")]);
			foreach($party_id_array as $val)
			{
				if($party_name=="") $party_name=$party_type_supplier[$val]; else $party_name.=",".$party_type_supplier[$val];
			}
			$buyer_name='';
			$buyer_id_array=explode(",",$inf[csf("tag_buyer")]);
			foreach($buyer_id_array as $val)
			{
				if($buyer_name=="") $buyer_name=$buyer_library[$val]; else $buyer_name.=",".$buyer_library[$val];
			}
		echo "document.getElementById('txt_supplier_name').value = '".($inf[csf("supplier_name")])."';\n";
		echo "document.getElementById('supplier_hidden_id').value = '".($inf[csf("id")])."';\n"; 
		echo "document.getElementById('txt_short_name').value = '".($inf[csf("short_name")])."';\n";    
		echo "document.getElementById('txt_contact_person').value  = '".($inf[csf("contact_person")])."';\n"; 
		echo "document.getElementById('txt_contact_no').value  = '".($inf[csf("contact_no")])."';\n"; 
		echo "document.getElementById('cbo_party_type').value  = '".$party_name."';\n";  
		echo "document.getElementById('txt_party_type_id').value  = '".($inf[csf("party_type")])."';\n"; 
		echo "document.getElementById('txt_desination').value = '".($inf[csf("designation")])."';\n";    
		echo "document.getElementById('cbo_tag_company').value  = '".($inf[csf("tag_company")])."';\n";  
		echo "document.getElementById('cbo_country').value = '".($inf[csf("country_id")])."';\n";    
		echo "document.getElementById('txt_web_site').value  = '".($inf[csf("web_site")])."';\n"; 
		echo "document.getElementById('owner_name').value  = '".($inf[csf("owner_name")])."';\n";  
		echo "document.getElementById('owner_email').value  = '".($inf[csf("owner_email")])."';\n";  
		echo "document.getElementById('owner_nid').value  = '".($inf[csf("owner_nid")])."';\n";  
		echo "document.getElementById('owner_info').value  = '".($inf[csf("owner_name")])."';\n";  
		echo "document.getElementById('owner_contact').value  = '".($inf[csf("owner_contact")])."';\n";  
		echo "document.getElementById('txt_email').value  = '".($inf[csf("email")])."';\n";  
		echo "document.getElementById('txt_address_1st').value  = '".preg_replace("/[\r\n]*/","",$inf[csf("address_1")])."';\n";  
		echo "document.getElementById('txt_address_2nd').value  = '".preg_replace("/[\r\n]*/","",$inf[csf("address_2")])."';\n";  
		echo "document.getElementById('txt_address_3rd').value  = '".preg_replace("/[\r\n]*/","",$inf[csf("address_3")])."';\n";  
		echo "document.getElementById('txt_address_4th').value  = '".preg_replace("/[\r\n]*/","",$inf[csf("address_4")])."';\n";  
		echo "document.getElementById('txt_remark').value  = '".($inf[csf("remark")])."';\n";  
		echo "document.getElementById('cbo_buyer').value  = '".($inf[csf("buyer")])."';\n";
		echo "document.getElementById('txt_credit_limit_days').value  = '".($inf[csf("credit_limit_days")])."';\n"; 
		echo "document.getElementById('txt_credit_limit_amount').value  = '".($inf[csf("credit_limit_amount")])."';\n"; 
		echo "document.getElementById('cbo_credit_limit_amount_curr').value  = '".($inf[csf("credit_limit_amount_currency")])."';\n"; 
		echo "document.getElementById('cbo_discount_method').value  = '".($inf[csf("discount_method")])."';\n"; 
		echo "document.getElementById('cbo_security_deducted').value  = '".($inf[csf("securitye_deducted")])."';\n"; 
		echo "document.getElementById('cbo_vat_to_be_deducted').value  = '".($inf[csf("vat_to_be_deducted")])."';\n"; 
		echo "document.getElementById('cbo_ait_to_be_deducted').value  = '".($inf[csf("ait_to_be_deducted")])."';\n"; 
		echo "document.getElementById('cbo_individual').value  = '".($inf[csf("individual")])."';\n"; 
		echo "document.getElementById('cbo_supplier_nature').value  = '".($inf[csf("supplier_nature")])."';\n";
		echo "document.getElementById('txt_tag_buyer_id').value  = '".($inf[csf("tag_buyer")])."';\n";
		echo "document.getElementById('txt_tin_number').value  = '".($inf[csf("tin_number")])."';\n";
		echo "document.getElementById('txt_vat_number').value  = '".($inf[csf("vat_number")])."';\n";
		echo "document.getElementById('cbo_tag_buyer').value  = '".$buyer_name."';\n";
		echo "document.getElementById('txt_supplier_ref').value  = '".($inf[csf("supplier_ref")])."';\n";
		echo "document.getElementById('cbo_supplier_source').value  = '".($inf[csf("source_id")])."';\n";
		echo "document.getElementById('cbo_status').value  = '".($inf[csf("status_active")])."';\n"; 
		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n";
		echo "document.getElementById('is_posted_accounts').value  = '".($inf[csf("is_posted_account")])."';\n"; 
		if($inf[csf("is_posted_account")]==1) $msg="Already Posted in Accounts. Supplier Name Tag Company and Status Update Not Allowed.";
		else $msg="";
		echo "$('#posted_account_td').text('".$msg."');\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_supplier_info',1);\n"; 
		echo "set_multiselect('cbo_tag_company','0','1','".($inf[csf("tag_company")])."','__set_buyer_status__../contact_details/requires/supplier_info_controller');\n"; 
 
	}
}

if ($action=="set_buyer_status")
{
	
	if($data=="") echo ""; 
	else
	{
		$data=explode(",",$data);
		if (in_array("90",$data))
			echo "$('#cbo_buyer').removeAttr('disabled');\n";
		else	
			echo "$('#cbo_buyer').attr('disabled','true');\n";
	}
}
if($action=="party_name_popup")
{
	echo load_html_head_contents("Party Type Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $party_type_id;
	?>
	<script>
		var selected_id = new Array();
		var selected_name = new Array();
		
		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1);
		});

		function toggle( x, origColor) {
            var newColor = 'yellow';
            if ( x.style ) {
                x.style.backgroundColor = (newColor == x.style.backgroundColor)? origColor : newColor;
            }
        }

		function check_all_data()
		{
            var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length-1;
            tbl_row_count = tbl_row_count;
			//alert(tbl_row_count);

            if(document.getElementById('check_all').checked)
			{
                for( var i = 1; i <= tbl_row_count; i++ ) {
	                document.getElementById( 'search' + i ).style.backgroundColor = 'yellow';
	                if( jQuery.inArray( $('#txt_individual_id' + i).val(), selected_id ) == -1 ) {
						selected_id.push( $('#txt_individual_id' + i).val() );
						selected_name.push( $('#txt_individual' + i).val() );
					}
                }

                var id = ''; var name = '';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ',';
				}
				id = id.substr( 0, id.length - 1 );
				name = name.substr( 0, name.length - 1 );
				$('#hidden_party_id').val(id);
				$('#hidden_party_name').val(name);
            }
			else
			{				
                for( var i = 1; i <= tbl_row_count; i++ ) {
                    if(i%2==0 ) document.getElementById('search'+i).style.backgroundColor = '#FFFFFF';
                    else document.getElementById('search'+i).style.backgroundColor = '#E9F3FF';

					for( var j = 0; j < selected_id.length; j++ ) {
                        if( selected_id[j] == $('#txt_individual_id' + i).val() ) break;
                    }
                    selected_id.splice( j,1 );
                }

				var id = ''; var name = '';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ',';
				}
				id = id.substr( 0, id.length - 1 );
				name = name.substr( 0, name.length - 1 );
				$('#hidden_party_id').val(id);
				$('#hidden_party_name').val(name);
            }
        }

		function js_set_value( str) 
		{
        	var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
            tbl_row_count = tbl_row_count-1;
            if ($("#search"+str).css("display") !='none')
			{
                if ( str%2==0 ) toggle( document.getElementById( 'search' + str ), '#FFFFFF');
				else toggle( document.getElementById( 'search' + str ), '#E9F3FF');

				if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
					selected_id.push( $('#txt_individual_id' + str).val() );
					selected_name.push( $('#txt_individual' + str).val() );
				}
				else
				{
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 );
				}
            }

			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			$('#hidden_party_id').val(id);
			$('#hidden_party_name').val(name);

            if (selected_id.length == tbl_row_count) document.getElementById("check_all").checked = true;
			else document.getElementById("check_all").checked = false;
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
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			if (old.length == tbl_row_count) document.getElementById("check_all").checked = true;
			else document.getElementById("check_all").checked = false;
        }
    </script>
	</head>
	<body>
		<div align="center">
			<fieldset style="width:370px;margin-left:10px">
		    	<input type="hidden" name="hidden_party_id" id="hidden_party_id" class="text_boxes" value="">
		        <input type="hidden" name="hidden_party_name" id="hidden_party_name" class="text_boxes" value="">
		        <form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
		            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table" >
		                <thead>
		                    <th width="50">SL</th>
		                    <th>Process Name</th>
		                </thead>
		            </table>
		            <div style="width:350px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
		                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="332" class="rpt_table" id="tbl_list_search" >
		                <?
		                    $i=1; $party_row_id=''; 
							$hidden_party_id=explode(",",$party_type_id);
		                    foreach($party_type_supplier as $id=>$name)
		                    {
								
								//if(in_array($id,$not_process_id_print_array))
								//{
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									if(in_array($id,$hidden_party_id)) 
									{ 
										if($party_row_id=="") $party_row_id=$i; else $party_row_id.=",".$i;
									}
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)"> 
										<td width="50" align="center"><?php echo "$i"; ?>
											<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $id; ?>"/>	
											<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $name; ?>"/>
										</td>	
										<td><p><? echo $name; ?></p></td>
									</tr>
									<?
									$i++;
								//}
		                    }
		                ?>
		                <input type="hidden" name="txt_party_row_id" id="txt_party_row_id" value="<? echo $party_row_id; ?>"/>
		                </table>
		            </div>
		             <table width="350" cellspacing="0" cellpadding="0" style="border:none" align="center">
		                <tr>
		                    <td align="center" height="30" valign="bottom">
		                        <div style="width:100%"> 
		                            <div style="width:50%; float:left" align="left">
		                                <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
		                            </div>
		                            <div style="width:50%; float:left" align="left">
		                                <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
		                            </div>
		                        </div>
		                    </td>
		                </tr>
		            </table>
		        </form>
		    </fieldset>
		</div>    
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>set_all();</script>
	</html>
	<?
	exit();
}
?>
<?
if($action=="buyer_name_popup")
{
	echo load_html_head_contents("Party Type Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $party_type_id;
	?>
	<script>
		var selected_id = new Array();
		var selected_name = new Array();
		
		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1);
		});

		function toggle( x, origColor) {
            var newColor = 'yellow';
            if ( x.style ) {
                x.style.backgroundColor = (newColor == x.style.backgroundColor)? origColor : newColor;
            }
        }

		function check_all_data()
		{
            var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length-1;
            tbl_row_count = tbl_row_count;
			//alert(tbl_row_count);

            if(document.getElementById('check_all').checked)
			{
                for( var i = 1; i <= tbl_row_count; i++ ) {
	                document.getElementById( 'search' + i ).style.backgroundColor = 'yellow';
	                if( jQuery.inArray( $('#txt_individual_id' + i).val(), selected_id ) == -1 ) {
						selected_id.push( $('#txt_individual_id' + i).val() );
						selected_name.push( $('#txt_individual' + i).val() );
					}
                }

                var id = ''; var name = '';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ',';
				}
				id = id.substr( 0, id.length - 1 );
				name = name.substr( 0, name.length - 1 );
				$('#hidden_buyer_id').val(id);
				$('#hidden_buyer_name').val(name);
            }
			else
			{				
                for( var i = 1; i <= tbl_row_count; i++ ) {
                    if(i%2==0 ) document.getElementById('search'+i).style.backgroundColor = '#FFFFFF';
                    else document.getElementById('search'+i).style.backgroundColor = '#E9F3FF';

					for( var j = 0; j < selected_id.length; j++ ) {
                        if( selected_id[j] == $('#txt_individual_id' + i).val() ) break;
                    }
                    selected_id.splice( j,1 );
                }

				var id = ''; var name = '';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ',';
				}
				id = id.substr( 0, id.length - 1 );
				name = name.substr( 0, name.length - 1 );
				$('#hidden_buyer_id').val(id);
				$('#hidden_buyer_name').val(name);
            }
        }

		function js_set_value( str) 
		{
        	var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
            tbl_row_count = tbl_row_count-1;
            if ($("#search"+str).css("display") !='none')
			{
                if ( str%2==0 ) toggle( document.getElementById( 'search' + str ), '#FFFFFF');
				else toggle( document.getElementById( 'search' + str ), '#E9F3FF');

				if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
					selected_id.push( $('#txt_individual_id' + str).val() );
					selected_name.push( $('#txt_individual' + str).val() );
				}
				else
				{
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 );
				}
            }

			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			$('#hidden_buyer_id').val(id);
			$('#hidden_buyer_name').val(name);

            if (selected_id.length == tbl_row_count) document.getElementById("check_all").checked = true;
			else document.getElementById("check_all").checked = false;
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
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			if (old.length == tbl_row_count) document.getElementById("check_all").checked = true;
			else document.getElementById("check_all").checked = false;
        }
	</script>	
	</head>
	<body>
		<div align="center">
			<fieldset style="width:370px;margin-left:10px">
		    	<input type="hidden" name="hidden_buyer_id" id="hidden_buyer_id" class="text_boxes" value="">
		        <input type="hidden" name="hidden_buyer_name" id="hidden_buyer_name" class="text_boxes" value="">
		        <form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
		            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table" >
		                <thead>
		                    <th width="50">SL</th>
		                    <th>Process Name</th>
		                </thead>
		            </table>
		            <div style="width:350px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
		                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="332" class="rpt_table" id="tbl_list_search" >
		                <?
		                    $i=1; $party_row_id=''; 
							$hidden_party_id=explode(",",$txt_tag_buyer_id);
							$sql_buyer=sql_select("select id,buyer_name from  lib_buyer where is_deleted=0 and status_active=1 order by buyer_name");
		                    foreach($sql_buyer as $row_buyer)
		                    {
								
								//if(in_array($id,$not_process_id_print_array))
								//{
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									if(in_array($row_buyer[csf('id')],$hidden_party_id)) 
									{ 
										if($party_row_id=="") $party_row_id=$i; else $party_row_id.=",".$i;
									}
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)"> 
										<td width="50" align="center"><?php echo "$i"; ?>
											<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $row_buyer[csf('id')]; ?>"/>	
											<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $row_buyer[csf('buyer_name')]; ?>"/>
										</td>	
										<td><p><? echo $row_buyer[csf('buyer_name')]; ?></p></td>
									</tr>
									<?
									$i++;
								//}
		                    }
		                ?>
		                <input type="hidden" name="txt_party_row_id" id="txt_party_row_id" value="<?php echo $party_row_id; ?>"/>
		                </table>
		            </div>
		             <table width="350" cellspacing="0" cellpadding="0" style="border:none" align="center">
		                <tr>
		                    <td align="center" height="30" valign="bottom">
		                        <div style="width:100%"> 
		                            <div style="width:50%; float:left" align="left">
		                                <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
		                            </div>
		                            <div style="width:50%; float:left" align="left">
		                                <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
		                            </div>
		                        </div>
		                    </td>
		                </tr>
		            </table>
		        </form>
		    </fieldset>
		</div>    
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		set_all();
	</script>
	</html>
	<?
	exit();
}

if($action=="owner_info")
{
	echo load_html_head_contents("Owner Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $party_type_id;
	?>
		
	</head>
	<body>
		<div align="center">
			<fieldset style="width:690px;margin-left:10px">
		    	
		        <form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
		            <table width="680">
		               <tr>
		               		<td width="60">Owner Name</td>
		               		<td width="180"><input type="text" name="owner_name" id="owner_name" class="text_boxes" style="width:180px;" value="<?php echo $owner_name;?>"></td>

		               		<td width="60">Owner NID</td>
		               		<td width="180"><input type="text" name="owner_nid" id="owner_nid" class="text_boxes" style="width:180px;" value="<?php echo $owner_nid;?>"></td>
		               </tr>
		               <tr>
		               		<td colspan="4">&nbsp;</td>
		               </tr>
		               <tr>
		               		<td width="60">Owner Contact</td>
		               		<td width="180"><input type="text" name="owner_contact" id="owner_contact" class="text_boxes" style="width:180px;" value="<?php echo $owner_contact;?>"></td>

		               		<td width="60">Owner Email</td>
		               		<td width="180"><input type="text" name="owner_email" id="owner_email" class="text_boxes" style="width:180px;" value="<?php echo $owner_email;?>"></td>
		               </tr>
		               <tr>
		               		<td colspan="4">&nbsp;</td>
		               </tr>
		               
		               <tr>
		               		<td colspan="4" style="justify-content: center;text-align: center;"><input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" /></td>
		               </tr>
		            </table>
		            
		            
		        </form>
		    </fieldset>
		</div>    
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	
	</html>
	<?
	exit();
}
?>