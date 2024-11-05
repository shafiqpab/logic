<?
include('../../../includes/common.php');
session_start();

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$trans_Type="1";

if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$color_arr=return_library_array( "SELECT id, color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
$size_arr=return_library_array( "SELECT id,size_name from lib_size where status_active =1 and is_deleted=0",'id','size_name');

if($action=="check_bundle")
{
	$nameArray= sql_select("select id, company_name, variable_list, item_show_in_detail,quantity_control,validation_qty_control from variable_setting_printing_prod where company_name='$data' and variable_list=5 order by id");
	$barcode_maintain=$nameArray[0][csf('quantity_control')];
	$is_independent=$nameArray[0][csf('validation_qty_control')];
	//echo $barcode_maintain.'_'.$is_independent;
	if($barcode_maintain==1 && $is_independent==1){
		echo '1';
	}else{
		echo '0';
	}
	exit();	
}

if($action=="check_last_bundle_no")
{
	$job_no=explode('-', $data);
	$sql_bundle_no="SELECT max(c.bundle_no_prefix_num) as bundle_no_prefix_num, a.job_no_prefix_num from subcon_ord_mst a, sub_material_mst b, prnting_bundle_dtls c where a.embellishment_job=b.embl_job_no and b.id=c.item_rcv_id and a.embellishment_job='$data' and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0  and c.status_active =1 and c.is_deleted =0 group by a.job_no_prefix_num";
	$bdlNoArray =sql_select($sql_bundle_no);
	$last_bundle_no=$bdlNoArray[0][csf("bundle_no_prefix_num")]; 
	if($last_bundle_no=='') $last_bundle_no=0; else $last_bundle_no=$last_bundle_no;
	$manual_bundle_no=$bdlNoArray[0][csf("job_no_prefix_num")].'-'.$job_no[2].'-'.$last_bundle_no;

	//echo $barcode_maintain.'_'.$is_independent;
	echo $manual_bundle_no;
	exit();	
}




if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 150, "SELECT id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );	
	exit();	 
}

if ($action=="load_drop_down_from_location")
{
	echo create_drop_down( "cbo_from_location_name", 150, "SELECT id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );	
	exit();	 
}

if ($action=="load_drop_down_buyer")
{
	//echo $data; die;
	$data=explode('_',$data);
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_party_name", 150, "SELECT comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Party --", $data[2], "");
		exit();
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 150, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --",$data[2], "" );
		exit();
	}
} 

if ($action=="load_drop_down_buyer_pop")
{
	//echo $data; die;
	$data=explode('_',$data);
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_party_name", 120, "SELECT comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Party --", $data[2], "");
		exit();
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 120, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --",$data[2], "" );
		exit();
	}
} 

if($action=="load_drop_down_emb_type")
{
	$data=explode('_',$data);
	
	if($data[0]==1) $emb_type=$emblishment_print_type;
	else if($data[0]==2) $emb_type=$emblishment_embroy_type;
	else if($data[0]==3) $emb_type=$emblishment_wash_type;
	else if($data[0]==4) $emb_type=$emblishment_spwork_type;
	else if($data[0]==5) $emb_type=$emblishment_gmts_type;
	
	echo create_drop_down( "cboReType_".$data[1], 80,$emb_type,"", 1, "-- Select --", "", "","","" ); 
	
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$trans_Type="1";
	// Insert Start Here ----------------------------------------------------------
	if ($operation==0)   
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
				
		if($db_type==0)
		{
			$new_receive_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name),'', 'PMR' , date("Y",time()), 5, "SELECT id,prefix_no,prefix_no_num from sub_material_mst where company_id=$cbo_company_name and trans_Type='$trans_Type' and entry_form=205 and status_active =1 and is_deleted=0 and YEAR(insert_date)=".date('Y',time())." order by id desc ", "prefix_no", "prefix_no_num" ));
		}
		else if($db_type==2)
		{
			$new_receive_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name),'', 'PMR' , date("Y",time()), 5, "SELECT id,prefix_no,prefix_no_num from sub_material_mst where company_id=$cbo_company_name and trans_Type='$trans_Type' and entry_form=205 and status_active =1 and is_deleted=0 and TO_CHAR(insert_date,'YYYY')=".date('Y',time())." order by id desc ", "prefix_no", "prefix_no_num" ));
		}

		/*if(is_duplicate_field( "a.chalan_no", "sub_material_mst a, sub_material_dtls b", "a.sys_no='$new_receive_no[0]' and a.chalan_no=$txt_receive_challan and b.order_id=$order_no_id and b.material_description=$txt_material_description and b.color_id=$color_id" )==1)
		{
			//check_table_status( $_SESSION['menu_id'],0);
			echo "11**0"; 
			disconnect($con); die;			
		}	*/		
		
		$id=return_next_id("id","sub_material_mst",1) ;
		$field_array="id, entry_form, prefix_no, prefix_no_num, sys_no, trans_type, company_id, location_id, party_id, chalan_no, subcon_date, within_group, embl_job_no,from_company_name,from_location_name,remarks, inserted_by, insert_date, status_active, is_deleted";
		$data_array="(".$id.",'205','".$new_receive_no[1]."','".$new_receive_no[2]."','".$new_receive_no[0]."','".$trans_Type."',".$cbo_company_name.",".$cbo_location_name.",".$cbo_party_name.",".$txt_receive_challan.",".$txt_receive_date.",".$cbo_within_group.",".$txt_job_no.",".$cbo_from_company_name.",".$cbo_from_location_name.",".$txt_receive_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";  
		//echo "10**"."INSERT INTO sub_material_mst (".$field_array.") VALUES ".$data_array; disconnect($con); die;
		
		
		$txt_receive_no=$new_receive_no[0];//change_date_format($data[2], "dd-mm-yyyy", "-",1)
		
		$id1=return_next_id("id","sub_material_dtls",1) ;
		$field_array2="id, mst_id, emb_name_id, quantity, uom, job_dtls_id, job_break_id, buyer_po_id, remarks, inserted_by, insert_date, status_active, is_deleted";
		$data_array2="";  $add_commaa=0;
		for($i=1; $i<=$total_row; $i++)
		{
			$ordernoid			= "ordernoid_".$i; 
			$breakdownid		= "breakdownid_".$i;
			$txtbuyerPoId		= "txtbuyerPoId_".$i;
			$cboProcessName		= "cboProcessName_".$i;
			$cbouom				= "cbouom_".$i;
			$txtreceiveqty		= "txtreceiveqty_".$i;
			$txtremarks			= "txtremarks_".$i;
			$updatedtlsid		= "updatedtlsid_".$i;
			
			if ($add_commaa!=0) $data_array2 .=",";
			 
			$data_array2.="(".$id1.",'".$id."',".$$cboProcessName.",".$$txtreceiveqty.",".$$cbouom.",".$$ordernoid.",".$$breakdownid.",".$$txtbuyerPoId.",".$$txtremarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			 
			$id1=$id1+1; $add_commaa++;
		}
		//echo "10**INSERT INTO sub_material_dtls (".$field_array2.") VALUES ".$data_array2; disconnect($con); die;
		$flag=1;
		//echo "10**INSERT INTO sub_material_dtls (".$field_array2.") VALUES ".$data_array2; disconnect($con); die;
		$rID=sql_insert("sub_material_mst",$field_array,$data_array,0);
		if($flag==1 && $rID==1) $flag=1; else $flag=0;
		$rID2=sql_insert("sub_material_dtls",$field_array2,$data_array2,1);	
		if($flag==1 && $rID2==1) $flag=1; else $flag=0;
			//echo "10**".$rID."**".$rID2	; die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_job_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_job_no);
			}
		}
		else if($db_type==2)
		{
			if($flag==1) 
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_job_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_job_no);
			}
		}
		disconnect($con);
		die;		
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$iss_number=return_field_value( "sys_no", "sub_material_mst"," embl_job_no=$txt_job_no and status_active=1 and is_deleted=0 and trans_type=2");
		if($iss_number)
		{
			//issue quantity > receive qty
			// if condition quantity uodate hobe else return
			//echo "emblIssue**".str_replace("'","",$txt_job_no)."**".$iss_number;
			//disconnect($con); die;
			
			$flag=1;
			$field_arr_up="quantity*updated_by*update_date";
			for($i=1; $i<=$total_row; $i++)
			{
				$txtreceiveqty		= "txtreceiveqty_".$i;
				$updatedtlsid		= "updatedtlsid_".$i;
				$txtpreqty			= "txtpreqty_".$i;
				$preissyqty			= "preissyqty_".$i;
				//echo "10**".str_replace("'","",$$preissyqty)."**".str_replace("'","",$$txtpreqty)."**".str_replace("'","",$$txtreceiveqty); disconnect($con); die;
				
				//10**300**550**10
				$preissue_Qty=str_replace("'","",$$preissyqty);
				$txtpre_Qty=str_replace("'","",$$txtpreqty);
				$receive_Qty=str_replace("'","",$$txtreceiveqty);
				if($txtpre_Qty!=''){
					$total_receive_Qty=$txtpre_Qty-$receive_Qty;
				}else{
					$total_receive_Qty=$receive_Qty;
				}
				
				
				//echo "10**".$preissue_Qty."**".$total_receive_Qty; die;

				//if(str_replace("'","",$$txtpreqty)  > str_replace("'","",$$preissyqty))
				if($preissue_Qty > $total_receive_Qty)
				{
					//echo "10**".$preissue_Qty."**".$total_receive_Qty."**".$txtpre_Qty."**".$receive_Qty; die;
					//10****-2499.9996****2499.9996
					echo "emblIssue**".str_replace("'","",$txt_job_no)."**".$iss_number; die;
				}
				else
				{
					if(str_replace("'","",$$updatedtlsid)!="")
					{
						$data_arr_up[str_replace("'","",$$updatedtlsid)]=explode("*",("".$$txtreceiveqty."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
						$id_arr_rec[]=str_replace("'","",$$updatedtlsid);
						$hdn_break_id_arr[]=str_replace("'","",$$updatedtlsid);
					}
				}

				if($data_arr_up!="")
				{
					//echo "10**".bulk_update_sql_statement( "sub_material_dtls", "id", $field_arr_up,$data_arr_up,$hdn_break_id_arr);
					$rID3=execute_query(bulk_update_sql_statement( "sub_material_dtls", "id", $field_arr_up,$data_arr_up,$hdn_break_id_arr),1);
					if($rID3==1 && $flag==1) $flag=1; else $flag=0;
				}
			}
		}
		else
		{
			$rec_sql_dtls="SELECT b.id from sub_material_dtls b, sub_material_mst a where a.id=b.mst_id and a.id=$update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trans_type=1";//
			$all_dtls_id_arr=array();
			//echo "10**".$rec_sql_dtls; die;
			$nameArray=sql_select( $rec_sql_dtls ); 
			foreach($nameArray as $row)
			{
				$all_dtls_id_arr[]=$row[csf('id')];
			}
			unset($nameArray);

			$field_array="location_id*party_id*chalan_no*subcon_date*embl_job_no*from_company_name*from_location_name*remarks*updated_by*update_date";
			$data_array="".$cbo_location_name."*".$cbo_party_name."*".$txt_receive_challan."*".$txt_receive_date."*".$txt_job_no."*".$cbo_from_company_name."*".$cbo_from_location_name."*".$txt_receive_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
			
			$field_array2="id, mst_id, emb_name_id, quantity, uom, job_dtls_id, job_break_id, buyer_po_id, remarks, inserted_by, insert_date, status_active, is_deleted";
			
			$field_arr_up="emb_name_id*quantity*uom*job_dtls_id*job_break_id*buyer_po_id*remarks*updated_by*update_date";
			
			$id1=return_next_id("id","sub_material_dtls",1);
			$data_array2="";  $add_commaa=0;
			for($i=1; $i<=$total_row; $i++)
			{
				$ordernoid			= "ordernoid_".$i; 
				$breakdownid		= "breakdownid_".$i;
				$txtbuyerPoId		= "txtbuyerPoId_".$i;
				$cboProcessName		= "cboProcessName_".$i;
				$cbouom				= "cbouom_".$i;
				$txtreceiveqty		= "txtreceiveqty_".$i;
				$txtremarks			= "txtremarks_".$i;
				$updatedtlsid		= "updatedtlsid_".$i;
				
				if(str_replace("'","",$$updatedtlsid)=="")
				{
					if ($add_commaa!=0) $data_array2 .=",";
				 
					$data_array2.="(".$id1.",".$update_id.",".$$cboProcessName.",".$$txtreceiveqty.",".$$cbouom.",".$$ordernoid.",".$$breakdownid.",".$$txtbuyerPoId.",".$$txtremarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					$id_arr_rec[]=$id1;
					$id1=$id1+1; $add_commaa++;
				}
				else if(str_replace("'","",$$updatedtlsid)!="")
				{
					$data_arr_up[str_replace("'","",$$updatedtlsid)]=explode("*",("".$$cboProcessName."*".$$txtreceiveqty."*".$$cbouom."*".$$ordernoid."*".$$breakdownid."*".$$txtbuyerPoId."*".$$txtremarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					$id_arr_rec[]=str_replace("'","",$$updatedtlsid);
					$hdn_break_id_arr[]=str_replace("'","",$$updatedtlsid);
				}
			}
			$flag=1;
			$rID=sql_update("sub_material_mst",$field_array,$data_array,"id",$update_id,0); 
			if($rID==1 && $flag==1) $flag=1; else $flag=0;	
			if($data_array2!="")
			{
				//echo "10**INSERT INTO sub_material_dtls (".$field_array2.") VALUES ".$data_array2; die;
				$rID2=sql_insert("sub_material_dtls",$field_array2,$data_array2,1);
				if($rID2==1 && $flag==1) $flag=1; else $flag=0;
			}
				
			if($data_arr_up!="")
			{
				//echo "10**".bulk_update_sql_statement( "sub_material_dtls", "id", $field_arr_up,$data_arr_up,$hdn_break_id_arr);
				$rID3=execute_query(bulk_update_sql_statement( "sub_material_dtls", "id", $field_arr_up,$data_arr_up,$hdn_break_id_arr),1);
				if($rID3==1 && $flag==1) $flag=1; else $flag=0;
			}
			
			$field_array_del="status_active*is_deleted*updated_by*update_date";
			$data_array_del="'0'*'1'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			//print_r ($distance_delete_id);
			
			$distance_delete_id="";
		
			if(implode(',',$id_arr_rec)!="")
			{
				$distance_delete_id=implode(',',array_diff($all_dtls_id_arr,$id_arr_rec));
			}
			else
			{
				$distance_delete_id=implode(',',$all_dtls_id_arr);
			}
			
			if(str_replace("'",'',$distance_delete_id)!="")
			{
				$ex_delete_id=explode(",",$distance_delete_id);
				$rID4=execute_query(bulk_update_sql_statement( "sub_material_dtls", "id", $field_array_del,$data_array_del,$ex_delete_id),1);
				if($rID4==1 && $flag==1) $flag=1; else $flag=0;
			}
		}
		
		
		//echo "10**".$rID."**".$rID2."**".$rID3."**".$rID4."**".implode(',',$all_dtls_id_arr); die;
		 
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_job_no);	
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_job_no);
			}
		}
		else if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_job_no);	
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_job_no);
			}
		}
		disconnect($con); die;
	}
	else if ($operation==2)   // delete
	{
		$con = connect();
		
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		 //echo $zero_val;
		$iss_number=return_field_value( "sys_no", "sub_material_mst"," embl_job_no=$txt_job_no and status_active=1 and is_deleted=0 and trans_type=2");
		if($iss_number){
			echo "emblIssue**".str_replace("'","",$txt_job_no)."**".$iss_number;
			disconnect($con); die;
		}
		/*if ( $zero_val==1 )
		{*/
			$field_array="status_active*is_deleted*updated_by*update_date";
			$data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
			$data_array_dtls="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
			
			/*if (str_replace("'",'',$cbo_status)==1)
			{
				$rID=sql_update("sub_material_dtls",$field_array,$data_array_dtls,"id",$update_id2,1); //disconnect($con); die;
			}
			else
			{*/
			$flag=1;
				$rID=sql_update("sub_material_mst",$field_array,$data_array,"id",$update_id,0); 
				if($rID==1 && $flag==1) $flag=1; else $flag=0; 
				//echo "INSERT INTO sub_material_dtls (".$field_array.") VALUES ".$data_array_dtls; die;

				$rID1=sql_update("sub_material_dtls",$field_array,$data_array_dtls,"mst_id",$update_id,1);
				if($rID1==1 && $flag==1) $flag=1; else $flag=0;  
			//}
		/*}
		else
		{
			$rID=0;
		}*/
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$update_id2);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$update_id2);
			}
		}
		else if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "2**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$update_id2);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$update_id2);
			}
		}
		disconnect($con);  die;
	}
}

if ($action=="receive_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	$data=explode("_",$data);
	$company=$data[0];
	$location=$data[1];
	$party_name=$data[2];
	$within_group=$data[3];
	?>
	<script>
		function js_set_value(id)
		{ 
			$("#hidden_mst_id").val(id);
			document.getElementById('selected_job').value=id;
			parent.emailwindow.hide();
		}
		
		function fnc_load_party_order_popup(company,party_name,within_group)
		{   //alert(company+'_'+party_name+'_'+within_group);	
			load_drop_down( 'embellishment_material_receive_controller', company+'_'+within_group+'_'+party_name, 'load_drop_down_buyer_pop', 'buyer_td' );
		}
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0) $('#search_by_td').html('Embl. Job No');
			else if(val==2) $('#search_by_td').html('W/O No');
			else if(val==3) $('#search_by_td').html('Buyer Job');
			else if(val==4) $('#search_by_td').html('Buyer Po');
			else if(val==5) $('#search_by_td').html('Buyer Style');
		}		
	</script>
	</head>
	<body onLoad="fnc_load_party_order_popup(<? echo $company;?>,<? echo $party_name;?>,<? echo $within_group;?>)">
        <div align="center" style="width:100%;" >
            <form name="searchreceivefrm_1"  id="searchreceivefrm_1" autocomplete="off">
                <table width="870" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                        <tr>
                            <th colspan="10"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                        </tr>
                    	<tr>                	 
                            <th width="140">Company Name</th>
                            <th width="50">Within Group</th>
                            <th width="120">Party Name</th>
                            <th width="70">Receive ID</th>
                            <th width="80">Challan No</th>
                            <th width="100">Search By</th>
                    		<th width="100" id="search_by_td">Embl. Job No</th>
                            <th width="100" colspan="2">Date Range</th>
                            <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                        </tr>         
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td> <input type="hidden" id="selected_job">  <!--  echo $data;-->
							<? 
								echo create_drop_down( "cbo_company_name", 140, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company, "load_drop_down( 'embellishment_material_receive_controller', this.value+'_'+".$within_group.", 'load_drop_down_buyer_pop', 'buyer_td' );"); ?>
                            </td>
                            <td>
							<?
								echo create_drop_down( "cbo_within_group", 50, $yes_no,"", 0, "--  --",$within_group, "load_drop_down( 'embellishment_material_receive_controller',document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_buyer_pop', 'buyer_td' );" ); ?>
							</td>
                            <td id="buyer_td">
								<? 
								echo create_drop_down( "cbo_party_name", 120, $blank_array,"", 1, "-- Select Party --", $selected, "" );?>
                            </td>
                            <td>
                                <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes_numeric" style="width:60px" placeholder="Receive ID" />
                            </td>
                            <td>
                                <input type="text" name="txt_search_challan" id="txt_search_challan" class="text_boxes" style="width:70px" placeholder="Challan" />
                            </td>
                            <td>
								<?
                                    $search_by_arr=array(1=>"Embl. Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style");
                                    echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                                ?>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                            </td>
                            <td>
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From">
                            </td>
                            <td>
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px" placeholder="To">
                            </td> 
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_search_challan').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value, 'create_receive_search_list_view', 'search_div', 'embellishment_material_receive_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
                        </tr>
                        <tr>
                            <td colspan="10" align="center" valign="middle">
								<? echo load_month_buttons(1);  ?>
                                <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
                            </td>
                        </tr>
                    </tbody>
                </table> 
                <div id="search_div"></div>   
            </form>
        </div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_receive_search_list_view")
{
	$data=explode('_',$data);
	$search_type =$data[6];
	$within_group =$data[7];
	$search_by=str_replace("'","",$data[8]);
	$search_str=trim(str_replace("'","",$data[9]));

	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer_cond=" and a.party_id='$data[1]'"; else $buyer_cond="";
	if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $recieve_date = "and a.subcon_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $recieve_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $recieve_date = "and a.subcon_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $recieve_date ="";
	}
	//echo $search_type."_".$search_str."_".$search_by; die;
	
	$job_cond=""; $style_cond=""; $po_cond="";  $search_com_cond=""; $style_cond2=""; $po_cond2="";
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num = '$search_str' ";
			else if ($search_by==4) {$po_cond=" and b.po_number = '$search_str' "; $po_cond2=" and b.buyer_po_no = '$search_str' ";}
			else if ($search_by==5) {$style_cond=" and a.style_ref_no = '$search_str' "; $style_cond2=" and b.buyer_style_ref = '$search_str' ";}

			
		}
		//echo $style_cond2; die;
		if ($data[4]!='') $rec_id_cond=" and a.prefix_no_num='$data[4]'"; else $rec_id_cond="";
		if ($data[5]!='') $challan_no_cond=" and a.chalan_no='$data[5]'"; else $challan_no_cond="";
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str%'";  
			else if ($search_by==4) {$po_cond=" and b.po_number like '%$search_str%'"; $po_cond2=" and b.buyer_po_no = '$search_str'"; }
			else if ($search_by==5) {$style_cond=" and a.style_ref_no like '%$search_str%'"; $style_cond2=" and b.buyer_style_ref = '$search_str'"; } 

			
		}
		if ($data[4]!='') $rec_id_cond=" and a.prefix_no_num like '%$data[4]%'"; else $rec_id_cond="";
		if ($data[5]!='') $challan_no_cond=" and a.chalan_no like '%$data[5]%'"; else $challan_no_cond="";
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '$search_str%'";  
			else if ($search_by==4) {$po_cond=" and b.po_number like '$search_str%'"; $po_cond2=" and b.buyer_po_no = '$search_str'";}
			else if ($search_by==5) {$style_cond=" and a.style_ref_no like '$search_str%'"; $style_cond2=" and b.buyer_style_ref = '$search_str'"; } 

			 
		}
		if ($data[4]!='') $rec_id_cond=" and a.prefix_no_num like '$data[4]%'"; else $rec_id_cond="";
		if ($data[5]!='') $challan_no_cond=" and a.chalan_no like '$data[5]%'"; else $challan_no_cond="";
		if ($data[9]!='') $order_no_cond=" and order_no like '$data[9]%'"; else $order_no_cond="";
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==4) {$po_cond=" and b.po_number like '%$search_str'"; $po_cond2=" and b.buyer_po_no = '$search_str'";}
			else if ($search_by==5) {$style_cond=" and a.style_ref_no like '%$search_str'"; $style_cond2=" and b.buyer_style_ref = '$search_str'"; } 

			
		}
		if ($data[4]!='') $rec_id_cond=" and a.prefix_no_num like '%$data[4]'"; else $rec_id_cond="";
		if ($data[5]!='') $challan_no_cond=" and a.chalan_no like '%$data[5]'"; else $challan_no_cond="";
	}

	//echo $style_cond2; die;
	
	$party_arr=return_library_array( "SELECT id, buyer_name from lib_buyer where status_active =1 and is_deleted=0",'id','buyer_name');
	$comp=return_library_array("SELECT id, company_name from lib_company where status_active =1 and is_deleted=0",'id','company_name');
	$po_arr=return_library_array( "SELECT id,order_no from subcon_ord_dtls where status_active =1 and is_deleted=0",'id','order_no');
	
	$po_ids=''; $buyer_po_arr=array();
	if($within_group==1)
	{
		if($db_type==0) $id_cond="group_concat(b.id)";
		else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
		//echo "select $id_cond as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
		if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
		{
			$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0", "id");
		}
		//echo $po_ids;
		if ($po_ids!="") $po_idsCond=" and b.buyer_po_id in ($po_ids)"; else $po_idsCond="";
		
		$po_sql ="SELECT a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
		$po_sql_res=sql_select($po_sql);
		foreach ($po_sql_res as $row)
		{
			$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
			$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		}
		unset($po_sql_res);
	}
	$spo_ids='';
	
	if($db_type==0)
	{
		$id_cond="group_concat(b.id)";
		$insert_date_cond="year(a.insert_date)";
		$wo_cond="group_concat(distinct(b.job_dtls_id))";
		$buyer_po_id_cond="group_concat(distinct(b.buyer_po_id))";
	}
	else if($db_type==2)
	{
		$id_cond="listagg(b.id,',') within group (order by b.id)";
		$insert_date_cond="TO_CHAR(a.insert_date,'YYYY')";
		$wo_cond="listagg(b.job_dtls_id,',') within group (order by b.job_dtls_id)";
		$buyer_po_id_cond="listagg(b.buyer_po_id,',') within group (order by b.buyer_po_id)";
	}

	//echo $style_cond2."_".$search_by."_".$search_com_cond."_".$po_cond2."_".$search_type; die;


	if(($search_com_cond!="" && $search_by==1) || ($search_com_cond!="" && $search_by==2) || ($po_cond2!="" && $search_by==4) || ($style_cond2!="" && $search_by==5))
	{
		//echo "select id form subcon_ord_mst a, subcon_ord_dtls b where a.embellishment_job=b.job_no_mst $po_cond2 $style_cond2 $search_com_cond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0"; die;

		$spo_ids = return_field_value("$id_cond as id", "subcon_ord_mst a, subcon_ord_dtls b", "a.embellishment_job=b.job_no_mst $po_cond2 $style_cond2 $search_com_cond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0", "id");
	}
	
	if ( $spo_ids!="") $spo_idsCond=" and b.job_dtls_id in ($spo_ids)"; else $spo_idsCond="";


	$style_sql ="SELECT a.id,a.buyer_po_no,a.buyer_style_ref from subcon_ord_dtls a where a.status_active =1 and a.is_deleted =0";
	$style_sql_res=sql_select($style_sql);
	$style_po_arr=array();
	foreach ($style_sql_res as $row)
	{
		$style_po_arr[$row[csf("id")]]['buyer_style_ref']=$row[csf("buyer_style_ref")];
		$style_po_arr[$row[csf("id")]]['buyer_po_no']=$row[csf("buyer_po_no")];
	}
	unset($style_sql_res);
	
	$sql= "SELECT a.id, a.sys_no, a.prefix_no_num, $insert_date_cond as year, a.location_id, a.within_group, a.party_id, a.subcon_date, a.chalan_no, a.remarks, b.job_dtls_id, a.embl_job_no, $wo_cond as order_id, $buyer_po_id_cond as buyer_po_id from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=1 and a.entry_form='205' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $recieve_date $company $buyer_cond $withinGroup $rec_id_cond $challan_no_cond $spo_idsCond $po_idsCond group by a.id, a.sys_no, a.prefix_no_num, a.insert_date, a.location_id, a.within_group, a.party_id, a.subcon_date, a.chalan_no, a.remarks, b.job_dtls_id, a.embl_job_no order by a.id DESC ";
	//echo $sql; 
	$result = sql_select($sql);
	?>
    <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table">
            <thead>
                <th width="40" >SL</th>
                <th width="70" >Receive No</th>
                <th width="70" >Year</th>
                <th width="120" >Party Name</th>
                <th width="100" >Challan No</th>
                <th width="80" >Receive Date</th>
                <th width="120">Order No</th>
                <th width="100">Buyer Po</th>
                <th>Buyer Style</th>
            </thead>
     	</table>
     <div style="width:820px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" id="tbl_po_list">
			<?
			$i=1;
            foreach($result as $row)
            {
                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$order_no='';
				$order_id=array_unique(explode(",",$row[csf("order_id")]));
				foreach($order_id as $val)
				{
					if($order_no=="") $order_no=$po_arr[$val]; else $order_no.=",".$po_arr[$val];
				}
				$order_no=implode(",",array_unique(explode(",",$order_no)));
				
				$buyer_po=""; $buyer_style="";
				$buyer_po_id=explode(",",$row[csf('buyer_po_id')]);
				foreach($buyer_po_id as $po_id)
				{
					if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
					if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
				}
				//$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
				//$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));

				if ($row[csf("within_group")]==1) {
					$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
					$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
				}else{
					$buyer_po=$style_po_arr[$row[csf("job_dtls_id")]]['buyer_po_no'];
					$buyer_style=$style_po_arr[$row[csf("job_dtls_id")]]['buyer_style_ref'];
				}
				
				$party_name="";
				if($row[csf("within_group")]==1) $party_name=$comp[$row[csf("party_id")]]; else $party_name=$party_arr[$row[csf("party_id")]];
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row[csf("id")]."_".$row[csf("embl_job_no")];?>');" > 
						<td width="40" align="center"><? echo $i; ?></td>
						<td width="70" align="center"><? echo $row[csf("prefix_no_num")]; ?></td>
                        <td width="70" align="center"><? echo $row[csf("year")]; ?></td>
                        <td width="120"><? echo $party_name; ?></td>		
						<td width="100"><? echo $row[csf("chalan_no")]; ?></td>
						<td width="80"><? echo change_date_format($row[csf("subcon_date")]);  ?></td>
                        <td width="120" style="word-break:break-all"><p><? echo $order_no; ?></p></td>	
                        <td width="100" style="word-break:break-all"><? echo $buyer_po; ?></td>
                        <td style="word-break:break-all"><? echo $buyer_style; ?></td>
						
					</tr>
				<? 
				$i++;
            }
   		?>
			</table>
		</div>
     </div>
     <?	
	exit();
}

if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "SELECT id, sys_no, company_id, location_id, party_id, subcon_date, chalan_no,within_group, embl_job_no,from_company_name,from_location_name,remarks from sub_material_mst where id='$data' and status_active =1 and is_deleted=0" ); 
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_receive_no').value 		= '".$row[csf("sys_no")]."';\n";
		echo "document.getElementById('cbo_company_name').value 	= '".$row[csf("company_id")]."';\n";
		echo "$('#cbo_company_name').attr('disabled','true')".";\n"; 
		
		echo "document.getElementById('cbo_within_group').value		= '".$row[csf("within_group")]."';\n"; 
		echo "load_drop_down( 'requires/embellishment_material_receive_controller', '".$row[csf("company_id")]."', 'load_drop_down_location', 'location_td' );\n";
		
		echo "load_drop_down( 'requires/embellishment_material_receive_controller', '".$row[csf("company_id")]."'+'_'+'".$row[csf("within_group")]."', 'load_drop_down_buyer', 'buyer_td' );\n"; 
		
		echo "load_drop_down( 'requires/embellishment_material_receive_controller', '".$row[csf("from_company_name")]."', 'load_drop_down_from_location', 'location_from_td' );\n";		
		
		echo "document.getElementById('cbo_location_name').value	= '".$row[csf("location_id")]."';\n";  
		echo "document.getElementById('cbo_party_name').value		= '".$row[csf("party_id")]."';\n"; 
		echo "document.getElementById('txt_receive_challan').value	= '".$row[csf("chalan_no")]."';\n"; 
		echo "document.getElementById('txt_receive_date').value 	= '".change_date_format($row[csf("subcon_date")])."';\n";  
		echo "document.getElementById('txt_job_no').value			= '".$row[csf("embl_job_no")]."';\n"; 
		echo "document.getElementById('cbo_from_company_name').value			= '".$row[csf("from_company_name")]."';\n"; 
		echo "document.getElementById('cbo_from_location_name').value			= '".$row[csf("from_location_name")]."';\n"; 
		echo "document.getElementById('txt_receive_remarks').value				= '".$row[csf("remarks")]."';\n"; 
	    echo "document.getElementById('update_id').value            = '".$row[csf("id")]."';\n";
		//echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_material_receive',1);\n";
		
		echo "$('#cbo_within_group').attr('disabled','true')".";\n"; 
		echo "$('#cbo_party_name').attr('disabled','true')".";\n"; 
	}
	exit();
}

if ($action=="job_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	$data=explode("_",$data);
	?>
	<script>
		function js_set_value(id)
		{ 
			document.getElementById('selected_order').value=id;
			parent.emailwindow.hide();
		}
		
		function fnc_load_party_order_popup(company,within_group,party)
		{
			//alert();
			load_drop_down( 'embellishment_order_entry_controller', company+'_'+within_group+'_'+party, 'load_drop_down_buyer', 'buyer_td' );
			
			$('#cbo_party_name').attr('disabled','disabled');
		}
		
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0) $('#search_by_td').html('Embl. Job No');
			else if(val==2) $('#search_by_td').html('W/O No');
			else if(val==3) $('#search_by_td').html('Buyer Job');
			else if(val==4) $('#search_by_td').html('Buyer Po');
			else if(val==5) $('#search_by_td').html('Buyer Style');
		}
		
	</script>
	</head>
	<body onLoad="fnc_load_party_order_popup(<? echo $data[0];?>,<? echo $data[3];?>,<? echo $data[2];?>)">
        <div align="center" style="width:100%;" >
            <form name="searchjobfrm_1"  id="searchjobfrm_1" autocomplete="off">
                <table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                        <tr>
                            <th colspan="6"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --",0 ); ?></th>
                        </tr>
                    	<tr>               	 
                            <th width="140">Company Name</th>
                            <th width="140">Party Name</th>
                            <th width="170">Date Range</th>
                            <th width="100">Search By</th>
                            <th width="100" id="search_by_td">Buyer Po</th>
                            <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                        </tr>         
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td><input type="hidden" id="selected_order">  
								<?   
									echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $data[0],"",1 );
                                ?>
                            </td>
                            <td id="buyer_td"><? echo create_drop_down( "cbo_party_name", 140, $blank_array,"", 1, "-- Select Party --", $selected, "" ); ?>
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                            </td> 
                            <td>
								<?
									$search_by_arr=array(1=>"Embl. Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style");
									echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",2,'search_by(this.value)',"","" );
								?>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                            </td>
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+<? echo $data[3];?>, 'create_job_search_list_view', 'search_div', 'embellishment_material_receive_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6" align="center" valign="middle">
								<? echo load_month_buttons(1);  ?>
                                <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
                            </td>
                        </tr>
                    </tbody>
                </table> 
                <br>
                <div id="search_div"></div>   
            </form>
        </div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_job_search_list_view(backup)")
{	
	$data=explode('_',$data);
	
	//print_r($data);
	$search_by=str_replace("'","",$data[4]);
	$search_str=trim(str_replace("'","",$data[5]));
	$search_type =$data[6];
	$within_group =$data[7];
	
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { $company=""; echo "PLease Select Company name"; die;}
	if ($data[1]!=0) $buyer=" and a.party_id='$data[1]'"; else { $buyer=""; echo "PLease Select Party name"; die;}
	if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
	
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
 			if ($search_by==3) $job_cond=" and a.job_no_prefix_num = '$search_str' ";
			else if ($search_by==5) $style_cond=" and b.buyer_style_ref = '$search_str' ";
			else if ($search_by==4) $po_cond=" and b.buyer_po_no = '$search_str' ";
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str%'";  
			else if ($search_by==5) $style_cond=" and b.buyer_style_ref like '%$search_str%'";  
			else if ($search_by==4) $po_cond=" and b.buyer_po_no like '%$search_str%'";  
		}
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '$search_str%'";  
			else if ($search_by==5) $style_cond=" and b.buyer_style_ref like '$search_str%'";  
			else if ($search_by==4) $po_cond=" and b.buyer_po_no like '$search_str%'";  
		}
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==5) $style_cond=" and b.buyer_style_ref like '%$search_str'";   
			else if ($search_by==4) $po_cond=" and b.buyer_po_no like '%$search_str'";  
		}
	}	
	
	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and a.receive_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $order_rcv_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and a.receive_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $order_rcv_date ="";
	}
	
	$po_ids='';
	
	if($db_type==0) $id_cond="group_concat(b.id)";
	else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	if(($job_cond!="" && $search_by==3) || ($style_cond!="" && $search_by==4)|| ($po_cond!="" && $search_by==5))
	{
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
	}
	
	if ($po_ids!="") $po_idsCond=" and b.buyer_po_id in ($po_ids)"; else $po_idsCond="";
	
	$buyer_po_arr=array();
	
	$po_sql ="SELECT a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);
	
	$comp=return_library_array( "SELECT id, company_name from lib_company where status_active =1 and is_deleted=0",'id','company_name');
	//$color_lib_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	//$arr=array (3=>$comp,6=>$yes_no,7=>$color_lib_arr);
	
	if($db_type==0)
	{
		$ins_year_cond="year(a.insert_date)";
		$color_id_str="group_concat(c.color_id)";
		$buyer_po_id_cond="year(b.buyer_po_id)";
	}
	else if($db_type==2)
	{
		$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
		$color_id_str="listagg(c.color_id,',') within group (order by c.color_id)";
		$buyer_po_id_cond="listagg(b.buyer_po_id,',') within group (order by b.buyer_po_id)";
	}
	 $sql= "SELECT a.id, a.embellishment_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date, $color_id_str as color_id, $buyer_po_id_cond as buyer_po_id  
	 from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
	 where a.entry_form=204 and a.embellishment_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and a.id=b.mst_id and b.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $order_rcv_date $company $buyer $withinGroup $search_com_cond $po_idsCond $job_cond $style_cond $po_cond and b.id=c.mst_id  
	 group by a.id, a.embellishment_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date
	 order by a.id DESC";

	//echo $sql;die;

	$data_array=sql_select($sql);
	?>
     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="885" >
        <thead>
            <th width="30">SL</th>
            <th width="60">Job No</th>
            <th width="60">Year</th>
            <th width="120">W/O No</th>
            <th width="100">Buyer Po</th>
            <th width="100">Buyer Style</th>
            <th width="80">Ord Receive Date</th>
            <th width="80">Delivery Date</th>
            <th>Color</th>
        </thead>
        </table>
        <div style="width:885px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="865" class="rpt_table" id="tbl_po_list">
        <tbody>
            <? 
            $i=1;
            foreach($data_array as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$excolor_id=array_unique(explode(",",$row[csf('color_id')]));
				$color_name="";	
				foreach ($excolor_id as $color_id)
				{
					if($color_name=="") $color_name=$color_arr[$color_id]; else $color_name.=','.$color_arr[$color_id];
				}
				$buyer_po=""; $buyer_style="";
				$buyer_po_id=explode(",",$row[csf('buyer_po_id')]);
				foreach($buyer_po_id as $po_id)
				{
					if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
					if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
				}
				$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
				$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('embellishment_job')]; ?>")' style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                    <td width="60" style="text-align:center;"><? echo $row[csf('year')]; ?></td>
                    <td width="120" style="word-break:break-all"><? echo $row[csf('order_no')]; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $buyer_po; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $buyer_style; ?></td>
                    <td width="80" style="text-align:center;"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                    <td width="80" style="text-align:center;"><? echo change_date_format($row[csf('delivery_date')]); ?></td>	
                    <td style="word-break:break-all"><? echo $color_name; ?></td>
                </tr>
				<? 
                $i++; 
            } 
            ?>
        </tbody>
    </table>
	<?    
	exit();
}
if($action=="create_job_search_list_view")
{	
	$data=explode('_',$data);
	
	//print_r($data);
	$search_by=str_replace("'","",$data[4]);
	$search_str=trim(str_replace("'","",$data[5]));
	$search_type =$data[6];
	$within_group =$data[7];
	
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { $company=""; echo "PLease Select Company name"; die;}
	if ($data[1]!=0) $buyer=" and a.party_id='$data[1]'"; else { $buyer=""; echo "PLease Select Party name"; die;}
	if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
	
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
			
			if ($search_by==3) $job_cond=" and d.job_no = '$search_str' ";
			else if ($search_by==5) $style_cond=" and b.buyer_style_ref = '$search_str' ";
			else if ($search_by==4) $po_cond=" and b.buyer_po_no = '$search_str' ";
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";  
			
			if ($search_by==3) $job_cond=" and d.job_no like '%$search_str%'";  
			else if ($search_by==5) $style_cond=" and b.buyer_style_ref like '%$search_str%'";  
			else if ($search_by==4) $po_cond=" and b.buyer_po_no like '%$search_str%'";  
		}
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
			
			if ($search_by==3) $job_cond=" and d.job_no like '$search_str%'";  
			else if ($search_by==5) $style_cond=" and b.buyer_style_ref like '$search_str%'";  
			else if ($search_by==4) $po_cond=" and b.buyer_po_no like '$search_str%'";  
		}
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
			
			if ($search_by==3) $job_cond=" and d.job_no like '%$search_str'";  
			else if ($search_by==5) $style_cond=" and b.buyer_style_ref like '%$search_str'";   
			else if ($search_by==4) $po_cond=" and b.buyer_po_no like '%$search_str'";  
		}
	}	
	
	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and a.receive_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $order_rcv_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and a.receive_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $order_rcv_date ="";
	}
	
	$po_ids='';
	
	if($db_type==0) $id_cond="group_concat(b.id)";
	else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	if(($job_cond!="" && $search_by==3) || ($style_cond!="" && $search_by==4)|| ($po_cond!="" && $search_by==5))
	{
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
	}
	
	if ($po_ids!="") $po_idsCond=" and b.buyer_po_id in ($po_ids)"; else $po_idsCond="";
	
	$buyer_po_arr=array();
	
	$po_sql ="SELECT a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);
	
	$comp=return_library_array( "SELECT id, company_name from lib_company where status_active =1 and is_deleted=0",'id','company_name');
	//$color_lib_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	//$arr=array (3=>$comp,6=>$yes_no,7=>$color_lib_arr);
	
	if($db_type==0)
	{
		$ins_year_cond="year(a.insert_date)";
		$color_id_str="group_concat(c.color_id)";
		$buyer_po_id_cond="year(b.buyer_po_id)";
	}
	else if($db_type==2)
	{
		$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
		//$color_id_str="listagg(c.color_id,',') within group (order by c.color_id)";
		//$buyer_po_id_cond="listagg(b.buyer_po_id,',') within group (order by b.buyer_po_id)";
		
		$color_id_str=",rtrim(xmlagg(xmlelement(e,c.color_id,',').extract('//text()') order by c.color_id).GetClobVal(),',') as color_id";
		//$buyer_po_id_cond="listagg(b.buyer_po_id,',') within group (order by b.buyer_po_id)";
		$buyer_po_id_cond=",rtrim(xmlagg(xmlelement(e,b.buyer_po_id,',').extract('//text()') order by b.buyer_po_id).GetClobVal(),',') as buyer_po_id";
		
		
	}
	if($within_group==1){
		$sql= "SELECT a.id, a.embellishment_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.within_group, b.buyer_po_no, b.buyer_style_ref, a.order_no, a.delivery_date $color_id_str  $buyer_po_id_cond 
		from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, wo_booking_mst d
		where a.entry_form=204 and a.embellishment_job=b.job_no_mst and c.order_id=d.id and b.job_no_mst=c.job_no_mst and a.id=b.mst_id and b.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $order_rcv_date $company $buyer $withinGroup $search_com_cond $po_idsCond $job_cond $style_cond $po_cond and b.id=c.mst_id  
		group by a.id, a.embellishment_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.within_group, b.buyer_po_no, b.buyer_style_ref, a.order_no, a.delivery_date
		order by a.id DESC";
	}
	else 
	{
		$sql= "SELECT a.id, a.embellishment_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.within_group, b.buyer_po_no, b.buyer_style_ref, a.order_no, a.delivery_date  $color_id_str  $buyer_po_id_cond 
		from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
		where a.entry_form=204 and a.embellishment_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and a.id=b.mst_id and b.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_rcv_date $company $buyer $withinGroup $search_com_cond $po_idsCond $job_cond $style_cond $po_cond and b.id=c.mst_id  
		group by a.id, a.embellishment_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.within_group, b.buyer_po_no, b.buyer_style_ref, a.order_no, a.delivery_date
		order by a.id DESC";
		
	}

	//echo $sql;die;

	$data_array=sql_select($sql);
	?>
     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="885" >
        <thead>
            <th width="30">SL</th>
            <th width="60">Job No</th>
            <th width="60">Year</th>
            <th width="120">W/O No</th>
            <th width="100">Buyer Po</th>
            <th width="100">Buyer Style</th>
            <th width="80">Ord Receive Date</th>
            <th width="80">Delivery Date</th>
            <th>Color</th>
        </thead>
        </table>
        <div style="width:885px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="865" class="rpt_table" id="tbl_po_list">
        <tbody>
            <? 
            $i=1;
            foreach($data_array as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				//$excolor_id=array_unique(explode(",",$row[csf('color_id')]));
				
				$color=$row[csf('color_id')];
				if($db_type==2) $color = $color->load();
				//$excolor_id=array_unique(explode(",",$row[csf('color_id')]));
				$excolor_id=array_unique(explode(",",$color));
				$color_name="";	
				foreach ($excolor_id as $color_id)
				{
					if($color_name=="") $color_name=$color_arr[$color_id]; else $color_name.=','.$color_arr[$color_id];
				}
				$buyer_po=""; $buyer_style="";
				
				$buyer_po_ids=$row[csf('buyer_po_id')];
				if($db_type==2) $buyer_po_ids = $buyer_po_ids->load();
				
				//$order_id=explode(",",$row[csf('order_id')]);
 				$buyer_po_id=array_unique(explode(",",$buyer_po_ids));
				
				
				//$buyer_po_id=explode(",",$row[csf('buyer_po_id')]);
				foreach($buyer_po_id as $po_id)
				{
					if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
					if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
				}
				//$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
				//$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));

				if ($row[csf('within_group')]==1) {
					$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
					$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
				}else{

					$buyer_po=$row[csf('buyer_po_no')];
					$buyer_style=$row[csf('buyer_style_ref')];
				}


                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('embellishment_job')]; ?>")' style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                    <td width="60" style="text-align:center;"><? echo $row[csf('year')]; ?></td>
                    <td width="120" style="word-break:break-all"><? echo $row[csf('order_no')]; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $buyer_po; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $buyer_style; ?></td>
                    <td width="80" style="text-align:center;"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                    <td width="80" style="text-align:center;"><? echo change_date_format($row[csf('delivery_date')]); ?></td>	
                    <td style="word-break:break-all"><? echo $color_name; ?></td>
                </tr>
				<? 
                $i++; 
            } 
            ?>
        </tbody>
    </table>
	<?    
	exit();
}

if($action=="load_php_dtls_form")
{
	//echo $data;
	$exdata=explode("**",$data);
	$jobno=''; $update_id=0;
	$update_id=$exdata[0];
	$jobno=$exdata[1];
	$bundle_variable=$exdata[3];
	$within_group=$exdata[4];
	$color_arrey=return_library_array( "SELECT id,color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
	$size_arrey=return_library_array( "SELECT id,size_name from  lib_size where status_active =1 and is_deleted=0",'id','size_name');
	$buyer_po_arr=array();
	
	$po_sql ="SELECT a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);
	
/*	$buyer_po_sql="select a.subcon_job, a.within_group, a.party_id, b.id, b.order_no, b.order_quantity, b.buyer_po_id,b.buyer_po_no, b.buyer_style_ref, 
   b.buyer_buyer from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.entry_form=295 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$buyer_sql_res=sql_select($buyer_po_sql);
	foreach ($buyer_sql_res as $row)
	{
		$order_buyer_po_arr[$row[csf("id")]]['style']=$row[csf("buyer_style_ref")];
		$order_buyer_po_arr[$row[csf("id")]]['po']=$row[csf("buyer_po_no")];
		$order_buyer_po_arr[$row[csf("id")]]['buyer']=$row[csf("buyer_name")];
	}
	unset($po_sql_res);
	*/
	
	
	$updtls_data_arr=array(); $pre_qty_arr=array();
	$sql_rec="SELECT a.id, a.mst_id, a.quantity, a.uom, a.job_dtls_id, a.job_break_id, a.buyer_po_id, a.remarks from sub_material_dtls a, sub_material_mst b where b.id=a.mst_id and b.embl_job_no='$jobno' and b.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$sql_rec_res =sql_select($sql_rec);
	
	foreach ($sql_rec_res as $row)
	{
		if($row[csf("mst_id")]==$update_id)
		{
			$updtls_data_arr[$row[csf("job_dtls_id")]][$row[csf("job_break_id")]]['dtlsid']=$row[csf("id")];
			$updtls_data_arr[$row[csf("job_dtls_id")]][$row[csf("job_break_id")]]['qty']=$row[csf("quantity")];
			$updtls_data_arr[$row[csf("job_dtls_id")]][$row[csf("job_break_id")]]['remarks']=$row[csf("remarks")];
		}
		else
		{
			$pre_qty_arr[$row[csf("job_dtls_id")]][$row[csf("job_break_id")]]['qty']+=$row[csf("quantity")];
		}
	}

	$updtls_issue_data_arr=array(); $pre_issue_qty_arr=array();
	
	 $sql_iss="SELECT a.id, a.mst_id, a.quantity, a.uom, a.job_dtls_id, a.job_break_id, a.buyer_po_id from sub_material_dtls a, sub_material_mst b where b.id=a.mst_id and b.embl_job_no='$jobno' and b.trans_type=2 and b.entry_form=207 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$sql_iss_res =sql_select($sql_iss);
	
	foreach ($sql_iss_res as $row)
	{
		if($row[csf("mst_id")]==$update_id)
		{
			$updtls_issue_data_arr[$row[csf("job_dtls_id")]][$row[csf("job_break_id")]]['dtlsid']=$row[csf("id")];
			$updtls_issue_data_arr[$row[csf("job_dtls_id")]][$row[csf("job_break_id")]]['qty']=$row[csf("quantity")];
		}
		else
		{
			$pre_issue_qty_arr[$row[csf("job_dtls_id")]][$row[csf("job_break_id")]]['qty']+=$row[csf("quantity")];
		}
	}
	 //print_r($updtls_data_arr);
	// print_r($pre_qty_arr);
	unset($sql_iss_res);
	
	$sql_job="SELECT a.id, a.embellishment_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id as po_id, b.buyer_po_id, b.gmts_item_id, b.embl_type, b.body_part, b.main_process_id, b.order_uom, c.id as breakdown_id, c.description, c.color_id, c.size_id, c.qnty,b.buyer_po_no, b.buyer_style_ref
		from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
		where a.entry_form=204 and a.embellishment_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id  and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and a.embellishment_job='$jobno' and c.qnty>0 order by c.id ASC";
	$sql_result =sql_select($sql_job);
	$k=0;
	$num_rowss=count($sql_result);
	foreach ($sql_result as $row)
	{
		$k++;
		if($row[csf("main_process_id")]==1) $emb_type=$emblishment_print_type;
		else if($row[csf("main_process_id")]==2) $emb_type=$emblishment_embroy_type;
		else if($row[csf("main_process_id")]==3) $emb_type=$emblishment_wash_type;
		else if($row[csf("main_process_id")]==4) $emb_type=$emblishment_spwork_type;
		else if($row[csf("main_process_id")]==5) $emb_type=$emblishment_gmts_type;
		else $emb_type="";
		
		$quantity=0; $dtlsup_id=""; $balanceQty=0; $prerec_qty=0; $preissue_qty=0; $orderQty=0; $remarks='';
		$prerec_qty=$pre_qty_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['qty'];
		$preissue_qty=$pre_issue_qty_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['qty'];
		//$orderQty=number_format($row[csf("qnty")]*12,4,'.','');
		
		if($row[csf("order_uom")]==1) { $orderQty=number_format($row[csf("qnty")],4,'.','');}
		if($row[csf("order_uom")]==2) { $orderQty=number_format($row[csf("qnty")]*12,4,'.','');}
		
		$balanceQty=number_format($orderQty-$prerec_qty,4,'.','');
		
		if($update_id!=0)
		{
			$quantity=$updtls_data_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['qty'];
			$remarks=$updtls_data_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['remarks'];
		}
		else $quantity='';
		if($quantity==0) $quantity='';
		if($balanceQty==0) $balanceQty=0;
		
		$dtlsup_id=$updtls_data_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['dtlsid'];

		if ($update_id>0) {
			$quantity=$quantity;
		}else{
			$quantity=$balanceQty;
		}
		
		if ($bundle_variable>0 && $within_group==2) {
			$qty_popup=" ondblclick='fnc_bundle_details($k)'";
			$qty_chk='';
			//$readonly="readonly='readonly'";
			$readonly="";
			
		}else{
			$qty_chk=" onKeyUp='check_receive_qty_ability(this.value,$k); fnc_total_calculate();'";
			$qty_popup='';
			$readonly='';
		}
		
		?>
		<tr>
            <td><input type="hidden" name="ordernoid_<? echo $k; ?>" id="ordernoid_<? echo $k; ?>" value="<? echo $row[csf("po_id")]; ?>">
                <input type="hidden" name="jobno_<? echo $k; ?>" id="jobno_<? echo $k; ?>" value="<? echo $row[csf("embellishment_job")]; ?>">
                <input type="hidden" name="updatedtlsid_<? echo $k; ?>" id="updatedtlsid_<? echo $k; ?>" value="<? echo $dtlsup_id; ?>">
                <input type="hidden" name="breakdownid_<? echo $k; ?>" id="breakdownid_<? echo $k; ?>" value="<? echo $row[csf("breakdown_id")]; ?>">
                <input type="text" name="txtorderno_<? echo $k; ?>" id="txtorderno_<? echo $k; ?>" class="text_boxes" style="width:90px" value="<? echo $row[csf("order_no")]; ?>" readonly />
            </td>
            <td><input name="txtbuyerPo_<? echo $k; ?>" id="txtbuyerPo_<? echo $k; ?>" type="text" class="text_boxes" style="width:90px" value="<?
			
			echo $row[csf("buyer_po_no")]; //$buyer_po_arr[$row[csf("buyer_po_id")]]['po']; ?>" readonly />
                <input name="txtbuyerPoId_<? echo $k; ?>" id="txtbuyerPoId_<? echo $k; ?>" type="hidden" class="text_boxes" style="width:70px" value="<? echo $row[csf("buyer_po_id")]; ?>" />
            </td>
            <td><input name="txtstyleRef_<? echo $k; ?>" id="txtstyleRef_<? echo $k; ?>" type="text" class="text_boxes" style="width:90px" value="<?  echo $row[csf("buyer_style_ref")]; //$buyer_po_arr[$row[csf("buyer_po_id")]]['style']; ?>" readonly /></td>
            <td><? echo create_drop_down( "cboProcessName_".$k, 80, $emblishment_name_array,"", 1, "--Select--",$row[csf("main_process_id")],"", 1,"" ); ?></td>
            <td id="reType_<? echo $k; ?>"><? echo create_drop_down( "cboReType_".$k, 80, $emb_type,"", 1, "Select Item", $row[csf("embl_type")], "",1); ?></td>
            <td><? echo create_drop_down( "cboGmtsItem_".$k, 90, $garments_item,"", 1, "-- Select --",$row[csf("gmts_item_id")], "",1,"" ); ?></td>
            <td><? echo create_drop_down( "cboBodyPart_".$k, 90, $body_part,"", 1, "-- Select --",$row[csf("body_part")], "",1,"" ); ?></td>
            <td>
                <input type="text" id="txtmaterialdescription_<? echo $k; ?>" name="txtmaterialdescription_<? echo $k; ?>" class="text_boxes" style="width:110px" value="<? echo $row[csf("description")]; ?>" readonly title="Maximum 200 Character" >
            </td>
            <td><input type="text" id="txtcolor_<? echo $k; ?>" name="txtcolor_<? echo $k; ?>" class="text_boxes" value="<? echo $color_arrey[$row[csf("color_id")]]; ?>" style="width:50px" readonly/></td>
            <td><input type="text" id="txtsize_<? echo $k; ?>" name="txtsize_<? echo $k; ?>" class="text_boxes" style="width:50px" value="<? echo $size_arrey[$row[csf("size_id")]]; ?>" readonly/></td>
            <td><input name="txtordqty_<? echo $k; ?>" id="txtordqty_<? echo $k; ?>" value="<? echo $orderQty; ?>" class="text_boxes_numeric" type="text" style="width:50px" disabled/></td>
            <td><input name="txtpreqty_<? echo $k; ?>" id="txtpreqty_<? echo $k; ?>" value="<? echo $prerec_qty; ?>" class="text_boxes_numeric" type="text" style="width:50px" disabled/>
            <input name="preissyqty_<? echo $k; ?>" id="preissyqty_<? echo $k; ?>" value="<? echo $preissue_qty; ?>" class="text_boxes_numeric" type="hidden" style="width:50px" disabled/>
            </td>
            <td><input name="txtreceiveqty_<? echo $k; ?>" id="txtreceiveqty_<? echo $k; ?>" class="text_boxes_numeric" type="text"  value="<? echo $quantity; ?>" placeholder="<? echo $balanceQty; ?>" pre_rec_qty="<? echo $prerec_qty; ?>" order_qty="<? echo $orderQty; ?>" <? echo $qty_popup; echo $qty_chk ; echo $readonly; ?> style="width:60px" /></td>
            <td><? echo create_drop_down( "cbouom_".$k,50, $unit_of_measurement,"", 1, "-Select-",1,"", 1,"" );?></td>
            <td><input type="text" name="txtremarks_<? echo $k; ?>" id="txtremarks_<? echo $k; ?>" style="width:40px" value="<? echo $remarks; ?>" class="text_boxes" placeholder="Remark" onClick="openmypage_remarks(<? echo $k; ?>);" /></td>
        </tr>
	<?	
	}
	exit();
}


if ($action=="matarial_receive_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data); die;


	$jobno=''; $update_id=0;
	$update_id=$data[1];
	$jobno=$data[3];
	$bundle_variable=$data[8];
	$within_group=$data[6];

	

	$sql= "SELECT id, sys_no, company_id, location_id, party_id, subcon_date, chalan_no,within_group, embl_job_no,from_company_name,from_location_name,remarks from sub_material_mst where id='$data[1]' and status_active =1 and is_deleted=0";

	//echo $sql; die;

	$dataArray=sql_select($sql);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location_arr=return_library_array( "select id, location_name from lib_location", "id", "location_name"  );
	if ($data[6]==1) {
		$buyer_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	}else{
		$buyer_arr=return_library_array( "select id,buyer_name from  lib_buyer", "id","buyer_name"  );
	}
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$floor_arr = return_library_array("select id, floor_name from  lib_prod_floor","id","floor_name");
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	?>
	<div style="width:1080px;">
    <table width="1060" cellspacing="0" border="0">
        <tr>
            <td colspan="2" rowspan="3">
			<img src="../../../<? echo $image_location; ?>" height="60" width="200" style="float:left;">
			</td>
			<td colspan="3" align="center" style="font-size:22px">
            <strong><? echo $company_library[$data[0]]; ?></strong>
            </td>
        </tr>
        <tr class="form_caption">
        	<td colspan="3" align="center" style="font-size:14px">
				<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
					foreach ($nameArray as $result)
					{
					?>
						Plot No: <? echo $result['plot_no']; ?>
						Level No: <? echo $result['level_no']?>
						Road No: <? echo $result['road_no']; ?>
						Block No: <? echo $result['block_no'];?>
						City No: <? echo $result['city'];?>
						Zip Code: <? echo $result['zip_code']; ?>
						Province No: <? echo $result['province'];?>
						Country: <? echo $country_arr[$result['country_id']]; ?><br>
						Email Address: <? echo $result['email'];?>
						Website No: <? echo $result['website'];
					}
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="3" align="center" style="font-size:18px"><strong><u><? echo $data[7]; ?></u></strong></td>
            
        </tr>
        <tr>
        	<td width="85"><strong>Receive ID</strong></td>
            <td width="125px"><strong>: </strong><? echo $dataArray[0][csf('sys_no')]; ?></td>
            <td width="100"><strong>Receive Date</strong></td>
            <td width="175px"><strong>: </strong><? echo change_date_format($dataArray[0][csf('subcon_date')]); ?></td>
            <td width="110"><strong>Company</strong></td>
            <td><strong>: </strong><? echo $company_library[$dataArray[0][csf('company_id')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Receive Challan</strong></td>
            <td><strong>: </strong><? echo $dataArray[0][csf('chalan_no')]; ?></td>
            <td><strong>Within Group</strong></td>
            <td><strong>: </strong><? echo $yes_no[$dataArray[0][csf('within_group')]]; ?></td>
            <td><strong>Location</strong></td>
            <td width="175px"><strong>: </strong><? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></td>
        </tr>
         <tr>
			<td><strong>Job No</strong></td>
			<td><strong>: </strong><? echo $dataArray[0][csf('embl_job_no')]; ?> </td>
           	<td><strong>Buyer</strong></td>
           	<td colspan="5" ><strong>: </strong><? echo $buyer_arr[$dataArray[0][csf('party_id')]]; ?></td>
       </tr>
    </table>
	<div style="width:100%;">
    <table cellspacing="0" width="1160"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" style="font-size:13px">
            <th width="30">SL</th>
            <th width="100">Order No</th>
            <th width="100">Buyer PO</th>
            <th width="100">Style Ref.</th>
            <th width="120">Garments Item</th>
            <th width="120">Body Part</th>
            <th width="80">Color</th>
            <th width="80">GMTS Size</th>
            <th width="80">Order Qty</th>
            <th width="80">Prev. Rec. Qty</th>
            <th width="80">Receive Qty</th>
            <th width="60">UOM</th>
            <th>Remarks</th>
        </thead>
        <tbody style="font-size:11px">
	<?


	$color_arrey=return_library_array( "SELECT id,color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
	$size_arrey=return_library_array( "SELECT id,size_name from  lib_size where status_active =1 and is_deleted=0",'id','size_name');
	$buyer_po_arr=array();
	
	$po_sql ="SELECT a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);
	

	
	
	$updtls_data_arr=array(); $pre_qty_arr=array();
	$sql_rec="SELECT a.id, a.mst_id, a.quantity, a.uom, a.job_dtls_id, a.job_break_id, a.buyer_po_id, a.remarks from sub_material_dtls a, sub_material_mst b where b.id=a.mst_id and b.embl_job_no='$jobno' and b.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$sql_rec_res =sql_select($sql_rec);
	
	foreach ($sql_rec_res as $row)
	{
		if($row[csf("mst_id")]==$update_id)
		{
			$updtls_data_arr[$row[csf("job_dtls_id")]][$row[csf("job_break_id")]]['dtlsid']=$row[csf("id")];
			$updtls_data_arr[$row[csf("job_dtls_id")]][$row[csf("job_break_id")]]['qty']=$row[csf("quantity")];
			$updtls_data_arr[$row[csf("job_dtls_id")]][$row[csf("job_break_id")]]['remarks']=$row[csf("remarks")];
		}
		else
		{
			$pre_qty_arr[$row[csf("job_dtls_id")]][$row[csf("job_break_id")]]['qty']+=$row[csf("quantity")];
		}
	}

	$updtls_issue_data_arr=array(); $pre_issue_qty_arr=array();
	
	 $sql_iss="SELECT a.id, a.mst_id, a.quantity, a.uom, a.job_dtls_id, a.job_break_id, a.buyer_po_id from sub_material_dtls a, sub_material_mst b where b.id=a.mst_id and b.embl_job_no='$jobno' and b.trans_type=2 and b.entry_form=207 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$sql_iss_res =sql_select($sql_iss);
	
	foreach ($sql_iss_res as $row)
	{
		if($row[csf("mst_id")]==$update_id)
		{
			$updtls_issue_data_arr[$row[csf("job_dtls_id")]][$row[csf("job_break_id")]]['dtlsid']=$row[csf("id")];
			$updtls_issue_data_arr[$row[csf("job_dtls_id")]][$row[csf("job_break_id")]]['qty']=$row[csf("quantity")];
		}
		else
		{
			$pre_issue_qty_arr[$row[csf("job_dtls_id")]][$row[csf("job_break_id")]]['qty']+=$row[csf("quantity")];
		}
	}
	 //print_r($updtls_data_arr);
	// print_r($pre_qty_arr);
	unset($sql_iss_res);
	
	$sql_job="SELECT a.id, a.embellishment_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id as po_id, b.buyer_po_id, b.gmts_item_id, b.embl_type, b.body_part, b.main_process_id, b.order_uom, c.id as breakdown_id, c.description, c.color_id, c.size_id, c.qnty,b.buyer_po_no, b.buyer_style_ref
		from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
		where a.entry_form=204 and a.embellishment_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id  and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and a.embellishment_job='$jobno' and c.qnty>0 order by c.id ASC";
	$sql_result =sql_select($sql_job);



	//$k=0; 
	$i=1; $pre_recei_qty=0; $receive_qty=0;
	$num_rowss=count($sql_result);
	foreach ($sql_result as $row)
	{
		//$k++;
		if($row[csf("main_process_id")]==1) $emb_type=$emblishment_print_type;
		else if($row[csf("main_process_id")]==2) $emb_type=$emblishment_embroy_type;
		else if($row[csf("main_process_id")]==3) $emb_type=$emblishment_wash_type;
		else if($row[csf("main_process_id")]==4) $emb_type=$emblishment_spwork_type;
		else if($row[csf("main_process_id")]==5) $emb_type=$emblishment_gmts_type;
		else $emb_type="";
		
		$quantity=0; $dtlsup_id=""; $balanceQty=0; $prerec_qty=0; $preissue_qty=0; $orderQty=0; $remarks='';
		$prerec_qty=$pre_qty_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['qty'];
		$preissue_qty=$pre_issue_qty_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['qty'];
		//$orderQty=number_format($row[csf("qnty")]*12,4,'.','');
		
		if($row[csf("order_uom")]==1) { $orderQty=number_format($row[csf("qnty")],4,'.','');}
		if($row[csf("order_uom")]==2) { $orderQty=number_format($row[csf("qnty")]*12,4,'.','');}
		
		//$balanceQty=number_format($orderQty-$prerec_qty,4,'.','');
		
		if($update_id!=0)
		{
			$quantity=$updtls_data_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['qty'];
			$remarks=$updtls_data_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['remarks'];
		}
		else $quantity='';
		if($quantity==0) $quantity='';
		//if($balanceQty==0) $balanceQty=0;
		
		$dtlsup_id=$updtls_data_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['dtlsid'];

		/*if ($update_id>0) {
			$quantity=$quantity;
		}else{
			$quantity=$balanceQty;
		}*/
		
		/*if ($bundle_variable>0 && $within_group==2) {
			$qty_popup=" ondblclick='fnc_bundle_details($k)'";
			$qty_chk='';
			//$readonly="readonly='readonly'";
			$readonly="";
			
		}else{
			$qty_chk=" onKeyUp='check_receive_qty_ability(this.value,$k); fnc_total_calculate();'";
			$qty_popup='';
			$readonly='';
		}*/
	?>
		<tr bgcolor="<? echo $bgcolor; ?>">
			<td align="center"><? echo $i; ?></td>
			<td align="center"><? echo $row[csf("order_no")]; ?></td>
			<td align="center"><? echo $row[csf("buyer_po_no")]; ?></td>
			<td align="center"><? echo $row[csf("buyer_style_ref")]; ?></td>
			<td align="center"><? echo $garments_item[$row[csf("gmts_item_id")]]; ?></td>
            <td align="center"><? echo $body_part[$row[csf("body_part")]]; ?></td>
            <td align="center"><? echo $color_arrey[$row[csf("color_id")]]; ?></td>
			<td align="center"><? echo $size_arrey[$row[csf("size_id")]]; ?></td>
			<td align="right"><? echo number_format($orderQty,2); ?></td>
			<td align="right"><? echo number_format($prerec_qty,2); ?></td>
			<td align="right"><? echo number_format($quantity,2); ?></td>
			<td align="center"><? echo $unit_of_measurement[$row[csf("order_uom")]]; ?></td>
			<td align="center"><? echo $remarks; //$row[csf("remarks")]; ?></td>
		</tr>
		<? 

		$pre_recei_qty+=$prerec_qty; 
		$receive_qty+=$quantity;

		$i++; 
	} 

	?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="9" align="right">Grand Total :</td>
            <td align="right"><? echo $pre_recei_qty; ?></td>
            <td align="right"><? echo $receive_qty; ?></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
    </tfoot>
    </table>
        <br>
		 <?
            echo signature_table(157, $data[0], "1160px");
         ?>
	</div>
	</div>
     <script type="text/javascript" src="../../../js/jquery.js"></script>
      
	<?
	exit();
}



if($action=="remarks_popup")
{
	echo load_html_head_contents("Remarks","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
    <script>
	function js_set_value(val)
	{
		document.getElementById('text_new_remarks').value=val;
		parent.emailwindow.hide();
	}
	</script>
    </head>
<body>
<div align="center">
	<fieldset style="width:400px;margin-left:4px;">
        <form name="remarksfrm_1"  id="remarksfrm_1" autocomplete="off">
            <table cellpadding="0" cellspacing="0" width="370" >
                <tr>
                    <td align="center"><input type="hidden" name="auto_id" id="auto_id" value="<? echo $data; ?>" />
                      <textarea id="text_new_remarks" name="text_new_remarks" class="text_area" title="Maximum 1000 Character" maxlength="1000" style="width:330px; height:270px" placeholder="Remarks Here. Maximum 1000 Character." ><? echo $data; ?></textarea>
                    </td>
                </tr>
                <tr>
                	<td align="center">
                 <input type="button" id="formbuttonplasminus" align="middle" class="formbutton" style="width:100px" value="Close" onClick="js_set_value(document.getElementById('text_new_remarks').value)" />
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


if($action=="qty_bundle_popup")
{
    echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
    extract($_REQUEST);
    $data=explode('_', $data);
    $row=$data[0];
    $update_id=$data[1];
    $emb_job=$data[2];
    $break_id=$data[3];
    $po_id=$data[4];
    $within_group=$data[5];
    $itemRcvDtlsId=$data[6];
    //1__OG-POE-21-00056_11845_5895_2 
    ?>
    <script>
        //if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";
        var permission='<?=$permission; ?>';
        
        function frm_close()
        {
            parent.emailwindow.hide();
        }

        function reset_for_refresh()
        {
            reset_form('quick_cosing_entry','','','','','update_id*hid_qc_no');
        }

       

		function show_hide_content(row, id){
			//alert(row)
			$('#contentBundle_'+row).toggle('slow', function() {
				 //get_php_form_data( id, 'set_php_form_data', '../woven_order/requires/size_color_breakdown_controller' );
			});
		}

		function create_bundle_list( type )
		{ 
			var row_num 	=1; var manual_bundle_no='';
			var no_of_row 	= $('#txtNoOfBundle'+type).val()*1;
			var table_id 	= 'tblBundleEntry'+type;
			var tr_id 		= 'trBundle'+type+'_';
			var pcs_per_bundle = $('#txtPcsPerBundle'+type).val();
			var prev_no_of_bundle = $('#hidPrevNoOfBdl'+type).val()*1;
			var txtHidBundleNo =$('#txtHidBundleNo'+type+'_'+row_num).val()*1;
			var txtBundleNo =$('#txtBundleNo'+type+'_'+row_num).val();
			var embJob =$('#txtEmbJob').val();
			var splt_val=txtBundleNo.split("-");
			if(prev_no_of_bundle==no_of_row){
				return;
			}else{
				var bundle_qty=no_of_row*pcs_per_bundle;
				$('#txtBundleQty'+type).val(bundle_qty);
				//alert(prev_no_of_bundle+'=='+no_of_row)
				//10==5
				//3**tblBundleEntry1**trBundle1_**1**50**5
				//alert(no_of_row+"**"+table_id+"**"+tr_id+"**"+row_num+"**"+pcs_per_bundle+"**"+prev_no_of_bundle);
				if(prev_no_of_bundle>no_of_row){
					for(var k=no_of_row+1;k<=prev_no_of_bundle;k++){
						//alert(tr_id+k)
						$("#"+tr_id+k).remove(); 
					}
				}else{ 
					var manual_bundle_no=return_global_ajax_value(embJob, 'check_last_bundle_no', '', 'embellishment_material_receive_controller');
					var bundle_no_splt_val=manual_bundle_no.split("-");
					var BundleNoPrefixNum=bundle_no_splt_val[2];
					var manualBundleNo=''; 
					if($('#txtBdlDtlsUpId'+type+'_'+row_num).val()==''){
						BundleNoPrefixNum++
						manualBundleNo=splt_val[0]+'-'+splt_val[1]+'-'+BundleNoPrefixNum;
						$('#txtHidBundleNo'+type+'_'+row_num).val(BundleNoPrefixNum);
						$('#txtBundleNo'+type+'_'+row_num).val(manualBundleNo);
					}
					//$('#bundle_variable').val(response);

					if (prev_no_of_bundle!=0)
					{
						var current_row_num=prev_no_of_bundle;
						row_num=current_row_num;
					}else{
						var current_row_num=1;
					}
					current_row_num=current_row_num*1;
					//alert(current_row_num);
					for(var i=current_row_num; i<no_of_row; i++){
						//alert(current_row_num);
						var clone= $("#"+tr_id+i).clone();
						row_num++;
						clone.attr({
							id: tr_id + row_num,
						});

						clone.find("input,select").each(function(){
							$(this).attr({ 
								'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ row_num },
								//'name': function(_, name) { var name=name.split("_"); return name[0] },
								'name': function(_, name) { return name },
								'value': function(_, value) { return value }
							});
						}).end();
						$("#"+tr_id+i).after(clone);
						$('#txtPcsNoOfBundle'+type+'_'+row_num).val(row_num);
						$('#txtBarcode'+type+'_'+row_num).val('');
						$('#txtBarcodeId'+type+'_'+row_num).val('');
						$('#txtBdlDtlsUpId'+type+'_'+row_num).val('');
						txtHidBundleNo++ ;  BundleNoPrefixNum++ ;
						$('#txtHidBundleNo'+type+'_'+row_num).val(BundleNoPrefixNum);
						manual_bundle_no=splt_val[0]+'-'+splt_val[1]+'-'+BundleNoPrefixNum;
						$('#txtBundleNo'+type+'_'+row_num).val(manual_bundle_no);
						//alert(i);
						//$('#txtPcsBundleQty'+type+'_'+i).val(pcs_per_bundle);
					}
				}
				$('#hidPrevNoOfBdl'+type).val(no_of_row);
				assign_bundle_qty(type);
				cal_total_bundle();
				set_all_onclick();
			}
		}
		
		function assign_bundle_qty(type){
			var pcs_per_bundle = $('#txtPcsPerBundle'+type).val()*1;
			var tbaleId= 'tblBundleEntry'+type+ ' tbody tr';
			//var j=0; var check_field=0; data_all="";
			//$("#"+tbaleId).length;
			var row_num =$("#"+tbaleId).length;
			for(var i=1; i<=row_num; i++){
				$('#txtPcsBundleQty'+type+'_'+i).val(pcs_per_bundle);
			}
			cal_total_bundle();
		}
			
		function fnc_save_create(type){
			var delete_master_info=0; var i=0;
			var operation 		=0;
			var txtPcsPerBundle = $('#txtPcsPerBundle'+type).val()*1;
			var txtNoOfBundle 	= $('#txtNoOfBundle'+type).val()*1;
			var txtBundleQty 	= $('#txtBundleQty'+type).val()*1;
			var txtBundleMstId 	= $('#txtBundleMstId'+type).val()*1;
			var txtRemarks 		= $('#txtRemarks'+type).val();
			var txtCutNo 		= $('#txtCutNo'+type).val();
			var txtTotalType 	= $('#txtTotalType').val();
			var txtPoId 		= $('#txtPoId').val();
			var txtOrdBreakdownId = $('#txtOrdBreakdownId').val();
			//var txtBundleMstId = $('#txtBundleMstId').val();
			var itemRcvDtlsId 	= $('#itemRcvDtlsId').val();
			var rcvId 			= $('#rcvId').val();
			var embJob 			= $('#txtEmbJob').val();
			var txtCompanyId 	= $('#txtCompanyId').val();
			var tbaleId= 'tblBundleEntry'+type+ ' tbody tr';
			var j=0; var check_field=0; data_all="";
			$("#"+tbaleId).each(function()
			{
				var pcsNoOfBundle 	='txtPcsNoOfBundle'+type;
				var hidBundleNo 	='txtHidBundleNo'+type;
				var bundleNo 		='txtBundleNo'+type;
				var pcsBundleQty 	='txtPcsBundleQty'+type;
				var barcode 		='txtBarcode'+type;
				var bundleId 		='txtBarcodeId'+type;
				var bundleUpdateId 	='txtBdlDtlsUpId'+type;
				var dtlsCutNo 		='txtDtlsCutNo'+type;

				var txtPcsNoOfBundle 	= $(this).find('input[name="'+pcsNoOfBundle+'[]"]').val();
				var txtHidBundleNo 		= $(this).find('input[name="'+hidBundleNo+'[]"]').val();
				var txtBundleNo 		= $(this).find('input[name="'+bundleNo+'[]"]').val();
				var txtPcsBundleQty 	= $(this).find('input[name="'+pcsBundleQty+'[]"]').val();
				var txtBarcode 			= $(this).find('input[name="'+barcode+'[]"]').val();
				var txtBarcodeId 		= $(this).find('input[name="'+bundleId+'[]"]').val();
				var txtBdlDtlsUpId 		= $(this).find('input[name="'+bundleUpdateId+'[]"]').val();
				var txtDtlsCutNo 		= $(this).find('input[name="'+dtlsCutNo+'[]"]').val();
				//alert(txtPcsNoOfBundle+'=='+txtPcsBundleQty+'=='+txtBarcode+'=='+txtBarcodeId+'=='+txtBdlDtlsUpId);
				
				/*if( txtPcsNoOfBundle ==0 || txtPcsNoOfBundle =='')
				{	
					alert('Please Write a Pcs Per Bundle');
					check_field=1 ; return; 				
					
				}*/
				
				if(check_field==0)
				{
					j++;
					data_all += "&txtPcsNoOfBundle_" + j + "='" + txtPcsNoOfBundle + "'&txtHidBundleNo_" + j + "='" + txtHidBundleNo + "'&txtBundleNo_" + j + "='" + txtBundleNo + "'&txtPcsBundleQty_" + j + "='" + txtPcsBundleQty+ "'&txtBarcode_" + j + "='" + txtBarcode+ "'&txtBarcodeId_" + j + "='" + txtBarcodeId+ "'&txtBdlDtlsUpId_" + j + "='" + txtBdlDtlsUpId+ "'&txtDtlsCutNo_" + j + "='" + txtDtlsCutNo + "'";
					i++;
				}
			});
			//alert (data_all); 
			//return;
			if(check_field==0)
			{
				var data="action=save_update_delete_bundle&operation="+operation+'&total_row='+i+'&txtPcsPerBundle='+txtPcsPerBundle+'&txtNoOfBundle='+txtNoOfBundle+'&txtBundleQty='+txtBundleQty+'&txtRemarks='+txtRemarks+'&txtCutNo='+txtCutNo+'&txtTotalType='+txtTotalType+'&txtPoId='+txtPoId+'&txtOrdBreakdownId='+txtOrdBreakdownId+'&txtBundleMstId='+txtBundleMstId+'&itemRcvDtlsId='+itemRcvDtlsId+'&txtCompanyId='+txtCompanyId+'&rcvId='+rcvId+'&embJob='+embJob+'&type='+type+data_all; 
				 
				//freeze_window(5); 
				//alert (operation); return;
				http.open("POST","embellishment_material_receive_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_job_order_entry_response;
			}
			else
			{
				return;
			}
		}

		function fnc_job_order_entry_response()
		{
			
			if(http.readyState == 4) 
			{
				//alert (http.responseText);//return;
				var response=trim(http.responseText).split('**');
				
				if(response[0]==0)
				{
					$('#txtBundleMstId'+response[2]).val(response[1]);
					var saved_bundle_data=trim(return_global_ajax_value(response[2]+'**'+response[1], 'saved_bundle_list_view', '', 'embellishment_material_receive_controller'));
					
					var tbodyId= 'tblBundleTbody'+response[2];
					$("#"+tbodyId).html(saved_bundle_data);
					if(response[3]==0)
					{
						var new_bundle_data=trim(return_global_ajax_value(response[2]+'**'+response[4], 'create_new_bundle_list_view', '', 'embellishment_material_receive_controller'));
						$('#append_div1:last').append(new_bundle_data);
						var txtTotalType = $('#txtTotalType').val()*1;
						txtTotalType++;
						$('#txtTotalType').val(txtTotalType);
					}

				}
				else if(response[0]==786) // for duplicate barcode chk
				{
					alert(response[1]);
				}
				else if(response[0]==121) // for duplicate barcode chk
				{
					alert(response[1]);
				}
				/*else if(response[0]==2)
				{
					location.reload();
				}*/
				show_msg(response[0]);
				release_freezing();
			}
		}

		function cal_total_bundle(){
			var totalType = $('#txtTotalType').val()*1;
			//alert (totalType);
			var pcsPerBundle=0; var noOfBundle=0; var bundleQty=0;
			for (var i = 1; i <= totalType; i++) {
				pcsPerBundle += $('#txtPcsPerBundle'+i).val()*1; 
				noOfBundle += $('#txtNoOfBundle'+i).val()*1; 
				bundleQty += $('#txtBundleQty'+i).val()*1; 
			}
			$('#txtTotalPcsPerBundle').val(pcsPerBundle);
			$('#txtTotalPcsNoOfBundle').val(noOfBundle);
			$('#txtTotalPcsBundleQty').val(bundleQty);
		}

		function check_all_report()
		{
			$("input[name=chkBundle]").each(function(index, element) { 
					
				if( $('#check_all').prop('checked')==true) 
					$(this).attr('checked','true');
				else
					$(this).removeAttr('checked');
			});
		}

		function fnc_bundle_report_one_urmi()
		{
			var data="";
			var error=1;
			
			var totalType = $('#txtTotalType').val()*1;
			for(var i=1; i<=totalType; i++){
				var tbaleId= 'tblBundleEntry'+i+ ' tbody tr';
				var row_num =$("#"+tbaleId).length;
				for (var j = 1; j <= row_num; j++){
					var txtBdlDtlsUpId 	='txtBdlDtlsUpId'+i+'_'+j;
					var chkBundle 	='chkBundle'+i+'_'+j;
					//alert (txtBdlDtlsUpId);
					if($("#"+chkBundle).is(":checked")==true){
						if(data=="") data=$("#"+txtBdlDtlsUpId).val(); else data=data+","+$("#"+txtBdlDtlsUpId).val();
						error=0;
					}
				}
			}
				
			//alert(data)
			if( error==1 )
			{
				alert('No data selected');
				return;
			}
			var job_id=$("#jobId").val();
			var order_id=$("#txtPoId").val();
			var rcvId=$("#rcvId").val();
			var details_id=$("#itemRcvDtlsId").val();
			var cbo_color_id=$("#colorId").val();
			var cbo_gmt_id=$("#sizeId").val();
			var txtCompanyId=$("#txtCompanyId").val();
			var chk_status= $("#check_all").prop('checked');
			if( chk_status==true)
			{
				data=420;
			}

			data=data+"***"+job_id+'***'+rcvId+'***'+details_id+'***'+cbo_gmt_id+'***'+cbo_color_id+'***'+order_id+'***'+txtCompanyId;
			//data=data+"***"+job_id;
			//alert(data); return;
			var title = 'Search Job No';	
			var page_link = 'embellishment_material_receive_controller.php?data='+data+'&action=print_report_bundle_barcode_eight';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=270px,height=250px,center=1,resize=0,scrolling=0','../../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]; 
				var prodID=this.contentDoc.getElementById("txt_selected_id").value;
				data=data+'***'+prodID;
				var url=return_ajax_request_value(data, "print_barcode_one_urmi", "embellishment_material_receive_controller");
				window.open(url,"##");	
				//window.open("embellishment_material_receive_controller.php?data=" + data+'&action=print_barcode_one_urmi', true );
			}

			//var url=return_ajax_request_value(data, "print_report_bundle_barcode", "embellishment_material_receive_controller");
			//window.open(url,"##");
		}


	function fnc_copy_cut_no(type)
	{
		 
		//var row_num = $('#details_tbl tbody tr').length;
		var tbaleId= 'tblBundleEntry'+type+ ' tbody tr';
		var row_num =$("#"+tbaleId).length;
		var cut_no = $('#txtCutNo'+type).val()*1;
		//alert(tbaleId+'='+row_num+'='+cut_no);
		if(document.getElementById('is_copy').checked==true)
		{
			for(i=1;i<=row_num;i++)
			{
				$('#txtDtlsCutNo'+type+'_'+i).val(cut_no);
			}
		}
		else
		{
			for(i=1;i<=row_num;i++)
			{
				$('#txtDtlsCutNo'+type+'_'+i).val('');
			}
		}
		fnc_total_calculate();
	}
		

    </script>
    <body onLoad="set_hotkey();">
    	<table width="100%" id="tbl_mst">
    		<?

    		if($db_type==0)
			{
				$insert_date_cond=", year(a.insert_date) as year";
			}
			else if($db_type==2)
			{
				$insert_date_cond=", TO_CHAR(a.insert_date,'YYYY') as year";
			}

			$sql_job="SELECT a.within_group,a.company_id , a.id, a.embellishment_job, a.order_id, a.order_no, b.id as po_id, c.id as breakdown_id, c.description, c.color_id, c.size_id, c.qnty, b.order_uom,b.gmts_item_id,a.job_no_prefix_num $insert_date_cond
			from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
			where a.entry_form=204 and a.embellishment_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id  and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and a.embellishment_job='$emb_job' and c.id= $break_id and b.id=$po_id and c.qnty>0 order by c.id ASC";
			$size_arr=return_library_array( "select id,size_name from  lib_size where status_active=1 and is_deleted=0",'id','size_name');
			$dataArray =sql_select($sql_job);

			$sql_bundle="SELECT a.id as bundl_mst_id, a.company_id, a.po_id, a.order_breakdown_id, a.item_rcv_dtls_id, a.pcs_per_bundle, a.no_of_bundle, a.bundle_qty, a.remarks,a.cut_no as mst_cut_no,b.id, b.mst_id, b.item_rcv_id, b.pcs_per_bundle as dtls_pcs_per_bundle, b.bundle_qty as dtls_bundle_qty, b.barcode_no, b.barcode_id ,b.bundle_no_prefix_num, b.bundle_no ,b.cut_no from prnting_bundle_mst a, prnting_bundle_dtls b where a.id=b.mst_id and b.item_rcv_id=$update_id and a.item_rcv_dtls_id=$itemRcvDtlsId and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.id ASC";
			$bdlArray =sql_select($sql_bundle);
			$noOf_bundle=0; $pcsPer_bundle=0; $bundleQty=0; $totalType=1; $bundle_chk_arr=array();
			if(count($bdlArray)>0){
				foreach ($bdlArray as $row) 
				{
					$noOf_bundle++;
				 	$bundle_arr[$row[csf('bundl_mst_id')]][$row[csf('pcs_per_bundle')]][$row[csf('no_of_bundle')]][$row[csf('bundle_qty')]][$row[csf('remarks')]][$row[csf('mst_cut_no')]].=$row[csf('id')].'_'.$row[csf('dtls_pcs_per_bundle')].'_'.$row[csf('dtls_bundle_qty')].'_'.$row[csf('barcode_no')].'_'.$row[csf('barcode_id')].'_'.$row[csf('bundle_no_prefix_num')].'_'.$row[csf('bundle_no')].'_'.$row[csf('cut_no')].'#';
				 	if (!in_array($row[csf('bundl_mst_id')], $bundle_chk_arr)){
				 		$pcsPer_bundle +=$row[csf('pcs_per_bundle')];
				 		$totalType ++;
				 	}
				 	$bundle_chk_arr[$row[csf('bundl_mst_id')]]=$row[csf('bundl_mst_id')];
				 	$bundleQty +=$row[csf('dtls_bundle_qty')];
				}
			}
			//echo "<pre>";
			//print_r($bundle_arr);
        
        	//$field_array_dtls=" id, mst_id, item_rcv_id, pcs_per_bundle, bundle_qty, barcode_no, inserted_by, insert_date, status_active, is_deleted";
       	 	//$field_array_barcode=" id, rcv_id, rcv_dtls_id, order_id, bundle_id, bundle_dtls_id, size_id, barcode_no, barcode_year, barcode_prifix, inserted_by, insert_date, status_active, is_deleted";
    		?>
    		<tr>
    			<th colspan="4"> Within Group: <?= $yes_no[$dataArray[0][csf("within_group")]] ; ?>,  Job No: <?= $dataArray[0][csf("embellishment_job")] ; ?>,  Order No: <?= $dataArray[0][csf("order_no")] ; ?>,  GMT Item: <?= $garments_item[$dataArray[0][csf("gmts_item_id")]] ; ?>,  Size: <?= $size_arr[$dataArray[0][csf("size_id")]] ; ?>, Receive Qty.: <?= $dataArray[0][csf("qnty")].' '.$unit_of_measurement[$dataArray[0][csf("order_uom")]]; ?></th>
    			
    			<th width="60" align="right"><input type="button" id="btn_stiker_urmi" name="btn_stiker_urmi" value="Sticker 1/Page" class="formbutton" onClick="fnc_bundle_report_one_urmi()"/></th>
    			<th width="60" align="right"><strong>Check All</strong></th>
    			<th width="25"  style="margin-right: 0;"><input type="checkbox" name="check_all"  id="check_all" onClick="check_all_report()"></th>
    		</tr>
    		
    	</table>
    			<!-- <tr id="trMst_1"> --> 
    	<?
    	$job_no=explode('-',$dataArray[0][csf("embellishment_job")]);

    	$sql_bundle_no="SELECT max(c.bundle_no_prefix_num) as bundle_no_prefix_num from subcon_ord_mst a, sub_material_mst b, prnting_bundle_dtls c where a.embellishment_job=b.embl_job_no and b.id=c.item_rcv_id and a.embellishment_job='$emb_job' and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0  and c.status_active =1 and c.is_deleted =0";
		$bdlNoArray =sql_select($sql_bundle_no);
		$last_bundle_no=$bdlNoArray[0][csf("bundle_no_prefix_num")]; 

		if($last_bundle_no=='') $last_bundle_no=1; else $last_bundle_no=$last_bundle_no+1;
		$manual_bundle_no=$dataArray[0][csf("job_no_prefix_num")].'-'.$job_no[2].'-'.$last_bundle_no;


    	if(count($bdlArray)>0)
    	{ 
    		$i=1;
    		foreach ($bundle_arr as $bundl_mst_id => $bundl_mst_data ) 
			{
				foreach ($bundl_mst_data as $pcs_per_bundle => $pcs_per_bundle_data ) 
				{
					foreach ($pcs_per_bundle_data as $no_of_bundle => $no_of_bundle_data ) 
					{
						foreach ($no_of_bundle_data as $bundle_qty => $bundle_qty_data ) 
						{
							foreach ($bundle_qty_data as $remarks => $remarks_data ) 
							{	
								foreach ($remarks_data as $cut_no => $row ) 
								{
									?>
							    	<h3 align="left" class="accordion_h" onClick="show_hide_content(<?=$i ?>, '')"> +Bundle Entry- <?= $i; ?></h3>
									<div id="contentBundle_<?= $i ?>" style="display:none;">
									<fieldset>
									    <form id="fabriccost_<?= $i ?>" autocomplete="off">
									    <table width="1045" cellspacing="0" class="rpt_table" border="0" id="tblBundleEntry<?= $i; ?>" rules="all">
									        <thead> 
									            <tr>
									                <th width="80" style="color:#2A3FFF">Pcs Per Bundle </th>
									                <th width="100"><input class="text_boxes_numeric"  type="text" name="txtPcsPerBundle<?= $i; ?>[]" id="txtPcsPerBundle<?= $i; ?>" value="<?= $pcs_per_bundle; ?>" style="width:87px;" placeholder="Write"  onkeyup="assign_bundle_qty (<?= $i; ?>);"  /></th>
									                <th width="80" style="color:#2A3FFF">No. of Bundle </th>
									                <th width="100"><input class="text_boxes_numeric"  type="text" name="txtNoOfBundle<?= $i; ?>[]" id="txtNoOfBundle<?= $i; ?>" value="<?= $no_of_bundle; ?>" onBlur="create_bundle_list (<?= $i; ?>);" style="width:87px;" placeholder="Write" />
									                	<input type="hidden" name="hidPrevNoOfBdl<?= $i; ?>[]" id="hidPrevNoOfBdl<?= $i; ?>" value="<?= $no_of_bundle; ?>" ></th>
									                	<input type="hidden" name="txtBundleMstId<?= $i; ?>[]" id="txtBundleMstId<?= $i; ?>" value="<?= $bundl_mst_id; ?>" /></th>
									                <th width="80" style="color:#2A3FFF">Bundle Qty.</th>
									                <th width="100"><input class="text_boxes_numeric"  type="text" name="txtBundleQty<?= $i; ?>[]" id="txtBundleQty<?= $i; ?>" value="<?= $bundle_qty; ?>" style="width:87px;" placeholder="Write" /></th>
									                <th width="60" >Cut No.<input type="checkbox" name="is_copy" id="is_copy" onClick="fnc_copy_cut_no(<?= $i; ?>)" /></th>
				                					<th width="100"><input class="text_boxes_numeric"  type="text" name="txtCutNo<?= $i; ?>[]" id="txtCutNo<?= $i; ?>" value="<?= $cut_no; ?>" style="width:87px;" placeholder="Write"/></th>
									                <th width="60" >Remarks</th>
									                <th width="140"><input class="text_boxes"  type="text" name="txtRemarks<?= $i; ?>[]" id="txtRemarks<?= $i; ?>" value="<?= $remarks; ?>" style="width:127px;" placeholder="Write"/></th>
									                <th><input type="button" id="btnSave_<?= $i; ?>" value="Save & Create" class="formbutton" onClick="fnc_save_create(<?= $i; ?>);" ></th>
									                <th>&nbsp;</th>
									            </tr>
									        </thead>
									        <tbody id="tblBundleTbody<?= $i; ?>" >
									        	<?
									        	$j=1; $row=chop($row,'#');
									        	$dtls_row_data=explode('#', $row);
									        	foreach ($dtls_row_data as  $value) {
									        		$dtls_data=explode('_', $value);
									        		?>
										        	<tr id="trBundle<?= $i.'_'.$j; ?>">
										        		<td colspan="3">&nbsp;</td>
										                <td><input class="text_boxes_numeric"  type="hidden" name="txtPcsNoOfBundle<?= $i; ?>[]" id="txtPcsNoOfBundle<?= $i.'_'.$j; ?>" value="<?= $dtls_data[1]; ?>"  placeholder="No. of Bundle" style="width:87px;" readonly />
										                	<input class="text_boxes_numeric"  type="hidden" name="txtHidBundleNo<?= $i; ?>[]" id="txtHidBundleNo<?= $i.'_'.$j; ?>" value="<?= $dtls_data[5]; ?>"  placeholder="Bundle No." style="width:87px;" readonly/>
				                							<input class="text_boxes"  type="text" name="txtBundleNo<?= $i; ?>[]" id="txtBundleNo<?= $i.'_'.$j; ?>" value="<?= $dtls_data[6]; ?>"  placeholder="Bundle No." style="width:87px;" readonly/></td>
										                <td>&nbsp;</td>
										                <td><input class="text_boxes_numeric"  type="text" name="txtPcsBundleQty<?= $i; ?>[]" id="txtPcsBundleQty<?= $i.'_'.$j; ?>" value="<?= $dtls_data[2]; ?>"  placeholder="Bundle Qty." style="width:87px;"/></td>
										                <td>&nbsp;</td>
										                <td><input class="text_boxes_numeric"  type="text" name="txtDtlsCutNo<?= $i ?>[]" id="txtDtlsCutNo<?= $i.'_'.$j; ?>" value="<?= $dtls_data[7]; ?>" placeholder="Cut No." style="width:87px;" readonly/></td>
										                <td >&nbsp;</td>
										                <td ><input class="text_boxes"  type="text" name="txtBarcode<?= $i; ?>[]" id="txtBarcode<?= $i.'_'.$j; ?>" value="<?= $dtls_data[3]; ?>"   placeholder="Display" readonly style="width:127px;"/></td>
										                <td>&nbsp;<input type="hidden" name="txtBarcodeId<?= $i; ?>[]" id="txtBarcodeId<?= $i.'_'.$j; ?>" value="<?= $dtls_data[4]; ?>" readonly /></td>
										                <td><input id="chkBundle<?= $i.'_'.$j; ?>" type="checkbox" name="chkBundle" ><input type="hidden" name="txtBdlDtlsUpId<?= $i; ?>[]" id="txtBdlDtlsUpId<?= $i.'_'.$j; ?>" value="<?= $dtls_data[0]; ?>" readonly /></td>
										        	</tr>
									        		<?
									        		$j++;
									        	}
									        	//echo $row;
									        	?>
									        </tbody>
									    </table>
									</form>
									</fieldset>
									</div>
								<?
								}
							$i++;
							}
						}
					}
				}
			}
			?>
			<h3 align="left" class="accordion_h" onClick="show_hide_content('<?= $i ?>', '')"> +Bundle Entry- <?= $i ?></h3>
			<div id="contentBundle_<?= $i ?>" style="display:none;">
			<fieldset>
			    <form id="fabriccost_<?= $i ?>" autocomplete="off">
			    <table width="1045" cellspacing="0" class="rpt_table" border="0" id="tblBundleEntry<?= $i ?>" rules="all">
			        <thead> 
			            <tr>
			                <th width="80" style="color:#2A3FFF">Pcs Per Bundle </th>
			                <th width="100"><input class="text_boxes_numeric"  type="text" name="txtPcsPerBundle<?= $i ?>[]" id="txtPcsPerBundle<?= $i ?>" style="width:87px;" placeholder="Write"  onkeyup="assign_bundle_qty (<?= $i ?>);"  /></th>
			                <th width="80" style="color:#2A3FFF">No. of Bundle </th>
			                <th width="100"><input class="text_boxes_numeric"  type="text" name="txtNoOfBundle<?= $i ?>[]" id="txtNoOfBundle<?= $i ?>" onBlur="create_bundle_list (<?= $i ?>);" style="width:87px;" placeholder="Write" />
			                	<input type="hidden" name="hidPrevNoOfBdl<?= $i ?>[]" id="hidPrevNoOfBdl<?= $i ?>" value="0"></th>
			                	<input type="hidden" name="txtBundleMstId<?= $i ?>[]" id="txtBundleMstId<?= $i ?>" value="<?= $dataArray[0][csf("bundle_mst_id")] ; ?>" /></th>
			                <th width="80" style="color:#2A3FFF">Bundle Qty.</th>
			                <th width="100"><input class="text_boxes_numeric"  type="text" name="txtBundleQty<?= $i ?>[]" id="txtBundleQty<?= $i ?>" style="width:87px;" placeholder="Write" /></th>
			                <th width="60" >Cut No.<input type="checkbox" name="is_copy" id="is_copy" onClick="fnc_copy_cut_no(<?= $i ?>)" /></th>
			                <th width="100"><input class="text_boxes_numeric"  type="text" name="txtCutNo<?= $i ?>[]" id="txtCutNo<?= $i ?>" style="width:87px;" placeholder="Write"/></th>
			                <th width="60" >Remarks</th>
			                <th width="140"><input class="text_boxes"  type="text" name="txtRemarks<?= $i ?>[]" id="txtRemarks<?= $i ?>" style="width:127px;" placeholder="Write"/></th>
			                <th><input type="button" id="btnSave_<?= $i ?>" value="Save & Create" class="formbutton" onClick="fnc_save_create(<?= $i ?>);" ></th>
			            </tr>
			        </thead>
			        <tbody id="tblBundleTbody<?= $i ?>" >
			        	<tr id="trBundle<?= $i ?>_1">
			        		<td colspan="3">&nbsp;</td>
			                <td><input class="text_boxes_numeric"  type="hidden" name="txtPcsNoOfBundle<?= $i ?>[]" id="txtPcsNoOfBundle<?= $i ?>_1" value="1"  placeholder="No. of Bundle" style="width:87px;" readonly/>
			                	<input class="text_boxes_numeric"  type="hidden" name="txtHidBundleNo<?= $i ?>[]" id="txtHidBundleNo<?= $i ?>_1" value="<?= $last_bundle_no; ?>"  placeholder="Bundle No." style="width:87px;" readonly/>
			                	<input class="text_boxes"  type="text" name="txtBundleNo<?= $i ?>[]" id="txtBundleNo<?= $i ?>_1" value="<?= $manual_bundle_no; ?>"  placeholder="Bundle No." style="width:87px;" readonly/>
			                </td>
			                <td>&nbsp;</td>
			                <td><input class="text_boxes_numeric"  type="text" name="txtPcsBundleQty<?= $i ?>[]" id="txtPcsBundleQty<?= $i ?>_1"  placeholder="Bundle Qty." style="width:87px;" readonly/></td>
			                <td>&nbsp;</td>
			                <td><input class="text_boxes_numeric"  type="text" name="txtDtlsCutNo<?= $i ?>[]" id="txtDtlsCutNo<?= $i ?>_1" placeholder="Cut No." style="width:87px;" readonly/></td>
			                <td >&nbsp;</td>
			                <td ><input class="text_boxes"  type="text" name="txtBarcode<?= $i ?>[]" id="txtBarcode<?= $i ?>_1" placeholder="Display" readonly style="width:127px;" readonly="readonly" /></td>
			                <td>&nbsp;<input type="hidden" name="txtBarcodeId<?= $i ?>[]" id="txtBarcodeId<?= $i ?>_1" value="" readonly /></td>
			                <td><input id="chkBundle<?= $i ?>_1" type="checkbox" name="chkBundle" ><input type="hidden" name="txtBdlDtlsUpId<?= $i ?>[]" id="txtBdlDtlsUpId<?= $i ?>_1" value="" /></td>
			        	</tr>
			        </tbody>
			    </table>
			</form>
			</fieldset>
			</div>
			<?
    	}else{
    		?>
	    	<h3 align="left" class="accordion_h" onClick="show_hide_content('1', '')"> +Bundle Entry-1</h3>
			<div id="contentBundle_1" style="display:none;">
			<fieldset>
			    <form id="fabriccost_1" autocomplete="off">
			    <table width="1045" cellspacing="0" class="rpt_table" border="0" id="tblBundleEntry1" rules="all">
			        <thead> 
			            <tr>
			                <th width="80" style="color:#2A3FFF">Pcs Per Bundle </th>
			                <th width="100"><input class="text_boxes_numeric"  type="text" name="txtPcsPerBundle1[]" id="txtPcsPerBundle1" style="width:87px;" placeholder="Write"  onkeyup="assign_bundle_qty (1);"  /></th>
			                <th width="80" style="color:#2A3FFF">No. of Bundle </th>
			                <th width="100"><input class="text_boxes_numeric"  type="text" name="txtNoOfBundle1[]" id="txtNoOfBundle1" onBlur="create_bundle_list (1);" style="width:87px;" placeholder="Write" />
			                	<input type="hidden" name="hidPrevNoOfBdl1[]" id="hidPrevNoOfBdl1" value="0"></th>
			                	<input type="hidden" name="txtBundleMstId1[]" id="txtBundleMstId1" value="<?= $dataArray[0][csf("bundle_mst_id")] ; ?>" /></th>
			                <th width="80" style="color:#2A3FFF">Bundle Qty.</th>
			                <th width="100"><input class="text_boxes_numeric"  type="text" name="txtBundleQty1[]" id="txtBundleQty1" style="width:87px;" placeholder="Write" /></th>
			                <th width="60" >Cut No.<input type="checkbox" name="is_copy" id="is_copy" onClick="fnc_copy_cut_no(1)" /></th>
			                <th width="100"><input class="text_boxes_numeric"  type="text" name="txtCutNo1[]" id="txtCutNo1" style="width:87px;" placeholder="Write"/></th>
			                <th width="60" >Remarks</th>
			                <th width="140"><input class="text_boxes"  type="text" name="txtRemarks1[]" id="txtRemarks1" style="width:127px;" placeholder="Write"/></th>
			                <th><input type="button" id="btnSave_1" value="Save & Create" class="formbutton" onClick="fnc_save_create(1);" ></th>
			                <td>&nbsp;</td>
			            </tr>
			        </thead>
			        <tbody id="tblBundleTbody1" >
			        	<tr id="trBundle1_1">
			        		<td colspan="3">&nbsp;</td>
			                <td><input class="text_boxes_numeric"  type="hidden" name="txtPcsNoOfBundle1[]" id="txtPcsNoOfBundle1_1" value="1"  placeholder="No. of Bundle" style="width:87px;" readonly/>
			                	<input class="text_boxes_numeric"  type="hidden" name="txtHidBundleNo1[]" id="txtHidBundleNo1_1" value="<?= $last_bundle_no; ?>"  placeholder="Bundle No." style="width:87px;" readonly/>
			                	<input class="text_boxes"  type="text" name="txtBundleNo1[]" id="txtBundleNo1_1" value="<?= $manual_bundle_no; ?>"  placeholder="Bundle No." style="width:87px;" readonly/></td>
			                <td>&nbsp;</td>
			                <td><input class="text_boxes_numeric"  type="text" name="txtPcsBundleQty1[]" id="txtPcsBundleQty1_1"  placeholder="Bundle Qty." style="width:87px;" readonly/></td>
			                <td>&nbsp;</td>
			                <td><input class="text_boxes_numeric"  type="text" name="txtDtlsCutNo1[]" id="txtDtlsCutNo1_1"  placeholder="Cut No." style="width:87px;" readonly/></td>
			                <td >&nbsp;</td>
			                <td ><input class="text_boxes"  type="text" name="txtBarcode1[]" id="txtBarcode1_1" placeholder="Barcode No." readonly style="width:127px;" readonly="readonly"/></td>
			                <td>&nbsp;<input type="hidden" name="txtBarcodeId1[]" id="txtBarcodeId1_1" value="" /></td>
			                <td><input id="chkBundle1_1" type="checkbox" name="chkBundle" ><input type="hidden" name="txtBdlDtlsUpId1[]" id="txtBdlDtlsUpId1_1" value="" /></td>
			        	</tr>
			        </tbody>
			    </table>
			</form>
			</fieldset>
			</div>
		<? } ?>
		<div id="append_div1"></div>
		<table width="1050" cellspacing="0" class="rpt_table" border="0" id="tblTotal" rules="all">
			<tfoot>
				<tr>
	        		<th width="80">&nbsp;</th> 
	        		<th width="100"><input class="text_boxes_numeric"  type="text" name="txtTotalPcsPerBundle[]" id="txtTotalPcsPerBundle" value="<?= $pcsPer_bundle ; ?>" style="width:87px;" />
	        			<input type="hidden" name="txtTotalType[]" id="txtTotalType" value="<?= $totalType; ?>" readonly/></th>
	        			<input type="hidden" name="txtPoId[]" id="txtPoId" value="<?= $dataArray[0][csf("po_id")] ; ?>" readonly /></th>
	        			<input type="hidden" name="txtOrdBreakdownId[]" id="txtOrdBreakdownId" value="<?= $dataArray[0][csf("breakdown_id")] ; ?>" readonly /></th>
	        			<input type="hidden" name="txtEmbJob[]" id="txtEmbJob" value="<?= $dataArray[0][csf("embellishment_job")] ; ?>" readonly /></th>
	        			
	        			<input type="hidden" name="itemRcvDtlsId[]" id="itemRcvDtlsId" value="<?= $itemRcvDtlsId; ?>" readonly /></th>
	        			<input type="hidden" name="jobId[]" id="jobId" value="<?= $dataArray[0][csf("breakdown_id")]; ?>" readonly /></th>
	        			<input type="hidden" name="rcvId[]" id="rcvId" value="<?= $update_id; ?>" readonly /></th>
	        			<input type="hidden" name="sizeId[]" id="sizeId" value="<?= $dataArray[0][csf("size_id")]; ?>" readonly /></th>
	        			<input type="hidden" name="colorId[]" id="colorId" value="<?= $dataArray[0][csf("color_id")]; ?>" readonly /></th>
	        			<input type="hidden" name="txtCompanyId[]" id="txtCompanyId" value="<?= $dataArray[0][csf("company_id")] ; ?>" readonly /></th>
	        		<th width="80">&nbsp;</th>
	                <th width="100"><input class="text_boxes_numeric"  type="text" name="txtTotalPcsNoOfBundle[]" id="txtTotalPcsNoOfBundle" value="<?= $noOf_bundle ; ?>" style="width:87px;" readonly /></th>
	                <th width="80">&nbsp;</th>
	                <th width="100"><input class="text_boxes_numeric"  type="text" name="txtTotalPcsBundleQty[]" id="txtTotalPcsBundleQty" value="<?= $bundleQty ; ?>" style="width:87px;" readonly /></th>
	                <th>&nbsp;</th>
	        	</tr>
        	</tfoot>
		</table>
	<!-- </tr> -->
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
    <?
	exit();
}


if($action=="create_new_bundle_list_view")
{
	$data=explode('**', $data);
	$row=$data[0]+1;
	$emb_job=$data[1];
	$table='';
	$job_no=explode('-', $emb_job);
	$sql_bundle_no="SELECT max(c.bundle_no_prefix_num) as bundle_no_prefix_num, a.job_no_prefix_num from subcon_ord_mst a, sub_material_mst b, prnting_bundle_dtls c where a.embellishment_job=b.embl_job_no and b.id=c.item_rcv_id and a.embellishment_job='$emb_job' and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0  and c.status_active =1 and c.is_deleted =0 group by a.job_no_prefix_num";
	$bdlNoArray =sql_select($sql_bundle_no);
	$last_bundle_no=$bdlNoArray[0][csf("bundle_no_prefix_num")]; 
	if($last_bundle_no=='') $last_bundle_no=1; else $last_bundle_no=$last_bundle_no+1;
	$manual_bundle_no=$bdlNoArray[0][csf("job_no_prefix_num")].'-'.$job_no[2].'-'.$last_bundle_no;

	$table=$table.'<h3 align="left" class="accordion_h" onClick="show_hide_content('.$row.',\'\')"> +Bundle Entry-'.$row.'</h3>';
	$table=$table.'<div id="contentBundle_'.$row.'" style="display:none;"><fieldset><form id="fabriccost_'.$row.'" autocomplete="off"><table width="945" cellspacing="0" class="rpt_table" border="0" id="tblBundleEntry'.$row.'" rules="all">';
	$table=$table.'<thead><tr><th width="80" style="color:#2A3FFF">Pcs Per Bundle </th><th width="100"><input class="text_boxes_numeric"  type="text" name="txtPcsPerBundle'.$row.'[]" id="txtPcsPerBundle'.$row.'" style="width:87px;" placeholder="Write"  onkeyup="assign_bundle_qty ('.$row.');"  /></th><th width="80" style="color:#2A3FFF">No. of Bundle </th><th width="100"><input class="text_boxes_numeric"  type="text" name="txtNoOfBundle'.$row.'[]" id="txtNoOfBundle'.$row.'" onblur="create_bundle_list ('.$row.');" style="width:87px;" placeholder="Write" /><input type="hidden" name="hidPrevNoOfBdl'.$row.'[]" id="hidPrevNoOfBdl'.$row.'"></th><th width="80" style="color:#2A3FFF">Bundle Qty.</th><th width="100"><input class="text_boxes_numeric"  type="text" name="txtBundleQty'.$row.'[]" id="txtBundleQty'.$row.'" style="width:87px;" placeholder="Write" /><th width="60" style="color:#2A3FFF">Cut No.<input type="checkbox" name="is_copy" id="is_copy" onClick="fnc_copy_cut_no('.$row.')" /></th><th width="100"><input class="text_boxes_numeric"  type="text" name="txtCutNo'.$row.'[]" id="txtCutNo'.$row.'" style="width:87px;" placeholder="Write" /></th></th><th width="60" >Remarks</th><th width="140"><input class="text_boxes"  type="text" name="txtRemarks'.$row.'[]" id="txtRemarks'.$row.'" style="width:127px;" placeholder="Write"/></th><th width="140">&nbsp;</th><th><input type="button" id="btnSave_'.$row.'" value="Save & Create" class="formbutton" onClick="fnc_save_create('.$row.');" ></th> </tr></thead>';
	$table=$table.'<tbody id="tblBundleTbody'.$row.'"><tr id="trBundle'.$row.'_1"><td colspan="3">&nbsp;</td><td><input class="text_boxes_numeric"  type="hidden" name="txtPcsNoOfBundle'.$row.'[]" id="txtPcsNoOfBundle'.$row.'_1" value="1"  placeholder="No. of Bundle" readonly="readonly" style="width:87px;"/><input class="text_boxes_numeric"  type="hidden" name="txtHidBundleNo'.$row.'[]" id="txtHidBundleNo'.$row.'_1" value="'.$last_bundle_no.'" readonly="readonly" style="width:87px;"/><input class="text_boxes"  type="text" name="txtBundleNo'.$row.'[]" id="txtBundleNo'.$row.'_1" value="'.$manual_bundle_no.'" readonly="readonly" style="width:87px;"/></td><td>&nbsp;</td><td><input class="text_boxes_numeric"  type="text" name="txtPcsBundleQty'.$row.'[]" id="txtPcsBundleQty'.$row.'_1"  placeholder="Bundle Qty." readonly="readonly" style="width:87px;"/></td><td>&nbsp;</td><td ><input class="text_boxes_numeric"  type="text" name="txtDtlsCutNo'.$row.'[]" id="txtDtlsCutNo'.$row.'_1" placeholder="Cut No." readonly="readonly" style="width:87px;"/></td><td>&nbsp;</td><td ><input class="text_boxes"  type="text" name="txtBarcode'.$row.'[]" id="txtBarcode'.$row.'_1" placeholder="Barcode No." readonly="readonly" style="width:127px;"/></td><td ><input type="hidden" name="txtBarcodeId'.$row.'[]" id="txtBarcodeId'.$row.'_1" value="" /></td><td><input id="chkBundle'.$row.'_1" type="checkbox" name="chkBundle" ></td><input type="hidden" name="txtBdlDtlsUpId'.$row.'[]" id="txtBdlDtlsUpId'.$row.'_1" value="" /></td></tr></tbody>';
	$table=$table.'</table></form></fieldset></div>'; 
	 
	echo substr($table,0,-1);
	//$table=$table.'<tr id="trMst_'.$row.'">'; 
	//$table=$table.'</tr>';

}

if ($action=="save_update_delete_bundle")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));
    //echo "10**".$txtBundleMstId; die;
    if ($operation==0)  // Insert Here
    {
        $con = connect();
        if($db_type==0)
        {
            mysql_query("BEGIN");
        }
       
        $dtlsId=return_next_id( "id", "prnting_bundle_dtls", 1 );
        $barCodeId=return_next_id( "id", "prnting_barcode", 1 );
        $year_id=date('Y',time());
		if (strlen($year_id)==4) $year_id=substr($year_id,2,2);
		$barcode_suffix_no=return_field_value("max(barcode_prifix) as suffix_no","prnting_barcode","barcode_year=$year_id","suffix_no");
		$job_no_mst=return_field_value( "job_no_mst", "subcon_ord_dtls"," id=$txtPoId and status_active=1 and is_deleted=0");


        //$field_array="id,qc_no,fabric_cost,accessories_cost,avl_min,cm_cost,frieght_cost,lab_test_cost,mis_offer_qty,other_cost,com_cost,fob,fob_pcs,margin,margin_percent,Yarn_Yarn_Count_1,Yarn_Yarn_Type_1,Yarn_Rate_1,Yarn_Yarn_Count_2,Yarn_Yarn_Type_2,Yarn_Rate_2,Yarn_Yarn_Count_3,Yarn_Yarn_Type_3,Yarn_Rate_3,total_yarn_cost,yarn_dyeing_cost,knit_Yarn_Count_1,knit_Fabric_Type_1,knit_Rate_1,knit_Yarn_Count_2,knit_Fabric_Type_2,knit_Rate_2,knitting_cost,df_Color_Type_1,df_Color_1,df_Rate_1,df_Color_Type_2,df_Color_2,df_Rate_2,df_Color_Type_3,df_Color_3,df_Rate_3,df_cost,aop_cost,total_cost,inserted_by,insert_date";
        
       
        if($txtBundleMstId!='' && $txtBundleMstId!=0 ){
        	$id=$txtBundleMstId; $is_update=1;
        	$field_array_up="pcs_per_bundle*no_of_bundle*bundle_qty*cut_no*remarks*updated_by*update_date*status_active*is_deleted";
        	$data_array_up="'".$txtPcsPerBundle."'*'".$txtNoOfBundle."'*'".$txtBundleQty."'*'".$txtCutNo."'*'".$txtRemarks."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";
        	$field_array_dtls_up=" pcs_per_bundle*bundle_qty*cut_no*updated_by*update_date*status_active*is_deleted";
        }
        else{
        	$is_update=0;
        	if($db_type==0)
			{
				//bundle_num_prefix ,bundle_num_prefix_no , bundle_number 
				$new_bundle_no=explode("*",return_mrr_number( str_replace("'","",$txtCompanyId),'', 'PBE' , date("Y",time()), 5, "SELECT id,bundle_num_prefix,bundle_num_prefix_no from prnting_bundle_mst where company_id=$txtCompanyId and entry_form=205 and status_active =1 and is_deleted=0 and YEAR(insert_date)=".date('Y',time())." order by id desc ", "bundle_num_prefix", "bundle_num_prefix_no" ));
			}
			else if($db_type==2)
			{
				$new_bundle_no=explode("*",return_mrr_number( str_replace("'","",$txtCompanyId),'', 'PBE' , date("Y",time()), 5, "SELECT id,bundle_num_prefix,bundle_num_prefix_no from prnting_bundle_mst where company_id=$txtCompanyId and entry_form=205 and status_active =1 and is_deleted=0 and TO_CHAR(insert_date,'YYYY')=".date('Y',time())." order by id desc ", "bundle_num_prefix", "bundle_num_prefix_no" ));
			}

        	$id=return_next_id( "id", "prnting_bundle_mst", 1 );
			$field_array="id, bundle_num_prefix, bundle_num_prefix_no, bundle_number, company_id, po_id, order_breakdown_id, item_rcv_dtls_id, pcs_per_bundle, no_of_bundle, bundle_qty, cut_no, remarks, entry_form, inserted_by, insert_date, status_active, is_deleted";
			$data_array="(".$id.",'".$new_bundle_no[1]."','".$new_bundle_no[2]."','".$new_bundle_no[0]."','".$txtCompanyId."','".$txtPoId."','".$txtOrdBreakdownId."','".$itemRcvDtlsId."','".$txtPcsPerBundle."','".$txtNoOfBundle."','".$txtBundleQty."','".$txtCutNo."','".$txtRemarks."',205,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
        }
        $sql_bundle_no="SELECT c.bundle_no_prefix_num,c.bundle_no from subcon_ord_mst a, sub_material_mst b, prnting_bundle_dtls c where a.embellishment_job=b.embl_job_no and b.id=c.item_rcv_id and a.embellishment_job='$embJob' and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0  and c.status_active =1 and c.is_deleted =0 and c.bundle_no is not null";
		$bdlNoArray =sql_select($sql_bundle_no); $bundleChkArr=array();
		foreach ($bdlNoArray as $row) 
		{
			$bundleChkArr[]=$row[csf('bundle_no')];
		}
		//echo "10**<pre>"; print_r($bundleChkArr); die;

        $field_array_dtls="id,mst_id,item_rcv_id,pcs_per_bundle,bundle_qty,cut_no,barcode_no,barcode_id,bundle_no,bundle_no_prefix_num,inserted_by,insert_date,status_active,is_deleted";
		$field_array_barcode=" id, rcv_id, rcv_dtls_id, order_id, bundle_id, bundle_dtls_id, size_id, barcode_no, barcode_year, barcode_prifix, inserted_by, insert_date, status_active, is_deleted";

        $add_commaa=0; $add_comma=0; 
        $data_array_dtls=''; $data_array_barcode='';
        $barcode_chk_array = array(); $bndl_dup_chk_arr = array();
        for($i=1;$i<=str_replace("'", '', $txtNoOfBundle);$i++) 
        {
            $txtPcsNoOfBundle       ="txtPcsNoOfBundle_".$i;
            $txtPcsBundleQty       	="txtPcsBundleQty_".$i; 
            $txtBarcode    			="txtBarcode_".$i;
            $txtBarcodeId    		="txtBarcodeId_".$i;
            $txtBdlDtlsUpId    		="txtBdlDtlsUpId_".$i;
            $txtHidBundleNo    		="txtHidBundleNo_".$i;
            $txtBundleNo    		="txtBundleNo_".$i;
            $txtDtlsCutNo    		="txtDtlsCutNo_".$i;
            //echo "6**".$$txtBdlDtlsUpId;
            if(str_replace("'", '', $$txtBdlDtlsUpId)==''){

            	if (in_array(str_replace("'", '', $$txtBundleNo), $bundleChkArr))
				{
					echo "121**Duplicate Bundle No. Found"; die;
				}
				else
				{
					if(!in_array(str_replace("'", '', $$txtBundleNo), $bndl_dup_chk_arr, true))
					{
		        		array_push( $bndl_dup_chk_arr, str_replace("'", '', $$txtBundleNo));
		    		}
		    		else
		    		{
		    			echo "121**Duplicate Bundle No. Found"; die;
		    		}

					//echo "10**"; die;
					$barcode_suffix_no=$barcode_suffix_no+1;
					$barcode_no=$year_id."205".str_pad($barcode_suffix_no,9,"0",STR_PAD_LEFT);
					$barcode_chk_array[$barcode_no] = $barcode_no;

		            if ($add_commaa!=0) $data_array_dtls .=","; $add_commaa=0;
		            $data_array_dtls .="(".$dtlsId.",".$id.",'".$rcvId."',".$$txtPcsNoOfBundle.",".$$txtPcsBundleQty.",".$$txtDtlsCutNo.",'".$barcode_no."','".$barCodeId."',".$$txtBundleNo.",".$$txtHidBundleNo.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."',1,0)";
		           
		            if ($add_comma!=0) $data_array_barcode .=","; $add_comma=0;
		            $data_array_barcode .="(".$barCodeId.",'".$rcvId."','".$itemRcvDtlsId."','".$txtPoId."',".$id.",".$dtlsId.",'".$sizeId."','".$barcode_no."',".$year_id.",".$barcode_suffix_no.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."',1,0)";
		            $barCodeId++; $add_comma++;
		            $dtlsId++; $add_commaa++;
				}
            	
            }else{
            	$data_array_dtls_up[str_replace("'", '', $$txtBdlDtlsUpId)]=explode("*",("".$$txtPcsNoOfBundle."*".$$txtPcsBundleQty."*".$$txtDtlsCutNo."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0"));
				$hdn_dtls_id_arr[]=str_replace("'",'',$$txtBdlDtlsUpId);
            }
        }

        $barcodes = "'". implode("','", $barcode_chk_array)."'";
		$sql = "SELECT barcode_no from prnting_barcode where status_active=1 and is_deleted=0 and barcode_year=$year_id and barcode_no in($barcodes)";
		// echo "786**$sql";die();
		$res = sql_select($sql);
		if(count($res)>0)
		{
			echo "786**Duplicate Barcode found. Please try again later.";
			disconnect($con);die();
		}
		$field_array_status="updated_by*update_date*status_active*is_deleted";
		$data_array_status=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";

        //echo "5**insert into prnting_bundle_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
        

        //echo "5**insert into prnting_bundle_mst (".$field_array.") values ".$data_array;die;
        //echo "5**insert into prnting_barcode (".$field_array_barcode.") values ".$data_array_dtls;die;
        //echo "5**insert into prnting_barcode (".$field_array_barcode.") values ".$data_array_barcode;die;
        $flag=1;
        if($txtBundleMstId!='' && $txtBundleMstId!=0 ){
        	$rID=sql_multirow_update("prnting_bundle_dtls",$field_array_status,$data_array_status,"mst_id",$txtBundleMstId,0);
        	if($rID) $flag=1 ; else $flag=0;
        }
        
        if($data_array!="" && $flag==1){
            $rID1=sql_insert("prnting_bundle_mst",$field_array,$data_array,0);
            if($rID1) $flag=1 ; else $flag=0;
        }
        else if($data_array_up!="" && $flag==1){
        	$rID1=sql_update("prnting_bundle_mst",$field_array_up,$data_array_up,"id",$id,0);
        	if($rID1) $flag=1 ; else $flag=0; 
        }
        if($data_array_dtls!="" && $flag==1){
            $rID2=sql_insert("prnting_bundle_dtls",$field_array_dtls,$data_array_dtls,0);
            if($rID2) $flag=1 ; else $flag=0;
        }
        if($data_array_barcode!="" && $flag==1){
            $rID3=sql_insert("prnting_barcode",$field_array_barcode,$data_array_barcode,0);
            if($rID3) $flag=1 ; else $flag=0;
        }

        if($data_array_dtls_up!="" && $flag==1){
        	//echo "10**".bulk_update_sql_statement( "prnting_bundle_dtls", "id",$field_array_dtls_up,$data_array_dtls_up,$hdn_dtls_id_arr);
           	$rID4=execute_query(bulk_update_sql_statement( "prnting_bundle_dtls", "id",$field_array_dtls_up,$data_array_dtls_up,$hdn_dtls_id_arr),1);
            if($rID4) $flag=1 ; else $flag=0;
        }
        //echo "10**".$rID ."&&".$rID1 ."&&".  $rID2."&&".  $rID3."&&".  $rID4. "&&".  $flag;die;

        if($db_type==0)
        {
            if($flag) 
            {
                mysql_query("COMMIT");
                echo "0**".str_replace("'", '', $id).'**'.str_replace("'", '', $type).'**'.$is_update.'**'.$job_no_mst;
            }
            else
            {
                mysql_query("ROLLBACK");
                echo "6**".str_replace("'", '', $id).'**'.str_replace("'", '', $type).'**'.$is_update.'**'.$job_no_mst;
            }
        }
        else if($db_type==2 || $db_type==1 )
        {
            if($flag)
            {
                oci_commit($con);
                echo "0**".str_replace("'", '', $id).'**'.str_replace("'", '', $type).'**'.$is_update.'**'.$job_no_mst;
            }
            else
            {
                oci_rollback($con);
                echo "6**".str_replace("'", '', $id).'**'.str_replace("'", '', $type).'**'.$is_update.'**'.$job_no_mst;
            }
        }
    }
}

function sql_updates($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues,$commit,$return_query='')
{

	$strQuery = "UPDATE ".$strTable." SET ";
	$arrUpdateFields=explode("*",$arrUpdateFields);
	$arrUpdateValues=explode("*",$arrUpdateValues);

	if(count($arrUpdateFields)!=count($arrUpdateValues)){
		return "0";
	}

	if(is_array($arrUpdateFields))
	{
		$arrayUpdate = array_combine($arrUpdateFields,$arrUpdateValues);
		$Arraysize = count($arrayUpdate);
		$i = 1;
		foreach($arrayUpdate as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value.", ":$key."=".$value;
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrUpdateFields."=".$arrUpdateValues;
	}
	$strQuery .=" WHERE ";

	$arrRefFields=explode("*",$arrRefFields);
	$arrRefValues=explode("*",$arrRefValues);
	if(is_array($arrRefFields))
	{
		$arrayRef = array_combine($arrRefFields,$arrRefValues);
		$Arraysize = count($arrayRef);
		$i = 1;
		foreach($arrayRef as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value." AND ":$key."=".$value."";
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrRefFields."=".$arrRefValues."";
	}
	if($return_query==1){return $strQuery ;}

	return $strQuery;die;
	global $con;
	if( strpos($strQuery, "WHERE")==false)  return "0";
	$stid =  oci_parse($con, $strQuery);
	$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
	
	if ($exestd){user_activities($exestd);}
	if ($exestd)
		return "1";
	else
		return "0";

	die;
	if ( $commit==1 )
	{
		if (!oci_error($stid))
		{
			oci_commit($con);
			return "1";
		}
		else
		{
			oci_rollback($con);
			return "10";
		}
	}
	else
		return 1;
	die;
}
function sql_multirow_updatess($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues, $commit)
{
	$strQuery = "UPDATE ".$strTable." SET ";
	$arrUpdateFields=explode("*",$arrUpdateFields);
	$arrUpdateValues=explode("*",$arrUpdateValues);


	if(is_array($arrUpdateFields))
	{
		$arrayUpdate = array_combine($arrUpdateFields,$arrUpdateValues);
		$Arraysize = count($arrayUpdate);
		$i = 1;
		foreach($arrayUpdate as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value.", ":$key."=".$value." WHERE ";
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrUpdateFields."=".$arrUpdateValues." WHERE ";
	}

	//$arrRefFields=explode("*",$arrRefFields);
	//$arrRefValues=explode("*",$arrRefValues);
	$strQuery .= $arrRefFields." in (".$arrRefValues.")";
	 echo "10**".$strQuery;die;
    global $con;
	$stid =  oci_parse($con, $strQuery);
	$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
	if ($exestd)
		return "1";
	else
		return "0";

	die;
	$_SESSION['last_query']=$_SESSION['last_query'].";;".$strQuery;
	if ($commit==1)
	{
		if (!oci_error($stid))
		{

		$pc_time= add_time(date("H:i:s",time()),360);
		$pc_date_time = date("d-M-Y h:i:s",strtotime(add_time(date("H:i:s",time()),360)));
	    $pc_date = date("d-M-Y",strtotime(add_time(date("H:i:s",time()),360)));

		$strQuery= "INSERT INTO activities_history ( session_id,user_id,ip_address,entry_time,entry_date,module_name,form_name,query_details,query_type) VALUES ('".$_SESSION['logic_erp']["history_id"]."','".$_SESSION['logic_erp']["user_id"]."','".$_SESSION['logic_erp']["pc_local_ip"]."','".$pc_time."','".$pc_date."','".$_SESSION["module_id"]."','".$_SESSION['menu_id']."','".encrypt($_SESSION['last_query'])."','1')";

		mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");

		$resultss=oci_parse($con, $strQuery);
		oci_execute($resultss);
		$_SESSION['last_query']="";
		oci_commit($con);
		return "0";
		}
		else
		{
			oci_rollback($con);
			return "10";
		}
	}
	else return 1;
	die;
}

if($action=="saved_bundle_list_view")
{
	$data=explode('**', $data);
	$type=$data[0];
	$table=''; $i=0;
	$sql= "select id, mst_id, item_rcv_id, pcs_per_bundle, bundle_qty, cut_no, barcode_no, bundle_no, bundle_no_prefix_num from prnting_bundle_dtls where mst_id=$data[1] and status_active=1 and is_deleted=0";
	$data_array=sql_select($sql);
	foreach($data_array as $val)
	{
		$i++;
		$table=$table.'<tr id="trBundle'.$type.'_'.$i.'"><td colspan="3">&nbsp;</td><td><input class="text_boxes_numeric"  type="hidden" name="txtPcsNoOfBundle'.$type.'[]" id="txtPcsNoOfBundle'.$type.'_'.$i.'" value="'.$val[csf('pcs_per_bundle')].'"  placeholder="No. of Bundle" readonly="readonly" style="width:87px;"/><input class="text_boxes_numeric"  type="hidden" name="txtHidBundleNo'.$type.'[]" id="txtHidBundleNo'.$type.'_'.$i.'" value="'.$val[csf('bundle_no_prefix_num')].'" readonly="readonly" style="width:87px;"/><input class="text_boxes"  type="text" name="txtBundleNo'.$type.'[]" id="txtBundleNo'.$type.'_'.$i.'" value="'.$val[csf('bundle_no')].'" placeholder="Bundle No." readonly="readonly" style="width:87px;"/></td><td>&nbsp;</td><td><input class="text_boxes_numeric"  type="text" name="txtPcsBundleQty'.$type.'[]" id="txtPcsBundleQty'.$type.'_'.$i.'"  value="'.$val[csf('bundle_qty')].'"  placeholder="Bundle Qty." readonly="readonly" style="width:87px;"/></td><td >&nbsp;</td><td ><input class="text_boxes_numeric"  type="text" name="txtDtlsCutNo'.$type.'[]" id="txtDtlsCutNo'.$type.'_'.$i.'" value="'.$val[csf('cut_no')].'"  placeholder="Cut No." readonly="readonly" style="width:87px;"/></td><td >&nbsp;</td><td ><input class="text_boxes"  type="text" name="txtBarcode'.$type.'[]" id="txtBarcode'.$type.'_'.$i.'"  value="'.$val[csf('barcode_no')].'"  placeholder="Display"  readonly="readonly" style="width:127px;"/></td><td ><input type="hidden" name="txtBarcodeId'.$type.'[]" id="txtBarcodeId'.$type.'_'.$i.'" value="" /></td><td ><input id="chkBundle'.$type.'_'.$i.'" type="checkbox" name="chkBundle" ></td><input type="hidden" name="txtBdlDtlsUpId'.$type.'[]" id="txtBdlDtlsUpId'.$type.'_'.$i.'" value="'.$val[csf('id')].'" /></td></tr>';
	}
	echo substr($table,0,-1);
	//$table=$table.'<tr id="trMst_'.$row.'">'; 
	//$table=$table.'</tr>';
	?>
	
	<?
}



if($action=="print_report_bundle_barcode_eight")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	//define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	//require('../../../ext_resource/pdf/code39.php');
	//require('file://///192.168.11.252/logic_erp_3rd_version/production/requires/html_table.php');
	$data_all=$data;
	$data=explode("***",$data);
	//echo $data[0];die;
	?>
      	<script>
		
		var selected_id = new Array;
		var selected_name = new Array;
		
    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) { 
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( strCon ) 
		{
			var splitSTR = strCon.split("_");
			var str = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			//$('#txt_individual_id' + str).val(splitSTR[1]);
			//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');
			
			toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( selectID, selected_id ) == -1 ) {
				selected_id.push( selectID );
				selected_name.push( selectDESC );					
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == selectID ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 ); 
			}
			var id = ''; var name = ''; var job = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ','; 
			}
			id 		= id.substr( 0, id.length - 1 );
			name 	= name.substr( 0, name.length - 1 ); 
			
			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name ); 
		
		} 
	</script>
    <?
		/*$sql_cut_name=sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2]");
		foreach($sql_cut_name as $cut_value)
		{
			$table_name=$cut_value[csf('table_no')];
			$cut_date=change_date_format($cut_value[csf('entry_date')]);
			$cut_prifix=$cut_value[csf('cut_num_prefix_no')];
			$company_id=$cut_value[csf('company_id')];
			$batch_no=$cut_value[csf('batch_id')];
			$comp_name=return_field_value("company_short_name","lib_company", "id=$company_id");
			$new_cut_no=$comp_name."-".$cut_prifix;
			$bundle_title="";
		}*/
	    $sql_bundle_copy="select id,bundle_use_for from ppl_bundle_title where company_id=$data[7]";
		//echo  create_list_view("tbl_list_search", "Bundle Use For", "240","240","180",0, $sql_bundle_copy, "js_set_value", "id,bundle_use_for", "", 1, "0", $arr, "bundle_use_for", "","setFilterGrid('tbl_list_search',-1)",'0',"",1);
		echo  create_list_view("tbl_list_search", "Bundle Use For", "240","240","180",0, $sql_bundle_copy, "js_set_value", "id,bundle_use_for", "", 1, "0", $arr, "bundle_use_for", "","setFilterGrid('tbl_list_search',-1)",'0',"",1);
	    echo "<input type='hidden' id='txt_selected_id' />";
		echo "<input type='hidden' id='txt_selected' />";
	exit();	
	
}

if($action=="print_barcode_one_urmi")
{	
	//echo $data; die;
	//420***12141***4205***8040***2***287***6052***3***30
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	require('../../../ext_resource/pdf/code39.php');
	//require('file://///192.168.11.252/logic_erp_3rd_version/production/requires/html_table.php');//order_cut_no
	// print_r($data);die();
	$data=explode("***",$data);
	$bundleUpDtlsIds=$data[0];
	$mst_id=$data[2];
	$detls_id=$data[3];
	$batch_id=return_field_value("batch_id","ppl_cut_lay_dtls", "id=$detls_id");
	$buyer_library=return_library_array( "select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$table_no_library=return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );
	$country_arr=return_library_array( "select id, short_name from lib_country",'id','short_name'); 
	$lib_season_arr=return_library_array( "select id, season_name from lib_buyer_season where status_active =1 and is_deleted=0",'id','season_name'); 
	$order_cut_no=return_field_value("order_cut_no","ppl_cut_lay_dtls","id=$detls_id and status_active=1 and is_deleted=0","order_cut_no");
	$pdf=new PDF_Code39('P','mm','a10');
	$pdf->AddPage();
	if($data[0]=='420'){
		$cond=" and c.id in ($detls_id) ";
	}
	else{
		$cond=" and b.id in ($bundleUpDtlsIds) ";
	}
	
	/*echo "SELECT a.id, a.company_id, a.po_id, a.order_breakdown_id, a.item_rcv_dtls_id, a.pcs_per_bundle, a.no_of_bundle, a.bundle_qty, a.remarks,a.bundle_num_prefix_no, b.id, b.mst_id, b.item_rcv_id, b.pcs_per_bundle, b.bundle_qty, b.barcode_no, b.barcode_id , c.job_dtls_id, c.job_break_id, c.buyer_po_id, d.embl_job_no,d.party_id 
	from prnting_bundle_mst a, prnting_bundle_dtls b, sub_material_dtls c, sub_material_mst d  where a.id=b.mst_id and a.item_rcv_dtls_id=c.id and b.item_rcv_id=c.mst_id and b.item_rcv_id=d.id and d.id=c.mst_id $cond and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0
	group by a.id, a.company_id, a.po_id, a.order_breakdown_id, a.item_rcv_dtls_id, a.pcs_per_bundle, a.no_of_bundle, a.bundle_qty, a.remarks,a.bundle_num_prefix_no, b.id, b.mst_id, b.item_rcv_id, b.pcs_per_bundle, b.bundle_qty, b.barcode_no, b.barcode_id , c.job_dtls_id, c.job_break_id, c.buyer_po_id, d.embl_job_no,d.party_id order by b.id"; die;*/
	$color_sizeID_arr=sql_select("SELECT a.id, a.company_id, a.po_id, a.order_breakdown_id, a.item_rcv_dtls_id, a.pcs_per_bundle, a.no_of_bundle, a.bundle_qty, a.remarks,b.bundle_no , b.id, b.mst_id, b.item_rcv_id, b.pcs_per_bundle, b.bundle_qty as dtls_bndl_qty, b.barcode_no, b.barcode_id , b.cut_no, c.job_dtls_id, c.job_break_id, c.buyer_po_id, d.embl_job_no,d.party_id 
	from prnting_bundle_mst a, prnting_bundle_dtls b, sub_material_dtls c, sub_material_mst d  where a.id=b.mst_id and a.item_rcv_dtls_id=c.id and b.item_rcv_id=c.mst_id and b.item_rcv_id=d.id and d.id=c.mst_id $cond and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0
	group by a.id, a.company_id, a.po_id, a.order_breakdown_id, a.item_rcv_dtls_id, a.pcs_per_bundle, a.no_of_bundle, a.bundle_qty, a.remarks,b.bundle_no , b.id, b.mst_id, b.item_rcv_id, b.pcs_per_bundle, b.bundle_qty, b.barcode_no, b.barcode_id, b.cut_no , c.job_dtls_id, c.job_break_id, c.buyer_po_id, d.embl_job_no,d.party_id order by b.id");


	$jobno=$color_sizeID_arr[0][csf('embl_job_no')];
	$company_id=$color_sizeID_arr[0][csf('company_id')];
	$sql_job="SELECT a.id, a.embellishment_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id as po_id, b.buyer_po_id, b.gmts_item_id, b.embl_type, b.body_part, b.main_process_id, b.order_uom, c.id as breakdown_id, c.description, c.color_id, c.size_id, c.qnty,b.buyer_po_no,b.buyer_buyer ,b.buyer_style_ref from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
	where a.entry_form=204 and a.embellishment_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id  and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and a.embellishment_job='$jobno' and c.qnty>0 order by c.id ASC";
	$job_arr=sql_select($sql_job);
	foreach($job_arr as $val)
   	{
   		$updtls_data_arr[$val[csf("po_id")]][$val[csf("breakdown_id")]]['buyer_buyer']=$val[csf("buyer_buyer")];
   		$updtls_data_arr[$val[csf("po_id")]][$val[csf("breakdown_id")]]['buyer_po_no']=$val[csf("buyer_po_no")];
		$updtls_data_arr[$val[csf("po_id")]][$val[csf("breakdown_id")]]['buyer_style_ref']=$val[csf("buyer_style_ref")];
		$updtls_data_arr[$val[csf("po_id")]][$val[csf("breakdown_id")]]['color_id']=$val[csf("color_id")];
		$updtls_data_arr[$val[csf("po_id")]][$val[csf("breakdown_id")]]['size_id']=$val[csf("size_id")];
		$updtls_data_arr[$val[csf("po_id")]][$val[csf("breakdown_id")]]['order_no']=$val[csf("order_no")];
   	}	
	//echo $data[7]; die;
	//echo "<pre>"; print_r($updtls_data_arr); die;
	//company_id=$company_id and
	//echo "select id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[8])"; die;
	$sql_bundle_copy=sql_select("select id,bundle_use_for from ppl_bundle_title where  id in ($data[8])");
	$i=2;
	if(count($sql_bundle_copy)!=0)
	{
		foreach($sql_bundle_copy as $inf)
		{
			if($br==1) { $pdf->AddPage(); $br=0; $i=2; $j=0; $k=0; }
			foreach($color_sizeID_arr as $val)
			{
				if($br==1) 
				{
					$pdf->AddPage(); $br=0; $i=2; $j=0; $k=0;
				}
				//if($seq_first==$inf[csf("id")]) $symb= "@@"; else $symb= "";
				//BNDL
				/*$style_name=$po_data_arr[$val[csf('order_id')]]["style_ref_no"];
				$buyer_name=$po_data_arr[$val[csf('order_id')]]["buyer_name"];
				$client_id=$po_data_arr[$val[csf('order_id')]]["client_id"];
				$po_number=$po_data_arr[$val[csf('order_id')]]["po_number"];
				$internal_ref=$po_data_arr[$val[csf('order_id')]]["grouping"];
				$batch_no=$roll_data_arr[$val[csf('roll_id')]];
				$bundle_no_prifix=$val[csf("bundle_num_prefix_no")];
				$buyer_name_str=""; if($client_id!=0) $buyer_name_str=$buyer_library[$buyer_name].'-'.$buyer_library[$client_id]; else $buyer_name_str=$buyer_library[$buyer_name];*/
				//$dbl_no="17910000000012";
				//if($val[csf('is_excess')]==1) $country="EXCESS"; else $country=$country_arr[$val[csf("country_id")]];
				//BNDL-2231, C. Buy-LPP, B.Sty-372188
				/*$pdf->Code40($i, $j-2, $symb." ".$buyer_name_str."  COUN# ".$country."  QTY# ".$val[csf("size_qty")], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;
				$pdf->Code40($i, $j+1.2, "STKR# ".$val[csf("number_start")]."-".$val[csf("number_end")]."  STY# ". $style_name, $ext = true, $cks = false, $w =0.15, $h = 1, $wide = true, true,7) ;
				$pdf->Code40($i, $j+4.4, $val[csf("bundle_no")]."  PO# ".$po_number, $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,7) ;
				$pdf->Code40($i, $j+7.6, "COLOR# ".$color_library[$data[5]], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;
				$pdf->Code40($i, $j+10.8, $inf[csf("bundle_use_for")]."  B# ".$batch_no."  S# ".$lib_season_arr[$matrix_season], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide=true,true,7);
				$pdf->Code40($i, $j+14, "CUT & ROLL# ".$order_cut_no." & ".$val[csf("roll_no")]."  SIZE# ".$size_arr[$val[csf("size_id")]]."(".$val[csf("pattern_no")].")", $ext = true, $cks = false, $w =0.15, $h = 1, $wide = true, true,7) ;
				$pdf->Code40($i+1.3, $j+17.2, $val[csf("barcode_no")], $ext = true, $cks = false, $w =0.15, $h = 1, $wide = true, true,7) ;
				//$pdf->Code40($i, $j+22, $val[csf("bundle_no")],$w =0.2, $h = 8);
				$pdf->Code39($i+1.3, $j+23, $val[csf("barcode_no")]);
				$k++;
				$i=2; $j=$j+23;
				$br++;*/

				$bundle_no=$val[csf("bundle_no")];
				$buyer_name=$val[csf("party_id")];

				$buyer_buyer=$updtls_data_arr[$val[csf("job_dtls_id")]][$val[csf("job_break_id")]]['buyer_buyer'];
				$buyer_po_no=$updtls_data_arr[$val[csf("job_dtls_id")]][$val[csf("job_break_id")]]['buyer_po_no'];
				$buyer_style_ref=$updtls_data_arr[$val[csf("job_dtls_id")]][$val[csf("job_break_id")]]['buyer_style_ref'];
				$color_id=$updtls_data_arr[$val[csf("job_dtls_id")]][$val[csf("job_break_id")]]['color_id'];
				$size_id=$updtls_data_arr[$val[csf("job_dtls_id")]][$val[csf("job_break_id")]]['size_id'];
				$order_no=$updtls_data_arr[$val[csf("job_dtls_id")]][$val[csf("job_break_id")]]['order_no'];
			
				$pdf->Code40($i, $j-2, "BNDL- ".$bundle_no.", Party- ".$buyer_library[$buyer_name], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;
				$pdf->Code40($i, $j+1.2, "Buy- ".$buyer_buyer.", Sty- ". $buyer_style_ref, $ext = true, $cks = false, $w =0.15, $h = 1, $wide = true, true,7) ;
				$pdf->Code40($i, $j+3.8, "PO- ".$order_no.", Color- ". $color_library[$color_id], $ext = true, $cks = false, $w =0.15, $h = 1, $wide = true, true,7) ;
				$pdf->Code40($i, $j+6, "Cut No- ".$val[csf("cut_no")].", Size- ". $size_arr[$size_id], $ext = true, $cks = false, $w =0.15, $h = 1, $wide = true, true,7) ;
				$pdf->Code40($i, $j+8.5, "BNDL Qty- ".$val[csf("dtls_bndl_qty")].',  '. $inf[csf("bundle_use_for")], $ext = true, $cks = false, $w = 0.2, $h = 1, $wide=true,true,7);
				//$pdf->Code40($i, $j+22, $val[csf("bundle_no")],$w =0.2, $h = 8);
				$pdf->Code39($i, $j+17, $val[csf("barcode_no")]);
				//$pdf->Code40($i, $j+1.2, $val[csf("barcode_no")], $ext = true, $cks = false, $w =0.15, $h = 1, $wide = true, true,7) ;
				$k++;
				$i=2; $j=$j+13;
				$br++;
			}
		}   
	}
	else
	{
		foreach($color_sizeID_arr as $val)
	   	{
			if($br==1) 
			{
				$pdf->AddPage(); $br=0; $i=2; $j=0; $k=0;
			}
			//if($seq_first==$inf[csf("id")]) $symb= "@@"; else $symb= "";
			//$style_name=$po_data_arr[$val[csf('order_id')]]["style_ref_no"];
			//$buyer_name=$po_data_arr[$val[csf('order_id')]]["buyer_name"];
			//$po_number=$po_data_arr[$val[csf('order_id')]]["po_number"];
			//$internal_ref=$po_data_arr[$val[csf('order_id')]]["grouping"];
			//$batch_no=$roll_data_arr[$val[csf('roll_id')]];
			$bundle_no=$val[csf("bundle_no")];
			$buyer_name=$val[csf("party_id")];

			$buyer_buyer=$updtls_data_arr[$val[csf("job_dtls_id")]][$val[csf("job_break_id")]]['buyer_buyer'];
			$buyer_po_no=$updtls_data_arr[$val[csf("job_dtls_id")]][$val[csf("job_break_id")]]['buyer_po_no'];
			$buyer_style_ref=$updtls_data_arr[$val[csf("job_dtls_id")]][$val[csf("job_break_id")]]['buyer_style_ref'];
			$color_id=$updtls_data_arr[$val[csf("job_dtls_id")]][$val[csf("job_break_id")]]['color_id'];
			$size_id=$updtls_data_arr[$val[csf("job_dtls_id")]][$val[csf("job_break_id")]]['size_id'];
			$order_no=$updtls_data_arr[$val[csf("job_dtls_id")]][$val[csf("job_break_id")]]['order_no'];
		
			$pdf->Code40($i, $j-2, "BNDL- ".$bundle_no.", Party- ".$buyer_library[$buyer_name], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;
			$pdf->Code40($i, $j+1.2, "Buy- ".$buyer_buyer.", Sty- ". $buyer_style_ref, $ext = true, $cks = false, $w =0.15, $h = 1, $wide = true, true,7) ;
			$pdf->Code40($i, $j+3.8, "PO- ".$order_no.", Color- ". $color_library[$color_id], $ext = true, $cks = false, $w =0.15, $h = 1, $wide = true, true,7) ;
			$pdf->Code40($i, $j+6, "Cut No- ".$val[csf("cut_no")].", Size- ". $size_arr[$size_id], $ext = true, $cks = false, $w =0.15, $h = 1, $wide = true, true,7) ;
			$pdf->Code40($i, $j+8.5, "BNDL Qty- ".$val[csf("dtls_bndl_qty")].',  '. $inf[csf("bundle_use_for")], $ext = true, $cks = false, $w = 0.2, $h = 1, $wide=true,true,7);
			//$pdf->Code40($i, $j+22, $val[csf("bundle_no")],$w =0.2, $h = 8);
			$pdf->Code39($i, $j+17, $val[csf("barcode_no")]);
			//$pdf->Code40($i+12.7, $j+7.6, $val[csf("barcode_no")], $ext = true, $cks = false, $w =0.15, $h = 1, $wide = true, true,7) ;
			$k++;
			$i=2; $j=$j+13;
			$br++;
		} 
	}
	
	foreach (glob(""."*.pdf") as $filename) {			
		@unlink($filename);
	}
	$name = 'bundle_barcode_' . date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output( "".$name, 'F');
	echo $name;
	exit(); 
}
	



?>