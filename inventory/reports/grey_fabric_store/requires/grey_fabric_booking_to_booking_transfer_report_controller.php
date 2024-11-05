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
	echo create_drop_down( "cbo_location_id", 130, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/grey_fabric_booking_to_booking_transfer_report_controller', document.getElementById('cbo_company_id').value+'_'+this.value , 'load_drop_from_store', 'from_store_td' );" );		
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
	
	//echo "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and b.category_type=13 and a.company_id=$data[0] and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name";die;
	$sql="select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and b.category_type=13 and a.company_id=$data[0]  and a.status_active=1 and a.is_deleted=0  group by a.id, a.store_name order by a.store_name";
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
	$sql="select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id  and a.company_id=$data[0]  and b.category_type=13 and a.status_active=1 and a.is_deleted=0 $location_cond group by a.id, a.store_name order by a.store_name";
	//echo $sql;
	echo create_drop_down( "cbo_from_store", 130, $sql,"id,store_name", 1, "--Select store--", $selected, "" ,0);
	exit();
}
if($action=="report_generate")
{ 
	$process = array( &$_POST );

	// echo "<pre>";
	// print_r($process);
	// echo "</pre>";
	// die;
	
	extract(check_magic_quote_gpc( $process ));
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0","id","color_name");
	$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count where status_active =1 and is_deleted=0","id","yarn_count");

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

	$cbo_company_id= str_replace("'","",$cbo_company_id);
	$cbo_location_id = str_replace("'","",trim($cbo_location_id));
	$cbo_buyer_id = str_replace("'","",trim($cbo_buyer_id));
	$start_date=str_replace("'","",trim($txt_date_from));
	$end_date=str_replace("'","",trim($txt_date_to));
	$cbo_int_ref=str_replace("'","",trim($cbo_int_ref));
	$cbo_from_job=str_replace("'","",trim($cbo_from_job));
	$cbo_to_job=str_replace("'","",trim($cbo_to_job));
	$cbo_from_booking=str_replace("'","",trim($cbo_from_booking));
	$cbo_to_booking=str_replace("'","",trim($cbo_to_booking));
	$cbo_from_store=str_replace("'","",trim($cbo_from_store));
	$cbo_to_store=str_replace("'","",trim($cbo_to_store));
	$cbo_transfer_criteria=str_replace("'","",trim($cbo_transfer_criteria));
	$cbo_year_selection=str_replace("'","",trim($cbo_year_selection));
	$from_booking_flag=0;
	$to_booking_flag=0;

	if(!empty($cbo_from_booking))
	{
		$from_booking_flag=1;
	}
	if(!empty($cbo_to_booking))
	{
		$to_booking_flag=1;
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
	if(!empty($cbo_to_booking) || !empty($cbo_to_job) )
	{
		//$po_booking_cond=where_con_using_array($po_arr_book_booking_arr,0,"b.id");

		$po_id_arr=array();
		$to_booking_cond='';
		if(!empty($cbo_to_booking))
		{
			$to_booking_cond=" and c.booking_no like '%$cbo_to_booking%'";
		}
		$to_job_cond='';
		if(!empty($cbo_to_job))
		{
			$to_job_cond=" and a.job_no='$cbo_to_job'";
		}

		$po_data_sql=sql_select("SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id,c.booking_no,c.fabric_color_id
 		FROM wo_po_details_master a, wo_po_break_down b ,wo_booking_dtls c  WHERE a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.booking_type in (1,4) $to_booking_cond $to_job_cond");

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
			array_push($po_id_arr, $row[csf("po_id")]);
		}
		unset($po_data_sql);
		

		$po_id_arr=array_unique($po_id_arr);
		$po_id_cond='';

		
		$po_id_cond=where_con_using_array($po_id_arr,0,"nvl(d.po_breakdown_id,b.from_order_id)");


		$sql="SELECT a.transfer_system_id,a.id,a.company_id,a.location_id,a.transfer_criteria,a.transfer_date,a.to_company,b.color_id,b.dia_width,b.dia_width_type,b.body_part_id,b.feb_description_id,b.gsm,b.stitch_length,b.from_store,b.to_store,b.to_prod_id,d.barcode_no,d.booking_no,d.qnty,a.remarks , b.yarn_lot, b.y_count,d.po_breakdown_id,b.from_order_id,d.dtls_id 
		from inv_item_transfer_mst a,inv_item_transfer_dtls b  , pro_roll_details d 
		where a.id=b.mst_id and  a.id=d.mst_id and b.id=d.dtls_id and a.status_active=1 and b.status_active=1  and d.status_active=1  and a.company_id=$cbo_company_id $transfer_date $transfer_criteria  $location_cond $to_store_cond $from_store_cond $po_id_cond order by a.transfer_system_id,d.id";
		//echo $sql;die;
		$result=sql_select($sql);
		$mst_ids='';
		$bar_code=array();
		$sys_id='';

		$count_barcode=0;
		foreach ($result as $row) 
		{
			$bar_code[] = "'".$row[csf('barcode_no')]."'";
			if($row[csf('from_booking_without_order')] == 1)
			{
				$non_order_booking_buyer_po_arr[$row[csf('from_order_id')]] = $row[csf('from_order_id')];
			}
			else
			{
				$po_arr_book_booking_arr[$row[csf('from_order_id')]] = $row[csf('from_order_id')];
			}

			$po_arr_book_booking_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
			array_push($dtls_id_arr, $row[csf('dtls_id')]);
		}

		$barcode_con=where_con_using_array($bar_code,0,"c.barcode_no");
		

		$sql_data="SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, max(b.id) as dtls_id, max(b.prod_id) as prod_id, max(b.body_part_id) as body_part_id, max(b.febric_description_id) as febric_description_id, max(b.machine_no_id) as machine_no_id, max(b.gsm) as gsm, max(b.width) as width, max(b.color_id) as color_id, max(b.yarn_lot) as yarn_lot, max(b.yarn_count) as yarn_count, max(b.stitch_length) as stitch_length, max(b.brand_id) as brand_id, max(b.floor_id) as floor, max(b.room) as room, max(b.rack) as rack, max(b.self) as self, c.barcode_no, max(c.id) as roll_tbl_id, c.roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.amount, c.booking_without_order,c.id as roll_mst_id
		FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
		WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 $barcode_con
		group by  a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, c.roll_id, c.roll_no, c.barcode_no, c.po_breakdown_id, c.qnty,c.amount, c.booking_without_order,c.id";
		$data_array=sql_select($sql_data);

		$barcode_wise_data=array();
		$program_with_order_arr=array();

		if(count($data_array)>0)
		{
			foreach($data_array as $val)
			{
				$splitted_barcode_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
				$barcode_wise_data[$val[csf("barcode_no")]]['color_id']=$val[csf('color_id')];
				$barcode_wise_data[$val[csf("barcode_no")]]['booking_no']=$val[csf('booking_no')];
				$barcode_wise_data[$val[csf("barcode_no")]]['booking_without_order']=$val[csf('booking_without_order')];
				$barcode_wise_data[$val[csf("barcode_no")]]['po_breakdown_id']=$val[csf('po_breakdown_id')];
				

				if($val[csf("booking_without_order")] == 1 )
				{
					$non_order_booking_buyer_po_arr[$val[csf("po_breakdown_id")]] = $val[csf("po_breakdown_id")];
				}else{
					$po_arr_book_booking_arr[$val[csf("po_breakdown_id")]] = $val[csf("po_breakdown_id")];
				}

				/*if($val[csf("receive_basis")] == 2){
					$program_id_arr[$val[csf("booking_id")]] = $val[csf("booking_id")];
				}*/
			}

			$splited_barcode = implode(',',array_filter($splitted_barcode_arr));
			$nxProcessedBarcode = array();
			if($splited_barcode)
			{
				$splited_barcode_cond=where_con_using_array($splitted_barcode_arr,0,"a.barcode_no");
				$nxtProcessSql = sql_select("select a.id,a.barcode_no,a.roll_no from  pro_roll_details a where  and a.entry_form in (61) and a.status_active=1 and a.is_deleted=0 and a.is_returned!=1 $splited_barcode_cond ");
				foreach ($nxtProcessSql as $val2)
				{
					$nxProcessedBarcode[$val2[csf("barcode_no")]] = $val2[csf("barcode_no")];
				}
				unset($nxtProcessSql);
				//print_r($nxProcessedBarcode);

				$splited_barcode_cond=str_replace("a.barcode_no", "barcode_no",$splited_barcode_cond);
				$splited_roll_sql=sql_select("select barcode_no,split_from_id from pro_roll_split where status_active =1 $splited_barcode_cond ");

				foreach($splited_roll_sql as $bar)
				{
					$splited_roll_ref[$bar[csf('barcode_no')]][$bar[csf('split_from_id')]]=$bar[csf('barcode_no')];
				}
				unset($splited_roll_sql);

				$child_split_sql=sql_select("select barcode_no, id from pro_roll_details where roll_split_from >0 $splited_barcode_cond and entry_form = 82 order by barcode_no");
				foreach($child_split_sql as $bar)
				{
					$splited_roll_ref[$bar[csf('barcode_no')]][$bar[csf('id')]]=$bar[csf('barcode_no')];
				}
				unset($child_split_sql);

				//print_r($splited_roll_ref);die;

			}
			$barcode_con=str_replace("c.barcode_no", "a.barcode_no", $barcode_con);

			$production_basis_sql = sql_select("select a.barcode_no, b.receive_basis, a.booking_without_order, b.booking_id  from pro_roll_details a, inv_receive_master b where a.mst_id = b.id and b.entry_form = 2 and a.entry_form = 2 and a.status_active =1 $barcode_con ");
			foreach ($production_basis_sql as $val)
			{
				$production_basis_arr[$val[csf("barcode_no")]]["receive_basis"] = $val[csf("receive_basis")];
				$production_basis_arr[$val[csf("barcode_no")]]["booking_without_order"] = $val[csf("booking_without_order")];
				$production_basis_arr[$val[csf("barcode_no")]]["booking_id"] = $val[csf("booking_id")];

				if($val[csf("receive_basis")] ==2 && $val[csf("booking_without_order")] ==0)
				{
					$program_with_order_arr[$val[csf("booking_id")]] = $val[csf("booking_id")];
				}

			}
			unset($production_basis_sql);
		}
		unset($data_array);

		$non_order_booking_buyer_po_arr = array_filter($non_order_booking_buyer_po_arr);
		//$book_buyer_arr=return_library_array("select id, buyer_id from wo_non_ord_samp_booking_mst where is_deleted=0 and status_active=1 and id in (".implode(',', $non_order_booking_buyer_po_arr).")",'id','buyer_id');

		if(count($non_order_booking_buyer_po_arr)>0)
		{
			$non_order_id_cond=where_con_using_array($non_order_booking_buyer_po_arr,0,"id");
			$non_order_sql = sql_select("select id, buyer_id, booking_no from wo_non_ord_samp_booking_mst where is_deleted=0 and status_active=1 $non_order_id_cond ");
			foreach ($non_order_sql as  $val)
			{
				$book_buyer_arr[$val[csf("id")]] = $val[csf("buyer_id")];
				$non_booking_arr[$val[csf("id")]] = $val[csf("booking_no")];
			}
			unset($non_order_sql);
		}

		$po_arr_book_booking_arr = array_filter($po_arr_book_booking_arr);
		if(count($po_arr_book_booking_arr)>0)
		{
			$po_booking_cond=where_con_using_array($po_arr_book_booking_arr,0,"a.po_break_down_id");
			if(count($program_with_order_arr))
			{
				$program_with_order_cond=where_con_using_array($program_with_order_arr,0,"c.id");
				$book_booking_sql=sql_select("SELECT a.po_break_down_id, a.booking_no , c.id as plan_id from wo_booking_dtls a left join ppl_planning_info_entry_mst b on a.booking_no = b.booking_no left join  ppl_planning_info_entry_dtls c on b.id = c.mst_id $program_with_order_cond where  a.is_deleted=0 and a.status_active=1 and a.booking_type in (1,4) $po_booking_cond group by a.po_break_down_id, a.booking_no ,c.id");

				foreach ($book_booking_sql as $val)
				{
					$book_booking_arr_plan_wise[$val[csf("po_break_down_id")]][$val[csf("plan_id")]] = $val[csf("booking_no")];
				}
				unset($book_booking_sql);
			}
			else
			{
				$po_booking_cond=str_replace("a.po_break_down_id", "po_break_down_id", $po_booking_cond);
				$book_booking_arr=return_library_array("SELECT po_break_down_id, booking_no from wo_booking_dtls where is_deleted=0 and status_active=1 and booking_type=1 $po_booking_cond ",'po_break_down_id','booking_no');
			}
		}
	}
	else if(!empty($cbo_from_booking) || !empty($cbo_from_job) || !empty($cbo_int_ref) )
	{
		if ($db_type == 0)
		{
			$year_cond=" and YEAR(insert_date)=$cbo_year_selection ";
		}
		else if ($db_type == 2)
		{
			$year_cond = " and to_char(insert_date,'YYYY')=$cbo_year_selection ";
		}

		$from_job_cond='';
		if(!empty($cbo_from_job))
		{
			$from_job_cond=" and a.job_no_mst='$cbo_from_job'";
		}

		$from_int_ref_cond='';
		if(!empty($cbo_int_ref))
		{
			$from_int_ref_cond=" and a.grouping='$cbo_int_ref'";
		}
		// echo $from_int_ref_cond;die;
		$booking_po_ids=array();
		$from_booking_cond='';
		if(!empty($cbo_from_booking))
		{
			$from_booking_cond=" and b.booking_no like '%$cbo_from_booking%'";
			$from_booking_cond2="  and a.booking_no like '%$cbo_from_booking%'";
			$non_order_sql = sql_select("SELECT id, buyer_id, booking_no from wo_non_ord_samp_booking_mst where is_deleted=0 and status_active=1 and booking_no like '%$cbo_from_booking%'");		
			foreach ($non_order_sql as  $val)
			{
				$book_buyer_arr[$val[csf("id")]] = $val[csf("buyer_id")];
				$non_booking_arr[$val[csf("id")]] = $val[csf("booking_no")];
				array_push($booking_po_ids, $val[csf("id")]);
			}
			unset($non_order_sql);
		}

		// $book_booking_arr=return_library_array("SELECT po_break_down_id, booking_no from wo_booking_dtls where is_deleted=0 and status_active=1 and booking_no like '%$cbo_from_booking%'  ",'po_break_down_id','booking_no');
		$book_booking_arr=return_library_array("SELECT b.po_break_down_id, b.booking_no from wo_po_break_down a, wo_booking_dtls b where a.id=b.po_break_down_id and b.is_deleted=0 and b.status_active=1 and b.booking_type in (1,4) $from_booking_cond $from_job_cond $from_int_ref_cond",'po_break_down_id','booking_no');
		// echo "<pre>";print_r($book_booking_arr);die;

		foreach ($book_booking_arr as $key => $value) {
			array_push($booking_po_ids, $key);
			$po_break_down_id_arr[$key] = $key;
		}
		if (!empty($book_booking_arr)) 
		{
			$order_cond=" and a.po_break_down_id in (".implode(",", $po_break_down_id_arr).")";
		}
		// echo $order_cond;die;
		//unset($book_booking_arr);

		if(!empty($cbo_from_booking) || !empty($book_booking_arr))
		{
			$year_cond=str_replace("insert_date", "a.insert_date", $year_cond);
			$book_booking_sql=sql_select("SELECT a.po_break_down_id, a.booking_no , c.id as plan_id from wo_booking_dtls a left join ppl_planning_info_entry_mst b on a.booking_no = b.booking_no left join  ppl_planning_info_entry_dtls c on b.id = c.mst_id  where  a.is_deleted=0 and a.status_active=1 and a.booking_type in (1,4) $from_booking_cond2 $order_cond  group by a.po_break_down_id, a.booking_no ,c.id");

			foreach ($book_booking_sql as $val)
			{
				$book_booking_arr_plan_wise[$val[csf("po_break_down_id")]][$val[csf("plan_id")]] = $val[csf("booking_no")];
				array_push($booking_po_ids, $val[csf("po_break_down_id")]);
			}
		}
		$booking_po_ids=array_unique($booking_po_ids);

		$po_id_conds='';
		function where_con($arrayData,$dataType=0,$table_coloum)
		{
			$chunk_list_arr=array_chunk($arrayData,999);
			$p=1;
			foreach($chunk_list_arr as $process_arr)
			{
				if($dataType==0){
					if($p==1){$sql .="  (".$table_coloum." in(".implode(',',$process_arr).")"; }
					else {$sql .=" or ".$table_coloum." in(".implode(',',$process_arr).")";}
				}
				else{
					if($p==1){$sql .="  (".$table_coloum." in('".implode("','",$process_arr)."')"; }
					else {$sql .=" or ".$table_coloum." in('".implode("','",$process_arr)."')";}
				}
				$p++;
			}
			
			$sql.=") ";
			return $sql;
		}
		if(count($booking_po_ids))
		{			
			$po_id_conds=" and ( ". where_con($booking_po_ids,0,"d.po_breakdown_id") . " or  ".where_con($booking_po_ids,0,"b.from_order_id"). ")";
		}
		else
		{
			$po_id_conds=" and d.po_breakdown_id in () or b.from_order_id in ()";
		}
		
		unset($book_booking_sql);

		
		$sql="SELECT a.transfer_system_id,a.id,a.company_id,a.location_id,a.transfer_criteria,a.transfer_date,a.to_company,b.color_id,b.dia_width,b.dia_width_type,b.body_part_id,b.feb_description_id,b.gsm,b.stitch_length,b.from_store,b.to_store,b.to_prod_id,d.barcode_no,d.booking_no,d.qnty,a.remarks , b.yarn_lot, b.y_count,d.po_breakdown_id,b.from_order_id,d.dtls_id 
		from inv_item_transfer_mst a,inv_item_transfer_dtls b, pro_roll_details d 
		where a.id=b.mst_id and  a.id=d.mst_id and b.id=d.dtls_id and a.status_active=1 and b.status_active=1  and d.status_active=1  and a.company_id=$cbo_company_id $transfer_date $transfer_criteria  $location_cond $to_store_cond $from_store_cond $po_id_conds order by a.transfer_system_id,d.id";
		// echo $sql;die;
		$result=sql_select($sql);
		$mst_ids='';
		$bar_code=array();
		$sys_id='';

		$count_barcode=0;
		
		foreach ($result as $row) 
		{
			$bar_code[] = "'".$row[csf('barcode_no')]."'";
			if($row[csf('from_booking_without_order')] == 1)
			{
				$non_order_booking_buyer_po_arr[$row[csf('from_order_id')]] = $row[csf('from_order_id')];
			}
			else
			{
				$po_arr_book_booking_arr[$row[csf('from_order_id')]] = $row[csf('from_order_id')];
			}

			$po_arr_book_booking_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
			array_push($dtls_id_arr, $row[csf('dtls_id')]);
		}
		//$barcode_con=" a.booking_no like '%$cbo_from_booking%' )";
		$barcode_con=where_con_using_array($bar_code,0,"c.barcode_no");
		

		$sql_data="SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, max(b.id) as dtls_id, max(b.prod_id) as prod_id, max(b.body_part_id) as body_part_id, max(b.febric_description_id) as febric_description_id, max(b.machine_no_id) as machine_no_id, max(b.gsm) as gsm, max(b.width) as width, max(b.color_id) as color_id, max(b.yarn_lot) as yarn_lot, max(b.yarn_count) as yarn_count, max(b.stitch_length) as stitch_length, max(b.brand_id) as brand_id, max(b.floor_id) as floor, max(b.room) as room, max(b.rack) as rack, max(b.self) as self, c.barcode_no, max(c.id) as roll_tbl_id, c.roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.amount, c.booking_without_order,c.id as roll_mst_id
		FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
		WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 $barcode_con
		group by  a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, c.roll_id, c.roll_no, c.barcode_no, c.po_breakdown_id, c.qnty,c.amount, c.booking_without_order,c.id";
		$data_array=sql_select($sql_data);

		$barcode_wise_data=array();
		$program_with_order_arr=array();

		if(count($data_array)>0)
		{
			foreach($data_array as $val)
			{
				$splitted_barcode_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
				$barcode_wise_data[$val[csf("barcode_no")]]['color_id']=$val[csf('color_id')];
				$barcode_wise_data[$val[csf("barcode_no")]]['booking_no']=$val[csf('booking_no')];
				$barcode_wise_data[$val[csf("barcode_no")]]['booking_without_order']=$val[csf('booking_without_order')];
				$barcode_wise_data[$val[csf("barcode_no")]]['po_breakdown_id']=$val[csf('po_breakdown_id')];
				

				if($val[csf("booking_without_order")] == 1 )
				{
					$non_order_booking_buyer_po_arr[$val[csf("po_breakdown_id")]] = $val[csf("po_breakdown_id")];
				}else{
					$po_arr_book_booking_arr[$val[csf("po_breakdown_id")]] = $val[csf("po_breakdown_id")];
				}

				/*if($val[csf("receive_basis")] == 2){
					$program_id_arr[$val[csf("booking_id")]] = $val[csf("booking_id")];
				}*/
			}

			$splited_barcode = implode(',',array_filter($splitted_barcode_arr));
			$nxProcessedBarcode = array();
			if($splited_barcode)
			{
				$splited_barcode_cond=where_con_using_array($splitted_barcode_arr,0,"a.barcode_no");
				$nxtProcessSql = sql_select("select a.id,a.barcode_no,a.roll_no from  pro_roll_details a where  and a.entry_form in (61) and a.status_active=1 and a.is_deleted=0 and a.is_returned!=1 $splited_barcode_cond ");
				foreach ($nxtProcessSql as $val2)
				{
					$nxProcessedBarcode[$val2[csf("barcode_no")]] = $val2[csf("barcode_no")];
				}
				unset($nxtProcessSql);
				//print_r($nxProcessedBarcode);

				$splited_barcode_cond=str_replace("a.barcode_no", "barcode_no",$splited_barcode_cond);
				$splited_roll_sql=sql_select("select barcode_no,split_from_id from pro_roll_split where status_active =1 $splited_barcode_cond ");

				foreach($splited_roll_sql as $bar)
				{
					$splited_roll_ref[$bar[csf('barcode_no')]][$bar[csf('split_from_id')]]=$bar[csf('barcode_no')];
				}
				unset($splited_roll_sql);

				$child_split_sql=sql_select("select barcode_no, id from pro_roll_details where roll_split_from >0 $splited_barcode_cond and entry_form = 82 order by barcode_no");
				foreach($child_split_sql as $bar)
				{
					$splited_roll_ref[$bar[csf('barcode_no')]][$bar[csf('id')]]=$bar[csf('barcode_no')];
				}
				unset($child_split_sql);

				//print_r($splited_roll_ref);die;

			}
			$barcode_con=str_replace("c.barcode_no", "a.barcode_no", $barcode_con);

			$production_basis_sql = sql_select("select a.barcode_no, b.receive_basis, a.booking_without_order, b.booking_id  from pro_roll_details a, inv_receive_master b where a.mst_id = b.id and b.entry_form = 2 and a.entry_form = 2 and a.status_active =1 $barcode_con ");
			foreach ($production_basis_sql as $val)
			{
				$production_basis_arr[$val[csf("barcode_no")]]["receive_basis"] = $val[csf("receive_basis")];
				$production_basis_arr[$val[csf("barcode_no")]]["booking_without_order"] = $val[csf("booking_without_order")];
				$production_basis_arr[$val[csf("barcode_no")]]["booking_id"] = $val[csf("booking_id")];

				if($val[csf("receive_basis")] ==2 && $val[csf("booking_without_order")] ==0)
				{
					$program_with_order_arr[$val[csf("booking_id")]] = $val[csf("booking_id")];
				}

			}
			unset($production_basis_sql);
		}

		unset($data_array);

		$po_arr_book_booking_arr = array_filter($po_arr_book_booking_arr);
		

		$po_booking_cond=where_con_using_array($po_arr_book_booking_arr,0,"b.id");

		$po_data_sql=sql_select("SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id,c.booking_no,c.fabric_color_id
		FROM wo_po_details_master a, wo_po_break_down b ,wo_booking_dtls c  WHERE a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.booking_type in (1,4) $po_booking_cond ");

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
		}
		unset($po_data_sql);
	}
	else
	{
		$sql="SELECT a.transfer_system_id,a.id,a.company_id,a.location_id,a.transfer_criteria,a.transfer_date,a.to_company,b.color_id,b.dia_width,b.dia_width_type,b.body_part_id,b.feb_description_id,b.gsm,b.stitch_length,b.from_store,b.to_store,b.to_prod_id,d.barcode_no,d.booking_no,d.qnty,a.remarks , b.yarn_lot, b.y_count,d.po_breakdown_id,b.from_order_id,d.dtls_id 
		from inv_item_transfer_mst a,inv_item_transfer_dtls b  , pro_roll_details d 
		where a.id=b.mst_id and  a.id=d.mst_id and b.id=d.dtls_id and a.status_active=1 and b.status_active=1  and d.status_active=1  and a.company_id=$cbo_company_id $transfer_date $transfer_criteria  $location_cond $to_store_cond $from_store_cond order by a.transfer_system_id,d.id";
		//echo $sql;
		$result=sql_select($sql);
		$mst_ids='';
		$bar_code=array();
		$sys_id='';

		$count_barcode=0;
		foreach ($result as $row) 
		{
			$bar_code[] = "'".$row[csf('barcode_no')]."'";
			if($row[csf('from_booking_without_order')] == 1)
			{
				$non_order_booking_buyer_po_arr[$row[csf('from_order_id')]] = $row[csf('from_order_id')];
			}
			else
			{
				$po_arr_book_booking_arr[$row[csf('from_order_id')]] = $row[csf('from_order_id')];
			}

			$po_arr_book_booking_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
			array_push($dtls_id_arr, $row[csf('dtls_id')]);
		}
		

		
		$barcode_con=where_con_using_array($bar_code,0,"c.barcode_no");
		

		$sql_data="SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, max(b.id) as dtls_id, max(b.prod_id) as prod_id, max(b.body_part_id) as body_part_id, max(b.febric_description_id) as febric_description_id, max(b.machine_no_id) as machine_no_id, max(b.gsm) as gsm, max(b.width) as width, max(b.color_id) as color_id, max(b.yarn_lot) as yarn_lot, max(b.yarn_count) as yarn_count, max(b.stitch_length) as stitch_length, max(b.brand_id) as brand_id, max(b.floor_id) as floor, max(b.room) as room, max(b.rack) as rack, max(b.self) as self, c.barcode_no, max(c.id) as roll_tbl_id, c.roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.amount, c.booking_without_order,c.id as roll_mst_id
		FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
		WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 $barcode_con
		group by  a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, c.roll_id, c.roll_no, c.barcode_no, c.po_breakdown_id, c.qnty,c.amount, c.booking_without_order,c.id";
		$data_array=sql_select($sql_data);

		$barcode_wise_data=array();
		$program_with_order_arr=array();

		if(count($data_array)>0)
		{
			foreach($data_array as $val)
			{
				$splitted_barcode_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
				$barcode_wise_data[$val[csf("barcode_no")]]['color_id']=$val[csf('color_id')];
				$barcode_wise_data[$val[csf("barcode_no")]]['booking_no']=$val[csf('booking_no')];
				$barcode_wise_data[$val[csf("barcode_no")]]['booking_without_order']=$val[csf('booking_without_order')];
				$barcode_wise_data[$val[csf("barcode_no")]]['po_breakdown_id']=$val[csf('po_breakdown_id')];
				

				if($val[csf("booking_without_order")] == 1 )
				{
					$non_order_booking_buyer_po_arr[$val[csf("po_breakdown_id")]] = $val[csf("po_breakdown_id")];
				}else{
					$po_arr_book_booking_arr[$val[csf("po_breakdown_id")]] = $val[csf("po_breakdown_id")];
				}

				/*if($val[csf("receive_basis")] == 2){
					$program_id_arr[$val[csf("booking_id")]] = $val[csf("booking_id")];
				}*/
			}

			$splited_barcode = implode(',',array_filter($splitted_barcode_arr));
			$nxProcessedBarcode = array();
			if($splited_barcode)
			{
				$splited_barcode_cond=where_con_using_array($splitted_barcode_arr,0,"a.barcode_no");
				$nxtProcessSql = sql_select("select a.id,a.barcode_no,a.roll_no from  pro_roll_details a where  and a.entry_form in (61) and a.status_active=1 and a.is_deleted=0 and a.is_returned!=1 $splited_barcode_cond ");
				foreach ($nxtProcessSql as $val2)
				{
					$nxProcessedBarcode[$val2[csf("barcode_no")]] = $val2[csf("barcode_no")];
				}
				unset($nxtProcessSql);
				//print_r($nxProcessedBarcode);

				$splited_barcode_cond=str_replace("a.barcode_no", "barcode_no",$splited_barcode_cond);
				$splited_roll_sql=sql_select("select barcode_no,split_from_id from pro_roll_split where status_active =1 $splited_barcode_cond ");

				foreach($splited_roll_sql as $bar)
				{
					$splited_roll_ref[$bar[csf('barcode_no')]][$bar[csf('split_from_id')]]=$bar[csf('barcode_no')];
				}

				unset($splited_roll_sql);

				$child_split_sql=sql_select("select barcode_no, id from pro_roll_details where roll_split_from >0 $splited_barcode_cond and entry_form = 82 order by barcode_no");
				foreach($child_split_sql as $bar)
				{
					$splited_roll_ref[$bar[csf('barcode_no')]][$bar[csf('id')]]=$bar[csf('barcode_no')];
				}

				unset($child_split_sql);

				//print_r($splited_roll_ref);die;

			}
			$barcode_con=str_replace("c.barcode_no", "a.barcode_no", $barcode_con);

			$production_basis_sql = sql_select("select a.barcode_no, b.receive_basis, a.booking_without_order, b.booking_id  from pro_roll_details a, inv_receive_master b where a.mst_id = b.id and b.entry_form = 2 and a.entry_form = 2 and a.status_active =1 $barcode_con ");
			foreach ($production_basis_sql as $val)
			{
				$production_basis_arr[$val[csf("barcode_no")]]["receive_basis"] = $val[csf("receive_basis")];
				$production_basis_arr[$val[csf("barcode_no")]]["booking_without_order"] = $val[csf("booking_without_order")];
				$production_basis_arr[$val[csf("barcode_no")]]["booking_id"] = $val[csf("booking_id")];

				if($val[csf("receive_basis")] ==2 && $val[csf("booking_without_order")] ==0)
				{
					$program_with_order_arr[$val[csf("booking_id")]] = $val[csf("booking_id")];
				}

			}
			unset($production_basis_sql);
		}

		unset($data_array);

		$non_order_booking_buyer_po_arr = array_filter($non_order_booking_buyer_po_arr);
		//$book_buyer_arr=return_library_array("select id, buyer_id from wo_non_ord_samp_booking_mst where is_deleted=0 and status_active=1 and id in (".implode(',', $non_order_booking_buyer_po_arr).")",'id','buyer_id');

		if(count($non_order_booking_buyer_po_arr)>0)
		{
			$non_order_id_cond=where_con_using_array($non_order_booking_buyer_po_arr,0,"id");
			$non_order_sql = sql_select("select id, buyer_id, booking_no from wo_non_ord_samp_booking_mst where is_deleted=0 and status_active=1 $non_order_id_cond ");
			foreach ($non_order_sql as  $val)
			{
				$book_buyer_arr[$val[csf("id")]] = $val[csf("buyer_id")];
				$non_booking_arr[$val[csf("id")]] = $val[csf("booking_no")];
			}
			unset($non_order_sql);
		}

		$po_arr_book_booking_arr = array_filter($po_arr_book_booking_arr);
		if(count($po_arr_book_booking_arr)>0)
		{
			$po_booking_cond=where_con_using_array($po_arr_book_booking_arr,0,"a.po_break_down_id");
			if(count($program_with_order_arr))
			{
				$program_with_order_cond=where_con_using_array($program_with_order_arr,0,"c.id");
				$book_booking_sql=sql_select("SELECT a.po_break_down_id, a.booking_no , c.id as plan_id from wo_booking_dtls a left join ppl_planning_info_entry_mst b on a.booking_no = b.booking_no left join  ppl_planning_info_entry_dtls c on b.id = c.mst_id $program_with_order_cond where  a.is_deleted=0 and a.status_active=1 and a.booking_type in (1,4) $po_booking_cond group by a.po_break_down_id, a.booking_no ,c.id");

				foreach ($book_booking_sql as $val)
				{
					$book_booking_arr_plan_wise[$val[csf("po_break_down_id")]][$val[csf("plan_id")]] = $val[csf("booking_no")];
				}
				unset($book_booking_sql);
			}
			else
			{
				$po_booking_cond=str_replace("a.po_break_down_id", "po_break_down_id", $po_booking_cond);
				$book_booking_arr=return_library_array("SELECT po_break_down_id, booking_no from wo_booking_dtls where is_deleted=0 and status_active=1 and booking_type=1 $po_booking_cond ",'po_break_down_id','booking_no');
			}
		}

		$po_booking_cond=where_con_using_array($po_arr_book_booking_arr,0,"b.id");

		$po_data_sql=sql_select("SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id,c.booking_no,c.fabric_color_id
	 		FROM wo_po_details_master a, wo_po_break_down b ,wo_booking_dtls c  WHERE a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.booking_type in (1,4) $po_booking_cond ");

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
		}
		unset($po_data_sql);
	}

	if($type==1) // Show
	{
		$po_booking_cond=where_con_using_array($po_arr_book_booking_arr,0,"b.id");
		$po_sql=sql_select("SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id,c.booking_no,c.fabric_color_id
	 		FROM wo_po_details_master a, wo_po_break_down b ,wo_booking_dtls c  WHERE a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.booking_type in (1,4) $po_booking_cond ");
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
	}
	

	$gauge_con=where_con_using_array($dtls_id_arr,0,"id");
	$gause_arr = return_library_array("select id, machine_gg from pro_grey_prod_entry_dtls where status_active =1 and is_deleted=0 $gauge_con","id","machine_gg");
	unset($gauge_con);
	
	$contents="";
	if($type==1) // Show
	{
		//echo $sql_data;
		//$sql_roll="inv_transaction c , pro_roll_details d and a.id=c.mst_id and a.id=d.mst_id and b.id=d.dtls_id"
		
		$table_width="2670"; $colspan="26";
		ob_start();
		?>
	    <fieldset style="width:100%">	
	        <table cellpadding="0" cellspacing="0" width="<?=$table_width; ?>" border="0" rules="all">
	            <tr class="form_caption">
	               <td align="center" width="100%" colspan="<?=$colspan; ?>" style="font-size:16px"><strong><?=$company_arr[$cbo_company_id]; ?></strong></td>
	            </tr>
	            <tr class="form_caption">
	               <td align="center" width="100%" colspan="<?=$colspan; ?>" style="font-size:16px"><strong><?=$report_title; ?></strong></td>
	            </tr>
	        </table>
	       	
	        <table class="rpt_table" border="1" rules="all" width="<?=$table_width; ?>" cellpadding="0" cellspacing="0" >
	            <thead>
	                <tr>
	                	<th width="40">SL</th>
	                    <th width="130">Company / Location</th>
	                    <th width="70">Transfer Date</th>
	                    <th width="120">System Challan</th>
	                    <th width="120">Tranfer Criteria</th>
	                    <th width="100">From Job</th>
	                    <th width="100">From Order</th>
	                    <th width="120">From Booking</th>
	                    <th width="100">To Job</th>
	                    <th width="100">To Order</th>
	                    <th width="100">To Booking</th>
	                    <th width="110">Buyer</th>
	                    <th width="110">From Color</th>
	                    <th width="110">To Color</th>
	                    <th width="60">Count</th>
	                    <th width="180">Composition</th>
	                    <th width="120">Fab Type</th>
	                    <th width="100">Lot No.</th>
	                    <th width="80">Guage</th>
	                    <th width="70">Stitch Lenth</th>
	                    <th width="50">Grey Dia</th>
	                    <th width="50">Fin Dia</th>
	                    <th width="70">Quantity</th>
	                    <th width="110">From Store</th>
	                    <th width="110">To Store</th>
	                    <th>Remaks </th>
	                </tr>
	           	</thead>
	        </table>

           	<table class="rpt_table" border="1" rules="all" width="<?=$table_width; ?>" cellpadding="0" cellspacing="0" id="scanning_table">	            
	           	<tbody >
	           		<?
	           		$contents = ob_get_flush();
					//ob_end_clean();
	           		?>
	           		<tr>
	                	<td width="40"></td>
	                    <td width="130"></td>
	                    <td width="70"></td>
	                    <td width="120"></td>
	                    <td width="120"></td>
	                    <td width="100"></td>
	                    <td width="100"></td>
	                    <td width="120"></td>
	                    <td width="100"></td>
	                    <td width="100"></td>
	                    <td width="100"></td>
	                    <td width="110"></td>
	                    <td width="110"></td>
	                    <td width="110" ></td>
	                    <td width="60"></td>
	                    <td width="180"></td>
	                    <td width="120"></td>
	                    <td width="100"></td>
	                    <td width="80"></td>
	                    <td width="70"></td>
	                    <td width="50"></td>
	                    <td width="50"></td>
	                    <td width="70"></td>
	                    <td width="110"></td>
	                    <td width="110"></td>
	                    <td> </td>
	                </tr>

					<?php 
					ob_start();

					$i=1; $j=1; $total=0;					
					foreach ($result as $row) 
					{
						$compsition_description=$composition_arr[$row[csf("feb_description_id")]];
						$fabric_type=$constructtion_arr[$row[csf("feb_description_id")]];

						$from_order_id =  $row[csf('from_order_id')];
						$from_booking_without_order = $row[csf('from_booking_without_order')];
						$color='';
						$color_id=explode(",",$barcode_wise_data[$row[csf("barcode_no")]]['color_id']);
						$po_breakdown_id=$barcode_wise_data[$row[csf("barcode_no")]]['po_breakdown_id'];
						foreach($color_id as $val)
						{
							if($val>0) $color.=$color_arr[$val].",";
						}
						$color=chop($color,',');

						$buyer_id='';

						if($from_booking_without_order == 1)
						{
							$buyer_name=$buyer_arr[$book_buyer_arr[$from_order_id]];
							$buyer_id=$book_buyer_arr[$from_order_id];
						}
						else
						{
							$buyer_name=$buyer_arr[$po_details_array[$from_order_id]['buyer_name']];
							$buyer_id=$po_details_array[$from_order_id]['buyer_name'];
						}

						$buyer_flag=1;

						if(!empty($cbo_buyer_id))
						{
							if($buyer_id==$cbo_buyer_id)
							{
								$buyer_flag=1;
							}
							else{
								$buyer_flag=0;
							}
						}

						if($barcode_wise_data[$row[csf("barcode_no")]]['booking_without_order']==1)
						{
							$booking_no_fab=$non_booking_arr[$po_breakdown_id];
						}
						else
						{
							if($production_basis_arr[$row[csf('barcode_no')]]["receive_basis"] == 2 && $production_basis_arr[$row[csf('barcode_no')]]["booking_without_order"] == 0 )
							{
								$plan_id = $production_basis_arr[$row[csf('barcode_no')]]["booking_id"];
								$booking_no_fab = $book_booking_arr_plan_wise[$po_breakdown_id][$plan_id];
							}
							else
							{
								$booking_no_fab=$book_booking_arr[$po_breakdown_id];
							}
						}
						$from=1;
						$to=1;
						if($from_booking_flag==1)
						{
							$from=substr_count(strtolower(trim($booking_no_fab)),strtolower(trim($cbo_from_booking)));
						}
						if($to_booking_flag==1)
						{
							$to=substr_count(strtolower(trim($po_details_array[$row[csf('po_breakdown_id')]]['booking_no'])),strtolower(trim($cbo_to_booking)));
						}
						
						if($to>0 && $from>0 && $buyer_flag==1)
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>" >
								<td  width="40"><?=$i; ?></td>
								<td width="130"><p><?=$company_arr[$row[csf('company_id')]]; ?></p></td>
								<td  width="70"><p><?=change_date_format($row[csf('transfer_date')]); ?>&nbsp;</p></td>
								<td width="120"><p><?=$row[csf('transfer_system_id')]; ?></p></td>
								<td width="120"><p><?=$item_transfer_criteria[$row[csf('transfer_criteria')]]; ?></p></td>

								<td width="100"><p><?=$po_details_array[$po_breakdown_id]['job_no']; ?></p></td>
								<td width="100" title="From Order ID: <? echo $po_breakdown_id;?>"><p><?=$po_details_array[$po_breakdown_id]['po_number']; ?></p></td>
								<td width="120"><p><?=$booking_no_fab ; ?></p></td>

								<td width="100"><p><?=$po_details_array[$row[csf("po_breakdown_id")]]['job_no']; ?></p></td>
								<td width="100" title="To Order ID: <? echo $row[csf('po_breakdown_id')];?>"><p><?=$po_details_array[$row[csf("po_breakdown_id")]]['po_number']; ?></p></td>
								<td width="100"><p><?=$po_details_array[$row[csf('po_breakdown_id')]]['booking_no']; ?></p></td>

								<td width="110"><p><?=$buyer_name; ?></p></td>
								<td width="110"><p><?=$color; ?></p></td>
								<td width="110"><p><?=$color/*$color_arr[$po_details_array[$row[csf('po_breakdown_id')]]['fabric_color_id']]*/; ?></p></td>
								<td width="60"><p><?=$yarn_count_arr[$row[csf('y_count')]]; ?></p></td>
								<td width="180"><p><?=$compsition_description; ?></p></td>
								<td width="120"><p><?=$fabric_type; ?></p></td>
								<td width="100"><p><?=$row[csf('yarn_lot')]; ?></p></td>
								<td width="80"><p><?=$gause_arr[$row[csf('dtls_id')]]; ?></p></td>
								<td width="70"><p><?=$row[csf('stitch_length')]; ?></p></td>
								<td width="50"><p><?=''; ?></p></td>
								<td width="50"><p><?=$row[csf('dia_width')]; ?></p></td>
								
								<td width="70" align="right"><p><?= number_format($row[csf('qnty')],2); ?></p></td>
								<td width="110"><p><?=$store_arr[$row[csf('from_store')]]; ?></p></td>
								<td width="110"><p><?=$store_arr[$row[csf('to_store')]]; ?></p></td>
								<td ><p><?=$row[csf('remarks')]; ?></p></td>
							</tr>
							<?	
							$total+=$row[csf('qnty')];
							$i++;
						}
					}
					?>				
				</tbody>				 
           	</table>

           	<table class="rpt_table" border="1" rules="all" width="<?=$table_width; ?>" cellpadding="0" cellspacing="0">
           		<?
           		$contents.= ob_get_flush();
				//ob_end_clean();
           		?>
           		<tbody >
	           		<tr>
	                	<td width="40"></td>
	                    <td width="130"></td>
	                    <td width="70"></td>
	                    <td width="120"></td>
	                    <td width="120"></td>
	                    <td width="100"></td>
	                    <td width="100"></td>
	                    <td width="120"></td>
	                    <td width="100"></td>
	                    <td width="100"></td>
	                    <td width="100"></td>
	                    <td width="110"></td>
	                    <td width="110"></td>
	                    <td width="110" ></td>
	                    <td width="60"></td>
	                    <td width="180"></td>
	                    <td width="120"></td>
	                    <td width="100"></td>
	                    <td width="80"></td>
	                    <td width="70"></td>
	                    <td width="50"></td>
	                    <td width="50"></td>
	                    <td width="70"></td>
	                    <td width="110"></td>
	                    <td width="110"></td>
	                    <td> </td>
	                </tr>
           		</tbody>
           		<?php
            	ob_start();
            	?>
	           	<tfoot>
				 	<tr>
				 		<td colspan="22" align="right" >Total </td>
				 		<td align="right" id="total_qnty" ><p><?php echo number_format($total,2); ?></p></td>
				 		<td ></td>
				 		<td ></td>
				 		<td ></td>
				 	</tr>
				</tfoot>
           </table>
	    </fieldset>
		<?
	}
	else if($type==2) // Summary
	{

		$buyer_wise_data=array();
		$booking_wise_data=array();
		foreach ($result as $row) 
		{

				
				

				$compsition_description=$composition_arr[$row[csf("feb_description_id")]];
				$fabric_type=$constructtion_arr[$row[csf("feb_description_id")]];

				$from_order_id =  $row[csf('from_order_id')];
				$from_booking_without_order = $row[csf('from_booking_without_order')];
				$color='';
				$color_id=explode(",",$barcode_wise_data[$row[csf("barcode_no")]]['color_id']);
				$po_breakdown_id=$barcode_wise_data[$row[csf("barcode_no")]]['po_breakdown_id'];
				foreach($color_id as $val)
				{
					if($val>0) $color.=$color_arr[$val].",";
				}
				$color=chop($color,',');

				$buyer_id='';

				if($from_booking_without_order == 1)
				{
					$buyer_name=$buyer_arr[$book_buyer_arr[$from_order_id]];
					$buyer_id=$book_buyer_arr[$from_order_id];
				}
				else
				{
					$buyer_name=$buyer_arr[$po_details_array[$from_order_id]['buyer_name']];
					$buyer_id=$po_details_array[$from_order_id]['buyer_name'];
				}

				$buyer_flag=1;

				if(!empty($cbo_buyer_id))
				{
					if($buyer_id==$cbo_buyer_id)
					{
						$buyer_flag=1;
					}
					else{
						$buyer_flag=0;
					}
				}

				if($barcode_wise_data[$row[csf("barcode_no")]]['booking_without_order']==1)
				{
					$booking_no_fab=$non_booking_arr[$po_breakdown_id];
				}
				else
				{
					if($production_basis_arr[$row[csf('barcode_no')]]["receive_basis"] == 2 && $production_basis_arr[$row[csf('barcode_no')]]["booking_without_order"] == 0 )
					{
						$plan_id = $production_basis_arr[$row[csf('barcode_no')]]["booking_id"];
						$booking_no_fab = $book_booking_arr_plan_wise[$po_breakdown_id][$plan_id];
					}
					else
					{
						$booking_no_fab=$book_booking_arr[$po_breakdown_id];
					}
				}
				$from=1;
				$to=1;
				if($from_booking_flag==1)
				{
					$from=substr_count(strtolower(trim($booking_no_fab)),strtolower(trim($cbo_from_booking)));
				}
				if($to_booking_flag==1)
				{
					$to=substr_count(strtolower(trim($po_details_array[$row[csf('po_breakdown_id')]]['booking_no'])),strtolower(trim($cbo_to_booking)));
				}
				
				if($to>0 && $from>0 && $buyer_flag==1)
				{


					

					$buyer_wise_data[$buyer_name]+=$row[csf('qnty')];
					if(substr_count(strtolower(trim($booking_no_fab)),strtolower("smn")))
					{

						$booking_wise_data['Sample']+=$row[csf('qnty')];
					}
					else
					{
						$booking_wise_data['Main']+=$row[csf('qnty')];

					}

				

			}	

		}

		//echo $sql_data;
		//$sql_roll="inv_transaction c , pro_roll_details d and a.id=c.mst_id and a.id=d.mst_id and b.id=d.dtls_id"
		
		$table_width="350"; $colspan="21";
		ob_start();
		?>
	    <fieldset style="width:800px;justify-content: center;text-align: center;">	
	        
	        <h3  style="font-size:16px;justify-content: center;text-align: center;"><strong><?=$company_arr[$cbo_company_id]; ?></strong></h3>
	         <h3  style="font-size:16px;justify-content: center;text-align: center;"><strong><?=$report_title; ?></strong></h3>
	       
	        <div style="width: 700px;float: left;justify-content: center;text-align: center;">
	       
	        <table class="rpt_table" border="1" rules="all" width="<?=$table_width; ?>" cellpadding="0" cellspacing="0" style="float: left;" >
	            <thead>
	                <tr>
	                	<th width="40">SL</th>
	                    
	                    <th width="110">Buyer</th>
	                   
	                    
	                    <th>Trans Qty </th>
	                </tr>

	            </thead> 
	           <tbody>
	            
				<?php 
					$i=1;
					$j=1;

					$total=0;
					
					foreach ($buyer_wise_data as $key => $value) 
					{

							
							




								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";


								?>
					

	                   
	                   
	                   
	                    
	                   
							<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>" >
								
								
								
								<td  width="40"><?=$i; ?></td>
								
								<td width="110"><p><?=$key; ?></p></td>
								
								<td align="right"><p><?= number_format($value,2); ?></p></td>
								
								
							</tr>
							
							<?	
								$total+=$value;
								$i++;

							

					}
				 ?>
				
				</tbody>
				 <tfoot >
				
				 	<tr>
				 		<td colspan="2" align="right">Total</td>
				 		<td align="right"><p><?php echo number_format($total,2); ?></p></td>
				 		
				 	</tr>
				 	
				 </tfoot>
	           </table>
	           <table class="rpt_table" border="1" rules="all" width="250" cellpadding="0" cellspacing="0" style="float: right;">
	           	<thead>
	                <tr>
	                	<th width="40">SL</th>
	                    
	                    <th width="110">Booking Type</th>
	                   
	                    
	                    <th >Trns. qty </th>
	                </tr>

	            </thead>
	              <tbody>
	            
				<?php 
					
					$j=1;

					$total=0;
					
					foreach ($booking_wise_data as $key => $value) 
					{

							
							




								if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";


								?>
					

	                   
	                   
	                   
	                    
	                   
							<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$j+$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$j+$i; ?>" >
								
								
								
								<td  width="40"><?=$j; ?></td>
								
								<td width="110"><p><?=$key; ?></p></td>
								
								<td align="right"><p><?= number_format($value,2); ?></p></td>
								
								
							</tr>
							
							<?	
								$total+=$value;
								$j++;

							

					}
				 ?>
				
				</tbody>
				 <tfoot >
				
				 	<tr>
				 		<td colspan="2" align="right">Total</td>
				 		<td align="right"><p><?php echo number_format($total,2); ?></p></td>
				 		
				 	</tr>
				 	
				 </tfoot>

	           </table>
	       </div>
	        
	        
	    </fieldset>
	    <script type="text/javascript">
	    	setFilterGrid('scanning_table',-1);
	    </script>

		<?
	}
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	//$is_created = fwrite($create_new_doc,ob_get_contents());
	$contents.= ob_get_contents();
	$is_created = fwrite($create_new_doc,$contents);
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}

if ($action == "work_order_print") 
{
	echo load_html_head_contents("Grey Fabric Booking To Booking Transfer Report", "../../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$data = explode("**", $data);
	
	$company_library = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[1]'","image_location");
	$supplier_arr = return_library_array("select id,supplier_name from lib_supplier", 'id', 'supplier_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

	$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0","id","color_name");

	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}

	$address="";
?>
	
		
        <table style="margin-top:10px;" width="1200" border="1" rules="all" cellpadding="3" cellspacing="0" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="80">Program Date</th>
                <th width="90">Program no </th>
                <th width="250">Fabric Description</th>
                <th width="100">M/C Dia x Gauge</th>
                <th width="100">S.L</th>
                <th width="150">Color Range</th>
                <th width="100">Program Qty.</th>
                <th width="80">WO Qty.</th>
                <th width="70">Rate</th>
                
                <th>Amount</th>
            </thead>
            <tbody>
            	<?php 

            		$sql="SELECT a.id,a.program_date,a.program_no,a.fabric_desc,a.machine_dia,a.machine_gg,a.stitch_length,a.color_range,a.program_qnty,a.wo_qty,a.rate,a.amount from knitting_work_order_dtls a where a.status_active=1 and a.is_deleted=0 and a.mst_id=$data[1] and a.id in (select b.wo_dtls_id from wo_bill_dtls b where b.status_active=1 and b.is_deleted=0)";
            		//echo $sql;
            		$result=sql_select($sql);
            		$i=1;
            		$program_qnty=0;
            		$wo_qty=0;
            		$amount=0;
            		foreach ($result as $row) 
            		{
            			$program_qnty+=$row[csf('program_qnty')];
            			$wo_qty+=$row[csf('wo_qty')];
            			$amount+=$row[csf('amount')];
            			
            			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

            		 ?>

            		 	<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>">
								<td><?=$i; ?></td>
								<td>
									<p><?=change_date_format($row[csf('program_date')]); ?></p>
								</td>
								<td><p><?php echo $row[csf('program_no')] ?></p></td>
								<td><p><?php echo $row[csf('fabric_desc')] ?></p></td>
								<td><p><?php echo $row[csf('machine_dia')]." x ".$row[csf('machine_gg')] ?></p></td>
								<td><p><?php echo $row[csf('stitch_length')] ?></p></td>
								<td><p><?php echo $color_range[$row[csf('color_range')]] ?></p></td>
								<td align="right"><p><?php echo number_format($row[csf('program_qnty')],2) ?></p></td>
								<td align="right"><p><?php echo number_format($row[csf('wo_qty')],2) ?></p></td>
								<td align="right"><p><?php echo number_format($row[csf('rate')],2) ?></p></td>
								<td align="right"><p><?php echo number_format($row[csf('amount')],2) ?></p></td>

            		 	</tr>
            			<?php 
            			$i++;
            		} 

            	?>

             </tbody>
             <tfoot>
				<tr>
					<td colspan="7" align="right">Total</td>
					<td align="right"><p><?php echo number_format($program_qnty,2) ?></p></td>
					<td align="right"><p><?php echo number_format($wo_qty,2) ?></p></td>
					<td></td>
					<td align="right"><p><?php echo number_format($amount,2) ?></p></td>
				</tr>
            </tfoot>
        </table>
		
    </div>
    <?
    exit();
}


?>