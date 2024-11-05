<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');


$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


 if ($action=="load_drop_down_location")
{
	//echo $data;die;
	echo create_drop_down( "cbo_location_id", 130, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/finish_fabric_transfer_report_v2_controller', document.getElementById('cbo_company_id').value+'_'+this.value , 'load_drop_from_store', 'from_store_td' );" );		
	exit(); 
}
if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0);  
	exit();
}
if($action=="load_drop_to_store")
{
	$data= explode("_", $data);
	$sql="select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and b.category_type=2 and a.company_id=$data[0]  and a.status_active=1 and a.is_deleted=0  group by a.id, a.store_name order by a.store_name";
	echo create_drop_down( "cbo_to_store", 130,$sql ,"id,store_name", 1, "--Select store--", $selected, "" ,0);
	exit();
}
if($action=="load_drop_from_store")
{
	$data= explode("_", $data);
	$location_cond='';
	if(count($data)==2)
	{
		$location_cond=" and a.location_id=$data[1] ";
	}
	$sql="select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id  and a.company_id=$data[0]  and b.category_type=2 and a.status_active=1 and a.is_deleted=0 $location_cond group by a.id, a.store_name order by a.store_name";
	//echo $sql;
	echo create_drop_down( "cbo_from_store", 130, $sql,"id,store_name", 1, "--Select store--", $selected, "" ,0);
	exit();
}
if($action=="report_generate")
{ 
	$process = array( &$_POST );	
	extract(check_magic_quote_gpc( $process ));
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0","id","color_name");
	$imge_arr=return_library_array( "select master_tble_id, image_location from  common_photo_library where file_type=1",'master_tble_id','image_location');
	$store_arr = return_library_array("select a.id, a.store_name from lib_store_location a where  a.status_active=1 order by a.store_name", 'id', 'store_name');
	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	unset($data_array);
	$user_name_arr = return_library_array( "select id, user_name from user_passwd",'id','user_name');

	$cbo_company_id= str_replace("'","",$cbo_company_id);
	$cbo_location_id = str_replace("'","",trim($cbo_location_id));
	$cbo_buyer_id = str_replace("'","",trim($cbo_buyer_id));
	$start_date=str_replace("'","",trim($txt_date_from));
	$end_date=str_replace("'","",trim($txt_date_to));
	$cbo_from_job=str_replace("'","",trim($cbo_from_job));
	$cbo_to_job=str_replace("'","",trim($cbo_to_job));
	$cbo_from_store=str_replace("'","",trim($cbo_from_store));
	$cbo_to_store=str_replace("'","",trim($cbo_to_store));
	$cbo_transfer_criteria=str_replace("'","",trim($cbo_transfer_criteria));
	$cbo_year=str_replace("'","",trim($cbo_year));

	if($db_type==0)
	{
		if($cbo_year!=0) $year_cond=" and year(b.insert_date)=$cbo_year"; else $year_cond="";
	}
	else if($db_type==2)
	{
		if($cbo_year!=0) $year_cond=" and TO_CHAR(b.insert_date,'YYYY')=$cbo_year"; else $year_cond="";
	}
	
	$transfer_date='';
	$transfer_criteria='';
	$buyer_cond='';
	$location_cond ='';
	$from_store_cond='';
	$to_store_cond='';

	if(!empty($cbo_transfer_criteria) && $cbo_transfer_criteria>0){
		$transfer_criteria=" and a.transfer_criteria=$cbo_transfer_criteria ";
	}

	if(!empty($cbo_buyer_id) && $cbo_buyer_id>0)
	{
		$buyer_cond=" and c.buyer_id = $cbo_buyer_id ";
	}
	if(!empty($cbo_location_id) && $cbo_location_id>0)
	{
		$location_cond="";

		$from_store="";
		if(!empty($cbo_from_store))
		{
			$from_store=" and id=$cbo_from_store "; 
		}
		$location_sql=sql_select("SELECT id from lib_store_location where location_id=$cbo_location_id and company_id=$cbo_company_id $from_store");
		$from_store_cond;
		$store_id_arr=array();
		foreach ($location_sql as $row) 
		{
			array_push($store_id_arr, $row[csf('id')]);
		}
		$from_store_cond=where_con_using_array($store_id_arr,0,"b.from_store");
	}
	else
	{

		if(!empty($cbo_from_store))
		{
			$from_store_cond=" and b.from_store=$cbo_from_store ";
		}
	}

	if(!empty($cbo_to_store))
	{
		$to_store_cond=" and b.to_store=$cbo_to_store ";
	}

	if($db_type==0)
	{
		if ($start_date!="" &&  $end_date!="") $transfer_date  = "and  a.transfer_date  between '".change_date_format($start_date, "yyyy-mm-dd", "-")."' and '".change_date_format($end_date, "yyyy-mm-dd", "-")."'"; else $wo_date_con ="";
	}

	if($db_type==2)
	{
		if ($start_date!="" &&  $end_date!="") $transfer_date  = "and  a.transfer_date  between '".change_date_format($start_date, "yyyy-mm-dd", "-",1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-",1)."'"; else $wo_date_con ="";
	}

	$dtls_id_arr=array();
	$po_id_arr=array();
	$to_job_cond='';
	/*if(!empty($cbo_from_job) || !empty($cbo_to_job))
	{
		$to_job_cond=" and a.job_no='$cbo_to_job'";
	}*/
	$job_cond="";
	if (!empty($cbo_from_job) && empty($cbo_to_job)) 
	{
		$job_cond=" and a.job_no_prefix_num=$cbo_from_job";
	}
	elseif (!empty($cbo_to_job) && empty($cbo_from_job)) 
	{
		$job_cond=" and a.job_no_prefix_num=$cbo_to_job";
	}
	elseif (!empty($cbo_to_job) && !empty($cbo_to_job)) 
	{
		$job_cond=" and a.job_no_prefix_num in($cbo_from_job,$cbo_to_job)";
	}
	// echo $job_cond;die;

	$con = connect();
    execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (41023)");
    execute_query("delete from tmp_barcode_no where userid=$user_id and entry_form in(41023,41030)");
    oci_commit($con);

	if (!empty($cbo_from_job) || !empty($cbo_to_job)) 
	{
		$po_data_sql=sql_select("SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id
		FROM wo_po_details_master a, wo_po_break_down b  WHERE a.job_no=b.job_no_mst $job_cond $year_cond");

		if (empty($po_data_sql)) 
		{
			echo "Data not found";die;
		}
		$po_details_array=array();
		foreach($po_data_sql as $row)
		{
			$po_details_array[$row[csf("po_id")]]['job_no']=$row[csf("job_no")];
			$po_details_array[$row[csf("po_id")]]['buyer_name']=$row[csf("buyer_name")];
			$po_details_array[$row[csf("po_id")]]['style_ref_no']=$row[csf("style_ref_no")];
			$po_details_array[$row[csf("po_id")]]['year']=date("Y",strtotime($row[csf("insert_date")]));
			$po_details_array[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];
			$po_details_array[$row[csf("po_id")]]['booking_no']=$row[csf("booking_no")];
			$po_details_array[$row[csf("po_id")]]['fabric_color_id']=$row[csf("fabric_color_id")];
			//array_push($po_id_arr, $row[csf("po_id")]);
			$po_id_arr[$row[csf("po_id")]]=$row[csf("po_id")];
		}
		unset($po_data_sql);

		$po_id_arr=array_unique($po_id_arr);
		$from_po_id_cond='';$to_po_id_cond='';$po_id_cond='';

		if (!empty($cbo_from_job) && empty($cbo_to_job)) 
		{
			$from_po_id_cond=where_con_using_array($po_id_arr,0,"b.from_order_id");
			$to_po_id_cond=where_con_using_array($po_id_arr,0,"d.po_breakdown_id");
		}
		elseif (!empty($cbo_to_job) && empty($cbo_from_job)) 
		{
			$from_po_id_cond=where_con_using_array($po_id_arr,0,"b.from_order_id");
			$to_po_id_cond=where_con_using_array($po_id_arr,0,"d.po_breakdown_id");
		}
		elseif (!empty($cbo_to_job) && !empty($cbo_to_job)) 
		{
			$po_id_cond=where_con_using_array($po_id_arr,0,"nvl(d.po_breakdown_id,b.from_order_id)");
		}
	}	
	//echo $from_po_id_cond.'='.$to_po_id_cond.'='.$po_id_cond;die;

	// Transfer sql--------
	/*$sql="SELECT a.transfer_system_id,a.id,a.company_id,a.location_id,a.transfer_criteria,a.transfer_date,a.to_company,a.inserted_by,b.insert_date,b.color_id,b.gsm,b.dia_width,b.dia_width_type,b.body_part_id,b.to_body_part,b.feb_description_id,b.gsm,b.stitch_length,b.from_store,b.to_store,b.to_prod_id,b.color_id,d.barcode_no,d.booking_no,d.qnty, b.yarn_lot, b.y_count,d.po_breakdown_id,b.from_order_id,d.dtls_id 
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details d 
	where a.id=b.mst_id and  a.id=d.mst_id and b.id=d.dtls_id and a.entry_form=134 and a.status_active=1 and b.status_active=1  and d.status_active=1  and a.company_id=$cbo_company_id $transfer_date $transfer_criteria  $location_cond $to_store_cond $from_store_cond $from_po_id_cond $to_po_id_cond $po_id_cond order by a.transfer_system_id,d.id";*/

	$sql="SELECT a.transfer_system_id,a.id,a.company_id,a.location_id,a.transfer_criteria,a.transfer_date,a.to_company,a.inserted_by,a.insert_date,b.color_id, b.gsm,b.dia_width,b.body_part_id,b.to_body_part,b.feb_description_id,b.from_store,b.to_store,b.to_prod_id, d.barcode_no,d.booking_no,d.qnty,d.po_breakdown_id,d.booking_without_order,b.from_order_id,d.dtls_id, e.transaction_type, e.cons_quantity 
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, INV_TRANSACTION e, pro_roll_details d
	where a.id=b.mst_id and a.id=d.mst_id and b.id=d.dtls_id and a.id=e.mst_id and b.mst_id=e.mst_id AND e.transaction_type in(6) AND e.ITEM_CATEGORY = 2 and b.trans_id=e.id and a.entry_form=134 and a.status_active=1 and b.status_active=1 and d.status_active=1 and e.status_active=1 and a.company_id=$cbo_company_id $transfer_date $transfer_criteria  $location_cond $from_store_cond $to_store_cond $from_po_id_cond $po_id_cond
	UNION ALL
	SELECT a.transfer_system_id,a.id,a.company_id,a.location_id,a.transfer_criteria,a.transfer_date,a.to_company,a.inserted_by,a.insert_date,b.color_id, b.gsm,b.dia_width,b.body_part_id,b.to_body_part,b.feb_description_id,b.from_store,b.to_store,b.to_prod_id, d.barcode_no,d.booking_no,d.qnty,d.po_breakdown_id,d.booking_without_order,b.from_order_id,d.dtls_id, e.transaction_type, e.cons_quantity 
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, INV_TRANSACTION e, pro_roll_details d
	where a.id=b.mst_id and a.id=d.mst_id and b.id=d.dtls_id and a.id=e.mst_id and b.mst_id=e.mst_id AND e.transaction_type in(5) AND e.ITEM_CATEGORY = 2 and b.TO_TRANS_ID=e.id and a.entry_form=134 and a.status_active=1 and b.status_active=1 and d.status_active=1 and e.status_active=1 and a.company_id=$cbo_company_id $transfer_date $transfer_criteria  $location_cond $from_store_cond $to_store_cond $to_po_id_cond $po_id_cond";
	// echo $sql;die;

	$result=sql_select($sql);
	$bar_code=array();
	$count_barcode=0;
	foreach ($result as $row) 
	{
		//$bar_code[] = "'".$row[csf('barcode_no')]."'";
		if($row[csf('booking_without_order')] == 1)
		{
			$non_order_booking_buyer_po_arr[$row[csf('from_order_id')]] = $row[csf('from_order_id')];
		}
		else
		{
			$po_arr_book_booking_arr[$row[csf('from_order_id')]] = $row[csf('from_order_id')];
		}

		$po_arr_book_booking_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
		//array_push($dtls_id_arr, $row[csf('dtls_id')]);

		if ($barcode_check[$row[csf('barcode_no')]]=="") 
		{
			$barcode_check[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
			$barcode=$row[csf('barcode_no')];
			execute_query("INSERT INTO tmp_barcode_no(barcode_no,userid,entry_form) VALUES(".$barcode.", ".$user_id.",41023)");
		}
	}
	oci_commit($con);

	//$barcode_con=where_con_using_array($bar_code,0,"c.barcode_no");

	$sql_data="SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, max(b.id) as dtls_id, max(b.prod_id) as prod_id, max(b.body_part_id) as body_part_id, max(b.fabric_description_id) as febric_description_id, max(b.machine_no_id) as machine_no_id, max(b.gsm) as gsm, max(b.width) as width, max(b.color_id) as color_id,max(b.batch_id) as batch_id, max(b.floor) as floor, max(b.room) as room, max(b.rack_no) as rack, max(b.shelf_no) as self, max(b.bin) as bin_box, c.barcode_no, max(c.id) as roll_tbl_id, c.roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.amount, c.booking_without_order,c.id as roll_mst_id
	FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c, tmp_barcode_no d
	WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(37,68) and c.entry_form in(37,68) and c.status_active=1 and c.is_deleted=0 and b.barcode_no = d.barcode_no and d.userid=$user_id and d.entry_form=41023
	group by  a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, c.roll_id, c.roll_no, c.barcode_no, c.po_breakdown_id, c.qnty,c.amount, c.booking_without_order,c.id";// $barcode_con
	//echo $sql_data;

	$data_array=sql_select($sql_data);
	$barcode_wise_data=array();
	$program_with_order_arr=array();
	if(count($data_array)>0)
	{
		foreach($data_array as $val)
		{
			$barcode_wise_data[$val[csf("barcode_no")]]['color_id']=$val[csf('color_id')];
			$barcode_wise_data[$val[csf("barcode_no")]]['booking_no']=$val[csf('booking_no')];
			$barcode_wise_data[$val[csf("barcode_no")]]['booking_without_order']=$val[csf('booking_without_order')];
			$barcode_wise_data[$val[csf("barcode_no")]]['po_breakdown_id']=$val[csf('po_breakdown_id')];
			
			if($val[csf("booking_without_order")] == 1 )
			{
				$non_order_booking_buyer_po_arr[$val[csf("po_breakdown_id")]] = $val[csf("po_breakdown_id")];
			}
			else
			{
				$po_arr_book_booking_arr[$val[csf("po_breakdown_id")]] = $val[csf("po_breakdown_id")];
			}

			/*if($val[csf("receive_basis")] == 2){
				$program_id_arr[$val[csf("booking_id")]] = $val[csf("booking_id")];
			}*/
			$splitted_barcode_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
			if ($split_barcode_check[$val[csf('barcode_no')]]=="") 
			{
				$split_barcode_check[$val[csf('barcode_no')]]=$val[csf('barcode_no')];
				$barcode=$val[csf('barcode_no')];
				execute_query("INSERT INTO tmp_barcode_no(barcode_no,userid,entry_form) VALUES(".$barcode.", ".$user_id.",41030)");
			}
		}
		oci_commit($con);

		/*$splited_barcode = implode(',',array_filter($splitted_barcode_arr));
		$nxProcessedBarcode = array();
		if($splited_barcode)
		{
			//$splited_barcode_cond=where_con_using_array($splitted_barcode_arr,0,"a.barcode_no");
			$nxtProcessSql = sql_select("SELECT a.id,a.barcode_no,a.roll_no from  tmp_barcode_no t, pro_roll_details a where  t.barcode_no = a.barcode_no and t.userid=$user_id and t.entry_form=41030 and a.entry_form in (61) and a.status_active=1 and a.is_deleted=0 and a.is_returned!=1 ");// $splited_barcode_cond
			foreach ($nxtProcessSql as $val2)
			{
				$nxProcessedBarcode[$val2[csf("barcode_no")]] = $val2[csf("barcode_no")];
			}
			unset($nxtProcessSql);
			//print_r($nxProcessedBarcode);

			//$splited_barcode_cond=str_replace("a.barcode_no", "barcode_no",$splited_barcode_cond);
			$splited_roll_sql=sql_select("SELECT a.barcode_no, a.split_from_id from tmp_barcode_no t, pro_roll_split a where  t.barcode_no = a.barcode_no and t.userid=$user_id and t.entry_form=41030 and a.status_active =1 "); // $splited_barcode_cond

			foreach($splited_roll_sql as $bar)
			{
				$splited_roll_ref[$bar[csf('barcode_no')]][$bar[csf('split_from_id')]]=$bar[csf('barcode_no')];
			}
			unset($splited_roll_sql);

			$child_split_sql=sql_select("SELECT a.barcode_no, a.id from tmp_barcode_no t, pro_roll_details a where  t.barcode_no = a.barcode_no and t.userid=$user_id and t.entry_form=41030 and a.roll_split_from >0 and a.entry_form = 82 order by a.barcode_no"); // $splited_barcode_cond
			foreach($child_split_sql as $bar)
			{
				$splited_roll_ref[$bar[csf('barcode_no')]][$bar[csf('id')]]=$bar[csf('barcode_no')];
			}
			unset($child_split_sql);

			//print_r($splited_roll_ref);die;
		}*/
		//$barcode_con=str_replace("c.barcode_no", "a.barcode_no", $barcode_con);

		$production_basis_sql = sql_select("SELECT a.barcode_no, a.qc_pass_qnty_pcs, a.coller_cuff_size, a.booking_without_order, b.receive_basis, b.booking_id  from tmp_barcode_no t, pro_roll_details a, inv_receive_master b where t.barcode_no = a.barcode_no and t.userid=$user_id and t.entry_form=41023 and a.mst_id = b.id and b.entry_form = 2 and a.entry_form = 2 and a.status_active =1");// $barcode_con 
		foreach ($production_basis_sql as $val)
		{
			$production_basis_arr[$val[csf("barcode_no")]]["receive_basis"] = $val[csf("receive_basis")];
			$production_basis_arr[$val[csf("barcode_no")]]["booking_without_order"] = $val[csf("booking_without_order")];
			$production_basis_arr[$val[csf("barcode_no")]]["booking_id"] = $val[csf("booking_id")];

			if($val[csf("receive_basis")] ==2 && $val[csf("booking_without_order")] ==0)
			{
				$program_with_order_arr[$val[csf("booking_id")]] = $val[csf("booking_id")];
			}
			$production_basis_arr[$val[csf("barcode_no")]]["qnty_in_pcs"] = $val[csf("qc_pass_qnty_pcs")];
		}
		unset($production_basis_sql);

		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 41023, 2,$program_with_order_arr, $empty_arr); // program insert
    	oci_commit($con);
	}
	unset($data_array);

	$non_order_booking_buyer_po_arr = array_filter($non_order_booking_buyer_po_arr);
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 41023, 3,$non_order_booking_buyer_po_arr, $empty_arr);//non_order
    oci_commit($con);

	if(count($non_order_booking_buyer_po_arr)>0)
	{
		//$non_order_id_cond=where_con_using_array($non_order_booking_buyer_po_arr,0,"id");
		$non_order_sql = sql_select("SELECT b.id, b.buyer_id, b.booking_no from GBL_TEMP_ENGINE t, wo_non_ord_samp_booking_mst b where t.ref_val=b.id and t.user_id=$user_id and t.entry_form=41023 and t.ref_from=3 and b.is_deleted=0 and b.status_active=1");// $non_order_id_cond 
		foreach ($non_order_sql as  $val)
		{
			$book_buyer_arr[$val[csf("id")]] = $val[csf("buyer_id")];
			$non_booking_arr[$val[csf("id")]] = $val[csf("booking_no")];
		}
		unset($non_order_sql);
	}

	$po_arr_book_booking_arr = array_filter($po_arr_book_booking_arr);
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 41023, 1,$po_arr_book_booking_arr, $empty_arr); // po id insert
    oci_commit($con);

	if(count($po_arr_book_booking_arr)>0)
	{
		//$po_booking_cond=where_con_using_array($po_arr_book_booking_arr,0,"a.po_break_down_id");
		if(count($program_with_order_arr))
		{
			$program_with_order_cond=where_con_using_array($program_with_order_arr,0,"c.id");
			$book_booking_sql=sql_select("SELECT a.po_break_down_id, a.booking_no , c.id as plan_id 
			from GBL_TEMP_ENGINE t, wo_booking_dtls a 
			left join ppl_planning_info_entry_mst b on a.booking_no = b.booking_no 
			left join  ppl_planning_info_entry_dtls c on b.id = c.mst_id $program_with_order_cond 
			where  a.is_deleted=0 and a.status_active=1 and a.booking_type in (1,4)
			and t.ref_val=a.po_break_down_id and t.user_id=$user_id and t.entry_form=41023 and t.ref_from=1
			group by a.po_break_down_id, a.booking_no ,c.id");// $po_booking_cond 

			foreach ($book_booking_sql as $val)
			{
				$book_booking_arr_plan_wise[$val[csf("po_break_down_id")]][$val[csf("plan_id")]] = $val[csf("booking_no")];
			}
			unset($book_booking_sql);
			// echo "<pre>";print_r($book_booking_arr_plan_wise);
		}
		else
		{
			//$po_booking_cond=str_replace("a.po_break_down_id", "po_break_down_id", $po_booking_cond);
			$book_booking_arr=return_library_array("SELECT b.po_break_down_id as po_break_down_id, b.booking_no as booking_no from GBL_TEMP_ENGINE t, wo_booking_dtls b where t.ref_val=b.po_break_down_id and t.user_id=$user_id and t.entry_form=41023 and t.ref_from=1 and b.is_deleted=0 and b.status_active=1 and b.booking_type=1 ",'po_break_down_id','booking_no');// $po_booking_cond
			// echo "<pre>";print_r($book_booking_arr);
		}
	}

	//$po_booking_cond=where_con_using_array($po_arr_book_booking_arr,0,"b.id");
	$po_sql=sql_select("SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id,c.booking_no,c.fabric_color_id
	FROM GBL_TEMP_ENGINE t, wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c  WHERE a.job_no=b.job_no_mst and t.ref_val=b.id and t.user_id=$user_id and t.entry_form=41023 and t.ref_from=1 and b.id=c.po_break_down_id and c.booking_type in (1,4)");// $po_booking_cond 
	$po_details_array=array();
	foreach($po_sql as $row)
	{
		$po_details_array[$row[csf("po_id")]]['job_no']=$row[csf("job_no")];
		$po_details_array[$row[csf("po_id")]]['buyer_name']=$row[csf("buyer_name")];
		$po_details_array[$row[csf("po_id")]]['style_ref_no']=$row[csf("style_ref_no")];
		$po_details_array[$row[csf("po_id")]]['year']=date("Y",strtotime($row[csf("insert_date")]));
		$po_details_array[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];
		$po_details_array[$row[csf("po_id")]]['booking_no']=$row[csf("booking_no")];
		$po_details_array[$row[csf("po_id")]]['fabric_color_id']=$row[csf("fabric_color_id")];
	}
	unset($po_sql);

	// Main loop Data Array here--------------------------
	foreach ($result as $row)
	{
		//$po_breakdown_id=$barcode_wise_data[$row[csf("barcode_no")]]['po_breakdown_id'];
		if($barcode_wise_data[$row[csf("barcode_no")]]['booking_without_order']==1)
		{
			//$booking_no_fab=$non_booking_arr[$po_breakdown_id];
			$booking_no_fab=$non_booking_arr[$row[csf("from_order_id")]].'=A';
		}
		else
		{
			if($production_basis_arr[$row[csf('barcode_no')]]["receive_basis"] == 2 && $production_basis_arr[$row[csf('barcode_no')]]["booking_without_order"] == 0 )
			{
				$plan_id = $production_basis_arr[$row[csf('barcode_no')]]["booking_id"];
				// $booking_no_fab = $book_booking_arr_plan_wise[$po_breakdown_id][$plan_id];
				$booking_no_fab = $book_booking_arr_plan_wise[$row[csf("from_order_id")]][$plan_id].'=B';
			}
			else
			{
				// $booking_no_fab=$book_booking_arr[$po_breakdown_id];
				$booking_no_fab=$book_booking_arr[$row[csf("from_order_id")]].'=C';
			}
		}

		$str_ref=$row[csf("from_order_id")].'*'.$row[csf("po_breakdown_id")].'*'.$row[csf("to_body_part")].'*'.$row[csf("feb_description_id")].'*'.$row[csf("color_id")].'*'.$row[csf("gsm")].'*'.$row[csf("dia_width")];
		$transfer_data_array[$row[csf("transfer_system_id")]][$str_ref]['transfer_date']=$row[csf("transfer_date")];
		$transfer_data_array[$row[csf("transfer_system_id")]][$str_ref]['company_id']=$row[csf("company_id")];
		$transfer_data_array[$row[csf("transfer_system_id")]][$str_ref]['from_store']=$row[csf("from_store")];
		$transfer_data_array[$row[csf("transfer_system_id")]][$str_ref]['from_booking']=$booking_no_fab;

		$transfer_data_array[$row[csf("transfer_system_id")]][$str_ref]['to_company']=$row[csf("to_company")];
		$transfer_data_array[$row[csf("transfer_system_id")]][$str_ref]['to_store']=$row[csf("to_store")];
		$transfer_data_array[$row[csf("transfer_system_id")]][$str_ref]['qnty']=$row[csf("qnty")];
		$transfer_data_array[$row[csf("transfer_system_id")]][$str_ref]['inserted_by']=$row[csf("inserted_by")];
		$transfer_data_array[$row[csf("transfer_system_id")]][$str_ref]['insert_date']=$row[csf("insert_date")];

		if ($row[csf("transaction_type")]==5) 
		{
			$transfer_data_array[$row[csf("transfer_system_id")]][$str_ref]['trans_in_qty']+=$row[csf("cons_quantity")];
			$transfer_in_kg_qty+=$row[csf("cons_quantity")];
			$roll_count_in++;
			$qnty_in_pcs+=$production_basis_arr[$row[csf('barcode_no')]]["qnty_in_pcs"];
		}
		else
		{
			$transfer_data_array[$row[csf("transfer_system_id")]][$str_ref]['trans_out_qty']+=$row[csf("cons_quantity")];
			$transfer_out_kg_qty+=$row[csf("cons_quantity")];
			$roll_count_out++;
			$qnty_out_pcs+=$production_basis_arr[$row[csf('barcode_no')]]["qnty_in_pcs"];
		}
	}
	// echo "<pre>";print_r($transfer_data_array);

	execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (41023)");
    execute_query("delete from tmp_barcode_no where userid=$user_id and entry_form in(41023,41030)");
    oci_commit($con);
	
	// Data Show here-------------------------
	$table_width="2670"; $colspan="26";
	ob_start();
	?>
	<style type="text/css">
        .word_wrap_break {
            word-break: break-all;
            word-wrap: break-word;
        }
    </style>
    <fieldset style="width:1825px;">
    	
        <table cellpadding="0" cellspacing="0" width="<?=$table_width; ?>" border="0" rules="all">
            <tr class="form_caption">
               <td align="center" width="100%" colspan="<?=$colspan; ?>" style="font-size:16px"><strong><?=$company_arr[$cbo_company_id]; ?></strong></td>
            </tr>
            <tr class="form_caption">
               <td align="center" width="100%" colspan="<?=$colspan; ?>" style="font-size:16px"><strong><?=$report_title; ?></strong></td>
            </tr>
        </table>
       	
       	<div align="left">
       	<table class="rpt_table" width="<?=$table_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
            <thead>
                <tr>
                	<th width="40">SL</th>
                    <th width="70">Transfer Date</th>
                    <th width="120">Transfer ID</th>
                    <th width="130">From Company</th>
                    <th width="150">From Store</th>
                    <th width="100">From Job</th>
                    <th width="120">From Booking</th>
                    <th width="100">From Order</th>

                    <th width="120">To Company</th>
                    <th width="150">To Store</th>
                    <th width="100">To Job</th>
                    <th width="100">To Booking</th>
                    <th width="100">To Order</th>
                    <th width="110" title="To Body Part">Body Part</th>
                    
                    <th width="120">Fabric Construction</th>
                    <th width="180">Fabric Composition</th>
                    <th width="180">Yarn Composition (Actual)</th>
                    <th width="110">Fabric Color</th>
                    <th width="50">GSM</th>
                    <th width="50">Dia</th>
                    <th width="50">UOM</th>
                    <th width="70">Transfer In</th>
                    <th width="70">Transfer Out</th>
                    <th width="70">User ID</th>
                    <th width="">Insert Time</th>
                </tr>
           	</thead>
        </table>

        <div style=" max-height:350px; width:<?=$table_width+20; ?>px; overflow-y:scroll;" id="scroll_body">
        	<table class="rpt_table" id="scanning_table" width="<?=$table_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all">	            
	           	<tbody >
					<?php					
					$i=1;
					foreach ($transfer_data_array as $transfer_no => $transfer_noAr) 
					{
						foreach ($transfer_noAr as $ref_str => $row) 
						{
							$str_ref=$row[csf("from_order_id")].'*'.$row[csf("po_breakdown_id")].'*'.$row[csf("to_body_part")].'*'.$row[csf("feb_description_id")].'*'.$row[csf("color_id")].'*'.$row[csf("gsm")].'*'.$row[csf("dia_width")];
							$data=explode("*", $ref_str);
							$from_order_id=$data[0];
							$po_breakdown_id=$data[1];//to_order_id
							$to_body_part=$data[2];
							$feb_description_id=$data[3];
							$color_id=$data[4];
							$gsm=$data[5];
							$dia=$data[6];

							$color_ids_arr=explode(",",$color_id);
							foreach($color_ids_arr as $val)
							{
								if($val>0) $color.=$color_arr[$val].",";
							}
							$color=chop($color,',');

							$compsition_description=$composition_arr[$feb_description_id];
							$constructtion=$constructtion_arr[$feb_description_id];
							
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>" >
								<td  width="40"><?=$i; ?></td>
								<td  width="70"><p><?=change_date_format($row['transfer_date']); ?>&nbsp;</p></td>
								<td width="120" class="word_wrap_break"><p><?=$transfer_no; ?></p></td>
								<td width="130" class="word_wrap_break"><p><?=$company_arr[$row['company_id']]; ?></p></td>
								<td width="150" class="word_wrap_break"><p><?=$store_arr[$row['from_store']]; ?></p></td>
								<td width="100" class="word_wrap_break"><p><?=$po_details_array[$from_order_id]['job_no']; ?></p></td>
								<td width="120" class="word_wrap_break"><p><?=$po_details_array[$from_order_id]['booking_no']; //$booking_no_fab ; ?></p></td>
								<td width="100" class="word_wrap_break" title="From Order ID: <? echo $from_order_id;?>"><p><?=$po_details_array[$from_order_id]['po_number']; ?></p></td>

								<td width="120" class="word_wrap_break"><p><?=$company_arr[$row['to_company']]; ?></p></td>
								<td width="150" class="word_wrap_break"><p><?=$store_arr[$row['to_store']]; ?></p></td>
								<td width="100" class="word_wrap_break"><p><?=$po_details_array[$po_breakdown_id]['job_no']; ?></p></td>
								<td width="100" class="word_wrap_break"><p><?=$po_details_array[$po_breakdown_id]['booking_no']; ?></p></td>
								<td width="100" class="word_wrap_break" title="To Order ID: <? echo $po_breakdown_id;?>"><p><?=$po_details_array[$po_breakdown_id]['po_number']; ?></p></td>
								<td width="110" title="<?=$to_body_part;?>"><p><?=$body_part[$to_body_part]; ?></p></td>
								
								<td width="120" class="word_wrap_break" title="<?=$feb_description_id;?>"><p><?=$constructtion; ?></p></td>
								<td width="180" class="word_wrap_break"><p><?=$compsition_description; ?></p></td>
								<td width="180" class="word_wrap_break"><p><?=$constructtion.', '.$compsition_description ; ?></p></td>
								<td width="110" class="word_wrap_break"><p><?=$color; ?></p></td>
								<td width="50" class="word_wrap_break"><p><?=$gsm; ?></p></td>
								<td width="50" class="word_wrap_break"><p><?=$dia; ?></p></td>
								<td width="50" class="word_wrap_break"><p><?='Kg'; ?></p></td>
								<td width="70" class="word_wrap_break" align="right"><p><?= number_format($row['trans_in_qty'],2,'.',''); ?></p></td>
								<td width="70" class="word_wrap_break" align="right"><p><?= number_format($row['trans_out_qty'],2,'.',''); ?></p></td>
								<td width="70" class="word_wrap_break"><p><?= $user_name_arr[$row['inserted_by']]; ?></p></td>
								<td width="" class="word_wrap_break"><p><?= $row['insert_date']; ?></p></td>
							</tr>
							<?	
							$total_transfer_in_qty+=$row['trans_in_qty'];
							$total_transfer_out_qty+=$row['trans_out_qty'];
							$i++;
						}
					}
					?>				
				</tbody>				 
	       	</table>
       	</div>

       	<table class="rpt_table" width="<?=$table_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all">
       		<tfoot>
           		<tr>
                	<th width="40"></th>
                    <th width="70"></th>
                    <th width="120"></th>
                    <th width="130"></th>
                    <th width="150"></th>
                    <th width="100"></th>
                    <th width="120"></th>
                    <th width="100"></th>

                    <th width="120"></th>
                    <th width="150"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="110" title="To Body Part"></th>
                    
                    <th width="120"></th>
                    <th width="180"></th>
                    <th width="180"></th>
                    <th width="110"></th>
                    <th width="50"></th>
                    <th width="50"></th>
                    <th width="50">G. Total</th>
                    <th width="70" id="value_trans_in_qty"><?=number_format($total_transfer_in_qty,2,'.','');?></th>
                    <th width="70" id="value_trans_out_qty"><?=number_format($total_transfer_out_qty,2,'.','');?></th>
                    <th width="70"></th>
                    <th width=""></th>
                </tr>
       		</tfoot>
       	</table>
       	</div>
       	<!-- Details part end -->
       	<br>
       	<!-- Summary Start -->
       	<div style="width: 300px;float: left;justify-content: center;text-align: center;">
           <table class="rpt_table" border="1" rules="all" width="300" cellpadding="0" cellspacing="0" style="float: left;" >
	            <thead>
	                <tr>
	                	<th colspan="2">TRANSFER SUMMERY</th>
	                	<th>TOTAL</th>		                	
	                </tr>
	            </thead> 
	           	<tbody>
	           		<tr bgcolor="#FFFFFF">
                        <td rowspan="3" align="center" style="font-size: 13px; width: 80px; vertical-align: middle;"><strong>TRANSFER IN</strong></td>
                        <td style="font-size: 13px; width: 60px;" align="left">Issue Qty KG</td>
                        <td align="right" style="font-size: 13px; width: 60px"><? echo number_format($transfer_in_kg_qty,2,'.',''); ?></td>
                    </tr>
                    <tr bgcolor="#E9F3FF">
                        <td style="font-size: 13px;" align="left">Issue Qty PCS</td>
                        <td align="right" style="font-size: 13px;"><? echo number_format($qnty_in_pcs,2,'.',''); ?></td>
                    </tr>
                    <tr bgcolor="#FFFFFF">
                        <td style="font-size: 13px;" align="left">Roll Qty</td>
                        <td align="right" style="font-size: 13px;"><? echo number_format($roll_count_in,2,'.',''); ?></td>
                    </tr>

                    <tr bgcolor="#E9F3FF">
                        <td rowspan="3" align="center" style="font-size: 13px; vertical-align: middle;"><strong>TRANSFER Out</strong></td>
                        <td style="font-size: 13px;" align="left">Issue Qty KG</td>
                        <td align="right" style="font-size: 13px;"><? echo number_format($transfer_out_kg_qty,2,'.',''); ?></td>
                    </tr>
                    <tr bgcolor="#FFFFFF">
                        <td style="font-size: 13px;" align="left">Issue Qty PCS</td>
                        <td align="right" style="font-size: 13px;"><? echo number_format($qnty_out_pcs,2,'.',''); ?></td>
                    </tr>
                    <tr bgcolor="#E9F3FF">
                        <td style="font-size: 13px;" align="left">Roll Qty</td>
                        <td align="right" style="font-size: 13px;"><? echo number_format($roll_count_out,2,'.',''); ?></td>
                    </tr>

				</tbody>
	        </table>
    	</div>
        <!-- Summary End -->
    </fieldset>
	<?

	$html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    	@unlink($filename);
    }
    
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    //$filename_summary = $user_id."_".$name . "short.xls";
    $create_new_doc = fopen($filename, 'w');
    //$create_new_doc_summary = fopen($filename_summary, 'w');
    $is_created = fwrite($create_new_doc, $html);
    //$is_created_short = fwrite($create_new_doc_summary, $summary_html);
    echo "$html####$filename";
    exit();
}
?>