<?
include('../../../includes/common.php'); 
session_start();
  
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

if ($action=="load_drop_down_location")
{
	$data=explode("_",$data);
	if($data[1]==1) $dropdown_name="cbo_location_name";
	else $dropdown_name="cbo_party_location";
	$location_arr=return_library_array( "select id, location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name",'id','location_name');
	if(count($location_arr)==1) $selected = key($location_arr); else $selected=0;
	echo create_drop_down( $dropdown_name, 150, $location_arr,"", 1, "-- Select Location --", $selected, "" );
	exit();
}

if($action=="load_drop_down_embl_type")
{
	$data=explode('_',$data);
	
	if($data[0]==1) $emb_type=$emblishment_print_type;
	else if($data[0]==2) $emb_type=$emblishment_embroy_type;
	else if($data[0]==3) $emb_type=$emblishment_wash_type;
	else if($data[0]==4) $emb_type=$emblishment_spwork_type;
	else if($data[0]==5) $emb_type=$emblishment_gmts_type;
	
	echo create_drop_down( "cboembtype_".$data[1], 80,$emb_type,"", 1, "-- Select --", "", "","","" ); 
	
	exit();
}

if ($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);

	if($data[1]==1) $load_function="fnc_load_party(2,document.getElementById('cbo_within_group').value)";
	else $load_function="";
	
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $data[2], "$load_function");
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[2], "" );
	}	
	exit();	 
} 

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	/*echo '<pre>';
	print_r($cbo_company_name);die;*/
	$user_id=$_SESSION['logic_erp']['user_id'];
	
	if ($operation==0) // Insert Start Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if($db_type==0) $insert_date_con="and YEAR(insert_date)=".date('Y',time())."";
		else if($db_type==2) $insert_date_con="and TO_CHAR(insert_date,'YYYY')=".date('Y',time())."";
		$section_prefix = 'JCP-'.(($cbo_section == 1) ? "ELC" :
			(($cbo_section ==2) ? "GT" :
			(($cbo_section == 3) ? "LB" :
			(($cbo_section == 4) ? "OP" :
			(($cbo_section == 5) ? "PB" :
			(($cbo_section == 6) ? "SP" :
			(($cbo_section == 7) ? "ST" :
			(($cbo_section == 8) ? "TT" :
			(($cbo_section == 9) ? "YD" :
			(($cbo_section == 10) ? "AOP" :
			(($cbo_section == 11) ? "EMB" :
			(($cbo_section == 13) ? "HAN" :
			(($cbo_section == 14) ? "CTN" :
			(($cbo_section == 15) ? "TWI" :
			(($cbo_section == 16) ? "DOU" :
			(($cbo_section == 17) ? "PT" :
			(($cbo_section == 18) ? "PAP" :
			(($cbo_section == 19) ? "TIP" :
			(($cbo_section == 20) ? "DC" :
			(($cbo_section == 21) ? "BT" :
			(($cbo_section == 22) ? "FAB" :
			(($cbo_section == 23) ? "TAP" :
			(($cbo_section == 24) ? "OTH" :
			(($cbo_section == 25) ? "HTP" :
			(($cbo_section == 26) ? "WOV" :
			(($cbo_section == 27) ? "CAR" :
			(($cbo_section == 29) ? "OFF" :
			(($cbo_section == 30) ? "MOB" :
			(($cbo_section == 31) ? "DTP" :  "N/A")))))))))))))))))))))))))))));
		//01.Twisting- TWI.02.Doubling- DOU.03.Price Ticket-PT.04.Paper-PAP.05.Tipping-TIP.06.Dye.Cut-DC.07.Hanger-HAN.08.Carton-CTN

		//$trims_section = array(1 => "Elastic", 2 => "Gum Tape", 3 => "Label", 4 => "Offset Print", 5 => "Poly", 6 => "Screen Print", 7 => "Sewing Thread", 8 => "Twill Tape", 9 => "Drawstring", 10 => "Yarn Dyeing", 11=> "All Over Print", 12 => "Embroidery", 13 => "Hanger", 14 => "Carton", 15 => "Twisting", 16 => "Doubling", 17 => "Price Ticket", 18 => "Paper", 19 => "Tipping", 20 => "Dye Cut");
		
		$new_job_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '',$section_prefix, date("Y",time()), 5, "select job_no_prefix,job_no_prefix_num from trims_job_card_mst where entry_form=257 and company_id=$cbo_company_name $insert_date_con and section_id=$cbo_section order by id desc ", "job_no_prefix", "job_no_prefix_num" ));
		
		if($db_type==0)
		{
			$txt_delivery_date=change_date_format(str_replace("'",'',$txt_delivery_date),'yyyy-mm-dd');
		}
		else
		{
			$txt_delivery_date=change_date_format(str_replace("'",'',$txt_delivery_date), "", "",1);
		}

		$color_library_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");

		$size_library_arr=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
		$id=return_next_id("id","trims_job_card_mst",1);
		$id1=return_next_id( "id", "trims_job_card_dtls",1) ;
		$id3=return_next_id( "id", "trims_job_card_breakdown", 1 ) ;
		$rID3=true;
		$field_array="id, entry_form, trims_job, job_no_prefix, job_no_prefix_num,  company_id, location_id, within_group, party_id,  party_location , delivery_date, order_id, order_no, order_qty, received_no, received_id, section_id, inserted_by, insert_date";
		//echo "10**".$new_job_no[0]; die;
		$data_array="(".$id.", 257, '".$new_job_no[0]."', '".$new_job_no[1]."', '".$new_job_no[2]."', '".$cbo_company_name."', '".$cbo_location_name."', '".$cbo_within_group."', '".$cbo_party_name."', '".$cbo_party_location."', '".$txt_delivery_date."', '".$hid_order_id."', '".$txt_order_no."', '".$txt_order_qty."', '".$txt_recv_no."', '".$hid_recv_id."', '".$cbo_section."', ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."')";
		
		$txt_job_no=$new_job_no[0];
		$field_array2="id, mst_id, job_no_mst, receive_dtls_id, booking_dtls_id, book_con_dtls_id, break_id, buyer_po_id,buyer_po_no, buyer_style_ref, item_description, gmts_color_id, gmts_size_id, color_id, size_id, sub_section, uom, job_quantity,  impression, material_color, conv_factor, is_copy_material, item_group_id, inserted_by, insert_date";
		$field_array3="id, mst_id, order_id, job_no_mst, book_con_dtls_id, product_id, description, specification, unit, pcs_unit, cons_qty, process_loss, process_loss_qty, req_qty, remarks, lot, inserted_by, insert_date";
		//echo "10**".$total_row; die;
		$data_array2= $data_array3="";  $add_commaa=0; $add_commaa_dtls=0;  $add_commadtls=0; $new_array_color=array();  $new_array_size=array();
		for($i=1; $i<=$total_row; $i++)
		{
			$txtbuyerPoId			= "txtbuyerPoId_".$i; 
			$txtbuyerPo				= "txtbuyerPo_".$i;
			$txtstyleRef			= "txtstyleRef_".$i;
			$cboItemGroup			= "cboItemGroup_".$i;
			$txtdescription			= "txtdescription_".$i;
			$txtgmtscolor			= "txtgmtscolor_".$i;
			$txtgmtscolorID			= "txtgmtscolorID_".$i;
			$txtgmtssize			= "txtgmtssize_".$i;
			$txtgmtssizeID			= "txtgmtssizeID_".$i;
			$txtcolor				= "txtcolor_".$i;
			$txtcolorID				= "txtcolorID_".$i;
			$txtsize				= "txtsize_".$i;
			$txtsizeID				= "txtsizeID_".$i;
			$cboSubSection			= "cboSubSection_".$i;
			$cboUom					= "cboUom_".$i;
			$txtJobQuantity			= "txtJobQuantity_".$i;
			$txtRawMat 				= "txtRawMat_".$i;
			$txtImpression 			= "txtImpression_".$i;
			$txtRawcolor 			= "txtRawcolor_".$i;
			$hdnDtlsdata 			= "hdnDtlsdata_".$i;
			$txtCopyChk 			= "txtCopyChk_".$i;
			$hdnbookingDtlsId 		= "hdnbookingDtlsId_".$i;
			$bookConDtlsId 			= "bookConDtlsId_".$i;
			$hdnDtlsIDs 			= "hdnRecDtlsIDs_".$i;
			$hdnBreakIDs 			= "hdnBreakIDs_".$i;
			$txtConvFactor 			= "txtConvFactor_".$i;
			//echo "10**".$$cboSubSection; die;
			if(str_replace("'",'',$$txtCopyChk)=='') $copyChk=0; else $copyChk=$$txtCopyChk;
			$rawcolor=''; $txtRawcolor=explode("__",str_replace("'",'',$$txtRawcolor));
			for($j=0; $j<count($txtRawcolor); $j++)
			{
				if (!in_array(str_replace("'","",trim($txtRawcolor[$j])),$new_array_color))
				{
					$color_id_raw = return_id( $txtRawcolor[$j], $color_library_arr, "lib_color", "id,color_name","257");  
					//echo $$txtColorName.'='.$color_id.'<br>';
					$new_array_color[$color_id_raw]=str_replace("'","",$txtRawcolor[$j]);
				}
				else $color_id_raw =  array_search(str_replace("'","",$txtRawcolor[$j]), $new_array_color); 
				$rawcolor.=$color_id_raw."__";

			}
			//echo  "10**".$rawcolor."**";
			$rawcolor=chop($rawcolor,"__");
			if(str_replace("'","",$$txtcolorID)=="")
			{
				if (str_replace("'", "", trim($$txtcolor)) != "") {
					if (!in_array(str_replace("'", "", trim($$txtcolor)),$new_array_color)){
						$color_id = return_id( str_replace("'", "", trim($$txtcolor)), $color_library_arr, "lib_color", "id,color_name","257");
						$new_array_color[$color_id]=str_replace("'", "", trim($$txtcolor));
					}
					else $color_id =  array_search(str_replace("'", "", trim($$txtcolor)), $new_array_color);
				} else $color_id = 0;
			}
			else
			{
				$color_id=str_replace("'","",$$txtcolorID);
			}
			
			if(str_replace("'","",trim($$txtsizeID))=="")
			{
				if(str_replace("'","",trim($$txtsize))!="")
				{ 
					if (!in_array(str_replace("'","",trim($$txtsize)),$new_array_size))
					{
						$size_id = return_id( str_replace("'","",trim($$txtsize)), $size_library_arr, "lib_size", "id,size_name","257");  
						$new_array_size[$size_id]=str_replace("'","",trim($$txtsize));
					}
					else $size_id =  array_search(str_replace("'","",trim($$txtsize)), $new_array_size); 
				}
				else
				{
					$size_id=0;
				}
			}
			else
			{
				$size_id=str_replace("'","",trim($$txtsizeID));
			}

			if(str_replace("'",'',$$txtbuyerPoId)=="") $txtbuyerPoId=0; else $txtbuyerPoId=str_replace("'",'',$$txtbuyerPoId);
			if ($add_commaa!=0) $data_array2 .=","; $add_comma=0;
			
			$data_array2 .="(".$id1.",".$id.",'".$new_job_no[0]."',".$$hdnDtlsIDs.",".$$hdnbookingDtlsId.",".$$bookConDtlsId.",".$$hdnBreakIDs.",'".$txtbuyerPoId."',".$$txtbuyerPo.",".$$txtstyleRef.",".$$txtdescription.",".$$txtgmtscolorID.",".$$txtgmtssizeID.",'".$color_id."','".$size_id."',".$$cboSubSection.",".$$cboUom.",".str_replace(",",'',$$txtJobQuantity).",".$$txtImpression.",'".$rawcolor."',".$$txtConvFactor.",".$copyChk.",".$$cboItemGroup.",'".$user_id."','".$pc_date_time."')";
			
			$dtls_data=explode("**",str_replace("'",'',$$hdnDtlsdata));
			/*echo "10**".$total_row; 
			print_r($dtls_data);
			die;*/
			for($j=0; $j<count($dtls_data); $j++)
			{
				$exdata=explode("_",$dtls_data[$j]);
				$description="'".$exdata[0]."'";
				$specification="'".$exdata[1]."'";
				$unit="'".$exdata[2]."'";
				$unit_pcs="'".str_replace(",",'',$exdata[3])."'";
				$cons_qty="'".str_replace(",",'',$exdata[4])."'";
				$process_loss="'".str_replace(",",'',$exdata[5])."'";
				$process_loss_qty="'".$exdata[6]."'";
				$req_qty="'".str_replace(",",'',$exdata[7])."'";
				$remarks="'".$exdata[8]."'";
				$product_id="'".$exdata[10]."'";
				$product_lot="'".$exdata[11]."'";
				
				if ($add_commadtls!=0) $data_array3 .=",";
				$data_array3.="(".$id3.",".$id1.",'".$hid_order_id."','".$new_job_no[0]."',".$$bookConDtlsId.",".$product_id.",".$description.",".$specification.",".$unit.",".$unit_pcs.",".$cons_qty.",".$process_loss.",".$process_loss_qty.",".$req_qty.",".$remarks.",".$product_lot.", ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."')";
				$id3=$id3+1; $add_commadtls++;   
			}
			
			$id1=$id1+1; $add_commaa++; $add_commaa_dtls++;
			//echo "10**INSERT INTO subcon_ord_breakdown (".$field_array3.") VALUES ".$data_array3; die;			
		}
		//echo "10**INSERT INTO trims_job_card_mst (".$field_array.") VALUES ".$data_array; die;
		//echo "10**INSERT INTO trims_job_card_dtls (".$field_array2.") VALUES ".$data_array2; die;
		//echo "10**INSERT INTO trims_job_card_breakdown (".$field_array3.") VALUES ".$data_array3; die;
		$flag=1;
		$rID=sql_insert("trims_job_card_mst",$field_array,$data_array,1);
		if($rID==1) $flag=1; else $flag=0;
		$rID2=sql_insert("trims_job_card_dtls",$field_array2,$data_array2,1);
		if($rID2==1) $flag=1; else $flag=0;
		if($data_array3!="" && $flag==1)
		{
			$rID3=sql_insert("trims_job_card_breakdown",$field_array3,$data_array3,1);
			if($rID3==1) $flag=1; else $flag=0;
		}
		//echo "10**".$rID."**".$rID2."**".$rID3."mmm"; die;
		if(str_replace("'","",$cbo_within_group)==1)
		{
			if($flag==1)
			{
				$rIDBooking=execute_query( "update wo_booking_mst set lock_another_process=1 where booking_no ='".$txt_order_no."'",1);
				if($rIDBooking==1) $flag=1; else $flag=0;
			}
		}
	
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
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$req_id=return_field_value("id","trims_raw_mat_requisition_mst","job_id=$update_id and status_active=1 and is_deleted=0","id");
		if($req_id !='' && $req_id !=0 ){
	    	$sqlBreak_result =sql_select("select  a.id, a.mst_id, a.order_id, a.job_no_mst, a.book_con_dtls_id, a.product_id, a.description, a.specification, a.pcs_unit, a.cons_qty, a.process_loss, a.process_loss_qty, a.req_qty, a.remarks,b.unit_of_measure as unit from trims_job_card_breakdown a , product_details_master b where a.product_id=b.id and b.entry_form in (334,220,300,285,0) and a.status_active=1 and a.is_deleted=0 and a.job_no_mst ='".$txt_job_no."'");
	    	foreach($sqlBreak_result as $row)
			{
				$prev_item_arr[$row[csf('id')]]['product_id']=$row[csf('product_id')];
				$prev_item_arr[$row[csf('id')]]['pcs_unit']=$row[csf('pcs_unit')];
				$prev_item_arr[$row[csf('id')]]['cons_qty']=$row[csf('cons_qty')];
				$prev_item_arr[$row[csf('id')]]['process_loss']=$row[csf('process_loss')];
				$prev_item_arr[$row[csf('id')]]['process_loss_qty']=$row[csf('process_loss_qty')];
				$prev_item_arr[$row[csf('id')]]['req_qty']=$row[csf('req_qty')];
				$all_req_prod_arr[]=$row[csf('product_id')];
				//$all_req_job_break_arr[]=$row[csf('product_id')];
			}

			$requisitiondataSql = sql_select("select b.product_id, b.requisition_qty as total_requisition_qty, b.trim_break_id   from trims_raw_mat_requisition_mst a, trims_raw_mat_requisition_dtls b
			where a.id=b.mst_id and a.job_id='$update_id' and a.status_active=1 and b.status_active=1 ");
			$requisition_data_arr = array(); 
			foreach ($requisitiondataSql as $row) 
			{
				$requisition_data_arr[$row[csf('product_id')]]['requisition_qty'] += $row[csf('total_requisition_qty')];
				$requisition_job_break_ids .= $row[csf('trim_break_id')].',';
				//$updaterequisitionQtyArr[$row[csf('id')]]['requisition_qty'] += $row[csf('total_requisition_qty')];
		 	}

	    }


		$color_library_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");

		//$size_library_arr=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");

		if($db_type==0){
			$txt_delivery_date=change_date_format(str_replace("'",'',$txt_delivery_date),'yyyy-mm-dd');
		}else{
			$txt_delivery_date=change_date_format(str_replace("'",'',$txt_delivery_date), "", "",1);
		}
		$field_array="delivery_date*order_id*order_no*order_qty*received_no*received_id*updated_by*update_date";		
		$data_array="'".$txt_delivery_date."'*'".$hid_order_id."'*'".$txt_order_no."'*'".$txt_order_qty."'*'".$txt_recv_no."'*'".$hid_recv_id."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$field_array2="buyer_po_id*buyer_po_no*buyer_style_ref*item_description*gmts_color_id*gmts_size_id*color_id*size_id*sub_section*uom*job_quantity*impression*material_color*conv_factor*is_copy_material*updated_by*update_date";

		$field_array_status="updated_by*update_date*status_active*is_deleted";
		$data_array_status=$user_id."*'".$pc_date_time."'*0*1";

		//$field_array5="id, mst_id, order_id, job_no_mst, product_id, description, specification, unit, pcs_unit, cons_qty, process_loss, process_loss_qty, req_qty, remarks, inserted_by, insert_date";
		$field_array5="id, mst_id, order_id, job_no_mst, book_con_dtls_id, product_id, description, specification, unit, pcs_unit, cons_qty, process_loss, process_loss_qty, req_qty, remarks, lot, inserted_by, insert_date";

		$field_array7="product_id*description*specification*unit*pcs_unit*cons_qty*process_loss*process_loss_qty*req_qty*remarks*status_active*is_deleted*updated_by*update_date";

		$field_array6="id, mst_id, job_no_mst, receive_dtls_id, booking_dtls_id, book_con_dtls_id, break_id, buyer_po_id,buyer_po_no, buyer_style_ref, item_description, gmts_color_id, gmts_size_id, color_id, size_id, sub_section, uom, job_quantity,  impression, material_color, conv_factor, is_copy_material, inserted_by, insert_date";
		//$field_array7="id, mst_id, order_id, job_no_mst, book_con_dtls_id, product_id, description, specification, unit, pcs_unit, cons_qty, process_loss, process_loss_qty, req_qty, remarks";
		//echo "10**delete from trims_job_card_breakdown where job_no_mst in ='$txt_job_no'"; die;
		
		$id1=return_next_id( "id", "trims_job_card_dtls",1) ;
		//echo "10**".$id1; die;
		$id3=return_next_id( "id", "trims_job_card_breakdown",1) ;

		$add_comma=0;	$flag=""; $new_array_color=array();  $new_array_size=array();
		for($i=1; $i<=$total_row; $i++)
		{			
			$txtbuyerPoId			= "txtbuyerPoId_".$i; 
			$txtbuyerPo				= "txtbuyerPo_".$i;
			$txtstyleRef			= "txtstyleRef_".$i;
			$cboItemGroup			= "cboItemGroup_".$i;
			$txtdescription			= "txtdescription_".$i;
			$txtgmtscolor			= "txtgmtscolor_".$i;
			$txtgmtscolorID			= "txtgmtscolorID_".$i;
			$txtgmtssize			= "txtgmtssize_".$i;
			$txtgmtssizeID			= "txtgmtssizeID_".$i;
			$txtcolor				= "txtcolor_".$i;
			$txtcolorID				= "txtcolorID_".$i;
			$txtsize				= "txtsize_".$i;
			$txtsizeID				= "txtsizeID_".$i;
			$cboSubSection			= "cboSubSection_".$i;
			$cboUom					= "cboUom_".$i;
			$txtJobQuantity			= "txtJobQuantity_".$i;
			$txtRawMat 				= "txtRawMat_".$i;
			$txtImpression 			= "txtImpression_".$i;
			$txtRawcolor 			= "txtRawcolor_".$i;			
			$hdnDtlsdata 			= "hdnDtlsdata_".$i;
			$txtCopyChk 			= "txtCopyChk_".$i;
			$hdnbookingDtlsId 		= "hdnbookingDtlsId_".$i;
			$bookConDtlsId 			= "bookConDtlsId_".$i;
			$hdnDtlsIDs 			= "hdnRecDtlsIDs_".$i;
			$hdnBreakIDs 			= "hdnBreakIDs_".$i;
			$txtConvFactor 			= "txtConvFactor_".$i;
			$hdnDtlsUpdateId 		= "hdnDtlsUpdateId_".$i;
			$hdnBrkDelId 	 		= "hdnBrkDelId_".$i;
			//$txtCopyChk 	 		= "txtCopyChk_".$i;

			$aa	=str_replace("'",'',$$hdnDtlsUpdateId);
			
			if(str_replace("'",'',$$txtCopyChk)=='') $copyChk=0; else $copyChk=str_replace("'",'',$$txtCopyChk);
			$rawcolor=''; $rawcolor=''; $txtRawcolor=explode("__",str_replace("'",'',$$txtRawcolor));
			for($j=0; $j<count($txtRawcolor); $j++)
			{
				/*if (!in_array($txtRawcolor[$j],$new_array_color))
				{
					$color_id_raw = return_id( $txtRawcolor[$j], $color_library_arr, "lib_color", "id,color_name","257");  
					$new_array_color[$color_id_raw]=str_replace("'","",$txtRawcolor[$j]);
				}
				else $color_id_raw =  array_search(str_replace("'","",$txtRawcolor[$j]), $new_array_color); 
				$rawcolor.=$color_id_raw."__";*/
				if (!in_array(str_replace("'","",trim($txtRawcolor[$j])),$new_array_color))
				{
					$color_id_raw = return_id( $txtRawcolor[$j], $color_library_arr, "lib_color", "id,color_name","257");  
					//echo $$txtColorName.'='.$color_id.'<br>';
					$new_array_color[$color_id_raw]=str_replace("'","",$txtRawcolor[$j]);
				}
				else $color_id_raw =  array_search(str_replace("'","",$txtRawcolor[$j]), $new_array_color); 
				$rawcolor.=$color_id_raw."__";

			}
			$rawcolor=chop($rawcolor,"__");

			if(str_replace("'",'',$$txtbuyerPoId)=="") $txtbuyerPoId=0; else $txtbuyerPoId=str_replace("'",'',$$txtbuyerPoId);
			if(str_replace("'",'',$$hdnDtlsUpdateId)!="")
			{
				$data_array2[$aa]=explode("*",("'".$$txtbuyerPoId."'*".$$txtbuyerPo."*".$$txtstyleRef."*".$$txtdescription."*".$$txtgmtscolorID."*".$$txtgmtssizeID."*".$$txtcolorID."*".$$txtsizeID."*".$$cboSubSection."*".$$cboUom."*".str_replace(",",'',$$txtJobQuantity)."*".$$txtImpression."*'".$rawcolor."'*".$$txtConvFactor."*".$copyChk."*".$user_id."*'".$pc_date_time."'"));
				$hdn_dtls_id_arr[]=str_replace("'",'',$$hdnDtlsUpdateId);
			}
			else
			{
				if ($add_commaa_dtls!=0) $data_array6 .=","; $add_commaa_dtls=0;
				$data_array6 .="(".$id1.",".$update_id.",'".$txt_job_no."',".$$hdnDtlsIDs.",".$$hdnbookingDtlsId.",".$$bookConDtlsId.",".$$hdnBreakIDs.",'".$txtbuyerPoId."',".$$txtbuyerPo.",".$$txtstyleRef.",".$$txtdescription.",".$$txtgmtscolorID.",".$$txtgmtssizeID.",".$$txtcolorID.",".$$txtsizeID.",".$$cboSubSection.",".$$cboUom.",".str_replace(",",'',$$txtJobQuantity).",".$$txtImpression.",'".$rawcolor."',".$$txtConvFactor.",".$copyChk.",'".$user_id."','".$pc_date_time."')";
				$add_commaa_dtls++;
			}
			//echo "10**".$$hdnDtlsdata."#";
			$dtls_data=explode("**",str_replace("'",'',$$hdnDtlsdata));
			for($j=0; $j<=count($dtls_data); $j++)
			{
				//echo "10**".$dtls_data[$j];// die;
				$dtlsId='';
				$exdata=explode("_",$dtls_data[$j]);
				$description="'".$exdata[0]."'";
				//echo "10**".$description."++";
				$specification="'".$exdata[1]."'";
				$unit="'".$exdata[2]."'";
				$unit_pcs="'".str_replace(",",'',$exdata[3])."'";
				$cons_qty="'".str_replace(",",'',$exdata[4])."'";
				$process_loss="'".str_replace(",",'',$exdata[5])."'";
				$process_loss_qty="'".$exdata[6]."'";
				$req_qty="'".str_replace(",",'',$exdata[7])."'";
				$remarks="'".$exdata[8]."'";
				$jobBrk_id="'".$exdata[9]."'";
				$product_id="".$exdata[10]."";
				$product_lot="".$exdata[11]."";
				$cc	=str_replace("'",'',$jobBrk_id);

				if($req_id !='' && $req_id !=0 ){
					$requisition_qty=$requisition_data_arr[$product_id]['requisition_qty'];
					$prev_process_loss=$prev_item_arr[$cc]['process_loss'];
					$prev_cons_unit=$prev_item_arr[$cc]['cons_qty'];
					$present_cons_qty=str_replace(",",'',$exdata[4]);
					$present_process_loss=str_replace(",",'',$exdata[5]);
					if($prev_cons_unit!='' && $prev_cons_unit!=0){
						if($prev_cons_unit>$present_cons_qty){
							echo "121**Req. Found. Can't reduce Cons/Unit"; die;
						}
					}
					if($prev_process_loss!='' && $prev_process_loss!=0){
						if($prev_process_loss>$present_process_loss){
							echo "121**Req. Found. Can't reduce Process Loss"; die;
						}
					}
				}
				$all_job_break_id_arr[]=$cc;
				$all_job_prod_id_arr[]=$product_id;
				$product_qty_chk[$product_id]+=$req_qty;

				//echo "10**".$copyChk."+++";
				if (str_replace("'",'',$$hdnDtlsUpdateId) =='' || str_replace("'",'',$$hdnDtlsUpdateId)==0) $dtlsId=$id1; else $dtlsId=$$hdnDtlsUpdateId;
				if($cc!="" && $copyChk==0)
				{
					//echo "10**".$cc."+";
					//$data_array7[$cc]=explode("*",("".$product_id."*".$description."*".$specification."*".$unit."*".$unit_pcs."*".$cons_qty."*".$process_loss."*".$process_loss_qty."*".str_replace(",",'',$req_qty)."*".$remarks."*0*1*".$user_id."*'".$pc_date_time."'"));
					$data_array7[$cc]=explode("*",("".$product_id."*".$description."*".$specification."*".$unit."*".$unit_pcs."*".$cons_qty."*".$process_loss."*".$process_loss_qty."*".str_replace(",",'',$req_qty)."*".$remarks."*1*0*".$user_id."*'".$pc_date_time."'"));
					//echo "10**".$cc;
					$hdn_jobBrk_id_arr[]=$cc;
				}
				else
				{
					//echo "5**".$product_id."_";
					if($product_id!='')
					{
						if ($add_commadtls!=0) $data_array5 .=","; 
						$data_array5.="(".$id3.",".$dtlsId.",'".$hid_order_id."','".$txt_job_no."',".$$bookConDtlsId.",".$product_id.",".$description.",".$specification.",".$unit.",".$unit_pcs.",".$cons_qty.",".$process_loss.",".$process_loss_qty.",".$req_qty.",".$remarks.",'".$product_lot."','".$user_id."','".$pc_date_time."')";
						
						$id3=$id3+1; $add_commadtls++;
					}
					
				}
				
			}
			$id1=$id1+1; $add_commaa++;
			//echo "10**".str_replace("'",'',$$hdnBrkDelId); //die;
			/*if(chop(str_replace("'",'',$$hdnBrkDelId),',')!="")
			{
				$hdnBrkDelIds.=str_replace("'",'',$$hdnBrkDelId).',';
			}*/	
		}
		//echo "10**".$data_array5."+";
		//echo "10**".$total_row; die;
		$requisition_job_break_id_arr=array_unique(explode(",",chop($requisition_job_break_ids,',')));


		/*foreach ($all_req_prod_arr as $key => $value) {
			if (!in_array($value, $all_job_prod_id_arr))
			{
			  	echo "121**Req. Found. Can't Reduce Item"; die;
			}
			else{
				if($requisition_data_arr[$value]['requisition_qty']>$product_qty_chk[$value]){
					echo "121**Req. Found. Can't Reduce Item"; die;
				}
			}
		}*/

		foreach ($requisition_job_break_id_arr as $key => $value) {
			if (!in_array($value, $all_job_break_id_arr))
			{
			  	echo "121**Req. Found. Can't Reduce Item"; die;
			}
		}

		
		
		$rID=sql_update("trims_job_card_mst",$field_array,$data_array,"id",$update_id,0);  
		if($rID) $flag=1; else $flag=0;
		
		if($data_array2!=""  && $flag==1){
			//echo "10**".bulk_update_sql_statement( "trims_job_card_dtls", "id",$field_array2,$data_array2,$hdn_dtls_id_arr); die;
			$rID2=execute_query(bulk_update_sql_statement( "trims_job_card_dtls", "id",$field_array2,$data_array2,$hdn_dtls_id_arr),1);
			if($rID2) $flag=1; else $flag=0;
		}
		
		if($data_array6!="" && $flag==1){
			//echo "10**INSERT INTO trims_job_card_dtls (".$field_array6.") VALUES ".$data_array6; die;
			$rID6=sql_insert("trims_job_card_dtls",$field_array6,$data_array6,1);
			if($rID6==1) $flag=1; else $flag=0;
		}

		if($flag==1){
			//echo sql_multirow_update("trims_job_card_breakdown",$field_array_status,$data_array_status,"id",chop($hdnBrkDelId,','),0); die;
			$txt_job_no="'".$txt_job_no."'";
			$rID4=sql_multirow_update("trims_job_card_breakdown",$field_array_status,$data_array_status,"job_no_mst",$txt_job_no,0);
			if($rID4) $flag=1; else $flag=0;
		} 
		if($data_array5!=""  && $flag==1)
		{
			//echo "10**INSERT INTO trims_job_card_breakdown (".$field_array5.") VALUES ".$data_array5; die;
			$rID3=sql_insert("trims_job_card_breakdown",$field_array5,$data_array5,1);
			if($rID3) $flag=1; else $flag=0;		
		}

		if($data_array7!=""  && $flag==1){
			//echo "10**".bulk_update_sql_statement( "trims_job_card_breakdown", "id",$field_array7,$data_array7,$hdn_jobBrk_id_arr); die;
			$rID7=execute_query(bulk_update_sql_statement( "trims_job_card_breakdown", "id",$field_array7,$data_array7,$hdn_jobBrk_id_arr),1);
			if($rID7) $flag=1; else $flag=0;
		}

		
		/*if($flag==1)
			//echo "10**delete from trims_job_card_breakdown where job_no_mst ='$txt_job_no'"; die;
			$rID1=execute_query( "delete from trims_job_card_breakdown where job_no_mst ='$txt_job_no'",0);
			if($rID1) $flag=1; else $flag=0;
		}*/
		//echo "10**".$rID.'='.$rID1.'='.$rID2.'='.$rID3.'='.$rID4.'='.$rID6.'='.$rID7; die;
		//if($rID4) $flag=1; else $flag=0;
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
	else if ($operation==2)   // delete here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");  
		}

		$req_id=return_field_value("id","trims_raw_mat_requisition_mst","job_id=$update_id and status_active=1 and is_deleted=0","id");
		if($req_id !='' && $req_id !=0 ){
			echo "21**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id);oci_rollback($con); disconnect($con); 
			disconnect($con);
			die;
	    }
		
		$next_process=return_field_value( "id", "trims_production_mst"," entry_form=269 and $update_id=job_id and status_active=1 and is_deleted=0");
		if($next_process!=''){
			echo "20**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id);oci_rollback($con); disconnect($con); 
			disconnect($con);
			die;
			
		}
		
		$next_process_issue=return_field_value( "issue_number", "inv_issue_master"," entry_form=265 and issue_basis=15 and req_id=$update_id and status_active=1 and is_deleted=0");
		if($next_process_issue!=''){
			
			echo "18**Delete restricted, This Information is used in Raw Material Issue.".$next_process_issue;oci_rollback($con); disconnect($con); 
			disconnect($con);
			die;
			
		}
		
		for($i=1; $i<=$total_row; $i++)
		{	
			$hdnDtlsdata 			= "hdnDtlsdata_".$i;
			$hdnDtlsIDs 			= "hdnRecDtlsIDs_".$i;
			$hdnBreakIDs 			= "hdnBreakIDs_".$i;
			$hdnDtlsUpdateId 		= "hdnDtlsUpdateId_".$i;
			$hdnBrkDelId 	 		= "hdnBrkDelId_".$i;
			
			$dtls_data=explode("**",str_replace("'",'',$$hdnDtlsdata));
			for($j=0; $j<=count($dtls_data); $j++)
			{
				//echo "10**".$dtls_data[$j];// die;
				$dtlsId='';
				$exdata=explode("_",$dtls_data[$j]);
				$jobBrk_id="'".$exdata[9]."'";
				$product_id="".$exdata[10]."";
				$cc	=str_replace("'",'',$jobBrk_id);
				if($cc!="")
				{
					$break_data_arr[str_replace("'",'',$cc)]=explode("*",("0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					$hdn_jobBrk_id_arr[]=$cc;
				}
			}
		}
		
		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		$rID=sql_update("trims_job_card_mst",$field_array,$data_array,"id",$update_id,1);
		if($rID) $flag=1; else $flag=0; 
		$rID1=sql_update("trims_job_card_dtls",$field_array,$data_array,"mst_id",$update_id,1); 
		if($rID1) $flag=1; else $flag=0;
		if($field_array!="")
		{
			//echo "10**".bulk_update_sql_statement( "trims_job_card_breakdown", "id",$field_array,$break_data_arr,$hdn_jobBrk_id_arr);
			$rID5=execute_query(bulk_update_sql_statement( "trims_job_card_breakdown", "id",$field_array,$break_data_arr,$hdn_jobBrk_id_arr),1);
			if($rID5) $flag=1; else $flag=0;
		}
		//echo "10**".$rID."**".$rID1."**".$rID5."**".$flag; die;
		
		if($db_type==0)
		{
			if($flag)
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
			if($flag)
			{
				oci_commit($con);
				echo "2**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id);
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

if ($action=="work_order_popup")
{
	echo load_html_head_contents("Job Popup Info","../../../", 1, 1, $unicode,'','');
	?>
	<script>
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		} 
		var selected_id = new Array; var selected_id_dtls = new Array; var selected_id_break = new Array; var selected_qty = new Array; var subconArr = new Array; var selected_trimGroup = new Array;
		
		function check_all_data(is_checked)
		{
			var tbl_row_count = document.getElementById( 'tbl_po_list' ).rows.length;
			tbl_row_count = tbl_row_count - 1; 
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var woMixCheck=js_set_value( i );
				if(woMixCheck==1) return;
			}
		}
		function js_set_value(str) 
		{  	//alert(str);
			var subcon_job = $('#hidden_subcon_job'+str).val();
			var dtls_id = $('#hidden_dtls_id'+str).val();
			var breaks_id = $('#hidden_breaks_id'+str).val();
			var hidden_qty = $('#hidden_qty'+str).val();
			var trim_group = $('#hidden_trim_group'+str).val();
			
			if(subconArr.length==0){
				subconArr.push( subcon_job );
			}else if( jQuery.inArray( subcon_job, subconArr )==-1 &&  subconArr.length>0){
				alert("Work Order Mixed is Not Allow");return true;
			}
			
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			if( jQuery.inArray( breaks_id, selected_id_break  ) == -1 ) {
				selected_id.push( subcon_job );
				selected_id_dtls.push( dtls_id );
				selected_id_break.push( breaks_id );
				selected_trimGroup.push( trim_group );
				selected_qty.push( hidden_qty );
			}else{
				for( var i = 0; i < selected_id.length; i++ ){
					if( selected_id_break[i] == breaks_id ) break;
				}
				subconArr.splice( i, 1 );
				selected_id.splice( i, 1 );
				selected_id_dtls.splice( i, 1 );
				selected_id_break.splice( i, 1 );
				selected_trimGroup.splice( i, 1 );
				selected_qty.splice( i, 1 );
			}
			var id ='';  var id_dtls = ''; var id_break = ''; var id_trim_group = ''; var qty = 0; var qnty=0;
			for( var i = 0; i < selected_id_break.length; i++ ) {
				id += selected_id[i] + ',';
				id_dtls += selected_id_dtls[i] + '_';
				id_break += selected_id_break[i] + '_';
				id_trim_group += selected_trimGroup[i] + ',';
				qty += selected_qty[i]*1;
			}
			//qnty=Math.round(qty);
			qnty=qty.toFixed(4);
			id 		= id.substr( 0, id.length - 1 );
			if(id_dtls!=""&& id_dtls !=null) id_dtls= id_dtls.substr( 0, id_dtls.length - 1 );
			if(id_break!=""&& id_break !=null) id_break= id_break.substr( 0, id_break.length - 1 );
			if(id_trim_group!=""&& id_trim_group !=null) id_trim_group= id_trim_group.substr( 0, id_trim_group.length - 1 );
			//if(qty!=""&& qty !=null) qty= qty.substr( 0, qty.length - 1 );
			//alert(id_break);
			$('#all_subcon_job').val( id );	
			$('#all_sub_dtls_id').val( id_dtls );
			$('#all_sub_break_id').val( id_break );
			$('#all_trim_group').val( id_trim_group );
			$('#total_order_qty').val( qnty );
		}
			
		function fnc_load_party_popup(type,within_group)
		{
			var company = $('#cbo_company_name').val();
			var party_name = $('#cbo_party_name').val();
			var location_name = $('#cbo_location_name').val();
			var within_group = $('#cbo_within_group').val();
			load_drop_down( 'Job_card_preparation_controller', company+'_'+within_group, 'load_drop_down_buyer', 'buyer_td' );
		}
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0)
			{
				$('#search_by_td').html('System ID');
			}
			else if(val==2)
			{
				$('#search_by_td').html('W/O No');
			}
			else if(val==4)
			{
				$('#search_by_td').html('Buyer Po');
			}
			else if(val==5)
			{
				$('#search_by_td').html('Buyer Style');
			}
		}
	</script>
</head>
<body>
<div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="940" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead> 
                <tr>
                    <th colspan="9"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </tr>
                <tr>               	 
                    <th width="140" class="must_entry_caption">Company Name</th>
                    <th width="60">Within Group</th>                           
                    <th width="140">Party Name</th>
                    <th width="90">Trims Group</th>
                    <th width="80">Search By</th>
                    <th width="100" id="search_by_td">System ID</th>
                    <th width="170">Date Range</th>                            
                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                </tr>           
            </thead>
            <tbody>
                <tr class="general">
                    <td><input type="hidden" id="selected_job"><? $data=explode("_",$data); ?>  <!--  echo $data;-->
                        <? 
                        echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $data[0], "fnc_load_party_popup(1,document.getElementById('cbo_within_group').value);",1); ?>
                    </td>
                    <td>
                        <?php echo create_drop_down( "cbo_within_group", 60, $yes_no,"", 0, "--  --", $data[3], "fnc_load_party_popup(1,this.value);" ); ?>
                    </td>
                    <td id="buyer_td">
                        <? 
						echo create_drop_down( "cbo_party_name", 150, "","", 1, "-- Select Party --", $data[2], "fnc_load_party_popup(1,this.value);" );   	  	 
                        ?>
                    </td>
                    <td><? echo create_drop_down( "cbo_item_group", 90, "select id, item_name from lib_item_group where item_category=4 and status_active=1","id,item_name", 1, "-- Select --",$selected, "",0,'','','','','','',"cboItemGroup[]"); ?>	</td>
                    <td>
						<?
                            $search_by_arr=array(1=>"System ID",2=>"W/O No",4=>"Buyer Po",5=>"Buyer Style");
                            echo create_drop_down( "cbo_type",80, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                        ?>
                    </td>
                    <td align="center">
                        <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                    </td>
                    
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                    </td>
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year_selection').value+'_'+<? echo $data[4]; ?>+'_'+document.getElementById('cbo_item_group').value+'_'+<? echo $data[5]; ?>, 'create_order_search_list_view', 'search_div', 'Job_card_preparation_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
                    </tr>
                    <tr>
                    	<td align="center"><? echo create_drop_down( "cbo_year_selection", 60, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
                        <td colspan=6 align="center" valign="middle">
                            <? echo load_month_buttons();  ?>
                            <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
                        </td>
                        <td align="center">&nbsp;</td>
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
if($action=="create_order_search_list_view")
{	
	$data=explode('_',$data);
	$party_id=str_replace("'","",$data[1]);
	$search_by=str_replace("'","",$data[4]);
	$search_str=trim(str_replace("'","",$data[5]));
	$search_type =$data[6];
	$within_group =$data[7];
	$section_id =$data[9];
	$trim_group_id =$data[10];
	$src_for_order =$data[11];
	if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[8]";   }
	if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$data[8]";}

	if($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if($section_id!='') $section=" and b.section=$section_id"; else { $section=''; }
	if($trim_group_id!=''  && $trim_group_id!=0) $trim_group_cond=" and b.item_group=$trim_group_id"; else { $trim_group_cond=''; }
	//echo $search_type."==".$search_str."==".$trim_group_id; die;
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no = '$search_str' ";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref = '$search_str' ";
		}
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '$search_str%'";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '$search_str%'";  
		}
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '%$search_str'";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '%$search_str'";  
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '%$search_str%'"; 
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '%$search_str%'";   
		}
	}	

	if($party_id!=0) $party_id_cond=" and a.party_id='$party_id'"; else $party_id_cond="";
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and a.receive_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $order_rcv_date ="";
		//$buyerBuyer_concat=" , group_concat(b.buyer_buyer) as buyer_buyer";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and a.receive_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $order_rcv_date ="";
		//$buyerBuyer_concat=" , listagg(CAST(b.buyer_buyer as VARCHAR(4000)),',') within group (order by b.id) as buyer_buyer";
	}
	if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
	if($within_group==1)
	{
		$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	}
	else
	{
		
		$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	}

	if($db_type==0) $dtls_id_cond="group_concat(b.id) as dtls_id";
	else if($db_type==2) $dtls_id_cond="rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') as dtls_id";
	if($db_type==0) $breaks_id_cond="group_concat(b.id) as breaks_id";
	else if($db_type==2) $breaks_id_cond="rtrim(xmlagg(xmlelement(e,c.id,',').extract('//text()') order by c.id).GetClobVal(),',') as breaks_id";
	if($db_type==0) $buyerBuyer_concat="group_concat(b.buyer_buyer) as buyer_buyer";
	else if($db_type==2) $buyerBuyer_concat="rtrim(xmlagg(xmlelement(e,b.buyer_buyer,',').extract('//text()') order by b.id).GetClobVal(),',') as buyer_buyer";

	if($src_for_order==2)
	{
		$search_com_cond.="and b.source_for_order=2";
	} else {
		$search_com_cond.="and b.source_for_order in(0,1)";
	}


	/*$sql= "select a.id, a.subcon_job, a.job_no_prefix_num, a.company_id, a.location_id, a.party_id, a.receive_date, a.delivery_date ,a.order_no,b.section ,b.item_group, c.description $buyerBuyer_concat, listagg(b.id,',') within group (order by b.id) as dtls_id, listagg(c.id,',') within group (order by c.id) as breaks_id, sum(c.booked_qty) as qnty
	from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
	where a.entry_form=255 and a.subcon_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and a.id=b.mst_id and b.id=c.mst_id  and b.order_quantity<>0 and c.qnty<>0  and a.status_active=1 and b.status_active=1 $order_rcv_date $company $party_id_cond $withinGroup $search_com_cond $section $trim_group_cond
	group by a.id, a.subcon_job, a.job_no_prefix_num, a.company_id, a.location_id, a.party_id, a.receive_date, a.delivery_date, a.order_no ,b.section ,b.item_group, c.description
	order by a.id DESC";
	*/
	$sql= "select a.id, a.subcon_job, a.job_no_prefix_num, a.company_id, a.location_id, a.party_id, a.receive_date, a.delivery_date ,a.order_no,b.section ,b.item_group, c.description ,$buyerBuyer_concat,$dtls_id_cond,$breaks_id_cond, sum(c.booked_qty) as qnty
	from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
	where a.entry_form=255 and a.subcon_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and a.id=b.mst_id and b.id=c.mst_id  and b.order_quantity<>0 and c.qnty<>0  and a.status_active=1 and b.status_active=1 and c.status_active=1 $order_rcv_date $company $party_id_cond $withinGroup $search_com_cond $section $trim_group_cond
	group by a.id, a.subcon_job, a.job_no_prefix_num, a.company_id, a.location_id, a.party_id, a.receive_date, a.delivery_date, a.order_no ,b.section ,b.item_group, c.description
	order by a.id DESC";
	$data_array=sql_select($sql);
	//echo $sql; die;
	/*
	foreach ($data_array as $row) 
	{
		$all_wo_arr[$row[csf('subcon_job')]]=$row[csf('subcon_job')];
		$all_wo_qty_arr[$row[csf('subcon_job')]][$row[csf('section')]]+=$row[csf('qnty')];
	}*/
	
	//$all_wo_no ="'".implode("','",$all_wo_arr)."'";

	$job_sql= "select a.received_id, a.section_id as section, sum(b.job_quantity) as qty,b.item_group_id 
	from trims_job_card_mst a, trims_job_card_dtls b  where a.entry_form=257 and a.id=b.mst_id and a.status_active=1 and b.status_active=1 
	group by a.received_id, a.section_id,b.item_group_id";
	 //echo $sql;
	$job_data_array=sql_select($job_sql);
	foreach ($job_data_array as $row) 
	{
	 	$all_job_arr[$row[csf('subcon_job')]]=$row[csf('subcon_job')];
	 	$all_job_trim_group_arr[$row[csf('received_id')]][$row[csf('section')]][$row[csf('item_group_id')]]=$row[csf('item_group_id')];
	 	//$all_job_qty_arr[$row[csf('subcon_job')]][$row[csf('section')]]+=$row[csf('qty')];
	}
	//echo "<pre>";
	//print_r($all_job_qty_arr);
	//$all_wo_no ="'".implode("','",$all_wo_arr)."'";
	?>
     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="845" >
        <thead>
            <th width="30">SL</th>
            <th width="100">Receive No</th>
            <th width="100">W/O No</th>
            <th width="60">Section</th>
            <th width="80">Trim Group</th>
            <th width="120">Description</th>
            <th width="80">W/O Qty</th>
            <th width="60">Ord Receive Date</th>
            <th width="60">Delivery Date</th>
            <th>Buyer's Buyer</th>
        </thead>
        </table>
        <div style="width:845px; max-height:260px;overflow-y:scroll;" >	 
        	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="825" class="rpt_table" id="tbl_po_list">
	        	<tbody>
	            <? 
	            $i=1;
	            $group_arr=return_library_array( "select id, item_name from lib_item_group where status_active=1 and item_category=4", "id", "item_name");
	            foreach($data_array as $row)
	            {
	            	/*$wo_qty=$all_wo_qty_arr[$row[csf('subcon_job')]][$row[csf('section')]];
	             	$job_qty=$all_job_qty_arr[$row[csf('subcon_job')]][$row[csf('section')]];
	             	//echo $wo_qty."==".$job_qty."++";
	            	if($wo_qty>$job_qty)
	            	{*/
            		
            		$attached_item_group=$all_job_trim_group_arr[$row[csf('id')]][$row[csf('section')]][$row[csf('item_group')]];
	            	if($attached_item_group=='')
	            	{
	            		$buyerBuyer='';$buyer_ids='';
               			$buyer_ids = $row[csf("buyer_buyer")]->load();
						//$buyer_buyer=array_unique(explode(",",$row[csf("buyer_buyer")]));
						$buyer_buyer=array_unique(explode(",",$buyer_ids));
						if($within_group==1){
							foreach($buyer_buyer as $val){
								if($buyerBuyer=="") $buyerBuyer=$buyer_arr[$val]; else $buyerBuyer.=",".$buyer_arr[$val];
							}
						}else{
							$buyerBuyer=implode(",",$buyer_buyer);
							//$buyerBuyer=$buyer_buyer;
						}
						/*$order_no=implode(",",array_unique(explode(",",$order_no)));
	            		//$buyer_arr
	            		if($within_group==1){
	            			$buyer_buyer=implode(", ",array_unique(explode(",",$row[csf('buyer_buyer')])));
	            		}else{
							$buyer_buyer=implode(", ",array_unique(explode(",",$row[csf('buyer_buyer')])));
						}*/
						
						$load_dtls_ids=""; $load_breaks_ids=""; $dtls_ids=""; $breaks_ids="";
               			$load_dtls_ids = $row[csf("dtls_id")]->load();
						$loaddtlsids=array_unique(explode(",",$load_dtls_ids));
						$dtls_ids=implode(",",$loaddtlsids);
						
						$load_breaks_ids = $row[csf("breaks_id")]->load();
						$loadbreaksids=array_unique(explode(",",$load_breaks_ids));
						$breaks_ids=implode(",",$loadbreaksids);
		            	if ($i%2==0)  
		            		$bgcolor="#E9F3FF";
		            	else
		            		$bgcolor="#FFFFFF";	
	            		?>	
		            	<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $i;?>)" > 
		            		<td width="30"><? echo $i; ?></td>
		            		<td width="100" align="center"><? echo $row[csf('subcon_job')]; ?></td>
		            		<td width="100"><? echo $row[csf('order_no')]; ?></td>
		            		<td width="60"><? echo $trims_section[$row[csf('section')]]; ?></td>
		            		<td width="80" ><? echo $group_arr[$row[csf('item_group')]]; ?></td>
		            		<td width="120" style="word-break:break-all" ><? echo $row[csf('description')]; ?></td>
		            		<td width="80"><? echo number_format($row[csf('qnty')],2); ?></td>
		            		<td width="60" style="text-align:center;"><? echo change_date_format($row[csf('receive_date')]); ?></td>
		            		<td width="60" style="text-align:center;"><? echo change_date_format($row[csf('delivery_date')]); ?></td>
		            		<td style="word-break:break-all" ><? echo $buyerBuyer; ?>
		            		<input  class="text_boxes" type="hidden" name="hidden_subcon_job<? echo $i; ?>" id="hidden_subcon_job<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>" style="width:70px">
		            		<input class="text_boxes" type="hidden"  name="hidden_dtls_id<? echo $i; ?>" id="hidden_dtls_id<? echo $i; ?>" value="<? echo $dtls_ids;//$row[csf('dtls_id')]; ?>" style="width:70px">
		            		<input class="text_boxes" type="hidden" name="hidden_breaks_id<? echo $i; ?>" id="hidden_breaks_id<? echo $i; ?>" value="<? echo $breaks_ids; //$row[csf('breaks_id')]; ?>" style="width:70px">
		            		<input class="text_boxes" type="hidden" name="hidden_qty<? echo $i; ?>" id="hidden_qty<? echo $i; ?>" value="<? echo $row[csf('qnty')]; ?>" style="width:70px">	
		            		<input class="text_boxes" type="hidden" name="hidden_trim_group<? echo $i; ?>" id="hidden_trim_group<? echo $i; ?>" value="<? echo $row[csf('item_group')]; ?>" style="width:70px">	
		            		</td>
			            </tr>
			            <? 
			            $i++;
	            	}
	            } 
	            ?>
	        </tbody>
	    </table>
	</div>
    <table style="width:100%; float:left" align="center">
        <tr>
            <td align="center" height="30" valign="bottom">
                <div style="width:100%">
                    <div style="width:45%; float:left" align="left">
                        <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data(this.checked)" /> Check / Uncheck All
                    </div>
                    <div style="width:53%; float:left" align="left">
                        <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                        <input type="hidden"  id="all_subcon_job" />
				    	<input type="hidden"  id="all_sub_dtls_id" />
				    	<input type="hidden"  id="all_sub_break_id" />
				    	<input type="hidden"  id="total_order_qty" />
				    	<input type="hidden"  id="all_trim_group" />
                    </div>
                </div>
            </td>
        </tr>
    </table>
    
	<?    
	exit();
}

 
if ($action=="load_php_data_to_form")
{
	$data=implode(",",array_unique(explode(",",$data)));
	$nameArray=sql_select( "select a.id, a.subcon_job, a.company_id, a.location_id, a.party_id, a.currency_id, a.party_location, a.delivery_date, a.within_group, a.party_location, a.order_id, a.order_no,b.section from subcon_ord_mst a ,subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.id in($data) and a.status_active=1 and b.status_active=1 group by  a.id, a.subcon_job, a.company_id, a.location_id, a.party_id, a.currency_id, a.party_location, a.delivery_date, a.within_group, a.party_location, a.order_id, a.order_no,b.section" );
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_recv_no').value 				= '".$row[csf("subcon_job")]."';\n";
		echo "document.getElementById('hid_recv_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_company_name').value 		= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_within_group').value 		= '".$row[csf("within_group")]."';\n";  
		echo "$('#cbo_company_name').attr('disabled','true')".";\n";
		echo "$('#cbo_section').attr('disabled','true')".";\n";
		echo "fnc_load_party(1,".$row[csf("within_group")].");\n";	
	//echo "load_drop_down( 'requires/Job_card_preparation_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_location', 'location_td' );\n";	
		echo "document.getElementById('cbo_location_name').value 		= '".$row[csf("location_id")]."';\n";
	//echo "load_drop_down( 'requires/Job_card_preparation_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_buyer', 'buyer_td' );\n";
		echo "document.getElementById('cbo_party_name').value			= '".$row[csf("party_id")]."';\n";
		//cbo_company_name cbo_location_name cbo_within_group cbo_party_name cbo_party_location txt_delivery_date cbo_section txt_order_no txt_order_qty
		echo "fnc_load_party(2,".$row[csf("within_group")].");\n";	 
		echo "document.getElementById('cbo_party_location').value		= '".$row[csf("party_location")]."';\n";	
		echo "document.getElementById('txt_delivery_date').value		= '".change_date_format($row[csf("delivery_date")])."';\n";  
		echo "document.getElementById('hid_order_id').value          	= '".$row[csf("order_id")]."';\n";
		echo "document.getElementById('txt_order_no').value         	= '".$row[csf("order_no")]."';\n";
		//echo "document.getElementById('cbo_section').value        		= '".$row[csf("section")]."';\n";
		//echo "document.getElementById('txt_order_qty').value         	= '".$row[csf("remarks")]."';\n";
		echo "$('#txt_order_no').attr('disabled','true')".";\n";
		echo "$('#cbo_within_group').attr('disabled','true')".";\n";
		echo "$('#cbo_party_name').attr('disabled','true')".";\n";
		//echo "document.getElementById('update_id').value          		= '".$row[csf("id")]."';\n";	
		//echo "set_button_status(1,'".$_SESSION['page_permission']."', 'fnc_job_order_entry',1);\n";	
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
		load_drop_down( 'Job_card_preparation_controller', company+'_'+1+'_'+party_name, 'load_drop_down_buyer', 'buyer_td' );
		$('#cbo_party_name').attr('disabled',true);
	}
	
	function search_by(val,type)
	{
		if(type==1)
		{
			if(val==1 || val==0)
			{
				$('#txt_search_common').val('');
				$('#search_td').html('W/O No');
			}
			else if(val==2)
			{
				$('#txt_search_common').val('');
				$('#search_td').html('Job NO');
			}
			else if(val==3)
			{
				$('#txt_search_common').val('');
				$('#search_td').html('Style Ref.');
			}
			else if(val==4)
			{
				$('#txt_search_common').val('');
				$('#search_td').html('Buyer Po');
			}
		}
	}
</script>
</head>
<body onLoad="fnc_load_party_order_popup(<? echo $company;?>,<? echo $party_name;?>)">
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
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
                            $searchtype_arr=array(1=>"W/O No",2=>"Buyer Job",3=>"Buyer Style Ref.",4=>"Buyer Po");
                            echo create_drop_down( "cbo_search_type", 80, $searchtype_arr,"", 0, "", 1, "search_by(this.value,1)",0,"" );
                        ?>
                    </td>
                    <td><input name="txt_search_common" id="txt_search_common" class="text_boxes" style="width:90px"></td>
                    <td><? echo create_drop_down( "cbo_year_selection", 60, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
                    <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From"></td>
                    <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To"></td> 
                    <td>
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_search_category').value+'_'+<? echo $company;?>+'_'+document.getElementById('cbo_search_type').value, 'create_booking_search_list_view', 'search_div', 'Job_card_preparation_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                    </td>
                </tr>
                <tr>
                    <td colspan="7"align="center" height="30" valign="middle"><?  echo load_month_buttons(); ?></td>
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
	$data=explode('_',$data);
	$search_type=$data[7];
	
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Party First."; die; }
	//if ($data[0]!=0 && ) $buyer=" and buyer_id='$data[1]'"; else  $buyer="";//{ echo "Please Select Buyer First."; die; }
	if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[4]"; } else if($db_type==2) { $year_cond=" and to_char(a.insert_date,'YYYY')=$data[4]"; }
	$master_company=$data[6];

	$woorder_cond=""; $job_cond=""; $style_cond=""; $po_cond="";
	if($data[5]==1)
	{
		if ($data[1]!="")
		{
			if ($search_type==1) $woorder_cond=" and a.booking_no = '$data[1]' ";
			if ($search_type==2) $job_cond=" and a.job_no_prefix_num = '$data[1]' ";
			if ($search_type==3) $style_cond=" and a.style_ref_no = '$data[1]' ";
			if ($search_type==4) $po_cond=" and b.po_number = '$data[1]' ";
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
		}
	}
	
	$po_ids='';
	
	if($db_type==0) $id_cond="group_concat(b.id)";
	else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	if(($job_cond!="" && $search_type==2) || ($style_cond!="" && $search_type==3)|| ($po_cond!="" && $search_type==4))
	{
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
		if ($po_ids=="")
		{
			$po_idsCond="";
			echo "Not Found."; die;
		}
	}
	
	if ($po_ids!="") $po_idsCond=" and b.po_break_down_id in ($po_ids)"; else $po_idsCond="";
	$buyer_po_arr=array();
	$po_sql ="Select a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		$buyer_po_arr[$row[csf("id")]]['buyer']=$row[csf("buyer_name")];
		$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no")];
	}
	unset($po_sql_res);
	
	$pre_cost_trims_arr=array();
	$pre_sql ="Select id, emb_name, emb_type, body_part_id  from wo_pre_cost_embe_cost_dtls where status_active=1 and is_deleted=0";
	$pre_sql_res=sql_select($pre_sql);
	foreach ($pre_sql_res as $row)
	{
		$pre_cost_trims_arr[$row[csf("id")]]['emb_name']=$row[csf("emb_name")];
		$pre_cost_trims_arr[$row[csf("id")]]['emb_type']=$row[csf("emb_type")];
		$pre_cost_trims_arr[$row[csf("id")]]['body_part_id']=$row[csf("body_part_id")];
	}
	unset($pre_sql_res);
	
	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="") $booking_date = "and a.booking_date between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
		$wo_year="YEAR(a.insert_date)";
		$pre_cost_trims_cond="group_concat(b.pre_cost_fabric_cost_dtls_id)";
		$gmts_item_cond="group_concat(b.gmt_item)";
		$po_id_cond="group_concat(b.po_break_down_id)";
	}
	else if($db_type==2)
	{
		if ($data[2]!="" &&  $data[3]!="") $booking_date = "and a.booking_date between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
		$wo_year="to_char(a.insert_date,'YYYY')";
		$pre_cost_trims_cond="listagg(b.pre_cost_fabric_cost_dtls_id,',') within group (order by b.pre_cost_fabric_cost_dtls_id)";
		$gmts_item_cond="listagg(b.gmt_item,',') within group (order by b.gmt_item)";
		$po_id_cond="listagg(b.po_break_down_id,',') within group (order by b.po_break_down_id)";
	} 
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (0=>$comp,1=>$buyer_arr);
	//$sql= "select $wo_year as year, id, booking_type, booking_no, company_id, buyer_id, job_no, booking_date from  wo_booking_mst where booking_type=6 and status_active=1 and is_deleted=0 $booking_date $company $woorder_cond $year_cond order by booking_no"; 
	//$sql= "select $wo_year as year, a.id, a.booking_type, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id, $pre_cost_trims_cond as pre_cost_trims_id, $gmts_item_cond as gmts_item, $po_id_cond as po_id from  wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=6 and a.status_active=1 and a.lock_another_process!=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $booking_date $company $woorder_cond $year_cond $po_idsCond group by a.insert_date, a.id, a.booking_type, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id order by a.id DESC";

	$sql= "select a.id, a.subcon_job, a.company_id, a.location_id, a.party_id, a.party_location, a.currency_id, a.receive_date, a.delivery_date,a.rec_start_date, a.order_id, a.order_no, a.exchange_rate, b.id, b.mst_id, b.order_id, b.order_no, b.booked_uom,b.delivery_date, b.buyer_po_no, b.buyer_style_ref, b.buyer_buyer,  b.section, b.item_group  from subcon_ord_mst a, subcon_ord_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=255 group by a.id, a.subcon_job, a.company_id, a.location_id, a.party_id, a.party_location, a.currency_id, a.receive_date, a.delivery_date, a.rec_start_date, a.order_id, a.order_no, a.exchange_rate,b.id, b.mst_id, b.order_id, b.order_no, b.booked_uom,b.delivery_date, b.buyer_po_no, b.buyer_style_ref, b.buyer_buyer, b.section, b.item_group";
	// a.subcon_job=job_no_mst a
	$data_array=sql_select($sql);
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="640" >
        <thead>
            <th width="30">SL</th>
            <th width="60">W/O Year</th>
            <th width="60">W/O No</th>
            <th width="70">W/O Date</th>
            <th width="100">Buyer</th>
            <th width="100">Buyer Po</th>
            <th width="100">Buyer Style</th>
            <th width="100">Buyer Job</th>
        </thead>
        </table>
        <div style="width:640px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="620" class="rpt_table" id="list_view">
        <tbody>
            <? 
            $i=1;
            foreach($data_array as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$row[csf('booking_no')].'_'.$row[csf('currency_id')]; ?>")' style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="60" align="center"><? echo $row[csf('year')]; ?></td>
                    <td width="60" align="center"><? echo $row[csf('booking_no_prefix_num')]; ?></td>
                    <td width="70"><? echo change_date_format($row[csf('booking_date')]); ?></td>
                    <td width="100" style="word-break:break-all"><? echo $buyer_name; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $po_no; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $buyer_style; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $buyer_job; ?></td>
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

if ($action=="populate_data_from_search_popup")
{
	//echo $action."nazim"; die;
	$data=explode('_',$data);
	$nameArray=sql_select( "select id, booking_type, booking_no, company_id, buyer_id, job_no, booking_date,currency_id from  wo_booking_mst where booking_type=6 and status_active=1 and is_deleted=0 and id='$data[0]'" );
	//$sql= "select to_char(insert_date,'YYYY') as year, id, booking_type, booking_no, company_id, buyer_id, job_no, booking_date from  wo_booking_mst where booking_type=6 and status_active=1 and is_deleted=0 $booking_date $company $order_cond order by booking_no";
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_order_no').value 	= '".$row[csf("booking_no")]."';\n";  
		echo "document.getElementById('cbo_party_name').value 	= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('hid_order_id').value		= '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_currency').value		= '".$row[csf("currency_id")]."';\n";
	}
	exit();	
}

if( $action=='order_dtls_list_view' ) 
{
	$data=explode('**',$data);
	//echo $data[7];
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$size_arr=return_library_array( "select id,size_name from lib_size",'id','size_name');
	$tblRow=0; $buyer_po_arr=array();
	if($data[0]==1)
	{
		$buyer_po_sql = sql_select("select id, buyer_po_no, buyer_style_ref,order_uom from subcon_ord_dtls  where status_active=1");
		foreach($buyer_po_sql as $row) //subcon_ord_mst subcon_ord_dtls subcon_ord_breakdown
		{
			$buyer_po_arr[$row[csf('id')]]['style']=$row[csf('buyer_style_ref')];
			$buyer_po_arr[$row[csf('id')]]['buyerpo']=$row[csf('buyer_po_no')];
			$buyer_po_arr[$row[csf('id')]]['order_uom']=$row[csf('order_uom')];
		}
		unset($buyer_po_sql);
		$dtlsIds=str_replace("_",",",$data[1]);
		//$brk_sql= "select a.description,a.color_id,a.size_id,a.mst_id,a.id,a.book_con_dtls_id,b.item_group from subcon_ord_breakdown a, subcon_ord_dtls b where b.id=a.mst_id and a.mst_id in ($dtlsIda) and b.job_no_mst='$data[6]' and a.status_active=1 "; 
		$brk_sql= "select a.mst_id,a.id,a.book_con_dtls_id, a.description,a.color_id,a.size_id,a.gmts_color_id, a.gmts_size_id,b.sub_section,b.booked_uom,b.booked_conv_fac,b.item_group from subcon_ord_dtls b ,subcon_ord_breakdown a where a.mst_id=b.id and b.order_quantity<>0 and a.qnty<>0  and a.status_active=1  and b.mst_id in ($data[3]) and section=$data[4] and b.job_no_mst='$data[6]' and b.item_group in ($data[7]) and a.id in ($dtlsIds) and b.job_no_mst=a.job_no_mst and a.status_active=1 and a.is_deleted=0";
		
		//echo $brk_sql; //die; 
		//$data_array=sql_select($brk_sql);

		$brk_array=sql_select($brk_sql); $subconBrk_arr=array(); $mstIDS=''; $ids=''; $book_con_dtls_ids='';
		foreach ($brk_array as  $row) 
		{
			$subconBrk_arr[$row[csf("sub_section")]][$row[csf("item_group")]][$row[csf("booked_uom")]][$row[csf("booked_conv_fac")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]][$row[csf("gmts_color_id")]][$row[csf("gmts_size_id")]]["mst_id"] .= $row[csf("mst_id")].',';
			$subconBrk_arr[$row[csf("sub_section")]][$row[csf("item_group")]][$row[csf("booked_uom")]][$row[csf("booked_conv_fac")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]][$row[csf("gmts_color_id")]][$row[csf("gmts_size_id")]]["id"] .= $row[csf("id")].',';
			$subconBrk_arr[$row[csf("sub_section")]][$row[csf("item_group")]][$row[csf("booked_uom")]][$row[csf("booked_conv_fac")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]][$row[csf("gmts_color_id")]][$row[csf("gmts_size_id")]]["book_con_dtls_id"] .= $row[csf("book_con_dtls_id")].',';
		}
		//die;
		//echo "<pre>";
		//print_r($subconBrk_arr); //die;
		//$dtlsIds=str_replace("_",",",$data[1]);
		$dtls_sql= "select a.description,a.color_id,a.size_id,a.gmts_color_id, a.gmts_size_id, b.sub_section,b.booked_uom,b.booked_conv_fac,b.item_group, sum(a.booked_qty) as qnty  $dtlsid_cond from subcon_ord_dtls b ,subcon_ord_breakdown a where a.mst_id=b.id and b.order_quantity<>0 and a.qnty<>0  and a.status_active=1  and b.mst_id in ($data[3]) and section=$data[4] and b.item_group in ($data[7]) and a.id in ($dtlsIds) and b.job_no_mst=a.job_no_mst group by b.sub_section,b.booked_uom,b.booked_conv_fac,a.description,a.color_id,a.size_id,a.gmts_color_id, a.gmts_size_id,b.item_group "; //die;
		$data_array=sql_select($dtls_sql); $subconDtls_arr=array(); 
		foreach ($data_array as  $rows) 
		{

			$subconDtls_arr[$rows[csf("sub_section")]][$rows[csf("item_group")]][$rows[csf("booked_uom")]][$rows[csf("booked_conv_fac")]][$rows[csf("description")]][$rows[csf("color_id")]][$rows[csf("size_id")]][$rows[csf("gmts_color_id")]][$rows[csf("gmts_size_id")]]["qnty"] +=$rows[csf("qnty")];
		}
		//echo "<pre>";
		//print_r($subconDtls_arr); die; 
		$a=1;
		foreach($subconDtls_arr as $subSection_id=> $subSection_data)
		{
			foreach($subSection_data as $item_group=> $item_group_data)
			{
				foreach($item_group_data as $booked_uom=> $booked_uom_data)
				{ 
					foreach($booked_uom_data as $booked_conv_fac=> $conv_fac_data)
					{
						foreach($conv_fac_data as $description=> $description_data)
						{ 
							foreach($description_data as $color_id=> $color_data)
							{
								//$mst_ids='';
								foreach($color_data as $size_id => $size_data)
								{
									foreach($size_data as $gmts_color_id=> $gmts_color_data)
									{
										foreach($gmts_color_data as $gmts_size_id=> $row)
										{
											//$a++;
											//echo $mstIDS.'=';
											
											$ids=chop($subconBrk_arr[$subSection_id][$item_group][$booked_uom][$booked_conv_fac][$description][$color_id][$size_id][$gmts_color_id][$gmts_size_id]['id'],',');
											$mstIDS=chop($subconBrk_arr[$subSection_id][$item_group][$booked_uom][$booked_conv_fac][$description][$color_id][$size_id][$gmts_color_id][$gmts_size_id]['mst_id'],',');
											$bookConDtls_Ids=chop($subconBrk_arr[$subSection_id][$item_group][$booked_uom][$booked_conv_fac][$description][$color_id][$size_id][$gmts_color_id][$gmts_size_id]['book_con_dtls_id'],',');
											//echo $ids.'=';
											//$booking_dtls_ids=$subconBrk_arr[$description][$color_id][$size_id]['booking_dtls_id'];
											
											if($mstIDS!='')
											{
												$dtls_ids=explode(",",$mstIDS);
												$buyer_po=''; $buyer_style=''; $order_uom='';
												for ($i=0; $i <count($dtls_ids) ; $i++) 
												{ 
													$buyer_po.=$buyer_po_arr[$dtls_ids[$i]]['buyerpo'].",";
													$buyer_style.=$buyer_po_arr[$dtls_ids[$i]]['style'].",";
												}
											}
											
											$buyer_po=chop(implode(',',array_unique(explode(",",$buyer_po))),',');
											$buyer_style=chop(implode(',',array_unique(explode(",",$buyer_style))),',');
											$buyer_po_id=chop(implode(',',array_unique(explode(",",$buyer_po_id))),',');
											//round($row['qnty'])
											$wo_data .= $buyer_po."**".$buyer_po_id."**".$buyer_style."**".$description."**".$color_library[$color_id]."**".$color_id."**".$size_arr[$size_id]."**".$size_id."**".$subSection_id."**".$booked_uom."**".$booked_conv_fac."**".number_format($row['qnty'],4)."**".$mstIDS."**".$row['booking_dtls_ids']."**".$bookConDtls_Ids."**".$ids."**".$item_group."**".$gmts_color_id."**".$color_library[$gmts_color_id]."**".$gmts_size_id."**".$size_arr[$gmts_size_id]."#";
										}
									}
								}
							}
						}
					}
				}
			}
		}
		echo substr($wo_data,0,-1);
	}
	else
	{
		//$color_library_arr=return_library_array( "select id,color_name from lib_color  where status_active=1 and is_deleted=0", "id", "color_name"  );

		$job_qnty_arr=array();
		$qty_sql ="SELECT a.id, a.booked_qty from subcon_ord_breakdown a where a.booked_qty is not null and a.booked_qty!=0  and a.status_active=1";
		$qty_sql_res=sql_select($qty_sql);
		foreach ($qty_sql_res as $row)
		{
			$job_qnty_arr[$row[csf("id")]]['qnty']=$row[csf("booked_qty")];
		}
		unset($qty_sql_res);

		$sql= "select a.id, a.mst_id, a.job_no_mst,a.receive_dtls_id, a.book_con_dtls_id,a.booking_dtls_id, a.break_id as subcon_break_ids,a.buyer_po_no,a.buyer_po_id, a.buyer_style_ref, a.item_description, a.item_group_id, a.color_id, a.size_id, a.gmts_color_id, a.gmts_size_id, a.sub_section, a.uom, a.job_quantity,  a.impression,a.material_color,a.conv_factor,a.is_copy_material, b.id as break_id, b.order_id, b.job_no_mst, b.product_id , b.description, b.specification, b.unit, b.pcs_unit, b.cons_qty, b.process_loss, b.process_loss_qty, b.req_qty, b.remarks,c.id as prodDtls_id from trims_job_card_breakdown b, trims_job_card_dtls a LEFT JOIN trims_production_dtls c ON c.job_dtls_id = a.id and c.status_active<>0 where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.mst_id=$data[1]"; 
		$data_array=sql_select($sql); $dtls_arr=array();
		foreach ($data_array as  $row) 
		{
			$rawcolor=''; $materialColor=''; $jobQnty=''; $material_color=explode("__",$row[csf("material_color")]);
			//print_r($material_color);
			for($j=0; $j<count($material_color); $j++)
			{
				$materialColor.=$color_library[$material_color[$j]]."__";
			}
			$rawcolor=chop($materialColor,"__");

			$subcon_break_ids=explode(",",$row[csf("subcon_break_ids")]);
			//echo "<pre>";
			//print_r($subcon_break_ids);
			for($j=0; $j<count($subcon_break_ids); $j++)
			{
				$jobQnty +=$job_qnty_arr[$subcon_break_ids[$j]]['qnty'];
				//$idddd.=$subcon_break_ids[$j].',';
			}
			//echo $jobQnty.'=';
			//$jobQnty=round($jobQnty);
			$jobQnty=number_format($jobQnty,4);
			$dtls_arr[$row[csf("mst_id")]][$row[csf("id")]]["id"]=$row[csf("id")];
			$dtls_arr[$row[csf("mst_id")]][$row[csf("id")]]["item"]				=$row[csf("sub_section")]."_".$row[csf("uom")]."_".$row[csf("conv_factor")]."_".$row[csf("item_description")]."_".$row[csf("color_id")]."_".$row[csf("size_id")];
			$dtls_arr[$row[csf("mst_id")]][$row[csf("id")]]["receive_dtls_id"]	=$row[csf("receive_dtls_id")];
			$dtls_arr[$row[csf("mst_id")]][$row[csf("id")]]["prodDtls_id"]		=$row[csf("prodDtls_id")];
			$dtls_arr[$row[csf("mst_id")]][$row[csf("id")]]["book_con_dtls_id"]	=$row[csf("book_con_dtls_id")];
			$dtls_arr[$row[csf("mst_id")]][$row[csf("id")]]["booking_dtls_id"]	=$row[csf("booking_dtls_id")];
			$dtls_arr[$row[csf("mst_id")]][$row[csf("id")]]["buyer_po_no"]		=$row[csf("buyer_po_no")];
			$dtls_arr[$row[csf("mst_id")]][$row[csf("id")]]["buyer_po_id"]		=$row[csf("buyer_po_id")];
			$dtls_arr[$row[csf("mst_id")]][$row[csf("id")]]["buyer_style_ref"]	=$row[csf("buyer_style_ref")];
			$dtls_arr[$row[csf("mst_id")]][$row[csf("id")]]["item_description"]	=$row[csf("item_description")];
			$dtls_arr[$row[csf("mst_id")]][$row[csf("id")]]["item_group_id"]	=$row[csf("item_group_id")];
			$dtls_arr[$row[csf("mst_id")]][$row[csf("id")]]["color_id"]			=$row[csf("color_id")];
			$dtls_arr[$row[csf("mst_id")]][$row[csf("id")]]["size_id"]			=$row[csf("size_id")];
			$dtls_arr[$row[csf("mst_id")]][$row[csf("id")]]["gmts_color_id"]	=$row[csf("gmts_color_id")];
			$dtls_arr[$row[csf("mst_id")]][$row[csf("id")]]["gmts_size_id"]		=$row[csf("gmts_size_id")];
			$dtls_arr[$row[csf("mst_id")]][$row[csf("id")]]["sub_section"]		=$row[csf("sub_section")];
			$dtls_arr[$row[csf("mst_id")]][$row[csf("id")]]["uom"]				=$row[csf("uom")];
			$dtls_arr[$row[csf("mst_id")]][$row[csf("id")]]["conv_factor"]		=$row[csf("conv_factor")];
			$dtls_arr[$row[csf("mst_id")]][$row[csf("id")]]["job_quantity"]		=$row[csf("job_quantity")];
			// $dtls_arr[$row[csf("mst_id")]][$row[csf("id")]]["job_quantity"]		=$jobQnty;  //$row[csf("job_quantity")];//
			$dtls_arr[$row[csf("mst_id")]][$row[csf("id")]]["impression"]		=$row[csf("impression")];
			$dtls_arr[$row[csf("mst_id")]][$row[csf("id")]]["is_copy_material"]	=$row[csf("is_copy_material")];
			$dtls_arr[$row[csf("mst_id")]][$row[csf("id")]]["material_color"]	=$rawcolor;
			$dtls_arr[$row[csf("mst_id")]][$row[csf("id")]]["break_id"]			.=$row[csf("break_id")].",";
			$dtls_arr[$row[csf("mst_id")]][$row[csf("id")]]["description"]		.=$row[csf("description")].",";
			$dtls_arr[$row[csf("mst_id")]][$row[csf("id")]]["break_data"]		.=$row[csf("description")]."_".$row[csf("specification")]."_".$row[csf("unit")]."_".$row[csf("pcs_unit")]."_".$row[csf("cons_qty")]."_".$row[csf("process_loss")]."_".$row[csf("process_loss_qty")]."_".$row[csf("req_qty")]."_".$row[csf("remarks")]."_".$row[csf("break_id")]."_".$row[csf("product_id")]."**";

			//subSection_id booked_uom booked_conv_fac description color_id size_id
		}
		//echo "<pre>";
		//print_r($dtls_arr);

		$rcvItemChk_arr=array();
		foreach($dtls_arr as $dtls_data)
		{
			$jobItem='';
			foreach($dtls_data as $row)
			{
				$tblRow++;
				if ($tblRow%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if ($row['prodDtls_id']!='')$bgcolor="#ff2d00"; else $bgcolor=$bgcolor;
				$rcvItemChk_arr[]=$row['item'];
				
				 
				$buyer_po=chop(implode(',',array_unique(explode(",",$row['buyer_po_no']))),',');
			    $buyer_style=chop(implode(',',array_unique(explode(",",$row['buyer_style_ref']))),',');
			    $buyer_po_id=chop(implode(',',array_unique(explode(",",$row['buyer_po_id']))),',');
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
					<td title="<? echo $buyer_po; ?>"><input id="txtbuyerPo_<? echo $tblRow; ?>" name="txtbuyerPo[]"   class="text_boxes" type="text" value="<? echo $buyer_po; ?>" style="width:90px" disabled />
						<input id="txtbuyerPoId_<? echo $tblRow; ?>" name="txtbuyerPoId[]" value="<? echo $buyer_po_id; ?>" class="text_boxes" type="hidden" style="width:70px" disabled />
					</td>
	                <td title="<? echo $buyer_style; ?>"><input id="txtstyleRef_<? echo $tblRow; ?>" name="txtstyleRef[]" type="text" class="text_boxes" value="<? echo $buyer_style; ?>"  style="width:100px" placeholder="Display" disabled/></td>
	                <td><? echo create_drop_down( "cboItemGroup_".$tblRow, 90, "select id, item_name from lib_item_group where item_category=4 and status_active=1","id,item_name", 1, "-- Select --",$row['item_group_id'], "",1,'','','','','','',"cboItemGroup[]"); ?>	</td>
	                <td title="<? echo $row['item_description']; ?>"><input id="txtdescription_<? echo $tblRow; ?>" name="txtdescription[]" type="text" class="text_boxes" value="<? echo $row['item_description']; ?>"  style="width:90px" title="<? echo $row['item_description']; ?>" placeholder="Display" disabled/></td>
					<td><input id="txtgmtscolor_<? echo $tblRow; ?>" name="txtgmtscolor[]" type="text" class="text_boxes" value="<? echo $color_library[$row['gmts_color_id']]; ?>" style="width:60px" placeholder="Display" disabled/>
						<input id="txtgmtscolorID_<? echo $tblRow; ?>" name="txtgmtscolorID[]" type="hidden" class="text_boxes" value="<? echo $row['gmts_color_id']; ?>" style="width:60px" placeholder="Display" disabled/>
	                </td>
					<td><input id="txtgmtssize_<? echo $tblRow; ?>" name="txtgmtssize[]" type="text" class="text_boxes" value="<? echo $size_arr[$row['gmts_size_id']]; ?>" style="width:60px" placeholder="Display" disabled/>
						<input id="txtgmtssizeID_<? echo $tblRow; ?>" name="txtgmtssizeID[]" type="hidden" class="text_boxes" value="<? echo $row['gmts_size_id']; ?>" style="width:60px" placeholder="Display" disabled/></td>
	                <td><input id="txtcolor_<? echo $tblRow; ?>" name="txtcolor[]" type="text" class="text_boxes" value="<? echo $color_library[$row['color_id']]; ?>" style="width:60px" placeholder="Display" disabled/>
						<input id="txtcolorID_<? echo $tblRow; ?>" name="txtcolorID[]" type="hidden" class="text_boxes" value="<? echo $row['color_id']; ?>" style="width:60px" placeholder="Display" disabled/>
	                </td>
	                <td><input id="txtsize_<? echo $tblRow; ?>" name="txtsize[]" type="text" class="text_boxes" value="<? echo $size_arr[$row['size_id']]; ?>" style="width:60px" placeholder="Display" disabled/>
						<input id="txtsizeID_<? echo $tblRow; ?>" name="txtsizeID[]" type="hidden" class="text_boxes" value="<? echo $row['size_id']; ?>" style="width:60px" placeholder="Display" disabled/>
	                </td>
	                <td id="subSectionTd_<? echo $tblRow; ?>"><? echo create_drop_down( "cboSubSection_".$tblRow, 70, $trims_sub_section,"", 1, "-- Select Section --", $row['sub_section'],"",1,'','','','','','',"cboSubSection[]"); ?></td>
	                <td><? echo create_drop_down( "cboUom_".$tblRow, 60, $unit_of_measurement,"", 1, "-- Select --",$row['uom'],1, 1,'','','','','','',"cboUom[]"); ?>	</td>
	                 <td ><input id="txtConvFactor_<? echo $tblRow; ?>" name="txtConvFactor[]" type="text" class="text_boxes_numeric" style="width:57px" value="<? echo $row['conv_factor']; ?>" readonly placeholder="Display"/></td>
	                <td ><input id="txtJobQuantity_<? echo $tblRow; ?>" name="txtJobQuantity[]" class="text_boxes_numeric" type="text" value="<? echo $row['job_quantity']; ?>" readonly style="width:67px" /></td>
					<td><input id="txtRawMat_<? echo $tblRow; ?>" name="txtRawMat[]" type="text" class="text_boxes" value="<? echo chop($row['description'],','); ?>" style="width:80px"  onClick="openmypage_row_metarial(2,'0',<? echo $tblRow; ?>)" placeholder="Double Click"/></td>
					<td><input id="txtImpression_<? echo $tblRow; ?>" name="txtImpression[]" type="text" class="text_boxes_numeric" value="<? echo $row['impression']; ?>" style="width:80px" placeholder="Write"/></td>
					<td><input id="txtRawcolor_<? echo $tblRow; ?>" name="txtRawcolor[]" type="text" class="text_boxes" value="<? echo $row['material_color']; ?>"  onClick="open_color(1,<? echo $tblRow; ?>)" style="width:80px" placeholder="Click"/>
						<input id="hdnRawcolor_<? echo $tblRow; ?>" name="hdnRawcolor[]" type="hidden" class="text_boxes" value="<? echo $row['material_color']; ?>" />
	                	<input type="hidden" name="hdnDtlsUpdateId[]" id="hdnDtlsUpdateId_<? echo $tblRow; ?>" value="<? echo $row['id']; ?>">
	                	<input type="hidden" name="hdnRecDtlsIDs[]" id="hdnRecDtlsIDs_<? echo $tblRow; ?>" value="<? echo $row['receive_dtls_id']; ?>">
	                	<input type="hidden" name="hdnbookingDtlsId[]" id="hdnbookingDtlsId_<? echo $tblRow; ?>" value="<? echo chop($row['booking_dtls_id'],","); ?>">
	                    <input type="hidden" name="bookConDtlsId[]" id="bookConDtlsId_<? echo $tblRow; ?>" value="<? echo chop($row['book_con_dtls_id'],","); ?>">
	                    <input type="hidden" name="hdnDtlsdata[]" id="hdnDtlsdata_<? echo $tblRow; ?>" value="<? echo chop($row['break_data'],"**"); ?>" >
	                    <input type="hidden" name="txtCopyChk[]" id="txtCopyChk_<? echo $tblRow; ?>" value="<? echo $row['is_copy_material']; ?>" >
	                    <input type="hidden" name="hdnBreakIDs[]" id="hdnBreakIDs_<? echo $tblRow; ?>" value="<? echo chop($row['break_id'],","); ?>">
	                    <input type="hidden" name="hdnProdId[]" id="hdnProdId_<? echo $tblRow; ?>" value="<? echo $row['prodDtls_id']; ?>">
	                    <input type="hidden" name="hdnBrkDelId[]" id="hdnBrkDelId_<? echo $tblRow; ?>" value="0">
	                </td>
				</tr>
				<?
			}
		}
		
		//echo "<pre>";
		//print_r($rcvItemChk_arr); die; 
		/************  FOR REVISED ITEM ******************/
		$buyer_po_sql = sql_select("select id, buyer_po_no, buyer_style_ref,order_uom from subcon_ord_dtls  where status_active=1");
		foreach($buyer_po_sql as $row) //subcon_ord_mst subcon_ord_dtls subcon_ord_breakdown
		{
			$buyer_po_arr[$row[csf('id')]]['style']=$row[csf('buyer_style_ref')];
			$buyer_po_arr[$row[csf('id')]]['buyerpo']=$row[csf('buyer_po_no')];
			$buyer_po_arr[$row[csf('id')]]['order_uom']=$row[csf('order_uom')];
		}
		unset($buyer_po_sql);
		/*if($db_type==0) $id_cond.=" ,group_concat(a.mst_id) as mst_id";
		else if($db_type==2) $id_cond.=" ,rtrim(xmlagg(xmlelement(e,a.mst_id,',').extract('//text()') order by a.id).GetClobVal(),',') as mst_id";
		if($db_type==0) $id_cond.=" ,group_concat(a.id) as id";
		else if($db_type==2) $id_cond.=" ,rtrim(xmlagg(xmlelement(e,a.id,',').extract('//text()') order by a.id).GetClobVal(),',') as id";
		if($db_type==0) $id_cond.=" ,group_concat(a.book_con_dtls_id) as book_con_dtls_id";
		else if($db_type==2) $id_cond.=" ,rtrim(xmlagg(xmlelement(e,a.book_con_dtls_id,',').extract('//text()') order by a.id).GetClobVal(),',') as book_con_dtls_id";*/
		
		$dtlsIda=str_replace("_",",",$data[5]);
		$brk_sql= "select a.description,a.color_id,a.size_id,a.mst_id,a.id,a.book_con_dtls_id,b.item_group from subcon_ord_breakdown a, subcon_ord_dtls b where b.id=a.mst_id and a.mst_id in ($dtlsIda) and job_no_mst='$data[6]' and a.status_active=1 "; 

		$brk_array=sql_select($brk_sql); $subconBrk_arr=array(); $mstIDS=''; $ids=''; $book_con_dtls_ids='';
		foreach ($brk_array as  $row) 
		{
			//$mstIDS 			=$row[csf("mst_id")]->load(); 
			//$ids 				=$row[csf("id")]->load(); 
			//$book_con_dtls_ids 	=$row[csf("book_con_dtls_id")]->load(); 
			//echo $mstIDS.'=='.$ids.'=='.$book_con_dtls_ids.'++'; 
			$subconBrk_arr[$row[csf("item_group")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]["description"] =$row[csf("description")];
			$subconBrk_arr[$row[csf("item_group")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]["color_id"] =$row[csf("color_id")];
			$subconBrk_arr[$row[csf("item_group")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]["size_id"] =$row[csf("size_id")];
			$subconBrk_arr[$row[csf("item_group")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]["mst_id"] .= $row[csf("mst_id")].',';
			$subconBrk_arr[$row[csf("item_group")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]["id"] .= $row[csf("id")].',';
			$subconBrk_arr[$row[csf("item_group")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]["book_con_dtls_id"] .= $row[csf("book_con_dtls_id")].',';
		}

		$dtls_sql= "select a.description,a.color_id,a.size_id,b.sub_section,b.booked_uom,b.booked_conv_fac,b.item_group, sum(a.booked_qty) as qnty  $dtlsid_cond from subcon_ord_dtls b ,subcon_ord_breakdown a where a.mst_id=b.id and b.order_quantity<>0 and a.qnty<>0  and a.status_active=1  and b.mst_id in ($data[3]) and section=$data[4] and b.job_no_mst=a.job_no_mst group by b.sub_section,b.booked_uom,b.booked_conv_fac,a.description,a.color_id,a.size_id,b.item_group "; //die;
		$data_array=sql_select($dtls_sql); $subconDtls_arr=array(); 
		foreach ($data_array as  $rows) 
		{

			$subconDtls_arr[$rows[csf("sub_section")]][$rows[csf("item_group")]][$rows[csf("booked_uom")]][$rows[csf("booked_conv_fac")]][$rows[csf("description")]][$rows[csf("color_id")]][$rows[csf("size_id")]]["qnty"] +=$rows[csf("qnty")];
		}
		//$brk_sql= "select a.description,a.color_id,a.size_id $id_cond from subcon_ord_breakdown a where job_no_mst='$data[5]'  and a.status_active=1 group by a.description,a.color_id,a.size_id "; 
		
		if($db_type==0) $dtlsid_cond.=" ,group_concat(b.booking_dtls_id) as booking_dtls_id";
		else if($db_type==2) $dtlsid_cond.=" ,rtrim(xmlagg(xmlelement(e,b.booking_dtls_id,',').extract('//text()') order by b.id).GetClobVal(),',') as booking_dtls_id";
		if($db_type==0) $dtlsid_cond.=" ,group_concat(a.buyer_po_id) as buyer_po_id";
		else if($db_type==2) $dtlsid_cond.=" ,rtrim(xmlagg(xmlelement(e,b.buyer_po_id,',').extract('//text()') order by b.id).GetClobVal(),',') as buyer_po_id";

		/*$brk_array=sql_select($brk_sql); $subconBrk_arr=array(); $mstIDS=''; $ids=''; $book_con_dtls_ids='';
		foreach ($brk_array as  $row) 
		{
			$mstIDS 			=$row[csf("mst_id")]->load(); 
			$ids 				=$row[csf("id")]->load(); 
			$book_con_dtls_ids 	=$row[csf("book_con_dtls_id")]->load(); 
			//echo $mstIDS.'=='.$ids.'=='.$book_con_dtls_ids.'++'; 
			$subconBrk_arr[$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]["description"] =$row[csf("description")];
			$subconBrk_arr[$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]["color_id"] =$row[csf("color_id")];
			$subconBrk_arr[$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]["size_id"] =$row[csf("size_id")];
			$subconBrk_arr[$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]["mst_id"] = $mstIDS;
			$subconBrk_arr[$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]["id"] = $ids;
			$subconBrk_arr[$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]["book_con_dtls_id"] = $book_con_dtls_ids;
		}*/
		if(1==2) // for revised item **** rather use*****
		{
			$revised_dtls_sql= "select a.description,a.color_id,a.size_id,b.sub_section,b.booked_uom,b.booked_conv_fac,b.item_group, sum(a.booked_qty) as qnty  $dtlsid_cond from subcon_ord_dtls b ,subcon_ord_breakdown a where a.mst_id=b.id and b.order_quantity<>0 and a.qnty<>0  and b.mst_id in ($data[3]) and section=$data[4] and b.job_no_mst=a.job_no_mst  and a.status_active=1 group by b.sub_section,b.booked_uom,b.booked_conv_fac,a.description,a.color_id,a.size_id,b.item_group";
			$revised_array=sql_select($revised_dtls_sql);
			foreach ($revised_array as  $rows) 
			{
				//echo 'nnn';
				//$booking_dtls_ids= $rows['booking_dtls_id']->load();
				$booking_dtls_ids=$rows[csf("booking_dtls_id")]->load();
				$buyer_po_id=$rows[csf("buyer_po_id")]->load();
				$subconDtls_arr[$rows[csf("sub_section")]][$rows[csf("item_group")]][$rows[csf("booked_uom")]][$rows[csf("booked_conv_fac")]][$rows[csf("description")]][$rows[csf("color_id")]][$rows[csf("size_id")]]["qnty"] +=$rows[csf("qnty")];
				$subconDtls_arr[$rows[csf("sub_section")]][$rows[csf("item_group")]][$rows[csf("booked_uom")]][$rows[csf("booked_conv_fac")]][$rows[csf("description")]][$rows[csf("color_id")]][$rows[csf("size_id")]]["booking_dtls_ids"] =$booking_dtls_ids;
				$subconDtls_arr[$rows[csf("sub_section")]][$rows[csf("item_group")]][$rows[csf("booked_uom")]][$rows[csf("booked_conv_fac")]][$rows[csf("description")]][$rows[csf("color_id")]][$rows[csf("size_id")]]["buyer_po_id"] =$buyer_po_id;
			}
			//echo "<pre>";
			//print_r($rcvItemChk_arr); //die; 
			$a=1;
			foreach($subconDtls_arr as $subSection_id=> $subSection_data)
			{
				foreach($subSection_data as $item_group_id=> $item_group_data)
				{
					foreach($item_group_data as $booked_uom=> $booked_uom_data)
					{ 
						foreach($booked_uom_data as $booked_conv_fac=> $conv_fac_data)
						{
							foreach($conv_fac_data as $description=> $description_data)
							{ 
								foreach($description_data as $color_id=> $color_data)
								{
									//$mst_ids='';
									$revisedItem='';
									foreach($color_data as $size_id => $row)
									{
										$revisedItem=$subSection_id."_".$booked_uom."_".$booked_conv_fac."_".$description."_".$color_id."_".$size_id;
										//if($revisedItem !inArray)
										

										if (!in_array($revisedItem, $rcvItemChk_arr))
										{
											//echo $revisedItem.'==';
											$ids=$subconBrk_arr[$description][$color_id][$size_id]['id'];
											$mstIDS=$subconBrk_arr[$description][$color_id][$size_id]['mst_id'];
											$bookConDtls_Ids=$subconBrk_arr[$description][$color_id][$size_id]['book_con_dtls_id'];
											//$booking_dtls_ids=$subconBrk_arr[$description][$color_id][$size_id]['booking_dtls_id'];
											
											if($mstIDS!='')
											{
												$dtls_ids=explode(",",$mstIDS);
												$buyer_po=''; $buyer_style=''; $order_uom='';
												for ($i=0; $i <count($dtls_ids) ; $i++) 
												{ 
													$buyer_po.=$buyer_po_arr[$dtls_ids[$i]]['buyerpo'].",";
													$buyer_style.=$buyer_po_arr[$dtls_ids[$i]]['style'].",";
												}
											}
 											$tblRow++;
											
											$buyer_po=chop(implode(',',array_unique(explode(",",$buyer_po))),',');
			   								$buyer_style=chop(implode(',',array_unique(explode(",",$buyer_style))),',');
			    							$buyer_po_id=chop(implode(',',array_unique(explode(",",$buyer_po_id))),',');
											
											if ($tblRow%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
											?>
											<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
												<td title="<? echo $buyer_po; ?>"><input id="txtbuyerPo_<? echo $tblRow; ?>" name="txtbuyerPo[]"   class="text_boxes" type="text" value="<? echo $buyer_po; ?>" style="width:100px" disabled />
													<input id="txtbuyerPoId_<? echo $tblRow; ?>" name="txtbuyerPoId[]" value="<? echo $buyer_po_id; ?>" class="text_boxes" type="hidden" style="width:70px" disabled />
												</td>
								                <td title="<? echo $buyer_style; ?>"><input id="txtstyleRef_<? echo $tblRow; ?>" name="txtstyleRef[]" type="text" class="text_boxes" value="<? echo $buyer_style; ?>"  style="width:100px" placeholder="Display" disabled/></td>
								                <td title="<? echo $row['item_description']; ?>"><input id="txtdescription_<? echo $tblRow; ?>" name="txtdescription[]" type="text" class="text_boxes" value="<? echo $description; ?>"  style="width:100px" title="<? echo $row['item_description']; ?>" placeholder="Display" disabled/></td>
								                <td><input id="txtcolor_<? echo $tblRow; ?>" name="txtcolor[]" type="text" class="text_boxes" value="<? echo $color_library[$color_id]; ?>" style="width:100px" placeholder="Display" disabled/>
													<input id="txtcolorID_<? echo $tblRow; ?>" name="txtcolorID[]" type="hidden" class="text_boxes" value="<? echo $color_id; ?>" style="width:100px" placeholder="Display" disabled/>
								                </td>
								                <td><input id="txtsize_<? echo $tblRow; ?>" name="txtsize[]" type="text" class="text_boxes" value="<? echo $size_arr[$size_id]; ?>" style="width:100px" placeholder="Display" disabled/>
													<input id="txtsizeID_<? echo $tblRow; ?>" name="txtsizeID[]" type="hidden" class="text_boxes" value="<? echo $size_id; ?>" style="width:100px" placeholder="Display" disabled/>
								                </td>
								                <td id="subSectionTd_<? echo $tblRow; ?>"><? echo create_drop_down( "cboSubSection_".$tblRow, 70, $trims_sub_section,"", 1, "-- Select Section --", $subSection_id,"",1,'','','','','','',"cboSubSection[]"); ?></td>
								                <td><? echo create_drop_down( "cboUom_".$tblRow, 70, $unit_of_measurement,"", 1, "-- Select --",$booked_uom,1, 1,'','','','','','',"cboUom[]"); ?>	</td>
								                 <td ><input id="txtConvFactor_<? echo $tblRow; ?>" name="txtConvFactor[]" type="text" class="text_boxes_numeric" style="width:57px" value="<? echo $booked_conv_fac; ?>" readonly placeholder="Display"/></td>
								                <td ><input id="txtJobQuantity_<? echo $tblRow; ?>" name="txtJobQuantity[]" class="text_boxes_numeric" type="text" value="<? echo number_format($row['qnty'],4); ?>" readonly style="width:67px" /></td>
												<td><input id="txtRawMat_<? echo $tblRow; ?>" name="txtRawMat[]" type="text" class="text_boxes" value="" style="width:100px"  onClick="openmypage_row_metarial(1,'0',<? echo $tblRow; ?>)" placeholder="Double Click"/></td>
												<td><input id="txtImpression_<? echo $tblRow; ?>" name="txtImpression[]" type="text" class="text_boxes_numeric" value="" style="width:100px" placeholder="Write"/></td>
												<td><input id="txtRawcolor_<? echo $tblRow; ?>" name="txtRawcolor[]" type="text" class="text_boxes" value=""  onClick="open_color(1,<? echo $tblRow; ?>)" style="width:100px" placeholder="Click"/>
													<input id="hdnRawcolor_<? echo $tblRow; ?>" name="hdnRawcolor[]" type="hidden" class="text_boxes" value="" />
								                	<input type="hidden" name="hdnDtlsUpdateId[]" id="hdnDtlsUpdateId_<? echo $tblRow; ?>" value="">
								                	<input type="hidden" name="hdnRecDtlsIDs[]" id="hdnRecDtlsIDs_<? echo $tblRow; ?>" value="<? echo $mstIDS; ?>">
								                	<input type="hidden" name="hdnbookingDtlsId[]" id="hdnbookingDtlsId_<? echo $tblRow; ?>" value="<? echo $row['booking_dtls_ids']; ?>">
								                    <input type="hidden" name="bookConDtlsId[]" id="bookConDtlsId_<? echo $tblRow; ?>" value="<? echo $bookConDtls_Ids; ?>">
								                    <input type="hidden" name="hdnDtlsdata[]" id="hdnDtlsdata_<? echo $tblRow; ?>" value="" >
								                    <input type="hidden" name="txtCopyChk[]" id="txtCopyChk_<? echo $tblRow; ?>" value="0" >
								                    <input type="hidden" name="hdnBreakIDs[]" id="hdnBreakIDs_<? echo $tblRow; ?>" value="<? echo $ids; ?>">
								                    <input type="hidden" name="hdnProdId[]" id="hdnProdId_<? echo $tblRow; ?>" value="0">
								                    <input type="hidden" name="hdnBrkDelId[]" id="hdnBrkDelId_<? echo $tblRow; ?>" value="0">
								                    
								                </td>
											</tr>
											<?
										}
										//$a++;
										//echo $mstIDS.'=';
										//round($row['qnty'])
										//$wo_data .= chop($buyer_po,",")."**".chop($buyer_po_id,",")."**".chop($buyer_style,",")."**".$description."**".$color_library[$color_id]."**".$color_id."**".$size_arr[$size_id]."**".$size_id."**".$subSection_id."**".$booked_uom."**".$booked_conv_fac."**".number_format($row['qnty'],4)."**".$mstIDS."**".$row['booking_dtls_ids']."**".$bookConDtls_Ids."**".$ids."#";

									}
								}
							}
						}
					}
				}
			}
		}
		
	}
	exit();
}
if($action=="row_metarial_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	
	//echo $cboUom."nazim"; die;
    ?>
    <script>
    	var str_color = [<? echo substr(return_library_autocomplete( "select color_name from lib_color group by color_name ", "color_name" ), 0, -1); ?> ];
    	var str_size = [<? echo substr(return_library_autocomplete( "select size_name from lib_size group by size_name ", "size_name" ), 0, -1); ?> ]; 
		var chkBoxVal= <? echo $txtCopyChk;  ?>; 
		var hdnBrkDelId= <? echo $hdnBrkDelId;  ?>; 
		var dtlsUpdateId= <? echo $hdnDtlsUpdateId;  ?>; 
		//alert(hdnDtlsUpdateId); //return;
		function add_share_row( i ) 
		{
			//var row_num=$('#tbl_share_details_entry tbody tr').length-1;
			var row_num=$('#tbl_share_details_entry tbody tr').length;
			if (row_num!=i)
			{
				return false;
			}
			i++;
			$("#tbl_share_details_entry tbody tr:last").clone().find("input,select").each(function() {
				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					'name': function(_, name) { return name + i },
					'value': function(_, value) { return value }              
				});
			}).end().appendTo("#tbl_share_details_entry tbody");
			$('#increaseset_'+i).removeAttr("onClick").attr("onClick","add_share_row("+i+");");
			$('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");
			$('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");

			$('#txtdescription_'+i).val('');
			$('#txtSpecification_'+i).val('');
			$('#txtUnit_'+i).val('');
			$('#txtUnitPcs_'+i).val('');
			$('#txtConsQty_'+i).val('');
			$('#txtProcessLoss_'+i).val('');
			$('#txtProcessLossQty_'+i).val('');
			$('#txtReqQty_'+i).val('');
			$('#txtRemarks_'+i).val('');
			$('#hiddenid_'+i).val('');
			$('#hiddenProdid_'+i).val('');
			$('#hiddenProdlot_'+i).val('');
			
			set_all_onclick();
		}		
		
		function fn_deletebreak_down_tr(rowNo) 
		{ 
			var numRow = $('table#tbl_share_details_entry tbody tr').length;
			var hiddenProdid=$('#hiddenProdid_'+rowNo).val(); 
			if(numRow!=1 && hiddenProdid!='')
			{
				var updateIdDtls=$('#hiddenid_'+rowNo).val();
				var txtDeletedId=$('#txtDeletedId').val();
				var selected_id='';
				if(updateIdDtls!='')
				{
					if(txtDeletedId=='') selected_id=updateIdDtls; else selected_id=txtDeletedId+','+updateIdDtls;
					$('#txtDeletedId').val( selected_id );
				}
				$("#row_"+rowNo).remove();
				/*$('#tbl_share_details_entry tbody tr:last').remove();*/
			}
			else
			{
				return false;
			}
			//sum_total_qnty(rowNo);
		}

		function fnc_close()
		{
			var tot_row=$('#tbl_share_details_entry tbody tr').length-1;
			//alert(tot_row);
			if(tot_row<1){
				alert('Please Chose any Item');return;
			}
			else
			{
				var data_break_down="";
				check_field=0;
				//alert(check_field+'iii');
				$("#tbl_share_details_entry tbody tr").each(function()
				{
					//alert(check_field+'hhh');
					var txtdescription 		= $(this).find('input[name="txtdescription[]"]').val();
					var txtSpecification 	= $(this).find('input[name="txtSpecification[]"]').val();
					var txtUnit 			= $(this).find('select[name="txtUnit[]"]').val();
					var txtUnitPcs 			= $(this).find('input[name="txtUnitPcs[]"]').val()*1;
					var txtConsQty 			= $(this).find('input[name="txtConsQty[]"]').val()*1;
					var txtProcessLoss 		= $(this).find('input[name="txtProcessLoss[]"]').val()*1;
					var txtProcessLossQty 	= $(this).find('input[name="txtProcessLossQty[]"]').val()*1;
					var txtReqQty 			= $(this).find('input[name="txtReqQty[]"]').val()*1;
					var txtRemarks 			= $(this).find('input[name="txtRemarks[]"]').val();
					var hiddenid 			= $(this).find('input[name="hiddenid[]"]').val();
					var hiddenProdid 		= $(this).find('input[name="hiddenProdid[]"]').val();
					var hiddenProdLot 		= $(this).find('input[name="hiddenProdlot[]"]').val();
					//alert(txtConsQty);
					//var txtdescription 		= $(this).find('input[name="txtdescription[]"]').val();
					
					if((txtdescription !='' &&  txtdescription!=0) && (txtConsQty=='' ||  txtConsQty==0))
					{
						alert('Please Fill up Cons. Qty.');
						check_field=1 ; return;
						
					}

					//alert(check_field+"b");
					if((txtdescription!='' && txtdescription!=0) && check_field ==0)
					{
						if(data_break_down=="")
						{
							data_break_down+=txtdescription+'_'+txtSpecification+'_'+txtUnit+'_'+txtUnitPcs+'_'+txtConsQty+'_'+txtProcessLoss+'_'+txtProcessLossQty+'_'+txtReqQty+'_'+txtRemarks+'_'+hiddenid+'_'+hiddenProdid+'_'+hiddenProdLot;
						}
						else
						{
							data_break_down+="**"+txtdescription+'_'+txtSpecification+'_'+txtUnit+'_'+txtUnitPcs+'_'+txtConsQty+'_'+txtProcessLoss+'_'+txtProcessLossQty+'_'+txtReqQty+'_'+txtRemarks+'_'+hiddenid+'_'+hiddenProdid+'_'+hiddenProdLot;
						}
					}
				});
				//alert(data_break_down+"c");
				if(check_field==0)
				{
					
					var copy_basis=$('input[name="copy_basis"]:checked').val();
					$('#is_copy').val( copy_basis );
					//alert(copy_basis+'kkk');
					/*if ($('#is_copy').is(":checked"))
					{
					  	$('#is_copy').val( 1 );
					}
					else
					{
						$('#is_copy').val( 2 );
					}*/
					$('#hidden_break_tot_row').val( data_break_down );
					parent.emailwindow.hide();
				}
			}
			
		}

		function create_description_row(prod_ids,id,row,company)
	    {
			
	    	if(id!=0 && id!='')
	    	{
				
				//$("#row_"+row).remove();
				var row_num =  $('#tbl_share_details_entry tbody tr').length;
				var response_data = return_global_ajax_value(prod_ids + "**" + row_num + "**" + id + "**" + company, 'populate_prod_data', '', 'Job_card_preparation_controller');
				$("#tbl_share_details_entry tbody:last").append(response_data);
	    	}
	    	else
	    	{
	    		var row_num =  $('#tbl_share_details_entry tbody tr').length; //$('#txt_tot_row').val();
		        var response_data = return_global_ajax_value(prod_ids + "**" + row_num + "**" + company + "**" + company, 'populate_prod_data', '', 'Job_card_preparation_controller');
		        $('#tbl_share_details_entry tbody').prepend(response_data);
		        var tot_row = $('#tbl_share_details_entry tbody tr').length;
	    	}
	       // freeze_window(5); //release_freezing();
	    }

		function openmypage_material(data)
		{
			//alert(data);
			page_link='Job_card_preparation_controller.php?action=material_description_popup&data='+data;
			title='Product List';
			emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=830px, height=400px, center=1, resize=0, scrolling=0','../../')
			var datas=(data).split('_');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]
				var theemailprod=this.contentDoc.getElementById("all_ids").value;
				if (theemailprod!="")
				{
					create_description_row(theemailprod,datas[1],datas[2],datas[0]);
				}
			}
		}

		function metarial_calculate(row)
		{
			var order_qty ='<? echo $txtJobQuantity ?>';
			//var prev_qty = $('#txtConsQty_'+row).getAttribute("placeholder")*1; 
			var prev_qty = $("#txtConsQty_"+row).attr('placeholder')*1;
			var cons_qty=$('#txtConsQty_'+row).val()*1;
			//var cons_qty =order_qty;
			//$('#txtConsQty_'+row).val(cons_qty.toFixed(2));
			//alert(prev_qty+'=='+cons_qty);
			if(prev_qty!='' && prev_qty!=0){
				if(prev_qty>cons_qty){
					alert('Req. Found cant reduce Cons/Unit'); 
					$('#txtConsQty_'+row).val(prev_qty);
					return;
				}
			}
			
			//var cons_qty=1;
			var txtProcessLoss=$('#txtProcessLoss_'+row).val()*1;
			
			var processLossQty=(cons_qty*txtProcessLoss)/100;
			var reqQty=processLossQty+cons_qty;
			$('#txtProcessLossQty_'+row).val(processLossQty.toFixed(4));
			$('#txtReqQty_'+row).val(reqQty);

			//alert(order_qty);
			/*var unitPcs=$('#txtUnitPcs_'+row).val()*1;
			if(unitPcs==0)unitPcs=1;
			var cons_qty =order_qty/unitPcs;*/
			/*var cons_qty =order_qty;
			$('#txtConsQty_'+row).val(cons_qty.toFixed(2));
			process_calculate(row);*/
		}

		function process_calculate(row)
		{
			var txtProcessLoss=$('#txtProcessLoss_'+row).val()*1;
			var prev_process_loss = $("#txtProcessLoss_"+row).attr('placeholder')*1;
			//var cons_qty =order_qty;
			//$('#txtConsQty_'+row).val(cons_qty.toFixed(2));

			if(prev_process_loss!='' && prev_process_loss!=0){
				if(prev_process_loss>txtProcessLoss){
					alert('Req. Found cant reduce Process Loss %'); 
					$('#txtConsQty_'+row).val(prev_process_loss);
					return;
				}
			}
			var cons_qty=$('#txtConsQty_'+row).val()*1;
			var processLossQty=(cons_qty*txtProcessLoss)/100;
			var reqQty=processLossQty+cons_qty;
			$('#txtProcessLossQty_'+row).val(processLossQty.toFixed(4));
			$('#txtReqQty_'+row).val(reqQty);
		}
	</script>
	</head>
	<body>
		<div align="center" style="width:100%;" >
			<form name="qntypopup_1"  id="qntypopup_1" autocomplete="off">
				<table class="rpt_table" width="700px" cellspacing="0" cellpadding="0" rules="all" id="tbl_share_details_entry">
					<thead>
						<tr>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>Booked UOM</th>
							<th> 
								<? 
									echo create_drop_down( "txtUnit_".$tblRow, 80, $unit_of_measurement,"", 1, "-- Select --",$cboUom,"", 1,'','','','','','',"txtUnit[]");
								?>
							<th colspan="6" >
				                <input type="radio" name="copy_basis" id="copy_0" value="0" checked >No Copy 
				                <input type="radio" name="copy_basis" id="copy_1" value="1">Copy Item Wise
				                <input type="radio" name="copy_basis" id="copy_2" value="2" >Copy to All
							</th>	
						</tr>
						<tr>
							<th rowspan="2" width="30">Sl.</th>
							<th rowspan="2" width="130">Description</th>
							<th rowspan="2" width="100">Specification</th>
							<th rowspan="2" width="80" >Cons Uom</th>
							<th rowspan="2" width="70" style="display: none;" >Pcs/ Unit</th>
							<th rowspan="2" width="60" class="must_entry_caption">Cons/Unit</th>
							<th rowspan="2" width="80">Process Loss %</th>
							<th rowspan="2" width="80">Process Loss Qty.</th>
							<th rowspan="2" width="80">Total Cons/Unit</th>
							<th rowspan="2" width="80">Remarks</th>
							<th rowspan="2"></th>
						</tr>
					</thead>
					<tbody id="description_list_view">
						<input type="hidden" name="txtDeletedId" id="txtDeletedId" class="text_boxes_numeric" style="width:90px" readonly />
						<input type="hidden" name="hidden_break_tot_row" id="hidden_break_tot_row" class="text_boxes" style="width:90px" />
						<input type="hidden" name="is_copy" id="is_copy" class="text_boxes" style="width:90px" />
	                    <? //echo $data_dreak;
	                    $k=0; 
	                    if($is_requisition_done !='' && $is_requisition_done !=0 ){
	                    	$sqlBreak_result =sql_select("select  a.id, a.mst_id, a.order_id, a.job_no_mst, a.book_con_dtls_id, a.product_id, a.description, a.specification, a.pcs_unit, a.cons_qty, a.process_loss, a.process_loss_qty, a.req_qty, a.remarks,b.unit_of_measure as unit from trims_job_card_breakdown a , product_details_master b where a.product_id=b.id and b.entry_form in (334,220,300,285,0) and a.status_active=1 and a.is_deleted=0 and a.is_deleted=0 and a.mst_id=$hdnDtlsUpdateId");
	                    	foreach($sqlBreak_result as $row)
							{
								$prev_item_arr[$row[csf('id')]]['product_id']=$row[csf('product_id')];
								$prev_item_arr[$row[csf('id')]]['pcs_unit']=$row[csf('pcs_unit')];
								$prev_item_arr[$row[csf('id')]]['cons_qty']=$row[csf('cons_qty')];
								$prev_item_arr[$row[csf('id')]]['process_loss']=$row[csf('process_loss')];
								$prev_item_arr[$row[csf('id')]]['process_loss_qty']=$row[csf('process_loss_qty')];
								$prev_item_arr[$row[csf('id')]]['req_qty']=$row[csf('req_qty')];
							}

							//echo "select b.product_id, sum(b.requisition_qty) as total_requisition_qty   from trims_raw_mat_requisition_mst a, trims_raw_mat_requisition_dtls b where a.id=b.mst_id and a.job_id='$job_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   group by b.product_id";
							$requisitiondataSql = sql_select("select b.product_id, sum(b.requisition_qty) as total_requisition_qty   from trims_raw_mat_requisition_mst a, trims_raw_mat_requisition_dtls b
							where a.id=b.mst_id and a.job_id='$job_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   group by b.product_id");
							$requisition_data_arr = array(); 
							foreach ($requisitiondataSql as $row) 
							{
								$requisition_data_arr[$row[csf('product_id')]]['requisition_qty'] += $row[csf('total_requisition_qty')];
								//$updaterequisitionQtyArr[$row[csf('id')]]['requisition_qty'] += $row[csf('total_requisition_qty')];
						 	}

	                    }
	                    //echo $data_break."==="; die;
	                    if($data_break!=''){
	                    	$data_array=explode("**",$data_break);
							$count_dtls_data=count($data_array);
	                    }else{
	                    	$count_dtls_data=0;
	                    }
						//echo $count_dtls_data; die;
						if($count_dtls_data>0)
						{
							$k++;
							?>
							<tr id="row_<? echo $k;?>">
								<td><input type="text" id="txtSl_<? echo $k;?>" name="txtSl_<? echo $k;?>" class="text_boxes" readonly style="width:30px" value="<? echo $k; ?>" />
								</td>
								<td><input type="text" id="txtdescription_<? echo $k;?>" name="txtdescription[]" class="text_boxes" readonly onClick='openmypage_material("<? echo $company.'_0_'.$k.'_'.$cbo_section; ?>")' placeholder="Click" style="width:120px" value="" />
									<input type="hidden" id="hiddenProdid_<? echo $k; ?>" name="hiddenProdid[]"  style="width:15px;" class="text_boxes" value="" readonly  />
                                    <input type="hidden" id="hiddenProdlot_<? echo $k; ?>" name="hiddenProdlot[]" value=""  style="width:15px;" class="text_boxes" readonly />
								</td>
								<td>
									<input type="text" id="txtSpecification_<? echo $k;?>" name="txtSpecification[]" class="text_boxes" style="width:90px" value="" <? echo $disabled; ?> readonly /></td>
								<td>
									<?
										echo create_drop_down( "txtUnit_".$tblRow, 80, $unit_of_measurement,"", 1, "-- Select --",'',"", 1,'','','','','','',"txtUnit[]");
									?>	
								</td>
								<td style="display: none;" >
									<input type="text" id="txtUnitPcs_<? echo $k;?>" name="txtUnitPcs[]" class="text_boxes_numeric" style="width:60px" onKeyUp="metarial_calculate(<? echo $k;?>)"  value="" <? echo $disabled; ?> />
								</td>
								<td><input type="text" id="txtConsQty_<? echo $k;?>" name="txtConsQty[]"  class="text_boxes_numeric" style="width:50px" onKeyUp="metarial_calculate(<? echo $k;?>)" value="" <? echo $disabled; ?> /></td>
								<td>
									<input type="text" id="txtProcessLoss_<? echo $k;?>" name="txtProcessLoss[]" class="text_boxes_numeric"  onKeyUp="process_calculate(<? echo $k;?>)"   style="width:70px" value=""  />
								</td>
								<td><input type="text" id="txtProcessLossQty_<? echo $k;?>" name="txtProcessLossQty[]" class="text_boxes_numeric" style="width:70px" value="" readonly /></td>
								<td><input type="text" id="txtReqQty_<? echo $k;?>" name="txtReqQty[]" class="text_boxes_numeric" style="width:70px" value="" disabled/></td>
								<td><input type="text" id="txtRemarks_<? echo $k;?>" name="txtRemarks[]" class="text_boxes" style="width:70px" value=""/></td>
								<td>
									<input type="hidden" id="hidbookingconsid_<? echo $k; ?>" name="hidbookingconsid[]"  style="width:15px;" class="text_boxes" value="" />
	                                <input type="hidden" id="hiddenid_<? echo $k; ?>" name="hiddenid[]"  style="width:15px;" class="text_boxes" value="" />
									<input type="button" id="decreaseset_<? echo $k;?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $k;?> ,'tbl_share_details_entry' );" <? echo $disabled; ?>/>
								</td>  
							</tr>
							<?
							foreach($data_array as $row)
							{
								$data=explode('_',$row);
								$req_qty=$prev_process_loss=$prev_cons_unit=$req_disabled='';
								if($is_requisition_done !='' && $is_requisition_done !=0 ){
									$req_qty=$requisition_data_arr[$data[10]]['requisition_qty'];
									$prev_process_loss=$prev_item_arr[$data[9]]['process_loss'];
									$prev_cons_unit=$prev_item_arr[$data[9]]['cons_qty'];
									if($disabled==''){
										//$req_disabled='disabled';
									} 
								}
								//echo $prev_cons_unit; die;
								$k++;
								?>
								<tr id="row_<? echo $k;?>">
									<td><input type="text" id="txtSl_<? echo $k;?>" name="txtSl_<? echo $k;?>" class="text_boxes" style="width:30px" value="<? echo $k; ?>" readonly />
									</td>
									<td><input type="text" id="txtdescription_<? echo $k;?>" name="txtdescription[]" class="text_boxes" placeholder="Double Click" style="width:120px" value="<? echo $data[0]; ?>" readonly />
										<input type="hidden" id="hiddenProdid_<? echo $k; ?>" name="hiddenProdid[]"  style="width:15px;" class="text_boxes" value="<? echo $data[10]; ?>" readonly />
                                        <input type="hidden" id="hiddenProdlot_<? echo $k; ?>" name="hiddenProdlot[]" value="<? echo $data[11]; ?>"  style="width:15px;" class="text_boxes" readonly />
									</td>
									<td>
										<input type="text" id="txtSpecification_<? echo $k;?>" name="txtSpecification[]" class="text_boxes" style="width:90px" value="<? echo $data[1]; ?>" <? echo $disabled; ?> readonly /></td>
									<td ><?
											echo create_drop_down( "txtUnit_".$tblRow, 80, $unit_of_measurement,"", 1, "-- Select --", $data[2],"", 1,'','','','','','',"txtUnit[]"); 
										?>	
									</td>
									<td style="display: none;" >
										<input type="text" id="txtUnitPcs_<? echo $k;?>" name="txtUnitPcs[]" value="<? echo $data[3]; ?>" class="text_boxes_numeric" style="width:60px" onKeyUp="metarial_calculate(<? echo $k;?>)" value="<? echo $data[3]; ?>" <? echo $disabled; ?> />
									</td>
									<td><input type="text" id="txtConsQty_<? echo $k;?>" name="txtConsQty[]"  class="text_boxes_numeric" style="width:50px" onKeyUp="metarial_calculate(<? echo $k;?>)" value="<? echo $data[4]; ?>" placeholder="<? echo $prev_cons_unit; ?>" <? echo $disabled; ?> />
									</td>
									<td>
										<input type="text" id="txtProcessLoss_<? echo $k;?>" name="txtProcessLoss[]" class="text_boxes_numeric"  style="width:70px" value="<? echo $data[5]; ?>" onKeyUp="process_calculate(<? echo $k;?>)" placeholder="<? echo $prev_process_loss; ?>"  />
									</td>
									<td><input type="text" id="txtProcessLossQty_<? echo $k;?>" name="txtProcessLossQty[]"class="text_boxes_numeric" style="width:70px" value="<? echo number_format($data[6],4,'.',''); ?>" readonly /></td>
									<td><input type="text" id="txtReqQty_<? echo $k;?>" name="txtReqQty[]" class="text_boxes_numeric" style="width:70px" value="<? echo $data[7]; ?>" disabled/></td>
									<td><input type="text" id="txtRemarks_<? echo $k;?>" name="txtRemarks[]" class="text_boxes" style="width:70px" value="<? echo $data[8]; ?>" /></td>
									<td>
										<input type="hidden" id="hidbookingconsid_<? echo $k; ?>" name="hidbookingconsid[]"  style="width:15px;" class="text_boxes" />
	                                    <input type="hidden" id="hiddenid_<? echo $k; ?>" name="hiddenid[]"  style="width:15px;" value="<? echo $data[9]; ?>" class="text_boxes"  />
										
										<input type="button" id="decreaseset_<? echo $k;?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $k;?> ,'tbl_share_details_entry' );" <? echo $disabled; echo $req_disabled; ?>/>
									</td>  
								</tr>
								<?
							}
						}
						else
						{
							$req_qty=$prev_process_loss=$prev_cons_unit=$req_disabled='';
							if($is_requisition_done !='' && $is_requisition_done !=0 ){
								$req_qty=$requisition_data_arr[$data[10]]['requisition_qty'];
								$prev_process_loss=$prev_item_arr[$data[9]]['process_loss'];
								$prev_cons_unit=$prev_item_arr[$data[9]]['cons_qty'];
								if($disabled=!'disabled'){
									//$req_disabled='disabled';
								} 
							}
							$k++;

							?>
	                        <tr>
								<td><input type="text" id="txtSl_<? echo $k;?>" name="txtSl_<? echo $k;?>" class="text_boxes" style="width:30px" value="<? echo $k; ?>" />
								</td>
								<td><input type="text" id="txtdescription_<? echo $k;?>" name="txtdescription[]" class="text_boxes" onClick='openmypage_material("<? echo $company.'_0_'.$k.'_'.$cbo_section; ?>")' placeholder="Click" readonly style="width:120px" value="<? echo $data[0]; ?>" />
									<input type="hidden" id="hiddenProdid_<? echo $k; ?>" name="hiddenProdid[]"  style="width:15px;" readonly class="text_boxes" value="<? echo $data[10]; ?>"  />
                                    <input type="hidden" id="hiddenProdlot_<? echo $k; ?>" name="hiddenProdlot[]" value="<? echo $data[11]; ?>"  style="width:15px;" class="text_boxes" readonly />
								</td>
								<td>
									<input type="text" id="txtSpecification_<? echo $k;?>" name="txtSpecification[]" class="text_boxes" readonly style="width:90px" value="<? echo $color_arr[$data[1]]; ?>" <? echo $disabled; ?> /></td>
								<td><?
										echo create_drop_down( "txtUnit_".$tblRow, 80, $unit_of_measurement,"", 1, "-- Select --", $data[2],"", 1,'','','','','','',"txtUnit[]");  ?>	</td>
								<td  style="display: none;" >
									<input type="text" id="txtUnitPcs_<? echo $k;?>" name="txtUnitPcs[]" class="text_boxes_numeric"  style="width:60px" onKeyUp="metarial_calculate(<? echo $k;?>)"  value="<? echo $data[3]; ?>" <? echo $disabled; ?> />
								</td>
								<td><input type="text" id="txtConsQty_<? echo $k;?>" name="txtConsQty[]"  class="text_boxes_numeric" style="width:50px" onKeyUp="metarial_calculate(<? echo $k;?>)" value="<? echo $data[4]; ?>" placeholder="<? echo $prev_cons_unit; ?>" <? echo $disabled; ?>  /></td>
								<td>
									<input type="text" id="txtProcessLoss_<? echo $k;?>" name="txtProcessLoss[]" class="text_boxes_numeric"  onKeyUp="process_calculate(<? echo $k;?>)"   style="width:70px" value="<? echo $data[3]; ?>"  placeholder="<? echo $prev_process_loss; ?>"   />
								</td>
								<td><input type="text" id="txtProcessLossQty_<? echo $k;?>" name="txtProcessLossQty[]" class="text_boxes_numeric" style="width:70px" value="<? echo number_format($data[5],4,'.',''); ?>" readonly /></td>
								<td><input type="text" id="txtReqQty_<? echo $k;?>" name="txtReqQty[]" class="text_boxes_numeric" style="width:70px" value="<? echo number_format($data[5],4,'.',''); ?>" disabled/></td>
								<td><input type="text" id="txtRemarks_<? echo $k;?>" name="txtRemarks[]" class="text_boxes" style="width:70px" value="<? echo $data[5]; ?>"/></td>
								<td>
									<input type="hidden" id="hidbookingconsid_<? echo $k; ?>" name="hidbookingconsid[]"  style="width:15px;" class="text_boxes" value="" />
	                                <input type="hidden" id="hiddenid_<? echo $k; ?>" name="hiddenid[]"  style="width:15px;" class="text_boxes" value="<? echo $data[9]; ?>" />
									<input type="button" id="decreaseset_<? echo $k;?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $k;?> ,'tbl_share_details_entry' );" <? echo $disabled; echo $req_disabled; ?>/>
								</td>  
							</tr>
							<?
						}
						?> 
					</tbody>
				</table> 
				<table>
					<tr>
						<td align="center"><input type="button" name="main_close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" /></td>
					</tr>
				</table>
			</form>
		</div>
	</body>
	<script>
		$( "#is_copy" ).val( chkBoxVal );
		if(chkBoxVal==1){
			$( "#copy_1" ).attr( "checked", true );
		}else if(chkBoxVal==2){
			$( "#copy_2" ).attr( "checked", true );
		}else{
			$( "#copy_0" ).attr( "checked", true );
		}

		if(dtlsUpdateId!='' && dtlsUpdateId!=0){
			 $("input[type=radio]").attr('disabled', true);

		}
		
		/*if(hdnBrkDelId!='' && hdnBrkDelId!=0){
			$( "#txtDeletedId" ).val( hdnBrkDelId );
		} */

	/*if(chkBoxVal==1)
	{
		$( "#is_copy" ).attr( "checked", true );
	}
	else{
		$( "#is_copy" ).attr( "checked", false );
	}*/
	//metarial_calculate(0);</script>        
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}	

	if ($action=="material_description_popup")
	{
		echo load_html_head_contents("Description Popup Info","../../../", 1, 1, $unicode,'','');
		extract($_REQUEST);
		//echo $data."---";
		?>
		<script>
			/*function js_set_value(id)
			{ 
				$("#hidden_mst_id").val(id);
				document.getElementById('selected_job').value=id;
				parent.emailwindow.hide();
			}*/

			function toggle( x, origColor ) {
				var newColor = 'yellow';
				if ( x.style ) {
					x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
				}
			} 
			var selected_id = new Array;
			function js_set_value(str) 
			{  // alert(str);
				var subcon_job = $('#txt_prod_id'+str).val()+"_"+$('#tdBatchLot'+str).text();
				//alert(subcon_job);
				toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
				
				if( jQuery.inArray( subcon_job, selected_id ) == -1 ) {
					selected_id.push( subcon_job );
				}
				else 
				{
					for( var i = 0; i < selected_id.length; i++ ) 
					{
						if( selected_id[i] == subcon_job ) break;
					}
					selected_id.splice( i, 1 );
				}
				var id ='';  var id_dtls = ''; var id_break = '';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
				}
				id 		= id.substr( 0, id.length - 1 );
				$('#all_ids').val( id );
			}
		</script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
	    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	        <table width="700" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
	            <thead> 
	                <tr>           	 
	                    <th width="120" class="must_entry_caption">Company Name</th>
	                    <th width="70">Item Group</th>  
	                    <th width="100">Section</th>                         
	                    <th width="140">Description</th>
	                    <th width="100">Brand</th>
	                    <th width="70">Product ID</th>
	                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
	                </tr>           
	            </thead>
	            <tbody>
	                <tr class="general">
	                    <td><input type="hidden" id="selected_job"><? $data=explode("_",$data); ?>  <!--  echo $data;-->
	                        <? echo create_drop_down( "cbo_company_name", 120, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $data[0], "",1); ?>
	                    </td>
	                    <td id="item_group_td">
	                        <?
	                        if($data[3]==25){
								$item_category_cond = "and item_category in (22,101)";
							}else{
								$item_category_cond = "and item_category in (101,4)";
							}

	                        echo create_drop_down( "cbo_item_group", 70, "select id,item_name from lib_item_group where status_active=1 and is_deleted=0 $item_category_cond order by item_name", "id,item_name", 1, "-- Select --", 0, "", $disabled,"" );
	                         ?>
	                    </td>
	                    <td id="section_td">
	                    	<? echo create_drop_down( "cbo_section", 100, $trims_section,"", 1, "-- Select Section --","",'',0,'','','','','','',"cboSection[]"); ?>
	                    </td>
	                    <td align="center">
	                        <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:127px" placeholder="" />
	                    </td>
	                    <td align="center">
	                        <input type="text" name="txt_brand_id" id="txt_brand_id" class="text_boxes" style="width:87px" placeholder="" />
	                    </td>
	                    <td align="center">
	                        <input type="text" name="txt_product_id" id="txt_product_id" class="text_boxes_numeric" style="width:57px" placeholder="" />
	                    </td>
	                    <td align="center">
	                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_item_group').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('txt_product_id').value+'_'+document.getElementById('txt_brand_id').value+'_'+document.getElementById('cbo_section').value+'_'+'<? echo $data[3]?>', 'create_description_search_list_view', 'search_div', 'Job_card_preparation_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" />
	                        <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
	                    </td>
	                    </tr>
	                    <tr>
	                        <td colspan="7" align="center" valign="top" id=""><div id="search_div"></div></td>
	                    </tr>
	                    <tr>
	                        <td colspan="7" align="center" valign="top" id=""> 
	                        	<div style="width:100%; float:left" align="center">
	    							<input type="button" class="formbutton" id="close" style="width:80px" onClick="parent.emailwindow.hide();" value="Close" />
	    							<input type="hidden"  id="all_ids" />
	    						</div>
	    					</td>
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

	if($action=="create_description_search_list_view")
	{	
		$data=explode('_',$data);
		$group_id=str_replace("'","",$data[1]);
		$description_str=str_replace("'","",$data[2]);
		$product_id=trim(str_replace("'","",$data[3]));
		$brand_name=trim(str_replace("'","",$data[4]));
		$section_id=str_replace("'","",$data[5]);
		$actual_section_id=str_replace("'","",$data[6]);
		$item_category_cond = "and a.item_category_id in (101)";
		if($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
		if($group_id!=0) $group=" and a.item_group_id=$group_id"; else { $group=''; }	
		if($description_str!='') $description=" and a.item_description like '%$description_str%'"; else { $description=''; }	
		if($product_id!='') $product=" and a.id='$product_id'"; else { $product=''; }	
		if($brand_name!='') $brand=" and a.brand_name='$brand_name'"; else { $brand=''; }	
		
		if($actual_section_id==25){
			$item_category_cond = "and a.item_category_id in (22,101 )";
			$entry_form_cond = "and a.entry_form in (220,300,285,0,334)";
		}else{
			$item_category_cond = "and a.item_category_id in (101)";
			$entry_form_cond = "and a.entry_form = 334"; 
		}
		if($section_id!=0 && $section_id!=25)
		{
			$section=" and a.section_id='$section_id'";
		}
			

		// $sql="select a.id, a.company_id, a.item_code, a.item_description, item_group_id,item_size,current_stock,brand_name,origin,model,sub_group_name,unit_of_measure ,section_id,item_category_id from product_details_master a
		// where status_active=1 and is_deleted=0  $company $group $description $product $brand $section $item_category_cond $entry_form_cond and status_active=1 and is_deleted=0";

		$sql="select a.id, a.company_id, a.item_code, a.item_description, a.item_group_id, a.item_size, a.current_stock, a.brand_name, a.origin, a.model, a.sub_group_name, a.unit_of_measure , a.section_id, a.item_category_id, null as batch_lot 
		from product_details_master a
		where a.status_active=1 and a.is_deleted=0 and a.item_category_id in (101) $company $group $description $product $brand $section $entry_form_cond";
		if($actual_section_id==25)
		{
			$sql.=" union all select a.id, a.company_id, a.item_code, a.item_description, a.item_group_id, a.item_size, a.current_stock, a.brand_name, a.origin, a.model, a.sub_group_name, a.unit_of_measure, a.section_id, a.item_category_id, b.batch_lot 
			from product_details_master a, inv_transaction b 
			where a.id=b.prod_id and a.status_active=1 and a.is_deleted=0 and a.item_category_id in (22)  and b.item_category in (22) $company $group $description $product $brand $section $entry_form_cond and b.status_active=1 and b.is_deleted=0
			group by a.id, a.company_id, a.item_code, a.item_description, a.item_group_id, a.item_size, a.current_stock, a.brand_name, a.origin, a.model, a.sub_group_name, a.unit_of_measure, a.section_id, a.item_category_id, b.batch_lot
			order by id ";
		}

		//echo $sql;
		$data_array=sql_select($sql);
		?>
	     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="800" >
	        <thead>
	        	<tr><th colspan="8"><?  echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --",4 ); ?></th></tr>
	        	<tr>
	        		<th width="30">SL</th>
		            <th width="105">Item Group</th>
		            <th width="105">Section</th>
		            <th width="50">UOM</th>
		            <th width="200">Description</th>
		            <th width="100">Lot</th>
		            <th width="120">Brand</th>
		            <th>Product ID</th>
	        	</tr>
	            
	        </thead>
	    </table>
	        <div style="width:800px; max-height:280px;overflow-y:scroll;" >	 
	        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table" id="tbl_po_list">
	        <tbody>
	            <? 
	            $i=1;
	            // item_category in (101,5, 6, 7, 23 ) and
	            $itemGroup_arr=return_library_array( "select id,item_name from lib_item_group where status_active=1 and is_deleted=0 order by item_name",'id','item_name');
	            foreach($data_array as $row)
	            {  
	                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	            	if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
					?>	
					<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $i;?>)" > 
		                <td width="30"><? echo $i; ?></td>
		                <td width="105"><? echo $itemGroup_arr[$row[csf('item_group_id')]]; ?></td>
		                <td width="105"><? echo $trims_section[$row[csf('section_id')]]; ?></td>
		                <td width="50"><? echo $unit_of_measurement[$row[csf('unit_of_measure')]]; ?></td>
		                <td width="200" style="word-break:break-all"  title="<? echo $item_category[$row[csf('item_category_id')]];?>"><? echo $row[csf('item_description')]; ?></td>
		                <td width="100" style="word-break:break-all" id="tdBatchLot<? echo $i; ?>"><? echo $row[csf('batch_lot')]; ?></td>
		                <td width="120" style="word-break:break-all" ><? echo $row[csf('brand_name')]; ?></td>
		                <td>
		                	<? echo $row[csf('id')]; ?>
		                	<input name="txt_prod_id<? echo $i; ?>" id="txt_prod_id<? echo $i; ?>" type="hidden" value="<? echo $row[csf('id')]; ?>" />
		                </td>
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

	if ($action == "populate_prod_data") 
	{
	    $ex_data = explode("**", $data);
	    $prod_id =   $ex_data[0] ; 
	    $updateId =   $ex_data[2] ; 
	    $companyId =   $ex_data[3] ; 
	    //and item_category_id=101 and entry_form=334
		if($prod_id)
		{
			$lot_cond_variable_setting=return_field_value( "AUTO_TRANSFER_RCV", "VARIABLE_SETTINGS_INVENTORY"," VARIABLE_LIST=32 and COMPANY_NAME=$companyId and status_active=1 and is_deleted=0");
			$porduct_ids=$porduct_lot="";
			$prod_data_arr=explode(",",$prod_id);
			foreach($prod_data_arr as $prod_ref)
			{
				$prod_ref_arr=explode("_",$prod_ref);
				$porduct_ids.=$prod_ref_arr[0].",";
				$porduct_lot.="'".$prod_ref_arr[1]."',";
			}
			$porduct_ids=chop($porduct_ids,",");
			$porduct_lot=chop($porduct_lot,",");
			if($porduct_lot!="") $lot_cond=" and b.batch_lot in($porduct_lot)";
			$lot_cond_variable="";
			if($lot_cond_variable_setting==1) $lot_cond_variable=" and b.batch_lot is not null";
		 	$sql="select a.id, a.company_id, a.item_code, a.item_description, a.item_group_id, a.item_size, a.current_stock, a.brand_name, a.origin, a.model, a.sub_group_name, a.unit_of_measure, b.batch_lot 
			from product_details_master a, inv_transaction b
			where a.id=b.prod_id and b.status_active=1 and a.status_active=1 and a.is_deleted=0 and a.id in($porduct_ids)  
			group by a.id, a.company_id, a.item_code, a.item_description, a.item_group_id, a.item_size, a.current_stock, a.brand_name, a.origin, a.model, a.sub_group_name, a.unit_of_measure, b.batch_lot";
		}
	    //echo $sql;die;
        $result = sql_select($sql);
        $count=count($result);
        $i=$ex_data[1]+$count;
        //$machine= trim(implode(",",[$value]));
        foreach ($result as $row)
        { 
	        ?>
	        <tr id="row_<? echo $i;?>">
				<td><input type="text" id="txtSl_<? echo $i;?>" name="txtSl_<? echo $i;?>" class="text_boxes" style="width:30px" value="<? echo $i; ?>" />
				</td>
				<td title="<? echo $row[csf('item_description')]; ?>"><input type="text" id="txtdescription_<? echo $i;?>" name="txtdescription[]" class="text_boxes" onClick="openmypage_material(<? echo $company."_".$data[9]; ?>)" placeholder="Click" style="width:120px" value="<? echo $row[csf('item_description')]; ?>" readonly />
					<input type="hidden" id="hiddenProdid_<? echo $i; ?>" name="hiddenProdid[]" value="<? echo $row[csf('id')]; ?>"  style="width:15px;" class="text_boxes" readonly />
                    <input type="hidden" id="hiddenProdlot_<? echo $i; ?>" name="hiddenProdlot[]" value="<? echo $row[csf('batch_lot')]; ?>"  style="width:15px;" class="text_boxes" readonly />
				</td>
				<td>
					<input type="text" id="txtSpecification_<? echo $i;?>" name="txtSpecification[]" class="text_boxes" style="width:90px" value="<? echo $row[csf('item_description')]; ?>" <? echo $disabled; ?> readonly /></td>
				<td><?
					echo create_drop_down( "txtUnit_".$tblRow, 80, $unit_of_measurement,"", 1, "-- Select --",$row[csf('unit_of_measure')],"", 1,'','','','','','',"txtUnit[]");
                ?></td>
				<td style="display:none;">
					<input type="text" id="txtUnitPcs_<? echo $i;?>" name="txtUnitPcs[]" onKeyUp="metarial_calculate(<? echo $i;?>)"  class="text_boxes_numeric" style="width:60px" />
				</td>
				<td><input type="text" id="txtConsQty_<? echo $i;?>" name="txtConsQty[]"  class="text_boxes_numeric" style="width:50px" onKeyUp="metarial_calculate(<? echo $i;?>)"  />
				</td>
				<td>
					<input type="text" id="txtProcessLoss_<? echo $i;?>" name="txtProcessLoss[]" class="text_boxes_numeric" style="width:70px"  onKeyUp="process_calculate(<? echo $i;?>)" value=""  />
				</td>
				<td><input type="text" id="txtProcessLossQty_<? echo $i;?>" name="txtProcessLossQty[]"   class="text_boxes_numeric" style="width:70px" readonly /></td>
				<td><input type="text" id="txtReqQty_<? echo $i;?>" name="txtReqQty[]" class="text_boxes_numeric" style="width:70px"  disabled/></td>
				<td><input type="text" id="txtRemarks_<? echo $i;?>" name="txtRemarks[]" class="text_boxes" style="width:70px" /></td>
				<td>
					<input type="hidden" id="hidbookingconsid_<? echo $i; ?>" name="hidbookingconsid[]"  style="width:15px;" class="text_boxes"  />
                    <input type="hidden" id="hiddenid_<? echo $i; ?>" name="hiddenid[]" value="<? echo $updateId; ?>"  style="width:15px;" class="text_boxes"  />
					<input type="button" id="decreaseset_<? echo $i;?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i;?> ,'tbl_share_details_entry' );" <? echo $disabled; ?>/>
				</td>  
			</tr>
	        <?
	        $i--;
             
        }
    	exit(); 
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
				var within_group = $('#cbo_within_group').val();
				load_drop_down( 'Job_card_preparation_controller', company+'_'+within_group, 'load_drop_down_buyer', 'buyer_td' );
			}
			function search_by(val)
			{
				$('#txt_search_string').val('');
				if(val==1 || val==0){
					$('#search_by_td').html('Job ID');
				}else if(val==2){
					$('#search_by_td').html('W/O No');
				}else if(val==4){
					$('#search_by_td').html('Buyer Po');
				}else if(val==5){
					$('#search_by_td').html('Buyer Style');
				}else if(val==6){
					$('#search_by_td').html('Receive No.');
				}
			}
		</script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
	    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	        <table width="940" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
	            <thead> 
	                <tr>
	                    <th colspan="9"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
	                </tr>
	                <tr>
	                    <th width="140" class="must_entry_caption">Company Name</th>
	                    <th width="60">Within Group</th>                           
	                    <th width="140">Party Name</th>
	                    <th width="80">Search By</th>
	                    <th width="100" id="search_by_td">Job ID</th>
	                    <th width="80">Section</th>
	                    <th width="60">Year</th>
	                    <th width="170">Date Range</th>                            
	                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
	                </tr>           
	            </thead>
	            <tbody>
	                <tr class="general">
	                    <td><input type="hidden" id="selected_job"><? $data=explode("_",$data); ?>  <!--  echo $data;-->
	                        <? 
	                        echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $data[0], "fnc_load_party_popup(1,document.getElementById('cbo_within_group').value);",1); ?>
	                    </td>
	                    <td>
	                        <?php echo create_drop_down( "cbo_within_group", 60, $yes_no,"", 0, "--  --", $data[3], "fnc_load_party_popup(1,this.value);" ); ?>
	                    </td>
	                    <td id="buyer_td">
	                        <? 
							echo create_drop_down( "cbo_party_name", 150, "","", 1, "-- Select Party --", $data[2], "fnc_load_party_popup(1,this.value);" );   	 	 
	                        ?>
	                    </td>
	                    <td>
	                    	<?
	                            $search_by_arr=array(1=>"Job ID",2=>"W/O No",4=>"Buyer Po",5=>"Buyer Style",6=>"Receive No.");
	                            echo create_drop_down( "cbo_type",80, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
	                        ?>
	                    </td>
	                    <td align="center">
	                        <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
	                    </td>
	                    <td><? echo create_drop_down( "cbo_section", 80, $trims_section,"", 1, "-- Select Section --","",'',0,'','','','','','',"cboSection[]"); ?></td>
	                    <td align="center"><? echo create_drop_down( "cbo_year_selection", 60, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
	                    <td align="center">
	                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
	                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
	                    </td>
	                    <td align="center">
	                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_section').value, 'create_job_search_list_view', 'search_div', 'Job_card_preparation_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
	                    </tr>
	                    <tr>
	                        <td colspan="9" align="center" valign="middle">
	                            <? echo load_month_buttons();  ?>
	                            <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
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
		$section_id =$data[9];
		if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[8]";   }
		if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$data[8]";}
		if($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
		if($party_id!=0) $party_id_cond=" and a.party_id='$party_id'"; else $party_id_cond="";
		if($section_id!=0) $section_id_cond=" and a.section_id='$section_id'"; else $section_id_cond="";

		if($db_type==0){ 
			if ($data[2]!="" &&  $data[3]!="") $delivery_date = "and a.delivery_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $delivery_date ="";
		}else{
			if ($data[2]!="" &&  $data[3]!="") $delivery_date = "and a.delivery_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $delivery_date ="";
		}
		if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";

		
		$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
		if($search_type==1){
			if($search_str!=""){
				if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
				else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
				else if ($search_by==4) $search_com_cond=" and b.buyer_po_no = '$search_str' ";
				else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref = '$search_str' ";
				else if ($search_by==6) $search_com_cond=" and a.received_no = '$search_str' ";
			}
		}else if($search_type==2){
			if($search_str!="")
			{
				if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
				else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
				else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '$search_str%'";
				else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '$search_str%'";
				else if ($search_by==6) $search_com_cond=" and a.received_no like '$search_str%'"; 
			}
		}else if($search_type==3){
			if($search_str!="")
			{
				if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";  
				else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
				else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '%$search_str'";
				else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '%$search_str'";
				else if ($search_by==6) $search_com_cond=" and a.received_no like '%$search_str'";
			}
		}else if($search_type==4 || $search_type==0){
			if($search_str!="")
			{
				if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str%'";  
				else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";  
				else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '%$search_str%'"; 
				else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '%$search_str%'";
				else if ($search_by==6) $search_com_cond=" and a.received_no like '%$search_str%'";  
			}
		}
		
		if($db_type==0) {
			$ins_year_cond="year(a.insert_date)";
		}else if($db_type==2){
			$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
		}
		if($within_group==1){
		$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		}else{
			$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		}	
		
		$sql= "select a.id, a.trims_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id,  a.order_no,a.received_no,received_id, a.delivery_date,a.section_id 
		from trims_job_card_mst a, trims_job_card_dtls b
		where a.entry_form=257 and a.trims_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and b.status_active=1 $delivery_date $company $party_id_cond $search_com_cond  $withinGroup $section_id_cond $year_cond
		group by a.id, a.trims_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.order_no ,a.received_no,received_id,a.delivery_date,a.section_id 
		order by a.id DESC";
		//echo $sql;

		$data_array=sql_select($sql);
		?>
	     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="785" >
	        <thead>
	            <th width="30">SL</th>
	            <th width="120">Party</th>
	            <th width="120">Job ID</th>
	            <th width="100">Section</th>
	            <th width="60">Year</th>
	            <th width="120">W/O No.</th>
	            <th width="120">Receive No.</th>
	            <th>Delivery Date</th>
	        </thead>
	        </table>
	        <div style="width:785px; max-height:270px;overflow-y:scroll;" >	 
	        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="765" class="rpt_table" id="tbl_po_list">
	        <tbody>
	            <? 
	            $i=1;
	            foreach($data_array as $row)
	            {  
	                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	                ?>
	                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$row[csf('trims_job')].'_'.$row[csf('received_id')].'_'.$row[csf('received_no')]; ?>")' style="cursor:pointer" >
	                    <td width="30"><? echo $i; ?></td>
	                    <td width="120" ><? echo $party_arr[$row[csf('party_id')]]; ?></td>
	                    <td width="120" style="text-align:center;" ><? echo $row[csf('job_no_prefix_num')]; ?></td>
	                    <td width="100"><? echo $trims_section[$row[csf('section_id')]]; ?></td>
	                    <td width="60" style="text-align:center;"><? echo $row[csf('year')]; ?></td>
	                    <td width="120"><? echo $row[csf('order_no')]; ?></td>
	                    <td width="120"><? echo $row[csf('received_no')]; ?></td>
	                    <td style="text-align:center;"><? echo change_date_format($row[csf('delivery_date')]); ?></td>
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

	if ($action=="load_mst_php_data_to_form")
	{
		//echo "select id, trims_job, company_id, location_id, party_id, currency_id, within_group, delivery_date, party_location, entry_form,  order_id, order_no, order_qty, received_no, received_id, section_id from trims_job_card_mst  where entry_form=257 and id=$data and status_active=1 order by a.id DESC" ;  

		$req_id=return_field_value("id","trims_raw_mat_requisition_mst","job_id=$data and status_active=1 and is_deleted=0","id");
		$nameArray=sql_select( "select id, trims_job, company_id, location_id, party_id, currency_id, within_group, delivery_date, party_location, entry_form,  order_id, order_no, order_qty, received_no, received_id, section_id 
		 from trims_job_card_mst  where entry_form=257 and id=$data and status_active=1 order by id DESC" );
		foreach ($nameArray as $row)
		{	
			echo "document.getElementById('update_id').value 				= '".$row[csf("id")]."';\n";
			echo "document.getElementById('txt_job_no').value 				= '".$row[csf("trims_job")]."';\n";
			echo "document.getElementById('txt_recv_no').value 				= '".$row[csf("received_no")]."';\n";
			echo "document.getElementById('hid_recv_id').value 				= '".$row[csf("received_id")]."';\n";
			echo "document.getElementById('cbo_company_name').value 		= '".$row[csf("company_id")]."';\n";
			echo "document.getElementById('cbo_within_group').value 		= '".$row[csf("within_group")]."';\n";  
			echo "$('#cbo_company_name').attr('disabled','true')".";\n";
			echo "fnc_load_party(1,".$row[csf("within_group")].");\n";	
		//echo "load_drop_down( 'requires/Job_card_preparation_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_location', 'location_td' );\n";	
			echo "document.getElementById('cbo_location_name').value 		= '".$row[csf("location_id")]."';\n";
		//echo "load_drop_down( 'requires/Job_card_preparation_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_buyer', 'buyer_td' );\n";
			echo "document.getElementById('cbo_party_name').value			= '".$row[csf("party_id")]."';\n";
			//cbo_company_name cbo_location_name cbo_within_group cbo_party_name cbo_party_location txt_delivery_date cbo_section txt_order_no txt_order_qty
			echo "fnc_load_party(2,".$row[csf("within_group")].");\n";	 
			echo "document.getElementById('cbo_party_location').value		= '".$row[csf("party_location")]."';\n";	
			echo "document.getElementById('txt_delivery_date').value		= '".change_date_format($row[csf("delivery_date")])."';\n";  
			echo "document.getElementById('hid_order_id').value          	= '".$row[csf("order_id")]."';\n";
			echo "document.getElementById('txt_order_no').value         	= '".$row[csf("order_no")]."';\n";
			echo "document.getElementById('txt_order_qty').value         	= '".$row[csf("order_qty")]."';\n";
			echo "document.getElementById('cbo_section').value        		= '".$row[csf("section_id")]."';\n";
			if($req_id!=''){
				echo "document.getElementById('is_requisition_done').value  = '".$req_id."';\n";
			}
			echo "$('#txt_order_no').attr('disabled','true')".";\n";
			echo "$('#cbo_within_group').attr('disabled','true')".";\n";
			echo "$('#cbo_party_name').attr('disabled','true')".";\n";
			//echo "document.getElementById('update_id').value          		= '".$row[csf("id")]."';\n";	
			//echo "set_button_status(1,'".$_SESSION['page_permission']."', 'fnc_job_order_entry',1);\n";	
		}
		exit();	
	}


	if($action=="color_popup")
	{
		extract($_REQUEST);
		echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
		//echo $style_id;die;

		?>
	    <script>
			/*function js_set_value(data)
			{
				$('#txt_selected_no').val(data);
				parent.emailwindow.hide();
			}*/
			var str_color = [<? echo substr(return_library_autocomplete( "select color_name from lib_color group by color_name ", "color_name" ), 0, -1); ?> ];

			function set_auto_complete(type)
			{
				if(type=='color_return')
				{
					$(".txt_color").autocomplete({
						source: str_color
					});
				}
			}
			function fnc_close()
			{
				var tot_row=$('#tbl_color_list tbody tr').length;
				var data_break_down="";
				for(var i=1; i<=tot_row; i++)
				{
					if (form_validation('txtRowColor_'+i,'Color')==false)
					{
						return;
					}

					if($("#txtRowColor_"+i).val()=="") $("#txtRowColor_"+i).val(0)
					if(data_break_down=="")
					{
						data_break_down+=$('#txtRowColor_'+i).val();
					}
					else
					{
						data_break_down+="__"+$('#txtRowColor_'+i).val();
					}
				}
				//alert(data_break_down);
				$('#hidden_break_tot_row').val( data_break_down );
				parent.emailwindow.hide();
			}

	    </script>
	    </script>
</head>
<body onLoad="set_auto_complete('color_return');">
	<div align="center" style="width:100%;" >
		<form name="qntypopup_1"  id="qntypopup_1" autocomplete="off">
	    <?
		$company=str_replace("'","",$company);
		$type=str_replace("'","",$type);
		$impression=str_replace("'","",$impression);
		$hdnRawcolor=str_replace("'","",$hdnRawcolor);
		$rawcolor=explode("__",$hdnRawcolor);
		$rawColorRow=count($rawcolor);
		
		?>
		<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="250" >
	        <thead>
	            <th width="30">SL</th>
	            <th width="220" class="must_entry_caption">Color</th>
	        </thead>
	        </table>
	        
	        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="250" class="rpt_table" id="tbl_color_list">
	        <tbody>
	        	<input type="hidden" name="hidden_break_tot_row" id="hidden_break_tot_row" class="text_boxes" style="width:90px" />
	            <?
	            //echo $rawColorRow; die;
	            //print_r($rawcolor); die;
	            if($rawColorRow>0)
	            {	
	            	$x=1;
	            	for($i=0; $i<$impression;$i++)
		            {
		            	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		                ?>
		                <tr bgcolor="<? echo $bgcolor; ?>">
		                    <td width="30"><? echo $x; ?></td>
		                    <td width="220"><input class="text_boxes txt_color" type="text" name="txtRowColor[]" id="txtRowColor_<? echo $x ?>" value="<? echo $rawcolor[$i]; ?>" style="width:207px;"/></td>
		                </tr>
						<? 
						$x++;
		            }
	            } 
	            else
	            {

	            	for($i=1; $i<=$impression;$i++)
		            {
		            	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		                ?>
		                <tr bgcolor="<? echo $bgcolor; ?>">
		                    <td width="30"><? echo $i; ?></td>
		                    <td width="220"><input class="text_boxes" type="text" name="txtRowColor[]" id="txtRowColor_<? echo $i ?>" style="width:207px;"/></td>
		                </tr>
						<? 
		            }
	            }
	            ?>
	        </tbody>
	    </table>
	    <table>
				<tr>
					<td align="center"><input type="button" name="main_close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" /></td>
				</tr>
			</table>
		</form>
	
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="job_card_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//echo "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]'  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2, 3)) order by buyer_name"; die;
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	//$party_library=return_library_array( "select id, supplier_name from lib_supplier", "id","supplier_name"  );
	$buyer_library_arr=return_library_array("select id, buyer_name from lib_buyer where status_active=1","id","buyer_name");

	//$buyer_library_arr=return_library_array("select id,buyer_name from lib_buyer where status_active=1","id","buyer_name");$brand_arr
	//$order_array=return_library_array( "select id, order_no from subcon_ord_dtls", "id","order_no"  );
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$brand_arr=return_library_array( "select id, brand_name from product_details_master",'id','brand_name');
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	
	if($data[2]==1)
	{
		$party_arr=return_library_array( "select id,company_name from  lib_company where status_active=1 and is_deleted=0 order by company_name",'id','company_name');
	}
	else
	{
		$party_arr=return_library_array( "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name",'id','buyer_name');
	}
	$sql_mst="select a.id, a.trims_job, a.job_no_prefix, a.job_no_prefix_num,  a.company_id, a.location_id, a.within_group, a.party_id,  a.party_location , a.delivery_date, a.order_id, a.order_qty, a.received_no, a.received_id, a.section_id , b.receive_date, b.delivery_date, b.order_no from trims_job_card_mst a , subcon_ord_mst b where a.received_id=b.id and a.company_id=$data[0] and a.id='$data[1]' and a.status_active=1 and a.is_deleted=0";
	$dataArray=sql_select($sql_mst);
	$section_id=$dataArray[0][csf("section_id")];
	$job_qnty_arr=array();
	//$qty_sql ="SELECT a.id, a.booked_qty from subcon_ord_breakdown a where a.booked_qty is not null and a.booked_qty!=0";
	$qty_sql= "select b.id as receive_details_id,a.id,a.description,a.color_id,a.size_id,b.sub_section,b.booked_uom,b.booked_conv_fac, a.booked_qty  from subcon_ord_dtls b ,subcon_ord_breakdown a where a.mst_id=b.id  and b.job_no_mst=a.job_no_mst and a.booked_qty is not null and a.booked_qty!=0  and a.status_active=1";
	$qty_sql_res=sql_select($qty_sql);
	foreach ($qty_sql_res as $row)
	{
		$job_qnty_arr[$row[csf("id")]][$row[csf("booked_conv_fac")]]['qnty']=$row[csf("booked_qty")];
	}
	unset($qty_sql_res);
	$received_id=$dataArray[0][csf('received_id')];
	//echo "select trims_ref from subcon_ord_mst where entry_form=255 and id=$received_id and status_active=1 and is_deleted=0"; die;
	$trims_ref=return_field_value( "trims_ref", "subcon_ord_mst"," entry_form=255 and id=$received_id and status_active=1 and is_deleted=0");
	//echo "<pre>";
	//print_r($party_arr);
	//die;
	
	?>
    <style>
	p {
		word-break: break-all;
	}
    </style>
    
    <? 
	?>
	<div style="width:1060px;">
		<table width="1060" cellspacing="0" align="center" border="0">
			<tr>
				<td colspan="6"></td>
			</tr>
			<tr>
				<td colspan="2" rowspan="2">
					<img src="../../<? echo $image_location; ?>" height="60" width="200" style="float:left;">
				</td>
				<td colspan="4" align="center" style="font-size:x-large">
					<strong><? echo $company_library[$data[0]]; ?></strong>
				</td>
			</tr>
			<tr>
				<td colspan="4" align="center">
					<?
					$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website, vat_number from lib_company where id=$data[0] and status_active=1 and is_deleted=0"); 
					foreach ($nameArray as $result)
					{ 
						?>
						<? echo $result[csf('plot_no')]; ?>,
						Level No: <? echo $result[csf('level_no')]?>,
						<? echo $result[csf('road_no')]; ?>, 
						<? echo $result[csf('block_no')];?>, 
						<? echo $result[csf('city')];?>, 
						<? echo $result[csf('zip_code')]; ?>, 
						<?php echo $result[csf('province')];?>, 
						<? echo $country_arr[$result[csf('country_id')]]; ?><br> 
						Email Address: <? echo $result[csf('email')];?> 
						Website No: <? echo $result[csf('website')];?> <br>
						<b> Vat No : <? echo $result[csf('vat_number')]; ?></b> <?
							//$party_id= $result[csf('party_id')];
					}
					?> 
				</td>
			</tr> 

			<tr>
				<td colspan="6" align="center" style="font-size:20px"><u>
					<strong style="margin-left:265px;"><? echo $data[3]; ?></strong></u>
					<strong style="margin-left:265px;">Section: <? echo $trims_section[$dataArray[0][csf('section_id')]]; ?></strong></u>
				</td>
			</tr>
			<tr>
				<td>Party </td><td colspan="5">: <strong> <? echo $party_arr[$dataArray[0][csf('party_id')]]; ?></strong></td>
				<td width="150">Buyer's Buyer</td>
                 <td width="175" id="td_cus_buyer"></td>
			</tr>
			<tr>
				<td>Order Recei. No </td><td colspan="5">: <strong> <? echo $dataArray[0][csf('received_no')]; ?></strong></td>
				<td>Trims Ref. </td><td><? echo ": ".$trims_ref; ?></td>
			</tr>
			<tr>
				<td width="170">Order Receive Date </td> <td width="175"><? echo ": ".change_date_format($dataArray[0][csf('receive_date')]); ?></td>
				<td width="130"></td> <td width="175"></td>
				<td width="130"></td> <td width="175"></td>
				<td width="150">Job Card No.</td><td width="300px">: <strong> <? echo $dataArray[0][csf('trims_job')]  ?></strong></td>
			</tr>
			<tr>
				<td>Target Delivery</td><td><? echo ": ".change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
				<td  colspan="4">&nbsp;</td> 
				<td>Order Qty </td><td colspan="3"><? echo ": ".$dataArray[0][csf('order_qty')]; ?></td>
				<!-- <td>W/O No </td><td colspan="3"><? //echo ": ".$dataArray[0][csf('order_no')]; ?></td> -->
			</tr>
			<tr>
				<td>W/O No </td><td ><? echo ": ".$dataArray[0][csf('order_no')]; ?></td>
				
				<td colspan="5">&nbsp;</td>
                <!-- <td>Order Qty </td><td colspan="3"><? //echo ": ".$dataArray[0][csf('order_qty')]; ?></td> -->
			</tr>
		</table>
		<br>
		<div style="width:100%;">
			<table align="left" cellspacing="0" width="1060"  border="1" rules="all" class="rpt_table"  >
				<thead bgcolor="#dddddd">
					<tr>
						<th colspan="10" style="text-align:center; font-size: 18px;">Material Consumption</th>
					</tr>
					<tr>						
						<th width="30" style="font-size: 16px;">SL</th>
						<th width="175" style="font-size: 16px;">Buyer Order</th>
						<th width="175" style="font-size: 16px;">Buyer Style</th>
						<th width="200" style="font-size: 16px;">Item Description</th>
						<th width="80" style="font-size: 16px;">UOM</th> 
						<th width="150" colspan="2" style="font-size: 16px;">Item Qty</th> 
						<th width="70" style="font-size: 16px;">Impression</th>
						<th>Color</th> 
						<th></th> 
					</tr>
				</thead>         
				<?	 
			$l=1;
			$mst_id=$data[1];
			$trims_job=$dataArray[0][csf('trims_job')]; //die;
			$sqlDtls_result =sql_select("select  id, mst_id, job_no_mst, receive_dtls_id, booking_dtls_id, book_con_dtls_id, break_id as subcon_break_ids, buyer_po_id,buyer_po_no, buyer_style_ref, item_description, color_id, size_id, uom, job_quantity,  impression, material_color,conv_factor from trims_job_card_dtls where mst_id=$data[1] and status_active=1 and is_deleted=0");

			$sqlBreak_result =sql_select("select  a.id, a.mst_id, a.order_id, a.job_no_mst, a.book_con_dtls_id, a.product_id, a.description, a.specification, a.pcs_unit, a.cons_qty, a.process_loss, a.process_loss_qty, a.req_qty, a.remarks,b.unit_of_measure as unit, a.lot from trims_job_card_breakdown a , product_details_master b where a.product_id=b.id and b.entry_form in (334,220,300,285,0) and a.status_active=1 and a.is_deleted=0 and a.job_no_mst='$trims_job'");

			   //new
            //   $sqlBreak_results ="SELECT  a.id, a.mst_id, a.order_id, a.job_no_mst, a.book_con_dtls_id, a.product_id, a.description, a.specification, a.pcs_unit, a.cons_qty, a.process_loss, a.process_loss_qty, a.req_qty, a.remarks,b.unit_of_measure as unit,null as batch_lot from trims_job_card_breakdown a , product_details_master b where a.product_id=b.id and b.entry_form in (334,220,300,285,0) and a.status_active=1 and a.is_deleted=0 and b.item_category_id in(101) and  a.job_no_mst='$trims_job'";

            // if($section_id=25){ 
			//   $sqlBreak_results .= "union all SELECT  a.id, a.mst_id, a.order_id, a.job_no_mst, a.book_con_dtls_id, a.product_id, a.description, a.specification, a.pcs_unit, a.cons_qty, a.process_loss, a.process_loss_qty, a.req_qty, a.remarks,b.unit_of_measure as unit, c.batch_lot from trims_job_card_breakdown a , product_details_master b , inv_transaction c where b.id=c.prod_id and a.product_id=b.id and b.entry_form in (334,220,300,285,0) and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.item_category_id in(22) and a.job_no_mst='$trims_job' and c.item_category=22
		    //    group by  a.id, a.mst_id, a.order_id, a.job_no_mst, a.book_con_dtls_id, a.product_id, a.description, a.specification, a.pcs_unit, a.cons_qty, a.process_loss, a.process_loss_qty, a.req_qty, a.remarks,b.unit_of_measure, c.batch_lot";
			// }
			
			//and b.entry_form in (334 , 220,300,285,0 )
			//echo "select  a.id, a.mst_id, a.order_id, a.job_no_mst, a.book_con_dtls_id, a.product_id, a.description, a.specification, a.pcs_unit, a.cons_qty, a.process_loss, a.process_loss_qty, a.req_qty, a.remarks,b.unit_of_measure as unit from trims_job_card_breakdown a , product_details_master b where a.product_id=b.id and b.entry_form in (334,220,300,285,0) and a.status_active=1 and a.is_deleted=0 and a.job_no_mst='$trims_job'";
			$break_arr=array(); $break_arr_summery=array();
			//$sqlBreak_result =sql_select($sqlBreak_results);
            //echo $sqlBreak_result;
			foreach($sqlBreak_result as $row)
			{
				$break_arr[$row[csf('mst_id')]]['info'].=$row[csf('description')]."_".$row[csf('pcs_unit')]."_".$row[csf('unit')]."_".$row[csf('req_qty')]."_".$row[csf('process_loss')]."_".$row[csf('remarks')]."_".$row[csf('product_id')]."_".$row[csf('lot')]."**";
				$break_arr_summery[$row[csf('description')]][$row[csf('unit')]][$row[csf('product_id')]]+=$row[csf('req_qty')];
			}
			
			//echo $received_id; die;
			$sqlOrdDtls_result =sql_select("select  id, order_quantity, buyer_buyer from subcon_ord_dtls where mst_id=$received_id");
			$ordDtls_arr=array();
			foreach($sqlOrdDtls_result as $row)
			{
				$ordDtls_arr[$row[csf('id')]]['order_quantity']=$row[csf('order_quantity')];
				$ordDtls_arr[$row[csf('id')]]['buyer_buyer']=$row[csf('buyer_buyer')];
			}
			//print_r($ordDtls_arr); die;
			$total_order_quantity=0; $receiveDtlsId_arr=array(); $buyer_buyer=$break_data='';  $req_quantity_arr=array(); 
			foreach($sqlDtls_result as $row)
			{
				//echo $total_req_quantity."++";
				if ($l%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				//$jobQnty
				$material_colors=explode('__',$row[csf('material_color')]); $metColor=''; $jobQnty='';
				for($j=0; $j<count($material_colors); $j++)
				{
					if($material_colors[$j]!='')
					{
						$metColor.=$color_arr[$material_colors[$j]].",";
					}
				}
				$subcon_break_ids=explode(",",chop($row[csf("subcon_break_ids")],','));
				$job_Qnty=0;
				for($j=0; $j<count($subcon_break_ids); $j++)
				{
					$job_Qnty +=$job_qnty_arr[$subcon_break_ids[$j]][$row[csf("conv_factor")]]['qnty']; 
				}
				//$jobQnty=round($jobQnty);
				$jobQnty=number_format($job_Qnty,4);
				$break_data=chop($break_arr[$row[csf('id')]]['info'],"**");
				$break_info=explode('**',$break_data);
				$metDesc=''; $metPcsUnit=''; $metUnit=''; $metReqQty=''; $metProcLoss=''; $metRemark='';
				
				$receive_dtls_ids=array_unique(explode(",",$row[csf("receive_dtls_id")]));
				$order_quantity=''; 
				for($j=0; $j<count($receive_dtls_ids); $j++)
				{
					if (!in_array($receive_dtls_ids[$j], $receiveDtlsId_arr))
					{
						$order_quantity+=$ordDtls_arr[$receive_dtls_ids[$j]]['order_quantity'];
						if($data[2]==1)
						{
							$buyer_buyer.=$buyer_library_arr[$ordDtls_arr[$receive_dtls_ids[$j]]['buyer_buyer']].',';
						}
						else
						{
							$buyer_buyer.=$ordDtls_arr[$receive_dtls_ids[$j]]['buyer_buyer'].',';
						}
						$receiveDtlsId_arr[]=$receive_dtls_ids[$j];
					}
				}
				$total_order_quantity+=$order_quantity;
				$buyer_buyers=chop(implode(',',array_unique(explode(",",$buyer_buyer))),',');
				?>
				<script type="text/javascript">
					var total_order_quantity='<? echo ": ".$total_order_quantity; ?>';
					var buyer_buyerss='<? echo ": ".$buyer_buyers; ?>';
					//document.getElementById('td_order_qty').innerHTML=total_order_quantity;
					document.getElementById('td_cus_buyer').innerHTML=buyer_buyerss;
				</script>
				
				<tr bgcolor="<? echo $bgcolor; ?>"> 
					<td><? echo $l; ?></td>
					<td><p><? echo implode(',',array_unique(explode(',',$row[csf('buyer_po_no')]))); ?></p></td>
					<td><p><? echo implode(',',array_unique(explode(',',$row[csf('buyer_style_ref')]))); ?></p></td>
					<td><p><? echo $row[csf('item_description')].",".$color_arr[$row[csf('color_id')]]; ?></p></td>
					<td><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
					<td align="right" colspan="2"><p><? echo number_format($jobQnty,4);?> </p></td>
					<td><p><? echo $row[csf('impression')]; ?></p></td>
					<td><p><? echo chop($metColor,","); ?></p></td>
					<td></td>
				</tr>
				<tr bgcolor="<? echo "#d6eaf8"; ?>">
					<td></td>
					<td style="text-align:center; font-size: 14px;"><strong>Raw Materials</strong></td>
					<td style="text-align:center; font-size: 14px;"><strong>Brand Name</strong></td>
					<td style="text-align:center; font-size: 14px; display: none;"><? echo $unit_of_measurement[$row[csf('uom')]]; ?> / Unit </td>
					<td style="text-align:center; font-size: 14px;"><strong>UOM</strong></td>
					<td style="text-align:center; font-size: 14px;"><strong>Total Cons/Unit</strong></td>
                    <td colspan="2" style="text-align:center; font-size: 14px;"><strong>Total Req.Qty</strong></td>
					<td style="text-align:center; font-size: 14px;"><strong>Pro Loss%</strong></td>
					<td style="text-align:center; font-size: 14px;"><strong>Remarks</strong></td>
					<td style="text-align:center; font-size: 14px;"><strong>LOT</strong></td>

				</tr>
				<?
				/*echo "<pre>";
				print_r($break_info);*/
				$pcs_unit='';  $req_quantity='';

				for($j=0; $j<count($break_info); $j++)
				{
					if ($j%2==0) $dtlbgcolor="#f0fbfc"; else $dtlbgcolor="#fcf5f8";
					$break_infos=explode('_',$break_info[$j]);
					$pcs_unit=$job_Qnty/$break_infos[3];
					//echo $job_Qnty.'=='.$break_infos[3].'++';
					$req_quantity=$job_Qnty*$break_infos[3];
					//echo $job_Qnty.'=='.$break_infos[3].'=='.$req_quantity.'++';
					$req_quantity_arr[$break_infos[6]] +=$req_quantity;
					//$total_req_quantity+=$req_quantity;
					$batch_lot=$break_infos[7];
					?>
					<tr bgcolor="<? echo $dtlbgcolor; ?>">
						<td></td>
						<td><p><?  echo $break_infos[0] ; ?></p></td>
						<td><p><?  echo $brand_arr[$break_infos[6]] ; ?></p></td>
						<td style="display:none;"><p><?  echo number_format($pcs_unit,2);//$break_infos[1] ; ?></p></td>
						<td><p><?  echo $unit_of_measurement[$break_infos[2]] ; ?></p></td>
						<td align="right"><p><?  echo $break_infos[3] ; ?></p></td>
                        <td colspan="2" align="right" ><p><?  echo number_format($req_quantity,4); ?></p></td>
						<td align="right"><p><?  echo $break_infos[4] ; ?></p></td>
						<td><p><?  echo $break_infos[5] ; ?></p></td>
						<td><p><?  echo $batch_lot; ?></p></td>

					</tr>
					<?
					//echo $total_req_quantity."==".$req_quantity."</br>";
				}
				$l++;
			}
			//echo "<pre>";
			//print_r($req_quantity_arr);
			?>
		</table>
		<div style="width: 400px ;  margin-top:5px; float: left;"  align="left">
			<? 
			   	echo get_spacial_instruction($data[1],"100%",257);
			?>
		</div>
		<div style="width: 470px; float: right; margin-top:5px;">
			<table align="right" cellspacing="0" width="460"  border="1" rules="all" class="rpt_table">
				<thead bgcolor="#dddddd">
					<tr>
						<th colspan="5" style="text-align:center; font-size: 17px;">Summary</th>
					</tr>
					<tr>
						<th width="30">SL</th>
						<th width="220">Item</th>
						<th width="120">Brand Name</th>
						<th width="60">UOM</th>
						<th width="100">Total Req.Qty</th>
					</tr>
				</thead>
				<tbody>
				<?
					$k=1;  
					foreach($break_arr_summery as $item=>$break_arr_val)
					{
						foreach($break_arr_val as $uom=>$brand_val)
						{
							foreach($brand_val as $brand=>$qnty)
							{
								//echo "<pre>";
								//print_r($brand);$dtlbgcolor="#f0fbfc"; else $dtlbgcolor="#fcf5f8";
								if($k%2==0) $sumBgcolor="#f0fbfc"; else $sumBgcolor="#fcf5f8";
								?>
								<tr bgcolor="<? echo $sumBgcolor; ?>"> 
									<td><? echo $k; ?></td>
									<td><p><? echo $item; ?></p></td>
									<td><p><? echo $brand_arr[$brand]; ?></p></td>
									<td><p><? echo $unit_of_measurement[$uom]; ?></p></td>
									<td><p><? echo number_format($req_quantity_arr[$brand],4);//$qnty;?> </p></td>
								</tr>
								<?
								$k++;
							}
						}
					}
					?>
					</tbody>
				</table>
			</div>
		<br>
	</div>
	<?
    	echo signature_table(160, $data[0], "1100px");
    ?>
</div>
<?
}


if($action=="job_card_print_3")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//echo "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]'  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2, 3)) order by buyer_name"; die;
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	//$party_library=return_library_array( "select id, supplier_name from lib_supplier", "id","supplier_name"  );
	$buyer_library_arr=return_library_array("select id, buyer_name from lib_buyer where status_active=1","id","buyer_name");
	$team_leader_arr=return_library_array("select id,team_leader_name from lib_marketing_team where  status_active =1 and is_deleted=0 and project_type=3","id","team_leader_name");

	$team_member_arr=return_library_array("select b.id,b.team_member_name  from lib_marketing_team a, lib_mkt_team_member_info b where a.id=b.team_id and   a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.project_type=3","id","team_member_name");

	
	//$buyer_library_arr=return_library_array("select id,buyer_name from lib_buyer where status_active=1","id","buyer_name");$brand_arr
	//$order_array=return_library_array( "select id, order_no from subcon_ord_dtls", "id","order_no"  );
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$group_name_arr=return_library_array( "select id, item_name from lib_item_group where item_category=4 and status_active=1",'id','item_name');  
	$itemGroup_arr=return_library_array( "select id,item_name from lib_item_group where status_active=1 and is_deleted=0 order by item_name",'id','item_name');
	$brand_arr=return_library_array( "select id, brand_name from product_details_master",'id','brand_name');
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	$user_lib_name_arr=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
	
	if($data[2]==1)
	{
		$party_arr=return_library_array( "select id,company_name from  lib_company where status_active=1 and is_deleted=0 order by company_name",'id','company_name');
	}
	else
	{
		$party_arr=return_library_array( "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name",'id','buyer_name');
	}
	$sql_mst="select a.id, a.trims_job, a.job_no_prefix, a.job_no_prefix_num,  a.company_id, a.location_id, a.within_group, a.party_id,  a.party_location , a.delivery_date, a.order_id, a.order_qty, a.received_no, a.received_id, a.section_id , b.receive_date, b.delivery_date, b.order_no, b.team_leader, b.team_member, a.inserted_by from trims_job_card_mst a , subcon_ord_mst b where a.received_id=b.id and a.company_id=$data[0] and a.id='$data[1]' and a.status_active=1 and a.is_deleted=0";
	$dataArray=sql_select($sql_mst);
	$job_qnty_arr=array();
	$order_qnty_arr=array();
	//$qty_sql ="SELECT a.id, a.booked_qty from subcon_ord_breakdown a where a.booked_qty is not null and a.booked_qty!=0";
	$qty_sql= "select b.id as receive_details_id,a.id,a.description,a.color_id,a.size_id,b.sub_section,b.booked_uom,b.booked_conv_fac, a.booked_qty, b.order_quantity, b.order_uom  from subcon_ord_dtls b ,subcon_ord_breakdown a where a.mst_id=b.id  and b.job_no_mst=a.job_no_mst and a.booked_qty is not null and a.booked_qty!=0  and a.status_active=1";
	$qty_sql_res=sql_select($qty_sql);
	foreach ($qty_sql_res as $row)
	{
		$job_qnty_arr[$row[csf("id")]][$row[csf("booked_conv_fac")]]['qnty']=$row[csf("booked_qty")];
		$order_qnty_arr[$row[csf("id")]]['order_quantity']=$row[csf("order_quantity")];
		$order_qnty_arr[$row[csf("id")]]['order_uom']=$row[csf("order_uom")];
	}
	unset($qty_sql_res);
	$received_id=$dataArray[0][csf('received_id')];
	$inserted_by=$dataArray[0][csf('inserted_by')];
	//echo "select trims_ref from subcon_ord_mst where entry_form=255 and id=$received_id and status_active=1 and is_deleted=0"; die;
	$trims_ref=return_field_value( "trims_ref", "subcon_ord_mst"," entry_form=255 and id=$received_id and status_active=1 and is_deleted=0");
	//echo "<pre>";
	//print_r($party_arr);
	//die;
	
	?>
    <style>
	p {
		word-break: break-all;
	}
    </style>
    
    <? 
	?>
	<div style="width:1060px;">
		<table width="1060" cellspacing="0" align="center" border="0">
			<tr>
				<td colspan="6"></td>
			</tr>
			<tr>
				<td colspan="2" rowspan="2">
					<img src="../../<? echo $image_location; ?>" height="60" width="200" style="float:left;">
				</td>
				<td colspan="4" align="center" style="font-size:x-large">
					<strong><? echo $company_library[$data[0]]; ?></strong>
				</td>
			</tr>
			<tr>
				<td colspan="4" align="center">
					<?
					$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website, vat_number from lib_company where id=$data[0] and status_active=1 and is_deleted=0"); 
					foreach ($nameArray as $result)
					{ 
						?>
						<? echo $result[csf('plot_no')]; ?>,
						Level No: <? echo $result[csf('level_no')]?>,
						<? echo $result[csf('road_no')]; ?>, 
						<? echo $result[csf('block_no')];?>, 
						<? echo $result[csf('city')];?>, 
						<? echo $result[csf('zip_code')]; ?>, 
						<?php echo $result[csf('province')];?>, 
						<? echo $country_arr[$result[csf('country_id')]]; ?><br> 
						Email Address: <? echo $result[csf('email')];?> 
						Website No: <? echo $result[csf('website')];?> <br>
						<b> Vat No : <? echo $result[csf('vat_number')]; ?></b> <?
							//$party_id= $result[csf('party_id')];
					}
					?> 
				</td>
			</tr> 

			<tr>
				<td colspan="6" align="center" style="font-size:20px"><u>
					<strong style="margin-left:265px;"><? echo $data[3]; ?></strong></u>
					<strong style="margin-left:265px;">Section: <? echo $trims_section[$dataArray[0][csf('section_id')]]; ?></strong></u>
				</td>
			</tr>
			<br>
			<tr>
				<td width="175">Party </td>
				<td width="175">: <strong> <? echo $party_arr[$dataArray[0][csf('party_id')]]; ?></strong></td>
				<td width="175">Job Card No.</td>
				<td width="175">: <strong> <? echo $dataArray[0][csf('trims_job')];  ?></strong></td>
				<td width="175" colspan="2" align="right">Team Leader</td>
				<td width="175" >: <strong> <? echo $team_leader_arr[$dataArray[0][csf('team_leader')]];  ?></strong></td>
			</tr>
			<tr>
				<td width="175">Order Receive Date </td> 
				<td width="175"><? echo ": ".change_date_format($dataArray[0][csf('receive_date')]); ?></td>
				<td width="175">Buyer's Buyer</td>
                 <td width="175" id="td_cus_buyer"></td>
				<td width="175" colspan="2" align="right">Team Member</td>
				<td width="175" >: <strong> <? echo $team_member_arr[$dataArray[0][csf('team_member')]];  ?></strong></td>
			</tr>
			<tr>
				<td width="175">Target Delivery</td>
				<td width="175"><? echo ": ".change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
				<td width="175">W/O No </td>
				<td width="175"><? echo ": ".$dataArray[0][csf('order_no')]; ?></td>
				<td width="175" colspan="2" align="right">Total Order Qty </td>
				<td width="175" id="td_order_qty"></td>
			</tr>
			<tr>
				<td width="175">Trims Ref. </td>
				<td width="175"><? echo ": ".$trims_ref; ?></td>
				<td width="175"></td>
				<td width="175"></td>
				<td width="175" colspan="2" align="right">Total Booked Qty </td>
				<td width="175"><? echo ": ".$dataArray[0][csf('order_qty')]; ?></td>
			</tr>
			
		</table>
		<br>
		<div style="width:100%;">
			<table align="left" cellspacing="0" width="1240"  border="1" rules="all" class="rpt_table"  >
				<thead bgcolor="#dddddd">
					<tr>
						<th colspan="10" style="text-align:center; font-size: 18px;">Material Consumption</th>
					</tr>
					<tr>						
						<th width="30" style="font-size: 16px;">SL</th>
						<th width="175" style="font-size: 16px;">Buyer Order</th>
						<th width="175" style="font-size: 16px;">Buyer Style</th>
						<th width="200" style="font-size: 16px;">Item Description</th>
						<th width="100" style="font-size: 16px;">Item Color</th> 
						<th width="100" style="font-size: 16px;">Item Size</th> 
						<th width="100" style="font-size: 16px;">Order Qty</th> 
						<th width="100" style="font-size: 16px;">Order Uom</th> 
						<th width="100" style="font-size: 16px;">Booked Qty</th> 
						<th width="" style="font-size: 16px;">Booked UOM</th> 
						<!-- <th width="150" colspan="2" style="font-size: 16px;">Item Qty</th> 
						<th width="70" style="font-size: 16px;">Impression</th>
						<th>Color</th>  -->
					</tr>
				</thead>         
				<?	 
			$l=1;
			$mst_id=$data[1];
			$trims_job=$dataArray[0][csf('trims_job')]; //die;
			$sqlDtls_result =sql_select("select  id, mst_id, job_no_mst, receive_dtls_id, item_group_id, booking_dtls_id, book_con_dtls_id, break_id as subcon_break_ids, buyer_po_id,buyer_po_no, buyer_style_ref, item_description, color_id, size_id, uom, job_quantity,  impression, material_color,conv_factor from trims_job_card_dtls where mst_id=$data[1] and status_active=1 and is_deleted=0");

			$sqlBreak_result =sql_select("select  a.id, a.mst_id, a.order_id, a.job_no_mst, a.book_con_dtls_id, a.product_id, a.description, a.specification, a.pcs_unit, a.cons_qty, a.process_loss, a.process_loss_qty, a.req_qty, a.remarks,b.unit_of_measure as unit, b.item_group_id from trims_job_card_breakdown a , product_details_master b where a.product_id=b.id and b.entry_form in (334,220,300,285,0) and a.status_active=1 and a.is_deleted=0 and a.job_no_mst='$trims_job'");

			//and b.entry_form in (334 , 220,300,285,0 )
			//echo "select  a.id, a.mst_id, a.order_id, a.job_no_mst, a.book_con_dtls_id, a.product_id, a.description, a.specification, a.pcs_unit, a.cons_qty, a.process_loss, a.process_loss_qty, a.req_qty, a.remarks,b.unit_of_measure as unit, b.item_group_id from trims_job_card_breakdown a , product_details_master b where a.product_id=b.id and b.entry_form in (334,220,300,285,0) and a.status_active=1 and a.is_deleted=0 and a.job_no_mst='$trims_job'";
			$break_arr=array(); $break_arr_summery=array();
			foreach($sqlBreak_result as $row)
			{
				$break_arr[$row[csf('mst_id')]]['info'].=$row[csf('description')]."_".$row[csf('pcs_unit')]."_".$row[csf('unit')]."_".$row[csf('req_qty')]."_".$row[csf('process_loss')]."_".$row[csf('remarks')]."_".$row[csf('product_id')]."_".$row[csf('cons_qty')]."_".$row[csf('item_group_id')]."**";
				$break_arr_summery[$row[csf('description')]][$row[csf('unit')]][$row[csf('product_id')]]+=$row[csf('req_qty')];
			}
			
			//echo $received_id; die;
			
			$sqlOrdDtls_result =sql_select("select  id, order_quantity, order_uom, buyer_buyer from subcon_ord_dtls where mst_id=$received_id");
			$ordDtls_arr=array();
			foreach($sqlOrdDtls_result as $row)
			{
				$ordDtls_arr[$row[csf('id')]]['order_quantity']+=$row[csf('order_quantity')];
				
				$ordDtls_arr[$row[csf('id')]]['buyer_buyer']=$row[csf('buyer_buyer')];
				$ordDtls_arr[$row[csf('id')]]['order_uom']=$row[csf('order_uom')];
			}




			$sqlOrdDtls2 ="select  b.id, a.order_quantity, a.order_uom, a.buyer_buyer, b.qnty from subcon_ord_dtls a, subcon_ord_breakdown b where a.id=b.mst_id and a.mst_id=$received_id";
			//echo $sqlOrdDtls;
			//$sqlOrdDtls_result =sql_select("select  id, order_quantity, order_uom, buyer_buyer from subcon_ord_dtls where mst_id=$received_id");
			$sqlOrdDtls_result2 =sql_select($sqlOrdDtls2);
			
			$ordDtls_qty_arr=array();
			foreach($sqlOrdDtls_result2 as $row)
			{
				
				$ordDtls_qty_arr[$row[csf('id')]]['qnty']=$row[csf('qnty')];
				
			}
			/*echo "<pre>";
			print_r($ordDtls_arr); die;*/
			$total_order_quantity=0; $receiveDtlsId_arr=array(); $order_uom=$buyer_buyer=$break_data='';  $req_quantity_arr=array(); 
			foreach($sqlDtls_result as $row)
			{
				//echo $total_req_quantity."++";
				if ($l%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				//$jobQnty
				$material_colors=explode('__',$row[csf('material_color')]); $metColor=''; $jobQnty='';
				for($j=0; $j<count($material_colors); $j++)
				{
					if($material_colors[$j]!='')
					{
						$metColor.=$color_arr[$material_colors[$j]].",";
					}
				}
				$subcon_break_ids=explode(",",chop($row[csf("subcon_break_ids")],','));


				$order_dtls_qnty=''; 
				for($j=0; $j<count($subcon_break_ids); $j++)
				{
					$order_dtls_qnty+=$ordDtls_qty_arr[$subcon_break_ids[$j]]['qnty'];
					
				}



				$job_Qnty=0;
				for($j=0; $j<count($subcon_break_ids); $j++)
				{
					$job_Qnty +=$job_qnty_arr[$subcon_break_ids[$j]][$row[csf("conv_factor")]]['qnty']; 
				}
				//$jobQnty=round($jobQnty);
				$jobQnty=number_format($job_Qnty,4);
				$break_data=chop($break_arr[$row[csf('id')]]['info'],"**");
				$break_info=explode('**',$break_data);
				$metDesc=''; $metPcsUnit=''; $metUnit=''; $metReqQty=''; $metProcLoss=''; $metRemark='';
				
				$receive_dtls_ids=array_unique(explode(",",$row[csf("receive_dtls_id")]));
				$order_quantity=''; 
				
				for($j=0; $j<count($receive_dtls_ids); $j++)
				{
					if (!in_array($receive_dtls_ids[$j], $receiveDtlsId_arr))
					{
						$order_quantity+=$ordDtls_arr[$receive_dtls_ids[$j]]['order_quantity'];
						
						if($data[2]==1)
						{
							$buyer_buyer.=$buyer_library_arr[$ordDtls_arr[$receive_dtls_ids[$j]]['buyer_buyer']].',';
							$order_uom.=$unit_of_measurement[$ordDtls_arr[$receive_dtls_ids[$j]]['order_uom']].',';
						}
						else
						{
							$buyer_buyer.=$ordDtls_arr[$receive_dtls_ids[$j]]['buyer_buyer'].',';
							$order_uom.=$ordDtls_arr[$receive_dtls_ids[$j]]['order_uom'].',';
						}
						$receiveDtlsId_arr[]=$receive_dtls_ids[$j];
					}
				}
				$total_order_quantity+=$order_quantity;
				$buyer_buyers=chop(implode(',',array_unique(explode(",",$buyer_buyer))),',');
				$order_uoms=chop(implode(',',array_unique(explode(",",$order_uom))),',');
				?>
				<script type="text/javascript">
					var total_order_quantity='<? echo ": ".$total_order_quantity; ?>';
					var buyer_buyerss='<? echo ": ".$buyer_buyers; ?>';
					document.getElementById('td_order_qty').innerHTML=total_order_quantity;
					document.getElementById('td_cus_buyer').innerHTML=buyer_buyerss;
				</script>
				
				<tr bgcolor="<? echo $bgcolor; ?>"> 
					<td><? echo $l; ?></td>
					<td><p><? echo implode(',',array_unique(explode(',',$row[csf('buyer_po_no')]))); ?></p></td>
					<td><p><? echo implode(',',array_unique(explode(',',$row[csf('buyer_style_ref')]))); ?></p></td>
					<td><p><? echo $row[csf('item_description')]; ?></p></td>
					<td><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
					<td><p><? echo $size_arr[$row[csf('size_id')]]; ?></p></td>
					<td><p><? echo $order_dtls_qnty; ?></p></td>
					<td><p><? echo $unit_of_measurement[$order_uoms]; ?></p></td>
					<td align="right"><p><? echo $jobQnty;?> </p></td>
					<td align="center"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
					<!-- <td align="right" colspan="2"><p><? //echo $jobQnty;?> </p></td>
					<td><p><? //echo $row[csf('impression')]; ?></p></td>
					<td><p><? //echo chop($metColor,","); ?></p></td> -->
				</tr>
				<tr bgcolor="<? echo "#d6eaf8"; ?>">
					<td></td>
					<td style="text-align:center; font-size: 14px;"><strong>Group Name</strong></td>
					<td colspan="2" style="text-align:center; font-size: 14px;"><strong>Raw Materials</strong></td>
					<td style="text-align:center; font-size: 14px;"><strong>UOM</strong></td>
					<td style="text-align:center; font-size: 14px;"><strong>Cons/Unit</strong></td>
					<td style="text-align:center; font-size: 14px;"><strong>Pro Loss%</strong></td>
					<td style="text-align:center; font-size: 14px;"><strong>Total Cons/Unit</strong></td>
                    <td style="text-align:center; font-size: 14px;"><strong>Total Req.Qty</strong></td>
					<td style="text-align:center; font-size: 14px;"><strong>Remarks</strong></td>
				</tr>
				<?
				/*echo "<pre>";
				print_r($break_info);*/
				$pcs_unit='';  $req_quantity='';

				for($j=0; $j<count($break_info); $j++)
				{
					if ($j%2==0) $dtlbgcolor="#f0fbfc"; else $dtlbgcolor="#fcf5f8";
					$break_infos=explode('_',$break_info[$j]);
					$pcs_unit=$job_Qnty/$break_infos[3];
					//echo $job_Qnty.'=='.$break_infos[3].'++';
					$req_quantity=$job_Qnty*$break_infos[3];
					//echo $job_Qnty.'=='.$break_infos[3].'=='.$req_quantity.'++';
					$req_quantity_arr[$break_infos[6]] +=$req_quantity;
					//$total_req_quantity+=$req_quantity;
					
					?>
					<tr bgcolor="<? echo $dtlbgcolor; ?>">
						<td></td>
						<td><p><?  echo $itemGroup_arr[$break_infos[8]]; ?></p></td>
						<td colspan="2"><p><?  echo $break_infos[0] ; ?></p></td>
						<td><p><?  echo $unit_of_measurement[$break_infos[2]] ; ?></p></td>
						<td align="right"><p><?  echo $break_infos[7] ; ?></p></td>
						<td align="right"><p><?  echo $break_infos[4] ; ?></p></td>
						<td align="right"><p><?  echo  number_format($break_infos[3],6) ; ?></p></td>
                        <td align="right" ><p><?  echo  number_format($req_quantity,6); ?></p></td>
						<td><p><?  echo $break_infos[5] ; ?></p></td>
					</tr>
					<?
					//echo $total_req_quantity."==".$req_quantity."</br>";
				}
				$l++;
			}
			//echo "<pre>";
			//print_r($req_quantity_arr);
			?>
		</table>
		<div style="width: 400px ;  margin-top:5px; float: left;"  align="left">
			<? 
			   	echo get_spacial_instruction($data[1],"100%",257);
			?>
		</div>
		<div style="width: 400px; float: right; margin-top:5px;">
			<table align="right" cellspacing="0" width="390"  border="1" rules="all" class="rpt_table">
				<thead bgcolor="#dddddd">
					<tr>
						<th colspan="4" style="text-align:center; font-size: 17px;">Summary</th>
					</tr>
					<tr>
						<th width="30">SL</th>
						<th width="220">Item Description</th>
						<th width="60">UOM</th>
						<th width="150">Total Req.Qty</th>
					</tr>
				</thead>
				<tbody>
				<?
					$k=1;  
					foreach($break_arr_summery as $item=>$break_arr_val)
					{
						foreach($break_arr_val as $uom=>$brand_val)
						{
							foreach($brand_val as $brand=>$qnty)
							{
								//echo "<pre>";
								//print_r($brand);$dtlbgcolor="#f0fbfc"; else $dtlbgcolor="#fcf5f8";
								if($k%2==0) $sumBgcolor="#f0fbfc"; else $sumBgcolor="#fcf5f8";
								?>
								<tr bgcolor="<? echo $sumBgcolor; ?>"> 
									<td><? echo $k; ?></td>
									<td><p><? echo $item; ?></p></td>
									<td><p><? echo $unit_of_measurement[$uom]; ?></p></td>
									<td align="right" ><p><? echo  number_format($req_quantity_arr[$brand],6);//$qnty;?> </p></td>
								</tr>
								<?
								$k++;
							}
						}
					}
					?>
					</tbody>
				</table>
			</div>
		<br>
	</div>
	<?
    	//echo signature_table(160, $data[0], "1100px");
	echo signature_table(160, $data[0], "1240px",'',70,$user_lib_name_arr[$inserted_by]);
    ?>
</div>
<?
}



if($action=="job_card_print2")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library_arr=return_library_array("select id, buyer_name from lib_buyer where status_active=1","id","buyer_name");
	
	$address_1=return_library_array("select id, address_1 from lib_buyer where status_active=1","id","address_1");
    $address_2=return_library_array("select id, address_2 from lib_buyer where status_active=1","id","address_2");
	
	$lib_location_arr=return_library_array( "select id,location_name from lib_location where status_active=1 and is_deleted=0", "id","location_name" );
	
	$team_leader_name=return_library_array("select id, team_leader_name from lib_marketing_team where status_active=1","id","team_leader_name");
	$team_member_name=return_library_array("select id, team_member_name from lib_mkt_team_member_info where status_active=1","id","team_member_name");
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$size_name_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	if($data[2]==1)
	{
		$party_arr=return_library_array( "select id,company_name from  lib_company where status_active=1 and is_deleted=0 order by company_name",'id','company_name');
	}
	else
	{
		$party_arr=return_library_array( "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name",'id','buyer_name');
	}
	
	
	$sql_mst="select a.id, a.trims_job, a.job_no_prefix, a.job_no_prefix_num,  a.company_id, a.location_id, a.within_group, a.party_id,  a.party_location , a.delivery_date, a.order_id, a.order_qty, a.received_no, a.received_id, a.section_id , b.receive_date, b.delivery_date, b.order_no, b.team_leader, b.team_member 
	from trims_job_card_mst a , subcon_ord_mst b 
	where a.received_id=b.id and a.company_id=$data[0] and a.id='$data[1]' and a.status_active=1 and a.is_deleted=0";
	$dataArray=sql_select($sql_mst);
	$section_id=$dataArray[0][csf("section_id")];

	$job_qnty_arr=array();
	//$qty_sql ="SELECT a.id, a.booked_qty from subcon_ord_breakdown a where a.booked_qty is not null and a.booked_qty!=0";
	
	$qty_sql= "select b.id as receive_details_id,a.id,a.description,a.color_id,a.size_id,b.sub_section,b.booked_uom,b.booked_conv_fac, a.booked_qty  from subcon_ord_dtls b ,subcon_ord_breakdown a where a.mst_id=b.id  and b.job_no_mst=a.job_no_mst and a.booked_qty is not null and a.booked_qty!=0  and a.status_active=1";
	
	$qty_sql_res=sql_select($qty_sql);
	foreach ($qty_sql_res as $row)
	{
		//$job_qnty_arr[$row[csf("id")]]['qnty']=$row[csf("booked_qty")];
		$job_qnty_arr[$row[csf("id")]][$row[csf("booked_conv_fac")]]['qnty']=$row[csf("booked_qty")];
	}
	unset($qty_sql_res);
	?>
    <style>
	p {
		word-break: break-all;
	}
    </style>
    
    <? 
	?>
	<div style="width:1060px;">
	<table width="1060" cellspacing="0" align="center" border="0">
			<tr>
				<td colspan="6"></td>
			</tr>
			<tr>
				<td width="200" rowspan="2">
					<img src="../../<? echo $image_location; ?>" height="60" width="200" style="float:left;">
				</td>
				<td colspan="4" align="left" style="font-size:x-large">
					<strong><? echo $company_library[$data[0]]; ?></strong>
				</td>
        		<td id="barcode_img_id"></td>
			</tr>
			<tr>
            	
				<td colspan="4" align="left">
					<?
					$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website, vat_number from lib_company where id=$data[0] and status_active=1 and is_deleted=0"); 
					foreach ($nameArray as $result)
					{ 
						 echo $result[csf('city')];
							//$party_id= $result[csf('party_id')];
					}
					?> 
				</td>
                <td align="center"><?  echo $dataArray[0][csf('trims_job')];  ?></td>
			</tr> 
            
		</table>
        <style>
        #job_no_tbl,#section_id_tbl{ margin-top:15px;}
		#details_id_tbl{ margin-top:15px; word-wrap:break-word; word-break:break-all}
        </style>
        
        <table border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="1060" rules="all" id="job_no_tbl">
        	<tr style="font-weight:bold;">
            	<td width="160" align="left">Job Card No<span style="float:right">:</span></td>
            	<td width="360"><? echo $dataArray[0][csf('trims_job')]; ?></td>
            	<td width="100" style="border-top:hidden; border-bottom:hidden;">&nbsp;</td>
            	<td width="150" align="left">Order Qty <span style="float:right">:</span></td>
            	<td width="150" id="td_total_order_quantity"></td>
                <td width="100" style="border-top:hidden; border-bottom:hidden;">&nbsp;</td>
            	<td width="150" align="left">Section<span style="float:right">:</span></td>
            	<td width="200"><? echo $trims_section[$dataArray[0][csf('section_id')]]; ?></td>
            </tr>
            <tr>
            	<td colspan="8" style="border-left:hidden; border-right:hidden;">&nbsp;</td>
            </tr>
            <tr>
            	<td align="left">Customer Name<span style="float:right">:</span></td>
                <td><? echo $party_arr[$dataArray[0][csf('party_id')]]; ?></td>
				<td style="border-top:hidden; border-bottom:hidden;">&nbsp;</td>
                <td align="left">WO No<span style="float:right">:</span></td>
                <td><? echo $dataArray[0][csf('order_no')]; ?></td>
                <td style="border-top:hidden; border-bottom:hidden;">&nbsp;</td>
                <td align="left">Customer's Buyer<span style="float:right">:</span></td>
                <td id="td_cus_buyer"></td>
            </tr>
            <tr>
            	<td align="left">Invoice Address<span style="float:right">:</span></td> 
                <td><p><? if($data[2]==1){ echo $lib_location_arr[$dataArray[0][csf('location_id')]];//$lib_location_arr[$data[2]]; 
				}else{echo $address_1[$dataArray[0][csf('party_id')]];}
				
				 ?></p></td>
				<td style="border-top:hidden; border-bottom:hidden;">&nbsp;</td>
                <td align="left">Order Rcv Date<span style="float:right">:</span></td>
                <td><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
                <td style="border-top:hidden; border-bottom:hidden;">&nbsp;</td>
                <td align="left">Merchandiser<span style="float:right">:</span></td>
                <td><? echo $team_member_name[$dataArray[0][csf('team_member')]]; ?></td>
            </tr>
             <tr>
            	<td align="left">Delivery Address<span style="float:right">:</span></td>
                <td><p><? if($data[2]==1){ echo $nameArray[0][csf('city')]; }else{echo $address_2[$dataArray[0][csf('party_id')]]; }  ?></p></td>
				<td style="border-top:hidden; border-bottom:hidden;">&nbsp;</td>
                <td align="left">Target Delivery<span style="float:right">:</span></td>
                <td><? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
                <td style="border-top:hidden; border-bottom:hidden;">&nbsp;</td>
                <td align="left">Marketing Lead<span style="float:right">:</span></td>
                <td><? echo $team_leader_name[$dataArray[0][csf('team_leader')]]; ?></td>
            </tr>
        </table>
		<div style="width:100%;" id="details_id_tbl">
			<table align="left" cellspacing="0" width="1060"  border="1" rules="all" class="rpt_table"  >
				<thead bgcolor="#dddddd">
					<tr>
						<th colspan="13" style="text-align:center; font-size: 18px;">Order & Product Details</th>
					</tr>
					<tr>						
						<th width="30" style="font-size: 16px;">SL</th>
						<th width="110" style="font-size: 16px;">Buyer Order</th>
						<th width="70" style="font-size: 16px;">Lot</th>
						<th width="110" style="font-size: 16px;">Buyer Style</th>
						<th width="120" style="font-size: 16px;">Item Description</th>
						<th width="60" style="font-size: 16px;">UOM</th> 
						<th width="90" style="font-size: 16px;">Gmts Color</th>
                        <th width="80" style="font-size: 16px;">Gmts Size</th>
                        <th width="80" style="font-size: 16px;">Item Color</th>
                        <th width="80" style="font-size: 16px;">Item Size</th>
						<th width="100" colspan="2" style="font-size: 16px;">Item Qty</th> 
						<th>Remarks</th> 
					</tr>
				</thead>         
				<?	 
			$l=1;
			$mst_id=$data[1];
			$trims_job=$dataArray[0][csf('trims_job')];

			$sqlDtls_result =sql_select("select  id, mst_id, job_no_mst, receive_dtls_id, booking_dtls_id, book_con_dtls_id, break_id as subcon_break_ids, buyer_po_id,buyer_po_no, buyer_style_ref, item_description, gmts_color_id, gmts_size_id, color_id, size_id, uom, job_quantity,  impression, material_color,conv_factor from trims_job_card_dtls 
			where mst_id=$data[1] and status_active=1 and is_deleted=0");
			
			/* $sqlBreak_result =sql_select("select  a.id, a.mst_id, a.order_id, a.job_no_mst, a.book_con_dtls_id, a.product_id, a.description, a.specification, a.pcs_unit, a.cons_qty, a.process_loss, a.process_loss_qty, a.req_qty, a.remarks,b.unit_of_measure as unit 
			 from trims_job_card_breakdown a , product_details_master b 
			 where a.product_id=b.id and a.job_no_mst='$trims_job' and b.entry_form in (334,220,300,285,0) and a.status_active=1 and a.is_deleted=0");
			 $sql_brak="select  a.id, a.mst_id, a.order_id, a.job_no_mst, a.book_con_dtls_id, a.product_id, a.description, a.specification, a.pcs_unit, a.cons_qty, a.process_loss, a.process_loss_qty, a.req_qty, a.remarks,b.unit_of_measure as unit, null as batch_lot
			 from trims_job_card_breakdown a , product_details_master b 
			 where a.product_id=b.id and a.job_no_mst='$trims_job' and b.entry_form in (334,220,300,285,0) and a.status_active=1 and a.is_deleted=0 and b.item_category_id=101";
			 if($section_id==25)
			 {
			 	$sql_brak.="union all select  a.id, a.mst_id, a.order_id, a.job_no_mst, a.book_con_dtls_id, a.product_id, a.description, a.specification, a.pcs_unit, a.cons_qty, a.process_loss, a.process_loss_qty, a.req_qty, a.remarks,b.unit_of_measure as unit , c.batch_lot
			 	from trims_job_card_breakdown a , product_details_master b, inv_transaction c  
			 	where a.product_id=b.id and b.id=c.prod_id and a.job_no_mst='$trims_job' and b.entry_form in (334,220,300,285,0) and a.status_active=1 and a.is_deleted=0 and b.item_category_id=22 and c.item_category=22
			 	group by a.id, a.mst_id, a.order_id, a.job_no_mst, a.book_con_dtls_id, a.product_id, a.description, a.specification, a.pcs_unit, a.cons_qty, a.process_loss, a.process_loss_qty, a.req_qty, a.remarks,b.unit_of_measure,c.batch_lot";
			 }*/

			$sql_brak="select  a.id, a.mst_id, a.order_id, a.job_no_mst, a.book_con_dtls_id, a.product_id, a.description, a.specification, a.pcs_unit, a.cons_qty, a.process_loss, a.process_loss_qty, a.req_qty, a.remarks,b.unit_of_measure as unit,a.lot
			 from trims_job_card_breakdown a , product_details_master b 
			 where a.product_id=b.id and a.job_no_mst='$trims_job' and b.entry_form in (334,220,300,285,0) and a.status_active=1 and a.is_deleted=0";
			//echo $sql_brak;

			$sqlBreak_result =sql_select($sql_brak);
			
			$break_arr=array(); $break_arr_summery=array();
			foreach($sqlBreak_result as $row)
			{
				$break_arr[$row[csf('mst_id')]]['info'].=$row[csf('description')]."_".$row[csf('pcs_unit')]."_".$row[csf('unit')]."_".$row[csf('req_qty')]."_".$row[csf('process_loss')]."_".$row[csf('remarks')]."_".$row[csf('product_id')]."_".$row[csf('lot')]."**";
				$break_arr_summery[$row[csf('description')]][$row[csf('unit')]][$row[csf('product_id')]]+=$row[csf('req_qty')];
			}
			$received_id=$dataArray[0][csf('received_id')];
			//echo $received_id; die;
			$sqlOrdDtls_result =sql_select("select  id, order_quantity, buyer_buyer from subcon_ord_dtls where mst_id=$received_id");
			$ordDtls_arr=array();
			foreach($sqlOrdDtls_result as $row)
			{
				$ordDtls_arr[$row[csf('id')]]['order_quantity']=$row[csf('order_quantity')];
				$ordDtls_arr[$row[csf('id')]]['buyer_buyer']=$row[csf('buyer_buyer')];
			}
			//print_r($ordDtls_arr); die;
			$total_order_quantity=0; $receiveDtlsId_arr=array(); $buyer_buyer=$break_data='';  $req_quantity_arr=array(); 
			foreach($sqlDtls_result as $row)
			{
				//echo $total_req_quantity."++";
				if ($l%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				//$jobQnty
				$material_colors=explode('__',$row[csf('material_color')]); $metColor=''; $jobQnty='';
				for($j=0; $j<count($material_colors); $j++)
				{
					if($material_colors[$j]!='')
					{
						$metColor.=$color_arr[$material_colors[$j]].",";
					}
				}
				$subcon_break_ids=explode(",",chop($row[csf("subcon_break_ids")],','));
				$job_Qnty=0;
				for($j=0; $j<count($subcon_break_ids); $j++)
				{
					$job_Qnty +=$job_qnty_arr[$subcon_break_ids[$j]][$row[csf("conv_factor")]]['qnty'];
				}
				//print_r($job_qnty_arr);
				
				//$jobQnty=round($jobQnty);
				$joborderquantity=$job_Qnty;
				$jobQnty=number_format($job_Qnty,4);
				$break_data=chop($break_arr[$row[csf('id')]]['info'],"**");
				$break_info=explode('**',$break_data);
				$metDesc=''; $metPcsUnit=''; $metUnit=''; $metReqQty=''; $metProcLoss=''; $metRemark='';
				
				$receive_dtls_ids=array_unique(explode(",",$row[csf("receive_dtls_id")]));
				$order_quantity=''; 
				for($j=0; $j<count($receive_dtls_ids); $j++)
				{
					if (!in_array($receive_dtls_ids[$j], $receiveDtlsId_arr))
					{
						$order_quantity+=$ordDtls_arr[$receive_dtls_ids[$j]]['order_quantity'];
						if($data[2]==1)
						{
							$buyer_buyer.=$buyer_library_arr[$ordDtls_arr[$receive_dtls_ids[$j]]['buyer_buyer']].',';
						}
						else
						{
							$buyer_buyer.=$ordDtls_arr[$receive_dtls_ids[$j]]['buyer_buyer'].',';
						}
						$receiveDtlsId_arr[]=$receive_dtls_ids[$j];
					}
				}
				//$total_order_quantity+=$order_quantity;
				$total_order_quantity+=$joborderquantity;
				$buyer_buyers=chop(implode(',',array_unique(explode(",",$buyer_buyer))),',');
				$pcs_unit='';  $req_quantity='';
				for($j=0; $j<count($break_info); $j++)
				{
					if ($j%2==0) $dtlbgcolor="#f0fbfc"; else $dtlbgcolor="#fcf5f8";
					$break_infos=explode('_',$break_info[$j]);
					$pcs_unit=$job_Qnty/$break_infos[3];
					$req_quantity=$job_Qnty*$break_infos[3];
					$req_quantity_arr[$break_infos[6]] +=$req_quantity;
					$batch_lot=$break_infos[7];
				}
				?>
				<script type="text/javascript">
					var total_order_quantity='<? echo $total_order_quantity; ?>';
					var buyer_buyerss='<? echo $buyer_buyers; ?>';
					document.getElementById('td_cus_buyer').innerHTML=buyer_buyerss;
					document.getElementById('td_total_order_quantity').innerHTML=total_order_quantity;
				</script>
				
				<tr bgcolor="<? echo $bgcolor; ?>"> 
					<td><? echo $l; ?></td>
					<td><p><? echo implode(',',array_unique(explode(',',$row[csf('buyer_po_no')]))); ?></p></td>
					<td><p><? echo $batch_lot; ?></p></td>

					<td><p><? echo implode(',',array_unique(explode(',',$row[csf('buyer_style_ref')]))); ?></p></td>
					<td><p><? echo $row[csf('item_description')]; ?></p></td>
					<td><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
					<td><p><? echo $color_arr[$row[csf('gmts_color_id')]]; ?></p></td>
                    <td><p><? echo $size_name_arr[$row[csf('gmts_size_id')]];; ?></p></td>
                    <td><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                    <td><p><? echo $size_name_arr[$row[csf('size_id')]];; ?></p></td>
					<td align="right" colspan="2"><p><? echo number_format($jobQnty,4);?> </p></td>
					<td><p><? echo $row[csf('impression')]; ?></p></td>
				</tr>
				
				<?
				//echo "<pre>";
				//print_r($break_info);				
				$l++;
			}
			//echo "<pre>";
			//print_r($req_quantity_arr);
			?>
		</table>
        
		<div style="width: 500px ;  margin-top:5px; float: left;"  align="left">
			<? 
			   	echo get_spacial_instruction($data[1],"115%",257);
			?>
		</div>
        
		<div style="width: 470px; float: right; margin-top:5px;">
			<table align="right" cellspacing="0" width="460"  border="1" rules="all" class="rpt_table">
				<thead bgcolor="#dddddd">
					<tr>
						<th colspan="5" style="text-align:center; font-size: 17px;">Summary</th>
					</tr>
					<tr>
						<th width="30">SL</th>
						<th width="220">Item</th>
						<th width="120">Brand Name</th>
						<th width="60">UOM</th>
						<th width="100">Total Req.Qty</th>
					</tr>
				</thead>
				<tbody>
				<?
					$k=1;  
					foreach($break_arr_summery as $item=>$break_arr_val)
					{
						foreach($break_arr_val as $uom=>$brand_val)
						{
							foreach($brand_val as $brand=>$qnty)
							{
								//echo "<pre>";
								//print_r($brand);$dtlbgcolor="#f0fbfc"; else $dtlbgcolor="#fcf5f8";
								if($k%2==0) $sumBgcolor="#f0fbfc"; else $sumBgcolor="#fcf5f8";
								?>
								<tr bgcolor="<? echo $sumBgcolor; ?>"> 
									<td><? echo $k; ?></td>
									<td><p><? echo $item; ?></p></td>
									<td><p><? echo $brand_arr[$brand]; ?></p></td>
									<td><p><? echo $unit_of_measurement[$uom]; ?></p></td>
									<td><p><? echo number_format($req_quantity_arr[$brand],4);//$qnty;?> </p></td>
								</tr>
								<?
								$k++;
							}
						}
					}
					?>
					</tbody>
				</table>
			</div>
            
		<br>
	</div>
	<?
    	echo signature_table(160, $data[0], "1100px");
    ?>
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
    function generateBarcode( valuess )
    {
        var value = valuess;//$("#barcodeValue").val();
        var btype = 'code39';//$("input[name=btype]:checked").val();
        var renderer ='bmp';// $("input[name=renderer]:checked").val();
        var settings = {
          output:renderer,
          bgColor: '#FFFFFF',
          color: '#000000',
          barWidth: 1,
          barHeight: 30,
          moduleSize:5,
          posX: 10,
          posY: 20,
          addQuietZone: 1
        };
       
         value = {code:value, rect: false};
        $("#barcode_img_id").show().barcode(value, btype, settings);
    } 
    generateBarcode("<? echo $dataArray[0][csf('trims_job')]; ?>");
    </script>
</div>
<?
}

if($action=="job_card_print2eeee")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library_arr=return_library_array("select id, buyer_name from lib_buyer where status_active=1","id","buyer_name");
	
	$address_1=return_library_array("select id, address_1 from lib_buyer where status_active=1","id","address_1");
    $address_2=return_library_array("select id, address_2 from lib_buyer where status_active=1","id","address_2");
	
	$team_leader_name=return_library_array("select id, team_leader_name from lib_marketing_team where status_active=1","id","team_leader_name");
	$team_member_name=return_library_array("select id, team_member_name from lib_mkt_team_member_info where status_active=1","id","team_member_name");
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$size_name_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	if($data[2]==1)
	{
		$party_arr=return_library_array( "select id,company_name from  lib_company where status_active=1 and is_deleted=0 order by company_name",'id','company_name');
	}
	else
	{
		$party_arr=return_library_array( "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name",'id','buyer_name');
	}
	
	
	$sql_mst="select a.id, a.trims_job, a.job_no_prefix, a.job_no_prefix_num,  a.company_id, a.location_id, a.within_group, a.party_id,  a.party_location , a.delivery_date, a.order_id, a.order_qty, a.received_no, a.received_id, a.section_id , b.receive_date, b.delivery_date, b.order_no, b.team_leader, b.team_member from trims_job_card_mst a , subcon_ord_mst b where a.received_id=b.id and a.company_id=$data[0] and a.id='$data[1]' and a.status_active=1 and a.is_deleted=0";
	$dataArray=sql_select($sql_mst);
	$job_qnty_arr=array();
	$qty_sql ="SELECT a.id, a.booked_qty from subcon_ord_breakdown a where a.booked_qty is not null and a.booked_qty!=0  and a.status_active=1";
	$qty_sql_res=sql_select($qty_sql);
	foreach ($qty_sql_res as $row)
	{
		$job_qnty_arr[$row[csf("id")]]['qnty']=$row[csf("booked_qty")];
	}
	unset($qty_sql_res);
	?>
    <style>
	p {
		word-break: break-all;
	}
    </style>
    
    <? 
	?>
	<div style="width:1060px;">
	<table width="1060" cellspacing="0" align="center" border="0">
			<tr>
				<td colspan="6"></td>
			</tr>
			<tr>
				<td width="230" rowspan="2">
					<img src="../../<? echo $image_location; ?>" height="60" width="200" style="float:left;">
				</td>
				<td colspan="4" align="left" style="font-size:x-large">
					<strong><? echo $company_library[$data[0]]; ?></strong>
				</td>
        		<td id="barcode_img_id"></td>
			</tr>
			<tr>
            	
				<td colspan="4" align="left">
					<?
					$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website, vat_number from lib_company where id=$data[0] and status_active=1 and is_deleted=0"); 
					foreach ($nameArray as $result)
					{ 
						 echo $result[csf('city')];
							//$party_id= $result[csf('party_id')];
					}
					?> 
				</td>
                <td align="center"><?  echo $dataArray[0][csf('trims_job')];  ?></td>
			</tr> 
            
		</table>
        <style>
        #job_no_tbl,#section_id_tbl{ margin-top:15px;}
		#details_id_tbl{ margin-top:15px; word-wrap:break-word; word-break:break-all}
        </style>
        
        <table border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="1060" rules="all" id="job_no_tbl">
        	<tr style="font-weight:bold;">
            	<td width="150">Job Card No:</td>
            	<td width="360"><? echo $dataArray[0][csf('trims_job')]; ?></td>
            	<td width="100" style="border-top:hidden; border-bottom:hidden; border-right:hidden;">&nbsp;</td>
            	<td width="100" style="border-top:hidden; border-bottom:hidden; border-right:hidden;">&nbsp;</td>
            	<td width="150" style="border-top:hidden; border-bottom:hidden; border-right:hidden;">&nbsp;</td>
                <td width="100" style="border-top:hidden; border-bottom:hidden;">&nbsp;</td>
            	<td width="150">Section:</td>
            	<td width="200"><? echo $trims_section[$dataArray[0][csf('section_id')]]; ?></td>
            </tr>
            <tr>
            	<td colspan="8" style="border-left:hidden; border-right:hidden;">&nbsp;</td>
            </tr>
            <tr>
            	<td>Customer Name:</td>
                <td><? echo $party_arr[$dataArray[0][csf('party_id')]]; ?></td>
				<td style="border-top:hidden; border-bottom:hidden;">&nbsp;</td>
                <td>WO No:</td>
                <td><? echo $dataArray[0][csf('order_no')]; ?></td>
                <td style="border-top:hidden; border-bottom:hidden;">&nbsp;</td>
                <td>Customer's Buyer:</td>
                <td id="td_cus_buyer"></td>
            </tr>
            <tr>
            	<td>Invoice Address:</td>
                <td><p><? echo $address_1[$dataArray[0][csf('party_id')]]; ?></p></td>
				<td style="border-top:hidden; border-bottom:hidden;">&nbsp;</td>
                <td>Order Rcv Date:</td>
                <td><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
                <td style="border-top:hidden; border-bottom:hidden;">&nbsp;</td>
                <td>Merchandiser:</td>
                <td><? echo $team_member_name[$dataArray[0][csf('team_member')]]; ?></td>
            </tr>
             <tr>
            	<td>Delivery Address::</td>
                <td><p><? echo $address_2[$dataArray[0][csf('party_id')]]; ?></p></td>
				<td style="border-top:hidden; border-bottom:hidden;">&nbsp;</td>
                <td>Target Delivery:</td>
                <td><? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
                <td style="border-top:hidden; border-bottom:hidden;">&nbsp;</td>
                <td>Marketing Lead:</td>
                <td><? echo $team_leader_name[$dataArray[0][csf('team_leader')]]; ?></td>
            </tr>
        </table>
   
   
    <table border="1" cellpadding="2" cellspacing="0" class="rpt_table"  width="300" rules="all" align="left" style="float:left;" id="job_no_tbl">
        <tr>
                <td><b>Job Card No:</b></td><td><b><?  echo $dataArray[0][csf('trims_job')];  ?></b></td>
        </tr>
    </table>
    
    <table cellspacing="0" align="center"  style="float:right;" border="1" class="rpt_table" id="section_id_tbl">
     <tr>
            <td width="115"><b>Section:</b></td><td width="105"><b><?  echo $trims_section[$dataArray[0][csf('section_id')]];  ?></b></td>
        </tr>
    </table>
   

 <table border="1" cellpadding="2" cellspacing="0" class="rpt_table"  width="300" rules="all"  style="float:left;" id="details_id_tbl">
        <tr>
            <td>Customer Name:</td><td><?  echo $party_arr[$dataArray[0][csf('party_id')]]; ?></td>
        </tr>
        <tr>
        	<td>Invoice Address:</td><td><? echo $address_1[$dataArray[0][csf('party_id')]];?></td>
        </tr> 
        <tr>
         <td>Delivery Address:</td><td><? echo $address_2[$dataArray[0][csf('party_id')]]; ?></td>
        </tr>
    </table>
    
    <table width="300" cellspacing="0" style="float:left;margin-left:150px;"  border="1" class="rpt_table">
         <tr>
            <td>WO No:</td><td><?  echo $dataArray[0][csf('order_no')]; ?></td>
        </tr>
        <tr>
        	<td>Order Rcv Date:</td><td><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
        </tr> 
        <tr>
         <td>Target Delivery:</td><td><?  echo change_date_format($dataArray[0][csf('delivery_date')]);  ?></td>
        </tr>
    </table>
    
    <table cellspacing="0"  style="float:right;" border="1" class="rpt_table">
        <tr>
            <td>Customer's Buyer:</td><td id="td_cus_buyer"></td>
        </tr> 
        <tr>
        	<td>Merchandiser:</td><td><? echo $team_member_name[$dataArray[0][csf('team_member')]];?></td>
        </tr> 
        <tr>
         <td>Marketing Lead:</td><td><? echo $team_leader_name[$dataArray[0][csf('team_leader')]]; ?></td>
        </tr>
    </table>
    
 <table align="center" cellspacing="0" width="1040"  rules="all" class="rpt_table" >
    <tr>
    <td>&nbsp; </td>
    </tr>
    </table>

		<br>
		<div style="width:100%;">
        
			<table align="left" cellspacing="0" width="1060"  border="1" rules="all" class="rpt_table"  >
				<thead bgcolor="#dddddd">
					<tr>
						<th colspan="10" style="text-align:center; font-size: 18px;">Order & Product Details</th>
					</tr>
					<tr>						
						<th width="30" style="font-size: 16px;">SL</th>
						<th width="175" style="font-size: 16px;">Buyer Order</th>
						<th width="175" style="font-size: 16px;">Buyer Style</th>
						<th width="200" style="font-size: 16px;">Item Description</th>
						<th width="80" style="font-size: 16px;">UOM</th> 
                        <th width="70" style="font-size: 16px;">Color</th>
                        <th width="70" style="font-size: 16px;">Size</th>
						<th width="150" colspan="2" style="font-size: 16px;">Item Qty</th> 
						<th>Remarks</th> 
					</tr>
				</thead>         
				<?	 
			$l=1;
			$mst_id=$data[1];
			$trims_job=$dataArray[0][csf('trims_job')]; //die;
			$sqlDtls_result =sql_select("select  id, mst_id, job_no_mst, receive_dtls_id, booking_dtls_id, book_con_dtls_id, break_id as subcon_break_ids, buyer_po_id,buyer_po_no, buyer_style_ref, item_description, color_id, size_id, uom, job_quantity,  impression, material_color from trims_job_card_dtls where mst_id=$data[1]");

			$sqlBreak_result =sql_select("select  a.id, a.mst_id, a.order_id, a.job_no_mst, a.book_con_dtls_id, a.product_id, a.description, a.specification, a.pcs_unit, a.cons_qty, a.process_loss, a.process_loss_qty, a.req_qty, a.remarks,b.unit_of_measure as unit from trims_job_card_breakdown a , product_details_master b where a.product_id=b.id and a.job_no_mst='$trims_job' and b.entry_form=334");
			$break_arr=array(); $break_arr_summery=array();
			foreach($sqlBreak_result as $row)
			{
				$break_arr[$row[csf('mst_id')]]['info'].=$row[csf('description')]."_".$row[csf('pcs_unit')]."_".$row[csf('unit')]."_".$row[csf('req_qty')]."_".$row[csf('process_loss')]."_".$row[csf('remarks')]."_".$row[csf('product_id')]."**";
				$break_arr_summery[$row[csf('description')]][$row[csf('unit')]][$row[csf('product_id')]]+=$row[csf('req_qty')];
			}
			$received_id=$dataArray[0][csf('received_id')];
			//echo $received_id; die;
			$sqlOrdDtls_result =sql_select("select  id, order_quantity, buyer_buyer from subcon_ord_dtls where mst_id=$received_id");
			$ordDtls_arr=array();
			foreach($sqlOrdDtls_result as $row)
			{
				$ordDtls_arr[$row[csf('id')]]['order_quantity']=$row[csf('order_quantity')];
				$ordDtls_arr[$row[csf('id')]]['buyer_buyer']=$row[csf('buyer_buyer')];
			}
			//print_r($ordDtls_arr); die;
			$total_order_quantity=0; $receiveDtlsId_arr=array(); $buyer_buyer=$break_data='';  $req_quantity_arr=array(); 
			foreach($sqlDtls_result as $row)
			{
				//echo $total_req_quantity."++";
				if ($l%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				//$jobQnty
				$material_colors=explode('__',$row[csf('material_color')]); $metColor=''; $jobQnty='';
				for($j=0; $j<count($material_colors); $j++)
				{
					if($material_colors[$j]!='')
					{
						$metColor.=$color_arr[$material_colors[$j]].",";
					}
				}
				$subcon_break_ids=explode(",",chop($row[csf("subcon_break_ids")],','));
				$job_Qnty=0;
				for($j=0; $j<count($subcon_break_ids); $j++)
				{
					$job_Qnty +=$job_qnty_arr[$subcon_break_ids[$j]]['qnty'];
				}
				//$jobQnty=round($jobQnty);
				$jobQnty=number_format($job_Qnty,4);
				$break_data=chop($break_arr[$row[csf('id')]]['info'],"**");
				$break_info=explode('**',$break_data);
				$metDesc=''; $metPcsUnit=''; $metUnit=''; $metReqQty=''; $metProcLoss=''; $metRemark='';
				
				$receive_dtls_ids=array_unique(explode(",",$row[csf("receive_dtls_id")]));
				$order_quantity=''; 
				for($j=0; $j<count($receive_dtls_ids); $j++)
				{
					if (!in_array($receive_dtls_ids[$j], $receiveDtlsId_arr))
					{
						$order_quantity+=$ordDtls_arr[$receive_dtls_ids[$j]]['order_quantity'];
						if($data[2]==1)
						{
							$buyer_buyer.=$buyer_library_arr[$ordDtls_arr[$receive_dtls_ids[$j]]['buyer_buyer']].',';
						}
						else
						{
							$buyer_buyer.=$ordDtls_arr[$receive_dtls_ids[$j]]['buyer_buyer'].',';
						}
						$receiveDtlsId_arr[]=$receive_dtls_ids[$j];
					}
				}
				$total_order_quantity+=$order_quantity;
				$buyer_buyers=chop(implode(',',array_unique(explode(",",$buyer_buyer))),',');
				?>
				<script type="text/javascript">
					var total_order_quantity='<? echo ": ".$total_order_quantity; ?>';
					var buyer_buyerss='<? echo ": ".$buyer_buyers; ?>';
					document.getElementById('td_cus_buyer').innerHTML=buyer_buyerss;
				</script>
				
				<tr bgcolor="<? echo $bgcolor; ?>"> 
					<td><? echo $l; ?></td>
					<td><p><? echo implode(',',array_unique(explode(',',$row[csf('buyer_po_no')]))); ?></p></td>
					<td><p><? echo implode(',',array_unique(explode(',',$row[csf('buyer_style_ref')]))); ?></p></td>
					<td><p><? echo $row[csf('item_description')]; ?></p></td>
					<td><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
                    <td><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                    <td><p><? echo $size_name_arr[$row[csf('size_id')]];; ?></p></td>
					<td align="right" colspan="2"><p><? echo $jobQnty;?> </p></td>
					<td><p><? echo $row[csf('impression')]; ?></p></td>
				</tr>
				
				<?
				//echo "<pre>";
				//print_r($break_info);
				$pcs_unit='';  $req_quantity='';
				for($j=0; $j<count($break_info); $j++)
				{
					if ($j%2==0) $dtlbgcolor="#f0fbfc"; else $dtlbgcolor="#fcf5f8";
					$break_infos=explode('_',$break_info[$j]);
					$pcs_unit=$job_Qnty/$break_infos[3];
					$req_quantity=$job_Qnty*$break_infos[3];
					$req_quantity_arr[$break_infos[6]] +=$req_quantity;
				}
				$l++;
			}
			//echo "<pre>";
			//print_r($req_quantity_arr);
			?>
		</table>
        
		<div style="width: 400px ;  margin-top:5px; float: left;"  align="left">
			<? 
			   	echo get_spacial_instruction($data[1],"100%",257);
			?>
		</div>
        
		<div style="width: 470px; float: right; margin-top:5px;">
			<table align="right" cellspacing="0" width="460"  border="1" rules="all" class="rpt_table">
				<thead bgcolor="#dddddd">
					<tr>
						<th colspan="5" style="text-align:center; font-size: 17px;">Summary</th>
					</tr>
					<tr>
						<th width="30">SL</th>
						<th width="220">Item</th>
						<th width="120">Brand Name</th>
						<th width="60">UOM</th>
						<th width="100">Total Req.Qty</th>
					</tr>
				</thead>
				<tbody>
				<?
					$k=1;  
					foreach($break_arr_summery as $item=>$break_arr_val)
					{
						foreach($break_arr_val as $uom=>$brand_val)
						{
							foreach($brand_val as $brand=>$qnty)
							{
								//echo "<pre>";
								//print_r($brand);$dtlbgcolor="#f0fbfc"; else $dtlbgcolor="#fcf5f8";
								if($k%2==0) $sumBgcolor="#f0fbfc"; else $sumBgcolor="#fcf5f8";
								?>
								<tr bgcolor="<? echo $sumBgcolor; ?>"> 
									<td><? echo $k; ?></td>
									<td><p><? echo $item; ?></p></td>
									<td><p><? echo $brand_arr[$brand]; ?></p></td>
									<td><p><? echo $unit_of_measurement[$uom]; ?></p></td>
									<td><p><? echo $req_quantity_arr[$brand];//$qnty;?> </p></td>
								</tr>
								<?
								$k++;
							}
						}
					}
					?>
					</tbody>
				</table>
			</div>
            
		<br>
	</div>

	<?
    	echo signature_table(160, $data[0], "1100px");
    ?>
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
    function generateBarcode( valuess )
    {
        var value = valuess;//$("#barcodeValue").val();
        var btype = 'code39';//$("input[name=btype]:checked").val();
        var renderer ='bmp';// $("input[name=renderer]:checked").val();
        var settings = {
          output:renderer,
          bgColor: '#FFFFFF',
          color: '#000000',
          barWidth: 1,
          barHeight: 30,
          moduleSize:5,
          posX: 10,
          posY: 20,
          addQuietZone: 1
        };
       
         value = {code:value, rect: false};
        $("#barcode_img_id").show().barcode(value, btype, settings);
    } 
    generateBarcode("<? echo $dataArray[0][csf('trims_job')]; ?>");
    </script>
</div>
<?
}



if($action=="job_card_print23")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library_arr=return_library_array("select id, buyer_name from lib_buyer where status_active=1","id","buyer_name");
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$brand_arr=return_library_array( "select id, brand_name from product_details_master",'id','brand_name');
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	
	if($data[2]==1)
	{
		$party_arr=return_library_array( "select id,company_name from  lib_company where status_active=1 and is_deleted=0 order by company_name",'id','company_name');
	}
	else
	{
		$party_arr=return_library_array( "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name",'id','buyer_name');
	}
	$sql_mst="select a.id, a.trims_job, a.job_no_prefix, a.job_no_prefix_num,  a.company_id, a.location_id, a.within_group, a.party_id,  a.party_location , a.delivery_date, a.order_id, a.order_qty, a.received_no, a.received_id, a.section_id , b.receive_date, b.delivery_date, b.order_no from trims_job_card_mst a , subcon_ord_mst b where a.received_id=b.id and a.company_id=$data[0] and a.id='$data[1]' and a.status_active=1 and a.is_deleted=0";
	$dataArray=sql_select($sql_mst);
	$job_qnty_arr=array();
	$qty_sql ="SELECT a.id, a.booked_qty from subcon_ord_breakdown a where a.booked_qty is not null and a.booked_qty!=0  and a.status_active=1";
	$qty_sql_res=sql_select($qty_sql);
	foreach ($qty_sql_res as $row)
	{
		$job_qnty_arr[$row[csf("id")]]['qnty']=$row[csf("booked_qty")];
	}
	unset($qty_sql_res);
	//echo "<pre>";
	//print_r($party_arr);
	//die;
	
	?>
    <style>
	p {
		word-break: break-all;
	}
    </style>
    
    <? 
	?>
	<div style="width:1060px;">
   
		<table width="1060" cellspacing="0" align="center" border="0">
			<tr>
				<td colspan="6"></td>
			</tr>
			<tr>
				<td colspan="2" rowspan="2">
					<img src="../../<? echo $image_location; ?>" height="60" width="200" style="float:left;">
				</td>
				<td colspan="4" align="center" style="font-size:x-large">
					<strong><? echo $company_library[$data[0]]; ?></strong>
				</td>
        		<td id="barcode_img_id"></td>
			</tr>
			<tr>
				<td colspan="4" align="center">
					<?
					$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website, vat_number from lib_company where id=$data[0] and status_active=1 and is_deleted=0"); 
					foreach ($nameArray as $result)
					{ 
						?>
						<? echo $result[csf('plot_no')]; ?>,
						Level No: <? echo $result[csf('level_no')]?>,
						<? echo $result[csf('road_no')]; ?>, 
						<? echo $result[csf('block_no')];?>, 
						<? echo $result[csf('city')];?>, 
						<? echo $result[csf('zip_code')]; ?>, 
						<?php echo $result[csf('province')];?>, 
						<? echo $country_arr[$result[csf('country_id')]]; ?><br> 
						Email Address: <? echo $result[csf('email')];?> 
						Website No: <? echo $result[csf('website')];?> <br>
						<b> Vat No : <? echo $result[csf('vat_number')]; ?></b> <?
							//$party_id= $result[csf('party_id')];
					}
					?> 
				</td>
			</tr> 

			<tr>
				<td colspan="6" align="center" style="font-size:20px"><u>
					<strong style="margin-left:265px;"><? echo $data[3]; ?></strong></u>
					<strong style="margin-left:265px;">Section: <? echo $trims_section[$dataArray[0][csf('section_id')]]; ?></strong></u>
				</td>
			</tr>
			<tr>
				<td>Party </td><td colspan="5">: <strong> <? echo $party_arr[$dataArray[0][csf('party_id')]]; ?></strong></td>
				<td width="150">Buyer's Buyer</td>
                 <td width="175" id="td_cus_buyer"></td>
			</tr>
			<tr>
				<td width="170">Order Receive Date </td> <td width="175"><? echo ": ".change_date_format($dataArray[0][csf('receive_date')]); ?></td>
				<td width="130"></td> <td width="175"></td>
				<td width="130"></td> <td width="175"></td>
				<td width="150">Job Card No.</td><td width="300px">: <strong> <? echo $dataArray[0][csf('trims_job')]  ?></strong></td>
			</tr>
			<tr>
				<td>Target Delivery</td><td><? echo ": ".change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
				<td  colspan="4">&nbsp;</td> 
				<td>W/O No </td><td colspan="3"><? echo ": ".$dataArray[0][csf('order_no')]; ?></td>
			</tr>
			<tr>
				<td colspan="6">&nbsp;</td>
                <td>Order Qty </td><td colspan="3"><? echo ": ".$dataArray[0][csf('order_qty')]; ?></td>
			</tr>
		</table>
		<br>
		<div style="width:100%;">
			<table align="left" cellspacing="0" width="1060"  border="1" rules="all" class="rpt_table"  >
				<thead bgcolor="#dddddd">
					<tr>
						<th colspan="9" style="text-align:center; font-size: 18px;">Material Consumption</th>
					</tr>
					<tr>						
						<th width="30" style="font-size: 16px;">SL</th>
						<th width="175" style="font-size: 16px;">Buyer Order</th>
						<th width="175" style="font-size: 16px;">Buyer Style</th>
						<th width="200" style="font-size: 16px;">Item Description</th>
						<th width="80" style="font-size: 16px;">UOM</th> 
						<th width="150" colspan="2" style="font-size: 16px;">Item Qty</th> 
						<th width="70" style="font-size: 16px;">Impression</th>
						<th>Color</th> 
					</tr>
				</thead>         
				<?	 
			$l=1;
			$mst_id=$data[1];
			$trims_job=$dataArray[0][csf('trims_job')]; //die;
			$sqlDtls_result =sql_select("select  id, mst_id, job_no_mst, receive_dtls_id, booking_dtls_id, book_con_dtls_id, break_id as subcon_break_ids, buyer_po_id,buyer_po_no, buyer_style_ref, item_description, color_id, size_id, uom, job_quantity,  impression, material_color from trims_job_card_dtls where mst_id=$data[1]");

			$sqlBreak_result =sql_select("select  a.id, a.mst_id, a.order_id, a.job_no_mst, a.book_con_dtls_id, a.product_id, a.description, a.specification, a.pcs_unit, a.cons_qty, a.process_loss, a.process_loss_qty, a.req_qty, a.remarks,b.unit_of_measure as unit from trims_job_card_breakdown a , product_details_master b where a.product_id=b.id and a.job_no_mst='$trims_job' and b.entry_form=334");
			//echo "select  a.id, a.mst_id, a.order_id, a.job_no_mst, a.book_con_dtls_id, a.product_id, a.description, a.specification, a.unit, a.pcs_unit, a.cons_qty, a.process_loss, a.process_loss_qty, a.req_qty, a.remarks,b.unit_of_measure from trims_job_card_breakdown a , product_details_master b where a.product_id=b.id and a.job_no_mst='$trims_job'";
			$break_arr=array(); $break_arr_summery=array();
			foreach($sqlBreak_result as $row)
			{
				$break_arr[$row[csf('mst_id')]]['info'].=$row[csf('description')]."_".$row[csf('pcs_unit')]."_".$row[csf('unit')]."_".$row[csf('req_qty')]."_".$row[csf('process_loss')]."_".$row[csf('remarks')]."_".$row[csf('product_id')]."**";
				$break_arr_summery[$row[csf('description')]][$row[csf('unit')]][$row[csf('product_id')]]+=$row[csf('req_qty')];
			}
			$received_id=$dataArray[0][csf('received_id')];
			//echo $received_id; die;
			$sqlOrdDtls_result =sql_select("select  id, order_quantity, buyer_buyer from subcon_ord_dtls where mst_id=$received_id");
			$ordDtls_arr=array();
			foreach($sqlOrdDtls_result as $row)
			{
				$ordDtls_arr[$row[csf('id')]]['order_quantity']=$row[csf('order_quantity')];
				$ordDtls_arr[$row[csf('id')]]['buyer_buyer']=$row[csf('buyer_buyer')];
			}
			//print_r($ordDtls_arr); die;
			$total_order_quantity=0; $receiveDtlsId_arr=array(); $buyer_buyer=$break_data='';  $req_quantity_arr=array(); 
			foreach($sqlDtls_result as $row)
			{
				//echo $total_req_quantity."++";
				if ($l%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				//$jobQnty
				$material_colors=explode('__',$row[csf('material_color')]); $metColor=''; $jobQnty='';
				for($j=0; $j<count($material_colors); $j++)
				{
					if($material_colors[$j]!='')
					{
						$metColor.=$color_arr[$material_colors[$j]].",";
					}
				}
				$subcon_break_ids=explode(",",chop($row[csf("subcon_break_ids")],','));
				$job_Qnty=0;
				for($j=0; $j<count($subcon_break_ids); $j++)
				{
					$job_Qnty +=$job_qnty_arr[$subcon_break_ids[$j]]['qnty'];
				}
				//$jobQnty=round($jobQnty);
				$jobQnty=number_format($job_Qnty,4);
				$break_data=chop($break_arr[$row[csf('id')]]['info'],"**");
				$break_info=explode('**',$break_data);
				$metDesc=''; $metPcsUnit=''; $metUnit=''; $metReqQty=''; $metProcLoss=''; $metRemark='';
				
				$receive_dtls_ids=array_unique(explode(",",$row[csf("receive_dtls_id")]));
				$order_quantity=''; 
				for($j=0; $j<count($receive_dtls_ids); $j++)
				{
					if (!in_array($receive_dtls_ids[$j], $receiveDtlsId_arr))
					{
						$order_quantity+=$ordDtls_arr[$receive_dtls_ids[$j]]['order_quantity'];
						if($data[2]==1)
						{
							$buyer_buyer.=$buyer_library_arr[$ordDtls_arr[$receive_dtls_ids[$j]]['buyer_buyer']].',';
						}
						else
						{
							$buyer_buyer.=$ordDtls_arr[$receive_dtls_ids[$j]]['buyer_buyer'].',';
						}
						$receiveDtlsId_arr[]=$receive_dtls_ids[$j];
					}
				}
				$total_order_quantity+=$order_quantity;
				$buyer_buyers=chop(implode(',',array_unique(explode(",",$buyer_buyer))),',');
				?>
				<script type="text/javascript">
					var total_order_quantity='<? echo ": ".$total_order_quantity; ?>';
					var buyer_buyerss='<? echo ": ".$buyer_buyers; ?>';
					//document.getElementById('td_order_qty').innerHTML=total_order_quantity;
					document.getElementById('td_cus_buyer').innerHTML=buyer_buyerss;
				</script>
				
				<tr bgcolor="<? echo $bgcolor; ?>"> 
					<td><? echo $l; ?></td>
					<td><p><? echo implode(',',array_unique(explode(',',$row[csf('buyer_po_no')]))); ?></p></td>
					<td><p><? echo implode(',',array_unique(explode(',',$row[csf('buyer_style_ref')]))); ?></p></td>
					<td><p><? echo $row[csf('item_description')].",".$color_arr[$row[csf('color_id')]]; ?></p></td>
					<td><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
					<td align="right" colspan="2"><p><? echo $jobQnty;?> </p></td>
					<td><p><? echo $row[csf('impression')]; ?></p></td>
					<td><p><? echo chop($metColor,","); ?></p></td>
				</tr>
				<tr bgcolor="<? echo "#d6eaf8"; ?>">
					<td></td>
					<td style="text-align:center; font-size: 14px;"><strong>Raw Materials</strong></td>
					<td style="text-align:center; font-size: 14px;"><strong>Brand Name</strong></td>
					<td style="text-align:center; font-size: 14px; display: none;"><? echo $unit_of_measurement[$row[csf('uom')]]; ?> / Unit </td>
					<td style="text-align:center; font-size: 14px;"><strong>UOM</strong></td>
					<td style="text-align:center; font-size: 14px;"><strong>Total Cons/Unit</strong></td>
                    <td colspan="2" style="text-align:center; font-size: 14px;"><strong>Total Req.Qty</strong></td>
					<td style="text-align:center; font-size: 14px;"><strong>Pro Loss%</strong></td>
					<td style="text-align:center; font-size: 14px;"><strong>Remarks</strong></td>
				</tr>
				<?
				//echo "<pre>";
				//print_r($break_info);
				$pcs_unit='';  $req_quantity='';

				for($j=0; $j<count($break_info); $j++)
				{
					if ($j%2==0) $dtlbgcolor="#f0fbfc"; else $dtlbgcolor="#fcf5f8";
					$break_infos=explode('_',$break_info[$j]);
					$pcs_unit=$job_Qnty/$break_infos[3];
					//echo $job_Qnty.'=='.$break_infos[3].'++';
					$req_quantity=$job_Qnty*$break_infos[3];
					//echo $job_Qnty.'=='.$break_infos[3].'=='.$req_quantity.'++';
					$req_quantity_arr[$break_infos[6]] +=$req_quantity;
					//$total_req_quantity+=$req_quantity;
					
					?>
					<tr bgcolor="<? echo $dtlbgcolor; ?>">
						<td></td>
						<td><p><?  echo $break_infos[0] ; ?></p></td>
						<td><p><?  echo $brand_arr[$break_infos[6]] ; ?></p></td>
						<td style="display:none;"><p><?  echo number_format($pcs_unit,2);//$break_infos[1] ; ?></p></td>
						<td><p><?  echo $unit_of_measurement[$break_infos[2]] ; ?></p></td>
						<td align="right"><p><?  echo $break_infos[3] ; ?></p></td>
                        <td colspan="2" align="right" ><p><?  echo $req_quantity; ?></p></td>
						<td align="right"><p><?  echo $break_infos[4] ; ?></p></td>
						<td><p><?  echo $break_infos[5] ; ?></p></td>
					</tr>
					<?
					//echo $total_req_quantity."==".$req_quantity."</br>";
				}
				$l++;
			}
			//echo "<pre>";
			//print_r($req_quantity_arr);
			?>
		</table>
		<div style="width: 400px ;  margin-top:5px; float: left;"  align="left">
			<? 
			   	echo get_spacial_instruction($data[1],"100%",257);
			?>
		</div>
		<div style="width: 470px; float: right; margin-top:5px;">
			<table align="right" cellspacing="0" width="460"  border="1" rules="all" class="rpt_table">
				<thead bgcolor="#dddddd">
					<tr>
						<th colspan="5" style="text-align:center; font-size: 17px;">Summary</th>
					</tr>
					<tr>
						<th width="30">SL</th>
						<th width="220">Item</th>
						<th width="120">Brand Name</th>
						<th width="60">UOM</th>
						<th width="100">Total Req.Qty</th>
					</tr>
				</thead>
				<tbody>
				<?
					$k=1;  
					foreach($break_arr_summery as $item=>$break_arr_val)
					{
						foreach($break_arr_val as $uom=>$brand_val)
						{
							foreach($brand_val as $brand=>$qnty)
							{
								//echo "<pre>";
								//print_r($brand);$dtlbgcolor="#f0fbfc"; else $dtlbgcolor="#fcf5f8";
								if($k%2==0) $sumBgcolor="#f0fbfc"; else $sumBgcolor="#fcf5f8";
								?>
								<tr bgcolor="<? echo $sumBgcolor; ?>"> 
									<td><? echo $k; ?></td>
									<td><p><? echo $item; ?></p></td>
									<td><p><? echo $brand_arr[$brand]; ?></p></td>
									<td><p><? echo $unit_of_measurement[$uom]; ?></p></td>
									<td><p><? echo $req_quantity_arr[$brand];//$qnty;?> </p></td>
								</tr>
								<?
								$k++;
							}
						}
					}
					?>
					</tbody>
				</table>
			</div>
		<br>
	</div>
	<?
    	echo signature_table(160, $data[0], "1100px");
    ?>
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
    function generateBarcode( valuess )
    {
        var value = valuess;//$("#barcodeValue").val();
        var btype = 'code39';//$("input[name=btype]:checked").val();
        var renderer ='bmp';// $("input[name=renderer]:checked").val();
        var settings = {
          output:renderer,
          bgColor: '#FFFFFF',
          color: '#000000',
          barWidth: 1,
          barHeight: 30,
          moduleSize:5,
          posX: 10,
          posY: 20,
          addQuietZone: 1
        };
       
         value = {code:value, rect: false};
        $("#barcode_img_id").show().barcode(value, btype, settings);
    } 
    generateBarcode("<? echo $dataArray[0][csf('trims_job')]; ?>");
    </script>
</div>
<?
}


