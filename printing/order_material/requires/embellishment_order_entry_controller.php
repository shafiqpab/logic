<?
include('../../../includes/common.php');
session_start();

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
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
	
	echo create_drop_down( $dropdown_name, 150, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );	
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
	echo create_drop_down( "cboembtype_".$data[1], 80, $emb_type,"", 1, "-- Select --","","","",'','','','','','',"cboembtype[]");
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
	$user_id=$_SESSION['logic_erp']['user_id'];
	
	if ($operation==0) // Insert Start Here=================================================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if($db_type==0) $insert_date_con="and YEAR(insert_date)=".date('Y',time())."";
		else if($db_type==2) $insert_date_con="and TO_CHAR(insert_date,'YYYY')=".date('Y',time())."";
		
		$new_job_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'POE', date("Y",time()), 5, "select job_no_prefix,job_no_prefix_num from subcon_ord_mst where entry_form=204 and company_id=$cbo_company_name $insert_date_con order by id desc ", "job_no_prefix", "job_no_prefix_num" ));
		if(str_replace("'",'',$txt_order_no)==""){
			$txt_order_no=$new_job_no[0];
		}else{
			$txt_order_no=str_replace("'",'',$txt_order_no);
		}
		$id=return_next_id("id","subcon_ord_mst",1);
		$id1=return_next_id( "id", "subcon_ord_dtls",1) ;
		$id3=return_next_id( "id", "subcon_ord_breakdown", 1 ) ;
		$rID3=true;
		$field_array="id, entry_form,wo_type, embellishment_job, job_no_prefix, job_no_prefix_num, company_id, location_id, within_group, party_id, party_location, currency_id, receive_date, delivery_date, rec_start_date, rec_end_date, order_id, order_no,exchange_rate,booking_type, remarks, inserted_by, insert_date";
		//txt_job_no*cbo_company_name*cbo_location_name*cbo_within_group*cbo_party_name*cbo_party_location*cbo_currency*txt_order_receive_date*txt_delivery_date*txt_order_no*hid_order_id*update_id
		$data_array="(".$id.", 204,".$cbo_wo_type.", '".$new_job_no[0]."', '".$new_job_no[1]."', '".$new_job_no[2]."', ".$cbo_company_name.", ".$cbo_location_name.", ".$cbo_within_group.", ".$cbo_party_name.", ".$cbo_party_location.", ".$cbo_currency.", ".$txt_order_receive_date.", ".$txt_delivery_date.",".$txt_rec_start_date.",".$txt_rec_end_date.", ".$hid_order_id.", '".$txt_order_no."',".$txt_exchange_rate.",".$hid_booking_type.",".$txt_remarks.", ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."')";
		
		$txt_job_no=$new_job_no[0];
		
		$field_array2="id, mst_id, job_no_mst, order_id, order_no, buyer_po_id, booking_dtls_id, gmts_item_id, main_process_id, embl_type, body_part, order_quantity, order_uom, rate, amount,domestic_amount, smv, delivery_date, wastage, buyer_po_no, buyer_style_ref, buyer_buyer, inserted_by, insert_date";
		$field_array3="id, mst_id, order_id, job_no_mst, book_con_dtls_id, item_id, body_part, embellishment_type, description, color_id, size_id, qnty, rate, amount,cutting_no,domestic_amount,fabric_description, inserted_by, insert_date";

		$color_library_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
		$size_library_arr=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
		$data_array2 = $data_array3="";  $add_commaa=0; $add_commadtls=0;  $new_array_color=array();  $new_array_size=array();

		for($i=1; $i<=$total_row; $i++)
		{			
			$txtbuyerPoId			= "txtbuyerPoId_".$i; 
			$txtbuyerPo				= "txtbuyerPo_".$i;
			$txtstyleRef			= "txtstyleRef_".$i;
			$txtbuyer				= "txtbuyer_".$i; 
			$cboGmtsItem			= "cboGmtsItem_".$i;
			$cboProcessName			= "cboProcessName_".$i;
			$cboembtype				= "cboembtype_".$i;
			$cboBodyPart			= "cboBodyPart_".$i;
			$txtOrderQuantity		= "txtOrderQuantity_".$i;
			$cboUom 				= "cboUom_".$i;
			$txtRate 				= "txtRate_".$i;
			$txtAmount 				= "txtAmount_".$i;
			$txtdomisticamount      ="txtdomisticamount_".$i;				
			$txtSmv 				= "txtSmv_".$i;
			$txtOrderDeliveryDate 	= "txtOrderDeliveryDate_".$i;
			$txtWastage 			= "txtWastage_".$i;
			$hdnDtlsdata 			= "hdnDtlsdata_".$i;
			$hdnDtlsUpdateId 		= "hdnDtlsUpdateId_".$i;
			$hdnbookingDtlsId 		= "hdnbookingDtlsId_".$i;

			if($db_type==0)
			{
				$orderDeliveryDate=change_date_format(str_replace("'",'',$$txtOrderDeliveryDate),'yyyy-mm-dd');
			}
			else
			{
				$orderDeliveryDate=change_date_format(str_replace("'",'',$$txtOrderDeliveryDate), "", "",1);
			}
			
			if(str_replace("'",'',$$txtbuyerPoId)=="") $txtbuyerPoId=0; else $txtbuyerPoId=str_replace("'",'',$$txtbuyerPoId);

			if ($add_commaa!=0) $data_array2 .=","; $add_comma=0;
			$data_array2 .="(".$id1.",".$id.",'".$new_job_no[0]."',".$hid_order_id.",'".$txt_order_no."','".$txtbuyerPoId."',".$$hdnbookingDtlsId.",".$$cboGmtsItem.",".$$cboProcessName.",".$$cboembtype.",".$$cboBodyPart.",".str_replace(",",'',$$txtOrderQuantity).",".$$cboUom.",".$$txtRate.",".str_replace(",",'',$$txtAmount).",".str_replace(",",'',$$txtdomisticamount).",".$$txtSmv.",'".$orderDeliveryDate."',".$$txtWastage.",".$$txtbuyerPo.",".$$txtstyleRef.",".$$txtbuyer.",'".$user_id."','".$pc_date_time."')";
			
			$dtls_data=explode("__",str_replace("'",'',$$hdnDtlsdata));
			/*echo "10**".$total_row; 
			print_r($dtls_data);
			die;*/
			for($j=0; $j<count($dtls_data); $j++)
			{
				$exdata=explode("_",$dtls_data[$j]);
				
				$description="'".$exdata[0]."'";
				$colorname="'".$exdata[1]."'";
				$sizename="'".$exdata[2]."'";
				$qty="'".str_replace(",",'',$exdata[3])."'";
				$rate="'".str_replace(",",'',$exdata[4])."'";
				$amount="'".str_replace(",",'',$exdata[5])."'";
				$book_con_dtls_id="'".$exdata[6]."'";
				$dtlsup_id="'".$exdata[7]."'";
				$cutting_no="'".$exdata[8]."'";
				$fabric_description="'".$exdata[9]."'";
				
				$domistic_amount=str_replace(",",'',$exdata[5])*str_replace("'",'',$txt_exchange_rate);
				//$domistic_amount="'".$exdata[9]."'";

				if (str_replace("'", "", trim($colorname)) != "") {
					if (!in_array(str_replace("'", "", trim($colorname)),$new_array_color)){
						$color_id = return_id( str_replace("'", "", trim($colorname)), $color_library_arr, "lib_color", "id,color_name","204");
						$new_array_color[$color_id]=str_replace("'", "", trim($colorname));
					}
					else $color_id =  array_search(str_replace("'", "", trim($colorname)), $new_array_color);
				} else $color_id = 0;

				if (str_replace("'", "", trim($sizename)) != "") {
					if (!in_array(str_replace("'", "", trim($sizename)),$new_array_size)){
						$size_id = return_id( str_replace("'", "", trim($sizename)), $size_library_arr, "lib_size", "id,size_name","204");
						$new_array_size[$size_id]=str_replace("'", "", trim($sizename));
					}
					else $size_id =  array_search(str_replace("'", "", trim($sizename)), $new_array_size);
				} else $size_id = 0;
				
				
				if ($add_commadtls!=0) $data_array3 .=",";
				$data_array3.="(".$id3.",".$id1.",".$hid_order_id.",'".$new_job_no[0]."',".$book_con_dtls_id.",".$$cboGmtsItem.",".$$cboBodyPart.",".$$cboembtype.",".$description.",'".$color_id."','".$size_id."',".$qty.",".$rate.",".$amount.",".$cutting_no.",".$domistic_amount.",".$fabric_description.",'".$user_id."','".$pc_date_time."')";
				$id3=$id3+1; $add_commadtls++;
			}
			
			$id1=$id1+1; $add_commaa++;
			//echo "10**INSERT INTO subcon_ord_breakdown (".$field_array3.") VALUES ".$data_array3; die;			
		}
		//echo "10**".$data_array2; die;
		//echo "10**".$data_array3; die;
		//echo "10**INSERT INTO subcon_ord_mst (".$field_array.") VALUES ".$data_array; die;
		//echo "10**INSERT INTO subcon_ord_dtls (".$field_array2.") VALUES ".$data_array2; die;
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
		//echo "10**$flag**".str_replace("'","",$cbo_within_group).'**'.str_replace("'","",$hid_booking_type); die;
		if(str_replace("'","",$cbo_within_group)==1)
		{
			if($flag==1 && (str_replace("'","",$hid_booking_type)==1 || str_replace("'","",$hid_booking_type)==6) )
			{
				$rIDBooking=execute_query("update wo_booking_mst set lock_another_process=1 where booking_no ='".$txt_order_no."'",1);
				if($rIDBooking==1 && $flag==1) $flag=1; else $flag=0;
			}
		}
		//echo "10**".$rID."**".$rID2."**".$rID3; die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_order_no);;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_order_no);;
			}
		}
		else if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_order_no);;
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_order_no);;
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
		
		
		//echo "10**";
		 //echo "select d.order_id,b.rate  from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_inbound_bill_dtls d where a.embellishment_job=b.job_no_mst and b.id=c.mst_id and c.id=d.color_size_id and a.entry_form=204 and d.process_id=13  and d.entry_form=395 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and  b.mst_id=$update_id group by d.order_id,b.rate order by d.order_id ASC"; die;
		
		
		$bill_qry_result=sql_select( "select d.order_id,b.rate  from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_inbound_bill_dtls d where a.embellishment_job=b.job_no_mst and b.id=c.mst_id and c.id=d.color_size_id and a.entry_form=204 and d.process_id=13  and d.entry_form=395 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and  b.mst_id=$update_id group by d.order_id,b.rate order by d.order_id ASC");
		$bill_arr=array();
		foreach($qry_result as $row)
		{
			$bill_arr[$row[csf('order_id')]]['rate']=$row[csf('rate')];
			//$bill_number.=$row[csf('sys_no')].',';
		}
		
		
		$within_group	=str_replace("'",'',$cbo_within_group);
		if($within_group==2)
		{
			$sql_up = "SELECT id,order_quantity  from subcon_ord_dtls where job_no_mst=".$txt_job_no." and status_active=1 and is_deleted=0 order by id ASC";	
			$sql_up_result=sql_select($sql_up);
			$order_data_arr=array();
			foreach($sql_up_result as $row)
			{
				$order_data_arr[$row[csf("id")]]['order_quantity']=$row[csf("order_quantity")]; 
			}

			//echo '10**';
			//print_r($order_data_arr);

			//die;

			$recev_sql =  "select a.sys_no, a.trans_type,a.company_id,a.within_group,a.entry_form,b.id ,b.quantity, b.job_dtls_id,b.job_break_id,a.embl_job_no from sub_material_mst a,sub_material_dtls b where  a.embl_job_no =".$txt_job_no." and a.entry_form=205 and a.status_active=1 and  a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and  a.trans_type=1 and a.entry_form=205 and  a.id=b.mst_id and a.within_group=2
			order by a.id desc";

			$recev_sql_result=sql_select($recev_sql);
			$prev_data_arr=array();
			foreach( $recev_sql_result as $row)
			{
				$prev_data_arr[$row[csf("job_dtls_id")]]['quantity']=$row[csf("quantity")]; 
			}
					
			for($t=1; $t<=$total_row; $t++)
			{
				$txtOrderQuantity		= "txtOrderQuantity_".$t;
				$hdnDtlsUpdateId 		= "hdnDtlsUpdateId_".$t;
				$OrderQuantity			=str_replace("'",'',$$txtOrderQuantity);
				$hdnDtlsUpId			=str_replace("'",'',$$hdnDtlsUpdateId);
				$prevOrderQuantity		=$order_data_arr[$hdnDtlsUpId]['order_quantity'];
				$receive_quantity       =$prev_data_arr[$hdnDtlsUpId]['quantity'];
				if($receive_quantity!="")
				{
					if($OrderQuantity<$prevOrderQuantity)
					{
						$rec_number=return_field_value( "sys_no", "sub_material_mst"," embl_job_no=$txt_job_no and status_active=1 and is_deleted=0 and trans_type=1 and entry_form=205");
						if($rec_number)
						{
							echo "emblRec**".str_replace("'","",$txt_job_no)."**".$rec_number;
							disconnect($con); die;
						}
					}
					else
					{
						if($OrderQuantity<$receive_quantity)
						{
						
							if($OrderQuantity<$prevOrderQuantity)
							{
								$rec_number=return_field_value( "sys_no", "sub_material_mst"," embl_job_no=$txt_job_no and status_active=1 and is_deleted=0 and trans_type=1 and entry_form=205");
								if($rec_number)
								{
									echo "emblRec**".str_replace("'","",$txt_job_no)."**".$rec_number;
									disconnect($con); die;
								}
							}
						}
					}
				}
				//echo "10**".$OrderQuantity."==".$prevOrderQuantity."==".$receive_quantity;
				//echo "10**".$quantity;
			}
		}
		else
		{
			$rec_number=return_field_value( "sys_no", "sub_material_mst"," embl_job_no=$txt_job_no and status_active=1 and is_deleted=0 and trans_type=1 and entry_form=205");
			if($rec_number)
			{
				echo "emblRec**".str_replace("'","",$txt_job_no)."**".$rec_number;
				disconnect($con); die;
			}
			
			$recipe_number=return_field_value( "recipe_no", "pro_recipe_entry_mst"," job_no=$txt_job_no and status_active=1 and is_deleted=0 and entry_form=220");
			if($recipe_number){
				echo "emblRecipe**".str_replace("'","",$txt_job_no)."**".$recipe_number;
				disconnect($con); die;
			}
		}
		//echo "10**".$within_group;
		//die;
		$color_library_arr=return_library_array( "select id,color_name from lib_color  where status_active=1 and is_deleted=0", "id", "color_name"  );
		$size_library_arr=return_library_array( "select id,size_name from lib_size  where status_active=1 and is_deleted=0", "id", "size_name"  );

		$field_array="location_id*within_group*party_id*party_location*currency_id*receive_date*delivery_date*rec_start_date*rec_end_date*order_id*order_no*exchange_rate*remarks*updated_by*update_date";		
		$data_array="".$cbo_location_name."*".$cbo_within_group."*".$cbo_party_name."*".$cbo_party_location."*".$cbo_currency."*".$txt_order_receive_date."*".$txt_delivery_date."*".$txt_rec_start_date."*".$txt_rec_end_date."*".$hid_order_id."*".$txt_order_no."*".$txt_exchange_rate."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		//$field_array2="id, mst_id, job_no_mst, order_id, order_no, buyer_po_id, booking_dtls_id, gmts_item_id, main_process_id, embl_type, body_part, order_quantity, order_uom, rate, amount, smv, delivery_date, wastage, inserted_by, insert_date";
		$field_array2="order_id*order_no*buyer_po_id*booking_dtls_id*gmts_item_id*main_process_id*embl_type*body_part*order_quantity*order_uom*rate*amount*domestic_amount*smv*delivery_date*wastage*buyer_po_no*buyer_style_ref*buyer_buyer*updated_by*update_date";
		
		$field_array3="id, mst_id, order_id, job_no_mst, book_con_dtls_id, item_id, body_part, embellishment_type, description, color_id, size_id, qnty, rate, amount,cutting_no,domestic_amount,fabric_description, inserted_by, insert_date";
		$field_array4="order_id*book_con_dtls_id*item_id*body_part*embellishment_type*description*color_id*size_id*qnty*rate*amount*cutting_no*domestic_amount*fabric_description*updated_by*update_date";
		$field_array5="id, mst_id, job_no_mst, order_id, order_no, buyer_po_id, booking_dtls_id, gmts_item_id, main_process_id, embl_type, body_part, order_quantity, order_uom, rate, amount,domestic_amount, smv, delivery_date, wastage, buyer_po_no, buyer_style_ref, buyer_buyer, inserted_by, insert_date";
		$id1=return_next_id( "id", "subcon_ord_dtls",1) ;
		$id3=return_next_id( "id", "subcon_ord_breakdown", 1) ;
		$add_comma=0;	$flag=""; $hdnBrkDelUpId=""; $new_array_color=array();  $new_array_size=array();
		//echo "10**".$total_row;die;
		for($i=1; $i<=$total_row; $i++)
		{			
			$txtbuyerPoId			= "txtbuyerPoId_".$i;
			$txtbuyerPo				= "txtbuyerPo_".$i;
			$txtstyleRef			= "txtstyleRef_".$i;
			$txtbuyer				= "txtbuyer_".$i;  
			$cboGmtsItem			= "cboGmtsItem_".$i;
			$cboProcessName			= "cboProcessName_".$i;
			$cboembtype				= "cboembtype_".$i;
			$cboBodyPart			= "cboBodyPart_".$i;
			$txtOrderQuantity		= "txtOrderQuantity_".$i;
			$cboUom 				= "cboUom_".$i;
			$txtRate 				= "txtRate_".$i;
			$txtAmount 				= "txtAmount_".$i;	
			$txtdomisticamount      ="txtdomisticamount_".$i;		
			$txtSmv 				= "txtSmv_".$i;
			$txtOrderDeliveryDate 	= "txtOrderDeliveryDate_".$i;
			$txtWastage 			= "txtWastage_".$i;
			$hdnDtlsdata 			= "hdnDtlsdata_".$i;
			$hdnDtlsUpdateId 		= "hdnDtlsUpdateId_".$i;
			$hdnbookingDtlsId 		= "hdnbookingDtlsId_".$i;
			$txtDelBreakId 			= "txtDelBreakId_".$i;
			$hdnDtlsUpId	=str_replace("'",'',$$hdnDtlsUpdateId);
			$hdnBrkDelUpId	.=str_replace("'",'',$$txtDelBreakId).",";
			
			$aa	=str_replace("'",'',$$hdnDtlsUpdateId);
			$prev_rate=$bill_arr[$aa]['rate'];
			if($prev_rate!='')
			{
				if($$txtRate != $prev_rate)
				{
					echo "emblBill**".str_replace("'","",$txt_job_no);
					//echo "emblRecQty**".str_replace("'","",$rcv_qty_pcs)."**".$qty;
					disconnect($con); die;
				}
			}
			//echo "10**".$$txtDelBreakId; 
			if($db_type==0)
			{
				$orderDeliveryDate=change_date_format(str_replace("'",'',$$txtOrderDeliveryDate),'yyyy-mm-dd');
			}
			else
			{
				$orderDeliveryDate=change_date_format(str_replace("'",'',$$txtOrderDeliveryDate), "", "",1);
			}
			if(str_replace("'",'',$$txtbuyerPoId)=="") $txtbuyerPoId=0; else $txtbuyerPoId=str_replace("'",'',$$txtbuyerPoId);
			if(str_replace("'",'',$$hdnDtlsUpdateId)!="")
			{
				$data_array2[$hdnDtlsUpId]=explode("*",("".$hid_order_id."*".$txt_order_no."*'".$txtbuyerPoId."'*".$$hdnbookingDtlsId."*".$$cboGmtsItem."*".$$cboProcessName."*".$$cboembtype."*".$$cboBodyPart."*".str_replace(",",'',$$txtOrderQuantity)."*".$$cboUom."*".$$txtRate."*".str_replace(",",'',$$txtAmount)."*".str_replace(",",'',$$txtdomisticamount)."*".$$txtSmv."*'".$orderDeliveryDate."'*".$$txtWastage."*".$$txtbuyerPo."*".$$txtstyleRef."*".$$txtbuyer."*".$user_id."*'".$pc_date_time."'"));
				$hdn_dtls_id_arr[]=str_replace("'",'',$$hdnDtlsUpdateId);
			}
			else
			{
				$hdnDtlsUpId=$id1;
				if ($add_commaa!=0) $data_array5 .=","; $add_comma=0;
				$data_array5 .="(".$id1.",".$update_id.",".$txt_job_no.",".$hid_order_id.",".$txt_order_no.",'".$txtbuyerPoId."',".$$hdnbookingDtlsId.",".$$cboGmtsItem.",".$$cboProcessName.",".$$cboembtype.",".$$cboBodyPart.",".str_replace(",",'',$$txtOrderQuantity).",".$$cboUom.",".$$txtRate.",".str_replace(",",'',$$txtAmount).",".str_replace(",",'',$$txtdomisticamount).",".$$txtSmv.",'".$orderDeliveryDate."',".$$txtWastage.",".$$txtbuyerPo.",".$$txtstyleRef.",".$$txtbuyer.",'".$user_id."','".$pc_date_time."')";
				$id1++;
			}
			//echo "10**".$$hdnDtlsdata;
			$dtls_data=explode("__",str_replace("'",'',$$hdnDtlsdata));
			//echo "10**";//.$total_row; die;
			/*print_r($dtls_data);
			*/
			for($j=0; $j<count($dtls_data); $j++)
			{
				$exdata=explode("_",$dtls_data[$j]);
				//echo "10**";
				//print_r($exdata);
				
				$description="'".$exdata[0]."'";
				$colorname="'".$exdata[1]."'";
				$sizename="'".$exdata[2]."'";
				$qty="'".str_replace(",",'',$exdata[3])."'";
				$rate="'".str_replace(",",'',$exdata[4])."'";
				$amount="'".str_replace(",",'',$exdata[5])."'";
				$book_con_dtls_id="'".$exdata[6]."'";
				$dtlsup_id="'".$exdata[7]."'";
				$breakId=$exdata[7];
				$cuttingno="'".str_replace(",",'',$exdata[8])."'";
				$fabric_description="'".$exdata[9]."'";
				$domistic_amount=str_replace(",",'',$exdata[5])*str_replace("'",'',$txt_exchange_rate);

				if (str_replace("'", "", trim($colorname)) != "") {
					if (!in_array(str_replace("'", "", trim($colorname)),$new_array_color)){
						$color_id = return_id( str_replace("'", "", trim($colorname)), $color_library_arr, "lib_color", "id,color_name","204");
						$new_array_color[$color_id]=str_replace("'", "", trim($colorname));
					}
					else $color_id =  array_search(str_replace("'", "", trim($colorname)), $new_array_color);
				} else $color_id = 0;

				if (str_replace("'", "", trim($sizename)) != "") {
					if (!in_array(str_replace("'", "", trim($sizename)),$new_array_size)){
						$size_id = return_id( str_replace("'", "", trim($sizename)), $size_library_arr, "lib_size", "id,size_name","204");
						$new_array_size[$size_id]=str_replace("'", "", trim($sizename));
					}
					else $size_id =  array_search(str_replace("'", "", trim($sizename)), $new_array_size);
				} else $size_id = 0;
				
				
				if($breakId==0)
				{
					if ($add_commadtls!=0) $data_array3 .=",";
					$data_array3.="(".$id3.",".$hdnDtlsUpId.",".$hid_order_id.",".$txt_job_no.",".$book_con_dtls_id.",".$$cboGmtsItem.",".$$cboBodyPart.",".$$cboembtype.",".$description.",'".$color_id."','".$size_id."',".$qty.",".$rate.",".$amount.",".$cuttingno.",".$domistic_amount.",".$fabric_description.",'".$user_id."','".$pc_date_time."')";
					$id3=$id3+1; $add_commadtls++;
				}
				else if($breakId!=0)
				{
					$data_array4[$breakId]=explode("*",("".$hid_order_id."*".$book_con_dtls_id."*".$$cboGmtsItem."*".$$cboBodyPart."*".$$cboembtype."*".$description."*'".$color_id."'*'".$size_id."'*".$qty."*".$rate."*".$amount."*".$cuttingno."*".$domistic_amount."*".$fabric_description."*".$user_id."*'".$pc_date_time."'"));
					$hdn_break_id_arr[]		=$breakId;
				}
			}
			//die;			
			//echo "10**".change_date_format(str_replace("'",'',$$txtOrderDeliveryDate),'yyyy-mm-dd')."nazim"; die;					
		}
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
		
		if($txt_deleted_id_dtls!="" && $flag==1)
		{
			$field_array_status="updated_by*update_date*status_active*is_deleted";
			$data_array_status=$user_id."*'".$pc_date_time."'*0*1";

			$rID3=sql_multirow_update("subcon_ord_dtls",$field_array_status,$data_array_status,"id",$txt_deleted_id_dtls,0);
			if($flag==1)
			{
				if($rID3) $flag=1; else $flag=0; 
			} 

			//$rID4=execute_query( "delete from subcon_ord_breakdown where mst_id in ( $txt_deleted_id_dtls)",0);
			$rID4=execute_query("UPDATE subcon_ord_breakdown SET updated_by=$user_id, update_date='$pc_date_time', status_active=0, is_deleted=1 WHERE  mst_id in ($txt_deleted_id_dtls)");
			if($flag==1)
			{
				if($rID4) $flag=1; else $flag=0; 
			} 
		}
		//$rID2=sql_update("subcon_ord_dtls",$field_array2,$data_array2,"id",$update_id2,0); 
		//if($rID2) $flag=1; else $flag=0;
			
		$id_break=implode(',',$hiddenTblIdBreak);
		//print_r ($hiddenTblIdBreak);die;
		/*if($data_array4!="")
		{
			$rID5=execute_query(bulk_update_sql_statement( "subcon_ord_breakdown", "id",$field_array4,$data_array4,$hdn_break_id_arr),1);
			if($rID5==1 && $flag==1) $flag=1; else $flag=0;
		}
		*/
		
		
		 if($data_array4!="")
		{
 			$data_array_update_trans_chunk = array_chunk($data_array4, 50, true);
			$transId_up_arr = array_chunk($hdn_break_id_arr, 50, true);
			$count_up_trans = count($transId_up_arr);
			for ($m = 0; $m < $count_up_trans; $m++) 
			{
    			 $rID5 = execute_query(bulk_update_sql_statement("subcon_ord_breakdown", "id", $field_array4, $data_array_update_trans_chunk[$m], array_values($transId_up_arr[$m])), 1);
				
 				if ($rID5 != "1") 
				{
					oci_rollback($con);
					echo "6**0**1**".bulk_update_sql_statement("subcon_ord_breakdown", "id", $field_array4, $data_array_update_trans_chunk[$m], array_values($transId_up_arr[$m]));
					disconnect($con);
					die;
				} 
			}
 			if($rID5==1 && $flag==1) $flag=1; else $flag=0;
			
 		}
		
		

		if($data_array3!="")
		{
			//echo "10**INSERT INTO subcon_ord_breakdown (".$field_array3.") VALUES ".$data_array3; die;
			$rID6=sql_insert("subcon_ord_breakdown",$field_array3,$data_array3,1);
			if($rID6==1 && $flag==1) $flag=1; else $flag=0;		
		}
		//echo "10**".$delete_ids; die;
		//$deleted_id=str_replace("'",'',$txtDeletedId);
		//echo "10**".$hdnBrkDelUpId; die;
		$delete_ids=explode(",",$hdnBrkDelUpId);
		$all_del_ids="";
		foreach ($delete_ids as $value) 
		{
			if($value)
			{
				if($all_del_ids=="") $all_del_ids.=$value; else $all_del_ids.=','.$value;
			}
		}
		//echo "10**delete from subcon_ord_breakdown where id in ( $all_del_ids)"; die;
		//echo "10**".$all_del_ids; die;
		//$delete_id=chop($delete_ids,",");
		if ($all_del_ids!="")
		{
			//$rID7=execute_query( "delete from subcon_ord_breakdown where id in ( $all_del_ids)",0);
			$rID7=execute_query("UPDATE subcon_ord_breakdown SET updated_by=$user_id, update_date='$pc_date_time', status_active=0, is_deleted=1 WHERE  id in ($all_del_ids)");
			if($rID7==1 && $flag==1) $flag=1; else $flag=0;
		}

		
			
		//==============================================================================================================================================
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no);;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no);;
			}
		}
		else if($db_type==2)
		{  
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no);;
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no);;
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
		
		$deletableSQL = sql_select("SELECT ORDER_ID FROM printing_bundle_receive_dtls WHERE ORDER_ID=$hid_order_id AND ENTRY_FORM=614 AND STATUS_ACTIVE=1 AND IS_DELETED=0");

		$rec_number=return_field_value( "sys_no", "sub_material_mst"," embl_job_no=$txt_job_no and status_active=1 and is_deleted=0 and trans_type=1 and entry_form=205");
		if($rec_number || count($deletableSQL))
		{
			echo "emblRec**".str_replace("'","",$txt_job_no)."**".$rec_number;
			disconnect($con); die;
		}

		/*$rec_number=return_field_value( "sys_no", "sub_material_mst"," embl_job_no=$txt_job_no and status_active=1 and is_deleted=0 and trans_type=1");
		if($rec_number){
			echo "emblRec**".str_replace("'","",$txt_job_no)."**".$rec_number;
			disconnect($con); die;
		}*/

		//if ( $delete_master_info==1 )
		//{

		for($i=1; $i<=$total_row; $i++)
		{
			$hdnDtlsdata 			= "hdnDtlsdata_".$i;
			$hdnDtlsUpdateId 		= "hdnDtlsUpdateId_".$i;
			$aa	=str_replace("'",'',$$hdnDtlsUpdateId);
			$dtls_data=explode("__",str_replace("'",'',$$hdnDtlsdata));
			/*echo "10**";//.$total_row; die;
			print_r($dtls_data);*/
			
			for($j=0; $j<count($dtls_data); $j++)
			{
				$exdata=explode("_",$dtls_data[$j]);
				$dtlsup_id="'".$exdata[7]."'";
				$bb=$exdata[7];
				if($bb!=0)
				{
					$break_ids_all .=$bb.',';
					$hdn_break_id_arr[]		=$bb;
				}
			}
		}
		
		$break_id_all=implode(",",array_unique(explode(",",chop($break_ids_all,','))));

		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		$flag=1;
		$rID=sql_update("subcon_ord_mst",$field_array,$data_array,"id",$update_id,1);  
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		$rID1=sql_update("subcon_ord_dtls",$field_array,$data_array,"mst_id",$update_id,1);
		if($rID1==1 && $flag==1) $flag=1; else $flag=0; 

		//$rID2=execute_query( "delete from subcon_ord_breakdown where id in ($break_id_all) and job_no_mst=$txt_job_no",0);
		$rID2=sql_update("subcon_ord_breakdown",$field_array,$data_array,"job_no_mst",$txt_job_no,1);
		if($rID2==1 && $flag==1) $flag=1; else $flag=0;

		//$rID = sql_delete("subcon_ord_dtls","status_active*is_deleted*updated_by*update_date","0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'",'id',$update_id2,1);
		//$rID2=sql_update("subcon_ord_breakdown",$field_array,$data_array,"job_no_mst",$txt_job_no,1);
		//if($rID2==1 && $flag==1) $flag=1; else $flag=0;  
		
		//$rID2=execute_query( "delete from subcon_ord_breakdown where job_no_mst=$txt_job_no",0);
		//if($rID2==1 && $flag==1) $flag=1; else $flag=0;
		
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
			var cbo_within_group = $('#cbo_within_group').val();
			load_drop_down( 'embellishment_order_entry_controller', company+'_'+cbo_within_group, 'load_drop_down_buyer', 'buyer_td' );
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
<body onLoad="fnc_load_party_popup();">
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
                    <th width="100" id="search_by_td">Embl. Job No</th>
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
                        <?php echo create_drop_down( "cbo_within_group", 100, $yes_no,"", 0, "--  --", $data[3], "fnc_load_party_popup(1,this.value);" ); ?>
                    </td>
                    <td id="buyer_td">
                        <? echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[2], "" );   	 
                        ?>
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
                    <td align="center"><? echo create_drop_down( "cbo_year_selection", 100, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                    </td>
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year_selection').value, 'create_job_search_list_view', 'search_div', 'embellishment_order_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
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
	//echo $search_type; die;
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond=""; $po_cond_2=""; $style_cond_2="";
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num = '$search_str' ";
			else if ($search_by==4) $search_com_cond="and b.buyer_po_no = '$search_str' ";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref = '$search_str' ";
		}
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
	}	


	//else if ($search_by==5) {$search_com_cond=" and e.style_ref_no like '%$search_str%'"; $search_com_cond=" and b.buyer_style_ref = '$search_str'"; }

	//else if ($search_by==4) {$search_com_cond=" and d.po_number like '%$search_str%'"; $search_com_cond=" and b.buyer_po_no = '$search_str'";}




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
	
	
	 

	$po_ids=''; $buyer_po_arr=array();
	if($within_group==1)
	{
		if($db_type==0) $id_cond="group_concat(b.id) as id";
		else if($db_type==2) $id_cond="rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') as id";
		//echo "select $id_cond as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
		if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
		{
			$po_ids = return_field_value("$id_cond", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0", "id");
		}
		//echo $po_ids;
		if($db_type==2 && $po_ids!="") $po_ids = $po_ids->load();
		if ($po_ids!="")
		{
			$po_ids=explode(",",$po_ids);
			$po_idsCond=""; $poIdsCond="";
			//echo count($po_ids); die;
			if($db_type==2 && count($po_ids)>=999)
			{
				$chunk_arr=array_chunk($po_ids,999);
				foreach($chunk_arr as $val)
				{
					$ids=implode(",",$val);
					if($po_idsCond=="")
					{
						$po_idsCond.=" and ( b.buyer_po_id in ( $ids) ";
						$poIdsCond.=" and ( b.id in ( $ids) ";
					}
					else
					{
						$po_idsCond.=" or  b.buyer_po_id in ( $ids) ";
						$poIdsCond.=" or  b.id in ( $ids) ";
					}
				}
				$po_idsCond.=")";
				$poIdsCond.=")";
			}
			else
			{
				$ids=implode(",",$po_ids);
				$po_idsCond.=" and b.buyer_po_id in ($ids) ";
				$poIdsCond.=" and b.id in ($ids) ";
			}
		}
		else if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
		{
			echo "Not Found"; die;
			//$po_idsCond.=" and b.buyer_po_id in ($ids) ";
		}
		
		$po_sql ="SELECT a.style_ref_no,a.job_no_prefix_num, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $poIdsCond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
		$po_sql_res=sql_select($po_sql);
		foreach ($po_sql_res as $row)
		{
			$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
			$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
			$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no_prefix_num")];
		}
		unset($po_sql_res);
	}
	
	/*if($db_type==0) $id_cond="group_concat(b.id)";
	else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	//echo "select $id_cond as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
	if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
	{
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted 0", "id");
	}
	echo $po_ids;
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
			$buyer_po_arr[$row[csf("id")]]['buyer']=$row[csf("po_number")];
		}
		unset($po_sql_res);
	}*/
	$color_arr=return_library_array( "SELECT id, color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
	if($db_type==0)
	{
		$ins_year_cond="year(a.insert_date)";
		$color_id_str="group_concat(c.color_id)";
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
		//$color_id_str="listagg(c.color_id,',') within group (order by c.color_id)";
		$color_id_str="rtrim(xmlagg(xmlelement(e,c.color_id,',').extract('//text()') order by c.id).GetClobVal(),',')";
		if($within_group==1)
		{
			$buyer_po_id_str=",rtrim(xmlagg(xmlelement(e,b.buyer_po_id,',').extract('//text()') order by b.id).GetClobVal(),',') as buyer_po_id";
		}
		else
		{
			$buyer_po_no_str=",rtrim(xmlagg(xmlelement(e,b.buyer_po_no,',').extract('//text()') order by b.id).GetClobVal(),',') as buyer_po_no";
			$buyer_po_style_str=",rtrim(xmlagg(xmlelement(e,b.buyer_style_ref,',').extract('//text()') order by b.id).GetClobVal(),',') as buyer_style";
		}
	}
	/*else if($db_type==2)
	{
		$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
		$color_id_str="listagg(c.color_id,',') within group (order by c.color_id)";
		if($within_group==1)
		{
			$buyer_po_id_str=",listagg(b.buyer_po_id,',') within group (order by b.buyer_po_id)  as buyer_po_id";
		}
		else
		{
			$buyer_po_no_str=",listagg(b.buyer_po_no,',') within group (order by b.id) as buyer_po_no";
			$buyer_po_style_str=",listagg(b.buyer_style_ref,',') within group (order by b.id) as buyer_style";
		}
	}*/
	/*$sql= "SELECT a.id, a.embellishment_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date, $color_id_str as color_id $buyer_po_id_str $buyer_po_no_str $buyer_po_style_str
	from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
	where a.entry_form=204 and a.embellishment_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_rcv_date $company $buyer $withinGroup $search_com_cond $po_idsCond $withinGroup and b.id=c.mst_id  
	group by a.id, a.embellishment_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date
	order by a.id DESC";*/
		
	
	$sql= "SELECT a.id, a.embellishment_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date, $color_id_str as color_id $buyer_po_id_str $buyer_po_no_str $buyer_po_style_str,a.exchange_rate,a.booking_type
	from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
	where a.entry_form=204 and a.embellishment_job=b.job_no_mst and a.id=b.mst_id and b.id=c.mst_id  and  b.job_no_mst=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $party_id_cond $order_rcv_date $company $buyer $withinGroup $search_com_cond $po_idsCond $withinGroup $year_cond and b.id=c.mst_id  
	group by a.id, a.embellishment_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date,a.exchange_rate,a.booking_type
	order by a.id DESC";
	// echo $sql;
	$data_array=sql_select($sql);
	?>
     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="885" >
        <thead>
            <th width="30">SL</th>
            <th width="60">Job No</th>
            <th width="60">Year</th>
            <th width="120">W/O No</th>
			<?
				if($within_group==1)
				{
					?>
						<th width="60">Buyer Job</th>
					<?
				}
			?>
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
				$color_id = $row[csf('color_id')]->load();
				//$excolor_id=array_unique(explode(",",$row[csf('color_id')]));
				$excolor_id=array_unique(explode(",",$color_id));
				//$excolor_id=array_unique(explode(",",$row[csf('color_id')]));
				$color_name="";	
				foreach ($excolor_id as $color_id)
				{
					if($color_name=="") $color_name=$color_arr[$color_id]; else $color_name.=','.$color_arr[$color_id];
				}

				if($within_group==1)
				{
					$buyer_po=""; $buyer_style=""; $buyer_job="";
					if($db_type==2 && $row[csf('buyer_po_id')]!="") {
						$buyer_po_id = $row[csf('buyer_po_id')]->load();
						$buyer_po_id=explode(",",$buyer_po_id);
					}else{
						$buyer_po_id=explode(",",$row[csf('buyer_po_id')]);
					}
					
					
					foreach($buyer_po_id as $po_id)
					{
						if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
						if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
						if($buyer_job=="") $buyer_job=$buyer_po_arr[$po_id]['job']; else $buyer_job.=','.$buyer_po_arr[$po_id]['job'];
					}
					$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
					$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
					$buyer_job=implode(",",array_unique(explode(",",$buyer_job)));
					
				}
				else
				{
					if($db_type==2 ) {
						$buyer_po_no = $row[csf('buyer_po_no')]->load();
						$buyer_style = $row[csf('buyer_style')]->load();
					}else{
						$buyer_po_no = $row[csf('buyer_po_no')];
						$buyer_style = $row[csf('buyer_style')];
					}
					
					$buyer_po=implode(",",array_unique(explode(",",$buyer_po_no)));
					$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
				}
				/*if($within_group==1)
				{
					$buyer_po=""; $buyer_style="";
					$buyer_po_id=explode(",",$row[csf('buyer_po_id')]);
					foreach($buyer_po_id as $po_id)
					{
						if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
						if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
					}
					$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
					$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
				}
				else
				{
					$buyer_po=implode(",",array_unique(explode(",",$row[csf('buyer_po_no')])));
					$buyer_style=implode(",",array_unique(explode(",",$row[csf('buyer_style')])));
				}*/
				if($row[csf('booking_type')]=='') $row[csf('booking_type')]=1;
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$row[csf('embellishment_job')].'_'.$row[csf('exchange_rate')].'_'.$row[csf('booking_type')]; ?>")' style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                    <td width="60" style="text-align:center;"><? echo $row[csf('year')]; ?></td>
                    <td width="120"><? echo $row[csf('order_no')]; ?></td>
					<?
						if($within_group==1)
						{
							?>
								<td width="60" style="word-break:break-all"><? echo $buyer_job; ?></td>
							<?
						}
					?>
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
 
if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "SELECT id,wo_type, embellishment_job, company_id, location_id, party_id, currency_id, party_location, delivery_date, rec_start_date, rec_end_date, receive_date, within_group, party_location, order_id, order_no,exchange_rate,booking_type, remarks from subcon_ord_mst where id='$data' and status_active=1 and  is_deleted=0" );
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_job_no').value 			= '".$row[csf("embellishment_job")]."';\n";  
		echo "document.getElementById('cbo_company_name').value 	= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_within_group').value 	= '".$row[csf("within_group")]."';\n";  
		echo "$('#cbo_company_name').attr('disabled','true')".";\n";
		echo "fnc_load_party(1,".$row[csf("within_group")].");\n";	
		//echo "load_drop_down( 'requires/embellishment_order_entry_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_location', 'location_td' );\n";	
		echo "document.getElementById('cbo_location_name').value 		= '".$row[csf("location_id")]."';\n";
		echo "document.getElementById('cbo_wo_type').value			= '".$row[csf("wo_type")]."';\n";
		//echo "load_drop_down( 'requires/embellishment_order_entry_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_buyer', 'buyer_td' );\n";
		echo "document.getElementById('cbo_party_name').value			= '".$row[csf("party_id")]."';\n";
 		echo "document.getElementById('txt_exchange_rate').value				= '".$row[csf("exchange_rate")]."';\n";
		echo "fnc_load_party(2,".$row[csf("within_group")].");\n";	
		echo "document.getElementById('cbo_currency').value				= '".$row[csf("currency_id")]."';\n"; 
		echo "document.getElementById('cbo_party_location').value		= '".$row[csf("party_location")]."';\n";	
		echo "document.getElementById('txt_order_receive_date').value	= '".change_date_format($row[csf("receive_date")])."';\n"; 
		echo "document.getElementById('txt_delivery_date').value		= '".change_date_format($row[csf("delivery_date")])."';\n"; 
		echo "document.getElementById('txt_rec_start_date').value		= '".change_date_format($row[csf("rec_start_date")])."';\n"; 
		echo "document.getElementById('txt_rec_end_date').value			= '".change_date_format($row[csf("rec_end_date")])."';\n"; 
		echo "document.getElementById('hid_order_id').value          	= '".$row[csf("order_id")]."';\n";
		echo "document.getElementById('txt_order_no').value         	= '".$row[csf("order_no")]."';\n";
		echo "document.getElementById('hid_booking_type').value         = '".$row[csf("booking_type")]."';\n";

		echo "document.getElementById('txt_remarks').value         = '".$row[csf("remarks")]."';\n";

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
		load_drop_down( 'embellishment_order_entry_controller', company+'_'+1+'_'+party_name, 'load_drop_down_buyer', 'buyer_td' );
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
                        <th width="100">IR/IB</th>
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
                    <td><input name="irib" id="irib" class="text_boxes" style="width:90px"></td>
                    <td><? echo create_drop_down( "cbo_year_selection", 60, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
                    <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From"></td>
                    <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To"></td> 
                    <td>
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_search_category').value+'_'+<? echo $company;?>+'_'+document.getElementById('cbo_search_type').value+'_'+<? echo $cbo_within_group; ?>+'_'+document.getElementById('irib').value, 'create_booking_search_list_view', 'search_div', 'embellishment_order_entry_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
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
	$cbo_within_group=$data[8];
	$irib=$data[9];
	

	if(!empty($irib)){ $irib_cond = " and d.grouping='$irib' ";}
	
	if ($data[6]!=0) $party_cond=" and a.supplier_id='$data[6]'"; else { echo "Please Select Company First."; die; }
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Party First."; die; }
	//if ($data[0]!=0 && ) $buyer=" and buyer_id='$data[1]'"; else  $buyer="";//{ echo "Please Select Buyer First."; die; }
	if($db_type==0) 
	{
		$year_cond=" and YEAR(a.insert_date)=$data[4]";
		$year_cond2=" and YEAR(c.insert_date)=$data[4]";
	} 
	else
	{
		$year_cond=" and to_char(a.insert_date,'YYYY')=$data[4]"; 
		$year_cond2=" and to_char(c.insert_date,'YYYY')=$data[4]"; 
	}
	$master_company=$data[0];

	$woorder_cond=""; $job_cond=""; $style_cond=""; $po_cond="";
	if($data[5]==1)
	{
		if ($data[1]!="")
		{
			if ($search_type==1) $woorder_cond=" and a.booking_no_prefix_num = '$data[1]' ";
			if ($search_type==2) $job_cond=" and a.job_no_prefix_num = '$data[1]' ";
			if ($search_type==3) $style_cond=" and a.style_ref_no = '$data[1]' ";
			if ($search_type==4) $po_cond=" and b.po_number = '$data[1]' ";
		}
	}
	if($data[5]==2)
	{
		if ($data[1]!="")
		{
			if ($search_type==1) $woorder_cond=" and a.booking_no_prefix_num like '$data[1]%'";
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
	
	if($data[1]!='')
	{
		if ($search_type==1) $woorderCond=" and c.booking_no_prefix_num = '$data[1]' ";
		if ($search_type==2) $jobCond=" and a.job_no_prefix_num = '$data[1]' ";
		if ($search_type==3) $styleCond=" and a.style_ref_no = '$data[1]' ";
		if ($search_type==4) $poCond=" and b.po_number = '$data[1]' ";
		//embellishment_job
		/*$attached_po_sql ="SELECT e.embellishment_job from wo_po_details_master a, wo_po_break_down b, wo_booking_mst c, wo_booking_dtls d, subcon_ord_mst e  where a.job_no=b.job_no_mst and b.id=d.po_break_down_id and c.booking_no=d.booking_no and c.id=e.order_id and c.booking_no=e.order_no and c.lock_another_process=1 and c.booking_type=6 and c.company_id='$data[0]' $jobCond $styleCond $poCond $woorderCond $year_cond2 and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and c.status_active =1 and c.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and e.status_active =1 and e.is_deleted =0"; //die;*/
		$attached_po_sql ="SELECT e.embellishment_job from wo_po_details_master a, wo_po_break_down b, wo_booking_mst c, wo_booking_dtls d, subcon_ord_mst e  where a.job_no=b.job_no_mst and b.id=d.po_break_down_id and c.booking_no=d.booking_no and c.id=e.order_id and c.booking_no=e.order_no and c.booking_type=6 and c.company_id='$data[0]' $jobCond $styleCond $poCond $woorderCond $year_cond2 and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and c.status_active =1 and c.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and e.status_active =1 and e.is_deleted =0"; //die;
		$attached_po_res=sql_select($attached_po_sql); 
		if(count($attached_po_res)<>0){
			$printJOBs='';
			foreach ($attached_po_res as $row){
				 $printJOBs .= $row[csf("embellishment_job")].',';
			}
			//echo $printJOBs; die;
			$printJOBs=implode(",",array_unique(explode(",",chop($printJOBs,','))));
			echo "Already has a Job no against this WO. <br> Job No: $printJOBs"; die;
		}
		
	}

	if($db_type==0) $id_cond="group_concat(b.id)";
	else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	if(($job_cond!="" && $search_type==2) || ($style_cond!="" && $search_type==3)|| ($po_cond!="" && $search_type==4))
	{
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0", "id");
		if ($po_ids=="")
		{
			$po_idsCond="";
			echo "Not Found."; die;
		}
	}
	
	if ($po_ids!="") $po_idsCond=" and b.po_break_down_id in ($po_ids)"; else $po_idsCond="";
	$buyer_po_arr=array();
	$po_sql ="SELECT a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
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
	$pre_sql ="SELECT id, emb_name, emb_type, body_part_id  from wo_pre_cost_embe_cost_dtls where emb_name=1 and status_active=1 and is_deleted=0";
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
		$gmts_item_id_cond="listagg(b.gmt_item_id,',') within group (order by b.gmt_item_id)";
		$gmts_item_cond="listagg(b.gmt_item,',') within group (order by b.gmt_item)";
		$po_id_cond="listagg(b.po_break_down_id,',') within group (order by b.po_break_down_id)";
	} 
	
	$buyer_arr=return_library_array( "SELECT id, buyer_name from lib_buyer where status_active =1 and is_deleted=0",'id','buyer_name');
	$comp=return_library_array( "SELECT id, company_name from lib_company where status_active =1 and is_deleted=0",'id','company_name');
	$arr=array (0=>$comp,1=>$buyer_arr);

	$approved_cond="";
	if ($cbo_within_group==1)
	{
		if ($db_type==2) $app_nes_setup_date=change_date_format(date('d-m-Y'), "", "",1);
		else if ($db_type==0) $app_nes_setup_date=change_date_format(date('d-m-Y'),'yyyy-mm-dd');
		$approval_status="select approval_need, allow_partial from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '$app_nes_setup_date' and company_id='$data[0]')) and page_id=51 and status_active=1 and is_deleted=0";
		$app_need_setup=sql_select($approval_status);
		$approval_need=$app_need_setup[0][csf("approval_need")];
		
		if ($approval_need ==1) $approved_cond=" and a.is_approved in(1,3)";
	}
	
	if($cbo_within_group == 1){
		$sql= "SELECT $wo_year as year, d.grouping, a.id,a.entry_form, a.booking_type, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.booking_date, a.currency_id, $pre_cost_trims_cond as pre_cost_trims_id, $gmts_item_cond as gmts_item, $po_id_cond as po_id, 1 as wo_type from  wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_embe_cost_dtls c, wo_po_break_down d where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and d.id=b.po_break_down_id and a.booking_type=6 and a.status_active=1 and c.emb_name=1 and a.lock_another_process!=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $booking_date $company $party_cond $woorder_cond $year_cond $po_idsCond $approved_cond $irib_cond group by a.insert_date, a.id,a.entry_form, a.booking_type, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.booking_date, a.currency_id, d.grouping";
	}else{
		$sql = "SELECT $wo_year as year, null as GROUPING, a.id,a.entry_form_id, a.booking_type, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.booking_date, a.currency_id, '0' as pre_cost_trims_id, $gmts_item_id_cond as gmts_item, '0' as po_id, 2 as wo_type  from wo_non_ord_embl_booking_mst a, wo_non_ord_embl_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=6 and a.entry_form_id=399 and a.status_active=1 and b.status_active=1 $booking_date $company $party_cond $woorder_cond $year_cond group by a.insert_date, a.id,a.entry_form_id, a.booking_type, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.booking_date, a.currency_id";
	}
		
	//echo $sql; exit();
	$data_array=sql_select($sql);
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="1240" >
        <thead>
            <th width="30">SL</th>
            <th width="40">Year</th>
            <th width="100">W/O No</th>
            <th width="60">W/O Date</th>
            <th width="100">Buyer</th>
            <th width="120">Buyer Po</th>
            <th width="120">Buyer Style</th>
            <th width="100">IR/IB</th>
            <th width="120">Buyer Job</th>
            <th width="130">Gmts. Item</th>
            <th width="120">Body Part</th>
            <th width="100">Embl. Type</th>
            <th>Booking Type</th>
        </thead>
    </table>
    <div style="width:1240px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1220" class="rpt_table" id="list_view">
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
				
				$expre_cost_trims_id=array_unique(explode(",",$row[csf('pre_cost_trims_id')]));
				$body_part_name=""; $embl_name=""; $embl_type="";
				foreach ($expre_cost_trims_id as $pre_cost_id)
				{
					if($body_part_name=="") $body_part_name=$body_part[$pre_cost_trims_arr[$pre_cost_id]['body_part_id']]; else $body_part_name.=','.$body_part[$pre_cost_trims_arr[$pre_cost_id]['body_part_id']];
					
					if($embl_name=="") $embl_name=$emblishment_name_array[$pre_cost_trims_arr[$pre_cost_id]['emb_name']]; else $embl_name.=','.$emblishment_name_array[$pre_cost_trims_arr[$pre_cost_id]['emb_name']];
					
					if($pre_cost_trims_arr[$pre_cost_id]['emb_name']==1) $emb_type=$emblishment_print_type;
					else if($pre_cost_trims_arr[$pre_cost_id]['emb_name']==2) $emb_type=$emblishment_embroy_type;
					else if($pre_cost_trims_arr[$pre_cost_id]['emb_name']==3) $emb_type=$emblishment_wash_type;
					else if($pre_cost_trims_arr[$pre_cost_id]['emb_name']==4) $emb_type=$emblishment_spwork_type;
					else if($pre_cost_trims_arr[$pre_cost_id]['emb_name']==5) $emb_type=$emblishment_gmts_type;
					
					if($embl_type=="") $embl_type=$emb_type[$pre_cost_trims_arr[$pre_cost_id]['emb_type']]; else $embl_type.=','.$emb_type[$pre_cost_trims_arr[$pre_cost_id]['emb_type']]; 
				}
				
				$body_part_name=implode(", ",array_unique(explode(",",$body_part_name)));
				$embl_name=implode(", ",array_unique(explode(",",$embl_name)));
				$embl_type=implode(", ",array_unique(explode(",",$embl_type)));
				
				$gmts_item_name="";
				$exgmts_item_id=explode(",",$row[csf('gmts_item')]);
				foreach($exgmts_item_id as $item_id)
				{
					if($gmts_item_name=="") $gmts_item_name=$garments_item[$item_id]; else $gmts_item_name.=','.$garments_item[$item_id];
				}
				$gmts_item_name=implode(", ",array_unique(explode(",",$gmts_item_name)));
				if($row[csf('wo_type')]==1) $booking_type='With Order'; else $booking_type='Without Order';
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$row[csf('booking_no')].'_'.$row[csf('currency_id')].'_'.$row[csf('booking_type')].'_'.$row[csf('wo_type')]; ?>")' style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="40" align="center"><? echo $row[csf('year')]; ?></td>
                    <td width="100" align="center"><? echo $row[csf('booking_no')]; ?></td>
                    <td width="60"><? echo change_date_format($row[csf('booking_date')]); ?></td>
                    <td width="100" style="word-break:break-all"><? echo $buyer_name; ?></td>
                    <td width="120" style="word-break:break-all"><? echo $po_no; ?></td>
                    <td width="120" style="word-break:break-all"><? echo $buyer_style; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $row[csf('grouping')]; ?></td>
                    <td width="120" style="word-break:break-all"><? echo $buyer_job; ?></td>
                    
                    <td width="130" style="word-break:break-all"><? echo $gmts_item_name; ?></td>
                    <td width="120" style="word-break:break-all"><? echo $body_part_name; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $embl_type; ?></td>
                    <td style="word-break:break-all"><? echo $booking_type; ?></td>
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
	$nameArray=sql_select( "SELECT id, booking_type, booking_no, company_id, buyer_id, job_no, booking_date,currency_id from  wo_booking_mst where booking_type=6 and status_active=1 and is_deleted=0 and id='$data[0]'" );
	//$sql= "select to_char(insert_date,'YYYY') as year, id, booking_type, booking_no, company_id, buyer_id, job_no, booking_date from  wo_booking_mst where booking_type=6 and status_active=1 and is_deleted=0 $booking_date $company $order_cond order by booking_no";
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_order_no').value 	= '".$row[csf("booking_no")]."';\n";  
		echo "document.getElementById('cbo_party_name').value 	= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('hid_order_id').value		= '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_currency').value		= '".$row[csf("currency_id")]."';\n";
		//if($row[csf("booking_date")]=="0000-00-00" || $row[csf("booking_date")]=="") $booking_date=""; else $booking_date=change_date_format($row[csf("booking_date")]);   
		//echo "$('#cbo_company_name').attr('disabled','true')".";\n";
		//echo "load_drop_down( 'requires/embellishment_order_entry_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_location', 'location_td' );\n";	
		//echo "document.getElementById('cbo_location_name').value 	= '".$row[csf("location_id")]."';\n";
		//echo "load_drop_down( 'requires/embellishment_order_entry_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_buyer', 'buyer_td' );\n";

		//echo "document.getElementById('txt_process_id').value		= '".$row[csf("service_type")]."';\n"; 
		//echo "document.getElementById('cbo_currency').value			= '".$row[csf("currency_id")]."';\n"; 
	    //echo "document.getElementById('update_id').value          	= '".$row[csf("id")]."';\n";	
		//echo "set_button_status(0,'".$_SESSION['page_permission']."', 'fnc_job_order_entry',1);\n";	
	}
	exit();	
}

if( $action=='order_dtls_list_view' ) 
{
	
//2_OG-EB-21-00030_1_5927_6_1 
	
	$data=explode('_',$data);
	//echo $data[2]; die;
	$update_id=$data[3];
	$exchange_rate=$data[6];
	$color_library=return_library_array( "SELECT id,color_name from lib_color where status_active =1 and is_deleted=0", "id", "color_name" );
	$size_arr=return_library_array( "SELECT id,size_name from lib_size where status_active =1 and is_deleted=0",'id','size_name');
	$tblRow=0;
	
	
	
	//echo $data[2]; die;
	
	 
	
	//$prev_pi_qnty_arr_dtls=return_library_array("select a.id, a.grey_qnty_by_uom-sum(b.quantity) as balance_qty  from fabric_sales_order_dtls a ,com_export_pi_dtls b where  a.id=work_order_dtls_id and b.status_active=1 and a.status_active=1 and a.is_deleted=0 group by a.id , a.finish_qty ",'id','balance_qty');
	//print_r($prev_pi_qnty_arr_dtls);
	//1_10549_FAL-EB-18-00022
	
	
	//2+'_'+txt_order_no+'_'+within_group+'_'+$("#update_id").val()+'_'+booking_type+'_'+wotype
	
	if($data[0]==2)// update type 2
	{
 		$qry_result=sql_select( "SELECT a.id, a.mst_id, a.order_id, a.job_no_mst, a.book_con_dtls_id, a.item_id, a.body_part, a.embellishment_type, a.description, a.color_id, a.size_id, a.qnty, a.rate, a.amount,a.cutting_no,fabric_description from subcon_ord_breakdown a, subcon_ord_dtls b where a.job_no_mst=b.job_no_mst and a.mst_id=b.id and b.mst_id=$update_id  and a.status_active=1 and a.is_deleted=0");
		// echo "SELECT a.id, a.mst_id, a.order_id, a.job_no_mst, a.book_con_dtls_id, a.item_id, a.body_part, a.embellishment_type, a.description, a.color_id, a.size_id, a.qnty, a.rate, a.amount,a.cutting_no,fabric_description from subcon_ord_breakdown a, subcon_ord_dtls b where a.job_no_mst=b.job_no_mst and a.mst_id=b.id and b.mst_id=$update_id  and a.status_active=1 and a.is_deleted=0";
		$data_dreak_arr=array(); $data_dreak=''; $add_comma=0; $k=1;
		foreach ($qry_result as $row)
		{
			if($row[csf('description')]=="") $row[csf('description')]=0;
			if($row[csf('fabric_description')]=="") $row[csf('fabric_description')]=0;
			if($row[csf('color_id')]=="") $row[csf('color_id')]=0;
			if($row[csf('size_id')]=="") $row[csf('size_id')]=0;
			if($row[csf('qnty')]=="") $row[csf('qnty')]=0;
			if($row[csf('rate')]=="") $row[csf('rate')]=0;
			if($row[csf('amount')]=="") $row[csf('amount')]=0;
			if($row[csf('cutting_no')]=="") $row[csf('cutting_no')]=0;
			if($row[csf('book_con_dtls_id')]=="") $row[csf('book_con_dtls_id')]=0;
			if(!in_array($row[csf('mst_id')],$temp_arr_mst_id))
			{
				$temp_arr_mst_id[]=$row[csf('mst_id')];
				//if($k!=1) {  }
				$add_comma=0; $data_dreak='';
				
			}
			//echo $add_comma.'='.$data_dreak.'='.$k.'<br>';
			$k++;
			
			if ($add_comma!=0) $data_dreak ="__";
			$data_dreak_arr[$row[csf('mst_id')]].=$row[csf('description')].'_'.$color_library[$row[csf('color_id')]].'_'.$size_arr[$row[csf('size_id')]].'_'.$row[csf('qnty')].'_'.$row[csf('rate')].'_'.$row[csf('amount')].'_'.$row[csf('book_con_dtls_id')].'_'.$row[csf('id')].'_'.$row[csf('cutting_no')].'_'.$row[csf('fabric_description')].'****';
			$add_comma++;
			
			/*if($data_dreak=="") $data_dreak.=$row[csf('description')].'_'.$row[csf('color_id')].'_'.$row[csf('size_id')].'_'.$row[csf('qnty')].'_'.$row[csf('rate')].'_'.$row[csf('amount')].'_'.$row[csf('book_con_dtls_id')].'_'.$row[csf('id')];
			else $data_dreak.='__'.$row[csf('description')].'_'.$row[csf('color_id')].'_'.$row[csf('size_id')].'_'.$row[csf('qnty')].'_'.$row[csf('rate')].'_'.$row[csf('amount')].'_'.$row[csf('book_con_dtls_id')].'_'.$row[csf('id')];*/
			
			//$data_dreak_arr[$row[csf('mst_id')]]=$data_dreak;
			//$data_dreak='';
		}
	}
	//die;
	//print_r($data_dreak_arr);
	if($data[2]==1)// within_group yes
	{
		
		$buyer_po_arr=array();
	
		$buyer_po_sql = sql_select("SELECT a.style_ref_no, a.buyer_name, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0");
		
		foreach($buyer_po_sql as $row)
		{
			$buyer_po_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$buyer_po_arr[$row[csf('id')]]['buyerpo']=$row[csf('po_number')];
			$buyer_po_arr[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
		}
		unset($buyer_po_sql);
		
		
		
		$embl_po_arr=array(); $po_arr=array();
		if($data[0]==2) // update type 2
		{
			 $sql_up = "SELECT id, order_no, buyer_po_id, booking_dtls_id, gmts_item_id, main_process_id, embl_type, body_part, order_quantity, order_uom, rate, amount,domestic_amount, smv, delivery_date, wastage, buyer_po_no, buyer_style_ref, buyer_buyer from subcon_ord_dtls where  mst_id=$update_id  and status_active=1 and is_deleted=0 order by id ASC";

			$data_arrup=sql_select($sql_up);
			
			foreach($data_arrup as $row)
			{
				$data[1]=$row[csf('order_no')];
				$embl_po_arr[$row[csf('booking_dtls_id')]]['id']=$row[csf('id')];
				$embl_po_arr[$row[csf('booking_dtls_id')]]['smv']=$row[csf('smv')];
				$embl_po_arr[$row[csf('booking_dtls_id')]]['delivery_date']=$row[csf('delivery_date')];
				$embl_po_arr[$row[csf('booking_dtls_id')]]['wastage']=$row[csf('wastage')];
				$embl_po_arr[$row[csf('booking_dtls_id')]]['order_uom']=$row[csf('order_uom')];
				$embl_po_arr[$row[csf('booking_dtls_id')]]['buyer_po_id']=$row[csf('buyer_po_id')];
				$embl_po_arr[$row[csf('booking_dtls_id')]]['domestic_amount']=$row[csf('domestic_amount')];

				$buyerpo=$buyer_po_arr[$row[csf('po_break_down_id')]]['buyerpo'];
				$style=$buyer_po_arr[$row[csf('po_break_down_id')]]['style'];
				$buyer_buyer=$buyer_po_arr[$row[csf('po_break_down_id')]]['buyer_name'];
				$break_down_id=$row[csf('po_break_down_id')];
			}
		}
		
		if($data[4]==1) // with order  (booking_type)
		{
			 $sql = "SELECT a.id as embe_cost_dtls_id, a.job_no, a.emb_name, a.emb_type, a.body_part_id, b.id as booking_dtls_id, b.booking_no, b.po_break_down_id, b.gmt_item, b.wo_qnty, b.rate, b.amount, b.delivery_date from wo_pre_cost_embe_cost_dtls a, wo_booking_dtls b where b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.pre_cost_fabric_cost_dtls_id=a.id and b.booking_type=6 and a.emb_name=1 and a.job_no=b.job_no and b.booking_no=trim('$data[1]') group by a.id, a.job_no, b.job_no, a.emb_name, a.emb_type, a.body_part_id, b.id, b.booking_no, b.po_break_down_id, b.gmt_item, b.wo_qnty, b.rate, b.amount, b.delivery_date order by b.id ASC";
		}
		else  // with Out order
		{
			
			 $sql_cons_per = "select b.costing_per ,c.id as embe_cost_dtls_id  from
		wo_po_details_master a, wo_pre_cost_mst b, wo_pre_cost_embe_cost_dtls c, wo_po_break_down d, wo_pre_cos_emb_co_avg_con_dtls e, wo_booking_dtls f
		where a.job_no=b.job_no and a.job_no=c.job_no and a.job_no=d.job_no_mst and a.job_no=e.job_no and a.job_no=f.job_no and c.id=e.pre_cost_emb_cost_dtls_id and d.id=e.po_break_down_id and e.pre_cost_emb_cost_dtls_id= f.pre_cost_fabric_cost_dtls_id and e.po_break_down_id=f.po_break_down_id and f.booking_type=6 and f.booking_no=trim('$data[1]') and d.is_deleted=0 and d.status_active=1 and f.status_active=1 and f.is_deleted=0 group by b.costing_per ,c.id";
		$data_ons_per=sql_select($sql_cons_per);
		
		foreach($data_ons_per as $row)
		{
			$cons_per_arr[$row[csf('embe_cost_dtls_id')]]['costing_per']=$row[csf('costing_per')];
		}
			
			if($data[5]==2)  // For Sample (wotype)
			{
			  $sql="SELECT a.id as booking_dtls_id, a.embl_cost_dtls_id as embe_cost_dtls_id ,a.embl_cost_dtls_id as printing_cost_dtls_id , a.booking_no, a.booking_type, a.delivery_date, a.gmt_item_id as gmt_item, a.req_id, a.req_no, a.req_booking_no, a.emb_name, a.emb_type, a.body_part_id, a.uom_id, a.sensitivity, a.cons_break_down, a.wo_qnty, a.exchange_rate, a.rate, a.amount, b.fin_fab_qnty as req_qty, b.amount as req_amt , 0 as po_break_down_id
			    from wo_non_ord_embl_booking_dtls a, sample_development_fabric_acc b where b.id=a.embl_cost_dtls_id and b.form_type=3  and a.entry_form_id=399 and a.booking_no=trim('$data[1]') and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 order by a.id"; 
			}
			else if($data[5]==1)// (wotype)
			{
			   /*$sql= "SELECT a.id as embe_cost_dtls_id, a.booking_type,c.emb_name, c.emb_type, c.body_part_id, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.booking_date, a.currency_id, b.id as booking_dtls_id, b.po_break_down_id, b.gmt_item, b.wo_qnty, b.rate, b.amount, b.delivery_date from  wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_embe_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.booking_type=6 and a.status_active=1 and c.emb_name=1 and a.lock_another_process!=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no=trim('$data[1]') group by a.id, a.booking_type,c.emb_name, c.emb_type, c.body_part_id, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.booking_date, a.currency_id, b.id, b.po_break_down_id, b.gmt_item, b.wo_qnty, b.rate, b.amount, b.delivery_date order by b.id ASC" ; */

			  $sql= "SELECT a.id as embe_cost_dtls_id,c.id as printing_cost_dtls_id, a.booking_type,c.emb_name, c.emb_type, c.body_part_id, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.booking_date, a.currency_id, b.id as booking_dtls_id, b.po_break_down_id, b.gmt_item, b.wo_qnty, b.rate, b.amount, b.delivery_date from  wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_embe_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.booking_type=6 and a.status_active=1 and c.emb_name=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no=trim('$data[1]') group by a.id, a.booking_type,c.emb_name, c.emb_type, c.body_part_id, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.booking_date, a.currency_id, b.id, b.po_break_down_id, b.gmt_item, b.wo_qnty, b.rate, b.amount, b.delivery_date,c.id order by b.id ASC" ; 
			}
			    /*echo $sql;*/
    	} 

			//$sql = "SELECT a.id as embe_cost_dtls_id, a.job_no, a.emb_name, a.emb_type, a.body_part_id, b.id as booking_dtls_id, b.booking_no, b.po_break_down_id, b.gmt_item, b.wo_qnty, b.rate, b.amount, b.delivery_date from wo_pre_cost_embe_cost_dtls a, wo_booking_dtls b where b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.pre_cost_fabric_cost_dtls_id=a.id and b.booking_type=6 and a.emb_name=1 and a.job_no=b.job_no and b.booking_no=trim('$data[1]') group by a.id, a.job_no, b.job_no, a.emb_name, a.emb_type, a.body_part_id, b.id, b.booking_no, b.po_break_down_id, b.gmt_item, b.wo_qnty, b.rate, b.amount, b.delivery_date order by b.id ASC";
		
	}
	else
	{
		$sql = "SELECT id, order_no, buyer_po_id, booking_dtls_id, gmts_item_id as gmt_item, main_process_id as emb_name, embl_type as emb_type, body_part as body_part_id, order_quantity as wo_qnty, order_uom, rate, amount, domestic_amount, smv, delivery_date, wastage ,buyer_po_no, buyer_style_ref, buyer_buyer from subcon_ord_dtls where mst_id='$data[3]' and status_active=1 and is_deleted=0 order by id ASC";
	}
	
	 //echo $data[2]; die;
	
	// echo $sql ; die; 
	$data_array=sql_select($sql);
	if(count($data_array) > 0)
	{
		$color_arr=return_library_array( "SELECT id,color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
		$size_arr=return_library_array( "SELECT id,size_name from  lib_size where status_active =1 and is_deleted=0",'id','size_name');
		foreach($data_array as $row)
		{
			$tblRow++;
			$type_array=array(0=>$blank_array,1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type);
			$dtls_id=0; $smv=0; $wastage=0;  $order_uom=0; $wo_qnty=0;
			if($data[2]==1) // within group yes
			{
				if($data[0]==2) // update
				{
					$dtls_id=$embl_po_arr[$row[csf('booking_dtls_id')]]['id']; 
					$smv=$embl_po_arr[$row[csf('booking_dtls_id')]]['smv']; 
					$row[csf("delivery_date")]=$embl_po_arr[$row[csf('booking_dtls_id')]]['delivery_date']; 
					$wastage=$embl_po_arr[$row[csf('booking_dtls_id')]]['wastage'];
					$order_uom=$embl_po_arr[$row[csf('booking_dtls_id')]]['order_uom'];
					$wo_qnty=$row[csf('wo_qnty')];
					$domestic_amount=$embl_po_arr[$row[csf('booking_dtls_id')]]['domestic_amount'];
					$buyerpo=$buyer_po_arr[$row[csf('po_break_down_id')]]['buyerpo'];
					$style=$buyer_po_arr[$row[csf('po_break_down_id')]]['style'];
					$buyer_buyer=$buyer_po_arr[$row[csf('po_break_down_id')]]['buyer_name'];
					$break_down_id=$row[csf('po_break_down_id')];
					//$isWithOrder=$row[csf('is_with_order')];
					$disable_dropdown='1';
					$disabled='disabled';
					//echo $wo_qnty."==".$break_down_id;
					$qty_popup_data= $row[csf('booking_dtls_id')];
 					
				}
				else // save
				{
					if($data[5]==1) // (wotype)
					{ // withOrder
						//echo $row[csf('po_break_down_id')];
						$buyerpo=$buyer_po_arr[$row[csf('po_break_down_id')]]['buyerpo'];
						$style=$buyer_po_arr[$row[csf('po_break_down_id')]]['style'];
						$buyer_buyer=$buyer_po_arr[$row[csf('po_break_down_id')]]['buyer_name'];
						$break_down_id=$row[csf('po_break_down_id')];
					} 
					else
					{
						$buyerpo='';
						$style='';
						$buyer_buyer='';
						$break_down_id='';
					}
					$wo_qnty=$row[csf('wo_qnty')];
					$booking_dtls_id=$row[csf('booking_dtls_id')];
					$dtls_id=$booking_dtls_id;
					$booking_po_id=$row[csf('po_break_down_id')];
					$embe_cost_dtls_id=$row[csf('embe_cost_dtls_id')];
					//echo $row[csf('wo_qnty')].'**'.$row[csf('rate')].'**'.$exchange_rate;
					$domestic_amount=($row[csf('wo_qnty')]*$row[csf('rate')])*$exchange_rate;
					$disable_dropdown='1';
					$disabled='disabled';
					
					$costing_per=$cons_per_arr[$row[csf('printing_cost_dtls_id')]]['costing_per'];
					if($costing_per==1)
					{
						$order_uom=2;
					}
					else if($costing_per==2)
					{
						$order_uom=1;
					}
					else if($costing_per==3)
					{
						$order_uom=2;
					}
					else if($costing_per==4)
					{
						$order_uom=2;
					}
					else if($costing_per==5)
					{
						$order_uom=2;
					}
					else
					{
 						if($data[5]==2)  // For Sample (wotype)
						{
							$order_uom=1;
							
						}
 					}
					
					//$order_uom=2;
					$qty_popup_data= $row[csf('booking_dtls_id')].'_'.$row[csf('embe_cost_dtls_id')];
					
					//$booking_dtls_id=$row[csf('booking_dtls_id')];
						
					//echo $data[4];
					if($data[5]==1) // (wotype)
					{ // with Order
						$sql = "SELECT id, description, color_number_id, gmts_sizes, requirment, color_size_table_id, rate, amount from wo_emb_book_con_dtls where wo_booking_dtls_id='$booking_dtls_id' and po_break_down_id='$booking_po_id' and requirment!=0 and status_active=1 and is_deleted=0 order by id ASC";
						$data_arr=sql_select($sql);
						$data_dreak="";
						foreach($data_arr as $row2){
							if($row2[csf('description')]=="") $row2[csf('description')]=0;
							if($row2[csf('fabric_description')]=="") $row2[csf('fabric_description')]=0;
							if($row2[csf('color_number_id')]=="") $row2[csf('color_number_id')]=0;
							if($row2[csf('gmts_sizes')]=="") $row2[csf('gmts_sizes')]=0;
							if($row2[csf('requirment')]=="") $row2[csf('requirment')]=0;
							if($row2[csf('rate')]=="") $row2[csf('rate')]=0;
							if($row2[csf('amount')]=="") $row2[csf('amount')]=0;
							if($break_down_arr[$row2[csf('id')]]=="") $break_down_arr[$row2[csf('id')]]=0;
							
							if($cutting_no_arr[$row2[csf('id')]]=="") $cutting_no_arr[$row2[csf('id')]]=0;
							
							$data_dreak_arr[$booking_dtls_id].=$row2[csf('description')].'_'.$color_arr[$row2[csf('color_number_id')]].'_'.$size_arr[$row2[csf('gmts_sizes')]].'_'.$row2[csf('requirment')].'_'.$row2[csf('rate')].'_'.$row2[csf('amount')].'_'.$row2[csf('id')].'_'.$break_down_arr[$row2[csf('id')]].'_'.$cutting_no_arr[$row2[csf('id')]].'_'.$row2[csf('fabric_description')].'****';
						}
					}
					else
					{
						
						
					//	echo "select id,wo_booking_dtls_id,description,item_color,item_size,cons,process_loss_percent,requirment,rate,amount,emb_color_size_id as color_size_table_id  from wo_non_ord_embl_book_cons_dtls where wo_booking_dtls_id in($booking_dtls_id) and status_active=1 and is_deleted=0"; die;
						
						//$booking_sql = "select id,wo_booking_dtls_id,description,item_color as color_number_id ,item_size as gmts_sizes,cons,process_loss_percent,requirment,rate,amount,emb_color_size_id as color_size_table_id  from wo_non_ord_embl_book_cons_dtls where wo_booking_dtls_id in($booking_dtls_id) and requirment!=0 and status_active=1 and is_deleted=0 order by id ASC";
						$booking_data=sql_select("select id,wo_booking_dtls_id,description,item_color,item_size,cons,process_loss_percent,requirment,rate,amount,emb_color_size_id as color_size_table_id  from wo_non_ord_embl_book_cons_dtls where wo_booking_dtls_id in($booking_dtls_id) and status_active=1 and is_deleted=0");
						
						$booking_data_arr=array();
						foreach($booking_data as $row2)
						{
							$booking_data_arr[$row2[csf('color_size_table_id')]][id]=$row2[csf('id')];
							$booking_data_arr[$row2[csf('color_size_table_id')]][description]=$row2[csf('description')];
							$booking_data_arr[$row2[csf('color_size_table_id')]][item_color]=$row2[csf('item_color')];
							$booking_data_arr[$row2[csf('color_size_table_id')]][item_size]=$row2[csf('item_size')];

							$booking_data_arr[$row2[csf('color_size_table_id')]][cons]+=$row2[csf('cons')];
							$booking_data_arr[$row2[csf('color_size_table_id')]][process_loss_percent]=$row2[csf('process_loss_percent')];
							$booking_data_arr[$row2[csf('color_size_table_id')]][requirment]+=$row2[csf('requirment')];
							$booking_data_arr[$row2[csf('color_size_table_id')]][rate]=$row2[csf('rate')];
							$booking_data_arr[$row2[csf('color_size_table_id')]][amount]+=$row2[csf('amount')];
                    	}
					 


					 	$sql="select a.id,a.style_ref_no,b.id as emb_dtls_id, (b.fin_fab_qnty) AS fin_fab_qnty,c.id as color_size_table_id,c.color_id as color_number_id,c.size_id as size_number_id,c.qnty
						from sample_development_mst a, sample_development_fabric_acc b,sample_develop_embl_color_size c where a.id=b.sample_mst_id and b.id=c.dtls_id and
						b.form_type=3  and b.id in($embe_cost_dtls_id) and a.entry_form_id=203 and b.form_type=3 and a.status_active=1 and b.status_active=1 and c.status_active=1   and b.fin_fab_qnty>0 order by a.id, b.id";
						
						
						$data_arr=sql_select($sql);
						if ( count($data_arr)>0)
						{
							$i=0; $data_dreak="";
							foreach( $data_arr as $row3 )
							{
								 /*if($cbo_colorsizesensitive==4){
									$txt_req_quantity=$row[csf('qnty')];
									$txtwoq_cal = def_number_format($txt_req_quantity,5,"");
								}*/
								$txt_req_quantity=$row3[csf('qnty')];
								$txtwoq_cal = def_number_format($txt_req_quantity,5,"");
								$description=$booking_data_arr[$row3[csf('color_size_table_id')]][description];
								$fabric_description='';
								$item_color=$booking_data_arr[$row3[csf('color_size_table_id')]][item_color];
								if($item_color==0 || $item_color=="" ) $item_color = $row3[csf('color_number_id')];
								$item_size=$booking_data_arr[$row3[csf('color_size_table_id')]][item_size];
								if($item_size==0 || $item_size == "") $item_size=$size_library[$row3[csf('size_number_id')]];
								
								//$cons=$booking_data_arr[$row3[csf('color_size_table_id')]][cons];
								
								$rate=$booking_data_arr[$row3[csf('color_size_table_id')]][rate];
								if($rate==0 || $rate=="") $rate=$txt_avg_price;

								$amount =$booking_data_arr[$row3[csf('color_size_table_id')]][amount];
								$embl_book_cons_dtls_id =$booking_data_arr[$row3[csf('color_size_table_id')]][id];
								
								$data_dreak_arr[$booking_dtls_id].=$description.'_'.$row3[csf('color_number_id')].'_'.$row3[csf('size_number_id')].'_'.$txtwoq_cal.'_'.$rate.'_'.$amount.'_'.$embl_book_cons_dtls_id.'_0_0_'.$fabric_description.'****';
								$i++;
							}
						}
					}
					//echo $buyerpo.'==';
				}
				
				$booking_type=$data[4]; // with order  (booking_type)
			}
			else if($data[2]==2) // within group no
			{
				if($data[0]==2) // update
				{
					$dtls_id=$row[csf('id')]; 
					$smv=$row[csf('smv')];
					$row[csf("delivery_date")]=$row[csf('delivery_date')];
					$wastage=$row[csf('wastage')];
					$order_uom=$row[csf('order_uom')];
					$wo_qnty=$row[csf('wo_qnty')];
					$buyerpo=$row[csf('buyer_po_no')];
					$style=$row[csf('buyer_style_ref')];
					$buyer_buyer=$row[csf('buyer_buyer')];
					$domestic_amount=$row[csf('domestic_amount')];;
					$order_uom=$row[csf('order_uom')];
					//$qty_popup_data= $row[csf('booking_dtls_id')];
					$qty_popup_data= 0;
					$booking_type=0;
					//$wo_qnty=$row[csf('wo_qnty')];
				}
				else // save
				{
					$wo_qnty=0;
					$disable_dropdown='1';
					$disabled='disabled';
					$order_uom=2;
					
				}
			}

			if($order_uom==2){
				$qty_pcs=$wo_qnty*12;
			}else if($order_uom==1){
				$qty_pcs=$wo_qnty;
			}else{
				$qty_pcs=0;
			}
			//echo $buyerpo.'++';
			//echo "<pre>"; print_r($data_dreak_arr); die;
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
				<td><input id="txtbuyerPo_<? echo $tblRow; ?>" name="txtbuyerPo[]" value="<? echo $buyerpo; ?>" class="text_boxes" type="text"  style="width:100px" <? echo $disabled ?> />
					<input id="txtbuyerPoId_<? echo $tblRow; ?>" name="txtbuyerPoId[]" value="<? echo $break_down_id; ?>" class="text_boxes" type="hidden" style="width:70px" readonly />
				</td>
				<td><input id="txtstyleRef_<? echo $tblRow; ?>" name="txtstyleRef[]" value="<? echo $style; ?>" class="text_boxes" type="text"  style="width:100px" <? echo $disabled ?> /></td>
				<td>
					<? 
					if($data[2]==1)
					{
						echo create_drop_down( "txtbuyer_".$tblRow, 110, "select id, buyer_name from lib_buyer where status_active=1","id,buyer_name", 1, "-- Select --",$buyer_buyer, "",$disable_dropdown,'','','','','','',"txtbuyer[]");
					}
					else
					{
					?>
						<input id="txtbuyer_<? echo $tblRow; ?>" name="txtbuyer[]" value="<? echo $buyer_buyer; ?>" class="text_boxes" type="text"  style="width:100px"  <? echo $disabled ?>  />
						<?
					}
					?>
				</td>
				<td><? echo create_drop_down( "cboGmtsItem_".$tblRow, 90, $garments_item,"", 1, "-- Select --",$row[csf('gmt_item')], "",1,'','','','','','',"cboGmtsItem[]"); ?>	</td>		
				<td><? echo create_drop_down( "cboProcessName_".$tblRow, 80, $emblishment_name_array,"", 1, "--Select--", $row[csf('emb_name')],  "change_caption_n_uom (".$tblRow.",this.value);", 1,1,'','','','','',"cboProcessName[]"); ?>	</td>
				<td id="embltype_td_<? echo $tblRow; ?>"><? echo create_drop_down( "cboembtype_".$tblRow, 80, $type_array[$row[csf('emb_name')]],"", 1, "-- Select --",$row[csf('emb_type')], "",1,'','','','','','',"cboembtype[]"); ?>	</td>
				<td><? echo create_drop_down( "cboBodyPart_".$tblRow, 80, $body_part,"", 1, "-- Select --",$row[csf('body_part_id')], "",1,'','','','','','',"cboBodyPart[]"); ?>	</td>

				<td><input name="txtOrderQuantity[]" id="txtOrderQuantity_<? echo $tblRow; ?>" value="<? echo number_format($wo_qnty,4,'.',''); ?>" class="text_boxes_numeric" type="text"  style="width:60px" onClick="openmypage_order_qnty(1,'<? echo $qty_popup_data; ?>',<? echo $tblRow; ?>,<? echo  $booking_type; ?>,<? echo $data[5]; ?>)" placeholder="Click To Search" readonly /></td> 
				<td><?
				 echo create_drop_down( "cboUom_".$tblRow, 60, $unit_of_measurement,"",1, "-- Select --",$order_uom,"fnc_load_uom(1,this.value);", 1,"2,1",'','','','','',"cboUom[]" );
				// echo create_drop_down( "cboUom_".$tblRow, 60, $unit_of_measurement,"", 1, "-- Select --",2,1, 1,'','','','','','',"cboUom[]");
				 ?>	</td>
				<td><input name="txtRate[]" id="txtRate_<? echo $tblRow; ?>" value="<? echo number_format($row[csf('rate')],4,'.',''); ?>" type="text"  class="text_boxes_numeric" style="width:60px" readonly/></td>
				<td><input name="txtAmount[]" id="txtAmount_<? echo $tblRow; ?>" value="<? echo number_format($row[csf('amount')],4,'.',''); ?>" type="text" style="width:70px"  class="text_boxes_numeric" readonly /></td> 
                <td align="center"><input type="text" name="txtdomisticamount[]" id="txtdomisticamount_<? echo $tblRow; ?>" class="text_boxes_numeric" style="width:90px;" value="<? echo number_format($domestic_amount,4,'.',''); ?>" readonly /></td>
                <td><input name="txtQtyPcs[]" id="txtQtyPcs_<? echo $tblRow; ?>" type="text" class="text_boxes_numeric" value="<? echo number_format($qty_pcs,4,'.',''); ?>" style="width:67px" readonly />
				<td><input name="txtSmv[]" id="txtSmv_<? echo $tblRow; ?>" type="text" value="<? echo $smv; ?>" class="text_boxes_numeric" style="width:40px" onChange="copy_value(this.value,<? echo $tblRow;?>)"/>
				<td><input type="text" name="txtOrderDeliveryDate[]"  id="txtOrderDeliveryDate_<? echo $tblRow; ?>" value="<? echo change_date_format($row[csf("delivery_date")]);?>" class="datepicker" style="width:50px" /></td>
				<td><input name="txtWastage[]" id="txtWastage_<? echo $tblRow; ?>" type="text" value="<? echo $wastage; ?>" class="text_boxes_numeric" style="width:47px" />
					<input name="hdnDtlsUpdateId[]" id="hdnDtlsUpdateId_<? echo $tblRow; ?>" type="hidden" value="<? echo $dtls_id; ?>" class="text_boxes_numeric" style="width:40px" />
					<input type="hidden" name="hdnDtlsdata[]" id="hdnDtlsdata_<? echo $tblRow; ?>" value="<? echo implode("__",array_filter(explode('****',$data_dreak_arr[$dtls_id]))); ?>">
                    <input type="hidden" name="hdnbookingDtlsId[]" id="hdnbookingDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf('booking_dtls_id')]; ?>">
                    <input type="hidden" id="txtIsWithOrder_<? echo $tblRow; ?>" name="txtIsWithOrder[]" value="<? echo $isWithOrder; ?>">
                    <input type="hidden" name="txtDelBreakId[]" id="txtDelBreakId_<? echo $tblRow; ?>">
				</td>
				<td width="65">
				<input type="button" id="increase_<? echo $tblRow; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fnc_addRow(<? echo $tblRow; ?>,'tbl_dtls_emb','row_')" />
				<input type="button" id="decrease_<? echo $tblRow; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fnc_deleteRow(<? echo $tblRow; ?>,'tbl_dtls_emb','row_');" />
				</td>
			</tr>
			<?
		}
	}
	else
	{
		?>		
		<tr id="row_1">
            <td><input name="txtbuyerPo[]" id="txtbuyerPo_1" type="text" class="text_boxes" style="width:100px" placeholder="Display" readonly />
            	<input name="txtbuyerPoId[]" id="txtbuyerPoId_1" type="hidden" class="text_boxes" style="width:70px" />
            </td>
            <td><input name="txtstyleRef[]" id="txtstyleRef_1" type="text" class="text_boxes" style="width:100px" placeholder="Display" readonly /></td>
            <td><input id="txtbuyer_1" name="txtbuyer[]" type="text" class="text_boxes" style="width:100px" placeholder="Display" /></td>
            <td><? echo create_drop_down( "cboGmtsItem_1", 90, $garments_item,"", 1, "-- Select --",$selected, "",0,'','','','','','',"cboProcessName[]"); ?>	</td>
            <td><? echo create_drop_down( "cboProcessName_1", 80, $emblishment_name_array,"", 1, "--Select--",0,"change_caption_n_uom(1,this.value);", 1,1,'','','','','',"cboProcessName[]"); ?>	</td>
            <td id="embltype_td_1"><? echo create_drop_down( "cboembtype", 80, $blank_array,"", 1, "-- Select --",$selected, "",0,"" ); ?></td>
            <td><? echo create_drop_down( "cboBodyPart_1", 80, $body_part,"", 1, "-- Select --",$selected, "",0,'','','','','','',"cboBodyPart[]"); ?>	</td>
            <td><input name="txtOrderQuantity[]" id="txtOrderQuantity_1" class="text_boxes_numeric" type="text"  style="width:60px" onClick="openmypage_order_qnty(1,'0',1,'0','0')" placeholder="Click To Search" readonly /></td>
            <td><? 
			
			//echo create_drop_down( "cboUom_1", 60, $unit_of_measurement,"", 1, "-- Select --",2,"fnc_load_uom(1,this.value);", 1,"2,1" );
			echo create_drop_down( "cboUom_1", 60, $unit_of_measurement,"",1, "-- Select --",2,"fnc_load_uom(1,this.value);", 1,"2,1",'','','','','',"cboUom[]" );
			//echo create_drop_down( "cboUom_1", 60, $unit_of_measurement,"", 1, "-- Select --",2,1, 1,'','','','','','',"cboBookUom[]"); 
			?>	</td>
            <td><input name="txtRate[]" id="txtRate_1" type="text"  class="text_boxes_numeric" style="width:60px" /></td>
            <td><input name="txtAmount[]" id="txtAmount_1" type="text" style="width:70px"  class="text_boxes_numeric" readonly /></td> 
            <td align="center"><input type="text" name="txtdomisticamount[]" id="txtdomisticamount_1" class="text_boxes_numeric" style="width:90px;" readonly /></td>
            <td><input name="txtQtyPcs[]" id="txtQtyPcs_1" type="text"  class="text_boxes_numeric" style="width:67px" readonly /></td> 
            <td><input name="txtSmv[]" id="txtSmv_1" type="text"  class="text_boxes_numeric" style="width:40px" /></td> 
            <td><input type="text" name="txtOrderDeliveryDate[]" id="txtOrderDeliveryDate_1" class="datepicker" style="width:50px" /></td>
            <td>
                <input name="txtWastage[]" id="txtWastage_1" type="text"  class="text_boxes_numeric" style="width:47px" />
                <input type="hidden" name="hdnDtlsUpdateId[]" id="hdnDtlsUpdateId_1">
                <input type="hidden" name="hdnDtlsdata[]" id="hdnDtlsdata_1">
                <input type="hidden" name="hdnbookingDtlsId[]" id="hdnbookingDtlsId_1">
                <input type="hidden" name="txtDelBreakId[]" id="txtDelBreakId_1">
            </td>
            <td width="65">
			<input type="button" id="increase_1" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fnc_addRow(1,'tbl_dtls_emb','row_')" />
			<input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fnc_deleteRow(1,'tbl_dtls_emb','row_');" />
			</td>
        </tr> 
		<?
	}
	exit();
}

if($action=="order_qty_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	$color_arr=return_library_array( "SELECT id,color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
	$size_arr=return_library_array( "SELECT id,size_name from  lib_size where status_active =1 and is_deleted=0",'id','size_name');
    ?>
    <script>
	var str_color = [<? echo substr(return_library_autocomplete( "SELECT color_name from lib_color where status_active =1 and is_deleted=0 group by color_name ", "color_name" ), 0, -1); ?> ];
	var str_size = [<? echo substr(return_library_autocomplete( "SELECT size_name from lib_size where status_active =1 and is_deleted=0 group by size_name ", "size_name" ), 0, -1); ?> ];
	
	function set_auto_complete(type)
	{
		if(type=='color_return')
		{
			$(".txt_color").autocomplete({
				source: str_color
			});
		}
	}

	function set_auto_complete_size(type)
	{
		if(type=='size_return')
		{
			$(".txt_size").autocomplete({
				source: str_size
			});
		}
	}

	function add_share_row(i,tr) 
	{
		//var row_num=$('#tbl_share_details_entry tbody tr').length-1;
		var row_num=$('#tbl_share_details_entry tbody tr').length;
		if (i==0)
		{
			i=1;
			$("#txtcolor_"+i).autocomplete({
				source: str_color
			});
			$("#txtsize_"+i).autocomplete({
				source:  str_size
			});
			return;
		}
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
		
		$('#increaseset_'+i).removeAttr("onClick").attr("onClick","add_share_row("+i+",this.id);");
		$('#txtcolor_'+i).removeAttr("onChange").attr("onChange","check_duplicate("+i+",this.id)");
		$('#txtsize_'+i).removeAttr("onChange").attr("onChange","check_duplicate("+i+",this.id)");
		$('#txtorderquantity_'+i).removeAttr("onKeyUp").attr("onKeyUp","sum_total_qnty("+i+",this.id);");
		$('#txtorderrate_'+i).removeAttr("onKeyUp").attr("onKeyUp","sum_total_qnty("+i+",this.id);");
		
		$('#txtcolor_'+i).removeAttr("disabled");
		$('#txtorderquantity_'+i).removeAttr("disabled");
		$('#txtorderrate_'+i).removeAttr("disabled");
		$('#txtdescription_'+i).removeAttr("disabled");
		$('#txtsize_'+i).removeAttr("disabled");
		
		$('#decreaseset_'+i).removeAttr("disabled");			
		//$('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+','+'"tbl_share_details_entry"'+");");
		$('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",this.id)");
		$('#txtsize_'+i).val('');
		//$('#loss_'+i).val('');
		$('#hiddenid_'+i).val('');
		set_all_onclick();
		$("#txtcolor_"+i).autocomplete({
			source: str_color
		});
		$("#txtsize_"+i).autocomplete({
			source: str_size
		});
		sum_total_qnty(i);
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
		sum_total_qnty(rowNo);
	}

	function fnc_close()
	{
		var tot_row=$('#tbl_share_details_entry tbody tr').length;
		
		var data_break_down="";
		for(var i=1; i<=tot_row; i++)
		{
			if (form_validation('txtcolor_'+i+'*txtsize_'+i+'*txtorderquantity_'+i,'Color*Size*Quantity')==false)
			{
				return;
			}
			if($("#txtdescription_"+i).val()=="") $("#txtdescription_"+i).val(0);
			if($("#txtFebdescription_"+i).val()=="") $("#txtFebdescription_"+i).val(0);
			if($("#txtcolor_"+i).val()=="") $("#txtcolor_"+i).val(0);
			if($("#txtsize_"+i).val()=="") $("#txtsize_"+i).val(0);
			if($("#txtorderquantity_"+i).val()=="") $("#txtorderquantity_"+i).val(0);
			if($("#txtorderrate_"+i).val()=="") $("#txtorderrate_"+i).val(0);
			if($("#txtorderamount_"+i).val()=="") $("#txtorderamount_"+i).val(0);
			if($("#hidbookingconsid_"+i).val()=="") $("#hidbookingconsid_"+i).val(0);
			if($("#hiddenid_"+i).val()=="") $("#hiddenid_"+i).val(0);
			if($("#txtcuttinno_"+i).val()=="") $("#txtcuttinno_"+i).val(0);
			
			if(data_break_down=="")
			{
				data_break_down+=$('#txtdescription_'+i).val()+'_'+$('#txtcolor_'+i).val()+'_'+$('#txtsize_'+i).val()+'_'+$('#txtorderquantity_'+i).val()+'_'+$('#txtorderrate_'+i).val()+'_'+$('#txtorderamount_'+i).val()+'_'+$('#hidbookingconsid_'+i).val()+'_'+$('#hiddenid_'+i).val()+'_'+$('#txtcuttinno_'+i).val()+'_'+$('#txtFebdescription_'+i).val();
			}
			else
			{
				data_break_down+="__"+$('#txtdescription_'+i).val()+'_'+$('#txtcolor_'+i).val()+'_'+$('#txtsize_'+i).val()+'_'+$('#txtorderquantity_'+i).val()+'_'+$('#txtorderrate_'+i).val()+'_'+$('#txtorderamount_'+i).val()+'_'+$('#hidbookingconsid_'+i).val()+'_'+$('#hiddenid_'+i).val()+'_'+$('#txtcuttinno_'+i).val()+'_'+$('#txtFebdescription_'+i).val();
			}
		}
		$('#hidden_break_tot_row').val( data_break_down );
		//alert(tot_row);//return;
		parent.emailwindow.hide();
	}

	function sum_total_qnty(id)
	{
		var ddd={ dec_type:5, comma:0, currency:''};
		var tot_row=$('#tbl_share_details_entry tbody tr').length;
		$("#txtorderamount_"+id).val(($("#txtorderquantity_"+id).val()*1)*($("#txtorderrate_"+id).val()*1));
		math_operation( "txt_total_order_qnty", "txtorderquantity_", "+", tot_row,ddd );
		//math_operation( "txt_average_rate", "txtorderrate_", "+", tot_row,ddd );
		math_operation( "txt_total_order_amount", "txtorderamount_", "+", tot_row,ddd );
		
		var tot_row=$('#tbl_share_details_entry tbody tr').length;
		
		var qty=0; var amt=0;
		for(var i=1; i<=tot_row; i++)
		{
			qty+=$("#txtorderquantity_"+i).val()*1;
			amt+=$("#txtorderamount_"+i).val()*1;
		}
		
		var rate=amt/qty;
		$("#txt_average_rate").val( number_format(rate,4,'.','' ) );
	}
		
	function checkalltr_f(value) 
	{
		var row_num=$('#tbl_share_details_entry tr').length-2;
		for (var k=1;k<=row_num; k++)
		{
			if(value==1)
			{
			$('#checktr_'+k).prop('checked', true);
			document.getElementById('checkalltr').value=2
			}
			if(value==2)
			{
			$('#checktr_'+k).prop('checked', false);
			document.getElementById('checkalltr').value=1
			}
			//$('#checktr_'+k).click();
		}
		show_hide_button_holder()
	}

	function show_hide_button_holder()
	{
		var row_num=$('#tbl_share_details_entry tr').length-2;
		var checked=0;
		for (var k=1;k<=row_num; k++)
		{
			if(checked==0)
			{
				var is_checked=$("#checktr_"+k).is(':checked');
				if(is_checked) checked=1; else checked=0
			}
		}
		if(checked==1)
		{
			$('#clear_button_holder').show();
		}
		if(checked==0)
		{
			$('#clear_button_holder').hide();
		}
	}
	
	function tr_check(i,e)
	{
		if (e.ctrlKey) {
		   var row_num=$('#tbl_share_details_entry tr').length-2;
		   var checked=[];
		   var i=0;
			for (var k=1;k<=row_num; k++)
			{
				var is_checked=$("#checktr_"+k).is(':checked');
				if(is_checked)
				{
					checked[i]=k;
					i++;
				}
			}
			checked.sort(function(a, b){return b-a});
			var highest=checked[0];
			//alert(highest);
			checked.sort(function(a, b){return a-b});
			var lowest=checked[0];
			//alert(lowest);
			for (var j=lowest+1;j<=highest-1; j++)
			{
				$('#checktr_'+j).prop('checked', true);
			}
		}
		show_hide_button_holder();
	}

	function clear_color(type)
	{
		var row_num=$('#tbl_share_details_entry tr').length-2;
		var checked=0;
		for (var k=1;k<=row_num; k++)
		{
			var is_checked=$("#checktr_"+k).is(':checked');
			if(is_checked)
			{
				$("#"+type+k).val('');
				checked+=1;
			}
		}
		if(checked==0)
		{
			alert("Check row First")
		}
	}

	function vacant_form()
	{
		document.getElementById('txt_copy_color').value="";
	}
	
	function copyset_tr()
	{
	   var rowNum=$('#tbl_share_details_entry tr').length-2;
	   
	   var checked=0;
		for (var k=1;k<=rowNum; k++)
		{
			var is_checked=$("#checktr_"+k).is(':checked');
			if(is_checked)
			{
				var txt_copy_color=(document.getElementById('txt_copy_color').value).toUpperCase();
				var txtcolor=(document.getElementById('txtcolor_'+k).value).toUpperCase();
				if(txt_copy_color==txtcolor)
				{
					alert("Duplicate Item, Color and Size found")
					continue;
				}
				var row_num=$('#tbl_share_details_entry tr').length-2;
				row_num+=1;
				$("#tbl_share_details_entry tr:eq("+k+")").clone().find("input,select").each(function() {
					$(this).attr({
						'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ row_num },
						'name': function(_, name) { return name + row_num },
						'value': function(_, value) { return value }
					});
				}).end().appendTo("#tbl_share_details_entry");
				$('#txtcolor_'+row_num).removeAttr("onChange").attr("onChange","check_duplicate("+row_num+",this.id)");
				$('#txtsize_'+row_num).removeAttr("onChange").attr("onChange","check_duplicate("+row_num+",this.id)");
				
				$('#txtorderquantity_'+row_num).removeAttr("onKeyUp").attr("onKeyUp","sum_total_qnty("+row_num+",this.id);");
				$('#txtorderrate_'+row_num).removeAttr("onKeyUp").attr("onKeyUp","sum_total_qnty("+row_num+",this.id);");
				$('#increaseset_'+row_num).removeAttr("onClick").attr("onClick","add_share_row("+row_num+",this.id);");
				//$('#decreaseset_'+row_num).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+row_num+",'tbl_share_details_entry',this);");
				$('#decreaseset_'+row_num).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+row_num+",this.id)");
				$('#checktr_'+row_num).removeAttr("onClick").attr("onClick","tr_check("+row_num+",event);");
				if(txt_copy_color !="")
				{
				$('#txtcolor_'+row_num).val(txt_copy_color);
				}
				$('#hiddenid_'+row_num).val("");
				$('#checktr_'+row_num).prop('checked', false);
				sum_total_qnty(k);
				checked+=1;
				//navigate_arrow_key();
			}
		}
		if(checked==0)
		{
			alert("Check row First")
		}
	}
	
	function check_duplicate(id,td)
	{
		var item_id=(document.getElementById('txtdescription_'+id).value);
		var txtcolor=(document.getElementById('txtcolor_'+id).value).toUpperCase();
		var txtsize=(document.getElementById('txtsize_'+id).value).toUpperCase();
		var row_num=$('#tbl_share_details_entry tr').length-2;
		for (var k=1;k<=row_num; k++)
		{
			if(k==id)
			{
				continue;
			}
			else
			{
				if(item_id==document.getElementById('txtdescription_'+k).value && trim(txtcolor)==trim(document.getElementById('txtcolor_'+k).value.toUpperCase()) && trim(txtsize)==trim(document.getElementById('txtsize_'+k).value.toUpperCase()))
				{
				alert("Same Description,Same Color and Same Size Duplication Not Allowed.");
				document.getElementById(td).value="";
				document.getElementById(td).focus();
				}
			}
		}
	}

	</script>
    </head>
    <body onLoad="set_auto_complete('color_return'); set_auto_complete_size('size_return');">
	<div align="center" style="width:100%;" >
		<form name="qntypopup_1"  id="qntypopup_1" autocomplete="off">
        
        <? if ($within_group==2)
		{
			$is_display=''; $colspan=5; ?>
        	<fieldset style="width:700px">
            <legend>Copy Color</legend>
                <table align="left">
                    <tr>
                    	
                        <td> New Color:</td>
                        <td><input class="text_boxes" type="text" style="width:120px;" name="txt_copy_color" id="txt_copy_color"/>
                        </td>
                         <td colspan="2" align="center">
                            <input type="button" id="copyset1" style="width:50px" class="formbutton" value="Copy" onClick="copyset_tr()" />
                        </td>
                        <td colspan="2" align="center">
                            <input type="reset" value="Reset" class="formbutton"  onClick="vacant_form()"/>
                        </td>
                    </tr>
                </table>
               		 <div style="display: none;" id="clear_button_holder">
                        <input type="button" class="image_uploader" id="color_clear" value="Clear Color" onClick="clear_color('txtcolor_')">
                        <input type="button" class="image_uploader" id="size_clear" value="Clear Size" onClick="clear_color('txtsize_')">
                        <input type="button" class="image_uploader" id="ord_qty_clear" value="Clear Order Qnty" onClick="clear_color('txtorderquantity_')">
                        <input type="button" class="image_uploader" id="rate_clear" value="Clear Rate" onClick="clear_color('txtorderrate_')">
                      </div>
            </fieldset>
            
            <? 
			}
		else
		{
			$is_display='style="display: none;"';  $colspan=3;
		}
		?>
         
			<table class="rpt_table" width="820px" cellspacing="0" cellpadding="0" rules="all" id="tbl_share_details_entry">
				<thead>
                	<th <? echo $is_display; ?> width="40"><input type="checkbox" name="checkalltr" id="checkalltr" onClick="checkalltr_f(this.value)" value="1"></th>
					<th width="130">Embelishment Description</th>
					<th width="130">Fabric Description</th>
					<th width="100">Color</th>
					<th width="80">GMTS Size</th>
					<th width="70" class="must_entry_caption">Order Qty</th>
					<th width="60">Rate</th>
					<th width="80">Amount</th>
                    <th width="80" style="display:none">Cutting No</th>
					<th>&nbsp;</th>
				</thead>
				<tbody>
					<input type="hidden" name="hidden_process_id" id="hidden_process_id" value="<? echo $process_id; ?>">
					<input type="hidden" name="txtDeletedId" id="txtDeletedId" class="text_boxes_numeric" value='' style="width:90px" readonly />
					<input type="hidden" name="hidden_break_tot_row" id="hidden_break_tot_row" class="text_boxes" style="width:90px" />
                    
					<?
					 $sql_break_down="SELECT id, mst_id, order_id, job_no_mst, book_con_dtls_id, item_id, body_part, embellishment_type, description, color_id, size_id, qnty, rate, amount,cutting_no, fabric_description from subcon_ord_breakdown where mst_id='$hdnDtlsUpdateId' and job_no_mst='$job_no' and qnty>0 and status_active=1 and is_deleted=0";
					$data_break_down=sql_select($sql_break_down);
					$break_down_arr=array();
					$cutting_no_arr=array();  $data_dreak="";
					foreach($data_break_down as $row)
					{
						$break_down_arr[$row[csf('book_con_dtls_id')]]=$row[csf('id')];
						$cutting_no_arr[$row[csf('book_con_dtls_id')]]=$row[csf('cutting_no')];
						
						if($row[csf('description')]=="") $row[csf('description')]=0;
						if($row[csf('fabric_description')]=="") $row[csf('fabric_description')]=0;
						if($row[csf('color_id')]=="") $row[csf('color_id')]=0;
						if($row[csf('size_id')]=="") $row[csf('size_id')]=0;
						if($row[csf('qnty')]=="") $row[csf('qnty')]=0;
						if($row[csf('rate')]=="") $row[csf('rate')]=0;
						if($row[csf('amount')]=="") $row[csf('amount')]=0;
						if($row[csf('cutting_no')]=="") $row[csf('cutting_no')]=0;
						
						if($data_dreak=="") $data_dreak.=$row[csf('description')].'_'.$row[csf('color_id')].'_'.$row[csf('size_id')].'_'.$row[csf('qnty')].'_'.$row[csf('rate')].'_'.$row[csf('amount')].'_'.$row[csf('id')].'_'.$row[csf('id')].'_'.$row[csf('cutting_no')].'_'.$row[csf('fabric_description')];
						else $data_dreak.='__'.$row[csf('description')].'_'.$row[csf('color_id')].'_'.$row[csf('size_id')].'_'.$row[csf('qnty')].'_'.$row[csf('rate')].'_'.$row[csf('amount')].'_'.$row[csf('id')].'_'.$row[csf('id')].'_'.$row[csf('cutting_no')].'_'.$row[csf('fabric_description')];
					}
					$booking_dtls_ids 	= explode('_', $booking_dtls_id);
					$booking_dtls_id 	= $booking_dtls_ids[0];
					$txtembcostid 		= $booking_dtls_ids[1];
					if($within_group==1)
					{	
						//echo $exchange_rate.'=='.$booking_type;
						if($booking_type==1 || $wo_type==1)
						{ // with Order
							$sql = "SELECT id, description, color_number_id, gmts_sizes, requirment, color_size_table_id, rate, amount from wo_emb_book_con_dtls where wo_booking_dtls_id='$booking_dtls_id' and po_break_down_id='$booking_po_id' and requirment!=0 and status_active=1 and is_deleted=0 order by id ASC";
							$data_arr=sql_select($sql);
							$data_dreak="";
							foreach($data_arr as $row){
								if($row[csf('description')]=="") $row[csf('description')]=0;
								if($row[csf('color_number_id')]=="") $row[csf('color_number_id')]=0;
								if($row[csf('gmts_sizes')]=="") $row[csf('gmts_sizes')]=0;
								if($row[csf('requirment')]=="") $row[csf('requirment')]=0;
								if($row[csf('rate')]=="") $row[csf('rate')]=0;
								if($row[csf('amount')]=="") $row[csf('amount')]=0;
								if($break_down_arr[$row[csf('id')]]=="") $break_down_arr[$row[csf('id')]]=0;
								
								if($cutting_no_arr[$row[csf('id')]]=="") $cutting_no_arr[$row[csf('id')]]=0;
								
								if($data_dreak=="") $data_dreak.=$row[csf('description')].'_'.$row[csf('color_number_id')].'_'.$row[csf('gmts_sizes')].'_'.$row[csf('requirment')].'_'.$row[csf('rate')].'_'.$row[csf('amount')].'_'.$row[csf('id')].'_'.$break_down_arr[$row[csf('id')]].'_'.$cutting_no_arr[$row[csf('id')]].'_ ';
								else $data_dreak.='__'.$row[csf('description')].'_'.$row[csf('color_number_id')].'_'.$row[csf('gmts_sizes')].'_'.$row[csf('requirment')].'_'.$row[csf('rate')].'_'.$row[csf('amount')].'_'.$row[csf('id')].'_'.$break_down_arr[$row[csf('id')]].'_'.$cutting_no_arr[$row[csf('id')]].'_ ';
							}
						}
						else
						{
							//echo "select id,wo_booking_dtls_id,description,item_color,item_size,cons,process_loss_percent,requirment,rate,amount,emb_color_size_id as color_size_table_id  from wo_non_ord_embl_book_cons_dtls where wo_booking_dtls_id in($booking_dtls_id) and status_active=1 and is_deleted=0";
							//$booking_sql = "select id,wo_booking_dtls_id,description,item_color as color_number_id ,item_size as gmts_sizes,cons,process_loss_percent,requirment,rate,amount,emb_color_size_id as color_size_table_id  from wo_non_ord_embl_book_cons_dtls where wo_booking_dtls_id in($booking_dtls_id) and requirment!=0 and status_active=1 and is_deleted=0 order by id ASC";
							$booking_data=sql_select("select id,wo_booking_dtls_id,description,item_color,item_size,cons,process_loss_percent,requirment,rate,amount,emb_color_size_id as color_size_table_id  from wo_non_ord_embl_book_cons_dtls where wo_booking_dtls_id in($booking_dtls_id) and status_active=1 and is_deleted=0");
							
							$booking_data_arr=array();
							foreach($booking_data as $row){
								$booking_data_arr[$row[csf('color_size_table_id')]][id]=$row[csf('id')];
								$booking_data_arr[$row[csf('color_size_table_id')]][description]=$row[csf('description')];
								$booking_data_arr[$row[csf('color_size_table_id')]][item_color]=$row[csf('item_color')];
								$booking_data_arr[$row[csf('color_size_table_id')]][item_size]=$row[csf('item_size')];

								$booking_data_arr[$row[csf('color_size_table_id')]][cons]+=$row[csf('cons')];
								$booking_data_arr[$row[csf('color_size_table_id')]][process_loss_percent]=$row[csf('process_loss_percent')];
								$booking_data_arr[$row[csf('color_size_table_id')]][requirment]+=$row[csf('requirment')];
								$booking_data_arr[$row[csf('color_size_table_id')]][rate]=$row[csf('rate')];
								$booking_data_arr[$row[csf('color_size_table_id')]][amount]+=$row[csf('amount')];
                        	}

							$sql="select a.id,a.style_ref_no,b.id as emb_dtls_id, (b.fin_fab_qnty) AS fin_fab_qnty,c.id as color_size_table_id,c.color_id as color_number_id,c.size_id as size_number_id,c.qnty
							from sample_development_mst a, sample_development_fabric_acc b,sample_develop_embl_color_size c where a.id=b.sample_mst_id and b.id=c.dtls_id and
							b.form_type=3  and b.id in($txtembcostid) and a.entry_form_id=203 and b.form_type=3 and a.status_active=1 and b.status_active=1 and c.status_active=1   and b.fin_fab_qnty>0 order by a.id, b.id";
							$data_arr=sql_select($sql);
							if ( count($data_arr)>0)
							{
								$i=0; $data_dreak="";
								foreach( $data_arr as $row )
								{
									 /*if($cbo_colorsizesensitive==4){
										$txt_req_quantity=$row[csf('qnty')];
										$txtwoq_cal = def_number_format($txt_req_quantity,5,"");
									}*/
									$txt_req_quantity=$row[csf('qnty')];
									$txtwoq_cal = def_number_format($txt_req_quantity,5,"");
									$description=$booking_data_arr[$row[csf('color_size_table_id')]][description];
									$item_color=$booking_data_arr[$row[csf('color_size_table_id')]][item_color];
									if($item_color==0 || $item_color=="" ) $item_color = $row[csf('color_number_id')];
									$item_size=$booking_data_arr[$row[csf('color_size_table_id')]][item_size];
									if($item_size==0 || $item_size == "") $item_size=$size_library[$row[csf('size_number_id')]];
									$feb_description='';
									
									//$cons=$booking_data_arr[$row[csf('color_size_table_id')]][cons];
									
									$rate=$booking_data_arr[$row[csf('color_size_table_id')]][rate];
									if($rate==0 || $rate=="") $rate=$txt_avg_price;

									$amount =$booking_data_arr[$row[csf('color_size_table_id')]][amount];
									$embl_book_cons_dtls_id =$booking_data_arr[$row[csf('color_size_table_id')]][id];

									if($data_dreak=="") $data_dreak.=$description.'_'.$row[csf('color_number_id')].'_'.$row[csf('size_number_id')].'_'.$txtwoq_cal.'_'.$rate.'_'.$amount.'_'.$embl_book_cons_dtls_id.'_0_0_'.$feb_description;
									else $data_dreak.='__'.$description.'_'.$row[csf('color_number_id')].'_'.$row[csf('size_number_id')].'_'.$txtwoq_cal.'_'.$rate.'_'.$amount.'_'.$embl_book_cons_dtls_id.'_0_0_'.$feb_description;
									$i++;
								}
							}
						}
					}
					
					
					//echo $data_dreak;
					/*if(count($data_array)<1)
					{
						$sql = "select id, description, color_number_id, gmts_sizes, requirment, color_size_table_id, rate, amount from wo_emb_book_con_dtls where wo_booking_dtls_id='$booking_dtls_id' and status_active=1 and is_deleted=0 order by id ASC";
					}*/
					$k=0; 
					//$data_array=explode("__",$data_break);
					$data_array=explode("__",$data_dreak);
					
					//print_r($data_array);
					if($within_group==1) $disabled="disabled"; else $disabled="";
					
					if($within_group==2)
					{
						//echo "select sys_no form sub_material_mst where  embl_job_no='$job_no' and status_active=1 and is_deleted=0 and trans_type=1 and entry_form=205"; die;
						$rec_number=return_field_value( "sys_no", "sub_material_mst"," embl_job_no='$job_no' and status_active=1 and is_deleted=0 and trans_type=1 and entry_form=205");
						$recipe_number=return_field_value( "recipe_no", "pro_recipe_entry_mst"," job_no='$job_no' and status_active=1 and is_deleted=0 and entry_form=220");
						//echo  $rec_number; die;
						if($rec_number!=""  || $recipe_number!="")
						{
							if($hdnDtlsUpdateId!="")
							{
								$disabled="disabled";
							} 
							else 
							{
								$disabled="";
							}
						}
						else
						{
							$disabled="";
						}

						$bill_rate=return_field_value( "rate", "subcon_inbound_bill_dtls"," order_id='$hdnDtlsUpdateId' and status_active=1 and is_deleted=0");
						if($bill_rate!="")
						{
							if($hdnDtlsUpdateId!="")
							{
								$rate_disabled="disabled";
							} 
							else 
							{
								$rate_disabled="";
							}
						}
						else
						{
							$rate_disabled="";
						}
					}else{
						$rate_disabled=$disabled;
					} 

					
					
					
					if(count($data_array)>0)
					{
						foreach($data_array as $row)
						{
							$data=explode('_',$row);
							$k++;
							?>
							<tr>
                           		<td  <? echo $is_display; ?> align="center"><input type="checkbox" name="checktr_<? echo $k; ?>" id="checktr_<? echo $k; ?>" onClick="tr_check(<? echo $k; ?>,event)"></td>
								<td><input type="text" id="txtdescription_<? echo $k;?>" name="txtdescription_<? echo $k;?>" class="text_boxes" style="width:120px" value="<? echo $data[0]; ?>"  <? if($within_group==2){ echo $disabled;} ?> />
								</td>
								<td><input type="text" id="txtFebdescription_<? echo $k;?>" name="txtFebdescription_<? echo $k;?>" class="text_boxes" style="width:120px" value="<? echo $data[9]; ?>"  <? if($within_group==2){ echo $disabled;} ?> />
								</td>
								<td>
									<input type="text" id="txtcolor_<? echo $k;?>" onChange="check_duplicate(<? echo $k; ?> ,this.id)" name="txtcolor_<? echo $k;?>" class="text_boxes txt_color" style="width:90px" value="<? echo $color_arr[$data[1]]; ?>" <? echo $disabled; ?> /></td>
								<td><input type="text" id="txtsize_<? echo $k;?>" onChange="check_duplicate(<? echo $k; ?> ,this.id)" name="txtsize_<? echo $k;?>" class="text_boxes txt_size" style="width:70px" value="<? echo $size_arr[$data[2]]; ?>" <? echo $disabled; ?> ></td>
								<td>
									<input type="text" id="txtorderquantity_<? echo $k;?>" name="txtorderquantity_<? echo $k;?>" class="text_boxes_numeric" style="width:60px" onKeyUp="sum_total_qnty(<? echo $k;?>);" value="<? echo number_format($data[3],4,'.',''); ?>"   <? if($within_group==1){ echo $disabled;} ?> />
									<input type="hidden" id="hiddenOrderQuantity_<? echo $k;?>" name="hiddenOrderQuantity_<? echo $k;?>" class="text_boxes_numeric" style="width:70px" value="<? echo $data[3]; ?>"  />
								</td>
								<td><input type="text" id="txtorderrate_<? echo $k;?>" name="txtorderrate_<? echo $k;?>"  class="text_boxes_numeric" style="width:50px" onKeyUp="sum_total_qnty(<? echo $k;?>)" value="<? echo number_format($data[4],4,'.',''); ?>" <? echo $rate_disabled; ?> />
								</td>
								<td><input type="text" id="txtorderamount_<? echo $k;?>" name="txtorderamount_<? echo $k;?>" class="text_boxes_numeric" style="width:70px" value="<? echo number_format($data[5],4,'.',''); ?>" disabled/></td>
                                <td style="display:none"><input type="text" id="txtcuttinno_<? echo $k;?>" name="txtcuttinno_<? echo $k;?>" class="text_boxes_numeric" style="width:70px" value="<? echo $data[8]; ?>" /></td>
                                
								<td>
									<input type="hidden" id="hidbookingconsid_<? echo $k; ?>" name="hidbookingconsid_<? echo $k; ?>"  style="width:15px;" class="text_boxes" value="<? echo $data[6]; ?>" />
                                    <input type="hidden" id="hiddenid_<? echo $k; ?>" name="hiddenid_<? echo $k; ?>"  style="width:15px;" class="text_boxes" value="<? echo $data[7]; ?>" />
									<input type="button" id="increaseset_<? echo $k;?>" style="width:30px" class="formbutton" value="+" onClick="add_share_row(<? echo $k; ?>,this)"    <? if($within_group==1){ echo $disabled;} ?> />
									<input type="button" id="decreaseset_<? echo $k;?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $k;?> ,'tbl_share_details_entry',this);" <? 	echo $disabled; ?>/>
								</td> 
                                 
							</tr>
							<?
						}
					}
					else
					{
						?>
                        <tr>
                        	<td><input type="checkbox" name="checktr_1" id="checktr_1" onClick="tr_check(1,event)"></td>
                            <td><input type="text" id="txtdescription_1" name="txtdescription_1" class="text_boxes" style="width:120px" value="" /></td>
                            <td><input type="text" id="txtFebdescription_1" name="txtFebdescription_1" class="text_boxes" style="width:120px" value="" /></td>
                            <td><input type="text" id="txtcolor_1" name="txtcolor_1" onChange="check_duplicate(1,this.id)" class="text_boxes txt_color" style="width:90px" value="" /></td>
                            <td><input type="text" id="txtsize_1" name="txtsize_1" onChange="check_duplicate(1,this.id)" class="text_boxes txt_size" style="width:70px" value="" ></td>
                            <td>
                                <input type="text" id="txtorderquantity_1" name="txtorderquantity_1" class="text_boxes_numeric" style="width:60px" onKeyUp="sum_total_qnty(1);" value="" />
                                <input type="hidden" id="hiddenOrderQuantity_1" name="hiddenOrderQuantity_1" class="text_boxes_numeric" style="width:70px" value="" />
                            </td>
                            <td><input type="text" id="txtorderrate_1" name="txtorderrate_1"  class="text_boxes_numeric" style="width:50px" onKeyUp="sum_total_qnty(1)" value="" /></td>
                            
                            <td><input type="text" id="txtorderamount_1" name="txtorderamount_1" class="text_boxes_numeric" style="width:70px" value="" disabled/></td>
                            <td style="display:none"><input type="text" id="txtcuttinno_1" name="txtcuttinno_1" class="text_boxes_numeric" style="width:70px" value=""/></td>
                            <td>
                            	<input type="hidden" id="hidbookingconsid_1" name="hidbookingconsid_1"  style="width:15px;" class="text_boxes" value="" />
                                <input type="hidden" id="hiddenid_1" name="hiddenid_1"  style="width:15px;" class="text_boxes" value="" />
                                <input type="button" id="increaseset_1" style="width:30px" class="formbutton" value="+" onClick="add_share_row(1,this)" />
                                <input type="button" id="decreaseset_1" style="width:30px" class="formbutton" value="-"  onClick="javascript:fn_deletebreak_down_tr(1 ,'tbl_share_details_entry',this );" />
                            </td>  
                        </tr>
						<?
					}
					?> 
				</tbody>
				<tfoot>
					<th colspan="<? echo $colspan; ?>">Total</th> 
					<th><input type="text" id="txt_total_order_qnty" name="txt_total_order_qnty" class="text_boxes_numeric" readonly style="width:60px" value="<? echo $break_tot_qty;//number_format($break_tot_qty,4); ?>"; /></th>
					<th><input type="text" id="txt_average_rate" name="txt_average_rate" class="text_boxes_numeric" readonly style="width:50px" value="<? echo $break_avg_rate; ?>"; /></th>
					<th><input type="text" id="txt_total_order_amount" name="txt_total_order_amount" class="text_boxes_numeric" readonly style="width:70px" value="<? echo $break_total_value; ?>"; /></th>
					<th></th>
                    <th></th>
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
	<script>sum_total_qnty(0);</script>        
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}



if ($action=="printing_order_entry")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data); die;


	$jobno=''; $update_id=0;
	$update_id=$data[1];
	$jobno=$data[3];
	$bundle_variable=$data[8];
	$within_group=$data[6];

	

	$sql= "SELECT id, party_id, party_location, receive_date , delivery_date, within_group, wo_type, order_no, rec_start_date, rec_end_date from subcon_ord_mst where id='$data[1]' and status_active =1 and is_deleted=0";

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
						Plot No: <? echo $result[csf('plot_no')]; ?>
						Level No: <? echo $result[csf('level_no')]?>
						Road No: <? echo $result[csf('road_no')]; ?>
						Block No: <? echo $result[csf('block_no')];?>
						City No: <? echo $result[csf('city')];?>
						Zip Code: <? echo $result[csf('zip_code')]; ?>
						Province No: <? echo $result[csf('province')];?>
						Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
						Email Address: <? echo $result[csf('email')];?>
						Website No: <? echo $result[csf('website')];
					}
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="3" align="center" style="font-size:18px"><strong><u><? echo $data[7]; ?></u></strong></td>
            
        </tr>
        <tr>
        	<td width="85"><strong>Within Group</strong></td>
            <td width="125px"><strong>: </strong><? echo $yes_no[$dataArray[0][csf('within_group')]]; ?></td>
            <td width="100"><strong>Party</strong></td>
            <td width="175px"><strong>: </strong><? echo $buyer_arr[$dataArray[0][csf('party_id')]]; ?></td>
            <td width="110"><strong> Party Location </strong></td>
            <td><strong>: </strong><? echo $location_arr[$dataArray[0][csf('party_location')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Ord. Receive Date</strong></td>
            <td><strong>: </strong><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
            <td><strong> Rcv. Start Date </strong></td>
            <td><strong>: </strong><? echo change_date_format($dataArray[0][csf('rec_start_date')]); ?></td>
            <td><strong>Delivery Date</strong></td>
            <td width="175px"><strong>: </strong><? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
        </tr>
         <tr>
			<td><strong>Rcv. end Date</strong></td>
			<td><strong>: </strong><? echo change_date_format($dataArray[0][csf('rec_end_date')]); ?> </td>
           	<td><strong>Work Order Type</strong></td>
           	<td ><strong>: </strong><? echo $wo_type_arr[$dataArray[0][csf('wo_type')]]; ?></td>
			<td><strong>Work Order</strong></td>
			<td><strong>: </strong><? echo $dataArray[0][csf('order_no')]; ?> </td>
       </tr>
	  
    </table>
	<div style="width:100%;">
    <table cellspacing="0" width="1160"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" style="font-size:13px">
            <th width="30">SL</th>
            <th width="100">Buyer PO</th>
            <th width="100">Style Ref.</th>
            <th width="100">Gmts. Item</th>
            <th width="100">Process /Embl. Name</th>
            <th width="120">Embl. Type</th>
            <th width="120">Body Part</th>
            <th width="80">Embelishment Description</th>
            <th width="80">Color</th>
            <th width="80"> Size</th>
            <th width="60">Order UOM</th>
            <th width="80">Order Qty</th>
            <th width="80">Rate/Dzn</th>
            <th width="80">Amount</th>
            <th width="80">Domestic Amount</th>
            <th width="80">Quantity (Pcs)</th>
            <th width="80">SMV</th>
            <th width="80">Delivery Date</th>
            <th width="80">Buyers Buyer</th>
            <th>Wastage %</th>
        </thead>
        <tbody style="font-size:11px">
	<?


	$color_arrey=return_library_array( "SELECT id,color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
	$size_arrey=return_library_array( "SELECT id,size_name from  lib_size where status_active =1 and is_deleted=0",'id','size_name');
	$buyer_po_arr=array();
	$buyer_name=return_library_array( "SELECT id,buyer_name from lib_buyer where status_active =1 and is_deleted=0",'id','buyer_name');
	//$buyer_arr=return_library_array( "select id,buyer_name from  lib_buyer", "id","buyer_name"  );


	$sql_job="SELECT a.id,b.buyer_po_no,b.buyer_style_ref ,b.buyer_buyer , b.gmts_item_id, b.embl_type, b.body_part,
	b.order_uom, c.rate, b.amount, b.domestic_amount, b.smv, b.delivery_date, b.wastage, b.buyer_po_id, b.main_process_id, c.id as breakdown_id, c.description, c.color_id, c.size_id, c.qnty, c.amount as amounts, a.exchange_rate
	from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
	where a.entry_form=204 and a.embellishment_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id  and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and a.id=$update_id and c.qnty>0 order by c.id ASC";
     //echo $sql_job; die;

	$sql_result =sql_select($sql_job);


	//$k=0; 
	$i=1; $pre_recei_qty=0; $receive_qty=0;
	$num_rowss=count($sql_result);
	foreach ($sql_result as $row)
	{
		 $rates =$row[csf("rate")];
		 $amount= $row[csf("amount")];
         $amounts= $row[csf("amounts")];
		 $exchange = $row[csf("exchange_rate")];
		 $qty = $row[csf("qnty")];

		 $domestic_ammount =$amounts*$exchange ;
               
		//  if( $row[csf("order_uom")]==1){
		// 	 $quantity_pcs= $qty/1;
		//  }if( $row[csf("order_uom")]==2){
		// 	$quantity_pcs= $qty/12;
		// }if( $row[csf("order_uom")]==3){
		// 	$quantity_pcs= $qty/24;
		// }if( $row[csf("order_uom")]==4){
		// 	$quantity_pcs= $qty/36;
		// }if( $row[csf("order_uom")]==5){
		// 	$quantity_pcs= $qty/48;
		// }
        $order_uom=0;
		$order_uom=$row[csf("order_uom")];

		if($order_uom==2){
			$qty_pcs=$qty*12;
		}else if($order_uom==1){
			$qty_pcs=$qty;
		}else{
			$qty_pcs=0;
		}

	
	?>
		<tr bgcolor="<? echo $bgcolor; ?>">
			<td align="center"><? echo $i; ?></td>
			<td align="center"><? echo $row[csf("buyer_po_no")]; ?></td>
			<td align="center"><? echo $row[csf("buyer_style_ref")]; ?></td>
			<td align="center"><? echo $garments_item[$row[csf("gmts_item_id")]]; ?></td>
			<td align="center"><? echo $emblishment_name_array[$row[csf("main_process_id")]]; ?></td>
			<td align="center"><? echo$emblishment_print_type_arr[$row[csf("embl_type")]]; ?></td>
            <td align="center"><? echo $body_part[$row[csf("body_part")]]; ?></td>
            <td align="center"><? echo $row[csf("description")]; ?></td>
            <td align="center"><? echo $color_arrey[$row[csf("color_id")]]; ?></td>
			<td align="center"><? echo $size_arrey[$row[csf("size_id")]]; ?></td>
			<td align="center"><? echo $unit_of_measurement[$row[csf("order_uom")]]; ?></td>
			<td align="right"><? echo  number_format($qty,2); ?></td>
			<td align="center"><? echo  number_format($rates,2) ?></td>
			<td align="right"><? echo  number_format($row[csf("amounts")],2); ?></td>
			<td align="right"><? echo number_format($domestic_ammount,2); ?></td>
			<td align="right"><? echo number_format($qty_pcs,2); ?></td>
			<td align="right"><? echo  $row[csf("smv")]; ?></td>
			<td align="right"><? echo  change_date_format( $row[csf("delivery_date")]); ?></td>
			<td align="right"><? echo  $buyer_name[$row[csf("buyer_buyer")]]; ?></td>
			<td align="center"><? echo $row[csf("wastage")]; ?></td>
		</tr>
		<? 

		$domestic_amount+=$domestic_ammount;
		$receive_qty+= $amounts;
		$qunty+=$qty;
		$quantity_pcs+=$qty_pcs;

		$i++; 
	} 

	?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="11" align="right">Grand Total :</td>
            <td align="right"><? echo number_format($qunty,2); ?></td>
			<td>&nbsp;</td>
            <td align="right"><? echo number_format($receive_qty,2); ?></td>
            <td align="right"><? echo number_format($domestic_amount,2); ?></td>
            <td align="right"><? echo number_format($quantity_pcs,2); ?></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
    </tfoot>
    </table>
		<br>
	     	<? 
			   	echo get_spacial_instruction($data[1],"1160px",204);
			?>
        <br>
		 <?
           // echo signature_table(157, $data[0], "1160px");
         ?>
	</div>
	</div>
     <script type="text/javascript" src="../../../js/jquery.js"></script>
      
	<?
	exit();
}





/*if($action=="show_fabric_booking_report")
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$show_yarn_rate=str_replace("'","",$show_yarn_rate);
	
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1 and master_tble_id='$cbo_company_name'",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');

	$location_name_arr=return_library_array( "select id,location_name from lib_location",'id','location_name');
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	//$po_qnty_tot1=return_field_value( "sum(po_quantity)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	$pro_sub_dept_array=return_library_array( "select id,sub_department_name from lib_pro_sub_deparatment",'id','sub_department_name');
	?>
	<div style="width:1330px" align="center">
    <?php
		$nameArray_approved = sql_select("select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7");
		list($nameArray_approved_row) = $nameArray_approved;
		$nameArray_approved_date = sql_select("select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
		list($nameArray_approved_date_row) = $nameArray_approved_date;
		$nameArray_approved_comments = sql_select("select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
		list($nameArray_approved_comments_row) = $nameArray_approved_comments;

		?>										<!--    Header Company Information         -->
        <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black; font-family:Arial Narrow;" >
            <tr>
                <td width="100"><img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' /></td>
                <td width="1250">
                    <table width="100%" cellpadding="0" cellspacing="0"  >
                        <tr>
                            <td align="center" style="font-size:20px;"><?php echo $company_library[$cbo_company_name]; ?></td>
                            <td rowspan="3" width="250">
                                <span style="font-size:18px"><b> Job No:&nbsp;&nbsp;<? echo trim($txt_job_no,"'"); ?></b></span><br/>
                                <?
                                if($nameArray_approved_row[csf('approved_no')]>1)
                                {
									?>
									<b> Revised No: <? echo $nameArray_approved_row[csf('approved_no')]-1; ?></b>
									<br/>
									Approved Date: <? echo $nameArray_approved_date_row[csf('approved_date')]; ?>
									<?
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">
                            <?
                            $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
                            if($txt_job_no!="") $location=return_field_value( "location_name", "wo_po_details_master","job_no='$txt_job_no'");
                            else $location="";
                            
                            foreach ($nameArray as $result)
                            {
								echo  $location_name_arr[$location];
								?>
								<!-- Plot No: <? //echo $result[csf('plot_no')]; ?>
								Level No: <? //echo $result[csf('level_no')]?>
								Road No: <? //echo $result[csf('road_no')]; ?>
								Block No: <? //echo $result[csf('block_no')];?>
								City No: <? //echo $result[csf('city')];?>
								Zip Code: <? //echo $result[csf('zip_code')]; ?>
								Province No: <?php //echo $result[csf('province')];?>
								Country: <? //echo $country_arr[$result[csf('country_id')]]; ?> --><br>
								Email Address: <? echo $result[csf('email')];?>
								Website No: <? echo $result[csf('website')]; ?>
								<?
                            }
							 if($txt_booking_no!="") $quality_level=return_field_value( "quality_level", "wo_booking_mst","booking_no=$txt_booking_no");
                            ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:20px">
                            	<strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<? if($report_title !=""){ echo $report_title;} else { echo "General Work Order";}?> &nbsp; &nbsp;&nbsp;<font style="color:#F00"><? if(str_replace("'","",$id_approved_id) ==1){ echo "(Approved)";}else{echo "";};  ?>
                                 </font></strong><b style="font-size: larger">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<? 
								 if($quality_level>0) echo $fbooking_order_nature[$quality_level];else echo " &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; "; ?></b>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
		<?
		
        $sample_booking_arr=array();
        $namesamplefab=sql_select( "select a.booking_no , sum(b.grey_fab_qnty) as grey_fab_qnty  from wo_booking_mst a, wo_booking_dtls b where  a.job_no=b.job_no and a.booking_no=b.booking_no  and a.tagged_booking_no=$txt_booking_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  group by a.booking_no");
        foreach($namesamplefab as $namesamplefabrow){
            $sample_booking_arr['booking_no'][$namesamplefabrow[csf('booking_no')]]=$namesamplefabrow[csf('booking_no')];
            $sample_booking_arr['grey_fab_qnty'][$namesamplefabrow[csf('booking_no')]]=$namesamplefabrow[csf('grey_fab_qnty')];
        }
        $sample_booking_no=implode($sample_booking_arr['booking_no']);
        $sample_booking_qty=array_sum($sample_booking_arr['grey_fab_qnty']);
		
        $job_no=''; $total_set_qnty=0; $colar_excess_percent=0; $cuff_excess_percent=0; $rmg_process_breakdown=0; $booking_percent=0; $booking_po_id='';
		
        $nameArray=sql_select( "select a.booking_no, a.booking_date, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.po_break_down_id, a.colar_excess_percent, a.cuff_excess_percent, a.delivery_date, a.is_apply_last_update, a.fabric_source, a.rmg_process_breakdown, a.insert_date, a.update_date, a.tagged_booking_no, a.uom, a.pay_mode, a.booking_percent, b.job_no, b.buyer_name, b.style_ref_no, b.gmts_item_id, b.order_uom, b.total_set_qnty, b.style_description, b.season_buyer_wise as season, b.product_dept, b.product_code, b.pro_sub_dep, b.dealing_marchant, b.order_repeat_no, b.repeat_job_no, a.fabric_composition, a.remarks from wo_booking_mst a, wo_po_details_master b where a.job_no=b.job_no and a.booking_no=$txt_booking_no");
		
		$po_id_all=$nameArray[0][csf('po_break_down_id')];
		$booking_uom=$nameArray[0][csf('uom')];
		$bookingup_date=$nameArray[0][csf('update_date')];
		$bookingins_date=$nameArray[0][csf('insert_date')];
		
		if($db_type==0)
        {
            $date_dif_cond="DATEDIFF(pub_shipment_date,po_received_date)";
            $group_concat_all="group_concat(grouping) as grouping, group_concat(file_no) as file_no";
        }
        else
        {
            $date_dif_cond="(pub_shipment_date-po_received_date)";
            $group_concat_all=" listagg(cast(grouping as varchar2(4000)),',') within group (order by grouping) as grouping,
                                listagg(cast(file_no as varchar2(4000)),',') within group (order by file_no) as file_no  ";
        }
        $po_number_arr=array(); $po_ship_date_arr=array(); $shipment_date=""; $po_no=""; $po_received_date=""; $shiping_status="";
        $po_sql=sql_select("select id, po_number, pub_shipment_date, MIN(pub_shipment_date) as mpub_shipment_date, MIN(po_received_date) as po_received_date, MIN(insert_date) as insert_date, plan_cut, po_quantity, shiping_status, $date_dif_cond as date_diff, $group_concat_all from wo_po_break_down where id in(".$po_id_all.") group by id, po_number, pub_shipment_date, plan_cut, po_quantity, shiping_status, po_received_date");
        foreach($po_sql as $row)
        {
            $po_qnty_tot+=$row[csf('plan_cut')];
            $po_qnty_tot1+=$row[csf('po_quantity')];
            $po_number_arr[$row[csf('id')]]=$row[csf('po_number')];
            $po_ship_date_arr[$row[csf('id')]]=$row[csf('pub_shipment_date')];
            $po_num_arr[$row[csf('id')]]=$row[csf('po_number')];
            $po_no.=$row[csf('po_number')].", ";
            $shipment_date.=change_date_format($row[csf('mpub_shipment_date')],'dd-mm-yyyy','-').", ";
            $lead_time.=$row[csf('date_diff')].",";
            $po_received_date=change_date_format($row[csf('po_received_date')],'dd-mm-yyyy','-');
            $grouping.=$row[csf('grouping')].",";
            $file_no.=$row[csf('file_no')].",";
			
			$daysInHand.=(datediff('d',date('d-m-Y',time()),$row[csf('mpub_shipment_date')])-1).",";
			
			if($bookingup_date=="" || $bookingup_date=="0000-00-00 00:00:00")
			{
				$booking_date=$bookingins_date;
			}
			$WOPreparedAfter.=(datediff('d',$row[csf('insert_date')],$booking_date)-1).",";

			if($row[csf('shiping_status')]==1) $shiping_status.= "FP".",";
			else if($row[csf('shiping_status')]==2) $shiping_status.= "PS".",";
			else if($row[csf('shiping_status')]==3) $shiping_status.= "FS".",";
        }
		
		$po_no=implode(",",array_filter(array_unique(explode(",",$po_no))));
		$shipment_date=implode(",",array_filter(array_unique(explode(",",$shipment_date))));
		$lead_time=implode(",",array_filter(array_unique(explode(",",$lead_time))));
		$po_received_date=implode(",",array_filter(array_unique(explode(",",$po_received_date))));
		$grouping=implode(",",array_filter(array_unique(explode(",",$grouping))));
		$file_no=implode(",",array_filter(array_unique(explode(",",$file_no))));
		
		$daysInHand=implode(",",array_filter(array_unique(explode(",",$daysInHand))));
		$WOPreparedAfter=implode(",",array_filter(array_unique(explode(",",$WOPreparedAfter))));
		$shiping_status=implode(",",array_filter(array_unique(explode(",",$shiping_status))));
		
        foreach ($nameArray as $result)
        {
            $total_set_qnty=$result[csf('total_set_qnty')];
            $colar_excess_percent=$result[csf('colar_excess_percent')];
            $cuff_excess_percent=$result[csf('cuff_excess_percent')];
            $rmg_process_breakdown=$result[csf('rmg_process_breakdown')];
            
            $booking_percent=$result[csf('booking_percent')];
			 $booking_po_id=$result[csf('po_break_down_id')];      

			?>
			<table width="100%" style="border:1px solid black; font-family:Arial Narrow;" >
				<tr>
					<td colspan="6" valign="top" style="font-size:18px; color:#F00"><? if($result[csf('is_apply_last_update')]==2){echo "Booking Info not synchronized with order entry and pre-costing. order entry or pre-costing has updated after booking entry.  Contact to ".$marchentrArr[$result[csf('dealing_marchant')]]; } else{ echo "";} ?></td>
				</tr>
				<tr>
					<td width="100"><span style="font-size:18px"><b>Buyer/Agent Name</b></span></td>
					<td width="110">:&nbsp;<span style="font-size:18px"><b><? echo $buyer_name_arr[$result[csf('buyer_name')]]; ?></b></span></td>
					<td width="100"><span style="font-size:16px;"><b>Dept. <? if($result[csf('product_code')] !=""){ echo " (Prod Code)";} if($result[csf('pro_sub_dep')] !=0){ echo " (Sub Dep)";} ?></b></span></td>
					<td width="110" style="font-size:16px;">:&nbsp;<?=$product_dept[$result[csf('product_dept')]] ; if($result[csf('product_code')] !=""){ echo " (".$result[csf('product_code')].")";} if($result[csf('pro_sub_dep')] !=0){ echo " (".$pro_sub_dept_array[$result[csf('pro_sub_dep')]].")";}?></td>
					<td width="100"><span style="font-size:16px;"><b>Order Qty</b></span></td>
					<td width="110" style="font-size:16px;">:&nbsp;<?=$po_qnty_tot1." ".$unit_of_measurement[$result[csf('order_uom')]]; ?></td>
				</tr>
				<tr>
					<td width="100" style="font-size:16px;"><b>Garments Item</b></td>
					<td width="110" style="font-size:16px;">:&nbsp;
						<?
                        $gmts_item_name="";
                        $gmts_item=explode(',',$result[csf('gmts_item_id')]);
                        for($g=0;$g<=count($gmts_item); $g++)
                        {
                            $gmts_item_name.= $garments_item[$gmts_item[$g]].",";
                        }
                        echo rtrim($gmts_item_name,',');
                        ?>
					</td>
					<td width="100" style="font-size:16px;"><b>Booking Release Date</b></td>
					<td width="110" style="font-size:16px;">:&nbsp;
						<?
                        if($booking_date=="" || $booking_date=="0000-00-00 00:00:00")
                        {
                        }
                        $booking_date=$result[csf('insert_date')];
                        echo change_date_format($booking_date,'dd-mm-yyyy','-','');
                        ?>&nbsp;&nbsp;&nbsp;</td>
					<td width="100" style="font-size:18px"><b>Style Ref.</b>   </td>
					<td width="110" style="font-size:18px">:&nbsp;<b><? echo $result[csf('style_ref_no')];?> </b>   </td>
				</tr>
				<tr>
					<td width="100" style="font-size:16px;"><b>Style Des.</b></td>
					<td width="110"style="font-size:16px;" >:&nbsp;<? echo $result[csf('style_description')]; $job_no= $result[csf('job_no')];?></td>
					<td width="100" style="font-size:16px"><b>Lead Time </b>   </td>
					<td width="110" style="font-size:16px;">:&nbsp;<?  echo rtrim($lead_time,",");?> </td>
					<td width="100" style="font-size:12px"><b>Dealing Merchant</b></td>
					<td width="110">:&nbsp;<? echo $marchentrArr[$result[csf('dealing_marchant')]]; ?></td>
				</tr>
				<tr>
					<td width="100" style="font-size:16px;"><b>Supplier Name</b>   </td>
					<td width="110" style="font-size:16px;">:&nbsp;
					<?
					if($result[csf('pay_mode')]==5 || $result[csf('pay_mode')]==3) echo $company_library[$result[csf('supplier_id')]];
					else echo $supplier_name_arr[$result[csf('supplier_id')]];
					?></td>
					<td width="100" style="font-size:16px;"><b>Delivery Date</b></td>
					<td width="110" style="font-size:16px;">:&nbsp;<? echo change_date_format( $result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>
					<td width="100" style="font-size:18px"><b>Booking No </b>   </td>
					<td width="110" style="font-size:18px">:&nbsp;<b><? echo $result[csf('booking_no')];?></b><? echo "(".$fabric_source[$result[csf('fabric_source')]].")"?></td>
					<?
					if($db_type==0)
					{
						$last_update_date=return_field_value("max(date(update_date)) as update_date","wo_booking_dtls","status_active=1 and is_deleted=0 and booking_no=$txt_booking_no","update_date");
					}
					else
					{
						$last_update_date=return_field_value("max(to_char(update_date,'DD-MM-YYYY')) as update_date","wo_booking_dtls","status_active=1 and is_deleted=0 and booking_no=$txt_booking_no","update_date");
					}
					?>
				</tr>
				<tr>
					<td width="100" style="font-size:16px;"><b>Season</b></td>
					<td width="110" style="font-size:16px;">:&nbsp;<? echo $season_name_arr[$result[csf('season')]]; ?></td>
					<td width="100" style="font-size:16px"><b>Last Update Date</b></td>
					<td width="100" style="font-size:18px">:&nbsp;<b><? if($last_update_date!="" && $last_update_date!="0000-00-00") echo change_date_format($last_update_date);?></b></td>
					<td width="100" style="font-size:16px;"><b>Po Received Date</b></td>
					<td width="110" style="font-size:16px;">:&nbsp;<? echo $po_received_date; ?></td>
				</tr>
				<tr>
				   <td width="100" style="font-size:18px"><b>Order No</b></td>
				   <td style="font-size:18px; word-break:break-all" colspan="5">:&nbsp;<b><? echo rtrim($po_no,", "); ?></b></td>
				</tr>
				<tr>
				   	<td width="100" style="font-size:16px;"><b>Shipment Date</b></td>
					<td width="110" colspan="5" style="font-size:16px;"> :&nbsp;<? echo rtrim($shipment_date,", "); ?></td>
				</tr>
				<tr>
				   <td width="100" style="font-size:16px;"><b>WO Prepared After</b></td>
				   <td width="110" style="font-size:16px;"> :&nbsp;<? echo rtrim($WOPreparedAfter,',').' Days' ?></td>
				   <td width="100" style="font-size:12px;"><b>Ship.days in Hand</b></td>
				   <td width="110" style="font-size:16px;"> :&nbsp;<? echo rtrim($daysInHand,',').' Days'?></td>
				   <td width="100" style="font-size:12px"><b>Ex-factory status</b></td>
				   <td width="110"> :&nbsp;<? echo rtrim($shiping_status,','); ?></td>
				</tr>
			   <tr>
				   <td width="100" style="font-size:18px"><b>Internal Ref No</b></td>
				   <td width="110" style="font-size:18px"> :&nbsp;<b><? echo implode(",",array_unique(explode(",",$grouping))); ?></b></td>
				   <td width="100" style="font-size:18px"><b>File no</b></td>
				   <td width="110" style="font-size:18px"> :&nbsp;<b><? echo  implode(",",array_unique(explode(",",$file_no)));?></b></td>
				   <td width="100" style="font-size:18px"><b>Order Repeat No</b></td>
				   <td width="110" style="font-size:18px"> :&nbsp;<b><? echo  $result[csf('order_repeat_no')];?></b></td>
				</tr>
				<tr>
				   <td width="100" style="font-size:18px"><b>Remarks</b></td>
				   <td style="font-size:18px" colspan="3"> :<? echo $result[csf('remarks')]?></td>
				   <td width="100" style="font-size:18px"><b>Order Repeat Job No</b></td>
				   <td width="110" style="font-size:18px"> :&nbsp;<b><? echo $result[csf('repeat_job_no')];?></b></td>
				</tr>
				<tr>
				   <td width="130" style="font-size:18px"><b>Fabric Composition</b></td>
				   <td style="font-size:18px" colspan="5"> :<? echo $result[csf('fabric_composition')]?></td>
				</tr>
				<tr>
				   <td width="130" style="font-size:18px"><b>Attention</b></td>
				   <td style="font-size:18px" colspan="5"> : &nbsp; <? echo $result[csf('attention')]?></td>
				</tr>
				<?php if ($sample_booking_no != '') {?>
					<tr>
						<td colspan="5" align="center">
					  <strong> <? echo "Fabric of Sample Fabric Booking No:".$sample_booking_no.", Total Fabric=".$sample_booking_qty." KG,	will be Dyed with this fabric."; ?> </strong>
						</td>
					</tr>
				<?php }?>
			</table>
			<?
		}
		
		if($cbo_fabric_source==1)
		{
			$nameArray_size=sql_select( "select size_number_id,min(id) as id, min(size_order) as size_order from wo_po_color_size_breakdown where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and is_deleted=0 and status_active=1 group by size_number_id order by size_order");
			?>
			<table width="100%" style="font-family:Arial Narrow;" >
                <tr>
                    <td width="800">
                        <div id="div_size_color_matrix" style="float:left; max-width:1000;">
                            <fieldset id="div_size_color_matrix" style="max-width:1000;">
                                <legend>Size and Color Breakdown</legend>
                                <table  class="rpt_table"  border="1" align="left" cellpadding="0" width="750" cellspacing="0" rules="all" >
                                    <tr>
                                        <td style="border:1px solid black"><strong>Color/Size</strong></td>
                                        <?
                                        foreach($nameArray_size  as $result_size)
                                        {
											?>
                                        	<td align="center" style="border:1px solid black"><strong><?=$size_library[$result_size[csf('size_number_id')]];?></strong></td>
                                        <? } ?>
                                        <td style="border:1px solid black; width:130px" align="center"><strong> Total Order Qty(Pcs)</strong></td>
                                        <td style="border:1px solid black; width:80px" align="center"><strong> Excess %</strong></td>
                                        <td style="border:1px solid black; width:130px" align="center"><strong> Total Plan Cut Qty(Pcs)</strong></td>
                                    </tr>
                                    <?
                                    $color_size_order_qnty_array=array(); $color_size_qnty_array=array(); $size_tatal=array(); $size_tatal_order=array();
                                    for($c=0;$c<count($gmts_item); $c++)
                                    {
										$item_size_tatal=array(); $item_size_tatal_order=array(); $item_grand_total=0; $item_grand_total_order=0;
										$nameArray_color=sql_select( "select  color_number_id,min(id) as id,min(color_order) as color_order from wo_po_color_size_breakdown where  item_number_id=$gmts_item[$c] and po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and is_deleted=0 and status_active=1 group by color_number_id  order by color_order");
										?>
										<tr>
											<td style="border:1px solid black; text-align:center;" colspan="<? echo count($nameArray_size)+3;?>"><strong><? echo $garments_item[$gmts_item[$c]];?></strong></td>
										</tr>
										<?
										foreach($nameArray_color as $result_color)
										{
											?>
											<tr>
                                                <td align="center" style="border:1px solid black"><?=$color_library[$result_color[csf('color_number_id')]]; ?></td>
                                                <?
                                                $color_total=0; $color_total_order=0;
                                                foreach($nameArray_size  as $result_size)
                                                {
													$nameArray_color_size_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as  order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$result_color[csf('color_number_id')]."  and item_number_id=$gmts_item[$c] and  status_active=1 and is_deleted =0");
													foreach($nameArray_color_size_qnty as $result_color_size_qnty)
													{
														?>
														<td style="border:1px solid black; text-align:center; font-size:18px;">
														<?
														if($result_color_size_qnty[csf('plan_cut_qnty')]!= "")
														{
															echo number_format($result_color_size_qnty[csf('order_quantity')],0);
															$color_total += $result_color_size_qnty[csf('plan_cut_qnty')] ;
															$color_total_order += $result_color_size_qnty[csf('order_quantity')] ;
															$item_grand_total+=$result_color_size_qnty[csf('plan_cut_qnty')];
															$item_grand_total_order+=$result_color_size_qnty[csf('order_quantity')];
															$grand_total +=$result_color_size_qnty[csf('plan_cut_qnty')];
															$grand_total_order +=$result_color_size_qnty[csf('order_quantity')];
															
															$color_size_qnty_array[$result_size[csf('size_number_id')]][$result_color[csf('color_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')];
															$color_size_order_qnty_array[$result_size[csf('size_number_id')]][$result_color[csf('color_number_id')]]=$result_color_size_qnty[csf('order_quantity')];
															if (array_key_exists($result_size[csf('size_number_id')], $size_tatal))
															{
																$size_tatal[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('plan_cut_qnty')];
																$size_tatal_order[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('order_quantity')];
															}
															else
															{
																$size_tatal[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')];
																$size_tatal_order[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('order_quantity')];
															}
															if (array_key_exists($result_size[csf('size_number_id')], $item_size_tatal))
															{
																$item_size_tatal[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('plan_cut_qnty')];
																$item_size_tatal_order[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('order_quantity')];
															}
															else
															{
																$item_size_tatal[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')];
																$item_size_tatal_order[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('order_quantity')];
															}
														}
														else echo "0";
														?>
														</td>
														<?
													}
                                                }
                                                ?>
                                                <td style="border:1px solid black; text-align:center; font-size:18px;"><?=number_format(round($color_total_order),0); ?></td>
                                                <td style="border:1px solid black; text-align:center; font-size:18px;"><? $excexss_per=($color_total-$color_total_order)/$color_total_order*100; echo number_format($excexss_per,2)." %"; ?></td>
                                                <td style="border:1px solid black; text-align:center; font-size:18px;"><?=number_format(round($color_total),0); ?></td>
											</tr>
											<?
										}
										?>
										
										<td align="center" style="border:1px solid black"><strong>Sub Total</strong></td>
										<?
										foreach($nameArray_size  as $result_size)
										{
											?>
											<td style="border:1px solid black;  text-align:center; font-size:18px;"><? echo $item_size_tatal_order[$result_size[csf('size_number_id')]];  ?></td>
											<?
										}
										?>
										<td style="border:1px solid black;  text-align:center; font-size:18px;"><?  echo number_format(round($item_grand_total_order),0); ?></td>
										<td style="border:1px solid black;  text-align:center; font-size:18px;"><? $excess_item_gra_tot=($item_grand_total-$item_grand_total_order)/$item_grand_total_order*100; echo number_format($excess_item_gra_tot,2)." %"; ?></td>
										<td style="border:1px solid black;  text-align:center; font-size:18px;"><?  echo number_format(round($item_grand_total),0); ?></td>
										</tr>
										<?
                                    }
                                    ?>
                                    <tr>
                                    	<td style="border:1px solid black; font-size:18px;" align="center" colspan="<? echo count($nameArray_size)+3; ?>"><strong>&nbsp;</strong></td>
                                    </tr>
                                    <tr>
                                        <td align="center" style="border:1px solid black"><strong>Grand Total</strong></td>
                                        <?
                                        foreach($nameArray_size  as $result_size)
                                        {
											?>
											<td style="border:1px solid black; text-align:center; font-size:18px;"><? echo $size_tatal_order[$result_size[csf('size_number_id')]]; ?></td>
											<?
                                        }
                                        ?>
                                        <td style="border:1px solid black; text-align:center; font-size:18px;"><?=number_format(round($grand_total_order),0); ?></td>
                                        <td style="border:1px solid black; text-align:center; font-size:18px;"><? $excess_gra_tot=($grand_total-$grand_total_order)/$grand_total_order*100; echo number_format($excess_gra_tot,2)." %"; ?></td>
                                        <td style="border:1px solid black; text-align:center; font-size:18px;"><?  echo number_format(round($grand_total),0); ?></td>
                                    </tr>
                                </table>
                            </fieldset>
                        </div>
                    </td>
                    <td width="200" valign="top" align="left">
                        <div id="div_size_color_matrix" style="float:left;">
							<? $rmg_process_breakdown_arr=explode('_',$rmg_process_breakdown); ?>
                            <fieldset id="" >
                                <legend>RMG Process Loss % </legend>
                                <table width="180" class="rpt_table" border="1" rules="all">
									<?
                                    if(number_format($rmg_process_breakdown_arr[8],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130">Cut Panel rejection <!-- Extra Cutting % breack Down 8--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[8],2); ?></td>
										</tr>
										<?
                                    }
                                    if(number_format($rmg_process_breakdown_arr[2],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130">Chest Printing <!-- Printing % breack Down 2--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[2],2); ?></td>
										</tr>
										<?
                                    }
                                    if(number_format($rmg_process_breakdown_arr[10],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130">Neck/Sleeve Printing <!-- New breack Down 10--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[10],2); ?></td>
										</tr>
										<?
                                    }
                                    if(number_format($rmg_process_breakdown_arr[1],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130">Embroidery   <!-- Embroidery  % breack Down 1--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[1],2); ?></td>
										</tr>
										<?
                                    }
                                    if(number_format($rmg_process_breakdown_arr[4],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130">Sewing /Input<!-- Sewing % breack Down 4--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[4],2); ?></td>
										</tr>
										<?
                                    }
                                    if(number_format($rmg_process_breakdown_arr[3],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130">Garments Wash <!-- Washing %breack Down 3--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[3],2); ?></td>
										</tr>
										<?
                                    }
                                    if(number_format($rmg_process_breakdown_arr[15],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130">Gmts Finishing <!-- Washing %breack Down 3--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[15],2); ?></td>
										</tr>
										<?
                                    }
                                    if(number_format($rmg_process_breakdown_arr[11],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130">Others <!-- New breack Down 11--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[11],2); ?></td>
										</tr>
										<?
                                    }
                                    $gmts_pro_sub_tot=$rmg_process_breakdown_arr[8]+$rmg_process_breakdown_arr[2]+$rmg_process_breakdown_arr[10]+$rmg_process_breakdown_arr[1]+$rmg_process_breakdown_arr[4]+$rmg_process_breakdown_arr[3]+$rmg_process_breakdown_arr[11]+$rmg_process_breakdown_arr[15];
                                    if($gmts_pro_sub_tot>0)
                                    {
										?>
										<tr>
                                            <td width="130">Sub Total <!-- New breack Down 11--></td>
                                            <td align="right"><? echo number_format($gmts_pro_sub_tot,2); ?></td>
										</tr>
										<?
                                    }
                                    ?>
                                </table>
                            </fieldset>
                            <fieldset id="" >
                                <legend>Fabric Process Loss % </legend>
                                <table width="180" class="rpt_table" border="1" rules="all" style="font-family:Arial Narrow;">
                                    <?
                                    if(number_format($rmg_process_breakdown_arr[6],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130">Knitting  <!--  Knitting % breack Down 6--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[6],2); ?></td>
										</tr>
										<?
                                    }
                                    if(number_format($rmg_process_breakdown_arr[12],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130">Yarn Dyeing  <!--  New breack Down 12--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[12],2); ?></td>
										</tr>
										<?
                                    }
                                    if(number_format($rmg_process_breakdown_arr[5],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130">Dyeing & Finishing  <!-- Finishing % breack Down 5--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[5],2); ?></td>
										</tr>
										<?
                                    }
                                    if(number_format($rmg_process_breakdown_arr[13],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130"> All Over Print <!-- new  breack Down 13--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[13],2); ?></td>
										</tr>
										<?
                                    }
                                    if(number_format($rmg_process_breakdown_arr[14],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130">Lay Wash (Fabric) <!-- new  breack Down 14--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[14],2); ?></td>
										</tr>
										<?
                                    }
                                    if(number_format($rmg_process_breakdown_arr[7],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130">Dying   <!-- breack Down 7--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[7],2); ?></td>
										</tr>
										<?
                                    }
                                    if(number_format($rmg_process_breakdown_arr[0],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130">Cutting (Fabric) <!-- Cutting % breack Down 0--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[0],2); ?></td>
										</tr>
										<?
                                    }
                                    if(number_format($rmg_process_breakdown_arr[9],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130">Others  <!-- Others% breack Down 9--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[9],2); ?></td>
										</tr>
										<?
                                    }

                                    $fab_proce_sub_tot=$rmg_process_breakdown_arr[6]+$rmg_process_breakdown_arr[12]+$rmg_process_breakdown_arr[5]+$rmg_process_breakdown_arr[13]+$rmg_process_breakdown_arr[14]+$rmg_process_breakdown_arr[7]+$rmg_process_breakdown_arr[0]+$rmg_process_breakdown_arr[9];
                                    if(fab_proce_sub_tot>0)
                                    {
										?>
										<tr>
                                            <td width="130">Sub Total  <!-- Others% breack Down 9--></td>
                                            <td align="right"><? echo number_format($fab_proce_sub_tot,2); ?></td>
										</tr>
										<?
                                    }
                                    if($gmts_pro_sub_tot+$fab_proce_sub_tot>0)
                                    {
										?>
										<tr>
                                            <td width="130">Grand Total  <!-- Others% breack Down 9--></td>
                                            <td align="right"><? echo number_format($gmts_pro_sub_tot+$fab_proce_sub_tot,2); ?></td>
										</tr>
										<?
                                    }
                                    ?>
                                </table>
                            </fieldset>
                        </div>
                    </td>
                    <td width="330" valign="top" align="left">
						<? $nameArray_imge =sql_select("SELECT image_location FROM common_photo_library where master_tble_id='$job_no' and file_type=1"); ?>
                        <div id="div_size_color_matrix" style="float:left;">
                            <fieldset id="" >
                                <legend>Image </legend>
                                <table width="310">
                                    <tr>
										<?
                                        $img_counter = 0;
                                        foreach($nameArray_imge as $result_imge)
                                        {
											if($path=="") $path='../../';
											?>
											<td><img src="<? echo $path.$result_imge[csf('image_location')]; ?>" width="90" height="100" border="2" /></td>
											<?
											$img_counter++;
                                        }
                                        ?>
                                    </tr>
                                </table>
                            </fieldset>
                        </div>
                    </td>
                </tr>
			</table>
			<?
		}
		
      	
		?>
        <br/>
        <?
		$sql_embelishment=sql_select("select emb_name,emb_type,cons_dzn_gmts,rate,amount from wo_pre_cost_embe_cost_dtls where job_no='$job_no' and status_active=1 and 	is_deleted=0");
		?>
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0" style="font-family:Arial Narrow;">
            <tr>
                <td width="49%" valign="top">
                <?
				if(count($sql_embelishment)>0)
				{
				?>
                    <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" style="font-family:Arial Narrow;">
                    <tr align="center">
                    <td colspan="7"><b>Embelishment (Pre Cost)</b></td>

                    </tr>
                    <tr align="center">
                    <td>Sl</td>
                    <td>Embelishment Name</td>
                    <td>Embelishment Type</td>
                    <td>Cons <? echo $costing_per; ?> Gmts</td>
                    <td>Rate</td>
                    <td>Amount</td>

                    </tr>
                    <?
					$sql_embelishment=sql_select("select emb_name,emb_type,cons_dzn_gmts,rate,amount from wo_pre_cost_embe_cost_dtls where job_no='$job_no' and status_active=1 and 	is_deleted=0");
					$i=0;
					//$total_yarn=0;
					foreach($sql_embelishment  as $row_embelishment)
                    {

						$i++;
						?>
	                    <tr align="center">
	                    <td><? echo $i; ?></td>
	                    <td>
						<?
						echo $emblishment_name_array[$row_embelishment[csf('emb_name')]];
						?>
	                    </td>
	                    <td>
	                    <?
						if($row_embelishment[csf('emb_name')]==1)
						{
						echo $emblishment_print_type[$row_embelishment[csf('emb_type')]];
						}
						if($row_embelishment[csf('emb_name')]==2)
						{
						echo $emblishment_embroy_type[$row_embelishment[csf('emb_type')]];
						}
						if($row_embelishment[csf('emb_name')]==3)
						{
						echo $emblishment_wash_type[$row_embelishment[csf('emb_type')]];
						}
						if($row_embelishment[csf('emb_name')]==4)
						{
						echo $emblishment_spwork_type[$row_embelishment[csf('emb_type')]];
						}
						if($row_embelishment[csf('emb_name')]==5)
						{
						echo $emblishment_gmts_type[$row_embelishment[csf('emb_type')]];
						}
						?>

	                    </td>
	                    <td>
	                    <?
						echo $row_embelishment[csf('cons_dzn_gmts')];
						?>
	                    </td>
	                    <td>
						<?
						echo $row_embelishment[csf('rate')];
						?>
	                    </td>

	                    <td>
						<?
						echo $row_embelishment[csf('amount')];
						?>
	                    </td>


	                    </tr>
                    	<?
					}
					?>

                    </table>
                    <?
				}
					?>
                </td>
                <td width="2%">
                </td>
                <td width="49%" valign="top" align="center">
                <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" style="font-family:Arial Narrow;">
                    <tr align="center">
                    <td><b>Approved Instructions</b></td>

                    </tr>
                    <tr>
                    <td>
                <?  echo $nameArray_approved_comments_row[csf('comments')];  ?>
                </td>
                </tr>
                </table>

                </td>
            </tr>
        </table>
        <br/>

        <?
		

		
		//echo get_spacial_instruction($txt_booking_no,"97%",118);
		$mst_id=$txt_booking_no; $width="97%"; $entry_form=118;
		$html = '
	<table  width=' . $width . ' class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
	<thead>
	<tr style="border:1px solid black;">
	<th width="3%" style="border:1px solid black;">Sl</th><th width="97%" style="border:1px solid black;">Special Instruction</th>
	</tr>
	</thead>
	<tbody>';

	if ($entry_form != '') {$entry_form_con = " and entry_form=$entry_form";}

	$data_array = sql_select("select id, terms from  wo_booking_terms_condition where booking_no='" . str_replace("'", "", $mst_id) . "' $entry_form_con   order by id");
	if (count($data_array) > 0) {
		$i = 0;
		foreach ($data_array as $row) {
			$i++;
			$html .= '
			<tr id="settr_1" align="" style="border:1px solid black;">
			<td style="border:1px solid black;">' . $i . '</td>
			<td style="border:1px solid black; font-weight:bold">' . $row[csf('terms')] . '</td>
			</tr>';
		}
	}

	$html .= '
	</tbody>
	</table>';
	echo $html;
		 ?>
        
        

         <!--<br><br><br><br>-->
         <div style="font-family:Arial Narrow;">
         <?
		 	echo signature_table(1, $cbo_company_name, "1330px");
		 ?>
         </div>
       </div>
       <?
	   exit();
}*/



?>