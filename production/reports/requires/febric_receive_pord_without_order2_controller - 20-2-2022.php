<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_name=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if($db_type==2 || $db_type==1 )
{
	$select_year="to_char(a.insert_date,'YYYY')";
}
else if ($db_type==0)
{
	$select_year="year(a.insert_date)";
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
	exit();
}

$tmplte=explode("**",$data);

if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;

	$booking_no_details=return_library_array( "select id, booking_no from wo_non_ord_samp_booking_mst", "id", "booking_no");
	$color_array=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
	$product_details=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_details=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
	$batch_details=return_library_array( "select id, batch_no from pro_batch_create_mst", "id", "batch_no");
	$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$yarn_count_details=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	
if($action=="report_generate")
{
	$started = microtime(true);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	//$sample_array=return_library_array( "select id,sample_name from lib_sample order by sample_name","id","sample_name");
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$cbo_wo_year=str_replace("'","",$cbo_wo_year);
	$txt_wo_no=trim(str_replace("'","",$txt_wo_no));
	$txt_internal_ref=trim(str_replace("'","",$txt_internal_ref));
	$wo_cond="";
	if($txt_wo_no!="") $wo_cond.=" and a.booking_no_prefix_num=$txt_wo_no"; 
	
	if($cbo_wo_year>0) $wo_cond.=" and $select_year='$cbo_wo_year'";
	
	if($txt_internal_ref!="") $wo_cond.=" and a.grouping='$txt_internal_ref'"; 
	//echo $wo_cond;die;
	
	if($cbo_buyer_name==0) $buyer_name="%%"; else $buyer_name=$cbo_buyer_name;
	if($db_type==0)
	{
		if($txt_date_from!="" && $txt_date_to!="") $str_cond=" and a.booking_date  between '$txt_date_from' and '$txt_date_to'"; else $str_cond="";
	}
	else if($db_type==2)
	{
		if($txt_date_from!="" && $txt_date_to!="") $str_cond=" and a.booking_date  between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'"; else $str_cond="";
	}
	
	//echo $str_cond;die;
	$sql="SELECT a.id as booking_id, a.booking_no, a.booking_no_prefix_num, $select_year as wo_year, a.is_short, a.po_break_down_id, a.fabric_source, a.is_approved, a.job_no, a.buyer_id, a.company_id,a.entry_form_id, a.supplier_id, a.item_category, a.is_approved, a.grouping, b.sample_type, b.grey_fabric as grey_fabric_qnty, b.finish_fabric as finish_fabric_qty, b.fabric_color, b.fabric_description, b.style_id, b.composition, a.booking_date, b.lib_yarn_count_deter_id, a.booking_date, a.delivery_date
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
	where a.booking_no=b.booking_no and a.item_category in(2,13) and a.company_id like '$cbo_company_name' and a.buyer_id like '$buyer_name'  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $str_cond $wo_cond order by a.booking_no";
	//echo $sql;die;
	$sql_res=sql_select($sql); $allBookingIds=''; $tot_rows=0;
	$booking_id_array = array();
	$booking_no_array = array();
	$booking_id_to_booking_no_array = array();
	foreach ($sql_res as $val) 
	{
		$booking_id_array[$val[csf("booking_id")]]=$val[csf("booking_id")];
		$booking_no_array[$val[csf("booking_no")]]=$val[csf("booking_no")];
		$booking_id_to_booking_no_array[$val[csf("booking_id")]]=$val[csf("booking_no")];
		if($val[csf("entry_form_id")]==140)
		{
			$samp_req_booking_arr[$val[csf("booking_id")]]=$val[csf("style_id")];
			$samp_req_booking_arr2[$val[csf("style_id")]]=$val[csf("booking_id")];
		}
	}
	$samp_req_booking_count=count($samp_req_booking_arr);
	$booking_id = implode(",", $booking_id_array);
	$samp_req_mst_booking_id= implode(",", $samp_req_booking_arr);
	//echo $samp_req_mst_booking_id.'D';
	$booking_no = "'".implode("', '", $booking_no_array)."'";

	$dtls_ids_arr=array();
	$booking_wise_gray_data=array();
	
	$fabricData = sql_select("select variable_list, fabric_roll_level from variable_settings_production where company_name ='$cbo_company_name' and variable_list in(3) and item_category_id=2 and is_deleted=0 and status_active=1");
	$finish_roll_maintained=2;//Issue Id=12692 //For Finish Fab Qty
	foreach ($fabricData as $row) {
		$finish_roll_maintained = $row[csf('fabric_roll_level')];
	}

	/*$program_data=sql_select("SELECT c.id as program_no, b.booking_no, a.id as booking_id 
		from wo_non_ord_samp_booking_mst a, ppl_planning_info_entry_mst b, ppl_planning_info_entry_dtls c
	where a.booking_no=b.booking_no and b.id=c.mst_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and b.booking_no in($booking_no) 
	group by c.id, b.booking_no, a.id");
	//$prog_booking_id_array=array();
	$program_no_array=array();
	foreach ($program_data as $val) 
	{
		$booking_id_array[$val[csf("program_no")]]=$val[csf("program_no")];
		$booking_no_array[$val[csf("booking_no")]]=$val[csf("booking_no")];
		$prog_booking_id_array[$val[csf("booking_id")]]=$val[csf("booking_no")];
	}
	$booking_id = implode(",", $booking_id_array);
	$booking_no = "'".implode("', '", $booking_no_array)."'";*/
	
	$sql_dtls_ids_arr=sql_select("SELECT b.id as dtls_id, a.roll_maintained,a.booking_id from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.booking_id in($booking_id) and a.booking_no in($booking_no) and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	union all
	SELECT b.id as dtls_id, a.roll_maintained,e.id as booking_id 
	from inv_receive_master a, pro_grey_prod_entry_dtls b, ppl_planning_entry_plan_dtls c, wo_non_ord_samp_booking_mst e 
	where a.id=b.mst_id  and a.booking_id=c.dtls_id and c.booking_no=e.booking_no 
	and e.id in($booking_id) and e.booking_no in($booking_no)
	and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

	/*echo "SELECT b.id as dtls_id, a.roll_maintained,a.booking_id from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.booking_id in($booking_id) and a.booking_no in($booking_no) and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0;
	die;*/

	$booking_wise_roll_check = array();
	foreach ($sql_dtls_ids_arr as $val) 
	{
		$booking_wise_roll_check[$val[csf('booking_id')]]=$val[csf('roll_maintained')];
		$dtls_ids_arr[]="'".$val[csf('dtls_id')]."'";
	}
	if($samp_req_booking_count>0)
	{
		$samp_req_color_arr="select b.booking_no,c.sample_mst_id,d.color_id,d.fabric_color from wo_non_ord_samp_booking_dtls b,sample_development_fabric_acc c,sample_development_rf_color d where c.id=d.dtls_id and c.sample_mst_id=b.style_id and c.sample_mst_id in($samp_req_mst_booking_id)  and b.entry_form_id=140 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and d.status_active=1 ";//and d.fabric_color>0
		// echo $samp_req_color_arr;
		$samp_req_color_result=sql_select($samp_req_color_arr);
	
		foreach ($samp_req_color_result as $val) 
		{
			if($val[csf('fabric_color')]==0)
			{
				$booking_wise_color_check[$val[csf('booking_no')]].=$val[csf('color_id')].',';
			}
			else
			{
				$booking_wise_color_check[$val[csf('booking_no')]].=$val[csf('fabric_color')].',';
			}
			
		}
	}
	//print_r($booking_wise_color_check);
	$dtls_trans_ids_arr=array();
	$sql_ord_sample=sql_select("select b.id as dtls_id, a.to_order_id as booking_id from inv_item_transfer_mst a, inv_item_transfer_dtls b where a.id=b.mst_id and a.to_order_id in($booking_id) and a.entry_form=110 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach ($sql_ord_sample as $val) 
	{
		$dtls_trans_ids_arr[]="'".$val[csf('dtls_id')]."'";
	}
			
	$dtls_ids= implode(",", array_unique($dtls_ids_arr));
	$dtls_trans_ids= implode(",", array_unique($dtls_trans_ids_arr));
	
	//FINDING BARCODE NUMBER AND ROLL HISTORY FOR PRODUCTIONS
	$prod_roll_dtls_arr=array();

	$dtls_ids_array = array_unique($dtls_ids_arr);
	if(!empty($dtls_ids_array))
	{
		$all_dtls_id_cond="";
		$dtlsIdCond="";
	    if($db_type==2 && count($dtls_ids_array)>999)
	    {
	    	$dtls_ids_arr_chunk=array_chunk($dtls_ids_array,999) ;
	    	foreach($dtls_ids_arr_chunk as $chunk_arr)
	    	{
	    		$chunk_arr_value=implode(",",$chunk_arr);
	    		$dtlsIdCond.="  dtls_id in($chunk_arr_value) or ";
	    	}

	    	$all_dtls_id_cond.=" and (".chop($dtlsIdCond,'or ').")";
	    }
	    else
	    {
	    	$all_dtls_id_cond=" and dtls_id in($dtls_ids)";
	    }

	    $sql_prod_roll_dtls_arr=sql_select("select id, roll_no, barcode_no, po_breakdown_id, qnty, booking_no from pro_roll_details where entry_form=2 and status_active=1 and is_deleted=0 $all_dtls_id_cond");//booking_no in($booking_no) and 
		foreach ($sql_prod_roll_dtls_arr as $val) 
		{
			$prod_roll_dtls_arr[]=$val[csf('barcode_no')];
			$booking_wise_gray_data[$booking_id_to_booking_no_array[$val[csf('po_breakdown_id')]]]['barcodes'].=$val[csf('barcode_no')].',';
		}
	}
	// echo "<pre>";print_r($booking_wise_gray_data);
	
	if($dtls_trans_ids!="")
	{
		$sql_trans_prod_roll_dtls_arr=sql_select("select id, roll_no, barcode_no, po_breakdown_id, qnty, booking_no from pro_roll_details where dtls_id in ($dtls_trans_ids) and entry_form=110 and status_active=1 and is_deleted=0");
		foreach ($sql_trans_prod_roll_dtls_arr as $val) 
		{
			$prod_roll_dtls_arr[]=$val[csf('barcode_no')];
			$booking_wise_gray_data[$val[csf('booking_no')]]['barcodes'].=$val[csf('barcode_no')].',';
		}
	}
	
	//$barcode_ids= implode(",", array_unique($prod_roll_dtls_arr));

	$prod_roll_dtls_arr = array_filter(array_unique($prod_roll_dtls_arr));
	if(!empty($prod_roll_dtls_arr))
	{
	    $all_barcode_no_cond="";
		$barCond="";
	    if($db_type==2 && count($prod_roll_dtls_arr)>999)
	    {
	    	$all_barcode_arr_chunk=array_chunk($prod_roll_dtls_arr,999) ;
	    	foreach($all_barcode_arr_chunk as $chunk_arr)
	    	{
	    		$chunk_arr_value=implode(",",$chunk_arr);
	    		$barCond.="  barcode_no in($chunk_arr_value) or ";
	    	}

	    	$all_barcode_no_cond.=" and (".chop($barCond,'or ').")";
	    }
	    else
	    {
	    	$barcode_ids= implode(",", $prod_roll_dtls_arr);
	    	$all_barcode_no_cond=" and barcode_no in($barcode_ids)";
	    }

	    //finding gray febric receive roll dtls 
		$gray_feb_rcv_arr=sql_select("select po_breakdown_id, sum(qnty) as recv_qty from pro_roll_details where entry_form=58 and status_active=1 and is_deleted=0 $all_barcode_no_cond group by po_breakdown_id"); 
		foreach ($gray_feb_rcv_arr as $val) 
		{
			$booking_wise_gray_data[$booking_id_to_booking_no_array[$val[csf('po_breakdown_id')]]]['gray_rcv_qty']=$val[csf('recv_qty')];
		}

		//finding gray febric issue roll wise
		$gray_feb_iss_arr=sql_select("select po_breakdown_id, sum(qnty) as roll_issue_qty from pro_roll_details where  entry_form=61 and status_active=1 and is_deleted=0 $all_barcode_no_cond group by po_breakdown_id");

		foreach ($gray_feb_iss_arr as $val) 
		{
			$booking_wise_gray_data[$booking_id_to_booking_no_array[$val[csf('po_breakdown_id')]]]['gray_issue_qty']=$val[csf('roll_issue_qty')];
		}

		//finding gray finish febric production roll wise
		$gray_fin_feb_prod_arr=sql_select("select booking_no, sum(qnty) as roll_qty from pro_roll_details where entry_form=66 and status_active=1 and is_deleted=0 $all_barcode_no_cond group by booking_no");
		$booking_wise_gray_data[$booking_no]['gray_fin_feb_prod_qty']=$gray_fin_feb_prod_arr[0][csf('roll_qty')];
		//echo "<pre>";print_r($booking_wise_gray_data2);

		//finding finish febric delivery to store roll wise
		$fin_feb_delivery_store_arr=sql_select("select booking_no, sum(qnty) as roll_qty from pro_roll_details where entry_form=67 and status_active=1 and is_deleted=0 $all_barcode_no_cond group by booking_no");
		$booking_wise_gray_data[$booking_no]['fin_feb_delivery_store_qty']=$fin_feb_delivery_store_arr[0][csf('roll_qty')];
		
		//finding finish febric roll receive by store qty
		$fin_feb_recv_by_store_arr=sql_select("select booking_no, sum(qnty) as roll_qty from pro_roll_details where entry_form=68 and status_active=1 and is_deleted=0 $all_barcode_no_cond group by booking_no");
		$booking_wise_gray_data[$booking_no]['fin_feb_rcb_by_store_qty']=$fin_feb_recv_by_store_arr[0][csf('roll_qty')];

		//finding finish febric roll Issue qty
		$fin_feb_roll_issue_arr=sql_select("select booking_no, sum(qnty) as roll_qty from pro_roll_details where entry_form=71 and status_active=1 and is_deleted=0 $all_barcode_no_cond group by booking_no");
		$booking_wise_gray_data[$booking_no]['fin_feb_roll_issue_qty']=$fin_feb_roll_issue_arr[0][csf('roll_qty')];
	}

	// die();
	//--------------------------------------------------------------------------------------------------
	if(!empty($sql_res))	
 	{
 		$con = connect();
		$r_id=execute_query("delete from tmp_booking_id where userid=$user_name");
		if($r_id)
		{
		    oci_commit($con);
		}
	}


	$booking_id_check=array();
	foreach($sql_res as $row)
	{
		$tot_rows++;
		$result_mst_array[$row[csf("booking_id")]]["booking_id"]=$row[csf("booking_id")];
		$result_mst_array[$row[csf("booking_id")]]["booking_no"]=$row[csf("booking_no")];
		$result_mst_array[$row[csf("booking_id")]]["booking_no_prefix_num"]=$row[csf("booking_no_prefix_num")];
		$result_mst_array[$row[csf("booking_id")]]["wo_year"]=$row[csf("wo_year")];
		$result_mst_array[$row[csf("booking_id")]]["is_short"]=$row[csf("is_short")];
		$result_mst_array[$row[csf("booking_id")]]["po_id"]=$row[csf("po_break_down_id")];
		$result_mst_array[$row[csf("booking_id")]]["job_no"]=$row[csf("job_no")];
		$result_mst_array[$row[csf("booking_id")]]["booking_date"]=$row[csf("booking_date")];
		$result_mst_array[$row[csf("booking_id")]]["company_id"]=$row[csf("company_id")];
		$result_mst_array[$row[csf("booking_id")]]["supplier_id"]=$row[csf("supplier_id")];
		$result_mst_array[$row[csf("booking_id")]]["item_category"]=$row[csf("item_category")];
		$result_mst_array[$row[csf("booking_id")]]["is_approved"]=$row[csf("is_approved")];
		$result_mst_array[$row[csf("booking_id")]]["sample_type"].=$row[csf("sample_type")].",";
		$result_mst_array[$row[csf("booking_id")]]["buyer_id"]=$row[csf("buyer_id")];
		$result_mst_array[$row[csf("booking_id")]]["style_id"]=$row[csf("style_id")];
		$result_mst_array[$row[csf("booking_id")]]["internal_ref"]=$row[csf("grouping")];
		
		$result_mst_array[$row[csf("booking_id")]]["booking_date"]=$row[csf("booking_date")];
		$result_mst_array[$row[csf("booking_id")]]["entry_form_id"]=$row[csf("entry_form_id")];
		$result_mst_array[$row[csf("booking_id")]]["delivery_date"]=$row[csf("delivery_date")];
		$result_mst_array[$row[csf("booking_id")]]["grey_fabric_qnty"]+=$row[csf("grey_fabric_qnty")];
		//echo $row[csf("entry_form_id")].'D';
		if(!$booking_id_check[$row[csf('booking_id')]])
	    {
	        $booking_id_check[$row[csf('booking_id')]]=$row[csf('booking_id')];
	        $BOOKINGID = $row[csf('booking_id')];
	        $BOOKINGNO = $row[csf('booking_no')];
	        $rID=execute_query("insert into tmp_booking_id (userid, booking_id,booking_no,type) values ($user_name,$BOOKINGID,'$BOOKINGNO',1)");
	    }

	   
		
		if($row[csf("entry_form_id")]==140) //Issue Id=12692 
		{
			$booking_wise_colors=rtrim($booking_wise_color_check[$row[csf('booking_no')]],',');
			$booking_wise_colors= array_unique(explode(",",$booking_wise_colors));
			foreach($booking_wise_colors as $fb_color)
			{	if($fb_color>0)
				{
				$result_dtls_array[$row[csf("booking_id")]][$fb_color]["fabric_source"]=$row[csf("fabric_source")];
				$result_dtls_array[$row[csf("booking_id")]][$fb_color]["fabric_description"][]=$row[csf("fabric_description")];
				$result_dtls_array[$row[csf("booking_id")]][$fb_color]["fabric_color"]=$fb_color;
				$result_dtls_array[$row[csf("booking_id")]][$fb_color]["lib_yarn_count_deter_id"]=$row[csf("lib_yarn_count_deter_id")];
				$result_dtls_array[$row[csf("booking_id")]][$fb_color]["composition"]=$row[csf("composition")];
				$result_dtls_array[$row[csf("booking_id")]][$fb_color]["finish_fabric_qty"]+=$row[csf("finish_fabric_qty")];
				}
			}
		}
		else
		{
			$result_dtls_array[$row[csf("booking_id")]][$row[csf("fabric_color")]]["fabric_source"]=$row[csf("fabric_source")];
			$result_dtls_array[$row[csf("booking_id")]][$row[csf("fabric_color")]]["fabric_description"][]=$row[csf("fabric_description")];
			$result_dtls_array[$row[csf("booking_id")]][$row[csf("fabric_color")]]["fabric_color"]=$row[csf("fabric_color")];
			$result_dtls_array[$row[csf("booking_id")]][$row[csf("fabric_color")]]["lib_yarn_count_deter_id"]=$row[csf("lib_yarn_count_deter_id")];
			$result_dtls_array[$row[csf("booking_id")]][$row[csf("fabric_color")]]["composition"]=$row[csf("composition")];
			$result_dtls_array[$row[csf("booking_id")]][$row[csf("fabric_color")]]["finish_fabric_qty"]+=$row[csf("finish_fabric_qty")];
		}
		
		
		$buyer_wise_result[$row[csf("buyer_id")]]["buyer_id"]=$row[csf("buyer_id")];
		$buyer_wise_result[$row[csf("buyer_id")]]["grey_fabric_qnty"] +=$row[csf("grey_fabric_qnty")];
		
		$dtls_batch_qty=$batch_qty_arr_check[$row[csf("booking_id")]][$row[csf("fabric_color")]]['batch_qnty'];
		$dy_check_id=$dyeing_check_arr[$batch_qty_arr_check[$row[csf("booking_id")]][$row[csf("fabric_color")]]['batch_id']];
		if($dy_check_id!="") 
		{
			$buyer_wise_result[$row[csf("buyer_id")]]["dyeing_qty"] +=$dtls_batch_qty;
			$gt_dying_qty+=$dtls_batch_qty;
		}
		
		$gt_yarn_grey_required+=$row[csf("grey_fabric_qnty")];
		$gt_finish_requir+=$row[csf("finish_fabric_qty")];
		$gt_finish_available+=($finish_receive_arr[$row[csf("booking_id")]][$row[csf("fabric_color")]]+$finish_product_arr[$row[csf("booking_id")]][$row[csf("fabric_color")]]);
		//$gt_issue_cutting+=$issue_to_cut_arr[$row[csf("booking_id")]][$row[csf("fabric_color")]];
		
		if($db_type==2)
		{
			$add_comma="'";
			$allBookingIds.=$row[csf("booking_id")].",";
			$allBookingNos.=$add_comma.$row[csf("booking_no")].$add_comma.",";
		}
		else
		{
			$add_comma="'";
		  	$allBookingIds.=$add_comma.$row[csf("booking_id")].$add_comma.",";
			$allBookingNos.=$add_comma.$row[csf("booking_no")].$add_comma.",";
		}
		//$allBookingIds.=$row[csf("booking_id")].",";
	}
	if($rID)
	{
	    oci_commit($con);
	}
	//unset($sql_res);
	// print_r($buyer_wise_result);die();

	$allBookingIds=implode(",",array_unique(explode(",",$allBookingIds)));
	$allBookingIds=chop($allBookingIds,','); $allBookingIds_cond=""; $pi_booking_id_cond=""; $finprod_booking_id_cond=""; $fin_service_booking_id_cond=""; $grey_del_booking_id_cond=""; $grey_del_pi_wo_batch_no_cond = "";$for_plan_booking_id_cond="";$booking_id_cond="";
	if($db_type==2 && $tot_rows>1000)
	{
		$allBookingIds_cond=" and (";
		$pi_booking_id_cond=" and (";
		$finprod_booking_id_cond=" and (";
		$fin_service_booking_id_cond=" and (";
		$grey_del_booking_id_cond=" and (";
		$for_plan_booking_id_cond=" and (";
		$booking_id_cond=" and (";
		//$grey_del_pi_wo_batch_no_cond = " and (";
		
		$allBookingIdsArr=array_chunk(explode(",",$allBookingIds),999);
		foreach($allBookingIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$allBookingIds_cond.=" a.booking_no_id in($ids) or ";
			$pi_booking_id_cond.=" d.work_order_id in($ids) or ";
			$finprod_booking_id_cond.=" c.booking_no_id in($ids) or ";
			$fin_service_booking_id_cond.=" e.fab_booking_id in($ids) or ";
			$grey_del_booking_id_cond.=" a.booking_id in($ids) or ";
			$for_plan_booking_id_cond.=" e.id in($ids) or ";
			$booking_id_cond.=" c.order_id in($ids) or ";
			//$grey_del_pi_wo_batch_no_cond.=" a.booking_id in($ids) or ";
		}
		$allBookingIds_cond=chop($allBookingIds_cond,'or ');
		$allBookingIds_cond.=")";
		
		$pi_booking_id_cond=chop($pi_booking_id_cond,'or ');
		$pi_booking_id_cond.=")";
		
		$finprod_booking_id_cond=chop($finprod_booking_id_cond,'or ');
		$finprod_booking_id_cond.=")";

		$fin_service_booking_id_cond=chop($fin_service_booking_id_cond,'or ');
		$fin_service_booking_id_cond.=")";
		
		$grey_del_booking_id_cond=chop($grey_del_booking_id_cond,'or ');
		$grey_del_booking_id_cond.=")";

		$for_plan_booking_id_cond=chop($for_plan_booking_id_cond,'or ');
		$for_plan_booking_id_cond.=")";

		$booking_id_cond=chop($booking_id_cond,'or ');
		$booking_id_cond.=")";
		/*$grey_del_pi_wo_batch_no_cond=chop($grey_del_pi_wo_batch_no_cond,'or ');
		$grey_del_pi_wo_batch_no_cond.=")";*/
	}
	else
	{
		$allBookingIds_cond=" and a.booking_no_id in($allBookingIds)";
		$pi_booking_id_cond=" and d.work_order_id in($allBookingIds)";
		$finprod_booking_id_cond=" and c.booking_no_id in($allBookingIds)";
		$fin_service_booking_id_cond=" and e.fab_booking_id in($allBookingIds)";
		$grey_del_booking_id_cond=" and a.booking_id in($allBookingIds)";
		$for_plan_booking_id_cond=" and e.id in($allBookingIds)";
		$booking_id_cond=" and c.order_id in($allBookingIds)";
		//$grey_del_pi_wo_batch_no_cond=" and a.booking_id in($allBookingIds)";
	}
	$allBookingNos=implode(",",array_unique(explode(",",$allBookingNos)));
	$allBookingNos=chop($allBookingNos,',');
	if($db_type==2 && $tot_rows>1000)
	{
		$grey_del_pi_wo_batch_no_cond = " and (";
		
		$allBookingNosArr=array_chunk(explode(",",$allBookingNos),999);
		foreach($allBookingNosArr as $ids)
		{
			$ids=implode(",",$ids);
			$grey_del_pi_wo_batch_no_cond.=" a.booking_no in($ids) or ";
		}
		$grey_del_pi_wo_batch_no_cond=chop($grey_del_pi_wo_batch_no_cond,'or ');
		$grey_del_pi_wo_batch_no_cond.=")";
	}
	else
	{
		$grey_del_pi_wo_batch_no_cond=" and a.booking_no in($allBookingNos)";
	}
	//echo $allBookingIds_cond;die;
	// echo $program_no;

	$sql_yarn_issue=sql_select("SELECT a.booking_id,sum(b.cons_quantity) as issue_qty,min(a.issue_date) as issue_date from tmp_booking_id x, inv_issue_master a, inv_transaction b where x.booking_id=a.booking_id and x.userid=$user_name and x.type=1 and a.id=b.mst_id and a.issue_basis=1 and a.issue_purpose=8 and a.item_category=1 and b.transaction_type=2 and a.entry_form=3  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.booking_id>0  group by a.booking_id
	union all
	SELECT c.order_id as booking_id,sum(b.cons_quantity) as issue_qty,min(a.issue_date) as issue_date
	from inv_issue_master a, inv_transaction b, ppl_yarn_requisition_breakdown c, tmp_booking_id x  
	where a.id=b.mst_id and b.requisition_no=c.requisition_id and c.order_id=x.booking_id and x.userid=$user_name and x.type=1 and c.item_id=b.prod_id and a.issue_basis=3 and a.issue_purpose=1 and a.item_category=1 and b.transaction_type=2 and a.entry_form=3 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  group by c.order_id");

	//$grey_del_booking_id_cond
	//$booking_id_cond
	
	foreach($sql_yarn_issue as $row)
	{
		$yarn_issue_arr[$row[csf("booking_id")]]=$row[csf("issue_qty")];
		$yarn_issue_date[$row[csf("booking_id")]]=$row[csf("issue_date")];
	}

	$sql_yarn_issue_rtn=sql_select("SELECT a.booking_id,sum(b.cons_quantity) as issue_rtn_qty from tmp_booking_id x,inv_receive_master a, inv_transaction b where x.booking_id=a.booking_id and x.userid=$user_name and x.type=1 and a.id=b.mst_id and a.receive_basis=1 and a.item_category=1 and b.transaction_type=4 and a.entry_form=9 and a.booking_id>0 and a.booking_without_order=1  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.booking_id
	union all 
	SELECT c.order_id as booking_id,sum(b.cons_quantity) as issue_rtn_qty 
	from inv_receive_master a, inv_transaction b , ppl_yarn_requisition_breakdown c,tmp_booking_id x  
	where a.id=b.mst_id 
	and a.booking_id=c.requisition_id and c.item_id=b.prod_id and c.order_id=x.booking_id and x.userid=$user_name and x.type=1 
	and a.receive_basis=3 and a.item_category=1 and b.transaction_type=4 and a.entry_form=9 and a.booking_id>0 and a.booking_without_order=1  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
	group by c.order_id");

	//$grey_del_booking_id_cond
	//$booking_id_cond

	foreach($sql_yarn_issue_rtn as $row)
	{
		$yarn_issue_rtn_arr[$row[csf("booking_id")]]=$row[csf("issue_rtn_qty")];
	}


	ob_start();
	?>
	<table cellpadding="0" cellspacing="0" width="1900">
		<tr>
		   <td align="center" width="100%" colspan="20" ><strong style="font-size:16px"><? echo $company_library[$cbo_company_name]; ?></strong></td>
		</tr>
	</table>
    <?
	
	$fin_store_rec_purchase=array();
	$sql_finish_store_pi=sql_select("select d.work_order_id as booking_id, b.color_id, sum(b.receive_qnty ) as parcess_qty from  inv_receive_master a, pro_finish_fabric_rcv_dtls b, com_pi_master_details c, com_pi_item_details d, tmp_booking_id x where a.id=b.mst_id and a.booking_id=c.id and c.id=d.pi_id and d.work_order_id=x.booking_id and x.userid=$user_name and x.type=1 and a.entry_form=37 and a.receive_basis=1 and a.booking_id>0  group by d.work_order_id, b.color_id");
	//$pi_booking_id_cond
	foreach($sql_finish_store_pi as $row)
	{
		$fin_store_rec_purchase[$row[csf("booking_id")]][$row[csf("color_id")]]=$row[csf("parcess_qty")];
	}
	unset($sql_finish_store_pi);
	
	/*$sql_finish_store_prod=sql_select("select c.booking_no_id as booking_id, c.batch_against, a.receive_basis, a.entry_form, b.color_id, max(a.receive_date) as receive_date, sum(b.receive_qnty) as production_qty from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c where a.id=b.mst_id and b.batch_id=c.id and c.booking_without_order=1 and c.batch_against=3 and a.entry_form in (7,37) and c.booking_no_id>0 $finprod_booking_id_cond group by c.booking_no_id, b.color_id,  c.batch_against, a.receive_basis, a.entry_form");*/


	$sql_finish_store_prod=sql_select("SELECT c.booking_no_id as booking_id, c.batch_against, a.receive_basis, a.entry_form, b.color_id, max(a.receive_date) as receive_date, sum(b.receive_qnty) as production_qty from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c,tmp_booking_id x where a.id=b.mst_id and b.batch_id=c.id and c.booking_no_id=x.booking_id and x.userid=$user_name and x.type=1 and c.booking_without_order=1 and c.batch_against=3 and a.entry_form in (7,37) and c.booking_no_id>0 and a.receive_basis !=11 and a.status_active=1 and b.status_active=1  group by c.booking_no_id, b.color_id,  c.batch_against, a.receive_basis, a.entry_form union all SELECT f.id as booking_id, d.batch_against, a.receive_basis, a.entry_form, b.color_id, max(a.receive_date) as receive_date, sum(b.receive_qnty) as production_qty from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst d, wo_non_ord_knitdye_booking_mst e, wo_non_ord_samp_booking_mst f,tmp_booking_id x where a.id=b.mst_id and b.batch_id=d.id and d.booking_without_order=1 and a.entry_form in (37) and d.booking_no=e.booking_no and a.receive_basis=11  and e.fab_booking_id=f.id and f.id=x.booking_id and e.fab_booking_id=x.booking_id and x.userid=$user_name and x.type=1  and a.status_active=1 and b.status_active=1 group by f.id, b.color_id, d.batch_against, a.receive_basis, a.entry_form");
	//$finprod_booking_id_cond
	//$fin_service_booking_id_cond

	foreach($sql_finish_store_prod as $row)
	{
		if($row[csf("entry_form")]==37 && ($row[csf("receive_basis")]==9 || $row[csf("receive_basis")]==11) )
		{
			$fin_store_rec_prod[$row[csf("booking_id")]][$row[csf("color_id")]]+=$row[csf("production_qty")];
		}
		else if($row[csf("entry_form")]==7 && $row[csf("receive_basis")]==5)
		{
			$finishfab_product_arr[$row[csf("booking_id")]][$row[csf("color_id")]]+=$row[csf("production_qty")];
		}
		if($row[csf("entry_form")]==7)
		{
			$finish_prodction_date[$row[csf("booking_id")]]=$row[csf("receive_date")];
		}
		
	}
	unset($sql_finish_store_prod);	
	
	$sql_finish_prodction_delivery=sql_select("SELECT c.booking_no_id, max(a.delevery_date) as receive_date, sum(b.current_delivery) as delivery_qty,b.color_id 
	from pro_grey_prod_delivery_mst a,pro_grey_prod_delivery_dtls b, pro_batch_create_mst c,tmp_booking_id x  
	where a.id=b.mst_id and b.batch_id=c.id and c.booking_no_id=x.booking_id and x.userid=$user_name and x.type=1 and  a.entry_form=54  and b.entry_form=54 and c.batch_against=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by c.booking_no_id,b.color_id  ");
	//$finprod_booking_id_cond
	foreach($sql_finish_prodction_delivery as $row)
	{
		$finish_prodction_delivery_qty[$row[csf("booking_no_id")]][$row[csf("color_id")]]=$row[csf("delivery_qty")];
		$finish_prodction_delivery_date[$row[csf("booking_no_id")]]=$row[csf("receive_date")];
	}
	unset($sql_finish_prodction_delivery);
	//==============
	
	$sql_grey_knit_production=sql_select("SELECT a.booking_id, sum(b.grey_receive_qnty) as receive_qty, max(a.receive_date) as receive_date 
	from tmp_booking_id x, inv_receive_master a, pro_grey_prod_entry_dtls b 
	where x.booking_id=a.booking_id and x.userid=$user_name and x.type=1 and a.id=b.mst_id and a.receive_basis=1 and a.booking_without_order=1 and a.entry_form=2 and a.booking_id>0  group by a.booking_id
	union all 
	SELECT e.id as booking_id, sum(b.grey_receive_qnty) as receive_qty, max(a.receive_date) as receive_date 
	from inv_receive_master a, pro_grey_prod_entry_dtls b, ppl_planning_entry_plan_dtls c, wo_non_ord_samp_booking_mst e,tmp_booking_id x 
	where a.id=b.mst_id and a.booking_id=c.dtls_id and c.booking_no=e.booking_no and e.id=x.booking_id and x.userid=$user_name and x.type=1 
	and a.receive_basis=2 and a.booking_without_order=1 and a.entry_form=2 and a.booking_id>0 group by e.id ");
	//$grey_del_booking_id_cond
	//$for_plan_booking_id_cond

	$prod_booking_id_arr=array();
	foreach($sql_grey_knit_production as $row)
	{
		$grey_knit_production_arr[$row[csf("booking_id")]]+=$row[csf("receive_qty")];
		$grey_knit_production_date[$row[csf("booking_id")]]=$row[csf("receive_date")];
		//$prod_booking_id_arr[$row[csf("id")]]=$row[csf("booking_id")];
	}
		//print_r($prod_booking_id_arr);
	unset($sql_grey_knit_production);
	
	$sql_gray_delivery=sql_select("SELECT c.id as delivery_id, a.booking_id, max(c.delevery_date) as delevery_date, sum(b.current_delivery) as current_stock 
	from tmp_booking_id x, inv_receive_master a,  pro_grey_prod_delivery_dtls b, pro_grey_prod_delivery_mst c 
	where x.booking_id=a.booking_id and x.userid=$user_name and x.type=1 and c.id=b.mst_id and a.entry_form=2 and c.entry_form in(53,56) and b.grey_sys_id=a.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=1  group by a.booking_id, c.id 
	union all 
	SELECT c.id as delivery_id, e.id as booking_id, max(c.delevery_date) as delevery_date, sum(b.current_delivery) as current_stock 
	from inv_receive_master a, pro_grey_prod_delivery_dtls b, pro_grey_prod_delivery_mst c, ppl_planning_entry_plan_dtls d, wo_non_ord_samp_booking_mst e,tmp_booking_id x   
	where c.id=b.mst_id and a.entry_form=2 and c.entry_form in(56) and b.grey_sys_id=a.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
	and a.booking_id=d.dtls_id and d.booking_no=e.booking_no and e.id=x.booking_id and x.userid=$user_name and x.type=1 
	and a.booking_without_order=1 
	group by e.id, c.id");

	//$grey_del_booking_id_cond
	//$for_plan_booking_id_cond

	$grey_prod_booking_array=array();$all_delivery_id="";$delivery_book_id=array();
	foreach($sql_gray_delivery as $row)
	{
		$grey_delivery_date[$row[csf("booking_id")]]=$row[csf("delevery_date")];
		$grey_delivery_stock[$row[csf("booking_id")]]+=$row[csf("current_stock")];
		$delivery_book_id[$row[csf("delivery_id")]]=$row[csf("booking_id")];
		$all_delivery_id.=$row[csf("delivery_id")].",";
	}
	unset($sql_gray_delivery);
	$all_delivery_id=chop($all_delivery_id,",");
	
	$sql_grey_knit_receive=sql_select("SELECT a.booking_id,a.entry_form, ( case when a.receive_basis <>9 then b.cons_quantity else 0 end) as receive_qty,( case when a.receive_basis in(9) then b.cons_quantity else 0 end) as production_qty 
		from inv_receive_master a, inv_transaction b 
		where a.id=b.mst_id and a.booking_without_order=1 and a.entry_form in(2,22) and b.transaction_type in(1,4) and a.booking_id>0 ");
	foreach($sql_grey_knit_receive as $result)
	{
		//echo $prod_booking_id_arr[$result[csf("booking_id")]];
		$grey_knit_receive_arr[$result[csf("booking_id")]]+=$result[csf("receive_qty")];
		$grey_knit_receive_prod_arr[$result[csf("booking_id")]]+=$result[csf("production_qty")];
	}
	unset($sql_grey_knit_receive);
	$sql_grey_booking=sql_select("select a.id,a.booking_id from inv_receive_master a,tmp_booking_id x where a.booking_id=x.booking_id and x.userid=$user_name and x.type=1 and a.receive_basis=1 and a.booking_without_order=1 and a.entry_form=2 ");
	//$grey_del_booking_id_cond
	
	foreach($sql_grey_booking as $result)
	{
		$grey_knit_receive_booking[$result[csf("booking_id")]].=$result[csf("id")].",";
	}
	unset($sql_grey_booking);


	$sql = sql_select("select b.order_id, 
	sum( case when a.transfer_criteria in (7,8) and b.transaction_type=6 then b.cons_quantity else 0 end) as tranfer_out, 
	sum( case when a.transfer_criteria in(6,8) and b.transaction_type=5 then b.cons_quantity else 0 end) as tranfer_in 
	from inv_item_transfer_mst a, inv_transaction b where a.id=b.mst_id   and a.item_category=13 and b.item_category=13 and b.transaction_type in(5,6) 
	group by b.order_id order by b.order_id");

	foreach($sql as $result)
	{
		$sample_transectionArr[$result[csf("order_id")]]['tranfer_out'] = $result[csf("tranfer_out")];
		$sample_transectionArr[$result[csf("order_id")]]['tranfer_in'] = $result[csf("tranfer_in")];
	}
	
	unset($sql_grey_knit_receive);

	$sql_grey_issue=sql_select("select a.booking_id, sum(b.issue_qnty) as issue_qty, max(a.issue_date) as issue_date
	from tmp_booking_id x,inv_issue_master a, inv_grey_fabric_issue_dtls b where x.booking_no=a.booking_no and x.userid=$user_name and x.type=1 and a.id=b.mst_id and a.issue_basis in(0,1) and a.issue_purpose in(8,11) 
	and a.entry_form in(16,61)  group by a.booking_id order by a.booking_id");
	//$grey_del_pi_wo_batch_no_cond
	foreach($sql_grey_issue as $result)
	{
		$grey_issue_arr[$result[csf("booking_id")]]+=$result[csf("issue_qty")];
		$grey_issue_dat_arr[$result[csf("booking_id")]]=$result[csf("issue_date")];
	}
	//print_r($grey_issue_arr);	
	unset($sql_grey_issue);
	$sql_batch_qty=sql_select("select a.id, a.batch_no, a.booking_no_id, a.color_id, sum(b.batch_qnty) as batch_qnty, max(a.batch_date) as batch_date from tmp_booking_id x, pro_batch_create_mst a, pro_batch_create_dtls b where  x.booking_id=a.booking_no_id and x.userid=$user_name and x.type=1 and  a.id=b.mst_id and a.booking_without_order=1  group by a.id,a.batch_no,a.booking_no_id ,a.color_id ");
	//$allBookingIds_cond
	//echo $sql_batch_qty;die;
	foreach($sql_batch_qty as $row)
	{
		$batch_qty_arr[$row[csf("booking_no_id")]]['batch_qnty']+=$row[csf("batch_qnty")];
		$batch_qty_arr[$row[csf("booking_no_id")]]['batch_date']=$row[csf("batch_date")];
		$batch_qty_arr_check[$row[csf("booking_no_id")]][$row[csf("color_id")]]['batch_qnty']+=$row[csf("batch_qnty")];
		$batch_qty_arr_check[$row[csf("booking_no_id")]][$row[csf("color_id")]]['batch_id']=$row[csf("id")];
		$dyeing_qty_arr[$row[csf("id")]]=$row[csf("batch_qnty")];
	}
	unset($sql_batch_qty);
	//var_dump($finish_prodction_date);die;
	$sql_dyeing_qty=sql_select("select a.id, a.booking_no_id,a.color_id, max(a.batch_date) as  batch_date from tmp_booking_id x,pro_batch_create_mst a, pro_fab_subprocess c where x.booking_id=a.booking_no_id and x.userid=$user_name and x.type=1 and c.batch_id=a.id and a.booking_without_order=1 and c.load_unload_id=2  group by a.id, a.booking_no_id, a.color_id ");
	//$allBookingIds_cond
	
	foreach($sql_dyeing_qty as $row)
	{
		$dyeing_check_arr[$row[csf("booking_no_id")]][$row[csf("color_id")]]['batch_qnty']+=$dyeing_qty_arr[$row[csf("id")]];
	}
	unset($sql_dyeing_qty);
	
	$sql_finish_receive=sql_select("select a.booking_id, b.color_id,sum(b.receive_qnty) as receive_qty from tmp_booking_id x,inv_receive_master a, pro_finish_fabric_rcv_dtls b where x.booking_id=a.booking_id and x.userid=$user_name and x.type=1 and a.id=b.mst_id and a.receive_basis !=9 and a.booking_without_order=1 and a.entry_form=37 and a.status_active=1 and a.is_deleted=0  group by a.booking_id,b.color_id");
	//$grey_del_booking_id_cond
	
	foreach($sql_finish_receive as $row)
	{
		$finish_receive_arr[$row[csf("booking_id")]][$row[csf("color_id")]]+=$row[csf("receive_qty")];
	}
	unset($sql_finish_receive);
	/*$sql_cutting_issue=sql_select("select c.booking_no_id, c.color_id, sum(b.issue_qnty) as issue_qty from inv_issue_master a, inv_finish_fabric_issue_dtls b, pro_batch_create_mst c where a.id=b.mst_id and b.batch_id=c.id and a.entry_form=18 and c.booking_no_id>0 and c.booking_without_order=1 $finprod_booking_id_cond group by c.booking_no_id,c.color_id");*/

	$sql_cutting_issue=sql_select("SELECT c.booking_no_id, c.color_id, sum(b.issue_qnty) as issue_qty from inv_issue_master a, inv_finish_fabric_issue_dtls b, pro_batch_create_mst c, wo_non_ord_samp_booking_mst d,tmp_booking_id x where a.id=b.mst_id and b.batch_id=c.id and a.entry_form=18 and c.booking_no_id>0 and c.booking_without_order=1 and c.booking_no = d.booking_no and d.booking_no=x.booking_no and c.booking_no_id=x.booking_id and x.userid=$user_name and x.type=1 group by c.booking_no_id,c.color_id union all select  f.id as booking_id,  d.color_id,  sum(b.issue_qnty) as issue_qty from inv_issue_master a, inv_finish_fabric_issue_dtls b, pro_batch_create_mst d, wo_non_ord_knitdye_booking_mst e, wo_non_ord_samp_booking_mst f,tmp_booking_id x where a.id=b.mst_id and b.batch_id=d.id and d.booking_without_order=1 and a.entry_form in (18) and d.booking_no=e.booking_no and d.booking_no_id>0 and e.fab_booking_id=f.id and f.id=x.booking_id and e.fab_booking_id=x.booking_id and x.userid=$user_name and x.type=1 group by f.id, d.color_id");
	//$finprod_booking_id_cond 
	//$fin_service_booking_id_cond 

	foreach($sql_cutting_issue as $row)
	{
		$issue_to_cut_arr[$row[csf("booking_no_id")]][$row[csf("color_id")]]+=$row[csf("issue_qty")];
	}
	unset($sql_cutting_issue);
	$sql_style_reff=sql_select("select a.style_ref_no, a.id, a.item_name from sample_development_mst a where a.status_active=1 and a.is_deleted=0 ");
	foreach($sql_style_reff as $val)
	{
		$style_reff_arr[$val[csf("id")]]['style_reff_no']=$val[csf("style_ref_no")];
		$style_reff_arr[$val[csf("id")]]['item_name']=$val[csf("item_name")];
	}
	unset($sql_style_reff);
	
	$sql="select a.id,b.count_id from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.is_deleted=0 order by a.id";
	$data_array=sql_select($sql);
	$lib_yean_count_arr=array();
	$lib_yean_type_arr=array();
	if (count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$lib_yean_count_arr))
			{
				$lib_yean_count_arr[$row[csf('id')]]=$lib_yean_count_arr[$row[csf('id')]].", ".$yarn_count_details[$row[csf('count_id')]];
			}
			else
			{
				$lib_yean_count_arr[$row[csf('id')]]=$yarn_count_details[$row[csf('count_id')]];
			}
			if(array_key_exists($row[csf('id')],$lib_yean_type_arr))
			{
				$lib_yean_type_arr[$row[csf('id')]]=$lib_yean_type_arr[$row[csf('id')]].", ".$yarn_type[$row[csf('count_id')]];
			}
			else
			{
				$lib_yean_type_arr[$row[csf('id')]]=$yarn_type[$row[csf('count_id')]];
			}
		}
	}
	unset($data_array);
	//print_r($lib_yean_type_arr);


	foreach($result_mst_array as $bookingId=> $row)
	{
		$mm=1;
		foreach($result_dtls_array[$row[("booking_id")]] as $dts_row)
		{
			if($mm==1)
			{	
				$net_yarn_issue=$yarn_issue_arr[$row[("booking_id")]]-$yarn_issue_rtn_arr[$row[("booking_id")]];
				$buyer_wise_result[$row[("buyer_id")]]["yarn_issue"]+=$net_yarn_issue;
				$buyer_wise_result[$row[("buyer_id")]]["knitting_total"] +=($grey_knit_production_arr[$row[("booking_id")]]+$grey_knit_receive_arr[$row[("booking_id")]]);
				if ($booking_wise_roll_check[$bookingId]==1) // If production is roll lavel
				{
					$buyer_wise_result[$row[("buyer_id")]]["grey_issue"] +=$booking_wise_gray_data[$row[("booking_no")]]['gray_issue_qty'];
				}
				else
				{
					$buyer_wise_result[$row[("buyer_id")]]["grey_issue"] +=$grey_issue_arr[$row["booking_id"]];
				}
				$dtls_batch_qty=$batch_qty_arr_check[$row[("booking_id")]][$dts_row[("fabric_color")]]['batch_qnty'];
				$buyer_wise_result[$row[("buyer_id")]]["batch_qty"] +=$dtls_batch_qty;
				$dtls_dying_qty=$dyeing_check_arr[$row[("booking_id")]][$dts_row[("fabric_color")]]['batch_qnty'];
				$dtls_tot_dying_qty +=$dtls_dying_qty; 
				$buyer_wise_result[$row[("buyer_id")]]["dyeing_qty"] +=$dtls_dying_qty;
				$buyer_wise_result[$row["buyer_id"]]["finish_fabric_qty"] +=$dts_row[("finish_fabric_qty")];
				//if ($booking_wise_roll_check[$bookingId]==1)
				if($finish_roll_maintained==1)
				{
					$finish_prod_rece_store_availabe=$booking_wise_gray_data[$booking_no]['fin_feb_rcb_by_store_qty'];
				}
				else
				{
					$finish_prod_rece_store_availabe=$fin_store_rec_prod[$row[("booking_id")]][$dts_row[("fabric_color")]];
				}
				$finish_parchase_rece_store=$fin_store_rec_purchase[$row[("booking_id")]][$dts_row[("fabric_color")]];
				$fabric_store_available=$finish_prod_rece_store_availabe+$finish_parchase_rece_store;
				$buyer_wise_result[$row["buyer_id"]]["fin_total_available"] +=$fabric_store_available;
				//if ($booking_wise_roll_check[$bookingId]==1)
				if($finish_roll_maintained==1)
				{
					$buyer_wise_result[$row["buyer_id"]]["issue_to_cut"] +=$booking_wise_gray_data[$booking_no]['fin_feb_roll_issue_qty'];
				}
				else
				{
					$buyer_wise_result[$row["buyer_id"]]["issue_to_cut"] +=$issue_to_cut_arr[$row[("booking_id")]][$dts_row[("fabric_color")]];
				}     
			}
			else
			{

				$dtls_batch_qty=$batch_qty_arr_check[$row[("booking_id")]][$dts_row[("fabric_color")]]['batch_qnty'];
				$buyer_wise_result[$row[("buyer_id")]]["batch_qty"] +=$dtls_batch_qty;
				$dtls_dying_qty=$dyeing_check_arr[$row[("booking_id")]][$dts_row[("fabric_color")]]['batch_qnty'];
				$dtls_tot_dying_qty +=$dtls_dying_qty; 
				$buyer_wise_result[$row[("buyer_id")]]["dyeing_qty"] +=$dtls_dying_qty;
				$buyer_wise_result[$row["buyer_id"]]["finish_fabric_qty"] +=$dts_row[("finish_fabric_qty")];
				$finish_prod_rece_store=$fin_store_rec_prod[$row[("booking_id")]][$dts_row[("fabric_color")]];
				$finish_parchase_rece_store=$fin_store_rec_purchase[$row[("booking_id")]][$dts_row[("fabric_color")]];
				$fabric_store_available=$finish_prod_rece_store+$finish_parchase_rece_store;
				$buyer_wise_result[$row["buyer_id"]]["fin_total_available"] +=$fabric_store_available;
				$buyer_wise_result[$row["buyer_id"]]["issue_to_cut"] +=$issue_to_cut_arr[$row[("booking_id")]][$dts_row[("fabric_color")]];
			}
			$mm++;
		}
	}
	$gt_grey_available=0;
	$gt_dying_qty=0;
	$dtls_tot_dying_qty=0;

	foreach($sql_res as $row)
	{
		if ($booking_wise_roll_check[$row[csf("booking_id")]]==1)  
		{
			$issueQty = $booking_wise_gray_data[$row[csf("booking_id")]]['gray_issue_qty'];
		}
		else
		{
			$issueQty = $grey_issue_arr[$row[csf("booking_id")]]; 
		} 

		$net_transfer =0;
		if($sample_transectionArr[$row[csf("booking_id")]]['tranfer_in']>0 || $sample_transectionArr[$row[csf("booking_id")]]['tranfer_out']>0 )
		{
			$net_transfer = $sample_transectionArr[$row[csf("booking_id")]]['tranfer_in']-$sample_transectionArr[$row[csf("booking_id")]]['tranfer_out'];

		} 
		
		if(!in_array($row[csf("booking_id")],$temp_book_arr))
		{
			
			$temp_book_arr[$row[csf("booking_id")]]=$row[csf("booking_id")];			
			//$gt_yarn_issue+=$yarn_issue_arr[$row[csf("booking_id")]]-$yarn_issue_rtn_arr[$row[csf("booking_id")]];
			$gt_grey_available+=($grey_knit_production_arr[$row[csf("booking_id")]]+$grey_knit_receive_arr[$row[csf("booking_id")]]+$net_transfer)-$issueQty ;
			$gt_dying_qty+=$grey_delivery_stock[$row[csf("booking_id")]];
			$dtls_dying_qty=$dyeing_check_arr[$row[csf("booking_id")]][$dts_row[csf("fabric_color")]]['batch_qnty'];
			$dtls_tot_dying_qty +=$dtls_dying_qty; 


			//$gt_batch_qty +=$batch_qty_arr[$row[csf("booking_id")]]['batch_qnty']; 
		}		 
	}
	$r_id=execute_query("delete from tmp_booking_id where userid=$user_name");
    if($r_id) $flag=1; else $flag=0;
    if($flag==1)
    {
        oci_commit($con);
    }

	?>
	<!-- All Summary Start-->
	<div style="width:2250px; margin-bottom:10px;">
		<!-- Buyer Level Summary Start-->
	 	<div style="float:left;  margin-bottom:10px;">
		<table class="rpt_table" border="1" rules="all" width="1600" cellpadding="0" cellspacing="0">
			<thead>
	        	<tr>
					<th  colspan="16" align="center">Buyer Level Summary</th>
				</tr>
	        	<tr>
					<th width="40">SL</th>
	                <th width="80">Buyer Name</th>
					<th width="100">Grey Req.</th>
	                <th width="100">Yarn Issue</th>
	                <th width="100">Yarn Balance</th>
					<th width="100">Knitting Total</th>
					<th width="100">Knit Balance</th>
					<th width="100">Grey Issue</th>
					<th width="100">Batch Qnty</th>
					<th width="100">Batch Balance</th>
					<th width="100">Total Dyeing</th>
					<th width="100">Dyeing Balance</th>
					<th width="100">Fin. Fab Req.</th>
	                <th width="100">Fin. Fab total</th>
	                <th width="100">Fin. Fab Balance</th>
					<th>Issue to Cutting </th>
				</tr>
	        </thead>
	        <tbody>
	        <?
			$p=1;
			$gt_issue_cutting=0;
			$gt_finish_available=0;
			$gt_batch_qty=0; 
			$gt_yarn_issue=0; 
			$gt_dying_qty=0; 
			$dtls_tot_dying_qty=0; 
			foreach($buyer_wise_result as $row_result)
			{
				if ($p%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
	        	<tr bgcolor="<? echo $bgcolor; ?>">
					<td><? echo $p; ?></td>
	                <td><? echo $buyer_short_name_library[$row_result["buyer_id"]]; ?></td>
					<td align="right"><? echo number_format($row_result["grey_fabric_qnty"],2); $buyer_tot_feb_req+=$row_result["grey_fabric_qnty"];?></td>
	                <td align="right"><? echo number_format($row_result["yarn_issue"],2);  $buyer_tot_yarn_issue+=$row_result["yarn_issue"]; ?></td>
	                <td align="right"><? echo number_format(($row_result["grey_fabric_qnty"]-$row_result["yarn_issue"]),2); $buyer_tot_yarn_balance+=($row_result["grey_fabric_qnty"]-$row_result["yarn_issue"]); ?></td>
					<td align="right"><? echo number_format($row_result["knitting_total"],2); $buyer_tot_grey_knitting+=$row_result["knitting_total"]; ?></td>
					<td align="right"><? echo number_format(($row_result["grey_fabric_qnty"]-$row_result["knitting_total"]),2); $buyer_tot_grey_knitting_bal+=($row_result["grey_fabric_qnty"]-$row_result["knitting_total"]); ?></td>
					<td align="right"><? echo number_format($row_result["grey_issue"],2); $buyer_tot_grey_issue+=$row_result["grey_issue"];  ?></td>
					<td align="right"><? echo number_format($row_result["batch_qty"],2); $buyer_tot_batch_qty+=$row_result["batch_qty"]; ?></td>
					<td align="right"><? echo number_format(($row_result["grey_fabric_qnty"]-$row_result["batch_qty"]),2); $buyer_tot_batch_balance+=($row_result["grey_fabric_qnty"]-$row_result["batch_qty"]); ?></td>
					<td align="right"><? echo number_format($row_result["dyeing_qty"],2);  $buyer_tot_dyeing_qty+=$row_result["dyeing_qty"];  ?></td>
					

					<td align="right"><? echo number_format(($row_result["grey_fabric_qnty"]-$row_result["dyeing_qty"]),2); $buyer_tot_dyeing_balance+=($row_result["grey_fabric_qnty"]-$row_result["dyeing_qty"]); ?></td>




					<td align="right"><? echo number_format($row_result["finish_fabric_qty"],2);  $buyer_tot_finish_req_qty+=$row_result["finish_fabric_qty"];  ?></td>


	                <td align="right"><? echo number_format($row_result["fin_total_available"],2); $buyer_tot_finish_abable_qty+=$row_result["fin_total_available"];  ?></td>
	                <td align="right"><? echo number_format(($row_result["finish_fabric_qty"]-$row_result["fin_total_available"]),2); $buyer_tot_finish_balance+=($row_result["finish_fabric_qty"]-$row_result["fin_total_available"]);  ?></td>

					<td align="right"><? echo number_format($row_result["issue_to_cut"],2); $buyer_tot_cutting_qty+=$row_result["issue_to_cut"]; ?></td>
				</tr>
	            <?
	            $gt_issue_cutting+=$row_result["issue_to_cut"];
	            $gt_finish_available+=$row_result["fin_total_available"]; 
	            $gt_batch_qty+=$row_result["batch_qty"];
	            $gt_yarn_issue+=$row_result["yarn_issue"];
	            $gt_dying_qty+=$row_result["grey_issue"];
	            $dtls_tot_dying_qty+=$row_result["dyeing_qty"];
				$p++;
			}
			?>
	        </tbody>
	        <tfoot>
	        	<tr>
	                <th colspan="2">Total:</th>
					<th align="right"><? echo number_format($buyer_tot_feb_req,2); ?></th>
	                <th align="right"><? echo number_format($buyer_tot_yarn_issue,2); ?></th>
	                <th align="right"><? echo number_format($buyer_tot_yarn_balance,2); ?></th>
					<th align="right"><? echo number_format($buyer_tot_grey_knitting,2); ?></th>
					<th align="right"><? echo number_format($buyer_tot_grey_knitting_bal,2); ?></th>
					<th align="right"><? echo number_format($buyer_tot_grey_issue,2); ?></th>
					<th align="right"><? echo number_format($buyer_tot_batch_qty,2); ?></th>
					<th align="right"><? echo number_format($buyer_tot_batch_balance,2); ?></th>
					<th align="right"><? echo number_format($buyer_tot_dyeing_qty,2); ?></th>
					<th align="right"><? echo number_format($buyer_tot_dyeing_balance,2); ?></th>
					<th align="right"><? echo number_format($buyer_tot_finish_req_qty,2); ?></th>
	                <th align="right"><? echo number_format($buyer_tot_finish_abable_qty,2); ?></th>
	                <th align="right"><? echo number_format($buyer_tot_finish_balance,2); ?></th>
					<th align="right"><? echo number_format($buyer_tot_cutting_qty,2); ?> </th>
				</tr>
	        </tfoot>
	    </table>
	    </div>
	    <!-- Buyer Level Summary End-->
	    <!-- Summary Start -->
		<div style="float:left; width:320px;  margin-left:20px;">
		    <table class="rpt_table" border="1" rules="all" width="400" cellpadding="0" cellspacing="0">
		        <thead>
		        	<tr>
		            	<th colspan="4">Summary</th>
		            </tr>
		            <tr>
		            	<th width="30">Sl</th>
		            	<th width="200">Particulars</th>
		                <th width="80">Quantity</th>
		                <th width="70">%</th>
		            </tr>
		        </thead>
		        <tbody>
		        	<tr>
		            	<td>1</td>
		            	<td>Total Yarn Required</td>
		                <td align="right"><? echo number_format($gt_yarn_grey_required,2); ?></td>
		                <td></td>
		            </tr>
		            <tr>
		            	<td>2</td>
		            	<td>Total Yarn Issued</td>
		                <td align="right"><? echo number_format($gt_yarn_issue,2); ?></td>
		                <td align="right"><? $yarn_issue_parcent=(($gt_yarn_issue/$gt_yarn_grey_required)*100); echo number_format($yarn_issue_parcent,2)."%"; ?></td>
		            </tr>
		            <tr>
		            	<td>3</td>
		            	<td ><strong> Total Issue Balance</strong></td>
		                <td align="right"><? $gt_issue_balance=$gt_yarn_grey_required-$gt_yarn_issue; echo number_format($gt_issue_balance,2); ?></td>
		                <td align="right"><? $issue_balance_parcentage=(($gt_issue_balance/$gt_yarn_grey_required)*100); echo number_format($issue_balance_parcentage,2)."%";  ?></td>
		            </tr>
		            <tr>
		            	<td>4</td>
		            	<td>Total Grey Fabric Required</td>
		                <td align="right"><? echo number_format($gt_yarn_grey_required,2); ?></td>
		                <td></td>
		            </tr>
		            <tr>
		            	<td>5</td>
		            	<td>Total Grey Fabric Available</td>
		                <td align="right"><? echo number_format($gt_grey_available,2); ?></td>
		                <td align="right"><? $grey_available_parcentage=(($gt_grey_available/$gt_yarn_grey_required)*100); echo number_format($grey_available_parcentage,2)."%";  ?></td>
		            </tr>
		            <tr>
		            	<td>6</td>
		            	<td>Total Grey Fabric Issued To Dye</td>
		                <td align="right"><? echo number_format($gt_dying_qty,2); ?></td>
		                <td align="right"><? $grey_dying_parcentage=(($gt_dying_qty/$gt_yarn_grey_required)*100); echo number_format($grey_dying_parcentage,2)."%";  ?></td>
		            </tr>
		         
		            
		            <tr>
		            	<td>7</td>
		            	<td><strong>Total Grey Fabric Issued Balance</strong></td>
		                <td align="right"><? $gt_grey_balance=$gt_yarn_grey_required-$gt_grey_available;  echo number_format($gt_grey_balance,2); ?></td>
		                <td align="right"><? $grey_balance_parcentage=(($gt_grey_balance/$gt_yarn_grey_required)*100); echo number_format($grey_balance_parcentage,2)."%";  ?></td>
		            </tr>
		           
		            <tr>
		            	<td>8</td>
		            	<td>Total Batch Qty.</td>
		                <td align="right"><? echo number_format($gt_batch_qty,2); ?></td>
		                <td align="right"></td>
		            </tr>
		             <tr>
		            	<td>9</td>
		            	<td><strong>Total Batch Balance To Grey</strong></td>
		                <td align="right"><? $total_batch_balance=$gt_yarn_grey_required-$gt_batch_qty; echo number_format($total_batch_balance,2); ?></td>
		                <td align="right"><? //$grey_batch_balance_parcentage=(($total_batch_balance/$gt_yarn_grey_required)*100); echo number_format($total_batch_balance,2)."%";  ?></td>
		            </tr>
		            <tr>
		            	<td>10</td>
		            	<td>Total Dyeing Qty</td>
		                <td align="right"><? echo number_format($dtls_tot_dying_qty,2); ?></td>
		                <td align="right"><? $grey_dying_parcentage=(($gt_dying_qty/$gt_yarn_grey_required)*100); echo number_format($grey_dying_parcentage,2)."%";  ?></td>
		            </tr>
		            <tr>
		            	<td>11</td>
		            	<td><strong>Total Dye Balance To Grey</strong></td>
		                <td align="right"><? $total_dying_balance=$gt_yarn_grey_required-$dtls_tot_dying_qty; echo number_format($total_batch_balance,2); ?></td>
		                <td align="right"><? $grey_dying_balance_parcentage=(($total_dying_balance/$gt_yarn_grey_required)*100); echo number_format($total_batch_balance,2)."%";  ?></td>
		            </tr>
		            <tr>
		            	<td>12</td>
		            	<td>Total Finish Fabric Required</td>
		                <td align="right"><? echo number_format($gt_finish_requir,2); ?></td>
		                <td></td>
		            </tr>
		            <tr>
		            	<td>13</td>
		            	<td>Total Finish Fabric Available</td>
		                <td align="right"><? echo number_format($gt_finish_available,2); ?></td>
		                <td align="right"><? $finish_available_parcentage=(($gt_finish_available/$gt_finish_requir)*100); echo number_format($finish_available_parcentage,2)."%";  ?></td>
		            </tr>
		            <tr>
		            	<td>14</td>
		            	<td><strong>Total Finish Fabric Balance</strong></td>
		                <td align="right"><? $gt_finish_balance=$gt_finish_requir-$gt_finish_available;  echo number_format($gt_finish_balance,2); ?></td>
		                <td align="right"><? $finish_balance_parcentage=(($gt_finish_balance/$gt_finish_requir)*100); echo number_format($finish_balance_parcentage,2)."%";  ?></td>
		            </tr>
		            <tr>
		            	<td>15</td>
		            	<td>Total Issue to Cutting</td>
		                <td align="right"><? echo number_format($gt_issue_cutting,2); ?></td>
		                <td align="right"><? $finish_issue_cutting_parcentage=(($gt_issue_cutting/$gt_finish_requir)*100); echo number_format($finish_issue_cutting_parcentage,2)."%";  ?></td>
		            </tr>
		            <tr>
		            	<td>16</td>
		            	<td><strong>Total Issue Balance</strong></td>
		                <td align="right"><? $gt_finish_issue_cut_balance=$gt_finish_requir-$gt_issue_cutting;  echo number_format($gt_finish_issue_cut_balance,2); ?></td>
		                <td align="right"><? $finish_issue_cut_bal_parcentage=(($gt_finish_issue_cut_balance/$gt_finish_requir)*100); echo number_format($finish_issue_cut_bal_parcentage,2)."%";  ?></td>
		            </tr>
		        </tbody>
		    </table>
	    </div>
	    <!-- Summary End -->
	</div>
	<!-- All Summary End-->

	<!-- Booking Details Start -->
	<table class="rpt_table" border="1" rules="all" width="4950" cellpadding="0" cellspacing="0">
		<thead>
			<tr>
				<th colspan="16">Booking Details</th>
				<th colspan="5">Yarn Details</th>
                <th colspan="4">Knitting Production</th>
				<th colspan="6">Grey Fabric Store</th>
                <th colspan="4">Dyeing Production</th>
				<th colspan="5">Finish Fabric Production</th>
                <th colspan="7">Finish Fabric Store</th>
                <th rowspan="2">Fabric Description</th>
			</tr>
			<tr>
				<th width="40">SL</th>
				<th width="65">Booking Year</th>
                <th width="65">Booking No</th>
				
                <th width="80">Buyer Name</th>
                <th width="80">Style Ref.</th>
                <th width="80">Internal Ref.</th>
                <th width="80">Item Name</th>
                <th width="80">W/O Booking Date<br/><font style="font-size:9px; font-weight:100">Days Count</font></th>
                <th width="80">W/O Delivery Date<br/><font style="font-size:9px; font-weight:100">(Days Count)</font></th>
                <th width="90">Yarn Delivey Date<br/><font style="font-size:9px; font-weight:100">Days Count</font></th>
                <th width="100">Knitting Production Date<br/><font style="font-size:9px; font-weight:100">(Days Count)</font></th>
                <th width="100">Knitting Delivery Date<br/><font style="font-size:9px; font-weight:100">(Days Count)</font></th>
                <th width="100">Batch Date<br/><font style="font-size:9px; font-weight:100">(Days Count)</font></th>
                <th width="100">Finished Fabric Production Date<br/><font style="font-size:9px; font-weight:100">(Days Count)</font></th>
                <th width="100">Finished Fabric Delivery Date<br/><font style="font-size:9px; font-weight:100">(Days Count)</font></th>
                
                <th width="80">Count</th>
                <th width="100">Composition</th>
                <th width="80">Type</th>
				<th width="110">Required<br/><font style="font-size:9px; font-weight:100">(As Per Booking)</font></th>
                <th width="110">Issued</th>
                <th width="110">Issue Balance<br/><font style="font-size:9px; font-weight:100">Grey Req-Yarn Issue</font></th>
				<th width="110">Knitted Production</th>
                <th width="110">Knit Balance</th>
                <th width="110">Grey Fab Delv. To Store</th>
                <th width="110">Grey in Knitting Floor</th>
				
                <th width="110">Grey Rcvd Prod.</th>
                <th width="110">Grey Rcvd - Purchase</th>
                <th width="110">Net Transfer</th>
                <th width="110">Fabric Available</th>
                <th width="110">Receive Balance</th>
				<th width="110">Grey Issue</th>
                
                <th width="110">Fabric Color</th>
				<th width="110">Batch Qnty</th>
				<th width="110">Dye Qnty</th>
                <th width="110">Balance</th>
				<th width="110">Required Qty (As per Booking)</th>
				
				<th width="110">Production Qty.</th>
                <th width="110">Balance Qty</th>
                <th width="110">Finish Fab. Delv. To Store</th>
                <th width="110">Fabric in Prod. Floor</th>
                
                <th width="110">Received - Prod.</th>
                <th width="110">Received - Purchase</th>
                <th width="110">Fabric Available</th>
                
                <th width="110">Receive Balance</th>
                <th width="110">Issue to Cutting</th>
                <th width="110">Yet to Issue</th>
				<th width="110">Fabric Stock/ Left Over</th>
			</tr>
		</thead>
	</table>
	<div style="width:4975px; overflow-y:scroll; max-height:300px" id="scroll_body">
    <table class="rpt_table" border="1" rules="all" width="4950" cellpadding="0" cellspacing="0" id="table_body">
        <tbody>
        <?
			$print_report_format=0;
			$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$cbo_company_name."' and module_id=2 and report_id=4 and is_deleted=0 and status_active=1");	//Sample Fabric Booking -Without order		
			$report_format_ids=explode(",",$print_report_format);
			
		$i=1;
		foreach($result_mst_array as $bookingID => $row)
		{
			// ========= report setting wise action==================
					if($report_format_ids[0]==34)
					{
						$action="show_fabric_booking_report";
					}else if($report_format_ids[0]==34){
						$action="show_fabric_booking_report2";
					}else if($report_format_ids[0]==36){
						$action="show_fabric_booking_report3";
					}else if($report_format_ids[0]==37){
						$action="show_fabric_booking_report4";
					}else if($report_format_ids[0]==64){
						$action="show_fabric_booking_report5";
					}else if($report_format_ids[0]==72){
						$action="show_fabric_booking_report6";
					}else if($report_format_ids[0]==174){
						$action="show_fabric_booking_report7";
					}


			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
        	<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
				<td width="40"><? echo $i; ?></td>
				<td width="65" align="center"><p><? echo $row[("wo_year")]; ?></p></td>
                <td width="65" align="center"><p><a href='##' style='color:#000' onclick="generate_order_report('<? echo $row['booking_no'];?> ','<? echo $row['company_id']; ?>','<? echo $row['is_approved'];?>','<? echo $action;?>')"><? echo $row[("booking_no_prefix_num")]; ?></a></p></td>
			
                <td width="80"><p><? echo $buyer_short_name_library[$row[("buyer_id")]]; ?></p></td>
                <td width="80" style="word-break:break-all"><? if($row[("style_id")]!="") echo $style_reff_arr[$row[("style_id")]]['style_reff_no']; ?></td>
                <td width="80" style="word-break:break-all"><? echo $row[("internal_ref")]; ?></td>
                <td width="80"><p><? if($row[("style_id")]!="") echo $garments_item[$style_reff_arr[$row[("style_id")]]['item_name']]; ?></p></td>
                <td width="80" align="center"><p><? echo  change_date_format($row["booking_date"]); ?></p></td>
                <td width="80" align="center"><p><? echo  change_date_format($row["delivery_date"]); ?></p></td>
                
                <td width="90" align="center"><p><? if($yarn_issue_date[$row["booking_id"]]!=""){ $days_yarn_issue=datediff("d",$row["booking_date"],$yarn_issue_date[$row["booking_id"]]); echo change_date_format($yarn_issue_date[$row["booking_id"]])."<br/>"; echo $days_yarn_issue." days";}// echo $yarn_issue_date[$row["booking_id"]]; ?></p></td>
                <td width="100" align="center"><p>
				<? if($grey_knit_production_date[$row["booking_id"]]!=""){ $knit_production_date=datediff("d",$row["booking_date"],$grey_knit_production_date[$row["booking_id"]]); echo change_date_format($grey_knit_production_date[$row["booking_id"]])."<br/>";  echo $knit_production_date." days";} 
				?></p></td>
                <td width="100" align="center"><p><? if($grey_delivery_date[$row["booking_id"]]!=""){ $days_grey_delivery=datediff("d",$row["booking_date"],$grey_delivery_date[$row["booking_id"]]); echo change_date_format($grey_delivery_date[$row["booking_id"]])."<br/>"; echo $days_grey_delivery." days";} ?></p></td>
                <td width="100" align="center"><p><? if($batch_qty_arr[$row["booking_id"]]['batch_date']!=""){ $days_batch=datediff("d",$row["booking_date"],$batch_qty_arr[$row["booking_id"]]['batch_date']); echo change_date_format($batch_qty_arr[$row["booking_id"]]['batch_date'])."<br/>"; echo $days_batch." days";} ?></p></td> 
                <td width="100" align="center"><p><? if($finish_prodction_date[$row["booking_id"]]!=""){ $date_finish_prodction=datediff("d",$row["booking_date"],$finish_prodction_date[$row["booking_id"]]); echo change_date_format($finish_prodction_date[$row["booking_id"]])."<br/>"; echo $date_finish_prodction." days";} ?></p></td>
                <td width="100" align="center"><p><? if($finish_prodction_delivery_date[$row["booking_id"]]!=""){ $date_finish_prodctiondelivery=datediff("d",$row["booking_date"],$finish_prodction_delivery_date[$row["booking_id"]]); echo change_date_format($finish_prodction_delivery_date[$row["booking_id"]])."<br/>"; echo $date_finish_prodctiondelivery." days";} ?></p></td>
				
                <?
				//details_part start here
				$m=1;
				//$dtls_tot_gery_available_all = 0;
				//print_r($result_dtls_array[$row[("booking_id")]]);
				foreach($result_dtls_array[$row[("booking_id")]] as $dts_row)
				{
					if($m==1)
					{
						?>
                        <td width="80"><p><? echo $lib_yean_count_arr[$dts_row[("lib_yarn_count_deter_id")]]; ?></p></td>
                        <td width="100"><p><? echo $dts_row[("composition")]; ?></p></td>
                        <td width="80"><p><?   echo $lib_yean_type_arr[$dts_row[("lib_yarn_count_deter_id")]]; ?></p></td>
                        <td width="110" align="right"><p><? echo number_format($row[("grey_fabric_qnty")],2); $dtls_tot_gery_req+=$row[("grey_fabric_qnty")]; ?></p></td>

                        
                        <td width="110" align="right"><p><a  href="##" onclick="openmypage('<? echo $row[("booking_id")]; ?>','yarn_issue','')">
						<?
						$net_yarn_issue=$yarn_issue_arr[$row[("booking_id")]]-$yarn_issue_rtn_arr[$row[("booking_id")]];
						echo number_format($net_yarn_issue,2); $dtls_tot_yarn_issue+=$net_yarn_issue; $buyer_wise_result[$row[("buyer_id")]]["yarn_issue"]+=$net_yarn_issue;
                        ?></a></p></td>


                        <td width="110" align="right"><p><? $yarn_balance=$row[("grey_fabric_qnty")]-$net_yarn_issue; echo number_format($yarn_balance,2); $dtls_tot_yarn_balance += $yarn_balance; ?></p></td>

                        <td width="110" align="right" title="<? echo $row[("booking_id")]; ?>"><p>
                        <a  href="##" onclick="openmypage('<? echo $row[("booking_id")]; ?>','grey_receive','')">
                            <?
							$buyer_wise_result[$row[("buyer_id")]]["knitting_total"] +=($grey_knit_production_arr[$row[("booking_id")]]+$grey_knit_receive_arr[$row[("booking_id")]]);
                                echo number_format($grey_knit_production_arr[$row[("booking_id")]],2);
								$dtls_tot_gery_knit_product+=$grey_knit_production_arr[$row[("booking_id")]]; 
                            ?>
                        </a>
                        </p></td>


                        <td width="110" align="right"><p><? 
						  $grey_total_available=($grey_knit_production_arr[$row[("booking_id")]]+$grey_knit_receive_arr[$row[("booking_id")]]); 
						  $grey_balance=$row[("grey_fabric_qnty")]-$grey_total_available; echo number_format($grey_balance,2);
						  $dtls_tot_gray_bal +=$grey_balance;
						?></p></td>
	                    <td width="110" align="right"><p>
							<a  href="##" onclick="openmypage('<? echo $row[("booking_id")]; ?>','grey_fabric_delivery_to_store','')">
							<? echo number_format($grey_delivery_stock[$row[("booking_id")]],2);
							 $dtls_tot_gery_delivery+=$grey_delivery_stock[$row[("booking_id")]];  ?>
	                        </a>
	                        </p>
	                    </td>
	                    <td width="110" align="right"><p><? echo number_format($grey_knit_production_arr[$row[("booking_id")]],2);  ?></p></td>
	                        
	                    <td width="110" align="right"><p>
                    	<?  //echo $prog_booking_id_array[$row[("booking_id")]];
                    		$booking_nos=str_replace("'","",$booking_no);
							if ($booking_wise_roll_check[$row[("booking_id")]]==1)
                    		{
                    			//$id=$booking_wise_gray_data[$booking_no]['barcodes'];
								$id=rtrim($booking_wise_gray_data[$booking_nos]['barcodes'],',');
                    		}
                    		else
                    		{
                    			$id=$row[("booking_id")]."_withoutRoll";
                    		}
                    	?>
						<a  href="##" onclick="openmypage('<? echo $id; ?>','grey_receive_prod','')">
						<?
						//echo $booking_wise_roll_check[$row[("booking_no")]];	 
						if ($booking_wise_roll_check[$row[("booking_id")]]==1) // If production is roll lavel
						{
							//echo "Grey Rcvd Prod";
							echo number_format($booking_wise_gray_data[$row[("booking_no")]]['gray_rcv_qty'],2);
							
							$dtls_tot_grey_knit_receive_prod+=$booking_wise_gray_data[$row[("booking_no")]]['gray_rcv_qty'];
						}
						else
						{
							$all_receive_id="";
							//print_r($grey_knit_receive_booking[$row[("booking_id")]]);
							$all_receive_id=array_filter(array_unique(explode(",",$grey_knit_receive_booking[$row[("booking_id")]])));
							//print_r($all_receive_id);
							$all_receive_value=0;
							foreach($all_receive_id as $row_id)
							{
								//echo $row_id;
								$all_receive_value+=$grey_knit_receive_prod_arr[$row_id];
							}
							echo number_format($all_receive_value,2);
							$dtls_tot_grey_knit_receive_prod+=$all_receive_value;
						}
							
						?>
                        </a></p>
	                    </td>
	                    <td width="110" align="right"><p><?  //echo number_format($grey_knit_production_arr[$row[("booking_id")]],2);  ?></p>
	                    </td>
	                    <td width="110" align="right" title="<? echo $row[("booking_id")]; ?>"><p><? $net_transfer =0;
						if($sample_transectionArr[$row[("booking_id")]]['tranfer_in']>0 || $sample_transectionArr[$row[("booking_id")]]['tranfer_out']>0 )
						{
							$net_transfer = $sample_transectionArr[$row[("booking_id")]]['tranfer_in']-$sample_transectionArr[$row[("booking_id")]]['tranfer_out'];
							$dtls_tot_net_transfer+= $sample_transectionArr[$row[("booking_id")]]['tranfer_out']-$sample_transectionArr[$row[("booking_id")]]['tranfer_in'] ;		
						}
						?>
						<a href="##" onclick="opengreyNetTransfer('<? echo $row["booking_id"]; ?>','<? echo $cbo_company_name; ?>','grey_fabric_transfer_transection')"><? echo $net_transfer;?></a>
	                    </p>
						</td>
	                    <td width="110" align="right" title="(Prod+Rec+Trans)-Issue">
	                    	<p>
							<?
							
							if ($booking_wise_roll_check[$row[("booking_id")]]==1) // If production is roll lavel
							{
								$issueQty = $booking_wise_gray_data[$row[("booking_id")]]['gray_issue_qty'];
							}
							else
							{
								$issueQty = $grey_issue_arr[$row[("booking_id")]]; 
							} 
							
							$gray_total_available_all = ($grey_knit_production_arr[$row[("booking_id")]]+$grey_knit_receive_arr[$row[("booking_id")]]+$net_transfer)-$issueQty; // $booking_wise_gray_data[$row[("booking_no")]]['gray_rcv_qty']
						    
							echo number_format($gray_total_available_all,2);  
							$dtls_tot_gray_available_all += $gray_total_available_all;  
							?>
	                        </p>
						</td>
	                    <td width="110" align="right">
	                    <p><? $grey_balance=$row[("grey_fabric_qnty")]-$grey_total_available; 
						echo number_format($grey_balance,2); 
						$dtls_tot_gray_balance +=$grey_balance; ?>
	                    </p>
	                    </td>
	                        
	                    <td width="110" align="right"><p>
	                    	<?
	                    		if ($booking_wise_roll_check[$row[("booking_id")]]==1)
	                    		{
	                    			$id==rtrim($booking_wise_gray_data[$row[("booking_no")]]['barcodes'],',');
	                    		}
	                    		else
	                    		{
	                    			$id=$row[("booking_id")]."_withoutRoll";
	                    		}
	                    	?>
	                        <a  href="##" onclick="openmypage('<? echo $id; ?>','grey_issue','')">
							<? 
								//echo $booking_wise_roll_check[$row[("booking_no")]];
	                            if ($booking_wise_roll_check[$row[("booking_id")]]==1) // If production is roll lavel
	                            {
	                                echo number_format($booking_wise_gray_data[$row[("booking_no")]]['gray_issue_qty'],2);
	                                $dtls_tot_gray_issue+=$booking_wise_gray_data[$row[("booking_no")]]['gray_issue_qty'];
									$buyer_wise_result[$row[("buyer_id")]]["grey_issue"] +=$booking_wise_gray_data[$row[("booking_no")]]['gray_issue_qty'];
	                            }
	                            else
	                            {
	                                echo number_format($grey_issue_arr[$row["booking_id"]],2); 
	                                $dtls_tot_gray_issue+=$grey_issue_arr[$row["booking_id"]]; 
									$buyer_wise_result[$row[("buyer_id")]]["grey_issue"] +=$grey_issue_arr[$row["booking_id"]];
	                            } 
	                        ?>
	                        </a>
	                        </p>
	                    </td>
	                        <td width="110"><p><? echo $color_array[$dts_row[("fabric_color")]]; ?></p> </td>
							<td width="110" align="right" title="Entry Form=<? echo $row[("entry_form_id")].' Color Id='.$dts_row[("fabric_color")];?>">
								<p>
								 <a  href="##" onclick="open_febric_receive_status_color_wise_popup('<? echo $row[("booking_id")]; ?>','batch_qty_popup','<? echo $dts_row[("fabric_color")]; ?>')">
								<?
								$dtls_batch_qty=$batch_qty_arr_check[$row[("booking_id")]][$dts_row[("fabric_color")]]['batch_qnty'];
								$dy_check_id=$dyeing_check_arr[$batch_qty_arr_check[$row[("booking_id")]][$dts_row[("fabric_color")]]['batch_id']];
								echo number_format($dtls_batch_qty,2); $dtls_tot_batch_qty +=$dtls_batch_qty; 
								$buyer_wise_result[$row[("buyer_id")]]["batch_qty"] +=$dtls_batch_qty;
								 ?>
		                         </a></p>
		                     </td>
	                        <td width="110" align="right">
		                        <a  href="##" onclick="openmypage('<? echo $row[("booking_id")]; ?>','dying_qty_popup','')">
								<?
								$dtls_dying_qty=$dyeing_check_arr[$row[("booking_id")]][$dts_row[("fabric_color")]]['batch_qnty'];
		                        echo number_format($dtls_dying_qty,2);
								$dtls_tot_dying_qty +=$dtls_dying_qty; 
								$buyer_wise_result[$row[("buyer_id")]]["dyeing_qty"] +=$dtls_dying_qty;

		                        ?>
		                        </a>
	                        </td>
							<td width="110" align="right"><p>
		                        <?
								$dying_balance=$dtls_batch_qty-$dtls_dying_qty; $dtls_tot_dying_balance+=$dying_balance;
								echo number_format($dying_balance,2);  $total_dying_balance_qty +=$dying_balance;  

								?>
		                        </p>
	                    	</td>
	                        <td width="110" align="right"><p><? echo number_format($dts_row[("finish_fabric_qty")],2); $dtls_tot_fin_req_qty +=$dts_row[("finish_fabric_qty")];

	                        $buyer_wise_result[$row["buyer_id"]]["finish_fabric_qty"] +=$dts_row[("finish_fabric_qty")];
	                         ?></p></td>
	                        
							<td width="110" align="right"><p>
								<?
									//if ($booking_wise_roll_check[$row[("booking_id")]]==1)
									if($finish_roll_maintained==1)
		                    		{
		                    			$id=chop($booking_wise_gray_data[$booking_nos]['barcodes'],",");
		                    		}
		                    		else
		                    		{
		                    			$id=$row[("booking_id")]."_withoutRoll";
		                    		}
								?>
		                        <a  href="##" onclick="open_febric_receive_status_color_wise_popup('<? echo $id; ?>','finish_feb_pord','<? echo $dts_row[("fabric_color")]; ?>')">
								<? 
									//if ($booking_wise_roll_check[$row[("booking_id")]]==1) //if production is roll level
									if($finish_roll_maintained==1)
									{
										// echo $row[("booking_no")];
										echo number_format($booking_wise_gray_data[$booking_no]['gray_fin_feb_prod_qty'],2);
										$dtls_tot_fin_prod_qnty +=$booking_wise_gray_data[$booking_no]['gray_fin_feb_prod_qty'];
										//$buyer_wise_result[$row["buyer_id"]]["finish_fabric_qty"] +=$booking_wise_gray_data[$row[("booking_no")]]['gray_fin_feb_prod_qty'];
									}
									else
									{
										echo number_format($finishfab_product_arr[$row[("booking_id")]][$dts_row[("fabric_color")]],2);
										$dtls_tot_fin_prod_qnty +=$finishfab_product_arr[$row[("booking_id")]][$dts_row[("fabric_color")]]; 
										//$buyer_wise_result[$row["buyer_id"]]["finish_fabric_qty"] +=$finishfab_product_arr[$row[("booking_id")]][$dts_row[("fabric_color")]];
									} 
								?>
		                        </a>
		                        </p>
	                    	</td>
	                        <td width="110" align="right"><p><? 
							 $finish_balance=$dts_row[("finish_fabric_qty")]-$finishfab_product_arr[$row[("booking_id")]][$dts_row[("fabric_color")]]; echo number_format($finish_balance,2); $tot_fin_balance +=$finish_balance; //$dtls_tot_fin_balance +=$finish_balance;  ?></p></td>
							<td width="110" align="right"><p>
								<?
									//if ($booking_wise_roll_check[$row[("booking_id")]]==1)
									if($finish_roll_maintained==1)
		                    		{
		                    			$id=chop($booking_wise_gray_data[$booking_nos]['barcodes'],",");
		                    		}
		                    		else
		                    		{
		                    			$id=$row[("booking_id")]."_withoutRoll";
		                    		}
								?>
								<a href="##" onclick="openmypage('<? echo $id."__".$dts_row[("fabric_color")]; ?>','finish_fabric_delivery_to_store','')">
								<? 
									//if ($booking_wise_roll_check[$row[("booking_id")]]==1)
									if($finish_roll_maintained==1)
									{
										echo number_format($booking_wise_gray_data[$booking_no]['fin_feb_delivery_store_qty'],2);
										$dtls_tot_fin_delivery_qty +=$booking_wise_gray_data[$booking_no]['fin_feb_delivery_store_qty'];
									}
									else
									{
									 	echo number_format($finish_prodction_delivery_qty[$row[("booking_id")]][$dts_row[("fabric_color")]],2);
									 	$dtls_tot_fin_delivery_qty +=$finish_prodction_delivery_qty[$row[("booking_id")]][$dts_row[("fabric_color")]]; 
									}
								?>
		                        </a>
		                         </p>
		                     </td>
							
	                        <td width="110" align="right"><p><? 
								$fabric_in_prod_floor=$finishfab_product_arr[$row[("booking_id")]][$dts_row[("fabric_color")]]-$finish_prodction_delivery_qty[$row[("booking_id")]][$dts_row[("fabric_color")]];
								 echo number_format($fabric_in_prod_floor,2); $dtls_tot_fabric_in_prod_floor +=$fabric_in_prod_floor;  ?></p>
							</td>

		                    <td width="110" align="right"><p>
		                    	<?
		                    		//if ($booking_wise_roll_check[$row[("booking_id")]]==1)
									if($finish_roll_maintained==1)
									{
										$id=chop($booking_wise_gray_data[$booking_nos]['barcodes'],",");
									}
									else
									{
										$id=$row[("booking_id")]."_withoutRoll";
									}
		                    	?>
								<a  href="##" onclick="open_febric_receive_status_color_wise_popup('<? echo $id; ?>','finish_fabric_receive_by_store','<? echo $dts_row[("fabric_color")]; ?>')">
								<?
								//if ($booking_wise_roll_check[$row[("booking_id")]]==1)
								if($finish_roll_maintained==1)
								{
									echo number_format($booking_wise_gray_data[$booking_no]['fin_feb_rcb_by_store_qty'],2);
									$dtls_tot_finish_prod_rece_store +=$booking_wise_gray_data[$booking_no]['fin_feb_rcb_by_store_qty'];
									$finish_prod_rece_store_availabe=$booking_wise_gray_data[$booking_no]['fin_feb_rcb_by_store_qty'];
								}
								else
								{
									$finish_prod_rece_store=$fin_store_rec_prod[$row[("booking_id")]][$dts_row[("fabric_color")]]; 
								 	echo number_format($finish_prod_rece_store,2); 
								 	$dtls_tot_finish_prod_rece_store +=$finish_prod_rece_store; 
								 	$finish_prod_rece_store_availabe=$fin_store_rec_prod[$row[("booking_id")]][$dts_row[("fabric_color")]];
								} 
								
								//$buyer_wise_result[$row["buyer_id"]]["fin_total_available"] +=$finishfab_product_arr[$row[("booking_id")]][$dts_row[("fabric_color")]]+$finishfab_product_arr[$row[("booking_id")]][$dts_row[("fabric_color")]];
								 ?>
		                         </a></p>
		                    </td>

	                        <td width="110" align="right"><p><?  $finish_parchase_rece_store=$fin_store_rec_purchase[$row[("booking_id")]][$dts_row[("fabric_color")]]; echo number_format($finish_parchase_rece_store,2); $dtls_tot_finish_parchase_rece_store +=$finish_parchase_rece_store; ?></p>
	                        </td>
	                        
	                        <td width="110" align="right"><p><? $fabric_store_available=$finish_prod_rece_store_availabe+$finish_parchase_rece_store; echo number_format($fabric_store_available,2); $dtls_tot_fabric_store_available +=$fabric_store_available; 
	                            $buyer_wise_result[$row["buyer_id"]]["fin_total_available"] +=$fabric_store_available;
	                         ?></p></td>
	                       
	                        <td width="110" align="right"><p><?
							$finish_total_available=$finish_prod_rece_store_availabe+$finish_parchase_rece_store;
	                         $finish_balance=$dts_row[("finish_fabric_qty")]-$finish_total_available; echo number_format($finish_balance,2); $dtls_tot_fin_balance +=$finish_balance;  ?></p></td>
	                        
	                        <td width="110" align="right"><p>
		                        <?
		                        	//if ($booking_wise_roll_check[$row[("booking_id")]]==1)
									if($finish_roll_maintained==1)
									{
										$id=chop($booking_wise_gray_data[$booking_nos]['barcodes'],",");
									}
									else
									{
										$id=$row[("booking_id")]."_withoutRoll";
									}
		                        ?>
		                        <a  href="##" onclick="open_febric_receive_status_color_wise_popup('<? echo $id; ?>','issue_to_cut','<? echo $dts_row[("fabric_color")]; ?>')">
								<? 
								//if ($booking_wise_roll_check[$row[("booking_id")]]==1)
								if($finish_roll_maintained==1)
								{
									echo number_format($booking_wise_gray_data[$booking_no]['fin_feb_roll_issue_qty'],2);
									$dtls_tot_cutting_qty +=$booking_wise_gray_data[$booking_no]['fin_feb_roll_issue_qty'];
									$buyer_wise_result[$row["buyer_id"]]["issue_to_cut"] +=$booking_wise_gray_data[$booking_no]['fin_feb_roll_issue_qty'];
								}
								else
								{	
									echo number_format($issue_to_cut_arr[$row[("booking_id")]][$dts_row[("fabric_color")]],2); 
									$dtls_tot_cutting_qty +=$issue_to_cut_arr[$row[("booking_id")]][$dts_row[("fabric_color")]];
									$buyer_wise_result[$row["buyer_id"]]["issue_to_cut"] +=$issue_to_cut_arr[$row[("booking_id")]][$dts_row[("fabric_color")]];
								}
								?>
		                        </a>
		                        </p>
	                    	</td>

	                        <td width="110" align="right" title="Fabric Available-Issue to Cutting"><p>
								<?								
		                       	//$yet_to_issue=$row[("grey_fabric_qnty")]-$issue_to_cut_arr[$row[("booking_id")]][$dts_row[("fabric_color")]];
								if($finish_roll_maintained==1)
								{
									$yet_to_issue=$fabric_store_available-$booking_wise_gray_data[$booking_no]['fin_feb_roll_issue_qty'];
									echo number_format($yet_to_issue,2); $dtls_tot_yet_to_issue +=$yet_to_issue; 
								}
								else
								{
									$yet_to_issue=$fabric_store_available-$issue_to_cut_arr[$row[("booking_id")]][$dts_row[("fabric_color")]];
									echo number_format($yet_to_issue,2); $dtls_tot_yet_to_issue +=$yet_to_issue;
								}
		                        ?>
		                        </p>
	                    	</td>
	                        <td width="110" align="right" title="Fabric Available-Issue to Cutting"><p>
	                        	<?
								//$left_over=$finish_prod_rece_store-$issue_to_cut_arr[$row[("booking_id")]][$dts_row[("fabric_color")]];
								if($finish_roll_maintained==1)
								{
									$left_over=$fabric_store_available-$booking_wise_gray_data[$booking_no]['fin_feb_roll_issue_qty'];
									echo number_format($left_over,2); $dtls_tot_left_over +=$left_over; 
								}
								else
								{
									$left_over=$fabric_store_available-$issue_to_cut_arr[$row[("booking_id")]][$dts_row[("fabric_color")]];
									echo number_format($left_over,2); $dtls_tot_left_over +=$left_over;
								}
								?>
		                        </p>
	                    	</td>
	                        <td ><? echo implode(',',$dts_row['fabric_description']); ?></td>
						</tr>
						<?
					}
					else
					{
						?>
                        <tr>
                            <td width="40">&nbsp;</td>
                            <td width="65">&nbsp;</td>
                            <td width="65">&nbsp;</td>
                            
                            <td width="80">&nbsp;</td>
                            <td width="80">&nbsp;</td>
                            <td width="80">&nbsp;</td>
                            <td width="80">&nbsp;</td>
                            <td width="80">&nbsp;</td>
                            <td width="80">&nbsp;</td>
                            <td width="90">&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            
                            <td width="80"><p><? echo $lib_yean_count_arr[$dts_row[("lib_yarn_count_deter_id")]]; ?></p></td>
                        	<td width="100"><p><? echo $dts_row[("composition")]; ?></p></td>
                        	<td width="80"><p><?   echo $lib_yean_type_arr[$dts_row[("lib_yarn_count_deter_id")]]; ?></p></td>
                            <td width="110" align="right">&nbsp;</td>
                            <td width="110" align="right">&nbsp;</td>
                            <td width="110" align="right">&nbsp;</td>
                            <td width="110" align="right">&nbsp;</td>
                            <td width="110" align="right">&nbsp;</td>
                            <td width="110" align="right">&nbsp;</td>
                            <td width="110" align="right">&nbsp;</td>
                            <td width="110" align="right">&nbsp;</td>
                            <td width="110" align="right">&nbsp;</td>
                            <td width="110" align="right">&nbsp;</td>
                            <td width="110" align="right">&nbsp;</td>
                            <td width="110" align="right">&nbsp;</td>
                            <td width="110"><p><? //echo $color_array[$dts_row[("fabric_color")]]; ?></p> </td>
                            <td width="110"><p><? echo $color_array[$dts_row[("fabric_color")]]; ?></p> </td>
                            <td width="110" align="right">
	                            <p>
	                             <a  href="##" onclick="open_febric_receive_status_color_wise_popup('<? echo $row[("booking_id")]; ?>','batch_qty_popup','<? echo $dts_row[("fabric_color")]; ?>')">
	                            <?
	                            $dtls_batch_qty=$batch_qty_arr_check[$row[("booking_id")]][$dts_row[("fabric_color")]]['batch_qnty'];
	                            $dy_check_id=$dyeing_check_arr[$batch_qty_arr_check[$row[("booking_id")]][$dts_row[("fabric_color")]]['batch_id']];
	                            echo number_format($dtls_batch_qty,2); $dtls_tot_batch_qty +=$dtls_batch_qty; 
	                            	$buyer_wise_result[$row[("buyer_id")]]["batch_qty"] +=$dtls_batch_qty;
	                             ?>
	                             </a></p>
	                         </td>
                            <td width="110" align="right"><p>
	                            <a  href="##" onclick="openmypage('<? echo $row[("booking_id")]; ?>','dying_qty_popup','')">
	                            <?
	                            $dtls_dying_qty=$dyeing_check_arr[$row[("booking_id")]][$dts_row[("fabric_color")]]['batch_qnty'];
	                            echo number_format($dtls_dying_qty,2);
	                            $dtls_tot_dying_qty +=$dtls_dying_qty; 
	                            //echo number_format($finishfab_product_arr[$row[("booking_id")]][$dts_row[("fabric_color")]],2); 
	                            $dtls_tot_fin_prod_qty +=$finishfab_product_arr[$row[("booking_id")]][$dts_row[("fabric_color")]];  
	                            $buyer_wise_result[$row[("buyer_id")]]["dyeing_qty"] +=$dtls_dying_qty;

	                            ?>
	                            </a>
	                            </p>
	                        </td>

	                        <td width="110" align="right"><p>
		                        <?
								$dying_balance=$dtls_batch_qty-$dtls_dying_qty; $dtls_tot_dying_balance+=$dying_balance;
								echo number_format($dying_balance,2);  $total_dying_balance_qty +=$dying_balance;  
								?>
		                        </p>
	                    	</td>
	                        <td width="110" align="right"><p><? echo number_format($dts_row[("finish_fabric_qty")],2); $dtls_tot_fin_req_qty +=$dts_row[("finish_fabric_qty")]; 

	                        $buyer_wise_result[$row["buyer_id"]]["finish_fabric_qty"] +=$dts_row[("finish_fabric_qty")];
	                        ?></p></td>
							<td width="110" align="right"><p>
								<?
									//if ($booking_wise_roll_check[$row[("booking_no")]]==1)
									if($finish_roll_maintained==1)
		                    		{
		                    			$id=$booking_wise_gray_data[$booking_no]['barcodes'];
		                    		}
		                    		else
		                    		{
		                    			$id=$row[("booking_id")]."_withoutRoll";
		                    		}
								?>
		                        <a  href="##" onclick="open_febric_receive_status_color_wise_popup('<? echo $id; ?>','finish_feb_pord','<? echo $dts_row[("fabric_color")]; ?>')">
								<? 
									//if ($booking_wise_roll_check[$row[("booking_no")]]==1) //if production is roll level
									if($finish_roll_maintained==1)
									{
										echo number_format($booking_wise_gray_data[$row[("booking_no")]]['gray_fin_feb_prod_qty'],2);
										$dtls_tot_fin_prod_qnty +=$booking_wise_gray_data[$row[("booking_no")]]['gray_fin_feb_prod_qty'];
										//$buyer_wise_result[$row["buyer_id"]]["finish_fabric_qty"] +=$booking_wise_gray_data[$row[("booking_no")]]['gray_fin_feb_prod_qty'];
									}
									else
									{
										echo number_format($finishfab_product_arr[$row[("booking_id")]][$dts_row[("fabric_color")]],2);
										$dtls_tot_fin_prod_qnty +=$finishfab_product_arr[$row[("booking_id")]][$dts_row[("fabric_color")]]; 
										//$buyer_wise_result[$row["buyer_id"]]["finish_fabric_qty"] +=$finishfab_product_arr[$row[("booking_id")]][$dts_row[("fabric_color")]];
									} 
								?>
		                        </a>
		                        </p>
	                    	</td>

                            <td width="110" align="right"><p><? 
                             $finish_balance=$dts_row[("finish_fabric_qty")]-$finishfab_product_arr[$row[("booking_id")]][$dts_row[("fabric_color")]]; echo number_format($finish_balance,2); $tot_fin_balance +=$finish_balance;  ?></p></td>
                            <td width="110" align="right"><p>
	                            <a  href="##" onclick="openmypage('<? echo $id."__".$dts_row[("fabric_color")]; ?>','finish_fabric_delivery_to_store','')">
	                            <? 
	                             echo number_format($finish_prodction_delivery_qty[$row[("booking_id")]][$dts_row[("fabric_color")]],2); $dtls_tot_fin_delivery_qty +=$finish_prodction_delivery_qty[$row[("booking_id")]][$dts_row[("fabric_color")]]; ?>
	                             </a>
	                             </p>
                         	</td>
                            
                            <td width="110" align="right"><p><? 
	                            $fabric_in_prod_floor=$finish_product_arr[$row[("booking_id")]][$dts_row[("fabric_color")]]-$finish_prodction_delivery_qty[$row[("booking_id")]][$dts_row[("fabric_color")]];
	                             echo number_format($fabric_in_prod_floor,2); $dtls_tot_fabric_in_prod_floor +=$fabric_in_prod_floor;  ?></p>
                         	</td>
                            <td width="110" align="right"><p>
                            	 <a  href="##" onclick="open_febric_receive_status_color_wise_popup('<? echo $id; ?>','finish_fabric_receive_by_store','<? echo $dts_row[("fabric_color")]; ?>')">
	                            <?
	                             $finish_prod_rece_store=$fin_store_rec_prod[$row[("booking_id")]][$dts_row[("fabric_color")]];
	                             
	                             echo number_format($finish_prod_rece_store,2); $dtls_tot_finish_prod_rece_store +=$finish_prod_rece_store;  ?>
	                             </a></p>
                         	</td>
                            <td width="110" align="right"><p><?  $finish_parchase_rece_store=$fin_store_rec_purchase[$row[("booking_id")]][$dts_row[("fabric_color")]]; echo number_format($finish_parchase_rece_store,2); $dtls_tot_finish_parchase_rece_store +=$finish_parchase_rece_store; ?></p></td>
                            
                            <td width="110" align="right"><p><? $fabric_store_available=$finish_prod_rece_store+$finish_parchase_rece_store; echo number_format($fabric_store_available,2); $dtls_tot_fabric_store_available +=$fabric_store_available;  
                            	$buyer_wise_result[$row["buyer_id"]]["fin_total_available"] +=$fabric_store_available;

                            ?>
                            </p></td>
                           
                            <td width="110" align="right"><p><? 
                            $finish_total_available=$finish_prod_rece_store+$finish_parchase_rece_store;
                            $finish_balance=$dts_row[("finish_fabric_qty")]-$finish_total_available; echo number_format($finish_balance,2); $dtls_tot_fin_balance +=$finish_balance;  ?></p></td>


                            <td width="110" align="right"><p>
	                            <a  href="##" onclick="open_febric_receive_status_color_wise_popup('<? echo $id; ?>','issue_to_cut','<? echo $dts_row[("fabric_color")]; ?>')">
	                            <? 
	                            echo number_format($issue_to_cut_arr[$row[("booking_id")]][$dts_row[("fabric_color")]],2); $dtls_tot_cutting_qty +=$issue_to_cut_arr[$row[("booking_id")]][$dts_row[("fabric_color")]]; 
	                            $buyer_wise_result[$row["buyer_id"]]["issue_to_cut"] +=$issue_to_cut_arr[$row[("booking_id")]][$dts_row[("fabric_color")]];
	                            ?>
	                            </a>
	                            </p>
                        	</td>
                            <td width="110" align="right"><p>
	                            <? 
	                            //$yet_to_issue=$row[("grey_fabric_qnty")]-$issue_to_cut_arr[$row[("booking_id")]][$dts_row[("fabric_color")]];
								if($finish_roll_maintained==1)
								{
									$yet_to_issue=$fabric_store_available-$booking_wise_gray_data[$booking_no]['fin_feb_roll_issue_qty'];
									echo number_format($yet_to_issue,2); $dtls_tot_yet_to_issue +=$yet_to_issue; 
								}
								else
								{
									$yet_to_issue=$fabric_store_available-$issue_to_cut_arr[$row[("booking_id")]][$dts_row[("fabric_color")]];
									echo number_format($yet_to_issue,2); $dtls_tot_yet_to_issue +=$yet_to_issue;
								}
								?>
	                            </p>
                        	</td>
                            <td width="110" align="right"><p>
                            	<?
								//$left_over=$finish_prod_rece_store-$issue_to_cut_arr[$row[("booking_id")]][$dts_row[("fabric_color")]];
								if($finish_roll_maintained==1)
								{
									$left_over=$fabric_store_available-$booking_wise_gray_data[$booking_no]['fin_feb_roll_issue_qty'];
									echo number_format($left_over,2); $dtls_tot_left_over +=$left_over; 
								}
								else
								{
									$left_over=$fabric_store_available-$issue_to_cut_arr[$row[("booking_id")]][$dts_row[("fabric_color")]];
									echo number_format($left_over,2); $dtls_tot_left_over +=$left_over;
								}
								?>
	                            </p>
                        	</td>
                            <td ><? echo implode(',',$dts_row['fabric_description']); ?></td>
                        </tr>
                        <?
					}
					$m++;
				}
			$i++;
		}
		?>
        </tbody>
    </table>
    </div>   
    <table class="rpt_table" border="1" rules="all" width="4950" cellpadding="0" cellspacing="0">
		<tfoot>
			<tr>
                <th width="40">&nbsp;</th>
                <th width="65">&nbsp;</th>
                <th width="65">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="90">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="80">Total</th>
               
               	<th width="110" id="value_dtls_tot_gery_req"><? echo number_format($dtls_tot_gery_req,2); ?></th>
                <th width="110" id="value_dtls_tot_yarn_issue"><? echo number_format($dtls_tot_yarn_issue,2); ?></th>
                <th width="110" id="value_dtls_tot_yarn_balance"><? echo number_format($dtls_tot_yarn_balance,2); ?></th>
                <th width="110" id="value_dtls_tot_gery_knit_product"><? echo number_format($dtls_tot_gery_knit_product,2); ?></th>
				<th width="110" id="value_dtls_tot_gray_bal"><? echo number_format($dtls_tot_gray_bal,2); ?></th>
                
                <th width="110" id="value_dtls_tot_gery_delivery"><? echo number_format($dtls_tot_gery_delivery,2); ?></th>
                <th width="110" id="value_dtls_tot_gery_in_knit_product"><? echo number_format($dtls_tot_gery_knit_product,2); ?></th>
				<th width="110" id="value_dtls_tot_grey_knit_receive_prod"><? echo number_format($dtls_tot_grey_knit_receive_prod,2); ?></th>
                
				<th width="110"><? //echo number_format($dtls_tot_gery_available,2); ?></th>
                <th width="110" id="value_dtls_tot_net_transfer"><? echo number_format($dtls_tot_net_transfer,2); ?></th>
                <th width="110" id="value_dtls_tot_gray_available_all"><? echo number_format($dtls_tot_gray_available_all,2); ?></th>
                
                <th width="110" id="value_dtls_tot_gray_balance"><? echo number_format($dtls_tot_gray_balance,2); ?></th>
				<th width="110" id="value_dtls_tot_gray_issue"><? echo number_format($dtls_tot_gray_issue,2); ?></th>
                <th width="110"><? //echo number_format($dtls_tot_cutting_qty,2); ?> </th>
                <th width="110" id="value_dtls_tot_batch_qty"><? echo number_format($dtls_tot_batch_qty,2); ?></th>
                <th width="110" id="value_dtls_tot_dying_qty"><? echo number_format($dtls_tot_dying_qty,2); ?></th>
				<th width="110" id="value_dtls_tot_dying_balance"><? echo number_format($dtls_tot_dying_balance,2); ?></th>
				<th width="110" id="value_dtls_tot_fin_req_qty"><? echo number_format($dtls_tot_fin_req_qty,2); ?></th>
				<th width="110" id="value_dtls_tot_fin_prod_qnty"><? echo number_format($dtls_tot_fin_prod_qnty,2); ?></th>

				<th width="110" id="value_tot_fin_balance"><? echo number_format($tot_fin_balance,2); ?></th>
				<th width="110" id="value_dtls_tot_fin_delivery_qty"><? echo number_format($dtls_tot_fin_delivery_qty,2); ?></th>
                
				<th width="110" id="value_dtls_tot_fabric_in_prod_floor"><? echo number_format($dtls_tot_fabric_in_prod_floor,2); ?></th>
                <th width="110" id="value_dtls_tot_finish_prod_rece_store"><? echo number_format($dtls_tot_finish_prod_rece_store,2); ?></th>
                <th width="110" id="value_finish_parchase_rece_store"><? echo number_format($finish_parchase_rece_store,2); ?></th>
                
				<th width="110" id="value_dtls_tot_fabric_store_available"><? echo number_format($dtls_tot_fabric_store_available,2); ?></th>
				<th width="110" id="value_dtls_tot_fin_balance"><? echo number_format($dtls_tot_fin_balance,2); ?> </th>
                <th width="110" id="value_dtls_tot_cutting_qty"><? echo number_format($dtls_tot_cutting_qty,2); ?> </th>
				<th width="110" id="value_dtls_tot_yet_to_issue"><? echo number_format($dtls_tot_yet_to_issue,2); ?> </th>
                <th width="110" id="value_dtls_tot_left_over"><? echo number_format($dtls_tot_left_over,2); ?></th>
				<th>&nbsp;</th>
			</tr>
		</tfoot>
	</table>
	<!-- Booking Details End -->
   <br />
    <?

    echo "<br />Execution Time: " . (microtime(true) - $started) . "S";

	foreach (glob("$user_name*.xls") as $filename) 
	{
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data####$filename####$tot_rows";
	exit();
}
if($action=="report_generate_old")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	//$sample_array=return_library_array( "select id,sample_name from lib_sample order by sample_name","id","sample_name");
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$cbo_wo_year=str_replace("'","",$cbo_wo_year);
	$txt_wo_no=trim(str_replace("'","",$txt_wo_no));
	$txt_internal_ref=trim(str_replace("'","",$txt_internal_ref));
	$wo_cond="";
	if($txt_wo_no!="") $wo_cond.=" and a.booking_no_prefix_num=$txt_wo_no"; 
	
	if($cbo_wo_year>0) $wo_cond.=" and $select_year='$cbo_wo_year'";
	
	if($txt_internal_ref!="") $wo_cond.=" and a.grouping='$txt_internal_ref'"; 
	//echo $wo_cond;die;
	
	if($cbo_buyer_name==0) $buyer_name="%%"; else $buyer_name=$cbo_buyer_name;
	if($db_type==0)
	{
		if($txt_date_from!="" && $txt_date_to!="") $str_cond=" and a.booking_date  between '$txt_date_from' and '$txt_date_to'"; else $str_cond="";
	}
	else if($db_type==2)
	{
		if($txt_date_from!="" && $txt_date_to!="") $str_cond=" and a.booking_date  between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'"; else $str_cond="";
	}
	
	//echo $str_cond;die;
	 $sql="SELECT a.id as booking_id, a.booking_no, a.booking_no_prefix_num, $select_year as wo_year, a.is_short, a.po_break_down_id, a.fabric_source, a.is_approved, a.job_no, a.buyer_id, a.company_id, a.supplier_id, a.item_category, a.is_approved, a.grouping, b.sample_type, b.grey_fabric as grey_fabric_qnty, b.finish_fabric as finish_fabric_qty, b.fabric_color, b.fabric_description, b.style_id, b.composition, a.booking_date, b.lib_yarn_count_deter_id, a.booking_date, a.delivery_date
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
	where a.booking_no=b.booking_no and a.item_category in(2,13) and a.company_id like '$cbo_company_name' and a.buyer_id like '$buyer_name'  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $str_cond $wo_cond order by a.booking_no";
	// echo $sql;die();
	$sql_res=sql_select($sql); $allBookingIds=''; $tot_rows=0;
	$booking_id_array = array();
	$booking_no_array = array();
	foreach ($sql_res as $val) 
	{
		$booking_id_array[$val[csf("booking_id")]]=$val[csf("booking_id")];
		$booking_no_array[$val[csf("booking_no")]]=$val[csf("booking_no")];
	}
	$booking_id = implode(",", $booking_id_array);
	$booking_no = "'".implode("', '", $booking_no_array)."'";

	$dtls_ids_arr=array();
	$booking_wise_gray_data=array();

	$sql_dtls_ids_arr=sql_select("SELECT b.id as dtls_id, a.roll_maintained,a.booking_id from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.booking_id in($booking_id) and a.booking_no in($booking_no) and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	$booking_wise_roll_check = array();
	foreach ($sql_dtls_ids_arr as $val) 
	{
		$booking_wise_roll_check[$val[csf('booking_id')]]=$val[csf('roll_maintained')];
		$dtls_ids_arr[]="'".$val[csf('dtls_id')]."'";
	}
	
	
	$dtls_trans_ids_arr=array();
	$sql_ord_sample=sql_select("select b.id as dtls_id, a.to_order_id as booking_id from inv_item_transfer_mst a, inv_item_transfer_dtls b where a.id=b.mst_id and a.to_order_id in($booking_id) and a.entry_form=110 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach ($sql_ord_sample as $val) 
	{
		$dtls_trans_ids_arr[]="'".$val[csf('dtls_id')]."'";
	}
			
	$dtls_ids= implode(",", array_unique($dtls_ids_arr));
	$dtls_trans_ids= implode(",", array_unique($dtls_trans_ids_arr));
	
	//FINDING BARCODE NUMBER AND ROLL HISTORY FOR PRODUCTIONS
	$prod_roll_dtls_arr=array();
	$sql_prod_roll_dtls_arr=sql_select("select id, roll_no, barcode_no, po_breakdown_id, qnty, booking_no from pro_roll_details where dtls_id in ($dtls_ids) and booking_no in($booking_no) and entry_form=2 and status_active=1 and is_deleted=0");
	foreach ($sql_prod_roll_dtls_arr as $val) 
	{
		$prod_roll_dtls_arr[]=$val[csf('barcode_no')];
		$booking_wise_gray_data[$val[csf('booking_no')]]['barcodes']=$val[csf('barcode_no')];
	}
	
	if($dtls_trans_ids!="")
	{
		$sql_trans_prod_roll_dtls_arr=sql_select("select id, roll_no, barcode_no, po_breakdown_id, qnty, booking_no from pro_roll_details where dtls_id in ($dtls_trans_ids) and entry_form=110 and status_active=1 and is_deleted=0");
		foreach ($sql_trans_prod_roll_dtls_arr as $val) 
		{
			$prod_roll_dtls_arr[]=$val[csf('barcode_no')];
			$booking_wise_gray_data[$val[csf('booking_no')]]['barcodes']=$val[csf('barcode_no')];
		}
	}
	
		
	$barcode_ids= implode(",", array_unique($prod_roll_dtls_arr));
	// $booking_wise_gray_data[$key]['barcodes']=$barcode_ids;
	//finding gray febric receive roll dtls 
	if($barcode_ids!="")
	{
		$gray_feb_rcv_arr=sql_select("select booking_no, sum(qnty) as recv_qty from pro_roll_details where barcode_no in ($barcode_ids) and entry_form=58 and status_active=1 and is_deleted=0 group by booking_no"); 
		foreach ($gray_feb_rcv_arr as $val) 
		{
			$booking_wise_gray_data[$val[csf('booking_no')]]['gray_rcv_qty']=$val[csf('recv_qty')];
		}
	}
	
	// $booking_wise_gray_data[$gray_feb_rcv_arr[0][csf('booking_no')]]['gray_rcv_qty']=$gray_feb_rcv_arr[0][csf('recv_qty')];
	// print_r($booking_wise_gray_data);
	

	//finding gray febric issue roll wise

	$gray_feb_iss_arr=sql_select("select booking_no, sum(qnty) as roll_issue_qty from pro_roll_details where barcode_no in ($barcode_ids) and entry_form=61 and status_active=1 and is_deleted=0 group by booking_no");
	foreach ($gray_feb_iss_arr as $val) 
	{
		$booking_wise_gray_data[$val[csf('booking_no')]]['gray_issue_qty']=$val[csf('roll_issue_qty')];
	}
	// print_r($booking_wise_gray_data);
	// $booking_wise_gray_data[$gray_feb_iss_arr[0][csf('booking_no')]]['gray_issue_qty']=$gray_feb_iss_arr[0][csf('roll_issue_qty')];

	//finding gray finish febric production roll wise

	$gray_fin_feb_prod_arr=sql_select("select booking_no, sum(qnty) as roll_qty from pro_roll_details where barcode_no in ($barcode_ids) and entry_form=66 and status_active=1 and is_deleted=0 group by booking_no");
	$booking_wise_gray_data[$booking_no]['gray_fin_feb_prod_qty']=$gray_fin_feb_prod_arr[0][csf('roll_qty')];

	//finding finish febric delivery to store roll wise

	$fin_feb_delivery_store_arr=sql_select("select booking_no, sum(qnty) as roll_qty from pro_roll_details where barcode_no in ($barcode_ids) and entry_form=67 and status_active=1 and is_deleted=0 group by booking_no");
	$booking_wise_gray_data[$booking_no]['fin_feb_delivery_store_qty']=$fin_feb_delivery_store_arr[0][csf('roll_qty')];

	//finding finish febric roll receive by store qty

	$fin_feb_recv_by_store_arr=sql_select("select booking_no, sum(qnty) as roll_qty from pro_roll_details where barcode_no in ($barcode_ids) and entry_form=68 and status_active=1 and is_deleted=0 group by booking_no");
	$booking_wise_gray_data[$booking_no]['fin_feb_rcb_by_store_qty']=$fin_feb_recv_by_store_arr[0][csf('roll_qty')];

	//finding finish febric roll Issue qty

	$fin_feb_roll_issue_arr=sql_select("select booking_no, sum(qnty) as roll_qty from pro_roll_details where barcode_no in ($barcode_ids) and entry_form=71 and status_active=1 and is_deleted=0 group by booking_no");
	$booking_wise_gray_data[$booking_no]['fin_feb_roll_issue_qty']=$fin_feb_roll_issue_arr[0][csf('roll_qty')];
	
	
	// die();
	//--------------------------------------------------------------------------------------------------
	foreach($sql_res as $row)
	{
		$tot_rows++;
		$result_mst_array[$row[csf("booking_id")]]["booking_id"]=$row[csf("booking_id")];
		$result_mst_array[$row[csf("booking_id")]]["booking_no"]=$row[csf("booking_no")];
		$result_mst_array[$row[csf("booking_id")]]["booking_no_prefix_num"]=$row[csf("booking_no_prefix_num")];
		$result_mst_array[$row[csf("booking_id")]]["wo_year"]=$row[csf("wo_year")];
		$result_mst_array[$row[csf("booking_id")]]["is_short"]=$row[csf("is_short")];
		$result_mst_array[$row[csf("booking_id")]]["po_id"]=$row[csf("po_break_down_id")];
		$result_mst_array[$row[csf("booking_id")]]["job_no"]=$row[csf("job_no")];
		$result_mst_array[$row[csf("booking_id")]]["booking_date"]=$row[csf("booking_date")];
		$result_mst_array[$row[csf("booking_id")]]["company_id"]=$row[csf("company_id")];
		$result_mst_array[$row[csf("booking_id")]]["supplier_id"]=$row[csf("supplier_id")];
		$result_mst_array[$row[csf("booking_id")]]["item_category"]=$row[csf("item_category")];
		$result_mst_array[$row[csf("booking_id")]]["is_approved"]=$row[csf("is_approved")];
		$result_mst_array[$row[csf("booking_id")]]["sample_type"].=$row[csf("sample_type")].",";
		$result_mst_array[$row[csf("booking_id")]]["buyer_id"]=$row[csf("buyer_id")];
		$result_mst_array[$row[csf("booking_id")]]["style_id"]=$row[csf("style_id")];
		$result_mst_array[$row[csf("booking_id")]]["internal_ref"]=$row[csf("grouping")];
		
		$result_mst_array[$row[csf("booking_id")]]["booking_date"]=$row[csf("booking_date")];
		$result_mst_array[$row[csf("booking_id")]]["delivery_date"]=$row[csf("delivery_date")];
		$result_mst_array[$row[csf("booking_id")]]["grey_fabric_qnty"]+=$row[csf("grey_fabric_qnty")];
		
		$result_dtls_array[$row[csf("booking_id")]][$row[csf("fabric_color")]]["fabric_source"]=$row[csf("fabric_source")];
		$result_dtls_array[$row[csf("booking_id")]][$row[csf("fabric_color")]]["fabric_description"][]=$row[csf("fabric_description")];
		$result_dtls_array[$row[csf("booking_id")]][$row[csf("fabric_color")]]["fabric_color"]=$row[csf("fabric_color")];
		$result_dtls_array[$row[csf("booking_id")]][$row[csf("fabric_color")]]["lib_yarn_count_deter_id"]=$row[csf("lib_yarn_count_deter_id")];
		$result_dtls_array[$row[csf("booking_id")]][$row[csf("fabric_color")]]["composition"]=$row[csf("composition")];
		$result_dtls_array[$row[csf("booking_id")]][$row[csf("fabric_color")]]["finish_fabric_qty"]+=$row[csf("finish_fabric_qty")];
		
		$buyer_wise_result[$row[csf("buyer_id")]]["buyer_id"]=$row[csf("buyer_id")];
		$buyer_wise_result[$row[csf("buyer_id")]]["grey_fabric_qnty"] +=$row[csf("grey_fabric_qnty")];
		
		$dtls_batch_qty=$batch_qty_arr_check[$row[csf("booking_id")]][$row[csf("fabric_color")]]['batch_qnty'];
		$dy_check_id=$dyeing_check_arr[$batch_qty_arr_check[$row[csf("booking_id")]][$row[csf("fabric_color")]]['batch_id']];
		if($dy_check_id!="") 
		{
			$buyer_wise_result[$row[csf("buyer_id")]]["dyeing_qty"] +=$dtls_batch_qty;
			$gt_dying_qty+=$dtls_batch_qty;
		}
		
		$gt_yarn_grey_required+=$row[csf("grey_fabric_qnty")];
		$gt_finish_requir+=$row[csf("finish_fabric_qty")];
		$gt_finish_available+=($finish_receive_arr[$row[csf("booking_id")]][$row[csf("fabric_color")]]+$finish_product_arr[$row[csf("booking_id")]][$row[csf("fabric_color")]]);
		//$gt_issue_cutting+=$issue_to_cut_arr[$row[csf("booking_id")]][$row[csf("fabric_color")]];
		
		if($db_type==2)
		{
			$add_comma="'";
			$allBookingIds.=$row[csf("booking_id")].",";
			$allBookingNos.=$add_comma.$row[csf("booking_no")].$add_comma.",";
		}
		else
		{
			$add_comma="'";
		  	$allBookingIds.=$add_comma.$row[csf("booking_id")].$add_comma.",";
			$allBookingNos.=$add_comma.$row[csf("booking_no")].$add_comma.",";
		}
		//$allBookingIds.=$row[csf("booking_id")].",";
	}
	//unset($sql_res);
	// print_r($buyer_wise_result);die();

	$allBookingIds=implode(",",array_unique(explode(",",$allBookingIds)));
	$allBookingIds=chop($allBookingIds,','); $allBookingIds_cond=""; $pi_booking_id_cond=""; $finprod_booking_id_cond=""; $grey_del_booking_id_cond=""; $grey_del_pi_wo_batch_no_cond = "";
	if($db_type==2 && $tot_rows>1000)
	{
		$allBookingIds_cond=" and (";
		$pi_booking_id_cond=" and (";
		$finprod_booking_id_cond=" and (";
		$grey_del_booking_id_cond=" and (";
		//$grey_del_pi_wo_batch_no_cond = " and (";
		
		$allBookingIdsArr=array_chunk(explode(",",$allBookingIds),999);
		foreach($allBookingIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$allBookingIds_cond.=" a.booking_no_id in($ids) or ";
			$pi_booking_id_cond.=" d.work_order_id in($ids) or ";
			$finprod_booking_id_cond.=" c.booking_no_id in($ids) or ";
			$grey_del_booking_id_cond.=" a.booking_id in($ids) or ";
			//$grey_del_pi_wo_batch_no_cond.=" a.booking_id in($ids) or ";
		}
		$allBookingIds_cond=chop($allBookingIds_cond,'or ');
		$allBookingIds_cond.=")";
		
		$pi_booking_id_cond=chop($pi_booking_id_cond,'or ');
		$pi_booking_id_cond.=")";
		
		$finprod_booking_id_cond=chop($finprod_booking_id_cond,'or ');
		$finprod_booking_id_cond.=")";
		
		$grey_del_booking_id_cond=chop($grey_del_booking_id_cond,'or ');
		$grey_del_booking_id_cond.=")";
		
		/*$grey_del_pi_wo_batch_no_cond=chop($grey_del_pi_wo_batch_no_cond,'or ');
		$grey_del_pi_wo_batch_no_cond.=")";*/
	}
	else
	{
		$allBookingIds_cond=" and a.booking_no_id in($allBookingIds)";
		$pi_booking_id_cond=" and d.work_order_id in($allBookingIds)";
		$finprod_booking_id_cond=" and c.booking_no_id in($allBookingIds)";
		$grey_del_booking_id_cond=" and a.booking_id in($allBookingIds)";
		//$grey_del_pi_wo_batch_no_cond=" and a.booking_id in($allBookingIds)";
	}
	$allBookingNos=implode(",",array_unique(explode(",",$allBookingNos)));
	$allBookingNos=chop($allBookingNos,',');
	if($db_type==2 && $tot_rows>1000)
	{
		$grey_del_pi_wo_batch_no_cond = " and (";
		
		$allBookingNosArr=array_chunk(explode(",",$allBookingNos),999);
		foreach($allBookingNosArr as $ids)
		{
			$ids=implode(",",$ids);
			$grey_del_pi_wo_batch_no_cond.=" a.booking_no in($ids) or ";
		}
		$grey_del_pi_wo_batch_no_cond=chop($grey_del_pi_wo_batch_no_cond,'or ');
		$grey_del_pi_wo_batch_no_cond.=")";
	}
	else
	{
		$grey_del_pi_wo_batch_no_cond=" and a.booking_no in($allBookingNos)";
	}
	//echo $allBookingIds_cond;die;

	$sql_yarn_issue=sql_select("SELECT a.booking_id,sum(b.cons_quantity) as issue_qty,min(a.issue_date) as issue_date from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.issue_basis=1 and a.issue_purpose=8 and a.item_category=1 and b.transaction_type=2 and a.entry_form=3  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.booking_id>0 $grey_del_booking_id_cond group by a.booking_id order by a.booking_id");
	
	foreach($sql_yarn_issue as $row)
	{
		$yarn_issue_arr[$row[csf("booking_id")]]=$row[csf("issue_qty")];
		$yarn_issue_date[$row[csf("booking_id")]]=$row[csf("issue_date")];
	}
	$sql_yarn_issue_rtn=sql_select("SELECT a.booking_id,sum(b.cons_quantity) as issue_rtn_qty from  inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.receive_basis=1 and a.item_category=1 and b.transaction_type=4 and a.entry_form=9 and a.booking_id>0 and a.booking_without_order=1 $grey_del_booking_id_cond group by a.booking_id order by a.booking_id");
	foreach($sql_yarn_issue_rtn as $row)
	{
		$yarn_issue_rtn_arr[$row[csf("booking_id")]]=$row[csf("issue_rtn_qty")];
	}


	ob_start();
	?>
	<table cellpadding="0" cellspacing="0" width="1900">
		<tr>
		   <td align="center" width="100%" colspan="20" ><strong style="font-size:16px"><? echo $company_library[$cbo_company_name]; ?></strong></td>
		</tr>
	</table>
    <?
	
	$fin_store_rec_purchase=array();
	$sql_finish_store_pi=sql_select("select d.work_order_id as booking_id, b.color_id, sum(b.receive_qnty ) as parcess_qty from  inv_receive_master a, pro_finish_fabric_rcv_dtls b, com_pi_master_details c, com_pi_item_details d where a.id=b.mst_id and a.booking_id=c.id and c.id=d.pi_id and a.entry_form=37 and a.receive_basis=1 and a.booking_id>0 $pi_booking_id_cond group by d.work_order_id, b.color_id");
	foreach($sql_finish_store_pi as $row)
	{
		$fin_store_rec_purchase[$row[csf("booking_id")]][$row[csf("color_id")]]=$row[csf("parcess_qty")];
	}
	unset($sql_finish_store_pi);
	
	$sql_finish_store_prod=sql_select("select c.booking_no_id as booking_id, c.batch_against, a.receive_basis, a.entry_form, b.color_id, max(a.receive_date) as receive_date, sum(b.receive_qnty) as production_qty 
	from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c 
	where a.id=b.mst_id and b.batch_id=c.id and c.booking_without_order=1 and c.batch_against=3 and a.entry_form in (7,37) and c.booking_no_id>0 $finprod_booking_id_cond group by c.booking_no_id, b.color_id,  c.batch_against, a.receive_basis, a.entry_form");
	foreach($sql_finish_store_prod as $row)
	{
		if($row[csf("entry_form")]==37 && $row[csf("receive_basis")]==9)
		{
			$fin_store_rec_prod[$row[csf("booking_id")]][$row[csf("color_id")]]+=$row[csf("production_qty")];
		}
		else if($row[csf("entry_form")]==7 && $row[csf("receive_basis")]==5)
		{
			$finishfab_product_arr[$row[csf("booking_id")]][$row[csf("color_id")]]+=$row[csf("production_qty")];
		}
		if($row[csf("entry_form")]==7)
		{
			$finish_prodction_date[$row[csf("booking_id")]]=$row[csf("receive_date")];
		}
		
	}
	unset($sql_finish_store_prod);	
	$sql_finish_prodction_delivery=sql_select("select c.booking_no_id, max(a.delevery_date) as receive_date, sum(b.current_delivery) as delivery_qty 
	from pro_grey_prod_delivery_mst a,pro_grey_prod_delivery_dtls b, pro_batch_create_mst c 
	where a.id=b.mst_id and b.batch_id=c.id and  a.entry_form=54  and b.entry_form=54 and c.batch_against=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $finprod_booking_id_cond group by c.booking_no_id ");
	
	foreach($sql_finish_prodction_delivery as $row)
	{
		$finish_prodction_delivery_qty[$row[csf("booking_no_id")]]=$row[csf("delivery_qty")];
		$finish_prodction_delivery_date[$row[csf("booking_no_id")]]=$row[csf("receive_date")];
	}
	unset($sql_finish_prodction_delivery);

	$sql_grey_knit_production=sql_select("select a.booking_id, sum(b.grey_receive_qnty) as receive_qty, max(a.receive_date) as receive_date 
		from inv_receive_master a, pro_grey_prod_entry_dtls b 
		where a.id=b.mst_id and a.receive_basis=1 and a.booking_without_order=1 and a.entry_form=2 and a.booking_id>0 $grey_del_booking_id_cond group by a.booking_id");
	$prod_booking_id_arr=array();
	foreach($sql_grey_knit_production as $row)
	{
		$grey_knit_production_arr[$row[csf("booking_id")]]+=$row[csf("receive_qty")];
		$grey_knit_production_date[$row[csf("booking_id")]]=$row[csf("receive_date")];
		//$prod_booking_id_arr[$row[csf("id")]]=$row[csf("booking_id")];
	}
		//print_r($prod_booking_id_arr);
	unset($sql_grey_knit_production);
	
	$sql_gray_delivery=sql_select("select c.id as delivery_id, a.booking_id, max(c.delevery_date) as delevery_date, sum(b.current_delivery) as current_stock 
		from inv_receive_master a,  pro_grey_prod_delivery_dtls b, pro_grey_prod_delivery_mst c 
		where c.id=b.mst_id and a.entry_form=2 and c.entry_form in(53,56) and b.grey_sys_id=a.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=1 $grey_del_booking_id_cond group by a.booking_id, c.id ");
	$grey_prod_booking_array=array();$all_delivery_id="";$delivery_book_id=array();
	foreach($sql_gray_delivery as $row)
	{
		$grey_delivery_date[$row[csf("booking_id")]]=$row[csf("delevery_date")];
		$grey_delivery_stock[$row[csf("booking_id")]]+=$row[csf("current_stock")];
		$delivery_book_id[$row[csf("delivery_id")]]=$row[csf("booking_id")];
		$all_delivery_id.=$row[csf("delivery_id")].",";
	}
	unset($sql_gray_delivery);
	$all_delivery_id=chop($all_delivery_id,",");
	
	$sql_grey_knit_receive=sql_select("select a.booking_id,a.entry_form, ( case when a.receive_basis <>9 then b.cons_quantity else 0 end) as receive_qty,( case when a.receive_basis in(9) then b.cons_quantity else 0 end) as production_qty 
		from inv_receive_master a, inv_transaction b 
		where a.id=b.mst_id and a.booking_without_order=1 and a.entry_form in(2,22) and b.transaction_type in(1,4) and a.booking_id>0 ");
		
	
	foreach($sql_grey_knit_receive as $result)
	{
		//echo $prod_booking_id_arr[$result[csf("booking_id")]];
		$grey_knit_receive_arr[$result[csf("booking_id")]]+=$result[csf("receive_qty")];
		$grey_knit_receive_prod_arr[$result[csf("booking_id")]]+=$result[csf("production_qty")];
	}
	unset($sql_grey_knit_receive);
	
	$sql_grey_booking=sql_select("select a.id,a.booking_id from inv_receive_master a where a.receive_basis=1 and a.booking_without_order=1 and a.entry_form=2 $grey_del_booking_id_cond");
	
	foreach($sql_grey_booking as $result)
	{
		$grey_knit_receive_booking[$result[csf("booking_id")]].=$result[csf("id")].",";
	}
	unset($sql_grey_booking);


	$sql = sql_select("select b.order_id, 
	sum( case when a.transfer_criteria in (7,8) and b.transaction_type=6 then b.cons_quantity else 0 end) as tranfer_out, 
	sum( case when a.transfer_criteria in(6,8) and b.transaction_type=5 then b.cons_quantity else 0 end) as tranfer_in 
	from inv_item_transfer_mst a, inv_transaction b where a.id=b.mst_id   and a.item_category=13 and b.item_category=13 and b.transaction_type in(5,6) 
	group by b.order_id order by b.order_id");

	foreach($sql as $result)
	{
		$sample_transectionArr[$result[csf("order_id")]]['tranfer_out'] = $result[csf("tranfer_out")];
		$sample_transectionArr[$result[csf("order_id")]]['tranfer_in'] = $result[csf("tranfer_in")];
	}
	
	unset($sql_grey_knit_receive);

	$sql_grey_issue=sql_select("select a.booking_id, sum(b.issue_qnty) as issue_qty, max(a.issue_date) as issue_date
	from inv_issue_master a, inv_grey_fabric_issue_dtls b where a.id=b.mst_id and a.issue_basis in(0,1) and a.issue_purpose in(8,11) 
	and a.entry_form in(16,61) $grey_del_pi_wo_batch_no_cond group by a.booking_id order by a.booking_id");
	foreach($sql_grey_issue as $result)
	{
		$grey_issue_arr[$result[csf("booking_id")]]+=$result[csf("issue_qty")];
		$grey_issue_dat_arr[$result[csf("booking_id")]]=$result[csf("issue_date")];
	}
	//print_r($grey_issue_arr);	
	unset($sql_grey_issue);
	
	$sql_batch_qty=sql_select("select a.id, a.batch_no, a.booking_no_id, a.color_id, sum(b.batch_qnty) as batch_qnty, max(a.batch_date) as batch_date from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.booking_without_order=1 $allBookingIds_cond group by a.id,a.batch_no,a.booking_no_id ,a.color_id ");
	//echo $sql_batch_qty;die;
	foreach($sql_batch_qty as $row)
	{
		$batch_qty_arr[$row[csf("booking_no_id")]]['batch_qnty']+=$row[csf("batch_qnty")];
		$batch_qty_arr[$row[csf("booking_no_id")]]['batch_date']=$row[csf("batch_date")];
		$batch_qty_arr_check[$row[csf("booking_no_id")]][$row[csf("color_id")]]['batch_qnty']+=$row[csf("batch_qnty")];
		$batch_qty_arr_check[$row[csf("booking_no_id")]][$row[csf("color_id")]]['batch_id']=$row[csf("id")];
		$dyeing_qty_arr[$row[csf("id")]]=$row[csf("batch_qnty")];
	}
	unset($sql_batch_qty);
	//var_dump($finish_prodction_date);die;
	$sql_dyeing_qty=sql_select("select a.id, a.booking_no_id,a.color_id, max(a.batch_date) as  batch_date from  pro_batch_create_mst a, pro_fab_subprocess c where c.batch_id=a.id and a.booking_without_order=1 and c.load_unload_id=2 $allBookingIds_cond group by a.id, a.booking_no_id, a.color_id ");
	
	foreach($sql_dyeing_qty as $row)
	{
		$dyeing_check_arr[$row[csf("booking_no_id")]][$row[csf("color_id")]]['batch_qnty']+=$dyeing_qty_arr[$row[csf("id")]];
	}
	unset($sql_dyeing_qty);
	
	$sql_finish_receive=sql_select("select a.booking_id, b.color_id,sum(b.receive_qnty) as receive_qty from inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.receive_basis !=9 and a.booking_without_order=1 and a.entry_form=37 and a.status_active=1 and a.is_deleted=0 $grey_del_booking_id_cond group by a.booking_id,b.color_id");
	
	foreach($sql_finish_receive as $row)
	{
		$finish_receive_arr[$row[csf("booking_id")]][$row[csf("color_id")]]+=$row[csf("receive_qty")];
	}
	unset($sql_finish_receive);
	$sql_cutting_issue=sql_select("select c.booking_no_id, c.color_id, sum(b.issue_qnty) as issue_qty from inv_issue_master a, inv_finish_fabric_issue_dtls b, pro_batch_create_mst c where a.id=b.mst_id and b.batch_id=c.id and a.entry_form=18 and c.booking_no_id>0 and c.booking_without_order=1 $finprod_booking_id_cond group by c.booking_no_id,c.color_id");
	foreach($sql_cutting_issue as $row)
	{
		$issue_to_cut_arr[$row[csf("booking_no_id")]][$row[csf("color_id")]]+=$row[csf("issue_qty")];
	}
	unset($sql_cutting_issue);
	$sql_style_reff=sql_select("select a.style_ref_no, a.id, a.item_name from sample_development_mst a where a.status_active=1 and a.is_deleted=0 ");
	foreach($sql_style_reff as $val)
	{
		$style_reff_arr[$val[csf("id")]]['style_reff_no']=$val[csf("style_ref_no")];
		$style_reff_arr[$val[csf("id")]]['item_name']=$val[csf("item_name")];
	}
	unset($sql_style_reff);
	
	$sql="select a.id,b.count_id from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.is_deleted=0 order by a.id";
	$data_array=sql_select($sql);
	$lib_yean_count_arr=array();
	$lib_yean_type_arr=array();
	if (count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$lib_yean_count_arr))
			{
				$lib_yean_count_arr[$row[csf('id')]]=$lib_yean_count_arr[$row[csf('id')]].", ".$yarn_count_details[$row[csf('count_id')]];
			}
			else
			{
				$lib_yean_count_arr[$row[csf('id')]]=$yarn_count_details[$row[csf('count_id')]];
			}
			if(array_key_exists($row[csf('id')],$lib_yean_type_arr))
			{
				$lib_yean_type_arr[$row[csf('id')]]=$lib_yean_type_arr[$row[csf('id')]].", ".$yarn_type[$row[csf('count_id')]];
			}
			else
			{
				$lib_yean_type_arr[$row[csf('id')]]=$yarn_type[$row[csf('count_id')]];
			}
		}
	}
	unset($data_array);
	//print_r($lib_yean_type_arr);


		foreach($result_mst_array as $bookingId=> $row)
		{
			$mm=1;
			foreach($result_dtls_array[$row[("booking_id")]] as $dts_row)
			{
				if($mm==1)
				{	
					$net_yarn_issue=$yarn_issue_arr[$row[("booking_id")]]-$yarn_issue_rtn_arr[$row[("booking_id")]];
					$buyer_wise_result[$row[("buyer_id")]]["yarn_issue"]+=$net_yarn_issue;
					$buyer_wise_result[$row[("buyer_id")]]["knitting_total"] +=($grey_knit_production_arr[$row[("booking_id")]]+$grey_knit_receive_arr[$row[("booking_id")]]);
					if ($booking_wise_roll_check[$bookingId]==1) // If production is roll lavel
					{
						$buyer_wise_result[$row[("buyer_id")]]["grey_issue"] +=$booking_wise_gray_data[$row[("booking_no")]]['gray_issue_qty'];
					}
					else
					{
						$buyer_wise_result[$row[("buyer_id")]]["grey_issue"] +=$grey_issue_arr[$row["booking_id"]];
					}
					$dtls_batch_qty=$batch_qty_arr_check[$row[("booking_id")]][$dts_row[("fabric_color")]]['batch_qnty'];
					$buyer_wise_result[$row[("buyer_id")]]["batch_qty"] +=$dtls_batch_qty;
					$dtls_dying_qty=$dyeing_check_arr[$row[("booking_id")]][$dts_row[("fabric_color")]]['batch_qnty'];
					$dtls_tot_dying_qty +=$dtls_dying_qty; 
					$buyer_wise_result[$row[("buyer_id")]]["dyeing_qty"] +=$dtls_dying_qty;
					$buyer_wise_result[$row["buyer_id"]]["finish_fabric_qty"] +=$dts_row[("finish_fabric_qty")];
					if ($booking_wise_roll_check[$bookingId]==1)
					{
						$finish_prod_rece_store_availabe=$booking_wise_gray_data[$booking_no]['fin_feb_rcb_by_store_qty'];
					}
					else
					{
						$finish_prod_rece_store_availabe=$fin_store_rec_prod[$row[("booking_id")]][$dts_row[("fabric_color")]];
					}
					$finish_parchase_rece_store=$fin_store_rec_purchase[$row[("booking_id")]][$dts_row[("fabric_color")]];
					$fabric_store_available=$finish_prod_rece_store_availabe+$finish_parchase_rece_store;
					$buyer_wise_result[$row["buyer_id"]]["fin_total_available"] +=$fabric_store_available;
					if ($booking_wise_roll_check[$bookingId]==1)
					{
						$buyer_wise_result[$row["buyer_id"]]["issue_to_cut"] +=$booking_wise_gray_data[$booking_no]['fin_feb_roll_issue_qty'];
					}
					else
					{
						$buyer_wise_result[$row["buyer_id"]]["issue_to_cut"] +=$issue_to_cut_arr[$row[("booking_id")]][$dts_row[("fabric_color")]];
					}     
				}
				else
				{

					$dtls_batch_qty=$batch_qty_arr_check[$row[("booking_id")]][$dts_row[("fabric_color")]]['batch_qnty'];
					$buyer_wise_result[$row[("buyer_id")]]["batch_qty"] +=$dtls_batch_qty;
					$dtls_dying_qty=$dyeing_check_arr[$row[("booking_id")]][$dts_row[("fabric_color")]]['batch_qnty'];
					$dtls_tot_dying_qty +=$dtls_dying_qty; 
					$buyer_wise_result[$row[("buyer_id")]]["dyeing_qty"] +=$dtls_dying_qty;
					$buyer_wise_result[$row["buyer_id"]]["finish_fabric_qty"] +=$dts_row[("finish_fabric_qty")];
					$finish_prod_rece_store=$fin_store_rec_prod[$row[("booking_id")]][$dts_row[("fabric_color")]];
					$finish_parchase_rece_store=$fin_store_rec_purchase[$row[("booking_id")]][$dts_row[("fabric_color")]];
					$fabric_store_available=$finish_prod_rece_store+$finish_parchase_rece_store;
					$buyer_wise_result[$row["buyer_id"]]["fin_total_available"] +=$fabric_store_available;
					$buyer_wise_result[$row["buyer_id"]]["issue_to_cut"] +=$issue_to_cut_arr[$row[("booking_id")]][$dts_row[("fabric_color")]];
				}
				$mm++;
			}
		}
	$gt_grey_available=0;
	$gt_dying_qty=0;
	$dtls_tot_dying_qty=0;

	foreach($sql_res as $row)
	{
		if ($booking_wise_roll_check[$row[csf("booking_id")]]==1)  
		{
			$issueQty = $booking_wise_gray_data[$row[csf("booking_id")]]['gray_issue_qty'];
		}
		else
		{
			$issueQty = $grey_issue_arr[$row[csf("booking_id")]]; 
		} 

		$net_transfer =0;
		if($sample_transectionArr[$row[csf("booking_id")]]['tranfer_in']>0 || $sample_transectionArr[$row[csf("booking_id")]]['tranfer_out']>0 )
		{
			$net_transfer = $sample_transectionArr[$row[csf("booking_id")]]['tranfer_in']-$sample_transectionArr[$row[csf("booking_id")]]['tranfer_out'];

		} 
		
		if(!in_array($row[csf("booking_id")],$temp_book_arr))
		{
			
			$temp_book_arr[$row[csf("booking_id")]]=$row[csf("booking_id")];			
			//$gt_yarn_issue+=$yarn_issue_arr[$row[csf("booking_id")]]-$yarn_issue_rtn_arr[$row[csf("booking_id")]];
			$gt_grey_available+=($grey_knit_production_arr[$row[csf("booking_id")]]+$grey_knit_receive_arr[$row[csf("booking_id")]]+$net_transfer)-$issueQty ;
			$gt_dying_qty+=$grey_delivery_stock[$row[csf("booking_id")]];
			$dtls_dying_qty=$dyeing_check_arr[$row[csf("booking_id")]][$dts_row[csf("fabric_color")]]['batch_qnty'];
			$dtls_tot_dying_qty +=$dtls_dying_qty; 


			//$gt_batch_qty +=$batch_qty_arr[$row[csf("booking_id")]]['batch_qnty']; 
		}		 
		 
	}


		?>
	<div style="width:2250px; margin-bottom:10px;">
	 	<div style="float:left;  margin-bottom:10px;">
		<table class="rpt_table" border="1" rules="all" width="1600" cellpadding="0" cellspacing="0">
			<thead>
	        	<tr>
					<th  colspan="16" align="center">Buyer Level Summary</th>
				</tr>
	        	<tr>
					<th width="40">SL</th>
	                <th width="80">Buyer Name</th>
					<th width="100">Grey Req.</th>
	                <th width="100">Yarn Issue</th>
	                <th width="100">Yarn Balance</th>
					<th width="100">Knitting Total</th>
					<th width="100">Knit Balance</th>
					<th width="100">Grey Issue</th>
					<th width="100">Batch Qnty</th>
					<th width="100">Batch Balance</th>
					<th width="100">Total Dyeing</th>
					<th width="100">Dyeing Balance</th>
					<th width="100">Fin. Fab Req.</th>
	                <th width="100">Fin. Fab total</th>
	                <th width="100">Fin. Fab Balance</th>
					<th>Issue to Cutting </th>
				</tr>
	        </thead>
	        <tbody>
	        <?
			$p=1;
			$gt_issue_cutting=0;
			$gt_finish_available=0;
			$gt_batch_qty=0; 
			$gt_yarn_issue=0; 
			$gt_dying_qty=0; 
			$dtls_tot_dying_qty=0; 
			foreach($buyer_wise_result as $row_result)
			{
				if ($p%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
	        	<tr bgcolor="<? echo $bgcolor; ?>">
					<td><? echo $p; ?></td>
	                <td><? echo $buyer_short_name_library[$row_result["buyer_id"]]; ?></td>
					<td align="right"><? echo number_format($row_result["grey_fabric_qnty"],2); $buyer_tot_feb_req+=$row_result["grey_fabric_qnty"];?></td>
	                <td align="right"><? echo number_format($row_result["yarn_issue"],2);  $buyer_tot_yarn_issue+=$row_result["yarn_issue"]; ?></td>
	                <td align="right"><? echo number_format(($row_result["grey_fabric_qnty"]-$row_result["yarn_issue"]),2); $buyer_tot_yarn_balance+=($row_result["grey_fabric_qnty"]-$row_result["yarn_issue"]); ?></td>
					<td align="right"><? echo number_format($row_result["knitting_total"],2); $buyer_tot_grey_knitting+=$row_result["knitting_total"]; ?></td>
					<td align="right"><? echo number_format(($row_result["grey_fabric_qnty"]-$row_result["knitting_total"]),2); $buyer_tot_grey_knitting_bal+=($row_result["grey_fabric_qnty"]-$row_result["knitting_total"]); ?></td>
					<td align="right"><? echo number_format($row_result["grey_issue"],2); $buyer_tot_grey_issue+=$row_result["grey_issue"];  ?></td>
					<td align="right"><? echo number_format($row_result["batch_qty"],2); $buyer_tot_batch_qty+=$row_result["batch_qty"]; ?></td>
					<td align="right"><? echo number_format(($row_result["grey_fabric_qnty"]-$row_result["batch_qty"]),2); $buyer_tot_batch_balance+=($row_result["grey_fabric_qnty"]-$row_result["batch_qty"]); ?></td>
					<td align="right"><? echo number_format($row_result["dyeing_qty"],2);  $buyer_tot_dyeing_qty+=$row_result["dyeing_qty"];  ?></td>
					

					<td align="right"><? echo number_format(($row_result["grey_fabric_qnty"]-$row_result["dyeing_qty"]),2); $buyer_tot_dyeing_balance+=($row_result["grey_fabric_qnty"]-$row_result["dyeing_qty"]); ?></td>




					<td align="right"><? echo number_format($row_result["finish_fabric_qty"],2);  $buyer_tot_finish_req_qty+=$row_result["finish_fabric_qty"];  ?></td>


	                <td align="right"><? echo number_format($row_result["fin_total_available"],2); $buyer_tot_finish_abable_qty+=$row_result["fin_total_available"];  ?></td>
	                <td align="right"><? echo number_format(($row_result["finish_fabric_qty"]-$row_result["fin_total_available"]),2); $buyer_tot_finish_balance+=($row_result["finish_fabric_qty"]-$row_result["fin_total_available"]);  ?></td>

					<td align="right"><? echo number_format($row_result["issue_to_cut"],2); $buyer_tot_cutting_qty+=$row_result["issue_to_cut"]; ?></td>
				</tr>
	            <?
	            $gt_issue_cutting+=$row_result["issue_to_cut"];
	            $gt_finish_available+=$row_result["fin_total_available"]; 
	            $gt_batch_qty+=$row_result["batch_qty"];
	            $gt_yarn_issue+=$row_result["yarn_issue"];
	            $gt_dying_qty+=$row_result["grey_issue"];
	            $dtls_tot_dying_qty+=$row_result["dyeing_qty"];
				$p++;
			}
			?>
	        </tbody>
	        <tfoot>
	        	<tr>
	                <th colspan="2">Total:</th>
					<th align="right"><? echo number_format($buyer_tot_feb_req,2); ?></th>
	                <th align="right"><? echo number_format($buyer_tot_yarn_issue,2); ?></th>
	                <th align="right"><? echo number_format($buyer_tot_yarn_balance,2); ?></th>
					<th align="right"><? echo number_format($buyer_tot_grey_knitting,2); ?></th>
					<th align="right"><? echo number_format($buyer_tot_grey_knitting_bal,2); ?></th>
					<th align="right"><? echo number_format($buyer_tot_grey_issue,2); ?></th>
					<th align="right"><? echo number_format($buyer_tot_batch_qty,2); ?></th>
					<th align="right"><? echo number_format($buyer_tot_batch_balance,2); ?></th>
					<th align="right"><? echo number_format($buyer_tot_dyeing_qty,2); ?></th>
					<th align="right"><? echo number_format($buyer_tot_dyeing_balance,2); ?></th>
					<th align="right"><? echo number_format($buyer_tot_finish_req_qty,2); ?></th>
	                <th align="right"><? echo number_format($buyer_tot_finish_abable_qty,2); ?></th>
	                <th align="right"><? echo number_format($buyer_tot_finish_balance,2); ?></th>
					<th align="right"><? echo number_format($buyer_tot_cutting_qty,2); ?> </th>
				</tr>
	        </tfoot>
	    </table>
	    </div>

		<div style="float:left; width:320px;  margin-left:20px;">
		    <table class="rpt_table" border="1" rules="all" width="400" cellpadding="0" cellspacing="0">
		        <thead>
		        	<tr>
		            	<th colspan="4">Summary</th>
		            </tr>
		            <tr>
		            	<th width="30">Sl</th>
		            	<th width="200">Particulars</th>
		                <th width="80">Quantity</th>
		                <th width="70">%</th>
		            </tr>
		        </thead>
		        <tbody>
		        	<tr>
		            	<td>1</td>
		            	<td>Total Yarn Required</td>
		                <td align="right"><? echo number_format($gt_yarn_grey_required,2); ?></td>
		                <td></td>
		            </tr>
		            <tr>
		            	<td>2</td>
		            	<td>Total Yarn Issued</td>
		                <td align="right"><? echo number_format($gt_yarn_issue,2); ?></td>
		                <td align="right"><? $yarn_issue_parcent=(($gt_yarn_issue/$gt_yarn_grey_required)*100); echo number_format($yarn_issue_parcent,2)."%"; ?></td>
		            </tr>
		            <tr>
		            	<td>3</td>
		            	<td ><strong> Total Issue Balance</strong></td>
		                <td align="right"><? $gt_issue_balance=$gt_yarn_grey_required-$gt_yarn_issue; echo number_format($gt_issue_balance,2); ?></td>
		                <td align="right"><? $issue_balance_parcentage=(($gt_issue_balance/$gt_yarn_grey_required)*100); echo number_format($issue_balance_parcentage,2)."%";  ?></td>
		            </tr>
		            <tr>
		            	<td>4</td>
		            	<td>Total Grey Fabric Required</td>
		                <td align="right"><? echo number_format($gt_yarn_grey_required,2); ?></td>
		                <td></td>
		            </tr>
		            <tr>
		            	<td>5</td>
		            	<td>Total Grey Fabric Available</td>
		                <td align="right"><? echo number_format($gt_grey_available,2); ?></td>
		                <td align="right"><? $grey_available_parcentage=(($gt_grey_available/$gt_yarn_grey_required)*100); echo number_format($grey_available_parcentage,2)."%";  ?></td>
		            </tr>
		            <tr>
		            	<td>6</td>
		            	<td>Total Grey Fabric Issued To Dye</td>
		                <td align="right"><? echo number_format($gt_dying_qty,2); ?></td>
		                <td align="right"><? $grey_dying_parcentage=(($gt_dying_qty/$gt_yarn_grey_required)*100); echo number_format($grey_dying_parcentage,2)."%";  ?></td>
		            </tr>
		         
		            
		            <tr>
		            	<td>7</td>
		            	<td><strong>Total Grey Fabric Issued Balance</strong></td>
		                <td align="right"><? $gt_grey_balance=$gt_yarn_grey_required-$gt_grey_available;  echo number_format($gt_grey_balance,2); ?></td>
		                <td align="right"><? $grey_balance_parcentage=(($gt_grey_balance/$gt_yarn_grey_required)*100); echo number_format($grey_balance_parcentage,2)."%";  ?></td>
		            </tr>
		           
		            <tr>
		            	<td>8</td>
		            	<td>Total Batch Qty.</td>
		                <td align="right"><? echo number_format($gt_batch_qty,2); ?></td>
		                <td align="right"></td>
		            </tr>
		             <tr>
		            	<td>9</td>
		            	<td><strong>Total Batch Balance To Grey</strong></td>
		                <td align="right"><? $total_batch_balance=$gt_yarn_grey_required-$gt_batch_qty; echo number_format($total_batch_balance,2); ?></td>
		                <td align="right"><? //$grey_batch_balance_parcentage=(($total_batch_balance/$gt_yarn_grey_required)*100); echo number_format($total_batch_balance,2)."%";  ?></td>
		            </tr>
		            <tr>
		            	<td>10</td>
		            	<td>Total Dyeing Qty</td>
		                <td align="right"><? echo number_format($dtls_tot_dying_qty,2); ?></td>
		                <td align="right"><? $grey_dying_parcentage=(($gt_dying_qty/$gt_yarn_grey_required)*100); echo number_format($grey_dying_parcentage,2)."%";  ?></td>
		            </tr>
		            <tr>
		            	<td>11</td>
		            	<td><strong>Total Dye Balance To Grey</strong></td>
		                <td align="right"><? $total_dying_balance=$gt_yarn_grey_required-$dtls_tot_dying_qty; echo number_format($total_batch_balance,2); ?></td>
		                <td align="right"><? $grey_dying_balance_parcentage=(($total_dying_balance/$gt_yarn_grey_required)*100); echo number_format($total_batch_balance,2)."%";  ?></td>
		            </tr>
		            <tr>
		            	<td>12</td>
		            	<td>Total Finish Fabric Required</td>
		                <td align="right"><? echo number_format($gt_finish_requir,2); ?></td>
		                <td></td>
		            </tr>
		            <tr>
		            	<td>13</td>
		            	<td>Total Finish Fabric Available</td>
		                <td align="right"><? echo number_format($gt_finish_available,2); ?></td>
		                <td align="right"><? $finish_available_parcentage=(($gt_finish_available/$gt_finish_requir)*100); echo number_format($finish_available_parcentage,2)."%";  ?></td>
		            </tr>
		            <tr>
		            	<td>14</td>
		            	<td><strong>Total Finish Fabric Balance</strong></td>
		                <td align="right"><? $gt_finish_balance=$gt_finish_requir-$gt_finish_available;  echo number_format($gt_finish_balance,2); ?></td>
		                <td align="right"><? $finish_balance_parcentage=(($gt_finish_balance/$gt_finish_requir)*100); echo number_format($finish_balance_parcentage,2)."%";  ?></td>
		            </tr>
		            <tr>
		            	<td>15</td>
		            	<td>Total Issue to Cutting</td>
		                <td align="right"><? echo number_format($gt_issue_cutting,2); ?></td>
		                <td align="right"><? $finish_issue_cutting_parcentage=(($gt_issue_cutting/$gt_finish_requir)*100); echo number_format($finish_issue_cutting_parcentage,2)."%";  ?></td>
		            </tr>
		            <tr>
		            	<td>16</td>
		            	<td><strong>Total Issue Balance</strong></td>
		                <td align="right"><? $gt_finish_issue_cut_balance=$gt_finish_requir-$gt_issue_cutting;  echo number_format($gt_finish_issue_cut_balance,2); ?></td>
		                <td align="right"><? $finish_issue_cut_bal_parcentage=(($gt_finish_issue_cut_balance/$gt_finish_requir)*100); echo number_format($finish_issue_cut_bal_parcentage,2)."%";  ?></td>
		            </tr>
		        </tbody>
		    </table>
	    </div>
	</div>
	<table class="rpt_table" border="1" rules="all" width="4950" cellpadding="0" cellspacing="0">
		<thead>
			<tr>
				<th colspan="16">Booking Details</th>
				<th colspan="5">Yarn Details</th>
                <th colspan="4">Knitting Production</th>
				<th colspan="6">Grey Fabric Store</th>
                <th colspan="4">Dyeing Production</th>
				<th colspan="5">Finish Fabric Production</th>
                <th colspan="7">Finish Fabric Store</th>
                <th rowspan="2">Fabric Description</th>
			</tr>
			<tr>
				<th width="40">SL</th>
				<th width="65">Booking Year</th>
                <th width="65">Booking No</th>
				
                <th width="80">Buyer Name</th>
                <th width="80">Style Ref.</th>
                <th width="80">Internal Ref.</th>
                <th width="80">Item Name</th>
                <th width="80">W/O Booking Date<br/><font style="font-size:9px; font-weight:100">Days Count</font></th>
                <th width="80">W/O Delivery Date<br/><font style="font-size:9px; font-weight:100">(Days Count)</font></th>
                <th width="90">Yarn Delivey Date<br/><font style="font-size:9px; font-weight:100">Days Count</font></th>
                <th width="100">Knitting Production Date<br/><font style="font-size:9px; font-weight:100">(Days Count)</font></th>
                <th width="100">Knitting Delivery Date<br/><font style="font-size:9px; font-weight:100">(Days Count)</font></th>
                <th width="100">Batch Date<br/><font style="font-size:9px; font-weight:100">(Days Count)</font></th>
                <th width="100">Finished Fabric Production Date<br/><font style="font-size:9px; font-weight:100">(Days Count)</font></th>
                <th width="100">Finished Fabric Delivery Date<br/><font style="font-size:9px; font-weight:100">(Days Count)</font></th>
                
                <th width="80">Count</th>
                <th width="100">Composition</th>
                <th width="80">Type</th>
				<th width="110">Required<br/><font style="font-size:9px; font-weight:100">(As Per Booking)</font></th>
                <th width="110">Issued</th>
                <th width="110">Issue Balance<br/><font style="font-size:9px; font-weight:100">Grey Req-Yarn Issue</font></th>
				<th width="110">Knitted Production</th>
                <th width="110">Knit Balance</th>
                <th width="110">Grey Fab Delv. To Store</th>
                <th width="110">Grey in Knitting Floor</th>
				
                <th width="110">Grey Rcvd Prod.</th>
                <th width="110">Grey Rcvd - Purchase</th>
                <th width="110">Net Transfer</th>
                <th width="110">Fabric Available</th>
                <th width="110">Receive Balance</th>
				<th width="110">Grey Issue</th>
                
                <th width="110">Fabric Color</th>
				<th width="110">Batch Qnty</th>
				<th width="110">Dye Qnty</th>
                <th width="110">Balance</th>
				<th width="110">Required Qty (As per Booking)</th>
				
				<th width="110">Production Qty.</th>
                <th width="110">Balance Qty</th>
                <th width="110">Finish Fab. Delv. To Store</th>
                <th width="110">Fabric in Prod. Floor</th>
                
                <th width="110">Received - Prod.</th>
                <th width="110">Received - Purchase</th>
                <th width="110">Fabric Available</th>
                
                <th width="110">Receive Balance</th>
                <th width="110">Issue to Cutting</th>
                <th width="110">Yet to Issue</th>
				<th width="110">Fabric Stock/ Left Over</th>
			</tr>
		</thead>
	</table>
	<div style="width:4975px; overflow-y:scroll; max-height:300px" id="scroll_body">
    <table class="rpt_table" border="1" rules="all" width="4950" cellpadding="0" cellspacing="0" id="table_body">
        <tbody>
        <?
		$i=1;
		foreach($result_mst_array as $bookingID => $row)
		{
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
        	<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
				<td width="40"><? echo $i; ?></td>
				<td width="65" align="center"><p><? echo $row[("wo_year")]; ?></p></td>
                <td width="65" align="center"><p><a href='##' style='color:#000' onclick="generate_order_report('<? echo $row['booking_no'];?> ','<? echo $row['company_id']; ?>','<? echo $row['is_approved'];?>')"><? echo $row[("booking_no_prefix_num")]; ?></a></p></td>
			
                <td width="80"><p><? echo $buyer_short_name_library[$row[("buyer_id")]]; ?></p></td>
                <td width="80" style="word-break:break-all"><? if($row[("style_id")]!="") echo $style_reff_arr[$row[("style_id")]]['style_reff_no']; ?></td>
                <td width="80" style="word-break:break-all"><? echo $row[("internal_ref")]; ?></td>
                <td width="80"><p><? if($row[("style_id")]!="") echo $garments_item[$style_reff_arr[$row[("style_id")]]['item_name']]; ?></p></td>
                <td width="80" align="center"><p><? echo  change_date_format($row["booking_date"]); ?></p></td>
                <td width="80" align="center"><p><? echo  change_date_format($row["delivery_date"]); ?></p></td>
                
                <td width="90" align="center"><p><? if($yarn_issue_date[$row["booking_id"]]!=""){ $days_yarn_issue=datediff("d",$row["booking_date"],$yarn_issue_date[$row["booking_id"]]); echo change_date_format($yarn_issue_date[$row["booking_id"]])."<br/>"; echo $days_yarn_issue." days";}// echo $yarn_issue_date[$row["booking_id"]]; ?></p></td>
                <td width="100" align="center"><p>
				<? if($grey_knit_production_date[$row["booking_id"]]!=""){ $knit_production_date=datediff("d",$row["booking_date"],$grey_knit_production_date[$row["booking_id"]]); echo change_date_format($grey_knit_production_date[$row["booking_id"]])."<br/>";  echo $knit_production_date." days";} 
				?></p></td>
                <td width="100" align="center"><p><? if($grey_delivery_date[$row["booking_id"]]!=""){ $days_grey_delivery=datediff("d",$row["booking_date"],$grey_delivery_date[$row["booking_id"]]); echo change_date_format($grey_delivery_date[$row["booking_id"]])."<br/>"; echo $days_grey_delivery." days";} ?></p></td>
                <td width="100" align="center"><p><? if($batch_qty_arr[$row["booking_id"]]['batch_date']!=""){ $days_batch=datediff("d",$row["booking_date"],$batch_qty_arr[$row["booking_id"]]['batch_date']); echo change_date_format($batch_qty_arr[$row["booking_id"]]['batch_date'])."<br/>"; echo $days_batch." days";} ?></p></td> 
                <td width="100" align="center"><p><? if($finish_prodction_date[$row["booking_id"]]!=""){ $date_finish_prodction=datediff("d",$row["booking_date"],$finish_prodction_date[$row["booking_id"]]); echo change_date_format($finish_prodction_date[$row["booking_id"]])."<br/>"; echo $date_finish_prodction." days";} ?></p></td>
                <td width="100" align="center"><p><? if($finish_prodction_delivery_date[$row["booking_id"]]!=""){ $date_finish_prodctiondelivery=datediff("d",$row["booking_date"],$finish_prodction_delivery_date[$row["booking_id"]]); echo change_date_format($finish_prodction_delivery_date[$row["booking_id"]])."<br/>"; echo $date_finish_prodctiondelivery." days";} ?></p></td>
				
                <?
				//details_part start here
				$m=1;
				//$dtls_tot_gery_available_all = 0;
				foreach($result_dtls_array[$row[("booking_id")]] as $dts_row)
				{
					if($m==1)
					{
						?>
                        <td width="80"><p><? echo $lib_yean_count_arr[$dts_row[("lib_yarn_count_deter_id")]]; ?></p></td>
                        <td width="100"><p><? echo $dts_row[("composition")]; ?></p></td>
                        <td width="80"><p><?   echo $lib_yean_type_arr[$dts_row[("lib_yarn_count_deter_id")]]; ?></p></td>
                        <td width="110" align="right"><p><? echo number_format($row[("grey_fabric_qnty")],2); $dtls_tot_gery_req+=$row[("grey_fabric_qnty")]; ?></p></td>
                        <td width="110" align="right"><p><a  href="##" onclick="openmypage('<? echo $row[("booking_id")]; ?>','yarn_issue','')">
						<?
						$net_yarn_issue=$yarn_issue_arr[$row[("booking_id")]]-$yarn_issue_rtn_arr[$row[("booking_id")]];
						echo number_format($net_yarn_issue,2); $dtls_tot_yarn_issue+=$net_yarn_issue; $buyer_wise_result[$row[("buyer_id")]]["yarn_issue"]+=$net_yarn_issue;
                        ?></a>
                        </p></td>
                        <td width="110" align="right"><p><? $yarn_balance=$row[("grey_fabric_qnty")]-$net_yarn_issue; echo number_format($yarn_balance,2); $dtls_tot_yarn_balance += $yarn_balance; ?></p></td>
                        <td width="110" align="right"><p>
                        <a  href="##" onclick="openmypage('<? echo $row[("booking_id")]; ?>','grey_receive','')">
                            <?
							$buyer_wise_result[$row[("buyer_id")]]["knitting_total"] +=($grey_knit_production_arr[$row[("booking_id")]]+$grey_knit_receive_arr[$row[("booking_id")]]);
                                echo number_format($grey_knit_production_arr[$row[("booking_id")]],2);
								$dtls_tot_gery_knit_product+=$grey_knit_production_arr[$row[("booking_id")]]; 
                            ?>
                        </a>
                       </p></td>
                       <td width="110" align="right"><p><? 
						  $grey_total_available=($grey_knit_production_arr[$row[("booking_id")]]+$grey_knit_receive_arr[$row[("booking_id")]]); 
						  $grey_balance=$row[("grey_fabric_qnty")]-$grey_total_available; echo number_format($grey_balance,2);
						  $dtls_tot_gray_bal +=$grey_balance;
						?></p></td>
	                    <td width="110" align="right"><p>
							<a  href="##" onclick="openmypage('<? echo $row[("booking_id")]; ?>','grey_fabric_delivery_to_store','')">
							<? echo number_format($grey_delivery_stock[$row[("booking_id")]],2);
							 $dtls_tot_gery_delivery+=$grey_delivery_stock[$row[("booking_id")]];  ?>
	                        </a>
	                        </p>
	                    </td>
	                    <td width="110" align="right"><p><? echo number_format($grey_knit_production_arr[$row[("booking_id")]],2);  ?></p></td>
	                        
	                    <td width="110" align="right"><p>
                    	<?
                    		if ($booking_wise_roll_check[$row[("booking_id")]]==1)
                    		{
                    			$id=$booking_wise_gray_data[$booking_no]['barcodes'];
                    		}
                    		else
                    		{
                    			$id=$row[("booking_id")]."_withoutRoll";
                    		}
                    	?>
						<a  href="##" onclick="openmypage('<? echo $id; ?>','grey_receive_prod','')">
						<?
						//echo $booking_wise_roll_check[$row[("booking_no")]];	 
						if ($booking_wise_roll_check[$row[("booking_id")]]==1) // If production is roll lavel
						{
							echo number_format($booking_wise_gray_data[$row[("booking_no")]]['gray_rcv_qty'],2);
							
							$dtls_tot_grey_knit_receive_prod+=$booking_wise_gray_data[$row[("booking_no")]]['gray_rcv_qty'];
						}
						else
						{
							$all_receive_id="";
							//print_r($grey_knit_receive_booking[$row[("booking_id")]]);
							$all_receive_id=array_filter(array_unique(explode(",",$grey_knit_receive_booking[$row[("booking_id")]])));
							//print_r($all_receive_id);
							$all_receive_value=0;
							foreach($all_receive_id as $row_id)
							{
								//echo $row_id;
								$all_receive_value+=$grey_knit_receive_prod_arr[$row_id];
							}
							echo number_format($all_receive_value,2);
							$dtls_tot_grey_knit_receive_prod+=$all_receive_value;
						}
							
						?>
                        </a></p>
	                    </td>
	                    <td width="110" align="right"><p><?  //echo number_format($grey_knit_production_arr[$row[("booking_id")]],2);  ?></p>
	                    </td>
	                    <td width="110" align="right"><p><? $net_transfer =0;
						if($sample_transectionArr[$row[("booking_id")]]['tranfer_in']>0 || $sample_transectionArr[$row[("booking_id")]]['tranfer_out']>0 )
						{
							$net_transfer = $sample_transectionArr[$row[("booking_id")]]['tranfer_in']-$sample_transectionArr[$row[("booking_id")]]['tranfer_out'];
							$dtls_tot_net_transfer+= $sample_transectionArr[$row[("booking_id")]]['tranfer_out']-$sample_transectionArr[$row[("booking_id")]]['tranfer_in'] ;		
						}
						?>
						<a href="##" onclick="opengreyNetTransfer('<? echo $row["booking_id"]; ?>','<? echo $cbo_company_name; ?>','grey_fabric_transfer_transection')"><? echo $net_transfer;?></a>
	                    </p>
						</td>
	                    <td width="110" align="right" title="(Prod+Rec+Trans)-Issue">
	                    	<p>
							<?
							
							if ($booking_wise_roll_check[$row[("booking_id")]]==1) // If production is roll lavel
							{
								$issueQty = $booking_wise_gray_data[$row[("booking_id")]]['gray_issue_qty'];
							}
							else
							{
								$issueQty = $grey_issue_arr[$row[("booking_id")]]; 
							} 
							
							$gray_total_available_all = ($grey_knit_production_arr[$row[("booking_id")]]+$grey_knit_receive_arr[$row[("booking_id")]]+$net_transfer)-$issueQty; // $booking_wise_gray_data[$row[("booking_no")]]['gray_rcv_qty']
						    
							echo number_format($gray_total_available_all,2);  
							$dtls_tot_gray_available_all += $gray_total_available_all;  
							?>
	                        </p>
						</td>
	                    <td width="110" align="right">
	                    <p><? $grey_balance=$row[("grey_fabric_qnty")]-$grey_total_available; 
						echo number_format($grey_balance,2); 
						$dtls_tot_gray_balance +=$grey_balance; ?>
	                    </p>
	                    </td>
	                        
	                    <td width="110" align="right"><p>
	                    	<?
	                    		$booking_nos=str_replace("'","",$booking_no);
	                    		if ($booking_wise_roll_check[$row[("booking_id")]]==1)
	                    		{
	                    			// $id=$booking_wise_gray_data[$row[("booking_no")]]['barcodes'];
	                    			$id=rtrim($booking_wise_gray_data[$booking_nos]['barcodes'],',');
	                    		}
	                    		else
	                    		{
	                    			$id=$row[("booking_id")]."_withoutRoll";
	                    		}
	                    	?>
	                        <a  href="##" onclick="openmypage('<? echo $id; ?>','grey_issue','')">
							<? 
								//echo $booking_wise_roll_check[$row[("booking_no")]];
	                            if ($booking_wise_roll_check[$row[("booking_id")]]==1) // If production is roll lavel
	                            {
	                                echo number_format($booking_wise_gray_data[$row[("booking_no")]]['gray_issue_qty'],2);
	                                $dtls_tot_gray_issue+=$booking_wise_gray_data[$row[("booking_no")]]['gray_issue_qty'];
									$buyer_wise_result[$row[("buyer_id")]]["grey_issue"] +=$booking_wise_gray_data[$row[("booking_no")]]['gray_issue_qty'];
	                            }
	                            else
	                            {
	                                echo number_format($grey_issue_arr[$row["booking_id"]],2); 
	                                $dtls_tot_gray_issue+=$grey_issue_arr[$row["booking_id"]]; 
									$buyer_wise_result[$row[("buyer_id")]]["grey_issue"] +=$grey_issue_arr[$row["booking_id"]];
	                            } 
	                        ?>
	                        </a>
	                        </p>
	                    </td>
	                        <td width="110"><p><? echo $color_array[$dts_row[("fabric_color")]]; ?></p> </td>
							<td width="110" align="right">
								<p>
								 <a  href="##" onclick="open_febric_receive_status_color_wise_popup('<? echo $row[("booking_id")]; ?>','batch_qty_popup','<? echo $dts_row[("fabric_color")]; ?>')">
								<?
								$dtls_batch_qty=$batch_qty_arr_check[$row[("booking_id")]][$dts_row[("fabric_color")]]['batch_qnty'];
								$dy_check_id=$dyeing_check_arr[$batch_qty_arr_check[$row[("booking_id")]][$dts_row[("fabric_color")]]['batch_id']];
								echo number_format($dtls_batch_qty,2); $dtls_tot_batch_qty +=$dtls_batch_qty; 
								$buyer_wise_result[$row[("buyer_id")]]["batch_qty"] +=$dtls_batch_qty;
								 ?>
		                         </a></p>
		                     </td>
	                        <td width="110" align="right">
		                        <a  href="##" onclick="openmypage('<? echo $row[("booking_id")]; ?>','dying_qty_popup','')">
								<?
								$dtls_dying_qty=$dyeing_check_arr[$row[("booking_id")]][$dts_row[("fabric_color")]]['batch_qnty'];
		                        echo number_format($dtls_dying_qty,2);
								$dtls_tot_dying_qty +=$dtls_dying_qty; 
								$buyer_wise_result[$row[("buyer_id")]]["dyeing_qty"] +=$dtls_dying_qty;

		                        ?>
		                        </a>
	                        </td>
							<td width="110" align="right"><p>
		                        <?
								$dying_balance=$dtls_batch_qty-$dtls_dying_qty; $dtls_tot_dying_balance+=$dying_balance;
								echo number_format($dying_balance,2);  $total_dying_balance_qty +=$dying_balance;  

								?>
		                        </p>
	                    	</td>
	                        <td width="110" align="right"><p><? echo number_format($dts_row[("finish_fabric_qty")],2); $dtls_tot_fin_req_qty +=$dts_row[("finish_fabric_qty")];

	                        $buyer_wise_result[$row["buyer_id"]]["finish_fabric_qty"] +=$dts_row[("finish_fabric_qty")];
	                         ?></p></td>
	                        
							<td width="110" align="right"><p>
								<?
									if ($booking_wise_roll_check[$row[("booking_id")]]==1)
		                    		{
		                    			$id=$booking_wise_gray_data[$booking_no]['barcodes'];
		                    		}
		                    		else
		                    		{
		                    			$id=$row[("booking_id")]."_withoutRoll";
		                    		}
								?>
		                        <a  href="##" onclick="open_febric_receive_status_color_wise_popup('<? echo $id; ?>','finish_feb_pord','<? echo $dts_row[("fabric_color")]; ?>')">
								<? 
									if ($booking_wise_roll_check[$row[("booking_id")]]==1) //if production is roll level
									{
										echo number_format($booking_wise_gray_data[$row[("booking_no")]]['gray_fin_feb_prod_qty'],2);
										$dtls_tot_fin_prod_qnty +=$booking_wise_gray_data[$row[("booking_no")]]['gray_fin_feb_prod_qty'];
										//$buyer_wise_result[$row["buyer_id"]]["finish_fabric_qty"] +=$booking_wise_gray_data[$row[("booking_no")]]['gray_fin_feb_prod_qty'];
									}
									else
									{
										echo number_format($finishfab_product_arr[$row[("booking_id")]][$dts_row[("fabric_color")]],2);
										$dtls_tot_fin_prod_qnty +=$finishfab_product_arr[$row[("booking_id")]][$dts_row[("fabric_color")]]; 
										//$buyer_wise_result[$row["buyer_id"]]["finish_fabric_qty"] +=$finishfab_product_arr[$row[("booking_id")]][$dts_row[("fabric_color")]];
									} 
								?>
		                        </a>
		                        </p>
	                    	</td>
	                        <td width="110" align="right"><p><? 
							 $finish_balance=$dts_row[("finish_fabric_qty")]-$finishfab_product_arr[$row[("booking_id")]][$dts_row[("fabric_color")]]; echo number_format($finish_balance,2); $tot_fin_balance +=$finish_balance; //$dtls_tot_fin_balance +=$finish_balance;  ?></p></td>
							<td width="110" align="right"><p>
								<?
									if ($booking_wise_roll_check[$row[("booking_id")]]==1)
		                    		{
		                    			$id=$booking_wise_gray_data[$booking_no]['barcodes'];
		                    		}
		                    		else
		                    		{
		                    			$id=$row[("booking_id")]."_withoutRoll";
		                    		}
								?>
								<a href="##" onclick="openmypage('<? echo $id; ?>','finish_fabric_delivery_to_store','')">
								<? 
									if ($booking_wise_roll_check[$row[("booking_id")]]==1)
									{
										echo number_format($booking_wise_gray_data[$row[("booking_no")]]['fin_feb_delivery_store_qty'],2);
										$dtls_tot_fin_delivery_qty +=$booking_wise_gray_data[$row[("booking_no")]]['fin_feb_delivery_store_qty'];
									}
									else
									{
									 	echo number_format($finish_prodction_delivery_qty[$row[("booking_id")]],2);
									 	$dtls_tot_fin_delivery_qty +=$finish_prodction_delivery_qty[$row[("booking_id")]]; 
									}
								?>
		                        </a>
		                         </p>
		                     </td>
							
	                        <td width="110" align="right"><p><? 
								$fabric_in_prod_floor=$finishfab_product_arr[$row[("booking_id")]][$dts_row[("fabric_color")]]-$finish_prodction_delivery_qty[$row[("booking_id")]];
								 echo number_format($fabric_in_prod_floor,2); $dtls_tot_fabric_in_prod_floor +=$fabric_in_prod_floor;  ?></p>
							</td>

		                    <td width="110" align="right"><p>
		                    	<?
		                    		if ($booking_wise_roll_check[$row[("booking_id")]]==1)
									{
										$id=$booking_wise_gray_data[$booking_no]['barcodes'];
									}
									else
									{
										$id=$row[("booking_id")]."_withoutRoll";
									}
		                    	?>
								<a  href="##" onclick="open_febric_receive_status_color_wise_popup('<? echo $id; ?>','finish_fabric_receive_by_store','<? echo $dts_row[("fabric_color")]; ?>')">
								<?
								if ($booking_wise_roll_check[$row[("booking_id")]]==1)
								{
									echo number_format($booking_wise_gray_data[$booking_no]['fin_feb_rcb_by_store_qty'],2);
									$dtls_tot_finish_prod_rece_store +=$booking_wise_gray_data[$booking_no]['fin_feb_rcb_by_store_qty'];
									$finish_prod_rece_store_availabe=$booking_wise_gray_data[$booking_no]['fin_feb_rcb_by_store_qty'];
								}
								else
								{
									$finish_prod_rece_store=$fin_store_rec_prod[$row[("booking_id")]][$dts_row[("fabric_color")]]; 
								 	echo number_format($finish_prod_rece_store,2); 
								 	$dtls_tot_finish_prod_rece_store +=$finish_prod_rece_store; 
								 	$finish_prod_rece_store_availabe=$fin_store_rec_prod[$row[("booking_id")]][$dts_row[("fabric_color")]];
								} 
								
								//$buyer_wise_result[$row["buyer_id"]]["fin_total_available"] +=$finishfab_product_arr[$row[("booking_id")]][$dts_row[("fabric_color")]]+$finishfab_product_arr[$row[("booking_id")]][$dts_row[("fabric_color")]];
								 ?>
		                         </a></p>
		                    </td>

	                        <td width="110" align="right"><p><?  $finish_parchase_rece_store=$fin_store_rec_purchase[$row[("booking_id")]][$dts_row[("fabric_color")]]; echo number_format($finish_parchase_rece_store,2); $dtls_tot_finish_parchase_rece_store +=$finish_parchase_rece_store; ?></p>
	                        </td>
	                        
	                        <td width="110" align="right"><p><? $fabric_store_available=$finish_prod_rece_store_availabe+$finish_parchase_rece_store; echo number_format($fabric_store_available,2); $dtls_tot_fabric_store_available +=$fabric_store_available; 
	                            $buyer_wise_result[$row["buyer_id"]]["fin_total_available"] +=$fabric_store_available;
	                         ?></p></td>
	                       
	                        <td width="110" align="right"><p><?
							$finish_total_available=$finish_prod_rece_store_availabe+$finish_parchase_rece_store;
	                         $finish_balance=$dts_row[("finish_fabric_qty")]-$finish_total_available; echo number_format($finish_balance,2); $dtls_tot_fin_balance +=$finish_balance;  ?></p></td>
	                        
	                        <td width="110" align="right"><p>
		                        <?
		                        	if ($booking_wise_roll_check[$row[("booking_id")]]==1)
									{
										$id=$booking_wise_gray_data[$booking_no]['barcodes'];
									}
									else
									{
										$id=$row[("booking_id")]."_withoutRoll";
									}
		                        ?>
		                        <a  href="##" onclick="open_febric_receive_status_color_wise_popup('<? echo $id; ?>','issue_to_cut','<? echo $dts_row[("fabric_color")]; ?>')">
								<? 
								if ($booking_wise_roll_check[$row[("booking_id")]]==1)
								{
									echo number_format($booking_wise_gray_data[$booking_no]['fin_feb_roll_issue_qty'],2);
									$dtls_tot_cutting_qty +=$booking_wise_gray_data[$booking_no]['fin_feb_roll_issue_qty'];
									$buyer_wise_result[$row["buyer_id"]]["issue_to_cut"] +=$booking_wise_gray_data[$booking_no]['fin_feb_roll_issue_qty'];
								}
								else
								{	
									echo number_format($issue_to_cut_arr[$row[("booking_id")]][$dts_row[("fabric_color")]],2); 
									$dtls_tot_cutting_qty +=$issue_to_cut_arr[$row[("booking_id")]][$dts_row[("fabric_color")]];
									$buyer_wise_result[$row["buyer_id"]]["issue_to_cut"] +=$issue_to_cut_arr[$row[("booking_id")]][$dts_row[("fabric_color")]];
								}
								?>
		                        </a>
		                        </p>
	                    	</td>

	                        <td width="110" align="right"><p>
								<? 
								$left_over=$finish_prod_rece_store-$issue_to_cut_arr[$row[("booking_id")]][$dts_row[("fabric_color")]];
								echo number_format($left_over,2); $dtls_tot_left_over +=$left_over; 
								?>
		                        </p>
	                    	</td>
	                        <td width="110" align="right"><p><?
		                       	//$yet_to_issue=$row[("grey_fabric_qnty")]-$issue_to_cut_arr[$row[("booking_id")]][$dts_row[("fabric_color")]];
								
								$yet_to_issue=$fabric_store_available-$issue_to_cut_arr[$row[("booking_id")]][$dts_row[("fabric_color")]];
								
								echo number_format($yet_to_issue,2); $dtls_tot_yet_to_issue +=$yet_to_issue; 
		                        ?>
		                        </p>
	                    	</td>
	                        <td ><? echo implode(',',$dts_row['fabric_description']); ?></td>
						</tr>
						<?
					}
					else
					{
						?>
                        <tr>
                            <td width="40">&nbsp;</td>
                            <td width="65">&nbsp;</td>
                            <td width="65">&nbsp;</td>
                            
                            <td width="80">&nbsp;</td>
                            <td width="80">&nbsp;</td>
                            <td width="80">&nbsp;</td>
                            <td width="80">&nbsp;</td>
                            <td width="80">&nbsp;</td>
                            <td width="80">&nbsp;</td>
                            <td width="90">&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            
                            <td width="80"><p><? echo $lib_yean_count_arr[$dts_row[("lib_yarn_count_deter_id")]]; ?></p></td>
                        	<td width="100"><p><? echo $dts_row[("composition")]; ?></p></td>
                        	<td width="80"><p><?   echo $lib_yean_type_arr[$dts_row[("lib_yarn_count_deter_id")]]; ?></p></td>
                            <td width="110" align="right">&nbsp;</td>
                            <td width="110" align="right">&nbsp;</td>
                            <td width="110" align="right">&nbsp;</td>
                            <td width="110" align="right">&nbsp;</td>
                            <td width="110" align="right">&nbsp;</td>
                            <td width="110" align="right">&nbsp;</td>
                            <td width="110" align="right">&nbsp;</td>
                            <td width="110" align="right">&nbsp;</td>
                            <td width="110" align="right">&nbsp;</td>
                            <td width="110" align="right">&nbsp;</td>
                            <td width="110" align="right">&nbsp;</td>
                            <td width="110" align="right">&nbsp;</td>
                            <td width="110"><p><? //echo $color_array[$dts_row[("fabric_color")]]; ?></p> </td>
                            <td width="110"><p><? echo $color_array[$dts_row[("fabric_color")]]; ?></p> </td>
                            <td width="110" align="right">
	                            <p>
	                             <a  href="##" onclick="open_febric_receive_status_color_wise_popup('<? echo $row[("booking_id")]; ?>','batch_qty_popup','<? echo $dts_row[("fabric_color")]; ?>')">
	                            <?
	                            $dtls_batch_qty=$batch_qty_arr_check[$row[("booking_id")]][$dts_row[("fabric_color")]]['batch_qnty'];
	                            $dy_check_id=$dyeing_check_arr[$batch_qty_arr_check[$row[("booking_id")]][$dts_row[("fabric_color")]]['batch_id']];
	                            echo number_format($dtls_batch_qty,2); $dtls_tot_batch_qty +=$dtls_batch_qty; 
	                            	$buyer_wise_result[$row[("buyer_id")]]["batch_qty"] +=$dtls_batch_qty;
	                             ?>
	                             </a></p>
	                         </td>
                            <td width="110" align="right"><p>
	                            <a  href="##" onclick="openmypage('<? echo $row[("booking_id")]; ?>','dying_qty_popup','')">
	                            <?
	                            $dtls_dying_qty=$dyeing_check_arr[$row[("booking_id")]][$dts_row[("fabric_color")]]['batch_qnty'];
	                            echo number_format($dtls_dying_qty,2);
	                            $dtls_tot_dying_qty +=$dtls_dying_qty; 
	                            //echo number_format($finishfab_product_arr[$row[("booking_id")]][$dts_row[("fabric_color")]],2); 
	                            $dtls_tot_fin_prod_qty +=$finishfab_product_arr[$row[("booking_id")]][$dts_row[("fabric_color")]];  
	                            $buyer_wise_result[$row[("buyer_id")]]["dyeing_qty"] +=$dtls_dying_qty;

	                            ?>
	                            </a>
	                            </p>
	                        </td>

	                        <td width="110" align="right"><p>
		                        <?
								$dying_balance=$dtls_batch_qty-$dtls_dying_qty; $dtls_tot_dying_balance+=$dying_balance;
								echo number_format($dying_balance,2);  $total_dying_balance_qty +=$dying_balance;  
								?>
		                        </p>
	                    	</td>
	                        <td width="110" align="right"><p><? echo number_format($dts_row[("finish_fabric_qty")],2); $dtls_tot_fin_req_qty +=$dts_row[("finish_fabric_qty")]; 

	                        $buyer_wise_result[$row["buyer_id"]]["finish_fabric_qty"] +=$dts_row[("finish_fabric_qty")];
	                        ?></p></td>
							<td width="110" align="right"><p>
								<?
									if ($booking_wise_roll_check[$row[("booking_no")]]==1)
		                    		{
		                    			$id=$booking_wise_gray_data[$booking_no]['barcodes'];
		                    		}
		                    		else
		                    		{
		                    			$id=$row[("booking_id")]."_withoutRoll";
		                    		}
								?>
		                        <a  href="##" onclick="open_febric_receive_status_color_wise_popup('<? echo $id; ?>','finish_feb_pord','<? echo $dts_row[("fabric_color")]; ?>')">
								<? 
									if ($booking_wise_roll_check[$row[("booking_no")]]==1) //if production is roll level
									{
										echo number_format($booking_wise_gray_data[$row[("booking_no")]]['gray_fin_feb_prod_qty'],2);
										$dtls_tot_fin_prod_qnty +=$booking_wise_gray_data[$row[("booking_no")]]['gray_fin_feb_prod_qty'];
										//$buyer_wise_result[$row["buyer_id"]]["finish_fabric_qty"] +=$booking_wise_gray_data[$row[("booking_no")]]['gray_fin_feb_prod_qty'];
									}
									else
									{
										echo number_format($finishfab_product_arr[$row[("booking_id")]][$dts_row[("fabric_color")]],2);
										$dtls_tot_fin_prod_qnty +=$finishfab_product_arr[$row[("booking_id")]][$dts_row[("fabric_color")]]; 
										//$buyer_wise_result[$row["buyer_id"]]["finish_fabric_qty"] +=$finishfab_product_arr[$row[("booking_id")]][$dts_row[("fabric_color")]];
										
									} 
								?>
		                        </a>
		                        </p>
	                    	</td>

                            <td width="110" align="right"><p><? 
                             $finish_balance=$dts_row[("finish_fabric_qty")]-$finishfab_product_arr[$row[("booking_id")]][$dts_row[("fabric_color")]]; echo number_format($finish_balance,2); $tot_fin_balance +=$finish_balance;  ?></p></td>
                            <td width="110" align="right"><p>
	                            <a  href="##" onclick="openmypage('<? echo $row[("booking_id")]; ?>','finish_fabric_delivery_to_store','')">
	                            <? 
	                             echo number_format($finish_prodction_delivery_qty[$row[("booking_id")]],2); $dtls_tot_fin_delivery_qty +=$finish_prodction_delivery_qty[$row[("booking_id")]]; ?>
	                             </a>
	                             </p>
                         	</td>
                            
                            <td width="110" align="right"><p><? 
	                            $fabric_in_prod_floor=$finish_product_arr[$row[("booking_id")]][$dts_row[("fabric_color")]]-$finish_prodction_delivery_qty[$row[("booking_id")]];
	                             echo number_format($fabric_in_prod_floor,2); $dtls_tot_fabric_in_prod_floor +=$fabric_in_prod_floor;  ?></p>
                         	</td>
                            <td width="110" align="right"><p>
                            	 <a  href="##" onclick="open_febric_receive_status_color_wise_popup('<? echo $row[("booking_id")]; ?>','finish_fabric_receive_by_store','<? echo $dts_row[("fabric_color")]; ?>')">
	                            <?
	                             $finish_prod_rece_store=$fin_store_rec_prod[$row[("booking_id")]][$dts_row[("fabric_color")]];
	                             
	                             echo number_format($finish_prod_rece_store,2); $dtls_tot_finish_prod_rece_store +=$finish_prod_rece_store;  ?>
	                             </a></p>
                         	</td>
                            <td width="110" align="right"><p><?  $finish_parchase_rece_store=$fin_store_rec_purchase[$row[("booking_id")]][$dts_row[("fabric_color")]]; echo number_format($finish_parchase_rece_store,2); $dtls_tot_finish_parchase_rece_store +=$finish_parchase_rece_store; ?></p></td>
                            
                            <td width="110" align="right"><p><? $fabric_store_available=$finish_prod_rece_store+$finish_parchase_rece_store; echo number_format($fabric_store_available,2); $dtls_tot_fabric_store_available +=$fabric_store_available;  
                            	$buyer_wise_result[$row["buyer_id"]]["fin_total_available"] +=$fabric_store_available;

                            ?>
                            </p></td>
                           
                            <td width="110" align="right"><p><? 
                            $finish_total_available=$finish_prod_rece_store+$finish_parchase_rece_store;
                            $finish_balance=$dts_row[("finish_fabric_qty")]-$finish_total_available; echo number_format($finish_balance,2); $dtls_tot_fin_balance +=$finish_balance;  ?></p></td>


                            <td width="110" align="right"><p>
	                            <a  href="##" onclick="open_febric_receive_status_color_wise_popup('<? echo $row[("booking_id")]; ?>','issue_to_cut','<? echo $dts_row[("fabric_color")]; ?>')">
	                            <? 
	                            echo number_format($issue_to_cut_arr[$row[("booking_id")]][$dts_row[("fabric_color")]],2); $dtls_tot_cutting_qty +=$issue_to_cut_arr[$row[("booking_id")]][$dts_row[("fabric_color")]]; 
	                            $buyer_wise_result[$row["buyer_id"]]["issue_to_cut"] +=$issue_to_cut_arr[$row[("booking_id")]][$dts_row[("fabric_color")]];
	                            ?>
	                            </a>
	                            </p>
                        	</td>
                            <td width="110" align="right"><p>
	                            <? 
	                            $left_over=$finish_prod_rece_store-$issue_to_cut_arr[$row[("booking_id")]][$dts_row[("fabric_color")]];
	                            echo number_format($left_over,2); $dtls_tot_left_over +=$left_over; 
	                            ?>
	                            </p>
                        	</td>
                            <td width="110" align="right"><p><?
	                            //$yet_to_issue=$row[("grey_fabric_qnty")]-$issue_to_cut_arr[$row[("booking_id")]][$dts_row[("fabric_color")]];
								$yet_to_issue=$fabric_store_available-$issue_to_cut_arr[$row[("booking_id")]][$dts_row[("fabric_color")]];
	                            echo number_format($yet_to_issue,2); $dtls_tot_yet_to_issue +=$yet_to_issue; 
	                            ?>
	                            </p>
                        	</td>
                            <td ><? echo implode(',',$dts_row['fabric_description']); ?></td>
                        </tr>
                        <?
					}
				$m++;
				}
			$i++;
		}
		?>
        </tbody>
    </table>
    </div>
   
    <table class="rpt_table" border="1" rules="all" width="4950" cellpadding="0" cellspacing="0">
		<tfoot>
			<tr>
                <th width="40">&nbsp;</th>
                <th width="65">&nbsp;</th>
                <th width="65">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="90">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="80">Total</th>
               
               	<th width="110"><? echo number_format($dtls_tot_gery_req,2); ?></th>
                <th width="110"><? echo number_format($dtls_tot_yarn_issue,2); ?></th>
                <th width="110"><? echo number_format($dtls_tot_yarn_balance,2); ?></th>
                <th width="110"><? echo number_format($dtls_tot_gery_knit_product,2); ?></th>
				<th width="110"><? echo number_format($dtls_tot_gray_bal,2); ?></th>
                
                <th width="110"><? echo number_format($dtls_tot_gery_delivery,2); ?></th>
                <th width="110"><? echo number_format($dtls_tot_gery_knit_product,2); ?></th>
				<th width="110"><? echo number_format($dtls_tot_grey_knit_receive_prod,2); ?></th>
                
				<th width="110"><? //echo number_format($dtls_tot_gery_available,2); ?></th>
                <th width="110"><? echo number_format($dtls_tot_net_transfer,2); ?></th>
                <th width="110"><? echo number_format($dtls_tot_gray_available_all,2); ?></th>
                
                <th width="110"><? echo number_format($dtls_tot_gray_balance,2); ?></th>
				<th width="110"><? echo number_format($dtls_tot_gray_issue,2); ?></th>
                <th width="110"><? //echo number_format($dtls_tot_cutting_qty,2); ?> </th>
                <th width="110"><? echo number_format($dtls_tot_batch_qty,2); ?></th>
                <th width="110"><? echo number_format($dtls_tot_dying_qty,2); ?></th>
				<th width="110"><? echo number_format($dtls_tot_dying_balance,2); ?></th>
				<th width="110"><? echo number_format($dtls_tot_fin_req_qty,2); ?></th>
				<th width="110"><? echo number_format($dtls_tot_fin_prod_qnty,2); ?></th>




				<th width="110"><? echo number_format($tot_fin_balance,2); ?></th>
				<th width="110"><? echo number_format($dtls_tot_fin_delivery_qty,2); ?></th>
                
				<th width="110"><? echo number_format($dtls_tot_fabric_in_prod_floor,2); ?></th>
                <th width="110"><? echo number_format($dtls_tot_finish_prod_rece_store,2); ?></th>
                <th width="110"><? echo number_format($finish_parchase_rece_store,2); ?></th>
                
				<th width="110"><? echo number_format($dtls_tot_fabric_store_available,2); ?></th>
				<th width="110"><? echo number_format($dtls_tot_fin_balance,2); ?> </th>
                <th width="110"><? echo number_format($dtls_tot_cutting_qty,2); ?> </th>
                <th width="110"><? echo number_format($dtls_tot_left_over,2); ?></th>
				<th width="110"><? echo number_format($dtls_tot_yet_to_issue,2); ?> </th>
				<th>&nbsp;</th>
			</tr>
		</tfoot>
	</table>

   <br />
    
     <?
	foreach (glob("$user_name*.xls") as $filename) 
	{
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data####$filename####$tot_rows";
	exit();
}


if($action=="yarn_issue")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$brand_arr=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name");
	?>
	<script>

		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";
		}	
	</script>	
	<!--	<div style="width:870px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	-->	
	<fieldset style="width:965px; margin-left:3px">
		<div id="report_container">
            <table border="1" class="rpt_table" rules="all" width="960" cellpadding="0" cellspacing="0">
                <?
                $i=1; $total_yarn_issue_qnty=0; $total_yarn_issue_qnty_out=0;

				$sql="SELECT a.booking_id, a.booking_no,a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, sum(d.cons_quantity) as issue_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id 
				from inv_issue_master a, product_details_master c, inv_transaction d 
				where a.id=d.mst_id and d.prod_id=c.id  and d.transaction_type=2 and d.item_category=1 and a.issue_basis=1 and a.issue_purpose=8 and d.transaction_type=2 and a.entry_form=3 and a.booking_id=$boking_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 
				group by a.booking_id, a.booking_no,a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company,c.lot, c.yarn_type, c.id, c.product_name_details,d.brand_id
				union all
				SELECT a.booking_id, a.booking_no,a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, sum(d.cons_quantity) as issue_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id 
				from inv_issue_master a, product_details_master c, inv_transaction d, ppl_yarn_requisition_breakdown e
				where a.id=d.mst_id and d.prod_id=c.id and d.requisition_no=e.requisition_id and e.item_id=d.prod_id and c.id=e.item_id and a.issue_basis=3 and a.issue_purpose=1 and a.item_category=1 and d.transaction_type=2 and a.entry_form=3 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.order_id=$boking_id
				group by a.booking_id, a.booking_no,a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company,c.lot, c.yarn_type, c.id, c.product_name_details,d.brand_id";
				//echo $sql;//die;
                $result=sql_select($sql);
				if(!empty($result))
				{
					?>
	            	<thead>
						<th colspan="10"><b>Yarn Issue</b></th>
					</thead>
					<thead>
	                    <th width="105">Issue Id</th>
	                    <th width="90">Issue To</th>
	                    <th width="105">Booking No</th>
	                    <th width="80">Challan No</th>
	                    <th width="60">Issue Date</th>
	                    <th width="70">Brand</th>
	                    <th width="60">Lot No</th>
	                    <th width="180">Yarn Description</th>
	                    <th width="70">Issue Qnty (In)</th>
	                    <th>Issue Qnty (Out)</th>
					</thead>
	                <?
				}
				foreach($result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				
					if($row[csf('knit_dye_source')]==1) $issue_to=$company_library[$row[csf('knit_dye_company')]]; 
					else if($row[csf('knit_dye_source')]==3) $issue_to=$supplier_details[$row[csf('knit_dye_company')]];
					else $issue_to="&nbsp;";
						
                    $yarn_issued=$row[csf('issue_qnty')];
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="105"><p><? echo $row[csf('issue_number')]; ?></p></td>
                        <td width="90"><p><? echo $issue_to; ?></p></td>
                        <td width="105"><p><? echo $row[csf('booking_no')];?></p></td>
                        <td width="80"><p><? echo $row[csf('challan_no')]; ?></p></td>
                        <td width="60" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                        <td width="70"><p><? echo $brand_arr[$row[csf("brand_id")]]; ?></p></td>
                        <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
                        <td width="180"><p><? echo$row[csf('product_name_details')]; ?></p></td>
                        <td align="right" width="70">
							<? 
								if($row[csf('knit_dye_source')]!=3)
								{
									echo number_format($yarn_issued,2);
									$total_yarn_issue_qnty+=$yarn_issued;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                        <td align="right">
							<? 
								if($row[csf('knit_dye_source')]==3)
								{ 
									echo number_format($yarn_issued,2); 
									$total_yarn_issue_qnty_out+=$yarn_issued;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                    </tr>
                	<?
                	$i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty,2);?></td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty_out,2);?></td>
                </tr>
                <tr style="font-weight:bold">
                    <td align="right" colspan="9">Issue Total</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty+$total_yarn_issue_qnty_out,2);?></td>
                </tr>
                <?
                $total_yarn_return_qnty=0; $total_yarn_return_qnty_out=0;
				$sql_out="SELECT a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, sum(d.cons_quantity) as returned_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id 
				from inv_receive_master a, product_details_master c, inv_transaction d 
				where a.id=d.mst_id  and d.prod_id=c.id  and d.transaction_type=4 and d.item_category=1 and a.entry_form=9 and a.booking_id=$boking_id and a.booking_without_order=1 and a.receive_basis=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0  group by a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no,c.lot, c.yarn_type, c.id, c.product_name_details, d.brand_id 
				union all
				SELECT a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, sum(d.cons_quantity) as returned_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id 
				from inv_receive_master a, product_details_master c, inv_transaction d, ppl_yarn_requisition_breakdown e
				where a.id=d.mst_id  and d.prod_id=c.id and a.booking_id=e.requisition_id and e.item_id=d.prod_id and c.id=e.item_id and d.transaction_type=4 and d.item_category=1 and a.entry_form=9 and a.booking_without_order=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.order_id=$boking_id
				group by a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no,c.lot, c.yarn_type, c.id, c.product_name_details, d.brand_id";
				//echo $sql_out;
				
                $result_out=sql_select($sql_out);
				if(!empty($result_out))
				{
					?>
	                <thead>
	                    <th colspan="10"><b>Yarn Return</b></th>
	                </thead>
	                <thead>
	                	<th width="105">Return Id</th>
	                    <th width="90">Return From</th>
	                    <th width="105">Booking No</th>
	                    <th width="80">Challan No</th>
	                    <th width="60">Return Date</th>
	                    <th width="70">Brand</th>
	                    <th width="60">Lot No</th>
	                    <th width="180">Yarn Description</th>
	                    <th width="70">Return Qnty (In)</th>
	                    <th>Return Qnty (Out)</th>
	               	</thead>
	                <?
				}
				foreach($result_out as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				
					if($row[csf('knitting_source')]==1) $return_from=$company_library[$row[csf('knitting_company')]]; 
					else if($row[csf('knitting_source')]==3) $return_from=$supplier_details[$row[csf('knitting_company')]];
					else $return_from="&nbsp;";
						
                    $yarn_returned=$row[csf('returned_qnty')];
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="105"><p><? echo $row[csf('recv_number')]; ?></p></td>
                        <td width="90"><p><? echo $return_from; ?></p></td>
                        <td width="105"><p><? echo $booking_no_details[$boking_id];//$row[csf('booking_no')];?></p></td>
                        <td width="80"><p><? echo $row[csf('challan_no')]; ?></p></td>
                        <td width="70"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
                        <td width="60" align="center"><? $brand_arr[$row[csf("brand_id")]]; ?></td>
                        <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
                        <td width="180"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right" width="70">
							<? 
								if($row[csf('knitting_source')]!=3)
								{
									echo number_format($yarn_returned,2);
									$total_yarn_return_qnty+=$yarn_returned;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                        <td align="right">
							<? 
								if($row[csf('knitting_source')]==3)
								{ 
									echo number_format($yarn_returned,2); 
									$total_yarn_return_qnty_out+=$yarn_returned;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                    </tr>
                	<?
                	$i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td align="right" colspan="9">Total Issue Rtn</td>
                    <td align="right"><? echo number_format(($total_yarn_return_qnty+$total_yarn_return_qnty_out),2);?></td>
                </tr>
                <tfoot>    
                    <tr>
                        <th align="right" colspan="9">Net Issue</th>
                        <th align="right"><? echo number_format(($total_yarn_issue_qnty+$total_yarn_issue_qnty_out)-($total_yarn_return_qnty+$total_yarn_return_qnty_out),2);?></th>
                    </tr>
                </tfoot>
            </table>	
		</div>
	</fieldset> 
	<?
	exit();
}

if($action=="batch_qty_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name","id","machine_no");
	?>
	<script>

		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";
		}	
		
	</script>	
	<!--	<div style="width:990px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
	<fieldset style="width:990px;">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="760" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<th colspan="9"><b>Batch Info</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="100">Batch ID </th>
                    <th width="120">Batch Name</th>
                    <th width="120">Batch Color</th>
                    <th width="120">Booking No</th>
                    <th width="90">Batch   Date</th>
                    <th width="90">Batch Weight </th>
                   <th width="90">Batch Qnty </th>
				</thead>
             </table>
             <div style="width:782px; max-height:330px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="760" cellpadding="0" cellspacing="0">
                    <?
                    $i=1; 
                    $sql="select a.id,a.batch_no,a.booking_no_id,a.color_id,sum(b.batch_qnty) as batch_qnty,a.batch_date as  batch_date,a.batch_weight from  pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and a.booking_without_order=1  and a.booking_no_id=$boking_id and a.color_id='$color_id' group by a.id,a.batch_no,a.booking_no_id ,a.color_id,a.batch_weight,a.batch_date ";
		//echo $sql_batch_qty;die;
					
					/*$sql_grey_knit_receive=sql_select("select a.booking_id,sum( case when a.receive_basis in(1,2,4,6) then b.cons_quantity else 0 end) as receive_qty from inv_receive_master a, inv_transaction b where a.id=b.mst_id   and a.booking_without_order=1 and a.entry_form=22 and b.transaction_type in(1,4)   and a.booking_id=$boking_id group by a.booking_id order by a.booking_id");*/
					//echo $sql;
					
			
                   $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
                        $total_receive_qnty+=$row[csf('batch_qnty')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="100"><p><? echo $row[csf('id')]; ?></p></td>
                            <td width="120"><p><? echo $row[csf('batch_no')]; ?></p></td>
                            <td width="120"><p><? echo $color_array[$row[csf('color_id')]]; ?></p></td>
                            <td width="120"><p><? echo $booking_no_details[$boking_id];//echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
                            <td width="90" align="center"><? echo change_date_format($row[csf('batch_date')]); ?></td>
                        	<td  width="90" align="center"><? echo $row[csf('batch_weight')]; ?></td>
                            <td  width="90" align="right"><? echo number_format($row[csf('batch_qnty')],2,'.',''); ?></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="7" align="right">Total</th>
                       
                        <th align="right"><? echo number_format($total_receive_qnty,2,'.',''); ?></th>
                        
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>   
	<?
	exit();
}

if($action=="dying_qty_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name","id","machine_no");
	?>
	<script>

		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";
		}	
		
	</script>	
	<!--	<div style="width:990px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
		<fieldset style="width:990px;">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="960" cellpadding="0" cellspacing="0" align="center">
					<thead>
						<th colspan="10"><b>Batch Info</b></th>
					</thead>
					<thead>
	                	<th width="30">SL</th>
	                    <th width="80">Batch ID </th>
	                    <th width="120">Batch Name</th>
	                    <th width="120">Batch Color</th>
	                    <th width="120">Booking No</th>
	                    <th width="90">Batch   Date</th>
	                    <th width="90">Batch Weight </th>
	                    <th width="90">Batch Qnty </th>
	                    <th width="90">Process </th>
	                    <th width="">Productuon Date</th>
					</thead>
	             </table>
	           	  <div style="width:990px; max-height:330px; overflow-y:scroll" id="scroll_body">
	                 <table border="1" class="rpt_table" rules="all" width="960" cellpadding="0" cellspacing="0">
	                    <?
	                    $i=1; 
	                    $sql="select a.id,a.batch_no,a.booking_no_id,a.color_id,sum(b.batch_qnty) as batch_qnty,a.batch_date as  batch_date,a.batch_weight,c.process_id,c.process_end_date from  pro_batch_create_mst a,pro_batch_create_dtls b,pro_fab_subprocess c where c.batch_id=a.id and c.load_unload_id=2 and a.id=b.mst_id and a.booking_without_order=1  and a.booking_no_id=$boking_id group by a.id,a.batch_no,a.booking_no_id ,a.color_id,a.batch_weight,a.batch_date,c.process_id,c.process_end_date ";
		
						//echo $sql;
	                   $result=sql_select($sql);
	        			foreach($result as $row)
	                    {
	                        if ($i%2==0)  
	                            $bgcolor="#E9F3FF";
	                        else
	                            $bgcolor="#FFFFFF";	
	                    
	                        $total_receive_qnty+=$row[csf('batch_qnty')];
	                    ?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                            <td width="30"><? echo $i; ?></td>
	                            <td width="80"><p><? echo $row[csf('id')]; ?></p></td>
	                            <td width="120"><p><? echo $row[csf('batch_no')]; ?></p></td>
	                            <td width="120"><p><? echo $color_array[$row[csf('color_id')]]; ?></p></td>
	                            <td width="120"><p><? echo $booking_no_details[$boking_id];//echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
	                            <td width="90" align="center"><? echo change_date_format($row[csf('batch_date')]); ?></td>
	                        	<td  width="90" align="center"><? echo $row[csf('batch_weight')]; ?></td>
	                            <td  width="90" align="right"><? echo number_format($row[csf('batch_qnty')],2,'.',''); ?></td>
	                            <td width="90"><p><? echo $conversion_cost_head_array[$row[csf('process_id')]];//echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
	                            <td width="" align="center"><? echo change_date_format($row[csf('process_end_date')]); ?></td>
	                        </tr>
	                    <?
	                    $i++;
	                    }
	                    ?>
	                    <tfoot>
	                        <th colspan="7" align="right">Total</th>
	                       
	                        <th align="right"><? echo number_format($total_receive_qnty,2,'.',''); ?></th>
	                        <th align="right"></th>
	                        <th  align="right"></th>
	                    </tfoot>
	                </table>
	            </div>
	        </div>
		</fieldset>   
	<?
	exit();
}

if($action=="grey_fabric_delivery_to_store")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name","id","machine_no");
	?>
	<script>

		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";
		}	
		
	</script>	
	<!--	<div style="width:990px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
	<fieldset style="width:990px;">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="970" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="12"><b>Grey Receive Info</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="110">Delivery Challan</th>
                    <th width="95">Receive Basis</th>
                    <th width="240">Product Details</th>
                    <th width="120">Booking / Program No</th>
                    <th width="70">Delivery  Date</th>
                    <th width="90">Total Prod. Qnty</th>
                    <th>Kniting Com.</th>
				</thead>
            </table>
            <div style="width:990px; max-height:330px; overflow-y:scroll" id="scroll_body">
                <table border="1" class="rpt_table" rules="all" width="970" cellpadding="0" cellspacing="0">
                    <?
					$receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");
                    $i=1; $total_receive_qnty=0;
					$product_arr=return_library_array( "select id,product_name_details from  product_details_master",'id','product_name_details'); 
					$sql="SELECT a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company,
					b.product_id, sum(b.current_delivery) as quantity, c.sys_number, c.delevery_date
					from inv_receive_master a, pro_grey_prod_delivery_dtls b,pro_grey_prod_delivery_mst c
					where c.id=b.mst_id and a.entry_form=2  and c.entry_form in(53,56) and b.grey_sys_id=a.id and a.status_active=1 and a.is_deleted=0 and
					b.status_active=1 and b.is_deleted=0  and a.booking_without_order=1 and a.booking_id=$boking_id
					group by a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.product_id, c.sys_number, c.delevery_date
					union all
					SELECT a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.product_id, sum(b.current_delivery) as quantity, c.sys_number, c.delevery_date 
					from inv_receive_master a, pro_grey_prod_delivery_dtls b,pro_grey_prod_delivery_mst c, ppl_planning_entry_plan_dtls d, wo_non_ord_samp_booking_mst e   
					where c.id=b.mst_id and a.entry_form=2 and c.entry_form in(56) and b.grey_sys_id=a.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
					and a.booking_id=d.dtls_id and d.booking_no=e.booking_no and a.booking_without_order=1 and e.id=$boking_id
					group by a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.product_id, c.sys_number, c.delevery_date";
					// echo $sql;
					/*	"select max(c.delevery_date) as delevery_date , a.booking_id,sum(b.current_delivery) as current_stock from inv_receive_master a,  pro_grey_prod_delivery_dtls b,pro_grey_prod_delivery_mst c where c.id=b.mst_id and a.entry_form=2  and c.entry_form=53 and b.grey_sys_id=a.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.booking_without_order=1 group by a.booking_id "*/
				
					//echo $sql;
                   	$result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
                        $total_receive_qnty+=$row[csf('quantity')];
                    	?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="110"><p><? echo $row[csf('sys_number')]; ?></p></td>
                            <td width="95"><p><? echo $receive_basis[$row[csf('receive_basis')]]; ?></p></td>
                            <td width="240"><p><? echo $product_arr[$row[csf('product_id')]]; ?></p></td>
                            <td width="120"><p><? echo $booking_no_details[$boking_id];//echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
                            <td width="70" align="center"><? echo change_date_format($row[csf('delevery_date')]); ?></td>
                            <td align="right" width="90"><? echo number_format($row[csf('quantity')],2,'.',''); ?></td>
                            <td><p><? if ($row[csf('knitting_source')]==1) echo $company_library[$row[csf('knitting_company')]]; else if ($row[csf('knitting_source')]==3) echo $supplier_details[$row[csf('knitting_company')]]; ?></p></td>
                        </tr>
                    	<?
                    	$i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="6" align="right">Total</th>
                        <th align="right"><? echo number_format($total_receive_qnty,2,'.',''); ?></th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>   
	<?
	exit();
}

if($action=="grey_receive")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name","id","machine_no");
	
	?>
	<script>

		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";
		}	
		
	</script>	
	<!--	<div style="width:990px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
	<fieldset style="width:990px;">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="970" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="12"><b>Grey Receive Info</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="110">Receive Id</th>
                    <th width="95">Prod. Basis</th>
                    <th width="110">Product Details</th>
                    <th width="100">Booking / Program No</th>
                    <th width="60">Machine No</th>
                    <th width="70">Production Date</th>
                    <th width="75">Inhouse Production</th>
                    <th width="75">Outside Production</th>
                    <th width="75">Total Prod. Qnty</th>
                    <th width="70">Challan No</th>
                    <th>Kniting Com.</th>
				</thead>
            </table>
            <div style="width:990px; max-height:330px; overflow-y:scroll" id="scroll_body">
                <table border="1" class="rpt_table" rules="all" width="970" cellpadding="0" cellspacing="0">
                    <?
					$receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");
                    $i=1; $total_receive_qnty=0;
					$product_arr=return_library_array( "select id,product_name_details from  product_details_master",'id','product_name_details'); 
					$sql="SELECT a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company,
					b.machine_no_id, b.prod_id, sum(b.grey_receive_qnty) as quantity 
					from inv_receive_master a, pro_grey_prod_entry_dtls b
					where a.id=b.mst_id and a.receive_basis=1 and a.booking_without_order=1 and a.entry_form=2 and a.booking_id=$boking_id and 
					a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
					group by a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.machine_no_id, b.prod_id
					union all 
					SELECT a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.machine_no_id, b.prod_id, sum(b.grey_receive_qnty) as quantity 
					from inv_receive_master a, pro_grey_prod_entry_dtls b, ppl_planning_entry_plan_dtls c, wo_non_ord_samp_booking_mst e 
					where a.id=b.mst_id and a.booking_id=c.dtls_id and c.booking_no=e.booking_no and a.receive_basis=2 and a.booking_without_order=1 and a.entry_form=2 and e.id=$boking_id
					and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 
					group by a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.machine_no_id, b.prod_id";
					//echo $sql;
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
                        $total_receive_qnty+=$row[csf('quantity')];
                    	?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="95"><p><? echo $receive_basis[$row[csf('receive_basis')]]; ?></p></td>
                            <td width="110"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
                            <td width="100"><p><? echo $booking_no_details[$boking_id];//echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
                            <td width="60"><p>&nbsp;<? echo $machine_arr[$row[csf('machine_no_id')]]; ?></p></td>
                            <td width="70" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td align="right" width="75">
								<? 
                                	if($row[csf('knitting_source')]!=3)
									{
										echo number_format($row[csf('quantity')],2,'.','');
										$total_receive_qnty_in+=$row[csf('quantity')];
									}
									else echo "&nbsp;";
                                ?>
                            </td>
                            <td align="right" width="75">
								<? 
                                	if($row[csf('knitting_source')]==3)
									{
										echo number_format($row[csf('quantity')],2,'.','');
										$total_receive_qnty_out+=$row[csf('quantity')];
									}
									else echo "&nbsp;";
                                ?>
                            </td>
                            <td align="right" width="75"><? echo number_format($row[csf('quantity')],2,'.',''); ?></td>
                            <td width="70"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                            <td><p><? if ($row[csf('knitting_source')]==1) echo $company_library[$row[csf('knitting_company')]]; else if ($row[csf('knitting_source')]==3) echo $supplier_details[$row[csf('knitting_company')]]; ?></p></td>
                        </tr>
                    	<?
                    	$i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="7" align="right">Total</th>
                        <th align="right"><? echo number_format($total_receive_qnty_in,2,'.',''); ?></th>
                        <th align="right"><? echo number_format($total_receive_qnty_out,2,'.',''); ?></th>
                        <th align="right"><? echo number_format($total_receive_qnty,2,'.',''); ?></th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>   
	<?
	exit();
}

if($action=="grey_receive_prod")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	$mydata=explode("_", $boking_id);
	$booking_type=$mydata[1];
	if($booking_type=="withoutRoll")
	{
		$boking_id=$mydata[0];
	}
	else
	{
		$barcode_ids=$mydata[0];
	}

	$machine_arr = return_library_array("select id, machine_no from lib_machine_name","id","machine_no");	
	?>
	<script>

		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";
		}	
		
	</script>	
	<!--	<div style="width:990px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
		<fieldset style="width:990px;">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="970" cellpadding="0" cellspacing="0">
					<thead>
						<th colspan="12"><b>Grey Receive Info</b></th>
					</thead>
					<thead>
	                	<th width="30">SL</th>
	                    <th width="110">Receive Id</th>
	                    <th width="95">Prod. Basis</th>
	                    <th width="110">Product Details</th>
	                    <th width="100">Booking / Program No</th>
	                    <th width="60">Machine No</th>
	                    <th width="70">Production Date</th>
	                    <th width="75">Inhouse Production</th>
	                    <th width="75">Outside Production</th>
	                    <th width="75">Total Prod. Qnty</th>
	                    <th width="70">Challan No</th>
	                    <th>Kniting Com.</th>
					</thead>
	             </table>
	             <div style="width:990px; max-height:330px; overflow-y:scroll" id="scroll_body">
	                 <table border="1" class="rpt_table" rules="all" width="970" cellpadding="0" cellspacing="0">
	                    <?
						
					if($booking_type=="withoutRoll")
					{	
						$sql_grey_booking=sql_select("select a.id,a.booking_id from inv_receive_master a where a.receive_basis=1 and a.booking_without_order=1 and a.entry_form=2 and a.booking_id=$boking_id");
						//echo "select a.id,a.booking_id from inv_receive_master a where a.receive_basis=1 and a.booking_without_order=1 and a.entry_form=2 and a.booking_id=$boking_id";
						
						foreach($sql_grey_booking as $val)
						{
							
							$receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan",9=>"Production");
							$i=1; $total_receive_qnty=0;
							$product_arr=return_library_array( "select id,product_name_details from  product_details_master",'id','product_name_details'); 
							
							/*$sql="select a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company,
							b.machine_no_id, b.prod_id, sum(b.grey_receive_qnty) as quantity 
							from inv_receive_master a, pro_grey_prod_entry_dtls b
							where a.id=b.mst_id and a.receive_basis in(9) and a.booking_without_order=1 and a.entry_form=22 and a.booking_id=".$val[csf('id')]." and 
							a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
							group by a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.machine_no_id, b.prod_id";*/

							$sql="select a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company,
							b.machine_no_id, b.prod_id, sum(b.grey_receive_qnty) as quantity 
							from inv_receive_master a, pro_grey_prod_entry_dtls b
							where a.id=b.mst_id and a.booking_without_order=1 and a.booking_id in (".$val[csf('id')].") and 
							a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in(22)
							group by a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.machine_no_id, b.prod_id";

							//echo $sql;
							$result=sql_select($sql);
							foreach($result as $row)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
							
								$total_receive_qnty+=$row[csf('quantity')];
							?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
									<td width="30"><? echo $i; ?></td>
									<td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
									<td width="95"><p><? echo $receive_basis[$row[csf('receive_basis')]]; ?></p></td>
									<td width="110"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
									<td width="100"><p><? echo $booking_no_details[$boking_id];//echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
									<td width="60"><p>&nbsp;<? echo $machine_arr[$row[csf('machine_no_id')]]; ?></p></td>
									<td width="70" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
									<td align="right" width="75">
										<? 
											if($row[csf('knitting_source')]!=3)
											{
												echo number_format($row[csf('quantity')],2,'.','');
												$total_receive_qnty_in+=$row[csf('quantity')];
											}
											else echo "&nbsp;";
										?>
									</td>
									<td align="right" width="75">
										<? 
											if($row[csf('knitting_source')]==3)
											{
												echo number_format($row[csf('quantity')],2,'.','');
												$total_receive_qnty_out+=$row[csf('quantity')];
											}
											else echo "&nbsp;";
										?>
									</td>
									<td align="right" width="75"><? echo number_format($row[csf('quantity')],2,'.',''); ?></td>
									<td width="70"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
									<td><p><? if ($row[csf('knitting_source')]==1) echo $company_library[$row[csf('knitting_company')]]; else if ($row[csf('knitting_source')]==3) echo $supplier_details[$row[csf('knitting_company')]]; ?></p></td>
								</tr>
							<?
							$i++;
							}
						}
					}
					else
					{
						$receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan",9=>"Production");
							$i=1; $total_receive_qnty=0;
							$product_arr=return_library_array( "select id,product_name_details from  product_details_master",'id','product_name_details'); 

							$sql="select a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.challan_no, a.knitting_company, b.machine_no_id, b.prod_id, c.booking_no, sum(c.qnty) as quantity from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where c.barcode_no in ($barcode_ids) and c.entry_form=58 and a.id=b.mst_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=c.mst_id and b.id=c.dtls_id and c.status_active=1 and c.is_deleted=0 group by c.booking_no, a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.challan_no, a.knitting_company, b.machine_no_id, b.prod_id";
							
							//echo $sql;
							$result=sql_select($sql);
							foreach($result as $row)
							{
								if ($i%2==0)  
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";	
							
								$total_receive_qnty+=$row[csf('quantity')];
							?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
									<td width="30"><? echo $i; ?></td>
									<td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
									<td width="95"><p><? echo $receive_basis[$row[csf('receive_basis')]]; ?></p></td>
									<td width="110"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
									<td width="100"><p><? echo $booking_no_details[$boking_id];//echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
									<td width="60"><p>&nbsp;<? echo $machine_arr[$row[csf('machine_no_id')]]; ?></p></td>
									<td width="70" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
									<td align="right" width="75">
										<? 
											if($row[csf('knitting_source')]!=3)
											{
												echo number_format($row[csf('quantity')],2,'.','');
												$total_receive_qnty_in+=$row[csf('quantity')];
											}
											else echo "&nbsp;";
										?>
									</td>
									<td align="right" width="75">
										<? 
											if($row[csf('knitting_source')]==3)
											{
												echo number_format($row[csf('quantity')],2,'.','');
												$total_receive_qnty_out+=$row[csf('quantity')];
											}
											else echo "&nbsp;";
										?>
									</td>
									<td align="right" width="75"><? echo number_format($row[csf('quantity')],2,'.',''); ?></td>
									<td width="70"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
									<td><p><? if ($row[csf('knitting_source')]==1) echo $company_library[$row[csf('knitting_company')]]; else if ($row[csf('knitting_source')]==3) echo $supplier_details[$row[csf('knitting_company')]]; ?></p></td>
								</tr>
							<?
							$i++;
							}
					}
	                    ?>
	                    <tfoot>
	                        <th colspan="7" align="right">Total</th>
	                        <th align="right"><? echo number_format($total_receive_qnty_in,2,'.',''); ?></th>
	                        <th align="right"><? echo number_format($total_receive_qnty_out,2,'.',''); ?></th>
	                        <th align="right"><? echo number_format($total_receive_qnty,2,'.',''); ?></th>
	                        <th>&nbsp;</th>
	                        <th>&nbsp;</th>
	                    </tfoot>
	                </table>
	            </div>	
	        </div>
		</fieldset>   
	<?
	exit();
}

//finish_fabric_receive_by_store
if($action=="finish_fabric_receive_by_store")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	$mydata=explode("_", $boking_id);
	$booking_type=$mydata[1];
	if($booking_type=="withoutRoll")
	{
		$boking_id=$mydata[0];
	}
	else
	{
		$barcode_ids=$mydata[0];
	}
	?>
	<script>

		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";
		}	
		
	</script>	
		<!--<div style="width:885px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
		<fieldset style="width:880px; margin-left:3px">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
					<thead>
						<th colspan="9"><b>Fabric Receive Info</b></th>
					</thead>
					<thead>
	                	<th width="30">SL</th>
	                    <th width="120">System Id</th>
	                    <th width="75">Rec. Date</th>
	                    <th width="80">Rec. Basis</th>
	                    <th width="90">Batch No</th>
	                    <th width="90">Dyeing Source</th>
	                    <th width="100">Dyeing Company</th>
	                    <th width="90">Receive Qnty</th>
	                    <th>Fabric Description</th>
					</thead>
	             </table>
	             <div style="width:877px; max-height:320px; overflow-y:scroll" id="scroll_body">
	                 <table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
	                    <?
						$receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");
	                    $i=1;
	                    $total_fabric_recv_qnty=0; $dye_company='';

	                if($booking_type=="withoutRoll")
	                {    
	                    $sql="select c.booking_no_id,b.color_id,sum(case when a.receive_basis=9 then b.receive_qnty else 0 end) as production_qty,a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, b.prod_id
						from inv_receive_master a,  pro_finish_fabric_rcv_dtls b,pro_batch_create_mst c 
						where a.id=b.mst_id and a.entry_form=37 and a.receive_basis !=11 and b.batch_id=c.id  and c.batch_against=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and  c.booking_no_id=$boking_id and b.color_id='$color_id'  group by c.booking_no_id,b.color_id,a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, b.prod_id
						union all
						select a.booking_id,b.color_id,sum(case when a.receive_basis=9 then b.receive_qnty else 0 end) as production_qty,a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, b.prod_id
						from inv_receive_master a,  pro_finish_fabric_rcv_dtls b 
						where a.id=b.mst_id and a.entry_form=37 and a.receive_basis !=11 and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and  a.booking_id=$boking_id and b.color_id='$color_id'  group by a.booking_id,b.color_id,a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, b.prod_id 
						union all
						select  f.id as booking_id, b.color_id, sum(b.receive_qnty) as production_qty, a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, b.prod_id from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst d, wo_non_ord_knitdye_booking_mst e, wo_non_ord_samp_booking_mst f where a.id=b.mst_id and b.batch_id=d.id and d.booking_without_order=1  and a.entry_form in (37) and d.booking_no=e.booking_no and a.receive_basis=11 and e.fab_booking_id =$boking_id and e.fab_booking_id=f.id and b.color_id='$color_id' group by f.id, b.color_id, a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, b.prod_id ";
					}
					else
					{
						$sql="select a.booking_id,b.color_id,sum(c.qnty) as production_qty,a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, b.prod_id from inv_receive_master a,  pro_finish_fabric_rcv_dtls b , pro_roll_details c where a.id=b.mst_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=c.mst_id and b.id=c.dtls_id and c.barcode_no in ($barcode_ids) and c.entry_form=68 and c.status_active=1 and c.is_deleted=0 group by a.booking_id,b.color_id,a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, b.prod_id ";
					}
					//echo $sql;
	                    $result=sql_select($sql);
	        			foreach($result as $row)
	                    {
	                        if ($i%2==0)  
	                            $bgcolor="#E9F3FF";
	                        else
	                            $bgcolor="#FFFFFF";	
	                    
	                        if($row[csf('knitting_source')]==1) 
	                        {
	                            $dye_company=$company_library[$row[csf('knitting_company')]]; 
	                        }
	                        else if($row['knitting_source']==3) 
	                        {
	                            $dye_company=$supplier_details[$row[csf('knitting_company')]];
	                        }
	                        else
	                            $dye_company="&nbsp;";
	                    
	                        $total_fabric_recv_qnty+=$row[csf('production_qty')];
	                    ?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                            <td width="30"><? echo $i; ?></td>
	                            <td width="120"><p><? echo $row[csf('recv_number')]; ?></p></td>
	                            <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
	                            <td width="80"><? echo $receive_basis_arr[$row[csf('receive_basis')]]; ?></td>
	                            <td width="90"><p><? echo $batch_details[$row[csf('batch_id')]]; ?></p></td>
	                            <td width="90"><? echo $knitting_source[$row[csf('knitting_source')]]; ?></td>
	                            <td width="100"><p><? echo $dye_company; ?></p></td>
	                            <td width="90" align="right"><? echo number_format($row[csf('production_qty')],2); ?></td>
	                            <td><p><? echo $product_details[$row[csf('prod_id')]]; ?></p></td>
	                        </tr>
	                    <?
	                    $i++;
	                    }
	                    ?>
	                    <tfoot>
	                        <th colspan="7" align="right">Total</th>
	                        <th align="right"><? echo number_format($total_fabric_recv_qnty,2); ?></th>
	                        <th>&nbsp;</th>
	                    </tfoot>
	                </table>
	            </div>	
	        </div>
		</fieldset>   
	<?
	exit();
}

if($action=="grey_issue")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$mydata=explode("_", $boking_id);
	$id=$mydata[0];
	$booking_type=$mydata[1];
	?>
	<script>

		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";
		}	
		
	</script>	
		<!--<div style="width:885px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
		<fieldset style="width:880px; margin-left:3px">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
	                <thead>
	                	<tr>
	                        <th colspan="9"><b>Grey Issue Info</b></th>
	                    </tr>
	                    <tr>
	                        <th width="40">SL</th>
	                        <th width="120">Issue Id</th>
	                        <th width="100">Issue Purpose</th>
	                        <th width="100">Issue To</th>
	                        <th width="115">Booking No</th>
	                        <th width="90">Batch No</th>
	                        <th width="80">Issue Date</th>
	                        <th width="100">Issue Qnty (In)</th>
	                        <th>Issue Qnty (Out)</th>
	                    </tr>
					</thead>
	             </table>
	             <div style="width:877px; max-height:320px; overflow-y:scroll" id="scroll_body">
	                 <table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
	                    <?
	                    $i=1; $issue_to='';

	                    if($booking_type=="withoutRoll")
	                    {
		                    $sql="select a.issue_number, a.issue_date, a.issue_purpose, a.knit_dye_source, a.knit_dye_company, a.booking_no, a.batch_no, sum(b.issue_qnty) as quantity 
							from inv_issue_master a, inv_grey_fabric_issue_dtls b
							where a.id=b.mst_id and a.entry_form=16 and a.booking_id=$id and a.issue_basis=1 and a.issue_purpose=8 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
							group by a.issue_number, a.issue_date, a.issue_purpose, a.knit_dye_source, a.knit_dye_company, a.booking_no, a.batch_no";
						}
						else
						{
							$sql="select a.issue_number, a.issue_date, a.issue_purpose, a.knit_dye_source, a.knit_dye_company, a.batch_no, c.booking_no, sum(c.qnty) as quantity from inv_issue_master a, inv_grey_fabric_issue_dtls b, pro_roll_details c where c.barcode_no in ($id) and c.entry_form=61 and a.id=b.mst_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=c.mst_id and b.id=c.dtls_id and c.status_active=1 and c.is_deleted=0 group by c.booking_no, a.issue_number, a.issue_date, a.issue_purpose, a.knit_dye_source, a.knit_dye_company, a.batch_no";
						}
						// echo $sql;
	                    $result=sql_select($sql);
	        			foreach($result as $row)
	                    {
	                        if ($i%2==0)  
	                            $bgcolor="#E9F3FF";
	                        else
	                            $bgcolor="#FFFFFF";	
	                    
	                        if($row[csf('knit_dye_source')]==1) 
	                        {
	                            $issue_to=$company_library[$row[csf('knit_dye_company')]]; 
	                        }
	                        else if($row['knit_dye_source']==3) 
	                        {
	                            $issue_to=$supplier_details[$row[csf('knit_dye_company')]];
	                        }
	                        else
	                            $issue_to="&nbsp;";
	                    
	                    ?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                            <td width="40"><? echo $i; ?></td>
	                            <td width="120"><p><? echo $row[csf('issue_number')]; ?></p></td>
	                            <td width="100"><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></td>
	                            <td width="100"><p><? echo $issue_to; ?></p></td>
	                            <td width="115"><? echo $row[csf('booking_no')]; ?>&nbsp;</td>
	                            <td width="90"><p><? echo $batch_details[$row[csf('batch_no')]]; ?>&nbsp;</p></td>
	                            <td width="80" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
	                            <td width="100" align="right">
									<?
	                                    if($row[csf('knit_dye_source')]!=3)
	                                    {
	                                        echo number_format($row[csf('quantity')],2);
	                                        $total_issue_qnty+=$row[csf('quantity')];
	                                    }
	                                    else echo "&nbsp;";
	                                ?>
	                            </td>
	                            <td align="right">
	                                <?
	                                    if($row[csf('knit_dye_source')]==3)
	                                    {
	                                        echo number_format($row[csf('quantity')],2);
	                                        $total_issue_qnty_out+=$row[csf('quantity')];
	                                    }
	                                    else echo "&nbsp;";
	                                ?>
	                            </td>
	                        </tr>
	                    <?
	                    $i++;
	                    }
	                    ?>
	                    <tfoot>
	                    	<tr>
	                            <th colspan="7" align="right">Total</th>
	                            <th align="right"><? echo number_format($total_issue_qnty,2); ?></th>
	                            <th align="right"><? echo number_format($total_issue_qnty_out,2); ?></th>
	                        </tr>
	                        <tr>
	                            <th colspan="7" align="right">Grand Total</th>
	                            <th align="right" colspan="2"><? echo number_format($total_issue_qnty+$total_issue_qnty_out,2); ?></th>
	                        </tr>
	                    </tfoot>
	                </table>
	            </div>	
	        </div>
		</fieldset>   
	<?
	exit();
}

if($action=="finish_feb_pord")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	$mydata=explode("_", $boking_id);
	if ($mydata[1]=="withoutRoll")
	{
		$boking_id=$mydata[0];
	}
	else
	{
		$barcode_ids=$mydata[0];
	}
	?>
	<script>

		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";
		}	
		
	</script>	
		<!--<div style="width:885px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
		<fieldset style="width:880px; margin-left:3px">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
					<thead>
						<th colspan="9"><b>Fabric Receive Info</b></th>
					</thead>
					<thead>
	                	<th width="30">SL</th>
	                    <th width="120">System Id</th>
	                    <th width="75">Rec. Date</th>
	                    <th width="80">Rec. Basis</th>
	                    <th width="90">Batch No</th>
	                    <th width="90">Dyeing Source</th>
	                    <th width="100">Dyeing Company</th>
	                    <th width="90">Receive Qnty</th>
	                    <th>Fabric Description</th>
					</thead>
	             </table>
	             <div style="width:877px; max-height:320px; overflow-y:scroll" id="scroll_body">
	                 <table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
	                    <?
	                    $i=1;
	                    $total_fabric_recv_qnty=0; $dye_company='';
	                    if ($mydata[1]=="withoutRoll")
	                    {
		                    $sql="select a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, b.prod_id, sum(b.receive_qnty) as quantity from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c where a.id=b.mst_id and b.batch_id=c.id  and a.receive_basis=5 and a.entry_form=7 and c.booking_without_order=1 and c.booking_no_id=$boking_id and b.color_id='$color_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, b.prod_id";
						}
						else
						{
							$sql="select a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, b.prod_id, sum(d.qnty) as quantity from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c, pro_roll_details d where a.id=b.mst_id and b.batch_id=c.id and a.id=d.mst_id and b.id=d.dtls_id and d.barcode_no in ($barcode_ids) and d.entry_form=66 and d.status_active=1 and d.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, b.prod_id";
						}
						//echo $sql;
	                    $result=sql_select($sql);
	        			foreach($result as $row)
	                    {
	                        if ($i%2==0)  
	                            $bgcolor="#E9F3FF";
	                        else
	                            $bgcolor="#FFFFFF";	
	                    
	                        if($row[csf('knitting_source')]==1) 
	                        {
	                            $dye_company=$company_library[$row[csf('knitting_company')]]; 
	                        }
	                        else if($row['knitting_source']==3) 
	                        {
	                            $dye_company=$supplier_details[$row[csf('knitting_company')]];
	                        }
	                        else
	                            $dye_company="&nbsp;";
	                    
	                        $total_fabric_recv_qnty+=$row[csf('quantity')];
	                    ?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                            <td width="30"><? echo $i; ?></td>
	                            <td width="120"><p><? echo $row[csf('recv_number')]; ?></p></td>
	                            <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
	                            <td width="80"><? echo $receive_basis_arr[$row[csf('receive_basis')]]; ?></td>
	                            <td width="90"><p><? echo $batch_details[$row[csf('batch_id')]]; ?></p></td>
	                            <td width="90"><? echo $knitting_source[$row[csf('knitting_source')]]; ?></td>
	                            <td width="100"><p><? echo $dye_company; ?></p></td>
	                            <td width="90" align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
	                            <td><p><? echo $product_details[$row[csf('prod_id')]]; ?></p></td>
	                        </tr>
	                    <?
	                    $i++;
	                    }
	                    ?>
	                    <tfoot>
	                        <th colspan="7" align="right">Total</th>
	                        <th align="right"><? echo number_format($total_fabric_recv_qnty,2); ?></th>
	                        <th>&nbsp;</th>
	                    </tfoot>
	                </table>
	            </div>	
	        </div>
		</fieldset>   
	<?
	exit();
}

if($action=="finish_fabric_delivery_to_store")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	// echo "<pre>"; print_r($boking_id);echo "<pre>";die;
	$data=explode("__", $boking_id);
	// echo "<pre>"; print_r($data);echo "<pre>";die;
	$mydata=explode("_", $data[0]);
	if($mydata[1]=="withoutRoll")
	{
		$boking_id=$mydata[0];
	}
	else
	{
		$barcode_ids=$mydata[0];
		$color_id=$data[1];
	}
	?>
	<script>

		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";
		}	
		
	</script>	
		<!--<div style="width:885px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
		<fieldset style="width:980px; margin-left:3px">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="950" cellpadding="0" cellspacing="0">
					<thead>
						<th colspan="9"><b>Fabric Delivery Info</b></th>
					</thead>
					<thead>
	                	<th width="30">SL</th>
	                    <th width="120">System Id</th>
	                    <th width="75">Prd. date</th>
	                    <th width="120">Booking No</th>
	                    <th width="120">Knitting Source </th>
	                    <th width="120">Knitting Company</th>
	                    <th width="90">Color</th>
	                    <th width="100">Batch No</th>
	                    
	                    <th width="">Delivery Qty</th>
					</thead>
	             </table>
	             <div style="width:977px; max-height:320px; overflow-y:scroll" id="scroll_body">
	                 <table border="1" class="rpt_table" rules="all" width="950" cellpadding="0" cellspacing="0">
	                    <?
	                    $i=1;
	                    $total_fabric_recv_qnty=0; $dye_company='';

	                    if($mydata[1]=="withoutRoll")
	                    {	
	                    	//$sql="select b.batch_id,b.color_id,e.grey_sys_number,e.determination_id,sum(e.current_delivery) as delivery_qty,c.booking_no_id,a.receive_date,a.knitting_source,a.knitting_company from inv_receive_master a,  pro_finish_fabric_rcv_dtls b,pro_batch_create_mst c,pro_grey_prod_delivery_mst d,pro_grey_prod_delivery_dtls e where c.booking_no_id=$boking_id and d.id=e.mst_id and e.grey_sys_id=a.id and d.entry_form=54  and e.entry_form=54 and a.id=b.mst_id and a.entry_form=7  and b.batch_id=c.id  and c.batch_against=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   group by b.batch_id,b.color_id,e.grey_sys_number,e.determination_id,c.booking_no_id,a.receive_date,a.knitting_source,a.knitting_company";
							$sql="SELECT c.id as batch_id,e.color_id,d.sys_number as grey_sys_number,e.determination_id as determination_id,sum(e.current_delivery) as delivery_qty,c.booking_no_id,d.delevery_date as receive_date,d.knitting_source,d.knitting_company from pro_batch_create_mst c,pro_grey_prod_delivery_mst d,pro_grey_prod_delivery_dtls e where c.booking_no_id=$boking_id and e.color_id=$color_id and d.id=e.mst_id and e.mst_id=d.id and d.entry_form=54  and e.entry_form=54   and e.batch_id=c.id  and c.batch_against=3 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0   group by c.id,e.color_id,d.sys_number,e.determination_id,c.booking_no_id,d.delevery_date,d.knitting_source,d.knitting_company";
	                    }
	                    else
	                    {
	                    	$sql="SELECT e.grey_sys_number, e.determination_id, sum(f.qnty) as delivery_qty, a.receive_date,a.knitting_source,a.knitting_company from inv_receive_master a, pro_grey_prod_delivery_mst d, pro_grey_prod_delivery_dtls e, pro_roll_details f where d.id=e.mst_id and e.grey_sys_id=a.id and d.id=f.mst_id and e.id=f.dtls_id and f.barcode_no in ($barcode_ids) and e.color_id=$color_id and f.entry_form=67 and f.status_active=1 and f.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 group by  e.grey_sys_number, e.determination_id, a.receive_date, a.knitting_source, a.knitting_company";
	                    }

						//echo $sql;	
			/*	$sql_finish_prodction_delivery=sql_select("select max(d.delevery_date) as receive_date ,sum(e.current_delivery) as delivery_qty,c.booking_no_id from inv_receive_master a,  pro_finish_fabric_rcv_dtls b,pro_batch_create_mst c,pro_grey_prod_delivery_mst d,pro_grey_prod_delivery_dtls e where d.id=e.mst_id and e.grey_sys_id=a.id and d.entry_form=54  and e.entry_form=54 and a.id=b.mst_id and a.entry_form=7  and b.batch_id=c.id  and c.batch_against=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   group by c.booking_no_id ");*/
						//echo $sql;
	                    $result=sql_select($sql);
	        			foreach($result as $row)
	                    {
	                        if ($i%2==0)  
	                            $bgcolor="#E9F3FF";
	                        else
	                            $bgcolor="#FFFFFF";	
	                    
	                        if($row[csf('knitting_source')]==1) 
	                        {
	                            $dye_company=$company_library[$row[csf('knitting_company')]]; 
	                        }
	                        else if($row['knitting_source']==3) 
	                        {
	                            $dye_company=$supplier_details[$row[csf('knitting_company')]];
	                        }
	                        else
	                            $dye_company="&nbsp;";
	                    
	                        $total_fabric_recv_qnty+=$row[csf('delivery_qty')];
	                    ?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                            <td width="30"><? echo $i; ?></td>
	                            <td width="120"><p><? echo $row[csf('grey_sys_number')]; ?></p></td>
	                            <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
	                            <td width="120"><? echo $booking_no_details[$row[csf('booking_no_id')]]; ?></td>
	                            <td width="120" align="left"><? echo $knitting_source[$row[csf('knitting_source')]]; ?></td>
	                            <td width="120"><p><? echo $dye_company; ?></p></td>
	                            <td width="90"><? echo $color_array[$row[csf('color_id')]]; ?></td>
	                            <td width="100"><p><? echo $batch_details[$row[csf('batch_id')]]; ?></p></td>
	                            
	                            <td  align="right"><? echo number_format($row[csf('delivery_qty')],2); ?></td>
	                        </tr>
	                    <?
	                    $i++;
	                    }
	                    ?>
	                    <tfoot>
	                        <th colspan="8" align="right">Total</th>
	                        <th align="right"><? echo number_format($total_fabric_recv_qnty,2); ?></th>
	                        
	                    </tfoot>
	                </table>
	            </div>	
	        </div>
		</fieldset>   
	<?
	exit();
}

if($action=="issue_to_cut")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	$mydata=explode("_", $boking_id);
	if($mydata[1]=="withoutRoll")
	{
		$boking_id=$mydata[0];
	}
	else
	{
		$barcode_ids=$mydata[0];
	}
	?>
	<script>

		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";
		}	
		
	</script>	
		<!--<div style="width:775px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
		<fieldset style="width:770px; margin-left:7px">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="750" cellpadding="0" cellspacing="0">
					<thead>
						<th colspan="6"><b>Issue To Cutting Info</b></th>
					</thead>
					<thead>
	                	<th width="50">SL</th>
	                    <th width="120">System Id</th>
	                    <th width="80">Issue Date</th>
	                    <th width="120">Batch No</th>
	                    <th width="110">Issue Qnty</th>
	                    <th>Fabric Description</th>
					</thead>
	             </table>
	             <div style="width:767px; max-height:320px; overflow-y:scroll" id="scroll_body">
	                 <table border="1" class="rpt_table" rules="all" width="750" cellpadding="0" cellspacing="0">
	                    <?
	                    $i=1; $total_issue_to_cut_qnty=0;

	                    if($mydata[1]=="withoutRoll")
	                    {
	                    	$sql="select a.issue_number, a.issue_date, b.batch_id, b.prod_id, sum(b.issue_qnty) as quantity from inv_issue_master a, inv_finish_fabric_issue_dtls b, pro_batch_create_mst c, wo_non_ord_samp_booking_mst d where a.id=b.mst_id and b.batch_id=c.id and a.entry_form=18 and c.booking_no = d.booking_no and c.booking_no_id>0 and c.booking_without_order=1 and c.booking_no_id=$boking_id and c.color_id='$color_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.issue_number, a.issue_date, b.batch_id, b.prod_id 
	                    	union all select  a.issue_number, a.issue_date, b.batch_id, b.prod_id,  sum(b.issue_qnty) as issue_qty from inv_issue_master a, inv_finish_fabric_issue_dtls b, pro_batch_create_mst d, wo_non_ord_knitdye_booking_mst e, wo_non_ord_samp_booking_mst f where a.id=b.mst_id and b.batch_id=d.id and d.booking_without_order=1  and a.entry_form in (18) and d.booking_no=e.booking_no and d.booking_no_id>0  and e.fab_booking_id =$boking_id and d.color_id='$color_id' and e.fab_booking_id=f.id group by a.issue_number, a.issue_date, b.batch_id, b.prod_id
	                    	";
	                    }
	                    else
	                    {
	                    	$sql="select a.issue_number, a.issue_date, b.batch_id, b.prod_id, sum(c.qnty) as quantity from inv_issue_master a, inv_finish_fabric_issue_dtls b, pro_roll_details c where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id and c.barcode_no in ($barcode_ids) and c.entry_form=71 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.issue_number, a.issue_date, b.batch_id, b.prod_id";
	                    }
						//echo $sql;
	                    $result=sql_select($sql);
	        			foreach($result as $row)
	                    {
	                        if ($i%2==0)  
	                            $bgcolor="#E9F3FF";
	                        else
	                            $bgcolor="#FFFFFF";	
	                    
	                        $total_issue_to_cut_qnty+=$row[csf('quantity')];
	                    ?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                            <td width="50"><? echo $i; ?></td>
	                            <td width="120"><p><? echo $row[csf('issue_number')]; ?></p></td>
	                            <td width="80" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
	                            <td width="120"><p><? echo $batch_details[$row[csf('batch_id')]]; ?></p></td>
	                            <td width="110" align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
	                            <td><p><? echo $product_details[$row[csf('prod_id')]]; ?></p></td>
	                        </tr>
	                    <?
	                    $i++;
	                    }
	                    ?>
	                    <tfoot>
	                        <th colspan="4" align="right">Total</th>
	                        <th align="right"><? echo number_format($total_issue_to_cut_qnty,2); ?></th>
	                        <th>&nbsp;</th>
	                    </tfoot>
	                </table>
	            </div>	
	        </div>
		</fieldset>   
	<?
	exit();
}


if($action=="grey_fabric_transfer_transection")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	$mydata = explode("_", $order_id);
	
	$result_transfer_mst = sql_select("select a.id, a.transfer_system_id, a.transfer_date, a.challan_no, a.transfer_criteria,b.order_id, 
	sum( case when a.transfer_criteria in (7,8) and b.transaction_type=6 then b.cons_quantity else 0 end) as tranfer_out, 
	sum( case when a.transfer_criteria in(6,8) and b.transaction_type=5 then b.cons_quantity else 0 end) as tranfer_in 
	from inv_item_transfer_mst a, inv_transaction b where a.id=b.mst_id   and a.item_category=13 and b.item_category=13 and b.transaction_type in(5,6) 
	and b.order_id='$mydata[0]' group by a.id, a.transfer_system_id, a.transfer_date, a.challan_no, a.transfer_criteria,b.order_id order by b.order_id");

	?>
	<fieldset style="width:770px; margin-left:7px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="750" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="8"><b>Grey Fabric Transfer Information</b></th>
				</thead>
				<thead>
                	<th width="50">SL</th>
                    <th width="120">System Id</th>
                    <th width="80">Transfered Date</th>
                    <th width="80">Challan No</th>
                    <th width="80">Transfered Type</th>
                    <th width="80">Transfered In Qnty</th>
                    <th width="80">Transfered Out Qnty</th>
                    <th width="80">Net Transfered Qnty</th>
				</thead>
             </table>
             <div style="width:767px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="750" cellpadding="0" cellspacing="0">
				<?
				$i=1;
				if(empty($result_transfer_mst)==false)
				{
					foreach($result_transfer_mst as $row)
					{
						 if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";	
						?>    
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td width="50" align="center"><?php echo $i; ?></td>
							<td width="120"><p><?php echo $row[csf('transfer_system_id')];?></p></td>
							<td width="80" align="center"><?php echo $row[csf('transfer_date')];?></td>
							<td width="80" align="center"><?php echo $row[csf('challan_no')];?></td>
							<td width="80" align="center"><?php echo $item_transfer_criteria[$row[csf('transfer_criteria')]];?></td>
                            <td width="80" align="center">
                            	<?php 
								echo $transferIn = $row[csf('tranfer_in')];
								$totalTransferIn +=$transferIn;
								?>
                            </td>
                            <td width="80" align="center">
                            	<?php 
								echo $transferOut = $row[csf('tranfer_out')];
								$totalTransferOut +=$transferOut; 
								?>
                            </td>
                            <td width="80" align="center">
                            <?php 
								echo $netTranferQty = ($row[csf('tranfer_in')]-$row[csf('tranfer_out')]);
								$totalNettranferQty += $netTranferQty;
							?>
							</td>
						</tr>
				   <? 
						$i++;
				   } 
				} else{
					echo "<h3 style='color:red;'> Transfer data not found!!</h3>";	
				}
			   ?>
                       
                    <tfoot>
                        <th colspan="5">Total</th>
                        <th style="text-align:center;"><?php echo $totalTransferIn; ?></th>
                        <th style="text-align:center;"><?php echo $totalTransferOut; ?></th>
                        <th style="text-align:center;"><?php echo $totalNettranferQty; ?></th>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>   
	<?
	exit();
}
?>