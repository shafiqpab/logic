<?
include('../../../includes/common.php');
session_start();

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

if ($action == "check_conversion_rate") {
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
if($action=="load_drop_down_embroidery")
{
	//$data=explode('_',$data);	
	echo create_drop_down( "cboembtype_1", 80,$emblishment_embroy_type,"", 1, "-- Select --", "", "","","" );	
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
	/*print_r($process);die; */

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
		
		$new_job_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'EOE', date("Y",time()), 5, "SELECT job_no_prefix,job_no_prefix_num from subcon_ord_mst where entry_form=311 and company_id=$cbo_company_name $insert_date_con order by id desc ", "job_no_prefix", "job_no_prefix_num" ));

		$id=return_next_id("id","subcon_ord_mst",1);
		$id1=return_next_id( "id", "subcon_ord_dtls",1) ;
		$id3=return_next_id( "id", "subcon_ord_breakdown", 1 ) ;
		$rID3=true;
		$field_array="id, entry_form, embellishment_job, job_no_prefix, job_no_prefix_num, company_id, location_id, within_group, party_id, party_location, currency_id, receive_date, delivery_date, rec_start_date, rec_end_date, order_id, order_no,exchange_rate, inserted_by, insert_date";
		//txt_job_no*cbo_company_name*cbo_location_name*cbo_within_group*cbo_party_name*cbo_party_location*cbo_currency*txt_order_receive_date*txt_delivery_date*txt_order_no*hid_order_id*update_id
		$data_array="(".$id.", 311, '".$new_job_no[0]."', '".$new_job_no[1]."', '".$new_job_no[2]."', ".$cbo_company_name.", ".$cbo_location_name.", ".$cbo_within_group.", ".$cbo_party_name.", ".$cbo_party_location.", ".$cbo_currency.", ".$txt_order_receive_date.", ".$txt_delivery_date.",".$txt_rec_start_date.",".$txt_rec_end_date.", ".$hid_order_id.", ".$txt_order_no.", ".$txt_exchange_rate.", ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."')";
		
		$txt_job_no=$new_job_no[0];
		
		$field_array2="id, mst_id, job_no_mst, order_id, order_no, buyer_po_id, booking_dtls_id, gmts_item_id, main_process_id, embl_type, body_part, order_quantity, order_uom, rate, amount,domestic_amount, smv, delivery_date, wastage,buyer_po_no, buyer_style_ref,buyer_buyer, inserted_by, insert_date";
		$field_array3="id, mst_id, order_id, job_no_mst, book_con_dtls_id, item_id, body_part, embellishment_type, description, color_id, size_id, qnty, rate, amount,domestic_amount, inserted_by, insert_date";

		$color_library_arr=return_library_array( "SELECT id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
		$size_library_arr=return_library_array( "SELECT id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
		$data_array2 	= $data_array3="";  $add_commaa=0; $add_commadtls=0;  $new_array_color=array();  $new_array_size=array();

		for($i=1; $i<=$total_row; $i++)
		{			
			$txtbuyerPoId			= "txtbuyerPoId_".$i; 
			$txtbuyerPo			    = "txtbuyerPo_".$i;
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
			$data_array2 .="(".$id1.",".$id.",'".$new_job_no[0]."',".$hid_order_id.",".$txt_order_no.",'".$txtbuyerPoId."',".$$hdnbookingDtlsId.",".$$cboGmtsItem.",".$$cboProcessName.",".$$cboembtype.",".$$cboBodyPart.",".str_replace(",",'',$$txtOrderQuantity).",".$$cboUom.",".$$txtRate.",".str_replace(",",'',$$txtAmount).",".str_replace(",",'',$$txtdomisticamount).",".$$txtSmv.",'".$orderDeliveryDate."',".$$txtWastage.",".$$txtbuyerPo.",".$$txtstyleRef.",".$$txtbuyer.",'".$user_id."','".$pc_date_time."')";
			
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
				$domistic_amount="'".$exdata[8]."'";
				

				if (str_replace("'", "", trim($colorname)) != "") {
					if (!in_array(str_replace("'", "", trim($colorname)),$new_array_color)){
						$color_id = return_id( str_replace("'", "", trim($colorname)), $color_library_arr, "lib_color", "id,color_name","311");
						$new_array_color[$color_id]=str_replace("'", "", trim($colorname));
					}
					else $color_id =  array_search(str_replace("'", "", trim($colorname)), $new_array_color);
				} else $color_id = 0;

				if (str_replace("'", "", trim($sizename)) != "") {
					if (!in_array(str_replace("'", "", trim($sizename)),$new_array_size)){
						$size_id = return_id( str_replace("'", "", trim($sizename)), $size_library_arr, "lib_color", "id,size_name","311");
						$new_array_size[$size_id]=str_replace("'", "", trim($sizename));
					}
					else $size_id =  array_search(str_replace("'", "", trim($sizename)), $new_array_size);
				} else $size_id = 0;
				
				if ($add_commadtls!=0) $data_array3 .=",";
				$data_array3.="(".$id3.",".$id1.",".$hid_order_id.",'".$new_job_no[0]."',".$book_con_dtls_id.",".$$cboGmtsItem.",".$$cboBodyPart.",".$$cboembtype.",".$description.",'".$color_id."','".$size_id."',".$qty.",".$rate.",".$amount.",".$domistic_amount.",'".$user_id."','".$pc_date_time."')";
				$id3=$id3+1; $add_commadtls++;
			}
			
			$id1=$id1+1; $add_commaa++;
			//echo "10**INSERT INTO subcon_ord_breakdown (".$field_array3.") VALUES ".$data_array3; die;			
		}
		//echo "10**".$data_array2; die;
		//echo "10**".$data_array3; die;
		//echo "10**INSERT INTO subcon_ord_breakdown (".$field_array3.") VALUES ".$data_array3; die;
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
		//echo "10**SELECT c.sys_no, d.job_break_id, d.quantity from subcon_ord_breakdown a, subcon_ord_dtls b, sub_material_mst c, sub_material_dtls d where a.job_no_mst=b.job_no_mst and a.job_no_mst=c.embl_job_no and a.mst_id=b.id and c.id=d.mst_id and a.id=d.job_break_id and b.id=d.job_dtls_id and b.mst_id=$update_id and c.embl_job_no=$txt_job_no and c.entry_form=312 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.sys_no, d.job_break_id, d.quantity"; die;
		
		$qry_result=sql_select( "SELECT c.sys_no, d.job_break_id, d.quantity from subcon_ord_breakdown a, subcon_ord_dtls b, sub_material_mst c, sub_material_dtls d where a.job_no_mst=b.job_no_mst and a.job_no_mst=c.embl_job_no and a.mst_id=b.id and c.id=d.mst_id and a.id=d.job_break_id and b.id=d.job_dtls_id and b.mst_id=$update_id and c.embl_job_no=$txt_job_no and c.entry_form=312 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.sys_no, d.job_break_id, d.quantity");
		$rcv_arr=array();
		foreach($qry_result as $row)
		{
			$rcv_arr[$row[csf('job_break_id')]]['quantity']+=$row[csf('quantity')];
			$rec_number.=$row[csf('sys_no')].',';
		}
		$rec_number=chop($rec_number,',');
		$rec_number=implode(", ",array_unique(explode(",",$rec_number)));


		$bill_qry_result=sql_select( "select d.order_id,b.rate  from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, subcon_inbound_bill_dtls d where a.embellishment_job=b.job_no_mst and b.id=c.mst_id and c.id=d.color_size_id and a.entry_form=311 and d.process_id=14  and d.entry_form=332 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.mst_id=$update_id group by d.order_id,b.rate order by d.order_id ASC");
		$bill_arr=array();
		foreach($qry_result as $row)
		{
			$bill_arr[$row[csf('order_id')]]['rate']=$row[csf('rate')];
			//$bill_number.=$row[csf('sys_no')].',';
		}
		

		
		/*if($rec_number){
			echo "emblRec**".str_replace("'","",$txt_job_no)."**".$rec_number;
			disconnect($con); die;
		}*/
		/*
		$recipe_number=return_field_value( "recipe_no", "pro_recipe_entry_mst"," job_no=$txt_job_no and status_active=1 and is_deleted=0 and entry_form=220");
		if($recipe_number){
			echo "emblRecipe**".str_replace("'","",$txt_job_no)."**".$recipe_number;
			disconnect($con); die;
		}*/
		
		
		//echo "10**"."select b.id from subcon_ord_dtls a, subcon_ord_breakdown b where a.id=b.mst_id and a.job_no_mst=b.job_no_mst and a.mst_id=$update_id and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0"; die;
		
		$breaksql = sql_select("select b.id from subcon_ord_dtls a, subcon_ord_breakdown b where a.id=b.mst_id and a.job_no_mst=b.job_no_mst and a.mst_id=$update_id and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0");
		$prev_data_arr=array();
		foreach( $breaksql as $row)
		{
			$prev_data_arr[$row[csf("id")]]=$row[csf("id")]; 
		}
		
		
		$color_library_arr=return_library_array( "SELECT id,color_name from lib_color  where status_active=1 and is_deleted=0", "id", "color_name"  );
		$size_library_arr=return_library_array( "SELECT id,size_name from lib_size  where status_active=1 and is_deleted=0", "id", "size_name"  );

		$field_array="location_id*within_group*party_id*party_location*currency_id*receive_date*delivery_date*rec_start_date*rec_end_date*order_id*order_no*exchange_rate*updated_by*update_date";		
		$data_array="".$cbo_location_name."*".$cbo_within_group."*".$cbo_party_name."*".$cbo_party_location."*".$cbo_currency."*".$txt_order_receive_date."*".$txt_delivery_date."*".$txt_rec_start_date."*".$txt_rec_end_date."*".$hid_order_id."*".$txt_order_no."*".$txt_exchange_rate."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		//$field_array2="id, mst_id, job_no_mst, order_id, order_no, buyer_po_id, booking_dtls_id, gmts_item_id, main_process_id, embl_type, body_part, order_quantity, order_uom, rate, amount, smv, delivery_date, wastage,buyer_po_no, buyer_style_ref, inserted_by, insert_date";
		$field_array2="order_id*order_no*buyer_po_id*booking_dtls_id*gmts_item_id*main_process_id*embl_type*body_part*order_quantity*order_uom*rate*amount*domestic_amount*smv*delivery_date*wastage*buyer_po_no*buyer_style_ref*buyer_buyer*updated_by*update_date";
		
		$field_array3="id, mst_id, order_id, job_no_mst, book_con_dtls_id, item_id, body_part, embellishment_type, description, color_id, size_id, qnty, rate, amount,domestic_amount, inserted_by, insert_date";
		$field_array4="order_id*book_con_dtls_id*item_id*body_part*embellishment_type*description*color_id*size_id*qnty*rate*amount*domestic_amount*updated_by*update_date";
		
		$field_array5="id, mst_id, job_no_mst, order_id, order_no, buyer_po_id, booking_dtls_id, gmts_item_id, main_process_id, embl_type, body_part, order_quantity, order_uom, rate, amount,domestic_amount, smv, delivery_date, wastage,buyer_po_no, buyer_style_ref,buyer_buyer, inserted_by, insert_date";
		$id3=return_next_id( "id", "subcon_ord_breakdown", 1) ;
		$id1=return_next_id( "id", "subcon_ord_dtls",1) ;
		$add_comma=0;	$flag="";  $new_array_color=array();  $new_array_size=array();
		for($i=1; $i<=$total_row; $i++)
		{			
			$txtbuyerPoId			= "txtbuyerPoId_".$i; 
			$txtbuyerPo			    = "txtbuyerPo_".$i;
			$txtbuyer				= "txtbuyer_".$i;  
			$txtstyleRef			= "txtstyleRef_".$i;
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
			
			
			
			$dtls_data=explode("__",str_replace("'",'',$$hdnDtlsdata));
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
				$bb=$exdata[7];
				$domistic_amount=str_replace(",",'',$exdata[5])*str_replace("'",'',$txt_exchange_rate);

				$rcv_qty=$rcv_arr[$bb]['quantity'];
				if($rcv_qty!='')
				{
					$rcv_qty_pcs=$rcv_qty/12;
					if(($rcv_qty_pcs*1)>(str_replace("'","",$qty)*1))
					{
						echo "emblRecQty**".str_replace("'","",$txt_job_no)."**".$rec_number;
						//echo "emblRecQty**".str_replace("'","",$rcv_qty_pcs)."**".$qty;
						disconnect($con); die;
					}
				}
				//echo "10**".$domistic_amount;//.$total_row; die;
				//$domistic_amount="'".$exdata[8]."'";
				//echo $dtlsup_id;

				if (str_replace("'", "", trim($colorname)) != "")
				 {
					if (!in_array(str_replace("'", "", trim($colorname)),$new_array_color))
					{
						$color_id = return_id( str_replace("'", "", trim($colorname)), $color_library_arr, "lib_color", "id,color_name","311");
						$new_array_color[$color_id]=str_replace("'", "", trim($colorname));
					}
					else $color_id =  array_search(str_replace("'", "", trim($colorname)), $new_array_color);
				} else $color_id = 0;

				if (str_replace("'", "", trim($sizename)) != "") {
					if (!in_array(str_replace("'", "", trim($sizename)),$new_array_size)){
						$size_id = return_id( str_replace("'", "", trim($sizename)), $size_library_arr, "lib_color", "id,size_name","311");
						$new_array_size[$size_id]=str_replace("'", "", trim($sizename));
					}
					else $size_id =  array_search(str_replace("'", "", trim($sizename)), $new_array_size);
				} else $size_id = 0;

				if(str_replace("'",'',$$hdnDtlsUpdateId)!=""){
					$dtls_id=str_replace("'",'',$$hdnDtlsUpdateId);
				}else{
					$dtls_id=$id1;
				}
				if($bb==0)
				{
					if ($add_commadtls!=0) $data_array3 .=",";
					$data_array3.="(".$id3.",".$dtls_id.",".$hid_order_id.",".$txt_job_no.",".$book_con_dtls_id.",".$$cboGmtsItem.",".$$cboBodyPart.",".$$cboembtype.",".$description.",'".$color_id."','".$size_id."',".$qty.",".$rate.",".$amount.",".$domistic_amount.",'".$user_id."','".$pc_date_time."')";
					$id3=$id3+1; $add_commadtls++;
				}
				else if($bb!=0)
				{
					$data_array4[$bb]=explode("*",("".$hid_order_id."*".$book_con_dtls_id."*".$$cboGmtsItem."*".$$cboBodyPart."*".$$cboembtype."*".$description."*'".$color_id."'*'".$size_id."'*".$qty."*".$rate."*".$amount."*".$domistic_amount."*".$user_id."*'".$pc_date_time."'"));
					$hdn_break_id_arr[]		=$bb;
				}
			}
			
			if($db_type==0){
				$orderDeliveryDate=change_date_format(str_replace("'",'',$$txtOrderDeliveryDate),'yyyy-mm-dd');
			}else{
				$orderDeliveryDate=change_date_format(str_replace("'",'',$$txtOrderDeliveryDate), "", "",1);
			}
			if(str_replace("'",'',$$txtbuyerPoId)=="") $txtbuyerPoId=0; else $txtbuyerPoId=str_replace("'",'',$$txtbuyerPoId);
			if(str_replace("'",'',$$hdnDtlsUpdateId)!="")
			{
				$data_array2[$aa]=explode("*",("".$hid_order_id."*".$txt_order_no."*'".$txtbuyerPoId."'*".$$hdnbookingDtlsId."*".$$cboGmtsItem."*".$$cboProcessName."*".$$cboembtype."*".$$cboBodyPart."*".str_replace(",",'',$$txtOrderQuantity)."*".$$cboUom."*".$$txtRate."*".str_replace(",",'',$$txtAmount)."*".str_replace(",",'',$$txtdomisticamount)."*".$$txtSmv."*'".$orderDeliveryDate."'*".$$txtWastage."*".$$txtbuyerPo."*".$$txtstyleRef."*".$$txtbuyer."*".$user_id."*'".$pc_date_time."'"));
				$hdn_dtls_id_arr[]=str_replace("'",'',$$hdnDtlsUpdateId);
			}
			else
			{
				if ($add_commaa!=0) $data_array5 .=","; $add_comma=0;
				$data_array5 .="(".$id1.",".$update_id.",".$txt_job_no.",".$hid_order_id.",".$txt_order_no.",'".$txtbuyerPoId."',".$$hdnbookingDtlsId.",".$$cboGmtsItem.",".$$cboProcessName.",".$$cboembtype.",".$$cboBodyPart.",".str_replace(",",'',$$txtOrderQuantity).",".$$cboUom.",".$$txtRate.",".str_replace(",",'',$$txtAmount).",".str_replace(",",'',$$txtdomisticamount).",".$$txtSmv.",'".$orderDeliveryDate."',".$$txtWastage.",".$$txtbuyerPo.",".$$txtstyleRef.",".$$txtbuyer.",'".$user_id."','".$pc_date_time."')";
				$id1=$id1+1;

			}
			//
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
		
		$id_break=implode(',',$hiddenTblIdBreak);
		
		if($data_array4!=""){
			$rID3=execute_query(bulk_update_sql_statement( "subcon_ord_breakdown", "id",$field_array4,$data_array4,$hdn_break_id_arr),1);
			if($rID3==1 && $flag==1) $flag=1; else $flag=0;
		}

		if($data_array3!=""){
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
			//$rID5=execute_query( "delete from subcon_ord_breakdown where id in ( $all_del_ids)",0);
			$rID5=execute_query("UPDATE subcon_ord_breakdown SET updated_by=$user_id, update_date='$pc_date_time', status_active=0, is_deleted=1 WHERE  id in ( $all_del_ids)");
			if($rID5==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		$deleteIds='';
		foreach($prev_data_arr as $prevbreakid=>$value)
		{
			if(!in_array($prevbreakid,$hdn_break_id_arr))
			{
				$deleteIds.=$prevbreakid.",";
			}
		}
		$deleteIds=chop($deleteIds,',');
 		if($deleteIds!='')
		{
			//echo $deleteIds; die;
			$field_array_status="updated_by*update_date*status_active*is_deleted";
			$data_array_status="'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*0*1";
			$rID6=sql_multirow_update("subcon_ord_breakdown",$field_array_status,$data_array_status,"id",$deleteIds,0);
			if($rID6==1 && $flag==1) $flag=1; else $flag=0;	
		}
		
		//echo "10**".$rID.'='.$rID2.'='.$rID3.'='.$rID4.'='.$rID8.'='.$rID5; die;
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
		$qry_result=sql_select( "SELECT c.sys_no from subcon_ord_breakdown a, subcon_ord_dtls b, sub_material_mst c where a.job_no_mst=b.job_no_mst and a.job_no_mst=c.embl_job_no and a.mst_id=b.id and b.mst_id=$update_id and c.embl_job_no=$txt_job_no and c.entry_form=312 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
		foreach($qry_result as $row)
		{
			$rec_number.=$row[csf('sys_no')].',';
		}
		$rec_number=chop($rec_number,',');
		$rec_number=implode(", ",array_unique(explode(",",$rec_number)));
		if($rec_number)
		{
			echo "emblRec**".str_replace("'","",$txt_job_no)."**".$rec_number;
			disconnect($con); die;
		}
		for($i=1; $i<=$total_row; $i++)
		{			
			$hdnDtlsdata 			= "hdnDtlsdata_".$i;
			$hdnDtlsUpdateId 		= "hdnDtlsUpdateId_".$i;

			$break_mst_ids_all .=str_replace("'",'',$$hdnDtlsUpdateId).',';
			/*$dtls_data=explode("__",str_replace("'",'',$$hdnDtlsdata));
			for($j=0; $j<count($dtls_data); $j++)
			{
				$exdata=explode("_",$dtls_data[$j]);
				$dtlsup_id="'".$exdata[7]."'";
				if($dtlsup_id!=0)
				{
					echo "10**".$dtlsup_id;
					$break_ids_all .=$dtlsup_id.',';
					$hdn_break_id_arr[]		=$dtlsup_id;
				}
			}*/
		}

		/*for($i=1; $i<=$total_row; $i++)
		{
			$hdnDtlsdata 			= "hdnDtlsdata_".$i;
			$hdnDtlsUpdateId 		= "hdnDtlsUpdateId_".$i;
			$aa	=str_replace("'",'',$$hdnDtlsUpdateId);
			$dtls_data=explode("__",str_replace("'",'',$$hdnDtlsdata));
			echo "10**";//.$total_row; die;
			print_r($dtls_data);
			
			
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
		}*/
		
		$break_mst_ids_all=implode(",",array_unique(explode(",",chop($break_mst_ids_all,','))));
		/*if($data_array4!="")
		{
			$rID3=execute_query(bulk_update_sql_statement( "subcon_ord_breakdown", "id",$field_array4,$data_array4,$hdn_break_id_arr),1);
			if($rID3==1 && $flag==1) $flag=1; else $flag=0;
		}*/

		//if ( $delete_master_info==1 )
		//{
		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		$flag=1;
		$rID=sql_update("subcon_ord_mst",$field_array,$data_array,"id",$update_id,1);  
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		$rID1=sql_update("subcon_ord_dtls",$field_array,$data_array,"mst_id",$update_id,1);
		if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		//$rID = sql_delete("subcon_ord_dtls","status_active*is_deleted*updated_by*update_date","0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'",'id',$update_id2,1);
		
		
		//$rID2=execute_query( "delete from subcon_ord_breakdown where mst_id in ($break_mst_ids_all) and job_no_mst=$txt_job_no",0);
		//if($rID2==1 && $flag==1) $flag=1; else $flag=0;
		
		
		$rID2=sql_update("subcon_ord_breakdown",$field_array,$data_array,"job_no_mst",$txt_job_no,1);
		if($rID2==1 && $flag==1) $flag=1; else $flag=0;  
		
		
		
		$rID3=execute_query( "update wo_booking_mst set lock_another_process=0 where booking_no =".$txt_order_no."",1);
		if($rID3==1 && $flag==1) $flag=1; else $flag=0;
		//}
		//echo "10**".$rID.'**'.$rID1.'**'.$rID2.'**'.$rID3.'**'.$break_mst_ids_all; die;
		//echo "10**".$break_id_all; die;
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
			var within_group = $('#cbo_within_group').val();
			load_drop_down( 'emb_order_entry_controller', company+'_'+within_group, 'load_drop_down_buyer', 'buyer_td' );
		}
		
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0) $('#search_by_td').html('Embl. Job No');
			else if(val==2) $('#search_by_td').html('W/O No');
			else if(val==3) $('#search_by_td').html('Buyer Job');
			else if(val==4) $('#search_by_td').html('Buyer Po');
			else if(val==5) $('#search_by_td').html('Buyer Style');
			else if(val==6) $('#search_by_td').html('IR/IB');
		}
	</script>
</head>
<body onLoad="fnc_load_party_popup(<? echo "1";?>,<? echo $cbo_within_group;?>)">
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
                    <th width="100" id="search_by_td">Emb Job No</th>
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
                            $search_by_arr=array(1=>"Embl. Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style", 6=>"IR/IB");
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
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year_selection').value, 'create_job_search_list_view', 'search_div', 'emb_order_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
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
	//$year =$exdata[8];
	if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[8]";   }
	if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$data[8]";}

	if($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	//echo $search_type; die;
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";$group_cond="";
	
	if($within_group==1)
	{
		if($search_type==1)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
				else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
				if ($search_by==3) $job_cond=" and a.job_no_prefix_num = '$search_str' ";
				else if ($search_by==4) $po_cond=" and b.po_number = '$search_str' ";
				else if ($search_by==5) $style_cond=" and a.style_ref_no = '$search_str' ";
				else if ($search_by==6) $group_cond=" and d.grouping = '$search_str' ";
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
				else if ($search_by==6) $group_cond=" and d.grouping like '%$search_str%' "; 
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
				else if ($search_by==6) $group_cond=" and d.grouping like '$search_str%' ";
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
				else if ($search_by==6) $group_cond=" and d.grouping like '%$search_str' ";
			}
		}
	}
	
	if($within_group==2)
	{
		if($search_type==1)
		{
			if($search_str!="") 
			{
				if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
				else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
				if ($search_by==3) $job_cond=" and a.job_no_prefix_num = '$search_str' ";
				else if ($search_by==4) $buyer_po_no_cond=" and b.buyer_po_no = '$search_str' ";
				else if ($search_by==5) $buyer_style_ref_cond=" and b.buyer_style_ref = '$search_str' ";
			}
		}
		else if($search_type==4 || $search_type==0)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str%'";  
				else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";  
				if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str%'";  
				else if ($search_by==4) $buyer_po_no_cond=" and b.buyer_po_no like '%$search_str%'"; 
				else if ($search_by==5) $buyer_style_ref_cond=" and b.buyer_style_ref like '%$search_str%'";   
			}
		}
		else if($search_type==2)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
				else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
				if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '$search_str%'";  
				else if ($search_by==4) $buyer_po_no_cond=" and b.buyer_po_no like '$search_str%'";
				else if ($search_by==5) $buyer_style_ref_cond=" and b.buyer_style_ref like '$search_str%'";  
			}
		}
		else if($search_type==3)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";  
				else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
				if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str'";  
				else if ($search_by==4) $buyer_po_no_cond=" and b.buyer_po_no like '%$search_str'";
				else if ($search_by==5) $buyer_style_ref_cond=" and b.buyer_style_ref like '%$search_str'";  
			}
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
	
	
	//echo "select $id_cond as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
	
	if($within_group==1)
	{
		
		$po_ids='';
		if($db_type==0) $id_cond="group_concat(b.id)";
		else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
		if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
		{
			//echo "ere"; die;
			
			//echo "select $id_cond as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
			
			$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
			
			if ($po_ids=="")
			{
				$po_idsCond="";
				echo "Not Found."; die;
			}
		}
		
		if ($po_ids!="") $po_idsCond=" and b.buyer_po_id in ($po_ids)"; else $po_idsCond="";
	}
	//echo $po_ids;
	
	
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
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
		$color_id_str="group_concat(c.color_id)";
		$buyer_po_id_str="group_concat(b.buyer_po_id)";
	}
	else if($db_type==2)
	{
		$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
		$color_id_str="listagg(c.color_id,',') within group (order by c.color_id)";
		$buyer_po_id_str="listagg(b.buyer_po_id,',') within group (order by b.buyer_po_id)";
	}

	if($within_group==1){
		$sql= "select a.id, a.embellishment_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, d.grouping, a.delivery_date,b.buyer_po_no,b.buyer_style_ref, $color_id_str as color_id, $buyer_po_id_str as buyer_po_id,a.exchange_rate
		from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, wo_po_break_down d
		where a.entry_form=311 and a.embellishment_job=b.job_no_mst and d.id=b.buyer_po_id and a.id=b.mst_id and d.status_active=1 and d.is_deleted=0 and a.status_active=1 and b.status_active=1 and  c.status_active=1 and c.is_deleted=0  $order_rcv_date $company $buyer $withinGroup $search_com_cond $year_cond $po_idsCond $group_cond $buyer_style_ref_cond $buyer_po_no_cond $party_id_cond and b.id=c.mst_id  and b.job_no_mst=c.job_no_mst 
		group by a.id,d.grouping, a.embellishment_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date,b.buyer_po_no,b.buyer_style_ref,a.exchange_rate
		order by a.id DESC";
	}else{
		$sql= "select a.id, a.embellishment_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date,b.buyer_po_no,b.buyer_style_ref, $color_id_str as color_id, $buyer_po_id_str as buyer_po_id,a.exchange_rate
		from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
		where a.entry_form=311 and a.embellishment_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and b.status_active=1 and  c.status_active=1 and c.is_deleted=0  $order_rcv_date $company $buyer $withinGroup $search_com_cond $year_cond $po_idsCond  $buyer_style_ref_cond $buyer_po_no_cond $party_id_cond and b.id=c.mst_id  and b.job_no_mst=c.job_no_mst 
		group by a.id, a.embellishment_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date,b.buyer_po_no,b.buyer_style_ref,a.exchange_rate
		order by a.id DESC";
	}
	 
	 //echo $sql;
	 $data_array=sql_select($sql);
	?>
     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="945" >
        <thead>
            <th width="30">SL</th>
            <th width="60">Job No</th>
            <th width="60">IR/IB</th>
            <th width="60">Year</th>
            <th width="120">W/O No</th>
            <th width="100">Buyer Po</th>
            <th width="100">Buyer Style</th>
            <th width="80">Ord Receive Date</th>
            <th width="80">Delivery Date</th>
            <th>Color</th>
        </thead>
        </table>
        <div style="width:965px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="945" class="rpt_table" id="tbl_po_list">
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
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$row[csf('embellishment_job')].'_'.$row[csf('exchange_rate')]; ?>")' style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                    <td width="60"><? echo $row[csf('grouping')]; ?></td>
                    <td width="60" style="text-align:center;"><? echo $row[csf('year')]; ?></td>
                    <td width="120"><? echo $row[csf('order_no')]; ?></td>
                    <td width="100" style="word-break:break-all"><?  if ($within_group==1)echo $buyer_po; else echo $row[csf('buyer_po_no')];//$buyer_po; ?></td>
                    <td width="100" style="word-break:break-all"><? if ($within_group==1)echo $buyer_style; echo $row[csf('buyer_style_ref')];//$buyer_style; ?></td>
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
	//echo $data;
	//echo "select id, embellishment_job, company_id, location_id, party_id, currency_id, party_location, delivery_date, rec_start_date, rec_end_date, receive_date, within_group, party_location, order_id, order_no from subcon_ord_mst where embellishment_job='$data' and status_active=1" ;
	$nameArray=sql_select( "select id, embellishment_job, company_id, location_id, party_id, currency_id, party_location, delivery_date, rec_start_date, rec_end_date, receive_date, within_group, party_location, order_id, order_no from subcon_ord_mst where id='$data' and status_active=1 and is_deleted= 0 and entry_form=311");
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_job_no').value 			= '".$row[csf("embellishment_job")]."';\n";  
		echo "document.getElementById('cbo_company_name').value 	= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_within_group').value 	= '".$row[csf("within_group")]."';\n";  
		echo "$('#cbo_company_name').attr('disabled','true')".";\n";
		echo "fnc_load_party(1,".$row[csf("within_group")].");\n";	
	//echo "load_drop_down( 'requires/emb_order_entry_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_location', 'location_td' );\n";	
		echo "document.getElementById('cbo_location_name').value 		= '".$row[csf("location_id")]."';\n";
	//echo "load_drop_down( 'requires/emb_order_entry_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_buyer', 'buyer_td' );\n";
		echo "document.getElementById('cbo_party_name').value			= '".$row[csf("party_id")]."';\n";
 		echo "fnc_load_party(2,".$row[csf("within_group")].");\n";	
		echo "document.getElementById('cbo_currency').value				= '".$row[csf("currency_id")]."';\n"; 
		echo "document.getElementById('cbo_party_location').value		= '".$row[csf("party_location")]."';\n";	
		echo "document.getElementById('txt_order_receive_date').value	= '".change_date_format($row[csf("receive_date")])."';\n"; 
		echo "document.getElementById('txt_delivery_date').value		= '".change_date_format($row[csf("delivery_date")])."';\n"; 
		echo "document.getElementById('txt_rec_start_date').value		= '".change_date_format($row[csf("rec_start_date")])."';\n"; 
		echo "document.getElementById('txt_rec_end_date').value		= '".change_date_format($row[csf("rec_end_date")])."';\n"; 
		echo "document.getElementById('hid_order_id').value          	= '".$row[csf("order_id")]."';\n";
		echo "document.getElementById('txt_order_no').value         	= '".$row[csf("order_no")]."';\n";
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
		load_drop_down( 'emb_order_entry_controller', company+'_'+1+'_'+party_name, 'load_drop_down_buyer', 'buyer_td' );
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
			else if(val==5) $('#search_td').html('IR/IB');
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
                            $searchtype_arr=array(1=>"W/O No",2=>"Buyer Job",3=>"Buyer Style Ref.",4=>"Buyer Po", 5=>"IR/IB");
                            echo create_drop_down( "cbo_search_type", 80, $searchtype_arr,"", 0, "", 1, "search_by(this.value,1)",0,"" );
                        ?>
                    </td>
                    <td><input name="txt_search_common" id="txt_search_common" class="text_boxes" style="width:90px"></td>
                    <td><? echo create_drop_down( "cbo_year_selection", 60, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
                    <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From"></td>
                    <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To"></td> 
                    <td>
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_search_category').value+'_'+<? echo $company;?>+'_'+document.getElementById('cbo_search_type').value+'_'+<? echo $cbo_within_group; ?>, 'create_booking_search_list_view', 'search_div', 'emb_order_entry_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
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
			if ($search_type==5) $group_cond=" and d.grouping = '$data[1]' ";
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
			if ($search_type==5) $group_cond=" and d.grouping like '$data[1]%' ";
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
			if ($search_type==5) $group_cond=" and d.grouping like '%$data[1]' ";
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
			if ($search_type==5) $group_cond=" and d.grouping like '%$data[1]%' ";
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
	$pre_sql ="Select id, emb_name, emb_type, body_part_id  from wo_pre_cost_embe_cost_dtls where emb_name=2 and status_active=1 and is_deleted=0";
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
	
	//$sql= "select $wo_year as year, id, booking_type, booking_no, company_id, buyer_id, job_no, booking_date from  wo_booking_mst where booking_type=6 and status_active=1 and is_deleted=0 $booking_date $company $woorder_cond $year_cond order by booking_no"; 
	$sql= "SELECT $wo_year as year, a.id, a.booking_type, d.grouping, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id, $pre_cost_trims_cond as pre_cost_trims_id, $gmts_item_cond as gmts_item, $po_id_cond as po_id from  wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_embe_cost_dtls c, wo_po_break_down d where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and d.id=b.po_break_down_id and a.booking_type=6 and a.status_active=1 and c.emb_name=2 and a.lock_another_process!=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $booking_date $company $woorder_cond $year_cond $po_idsCond $approved_cond $group_cond group by a.insert_date, d.grouping, a.id, a.booking_type, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id order by a.id DESC";
	//echo $sql; exit();
	$data_array=sql_select($sql);
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="1140" >
        <thead>
            <th width="30">SL</th>
            <th width="60">W/O Year</th>
            <th width="60">W/O No</th>
            <th width="70">W/O Date</th>
            <th width="100">Buyer</th>
            <th width="100">Buyer Po</th>
            <th width="100">Buyer Style</th>
            <th width="100">Buyer Job</th>
			<th width="100">IR/IB</th>
            <th width="100">Gmts. Item</th>
            <th width="100">Body Part</th>
            <th width="100">Emb Name</th>
            <th>Emb Type</th>
        </thead>
        </table>
        <div style="width:1140px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1120" class="rpt_table" id="list_view">
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
                    <td width="100" style="word-break:break-all"><? echo $row[csf('grouping')]; ?></td>
                    
                    <td width="100" style="word-break:break-all"><? echo $gmts_item_name; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $body_part_name; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $embl_name; ?></td>
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
	$nameArray=sql_select( "select id, booking_type, booking_no, company_id, buyer_id, job_no, booking_date,currency_id from  wo_booking_mst where booking_type=6 and status_active=1 and is_deleted=0 and id='$data[0]'" );
	//$sql= "select to_char(insert_date,'YYYY') as year, id, booking_type, booking_no, company_id, buyer_id, job_no, booking_date from  wo_booking_mst where booking_type=6 and status_active=1 and is_deleted=0 $booking_date $company $order_cond order by booking_no";
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_order_no').value 	= '".$row[csf("booking_no")]."';\n";  
		echo "document.getElementById('cbo_party_name').value 	= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('hid_order_id').value		= '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_currency').value		= '".$row[csf("currency_id")]."';\n";
		//if($row[csf("booking_date")]=="0000-00-00" || $row[csf("booking_date")]=="") $booking_date=""; else $booking_date=change_date_format($row[csf("booking_date")]);   
		//echo "$('#cbo_company_name').attr('disabled','true')".";\n";
		//echo "load_drop_down( 'requires/emb_order_entry_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_location', 'location_td' );\n";	
		//echo "document.getElementById('cbo_location_name').value 	= '".$row[csf("location_id")]."';\n";
		//echo "load_drop_down( 'requires/emb_order_entry_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_buyer', 'buyer_td' );\n";

		//echo "document.getElementById('txt_process_id').value		= '".$row[csf("service_type")]."';\n"; 
		//echo "document.getElementById('cbo_currency').value			= '".$row[csf("currency_id")]."';\n"; 
	    //echo "document.getElementById('update_id').value          	= '".$row[csf("id")]."';\n";	
		//echo "set_button_status(0,'".$_SESSION['page_permission']."', 'fnc_job_order_entry',1);\n";	
	}
	exit();	
}

if( $action=='order_dtls_list_view' ) 
{
	//echo $data;
	$data=explode('_',$data);
	
	$update_id=$data[3];
	
	// echo print_r($data); die;
	 
	// Array ( [0] => 1 [1] => FAL-EB-23-00113 [2] => 1 [3] => 105 ) 1
	
	$color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name" );
	$size_arr=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0",'id','size_name');
	$tblRow=0;
	$buyer_po_arr=array();
	
	$buyer_po_sql = sql_select("select a.style_ref_no,a.buyer_name,b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst");
	
	foreach($buyer_po_sql as $row)
	{
		$buyer_po_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
		$buyer_po_arr[$row[csf('id')]]['buyerpo']=$row[csf('po_number')];
		$buyer_po_arr[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
	}
	unset($buyer_po_sql);
	//$prev_pi_qnty_arr_dtls=return_library_array("select a.id, a.grey_qnty_by_uom-sum(b.quantity) as balance_qty  from fabric_sales_order_dtls a ,com_export_pi_dtls b where  a.id=work_order_dtls_id and b.status_active=1 and a.status_active=1 and a.is_deleted=0 group by a.id , a.finish_qty ",'id','balance_qty');
	//print_r($prev_pi_qnty_arr_dtls);
	//1_10549_FAL-EB-18-00022
	if($data[0]==2)
	{

			//echo "SELECT id, mst_id, order_id, job_no_mst, book_con_dtls_id, item_id, body_part, embellishment_type, description, color_id, size_id, qnty, rate, amount from subcon_ord_breakdown where job_no_mst='$data[1]'"; die;
			
		//	echo "SELECT a.id, a.mst_id, a.order_id, a.job_no_mst, a.book_con_dtls_id, a.item_id, a.body_part, a.embellishment_type, a.description, a.color_id, a.size_id, a.qnty, a.rate, a.amount,a.cutting_no from subcon_ord_breakdown a, subcon_ord_dtls b where a.job_no_mst=b.job_no_mst and a.mst_id=b.id and b.mst_id=$update_id  and a.status_active=1 and a.is_deleted=0"; die;
		//$break_down_arr=array(); 
		
		$qry_result=sql_select( "SELECT a.id, a.mst_id, a.order_id, a.job_no_mst, a.book_con_dtls_id, a.item_id, a.body_part, a.embellishment_type, a.description, a.color_id, a.size_id, a.qnty, a.rate, a.amount,a.cutting_no from subcon_ord_breakdown a, subcon_ord_dtls b where a.job_no_mst=b.job_no_mst and a.mst_id=b.id and b.mst_id=$update_id  and a.status_active=1 and a.is_deleted=0");	
		//echo "select id, mst_id, order_id, job_no_mst, book_con_dtls_id, item_id, body_part, embellishment_type, description, color_id, size_id, qnty, rate, amount from subcon_ord_breakdown where job_no_mst='$data[1]'";
		$data_break_arr=array(); $data_break=''; $add_comma=0; $k=1;
		foreach ($qry_result as $row)
		{
			if($row[csf('description')]=="") $row[csf('description')]=0;
			if($row[csf('color_id')]=="") $row[csf('color_id')]=0;
			if($row[csf('size_id')]=="") $row[csf('size_id')]=0;
			if($row[csf('qnty')]=="") $row[csf('qnty')]=0;
			if($row[csf('rate')]=="") $row[csf('rate')]=0;
			if($row[csf('amount')]=="") $row[csf('amount')]=0;
			if($row[csf('book_con_dtls_id')]=="") $row[csf('book_con_dtls_id')]=0;
			if(!in_array($row[csf('mst_id')],$temp_arr_mst_id))
			{
				$temp_arr_mst_id[]=$row[csf('mst_id')];
				//if($k!=1) {  }
				$add_comma=0; $data_break='';
				
			}
			//echo $add_comma.'='.$data_break.'='.$k.'<br>';
			$k++;
			
			if ($add_comma!=0) $data_break ="__";
			$data_break_arr[$row[csf('mst_id')]].=$row[csf('description')].'_'.$color_library[$row[csf('color_id')]].'_'.$size_arr[$row[csf('size_id')]].'_'.$row[csf('qnty')].'_'.$row[csf('rate')].'_'.$row[csf('amount')].'_'.$row[csf('book_con_dtls_id')].'_'.$row[csf('id')].',';
			$add_comma++;
			
			/*if($data_break=="") $data_break.=$row[csf('description')].'_'.$row[csf('color_id')].'_'.$row[csf('size_id')].'_'.$row[csf('qnty')].'_'.$row[csf('rate')].'_'.$row[csf('amount')].'_'.$row[csf('book_con_dtls_id')].'_'.$row[csf('id')];
			else $data_break.='__'.$row[csf('description')].'_'.$row[csf('color_id')].'_'.$row[csf('size_id')].'_'.$row[csf('qnty')].'_'.$row[csf('rate')].'_'.$row[csf('amount')].'_'.$row[csf('book_con_dtls_id')].'_'.$row[csf('id')];*/
			
			//$data_break_arr[$row[csf('mst_id')]]=$data_break;
			//$data_break='';
		}
	}
	//die;
	//print_r($data_break_arr);
	if($data[2]==1) // within_group yes
	{
		$embl_po_arr=array();
		if($data[0]==2) // update type 2
		{
			$sql_up = "SELECT id, order_no, buyer_po_id, booking_dtls_id, gmts_item_id, main_process_id, embl_type, body_part, order_quantity, order_uom, rate, amount,domestic_amount, smv, delivery_date, wastage from subcon_ord_dtls where mst_id=$update_id and status_active=1 and is_deleted=0 order by id ASC";
			$data_arrup=sql_select($sql_up);
			
			foreach($data_arrup as $row)
			{
				$data[1]=$row[csf('order_no')];
				$embl_po_arr[$row[csf('booking_dtls_id')]]['id']=$row[csf('id')];
				$embl_po_arr[$row[csf('booking_dtls_id')]]['smv']=$row[csf('smv')];
				$embl_po_arr[$row[csf('booking_dtls_id')]]['delivery_date']=$row[csf('delivery_date')];
				$embl_po_arr[$row[csf('booking_dtls_id')]]['wastage']=$row[csf('wastage')];
				$embl_po_arr[$row[csf('booking_dtls_id')]]['order_uom']=$row[csf('order_uom')];
				$embl_po_arr[$row[csf('booking_dtls_id')]]['order_quantity']=$row[csf('order_quantity')];
				$embl_po_arr[$row[csf('booking_dtls_id')]]['domestic_amount']=$row[csf('domestic_amount')];
			}
		}
		
		 $sql ="SELECT a.id as embe_cost_dtls_id, a.job_no, a.emb_name, a.emb_type, a.body_part_id, b.id as booking_dtls_id, b.booking_no, b.po_break_down_id, b.gmt_item, b.wo_qnty, b.rate, b.amount, b.delivery_date, b.uom from wo_pre_cost_embe_cost_dtls a, wo_booking_dtls b where b.status_active=1 and b.is_deleted=0 and a.status_active=1 and b.pre_cost_fabric_cost_dtls_id=a.id and b.booking_type=6 and a.emb_name=2 and a.job_no=b.job_no and b.booking_no=trim('$data[1]') group by a.id, a.job_no, b.job_no, a.emb_name, a.emb_type, a.body_part_id, b.id, b.booking_no, b.po_break_down_id, b.gmt_item, b.wo_qnty, b.rate, b.amount, b.delivery_date,b.uom order by b.id ASC";
	}
	else
	{
		 $sql ="SELECT id, order_no, buyer_po_id,buyer_po_no, buyer_style_ref, booking_dtls_id, gmts_item_id as gmt_item, main_process_id as emb_name, embl_type as emb_type, body_part as body_part_id, order_quantity as wo_qnty, order_uom, rate, amount,domestic_amount, smv, delivery_date, wastage,buyer_buyer from subcon_ord_dtls where mst_id=$update_id and status_active=1 and is_deleted=0 order by id ASC";
	}
	//echo $sql; die; 
	$data_array=sql_select($sql);
	
	
	
	//echo $data[2]; die; 
	if(count($data_array) > 0)
	{
		
		$color_arr=return_library_array( "SELECT id,color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
		$size_arr=return_library_array( "SELECT id,size_name from  lib_size where status_active =1 and is_deleted=0",'id','size_name');
		
		foreach($data_array as $row)
		{
			$tblRow++;
			$type_array=array(0=>$blank_array,1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type);
			$dtls_id=0; $smv=0; $wastage=0;  $order_uom=0; $wo_qnty=0;$readonly=''; $buyerPo=''; $buyerPoId=''; $buyerStyle='';  $buyer_buyer=''; 
			if($data[2]==1)
			{
				
				
				
				$disable_dropdown='1';
				$buyerPo=$buyer_po_arr[$row[csf('po_break_down_id')]]['buyerpo'];
				$buyerPoId=$row[csf('po_break_down_id')];
				$buyerStyle=$buyer_po_arr[$row[csf('po_break_down_id')]]['style'];
				$buyer_buyer=$buyer_po_arr[$row[csf('po_break_down_id')]]['buyer_name'];
				$booking_dtls_id=$row[csf('booking_dtls_id')];
				$dtls_id=$booking_dtls_id;
				$readonly='readonly';
				
				if($data[0]==2)
				{
					$dtls_id=$embl_po_arr[$row[csf('booking_dtls_id')]]['id']; 
					$smv=$embl_po_arr[$row[csf('booking_dtls_id')]]['smv']; 
					$row[csf("delivery_date")]=$embl_po_arr[$row[csf('booking_dtls_id')]]['delivery_date']; 
					$wastage=$embl_po_arr[$row[csf('booking_dtls_id')]]['wastage'];
					$order_uom=$embl_po_arr[$row[csf('booking_dtls_id')]]['order_uom'];
					//$wo_qnty=$row[csf('wo_qnty')];
					$wo_qnty=$embl_po_arr[$row[csf('booking_dtls_id')]]['order_quantity'];
					$domestic_amount=$embl_po_arr[$row[csf('booking_dtls_id')]]['domestic_amount'];
				}
				else
				{
					$wo_qnty=0;
					$wo_qnty=$row[csf('wo_qnty')];
					//$order_uom=$row[csf('order_uom')];
					$order_uom=$row[csf('uom')];
					$domestic_amount=($row[csf('wo_qnty')]*$row[csf('rate')])*$exchange_rate;
					
					$sql = "SELECT id, description, color_number_id, gmts_sizes, requirment, color_size_table_id, rate, amount from wo_emb_book_con_dtls where wo_booking_dtls_id='$booking_dtls_id' and po_break_down_id='$buyerPoId' and requirment!=0 and status_active=1 and is_deleted=0 order by id ASC";
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
							
							$data_break_arr[$booking_dtls_id].=$row2[csf('description')].'_'.$color_arr[$row2[csf('color_number_id')]].'_'.$size_arr[$row2[csf('gmts_sizes')]].'_'.$row2[csf('requirment')].'_'.$row2[csf('rate')].'_'.$row2[csf('amount')].'_'.$row2[csf('id')].'_'.$break_down_arr[$row2[csf('id')]].'_'.$domestic_amount.',';
 							 
						}
				}
				
				
				   // if($data[5]==1) // (wotype)
					//{ // with Order
						  
					//}
				
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
					$wo_qnty=$row[csf('wo_qnty')];
					$domestic_amount=$row[csf('domestic_amount')];;
				}
				else
				{
					$wo_qnty=0;
					$order_uom=2;
				}
				
				$buyerPo=$row[csf('buyer_po_no')];
				$buyerPoId='';
				$buyerStyle=$row[csf('buyer_style_ref')];
				$buyer_buyer=$row[csf('buyer_buyer')];
				$readonly='';
				
			}
			
			if($order_uom==2){
				$qty_pcs=$wo_qnty*12;
			}else if($order_uom==1){
				$qty_pcs=$wo_qnty;
			}else{
				$qty_pcs=0;
			}
			
			//echo $row[csf('order_uom')];; die;
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
				<td><input name="txtbuyerPo_<? echo $tblRow; ?>" id="txtbuyerPo_<? echo $tblRow; ?>" value="<? echo $buyerPo;//$buyer_po_arr[$row[csf('po_break_down_id')]]['buyerpo']; ?>" class="text_boxes" type="text"  style="width:100px" <? echo $readonly; ?>  />
					<input name="txtbuyerPoId_<? echo $tblRow; ?>" id="txtbuyerPoId_<? echo $tblRow; ?>" value="<? echo $buyerPoId; //$row[csf('po_break_down_id')]; ?>" class="text_boxes" type="hidden" style="width:70px" />
				</td>
				<td><input name="txtstyleRef_<? echo $tblRow; ?>" id="txtstyleRef_<? echo $tblRow; ?>" value="<? echo $buyerStyle;//$buyer_po_arr[$row[csf('po_break_down_id')]]['style']; ?>" class="text_boxes" type="text"  style="width:100px" <? echo $readonly; ?>  /></td>
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
				<td><? echo create_drop_down( "cboGmtsItem_".$tblRow, 90, $garments_item,"", 1, "-- Select --",$row[csf('gmt_item')], "",1,"" ); ?></td>			
				<td><? echo create_drop_down( "cboProcessName_".$tblRow, 80, $emblishment_name_array,"", 1, "--Select--", $row[csf('emb_name')],  "change_caption_n_uom (".$tblRow.",this.value);", 1,"" ); ?></td>
				<td id="embltype_td_<? echo $tblRow; ?>"><? echo create_drop_down( "cboembtype_".$tblRow, 80, $type_array[$row[csf('emb_name')]],"", 1, "-- Select --",$row[csf('emb_type')], "",1,"" ); ?></td>
				<td><? echo create_drop_down( "cboBodyPart_".$tblRow, 80, $body_part,"", 1, "-- Select --",$row[csf('body_part_id')], "",1,"" ); ?></td>

				<td><input name="txtOrderQuantity[]" id="txtOrderQuantity_<? echo $tblRow; ?>" value="<? echo number_format($wo_qnty,4,'.',''); ?>" class="text_boxes_numeric" type="text"  style="width:60px" onClick="openmypage_order_qnty(1,'<? echo $row[csf('booking_dtls_id')]; ?>',<? echo $tblRow; ?>)" placeholder="Click To Search" readonly /></td>
				<td><? 	//	echo create_drop_down( "cboUom_".$tblRow, 60, $unit_of_measurement,"", 1, "-- Select --",2,"", 1,"" );
				 echo create_drop_down( "cboUom_".$tblRow, 60, $unit_of_measurement,"", 0, "-- Select --",$order_uom,"fnc_load_uom(1,this.value);", 1,"2,1" );
				 ?></td>
				<td><input name="txtRate_<? echo $tblRow; ?>" id="txtRate_<? echo $tblRow; ?>" value="<? echo number_format($row[csf('rate')],4,'.',''); ?>" type="text"  class="text_boxes_numeric" style="width:60px" readonly/></td>
				<td><input name="txtAmount[]" id="txtAmount_<? echo $tblRow; ?>" value="<? echo number_format($row[csf('amount')],4,'.',''); ?>" type="text" style="width:70px"  class="text_boxes_numeric" readonly /></td> 
                <td align="center"><input type="text" name="txtdomisticamount[]" id="txtdomisticamount_<? echo $tblRow; ?>" class="text_boxes_numeric" style="width:90px;" value="<? echo number_format($domestic_amount,4,'.',''); ?>" readonly /></td>
				<td><input name="txtQtyPcs[]" id="txtQtyPcs_<? echo $tblRow; ?>" type="text" class="text_boxes_numeric" value="<? echo number_format($qty_pcs,4,'.',''); ?>" style="width:67px" readonly />
				<td><input name="txtSmv_<? echo $tblRow; ?>" id="txtSmv_<? echo $tblRow; ?>" type="text" value="<? echo $smv; ?>" class="text_boxes_numeric" style="width:40px" />
				<td><input type="text" name="txtOrderDeliveryDate_<? echo $tblRow; ?>"  id="txtOrderDeliveryDate_<? echo $tblRow; ?>" value="<? echo change_date_format($row[csf("delivery_date")]);?>" class="datepicker" style="width:50px" /></td>
				<td><input name="txtWastage_<? echo $tblRow; ?>" id="txtWastage_<? echo $tblRow; ?>" type="text" value="<? echo $wastage; ?>" class="text_boxes_numeric" style="width:40px" />
					<input name="hdnDtlsUpdateId_<? echo $tblRow; ?>" id="hdnDtlsUpdateId_<? echo $tblRow; ?>" type="hidden" value="<? echo $dtls_id; ?>" class="text_boxes_numeric" style="width:40px" />
					<input type="hidden" name="hdnDtlsdata_<? echo $tblRow; ?>" id="hdnDtlsdata_<? echo $tblRow; ?>" value="<? echo implode("__",array_filter(explode(',',$data_break_arr[$dtls_id]))); ?>">
                    <input type="hidden" name="hdnbookingDtlsId_<? echo $tblRow; ?>" id="hdnbookingDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf('booking_dtls_id')]; ?>">
				</td>
				<td width="65">
					<input type="button" id="increase_1" name="increase[]" style="width:20px" class="formbuttonplasminus" value="+" onClick="add_dtls_tr(1)" />
					<input type="button" id="decrease_1" name="decrease[]" style="width:20px" class="formbuttonplasminus" value="-" onClick="fnc_delet_dtls_tr(1);" />
				</td>
			</tr>
			<?
		}
	}
	else
	{
		?>		
		<tr>
			<td><input name="txtbuyerPo_1" id="txtbuyerPo_1" type="text" class="text_boxes" style="width:100px" readonly />
				<input name="txtbuyerPoId_1" id="txtbuyerPoId_1" type="hidden" class="text_boxes" style="width:70px" />
			</td>
			<td><input name="txtstyleRef_1" id="txtstyleRef_1" type="text" class="text_boxes" style="width:100px" readonly /></td>
             <td><input id="txtbuyer_1" name="txtbuyer[]" type="text" class="text_boxes" style="width:100px" placeholder="Display" /></td>
			<td><? echo create_drop_down( "cboGmtsItem_1", 90, $garments_item,"", 1, "-- Select --",$selected, "",0,"" ); ?></td>
			<td><? echo create_drop_down( "cboProcessName_1", 80, $emblishment_name_array,"", 1, "--Select--",0,"change_caption_n_uom(this.value);", "","" ); ?></td>
			<td><? echo create_drop_down( "cboembtype_1", 80, $blank_array,"", 1, "-- Select --",$selected, "",0,"" ); ?></td>
			<td><? echo create_drop_down( "cboBodyPart_1", 80, $body_part,"", 1, "-- Select --",$selected, "",0,"" ); ?></td>
			<td><input name="txtOrderQuantity[]" id="txtOrderQuantity_1" class="text_boxes_numeric" type="text"  style="width:60px" onClick="openmypage_order_qnty(1,'0',1)" placeholder="Click To Search" readonly /></td>
			<td><? //echo create_drop_down( "cboUom_1", 60, $unit_of_measurement,"", 1, "-- Select --",2,"", 1,"" );
			
			echo create_drop_down( "cboUom_1", 60, $unit_of_measurement,"", 0, "-- Select --",2,"fnc_load_uom(1,this.value);", 1,"2,1" );
			
			 ?></td>
			<td><input name="txtRate_1" id="txtRate_1" type="text"  class="text_boxes_numeric" style="width:60px" /></td>
			<td><input name="txtAmount[]" id="txtAmount_1" type="text" style="width:70px"  class="text_boxes_numeric" readonly /></td> 
             <td align="center"><input type="text" name="txtdomisticamount[]" id="txtdomisticamount_1" class="text_boxes_numeric" style="width:90px;" readonly /></td>
            <td><input name="txtQtyPcs[]" id="txtQtyPcs_1" type="text"  class="text_boxes_numeric" style="width:67px" readonly /></td>
			<td><input name="txtSmv_1" id="txtSmv_1" type="text"  class="text_boxes_numeric" style="width:40px" /></td> 
			<td><input type="text" name="txtOrderDeliveryDate_1" id="txtOrderDeliveryDate_1" class="datepicker" style="width:50px" /></td>
			<td>
				<input name="txtWastage_1" id="txtWastage_1" type="text"  class="text_boxes_numeric" style="width:40px" />
				<input type="hidden" name="hdnDtlsUpdateId_1" id="hdnDtlsUpdateId_1">
				<input type="hidden" name="hdnDtlsdata_1" id="hdnDtlsdata_1">
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

if($action=="order_qty_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	$color_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$size_arr=return_library_array( "select id,size_name from  lib_size where status_active=1 and is_deleted=0",'id','size_name');
    ?>
    <script>
    	var str_color = [<? echo substr(return_library_autocomplete( "select color_name from lib_color where status_active=1 and is_deleted=0 group by color_name ", "color_name" ), 0, -1); ?> ];
    	var str_size = [<? echo substr(return_library_autocomplete( "select size_name from lib_size where status_active=1 and is_deleted=0 group by size_name ", "size_name" ), 0, -1); ?> ];
		
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
			$('#txtorderquantity_'+i).removeAttr("onKeyUp").attr("onKeyUp","sum_total_qnty("+i+");");
			$('#txtorderrate_'+i).removeAttr("onKeyUp").attr("onKeyUp","sum_total_qnty("+i+");");
			$('#txtcolor_'+i).removeAttr("disabled");
			$('#txtorderquantity_'+i).removeAttr("disabled");
			$('#decreaseset_'+i).removeAttr("disabled");			
			//$('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+','+'"tbl_share_details_entry"'+");");
			$('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");
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
				if($("#txtcolor_"+i).val()=="") $("#txtcolor_"+i).val(0);
				if($("#txtsize_"+i).val()=="") $("#txtsize_"+i).val(0);
				if($("#txtorderquantity_"+i).val()=="") $("#txtorderquantity_"+i).val(0);
				if($("#txtorderrate_"+i).val()=="") $("#txtorderrate_"+i).val(0);
				if($("#txtorderamount_"+i).val()=="") $("#txtorderamount_"+i).val(0);
				if($("#txtdomisticamount_"+i).val()=="") $("#txtdomisticamount_"+i).val(0);
				if($("#hidbookingconsid_"+i).val()=="") $("#hidbookingconsid_"+i).val(0);
				if($("#hiddenid_"+i).val()=="") $("#hiddenid_"+i).val(0);
				
				if(data_break_down=="")
				{
					data_break_down+=$('#txtdescription_'+i).val()+'_'+$('#txtcolor_'+i).val()+'_'+$('#txtsize_'+i).val()+'_'+$('#txtorderquantity_'+i).val()+'_'+$('#txtorderrate_'+i).val()+'_'+$('#txtorderamount_'+i).val()+'_'+$('#hidbookingconsid_'+i).val()+'_'+$('#hiddenid_'+i).val()+'_'+$('#txtdomisticamount_'+i).val();
				}
				else
				{
					data_break_down+="__"+$('#txtdescription_'+i).val()+'_'+$('#txtcolor_'+i).val()+'_'+$('#txtsize_'+i).val()+'_'+$('#txtorderquantity_'+i).val()+'_'+$('#txtorderrate_'+i).val()+'_'+$('#txtorderamount_'+i).val()+'_'+$('#hidbookingconsid_'+i).val()+'_'+$('#hiddenid_'+i).val()+'_'+$('#txtdomisticamount_'+i).val();
				}
			}
			$('#hidden_break_tot_row').val( data_break_down );
			/*alert(tot_row);//return;*/
			parent.emailwindow.hide();
		}

		function sum_total_qnty(id)
		{
			
			var receive_qty = $("#hiddenOrderQuantity_"+id).attr('placeholder')*1;
			var txtorderquantity=$("#txtorderquantity_"+id).val()*1;
			var hiddenid=$("#hiddenid_"+id).val();
			//alert(txtorderquantity+'<'+receive_qty)
			if(hiddenid!='' && hiddenid !=0)
			{
				if(txtorderquantity<receive_qty)
				{
					alert('Order Quantity Cannot Be Less Than Received Quantity');
					$("#txtorderquantity_"+id).val('');
					return;
				}
			}
			//alert(receive_qty);alert(txtorderquantity);
			var ddd={ dec_type:5, comma:0, currency:''};
			var tot_row=$('#tbl_share_details_entry tbody tr').length;
			$("#txtorderamount_"+id).val(($("#txtorderquantity_"+id).val()*1)*($("#txtorderrate_"+id).val()*1));
			
			var amount=($("#txtorderquantity_"+id).val()*1)*($("#txtorderrate_"+id).val()*1);
			var exchange_rate=$("#hidden_exchange_rate").val()*1;
			var domisticamount=amount*exchange_rate;
			$("#txtdomisticamount_"+id).val(domisticamount);
			
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

	</script>
</head>
<body onLoad="set_auto_complete('color_return'); set_auto_complete_size('size_return');">
	<div align="center" style="width:100%;" >
		<form name="qntypopup_1"  id="qntypopup_1" autocomplete="off">
			<table class="rpt_table" width="630px" cellspacing="0" cellpadding="0" rules="all" id="tbl_share_details_entry">
				<thead>
					<th width="130">Embelishment Description</th>
					<th width="100">Color</th>
					<th width="80">GMTS Size</th>
					<th width="70" class="must_entry_caption">Order Qty</th>
					<th width="60">Rate</th>
					<th width="80">Amount</th>
					<th>&nbsp;</th>
				</thead>
				<tbody>
					<input type="hidden" name="hidden_process_id" id="hidden_process_id" value="<? echo $process_id; ?>">
                    <input type="hidden" name="hidden_exchange_rate" id="hidden_exchange_rate" value="<? echo $exchange_rate; ?>">
					<input type="hidden" name="txtDeletedId" id="txtDeletedId" class="text_boxes_numeric" style="width:90px" readonly />
					<input type="hidden" name="hidden_break_tot_row" id="hidden_break_tot_row" class="text_boxes" style="width:90px" />
                    
					<?
					//echo $data_break.'==';
					$sql_break_down="select id, mst_id, order_id, job_no_mst, book_con_dtls_id, item_id, body_part, embellishment_type, description, color_id, size_id, qnty, rate, amount,domestic_amount from subcon_ord_breakdown where mst_id='$hdnDtlsUpdateId' and job_no_mst='$job_no' and  status_active=1 and is_deleted=0 ";
					$data_break_down=sql_select($sql_break_down);
					$break_down_arr=array(); 
					if(count($data_break_down)<1  && $data_break!=''){
						$data_break=$data_break;
					}else{
						$data_break='';
					}
					//echo $data_break.'==';
					//$data_break="";
					foreach($data_break_down as $row)
					{
						$break_down_arr[$row[csf('book_con_dtls_id')]]=$row[csf('id')];
						if($row[csf('description')]=="") $row[csf('description')]=0;
						if($row[csf('color_id')]=="") $row[csf('color_id')]=0;
						if($row[csf('size_id')]=="") $row[csf('size_id')]=0;
						if($row[csf('qnty')]=="") $row[csf('qnty')]=0;
						if($row[csf('rate')]=="") $row[csf('rate')]=0;
						if($row[csf('amount')]=="") $row[csf('amount')]=0;
						if($row[csf('domestic_amount')]=="") $row[csf('domestic_amount')]=0;
						
						if($data_break=="") $data_break.=$row[csf('description')].'_'.$row[csf('color_id')].'_'.$row[csf('size_id')].'_'.$row[csf('qnty')].'_'.$row[csf('rate')].'_'.$row[csf('amount')].'_'.$row[csf('id')].'_'.$row[csf('id')].'_'.$row[csf('domestic_amount')];
						else $data_break.='__'.$row[csf('description')].'_'.$row[csf('color_id')].'_'.$row[csf('size_id')].'_'.$row[csf('qnty')].'_'.$row[csf('rate')].'_'.$row[csf('amount')].'_'.$row[csf('id')].'_'.$row[csf('id')].'_'.$row[csf('domestic_amount')];
					}
					//echo $within_group;
					if($within_group==1)
					{	
						$rec_number=return_field_value( "exchange_rate", "subcon_ord_mst"," embellishment_job='$job_no' and status_active=1 and is_deleted=0 and  entry_form=311");
						$sql = "select id, description, color_number_id, gmts_sizes, requirment, color_size_table_id, rate, amount from wo_emb_book_con_dtls where wo_booking_dtls_id='$booking_dtls_id' and po_break_down_id='$booking_po_id' and requirment!=0 and status_active=1 and is_deleted=0 order by id ASC";
						$data_arr=sql_select($sql);
						$data_break="";
						foreach($data_arr as $row)
						{
							if($row[csf('description')]=="") $row[csf('description')]=0;
							if($row[csf('color_number_id')]=="") $row[csf('color_number_id')]=0;
							if($row[csf('gmts_sizes')]=="") $row[csf('gmts_sizes')]=0;
							if($row[csf('requirment')]=="") $row[csf('requirment')]=0;
							if($row[csf('rate')]=="") $row[csf('rate')]=0;
							if($row[csf('amount')]=="") $row[csf('amount')]=0;
							if($break_down_arr[$row[csf('id')]]=="") $break_down_arr[$row[csf('id')]]=0;
							$domestic_amount=$row[csf('amount')]*$exchange_rate;
							if($domestic_amount=="") $domestic_amount=0;
							
							if($data_break=="") $data_break.=$row[csf('description')].'_'.$row[csf('color_number_id')].'_'.$row[csf('gmts_sizes')].'_'.$row[csf('requirment')].'_'.$row[csf('rate')].'_'.$row[csf('amount')].'_'.$row[csf('id')].'_'.$break_down_arr[$row[csf('id')]].'_'.$domestic_amount;
							else $data_break.='__'.$row[csf('description')].'_'.$row[csf('color_number_id')].'_'.$row[csf('gmts_sizes')].'_'.$row[csf('requirment')].'_'.$row[csf('rate')].'_'.$row[csf('amount')].'_'.$row[csf('id')].'_'.$break_down_arr[$row[csf('id')]].'_'.$domestic_amount;
						}
					}
					
					/*if(count($data_array)<1)
					{
						$sql = "select id, description, color_number_id, gmts_sizes, requirment, color_size_table_id, rate, amount from wo_emb_book_con_dtls where wo_booking_dtls_id='$booking_dtls_id' and status_active=1 and is_deleted=0 order by id ASC";
					}*/
					
					$order_uom=return_field_value( "order_uom", "subcon_ord_mst a ,subcon_ord_dtls b"," a.id=b.mst_id and b.id='$hdnDtlsUpdateId' and a.embellishment_job='$job_no' and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  and  a.entry_form=311"); 
					
					//echo $order_uom; die;
					if($order_uom==2)
					{
						$order_uomDzn=12;
					}
					else if($order_uom==1)
					{
						$order_uomDzn=1;	
					}
					$receive_sql_break_down="select a.id, a.qnty as order_qty,b.job_break_id,b.quantity as receive_qty  from subcon_ord_breakdown a,sub_material_dtls b ,SUB_MATERIAL_MST c where a.id=b.job_break_id and b.mst_id=c.id and c.entry_form =312 and  a.mst_id='$hdnDtlsUpdateId' and a.job_no_mst='$job_no' and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.qnty ,b.job_break_id,b.quantity ";
					$receive_data_break_down=sql_select($receive_sql_break_down);
					$receive_break_down_arr=array();  
					foreach($receive_data_break_down as $row)
					{
						$receive_break_down_arr[$row[csf('job_break_id')]]['order_qty']+=$row[csf('order_qty')];
						$receive_break_down_arr[$row[csf('job_break_id')]]['receive_qty']+=$row[csf('receive_qty')]/$order_uomDzn;
 					}
					
					//echo "<pre>";
					//print_r($receive_break_down_arr); die;
					
					$k=0; 
					$data_array=explode("__",$data_break);
					if($within_group==1) $disabled="disabled"; else $disabled="";
					if(count($data_array)>0)
					{
						foreach($data_array as $row)
						{
							$data=explode('_',$row);
							$k++;
							$receive_qty=0;
							$receive_qty=$receive_break_down_arr[$data[7]]['receive_qty'];
							
							//echo $receive_qty; 
							//die;
							
							?>
							<tr>
								<td><input type="text" id="txtdescription_<? echo $k;?>" name="txtdescription_<? echo $k;?>" class="text_boxes" style="width:120px" value="<? echo $data[0]; ?>" />
								</td>
								<td>
									<input type="text" id="txtcolor_<? echo $k;?>" name="txtcolor_<? echo $k;?>" class="text_boxes txt_color" style="width:90px" value="<? echo $color_arr[$data[1]]; ?>" <? echo $disabled; ?> /></td>
								<td><input type="text" id="txtsize_<? echo $k;?>" name="txtsize_<? echo $k;?>" class="text_boxes txt_size" style="width:70px" value="<? echo $size_arr[$data[2]]; ?>" <? echo $disabled; ?> ></td>
								<td>
									<input type="text" id="txtorderquantity_<? echo $k;?>" name="txtorderquantity[]" class="text_boxes_numeric" style="width:60px" onBlur="sum_total_qnty(<? echo $k;?>);" value="<? echo number_format($data[3],4,'.',''); ?>" <? echo $disabled; ?> />
									<input type="hidden" id="hiddenOrderQuantity_<? echo $k;?>" name="hiddenOrderQuantity_<? echo $k;?>" class="text_boxes_numeric" style="width:70px" value="<? echo $data[3]; ?>"    placeholder="<? echo $receive_qty; ?>"  />
								</td>
								<td><input type="text" id="txtorderrate_<? echo $k;?>" name="txtorderrate_<? echo $k;?>"  class="text_boxes_numeric" style="width:50px" onBlur="sum_total_qnty(<? echo $k;?>)" value="<? echo number_format($data[4],4,'.',''); ?>" <? echo $disabled; ?> />
								</td>
								<td><input type="text" id="txtorderamount_<? echo $k;?>" name="txtorderamount_<? echo $k;?>" class="text_boxes_numeric" style="width:70px" value="<? echo number_format($data[5],4,'.',''); ?>" disabled/>
                                <input type="hidden" id="txtdomisticamount_<? echo $k;?>" name="txtdomisticamount_<? echo $k;?>" class="text_boxes_numeric" style="width:70px" value="<? echo $data[8]; ?>" disabled/>
                                
                                </td>
								<td>
									<input type="hidden" id="hidbookingconsid_<? echo $k; ?>" name="hidbookingconsid_<? echo $k; ?>"  style="width:15px;" class="text_boxes" value="<? echo $data[6]; ?>" />
                                    <input type="hidden" id="hiddenid_<? echo $k; ?>" name="hiddenid_<? echo $k; ?>"  style="width:15px;" class="text_boxes" value="<? echo $data[7]; ?>" />
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
                            <td><input type="text" id="txtcolor_1" name="txtcolor_1" class="text_boxes txt_color" style="width:90px" value="" /></td>
                            <td><input type="text" id="txtsize_1" name="txtsize_1" class="text_boxes txt_size" style="width:70px" value="" ></td>
                            <td>
                                <input type="text" id="txtorderquantity_1" name="txtorderquantity[]" class="text_boxes_numeric" style="width:60px" onBlur="sum_total_qnty(1);" value="" />
                                <input type="hidden" id="hiddenOrderQuantity_1" name="hiddenOrderQuantity_1" class="text_boxes_numeric" style="width:70px" value="" />
                            </td>
                            <td><input type="text" id="txtorderrate_1" name="txtorderrate_1"  class="text_boxes_numeric" style="width:50px" onBlur="sum_total_qnty(1)" value="" /></td>
                            <td>
                            <input type="text" id="txtorderamount_1" name="txtorderamount_1" class="text_boxes_numeric" style="width:70px" value="" disabled/>
                            <input type="hidden" id="txtdomisticamount_1" name="txtdomisticamount_1" class="text_boxes_numeric" style="width:70px" value="" disabled/>
                            
                            </td>
                            <td>
                            	<input type="hidden" id="hidbookingconsid_1" name="hidbookingconsid_1"  style="width:15px;" class="text_boxes" value="" />
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
					<th colspan="3">Total</th> 
					<th><input type="text" id="txt_total_order_qnty" name="txt_total_order_qnty" class="text_boxes_numeric" readonly style="width:70px" value="<? echo $break_tot_qty;//number_format($break_tot_qty,4); ?>"; /></th>
					<th><input type="text" id="txt_average_rate" name="txt_average_rate" class="text_boxes_numeric" readonly style="width:61px" value="<? echo $break_avg_rate; ?>"; /></th>
					<th><input type="text" id="txt_total_order_amount" name="txt_total_order_amount" class="text_boxes_numeric" readonly style="width:70px" value="<? echo $break_total_value; ?>"; /></th>
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
?>