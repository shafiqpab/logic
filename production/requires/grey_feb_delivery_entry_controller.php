<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//year(a.insert_date)
if($db_type==2 || $db_type==1 )
{
	$mrr_date_check=" to_char(a.insert_date,'YYYY')";
}
else if ($db_type==0)
{
	$mrr_date_check=" year(a.insert_date)";
}

 //-------------------START ----------------------------------------

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 100, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "" );
	exit();
}

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	exit();
}
if($action=="load_drop_down_buyer_form")
{
	echo create_drop_down( "cbo_buyer_id", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	exit();
}

if($action=="print_button_variable_setting")
{
    $print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=7 and report_id=234 and is_deleted=0 and status_active=1");
    echo "print_report_button_setting('".$print_report_format."');\n";
    exit();
}




if ($action=="save_update_delete")
{

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	//extract( $process );
	//echo $cbo_buyer_id;die;
	//=============================Check============================
		for($j=1;$j<=$total_row;$j++)
		{
			$sys_id_chk="hidesysid_".$j;
			$hideprodid_chk="hideprodid_".$j;
			$hideorder_chk="hideorder_".$j;
			if (str_replace("'", "", $$sys_id_chk)!="")
			{
				$sys_ids.=$$sys_id_chk.",";
				$hideprod_ids.=$$hideprodid_chk.",";
				$hideorder_ids.=$$hideorder_chk.",";
			}

		}

		$sys_ids=implode(",",array_unique(explode(",",chop($sys_ids,","))));
		$hideprod_ids=implode(",",array_unique(explode(",",chop($hideprod_ids,","))));
		$hideorder_ids=implode(",",array_filter(array_unique(explode(",",chop($hideorder_ids,",")))));

		$grey_sys_id_cond="";
		$receive_id_cond="";
		if ($sys_ids !="") $grey_sys_id_cond=" and grey_sys_id in (".$sys_ids.")";
		if ($sys_ids !="") $receive_id_cond=" and a.id in (".$sys_ids.")";

		$product_id_cond="";
		$product_id_cond_2="";
		if ($hideprod_ids !="") $product_id_cond=" and product_id in (".$hideprod_ids.")";
		if ($hideprod_ids !="") $product_id_cond_2=" and b.prod_id in (".$hideprod_ids.")";

		$order_id_cond="";
		$breakdown_id_cond="";
		if ($hideorder_ids !="") $order_id_cond=" and order_id in (".$hideorder_ids.")";
		if ($hideorder_ids !="") $breakdown_id_cond=" and c.po_breakdown_id in (".$hideorder_ids.")";

		if( str_replace("'","",$update_mst_id) != "" )
		{
			 $up_cond = " and mst_id != $update_mst_id";
		}
		$order_type=str_replace("'","",$cbo_order_type);
		if ($order_type==1) // With Order
		{
			$sql_qty_check=sql_select("SELECT grey_sys_id, product_id, order_id,sum(current_delivery) as current_delivery,sum(current_delivery_qnty_in_pcs) as current_delivery_qnty_in_pcs,size_coller_cuff
			from pro_grey_prod_delivery_dtls
			where status_active=1 and is_deleted=0 and entry_form=53 $grey_sys_id_cond $product_id_cond $order_id_cond $up_cond
			group by grey_sys_id, product_id, order_id,size_coller_cuff");
			$current_delivery=0;
			$current_delivery_qnty_in_pcs=0;
			foreach ($sql_qty_check as $row)
			{
				$sql_delivery_arr[$row[csf("grey_sys_id")]][$row[csf("product_id")]][$row[csf("order_id")]][$row[csf("size_coller_cuff")]]['delivery_qty'] +=$row[csf("current_delivery")];
				$sql_delivery_arr[$row[csf("grey_sys_id")]][$row[csf("product_id")]][$row[csf("order_id")]][$row[csf("size_coller_cuff")]]['current_delivery_qnty_in_pcs'] +=$row[csf("current_delivery_qnty_in_pcs")];
			}


			$sql_prod_qty=sql_select("SELECT a.id AS receive_id, b.prod_id, c.po_breakdown_id, SUM (c.quantity) AS current_stock, SUM (c.quantity_pcs) AS current_qntyInPcs,b.coller_cuff_size FROM inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c
			WHERE  a.id = b.mst_id AND b.id = c.dtls_id AND a.entry_form = 2 AND c.entry_form = 2 AND a.status_active = 1 AND a.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 AND c.trans_type = 1 AND a.company_id = $cbo_company_id AND a.booking_without_order = 0 AND c.po_breakdown_id != 0 $receive_id_cond $product_id_cond_2 $breakdown_id_cond
			group by a.id, b.prod_id, c.po_breakdown_id,b.coller_cuff_size
			ORDER BY a.id");
			$total_Prod_qty=0;
			foreach ($sql_prod_qty as $row)
			{
				$sql_production_arr[$row[csf("receive_id")]][$row[csf("prod_id")]][$row[csf("po_breakdown_id")]][$row[csf("coller_cuff_size")]]['prodcut_qty'] +=$row[csf("current_stock")];
				$sql_production_arr[$row[csf("receive_id")]][$row[csf("prod_id")]][$row[csf("po_breakdown_id")]][$row[csf("coller_cuff_size")]]['prodcut_qty_in_pcs'] +=$row[csf("current_qntyInPcs")];
			}
		}
		else // Without Order
		{
			/*echo "SELECT grey_sys_id, product_id, order_id,sum(current_delivery) as current_delivery
			from pro_grey_prod_delivery_dtls
			where status_active=1 and is_deleted=0 and entry_form=53 $grey_sys_id_cond $product_id_cond $up_cond
			group by grey_sys_id, product_id, order_id";*/

			$sql_qty_check=sql_select("SELECT grey_sys_id, product_id, order_id,sum(current_delivery) as current_delivery ,sum(current_delivery_qnty_in_pcs) as current_delivery_qnty_in_pcs,size_coller_cuff
			from pro_grey_prod_delivery_dtls
			where status_active=1 and is_deleted=0 and entry_form=53 $grey_sys_id_cond $product_id_cond $up_cond
			group by grey_sys_id, product_id, order_id,size_coller_cuff");
			$current_delivery=0;
			$current_delivery_qnty_in_pcs=0;
			foreach ($sql_qty_check as $row)
			{
				$sql_delivery_arr[$row[csf("grey_sys_id")]][$row[csf("product_id")]][$row[csf("size_coller_cuff")]]['delivery_qty'] +=$row[csf("current_delivery")];
				$sql_delivery_arr[$row[csf("grey_sys_id")]][$row[csf("product_id")]][$row[csf("size_coller_cuff")]]['current_delivery_qnty_in_pcs'] +=$row[csf("current_delivery_qnty_in_pcs")];
			}

			$sql_prod_qty=sql_select("SELECT a.id AS receive_id, b.prod_id, SUM (b.grey_receive_qnty) AS current_stock,0 as current_qntyInPcs,b.coller_cuff_size
			FROM inv_receive_master a, pro_grey_prod_entry_dtls b
			WHERE a.id=b.mst_id AND a.entry_form=2 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND a.company_id=$cbo_company_id AND a.booking_without_order=1 $receive_id_cond $product_id_cond_2
			GROUP BY a.id, b.prod_id,b.coller_cuff_size
			ORDER BY a.id");
			$total_Prod_qty=0;
			foreach ($sql_prod_qty as $row)
			{
				$sql_production_arr[$row[csf("receive_id")]][$row[csf("prod_id")]][$row[csf("coller_cuff_size")]]['prodcut_qty'] +=$row[csf("current_stock")];
				$sql_production_arr[$row[csf("receive_id")]][$row[csf("prod_id")]][$row[csf("coller_cuff_size")]]['prodcut_qty_in_pcs'] +=$row[csf("current_qntyInPcs")];
			}
		}
	//=========================================================

	if($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}

		//ya
		if( str_replace("'","",$update_mst_id) == "" ) //new insert cbo_ready_to_approved
		{
			//$id=return_next_id("id", " pro_grey_prod_delivery_mst", 1);
			//$new_mrr_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'GDS', date("Y",time()), 5, "select a.sys_number_prefix,a.sys_number_prefix_num from pro_grey_prod_delivery_mst a where a.company_id=$cbo_company_id and $mrr_date_check =".date('Y',time())." and a.entry_form=53 order by a.id DESC", "sys_number_prefix", "sys_number_prefix_num" ));

			$id = return_next_id_by_sequence("PRO_GREY_PROD_DELI_MST_PK_SEQ", "pro_grey_prod_delivery_mst", $con);
            //print_r($id); die;
            $new_mrr_number = explode("*", return_next_id_by_sequence("PRO_GREY_PROD_DELI_MST_PK_SEQ", "pro_grey_prod_delivery_mst",$con,1,$cbo_company_id,'GDS',53,date("Y",time()),13 ));


			$field_array="id,sys_number_prefix,sys_number_prefix_num,sys_number,entry_form,order_status,delevery_date,company_id,location_id,buyer_id,inserted_by,insert_date";
			$data_array="(".$id.",'".$new_mrr_number[1]."','".$new_mrr_number[2]."','".$new_mrr_number[0]."',53,".$cbo_order_type.",".$txt_delevery_date.",".$cbo_company_id.",".$cbo_location_id.",".$cbo_buyer_id.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
			//$rID=sql_insert(" pro_grey_prod_delivery_mst",$field_array,$data_array,1);
		}


		$field_array_dtls="id,mst_id,entry_form,grey_sys_id,grey_sys_number,program_no,product_id,job_no,order_id,determination_id,gsm,dia,current_delivery,current_delivery_qnty_in_pcs,size_coller_cuff,current_reject,roll,remarks,inserted_by,insert_date";
		//$dtls_id=return_next_id("id", "pro_grey_prod_delivery_dtls", 1);
		//$ref_dtls_id=return_next_id("id", "pro_grey_prod_delivery_dtls", 1);


		$k=1;
		$all_dtls_id="";
		for($i=1;$i<=$total_row;$i++)
		{
			//// hidesysid_, hideprogrum_, hideorder_
			$sys_id="hidesysid_".$i;
			$hidesysnum="hidesysnum_".$i;
			$hideprogram="hideprogrum_".$i;
			$hidefindtls="hidefindtls_".$i;
			$hideprodid="hideprodid_".$i;
			$hidejob="hidejob_".$i;
			$hideorder="hideorder_".$i;
			$hideconstruc="hideconstruction_".$i;
			$hidecomposit="hidecomposition_".$i;
			$hidegsm="hidegsm_".$i;
			$hidedia="hidedia_".$i;
			$txtcurrentdelivery="txtcurrentdelivery_".$i;
			$txtcurrentRejdelivery="txtcurrentRejdelivery_".$i;
			$txt_roll="txtroll_".$i;
			$txt_remarks="txt_remarks_".$i;
			$hid_Balance_Qty="hidtotalqtyTd_".$i;
			//echo $$txtcurrentdelivery;die;
			$txtcurrentdelivery_qntyinpcs="txtcurrentdelivery_qntyinpcs_".$i;
			$hidesize="hidesize_".$i;

			if(str_replace("'","",$$txtcurrentdelivery)>0)
			{
				//==============================Check===========================
				if ($order_type==1) // With Order
				{
					$previous_delivery = $sql_delivery_arr[str_replace("'","",$$sys_id)][str_replace("'","",$$hideprodid)][str_replace("'","",$$hideorder)][str_replace("'","",$$hidesize)]['delivery_qty'];
					$previous_delivery_qnty_in_pcs = $sql_delivery_arr[str_replace("'","",$$sys_id)][str_replace("'","",$$hideprodid)][str_replace("'","",$$hideorder)][str_replace("'","",$$hidesize)]['current_delivery_qnty_in_pcs'];
					$total_Prod_qty =  $sql_production_arr[str_replace("'","",$$sys_id)][str_replace("'","",$$hideprodid)][str_replace("'","",$$hideorder)][str_replace("'","",$$hidesize)]['prodcut_qty'];
					$total_Prod_qty_in_pcs =  $sql_production_arr[str_replace("'","",$$sys_id)][str_replace("'","",$$hideprodid)][str_replace("'","",$$hideorder)][str_replace("'","",$$hidesize)]['prodcut_qty_in_pcs'];
				}
				else // Without Order
				{
					$previous_delivery = $sql_delivery_arr[str_replace("'","",$$sys_id)][str_replace("'","",$$hideprodid)][str_replace("'","",$$hidesize)]['delivery_qty'];
					$previous_delivery_qnty_in_pcs = $sql_delivery_arr[str_replace("'","",$$sys_id)][str_replace("'","",$$hideprodid)][str_replace("'","",$$hidesize)]['current_delivery_qnty_in_pcs'];
					$total_Prod_qty =  $sql_production_arr[str_replace("'","",$$sys_id)][str_replace("'","",$$hideprodid)][str_replace("'","",$$hidesize)]['prodcut_qty'];
					$total_Prod_qty_in_pcs =  $sql_production_arr[str_replace("'","",$$sys_id)][str_replace("'","",$$hideprodid)][str_replace("'","",$$hidesize)]['prodcut_qty_in_pcs'];
				}

				$current_delivery = str_replace("'","",$$txtcurrentdelivery)*1;
				$currentdelivery_qntyinpcs = str_replace("'","",$$txtcurrentdelivery_qntyinpcs)*1;
				// echo $total_Prod_qty.'='.$current_delivery .'='. $previous_delivery.'<br>';
				if ($total_Prod_qty*1 < ($current_delivery + $previous_delivery))
				{
					echo "20**"."Delivery Quantity Must be Less Then Blance Quantity"."\n"."Total Production Qty=".$total_Prod_qty."\n"."Total Delivery + Current Qty= ".($current_delivery + $previous_delivery);
					die;
				}
				if ($total_Prod_qty_in_pcs*1 < ($currentdelivery_qntyinpcs + $previous_delivery_qnty_in_pcs))
				{
					echo "20**"."Delivery Quantity In Pcs Must be Less Then Blance Quantity In Pcs"."\n"."Total Production Qty In Pcs=".$total_Prod_qty_in_pcs."\n"."Total Delivery qnty in pcs + Current Qty qnty in pcs = ".($currentdelivery_qntyinpcs + $previous_delivery_qnty_in_pcs);
					die;
				}
				//=========================================================


				$dtls_id = return_next_id_by_sequence("PRO_GREY_PROD_DELI_DTLS_PK_SEQ", "pro_grey_prod_delivery_dtls", $con);
				// $ref_dtls_id = return_next_id_by_sequence("PRO_GREY_PROD_DELI_DTLS_PK_SEQ", "pro_grey_prod_delivery_dtls", $con);
				//if($k!=1)$dtls_id=$dtls_id+1;
				if ($k!=1) $data_array_dtls .=",";
				$data_array_dtls.="(".$dtls_id.",".$id.",53,".$$sys_id.",".$$hidesysnum.",".$$hideprogram.",".$$hideprodid.",".$$hidejob.",".$$hideorder.",".$$hideconstruc.",".$$hidegsm.",".$$hidedia.",".$$txtcurrentdelivery.",".$$txtcurrentdelivery_qntyinpcs.",".$$hidesize.",".$$txtcurrentRejdelivery.",".$$txt_roll.",".$$txt_remarks.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
				$k++;
				if($all_dtls_id=="") $all_dtls_id=$$hidefindtls."**".$$txtcurrentdelivery."**".$$txtcurrentdelivery_qntyinpcs; else $all_dtls_id.="_".$$hidefindtls."***".$$txtcurrentdelivery."**".$$txtcurrentdelivery_qntyinpcs;
			}
		}
		// echo "string";die;

		//echo $$sys_id.'='.$$hideprodid.'='.$$hideorder;die;
		/* deccesion from fuad bai

		if($all_dtls_id!="")
		{
			$all_dtls_ref=explode("_",$all_dtls_id);
			foreach($all_dtls_ref as $dtls_val)
			{
				$dtls_val_ref=explode("**",$dtls_val);
				$production_qnty=return_field_value("sum(id) as po_id","wo_po_break_down","po_number like '%$order_no' and status_active=1","po_id");
			}
		}*/


		//oci_rollback($con);
		//echo "10**".$rID."##".$rID2;die;
		//echo $field_array_dtls."*".$data_array_dtls;die;
		$rID=$rID2=true;
		if( str_replace("'","",$update_mst_id) == "" ) //new insert cbo_ready_to_approved
		{
			$rID=sql_insert("pro_grey_prod_delivery_mst",$field_array,$data_array,1);
		}
		$rID2=sql_insert("pro_grey_prod_delivery_dtls",$field_array_dtls,$data_array_dtls,1);


		if($db_type==0)
		{
			if($rID && $rID2 )
			{
				mysql_query("COMMIT");
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$new_mrr_number[0])."**".str_replace("'",'',$dtls_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'",'',$id);
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 )
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$new_mrr_number[0])."**".str_replace("'",'',$dtls_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$id);
			}
		}
		disconnect($con);
		//die;
	}
	else if($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		if( str_replace("'","",$update_mst_id) != "")
		{
			$field_array_mst="delevery_date*company_id*location_id*buyer_id*updated_by*update_date*status_active*is_deleted";
			$data_array_mst="".$txt_delevery_date."*".$cbo_company_id."*".$cbo_location_id."*".$cbo_buyer_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";
			//$rID=sql_update("pro_grey_prod_delivery_mst",$field_array_mst,$data_array_mst,"id",$update_mst_id,0);
		}
		if( str_replace("'","",$update_mst_id) != "")
		{
			$rID3=1;
			$id_arr=array();
			$data_array_dtls=array();
			$data_array_dtls_in="";
			$field_array_dtls="current_delivery*current_delivery_qnty_in_pcs*current_reject*roll*remarks*updated_by*update_date";
			//$dtls_id=return_next_id("id", "pro_grey_prod_delivery_dtls", 1);
			//$ref_dtls_id=return_next_id("id", "pro_grey_prod_delivery_dtls", 1);
			$mst_id=str_replace("'",'',$update_mst_id);
			$field_array_dtls_in="id,mst_id,entry_form,grey_sys_id,grey_sys_number,program_no,product_id,job_no,order_id,determination_id,gsm,dia,current_delivery,current_delivery_qnty_in_pcs,size_coller_cuff,current_reject,roll,remarks,inserted_by,insert_date";


			$coma=0;
			for($i=1; $i<=$total_row; $i++)
			{
				$sys_id="hidesysid_".$i;
				$hidesysnum="hidesysnum_".$i;
				$hideprogram="hideprogrum_".$i;
				$hideprodid="hideprodid_".$i;
				$hidejob="hidejob_".$i;
				$hideorder="hideorder_".$i;
				$hideconstruc="hideconstruction_".$i;
				$hidecomposit="hidecomposition_".$i;
				$hidegsm="hidegsm_".$i;
				$hidedia="hidedia_".$i;
				$txtcurrentdelivery="txtcurrentdelivery_".$i;
				$txtcurrentRejdelivery="txtcurrentRejdelivery_".$i;
				$update_id_dtls="hiddendtlsid_".$i;
				$txt_roll="txtroll_".$i;
				$txt_remarks="txt_remarks_".$i;
				$txtcurrentdelivery_qntyinpcs="txtcurrentdelivery_qntyinpcs_".$i;
				$hidesize="hidesize_".$i;

				// echo $$update_id_dtls; die;

				if(str_replace("'",'',$$sys_id)!="")
				{
					//==============================Check===========================
					if ($order_type==1) // With Order
					{
						$previous_delivery = $sql_delivery_arr[str_replace("'","",$$sys_id)][str_replace("'","",$$hideprodid)][str_replace("'","",$$hideorder)][str_replace("'","",$$hidesize)]['delivery_qty'];
						$previous_delivery_qnty_in_pcs = $sql_delivery_arr[str_replace("'","",$$sys_id)][str_replace("'","",$$hideprodid)][str_replace("'","",$$hideorder)][str_replace("'","",$$hidesize)]['current_delivery_qnty_in_pcs'];
						$total_Prod_qty =  $sql_production_arr[str_replace("'","",$$sys_id)][str_replace("'","",$$hideprodid)][str_replace("'","",$$hideorder)][str_replace("'","",$$hidesize)]['prodcut_qty'];
						$total_Prod_qty_in_pcs =  $sql_production_arr[str_replace("'","",$$sys_id)][str_replace("'","",$$hideprodid)][str_replace("'","",$$hideorder)][str_replace("'","",$$hidesize)]['prodcut_qty_in_pcs'];
					}
					else // Without Order
					{
						$previous_delivery = $sql_delivery_arr[str_replace("'","",$$sys_id)][str_replace("'","",$$hideprodid)][str_replace("'","",$$hidesize)]['delivery_qty'];
						$previous_delivery_qnty_in_pcs = $sql_delivery_arr[str_replace("'","",$$sys_id)][str_replace("'","",$$hideprodid)][str_replace("'","",$$hidesize)]['current_delivery_qnty_in_pcs'];
						$total_Prod_qty =  $sql_production_arr[str_replace("'","",$$sys_id)][str_replace("'","",$$hideprodid)][str_replace("'","",$$hidesize)]['prodcut_qty'];
						$total_Prod_qty_in_pcs =  $sql_production_arr[str_replace("'","",$$sys_id)][str_replace("'","",$$hideprodid)][str_replace("'","",$$hidesize)]['prodcut_qty_in_pcs'];
					}

					$current_delivery = str_replace("'","",$$txtcurrentdelivery)*1;
					$current_delivery_qnty_in_pcs = str_replace("'","",$$txtcurrentdelivery_qntyinpcs)*1;
					// echo $total_Prod_qty.'='.$current_delivery.'+'.$previous_delivery.'end';
					if ($total_Prod_qty*1 < ($current_delivery + $previous_delivery))
					{
						echo "20**"."Delivery Quantity Must be Less Then Blance Quantity"."\n"."Total Production Qty=".$total_Prod_qty."\n"."Total Delivery + Current Qty= ".($current_delivery + $previous_delivery);
						die;
					}
					if ($total_Prod_qty_in_pcs*1 < ($current_delivery_qnty_in_pcs + $previous_delivery_qnty_in_pcs))
					{
						echo "20**"."Delivery Quantity in pcs Must be Less Then Blance Quantity in pcs"."\n"."Total Production Qty in pcs=".$total_Prod_qty_in_pcs."\n"."Total Delivery qnty in pcs + Current Qty in pcs= ".($current_delivery_qnty_in_pcs + $previous_delivery_qnty_in_pcs);
						die;
					}
					/*else
					{
						echo "20**"."Tipu"."\n"."Total Production Qty=".$total_Prod_qty."\n"."Total Delivery + Current Qty= ".($current_delivery + $previous_delivery);
						die;
					}*/
					//=========================================================
				}

				if(str_replace("'",'',$$update_id_dtls)!="")
				{
					$id_arr[]=str_replace("'",'',$$update_id_dtls);
					$data_array_dtls[str_replace("'",'',$$update_id_dtls)] =explode(",",("".$$txtcurrentdelivery.",".$$txtcurrentdelivery_qntyinpcs.",".$$txtcurrentRejdelivery.",".$$txt_roll.",".$$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'"));
				}
				else
				{
					if(str_replace("'","",$$txtcurrentdelivery)>0)
					{
						$dtls_id = return_next_id_by_sequence("PRO_GREY_PROD_DELI_DTLS_PK_SEQ", "pro_grey_prod_delivery_dtls", $con);
						$ref_dtls_id = return_next_id_by_sequence("PRO_GREY_PROD_DELI_DTLS_PK_SEQ", "pro_grey_prod_delivery_dtls", $con);

						if ($coma!=0) $data_array_dtls_in .=",";
						if ($coma!=0) $dtls_id=$dtls_id+1;
						$data_array_dtls_in	.="(".$dtls_id.",".$mst_id.",53,".$$sys_id.",".$$hidesysnum.",".$$hideprogram.",".$$hideprodid.",".$$hidejob.",".$$hideorder.",".$$hideconstruc.",".$$hidegsm.",".$$hidedia.",".$$txtcurrentdelivery.",".$$txtcurrentdelivery_qntyinpcs.",".$$hidesize.",".$$txtcurrentRejdelivery.",".$$txt_roll.",".$$txt_remarks.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
						$coma++;
					}

				}
			}
			// echo bulk_update_sql_statement("pro_grey_prod_delivery_dtls","id", $field_array_dtls,$data_array_dtls,$id_arr); die;
			$rID=$rID2=$rID3=true;
			if( str_replace("'","",$update_mst_id) != "")
			{
				$rID=sql_update("pro_grey_prod_delivery_mst",$field_array_mst,$data_array_mst,"id",$update_mst_id,1);
			}
			$rID2=execute_query(bulk_update_sql_statement("pro_grey_prod_delivery_dtls","id", $field_array_dtls,$data_array_dtls,$id_arr),1);
			if($data_array_dtls_in!="")
			{
				$rID3=sql_insert("pro_grey_prod_delivery_dtls",$field_array_dtls_in,$data_array_dtls_in,1);
			}
		}

		// echo "10**".$rID.'='.$rID2.'='.$rID3;die;

		if($db_type==0)
		{
			if($rID && $rID2 && $rID3)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'",'',$mst_id)."**".str_replace("'",'',$ref_dtls_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'",'',$mst_id);
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$mst_id)."**".str_replace("'",'',$ref_dtls_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$mst_id);
			}
		}
		disconnect($con);
		die;
	}


	exit();
}

?>
<script>
	function print_button_setting(company)
	{
		$('#button_data_panel').html('');
		// alert(company);
		get_php_form_data($('#cbo_company_id').val(),'print_button_variable_setting','requires/grey_feb_delivery_entry_controller');
	}

	function print_report_button_setting(report_ids)
	{
		var report_id=report_ids.split(",");
		for (var k=0; k<report_id.length; k++)
		{
			if(report_id[k]==109)
			{
				$('#button_data_panel')
					.append( '<td align="right"><input type="button" class="formbutton" value="Print" style=" width:80px" onClick="fnc_prod_delivery(4);" ></td>&nbsp;&nbsp;&nbsp;' );
			}
			if(report_id[k]==116)
			{
				$('#button_data_panel').append( '<td align="right"><input type="button" class="formbutton" value="Print 2" style=" width:80px" onClick="fnc_prod_delivery(6);" ></td>&nbsp;&nbsp;&nbsp;' );
			}
			if(report_id[k]==85)
			{
				$('#button_data_panel').append( '<td align="right"><input type="button" class="formbutton" value="Print 3" style=" width:80px" onClick="fnc_prod_delivery(7);" ></td>&nbsp;&nbsp;&nbsp;' );
			}
			if(report_id[k]==281)
			{
				$('#button_data_panel').append( '<td align="right"><input type="button" class="formbutton" value="Print(short)" style=" width:80px" onClick="fnc_prod_delivery(5);" ></td>&nbsp;&nbsp;&nbsp;' );
			}
			if(report_id[k]==305)
			{
				$('#button_data_panel').append( '<td align="right"><input type="button" class="formbutton" value="Print(Short 2)" style=" width:80px" onClick="fnc_prod_delivery(8);" ></td>&nbsp;&nbsp;&nbsp;' );
			}
		}
	}
</script>
<?
/*if($action=="current_delv_good_qty")
{
	extract($_REQUEST);
	$order_id_cond="";
	if ($po_breakdown_id!="") $order_id_cond=" and order_id=$po_breakdown_id";

	$sql_production=sql_select("SELECT sum(current_delivery) as current_delivery from pro_grey_prod_delivery_dtls where status_active=1 and is_deleted=0 and entry_form=53 and grey_sys_id=$receive_id and product_id=$prod_id $order_id_cond");
	$current_delivery=0;
	foreach ($sql_production as $key => $row)
	{
		$current_delivery=$row[csf('current_delivery')];
	}
	echo $current_delivery;

	exit();
}*/

$composition_arr=array();
$construction_arr=array();
$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
$data_array=sql_select($sql_deter);
if(count($data_array)>0)
{
	foreach( $data_array as $row )
	{
		$construction_arr[$row[csf('id')]]=$row[csf('construction')];
		if(array_key_exists($row[csf('id')],$composition_arr))
		{
			$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
		}
		else
		{
			$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
		}
	}
}



if($action=='list_generate')
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$permission=$_SESSION['page_permission'];
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_location_id=str_replace("'","",$cbo_location_id);
	$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
	$txt_prog_no=str_replace("'","",$txt_prog_no);
	$cbo_year=str_replace("'","",$cbo_year);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_ord_no=str_replace("'","",$txt_ord_no);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$cbo_status=str_replace("'","",$cbo_status);
	$update_mst_id=str_replace("'","",$update_mst_id);
	$cbo_order_type=str_replace("'","",$cbo_order_type);
	$txt_style=str_replace("'","",$txt_style);
	$cbo_knitting_source=str_replace("'","",$cbo_knitting_source);



	$hidden_receive_id=str_replace("'","",$hidden_receive_id);
	$hidden_product_id=str_replace("'","",$hidden_product_id);
	$hidden_order_id=str_replace("'","",$hidden_order_id);
	if($hidden_receive_id=="") $hidden_receive_id=0;
	if($hidden_product_id=="") $hidden_product_id=0;
	if($hidden_order_id=="") $hidden_order_id=0;

	$sql_roll=sql_select("select id,mst_id,prod_id,order_id,no_of_roll as no_of_roll from pro_grey_prod_entry_dtls where status_active=1 and is_deleted=0");
	foreach($sql_roll as $row)
	{
		$roll_arr[$row[csf("mst_id")]][$row[csf("id")]]=$row[csf("no_of_roll")];

	}
	//echo $hidden_receive_id."***".$hidden_product_id."***".$hidden_order_id;die;

	if($txt_prog_no!="")
	{
		$program_cond="and a.booking_no like '%$txt_prog_no%'";
		$program_cond2="and dtls_id like '%$txt_prog_no%'";
	}
	else
	{
		$program_cond="";
	}
	if($txt_job_no!="")
	{
		if($cbo_year!="") $cbo_year="and $mrr_date_check='$cbo_year'"; else $cbo_year="";
		if($db_type==0)
		{
			$job_no_po_id=return_field_value("group_concat(distinct b.id) as po_id","wo_po_details_master a, wo_po_break_down b","a.job_no=b.job_no_mst and a.job_no_prefix_num='$txt_job_no' and a.status_active=1 $cbo_year","po_id");
		}
		else if($db_type==2)
		{
			$job_no_po_id=return_field_value(" LISTAGG(CAST( b.id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as po_id","wo_po_details_master a, wo_po_break_down b","a.job_no=b.job_no_mst and a.job_no_prefix_num='$txt_job_no' and a.status_active=1 $cbo_year","po_id");
		}
	}

	if($txt_style!="")
	{
		if($db_type==0)
		{
			$style_no_po_id=return_field_value("group_concat(b.id) as po_id","wo_po_details_master a, wo_po_break_down b","a.job_no=b.job_no_mst and a.style_ref_no like '%$txt_style%' and a.status_active=1","po_id");
		}
		else if($db_type==2)
		{
			$style_no_po_id=return_field_value("LISTAGG(CAST( b.id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as po_id","wo_po_details_master a, wo_po_break_down b","a.job_no=b.job_no_mst and a.style_ref_no like '%$txt_style%' and a.status_active=1","po_id");
		}
		$style_no_po_id=implode(",",array_unique(explode(",",$style_no_po_id)));
	}
	//echo $style_no_po_id;die;
	if($txt_ord_no!="")
	{
		if($db_type==0)
		{
			$po_id=return_field_value("group_concat(id) as po_id","wo_po_break_down","po_number like '%$txt_ord_no' and status_active=1","po_id");
		}
		else if($db_type==2)
		{
			$po_id=return_field_value("LISTAGG(CAST( id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY id) as po_id","wo_po_break_down","po_number like '%$txt_ord_no' and status_active=1","po_id");
		}
	}
	//echo $txt_date_from;
	if($cbo_location_id!=0) $location_cond="and a.location_id=$cbo_location_id"; else $location_cond="";
	if($cbo_buyer_id!=0) $buyer_cond="and a.buyer_id=$cbo_buyer_id"; else $buyer_cond="";
	if($cbo_knitting_source!=0) $knitting_source_cond="and a.knitting_source=$cbo_knitting_source"; else $knitting_source_cond="";
	if(trim($job_no_po_id)!="") $job_cond="and c.po_breakdown_id in(".trim($job_no_po_id).")"; else $job_cond="";
	if(trim($style_no_po_id)!="") $style_cond="and c.po_breakdown_id in(".trim($style_no_po_id).")"; else $style_cond="";
	if(trim($po_id)!="") $order_cond="and c.po_breakdown_id in(".$po_id.")"; else $order_cond="";
	if($txt_date_from!="" && $txt_date_to!="") $date_cond="and a.receive_date between '$txt_date_from' and '$txt_date_to'"; else $date_cond="";

	if($db_type==0)
	{
		$select_dtls_id="group_concat(b.id) as dtls_id";
	}
	else if($db_type==2)
	{
		$select_dtls_id="LISTAGG(CAST(b.id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY  b.id) as dtls_id";
	}
	$update_row_check=array();

	if($update_mst_id=="")
	{
		if($cbo_order_type==1) // With Order
		{
			if($db_type==2) $select_year="to_char(e.insert_date,'YYYY') as job_year"; else if($db_type==0)	$select_year="year(e.insert_date) as job_year";
			$sql_mst="SELECT
						a.id as receive_id,
						a.recv_number,
						a.receive_date,
						a.booking_id as prog_id,
						a.booking_no as prog_no,
						a.buyer_id,
						a.receive_basis,
						a.knitting_source,
						a.booking_without_order,
						max(b.febric_description_id) as detarmination_id,
						max(b.gsm) as gsm,
						max(b.width) as dia_width,
						b.prod_id,
						$select_dtls_id,
						c.po_breakdown_id,
						sum(c.quantity) as current_stock,sum(c.quantity_pcs) as current_qntyInPcs,c.coller_cuff_size,
						d.po_number,
						e.job_no,
						e.style_ref_no,
						e.job_no_prefix_num,
						c.is_sales,
						$select_year

					from
						inv_receive_master a,  pro_grey_prod_entry_dtls b, order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e
					where
						a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and a.entry_form=2 and c.entry_form=2 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.trans_type=1 and a.company_id=$cbo_company_id and a.roll_maintained=0 and a.booking_without_order=0 and c.po_breakdown_id!=0 $location_cond $program_cond $buyer_cond $job_cond $style_cond $order_cond $date_cond $knitting_source_cond
					group by a.id, a.recv_number, a.receive_date, a.booking_id,a.booking_no, a.buyer_id, a.receive_basis, a.knitting_source,a.booking_without_order,b.prod_id,c.po_breakdown_id,d.po_number,e.job_no,e.style_ref_no,e.job_no_prefix_num,c.is_sales,e.insert_date,c.coller_cuff_size
					order by e.style_ref_no,a.id";


					$results=sql_select($sql_mst);
					$salesIDs="";
					foreach($results as $row)
					{
						if($row[csf("receive_basis")]==2 && $row[csf("is_sales")]==1)
						{
							$salesIDs.=$row[csf("po_breakdown_id")].",";
						}
					}
					$salesIDs=chop($salesIDs,",");
					$po_info_sql=sql_select("select a.id as sales_id,a.job_no as sales_job,e.job_no,e.style_ref_no,e.job_no_prefix_num,$select_year from fabric_sales_order_mst a,wo_booking_mst b ,wo_po_break_down d, wo_po_details_master e where a.sales_booking_no=b.booking_no and b.job_no=d.job_no_mst and d.job_no_mst=e.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.id in($salesIDs)");
					foreach($po_info_sql as $row)
					{
						$po_info_arr[$row[csf("sales_id")]]["job_no"]=$row[csf("job_no")];
						$po_info_arr[$row[csf("sales_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
						$po_info_arr[$row[csf("sales_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
						$po_info_arr[$row[csf("sales_id")]]["job_year"]=$row[csf("job_year")];

						$salesOrderJob[$row[csf("sales_id")]]["sales_job"]=$row[csf("sales_job")];
					}

		}
		else // Without Order
		{
			$sql_mst="SELECT
						a.id as receive_id,
						a.recv_number,
						a.receive_date,
						a.booking_id as prog_id,
						a.booking_no as prog_no,
						a.buyer_id,
						a.receive_basis,
						a.knitting_source,
						a.booking_without_order,
						max(b.febric_description_id) as detarmination_id,
						max(b.gsm) as gsm,
						max(b.width) as dia_width,
						b.prod_id,
						$select_dtls_id,
						sum(b.grey_receive_qnty) as current_stock,0 as current_qntyInPcs, null as coller_cuff_size
					from
						inv_receive_master a,  pro_grey_prod_entry_dtls b
					where
						a.id=b.mst_id and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_id and a.roll_maintained=0 and a.booking_without_order=1 $location_cond $program_cond $buyer_cond $date_cond $knitting_source_cond
					group by a.id, a.recv_number, a.receive_date, a.booking_id,a.booking_no, a.buyer_id, a.receive_basis, a.knitting_source,a.booking_without_order,b.prod_id
					order by a.id";
		}

	}
	else if($update_mst_id>0)
	{
		if($update_mst_id!="")
		{
			//echo "select id,grey_sys_id,product_id,job_no,order_id,current_delivery,roll,remarks,current_reject from pro_grey_prod_delivery_dtls where mst_id=$update_mst_id";
			$greyProdId=array();
			$sql_update=sql_select("select id, grey_sys_id, product_id, job_no, order_id, current_delivery,current_delivery_qnty_in_pcs, roll, remarks,current_reject,size_coller_cuff from pro_grey_prod_delivery_dtls where mst_id=$update_mst_id");
			foreach($sql_update as $row)
			{
				$greyProdId[$row[csf("grey_sys_id")]]=$row[csf("grey_sys_id")];
				if($cbo_order_type==1)
				{
					$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("order_id")]."*".$row[csf("size_coller_cuff")]]["current_delivery"] +=$row[csf("current_delivery")];
					$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("order_id")]."*".$row[csf("size_coller_cuff")]]["current_delivery_qnty_in_pcs"] +=$row[csf("current_delivery_qnty_in_pcs")];
					$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("order_id")]."*".$row[csf("size_coller_cuff")]]["id"] =$row[csf("id")];
					$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("order_id")]."*".$row[csf("size_coller_cuff")]]["roll"] =$row[csf("roll")];
					$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("order_id")]."*".$row[csf("size_coller_cuff")]]["current_reject"] =$row[csf("current_reject")];
					$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("order_id")]."*".$row[csf("size_coller_cuff")]]["remarks"] =$row[csf("remarks")];

				}
				else
				{
					$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("size_coller_cuff")]]["current_delivery"] +=$row[csf("current_delivery")];
					$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("size_coller_cuff")]]["current_delivery_qnty_in_pcs"] +=$row[csf("current_delivery_qnty_in_pcs")];
					$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("size_coller_cuff")]]["id"] =$row[csf("id")];
					$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("size_coller_cuff")]]["roll"] =$row[csf("roll")];
					$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("size_coller_cuff")]]["current_reject"] =$row[csf("current_reject")];
					$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("size_coller_cuff")]]["remarks"] =$row[csf("remarks")];
				}
			}
		}

		if(count($greyProdId)>0)
		{
			$greypordid=array_chunk($greyProdId,999, true);
			$greyprodCond="";
			$ji=0;
			foreach($greypordid as $key=>$value)
			{
				if($ji==0)
				{
					$greyprodCond=" and a.id in(".implode(",",$value).")";
				}
				else
				{
					$greyprodCond.=" or a.id in(".implode(",",$value).")";
				}
				$ji++;
			}
		}

		if($cbo_order_type==1) // With Order
		{
			if($db_type==2) $select_year="to_char(e.insert_date,'YYYY') as job_year"; else if($db_type==0)	$select_year="year(e.insert_date) as job_year";
			$sql_mst="SELECT
						a.id as receive_id,
						a.recv_number,
						a.receive_date,
						a.booking_id as prog_id,
						a.booking_no as prog_no,
						a.buyer_id,
						a.receive_basis,
						a.knitting_source,
						a.booking_without_order,
						max(b.febric_description_id) as detarmination_id,
						max(b.gsm) as gsm,
						max(b.width) as dia_width,
						b.prod_id,
						$select_dtls_id,
						c.po_breakdown_id,
						sum(c.quantity) as current_stock,sum(c.quantity_pcs) as current_qntyInPcs,c.coller_cuff_size,
						d.po_number,
						e.job_no,
						e.style_ref_no,
						e.job_no_prefix_num,
						c.is_sales,
						$select_year
					from
						inv_receive_master a,  pro_grey_prod_entry_dtls b, order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e
					where
						a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and a.entry_form=2 and c.entry_form=2 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.trans_type=1 and a.company_id=$cbo_company_id and a.roll_maintained=0 and c.po_breakdown_id!=0 $location_cond $program_cond $buyer_cond $job_cond $order_cond $date_cond $greyprodCond $knitting_source_cond
					group by a.id, a.recv_number, a.receive_date, a.booking_id,a.booking_no, a.buyer_id, a.receive_basis, a.knitting_source,a.booking_without_order,b.prod_id,c.po_breakdown_id ,d.po_number,e.job_no,e.style_ref_no,e.job_no_prefix_num,c.is_sales,e.insert_date,c.coller_cuff_size
					order by e.style_ref_no,a.id";

					$results=sql_select($sql_mst);

					$salesIDs="";
					foreach($results as $row)
					{
						if($row[csf("receive_basis")]==2 && $row[csf("is_sales")]==1)
						{
							$salesIDs.=$row[csf("po_breakdown_id")].",";
						}
					}
					$salesIDs=chop($salesIDs,",");
					$po_info_sql=sql_select("select a.id as sales_id,a.job_no as sales_job,e.job_no,e.style_ref_no,e.job_no_prefix_num,$select_year from fabric_sales_order_mst a,wo_booking_mst b ,wo_po_break_down d, wo_po_details_master e where a.sales_booking_no=b.booking_no and b.job_no=d.job_no_mst and d.job_no_mst=e.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.id in($salesIDs)");
					foreach($po_info_sql as $row)
					{
						$po_info_arr[$row[csf("sales_id")]]["job_no"]=$row[csf("job_no")];
						$po_info_arr[$row[csf("sales_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
						$po_info_arr[$row[csf("sales_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
						$po_info_arr[$row[csf("sales_id")]]["job_year"]=$row[csf("job_year")];

						$salesOrderJob[$row[csf("sales_id")]]["sales_job"]=$row[csf("sales_job")];
					}
		}
		else // Without Order
		{
			$sql_mst="SELECT
						a.id as receive_id,
						a.recv_number,
						a.receive_date,
						a.booking_id as prog_id,
						a.booking_no as prog_no,
						a.buyer_id,
						a.receive_basis,
						a.knitting_source,
						a.booking_without_order,
						max(b.febric_description_id) as detarmination_id,
						max(b.gsm) as gsm,
						max(b.width) as dia_width,
						b.prod_id,
						$select_dtls_id,
						sum(b.grey_receive_qnty) as current_stock, 0 as current_qntyInPcs,null as coller_cuff_size
					from
						inv_receive_master a,  pro_grey_prod_entry_dtls b
					where
						a.id=b.mst_id and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_id and a.roll_maintained=0 $location_cond $program_cond $buyer_cond $date_cond $greyprodCond $knitting_source_cond group by a.id, a.recv_number, a.receive_date, a.booking_id,a.booking_no, a.buyer_id, a.receive_basis, a.knitting_source,a.booking_without_order,b.prod_id
					order by a.id";
		}
	}

	$sql_production=sql_select("Select grey_sys_id,product_id,order_id,current_delivery,current_delivery_qnty_in_pcs,size_coller_cuff from pro_grey_prod_delivery_dtls where status_active=1 and is_deleted=0 and entry_form=53");
	foreach($sql_production as $row)
	{
		if($cbo_order_type==1)
		{
			$sql_production_arr[$row[csf("grey_sys_id")]][$row[csf("product_id")]][$row[csf("order_id")]][$row[csf("size_coller_cuff")]]['prodcut_qty'] +=$row[csf("current_delivery")];
			$sql_production_arr[$row[csf("grey_sys_id")]][$row[csf("product_id")]][$row[csf("order_id")]][$row[csf("size_coller_cuff")]]['prodcut_qty_in_pcs'] +=$row[csf("current_delivery_qnty_in_pcs")];
		}
		else
		{
			$sql_production_arr[$row[csf("grey_sys_id")]][$row[csf("product_id")]][$row[csf("size_coller_cuff")]]['prodcut_qty'] +=$row[csf("current_delivery")];
			$sql_production_arr[$row[csf("grey_sys_id")]][$row[csf("product_id")]][$row[csf("size_coller_cuff")]]['prodcut_qty_in_pcs'] +=$row[csf("current_delivery_qnty_in_pcs")];
		}
	}

	$buyer_array = return_library_array("select id, short_name from  lib_buyer","id","short_name");
	//var_dump($update_row_check);die;


	$sql_plan = "select mst_id, booking_no, dtls_id as prog_no
	from ppl_planning_entry_plan_dtls where status_active=1 and is_deleted=0 $program_cond2  group by dtls_id, mst_id, booking_no";

	$res_plan = sql_select($sql_plan);
	foreach ($res_plan as $rowPlan) {
		$progrum[$rowPlan[csf('prog_no')]] = $rowPlan[csf('booking_no')];
	}



	ob_start();
	?>

<div style="width:2095px;" id="">
        <form name="delivery_details" id="delivery_details" autocomplete="off" >
	<div id="report_print" style="width:2025px;">
    <table width="2075" class="rpt_table" id="tbl_header" cellpadding="0" cellspacing="1" rules="all">
    	<thead>
        	<th width="30">Sl</th>
            <th width="120">System Id</th>
            <th width="90">Progm/ Booking No</th>
            <th width="100">Production Basis</th>
            <th width="75">Knitting Source</th>
            <th width="70">Prd. date</th>
            <th width="50">Prod. Id</th>
            <th width="40">Year</th>
            <th width="60">Job No</th>
            <th width="100">Style No</th>
            <th width="50">Buyer</th>
            <th width="90">Order No</th>
            <th width="110">Construction </th>
            <th width="110">Composition</th>
            <th width="40">GSM</th>
            <th width="40">Dia</th>
            <th width="40">Roll</th>
            <th width="40">Size</th>
            <th width="70">Prod. Qty In Pcs</th>
            <th width="70">Total Delivery Qty In Pcs</th>
            <th width="70">Balance Qty In Pcs</th>
            <th width="75" >Current Delv (Qty In Pcs)</th>
            <th width="70">Prod. qty</th>
            <th width="70">Total Delivery </th>
            <th width="70">Balance</th>
            <th width="75" >Current Delv (Good qty)</th>
            <th width="70">Current Delv (reject qty)</th>
            <th width="57" >Roll</th>
            <th>Remarks</th>
        </thead>
    </table>
    <div style="width:2093px; overflow-y:scroll; max-height:200px;font-size:12px; overflow-x:hidden;" id="scroll_body">
    <table width="2075" class="rpt_table" id="table_body" cellpadding="0" cellspacing="1" rules="all">
    	<tbody>
        <?
		//var_dump($update_mst_id);
		$i=1;
		$result=sql_select($sql_mst);
		$current_row_array=array();
		foreach($result as $row)
		{
			if($row[csf("receive_basis")]==2 && $row[csf("is_sales")]==1)
			{
				$jobNo=$po_info_arr[$row[csf("po_breakdown_id")]]["job_no"];
				$styleRef=$po_info_arr[$row[csf("po_breakdown_id")]]["style_ref_no"];
				$jobNoPrefix=$po_info_arr[$row[csf("po_breakdown_id")]]["job_no_prefix_num"];
				$jobYear=$po_info_arr[$row[csf("po_breakdown_id")]]["job_year"];
				$poNumber=$salesOrderJob[$row[csf("po_breakdown_id")]]["sales_job"];

			}
			else
			{
				$jobNo=$row[csf("job_no")];
				$styleRef=$row[csf("style_ref_no")];
				$jobNoPrefix=$row[csf("job_no_prefix_num")];
				$jobYear=$row[csf("job_year")];
				$poNumber=$row[csf("po_number")];
			}

			if ($i%2==0)
			$bgcolor="#E9F3FF";
			else
			$bgcolor="#FFFFFF";

			if($row[csf("booking_without_order")]==0)
			{
				$index_pk=$row[csf("receive_id")]."*".$row[csf("prod_id")]."*".$row[csf("po_breakdown_id")]."*".$row[csf("coller_cuff_size")];
				$tot_delivery=$sql_production_arr[$row[csf("receive_id")]][$row[csf("prod_id")]][$row[csf("po_breakdown_id")]][$row[csf("coller_cuff_size")]]['prodcut_qty'];
				$tot_delivery_qntyInPcs=$sql_production_arr[$row[csf("receive_id")]][$row[csf("prod_id")]][$row[csf("po_breakdown_id")]][$row[csf("coller_cuff_size")]]['prodcut_qty_in_pcs'];
			}
			else
			{
				$index_pk=$row[csf("receive_id")]."*".$row[csf("prod_id")]."*".$row[csf("coller_cuff_size")];
				$tot_delivery=$sql_production_arr[$row[csf("receive_id")]][$row[csf("prod_id")]][$row[csf("coller_cuff_size")]]['prodcut_qty'];
				$tot_delivery_qntyInPcs=$sql_production_arr[$row[csf("receive_id")]][$row[csf("prod_id")]][$row[csf("coller_cuff_size")]]['prodcut_qty_in_pcs'];
			}

			$dtls_id_all=implode(",",array_unique(explode(",",$row[csf("dtls_id")])));
			$current_stock=$row[csf('current_stock')];
			//$tot_delivery=number_format($tot_delivery,2);
			//$current_stock=number_format($row['current_stock'],2);
			//if($update_row_check[$index_pk]["id"]) echo $update_row_check[$index_pk]["id"]."xx";

			if($update_mst_id=="")
			{
				if($cbo_status==1) // Pending
				{
					//$tot_delivery=$tot_delivery*1;
					//var_dump($current_stock);
					//var_dump($tot_delivery);
					if( $current_stock > $tot_delivery)
					{
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30" align="center"><p><? echo $i; ?>
							<input type="hidden" id="hidesysid_<? echo $i;?>" name="hidesysid_<? echo $i;?>" value="<? echo $row[csf("receive_id")];?>"  />
                            <input type="hidden" id="hidefindtls_<? echo $i;?>" name="hidefindtls_<? echo $i;?>" value="<? echo $dtls_id_all;?>"  />
                            &nbsp;
							</p></td>
							<td width="120"><p><input type="hidden" id="hidesysnum_<? echo $i;?>" name="hidesysnum_<? echo $i;?>" value="<? echo $row[csf("recv_number")];?>"  />
							<?
							 echo $row[csf("recv_number")];
							?>&nbsp;
							</p></td>
							<td width="90" align="center"><p>
							<input type="hidden" id="hideprogrum_<? echo $i;?>" name="hideprogrum_<? echo $i;?>" value="<? echo $row[csf("prog_id")];?>"  />
							<? if($row[csf("prog_id")]==0) echo "Independent"; else echo $row[csf("prog_no")]."<br/>".$progrum[$row[csf('prog_no')]]; ?>&nbsp;
							</p></td>
                            <td width="100" align="center"><p>
							<?
							  if($row[csf("receive_basis")]==2)
							  {
								  echo "Knitting Plan";
							  }
							  else if($row[csf("receive_basis")]==1)
							  {
								  if($row[csf("booking_without_order")]==1)
								  {
									  echo "Without order";
								  }
								  else
								  {
									  echo "With order";
								  }

							  }
							  else
							  {
								  echo "Independent";
							  }
							?>&nbsp;
							</p></td>
							<td width="75" align="center"><p>
							<?
							  if($row[csf("knitting_source")]==1)  echo "In-House"; if($row[csf("knitting_source")]==3) echo "Sub-Contract";
							?>&nbsp;
							</p></td>
							<td width="70" align="center"><p><?  if($row[csf("receive_date")]!='0000-00-00')  echo change_date_format($row[csf("receive_date")]); else echo ""; ?>&nbsp;</p></td>
							<td width="50" align="center"><p>
							<input type="hidden" id="hideprodid_<? echo $i;?>" name="hideprodid_<? echo $i;?>" value="<? echo $row[csf("prod_id")];?>"  />
							<? echo $row[csf("prod_id")]; ?>&nbsp;
							</p></td>
							<td width="40" align="center"><p><? echo $jobYear; ?>&nbsp;</p></td>
							<td width="60" align="center"><p>
							<input type="hidden" id="hidejob_<? echo $i;?>" name="hidejob_<? echo $i;?>" value="<? echo $jobNo;?>"  />
							<? echo $jobNoPrefix; ?>&nbsp;
							</p></td>
                            <td width="100" align="center"><p><? echo  $styleRef; ?>&nbsp;</p></td>
							<td width="50"><p><? echo $buyer_array[$row[csf("buyer_id")]]; ?>&nbsp;</p></td>
							<td width="90"><p>
							<input type="hidden" id="hideorder_<? echo $i;?>" name="hideorder_<? echo $i;?>" value="<? echo $row[csf("po_breakdown_id")];?>"  />
							<? echo $poNumber; ?>&nbsp;
							</p></td>
							<td width="110"><p>
							 <input type="hidden" id="hideconstruction_<? echo $i;?>" name="hideconstruction_<? echo $i;?>" value="<? echo $row[csf("detarmination_id")];?>"  />
							<? echo $construction_arr[$row[csf("detarmination_id")]]; ?>&nbsp;
							</p></td>
							<td width="110"><p>
							<input type="hidden" id="hidecomposition_<? echo $i;?>" name="hidecomposition_<? echo $i;?>" value="<? echo $row[csf("detarmination_id")];?>"  />
							<? echo $composition_arr[$row[csf("detarmination_id")]]; ?>&nbsp;
							</p></td>
							<td width="40" align="center"><p>
							 <input type="hidden" id="hidegsm_<? echo $i;?>" name="hidegsm_<? echo $i;?>" value="<? echo $row[csf("gsm")]; ?>"  />
							<? echo $row[csf("gsm")]; ?>&nbsp;
							</p></td>
							<td width="40" align="center"><p>
							 <input type="hidden" id="hidedia_<? echo $i;?>" name="hidedia_<? echo $i;?>" value="<? echo $row[csf("dia_width")]; ?>"  />
							<? echo $row[csf("dia_width")]; ?>&nbsp;
							</p></td>
							<td width="40" align="center"><p>
							<? echo $roll_arr[$row[csf("receive_id")]][$row[csf("dtls_id")]]; ?>&nbsp;
							</p></td>




							<td width="40" align="center"><p>
								<input type="hidden" id="hidesize_<? echo $i;?>" name="hidesize_<? echo $i;?>" value="<? echo $row[csf("coller_cuff_size")]; ?>"  />
								<? echo $row[csf("coller_cuff_size")]; ?>&nbsp;
							</p></td>
							<td width="70" align="center"><p>
							<? echo number_format($row[csf("current_qntyInPcs")],2);$total_stock_qntyInPcs+=$row[csf("current_qntyInPcs")]; ?>&nbsp;
							</p></td>
							<td width="70" align="center"><p>
							<? echo  number_format($tot_delivery_qntyInPcs,2);
								$gt_tot_delivery_qntyInPcs+=$tot_delivery_qntyInPcs; ?>&nbsp;
							</p></td>
							<td width="70" align="center"><p>
							<?  $balance_qnty_in_pcs=$row[csf("current_qntyInPcs")]-$tot_delivery_qntyInPcs;
								echo number_format($balance_qnty_in_pcs,2);
								$total_balance_qntyInPcs+=$balance_qnty_in_pcs;

								?><input type="hidden" name="totbalanceQntyinpcs" id="hidtotalqtyInPcsTd_<? echo $i; ?>" value="<? echo $balance_qnty_in_pcs; ?>"></td>
							</p></td>

							<td width="75">
									<input type="text" id="txtcurrentdelivery_qntyinpcs_<? echo $i;?>" name="txtcurrentdelivery_qntyinpcs_<? echo $i;?>" class="text_boxes_numeric" style="width:60px;" value="<? echo $balance_qnty_in_pcs;
									$total_delivey_balance_qnty_inpcs += $balance_qnty_in_pcs; ?>" onBlur="setHidevalPcs(<? echo $i.','.$row[csf("receive_id")].','.$row[csf("prod_id")].','.$row[csf("po_breakdown_id")]; ?>)"/>

									<input type="hidden" id="hiddenCurrentValQntyInPcs_<? echo $i;?>" value="<? echo $balance_qnty_in_pcs;?>">
							</td>



							<td width="70" align="right" title="current"><p><? echo number_format($row[csf("current_stock")],2); $total_stock+=$row[csf("current_stock")]; ?>&nbsp;</p></td>
							<td width="70" align="right" ><p>
							<?
							echo number_format($tot_delivery,2);
							$gt_tot_delivery+=$tot_delivery;

							//echo $job_po_arr[$po_key]["po_id"];
							?>&nbsp;</p>
							</td>
							<td width="70" align="right" id="totalqtyTd_<? echo $i; ?>"><p><? $balance=($row[csf("current_stock")]-$tot_delivery); echo number_format($balance,2); $total_balance+=$balance; ?>&nbsp;</p>
								<input type="hidden" name="totbalance" id="hidtotalqtyTd_<? echo $i; ?>" value="<? echo $balance ?>"></td>
							<td width="75">
							<p><input type="hidden" id="hiddendtlsid_<? echo $i;?>" name="hiddendtlsid_<? echo $i;?>"  style="width:60px;" value="" />
								<input type="text" id="txtcurrentdelivery_<? echo $i;?>" name="txtcurrentdelivery_<? echo $i;?>" class="text_boxes_numeric" style="width:60px;" value="<? echo $balance; $total_delivey_balance += $balance; ?>" onBlur="setHideval(<? echo $i.','.$row[csf("receive_id")].','.$row[csf("prod_id")].','.$row[csf("po_breakdown_id")]; ?>)" />
						<input type="hidden" id="hiddenCurrentVal_<? echo $i;?>" value="<? echo $balance;?>">
							&nbsp;</p></td>
                            <td width="70">
							<p>
								<input type="text" id="txtcurrentRejdelivery_<? echo $i;?>" name="txtcurrentRejdelivery_<? echo $i;?>" class="text_boxes_numeric" style="width:60px;" value="" onBlur="total_reject(<? echo $i; ?>)" />
						<input type="hidden" id="hidden_current_rej_val_<? echo $i;?>" value="">
							&nbsp;</p></td>
							<td width="57"><p>
								<input type="text" id="txtroll_<? echo $i;?>" name="txtroll_<? echo $i;?>" class="text_boxes_numeric" style="width:45px;" value="" onBlur="total_roll(<? echo $i; ?>)"  />
						<input type="hidden" id="hideroll_<? echo $i;?>" value=""  >
							&nbsp;</p></td>


                            <td ><p>
								<input type="text" id="txt_remarks_<? echo $i;?>" name="txt_remarks_<? echo $i;?>" class="text_boxes" style="width:85px;" value=""  />
						  		<input type="hidden" id="hideremarks_<? echo $i;?>" value=""  >
							&nbsp;</p></td>

						</tr>
						<?
						$i++;
					}
				}

				else // Full Delivery
				{
					if($current_stock<=$tot_delivery)
					{
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30" align="center"><p><? echo $i; ?>
							<input type="hidden" id="hidesysid_<? echo $i;?>" name="hidesysid_<? echo $i;?>" value="<? echo $row[csf("receive_id")];?>"  />
                            <input type="hidden" id="hidefindtls_<? echo $i;?>" name="hidefindtls_<? echo $i;?>" value="<? echo $dtls_id_all;?>"  />
                            &nbsp;
							</p></td>
							<td width="120"><p><input type="hidden" id="hidesysnum_<? echo $i;?>" name="hidesysnum_<? echo $i;?>" value="<? echo $row[csf("recv_number")];?>"  />
							<?
							 echo $row[csf("recv_number")];
							?>&nbsp;
							</p></td>
							<td width="90" align="center"><p>
							<input type="hidden" id="hideprogrum_<? echo $i;?>" name="hideprogrum_<? echo $i;?>" value="<? echo $row[csf("prog_id")];?>"  />
							<? if($row[csf("prog_id")]==0) echo "Independent"; else echo $row[csf("prog_no")]."<br/>".$progrum[$row[csf('prog_no')]]; ?>&nbsp;
							</p></td>
                            <td width="100" align="center"><p>
							<?
							  if($row[csf("receive_basis")]==2)
							  {
								  echo "Knitting Plan";
							  }
							  else if($row[csf("receive_basis")]==1)
							  {
								  if($row[csf("booking_without_order")]==1)
								  {
									  echo "Without order";
								  }
								  else
								  {
									  echo "With order";
								  }

							  }
							  else
							  {
								  echo "Independent";
							  }
							?>&nbsp;
							</p></td>
							<td width="75" align="center"><p>
							<?
							  if($row[csf("knitting_source")]==1)  echo "In-House"; if($row[csf("knitting_source")]==3) echo "Sub-Contract";
							?>&nbsp;
							</p></td>
							<td width="70" align="center"><p><?  if($row[csf("receive_date")]!='0000-00-00')  echo change_date_format($row[csf("receive_date")]); else echo ""; ?>&nbsp;</p></td>
							<td width="50" align="center"><p>
							<input type="hidden" id="hideprodid_<? echo $i;?>" name="hideprodid_<? echo $i;?>" value="<? echo $row[csf("prod_id")];?>"  />
							<? echo $row[csf("prod_id")]; ?>&nbsp;
							</p></td>
							<td width="40" align="center"><p><? echo  $jobYear; ?>&nbsp;</p></td>
							<td width="60" align="center"><p>
							<input type="hidden" id="hidejob_<? echo $i;?>" name="hidejob_<? echo $i;?>" value="<? echo $jobNo;?>"  />
							<? echo $jobNoPrefix; ?>&nbsp;
							</p></td>
                            <td width="100" align="center"><p><? echo $styleRef; ?>&nbsp;</p></td>
							<td width="50"><p><? echo $buyer_array[$row[csf("buyer_id")]]; ?>&nbsp;</p></td>
							<td width="90"><p>
							<input type="hidden" id="hideorder_<? echo $i;?>" name="hideorder_<? echo $i;?>" value="<? echo $row[csf("po_breakdown_id")];?>"  />
							<? echo $poNumber; ?>&nbsp;
							</p></td>
							<td width="110"><p>
							 <input type="hidden" id="hideconstruction_<? echo $i;?>" name="hideconstruction_<? echo $i;?>" value="<? echo $row[csf("detarmination_id")];?>"  />
							<? echo $construction_arr[$row[csf("detarmination_id")]]; ?>&nbsp;
							</p></td>
							<td width="110"><p>
							<input type="hidden" id="hidecomposition_<? echo $i;?>" name="hidecomposition_<? echo $i;?>" value="<? echo $row[csf("detarmination_id")];?>"  />
							<? echo $composition_arr[$row[csf("detarmination_id")]]; ?>&nbsp;
							</p></td>
							<td width="40" align="center"><p>
							 <input type="hidden" id="hidegsm_<? echo $i;?>" name="hidegsm_<? echo $i;?>" value="<? echo $row[csf("gsm")]; ?>"  />
							<? echo $row[csf("gsm")]; ?>&nbsp;
							</p></td>
							<td width="40" align="center"><p>
							 <input type="hidden" id="hidedia_<? echo $i;?>" name="hidedia_<? echo $i;?>" value="<? echo $row[csf("dia_width")]; ?>"  />
							<? echo $row[csf("dia_width")]; ?>&nbsp;
							</p></td>
							<td width="40" align="center"><p>
							<? echo $roll_arr[$row[csf("receive_id")]][$row[csf("dtls_id")]]; ?>&nbsp;
							</p></td>


							<td width="40" align="center"><p>
								<input type="hidden" id="hidesize_<? echo $i;?>" name="hidesize_<? echo $i;?>" value="<? echo $row[csf("coller_cuff_size")]; ?>"  />
								<? echo $row[csf("coller_cuff_size")]; ?>&nbsp;
							</p></td>
							<td width="70" align="center"><p>
							<? echo number_format($row[csf("current_qntyInPcs")],2);$total_stock_qntyInPcs+=$row[csf("current_qntyInPcs")]; ?>&nbsp;
							</p></td>
							<td width="70" align="center"><p>
							<? echo  number_format($tot_delivery_qntyInPcs,2);
								$gt_tot_delivery_qntyInPcs+=$tot_delivery_qntyInPcs; ?>&nbsp;
							</p></td>
							<td width="70" align="center"><p>
							<?  $balance_qnty_in_pcs=$row[csf("current_qntyInPcs")]-$tot_delivery_qntyInPcs;
								echo number_format($balance_qnty_in_pcs,2);
								$total_balance_qntyInPcs+=$balance_qnty_in_pcs;

								?><input type="hidden" name="totbalanceQntyinpcs" id="hidtotalqtyInPcsTd_<? echo $i; ?>" value="<? echo $balance_qnty_in_pcs; ?>"></td>
							</p></td>

							<td width="75">
									<input type="text" id="txtcurrentdelivery_qntyinpcs_<? echo $i;?>" name="txtcurrentdelivery_qntyinpcs_<? echo $i;?>" class="text_boxes_numeric" style="width:60px;" value="<? echo $balance_qnty_in_pcs;
									$total_delivey_balance_qnty_inpcs += $balance_qnty_in_pcs; ?>" onBlur="setHidevalPcs(<? echo $i.','.$row[csf("receive_id")].','.$row[csf("prod_id")].','.$row[csf("po_breakdown_id")]; ?>)"/>

									<input type="hidden" id="hiddenCurrentValQntyInPcs_<? echo $i;?>" value="<? echo $balance_qnty_in_pcs;?>">
							</td>


							<td width="70" align="right" ><p><? echo number_format($row[csf("current_stock")],2); $total_stock+=$row[csf("current_stock")]; ?>&nbsp;</p></td>
							<td width="70" align="right" ><p>
							<?
							echo number_format($tot_delivery,2);
							$gt_tot_delivery+=$tot_delivery;
							//echo $job_po_arr[$po_key]["po_id"];
							?>&nbsp;</p>
							</td>
							<td width="70" align="right" id="totalqtyTd_<? echo $i; ?>"><p><? $balance=($row[csf("current_stock")]-$tot_delivery); echo number_format($balance,2); $total_balance+=$balance; ?>&nbsp;</p>
							<input type="hidden" name="totbalance" id="hidtotalqtyTd_<? echo $i; ?>" value="<? echo $balance ?>"></td>
							<td width="75">
							<p><input type="hidden" id="hiddendtlsid_<? echo $i;?>" name="hiddendtlsid_<? echo $i;?>"  style="width:60px;" value="" />
								<input type="text" id="txtcurrentdelivery_<? echo $i;?>" name="txtcurrentdelivery_<? echo $i;?>" class="text_boxes_numeric" style="width:60px;" value="<? echo $balance; $total_delivey_balance += $balance;?>" onBlur="setHideval(<? echo $i.','.$row[csf("receive_id")].','.$row[csf("prod_id")].','.$row[csf("po_breakdown_id")]; ?>)"  />
						<input type="hidden" id="hiddenCurrentVal_<? echo $i;?>" value="<? echo $balance; ?>">
							&nbsp;</p></td>
                            <td width="70">
							<p>
								<input type="text" id="txtcurrentRejdelivery_<? echo $i;?>" name="txtcurrentRejdelivery_<? echo $i;?>" class="text_boxes_numeric" style="width:60px;" value="" onBlur="total_reject(<? echo $i; ?>)" />
						<input type="hidden" id="hidden_current_rej_val_<? echo $i;?>" value="">
							&nbsp;</p></td>
							<td width="57"><p>
								<input type="text" id="txtroll_<? echo $i;?>" name="txtroll_<? echo $i;?>" class="text_boxes_numeric" style="width:45px;" value="" onBlur="total_roll(<? echo $i; ?>)"  />
						<input type="hidden" id="hideroll_<? echo $i;?>" value=""  >
							&nbsp;</p></td>

                            <td ><p>
								<input type="text" id="txt_remarks_<? echo $i;?>" name="txt_remarks_<? echo $i;?>" class="text_boxes" style="width:85px;" value=""  />
						  		<input type="hidden" id="hideremarks_<? echo $i;?>" value=""  >
							&nbsp;</p></td>

						</tr>
						<?
						$i++;
					}
				}
			}
			else
			{
				//echo $index_pk."<br>";
				//echo $update_row_check[$index_pk]["current_delivery"]."<br>";
				if($update_row_check[$index_pk]["current_delivery"]>0)
				{
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="background-color:#FF6;" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td width="30" align="center"><p><? echo $i; ?>
						<input type="hidden" id="hidesysid_<? echo $i;?>" name="hidesysid_<? echo $i;?>" value="<? echo $row[csf("receive_id")];?>"  />
                        <input type="hidden" id="hidefindtls_<? echo $i;?>" name="hidefindtls_<? echo $i;?>" value="<? echo $dtls_id_all;?>"  />
                        &nbsp;
						</p></td>
						<td width="120"><p><input type="hidden" id="hidesysnum_<? echo $i;?>" name="hidesysnum_<? echo $i;?>" value="<? echo $row[csf("recv_number")];?>"  />
						<?
						echo $row[csf("recv_number")];
						?>&nbsp;
						</p></td>
						<td width="90" align="center"><p>
						<input type="hidden" id="hideprogrum_<? echo $i;?>" name="hideprogrum_<? echo $i;?>" value="<? echo $row[csf("prog_id")];?>"  />
						<? if($row[csf("prog_id")]==0) echo "Independent"; else echo $row[csf("prog_no")]."<br/>".$progrum[$row[csf('prog_no')]]; ?>&nbsp;
						</p></td>
                        <td width="100" align="center"><p>
							<?
							  if($row[csf("receive_basis")]==2)
							  {
								  echo "Knitting Plan";
							  }
							  else if($row[csf("receive_basis")]==1)
							  {
								  if($row[csf("booking_without_order")]==1)
								  {
									  echo "Without order";
								  }
								  else
								  {
									  echo "With order";
								  }

							  }
							  else
							  {
								  echo "Independent";
							  }
							?>&nbsp;
							</p></td>
						<td width="75" align="center"><p>
						<?
						if($row[csf("knitting_source")]==1)  echo "In-House"; if($row[csf("knitting_source")]==3) echo "Sub-Contract";
						?>&nbsp;
						</p></td>
						<td width="70" align="center"><p><?  if($row[csf("receive_date")]!='000-00-000')  echo change_date_format($row[csf("receive_date")]); else echo ""; ?>&nbsp;</p></td>
						<td width="50" align="center"><p>
						<input type="hidden" id="hideprodid_<? echo $i;?>" name="hideprodid_<? echo $i;?>" value="<? echo $row[csf("prod_id")];?>"  />
						<? echo $row[csf("prod_id")]; ?>&nbsp;
						</p></td>
						<td width="40" align="center"><p><? echo  $jobYear; ?>&nbsp;</p></td>
						<td width="60" align="center"><p>
						<input type="hidden" id="hidejob_<? echo $i;?>" name="hidejob_<? echo $i;?>" value="<? echo $jobNo;?>"  />
						<? echo $jobNoPrefix; ?>&nbsp;
						</p></td>
                        <td width="100" align="center"><p><? echo  $styleRef; ?>&nbsp;</p></td>
						<td width="50"><p><? echo $buyer_array[$row[csf("buyer_id")]]; ?>&nbsp;</p></td>
						<td width="90"><p>
						<input type="hidden" id="hideorder_<? echo $i;?>" name="hideorder_<? echo $i;?>" value="<? echo $row[csf("po_breakdown_id")];?>"  />
						<? echo $poNumber; ?>&nbsp;
						</p></td>
						<td width="110"><p>
						<input type="hidden" id="hideconstruction_<? echo $i;?>" name="hideconstruction_<? echo $i;?>" value="<? echo $row[csf("detarmination_id")];?>"  />
						<?  echo $construction_arr[$row[csf("detarmination_id")]]; ?>&nbsp;
						</p></td>
						<td width="110"><p>
						<input type="hidden" id="hidecomposition_<? echo $i;?>" name="hidecomposition_<? echo $i;?>" value="<? echo $row[csf("detarmination_id")];?>"  />
						<? echo $composition_arr[$row[csf("detarmination_id")]]; ?>&nbsp;
						</p></td>
						<td width="40" align="center"><p>
						<input type="hidden" id="hidegsm_<? echo $i;?>" name="hidegsm_<? echo $i;?>" value="<? echo $row[csf("gsm")]; ?>"  />
						<? echo $row[csf("gsm")]; ?>&nbsp;
						</p></td>
						<td width="40" align="center"><p>
						<input type="hidden" id="hidedia_<? echo $i;?>" name="hidedia_<? echo $i;?>" value="<? echo $row[csf("dia_width")]; ?>"  />
						<? echo $row[csf("dia_width")]; ?>&nbsp;
						</p></td>
						<td width="40" align="center"><p>
						<? echo $roll_arr[$row[csf("receive_id")]][$row[csf("dtls_id")]]; ?>&nbsp;
						</p></td>



						<td width="40" align="center"><p>
							<input type="hidden" id="hidesize_<? echo $i;?>" name="hidesize_<? echo $i;?>" value="<? echo $row[csf("coller_cuff_size")]; ?>"  />
							<? echo $row[csf("coller_cuff_size")]; ?>&nbsp;
						</p></td>
						<td width="70" align="center"><p>
						<? echo number_format($row[csf("current_qntyInPcs")],2);$total_stock_qntyInPcs+=$row[csf("current_qntyInPcs")]; ?>&nbsp;
						</p></td>
						<td width="70" align="center"><p>
						<?
							$tot_delivery_qntyInPcs=$tot_delivery_qntyInPcs-$update_row_check[$index_pk]["current_delivery_qnty_in_pcs"];
							echo number_format($tot_delivery_qntyInPcs,2);
							$gt_tot_delivery_qntyInPcs+=$tot_delivery_qntyInPcs; ?>&nbsp;
						</p></td>
						<td width="70" align="center"><p>
						<?  $balance_qnty_in_pcs=$row[csf("current_qntyInPcs")]-$tot_delivery_qntyInPcs;
							echo number_format($balance_qnty_in_pcs,2);
							$total_balance_qntyInPcs+=$balance_qnty_in_pcs;

							?><input type="hidden" name="totbalanceQntyinpcs" id="hidtotalqtyInPcsTd_<? echo $i; ?>" value="<? echo $balance_qnty_in_pcs; ?>"></td>
						</p></td>

						<td width="75">
								<input type="text" id="txtcurrentdelivery_qntyinpcs_<? echo $i;?>" name="txtcurrentdelivery_qntyinpcs_<? echo $i;?>" class="text_boxes_numeric" style="width:60px;" value="<? echo $update_row_check[$index_pk]["current_delivery_qnty_in_pcs"]; $total_delivey_balance_qnty_inpcs+=$update_row_check[$index_pk]["current_delivery_qnty_in_pcs"];?>" onBlur="setHidevalPcs(<? echo $i.','.$row[csf("receive_id")].','.$row[csf("prod_id")].','.$row[csf("po_breakdown_id")]; ?>)"/>

								<input type="hidden" id="hiddenCurrentValQntyInPcs_<? echo $i;?>" value="<? echo $update_row_check[$index_pk]["current_delivery_qnty_in_pcs"];?>">
						</td>



						<td width="70" align="right" ><p><? echo number_format($row[csf("current_stock")],2); $total_stock+=$row[csf("current_stock")]; ?>&nbsp;</p></td>
						<td width="70" align="right" ><p>
						<?
						$tot_delivery=$tot_delivery-$update_row_check[$index_pk]["current_delivery"];
						echo number_format($tot_delivery,2);
						$gt_tot_delivery+=$tot_delivery;
						//echo $job_po_arr[$po_key]["po_id"];  onKeyUp=" setHideval(<? echo $i; )"  onChange="total_current_val(<? echo $i;  )"
						?>&nbsp;</p>
						</td>
						<td width="70" align="right" id="totalqtyTd_<? echo $i; ?>"><p><? $balance=($row[csf("current_stock")]-$tot_delivery); echo number_format($balance,2); $total_balance+=$balance; ?>&nbsp;</p>
						<input type="hidden" name="totbalance" id="hidtotalqtyTd_<? echo $i; ?>" value="<? echo $balance ?>"></td>


						<td width="75">
						<p><input type="hidden" id="hiddendtlsid_<? echo $i;?>" name="hiddendtlsid_<? echo $i;?>"  style="width:60px;" value="<? echo $update_row_check[$index_pk]["id"]; ?>" />
						<input type="text" id="txtcurrentdelivery_<? echo $i;?>" name="txtcurrentdelivery_<? echo $i;?>" class="text_boxes_numeric" style="width:60px;" value="<? echo $update_row_check[$index_pk]["current_delivery"]; $total_delivey_balance+=$update_row_check[$index_pk]["current_delivery"]; ?>" onBlur="setHideval(<? echo $i.','.$row[csf("receive_id")].','.$row[csf("prod_id")].','.$row[csf("po_breakdown_id")]; ?>)" />
						<input type="hidden" id="hiddenCurrentVal_<? echo $i;?>" value="<? echo $update_row_check[$index_pk]["current_delivery"]; ?>">
						&nbsp;</p></td>
                        <td width="70">
							<p>
								<input type="text" id="txtcurrentRejdelivery_<? echo $i;?>" name="txtcurrentRejdelivery_<? echo $i;?>" class="text_boxes_numeric" style="width:60px;" value="<? echo $update_row_check[$index_pk]["current_reject"]; $total_reject+=$update_row_check[$index_pk]["current_reject"];  ?>" onBlur="total_reject(<? echo $i; ?>)" />
						<input type="hidden" id="hidden_current_rej_val_<? echo $i;?>" value="<? echo $update_row_check[$index_pk]["current_reject"]; ?>">
							&nbsp;</p></td>
						<td width="57"><p>
						<input type="text" id="txtroll_<? echo $i;?>" name="txtroll_<? echo $i;?>" class="text_boxes_numeric" style="width:45px;" value="<? echo $update_row_check[$index_pk]["roll"]; $to_roll+=$update_row_check[$index_pk]["roll"]; ?>" onBlur="total_roll(<? echo $i;?>)" />
						<input type="hidden" id="hideroll_<? echo $i;?>" value="<? echo $update_row_check[$index_pk]["roll"]; ?>"  >
						&nbsp;</p></td>
                        <td ><p>
						<input type="text" id="txt_remarks_<? echo $i;?>" name="txt_remarks_<? echo $i;?>" class="text_boxes" style="width:85px;" value="<? echo $update_row_check[$index_pk]["remarks"]; ?>" />
						<input type="hidden" id="hideremarks_<? echo $i;?>" value="<? echo $update_row_check[$index_pk]["remarks"]; ?>"  >
						&nbsp;</p></td>

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

    <table width="2075" class="rpt_table" id="tbl_footer" cellpadding="0" cellspacing="1" rules="all">
    	<tfoot>
        	<th colspan="18" align="right">Total:</th>
            <th width="70" id="value_total_prod_qntyinpcs"><? echo number_format($total_stock_qntyInPcs,2); ?></th>
            <th width="70" id="value_total_delivery_qntyinpcs"><? echo number_format($gt_tot_delivery_qntyInPcs,2); ?></th>
            <th width="70" id="value_total_balance_delivery_qntyinpcs"><? echo number_format($total_balance_qntyInPcs,2); ?></th>
            <th width="75" id="value_current_qntyinpcs_val"><? echo number_format($total_delivey_balance_qnty_inpcs,2); ?></th>


            <th width="70" id="value_total_stock"><? echo number_format($total_stock,2); ?></th>
            <th width="70" id="value_gt_tot_delivery"><? echo number_format($gt_tot_delivery,2); ?></th>
            <th width="70" id="value_total_balance"><? echo number_format($total_balance,2); ?></th>
            <th width="75" id="total_current_val" align="right"> <? echo number_format($total_delivey_balance,2); //echo number_format($to_delivey,2); ?></th>
            <th width="72" id="total_reject" align="right"><? echo number_format($total_reject,0); ?></th>
            <th width="57" id="total_roll" align="right"><? echo number_format($to_roll,0); ?></th>
            <th width="96"></th>
        </tfoot>
    </table>

    </div>
    <table width="1985" class="rpt_table" id="tbl_foot" cellpadding="0" cellspacing="1" rules="all">
    	<tr>
        	<td colspan="20" height="30" valign="middle" align="center" class="button_container">
				<?
                	echo load_submit_buttons( $permission, "fnc_prod_delivery",0,0 ,"",1) ;
                ?>

            </td>
        </tr>
		<tr>
		<td id="button_data_panel" align="center"> </td>
		</tr>
    </table>
    </form>
</div>
<script>
	// load.print_button_setting()
	window.onload = print_button_setting();
</script>
<!--<script src="../includes/functions_bottom.js" type="text/javascript"></script>-->
	<?
foreach (glob("$user_id*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename####$update_mst_id";

	exit();
}




if($action=="delevery_search")
{

	echo load_html_head_contents("Export Information Entry Form", "../../", 1, 1,'','1','');
	extract($_REQUEST);
	?>

	<script>

		function js_set_value(data)
		{
			$('#hidden_tbl_id').val(data);
			parent.emailwindow.hide();
		}

    </script>

</head>

<body>
<div align="center" style="width:955px;">
	<form name="searchexportinformationfrm"  id="searchexportinformationfrm">
		<fieldset style="width:950px;">
		<legend>Enter search words</legend>
            <table cellpadding="0" cellspacing="0" width="950" class="rpt_table" border="1" rules="all" align="center">
                <thead>
                    <th width="100" class="must_entry_caption">Company</th>
                    <th  width="100">Location</th>
                    <th width="100">Buyer</th>
                    <th width="90">Prog. No</th>
                    <th width="90">Order No</th>
                    <th width="90">Deli. Date from</th>
                    <th width="90">Deli. Date To</th>
                    <th width="100">System Id</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:70px" class="formbutton" />
                    </th>
                </thead>
                <tr class="general">
                    <td>
                        <?
                        	echo create_drop_down( "cbo_company_id", 100, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "--Select Company--", str_replace("'","",$company) , "load_drop_down( 'grey_feb_delivery_entry_controller', this.value, 'load_drop_down_location', 'location_td' );load_drop_down( 'grey_feb_delivery_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                        ?>
                    </td>
                    <td id="location_td">
                            <?
								$blank_array="select id,location_name from lib_location where company_id='".str_replace("'","",$company)."' and status_active =1 and is_deleted=0 order by location_name";
                                echo create_drop_down( "cbo_location_id",100,$blank_array,"id,location_name", 1, "--Select Location--", $selected, "","","","","","",2);
                            ?>
                        </td>
                        <td id="buyer_td">
                            <?
								$blank_array="select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='".str_replace("'","",$company)."' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name";
                                echo create_drop_down( "cbo_buyer_id",100,$blank_array,"id,buyer_name", 1, "--Select Buyer--", $selected, "","","","","","",2);
                            ?>
                        </td>
                        <td>
                        	<input type="text" name="txt_prog_no" id="txt_prog_no" class="text_boxes" style="width:80px;"/>
                        </td>
                        <td>
                        	<input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:80px;"/>
                        </td>
                        <td>
                        	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px;" placeholder="From Date" readonly/>
                        </td>
                        <td>
                        	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px;" placeholder="To Date" readonly/>
                        </td>
                        <td>
                        	<input type="text" name="txt_sys_id" id="txt_sys_id" class="text_boxes" style="width:95px;"/>
                        </td>
                     <td>
                        <input type="button" id="search_button" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_location_id').value+'**'+document.getElementById('cbo_buyer_id').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('txt_sys_id').value+'**'+document.getElementById('txt_prog_no').value+'**'+document.getElementById('txt_order_no').value, 'delivery_search_list_view', 'search_div', 'grey_feb_delivery_entry_controller', 'setFilterGrid(\'tbl_body\',-1)')" style="width:70px;" />
                     </td>
                </tr>
                <tr>
                	<td colspan="9" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
                </tr>
           </table>
           <input type="hidden" id="hidden_tbl_id">
            <div style="width:100%; margin-top:10px" id="search_div" align="left"></div>
		</fieldset>
	</form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
$("#cbo_location_id").val(0);
$("#cbo_buyer_id").val(0);
</script>
</html>
<?
	exit();

}

if ($action=="delivery_search_list_view")
{
	$data=explode("**",$data);
	$cbo_company_name=str_replace("'","",$data[0]);
	$cbo_location_name=str_replace("'","",$data[1]);
	$cbo_buyer_name=str_replace("'","",$data[2]);
	$txt_date_from=str_replace("'","",$data[3]);
	$txt_date_to=str_replace("'","",$data[4]);
	$sys_id=str_replace("'","",$data[5]);
	$prog_no=str_replace("'","",$data[6]);
	$order_no=str_replace("'","",$data[7]);
	//echo $txt_date_from."**".$txt_date_to;die;

	$order_no_arr=return_library_array( "select id, po_number from  wo_po_break_down",'id','po_number');

	if($order_no!="")
	{
		if($db_type==0)
		{
			$po_id=return_field_value("group_concat(id) as po_id","wo_po_break_down","po_number like '%$order_no' and status_active=1","po_id");
		}
		else if($db_type==2)
		{
			$po_id=return_field_value("LISTAGG(CAST( id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY id) as po_id","wo_po_break_down","po_number like '%$order_no' and status_active=1","po_id");
		}

	}
	if($prog_no!="") $program_cond="and b.program_no='$prog_no'"; else $program_cond="";
	if($po_id!="") $order_cond="and b.order_id in($po_id)"; else $order_cond="";
	if($cbo_company_name!=0) {$cbo_company_name="and a.company_id='$cbo_company_name'";} else {echo "Please select the company";die;}
	if($cbo_location_name!=0) $cbo_location_name="and a.location_id='$cbo_location_name'"; else $cbo_location_name="";
	if($cbo_buyer_name !=0) $cbo_buyer_cond="and a.buyer_id='$cbo_buyer_name'"; else $cbo_buyer_cond="";
	if($db_type==0)
	{
		if($txt_date_from!="" && $txt_date_to !="") $date_condition="and a.delevery_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."'"; else $date_condition="";
	}
	//if($txt_date_from!="" || $txt_date_to!="") $sql_cond .= " and wo_date  between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
	else if($db_type==2)
	{
		if($txt_date_from!="" && $txt_date_to !="") $date_condition="and a.delevery_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'"; else $date_condition="";
	}
	//echo $date_condition;die;
	$company_arr=return_library_array( "select id, company_name from  lib_company",'id','company_name');

	//LISTAGG(CAST( b.grey_sys_id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.grey_sys_id) as grey_sys_id
	if($sys_id!="") $sys_cond="and a.sys_number_prefix_num like '$sys_id'"; else $sys_cond="";
	if($db_type==2)
	{
		$sql="select a.id,a.sys_number_prefix_num,$mrr_date_check as sys_year,a.sys_number,a.delevery_date,a.company_id,a.location_id,LISTAGG(CAST( b.program_no  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.program_no) as prog_no,LISTAGG(CAST( b.order_id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.order_id) as order_id,sum(b.current_delivery) as current_delivery
			from  pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b
			where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=53 $cbo_company_name $cbo_location_name $date_condition $sys_cond $program_cond $order_cond $cbo_buyer_cond
			group by  a.id,a.sys_number,a.delevery_date,a.company_id,a.location_id,a.sys_number_prefix_num,a.insert_date order by a.sys_number_prefix_num";
	}
	else if($db_type==0)
	{
		$sql="select a.id,a.sys_number_prefix_num,$mrr_date_check as sys_year,a.sys_number,a.delevery_date,a.company_id,a.location_id,group_concat(distinct b.program_no) as prog_no,group_concat(distinct b.order_id) as order_id,sum(b.current_delivery) as current_delivery
			from  pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b
			where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=53 $cbo_company_name $cbo_location_name $date_condition $sys_cond $program_cond $order_cond $cbo_buyer_cond
			group by  a.id,a.sys_number,a.delevery_date,a.company_id,a.location_id,a.sys_number_prefix_num,a.insert_date order by a.sys_number_prefix_num";
	}
	$location_arr=return_library_array( "select id, location_name from  lib_location",'id','location_name');
	//echo $sql;
	$sql_result=sql_select($sql);
	/*$arr=array(2=>$company_arr,3=>$location_arr,5=>$order_no_arr);
	echo  create_list_view("list_view", "Year,Delivery Sys.Num,Company Name,Location Name,Program No, Order No,Delivery Date,Delivery Qty","100,80,150,140,100,100,110","950","260",0, $sql , "js_set_value", "id,company_id,location_id,sys_number", "", 1, "0,0,company_id,location_id,0,order_id,0,0", $arr, "sys_year,sys_number_prefix_num,company_id,location_id,prog_no,order_id,delevery_date,current_delivery", "",'','0,0,0,0,0,0,3,2') ;	*/
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="950" class="rpt_table">
    	<thead>
        	<tr>
            	<th width="30">SL</th>
                <th width="70">Year</th>
                <th width="70">Delivery Sys.Num</th>
                <th width="110">Company Name</th>
                <th width="110">Location Name</th>
                <th width="150">Program No</th>
                <th width="250">Order No</th>
                <th width="70">Delivery Date</th>
                <th>Delivery Qty</th>
            </tr>
        </thead>
    </table>
    <div id="scroll_body">
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="950" class="rpt_table" id="tbl_body">
        <tbody>
        <?
		$i=1;
		foreach($sql_result as $row)
		{
			if ($i%2==0)
			$bgcolor="#E9F3FF";
			else
			$bgcolor="#FFFFFF";
			$progr_no=implode(",",array_unique(explode(",",$row[csf("prog_no")])));
			$po_no_arr=array_unique(explode(",",$row[csf("order_id")]));
			?>
        	<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $row[csf("id")] ?>_<? echo $row[csf("company_id")] ?>_<? echo $row[csf("location_id")] ?>_<? echo $row[csf("sys_number")] ?>_<? echo change_date_format($row[csf("delevery_date")]) ?>')" style="cursor:pointer;">
            	<td width="30"><? echo $i; ?></td>
                <td align="center" width="70"><p><? echo $row[csf("sys_year")]; ?></p></td>
                <td width="70"><p><? echo $row[csf("sys_number_prefix_num")]; ?></p></td>
                <td width="110"><p><? echo $company_arr[$row[csf("company_id")]]; ?></p></td>
                <td width="110"><p><? echo $location_arr[$row[csf("location_id")]]; ?></p></td>
                <td width="150"><p><? echo $progr_no; ?></p></td>
                <td width="250">
				<p><?
				$po_group="";
				foreach($po_no_arr as $po)
				{
					if($po_group=="") $po_group=$order_no_arr[$po]; else $po_group=$po_group.",".$order_no_arr[$po];
				}
				echo $po_group;
				?></p></td>
                <td width="70"><p><? if($row[csf("delevery_date")]!='0000-00-00' || $row[csf("delevery_date")]!="") echo change_date_format($row[csf("delevery_date")]);//echo $i; ?></p></td>
                <td align="right"><p><? echo number_format($row[csf("current_delivery")],2); ?></p></td>
            </tr>
            <?
			$i++;
		}
		?>
        </tbody>
    </table>
    </div>
    <?
}


if($action=="populate_master_from_data")
{
	if($db_type==2)
	{
		$sql=sql_select("select a.id,a.delevery_date,a.company_id,a.order_status,a.location_id,a.buyer_id,LISTAGG(CAST( b.grey_sys_id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.grey_sys_id) as grey_sys_id,LISTAGG(CAST( b.program_no  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.program_no) as program_no,LISTAGG(CAST( b.order_id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.order_id) as order_id,LISTAGG(CAST( b.product_id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.product_id) as product_id from  pro_grey_prod_delivery_mst a,  pro_grey_prod_delivery_dtls b where a.id=b.mst_id and a.id=$data group by a.id,a.delevery_date,a.company_id,a.order_status,a.location_id,a.buyer_id");
	}
	else if($db_type==0)
	{
		$sql=sql_select("select a.id,a.delevery_date,a.order_status,a.company_id,a.location_id,a.buyer_id,group_concat(distinct b.grey_sys_id  ) as grey_sys_id,group_concat(distinct b.program_no ) as program_no,group_concat(distinct b.order_id ) as order_id,group_concat(distinct b.product_id ) as product_id from  pro_grey_prod_delivery_mst a,  pro_grey_prod_delivery_dtls b where a.id=b.mst_id and a.id=$data group by a.id");
	}
	foreach($sql as $row)
	{
		$receive_id=implode(",",array_unique(explode(",",$row[csf("grey_sys_id")])));
		$progrum_id=implode(",",array_unique(explode(",",$row[csf("program_no")])));
		$order_id=implode(",",array_unique(explode(",",$row[csf("order_id")])));
		$product_detail_id=implode(",",array_unique(explode(",",$row[csf("product_id")])));

		echo "document.getElementById('txt_delevery_date').value 			= '".change_date_format($row[csf("delevery_date")])."';\n";
		echo "document.getElementById('cbo_company_id').value 				= ".$row[csf("company_id")].";\n";
		echo "document.getElementById('cbo_location_id').value 				= ".$row[csf("location_id")].";\n";
		echo "document.getElementById('cbo_buyer_id').value 				= ".$row[csf("buyer_id")].";\n";
		echo "document.getElementById('hidden_receive_id').value 			= '".$receive_id."';\n";
		echo "document.getElementById('hidden_product_id').value 			= '".$product_detail_id."';\n";
		echo "document.getElementById('hidden_order_id').value 				= '".$order_id."';\n";
		echo "document.getElementById('cbo_order_type').value 				= ".$row[csf("order_status")].";\n";
		echo "document.getElementById('update_mst_id').value 				= '".$row[csf("id")]."';\n";

	}
}


if($action=="delivery_challan_print") // Print
{
	extract($_REQUEST);

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$datas=explode('_',$data);
	$program_ids = str_replace("'","",$datas[0]);
	$company = str_replace("'","",$datas[1]);
	$from_date = str_replace("'","",$datas[2]);
	$to_date = str_replace("'","",$datas[3]);
	$product_ids = str_replace("'","",$datas[4]);
	$order_ids = str_replace("'","",$datas[5]);
	$location= str_replace("'","",$datas[6]);
	$buyer = str_replace("'","",$datas[7]);
	$update_mst_id = str_replace("'","",$datas[8]);
	$delivery_date = str_replace("'","",$datas[9]);
	$Challan_no = str_replace("'","",$datas[10]);
	$cbo_order_type = str_replace("'","",$datas[11]);
	$report_title = str_replace("'","",$datas[12]);
	//echo $cbo_order_type;die;
	$company_details=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$yarn_count_details=return_library_array( "select id,yarn_count from lib_yarn_count", "id", "yarn_count"  );
	$buyer_array = return_library_array("select id, short_name from  lib_buyer","id","short_name");
	$brand_details=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name"  );
	$supplier_arr=return_library_array( "select id, supplier_name from   lib_supplier",'id','supplier_name');
	$machine_details=array();
	$machine_data=sql_select("select id, machine_no, dia_width from lib_machine_name");
	foreach($machine_data as $row)
	{
		$machine_details[$row[csf('id')]]['no']=$row[csf('machine_no')];
		$machine_details[$row[csf('id')]]['dia']=$row[csf('dia_width')];
	}

	/*$po_array=array();
	//echo "select a.job_no, a.job_no_prefix_num, a.style_ref_no, b.id, b.po_number as po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company group by b.po_number";
	if($cbo_order_type==1)
	{
		$po_data=sql_select("select a.job_no, a.job_no_prefix_num, a.style_ref_no, b.id, b.po_number as po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company group by  b.id, b.po_number,a.job_no, a.job_no_prefix_num, a.style_ref_no");
		foreach($po_data as $row)
		{
			$job_year=explode("-",$row[csf('job_no')]);
			$po_array[$row[csf('id')]]['no']=$row[csf('po_number')];
			$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
			$po_array[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['job_no_prifix']=$job_year[1]."-".$job_year[2];
			$po_array[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
		}
	}*/

	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	// echo "<pre>";print_r($data_array);die;
	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
		}
	}

	//echo "select id,grey_sys_id,product_id,job_no,order_id,current_delivery,roll from pro_grey_prod_delivery_dtls where mst_id=$update_mst_id";
	$update_row_check=array();
	if($update_mst_id!="")
	{
		$sql_update=sql_select("select id,grey_sys_id,product_id,job_no,order_id,current_delivery,roll,remarks,current_reject from pro_grey_prod_delivery_dtls where mst_id=$update_mst_id");
		foreach($sql_update as $row)
		{
			if($cbo_order_type==1)
			{
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("order_id")]]["current_delivery"] +=$row[csf("current_delivery")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("order_id")]]["current_reject"] +=$row[csf("current_reject")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("order_id")]]["id"] =$row[csf("id")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("order_id")]]["roll"] =$row[csf("roll")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("order_id")]]["remarks"] =$row[csf("remarks")];

			}
			else
			{
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]]["current_delivery"] +=$row[csf("current_delivery")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]]["current_reject"] +=$row[csf("current_reject")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]]["id"] =$row[csf("id")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]]["roll"] =$row[csf("roll")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]]["remarks"] =$row[csf("remarks")];
			}
		}
	}

	//var_dump($update_row_check);
	if($cbo_order_type==1)
	{
		$table_width=2000;
		$col_span=25;
	}
	else
	{
		$table_width=1770;
		$col_span=23;
	}

	?>
	<div style="width:<? echo $table_width;?>px;">
		<table width="<? echo $table_width;?>" cellspacing="0" align="center" border="0">
			<tr>
				<td colspan="1" rowspan="3" id="barcode_img_id"></td>
                <td colspan="<? echo $col_span-1;?>" align="center" style="font-size:x-large"><strong><? echo $company_details[$company]; ?></strong></td>
			</tr>
			<tr>
				<td colspan="<? echo $col_span-1;?>" align="center" style="font-size:18px"><strong><u><? echo $report_title; ?></u></strong></td>
			</tr>
        </table>
        <br>
		<table width="<? echo $table_width;?>" cellspacing="0" align="center" border="0">
			<tr>
				<td   style="font-size:16px; font-weight:bold;" width="110">Challan No</td>
                <td colspan="<? echo $col_span-1;?>"  >:&nbsp;<? echo $Challan_no; ?></td>
			</tr>
            <tr>
				<td style="font-size:16px; font-weight:bold;" width="110">Delivery Date </td>
                <td colspan="<? echo $col_span-1;?>" >:&nbsp;<? echo $delivery_date; ?></td>
			</tr>
        </table>
    </div>
    <div style="width:<? echo $table_width;?>px">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table_width;?>" class="rpt_table" >
            <thead>
                <tr>
                    <th width="30" >SL</th>
                    <?
                    if($cbo_order_type==1)
                    {
						?>
                        <th width="130" >Order No</th>
                        <th width="80" >Style No</th>
                        <th width="80" >Buyer <br> & Job</th>
                    	<?
					}
					else
					{
						?>
						<th width="60" >Buyer</th>
						<?
					}
					?>
                    <th width="50">System ID</th>
                    <?
                    if($cbo_order_type==1)
                    {
						?>
                    	<th width="100" >Prog. /Booking ID</th>
                        <?

					}
					else
					{
						?>
                        <th width="100" >Booking ID</th>
                        <?
					}
					?>
					<th width="100" >Challan No</th>
					<th width="100" >Booking No</th>
                    <th width="100" >Production Basis</th>
                    <th width="70" >Knitting Source</th>
                    <th width="90" >Knitting Company</th>
                    <th width="50" >Yarn Issue Challan No</th>
                    <th width="50" >Yarn Count</th>
                    <th width="90" >Yarn Brand</th>
                    <th width="60" >Lot No</th>
                    <th width="70" >Fab Color</th>
                    <th width="80" >Color Range</th>
                    <th width="240">Fabric Type</th>
                    <th width="50" >Stich</th>
                    <th width="60" >Fin GSM</th>
                    <th width="40" >Fab. Dia</th>
                    <th width="40" >M/C Dia</th>
                    <th width="70" >Total Roll</th>
                    <th width="80">Good Qty</th>
                    <th width="80">Reject Qty</th>
                    <th width="100">Remarks</th>
                </tr>
            </thead>
          <tbody>
	<?
		if($db_type==0)
		{
			if($from_date!="" && $to_date!="") $date_con="and a.receive_date between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'"; else $date_con="";
		}
		else if($db_type==2)
		{
			//if($txt_date_from!="" || $txt_date_to!="") $sql_cond .= " and wo_date  between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
			if($from_date!="" && $to_date!="") $date_con="and a.receive_date between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'"; else $date_con="";
		}

		if($location!=0) $location_con="and a.location_id=$location"; else $location_con="";
		if($buyer!=0) $buyer_con="and a.buyer_id=$buyer"; else $buyer_con="";
		if($db_type==0)
		{

			if($cbo_order_type==1)
			{
				$sql="select a.id as receive_id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no,a.booking_without_order, a.buyer_id, a.remarks,a.yarn_issue_challan_no, b.prod_id, b.febric_description_id, group_concat(b.gsm) as gsm, group_concat(b.width) as width,   group_concat(b.yarn_lot) as yarn_lot, group_concat(b.yarn_count) as yarn_count, max(b.brand_id) as brand_id, min(b.machine_no_id) as machine_no_id,group_concat(b.color_id) as color_id,max(b.color_range_id) as color_range_id, max(b.stitch_length) as  stitch_length, c.po_breakdown_id,c.is_sales, sum(c.quantity)  as outqntyshift, e.job_no, e.job_no_prefix_num, e.style_ref_no, d.po_number as po_number,b.machine_dia, a.challan_no
				from
					inv_receive_master a, pro_grey_prod_entry_dtls b , order_wise_pro_details c, wo_po_break_down  d, wo_po_details_master e
				where
					a.id=b.mst_id  and b.id=c.dtls_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and c.trans_type=1 and c.po_breakdown_id in($order_ids) and a.entry_form=2 and a.status_active=1 and a.is_deleted=0  and a.company_id=$company  and a.id in ($program_ids) and b.prod_id in ($product_ids)  $date_con $location_con $buyer_con
				group by a.id,b.prod_id,c.po_breakdown_id,c.is_sales, d.po_number, e.job_no, e.job_no_prefix_num, e.style_ref_no,b.machine_dia, a.challan_no order by e.style_ref_no";

				$results=sql_select($sql);
				$salesIDs="";
				foreach($results as $row)
				{
					if($row[csf("receive_basis")]==2 && $row[csf("is_sales")]==1)
					{
						$salesIDs.=$row[csf("po_breakdown_id")].",";
					}
				}
				$salesIDs=chop($salesIDs,",");
				$po_info_sql=sql_select("select a.id as sales_id,a.job_no as sales_job,e.job_no,e.style_ref_no,e.job_no_prefix_num from fabric_sales_order_mst a,wo_booking_mst b ,wo_po_break_down d, wo_po_details_master e where a.sales_booking_no=b.booking_no and b.job_no=d.job_no_mst and d.job_no_mst=e.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.id in($salesIDs)");
				foreach($po_info_sql as $row)
				{
					$po_info_arr[$row[csf("sales_id")]]["job_no"]=$row[csf("job_no")];
					$po_info_arr[$row[csf("sales_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
					$po_info_arr[$row[csf("sales_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];

					$salesOrderJob[$row[csf("sales_id")]]["sales_job"]=$row[csf("sales_job")];
				}
			}
			else
			{
				$sql="select a.id as receive_id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no,a.booking_without_order, a.buyer_id, a.remarks,a.yarn_issue_challan_no, b.prod_id, b.febric_description_id, group_concat(b.gsm) as gsm, group_concat(b.width) as width,   group_concat(b.yarn_lot) as yarn_lot, group_concat(b.yarn_count) as yarn_count, max(b.brand_id) as brand_id, max(b.machine_no_id) as machine_no_id, group_concat(b.color_id) as color_id,max(b.color_range_id) as color_range_id, max(b.stitch_length) as  stitch_length , sum(b.grey_receive_qnty)  as outqntyshift,b.machine_dia, a.challan_no
				from
					inv_receive_master a, pro_grey_prod_entry_dtls b
				where
					a.id=b.mst_id and a.entry_form=2 and a.status_active=1 and a.is_deleted=0  and a.company_id=$company and a.booking_without_order=1  and a.id in ($program_ids) and b.prod_id in ($product_ids)  $date_con $location_con $buyer_con
				group by a.id,b.prod_id,b.machine_dia, a.challan_no order by a.id";
			}
		}
		//LISTAGG(CAST( b.id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as po_id
		else
		{

			if($cbo_order_type==1)
			{
				$sql="select a.id as receive_id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no,a.booking_without_order, a.buyer_id, a.remarks,a.yarn_issue_challan_no, b.prod_id, max(b.febric_description_id) as febric_description_id, max(b.gsm) as gsm, max(b.width) as width, LISTAGG(CAST(b.yarn_lot AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY b.yarn_lot )as yarn_lot, LISTAGG(CAST(b.yarn_count AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY b.yarn_count )as yarn_count, max(b.brand_id) as brand_id, max(b.machine_no_id) as machine_no_id, LISTAGG(CAST(b.color_id AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY b.color_id ) as color_id,max(b.color_range_id) as color_range_id,max(b.stitch_length) as stitch_length, c.po_breakdown_id,c.is_sales, sum(c.quantity)  as outqntyshift, e.job_no, e.job_no_prefix_num, e.style_ref_no, d.po_number as po_number,b.machine_dia, a.challan_no, a.inserted_by
				from
					inv_receive_master a, pro_grey_prod_entry_dtls b ,  order_wise_pro_details c, wo_po_break_down  d, wo_po_details_master e
				where
					a.id=b.mst_id and  b.id=c.dtls_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and c.po_breakdown_id in($order_ids)  and c.trans_type=1 and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and a.company_id=$company  and a.id in ($program_ids) and b.prod_id in ($product_ids)  $date_con $location_con $buyer_con
				group by
						 a.id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no,a. 	booking_without_order, a.buyer_id, a.remarks, a.yarn_issue_challan_no, b.prod_id,c.po_breakdown_id,c.is_sales, d.po_number, e.job_no, e.job_no_prefix_num, e.style_ref_no,b.machine_dia,a.challan_no,a.inserted_by
				order by e.style_ref_no";


				$results=sql_select($sql);
				$salesIDs="";
				foreach($results as $row)
				{
					if($row[csf("receive_basis")]==2 && $row[csf("is_sales")]==1)
					{
						$salesIDs.=$row[csf("po_breakdown_id")].",";
					}
				}
				$salesIDs=chop($salesIDs,",");
				$po_info_sql=sql_select("select a.id as sales_id,a.job_no as sales_job,e.job_no,e.style_ref_no,e.job_no_prefix_num from fabric_sales_order_mst a,wo_booking_mst b ,wo_po_break_down d, wo_po_details_master e where a.sales_booking_no=b.booking_no and b.job_no=d.job_no_mst and d.job_no_mst=e.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.id in($salesIDs)");
				foreach($po_info_sql as $row)
				{
					$po_info_arr[$row[csf("sales_id")]]["job_no"]=$row[csf("job_no")];
					$po_info_arr[$row[csf("sales_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
					$po_info_arr[$row[csf("sales_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];

					$salesOrderJob[$row[csf("sales_id")]]["sales_job"]=$row[csf("sales_job")];
				}

			}
			else
			{
				$sql="SELECT a.id as receive_id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no,a.booking_without_order, a.buyer_id, a.remarks,a.yarn_issue_challan_no, b.prod_id, max(b.febric_description_id) as febric_description_id, max(b.gsm) as gsm, max(b.width) as width, LISTAGG(CAST(b.yarn_lot AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY b.yarn_lot )as yarn_lot, LISTAGG(CAST(b.yarn_count AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY b.yarn_count )as yarn_count, max(b.brand_id) as brand_id, max(b.machine_no_id) as machine_no_id, LISTAGG(CAST(b.color_id AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY b.color_id ) as color_id, max(b.color_range_id) as color_range_id,max(b.stitch_length) as stitch_length, sum(b.grey_receive_qnty)  as outqntyshift,b.machine_dia, a.challan_no,a.inserted_by
				from
					inv_receive_master a, pro_grey_prod_entry_dtls b
				where
					a.id=b.mst_id and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and a.company_id=$company and a.booking_without_order=1 and a.id in ($program_ids) and b.prod_id in ($product_ids)  $date_con $location_con $buyer_con
				group by
						 a.id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no,a. 	booking_without_order, a.buyer_id, a.remarks, a.yarn_issue_challan_no, b.prod_id,b.machine_dia, a.challan_no, a.inserted_by
				order by a.id";
			}

		}
	// echo $sql;
	// and dtls_id in (89,90,497) and is_sales!=1
	$sql_plan = "select mst_id, booking_no, dtls_id as prog_no
 	from ppl_planning_entry_plan_dtls where status_active=1 and is_deleted=0  group by dtls_id, mst_id, booking_no";

 	$res_plan = sql_select($sql_plan);
	foreach ($res_plan as $rowPlan) {
		$progrum[$rowPlan[csf('prog_no')]] = $rowPlan[csf('booking_no')];
	}

	$nameArray=sql_select( $sql); $i=1; $tot_roll=0; $tot_qty=0;$style_check=array();$k=1;
	$inserted_by=$nameArray[0]['INSERTED_BY'];
	foreach($nameArray as $row)
	{
		if ($i%2==0)
			$bgcolor="#E9F3FF";
		else
			$bgcolor="#FFFFFF";


		$color_arr[$row[csf('color_id')]];
		$color_all="";
		$color_id_arr=array_unique(explode(",",$row[csf('color_id')]));
		foreach($color_id_arr as $color_id)
		{
			if($color_arr[$color_id]!="")
			{
				$color_all.=$color_arr[$color_id]. ", ";
			}
		}
		$color_all=chop($color_all," , ");


		$count='';
		$yarn_count=explode(",",$row[csf('yarn_count')]);
		foreach($yarn_count as $count_id)
		{
			if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
		}
		$count=implode(",",array_unique(explode(",",$count)));

		if($row[csf("booking_without_order")]==0)
		{
			$index_pk=$row[csf("receive_id")]."*".$row[csf("prod_id")]."*".$row[csf("po_breakdown_id")];
			$tot_delivery=$sql_production_arr[$row[csf("receive_id")]][$row[csf("prod_id")]][$row[csf("po_breakdown_id")]]['prodcut_qty'];
		}
		else
		{
			$index_pk=$row[csf("receive_id")]."*".$row[csf("prod_id")];
			$tot_delivery=$sql_production_arr[$row[csf("receive_id")]][$row[csf("prod_id")]]['prodcut_qty'];
		}
		//echo "<br>";
		//echo $row[csf("receive_id")]."jahid";


		if($row[csf("receive_basis")]==2 && $row[csf("is_sales")]==1)
		{
			$jobNo=$po_info_arr[$row[csf("po_breakdown_id")]]["job_no"];
			$styleRef=$po_info_arr[$row[csf("po_breakdown_id")]]["style_ref_no"];
			$jobNoPrefix=$po_info_arr[$row[csf("po_breakdown_id")]]["job_no_prefix_num"];
			$poNumber=$salesOrderJob[$row[csf("po_breakdown_id")]]["sales_job"];
		}
		else
		{
			$jobNo=$row[csf("job_no")];
			$styleRef=$row[csf("style_ref_no")];
			$jobNoPrefix=$row[csf("job_no_prefix_num")];
			$poNumber=$row[csf("po_number")];
		}



		if($update_row_check[$index_pk]["current_delivery"]>0)
		{
			if(!in_array($row[csf('style_ref_no')],$style_check))
			{
				$style_check[]=$row[csf('style_ref_no')];
				if ($k!=1)
				{
					?>
                    <tr bgcolor="#CCCCCC">
                        <td width="30">&nbsp;</td>
                        <?
                        if($cbo_order_type==1)
                        {
                            ?>
                            <td width="130">&nbsp;</td>
                            <td width="80">&nbsp;</td>
                            <?
                        }
                        ?>
                        <td width="80">&nbsp;</td>
                        <td width="50">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="90">&nbsp;</td>
                        <td width="50">&nbsp;</td>
                        <td width="50">&nbsp;</td>
                        <td width="90">&nbsp;</td>
                        <td width="60">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="80">&nbsp;</td>
                        <td width="240">&nbsp;</td>
                        <td width="50">&nbsp;</td>
                        <td width="40">&nbsp;</td>
                        <td width="60">&nbsp;</td>
                        <td width="40">&nbsp;</td>
                        <td width="40">&nbsp;</td>
                        <td width="70" align="right" style="font-weight:bold;">File Total:</td>
                        <td  align="right" width="80"><div style="word-wrap:break-word; width:80px"><? echo number_format($file_current_delivery,2); ?></div></td>
                        <td  align="right" width="80"><div style="word-wrap:break-word; width:80px"><? echo number_format($file_current_reject,2); ?></div></td>
                        <td  align="right" width="100"><div style="word-wrap:break-word; width:100px"></div></td>
                    </tr>
                    <?
				}
				$k++;$file_current_delivery=$file_current_reject=0;
			}
			?>
			<tr bgcolor="<? echo $bgcolor; ?>">
                <td width="30"><div style="word-wrap:break-word; width:30px"><? echo $i; ?></div></td>
                <?
				if($cbo_order_type==1)
				{
					?>
                	<td width="130"><div style="word-wrap:break-word; width:130px"><? echo $poNumber;?></div></td>
                    <td width="80"><div style="word-wrap:break-word; width:80px"><? echo $styleRef;?></div></td>
                    <?
				}
				?>
                <td width="80"><div style="word-wrap:break-word; width:80px">
				<?

				echo $buyer_array[$row[csf('buyer_id')]]; echo "<br>";

				if($jobNoPrefix!="")
				{
					echo "Job-".$jobNoPrefix;
				}
				?></div></td>
                <td width="50" align="center"><div style="word-wrap:break-word; width:50px"><? echo $row[csf('recv_number_prefix_num')]; ?></div></td>
                <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[csf('booking_no')]; ?></div></td>
                <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[csf('challan_no')]; ?></div></td>
                <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $progrum[$row[csf('booking_no')]]; ?> </div></td>
                <td width="100"><div style="word-wrap:break-word; width:100px">
				<?
				if($row[csf('receive_basis')]==2)
				{
					echo "Knitting Plan";
				}
				else if($row[csf('receive_basis')]==1)
				{
					if($row[csf('booking_without_order')]==1)
					{
						echo "Without Order";
					}
					else
					{
						echo "With Order";
					}
				}
				else
				{
					echo "Independent";
				}
				?>
                </div></td>
                <td width="70"><div style="word-wrap:break-word; width:70px"><? if($row[csf("knitting_source")]==1)  echo "In-House"; else if($row[csf("knitting_source")]==3) echo "Sub-Contract";  ?></div></td>
                <td width="90"><div style="word-wrap:break-word; width:90px">
				<?
					if($row[csf("knitting_source")]==1)  echo $company_details[$row[csf("knitting_company")]]; //$company_arr
					else if($row[csf("knitting_source")]==3) echo $supplier_arr[$row[csf("knitting_company")]];
				?>
                </div></td>
                <td width="50" align="center"><div style="word-wrap:break-word; width:50px"><? echo $row[csf("yarn_issue_challan_no")]; ?></div></td>
                <td width="50"><div style="word-wrap:break-word; width:50px"><? echo $count; ?></div></td>
                <td width="90"><div style="word-wrap:break-word; width:90px"><? echo $brand_details[$row[csf('brand_id')]];?></div></td>
                <td width="60"><div style="word-wrap:break-word; width:60px"><? echo implode(",",array_unique(explode(",",$row[csf('yarn_lot')]))); ?></div></td>
                <td width="70"><div style="word-wrap:break-word; width:70px"><? echo $color_all; ?></div></td>
                <td width="80"><div style="word-wrap:break-word; width:80px"><? echo $color_range[$row[csf('color_range_id')]]; ?></div></td>
                <td width="240"><div style="word-wrap:break-word; width:240px"><? echo $composition_arr[$row[csf('febric_description_id')]]; ?></div></td>
                <td width="50"><div style="word-wrap:break-word; width:50px"><? echo $row[csf("stitch_length")]; ?></div></td>
                <td width="60"><div style="word-wrap:break-word; width:60px"><?  echo implode(",",array_unique(explode(",",$row[csf('gsm')]))); ?></div></td>
                <td width="40"><div style="word-wrap:break-word; width:40px"><? echo implode(",",array_unique(explode(",",$row[csf('width')])));?></div></td>
                <td width="40"><div style="word-wrap:break-word; width:40px"><? echo $row[csf('machine_dia')];//$machine_details[$row[csf('machine_no_id')]]['dia']; ?></div></td>
                <td width="70" align="right"><div style="word-wrap:break-word; width:70px"><? echo number_format($update_row_check[$index_pk]["roll"],2); $tot_roll+=$update_row_check[$index_pk]["roll"]; ?></div></td>
                <td  align="right" width="80"><div style="word-wrap:break-word; width:80px"><? echo number_format($update_row_check[$index_pk]["current_delivery"],2); $tot_qty+=$update_row_check[$index_pk]["current_delivery"]; ?></div></td>
                <td  align="right" width="80"><div style="word-wrap:break-word; width:80px"><? echo number_format($update_row_check[$index_pk]["current_reject"],2); $tot_reject_qty+=$update_row_check[$index_pk]["current_reject"]; ?></div></td>
                 <td width="100" align="left"><div style="word-wrap:break-word; width:100px"><? echo $update_row_check[$index_pk]["remarks"]; ?></div></td>
			</tr>
			<?
			$file_current_delivery+=$update_row_check[$index_pk]["current_delivery"];
			$file_current_reject+=$update_row_check[$index_pk]["current_reject"];
			$i++;
		}
	}
	?>
    		<tr bgcolor="#CCCCCC">
                <td width="30">&nbsp;</td>
                <?
                if($cbo_order_type==1)
                {
                    ?>
                    <td width="130">&nbsp;</td>
                    <td width="80">&nbsp;</td>
                    <?
                }
                ?>
                <td width="80">&nbsp;</td>
                <td width="50">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="90">&nbsp;</td>
                <td width="50">&nbsp;</td>
                <td width="50">&nbsp;</td>
                <td width="90">&nbsp;</td>
                <td width="60">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="240">&nbsp;</td>
                <td width="50">&nbsp;</td>
                <td width="60">&nbsp;</td>
                <td width="40">&nbsp;</td>
                <td width="40">&nbsp;</td>
                <td width="40">&nbsp;</td>
                <td width="70" align="right" style="font-weight:bold;">File Total:</td>
                <td  align="right" width="80"><div style="word-wrap:break-word; width:80px"><? echo number_format($file_current_delivery,2); ?></div></td>
                <td  align="right" width="80"><div style="word-wrap:break-word; width:80px"><? echo number_format($file_current_reject,2); ?></div></td>
                <td  align="right" width="100"><div style="word-wrap:break-word; width:100px"></div></td>
            </tr>


        	<tr>
                <td align="right" colspan="<? echo $col_span-3;?>" ><strong>Total:</strong></td>
                <td align="right"><? echo number_format($tot_roll,2,'.',''); ?></td>
                <td align="right" ><? echo number_format($tot_qty,2,'.',''); ?></td>
                <td align="right" ><? echo number_format($tot_reject_qty,2,'.',''); ?></td>
                <td align="right" ></td>
			</tr>
            <tr>
                <td colspan="2" align="left"><b>Remarks: <? //echo number_to_words($format_total_amount,$uom_unit,$uom_gm); ?></b></td>
                <td colspan="<? echo $col_span-2;?>" >&nbsp;</td>
            </tr>
		</table>
        <br>
		 <?
			$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
			echo signature_table(108,$company,$table_width."px",$template_id,10,$user_lib_name[$inserted_by]);
         ?>
		<script type="text/javascript" src="../includes/functions.js"></script>
		<script type="text/javascript" src="../js/jquery.js"></script>
        <script type="text/javascript" src="../js/jquerybarcode.js"></script>
		<script>
            fnc_generate_Barcode('<? echo $Challan_no;?>','barcode_img_id');
        </script>
	</div>
	<?
    exit();
}

if($action=="delivery_challan_print_2") // Print(Short)
{
	extract($_REQUEST);

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$datas=explode('_',$data);
	$program_ids = str_replace("'","",$datas[0]);
	$company = str_replace("'","",$datas[1]);
	$from_date = str_replace("'","",$datas[2]);
	$to_date = str_replace("'","",$datas[3]);
	$product_ids = str_replace("'","",$datas[4]);
	$order_ids = str_replace("'","",$datas[5]);
	$location= str_replace("'","",$datas[6]);
	$buyer = str_replace("'","",$datas[7]);
	$update_mst_id = str_replace("'","",$datas[8]);
	$delivery_date = str_replace("'","",$datas[9]);
	$Challan_no = str_replace("'","",$datas[10]);
	$cbo_order_type = str_replace("'","",$datas[11]);
	$report_title = str_replace("'","",$datas[12]);
	//echo $cbo_order_type;die;
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library",'master_tble_id','image_location');
	$company_details=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$yarn_count_details=return_library_array( "select id,yarn_count from lib_yarn_count", "id", "yarn_count"  );
	$buyer_array = return_library_array("select id, short_name from  lib_buyer","id","short_name");
	$brand_details=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name"  );
	$machine_details=array();
	$machine_data=sql_select("select id, machine_no, dia_width from lib_machine_name");
	foreach($machine_data as $row)
	{
		$machine_details[$row[csf('id')]]['no']=$row[csf('machine_no')];
		$machine_details[$row[csf('id')]]['dia']=$row[csf('dia_width')];
	}

	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
		}
	}

	$update_row_check=array();
	if($update_mst_id!="")
	{
		$sql_update=sql_select("select id,grey_sys_id,product_id,job_no,order_id,current_delivery,roll,remarks,current_reject from pro_grey_prod_delivery_dtls where mst_id=$update_mst_id");
		foreach($sql_update as $row)
		{
			if($cbo_order_type==1)
			{
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("order_id")]]["current_delivery"] +=$row[csf("current_delivery")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("order_id")]]["current_reject"] +=$row[csf("current_reject")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("order_id")]]["id"] =$row[csf("id")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("order_id")]]["roll"] =$row[csf("roll")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("order_id")]]["remarks"] =$row[csf("remarks")];

			}
			else
			{
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]]["current_delivery"] +=$row[csf("current_delivery")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]]["current_reject"] +=$row[csf("current_reject")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]]["id"] =$row[csf("id")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]]["roll"] =$row[csf("roll")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]]["remarks"] =$row[csf("remarks")];
			}
		}
	}
	//var_dump($update_row_check);
	if($cbo_order_type==1)
	{
		$table_width=1100;
		$col_span=16;
	}
	else
	{
		$table_width=1100;
		$col_span=15;
	}

	?>
	<div style="width:<? echo $table_width;?>px;">
		<table width="<? echo $table_width;?>" cellspacing="0" align="center" border="0">
			<tr>
				<td rowspan="3"><img  src='../<? echo $imge_arr[str_replace("'","",$company)]; ?>' height='70' /></td>
                <td colspan="<? echo $col_span-2;?>" align="center" style="font-size:x-large">
                <strong><?
				echo $company_details[$company].'<br>';

				$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, contact_no, country_id, province, city, zip_code, contact_no, email, website, vat_number from lib_company where id=$company and status_active=1 and is_deleted=0");
				foreach ($nameArray as $result)
				{
				?>
					<? echo $result[csf('plot_no')]; ?> &nbsp;
					<? echo $result[csf('level_no')]; ?> &nbsp;
					<? echo $result[csf('road_no')]; ?> &nbsp;
					<? echo $result[csf('block_no')];?> &nbsp;
					<? echo $result[csf('city')]; ?> &nbsp;
					<? echo $result[csf('contact_no')]; ?> &nbsp;
					<? echo $result[csf('email')]; ?> &nbsp;
					<? echo $result[csf('website')];?> <br>
				   <b> Vat No : <? echo $result[csf('vat_number')]; ?></b> <?
				}

				?></strong>
                </td>
                <td rowspan="3" id="barcode_img_id"></td>
			</tr>
			<tr>
				<td colspan="<? echo $col_span-2;?>" align="center" style="font-size:18px"><strong><u><? echo $report_title; ?></u></strong></td>
			</tr>
        </table>
        <br>
		<table cellspacing="0" border="0">
			<tr>
				<td style="font-size:16px; font-weight:bold;" width="110">Challan No</td>
                <td width="180">:&nbsp;<? echo $Challan_no; ?></td>
                <td width="130">Knitting Source &nbsp;:</td>
                <td id="kniteing_sorce_td"></td>
			</tr>
            <tr>
				<td style="font-size:16px; font-weight:bold;" width="110">Delivery Date </td>
                <td colspan="<? echo $col_span-1;?>" >:&nbsp;<? echo $delivery_date; ?></td>
			</tr>
        </table>
    </div>
    <div style="width:<? echo $table_width;?>px; ">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table_width;?>" class="rpt_table" style="font-size:20px !important;" >
            <thead>
                <tr>
                    <th width="30" >SL</th>
                    <?
                    if($cbo_order_type==1)
                    {
						?>
                        <th width="100" >Order No</th>
                        <!--<th width="80" >Style No</th>-->
                        <th width="80" >Buyer <br> & Job</th>
                    	<?
					}
					else
					{
						?>
						<th width="40" >Buyer</th>
						<?
					}
					?>
                    <th width="40">Prod ID</th>
                    <?
                    if($cbo_order_type==1)
                    {
						?>
                    	<th width="70" >Prog. /Booking ID</th>
                        <?

					}
					else
					{
						?>
                        <th width="100" >Booking ID</th>
                        <?
					}
					?>
                    <th width="50" >Yarn Issue Challan No</th>
                    <th width="50" >Yarn Count</th>
                    <th width="90" >Yarn Brand /Lot No</th>
                    <th width="80" >Fab Color /Color Range</th>
                    <th width="130">Fabric Type</th>
                    <th width="50" >Stich</th>
                    <th width="40" >Fin GSM</th>
                    <th width="40" >Fab. Dia</th>
                    <th width="40" >M/C Dia</th>
                    <th width="60" >Total Roll</th>
                    <th width="60">Del Qty</th>
                    <th width="100">Remarks</th>
                </tr>
            </thead>
          <tbody>
	<?
		if($db_type==0)
		{
			if($from_date!="" && $to_date!="") $date_con="and a.receive_date between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'"; else $date_con="";
		}
		else if($db_type==2)
		{
			//if($txt_date_from!="" || $txt_date_to!="") $sql_cond .= " and wo_date  between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
			if($from_date!="" && $to_date!="") $date_con="and a.receive_date between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'"; else $date_con="";
		}

		if($location!=0) $location_con="and a.location_id=$location"; else $location_con="";
		if($buyer!=0) $buyer_con="and a.buyer_id=$buyer"; else $buyer_con="";
		if($db_type==0)
		{

			if($cbo_order_type==1)
			{
				$sql="select a.id as receive_id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no,a.booking_without_order, a.buyer_id, a.remarks,a.yarn_issue_challan_no, b.prod_id, b.febric_description_id, group_concat(b.gsm) as gsm, group_concat(b.width) as width,   group_concat(b.yarn_lot) as yarn_lot, group_concat(b.yarn_count) as yarn_count, max(b.brand_id) as brand_id, min(b.machine_no_id) as machine_no_id,group_concat(b.color_id) as color_id,max(b.color_range_id) as color_range_id, max(b.stitch_length) as  stitch_length, c.po_breakdown_id,c.is_sales, sum(c.quantity)  as outqntyshift, e.job_no, e.job_no_prefix_num, e.style_ref_no, d.po_number as po_number,b.machine_dia
				from
					inv_receive_master a, pro_grey_prod_entry_dtls b , order_wise_pro_details c, wo_po_break_down  d, wo_po_details_master e
				where
					a.id=b.mst_id  and b.id=c.dtls_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and c.trans_type=1 and c.po_breakdown_id in($order_ids) and a.entry_form=2 and a.status_active=1 and a.is_deleted=0  and a.company_id=$company  and a.id in ($program_ids) and b.prod_id in ($product_ids)  $date_con $location_con $buyer_con
				group by a.id,b.prod_id,c.po_breakdown_id,c.is_sales, d.po_number, e.job_no, e.job_no_prefix_num, e.style_ref_no,b.machine_dia order by e.style_ref_no";

				$results=sql_select($sql);
				$salesIDs="";
				foreach($results as $row)
				{
					if($row[csf("receive_basis")]==2 && $row[csf("is_sales")]==1)
					{
						$salesIDs.=$row[csf("po_breakdown_id")].",";
					}
				}
				$salesIDs=chop($salesIDs,",");
				$po_info_sql=sql_select("select a.id as sales_id,a.job_no as sales_job,e.job_no,e.style_ref_no,e.job_no_prefix_num from fabric_sales_order_mst a,wo_booking_mst b ,wo_po_break_down d, wo_po_details_master e where a.sales_booking_no=b.booking_no and b.job_no=d.job_no_mst and d.job_no_mst=e.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.id in($salesIDs)");
				foreach($po_info_sql as $row)
				{
					$po_info_arr[$row[csf("sales_id")]]["job_no"]=$row[csf("job_no")];
					$po_info_arr[$row[csf("sales_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
					$po_info_arr[$row[csf("sales_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];

					$salesOrderJob[$row[csf("sales_id")]]["sales_job"]=$row[csf("sales_job")];
				}

			}
			else
			{
				$sql="select a.id as receive_id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no,a.booking_without_order, a.buyer_id, a.remarks,a.yarn_issue_challan_no, b.prod_id, b.febric_description_id, group_concat(b.gsm) as gsm, group_concat(b.width) as width,   group_concat(b.yarn_lot) as yarn_lot, group_concat(b.yarn_count) as yarn_count, max(b.brand_id) as brand_id, max(b.machine_no_id) as machine_no_id, group_concat(b.color_id) as color_id,max(b.color_range_id) as color_range_id, max(b.stitch_length) as  stitch_length , sum(b.grey_receive_qnty)  as outqntyshift,b.machine_dia
				from
					inv_receive_master a, pro_grey_prod_entry_dtls b
				where
					a.id=b.mst_id and a.entry_form=2 and a.status_active=1 and a.is_deleted=0  and a.company_id=$company and a.booking_without_order=1  and a.id in ($program_ids) and b.prod_id in ($product_ids)  $date_con $location_con $buyer_con
				group by a.id,b.prod_id,b.machine_dia order by a.id";
			}
		}
		//LISTAGG(CAST( b.id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as po_id
		else
		{

			if($cbo_order_type==1)
			{
				$sql="select a.id as receive_id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no,a.booking_without_order, a.buyer_id, a.remarks,a.yarn_issue_challan_no, b.prod_id, max(b.febric_description_id) as febric_description_id, max(b.gsm) as gsm, max(b.width) as width, LISTAGG(CAST(b.yarn_lot AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY b.yarn_lot )as yarn_lot, LISTAGG(CAST(b.yarn_count AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY b.yarn_count )as yarn_count, max(b.brand_id) as brand_id, max(b.machine_no_id) as machine_no_id, LISTAGG(CAST(b.color_id AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY b.color_id ) as color_id,max(b.color_range_id) as color_range_id,max(b.stitch_length) as stitch_length, c.po_breakdown_id,c.is_sales, sum(c.quantity)  as outqntyshift, e.job_no, e.job_no_prefix_num, e.style_ref_no, d.po_number as po_number,b.machine_dia

				from
					inv_receive_master a, pro_grey_prod_entry_dtls b ,  order_wise_pro_details c, wo_po_break_down  d, wo_po_details_master e
				where
					a.id=b.mst_id and  b.id=c.dtls_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and c.po_breakdown_id in($order_ids)  and c.trans_type=1 and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and a.company_id=$company  and a.id in ($program_ids) and b.prod_id in ($product_ids)  $date_con $location_con $buyer_con
				group by
						 a.id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no,a. 	booking_without_order, a.buyer_id, a.remarks, a.yarn_issue_challan_no, b.prod_id,c.po_breakdown_id,c.is_sales, d.po_number, e.job_no, e.job_no_prefix_num, e.style_ref_no,b.machine_dia
				order by e.style_ref_no";

				$results=sql_select($sql);
				$salesIDs="";
				foreach($results as $row)
				{
					if($row[csf("receive_basis")]==2 && $row[csf("is_sales")]==1)
					{
						$salesIDs.=$row[csf("po_breakdown_id")].",";
					}
				}
				$salesIDs=chop($salesIDs,",");
				$po_info_sql=sql_select("select a.id as sales_id,a.job_no as sales_job,e.job_no,e.style_ref_no,e.job_no_prefix_num from fabric_sales_order_mst a,wo_booking_mst b ,wo_po_break_down d, wo_po_details_master e where a.sales_booking_no=b.booking_no and b.job_no=d.job_no_mst and d.job_no_mst=e.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.id in($salesIDs)");
				foreach($po_info_sql as $row)
				{
					$po_info_arr[$row[csf("sales_id")]]["job_no"]=$row[csf("job_no")];
					$po_info_arr[$row[csf("sales_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
					$po_info_arr[$row[csf("sales_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];

					$salesOrderJob[$row[csf("sales_id")]]["sales_job"]=$row[csf("sales_job")];
				}
			}
			else
			{
				$sql="select a.id as receive_id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no,a.booking_without_order, a.buyer_id, a.remarks,a.yarn_issue_challan_no, b.prod_id, max(b.febric_description_id) as febric_description_id, max(b.gsm) as gsm, max(b.width) as width, LISTAGG(CAST(b.yarn_lot AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY b.yarn_lot )as yarn_lot, LISTAGG(CAST(b.yarn_count AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY b.yarn_count )as yarn_count, max(b.brand_id) as brand_id, max(b.machine_no_id) as machine_no_id, LISTAGG(CAST(b.color_id AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY b.color_id ) as color_id, max(b.color_range_id) as color_range_id,max(b.stitch_length) as stitch_length, sum(b.grey_receive_qnty)  as outqntyshift,b.machine_dia
				from
					inv_receive_master a, pro_grey_prod_entry_dtls b
				where
					a.id=b.mst_id and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and a.company_id=$company and a.booking_without_order=1 and a.id in ($program_ids) and b.prod_id in ($product_ids)  $date_con $location_con $buyer_con
				group by
						 a.id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no,a.booking_without_order, a.buyer_id, a.remarks, a.yarn_issue_challan_no, b.prod_id,b.machine_dia
				order by a.id";
			}

		}
	//echo $sql;


	$nameArray=sql_select( $sql); $i=1; $tot_roll=0; $tot_qty=0;
	$knit_sorce_arr=array();
	foreach($nameArray as $row)
	{
		if ($i%2==0)
			//$bgcolor="#E9F3FF";
			$bgcolor="#FFFFFF";
		else
			$bgcolor="#FFFFFF";


		$color_arr[$row[csf('color_id')]];
		$color_all="";
		$color_id_arr=array_unique(explode(",",$row[csf('color_id')]));
		foreach($color_id_arr as $color_id)
		{
			if($color_arr[$color_id]!="")
			{
				$color_all.=$color_arr[$color_id]. ", ";
			}
		}
		$color_all=chop($color_all," , ");


		$count='';
		$yarn_count=explode(",",$row[csf('yarn_count')]);
		foreach($yarn_count as $count_id)
		{
			if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
		}
		$count=implode(",",array_unique(explode(",",$count)));

		if($row[csf("booking_without_order")]==0)
		{
			$index_pk=$row[csf("receive_id")]."*".$row[csf("prod_id")]."*".$row[csf("po_breakdown_id")];
			$tot_delivery=$sql_production_arr[$row[csf("receive_id")]][$row[csf("prod_id")]][$row[csf("po_breakdown_id")]]['prodcut_qty'];
		}
		else
		{
			$index_pk=$row[csf("receive_id")]."*".$row[csf("prod_id")];
			$tot_delivery=$sql_production_arr[$row[csf("receive_id")]][$row[csf("prod_id")]]['prodcut_qty'];
		}
		//echo "<br>";
		//echo $row[csf("receive_id")]."jahid";

		if($row[csf("receive_basis")]==2 && $row[csf("is_sales")]==1)
		{
			$jobNo=$po_info_arr[$row[csf("po_breakdown_id")]]["job_no"];
			$styleRef=$po_info_arr[$row[csf("po_breakdown_id")]]["style_ref_no"];
			$jobNoPrefix=$po_info_arr[$row[csf("po_breakdown_id")]]["job_no_prefix_num"];
			$poNumber=$salesOrderJob[$row[csf("po_breakdown_id")]]["sales_job"];
		}
		else
		{
			$jobNo=$row[csf("job_no")];
			$styleRef=$row[csf("style_ref_no")];
			$jobNoPrefix=$row[csf("job_no_prefix_num")];
			$poNumber=$row[csf("po_number")];
		}


		if($update_row_check[$index_pk]["current_delivery"]>0)
		{
			if($row[csf("knitting_source")]==1){$knit_sorce_arr[$row[csf("knitting_source")]]='In-House';}
			else if($row[csf("knitting_source")]==3){$knit_sorce_arr[$row[csf("knitting_source")]]='Sub-Contract';}

			?>
			<tr bgcolor="<? echo $bgcolor; ?>">
                <td width="30" align="center"><div style="word-wrap:break-word; width:30px"><? echo $i; ?></div></td>
                <?
				if($cbo_order_type==1)
				{
					?>
                	<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $poNumber;?></div></td>
                   <!-- <td width="80"><div style="word-wrap:break-word; width:80px"><? //echo $row[csf('style_ref_no')];?></div></td>-->
                   <td width="80"><div style="word-wrap:break-word; width:80px">
					<?

                    echo $buyer_array[$row[csf('buyer_id')]]; echo "<br>";

                    if($jobNoPrefix!="")
                    {
                        echo "Job-".$jobNoPrefix;
                    }
                    ?></div></td>
                    <?
				}
				else
				{
					?>
					<td width="40"><div style="word-wrap:break-word; width:40px">
					<?

					echo $buyer_array[$row[csf('buyer_id')]];
					?></div></td>
					<?
				}
				?>
                <td width="40" align="center"><div style="word-wrap:break-word; width:40px"><? echo $row[csf('recv_number_prefix_num')]; ?></div></td>
                <td width="70"><div style="word-wrap:break-word; width:70px"><? echo $row[csf('booking_no')]; ?></div></td>
                <td width="50" align="center"><div style="word-wrap:break-word; width:50px"><? echo $row[csf("yarn_issue_challan_no")]; ?></div></td>
                <td width="50"><div style="word-wrap:break-word; width:50px"><? echo $count; ?></div></td>
                <td width="90"><div style="word-wrap:break-word; width:90px">
				<?
					echo $brand_details[$row[csf('brand_id')]].'<hr>';
					echo implode(",",array_unique(explode(",",$row[csf('yarn_lot')])));
				?>
                </div></td>
                <td width="80"><div style="word-wrap:break-word; width:80px">
				<?
					echo $color_all.'<hr>';
					echo $color_range[$row[csf('color_range_id')]];
				?>
                </div></td>
                <td width="130"><div style="word-wrap:break-word; width:130px"><? echo $composition_arr[$row[csf('febric_description_id')]]; ?></div></td>
                <td width="50"><div style="word-wrap:break-word; width:50px"><? echo $row[csf("stitch_length")]; ?></div></td>
                <td width="40"><div style="word-wrap:break-word; width:40px"><?  echo implode(",",array_unique(explode(",",$row[csf('gsm')]))); ?></div></td>
                <td width="40"><div style="word-wrap:break-word; width:40px"><? echo implode(",",array_unique(explode(",",$row[csf('width')])));?></div></td>
                <td width="40"><div style="word-wrap:break-word; width:40px"><? if($cbo_order_type==1){ echo $row[csf('machine_dia')]; }else{echo $machine_details[$row[csf('machine_no_id')]]['dia'];} ?></div></td>
                <td width="60" align="right"><div style="word-wrap:break-word; width:60px"><? echo number_format($update_row_check[$index_pk]["roll"],2); $tot_roll+=$update_row_check[$index_pk]["roll"]; ?></div></td>
                <td  align="right" width="60"><div style="word-wrap:break-word; width:60px"><? echo number_format($update_row_check[$index_pk]["current_delivery"],2); $tot_qty+=$update_row_check[$index_pk]["current_delivery"]; ?></div></td>
                <td width="100" align="left"><div style="word-wrap:break-word; width:100px"><? echo $update_row_check[$index_pk]["remarks"]; ?></div></td>
			</tr>
			<?
			$i++;
		}
	}
	?>
        	<tr>
                <td align="right" colspan="<? echo $col_span-2;?>" ><strong>Total:</strong></td>
                <td align="right"><? echo number_format($tot_roll,2,'.',''); ?>&nbsp;</td>
                <td align="right" ><? echo number_format($tot_qty,2,'.',''); ?>&nbsp;</td>
                <td align="right" >&nbsp;</td>
			</tr>
            <tr>
                <td colspan="2" align="left"><b>Remarks: <? //echo number_to_words($format_total_amount,$uom_unit,$uom_gm); ?></b></td>
                <td colspan="<? echo $col_span-1;?>" >&nbsp;</td>
            </tr>
		</table>
        <br>
		 <?
            echo signature_table(108, $company, $table_width."px");
            // echo signature_table(2, $cbo_company_name, "1330px";
         ?>
		<script type="text/javascript" src="../includes/functions.js"></script>
		<script type="text/javascript" src="../js/jquery.js"></script>
        <script type="text/javascript" src="../js/jquerybarcode.js"></script>
		<script>
            fnc_generate_Barcode('<? echo $Challan_no;?>','barcode_img_id');
        </script>
        <script> document.getElementById('kniteing_sorce_td').innerHTML='<? echo implode(',',$knit_sorce_arr);?>';  </script>
	</div>
	<?
    exit();
}

if($action=="delivery_challan_print_short2") // Print(Short 2)
{
	extract($_REQUEST);

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$datas=explode('_',$data);
	$program_ids = str_replace("'","",$datas[0]);
	$company = str_replace("'","",$datas[1]);
	$from_date = str_replace("'","",$datas[2]);
	$to_date = str_replace("'","",$datas[3]);
	$product_ids = str_replace("'","",$datas[4]);
	$order_ids = str_replace("'","",$datas[5]);
	$location= str_replace("'","",$datas[6]);
	$buyer = str_replace("'","",$datas[7]);
	$update_mst_id = str_replace("'","",$datas[8]);
	$delivery_date = str_replace("'","",$datas[9]);
	$Challan_no = str_replace("'","",$datas[10]);
	$cbo_order_type = str_replace("'","",$datas[11]);
	$report_title = str_replace("'","",$datas[12]);
	//echo $cbo_order_type;die;
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library",'master_tble_id','image_location');
	$company_details=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$yarn_count_details=return_library_array( "select id,yarn_count from lib_yarn_count", "id", "yarn_count"  );
	$buyer_array = return_library_array("select id, short_name from  lib_buyer","id","short_name");
	$brand_details=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name"  );
	$machine_details=array();
	$machine_data=sql_select("select id, machine_no, dia_width from lib_machine_name");
	foreach($machine_data as $row)
	{
		$machine_details[$row[csf('id')]]['no']=$row[csf('machine_no')];
		$machine_details[$row[csf('id')]]['dia']=$row[csf('dia_width')];
	}

	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$data_array=sql_select($sql_deter);
	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
		}
	}

	$update_row_check=array();
	if($update_mst_id!="")
	{
		$sql_update=sql_select("select id,grey_sys_id,product_id,job_no,order_id,current_delivery,roll,remarks,current_reject from pro_grey_prod_delivery_dtls where mst_id=$update_mst_id");
		foreach($sql_update as $row)
		{
			if($cbo_order_type==1)
			{
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("order_id")]]["current_delivery"] +=$row[csf("current_delivery")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("order_id")]]["current_reject"] +=$row[csf("current_reject")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("order_id")]]["id"] =$row[csf("id")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("order_id")]]["roll"] =$row[csf("roll")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("order_id")]]["remarks"] =$row[csf("remarks")];

			}
			else
			{
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]]["current_delivery"] +=$row[csf("current_delivery")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]]["current_reject"] +=$row[csf("current_reject")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]]["id"] =$row[csf("id")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]]["roll"] =$row[csf("roll")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]]["remarks"] =$row[csf("remarks")];
			}
		}
	}
	//var_dump($update_row_check);
	if($cbo_order_type==1)
	{
		$table_width=1240;
		$col_span=17;
	}
	else
	{
		$table_width=1240;
		$col_span=16;
	}

	?>
	<div style="width:<? echo $table_width;?>px;">
		<table width="<? echo $table_width;?>" cellspacing="0" align="center" border="0">
			<tr>
				<td rowspan="3"><img  src='../<? echo $imge_arr[str_replace("'","",$company)]; ?>' height='70' /></td>
                <td colspan="<? echo $col_span-2;?>" align="center" style="font-size:x-large">
                <strong><?
				echo $company_details[$company].'<br>';

				$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, contact_no, country_id, province, city, zip_code, contact_no, email, website, vat_number from lib_company where id=$company and status_active=1 and is_deleted=0");
				foreach ($nameArray as $result)
				{
				?>
					<? echo $result[csf('plot_no')]; ?> &nbsp;
					<? echo $result[csf('level_no')]; ?> &nbsp;
					<? echo $result[csf('road_no')]; ?> &nbsp;
					<? echo $result[csf('block_no')];?> &nbsp;
					<? echo $result[csf('city')]; ?> &nbsp;
					<? echo $result[csf('contact_no')]; ?> &nbsp;
					<? echo $result[csf('email')]; ?> &nbsp;
					<? echo $result[csf('website')];?> <br>
				   <b> Vat No : <? echo $result[csf('vat_number')]; ?></b> <?
				}

				?></strong>
                </td>
                <td rowspan="3" id="barcode_img_id"></td>
			</tr>
			<tr>
				<td colspan="<? echo $col_span-2;?>" align="center" style="font-size:18px"><strong><u><? echo $report_title; ?></u></strong></td>
			</tr>
        </table>
        <br>
		<table cellspacing="0" border="0">
			<tr>
				<td style="font-size:16px; font-weight:bold;" width="110">Challan No</td>
                <td width="180">:&nbsp;<? echo $Challan_no; ?></td>
                <td width="130">Knitting Source &nbsp;:</td>
                <td id="kniteing_sorce_td"></td>
			</tr>
            <tr>
				<td style="font-size:16px; font-weight:bold;" width="110">Delivery Date </td>
                <td colspan="<? echo $col_span-1;?>" >:&nbsp;<? echo $delivery_date; ?></td>
			</tr>
        </table>
    </div>
    <div style="width:<? echo $table_width;?>px; ">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table_width;?>" class="rpt_table" style="font-size:20px !important;" >
            <thead>
                <tr>
                    <th width="30" >SL</th>
                    <?
                    if($cbo_order_type==1)
                    {
						?>
                        <th width="100" >Order No</th>
                        <!--<th width="80" >Style No</th>-->
                        <th width="80" >Buyer <br> & Job</th>
                    	<?
					}
					else
					{
						?>
						<th width="40" >Buyer</th>
						<?
					}
					?>
                    <th width="40">Prod ID</th>
                    <?
                    if($cbo_order_type==1)
                    {
						?>
                    	<th width="70" >Prog. /Booking ID</th>
                        <?

					}
					else
					{
						?>
                        <th width="100" >Booking ID</th>
                        <?
					}
					?>
                    <th width="50" >Yarn Issue Challan No</th>
                    <th width="50" >Yarn Count</th>
                    <th width="90" >Yarn Brand /Lot No</th>
                    <th width="80" >Fab Color /Color Range</th>
                    <th width="100">Body Part</th>
                    <th width="130">Fabric Type</th>
                    <th width="50" >Stich</th>
                    <th width="40" >Fin GSM</th>
                    <th width="40" >Fab. Dia</th>
                    <th width="40" >M/C Dia</th>
                    <th width="60" >Total Roll</th>
                    <th width="60">Del Qty</th>
                    <th width="60">Rate-Bdt</th>
                    <th width="100">Amount</th>
                    <th width="100">Remarks</th>
                </tr>
            </thead>
	        <tbody>
			<?
			if($db_type==0)
			{
				if($from_date!="" && $to_date!="") $date_con="and a.receive_date between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'"; else $date_con="";
			}
			else if($db_type==2)
			{
				//if($txt_date_from!="" || $txt_date_to!="") $sql_cond .= " and wo_date  between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
				if($from_date!="" && $to_date!="") $date_con="and a.receive_date between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'"; else $date_con="";
			}

			if($location!=0) $location_con="and a.location_id=$location"; else $location_con="";
			if($buyer!=0) $buyer_con="and a.buyer_id=$buyer"; else $buyer_con="";
			if($db_type==0)
			{

				if($cbo_order_type==1)
				{
					$sql="select a.id as receive_id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no,a.booking_without_order, a.buyer_id, a.remarks,a.yarn_issue_challan_no, b.prod_id, b.febric_description_id, group_concat(b.gsm) as gsm, group_concat(b.width) as width,   group_concat(b.yarn_lot) as yarn_lot, group_concat(b.yarn_count) as yarn_count, max(b.brand_id) as brand_id, min(b.machine_no_id) as machine_no_id,group_concat(b.color_id) as color_id,max(b.color_range_id) as color_range_id, max(b.stitch_length) as  stitch_length, c.po_breakdown_id,c.is_sales, sum(c.quantity)  as outqntyshift, e.job_no, e.job_no_prefix_num, e.style_ref_no, d.po_number as po_number,b.machine_dia,b.body_part_id
					from
						inv_receive_master a, pro_grey_prod_entry_dtls b , order_wise_pro_details c, wo_po_break_down  d, wo_po_details_master e
					where
						a.id=b.mst_id  and b.id=c.dtls_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and c.trans_type=1 and c.po_breakdown_id in($order_ids) and a.entry_form=2 and a.status_active=1 and a.is_deleted=0  and a.company_id=$company  and a.id in ($program_ids) and b.prod_id in ($product_ids)  $date_con $location_con $buyer_con
					group by a.id,b.prod_id,c.po_breakdown_id,c.is_sales, d.po_number, e.job_no, e.job_no_prefix_num, e.style_ref_no,b.machine_dia order by e.style_ref_no";

					$results=sql_select($sql);
					$salesIDs="";
					foreach($results as $row)
					{
						if($row[csf("receive_basis")]==2 && $row[csf("is_sales")]==1)
						{
							$salesIDs.=$row[csf("po_breakdown_id")].",";
						}
					}
					$salesIDs=chop($salesIDs,",");
					$po_info_sql=sql_select("select a.id as sales_id,a.job_no as sales_job,e.job_no,e.style_ref_no,e.job_no_prefix_num from fabric_sales_order_mst a,wo_booking_mst b ,wo_po_break_down d, wo_po_details_master e where a.sales_booking_no=b.booking_no and b.job_no=d.job_no_mst and d.job_no_mst=e.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.id in($salesIDs)");
					foreach($po_info_sql as $row)
					{
						$po_info_arr[$row[csf("sales_id")]]["job_no"]=$row[csf("job_no")];
						$po_info_arr[$row[csf("sales_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
						$po_info_arr[$row[csf("sales_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];

						$salesOrderJob[$row[csf("sales_id")]]["sales_job"]=$row[csf("sales_job")];
					}

				}
				else
				{
					$sql="select a.id as receive_id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no,a.booking_without_order, a.buyer_id, a.remarks,a.yarn_issue_challan_no, b.prod_id, b.febric_description_id, group_concat(b.gsm) as gsm, group_concat(b.width) as width,   group_concat(b.yarn_lot) as yarn_lot, group_concat(b.yarn_count) as yarn_count, max(b.brand_id) as brand_id, max(b.machine_no_id) as machine_no_id, group_concat(b.color_id) as color_id,max(b.color_range_id) as color_range_id, max(b.stitch_length) as  stitch_length , sum(b.grey_receive_qnty)  as outqntyshift,b.machine_dia,b.body_part_id
					from
						inv_receive_master a, pro_grey_prod_entry_dtls b
					where
						a.id=b.mst_id and a.entry_form=2 and a.status_active=1 and a.is_deleted=0  and a.company_id=$company and a.booking_without_order=1  and a.id in ($program_ids) and b.prod_id in ($product_ids)  $date_con $location_con $buyer_con
					group by a.id,b.prod_id,b.machine_dia order by a.id";
				}
			}
			//LISTAGG(CAST( b.id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as po_id
			else
			{

				if($cbo_order_type==1)
				{
					$sql="select a.id as receive_id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no,a.booking_without_order, a.buyer_id, a.remarks,a.yarn_issue_challan_no, b.prod_id, max(b.febric_description_id) as febric_description_id, max(b.gsm) as gsm, max(b.width) as width, LISTAGG(CAST(b.yarn_lot AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY b.yarn_lot )as yarn_lot, LISTAGG(CAST(b.yarn_count AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY b.yarn_count )as yarn_count, max(b.brand_id) as brand_id, max(b.machine_no_id) as machine_no_id, LISTAGG(CAST(b.color_id AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY b.color_id ) as color_id,max(b.color_range_id) as color_range_id,max(b.stitch_length) as stitch_length, c.po_breakdown_id,c.is_sales, sum(c.quantity)  as outqntyshift, e.job_no, e.job_no_prefix_num, e.style_ref_no, d.po_number as po_number,b.machine_dia,LISTAGG(CAST(b.body_part_id AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY b.body_part_id ) as body_part_id

					from
						inv_receive_master a, pro_grey_prod_entry_dtls b ,  order_wise_pro_details c, wo_po_break_down  d, wo_po_details_master e
					where
						a.id=b.mst_id and  b.id=c.dtls_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and c.po_breakdown_id in($order_ids)  and c.trans_type=1 and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and a.company_id=$company  and a.id in ($program_ids) and b.prod_id in ($product_ids)  $date_con $location_con $buyer_con
					group by
							 a.id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no,a. 	booking_without_order, a.buyer_id, a.remarks, a.yarn_issue_challan_no, b.prod_id,c.po_breakdown_id,c.is_sales, d.po_number, e.job_no, e.job_no_prefix_num, e.style_ref_no,b.machine_dia
					order by e.style_ref_no";

					$results=sql_select($sql);
					$salesIDs="";
					foreach($results as $row)
					{
						if($row[csf("receive_basis")]==2 && $row[csf("is_sales")]==1)
						{
							$salesIDs.=$row[csf("po_breakdown_id")].",";
						}
					}
					$salesIDs=chop($salesIDs,",");
					$po_info_sql=sql_select("select a.id as sales_id,a.job_no as sales_job,e.job_no,e.style_ref_no,e.job_no_prefix_num from fabric_sales_order_mst a,wo_booking_mst b ,wo_po_break_down d, wo_po_details_master e where a.sales_booking_no=b.booking_no and b.job_no=d.job_no_mst and d.job_no_mst=e.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.id in($salesIDs)");
					foreach($po_info_sql as $row)
					{
						$po_info_arr[$row[csf("sales_id")]]["job_no"]=$row[csf("job_no")];
						$po_info_arr[$row[csf("sales_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
						$po_info_arr[$row[csf("sales_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];

						$salesOrderJob[$row[csf("sales_id")]]["sales_job"]=$row[csf("sales_job")];
					}
				}
				else
				{
					$sql="select a.id as receive_id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no,a.booking_without_order, a.buyer_id, a.remarks,a.yarn_issue_challan_no, b.prod_id, max(b.febric_description_id) as febric_description_id, max(b.gsm) as gsm, max(b.width) as width, LISTAGG(CAST(b.yarn_lot AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY b.yarn_lot )as yarn_lot, LISTAGG(CAST(b.yarn_count AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY b.yarn_count )as yarn_count, max(b.brand_id) as brand_id, max(b.machine_no_id) as machine_no_id, LISTAGG(CAST(b.color_id AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY b.color_id ) as color_id, max(b.color_range_id) as color_range_id,max(b.stitch_length) as stitch_length, sum(b.grey_receive_qnty)  as outqntyshift,b.machine_dia,LISTAGG(CAST(b.body_part_id AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY b.body_part_id ) as body_part_id
					from
						inv_receive_master a, pro_grey_prod_entry_dtls b
					where
						a.id=b.mst_id and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and a.company_id=$company and a.booking_without_order=1 and a.id in ($program_ids) and b.prod_id in ($product_ids)  $date_con $location_con $buyer_con
					group by
							 a.id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no,a.booking_without_order, a.buyer_id, a.remarks, a.yarn_issue_challan_no, b.prod_id,b.machine_dia
					order by a.id";
				}
			}

			$nameArray=sql_select( $sql);
			$jobNoArr = array();
			$jobNoChk = array();
			foreach($nameArray as $row)
			{
				if($row[csf("receive_basis")]==2 && $row[csf("is_sales")]==1)
				{
					$jobNo=$po_info_arr[$row[csf("po_breakdown_id")]]["job_no"];
				}
				else
				{
					$jobNo=$row[csf("job_no")];
				}

				if($jobNoChk[$jobNo] == "")
				{
					$jobNoChk[$jobNo] = $jobNo;
					array_push($jobNoArr,$jobNo);
				}

			}
			
			$preCost_sql = "SELECT a.job_no, a.lib_yarn_count_deter_id,b.id, b.charge_unit,b.fabric_description FROM wo_pre_cost_fab_conv_cost_dtls b, wo_pre_cost_fabric_cost_dtls a WHERE  a.id=b.fabric_description and a.status_active = 1 AND a.is_deleted = 0 and b.status_active = 1 AND b.is_deleted = 0 AND b.cons_process = 1 ".where_con_using_array($jobNoArr,1,'a.job_no')."  order by  b.fabric_description";
			
			$preCost_rslt=sql_select($preCost_sql);
			$preCostInfoArrr =array();
			foreach($preCost_rslt as $row)
			{
				$preCostInfoArrr[$row[csf('job_no')]][$row[csf('lib_yarn_count_deter_id')]]['unit_charge']= $row[csf('charge_unit')];
			}
			unset($preCost_rslt);
			//echo "<pre>";print_r($preCostInfoArrr);
			
			$exchange_rate = return_field_value("conversion_rate", "(SELECT conversion_rate FROM currency_conversion_rate where company_id=$company and currency=2 and status_active=1 and is_deleted=0 ORDER BY id DESC)", "ROWNUM = 1");

			 $i=1; $tot_roll=0; $tot_qty=0;
			$knit_sorce_arr=array();
			foreach($nameArray as $row)
			{
				if ($i%2==0)
					//$bgcolor="#E9F3FF";
					$bgcolor="#FFFFFF";
				else
					$bgcolor="#FFFFFF";


				$color_arr[$row[csf('color_id')]];
				$color_all="";
				$color_id_arr=array_unique(explode(",",$row[csf('color_id')]));
				foreach($color_id_arr as $color_id)
				{
					if($color_arr[$color_id]!="")
					{
						$color_all.=$color_arr[$color_id]. ", ";
					}
				}
				$color_all=chop($color_all," , ");


				$count='';
				$yarn_count=explode(",",$row[csf('yarn_count')]);
				foreach($yarn_count as $count_id)
				{
					if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
				}
				$count=implode(",",array_unique(explode(",",$count)));

				$bodyPart='';
				$body_part_ids=explode(",",$row[csf('body_part_id')]);
				foreach($body_part_ids as $body_part_id)
				{
					if($bodyPart=='') $bodyPart=$body_part[$body_part_id]; else $bodyPart.=",".$body_part[$body_part_id];
				}
				$bodyPart=implode(",",array_unique(explode(",",$bodyPart)));

				if($row[csf("booking_without_order")]==0)
				{
					$index_pk=$row[csf("receive_id")]."*".$row[csf("prod_id")]."*".$row[csf("po_breakdown_id")];
					$tot_delivery=$sql_production_arr[$row[csf("receive_id")]][$row[csf("prod_id")]][$row[csf("po_breakdown_id")]]['prodcut_qty'];
				}
				else
				{
					$index_pk=$row[csf("receive_id")]."*".$row[csf("prod_id")];
					$tot_delivery=$sql_production_arr[$row[csf("receive_id")]][$row[csf("prod_id")]]['prodcut_qty'];
				}
				//echo "<br>";
				//echo $row[csf("receive_id")]."jahid";

				if($row[csf("receive_basis")]==2 && $row[csf("is_sales")]==1)
				{
					$jobNo=$po_info_arr[$row[csf("po_breakdown_id")]]["job_no"];
					$styleRef=$po_info_arr[$row[csf("po_breakdown_id")]]["style_ref_no"];
					$jobNoPrefix=$po_info_arr[$row[csf("po_breakdown_id")]]["job_no_prefix_num"];
					$poNumber=$salesOrderJob[$row[csf("po_breakdown_id")]]["sales_job"];
				}
				else
				{
					$jobNo=$row[csf("job_no")];
					$styleRef=$row[csf("style_ref_no")];
					$jobNoPrefix=$row[csf("job_no_prefix_num")];
					$poNumber=$row[csf("po_number")];
				}

				$rate=$preCostInfoArrr[$jobNo][$row[csf('febric_description_id')]]['unit_charge'];

				if($update_row_check[$index_pk]["current_delivery"]>0)
				{
					if($row[csf("knitting_source")]==1){$knit_sorce_arr[$row[csf("knitting_source")]]='In-House';}
					else if($row[csf("knitting_source")]==3){$knit_sorce_arr[$row[csf("knitting_source")]]='Sub-Contract';}

					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
		                <td width="30" align="center"><div style="word-wrap:break-word; width:30px"><? echo $i; ?></div></td>
		                <?
						if($cbo_order_type==1)
						{
							?>
		                	<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $poNumber;?></div></td>
		                   <!-- <td width="80"><div style="word-wrap:break-word; width:80px"><? //echo $row[csf('style_ref_no')];?></div></td>-->
		                   <td width="80" title="<? echo $jobNo;?>"><div style="word-wrap:break-word; width:80px">
							<?

		                    echo $buyer_array[$row[csf('buyer_id')]]; echo "<br>";

		                    if($jobNoPrefix!="")
		                    {
		                        echo "Job-".$jobNoPrefix;
		                    }
		                    ?></div></td>
		                    <?
						}
						else
						{
							?>
							<td width="40"><div style="word-wrap:break-word; width:40px">
							<?

							echo $buyer_array[$row[csf('buyer_id')]];
							?></div></td>
							<?
						}
						?>
		                <td width="40" align="center"><div style="word-wrap:break-word; width:40px"><? echo $row[csf('recv_number_prefix_num')]; ?></div></td>
		                <td width="70"><div style="word-wrap:break-word; width:70px"><? echo $row[csf('booking_no')]; ?></div></td>
		                <td width="50" align="center"><div style="word-wrap:break-word; width:50px"><? echo $row[csf("yarn_issue_challan_no")]; ?></div></td>
		                <td width="50"><div style="word-wrap:break-word; width:50px"><? echo $count; ?></div></td>
		                <td width="90"><div style="word-wrap:break-word; width:90px">
						<?
							echo $brand_details[$row[csf('brand_id')]].'<hr>';
							echo implode(",",array_unique(explode(",",$row[csf('yarn_lot')])));
						?>
		                </div></td>
		                <td width="80"><div style="word-wrap:break-word; width:80px">
						<?
							echo $color_all.'<hr>';
							echo $color_range[$row[csf('color_range_id')]];
						?>
		                </div></td>
						<td width="100"><div style="word-wrap:break-word; width:130px"><? echo $bodyPart; ?></div></td>
		                <td width="130"><div style="word-wrap:break-word; width:130px" title="<? echo $row[csf('febric_description_id')];?>"><? echo $composition_arr[$row[csf('febric_description_id')]]; ?></div></td>
		                <td width="50"><div style="word-wrap:break-word; width:50px"><? echo $row[csf("stitch_length")]; ?></div></td>
		                <td width="40"><div style="word-wrap:break-word; width:40px"><?  echo implode(",",array_unique(explode(",",$row[csf('gsm')]))); ?></div></td>
		                <td width="40"><div style="word-wrap:break-word; width:40px"><? echo implode(",",array_unique(explode(",",$row[csf('width')])));?></div></td>
		                <td width="40"><div style="word-wrap:break-word; width:40px"><? if($cbo_order_type==1){ echo $row[csf('machine_dia')]; }else{echo $machine_details[$row[csf('machine_no_id')]]['dia'];} ?></div></td>
		                <td width="60" align="right"><div style="word-wrap:break-word; width:60px"><? echo number_format($update_row_check[$index_pk]["roll"],2); $tot_roll+=$update_row_check[$index_pk]["roll"]; ?></div></td>
		                <td  align="right" width="60"><div style="word-wrap:break-word; width:60px"><? echo number_format($update_row_check[$index_pk]["current_delivery"],2); $tot_qty+=$update_row_check[$index_pk]["current_delivery"]; ?></div></td>
		                <td  align="right" width="60"><div style="word-wrap:break-word; width:60px"><? echo number_format($rate*$exchange_rate,2);?></div></td>
		                <td  align="right" width="100" style="word-wrap:break-word; width:60px"><? echo number_format($rate*$exchange_rate*$update_row_check[$index_pk]["current_delivery"],2); $tot_amount +=$rate*$exchange_rate*$update_row_check[$index_pk]["current_delivery"];?></td>
		                <td width="100" align="left"><div style="word-wrap:break-word; width:100px"><? echo $update_row_check[$index_pk]["remarks"]; ?></div></td>
					</tr>
					<?
					$i++;
				}
			}
			?>
        	<tr>
                <td align="right" colspan="<? echo $col_span-2;?>" ><strong>Total:</strong></td>
                <td align="right"><? echo number_format($tot_roll,2,'.',''); ?>&nbsp;</td>
                <td align="right" ><? echo number_format($tot_qty,2,'.',''); ?>&nbsp;</td>
                <td align="right" >&nbsp;</td>
                <td align="right" ><? echo number_format($tot_amount,2,'.',''); ?></td>
                <td align="right" >&nbsp;</td>
			</tr>
            <tr>
                <td colspan="2" align="left"><b>Remarks: <? //echo number_to_words($format_total_amount,$uom_unit,$uom_gm); ?></b></td>
                <td colspan="<? echo $col_span+2;?>" >&nbsp;</td>
            </tr>
		</table>
        <br>
		<?
		echo signature_table(108, $company, $table_width."px");
		// echo signature_table(2, $cbo_company_name, "1330px";
		?>
		<script type="text/javascript" src="../includes/functions.js"></script>
		<script type="text/javascript" src="../js/jquery.js"></script>
        <script type="text/javascript" src="../js/jquerybarcode.js"></script>
		<script>
            fnc_generate_Barcode('<? echo $Challan_no;?>','barcode_img_id');
        </script>
        <script> document.getElementById('kniteing_sorce_td').innerHTML='<? echo implode(',',$knit_sorce_arr);?>';  </script>
	</div>
	<?
    exit();
}

if($action=="delivery_challan_print_3") // Print 2
{
	extract($_REQUEST);

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$datas=explode('_',$data);
	$program_ids = str_replace("'","",$datas[0]);
	$company = str_replace("'","",$datas[1]);
	$from_date = str_replace("'","",$datas[2]);
	$to_date = str_replace("'","",$datas[3]);
	$product_ids = str_replace("'","",$datas[4]);
	$order_ids = str_replace("'","",$datas[5]);
	$location= str_replace("'","",$datas[6]);
	$buyer = str_replace("'","",$datas[7]);
	$update_mst_id = str_replace("'","",$datas[8]);
	$delivery_date = str_replace("'","",$datas[9]);
	$Challan_no = str_replace("'","",$datas[10]);
	$cbo_order_type = str_replace("'","",$datas[11]);
	$report_title = str_replace("'","",$datas[12]);
	//echo $cbo_order_type;die;
	$company_details=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$yarn_count_details=return_library_array( "select id,yarn_count from lib_yarn_count", "id", "yarn_count"  );
	$buyer_array = return_library_array("select id, short_name from  lib_buyer","id","short_name");
	$brand_details=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name"  );
	$supplier_arr=return_library_array( "select id, supplier_name from   lib_supplier",'id','supplier_name');
	$machine_details=array();
	$machine_data=sql_select("select id, machine_no, dia_width from lib_machine_name");
	foreach($machine_data as $row)
	{
		$machine_details[$row[csf('id')]]['no']=$row[csf('machine_no')];
		$machine_details[$row[csf('id')]]['dia']=$row[csf('dia_width')];
	}

	/*$po_array=array();
	//echo "select a.job_no, a.job_no_prefix_num, a.style_ref_no, b.id, b.po_number as po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company group by b.po_number";
	if($cbo_order_type==1)
	{
		$po_data=sql_select("select a.job_no, a.job_no_prefix_num, a.style_ref_no, b.id, b.po_number as po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company group by  b.id, b.po_number,a.job_no, a.job_no_prefix_num, a.style_ref_no");
		foreach($po_data as $row)
		{
			$job_year=explode("-",$row[csf('job_no')]);
			$po_array[$row[csf('id')]]['no']=$row[csf('po_number')];
			$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
			$po_array[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['job_no_prifix']=$job_year[1]."-".$job_year[2];
			$po_array[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
		}
	}*/

	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
		}
	}

	//echo "select id,grey_sys_id,product_id,job_no,order_id,current_delivery,roll from pro_grey_prod_delivery_dtls where mst_id=$update_mst_id";
	$update_row_check=array();
	if($update_mst_id!="")
	{
		$sql_update=sql_select("select id,grey_sys_id,product_id,job_no,order_id,current_delivery,roll,remarks,current_reject from pro_grey_prod_delivery_dtls where mst_id=$update_mst_id");
		foreach($sql_update as $row)
		{
			if($cbo_order_type==1)
			{
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("order_id")]]["current_delivery"] +=$row[csf("current_delivery")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("order_id")]]["current_reject"] +=$row[csf("current_reject")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("order_id")]]["id"] =$row[csf("id")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("order_id")]]["roll"] =$row[csf("roll")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("order_id")]]["remarks"] =$row[csf("remarks")];

			}
			else
			{
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]]["current_delivery"] +=$row[csf("current_delivery")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]]["current_reject"] +=$row[csf("current_reject")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]]["id"] =$row[csf("id")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]]["roll"] =$row[csf("roll")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]]["remarks"] =$row[csf("remarks")];
			}
		}
	}

	//var_dump($update_row_check);
	if($cbo_order_type==1)
	{
		$table_width=1770;
		$col_span=22;
	}
	else
	{
		$table_width=1540;
		$col_span=20;
	}

	?>
	<div style="width:<? echo $table_width;?>px;">
		<table width="<? echo $table_width;?>" cellspacing="0" align="center" border="0">
			<tr>
				<td colspan="1" rowspan="3" id="barcode_img_id"></td>
                <td colspan="<? echo $col_span-1;?>" align="center" style="font-size:x-large"><strong><? echo $company_details[$company]; ?></strong></td>
			</tr>
			<tr>
				<td colspan="<? echo $col_span-1;?>" align="center" style="font-size:18px"><strong><u><? echo $report_title; ?></u></strong></td>
			</tr>
        </table>
        <br>
		<table width="<? echo $table_width;?>" cellspacing="0" align="center" border="0">
			<tr>
				<td   style="font-size:16px; font-weight:bold;" width="110">Challan No</td>
                <td colspan="<? echo $col_span-1;?>"  >:&nbsp;<? echo $Challan_no; ?></td>
			</tr>
            <tr>
				<td style="font-size:16px; font-weight:bold;" width="110">Delivery Date </td>
                <td colspan="<? echo $col_span-1;?>" >:&nbsp;<? echo $delivery_date; ?></td>
			</tr>
        </table>
    </div>
    <div style="width:<? echo $table_width;?>px">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table_width;?>" class="rpt_table" >
            <thead>
                <tr>
                    <th width="30" >SL</th>
                    <?
                    if($cbo_order_type==1)
                    {
						?>
                        <th width="130" >Order No</th>
                        <th width="80" >Style No</th>
                        <th width="80" >Buyer <br> & Job</th>
                    	<?
					}
					else
					{
						?>
						<th width="60" >Buyer</th>
						<?
					}
					?>
                    <th width="50">System ID</th>
                    <?
                    if($cbo_order_type==1)
                    {
						?>
                    	<th width="100" >Prog. /Booking ID</th>
                        <?

					}
					else
					{
						?>
                        <th width="100" >Booking ID</th>
                        <?
					}
					?>
					<th width="100" >Booking No</th>
                    <th width="100" >Production Basis</th>
                    <th width="70" >Knitting Source</th>
                    <th width="90" >Knitting Company</th>
                    <!-- <th width="50" >Yarn Issue Challan No</th> -->
                    <th width="50" >Yarn Count</th>
                    <th width="90" >Yarn Brand</th>
                    <th width="60" >Lot No</th>
                    <th width="70" >Fab Color</th>
                    <!-- <th width="80" >Color Range</th> -->
                    <th width="240">Fabric Type</th>
                    <th width="50" >Stich</th>
                    <th width="60" >Fin GSM</th>
                    <th width="40" >Fab. Dia</th>
                    <th width="40" >M/C Dia</th>
                    <th width="70" >Total Roll</th>
                    <th width="80">Good Qty</th>
                    <th width="80">Reject Qty</th>
                    <th width="100">Remarks</th>
                </tr>
            </thead>
          <tbody>
	<?
		if($db_type==0)
		{
			if($from_date!="" && $to_date!="") $date_con="and a.receive_date between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'"; else $date_con="";
		}
		else if($db_type==2)
		{
			//if($txt_date_from!="" || $txt_date_to!="") $sql_cond .= " and wo_date  between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
			if($from_date!="" && $to_date!="") $date_con="and a.receive_date between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'"; else $date_con="";
		}

		if($location!=0) $location_con="and a.location_id=$location"; else $location_con="";
		if($buyer!=0) $buyer_con="and a.buyer_id=$buyer"; else $buyer_con="";
		if($db_type==0)
		{

			if($cbo_order_type==1)
			{
				$sql="select a.id as receive_id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no,a.booking_without_order, a.buyer_id, a.remarks,a.yarn_issue_challan_no, b.prod_id, b.febric_description_id, group_concat(b.gsm) as gsm, group_concat(b.width) as width,   group_concat(b.yarn_lot) as yarn_lot, group_concat(b.yarn_count) as yarn_count, max(b.brand_id) as brand_id, min(b.machine_no_id) as machine_no_id,group_concat(b.color_id) as color_id,max(b.color_range_id) as color_range_id, max(b.stitch_length) as  stitch_length, c.po_breakdown_id,c.is_sales, sum(c.quantity)  as outqntyshift, e.job_no, e.job_no_prefix_num, e.style_ref_no, d.po_number as po_number,b.machine_dia
				from
					inv_receive_master a, pro_grey_prod_entry_dtls b , order_wise_pro_details c, wo_po_break_down  d, wo_po_details_master e
				where
					a.id=b.mst_id  and b.id=c.dtls_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and c.trans_type=1 and c.po_breakdown_id in($order_ids) and a.entry_form=2 and a.status_active=1 and a.is_deleted=0  and a.company_id=$company  and a.id in ($program_ids) and b.prod_id in ($product_ids)  $date_con $location_con $buyer_con
				group by a.id,b.prod_id,c.po_breakdown_id,c.is_sales, d.po_number, e.job_no, e.job_no_prefix_num, e.style_ref_no,b.machine_dia order by e.style_ref_no";
				$results=sql_select($sql);
				$salesIDs="";
				foreach($results as $row)
				{
					if($row[csf("receive_basis")]==2 && $row[csf("is_sales")]==1)
					{
						$salesIDs.=$row[csf("po_breakdown_id")].",";
					}
				}
				$salesIDs=chop($salesIDs,",");
				$po_info_sql=sql_select("select a.id as sales_id,a.job_no as sales_job,e.job_no,e.style_ref_no,e.job_no_prefix_num from fabric_sales_order_mst a,wo_booking_mst b ,wo_po_break_down d, wo_po_details_master e where a.sales_booking_no=b.booking_no and b.job_no=d.job_no_mst and d.job_no_mst=e.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.id in($salesIDs)");
				foreach($po_info_sql as $row)
				{
					$po_info_arr[$row[csf("sales_id")]]["job_no"]=$row[csf("job_no")];
					$po_info_arr[$row[csf("sales_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
					$po_info_arr[$row[csf("sales_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];

					$salesOrderJob[$row[csf("sales_id")]]["sales_job"]=$row[csf("sales_job")];
				}

			}
			else
			{
				$sql="select a.id as receive_id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no,a.booking_without_order, a.buyer_id, a.remarks,a.yarn_issue_challan_no, b.prod_id, b.febric_description_id, group_concat(b.gsm) as gsm, group_concat(b.width) as width,   group_concat(b.yarn_lot) as yarn_lot, group_concat(b.yarn_count) as yarn_count, max(b.brand_id) as brand_id, max(b.machine_no_id) as machine_no_id, group_concat(b.color_id) as color_id,max(b.color_range_id) as color_range_id, max(b.stitch_length) as  stitch_length , sum(b.grey_receive_qnty)  as outqntyshift,b.machine_dia
				from
					inv_receive_master a, pro_grey_prod_entry_dtls b
				where
					a.id=b.mst_id and a.entry_form=2 and a.status_active=1 and a.is_deleted=0  and a.company_id=$company and a.booking_without_order=1  and a.id in ($program_ids) and b.prod_id in ($product_ids)  $date_con $location_con $buyer_con
				group by a.id,b.prod_id,b.machine_dia order by a.id";
			}
		}
		//LISTAGG(CAST( b.id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as po_id
		else
		{

			if($cbo_order_type==1)
			{
				$sql="select a.id as receive_id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no,a.booking_without_order, a.buyer_id, a.remarks,a.yarn_issue_challan_no, b.prod_id, max(b.febric_description_id) as febric_description_id, max(b.gsm) as gsm, max(b.width) as width, LISTAGG(CAST(b.yarn_lot AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY b.yarn_lot )as yarn_lot, LISTAGG(CAST(b.yarn_count AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY b.yarn_count )as yarn_count, max(b.brand_id) as brand_id, max(b.machine_no_id) as machine_no_id, LISTAGG(CAST(b.color_id AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY b.color_id ) as color_id,max(b.color_range_id) as color_range_id,max(b.stitch_length) as stitch_length, c.po_breakdown_id,c.is_sales, sum(c.quantity)  as outqntyshift, e.job_no, e.job_no_prefix_num, e.style_ref_no, d.po_number as po_number,b.machine_dia
				from
					inv_receive_master a, pro_grey_prod_entry_dtls b ,  order_wise_pro_details c, wo_po_break_down  d, wo_po_details_master e
				where
					a.id=b.mst_id and  b.id=c.dtls_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and c.po_breakdown_id in($order_ids)  and c.trans_type=1 and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and a.company_id=$company  and a.id in ($program_ids) and b.prod_id in ($product_ids)  $date_con $location_con $buyer_con
				group by
						 a.id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no,a. 	booking_without_order, a.buyer_id, a.remarks, a.yarn_issue_challan_no, b.prod_id,c.po_breakdown_id,c.is_sales, d.po_number, e.job_no, e.job_no_prefix_num, e.style_ref_no,b.machine_dia
				order by e.style_ref_no";

				$results=sql_select($sql);
				$salesIDs="";
				foreach($results as $row)
				{
					if($row[csf("receive_basis")]==2 && $row[csf("is_sales")]==1)
					{
						$salesIDs.=$row[csf("po_breakdown_id")].",";
					}
				}
				$salesIDs=chop($salesIDs,",");
				$po_info_sql=sql_select("select a.id as sales_id,a.job_no as sales_job,e.job_no,e.style_ref_no,e.job_no_prefix_num from fabric_sales_order_mst a,wo_booking_mst b ,wo_po_break_down d, wo_po_details_master e where a.sales_booking_no=b.booking_no and b.job_no=d.job_no_mst and d.job_no_mst=e.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.id in($salesIDs)");
				foreach($po_info_sql as $row)
				{
					$po_info_arr[$row[csf("sales_id")]]["job_no"]=$row[csf("job_no")];
					$po_info_arr[$row[csf("sales_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
					$po_info_arr[$row[csf("sales_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];

					$salesOrderJob[$row[csf("sales_id")]]["sales_job"]=$row[csf("sales_job")];
				}
			}
			else
			{
				$sql="select a.id as receive_id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no,a.booking_without_order, a.buyer_id, a.remarks,a.yarn_issue_challan_no, b.prod_id, max(b.febric_description_id) as febric_description_id, max(b.gsm) as gsm, max(b.width) as width, LISTAGG(CAST(b.yarn_lot AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY b.yarn_lot )as yarn_lot, LISTAGG(CAST(b.yarn_count AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY b.yarn_count )as yarn_count, max(b.brand_id) as brand_id, max(b.machine_no_id) as machine_no_id, LISTAGG(CAST(b.color_id AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY b.color_id ) as color_id, max(b.color_range_id) as color_range_id,max(b.stitch_length) as stitch_length, sum(b.grey_receive_qnty)  as outqntyshift,b.machine_dia
				from
					inv_receive_master a, pro_grey_prod_entry_dtls b
				where
					a.id=b.mst_id and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and a.company_id=$company and a.booking_without_order=1 and a.id in ($program_ids) and b.prod_id in ($product_ids)  $date_con $location_con $buyer_con
				group by
						 a.id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id, a.booking_no,a. 	booking_without_order, a.buyer_id, a.remarks, a.yarn_issue_challan_no, b.prod_id,b.machine_dia
				order by a.id";
			}

		}
	//echo $sql;
	// and dtls_id in (89,90,497) and is_sales!=1
	$sql_plan = "select mst_id, booking_no, dtls_id as prog_no
 from ppl_planning_entry_plan_dtls where status_active=1 and is_deleted=0  group by dtls_id, mst_id, booking_no";

 	$res_plan = sql_select($sql_plan);
	foreach ($res_plan as $rowPlan) {
		$progrum[$rowPlan[csf('prog_no')]] = $rowPlan[csf('booking_no')];
	}

	$nameArray=sql_select( $sql); $i=1; $tot_roll=0; $tot_qty=0;$style_check=array();$k=1;
	foreach($nameArray as $row)
	{
		if ($i%2==0)
			$bgcolor="#E9F3FF";
		else
			$bgcolor="#FFFFFF";


		$color_arr[$row[csf('color_id')]];
		$color_all="";
		$color_id_arr=array_unique(explode(",",$row[csf('color_id')]));
		foreach($color_id_arr as $color_id)
		{
			if($color_arr[$color_id]!="")
			{
				$color_all.=$color_arr[$color_id]. ", ";
			}
		}
		$color_all=chop($color_all," , ");


		$count='';
		$yarn_count=explode(",",$row[csf('yarn_count')]);
		foreach($yarn_count as $count_id)
		{
			if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
		}
		$count=implode(",",array_unique(explode(",",$count)));

		if($row[csf("booking_without_order")]==0)
		{
			$index_pk=$row[csf("receive_id")]."*".$row[csf("prod_id")]."*".$row[csf("po_breakdown_id")];
			$tot_delivery=$sql_production_arr[$row[csf("receive_id")]][$row[csf("prod_id")]][$row[csf("po_breakdown_id")]]['prodcut_qty'];
		}
		else
		{
			$index_pk=$row[csf("receive_id")]."*".$row[csf("prod_id")];
			$tot_delivery=$sql_production_arr[$row[csf("receive_id")]][$row[csf("prod_id")]]['prodcut_qty'];
		}
		//echo "<br>";
		//echo $row[csf("receive_id")]."jahid";

		if($row[csf("receive_basis")]==2 && $row[csf("is_sales")]==1)
		{
			$jobNo=$po_info_arr[$row[csf("po_breakdown_id")]]["job_no"];
			$styleRef=$po_info_arr[$row[csf("po_breakdown_id")]]["style_ref_no"];
			$jobNoPrefix=$po_info_arr[$row[csf("po_breakdown_id")]]["job_no_prefix_num"];
			$poNumber=$salesOrderJob[$row[csf("po_breakdown_id")]]["sales_job"];
		}
		else
		{
			$jobNo=$row[csf("job_no")];
			$styleRef=$row[csf("style_ref_no")];
			$jobNoPrefix=$row[csf("job_no_prefix_num")];
			$poNumber=$row[csf("po_number")];
		}



		if($update_row_check[$index_pk]["current_delivery"]>0)
		{
			if(!in_array($row[csf('style_ref_no')],$style_check))
			{
				$style_check[]=$row[csf('style_ref_no')];
				if ($k!=1)
				{
					?>
                    <tr bgcolor="#CCCCCC">
                        <td width="30">&nbsp;</td>
                        <?
                        if($cbo_order_type==1)
                        {
                            ?>
                            <td width="130">&nbsp;</td>
                            <td width="80">&nbsp;</td>
                            <?
                        }
                        ?>
                        <td width="80">&nbsp;</td>
                        <td width="50">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="90">&nbsp;</td>
                        <td width="50">&nbsp;</td>
                        <!-- <td width="50">&nbsp;</td> -->
                        <td width="90">&nbsp;</td>
                        <td width="60">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <!-- <td width="80">&nbsp;</td> -->
                        <td width="240">&nbsp;</td>
                        <td width="50">&nbsp;</td>
                        <td width="40">&nbsp;</td>
                        <td width="60">&nbsp;</td>
                        <td width="40">&nbsp;</td>
                        <td width="40">&nbsp;</td>
                        <td width="70" align="right" style="font-weight:bold;">File Total:</td>
                        <td  align="right" width="80"><div style="word-wrap:break-word; width:80px"><? echo number_format($file_current_delivery,2); ?></div></td>
                        <td  align="right" width="80"><div style="word-wrap:break-word; width:80px"><? echo number_format($file_current_reject,2); ?></div></td>
                        <td  align="right" width="100"><div style="word-wrap:break-word; width:100px"></div></td>
                    </tr>
                    <?
				}
				$k++;$file_current_delivery=$file_current_reject=0;
			}
			?>
			<tr bgcolor="<? echo $bgcolor; ?>">
                <td width="30"><div style="word-wrap:break-word; width:30px"><? echo $i; ?></div></td>
                <?
				if($cbo_order_type==1)
				{
					?>
                	<td width="130"><div style="word-wrap:break-word; width:130px"><? echo  $poNumber;?></div></td>
                    <td width="80"><div style="word-wrap:break-word; width:80px"><? echo  $styleRef;?></div></td>
                    <?
				}
				?>
                <td width="80"><div style="word-wrap:break-word; width:80px">
				<?

				echo $buyer_array[$row[csf('buyer_id')]]; echo "<br>";

				if($jobNoPrefix!="")
				{
					echo "Job-".$jobNoPrefix;
				}
				?></div></td>
                <td width="50" align="center"><div style="word-wrap:break-word; width:50px"><? echo $row[csf('recv_number_prefix_num')]; ?></div></td>
                <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[csf('booking_no')]; ?></div></td>
                <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $progrum[$row[csf('booking_no')]]; ?> </div></td>
                <td width="100"><div style="word-wrap:break-word; width:100px">
				<?
				if($row[csf('receive_basis')]==2)
				{
					echo "Knitting Plan";
				}
				else if($row[csf('receive_basis')]==1)
				{
					if($row[csf('booking_without_order')]==1)
					{
						echo "Without Order";
					}
					else
					{
						echo "With Order";
					}
				}
				else
				{
					echo "Independent";
				}
				?>
                </div></td>
                <td width="70"><div style="word-wrap:break-word; width:70px"><? if($row[csf("knitting_source")]==1)  echo "In-House"; else if($row[csf("knitting_source")]==3) echo "Sub-Contract";  ?></div></td>
                <td width="90"><div style="word-wrap:break-word; width:90px">
				<?
					if($row[csf("knitting_source")]==1)  echo $company_details[$row[csf("knitting_company")]]; //$company_arr
					else if($row[csf("knitting_source")]==3) echo $supplier_arr[$row[csf("knitting_company")]];
				?>
                </div></td>
                <!-- <td width="50" align="center"><div style="word-wrap:break-word; width:50px"><? //echo $row[csf("yarn_issue_challan_no")]; ?></div></td> -->
                <td width="50"><div style="word-wrap:break-word; width:50px"><? echo $count; ?></div></td>
                <td width="90"><div style="word-wrap:break-word; width:90px"><? echo $brand_details[$row[csf('brand_id')]];?></div></td>
                <td width="60"><div style="word-wrap:break-word; width:60px"><? echo implode(",",array_unique(explode(",",$row[csf('yarn_lot')]))); ?></div></td>
                <td width="70"><div style="word-wrap:break-word; width:70px"><? echo $color_all; ?></div></td>
                <!-- <td width="80"><div style="word-wrap:break-word; width:80px"><? //echo $color_range[$row[csf('color_range_id')]]; ?></div></td> -->
                <td width="240"><div style="word-wrap:break-word; width:240px"><? echo $composition_arr[$row[csf('febric_description_id')]]; ?></div></td>
                <td width="50"><div style="word-wrap:break-word; width:50px"><? echo $row[csf("stitch_length")]; ?></div></td>
                <td width="60"><div style="word-wrap:break-word; width:60px"><?  echo implode(",",array_unique(explode(",",$row[csf('gsm')]))); ?></div></td>
                <td width="40"><div style="word-wrap:break-word; width:40px"><? echo implode(",",array_unique(explode(",",$row[csf('width')])));?></div></td>
                <td width="40"><div style="word-wrap:break-word; width:40px"><? echo $row[csf('machine_dia')];//$machine_details[$row[csf('machine_no_id')]]['dia']; ?></div></td>
                <td width="70" align="right"><div style="word-wrap:break-word; width:70px"><? echo number_format($update_row_check[$index_pk]["roll"],2); $tot_roll+=$update_row_check[$index_pk]["roll"]; ?></div></td>
                <td  align="right" width="80"><div style="word-wrap:break-word; width:80px"><? echo number_format($update_row_check[$index_pk]["current_delivery"],2); $tot_qty+=$update_row_check[$index_pk]["current_delivery"]; ?></div></td>
                <td  align="right" width="80"><div style="word-wrap:break-word; width:80px"><? echo number_format($update_row_check[$index_pk]["current_reject"],2); $tot_reject_qty+=$update_row_check[$index_pk]["current_reject"]; ?></div></td>
                 <td width="100" align="left"><div style="word-wrap:break-word; width:100px"><? echo $update_row_check[$index_pk]["remarks"]; ?></div></td>
			</tr>
			<?
			$file_current_delivery+=$update_row_check[$index_pk]["current_delivery"];
			$file_current_reject+=$update_row_check[$index_pk]["current_reject"];
			$i++;
		}
	}
	?>
    		<tr bgcolor="#CCCCCC">
                <td width="30">&nbsp;</td>
                <?
                if($cbo_order_type==1)
                {
                    ?>
                    <td width="130">&nbsp;</td>
                    <td width="80">&nbsp;</td>
                    <?
                }
                ?>
                <td width="80">&nbsp;</td>
                <td width="50">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="90">&nbsp;</td>
                <td width="50">&nbsp;</td>
                <!-- <td width="50">&nbsp;</td> -->
                <td width="90">&nbsp;</td>
                <td width="60">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <!-- <td width="80">&nbsp;</td> -->
                <td width="240">&nbsp;</td>
                <td width="50">&nbsp;</td>
                <td width="60">&nbsp;</td>
                <td width="40">&nbsp;</td>
                <td width="40">&nbsp;</td>
                <td width="40">&nbsp;</td>
                <td width="70" align="right" style="font-weight:bold;">File Total:</td>
                <td  align="right" width="80"><div style="word-wrap:break-word; width:80px"><strong><? echo number_format($file_current_delivery,2); ?></strong></div></td>
                <td  align="right" width="80"><div style="word-wrap:break-word; width:80px"><strong><? echo number_format($file_current_reject,2); ?></strong></div></td>
                <td  align="right" width="100"><div style="word-wrap:break-word; width:100px"></div></td>
            </tr>


        	<tr>
                <td align="right" colspan="<? echo $col_span-3;?>" ><strong>Total:</strong></td>
                <td align="right"><strong><? echo number_format($tot_roll,2,'.',''); ?></strong></td>
                <td align="right" ><strong><? echo number_format($tot_qty,2,'.',''); ?></strong></td>
                <td align="right" ><strong><? echo number_format($tot_reject_qty,2,'.',''); ?></strong></td>
                <td align="right" ></td>
			</tr>
            <tr>
                <td colspan="2" align="left"><b>Remarks: <? //echo number_to_words($format_total_amount,$uom_unit,$uom_gm); ?></b></td>
                <td colspan="<? echo $col_span-2;?>" >&nbsp;</td>
            </tr>
		</table>
        <br>
		 <?
            echo signature_table(108, $company, $table_width."px");
         ?>
		<script type="text/javascript" src="../includes/functions.js"></script>
		<script type="text/javascript" src="../js/jquery.js"></script>
        <script type="text/javascript" src="../js/jquerybarcode.js"></script>
		<script>
            fnc_generate_Barcode('<? echo $Challan_no;?>','barcode_img_id');
        </script>
	</div>
	<?
    exit();
}

if($action=="delivery_challan_print_4") // Print 3
{
	extract($_REQUEST);

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$datas=explode('_',$data);
	$production_id = str_replace("'","",$datas[0]);
	$company = str_replace("'","",$datas[1]);
	$from_date = str_replace("'","",$datas[2]);
	$to_date = str_replace("'","",$datas[3]);
	$product_ids = str_replace("'","",$datas[4]);
	$order_ids = str_replace("'","",$datas[5]);
	$location= str_replace("'","",$datas[6]);
	$buyer = str_replace("'","",$datas[7]);
	$update_mst_id = str_replace("'","",$datas[8]);
	$delivery_date = str_replace("'","",$datas[9]);
	$Challan_no = str_replace("'","",$datas[10]);
	$cbo_order_type = str_replace("'","",$datas[11]);
	$report_title = str_replace("'","",$datas[12]);
	//echo $cbo_order_type;die;
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library",'master_tble_id','image_location');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$yarn_count_details=return_library_array( "select id,yarn_count from lib_yarn_count", "id", "yarn_count"  );
	$buyer_array = return_library_array("select id, short_name from  lib_buyer","id","short_name");
	$brand_details=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name");
	$location_arr=return_library_array( "select id, location_name from lib_location", "id", "location_name");

	$company_data=sql_select("select id, company_name, company_short_name, plot_no, road_no, city, contact_no, country_id from lib_company where status_active=1 and is_deleted=0");
	foreach($company_data as $row)
	{
		$company_array[$row[csf('id')]]['shortname']=$row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name']=$row[csf('company_name')];
		$company_address_arr[$row[csf('id')]] = 'Plot No:'.$row[csf('plot_no')].', Road No:'.$row[csf('road_no')].', City / Town:'.$row[csf('city')].', Country:'.$country_name_arr[$row[csf('country_id')]].', Contact No:'.$row[csf('contact_no')];
	}

	//for supplier
	$sqlSupplier = sql_select("select id as id, supplier_name as supplier_name, short_name as short_name, address_1 from lib_supplier where status_active=1 and is_deleted=0");
	foreach($sqlSupplier as $row)
	{
		$supplier_arr[$row[csf('id')]] = $row[csf('short_name')];
		$supplier_dtls_arr[$row[csf('id')]] = $row[csf('supplier_name')];
		$supplier_address_arr[$row[csf('id')]] = $row[csf('address_1')];
	}
	unset($sqlSupplier);

	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
		}
	}

	if($update_mst_id!="")
	{
		$sql_update=sql_select("SELECT id, grey_sys_id, product_id, job_no,order_id, current_delivery, current_delivery_qnty_in_pcs, roll, remarks, current_reject, insert_date, program_no, size_coller_cuff from pro_grey_prod_delivery_dtls where mst_id=$update_mst_id");
	}

	$production_sql="SELECT a.knitting_company, a.knitting_source, a.knitting_location_id, a.inserted_by from inv_receive_master a
	where a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and a.company_id=$company and a.id in ($production_id)";
	// echo $production_sql;
	$production_sql_result=sql_select($production_sql);
	$inserted_by=$production_sql_result[0]['INSERTED_BY'];
	foreach ($production_sql_result as $key => $row)
	{
		if ($row[csf('knitting_source')]==1) // In-House
		{
			$knitting_party=$company_array[$row[csf('knitting_company')]]['name'];
			$knitting_party_address=$location_arr[$row[csf('knitting_location_id')]];
		}
		else
		{
			$knitting_party=$supplier_dtls_arr[$row[csf('knitting_company')]];
			$knitting_party_address=$location_arr[$row[csf('knitting_location_id')]];
		}
	}

	if($cbo_order_type==1)
	{
		$table_width=1100;
		$col_span=16;
	}
	else
	{
		$table_width=1100;
		$col_span=15;
	}
	$date_time = strtotime($sql_update[0]['INSERT_DATE']);
	?>
	<div style="width:<? echo $table_width;?>px;">
		<table width="<? echo $table_width;?>" cellspacing="0" align="center" border="0">
			<tr>
				<td rowspan="3"><img  src='../<? echo $imge_arr[str_replace("'","",$company)]; ?>' height='70' /></td>
                <td colspan="<? echo $col_span-2;?>" align="center" style="font-size:x-large">
                <strong><?
				echo $company_array[$company]['name'].'<br>';//$company_details[$company].'<br>';

				$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, contact_no, country_id, province, city, zip_code, contact_no, email, website, vat_number from lib_company where id=$company and status_active=1 and is_deleted=0");
				foreach ($nameArray as $result)
				{
					?>
					<? echo $result[csf('plot_no')]; ?> &nbsp;
					<? echo $result[csf('level_no')]; ?> &nbsp;
					<? echo $result[csf('road_no')]; ?> &nbsp;
					<? echo $result[csf('block_no')];?> &nbsp;
					<? echo $result[csf('city')]; ?> &nbsp;
					<? echo $result[csf('contact_no')]; ?> &nbsp;
					<? echo $result[csf('email')]; ?> &nbsp;
					<? echo $result[csf('website')];?> <br>
				   	<b> Vat No : <? echo $result[csf('vat_number')]; ?></b> <?
				}
				?></strong>
                </td>
			</tr>
			<tr>
				<td colspan="<? echo $col_span-2;?>" align="center" style="font-size:18px"><strong><u><? echo ' KNITTING GREY DELIVERY CHALLAN'; ?></u></strong></td>
			</tr>
        </table>
        <br>
		<table cellspacing="0" border="0">
			<tr>
				<td style="font-size:16px; font-weight:bold;" width="200">Challan No</td>
                <td width="180">:&nbsp;<? echo $Challan_no; ?></td>
                <td width="150">Date &nbsp;</td>
                <td width="120">:&nbsp;<? echo date( 'd-m-y', $date_time ) ?></td>
                <td><? echo date( 'h:i:s a', $date_time );?></td>
			</tr>
            <tr>
				<td style="font-size:16px; font-weight:bold;" width="200">Knitting Party Name</td>
				<td width="180">:&nbsp;<? echo $knitting_party; ?></td>
                <td width="150">Print Date Time&nbsp;</td>
                <td width="120">:&nbsp;<? echo date('d-m-y'); ?></td>
                <td><? echo date('h:i:s a'); ?></td>
			</tr>
			<tr>
				<td style="font-size:16px; font-weight:bold;" width="200">Knitting Party Address</td>
				<td width="520">:&nbsp;<? echo $knitting_party_address; ?></td>
			</tr>
        </table>
    </div>
    <div style="width:<? echo $table_width;?>px; ">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table_width;?>" class="rpt_table" style="font-size:16px !important;" >
            <thead>
                <tr>
                    <th width="50">Program No</th>
                    <th width="250">Description</th>
                    <th width="90">Color</th>
                    <th width="80">Item</th>
                    <th width="80">Fin Dia</th>
                    <th width="50">Roll</th>
                    <th width="40">Grey Qty (kg)</th>
                    <th width="40">Size</th>
                    <th width="40">Qty (Pcs)</th>
                    <th width="100">Remarks</th>
                </tr>
            </thead>
          	<tbody>
				<?
				if($db_type==0)
				{
					if($from_date!="" && $to_date!="") $date_con="and a.receive_date between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'"; else $date_con="";
				}
				else if($db_type==2)
				{
					if($from_date!="" && $to_date!="") $date_con="and a.receive_date between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'"; else $date_con="";
				}

				if($location!=0) $location_con="and a.location_id=$location"; else $location_con="";
				if($buyer!=0) $buyer_con="and a.buyer_id=$buyer"; else $buyer_con="";

				if($cbo_order_type==1)
				{
					// $sql="SELECT a.id as receive_id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id as prog_no, a.booking_no,a.booking_without_order, a.buyer_id, a.remarks,a.yarn_issue_challan_no, b.prod_id, b.febric_description_id, b.gsm as gsm, b.width as width, b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.color_id, b.color_range_id,b.stitch_length, b.body_part_id, c.po_breakdown_id,c.is_sales, c.quantity, e.job_no, e.job_no_prefix_num, e.style_ref_no, d.po_number as po_number,b.machine_dia, b.coller_cuff_size from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, wo_po_break_down  d, wo_po_details_master e where a.id=b.mst_id and  b.id=c.dtls_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and c.po_breakdown_id in($order_ids)  and c.trans_type=1 and a.entry_form=2 and a.receive_basis=2 and a.status_active=1 and a.is_deleted=0 and a.company_id=$company  and a.id in ($production_id) and b.prod_id in ($product_ids)  $date_con $location_con $buyer_con and c.is_sales=0 ";


					$sql = "SELECT a.id as receive_id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id as prog_no, a.booking_no,a.booking_without_order, a.buyer_id, a.remarks,a.yarn_issue_challan_no, b.prod_id, b.febric_description_id, b.gsm as gsm, b.width as width, b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.color_id, b.color_range_id,b.stitch_length, b.body_part_id, c.po_breakdown_id,c.is_sales, c.quantity, e.job_no, e.job_no_prefix_num, e.style_ref_no, d.po_number as po_number,b.machine_dia, b.coller_cuff_size from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, wo_po_break_down  d, wo_po_details_master e where a.id=b.mst_id and  b.id=c.dtls_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and c.po_breakdown_id in($order_ids)  and c.trans_type=1 and a.entry_form=2 and a.receive_basis=2 and a.status_active=1 and a.is_deleted=0 and a.company_id=$company  and a.id in ($production_id) and b.prod_id in ($product_ids)  $date_con $location_con $buyer_con and c.is_sales=0 
				   union all  
				  SELECT a.id as receive_id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date,
					a.booking_id as prog_no, a.booking_no,a.booking_without_order, a.buyer_id, a.remarks,a.yarn_issue_challan_no, b.prod_id, b.febric_description_id, 
					b.gsm as gsm, b.width as width, b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.color_id, b.color_range_id,b.stitch_length, b.body_part_id,
					 c.po_breakdown_id,c.is_sales, c.quantity, d.job_no, null as job_no_prefix_num, d.STYLE_REF_NO , null as po_number,  b.machine_dia, b.coller_cuff_size 
					 from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, fabric_sales_order_mst d
					 where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and c.po_breakdown_id in($order_ids) and c.trans_type=1 
					 and a.entry_form=2 and a.receive_basis=2 and a.status_active=1 and a.is_deleted=0 and a.company_id=$company and a.id in ($production_id) and c.is_sales=1
				   and b.prod_id in ($product_ids) $date_con $location_con $buyer_con ";



					
				}
				else
				{
					$sql="SELECT a.id as receive_id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.knitting_source, a.knitting_company, a.receive_date, a.booking_id as prog_no, a.booking_no,a.booking_without_order, a.buyer_id, a.remarks,a.yarn_issue_challan_no, b.prod_id, b.febric_description_id, b.gsm as gsm, b.width as width, b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.color_id, b.color_range_id,b.stitch_length, b.body_part_id, b.grey_receive_qnty as quantity,b.machine_dia, b.coller_cuff_size
					from inv_receive_master a, pro_grey_prod_entry_dtls b
					where a.id=b.mst_id and a.entry_form=2 and a.receive_basis=2 and a.status_active=1 and a.is_deleted=0 and a.company_id=$company and a.booking_without_order=1 and a.id in ($production_id) and b.prod_id in ($product_ids)  $date_con $location_con $buyer_con";
				}
				// echo $sql;
				$results=sql_select($sql);
				foreach($results as $row)
				{
					$main_data_array[$row[csf("prog_no")]][$row[csf("body_part_id")]][$row[csf("coller_cuff_size")]]["color_id"].=$row[csf("color_id")].',';
					$main_data_array[$row[csf("prog_no")]][$row[csf("body_part_id")]][$row[csf("coller_cuff_size")]]["buyer_id"].=$row[csf("buyer_id")].',';
					$main_data_array[$row[csf("prog_no")]][$row[csf("body_part_id")]][$row[csf("coller_cuff_size")]]["style_ref_no"].=$row[csf("style_ref_no")].',';
					$main_data_array[$row[csf("prog_no")]][$row[csf("body_part_id")]][$row[csf("coller_cuff_size")]]["po_number"].=$row[csf("po_number")].',';
					$main_data_array[$row[csf("prog_no")]][$row[csf("body_part_id")]][$row[csf("coller_cuff_size")]]["stitch_length"].=$row[csf("stitch_length")].',';
					$main_data_array[$row[csf("prog_no")]][$row[csf("body_part_id")]][$row[csf("coller_cuff_size")]]["yarn_count"].=$row[csf("yarn_count")].',';
					$main_data_array[$row[csf("prog_no")]][$row[csf("body_part_id")]][$row[csf("coller_cuff_size")]]["brand_id"].=$row[csf("brand_id")].',';
					$main_data_array[$row[csf("prog_no")]][$row[csf("body_part_id")]][$row[csf("coller_cuff_size")]]["yarn_lot"].=$row[csf("yarn_lot")].',';
					$main_data_array[$row[csf("prog_no")]][$row[csf("body_part_id")]][$row[csf("coller_cuff_size")]]["febric_description_id"].=$row[csf("febric_description_id")].',';
					$main_data_array[$row[csf("prog_no")]][$row[csf("body_part_id")]][$row[csf("coller_cuff_size")]]["job_no"].=$row[csf("job_no")].',';
					$main_data_array[$row[csf("prog_no")]][$row[csf("body_part_id")]][$row[csf("coller_cuff_size")]]["width"].=$row[csf("width")].',';
					$prod_body_part_array[$row[csf("prog_no")]]=$row[csf("body_part_id")];
				}
				// echo "<pre>";print_r($main_data_array);

				// =========== Grey Fabric Delivery to Store Start ==============================
				$update_row_check=array();
				if($update_mst_id!="")
				{
					foreach($sql_update as $row)
					{
						$prod_body_part=$prod_body_part_array[$row[csf("program_no")]];
						//echo $prod_body_part.'<br>';
						$update_row_check[$row[csf("program_no")]."*".$prod_body_part."*".$row[csf("size_coller_cuff")]]["current_delivery"] +=$row[csf("current_delivery")];
						$update_row_check[$row[csf("program_no")]."*".$prod_body_part."*".$row[csf("size_coller_cuff")]]["qnty_in_pcs"] +=$row[csf("current_delivery_qnty_in_pcs")];
						$update_row_check[$row[csf("program_no")]."*".$prod_body_part."*".$row[csf("size_coller_cuff")]]["id"]=$row[csf("id")];
						$update_row_check[$row[csf("program_no")]."*".$prod_body_part."*".$row[csf("size_coller_cuff")]]["roll"]+=$row[csf("roll")];
						$update_row_check[$row[csf("program_no")]."*".$prod_body_part."*".$row[csf("size_coller_cuff")]]["remarks"].=$row[csf("remarks")].', ';
					}
				}
				// echo "<pre>";print_r($update_row_check);
				// =========== Grey Fabric Delivery to Store End ==============================

				$program_count = array(); $body_part_count = array();
				foreach ($main_data_array as $program_v => $program_ar)
				{
					foreach ($program_ar as $body_part_id => $body_part_ar)
					{
						foreach ($body_part_ar as $ccsize => $row)
						{
							$program_count[$program_v]++;
							$body_part_count[$program_v][$body_part_id]++;
						}
					}
				}

				$nameArray=sql_select( $sql); $i=1; $tot_roll=0; $tot_qty=0;$tot_qty_pcs=0;
				$knit_sorce_arr=array();
				foreach ($main_data_array as $program_v => $program_ar)
				{
					$sub_tot_roll=0; $sub_tot_qty=0;$sub_tot_qty_pcs=0;
					foreach ($program_ar as $body_part_id => $body_part_ar)
					{
						foreach ($body_part_ar as $ccsize => $row)
						{
							$program_span = $program_count[$program_v]++;
							$body_part_span = $body_part_count[$program_v][$body_part_id]++;

							$count='';
							$yarn_count=array_unique(explode(",",chop($row['yarn_count'],",")));
							foreach($yarn_count as $count_id)
							{
								if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
							}
							$count=implode(",",array_unique(explode(",",$count)));

							$fabric_info="";
							$detar_id_arr=array_unique(explode(",",chop($row['febric_description_id'],",")));
							foreach($detar_id_arr as $detar_id)
							{
								if($composition_arr[$detar_id]!="")
								{
									$fabric_info.=$composition_arr[$detar_id]. ", ";
								}
							}
							$fabric_info=chop($fabric_info," , ");

							$buyer_name="";
							$buyer_id_arr=array_unique(explode(",",chop($row['buyer_id'],",")));
							foreach($buyer_id_arr as $bu_id)
							{
								if($buyer_array[$bu_id]!="")
								{
									$buyer_name.=$buyer_array[$bu_id]. ", ";
								}
							}
							$buyer_name=chop($buyer_name," , ");

							$brand_name="";
							$brand_id_arr=array_unique(explode(",",chop($row['brand_id'],",")));
							foreach($brand_id_arr as $brand_id)
							{
								if($brand_details[$brand_id]!="")
								{
									$brand_name.=$brand_details[$brand_id]. ", ";
								}
							}
							$brand_name=chop($brand_name," , ");

							$style_no=implode(",", array_unique(explode(",",chop($row['style_ref_no'],","))));
							$po_number=implode(",", array_unique(explode(",",chop($row['po_number'],","))));
							$stitch_length=implode(",", array_unique(explode(",",chop($row['stitch_length'],","))));
							$yarn_lot=implode(",", array_unique(explode(",",chop($row['yarn_lot'],","))));
							$po_number=implode(",", array_unique(explode(",",chop($row['job_no'],","))));

							$description="Buyer Name - ".$buyer_name."<br>Style No - ".$style_no."<br>Order No - ".$po_number."<br>Stitch Length - ".$stitch_length."<br>Yarn Count - ".$count."<br>Brand - ".$brand_name."<br>Yarn Lot - ".$yarn_lot."<br>Composition - ".$fabric_info;

							$color_all="";
							$color_id_arr=array_unique(explode(",",chop($row['color_id'],",")));
							foreach($color_id_arr as $color_id)
							{
								if($color_arr[$color_id]!="")
								{
									$color_all.=$color_arr[$color_id]. ", ";
								}
							}
							$color_all=chop($color_all," , ");

							$fin_dia=implode(",",array_unique(explode(",",chop($row['width'],","))));

							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$index_pk=$program_v."*".$body_part_id."*".$ccsize;
							if($update_row_check[$index_pk]["current_delivery"]>0)
							{
								?>
								<tr bgcolor="<? echo $bgcolor; ?>">
									<?
									if(!in_array($program_v,$program_chk))
									{
										$program_chk[]=$program_v;
										?>
										<td width="50" rowspan="<? echo $program_span ;?>" valign="middle" ><? echo $program_v; ?>&nbsp;</td>
										<td width="250" class="word_wrap_break" rowspan="<? echo $program_span ;?>" valign="middle" ><? echo $description; ?></td>
										<td width="90" class="word_wrap_break" rowspan="<? echo $program_span ;?>" valign="middle"><? echo $color_all; ?></td>
										<?
									}
									if(!in_array($program_v."**".$body_part_id,$body_part_chk))
									{
										$body_part_chk[]=$program_v."**".$body_part_id;
										?>
										<td width="80" rowspan="<? echo $body_part_span ;?>" valign="middle"><? echo $body_part[$body_part_id]; ?></td>
										<?
									}
					                ?>

					                <td width="80"><? echo $fin_dia;?></td>
					                <td width="50" align="right"><? echo number_format($update_row_check[$index_pk]["roll"],2); ?></td>
					                <td width="40" align="right"><? echo number_format($update_row_check[$index_pk]["current_delivery"],2); ?></td>
					                <td width="40"><? echo $ccsize;?></td>
					                <td width="40" align="right"><? echo number_format($update_row_check[$index_pk]["qnty_in_pcs"],2); ?></td>
					                <td width="100"><? echo chop($update_row_check[$index_pk]["remarks"],", "); ?></td>
								</tr>
								<?
								$i++;
								$sub_tot_roll+=$update_row_check[$index_pk]["roll"];
								$sub_tot_qty+=$update_row_check[$index_pk]["current_delivery"];
								$sub_tot_qty_pcs+=$update_row_check[$index_pk]["qnty_in_pcs"];

								$tot_roll+=$update_row_check[$index_pk]["roll"];
								$tot_qty+=$update_row_check[$index_pk]["current_delivery"];
								$tot_qty_pcs+=$update_row_check[$index_pk]["qnty_in_pcs"];
							}
						}
					}
					?>
					<tr>
			            <td></td>
			            <td></td>
			            <td></td>
			            <td></td>
			            <td align="right"" ><strong>Sub Total</strong></td>
			            <td align="right"><? echo number_format($sub_tot_roll,2,'.',''); ?></td>
			            <td align="right"><? echo number_format($sub_tot_qty,2,'.',''); ?></td>
			            <td></td>
			            <td align="right"><? echo number_format($sub_tot_qty_pcs,2,'.',''); ?></td>
			            <td></td>
					</tr>
					<?
				}
				?>
		        <tr>
		            <td></td>
		            <td></td>
		            <td></td>
		            <td></td>
		            <td align="right"" ><strong>Total</strong></td>
		            <td align="right"><? echo number_format($tot_roll,2,'.',''); ?></td>
		            <td align="right"><? echo number_format($tot_qty,2,'.',''); ?></td>
		            <td></td>
		            <td align="right"><? echo number_format($tot_qty_pcs,2,'.',''); ?></td>
		            <td></td>
				</tr>
		    </tbody>
		</table>
		<br>
		<?
		 	$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
			echo signature_table(108,$company,$table_width."px",$template_id,10,$user_lib_name[$inserted_by]);
        ?>
	</div>
    <br>
	<script type="text/javascript" src="../includes/functions.js"></script>
	<script type="text/javascript" src="../js/jquery.js"></script>
	</div>
	<?
    exit();
}
?>
