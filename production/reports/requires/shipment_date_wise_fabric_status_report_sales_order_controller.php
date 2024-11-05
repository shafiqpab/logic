<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

require_once('../../../includes/common.php');
$user_name = $_SESSION['logic_erp']['user_id'];

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

if ($action == "load_drop_down_buyer") {
	echo create_drop_down("cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name", "id,buyer_name", 1, "-- All Buyer --", $selected, "");
	exit();
}
//--------------------------------------------------------------------------------------------------------------------
function page_style()
{
	?>
	<style type="text/css">
	table tr th small {
		font-weight: normal !important;
	}

	table tr td, table tr th {
		text-align: right;
		padding: 0px 2px;
	}

	#summary tr td {
		text-align: left !important;
	}

	table tr td.right, #summary tr td.right {
		text-align: right !important;
	}

	table tr td.left {
		text-align: left !important;
	}

	table tr td.center {
		text-align: center !important;
	}

</style>
<?
}

if($action == "report_generate")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$company_name = str_replace("'", "", $cbo_company_name);
	$buyer_name = str_replace("'", "", trim($cbo_buyer_name));
	$sales_job_no = str_replace("'", "", trim($txt_sales_job_no));
	$hide_job_id = str_replace("'", "", $hide_job_id);
	$sales_booking_no = str_replace("'", "", $txt_booking_no);
	$hide_booking_id = str_replace("'", "", $hide_booking_id);
	$start_date = str_replace("'", "", trim($txt_date_from));
	$end_date = str_replace("'", "", trim($txt_date_to));
	$cbo_year_selection = str_replace("'", "", trim($cbo_year_selection));
	$cbo_within_group = str_replace("'", "", trim($cbo_within_group));

    

	if($db_type==0)
	{
		$year_cond=" and YEAR(a.insert_date)=$cbo_year_selection";
	}
	else if($db_type==2)
	{
		$year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year_selection";
	}
	else
	{
		$year_cond="";
	}

	if ($end_date == "") {
		$end_date = $start_date;
	} else {
		$end_date = $end_date;
	}

	if ($start_date != "" && $end_date != "") {
		if ($db_type == 0) {
			$str_cond_insert = " and b.pub_shipment_date between '" . $start_date . "' and '" . $end_date . "'";
		} else {
			$str_cond_insert = " and b.pub_shipment_date between '" . $start_date . "' and '" . $end_date . "'";
		}
	} else {
		$str_cond_insert = "";
	}

	if($sales_job_no != "")
	{
		if($hide_job_id == ""){
			$sales_order_cond = ($sales_job_no != "") ? " and a.job_no_prefix_num=$sales_job_no" : "";
		}else{
			$sales_order_cond = " and a.id in($hide_job_id)";
		}
	}

	/*if($hide_booking_id == ""){
		$sales_booking_cond = ($sales_booking_no != "") ? " and a.sales_booking_no like '%$sales_booking_no%'" : "";
	}else{
		$sales_booking_cond = " and a.sales_booking_no='$sales_booking_no'";
	}*/
	$str_arry = explode(",","$sales_booking_no");
    $sales_booking_no="";
    foreach ($str_arry as $key => $value) 
    {
        if ($sales_booking_no=="") 
        {
            $sales_booking_no.= $value;
        }
        else 
        {
            $sales_booking_no.= "','".$value;
        }
    }
    //echo $sales_booking_no;die;

	$sales_booking_cond="";
	if($sales_booking_no != "")
	{
		$sales_booking_cond = " and a.sales_booking_no in('$sales_booking_no')";
	}

	//$buyer_cond = ($buyer_name != 0) ? " and c.buyer_id=$buyer_name" : "";

	if($cbo_within_group != 0)
	{
		$within_group_cond = " and a.within_group = $cbo_within_group ";
	}


	if ($buyer_name)
	{

		if($cbo_within_group == 1)
		{
			$po_buyer_id_cond = " and a.po_buyer=$buyer_name";
		}
		else if($cbo_within_group == 2)
		{
			$po_buyer_id_cond = " and a.buyer_id =$buyer_name";
		}
		else
		{
			$po_buyer_id_cond = " and a.po_buyer=$buyer_name";
		} 
	}
	 
	$dataArraySalesOrder = array();
	$started = microtime(true);
	 
	$salesOrderDataSql = "SELECT  c.buyer_id as po_buyer, b.pub_shipment_date, a.booking_id,a.id,a.buyer_id sales_buyer,a.within_group,c.id booking_id,c.buyer_id,a.company_id,a.job_no sales_job_no, a.job_no_prefix_num,a.style_ref_no,a.delivery_date sales_order_dt,a.delivery_date,c.po_break_down_id,a.sales_booking_no booking_no, c.fabric_composition,c.is_short,c.fabric_source,c.job_no,c.is_approved,c.item_category,c.entry_form,c.booking_type,a.within_group from fabric_sales_order_mst a , wo_booking_mst c,wo_po_break_down b ,wo_booking_dtls d,wo_po_color_size_breakdown e   where  a.company_id = $company_name $str_cond_insert $sales_order_cond and a.booking_id = c.id and b.job_no_mst=c.job_no and d.po_break_down_id=b.id and c.booking_no=d.booking_no and d.po_break_down_id=e.po_break_down_id and e.status_active=1 and d.status_active=1 and b.status_active=1  and  a.is_deleted=0 and a.status_active=1      $sales_booking_cond $po_buyer_id_cond group by  c.buyer_id, b.pub_shipment_date,   a.booking_id,a.id,a.buyer_id ,a.within_group,c.id ,c.buyer_id,a.company_id,a.job_no , a.job_no_prefix_num,a.style_ref_no,a.delivery_date  ,a.delivery_date,c.po_break_down_id,a.sales_booking_no , c.fabric_composition,c.is_short,c.fabric_source,c.job_no,c.is_approved,c.item_category,c.entry_form,c.booking_type,a.within_group order by b.pub_shipment_date asc "; //$buyer_cond $year_cond

	// echo $salesOrderDataSql;

	$sales_order_ids = $job_no_arr = $booking_id_arr = array();
	$listagg_sql_po_item_arr_full = array();
	$salesOrderDataResult = array();
	$salesOrderSql = "SELECT a.booking_id, a.id, a.buyer_id as sales_buyer, a.within_group, a.company_id, a.job_no as sales_job_no, a.job_no_prefix_num, a.style_ref_no, a.delivery_date, a.sales_booking_no as booking_no, b.pub_shipment_date,b.id as po_id from fabric_sales_order_mst a, wo_po_break_down b where a.po_job_no  = b.job_no_mst and	a.company_id = $company_name  $po_buyer_id_cond $str_cond_insert $sales_order_cond and a.is_deleted = 0 and a.status_active = 1 $sales_booking_cond";
	// $salesOrderSql = "SELECT a.booking_id, a.id, a.buyer_id as sales_buyer, a.within_group, a.company_id, a.job_no as sales_job_no, a.job_no_prefix_num, a.style_ref_no, a.delivery_date, a.sales_booking_no as booking_no, b.pub_shipment_date,b.id as po_id
	// from fabric_sales_order_mst a
  	// left join wo_po_break_down b
	// on a.po_job_no  = b.job_no_mst
	// where a.company_id = $company_name  $po_buyer_id_cond $str_cond_insert $sales_order_cond and a.is_deleted = 0 and a.status_active = 1 $sales_booking_cond";

	//   echo $salesOrderSql;
	$salesOrderArr = sql_select($salesOrderSql);

	foreach ($salesOrderArr as $row) {
		$sales_order_ids[$row[csf("id")]] = $row[csf("id")];
		$booking_id_arr[$row[csf("booking_id")]] = $row[csf("booking_id")];
		$job_no_arr["'".$row[csf("job_no")]."'"] = "'".$row[csf("job_no")]."'";
	}
	$sales_ids = implode(",",array_filter($sales_order_ids));
	$booking_ids = implode(",",array_filter($booking_id_arr));

	$bookingSql = "SELECT c.buyer_id as po_buyer, b.pub_shipment_date, c.id as booking_id, c.buyer_id, d.po_break_down_id, c.fabric_composition, c.is_short, c.fabric_source, c.job_no, c.is_approved, c.item_category, c.entry_form, c.booking_type
	from wo_booking_mst c, wo_po_break_down b, wo_booking_dtls d, wo_po_color_size_breakdown e
	where b.id = e.po_break_down_id and d.po_break_down_id = b.id and c.booking_no = d.booking_no and d.po_break_down_id = e.po_break_down_id and e.status_active = 1 and d.status_active = 1 and b.status_active = 1 and c.id in ($booking_ids)
	group by c.buyer_id, b.pub_shipment_date, c.id, c.buyer_id, d.po_break_down_id, c.fabric_composition, c.is_short, c.fabric_source, c.job_no, c.is_approved, c.item_category, c.entry_form, c.booking_type";

	//  echo "</br>".$bookingSql;die;
	$bookingArr = sql_select($bookingSql);

	$bookingDataArr = array();
	$bookingPODataArr = array();
	foreach ($bookingArr as $row) {
		$booking_no_arr["'".$row[csf("booking_no")]."'"] = "'".$row[csf("booking_no")]."'";
		$listagg_sql_po_item_arr_full[$row[csf("booking_id")]]["po"].=$row[csf("po_break_down_id")].",";
		$bookingDataArr[$row[csf('booking_id')]]['id'] = $row[csf('booking_id')];
		$bookingDataArr[$row[csf('booking_id')]]['po_buyer'] = $row[csf('po_buyer')];
		$bookingDataArr[$row[csf('booking_id')]]['pub_shipment_date'] = $row[csf('pub_shipment_date')];
		$bookingDataArr[$row[csf('booking_id')]]['buyer_id'] = $row[csf('buyer_id')];
		$bookingDataArr[$row[csf('booking_id')]]['po_break_down_id'] = $row[csf('po_break_down_id')];
		$bookingPODataArr[$row[csf('booking_id')]][$row[csf('po_break_down_id')]]['po_break_down_id'] = $row[csf('po_break_down_id')];
		$bookingDataArr[$row[csf('booking_id')]]['fabric_composition'] = $row[csf('fabric_composition')];
		$bookingDataArr[$row[csf('booking_id')]]['is_short'] = $row[csf('is_short')];
		$bookingDataArr[$row[csf('booking_id')]]['fabric_source'] = $row[csf('fabric_source')];
		$bookingDataArr[$row[csf('booking_id')]]['job_no'] = $row[csf('job_no')];
		$bookingDataArr[$row[csf('booking_id')]]['is_approved'] = $row[csf('is_approved')];
		$bookingDataArr[$row[csf('booking_id')]]['item_category'] = $row[csf('item_category')];
		$bookingDataArr[$row[csf('booking_id')]]['entry_form'] = $row[csf('entry_form')];
		$bookingDataArr[$row[csf('booking_id')]]['booking_type'] = $row[csf('booking_type')];
	}

	foreach ($salesOrderArr as $index => $row) {
		$salesOrderDataResult[$index][csf('booking_id')] = $row[csf('booking_id')];
		$salesOrderDataResult[$index][csf('id')] = $row[csf('id')];
		$salesOrderDataResult[$index][csf('sales_buyer')] = $row[csf('sales_buyer')];
		$salesOrderDataResult[$index][csf('within_group')] = $row[csf('within_group')];
		$salesOrderDataResult[$index][csf('company_id')] = $row[csf('company_id')];
		$salesOrderDataResult[$index][csf('sales_job_no')] = $row[csf('sales_job_no')];
		$salesOrderDataResult[$index][csf('job_no_prefix_num')] = $row[csf('job_no_prefix_num')];
		$salesOrderDataResult[$index][csf('style_ref_no')] = $row[csf('style_ref_no')];
		$salesOrderDataResult[$index][csf('delivery_date')] = $row[csf('delivery_date')];
		$salesOrderDataResult[$index][csf('booking_no')] = $row[csf('booking_no')];
		$salesOrderDataResult[$index][csf('po_buyer')] = $bookingDataArr[$row[csf('booking_id')]]['po_buyer'];
		$salesOrderDataResult[$index][csf('pub_shipment_date')] = $row[csf('pub_shipment_date')];
		$salesOrderDataResult[$index][csf('po_id')] = $row[csf('po_id')];
		$salesOrderDataResult[$index][csf('buyer_id')] = $bookingDataArr[$row[csf('booking_id')]]['buyer_id'];
		$salesOrderDataResult[$index][csf('po_break_down_id')] = $bookingPODataArr[$row[csf('booking_id')]][$row[csf('po_id')]]['po_break_down_id'];
		$salesOrderDataResult[$index][csf('fabric_composition')] = $bookingDataArr[$row[csf('booking_id')]]['fabric_composition'];
		$salesOrderDataResult[$index][csf('is_short')] = $bookingDataArr[$row[csf('booking_id')]]['is_short'];
		$salesOrderDataResult[$index][csf('fabric_source')] = $bookingDataArr[$row[csf('booking_id')]]['fabric_source'];
		$salesOrderDataResult[$index][csf('job_no')] = $bookingDataArr[$row[csf('booking_id')]]['job_no'];
		$salesOrderDataResult[$index][csf('is_approved')] = $bookingDataArr[$row[csf('booking_id')]]['is_approved'];
		$salesOrderDataResult[$index][csf('item_category')] = $bookingDataArr[$row[csf('booking_id')]]['item_category'];
		$salesOrderDataResult[$index][csf('entry_form')] = $bookingDataArr[$row[csf('booking_id')]]['entry_form'];
		$salesOrderDataResult[$index][csf('booking_type')] = $bookingDataArr[$row[csf('booking_id')]]['booking_type'];
	}

	// echo $bookingSql;
	// $salesOrderDataResult = sql_select($salesOrderDataSql);
	
	if (!empty($salesOrderDataResult))
	{
		foreach ($salesOrderDataResult as $row) {
			$sales_order_ids[$row[csf("id")]] = $row[csf("id")];
			$job_no_arr["'".$row[csf("job_no")]."'"] = "'".$row[csf("job_no")]."'";
			$booking_no_arr["'".$row[csf("booking_no")]."'"] = "'".$row[csf("booking_no")]."'";
			$listagg_sql_po_item_arr_full[$row[csf("id")]]["po"].=$row[csf("po_break_down_id")].",";
		}
		$sales_ids = implode(",",array_filter($sales_order_ids));
		// $sales_ids = "1479,1481,1491,1501";
		$job_nos = implode(",",$job_no_arr);
		$booking_no_arr = array_filter($booking_no_arr);
		$booking_nos = implode(",",$booking_no_arr);
		$sales_id_cond="";
		//if($sales_job_no)
		$sales_id_cond="and a.id in($sales_ids)";
		$listagg_sql_po_item = "SELECT b.pub_shipment_date,a.id,b.id as po_ids,e.item_number_id ,b.po_number from fabric_sales_order_mst a ,wo_booking_mst c,wo_po_break_down b , wo_booking_dtls d,wo_po_color_size_breakdown e   where a.company_id = $company_name  $sales_order_cond $str_cond_insert and  a.booking_id = c.id and d.po_break_down_id=b.id and c.booking_no=d.booking_no and d.po_break_down_id=e.po_break_down_id and e.status_active=1 and d.status_active=1 and b.status_active=1  and  a.is_deleted=0 and a.status_active=1 $sales_booking_cond $sales_id_cond group by  b.pub_shipment_date,a.id,b.id ,e.item_number_id ,b.po_number "; //$buyer_cond $year_cond  
		// $listagg_sql_po_item = "SELECT b.pub_shipment_date,a.id,b.id as po_ids,e.item_number_id ,b.po_number from fabric_sales_order_mst a ,wo_booking_mst c,wo_po_break_down b , wo_booking_dtls d,wo_po_color_size_breakdown e   where a.company_id = $company_name  $sales_order_cond $str_cond_insert and  a.booking_id = c.id and d.po_break_down_id=b.id and c.booking_no=d.booking_no and d.po_break_down_id=e.po_break_down_id and e.status_active=1 and d.status_active=1 and b.status_active=1  and  a.is_deleted=0 and a.status_active=1 $sales_booking_cond $sales_id_cond group by  b.pub_shipment_date,a.id,b.id ,e.item_number_id ,b.po_number "; //$buyer_cond $year_cond  

	// echo $listagg_sql_po_item;
		$listagg_arr_po_item = sql_select($listagg_sql_po_item);
	   foreach($listagg_arr_po_item as $v)
	   {
	   		$sales=$v[csf("id")];
	   		$pub_shipment_date=$v[csf("pub_shipment_date")];
	   		$item_number_id=$garments_item[$v[csf("item_number_id")]];
	   		$po_number=$v[csf("po_number")];
	   		$po_ids=$v[csf("po_ids")];
	   		if($listagg_sql_po_item_arr[$sales][$pub_shipment_date]["po"])
	   		$listagg_sql_po_item_arr[$sales][$pub_shipment_date]["po"].=','.$po_number;
	   		else 
	   		$listagg_sql_po_item_arr[$sales][$pub_shipment_date]["po"].=$po_number;

	   		if(isset($listagg_sql_po_item_arr[$sales][$pub_shipment_date]["po_ids"]))
	   		$listagg_sql_po_item_arr[$sales][$pub_shipment_date]["po_ids"].=','.$po_ids;
	   		else 
	   		$listagg_sql_po_item_arr[$sales][$pub_shipment_date]["po_ids"].=$po_ids;

	   		if(!$listagg_sql_po_item_arr_chk[$sales][$pub_shipment_date][$v[csf("item_number_id")]]["chk"])
	   		{
	   			if($listagg_sql_po_item_arr[$sales][$pub_shipment_date]["item"])
		   		$listagg_sql_po_item_arr[$sales][$pub_shipment_date]["item"].=','.$item_number_id;
		   		else 
		   		$listagg_sql_po_item_arr[$sales][$pub_shipment_date]["item"].=$item_number_id;
		   		$listagg_sql_po_item_arr_chk[$sales][$pub_shipment_date][$v[csf("item_number_id")]]["chk"]=420;
	   		}

		   	$sales_order_of_po_arr[$po_ids] = $sales;

	   } 
	   // echo "<pre>"; print_r($listagg_sql_po_item_arr);die;
		$sql_deter="SELECT a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
		$deter_array=sql_select($sql_deter);

		if(count($deter_array)>0)
		{
			foreach( $deter_array as $row )
			{
				if(array_key_exists($row[csf('id')],$composition_arr))
				{
					$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
					$constructionArr[$row[csf('id')]]=$constructionArr[$row[csf('id')]];
					list($cst,$cps)=explode(',',$composition_arr[$row[csf('id')]]);
					$copmpositionArr[$row[csf('id')]]=$cps;
				}
				else
				{
					$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
					$constructionArr[$row[csf('id')]]=$row[csf('construction')];
					list($cst,$cps)=explode(',',$composition_arr[$row[csf('id')]]);
					$copmpositionArr[$row[csf('id')]]=$cps;
				}
			}
		}
		unset($deter_array);



		if(!empty($booking_no_arr))
		{
			$style_owner_info_booking_Cond=""; $soibcCond="";
			if($db_type==2 && count($booking_no_arr)>999)
			{
				$booking_no_arr_chunk=array_chunk($booking_no_arr,999) ;
				foreach($booking_no_arr_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$soibcCond.="  b.booking_no in($chunk_arr_value) or ";
				}

				$style_owner_info_booking_Cond.=" and (".chop($soibcCond,'or ').")";
			}
			else
			{
				$style_owner_info_booking_Cond=" and b.booking_no in ($booking_nos)";
			}


			// STYLE OWNER INFO START
			$style_owner_info = sql_select("SELECT a.job_no, a.style_owner,b.booking_no,a.gmts_item_id from wo_po_details_master a,wo_booking_dtls b where a.job_no=b.job_no and a.status_active = 1 and a.is_deleted = 0 and a.style_owner != 0 and b.status_active=1 $style_owner_info_booking_Cond group by a.job_no, a.style_owner,b.booking_no,a.gmts_item_id");
			foreach ($style_owner_info as $row) {
				$style_owner_arr[$row[csf('booking_no')]]["style_owner"] = $row[csf('style_owner')];
				$style_owner_arr[$row[csf('booking_no')]]["job_no"] = $row[csf('job_no')];
				$job_gmts_item_arr[$row[csf('job_no')]]["gmts_item_id"] = $row[csf('gmts_item_id')];
			}
			// STYLE OWNER INFO END
		}


		if($sales_ids != "")
		{
			$sales_ids_arr = explode(",", $sales_ids);
			$grey_fabric_status_fso_Cond=""; $fsoCond_1="";
			$transfer_in_fso_Cond=""; $fsoCond_2="";
			$grey_delivery_fso_Cond=""; $fsoCond_3="";
			$yarn_sql_fso_Cond=""; $fsoCond_4="";
			$yarn_qty_requisition_fso_Cond=""; $fsoCond_5="";
			$yarn_iss_return_fso_Cond=""; $fsoCond_6="";
			$yarn_iss_fso_Cond=""; $fsoCond_7="";

			$trans_grey_issue_return_fso_Cond=""; $fsoCond_8="";
			$receive_by_batch_fso_Cond=""; $fsoCond_9="";
			$batch_fso_Cond=""; $fsoCond_10="";
			$dye_fso_Cond=""; $fsoCond_11="";
			$sales_fin_qnty_fso_Cond=""; $fsoCond_12="";
			$finish_production_fso_Cond=""; $fsoCond_13="";
			$fin_rcv_trans_iss_fso_Cond=""; $fsoCond_14="";

			if($db_type==2 && count($sales_ids_arr)>999)
			{
				$sales_ids_arr_chunk=array_chunk($sales_ids_arr,999) ;
				foreach($sales_ids_arr_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$fsoCond_1.=" a.mst_id in($chunk_arr_value) or ";
					$fsoCond_2.=" a.to_order_id in($chunk_arr_value) or ";
					$fsoCond_3.=" a.order_id in($chunk_arr_value) or ";
					$fsoCond_4.=" b.mst_id in($chunk_arr_value) or ";
					$fsoCond_5.=" a.po_id in($chunk_arr_value) or ";
					$fsoCond_6.=" e.id in($chunk_arr_value) or ";
					$fsoCond_7.=" a.po_id in($chunk_arr_value) or ";

					$fsoCond_8.=" a.po_breakdown_id in($chunk_arr_value) or ";
					$fsoCond_9.=" c.po_breakdown_id in($chunk_arr_value) or ";
					$fsoCond_10.=" a.sales_order_id in($chunk_arr_value) or ";
					$fsoCond_11.=" b.po_id in($chunk_arr_value) or ";
					$fsoCond_12.=" mst_id in($chunk_arr_value) or ";
					$fsoCond_13.=" c.po_breakdown_id in($chunk_arr_value) or ";
					$fsoCond_14.=" po_breakdown_id in($chunk_arr_value) or ";
				}

				$grey_fabric_status_fso_Cond.=" and (".chop($fsoCond_1,'or ').")";
				$transfer_in_fso_Cond.=" and (".chop($fsoCond_2,'or ').")";
				$grey_delivery_fso_Cond.=" and (".chop($fsoCond_3,'or ').")";
				$yarn_sql_fso_Cond.=" and (".chop($fsoCond_4,'or ').")";
				$yarn_qty_requisition_fso_Cond.=" and (".chop($fsoCond_5,'or ').")";
				$yarn_iss_return_fso_Cond.=" and (".chop($fsoCond_6,'or ').")";
				$yarn_iss_fso_Cond.=" and (".chop($fsoCond_7,'or ').")";

				$trans_grey_issue_return_fso_Cond.=" and (".chop($fsoCond_8,'or ').")";
				$receive_by_batch_fso_Cond.=" and (".chop($fsoCond_9,'or ').")";
				$batch_fso_Cond.=" and (".chop($fsoCond_10,'or ').")";
				$dye_fso_Cond.=" and (".chop($fsoCond_11,'or ').")";
				$sales_fin_qnty_fso_Cond.=" and (".chop($fsoCond_12,'or ').")";
				$finish_production_fso_Cond.=" and (".chop($fsoCond_13,'or ').")";
				$fin_rcv_trans_iss_fso_Cond.=" and (".chop($fsoCond_14,'or ').")";
			}
			else
			{
				$grey_fabric_status_fso_Cond=" and a.mst_id in ($sales_ids)";
				$transfer_in_fso_Cond=" and a.to_order_id in ($sales_ids)";
				$grey_delivery_fso_Cond=" and a.order_id in ($sales_ids)";
				$yarn_sql_fso_Cond=" and b.mst_id in ($sales_ids)";
				$yarn_qty_requisition_fso_Cond=" and a.po_id in ($sales_ids)";
				$yarn_iss_return_fso_Cond=" and e.id in ($sales_ids)";
				$yarn_iss_fso_Cond=" and a.po_id in ($sales_ids)";

				$trans_grey_issue_return_fso_Cond=" and a.po_breakdown_id in ($sales_ids)";
				$receive_by_batch_fso_Cond=" and c.po_breakdown_id in ($sales_ids)";
				$batch_fso_Cond=" and a.sales_order_id in ($sales_ids)";
				$dye_fso_Cond=" and b.po_id in ($sales_ids)";
				$sales_fin_qnty_fso_Cond=" and mst_id in ($sales_ids)";
				$finish_production_fso_Cond=" and c.po_breakdown_id in ($sales_ids)";
				$fin_rcv_trans_iss_fso_Cond=" and po_breakdown_id in ($sales_ids)";
			}
		}

		// GREY FABRIC DETAILS ARRAY START
		$grey_fabric_status_sql = "select a.mst_id,a.color_id, listagg(a.determination_id, ',') within group (order by a.determination_id) as determination_id ,sum(a.grey_qty) grey_qty from fabric_sales_order_dtls a where a.status_active = 1 and a.is_deleted = 0 $grey_fabric_status_fso_Cond  group by a.mst_id, a.color_id"; //and a.mst_id in($sales_ids)

		$grey_fabric_status_info = sql_select($grey_fabric_status_sql);
		foreach ($grey_fabric_status_info as $row) {
			$grey_fabric_status_arr[$row[csf('mst_id')]][] = array(
				'color_id' => $row[csf("color_id")],
				'determination_id' => $row[csf("determination_id")],
				'grey_qty' => $row[csf('grey_qty')]
			);

			$grey_fabric_deter_arr[$row[csf('mst_id')]] .= $row[csf("determination_id")].",";
		}

		// TRANSFER IN ARRAY
		$transfer_in_sql = sql_select("select a.transfer_system_id ,a.company_id,a.to_order_id,b.from_prod_id,c.product_name_details,c.detarmination_id,sum(b.transfer_qnty) transfer_qnty from inv_item_transfer_mst a,inv_item_transfer_dtls b,product_details_master c where a.id=b.mst_id and b.from_prod_id=c.id and a.status_active=1 and b.status_active=1 and a.entry_form=133 and a.transfer_criteria=4 $transfer_in_fso_Cond  group by a.transfer_system_id ,a.company_id,a.to_order_id,b.from_prod_id,c.product_name_details,c.detarmination_id");
  			//and a.to_order_id in($sales_ids)
		$transfer_arr=array();
		foreach ($transfer_in_sql as $transfer_row) {
			$transfer_arr[$transfer_row[csf('to_order_id')]][] = array(
				'fabric_desc' => $transfer_row[csf("product_name_details")],
				'detarmination_id' => $transfer_row[csf("detarmination_id")],
				'transfer_qnty' => $transfer_row[csf('transfer_qnty')]
			);
		}

		// grey fabric delivery to store
		$sql_grey_delivery =" select a.id, a.order_id,a.entry_form,a.color_id, a.uom, (a.current_delivery) as grey_delivery, (case when a.entry_form = 54 and a.is_sales =1 then a.current_delivery else 0 end) fin_delivery from pro_grey_prod_delivery_dtls a left join pro_roll_details b on a.id = b.dtls_id and b.entry_form = 56 and b.is_sales = 1 and b.status_active=1 where a.entry_form in(54,56) and a.status_active=1 and a.is_deleted=0  $grey_delivery_fso_Cond ";  //  and a.order_id in($sales_ids)
		$data_grey_delivery = sql_select($sql_grey_delivery);
		foreach ($data_grey_delivery as $greyDel) {
			if($greyDel[csf('entry_form')] == 56)
			{
				$greyDeliveryArray[$greyDel[csf('order_id')]] += $greyDel[csf('grey_delivery')];
			}
			else
			{
				$finDeliveryArray[$greyDel[csf('order_id')]][$greyDel[csf('color_id')]][$greyDel[csf('uom')]] += $greyDel[csf('fin_delivery')];
			}
		}

		$buyer_name_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
		$company_arr = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
		$color_array = return_library_array("select id, color_name from lib_color", "id", "color_name");
		$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
		$supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
		$yarn_count_details = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");


		// YARN DETAILS ARRAY START
		$yarn_sql = "select b.mst_id, b.yarn_count_id,  b.composition_id, b.composition_perc,  b.color_id, b.yarn_type, sum(b.cons_qty) as cons_qty from fabric_sales_order_yarn_dtls b where b.status_active=1 and b.is_deleted=0 $yarn_sql_fso_Cond group by b.mst_id, b.composition_id, b.composition_perc,  b.color_id,b.yarn_type, b.yarn_count_id";   //and b.mst_id in($sales_ids)
		$yarn_info = sql_select($yarn_sql);
		foreach ($yarn_info as $row) {

			$yarn_desc_key = $yarn_count_details[$row[csf('yarn_count_id')]].", ".$composition[$row[csf('composition_id')]]." ".$row[csf('composition_perc')]." % ".$yarn_type[$row[csf('yarn_type')]];
			$yarn_details_arr[$row[csf('mst_id')]][$yarn_desc_key] += $row[csf('cons_qty')];

			$yarn_sales_requ_description_array[$row[csf('mst_id')]][$yarn_desc_key]["sales_yarn"] += $row[csf('cons_qty')];
		}

		// PREPARE YARN REQUISITION DATA ARRAY
		$yarn_qty_requisition = sql_select("select a.dtls_id, a.determination_id,b.knit_id,b.requisition_no,b.yarn_qnty,a.po_id,b.id,b.prod_id,c.yarn_count_id,c.yarn_comp_percent1st,c.yarn_comp_type1st,c.yarn_type from ppl_planning_entry_plan_dtls a inner join ppl_yarn_requisition_entry b on a.dtls_id = b.knit_id inner join product_details_master c on b.prod_id=c.id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $yarn_qty_requisition_fso_Cond and is_sales = 1"); //  and a.po_id in($sales_ids)
		foreach ($yarn_qty_requisition as $row)
		{
			$yarn_desc_key = $yarn_count_details[$row[csf('yarn_count_id')]].", ".$composition[$row[csf('yarn_comp_type1st')]]." ".$row[csf('yarn_comp_percent1st')]." % ".$yarn_type[$row[csf('yarn_type')]];
			$yarn_sales_requ_description_array[$row[csf('po_id')]][$yarn_desc_key]["requisition"] += $row[csf('yarn_qnty')];



			$yarn_requi_pop_ref_key = $row[csf('yarn_count_id')].",".$row[csf('yarn_comp_type1st')].",".$row[csf('yarn_comp_percent1st')].",".$row[csf('yarn_type')];

			$yarn_qty_requisition_arr[$row[csf('po_id')]][$yarn_desc_key]['requisition_no'] .= $row[csf('requisition_no')].",";
			$yarn_pop_description_ref_key_arr[$yarn_desc_key] = $yarn_requi_pop_ref_key;

		}

		$sql_yarn_iss_return = sql_select("select sum(b.quantity) as returned_qnty, c.yarn_type, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, e.id as sales_id from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d,fabric_sales_order_mst e where a.id=d.mst_id and b.po_breakdown_id=e.id and d.transaction_type=4 and c.item_category_id=1 and d.item_category=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and b.prod_id=c.id $yarn_iss_return_fso_Cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2 group by c.yarn_type, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, e.id");  //and e.id in ($sales_ids)
		foreach ($sql_yarn_iss_return as $row) {

			$yarn_desc_reference_for_pop_up = $row[csf('yarn_count_id')].",".$row[csf('yarn_comp_type1st')].",".$row[csf('yarn_comp_percent1st')].",".$row[csf('yarn_type')];
			$yarn_issue_return_arr[$row[csf('sales_id')]][$yarn_desc_reference_for_pop_up] += $row[csf('returned_qnty')];
		}
		unset($sql_yarn_iss_return);

		$sql_yarn_iss = "select a.po_id, e.yarn_count_id, e.yarn_comp_type1st, e.yarn_comp_percent1st, e.yarn_type,sum(d.cons_quantity) cons_quantity from ppl_planning_entry_plan_dtls a inner join ppl_yarn_requisition_entry b on a.dtls_id = b.knit_id inner join inv_transaction d on (b.requisition_no=d.requisition_no and d.transaction_type=2 and b.prod_id=d.prod_id) inner join product_details_master e on d.prod_id = e.id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $yarn_iss_fso_Cond and a.is_sales=1 group by a.po_id, e.yarn_count_id, e.yarn_comp_type1st, e.yarn_comp_percent1st, e.yarn_type";
		// echo $sql_yarn_iss;
		$dataArrayIssue = sql_select($sql_yarn_iss);  //and a.po_id in($sales_ids)
		$yarn_desc_key="";
		foreach ($dataArrayIssue as $row_yarn_iss) {
			$yarn_desc_key = $yarn_count_details[$row_yarn_iss[csf('yarn_count_id')]].", ".$composition[$row_yarn_iss[csf('yarn_comp_type1st')]]." ".$row_yarn_iss[csf('yarn_comp_percent1st')]." % ".$yarn_type[$row_yarn_iss[csf('yarn_type')]];

			$yarn_desc_reference_for_pop_up = $row_yarn_iss[csf('yarn_count_id')].",".$row_yarn_iss[csf('yarn_comp_type1st')].",".$row_yarn_iss[csf('yarn_comp_percent1st')].",".$row_yarn_iss[csf('yarn_type')];

			$yarn_sales_requ_description_array[$row_yarn_iss[csf('po_id')]][$yarn_desc_key]["issue"] += $row_yarn_iss[csf('cons_quantity')];

			//$yarn_sales_requ_description_array[$row_yarn_iss[csf('po_id')]][$yarn_desc_key]['popup_des_ref'] = $yarn_desc_reference_for_pop_up;

			$yarn_pop_description_ref_key_arr[$yarn_desc_key] = $yarn_desc_reference_for_pop_up;

		}
		unset($dataArrayIssue);


		$dataArrayTrans = sql_select("select a.po_breakdown_id, a.trans_id,a.entry_form,
			sum(case when a.entry_form = 2 and a.trans_id <> 0 then quantity else 0 end) as grey_receive,
			sum(case when a.entry_form = 22 and a.trans_id <> 0 and c.entry_form = 22 and c.receive_basis =1 then quantity else 0 end) as grey_purchase,
			sum(case when a.entry_form = 2 then quantity else 0 end) as grey_production,
			sum(case when a.entry_form = 58 then quantity else 0 end) as grey_roll_receive,
			sum(case when a.entry_form =61 then a.quantity else 0 end) as grey_issue_roll_wise,
			sum(case when a.entry_form =84 then a.quantity else 0 end) as grey_issue_return_roll_wise,
			sum(case when a.entry_form =133 and a.trans_type=6 then quantity else 0 end) as transfer_out,
			sum(case when a.entry_form =133 and a.trans_type=5 then quantity else 0 end) as transfer_in
			from order_wise_pro_details a left join pro_grey_prod_entry_dtls b on a.dtls_id = b.id left join inv_receive_master c on b.mst_id = c.id and c.entry_form = 22 and c.receive_basis =1
			where a.status_active=1 and a.is_deleted=0 and a.entry_form in(2,22,58,61,84,133) $trans_grey_issue_return_fso_Cond  and a.is_sales = 1
			group by a.po_breakdown_id,a.trans_id,a.entry_form
		order by a.entry_form");  //  and a.po_breakdown_id in($sales_ids)

		foreach ($dataArrayTrans as $row)
		{
			$grey_receive_qnty_arr[$row[csf('po_breakdown_id')]] +=$row[csf('grey_receive')] + $row[csf('grey_roll_receive')] + $row[csf('grey_purchase')];
			$grey_issue_qnty_arr[$row[csf('po_breakdown_id')]] += $row[csf('grey_issue_roll_wise')];

			$grey_production_qnty_arr[$row[csf('po_breakdown_id')]] +=$row[csf('grey_production')];

			$trans_qnty_arr[$row[csf('po_breakdown_id')]]["transfer_out"] += $row[csf('transfer_out')];
			$trans_qnty_arr[$row[csf('po_breakdown_id')]]["transfer_in"] += $row[csf('transfer_in')];
		}

		//Grey Issue Return
		$grey_issue_return = sql_select("select a.barcode_no, a.po_breakdown_id, a.qnty
			from pro_roll_details a, pro_roll_details b
			where a.barcode_no = b.barcode_no and a.entry_form=61 and b.entry_form = 84 and a.is_sales =1
			and a.is_deleted =0 and a.status_active = 1 and b.is_deleted =0 and b.status_active = 1 $trans_grey_issue_return_fso_Cond
			group by  a.barcode_no, a.po_breakdown_id, a.qnty");
		// and a.po_breakdown_id in($sales_ids)

		foreach ($grey_issue_return as $row)
		{
			$grey_issue_return_qnty_arr[$row[csf('po_breakdown_id')]] +=$row[csf('qnty')];
		}


		// RECEIVE BY BATCH ARRAY
		$receive_by_batch_sql=sql_select("select c.po_breakdown_id,sum(c.qnty) roll_wgt from inv_receive_mas_batchroll a,pro_roll_details c where a.id=c.mst_id and c.entry_form=62 and a.is_deleted=0 and a.status_active=1 and c.status_active=1 and c.is_deleted=0 $receive_by_batch_fso_Cond group by c.po_breakdown_id"); //  and c.po_breakdown_id in($sales_ids)
		$receive_by_batch_arr=array();
		foreach ($receive_by_batch_sql as $row) {
			$receive_by_batch_arr[$row[csf("po_breakdown_id")]]['receive_qnty'] += $row[csf("roll_wgt")];
		}

		// BATCH ARRAY
		$batch_sql = "SELECT a.sales_order_id,a.color_id,c.detarmination_id,a.extention_no,sum(b.batch_qnty) qnty from pro_batch_create_mst a,pro_batch_create_dtls b,product_details_master c where a.id=b.mst_id and b.prod_id=c.id $batch_fso_Cond and (a.extention_no is null or a.extention_no=0) and a.status_active=1 and a.is_deleted=0 and a.entry_form = 0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.sales_order_id,a.color_id,c.detarmination_id,a.extention_no";  // and a.sales_order_id in($sales_ids)
		$batch_result = sql_select($batch_sql);
		$batch_arr=array();
		foreach ($batch_result as $row) {
			$batch_arr[$row[csf("sales_order_id")]][$row[csf("color_id")]] += $row[csf("qnty")];

			$total_batch_qnty_arr[$row[csf("sales_order_id")]]["total_fab_batch"] += $row[csf("qnty")];
		}
		unset($batch_result);

		// DYEING PRODUCTION
		$sql_dye = "select b.po_id, a.color_id, sum(b.batch_qnty) as dye_qnty from pro_batch_create_mst a, pro_batch_create_dtls b, pro_fab_subprocess c,product_details_master d where a.id=b.mst_id and a.id=c.batch_id and b.prod_id=d.id and c.load_unload_id=2 and c.entry_form=35 and a.batch_against<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $dye_fso_Cond group by b.po_id, a.color_id";  // and b.po_id in ($sales_ids)
		$resultDye = sql_select($sql_dye);
		foreach ($resultDye as $dyeRow) {
			$dye_qnty_arr[$dyeRow[csf('po_id')]][$dyeRow[csf('color_id')]] += $dyeRow[csf('dye_qnty')];
		}
		unset($resultDye);


		$sales_fin_qnty_arr = array();
		$salesOrderDetailsDataSql = "SELECT cons_uom, mst_id,color_id,sum(grey_qty) grey_qty,order_uom from fabric_sales_order_dtls where is_deleted=0 and status_active=1 $sales_fin_qnty_fso_Cond group by cons_uom, mst_id,color_id,order_uom";  // and mst_id in($sales_ids)
		$salesOrderDetailsDataResult = sql_select($salesOrderDetailsDataSql);
		foreach ($salesOrderDetailsDataResult as $row)
		{
			//$sales_fin_qnty_arr[$row[csf('mst_id')]][$row[csf("color_id")]][$row[csf("cons_uom")]] += $row[csf("grey_qty")];
			$sales_fin_qnty_arr[$row[csf('mst_id')]][$row[csf("color_id")]][$row[csf("order_uom")]] += $row[csf("grey_qty")];
		}

		// FINISH PRODUCTION
		$finish_sql = sql_select("SELECT b.uom, c.po_breakdown_id,b.color_id,sum(b.receive_qnty ) fin_receive_qnty from inv_receive_master a,pro_finish_fabric_rcv_dtls b,order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.entry_form in(7) $finish_production_fso_Cond and b.is_sales =1 group by b.uom,c.po_breakdown_id,b.color_id"); //  and c.po_breakdown_id in($sales_ids)

		foreach ($finish_sql as $row) {
			//$finish_arr[$row[csf('po_breakdown_id')]][$row[csf('fabric_description_id')]][$row[csf('color_id')]]['prod_id'] = $row[csf("prod_id")];
			$finish_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]][$row[csf('uom')]]['fin_production_qnty'] += $row[csf("fin_receive_qnty")];
			//$finish_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['delivery_to_store'] += $finDeliveryArray[$row[csf('po_breakdown_id')]][$row[csf('color_id')]];
		}
		unset($finish_sql);

		$fin_rcv_trans_iss_sql = sql_select("select a.entry_form,a.trans_type, a.color_id, a.po_breakdown_id, a.quantity, b.cons_uom from order_wise_pro_details a, inv_transaction b where a.trans_id=b.id and a.entry_form in (225,287,224,230,233) and a.is_sales=1 and a.is_deleted=0 and a.status_active=1 $fin_rcv_trans_iss_fso_Cond");
		foreach ($fin_rcv_trans_iss_sql as $row)
		{
			if($row[csf('entry_form')] == 225)
			{
				$finish_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]][$row[csf('cons_uom')]]["fin_receive_qnty"] += $row[csf('quantity')];
			}
			else if($row[csf('entry_form')] == 287)
			{
				$finish_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]][$row[csf('cons_uom')]]["fin_receive_return_qnty"] += $row[csf('quantity')];
			}
			else if($row[csf('entry_form')] == 230)
			{
				if($row[csf('trans_type')] == 5){
					$finish_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]][$row[csf('cons_uom')]]["fin_trans_in_qnty"] += $row[csf('quantity')];
				}else{
					$finish_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]][$row[csf('cons_uom')]]["fin_trans_out_qnty"] += $row[csf('quantity')];
				}

				$textile_garments_trans_in_color[$row[csf('po_breakdown_id')]][$row[csf('color_id')]] = $row[csf('color_id')];
			}
			else if($row[csf('entry_form')] == 233)
			{
				$finish_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]][$row[csf('cons_uom')]]["fin_issue_ret_qnty"] += $row[csf('quantity')];
			}
			else{
				$finish_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]][$row[csf('cons_uom')]]["fin_issue_qnty"] += $row[csf('quantity')];
			}
		}

		foreach($salesOrderDataResult as $row)
		{
			$sales_id = $row[csf('id')];
			$pub_shipment_date = $row[csf('pub_shipment_date')];
			$ids=array_unique(explode(",",$listagg_sql_po_item_arr_full[$sales_id]["po"]));
			foreach($ids as $po_val)$all_po_arr[$po_val]=$po_val;
		}
		$all_po_arr = array_filter($all_po_arr);

		$all_po_ids= implode(",", $all_po_arr);
		$po_conds=" and po_break_down_id in($all_po_ids)";
		$po_conds_2=" and po_breakdown_id in($all_po_ids)";
		if($db_type==2 && count($all_po_arr)>999)
		{
			$po_conds=$po_conds_2="";
			$chnk=array_chunk($all_po_arr, 999);
			foreach($chnk as $val)
			{
				$ids=implode(",", $val);
				if(!$po_conds){
					$po_conds.=" and ( po_break_down_id in($ids) ";
					$po_conds_2.=" and ( po_breakdown_id in($ids) ";
				}
				else {
					$po_conds.=" or  po_break_down_id in($ids) ";
					$po_conds_2.=" or  po_breakdown_id in($ids) ";
				}
			}
			$po_conds.=")";
			$po_conds_2.=")";

		}
		// echo $po_conds;die;
		$po_conds=str_replace(",,",",", $po_conds);
		// echo $po_conds;die;
		$col_sql="SELECT  pub_shipment_date,color_number_id, po_break_down_id,  order_quantity  FROM wo_po_break_down a, wo_po_color_size_breakdown b Where a.id=b.po_break_down_id and a.status_active=1 and b.status_active = 1 $po_conds  "; 
		// echo $col_sql;
		foreach(sql_select($col_sql) as $v)
		{
			$po_wise_qnty[$v[csf("po_break_down_id")]]+=$v[csf("order_quantity")];
			//$pub_shipment_date_wise_color[$v[csf("pub_shipment_date")]].=','.$v[csf("color_number_id")];
		} 

		
		$garments_trans_in_order = array();
		$finish_fab_gmts_sql = sql_select("SELECT a.entry_form,a.trans_type, a.color_id, a.po_breakdown_id, a.quantity, b.cons_uom from order_wise_pro_details a, inv_transaction b where a.trans_id=b.id and a.entry_form in (7,14,37,46) and a.is_sales!=1 and a.is_deleted=0 and a.trans_id>0 and a.status_active=1 $po_conds_2"); //18,52

		foreach ($finish_fab_gmts_sql as $row)
		{
			if($row[csf('entry_form')] == 7 || $row[csf('entry_form')] == 37)
			{
				$finish_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]][$row[csf('cons_uom')]]["fin_gmts_rcv_qnty"] += $row[csf('quantity')];
			}
			else if($row[csf('entry_form')] == 46)
			{
				$finish_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]][$row[csf('cons_uom')]]["fin_gmts_rcv_rtn_qnty"] += $row[csf('quantity')];
			}
			else if($row[csf('entry_form')] == 14)
			{
				if($row[csf('trans_type')] == 5){
					$finish_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]][$row[csf('cons_uom')]]["fin_gmts_trans_in_qnty"] += $row[csf('quantity')];
					$garments_trans_in_order[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
				}else{
					$finish_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]][$row[csf('cons_uom')]]["fin_gmts_trans_out_qnty"] += $row[csf('quantity')];
				}
				

				$textile_garments_trans_in_color[$sales_order_of_po_arr[$row[csf('po_breakdown_id')]]][$row[csf('color_id')]] = $row[csf('color_id')];
				// here color is againt sales order
			}
			/*else if($row[csf('entry_form')] == 52)
			{
				$finish_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]][$row[csf('cons_uom')]]["fin_gmts_issue_ret_qnty"] += $row[csf('quantity')];
			}
			else{
				$finish_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]][$row[csf('cons_uom')]]["fin_gmts_issue_qnty"] += $row[csf('quantity')];
			}*/
		}
		//print_r($finish_arr[43248]);

		$col_sql2="SELECT c.id as sales_id, b.booking_no, pub_shipment_date,b.fabric_color_id as color_number_id,b.gmts_color_id  FROM wo_po_break_down a, wo_booking_dtls b,fabric_sales_order_mst c  Where a.id=b.po_break_down_id and b.booking_no=c.sales_booking_no and a.status_active=1 and b.status_active = 1 $po_conds group by c.id , b.booking_no, pub_shipment_date,b.fabric_color_id ,b.gmts_color_id"; 
		foreach(sql_select($col_sql2) as $v)
		{
			$pub_shipment_date_wise_color[$v[csf("sales_id")]][$v[csf("pub_shipment_date")]][$v[csf("booking_no")]].=','.$v[csf("color_number_id")];
			//$pub_shipment_date_wise_color2[$v[csf("sales_id")]][$v[csf("pub_shipment_date")]][$v[csf("booking_no")]][$v[csf("color_number_id")]].=','. $v[csf("gmts_color_id")];
			$pub_shipment_date_wise_color3[$v[csf("sales_id")]][$v[csf("booking_no")]][$v[csf("color_number_id")]].=','. $v[csf("gmts_color_id")];

			foreach ($textile_garments_trans_in_color[$v[csf("sales_id")]]  as $key => $color) 
			{
				$pub_shipment_date_wise_color[$v[csf("sales_id")]][$v[csf("pub_shipment_date")]][$v[csf("booking_no")]].=','.$color;
				$pub_shipment_date_wise_color3[$v[csf("sales_id")]][$v[csf("booking_no")]][$v[csf("color_number_id")]].=','. $color;
			}
		}
	}
	
	
	//echo "<pre>";
	//print_r($pub_shipment_date_wise_color);die;


	$all_po_arr_sales=array_unique( explode(",", $sales_ids));
	$all_po_ids= implode(",", $all_po_arr_sales);
	$sales_po_conds=" and a.id in($all_po_ids)";
	if($db_type==2 && count($all_po_arr_sales)>999)
	{
		$sales_po_conds="";
		$chnk=array_chunk($all_po_arr_sales, 999);
		foreach($chnk as $val)
		{
			$ids=implode(",", $val);
			if(!$sales_po_conds)$sales_po_conds.=" and ( a.id in($ids) ";
			else $sales_po_conds.=" or  a.id in($ids) ";
		}
		$sales_po_conds.=")";
	}

	$color_wise_qnty_sql=" SELECT e.id AS col_id,  b.pub_shipment_date,a.id ,e.color_number_id,  e.order_quantity as qnty from fabric_sales_order_mst a, wo_booking_mst c,wo_po_break_down b ,wo_booking_dtls d,wo_po_color_size_breakdown e where a.company_id =$company_name and a.booking_id=c.id and b.job_no_mst=c.job_no and d.po_break_down_id=b.id and c.booking_no=d.booking_no and d.po_break_down_id=e.po_break_down_id and e.status_active=1 and d.status_active=1 and b.status_active=1 and a.is_deleted=0 and a.status_active=1  $sales_po_conds group by e.id, b.pub_shipment_date, a.id, e.color_number_id, e.order_quantity"; 

	 foreach(sql_select($color_wise_qnty_sql) as $vals)
	 {
	 	if(!$chk_arr[$vals[csf("col_id")]])
	 	{ 
	 		$color_wise_qnty_arr[$vals[csf("id")]][$vals[csf("color_number_id")]]+=$vals[csf("qnty")];
	 		$date_color_wise_qnty_arr[$vals[csf("id")]][$vals[csf("pub_shipment_date")]][$vals[csf("color_number_id")]]+=$vals[csf("qnty")];
	 		$chk_arr[$vals[csf("col_id")]]=420;
	 	}
	 }

	//echo "<pre>";	print_r($color_wise_qnty_arr); //die;
	// echo "<pre>";	print_r($salesOrderDataResult);die;
	foreach($salesOrderDataResult as $row)
	{
		$sales_id = $row[csf('id')];
		$pub_shipment_date = $row[csf('pub_shipment_date')];
		$ids=array_unique(explode(",",$listagg_sql_po_item_arr_full[$sales_id]["po"]));
		//print_r($ids);
		//foreach($ids as $po_val)$all_po_arr[$po_val]=$po_val;
		$po_ids=$ids;
		foreach($ids as $vals)
		{
				 
			if(! $all_dynamic_calc[$sales_id][$vals]["po_no"] )
			{
				$all_array_info[$sales_id]["po_qty"]+=$po_wise_qnty[$vals] ;
				$all_dynamic_calc[$sales_id][$vals]["po_no"]=420;
			}
		}

		$po_ids=array_unique(explode(",", $listagg_sql_po_item_arr[$sales_id][$pub_shipment_date]["po_ids"]));
		$grey_fabric_status_details = $grey_fabric_status_arr[$sales_id];

		$po_qnty=0;
		foreach($po_ids as $value)
		{

			$po_qnty+=$po_wise_qnty[$value];
		}
		foreach ($grey_fabric_status_details as $grey_fabric_row) 
		{

			$batch_qnty = $batch_arr[$sales_id][$grey_fabric_row['color_id']];	    					 
			$batch_qnty=($batch_qnty/$all_array_info[$sales_id]["po_qty"])*$po_qnty;
			$array_for_yet_to_batch[$sales_id][$pub_shipment_date]+=$batch_qnty;
		}
	} 
	//print_r($array_for_yet_to_batch);die;
	ob_start();

	?>
	<style type="text/css">
	.alignment_css
	{
		word-break: break-all;
		word-wrap: break-word;
	}
</style>

<fieldset width="6110">
	<table cellpadding="5" cellspacing="0" width="6110">
		<tr>
			<td width="100%"  colspan="36" style="font-size:16px; text-align: left !important;">
				<strong><?php echo $company_library[$company_name]; ?></strong></td>
			</tr>
			<tr>
				<td align="center" width="100%" colspan="36" style="font-size:16px; text-align: left !important;">
					<strong><? if ($start_date != "" && $end_date != "") echo "From " . change_date_format($start_date) . " To " . change_date_format($end_date); ?></strong>
				</td>
			</tr>
		</table>
		<table class="rpt_table" border="1" rules="all" width="6110" cellpadding="1" cellspacing="0" id="tbl_list_search" align="left">
			<thead>
				<tr>
					<th class="alignment_css" colspan="10">Order Details</th>
					<th class="alignment_css" colspan="5">Yarn Status</th>
					<th class="alignment_css" colspan="4">Knitting Production</th>
					<th class="alignment_css" colspan="6">Grey Fabric Store Status</th>
					<th class="alignment_css" colspan="5">Dyeing Production</th>
					<th class="alignment_css" colspan="6">Finish Fabric Production</th>
					<th class="alignment_css" colspan="12">Textile Fabric Store Transaction</th>
					<th class="alignment_css" colspan="8">Garments Fabric Store Transaction</th>
					 
				</tr>
				<tr>
					<th  class="alignment_css"  width="40" rowspan="2">SL</th>
					<th  class="alignment_css"  width="165" rowspan="2">Shipdate</th>
					<th  class="alignment_css"  width="100" rowspan="2">Buyer Name</th>
					<th  class="alignment_css"  width="165" rowspan="2">Style Ref.</th>
					<th  class="alignment_css"  width="100" rowspan="2">Item Name</th>
					<th  class="alignment_css"  width="150" rowspan="2">Fabric Booking No</th>
					<th  class="alignment_css"  width="170" rowspan="2">Sales Order Number</th>
					<th  class="alignment_css"  width="180" rowspan="2">Po Number</th>
					<th  class="alignment_css"  width="100" rowspan="2">Po Qnty</th>
					<th  class="alignment_css"  width="100" rowspan="2">Fabric Delivery<br> Date</th>
					<th  class="alignment_css"  width="100" rowspan="2">Yarn Description</th>
					
					<th  class="alignment_css"  width="100" rowspan="2">Required<br/>
						<small>(As Per Sales <br>Order)</small>
					</th>
					<th  class="alignment_css"  width="100" rowspan="2">Required<br/>
						<small>(As Per Req.)</small>
					</th>
					<th  class="alignment_css"  width="100" rowspan="2">Issued</th>
					<th  class="alignment_css"  width="100" rowspan="2">Balance<br/>
						<small>(Required as per <br>requisition - Issue)</small>
					</th>
					<th  class="alignment_css"  width="280" rowspan="2">Fabric Description</th>
					<th  class="alignment_css"  width="100" rowspan="2">Grey Required<br/>
						<small>(As Per Sales <br>Order)</small>
					</th>
					<th  class="alignment_css"  width="100" rowspan="2">Grey Production</font></th>
					<th  class="alignment_css"  width="100" rowspan="2">Knitting Balance<br/>
						<small>(Grey Required - <br>Grey Prod.)</small>
					</th>
					 
					<th  class="alignment_css"  width="100" rowspan="2">Grey Receive</th>
    				 
    				<th  class="alignment_css"  width="100" colspan="2">Transfer</th>
    				 
    				<th  class="alignment_css"  width="100" rowspan="2">Grey Balance<br/>
    					<small>(Grey Required - <br>Available)</small>
    				</th>
    				<th  class="alignment_css"  width="100" rowspan="2">Grey Issue</th>
    				<th  class="alignment_css"  width="100" rowspan="2">Grey In<br> Hand<br/>
    					<small>(Grey Available - <br>Grey Issue)</small>
    				</th>
    				 
    				<th  class="alignment_css"  width="100" rowspan="2">Fabric Color</th>
    				<th  class="alignment_css"  width="100" rowspan="2">Batch Qnty</th>
    				<th  class="alignment_css"  width="100" rowspan="2">Yet To Batch<br/>
    					<small>(Grey Issue - <br>Batch Qty)</small>
    				</th>
    				<th class="alignment_css" width="100" rowspan="2">Dye Qnty</th>
    				<th class="alignment_css" width="100" rowspan="2">Balance Qty</th>
    				<th class="alignment_css" width="100" rowspan="2">Req. Qty<br>(Kg)</th>
    				<th class="alignment_css" width="100" rowspan="2">Req. Qty<br>(Yds)</th>
    				<th class="alignment_css" width="100" rowspan="2">Production Qty<br>(Kg)</th>
    				<th class="alignment_css" width="100" rowspan="2">Production Qty<br>(Yds)</th>
    				<th class="alignment_css" width="100" rowspan="2">Balance Qty<br>(Kg)</th>
    				<th class="alignment_css" width="100" rowspan="2">Balance Qty<br>(Yds)</th>

    				<th class="alignment_css" width="100" rowspan="2">Delivery To Tex. Store<br>(Kg)</th>
    				<th class="alignment_css" width="100" rowspan="2">Delivery To Tex. Store<br>(Yds)</th>
    				<th class="alignment_css" width="100" rowspan="2">Store Rcv<br>(Kg)</th>
    				<th class="alignment_css" width="100" rowspan="2">Store Rcv<br>(Yds)</th>
    				<th class="alignment_css" width="100" rowspan="2">Transfer In<br>(Kg)</th>
    				<th class="alignment_css" width="100" rowspan="2">Transfer In<br>(Yds)</th>
    				<th class="alignment_css" width="100" rowspan="2">Transfer Out<br>(Kg)</th>
    				<th class="alignment_css" width="100" rowspan="2">Transfer Out<br>(Yds)</th>
    				<th class="alignment_css" width="100" rowspan="2">Delivery Balance<br>(Kg)</th>
    				<th class="alignment_css" width="100" rowspan="2">Delivery Balance<br>(Yds)</th>
    				<th class="alignment_css" width="100" rowspan="2">Delivery To Garment Store<br>(Kg)</th>
    				<th class="alignment_css" width="100" rowspan="2">Delivery To Garment Store<br>(Yds)</th>

    				<th class="alignment_css" width="100" rowspan="2">Store Rcv<br>(Kg)</th>
    				<th class="alignment_css" width="100" rowspan="2">Store Rcv<br>(Yds)</th>
    				<th class="alignment_css" width="100" rowspan="2">Transfer In<br>(Kg)</th>
    				<th class="alignment_css" width="100" rowspan="2">Transfer In<br>(Yds)</th>
    				<th class="alignment_css" width="100" rowspan="2">Transfer Out<br>(Kg)</th>
    				<th class="alignment_css" width="100" rowspan="2">Transfer Out<br>(Yds)</th>
    				<th class="alignment_css" width="100" rowspan="2">Delivery Balance<br>(Kg)</th>
    				<th class="alignment_css" width="100" rowspan="2">Delivery Balance<br>(Yds)</th>	 
    			</tr>
    			<tr>
    				<th width="50">In</th>
    				<th width="50">Out</th>
    			</tr>
			</thead>
		</table>
		<div style="width:6130px; max-height:540px; overflow-y:scroll"  align="left" id="scroll_body">
			<table class="rpt_table" border="1" rules="all" width="6110" cellpadding="1" cellspacing="0" id="tbl_list_search" align="left">
				<tbody>
					<?
					$i=1; $y=1;
					
					// print_r($po_wise_qnty);die;
					$total_po_qnty=0;
					$total_transfer_out_qnty=0;

					foreach ($salesOrderDataResult as $row)
					{
						$sales_id = $row[csf('id')];
						$pub_shipment_date=$row[csf("pub_shipment_date")];
						$item_ids=array_unique(explode(",", $listagg_sql_po_item_arr[$sales_id][$pub_shipment_date]["item"]));
						/*$item_string="";
						foreach($item_ids as $v)
						{
							if($item_string)$item_string.=",".$v; else $item_string.=$v;
						}*/

						$item_string=$listagg_sql_po_item_arr[$sales_id][$pub_shipment_date]["item"];
						$po_numbers=array_unique(explode(",", $listagg_sql_po_item_arr[$sales_id][$pub_shipment_date]["po"]));
						$po_string="";

						foreach($po_numbers as $vals)
						{
							if($po_string)$po_string.=",".$vals; else $po_string.=$vals;

						}

						$po_ids_arr=array_unique(explode(",", $listagg_sql_po_item_arr[$sales_id][$pub_shipment_date]["po_ids"]));
						// print_r($po_ids_arr);die('kakku');
						$po_qnty=0;
						foreach($po_ids_arr as $vals)
						{

							$po_qnty+=$po_wise_qnty[$vals]; 
						}

						$within_group = ($row[csf('within_group')] == 1) ? "Yes" : "No";
						$sales_order = "<a href='##' style='color:#000' onclick=\"fnc_fabric_sales_order_print('" . $company_name . "','" . $row[csf('booking_id')] . "','" . $row[csf('booking_no')] . "','" . $row[csf('sales_job_no')] . "','fabric_sales_order_print3')\"><font style='font-weight:bold' $wo_color>" . $row[csf('sales_job_no')] . "</font></a>";
						$po_ids = rtrim($row[csf('po_break_down_id')], ',');

						if ($row[csf('within_group')] == 1) {
							$main_booking = "<a href='##' style='color:#000' onclick=\"generate_worder_report('" . $row[csf('booking_type')] . "','" . $row[csf('booking_no')] . "','" . $company_name . "','" . $po_ids . "','" . $row[csf('item_category')] . "','" . $row[csf('fabric_source')] . "','" . $row[csf('job_no')] . "','" . $row[csf('is_approved')] . "'," . $row[csf('entry_form')] . "," . $row[csf('is_short')] . ",'show_fabric_booking_report_urmi')\"><font style='font-weight:bold' $wo_color >" . $row[csf('booking_no')] . "</font></a>";
						} else {
							$main_booking = "<strong>" . $row[csf('booking_no')] . "</strong>";
						}


						$grey_fabric_status_details = $grey_fabric_status_arr[$row[csf("id")]];
						$rowspan=$grey_required=0;
						$fabric_rowspan=array();
						foreach ($grey_fabric_status_details as $grey_fabric_row) {
							$grey_required += $grey_fabric_row["grey_qty"];
							$color_id=$grey_fabric_row['color_id'];
							$batch_qnty = $batch_arr[$row[csf("id")]][$color_id];
							//if($batch_qnty)	
								//$rowspan++;
						}
						//print_r(array_unique(explode(",",trim($pub_shipment_date_wise_color[$row[csf("id")]][$pub_shipment_date][$row[csf('booking_no')]],",") )));die;
						foreach(array_unique(explode(",",trim($pub_shipment_date_wise_color[$row[csf("id")]][$pub_shipment_date][$row[csf('booking_no')]],",") )) as $keys=>$vals)
						{
							$rowspan++;
						}

						$grey_receive_qnty=  $grey_receive_qnty_arr[$row[csf('id')]];
						
						$grey_production_qnty = $grey_production_qnty_arr[$row[csf('id')]];
						
						$grey_delivery_qnty = $greyDeliveryArray[$row[csf('id')]];
						$grey_in_knit_floor = $grey_production_qnty - $grey_delivery_qnty;
						$transfer_out_qnty = $trans_qnty_arr[$row[csf('id')]]["transfer_out"];
						$transfer_in_qnty = $trans_qnty_arr[$row[csf('id')]]["transfer_in"];

						//$grey_receive_balance = $knitting_balance - $transfer_in_qnty+ $transfer_out_qnty;
						
						$receive_by_batch_qnty = $receive_by_batch_arr[$row[csf("id")]]['receive_qnty'];

						$determination_id = chop($grey_fabric_deter_arr[$row[csf("id")]],",");
						$job_no = $style_owner_arr[$row[csf('booking_no')]]["job_no"]."__".$pub_shipment_date;
						$style_owner = $style_owner_arr[$row[csf('booking_no')]]["style_owner"];
						$buyer = ($row[csf('within_group')]==1)?$row[csf("po_buyer")]:$row[csf("sales_buyer")];

						$determination_id_arr = array_unique(explode(",", $determination_id));
						$fabric_desc="";
						foreach ($determination_id_arr as $val) {
							if($fabric_desc =="") $fabric_desc = $composition_arr[$val]; else $fabric_desc .= ", <br>". $composition_arr[$val];
						}

						$gmts_item='';
						$gmts_item_id=array_unique(explode(",",$job_gmts_item_arr[$row[csf('job_no')]]["gmts_item_id"]));
						foreach($gmts_item_id as $item_id)
						{
							if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
						}

						if ($y % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						?>
						<tr bgcolor='<? echo $bgcolor;?>' onClick="change_color('tr_<? echo $y; ?>','#FFFFFF')" id="tr_<? echo $y;?>">
							<td class="alignment_css" width="40" rowspan="<? echo $rowspan;?>"><? echo $i?></td>
							<td class="alignment_css" width="165" align="center" rowspan="<? echo $rowspan;?>">	 <? echo change_date_format($row[csf('pub_shipment_date')]);?>	</td>

						<td class="alignment_css" width="100" align="center" rowspan="<? echo $rowspan;?>"><? echo $buyer_name_array[$buyer] ?></td>
						<td class="alignment_css" width="165" rowspan="<? echo $rowspan;?>"><? echo $row[csf('style_ref_no')];?></td>
						<td class="alignment_css" width="100" align="center" rowspan="<? echo $rowspan;?>"><? echo $item_string;?></td>
						<td class="alignment_css" width="150" align="center" rowspan="<? echo $rowspan;?>">
							<b>
								<?
								if($row[csf('within_group')]==1)
								{
									?>
									<a href='##' style='color:#000' onClick="generate_worder_report('<? echo $row[csf('booking_type')]?>', '<? echo $row[csf('booking_no')]?>' , '<? echo  $style_owner ?>' , '<? echo $po_ids ?>' , '<? echo $row[csf('item_category')]; ?>', '<? echo $row[csf('fabric_source')] ?>' ,'<? echo $job_no ?>' ,'<? echo  $row[csf('is_approved')] ?>' , '<? echo $row[csf('entry_form')]; ?>' , '<? echo  $row[csf('is_short')] ?>' ,'show_fabric_booking_report_urmi')"> <? echo  $row[csf('booking_no')] ?>
									</a>
									<?
								}
								else
								{
									echo $row[csf("booking_no")];
								}
								?>
							</b>
						</td>
						<td class="alignment_css" width="170" rowspan="<? echo $rowspan;?>"><? echo $sales_order;?></td>
						<td width='180' valign='middle' align="center" class='' rowspan="<? echo $rowspan;?>" style='word-break:break-all' ><a href='#' data-job="<? echo $job_no;?>" class='view_order' title='Click here to view Order numbers'><? echo "View"; //$po_string;?></a></td>
						<td class="alignment_css" width="100" align="right" rowspan="<? echo $rowspan;?>"><? echo $po_qnty;?></td>
						<td class="alignment_css" width="100" align="center" rowspan="<? echo $rowspan;?>" ><? echo  change_date_format($row[csf('delivery_date')]);?> </td>
						 
						<td class="alignment_css" width="500"  rowspan="<? echo $rowspan;?>" >
							<table cellspacing="0" cellpadding="0" width="100%"  >
								<?
								$total_po_qnty+=$po_qnty;
								$popup_des_ref = array();
								$yarn_sales_requ_description = $yarn_sales_requ_description_array[$row[csf('id')]];
								$yarn_sales_description = $yarn_details_arr[$row[csf('id')]];
								$yarn_issue_return = $yarn_issue_return_arr[$row[csf('id')]];
								$yarn_requisition_no_arr = $yarn_qty_requisition_arr[$row[csf('id')]];
								foreach ($yarn_sales_requ_description as $key => $val)
								{
									$yarn_issue_return_qnty = $yarn_issue_return[$val['popup_des_ref']];
									$yarn_issue_return_qnty = is_nan($yarn_issue_return_qnty) ? 0 : $yarn_issue_return_qnty;
									
									if($yarn_sales_description[$key] =="") $Description = "";else $Description =$key;

									$popup_des_ref = explode(",", $yarn_pop_description_ref_key_arr[$key]);
									$yarn_count_id = $popup_des_ref[0];
									$yarn_comp_type1st = $popup_des_ref[1];
									$yarn_comp_percent1st = $popup_des_ref[2];
									$yarn_type = $popup_des_ref[3];

									$yarn_requisition_nos = chop($yarn_requisition_no_arr[$key]['requisition_no'],",")."****2";
									$val["sales_yarn"]=($val["sales_yarn"]/$all_array_info[$sales_id]["po_qty"])*$po_qnty;
									$val["requisition"]=($val["requisition"]/$all_array_info[$sales_id]["po_qty"])*$po_qnty;
									$val["issue"]=($val["issue"]/$all_array_info[$sales_id]["po_qty"])*$po_qnty;
									$yarn_bal = $val["requisition"]-$val["issue"]+$yarn_issue_return_qnty;

									$salesYarn = is_nan($val["sales_yarn"]) ? 0 : $val["sales_yarn"];
									$requisition = is_nan($val["requisition"]) ? 0 : $val["requisition"];
									$yarn_bal = is_nan($yarn_bal) ? 0 : $yarn_bal;
									$issued = is_nan($val["issue"]) ? 0 : $val["issue"];
									?>
									<tr>
										<td class="alignment_css" width="100" align="center"><p><? echo $Description;?>&nbsp;</p></td>
										<td class="alignment_css" width="100" align="right"><? echo number_format($salesYarn,2,".","");?></td>

										<td class="alignment_css" width='100' align="right"><p href='##' onDblClick="openmypage('<? echo $yarn_requisition_nos;?>','yarn_requisition_popup','<? echo $yarn_count_id;?>','<? echo $yarn_comp_type1st;?>','<? echo $yarn_comp_percent1st;?>','<? echo $yarn_type;?>')"> <? echo number_format($requisition,2,".","");?></p></td>

										<td class="alignment_css" width='100' align="right"><p href='##' onDblClick="openmypage('<? echo $row[csf("id")];?>','yarn_issue_popup_for_report_2','<? echo $yarn_count_id;?>','<? echo $yarn_comp_type1st;?>','<? echo $yarn_comp_percent1st;?>','<? echo $yarn_type;?>')"> <? echo number_format(($issued-$yarn_issue_return_qnty),2,".","");?></p></td>

										<td class="alignment_css" width="100" align="right"><? echo number_format($yarn_bal,2,".","");?></td>
									</tr>
									<?
									$total_yarn_req_sales += $val["sales_yarn"];
									$total_yarn_req_requisition += $val["requisition"];
									$total_yarn_issue += $val["issue"]-$yarn_issue_return_qnty;
									$total_yarn_balance += $yarn_bal;
								}
								$grey_issue_qnty = ($grey_issue_qnty_arr[$row[csf('id')]]/$all_array_info[$sales_id]["po_qty"])*$po_qnty;
								$grey_issue_return_qnty = ($grey_issue_return_qnty_arr[$row[csf('id')]]/$all_array_info[$sales_id]["po_qty"])*$po_qnty;
								$grey_receive_qnty=($grey_receive_qnty/$all_array_info[$sales_id]["po_qty"])*$po_qnty;
								$grey_receive_qnty = is_nan($grey_receive_qnty) ? 0 : $grey_receive_qnty;
								$grey_required=($grey_required/$all_array_info[$sales_id]["po_qty"])*$po_qnty;
								$grey_required = is_nan($grey_required) ? 0 : $grey_required;
								$transfer_in_qnty=($transfer_in_qnty/$all_array_info[$sales_id]["po_qty"])*$po_qnty;
								$transfer_in_qnty = is_nan($transfer_in_qnty) ? 0 : $transfer_in_qnty;
								$transfer_out_qnty=($transfer_out_qnty/$all_array_info[$sales_id]["po_qty"])*$po_qnty;
								$transfer_out_qnty = is_nan($transfer_out_qnty) ? 0 : $transfer_out_qnty;
								$grey_production_qnty=($grey_production_qnty/$all_array_info[$sales_id]["po_qty"])*$po_qnty;
								$grey_production_qnty=is_nan($grey_production_qnty) ? 0 : $grey_production_qnty;

								$knitting_balance = $grey_required - $grey_production_qnty ;
								$grey_receive_balance = $grey_required - ($grey_receive_qnty + ($transfer_in_qnty - $transfer_out_qnty));
								$grey_receive_balance = is_nan($grey_receive_balance) ? 0 : $grey_receive_balance;
								$grey_issue_without_return = $grey_issue_qnty - $grey_issue_return_qnty;
								$grey_issue_without_return = is_nan($grey_issue_without_return) ? 0 : $grey_issue_without_return;
								$grey_in_hand = ($grey_receive_qnty + $transfer_in_qnty) - ($grey_issue_without_return + $transfer_out_qnty);
								?>

							</table>
						</td>

						<td class="alignment_css" width="280" rowspan="<? echo $rowspan;?>" align="center"><? echo $fabric_desc;?></td>
						<td class="alignment_css" width="100" rowspan="<? echo $rowspan;?>" align="right"><p  href='##' onDblClick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('id')];?>','grey_required_popup','', '' )"><? echo number_format($grey_required,2,".","");?></p></td>

						<td class="alignment_css" width="100" rowspan="<? echo $rowspan;?>" align="right"><p  href='##' onDblClick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('id')];?>','grey_receive_popup','', '' )"><? echo number_format($grey_production_qnty,2,".","");?></p></td>

						<td class="alignment_css" width="100" rowspan="<? echo $rowspan;?>" align="right"><? echo number_format($knitting_balance,2,".","");?></td>
						 
						<td class="alignment_css" width="100" rowspan="<? echo $rowspan;?>" align="right"><p  href='##' onDblClick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('id')];?>_9','grey_purchase_popup','','')"><? echo number_format($grey_receive_qnty,2,".","");?></p></td>
						<td class="alignment_css" width="50" rowspan="<? echo $rowspan;?>" align="right"><p  href='##' onDblClick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('id')];?>','yarn_trans_in_popup','','')"><? echo number_format($transfer_in_qnty,2,".","");?></p></td>
						<td class="alignment_css" width="50" rowspan="<? echo $rowspan;?>" align="right"><p  href='##' onDblClick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('id')];?>','yarn_trans_out_popup','','')"><? echo number_format($transfer_out_qnty,2,".","");?></p></td>
						<td class="alignment_css" width="100" rowspan="<? echo $rowspan;?>" align="right"><? echo number_format($grey_receive_balance,2,".","");?></td>
						<td class="alignment_css" width="100" rowspan="<? echo $rowspan;?>" align="right"><p  href='##' onDblClick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('id')];?>_2','grey_issue_popup','','')"><? echo number_format($grey_issue_without_return,2,".","");?></p></td>
						<td class="alignment_css" width="100" rowspan="<? echo $rowspan;?>" align="right"><? echo number_format($grey_in_hand,2,".","");?></td>

						<?
						$total_transfer_out_qnty+=$transfer_out_qnty;
						$t= 0;

						foreach (array_unique(explode(",",trim($pub_shipment_date_wise_color[$row[csf('id')]][$pub_shipment_date][$row[csf('booking_no')]],",") )) as $key=>$color_id)
						{
							//$color_id=$grey_fabric_row['color_id'];
							$all_array_info2[$sales_id]["po_qty"]=$color_wise_qnty_arr[$row[csf("id")]][$color_id];
							$po_qnty2=$date_color_wise_qnty_arr[$row[csf("id")]][$pub_shipment_date][$color_id];


							$batch_qnty = $batch_arr[$row[csf("id")]][$color_id];
							$batch_qnty=($batch_qnty/$all_array_info2[$sales_id]["po_qty"])*$po_qnty2;
							$dye_qnty = $dye_qnty_arr[$row[csf("id")]][$color_id];
							$dye_qnty=($dye_qnty/$all_array_info2[$sales_id]["po_qty"])*$po_qnty2;
							if(!$color_wise_qnty_arr[$row[csf("id")]][$color_id] ) 
							{
								//echo "string";
								//$color_gmts_arr=array_unique(explode(",",trim($pub_shipment_date_wise_color2[$row[csf('id')]][$pub_shipment_date][$row[csf('booking_no')]][$color_id],",") ));
								$color_gmts_arr=array_unique(explode(",",trim($pub_shipment_date_wise_color3[$row[csf('id')]][$row[csf('booking_no')]][$color_id],",") ));

								//print_r($color_gmts_arr); 
								$all_array_info2[$sales_id]["po_qty"]=0;
								$po_qnty2=0;
								foreach($color_gmts_arr as $color_gmts) 
								{
									//echo  $color_wise_qnty_arr[$row[csf("id")]][$color_gmts] ."string ";
									$all_array_info2[$sales_id]["po_qty"]+=$color_wise_qnty_arr[$row[csf("id")]][$color_gmts];
									$po_qnty2+=$date_color_wise_qnty_arr[$row[csf("id")]][$pub_shipment_date][$color_gmts];

								}
								//echo "ddd".$all_array_info2[$sales_id]["po_qty"];
								
								$batch_qnty = $batch_arr[$row[csf("id")]][$color_id];

								if($batch_qnty !=""){
									$batch_qnty=($batch_qnty/$all_array_info2[$sales_id]["po_qty"])*$po_qnty2;
								}else{
									$batch_qnty=0;
								}
								//$batch_qnty=($batch_qnty/$all_array_info2[$sales_id]["po_qty"])*$po_qnty2;

								$dye_qnty = $dye_qnty_arr[$row[csf("id")]][$color_id];
								if($dye_qnty !=""){
									$dye_qnty=($dye_qnty/$all_array_info2[$sales_id]["po_qty"])*$po_qnty2;
								}else{
									$dye_qnty=0;
								}
								

							}
							
							//if(!$batch_qnty)continue;
	    					//$yet_to_batch = $grey_issue_without_return - $batch_qnty;
	    					//echo $all_array_info2[$sales_id]["po_qty"]."<br>";

							if($sales_fin_qnty_arr[$row[csf("id")]][$color_id][12] && $all_array_info2[$sales_id]["po_qty"] && $po_qnty2)
							{
								$fin_req_qnty_kg = ($sales_fin_qnty_arr[$row[csf("id")]][$color_id][12]/$all_array_info2[$sales_id]["po_qty"])*$po_qnty2;
							}else{
								$fin_req_qnty_kg=0;
							}

							if($sales_fin_qnty_arr[$row[csf("id")]][$color_id][27] && $all_array_info2[$sales_id]["po_qty"] && $po_qnty2)
							{
								$fin_req_qnty_yds = ($sales_fin_qnty_arr[$row[csf("id")]][$color_id][27]/$all_array_info2[$sales_id]["po_qty"])*$po_qnty2;
							}else{
								$fin_req_qnty_yds =0;
							}
							
							
							$dye_bal = $batch_qnty - $dye_qnty;
							$fin_qnty_kg = number_format( ($finish_arr[$row[csf("id")]][$color_id][12]['fin_production_qnty']/$all_array_info2[$sales_id]["po_qty"])*$po_qnty2, 2, '.', '');
							$fin_qnty_yds = number_format(($finish_arr[$row[csf("id")]][$color_id][27]['fin_production_qnty']/$all_array_info2[$sales_id]["po_qty"])*$po_qnty2, 2, '.', '');
							$fin_bal_kg = number_format(($fin_req_qnty_kg - $fin_qnty_kg), 2, '.', '');
							$fin_bal_yds = number_format(($fin_req_qnty_yds - $fin_qnty_yds), 2, '.', '');

							
							//$grey_issue_qnty = ($grey_issue_qnty_arr[$row[csf('id')]]/$all_array_info[$sales_id]["po_qty"])*$po_qnty;
							//echo $all_array_info[$sales_id]["po_qty"]."*".$po_qnty."<br>";

							$fin_delivery_qnty_kg = number_format(($finDeliveryArray[$row[csf("id")]][$color_id][12]/$all_array_info[$sales_id]["po_qty"])*$po_qnty,2,'.','');
							$fin_delivery_qnty_yds = number_format(($finDeliveryArray[$row[csf("id")]][$color_id][27]/$all_array_info[$sales_id]["po_qty"])*$po_qnty,2,'.','');

							$fin_prod_floor = number_format(($fin_qnty- $fin_delivery_qnty),2,'.','');

							$fin_receive_return_qnty_kg = ($finish_arr[$row[csf('id')]][$color_id][12]["fin_receive_return_qnty"]/$all_array_info[$sales_id]["po_qty"])*$po_qnty;
							$fin_receive_return_qnty_yds = ($finish_arr[$row[csf('id')]][$color_id][27]["fin_receive_return_qnty"]/$all_array_info[$sales_id]["po_qty"])*$po_qnty;
							$fin_receive_qnty_kg = ($finish_arr[$row[csf('id')]][$color_id][12]["fin_receive_qnty"]/$all_array_info[$sales_id]["po_qty"])*$po_qnty - $fin_receive_return_qnty_kg;
							$fin_receive_qnty_yds = ($finish_arr[$row[csf('id')]][$color_id][27]["fin_receive_qnty"]/$all_array_info[$sales_id]["po_qty"])*$po_qnty - $fin_receive_return_qnty_yds;

							$fin_trans_in_qnty_kg = ($finish_arr[$row[csf('id')]][$color_id][12]["fin_trans_in_qnty"]/$all_array_info[$sales_id]["po_qty"])*$po_qnty;
							$fin_trans_in_qnty_yds = ($finish_arr[$row[csf('id')]][$color_id][27]["fin_trans_in_qnty"]/$all_array_info[$sales_id]["po_qty"])*$po_qnty;
							$fin_trans_out_qnty_kg = ($finish_arr[$row[csf('id')]][$color_id][12]["fin_trans_out_qnty"]/$all_array_info[$sales_id]["po_qty"])*$po_qnty;
							$fin_trans_out_qnty_yds = ($finish_arr[$row[csf('id')]][$color_id][27]["fin_trans_out_qnty"]/$all_array_info[$sales_id]["po_qty"])*$po_qnty;

							$fin_delivery_qnty_balance_kg =$fin_delivery_qnty_kg-$fin_qnty_kg;
							$fin_delivery_qnty_balance_yds =$fin_delivery_qnty_yds-$fin_qnty_yds;

							$fin_issue_qnty_kg = ($finish_arr[$row[csf('id')]][$color_id][12]["fin_issue_qnty"]/$all_array_info[$sales_id]["po_qty"])*$po_qnty - ($finish_arr[$row[csf('id')]][$color_id][12]["fin_issue_ret_qnty"]/$all_array_info[$sales_id]["po_qty"])*$po_qnty;
							$fin_issue_qnty_yds = ($finish_arr[$row[csf('id')]][$color_id][27]["fin_issue_qnty"]/$all_array_info[$sales_id]["po_qty"])*$po_qnty - ($finish_arr[$row[csf('id')]][$color_id][27]["fin_issue_ret_qnty"]/$all_array_info[$sales_id]["po_qty"])*$po_qnty;
							$fin_issue_qnty_kg = is_nan($fin_issue_qnty_kg) ? 0 : $fin_issue_qnty_kg;
							$fin_issue_qnty_yds = is_nan($fin_issue_qnty_yds) ? 0 : $fin_issue_qnty_yds;
							$fin_trans_out_qnty_yds = is_nan($fin_trans_out_qnty_yds) ? 0 : $fin_trans_out_qnty_yds;
							$fin_trans_out_qnty_kg = is_nan($fin_trans_out_qnty_kg) ? 0 : $fin_trans_out_qnty_kg;
							$fin_trans_in_qnty_kg = is_nan($fin_trans_in_qnty_kg) ? 0 : $fin_trans_in_qnty_kg;
							$fin_trans_in_qnty_yds = is_nan($fin_trans_in_qnty_yds) ? 0 : $fin_trans_in_qnty_yds;
							$fin_receive_qnty_yds = is_nan($fin_receive_qnty_yds) ? 0 : $fin_receive_qnty_yds;
							$fin_receive_qnty_kg = is_nan($fin_receive_qnty_kg) ? 0 : $fin_receive_qnty_kg;


							$fin_gmts_rcv_qnty_kg=$fin_gmts_rcv_qnty_yds=$fin_gmts_trans_in_qnty_kg=$fin_gmts_trans_in_qnty_yds=$fin_gmts_trans_out_qnty_kg=$fin_gmts_trans_out_qnty_yds=0;

							//$po_ids_arr = array_unique(array_merge($garments_trans_in_order,$po_ids_arr));
							foreach($po_ids_arr as $id_val)
							{
								$fin_gmts_rcv_qnty_kg +=$finish_arr[$id_val][$color_id][12]["fin_gmts_rcv_qnty"]-$finish_arr[$id_val][$color_id][12]["fin_gmts_rcv_rtn_qnty"];
								$fin_gmts_rcv_qnty_yds +=$finish_arr[$id_val][$color_id][27]["fin_gmts_rcv_qnty"]-$finish_arr[$id_val][$color_id][27]["fin_gmts_rcv_rtn_qnty"];

								$fin_gmts_trans_in_qnty_kg +=$finish_arr[$id_val][$color_id][12]["fin_gmts_trans_in_qnty"];
								$fin_gmts_trans_in_qnty_yds +=$finish_arr[$id_val][$color_id][27]["fin_gmts_trans_in_qnty"];

								$fin_gmts_trans_out_qnty_kg +=$finish_arr[$id_val][$color_id][12]["fin_gmts_trans_out_qnty"];
								$fin_gmts_trans_out_qnty_yds +=$finish_arr[$id_val][$color_id][27]["fin_gmts_trans_out_qnty"];
							}

							//$fin_gmts_rcv_qnty_kg= ($fin_gmts_rcv_qnty_kg/$all_array_info[$sales_id]["po_qty"])*$po_qnty;
							//$fin_gmts_rcv_qnty_yds = ($fin_gmts_rcv_qnty_yds/$all_array_info[$sales_id]["po_qty"])*$po_qnty;

							//$fin_gmts_trans_in_qnty_kg = ($fin_gmts_trans_in_qnty_kg/$all_array_info[$sales_id]["po_qty"])*$po_qnty;
							//$fin_gmts_trans_in_qnty_yds = ($fin_gmts_trans_in_qnty_yds/$all_array_info[$sales_id]["po_qty"])*$po_qnty;

							//$fin_gmts_trans_out_qnty_kg = ($fin_gmts_trans_out_qnty_kg/$all_array_info[$sales_id]["po_qty"])*$po_qnty;
							//$fin_gmts_trans_out_qnty_yds = ($fin_gmts_trans_out_qnty_yds/$all_array_info[$sales_id]["po_qty"])*$po_qnty;

							$fin_gmts_delivery_bal_kg = $fin_gmts_rcv_qnty_kg -$fin_issue_qnty_kg;
							$fin_gmts_delivery_bal_kg = is_nan($fin_gmts_delivery_bal_kg) ? 0 : $fin_gmts_delivery_bal_kg;
							$fin_gmts_delivery_bal_yds = $fin_gmts_rcv_qnty_yds -$fin_issue_qnty_yds;
							$fin_gmts_delivery_bal_yds = is_nan($fin_gmts_delivery_bal_yds) ? 0 : $fin_gmts_delivery_bal_yds;

							?>
							<td class="alignment_css" width="100" align="center"><? echo $color_array[$color_id]; ?></td>
							<td class="alignment_css" width="100" align="right">
							<p href='##' onDblClick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('id')];?>','batch_popup','','<? echo $color_id;?>')">
								<?
									echo number_format($batch_qnty,2,".","");
								?>
							</p>
							</td>

							<?
							if($t==0)
							{
								$yet_to_batch = $grey_issue_without_return - $array_for_yet_to_batch[$sales_id][$pub_shipment_date];
								if($yet_to_batch != 0) $yet_to_batch = number_format($yet_to_batch,2); else $yet_to_batch ="";
								?>
								<td class="alignment_css" width="100" rowspan="<? echo $rowspan;?>" align="right"><? echo number_format($yet_to_batch,2,".","");?></td>
								<?
							}
							?>

							<td class="alignment_css" width="100" align="right"><p  href='##' onDblClick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('id')];?>','dyeing_popup','<? echo $color_id;?>','')"><? echo number_format($dye_qnty,2,".","");?></p></td>

							<td class="alignment_css" width="100" align="right"><? echo number_format($dye_bal,2,".","");?></td>
							<td class="alignment_css" width="100" align="right"><? echo number_format($fin_req_qnty_kg,2,".","");?></td>
							<td class="alignment_css" width="100" align="right"><? echo number_format($fin_req_qnty_yds,2,".","");?></td>

							<td class="alignment_css" width="100" align="right" title="<? echo $finish_arr[$row[csf("id")]][$color_id][12]['fin_production_qnty'];?>"><p  href='##' onDblClick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('id')];?>','fabric_receive','<? echo $color_id;?>','')"><? echo number_format($fin_qnty_kg,2,".","");?></p></td>
							<td class="alignment_css" width="100" align="right"><p  href='##' onDblClick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('id')];?>','fabric_receive','<? echo $color_id;?>','')"><? echo number_format($fin_qnty_yds,2,".","");?></p></td>

							<td class="alignment_css" width="100" align="right"><? echo number_format($fin_bal_kg,2,".","");?></td>
							<td class="alignment_css" width="100" align="right"><? echo number_format($fin_bal_yds,2,".","");?></td>

							<td class="alignment_css" width="100" align="right"><? echo number_format($fin_delivery_qnty_kg,2,".","");?></td>
							<td class="alignment_css" width="100" align="right"><? echo number_format($fin_delivery_qnty_yds,2,".","");?></td>
							<td class="alignment_css" width="100" align="right"><? echo number_format($fin_receive_qnty_kg,2,".","");?></td>
							<td class="alignment_css" width="100" align="right"><? echo number_format($fin_receive_qnty_yds,2,".","");?></td>
							<td class="alignment_css" width="100" align="right"><? echo number_format($fin_trans_in_qnty_kg,2,".","");?></td>
							<td class="alignment_css" width="100" align="right"><? echo number_format($fin_trans_in_qnty_yds,2,".","");?></td>
							<td class="alignment_css" width="100" align="right"><? echo number_format($fin_trans_out_qnty_kg,2,".","");?></td>
							<td class="alignment_css" width="100" align="right"><? echo number_format($fin_trans_out_qnty_yds,2,".","");?></td>
							<td class="alignment_css" width="100" align="right"><? echo number_format($fin_delivery_qnty_balance_kg,2,".","");?></td>
							<td class="alignment_css" width="100" align="right"><? echo number_format($fin_delivery_qnty_balance_yds,2,".","");?></td>
							<td class="alignment_css" width="100" align="right" title="<? echo "(".$finish_arr[$row[csf('id')]][$color_id][12]["fin_issue_qnty"]."/".$all_array_info[$sales_id]["po_qty"].")*".$po_qnty; ?>"><? echo number_format($fin_issue_qnty_kg,2,".","");?></td>
							<td class="alignment_css" width="100" align="right"><? echo number_format($fin_issue_qnty_yds,2,".","");?></td>

							<td class="alignment_css" width="100" align="right"><? echo number_format($fin_gmts_rcv_qnty_kg,2,".","");?></td>
							<td class="alignment_css" width="100" align="right"><? echo number_format($fin_gmts_rcv_qnty_yds,2,".","");?></td>
							<td class="alignment_css" width="100" align="right" title="<? echo implode(',', $po_ids_arr);?>"><? echo number_format($fin_gmts_trans_in_qnty_kg,2,".","");?></td>
							<td class="alignment_css" width="100" align="right"><? echo number_format($fin_gmts_trans_in_qnty_yds,2,".","");?></td>
							<td class="alignment_css" width="100" align="right"><? echo number_format($fin_gmts_trans_out_qnty_kg,2,".","");?></td>
							<td class="alignment_css" width="100" align="right"><? echo number_format($fin_gmts_trans_out_qnty_yds,2,".","");?></td>
							<td class="alignment_css" width="100" align="right"><? echo number_format($fin_gmts_delivery_bal_kg,2,".","");?></td>
							<td class="alignment_css" width="100" align="right"><? echo number_format($fin_gmts_delivery_bal_yds,2,".","");?></td>
						</tr>
						<?
						$t++; $y++;
						$total_batch_qnty += $batch_qnty;
						$total_dye_qnty += $dye_qnty;
						$total_dye_bal += $dye_bal;
						$total_fin_req_qnty_kg += $fin_req_qnty_kg;
						$total_fin_req_qnty_yds += $fin_req_qnty_yds;
						$total_fin_qnty_kg += $fin_qnty_kg;
						$total_fin_qnty_yds += $fin_qnty_yds;
						$total_fin_bal_kg += $fin_bal_kg;
						$total_fin_bal_yds += $fin_bal_yds;
						$total_fin_delivery_qnty += $fin_delivery_qnty;
						$total_fin_prod_floor += $fin_prod_floor;
						$total_fin_receive_qnty += $fin_receive_qnty;
						$total_fin_trans_in_qnty += $fin_trans_in_qnty;
						$total_fin_trans_out_qnty += $fin_trans_out_qnty;
						$total_fin_issue_qnty += $fin_issue_qnty;
						$total_fin_stock_qnty += $fin_stock_qnty;

						$total_fin_delivery_qnty_kg += $fin_delivery_qnty_kg;
						$total_fin_delivery_qnty_yds += $fin_delivery_qnty_yds;
						$total_fin_receive_qnty_kg += $fin_receive_qnty_kg;
						$total_fin_receive_qnty_yds += $fin_receive_qnty_yds;
						$total_fin_trans_in_qnty_kg += $fin_trans_in_qnty_kg;
						$total_fin_trans_in_qnty_yds += $fin_trans_in_qnty_yds;
						$total_fin_trans_out_qnty_kg += $fin_trans_out_qnty_kg;
						$total_fin_trans_out_qnty_yds += $fin_trans_out_qnty_yds;
						$total_fin_delivery_qnty_balance_kg += $fin_delivery_qnty_balance_kg;
						$total_fin_delivery_qnty_balance_yds += $fin_delivery_qnty_balance_yds;
						$total_fin_issue_qnty_kg += $fin_issue_qnty_kg;
						$total_fin_issue_qnty_yds += $fin_issue_qnty_yds;

						$total_fin_gmts_rcv_qnty_kg += $fin_gmts_rcv_qnty_kg;
						$total_fin_gmts_rcv_qnty_yds += $fin_gmts_rcv_qnty_yds;
						$total_fin_gmts_trans_in_qnty_kg += $fin_gmts_trans_in_qnty_kg;
						$total_fin_gmts_trans_in_qnty_yds += $fin_gmts_trans_in_qnty_yds;
						$total_fin_gmts_trans_out_qnty_kg += $fin_gmts_trans_out_qnty_kg;
						$total_fin_gmts_trans_out_qnty_yds += $fin_gmts_trans_out_qnty_yds;
						$total_fin_gmts_delivery_bal_kg += $fin_gmts_delivery_bal_kg;
						$total_fin_gmts_delivery_bal_yds += $fin_gmts_delivery_bal_yds;
					}
					$i++; 
					$total_grey_req_qnty += $grey_required;
					$total_grey_production_qnty +=$grey_production_qnty;
					$total_grey_delivery_qnty += $grey_delivery_qnty;
					$total_grey_in_knit_floor += $grey_in_knit_floor;
					$total_grey_receive_qnty += $grey_receive_qnty;
					$total_grey_trans_in_qnty += $transfer_in_qnty;
					$totol_grey_trans_out_qnty += $transfer_out_qnty;
					$total_grey_receive_balance += $grey_receive_balance;
					$total_grey_issue_without_return += $grey_issue_without_return;
					$total_grey_in_hand += $grey_in_hand;
					$total_receive_by_batch_qnty += $receive_by_batch_qnty;
				}
				?>
			</tbody>
		</table>
	</div>
	<table class="rpt_table" border="1" rules="all" width="6110" cellpadding="1" cellspacing="0" align="left">
		<tfoot>
			<tr>
				<th  class="alignment_css"  width="40" ></th>
				<th  class="alignment_css"  width="165" ></th>
				<th  class="alignment_css"  width="100" ></th>
				<th  class="alignment_css"  width="165" ></th>
				<th  class="alignment_css"  width="100" ></th>
				<th  class="alignment_css"  width="150" ></th>
				<th  class="alignment_css"  width="170" ></th>
				<th  class="alignment_css"  width="180" ></th>
				<th  class="alignment_css"  width="100" align="right" ><? echo number_format($total_po_qnty,0);?></th>
				<th  class="alignment_css"  width="100" ></th>
				<th  class="alignment_css"  width="100" ></th>
				<th class="alignment_css" width="100"><? echo number_format($total_yarn_req_sales,2);?></th>
				<th class="alignment_css" width="100"><? echo number_format($total_yarn_req_requisition,2);?></th>
				<th class="alignment_css" width="100"><? echo number_format($total_yarn_issue,2);?></th>
				<th class="alignment_css" width="100"><? echo number_format($total_yarn_balance,2);?></th>


				<th class="alignment_css" width="280">&nbsp;</th>
				<th class="alignment_css" width="100"><? echo number_format($total_grey_req_qnty,2);?></th>
				<th class="alignment_css" width="100"><? echo number_format($total_grey_production_qnty,2);?></th>
				<th class="alignment_css" width="100">&nbsp;</th>
				 

				<th class="alignment_css" width="100"><? echo number_format($total_grey_receive_qnty,2);?></th>
				<th class="alignment_css" width="50"><? echo number_format($total_grey_trans_in_qnty,2);?></th>
				<th class="alignment_css" width="50"><? echo number_format($total_transfer_out_qnty,2);?></th>
				<th class="alignment_css" width="100"><? echo number_format($total_grey_receive_balance,2);?></th>
				<th class="alignment_css" width="100"><? echo number_format($total_grey_issue_without_return,2);?></th>
				<th class="alignment_css" width="100"><? echo number_format($total_grey_in_hand,2);?></th>
				 

				<th class="alignment_css" width="100">&nbsp;</th>
				<th class="alignment_css" width="100"><? echo number_format($total_batch_qnty,2);?></th>
				<th class="alignment_css" width="100">&nbsp;</th>
				<th class="alignment_css" width="100"><? echo number_format($total_dye_qnty,2);?></th>
				<th class="alignment_css" width="100"><? echo number_format($total_dye_bal,2);?></th>


				<th class="alignment_css" width="100"><? echo number_format($total_fin_req_qnty_kg,2);?></th>
				<th class="alignment_css" width="100"><? echo number_format($total_fin_req_qnty_yds,2);?></th>
				<th class="alignment_css" width="100"><? echo number_format($total_fin_qnty_kg,2);?></th>
				<th class="alignment_css" width="100"><? echo number_format($total_fin_qnty_yds,2);?></th>
				<th class="alignment_css" width="100"><? echo number_format($total_fin_bal_kg,2);?></th>
				<th class="alignment_css" width="100"><? echo number_format($total_fin_bal_yds,2);?></th>
				 

				<th class="alignment_css" width="100" rowspan="2"><? echo number_format($total_fin_delivery_qnty_kg,2);?></th>
				<th class="alignment_css" width="100" rowspan="2"><? echo number_format($total_fin_delivery_qnty_yds,2);?></th>
				<th class="alignment_css" width="100" rowspan="2"><? echo number_format($total_fin_receive_qnty_kg,2);?></th>
				<th class="alignment_css" width="100" rowspan="2"><? echo number_format($total_fin_receive_qnty_yds,2);?></th>
				<th class="alignment_css" width="100" rowspan="2"><? echo number_format($total_fin_trans_in_qnty_kg,2);?></th>
				<th class="alignment_css" width="100" rowspan="2"><? echo number_format($total_fin_trans_in_qnty_yds,2);?></th>
				<th class="alignment_css" width="100" rowspan="2"><? echo number_format($total_fin_trans_out_qnty_kg,2);?></th>
				<th class="alignment_css" width="100" rowspan="2"><? echo number_format($total_fin_trans_out_qnty_yds,2);?></th>
				<th class="alignment_css" width="100" rowspan="2"><? echo number_format($total_fin_delivery_qnty_balance_kg,2);?></th>
				<th class="alignment_css" width="100" rowspan="2"><? echo number_format($total_fin_delivery_qnty_balance_yds,2);?></th>
				<th class="alignment_css" width="100" rowspan="2"><? echo number_format($total_fin_issue_qnty_kg,2);?></th>
				<th class="alignment_css" width="100" rowspan="2"><? echo number_format($total_fin_issue_qnty_yds,2);?></th>

				<th class="alignment_css" width="100" rowspan="2"><? echo number_format($total_fin_gmts_rcv_qnty_kg,2);?></th>
				<th class="alignment_css" width="100" rowspan="2"><? echo number_format($total_fin_gmts_rcv_qnty_yds,2);?></th>
				<th class="alignment_css" width="100" rowspan="2"><? echo number_format($total_fin_gmts_trans_in_qnty_kg,2);?></th>
				<th class="alignment_css" width="100" rowspan="2"><? echo number_format($total_fin_gmts_trans_in_qnty_yds,2);?></th>
				<th class="alignment_css" width="100" rowspan="2"><? echo number_format($total_fin_gmts_trans_out_qnty_kg,2);?></th>
				<th class="alignment_css" width="100" rowspan="2"><? echo number_format($total_fin_gmts_trans_out_qnty_yds,2);?></th>
				<th class="alignment_css" width="100" rowspan="2"><? echo number_format($total_fin_gmts_delivery_bal_kg,2);?></th>
				<th class="alignment_css" width="100" rowspan="2"><? echo number_format($total_fin_gmts_delivery_bal_yds,2);?></th>
			</tr>
		</tfoot>
	</table>
	<?
	$html = ob_get_contents();
	ob_clean();
	foreach (glob("$user_name*.xls") as $filename) {
		@unlink($filename);

	}
    //---------end------------//
	$name=time();
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html####$filename";
}


 
if ($action == "yarn_requisition_popup") {
	echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
	page_style();
	extract($_REQUEST);
	$brand_array = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$order_id = explode("****", $order_id);
	?>
	<script>
		function print_window() {
			//document.getElementById('scroll_body').style.overflow = "auto";
			//document.getElementById('scroll_body').style.maxHeight = "none";
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');
			d.close();
			//document.getElementById('scroll_body').style.overflowY = "scroll";
			//document.getElementById('scroll_body').style.maxHeight = "230px";
		}
	</script>
	<div style="width:870px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
		style="width:100px" class="formbutton"/></div>
		<fieldset style="width:865px; margin-left:3px">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
					<thead>
						<th colspan="11"><b>Yarn Requisition</b></th>
					</thead>
					<thead>
						<th width="105">SL</th>
						<th width="105">Booking No</th>
						<th width="80">Requisition No</th>
						<th width="75">Requisition Date</th>
						<th width="70">Brand</th>
						<th width="200">Yarn Description</th>
						<th width="60">Lot No</th>
						<th width="80">Yarn Type</th>
						<th width="90">Requisition Qnty</th>
					</thead>
					<?
					$i = 1;
					$total_yarn_issue_qnty = 0;
					$total_yarn_issue_qnty_out = 0;
					if($order_id[1] == 1)
					{
						$sql = "select a.dtls_id,a.booking_no, a.determination_id,b.knit_id,b.requisition_no,b.requisition_date,b.yarn_qnty,a.po_id,b.id,b.prod_id,c.product_name_details,yarn_type,c.lot,c.brand from ppl_planning_entry_plan_dtls a inner join ppl_yarn_requisition_entry b on a.dtls_id = b.knit_id inner join product_details_master c on b.prod_id=c.id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.requisition_no in($order_id[0]) and a.determination_id=$yarn_count";
					}
					else
					{
						$sql = "select a.dtls_id,a.booking_no, a.determination_id,b.knit_id,b.requisition_no,b.requisition_date,b.yarn_qnty,a.po_id,b.id,b.prod_id,c.product_name_details,yarn_type,c.lot,c.brand from ppl_planning_entry_plan_dtls a inner join ppl_yarn_requisition_entry b on a.dtls_id = b.knit_id inner join product_details_master c on b.prod_id=c.id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.requisition_no in($order_id[0]) and c.yarn_type=$yarn_type_id and c.yarn_comp_percent1st = $yarn_comp_percent1st and c.yarn_comp_type1st = $yarn_comp_type1st and c.yarn_count_id=$yarn_count";
					}

					$result = sql_select($sql);
					foreach ($result as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";

						$issue_to = "";
						if ($row[csf('knit_dye_source')] == 1) {
							$issue_to = $company_library[$row[csf('knit_dye_company')]];
						} else {
							$issue_to = $supplier_details[$row[csf('knit_dye_company')]];
						}

						$yarn_issued = $row[csf('issue_qnty')];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
							<td width="105" class="center"><? echo $i; ?></td>
							<td width="105" class="center"><? echo $row[csf('booking_no')]; ?></td>
							<td width="80" class="center"><? echo $row[csf('requisition_no')]; ?></td>
							<td width="75" class="center"><? echo change_date_format($row[csf('requisition_date')]); ?></td>
							<td width="70" class="center"><? echo $brand_array[$row[csf('brand')]]; ?></td>
							<td width="60" class="center"><? echo $row[csf('product_name_details')]; ?></td>
							<td width="60" class="center"><? echo $row[csf('lot')]; ?></td>
							<td width="80" class="center"><? echo $yarn_type[$row[csf('yarn_type')]]; ?></td>
							<td align="right" width="90"><? echo $row[csf('yarn_qnty')]; ?></td>
						</tr>
						<?
						$total_req_qnty += $row[csf('yarn_qnty')];
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
						<td align="right"><? echo number_format($total_req_qnty, 2); ?></td>
					</tr>
				</table>
			</div>
		</fieldset>
		<?
		exit();
	}

	if ($action == "yarn_issue_popup") {
		echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
		page_style();
		extract($_REQUEST);
		$brand_array = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
		$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
		$supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
		$order_id = explode('_', $order_id);
		?>
		<script>
			function print_window() {
				document.getElementById('scroll_body').style.overflow = "auto";
				document.getElementById('scroll_body').style.maxHeight = "none";
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
					'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');
				d.close();
				document.getElementById('scroll_body').style.overflowY = "scroll";
				document.getElementById('scroll_body').style.maxHeight = "230px";
			}
		</script>
		<div style="width:870px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
			style="width:100px" class="formbutton"/></div>
			<fieldset style="width:865px; margin-left:3px">
				<div id="report_container">

					<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
						<thead>
							<th colspan="11"><b>Yarn Issue</b></th>
						</thead>
						<thead>
							<th width="105">Issue Id</th>
							<th width="90">Issue To</th>
							<th width="105">Booking No</th>
							<th width="80">Challan No</th>
							<th width="70">Brand</th>
							<th width="200">Yarn Description</th>
							<th width="60">Lot No</th>
							<th width="75">Issue Date</th>
							<th width="80">Yarn Type</th>
							<th width="90">Issue Qnty (In)</th>
							<th>Issue Qnty (Out)</th>
						</thead>
						<?
						$i = 1;
						$total_yarn_issue_qnty = 0;
						$total_yarn_issue_qnty_out = 0;
						$sql = "select a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, e.sales_booking_no booking_no, sum(b.quantity) as issue_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id from inv_issue_master a, order_wise_pro_details b, product_details_master c, inv_transaction d,fabric_sales_order_mst e where a.id=d.mst_id and b.po_breakdown_id=e.id and d.transaction_type=2 and d.item_category=1 and c.item_category_id=1 and d.id=b.trans_id and b.trans_type=2 and b.entry_form=3 and a.id in ($order_id[0]) and d.requisition_no in($order_id[1]) and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2 group by a.id, c.id, a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, e.sales_booking_no, c.lot, c.yarn_type, c.product_name_details, d.brand_id";
						$result = sql_select($sql);
						foreach ($result as $row) {
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";

							$issue_to = "";
							if ($row[csf('knit_dye_source')] == 1) {
								$issue_to = $company_library[$row[csf('knit_dye_company')]];
							} else {
								$issue_to = $supplier_details[$row[csf('knit_dye_company')]];
							}

							$yarn_issued = $row[csf('issue_qnty')];
							?>
							<tr bgcolor="<? echo $bgcolor; ?>"
								onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="105" class="center"><? echo $row[csf('issue_number')]; ?></td>
								<td width="90" class="center"><? echo $issue_to; ?></td>
								<td width="105" class="center"><? echo $row[csf('booking_no')]; ?></td>
								<td width="80" class="center"><? echo $row[csf('challan_no')]; ?></td>
								<td width="70" class="center"><? echo $brand_array[$row[csf('brand_id')]]; ?></td>
								<td width="60" class="center"><? echo $row[csf('product_name_details')]; ?></td>
								<td width="60" class="center"><? echo $row[csf('lot')]; ?></td>
								<td width="75" class="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
								<td width="80" class="center"><? echo $yarn_type[$row[csf('yarn_type')]]; ?></td>
								<td align="right" width="90">
									<?
									if ($row[csf('knit_dye_source')] != 3) {
										echo number_format($yarn_issued, 2);
										$total_yarn_issue_qnty += $yarn_issued;
									} else echo "&nbsp;";
									?>
								</td>
								<td align="right">
									<?
									if ($row[csf('knit_dye_source')] == 3) {
										echo number_format($yarn_issued, 2);
										$total_yarn_issue_qnty_out += $yarn_issued;
									} else echo "&nbsp;";
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
							<td></td>
							<td align="right">Total</td>
							<td align="right"><? echo number_format($total_yarn_issue_qnty, 2); ?></td>
							<td align="right"><? echo number_format($total_yarn_issue_qnty_out, 2); ?></td>
						</tr>
						<tr style="font-weight:bold">
							<td align="right" colspan="10">Issue Total</td>
							<td align="right"><? echo number_format($total_yarn_issue_qnty + $total_yarn_issue_qnty_out, 2); ?></td>
						</tr>
						<thead>
							<th colspan="11"><b>Yarn Return</b></th>
						</thead>
						<thead>
							<th width="105">Return Id</th>
							<th width="90">Return From</th>
							<th width="105">Booking No</th>
							<th width="80">Challan No</th>
							<th width="70">Brand</th>
							<th width="200">Yarn Description</th>
							<th width="60">Lot No</th>
							<th width="75">Return Date</th>
							<th width="80">Yarn Type</th>
							<th width="90">Return Qnty (In)</th>
							<th>Return Qnty (Out)</th>
						</thead>
						<?
						$total_yarn_return_qnty = 0;
						$total_yarn_return_qnty_out = 0;
					//$issue_ids = return_field_value("listagg(mst_id ,',') within group (order by mst_id) as mst_id","inv_transaction", "requisition_no=$order_id[1]","mst_id");

						$sql = "select a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, sum(b.quantity) as returned_qnty, c.lot, c.product_name_details,c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d,fabric_sales_order_mst e where a.id=d.mst_id and b.po_breakdown_id=e.id and d.transaction_type=4 and c.item_category_id=1 and d.item_category=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and b.prod_id=c.id and d.issue_id in($order_id[0]) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2 group by a.id, c.id, a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, c.lot,c.product_name_details, c.yarn_type, c.product_name_details, d.brand_id";
						$result = sql_select($sql);
						foreach ($result as $row) {
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";

							$return_from = "";
							if ($row[csf('knitting_source')] == 1) {
								$return_from = $company_library[$row[csf('knitting_company')]];
							} else {
								$return_from = $supplier_details[$row[csf('knitting_company')]];
							}

							$yarn_returned = $row[csf('returned_qnty')];
							?>
							<tr bgcolor="<? echo $bgcolor; ?>"
								onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="105"><p><? echo $row[csf('recv_number')]; ?></p></td>
								<td width="90"><p><? echo $return_from; ?></p></td>
								<td width="105"><p><? echo $row[csf('booking_no')]; ?></p></td>
								<td width="80"><p><? echo $row[csf('challan_no')]; ?></p></td>
								<td width="70"><p><? echo $brand_array[$row[csf('brand_id')]]; ?></p></td>
								<td width="60"><p><? echo $row[csf('product_name_details')]; ?></p></td>
								<td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
								<td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
								<td width="80"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
								<td align="right" width="90">
									<?
									if ($row[csf('knitting_source')] != 3) {
										echo number_format($yarn_returned, 2);
										$total_yarn_return_qnty += $yarn_returned;
									} else echo "&nbsp;";
									?>
								</td>
								<td align="right">
									<?
									if ($row[csf('knitting_source')] == 3) {
										echo number_format($yarn_returned, 2);
										$total_yarn_return_qnty_out += $yarn_returned;
									} else echo "&nbsp;";
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
							<td></td>
							<td align="right">Balance</td>
							<td align="right"><? echo number_format($total_yarn_issue_qnty - $total_yarn_return_qnty, 2); ?></td>
							<td align="right"><? echo number_format($total_yarn_issue_qnty_out - $total_yarn_return_qnty_out, 2); ?></td>
						</tr>
						<tfoot>
							<tr>
								<th align="right" colspan="10">Total Balance</th>
								<th align="right"><? echo number_format(($total_yarn_issue_qnty + $total_yarn_issue_qnty_out) - ($total_yarn_return_qnty + $total_yarn_return_qnty_out), 2); ?></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</fieldset>
			<?
			exit();
		}


		if ($action == "yarn_issue_popup_for_report_2") {
			echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
			page_style();
			extract($_REQUEST);
			$brand_array = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
			$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
			$supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");

			?>
			<script>
				function print_window() {
				//document.getElementById('scroll_body').style.overflow = "auto";
				//document.getElementById('scroll_body').style.maxHeight = "none";
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
					'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');
				d.close();
				//document.getElementById('scroll_body').style.overflowY = "scroll";
				//document.getElementById('scroll_body').style.maxHeight = "230px";
			}
		</script>
		<div style="width:870px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
			style="width:100px" class="formbutton"/></div>
			<fieldset style="width:865px; margin-left:3px">
				<div id="report_container">

					<table border="1" class="rpt_table" rules="all" width="960" cellpadding="0" cellspacing="0">
						<thead>
							<th colspan="12"><b>Yarn Issue</b></th>
						</thead>
						<thead>
							<th width="105">Issue Id</th>
							<th width="90">Issue To</th>
							<th width="105">Booking No</th>
							<th width="80">Challan No</th>
							<th width="70">Brand</th>
							<th width="200">Yarn Description</th>
							<th width="60">Lot No</th>
							<th width="75">Issue Date</th>
							<th width="80">Yarn Type</th>
							<th width="90">Issue Qnty (In)</th>
							<th width="90">Issue Qnty (Out)</th>
							<th >Returnable Qnty</th>
						</thead>
						<?
						$i = 1;
						$total_yarn_issue_qnty = 0;
						$total_yarn_issue_qnty_out = 0;

						$sql = " select a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, e.sales_booking_no booking_no, sum(b.quantity) as issue_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id, sum(b.returnable_qnty) as returnable_qnty from inv_issue_master a,order_wise_pro_details b, product_details_master c, inv_transaction d,fabric_sales_order_mst e where a.id=d.mst_id and b.po_breakdown_id=e.id and d.transaction_type=2 and d.item_category=1 and c.item_category_id=1 and d.id=b.trans_id and b.trans_type=2 and b.entry_form=3 and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2 and b.po_breakdown_id = $order_id and c.yarn_count_id = $yarn_count and c.yarn_type = $yarn_type_id and c.yarn_comp_percent1st= $yarn_comp_percent1st and c.yarn_comp_type1st = $yarn_comp_type1st group by a.id, c.id, a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, e.sales_booking_no, c.lot, c.yarn_type, c.product_name_details, d.brand_id";
						$result = sql_select($sql);
						foreach ($result as $row) {
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";

							$issue_to = "";
							if ($row[csf('knit_dye_source')] == 1) {
								$issue_to = $company_library[$row[csf('knit_dye_company')]];
							} else {
								$issue_to = $supplier_details[$row[csf('knit_dye_company')]];
							}

							$yarn_issued = $row[csf('issue_qnty')];
							?>
							<tr bgcolor="<? echo $bgcolor; ?>"
								onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="105" class="center"><? echo $row[csf('issue_number')]; ?></td>
								<td width="90" class="center"><? echo $issue_to; ?></td>
								<td width="105" class="center"><? echo $row[csf('booking_no')]; ?></td>
								<td width="80" class="center"><? echo $row[csf('challan_no')]; ?></td>
								<td width="70" class="center"><? echo $brand_array[$row[csf('brand_id')]]; ?></td>
								<td width="60" class="center"><? echo $row[csf('product_name_details')]; ?></td>
								<td width="60" class="center"><? echo $row[csf('lot')]; ?></td>
								<td width="75" class="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
								<td width="80" class="center"><? echo $yarn_type[$row[csf('yarn_type')]]; ?></td>

								<td align="right" width="90">
									<?
									if ($row[csf('knit_dye_source')] != 3) {
										echo number_format($yarn_issued, 2);
										$total_yarn_issue_qnty += $yarn_issued;
									} else echo "&nbsp;";
									?>
								</td>
								<td align="right" width="90">
									<?
									if ($row[csf('knit_dye_source')] == 3) {
										echo number_format($yarn_issued, 2);
										$total_yarn_issue_qnty_out += $yarn_issued;
									} else echo "&nbsp;";
									?>
								</td>
								<td  class="center"><? echo $row[csf('returnable_qnty')]; $total_returnable_qnty += $row[csf('returnable_qnty')]; ?></td>
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
							<td></td>
							<td align="right">Total</td>
							<td align="right"><? echo number_format($total_yarn_issue_qnty, 2); ?></td>
							<td align="right"><? echo number_format($total_yarn_issue_qnty_out, 2); ?></td>
							<td align="right"><? echo number_format($total_returnable_qnty, 2); ?></td>
						</tr>
						<tr style="font-weight:bold">
							<td align="right" colspan="9">Issue Total</td>
							<td align="right"><? echo number_format($total_yarn_issue_qnty + $total_yarn_issue_qnty_out, 2); ?></td>
						</tr>
						<thead>
							<th colspan="12"><b>Yarn Return</b></th>
						</thead>
						<thead>
							<th width="105">Return Id</th>
							<th width="90">Return From</th>
							<th width="105">Booking No</th>
							<th width="80">Challan No</th>
							<th width="70">Brand</th>
							<th width="200">Yarn Description</th>
							<th width="60">Lot No</th>
							<th width="75">Return Date</th>
							<th width="80">Yarn Type</th>
							<th width="90">Return Qnty (In)</th>
							<th>Return Qnty (Out)</th>
						</thead>
						<?
						$total_yarn_return_qnty = 0;
						$total_yarn_return_qnty_out = 0;

						$sql = "select a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, sum(b.quantity) as returned_qnty, c.lot, c.product_name_details,c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id , e.id from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d,fabric_sales_order_mst e where a.id=d.mst_id and b.po_breakdown_id=e.id and d.transaction_type=4 and c.item_category_id=1 and d.item_category=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and b.prod_id=c.id and c.yarn_count_id=$yarn_count and c.yarn_comp_type1st =$yarn_comp_type1st and c.yarn_type = $yarn_type_id and c.yarn_comp_percent1st = $yarn_comp_percent1st and e.id = $order_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2 group by a.id, c.id, a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, c.lot,c.product_name_details, c.yarn_type, c.product_name_details, d.brand_id, e.id";
						$result = sql_select($sql);
						foreach ($result as $row) {
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";

							$return_from = "";
							if ($row[csf('knitting_source')] == 1) {
								$return_from = $company_library[$row[csf('knitting_company')]];
							} else {
								$return_from = $supplier_details[$row[csf('knitting_company')]];
							}

							$yarn_returned = $row[csf('returned_qnty')];
							?>
							<tr bgcolor="<? echo $bgcolor; ?>"
								onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="105"><p><? echo $row[csf('recv_number')]; ?></p></td>
								<td width="90"><p><? echo $return_from; ?></p></td>
								<td width="105"><p><? echo $row[csf('booking_no')]; ?></p></td>
								<td width="80"><p><? echo $row[csf('challan_no')]; ?></p></td>
								<td width="70"><p><? echo $brand_array[$row[csf('brand_id')]]; ?></p></td>
								<td width="60"><p><? echo $row[csf('product_name_details')]; ?></p></td>
								<td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
								<td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
								<td width="80"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
								<td align="right" width="90">
									<?
									if ($row[csf('knitting_source')] != 3) {
										echo number_format($yarn_returned, 2);
										$total_yarn_return_qnty += $yarn_returned;
									} else echo "&nbsp;";
									?>
								</td>
								<td align="right">
									<?
									if ($row[csf('knitting_source')] == 3) {
										echo number_format($yarn_returned, 2);
										$total_yarn_return_qnty_out += $yarn_returned;
									} else echo "&nbsp;";
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
							<td></td>
							<td align="right">Total</td>
							<td align="right"><? echo number_format($total_yarn_return_qnty, 2); ?></td>
							<td align="right"><? echo number_format($total_yarn_return_qnty_out, 2); ?></td>
						</tr>
						<tfoot>
							<tr>
								<th align="right" colspan="10">Total Balance</th>
								<th align="right"><? echo number_format(($total_yarn_issue_qnty + $total_yarn_issue_qnty_out) - ($total_yarn_return_qnty + $total_yarn_return_qnty_out), 2); ?></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</fieldset>
			<?
			exit();
		}

		if ($action == "yarn_trans_popup") {
			echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
			page_style();
			extract($_REQUEST);
			$po_arr = return_library_array("select id, po_number from wo_po_break_down", "id", "po_number");
			?>
			<script>

				function print_window() {
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
						'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

					d.close();
				}

			</script>
			<div style="width:675px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
				style="width:100px" class="formbutton"/></div>
				<fieldset style="width:700px; margin:auto;">
					<div id="report_container">
						<table border="1" class="rpt_table" rules="all" width="100%" cellpadding="0" cellspacing="0">
							<thead>
								<tr>
									<th colspan="6">Transfer In</th>
								</tr>
								<tr>
									<th width="40">SL</th>
									<th width="115">Transfer Id</th>
									<th width="80">Transfer Date</th>
									<th width="100">From Order</th>
									<th width="170">Item Description</th>
									<th>Transfer Qnty</th>
								</tr>
							</thead>
							<?
							$i = 1;
							$total_trans_in_qnty = 0;
							if($deter_id != "") $deter_id_cond = " and d.detarmination_id=$deter_id"; else $deter_id_cond = "";
							$sql = "select a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id, b.from_prod_id, sum(c.quantity) as transfer_qnty, d.product_name_details from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=13 and a.transfer_criteria=4 and c.trans_type=5 and c.entry_form=133 and c.po_breakdown_id in ($order_id) $deter_id_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id, b.from_prod_id, d.product_name_details";
							$result = sql_select($sql);
							foreach ($result as $row) {
								if ($i % 2 == 0)
									$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>"
									onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="40"><? echo $i; ?></td>
									<td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
									<td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
									<td width="100"><p><? echo $po_arr[$row[csf('from_order_id')]]; ?></p></td>
									<td width="170"><p><? echo $row[csf('product_name_details')]; ?></p></td>
									<td align="right"><? echo number_format($row[csf('transfer_qnty')], 2); ?> </td>
								</tr>
								<?
								$total_trans_in_qnty += $row[csf('transfer_qnty')];
								$i++;
							}
							?>
							<tr style="font-weight:bold">
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right">Total</td>
								<td align="right"><? echo number_format($total_trans_in_qnty, 2); ?></td>
							</tr>
							<thead>
								<tr>
									<th colspan="6">Transfer Out</th>
								</tr>
								<tr>
									<th width="40">SL</th>
									<th width="115">Transfer Id</th>
									<th width="80">Transfer Date</th>
									<th width="100">To Order</th>
									<th width="170">Item Description</th>
									<th>Transfer Qnty</th>
								</tr>
							</thead>
							<?
							$total_trans_out_qnty = 0;
							if($deter_id != "") $deter_id_cond = " and d.detarmination_id=$deter_id"; else $deter_id_cond = "";
							$sql = "select a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, sum(c.quantity) as transfer_qnty, d.product_name_details from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=13 and a.transfer_criteria=4 and c.trans_type=6 and c.entry_form=133 and c.po_breakdown_id in ($order_id) $deter_id_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, d.product_name_details";
							$result = sql_select($sql);
							foreach ($result as $row) {
								if ($i % 2 == 0)
									$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>"
									onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="40"><? echo $i; ?></td>
									<td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
									<td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
									<td width="100"><p><? echo $po_arr[$row[csf('to_order_id')]]; ?></p></td>
									<td width="170"><p><? echo $row[csf('product_name_details')]; ?></p></td>
									<td align="right"><? echo number_format($row[csf('transfer_qnty')], 2); ?> </td>
								</tr>
								<?
								$total_trans_out_qnty += $row[csf('transfer_qnty')];
								$i++;
							}
							?>
							<tr style="font-weight:bold">
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right">Total</td>
								<td align="right"><? echo number_format($total_trans_out_qnty, 2); ?></td>
							</tr>
							<tfoot>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th>Net Transfer</th>
								<th><? echo number_format($total_trans_in_qnty - $total_trans_out_qnty, 2); ?></th>
							</tfoot>
						</table>
					</div>
				</fieldset>
				<?
				exit();
			}

			if ($action == "yarn_trans_in_popup") {
				echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
				page_style();
				extract($_REQUEST);
				$po_arr = return_library_array("select id, po_number from wo_po_break_down", "id", "po_number");
				?>
				<script>

					function print_window() {
						var w = window.open("Surprise", "#");
						var d = w.document.open();
						d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
							'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

						d.close();
					}

				</script>
				<div style="width:675px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
					style="width:100px" class="formbutton"/></div>
					<fieldset style="width:700px; margin:auto;">
						<div id="report_container">
							<table border="1" class="rpt_table" rules="all" width="100%" cellpadding="0" cellspacing="0">
								<thead>
									<tr>
										<th colspan="6">Transfer In</th>
									</tr>
									<tr>
										<th width="40">SL</th>
										<th width="115">Transfer Id</th>
										<th width="80">Transfer Date</th>
										<th width="100">From Order</th>
										<th width="170">Item Description</th>
										<th>Transfer Qnty</th>
									</tr>
								</thead>
								<?
								$i = 1;
								$total_trans_in_qnty = 0;
								if($deter_id != "") $deter_id_cond = " and d.detarmination_id=$deter_id"; else $deter_id_cond = "";
								$sql = "select a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id, b.from_prod_id, sum(c.quantity) as transfer_qnty, d.product_name_details from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=13 and a.transfer_criteria=4 and c.trans_type=5 and c.entry_form=133 and c.po_breakdown_id in ($order_id) $deter_id_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id, b.from_prod_id, d.product_name_details";
								$result = sql_select($sql);
								foreach ($result as $row) {
									if ($i % 2 == 0)
										$bgcolor = "#E9F3FF";
									else
										$bgcolor = "#FFFFFF";
									?>
									<tr bgcolor="<? echo $bgcolor; ?>"
										onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td width="40"><? echo $i; ?></td>
										<td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
										<td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
										<td width="100"><p><? echo $po_arr[$row[csf('from_order_id')]]; ?></p></td>
										<td width="170"><p><? echo $row[csf('product_name_details')]; ?></p></td>
										<td align="right"><? echo number_format($row[csf('transfer_qnty')], 2); ?> </td>
									</tr>
									<?
									$total_trans_in_qnty += $row[csf('transfer_qnty')];
									$i++;
								}
								?>

								<tfoot>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th>Total</th>
									<th><? echo number_format($total_trans_in_qnty, 2); ?></th>
								</tfoot>
							</table>
						</div>
					</fieldset>
					<?
					exit();
				}

				if ($action == "yarn_trans_out_popup")
				{
					echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
					page_style();
					extract($_REQUEST);
					$po_arr = return_library_array("select id, po_number from wo_po_break_down", "id", "po_number");
					?>
					<script>

						function print_window() {
							var w = window.open("Surprise", "#");
							var d = w.document.open();
							d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
								'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

							d.close();
						}

					</script>
					<div style="width:675px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
						style="width:100px" class="formbutton"/></div>
						<fieldset style="width:700px; margin:auto;">
							<div id="report_container">
								<table border="1" class="rpt_table" rules="all" width="100%" cellpadding="0" cellspacing="0">
									<thead>
										<tr>
											<th colspan="6">Transfer Out</th>
										</tr>
										<tr>
											<th width="40">SL</th>
											<th width="115">Transfer Id</th>
											<th width="80">Transfer Date</th>
											<th width="100">To Order</th>
											<th width="170">Item Description</th>
											<th>Transfer Qnty</th>
										</tr>
									</thead>
									<?
									$total_trans_out_qnty = 0;
									if($deter_id != "") $deter_id_cond = " and d.detarmination_id=$deter_id"; else $deter_id_cond = "";
									$sql = "select a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, sum(c.quantity) as transfer_qnty, d.product_name_details from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=13 and a.transfer_criteria=4 and c.trans_type=6 and c.entry_form=133 and c.po_breakdown_id in ($order_id) $deter_id_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, d.product_name_details";
									$result = sql_select($sql);
									foreach ($result as $row) {
										if ($i % 2 == 0)
											$bgcolor = "#E9F3FF";
										else
											$bgcolor = "#FFFFFF";
										?>
										<tr bgcolor="<? echo $bgcolor; ?>"
											onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
											<td width="40"><? echo $i; ?></td>
											<td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
											<td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
											<td width="100"><p><? echo $po_arr[$row[csf('to_order_id')]]; ?></p></td>
											<td width="170"><p><? echo $row[csf('product_name_details')]; ?></p></td>
											<td align="right"><? echo number_format($row[csf('transfer_qnty')], 2); ?> </td>
										</tr>
										<?
										$total_trans_out_qnty += $row[csf('transfer_qnty')];
										$i++;
									}
									?>
									<tfoot>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th>Total</th>
										<th><? echo number_format( $total_trans_out_qnty, 2); ?></th>
									</tfoot>
								</table>
							</div>
						</fieldset>
						<?
						exit();
					}
					if ($action == "grey_required_popup") {

						echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
						page_style();
						extract($_REQUEST);


						$machine_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
						$receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan");
						?>
						<script>

							var tableFilters = {
								col_operation: {
									id: ["td_booking_qty", "td_finish_qty", "td_grey_qty"],
									col: [9, 13, 15],
									operation: ["sum", "sum", "sum"],
									write_method: ["innerHTML", "innerHTML", "innerHTML"]
								}
							}
							$(document).ready(function (e) {
								setFilterGrid('tbl_list_search', -1, tableFilters);
							});

							function print_window() {
								document.getElementById('scroll_body').style.overflow = "auto";
								document.getElementById('scroll_body').style.maxHeight = "none";

								$('.flt').hide();

								var w = window.open("Surprise", "#");
								var d = w.document.open();
								d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
									'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

								d.close();
								document.getElementById('scroll_body').style.overflowY = "scroll";
								document.getElementById('scroll_body').style.maxHeight = "330px";

								$('.flt').show();
							}

						</script>
						<style type="text/css">
						.alignment_css
						{
							word-break: break-all;
							word-wrap: break-word;
						}
					</style>
					<div style="width:1308px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
						style="width:100px" class="formbutton"/></div>
						<fieldset style="width:1308px;">
							<div id="report_container">

								<table border="1" class="rpt_table" rules="all" width="1290" cellpadding="0" cellspacing="0"  align="left">
									<thead>
										<th colspan="17"><b>Grey Required Info</b></th>
									</thead>
									<thead>
										<tr>


											<th class='alignment_css' width="30">SL</th>
											<th class='alignment_css' width="115">Body Part</th>
											<th class='alignment_css' width="95">Color Type</th>
											<th class='alignment_css' width="110">Fabric Description</th>
											<th class='alignment_css' width="60">Fabric GSM</th>
											<th class='alignment_css' width="60">Fabric Dia</th>
											<th class='alignment_css' width="100">Color</th>
											<th class='alignment_css' width="100">Color Range</th>
											<th class='alignment_css' width="60">Con. UOM</th>
											<th class='alignment_css' width="60">Booking Qty</th>
											<th class='alignment_css' width="80">Avg. Price</th>
											<th class='alignment_css' width="80">Amount</th>
											<th class='alignment_css' width="60">UOM</th>
											<th class='alignment_css' width="70">Finish Qty</th>
											<th class='alignment_css'  width="70">Process Loss %.</th>
											<th class='alignment_css' width="70">Grey Qty</th>
											<th class='alignment_css' width="70">Remarks</th>
										</tr>
									</thead>
								</table>
								<div style="width:1308px; max-height:330px; overflow-y:scroll" id="scroll_body">
									<table border="1"  align="left" class="rpt_table" rules="all" width="1290" cellpadding="0" cellspacing="0"
									id="tbl_list_search">
									<tbody>
										<?
										$i = 1;
										$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
										$total_receive_qnty = 0;

										$product_arr = return_library_array("select id,product_name_details from product_details_master where item_category_id=13", 'id', 'product_name_details');
										if($deter_id != "") $deter_id_cond = " and b.febric_description_id=$deter_id";

										$sql = "SELECT id, mst_id, job_no_mst, body_part_id, color_type_id, determination_id, fabric_desc, gsm_weight, dia, width_dia_type, color_id, color_range_id, finish_qty, avg_rate, amount, process_loss, grey_qty, work_scope, yarn_data, order_uom, pre_cost_remarks, rmg_qty, pre_cost_fabric_cost_dtls_id, item_number_id, grey_qnty_by_uom, cons_uom FROM fabric_sales_order_dtls Where MST_ID = '$order_id' and status_active=1 order by id asc "; //, process_id, process_seq, barcode_year, barcode_suffix_no, barcode_no
										$result = sql_select($sql);

										foreach ($result as $row) {
											if ($i % 2 == 0)
												$bgcolor = "#E9F3FF";
											else
												$bgcolor = "#FFFFFF";

											$total_receive_qnty += $row[csf('quantity')];
											?>
											<tr bgcolor="<? echo $bgcolor; ?>"
												onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
												<td class='alignment_css' width="30"><? echo $i; ?></td>
												<td class='alignment_css' width="115" class="center"><? echo $body_part[$row[csf('body_part_id')]]; ?></td>
												<td class='alignment_css' width="95" class="center"><? echo $color_type[$row[csf('color_type_id')]]; ?></td>
												<td class='alignment_css' width="110" class="center"><? echo $row[csf('fabric_desc')]; ?></td>
												<td class='alignment_css' width="60" class="center"><? echo $row[csf('gsm_weight')]; ?></td>
												<td class='alignment_css' width="60" class="center"><? echo $row[csf('dia')]; ?></td>

												<td class='alignment_css' width="100" class="center"><? echo  $color_arr[$row[csf('color_id')]]; ?></td>
												<td class='alignment_css' width="100" class="center"><? echo  $color_range[$row[csf('color_range_id')]]; ?></td>
												<td class='alignment_css' width="60" class="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></td>
												<td class='alignment_css' width="60" class="left"><? echo number_format( $row[csf('grey_qnty_by_uom')],2); ?></td>
												<td class='alignment_css' width="80" class="center"><? echo $row[csf('avg_rate')]; ?></td>
												<td class='alignment_css' width="80" class="left"><? echo number_format($row[csf('amount')],2); ?></td>
												<td class='alignment_css' width="60" class="center"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
												<td class='alignment_css' width="70" class="left"><? echo number_format($row[csf('finish_qty')],2); ?></td>
												<td class='alignment_css' width="70" class="center"><? echo $row[csf('process_loss')]; ?></td>
												<td class='alignment_css' width="70" class="left"><? echo number_format($row[csf('grey_qty')],2); ?></td>
												<td class='alignment_css' width="70" class="center"><? echo $row[csf('pre_cost_remarks')]; ?></td>


											</tr>
											<?
											$i++;
										}
										?>
									</tbody>
								</table>
								<table border="1" class="rpt_table" rules="all" width="1290" cellpadding="0" cellspacing="0" align="left">
									<tfoot>
										<th class='alignment_css' width="30"></th>
										<th class='alignment_css' width="115"> </th>
										<th class='alignment_css' width="95"> </th>
										<th class='alignment_css' width="110"> </th>
										<th class='alignment_css' width="60"> </th>
										<th class='alignment_css' width="60"> </th>
										<th class='alignment_css' width="100"></th>
										<th class='alignment_css' width="100"> </th>
										<th class='alignment_css' width="60"> </th>
										<th class='alignment_css' id="td_booking_qty" width="60"></th>
										<th class='alignment_css' width="80"></th>
										<th class='alignment_css' width="80"></th>
										<th class='alignment_css' width="60"></th>
										<th class='alignment_css' id="td_finish_qty" width="70"></th>
										<th class='alignment_css'  width="70"></th>
										<th class='alignment_css' id="td_grey_qty" width="70"> </th>
										<th class='alignment_css' width="70"></th>
									</tfoot>
								</table>
							</div>

						</div>
					</fieldset>
					<?
					exit();
				}


				if ($action == "grey_receive_popup") {
					echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
					page_style();
					extract($_REQUEST);

					$machine_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
					$receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan");
					?>
					<script>

						var tableFilters = {
							col_operation: {
								id: ["value_receive_qnty_in", "value_receive_qnty_out", "value_receive_qnty_tot"],
								col: [9,10,11],
								operation: ["sum", "sum", "sum"],
								write_method: ["innerHTML", "innerHTML", "innerHTML"]
							}
						}
						$(document).ready(function (e) {
							setFilterGrid('tbl_list_search', -1, tableFilters);
						});

						function print_window() {
							document.getElementById('scroll_body').style.overflow = "auto";
							document.getElementById('scroll_body').style.maxHeight = "none";

							$('#tbl_list_search tr:first').hide();

							var w = window.open("Surprise", "#");
							var d = w.document.open();
							d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
								'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

							d.close();
							document.getElementById('scroll_body').style.overflowY = "scroll";
							document.getElementById('scroll_body').style.maxHeight = "230px";

							$('#tbl_list_search tr:first').show();
						}

					</script>
					<div style="width:1237px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
						style="width:100px" class="formbutton"/></div>
						<fieldset style="width:1237px;">
							<div id="report_container">

								<table border="1" class="rpt_table" rules="all" width="1220" cellpadding="0" cellspacing="0">
									<thead>
										<th colspan="14"><b>Grey Receive Info</b></th>
									</thead>
									<thead>
										<th width="30">SL</th>
										<th width="115">Receive Id</th>
										<th width="95">Receive Basis</th>
										<th width="110">Product Details</th>
										<th width="100">Booking / Program No</th>
										<th width="100">Color</th>
										<th width="100">Color Range</th>
										<th width="60">Machine No</th>
										<th width="75">Production Date</th>
										<th width="80">Inhouse Production</th>
										<th width="80">Outside Production</th>
										<th width="80">Production Qnty</th>
										<th width="70">Challan No</th>
										<th>Kniting Com.</th>
									</thead>
								</table>
								<div style="width:1238px; max-height:330px; overflow-y:scroll" id="scroll_body">
									<table border="1" class="rpt_table" rules="all" width="1220" cellpadding="0" cellspacing="0"
									id="tbl_list_search">
									<?
									$i = 1;
									$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
									$total_receive_qnty = 0;
									$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");

									$supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
									$product_arr = return_library_array("select id,product_name_details from product_details_master where item_category_id=13", 'id', 'product_name_details');
									if($deter_id != "") $deter_id_cond = " and b.febric_description_id=$deter_id";

									$sql = "select a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.machine_no_id, b.prod_id, sum(c.quantity) as quantity from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.po_breakdown_id in($order_id) and c.is_sales=1 $deter_id_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.machine_no_id, b.prod_id";
									$result = sql_select($sql);
									$all_program_arr=array();
									foreach($result as $v)
									{
										$all_program_arr[$v[csf("booking_no")]]=$v[csf("booking_no")];
									}
									$all_programs=implode(",", $all_program_arr);
									$program_wise_col_sql="SELECT  id , color_id, color_range FROM ppl_planning_info_entry_dtls Where ID in($all_programs) ";
									$program_wise_col_arr=array();
									foreach(sql_select($program_wise_col_sql) as $v)
									{
										$colors=$v[csf("color_id")];
										$program_id=$v[csf("id")];
										$range=$v[csf("color_range")];
										foreach(explode(",",$colors) as $color_id)
										{
											if($program_wise_col_arr[$program_id]['color']=="")$program_wise_col_arr[$program_id]['color']=$color_arr[$color_id];
											else $program_wise_col_arr[$program_id]['color'].=','.$color_arr[$color_id];
										}
										$program_wise_col_arr[$program_id]['range']=$color_range[$range];
									}
									foreach ($result as $row) {
										if ($i % 2 == 0)
											$bgcolor = "#E9F3FF";
										else
											$bgcolor = "#FFFFFF";

										$total_receive_qnty += $row[csf('quantity')];
										?>
										<tr bgcolor="<? echo $bgcolor; ?>"
											onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
											<td width="30"><? echo $i; ?></td>
											<td width="115" class="center"><? echo $row[csf('recv_number')]; ?></td>
											<td width="95" class="center"><? echo $receive_basis[$row[csf('receive_basis')]]; ?></td>
											<td width="110" class="center"><? echo $product_arr[$row[csf('prod_id')]]; ?></td>
											<td width="100" class="center"><? echo $row[csf('booking_no')]; ?></td>
											<td width="100" class="center"><? echo  $program_wise_col_arr[$row[csf('booking_no')]]['color']; ?></td>
											<td width="100" class="center"><? echo  $program_wise_col_arr[$row[csf('booking_no')]]['range']; ?></td>

											<td width="60" class="center"><? echo $machine_arr[$row[csf('machine_no_id')]]; ?></td>
											<td width="75" class="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
											<td align="right" width="80">
												<?
												if ($row[csf('knitting_source')] != 3) {
													echo number_format($row[csf('quantity')], 2, '.', '');
													$total_receive_qnty_in += $row[csf('quantity')];
												} else echo "&nbsp;";
												?>
											</td>
											<td align="right" width="80">
												<?
												if ($row[csf('knitting_source')] == 3) {
													echo number_format($row[csf('quantity')], 2, '.', '');
													$total_receive_qnty_out += $row[csf('quantity')];
												} else echo "&nbsp;";
												?>
											</td>
											<td class="right"
											width="80"><? echo number_format($row[csf('quantity')], 2, '.', ''); ?></td>
											<td width="70"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
											<td>
												<p><? if ($row[csf('knitting_source')] == 1) echo $company_library[$row[csf('knitting_company')]]; else if ($row[csf('knitting_source')] == 3) echo $supplier_details[$row[csf('knitting_company')]]; ?></p>
											</td>
										</tr>
										<?
										$i++;
									}
									?>
								</table>
							</div>
							<table border="1" class="rpt_table" rules="all" width="1220" cellpadding="0" cellspacing="0">
								<tfoot>
									<th width="30">&nbsp;</th>
									<th width="115">&nbsp;</th>
									<th width="95">&nbsp;</th>
									<th width="110">&nbsp;</th>
									<th width="100">&nbsp;</th>
									<th width="100">&nbsp;</th>
									<th width="100">&nbsp;</th>
									<th width="60">&nbsp;</th>
									<th width="75" align="right">Total</th>
									<th width="80" align="right"
									id="value_receive_qnty_in"><? echo number_format($total_receive_qnty_in, 2, '.', ''); ?></th>
									<th width="80" align="right"
									id="value_receive_qnty_out"><? echo number_format($total_receive_qnty_out, 2, '.', ''); ?></th>
									<th width="80" align="right"
									id="value_receive_qnty_tot"><? echo number_format($total_receive_qnty, 2, '.', ''); ?></th>
									<th width="70">&nbsp;</th>
									<th>&nbsp;</th>
								</tfoot>
							</table>
						</div>
					</fieldset>
					<?
					exit();
				}

				if ($action == "grey_purchase_delivery") {
					echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
					page_style();
					extract($_REQUEST);
					$order_id = explode('_', $order_id);
					?>
					<script>
						function print_window() {
							document.getElementById('scroll_body').style.overflow = "auto";
							document.getElementById('scroll_body').style.maxHeight = "none";

							var w = window.open("Surprise", "#");
							var d = w.document.open();
							d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
								'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

							d.close();
							document.getElementById('scroll_body').style.overflowY = "scroll";
							document.getElementById('scroll_body').style.maxHeight = "230px";
						}
					</script>
					<div style="width:750px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
						style="width:100px" class="formbutton"/></div>
						<fieldset style="width:740px; margin-left:2px">
							<div id="report_container">
								<table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0">
									<thead>
										<th colspan="11"><b>Grey Receive / Purchase Info</b></th>
									</thead>
									<thead>
										<th width="30">SL</th>
										<th width="125">Receive Id</th>
										<th width="150">Product Details</th>
										<th width="75">Production Date</th>
										<th width="80">Delivery Quantity</th>
										<th>Kniting Com.</th>
									</thead>
								</table>
								<div style="width:740px; max-height:330px; overflow-y:scroll" id="scroll_body">
									<table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0">
										<?
										$i = 1;
										$total_receive_qnty = 0;
										$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
										$supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
										$product_arr = return_library_array("select id,product_name_details from product_details_master where item_category_id=13", 'id', 'product_name_details');
										$sql = "select a.sys_number,a.knitting_company,a.knitting_source,a.delevery_date, b.order_id, sum(b.current_delivery) as quantity,b.product_id,b.grey_sys_id from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b, pro_roll_details c where a.id=b.mst_id and b.id = c.dtls_id and b.entry_form =56 and c.entry_form = 56 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.order_id in($order_id[0]) and c.is_sales =1 group by a.sys_number,a.knitting_company,a.knitting_source,a.delevery_date,b.order_id,b.product_id,b.grey_sys_id";
										$result = sql_select($sql);
										foreach ($result as $row) {
											if ($i % 2 == 0)
												$bgcolor = "#E9F3FF";
											else
												$bgcolor = "#FFFFFF";

											$total_receive_qnty += $row[csf('quantity')];
											?>
											<tr bgcolor="<? echo $bgcolor; ?>"
												onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
												<td width="30"><? echo $i; ?></td>
												<td width="125"><p><? echo $row[csf('sys_number')]; ?></p></td>
												<td width="150"><p><? echo $product_arr[$row[csf('product_id')]]; ?></p></td>
												<td width="75" align="center"><? echo change_date_format($row[csf('delevery_date')]); ?></td>
												<td align="right" width="80">
													<?
													echo number_format($row[csf('quantity')], 2, '.', '');
													$total_receive_qnty_in += $row[csf('quantity')];
													?>
												</td>
												<td>
													<? if ($row[csf('knitting_source')] == 1) echo $company_library[$row[csf('knitting_company')]]; else if ($row[csf('knitting_source')] == 3) echo $supplier_details[$row[csf('knitting_company')]]; ?>
												</td>
											</tr>
											<?
											$i++;
										}
										?>
										<tfoot>
											<th colspan="4" align="right">Total</th>
											<th align="right"><? echo number_format($total_receive_qnty_in, 2, '.', ''); ?></th>
											<th>&nbsp;</th>
										</tfoot>
									</table>
								</div>
							</div>
						</fieldset>
						<?
						exit();
					}
					if ($action == "grey_purchase_popup") {
						echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
						page_style();
						extract($_REQUEST);
						$order_id = explode('_', $order_id);
						?>
						<script>
							function print_window() {
								document.getElementById('scroll_body').style.overflow = "auto";
								document.getElementById('scroll_body').style.maxHeight = "none";

								var w = window.open("Surprise", "#");
								var d = w.document.open();
								d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
									'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

								d.close();
								document.getElementById('scroll_body').style.overflowY = "scroll";
								document.getElementById('scroll_body').style.maxHeight = "230px";
							}
						</script>
						<div style="width:1037px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
							style="width:100px" class="formbutton"/></div>
							<fieldset style="width:1037px; margin-left:2px">
								<div id="report_container">
									<table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0">
										<thead>
											<th colspan="11"><b>Grey Receive / Purchase Info</b></th>
										</thead>
										<thead>
											<th width="30">SL</th>
											<th width="125">Receive Id</th>
											<th width="95">Receive Basis</th>
											<th width="150">Product Details</th>
											<th width="110">Booking/PI/ Production No</th>
											<th width="75">Production Date</th>
											<th width="80">Inhouse Production</th>
											<th width="80">Outside Production</th>
											<th width="80">Production Qnty</th>
											<th width="65">Challan No</th>
											<th>Kniting Com.</th>
										</thead>
									</table>
									<div style="width:1037px; max-height:330px; overflow-y:scroll" id="scroll_body">
										<table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0">
											<?
											if ($order_id[1] == 9) $receive_basis_cond = " and a.receive_basis in (9,10)"; else if ($order_id[1] == 0) $receive_basis_cond = " and a.receive_basis not in (9,10)";
											if($deter_id != "") $deter_id_cond = " and b.febric_description_id=$deter_id";else $deter_id_cond = "";
											$i = 1;
											$total_receive_qnty = 0;
											$product_arr = return_library_array("select id,product_name_details from product_details_master where item_category_id=13", 'id', 'product_name_details');
											$sql = "select a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.prod_id, sum(c.quantity) as quantity from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id $receive_basis_cond and a.entry_form in (22,58) and c.entry_form in (22,58) and c.po_breakdown_id in($order_id[0]) $deter_id_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.prod_id";
											$result = sql_select($sql);
											foreach ($result as $row) {
												if ($i % 2 == 0)
													$bgcolor = "#E9F3FF";
												else
													$bgcolor = "#FFFFFF";

												$total_receive_qnty += $row[csf('quantity')];
												?>
												<tr bgcolor="<? echo $bgcolor; ?>"
													onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
													<td width="30"><? echo $i; ?></td>
													<td width="125"><p><? echo $row[csf('recv_number')]; ?></p></td>
													<td width="95"><p><? echo $receive_basis_arr[$row[csf('receive_basis')]]; ?></p></td>
													<td width="150"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
													<td width="110"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
													<td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
													<td align="right" width="80">
														<?
														if ($row[csf('knitting_source')] != 3) {
															echo number_format($row[csf('quantity')], 2, '.', '');
															$total_receive_qnty_in += $row[csf('quantity')];
														} else echo "&nbsp;";
														?>
													</td>
													<td align="right" width="80">
														<?
														if ($row[csf('knitting_source')] == 3) {
															echo number_format($row[csf('quantity')], 2, '.', '');
															$total_receive_qnty_out += $row[csf('quantity')];
														} else echo "&nbsp;";
														?>
													</td>
													<td align="right"
													width="80"><? echo number_format($row[csf('quantity')], 2, '.', ''); ?></td>
													<td width="65"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
													<td>
														<p><? if ($row[csf('knitting_source')] == 1) echo $company_library[$row[csf('knitting_company')]]; else if ($row[csf('knitting_source')] == 3) echo $supplier_details[$row[csf('knitting_company')]]; ?>
													&nbsp;</p></td>
												</tr>
												<?
												$i++;
											}
											?>
											<tfoot>
												<th colspan="6" align="right">Total</th>
												<th align="right"><? echo number_format($total_receive_qnty_in, 2, '.', ''); ?></th>
												<th align="right"><? echo number_format($total_receive_qnty_out, 2, '.', ''); ?></th>
												<th align="right"><? echo number_format($total_receive_qnty, 2, '.', ''); ?></th>
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

						if ($action == "grey_issue_popup") {
							echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
							page_style();
							extract($_REQUEST);
							$order_id = explode('_', $order_id);
							?>
							<script>

								function print_window() {
									document.getElementById('scroll_body').style.overflow = "auto";
									document.getElementById('scroll_body').style.maxHeight = "none";

									var w = window.open("Surprise", "#");
									var d = w.document.open();
									d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
										'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

									d.close();
									document.getElementById('scroll_body').style.overflowY = "scroll";
									document.getElementById('scroll_body').style.maxHeight = "230px";
								}

							</script>
							<div style="width:955px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
								style="width:100px" class="formbutton"/></div>
								<fieldset style="width:970px; margin-left:3px">
									<div id="report_container">
										<table border="1" class="rpt_table" rules="all" width="950" cellpadding="0" cellspacing="0">
											<thead>
												<tr>
													<th colspan="10"><b>Grey Issue Info</b></th>
												</tr>
												<tr>
													<th width="40">SL</th>
													<th width="120">Issue Id</th>
													<th width="100">Issue Purpose</th>
													<th width="100">Issue To</th>
													<th width="115">Booking No</th>
													<th width="90">Batch No</th>
													<th width="90">Batch Color</th>
													<th width="80">Issue Date</th>
													<th width="100">Issue Qnty (In)</th>
													<th>Issue Qnty (Out)</th>
												</tr>
											</thead>
										</table>
										<div style="width:967px; max-height:320px; overflow-y:scroll" id="scroll_body">
											<table border="1" class="rpt_table" rules="all" width="950" cellpadding="0" cellspacing="0">
												<?
												$batch_color_details = return_library_array("select  id,color_id from pro_batch_create_mst", "id", "color_id");
												$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
												$supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
												$batch_details = return_library_array("select id, batch_no from pro_batch_create_mst", "id", "batch_no");
												$i = 1;
												$issue_to = '';
												if($deter_id !="" ) $deter_id_cond = " and d.detarmination_id=$deter_id"; $deter_id_cond = "";
												$sql = "select a.issue_number, a.issue_date, a.issue_purpose, a.knit_dye_source, a.knit_dye_company, e.sales_booking_no booking_no, a.batch_no, sum(c.quantity) as quantity,d.detarmination_id  from inv_issue_master a, inv_grey_fabric_issue_dtls b, order_wise_pro_details c,product_details_master d,fabric_sales_order_mst e  where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=e.id and b.prod_id=d.id and a.entry_form in(16,61) and c.entry_form in(16,61) and c.po_breakdown_id in($order_id[0]) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $deter_id_cond group by a.id,  a.issue_number, a.issue_date, a.issue_purpose, a.knit_dye_source, a.knit_dye_company, e.sales_booking_no, a.batch_no,d.detarmination_id";
												$result = sql_select($sql);
												foreach ($result as $row) {
													if ($i % 2 == 0)
														$bgcolor = "#E9F3FF";
													else
														$bgcolor = "#FFFFFF";

													if ($row[csf('knit_dye_source')] == 1) {
														$issue_to = $company_library[$row[csf('knit_dye_company')]];
													} else if ($row['knit_dye_source'] == 3) {
														$issue_to = $supplier_details[$row[csf('knit_dye_company')]];
													} else
													$issue_to = "&nbsp;";

													?>
													<tr bgcolor="<? echo $bgcolor; ?>"
														onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
														<td width="40"><? echo $i; ?></td>
														<td width="120"><p><? echo $row[csf('issue_number')]; ?></p></td>
														<td width="100"><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></td>
														<td width="100"><p><? echo $issue_to; ?></p></td>
														<td width="115"><? echo $row[csf('booking_no')]; ?>&nbsp;</td>
														<td width="90"><p><? echo $batch_details[$row[csf('batch_no')]]; ?>&nbsp;</p></td>
														<td width="90"><p><? echo $color_array[$batch_color_details[$row[csf('batch_no')]]]; ?>
													&nbsp;</p></td>
													<td width="80" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
													<td width="100" align="right">
														<?
														if ($row[csf('knit_dye_source')] != 3) {
															echo number_format($row[csf('quantity')], 2);
															$total_issue_qnty += $row[csf('quantity')];
														} else echo "&nbsp;";
														?>
													</td>
													<td align="right">
														<?
														if ($row[csf('knit_dye_source')] == 3) {
															echo number_format($row[csf('quantity')], 2);
															$total_issue_qnty_out += $row[csf('quantity')];
														} else echo "&nbsp;";
														?>
													</td>
												</tr>
												<?
												$i++;
											}
											?>
											<tfoot>
												<tr>
													<th colspan="8" align="right">Total</th>
													<th align="right"><? echo number_format($total_issue_qnty, 2); ?></th>
													<th align="right"><? echo number_format($total_issue_qnty_out, 2); ?></th>
												</tr>
												<tr>
													<th colspan="8" align="right">Grand Total</th>
													<th align="right"
													colspan="2"><? echo number_format($total_issue_qnty + $total_issue_qnty_out, 2); ?></th>
												</tr>
											</tfoot>
										</table>
									</div>
									<table border="1" class="rpt_table" rules="all" width="950" cellpadding="0" cellspacing="0">
										<thead>
											<th colspan="6"><b>Grey fabric issue Return</b></th>
										</thead>
										<thead>
											<th width="40">SL</th>
											<th width="105">Issue Return No</th>
											<th width="100">Issue No</th>
											<th width="100">Booking No</th>
											<th width="100">Return Date</th>
											<th width="100">Return Qnty</th>
										</thead>
										<?
										$total_yarn_return_qnty = 0;
										$total_yarn_return_qnty_out = 0;

										$sql ="select  a.po_breakdown_id, a.qnty, c.recv_number, c.receive_date, f.sales_booking_no, g.issue_number from pro_roll_details a, pro_roll_details b, inv_receive_master c, pro_grey_prod_entry_dtls d, fabric_sales_order_mst f ,inv_issue_master g where a.barcode_no = b.barcode_no and a.entry_form =61 and b.entry_form = 84 and b.mst_id = c.id and c.id = d.mst_id and b.po_breakdown_id= f.id and a.mst_id = g.id and g.entry_form=61 and a.is_sales =1 and a.po_breakdown_id in($order_id[0]) and a.is_deleted =0 and a.status_active = 1 and b.is_deleted =0 and b.status_active = 1 group by  a.po_breakdown_id, a.qnty, c.recv_number, c.receive_date,f.sales_booking_no,g.issue_number";
										$result = sql_select($sql);
										$y=1;
										foreach ($result as $row) {
											if ($y % 2 == 0)
												$bgcolor = "#E9F3FF";
											else
												$bgcolor = "#FFFFFF";
											$fab_desc = $composition_arr[$row[csf('detarmination_id')]] ;
											?>
											<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $y; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $y; ?>">
												<td width="40" class="center"><? echo $y; ?></td>
												<td width="105"><p><? echo $row[csf('recv_number')]; ?></p></td>
												<td width="100"><p><? echo $row[csf('issue_number')]; ?></p></td>
												<td width="100"><p><? echo $row[csf('sales_booking_no')]; ?></p></td>
												<td width="100" class="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
												<td width="100" class="right"><? echo $row[csf('qnty')]; ?></td>
											</tr>
											<?
											$y++;
											$return_qnty += $row[csf('qnty')];
										}

										$balance_qnty = $total_issue_qnty + $total_issue_qnty_out - $return_qnty;
										?>
										<tr style="font-weight:bold">
											<td align="right" colspan="5">Total Return</td>
											<td align="right"><? echo number_format($return_qnty, 2); ?></td>
										</tr>
										<tfoot>
											<tr>
												<th align="right" colspan="5">Total Balance</th>
												<th align="right"><? echo number_format($balance_qnty, 2); ?></th>
											</tr>
										</tfoot>
									</table>
								</div>
							</fieldset>

							<?
							exit();
						}

						if($action=="grey_receive_by_batch_popup"){
							echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
							page_style();
							extract($_REQUEST);
							?>
							<script>

								function print_window() {
									document.getElementById('scroll_body').style.overflow = "auto";
									document.getElementById('scroll_body').style.maxHeight = "none";

									var w = window.open("Surprise", "#");
									var d = w.document.open();
									d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
										'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

									d.close();
									document.getElementById('scroll_body').style.overflowY = "scroll";
									document.getElementById('scroll_body').style.maxHeight = "230px";
								}

							</script>
							<div style="width:1020px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
								style="width:100px" class="formbutton"/></div>
								<fieldset style="width:1030px; margin-left:3px">
									<div id="report_container">
										<table border="1" class="rpt_table" rules="all" width="1010" cellpadding="0" cellspacing="0">
											<thead>
												<tr>
													<th colspan="10"><b>Grey Receive By Batch Info</b></th>
												</tr>
												<tr>
													<th width="40">SL</th>
													<th width="140">Company</th>
													<th width="140">Receive No</th>
													<th width="70">Year</th>
													<th width="120">Dyeing Source</th>
													<th width="140">Dyeing Company</th>
													<th width="130">Receive date</th>
													<th width="100">Recv Qty</th>
													<th width="130">Issue Challan</th>
												</tr>
											</thead>
										</table>
										<div style="width:1030px; max-height:320px; overflow-y:scroll" id="scroll_body">
											<table border="1" class="rpt_table" rules="all" width="1010" cellpadding="0" cellspacing="0">
												<?
												if($db_type==0)
												{
													$year_field=" YEAR(a.insert_date) as year,";
												}
												else if($db_type==2)
												{
													$year_field=" to_char(a.insert_date,'YYYY') as year,";

												}
												else $year_field="";

												if($deter_id != "") $deter_id_cond = " and b.febric_description_id=$deter_id"; else $deter_id_cond = "";
												$sql="select a.id,a.recv_number,a.company_id, a.dyeing_source,a.dyeing_company, a.receive_date,a.challan_no, $year_field c.po_breakdown_id,b.febric_description_id,sum(c.qnty) roll_wgt from pro_grey_batch_dtls b,inv_receive_mas_batchroll a,pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and c.entry_form=62 and c.po_breakdown_id in($order_id) $deter_id_cond and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id,a.recv_number,a.company_id, a.dyeing_source,a.dyeing_company, a.receive_date,a.challan_no,c.po_breakdown_id,b.febric_description_id,a.insert_date";
												$result = sql_select($sql);
												$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
												$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
												$result = sql_select($sql);
												$i=1;
												foreach ($result as $row) {
													if ($i % 2 == 0)
														$bgcolor = "#E9F3FF";
													else
														$bgcolor = "#FFFFFF";

													$knit_comp="&nbsp;";
													if($row[csf('dyeing_source')]==1)
														$knit_comp=$company_arr[$row[csf('dyeing_company')]];
													else
														$knit_comp=$supllier_arr[$row[csf('dyeing_company')]];

													?>
													<tr bgcolor="<? echo $bgcolor; ?>"
														onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
														<td width="40" style="text-align: center;"><? echo $i; ?></td>
														<td width="140" style="text-align: center;"><? echo $company_arr[$row[csf('company_id')]]; ?></td>
														<td width="140" style="text-align: center;"><? echo $row[csf('recv_number')]; ?></td>
														<td width="70" style="text-align: center;"><? echo $row[csf('year')]; ?></td>
														<td width="120" style="text-align:center;"><? echo $knitting_source[$row[csf('dyeing_source')]]; ?></td>
														<td width="140" style="text-align: center;"><? echo $knit_comp; ?></td>
														<td width="130" style="text-align: center;"><? echo change_date_format($row[csf('receive_date')]); ?></td>
														<td width="100"><? echo number_format($row[csf('roll_wgt')],2); ?></td>
														<td width="130" style="text-align: center;"><? echo $row[csf('challan_no')]; ?></td>
													</tr>
													<?
													$total_issue_qnty += $row[csf('roll_wgt')];
													$i++;
												}
												?>
												<tfoot>
													<tr>
														<th colspan="7" align="right">Total</th>
														<th align="right"><? echo number_format($total_issue_qnty, 2); ?></th>
														<th></th>
													</tr>
												</tfoot>
											</table>
										</div>
									</div>
								</fieldset>
								<?
								exit();
							}

							if($action=="batch_popup"){
								echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
								page_style();
								extract($_REQUEST);
								?>
								<script>
									function print_window() {
										document.getElementById('scroll_body').style.overflow = "auto";
										document.getElementById('scroll_body').style.maxHeight = "none";
										var w = window.open("Surprise", "#");
										var d = w.document.open();
										d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
											'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');
										d.close();
										document.getElementById('scroll_body').style.overflowY = "scroll";
										document.getElementById('scroll_body').style.maxHeight = "230px";
									}
								</script>
								<?
								$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
								$result = sql_select("select a.id, a.batch_no,a.sales_order_no, a.extention_no, a.batch_weight, a.batch_date, a.batch_against, a.batch_for, a.booking_no, a.color_id,sum(b.batch_qnty)batch_qnty from pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and a.page_without_roll=0 and a.status_active=1 and a.entry_form=0 and a.is_deleted=0 and a.sales_order_id=$order_id and a.color_id=$deter_id  and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no,a.sales_order_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.batch_against, a.batch_for, a.booking_no, a.color_id,b.is_sales order by a.batch_date desc");//and (a.extention_no is null or a.extention_no=0)
								?>
								<div style="width:1020px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
									style="width:100px" class="formbutton"/></div>
									<fieldset style="width:1030px; margin-left:3px">
										<div id="report_container">
											<table border="1" class="rpt_table" rules="all" width="1010" cellpadding="0" cellspacing="0">
												<thead>
													<tr>
														<th width="50">SL No</th>
														<th width="100">Batch No</th>
														<th width="70">Ext. No</th>
														<th width="150">Sales Order No</th>
														<th width="105">Booking No</th>
														<th width="80">Batch Quantity</th>
														<th width="80">Batch Date</th>
														<th width="80">Batch Against</th>
														<th width="85">Batch For</th>
														<th>Color</th>
													</tr>
												</thead>
											</table>
											<div style="width:1030px; max-height:320px; overflow-y:scroll" id="scroll_body">
												<table border="1" class="rpt_table" rules="all" width="1010" cellpadding="0" cellspacing="0">
													<tbody>
														<?php
														$i = 1;
														foreach ($result as $row) {
															if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
															?>
															<tr onClick="js_set_value(<? echo $row[csf('id')]; ?>)" bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer">
																<td style="text-align: center;" width="50"><?php echo $i; ?></td>
																<td style="text-align: center;" width="100"><?php echo $row[csf("batch_no")]; ?></td>
																<td style="text-align: center;" width="70"><?php echo $row[csf("extention_no")]; ?></td>
																<td style="text-align: center;" width="150"><p><?php echo $row[csf("sales_order_no")]; ?></p></td>
																<td style="text-align: center;" width="105"><?php echo $row[csf("booking_no")]; ?></td>
																<td width="80"><?php echo number_format($row[csf("batch_qnty")], 2); ?></td>
																<td style="text-align: center;" width="80"><?php echo $row[csf("batch_date")]; ?></td>
																<td style="text-align: center;" width="80"><?php echo $batch_against[$row[csf("batch_against")]]; ?></td>
																<td style="text-align: center;" width="85"><?php echo $batch_for[$row[csf("batch_for")]]; ?></td>
																<td style="text-align: center;"><?php echo $color_arr[$row[csf("color_id")]]; ?></td>
															</tr>
															<?php
															$total_batch_qnty += $row[csf("batch_qnty")];
															$i++;
														}
														?>
														<tfoot>
															<tr>
																<th colspan="5" align="right">Total</th>
																<th align="right"><? echo number_format($total_batch_qnty, 2); ?></th>
																<th></th>
																<th></th>
																<th></th>
																<th></th>
															</tr>
														</tfoot>
													</tbody>
												</table>
											</div>
										</div>
									</div>
								</fieldset>
								<?
								exit();
							}

							if($action=="dyeing_popup"){
								echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
								page_style();
								extract($_REQUEST);
								?>
								<script>
									function print_window() {
										document.getElementById('scroll_body').style.overflow = "auto";
										document.getElementById('scroll_body').style.maxHeight = "none";
										var w = window.open("Surprise", "#");
										var d = w.document.open();
										d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
											'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');
										d.close();
										document.getElementById('scroll_body').style.overflowY = "scroll";
										document.getElementById('scroll_body').style.maxHeight = "230px";
									}
								</script>
								<?
								$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
								$result = sql_select("select a.batch_no,a.batch_against,a.color_id,a.extention_no,a.sales_order_no,a.booking_no, b.item_description as febric_description, sum(b.batch_qnty) as batch_qnty,c.process_end_date,c.process_id from pro_batch_create_mst a,pro_batch_create_dtls b, pro_fab_subprocess c where a.id=b.mst_id and a.id=c.batch_id and a.color_id=$color and c.load_unload_id=2 and c.entry_form=35 and b.po_id in($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.batch_no,a.batch_against,a.color_id,a.extention_no,a.sales_order_no,a.booking_no, b.item_description,c.process_end_date,c.process_id");// and a.batch_against<>2
								?>
								<div style="width:1020px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
									style="width:100px" class="formbutton"/></div>
									<fieldset style="width:1030px; margin-left:3px">
										<div id="report_container">
											<table border="1" class="rpt_table" rules="all" width="1010" cellpadding="0" cellspacing="0">
												<thead>
													<tr>
														<th width="50">SL No</th>
														<th width="100">Batch No</th>
														<th width="70">Ext. No</th>
														<th width="150">Sales Order No</th>
														<th width="105">Booking No</th>
														<th width="80">Dyeing Quantity</th>
														<th width="80">Dyeing Date</th>
														<th width="80">Batch Against</th>
														<th width="85">Process For</th>
														<th>Color</th>
													</tr>
												</thead>
											</table>
											<div style="width:1030px; max-height:320px; overflow-y:scroll" id="scroll_body">
												<table border="1" class="rpt_table" rules="all" width="1010" cellpadding="0" cellspacing="0">
													<tbody>
														<?php
														$i = 1;
														foreach ($result as $row) {
															if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
															?>
															<tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer">
																<td style="text-align: center;" width="50"><?php echo $i; ?></td>
																<td style="text-align: center;" width="100"><?php echo $row[csf("batch_no")]; ?></td>
																<td style="text-align: center;" width="70"><?php echo $row[csf("extention_no")]; ?></td>
																<td style="text-align: center;" width="150"><p><?php echo $row[csf("sales_order_no")]; ?></p></td>
																<td style="text-align: center;" width="105"><?php echo $row[csf("booking_no")]; ?></td>
																<td width="80"><?php echo number_format($row[csf("batch_qnty")], 2); ?></td>
																<td style="text-align: center;" width="80"><?php echo $row[csf("process_end_date")]; ?></td>
																<td style="text-align: center;" width="80"><?php echo $batch_against[$row[csf("batch_against")]]; ?></td>
																<td style="text-align: center;" width="85"><?php echo $conversion_cost_head_array[$row[csf("process_id")]]; ?></td>
																<td style="text-align: center;"><?php echo $color_arr[$row[csf("color_id")]]; ?></td>
															</tr>
															<?php
															$total_batch_qnty += $row[csf("batch_qnty")];
															$i++;
														}
														?>
														<tfoot>
															<tr>
																<th colspan="5" align="right">Total</th>
																<th align="right"><? echo number_format($total_batch_qnty, 2); ?></th>
																<th></th>
																<th></th>
																<th></th>
																<th></th>
															</tr>
														</tfoot>
													</tbody>
												</table>
											</div>
										</div>
									</div>
								</fieldset>
								<?
								exit();
							}

							if($action=="finish_receive_popup"){
								echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
								page_style();
								extract($_REQUEST);
								?>
								<script>
									function print_window() {
										document.getElementById('scroll_body').style.overflow = "auto";
										document.getElementById('scroll_body').style.maxHeight = "none";
										var w = window.open("Surprise", "#");
										var d = w.document.open();
										d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
											'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');
										d.close();
										document.getElementById('scroll_body').style.overflowY = "scroll";
										document.getElementById('scroll_body').style.maxHeight = "230px";
									}
								</script>
								<?
								$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');

								$result = sql_select("select a.recv_number, a.receive_date, a.booking_no, d.batch_no, d.booking_no as sales_booking_no, d.sales_order_no, b.color_id,b.body_part_id,e.product_name_details,b.uom, c.quantity from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, pro_batch_create_mst d,product_details_master e where a.id = b.mst_id and b.id = c.dtls_id and b.batch_id = d.id and b.prod_id = e.id  and a.receive_basis in (10,14) and a.entry_form in (225) and c.entry_form in (225) and c.po_breakdown_id in($order_id) and b.color_id=$color and c.is_sales = 1 and a.status_active = 1 and b.status_active=1 and c.status_active=1 order by b.uom ASC");

								?>
								<div style="width:970px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px" class="formbutton"/></div>
								<fieldset style="width:970px; margin-left:3px">
									<div id="report_container">
										<table border="1" class="rpt_table" rules="all" width="950" cellpadding="0" cellspacing="0">
											<thead>
												<th colspan="11"><b>Finish Fabric Receive</b></th>
											</thead>
											<thead>
												<tr>
													<th width="40">SL No</th>
													<th width="120">Challan No</th>
													<th width="80">Receive Date</th>
													<th width="80">Batch No</th>
													<th width="120">Body Part</th>
													<th width="250">Fabric Description</th>
													<th width="100">Color</th>
													<th width="50">UOM</th>
													<th >Rec. Qty</th>
												</tr>
											</thead>
										</table>
										<div style="width:970px; max-height:320px; overflow-y:scroll" id="scroll_body">
											<table border="1" class="rpt_table" rules="all" width="950" cellpadding="0" cellspacing="0">
												<tbody>
													<?php
													$i = 1;
													foreach ($result as $row)
													{
														if (!in_array($row[csf("uom")], $checkUomArr))
														{
															$checkUomArr[$i] = $row[csf("uom")];
															if ($i > 1)
															{
																$sub_uom=implode(",",array_filter(array_unique(explode(",",chop($sub_uom,",")))));
																?>
																<tr style="font-weight: bold;">
																	<td colspan="8" align="right">UOM Total (<? echo $unit_of_measurement[$sub_uom]; ?>)</td>
																	<td align="right"><? echo number_format($sub_uom_total,2); ?></td>
																</tr>
																<?
																$sub_uom="";
																$sub_uom_total=0;
															}
														}
														if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
														?>
														<tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer">
															<td style="text-align: center;" width="40"><?php echo $i; ?></td>
															<td style="text-align: center;" width="120"><?php echo $row[csf("recv_number")]; ?></td>
															<td style="text-align: center;" width="80"><?php echo change_date_format($row[csf("receive_date")]); ?></td>
															<td style="text-align: center;" width="80"><p><?php echo $row[csf("batch_no")]; ?></p></td>
															<td style="text-align: center;" width="120"><p><?php echo $body_part[$row[csf("body_part_id")]]; ?></p></td>
															<td style="text-align: left;" width="250"><p><?php echo $row[csf("product_name_details")]; ?></p></td>
															<td style="text-align: center;" width="100"><?php echo $color_arr[$row[csf("color_id")]]; ?></td>
															<td style="text-align: center;" width="50"><?php echo $unit_of_measurement[$row[csf("uom")]]; ?></td>
															<td style="text-align: right;"><?php echo $row[csf("quantity")]; ?></td>
														</tr>
														<?php
														$total_receive_qnty += $row[csf("quantity")];
														$sub_uom .= $row[csf("uom")].",";
														$sub_uom_total+=$row[csf('quantity')];
														$i++;
													}
													?>
													<tfoot>
														<tr style="font-weight: bold;">
															<td colspan="8" align="right">UOM Total (<? echo $unit_of_measurement[$row[csf('uom')]]; ?>) </td>
															<td align="right"><? echo number_format($sub_uom_total,2); ?></td>
														</tr>
													<!-- <tr>
														<th colspan="8" align="right">Total</th>
														<th align="right"><? //echo number_format($total_receive_qnty, 2); ?></th>
													</tr> -->
												</tfoot>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</fieldset>
						<?
						exit();
					}

					if ($action == "finish_issue_popup") {
						echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
						page_style();
						extract($_REQUEST);
						$brand_array = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
						$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
						$supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
						$color_array = return_library_array("select id, color_name from lib_color", "id", "color_name");

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
						$order_id = explode('_', $order_id);
						?>
						<script>
							function print_window() {
								document.getElementById('scroll_body').style.overflow = "auto";
								document.getElementById('scroll_body').style.maxHeight = "none";
								var w = window.open("Surprise", "#");
								var d = w.document.open();
								d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
									'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');
								d.close();
								document.getElementById('scroll_body').style.overflowY = "scroll";
								document.getElementById('scroll_body').style.maxHeight = "230px";
							}
						</script>



						<div style="width:920px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px" class="formbutton"/></div>
						<fieldset style="width:920px; margin-left:3px">
							<div id="report_container">

								<table border="1" class="rpt_table" rules="all" width="910" cellpadding="0" cellspacing="0">
									<thead>
										<th colspan="9"><b>Finish Fabric Delivery to Garments</b></th>
									</thead>
									<thead>
										<th width="40">SL</th>
										<th width="105">Challan No</th>
										<th width="75">Delivery Date</th>
										<th width="105">Batch No</th>
										<th width="150">Body Part</th>
										<th width="250">Fabric Description</th>
										<th width="60">Fabric Color</th>
										<th width="40">UOM</th>
										<th>Del. Qnty</th>

									</thead>
								</table>

								<div id="scroll_body">
									<table border="1" class="rpt_table" rules="all" width="910" cellpadding="0" cellspacing="0">
										<tbody>
											<?
											$i = 1;
											$sql = "select a.issue_number, a.issue_date, d.batch_no,b.body_part_id,b.uom, sum(c.quantity) as  quantity,   e.detarmination_id,e.color
											from inv_issue_master a,inv_finish_fabric_issue_dtls b, order_wise_pro_details c, pro_batch_create_mst d, product_details_master e
											where a.id = b.mst_id and b.id = c.dtls_id and b.batch_id = d.id and b.prod_id = e.id  and a.entry_form = 224 and c.entry_form = 224 and c.is_sales = 1 and c.po_breakdown_id  =  $order_id[0] and d.color_id= $color
											group by a.issue_number, a.issue_date, d.batch_no,b.body_part_id,b.uom,  e.detarmination_id,e.color order by b.uom ASC";
											$result = sql_select($sql);
											foreach ($result as $row) {
												if ($i % 2 == 0)
													$bgcolor = "#E9F3FF";
												else
													$bgcolor = "#FFFFFF";
												$fab_desc = $composition_arr[$row[csf('detarmination_id')]] ;
												if (!in_array($row[csf("uom")], $checkUomArr))
												{
													$checkUomArr[$i] = $row[csf("uom")];
													if ($i > 1)
													{
														$sub_uom=implode(",",array_filter(array_unique(explode(",",chop($sub_uom,",")))));
														?>
														<tr style="font-weight: bold;">
															<td colspan="8" align="right">UOM Total (<? echo $unit_of_measurement[$sub_uom]; ?>)</td>
															<td align="right"><? echo number_format($sub_uom_total,2); ?></td>
														</tr>
														<?
														$sub_uom="";
														$sub_uom_total=0;
													}
												}
												?>
												<tr bgcolor="<? echo $bgcolor; ?>"
													onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
													<td width="40" class="center"><? echo $i; ?></td>
													<td width="105" class="center"><? echo $row[csf('issue_number')]; ?></td>
													<td width="75" class="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
													<td align="center" width="105" class="center"><? echo $row[csf('batch_no')]; ?></td>
													<td width="150" class="center"><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
													<td style="text-align: center;" width="250"><p><? echo $fab_desc; ?></p></td>
													<td style="text-align: center;width:60px;"><? echo $color_array[$row[csf('color')]]; ?></td>
													<td style="text-align: left; width:40px;"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
													<td style="text-align: right;"><? echo $row[csf('quantity')]; ?></td>

												</tr>

												<?
												$i++;
												$total_finish_issue_qnty += $row[csf('quantity')];
												$sub_uom .= $row[csf("uom")].",";
												$sub_uom_total+=$row[csf('quantity')];
											}
											?>
											<tr style="font-weight: bold;">
												<td colspan="8" align="right">UOM Total (<? echo $unit_of_measurement[$row[csf('uom')]]; ?>) </td>
												<td align="right"><? echo number_format($sub_uom_total,2); ?></td>
											</tr>
										</tbody>
									</table>

								</div>
							</div>
							<table border="1" class="rpt_table" rules="all" width="810" cellpadding="0" cellspacing="0">
								<thead>
									<th colspan="11"><b>Finish fabric issue Return</b></th>
								</thead>
								<thead>
									<th width="40">SL</th>
									<th width="105">Issue Return No</th>
									<th width="80">Issue No</th>
									<th width="75">Return Date</th>
									<th width="105">Batch No</th>
									<th width="90">Issue Qnty</th>
									<th width="160">Fabric Description</th>
								</thead>
								<?
								$total_yarn_return_qnty = 0;
								$total_yarn_return_qnty_out = 0;

								$sql = "select a.recv_number, a.receive_date, sum(c.quantity) quantity, a.booking_id, a.booking_no,  d.batch_no, e.detarmination_id
								from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, pro_batch_create_mst d , product_details_master e
								where a.id = b.mst_id and b.id = c.dtls_id and b.batch_id = d.id and b.prod_id = e.id and c.po_breakdown_id  =  $order_id[0] and c.color_id=$color and a.entry_form = 233 and c.entry_form = 233 and c.is_sales=1 and a.status_active =1 and b.status_active =1 and c.status_active=1 group by a.recv_number, a.receive_date,  a.booking_id, a.booking_no,  d.batch_no, e.detarmination_id";
								$result = sql_select($sql);
								$y=1;
								foreach ($result as $row) {
									if ($y % 2 == 0)
										$bgcolor = "#E9F3FF";
									else
										$bgcolor = "#FFFFFF";
									$fab_desc = $composition_arr[$row[csf('detarmination_id')]] ;
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $y; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $y; ?>">
										<td width="40" class="center"><? echo $y; ?></td>
										<td width="105"><p><? echo $row[csf('recv_number')]; ?></p></td>
										<td width="80"><p><? echo $row[csf('booking_no')]; ?></p></td>
										<td width="75" class="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
										<td width="105"><p><? echo $row[csf('batch_no')]; ?></p></td>
										<td width="90" class="right"><? echo $row[csf('quantity')]; ?></td>
										<td align="right" width="160"><? echo $fab_desc; ?></td>
									</tr>
									<?
									$y++;
									$return_qnty += $row[csf('quantity')];
								}

								$balance_qnty = $total_finish_issue_qnty - $return_qnty;
								?>
								<tr style="font-weight:bold">
									<td align="right" colspan="5">Total Return</td>
									<td align="right"><? echo number_format($return_qnty, 2); ?></td>
									<td align="right"></td>
								</tr>
								<tfoot>
									<tr>
										<th align="right" colspan="5">Total Balance</th>
										<th align="right"><? echo number_format($balance_qnty, 2); ?></th>
										<th align="right"></th>
									</tr>
								</tfoot>
							</table>
						</div>
					</fieldset>
					<?
					exit();
				}

				if ($action == "style_ref_search_popup")
				{
					echo load_html_head_contents("Style Reference / Job No. Info", "../../../", 1, 1, '', '', '');
					extract($_REQUEST);

					?>

					<script>

						var selected_id = new Array;
						var selected_name = new Array;

						function check_all_data() {
							var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
							tbl_row_count = tbl_row_count - 1;

							for (var i = 1; i <= tbl_row_count; i++) {
								js_set_value(i);
							}
						}

						function toggle(x, origColor) {
							var newColor = 'yellow';
							if (x.style) {
								x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
							}
						}

						function js_set_value(str) {

							toggle(document.getElementById('search' + str), '#FFFFCC');

							if (jQuery.inArray($('#txt_job_id' + str).val(), selected_id) == -1) {
								selected_id.push($('#txt_job_id' + str).val());
								selected_name.push($('#txt_job_no' + str).val());

							}
							else {
								for (var i = 0; i < selected_id.length; i++) {
									if (selected_id[i] == $('#txt_job_id' + str).val()) break;
								}
								selected_id.splice(i, 1);
								selected_name.splice(i, 1);
							}
							var id = '';
							var name = '';
							for (var i = 0; i < selected_id.length; i++) {
								id += selected_id[i] + ',';
								name += selected_name[i] + '*';
							}

							id = id.substr(0, id.length - 1);
							name = name.substr(0, name.length - 1);

							$('#hide_job_id').val(id);
							$('#hide_job_no').val(name);
						}

					</script>

				</head>

				<body>
					<div align="center">
						<form name="styleRef_form" id="styleRef_form">
							<fieldset style="width:780px;">
								<table width="550" cellspacing="0" cellpadding="0" border="1" rules="all" align="center"
								class="rpt_table" id="tbl_list">
								<thead>
									<th>Buyer Name</th>
									<th>Search By</th>
									<th id="search_by_td_up" width="170">Please Enter Sales Order No</th>
									<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:90px;"></th>
									<input type="hidden" name="hide_job_no" id="hide_job_no" value=""/>
									<input type="hidden" name="hide_job_id" id="hide_job_id" value=""/>
								</thead>
								<tbody>
									<tr>
										<td id="buyer_td">
											<?
											echo create_drop_down("cbo_po_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$companyID' $buyer_id_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $buyerID, "");
											?>
										</td>
										<td align="center">
											<?
											$search_by_arr = array(1 => "Sales Order No", 2 => "Style Ref",3 => "Booking No");
											$dd = "change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
											echo create_drop_down("cbo_search_by", 110, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
											?>
										</td>
										<td align="center" id="search_by_td">
											<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
											id="txt_search_common"/>
										</td>
										<td align="center">
											<input type="button" name="button" class="formbutton" value="Show"
											onClick="show_list_view ('<? echo $companyID; ?>**' +'<? echo $buyerID; ?>'+'**'+document.getElementById('cbo_po_buyer_name').value + '**'+'<? echo $within_group; ?>**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**', 'create_job_search_list_view', 'search_div', 'shipment_date_wise_fabric_status_report_sales_order_controller', 'setFilterGrid(\'tbl_list_search\',-1)');"
											style="width:90px;"/>
										</td>
									</tr>
								</tbody>
							</table>
							<div style="margin-top:05px" id="search_div"></div>
						</fieldset>
					</form>
				</div>
			</body>
			<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
			</html>
			<?
			exit();
		}

		if ($action == "create_job_search_list_view")
		{
			$data = explode('**', $data);
			$company_arr = return_library_array("select id,company_short_name from lib_company", 'id', 'company_short_name');
			$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');

			$company_id = $data[0];
			$buyer_id = $data[1];
			$po_buyer_id = $data[2];
			$within_group = $data[3];
			$search_by = $data[4];
			$search_string = trim($data[5]);

			$search_field_cond = '';
			if ($search_string != "") {
				if ($search_by == 1) {
					$search_field_cond = " and a.job_no like '%" . $search_string . "'";
				} else if($search_by == 2) {
					$search_field_cond = " and LOWER(a.style_ref_no) like LOWER('" . $search_string . "%')";
				}
				else
				{
					$search_field_cond = " and a.sales_booking_no like '%$search_string%'";
				}
			}

			if ($within_group == 0) $within_group_cond = ""; else $within_group_cond = " and within_group=$within_group";
	//echo "==".$_SESSION['logic_erp']["buyer_id"];die;
			if ($po_buyer_id == 0) {
				if ($_SESSION['logic_erp']["buyer_id"] != "")
				{
					if($within_group == 1)
					{
						$po_buyer_id_cond = " and a.po_buyer in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
					}
					else if($within_group == 2)
					{
						$po_buyer_id_cond = " and a.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
					}
					else
					{
						$po_buyer_id_cond = " and (a.po_buyer in (" . $_SESSION['logic_erp']["buyer_id"] .") or a.buyer_id in ( " .$_SESSION['logic_erp']["buyer_id"]. ") )";
					}
				}
				else
				{
					$po_buyer_id_cond = "";
				}
			}
			else
			{
				if($within_group == 1)
				{
					$po_buyer_id_cond = " and a.po_buyer=$po_buyer_id";
				}
				else if($within_group == 2)
				{
					$po_buyer_id_cond = " and a.buyer_id=$po_buyer_id";
				}
				else
				{
					$po_buyer_id_cond = " and (a.po_buyer=$po_buyer_id or a.buyer_id=$po_buyer_id )";
				}
			}

			if ($db_type == 0) $year_field = "YEAR(a.insert_date) as year";
			else if ($db_type == 2) $year_field = "to_char(a.insert_date,'YYYY') as year";
			else $year_field = "";

			$sql = "select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id, a.po_buyer, a.po_company_id from fabric_sales_order_mst a
			where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $within_group_cond $search_field_cond $po_buyer_id_cond order by a.id desc";

			$result = sql_select($sql);
			?>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" align="left">
				<thead>
					<th width="40">SL</th>
					<th width="115">Sales Order No</th>
					<th width="60">Year</th>
					<th width="80">Within Group</th>
					<th width="70">Sales Order Buyer</th>
					<th width="70">PO Buyer</th>
					<th width="70">PO Company</th>
					<th width="120">Sales/ Booking No</th>
					<th>Style Ref.</th>
				</thead>
			</table>
			<div style="width:820px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" align="left" id="tbl_list_search">
					<?
					$i = 1;
					foreach ($result as $row)
					{
						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						if ($row[csf('within_group')] == 1)
							$sales_order_buyer = $company_arr[$row[csf('buyer_id')]];
						else
							$sales_order_buyer = $buyer_arr[$row[csf('buyer_id')]];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $i; ?>);" id="search<? echo $i; ?>">
							<td width="40" align="center"><? echo $i; ?>
							<input type="hidden" name="txt_job_id" id="txt_job_id<?php echo $i ?>" value="<? echo $row[csf('id')]; ?>"/>
							<input type="hidden" name="txt_job_no" id="txt_job_no<?php echo $i ?>" value="<? echo $row[csf('job_no')]; ?>"/>
						</td>
						<td width="115" align="center"><p><? echo $row[csf('job_no')]; ?></p></td>
						<td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
						<td width="80" align="center"><p><? echo $yes_no[$row[csf('within_group')]]; ?>&nbsp;</p></td>
						<td width="70" align="center"><p><? echo $sales_order_buyer; ?>&nbsp;</p></td>
						<td width="70" align="center"><p><? echo $buyer_arr[$row[csf('po_buyer')]]; ?>&nbsp;</p></td>
						<td width="70" align="center"><p><? echo $company_arr[$row[csf('po_company_id')]]; ?>&nbsp;</p></td>
						<td width="120" align="center"><p><? echo $row[csf('sales_booking_no')]; ?></p></td>
						<td><p><? echo $row[csf('style_ref_no')]; ?></p></td>
					</tr>
					<?
					$i++;
				}
				?>
			</table>
		</div>
		<table width="800" cellspacing="0" cellpadding="0" style="border:none" align="left">
			<tr>
				<td align="center" height="30" valign="bottom">
					<div style="width:100%">
						<div style="width:50%; float:left" align="left">
							<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()"/> Check /
							Uncheck All
						</div>
						<div style="width:50%; float:left" align="left">
							<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton"
							value="Close" style="width:100px"/>
						</div>
					</div>
				</td>
			</tr>
		</table>
		<?
		exit();
	}

	if ($action == "booking_no_search_popup")
	{
		echo load_html_head_contents("Booking Info", "../../../", 1, 1, '', '', '');
		extract($_REQUEST);
		?>
		<script>
			/*$(function(){
	            load_drop_down( 'fabric_receive_status_report_controller',<? //echo $companyID;?>, 'load_drop_down_buyer', 'buyer_td' );
	        });*/
	        
	        var selected_id = new Array; var selected_name = new Array;
	        
	        function check_all_data()
	        {
	            var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
	            tbl_row_count = tbl_row_count - 1;

	            for( var i = 1; i <= tbl_row_count; i++ )
	            {
	                $('#tr_'+i).trigger('click'); 
	            }
	        }
	        
	        function toggle( x, origColor ) {
	            var newColor = 'yellow';
	            if ( x.style ) {
	                x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
	            }
	        }
	        
	        function js_set_value( str ) {
	            if (str!="") str=str.split("_");
	            //alert(str[0]);
	            toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
	             
	            if( jQuery.inArray( str[1], selected_id ) == -1 ) {
	                selected_id.push( str[1] );
	                selected_name.push( str[2] );
	                
	            }
	            else 
	            {
	                for( var i = 0; i < selected_id.length; i++ ) 
	                {
	                    if( selected_id[i] == str[1] ) break;
	                }
	                selected_id.splice( i, 1 );
	                selected_name.splice( i, 1 );
	            }
	            var id = ''; var name = '';
	            for( var i = 0; i < selected_id.length; i++ ) 
	            {
	                id += selected_id[i] + ',';
	                name += selected_name[i] + ',';
	            }
	            
	            id = id.substr( 0, id.length - 1 );
	            name = name.substr( 0, name.length - 1 );
	            //alert(id);
	            $('#hidden_booking_no').val( id );
	            $('#hidden_booking_num').val( name );
	        }
			/*function js_set_value(booking_no,booking_num) {
				$('#hidden_booking_no').val(booking_no);
				$('#hidden_booking_num').val(booking_num);
				parent.emailwindow.hide();
			}*/

		</script>
	</head>

	<body>
		<div align="center" style="width:730px;">
			<form name="searchwofrm" id="searchwofrm" autocomplete=off>
				<fieldset style="width:100%;">
					<legend>Enter search words</legend>
					<table cellpadding="0" cellspacing="0" width="725" class="rpt_table" border="1" rules="all">
						<thead>
							<th>Po Buyer</th>
							<th>Booking Date</th>
							<th>Search By</th>
							<th id="search_by_td_up" width="150">Please Enter Booking No</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:90px"
								class="formbutton"/>
								<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes"
								value="<? echo $companyID; ?>">
								<input type="hidden" name="cbo_within_group" id="cbo_within_group" class="text_boxes"
								value="<? echo $cbo_within_group; ?>">
								<input type="hidden" name="hidden_booking_no" id="hidden_booking_no" class="text_boxes" value="">
								<input type="hidden" name="hidden_booking_num" id="hidden_booking_num" class="text_boxes" value="">
							</th>
						</thead>
						<tr>
							<td align="center">
								<?
								echo create_drop_down("cbo_po_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$companyID' $buyer_id_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
								?>
							</td>
							<td align="center">
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker"
								style="width:70px" readonly>To
								<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"
								readonly>
							</td>
							<td align="center">
								<?
								$search_by_arr = array(1 => "Booking No", 2 => "Job No");
								$dd = "change_search_event(this.value, '0*0', '0*0', '../../') ";
								echo create_drop_down("cbo_search_by", 100, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>
							<td align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
								id="txt_search_common"/>
							</td>
							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show"
								onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_po_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_within_group').value, 'create_booking_search_list_view', 'search_div', 'shipment_date_wise_fabric_status_report_sales_order_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
								style="width:90px;"/>
							</td>
						</tr>
						<tr>
							<td colspan="5" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
						</tr>
					</table>
					<div style="width:100%; margin-top:5px; margin-left:3px" id="search_div" align="left"></div>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action == "create_booking_search_list_view") 
{
	$data = explode("_", $data);

	$search_string = trim($data[0]);
	$search_by = $data[1];
	$company_id = $data[2];
	$buyer_id = $data[3];
	$date_from = trim($data[4]);
	$date_to = trim($data[5]);
	$cbo_within_group = trim($data[6]);


	if ($date_from != "" && $date_to != "") 
	{
		if ($db_type == 0) {
			$date_cond = "and b.booking_date between '" . change_date_format(trim($date_from), "yyyy-mm-dd", "-") . "' and '" . change_date_format(trim($date_to), "yyyy-mm-dd", "-") . "'";
		} else {
			$date_cond = "and b.booking_date between '" . change_date_format(trim($date_from), '', '', 1) . "' and '" . change_date_format(trim($date_to), '', '', 1) . "'";
		}
	}

	$company_arr = return_library_array("select id,company_short_name from lib_company", 'id', 'company_short_name');
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");

	$search_field_cond = "";
	if ($search_by == 1) {
		$search_field_cond .= " and sales_booking_no like '%$search_string%'";
	}else{
		$search_field_cond .= " and po_job_no like '%$search_string%'";
	}
	if ($buyer_id != 0) {
		$search_field_cond .= " and po_buyer=$buyer_id";
	}
	if ($cbo_within_group > 0) {
		$search_field_cond .= " and within_group=$cbo_within_group";
	}
	$sql = "SELECT b.id, b.sales_booking_no as booking_no, b.booking_date, b.po_buyer, b.company_id, b.job_no, b.style_ref_no, b.po_job_no,b.booking_id from fabric_sales_order_mst b 
	where b.company_id=$company_id and b.status_active =1 and b.is_deleted=0 $search_field_cond $date_cond
	group by b.id, b.sales_booking_no, b.booking_date,b.po_buyer, b.company_id,b.job_no, b.style_ref_no,b.po_job_no,b.booking_id";
	// echo $sql;
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="80">PO Buyer</th>
			<th width="120">Booking No</th>
			<th width="90">Sales Order No</th>
			<th width="120">Style Ref.</th>
			<th width="80">Booking Date</th>
			<th>Job No.</th>
		</thead>
	</table>
	<div style="width:720px; max-height:270px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="700" class="rpt_table"
			id="tbl_list_search">
			<?
			$i = 1;
			$j = 1;
			$result = sql_select($sql);
			foreach ($result as $row) 
			{
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

				if ($row[csf('po_break_down_id')] != "") {
					$po_no = '';
					$po_ids = explode(",", $row[csf('po_break_down_id')]);
					foreach ($po_ids as $po_id) {
						if ($po_no == "") $po_no = $po_arr[$po_id]; else $po_no .= "," . $po_arr[$po_id];
					}
				}
				?>
				<tr id="tr_<? echo $i; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
					onClick="js_set_value('<? echo $i; ?>_<? echo $row[csf('booking_no')]; ?>_<? echo $row[csf('booking_no_prefix_num')]; ?>')">
					<td width="40"><? echo $i; ?></td>
					<td width="80" align="center"><p><? echo $buyer_arr[$row[csf('po_buyer')]]; ?>&nbsp;</p></td>
					<td width="120"><p><? echo $row[csf('booking_no')]; ?></p></td>
					<td width="90" align="center"><p><? echo $row[csf('job_no')]; ?>&nbsp;</p></td>
					<td width="120"><p><? echo $row[csf('style_ref_no')]; ?>&nbsp;</p></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
					<td><? echo $row[csf('po_job_no')]; ?></td>
				</tr>
				<?
				$i++;
			}

			/*$sql_partial = "SELECT a.id, a.booking_no,a.booking_no_prefix_num, a.booking_date,a.buyer_id, a.company_id, a.delivery_date, a.currency_id, listagg(c.po_break_down_id, ',') within group (order by c.po_break_down_id) as po_break_down_id, c.job_no 
			from wo_booking_mst a, wo_booking_dtls c,fabric_sales_order_mst b 
			where a.booking_no=c.booking_no and a.booking_no=b.sales_booking_no and a.status_active =1 and a.is_deleted =0 and a.pay_mode=5 and a.fabric_source in(1,2) and a.item_category=2 $buyer_id_cond $search_field_cond $date_cond and a.entry_form=108 
			group by a.id, a.booking_no,a.booking_no_prefix_num,a.booking_date,a.buyer_id,a.company_id,a.delivery_date,a.currency_id,c.job_no";
			// echo $sql_partial;
			$result_partial = sql_select($sql_partial);
			foreach ($result_partial as $row) 
			{
				if ($j % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

				if ($row[csf('po_break_down_id')] != "") 
				{
					$po_no = '';
					$po_ids = array_unique(explode(",", $row[csf('po_break_down_id')]));
					foreach ($po_ids as $po_id) {
						if ($po_no == "") $po_no = $po_arr[$po_id]; else $po_no .= "," . $po_arr[$po_id];
					}
				}
				?>
				<tr id="tr_<? echo $i; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
					onClick="js_set_value('<? echo $i; ?>_<? echo $row[csf('booking_no')]; ?>_<? echo $row[csf('booking_no_prefix_num')]; ?>')">
					<td width="40"><? echo $j; ?>p</td>
					<td width="80" align="center"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
					<td width="120"><p><? echo $row[csf('booking_no')]; ?></p></td>
					<td width="90" align="center"><p><? echo $row[csf('job_no')]; ?>&nbsp;</p></td>
					<td width="120"><p><? echo $row[csf('style_ref_no')]; ?>&nbsp;</p></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
					<td><p><? echo $po_no; ?>&nbsp;</p></td>
				</tr>
				<?
				$j++;
			}*/
			?>
		</table>
		<div style="width:100%">
			<div style="width:50%; float:left" align="left">
				<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()"> Check / Uncheck All
			</div>
			<div style="width:50%; float:left" align="left">
				<input type="button" name="close" id="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px">
			</div>
		</div>
	</div>
	<?
	exit();
}

if ($action == "order_popup") {
	echo load_html_head_contents("Order Info", "../../../", 1, 1, $unicode, '', '');
	extract($_REQUEST);
	$job_no=explode("__",$job_no);

	$po_info = sql_select("select a.job_no,a.style_ref_no,b.po_number from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst   and a.job_no='$job_no[0]' and pub_shipment_date='$job_no[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table" id="tbl_list_search">
		<thead>
			<th width="30">SL</th>
			<th width="115">Job No</th>
			<th width="75">Style Reference No</th>
			<th width="60">PO Number</th>
		</thead>
		<tbody>
			<?php
			$i = 1;
			foreach ($po_info as $row) {
				?>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td width="30" align="center"><? echo $i; ?></td>
					<td width="115" align="center"><? echo $row[csf('job_no')]; ?></td>
					<td width="75" align="center"><? echo $row[csf('style_ref_no')]; ?></td>
					<td width="60" align="center"><? echo $row[csf('po_number')]; ?></td>
				</tr>
				<?php
				$i++;
			}
			?>
		</tbody>
	</table>
	<?php
	exit();
}

if($action=="fabric_receive")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$color_array = return_library_array("select id, color_name from lib_color", "id", "color_name");
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
	<div style="width:1075px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:1070px; margin-left:3px">
		<div id="report_container">

			<table border="1" class="rpt_table" rules="all" width="1050" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="11"><b>Finish Fabric Production Deatails</b></th>
				</thead>
				<thead>
					<th width="30">SL</th>
					<th width="120">System Id</th>
					<th width="75">Production Date</th>
					<th width="100">Batch No</th>
					<!-- <th width="80">Rec. Basis</th> -->
					<th width="100">Dyeing Source</th>
					<th width="100">Dyeing Company</th>
					<th width="120">Body part</th>
					<th width="190">Fabric Description</th>
					<th width="100">Fabric Color</th>
					<th width="40">UOM</th>
					<th>Prod. Qnty</th>

				</thead>
			</table>
			<div style="width:1070px; max-height:320px; overflow-y:scroll" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="1050" cellpadding="0" cellspacing="0">
					<?
					$i=1;
					$total_fabric_recv_qnty=0; $dye_company='';
					$sql="select a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id,b.body_part_id,b.uom, d.batch_no, b.prod_id, sum(c.quantity) as quantity,e.product_name_details,e.color from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, pro_batch_create_mst d,  product_details_master e where a.id=b.mst_id and b.id=c.dtls_id and b.batch_id = d.id and b.prod_id = e.id and a.entry_form in (7) and c.entry_form in (7) and c.po_breakdown_id in($order_id)  and b.is_sales =1 and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id,b.body_part_id,b.uom,d.batch_no, b.prod_id,e.product_name_details,e.color order by b.uom";
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

						if (!in_array($row[csf("uom")], $checkUomArr))
						{
							$checkUomArr[$i] = $row[csf("uom")];
							if ($i > 1)
							{
								$sub_uom=implode(",",array_filter(array_unique(explode(",",chop($sub_uom,",")))));
								?>
								<tr style="font-weight: bold;">
									<td colspan="10" align="right">UOM Total (<? echo $unit_of_measurement[$sub_uom]; ?>)</td>
									<td align="right"><? echo number_format($sub_uom_total,2); ?></td>
								</tr>
								<?
								$sub_uom="";
								$sub_uom_total=0;
							}
						}

						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><? echo $i; ?></td>
							<td align="center" width="120"><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
							<td align="center" width="100"><p><? echo $row[csf('batch_no')]; ?></p></td>
							<!-- <td width="80"><? //echo $receive_basis_arr[$row[csf('receive_basis')]]; ?></td> -->
							<td align="center" width="100"><? echo $knitting_source[$row[csf('knitting_source')]]; ?></td>
							<td align="center" width="100"><p><? echo $dye_company; ?></p></td>
							<td align="center" width="120"><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
							<td width="190"><p><? echo $row[csf('product_name_details')]; ?></p></td>
							<td align="center" width="100"><p><? echo $color_array[$row[csf('color')]] ; ?></p></td>
							<td width="40"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
							<td align="right"><? echo number_format($row[csf('quantity')],2); ?></td>

						</tr>
						<?
						$sub_uom .= $row[csf("uom")].",";
						$sub_uom_total+=$row[csf('quantity')];
						$i++;
					}
					?>

					<tr style="font-weight: bold;">
						<td colspan="10" align="right">UOM Total (<? echo $unit_of_measurement[$row[csf('uom')]]; ?>) </td>
						<td align="right"><? echo number_format($sub_uom_total,2); ?></td>
					</tr>
                    <!-- <tfoot>
                        <th colspan="10" align="right">Total</th>
                        <th align="right"><? //echo number_format($total_fabric_recv_qnty,2); ?></th>
                    </tfoot> -->
                </table>
            </div>
        </div>
    </fieldset>
    <?
    exit();
}

if($action=="finish_delivery_to_store")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$ex_data=explode('_',$order_id);
	$color_arr = return_library_array("select id, color_name from lib_color","id","color_name");
	?>
	<script>

		var tableFilters = {
			col_operation: {
				id: ["value_delivery_qnty"],
				col: [8],
				operation: ["sum"],
				write_method: ["innerHTML"]
			}
		}
		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1,tableFilters);
		});

		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";

			$('#tbl_list_search tr:first').hide();

			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";

			$('#tbl_list_search tr:first').show();
		}

	</script>
	<div style="width:970px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:970px;">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="970" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="10"><b>Finish Delivery To Store Info</b></th>
				</thead>
				<thead>
					<th width="30">SL</th>
					<th width="115">Challan No</th>
					<th width="80">Delivery Date</th>
					<th width="100">Batch No</th>
					<th width="120">Body Part</th>
					<th width="250">Fabric Description</th>
					<th width="70">Color</th>
					<th width="40">UOM</th>
					<th>Delivery Qnty</th>
				</thead>
			</table>
			<div style="width:990px; max-height:380px; overflow-y:scroll" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="970" cellpadding="0" cellspacing="0" id="tbl_list_searchs">
					<?
					$i=1; $total_delivery_fin_qnty=0;
					$sql="select a.sys_number_prefix_num, a.sys_number, a.delevery_date, b.grey_sys_number, b.product_id, b.construction, b.composition, b.gsm,b.bodypart_id,b.uom,b.batch_id, b.dia, sum(b.current_delivery) as delivery_qty, c.product_name_details, c.color from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b, product_details_master c where a.id=b.mst_id and b.product_id=c.id and a.entry_form in (54) and b.order_id in ($order_id) and c.color='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.is_sales = 1 group by a.sys_number_prefix_num, a.sys_number, a.delevery_date, b.grey_sys_number, b.product_id, b.construction, b.composition, b.gsm,b.bodypart_id,b.uom,b.batch_id, b.dia, c.product_name_details, c.color order by b.uom ASC";
					$result=sql_select($sql);
					$batch_ids="";
					foreach($result as $row)
					{
						$batch_ids.=$row[csf('batch_id')].',';
					}
					$batch_ids=chop($batch_ids,",");
					$batchNo_arr = return_library_array("select id, batch_no from pro_batch_create_mst where id in($batch_ids)","id","batch_no");

					foreach($result as $row)
					{
						if (!in_array($row[csf("uom")], $checkUomArr))
						{
							$checkUomArr[$i] = $row[csf("uom")];
							if ($i > 1)
							{
								$sub_uom=implode(",",array_filter(array_unique(explode(",",chop($sub_uom,",")))));
								?>
								<tr style="font-weight: bold;">
									<td colspan="8" align="right">UOM Total (<? echo $unit_of_measurement[$sub_uom]; ?>)</td>
									<td align="right"><? echo number_format($sub_uom_total,2); ?></td>
								</tr>
								<?
								$sub_uom="";
								$sub_uom_total=0;
							}
						}

						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><? echo $i; ?></td>
							<td align="center" width="115"><p><? echo $row[csf('sys_number')]; ?></p></td>
							<td align="center" width="80"><p><? echo change_date_format($row[csf('delevery_date')]); ?></p></td>
							<td align="center" width="100"><p><? echo $batchNo_arr[$row[csf('batch_id')]]; ?></p></td>
							<td align="center" width="120"><p><? echo $body_part[$row[csf('bodypart_id')]]; ?></p></td>
							<td width="250"><p><? echo $row[csf('product_name_details')]; ?>&nbsp;</p></td>
							<td align="center" width="70"><? echo $color_arr[$row[csf('color')]]; ?></td>
							<td width="40"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
							<td align="right"><? echo number_format($row[csf('delivery_qty')],2,'.',''); ?></td>
						</tr>
						<?
						$total_delivery_fin_qnty+=$row[csf('delivery_qty')];
						$sub_uom .= $row[csf("uom")].",";
						$sub_uom_total+=$row[csf('delivery_qty')];
						$i++;
					}
					?>
					<tfoot>
						<tr style="font-weight: bold;">
							<td colspan="8"  align="right" >UOM Total (<? echo $unit_of_measurement[$row[csf('uom')]]; ?>) </td>
							<td align="right"><? echo number_format($sub_uom_total,2); ?></td>
						</tr>
					</tfoot>
				</table>
			</div>

		</div>
	</fieldset>
	<?
	exit();
}


if ($action == "finish_trans_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
	page_style();
	extract($_REQUEST);
	$store_arr = return_library_array("select id, store_name from lib_store_location", "id", "store_name");
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$deter_array=sql_select($sql_deter);

	if(count($deter_array)>0)
	{
		foreach( $deter_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				$constructionArr[$row[csf('id')]]=$constructionArr[$row[csf('id')]];
				list($cst,$cps)=explode(',',$composition_arr[$row[csf('id')]]);
				$copmpositionArr[$row[csf('id')]]=$cps;
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				$constructionArr[$row[csf('id')]]=$row[csf('construction')];
				list($cst,$cps)=explode(',',$composition_arr[$row[csf('id')]]);
				$copmpositionArr[$row[csf('id')]]=$cps;
			}
		}
	}
	unset($deter_array);
	?>
	<script>

		function print_window() {
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

			d.close();
		}

	</script>
	<div style="width:975px" align="center"><input type="button" value="Print Preview" onClick="print_window()" Style="width:100px" class="formbutton"/></div>
	<fieldset style="width:980px; margin:auto;">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="100%" cellpadding="0" cellspacing="0">
				<thead>
					<tr>
						<th colspan="11">Transfer In</th>
					</tr>
					<tr>
						<th width="40">SL</th>
						<th width="115">Transfer Id</th>
						<th width="80">Transfer Date</th>
						<th width="100">From Order</th>
						<th width="100">Batch No</th>
						<th width="70">Fab. Shade</th>
						<th width="100">Body Part</th>
						<th width="70">Dia/W.Type</th>
						<th width="110">Fabric Description</th>
						<th width="80">Store Name</th>
						<th>Transfer In Qnty</th>
					</tr>

				</thead>
				<?
				$i = 1;
				$total_trans_in_qnty = 0;
				if($db_type ==1)
				{
					$null_cond = " and c.quantity =''";
				}else{
					$null_cond = " and c.quantity is not null";
				}
				$sql = "select a.transfer_system_id,a.transfer_date,a.from_order_id,d.job_no,b.batch_id,e.batch_no, b.fabric_shade, b.body_part_id, b.dia_width_type, b.feb_description_id, b.to_store,  c.quantity from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, fabric_sales_order_mst d, pro_batch_create_mst e where a.id = b.mst_id and b.id = c.dtls_id and a.from_order_id = d.id and c.trans_type=5 and b.batch_id = e.id and c.color_id =$color and a.to_order_id = $order_id and c.entry_form = 230 and a.entry_form = 230 and a.status_active =1 and a.is_deleted =0 and c.quantity <> 0 and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and d.status_active=1 and a.is_deleted=0 and c.is_sales=1";
				$result = sql_select($sql);
				foreach ($result as $row) {
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>"
						onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td width="40"><? echo $i; ?></td>
						<td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
						<td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
						<td width="100"><p><? echo $row[csf('job_no')]; ?></p></td>
						<td width="100"><p><? echo $row[csf('batch_no')]; ?></p></td>
						<td width="70"><p><? echo $fabric_shade[$row[csf('fabric_shade')]]; ?></p></td>
						<td width="100"><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
						<td width="70"><p><? echo $fabric_typee[$row[csf('dia_width_type')]]; ?></p></td>
						<td width="110"><p><? echo $composition_arr[$row[csf('feb_description_id')]]; ?></p></td>
						<td width="80"><p><? echo $store_arr[$row[csf('to_store')]]; ?></p></td>
						<td align="right"><? echo number_format($row[csf('quantity')], 2); ?> </td>
					</tr>
					<?
					$total_trans_in_qnty += $row[csf('quantity')];
					$i++;
				}
				?>
				<tr style="font-weight:bold;background-color: #e0e0e0;">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td align="right">Total</td>
					<td align="right"><? echo number_format($total_trans_in_qnty, 2); ?></td>
				</tr>
				<thead>
					<tr>
						<th colspan="11">Transfer Out</th>
					</tr>
					<tr>
						<th width="40">SL</th>
						<th width="115">Transfer Id</th>
						<th width="80">Transfer Date</th>
						<th width="100">To Order</th>
						<th width="100">Batch No</th>
						<th width="70">Fab. Shade</th>
						<th width="100">Body Part</th>
						<th width="70">Dia/W.Type</th>
						<th width="110">Fabric Description</th>
						<th width="80">Store Name</th>
						<th>Transfer Qnty</th>
					</tr>
				</thead>
				<?
				$total_trans_out_qnty = 0;
				$y =1;
				$sql = "select a.transfer_system_id,a.transfer_date,a.to_order_id,d.job_no,b.batch_id,e.batch_no, b.fabric_shade, b.body_part_id, b.dia_width_type, b.feb_description_id, b.from_store, c.quantity from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, fabric_sales_order_mst d, pro_batch_create_mst e where a.id = b.mst_id and b.id = c.dtls_id and a.to_order_id = d.id and c.trans_type=6 and b.batch_id = e.id  and  c.color_id =$color and a.from_order_id = $order_id and c.entry_form = 230 and a.entry_form = 230 and a.status_active =1 and a.is_deleted =0 and c.quantity is not null and  c.quantity <> 0 and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and d.status_active=1 and a.is_deleted=0 and c.is_sales=1";
				$result = sql_select($sql);
				foreach ($result as $row) {
					if ($y % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>"
						onclick="change_color('tr_<? echo $y; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $y; ?>">
						<td width="40"><? echo $i; ?></td>
						<td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
						<td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
						<td width="100"><p><? echo $row[csf('job_no')]; ?></p></td>
						<td width="100"><p><? echo $row[csf('batch_no')]; ?></p></td>
						<td width="70"><p><? echo $fabric_shade[$row[csf('fabric_shade')]]; ?></p></td>
						<td width="100"><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
						<td width="70"><p><? echo $fabric_typee[$row[csf('dia_width_type')]]; ?></p></td>
						<td width="110"><p><? echo $composition_arr[$row[csf('feb_description_id')]]; ?></p></td>
						<td width="80"><p><? echo $store_arr[$row[csf('from_store')]]; ?></p></td>
						<td align="right"><? echo number_format($row[csf('quantity')], 2); ?> </td>
					</tr>
					<?
					$total_trans_out_qnty += $row[csf('quantity')];
					$y++;
				}
				?>
				<tr style="font-weight:bold;background-color: #e0e0e0;">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td align="right">Total</td>
					<td align="right"><? echo number_format($total_trans_out_qnty, 2); ?></td>
				</tr>
				<tfoot>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th>Net Transfer</th>
					<th><? echo number_format($total_trans_in_qnty - $total_trans_out_qnty, 2); ?></th>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<?
	exit();
}

if ($action == "finish_trans_in_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
	page_style();
	extract($_REQUEST);
	$store_arr = return_library_array("select id, store_name from lib_store_location", "id", "store_name");
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$deter_array=sql_select($sql_deter);

	if(count($deter_array)>0)
	{
		foreach( $deter_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				$constructionArr[$row[csf('id')]]=$constructionArr[$row[csf('id')]];
				list($cst,$cps)=explode(',',$composition_arr[$row[csf('id')]]);
				$copmpositionArr[$row[csf('id')]]=$cps;
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				$constructionArr[$row[csf('id')]]=$row[csf('construction')];
				list($cst,$cps)=explode(',',$composition_arr[$row[csf('id')]]);
				$copmpositionArr[$row[csf('id')]]=$cps;
			}
		}
	}
	unset($deter_array);
	?>
	<script>

		function print_window() {
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

			d.close();
		}

	</script>
	<div style="width:975px" align="center"><input type="button" value="Print Preview" onClick="print_window()" Style="width:100px" class="formbutton"/></div>
	<fieldset style="width:980px; margin:auto;">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="100%" cellpadding="0" cellspacing="0">
				<thead>
					<tr>
						<th colspan="11">Transfer In</th>
					</tr>
					<tr>
						<th width="40">SL</th>
						<th width="115">Transfer Id</th>
						<th width="80">Transfer Date</th>
						<th width="100">From Order</th>
						<th width="100">Batch No</th>
						<th width="70">Fab. Shade</th>
						<th width="100">Body Part</th>
						<th width="70">Dia/W.Type</th>
						<th width="110">Fabric Description</th>
						<th width="80">Store Name</th>
						<th>Transfer In Qnty</th>
					</tr>

				</thead>
				<?
				$i = 1;
				$total_trans_in_qnty = 0;
				if($db_type ==1)
				{
					$null_cond = " and c.quantity =''";
				}else{
					$null_cond = " and c.quantity is not null";
				}
				$sql = "select a.transfer_system_id,a.transfer_date,a.from_order_id,d.job_no,b.batch_id,e.batch_no, b.fabric_shade, b.body_part_id, b.dia_width_type, b.feb_description_id, b.to_store,  c.quantity from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, fabric_sales_order_mst d, pro_batch_create_mst e where a.id = b.mst_id and b.id = c.dtls_id and a.from_order_id = d.id and c.trans_type=5 and b.batch_id = e.id and c.color_id =$color and a.to_order_id = $order_id and c.entry_form = 230 and a.entry_form = 230 and a.status_active =1 and a.is_deleted =0 and c.quantity <> 0 and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and d.status_active=1 and a.is_deleted=0 and c.is_sales=1";
				$result = sql_select($sql);
				foreach ($result as $row) {
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>"
						onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td width="40"><? echo $i; ?></td>
						<td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
						<td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
						<td width="100"><p><? echo $row[csf('job_no')]; ?></p></td>
						<td width="100"><p><? echo $row[csf('batch_no')]; ?></p></td>
						<td width="70"><p><? echo $fabric_shade[$row[csf('fabric_shade')]]; ?></p></td>
						<td width="100"><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
						<td width="70"><p><? echo $fabric_typee[$row[csf('dia_width_type')]]; ?></p></td>
						<td width="110"><p><? echo $composition_arr[$row[csf('feb_description_id')]]; ?></p></td>
						<td width="80"><p><? echo $store_arr[$row[csf('to_store')]]; ?></p></td>
						<td align="right"><? echo number_format($row[csf('quantity')], 2); ?> </td>
					</tr>
					<?
					$total_trans_in_qnty += $row[csf('quantity')];
					$i++;
				}
				?>

				<tfoot>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th>Total</th>
					<th><? echo number_format($total_trans_in_qnty, 2); ?></th>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<?
	exit();
}

if ($action == "finish_trans_out_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
	page_style();
	extract($_REQUEST);
	$store_arr = return_library_array("select id, store_name from lib_store_location", "id", "store_name");
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$deter_array=sql_select($sql_deter);

	if(count($deter_array)>0)
	{
		foreach( $deter_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				$constructionArr[$row[csf('id')]]=$constructionArr[$row[csf('id')]];
				list($cst,$cps)=explode(',',$composition_arr[$row[csf('id')]]);
				$copmpositionArr[$row[csf('id')]]=$cps;
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				$constructionArr[$row[csf('id')]]=$row[csf('construction')];
				list($cst,$cps)=explode(',',$composition_arr[$row[csf('id')]]);
				$copmpositionArr[$row[csf('id')]]=$cps;
			}
		}
	}
	unset($deter_array);
	?>
	<script>

		function print_window() {
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

			d.close();
		}

	</script>
	<div style="width:975px" align="center"><input type="button" value="Print Preview" onClick="print_window()" Style="width:100px" class="formbutton"/></div>
	<fieldset style="width:980px; margin:auto;">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="100%" cellpadding="0" cellspacing="0">
				<thead>
					<tr>
						<th colspan="11">Transfer Out</th>
					</tr>
					<tr>
						<th width="40">SL</th>
						<th width="115">Transfer Id</th>
						<th width="80">Transfer Date</th>
						<th width="100">To Order</th>
						<th width="100">Batch No</th>
						<th width="70">Fab. Shade</th>
						<th width="100">Body Part</th>
						<th width="70">Dia/W.Type</th>
						<th width="110">Fabric Description</th>
						<th width="80">Store Name</th>
						<th>Transfer Qnty</th>
					</tr>
				</thead>
				<?
				$total_trans_out_qnty = 0;
				$y =1;
				$sql = "select a.transfer_system_id,a.transfer_date,a.to_order_id,d.job_no,b.batch_id,e.batch_no, b.fabric_shade, b.body_part_id, b.dia_width_type, b.feb_description_id, b.from_store, c.quantity from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, fabric_sales_order_mst d, pro_batch_create_mst e where a.id = b.mst_id and b.id = c.dtls_id and a.to_order_id = d.id and c.trans_type=6 and b.batch_id = e.id  and  c.color_id =$color and a.from_order_id = $order_id and c.entry_form = 230 and a.entry_form = 230 and a.status_active =1 and a.is_deleted =0 and c.quantity is not null and  c.quantity <> 0 and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and d.status_active=1 and a.is_deleted=0 and c.is_sales=1";
				$result = sql_select($sql);
				foreach ($result as $row) {
					if ($y % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>"
						onclick="change_color('tr_<? echo $y; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $y; ?>">
						<td width="40"><? echo $i; ?></td>
						<td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
						<td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
						<td width="100"><p><? echo $row[csf('job_no')]; ?></p></td>
						<td width="100"><p><? echo $row[csf('batch_no')]; ?></p></td>
						<td width="70"><p><? echo $fabric_shade[$row[csf('fabric_shade')]]; ?></p></td>
						<td width="100"><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
						<td width="70"><p><? echo $fabric_typee[$row[csf('dia_width_type')]]; ?></p></td>
						<td width="110"><p><? echo $composition_arr[$row[csf('feb_description_id')]]; ?></p></td>
						<td width="80"><p><? echo $store_arr[$row[csf('from_store')]]; ?></p></td>
						<td align="right"><? echo number_format($row[csf('quantity')], 2); ?> </td>
					</tr>
					<?
					$total_trans_out_qnty += $row[csf('quantity')];
					$y++;
				}
				?>
				<tfoot>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th>Total</th>
					<th><? echo number_format($total_trans_out_qnty, 2); ?></th>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<?
	exit();
}
?>