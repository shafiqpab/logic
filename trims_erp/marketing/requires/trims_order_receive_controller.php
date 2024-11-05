<?
include('../../../includes/common.php');
session_start();

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

if ($action=="populate_data_from_image")
{
	$image_data=sql_select("select master_tble_id from common_photo_library where master_tble_id='$data' and form_name='trims_order_receive' and file_type=2 and is_deleted=0");
	echo "document.getElementById('image_location_id').value = '".$image_data[0][csf("master_tble_id")]."';\n";
	echo "document.getElementById('txt_is_file_uploaded').value = 1;\n";
}

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




if($action=="company_wise_report_button_setting")
{
	extract($_REQUEST);

	$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=17 and report_id=173 and is_deleted=0 and status_active=1");
	
	//echo $print_report_format; disconnect($con); die;
	
	$print_report_format_arr=explode(",",$print_report_format);
	//print_r($print_report_format_arr);
	echo "$('#Print').hide();\n";
	echo "$('#btn_print2').hide();\n";
	echo "$('#btn_print3').hide();\n";
	echo "$('#btn_print4').hide();\n";
	
	
	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			
			if($id==86){echo "$('#Print').show();\n";}
			if($id==84){echo "$('#btn_print2').show();\n";}
			if($id==377){echo "$('#btn_print3').show();\n";}
			if($id==160){echo "$('#btn_print4').show();\n";}
			
		}
	}
	exit();	
}




/*if ($action=="load_drop_down_location")
{
	$data=explode("_",$data);
	if($data[1]==1) $dropdown_name="cbo_location_name";
	else $dropdown_name="cbo_party_location";
	
	echo create_drop_down( $dropdown_name, 150, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );	
	exit();
}
*/
if ($action=="load_drop_down_member")
{
	$data=explode("_",$data);
	$sql="select b.id,b.team_member_name  from lib_marketing_team a, lib_mkt_team_member_info b where a.id=b.team_id and   a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.project_type=3 and team_id='$data[0]'";
	echo create_drop_down( "cbo_team_member", 150, $sql,"id,team_member_name", 1, "-- Select Member --", $selected, "" );		
	exit();
}

/*if ($action=="load_drop_down_marchan")
{
	$data=explode("_",$data);
	$sql="select b.id,b.team_member_name  from lib_marketing_team a, lib_mkt_team_member_info b where a.id=b.team_id and   a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.project_type=3 and team_id='$data[0]'";
	echo create_drop_down( "cbo_fac_merchan", 150, $sql,"id,team_member_name", 1, "-- Select Marchan --", $selected, "" );		
	exit();
}*/



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
	if($data[0]==1) $subID='1,2,3,23,29,30,31,32,33,34,35,36,37,38,39,40';
	else if($data[0]==3) $subID='4,5,18';
	else if($data[0]==5) $subID='6,7,8,9,10,11,12,13,16,17,17,24';
	else if($data[0]==10) $subID='14,15';
	else if($data[0]==7) $subID='19,20,21,25,26,27,28,41';
	else if($data[0]==9) $subID='22';
	else $subID='0';
	//echo $data[0]."**".$subID;
	//echo create_drop_down( "cboembtype_".$data[1], 80,$emb_type,"", 1, "-- Select --", "", "","","" ); 
	echo create_drop_down( "cboSubSection_".$data[1], 90, $trims_sub_section,"",1, "-- Select Sub-Section --","","load_sub_section_value($data[1])",0,$subID,'','','','','',"cboSubSection[]");
	exit();
}

if($action=="load_drop_down_itemgroup")
{
	$data=explode('_',$data); 
	 echo create_drop_down( "cboItemGroup_".$data[1], 90, "select id, item_name from lib_item_group where item_category=4 and section='$data[0]' and status_active=1","id,item_name", 1, "-- Select --",$selected, "load_uom($data[1])",'','','','','','','',"cboItemGroup[]"); 
	   
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
	$cbo_company_id = str_replace("'","",$cbo_company_name);
	/*echo '<pre>';
	print_r($cbo_company_name);die;*/
	$user_id=$_SESSION['logic_erp']['user_id'];
	//$str_rep=array("/", "&", "*", "(", ")", "=","'",",","\r", "\n",'"','#','_','€','$','৳','~','?',':',';','-',':-',"'\'");
	$str_rep=array( "&", "*", "(", ")", "=","'","\r", "\n",'"','#','_','€','$','৳','~','?',':',';',':-');
	
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
			echo "26**"; disconnect($con); die;
		}
		else if($receive_date != $current_date && $_SESSION['logic_erp']['data_arr'][255][$cbo_company_id]['txt_order_receive_date']['is_disable'] ==1)
		{
			echo "25**"; disconnect($con); die;
		}

		
		if($db_type==0) $insert_date_con="and YEAR(insert_date)=".date('Y',time())."";
		else if($db_type==2) $insert_date_con="and TO_CHAR(insert_date,'YYYY')=".date('Y',time())."";
		
		$new_job_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'TOR', date("Y",time()), 5, "select job_no_prefix,job_no_prefix_num from subcon_ord_mst where entry_form=255 and company_id=$cbo_company_name $insert_date_con order by id desc ", "job_no_prefix", "job_no_prefix_num" ));
		//$new_job_no = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "subcon_ord_mst",$con,1,$cbo_company_name,'TOR',1,date("Y",time()),1 ));
        $getVeriableSettingData = sql_select("select work_order_number_control as WORK_ORDER_NUMBER_CONTROL from variable_setting_trim_marketing where company_name=$cbo_company_name and variable_list=1 order by id asc");
        $wo_order_system_fill_up = 1;
        if(count($getVeriableSettingData) > 0){
            $wo_order_system_fill_up = $getVeriableSettingData[0]['WORK_ORDER_NUMBER_CONTROL'];
        }
        if($wo_order_system_fill_up == 2){
            if(str_replace("'",'',$txt_order_no)=="" && str_replace("'","",$cbo_within_group) == 2){
                $txt_order_no="";
            }else{
                $txt_order_no=str_replace("'",'',$txt_order_no);
            }
        }else {
            if (str_replace("'", '', $txt_order_no) == "" && str_replace("'","",$cbo_within_group) == 2) {
                $txt_order_no = $new_job_no[0];
            } else {
                $txt_order_no = str_replace("'", '', $txt_order_no);
            }
        }

		if (is_duplicate_field( "order_no", "subcon_ord_mst", "order_no='$txt_order_no' and company_id=$cbo_company_name and status_active=1 and is_deleted=0" ) == 1)
		{
			echo "37**0"; disconnect($con); die;
		}
		else
		{
			//echo "10**select order_no from subcon_ord_mst where order_no='$txt_order_no' and company_id=$cbo_company_name and status_active=1 and is_deleted=0 and id !=$update_id"; die;
			if($db_type==0){
				$txt_delivery_date=change_date_format(str_replace("'",'',$txt_delivery_date),'yyyy-mm-dd');
				$txt_rec_start_date=change_date_format(str_replace("'",'',$txt_rec_start_date),'yyyy-mm-dd');
				$txt_rec_end_date=change_date_format(str_replace("'",'',$txt_rec_end_date),'yyyy-mm-dd');
				$txt_order_receive_date=change_date_format(str_replace("'",'',$txt_order_receive_date),'yyyy-mm-dd');
			}else{
				$txt_delivery_date=change_date_format(str_replace("'",'',$txt_delivery_date), "", "",1);
				$txt_rec_start_date=change_date_format(str_replace("'",'',$txt_rec_start_date), "", "",1);
				$txt_rec_end_date=change_date_format(str_replace("'",'',$txt_rec_end_date), "", "",1);
				$txt_order_receive_date=change_date_format(str_replace("'",'',$txt_order_receive_date), "", "",1);
			}
			$id=return_next_id("id","subcon_ord_mst",1);
			$id1=return_next_id( "id", "subcon_ord_dtls",1);
			$id3=return_next_id( "id", "subcon_ord_breakdown", 1 );
            $id4=return_next_id( "id", "SUBCON_ORD_BREAKDOWN_SIZE_INFO", 1 );
			$rID3=true;
			$field_array="id, entry_form, subcon_job, job_no_prefix, job_no_prefix_num, company_id, location_id, within_group, party_id, party_location, currency_id, receive_date, delivery_date, rec_start_date, rec_end_date, order_id, order_no, exchange_rate,team_leader,team_member, team_marchant,buying_merchant, ready_to_approved, payterm_id,wo_type,party_wise_grade, remarks, trims_ref, delivery_point, inserted_by, insert_date,status,buyer_tb";
			$data_array="(".$id.", 255, '".$new_job_no[0]."', '".$new_job_no[1]."', '".$new_job_no[2]."', '".$cbo_company_name."', '".$cbo_location_name."', '".$cbo_within_group."', '".$cbo_party_name."', '".$cbo_party_location."', '".$cbo_currency."', '".$txt_order_receive_date."', '".$txt_delivery_date."','".$txt_rec_start_date."','".$txt_rec_end_date."', '".$hid_order_id."', '".$txt_order_no."', '".$txt_exchange_rate."', '".$cbo_team_leader."', '".$cbo_team_member."', '".$txt_fac_merchan."', '".$txt_buying_merchant."', '".$cbo_ready_to_approved."', '".$cbo_payterm_id."', '".$cbo_wo_type_id."','".$cbo_party_wise_grade."', '".$txt_remarks."', '".$txt_trims_ref."', '".$txt_delivery_point."', ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."', '".$cbo_Status_type."', '".$txt_buyer_tb."')";
			
			$txt_job_no=$new_job_no[0];
			
			$field_array2="id, mst_id, job_no_mst, order_id, order_no, buyer_po_id, booking_dtls_id, order_quantity, order_uom, rate, amount, submit_date, approve_date, delivery_date, buyer_po_no, buyer_style_ref, buyer_buyer, section, sub_section, item_group, rate_domestic,  amount_domestic, is_with_order, booked_uom, booked_conv_fac, booked_qty, source_for_order, inserted_by, insert_date, plan_cut";
			$field_array3="id, mst_id, order_id, job_no_mst, book_con_dtls_id, description, color_id, size_id, qnty, rate, amount, booked_qty, style, inserted_by, insert_date, excess_cut, plan_cut, plan_cut_amount, ply, size_name, gmts_color_id, gmts_size_id";
			$field_array4="id, subconordbreakdownid, length, width, height, flap, gusset, thickness, measurementid";
			$color_library_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
			$size_library_arr=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
			$data_array2 	= $data_array3=""; $data_arra4="";  $add_commaa=0; $add_commadtls=0; $add_commadtls1=0; $new_array_color=array();  $new_array_size=array();
			$cbo_within_group=str_replace("'","",$cbo_within_group);
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
				$txtSamSubDate 			= "txtSamSubDate_".$i;
				$txtSamApprDate 		= "txtSamApprDate_".$i;
				$txtOrderDeliveryDate 	= "txtOrderDeliveryDate_".$i;
				$txtDomRate 			= "txtDomRate_".$i;
				$txtDomamount 			= "txtDomamount_".$i;
				$hdnDtlsdata 			= "hdnDtlsdata_".$i;
                $sizedtlsdata 			= "sizedtlsdata_".$i;
				$hdnDtlsUpdateId 		= "hdnDtlsUpdateId_".$i;
				$hdnbookingDtlsId 		= "hdnbookingDtlsId_".$i;
				$txtIsWithOrder 		= "txtIsWithOrder_".$i;
				$txtIsDuplicate 		= "txtIsDuplicate_".$i;
				$cboBookUom 			= "cboBookUom_".$i;
				$txtConvFactor 			= "txtConvFactor_".$i;
				$txtBookQty 			= "txtBookQty_".$i;
				$cboSource 				= "cboSource_".$i;
				$txtPlaneCut 			= "txtPlaneCut_".$i;

				$orddelivery_date=strtotime(str_replace("'",'',$$txtOrderDeliveryDate));
				if($receive_date>$orddelivery_date)
				{
					echo "26**"; disconnect($con); die;
				}
				if($db_type==0)
				{
					$orderDeliveryDate=change_date_format(str_replace("'",'',$$txtOrderDeliveryDate),'yyyy-mm-dd');
				}
				else
				{
					$orderDeliveryDate=change_date_format(str_replace("'",'',$$txtOrderDeliveryDate), "", "",1);
				}

				$txtSamSubDate=change_date_format(str_replace("'",'',$$txtSamSubDate), "", "",1);
				$txtSamApprDate=change_date_format(str_replace("'",'',$$txtSamApprDate), "", "",1);

				if(str_replace("'",'',$$txtbuyerPoId)=="") $txtbuyerPoId=0; else $txtbuyerPoId=str_replace("'",'',$$txtbuyerPoId);
				if ($add_commaa!=0) $data_array2 .=","; $add_comma=0;

				$txtbuyerPo=str_replace($str_rep,' ',$$txtbuyerPo);
				$txtstyleRef=str_replace($str_rep,' ',$$txtstyleRef);
				$txtIsDuplicate=str_replace("'","",$$txtIsDuplicate);

				$data_array2 .="(".$id1.",".$id.",'".$new_job_no[0]."','".$hid_order_id."','".$txt_order_no."','".$txtbuyerPoId."',".$$hdnbookingDtlsId.",".str_replace(",",'',$$txtOrderQuantity).",".$$cboUom.",".$$txtRate.",".str_replace(",",'',$$txtAmount).",'".$txtSamSubDate."','".$txtSamApprDate."','".$orderDeliveryDate."','".trim($txtbuyerPo)."','".trim($txtstyleRef)."',".$$txtbuyer.",".$$cboSection.",".$$cboSubSection.",".$$cboItemGroup.",".str_replace(",",'',$$txtDomRate).",".str_replace(",",'',$$txtDomamount).",".str_replace(",",'',$$txtIsWithOrder).",".$$cboBookUom.",".str_replace(",",'',$$txtConvFactor).",".str_replace(",",'',$$txtBookQty).",".$$cboSource.",'".$user_id."','".$pc_date_time."',".$$txtPlaneCut.")";
				
				$dtls_data=explode("***",str_replace("'",'',$$hdnDtlsdata));
				$dtls_data_size=explode("***",str_replace("'",'',$$sizedtlsdata));
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
					$style="'".$exdata[10]."'";
					$excess_cut="'".$exdata[11]."'";
					$plan_cut="'".$exdata[12]."'";
					$booked_qty=str_replace(",",'',$exdata[12])*str_replace("'",'',$$txtConvFactor);
					$plan_cut_amount="'".$exdata[13]."'";
					$ply="'".$exdata[14]."'";
					$txtgmtscolor="'".$exdata[15]."'";
					$txtgmtssize="'".$exdata[16]."'";
                    $exdata_size = [];
                    if($dtls_data_size[$j] != "") {
                        $exdata_size = explode("_", $dtls_data_size[$j]);
                    }
					$description=str_replace($str_rep,' ',$description);

					if (str_replace("'", "", trim($colorname)) != "") {
						if (!in_array(str_replace("'", "", trim($colorname)),$new_array_color)){
							$color_id = return_id( str_replace("'", "", trim($colorname)), $color_library_arr, "lib_color", "id,color_name","255");
							$new_array_color[$color_id]=str_replace("'", "", trim($colorname));
						}
						else $color_id =  array_search(str_replace("'", "", trim($colorname)), $new_array_color);
					}else $color_id = 0;

					
					if(str_replace("'","",$sizename)!="" && count($exdata_size) == 0)
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

					if (str_replace("'", "", trim($txtgmtscolor)) != "") {
						if (!in_array(str_replace("'", "", trim($txtgmtscolor)),$new_array_color2)){
							$gmts_color_id = return_id( str_replace("'", "", trim($txtgmtscolor)), $color_library_arr, "lib_color", "id,color_name","255");
							$new_array_color2[$gmts_color_id]=str_replace("'", "", trim($txtgmtscolor));
						}
						else $gmts_color_id =  array_search(str_replace("'", "", trim($txtgmtscolor)), $new_array_color2);
					}else $gmts_color_id = 0;

					if(str_replace("'","",$txtgmtssize)!="" )
					{
						if (!in_array(str_replace("'","",$txtgmtssize),$new_array_size2))
						{
							$gmts_size_id = return_id( str_replace("'","",$txtgmtssize), $size_library_arr, "lib_size", "id,size_name","255");
							//echo $$txtColorName.'='.$color_id.'<br>';
							$new_array_size2[$gmts_size_id]=str_replace("'","",$txtgmtssize);
						}
						else $gmts_size_id =  array_search(str_replace("'","",$txtgmtssize), $new_array_size2); 
					}
					else $gmts_size_id=0;
					
					if ($add_commadtls!=0) $data_array3 .=",";
					$data_array3.="(".$id3.",".$id1.",'".$hid_order_id."','".$new_job_no[0]."',".$book_con_dtls_id.",'".trim($description)."','".$color_id."','".$size_id."',".$qty.",".$rate.",".$amount.",".$booked_qty.",".$style.",'".$user_id."','".$pc_date_time."',".$excess_cut.",".$plan_cut.",".$plan_cut_amount.",".$ply.",".$sizename.",".$gmts_color_id.",".$gmts_size_id.")";

                    if(count($exdata_size) > 0) {
                        $length = $exdata_size[1] != '' ? $exdata_size[1] : 0;
                        $width = $exdata_size[2] != '' ? $exdata_size[2] : 0;
                        $height = $exdata_size[3] != '' ? $exdata_size[3] : 0;
                        $flap = $exdata_size[4] != '' ? $exdata_size[4] : 0;
                        $gusset = $exdata_size[5] != '' ? $exdata_size[5] : 0;
                        $thickness = $exdata_size[6];
                        $meagurement_id = $exdata_size[7];
                        if ($add_commadtls1 != 0) $data_array4 .= ",";
                        $data_array4 .= "(" . $id4 . "," . $id3 . "," . $length . "," . $width . "," . $height . "," . $flap . "," . $gusset . ",'" . $thickness . "'," . $meagurement_id . ")";
                    }
                    $id3=$id3+1; $add_commadtls++;
                    $id4=$id4+1; $add_commadtls1++;
				}
				$id1=$id1+1; $add_commaa++;
		//				echo "10**INSERT INTO subcon_ord_breakdown (".$field_array3.") VALUES ".$data_array3;
		//                echo "<br>";
		//				echo "10**INSERT INTO SUBCON_ORD_BREAKDOWN_SIZE_INFO (".$field_array4.") VALUES ".$data_array4;
		//                echo "<br>";

					}
		//            die();


			$flag=1;
			$rID=$rID2=$rID3=$rID4=$rIDBooking=true;
			//echo "10**insert into subcon_ord_mst ($field_array) values $data_array";die;
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
            if($data_array4!="" && $flag==1)
            {
                $rID4=sql_insert("SUBCON_ORD_BREAKDOWN_SIZE_INFO",$field_array4,$data_array4,1);
                if($rID4==1) $flag=1; else $flag=0;
            }

			if(str_replace("'","",$cbo_within_group)==1)
			{
				if($flag==1)
				{
					$rIDBooking=execute_query( "update wo_booking_mst set lock_another_process=1 where booking_no ='".$txt_order_no."'",1);
					if($rIDBooking==1) $flag=1; else $flag=0;
				}
			}
			
			//echo "10**$rID=$rID2=$rID3=$rID4=$rIDBooking=";die;
		
			if($db_type==0)
			{
                if (str_replace("'", '', $txt_order_no) == "" && str_replace("'","",$cbo_within_group) == 2) {
                    $txt_order_no = $new_job_no[0];
                }
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
		
		
		
		/////////////////////////////////////////// variable anujoy
		$cbo_company_name=str_replace("'",'',$cbo_company_name);
		$sql = "select id, work_order_number_control from variable_setting_trim_marketing where company_name=$cbo_company_name and status_active=1 and is_deleted=0 and variable_list=3"; 
		$sectionvariable = sql_select($sql);
		$variable_bill_prod_delv=$sectionvariable[0][csf('work_order_number_control')];
 		$bil_qty_arr=array(); 
		
		// echo "10**"."select c.mst_id as job_dtls_id,b.delevery_qty  from  trims_delivery_dtls b ,subcon_ord_breakdown c   where  c.id=b.break_down_details_id and   b.received_id=$update_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0"; die; 
		
		if($variable_bill_prod_delv==1)
		{
				$bill_po_sql = sql_select("select c.mst_id as job_dtls_id,a.quantity as bil_quantity,b.delevery_qty  from trims_bill_dtls a,trims_delivery_dtls b ,subcon_ord_breakdown c   where a.production_dtls_id=b.id and c.id=b.break_down_details_id and   b.received_id=$update_id   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
			foreach($bill_po_sql as $row)
			{
				$bil_qty_arr[$row[csf('job_dtls_id')]]['bil_quantity']+=$row[csf('bil_quantity')];
 			}
		}
		
		
		
		if($variable_bill_prod_delv==2)
		{
			
			//echo "10**"."select c.mst_id as job_dtls_id,b.delevery_qty,a.production_qty  from  trims_production_dtls a,trims_delivery_dtls b ,subcon_ord_breakdown c   where  a.id=b.production_dtls_id and c.id=b.break_down_details_id and   b.received_id=$update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0"; die;
			
				$bill_po_sql = sql_select("select c.mst_id as job_dtls_id,b.delevery_qty,a.production_qty  from  trims_production_dtls a,trims_delivery_dtls b ,subcon_ord_breakdown c   where  a.id=b.production_dtls_id and c.id=b.break_down_details_id and   b.received_id=$update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
			foreach($bill_po_sql as $row)
			{
 				$bil_qty_arr[$row[csf('job_dtls_id')]]['production_qty']+=$row[csf('production_qty')];
			}
		}
		
		if($variable_bill_prod_delv==3)
		{
				$bill_po_sql = sql_select("select c.mst_id as job_dtls_id,b.delevery_qty  from  trims_delivery_dtls b ,subcon_ord_breakdown c   where  c.id=b.break_down_details_id and   b.received_id=$update_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
			foreach($bill_po_sql as $row)
			{
 				$bil_qty_arr[$row[csf('job_dtls_id')]]['delevery_qty']+=$row[csf('delevery_qty')];
			}
		}
		
		///////////////////////////////////////////	
		

		

		$color_library_arr=return_library_array( "select id,color_name from lib_color  where status_active=1 and is_deleted=0", "id", "color_name"  );
		$size_library_arr=return_library_array( "select id,size_name from lib_size  where status_active=1 and is_deleted=0", "id", "size_name"  );

		if (is_duplicate_field( "order_no", "subcon_ord_mst", "order_no='$txt_order_no' and company_id=$cbo_company_name and status_active=1 and is_deleted=0 and id !=$update_id" ) == 1)
		{
			echo "11**0"; disconnect($con); die;
		}
		else
		{
			
			if($variable_bill_prod_delv==1 || $variable_bill_prod_delv==2 || $variable_bill_prod_delv==3)
			{
				$chk_next_process=1;
			}
			else
			{

				if( str_replace("'","",$cbo_Status_type)==3 ){
					$delevery_id=return_field_value( "RECEIVED_ID", "trims_delivery_mst","received_id='$update_id' and status_active=1 and is_deleted=0");
					if($delevery_id!="")
					{   
						echo "11**"; disconnect($con); die;
					}
				}
			}
			//echo "10**select order_no from subcon_ord_mst where order_no=$txt_order_no and company_id=$cbo_company_name and status_active=1 and is_deleted=0 and id !=$update_id"; die;
			$receive_date=strtotime(str_replace("'",'',$txt_order_receive_date));
			$delivery_date=strtotime(str_replace("'",'',$txt_delivery_date));
			//$current_date=strtotime(date("d-m-Y"));
			//echo "10**".$receive_date."**".$delivery_date; die;
			if($receive_date>$delivery_date)
			{
				echo "26**"; disconnect($con); die;
			}
			
			
			$next_process=return_field_value( "trims_job", "trims_job_card_mst"," entry_form=257 and received_id=$update_id and status_active=1 and is_deleted=0");
			$next_process_bill=return_field_value( "ORDER_NO", "TRIMS_BILL_dtls","ORDER_NO='$txt_order_no' and status_active=1 and is_deleted=0");
			//echo "10**select ORDER_NO from TRIMS_BILL_MST where ORDER_NO='$txt_order_no' and status_active=1 and is_deleted=0 ".$next_process_bill; die;
			$chk_next_process=0; $flag=1; 

			if($next_process_bill!='' && $next_process!='')
			{
 					if($variable_bill_prod_delv==1 || $variable_bill_prod_delv==2 || $variable_bill_prod_delv==3)
					{
						$chk_next_process=1;
					}
					else
					{
						//echo "20**".str_replace("'","",$txt_job_no)."**".$rec_number;
						echo "20**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no)."**".$next_process_bill;
						disconnect($con);
						die;
					}
			}
			else if($next_process_bill=='' && $next_process!='' )
			{
				//echo "20**".str_replace("'","",$txt_job_no)."**".$rec_number;
				$chk_next_process=1;
			}
			
		 // echo "10**".$chk_next_process."eeee"; die;
			if($chk_next_process==0)
			{
				$field_array="location_id*within_group*party_id*party_location*currency_id*receive_date*delivery_date*rec_start_date*rec_end_date*order_id*order_no*exchange_rate*team_leader*team_member*team_marchant*buying_merchant*delivery_point*ready_to_approved*remarks*payterm_id*wo_type*party_wise_grade*trims_ref*is_apply_last_update*revise_no*status*buyer_tb*updated_by*update_date";  
				$field_array2="order_id*order_no*buyer_po_id*booking_dtls_id*order_quantity*order_uom*rate*amount*submit_date*approve_date*delivery_date*buyer_po_no*buyer_style_ref*buyer_buyer*section*sub_section*item_group*rate_domestic*amount_domestic*booked_uom*booked_conv_fac*booked_qty*source_for_order*updated_by*update_date*plan_cut";
				
				$field_array3="id, mst_id, order_id, job_no_mst, book_con_dtls_id, description, color_id, size_id, qnty, rate, amount,booked_qty,style, inserted_by, insert_date, excess_cut, plan_cut,  plan_cut_amount, ply, size_name, gmts_color_id, gmts_size_id";
				$field_array4="order_id*book_con_dtls_id*description*color_id*size_id*qnty*rate*amount*booked_qty*style*updated_by*update_date*excess_cut*plan_cut*plan_cut_amount*ply*size_name*gmts_color_id*gmts_size_id";
				$field_array5="id, mst_id, job_no_mst, order_id, order_no, buyer_po_id, booking_dtls_id, order_quantity, order_uom, rate, amount, submit_date, approve_date, delivery_date, buyer_po_no, buyer_style_ref, buyer_buyer, section, sub_section, item_group, rate_domestic,  amount_domestic , booked_uom, booked_conv_fac, booked_qty,is_with_order,source_for_order, inserted_by, insert_date ,plan_cut";
                $field_array6="id, subconordbreakdownid, length, width, height, flap, gusset, thickness, measurementid";
                $field_array7="length*width*height*flap*gusset*thickness*measurementid";

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
					$delv_sql= "SELECT a.id,a.mst_id,a.job_no_mst,a.order_id,a.order_no,a.buyer_po_id,a.booking_dtls_id,b.qnty AS order_quantity,a.order_uom,a.booked_uom,a.booked_conv_fac,b.rate,a.amount,a.delivery_date,a.buyer_po_no,a.buyer_style_ref,a.buyer_buyer,a.section,a.item_group AS trim_group,a.rate_domestic,a.amount_domestic,b.item_id,b.color_id,b.size_id,b.description,b.id AS break_id,b.book_con_dtls_id,c.delevery_qty,c.claim_qty,c.id AS delDtlsId, c.receive_dtls_id,c.break_down_details_id,c.size_name,c.color_name,c.remarks FROM subcon_ord_dtls a ,subcon_ord_breakdown b LEFT JOIN trims_delivery_dtls c ON c.break_down_details_id = b.id WHERE a.id = b.mst_id and a.mst_id=$update_id and b.status_active=1 and b.is_deleted=0 ";
					$delRectlSID_arr=array(); $delBrktlSID_arr=array(); $rectlSID_arr=array(); $delBrkQty_arr=array();
					$delv_sql_res = sql_select($delv_sql);
					foreach($delv_sql_res as $rows)
					{
						$rectlSID_arr[$rows[csf('id')]]=$rows[csf('id')];
						$delRectlSID_arr[$rows[csf('receive_dtls_id')]]=$rows[csf('receive_dtls_id')];
						$delBrktlSID_arr[$rows[csf('receive_dtls_id')]]['break_id'] .=$rows[csf('break_id')].",";
						$delBrktlSID_arr[$rows[csf('receive_dtls_id')]]['breakDelv_id'] .=$rows[csf('break_down_details_id')].",";
						$delBrktlSID_arr[$rows[csf('receive_dtls_id')]]['delevery_qty']+=$rows[csf('delevery_qty')];
						$delBrkQty_arr[$rows[csf('break_id')]]['delevery_qty']+=$rows[csf('delevery_qty')];
					}

					$subcon_arr=array(); $subcon_brk_arr=array(); $dtlSID_arr=array(); $breakIDarr=array();
					$subcon_sql ="select a.id, a.subcon_job, a.company_id, a.location_id, a.party_id, a.receive_date, b.order_no,  b.id as subDtlsID,b.booking_dtls_id, b.order_id, b.booked_qty, b.booked_conv_fac, b.order_quantity , b.rate , b.amount , b.rate_domestic, b.amount_domestic,b.buyer_po_id,b.order_uom,b.buyer_po_no,b.buyer_style_ref,b.buyer_buyer,b.section,b.sub_section,b.item_group, b.booked_uom,b.delivery_date, a.exchange_rate,c.id as subBrkID,c.book_con_dtls_id, c.description, c.color_id, c.size_id, c.qnty, c.rate, c.amount ,c.booked_qty, c.gmts_color_id, c.gmts_size_id
					from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
					where a.entry_form=255 and a.within_group=1 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.id=c.mst_id and a.id=$update_id
					order by a.id DESC"; 
					//book_con_dtls_id, description, color_id, size_id, qnty, rate, amount,booked_qty
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
						
						$dtlSID_arr_qty[$row[csf('subDtlsID')]]['info']=$row[csf('order_id')].'_'.$row[csf('order_no')].'_'.$row[csf('buyer_po_id')].'_'.$row[csf('booking_dtls_id')].'_'.change_date_format($row[csf('delivery_date')], "", "",1).'_'.$row[csf('buyer_po_no')].'_'.$row[csf('buyer_style_ref')].'_'.$row[csf('buyer_buyer')].'_'.$row[csf('section')].'_'.$row[csf('sub_section')].'_'.$row[csf('item_group')].'_'.$row[csf('order_uom')];
						$dtlSID_arr_qty[$row[csf('subDtlsID')]]['info_withQty']=$row[csf('order_id')].'_'.$row[csf('order_no')].'_'.$row[csf('buyer_po_id')].'_'.$row[csf('booking_dtls_id')].'_'.change_date_format($row[csf('delivery_date')], "", "",1).'_'.$row[csf('buyer_po_no')].'_'.$row[csf('buyer_style_ref')].'_'.$row[csf('buyer_buyer')].'_'.$row[csf('section')].'_'.$row[csf('sub_section')].'_'.$row[csf('item_group')].'_'.$row[csf('order_uom')].'_'.$row[csf('order_quantity')].'_'.$row[csf('rate')];
						$dtlSID_arr_qty[$row[csf('subDtlsID')]]['break_ids'].=$row[csf('subBrkID')].",";
						
						$brk_arr_qty[$row[csf('subBrkID')]]['info']=$row[csf('book_con_dtls_id')]."_".$row[csf('description')]."_".$row[csf('color_id')]."_".$row[csf('size_id')];
						$brk_arr_qty[$row[csf('subBrkID')]]['info_withQty']=$row[csf('book_con_dtls_id')]."_".$row[csf('description')]."_".$row[csf('color_id')]."_".$row[csf('size_id')]."_".$row[csf('qnty')]."_".$row[csf('rate')]."_".$row[csf('amount')]."_".$row[csf('booked_qty')];

						$field_array_re="booked_qty*order_quantity*rate*amount*rate_domestic*amount_domestic*is_revised*updated_by*update_date";
						$field_brk_array_re="qnty*rate*amount*booked_qty*is_revised";
						$field_job_array_re="job_quantity*is_revised*updated_by*update_date";
					}
				}  
				else
				{
					$lastUpdate=str_replace("'",'',$is_apply_last_update);
					$revise_no=str_replace("'",'',$txt_revise_no)+1;
				} 

				
				$data_array="'".$cbo_location_name."'*'".$cbo_within_group."'*'".$cbo_party_name."'*'".$cbo_party_location."'*'".$cbo_currency."'*'".$txt_order_receive_date."'*'".$txt_delivery_date."'*'".$txt_rec_start_date."'*'".$txt_rec_end_date."'*'".$hid_order_id."'*'".$txt_order_no."'*'".$txt_exchange_rate."'*'".$cbo_team_leader."'*'".$cbo_team_member."'*'".$txt_fac_merchan."'*'".$txt_buying_merchant."'*'".$txt_delivery_point."'*'".$cbo_ready_to_approved."'*'".$txt_remarks."'*'".$cbo_payterm_id."'*'".$cbo_wo_type_id."'*'".$cbo_party_wise_grade."'*'".$txt_trims_ref."'*'".$lastUpdate."'*'".$revise_no."'*'".$cbo_Status_type."'*'".$txt_buyer_tb."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				
				
				//echo "10**".str_replace("'",'',$is_apply_last_update); die; 
				$id1=return_next_id( "id", "subcon_ord_dtls",1) ;
				$id3=return_next_id( "id", "subcon_ord_breakdown", 1) ;
				$id4=return_next_id( "id", "subcon_ord_breakdown_size_info", 1) ;

				//$dtlsIdForBreak=$id1;
				$add_comma=0; $breakDelIds=''; $item_missmatch_chk_arr=array(); $revDtlsId=array(); $new_array_color=array();  $new_array_size=array();
                $data_arra3=""; $data_arra6=""; $add_commadtls=0; $add_commadtls1=0;
				//echo "10**".$rID.str_replace("'",'',$is_apply_last_update); 
				$cbo_within_group=str_replace("'","",$cbo_within_group);
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
					$txtSamSubDate 			= "txtSamSubDate_".$i;
					$txtSamApprDate 		= "txtSamApprDate_".$i;
					$txtOrderDeliveryDate 	= "txtOrderDeliveryDate_".$i;
					$txtDomRate 			= "txtDomRate_".$i;
					$txtDomamount 			= "txtDomamount_".$i;
					$hdnDtlsdata 			= "hdnDtlsdata_".$i;
					$hdnDtlsUpdateId 		= "hdnDtlsUpdateId_".$i;
					$sizedtlsdata 		    = "sizedtlsdata_".$i;
					$hdnbookingDtlsId 		= "hdnbookingDtlsId_".$i;
					$txtDeletedId 			= "txtDeletedId_".$i;
					$txtIsWithOrder 		= "txtIsWithOrder_".$i;
					$txtIsDuplicate 		= "txtIsDuplicate_".$i;
					$cboBookUom 			= "cboBookUom_".$i;
					$txtConvFactor 			= "txtConvFactor_".$i;
					$txtBookQty 			= "txtBookQty_".$i;
					$cboSource 				= "cboSource_".$i;
					$txtPlaneCut 			= "txtPlaneCut_".$i;
					
					
					    $var_bil_quantity=$bil_qty_arr[str_replace("'",'',$$hdnDtlsUpdateId)]['bil_quantity'];
			            $var_delevery_qty=$bil_qty_arr[str_replace("'",'',$$hdnDtlsUpdateId)]['delevery_qty'];
						$var_production_qty=$bil_qty_arr[str_replace("'",'',$$hdnDtlsUpdateId)]['production_qty'];
						
						if($var_bil_quantity)
						{
								if($variable_bill_prod_delv==1)
								{
									if(str_replace("'",'',$$txtOrderQuantity)<$var_bil_quantity)
									{
											//echo "20**".str_replace("'","",$txt_job_no)."**".$rec_number;
										echo "28**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no)."**".$next_process_bill;
										disconnect($con);
										 die;
									}
								}
						}
						else
						{
							
								if($variable_bill_prod_delv==1)
								{
										if(str_replace("'",'',$$txtOrderQuantity)<$var_delevery_qty)
										{
												//echo "20**".str_replace("'","",$txt_job_no)."**".$rec_number;
											echo "27**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no)."**".$next_process_bill;
											disconnect($con);
											 die;
										}
								
								}
 						 }
 						 // echo "10**".$variable_bill_prod_delv."eeee"; die;
  						//echo "10**".$variable_bill_prod_delv; die;
						if($variable_bill_prod_delv==3)
						{
							if(str_replace("'",'',$$txtOrderQuantity)<$var_delevery_qty)
							{
									//echo "20**".str_replace("'","",$txt_job_no)."**".$rec_number;
								echo "27**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no)."**".$next_process_bill;
								disconnect($con);
								 die;
							}
						}
						
						if($variable_bill_prod_delv==2)
						{
							if(str_replace("'",'',$$txtOrderQuantity)<$var_production_qty)
							{
									//echo "20**".str_replace("'","",$txt_job_no)."**".$rec_number;
								echo "24**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no)."**".$next_process_bill;
								disconnect($con);
								 die;
							}
						}
						

					
					$txtbuyerPo=str_replace($str_rep,' ',$$txtbuyerPo);
					$txtstyleRef=str_replace($str_rep,' ',$$txtstyleRef);
					$txtIsDuplicate=str_replace("'","",$$txtIsDuplicate);
					$aa	=str_replace("'",'',$$hdnDtlsUpdateId);

					$orddelivery_date=strtotime(str_replace("'",'',$$txtOrderDeliveryDate));
					if($receive_date>$orddelivery_date)
					{
						echo "26**"; disconnect($con); die;
					} 
					if($db_type==0) $orderDeliveryDate=change_date_format(str_replace("'",'',$$txtOrderDeliveryDate),'yyyy-mm-dd');else $orderDeliveryDate=change_date_format(str_replace("'",'',$$txtOrderDeliveryDate), "", "",1);	
					if(str_replace("'",'',$$txtDeletedId)!='') $breakDelIds .= str_replace("'",'',$$txtDeletedId).","; 

					$txtSamSubDate=change_date_format(str_replace("'",'',$$txtSamSubDate), "", "",1);
					$txtSamApprDate=change_date_format(str_replace("'",'',$$txtSamApprDate), "", "",1);
					if(str_replace("'",'',$$txtbuyerPoId)=="") $txtbuyerPoId=0; else $txtbuyerPoId=str_replace("'",'',$$txtbuyerPoId);
					if(str_replace("'",'',$$hdnbookingDtlsId)=="") $hdnbookingDtlsId=0; else $hdnbookingDtlsId=str_replace("'",'',$$hdnbookingDtlsId);
					
					if(str_replace("'",'',$is_apply_last_update)==1)
					{ 
						$actDelQTY=0; $prev_item='';$revised_item='';
						$prev_item=$dtlSID_arr_qty[$aa]['info'];
						$revised_item=str_replace("'","",$hid_order_id)."_".str_replace("'","",$txt_order_no)."_".str_replace("'","",$$txtbuyerPoId)."_".str_replace("'","",$$hdnbookingDtlsId)."_".str_replace("'","",$orderDeliveryDate)."_'".trim($txtbuyerPo)."'_'".$txtstyleRef."'_".str_replace("'","",$$txtbuyer)."_".str_replace("'","",$$cboSection)."_".str_replace("'","",$$cboSubSection)."_".str_replace("'","",$$cboItemGroup)."_".str_replace("'","",$$cboUom);
						
						//echo "10**".$prev_item.'**'.$revised_item.'==';
						if($prev_item==$revised_item)
						{
							if($aa!='') $revDtlsId[$aa]=$aa;
							$prev_item_withQty=explode("_",$dtlSID_arr_qty[$aa]['info_withQty']);
							//echo "10**".$prev_item_withQty[12].'**'.str_replace("'",'',$$txtOrderQuantity).'==';
							if($prev_item_withQty[12] !=str_replace("'",'',$$txtOrderQuantity))
							{
								$actDelQTY 	=$delBrktlSID_arr[$aa]['delevery_qty']; 
								//echo "10**".$actDelQTY.'**'.str_replace("'",'',$$txtOrderQuantity).'==';
								if($actDelQTY>str_replace("'",'',$$txtOrderQuantity))
								{
									echo "27**".str_replace("'",'',$$txtOrderQuantity)."**".$actDelQTY;
									disconnect($con);
									die;
								}
							}
						}
						else
						{
							if($aa!='') $item_missmatch_chk_arr[$aa]=$aa;
						}
					
						if(str_replace("'",'',$$hdnDtlsUpdateId)!="" && (!in_array(str_replace("'",'',$$hdnDtlsUpdateId), $item_missmatch_chk_arr)))
						{
							$data_array2[$aa]=explode("*",("".$hid_order_id."*'".$txt_order_no."'*".$txtbuyerPoId."*'".$hdnbookingDtlsId."'*".str_replace(",",'',$$txtOrderQuantity)."*".$$cboUom."*".$$txtRate."*".str_replace(",",'',$$txtAmount)."*'".$txtSamSubDate."'*'".$txtSamApprDate."'*'".$orderDeliveryDate."'*'".trim($txtbuyerPo)."'*'".trim($txtstyleRef)."'*".$$txtbuyer."*".$$cboSection."*".$$cboSubSection."*".$$cboItemGroup."*".$$txtDomRate."*".$$txtDomamount."*".$$cboBookUom."*".str_replace(",",'',$$txtConvFactor)."*".str_replace(",",'',$$txtBookQty)."*".$$cboSource."*".$user_id."*'".$pc_date_time."'*".$$txtPlaneCut.""));
							$hdn_dtls_id_arr[]=str_replace("'",'',$$hdnDtlsUpdateId);
						}
						else
						{
							if ($add_commaa!=0) $data_array5 .=","; $add_comma=0;
							$data_array5 .="(".$id1.",".$update_id.",'".$txt_job_no."','".$hid_order_id."','".$txt_order_no."','".$txtbuyerPoId."','".$hdnbookingDtlsId."',".str_replace(",",'',$$txtOrderQuantity).",".$$cboUom.",".$$txtRate.",".str_replace(",",'',$$txtAmount).",'".$txtSamSubDate."','".$txtSamApprDate."','".$orderDeliveryDate."','".trim($txtbuyerPo)."','".trim($txtstyleRef)."',".$$txtbuyer.",".$$cboSection.",".$$cboSubSection.",".$$cboItemGroup.",".str_replace(",",'',$$txtDomRate).",".str_replace(",",'',$$txtDomamount).",".$$cboBookUom.",".str_replace(",",'',$$txtConvFactor).",".str_replace(",",'',$$txtBookQty).",".str_replace(",",'',$$txtIsWithOrder).",".$$cboSource.",'".$user_id."','".$pc_date_time."',".$$txtPlaneCut.")";
							//$id1++;
						}
					}else{
						if(str_replace("'",'',$$hdnDtlsUpdateId)!="")
						{
							$data_array2[$aa]=explode("*",("".$hid_order_id."*'".$txt_order_no."'*".$txtbuyerPoId."*'".$hdnbookingDtlsId."'*".str_replace(",",'',$$txtOrderQuantity)."*".$$cboUom."*".$$txtRate."*".str_replace(",",'',$$txtAmount)."*'".$txtSamSubDate."'*'".$txtSamApprDate."'*'".$orderDeliveryDate."'*'".trim($txtbuyerPo)."'*'".trim($txtstyleRef)."'*".$$txtbuyer."*".$$cboSection."*".$$cboSubSection."*".$$cboItemGroup."*".$$txtDomRate."*".$$txtDomamount."*".$$cboBookUom."*".str_replace(",",'',$$txtConvFactor)."*".str_replace(",",'',$$txtBookQty)."*".$$cboSource."*".$user_id."*'".$pc_date_time."'*".$$txtPlaneCut.""));
							$hdn_dtls_id_arr[]=str_replace("'",'',$$hdnDtlsUpdateId);
						}
						else
						{
							if ($add_commaa!=0) $data_array5 .=","; $add_comma=0;
							$data_array5 .="(".$id1.",".$update_id.",'".$txt_job_no."','".$hid_order_id."','".$txt_order_no."','".$txtbuyerPoId."','".$hdnbookingDtlsId."',".str_replace(",",'',$$txtOrderQuantity).",".$$cboUom.",".$$txtRate.",".str_replace(",",'',$$txtAmount).",'".$txtSamSubDate."','".$txtSamApprDate."','".$orderDeliveryDate."','".trim($txtbuyerPo)."','".trim($txtstyleRef)."',".$$txtbuyer.",".$$cboSection.",".$$cboSubSection.",".$$cboItemGroup.",".str_replace(",",'',$$txtDomRate).",".str_replace(",",'',$$txtDomamount).",".$$cboBookUom.",".str_replace(",",'',$$txtConvFactor).",".str_replace(",",'',$$txtBookQty).",".str_replace(",",'',$$txtIsWithOrder).",".$$cboSource.",'".$user_id."','".$pc_date_time."',".$$txtPlaneCut.")";
							//$id1++;
						}
					}
					//echo '10**'.$data_array5; //die;
					$dtls_data=explode("***",str_replace("'",'',$$hdnDtlsdata));
					$dtls_data_size=explode("***",str_replace("'",'',$$sizedtlsdata));

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
						$style=$exdata[10];
						$excess_cut= "'".$exdata[11]."'";
						$plan_cut=$exdata[12];
						$booked_qty=str_replace(",",'',$exdata[12])*str_replace("'",'',$$txtConvFactor);
						$plan_cut_amount=$exdata[13];
						$ply="'".$exdata[14]."'";
						$txtgmtscolor="'".$exdata[15]."'";
						$txtgmtssize="'".$exdata[16]."'";
                        $exdata_size = [];
                        if($dtls_data_size[$j] != "") {
                            $exdata_size = explode("_", $dtls_data_size[$j]);
                        }
                        $size_update_id = $exdata_size[0];
						$description=str_replace($str_rep,' ',$description);
						//echo "10**".$dtlsup_id; die;
						if (str_replace("'", "", trim($colorname)) != "") {
							if (!in_array(str_replace("'", "", trim($colorname)),$new_array_color)){
								$color_id = return_id( str_replace("'", "", trim($colorname)), $color_library_arr, "lib_color", "id,color_name","255");
								$new_array_color[$color_id]=str_replace("'", "", trim($colorname));
							}
							else $color_id =  array_search(str_replace("'", "", trim($colorname)), $new_array_color);
						} else $color_id = 0;
						
						
						if(str_replace("'","",$sizename)!="" && count($exdata_size)== 0)
						{ 
							if (!in_array(str_replace("'","",$sizename),$new_array_size))
							{
								$size_id = return_id( str_replace("'","",$sizename), $size_library_arr, "lib_size", "id,size_name","255"); 
								$new_array_size[$size_id]=str_replace("'","",$sizename);
							}
							else $size_id =  array_search(str_replace("'","",$sizename), $new_array_size); 
						}
						else $size_id=0;

						if (str_replace("'", "", trim($txtgmtscolor)) != "") {
							if (!in_array(str_replace("'", "", trim($txtgmtscolor)),$new_array_color)){
								$gmts_color_id = return_id( str_replace("'", "", trim($txtgmtscolor)), $color_library_arr, "lib_color", "id,color_name","255");
								$new_array_color[$gmts_color_id]=str_replace("'", "", trim($txtgmtscolor));
							}
							else $gmts_color_id =  array_search(str_replace("'", "", trim($txtgmtscolor)), $new_array_color);
						} else $gmts_color_id = 0; 

						if(str_replace("'","",$txtgmtssize)!="" )
						{ 
							if (!in_array(str_replace("'","",$txtgmtssize),$new_array_size))
							{
								$gmts_size_id = return_id( str_replace("'","",$txtgmtssize), $size_library_arr, "lib_size", "id,size_name","255"); 
								$new_array_size[$gmts_size_id]=str_replace("'","",$txtgmtssize);
							}
							else $gmts_size_id =  array_search(str_replace("'","",$txtgmtssize), $new_array_size); 
						}
						else $gmts_size_id=0;

						if(str_replace("'",'',$is_apply_last_update)==1)
						{ 

							$prev_item_brk=$brk_arr_qty[$bb]['info_withQty'];
							$revised_brk_item=str_replace("'","",$book_con_dtls_id)."_'".$description."'_".$color_id."_".$size_id;
							$actBrkDelQTY=0;
							if($prev_item_brk==$revised_brk_item)
							{
								$prev_item_brk=explode("_",$dtlSID_arr_qty[$bb]['info_withQty']);
								if($prev_item_brk[4] !=str_replace("'",'',$qty))
								{
									$actBrkDelQTY 	=$delBrkQty_arr[$bb]['delevery_qty']; 
									//echo "10**".$actBrkDelQTY.'**'.str_replace("'",'',$qty).'**'.$prev_item_brk[4].'==';
									if($actBrkDelQTY>str_replace("'",'',$qty))
									{
										echo "27**".str_replace("'",'',$qty)."**".$actBrkDelQTY;
										disconnect($con);
										die;
									}
								}
							}
							else
							{
								$brk_item_missmatch_chk_arr[$bb]=$bb;
							}

							if($bb==0  || ((in_array($dtlsIdForBreak, $brk_item_missmatch_chk_arr)) && str_replace("'","",$cbo_within_group)==1))
							{
								if(str_replace("'",'',$$hdnDtlsUpdateId)!='') $dtlsIdForBreak=str_replace("'",'',$$hdnDtlsUpdateId); else $dtlsIdForBreak=$id1;
								if ($add_commadtls!=0) $data_array3 .=",";
								$data_array3.="(".$id3.",".$dtlsIdForBreak.",'".$hid_order_id."','".$txt_job_no."',".$book_con_dtls_id.",'".trim($description)."','".$color_id."','".$size_id."',".$qty.",".$rate.",".$amount.",".$booked_qty.",'".$style."','".$user_id."','".$pc_date_time."',".$excess_cut.",".$plan_cut.",".$plan_cut_amount.",".$ply.",".$sizename.",".$gmts_color_id.",".$gmts_size_id.")";
                                if(count($exdata_size) > 0) {
                                    $length = $exdata_size[1] != '' ? $exdata_size[1] : 0;
                                    $width = $exdata_size[2] != '' ? $exdata_size[2] : 0;
                                    $height = $exdata_size[3] != '' ? $exdata_size[3] : 0;
                                    $flap = $exdata_size[4] != '' ? $exdata_size[4] : 0;
                                    $gusset = $exdata_size[5] != '' ? $exdata_size[5] : 0;
                                    $thickness = $exdata_size[6];
                                    $meagurement_id = $exdata_size[7];
                                    if ($add_commadtls1 != 0) $data_array6 .= ",";
                                    $data_array6 .= "(" . $id4 . "," . $id3 . "," . $length . "," . $width . "," . $height . "," . $flap . "," . $gusset . ",'" . $thickness . "'," . $meagurement_id . ")";
                                }
                                $id3=$id3+1; $add_commadtls++;$id4=$id4+1; $add_commadtls1++;
							}
							else if($bb!=0 && (!in_array($dtlsIdForBreak, $brk_item_missmatch_chk_arr)))
							{
								$data_array4[$bb]=explode("*",($hid_order_id."*".$book_con_dtls_id."*'".trim($description)."'*".$color_id."*".$size_id."*".$qty."*".$rate."*".$amount."*".$booked_qty."*'".$style."'*".$user_id."*'".$pc_date_time."'*".$excess_cut."*".$plan_cut."*".$plan_cut_amount."*".$ply."*".$sizename."*".$gmts_color_id."*".$gmts_size_id));
                                if($size_update_id!=''){
                                    $length = $exdata_size[1] != '' ? $exdata_size[1] : 0;
                                    $width = $exdata_size[2] != '' ? $exdata_size[2] : 0;
                                    $height = $exdata_size[3] != '' ? $exdata_size[3] : 0;
                                    $flap = $exdata_size[4] != '' ? $exdata_size[4] : 0;
                                    $gusset = $exdata_size[5] != '' ? $exdata_size[5] : 0;
                                    $thickness = $exdata_size[6];
                                    $meagurement_id = $exdata_size[7];
                                    $data_array7[$size_update_id]=explode("*",($length."*".$width."*".$height."*".$flap."*".$gusset."*'".$thickness."'*".$meagurement_id));
                                    $size_update_id_arr[] = $size_update_id;
                                }
								$hdn_break_id_arr[]		=$bb;
							}
						}else{
							if($bb==0 )
							{
								if ($add_commadtls!=0) $data_array3 .=",";
								
								if(str_replace("'",'',$$hdnDtlsUpdateId)!='' ) $dtlsIdForBreak=str_replace("'",'',$$hdnDtlsUpdateId); else $dtlsIdForBreak=$id1;
								//echo "10**".$dtlsIdForBreak; die;
								$data_array3.="(".$id3.",".$dtlsIdForBreak.",'".$hid_order_id."','".$txt_job_no."',".$book_con_dtls_id.",'".trim($description)."','".$color_id."','".$size_id."',".$qty.",".$rate.",".$amount.",".$booked_qty.",'".$style."','".$user_id."','".$pc_date_time."',".$excess_cut.",".$plan_cut.",".$plan_cut_amount.",".$ply.",".$sizename.",".$gmts_color_id.",".$gmts_size_id.")";
                                if(count($exdata_size) > 0) {
                                    $length = $exdata_size[1] != '' ? $exdata_size[1] : 0;
                                    $width = $exdata_size[2] != '' ? $exdata_size[2] : 0;
                                    $height = $exdata_size[3] != '' ? $exdata_size[3] : 0;
                                    $flap = $exdata_size[4] != '' ? $exdata_size[4] : 0;
                                    $gusset = $exdata_size[5] != '' ? $exdata_size[5] : 0;
                                    $thickness = $exdata_size[6];
                                    $meagurement_id = $exdata_size[7];
                                    if ($add_commadtls1 != 0) $data_array6 .= ",";
                                    $data_array6 .= "(" . $id4 . "," . $id3 . "," . $length . "," . $width . "," . $height . "," . $flap . "," . $gusset . ",'" . $thickness . "'," . $meagurement_id . ")";
                                }
                                $id3=$id3+1; $add_commadtls++;
                                $id4=$id4+1; $add_commadtls1++;
							}
							else{
								$data_array4[$bb]=explode("*",($hid_order_id."*".$book_con_dtls_id."*'".trim($description)."'*".$color_id."*".$size_id."*".$qty."*".$rate."*".$amount."*".$booked_qty."*'".$style."'*".$user_id."*'".$pc_date_time."'*".$excess_cut."*".$plan_cut."*".$plan_cut_amount."*".$ply."*".$sizename."*".$gmts_color_id."*".$gmts_size_id));
                                if($size_update_id!=''){
                                    $length = $exdata_size[1] != '' ? $exdata_size[1] : 0;
                                    $width = $exdata_size[2] != '' ? $exdata_size[2] : 0;
                                    $height = $exdata_size[3] != '' ? $exdata_size[3] : 0;
                                    $flap = $exdata_size[4] != '' ? $exdata_size[4] : 0;
                                    $gusset = $exdata_size[5] != '' ? $exdata_size[5] : 0;
                                    $thickness = $exdata_size[6];
                                    $meagurement_id = $exdata_size[7];
                                    $data_array7[$size_update_id]=explode("*",($length."*".$width."*".$height."*".$flap."*".$gusset."*'".$thickness."'*".$meagurement_id));
                                    $size_update_id_arr[] = $size_update_id;
                                }
								$hdn_break_id_arr[]		=$bb;
							}
						}
					}
					if(str_replace("'",'',$$hdnDtlsUpdateId)=="")
					{
						$id1++;
					}
				}

				if(str_replace("'",'',$is_apply_last_update)==1)
				{
					$result=array_diff($dtlSID_arr,$revDtlsId); // Difference between Order and new order
					$actualDelQTY=''; $revisedFlag=0;
					$marged_result=$result+$item_missmatch_chk_arr;
					$marged_result=array_unique($marged_result);

					foreach ($marged_result as $rcvDID => $val) 
					{
						if (in_array($rcvDID,$delRectlSID_arr))
						{
							$actualDelQTY 	=$delBrktlSID_arr[$rcvDID]['delevery_qty']; 
						}
						else
						{
							$actualDelQTY=0;
						}
						$recBookQty			+=$dtlSID_arr_qty[$rcvDID]['booked_qty'];
						//$actualProdItem		=array_unique(explode("#",$prodItem));
						$booked_conv_fac	=$dtlSID_arr_qty[$rcvDID]['booked_conv_fac'];
						$order_quantity 	=$dtlSID_arr_qty[$rcvDID]['order_quantity'];
						$rate 				=$dtlSID_arr_qty[$rcvDID]['rate'];
						$amount 			=$dtlSID_arr_qty[$rcvDID]['amount'];
						$rate_domestic 	 	=$dtlSID_arr_qty[$rcvDID]['rate_domestic'];
						$amount_domestic 	=$dtlSID_arr_qty[$rcvDID]['amount_domestic'];
						$exchange_rate 		=$dtlSID_arr_qty[$rcvDID]['exchange_rate'];
						//echo "10**".$actualDelQTY; 
						$neddToRevisedVal=$reOrder_quantity=$rerate=$reAmount=$reRate_domestic=$reAmount_domestic=0;
						if($actualDelQTY>0)
						{
							$neddToRevisedVal 	=$actualDelQTY;
							$reOrder_quantity 	=$neddToRevisedVal;
							$rerate 			=$rate;
							$reAmount 			=$reOrder_quantity*$rate;
							$reRate_domestic 	=$exchange_rate*$rate;
							$reAmount_domestic 	=$exchange_rate*$reAmount;

							$break_ids=array_unique(explode(",",(chop($delBrktlSID_arr[$rcvDID]['breakDelv_id'],','))));
							$break_datas='';
							foreach ($break_ids as  $bId)
							{
								//$brk_data_arr_qty[$row[csf('subBrkID')]]['break_datas']=$row[csf('qnty')]."_".$row[csf('rate')]."_".$row[csf('amount')]."_".$row[csf('booked_qty')];
								$break_datas 	= $brk_data_arr_qty[$bId]['break_datas'];
								$break_data 	=array_unique(explode("_",($break_datas)));
								$reBrkQty 		=($break_data[0]*$reOrder_quantity)/$order_quantity;
								$reBrkRate 		=$break_data[1];
								$reBrkAmt 		=($reBrkQty*$reBrkRate);
								$reBrkBokQty 	=($reBrkQty);
								
								$data_brk_array_re[$bId]=explode("*",("'".$reBrkQty."'*'".$reBrkRate."'*'".$reBrkAmt."'*'".$reBrkBokQty."'*1"));
								$revBrk_id_arr[]=$bId;
							}
						}
						else
						{
							$break_ids=array_unique(explode(",",(chop($delBrktlSID_arr[$rcvDID]['break_id'],','))));
							$break_datas='';$reBrkQty=$reBrkRate=$reBrkAmt=$reBrkBokQty=0;
							foreach ($break_ids as  $bId)
							{
								$data_brk_array_re[$bId]=explode("*",("'".$reBrkQty."'*'".$reBrkRate."'*'".$reBrkAmt."'*'".$reBrkBokQty."'*2"));
							}
						}

						$data_array_re[$rcvDID] =explode("*",("'".$neddToRevisedVal."'*'".$reOrder_quantity."'*'".$rerate."'*'".$reAmount."'*'".$reRate_domestic."'*'".$reAmount_domestic."'*2*".$user_id."*'".$pc_date_time."'"));
						$revDtls_id_arr[] =$rcvDID;
					}
					//echo "10**".$flag."**".$flag; die;
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
					if($flag==1)
					{
						$rIDBooking=execute_query( "update wo_booking_mst set lock_another_process=1 where booking_no ='".$txt_order_no."'",1);
						if($rIDBooking==1) $flag=1; else $flag=0;
					}
				}
				if($data_array5!="" && $flag==1)
				{
					//echo "10**INSERT INTO subcon_ord_dtls (".$field_array5.") VALUES ".$data_array5; die;
					$rID7=sql_insert("subcon_ord_dtls",$field_array5,$data_array5,0);
					if($rID7) $flag=1; else $flag=0;
				}

				$id_break=implode(',',$hiddenTblIdBreak);
				if($data_array3!=""  && $flag==1)
				{
					//echo "10**INSERT INTO subcon_ord_breakdown (".$field_array3.") VALUES ".$data_array3; die;
					$rID4=sql_insert("subcon_ord_breakdown",$field_array3,$data_array3,1);
					if($rID4) $flag=1; else $flag=0;
				}
                if($data_array6!=""  && $flag==1)
                {
                    $rID6=sql_insert("subcon_ord_breakdown_size_info",$field_array6,$data_array6,1);
                    if($rID6) $flag=1; else $flag=0;
                }

				$breakDelIds=chop($breakDelIds,",");
				if ($breakDelIds!=""  && $flag==1)
				{
					//$rID8=execute_query("UPDATE subcon_ord_breakdown SET updated_by=$user_id, update_date='$pc_date_time', status_active=0, is_deleted=1 WHERE mst_id in ($txt_deleted_id) and job_no_mst='$txt_job_no'");
					//$rID8=execute_query( "delete from subcon_ord_breakdown where id in ( $breakDelIds)",0);
					$rID8=execute_query("UPDATE subcon_ord_breakdown SET updated_by=$user_id, update_date='$pc_date_time', status_active=0, is_deleted=1 WHERE  id in ( $breakDelIds)");
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
				
					$rID8=execute_query("UPDATE subcon_ord_breakdown SET updated_by=$user_id, update_date='$pc_date_time', status_active=0, is_deleted=1 WHERE mst_id in ($txt_deleted_id) and job_no_mst='$txt_job_no'");
					//$rID8=execute_query( "delete from subcon_ord_breakdown where mst_id in ( $txt_deleted_id)",0);
					if($flag==1)
					{
						if($rID8) $flag=1; else $flag=0; 
					} 
				}
			}
			else
			{
				
				//echo "10**".$variable_bill_prod_delv."eeee"; die;
				
				if($variable_bill_prod_delv==1 || $variable_bill_prod_delv==2 || $variable_bill_prod_delv==3)
				{
					
 					//echo "10**".$variable_bill_prod_delv."eeee"; die;
					$field_array="ready_to_approved*buyer_tb*remarks*status*updated_by*update_date";
					$field_array2="rate*amount*rate_domestic*amount_domestic*updated_by*update_date*plan_cut*order_quantity*booked_qty"; 
					//$field_array4="rate*amount*updated_by*update_date*excess_cut*plan_cut*plan_cut_amount";
					$field_array4="order_id*book_con_dtls_id*description*color_id*size_id*qnty*rate*amount*booked_qty*style*updated_by*update_date*excess_cut*plan_cut*plan_cut_amount*ply*size_name*gmts_color_id*gmts_size_id";
					$data_array="'".$cbo_ready_to_approved."'*'".$txt_buyer_tb."'*'".$txt_remarks."'*'".$cbo_Status_type."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
					$field_array7="length*width*height*flap*gusset*thickness*measurementid";
	
					$field_array_status="updated_by*update_date*status_active*is_deleted";
					$data_array_status=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
					
					
					for($i=1; $i<=$total_row; $i++)
					{
						$hdnDtlsdata 			= "hdnDtlsdata_".$i;
						$sizedtlsdata 		    = "sizedtlsdata_".$i;
						$txtRate 				= "txtRate_".$i;
						$txtOrderQuantity 		= "txtOrderQuantity_".$i;
						$txtBookQty 			= "txtBookQty_".$i;
						$txtAmount 				= "txtAmount_".$i;			
						$txtOrderDeliveryDate 	= "txtOrderDeliveryDate_".$i;
						$txtDomRate 			= "txtDomRate_".$i;
						$txtDomamount 			= "txtDomamount_".$i;
						$hdnDtlsUpdateId 		= "hdnDtlsUpdateId_".$i;
						$txtPlaneCut 	     	= "txtPlaneCut_".$i;
						$txtConvFactor 			= "txtConvFactor_".$i;
						$hdn_dtls_id_arr[]=str_replace("'",'',$$hdnDtlsUpdateId);
						$aa	=str_replace("'",'',$$hdnDtlsUpdateId);
						$dtls_data=explode("***",str_replace("'",'',$$hdnDtlsdata));
						$dtls_data_size=explode("***",str_replace("'",'',$$sizedtlsdata));
						
						$var_bil_quantity=$bil_qty_arr[str_replace("'",'',$$hdnDtlsUpdateId)]['bil_quantity'];
			            $var_delevery_qty=$bil_qty_arr[str_replace("'",'',$$hdnDtlsUpdateId)]['delevery_qty'];
						$var_production_qty=$bil_qty_arr[str_replace("'",'',$$hdnDtlsUpdateId)]['production_qty'];
						
						if($var_bil_quantity)
						{
								if($variable_bill_prod_delv==1)
								{
									if(str_replace("'",'',$$txtOrderQuantity)<$var_bil_quantity)
									{
											//echo "20**".str_replace("'","",$txt_job_no)."**".$rec_number;
										echo "28**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no)."**".$next_process_bill;
										disconnect($con);
										 die;
									}
								}
						}
						else
						{
							
								if($variable_bill_prod_delv==1)
								{
										if(str_replace("'",'',$$txtOrderQuantity)<$var_delevery_qty)
										{
												//echo "20**".str_replace("'","",$txt_job_no)."**".$rec_number;
											echo "27**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no)."**".$next_process_bill;
											disconnect($con);
											 die;
										}
								
								}
 						 }
						 
						 // echo "10**".$variable_bill_prod_delv."eeee"; die;
						
 						//echo "10**".$variable_bill_prod_delv; die;
						if($variable_bill_prod_delv==3)
						{
							if(str_replace("'",'',$$txtOrderQuantity)<$var_delevery_qty)
							{
									//echo "20**".str_replace("'","",$txt_job_no)."**".$rec_number;
								echo "27**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no)."**".$next_process_bill;
								disconnect($con);
								 die;
							}
						}
						
						if($variable_bill_prod_delv==2)
						{
							if(str_replace("'",'',$$txtOrderQuantity)<$var_production_qty)
							{
									//echo "20**".str_replace("'","",$txt_job_no)."**".$rec_number;
								echo "24**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no)."**".$next_process_bill;
								disconnect($con);
								 die;
							}
						}
						
						
						
						//echo "10**".str_replace("'",'',$$txtOrderQuantity)."eeee".$var_delevery_qty; die;
	
						$data_array2[$aa]=explode("*",("".$$txtRate."*".str_replace(",",'',$$txtAmount)."*".$$txtDomRate."*".$$txtDomamount."*".$user_id."*'".$pc_date_time."'*".$$txtPlaneCut."*".str_replace(",",'',$$txtOrderQuantity)."*".str_replace(",",'',$$txtBookQty)."")); 	 
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
							$style=$exdata[10];
							$excess_cut= "'".$exdata[11]."'";
							$plan_cut=$exdata[12];
							$booked_qty=str_replace(",",'',$exdata[12])*str_replace("'",'',$$txtConvFactor);
							$plan_cut_amount=$exdata[13];
							$description=str_replace($str_rep,' ',$description);
							$ply="'".$exdata[14]."'";
							$gmts_color_id="'".$exdata[17]."'";
							$gmts_size_id="'".$exdata[18]."'";
							$exdata_size = [];
							if($dtls_data_size[$j] != "") {
								$exdata_size = explode("_", $dtls_data_size[$j]);
							}
							$size_update_id = $exdata_size[0];
							if (str_replace("'", "", trim($colorname)) != "") {
								if (!in_array(str_replace("'", "", trim($colorname)),$new_array_color)){
									$color_id = return_id( str_replace("'", "", trim($colorname)), $color_library_arr, "lib_color", "id,color_name","255");
									$new_array_color[$color_id]=str_replace("'", "", trim($colorname));
								}
								else $color_id =  array_search(str_replace("'", "", trim($colorname)), $new_array_color);
							} else $color_id = 0;
	
							if(str_replace("'","",$sizename)!="" && $size_update_id == "")
							{ 
								if (!in_array(str_replace("'","",$sizename),$new_array_size))
								{
									$size_id = return_id( str_replace("'","",$sizename), $size_library_arr, "lib_size", "id,size_name","255"); 
									$new_array_size[$size_id]=str_replace("'","",$sizename);
								}
								else $size_id =  array_search(str_replace("'","",$sizename), $new_array_size); 
							}
							else $size_id=0;
	
							if($bb!='')
							{
								$data_array4[$bb]=explode("*",("".$hid_order_id."*".$book_con_dtls_id."*'".trim($description)."'*".$color_id."*".$size_id."*".$qty."*".$rate."*".$amount."*".$booked_qty."*'".$style."'*".$user_id."*'".$pc_date_time."'*".$excess_cut."*".$plan_cut."*".$plan_cut_amount."*".$ply."*".$sizename."*".$gmts_color_id."*".$gmts_size_id.""));
								$hdn_break_id_arr[]		=$bb;
							}
							if($size_update_id!='')
							{
								$length = $exdata_size[1] != '' ? $exdata_size[1] : 0;
								$width = $exdata_size[2] != '' ? $exdata_size[2] : 0;
								$height = $exdata_size[3] != '' ? $exdata_size[3] : 0;
								$flap = $exdata_size[4] != '' ? $exdata_size[4] : 0;
								$gusset = $exdata_size[5] != '' ? $exdata_size[5] : 0;
								$thickness = $exdata_size[6];
								$meagurement_id = $exdata_size[7];
								$data_array7[$size_update_id]=explode("*",($length."*".$width."*".$height."*".$flap."*".$gusset."*'".$thickness."'*".$meagurement_id));
								$size_update_id_arr[] = $size_update_id;
							}
							
						}
					}	
				}
				else
				{
				 //echo "10**".$variable_bill_prod_delv."eeee"; die;
				
				$field_array="ready_to_approved*buyer_tb*remarks*status*updated_by*update_date";
				$field_array2="rate*amount*rate_domestic*amount_domestic*updated_by*update_date*plan_cut";
				//$field_array4="rate*amount*updated_by*update_date*excess_cut*plan_cut*plan_cut_amount";
				$field_array4="order_id*book_con_dtls_id*description*color_id*size_id*qnty*rate*amount*booked_qty*style*updated_by*update_date*excess_cut*plan_cut*plan_cut_amount*ply*size_name*gmts_color_id*gmts_size_id";
				$data_array="'".$cbo_ready_to_approved."'*'".$txt_buyer_tb."'*'".$txt_remarks."'*'".$cbo_Status_type."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
                $field_array7="length*width*height*flap*gusset*thickness*measurementid";

                $field_array_status="updated_by*update_date*status_active*is_deleted";
				$data_array_status=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
				for($i=1; $i<=$total_row; $i++)
				{
					$hdnDtlsdata 			= "hdnDtlsdata_".$i;
                    $sizedtlsdata 		    = "sizedtlsdata_".$i;
					$txtRate 				= "txtRate_".$i;
					$txtAmount 				= "txtAmount_".$i;			
					$txtOrderDeliveryDate 	= "txtOrderDeliveryDate_".$i;
					$txtDomRate 			= "txtDomRate_".$i;
					$txtDomamount 			= "txtDomamount_".$i;
					$hdnDtlsUpdateId 		= "hdnDtlsUpdateId_".$i;
					$txtPlaneCut 	     	= "txtPlaneCut_".$i;
					$txtConvFactor 			= "txtConvFactor_".$i;
					$hdn_dtls_id_arr[]=str_replace("'",'',$$hdnDtlsUpdateId);
					$aa	=str_replace("'",'',$$hdnDtlsUpdateId);
					$dtls_data=explode("***",str_replace("'",'',$$hdnDtlsdata));
					$dtls_data_size=explode("***",str_replace("'",'',$$sizedtlsdata));

					$data_array2[$aa]=explode("*",("".$$txtRate."*".str_replace(",",'',$$txtAmount)."*".$$txtDomRate."*".$$txtDomamount."*".$user_id."*'".$pc_date_time."'*".$$txtPlaneCut.""));
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
						$style=$exdata[10];
						$excess_cut= "'".$exdata[11]."'";
						$plan_cut=$exdata[12];
						$booked_qty=str_replace(",",'',$exdata[12])*str_replace("'",'',$$txtConvFactor);
						$plan_cut_amount=$exdata[13];
						$description=str_replace($str_rep,' ',$description);
                        $ply="'".$exdata[14]."'";
						$gmts_color_id="'".$exdata[17]."'";
						$gmts_size_id="'".$exdata[18]."'";
                        $exdata_size = [];
                        if($dtls_data_size[$j] != "") {
                            $exdata_size = explode("_", $dtls_data_size[$j]);
                        }
                        $size_update_id = $exdata_size[0];
						if (str_replace("'", "", trim($colorname)) != "") {
							if (!in_array(str_replace("'", "", trim($colorname)),$new_array_color)){
								$color_id = return_id( str_replace("'", "", trim($colorname)), $color_library_arr, "lib_color", "id,color_name","255");
								$new_array_color[$color_id]=str_replace("'", "", trim($colorname));
							}
							else $color_id =  array_search(str_replace("'", "", trim($colorname)), $new_array_color);
						} else $color_id = 0;

						if(str_replace("'","",$sizename)!="" && $size_update_id == "")
						{ 
							if (!in_array(str_replace("'","",$sizename),$new_array_size))
							{
								$size_id = return_id( str_replace("'","",$sizename), $size_library_arr, "lib_size", "id,size_name","255"); 
								$new_array_size[$size_id]=str_replace("'","",$sizename);
							}
							else $size_id =  array_search(str_replace("'","",$sizename), $new_array_size); 
						}
						else $size_id=0;

						if($bb!=''){
							$data_array4[$bb]=explode("*",("".$hid_order_id."*".$book_con_dtls_id."*'".trim($description)."'*".$color_id."*".$size_id."*".$qty."*".$rate."*".$amount."*".$booked_qty."*'".$style."'*".$user_id."*'".$pc_date_time."'*".$excess_cut."*".$plan_cut."*".$plan_cut_amount."*".$ply."*".$sizename."*".$gmts_color_id."*".$gmts_size_id.""));
							$hdn_break_id_arr[]		=$bb;
						}
                        if($size_update_id!=''){
                            $length = $exdata_size[1] != '' ? $exdata_size[1] : 0;
                            $width = $exdata_size[2] != '' ? $exdata_size[2] : 0;
                            $height = $exdata_size[3] != '' ? $exdata_size[3] : 0;
                            $flap = $exdata_size[4] != '' ? $exdata_size[4] : 0;
                            $gusset = $exdata_size[5] != '' ? $exdata_size[5] : 0;
                            $thickness = $exdata_size[6];
                            $meagurement_id = $exdata_size[7];
                            $data_array7[$size_update_id]=explode("*",($length."*".$width."*".$height."*".$flap."*".$gusset."*'".$thickness."'*".$meagurement_id));
                            $size_update_id_arr[] = $size_update_id;
                        }
						
					}
				}
				
				}
			}
			$rID=sql_update("subcon_ord_mst",$field_array,$data_array,"id",$update_id,0);  
			if($rID && $flag==1) $flag=1; else $flag=0;
			if($data_array2!="" && $flag==1)
			{
				$rID2=execute_query(bulk_update_sql_statement( "subcon_ord_dtls", "id",$field_array2,$data_array2,$hdn_dtls_id_arr),1);
				if($rID2) $flag=1; else $flag=0;
			}
			if($data_array4!=""  && $flag==1)
			{
				$rID3=execute_query(bulk_update_sql_statement( "subcon_ord_breakdown", "id",$field_array4,$data_array4,$hdn_break_id_arr),1);
				if($rID3) $flag=1; else $flag=0;
			}

            if(count($data_array7) > 0  && $flag==1)
            {
                $rID7=execute_query(bulk_update_sql_statement( "subcon_ord_breakdown_size_info", "id",$field_array7,$data_array7,$size_update_id_arr),1);
                if($rID7) $flag=1; else $flag=0;
            }

			
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
		
		$next_process=return_field_value( "trims_job", "trims_job_card_mst"," entry_form=257 and $update_id=received_id and status_active=1 and is_deleted=0");
		if($next_process!=''){
			echo "20**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no)."**".$next_process;
			disconnect($con);
			die;
		}
		$job_no="'".$txt_job_no."'";
		$order_no="'".$txt_order_no."'";
		$flag=0;
		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		$rID=sql_update("subcon_ord_mst",$field_array,$data_array,"id",$update_id,1);
		if($rID) $flag=1; else $flag=0; 
		
		if($flag==1)
		{
			$rID1=sql_update("subcon_ord_dtls",$field_array,$data_array,"job_no_mst",$job_no,1);
			if($rID1) $flag=1; else $flag=0; 
			
		}   
		//$rID = sql_delete("subcon_ord_dtls","status_active*is_deleted*updated_by*update_date","0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'",'id',$update_id2,1);
		//echo "10**update wo_booking_mst set lock_another_process=0 where booking_no =".$order_no.""; die;
		if($flag==1)
		{
			//$rID2=execute_query( "delete from subcon_ord_breakdown where job_no_mst=$job_no",0);
			$rID2=sql_update("subcon_ord_breakdown",$field_array,$data_array,"job_no_mst",$job_no,1);
			if($rID2) $flag=1; else $flag=0; 
		}

		if(str_replace("'",'',$cbo_within_group)==1)
		{
			if($flag==1)
			{
				$rID3=execute_query( "update wo_booking_mst set lock_another_process=0 where booking_no =$order_no",1);
				if($rID3) $flag=1; else $flag=0; 
			} 
		}
		//echo "10**".$rID."**".$rID1."**".$rID2."**".$rID3."**".$flag; die;
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
	return $strQuery ; die;
	global $con;
	if( strpos($strQuery, "WHERE")==false)  return "0";
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
        <table width="1040" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead> 
                <tr>
                    <th colspan="8"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </tr>
                <tr>               	 
                    <th width="140" class="must_entry_caption">Company Name</th>
                    <th width="100">Within Group</th>                           
                    <th width="140">Party Name</th>
                     <th width="100">Section</th>
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
                   		<? echo create_drop_down( "cboSection", 90, $trims_section,"", 1, "-- Select Section --","","",0,'','','','','','',"cboSection[]"); ?>
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
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cboSection').value, 'create_job_search_list_view', 'search_div', 'trims_order_receive_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
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
	if($data[9]!=0) $section=" and b.section='$data[9]'";
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
	if($db_type==0) $id_cond="group_concat(b.id) as id";
	else if($db_type==2) $id_cond="rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') as id";

	//echo "select $id_cond as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
	if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
	{
		$po_ids = return_field_value("$id_cond", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
	}
	//echo $po_ids;
	if($db_type==2 && $po_ids!="") $po_ids = $po_ids->load();
	if ($po_ids!="")
	{	//echo $po_ids;
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
				}
				else
				{
					$po_idsCond.=" or  b.buyer_po_id in ( $ids) ";
				}
			}
			$po_idsCond.=")";
		}
		else
		{
			$ids=implode(",",$po_ids);
			$po_idsCond.=" and b.buyer_po_id in ($ids) ";
		}
	}
	else if($po_ids=="" && ($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
	{
		die;
		//$po_idsCond.=" and b.buyer_po_id in ($ids) ";
	}
	//if ($po_ids!="") $po_idsCond=" and b.buyer_po_id in ($po_ids)"; else $po_idsCond="";
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$trim_group_arr=return_library_array( "select id, item_name from lib_item_group where item_category=4",'id','item_name');
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
		$color_id_str=" ,rtrim(xmlagg(xmlelement(e,c.color_id,',').extract('//text()') order by c.color_id).GetClobVal(),',') as color_id";
		
		if($within_group==1)
		{
			$buyer_po_id_str=" ,rtrim(xmlagg(xmlelement(e,b.buyer_po_id,',').extract('//text()') order by b.id).GetClobVal(),',') as buyer_po_id";
		}
		else
		{
			$buyer_po_no_str=" ,rtrim(xmlagg(xmlelement(e,b.buyer_po_no,',').extract('//text()') order by b.id).GetClobVal(),',') as buyer_po_no";
			$buyer_po_style_str=" ,rtrim(xmlagg(xmlelement(e,b.buyer_style_ref,',').extract('//text()') order by b.id).GetClobVal(),',') as buyer_style";
		}
	}

	$sql= "select a.id, a.subcon_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date, b.item_group $buyer_po_no_str $buyer_po_style_str $color_id_str $buyer_po_id_str,b.section
	from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
	where a.entry_form=255 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 $order_rcv_date $company $buyer $withinGroup $search_com_cond $po_idsCond $withinGroup and b.id=c.mst_id  $year_cond  $party_id_cond $section
	group by a.id, a.subcon_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date, b.item_group,b.section
	order by a.id DESC";
	//echo $sql;
	$data_array=sql_select($sql);
	//echo "<pre>";
	//print_r($data_array);
	?>
    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="985" >
        <thead>
            <th width="30">SL</th>
            <th width="60">Job No</th>
            <th width="60">Year</th>
            <th width="120">W/O No</th>
            <th width="100">Buyer Po</th>
            <th width="100">Buyer Style</th>
            <th width="80">Ord Receive Date</th>
            <th width="80">Delivery Date</th>
            <th width="80">Section</th>
            <th width="100">Trims Group</th>
            <th>Color</th>
        </thead>
        </table>
        <div style="width:985px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="965" class="rpt_table" id="tbl_po_list">
        <tbody>
            <? 
            $i=1; 
            foreach($data_array as $row)
            {  
            	$color_ids =$buyer_po_ids =$buyer_po_nos =$buyer_styles ='';
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                $color_ids = $row[csf('color_id')]->load();
               
                if($within_group!=1)
				{
					$buyer_po_nos = $row[csf('buyer_po_no')]->load();
                	$buyer_styles = $row[csf('buyer_style')]->load();
				}
				else
				{
					$buyer_po_ids = $row[csf('buyer_po_id')]->load();
				}

				$excolor_id=array_unique(explode(",",$color_ids));
				$color_name="";	
				foreach ($excolor_id as $color_id)
				{
					if($color_name=="") $color_name=$color_arr[$color_id]; else $color_name.=','.$color_arr[$color_id];
				}
				if($within_group==1)
				{
					$buyer_po=""; $buyer_style="";
					$buyer_po_id=explode(",",$buyer_po_ids);
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
					$buyer_po=implode(",",array_unique(explode(",",$buyer_po_nos)));
					$buyer_style=implode(",",array_unique(explode(",",$buyer_styles)));
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
                    <td width="80" style="text-align:center;"><? echo $trims_section[$row[csf('section')]]; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $trim_group_arr[$row[csf('item_group')]]; ?></td>
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
	$nameArray=sql_select( "select id, subcon_job, company_id, location_id, party_id, currency_id, party_location, delivery_date, rec_start_date, rec_end_date, receive_date, within_group, party_location, order_id, delivery_point, order_no, exchange_rate, team_leader,team_member, team_marchant, trims_ref, ready_to_approved, remarks, is_apply_last_update, revise_no, approved,status,buyer_tb,buying_merchant,payterm_id,wo_type,party_wise_grade from subcon_ord_mst where subcon_job='$data' and entry_form=255 and status_active=1" );
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_job_no').value 				= '".$row[csf("subcon_job")]."';\n";  
		echo "document.getElementById('cbo_company_name').value 		= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_within_group').value 		= '".$row[csf("within_group")]."';\n";  
		echo "document.getElementById('cbo_payterm_id').value 		= '".$row[csf("payterm_id")]."';\n";  
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
		echo "document.getElementById('cbo_Status_type').value         	= '".$row[csf("status")]."';\n";
		echo "document.getElementById('txt_trims_ref').value         	= '".$row[csf("trims_ref")]."';\n";
		echo "document.getElementById('txt_delivery_point').value       = '".$row[csf("delivery_point")]."';\n";
		echo "document.getElementById('txt_buyer_tb').value             = '".$row[csf("buyer_tb")]."';\n";
		echo "load_drop_down( 'requires/trims_order_receive_controller', document.getElementById('cbo_team_leader').value, 'load_drop_down_member', 'member_td' );\n";
		echo "document.getElementById('cbo_team_member').value         	= '".$row[csf("team_member")]."';\n";
		echo "document.getElementById('cbo_wo_type_id').value         	= '".$row[csf("wo_type")]."';\n";
		echo "document.getElementById('cbo_party_wise_grade').value         	= '".$row[csf("party_wise_grade")]."';\n";
		echo "document.getElementById('txt_fac_merchan').value         	= '".$row[csf("team_marchant")]."';\n";
		echo "document.getElementById('txt_buying_merchant').value         	= '".$row[csf("buying_merchant")]."';\n";
		echo "document.getElementById('cbo_ready_to_approved').value    = '".$row[csf("ready_to_approved")]."';\n";
		echo "document.getElementById('is_approved_id').value 			= '".$row[csf("approved")]."';\n";
		if($row[csf("approved")]==1)
		{
		  echo "$('#approved_msg').text('Approved');\n";
		}
	  	else if($row[csf("approved")]==3)
		{
		  echo "$('#approved_msg').text('Partial Approved');\n";
		}
		else
		{
		   echo "$('#approved_msg').text('');\n";
		}
		echo "document.getElementById('txt_remarks').value         		= '".$row[csf("remarks")]."';\n";
		echo "$('#txt_order_no').attr('disabled','true')".";\n";
		echo "$('#cbo_within_group').attr('disabled','true')".";\n";
		echo "$('#cbo_party_name').attr('disabled','true')".";\n";
		echo "$('#cbo_currency').attr('disabled','true')".";\n";
		echo "document.getElementById('update_id').value          		= '".$row[csf("id")]."';\n";	
		echo "document.getElementById('is_apply_last_update').value     = '".$row[csf("is_apply_last_update")]."';\n";	
		echo "document.getElementById('txt_revise_no').value     		= '".$row[csf("revise_no")]."';\n";	

		$image_data=sql_select("select master_tble_id from common_photo_library where master_tble_id='".$row[csf("id")]."' and form_name='trims_order_receive' and file_type=2 and is_deleted=0");
		if (count($image_data)) {
			echo "document.getElementById('image_location_id').value = '".$image_data[0][csf("master_tble_id")]."';\n";
			echo "document.getElementById('txt_is_file_uploaded').value = 1;\n";
		}

		if($row[csf("status")]==3){
            echo "$('#refusing_cause').text('CANCELLED ORDER');\n";
        }
		
		


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

	function fnc_spo_data(is_checked)
	{
		if(is_checked==true){
			$("#txt_allow_spo").val(1);
		}else{
			$("#txt_allow_spo").val(0);
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
                        <th colspan="5" align="center">
                            <? echo create_drop_down( "cbo_search_category", 110, $string_search_type,'', 1, "-- Search Catagory --" ); ?>
                        </th>
                        <th colspan="2" align="center"> <input type="checkbox" name="allow_spo" id="allow_spo" onClick="fnc_spo_data(this.checked)" /> Allow SPO
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
                            <input type="hidden" id="txt_allow_spo" value="0">
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
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_search_category').value+'_'+<? echo $company;?>+'_'+document.getElementById('cbo_search_type').value+'_'+document.getElementById('txt_allow_spo').value+'_'+<? echo $cbo_within_group; ?>, 'create_booking_search_list_view', 'search_div', 'trims_order_receive_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
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
	$master_company=$data[6];
	$search_type=$data[7];
	$allow_spo=$data[8];
	$cbo_within_group=$data[9];
	//echo $cbo_within_group ; die;
	
	if ($allow_spo==1) {
		if ($data[0]!=0 ) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Party First."; die; }
	}else{
		if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Party First."; die; }
		if ($data[0]!=0) $sample_company=" and c.company_id='$data[0]'"; else { echo "Please Select Party First."; die; }
	}
	
	if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[4]"; } else if($db_type==2) { $year_cond=" and to_char(a.insert_date,'YYYY')=$data[4]"; }
	if($db_type==0) { $sample_year_cond=" and YEAR(c.insert_date)=$data[4]"; } else if($db_type==2) { $sample_year_cond=" and to_char(c.insert_date,'YYYY')=$data[4]"; }
	if ($master_company!=0) $supplier_cond=" and a.supplier_id=$master_company"; else $supplier_cond="";
	
	//echo $supplier_cond; die;
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
			if ($search_type==1) $wo_cond=" and a.wo_number = '$data[1]' ";
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
			if ($search_type==1) $wo_cond=" and a.wo_number = '$data[1]%' ";
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
			if ($search_type==1) $wo_cond=" and a.wo_number like '%$data[1]' ";
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
			if ($search_type==1) $wo_cond=" and a.wo_number like '%$data[1]%' ";
		}
	}
	
	$po_ids='';
	
	if($db_type==0) $id_cond="group_concat(b.id)";
	else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	if(($job_cond!="" && $search_type==2) || ($style_cond!="" && $search_type==3)|| ($po_cond!="" && $search_type==4))
	{
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.id=b.job_id $job_cond $style_cond $po_cond", "id");
		if ($po_ids=="")
		{
			$po_idsCond="";
			echo "Not Found."; die;
		}
	}
	
	if ($po_ids!="") $po_idsCond=" and b.po_break_down_id in ($po_ids)"; else $po_idsCond="";
	if ($po_ids!="") $sample_po_idsCond=" and c.po_break_down_id in ($po_ids)"; else $sample_po_idsCond="";
	$buyer_po_arr=array();
	$po_sql ="Select a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.is_deleted=0 and b.is_deleted=0";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		$buyer_po_arr[$row[csf("id")]]['buyer']=$row[csf("buyer_name")];
		$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no")];
	}
	unset($po_sql_res);

	$approved_cond="";
	if ($cbo_within_group==1)
	{
		if ($db_type==2) $app_nes_setup_date=change_date_format(date('d-m-Y'), "", "",1);
		else if ($db_type==0) $app_nes_setup_date=change_date_format(date('d-m-Y'),'yyyy-mm-dd');
		$approval_status="select approval_need, allow_partial from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$data[0]' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '$app_nes_setup_date' and company_id='$data[0]')) and page_id=9 and status_active=1 and is_deleted=0";
		$app_need_setup=sql_select($approval_status);
		$approval_need=$app_need_setup[0][csf("approval_need")];
		
		if ($approval_need ==1) $approved_cond=" and a.is_approved in(1,3)";
	}	
	
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
		if($allow_spo==1)
		{
			if ($data[2]!="" &&  $data[3]!="") $wo_date = "and a.wo_date between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $wo_date ="";
		}
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
		if($allow_spo==1)
		{
			if ($data[2]!="" &&  $data[3]!="") $wo_date = "and a.wo_date between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $wo_date ="";
		}
	} 
	if($allow_spo==1)
	{
		$sql = " select  $wo_year as year, a.id,a.wo_number_prefix_num as wo_prefix, a.wo_number as booking_no ,a.company_name,a.buyer_po,a.wo_date as booking_date ,a.supplier_id,a.attention,a.wo_basis_id ,3 as type, a.item_category,a.currency_id,a.delivery_date,source,a.pay_mode from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id = b.mst_id and a.entry_form = 146 and a.status_active=1 and a.is_deleted=0 and a.pay_mode=5 $company $supplier_cond $wo_cond group by a.id,a.wo_number_prefix_num,a.wo_number,a.company_name,a.buyer_po,a.wo_date,a.supplier_id,a.attention,a.wo_basis_id,  a.item_category,a.currency_id,a.delivery_date,source,a.pay_mode, a.insert_date  order by a.id";
	}
	else
	{
		$sql= "SELECT $wo_year as year, a.id, a.booking_type, a.booking_no, a.booking_no_prefix_num as wo_prefix, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id, $pre_cost_trims_cond as pre_cost_trims_id, $po_id_cond as po_id ,1 as type from  wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type in(2,5) and a.status_active=1 and a.lock_another_process!=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $booking_date $company $supplier_cond $woorder_cond $year_cond $po_idsCond $approved_cond group by a.insert_date, a.id, a.booking_type, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id 
		UNION
		SELECT $sample_wo_year as year, c.id, c.booking_type, c.booking_no, c.booking_no_prefix_num as wo_prefix, c.company_id, c.buyer_id, c.job_no, c.booking_date, c.currency_id, $sample_pre_cost_trims_cond as pre_cost_trims_id, $sample_po_id_cond as po_id ,2 as type from  wo_non_ord_samp_booking_mst c, wo_non_ord_samp_booking_dtls d where c.booking_no=d.booking_no and c.booking_type in(5) and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $sample_booking_date $sample_company $sample_woorder_cond $sample_year_cond $sample_po_idsCond group by c.insert_date, c.id, c.booking_type, c.booking_no, c.booking_no_prefix_num, c.company_id, c.buyer_id, c.job_no, c.booking_date, c.currency_id";
	}
	//echo $sql;
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (0=>$comp,1=>$buyer_arr);
	

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
            {  //echo $row[csf('po_id')];
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
                    <td width="60" align="center"><? echo $row[csf('wo_prefix')]; ?></td>
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

if( $action=='order_dtls_list_view' ) 
{
	//echo $data; die;
	$data=explode('_',$data);
	$round_type=$data[5];

	$color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name" );
	$size_arr=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0",'id','size_name');
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
	
	$bookConsIds_arr=array(); $subcon_brk_id = array();
	if($data[2]==1 && $data[0]==2 )
	{
		//ho "select id, book_con_dtls_id,mst_id from subcon_ord_breakdown where job_no_mst='$data[3]'"; die;
		$sql_cons=sql_select("select id, book_con_dtls_id, mst_id, style, excess_cut, plan_cut, plan_cut_amount, ply, size_name, size_id from subcon_ord_breakdown where status_active=1 and is_deleted=0 and job_no_mst='$data[3]'");
		foreach($sql_cons as $row)
		{
			$bookConsIds_arr[$row[csf('book_con_dtls_id')]]['id']=$row[csf('id')];
			$bookConsIds_arr[$row[csf('book_con_dtls_id')]]['excess_cut']=$row[csf('excess_cut')];
			$bookConsIds_arr[$row[csf('book_con_dtls_id')]]['plan_cut']=$row[csf('plan_cut')];
			$bookConsIds_arr[$row[csf('book_con_dtls_id')]]['plan_cut_amount']=$row[csf('plan_cut_amount')];
			$bookConsIds_arr[$row[csf('book_con_dtls_id')]]['style']=$row[csf('style')];
			$bookConsIds_arr[$row[csf('book_con_dtls_id')]]['ply']=$row[csf('ply')];
			$bookConsIds_arr[$row[csf('book_con_dtls_id')]]['sizename']=$row[csf('size_name')];
			$bookConsIds_arr[$row[csf('book_con_dtls_id')]]['sizeid']=$row[csf('size_id')];
            array_push($subcon_brk_id, $row[csf('id')]);
		}
		unset($sql_cons);
	}
	$job_no_mst=$row[csf('job_no_mst')];
	if($data[2]==1)
	{
		//echo "select  id as book_con_dtls_id, wo_trim_booking_dtls_id, job_no,  po_break_down_id,  item_color as color_id, item_size as size_id, requirment as qnty, description, brand_supplier, rate, amount from wo_trim_book_con_dtls where booking_no='$data[1]' and status_active=1 and is_deleted=0 and requirment!=0 order by id ASC";
		//echo "select  id as book_con_dtls_id, wo_trim_booking_dtls_id, job_no,  po_break_down_id,  item_color as color_id, item_size as size_id, requirment as qnty, description, brand_supplier, rate, amount from wo_trim_book_con_dtls where booking_no='$data[1]' and status_active=1 and is_deleted=0 and requirment!=0 order by id ASC"; die;
		$qry="select  id as book_con_dtls_id, wo_trim_booking_dtls_id, job_no,  po_break_down_id,  color_number_id as gmts_color_id, gmts_sizes as gmts_size_id, item_color as color_id, item_size as size_id, requirment as qnty, description, brand_supplier, rate, amount from wo_trim_book_con_dtls where booking_no='$data[1]' and status_active=1 and is_deleted=0 and requirment!=0 order by id ASC";
		$qry_result = sql_select($qry);
		$data_dreak_arr=array(); $data_dreak=''; $add_comma=0; $k=1;
		foreach ($qry_result as $rows)
		{
			if($rows[csf('description')]=="") $rows[csf('description')]=0;
			if($rows[csf('gmts_color_id')]=="") $rows[csf('gmts_color_id')]=0;
			if($rows[csf('gmts_size_id')]=="") $rows[csf('gmts_size_id')]=0;
			if($rows[csf('color_id')]=="") $rows[csf('color_id')]=0;
			if($rows[csf('size_id')]=="") $rows[csf('size_id')]=0;
			if($rows[csf('qnty')]=="") $rows[csf('qnty')]=0;
			if($rows[csf('rate')]=="") $rows[csf('rate')]=0;
			if($rows[csf('amount')]=="") $rows[csf('amount')]=0;
			if($rows[csf('book_con_dtls_id')]=="") $rows[csf('book_con_dtls_id')]=0;
			if(!in_array($rows[csf('mst_id')],$temp_arr_mst_id))
			{
				$temp_arr_mst_id[]=$rows[csf('mst_id')];
				$add_comma=0; $data_dreak='';
			}
			$k++;
			if($bookConsIds_arr[$rows[csf('book_con_dtls_id')]]['id']=="") $bookConsIds_arr[$rows[csf('book_con_dtls_id')]]['id']=0;

            if( $data[0]==1) 
			{
                $excess_cut = 0;
                $plan_cut = $rows[csf('qnty')];
                $plan_cut_amount = $rows[csf('amount')];
				$style=$buyer_po_arr[$rows[csf('po_break_down_id')]]['style'];
				$sizename=$rows[csf('size_id')];			
            }
			else
			{
                $excess_cut = $bookConsIds_arr[$rows[csf('book_con_dtls_id')]]['excess_cut'];
                $plan_cut = $bookConsIds_arr[$rows[csf('book_con_dtls_id')]]['plan_cut'];
                $plan_cut_amount = $bookConsIds_arr[$rows[csf('book_con_dtls_id')]]['plan_cut_amount'];
				$style=$bookConsIds_arr[$rows[csf('book_con_dtls_id')]]['style'];
				$sizeid=$bookConsIds_arr[$rows[csf('book_con_dtls_id')]]['sizeid'] > 0 ? $bookConsIds_arr[$rows[csf('book_con_dtls_id')]]['sizeid']: 0 ;
				$sizename = "";
				if( $sizeid > 0)
					$sizename = $size_arr[$sizeid];
				else
                $sizename=$bookConsIds_arr[$rows[csf('book_con_dtls_id')]]['sizename'];
				$breakDownUpId=$bookConsIds_arr[$rows[csf('book_con_dtls_id')]]['id'];
				$ply=$bookConsIds_arr[$rows[csf('book_con_dtls_id')]]['ply'];
            }
			//$style=$bookConsIds_arr[$rows[csf('book_con_dtls_id')]]['style'];

			$data_dreak_arr[$rows[csf('wo_trim_booking_dtls_id')]].=$rows[csf('description')].'_'.$color_library[$rows[csf('color_id')]].'_'.$sizename.'_'.$rows[csf('qnty')].'_'.$rows[csf('rate')].'_'.$rows[csf('amount')].'_'.$rows[csf('book_con_dtls_id')].'_'.$breakDownUpId.'_'.$rows[csf('is_revised')].'_'.$sizeid.'_'.$style.'_'.$excess_cut.'_'.$plan_cut.'_'.$plan_cut_amount.'_'.$ply.'_'.$color_library[$rows[csf('gmts_color_id')]].'_'.$size_arr[$rows[csf('gmts_size_id')]].'_'.$rows[csf('gmts_color_id')].'_'.$rows[csf('gmts_size_id')].'#';
        }
	}
	else
	{
		$qry_result=sql_select( "select id, mst_id, order_id, job_no_mst, book_con_dtls_id, description, color_id, size_id, qnty, rate, amount, is_revised, style, excess_cut, plan_cut, plan_cut_amount, ply, size_name, gmts_color_id, gmts_size_id from subcon_ord_breakdown where status_active=1 and is_deleted=0 and job_no_mst='$data[3]' order by id");
	
		$data_dreak_arr=array(); $data_dreak=''; $add_comma=0; $k=1;
		foreach ($qry_result as $rows)
		{
			if($rows[csf('description')]=="") $rows[csf('description')]=0;
			if($rows[csf('color_id')]=="") $rows[csf('color_id')]=0;
			if($rows[csf('size_id')]=="") $rows[csf('size_id')]=0;
			if($rows[csf('gmts_color_id')]=="") $rows[csf('gmts_color_id')]=0;
			if($rows[csf('gmts_size_id')]=="") $rows[csf('gmts_size_id')]=0;
			if($rows[csf('qnty')]=="") $rows[csf('qnty')]=0;
			if($rows[csf('rate')]=="") $rows[csf('rate')]=0;
			if($rows[csf('amount')]=="") $rows[csf('amount')]=0;
			if($rows[csf('book_con_dtls_id')]=="") $rows[csf('book_con_dtls_id')]=0;
			if($rows[csf('style')]=="") $rows[csf('style')]=0;
			if($rows[csf('excess_cut')]=="") $rows[csf('excess_cut')]=0;
			if($rows[csf('plan_cut')]=="") $rows[csf('plan_cut')]=0;
			if($rows[csf('plan_cut_amount')]=="") $rows[csf('plan_cut_amount')]=0;
			if(!in_array($rows[csf('mst_id')],$temp_arr_mst_id))
			{
				$temp_arr_mst_id[]=$rows[csf('mst_id')];
				$add_comma=0; $data_dreak='';
			}
			$k++;
            array_push($subcon_brk_id, $rows[csf('id')]);
            if($bookConsIds_arr[$rows[csf('book_con_dtls_id')]]=="") $bookConsIds_arr[$rows[csf('book_con_dtls_id')]]=0;
			$data_dreak_arr[$rows[csf('mst_id')]].=$rows[csf('description')].'_'.$color_library[$rows[csf('color_id')]].'_'.$rows[csf('size_name')].'_'.$rows[csf('qnty')].'_'.$rows[csf('rate')].'_'.$rows[csf('amount')].'_'.$rows[csf('book_con_dtls_id')].'_'.$rows[csf('id')].'_'.$rows[csf('is_revised')].'_'.$rows[csf('size_id')].'_'.$rows[csf('style')].'_'.$rows[csf('excess_cut')].'_'.$rows[csf('plan_cut')].'_'.$rows[csf('plan_cut_amount')].'_'.$rows[csf('ply')].'_'.$color_library[$rows[csf('gmts_color_id')]].'_'.$size_arr[$rows[csf('gmts_size_id')]].'_'.$rows[csf('gmts_color_id')].'_'.$rows[csf('gmts_size_id')].'#';
        }
	}


   /*  echo "<pre>";
    print_r($data_dreak_arr); */
    $sizeInfoData = array();
    if(count($subcon_brk_id) > 0) {
        $subcon_brk_id = array_chunk(array_unique($subcon_brk_id), 999);
        $id_cond_for_size_dtls = "";
        foreach ($subcon_brk_id as $key => $value) {
            if($key == 0)
                $id_cond_for_size_dtls .= " and b.subconordbreakdownid in (".implode(',', $value).")";
            else
                $id_cond_for_size_dtls .= " or b.subconordbreakdownid in (".implode(',', $value).")";
        }
        $sql_get_size_info = sql_select("SELECT a.id, b.id as sizeinfoid, a.mst_id, b.length, b.width, b.height, b.flap, b.gusset, b.thickness, b.measurementid from subcon_ord_breakdown a, subcon_ord_breakdown_size_info b where a.id=b.subconordbreakdownid and a.status_active = 1 and a.is_deleted = 0 $id_cond_for_size_dtls order by b.subconordbreakdownid asc");
        if(count($sql_get_size_info) > 0){
            foreach($sql_get_size_info as $sizeinfo){
                $sizeInfoData[$sizeinfo[csf('mst_id')]] .= $sizeinfo[csf('sizeinfoid')].'_'.$sizeinfo[csf('length')].'_'.$sizeinfo[csf('width')].'_'.$sizeinfo[csf('height')].'_'.$sizeinfo[csf('flap')].'_'.$sizeinfo[csf('gusset')].'_'.$sizeinfo[csf('thickness')].'_'.$sizeinfo[csf('measurementid')].'_'.$sizeinfo[csf('id')]."***";
            }
        }
    }
	if($data[2]==1 && $data[0]==1 )
	{
		//echo $data[4].'=='; 
		if(trim($data[4])==1)
		{
			$sql = "SELECT  a.id, a.booking_type, a.booking_no, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id, b.id as booking_dtls_id, b.po_break_down_id, b.trim_group,b.delivery_date,b.fabric_description, b.uom, b.wo_qnty, b.rate, b.amount,1 as source_for_order, d.section
			from  wo_booking_mst a, wo_booking_dtls b, wo_trim_book_con_dtls c, lib_item_group d where a.booking_no=b.booking_no and a.booking_type=2 and c.wo_trim_booking_dtls_id=b.id and b.trim_group=d.id and c.requirment>0 and  b.booking_no=trim('$data[1]') and a.status_active=1 and a.lock_another_process!=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.booking_type, a.booking_no, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id, b.id, b.po_break_down_id, b.trim_group, b.delivery_date,b.fabric_description, b.uom, b.wo_qnty, b.rate, b.amount, d.section order by b.id ASC";
		}
		else if(trim($data[4])==2)
		{
			$sql = "SELECT  a.id, a.booking_type, a.booking_no, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id, b.id as booking_dtls_id, a.po_break_down_id, b.trim_group,b.delivery_date,b.fabric_description, b.gmts_color, b.fabric_color, b.gmts_size, b.item_size, b.uom, b.trim_qty as wo_qnty, b.rate, b.amount,1 as source_for_order, c.section
			from  wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, lib_item_group c where a.booking_no=b.booking_no and b.trim_group=c.id and a.booking_type=5  and a.booking_no=trim('$data[1]') and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.booking_type, a.booking_no, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id, b.id, a.po_break_down_id, b.trim_group, b.delivery_date,b.fabric_description, b.gmts_color, b.fabric_color, b.gmts_size, b.item_size, b.uom, b.trim_qty, b.rate, b.amount, c.section order by b.id ASC";
		}
		else
		{
			$sql = " SELECT  a.id, a.wo_number as booking_no ,a.company_name as company_id,b.buyer_id,b.job_no,b.style_no,a.wo_date as booking_date,a.currency_id, b.id as booking_dtls_id,0 as po_break_down_id,c.item_group_id as trim_group,a.delivery_date ,c.item_description as fabric_description ,b.uom,sum(b.supplier_order_quantity) as wo_qnty , sum(b.amount) as amount,1 as source_for_order, d.section from wo_non_order_info_mst a, wo_non_order_info_dtls b, product_details_master c, lib_item_group d where a.id = b.mst_id and b.item_id=c.id and c.item_group_id=d.id and a.entry_form = 146 and a.status_active=1 and a.is_deleted=0 and a.pay_mode=5 and a.wo_number=trim('$data[1]') group by a.id, a.wo_number ,a.company_name,b.buyer_id,b.job_no,b.style_no,a.wo_date ,a.currency_id, b.id as booking_dtls_id,0 as po_break_down_id,c.item_group_id as trim_group,a.delivery_date ,c.item_description as fabric_description ,b.uom, d.section  order by a.id";
			//, wo_non_ord_samp_yarn_dtls c and c.wo_non_ord_samp_book_dtls_id=b.id
		}
	}
	else if($data[2]==1 && $data[0]==2 )
	{
		$sql = "SELECT id, mst_id, job_no_mst, order_id, order_no, buyer_po_id as po_break_down_id, booking_dtls_id, order_quantity as wo_qnty, plan_cut, order_uom, rate, amount, submit_date, approve_date, delivery_date, buyer_po_no, buyer_style_ref, buyer_buyer, section, sub_section, item_group as trim_group, rate_domestic,  amount_domestic, is_with_order, booked_uom, booked_conv_fac, booked_qty, is_revised,source_for_order from subcon_ord_dtls where order_no='$data[1]' and job_no_mst='$data[3]' and status_active=1 and is_deleted=0 order by id ASC";
	}
	else
	{
		$sql = "SELECT id, mst_id, job_no_mst, order_id, order_no, buyer_po_id as po_break_down_id, booking_dtls_id, order_quantity as wo_qnty, plan_cut, order_uom, rate, amount, submit_date, approve_date, delivery_date, buyer_po_no, buyer_style_ref, buyer_buyer, section,sub_section, item_group as trim_group, rate_domestic,  amount_domestic , is_with_order, booked_uom, booked_conv_fac, booked_qty,is_revised,source_for_order from subcon_ord_dtls where job_no_mst='$data[3]' and status_active=1 and is_deleted=0 order by id ASC";
	}
	//echo $sql; //die; 
	$data_array=sql_select($sql); $del_date_arr=array();
	//echo $sql; //die; 
	if($data[0]==2 )
	{
		$mst_id=$data[4];
		$delivery_found=return_field_value( "received_id", "trims_delivery_dtls"," received_id=$mst_id and status_active=1 and is_deleted=0");
	}
	
 	
	/* $sql = "select id, work_order_number_control from variable_setting_trim_marketing where company_name=$data[6] and status_active=1 and is_deleted=0 and variable_list=3"; 
	 $sectionvariable = sql_select($sql);
 	 $variable_bill_prod_delv=$sectionvariable[0][csf('work_order_number_control')];
 	// echo $variable_bill_prod_delv; 1==bill 2=prod 3=delv
  	$bil_qty_arr=array();  
 	$bill_po_sql = sql_select("select c.mst_id as job_dtls_id,a.quantity as bil_quantity,b.delevery_qty  from trims_bill_dtls a,trims_delivery_dtls b ,subcon_ord_breakdown c   where a.production_dtls_id=b.id and c.id=b.break_down_details_id and   b.received_id=$mst_id   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
  	foreach($bill_po_sql as $row)
	{
		$bil_qty_arr[$row[csf('job_dtls_id')]]['bil_quantity']+=$row[csf('bil_quantity')];
		$bil_qty_arr[$row[csf('job_dtls_id')]]['delevery_qty']+=$row[csf('delevery_qty')];
 	}
 	 */

	 
	  
	  
	//receive_dtls_ids
	//die;
	//////////////////////'
	
	//print_r($data_array);
	$ind=0;
	if(count($data_array) > 0)
	{
		$exchange_rate=$data[3];
		$min_date=0; 
		foreach($data_array as $row)
		{
            $size_break="";
			$tblRow++;
			$dtls_id=0; $order_uom=0; $wo_qnty=0; $disabled_conv=''; $plan_cut=0;
			
			if($data[2]==1)  //within group yes 
			{
                $wo_qnty=$row[csf('wo_qnty')];
				if($data[0]==1)
				{
					$order_uom=$row[csf('uom')];
					$del_date_arr[$ind++]=  $row[csf('delivery_date')] ;
                    $plan_cut=$wo_qnty;

                }
				else
				{
					$order_uom=$row[csf('order_uom')];
					$dtlsID=$row[csf('id')];
                    $plan_cut=$row[csf('plan_cut')];

                }

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
						if($data_break=="") $data_break.=$row[csf('fabric_description')].'_'.$color_library[$row[csf('fabric_color')]].'_'.$size_arr[$row[csf('item_size')]].'_'.$row[csf('wo_qnty')].'_'.$row[csf('rate')].'_'.$row[csf('amount')].'_'.$row[csf('id')].'_'.$break_down_arr[$row[csf('id')]].'_'.$color_library[$row[csf('fabric_color')]].'_'.$size_arr[$row[csf('item_size')]].'_'.$row[csf('gmts_color')].'_'.$row[csf('gmts_size')];
						else $data_break.='***'.$row[csf('fabric_description')].'_'.$color_library[$row[csf('fabric_color')]].'_'.$size_arr[$row[csf('item_size')]].'_'.$row[csf('wo_qnty')].'_'.$row[csf('rate')].'_'.$row[csf('amount')].'_'.$row[csf('id')].'_'.$break_down_arr[$row[csf('id')]].'_'.$color_library[$row[csf('fabric_color')]].'_'.$size_arr[$row[csf('item_size')]].'_'.$row[csf('gmts_color')].'_'.$row[csf('gmts_size')];
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
						$data_break="";
						$data_break.=implode("***",array_filter(explode('#',$data_dreak_arr[$row[csf('booking_dtls_id')]])));
					}
				}
				else if($data[0]==2)
				{
					$buyerpo=$buyer_po_arr[$row[csf('po_break_down_id')]]['buyerpo'];
					$style=$buyer_po_arr[$row[csf('po_break_down_id')]]['style'];
					$buyer_buyer=$buyer_po_arr[$row[csf('po_break_down_id')]]['buyer_name'];
					$break_down_id=$row[csf('po_break_down_id')];
					$booking_dtls_id=$row[csf('booking_dtls_id')];
					$disable_dropdown='1';
					$disabled='disabled';
					$data_break="";
					$data_break=implode("***",array_filter(explode('#',$data_dreak_arr[$row[csf('booking_dtls_id')]])));
				}
			}
			else if($data[2]==2)
			{
				if($data[0]==2)
				{
					$dtlsID=$row[csf('id')];
					//$dtlsID=$row[csf('id')];
					$row[csf("delivery_date")]=$row[csf('delivery_date')];
					$order_uom=$row[csf('order_uom')];
					$wo_qnty=$row[csf('wo_qnty')];
					$buyerpo=$row[csf('buyer_po_no')];
					$style=$row[csf('buyer_style_ref')];
					$buyer_buyer=$row[csf('buyer_buyer')];
					$plan_cut=$row[csf('plan_cut')];
					$break_down_id="";
					$data_break=implode("***",array_filter(explode('#',$data_dreak_arr[$dtlsID])));
                }
				else $wo_qnty=0;
			}
			//echo $data[4].'==';
			if($data[0]==1)
			{
				$domRate=$row[csf('rate')]*$exchange_rate; 
				$domAmount=$row[csf('amount')]*$exchange_rate;
				//$buyer_buyer='';
				
				$isWithOrder=$data[4];
				if($data[4]==1 && $isWithOrder==1)
				{
					$disabled='disabled';
					$disable_dropdown='1';
				}
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

			}
			$next_process=0;
			//echo $disable_dropdown.'++';
			if($delivery_found !='' ){
				//$next_proc_disabled ='disabled=disabled';
				$next_proc_disabled_dropdown='1';
				$next_process='1';
			}/*else if($disable_dropdown==1){
				$next_proc_disabled_dropdown='1';
			}*/else{
				$next_proc_disabled_dropdown='0';
			}
			if ($tblRow%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			if ($row[csf('is_revised')]==2)
			{
				$bgcolor="#ff2d00";
				//tr_disabled(echo $tblRow);
				//$revised_disabled='disabled';
				//$revised_disabled_drop='disabled';
			}
			//echo $delivery_found.'=='.$disable_dropdown.'=='.$next_proc_disabled_dropdown;
			//echo $data_break.'==';
			
			
			
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
                <td align="center"><input type="checkbox" value="<? echo $tblRow; ?>" class="rowCheck" id="rowCheck_<? echo $tblRow; ?>" onClick="rowSingleCheck()"></td>
                <td><input id="txtbuyerPo_<? echo $tblRow; ?>" name="txtbuyerPo[]" value="<? echo $buyerpo; ?>" class="text_boxes" type="text"  style="width:100px" <? echo $disabled ?> />
					<input id="txtbuyerPoId_<? echo $tblRow; ?>" name="txtbuyerPoId[]" value="<? echo $break_down_id; ?>" class="text_boxes" type="hidden" style="width:70px" readonly />
				</td>
				<td><input id="txtstyleRef_<? echo $tblRow; ?>" name="txtstyleRef[]" value="<? echo $style; ?>" class="text_boxes" type="text"  style="width:100px" <? echo $disabled ?> /></td>
				<td>
					<?
					//echo $data[2]."**".$isWithOrder ;
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

					if($row[csf('section')]==1) $subID='1,2,3,23,29,30,31,32,33,34,35,36,37,38,39,40';
					else if($row[csf('section')]==3) $subID='4,5,18';
					else if($row[csf('section')]==5) $subID='6,7,8,9,10,11,12,13,16,17,17,24';
					else if($row[csf('section')]==10) $subID='14,15';
					else if($row[csf('section')]==7) $subID='19,20,21,25,26,27,28,41';
					else if($row[csf('section')]==9) $subID='22';
					else $subID='0';
			
		

					?>
				</td>
				<td><? echo create_drop_down( "cboSection_".$tblRow, 90, $trims_section,"", 1, "-- Select Section --",$row[csf('section')],"load_sub_section($tblRow)",$next_proc_disabled_dropdown,'','','','','','',"cboSection[]"); ?></td>			
				<td id="subSectionTd_<? echo $tblRow; ?>"><? echo create_drop_down( "cboSubSection_".$tblRow, 90, $trims_sub_section,"", 1, "-- Select Section --",$row[csf('sub_section')],"load_sub_section_value($tblRow)",$next_proc_disabled_dropdown,$subID,'','','','','',"cboSubSection[]"); ?></td>			
				<td id="itemGroupTd_<? echo $tblRow; ?>"><? echo create_drop_down( "cboItemGroup_".$tblRow, 90, "select id, item_name from lib_item_group where item_category=4 and status_active=1","id,item_name", 1, "-- Select --",$row[csf('trim_group')], "",$next_proc_disabled_dropdown,'','','','','','',"cboItemGroup[]"); ?></td>
				<td><? echo create_drop_down( "cboUom_".$tblRow, 60, $unit_of_measurement,"", 1, "-- Select --",$order_uom,"", 1,'','','','','','',"cboUom[]"); ?>	</td>
				<td><input id="txtOrderQuantity_<? echo $tblRow; ?>" name="txtOrderQuantity[]" value="<? if($round_type==1){  echo round($wo_qnty);} elseif($round_type==2){ echo number_format($wo_qnty); } else{ echo number_format($wo_qnty,4,'.',''); } ?>"
				 class="text_boxes_numeric" type="text"  style="width:60px" onClick="openmypage_order_qnty(<? echo $tblRow; ?>)" placeholder="Click To Search" readonly /></td>

				<td><input id="txtPlaneCut_<? echo $tblRow; ?>" name="txtPlaneCut[]" value="<? if($round_type==1){  echo round($plan_cut);} elseif($round_type==2){ echo number_format($plan_cut); } else{ echo number_format($plan_cut,4,'.',''); } ?>" class="text_boxes_numeric" type="text"  style="width:60px" readonly /></td>

				<td><? echo create_drop_down( "cboBookUom_".$tblRow, 60, $unit_of_measurement,"", 1, "-- Select --",$row[csf('booked_uom')],1, 1,'','','','','','',"cboBookUom[]"); ?>	</td>
				<?
				if($row[csf('booked_uom')]==$order_uom)
				{
					$disabled_conv="disabled";
				}
				?>
				<td><input id="txtConvFactor_<? echo $tblRow; ?>" name="txtConvFactor[]" type="text"  class="text_boxes_numeric" value="<? echo $row[csf('booked_conv_fac')]; ?>"  onkeyup="cal_booked_qty(<? echo $tblRow; ?>);" style="width:47px"  <? echo $disabled_conv; ?>  /></td>
				<td><input id="txtBookQty_<? echo $tblRow; ?>" name="txtBookQty[]" type="text"  class="text_boxes_numeric" style="width:57px"  value="<? if($round_type==1){  echo round($row[csf('booked_qty')]);} elseif($round_type==2){ echo number_format($row[csf('booked_qty')]); } else{ echo number_format($row[csf('booked_qty')],4,'.',''); } ?>" readonly /></td>
				<td><input id="txtRate_<? echo $tblRow; ?>" name="txtRate[]" value="<? echo number_format($row[csf('rate')],6,'.',''); ?>" type="text"  class="text_boxes_numeric" style="width:60px" readonly/></td>
				<td><input id="txtAmount_<? echo $tblRow; ?>" name="txtAmount[]"  value="<? echo number_format($row[csf('amount')],4,'.',''); ?>" type="text" style="width:70px"  class="text_boxes_numeric"  disabled /></td>
				<td><input id="txtDomRate_<? echo $tblRow; ?>" name="txtDomRate[]" value="<? echo number_format($domRate,4,'.',''); ?>" type="text"  class="text_boxes_numeric" style="width:57px" readonly /></td>
				<td><input id="txtDomamount_<? echo $tblRow; ?>" name="txtDomamount[]" value="<? echo number_format($domAmount,4,'.',''); ?>" type="text"  class="text_boxes_numeric" style="width:77px" readonly /></td>

				<td><input type="text"  id="txtSamSubDate_<? echo $tblRow; ?>" name="txtSamSubDate[]" value="<? echo change_date_format($row[csf("submit_date")]);?>" class="datepicker" style="width:67px"  /></td>
				<td><input type="text"  id="txtSamApprDate_<? echo $tblRow; ?>" name="txtSamApprDate[]" value="<? echo change_date_format($row[csf("approve_date")]);?>" class="datepicker" style="width:67px"  /></td>

				<td><input type="text"  id="txtOrderDeliveryDate_<? echo $tblRow; ?>" name="txtOrderDeliveryDate[]" value="<? echo change_date_format($row[csf("delivery_date")]);?>" class="datepicker" onChange="chk_min_del_date(<? echo $tblRow; ?>); dateCopy(<? echo $tblRow; ?>);" style="width:67px"  />
					<input id="hdnDtlsUpdateId_<? echo $tblRow; ?>" name="hdnDtlsUpdateId[]" type="hidden" value="<? echo $dtlsID; ?>" class="text_boxes_numeric" style="width:40px" />
					<input type="hidden" id="hdnDtlsdata_<? echo $tblRow; ?>" name="hdnDtlsdata[]" value="<?=$data_break; ?>">
                    <?
                    if(isset($sizeInfoData[$dtlsID])) {
                        $size_break = rtrim($sizeInfoData[$dtlsID], "***");
                    }
                    ?>
                    <input type="hidden" name="sizedtlsdata[]" id="sizedtlsdata_<? echo $tblRow; ?>" value="<?=$size_break?>">
                    <input type="hidden" id="hdnbookingDtlsId_<? echo $tblRow; ?>" name="hdnbookingDtlsId[]" value="<? echo $row[csf('booking_dtls_id')]; ?>">
	                <input type="hidden" id="txtDeletedId_<? echo $tblRow; ?>" name="txtDeletedId[]" value="">
	                <input type="hidden" id="txtIsWithOrder_<? echo $tblRow; ?>" name="txtIsWithOrder[]" value="<? echo $isWithOrder; ?>">
					<input type="hidden" name="txtIsDuplicate[]" id="txtIsDuplicate_<? echo $tblRow; ?>" value="0">
	                <input type="hidden" id="txtIsRevised_<? echo $tblRow; ?>" name="txtIsRevised[]" value="<? echo $row[csf("is_revised")]; ?>">
	                <input type="hidden" id="nextProcessChk_<? echo $tblRow; ?>" name="nextProcessChk[]" value="<? echo $next_process; ?>">
	            </td>
	            <td><? $source_for_order = array(1 => 'In-House', 2 => 'Sub-Contract');
	            	echo create_drop_down( "cboSource_".$tblRow, 60, $source_for_order,"", 1, "-- Select --",$row[csf('source_for_order')],1, 0,'','','','','','',"cboSource[]"); ?>	</td>
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
            <td><input id="txtbuyerPo_1" name="txtbuyerPo[]" type="text" class="text_boxes" style="width:100px" placeholder="Display"/>
            	<input id="txtbuyerPoId_1" name="txtbuyerPoId[]" type="hidden" class="text_boxes" style="width:70px"readonly />
            </td>
            <td><input id="txtstyleRef_1" name="txtstyleRef[]" type="text" class="text_boxes" style="width:100px" placeholder="Display"/></td>
             <td><input id="txtbuyer_1" name="txtbuyer[]" type="text" class="text_boxes" style="width:100px" placeholder="Display" /></td>
            <td><? echo create_drop_down( "cboSection_1", 90, $trims_section,"id,section_name", 1, "-- Select Section --","","load_sub_section($tblRow)",0,'','','','','','',"cboSection[]"); ?></td>
            <td id="subSectionTd_1"><? echo create_drop_down( "cboSubSection_1", 90, $trims_sub_section,"id,section_name", 1, "-- Select Sub Section --","",'',0,'','','','','','',"cboSubSection[]"); ?></td>
            <td id="itemGroupTd_1"><? echo create_drop_down( "cboItemGroup_1", 90, "select id, item_name from lib_item_group where item_category=4 and  status_active=1","id,item_name", 1, "-- Select --",$selected, "",0,'','','','','','',"cboItemGroup[]"); ?>	</td>
            <td><? echo create_drop_down( "cboUom_1", 60, $unit_of_measurement,"", 1, "-- Select --",2,1, 1,'','','','','','',"cboUom[]"); ?>	</td>
            <td><input id="txtOrderQuantity_1" name="txtOrderQuantity[]" class="text_boxes_numeric" type="text"  style="width:60px" onClick="openmypage_order_qnty(1,'0',1)" placeholder="Click To Search" readonly /></td>
			<td><input id="txtPlaneCut_1" name="txtPlaneCut[]" class="text_boxes_numeric" type="text"  style="width:60px" readonly /></td>
            <td><input id="txtRate_1" name="txtRate[]" type="text"  class="text_boxes_numeric" style="width:60px" readonly /></td>
            <td><input id="txtAmount_1" name="txtAmount[]" type="text" style="width:70px"  class="text_boxes_numeric" readonly /></td> 
            <td><input id="txtDomRate_1" name="txtDomRate[]" type="text"  class="text_boxes_numeric" style="width:57px" readonly /></td> 
            <td><input id="txtDomamount_1" name="txtDomamount[]" type="text"  class="text_boxes_numeric" style="width:77px" readonly  /></td> 

            <td><input type="text"  id="txtSamSubDate_<? echo $tblRow; ?>" name="txtSamSubDate[]" value="<? echo change_date_format($row[csf("submit_date")]);?>" class="datepicker" style="width:67px"  /></td>
			<td><input type="text"  id="txtSamApprDate_<? echo $tblRow; ?>" name="txtSamApprDate[]" value="<? echo change_date_format($row[csf("approve_date")]);?>" class="datepicker" style="width:67px"  /></td>

            <td><input type="text" name="txtOrderDeliveryDate[]" id="txtOrderDeliveryDate_1" class="datepicker"  onChange="chk_min_del_date(1); dateCopy(1);"  style="width:67px" />
            	<input type="hidden" name="hdnDtlsUpdateId[]" id="hdnDtlsUpdateId_1">
                <input type="hidden" name="sizedtlsdata[]" id="sizedtlsdata_1">
                <input type="hidden" name="hdnDtlsdata[]" id="hdnDtlsdata_1">
                <input type="hidden" name="hdnbookingDtlsId[]" id="hdnbookingDtlsId_1">
                <input type="hidden" name="txtDeletedId[]" id="txtDeletedId_1">
                <input type="hidden" id="nextProcessChk_1" name="nextProcessChk[]" value="0">
            </td>
            <td><? $source_for_order = array(1 => 'In-House', 2 => 'Sub-Contract');
                            echo create_drop_down( "cboSource_1", 60, $source_for_order,"", 0, "-- Select --",1,1, 0,'','','','','','',"cboSource[]"); ?>	</td>
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
	?><input type="hidden" id="min_date_id" name="min_date_id" value="<? echo change_date_format($min_dates);?>"><?
	exit();
}


if($action=="order_qty_popup_only_last_row_delete")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	$color_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$size_arr=return_library_array( "select id,size_name from  lib_size where status_active=1 and is_deleted=0",'id','size_name');
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
			//alert(i); return;
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
			/*for(var k=1; k<=row_num; k++)
			{

			}*/
			
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
				if($("#txtStyle_"+i).val()=="") $("#txtStyle_"+i).val(0);
				
				if(data_break_down=="")
				{
					data_break_down+=$('#txtdescription_'+i).val()+'_'+$('#txtcolor_'+i).val()+'_'+$('#txtsize_'+i).val()+'_'+$('#txtorderquantity_'+i).val()+'_'+$('#txtorderrate_'+i).val()+'_'+$('#txtorderamount_'+i).val()+'_'+$('#hidbookingconsid_'+i).val()+'_'+$('#hiddenid_'+i).val()+'_'+$('#txtcolorId_'+i).val()+'_'+$('#txtsizeID_'+i).val()+'_'+$('#txtStyle_'+i).val();
				}
				else
				{
					data_break_down+="***"+$('#txtdescription_'+i).val()+'_'+$('#txtcolor_'+i).val()+'_'+$('#txtsize_'+i).val()+'_'+$('#txtorderquantity_'+i).val()+'_'+$('#txtorderrate_'+i).val()+'_'+$('#txtorderamount_'+i).val()+'_'+$('#hidbookingconsid_'+i).val()+'_'+$('#hiddenid_'+i).val()+'_'+$('#txtcolorId_'+i).val()+'_'+$('#txtsizeID_'+i).val()+'_'+$('#txtStyle_'+i).val();
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
			$("#txt_average_rate").val( number_format(rate,6,'.','' ) );
		}

	</script>
</head>
<body onLoad="set_auto_complete('color_return'); set_auto_complete_size('size_return');">
	<div align="center" style="width:100%;" >
		<form name="qntypopup_1"  id="qntypopup_1" autocomplete="off">
			<table class="rpt_table" width="780px" cellspacing="0" cellpadding="0" rules="all" id="tbl_share_details_entry">
				<thead>
					<th width="130">Style</th>
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
					//echo "<pre>".$data_break."</pre>";
					if($data_break!=''){
						$data_array=explode("***",$data_break);
						$is_available_datas=count($data_array);
					}
					else
					{
						$is_available_datas=0;
					}
					//echo $within_group;
					$k=0;
					//echo count($data_array);
					if($within_group==1) $disabled="disabled"; else $disabled="";
					if($is_available_datas>0)
					{
						foreach($data_array as $row)
						{
							$data=explode('_',$row);
							$k++;
							if(($data[7]=='' || $data[7]==0 ) && $within_group==1) $styleRef=$txtstyleRef; else $styleRef=$data[10];
							?>
							<tr>
								<td><input type="text" id="txtStyle_<? echo $k;?>" name="txtStyle_<? echo $k;?>" class="text_boxes" style="width:120px" value="<? echo $styleRef; ?>" <? echo $disabled; ?> /></td>
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
                        	<td><input type="text" id="txtStyle_1" name="txtStyle_1" class="text_boxes" style="width:120px" value="<? echo $txtstyleRef; ?>" /></td>
                        	<td><input type="text" id="txtdescription_1" name="txtdescription_1" class="text_boxes" style="width:120px" value="" /></td>
							<td>
								<input type="text" id="txtcolor_1" name="txtcolor_1" class="text_boxes txt_color" style="width:90px" value="" >
								<input type="hidden" id="txtcolorId_1" name="txtcolorId_1" class="text_boxes_numeric" style="width:90px" value=""  /></td>
							<td><input type="text" id="txtsize_1" name="txtsize_1" class="text_boxes txt_size" style="width:70px" value=""  >
								<input type="hidden" id="txtsizeID_1" name="txtsizeID_1" class="text_boxes_numeric" style="width:70px" value=""></td>
							<td>
								<input type="text" id="txtorderquantity_1" name="txtorderquantity_1" class="text_boxes_numeric" style="width:70px" onKeyUp="sum_total_qnty(1);" value="" <? echo $disabled; ?> />
								<input type="hidden" id="hiddenOrderQuantity_1" name="hiddenOrderQuantity_1" class="text_boxes_numeric" style="width:70px" value=""  />
							</td>
							<td><input type="text" id="txtorderrate_1" name="txtorderrate_1"  class="text_boxes_numeric" style="width:60px" onKeyUp="sum_total_qnty(1)" value="" <? echo $disabled; ?> />
							</td>
							<td><input type="text" id="txtorderamount_1" name="txtorderamount_1" class="text_boxes_numeric" style="width:70px" value="" disabled/></td>
							<td>
								<input type="hidden" id="hidbookingconsid_1" name="hidbookingconsid_1"  style="width:15px;" class="text_boxes" value="" />
                                <input type="hidden" id="hiddenid_1" name="hiddenid_1"  style="width:15px;" class="text_boxes" value="" />
								<input type="button" id="increaseset_1" style="width:30px" class="formbutton" value="+" onClick="add_share_row(1)" <? echo $disabled; ?> />
								<input type="button" id="decreaseset_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(1 ,'tbl_share_details_entry' );" <? echo $disabled; ?>/>
							</td>
                        </tr>
						<?
					}
					?> 
				</tbody>
				<tfoot>
					<th colspan="4">Total</th> 
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

if($action=="order_qty_popup")
{
	
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	$round= str_replace("'","",$cbo_round_type);
	//echo $round;
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


		
		function add_share_row( i, table_id, tr_id ) 
		{
			var prefix=tr_id.substr(0, tr_id.length-1);
			var row_num = $('#tbl_share_details_entry tbody tr').length; 
			//alert(i+"**"+table_id+"**"+tr_id+"**"+row_num);
			row_num++;
			var clone= $("#"+tr_id+i).clone();
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
            clone.find('.load_size_popup').attr("onclick", "openmypage_sizepopup("+row_num+")");
			$("#"+tr_id+i).after(clone);

			//$('#increaseset_'+i).removeAttr("onClick").attr("onClick","add_share_row("+i+");");
			//$('#increaseset_'+row_num).removeAttr("onClick").attr("onClick","add_share_row("+row_num+")");
			$('#txtRow_'+row_num).val(row_num);
			$('#txtorderquantity_'+row_num).removeAttr("onKeyUp").attr("onKeyUp","sum_total_qnty("+row_num+")");
			$('#txtorderrate_'+row_num).removeAttr("onKeyUp").attr("onKeyUp","sum_total_qnty("+row_num+")");
			$('#txtexcesscut_'+row_num).removeAttr("onKeyUp").attr("onKeyUp","sum_total_qnty("+row_num+")");
			$('#txtcolor_'+row_num).removeAttr("disabled");
			$('#txtorderquantity_'+row_num).removeAttr("disabled");
			$('#decreaseset_'+row_num).removeAttr("disabled");			
			//$('#decreaseset_'+row_num).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+row_num+','+'"tbl_share_details_entry"'+");");
			//$('#decreaseset_'+row_num).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+row_num+")");

			$('#increaseset_'+row_num).removeAttr("onclick").attr("onclick","add_share_row("+row_num+",'"+table_id+"','"+tr_id+"');");
			$('#decreaseset_'+row_num).removeAttr("onclick").attr("onclick","fn_deletebreak_down_tr("+row_num+",'"+table_id+"','"+tr_id+"');");

			$('#txtsize_'+row_num).val('');
            $('#hiddensizeinfo_'+row_num).val('');
			//$('#loss_'+row_num).val('');
			$('#hiddenid_'+row_num).val('');
			var k=0; var var_id='';
			$("#tbl_share_details_entry tbody tr").each(function()
			{
				k++;
				var var_id 		= $(this).find('input[name="txtSl[]"]').attr('id');
				//alert(var_id);
				$('#'+var_id).val(k);
			});
			
			set_all_onclick();
			$("#txtgmtscolor_"+row_num).autocomplete({
				source: str_color
			});
			$("#txtgmtssize_"+row_num).autocomplete({
				source: str_size
			});
			$("#txtcolor_"+row_num).autocomplete({
				source: str_color
			});
			$("#txtsize_"+row_num).autocomplete({
				source: str_size
			});
			set_all_onclick();
			sum_total_qnty(row_num);
		}		
		
		function fn_deletebreak_down_tr(rowNo,table_id,tr_id) 
		{ 
			var numRow = $('#'+table_id+' tbody tr').length; 
			var prefix=tr_id.substr(0, tr_id.length-1);
			var total_row=$('#'+prefix+'_tot_row').val();
			
			var numRow = $('table#tbl_share_details_entry tbody tr').length; 
			if(numRow!=1)
			{
				var updateIdDtls=$('#hiddenid_'+rowNo).val();
				var txt_deleted_id=$('#txtDeletedId').val();
				var selected_id='';
				if(updateIdDtls!='')
				{
					if(txt_deleted_id=='') selected_id=updateIdDtls; else selected_id=txt_deleted_id+','+updateIdDtls;
					$('#txtDeletedId').val( selected_id );
				}
				
				$("#"+tr_id+rowNo).remove();
				$('#'+prefix+'_tot_row').val(total_row-1);
				var k=0; var var_id='';
				$("#tbl_share_details_entry tbody tr").each(function()
				{
					k++;
					var var_id 		= $(this).find('input[name="txtSl[]"]').attr('id');
					//alert(var_id);
					$('#'+var_id).val(k);
				});
				set_all_onclick();
				sum_total_qnty(numRow);
				//calculate_total_amount(1);
			}
		}

		
		function fnc_close()
		{
			var check_field=0; 
			var data_break_down="", sizeinfodtls = "";
			$("#tbl_share_details_entry tbody tr").each(function()
			{
				var txtdescription 	    = $(this).find('input[name="txtdescription[]"]').val();
				var txtcolor 		    = $(this).find('input[name="txtcolor[]"]').val();
				var txtsize 		    = $(this).find('input[name="txtsize[]"]').val();
				var txtorderquantity    = $(this).find('input[name="txtorderquantity[]"]').val();
				var txtorderrate 	    = $(this).find('input[name="txtorderrate[]"]').val();
				var txtorderamount 	    = $(this).find('input[name="txtorderamount[]"]').val();
				var hidbookingconsid    = $(this).find('input[name="hidbookingconsid[]"]').val();
				var hiddenid 		    = $(this).find('input[name="hiddenid[]"]').val();
				var txtcolorId 		    = $(this).find('input[name="txtcolorId[]"]').val();
				var txtsizeID 		    = $(this).find('input[name="txtsizeID[]"]').val();
				var txtStyle 		    = $(this).find('input[name="txtStyle[]"]').val();
				var txtexcesscut 	    = $(this).find('input[name="txtexcesscut[]"]').val();
				var txtplanecut 	    = $(this).find('input[name="txtPlaneCut[]"]').val();
				var txtplanecutamount 	= $(this).find('input[name="txtPlaneCutAmount[]"]').val();
				var txtply 	            = $(this).find('input[name="txtply[]"]').val();
				var txtgmtscolor        = $(this).find('input[name="txtgmtscolor[]"]').val();
				var txtgmtscolorId      = $(this).find('input[name="txtgmtscolorId[]"]').val();
				var txtgmtssize         = $(this).find('input[name="txtgmtssize[]"]').val();
				var txtgmtssizeId       = $(this).find('input[name="txtgmtssizeId[]"]').val();				

				//alert(cboSection);
				
				if( txtorderquantity ==''  || txtorderquantity ==0 )
				{	 				
					alert('Please Fill up Qty ');
					check_field=1 ; return;
				}
				
				if(check_field==0)
				{
					if(data_break_down=="")
					{
						data_break_down+=txtdescription+'_'+txtcolor+'_'+txtsize+'_'+txtorderquantity+'_'+txtorderrate+'_'+txtorderamount+'_'+hidbookingconsid+'_'+hiddenid+'_'+txtcolorId+'_'+txtsizeID+'_'+txtStyle+'_'+txtexcesscut+'_'+txtplanecut+'_'+txtplanecutamount+'_'+txtply+'_'+txtgmtscolor+'_'+txtgmtssize+'_'+txtgmtscolorId+'_'+txtgmtssizeId;
					}
					else
					{
						data_break_down+="***"+txtdescription+'_'+txtcolor+'_'+txtsize+'_'+txtorderquantity+'_'+txtorderrate+'_'+txtorderamount+'_'+hidbookingconsid+'_'+hiddenid+'_'+txtcolorId+'_'+txtsizeID+'_'+txtStyle+'_'+txtexcesscut+'_'+txtplanecut+'_'+txtplanecutamount+'_'+txtply+'_'+txtgmtscolor+'_'+txtgmtssize+'_'+txtgmtscolorId+'_'+txtgmtssizeId;
					}
                    if(sizeinfodtls == "")
                        sizeinfodtls += $(this).find('input[name="hiddensizeinfo[]"]').val();
                    else
                        sizeinfodtls += "***"+$(this).find('input[name="hiddensizeinfo[]"]').val();
                }
			});
			$('#hidden_break_tot_row').val( data_break_down );
			$('#sizeinfodtls').val( sizeinfodtls );
			//alert(tot_row);//return;
			parent.emailwindow.hide();
		}

		function sum_total_qnty(id)
		{
			var ddd={ dec_type:5, comma:0, currency:''};
			var qty=0; var amt=0; var pln=0; var exces=0; var plancut=0;
			var orderquantity=$('#txtorderquantity_'+id).val()*1;
			var orderrate=$('#txtorderrate_'+id).val()*1;
			var excesscut=$('#txtexcesscut_'+id).val()*1;
			var amount =orderquantity*orderrate;

			var excessparcent=(excesscut / 100);
			var planecut=(excessparcent*orderquantity)+orderquantity;

			//alert(excesscut+"="+excessparcent+"="+planecut+"="+orderquantity);

			var planecutamounts=planecut*orderrate;
			//alert(amount);
			$("#txtorderamount_"+id).val( number_format(amount,4,'.','' ) );
			$("#txtPlaneCutAmount_"+id).val( number_format(planecutamounts,4,'.','' ) );
			var round_type="<?= $round;?>";
			//alert(round_type+"="+id+"="+planecut+"="+excessparcent+"="+excesscut+"="+orderquantity);
			if(round_type==1){
				$("#txtPlaneCut_"+id).val( Math.round(planecut) );
			}else if(round_type==2){
				$("#txtPlaneCut_"+id).val( Math.floor(planecut) );
			}else{
				$("#txtPlaneCut_"+id).val( number_format(planecut,4,'.','' ) );
			}

			$("#tbl_share_details_entry tbody tr").each(function()
			{
				var txtorderquantity = $(this).find('input[name="txtorderquantity[]"]').val()*1;
				var txtorderrate = $(this).find('input[name="txtorderrate[]"]').val()*1;
				var txtorderamount 	= $(this).find('input[name="txtorderamount[]"]').val()*1;
				var txtplanecut 	= $(this).find('input[name="txtPlaneCut[]"]').val()*1;
				var txtExcesscut 	= $(this).find('input[name="txtexcesscut[]"]').val()*1;
				var planecutamount 	= $(this).find('input[name="txtPlaneCutAmount[]"]').val()*1;

				qty+=txtorderquantity*1;
				amt+=txtorderamount*1;
				pln+=txtplanecut*1;
				//exces+=txtExcesscut*1;
				plancut+=planecutamount*1;
				// alert(pln);
			});
			var rate=amt/qty;
			$("#txt_total_order_qnty").val( number_format(qty,4,'.','' ) );
			$("#txt_total_order_amount").val( number_format(amt,4,'.','' ) );
			$("#txt_average_rate").val( number_format(rate,6,'.','' ) );
			$("#txtplane_total").val( number_format(pln,4,'.','' ) );
			//$("#excess_cut").val( number_format(exces,4,'.','' ) );
			$("#plncut_amount").val( number_format(plancut,4,'.','' ) );
		}

        function openmypage_sizepopup(row)
        {
            var sizedtlsdata = $("#hiddensizeinfo_"+row).val();
            var page_link = 'trims_order_receive_controller.php?breakdowndtls='+sizedtlsdata+'&action=size_details_popup&row='+row;
            emailwindow=dhtmlmodal.open('EmailBox','iframe',page_link,'Size Details Pop-up', 'width=600px, height=250px, center=1, resize=0, scrolling=0','../../');
            emailwindow.onclose=function()
            {
                var id              = this.contentDoc.getElementById("s_id").value;
                var length          = this.contentDoc.getElementById("s_length").value;
                var width           = this.contentDoc.getElementById("s_width").value;
                var height          = this.contentDoc.getElementById("s_height").value;
                var flap            = this.contentDoc.getElementById("s_flap").value;
                var gusset          = this.contentDoc.getElementById("s_gusset").value;
                var thickness       = this.contentDoc.getElementById("s_thickness").value;
                var measurement_id  = this.contentDoc.getElementById("s_measuremnt_id").value;
                var subcon_brk_id   = this.contentDoc.getElementById("s_subcon_order_brk_id").value;
                var mesurement_arr = {"25":"CM", "26":"MM", "29":"Inch"};
                var blankstats = false;
                if(length != "" || width != "" || height != "" || flap!= "" || gusset != "")
                    blankstats = true;
                var concatSizedata = id+'_'+length+'_'+width+'_'+height+'_'+flap+'_'+gusset+'_'+thickness+'_'+measurement_id+'_'+subcon_brk_id;
                var fieldValue = (length != "" ? "L "+length+"["+mesurement_arr[measurement_id]+"] -" : "")+(width != "" ? " W "+width+"["+mesurement_arr[measurement_id]+"] -" : "")+(height != "" ? " H "+height+"["+mesurement_arr[measurement_id]+"] -" : "")+(flap != "" ? " F "+flap+"["+mesurement_arr[measurement_id]+"] -" : "")+(gusset != "" ? " G "+gusset+"["+mesurement_arr[measurement_id]+"] -" : "")+(thickness != "" ? " THICK "+thickness : "");
                if(blankstats){
                    $('#hiddensizeinfo_'+row).val(concatSizedata);
                    $('#txtsize_'+row).val(fieldValue.replace(/-\s*$/, ''));
                    $('#txtsizeID_'+row).val(0);
                }
            }
        }
	</script>
</head>
<body onLoad="set_auto_complete('color_return'); set_auto_complete_size('size_return');">
	<div align="center" style="width:100%;" >
		<form name="qntypopup_1"  id="qntypopup_1" autocomplete="off">
			<table class="rpt_table" width="1240px" cellspacing="0" cellpadding="0" rules="all" id="tbl_share_details_entry">
				<thead>
					<th width="40">SL No</th>
					<th width="80">Style</th>
					<th width="80">Description</th>
					<th width="70">Ply</th>
					<th width="70">Gmts Color</th>					
                    <th width="70">Gmts Size</th>
					<th width="70">Item Color</th>					
                    <th width="110">Item Size</th>
					<th width="70" class="must_entry_caption">Order Qty</th>
					<th width="60">Excess %</th>
					<th width="60">Plan Qty</th>
					<th width="60">Rate</th>
					<th width="80">Amount</th>
					<th width="90">Plan Amount</th>
					<th>&nbsp;</th>
				</thead>
				<tbody>
					<input type="hidden" name="txtDeletedId" id="txtDeletedId" class="text_boxes_numeric" style="width:90px" readonly />
					<input type="hidden" name="hidden_break_tot_row" id="hidden_break_tot_row" class="text_boxes" style="width:90px" />
					<input type="hidden" name="sizeinfodtls" id="sizeinfodtls" class="text_boxes" style="width:90px" />
					<?
//                    $sizeInfoData = array();
//					if($hdnDtlsUpdateId != ""){
//                        $sql_get_size_info = sql_select("SELECT a.id, b.id as sizeinfoid, b.length, b.width, b.height, b.flap, b.gusset, b.thickness, b.measurementid from subcon_ord_breakdown a, subcon_ord_breakdown_size_info b where mst_id = $hdnDtlsUpdateId and status_active = 1 and is_deleted = 0 and a.id = b.subconordbreakdownid order by b.subconordbreakdownid asc");
//                        if(count($sql_get_size_info) > 0){
//                            foreach($sql_get_size_info as $sizeinfo){
//                                $sizeInfoData[$sizeinfo[csf('id')]]= $sizeinfo[csf('sizeinfoid')].'_'.$sizeinfo[csf('length')].'_'.$sizeinfo[csf('width')].'_'.$sizeinfo[csf('height')].'_'.$sizeinfo[csf('flap')].'_'.$sizeinfo[csf('gusset')].'_'.$sizeinfo[csf('thickness')].'_'.$sizeinfo[csf('measurementid')].'_'.$sizeinfo[csf('id')];
//                            }
//                        }
//                    }
                    $data_array_size = array();
                    if($sizedtlsdata != ""){
                        $data_array_size=explode("***",$sizedtlsdata);
                    }
//                  print_r($data_array_size);
					if($data_break!='')
					{
						$data_array=explode("***",$data_break);
						$is_available_datas=count($data_array);
					}
					else
					{
						$is_available_datas=0;
					}
					
					
				 
				$delivery_arr=array();  
				$delivery_sql ="select break_down_details_id
				from trims_delivery_dtls  
				where  receive_dtls_id=$hdnDtlsUpdateId and  status_active=1 and  status_active=1";
				$delivery_sql_res = sql_select($delivery_sql);
				foreach($delivery_sql_res as $row)
				{
 					if($row[csf('break_down_details_id')]!='')
					{
						$delivery_arr[$row[csf('break_down_details_id')]]=$row[csf('break_down_details_id')];
					}
 				}
					
					
					
					 $cbo_company_name=str_replace("'",'',$cbo_company_name);
					 $sql = "select id, work_order_number_control from variable_setting_trim_marketing where company_name=$company and status_active=1 and is_deleted=0 and variable_list=3"; 
					$sectionvariable = sql_select($sql);
					$variable_bill_prod_delv=$sectionvariable[0][csf('work_order_number_control')];
					
					
								
					
					 //  print_r($delivery_arr);
					
					$k=0;
					//echo count($data_array);
					
					if($variable_bill_prod_delv==1 || $variable_bill_prod_delv==2 || $variable_bill_prod_delv==3)
					{
						if($within_group==1) $disabled="disabled"; else $disabled="";
						if($nextProcessChk==1) $disabled_next_content="disabled"; else $disabled_next_content="";
					}
					else
					{
						if($within_group==1) $disabled="disabled"; else $disabled="";
						if($nextProcessChk==1) $disabled_next="disabled"; else $disabled_next="";
						
					}
					
					

					
					if($is_available_datas>0)
					{
						foreach($data_array as $row)
						{
							$data=explode('_',$row);
							$k++;
							if(($data[7]=='' || $data[7]==0 ) && $within_group==1) $styleRef=$txtstyleRef; else $styleRef=$data[10];
                            if($data[9] > 0 && $data[2] == ''){
                                $size_name_cng = $size_arr[$data[9]];
                            }else{
                                $size_name_cng = $data[2];
                            }
							
							
							   if($variable_bill_prod_delv==1 || $variable_bill_prod_delv==2 || $variable_bill_prod_delv==3)
								{
									if($within_group==2)
									{
									  $disabled_next_delivery=""; 
									}
								}
								else
								{
									if($within_group==2)
									{
									   if($delivery_arr[$data[7]]) $disabled_next_delivery="disabled"; else $disabled_next_delivery="";
									}
									
								}
							
							
                            ?>
							<tr id="row_<? echo $k;?>">
								<td><input type="text" name="txtSl[]" id="txtRow_<? echo $k;?>" class="text_boxes" style="width:30px; text-align: center;" value="<? echo $k; ?>" disabled /></td>
								<td><input type="text" id="txtStyle_<? echo $k;?>" name="txtStyle[]" class="text_boxes" style="width:80px" value="<? echo $styleRef; ?>" <? echo $disabled; ?> <? echo $disabled_next; ?> <? echo $disabled_next_delivery; ?> <? echo $disabled_next_content; ?>/></td>
								<td>
                                    <input type="text" id="txtdescription_<? echo $k;?>" name="txtdescription[]" class="text_boxes" style="width:80px" value="<? echo $data[0]; ?>" <? echo $disabled_next; ?> <? echo $disabled_next_delivery; ?> <? echo $disabled_next_content; ?> />
								</td>
								<td>
                                    <input type="text" id="txtply_<? echo $k;?>" name="txtply[]" class="text_boxes txt_ply" style="width:70px" value="<? echo $data[14]; ?>" <? echo $disabled_next_delivery; ?><? echo $disabled_next_content; ?> />
                                </td>
								<td>
									<input type="text" id="txtgmtscolor_<? echo $k;?>" name="txtgmtscolor[]" class="text_boxes" style="width:70px" value="<? echo $data[15]; ?>" <? echo $disabled; ?> <? echo $disabled_next_delivery; ?> <? echo $disabled_next_content; ?> />
									<input type="hidden" id="txtgmtscolorId_<? echo $k;?>" name="txtgmtscolorId[]" class="text_boxes_numeric" style="width:70px" value="<? echo $data[17]; ?>"  />
								</td>
								<td>
									<input type="text" id="txtgmtssize_<? echo $k;?>" name="txtgmtssize[]" class="text_boxes" style="width:70px" value="<? echo $data[16]; ?>" <? echo $disabled; ?> <? echo $disabled_next_delivery; ?> <? echo $disabled_next_content; ?>/>
									<input type="hidden" id="txtgmtssizeId_<? echo $k;?>" name="txtgmtssizeId[]" class="text_boxes_numeric" style="width:70px" value="<? echo $data[18]; ?>"  />
								</td>
								<td>
									<input type="text" id="txtcolor_<? echo $k;?>" name="txtcolor[]" class="text_boxes txt_color" style="width:70px" value="<? echo $data[1]; ?>" <? echo $disabled_next; ?> <? echo $disabled_next_delivery; ?> <? echo $disabled_next_content; ?> />
									<input type="hidden" id="txtcolorId_<? echo $k;?>" name="txtcolorId[]" class="text_boxes_numeric" style="width:70px" value="<? echo $data[8]; ?>"  /></td>
                                
                                <td>
                                    <input type="text" id="txtsize_<? echo $k;?>" name="txtsize[]" class="text_boxes txt_size" style="width:80px" value="<? echo $size_name_cng; ?>" <? echo $disabled_next; ?> <? echo $disabled_next_content; ?>><span class="load_size_popup" style="float:right; width:15px; font-weight: bold;background-image: -webkit-linear-gradient(bottom, rgb(136,170,214) 7%, rgb(194,220,255) 10%, rgb(136,170,214) 96%);color:#000; border: outset 1px #66CC00; cursor: pointer;text-align: center;font-size: 13px;border-radius: 5px;" onClick="openmypage_sizepopup(<?=$k?>)">&#9744;</span>
									<input type="hidden" id="txtsizeID_<? echo $k;?>" name="txtsizeID[]" class="text_boxes_numeric" style="width:70px" value="<? echo $data[9]; ?>"></td>
								<td>
									<input type="text" id="txtorderquantity_<? echo $k;?>" name="txtorderquantity[]" class="text_boxes_numeric" style="width:70px" onKeyUp="sum_total_qnty(<? echo $k;?>);" value="<? echo number_format($data[3],4,'.',''); ?>" <? echo $disabled; ?>  <? echo $disabled_next_delivery; ?> />
									<input type="hidden" id="hiddenOrderQuantity_<? echo $k;?>" name="hiddenOrderQuantity[]" class="text_boxes_numeric" style="width:70px" value="<? echo $data[3]; ?>"  />
								</td>
								<td>
                                    <input type="text" title="new" id="txtexcesscut_<? echo $k;?>" name="txtexcesscut[]"  class="text_boxes_numeric" style="width:60px" onKeyUp="sum_total_qnty(<? echo $k;?>)" value="<? echo number_format($data[11],0,'.',''); ?>" <? echo $disabled_next_delivery; ?> <? echo $disabled_next_content; ?>/>
								</td>
								<td>
                                    <input type="text" title="new" id="txtPlaneCut_<? echo $k;?>" name="txtPlaneCut[]"  class="text_boxes_numeric" style="width:60px" onKeyUp="sum_total_qnty(<? echo $k;?>)" value="<? echo number_format($data[12],4,'.',''); ?>" <? echo $disabled; ?>  <? echo $disabled_next_delivery; ?> <? echo $disabled_next_content; ?>/>
								</td>
								<td>
                                    <input type="text" id="txtorderrate_<? echo $k;?>" name="txtorderrate[]"  class="text_boxes_numeric" style="width:60px" onKeyUp="sum_total_qnty(<? echo $k;?>)" value="<? echo number_format($data[4],6,'.',''); ?>" <? echo $disabled; ?>  <? echo $disabled_next_delivery; ?>/>
								</td>
								
								<td><input type="text" id="txtorderamount_<? echo $k;?>" name="txtorderamount[]" class="text_boxes_numeric" style="width:70px" value="<? echo number_format($data[5],4,'.',''); ?>"  disabled/></td>
								
								<td>
                                    <input type="text" title="new" id="txtPlaneCutAmount_<? echo $k;?>" name="txtPlaneCutAmount[]" class="text_boxes_numeric" style="width:90px" value="<? echo number_format($data[13],4,'.',''); ?>" disabled/></td>
								<td align="center">
									<input type="hidden" id="hidbookingconsid_<? echo $k; ?>" name="hidbookingconsid[]"  style="width:15px;" class="text_boxes" value="<? echo $data[6]; ?>" />
                                    <input type="hidden" id="hiddenid_<? echo $k; ?>" name="hiddenid[]"  style="width:15px;" class="text_boxes" value="<? echo $data[7]; ?>" />
                                    <?
                                    if(isset($data_array_size[$k-1]))
                                        $ordersizeinfo = $data_array_size[$k-1];
                                    else
                                        $ordersizeinfo = "";
                                    ?>
                                    <input type="hidden" id="hiddensizeinfo_<? echo $k; ?>" name="hiddensizeinfo[]"  style="width:15px;" class="text_boxes" value="<? echo $ordersizeinfo; ?>" />
									<input type="button" id="increaseset_<? echo $k;?>" name="increaseset[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_share_row(<? echo $k;?>,'tbl_share_details_entry','row_')"  <? echo $disabled; ?> />
									<input type="button" id="decreaseset_<? echo $k;?>" name="decreaseset[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deletebreak_down_tr(<? echo $k;?>,'tbl_share_details_entry','row_');"  <? echo $disabled; ?>  <? echo $disabled_next_delivery; ?> />
								</td>
							</tr>
							<?
						}
					}
					else
					{
						?>
                        <tr id="row_1">
							<td><input type="text"  name="txtSl[]" id="txtRow_1" class="text_boxes" style="width:30px; text-align: center" value="1" disabled /></td>
                        	<td><input type="text" id="txtStyle_1" name="txtStyle[]" class="text_boxes" style="width:80px" value="<? echo $txtstyleRef; ?>" /></td>
                        	<td><input type="text" id="txtdescription_1" name="txtdescription[]" class="text_boxes" style="width:80px" value="" /></td>
							<td><input type="text" id="txtply_1" name="txtply[]" class="text_boxes txt_ply" style="width:70px" value=""> </td>
							<td>
								<input type="text" id="txtgmtscolor_1" name="txtgmtscolor[]" class="text_boxes txt_color" style="width:70px" value="" >
								<input type="hidden" id="txtgmtscolorId_1" name="txtgmtscolorId[]" class="text_boxes_numeric" style="width:70px" value=""  />
                            </td>
							<td>
								<input type="text" id="txtgmtssize_1" name="txtgmtssize[]" class="text_boxes txt_size" style="width:70px" value="" >
								<input type="hidden" id="txtgmtssizeId_1" name="txtgmtssizeId[]" class="text_boxes_numeric" style="width:70px" value=""  />
                            </td>
							<td>
								<input type="text" id="txtcolor_1" name="txtcolor[]" class="text_boxes txt_color" style="width:70px" value="" >
								<input type="hidden" id="txtcolorId_1" name="txtcolorId[]" class="text_boxes_numeric" style="width:70px" value=""  />
                            </td>
                           
                            <td>
                                <input type="text" id="txtsize_1" name="txtsize[]" class="text_boxes txt_size" style="width:80px" value=""  ><span class="load_size_popup" style="float:right; width:15px; font-weight: bold;background-image: -webkit-linear-gradient(bottom, rgb(136,170,214) 7%, rgb(194,220,255) 10%, rgb(136,170,214) 96%);color:#000; border: outset 1px #66CC00; cursor: pointer;text-align: center;font-size: 13px;border-radius: 5px;" onClick="openmypage_sizepopup(1)">&#9744;</span>
								<input type="hidden" id="txtsizeID_1" name="txtsizeID[]" class="text_boxes_numeric" style="width:70px" value="">
                            </td>
							<td>
								<input type="text" id="txtorderquantity_1" name="txtorderquantity[]" class="text_boxes_numeric" style="width:70px" onKeyUp="sum_total_qnty(1);" value="" <? echo $disabled; ?> />
								<input type="hidden" id="hiddenOrderQuantity_1" name="hiddenOrderQuantity[]" class="text_boxes_numeric" style="width:70px" value=""  />
							</td>
							<td>
                                <input type="text" id="txtexcesscut_1" title="new" name="txtexcesscut[]"  class="text_boxes_numeric" style="width:60px" onKeyUp="sum_total_qnty(1)" value="" />
							</td>
							<td>
                                <input type="text" id="txtPlaneCut_1" title="new" name="txtPlaneCut[]"  class="text_boxes_numeric" style="width:60px" onKeyUp="sum_total_qnty(1)" value="" <? echo $disabled; ?> />
							</td>
							<td>
                                <input type="text" id="txtorderrate_1" name="txtorderrate[]"  class="text_boxes_numeric" style="width:60px" onKeyUp="sum_total_qnty(1)" value="" <? echo $disabled; ?> />
							</td>
							<td><input type="text" id="txtorderamount_1" name="txtorderamount[]" class="text_boxes_numeric" style="width:70px" value="" disabled/></td>

							<td><input type="text" id="txtPlaneCutAmount_1" title="new" name="txtPlaneCutAmount[]" class="text_boxes_numeric" style="width:90px" value="" disabled/></td>
							<td align="center">
								<input type="hidden" id="hidbookingconsid_1" name="hidbookingconsid[]"  style="width:15px;" class="text_boxes" value="" />
                                <input type="hidden" id="hiddenid_1" name="hiddenid[]"  style="width:15px;" class="text_boxes" value="" />
                                <input type="hidden" id="hiddensizeinfo_1" name="hiddensizeinfo[]"  style="width:15px;" class="text_boxes" value="" />
                                <input type="button" id="increaseset_1" name="increaseset[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_share_row(1,'tbl_share_details_entry','row_')"  <? echo $disabled; ?>  />
								<input type="button" id="decreaseset_1" name="decreaseset[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deletebreak_down_tr(1,'tbl_share_details_entry','row_');"  <? echo $disabled; ?>  />
							</td>
                        </tr>
						<?
					}
					?> 
				</tbody>
				<tfoot>
					<th colspan="8">Total</th>
					<th><input type="text" id="txt_total_order_qnty" name="txt_total_order_qnty" class="text_boxes_numeric" readonly style="width:70px" value="<? echo $break_tot_qty;//number_format($break_tot_qty,4); ?>"; /></th>
					<th><input type="text" id="excess_cut" name="excess_cut" class="text_boxes_numeric" readonly style="width:61px" value="" /></th>
					<th><input type="text" id="txtplane_total" value="<? echo number_format($break_avg_rate, 6, '.', ''); ?>"  name="txtplane_total" class="text_boxes_numeric" readonly style="width:61px"/></th>
					<th><input type="text" id="txt_average_rate" name="txt_average_rate" class="text_boxes_numeric" readonly style="width:61px" value="<? echo $break_avg_rate ?>"/></th>
					<th><input type="text" id="txt_total_order_amount" name="txt_total_order_amount" class="text_boxes_numeric" readonly style="width:70px" value="<? echo $break_total_value; ?>" /></th>
					<th><input type="text" id="plncut_amount" name="plncut_amount" class="text_boxes_numeric" readonly style="width:90px" value="<? echo $break_total_value; ?>" /></th>
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


if($action=="size_details_popup")
    {

        echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
        extract($_REQUEST);
        $s_data = explode('_', $breakdowndtls);
        ?>
        <script>

            function size_fnc_close(row)
            {
                parent.emailwindow.hide();
            }

        </script>
        </head>
        <body>
        <div align="center" style="width:100%;" >
            <form name="qntypopup_1"  id="qntypopup_1" autocomplete="off">
                <table class="rpt_table" width="580px" cellspacing="0" cellpadding="0" rules="all" id="tbl_share_details_entry">
                    <thead>
                    <tr>
                        <th colspan="7">Size</th>
                    </tr>
                    <tr>
                        <th width="90">Measurement</th>
                        <th width="80">Length (L)</th>
                        <th width="80">Width (W)</th>
                        <th width="80">Height (H)</th>
                        <th width="80">Flap (F)</th>
                        <th width="80">Gusset (G)</th>
                        <th>Thickness</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <?
                            if(isset($s_data[7]))
                                $selected = $s_data[7];
                            else
                                $selected = 25;
                            $mesurement = array_intersect_key($unit_of_measurement, array(25=>'', 26=>'', 29=> ''));
                            echo create_drop_down("s_measuremnt_id", 80, $mesurement, "", 0, "", $selected, "");
                            ?>
                        </td>
                        <td>
                            <input type="text" id="s_length" name="s_length" class="text_boxes_numeric " style="width:70px" value="<? echo isset($s_data[1])  && $s_data[1] != 0 ? $s_data[1] : ''; ?>"  >
                        </td>
                        <td>
                            <input type="text" id="s_width" name="s_width" class="text_boxes_numeric " style="width:70px" value="<? echo isset($s_data[2]) && $s_data[2] != 0 ? $s_data[2] : ''; ?>"  >
                        </td>
                        <td>
                            <input type="text" id="s_height" name="s_height" class="text_boxes_numeric " style="width:70px" value="<? echo isset($s_data[3])  && $s_data[3] != 0 ? $s_data[3] : ''; ?>"  >
                        </td>
                        <td>
                            <input type="text" id="s_flap" name="s_flap" class="text_boxes_numeric " style="width:70px" value="<? echo isset($s_data[4])  && $s_data[4] != 0 ? $s_data[4] : ''; ?>"  >
                        </td>
                        <td>
                            <input type="text" id="s_gusset" name="s_gusset" class="text_boxes_numeric " style="width:70px" value="<? echo isset($s_data[5])  && $s_data[5] != 0 ? $s_data[5] : ''; ?>"  >
                        </td>
                        <td>
                            <input type="text" id="s_thickness" name="s_thickness" class="text_boxes " style="max-width:80px" value="<? echo isset($s_data[6]) ? $s_data[6] : ''; ?>"  >
                            <input type="hidden" id="s_id" name="s_id"  value="<? echo isset($s_data[0]) ? $s_data[0] : ''; ?>"  >
                            <input type="hidden" id="s_subcon_order_brk_id" name="s_subcon_order_brk_id"  value="<? echo isset($s_data[8]) ? $s_data[8] : ''; ?>"  >
                        </td>
                    </tr>
                    </tbody>

                </table>
                <table>
                    <tr>
                        <td align="center"><input type="button" name="main_close" class="formbutton" value="Close" id="" onClick="size_fnc_close(<?=$row?>);" style="width:100px" /></td>
                    </tr>
                </table>
            </form>
        </div>
        </body>
        <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
        </html>
        <?
        exit();
    }

if($action=="check_conversion_rate")
{
	$data=explode("**",$data);
	
	/*if($db_type==0)
	{
		$conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
	}
	else
	{
		$conversion_date=change_date_format($data[1], "d-M-y", "-",1);
	}*/
	$conversion_date=date("Y/m/d");
	$exchange_rate=set_conversion_rate( $data[0], $conversion_date,$data[1] );
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
	
	//echo "select uom_id from lib_booked_uom_setup where company_id=$data[0] and section_id=$data[1] and sub_section_id=$data[2] and status_active = 1 and is_deleted=0 ";  
	$uom=return_field_value( "uom_id","lib_booked_uom_setup","company_id=$data[0] and section_id=$data[1] and sub_section_id=$data[2] and status_active = 1 and is_deleted=0");
	echo $uom;
	exit();	
}

if($action=="section_variable")
{
	$data=explode("**",$data);
	 $sql = "select id, work_order_number_control from variable_setting_trim_marketing where company_name=$data[1] and status_active=1 and is_deleted=0 and variable_list=2"; 
	 $sectionvariable = sql_select($sql);
 	echo $sectionvariable[0][csf('work_order_number_control')];
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
	$color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name" );
	$size_arr=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0",'id','size_name');
	$tblRow=0;

	
	
	//$prod_rec_dtls_ids=array_unique(explode(",",$prodRecdtlSID_arr));
	//$prod_rec_brk_ids=array_unique(explode(",",$prodRecdBrktlSID_arr));
	//unset($buyer_po_sql);
	//echo "<pre>";
	//print_r($prod_rec_dtls_ids);
	$subcon_arr=array(); $subcon_brk_arr=array(); $dtlSID_arr=array(); $breakIDarr=array();
	$subcon_sql ="select a.id, a.subcon_job, a.company_id, a.location_id, a.party_id, a.receive_date, b.order_no, a.delivery_date , b.id as subDtlsID,b.booking_dtls_id, b.order_id, b.booked_qty,c.id as subBrkID,c.book_con_dtls_id
	from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
	where a.entry_form=255 and a.within_group=1 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 and b.id=c.mst_id and b.order_no=trim('$data[1]') 
	order by a.id DESC";
	$subcon_sql_res = sql_select($subcon_sql);
	foreach($subcon_sql_res as $row)
	{
		$dtlSID_arr[$row[csf('subDtlsID')]]=$row[csf('subDtlsID')];
		$dtlSID_arr_qty[$row[csf('subDtlsID')]]['booked_qty']=$row[csf('booked_qty')];
		$breakIDarr[$row[csf('subBrkID')]]=$row[csf('subBrkID')];
		if($row[csf('booking_dtls_id')]!='')
		{
			$subcon_arr[$row[csf('order_no')]][$row[csf('booking_dtls_id')]]['subDtlsID']=$row[csf('subDtlsID')];
		}
		$subcon_brk_arr[$row[csf('booking_dtls_id')]][$row[csf('book_con_dtls_id')]]['subBrkID']=$row[csf('subBrkID')];
	}
	//unset($buyer_po_sql);
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
		$sql = "SELECT  a.id, a.booking_type, a.booking_no, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id, b.id as booking_dtls_id, a.po_break_down_id, b.trim_group,b.delivery_date,b.fabric_description, b.gmts_color as gmts_color_id, b.fabric_color, b.gmts_size as gmts_size_id, b.item_size, b.uom, b.trim_qty as wo_qnty, b.rate, b.amount
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
				if($rows[csf('gmts_color_id')]=="") $rows[csf('gmts_color_id')]=0;
				if($rows[csf('gmts_size_id')]=="") $rows[csf('gmts_size_id')]=0;
				if($row[csf('fabric_color')]=="") $row[csf('fabric_color')]=0;
				if($row[csf('item_size')]=="") $row[csf('item_size')]=0;
				if($row[csf('wo_qnty')]=="") $row[csf('wo_qnty')]=0;
				if($row[csf('rate')]=="") $row[csf('rate')]=0;
				if($row[csf('amount')]=="") $row[csf('amount')]=0;
				if($break_down_arr[$row[csf('id')]]=="") $break_down_arr[$row[csf('id')]]=0;
				//echo $data_break."++";
				if($data_break=="") $data_break.=$row[csf('fabric_description')].'_'.$color_library[$row[csf('fabric_color')]].'_'.$size_arr[$row[csf('item_size')]].'_'.$row[csf('wo_qnty')].'_'.$row[csf('rate')].'_'.$row[csf('amount')].'_'.$row[csf('id')].'_'.$break_down_arr[$row[csf('id')]].'_'.$row[csf('fabric_color')].'_'.$row[csf('item_size')].'_'.$color_library[$row[csf('gmts_color_id')]].'_'.$size_arr[$row[csf('gmts_size_id')]].'_'.$row[csf('gmts_color_id')].'_'.$row[csf('gmts_size_id')];
				else $data_break.='***'.$row[csf('fabric_description')].'_'.$color_library[$row[csf('fabric_color')]].'_'.$size_arr[$row[csf('item_size')]].'_'.$row[csf('wo_qnty')].'_'.$row[csf('rate')].'_'.$row[csf('amount')].'_'.$row[csf('id')].'_'.$break_down_arr[$row[csf('id')]].'_'.$row[csf('fabric_color')].'_'.$row[csf('item_size')].'_'.$color_library[$row[csf('gmts_color_id')]].'_'.$size_arr[$row[csf('gmts_size_id')]].'_'.$row[csf('gmts_color_id')].'_'.$row[csf('gmts_size_id')];
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
				$sql = "select  id, wo_trim_booking_dtls_id, job_no,  po_break_down_id, color_number_id as gmts_color_id, gmts_sizes as gmts_size_id, item_color, item_size, requirment, description, brand_supplier, rate, amount from wo_trim_book_con_dtls where wo_trim_booking_dtls_id='$booking_dtls_id' and status_active=1 and is_deleted=0 and requirment!=0 order by id ASC"; //die;
				$breakData_arr=sql_select($sql);
				//$data_break="";
				$subBrkID='';
				foreach($breakData_arr as $rows)
				{//echo "1--";
					if($rows[csf('description')]=="") $rows[csf('description')]=0;
					if($rows[csf('gmts_color_id')]=="") $rows[csf('gmts_color_id')]=0;
					if($rows[csf('gmts_size_id')]=="") $rows[csf('gmts_size_id')]=0;
					if($rows[csf('item_color')]=="") $rows[csf('item_color')]=0;
					if($rows[csf('item_size')]=="") $rows[csf('item_size')]=0;
					if($rows[csf('requirment')]=="") $rows[csf('requirment')]=0;
					if($rows[csf('rate')]=="") $rows[csf('rate')]=0;
					if($rows[csf('amount')]=="") $rows[csf('amount')]=0;
					if($break_down_arr[$rows[csf('id')]]=="") $break_down_arr[$rows[csf('id')]]=0;
					$subBrkID=$subcon_brk_arr[$rows[csf('wo_trim_booking_dtls_id')]][$rows[csf('id')]]['subBrkID'];
					//echo $subBrkID."++";
					if($data_break=="") $data_break.=$rows[csf('description')].'_'.$color_library[$rows[csf('item_color')]].'_'.$rows[csf('item_size')].'_'.$rows[csf('requirment')].'_'.$rows[csf('rate')].'_'.$rows[csf('amount')].'_'.$rows[csf('id')].'_'.$subBrkID.'_'.$rows[csf('item_color')].'_'.$rows[csf('item_size')].'_'.$color_library[$rows[csf('gmts_color_id')]].'_'.$size_arr[$rows[csf('gmts_size_id')]].'_'.$rows[csf('gmts_color_id')].'_'.$rows[csf('gmts_size_id')];
					else $data_break.='***'.$rows[csf('description')].'_'.$color_library[$rows[csf('item_color')]].'_'.$rows[csf('item_size')].'_'.$rows[csf('requirment')].'_'.$rows[csf('rate')].'_'.$rows[csf('amount')].'_'.$rows[csf('id')].'_'.$subBrkID.'_'.$rows[csf('item_color')].'_'.$rows[csf('item_size')].'_'.$color_library[$rows[csf('gmts_color_id')]].'_'.$size_arr[$rows[csf('gmts_size_id')]].'_'.$rows[csf('gmts_color_id')].'_'.$rows[csf('gmts_size_id')];

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

					if($row[csf('section')]==1) $subID='1,2,3,23,29,30,31,32,33,34,35,36,37,38,39,40';
					else if($row[csf('section')]==3) $subID='4,5,18';
					else if($row[csf('section')]==5) $subID='6,7,8,9,10,11,12,13,16,17,24';
					else if($row[csf('section')]==10) $subID='14,15';
					else if($row[csf('section')]==7) $subID='19,20,21,25,26,27,28,41';
					else if($row[csf('section')]==9) $subID='22';
					else $subID='0';
					?>
				</td>
				<td><? echo create_drop_down( "cboSection_".$tblRow, 90, $trims_section,"", 1, "-- Select Section --",$row[csf('section')],"load_sub_section($tblRow)",0,'','','','','','',"cboSection[]"); ?></td>			
				<td id="subSectionTd_<? echo $tblRow; ?>"><? echo create_drop_down( "cboSubSection_".$tblRow, 90, $trims_sub_section,"", 1, "-- Select Section --",$row[csf('sub_section')],"load_sub_section_value($tblRow)",0,$subID,'','','','','',"cboSubSection[]"); ?></td>			
				<td id="itemGroupTd_<? echo $tblRow; ?>"><? echo create_drop_down( "cboItemGroup_".$tblRow, 90, "select id, item_name from lib_item_group where item_category=4 and status_active=1","id,item_name", 1, "-- Select --",$row[csf('trim_group')], "",$disable_dropdown,'','','','','','',"cboItemGroup[]"); ?></td>
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
				<td><input id="txtBookQty_<? echo $tblRow; ?>" name="txtBookQty[]" type="text"  class="text_boxes_numeric" style="width:57px"  value="<? echo number_format($row[csf('booked_qty')],4,'.',''); ?>" readonly /></td>
				<td><input id="txtRate_<? echo $tblRow; ?>" name="txtRate[]" value="<? echo number_format($row[csf('rate')],4,'.',''); ?>" type="text"  class="text_boxes_numeric" style="width:60px" readonly/></td>
				<td><input id="txtAmount_<? echo $tblRow; ?>" name="txtAmount[]"  value="<? echo number_format($row[csf('amount')],4,'.',''); ?>" type="text" style="width:70px"  class="text_boxes_numeric"  disabled /></td>
				<td><input id="txtDomRate_<? echo $tblRow; ?>" name="txtDomRate[]" value="<? echo number_format($domRate,4,'.',''); ?>" type="text"  class="text_boxes_numeric" style="width:57px" readonly /></td>
				<td><input id="txtDomamount_<? echo $tblRow; ?>" name="txtDomamount[]" value="<? echo number_format($domAmount,4,'.',''); ?>" type="text"  class="text_boxes_numeric" style="width:77px" readonly /></td>

				<td><input type="text"  id="txtSamSubDate_<? echo $tblRow; ?>" name="txtSamSubDate[]" value="<? echo change_date_format($row[csf("submit_date")]);?>" class="datepicker" style="width:67px"  /></td>
				<td><input type="text"  id="txtSamApprDate_<? echo $tblRow; ?>" name="txtSamApprDate[]" value="<? echo change_date_format($row[csf("approve_date")]);?>" class="datepicker" style="width:67px"  /></td>

				<td><input type="text"  id="txtOrderDeliveryDate_<? echo $tblRow; ?>" name="txtOrderDeliveryDate[]" value="<? echo change_date_format($row[csf("delivery_date")]);?>" class="datepicker" onChange="chk_min_del_date(<? echo $tblRow; ?>); dateCopy(<? echo $tblRow; ?>);" style="width:67px"  />
					<input id="hdnDtlsUpdateId_<? echo $tblRow; ?>" name="hdnDtlsUpdateId[]" type="hidden" value="<? echo $dtlsID; ?>" class="text_boxes_numeric" style="width:40px" />
					<input type="hidden" id="hdnDtlsdata_<? echo $tblRow; ?>" name="hdnDtlsdata[]" value="<? echo $data_break; ?>">
	                <input type="hidden" id="hdnbookingDtlsId_<? echo $tblRow; ?>" name="hdnbookingDtlsId[]" value="<? echo $row[csf('booking_dtls_id')]; ?>">
	                <input type="hidden" id="txtDeletedId_<? echo $tblRow; ?>" name="txtDeletedId[]" value="">
	                <input type="hidden" id="txtIsWithOrder_<? echo $tblRow; ?>" name="txtIsWithOrder[]" value="<? echo $isWithOrder; ?>">
					<input type="hidden" name="txtIsDuplicate[]" id="txtIsDuplicate_<? echo $tblRow; ?>" value="0">
	                <input type="hidden" id="txtIsRevised_<? echo $tblRow; ?>" name="txtIsRevised[]" value="<? echo $row[csf("is_revised")]; ?>">
	                <input type="hidden" id="nextProcessChk_<? echo $tblRow; ?>" name="nextProcessChk[]" value="<? echo $next_proc_disabled_dropdown; ?>">
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
            <td id="itemGroupTd_1"><? echo create_drop_down( "cboItemGroup_1", 90, "select id, item_name from lib_item_group where item_category=4 and  status_active=1","id,item_name", 1, "-- Select --",$selected, "",0,'','','','','','',"cboItemGroup[]"); ?>	</td>
            <td><? echo create_drop_down( "cboUom_1", 60, $unit_of_measurement,"", 1, "-- Select --",2,1, 1,'','','','','','',"cboUom[]"); ?>	</td>
            <td><input id="txtOrderQuantity_1" name="txtOrderQuantity[]" class="text_boxes_numeric" type="text"  style="width:60px" onClick="openmypage_order_qnty(1,'0',1)" placeholder="Click To Search" readonly /></td>
            <td><input id="txtRate_1" name="txtRate[]" type="text"  class="text_boxes_numeric" style="width:60px" readonly /></td>
            <td><input id="txtAmount_1" name="txtAmount[]" type="text" style="width:70px"  class="text_boxes_numeric" readonly /></td> 
            <td><input id="txtDomRate_1" name="txtDomRate[]" type="text"  class="text_boxes_numeric" style="width:57px" readonly /></td> 
            <td><input id="txtDomamount_1" name="txtDomamount[]" type="text"  class="text_boxes_numeric" style="width:77px" readonly  /></td> 

            <td><input type="text"  id="txtSamSubDate_<? echo $tblRow; ?>" name="txtSamSubDate[]" value="<? echo change_date_format($row[csf("submit_date")]);?>" class="datepicker" style="width:67px"  /></td>
			<td><input type="text"  id="txtSamApprDate_<? echo $tblRow; ?>" name="txtSamApprDate[]" value="<? echo change_date_format($row[csf("approve_date")]);?>" class="datepicker" style="width:67px"  /></td>

            <td><input type="text" name="txtOrderDeliveryDate[]" id="txtOrderDeliveryDate_1" class="datepicker"  onChange="chk_min_del_date(1); dateCopy(1);"  style="width:67px" />
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
if($action=="trims_order_receive_print")
{
	//select id, item_name from lib_item_group where item_category=4 and status_active=1
	extract($_REQUEST);
	$data=explode('*',$data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$trim_group_arr=return_library_array( "select id, item_name from lib_item_group where item_category=4",'id','item_name');
	$leader_name_arr=return_library_array( "select id, team_leader_name from lib_marketing_team",'id','team_leader_name');
	$member_name_arr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
	//$store_name_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$source_for_order = array(1 => 'In-House', 2 => 'Sub-Contract');
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");

	if($data[3]==1)
	{
		$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	}
	else
	{
		
		$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	}

	$sql = "SELECT id, mst_id, job_no_mst, order_id, order_no, buyer_po_id as po_break_down_id, booking_dtls_id, order_quantity as wo_qnty, order_uom, rate, amount, submit_date, approve_date, delivery_date, buyer_po_no, buyer_style_ref, buyer_buyer, section,sub_section, item_group as trim_group, rate_domestic,  amount_domestic , is_with_order, booked_uom, booked_conv_fac, booked_qty,is_revised,source_for_order from subcon_ord_dtls where mst_id =$data[1] and status_active=1 and is_deleted=0 order by id ASC";
	$qry_result=sql_select($sql);

	foreach ($qry_result as  $row) 
	{
		$wo_arr[$row[csf("order_uom")]][$row[csf("id")]]["buyer_po_no"] =$row[csf("buyer_po_no")];
		$wo_arr[$row[csf("order_uom")]][$row[csf("id")]]["buyer_style_ref"] =$row[csf("buyer_style_ref")];
		$wo_arr[$row[csf("order_uom")]][$row[csf("id")]]["buyer_buyer"] =$row[csf("buyer_buyer")];
		$wo_arr[$row[csf("order_uom")]][$row[csf("id")]]["section"] =$row[csf("section")];
		$wo_arr[$row[csf("order_uom")]][$row[csf("id")]]["sub_section"] =$row[csf("sub_section")];
		$wo_arr[$row[csf("order_uom")]][$row[csf("id")]]["trim_group"] =$row[csf("trim_group")];
		$wo_arr[$row[csf("order_uom")]][$row[csf("id")]]["wo_qnty"] =$row[csf("wo_qnty")];
		$wo_arr[$row[csf("order_uom")]][$row[csf("id")]]["rate"] =$row[csf("rate")];
		$wo_arr[$row[csf("order_uom")]][$row[csf("id")]]["amount"] =$row[csf("amount")];
		$wo_arr[$row[csf("order_uom")]][$row[csf("id")]]["source_for_order"] =$row[csf("source_for_order")];
	}

	$sql_mst="SELECT id, subcon_job, company_id, location_id, party_id, currency_id, party_location, delivery_date, rec_start_date, rec_end_date, receive_date, within_group, party_location, order_id, order_no,exchange_rate,team_leader,team_member,team_marchant, trims_ref,remarks,is_apply_last_update,revise_no, inserted_by from subcon_ord_mst where id=$data[1] and entry_form=255 and status_active=1";
	$dataArray=sql_select($sql_mst);
	$inserted_by=$dataArray[0]['INSERTED_BY'];
	$com_dtls = fnc_company_location_address($data[0], 0, 2);
	//echo "<pre>";
	//print_r($wo_arr);
	//die;

	?>
	<style type="text/css">
		td.make_bold {
	  		font-weight: 900;
		}
	</style>
	<div style="width:1100px;">
		<table width="1100" cellspacing="0" align="center" border="0">
			<tr>
            	<td  align="left"><img src="../../<? echo $com_dtls[2]; ?>" height="70" width="200"></td>
            	<td colspan="5" align="center"  style="font-size:xx-large; text-align:left;"><strong ><? echo $com_dtls[0]; ?></strong>
        	</tr>
	        <tr class="form_caption">
	            <td colspan="6" align="center"><? echo $com_dtls[1]; ?> </td>
	        </tr>
	        <tr>
	            <td colspan="6" style="font-size:large; text-align:center;" align="center"><strong ><? echo "Order Receive"; ?></strong> </td>
	        </tr>
		</table>
		<br>
		<table width="1100" cellspacing="0" align="center" border="0">
			
			<tr>			
				<td width="120" class="make_bold">Order Rcv ID : </td> <td width="175" class="make_bold"><? echo $dataArray[0][csf('subcon_job')]; ?></td>
				<td width="120" class="make_bold">Receive Date : </td> <td width="175" class="make_bold"><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
				<td width="120" class="make_bold">Delivery Date : </td> <td width="175" class="make_bold"><? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
			</tr>
			<tr>
				<td width="120">Party Name : </td> <td width="175"><? echo $party_arr[$dataArray[0][csf('party_id')]]; ?></td>
				<td width="120">Work Order no : </td> <td width="175"><? echo $dataArray[0][csf('order_no')]; ?></td>
				<td width="120">Team Leader : </td> <td width="175"><? echo $leader_name_arr[$dataArray[0][csf('team_leader')]]; ?></td>
			</tr>
			<tr>
				<td width="120">Currency : </td> <td width="175"><? echo $currency[$dataArray[0][csf('currency_id')]]; ?></td>
				<td width="120">Exchange Rate : </td> <td width="175"><? echo $dataArray[0][csf('exchange_rate')]; ?></td>
				<td width="120">Team Member : </td> <td width="175"><? echo $member_name_arr[$dataArray[0][csf('team_member')]]; ?></td>
			</tr>
			<tr>
				<td width="120">Remarks : </td> <td><? echo $dataArray[0][csf('remarks')]; ?></td>
				<td width="120">Factory Merchant : </td> <td width="175"><? echo $dataArray[0][csf('team_marchant')]; ?></td>
				<td width="120">Trims Ref. : </td> <td width="175"><? echo $dataArray[0][csf('trims_ref')]; ?></td>
			</tr>
		</table>
		<br>
		<div style="width:100%;">
			<table align="left" cellspacing="0" width="1100"  border="1" rules="all" class="rpt_table"  >
				<thead>
					<tr>
		        		<th width="110">Buyer PO NO.</th>
		                <th width="110" >Buyer Style Reff.</th>
		                <th width="110">Buyer's Buyer</th>
		                <th width="110">Section</th>
		                <th width="110">Sub-Section</th>
		                <th width="110">Trims Group</th>
		                <th width="60">Order UOM</th>
		                <th width="70">Order Qty</th>
		                <th width="60">Rate</th>
		                <th width="80">Amount</th>
		                <th>Source</th>
		        	</tr>
				</thead>
				<tbody>
				<?
				$tblRow=1; $i=1;
				foreach($wo_arr as $uom_id=> $uom_data)
				{
					$uom_wise_qnty=0; $uom_wise_amt=0;
					foreach($uom_data as $id=> $row)
					{
						$qnty=$row['wo_qnty'];
						$amt=$row['amount'];
						$uom_wise_qnty +=$qnty;
						$uom_wise_amt +=$amt;
						if($data[3]==1) $buyer_buyer=$buyer_arr[$row['buyer_buyer']]; else $buyer_buyer=$row['buyer_buyer'];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
							<td style="word-break:break-all" width="110"><?  echo $row['buyer_po_no'] ; ?></td>
			                <td style="word-break:break-all" width="110" ><?  echo $row['buyer_style_ref'] ; ?></td>
			                <td style="word-break:break-all" width="110"><?  echo $buyer_buyer ; ?></td>
			                <td style="word-break:break-all" width="110"><?  echo $trims_section[$row['section']] ; ?></td>
			                <td style="word-break:break-all" width="110"><?  echo $trims_sub_section[$row['sub_section']] ; ?></td>
			                <td width="110"><?  echo $trim_group_arr[$row['trim_group']] ; ?></td>
			                <td width="60"><?  echo $unit_of_measurement[$uom_id] ; ?></td>
			                <td width="70" align="right"><?  echo number_format($qnty,4) ; ?></td>
			                <td width="60" align="right"><?  echo number_format($row['rate'],4) ; ?></td>
			                <td width="80" align="right"><?  echo number_format($amt,4) ; ?></td>
			                <td><?  echo $source_for_order[$row['source_for_order']] ; ?></td>
						</tr>
						<?
						$tblRow++; 
					}
					?> 
					<tr>
						<td colspan="7" align="right"><strong>UOM Total:</strong></td>
						<td align="right" class="make_bold"><p><?  echo number_format($uom_wise_qnty,4) ; ?></p></td>
						<td >&nbsp;</td>
						<td align="right" class="make_bold"><p><?  echo number_format($uom_wise_amt,4) ; ?></p></td>
						<td >&nbsp;</td>
					</tr><? 
				}
				?>
				</table>
			</div>
		<br>
		<div style="width: 500px ;  margin-top:5px; float: left;"  align="left">
			<? 
			   	echo get_spacial_instruction($data[1],"115%",255);
			?>
		</div>
		<br>
	</div>
	<?
		$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
		echo signature_table(212,$data[0],"1100px",$template_id,10,$user_lib_name[$inserted_by]);
    ?>
</div>
<?
exit();
}


if($action=="trims_order_receive_print_2")
{
	//select id, item_name from lib_item_group where item_category=4 and status_active=1
	extract($_REQUEST);
	$data=explode('*',$data);
	$rate_cond=$data[4];
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$trim_group_arr=return_library_array( "select id, item_name from lib_item_group where item_category=4",'id','item_name');
	$leader_name_arr=return_library_array( "select id, team_leader_name from lib_marketing_team",'id','team_leader_name');
	$member_name_arr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
	//$store_name_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$source_for_order = array(1 => 'In-House', 2 => 'Sub-Contract');
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");

	if($data[3]==1)
	{
		$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	}
	else
	{
		$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	}

	$sql_mst="SELECT id, subcon_job, company_id, location_id, party_id, currency_id, approved, party_location, delivery_date, rec_start_date, rec_end_date, receive_date, within_group, party_location, order_id, order_no,exchange_rate,team_leader,team_member,team_marchant,trims_ref,remarks,is_apply_last_update,revise_no, delivery_point,update_date,inserted_by, buyer_tb, buying_merchant
	from subcon_ord_mst
	where id=$data[1] and entry_form=255 and status_active=1";
   //    echo $sql_mst; die;
	$dataArray=sql_select($sql_mst);
	$inserted_by=$dataArray[0][csf("inserted_by")];
	$sql= "SELECT b.id as receive_details_id, b.buyer_po_no, b.buyer_style_ref, b.buyer_buyer, b.section,b.sub_section, b.item_group as trim_group, b.source_for_order , b.order_uom ,a.id, a.description, a.color_id, a.size_id, a.qnty, a.rate , a.amount, a.style, b.sub_section,b.booked_uom, b.booked_conv_fac, a.booked_qty, a.gmts_color_id, a.gmts_size_id  from subcon_ord_dtls b ,subcon_ord_breakdown a where a.mst_id=b.id  and b.job_no_mst=a.job_no_mst and b.mst_id =$data[1] and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.booked_qty is not null and a.booked_qty!=0  and a.status_active=1 order by b.id,a.id";
	$qry_result=sql_select($sql);

	$color_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$size_arr=return_library_array( "select id,size_name from  lib_size where status_active=1 and is_deleted=0",'id','size_name');
	$country_full_name = return_library_array("SELECT id,country_name from lib_country", "id", "country_name");
	$company_sql = "SELECT * FROM lib_company WHERE id = $data[0]  AND is_deleted=0 AND status_active=1 ORDER BY company_name ASC";
	$result = sql_select( $company_sql );
	foreach( $result as $row  )
	{
		if($row[csf("level_no")])		$level_no	= $row[csf("level_no")].', ';
		if($row[csf("plot_no")])		$plot_no 	= $row[csf("plot_no")].', ';
		if($row[csf("road_no")]) 		$road_no 	= $row[csf("road_no")].', ';
		if($row[csf("block_no")]!='')	$block_no 	= $row[csf("block_no")].', ';
		if($row[csf("zip_code")]!='')	$zip_code 	= $row[csf("zip_code")].', ';
		if($row[csf("city")]!='') $city 			= $row[csf("city")];
		if($row[csf("country_id")]!='')	$country 	= $country_full_name[$row[csf("country_id")]].'.';
		if($row[csf("contact_no")]!='')	$contact_no = $row[csf("contact_no")];
		if($row[csf("email")]!='')		$email 		= $row[csf("email")];
		if($row[csf("website")]!='')	$website 	= $row[csf("website")].'.'; 
	}
	$head_oofice= "House #".$plot_no."Road #".$road_no."Sector #".$block_no.$zip_code.$country;
	$company_address="Head Office :".$head_oofice.'</br> Factory address : '.$city.'</br> Email : '.$email.'</br> Mobile : '.$contact_no;
	$com_dtls = fnc_company_location_address($data[0], 0, 2);
	$currency_id = $dataArray[0][csf("currency_id")];
	if($currency_id==2) $usd_taka='(USD)'; else $usd_taka='(Taka)';
	//echo "<pre>";
	//print_r($wo_arr);
	//die;
	if($db_type==0){
		$lib_currency_data=sql_select("select conversion_rate from currency_conversion_rate where company_id=$data[0] and currency=2 order by id desc limit 1");
	}else{
		$lib_currency_data=sql_select("select conversion_rate from currency_conversion_rate where company_id=$data[0] and currency=2 and rownum<2 order by id desc");
	}
	$currency_conversion_rate=$lib_currency_data[0][csf("conversion_rate")];
	if($rate_cond ==1) $width=1450; else $width=1270;
	?>
	<style type="text/css">
		td.make_bold {
	  		font-weight: 900;
		}
	</style>
	<div style="width:<? echo $width.'px'; ?>;">
		<table width="<? echo $width; ?>" cellspacing="0" align="center" border="0">
			<tr>
            	<td  align="left"><img src="../../<? echo $com_dtls[2]; ?>" height="70" width="200"></td>
            	<td colspan="5" align="center"  style="font-size:xx-large; text-align:left;"><strong ><? echo $com_dtls[0]; ?></strong>
        	</tr>
	        <tr class="form_caption">
	            <td colspan="6" align="center"><? echo $company_address; ?> </td>
	        </tr>
	        <tr>
	            <td colspan="6" style="font-size:xx-large; text-align:center;" align="center"><strong >Order Receive Booking </strong> </td>
	        </tr>
		</table>
		<br>
		<table width="<? echo $width; ?>" cellspacing="0" align="center" border="0">
			
			<tr>			
				<td width="120" class="make_bold">Order Rcv ID </td> <td width="175" class="make_bold"><? echo " : ".$dataArray[0][csf('subcon_job')]; ?></td>
				<td width="120" class="make_bold">Receive Date </td> <td width="175" class="make_bold"><? echo " : ".change_date_format($dataArray[0][csf('receive_date')]); ?></td>
				<td width="120" class="make_bold">Revised W/O</td> <td width="175" class="make_bold"><? echo " : ".$dataArray[0][csf('revise_no')]; ?></td>
			</tr>
			<tr>
				<td width="120">Party Name </td> <td width="175"><? echo " : ".$party_arr[$dataArray[0][csf('party_id')]]; ?></td>
				<td width="120">Work Order no </td> <td width="175"><? echo " : ".$dataArray[0][csf('order_no')]; ?></td>
				<td width="120" class="make_bold">Last Revised Date</td> <td width="175" class="make_bold"><? echo " : ".change_date_format($dataArray[0][csf('update_date')]); ?></td>
			</tr>
			<tr>
				<td width="120">Currency </td> <td width="175"><? echo " : ".$currency[$dataArray[0][csf('currency_id')]]; ?></td>
				<td width="120">Exchange Rate </td> <td width="175"><? echo " : ".$dataArray[0][csf('exchange_rate')]; ?></td>
				<td width="120" class="make_bold">Delivery Date</td> <td width="175" class="make_bold"><? echo " : ".change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
			</tr>
			<tr>
				
				<td width="120">Team Leader </td> <td ><? echo " : ".$leader_name_arr[$dataArray[0][csf('team_leader')]]; ?></td>
				<td width="120">Team Member </td> <td width="175"><? echo " : ".$member_name_arr[$dataArray[0][csf('team_member')]]; ?></td>
				<td width="120">Factory Merchant </td> <td width="175"><? echo " : ".$dataArray[0][csf('team_marchant')]; ?></td>
			</tr>
			<tr>
				<td width="120">Delivery Point </td> <td><? echo " : ".$dataArray[0][csf('delivery_point')]; ?></td>
				<td width="120">Remarks </td> <td ><? echo " : ".$dataArray[0][csf('remarks')]; ?></td>
				<td width="120">Buyers TB</td> <td width="175"><? echo " : ".$dataArray[0][csf('buyer_tb')]; ?></td>
			</tr>
			<tr>
				<td width="120">Buying Merchant </td> <td><? echo " : ".$dataArray[0][csf('buying_merchant')]; ?></td>
				
			</tr>
		</table>
		<br>
		<div style="width:100%;">
			<table align="left" cellspacing="0" width="<? echo $width; ?>"  border="1" rules="all" class="rpt_table"  >
				<thead>
					<tr>
                        <th width="30">SL No.</th>
		        		<th width="100">PO No.</th>
		                <th width="100">Buyer's Buyer</th>
		                <th width="80">Section</th>
		                <th width="100">Sub-Section</th>
		                <th width="100">Trims Group</th>
		                <th width="100">Style No.</th>
		                <th width="100">Item Description</th>
						<th width="80">Gmts Color</th>
		                <th width="80">Gmts Size</th>
		                <th width="80">Item Color</th>
		                <th width="80">Item Size</th>
		                <th width="60">Order UOM</th>
		                <th width="80">Order Qty</th>
		                <? if($rate_cond==1){
		                ?>	<th width="60"> Rate <? echo $usd_taka ; ?></th>
		                	<th width="80"> Amount <? echo $usd_taka ; ?></th>
		                <?
		                }?>
		                <th>Source</th>
		        	</tr>
				</thead>
				<tbody>
				<?
				$tblRow=1; $i=1;
				$total_amount=0; $total_qnty=0;
				foreach($qry_result as $row)
				{
					
					$rate=$row[csf("rate")];
					$amount=$row[csf("amount")];
					//$title=1;
					if($data[3]==1) $buyer_buyer=$buyer_arr[$row[csf("buyer_buyer")]]; else $buyer_buyer=$row[csf("buyer_buyer")];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
                        <td align="center" width="30"><?=$tblRow?></td>
						<td style="word-break: break-word;" width="100"><p><?  echo $row[csf("buyer_po_no")] ; ?></p></td>
		                <td style="word-break: break-word;" width="100"><p><?  echo $buyer_buyer ; ?></p></td>
		                <td style="word-break: break-word;" width="80"><p><?  echo $trims_section[$row[csf("section")]] ; ?></p></td>
		                <td style="word-break: break-word;" width="100"><p><?  echo $trims_sub_section[$row[csf("sub_section")]] ; ?></p></td>
		                <td style="word-break: break-word;" width="100"><p><?  echo $trim_group_arr[$row[csf("trim_group")]] ; ?></p></td>
		                <td style="word-break: break-word;" width="100"><p><?  echo $row[csf("style")] ; ?></p></td>
		                <td style="word-break: break-word;" width="100"><p><?  echo $row[csf("description")] ; ?></p></td>
						<td style="word-break: break-word;" width="80"><p><?  echo $color_arr[$row[csf("gmts_color_id")]] ; ?></p></td>
						<td style="word-break: break-word;" width="80"><p><?  echo $size_arr[$row[csf("gmts_size_id")]] ; ?></p></td>
		                <td style="word-break: break-word;" width="80"><p><?  echo $color_arr[$row[csf("color_id")]] ; ?></p></td>
		                <td style="word-break: break-word;" width="80"><p><?  echo $size_arr[$row[csf("size_id")]] ; ?></p></td>
		                <td width="60"><?  echo $unit_of_measurement[$row[csf("order_uom")]] ; ?></td>
		                <td width="80" align="right"><?  echo number_format($row[csf("qnty")],4) ; ?></td>
		                <? if($rate_cond==1){
		                ?>	<td width="60" align="right"><?  echo number_format($rate,4) ; ?></td>
		                	<td width="80" align="right"><?  echo number_format($amount,4) ; ?></td>
		                <?
		                }?>
		                <td><?  echo $source_for_order[$row[csf("source_for_order")]] ; ?></td>
					</tr>
					<?
					$total_amount+=$amount;
					$total_qnty+=$row[csf("qnty")];
					$tblRow++; 
				}
				$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');
				$currency_sign = $currency_sign_arr[$dataArray[0][csf("currency_id")]];
				
				//$mcurrency, $dcurrency;
				$dcurrency="";
				if($currency_id==1){
					$mcurrency='Taka';
					$dcurrency='Paisa';
				}else if($currency_id==2){
					$mcurrency='USD';
					$dcurrency='CENTS';
				}else if($currency_id==3){
					$mcurrency='EURO';
					$dcurrency='CENTS';
				}
				?>
				<? if($rate_cond ==1)
				{
				?> 
				<tr>
					<td colspan="13" align="right"><strong>Total:</strong></td>
					<td align="right" class="make_bold"><p><?  echo number_format($total_qnty,4) ; ?></p></td>
					 <td>&nbsp;</td>
					<td align="right" class="make_bold"><p><?  echo number_format($total_amount,4) ; ?></p></td>
					<td >&nbsp;</td>
				</tr>
				<?
				}else{
					?> 
				<tr>
					<td colspan="13" align="right"><strong>Total:</strong></td>
					<td align="right" class="make_bold"><p><?  echo number_format($total_qnty,4) ; ?></p></td>
					<td >&nbsp;</td>
				</tr>
				<?
				}
				?>
				</table>
			</div>
		<br>
		<? if($rate_cond ==1)
		{
		?>
		    <div style="width: 1060px ;  margin-top:10px; float: left;"  align="left">
				<table style="border: 1px;">
					<tr>
						<td><p><strong>Total Work Order Amount (In Word) : <? echo number_to_words(number_format($total_amount,2), $mcurrency, $dcurrency);?></strong></p></td>
					</tr>
				</table>
			</div>
		<?
		}?>
		<br>
	</div>
            <div style="width: 1060px ;  padding-top:10px; clear: both"  align="left">
            <?
            echo get_spacial_instruction($data[1],"100%",255);
            ?>
            </div>
            <div  style="width: 1060px ;  padding-top:10px; clear: both"   align="left">
        <?
        $lib_designation_arr=return_library_array(" select id,custom_designation from lib_designation","id","custom_designation");
        $user_lib_designation_arr=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
        $user_lib_name_arr=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
        $approve_data_array=sql_select("select b.approved_by,min(b.approved_date) as approved_date from   approval_history b where b.mst_id=$data[1] and b.entry_form=51 and current_approval_status = 1 group by  b.approved_by order by b.approved_by asc");
        $unapprove_data_array=sql_select("select b.id, b.approved_by,b.approved_date,b.approved_no, (select 1 from dual) as type from approval_history b where b.mst_id=$data[1] and b.entry_form=51 union all select b.id, b.un_approved_by, b.un_approved_date,b.approved_no, (select 2 from dual) as type from approval_history b where b.mst_id=$data[1] and b.entry_form=51 and b.un_approved_date is not null order by approved_date desc");

       //        echo "select b.approved_by,b.approved_date,b.un_approved_reason,b.un_approved_date,b.approved_no from   approval_history b where b.mst_id=$data[1] and b.entry_form=51  order by b.approved_date desc";
        if($dataArray[0][csf("approved")] == 1){
        ?>
        <table  width="100%" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
            <tr >
                <th colspan="5" >Approval Status -
                    <?
                    if($dataArray[0][csf("approved")] == 1){
                        echo '<span style="color: red;">Approved</span>';
                    }elseif($dataArray[0][csf("approved")] == 0){
                        echo '<span style="color: red;">Un-Approved</span>';
                    }else{
                        echo '<span style="color: red;">Partially Approved</span>';
                    }
                    ?>
                </th>
            </tr>
            <?
            if(count($approve_data_array)>0)
            {
                ?>
                <tr >
                    <th width="3%">Sl</th>
                    <th width="40%">Name</th>
                    <th width="30%">Designation</th>
                    <th width="27%">Approval Date</th>

                </tr>
                <?
            }
            ?>
            </thead>
            <tbody>
            <?

            if (count($approve_data_array) > 0)
            {
                $i=1;
                foreach($approve_data_array as $row){
                    ?>
                    <tr style="border:1px solid black;">
                        <td width="3%" align="center" ><? echo $i;?></td>
                        <td width="40%" style="text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]];?></td>
                        <td width="30%" style="text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
                        <td width="27%" style="text-align:center"><? echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')]));?></td>

                    </tr>
                    <?
                    $i++;
                }
            }
            ?>
            </tbody>
        </table>
        <br/>
        <?
        if(count($unapprove_data_array)>0)
        {
            ?>
            <table  width="100%" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
                <thead>
                <tr >
                    <th colspan="6" >Approval/Un Approval History</th>
                </tr>
                <tr >
                    <th width="3%" >Sl</th>
                    <th width="30%" >Name</th>
                    <th width="20%" >Designation</th>
                    <th width="15%" >Approval Status</th>
                    <th width="22%" > Date</th>
                </tr>
                </thead>
                <tbody>
                <?
                $i=1;
                foreach($unapprove_data_array as $row){
                    if($row[csf('type')] == 1){
                    ?>
                    <tr >
                        <td width="3%" align="center"><? echo $i;?></td>
                        <td width="30%" style="text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]];?></td>
                        <td width="20%" style="text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
                        <td width="15%" style=" text-align:center"><? echo 'Yes';?></td>
                        <td width="22%" style="text-align:center"><? if($row[csf('approved_date')]!="") echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')])); else echo "";?></td>
                    </tr>
                    <?
                    $i++;
                    }else{
                    ?>
                        <tr >
                            <td width="3%" align="center"><? echo $i;?></td>
                            <td width="30%" style="text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]];?></td>
                            <td width="20%" style="text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
                            <td width="15%" style=" text-align:center"><? echo 'No';?></td>
                            <td width="22%" style="text-align:center"><? if($row[csf('approved_date')]!="") echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')])); else echo "";?></td>
                        </tr>
                    <?
                    }
                }
                    ?>

                </tbody>
            </table>
            <?
        }
        }
        ?>
    </div>
	<?
    	echo signature_table(212, $data[0], $width."px","","70",$inserted_by);
    ?>
</div>
<?
exit();
}

if($action=="trims_order_receive_print_4")
{
	//select id, item_name from lib_item_group where item_category=4 and status_active=1
	extract($_REQUEST);
	$data=explode('*',$data);
	$rate_cond=$data[4];
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$trim_group_arr=return_library_array( "select id, item_name from lib_item_group where item_category=4",'id','item_name');
	$leader_name_arr=return_library_array( "select id, team_leader_name from lib_marketing_team",'id','team_leader_name');
	$member_name_arr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
	//$store_name_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$source_for_order = array(1 => 'In-House', 2 => 'Sub-Contract');
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	$lib_location_arr=return_library_array( "select id, location_name from lib_location where status_active =1 and is_deleted=0",'id','location_name');

	$sql_mst="SELECT id, subcon_job, company_id, location_id, party_id, currency_id, approved,delivery_date, rec_start_date, rec_end_date, receive_date, within_group, party_location, order_id, order_no,exchange_rate,team_leader,team_member,team_marchant,trims_ref,remarks,is_apply_last_update,revise_no, delivery_point,update_date,inserted_by, buyer_tb, buying_merchant
	from subcon_ord_mst
	where id=$data[1] and entry_form=255 and status_active=1";
     //echo $sql_mst; die;

	$dataArray=sql_select($sql_mst);
	$inserted_by=$dataArray[0][csf("inserted_by")];
	$party_id=$dataArray[0][csf("party_id")];

	if($data[3]==1)
	{
		$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		$party_addres=$lib_location_arr[$dataArray[0][csf('party_location')]];
	}
	else
	{
		$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

		$sql_party=sql_select("select id, address_1 from lib_buyer where status_active =1 and is_deleted=0 and id=$party_id");
	    $party_addres=$sql_party[0]["ADDRESS_1"];
	}

	
	$sql= "SELECT b.id as receive_details_id, b.buyer_po_no, b.buyer_style_ref, b.buyer_buyer, b.section,b.sub_section, b.item_group as trim_group, b.source_for_order , b.order_uom ,a.id, a.description, a.color_id, a.size_id, a.qnty, a.rate , a.amount, a.style, b.sub_section,b.booked_uom, b.booked_conv_fac, a.booked_qty, a.gmts_color_id, a.gmts_size_id  from subcon_ord_dtls b ,subcon_ord_breakdown a where a.mst_id=b.id  and b.job_no_mst=a.job_no_mst and b.mst_id =$data[1] and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.booked_qty is not null and a.booked_qty!=0  and a.status_active=1 order by b.id,a.id";
	$qry_result=sql_select($sql);

	$color_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$size_arr=return_library_array( "select id,size_name from  lib_size where status_active=1 and is_deleted=0",'id','size_name');
	$country_full_name = return_library_array("SELECT id,country_name from lib_country", "id", "country_name");
	$company_sql = "SELECT * FROM lib_company WHERE id = $data[0]  AND is_deleted=0 AND status_active=1 ORDER BY company_name ASC";
	$result = sql_select( $company_sql );
	foreach( $result as $row  )
	{
		if($row[csf("level_no")])		$level_no	= $row[csf("level_no")].', ';
		if($row[csf("plot_no")])		$plot_no 	= $row[csf("plot_no")].', ';
		if($row[csf("road_no")]) 		$road_no 	= $row[csf("road_no")].', ';
		if($row[csf("block_no")]!='')	$block_no 	= $row[csf("block_no")].', ';
		if($row[csf("zip_code")]!='')	$zip_code 	= $row[csf("zip_code")].', ';
		if($row[csf("city")]!='') $city 			= $row[csf("city")];
		if($row[csf("country_id")]!='')	$country 	= $country_full_name[$row[csf("country_id")]].'.';
		if($row[csf("contact_no")]!='')	$contact_no = $row[csf("contact_no")];
		if($row[csf("email")]!='')		$email 		= $row[csf("email")];
		if($row[csf("website")]!='')	$website 	= $row[csf("website")].'.'; 
	}
	$head_oofice= "House #".$plot_no."Road #".$road_no."Sector #".$block_no.$zip_code.$country;
	$company_address="Head Office :".$head_oofice.'</br> Factory address : '.$city.'</br> Email : '.$email.'</br> Mobile : '.$contact_no;
	$com_dtls = fnc_company_location_address($data[0], 0, 2);
	$currency_id = $dataArray[0][csf("currency_id")];
	if($currency_id==2) $usd_taka='(USD)'; else $usd_taka='(Taka)';
	//echo "<pre>";
	//print_r($wo_arr);
	//die;
	if($db_type==0){
		$lib_currency_data=sql_select("select conversion_rate from currency_conversion_rate where company_id=$data[0] and currency=2 order by id desc limit 1");
	}else{
		$lib_currency_data=sql_select("select conversion_rate from currency_conversion_rate where company_id=$data[0] and currency=2 and rownum<2 order by id desc");
	}
	$currency_conversion_rate=$lib_currency_data[0][csf("conversion_rate")];
	if($rate_cond ==1) $width=1450; else $width=1270;
	?>
	<style type="text/css">
		td.make_bold {
	  		font-weight: 900;
		}
	</style>
	<div style="width:<? echo $width.'px'; ?>;">
		<table width="<? echo $width; ?>" cellspacing="0" align="center" border="0">
			<tr>
            	<td  align="left"><img src="../../<? echo $com_dtls[2]; ?>" height="70" width="200"></td>
            	<td colspan="5" align="center"  style="font-size:xx-large; text-align:left;"><strong ><? echo $com_dtls[0]; ?></strong>
        	</tr>
	        <tr class="form_caption">
	            <td colspan="6" align="center"><? echo $company_address; ?> </td>
	        </tr>
	        <tr>
	            <td colspan="6" style="font-size:xx-large; text-align:center;" align="center"><strong >Order Receive Booking </strong> </td>
	        </tr>
		</table>
		<br>
		<table width="<? echo $width; ?>" cellspacing="0" align="center" border="0">
			
			<tr>			
				<td width="120" class="make_bold">Order Rcv ID </td> <td width="175" class="make_bold"><? echo " : ".$dataArray[0][csf('subcon_job')]; ?></td>
				<td width="120" class="make_bold">Receive Date </td> <td width="175" class="make_bold"><? echo " : ".change_date_format($dataArray[0][csf('receive_date')]); ?></td>
				<td width="120" class="make_bold">Revised W/O</td> <td width="175" class="make_bold"><? echo " : ".$dataArray[0][csf('revise_no')]; ?></td>
			</tr>
			<tr>
				<td width="120">Party Name </td> <td width="175"><? echo " : ".$party_arr[$dataArray[0][csf('party_id')]]; ?></td>
				<td width="120">Work Order no </td> <td width="175"><? echo " : ".$dataArray[0][csf('order_no')]; ?></td>
				<td width="120" class="make_bold">Last Revised Date</td> <td width="175" class="make_bold"><? echo " : ".change_date_format($dataArray[0][csf('update_date')]); ?></td>
			</tr>
			<tr>
				<td width="120" style="vertical-align: top;" >Party Location </td> <td width="175"><? echo " : ".$party_addres; ?></td>
				<td width="120">Currency </td> <td width="175"><? echo " : ".$currency[$dataArray[0][csf('currency_id')]]; ?></td>
				<td width="120" class="make_bold">Delivery Date</td> <td width="175" class="make_bold"><? echo " : ".change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
			</tr>
			<tr>
				
				<td width="120">Team Leader </td> <td ><? echo " : ".$leader_name_arr[$dataArray[0][csf('team_leader')]]; ?></td>
				<td width="120">Team Member </td> <td width="175"><? echo " : ".$member_name_arr[$dataArray[0][csf('team_member')]]; ?></td>
				<td width="120">Factory Merchant </td> <td width="175"><? echo " : ".$dataArray[0][csf('team_marchant')]; ?></td>
			</tr>
			<tr>
				<td width="120">Delivery Point </td> <td><? echo " : ".$dataArray[0][csf('delivery_point')]; ?></td>
				<td width="120">Remarks </td> <td ><? echo " : ".$dataArray[0][csf('remarks')]; ?></td>
				<td width="120">Buyers TB</td> <td width="175"><? echo " : ".$dataArray[0][csf('buyer_tb')]; ?></td>
			</tr>
			<tr>
				<td width="120">Buying Merchant </td> <td><? echo " : ".$dataArray[0][csf('buying_merchant')]; ?></td>
				
			</tr>
		</table>
		<br>
		<div style="width:100%;">
			<table align="left" cellspacing="0" width="<? echo $width; ?>"  border="1" rules="all" class="rpt_table"  >
				<thead>
					<tr>
                        <th width="30">SL No.</th>
		        		<th width="100">PO No.</th>
		                <th width="100">Buyer's Buyer</th>
		                <th width="80">Section</th>
		                <th width="100">Sub-Section</th>
		                <th width="100">Trims Group</th>
		                <th width="100">Style No.</th>
		                <th width="100">Item Description</th>
						<th width="80">Gmts Color</th>
		                <th width="80">Gmts Size</th>
		                <th width="80">Item Color</th>
		                <th width="80">Item Size</th>
		                <th width="60">Order UOM</th>
		                <th width="80">Order Qty</th>
		                <? if($rate_cond==1){
		                ?>	<th width="60"> Rate <? echo $usd_taka ; ?></th>
		                	<th width="80"> Amount <? echo $usd_taka ; ?></th>
		                <?
		                }?>
		                <th>Source</th>
		        	</tr>
				</thead>
				<tbody>
				<?
				$tblRow=1; $i=1;
				$total_amount=0; $total_qnty=0;
				foreach($qry_result as $row)
				{
					
					$rate=$row[csf("rate")];
					$amount=$row[csf("amount")];
					//$title=1;
					if($data[3]==1) $buyer_buyer=$buyer_arr[$row[csf("buyer_buyer")]]; else $buyer_buyer=$row[csf("buyer_buyer")];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
                        <td align="center" width="30"><?=$tblRow?></td>
						<td style="word-break: break-word;" width="100"><p><?  echo $row[csf("buyer_po_no")] ; ?></p></td>
		                <td style="word-break: break-word;" width="100"><p><?  echo $buyer_buyer ; ?></p></td>
		                <td style="word-break: break-word;" width="80"><p><?  echo $trims_section[$row[csf("section")]] ; ?></p></td>
		                <td style="word-break: break-word;" width="100"><p><?  echo $trims_sub_section[$row[csf("sub_section")]] ; ?></p></td>
		                <td style="word-break: break-word;" width="100"><p><?  echo $trim_group_arr[$row[csf("trim_group")]] ; ?></p></td>
		                <td style="word-break: break-word;" width="100"><p><?  echo $row[csf("style")] ; ?></p></td>
		                <td style="word-break: break-word;" width="100"><p><?  echo $row[csf("description")] ; ?></p></td>
						<td style="word-break: break-word;" width="80"><p><?  echo $color_arr[$row[csf("gmts_color_id")]] ; ?></p></td>
						<td style="word-break: break-word;" width="80"><p><?  echo $size_arr[$row[csf("gmts_size_id")]] ; ?></p></td>
		                <td style="word-break: break-word;" width="80"><p><?  echo $color_arr[$row[csf("color_id")]] ; ?></p></td>
		                <td style="word-break: break-word;" width="80"><p><?  echo $size_arr[$row[csf("size_id")]] ; ?></p></td>
		                <td width="60"><?  echo $unit_of_measurement[$row[csf("order_uom")]] ; ?></td>
		                <td width="80" align="right"><?  echo number_format($row[csf("qnty")],4) ; ?></td>
		                <? if($rate_cond==1){
		                ?>	<td width="60" align="right"><?  echo number_format($rate,4) ; ?></td>
		                	<td width="80" align="right"><?  echo number_format($amount,4) ; ?></td>
		                <?
		                }?>
		                <td><?  echo $source_for_order[$row[csf("source_for_order")]] ; ?></td>
					</tr>
					<?
					$total_amount+=$amount;
					$total_qnty+=$row[csf("qnty")];
					$tblRow++; 
				}
				$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');
				$currency_sign = $currency_sign_arr[$dataArray[0][csf("currency_id")]];
				
				//$mcurrency, $dcurrency;
				$dcurrency="";
				if($currency_id==1){
					$mcurrency='Taka';
					$dcurrency='Paisa';
				}else if($currency_id==2){
					$mcurrency='USD';
					$dcurrency='CENTS';
				}else if($currency_id==3){
					$mcurrency='EURO';
					$dcurrency='CENTS';
				}
				?>
				<? if($rate_cond ==1)
				{
				?> 
				<tr>
					<td colspan="13" align="right"><strong>Total:</strong></td>
					<td align="right" class="make_bold"><p><?  echo number_format($total_qnty,4) ; ?></p></td>
					 <td>&nbsp;</td>
					<td align="right" class="make_bold"><p><?  echo number_format($total_amount,4) ; ?></p></td>
					<td >&nbsp;</td>
				</tr>
				<?
				}else{
					?> 
				<tr>
					<td colspan="13" align="right"><strong>Total:</strong></td>
					<td align="right" class="make_bold"><p><?  echo number_format($total_qnty,4) ; ?></p></td>
					<td >&nbsp;</td>
				</tr>
				<?
				}
				?>
				</table>
			</div>
		<br>
		<? if($rate_cond ==1)
		{
		?>
		    <div style="width: 1060px ;  margin-top:10px; float: left;"  align="left">
				<table style="border: 1px;">
					<tr>
						<td><p><strong>Total Work Order Amount (In Word) : <? echo number_to_words(number_format($total_amount,2), $mcurrency, $dcurrency);?></strong></p></td>
					</tr>
				</table>
			</div>
		<?
		}?>
		<br>
	</div>
            <div style="width: 1060px ;  padding-top:10px; clear: both"  align="left">
            <?
            echo get_spacial_instruction($data[1],"100%",255);
            ?>
            </div>
            <div  style="width: 1060px ;  padding-top:10px; clear: both"   align="left">
        <?
        $lib_designation_arr=return_library_array(" select id,custom_designation from lib_designation","id","custom_designation");
        $user_lib_designation_arr=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
        $user_lib_name_arr=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
        $approve_data_array=sql_select("select b.approved_by,min(b.approved_date) as approved_date from   approval_history b where b.mst_id=$data[1] and b.entry_form=51 and current_approval_status = 1 group by  b.approved_by order by b.approved_by asc");
        $unapprove_data_array=sql_select("select b.id, b.approved_by,b.approved_date,b.approved_no, (select 1 from dual) as type from approval_history b where b.mst_id=$data[1] and b.entry_form=51 union all select b.id, b.un_approved_by, b.un_approved_date,b.approved_no, (select 2 from dual) as type from approval_history b where b.mst_id=$data[1] and b.entry_form=51 and b.un_approved_date is not null order by approved_date desc");

       //        echo "select b.approved_by,b.approved_date,b.un_approved_reason,b.un_approved_date,b.approved_no from   approval_history b where b.mst_id=$data[1] and b.entry_form=51  order by b.approved_date desc";
        if($dataArray[0][csf("approved")] == 1){
        ?>
        <table  width="100%" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
            <tr >
                <th colspan="5" >Approval Status -
                    <?
                    if($dataArray[0][csf("approved")] == 1){
                        echo '<span style="color: red;">Approved</span>';
                    }elseif($dataArray[0][csf("approved")] == 0){
                        echo '<span style="color: red;">Un-Approved</span>';
                    }else{
                        echo '<span style="color: red;">Partially Approved</span>';
                    }
                    ?>
                </th>
            </tr>
            <?
            if(count($approve_data_array)>0)
            {
                ?>
                <tr >
                    <th width="3%">Sl</th>
                    <th width="40%">Name</th>
                    <th width="30%">Designation</th>
                    <th width="27%">Approval Date</th>

                </tr>
                <?
            }
            ?>
            </thead>
            <tbody>
            <?

            if (count($approve_data_array) > 0)
            {
                $i=1;
                foreach($approve_data_array as $row){
                    ?>
                    <tr style="border:1px solid black;">
                        <td width="3%" align="center" ><? echo $i;?></td>
                        <td width="40%" style="text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]];?></td>
                        <td width="30%" style="text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
                        <td width="27%" style="text-align:center"><? echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')]));?></td>

                    </tr>
                    <?
                    $i++;
                }
            }
            ?>
            </tbody>
        </table>
        <br/>
        <?
        if(count($unapprove_data_array)>0)
        {
            ?>
            <table  width="100%" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
                <thead>
                <tr >
                    <th colspan="6" >Approval/Un Approval History</th>
                </tr>
                <tr >
                    <th width="3%" >Sl</th>
                    <th width="30%" >Name</th>
                    <th width="20%" >Designation</th>
                    <th width="15%" >Approval Status</th>
                    <th width="22%" > Date</th>
                </tr>
                </thead>
                <tbody>
                <?
                $i=1;
                foreach($unapprove_data_array as $row){
                    if($row[csf('type')] == 1){
                    ?>
                    <tr >
                        <td width="3%" align="center"><? echo $i;?></td>
                        <td width="30%" style="text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]];?></td>
                        <td width="20%" style="text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
                        <td width="15%" style=" text-align:center"><? echo 'Yes';?></td>
                        <td width="22%" style="text-align:center"><? if($row[csf('approved_date')]!="") echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')])); else echo "";?></td>
                    </tr>
                    <?
                    $i++;
                    }else{
                    ?>
                        <tr >
                            <td width="3%" align="center"><? echo $i;?></td>
                            <td width="30%" style="text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]];?></td>
                            <td width="20%" style="text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
                            <td width="15%" style=" text-align:center"><? echo 'No';?></td>
                            <td width="22%" style="text-align:center"><? if($row[csf('approved_date')]!="") echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')])); else echo "";?></td>
                        </tr>
                    <?
                    }
                }
                    ?>

                </tbody>
            </table>
            <?
        }
        }
        ?>
    </div>
	<?
    	echo signature_table(212, $data[0], $width."px","","70",$inserted_by);
    ?>
</div>
<?
exit();
}




if($action=="trims_order_receive_print_3")
{
	
	//select id, item_name from lib_item_group where item_category=4 and status_active=1
	extract($_REQUEST);
	$data=explode('*',$data);
	$rate_cond=$data[4];
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$trim_group_arr=return_library_array( "select id, item_name from lib_item_group where item_category=4",'id','item_name');
	$leader_name_arr=return_library_array( "select id, team_leader_name from lib_marketing_team",'id','team_leader_name');
	$member_name_arr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
	//$store_name_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$source_for_order = array(1 => 'In-House', 2 => 'Sub-Contract');
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");

	if($data[3]==1)
	{
		$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	}
	else
	{
		$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	}

	$sql_mst="select id, subcon_job, company_id, location_id, party_id, currency_id, party_location, delivery_date, rec_start_date, rec_end_date, receive_date, within_group, party_location, order_id, order_no,exchange_rate,team_leader,team_member,team_marchant,trims_ref,remarks,is_apply_last_update,revise_no, delivery_point,update_date,inserted_by
		from subcon_ord_mst
		where id=$data[1] and entry_form=255 and status_active=1";
	$dataArray=sql_select($sql_mst);
	$sql= "select b.id as receive_details_id, b.buyer_po_no, b.buyer_style_ref, b.buyer_buyer, b.section,b.sub_section, b.item_group as trim_group, b.source_for_order , b.order_uom ,a.id, a.description,a.color_id, a.size_id, a.qnty, a.rate , a.amount, a.style, b.sub_section,b.booked_uom, b.booked_conv_fac, a.booked_qty  from subcon_ord_dtls b ,subcon_ord_breakdown a where a.mst_id=b.id  and b.job_no_mst=a.job_no_mst and b.mst_id =$data[1] and b.source_for_order=1 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.booked_qty is not null and a.booked_qty!=0  and a.status_active=1 order by b.id,a.id";
	$qry_result=sql_select($sql);

	$color_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$size_arr=return_library_array( "select id,size_name from  lib_size where status_active=1 and is_deleted=0",'id','size_name');
	$country_full_name = return_library_array("SELECT id,country_name from lib_country", "id", "country_name");
	$company_sql = "SELECT * FROM lib_company WHERE id = $data[0]  AND is_deleted=0 AND status_active=1 ORDER BY company_name ASC";
	$result = sql_select( $company_sql );
	foreach( $result as $row  )
	{
		if($row[csf("level_no")])		$level_no	= $row[csf("level_no")].', ';
		if($row[csf("plot_no")])		$plot_no 	= $row[csf("plot_no")].', ';
		if($row[csf("road_no")]) 		$road_no 	= $row[csf("road_no")].', ';
		if($row[csf("block_no")]!='')	$block_no 	= $row[csf("block_no")].', ';
		if($row[csf("zip_code")]!='')	$zip_code 	= $row[csf("zip_code")].', ';
		if($row[csf("city")]!='') $city 			= $row[csf("city")];
		if($row[csf("country_id")]!='')	$country 	= $country_full_name[$row[csf("country_id")]].'.';
		if($row[csf("contact_no")]!='')	$contact_no = $row[csf("contact_no")];
		if($row[csf("email")]!='')		$email 		= $row[csf("email")];
		if($row[csf("website")]!='')	$website 	= $row[csf("website")].'.'; 
	}
	$head_oofice= "House #".$plot_no."Road #".$road_no."Sector #".$block_no.$zip_code.$country;
	$company_address="Head Office :".$head_oofice.'</br> Factory address : '.$city.'</br> Email : '.$email.'</br> Mobile : '.$contact_no;
	$com_dtls = fnc_company_location_address($data[0], 0, 2);
	$currency_id = $dataArray[0][csf("currency_id")];
	$inserted_by = $dataArray[0][csf("inserted_by")];
	if($currency_id==2) $usd_taka='(USD)'; else $usd_taka='(Taka)';
	//echo "<pre>";
	//print_r($wo_arr);
	//die;
	if($db_type==0){
		$lib_currency_data=sql_select("select conversion_rate from currency_conversion_rate where company_id=$data[0] and currency=2 order by id desc limit 1");
	}else{
		$lib_currency_data=sql_select("select conversion_rate from currency_conversion_rate where company_id=$data[0] and currency=2 and rownum<2 order by id desc");
	}
	$currency_conversion_rate=$lib_currency_data[0][csf("conversion_rate")];
	if($rate_cond ==1) $width=1450; else $width=1270;
	?>
	<style type="text/css">
		td.make_bold {
	  		font-weight: 900;
		}
	</style>
	<div style="width:<? echo $width.'px'; ?>;">
		<table width="<? echo $width; ?>" cellspacing="0" align="center" border="0">
			<tr>
            	<td  align="left"><img src="../../<? echo $com_dtls[2]; ?>" height="70" width="200"></td>
            	<td colspan="5" align="center"  style="font-size:xx-large; text-align:left;"><strong ><? echo $com_dtls[0]; ?></strong>
        	</tr>
	        <tr class="form_caption">
	            <td colspan="6" align="center"><? echo $company_address; ?> </td>
	        </tr>
	        <tr>
	            <td colspan="6" style="font-size:xx-large; text-align:center;" align="center"><strong >Order Receive Booking </strong> </td>
	        </tr>
		</table>
		<br>
		<table width="<? echo $width; ?>" cellspacing="0" align="center" border="0">
			
			<tr>			
				<td width="120" class="make_bold">Order Rcv ID </td> <td width="175" class="make_bold"><? echo " : ".$dataArray[0][csf('subcon_job')]; ?></td>
				<td width="120" class="make_bold">Receive Date </td> <td width="175" class="make_bold"><? echo " : ".change_date_format($dataArray[0][csf('receive_date')]); ?></td>
				<td width="120" class="make_bold">Revised W/O</td> <td width="175" class="make_bold"><? echo " : ".$dataArray[0][csf('revise_no')]; ?></td>
			</tr>
			<tr>
				<td width="120">Party Name </td> <td width="175"><? echo " : ".$party_arr[$dataArray[0][csf('party_id')]]; ?></td>
				<td width="120">Work Order no </td> <td width="175"><? echo " : ".$dataArray[0][csf('order_no')]; ?></td>
				<td width="120" class="make_bold">Last Revised Date</td> <td width="175" class="make_bold"><? echo " : ".change_date_format($dataArray[0][csf('update_date')]); ?></td>
			</tr>
			<tr>
				<td width="120">Currency </td> <td width="175"><? echo " : ".$currency[$dataArray[0][csf('currency_id')]]; ?></td>
				<td width="120">Exchange Rate </td> <td width="175"><? echo " : ".$dataArray[0][csf('exchange_rate')]; ?></td>
				<td width="120" class="make_bold">Delivery Date</td> <td width="175" class="make_bold"><? echo " : ".change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
			</tr>
			<tr>
				
				<td width="120">Team Leader </td> <td ><? echo " : ".$leader_name_arr[$dataArray[0][csf('team_leader')]]; ?></td>
				<td width="120">Team Member </td> <td width="175"><? echo " : ".$member_name_arr[$dataArray[0][csf('team_member')]]; ?></td>
				<td width="120">Factory Merchant </td> <td width="175"><? echo " : ".$dataArray[0][csf('team_marchant')]; ?></td>
			</tr>
			<tr>
				<td width="120">Delivery Point </td> <td><? echo " : ".$dataArray[0][csf('delivery_point')]; ?></td>
				<td width="120">Remarks </td> <td ><? echo " : ".$dataArray[0][csf('remarks')]; ?></td>
				<td width="120"></td> <td width="175"></td>
			</tr>
		</table>
		<br>
		<div style="width:100%;">
			<table align="left" cellspacing="0" width="<? echo $width; ?>"  border="1" rules="all" class="rpt_table"  >
				<thead>
					<tr>
		        		<th width="110">PO No.</th>
		                <th width="110">Buyer's Buyer</th>
		                <th width="80">Section</th>
		                <th width="100">Sub-Section</th>
		                <th width="130">Trims Group</th>
		                <th width="110">Style No.</th>
		                <th width="110">Item Description</th>
		                <th width="110">Color</th>
		                <th width="110">Size</th>
		                <th width="60">Order UOM</th>
		                <th width="80">Order Qty</th>
		                <? if($rate_cond==1){
		                ?>	<th width="60"> Rate <? echo $usd_taka ; ?></th>
		                	<th width="80"> Amount <? echo $usd_taka ; ?></th>
		                <?
		                }?>
		                <th>Source</th>
		        	</tr>
				</thead>
				<tbody>
				<?
				$tblRow=1; $i=1;
				$total_amount=0; $total_qnty=0;
				foreach($qry_result as $row)
				{
					
					$rate=$row[csf("rate")];
					$amount=$row[csf("amount")];
					//$title=1;
					if($data[3]==1) $buyer_buyer=$buyer_arr[$row[csf("buyer_buyer")]]; else $buyer_buyer=$row[csf("buyer_buyer")];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
						<td style="word-break: break-word;" width="110"><p><?  echo $row[csf("buyer_po_no")] ; ?></p></td>
		                <td style="word-break: break-word;" width="110"><p><?  echo $buyer_buyer ; ?></p></td>
		                <td style="word-break: break-word;" width="80"><p><?  echo $trims_section[$row[csf("section")]] ; ?></p></td>
		                <td style="word-break: break-word;" width="100"><p><?  echo $trims_sub_section[$row[csf("sub_section")]] ; ?></p></td>
		                <td style="word-break: break-word;" width="130"><p><?  echo $trim_group_arr[$row[csf("trim_group")]] ; ?></p></td>
		                <td style="word-break: break-word;" width="110"><p><?  echo $row[csf("style")] ; ?></p></td>
		                <td style="word-break: break-word;" width="110"><p><?  echo $row[csf("description")] ; ?></p></td>
		                <td style="word-break: break-word;" width="110"><p><?  echo $color_arr[$row[csf("color_id")]] ; ?></p></td>
		                <td style="word-break: break-word;" width="110"><p><?  echo $size_arr[$row[csf("size_id")]] ; ?></p></td>
		                <td width="60"><?  echo $unit_of_measurement[$row[csf("order_uom")]] ; ?></td>
		                <td width="80" align="right"><?  echo number_format($row[csf("qnty")],4) ; ?></td>
		                <? if($rate_cond==1){
		                ?>	<td width="60" align="right"><?  echo number_format($rate,4) ; ?></td>
		                	<td width="80" align="right"><?  echo number_format($amount,4) ; ?></td>
		                <?
		                }?>
		                <td><?  echo $source_for_order[$row[csf("source_for_order")]] ; ?></td>
					</tr>
					<?
					$total_amount+=$amount;
					$total_qnty+=$row[csf("qnty")];
					$tblRow++; 
				}
				$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');
				$currency_sign = $currency_sign_arr[$dataArray[0][csf("currency_id")]];
				
				//$mcurrency, $dcurrency;
				$dcurrency="";
				if($currency_id==1){
					$mcurrency='Taka';
					$dcurrency='Paisa';
				}else if($currency_id==2){
					$mcurrency='USD';
					$dcurrency='CENTS';
				}else if($currency_id==3){
					$mcurrency='EURO';
					$dcurrency='CENTS';
				}
				?>
				<? if($rate_cond ==1)
				{
				?> 
				<tr>
					<td colspan="10" align="right"><strong>Total:</strong></td>
					<td align="right" class="make_bold"><p><?  echo number_format($total_qnty,4) ; ?></p></td>
					 <td>&nbsp;</td>
					<td align="right" class="make_bold"><p><?  echo number_format($total_amount,4) ; ?></p></td>
					<td >&nbsp;</td>
				</tr>
				<?
				}else{
					?> 
				<tr>
					<td colspan="10" align="right"><strong>Total:</strong></td>
					<td align="right" class="make_bold"><p><?  echo number_format($total_qnty,4) ; ?></p></td>
					<td >&nbsp;</td>
				</tr>
				<?
				}
				?>
				</table>
			</div>
		<br>
		<? if($rate_cond ==1)
		{
		?>
		    <div style="width: 1060px ;  margin-top:5px; float: left;"  align="left">
				<table style="border: 1px;">
					<tr>
						<td><p><strong>Total Work Order Amount (In Word) : <? echo number_to_words(number_format($total_amount,2), $mcurrency, $dcurrency);?></strong></p></td>
					</tr>
				</table>
			</div>
		<?
		}?>
		<br>
		<div style="width: 500px ;  margin-top:5px; float: left;"  align="left">
			<? 
			   	echo get_spacial_instruction($data[1],"115%",255);
			?>
		</div>
		<br>
	</div>
	<?
    	echo signature_table(212, $data[0], "1100px","","70",$inserted_by);
    ?>
</div>
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


if ($action=='copy_order_rcv') {
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$user_id=$_SESSION['logic_erp']['user_id'];
	//$str_rep=array("/", "&", "*", "(", ")", "=","'",",","\r", "\n",'"','#','_','€','$','৳','~','?',':',';','-',':-',"'\'");
	$str_rep=array( "&", "*", "(", ")", "=","'","\r", "\n",'"','#','_','€','$','৳','~','?',':',';',':-');
	
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	
	$receive_date=strtotime(str_replace("'",'',$txt_order_receive_date));
	$delivery_date=strtotime(str_replace("'",'',$txt_delivery_date));
	$current_date=date("d-m-Y");
	$txt_order_receive_date = $current_date;
	if( $receive_date > $delivery_date )
	{
		echo "26**"; disconnect($con); die;
	}
	/*else if($receive_date != $current_date)
	{
		echo "25**"; disconnect($con); die;
	}*/

	// $receive_date = $current_date;
	
	if($db_type==0) $insert_date_con="and YEAR(insert_date)=".date('Y',time())."";
	else if($db_type==2) $insert_date_con="and TO_CHAR(insert_date,'YYYY')=".date('Y',time())."";
	
	$new_job_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'TOR', date("Y",time()), 5, "select job_no_prefix,job_no_prefix_num from subcon_ord_mst where entry_form=255 and company_id=$cbo_company_name $insert_date_con order by id desc", "job_no_prefix", "job_no_prefix_num" ));
	//$new_job_no = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "subcon_ord_mst",$con,1,$cbo_company_name,'TOR',1,date("Y",time()),1 ));
	$txt_order_no=$new_job_no[0];

	if (is_duplicate_field( "order_no", "subcon_ord_mst", "order_no='$txt_order_no' and company_id=$cbo_company_name and status_active=1 and is_deleted=0" ) == 1)
	{
		echo "11**0"; disconnect($con); die;
	}
	else
	{
		//echo "10**select order_no from subcon_ord_mst where order_no='$txt_order_no' and company_id=$cbo_company_name and status_active=1 and is_deleted=0 and id !=$update_id"; die;
		if($db_type==0){
			$txt_delivery_date=change_date_format(str_replace("'",'',$txt_delivery_date),'yyyy-mm-dd');
			$txt_rec_start_date=change_date_format(str_replace("'",'',$txt_rec_start_date),'yyyy-mm-dd');
			$txt_rec_end_date=change_date_format(str_replace("'",'',$txt_rec_end_date),'yyyy-mm-dd');
			$txt_order_receive_date=change_date_format(str_replace("'",'',$txt_order_receive_date),'yyyy-mm-dd');
		}else{
			$txt_delivery_date=change_date_format(str_replace("'",'',$txt_delivery_date), "", "",1);
			$txt_rec_start_date=change_date_format(str_replace("'",'',$txt_rec_start_date), "", "",1);
			$txt_rec_end_date=change_date_format(str_replace("'",'',$txt_rec_end_date), "", "",1);
			$txt_order_receive_date=change_date_format(str_replace("'",'',$txt_order_receive_date), "", "",1);
		}
		$id=return_next_id("id","subcon_ord_mst",1);
		$id1=return_next_id( "id", "subcon_ord_dtls",1);
		$id3=return_next_id( "id", "subcon_ord_breakdown", 1 );
		$rID3=true;
		$field_array="id, entry_form, subcon_job, job_no_prefix, job_no_prefix_num, company_id, location_id, within_group, party_id, party_location, currency_id, receive_date, delivery_date, rec_start_date, rec_end_date, order_id, order_no, exchange_rate,team_leader,team_member, team_marchant, remarks, trims_ref, delivery_point, inserted_by, insert_date";
		$data_array="(".$id.", 255, '".$new_job_no[0]."', '".$new_job_no[1]."', '".$new_job_no[2]."', '".$cbo_company_name."', '".$cbo_location_name."', '".$cbo_within_group."', '".$cbo_party_name."', '".$cbo_party_location."', '".$cbo_currency."', '".$txt_order_receive_date."', '".$txt_delivery_date."','".$txt_rec_start_date."','".$txt_rec_end_date."', '".$hid_order_id."', '".$txt_order_no."', '".$txt_exchange_rate."', '".$cbo_team_leader."', '".$cbo_team_member."', '".$txt_fac_merchan."', '".$txt_remarks."', '".$txt_trims_ref."', '".$txt_delivery_point."', ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."')";
		
		$txt_job_no=$new_job_no[0];
		
		$field_array2="id, mst_id, job_no_mst, order_id, order_no, buyer_po_id, booking_dtls_id, order_quantity, order_uom, rate, amount, submit_date, approve_date, delivery_date, buyer_po_no, buyer_style_ref, buyer_buyer, section, sub_section, item_group, rate_domestic,  amount_domestic, is_with_order, booked_uom, booked_conv_fac, booked_qty, source_for_order, inserted_by, insert_date";
		$field_array3="id, mst_id, order_id, job_no_mst, book_con_dtls_id, description, color_id, size_id, qnty, rate, amount, booked_qty, style";

		$color_library_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
		$size_library_arr=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
		$data_array2 	= $data_array3="";  $add_commaa=0; $add_commadtls=0; $new_array_color=array();  $new_array_size=array();

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
			$txtSamSubDate 			= "txtSamSubDate_".$i;
			$txtSamApprDate 		= "txtSamApprDate_".$i;
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
			$cboSource 				= "cboSource_".$i;

			$orddelivery_date=strtotime(str_replace("'",'',$$txtOrderDeliveryDate));
			if($receive_date>$orddelivery_date)
			{
				echo "26**"; disconnect($con); die;
			}
			if($db_type==0)
			{
				$orderDeliveryDate=change_date_format(str_replace("'",'',$$txtOrderDeliveryDate),'yyyy-mm-dd');
			}
			else
			{
				$orderDeliveryDate=change_date_format(str_replace("'",'',$$txtOrderDeliveryDate), "", "",1);
			}

			$txtSamSubDate=change_date_format(str_replace("'",'',$$txtSamSubDate), "", "",1);
			$txtSamApprDate=change_date_format(str_replace("'",'',$$txtSamApprDate), "", "",1);

			if(str_replace("'",'',$$txtbuyerPoId)=="") $txtbuyerPoId=0; else $txtbuyerPoId=str_replace("'",'',$$txtbuyerPoId);
			if ($add_commaa!=0) $data_array2 .=","; $add_comma=0;

			$txtbuyerPo=str_replace($str_rep,' ',$$txtbuyerPo);
			$txtstyleRef=str_replace($str_rep,' ',$$txtstyleRef);
			
			$data_array2 .="(".$id1.",".$id.",'".$new_job_no[0]."','".$hid_order_id."','".$txt_order_no."','".$txtbuyerPoId."',".$$hdnbookingDtlsId.",".str_replace(",",'',$$txtOrderQuantity).",".$$cboUom.",".$$txtRate.",".str_replace(",",'',$$txtAmount).",'".$txtSamSubDate."','".$txtSamApprDate."','".$orderDeliveryDate."','".trim($txtbuyerPo)."','".trim($txtstyleRef)."',".$$txtbuyer.",".$$cboSection.",".$$cboSubSection.",".$$cboItemGroup.",".str_replace(",",'',$$txtDomRate).",".str_replace(",",'',$$txtDomamount).",".str_replace(",",'',$$txtIsWithOrder).",".$$cboBookUom.",".str_replace(",",'',$$txtConvFactor).",".str_replace(",",'',$$txtBookQty).",".$$cboSource.",'".$user_id."','".$pc_date_time."')";
			
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
				$style="'".$exdata[10]."'";
				
				$description=str_replace($str_rep,' ',$description);

				if (str_replace("'", "", trim($colorname)) != "") {
					if (!in_array(str_replace("'", "", trim($colorname)),$new_array_color)){
						$color_id = return_id( str_replace("'", "", trim($colorname)), $color_library_arr, "lib_color", "id,color_name","255");
						$new_array_color[$color_id]=str_replace("'", "", trim($colorname));
					}
					else $color_id =  array_search(str_replace("'", "", trim($colorname)), $new_array_color);
				} else $color_id = 0;
				//UPDATE LIB_COLOR SET COLOR_NAME = replace(COLOR_NAME, '(', '[') where 1=1;
				/*if(str_replace("'","",$colorname)!="")
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
				}*/
				
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
				
				if ($add_commadtls!=0) $data_array3 .=",";
				$data_array3.="(".$id3.",".$id1.",'".$hid_order_id."','".$new_job_no[0]."',".$book_con_dtls_id.",'".trim($description)."','".$color_id."','".$size_id."',".$qty.",".$rate.",".$amount.",".$booked_qty.",".$style.")";
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
				echo "0**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_order_no)."**".$current_date;
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
				echo "0**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_order_no)."**".$current_date;
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_order_no);
			}
		}
	}
	disconnect($con);
	die;
}

if($action=="trimsref_deliverypointupdate")
{
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	$exdata=explode("***", $data);
	$type=$exdata[1];
	$upid=$exdata[2];
	$refpointval=$exdata[0];
	
	if($type==1)//Trims ref
	{
		$rIDUP=execute_query( "update subcon_ord_mst set trims_ref='$refpointval' where id ='".$upid."' and status_active=1 and is_deleted=0",0);
	}
	else if($type==2)//Delivery Point
	{
		$rIDUP=execute_query( "update subcon_ord_mst set delivery_point='$refpointval' where id ='".$upid."' and status_active=1 and is_deleted=0",0);
	}
	if($db_type==0)
	{
		if($rIDUP ){
			mysql_query("COMMIT");
			echo "1";
		}
		else{
			mysql_query("ROLLBACK");
			echo "10";
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($rIDUP ){
			oci_commit($con);
			echo "1";
		}
		else{
			oci_rollback($con);
			echo "10";
		}
	}
	disconnect($con);
	die;
}

?>