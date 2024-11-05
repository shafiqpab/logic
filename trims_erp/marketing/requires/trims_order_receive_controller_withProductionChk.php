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
	
	echo create_drop_down( $dropdown_name, 150, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );	
	exit();
}

if ($action=="load_drop_down_member")
{
	$data=explode("_",$data);
	$sql="select b.id,b.team_member_name  from lib_marketing_team a, lib_mkt_team_member_info b where a.id=b.team_id and   a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.project_type=3 and team_id='$data[0]'";
	echo create_drop_down( "cbo_team_member", 150, $sql,"id,team_member_name", 1, "-- Select Member --", $selected, "" );	
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

if($action=="load_drop_down_subsection")
{
	$data=explode('_',$data);
	if($data[0]==1) $subID='1,2,3';
	else if($data[0]==3) $subID='4,5';
	else if($data[0]==5) $subID='6,7,8,9,10,11,12,13';
	else if($data[0]==10) $subID='14,15';
	else $subID='0';
	//echo $data[0]."**".$subID;
	//echo create_drop_down( "cboembtype_".$data[1], 80,$emb_type,"", 1, "-- Select --", "", "","","" ); 
	echo create_drop_down( "cboSubSection_".$data[1], 90, $trims_sub_section,"",1, "-- Select Sub-Section --","","load_sub_section_value($data[1])",0,$subID,'','','','','',"cboSubSection[]");
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
		
		$receive_date=strtotime(str_replace("'",'',$txt_order_receive_date));
		$delivery_date=strtotime(str_replace("'",'',$txt_delivery_date));
		$current_date=strtotime(date("d-m-Y"));
		if($receive_date>$delivery_date)
		{
			echo "26**"; die;
		}
		else if($receive_date != $current_date)
		{
			echo "25**"; die;
		}

		
		if($db_type==0) $insert_date_con="and YEAR(insert_date)=".date('Y',time())."";
		else if($db_type==2) $insert_date_con="and TO_CHAR(insert_date,'YYYY')=".date('Y',time())."";
		
		$new_job_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'TOR', date("Y",time()), 5, "select job_no_prefix,job_no_prefix_num from subcon_ord_mst where entry_form=255 and company_id=$cbo_company_name $insert_date_con order by id desc ", "job_no_prefix", "job_no_prefix_num" ));
		if(str_replace("'",'',$txt_order_no)=="")
		{
			$txt_order_no=$new_job_no[0];
		}
		else
		{
			$txt_order_no=str_replace("'",'',$txt_order_no);
		}
		
		if($db_type==0)
		{
			$txt_delivery_date=change_date_format(str_replace("'",'',$txt_delivery_date),'yyyy-mm-dd');
			$txt_rec_start_date=change_date_format(str_replace("'",'',$txt_rec_start_date),'yyyy-mm-dd');
			$txt_rec_end_date=change_date_format(str_replace("'",'',$txt_rec_end_date),'yyyy-mm-dd');
			$txt_order_receive_date=change_date_format(str_replace("'",'',$txt_order_receive_date),'yyyy-mm-dd');
		}
		else
		{
			$txt_delivery_date=change_date_format(str_replace("'",'',$txt_delivery_date), "", "",1);
			$txt_rec_start_date=change_date_format(str_replace("'",'',$txt_rec_start_date), "", "",1);
			$txt_rec_end_date=change_date_format(str_replace("'",'',$txt_rec_end_date), "", "",1);
			$txt_order_receive_date=change_date_format(str_replace("'",'',$txt_order_receive_date), "", "",1);
		}
		$id=return_next_id("id","subcon_ord_mst",1);
		$id1=return_next_id( "id", "subcon_ord_dtls",1);
		$id3=return_next_id( "id", "subcon_ord_breakdown", 1 );
		$rID3=true;
		$field_array="id, entry_form, subcon_job, job_no_prefix, job_no_prefix_num, company_id, location_id, within_group, party_id, party_location, currency_id, receive_date, delivery_date, rec_start_date, rec_end_date, order_id, order_no, exchange_rate,team_leader,team_member, remarks, inserted_by, insert_date";
		$data_array="(".$id.", 255, '".$new_job_no[0]."', '".$new_job_no[1]."', '".$new_job_no[2]."', '".$cbo_company_name."', '".$cbo_location_name."', '".$cbo_within_group."', '".$cbo_party_name."', '".$cbo_party_location."', '".$cbo_currency."', '".$txt_order_receive_date."', '".$txt_delivery_date."','".$txt_rec_start_date."','".$txt_rec_end_date."', '".$hid_order_id."', '".$txt_order_no."', '".$txt_exchange_rate."', '".$cbo_team_leader."', '".$cbo_team_member."', '".$txt_remarks."', ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."')";
		
		$txt_job_no=$new_job_no[0];
		
		$field_array2="id, mst_id, job_no_mst, order_id, order_no, buyer_po_id, booking_dtls_id, order_quantity, order_uom, rate, amount, delivery_date, buyer_po_no, buyer_style_ref, buyer_buyer, section, sub_section, item_group, rate_domestic,  amount_domestic, is_with_order, booked_uom, booked_conv_fac, booked_qty, inserted_by, insert_date";
		$field_array3="id, mst_id, order_id, job_no_mst, book_con_dtls_id, description, color_id, size_id, qnty, rate, amount, booked_qty";

		$color_library_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
		$size_library_arr=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
		$data_array2 	= $data_array3="";  $add_commaa=0; $add_commadtls=0;

		for($i=1; $i<=$total_row; $i++)
		{			
			$txtbuyerPoId			= "txtbuyerPoId_".$i; 
			$txtbuyerPo				= "txtbuyerPo_".$i;
			$txtstyleRef			= "txtstyleRef_".$i;
			$txtbuyer				= "txtbuyer_".$i;
			$cboSection				= "cboSection_".$i;
			$cboSubSection			= "cboSubSection_".$i;
			$cboItemGroup			= "cboItemGroup_".$i;
			$txtOrderQuantity		= "txtOrderQuantity_".$i;
			$cboUom 				= "cboUom_".$i;
			$txtRate 				= "txtRate_".$i;
			$txtAmount 				= "txtAmount_".$i;			
			$txtOrderDeliveryDate 	= "txtOrderDeliveryDate_".$i;
			$txtDomRate 			= "txtDomRate_".$i;
			$txtDomamount 			= "txtDomamount_".$i;
			$hdnDtlsdata 			= "hdnDtlsdata_".$i;
			$hdnDtlsUpdateId 		= "hdnDtlsUpdateId_".$i;
			$hdnbookingDtlsId 		= "hdnbookingDtlsId_".$i;
			$txtIsWithOrder 		= "txtIsWithOrder_".$i;
			$cboBookUom 			= "cboBookUom_".$i;
			$txtConvFactor 			= "txtConvFactor_".$i;
			$txtBookQty 			= "txtBookQty_".$i;

			$orddelivery_date=strtotime(str_replace("'",'',$$txtOrderDeliveryDate));
			if($receive_date>$orddelivery_date)
			{
				echo "26**"; die;
			}
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
			
			$data_array2 .="(".$id1.",".$id.",'".$new_job_no[0]."','".$hid_order_id."','".$txt_order_no."','".$txtbuyerPoId."',".$$hdnbookingDtlsId.",".str_replace(",",'',$$txtOrderQuantity).",".$$cboUom.",".$$txtRate.",".str_replace(",",'',$$txtAmount).",'".$orderDeliveryDate."',".$$txtbuyerPo.",".$$txtstyleRef.",".$$txtbuyer.",".$$cboSection.",".$$cboSubSection.",".$$cboItemGroup.",".str_replace(",",'',$$txtDomRate).",".str_replace(",",'',$$txtDomamount).",".str_replace(",",'',$$txtIsWithOrder).",".$$cboBookUom.",".str_replace(",",'',$$txtConvFactor).",".str_replace(",",'',$$txtBookQty).",'".$user_id."','".$pc_date_time."')";
			
			$dtls_data=explode("***",str_replace("'",'',$$hdnDtlsdata));
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
				$booked_qty=str_replace(",",'',$exdata[3])*str_replace("'",'',$$txtConvFactor);
				$rate="'".str_replace(",",'',$exdata[4])."'";
				$amount="'".str_replace(",",'',$exdata[5])."'";
				$book_con_dtls_id="'".$exdata[6]."'";
				$dtlsup_id="'".$exdata[7]."'";
				
				if(str_replace("'","",$colorname)!="")
				{ 
					if (!in_array(str_replace("'","",$colorname),$new_array_color))
					{
						$color_id = return_id( str_replace("'","",$colorname), $color_library_arr, "lib_color", "id,color_name","255");  
						//echo $$txtColorName.'='.$color_id.'<br>';
						$new_array_color[$color_id]=str_replace("'","",$colorname);
						
					}
					else $color_id =  array_search(str_replace("'","",$colorname), $new_array_color); 
				}
				else
				{
					$color_id=0;
				}
				
				if(str_replace("'","",$sizename)!="")
				{ 
					if (!in_array(str_replace("'","",$sizename),$new_array_size))
					{
						$size_id = return_id( str_replace("'","",$sizename), $size_library_arr, "lib_size", "id,size_name","204");  
						//echo $$txtColorName.'='.$color_id.'<br>';
						$new_array_size[$size_id]=str_replace("'","",$sizename);
					}
					else $size_id =  array_search(str_replace("'","",$sizename), $new_array_size); 
				}
				else
				{
					$size_id=0;
				}
				
				if ($add_commadtls!=0) $data_array3 .=",";
				$data_array3.="(".$id3.",".$id1.",'".$hid_order_id."','".$new_job_no[0]."',".$book_con_dtls_id.",".$description.",'".$color_id."','".$size_id."',".$qty.",".$rate.",".$amount.",".$booked_qty.")";
				$id3=$id3+1; $add_commadtls++;
			}
			
			$id1=$id1+1; $add_commaa++;
			//echo "10**INSERT INTO subcon_ord_breakdown (".$field_array3.") VALUES ".$data_array3; die;			
		}
		//echo "10**INSERT INTO subcon_ord_mst (".$field_array.") VALUES ".$data_array; die;
		//echo "10**INSERT INTO subcon_ord_breakdown (".$field_array3.") VALUES ".$data_array3; die;
		//echo "10**INSERT INTO subcon_ord_dtls (".$field_array2.") VALUES ".$data_array2; die;
		$flag=1;
		$rID=sql_insert("subcon_ord_mst",$field_array,$data_array,1);
		if($rID==1) $flag=1; else $flag=0;
		if($flag==1)
		{
			$rID2=sql_insert("subcon_ord_dtls",$field_array2,$data_array2,1);
			if($rID2==1) $flag=1; else $flag=0;
		}
		
		if($data_array3!="" && $flag==1)
		{
			$rID3=sql_insert("subcon_ord_breakdown",$field_array3,$data_array3,1);
			if($rID3==1) $flag=1; else $flag=0;
		}
		//echo "10**".$rID."**".$rID2."**".$rID3; die;
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
				echo "0**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_order_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_order_no);
			}
		}
		else if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_order_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_order_no);
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

		$receive_date=strtotime(str_replace("'",'',$txt_order_receive_date));
		$delivery_date=strtotime(str_replace("'",'',$txt_delivery_date));
		//$current_date=strtotime(date("d-m-Y"));
		//echo "10**".$receive_date."**".$delivery_date; die;
		if($receive_date>$delivery_date)
		{
			echo "26**"; die;
		}
		

		$next_process=return_field_value( "sys_no", "trims_job_card_mst"," entry_form=257 and $update_id=received_id and status_active=1 and is_deleted=0");
		if($next_process!=''){
			//echo "20**".str_replace("'","",$txt_job_no)."**".$rec_number;
			echo "20**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no);
			die;
		}
		
		/*$rec_number=return_field_value( "sys_no", "sub_material_mst"," embl_job_no=$txt_job_no and status_active=1 and is_deleted=0 and trans_type=1");
		if($rec_number){
			echo "emblRec**".str_replace("'","",$txt_job_no)."**".$rec_number;
			die;
		}
		
		$recipe_number=return_field_value( "recipe_no", "pro_recipe_entry_mst"," job_no=$txt_job_no and status_active=1 and is_deleted=0 and entry_form=220");
		if($recipe_number){
			echo "emblRecipe**".str_replace("'","",$txt_job_no)."**".$recipe_number;
			die;
		}*/
		
		$color_library_arr=return_library_array( "select id,color_name from lib_color  where status_active=1 and is_deleted=0", "id", "color_name"  );
		$size_library_arr=return_library_array( "select id,size_name from lib_size  where status_active=1 and is_deleted=0", "id", "size_name"  );
		if($db_type==0)
		{
			$txt_delivery_date=change_date_format(str_replace("'",'',$txt_delivery_date),'yyyy-mm-dd');
			$txt_rec_start_date=change_date_format(str_replace("'",'',$txt_rec_start_date),'yyyy-mm-dd');
			$txt_rec_end_date=change_date_format(str_replace("'",'',$txt_rec_end_date),'yyyy-mm-dd');
			$txt_order_receive_date=change_date_format(str_replace("'",'',$txt_order_receive_date),'yyyy-mm-dd');
		}
		else
		{
			$txt_delivery_date=change_date_format(str_replace("'",'',$txt_delivery_date), "", "",1);
			$txt_rec_start_date=change_date_format(str_replace("'",'',$txt_rec_start_date), "", "",1);
			$txt_rec_end_date=change_date_format(str_replace("'",'',$txt_rec_end_date), "", "",1);
			$txt_order_receive_date=change_date_format(str_replace("'",'',$txt_order_receive_date), "", "",1);
		}

		if(str_replace("'",'',$is_apply_last_update)==1)
		{
			$lastUpdate=0;
			$revise_no=str_replace("'",'',$txt_revise_no)+1;
			$production_sql ="select a.id, a.order_id, a.received_id, a.job_id,b.id as prod_dtls_id, b.receive_dtls_id,b.job_dtls_id, b.break_id, b.production_qty, b.size_id, b.color_id, b.item_description,c.job_quantity from trims_production_mst a, trims_production_dtls b , trims_job_card_dtls c where a.entry_form=269 and a.within_group=1 and a.id=b.mst_id and b.job_dtls_id=c.id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.received_id=$update_id "; 
			$prodRecdtlSID_arr=array(); $prodRecdBrktlSID_arr=array(); $prodQty_arr=array(); $prod_item_dtls_arr=array();
			$production_sql_res = sql_select($production_sql);
			foreach($production_sql_res as $rows)
			{
				$prodQty_arr[$rows[csf('prod_dtls_id')]]=$rows[csf('production_qty')];
				$prodRecDIds=explode(",",$rows[csf('receive_dtls_id')]);
				foreach($prodRecDIds as $val)
				{
					$prodRecdtlSID_arr[$val]=$val;
				}
				$prod_Rec_DIds[$rows[csf('prod_dtls_id')]] 		=$rows[csf('receive_dtls_id')];
				$prod_job_DIds[$rows[csf('prod_dtls_id')]] 		=$rows[csf('job_dtls_id')];
				$job_item_dtls_arr[$rows[csf('job_dtls_id')]] 	+=$rows[csf('production_qty')];
				$prod_item_dtls_arr[$rows[csf('prod_dtls_id')]] =$rows[csf('item_description')].'_'.$rows[csf('size_id')].'_'.$rows[csf('color_id')];
			}

			$subcon_arr=array(); $subcon_brk_arr=array(); $dtlSID_arr=array(); $breakIDarr=array();
			$subcon_sql ="select a.id, a.subcon_job, a.company_id, a.location_id, a.party_id, a.receive_date, b.order_no, a.delivery_date , b.id as subDtlsID,b.booking_dtls_id, b.order_id, b.booked_qty, b.booked_conv_fac, b.order_quantity , b.rate , b.amount , b.rate_domestic, b.amount_domestic, a.exchange_rate,c.id as subBrkID,c.book_con_dtls_id, c.qnty, c.rate, c.amount ,c.booked_qty
			from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
			where a.entry_form=255 and a.within_group=1 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and b.status_active=1 and b.id=c.mst_id and a.id=$update_id
			order by a.id DESC";
			$subcon_sql_res = sql_select($subcon_sql);
			foreach($subcon_sql_res as $row)
			{
				$dtlSID_arr[$row[csf('subDtlsID')]]=$row[csf('subDtlsID')];
				$dtlSID_arr_qty[$row[csf('subDtlsID')]]['booked_qty']=$row[csf('booked_qty')];
				$dtlSID_arr_qty[$row[csf('subDtlsID')]]['booked_conv_fac']=$row[csf('booked_conv_fac')];
				$dtlSID_arr_qty[$row[csf('subDtlsID')]]['order_quantity']=$row[csf('order_quantity')];
				$dtlSID_arr_qty[$row[csf('subDtlsID')]]['rate']=$row[csf('rate')];
				$dtlSID_arr_qty[$row[csf('subDtlsID')]]['amount']=$row[csf('amount')];
				$dtlSID_arr_qty[$row[csf('subDtlsID')]]['amount_domestic']=$row[csf('amount_domestic')];
				$dtlSID_arr_qty[$row[csf('subDtlsID')]]['rate_domestic']=$row[csf('rate_domestic')];
				$dtlSID_arr_qty[$row[csf('subDtlsID')]]['exchange_rate']=$row[csf('exchange_rate')];
				$dtlSID_arr_qty[$row[csf('subDtlsID')]]['break_ids'].=$row[csf('subBrkID')].",";
				$brk_data_arr_qty[$row[csf('subBrkID')]]['break_datas']=$row[csf('qnty')]."_".$row[csf('rate')]."_".$row[csf('amount')]."_".$row[csf('booked_qty')];

				$field_array_re="booked_qty*order_quantity*rate*amount*rate_domestic*amount_domestic*is_revised*updated_by*update_date";
				$field_brk_array_re="qnty*rate*amount*booked_qty*is_revised";
				$field_job_array_re="job_quantity*is_revised*updated_by*update_date";
			}
		}  
		else
		{
			$lastUpdate=str_replace("'",'',$is_apply_last_update);
			$revise_no=str_replace("'",'',$txt_revise_no);
		} 
		//echo "10**".$revise_no; die;
		$field_array="location_id*within_group*party_id*party_location*currency_id*receive_date*delivery_date*rec_start_date*rec_end_date*order_id*order_no*exchange_rate*team_leader*team_member*remarks*is_apply_last_update*revise_no*updated_by*update_date";	
		$data_array="'".$cbo_location_name."'*'".$cbo_within_group."'*'".$cbo_party_name."'*'".$cbo_party_location."'*'".$cbo_currency."'*'".$txt_order_receive_date."'*'".$txt_delivery_date."'*'".$txt_rec_start_date."'*'".$txt_rec_end_date."'*'".$hid_order_id."'*'".$txt_order_no."'*'".$txt_exchange_rate."'*'".$cbo_team_leader."'*'".$cbo_team_member."'*'".$txt_remarks."'*'".$lastUpdate."'*'".$revise_no."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$field_array2="order_id*order_no*buyer_po_id*booking_dtls_id*order_quantity*order_uom*rate*amount*delivery_date*buyer_po_no*buyer_style_ref*buyer_buyer*section*sub_section*item_group*rate_domestic*amount_domestic*booked_uom*booked_conv_fac*booked_qty*updated_by*update_date";
		
		$field_array3="id, mst_id, order_id, job_no_mst, book_con_dtls_id, description, color_id, size_id, qnty, rate, amount,booked_qty";
		$field_array4="order_id*book_con_dtls_id*description*color_id*size_id*qnty*rate*amount*booked_qty";
		$field_array5="id, mst_id, job_no_mst, order_id, order_no, buyer_po_id, booking_dtls_id, order_quantity, order_uom, rate, amount, delivery_date, buyer_po_no, buyer_style_ref, buyer_buyer, section, sub_section, item_group, rate_domestic,  amount_domestic , booked_uom, booked_conv_fac, booked_qty, inserted_by, insert_date";

		//echo "10**".$operation; die;
		$id3=return_next_id( "id", "subcon_ord_breakdown", 1) ;
		$id1=return_next_id( "id", "subcon_ord_dtls",1) ;
		$dtlsIdForBreak=$id1;
		$add_comma=0;	$flag=""; $breakDelIds='';
		for($i=1; $i<=$total_row; $i++)
		{			
			$txtbuyerPoId			= "txtbuyerPoId_".$i; 
			$txtbuyerPo				= "txtbuyerPo_".$i;
			$txtstyleRef			= "txtstyleRef_".$i;
			$txtbuyer				= "txtbuyer_".$i;
			$cboSection				= "cboSection_".$i;
			$cboSubSection			= "cboSubSection_".$i;
			$cboItemGroup			= "cboItemGroup_".$i;
			$txtOrderQuantity		= "txtOrderQuantity_".$i;
			$cboUom 				= "cboUom_".$i;
			$txtRate 				= "txtRate_".$i;
			$txtAmount 				= "txtAmount_".$i;			
			$txtOrderDeliveryDate 	= "txtOrderDeliveryDate_".$i;
			$txtDomRate 			= "txtDomRate_".$i;
			$txtDomamount 			= "txtDomamount_".$i;
			$hdnDtlsdata 			= "hdnDtlsdata_".$i;
			$hdnDtlsUpdateId 		= "hdnDtlsUpdateId_".$i;
			$hdnbookingDtlsId 		= "hdnbookingDtlsId_".$i;
			$txtDeletedId 			= "txtDeletedId_".$i;
			$cboBookUom 			= "cboBookUom_".$i;
			$txtConvFactor 			= "txtConvFactor_".$i;
			$txtBookQty 			= "txtBookQty_".$i;

			$orddelivery_date=strtotime(str_replace("'",'',$$txtOrderDeliveryDate));
			if($receive_date>$orddelivery_date)
			{
				echo "26**"; die;
			}
			
			if(str_replace("'",'',$$txtDeletedId)!='')
			{
				$breakDelIds			.= str_replace("'",'',$$txtDeletedId).",";
			}
			else
			{
				$breakDelIds='';
			}
			//'".$aa."','".$hid_order_id."'
			
			
			$aa	=str_replace("'",'',$$hdnDtlsUpdateId);

			$revDtlsId[str_replace("'",'',$$hdnDtlsUpdateId)]=str_replace("'",'',$$hdnDtlsUpdateId);
			//echo "10**".$aa; //die;
			if($db_type==0)
			{
				$orderDeliveryDate=change_date_format(str_replace("'",'',$$txtOrderDeliveryDate),'yyyy-mm-dd');
			}
			else
			{
				$orderDeliveryDate=change_date_format(str_replace("'",'',$$txtOrderDeliveryDate), "", "",1);
			}
			if(str_replace("'",'',$$txtbuyerPoId)=="") $txtbuyerPoId=0; else $txtbuyerPoId=str_replace("'",'',$$txtbuyerPoId);
			if(str_replace("'",'',$$hdnbookingDtlsId)=="") $hdnbookingDtlsId=0; else $hdnbookingDtlsId=str_replace("'",'',$$hdnbookingDtlsId);
			//echo "10**".str_replace("'",'',$$hdnDtlsUpdateId);
			if(str_replace("'",'',$$hdnDtlsUpdateId)!="")
			{
				$data_array2[$aa]=explode("*",("".$hid_order_id."*'".$txt_order_no."'*".$txtbuyerPoId."*".$$hdnbookingDtlsId."*".str_replace(",",'',$$txtOrderQuantity)."*".$$cboUom."*".$$txtRate."*".str_replace(",",'',$$txtAmount)."*'".$orderDeliveryDate."'*".$$txtbuyerPo."*".$$txtstyleRef."*".$$txtbuyer."*".$$cboSection."*".$$cboSubSection."*".$$cboItemGroup."*".$$txtDomRate."*".$$txtDomamount."*".$$cboBookUom."*".str_replace(",",'',$$txtConvFactor)."*".str_replace(",",'',$$txtBookQty)."*".$user_id."*'".$pc_date_time."'"));
				$hdn_dtls_id_arr[]=str_replace("'",'',$$hdnDtlsUpdateId);
			}
			else
			{
				if ($add_commaa!=0) $data_array5 .=","; $add_comma=0;
				
				$data_array5 .="(".$id1.",".$update_id.",'".$txt_job_no."','".$hid_order_id."','".$txt_order_no."','".$txtbuyerPoId."','".$$hdnbookingDtlsId."',".str_replace(",",'',$$txtOrderQuantity).",".$$cboUom.",".$$txtRate.",".str_replace(",",'',$$txtAmount).",'".$orderDeliveryDate."',".$$txtbuyerPo.",".$$txtstyleRef.",".$$txtbuyer.",".$$cboSection.",".$$cboSubSection.",".$$cboItemGroup.",".str_replace(",",'',$$txtDomRate).",".str_replace(",",'',$$txtDomamount).",".$$cboBookUom.",".str_replace(",",'',$$txtConvFactor).",".str_replace(",",'',$$txtBookQty).",'".$user_id."','".$pc_date_time."')";
				$id1++;
			}
			$dtls_data=explode("***",str_replace("'",'',$$hdnDtlsdata));
			
			for($j=0; $j<count($dtls_data); $j++)
			{
				$exdata=explode("_",$dtls_data[$j]);
				$description="'".$exdata[0]."'";
				$colorname="'".$exdata[1]."'";
				$sizename="'".$exdata[2]."'";
				$qty="'".str_replace(",",'',$exdata[3])."'";
				$booked_qty=str_replace(",",'',$exdata[3])*str_replace("'",'',$$txtConvFactor);
				//echo "10**".str_replace(",",'',$exdata[3])."**".str_replace("'",'',$$txtConvFactor); die;
				$rate="'".str_replace(",",'',$exdata[4])."'";
				$amount="'".str_replace(",",'',$exdata[5])."'";
				$book_con_dtls_id="'".$exdata[6]."'";
				$dtlsup_id="'".$exdata[7]."'";
				$bb=$exdata[7];
				//echo "10**".$dtlsup_id;
				if(str_replace("'","",$colorname)!="")
				{ 
					if (!in_array(str_replace("'","",$colorname),$new_array_color))
					{
						$color_id = return_id( str_replace("'","",$colorname), $color_library_arr, "lib_color", "id,color_name","255");  
						//echo $$txtColorName.'='.$color_id.'<br>';
						$new_array_color[$color_id]=str_replace("'","",$colorname);
					}
					else $color_id =  array_search(str_replace("'","",$colorname), $new_array_color); 
				}
				else
				{
					$color_id=0;
				}
				
				if(str_replace("'","",$sizename)!="")
				{ 
					if (!in_array(str_replace("'","",$sizename),$new_array_size))
					{
						$size_id = return_id( str_replace("'","",$sizename), $size_library_arr, "lib_size", "id,size_name","255"); 
						//echo $$txtColorName.'='.$color_id.'<br>';
						$new_array_size[$size_id]=str_replace("'","",$sizename);
					}
					else $size_id =  array_search(str_replace("'","",$sizename), $new_array_size); 
				}
				else
				{
					$size_id=0;
				}
				
				if(str_replace("'",'',$is_apply_last_update)==1)
				{
					if(in_array(str_replace("'",'',$$hdnDtlsUpdateId),$prodRecdtlSID_arr)) //production found 
					{
						$actualProdQTY=''; $prodItem='';  $rcvDID=str_replace("'",'',$$hdnDtlsUpdateId);
						$neddToRevisedVal=$reOrder_quantity=$rerate=$reAmount=$reRate_domestic=$reAmount_domestic=$reBrkQty=$reBrkRate=$reBrkAmt=$reBrkBokQty=$totalOrdBookQty=0;
						foreach ($prod_Rec_DIds as $key => $value) // Received Dtls "Ids" in Production
						{
							$value_arr=explode(",",$value);
							foreach ($value_arr as $recvdID) // $rcvDID=2579
							{
								//echo "10**".$recvdID.'=='.$rcvDID."#";
								if($recvdID==$rcvDID)   // Check Rec DtlsId in Rec Dtls "Ids" in Production
								{
									$actualProdQTY 	+=$prodQty_arr[$key];  // sum prod qty aginst of prod dtlsId
									$actualRecIds 	.=$rcvDID.","; 
									$prodItem 		.=$prod_item_dtls_arr[$key]."#";
									$jobDIds 		.=$prod_job_DIds [$key].",";
								}
							}
						}
						$actualRecId=array_unique(explode(",",chop($actualRecIds,',')));
						foreach ($actualRecId as $ind => $id) 
						{
							$totalOrdBookQty+=$dtlSID_arr_qty[$id]['booked_qty']; //
						}
						$recBookQty			+=$dtlSID_arr_qty[$rcvDID]['booked_qty'];
						$actualProdItem		=array_unique(explode("#",$prodItem));
						$booked_conv_fac	=$dtlSID_arr_qty[$rcvDID]['booked_conv_fac'];
						$order_quantity 	=$dtlSID_arr_qty[$rcvDID]['order_quantity'];
						$rate 				=$dtlSID_arr_qty[$rcvDID]['rate'];
						$amount 			=$dtlSID_arr_qty[$rcvDID]['amount'];
						$rate_domestic 	 	=$dtlSID_arr_qty[$rcvDID]['rate_domestic'];
						$amount_domestic 	=$dtlSID_arr_qty[$rcvDID]['amount_domestic'];
						$exchange_rate 		=$dtlSID_arr_qty[$rcvDID]['exchange_rate'];
						
						$bookQtyWithOutThis	=$totalOrdBookQty-$recBookQty; 
						if($bookQtyWithOutThis>$actualProdQTY)
						{
							$neddToRevisedVal 	=$actualProdQTY-$bookQtyWithOutThis;
							$reOrder_quantity 	=$neddToRevisedVal/$booked_conv_fac;
							$rerate 			=$rate;
							$reAmount 			=$reOrder_quantity*$rate;
							$reRate_domestic 	=$exchange_rate*$rate;
							$reAmount_domestic 	=$exchange_rate*$reAmount;

							$break_ids=array_unique(explode(",",(chop($dtlSID_arr_qty[$rcvDID]['break_ids'],','))));
							$break_datas='';
							foreach ($break_ids as  $bId)
							{
								$break_datas 	= $brk_data_arr_qty[$bId]['break_datas'];
								$break_data 	=array_unique(explode("_",($break_datas)));
								$reBrkQty 		=($break_data[0]*$reOrder_quantity)/$order_quantity;
								$reBrkRate 		=$break_data[1];
								$reBrkAmt 		=($reBrkQty*$reBrkRate);
								$reBrkBokQty 	=($reBrkQty*$booked_conv_fac);
								
								$data_brk_array_re[$bId]=explode("*",("'".$reBrkQty."'*'".$reBrkRate."'*'".$reBrkAmt."'*'".$reBrkBokQty."'*1"));
								$revBrk_id_arr[]=$bId;
							}
						}
						else
						{
							$break_ids=array_unique(explode(",",(chop($dtlSID_arr_qty[$rcvDID]['break_ids'],','))));
							$break_datas='';
							foreach ($break_ids as  $bId)
							{
								$break_datas 	= $brk_data_arr_qty[$bId]['break_datas'];
								$break_data 	=array_unique(explode("_",($break_datas)));
								$data_brk_array_re[$bId]=explode("*",("'".$reBrkQty."'*'".$reBrkRate."'*'".$reBrkAmt."'*'".$reBrkBokQty."'*1"));
								$revBrk_id_arr[]=$bId;
							}
						}

						$jobD_ids=array_unique(explode(",",(chop($jobDIds,','))));
						$job_qty='';
						foreach ($jobD_ids as  $jId)
						{
							$job_prod_qty 		= $job_item_dtls_arr[$jId];
							$reJobQty 			= $job_prod_qty; //$actualProdQTY; //($job_qty*$reOrder_quantity)/$order_quantity;
							$data_job_arr_re[$jId]	=explode("*",("'".$reJobQty."'*1*".$user_id."*'".$pc_date_time."'"));
							$revJob_id_arr[]	=$jId;
						}

						$data_array_re[$rcvDID] =explode("*",("'".$neddToRevisedVal."'*'".$reOrder_quantity."'*'".$rerate."'*'".$reAmount."'*'".$reRate_domestic."'*'".$reAmount_domestic."'*1*".$user_id."*'".$pc_date_time."'"));
						$revDtls_id_arr[] =$rcvDID;

					}
				}


				if($bb==0)
				{
					if(str_replace("'",'',$$hdnDtlsUpdateId)!='')
					{
						$dtlsIdForBreak=str_replace("'",'',$$hdnDtlsUpdateId);
					}
					else
					{
						$dtlsIdForBreak=$dtlsIdForBreak;
					}
					if ($add_commadtls!=0) $data_array3 .=",";
					$data_array3.="(".$id3.",".$dtlsIdForBreak.",'".$hid_order_id."','".$txt_job_no."',".$book_con_dtls_id.",".$description.",'".$color_id."','".$size_id."',".$qty.",".$rate.",".$amount.",".$booked_qty.")";
					$id3=$id3+1; $add_commadtls++;
				}
				else if($bb!=0)
				{
					$data_array4[$bb]=explode("*",("".$hid_order_id."*".$book_con_dtls_id."*".$description."*".$color_id."*".$size_id."*".$qty."*".$rate."*".$amount."*".$booked_qty."*".$user_id."*'".$pc_date_time."'"));
					$hdn_break_id_arr[]		=$bb;
				}
			}
			/*if($$txtDeletedId!="")
			{
				$rID5 = execute_query("DELETE FROM subcon_ord_breakdown WHERE id in($$txtDeletedId)");
				if($rID5) $flag=1; else $flag=0; 
			}*/
		}

		if(str_replace("'",'',$is_apply_last_update)==1)
		{
			$result=array_diff($dtlSID_arr,$revDtlsId); // Difference between Order and new order

			//echo "10**";
			//echo "<pre>";
			//print_r($result);

			$actualRecIds=''; $revisedFlag=0;
			//$prodRecdtlSID_arr
			foreach ($result as $rcvDID => $val) 
			{
				$actualProdQTY=''; $prodItem=''; 
				$neddToRevisedVal=$reOrder_quantity=$rerate=$reAmount=$reRate_domestic=$reAmount_domestic=$reBrkQty=$reBrkRate=$reBrkAmt=$reBrkBokQty=$totalOrdBookQty=0;
				foreach ($prod_Rec_DIds as $key => $value) // Received Dtls "Ids" in Production
				{
					$value_arr=explode(",",$value);
					foreach ($value_arr as $recvdID) // $rcvDID=2579
					{
						//echo "10**".$recvdID.'=='.$rcvDID."#";
						if($recvdID==$rcvDID)   // Check Rec DtlsId in Rec Dtls "Ids" in Production
						{
							$actualProdQTY 	+=$prodQty_arr[$key];  // sum prod qty aginst of prod dtlsId
							$actualRecIds 	.=$rcvDID.","; 
							$prodItem 		.=$prod_item_dtls_arr[$key]."#";
							$jobDIds 		.=$prod_job_DIds [$key].",";
						}
					}
				}
				$actualRecId=array_unique(explode(",",chop($actualRecIds,',')));
				foreach ($actualRecId as $ind => $id) 
				{
					$totalOrdBookQty+=$dtlSID_arr_qty[$id]['booked_qty']; //
				}
				$recBookQty			+=$dtlSID_arr_qty[$rcvDID]['booked_qty'];
				$actualProdItem		=array_unique(explode("#",$prodItem));
				$booked_conv_fac	=$dtlSID_arr_qty[$rcvDID]['booked_conv_fac'];
				$order_quantity 	=$dtlSID_arr_qty[$rcvDID]['order_quantity'];
				$rate 				=$dtlSID_arr_qty[$rcvDID]['rate'];
				$amount 			=$dtlSID_arr_qty[$rcvDID]['amount'];
				$rate_domestic 	 	=$dtlSID_arr_qty[$rcvDID]['rate_domestic'];
				$amount_domestic 	=$dtlSID_arr_qty[$rcvDID]['amount_domestic'];
				$exchange_rate 		=$dtlSID_arr_qty[$rcvDID]['exchange_rate'];
				
				$bookQtyWithOutThis	=$totalOrdBookQty-$recBookQty; 
				if($bookQtyWithOutThis>$actualProdQTY)
				{
					$neddToRevisedVal 	=$actualProdQTY-$bookQtyWithOutThis;
					$reOrder_quantity 	=$neddToRevisedVal/$booked_conv_fac;
					$rerate 			=$rate;
					$reAmount 			=$reOrder_quantity*$rate;
					$reRate_domestic 	=$exchange_rate*$rate;
					$reAmount_domestic 	=$exchange_rate*$reAmount;

					$break_ids=array_unique(explode(",",(chop($dtlSID_arr_qty[$rcvDID]['break_ids'],','))));
					$break_datas='';
					foreach ($break_ids as  $bId)
					{
						$break_datas 	= $brk_data_arr_qty[$bId]['break_datas'];
						$break_data 	=array_unique(explode("_",($break_datas)));
						$reBrkQty 		=($break_data[0]*$reOrder_quantity)/$order_quantity;
						$reBrkRate 		=$break_data[1];
						$reBrkAmt 		=($reBrkQty*$reBrkRate);
						$reBrkBokQty 	=($reBrkQty*$booked_conv_fac);
						
						$data_brk_array_re[$bId]=explode("*",("'".$reBrkQty."'*'".$reBrkRate."'*'".$reBrkAmt."'*'".$reBrkBokQty."'*1"));
						$revBrk_id_arr[]=$bId;
					}
				}
				else
				{
					$break_ids=array_unique(explode(",",(chop($dtlSID_arr_qty[$rcvDID]['break_ids'],','))));
					$break_datas='';
					foreach ($break_ids as  $bId)
					{
						$break_datas 	= $brk_data_arr_qty[$bId]['break_datas'];
						$break_data 	=array_unique(explode("_",($break_datas)));
						$data_brk_array_re[$bId]=explode("*",("'".$reBrkQty."'*'".$reBrkRate."'*'".$reBrkAmt."'*'".$reBrkBokQty."'*1"));
						$revBrk_id_arr[]=$bId;
					}
				}

				$jobD_ids=array_unique(explode(",",(chop($jobDIds,','))));
				$job_qty='';
				foreach ($jobD_ids as  $jId)
				{
					$job_prod_qty 		= $job_item_dtls_arr[$jId];
					$reJobQty 			= $job_prod_qty; //$actualProdQTY; //($job_qty*$reOrder_quantity)/$order_quantity;
					$data_job_arr_re[$jId]	=explode("*",("'".$reJobQty."'*1*".$user_id."*'".$pc_date_time."'"));
					$revJob_id_arr[]	=$jId;
				}

				$data_array_re[$rcvDID] =explode("*",("'".$neddToRevisedVal."'*'".$reOrder_quantity."'*'".$rerate."'*'".$reAmount."'*'".$reRate_domestic."'*'".$reAmount_domestic."'*1*".$user_id."*'".$pc_date_time."'"));
				$revDtls_id_arr[] =$rcvDID;
			}


			if($data_array_re!="")
			{
				$rID_re=execute_query(bulk_update_sql_statement( "subcon_ord_dtls", "id",$field_array_re,$data_array_re,$revDtls_id_arr),1);
				if($rID_re) $flag=1; else $flag=0;
			}

			if($data_brk_array_re!="" && $flag==1)
			{
				$rIDBrk_re=execute_query(bulk_update_sql_statement( "subcon_ord_breakdown", "id",$field_brk_array_re,$data_brk_array_re,$revBrk_id_arr),1);
				if($rIDBrk_re) $flag=1; else $flag=0;
			}		
			//echo "10**a**".$data_job_arr_re."**".$flag; //die;
			if($data_job_arr_re!="" && $flag==1)
			{
				//echo "10**b**".$flag; //die;
				$rIDjob_re=execute_query(bulk_update_sql_statement( "trims_job_card_dtls", "id",$field_job_array_re,$data_job_arr_re,$revJob_id_arr),1);
				if($rIDjob_re) $flag=1; else $flag=0;
			}	
		}

		//echo "10**";
		//echo "<pre>";
		//print_r($data_array_re);
		//die;
		//echo "10**".$rIDjob_re."**".$flag; die;
		//echo "10**".bulk_update_sql_statement( "subcon_ord_dtls", "id",$field_array_re,$data_array_re,$revDtls_id_arr); die;
		//die;
		
			

		$rID=sql_update("subcon_ord_mst",$field_array,$data_array,"id",$update_id,0);  
		if($rID) $flag=1; else $flag=0;


		if($data_array2!="" && $flag==1)
		{
			$rID2=execute_query(bulk_update_sql_statement( "subcon_ord_dtls", "id",$field_array2,$data_array2,$hdn_dtls_id_arr),1);
			if($rID2) $flag=1; else $flag=0;
		}
		if($data_array5!="" && $flag==1)
		{
			//echo "10**INSERT INTO subcon_ord_dtls (".$field_array5.") VALUES ".$data_array5; die;
			$rID7=sql_insert("subcon_ord_dtls",$field_array5,$data_array5,0);
			if($rID7) $flag=1; else $flag=0;
		}
		
		$id_break=implode(',',$hiddenTblIdBreak);
		//echo "10**".bulk_update_sql_statement( "subcon_ord_breakdown", "id",$field_array4,$data_array4,$hdn_break_id_arr); die;
		if($data_array4!=""  && $flag==1)
		{
			$rID3=execute_query(bulk_update_sql_statement( "subcon_ord_breakdown", "id",$field_array4,$data_array4,$hdn_break_id_arr),1);
			if($rID3) $flag=1; else $flag=0;
		}
		//echo "10**".$data_array3; die;
		if($data_array3!=""  && $flag==1)
		{
			//echo "10**INSERT INTO subcon_ord_breakdown (".$field_array3.") VALUES ".$data_array3; die;
			$rID4=sql_insert("subcon_ord_breakdown",$field_array3,$data_array3,1);
			if($rID4) $flag=1; else $flag=0;		
		}
		
		$breakDelIds=chop($breakDelIds,",");
		if ($breakDelIds!=""  && $flag==1)
		{
			$rID8=execute_query( "delete from subcon_ord_breakdown where id in ( $breakDelIds)",0);
			if($rID8) $flag=1; else $flag=0;
		}

		if($txt_deleted_id!="" && $flag==1)
		{
			$field_array_status="updated_by*update_date*status_active*is_deleted";
			$data_array_status=$user_id."*'".$pc_date_time."'*0*1";

			$rID6=sql_multirow_update("subcon_ord_dtls",$field_array_status,$data_array_status,"id",$txt_deleted_id,0);
			if($flag==1)
			{
				if($rID6) $flag=1; else $flag=0; 
			} 

			$rID8=execute_query( "delete from subcon_ord_breakdown where mst_id in ( $txt_deleted_id)",0);
			if($flag==1)
			{
				if($rID8) $flag=1; else $flag=0; 
			} 
		}	
		//echo "10**".$rID.'='.$rID2.'='.$rID3.'='.$rID4.'='.$rID6.'='.$rID7.'='.$rID8.'='.$rID_re.'='.$rIDBrk_re.'='.$rIDjob_re.'='.$flag.'='.str_replace("'",'',$is_apply_last_update); die;
		//if($rID4) $flag=1; else $flag=0;
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no);
			}
		}
		else if($db_type==2)
		{  
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no);
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
		
		$next_process=return_field_value( "id", "trims_job_card_mst"," entry_form=257 and $update_id=received_id and status_active=1 and is_deleted=0");
		if($next_process!=''){
			echo "20**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no);
			die;
		}

		$flag=0;
		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		$rID=sql_update("subcon_ord_mst",$field_array,$data_array,"id",$update_id,1);
		if($rID)
		{
			 $flag=1; 
		}
		$rID1=sql_update("subcon_ord_dtls",$field_array,$data_array,"job_no_mst",$txt_job_no,1);
		if($flag==1)
		{
			if($rID1) $flag=1; else $flag=0; 
		}   
		//$rID = sql_delete("subcon_ord_dtls","status_active*is_deleted*updated_by*update_date","0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'",'id',$update_id2,1);
		$rID2=execute_query( "delete from subcon_ord_breakdown where job_no_mst=$txt_job_no",0);
		if($flag==1)
		{
			if($rID2) $flag=1; else $flag=0; 
		}

		if(str_replace("'",'',$cbo_within_group)==1)
		{
			$rID3=execute_query( "update wo_booking_mst set lock_another_process=0 where booking_no =".$txt_order_no."",1);
			if($flag==1)
			{
				if($rID1) $flag=1; else $flag=0; 
			} 
		}
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2)
		{
			if($rID)
			{
				oci_commit($con);
				echo "2**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no);
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

function sql_updates($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues,$commit)
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

	global $con;
	if( strpos($strQuery, "WHERE")==false)  return "0";
	echo "10**".$strQuery; die;
	$stid =  oci_parse($con, $strQuery);
	$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
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
			load_drop_down( 'trims_order_receive_controller', company+'_'+within_group, 'load_drop_down_buyer', 'buyer_td' );
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
			else if(val==3)
			{
				$('#search_by_td').html('Buyer Job');
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
                    <th colspan="8"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </tr>
                <tr>               	 
                    <th width="140" class="must_entry_caption">Company Name</th>
                    <th width="100">Within Group</th>                           
                    <th width="140">Party Name</th>
                    <th width="100">Search By</th>
                    <th width="100" id="search_by_td">System ID</th>
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
                            $search_by_arr=array(1=>"System ID",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style");
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
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year_selection').value, 'create_job_search_list_view', 'search_div', 'trims_order_receive_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
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
	
	$po_ids='';
	
	if($db_type==0) $id_cond="group_concat(b.id)";
	else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	//echo "select $id_cond as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
	if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
	{
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
	}
	//echo $po_ids;
	if ($po_ids!="") $po_idsCond=" and b.buyer_po_id in ($po_ids)"; else $po_idsCond="";
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
	$buyer_po_id_str=""; $buyer_po_no_str=""; $buyer_po_style_str="";
	if($db_type==0) 
	{
		$ins_year_cond="year(a.insert_date)";
		$color_id_str=",group_concat(c.color_id) as color_id";
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
		$color_id_str=",listagg(c.color_id,',') within group (order by c.color_id) as color_id";
		
		if($within_group==1)
		{
			$buyer_po_id_str=",listagg(b.buyer_po_id,',') within group (order by b.buyer_po_id)  as buyer_po_id";
		}
		else
		{
			$buyer_po_no_str=",listagg(b.buyer_po_no,',') within group (order by b.id) as buyer_po_no";
			$buyer_po_style_str=",listagg(b.buyer_style_ref,',') within group (order by b.id) as buyer_style";
		}
	}
	 $sql= "select a.id, a.subcon_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date $color_id_str $buyer_po_id_str $buyer_po_no_str $buyer_po_style_str
	 from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
	 where a.entry_form=255 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and b.status_active=1 $order_rcv_date $company $buyer $withinGroup $search_com_cond $po_idsCond $withinGroup and b.id=c.mst_id  
	 group by a.id, a.subcon_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date
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
				if($within_group==1)
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
				}
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$row[csf('subcon_job')]; ?>")' style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                    <td width="60" style="text-align:center;"><? echo $row[csf('year')]; ?></td>
                    <td width="120"><? echo $row[csf('order_no')]; ?></td>
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
	$nameArray=sql_select( "select id, subcon_job, company_id, location_id, party_id, currency_id, party_location, delivery_date, rec_start_date, rec_end_date, receive_date, within_group, party_location, order_id, order_no,exchange_rate,team_leader,team_member,remarks,is_apply_last_update,revise_no from subcon_ord_mst where subcon_job='$data' and entry_form=255 and status_active=1" );
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_job_no').value 				= '".$row[csf("subcon_job")]."';\n";  
		echo "document.getElementById('cbo_company_name').value 		= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_within_group').value 		= '".$row[csf("within_group")]."';\n";  
		echo "$('#cbo_company_name').attr('disabled','true')".";\n";
		echo "fnc_load_party(1,".$row[csf("within_group")].");\n";	
		//echo "load_drop_down( 'requires/trims_order_receive_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_location', 'location_td' );\n";	
		echo "document.getElementById('cbo_location_name').value 		= '".$row[csf("location_id")]."';\n";
		echo "document.getElementById('cbo_party_name').value			= '".$row[csf("party_id")]."';\n";
		echo "document.getElementById('cbo_currency').value				= '".$row[csf("currency_id")]."';\n";
		echo "fnc_load_party(2,".$row[csf("within_group")].");\n";	 
		echo "document.getElementById('cbo_party_location').value		= '".$row[csf("party_location")]."';\n";	
		echo "document.getElementById('txt_order_receive_date').value	= '".change_date_format($row[csf("receive_date")])."';\n"; 
		echo "document.getElementById('txt_delivery_date').value		= '".change_date_format($row[csf("delivery_date")])."';\n"; 
		echo "document.getElementById('txt_rec_start_date').value		= '".change_date_format($row[csf("rec_start_date")])."';\n"; 
		echo "document.getElementById('txt_rec_end_date').value			= '".change_date_format($row[csf("rec_end_date")])."';\n"; 
		echo "document.getElementById('hid_order_id').value          	= '".$row[csf("order_id")]."';\n";
		echo "document.getElementById('txt_order_no').value         	= '".$row[csf("order_no")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value        = '".$row[csf("exchange_rate")]."';\n";
		echo "document.getElementById('cbo_team_leader').value         	= '".$row[csf("team_leader")]."';\n";
		echo "load_drop_down( 'requires/trims_order_receive_controller', document.getElementById('cbo_team_leader').value, 'load_drop_down_member', 'member_td' );\n";
		echo "document.getElementById('txt_remarks').value         		= '".$row[csf("remarks")]."';\n";
		echo "$('#txt_order_no').attr('disabled','true')".";\n";
		echo "$('#cbo_within_group').attr('disabled','true')".";\n";
		echo "$('#cbo_party_name').attr('disabled','true')".";\n";
		echo "$('#cbo_currency').attr('disabled','true')".";\n";
		echo "document.getElementById('update_id').value          		= '".$row[csf("id")]."';\n";	
		echo "document.getElementById('is_apply_last_update').value     = '".$row[csf("is_apply_last_update")]."';\n";	
		echo "document.getElementById('txt_revise_no').value     		= '".$row[csf("revise_no")]."';\n";	
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
		load_drop_down( 'trims_order_receive_controller', company+'_'+1+'_'+party_name, 'load_drop_down_buyer', 'buyer_td' );
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
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_search_category').value+'_'+<? echo $company;?>+'_'+document.getElementById('cbo_search_type').value, 'create_booking_search_list_view', 'search_div', 'trims_order_receive_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
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
	if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[4]"; } else if($db_type==2) { $year_cond=" and to_char(a.insert_date,'YYYY')=$data[4]"; }

	if ($data[0]!=0) $sample_company=" and c.company_id='$data[0]'"; else { echo "Please Select Party First."; die; }
	if($db_type==0) { $sample_year_cond=" and YEAR(c.insert_date)=$data[4]"; } else if($db_type==2) { $sample_year_cond=" and to_char(c.insert_date,'YYYY')=$data[4]"; }

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

			if ($search_type==1) $sample_woorder_cond=" and c.booking_no = '$data[1]' ";
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

			if ($search_type==1) $sample_woorder_cond=" and c.booking_no = '$data[1]%' ";
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

			if ($search_type==1) $sample_woorder_cond=" and c.booking_no like '%$data[1]' ";
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

			if ($search_type==1) $sample_woorder_cond=" and c.booking_no like '%$data[1]%' ";
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
	if ($po_ids!="") $sample_po_idsCond=" and c.po_break_down_id in ($po_ids)"; else $sample_po_idsCond="";
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
	
	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="") $booking_date = "and a.booking_date between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
		$wo_year="YEAR(a.insert_date)";
		$pre_cost_trims_cond="group_concat(b.pre_cost_fabric_cost_dtls_id)";
		$gmts_item_cond="group_concat(b.gmt_item)";
		$po_id_cond="group_concat(b.po_break_down_id)";

		if ($data[2]!="" &&  $data[3]!="") $sample_booking_date = "and c.booking_date between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $sample_booking_date ="";
		$sample_wo_year="YEAR(c.insert_date)";
		$sample_pre_cost_trims_cond="NULL";
		$sample_gmts_item_cond="";
		$sample_po_id_cond="group_concat(c.po_break_down_id)";
	}
	else if($db_type==2)
	{
		if ($data[2]!="" &&  $data[3]!="") $booking_date = "and a.booking_date between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
		$wo_year="to_char(a.insert_date,'YYYY')";
		$pre_cost_trims_cond="listagg(b.pre_cost_fabric_cost_dtls_id,',') within group (order by b.pre_cost_fabric_cost_dtls_id)";
		$gmts_item_cond="listagg(b.gmt_item,',') within group (order by b.gmt_item)";
		$po_id_cond="listagg(b.po_break_down_id,',') within group (order by b.po_break_down_id)";

		if ($data[2]!="" &&  $data[3]!="") $sample_booking_date = "and c.booking_date between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $sample_booking_date ="";
		$sample_wo_year="to_char(c.insert_date,'YYYY')";
		$sample_pre_cost_trims_cond="NULL";
		$sample_gmts_item_cond="";
		$sample_po_id_cond="listagg(c.po_break_down_id,',') within group (order by c.po_break_down_id)";
	} 
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (0=>$comp,1=>$buyer_arr);
	$sql= "SELECT $wo_year as year, a.id, a.booking_type, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id, $pre_cost_trims_cond as pre_cost_trims_id, $po_id_cond as po_id ,1 as type from  wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type in(2,5) and a.status_active=1 and a.lock_another_process!=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $booking_date $company $woorder_cond $year_cond $po_idsCond group by a.insert_date, a.id, a.booking_type, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id 
	UNION
	SELECT $sample_wo_year as year, c.id, c.booking_type, c.booking_no, c.booking_no_prefix_num, c.company_id, c.buyer_id, c.job_no, c.booking_date, c.currency_id, $sample_pre_cost_trims_cond as pre_cost_trims_id, $sample_po_id_cond as po_id ,2 as type from  wo_non_ord_samp_booking_mst c, wo_non_ord_samp_booking_dtls d where c.booking_no=d.booking_no and c.booking_type in(5) and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $sample_booking_date $sample_company $sample_woorder_cond $sample_year_cond $sample_po_idsCond group by c.insert_date, c.id, c.booking_type, c.booking_no, c.booking_no_prefix_num, c.company_id, c.buyer_id, c.job_no, c.booking_date, c.currency_id";

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
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$row[csf('booking_no')].'_'.$row[csf('currency_id')].'_'.$row[csf('type')]; ?>")' style="cursor:pointer" >
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

/*if ($action=="populate_data_from_search_popup")
{
	//echo $action."nazim"; die;
	$data=explode('_',$data);
	$nameArray=sql_select( "select id, booking_type, booking_no, company_id, buyer_id, job_no, booking_date,currency_id from  wo_booking_mst where booking_type=6 and status_active=1 and is_deleted=0 and id='$data[0]'" );
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_order_no').value 	= '".$row[csf("booking_no")]."';\n";  
		echo "document.getElementById('cbo_party_name').value 	= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('hid_order_id').value		= '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_currency').value		= '".$row[csf("currency_id")]."';\n";

		//if($row[csf("booking_date")]=="0000-00-00" || $row[csf("booking_date")]=="") $booking_date=""; else $booking_date=change_date_format($row[csf("booking_date")]);   
		//echo "$('#cbo_company_name').attr('disabled','true')".";\n";
		//echo "load_drop_down( 'requires/trims_order_receive_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_location', 'location_td' );\n";	
		//echo "document.getElementById('cbo_location_name').value 	= '".$row[csf("location_id")]."';\n";
		//echo "load_drop_down( 'requires/trims_order_receive_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_buyer', 'buyer_td' );\n";

		//echo "document.getElementById('txt_process_id').value		= '".$row[csf("service_type")]."';\n"; 
		//echo "document.getElementById('cbo_currency').value			= '".$row[csf("currency_id")]."';\n"; 
	    //echo "document.getElementById('update_id').value          	= '".$row[csf("id")]."';\n";	
		//echo "set_button_status(0,'".$_SESSION['page_permission']."', 'fnc_job_order_entry',1);\n";	
	}
	exit();	
}*/

if( $action=='order_dtls_list_view' ) 
{
	//echo $data; die;2_FAL-TOR-19-00092_1_3895
	$data=explode('_',$data);
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$size_arr=return_library_array( "select id,size_name from lib_size",'id','size_name');
	$tblRow=0;
	$buyer_po_arr=array();
	
	$buyer_po_sql = sql_select("select a.style_ref_no, a.buyer_name, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst");
	
	foreach($buyer_po_sql as $row)
	{
		$buyer_po_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
		$buyer_po_arr[$row[csf('id')]]['buyerpo']=$row[csf('po_number')];
		$buyer_po_arr[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
	}
	unset($buyer_po_sql);
	
	//die;
	//print_r($data_dreak_arr);
	//2_FAL-TOR-19-00041_1_3797 
	if($data[2]==1 && $data[0]==1 )
	{
		if($data[4]==1)
		{
			$sql = "SELECT  a.id, a.booking_type, a.booking_no, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id, b.id as booking_dtls_id, b.po_break_down_id, b.trim_group,b.delivery_date,b.fabric_description, b.uom, b.wo_qnty, b.rate, b.amount
			from  wo_booking_mst a, wo_booking_dtls b, wo_trim_book_con_dtls c where a.booking_no=b.booking_no and a.booking_type=2 and c.wo_trim_booking_dtls_id=b.id and c.requirment>0 and  b.booking_no=trim('$data[1]') and a.status_active=1 and a.lock_another_process!=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.booking_type, a.booking_no, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id, b.id, b.po_break_down_id, b.trim_group, b.delivery_date,b.fabric_description, b.uom, b.wo_qnty, b.rate, b.amount order by b.id ASC";
		}
		else
		{
			$sql = "SELECT  a.id, a.booking_type, a.booking_no, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id, b.id as booking_dtls_id, a.po_break_down_id, b.trim_group,b.delivery_date,b.fabric_description, b.gmts_color, b.fabric_color, b.gmts_size, b.item_size, b.uom, b.trim_qty as wo_qnty, b.rate, b.amount
			from  wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=5  and a.booking_no=trim('$data[1]') and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.booking_type, a.booking_no, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id, b.id, a.po_break_down_id, b.trim_group, b.delivery_date,b.fabric_description, b.gmts_color, b.fabric_color, b.gmts_size, b.item_size, b.uom, b.trim_qty, b.rate, b.amount order by b.id ASC";
			//, wo_non_ord_samp_yarn_dtls c and c.wo_non_ord_samp_book_dtls_id=b.id
		}
	}
	else if($data[2]==1 && $data[0]==2 )
	{
		$sql = "SELECT id, mst_id, job_no_mst, order_id, order_no, buyer_po_id as po_break_down_id, booking_dtls_id, order_quantity as wo_qnty, order_uom, rate, amount, delivery_date, buyer_po_no, buyer_style_ref, buyer_buyer, section, sub_section, item_group as trim_group, rate_domestic,  amount_domestic, is_with_order, booked_uom, booked_conv_fac, booked_qty, is_revised from subcon_ord_dtls where job_no_mst='$data[1]' and mst_id='$data[3]' and status_active=1 and is_deleted=0 order by id ASC";
	}
	else
	{
		$sql = "SELECT id, mst_id, job_no_mst, order_id, order_no, buyer_po_id as po_break_down_id, booking_dtls_id, order_quantity as wo_qnty, order_uom, rate, amount, delivery_date, buyer_po_no, buyer_style_ref, buyer_buyer, section,sub_section, item_group as trim_group, rate_domestic,  amount_domestic , is_with_order, booked_uom, booked_conv_fac, booked_qty from subcon_ord_dtls where job_no_mst='$data[1]' and mst_id='$data[3]' and status_active=1 and is_deleted=0 order by id ASC";
	}
	//echo $sql; die; 
	$data_array=sql_select($sql); $del_date_arr=array();
	//print_r($data_array);
	$ind=0;
	if(count($data_array) > 0)
	{
		$exchange_rate=$data[3];
		$min_date=0; 
		foreach($data_array as $row)
		{
			$tblRow++;
			$dtls_id=0; $order_uom=0; $wo_qnty=0; $disabled_conv='';
			if($data[2]==1)  //within group yes 
			{
				//$dtls_id=$row[csf('id')]; 
				//$row[csf("delivery_date")]=$row[csf('delivery_date')];
				if($data[0]==1)
				{
					$order_uom=$row[csf('uom')];
					$del_date_arr[$ind++]=  $row[csf('delivery_date')] ;
				}
				else
				{
					$order_uom=$row[csf('order_uom')];
				} 
				$wo_qnty=$row[csf('wo_qnty')];
				$data_break='';
				if($data[0]==1)
				{
					if($data[4]==2)
					{
						if($row[csf('fabric_description')]=="") $row[csf('fabric_description')]=0;
						if($row[csf('fabric_color')]=="") $row[csf('fabric_color')]=0;
						if($row[csf('item_size')]=="") $row[csf('item_size')]=0;
						if($row[csf('wo_qnty')]=="") $row[csf('wo_qnty')]=0;
						if($row[csf('rate')]=="") $row[csf('rate')]=0;
						if($row[csf('amount')]=="") $row[csf('amount')]=0;
						if($break_down_arr[$row[csf('id')]]=="") $break_down_arr[$row[csf('id')]]=0;
						//echo $data_break."++";
						if($data_break=="") $data_break.=$row[csf('fabric_description')].'_'.$color_library[$row[csf('fabric_color')]].'_'.$size_arr[$row[csf('item_size')]].'_'.$row[csf('wo_qnty')].'_'.$row[csf('rate')].'_'.$row[csf('amount')].'_'.$row[csf('id')].'_'.$break_down_arr[$row[csf('id')]].'_'.$row[csf('fabric_color')].'_'.$row[csf('item_size')];
						else $data_break.='***'.$row[csf('fabric_description')].'_'.$color_library[$row[csf('fabric_color')]].'_'.$size_arr[$row[csf('item_size')]].'_'.$row[csf('wo_qnty')].'_'.$row[csf('rate')].'_'.$row[csf('amount')].'_'.$row[csf('id')].'_'.$break_down_arr[$row[csf('id')]].'_'.$row[csf('fabric_color')].'_'.$row[csf('item_size')];
					}
					else
					{
						$buyerpo=$buyer_po_arr[$row[csf('po_break_down_id')]]['buyerpo'];
						$style=$buyer_po_arr[$row[csf('po_break_down_id')]]['style'];
						$buyer_buyer=$buyer_po_arr[$row[csf('po_break_down_id')]]['buyer_name'];
						$break_down_id=$row[csf('po_break_down_id')];
						$booking_dtls_id=$row[csf('booking_dtls_id')];
						$disable_dropdown='1';
						$disabled='disabled';
						$sql = "select  id, wo_trim_booking_dtls_id, job_no,  po_break_down_id,  item_color, item_size, requirment,description, brand_supplier, rate, amount from wo_trim_book_con_dtls where wo_trim_booking_dtls_id='$booking_dtls_id' and status_active=1 and is_deleted=0 and requirment!=0 order by id ASC"; //die;
						$breakData_arr=sql_select($sql);
						//$data_break="";
						foreach($breakData_arr as $rows)
						{//echo "1--";
							if($rows[csf('description')]=="") $rows[csf('description')]=0;
							if($rows[csf('item_color')]=="") $rows[csf('item_color')]=0;
							if($rows[csf('item_size')]=="") $rows[csf('item_size')]=0;
							if($rows[csf('requirment')]=="") $rows[csf('requirment')]=0;
							if($rows[csf('rate')]=="") $rows[csf('rate')]=0;
							if($rows[csf('amount')]=="") $rows[csf('amount')]=0;
							if($break_down_arr[$rows[csf('id')]]=="") $break_down_arr[$rows[csf('id')]]=0;
							//echo $data_break."++";
							if($data_break=="") $data_break.=$rows[csf('description')].'_'.$color_library[$rows[csf('item_color')]].'_'.$rows[csf('item_size')].'_'.$rows[csf('requirment')].'_'.$rows[csf('rate')].'_'.$rows[csf('amount')].'_'.$rows[csf('id')].'_'.$break_down_arr[$rows[csf('id')]].'_'.$rows[csf('item_color')].'_'.$rows[csf('item_size')];
							else $data_break.='***'.$rows[csf('description')].'_'.$color_library[$rows[csf('item_color')]].'_'.$rows[csf('item_size')].'_'.$rows[csf('requirment')].'_'.$rows[csf('rate')].'_'.$rows[csf('amount')].'_'.$rows[csf('id')].'_'.$break_down_arr[$rows[csf('id')]].'_'.$rows[csf('item_color')].'_'.$rows[csf('item_size')];
						}
					}
				}
			}
			else if($data[2]==2)
			{
				if($data[0]==2)
				{
					$dtls_id=$row[csf('id')];
					$row[csf("delivery_date")]=$row[csf('delivery_date')];
					$order_uom=$row[csf('order_uom')];
					$wo_qnty=$row[csf('wo_qnty')];
					$buyerpo=$row[csf('buyer_po_no')];
					$style=$row[csf('buyer_style_ref')];
					$buyer_buyer=$row[csf('buyer_buyer')];
					$break_down_id="";
				}
				else
				{
					$wo_qnty=0;
				}
			}

			if($data[0]==2)
			{
				$job_no_mst=$row[csf('job_no_mst')];
				$dtlsID=$row[csf('id')];

				$qry_result=sql_select( "select id, mst_id, order_id, job_no_mst, book_con_dtls_id, description, color_id, size_id, qnty, rate, amount from subcon_ord_breakdown where job_no_mst='$data[1]' order by id");	
				$data_dreak_arr=array(); $data_dreak=''; $add_comma=0; $k=1;
				foreach ($qry_result as $rows)
				{
					if($rows[csf('description')]=="") $rows[csf('description')]=0;
					if($rows[csf('color_id')]=="") $rows[csf('color_id')]=0;
					if($rows[csf('size_id')]=="") $rows[csf('size_id')]=0;
					if($rows[csf('qnty')]=="") $rows[csf('qnty')]=0;
					if($rows[csf('rate')]=="") $rows[csf('rate')]=0;
					if($rows[csf('amount')]=="") $rows[csf('amount')]=0;
					if($rows[csf('book_con_dtls_id')]=="") $rows[csf('book_con_dtls_id')]=0;
					if(!in_array($rows[csf('mst_id')],$temp_arr_mst_id))
					{
						$temp_arr_mst_id[]=$rows[csf('mst_id')];
						//if($k!=1) {  }
						$add_comma=0; $data_dreak='';
						
					}
					//echo $add_comma.'='.$data_dreak.'='.$k.'<br>';
					$k++;
					
					if ($add_comma!=0) $data_dreak ="***";
					$data_dreak_arr[$rows[csf('mst_id')]].=$rows[csf('description')].'_'.$color_library[$rows[csf('color_id')]].'_'.$size_arr[$rows[csf('size_id')]].'_'.$rows[csf('qnty')].'_'.$rows[csf('rate')].'_'.$rows[csf('amount')].'_'.$rows[csf('book_con_dtls_id')].'_'.$rows[csf('id')].'#';
					$add_comma++;
				}
			}

			if($data[0]==1)
			{
				$domRate=$row[csf('rate')]*$exchange_rate; 
				$domAmount=$row[csf('amount')]*$exchange_rate;
				//$buyer_buyer='';
				if($data[4]==1)
				{
					$disabled='disabled';
					$disable_dropdown='1';
				}
				$isWithOrder=$data[4];
			}
			else
			{
				$domRate=$row[csf('rate_domestic')]; 
				$domAmount=$row[csf('amount_domestic')];
				$buyer_buyer=$row[csf('buyer_buyer')];
				$style=$row[csf('buyer_style_ref')];
				$buyer_po_id=$row[csf('po_break_down_id')];
				$buyerpo=$row[csf('buyer_po_no')];
				$isWithOrder=$row[csf('is_with_order')];
				if($isWithOrder==1)
				{
					$disable_dropdown='1';
					$disabled='disabled';
				}
				//$disabled='';
				//$disable_dropdown='0';
			}
			if ($tblRow%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			if ($row[csf('is_revised')]==1) $bgcolor="#ffb8a9";
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
				<td><input id="txtbuyerPo_<? echo $tblRow; ?>" name="txtbuyerPo[]" value="<? echo $buyerpo; ?>" class="text_boxes" type="text"  style="width:100px" <? echo $disabled ?> />
					<input id="txtbuyerPoId_<? echo $tblRow; ?>" name="txtbuyerPoId[]" value="<? echo $break_down_id; ?>" class="text_boxes" type="hidden" style="width:70px" readonly />
				</td>
				<td><input id="txtstyleRef_<? echo $tblRow; ?>" name="txtstyleRef[]" value="<? echo $style; ?>" class="text_boxes" type="text"  style="width:100px" <? echo $disabled ?> /></td>
				<td>
					<? 
					if($data[2]==1 && $isWithOrder==1)
					{
						echo create_drop_down( "txtbuyer_".$tblRow, 100, "select id, buyer_name from lib_buyer where status_active=1","id,buyer_name", 1, "-- Select --",$buyer_buyer, "",$disable_dropdown,'','','','','','',"txtbuyer[]");
					}
					else
					{
						?>
						<input id="txtbuyer_<? echo $tblRow; ?>" name="txtbuyer[]" value="<? echo $buyer_buyer; ?>" class="text_boxes" type="text"  style="width:87px"  <? echo $disabled ?>  />
						<?
					}

					if($row[csf('section')]==1) $subID='1,2,3';
					else if($row[csf('section')]==3) $subID='4,5';
					else if($row[csf('section')]==5) $subID='6,7,8,9,10,11,12,13';
					else if($row[csf('section')]==10) $subID='14,15';
					else $subID='0';
					?>
				</td>
				<td><? echo create_drop_down( "cboSection_".$tblRow, 90, $trims_section,"", 1, "-- Select Section --",$row[csf('section')],"load_sub_section($tblRow)",0,'','','','','','',"cboSection[]"); ?></td>			
				<td id="subSectionTd_<? echo $tblRow; ?>"><? echo create_drop_down( "cboSubSection_".$tblRow, 90, $trims_sub_section,"", 1, "-- Select Section --",$row[csf('sub_section')],"load_sub_section_value($tblRow)",0,$subID,'','','','','',"cboSubSection[]"); ?></td>			
				<td><? echo create_drop_down( "cboItemGroup_".$tblRow, 90, "select id, item_name from lib_item_group where item_category=4 and status_active=1","id,item_name", 1, "-- Select --",$row[csf('trim_group')], "",$disable_dropdown,'','','','','','',"cboItemGroup[]"); ?></td>
				<td><? echo create_drop_down( "cboUom_".$tblRow, 60, $unit_of_measurement,"", 1, "-- Select --",$order_uom,"", 1,'','','','','','',"cboUom[]"); ?>	</td>
				<td><input id="txtOrderQuantity_<? echo $tblRow; ?>" name="txtOrderQuantity[]" value="<? echo number_format($wo_qnty,4,'.',''); ?>" class="text_boxes_numeric" type="text"  style="width:60px" onClick="openmypage_order_qnty(<? echo $tblRow; ?>)" placeholder="Click To Search" readonly /></td>
				<td><? echo create_drop_down( "cboBookUom_".$tblRow, 60, $unit_of_measurement,"", 1, "-- Select --",$row[csf('booked_uom')],1, 1,'','','','','','',"cboBookUom[]"); ?>	</td>
				<?
				if($row[csf('booked_uom')]==$order_uom)
				{
					$disabled_conv="disabled";
				}
				?>
				<td><input id="txtConvFactor_<? echo $tblRow; ?>" name="txtConvFactor[]" type="text"  class="text_boxes_numeric" value="<? echo $row[csf('booked_conv_fac')]; ?>"  onkeyup="cal_booked_qty(<? echo $tblRow; ?>);" style="width:47px"  <? echo $disabled_conv; ?>  /></td>
				<td><input id="txtBookQty_<? echo $tblRow; ?>" name="txtBookQty[]" type="text"  class="text_boxes_numeric" style="width:57px"  value="<? echo number_format($row[csf('booked_qty')],4,'.',''); ?>" readonly="readonly" /></td>
				<td><input id="txtRate_<? echo $tblRow; ?>" name="txtRate[]" value="<? echo number_format($row[csf('rate')],4,'.',''); ?>" type="text"  class="text_boxes_numeric" style="width:60px" readonly/></td>
				<td><input id="txtAmount_<? echo $tblRow; ?>" name="txtAmount[]"  value="<? echo number_format($row[csf('amount')],4,'.',''); ?>" type="text" style="width:70px"  class="text_boxes_numeric"  disabled /></td>
				<td><input id="txtDomRate_<? echo $tblRow; ?>" name="txtDomRate[]" value="<? echo number_format($domRate,4,'.',''); ?>" type="text"  class="text_boxes_numeric" style="width:57px" readonly /></td>
				<td><input id="txtDomamount_<? echo $tblRow; ?>" name="txtDomamount[]" value="<? echo number_format($domAmount,4,'.',''); ?>" type="text"  class="text_boxes_numeric" style="width:77px" readonly /></td>
				<td><input type="text"  id="txtOrderDeliveryDate_<? echo $tblRow; ?>" name="txtOrderDeliveryDate[]" value="<? echo change_date_format($row[csf("delivery_date")]);?>" class="datepicker" onChange="chk_min_del_date(<? echo $tblRow; ?>)" style="width:67px"  />
					<input id="hdnDtlsUpdateId_<? echo $tblRow; ?>" name="hdnDtlsUpdateId[]" type="hidden" value="<? echo $dtlsID; ?>" class="text_boxes_numeric" style="width:40px" />
					<input type="hidden" id="hdnDtlsdata_<? echo $tblRow; ?>" name="hdnDtlsdata[]" value="<?
					if($data[0]==1 && $data[2]==1)
					{
						echo $data_break;
					}
					else
					{
						echo implode("***",array_filter(explode('#',$data_dreak_arr[$dtlsID])));
					}
					?>">
	                <input type="hidden" id="hdnbookingDtlsId_<? echo $tblRow; ?>" name="hdnbookingDtlsId[]" value="<? echo $row[csf('booking_dtls_id')]; ?>">
	                <input type="hidden" id="txtDeletedId_<? echo $tblRow; ?>" name="txtDeletedId[]" value="">
	                <input type="hidden" id="txtIsWithOrder_<? echo $tblRow; ?>" name="txtIsWithOrder[]" value="<? echo $isWithOrder; ?>">
	            </td>
                <td width="65">
					<input type="button" id="increase_<? echo $tblRow; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fnc_addRow(
					<? echo $tblRow.","."'tbl_dtls_emb'".","."'row_'" ;?>)" />
					<input type="button" id="decrease_<? echo $tblRow; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fnc_deleteRow(<?echo $tblRow.","."'tbl_dtls_emb'".","."'row_'" ;?>);" />
				</td>
			</tr>
			<?
		}
	}
	else
	{
		?>		
		<tr id="row_1">
            <td><input id="txtbuyerPo_1" name="txtbuyerPo[]" name="text" class="text_boxes" style="width:100px" placeholder="Display"/>
            	<input id="txtbuyerPoId_1" name="txtbuyerPoId[]" type="hidden" class="text_boxes" style="width:70px"readonly />
            </td>
            <td><input id="txtstyleRef_1" name="txtstyleRef[]" type="text" class="text_boxes" style="width:100px" placeholder="Display"/></td>
             <td><input id="txtbuyer_1" name="txtbuyer[]" type="text" class="text_boxes" style="width:100px" placeholder="Display" /></td>
            <td><? echo create_drop_down( "cboSection_1", 90, $trims_section,"id,section_name", 1, "-- Select Section --","","load_sub_section($tblRow)",0,'','','','','','',"cboSection[]"); ?></td>
            <td id="subSectionTd_1"><? echo create_drop_down( "cboSubSection_1", 90, $trims_sub_section,"id,section_name", 1, "-- Select Sub Section --","",'',0,'','','','','','',"cboSubSection[]"); ?></td>
            <td><? echo create_drop_down( "cboItemGroup_1", 90, "select id, item_name from lib_item_group where item_category=4 and  status_active=1","id,item_name", 1, "-- Select --",$selected, "",0,'','','','','','',"cboItemGroup[]"); ?>	</td>
            <td><? echo create_drop_down( "cboUom_1", 60, $unit_of_measurement,"", 1, "-- Select --",2,1, 1,'','','','','','',"cboUom[]"); ?>	</td>
            <td><input id="txtOrderQuantity_1" name="txtOrderQuantity[]" class="text_boxes_numeric" type="text"  style="width:60px" onClick="openmypage_order_qnty(1,'0',1)" placeholder="Click To Search" readonly /></td>
            <td><input id="txtRate_1" name="txtRate[]" type="text"  class="text_boxes_numeric" style="width:60px" readonly="readonly" /></td>
            <td><input id="txtAmount_1" name="txtAmount[]" type="text" style="width:70px"  class="text_boxes_numeric" readonly /></td> 
            <td><input id="txtDomRate_1" name="txtDomRate[]" type="text"  class="text_boxes_numeric" style="width:57px" readonly="readonly" /></td> 
            <td><input id="txtDomamount_1" name="txtDomamount[]" type="text"  class="text_boxes_numeric" style="width:77px" readonly="readonly"  /></td> 
            <td><input type="text" name="txtOrderDeliveryDate[]" id="txtOrderDeliveryDate_1" class="datepicker"  onChange="chk_min_del_date(1)"  style="width:67px" />
            	<input type="hidden" name="hdnDtlsUpdateId[]" id="hdnDtlsUpdateId_1">
                <input type="hidden" name="hdnDtlsdata[]" id="hdnDtlsdata_1">
                <input type="hidden" name="hdnbookingDtlsId[]" id="hdnbookingDtlsId_1">
                <input type="hidden" name="txtDeletedId[]" id="txtDeletedId_1">
            </td>
            <td width="65">
				<input type="button" id="increase_1" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fnc_addRow(1,'tbl_dtls_emb','row_')" />
				<input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fnc_deleteRow(1,'tbl_dtls_emb','row_');" />
			</td>
        </tr> 
		<?
	}
	$min_dates=$del_date_arr[0];
	foreach($del_date_arr as $v) 
	{
		if(strtotime($min_dates)>strtotime($v))$min_dates=$v;

	}
	 
	?>
	<input type="hidden" id="min_date_id" name="min_date_id" value="<? echo change_date_format($min_dates);?>">

	<?

	exit();
}

if($action=="order_qty_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	$color_arr=return_library_array( "select id,color_name from lib_color",'id','color_name');
	$size_arr=return_library_array( "select id,size_name from  lib_size",'id','size_name');
    ?>
    <script>
    	var str_color = [<? echo substr(return_library_autocomplete( "select color_name from lib_color group by color_name ", "color_name" ), 0, -1); ?> ];
    	var str_size = [<? echo substr(return_library_autocomplete( "select size_name from lib_size group by size_name ", "size_name" ), 0, -1); ?> ];
		
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
				if (form_validation('txtorderquantity_'+i,'Quantity')==false)
				{
					return;
				}
				//alert($("#txtsize_"+i).val());
				if($("#txtdescription_"+i).val()=="") $("#txtdescription_"+i).val(0)
				if($("#txtcolor_"+i).val()=="") $("#txtcolor_"+i).val(0);
				if($("#txtsize_"+i).val()=="") $("#txtsize_"+i).val(0);
				if($("#txtorderquantity_"+i).val()=="") $("#txtorderquantity_"+i).val(0);
				if($("#txtorderrate_"+i).val()=="") $("#txtorderrate_"+i).val(0);
				if($("#txtorderamount_"+i).val()=="") $("#txtorderamount_"+i).val(0);
				if($("#hidbookingconsid_"+i).val()=="") $("#hidbookingconsid_"+i).val(0);
				if($("#hiddenid_"+i).val()=="") $("#hiddenid_"+i).val(0);
				if($("#txtcolorId_"+i).val()=="") $("#txtcolorId_"+i).val(0);
				if($("#txtsizeID_"+i).val()=="") $("#txtsizeID_"+i).val(0);
				
				if(data_break_down=="")
				{
					data_break_down+=$('#txtdescription_'+i).val()+'_'+$('#txtcolor_'+i).val()+'_'+$('#txtsize_'+i).val()+'_'+$('#txtorderquantity_'+i).val()+'_'+$('#txtorderrate_'+i).val()+'_'+$('#txtorderamount_'+i).val()+'_'+$('#hidbookingconsid_'+i).val()+'_'+$('#hiddenid_'+i).val()+'_'+$('#txtcolorId_'+i).val()+'_'+$('#txtsizeID_'+i).val();
				}
				else
				{
					data_break_down+="***"+$('#txtdescription_'+i).val()+'_'+$('#txtcolor_'+i).val()+'_'+$('#txtsize_'+i).val()+'_'+$('#txtorderquantity_'+i).val()+'_'+$('#txtorderrate_'+i).val()+'_'+$('#txtorderamount_'+i).val()+'_'+$('#hidbookingconsid_'+i).val()+'_'+$('#hiddenid_'+i).val()+'_'+$('#txtcolorId_'+i).val()+'_'+$('#txtsizeID_'+i).val();
				}
			}
			//alert(data_break_down);
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

	</script>
</head>
<body onLoad="set_auto_complete('color_return'); set_auto_complete_size('size_return');">
	<div align="center" style="width:100%;" >
		<form name="qntypopup_1"  id="qntypopup_1" autocomplete="off">
			<table class="rpt_table" width="630px" cellspacing="0" cellpadding="0" rules="all" id="tbl_share_details_entry">
				<thead>
					<th width="130">Description</th>
					<th width="100">Color</th>
					<th width="80">Size</th>
					<th width="70" class="must_entry_caption">Order Qty</th>
					<th width="60">Rate</th>
					<th width="80">Amount</th>
					<th>&nbsp;</th>
				</thead>
				<tbody>
					<input type="hidden" name="txtDeletedId" id="txtDeletedId" class="text_boxes_numeric" style="width:90px" readonly />
					<input type="hidden" name="hidden_break_tot_row" id="hidden_break_tot_row" class="text_boxes" style="width:90px" />
					<?
					
					$data_array=explode("***",$data_break);
					//echo $within_group;
					$k=0;
					//echo count($data_array);
					if($within_group==1) $disabled="disabled"; else $disabled="";
					if(count($data_array)>0)
					{
						foreach($data_array as $row)
						{
							$data=explode('_',$row);
							$k++;
							?>
							<tr>
								<td><input type="text" id="txtdescription_<? echo $k;?>" name="txtdescription_<? echo $k;?>" class="text_boxes" style="width:120px" value="<? echo $data[0]; ?>" />
								</td>
								<td>
									<input type="text" id="txtcolor_<? echo $k;?>" name="txtcolor_<? echo $k;?>" class="text_boxes txt_color" style="width:90px" value="<? echo $data[1]; ?>" >
									<input type="hidden" id="txtcolorId_<? echo $k;?>" name="txtcolorId_<? echo $k;?>" class="text_boxes_numeric" style="width:90px" value="<? echo $data[8]; ?>"  /></td>
								<td><input type="text" id="txtsize_<? echo $k;?>" name="txtsize_<? echo $k;?>" class="text_boxes txt_size" style="width:70px" value="<? echo $data[2]; ?>"  >
									<input type="hidden" id="txtsizeID_<? echo $k;?>" name="txtsizeID_<? echo $k;?>" class="text_boxes_numeric" style="width:70px" value="<? echo $data[9]; ?>"></td>
								<td>
									<input type="text" id="txtorderquantity_<? echo $k;?>" name="txtorderquantity_<? echo $k;?>" class="text_boxes_numeric" style="width:70px" onKeyUp="sum_total_qnty(<? echo $k;?>);" value="<? echo number_format($data[3],4,'.',''); ?>" <? echo $disabled; ?> />
									<input type="hidden" id="hiddenOrderQuantity_<? echo $k;?>" name="hiddenOrderQuantity_<? echo $k;?>" class="text_boxes_numeric" style="width:70px" value="<? echo $data[3]; ?>"  />
								</td>
								<td><input type="text" id="txtorderrate_<? echo $k;?>" name="txtorderrate_<? echo $k;?>"  class="text_boxes_numeric" style="width:60px" onKeyUp="sum_total_qnty(<? echo $k;?>)" value="<? echo number_format($data[4],4,'.',''); ?>" <? echo $disabled; ?> />
								</td>
								<td><input type="text" id="txtorderamount_<? echo $k;?>" name="txtorderamount_<? echo $k;?>" class="text_boxes_numeric" style="width:70px" value="<? echo number_format($data[5],4,'.',''); ?>" disabled/></td>
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
                            <td><input type="text" id="txtcolor_1" name="txtcolor_1" class="text_boxes txt_color" style="width:90px" value="" /><input type="hidden" id="txtcolorID_1" name="txtcolorID_1" class="text_boxes_numeric" style="width:90px" value="" /></td>
                            <td><input type="text" id="txtsize_1" name="txtsize_1" class="text_boxes txt_size" style="width:70px" value="" ><input type="hidden" id="txtsize_ID_1" name="txtsize_ID_1" class="text_boxes_numeric" style="width:90px" value="" /></td>
                            <td>
                                <input type="text" id="txtorderquantity_1" name="txtorderquantity_1" class="text_boxes_numeric" style="width:70px" onKeyUp="sum_total_qnty(1);" value="" />
                                <input type="hidden" id="hiddenOrderQuantity_1" name="hiddenOrderQuantity_1" class="text_boxes_numeric" style="width:70px" value="" />
                            </td>
                            <td><input type="text" id="txtorderrate_1" name="txtorderrate_1"  class="text_boxes_numeric" style="width:60px" onKeyUp="sum_total_qnty(1)" value="" /></td>
                            <td><input type="text" id="txtorderamount_1" name="txtorderamount_1" class="text_boxes_numeric" style="width:70px" value="" disabled/></td>
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

if($action=="check_conversion_rate")
{
	//$data=explode("**",$data);
	
	/*if($db_type==0)
	{
		$conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
	}
	else
	{
		$conversion_date=change_date_format($data[1], "d-M-y", "-",1);
	}*/
	$conversion_date=date("Y/m/d");
	$exchange_rate=set_conversion_rate( $data, $conversion_date );
	echo $exchange_rate;
	exit();	
}

if($action=="check_uom")
{
	$uom=return_field_value( "order_uom","lib_item_group","id='$data'");
	echo $uom;
	exit();	
}

if($action=="check_booked_uom")
{
	$data=explode("_",$data);
	$uom=return_field_value( "uom_id","lib_booked_uom_setup","company_id=$data[0] and section_id=$data[1] and sub_section_id=$data[2]");
	echo $uom;
	exit();	
}

//,is_master_part_updated    and within_group=1
if ($action == 'btn_load_change_bookings') {
	$sql = "select id, company_id,order_no, subcon_job from subcon_ord_mst where status_active=1 and is_deleted=0 and  is_apply_last_update=2 and within_group=1";
	$data_array = sql_select($sql);
	echo count($data_array);
	exit();
}



if ($action == 'show_change_bookings') {
	$sql = "select id, company_id,order_id,order_no, subcon_job from subcon_ord_mst where status_active=1 and is_deleted=0 and  is_apply_last_update=2";
	$data_array = sql_select($sql);
	/*$sales_po_booking_arr=$sales_wpo_booking_arr=array();
	foreach ($data_array as $row) {
		if($row[csf("booking_without_order")]==1){
			$sales_booking_arr[] = $row[csf("booking_id")];
		}else{
			$sales_booking_arr[] = $row[csf("booking_id")];
		}
	}

	$all_sales_po_booking_arr = array_filter($sales_booking_arr);
	$booking_cond="";
	if($db_type==2)
	{
		if(count($all_sales_po_booking_arr)>999)
		{
			$all_booking_chunk=array_chunk($all_sales_po_booking_arr,999) ;
			foreach($all_booking_chunk as $chunk_arr)
			{
				$bookCond .=" a.id in (".implode(",", $chunk_arr).") or ";
			}
			$booking_cond.=" and (".chop($bookCond,'or ').")";
		}else{
			$booking_cond=" and a.id in (".implode(",", $all_sales_po_booking_arr).")";
		}
	}
	else
	{
		$booking_cond=" and a.id in(".implode($sales_booking_arr).")";
	}

	$nameArray_approved = sql_select("select a.id,max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id $booking_cond group by a.id
		union all
		select a.id,max(b.approved_no) as approved_no from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and a.status_active=1 $booking_cond and a.ENTRY_FORM_ID=9 group by a.id");
	foreach ($nameArray_approved as $approve_row) {
		$approve_arr[$approve_row[csf("id")]] = $approve_row[csf("approved_no")];
	}*/

	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="290">
		<thead>
			<th width="20" align="center">SL</th>
			<th width="110">System ID</th>
			<th>Work Order</th>
		</thead>
	</table>
	<div style="width:290px; max-height:130px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="272" class="rpt_table" id="tbl_list_search_revised">
			<?
			$i = 1;
			foreach ($data_array as $row) {
				if ($row[csf("is_master_part_updated")] == 1) {
					$bgcolor = "#8FCF57";
				} else {
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";
				}
				?>
				<tr bgcolor="<? echo $bgcolor; ?>"
					onClick='set_form_data("<? echo $row[csf('id')] . "**" . $row[csf('company_id')] . "**" . $row[csf('subcon_job')]; ?>")'
					style="cursor:pointer">
					<td width="20" align="center"><? echo $i; ?></td>
					<td width="110"><? echo $row[csf('subcon_job')]; ?></td>
					<td ><? echo $row[csf('order_no')]; ?></td>
					
				</tr>
				<?
				$i++;
			}
			?>
		</table>
	</div>
	<?
	exit();
}

if( $action=='order_dtls_list_view_last_update' ) 
{
	//echo $data; die;2_FAL-TOR-19-00092_1_3895
	$data=explode('_',$data);
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$size_arr=return_library_array( "select id,size_name from lib_size",'id','size_name');
	$tblRow=0;

	
	
	//$prod_rec_dtls_ids=array_unique(explode(",",$prodRecdtlSID_arr));
	//$prod_rec_brk_ids=array_unique(explode(",",$prodRecdBrktlSID_arr));
	//unset($buyer_po_sql);
	//echo "<pre>";
	//print_r($prod_rec_dtls_ids);
	$subcon_arr=array(); $subcon_brk_arr=array(); $dtlSID_arr=array(); $breakIDarr=array();
	$subcon_sql ="select a.id, a.subcon_job, a.company_id, a.location_id, a.party_id, a.receive_date, b.order_no, a.delivery_date , b.id as subDtlsID,b.booking_dtls_id, b.order_id, b.booked_qty,c.id as subBrkID,c.book_con_dtls_id
	from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
	where a.entry_form=255 and a.within_group=1 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and b.status_active=1 and b.id=c.mst_id and b.order_no=trim('$data[1]') 
	order by a.id DESC";
	$subcon_sql_res = sql_select($subcon_sql);
	foreach($subcon_sql_res as $row)
	{
		$dtlSID_arr[$row[csf('subDtlsID')]]=$row[csf('subDtlsID')];
		$dtlSID_arr_qty[$row[csf('subDtlsID')]]['booked_qty']=$row[csf('booked_qty')];
		$breakIDarr[$row[csf('subBrkID')]]=$row[csf('subBrkID')];

		$subcon_arr[$row[csf('order_no')]][$row[csf('booking_dtls_id')]]['subDtlsID']=$row[csf('subDtlsID')];
		$subcon_brk_arr[$row[csf('booking_dtls_id')]][$row[csf('book_con_dtls_id')]]['subBrkID']=$row[csf('subBrkID')];
	}
	unset($buyer_po_sql);
	//echo "<pre>";
	//print_r($subcon_arr);
	$buyer_po_arr=array();
	$buyer_po_sql = sql_select("select a.style_ref_no, a.buyer_name, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst");
	
	foreach($buyer_po_sql as $row)
	{
		$buyer_po_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
		$buyer_po_arr[$row[csf('id')]]['buyerpo']=$row[csf('po_number')];
		$buyer_po_arr[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
	}
	unset($buyer_po_sql);
	if($data[4]==1)
	{
		$sql = "SELECT  a.id, a.booking_type, a.booking_no, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id, b.id as booking_dtls_id, b.po_break_down_id, b.trim_group,b.delivery_date,b.fabric_description, b.uom, b.wo_qnty, b.rate, b.amount
		from  wo_booking_mst a, wo_trim_book_con_dtls c, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=2 and c.wo_trim_booking_dtls_id=b.id and c.requirment>0 and  b.booking_no=trim('$data[1]') and a.status_active=1 and a.lock_another_process=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.booking_type, a.booking_no, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id, b.id, b.po_break_down_id, b.trim_group, b.delivery_date,b.fabric_description, b.uom, b.wo_qnty, b.rate, b.amount order by b.id ASC";
	}
	else
	{
		$sql = "SELECT  a.id, a.booking_type, a.booking_no, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id, b.id as booking_dtls_id, a.po_break_down_id, b.trim_group,b.delivery_date,b.fabric_description, b.gmts_color, b.fabric_color, b.gmts_size, b.item_size, b.uom, b.trim_qty as wo_qnty, b.rate, b.amount
		from  wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=5  and a.booking_no=trim('$data[1]') and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.booking_type, a.booking_no, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id, b.id, a.po_break_down_id, b.trim_group, b.delivery_date,b.fabric_description, b.gmts_color, b.fabric_color, b.gmts_size, b.item_size, b.uom, b.trim_qty, b.rate, b.amount order by b.id ASC";
		//, wo_non_ord_samp_yarn_dtls c and c.wo_non_ord_samp_book_dtls_id=b.id
	}

	//echo $sql; die; 
	$data_array=sql_select($sql); $del_date_arr=array();
	//print_r($data_array);
	$ind=0;
	if(count($data_array) > 0)
	{
		$exchange_rate=$data[3];
		$min_date=0;  $revDtlsId=array();
		foreach($data_array as $row)
		{
			$tblRow++;
			$order_uom=0; $wo_qnty=0; $disabled_conv='';
			//echo $row[csf('booking_no')]."==".$row[csf('booking_dtls_id')]."++";
			$dtlsID=$subcon_arr[$row[csf('booking_no')]][$row[csf('booking_dtls_id')]]['subDtlsID'];
			//$revDtlsId[$dtlsID]=$dtlsID;

			/*if(!in_array($dtlsID,$dtlSID_arr)) // new row
			{
				echo "a";
				$prodQTY='';
				if(in_array($dtlsID,$prod_rec_dtls_ids))
				{
					echo "b";
					foreach ($bb as $key => $value) 
					{
						$value_arr=explode(",",$value);
						if(in_array($dtlsID,$value_arr))
						{
							$prodQTY +=$prodQty_arr[$key];
						}
					}
				}
				echo $prodQTY;
				$neddToRevised.=$dtlsID."_".$prodQTY."#";
			}*/

			$order_uom=$row[csf('uom')];
			$del_date_arr[$ind++]=  $row[csf('delivery_date')] ;
			$wo_qnty=$row[csf('wo_qnty')];
			$data_break='';
			
			if($data[4]==2)
			{
				if($row[csf('fabric_description')]=="") $row[csf('fabric_description')]=0;
				if($row[csf('fabric_color')]=="") $row[csf('fabric_color')]=0;
				if($row[csf('item_size')]=="") $row[csf('item_size')]=0;
				if($row[csf('wo_qnty')]=="") $row[csf('wo_qnty')]=0;
				if($row[csf('rate')]=="") $row[csf('rate')]=0;
				if($row[csf('amount')]=="") $row[csf('amount')]=0;
				if($break_down_arr[$row[csf('id')]]=="") $break_down_arr[$row[csf('id')]]=0;
				//echo $data_break."++";
				if($data_break=="") $data_break.=$row[csf('fabric_description')].'_'.$color_library[$row[csf('fabric_color')]].'_'.$size_arr[$row[csf('item_size')]].'_'.$row[csf('wo_qnty')].'_'.$row[csf('rate')].'_'.$row[csf('amount')].'_'.$row[csf('id')].'_'.$break_down_arr[$row[csf('id')]].'_'.$row[csf('fabric_color')].'_'.$row[csf('item_size')];
				else $data_break.='***'.$row[csf('fabric_description')].'_'.$color_library[$row[csf('fabric_color')]].'_'.$size_arr[$row[csf('item_size')]].'_'.$row[csf('wo_qnty')].'_'.$row[csf('rate')].'_'.$row[csf('amount')].'_'.$row[csf('id')].'_'.$break_down_arr[$row[csf('id')]].'_'.$row[csf('fabric_color')].'_'.$row[csf('item_size')];
			}
			else
			{
				$buyerpo=$buyer_po_arr[$row[csf('po_break_down_id')]]['buyerpo'];
				$style=$buyer_po_arr[$row[csf('po_break_down_id')]]['style'];
				$buyer_buyer=$buyer_po_arr[$row[csf('po_break_down_id')]]['buyer_name'];
				$break_down_id=$row[csf('po_break_down_id')];
				$booking_dtls_id=$row[csf('booking_dtls_id')];
				$disable_dropdown='1';
				$disabled='disabled';
				$sql = "select  id, wo_trim_booking_dtls_id, job_no,  po_break_down_id,  item_color, item_size, requirment,description, brand_supplier, rate, amount from wo_trim_book_con_dtls where wo_trim_booking_dtls_id='$booking_dtls_id' and status_active=1 and is_deleted=0 and requirment!=0 order by id ASC"; //die;
				$breakData_arr=sql_select($sql);
				//$data_break="";
				$subBrkID='';
				foreach($breakData_arr as $rows)
				{//echo "1--";
					if($rows[csf('description')]=="") $rows[csf('description')]=0;
					if($rows[csf('item_color')]=="") $rows[csf('item_color')]=0;
					if($rows[csf('item_size')]=="") $rows[csf('item_size')]=0;
					if($rows[csf('requirment')]=="") $rows[csf('requirment')]=0;
					if($rows[csf('rate')]=="") $rows[csf('rate')]=0;
					if($rows[csf('amount')]=="") $rows[csf('amount')]=0;
					if($break_down_arr[$rows[csf('id')]]=="") $break_down_arr[$rows[csf('id')]]=0;
					$subBrkID=$subcon_brk_arr[$rows[csf('wo_trim_booking_dtls_id')]][$rows[csf('id')]]['subBrkID'];
					//echo $subBrkID."++";
					if($data_break=="") $data_break.=$rows[csf('description')].'_'.$color_library[$rows[csf('item_color')]].'_'.$rows[csf('item_size')].'_'.$rows[csf('requirment')].'_'.$rows[csf('rate')].'_'.$rows[csf('amount')].'_'.$rows[csf('id')].'_'.$subBrkID.'_'.$rows[csf('item_color')].'_'.$rows[csf('item_size')];
					else $data_break.='***'.$rows[csf('description')].'_'.$color_library[$rows[csf('item_color')]].'_'.$rows[csf('item_size')].'_'.$rows[csf('requirment')].'_'.$rows[csf('rate')].'_'.$rows[csf('amount')].'_'.$rows[csf('id')].'_'.$subBrkID.'_'.$rows[csf('item_color')].'_'.$rows[csf('item_size')];

					//$data_dreak_arr[$rows[csf('mst_id')]].=$rows[csf('description')].'_'.$color_library[$rows[csf('color_id')]].'_'.$size_arr[$rows[csf('size_id')]].'_'.$rows[csf('qnty')].'_'.$rows[csf('rate')].'_'.$rows[csf('amount')].'_'.$rows[csf('book_con_dtls_id')].'_'.$rows[csf('id')].'#';
				}
			}
			
			$domRate=$row[csf('rate')]*$exchange_rate; 
			$domAmount=$row[csf('amount')]*$exchange_rate;
			//$buyer_buyer='';
			if($data[4]==1)
			{
				$disabled='disabled';
				$disable_dropdown='1';
			}
			$isWithOrder=$data[4];

			?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
				<td><input id="txtbuyerPo_<? echo $tblRow; ?>" name="txtbuyerPo[]" value="<? echo $buyerpo; ?>" class="text_boxes" type="text"  style="width:100px" <? echo $disabled ?> />
					<input id="txtbuyerPoId_<? echo $tblRow; ?>" name="txtbuyerPoId[]" value="<? echo $break_down_id; ?>" class="text_boxes" type="hidden" style="width:70px" readonly />
				</td>
				<td><input id="txtstyleRef_<? echo $tblRow; ?>" name="txtstyleRef[]" value="<? echo $style; ?>" class="text_boxes" type="text"  style="width:100px" <? echo $disabled ?> /></td>
				<td>
					<? 
					if($isWithOrder==1)
					{
						echo create_drop_down( "txtbuyer_".$tblRow, 100, "select id, buyer_name from lib_buyer where status_active=1","id,buyer_name", 1, "-- Select --",$buyer_buyer, "",$disable_dropdown,'','','','','','',"txtbuyer[]");
					}
					else
					{
						?>
						<input id="txtbuyer_<? echo $tblRow; ?>" name="txtbuyer[]" value="<? echo $buyer_buyer; ?>" class="text_boxes" type="text"  style="width:87px"  <? echo $disabled ?>  />
						<?
					}

					if($row[csf('section')]==1) $subID='1,2,3';
					else if($row[csf('section')]==3) $subID='4,5';
					else if($row[csf('section')]==5) $subID='6,7,8,9,10,11,12,13';
					else if($row[csf('section')]==10) $subID='14,15';
					else $subID='0';
					?>
				</td>
				<td><? echo create_drop_down( "cboSection_".$tblRow, 90, $trims_section,"", 1, "-- Select Section --",$row[csf('section')],"load_sub_section($tblRow)",0,'','','','','','',"cboSection[]"); ?></td>			
				<td id="subSectionTd_<? echo $tblRow; ?>"><? echo create_drop_down( "cboSubSection_".$tblRow, 90, $trims_sub_section,"", 1, "-- Select Section --",$row[csf('sub_section')],"load_sub_section_value($tblRow)",0,$subID,'','','','','',"cboSubSection[]"); ?></td>			
				<td><? echo create_drop_down( "cboItemGroup_".$tblRow, 90, "select id, item_name from lib_item_group where item_category=4 and status_active=1","id,item_name", 1, "-- Select --",$row[csf('trim_group')], "",$disable_dropdown,'','','','','','',"cboItemGroup[]"); ?></td>
				<td><? echo create_drop_down( "cboUom_".$tblRow, 60, $unit_of_measurement,"", 1, "-- Select --",$order_uom,"", 1,'','','','','','',"cboUom[]"); ?>	</td>
				<td><input id="txtOrderQuantity_<? echo $tblRow; ?>" name="txtOrderQuantity[]" value="<? echo number_format($wo_qnty,4,'.',''); ?>" class="text_boxes_numeric" type="text"  style="width:60px" onClick="openmypage_order_qnty(<? echo $tblRow; ?>)" placeholder="Click To Search" readonly /></td>
				<td><? echo create_drop_down( "cboBookUom_".$tblRow, 60, $unit_of_measurement,"", 1, "-- Select --",$row[csf('booked_uom')],1, 1,'','','','','','',"cboBookUom[]"); ?>	</td>
				<?
				if($row[csf('booked_uom')]==$order_uom)
				{
					$disabled_conv="disabled";
				}
				?>
				<td><input id="txtConvFactor_<? echo $tblRow; ?>" name="txtConvFactor[]" type="text"  class="text_boxes_numeric" value="<? echo $row[csf('booked_conv_fac')]; ?>"  onkeyup="cal_booked_qty(<? echo $tblRow; ?>);" style="width:47px"  <? echo $disabled_conv; ?>  /></td>
				<td><input id="txtBookQty_<? echo $tblRow; ?>" name="txtBookQty[]" type="text"  class="text_boxes_numeric" style="width:57px"  value="<? echo number_format($row[csf('booked_qty')],4,'.',''); ?>" readonly="readonly" /></td>
				<td><input id="txtRate_<? echo $tblRow; ?>" name="txtRate[]" value="<? echo number_format($row[csf('rate')],4,'.',''); ?>" type="text"  class="text_boxes_numeric" style="width:60px" readonly/></td>
				<td><input id="txtAmount_<? echo $tblRow; ?>" name="txtAmount[]"  value="<? echo number_format($row[csf('amount')],4,'.',''); ?>" type="text" style="width:70px"  class="text_boxes_numeric"  disabled /></td>
				<td><input id="txtDomRate_<? echo $tblRow; ?>" name="txtDomRate[]" value="<? echo number_format($domRate,4,'.',''); ?>" type="text"  class="text_boxes_numeric" style="width:57px" readonly /></td>
				<td><input id="txtDomamount_<? echo $tblRow; ?>" name="txtDomamount[]" value="<? echo number_format($domAmount,4,'.',''); ?>" type="text"  class="text_boxes_numeric" style="width:77px" readonly /></td>
				<td><input type="text"  id="txtOrderDeliveryDate_<? echo $tblRow; ?>" name="txtOrderDeliveryDate[]" value="<? echo change_date_format($row[csf("delivery_date")]);?>" class="datepicker" onChange="chk_min_del_date(<? echo $tblRow; ?>)" style="width:67px"  />
					<input id="hdnDtlsUpdateId_<? echo $tblRow; ?>" name="hdnDtlsUpdateId[]" type="hidden" value="<? echo $dtlsID; ?>" class="text_boxes_numeric" style="width:40px" />
					<input type="hidden" id="hdnDtlsdata_<? echo $tblRow; ?>" name="hdnDtlsdata[]" value="<? echo $data_break; ?>">
	                <input type="hidden" id="hdnbookingDtlsId_<? echo $tblRow; ?>" name="hdnbookingDtlsId[]" value="<? echo $row[csf('booking_dtls_id')]; ?>">
	                <input type="hidden" id="txtDeletedId_<? echo $tblRow; ?>" name="txtDeletedId[]" value="">
	                <input type="hidden" id="txtIsWithOrder_<? echo $tblRow; ?>" name="txtIsWithOrder[]" value="<? echo $isWithOrder; ?>">
	            </td>
                <td width="65">
					<input type="button" id="increase_<? echo $tblRow; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fnc_addRow(
					<? echo $tblRow.","."'tbl_dtls_emb'".","."'row_'" ;?>)" />
					<input type="button" id="decrease_<? echo $tblRow; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fnc_deleteRow(<?echo $tblRow.","."'tbl_dtls_emb'".","."'row_'" ;?>);" />
				</td>
			</tr>
			<?
		}
	}
	else
	{
		?>		
		<tr id="row_1">
            <td><input id="txtbuyerPo_1" name="txtbuyerPo[]" name="text" class="text_boxes" style="width:100px" placeholder="Display"/>
            	<input id="txtbuyerPoId_1" name="txtbuyerPoId[]" type="hidden" class="text_boxes" style="width:70px"readonly />
            </td>
            <td><input id="txtstyleRef_1" name="txtstyleRef[]" type="text" class="text_boxes" style="width:100px" placeholder="Display"/></td>
             <td><input id="txtbuyer_1" name="txtbuyer[]" type="text" class="text_boxes" style="width:100px" placeholder="Display" /></td>
            <td><? echo create_drop_down( "cboSection_1", 90, $trims_section,"id,section_name", 1, "-- Select Section --","","load_sub_section($tblRow)",0,'','','','','','',"cboSection[]"); ?></td>
            <td id="subSectionTd_1"><? echo create_drop_down( "cboSubSection_1", 90, $trims_sub_section,"id,section_name", 1, "-- Select Sub Section --","",'',0,'','','','','','',"cboSubSection[]"); ?></td>
            <td><? echo create_drop_down( "cboItemGroup_1", 90, "select id, item_name from lib_item_group where item_category=4 and  status_active=1","id,item_name", 1, "-- Select --",$selected, "",0,'','','','','','',"cboItemGroup[]"); ?>	</td>
            <td><? echo create_drop_down( "cboUom_1", 60, $unit_of_measurement,"", 1, "-- Select --",2,1, 1,'','','','','','',"cboUom[]"); ?>	</td>
            <td><input id="txtOrderQuantity_1" name="txtOrderQuantity[]" class="text_boxes_numeric" type="text"  style="width:60px" onClick="openmypage_order_qnty(1,'0',1)" placeholder="Click To Search" readonly /></td>
            <td><input id="txtRate_1" name="txtRate[]" type="text"  class="text_boxes_numeric" style="width:60px" readonly="readonly" /></td>
            <td><input id="txtAmount_1" name="txtAmount[]" type="text" style="width:70px"  class="text_boxes_numeric" readonly /></td> 
            <td><input id="txtDomRate_1" name="txtDomRate[]" type="text"  class="text_boxes_numeric" style="width:57px" readonly="readonly" /></td> 
            <td><input id="txtDomamount_1" name="txtDomamount[]" type="text"  class="text_boxes_numeric" style="width:77px" readonly="readonly"  /></td> 
            <td><input type="text" name="txtOrderDeliveryDate[]" id="txtOrderDeliveryDate_1" class="datepicker"  onChange="chk_min_del_date(1)"  style="width:67px" />
            	<input type="hidden" name="hdnDtlsUpdateId[]" id="hdnDtlsUpdateId_1">
                <input type="hidden" name="hdnDtlsdata[]" id="hdnDtlsdata_1">
                <input type="hidden" name="hdnbookingDtlsId[]" id="hdnbookingDtlsId_1">
                <input type="hidden" name="txtDeletedId[]" id="txtDeletedId_1">
            </td>
            <td width="65">
				<input type="button" id="increase_1" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fnc_addRow(1,'tbl_dtls_emb','row_')" />
				<input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fnc_deleteRow(1,'tbl_dtls_emb','row_');" />
			</td>
        </tr> 
		<?
	}
	$min_dates=$del_date_arr[0];
	foreach($del_date_arr as $v) 
	{
		if(strtotime($min_dates)>strtotime($v))$min_dates=$v;

	}
	 
	?>
	<input type="hidden" id="min_date_id" name="min_date_id" value="<? echo change_date_format($min_dates);?>">

	<?

	exit();
}


/*if($action=="check_minimum_delivery_date")
{
	$data=explode("_",$data);
	$receive_date=strtotime(str_replace("'",'',$txt_order_receive_date));
	$delivery_date=strtotime(str_replace("'",'',$txt_delivery_date));
	$current_date=strtotime(date("d-m-Y"));
	if($receive_date>$delivery_date)
	{
		echo "26**"; die;
	}
	else if($receive_date != $current_date)
	{
		echo "25**"; die;
	}
	echo $uom;
	exit();	
}*/	
?>

