<?
die;
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
	
	echo create_drop_down( $dropdown_name, 150, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );	
	exit();
}

if($action=="load_drop_down_wash_type")
{
	$data=explode('_',$data);
	
	if($data[0]==1) $wash_type=$wash_wet_process;
	else if($data[0]==2) $wash_type=$wash_dry_process;
	else if($data[0]==3) $wash_type=$wash_laser_desing;
	else $wash_type=$blank_array;

	echo create_drop_down( "cboProcessType_".$data[1], 90, $wash_type,"", 1, "-- Select --",$data[2],"", 0,"" );
	exit();
}

if ($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);

	if($data[1]==1) $load_function="fnc_load_party(2,document.getElementById('cbo_within_group').value)";
	else $load_function="";
	//$company_cond 
	if($data[1]==1)
	{
		//echo  "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name";
		echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name","id,company_name", 1, "-- Select Company --", $data[2], "$load_function");
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
	$user_id=$_SESSION['logic_erp']['user_id'];
	$color_library_arr=return_library_array( "select id,color_name from lib_color  where status_active=1 and is_deleted=0", "id", "color_name");
	$size_arr=return_library_array( "select id, size_name from  lib_size where status_active=1 and is_deleted=0",'id','size_name');
	if ($operation==0) // Insert Start Here=================================================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if($db_type==0) $insert_date_con="and YEAR(insert_date)=".date('Y',time())."";
		else if($db_type==2) $insert_date_con="and TO_CHAR(insert_date,'YYYY')=".date('Y',time())."";
		
		$new_job_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'WOE', date("Y",time()), 5, "select job_no_prefix, job_no_prefix_num from subcon_ord_mst where entry_form=640 and company_id=$cbo_company_name $insert_date_con order by id desc ", "job_no_prefix", "job_no_prefix_num" ));

		$id=return_next_id("id","subcon_ord_mst",1);
		$id1=return_next_id( "id", "subcon_ord_dtls",1);
		$id3=return_next_id( "id", "subcon_ord_breakdown", 1) ;
		$rID3=true;
		$field_array="id, entry_form, subcon_job, job_no_prefix, job_no_prefix_num, company_id, location_id, within_group, party_id, party_location, currency_id, exchange_rate, receive_date, delivery_date,gmts_type, rec_start_date, rec_end_date, order_id, order_no, conv_factor, inserted_by, insert_date, status_active, is_deleted";
		//txt_job_no*cbo_company_name*cbo_location_name*cbo_within_group*cbo_party_name*cbo_party_location*cbo_currency*txt_order_receive_date*txt_delivery_date*txt_order_no*hid_order_id*update_id
		$data_array="(".$id.",640,'".$new_job_no[0]."','".$new_job_no[1]."','".$new_job_no[2]."',".$cbo_company_name.",".$cbo_location_name.",".$cbo_within_group.",".$cbo_party_name.",".$cbo_party_location.",".$cbo_currency.",".$txt_ex_rate.",".$txt_order_receive_date.",".$txt_delivery_date.",".$cbo_gmts_type.",".$txt_rec_start_date.",".$txt_rec_end_date.",".$hid_order_id.",".$txt_order_no.",".$txt_converstion_factor.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		
		$txt_job_no=$new_job_no[0];
		//buyer_po_no buyer_style_ref
		$field_array2="id, mst_id, job_no_mst, order_id, order_no, buyer_po_id , buyer_po_no, buyer_style_ref , booking_dtls_id, gmts_item_id, gmts_color_id, gmts_size_id, order_quantity, order_uom, rate, amount, amount_domestic, smv, delivery_date, wastage, remarks, party_buyer_name, inserted_by, insert_date, status_active, is_deleted";
		
		$field_array3="id, mst_id, order_id, job_no_mst, description, process, embellishment_type, rate,prod_sequence_no";

		$data_array2 	= $data_array3="";  $add_commaa=0; $add_commadtls=0;

		for($i=1; $i<=$total_row; $i++)
		{			
			$txtbuyerPoId			= "txtbuyerPoId_".$i; 
			$txtbuyerPo				= "txtbuyerPo_".$i; 
			$txtstyleRef			= "txtstyleRef_".$i; 
			$cboGmtsItem			= "cboGmtsItem_".$i;
			$txtColor				= "txtColor_".$i;
			$txtColorId				= "txtColorId_".$i;
			$txtSize				= "txtSize_".$i;
			$txtSizeId				= "txtSizeId_".$i;
			$txtOrderQuantity		= "txtOrderQuantity_".$i;
			$cboUom 				= "cboUom_".$i;
			$txtRate 				= "txtRate_".$i;
			$txtAmount 				= "txtAmount_".$i;	
			$txtDomAmount 			= "txtDomAmount_".$i;
					
			$txtSmv 				= "txtSmv_".$i;
			$txtOrderDeliveryDate 	= "txtOrderDeliveryDate_".$i;
			$txtWastage 			= "txtWastage_".$i;
			$txtremarks 			= "txtremarks_".$i;
			
			$hdnDtlsdata 			= "hdnDtlsdata_".$i;
			$hdnDtlsUpdateId 		= "hdnDtlsUpdateId_".$i;
			$hdnbookingDtlsId 		= "hdnbookingDtlsId_".$i;
			$txtpartybuyername 		= "txtpartybuyername_".$i;

			if($db_type==0)
			{
				$orderDeliveryDate=change_date_format(str_replace("'",'',$$txtOrderDeliveryDate),'yyyy-mm-dd');
			}
			else
			{
				$orderDeliveryDate=change_date_format(str_replace("'",'',$$txtOrderDeliveryDate), "", "",1);
			}
			
			if(str_replace("'",'',$$txtbuyerPoId)=="") $txtbuyerPoId=0; else $txtbuyerPoId=str_replace("'",'',$$txtbuyerPoId);

			if(str_replace("'","",$cbo_within_group)==2)
			{ 
				if(str_replace("'","",$$txtColor)!="")
				{ 
					if (!in_array(str_replace("'","",$$txtColor),$new_array_color))
					{
						$color_id = return_id( str_replace("'","",$$txtColor), $color_library_arr, "lib_color", "id,color_name","255");  
						$new_array_color[$color_id]=str_replace("'","",$$txtColor);
					}
					else $color_id =  array_search(str_replace("'","",$$txtColor), $new_array_color); 
				}
				else $color_id=0;
				
				if(str_replace("'","",$$txtSize)!="")
				{ 
					if (!in_array(str_replace("'","",$$txtSize),$new_array_size))
					{
						$size_id = return_id( str_replace("'","",$$txtSize), $size_arr, "lib_size", "id,size_name","255");  
						$new_array_size[$size_id]=str_replace("'","",$$txtSize);
					}
					else $size_id =  array_search(str_replace("'","",$$txtSize), $new_array_size); 
				}
				else $size_id=0;
			}
			else
			{
				$color_id=str_replace("'","",$$txtColorId);
				$size_id=str_replace("'","",$$txtSizeId);
			}

			if ($add_commaa!=0) $data_array2 .=","; $add_comma=0;
			$data_array2 .="(".$id1.",".$id.",'".$new_job_no[0]."',".$hid_order_id.",".$txt_order_no.",'".$txtbuyerPoId."',".$$txtbuyerPo.",".$$txtstyleRef.",".$$hdnbookingDtlsId.",".$$cboGmtsItem.",".$color_id.",".$size_id.",".str_replace(",",'',$$txtOrderQuantity).",".$$cboUom.",".$$txtRate.",".str_replace(",",'',$$txtAmount).",".str_replace(",",'',$$txtDomAmount).",".$$txtSmv.",'".$orderDeliveryDate."',".$$txtWastage.",".$$txtremarks.",".$$txtpartybuyername.",'".$user_id."','".$pc_date_time."',1,0)";
			
			$dtls_data=explode("__",str_replace("'",'',$$hdnDtlsdata));
			/*echo "10**".$total_row; 
			print_r($dtls_data);
			die;*/
			for($j=0; $j<count($dtls_data); $j++)
			{
				$exdata=explode("_",$dtls_data[$j]);
				
				$description="'".$exdata[0]."'";
				$process="'".$exdata[1]."'";
				$washtype="'".$exdata[2]."'";
				$rate="'".str_replace(",",'',$exdata[3])."'";
				$dtlsup_id="'".$exdata[4]."'";	
				$prodsequenceno="'".$exdata[5]."'";				
				
				if ($add_commadtls!=0) $data_array3 .=",";
				$data_array3.="(".$id3.",".$id1.",".$hid_order_id.",'".$new_job_no[0]."',".$description.",".$process.",".$washtype.",".$rate.",".$prodsequenceno.")";
				$id3=$id3+1; $add_commadtls++;
			}
			
			$id1=$id1+1; $add_commaa++;
			//echo "10**INSERT INTO subcon_ord_breakdown (".$field_array3.") VALUES ".$data_array3; die;			
		}
		//echo "10**".$data_array2; die;
		//echo "10**".$data_array3; die;
		//echo "10**INSERT INTO subcon_ord_breakdown (".$field_array3.") VALUES ".$data_array3; die;
		//echo "10**INSERT INTO subcon_ord_dtls (".$field_array2.") VALUES ".$data_array2; die;
		//echo "10**INSERT INTO subcon_ord_mst (".$field_array.") VALUES ".$data_array; die;
		$flag=1;
		$rID=sql_insert("subcon_ord_mst",$field_array,$data_array,1);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		$rID2=sql_insert("subcon_ord_dtls",$field_array2,$data_array2,1);
		if($rID2==1 && $flag==1) $flag=1; else $flag=0;
		if($data_array3!="")
		{
			$rID3=sql_insert("subcon_ord_breakdown",$field_array3,$data_array3,1);
			if($rID3==1 && $flag==1) $flag=1; else $flag=0;
		}
		//echo "10**".$rID."**".$rID2."**".$rID3; die;
		if(str_replace("'","",$cbo_within_group)==1)
		{
			if($flag==1)
			{
				$rIDBooking=execute_query( "update wo_booking_mst set lock_another_process=1 where booking_no =".$txt_order_no."",1);
				if($rIDBooking==1 && $flag==1) $flag=1; else $flag=0;
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
	else if ($operation==1)   // Update Here================================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		/*$rec_number=return_field_value( "sys_no", "sub_material_mst"," embl_job_no=$txt_job_no and status_active=1 and is_deleted=0 and trans_type=1");
		if($rec_number){
			echo "washRec**".str_replace("'","",$txt_job_no)."**".$rec_number;
			die;
		}
		*/
		$recipe_number=return_field_value( "recipe_no", "pro_recipe_entry_mst"," job_no=$txt_job_no and status_active=1 and is_deleted=0 and entry_form=220");
		if($recipe_number){
			echo "washRecipe**".str_replace("'","",$txt_job_no)."**".$recipe_number;
			die;
		}
		
		
		$field_array="location_id*within_group*party_id*party_location*currency_id*exchange_rate*receive_date*delivery_date*gmts_type*rec_start_date*rec_end_date*order_id*order_no*conv_factor*updated_by*update_date";		
		$data_array="".$cbo_location_name."*".$cbo_within_group."*".$cbo_party_name."*".$cbo_party_location."*".$cbo_currency."*".$txt_ex_rate."*".$txt_order_receive_date."*".$txt_delivery_date."*".$cbo_gmts_type."*".$txt_rec_start_date."*".$txt_rec_end_date."*".$hid_order_id."*".$txt_order_no."*".$txt_converstion_factor."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		//$field_array2="id, mst_id, job_no_mst, order_id, order_no, buyer_po_id, buyer_po_no, buyer_style_ref, booking_dtls_id, gmts_item_id, gmts_color_id, order_quantity, order_uom, rate, amount, amount_domestic, smv, delivery_date, wastage, remarks, inserted_by, insert_date, status_active, is_deleted";
		$field_array2="order_id*order_no*buyer_po_id*buyer_po_no*buyer_style_ref*booking_dtls_id*gmts_item_id*gmts_color_id*gmts_size_id*order_quantity*order_uom*rate*amount*amount_domestic*smv*delivery_date*wastage*remarks*party_buyer_name*updated_by*update_date";
		
		$field_array3="id, mst_id, order_id, job_no_mst, description, process, embellishment_type, rate,prod_sequence_no";
		
		$field_array4="order_id*description*process*embellishment_type*rate*prod_sequence_no";
		
		//$field_array5="id, mst_id, job_no_mst, order_id, order_no, buyer_po_id, booking_dtls_id, gmts_item_id, main_process_id, embl_type, body_part, order_quantity, order_uom, rate, amount, smv, delivery_date, wastage, buyer_po_no, buyer_style_ref, buyer_buyer, inserted_by, insert_date";
		
		$field_array5="id, mst_id, job_no_mst, order_id, order_no, buyer_po_id , buyer_po_no, buyer_style_ref , booking_dtls_id, gmts_item_id, gmts_color_id, gmts_size_id, order_quantity, order_uom, rate, amount, amount_domestic, smv, delivery_date, wastage, remarks, party_buyer_name, inserted_by, insert_date, status_active, is_deleted";
		$id1=return_next_id( "id", "subcon_ord_dtls",1) ;
		//$dtlsIdForBreak=$id1;
		$id3=return_next_id( "id", "subcon_ord_breakdown", 1) ;
		$add_comma=0;	$flag="";
		for($i=1; $i<=$total_row; $i++)
		{			
			$txtbuyerPoId			= "txtbuyerPoId_".$i; 
			$txtbuyerPo				= "txtbuyerPo_".$i; 
			$txtstyleRef			= "txtstyleRef_".$i; 
			$cboGmtsItem			= "cboGmtsItem_".$i;
			$txtColor				= "txtColor_".$i;
			$txtColorId				= "txtColorId_".$i;
			$txtSize				= "txtSize_".$i;
			$txtSizeId				= "txtSizeId_".$i;
			$txtOrderQuantity		= "txtOrderQuantity_".$i;
			$cboUom 				= "cboUom_".$i;
			$txtRate 				= "txtRate_".$i;
			$txtAmount 				= "txtAmount_".$i;	
			$txtDomAmount 			= "txtDomAmount_".$i;	
			$txtSmv 				= "txtSmv_".$i;
			$txtOrderDeliveryDate 	= "txtOrderDeliveryDate_".$i;
			$txtWastage 			= "txtWastage_".$i;
			$txtremarks 			= "txtremarks_".$i;
			$hdnDtlsdata 			= "hdnDtlsdata_".$i;
			$hdnDtlsUpdateId 		= "hdnDtlsUpdateId_".$i;
			$hdnbookingDtlsId 		= "hdnbookingDtlsId_".$i;
			$txtpartybuyername 		= "txtpartybuyername_".$i;
			$aa	=str_replace("'",'',$$hdnDtlsUpdateId);
			
			if($db_type==0)
			{
				$orderDeliveryDate=change_date_format(str_replace("'",'',$$txtOrderDeliveryDate),'yyyy-mm-dd');
			}
			else
			{
				$orderDeliveryDate=change_date_format(str_replace("'",'',$$txtOrderDeliveryDate), "", "",1);
			}
			if(str_replace("'",'',$$txtbuyerPoId)=="") $txtbuyerPoId=0; else $txtbuyerPoId=str_replace("'",'',$$txtbuyerPoId);

			if(str_replace("'","",$cbo_within_group)==2)
			{ 
				if(str_replace("'","",$$txtColor)!="")
				{ 
					if (!in_array(str_replace("'","",$$txtColor),$new_array_color))
					{
						$color_id = return_id( str_replace("'","",$$txtColor), $color_library_arr, "lib_color", "id,color_name","255");  
						//echo $$txtColorName.'='.$color_id.'<br>';
						$new_array_color[$color_id]=str_replace("'","",$$txtColor);
						
					}
					else $color_id =  array_search(str_replace("'","",$$txtColor), $new_array_color); 
				}
				else $color_id=0;
				
				if(str_replace("'","",$$txtSize)!="")
				{ 
					if (!in_array(str_replace("'","",$$txtSize),$new_array_size))
					{
						$size_id = return_id( str_replace("'","",$$txtSize), $size_arr, "lib_size", "id,size_name","255");  
						$new_array_size[$size_id]=str_replace("'","",$$txtSize);
					}
					else $size_id =  array_search(str_replace("'","",$$txtSize), $new_array_size); 
				}
				else $size_id=0;
			}
			else
			{
				$color_id=str_replace("'","",$$txtColorId);
				$size_id=str_replace("'","",$$txtSizeId);
			}
			//echo "10**".$color_id; 
			if(str_replace("'",'',$$hdnDtlsUpdateId)!="")
			{
			
			$data_array2[$aa]=explode("*",("".$hid_order_id."*".$txt_order_no."*'".$txtbuyerPoId."'*".$$txtbuyerPo."*".$$txtstyleRef."*".$$hdnbookingDtlsId."*".$$cboGmtsItem."*".$color_id."*".$size_id."*".str_replace(",",'',$$txtOrderQuantity)."*".$$cboUom."*".$$txtRate."*".str_replace(",",'',$$txtAmount)."*".str_replace(",",'',$$txtDomAmount)."*".$$txtSmv."*'".$orderDeliveryDate."'*".$$txtWastage."*".$$txtremarks."*".$$txtpartybuyername."*".$user_id."*'".$pc_date_time."'"));
			$hdn_dtls_id_arr[]=str_replace("'",'',$$hdnDtlsUpdateId);
			}
			else
			{
				$hdnDtlsUpId=$id1;
				if ($add_commaa!=0) $data_array5 .=","; $add_comma=0;
				$data_array5 .="(".$id1.",".$update_id.",".$txt_job_no.",".$hid_order_id.",".$txt_order_no.",'".$txtbuyerPoId."',".$$txtbuyerPo.",".$$txtstyleRef.",".$$hdnbookingDtlsId.",".$$cboGmtsItem.",".$color_id.",".$size_id.",".str_replace(",",'',$$txtOrderQuantity).",".$$cboUom.",".$$txtRate.",".str_replace(",",'',$$txtAmount).",".str_replace(",",'',$$txtDomAmount).",".$$txtSmv.",'".$orderDeliveryDate."',".$$txtWastage.",".$$txtremarks.",".$$txtpartybuyername.",'".$user_id."','".$pc_date_time."',1,0)";
				$id1++;
			}
			
			$dtls_data=explode("__",str_replace("'",'',$$hdnDtlsdata));
			//echo "10**";//.$total_row; die;
			/*print_r($dtls_data);
			*/
			for($j=0; $j<count($dtls_data); $j++)
			{
				$exdata=explode("_",$dtls_data[$j]);
				
				$description="'".$exdata[0]."'";
				$process="'".$exdata[1]."'";
				$washtype="'".$exdata[2]."'";
				$rate="'".str_replace(",",'',$exdata[3])."'";
				$dtlsup_id="'".$exdata[4]."'";	
				$bb=$exdata[4];
				$prodsequenceno="'".$exdata[5]."'";	
				
				//echo $dtlsup_id; 
				if($bb==0)
				{
					
					if(str_replace("'",'',$$hdnDtlsUpdateId)!='') $dtlsIdForBreak=str_replace("'",'',$$hdnDtlsUpdateId); else $dtlsIdForBreak=$hdnDtlsUpId;
					if ($add_commadtls!=0) $data_array3 .=",";
					$data_array3.="(".$id3.",".$dtlsIdForBreak.",".$hid_order_id.",".$txt_job_no.",".$description.",".$process.",".$washtype.",".$rate.",".$prodsequenceno.")";
					$id3=$id3+1; 
					$add_commadtls++;
				}
				else if($bb!=0)
				{
					$data_array4[$bb]=explode("*",("".$hid_order_id."*".$description."*".$process."*".$washtype."*".$rate."*".$prodsequenceno.""));
					$hdn_break_id_arr[]		=$bb;
				}
			}
			//die;			
			//echo "10**".change_date_format(str_replace("'",'',$$txtOrderDeliveryDate),'yyyy-mm-dd')."nazim"; die;					
		}
		
		//echo "10**INSERT INTO subcon_ord_dtls (".$field_array5.") VALUES ".$data_array5; die;	
		$flag=1;	
		//echo "10**";
		//print_r($data_array3); echo "55";die;
		//echo "10**".bulk_update_sql_statement( "subcon_ord_dtls", "id",$field_array2,$data_array2,$hdn_dtls_id_arr); die;
		$rID=sql_update("subcon_ord_mst",$field_array,$data_array,"id",$update_id,0);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;

		if($data_array2!="")
		{
			$rID2=execute_query(bulk_update_sql_statement( "subcon_ord_dtls", "id",$field_array2,$data_array2,$hdn_dtls_id_arr),1);
			if($rID2==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		
		
		if($data_array5!="")
		{
			//echo "10**INSERT INTO subcon_ord_dtls (".$field_array5.") VALUES ".$data_array5; die;	
			$rID8=sql_insert("subcon_ord_dtls",$field_array5,$data_array5,1);
			if($rID8==1 && $flag==1) $flag=1; else $flag=0;
		}
		$txt_deleted_id_dtls=str_replace("'","",$txt_deleted_id_dtls);
		if($txt_deleted_id_dtls!="" && $flag==1)
		{
			$field_array_status="updated_by*update_date*status_active*is_deleted";
			$data_array_status=$user_id."*'".$pc_date_time."'*0*1";

			$rID9=sql_multirow_update("subcon_ord_dtls",$field_array_status,$data_array_status,"id",$txt_deleted_id_dtls,0);
			if($flag==1)
			{
				if($rID9) $flag=1; else $flag=0; 
			} 

			$rID7=execute_query( "delete from subcon_ord_breakdown where mst_id in ( $txt_deleted_id_dtls)",0);
			if($flag==1)
			{
				if($rID7) $flag=1; else $flag=0; 
			} 
		}
		
		
		//////////////////////////////
		
		$id_break=implode(',',$hiddenTblIdBreak);
		//print_r ($hiddenTblIdBreak);die;
		if($data_array4!="")
		{
			$rID3=execute_query(bulk_update_sql_statement( "subcon_ord_breakdown", "id",$field_array4,$data_array4,$hdn_break_id_arr),1);
			if($rID3==1 && $flag==1) $flag=1; else $flag=0;
		}

		if($data_array3!="")
		{
			//echo "10**INSERT INTO subcon_ord_breakdown (".$field_array3.") VALUES ".$data_array3; die;
			$rID4=sql_insert("subcon_ord_breakdown",$field_array3,$data_array3,1);
			if($rID4==1 && $flag==1) $flag=1; else $flag=0;		
		}
		
		
		
		
		//echo "10**".$delete_ids; die;
		//$deleted_id=str_replace("'",'',$txtDeletedId);
		$delete_ids=explode(",",$delete_ids);
		$all_del_ids="";
		foreach ($delete_ids as $value) 
		{
			if($value)
			{
				if($all_del_ids=="") $all_del_ids.=$value; else $all_del_ids.=','.$value;
			}
		}
		//echo "10**".$all_del_ids; die;
		//$delete_id=chop($delete_ids,",");
		if ($all_del_ids!="")
		{
			$rID5=execute_query( "delete from subcon_ord_breakdown where id in ( $all_del_ids)",0);
			if($rID5==1 && $flag==1) $flag=1; else $flag=0;
		}
		//1=1=1=0==1==
		//echo "10**".$rID.'='.$rID2.'='.$rID3.'='.$rID4.'='.$rID5.'='.$rID8.'='.$rID9.'='.$rID7; die;
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
			echo "washRec**".str_replace("'","",$txt_job_no)."**".$rec_number;
			die;
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
			load_drop_down( 'wash_order_entry_controller', company+'_'+within_group, 'load_drop_down_buyer', 'buyer_td' );
		}
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0) $('#search_by_td').html('Wash Job No');
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
        <table width="940" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead> 
                <tr>
                    <th colspan="8"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </tr>
                <tr>               	 
                    <th width="140" class="must_entry_caption">Company Name</th>
                    <th width="100">Within Group</th>                           
                    <th width="140">Party Name</th>
                    <th width="100">Search By</th>
                    <th width="100" id="search_by_td">Wash Job No</th>
                    <th width="100">Year</th>
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
                        <?php echo create_drop_down( "cbo_within_group", 100, $yes_no,"", 1, "-- Select --", '', "fnc_load_party_popup(1,this.value);" ); ?>
                    </td>
                    <td id="buyer_td">
                        <?
                        //select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name 
                        echo create_drop_down( "cbo_party_name", 150,$blank_array,"", 1, "-- Select Party --",'', "" );   	 
                        ?>
                    </td>
                    <td>
						<?
                            $search_by_arr=array(1=>"Wash Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style");
                            echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                        ?>
                    </td>
                    <td align="center">
                        <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                    </td>
                    <td align="center"><? echo create_drop_down( "cbo_year_selection", 100, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                    </td>
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year_selection').value, 'create_job_search_list_view', 'search_div', 'wash_order_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
                    </tr>
                    <tr>
                        <td colspan="8" align="center" valign="middle">
                            <? echo load_month_buttons();  ?>
                            <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="8" align="center" valign="top" id=""><div id="search_div"></div></td>
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
	if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[8]";   }
	if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$data[8]";}

	if($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
			else if ($search_by==3) $search_com_cond=" and a.job_no_prefix_num = '$search_str' ";
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no = '$search_str' ";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref = '$search_str' ";
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		
		if($search_str!="")
		{
			//echo $search_type; die;
			
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str%'"; 
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'"; 
			else if ($search_by==3) $search_com_cond=" and a.job_no_prefix_num like '%$search_str%'";
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '%$search_str%'"; 
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '%$search_str%'";   
		}
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
			
			else if ($search_by==3) $search_com_cond=" and a.job_no_prefix_num like '$search_str%'";  
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
			
			else if ($search_by==3) $search_com_cond=" and a.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '%$search_str'";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '%$search_str'";  
		}
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
		$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	}
	else
	{
		$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	}
	
	/*$po_ids='';
	
	if($db_type==0) $id_cond="group_concat(b.id)";
	else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	//echo "select $id_cond as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
	if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
	{
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
	}*/
	//echo $po_ids;
	//if ($po_ids!="") $po_idsCond=" and b.buyer_po_id in ($po_ids)"; else $po_idsCond="";
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$buyer_po_arr=array();
	if($within_group==1)
	{
		$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
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
		$color_id_str="group_concat(b.gmts_color_id)";
		$buyer_po_id_str="group_concat(b.buyer_po_id)";
		$delivery_date_str="group_concat(b.delivery_date)";
	}
	else if($db_type==2)
	{
		$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
		$color_id_str="listagg(b.gmts_color_id,',') within group (order by b.gmts_color_id)";
		$buyer_po_id_str="listagg(b.buyer_po_id,',') within group (order by b.buyer_po_id)";
		$delivery_date_str="listagg(b.delivery_date,',') within group (order by b.delivery_date)";
	}
	$sql= "select a.id, a.subcon_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.within_group,b.buyer_po_no,b.buyer_style_ref, $color_id_str as color_id, $buyer_po_id_str as buyer_po_id , $delivery_date_str as delivery_date
	 from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
	 where a.entry_form=640 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and b.status_active=1 $order_rcv_date $company $buyer $withinGroup $search_com_cond   $withinGroup and b.id=c.mst_id  
	 group by a.id, a.subcon_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.within_group,b.buyer_po_no,b.buyer_style_ref
	 order by a.id DESC";
	 //echo $sql;
	$data_array=sql_select($sql);
	?>
    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="885" >
        <thead>
            <th width="30">SL</th>
            <th width="60">Job No</th>
            <th width="60">Year</th>
            <th width="130">W/O No</th>
            <th width="60">Within Group</th>
            <th width="130">Buyer Po</th>
            <th width="130">Buyer Style</th>
            <th width="60">Ord Receive Date</th>
            <th width="60">Delivery Date</th>
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
				$delivery_dates=array_unique(explode(",",$row[csf('delivery_date')]));
				$color_name="";	
				foreach ($excolor_id as $color_id)
				{
					if($color_name=="") $color_name=$color_arr[$color_id]; else $color_name.=','.$color_arr[$color_id];
				}
				$delivery_date="";	
				foreach ($delivery_dates as $dDate)
				{
					if($delivery_date=="") $delivery_date=change_date_format($dDate); else $delivery_date.='<br>'.change_date_format($dDate);
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
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$row[csf('subcon_job')]; ?>")' style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="60" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                    <td width="60" style="text-align:center;"><? echo $row[csf('year')]; ?></td>
                    <td width="130"><? echo $row[csf('order_no')]; ?></td>
                    <td width="60"><? echo $yes_no[$row[csf('within_group')]]; ?></td>
                    <td width="130" style="word-break:break-all"><? if ($within_group==1)echo $buyer_po; else echo $row[csf('buyer_po_no')];//echo $buyer_po; ?></td>
                    <td width="130" style="word-break:break-all"><? if ($within_group==1)echo $buyer_style; echo $row[csf('buyer_style_ref')];//echo $buyer_style; ?></td>
                    <td width="60" style="text-align:center;"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                    <td width="60" style="text-align:center;word-break:break-all"><? echo $delivery_date; ?></td>	
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
 
if ($action=="load_php_data_to_form")
{
	
	//echo "select id, subcon_job, company_id, location_id, party_id, currency_id, exchange_rate, party_location, delivery_date, rec_start_date, rec_end_date, receive_date, within_group, party_location, order_id, order_no, conv_factor,gmts_type from subcon_ord_mst where subcon_job='$data' and status_active=1"; die;
	$nameArray=sql_select( "select id, subcon_job, company_id, location_id, party_id, currency_id, exchange_rate, party_location, delivery_date, rec_start_date, rec_end_date, receive_date, within_group, party_location, order_id, order_no, conv_factor,gmts_type from subcon_ord_mst where subcon_job='$data' and status_active=1" );
	foreach ($nameArray as $row)
	{
		
		echo "document.getElementById('txt_job_no').value 			= '".$row[csf("subcon_job")]."';\n";  
		echo "document.getElementById('cbo_company_name').value 	= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_within_group').value 	= '".$row[csf("within_group")]."';\n";  
		echo "$('#cbo_company_name').attr('disabled','true')".";\n";
		echo "fnc_load_party(1,".$row[csf("within_group")].");\n";	
	//echo "load_drop_down( 'requires/wash_order_entry_controller', document.getElementById('cbo_gmts_type').value, 'load_drop_down_location', 'location_td' );\n";	
		echo "document.getElementById('cbo_location_name').value 		= '".$row[csf("location_id")]."';\n";
	//echo "load_drop_down( 'requires/wash_order_entry_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_buyer', 'buyer_td' );\n";
		echo "document.getElementById('cbo_party_name').value			= '".$row[csf("party_id")]."';\n";
		echo "document.getElementById('cbo_currency').value				= '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('txt_ex_rate').value				= '".$row[csf("exchange_rate")]."';\n";
		echo "fnc_load_party(2,".$row[csf("within_group")].");\n";	 
		echo "document.getElementById('cbo_party_location').value		= '".$row[csf("party_location")]."';\n";	
		echo "document.getElementById('txt_order_receive_date').value	= '".change_date_format($row[csf("receive_date")])."';\n"; 
		echo "document.getElementById('txt_delivery_date').value		= '".change_date_format($row[csf("delivery_date")])."';\n"; 
		//echo "document.getElementById('cbo_gmts_type').value		= '".change_date_format($row[csf("gmts_type")])."';\n"; 
		echo "document.getElementById('cbo_gmts_type').value		= '".$row[csf("gmts_type")]."';\n";
		echo "document.getElementById('txt_rec_start_date').value		= '".change_date_format($row[csf("rec_start_date")])."';\n"; 
		echo "document.getElementById('txt_rec_end_date').value		= '".change_date_format($row[csf("rec_end_date")])."';\n"; 
		echo "document.getElementById('hid_order_id').value          	= '".$row[csf("order_id")]."';\n";
		echo "document.getElementById('txt_order_no').value         	= '".$row[csf("order_no")]."';\n";
		
		echo "document.getElementById('txt_converstion_factor').value				= '".$row[csf("conv_factor")]."';\n";
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
		load_drop_down( 'wash_order_entry_controller', company+'_'+1+'_'+party_name, 'load_drop_down_buyer', 'buyer_td' );
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
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_search_category').value+'_'+<? echo $company;?>+'_'+document.getElementById('cbo_search_type').value, 'create_booking_search_list_view', 'search_div', 'wash_order_entry_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                    </td>
                </tr>
                <tr>
                    <td colspan="7"align="center" valign="middle"><?  echo load_month_buttons(1); ?></td>
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
	
	if ($data[6]!=0) $company_cond=" and a.supplier_id='$data[6]'"; else { echo "Please Select Party First."; die; }
	if ($data[0]!=0) $party_cond=" and a.company_id='$data[0]'"; else  $party_cond="";//{ echo "Please Select Buyer First."; die; }
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
	$pre_sql ="Select id, emb_name, emb_type, body_part_id  from wo_pre_cost_embe_cost_dtls where emb_name=3 and status_active=1 and is_deleted=0";
	$pre_sql_res=sql_select($pre_sql);
	foreach ($pre_sql_res as $row)
	{
		$pre_cost_trims_arr[$row[csf("id")]]['emb_name']=$row[csf("emb_name")];
		$pre_cost_trims_arr[$row[csf("id")]]['emb_type']=$row[csf("emb_type")];
		//$pre_cost_trims_arr[$row[csf("id")]]['body_part_id']=$row[csf("body_part_id")];
	}
	unset($pre_sql_res);
	
	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="") $booking_date = "and a.booking_date between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
		$wo_year="YEAR(a.insert_date)";
		$gmts_color_id_cond="group_concat(b.gmts_color_id)";
		$pre_cost_dtls_id_cond="group_concat(b.pre_cost_fabric_cost_dtls_id)";
		$pre_cost_trims_cond="group_concat(b.pre_cost_fabric_cost_dtls_id)";
		$gmts_item_cond="group_concat(b.gmt_item)";
		$po_id_cond="group_concat(b.po_break_down_id)";
	}
	else if($db_type==2)
	{
		if ($data[2]!="" &&  $data[3]!="") $booking_date = "and a.booking_date between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
		$wo_year="to_char(a.insert_date,'YYYY')";
		$gmts_color_id_cond="listagg(d.color_number_id,',') within group (order by d.color_number_id)";
		$pre_cost_dtls_id_cond="listagg(b.pre_cost_fabric_cost_dtls_id,',') within group (order by b.pre_cost_fabric_cost_dtls_id)";
		$pre_cost_trims_cond="listagg(b.pre_cost_fabric_cost_dtls_id,',') within group (order by b.pre_cost_fabric_cost_dtls_id)";
		$gmts_item_cond="listagg(b.gmt_item,',') within group (order by b.gmt_item)";
		$po_id_cond="listagg(b.po_break_down_id,',') within group (order by b.po_break_down_id)";
	} 
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	
	$sql= "select $wo_year as year, a.id, a.booking_type, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id, $pre_cost_trims_cond as pre_cost_trims_id, $gmts_item_cond as gmts_item, $po_id_cond as po_id, $pre_cost_dtls_id_cond as pre_cost_dtls_id, $gmts_color_id_cond as gmts_color_id from  wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_embe_cost_dtls c, wo_emb_book_con_dtls d where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and b.id=d.wo_booking_dtls_id and a.booking_type=6 and a.status_active=1 and c.emb_name=3 and a.lock_another_process!=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $booking_date $company $party_cond $woorder_cond $year_cond $po_idsCond group by a.insert_date, a.id, a.booking_type, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id, a.exchange_rate order by a.id DESC";
	//echo $sql;
	$data_array=sql_select($sql);
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="940" >
        <thead>
            <th width="30">SL</th>
            <th width="60">W/O Year</th>
            <th width="60">W/O No</th>
            <th width="70">W/O Date</th>
            <th width="100">Buyer</th>
            <th width="100">Buyer Po</th>
            <th width="100">Buyer Style</th>
            <th width="100">Buyer Job</th>
            <th width="100">Gmts. Item</th>
            <th width="100">Gmts. Color</th>
            <th>W/O Wash Type</th>
        </thead>
        </table>
        <div style="width:940px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="920" class="rpt_table" id="list_view">
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
				
				$gmts_color_id=array_unique(explode(",",$row[csf('gmts_color_id')]));
				$gmts_color_name="";
				foreach ($gmts_color_id as $color_id)
				{
					if($gmts_color_name=='') $gmts_color_name=$color_arr[$color_id]; else $gmts_color_name.=','.$color_arr[$color_id];
				}
				
				$expre_cost_trims_id=array_unique(explode(",",$row[csf('pre_cost_dtls_id')]));
				$body_part_name=""; $embl_name=""; $embl_type="";
				foreach ($expre_cost_trims_id as $pre_cost_id)
				{
					if($embl_name=="") $embl_name=$emblishment_name_array[$pre_cost_trims_arr[$pre_cost_id]['emb_name']]; else $embl_name.=','.$emblishment_name_array[$pre_cost_trims_arr[$pre_cost_id]['emb_name']];
					
					if($pre_cost_trims_arr[$pre_cost_id]['emb_name']==1) $emb_type=$emblishment_print_type;
					else if($pre_cost_trims_arr[$pre_cost_id]['emb_name']==2) $emb_type=$emblishment_embroy_type;
					else if($pre_cost_trims_arr[$pre_cost_id]['emb_name']==3) $emb_type=$emblishment_wash_type;
					else if($pre_cost_trims_arr[$pre_cost_id]['emb_name']==4) $emb_type=$emblishment_spwork_type;
					else if($pre_cost_trims_arr[$pre_cost_id]['emb_name']==5) $emb_type=$emblishment_gmts_type;
					
					if($embl_type=="") $embl_type=$emb_type[$pre_cost_trims_arr[$pre_cost_id]['emb_type']]; else $embl_type.=','.$emb_type[$pre_cost_trims_arr[$pre_cost_id]['emb_type']]; 
				}
				
				$embl_type=implode(", ",array_unique(explode(",",$embl_type)));
				
				$gmts_item_name="";
				$exgmts_item_id=explode(",",$row[csf('gmts_item')]);
				foreach($exgmts_item_id as $item_id)
				{
					if($gmts_item_name=="") $gmts_item_name=$garments_item[$item_id]; else $gmts_item_name.=','.$garments_item[$item_id];
				}
				$gmts_item_name=implode(", ",array_unique(explode(",",$gmts_item_name)));
				$currency_rate=set_conversion_rate( $row[csf("currency_id")], $row[csf("booking_date")] );	
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$row[csf('booking_no')].'_'.$row[csf('currency_id')].'_'.$currency_rate; ?>")' style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="60" align="center"><? echo $row[csf('year')]; ?></td>
                    <td width="60" align="center"><? echo $row[csf('booking_no_prefix_num')]; ?></td>
                    <td width="70"><? echo change_date_format($row[csf('booking_date')]); ?></td>
                    <td width="100" style="word-break:break-all"><? echo $buyer_name; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $po_no; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $buyer_style; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $buyer_job; ?></td>
                    
                    <td width="100" style="word-break:break-all"><? echo $gmts_item_name; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $gmts_color_name; ?></td>
                    <td style="word-break:break-all"><? echo $embl_type; ?></td>
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
	$nameArray=sql_select( "select id, booking_type, booking_no, company_id, buyer_id, job_no, booking_date, currency_id, exchange_rate from  wo_booking_mst where booking_type=6 and status_active=1 and is_deleted=0 and id='$data[0]'" );
	//$sql= "select to_char(insert_date,'YYYY') as year, id, booking_type, booking_no, company_id, buyer_id, job_no, booking_date from  wo_booking_mst where booking_type=6 and status_active=1 and is_deleted=0 $booking_date $company $order_cond order by booking_no";
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_order_no').value 	= '".$row[csf("booking_no")]."';\n";  
		echo "document.getElementById('cbo_party_name').value 	= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('hid_order_id').value		= '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_currency').value		= '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('txt_ex_rate').value		= '".$row[csf("exchange_rate")]."';\n";
	}
	exit();	
}

if( $action=='order_dtls_list_view' )
{
	//echo $data;
	$data=explode('_',$data);
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$size_arr=return_library_array( "select id,size_name from lib_size",'id','size_name');
	$tblRow=0;
	$buyer_po_arr=array();
	
	$buyer_po_sql = sql_select("select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst");
	
	foreach($buyer_po_sql as $row)
	{
		$buyer_po_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
		$buyer_po_arr[$row[csf('id')]]['buyerpo']=$row[csf('po_number')];
	}
	unset($buyer_po_sql);
	//$prev_pi_qnty_arr_dtls=return_library_array("select a.id, a.grey_qnty_by_uom-sum(b.quantity) as balance_qty  from fabric_sales_order_dtls a ,com_export_pi_dtls b where  a.id=work_order_dtls_id and b.status_active=1 and a.status_active=1 and a.is_deleted=0 group by a.id , a.finish_qty ",'id','balance_qty');
	//print_r($prev_pi_qnty_arr_dtls);
	//1_10549_FAL-EB-18-00022
	if($data[0]==2)
	{
		$qry_result=sql_select( "select id, mst_id, description, process, embellishment_type, rate,prod_sequence_no from subcon_ord_breakdown where job_no_mst='$data[1]'");	
		//echo "select id, mst_id, order_id, job_no_mst, book_con_dtls_id, item_id, body_part, embellishment_type, description, color_id, size_id, qnty, rate, amount from subcon_ord_breakdown where job_no_mst='$data[1]'";
		$data_break_arr=array(); $data_break=''; $add_comma=0; $k=1;
		foreach ($qry_result as $row)
		{
			if($row[csf('description')]=="") $row[csf('description')]=0;
			if($row[csf('prod_sequence_no')]=="") $row[csf('prod_sequence_no')]=0;
			if($row[csf('process')]=="") $row[csf('process')]=0;
			if($row[csf('embellishment_type')]=="") $row[csf('embellishment_type')]=0;
			if($row[csf('rate')]=="") $row[csf('rate')]=0;
			if(!in_array($row[csf('mst_id')],$temp_arr_mst_id))
			{
				$temp_arr_mst_id[]=$row[csf('mst_id')];
				//if($k!=1) {  }
				$add_comma=0; $data_break='';
				
			}
			$k++;
			
			if ($add_comma!=0) $data_break ="__";
			$data_break_arr[$row[csf('mst_id')]].=$row[csf('description')].'_'.$row[csf('process')].'_'.$row[csf('embellishment_type')].'_'.$row[csf('rate')].'_'.$row[csf('id')].'_'.$row[csf('prod_sequence_no')].',';
			$add_comma++;
		}
	}
	//die;
	//print_r($data_break_arr);
	if($data[2]==1)
	{
		$embl_po_arr=array();
		if($data[0]==2)
		{
			$sql_up = "select id, order_no, buyer_po_id, booking_dtls_id, gmts_item_id, order_quantity, order_uom, rate, amount, amount_domestic, smv, delivery_date, wastage, remarks, gmts_color_id, gmts_size_id, party_buyer_name from subcon_ord_dtls where job_no_mst='$data[1]' and status_active=1 and is_deleted=0 order by id ASC";
			$data_arrup=sql_select($sql_up);
			
			foreach($data_arrup as $row)
			{
				$data[1]=$row[csf('order_no')];
				$embl_po_arr[$row[csf('booking_dtls_id')]]['id']=$row[csf('id')];
				$embl_po_arr[$row[csf('booking_dtls_id')]]['smv']=$row[csf('smv')];
				$embl_po_arr[$row[csf('booking_dtls_id')]]['delivery_date']=$row[csf('delivery_date')];
				$embl_po_arr[$row[csf('booking_dtls_id')]]['wastage']=$row[csf('wastage')];
				$embl_po_arr[$row[csf('booking_dtls_id')]]['order_uom']=$row[csf('order_uom')];
				$embl_po_arr[$row[csf('booking_dtls_id')]]['rate']=$row[csf('rate')];
				$embl_po_arr[$row[csf('booking_dtls_id')]]['amount']=$row[csf('amount')];
				$embl_po_arr[$row[csf('booking_dtls_id')]]['amount_domestic']=$row[csf('amount_domestic')];
				$embl_po_arr[$row[csf('booking_dtls_id')]]['remarks']=$row[csf('remarks')];

				$embl_po_arr[$row[csf('booking_dtls_id')]]['gmts_color_id']=$row[csf('gmts_color_id')];
				$embl_po_arr[$row[csf('booking_dtls_id')]]['gmts_size_id']=$row[csf('gmts_size_id')];
				$embl_po_arr[$row[csf('booking_dtls_id')]]['party_buyer_name']=$row[csf('party_buyer_name')];
			}
		}
		
		//$sql = "select a.id as embe_cost_dtls_id, a.job_no, a.emb_name, a.emb_type, a.body_part_id, b.id as booking_dtls_id, b.booking_no, b.po_break_down_id, b.gmt_item, b.wo_qnty, b.rate, b.amount, b.delivery_date from wo_pre_cost_embe_cost_dtls a, wo_booking_dtls b where b.status_active=1 and b.is_deleted=0 and a.status_active=1 and b.pre_cost_fabric_cost_dtls_id=a.id and b.booking_type=6 and a.emb_name=3 and a.job_no=b.job_no and b.booking_no=trim('$data[1]') group by a.id, a.job_no, b.job_no, a.emb_name, a.emb_type, a.body_part_id, b.id, b.booking_no, b.po_break_down_id, b.gmt_item, b.wo_qnty, b.rate, b.amount, b.delivery_date order by b.id ASC";
		//$sql = "select id as booking_dtls_id, pre_cost_fabric_cost_dtls_id as embe_cost_dtls_id, booking_no, po_break_down_id, gmt_item, gmts_color_id, wo_qnty, rate, amount, delivery_date from wo_booking_dtls where status_active=1 and is_deleted=0 and booking_type=6 and emblishment_name=3 and booking_no=trim('$data[1]') order by id ASC";
		
		$sql = "select a.id as embe_cost_dtls_id, a.job_no, a.emb_name, a.emb_type, a.body_part_id, c.id as booking_dtls_id, b.booking_no, b.po_break_down_id, b.gmt_item, b.delivery_date, c.color_number_id, c.gmts_sizes, sum(c.requirment) as wo_qnty, sum(c.rate) as rate, sum(c.amount) as amount from wo_pre_cost_embe_cost_dtls a, wo_booking_dtls b, wo_emb_book_con_dtls c where b.status_active=1 and b.is_deleted=0 and a.status_active=1 and b.pre_cost_fabric_cost_dtls_id=a.id and b.id=c.wo_booking_dtls_id and b.booking_type=6 and a.emb_name=3 and a.job_no=b.job_no and b.booking_no=trim('$data[1]') and c.is_deleted=0 and c.status_active=1 and c.requirment !=0 group by a.id, a.job_no, b.job_no, a.emb_name, a.emb_type, a.body_part_id, c.id, b.booking_no, b.po_break_down_id, b.gmt_item, b.delivery_date, c.color_number_id, c.gmts_sizes order by c.id ASC";
	}
	else
	{
		$sql = "select id, order_no, buyer_po_id, buyer_po_no, buyer_style_ref, booking_dtls_id, gmts_item_id as gmt_item, main_process_id as emb_name, embl_type as emb_type, body_part as body_part_id, order_quantity as wo_qnty, order_uom, rate, amount, smv, delivery_date, wastage, gmts_color_id, gmts_size_id, amount_domestic, party_buyer_name from subcon_ord_dtls where job_no_mst='$data[1]' and mst_id='$data[3]' and status_active=1 and is_deleted=0 order by id ASC";
	}
	//echo $sql; die; 
	$data_array=sql_select($sql); $readonly='readonly';
	if(count($data_array) > 0)
	{
		foreach($data_array as $row)
		{
			$tblRow++;
			$dtls_id=0; $smv=0; $wastage=0;  $order_uom=0; $wo_qnty=0; $wo_rate=0; $gmts_color_id=0; $gmts_sizes=0; $readonly=''; $buyerPo=''; $buyerPoId=''; $buyerStyle=''; 
			if($data[2]==1)
			{
				if($data[0]==2)
				{
					$dtls_id=$embl_po_arr[$row[csf('booking_dtls_id')]]['id']; 
					$smv=$embl_po_arr[$row[csf('booking_dtls_id')]]['smv']; 
					$row[csf("delivery_date")]=$embl_po_arr[$row[csf('booking_dtls_id')]]['delivery_date']; 
					$wastage=$embl_po_arr[$row[csf('booking_dtls_id')]]['wastage'];
					$order_uom=$embl_po_arr[$row[csf('booking_dtls_id')]]['order_uom'];
					$wo_rate=$embl_po_arr[$row[csf('booking_dtls_id')]]['rate'];
					$gmts_color_id=$embl_po_arr[$row[csf('booking_dtls_id')]]['gmts_color_id'];
					$gmts_sizes=$embl_po_arr[$row[csf('booking_dtls_id')]]['gmts_size_id'];
					$amount=$embl_po_arr[$row[csf('booking_dtls_id')]]['amount'];
					$amount_domestic=$embl_po_arr[$row[csf('booking_dtls_id')]]['amount_domestic'];
				}
				else 
				{
					$wo_rate=$row[csf('rate')];
					//$wo_rate=0;
					$gmts_color_id=$row[csf('color_number_id')];
					$gmts_sizes=$row[csf('gmts_sizes')];
					$amount=$row[csf('amount')];
					$order_uom=2;
					//$service_rate=$row[csf('rate')];
				}
				$buyerPo=$buyer_po_arr[$row[csf('po_break_down_id')]]['buyerpo'];
				$buyerPoId=$row[csf('po_break_down_id')];
				$buyerStyle=$buyer_po_arr[$row[csf('po_break_down_id')]]['style'];
				$readonly='readonly';
			}
			else if($data[2]==2)
			{
				if($data[0]==2)
				{
					$dtls_id=$row[csf('id')]; 
					$smv=$row[csf('smv')];
					$row[csf("delivery_date")]=$row[csf('delivery_date')];
					$wastage=$row[csf('wastage')];
					$order_uom=$row[csf('order_uom')];
					$readonly='';
					//$wo_qnty=$row[csf('wo_qnty')];
					$wo_rate=$row[csf('rate')];
					$gmts_color_id=$row[csf('gmts_color_id')];
					$gmts_sizes=$row[csf('gmts_size_id')];
					$amount=$row[csf('amount')];
					$amount_domestic=$row[csf('amount_domestic')];
				} 
				else 
				{
					$wo_rate=0;
					$order_uom=2;
				}
				$buyerPo=$row[csf('buyer_po_no')];
				$buyerPoId='';
				$buyerStyle=$row[csf('buyer_style_ref')];
				$readonly='';
					
			}
			$wo_qnty=$row[csf('wo_qnty')];
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
            <td><input name="txtpartybuyername_<? echo $tblRow; ?>" id="txtpartybuyername_<? echo $tblRow; ?>" value="<? echo $row[csf('party_buyer_name')] ?>" class="text_boxes" type="text"  style="width:80px"  <? echo $readonly; ?>  /></td>
				<td><input name="txtbuyerPo_<? echo $tblRow; ?>" id="txtbuyerPo_<? echo $tblRow; ?>" value="<? echo $buyerPo ; ?>" class="text_boxes" type="text"  style="width:80px" <? echo $readonly; ?> />
					<input name="txtbuyerPoId_<? echo $tblRow; ?>" id="txtbuyerPoId_<? echo $tblRow; ?>" value="<? echo $buyerPoId; ?>" class="text_boxes" type="hidden" style="width:70px" readonly />
				</td>
				<td><input name="txtstyleRef_<? echo $tblRow; ?>" id="txtstyleRef_<? echo $tblRow; ?>" value="<? echo $buyerStyle ; ?>" class="text_boxes" type="text"  style="width:80px"  <? echo $readonly; ?>  /></td>
				<td><? echo create_drop_down( "cboGmtsItem_".$tblRow, 90, $garments_item,"", 1, "-- Select --",$row[csf('gmt_item')], "",1,"" ); ?></td>			
				<td>
                	<input name="txtColor_<? echo $tblRow; ?>" id="txtColor_<? echo $tblRow; ?>" type="text" class="text_boxes" value="<? echo $color_library[$gmts_color_id]; ?>" style="width:80px"  placeholder="Display" <? echo $readonly; ?> />
                    <input name="txtColorId_<? echo $tblRow; ?>" id="txtColorId_<? echo $tblRow; ?>" type="hidden" class="text_boxes" value="<? echo $gmts_color_id; ?>" style="width:50px" />
                </td>
                <td>
                	<input name="txtSize_<? echo $tblRow; ?>" id="txtSize_<? echo $tblRow; ?>" type="text" class="text_boxes" value="<? echo $size_arr[$gmts_sizes]; ?>" style="width:80px" placeholder="Display" <? echo $readonly; ?> />
                    <input name="txtSizeId_<? echo $tblRow; ?>" id="txtSizeId_<? echo $tblRow; ?>" type="hidden" class="text_boxes" value="<? echo $gmts_sizes; ?>" style="width:50px" />
                </td>
				<td><? if($data[2]==1){$readonly="readonly";} ?>
                
                <input name="txtOrderQuantity_<? echo $tblRow; ?>" id="txtOrderQuantity_<? echo $tblRow; ?>" value="<? echo number_format($wo_qnty,4,'.',''); ?>" class="text_boxes_numeric" type="text"  style="width:60px" placeholder="Click To Search" <? echo $readonly ?> /></td>
                
               
                
				<td><?
				
				 echo create_drop_down( "cboUom_".$tblRow, 60, $unit_of_measurement,"", 0, "-- Select --",$order_uom,"fnc_load_uom(1,this.value);", 1,"2,1" );
				 ?></td>
				<td><input name="txtRate_<? echo $tblRow; ?>" id="txtRate_<? echo $tblRow; ?>" value="<? echo number_format( $wo_rate,4,'.',''); ?>" type="text" onClick="openmypage_order_rate(1,'<? echo $row[csf('booking_dtls_id')]; ?>',<? echo $tblRow; ?>)" class="text_boxes_numeric" style="width:50px" readonly placeholder="<? echo $service_rate; ?>"/></td>
				<td><input name="txtAmount_<? echo $tblRow; ?>" id="txtAmount_<? echo $tblRow; ?>" value="<? echo number_format($amount,4,'.',''); ?>" type="text" style="width:70px"  class="text_boxes_numeric" readonly /></td> 
                <td><input name="txtDomAmount_<? echo $tblRow; ?>" id="txtDomAmount_<? echo $tblRow; ?>" value="<? echo number_format($amount_domestic,4,'.',''); ?>" type="text" style="width:70px"  class="text_boxes_numeric" readonly /></td>
				<td><input name="txtSmv_<? echo $tblRow; ?>" id="txtSmv_<? echo $tblRow; ?>" type="text" value="<? echo $smv; ?>" class="text_boxes_numeric" style="width:40px" />
				<td><input type="text" name="txtOrderDeliveryDate_<? echo $tblRow; ?>"  id="txtOrderDeliveryDate_<? echo $tblRow; ?>" value="<? echo change_date_format($row[csf("delivery_date")]);?>" class="datepicker" style="width:50px" /></td>
				<td><input name="txtWastage_<? echo $tblRow; ?>" id="txtWastage_<? echo $tblRow; ?>" type="text" value="<? echo $wastage; ?>" class="text_boxes_numeric" style="width:40px" />
					<input name="hdnDtlsUpdateId_<? echo $tblRow; ?>" id="hdnDtlsUpdateId_<? echo $tblRow; ?>" type="hidden" value="<? echo $dtls_id; ?>" class="text_boxes_numeric" style="width:40px" />
					<input type="hidden" name="hdnDtlsdata_<? echo $tblRow; ?>" id="hdnDtlsdata_<? echo $tblRow; ?>" value="<? echo implode("__",array_filter(explode(',',$data_break_arr[$dtls_id]))); ?>">
                    <input type="hidden" name="hdnbookingDtlsId_<? echo $tblRow; ?>" id="hdnbookingDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf('booking_dtls_id')]; ?>">
				</td>
                <td><input type="button" name="btnremarks_<? echo $tblRow; ?>" id="btnremarks_<? echo $tblRow; ?>" class="formbuttonplasminus" value="RMK" onClick="openmypage_remarks(<? echo $tblRow; ?>);" />
                    <input type="hidden" name="txtremarks_<? echo $tblRow; ?>" id="txtremarks_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $embl_po_arr[$row[csf('booking_dtls_id')]]['remarks']; ?>" />
                </td>
                <td width="65">
					<input type="button" id="increase_1" name="increase[]" style="width:20px" class="formbuttonplasminus" value="+" onClick="add_dtls_tr(<? echo $tblRow; ?>)" />
					<input type="button" id="decrease_1" name="decrease[]" style="width:20px" class="formbuttonplasminus" value="-" onClick="fnc_delet_dtls_tr(<? echo $tblRow; ?>);" />
				</td>
			</tr>
			<?
		}
	}
	else
	{
		?>		
		<tr>
        	<td><input name="txtpartybuyername_1" id="txtpartybuyername_1" type="text" class="text_boxes" style="width:80px" placeholder="Display" readonly /></td>
            <td><input name="txtbuyerPo_1" id="txtbuyerPo_1" type="text" class="text_boxes" style="width:80px" placeholder="Display" readonly />
                <input name="txtbuyerPoId_1" id="txtbuyerPoId_1" type="hidden" class="text_boxes" style="width:70px" />
            </td>
            <td><input name="txtstyleRef_1" id="txtstyleRef_1" type="text" class="text_boxes" style="width:80px" placeholder="Display" readonly /></td>
            <td><? echo create_drop_down( "cboGmtsItem_1", 90, $garments_item,"", 1, "-- Select --",$selected, "",0,"" ); ?></td>
            <td>
                <input name="txtColor_1" id="txtColor_1" type="text" class="text_boxes" style="width:80px" readonly placeholder="Display" />
                <input name="txtColorId_1" id="txtColorId_1" type="hidden" class="text_boxes" style="width:50px" />
            </td>
            <td>
                <input name="txtSize_1" id="txtSize_1" type="text" class="text_boxes" style="width:60px" readonly placeholder="Display" />
                <input name="txtSizeId_1" id="txtSizeId_1" type="hidden" class="text_boxes" style="width:50px" />
            </td>
            <td><input name="txtOrderQuantity_1" id="txtOrderQuantity_1" class="text_boxes_numeric" type="text"  style="width:60px" readonly/></td>
            <td><?
				echo create_drop_down( "cboUom_1", 60, $unit_of_measurement,"", 0, "-- Select --",2,"fnc_load_uom(1,this.value);", 1,"2,1" );
			// echo create_drop_down( "cboUom_1", 60, $unit_of_measurement,"", 0, "-- Select --",2,1, 1,"2,1" ); ?></td>
            <td><input name="txtRate_1" id="txtRate_1" type="text"  class="text_boxes_numeric" style="width:50px" onClick="openmypage_order_rate(1,'0',1)" placeholder="Browse" /></td>
            <td><input name="txtAmount_1" id="txtAmount_1" type="text" style="width:70px"  class="text_boxes_numeric" readonly /></td> 
            <td><input name="txtDomAmount_1" id="txtDomAmount_1" type="text" style="width:70px"  class="text_boxes_numeric" readonly /></td> 
            <td><input name="txtSmv_1" id="txtSmv_1" type="text"  class="text_boxes_numeric" style="width:40px" /></td> 
            <td><input type="text" name="txtOrderDeliveryDate_1" id="txtOrderDeliveryDate_1" class="datepicker" style="width:50px" /></td>
            <td>
                <input name="txtWastage_1" id="txtWastage_1" type="text"  class="text_boxes_numeric" style="width:40px" />
                <input type="hidden" name="hdnDtlsUpdateId_1" id="hdnDtlsUpdateId_1">
                <input type="hidden" name="hdnDtlsdata_1" id="hdnDtlsdata_1">
                <input type="hidden" name="hdnbookingDtlsId_1" id="hdnbookingDtlsId_1">
            </td>
            <td><input type="button" name="btnremarks_1" id="btnremarks_1" class="formbuttonplasminus" value="RMK" onClick="openmypage_remarks(1);" />
                <input type="hidden" name="txtremarks_1" id="txtremarks_1" class="text_boxes" />
            </td>
            <td width="65">
				<input type="button" id="increase_1" name="increase[]" style="width:20px" class="formbuttonplasminus" value="+" onClick="add_dtls_tr(1)" />
				<input type="button" id="decrease_1" name="decrease[]" style="width:20px" class="formbuttonplasminus" value="-" onClick="fnc_delet_dtls_tr(1);" />
			</td>
        </tr>
		<?
	}
	exit();
}

if($action=="order_rate_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
    ?>
    <script>
		function add_share_row( i ) 
		{
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
			
			$('#txtdescription_'+i).val('');
			$('#txtprodsequenceno_'+i).val('');
			$('#cboProcess_'+i).val(0);
			$('#cboProcessType_'+i).val(0);
			$('#tbl_share_details_entry tr:eq('+i+') td:eq(3)').attr('id','typetd_'+i);
			$('#cboProcess_'+i).removeAttr("onchange").attr("onchange","fnc_wash_type_load("+i+",);");
			$('#txtorderrate_'+i).removeAttr("onKeyUp").attr("onKeyUp","sum_total_rate("+i+");");
			$('#increaseset_'+i).removeAttr("onClick").attr("onClick","add_share_row("+i+");");
			$('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");
			$('#txtsize_'+i).val('');
			$('#hiddenid_'+i).val('');
			sum_total_rate(i);
		}		
		
		function fn_deletebreak_down_tr(rowNo) 
		{ 
			var numRow = $('table#tbl_share_details_entry tbody tr').length; 
			if(numRow==rowNo && rowNo!=1)
			{
				var updateIdDtls=$('#hiddenid_'+rowNo).val();
				var txtDeletedId=$('#txtDeletedId').val();
				var selected_id='';
				if(updateIdDtls!='')
				{
					if(txtDeletedId=='') selected_id=updateIdDtls; else selected_id=txtDeletedId+','+updateIdDtls;
					$('#txtDeletedId').val( selected_id );
				}
				
				$('#tbl_share_details_entry tbody tr:last').remove();
			}
			else
			{
				return false;
			}
			sum_total_rate(rowNo);
		}

		function fnc_close()
		{
			var tot_row=$('#tbl_share_details_entry tbody tr').length;
			
			var data_break_down="";
			for(var i=1; i<=tot_row; i++)
			{
				if (form_validation('txtprodsequenceno_'+i+'*cboProcess_'+i+'*cboProcessType_'+i+'*txtorderrate_'+i,'Prod. Sequence no*Process*Wash Type*Rate')==false)
				{
					return;
				}
				if($("#txtdescription_"+i).val()=="") $("#txtdescription_"+i).val(0)
				if($("#cboProcess_"+i).val()=="") $("#cboProcess_"+i).val(0);
				if($("#cboProcessType_"+i).val()=="") $("#cboProcessType_"+i).val(0);
				if($("#txtorderrate_"+i).val()=="") $("#txtorderrate_"+i).val(0);
				if($("#hiddenid_"+i).val()=="") $("#hiddenid_"+i).val(0);
				if($("#txtprodsequenceno_"+i).val()=="") $("#txtprodsequenceno_"+i).val(0);
				
				if(data_break_down=="")
				{
					data_break_down+=$('#txtdescription_'+i).val()+'_'+$('#cboProcess_'+i).val()+'_'+$('#cboProcessType_'+i).val()+'_'+$('#txtorderrate_'+i).val()+'_'+$('#hiddenid_'+i).val()+'_'+$('#txtprodsequenceno_'+i).val();
				}
				else
				{
					data_break_down+="__"+$('#txtdescription_'+i).val()+'_'+$('#cboProcess_'+i).val()+'_'+$('#cboProcessType_'+i).val()+'_'+$('#txtorderrate_'+i).val()+'_'+$('#hiddenid_'+i).val()+'_'+$('#txtprodsequenceno_'+i).val();
				}
			}
			$('#hidden_break_tot_row').val( data_break_down );
			//alert(tot_row);//return;
			parent.emailwindow.hide();
		}

		function sum_total_rate(id)
		{
			var tot_row=$('#tbl_share_details_entry tbody tr').length;
			var rate=0;
			for(var i=1; i<=tot_row; i++)
			{
				rate+=$("#txtorderrate_"+i).val()*1;
			}
			$("#txt_tot_rate").val( number_format(rate,4,'.','' ) );
		}
		
		function fnc_wash_type_load(i,data)
		{
			var process=$("#cboProcess_"+i).val();
			load_drop_down( 'wash_order_entry_controller', process+'_'+i+'_'+data, 'load_drop_down_wash_type', 'typetd_'+i);
		}
	</script>
</head>
<body>
	<div align="center" style="width:100%;" >
		<form name="ratepopup_1"  id="ratepopup_1" autocomplete="off">
			<table class="rpt_table" width="530px" cellspacing="0" cellpadding="0" rules="all" id="tbl_share_details_entry">
				<thead>
					<th width="130">Wash Description</th>
                    <th width="35" class="must_entry_caption">Prod. Sequence no</th>
					<th width="80" class="must_entry_caption">Process</th>
					<th width="90" class="must_entry_caption">Wash Type</th>
					<th width="60" class="must_entry_caption">Rate</th>
					<th>&nbsp;</th>
				</thead>
				<tbody>
					<input type="hidden" name="hidden_break_tot_row" id="hidden_break_tot_row" class="text_boxes" style="width:90px" />
					<?
					
					$data_array=explode("__",$data_break);
					//echo count($data_array)."==".$data_break; die;
					//if(count($data_array)==0 || count($data_array)=='' || $data_break=='')
					if($hdnDtlsUpdateId !='')
					{
						$sql_break_down="select id, description, process, embellishment_type, rate,prod_sequence_no from subcon_ord_breakdown where mst_id='$hdnDtlsUpdateId'";
						$data_break_down=sql_select($sql_break_down);
						$break_down_arr=array(); $data_break="";
						foreach($data_break_down as $row)
						{
							$break_down_arr[$row[csf('book_con_dtls_id')]]=$row[csf('id')];
							
							if($row[csf('description')]=="") $row[csf('description')]=0;
							if($row[csf('prod_sequence_no')]=="") $row[csf('prod_sequence_no')]=0;
							if($row[csf('process')]=="") $row[csf('process')]=0;
							if($row[csf('embellishment_type')]=="") $row[csf('embellishment_type')]=0;
							if($row[csf('rate')]=="") $row[csf('rate')]=0;
							
							if($data_break=="") $data_break.=$row[csf('description')].'_'.$row[csf('process')].'_'.$row[csf('embellishment_type')].'_'.$row[csf('rate')].'_'.$row[csf('id')].'_'.$row[csf('prod_sequence_no')];
							else $data_break.='__'.$row[csf('description')].'_'.$row[csf('process')].'_'.$row[csf('embellishment_type')].'_'.$row[csf('rate')].'_'.$row[csf('id')].'_'.$row[csf('prod_sequence_no')];
						}
					}
					$k=0; 
					//$data_array=explode("__",$data_break);
					//if($withinGroup==1) $disabled="disabled"; else $disabled="";
					if(count($data_array)>0)
					{
						
						foreach($data_array as $row)
						{
							$data=explode('_',$row);
							$k++;
							if($data[1]==1) $process_type=$wash_wet_process;
							else if($data[1]==2) $process_type=$wash_dry_process;
							else if($data[1]==3) $process_type=$wash_laser_desing;
							else $process_type=$blank_array;
							?>
							<tr>
								<td><input type="text" id="txtdescription_<? echo $k;?>" name="txtdescription_<? echo $k;?>" class="text_boxes" style="width:120px" value="<? echo $data[0]; ?>" />
								</td>
                                <td><input type="text" id="txtprodsequenceno_<? echo $k;?>" name="txtprodsequenceno_<? echo $k;?>"  class="text_boxes_numeric" style="width:35px"  value="<? echo $data[5]; ?>"/>	</td>
                                <td><? echo create_drop_down( "cboProcess_$k", 80, $wash_type,"", 1, "-- Select --",$data[1],"fnc_wash_type_load($k,$data[2]);", 0,"" );	// fnc_wash_type_load($k,$data[2]); ?></td>
                                <td id="typetd_<? echo $k;?>"><? echo 
                                //echo create_drop_down( "cboProcessType_".$data[1], 90, $wash_type,"", 1, "-- Select --",$data[2],"", 0,"" );
                                create_drop_down( "cboProcessType_$k", 90, $process_type,"", 1, "-- Select --",$data[2],"", 0,"" ); ?></td>
                                <td><input type="text" id="txtorderrate_<? echo $k;?>" name="txtorderrate_<? echo $k;?>"  class="text_boxes_numeric" style="width:60px" onKeyUp="sum_total_rate(<? echo $k;?>)" value="<? echo number_format($data[3],4,'.',''); ?>" <? echo $disabled; ?> />
								</td>
								<td>
                                    <input type="hidden" id="hiddenid_<? echo $k; ?>" name="hiddenid_<? echo $k; ?>"  style="width:15px;" class="text_boxes" value="<? echo $data[4]; ?>" />
									<input type="button" id="increaseset_<? echo $k;?>" style="width:30px" class="formbutton" value="+" onClick="add_share_row(<? echo $k;?>)" <? echo $disabled; ?> />
									<input type="button" id="decreaseset_<? echo $k;?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $k;?> ,'tbl_share_details_entry' );" <? echo $disabled; ?>/>
								</td>  
							</tr>
							<?
						}
					}
					else
					{
						?>
                        <tr>
                            <td><input type="text" id="txtdescription_1" name="txtdescription_1" class="text_boxes" style="width:120px" value="" /></td>
                            <td><input type="text" id="txtprodsequenceno_1" name="txtprodsequenceno_1"  class="text_boxes_numeric" style="width:35px"  value=""/></td>
                            <td><? echo create_drop_down( "cboProcess_1", 80, $wash_type,"", 1, "-- Select --",0,"fnc_wash_type_load(1,0);", 0,"" ); ?></td>
                            <td id="typetd_1"><? echo create_drop_down( "cboProcessType_1", 90, $blank_array,"", 1, "-- Select --",0,1, 0,"" ); ?></td>
                            <td><input type="text" id="txtorderrate_1" name="txtorderrate_1"  class="text_boxes_numeric" style="width:35px" onKeyUp="sum_total_rate(1)" value="" />
                            </td>
                            <td>
                                <input type="hidden" id="hiddenid_1" name="hiddenid_1"  style="width:15px;" class="text_boxes" value="" />
                                <input type="button" id="increaseset_1" style="width:30px" class="formbutton" value="+" onClick="add_share_row(1)" />
                                <input type="button" id="decreaseset_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(1 ,'tbl_share_details_entry' );" />
                            </td>  
                        </tr>
						<?
					}
					?> 
				</tbody>
				<tfoot>
					<th colspan="4">Total Rate</th> 
					<th colspan="1" align="right"><input type="text" id="txt_tot_rate" name="txt_tot_rate" class="text_boxes_numeric" readonly style="width:60px" value="" /></th>
                    <th>&nbsp;</th>
				</tfoot>
			</table> 
			<table>
				<tr>
					<td align="center"><input type="button" name="main_close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" /></td>
				</tr>
			</table>
		</form>
	</div>
</body>
<script>sum_total_rate(0);</script>        
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="remarks_popup")
{
	echo load_html_head_contents("Remarks","../../", 1, 1, $unicode);
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
                          <textarea id="text_new_remarks" name="text_new_remarks" class="text_area" title="Maximum 1000 Character" maxlength="1000" style="width:370px; height:250px" placeholder="Remarks Here. Maximum 1000 Character." ><? echo $data; ?></textarea>
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
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}
?>