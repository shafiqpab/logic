<?
include('../../../includes/common.php');
session_start();

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

if ($action == "load_drop_machine")
	{
		$data=explode('_',$data);
		//$company_id = $data;
 		echo create_drop_down("cboMachineName_".$data[1], 100, "select id, machine_no as machine_name from lib_machine_name where category_id=33 and company_id=$data[0] and status_active=1 and is_deleted=0 order by seq_no", "id,machine_name", 1, "-- Select Machine --", 0, "copy_machine_name($data[1])","","","","","","","","cboMachineName[]");
 		exit();
	}
if ($action == "check_conversion_rate") 
{
	$data = explode("**", $data);
	if ($db_type == 0) {
		$conversion_date = change_date_format($data[1], "Y-m-d", "-", 1);
	} else {
		$conversion_date = change_date_format($data[1], "d-M-y", "-", 1);
	}
	$exchange_rate = set_conversion_rate($data[0], $conversion_date, $data[2]);
	//$exchange_rate = set_conversion_rate($data[0], $conversion_date);
	echo $exchange_rate;
	exit();
}


if ($action=="load_drop_down_location")
{
	$data=explode("_",$data);
	if($data[1]==1) $dropdown_name="cbo_location_name";
	else $dropdown_name="cbo_party_location";
	
	echo create_drop_down( $dropdown_name, 150, "SELECT id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );	
	exit();
}

if ($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);

	if($data[1]==1) $load_function="fnc_load_party(2,document.getElementById('cbo_within_group').value)";
	else $load_function="";
	
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_party_name", 150, "SELECT comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $data[2], "$load_function");
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 150, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[2], "" );
	}	
	exit();	 
}

if ($action=="load_drop_down_member")
{
	$data=explode("_",$data);
	$sql="select b.id,b.team_member_name  from lib_marketing_team a, lib_mkt_team_member_info b where a.id=b.team_id and   a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.project_type=5 and team_id='$data[0]'";
	echo create_drop_down( "cbo_team_member", 150, $sql,"id,team_member_name", 1, "-- Select Member --", $selected, "" );	
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$user_id=$_SESSION['logic_erp']['user_id'];
	
	if ($operation==0) // Insert Start Here=================================================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		  
		//echo "10**".$operation; die;
		if($db_type==0) $insert_date_con="and YEAR(insert_date)=".date('Y',time())."";
		else if($db_type==2) $insert_date_con="and TO_CHAR(insert_date,'YYYY')=".date('Y',time())."";
		
		$new_job_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'AOP', date("Y",time()), 5, "select job_no_prefix, job_no_prefix_num from subcon_ord_mst where entry_form=278 and company_id=$cbo_company_name $insert_date_con order by id desc ", "job_no_prefix", "job_no_prefix_num" ));

		$id=return_next_id("id","subcon_ord_mst",1);
		$id1=return_next_id("id", "subcon_ord_dtls",1) ;
		//$id=$id1=true;
		$field_array="id, entry_form, subcon_job, job_no_prefix, job_no_prefix_num, company_id, location_id, within_group, party_id, party_location, currency_id, receive_date, delivery_date, rec_start_date, rec_end_date, order_id, order_no,aop_reference,aop_order_type, aop_work_order_type, remarks, team_leader, team_member,exchange_rate,inserted_by, insert_date, status_active, is_deleted";
		
		if(str_replace("'","",$txt_order_no)!="")
		{
			$txt_order_no=$txt_order_no;
		}
		else
		{
			$txt_order_no="'".$new_job_no[0]."'";
		}

		$data_array="(".$id.", 278, '".$new_job_no[0]."', '".$new_job_no[1]."', '".$new_job_no[2]."', ".$cbo_company_name.", ".$cbo_location_name.", ".$cbo_within_group.", ".$cbo_party_name.", ".$cbo_party_location.", ".$cbo_currency.", ".$txt_order_receive_date.", ".$txt_delivery_date.",".$txt_rec_start_date.",".$txt_rec_end_date.", ".$hid_order_id.", ".$txt_order_no.", ".$txt_aop_ref.", ".$cbo_order_type.", ".$cbo_work_order_type.", ".$txt_remarks.", ".$cbo_team_leader.", ".$cbo_team_member.",".$txt_exchange_rate.",".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."',1,0)";
		$txt_job_no=$new_job_no[0];
		$field_array2="id, mst_id, job_no_mst, order_id, order_no, buyer_po_id, body_part, construction, composition, gsm, grey_dia, gmts_color_id, item_color_id, fin_dia, aop_color_id, lib_yarn_deter, booking_dtls_id, order_quantity, order_uom, rate, amount,domestic_amount, wastage,artwork_no ,design_no ,buyer_po_no, buyer_style_ref, buyer_buyer, print_type,billing_on,machine_id,delivery_date, inserted_by, insert_date, status_active, is_deleted";
		
		$color_library_arr=return_library_array( "SELECT id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
		//$size_library_arr=return_library_array( "SELECT id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
		$data_array2="";  $add_commaa=0; $new_arr_color=array(); 

		for($i=1; $i<=$total_row; $i++)
		{			
			$txtbuyerPoId			= "txtbuyerPoId_".$i; 
			$txtbuyerPo				= "txtbuyerPo_".$i;
			$txtstyleRef			= "txtstyleRef_".$i;
			$txtbuyer				= "txtbuyer_".$i;
			$cboBodyPart			= "cboBodyPart_".$i;
			$txtConstruction		= "txtConstruction_".$i;
			$txtComposition			= "txtComposition_".$i;
			$txtGsm					= "txtGsm_".$i;
			$txtDia					= "txtDia_".$i;
			$txtGmtsColor 			= "txtGmtsColor_".$i;
			$txtItemColor 			= "txtItemColor_".$i;
			$txtFinDia 				= "txtFinDia_".$i;			
			$txtAopColor 			= "txtAopColor_".$i;
			$txtQty 				= "txtQty_".$i;
			$cboUom 				= "cboUom_".$i;
			$txtRateUnit 			= "txtRateUnit_".$i;
			$txtAmount 				= "txtAmount_".$i;
			$txtProcessLoss 		= "txtProcessLoss_".$i;
			$txtArtwork 			= "txtArtwork_".$i;
			$cboPrintType 			= "cboPrintType_".$i;
			$cboMachineName 		= "cboMachineName_".$i;
			$cboBillingOn 			= "cboBillingOn_".$i;
			$txtDesignNo 			= "txtDesignNo_".$i;
			$hdnlibyarndetar 		= "hdnlibyarndetar_".$i;
			$hdnbookingDtlsId 		= "hdnbookingDtlsId_".$i;
			$hdnDtlsUpdateId 		= "hdnDtlsUpdateId_".$i;
			$txtOrderDeliveryDate 	= "txtOrderDeliveryDate_".$i;
			
			/*if(str_replace("'","",$$txtGmtsColor)!="")
			{ 
				if (!in_array(str_replace("'","",$$txtGmtsColor),$new_arr_color))
				{
					$gmtsColor_id = return_id( str_replace("'","",$$txtGmtsColor), $color_library_arr, "lib_color", "id,color_name","278");  
					$new_arr_color_gmts[$gmtsColor_id]=str_replace("'","",$$txtGmtsColor);
				}
				else $gmtsColor_id =  array_search(str_replace("'","",$$txtGmtsColor), $new_arr_color_gmts); 
			}
			else $gmtsColor_id=0;*/
			
			
			$domistic_amount=str_replace("'",'',$$txtAmount)*str_replace("'",'',$txt_exchange_rate);

			if (str_replace("'", "", trim($$txtGmtsColor)) != "") {
				if (!in_array(str_replace("'", "", trim($$txtGmtsColor)),$new_arr_color,TRUE)){
					$gmtsColor_id = return_id( str_replace("'", "", trim($$txtGmtsColor)), $color_library_arr, "lib_color", "id,color_name","278");
					$new_arr_color[$gmtsColor_id]=str_replace("'", "", trim($$txtGmtsColor));
				}
				else $gmtsColor_id =  array_search(str_replace("'", "", trim($$txtGmtsColor)), $new_arr_color);
			} else $gmtsColor_id = 0;

			if (str_replace("'", "", trim($$txtItemColor)) != "") {
				if (!in_array(str_replace("'", "", trim($$txtItemColor)),$new_arr_color,TRUE)){
					$itemColor_id = return_id( str_replace("'", "", trim($$txtItemColor)), $color_library_arr, "lib_color", "id,color_name","278");
					$new_arr_color[$itemColor_id]=str_replace("'", "", trim($$txtItemColor));
				}
				else $itemColor_id =  array_search(str_replace("'", "", trim($$txtItemColor)), $new_arr_color);
			} else $itemColor_id = 0;
			
			
			if (str_replace("'", "", trim($$txtAopColor)) != "") {
				if (!in_array(str_replace("'", "", trim($$txtAopColor)),$new_arr_color,TRUE)){
					$aopColor_id = return_id( str_replace("'", "", trim($$txtAopColor)), $color_library_arr, "lib_color", "id,color_name","278");
					$new_arr_color[$aopColor_id]=str_replace("'", "", trim($$txtAopColor));
				}
				else $aopColor_id =  array_search(str_replace("'", "", trim($$txtAopColor)), $new_arr_color);
			} else $aopColor_id = 0;


			if(str_replace("'",'',$$txtbuyerPoId)=="") $txtbuyerPoId=0; else $txtbuyerPoId=str_replace("'",'',$$txtbuyerPoId);
			if ($add_commaa!=0) $data_array2 .=","; $add_comma=0;
			$data_array2 .="(".$id1.",".$id.",'".$new_job_no[0]."',".$hid_order_id.",".$txt_order_no.",'".$txtbuyerPoId."',".$$cboBodyPart.",".$$txtConstruction.",".$$txtComposition.",".$$txtGsm.",".$$txtDia.",'".$gmtsColor_id."','".$itemColor_id."',".$$txtFinDia.",'".$aopColor_id."',".$$hdnlibyarndetar.",".$$hdnbookingDtlsId.",".str_replace(",",'',$$txtQty).",".$$cboUom.",".$$txtRateUnit.",".str_replace(",",'',$$txtAmount).",".$domistic_amount.",".$$txtProcessLoss.",".$$txtArtwork.",".$$txtDesignNo.",".$$txtbuyerPo.",".$$txtstyleRef.",".$$txtbuyer.",".$$cboPrintType.",".$$cboBillingOn.",".$$cboMachineName.",".$$txtOrderDeliveryDate.",'".$user_id."','".$pc_date_time."',1,0)";
			
			$id1=$id1+1; $add_commaa++;
		}
		// echo "10**INSERT INTO subcon_ord_dtls (".$field_array2.") VALUES ".$data_array2; die;
		$flag=1;
		$rID=sql_insert("subcon_ord_mst",$field_array,$data_array,0);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		$rID1=sql_insert("subcon_ord_dtls",$field_array2,$data_array2,0);
		if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		
		if(str_replace("'","",$cbo_within_group)==1)
		{
			$rIDBooking=execute_query( "update wo_booking_mst set lock_another_process=1 where booking_no =".$txt_order_no."",1);
			if($rIDBooking==1 && $flag==1) $flag=1; else $flag=0;
		}
		 //echo "10**".$rID."**".$rID1."**".$rIDBooking; die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$id);
			}
		}
		else if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$id);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here================================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		/*$rec_number=return_field_value( "sys_no", "sub_material_mst"," embl_job_no=$txt_job_no and status_active=1 and is_deleted=0 and trans_type=1");
		if($rec_number){
			echo "aopRec**".str_replace("'","",$txt_job_no)."**".$rec_number;
			disconnect($con); die;
		}
		
		$recipe_number=return_field_value( "recipe_no", "pro_recipe_entry_mst"," job_no=$txt_job_no and status_active=1 and is_deleted=0 and entry_form=220");
		if($recipe_number){
			echo "aopRecipe**".str_replace("'","",$txt_job_no)."**".$recipe_number;
			disconnect($con); die;
		}*/
		
		$color_library_arr=return_library_array( "SELECT id,color_name from lib_color  where status_active=1 and is_deleted=0", "id", "color_name"  );
		//$size_library_arr=return_library_array( "SELECT id,size_name from lib_size  where status_active=1 and is_deleted=0", "id", "size_name"  );

		$field_array="location_id*within_group*party_id*party_location*currency_id*receive_date*delivery_date*rec_start_date*rec_end_date*order_id*order_no*aop_reference*aop_order_type*aop_work_order_type*remarks*team_leader*team_member*exchange_rate*updated_by*update_date";		
		$data_array="".$cbo_location_name."*".$cbo_within_group."*".$cbo_party_name."*".$cbo_party_location."*".$cbo_currency."*".$txt_order_receive_date."*".$txt_delivery_date."*".$txt_rec_start_date."*".$txt_rec_end_date."*".$hid_order_id."*".$txt_order_no."*".$txt_aop_ref."*".$cbo_order_type."*".$cbo_work_order_type."*".$txt_remarks."*".$cbo_team_leader."*".$cbo_team_member."*".$txt_exchange_rate."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		//$field_array2="id, mst_id, job_no_mst, order_id, order_no, buyer_po_id, body_part, construction, composition, gsm, grey_dia, gmts_color_id, item_color_id, fin_dia, aop_color_id, lib_yarn_deter, booking_dtls_id, order_quantity, order_uom, rate, amount, wastage, inserted_by, insert_date, status_active, is_deleted";
		$field_array2="order_id*order_no*buyer_po_id*body_part*construction*composition*gsm*grey_dia*gmts_color_id*item_color_id*fin_dia*aop_color_id*lib_yarn_deter*booking_dtls_id*order_quantity*order_uom*rate*amount*wastage*artwork_no*design_no*buyer_po_no*buyer_style_ref*buyer_buyer*print_type*billing_on*machine_id*delivery_date*domestic_amount*status_active*is_deleted*updated_by*update_date";
		$field_array_insert="id, mst_id, job_no_mst, order_id, order_no, buyer_po_id, body_part, construction, composition, gsm, grey_dia, gmts_color_id, item_color_id, fin_dia, aop_color_id, lib_yarn_deter, booking_dtls_id, order_quantity, order_uom, rate, amount,domestic_amount, wastage,artwork_no ,design_no ,buyer_po_no, buyer_style_ref, buyer_buyer,print_type,billing_on,machine_id, inserted_by, insert_date, status_active, is_deleted";
		
		$id1=return_next_id("id", "subcon_ord_dtls",1) ;
		$add_comma=0;	$flag=""; $new_arr_color=array(); 
		for($i=1; $i<=$total_row; $i++)
		{			
			$txtbuyerPoId			= "txtbuyerPoId_".$i; 
			$txtbuyerPo				= "txtbuyerPo_".$i;
			$txtstyleRef			= "txtstyleRef_".$i;
			$txtbuyer				= "txtbuyer_".$i;
			$cboBodyPart			= "cboBodyPart_".$i;
			$txtConstruction		= "txtConstruction_".$i;
			$txtComposition			= "txtComposition_".$i;
			$txtGsm					= "txtGsm_".$i;
			$txtDia					= "txtDia_".$i;
			$txtGmtsColor 			= "txtGmtsColor_".$i;
			$txtItemColor 			= "txtItemColor_".$i;
			$txtFinDia 				= "txtFinDia_".$i;			
			$txtAopColor 			= "txtAopColor_".$i;
			$txtQty 				= "txtQty_".$i;
			$cboUom 				= "cboUom_".$i;
			$txtRateUnit 			= "txtRateUnit_".$i;
			$txtAmount 				= "txtAmount_".$i;
			$txtProcessLoss 		= "txtProcessLoss_".$i;
			$txtArtwork 			= "txtArtwork_".$i;
			$cboPrintType 			= "cboPrintType_".$i;
			$cboBillingOn 			= "cboBillingOn_".$i;
			$txtDesignNo 			= "txtDesignNo_".$i;
			$hdnlibyarndetar 		= "hdnlibyarndetar_".$i;
			$hdnbookingDtlsId 		= "hdnbookingDtlsId_".$i;
			$hdnDtlsUpdateId 		= "hdnDtlsUpdateId_".$i;
			$txtOrderDeliveryDate 	= "txtOrderDeliveryDate_".$i;
			$cboMachineName 		= "cboMachineName_".$i;
			
			$domistic_amount=str_replace("'",'',$$txtAmount)*str_replace("'",'',$txt_exchange_rate);
			
			//echo "10**".$domistic_amount; die;
			
			
			$aa=str_replace("'",'',$$hdnDtlsUpdateId);
			
			if(str_replace("'","",$$txtGmtsColor)=='0') $$txtGmtsColor='';
			if (str_replace("'", "", trim($$txtGmtsColor)) != "") {
				if (!in_array(str_replace("'", "", trim($$txtGmtsColor)),$new_arr_color,TRUE)){
					$gmtsColor_id = return_id( str_replace("'", "", trim($$txtGmtsColor)), $color_library_arr, "lib_color", "id,color_name","278");
					$new_arr_color[$gmtsColor_id]=str_replace("'", "", trim($$txtGmtsColor));
				}
				else $gmtsColor_id =  array_search(str_replace("'", "", trim($$txtGmtsColor)), $new_arr_color);
			} else $gmtsColor_id = 0;
			if(str_replace("'","",$$txtItemColor)=='0') $$txtItemColor='';
			if (str_replace("'", "", trim($$txtItemColor)) != "") {
				if (!in_array(str_replace("'", "", trim($$txtItemColor)),$new_arr_color,TRUE)){
					$itemColor_id = return_id( str_replace("'", "", trim($$txtItemColor)), $color_library_arr, "lib_color", "id,color_name","278");
					$new_arr_color[$itemColor_id]=str_replace("'", "", trim($$txtItemColor));
				}
				else $itemColor_id =  array_search(str_replace("'", "", trim($$txtItemColor)), $new_arr_color);
			} else $itemColor_id = 0;
			
			if(str_replace("'","",$$txtAopColor)=='0') $$txtAopColor='';
			if (str_replace("'", "", trim($$txtAopColor)) != "") {
				if (!in_array(str_replace("'", "", trim($$txtAopColor)),$new_arr_color,TRUE)){
					$aopColor_id = return_id( str_replace("'", "", trim($$txtAopColor)), $color_library_arr, "lib_color", "id,color_name","278");
					$new_arr_color[$aopColor_id]=str_replace("'", "", trim($$txtAopColor));
				}
				else $aopColor_id =  array_search(str_replace("'", "", trim($$txtAopColor)), $new_arr_color);
			} else $aopColor_id = 0;

			if(str_replace("'",'',$$txtbuyerPoId)=="") $txtbuyerPoId=0; else $txtbuyerPoId=str_replace("'",'',$$txtbuyerPoId);
			if ($add_commaa!=0) $data_array_insert .=","; $add_comma=0;
			if($aa=="")
			{
				$data_array_insert .="(".$id1.",".$update_id.",".$txt_job_no.",".$hid_order_id.",".$txt_order_no.",'".$txtbuyerPoId."',".$$cboBodyPart.",".$$txtConstruction.",".$$txtComposition.",".$$txtGsm.",".$$txtDia.",'".$gmtsColor_id."','".$itemColor_id."',".$$txtFinDia.",'".$aopColor_id."',".$$hdnlibyarndetar.",".$$hdnbookingDtlsId.",".str_replace(",",'',$$txtQty).",".$$cboUom.",".$$txtRateUnit.",".str_replace(",",'',$$txtAmount).",".$domistic_amount.",".$$txtProcessLoss.",".$$txtArtwork.",".$$txtDesignNo.",".$$txtbuyerPo.",".$$txtstyleRef.",".$$txtbuyer.",".$$cboPrintType.",".$$cboBillingOn.",".$$cboMachineName.",'".$user_id."','".$pc_date_time."',1,0)";
			
				$id1=$id1+1; $add_commaa++;
			}
			else
			{
				$data_array2[$aa]=explode("*",("".$hid_order_id."*".$txt_order_no."*'".$txtbuyerPoId."'*".$$cboBodyPart."*".$$txtConstruction."*".$$txtComposition."*".$$txtGsm."*".$$txtDia."*'".$gmtsColor_id."'*'".$itemColor_id."'*".$$txtFinDia."*'".$aopColor_id."'*".$$hdnlibyarndetar."*".$$hdnbookingDtlsId."*".str_replace(",",'',$$txtQty)."*".$$cboUom."*".$$txtRateUnit."*".str_replace(",",'',$$txtAmount)."*".$$txtProcessLoss."*".$$txtArtwork."*".$$txtDesignNo."*".$$txtbuyerPo."*".$$txtstyleRef."*".$$txtbuyer."*".$$cboPrintType."*".$$cboBillingOn."*".$$cboMachineName."*".$$txtOrderDeliveryDate."*".$domistic_amount."*1*0*".$user_id."*'".$pc_date_time."'"));
				$hdn_dtls_id_arr[]=str_replace("'",'',$$hdnDtlsUpdateId);
			}
		}
		$flag=1;
		//echo "10**";
		//print_r($data_array2); disconnect($con); die;
		//echo "INSERT INTO subcon_ord_breakdown (".$field_array3.") VALUES ".$data_array3; disconnect($con); die;
		//echo "10**".bulk_update_sql_statement( "subcon_ord_dtls", "id",$field_array2,$data_array2,$hdn_dtls_id_arr); disconnect($con); die;
		$rID=sql_update("subcon_ord_mst",$field_array,$data_array,"id",$update_id,0);  
		if($rID==1 && $flag==1) $flag=1; else $flag=0;

		$rID_del=sql_multirow_update("subcon_ord_dtls","status_active*is_deleted*updated_by*update_date","0*1*$user_id*'".$pc_date_time."'","mst_id",$update_id,0);
		if($rID_del==1 && $flag==1) $flag=1; else $flag=0;
		
		if($data_array2!="")
		{
			$rID1=execute_query(bulk_update_sql_statement( "subcon_ord_dtls", "id",$field_array2,$data_array2,$hdn_dtls_id_arr),0);
			if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		}
		//echo "10**INSERT INTO subcon_ord_dtls (".$field_array_insert.") VALUES ".$data_array_insert; disconnect($con); die;
		if($data_array_insert!="")
		{
			$rID2=sql_insert("subcon_ord_dtls",$field_array_insert,$data_array_insert,0);
			if($rID2==1 && $flag==1) $flag=1; else $flag=0;
		}
		//$rID1=sql_update("subcon_ord_dtls",$field_array2,$data_array2,"id",$update_id2,0); 
		//if($rID1) $flag=1; else $flag=0;
		
		// echo "10**".$rID.'='.$rID1.'='.$rID2.'='.$rID_del.'='.$flag; disconnect($con); die;
		//==============================================================================================================================================
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id);
			}
		}
		else if($db_type==2)
		{  
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // delete here=======================================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");  
		}
		
		$rec_number=return_field_value( "sys_no", "sub_material_mst"," embl_job_no=$txt_job_no and status_active=1 and is_deleted=0 and trans_type=1");
		if($rec_number){
			echo "aopRec**".str_replace("'","",$txt_job_no)."**".$rec_number;
			disconnect($con); die;
		}

		//if ( $delete_master_info==1 )
		//{
		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		$flag=1;
		$rID=sql_update("subcon_ord_mst",$field_array,$data_array,"id",$update_id,1);  
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		$rID1=sql_update("subcon_ord_dtls",$field_array,$data_array,"job_no_mst",$txt_job_no,1);
		if($rID1==1 && $flag==1) $flag=1; else $flag=0;  
		//$rID = sql_delete("subcon_ord_dtls","status_active*is_deleted*updated_by*update_date","0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'",'id',$update_id2,1);
		$rID2=execute_query( "delete from subcon_ord_breakdown where job_no_mst=$txt_job_no",0);
		if($rID2==1 && $flag==1) $flag=1; else $flag=0;
		
		$rID3=execute_query( "update wo_booking_mst set lock_another_process=0 where booking_no =".$txt_order_no."",1);
		if($rID3==1 && $flag==1) $flag=1; else $flag=0;
		//}
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "2**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id);;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die; 
	}
}

if ($action=="job_popup")
{
	echo load_html_head_contents("Job Popup Info","../../../", 1, 1, $unicode,'','');
	?>
	<script>
		function js_set_value(id)
		{ 
			$("#hidden_mst_id").val(id);
			document.getElementById('selected_job').value=id;
			parent.emailwindow.hide();
		}
		
		function fnc_load_party_popup(type,within_group)
		{
			var company = $('#cbo_company_name').val();
			var party_name = $('#cbo_party_name').val();
			var location_name = $('#cbo_location_name').val();
			load_drop_down( 'aop_order_entry_controller', company+'_'+within_group, 'load_drop_down_buyer', 'buyer_td' );
		}
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0) $('#search_by_td').html('AOP. Job No');
			else if(val==2) $('#search_by_td').html('W/O No');
			else if(val==3) $('#search_by_td').html('Buyer Job');
			else if(val==4) $('#search_by_td').html('Buyer Po');
			else if(val==5) $('#search_by_td').html('Buyer Style');
		}
	</script>
</head>
<body>
<div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="1050" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead> 
                <tr>
                    <th colspan="9"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </tr>
                <tr>               	 
                    <th width="140" class="must_entry_caption">Company Name</th>
                    <th width="100">Within Group</th>                           
                    <th width="140">Party Name</th>
                    <th width="100">Search By</th>
                    <th width="100" id="search_by_td">AOP. Job No</th>
                     <th width="100">AOP Ref.</th>
                    <th width="100">Year</th>
                    <th width="170">Date Range</th>                            
                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                </tr>           
            </thead>
            <tbody>
                <tr class="general">
                    <td><input type="hidden" id="selected_job"><? $data=explode("_",$data); ?>  <!--  echo $data;-->
                        <? 
                        echo create_drop_down( "cbo_company_name", 140, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $data[0], "fnc_load_party_popup(1,document.getElementById('cbo_within_group').value);",1); ?>
                    </td>
                    <td>
                        <?php echo create_drop_down( "cbo_within_group", 100, $yes_no,"", 0, "--  --", $data[3], "fnc_load_party_popup(1,this.value);" ); ?>
                    </td>
                    <td id="buyer_td">
                        <? echo create_drop_down( "cbo_party_name", 150, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[2], "" );   	 
                        ?>
                    </td>
                    <td>
						<?
                            $search_by_arr=array(1=>"AOP. Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style");
                            echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                        ?>
                    </td>
                    <td align="center">
                        <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                    </td>
                    <td align="center">
                                <input type="text" name="txt_aop_ref" id="txt_aop_ref" class="text_boxes" style="width:100px" placeholder="" />
                     </td>
                    <td align="center"><? echo create_drop_down( "cbo_year_selection", 100, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" readonly>
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" readonly>
                    </td>
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_aop_ref').value, 'create_job_search_list_view', 'search_div', 'aop_order_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
                    </tr>
                    <tr>
                        <td colspan="9" align="center" valign="middle">
                            <? echo load_month_buttons();  ?>
                            <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px" readonly>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="9" align="center" valign="top" id=""><div id="search_div"></div></td>
                    </tr>
                </tbody>
            </table>    
            </form>
        </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}
	
if($action=="create_job_search_list_view")
{	
	$data=explode('_',$data);
	$party_id=str_replace("'","",$data[1]);
	$search_by=str_replace("'","",$data[4]);
	$search_str=trim(str_replace("'","",$data[5]));
	$search_type =$data[6];
	$within_group =$data[7];
	$aop_referrance=trim(str_replace("'","",$data[9]));
	//print_r($aop_ref);
	
	if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[8]";   }
	if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$data[8]";}
	
	
	//print_r($aop_cond);   die;

	if($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	//echo $search_type; die;
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num = '$search_str' ";
			else if ($search_by==4) $po_cond=" and b.po_number = '$search_str' ";
			else if ($search_by==5) $style_cond=" and a.style_ref_no = '$search_str' ";
		}
		if ($aop_referrance!="") $aop_cond=" and a.aop_reference = '$aop_referrance' ";
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str%'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str%'"; 
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str%'";   
		}
		if ($aop_referrance!="") $aop_cond=" and a.aop_reference like '%$aop_referrance%'";  
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '$search_str%'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '$search_str%'";
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '$search_str%'";  
		}
		if ($aop_referrance!="") $aop_cond=" and a.aop_reference like '$aop_referrance%'";  
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str'";
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str'";  
		}
		if ($aop_referrance!="") $aop_cond=" and a.aop_reference like '%$aop_referrance'";  
	}	

	if($party_id!=0) $party_id_cond=" and a.party_id='$party_id'"; else $party_id_cond="";

	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and a.receive_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $order_rcv_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and a.receive_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $order_rcv_date ="";
	}
	if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
	if($within_group==1)
	{
		$party_arr=return_library_array( "SELECT id, company_name from lib_company where status_active =1 and is_deleted=0",'id','company_name');
	}
	else
	{
		$party_arr=return_library_array( "SELECT id, buyer_name from lib_buyer where status_active =1 and is_deleted=0",'id','buyer_name');
	}
	
	$po_ids='';
	
	if($db_type==0) $id_cond="group_concat(b.id)";
	else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	//echo "select $id_cond as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
	if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
	{
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond and and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0", "id");
	}
	//echo $po_ids;
	if ($po_ids!="") $po_idsCond=" and b.buyer_po_id in ($po_ids)"; else $po_idsCond="";
	$color_arr=return_library_array( "SELECT id, color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
	$buyer_po_arr=array();
	if($within_group==1)
	{
		$po_sql ="SELECT a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
		$po_sql_res=sql_select($po_sql);
		foreach ($po_sql_res as $row)
		{
			$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
			$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		}
		unset($po_sql_res);
	}
	
	if($db_type==0)
	{
		$ins_year_cond="year(a.insert_date)";
		$aopcolor_id_str="group_concat(b.aop_color_id)";
		$buyer_po_id_str="group_concat(b.buyer_po_id)";
		
		if($within_group==1)
		{
			$buyer_po_id_str=",group_concat(b.buyer_po_id) as buyer_po_id";
		}
		else
		{
			$buyer_po_no_str=",group_concat(b.buyer_po_no) as buyer_po_id";
			$buyer_po_style_str=",group_concat(b.buyer_style_ref) as buyer_style";
		}
	}
	else if($db_type==2)
	{
		$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
		
		
		if($within_group==1)
		{
			$aopcolor_id_str="listagg(b.aop_color_id,',') within group (order by b.aop_color_id)";
		    $buyer_po_id_str="listagg(b.buyer_po_id,',') within group (order by b.buyer_po_id)";
		}
		else
		{
			
			$aopcolor_id_str="listagg(b.aop_color_id,',') within group (order by b.aop_color_id)";
		    $buyer_po_id_str="listagg(b.buyer_po_id,',') within group (order by b.buyer_po_id)";
			
			$buyer_po_no_str=",listagg(b.buyer_po_no,',') within group (order by b.id) as buyer_po_no";
			$buyer_po_style_str=",listagg(b.buyer_style_ref,',') within group (order by b.id) as buyer_style";
			
			
		}
		
	}
	 $sql= "SELECT a.id, a.subcon_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date, $aopcolor_id_str as color_id, $buyer_po_id_str as buyer_po_id $buyer_po_no_str $buyer_po_style_str,a.aop_reference
	 from subcon_ord_mst a, subcon_ord_dtls b 
	 where a.entry_form=278 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and b.status_active=1 $order_rcv_date $company $buyer $withinGroup $search_com_cond $po_idsCond $withinGroup $aop_cond $year_cond
	 group by a.id, a.subcon_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date,a.aop_reference
	 order by a.id DESC";
	// echo $sql;
	 $data_array=sql_select($sql);
	?>
     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="1050" >
        <thead>
            <th width="30">SL</th>
            <th width="60">Job No</th>
            <th width="60">Year</th>
            <th width="120">W/O No</th>
            <th width="150">Buyer Po</th>
            <th width="150">Buyer Style</th>
            <th width="80">Ord Receive Date</th>
            <th width="80">Delivery Date</th>
            <th width="200">AOP Color</th>
            <th>AOP Reference</th>
        </thead>
        </table>
        <div style="width:1050px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1030" class="rpt_table" id="tbl_po_list">
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
				$buyer_po_id=array_unique(explode(",",$row[csf('buyer_po_id')]));
				$buyer_po_ids_arr=array();
				foreach($buyer_po_id as $po_id)
				{
					if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
					if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
					
					$buyer_po_ids_arr[$po_id].=$po_id;
				}
				
				
				//print_r($buyer_po_ids_arr);
						
				if($within_group==1)
				{
						$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
						$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
				}
				else
				{
					
					$buyer_po_no = $row[csf('buyer_po_no')];
					$buyer_style = $row[csf('buyer_style')];
					$buyer_po=implode(",",array_unique(explode(",",$buyer_po_no)));
					$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
					
					}
					
					
				$buyerpoidsdata=implode(",",$buyer_po_ids_arr);
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$row[csf('subcon_job')].'_'.$buyerpoidsdata; ?>")' style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                    <td width="60" style="text-align:center;"><? echo $row[csf('year')]; ?></td>
                    <td width="120"><? echo $row[csf('order_no')]; ?></td>
                    <td width="150" style="word-break:break-all"><? echo $buyer_po; ?></td>
                    <td width="150" style="word-break:break-all"><? echo $buyer_style; ?></td>
                    <td width="80" style="text-align:center;"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                    <td width="80" style="text-align:center;"><? echo change_date_format($row[csf('delivery_date')]); ?></td>	
                    <td width="200"  style="word-break:break-all"><? echo $color_name; ?></td>
                    <td style="word-break:break-all"><? echo $row[csf('aop_reference')]; ?></td>
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
 
if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "SELECT id, subcon_job, company_id, location_id, party_id, currency_id, party_location, delivery_date, rec_start_date, rec_end_date, receive_date, within_group, party_location, order_id, order_no,aop_reference,aop_order_type, aop_work_order_type, remarks, team_leader, team_member,exchange_rate
		from subcon_ord_mst
		where subcon_job='$data' and status_active=1 and is_deleted=0" );
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_job_no').value 			= '".$row[csf("subcon_job")]."';\n";  
		echo "document.getElementById('cbo_company_name').value 	= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_within_group').value 	= '".$row[csf("within_group")]."';\n";  
		echo "$('#cbo_company_name').attr('disabled','true')".";\n";
		echo "fnc_load_party(1,".$row[csf("within_group")].");\n";	
	//echo "load_drop_down( 'requires/aop_order_entry_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_location', 'location_td' );\n";	
		echo "document.getElementById('cbo_location_name').value 		= '".$row[csf("location_id")]."';\n";
	//echo "load_drop_down( 'requires/aop_order_entry_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_buyer', 'buyer_td' );\n";
		echo "document.getElementById('cbo_party_name').value			= '".$row[csf("party_id")]."';\n";
		echo "document.getElementById('cbo_currency').value				= '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value				= '".$row[csf("exchange_rate")]."';\n";
		echo "fnc_load_party(2,".$row[csf("within_group")].");\n";	 
		echo "document.getElementById('cbo_party_location').value		= '".$row[csf("party_location")]."';\n";	
		echo "document.getElementById('txt_order_receive_date').value	= '".change_date_format($row[csf("receive_date")])."';\n"; 
		echo "document.getElementById('txt_delivery_date').value		= '".change_date_format($row[csf("delivery_date")])."';\n"; 
		echo "document.getElementById('txt_rec_start_date').value		= '".change_date_format($row[csf("rec_start_date")])."';\n"; 
		echo "document.getElementById('txt_rec_end_date').value			= '".change_date_format($row[csf("rec_end_date")])."';\n"; 
		echo "document.getElementById('hid_order_id').value          	= '".$row[csf("order_id")]."';\n";
		echo "document.getElementById('txt_order_no').value         	= '".$row[csf("order_no")]."';\n";
		echo "document.getElementById('txt_aop_ref').value         		= '".$row[csf("aop_reference")]."';\n";
		echo "document.getElementById('cbo_order_type').value         	= '".$row[csf("aop_order_type")]."';\n";
		echo "document.getElementById('cbo_work_order_type').value      = '".$row[csf("aop_work_order_type")]."';\n";

		echo "document.getElementById('cbo_team_leader').value    		= '".$row[csf("team_leader")]."';\n";
		echo "load_drop_down( 'requires/aop_order_entry_controller', ".$row[csf("team_leader")].", 'load_drop_down_member', 'member_td');\n";
		echo "document.getElementById('cbo_team_member').value    		= '".$row[csf("team_member")]."';\n";

		echo "document.getElementById('txt_remarks').value         		= '".$row[csf("remarks")]."';\n";
		echo "$('#txt_order_no').attr('disabled','true')".";\n";
		echo "$('#cbo_within_group').attr('disabled','true')".";\n";
		echo "$('#cbo_party_name').attr('disabled','true')".";\n";
		echo "$('#cbo_currency').attr('disabled','true')".";\n";
		echo "document.getElementById('update_id').value          		= '".$row[csf("id")]."';\n";	
		echo "set_button_status(1,'".$_SESSION['page_permission']."', 'fnc_job_order_entry',1);\n";	
	}
	exit();	
}

if ($action=="order_popup")
{	
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
	function js_set_value(id)
	{
		//alert(booking_no); 
		document.getElementById('hidd_booking_data').value=id;
		parent.emailwindow.hide();
	}

	function fnc_load_party_order_popup(company,party_name)
	{   	
		load_drop_down( 'aop_order_entry_controller', company+'_'+1+'_'+party_name, 'load_drop_down_buyer', 'buyer_td' );
		$('#cbo_party_name').attr('disabled',true);
	}
	
	function search_by(val,type)
	{
		if(type==1)
		{
			$('#txt_search_common').val('');
			if(val==1 || val==0) $('#search_td').html('W/O No');
			else if(val==2) $('#search_td').html('Job NO');
			else if(val==3) $('#search_td').html('Style Ref.');
			else if(val==4) $('#search_td').html('Buyer Po');
			else if(val==5) $('#search_td').html('Internal Ref no.');
		}
	}
</script>
</head>
<body onLoad="fnc_load_party_order_popup(<? echo $company;?>,<? echo $party_name;?>)">
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table width="750" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                <thead>
                    <tr>
                        <th colspan="7" align="center">
                            <? echo create_drop_down( "cbo_search_category", 110, $string_search_type,'', 1, "-- Search Catagory --" ); ?>
                        </th>
                    </tr>
                    <tr>                	 
                        <th width="150">Party Name</th>
                        <th width="80">Search Type</th>
                        <th width="100" id="search_td">W/O No</th>
                        <th width="60">W/O Year</th>
                        <th colspan="2" width="120">W/O Date Range</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                            <input type="hidden" id="hidd_booking_data">
                        </th>
                    </tr>
                </thead>
                <tr class="general">
                    <td id="buyer_td"><? echo create_drop_down( "cbo_party_name", 150, $blank_array,"", 1, "-- Select Buyer --" ); ?></td>
                    <td>
                        <? 
                            $searchtype_arr=array(1=>"W/O No",2=>"Buyer Job",3=>"Buyer Style Ref.",4=>"Buyer Po",5=>"Internal Ref no.");
                            echo create_drop_down( "cbo_search_type", 80, $searchtype_arr,"", 0, "", 1, "search_by(this.value,1)",0,"" );
                        ?>
                    </td>
                    <td><input name="txt_search_common" id="txt_search_common" class="text_boxes" style="width:90px"></td>
                    <td><? echo create_drop_down( "cbo_year_selection", 60, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
                    <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From" readonly></td>
                    <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To" readonly></td> 
                    <td>
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_search_category').value+'_'+<? echo $company;?>+'_'+document.getElementById('cbo_search_type').value, 'create_booking_search_list_view', 'search_div', 'aop_order_entry_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                    </td>
                </tr>
                <tr>
                    <td colspan="7"align="center" valign="middle"><?  echo load_month_buttons(); ?></td>
                </tr>
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

if($action=="create_booking_search_list_view")
{	
	// echo "<pre>";print_r($data);die;
	$data=explode('_',$data);
	$search_type=$data[7];
	
	if ($data[6]!=0) $party_cond=" and a.supplier_id='$data[6]'"; else { echo "Please Select Company First."; die; }
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Party First."; die; }
	//if ($data[0]!=0) $buyer=" and buyer_id='$data[1]'"; else  $buyer="";{ echo "Please Select Buyer First."; die; }
	if($data[4]!=0)
	{
		if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[4]"; } else if($db_type==2) { $year_cond=" and to_char(a.insert_date,'YYYY')=$data[4]"; }
	} else $year_cond="";
	$master_company=$data[6];

	$woorder_cond=""; $job_cond=""; $style_cond=""; $po_cond="";$internal_ref_no="";
	if($data[5]==1)
	{
		if ($data[1]!="")
		{
			if ($search_type==1) $woorder_cond=" and a.booking_no = '$data[1]' ";
			if ($search_type==2) $job_cond=" and a.job_no_prefix_num = '$data[1]' ";
			if ($search_type==3) $style_cond=" and a.style_ref_no = '$data[1]' ";
			if ($search_type==4) $po_cond=" and b.po_number = '$data[1]' ";
			if ($search_type==5) $internal_ref_no=" and b.grouping = '$data[1]' ";
		}
	}
	if($data[5]==2)
	{
		if ($data[1]!="")
		{
			if ($search_type==1) $woorder_cond=" and a.booking_no like '$data[1]%' ";
			if ($search_type==2) $job_cond=" and a.job_no_prefix_num like '$data[1]%' ";
			if ($search_type==3) $style_cond=" and a.style_ref_no like '$data[1]%' ";
			if ($search_type==4) $po_cond=" and b.po_number like '$data[1]%' ";
			if ($search_type==5) $internal_ref_no=" and b.grouping like '$data[1]%' ";
		}
	}
	if($data[5]==3)
	{
		if ($data[1]!="")
		{
			if ($search_type==1) $woorder_cond=" and a.booking_no like '%$data[1]' ";
			if ($search_type==2) $job_cond=" and a.job_no_prefix_num like '%$data[1]' ";
			if ($search_type==3) $style_cond=" and a.style_ref_no like '%$data[1]' ";
			if ($search_type==4) $po_cond=" and b.po_number like '%$data[1]' ";
			if ($search_type==5) $internal_ref_no=" and b.grouping like '%$data[1]' ";
		}	
	}
	if($data[5]==4 || $data[5]==0)
	{
		if ($data[1]!="")
		{
			if ($search_type==1) $woorder_cond=" and a.booking_no like '%$data[1]%' ";
			if ($search_type==2) $job_cond=" and a.job_no_prefix_num like '%$data[1]%' ";
			if ($search_type==3) $style_cond=" and a.style_ref_no like '%$data[1]%' ";
			if ($search_type==4) $po_cond=" and b.po_number like '%$data[1]%' ";
			if ($search_type==5) $internal_ref_no=" and b.grouping like '%$data[1]%' ";
		}
	}
	
	$po_ids='';
	
	if($db_type==0) $id_cond="group_concat(b.id)";
	else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	if(($job_cond!="" && $search_type==2) || ($style_cond!="" && $search_type==3)|| ($po_cond!="" && $search_type==4)|| ($internal_ref_no!="" && $search_type==5))
	{ 
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond $internal_ref_no and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0", "id");
		if ($po_ids=="")
		{
			$po_idsCond="";
			echo "Not Found."; die;
		}
	}
	
	if ($po_ids!="") $po_idsCond=" and b.po_break_down_id in ($po_ids)"; else $po_idsCond="";
	$buyer_po_arr=array();
	$po_sql ="SELECT a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number, b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond $internal_ref_no and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		$buyer_po_arr[$row[csf("id")]]['buyer']=$row[csf("buyer_name")];
		$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no")];
	}
	// echo "<pre>"; print_r($buyer_po_arr);die;
	unset($po_sql_res);
	
	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="") $booking_date = "and a.booking_date between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
		$wo_year="YEAR(a.insert_date)";
		$pre_cost_conv_cond="group_concat(b.pre_cost_fabric_cost_dtls_id)";
		$gmts_item_cond="group_concat(b.gmt_item)";
		$po_id_cond="group_concat(b.po_break_down_id)";
	}
	else if($db_type==2)
	{
		if ($data[2]!="" &&  $data[3]!="") $booking_date = "and a.booking_date between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
		$wo_year="to_char(a.insert_date,'YYYY')";
		$pre_cost_conv_cond="listagg(b.pre_cost_fabric_cost_dtls_id,',') within group (order by b.pre_cost_fabric_cost_dtls_id)";
		$gmts_item_cond="listagg(b.gmt_item,',') within group (order by b.gmt_item)";
		$po_id_cond="listagg(b.po_break_down_id,',') within group (order by b.po_break_down_id)";
	} 
	
	$buyer_arr=return_library_array( "SELECT id, buyer_name from lib_buyer where status_active =1 and is_deleted=0",'id','buyer_name');
	$comp=return_library_array( "SELECT id, company_name from lib_company where status_active =1 and is_deleted=0",'id','company_name');
	$arr=array (0=>$comp,1=>$buyer_arr);
	
	//$sql= "select $wo_year as year, a.id, a.booking_type, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id, $pre_cost_trims_cond as pre_cost_trims_id, $gmts_item_cond as gmts_item, $po_id_cond as po_id from  wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_embe_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.booking_type=3 and a.status_active=1 and c.emb_name=1 and a.lock_another_process!=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $booking_date $company $woorder_cond $year_cond $po_idsCond group by a.insert_date, a.id, a.booking_type, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id order by a.id DESC";
	
	$sql= "SELECT $wo_year as year, a.id, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id, $pre_cost_conv_cond as pre_cost_conv_id, $po_id_cond as po_id 
	from  wo_booking_mst a, wo_booking_dtls b
	where a.booking_no=b.booking_no and b.process=35 and a.booking_type=3 and a.pay_mode in (3,5) and a.lock_another_process!=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $booking_date $company $party_cond $woorder_cond $year_cond $po_idsCond group by a.insert_date, a.id, a.booking_type, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id order by a.id DESC";
	// echo $sql;
	$data_array=sql_select($sql);
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="750" >
        <thead>
            <th width="30">SL</th>
            <th width="60">W/O Year</th>
            <th width="60">W/O No</th>
            <th width="70">W/O Date</th>
            <th width="100">Buyer</th>
            <th width="150">Buyer Po</th>
            <th width="120">Buyer Style</th>
            <th>Buyer Job</th>
        </thead>
        </table>
        <div style="width:750px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="730" class="rpt_table" id="list_view">
        <tbody>
            <? 
            $i=1;
            foreach($data_array as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$expo_id=array_unique(explode(",",$row[csf('po_id')]));
				$buyer_name=""; $po_no=""; $buyer_style=""; $buyer_job="";
				foreach ($expo_id as $po_id)
				{
					if($buyer_name=="") $buyer_name=$buyer_arr[$buyer_po_arr[$po_id]['buyer']]; else $buyer_name.=','.$buyer_arr[$buyer_po_arr[$po_id]['buyer']];
					if($po_no=="") $po_no=$buyer_po_arr[$po_id]['po']; else $po_no.=','.$buyer_po_arr[$po_id]['po'];
					if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
					if($buyer_job=="") $buyer_job=$buyer_po_arr[$po_id]['job']; else $buyer_job.=','.$buyer_po_arr[$po_id]['job'];
				}
				
				$buyer_name=implode(", ",array_unique(explode(",",$buyer_name)));
				$po_no=implode(", ",array_unique(explode(",",$po_no)));
				$buyer_style=implode(", ",array_unique(explode(",",$buyer_style)));
				$buyer_job=implode(", ",array_unique(explode(",",$buyer_job)));
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$row[csf('booking_no')].'_'.$row[csf('currency_id')]; ?>")' style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="60" align="center"><? echo $row[csf('year')]; ?></td>
                    <td width="60" align="center"><? echo $row[csf('booking_no_prefix_num')]; ?></td>
                    <td width="70"><? echo change_date_format($row[csf('booking_date')]); ?></td>
                    <td width="100" style="word-break:break-all"><? echo $buyer_name; ?></td>
                    <td width="150" style="word-break:break-all"><? echo $po_no; ?></td>
                    <td width="120" style="word-break:break-all"><? echo $buyer_style; ?></td>
                    <td style="word-break:break-all"><? echo $buyer_job; ?></td>
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

if( $action=='order_dtls_list_view' ) 
{
	//echo $data;
	$data=explode('_',$data);
	$color_arr=return_library_array( "SELECT id,color_name from lib_color where status_active =1 and is_deleted=0", "id", "color_name" );
	$tblRow=1;
	
	//echo $data[5]; die;
 	if($data[2]==1) // within_group Yes
	{
		$aop_po_arr=array();
		if($data[0]==2) //Update
		{
			$sql_up = "SELECT id, order_no, booking_dtls_id, wastage, design_no, artwork_no, aop_color_id, item_color_id, print_type,billing_on,machine_id, delivery_date
			from subcon_ord_dtls
			where job_no_mst='$data[1]' and status_active=1 and is_deleted=0 order by id ASC";
			$data_arrup=sql_select($sql_up);
			foreach($data_arrup as $row)
			{

				$data[1]=$row[csf('order_no')];
				$aop_po_arr[$row[csf('booking_dtls_id')]]['id']=$row[csf('id')];
				$aop_po_arr[$row[csf('booking_dtls_id')]]['wastage']=$row[csf('wastage')];
				$aop_po_arr[$row[csf('booking_dtls_id')]]['design_no']=$row[csf('design_no')];
				$aop_po_arr[$row[csf('booking_dtls_id')]]['artwork_no']=$row[csf('artwork_no')];
				$aop_po_arr[$row[csf('booking_dtls_id')]]['aop_color_id']=$row[csf('aop_color_id')];
				$aop_po_arr[$row[csf('booking_dtls_id')]]['item_color_id']=$row[csf('item_color_id')];
				$aop_po_arr[$row[csf('booking_dtls_id')]]['print_type']=$row[csf('print_type')];
				$aop_po_arr[$row[csf('booking_dtls_id')]]['billing_on']=$row[csf('billing_on')];
				$aop_po_arr[$row[csf('booking_dtls_id')]]['delivery_date']=$row[csf('delivery_date')];
				$aop_po_arr[$row[csf('booking_dtls_id')]]['machine_id']=$row[csf('machine_id')];
			}
			/*foreach($data_arrup as $row)
			{
				$data[1]=$row[csf('order_no')];
				$aop_po_arr[$row[csf('booking_dtls_id')]]['id']=$row[csf('id')];
				$aop_po_arr[$row[csf('booking_dtls_id')]]['wastage']=$row[csf('wastage')];
			}*/
			$is_used_cond="";
			$readonly="readonly";
		}
		else $is_used_cond="and a.lock_another_process!=1";
		//print_r($aop_po_arr);
		 $sql = "SELECT a.currency_id, a.exchange_rate, b.id as booking_dtls_id, b.po_break_down_id, b.dia_width, b.gmts_color_id, b.fabric_color_id as item_color_id, b.fin_dia, b.printing_color_id as aop_color_id, b.wo_qnty, b.uom, b.rate, b.amount,b.artwork_no, d.body_part_id, d.lib_yarn_count_deter_id, d.construction, d.composition, d.gsm_weight from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fab_conv_cost_dtls c, wo_pre_cost_fabric_cost_dtls d where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and c.fabric_description=d.id and a.booking_no=trim('$data[1]') and b.process=35 and a.booking_type=3 and a.pay_mode in (3,5) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $is_used_cond order by b.id ASC";
		$readonly="readonly";
	}
	else
	{
		$sql = "SELECT id, order_no, buyer_po_id as po_break_down_id, booking_dtls_id, gmts_item_id as gmt_item, main_process_id as emb_name, embl_type as emb_type, body_part as body_part_id, order_quantity as wo_qnty, order_uom as uom, rate, amount, smv, delivery_date, wastage,artwork_no,design_no,construction, composition, gsm as gsm_weight, grey_dia, gmts_color_id, item_color_id, fin_dia as dia_width, aop_color_id, lib_yarn_deter ,buyer_po_no, buyer_style_ref, buyer_buyer, print_type,billing_on,machine_id
		from subcon_ord_dtls
		where job_no_mst='$data[1]' and mst_id='$data[3]' and status_active=1 and is_deleted=0
		order by id ASC";
		$readonly="";
	}
	//echo $sql; //die; 
	$data_array=sql_select($sql);
	
	
	
	
	
	$buyer_po_arr=array();
	
	//echo "SELECT a.style_ref_no, a.buyer_name, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and b.id($data[5])"; die;
	
	$buyer_po_sql = sql_select("SELECT a.style_ref_no, a.buyer_name, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and b.id in($data[5])");
	
	foreach($buyer_po_sql as $row)
	{
		$buyer_po_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
		$buyer_po_arr[$row[csf('id')]]['buyerpo']=$row[csf('po_number')];
		$buyer_po_arr[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
	}
	unset($buyer_po_sql);
	
	if(count($data_array) > 0)
	{
		foreach($data_array as $row)
		{	
			$dtls_id=0; $smv=0; $wastage=0;  $order_uom=0; $wo_qnty=0; $print_type_id='';$billing_on='';
			
			if($data[2]==1)
			{
				if($data[0]==2)
				{   // within group yes + Update mood
					//echo "a**";
					$dtls_id=$aop_po_arr[$row[csf('booking_dtls_id')]]['id']; 
					$wastage=$aop_po_arr[$row[csf('booking_dtls_id')]]['wastage'];
					$design_no=$aop_po_arr[$row[csf('booking_dtls_id')]]['design_no'];
					$artwork_no=$aop_po_arr[$row[csf('booking_dtls_id')]]['artwork_no'];
					$aop_color_id=$aop_po_arr[$row[csf('booking_dtls_id')]]['aop_color_id'];
					$item_color_id=$aop_po_arr[$row[csf('booking_dtls_id')]]['item_color_id'];
					$print_type_id=$aop_po_arr[$row[csf('booking_dtls_id')]]['print_type'];
					$billing_on=$aop_po_arr[$row[csf('booking_dtls_id')]]['billing_on'];
					$machine_id=$aop_po_arr[$row[csf('booking_dtls_id')]]['machine_id'];
					$deliveryDate = $aop_po_arr[$row[csf('booking_dtls_id')]]["delivery_date"];
					//$deliveryDate = change_date_format($deliveryDate);
					$wo_qnty=$row[csf('wo_qnty')];

					$buyerpo=$buyer_po_arr[$row[csf('po_break_down_id')]]['buyerpo'];
					$style=$buyer_po_arr[$row[csf('po_break_down_id')]]['style'];
					$buyer_buyer=$buyer_po_arr[$row[csf('po_break_down_id')]]['buyer_name'];
					$break_down_id=$row[csf('po_break_down_id')];
					$disabled='disabled';
					$disable_dropdown=1;

				}
				else
				{ // within group yes + insert mood
					//echo "b**";
					$wo_qnty=0;
					$wastage=$row[csf('wastage')];
					$order_uom=$row[csf('order_uom')];
					$wo_qnty=$row[csf('wo_qnty')];
					$design_no=$row[csf('design_no')];
					$artwork_no=$row[csf('artwork_no')];
					$aop_color_id=$row[csf('aop_color_id')];
					$item_color_id=$row[csf('item_color_id')];

					$buyerpo=$buyer_po_arr[$row[csf('po_break_down_id')]]['buyerpo'];
					$style=$buyer_po_arr[$row[csf('po_break_down_id')]]['style'];
					$buyer_buyer=$buyer_po_arr[$row[csf('po_break_down_id')]]['buyer_name'];
					$break_down_id=$row[csf('po_break_down_id')];
					$booking_dtls_id=$row[csf('booking_dtls_id')];
					$disabled='disabled';
					$disable_dropdown=1;

					/*$buyerpo=$row[csf('buyer_po_no')];
					$style=$row[csf('buyer_style_ref')];
					$buyer_buyer=$row[csf('buyer_buyer')];*/
					/*$aop_po_arr[$row[csf('booking_dtls_id')]]['id']=$row[csf('id')];
					$aop_po_arr[$row[csf('booking_dtls_id')]]['wastage']=$row[csf('wastage')];
					$aop_po_arr[$row[csf('booking_dtls_id')]]['design_no']=$row[csf('design_no')];
					$aop_po_arr[$row[csf('booking_dtls_id')]]['artwork_no']=$row[csf('artwork_no')];
					$aop_po_arr[$row[csf('booking_dtls_id')]]['aop_color_id']=$row[csf('aop_color_id')];
					$aop_po_arr[$row[csf('booking_dtls_id')]]['item_color_id']=$row[csf('item_color_id')];*/
				}
			}
			else if($data[2]==2)
			{
				if($data[0]==2)
				{
					// within group no + Update mood
					//echo "c**";
					$dtls_id=$row[csf('id')]; 
					$wastage=$row[csf('wastage')];
					$order_uom=$row[csf('order_uom')];
					$wo_qnty=$row[csf('wo_qnty')];
					$design_no=$row[csf('design_no')];
					$artwork_no=$row[csf('artwork_no')];
					$aop_color_id=$row[csf('aop_color_id')];
					$item_color_id=$row[csf('item_color_id')];
					$print_type_id=$row[csf('print_type')];
					$billing_on=$row[csf('billing_on')];
					$machine_id=$row[csf('machine_id')];
					$deliveryDate=$row[csf('delivery_date')];

					$buyerpo=$row[csf('buyer_po_no')];
					$style=$row[csf('buyer_style_ref')];
					$buyer_buyer=$row[csf('buyer_buyer')];
					$break_down_id=$row[csf('po_break_down_id')];

					//$disabled='disabled';
					//$disable_dropdown=1;
				}
				else
				{
					//echo "d**";
					$wo_qnty=0;
				}
			}
			$wo_qnty_cond= number_format($row[csf('wo_qnty')],4);
			if($wo_qnty_cond>0)
			{
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
				<td><input name="txtbuyerPo_<? echo $tblRow; ?>" id="txtbuyerPo_<? echo $tblRow; ?>" value="<? echo $buyerpo; ?>" class="text_boxes" type="text"  style="width:87px"  <? echo $disabled; ?> />
					<input name="txtbuyerPoId_<? echo $tblRow; ?>" id="txtbuyerPoId_<? echo $tblRow; ?>" value="<? echo $break_down_id; ?>" class="text_boxes" type="hidden" style="width:70px" />
                    <input type="hidden" name="txtSerial[]" id="txtSerial_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $tblRow; ?>" readonly/>
				</td>
				<td><input name="txtstyleRef_<? echo $tblRow; ?>" id="txtstyleRef_<? echo $tblRow; ?>" value="<? echo $style; ?>" class="text_boxes" type="text"  style="width:87px"  <? echo $disabled; ?> /></td>
				<td>
					<? 
					if($data[2]==1){
						echo create_drop_down( "txtbuyer_".$tblRow, 100, "select id, buyer_name from lib_buyer where status_active=1","id,buyer_name", 1, "-- Select --",$buyer_buyer, "",$disable_dropdown,'','','','','','',"txtbuyer[]");
						$colorPopUpYes1=$colorPopUpYes2=$colorPopUpYes3='';
					}else{ ?>
						<input id="txtbuyer_<? echo $tblRow; ?>" name="txtbuyer[]" value="<? echo $buyer_buyer; ?>" class="text_boxes" type="text"  style="width:87px"  <? echo $disabled ?>  /><?
						$colorPopUpYes1 ='onDblClick="color_select_popup('."'txtGmtsColor_',$tblRow".')"';
						$colorPopUpYes2 ='onDblClick="color_select_popup('."'txtItemColor_',$tblRow".')"';
						$colorPopUpYes3 ='onDblClick="color_select_popup('."'txtAopColor_',$tblRow".')"';
					} ?>
				</td>
				<td><? echo create_drop_down( "cboBodyPart_".$tblRow, 80, $body_part,"", 1, "--Select--",$row[csf('body_part_id')],"", 1 ); ?></td>	
                <td><input type="text" id="txtConstruction_<? echo $tblRow; ?>" name="txtConstruction_<? echo $tblRow; ?>" value="<? echo $row[csf('construction')]; ?>" class="text_boxes" style="width:77px" <? echo $readonly; ?> /></td>
                <td><input type="text" id="txtComposition_<? echo $tblRow; ?>" name="txtComposition_<? echo $tblRow; ?>" value="<? echo $row[csf('composition')]; ?>" class="text_boxes" style="width:77px" <? echo $readonly; ?> /></td>
                <td><input type="text" id="txtGsm_<? echo $tblRow; ?>" name="txtGsm_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('gsm_weight')]; ?>" style="width:47px" <? echo $readonly; ?> /></td>
                <td><input type="text" id="txtDia_<? echo $tblRow; ?>" name="txtDia_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('dia_width')]; ?>" style="width:47px" <? echo $readonly; ?> /></td>
                <td><input type="text" id="txtGmtsColor_<? echo $tblRow; ?>" name="txtGmtsColor_<? echo $tblRow; ?>" <? echo  $colorPopUpYes1.' '.$readonly; ?>  value="<? echo $color_arr[$row[csf('gmts_color_id')]]; ?>" class="text_boxes txt_color" style="width:67px;" /></td>
                <td><input type="text" id="txtItemColor_<? echo $tblRow; ?>" name="txtItemColor_<? echo $tblRow; ?>" value="<? echo $color_arr[$item_color_id]; ?>" class="text_boxes txt_color" style="width:67px" <? echo $colorPopUpYes2.' '.$readonly; ?>/></td>
                <td><input type="text" id="txtFinDia_<? echo $tblRow; ?>" name="txtFinDia_<? echo $tblRow; ?>" value="<? echo $row[csf('dia_width')]; ?>" class="text_boxes" style="width:47px" <? echo $readonly; ?> /></td>
                <td><input type="text" id="txtAopColor_<? echo $tblRow; ?>" name="txtAopColor_<? echo $tblRow; ?>" value="<? echo $color_arr[$aop_color_id]; ?>" class="text_boxes txt_color" style="width:67px" <? echo $colorPopUpYes3.' '.$readonly; ?> /></td>
                <td><input type="text" id="txtArtwork_<? echo $tblRow; ?>" name="txtArtwork_<? echo $tblRow; ?>" class="text_boxes" style="width:67px" value="<? echo $artwork_no; ?>" /></td>
                <td><? echo create_drop_down( "cboPrintType_".$tblRow, 60, $print_type,'', 1, '-Select-', $print_type_id, "copy_process_loss($tblRow)","","" ); ?></td>
                 <td id="machinetd_<? echo $tblRow; ?>"><? 
				  echo create_drop_down( "cboMachineName_".$tblRow,100, "select id, machine_no as machine_name from lib_machine_name where category_id=33 and company_id=$data[4] and status_active=1 and is_deleted=0 order by seq_no",'id,machine_name', 1, '-Select-',$machine_id,"copy_machine_name($tblRow)","","","","","","","","cboMachineName[]"); 
				 
				 ?></td>
                 <td><? echo create_drop_down( "cboBillingOn_".$tblRow, 60, $billing_on_arr,'', 1, '-Select-', $billing_on, "","","" ); ?></td>
                <td><input type="text" id="txtQty_<? echo $tblRow; ?>" name="txtQty_<? echo $tblRow; ?>" value="<? echo number_format($row[csf('wo_qnty')],4,'.',''); ?>" class="text_boxes_numeric" style="width:57px"  onkeyup="cal_amount(<? echo $tblRow; ?>);" <? echo $readonly; ?> /></td>
                <td><? echo create_drop_down( "cboUom_".$tblRow, 60, $unit_of_measurement,'', 1, '-Select-', $row[csf('uom')], "",1,"1,12,15,23,27" ); ?></td>
                <td><input type="text" id="txtRateUnit_<? echo $tblRow; ?>" name="txtRateUnit_<? echo $tblRow; ?>" value="<? echo number_format($row[csf('rate')],4,'.',''); ?>"  onkeyup="cal_amount(<? echo $tblRow; ?>);" class="text_boxes_numeric" style="width:47px" <? echo $readonly; ?> /></td>
                <td><input type="text" id="txtAmount_<? echo $tblRow; ?>" name="txtAmount_<? echo $tblRow; ?>" value="<? echo number_format($row[csf('amount')],4,'.',''); ?>" class="text_boxes_numeric" style="width:57px" readonly /></td>
                <td><input type="text" id="txtProcessLoss_<? echo $tblRow; ?>" name="txtProcessLoss_<? echo $tblRow; ?>"   value="<? echo $wastage; ?>" onKeyUp="copy_process_loss(<? echo $tblRow; ?>)" class="text_boxes_numeric" style="width:47px" />
                    <input type="hidden" name="hdnDtlsUpdateId_<? echo $tblRow; ?>" id="hdnDtlsUpdateId_<? echo $tblRow; ?>" value="<? echo $dtls_id; ?>">
                    <input type="hidden" name="hdnlibyarndetar_<? echo $tblRow; ?>" id="hdnlibyarndetar_<? echo $tblRow; ?>" value="<? echo $row[csf('lib_yarn_count_deter_id')]; ?>">
                    <input type="hidden" name="hdnbookingDtlsId_<? echo $tblRow; ?>" id="hdnbookingDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf('booking_dtls_id')]; ?>">
                </td>
                <td>
                	<input type="text" name="txtOrderDeliveryDate_<?php echo $tblRow; ?>" id="txtOrderDeliveryDate_<?php echo $tblRow; ?>" class="datepicker" style="width:67px" value="<?php echo change_date_format($deliveryDate); ?>"  onChange="chk_min_del_date(<? echo $tblRow; ?>)"  readonly/>

                </td>
                <td><input type="text" id="txtDesignNo_<? echo $tblRow; ?>" name="txtDesignNo_<? echo $tblRow; ?>" value="<? echo $design_no; ?>" class="text_boxes" style="width:57px" />
                </td>
                <td id="image_<? echo $tblRow; ?>"><input type="button" class="image_uploader" name="txtFile_<? echo $tblRow; ?>" id="txtFile_<? echo $tblRow; ?>" onClick="file_uploader ( '../../', document.getElementById('hdnDtlsUpdateId_<? echo $tblRow; ?>').value,'', 'aoporderentry_<? echo $tblRow; ?>', 0 ,1)" style="" value="ADD IMAGE"></td>
                <td width="65">
					<input type="button" id="increase_1" name="increase[]" style="width:20px" class="formbuttonplasminus" value="+" onClick="add_dtls_tr(<? echo $tblRow; ?>)" />
					<input type="button" id="decrease_1" name="decrease[]" style="width:20px" class="formbuttonplasminus" value="-" onClick="fnc_delet_dtls_tr(<? echo $tblRow; ?>);" />
				</td>
			</tr>
			<?
			$tblRow++;
			}
		}
	}
	else
	{
		?>		
		<tr>
			<td><input name="txtbuyerPo_1" id="txtbuyerPo_1" type="text" class="text_boxes" style="width:87px" placeholder="Display" readonly />
                <input name="txtbuyerPoId_1" id="txtbuyerPoId_1" type="hidden" class="text_boxes" style="width:70px" />
                 <input type="hidden" name="txtSerial[]" id="txtSerial_1" class="text_boxes" value="1" readonly/>
            </td>
            <td><input name="txtstyleRef_1" id="txtstyleRef_1" type="text" class="text_boxes" style="width:87px" placeholder="Display" readonly /></td>
            <td><? echo create_drop_down( "cboBodyPart_1", 80, $body_part,"", 1, "--Select--",0,"", 0 ); ?></td>
            <td><input type="text" id="txtConstruction_1" name="txtConstruction_1" class="text_boxes" style="width:77px" /></td>
            <td><input type="text" id="txtComposition_1" name="txtComposition_1" class="text_boxes" style="width:77px" /></td>
            <td><input type="text" id="txtGsm_1" name="txtGsm_1" class="text_boxes_numeric" style="width:47px" /></td>
            <td><input type="text" id="txtDia_1" name="txtDia_1" class="text_boxes" style="width:47px" /></td>
            <td><input type="text" id="txtGmtsColor_1" name="txtGmtsColor_1" class="text_boxes" style="width:67px" /></td>
            <td><input type="text" id="txtItemColor_1" name="txtItemColor_1" class="text_boxes" style="width:67px" /></td>
            <td><input type="text" id="txtFinDia_1" name="txtFinDia_1" class="text_boxes" style="width:47px" /></td>
            <td><input type="text" id="txtAopColor_1" name="txtAopColor_1" class="text_boxes" style="width:67px" /></td>
            <td><input type="text" id="txtArtwork_1" name="txtArtwork_1" class="text_boxes" style="width:67px" /></td>
            <td><? echo create_drop_down( "cboPrintType_1", 60, $print_type,'', 1, '-Select-', $selected, "","","" ); ?></td>
             <td id="machinetd_1"><?  echo create_drop_down( "cboMachineName_1",100, $blank_array,'', 1, '-Select-',0,"","","","","","","","","cboMachineName[]");  ?></td>
            <td><? echo create_drop_down( "cboBillingOn_1", 60, $billing_on_arr,'', 1, '-Select-', $selected, "","","" ); ?></td>
            <td><input type="text" id="txtQty_1" name="txtQty_1" class="text_boxes_numeric" style="width:57px" /></td>
            <td><? echo create_drop_down( "cboUom_1", 60, $unit_of_measurement,'', 1, '-Select-', $uom, "","","1,12,15,23,27" ); ?></td>
            <td><input type="text" id="txtRateUnit_1" name="txtRateUnit_1" class="text_boxes_numeric" style="width:47px" /></td>
            <td><input type="text" id="txtAmount_1" name="txtAmount_1" class="text_boxes_numeric" style="width:57px" /></td>
            <td><input type="text" id="txtProcessLoss_1" name="txtProcessLoss_1"  onKeyUp="copy_process_loss(1)" class="text_boxes_numeric" style="width:47px" />
                <input type="hidden" name="hdnDtlsUpdateId_1" id="hdnDtlsUpdateId_1">
                <input type="hidden" name="hdnlibyarndetar_1" id="hdnlibyarndetar_1">
                <input type="hidden" name="hdnbookingDtlsId_1" id="hdnbookingDtlsId_1">
            </td>
            <td>
            	<input type="text" name="txtOrderDeliveryDate_1" id="txtOrderDeliveryDate_1" class="datepicker" style="width:67px" onChange="chk_min_del_date(1)"  readonly/>
            </td>
            <td><input type="text" id="txtDesignNo_1" name="txtDesignNo_1" class="text_boxes" style="width:57px" />
        	<td width="65">
				<input type="button" id="increase_1" name="increase[]" style="width:20px" class="formbuttonplasminus" value="+" onClick="add_dtls_tr(1)" />
				<input type="button" id="decrease_1" name="decrease[]" style="width:20px" class="formbuttonplasminus" value="-" onClick="fnc_delet_dtls_tr(1);" />
			</td>
		</tr>
		<?
	}
	exit();
}

if($action=="fabric_description_popup")
{
	echo load_html_head_contents("Fabric Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	<script>
		function js_set_value(data)
		{
			var data=data.split('_');
			document.getElementById('construction').value=trim(data[1]);
			document.getElementById('fab_gsm').value=trim(data[2]);
			document.getElementById('composition').value=trim(data[3]);
			parent.emailwindow.hide();
		}
		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			document.getElementById(x).style.backgroundColor = ( newColor == document.getElementById(x).style.backgroundColor )? origColor : newColor;
		}
	</script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                <thead>
                    <tr>
                    	<th colspan="3" align="center"><? echo create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                    </tr>
                    <tr>
                        <th>Construction</th>
                        <th>GSM/Weight</th>
                        <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td align="center"><input type="text" style="width:130px" class="text_boxes" name="txt_construction" id="txt_construction" /></td>
                        <td align="center">	<input type="text" style="width:130px" class="text_boxes" name="txt_gsm_weight" id="txt_gsm_weight" /></td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_construction').value+'_'+document.getElementById('txt_gsm_weight').value+'_'+document.getElementById('cbo_string_search_type').value, 'fabric_description_popup_search_list_view', 'search_div', 'aop_order_entry_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                        </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:10px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}


if($action=="fabric_description_popup_search_list_view")
{
	//echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//list($fabric_nature,$libyarncountdeterminationid,$construction,$gsm_weight,$string_search_type)=explode('**',$data);
	$data=explode('_',$data);
	$construction=$data[0];
	$gsm_weight=$data[1];
	$string_search_type=$data[2];
	$lib_yarn_count=return_library_array( "SELECT yarn_count,id from lib_yarn_count where status_active =1 and is_deleted=0", "id", "yarn_count"  );
	$search_con='';
	if($string_search_type==1)
	{
		if($construction!='') {$search_con .= " and a.construction='".trim($construction)."'";}
		if($gsm_weight!='') {$search_con .= " and a.gsm_weight='".trim($gsm_weight)."'";}
	}
	else if($string_search_type==2)
	{
		if($construction!='') {$search_con .= " and a.construction like ('".trim($construction)."%')";}
		if($gsm_weight!='') {$search_con .= " and a.gsm_weight like ('".trim($gsm_weight)."%')";}
	}
	else if($string_search_type==3)
	{
		if($construction!='') {$search_con .= " and a.construction like ('%".trim($construction)."')";}
		if($gsm_weight!='') {$search_con .= " and a.gsm_weight like ('%".trim($gsm_weight)."')";}
	}
	else if($string_search_type==4 || $string_search_type==0)
	{
		if($construction!='') {$search_con .= " and a.construction like ('%".trim($construction)."%')";}
		if($gsm_weight!='') {$search_con .= " and a.gsm_weight like ('%".trim($gsm_weight)."%')";}
	}

	//if($construction!='') {$search_con = " and a.construction like ('%".trim($construction)."%')";}
	//if($gsm_weight!='') {$search_con  .= " and a.gsm_weight like ('%".trim($gsm_weight)."%')";}
?>
<script>
</script>
</head>
<body>
    <div align="center">
        <form>
            <input type="hidden" id="construction" name="construction" />
            <input type="hidden" id="composition" name="composition" />
            <input type="hidden" id="fab_gsm" name="fab_gsm" />
        </form>
	<?
	$lib_yarn_count=return_library_array( "SELECT yarn_count,id from lib_yarn_count where status_active =1 and is_deleted=0", "id", "yarn_count");
	$lib_composition_name=return_library_array( "SELECT composition_name,id from lib_composition_array where status_active =1 and is_deleted=0", "id", "composition_name");
	?>
    <table class="rpt_table" width="550" cellspacing="0" cellpadding="0" border="0" rules="all">
        <thead>
            <tr>
                <th width="50">SL No</th>
                <th width="100">Construction</th>
                <th width="100">GSM/Weight</th>
                <th>Composition</th>
            </tr>
       </thead>
   </table>
   <div id="" style="max-height:300px; width:548px; overflow-y:scroll">
   <table id="list_view" class="rpt_table" width="530" height="" cellspacing="0" cellpadding="0" border="1" rules="all">
        <tbody>
			<?
			$sql_data=sql_select("SELECT a.construction,a.gsm_weight,a.id,b.copmposition_id,b.percent,b.count_id,b.type_id from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_con group by a.id,a.construction,a.gsm_weight,b.copmposition_id,b.percent,b.count_id,b.type_id order by a.id");
			$i=1; $data_arr=array();
			foreach ($sql_data as $row)
			{
				$data_arr[$row[csf("id")]][$row[csf("construction")]][$row[csf("gsm_weight")]]['composition_info'].=$lib_composition_name[$row[csf('copmposition_id')]].' '.$row[csf('percent')].'% '.$lib_yarn_count[$row[csf('count_id')]].' '.$yarn_type[$row[csf('type_id')]].' - ';
				$data_arr[$row[csf("id")]][$row[csf("construction")]][$row[csf("gsm_weight")]]['construction']=$row[csf('construction')];
				$data_arr[$row[csf("id")]][$row[csf("construction")]][$row[csf("gsm_weight")]]['gsm_weight']=$row[csf('gsm_weight')];
				
			}
			
			foreach($data_arr as $id=> $infos)
			{
				foreach($infos as $constructionId=> $con_infos)
				{
					foreach($con_infos as $gsmId=> $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$composition_info=chop($data_arr[$id][$constructionId][$gsmId]['composition_info'],' - ');
						?>
				        <tr id="tr_<? echo $id ?>" bgcolor="<? echo $bgcolor; ?>" height="20" style="cursor:pointer; word-break:break-all;" onClick="js_set_value('<? echo $id."_".$constructionId."_".$gsmId."_".$composition_info ?>')">
				            <td width="50"><? echo $i; ?></td>
				            <td width="100" align="left"><? echo $row[('construction')]; ?></td>
				            <td width="100" align="center"><? echo $row[('gsm_weight')]; ?></td>
				            <td><? echo $composition_info; ?></td>
				        </tr>
						<?
				        $i++;
					}
				}
		    }
		    ?>
        </tbody>
    </table>
</div>
</div>
</body>
</html>
<?
exit();
}

if($action=="color_popup")
{
	echo load_html_head_contents("Color Selection Pop-up","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(data)
		{
			document.getElementById('color_name').value=data;
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
        <div align="center">
        <form>
            <input type="hidden" id="color_name" name="color_name" />
            <?
            	$sql="select id, color_name FROM lib_color  WHERE status_active=1 and is_deleted=0";
           	 	echo  create_list_view("list_view", "Color Name", "160","410","420",0, $sql , "js_set_value", "color_name", "", 1, "0", $arr , "color_name", "",'setFilterGrid("list_view",-1);','0') ;
            ?>
        </form>
        </div>
	</body>
	</html>
	<?
	exit();
}

if($action=="file_upload")
{
	header("Content-Type: application/json");
	$filename = time().$_FILES['file']['name']; 
	$array = explode('.', $_FILES['file']['name']);
	$extension = end($array);
	$image_extension_list = array("PNG","JPEG","JPG","GIF","bmp");
	$file_type = 2;
	if(in_array(strtoupper($extension), $image_extension_list))
	{
		$file_type = 1;
	}
	$location = "../../../file_upload/".$filename; 
	$uploadOk = 1;
	if(empty($mst_id))
	{
		$mst_id=$_GET['mst_id'];
	} 
	if(move_uploaded_file($_FILES['file']['tmp_name'], $location))
	{ 
		 $uploadOk = 1;
	}
	else
	{ 
		$uploadOk=0; 
	} 
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}

	$id=return_next_id( "id","COMMON_PHOTO_LIBRARY", 1 ) ;
	$data_array .="(".$id.",".$mst_id.",'aop_order_entry','file_upload/".$filename."','".$file_type."','".$filename."')";
	$field_array="id,master_tble_id,form_name,image_location,file_type,real_file_name";
	$rID=sql_insert("COMMON_PHOTO_LIBRARY",$field_array,$data_array,1);

	if($db_type==0)
	{
		if($rID==1 && $uploadOk==1)
		{
			mysql_query("COMMIT");
			echo "0**".$new_system_id[0]."**".$mst_id;
		}
		else
		{
			mysql_query("ROLLBACK");
			echo "10**".$mst_id;
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($rID==1 && $uploadOk==1)
		{
			oci_commit($con);
			echo "0**".$new_system_id[0]."**".$mst_id;
		}
		else
		{
			oci_rollback($con);
			echo "10**".$rID."**".$uploadOk."**INSERT INTO COMMON_PHOTO_LIBRARY(".$field_array.") VALUES ".$data_array;
		}
	}
	disconnect($con);
	die;
}

?>