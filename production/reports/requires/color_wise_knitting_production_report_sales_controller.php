<?

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
//--------------------------------------------------------------------------------------------------------------------
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];



if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "" );     	 
	exit();
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor_id", 120, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and company_id='$data' and production_process=2  order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );
}

$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$floor_name = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_name= str_replace("'","",$cbo_company_name);
	$cbo_within_group= str_replace("'","",$cbo_within_group);
	$cbo_floor_id= str_replace("'","",$cbo_floor_id);
	$cbo_buyer_name= str_replace("'","",$cbo_buyer_name);
	$fso_number= str_replace("'","",$fso_number);

	if($cbo_buyer_name)
	{
		$buyer_id_cond=" and ((a.po_buyer=$cbo_buyer_name and a.within_group=1) or (a.buyer_id=$cbo_buyer_name and a.within_group=2))";
		
	}
	
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		$date_cond=" and e.receive_date between $txt_date_from and $txt_date_to ";
	}

	
	if($cbo_floor_id !=0) $floor_cond=" and d.floor_id=$cbo_floor_id";
	if($cbo_within_group !=0) $within_group_cond=" and a.within_group=$cbo_within_group";

	$sales_orders_cond="";
	$sales_orders_cond2="";
	if($fso_number != "")
	{
		$sales_orders="";
		foreach (explode(",", $fso_number) as $row)
		{
			$sales_orders.= ($sales_orders=="") ? "".$row."" : ",".$row."";
		}

		if($sales_orders)
		{
			$sales_orders_cond ="and a.id in ($sales_orders)";
			$sales_orders_cond2 ="and c.po_breakdown_id in ($sales_orders)";
		}
	}

	$con = connect();
	$r_id=execute_query("delete from tmp_po_id where user_id=$user_id ");
	if($r_id)
	{
		oci_commit($con);
	}

	$sql="SELECT e.id as production_id,a.id as fso_id, a.job_no, a.company_id, a.within_group, a.buyer_id, a.po_buyer,a.sales_booking_no, a.job_no_prefix_num,a.style_ref_no, a.booking_type, a.booking_entry_form, a.booking_without_order, a.po_job_no, b.fabric_desc, b.determination_id, b.color_id, b.body_part_id, b.gsm_weight,b.dia, b.id as fso_dtls_id, b.grey_qty, b.finish_qty, c.quantity, c.id as prop_id, d.floor_id, e.receive_date, e.knitting_source,d.color_id as fabric_color_id
	from fabric_sales_order_mst a, fabric_sales_order_dtls b, order_wise_pro_details c, pro_grey_prod_entry_dtls d, inv_receive_master e
	where a.company_id = $company_name and a.id=b.mst_id and a.id=c.po_breakdown_id and c.entry_form=2 and c.dtls_id= d.id and d.mst_id=e.id and e.entry_form=2 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $sales_orders_cond $within_group_cond $floor_cond $buyer_id_cond $date_cond   
	group by  e.id,a.id , a.job_no, a.company_id, a.within_group, a.buyer_id, a.po_buyer,a.sales_booking_no, a.job_no_prefix_num,a.style_ref_no, a.booking_type, a.booking_entry_form, 
	a.booking_without_order, a.po_job_no, b.fabric_desc, b.determination_id, b.color_id, b.body_part_id, b.gsm_weight,b.dia, b.id , b.grey_qty, b.finish_qty, c.quantity, c.id , d.floor_id, e.receive_date, e.knitting_source,d.color_id  order by a.id";

	//echo $sql;die;
		 
	$sql_result=sql_select($sql);
	$details_data=$fsoNoChk=$fsoDtlsChk=$propIdChk=array();$arrDelivery=array();
	$booking_type_arr=array("118"=>"Main","108"=>"Partial","88"=>"Short","89"=>"Sample With Order","90"=>"Sample Without Order");
	$con = connect();
	foreach($sql_result as $row)
	{
		$fabric_string = $row[csf("determination_id")]."*".$row[csf("color_id")]."*".$row[csf("body_part_id")]."*".$row[csf("gsm_weight")]."*".$row[csf("dia")];
		$arrDelivery[$row[csf("production_id")]][$row[csf("fso_id")]] = $row[csf("body_part_id")];
		if($fsoDtlsChk[$row[csf("fso_dtls_id")]] =="")
		{
			$fsoDtlsChk[$row[csf("fso_dtls_id")]] = $row[csf("fso_dtls_id")];
			$details_data[$row[csf("job_no")]][$row[csf("sales_booking_no")]][$fabric_string]["grey_qty"]+=$row[csf("grey_qty")];
			$details_data[$row[csf("job_no")]][$row[csf("sales_booking_no")]][$fabric_string]["finish_qty"]+=$row[csf("finish_qty")];
		}

		//FSO Row count for transfer column
		if($fso_row_counts[$row[csf("job_no")]][$row[csf("sales_booking_no")]][$fabric_string] =="")
		{
			$fso_row_counts[$row[csf("job_no")]][$row[csf("sales_booking_no")]][$fabric_string] = $row[csf("job_no")]."=".$row[csf("sales_booking_no")]."=".$fabric_string;
			$fso_row_count[$row[csf("fso_id")]]++;
		}
		else
		{
			$fso_row_counts[$row[csf("job_no")]][$row[csf("sales_booking_no")]][$fabric_string] = $row[csf("job_no")]."=".$row[csf("sales_booking_no")]."=".$fabric_string;
		}

		if($row[csf("within_group")] ==1)
		{
			$details_data[$row[csf("job_no")]][$row[csf("sales_booking_no")]][$fabric_string]["buyer_id"]=$row[csf("po_buyer")];
		}
		else
		{
			$details_data[$row[csf("job_no")]][$row[csf("sales_booking_no")]][$fabric_string]["buyer_id"]=$row[csf("buyer_id")];
		}
		

		

		$bookingType="";
		if($row[csf('booking_type')] == 4)
		{
			if($row[csf('booking_without_order')] == 1)
			{
				$bookingType = "Sample Without Order";
			}
			else
			{
				$bookingType =  "Sample With Order";
			}
		}
		else
		{
			$bookingType =  $booking_type_arr[$row[csf('booking_entry_form')]];
		}

		$details_data[$row[csf("job_no")]][$row[csf("sales_booking_no")]][$fabric_string]["style_ref_no"]=$row[csf("style_ref_no")];
		$details_data[$row[csf("job_no")]][$row[csf("sales_booking_no")]][$fabric_string]["company_id"]=$row[csf("company_id")];
		$details_data[$row[csf("job_no")]][$row[csf("sales_booking_no")]][$fabric_string]["booking_type"]=$bookingType;
		$details_data[$row[csf("job_no")]][$row[csf("sales_booking_no")]][$fabric_string]["po_job_no"]=$row[csf("po_job_no")];
		$details_data[$row[csf("job_no")]][$row[csf("sales_booking_no")]][$fabric_string]["fso_id"]=$row[csf("fso_id")];

		
		if($all_fso_id[$row[csf("fso_id")]] ==""){
			$all_fso_id[$row[csf("fso_id")]] =$row[csf("fso_id")];
			execute_query("insert into tmp_po_id (user_id, po_id) values ($user_id,".$row[csf("fso_id")].")");
		}
	}
	oci_commit($con);

	$sql_program = sql_select("SELECT b.po_id, a.knitting_source, sum(b.program_qnty) as program_qnty, a.color_id ,b.body_part_id from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b, tmp_po_id c where a.id=b.dtls_id and b.po_id=c.po_id and c.user_id=$user_id and b.is_sales=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_id, a.knitting_source, a.color_id ,b.body_part_id");

	foreach ($sql_program as $val) 
	{
		$fso_program_arr[$val[csf("po_id")]][$val[csf("color_id")]][$val[csf("knitting_source")]][$val[csf("body_part_id")]] += $val[csf("program_qnty")];
	}

	$alloc_qty_arr = return_library_array("SELECT sum(a.qnty) as alloc_qty, a.po_break_down_id from inv_material_allocation_dtls a, tmp_po_id b  where a.po_break_down_id =b.po_id and b.user_id=$user_id group by a.po_break_down_id","po_break_down_id","alloc_qty");
	
	$requisition_sql = sql_select("SELECT b.po_id, c.knitting_source, sum(a.yarn_qnty) as requ_qnty, c.color_id from ppl_yarn_requisition_entry a, ppl_planning_entry_plan_dtls b, ppl_planning_info_entry_dtls c, tmp_po_id d where a.knit_id=b.dtls_id and b.is_sales=1 and a.status_active=1 and b.status_active=1 and b.dtls_id=c.id and b.po_id=d.po_id and d.user_id=$user_id and b.is_sales=1 group by b.po_id,c.knitting_source, c.color_id");

	foreach ($requisition_sql as $val) 
	{
		$requisition_qty_arr[$val[csf("po_id")]][$val[csf("color_id")]][$val[csf("knitting_source")]] += $val[csf("requ_qnty")];
	}

	$delivery_data_array = sql_select("SELECT c.po_breakdown_id, c.barcode_no, b.color_id from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, tmp_po_id d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.po_id and d.user_id=$user_id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0  ");

	$deliveryArr = array();
	foreach ($delivery_data_array as $val) 
	{
		$deliveryArr[$val[csf("po_breakdown_id")]][$val[csf("barcode_no")]]['color_id'] = $val[csf("color_id")];
	}
	unset($delivery_data_array);

	
	$sr_data_array = sql_select("SELECT c.po_breakdown_id, c.barcode_no, b.color_id from pro_grey_prod_entry_dtls b, pro_roll_details c, tmp_po_id d WHERE  b.id=c.dtls_id and c.po_breakdown_id=d.po_id and d.user_id=$user_id and c.entry_form in(22,58) and c.status_active=1 and c.is_deleted=0  ");

	$srArr = array();
	foreach ($sr_data_array as $val) 
	{
		$srArr[$val[csf("po_breakdown_id")]][$val[csf("barcode_no")]]['color_id'] = $val[csf("color_id")];
	}
	unset($sr_data_array);


	/* $delivery_rcv_sql = sql_select("SELECT a.po_breakdown_id, a.entry_form, sum(a.qnty) as qnty, a.barcode_no, 0 as grey_sys_id from pro_roll_details a, tmp_po_id b where a.entry_form in (58,22,133) and a.po_breakdown_id=b.po_id and b.user_id=$user_id and a.status_active=1 and a.is_deleted=0 and a.is_sales=1 group by a.po_breakdown_id, a.entry_form, a.barcode_no union all SELECT a.po_breakdown_id, a.entry_form, sum(a.qnty) as qnty, a.barcode_no,c.grey_sys_id
	from pro_grey_prod_delivery_dtls c,pro_roll_details a, tmp_po_id b 
	where c.id=a.dtls_id and c.barcode_num=a.barcode_no and  a.entry_form in (56) and a.po_breakdown_id=b.po_id and b.user_id=1 and a.status_active=1 and a.is_deleted=0 and a.is_sales=1    and c.status_active=1 and c.is_deleted=0
	group by a.po_breakdown_id, a.entry_form, a.barcode_no,c.grey_sys_id"); */

	$delivery_rcv_sql = sql_select("SELECT a.po_breakdown_id, a.entry_form, sum(a.qnty) as qnty, a.barcode_no, 0 as grey_sys_id, 0 as determination_id, null as color_id, 0 as body_part_id from pro_roll_details a, tmp_po_id b where a.entry_form in (58,22,133) and a.po_breakdown_id=b.po_id and b.user_id=$user_id and a.status_active=1 and a.is_deleted=0 and a.is_sales=1 group by a.po_breakdown_id, a.entry_form, a.barcode_no union all SELECT a.po_breakdown_id, a.entry_form, sum(a.qnty) as qnty, a.barcode_no,c.grey_sys_id,c.determination_id,x.color_id,x.body_part_id 
	from PRO_GREY_PROD_ENTRY_DTLS x,pro_grey_prod_delivery_dtls c,pro_roll_details a, tmp_po_id b 
	where  x.mst_id=c.GREY_SYS_ID and c.SYS_DTLS_ID=x.id and c.id=a.dtls_id and c.barcode_num=a.barcode_no and  a.entry_form in (56) and a.po_breakdown_id=b.po_id and b.user_id=$user_id and a.status_active=1 and a.is_deleted=0 and a.is_sales=1    and c.status_active=1 and c.is_deleted=0
	group by a.po_breakdown_id, a.entry_form, a.barcode_no,c.grey_sys_id,c.determination_id,x.color_id,x.body_part_id");
	
	foreach ($delivery_rcv_sql as $val) 
	{
		if($val[csf("entry_form")] == 22 || $val[csf("entry_form")] ==58)
		{
			$sr_color_id = $srArr[$val[csf("po_breakdown_id")]][$val[csf("barcode_no")]]['color_id']; 
			$delivery_rcv_arr[$val[csf("po_breakdown_id")]][$sr_color_id]['rcv'] +=$val[csf("qnty")];
		}
		else if($val[csf("entry_form")] == 56)
		{
			$dts_color_id = $deliveryArr[$val[csf("po_breakdown_id")]][$val[csf("barcode_no")]]['color_id'];
			//$delivery_rcv_arr[$val[csf("po_breakdown_id")]][$dts_color_id][$arrDelivery[$val[csf("grey_sys_id")]][$val[csf("po_breakdown_id")]]]['delivery'] +=$val[csf("qnty")];
			$delivery_rcv_arr[$val[csf("po_breakdown_id")]][$val[csf("color_id")]][$val[csf("body_part_id")]][$val[csf("determination_id")]]['delivery'] +=$val[csf("qnty")];
		}
		else
		{
			$delivery_rcv_arr[$val[csf("po_breakdown_id")]]['trans_in'] +=$val[csf("qnty")];
		}
			
	}
	//echo "<pre>";print_r($delivery_rcv_arr);

	$trans_out_arr = return_library_array("SELECT a.from_order_id, sum(qnty) as qnty from inv_item_transfer_mst a, pro_roll_details b, tmp_po_id c where a.id=b.mst_id and a.entry_form in (133) and b.entry_form in (133) and a.from_order_id=c.po_id and c.user_id=$user_id and b.status_active=1 and b.is_deleted=0 and b.is_sales=1 group by a.from_order_id","from_order_id","qnty");

	$internal_ref_sql = sql_select("SELECT a.id as fso_id, c.grouping
	from fabric_sales_order_mst a, wo_booking_dtls b, wo_po_break_down c, tmp_po_id d
	where a.booking_id=b.booking_mst_id and a.within_group =1 and a.booking_without_order=0 
	and b.booking_type in (1,4) and b.po_break_down_id=c.id and b.status_active=1 and c.status_active=1 and c.grouping is not null
	and a.id=d.po_id and d.user_id=$user_id 
	group by a.id, c.grouping");

	foreach ($internal_ref_sql as $row)
	{
		$int_ref_arr[$row[csf('fso_id')]] .=$row[csf('grouping')].",";
	}

	//print_r($trans_out_arr);

	/* $production_sql= sql_select("SELECT c.po_breakdown_id as fso_id,d.body_part_id, c.quantity, e.knitting_source,d.color_id as fabric_color_id,e.booking_id, c.id as prop_id from order_wise_pro_details c, pro_grey_prod_entry_dtls d, inv_receive_master e, tmp_po_id f where e.company_id = $company_name and c.entry_form=2 and c.dtls_id= d.id and d.mst_id=e.id and c.po_breakdown_id=f.po_id and f.user_id=$user_id and e.entry_form=2  and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0  $sales_orders_cond2 $floor_cond $date_cond");  */  
	$production_sql= sql_select("SELECT c.po_breakdown_id as fso_id,d.body_part_id, c.quantity, e.knitting_source,d.color_id as fabric_color_id,e.booking_id, c.id as prop_id,d.febric_description_id from order_wise_pro_details c, pro_grey_prod_entry_dtls d, inv_receive_master e, tmp_po_id f where e.company_id = $company_name and c.entry_form=2 and c.dtls_id= d.id and d.mst_id=e.id and c.po_breakdown_id=f.po_id and f.user_id=$user_id and e.entry_form=2  and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0  $sales_orders_cond2 $floor_cond $date_cond " );
	foreach ($production_sql as $row)
	{
		if($propIdChk[$row[csf("prop_id")]]=="")
		{
			$propIdChk[$row[csf("prop_id")]] = $row[csf("prop_id")];
			if($row[csf("knitting_source")] ==1)
			{
				//$details_data_knit_production[$row[csf("fso_id")]][$row[csf("fabric_color_id")]][$row[csf("body_part_id")]]["knit_in"]+=$row[csf("quantity")];
				$details_data_knit_production[$row[csf("fso_id")]][$row[csf("fabric_color_id")]][$row[csf("body_part_id")]][$row[csf("febric_description_id")]]["knit_in"]+=$row[csf("quantity")];

			}
			else if($row[csf("knitting_source")] ==3)
			{
				//$details_data_knit_production[$row[csf("fso_id")]][$row[csf("fabric_color_id")]][$row[csf("body_part_id")]]["knit_out"]+=$row[csf("quantity")];
				$details_data_knit_production[$row[csf("fso_id")]][$row[csf("fabric_color_id")]][$row[csf("body_part_id")]][$row[csf("febric_description_id")]]["knit_out"]+=$row[csf("quantity")];
			}
		}
	}



	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id ";
	$data_array=sql_select($sql_deter);

	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
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
				$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				$constructionArr[$row[csf('id')]]=$row[csf('construction')];
				list($cst,$cps)=explode(',',$composition_arr[$row[csf('id')]]);
				$copmpositionArr[$row[csf('id')]]=$cps;
			}
		}
	}
	$con = connect();
	$r_id=execute_query("delete from tmp_po_id where user_id=$user_id ");
	oci_commit($con);

	?>
	<style type="text/css">
		.word_wrap {
			word-wrap:break-word;
			word-break: break-all;
		}
	</style>
	<?
	ob_start();
	?>
	
	<fieldset style="width:3410px;">
		<table width="3430" cellspacing="0" cellpadding="0" border="0" rules="all" >
			<tr class="form_caption">
				<td colspan="20" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
			</tr>
			<tr class="form_caption">
				<td colspan="20" align="center"><? echo $company_library[$company_name]; ?></td>
			</tr>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="3430" class="rpt_table" >
			<thead>
				<tr>
					<th width="30" rowspan="2">SL No</th>
					<th width="100" rowspan="2">Company Name</th>
					<th width="100" rowspan="2">Buyer Name</th>
					<th width="110" rowspan="2">Internal Ref. Number</th>
					<th width="100" rowspan="2">Fabric Booking No</th>
					<th width="100" rowspan="2">Booking Type</th>
					<th width="120" rowspan="2">Textile Ref NO</th>
					<th width="100" rowspan="2">Job No</th>
					<th width="110" rowspan="2">Style No</th>

					<th width="110" rowspan="2">Composition</th>
					<th width="110" rowspan="2">Constraction</th>
					<th width="120" rowspan="2">Color Name</th>
					<th width="120" rowspan="2">Body Part</th>
					<th width="100" rowspan="2">Finish Dia</th>
					<th width="100" rowspan="2">Finish GSM</th>
					<th width="100" rowspan="2">Finish Qty.</th>
					<th width="100" rowspan="2">Grey Qty.</th>
					<th width="100" rowspan="2">Allocated Qty</th>
					<th colspan="2">SR Qty.</th>
					<th colspan="2">Prog Qty.</th>
					<th width="100" rowspan="2">Color Prog. Qty</th>
					<th width="100" rowspan="2">Color Prog. Balance</th>
					<th colspan="2">Knitting Production</th>
					<th width="100" rowspan="2">Knitting Production Qty</th>
					<th colspan="2">Production Balance</th>
					<th width="100" rowspan="2">Color Production. Balance</th>
					<th width="100" rowspan="2">Knitting Delivery TO Store</th>
					<th width="100" rowspan="2">Delivery Balance</th>
					<th width="100" rowspan="2">Store Rcvd</th>
					<th width="100" rowspan="2">Grey Rcvd Balance</th>
					<!-- <th colspan="2">Sales order</th>	 -->
				</tr>
				<tr>
					<th width="100">In House</th>
					<th width="100">Outbound</th>
					<th width="100">In House</th>
					<th width="100">Outbound</th>
					<th width="100">In House</th>
					<th width="100">Outbound</th>
					<th width="100">In House</th>
					<th width="100">Outbound</th>
					<!-- <th width="100">Trans In</th>
					<th width="100">Trans Out</th> -->
				</tr>				
																		

			</thead>
		</table>
		<div style="width:3450px; overflow-y:scroll; max-height:450px;" id="scroll_body">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="3430" class="rpt_table" id="table_body">
			<?
			$i=1;
			//$details_data[$row[csf("job_no")]][$row[csf("sales_booking_no")]][$fabric_string]["buyer_id"];
			foreach($details_data as $fso_no=>$fso_data)
			{
				foreach($fso_data as $fso_booking=>$fso_booking_data)
				{
					foreach($fso_booking_data as $fabStr=>$row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$knit_qnty=$grey_delivery=$grey_receive=$grey_issue=$knit_balance=$delivery_balance=$receive_balance=$in_hand=$transfer_in=$transfer_out=0;
						
						
						//$fabric_string = $row[csf("determination_id")]."*".$row[csf("color_id")]."*".$row[csf("body_part_id")]."*".$row[csf("gsm_weight")]."*".$row[csf("dia")];

						$fab_str_arr = explode("*",$fabStr);

						$determination_id 	= $fab_str_arr[0];
						$color_id 			= $fab_str_arr[1];
						$body_part_id 		= $fab_str_arr[2];
						$gsm_weight 		= $fab_str_arr[3];
						$dia 				= $fab_str_arr[4];

						//$fabStr

						//$knit_in = $details_data_knit_production[$row['fso_id']][$color_id][$body_part_id]['knit_in'];
						//$knit_out = $details_data_knit_production[$row['fso_id']][$color_id][$body_part_id]['knit_out'];

						$knit_in = $details_data_knit_production[$row['fso_id']][$color_id][$body_part_id][$determination_id]
						['knit_in'];
						$knit_out = $details_data_knit_production[$row['fso_id']][$color_id][$body_part_id][$determination_id]['knit_out'];

						$program_in = $fso_program_arr[$row['fso_id']][$color_id][1][$body_part_id];
						$program_out =$fso_program_arr[$row['fso_id']][$color_id][3][$body_part_id];
						

						$color_program_qnty = $program_in+$program_out;
						$color_program_balance = $row["grey_qty"]-$color_program_qnty;

						$color_production_qty = $knit_in+$knit_out;

						$production_balance_in = $program_in-$knit_in;
						$production_balance_out = $program_out-$knit_out;

						$color_production_balance = $color_program_qnty-$color_production_qty;

						$alloc_qty = $alloc_qty_arr[$row['fso_id']];
						$requ_qty_in = $requisition_qty_arr[$row['fso_id']][$color_id][1];
						$requ_qty_out = $requisition_qty_arr[$row['fso_id']][$color_id][3];

						$rcv_qnty = $delivery_rcv_arr[$row['fso_id']][$color_id]['rcv'];
						
						//$delivery_qnty = $delivery_rcv_arr[$row['fso_id']][$color_id][$body_part_id]['delivery'];
						$delivery_qnty = $delivery_rcv_arr[$row['fso_id']][$color_id][$body_part_id][$determination_id]['delivery'];
						
						$trans_in = $delivery_rcv_arr[$row['fso_id']]['trans_in'];

						$delivery_balance = $color_production_balance - $delivery_qnty;
						$receive_balance = $color_program_qnty - $rcv_qnty;

						$trans_out = $trans_out_arr[$row['fso_id']];
						$int_reference = implode(",",array_filter(array_unique(explode(",",$int_ref_arr[$row['fso_id']]))));

						$row_span = $fso_row_count[$row['fso_id']];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
							<td width="30"><? echo $i;?></td>
							<td width="100"><? echo $company_library[$row["company_id"]];?></td>
							<td width="100"><? echo $buyer_library[$row["buyer_id"]];?></td>
							<td width="110" class="word_wrap"><? echo $int_reference;?></td>
							<td width="100"><? echo $fso_booking;?></td>
							<td width="100"><? echo $row["booking_type"];?></td>
							<td width="120"><? echo $fso_no;?></td>
							<td width="100" class="word_wrap"><? echo $row["po_job_no"];?></td>
							<td width="110" class="word_wrap"><? echo $row["style_ref_no"];?></td>

							<td width="110" class="word_wrap"><? echo $composition_arr[$determination_id]; ?></td>
							<td width="110" class="word_wrap"><? echo $constructionArr[$determination_id];?></td>
							<td width="120" class="word_wrap"><? echo $color_library[$color_id]; ?></td>
							<td width="120"><? echo $body_part[$body_part_id];?></td>
							<td width="100"><? echo $dia;?></td>
							<td width="100"><? echo $gsm_weight;?></td>
							<td width="100" align="right"><? echo number_format($row["finish_qty"],2);?></td>
							<td width="100" align="right"><? echo number_format($row["grey_qty"],2);?></td>
							<td width="100" align="right">
								<? if(number_format($alloc_qty,2) > 0.00){?>
									<a href='#report_details' onClick="openmypage_knitting('<? echo $row['fso_id']."=".$fso_no."=".$row["po_job_no"]."=".$fso_booking."=".$int_reference."=".$fabStr."=".$color_id; ?>','1','alloc_popup');"><? echo number_format($alloc_qty,2);?></a>
								<? } else {echo "0.00";}?>
								<? //echo number_format($alloc_qty,2); ?>
							</td>
							<td width="100" align="right">
							<? if(number_format($requ_qty_in,2) > 0.00){?>
								<a href='#report_details' onClick="openmypage_knitting('<? echo $row['fso_id']."=".$fso_no."=".$row["po_job_no"]."=".$fso_booking."=".$int_reference."=".$fabStr."=".$color_id; ?>','1','requ_popup');"><? echo number_format($requ_qty_in,2);?></a>
							<? } else {echo "0.00";}?>
							</td>
							<td width="100" align="right">
							<? if(number_format($requ_qty_out,2) > 0.00){?>
								<a href='#report_details' onClick="openmypage_knitting('<? echo $row['fso_id']."=".$fso_no."=".$row["po_job_no"]."=".$fso_booking."=".$int_reference."=".$fabStr."=".$color_id; ?>','3','requ_popup');"><? echo number_format($requ_qty_out,2);?></a>
							<? } else {echo "0.00";}?>
							</td>
							<td width="100" align="right">
							<? if(number_format($program_in,2) > 0.00){?>
								<a href='#report_details' onClick="openmypage_knitting('<? echo $row['fso_id']."=".$fso_no."=".$row["po_job_no"]."=".$fso_booking."=".$int_reference."=".$fabStr."=".$color_id; ?>','1','program_popup');"><? echo number_format($program_in,2);?></a>
								<? } else {echo "0.00";}?>
							</td>
							<td width="100" align="right">
							<? if(number_format($program_out,2) > 0.00){?>
								<a href='#report_details' onClick="openmypage_knitting('<? echo $row['fso_id']."=".$fso_no."=".$row["po_job_no"]."=".$fso_booking."=".$int_reference."=".$fabStr."=".$color_id; ?>','3','program_popup');"><? echo number_format($program_out,2);?></a>
							<? } else {echo "0.00";}?>
							</td>
							<td width="100" align="right" title="( Prog Qty In House + Prog Qty Outbound )"><? echo number_format($color_program_qnty,2);?></td>
							<td width="100" align="right" title="( Grey Qty - Color Prog. Qty )"><? echo number_format($color_program_balance,2)?></td>
							<td width="100" align="right">
							<? if(number_format($knit_in,2) > 0.00){?>
								<a href='#report_details' onClick="openmypage_knitting('<? echo $row['fso_id']."=".$fso_no."=".$row["po_job_no"]."=".$fso_booking."=".$int_reference."=".$fabStr."=".$color_id; ?>','1','knitting_popup');"><? echo number_format($knit_in,2);?></a>
							<? } else {echo "0.00";}?>
							</td>
							<td width="100" align="right">
								<? if(number_format($knit_out,2) > 0.00){?>
									<a href='#report_details' onClick="openmypage_knitting('<? echo $row['fso_id']."=".$fso_no."=".$row["po_job_no"]."=".$fso_booking."=".$int_reference."=".$fabStr."=".$color_id; ?>','3','knitting_popup');"><? echo number_format($knit_out,2);?></a>
								<? } else {echo "0.00";}?>
								
							
							</td>
							<td width="100" align="right" title="( Knitting Production In House + Knitting Production Outbound )"><? echo number_format($color_production_qty,2);?></td>
							<td width="100" align="right" ><? echo number_format($production_balance_in,2);?></td>
							<td width="100" align="right"><? echo number_format($production_balance_out,2);?></td>
							<td width="100" align="right" title="( Production Balance In House + Production Balance Outbound )"><? echo number_format($color_production_balance,2);?></td>
							<td width="100" align="right">
							<? if(number_format($delivery_qnty,2) > 0.00){?>
								<a href='#report_details' title="<? echo 'colorId: '.$color_id;?>" onClick="openmypage_knitting('<? echo $row['fso_id']."=".$fso_no."=".$row["po_job_no"]."=".$fso_booking."=".$int_reference."=".$fabStr."=".$color_id; ?>','1','delivery_popup');"><? echo number_format($delivery_qnty,2);?></a>
							<? } else {echo "0.00";}?>
							</td>
							<td width="100" align="right" title="( Color Production. Balance - Knitting Delivery TO Store )"><? echo number_format($delivery_balance,2);?></td>
							<td width="100" align="right"><? echo number_format($rcv_qnty,2);?></td>
							<td width="100" align="right" title="( Color Prog. Qty - Store Rcvd)"><? echo number_format($receive_balance,2);?></td>
							<? 
							/* if($duplicate_chk[$row['fso_id']]=="")
							{
								$duplicate_chk[$row['fso_id']]=$row['fso_id'];
								?>
								<td width="100" align="right" rowspan="<? echo $row_span;?>">
									<a href='#report_details' onClick="openmypage_knitting('<? echo $row['fso_id']."=".$fso_no."=".$row["po_job_no"]."=".$fso_booking."=".$int_reference."=".$fabStr; ?>','1','trans_in_popup');"><? echo number_format($trans_in,2);?></a>
								</td>
								<td width="100" align="right" rowspan="<? echo $row_span;?>">
									<a href='#report_details' onClick="openmypage_knitting('<? echo $row['fso_id']."=".$fso_no."=".$row["po_job_no"]."=".$fso_booking."=".$int_reference."=".$fabStr; ?>','2','trans_out_popup');"><? echo number_format($trans_out,2);?></a>
								</td>
								<?
								$total_trans_in +=$trans_in;
								$total_trans_out +=$trans_out;
							} */
							?>
						</tr>
						<?
						$total_finish_qty += $row["finish_qty"];
						$total_grey_qty += $row["grey_qty"];
						$total_alloc_qty += $alloc_qty;
						$total_requ_qty_in += $requ_qty_in;
						$total_requ_qty_out += $requ_qty_out;

						$total_program_in += $program_in;
						$total_program_out += $program_out;
						$total_color_program_qnty += $color_program_qnty;
						$total_color_program_balance += $color_program_balance;
						$total_knit_in += $knit_in;
						$total_knit_out += $knit_out;
						$total_color_production_qty += $color_production_qty;
						$total_production_balance_in += $production_balance_in;
						$total_production_balance_out += $production_balance_out;
						$total_color_production_balance += $color_production_balance;
						$total_delivery_qnty += $delivery_qnty;
						$total_delivery_balance += $delivery_balance;
						$total_rcv_qnty += $rcv_qnty;
						$total_receive_balance += $receive_balance;						
						$i++; 
					}
				}
			}
	?>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="3430" class="rpt_table">
			<tfoot>

			<th width="30">&nbsp;</th>
			<th width="100"></th>
			<th width="100"></th>
			<th width="110"></th>
			<th width="100"></th>
			<th width="100"></th>
			<th width="120"></th>
			<th width="100"></th>
			<th width="110"></th>

			<th width="110"></th>
			<th width="110"></th>
			<th width="120"></th>
			<th width="120"></th>
			<th width="100"></th>
			<th width="100"></th>
			<th width="100" id="value_total_finish_qty"><? echo number_format($total_finish_qty,2);?></th>
			<th width="100" id="value_total_grey_qty"><? echo number_format($total_grey_qty,2);?></th>
			<th width="100"><?// echo number_format($total_alloc_qty,2); ?></th>
			<th width="100"><? //echo number_format($total_requ_qty_in,2);?></th>
			<th width="100" id="value_total_requ_qty_out"><? echo number_format($total_requ_qty_out,2);?></th>

			<th width="100" id="value_total_program_in"><? echo number_format($total_program_in,2);?></th>
			<th width="100" id="value_total_program_out"><? echo number_format($total_program_out,2);?></th>
			<th width="100" id="value_total_color_program_qnty"><? echo number_format($total_color_program_qnty,2);?></th>
			<th width="100" id="value_total_color_program_balance"><? echo number_format($total_color_program_balance,2);?></th>
			<th width="100" id="value_total_knit_in"><? echo number_format($total_knit_in,2);?></th>
			<th width="100" id="value_total_knit_out"><? echo number_format($total_knit_out,2);?></th>

			<th width="100" id="value_total_color_production_qty"><? echo number_format($total_color_production_qty,2);?></th>
			<th width="100" id="value_total_production_balance_in"><? echo number_format($total_production_balance_in,2);?></th>
			<th width="100" id="value_total_production_balance_out"><? echo number_format($total_production_balance_out,2);?></th>
			<th width="100" id="value_total_color_production_balance"><? echo number_format($total_color_production_balance,2);?></th>

			<th width="100" id="value_total_delivery_qnty"><? echo number_format($total_delivery_qnty,2);?></th>
			<th width="100" id="value_total_delivery_balance"><? echo number_format($total_delivery_balance,2);?></th>
			<th width="100" id="value_total_rcv_qnty"><? echo number_format($total_rcv_qnty,2);?></th>
			<th width="100" id="value_total_receive_balance"><? echo number_format($total_receive_balance,2);?></th>



			<!-- <th width="100"><? //echo number_format($total_trans_in,2);?></th>
			<th width="100"><? //echo number_format($total_trans_out,2);?></th> -->
			</tfoot>
		</table>
		</div>
	</fieldset>
	<?
	 $html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    	@unlink($filename);

    }
    //---------end------------//
    $name=time();
    $filename=$user_name."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$filename";
	exit();
}

if($action=="report_generate_old")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_name= str_replace("'","",$cbo_company_name);
	$cbo_within_group= str_replace("'","",$cbo_within_group);
	$cbo_floor_id= str_replace("'","",$cbo_floor_id);
	$cbo_buyer_name= str_replace("'","",$cbo_buyer_name);
	$fso_number= str_replace("'","",$fso_number);

	if($cbo_buyer_name)
	{
		$buyer_id_cond=" and ((a.po_buyer=$cbo_buyer_name and a.within_group=1) or (a.buyer_id=$cbo_buyer_name and a.within_group=2))";
		
	}
	
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		$date_cond=" and e.receive_date between $txt_date_from and $txt_date_to ";
	}

	
	if($cbo_floor_id !=0) $floor_cond=" and d.floor_id=$cbo_floor_id";
	if($cbo_within_group !=0) $within_group_cond=" and a.within_group=$cbo_within_group";

	$sales_orders_cond="";
	if($fso_number != "")
	{
		$sales_orders="";
		foreach (explode(",", $fso_number) as $row)
		{
			$sales_orders.= ($sales_orders=="") ? "".$row."" : ",".$row."";
		}

		if($sales_orders)
		{
			$sales_orders_cond ="and a.id in ($sales_orders)";
		}
	}

	$con = connect();
	$r_id=execute_query("delete from tmp_po_id where user_id=$user_id ");
	if($r_id)
	{
		oci_commit($con);
	}

	$sql="SELECT a.id as fso_id, a.job_no, a.company_id, a.within_group, a.buyer_id, a.po_buyer,a.sales_booking_no, a.job_no_prefix_num,a.style_ref_no, a.booking_type, a.booking_entry_form, a.booking_without_order, a.po_job_no, b.fabric_desc, b.determination_id, b.color_id, b.body_part_id, b.gsm_weight,b.dia, b.id as fso_dtls_id, b.grey_qty, b.finish_qty, c.quantity, c.id as prop_id, d.floor_id, e.receive_date, e.knitting_source
	from fabric_sales_order_mst a, fabric_sales_order_dtls b, order_wise_pro_details c, pro_grey_prod_entry_dtls d, inv_receive_master e
	where a.company_id = $company_name and a.id=b.mst_id and a.id=c.po_breakdown_id and c.entry_form=2 and c.dtls_id= d.id and d.mst_id=e.id and e.entry_form=2 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $sales_orders_cond $within_group_cond $floor_cond $buyer_id_cond $date_cond order by a.id";

	//echo $sql;//die;
		 
	$sql_result=sql_select($sql);
	$details_data=$fsoNoChk=$fsoDtlsChk=$propIdChk=array();
	$booking_type_arr=array("118"=>"Main","108"=>"Partial","88"=>"Short","89"=>"Sample With Order","90"=>"Sample Without Order");
	foreach($sql_result as $row)
	{
		$fabric_string = $row[csf("determination_id")]."*".$row[csf("color_id")]."*".$row[csf("body_part_id")]."*".$row[csf("gsm_weight")]."*".$row[csf("dia")];

		if($fsoDtlsChk[$row[csf("fso_dtls_id")]] =="")
		{
			$fsoDtlsChk[$row[csf("fso_dtls_id")]] = $row[csf("fso_dtls_id")];
			$details_data[$row[csf("job_no")]][$row[csf("sales_booking_no")]][$fabric_string]["grey_qty"]+=$row[csf("grey_qty")];
			$details_data[$row[csf("job_no")]][$row[csf("sales_booking_no")]][$fabric_string]["finish_qty"]+=$row[csf("finish_qty")];
		}

		//FSO Row count for transfer column
		if($fso_row_counts[$row[csf("job_no")]][$row[csf("sales_booking_no")]][$fabric_string] =="")
		{
			$fso_row_counts[$row[csf("job_no")]][$row[csf("sales_booking_no")]][$fabric_string] = $row[csf("job_no")]."=".$row[csf("sales_booking_no")]."=".$fabric_string;
			$fso_row_count[$row[csf("fso_id")]]++;
		}
		else
		{
			$fso_row_counts[$row[csf("job_no")]][$row[csf("sales_booking_no")]][$fabric_string] = $row[csf("job_no")]."=".$row[csf("sales_booking_no")]."=".$fabric_string;
		}

		if($row[csf("within_group")] ==1)
		{
			$details_data[$row[csf("job_no")]][$row[csf("sales_booking_no")]][$fabric_string]["buyer_id"]=$row[csf("po_buyer")];
		}
		else
		{
			$details_data[$row[csf("job_no")]][$row[csf("sales_booking_no")]][$fabric_string]["buyer_id"]=$row[csf("buyer_id")];
		}
		

		if($propIdChk[$row[csf("prop_id")]]=="")
		{
			$propIdChk[$row[csf("prop_id")]] = $row[csf("prop_id")];
			if($row[csf("knitting_source")] ==1)
			{
				$details_data_knit_production[$row[csf("job_no")]]["knit_in"]+=$row[csf("quantity")];
			}
			else if($row[csf("knitting_source")] ==3)
			{
				$details_data_knit_production[$row[csf("job_no")]]["knit_out"]+=$row[csf("quantity")];
			}
		}

		$bookingType="";
		if($row[csf('booking_type')] == 4)
		{
			if($row[csf('booking_without_order')] == 1)
			{
				$bookingType = "Sample Without Order";
			}
			else
			{
				$bookingType =  "Sample With Order";
			}
		}
		else
		{
			$bookingType =  $booking_type_arr[$row[csf('booking_entry_form')]];
		}

		$details_data[$row[csf("job_no")]][$row[csf("sales_booking_no")]][$fabric_string]["style_ref_no"]=$row[csf("style_ref_no")];
		$details_data[$row[csf("job_no")]][$row[csf("sales_booking_no")]][$fabric_string]["company_id"]=$row[csf("company_id")];
		$details_data[$row[csf("job_no")]][$row[csf("sales_booking_no")]][$fabric_string]["booking_type"]=$bookingType;
		$details_data[$row[csf("job_no")]][$row[csf("sales_booking_no")]][$fabric_string]["po_job_no"]=$row[csf("po_job_no")];
		$details_data[$row[csf("job_no")]][$row[csf("sales_booking_no")]][$fabric_string]["fso_id"]=$row[csf("fso_id")];

		
		if($all_fso_id[$row[csf("fso_id")]] ==""){
			$all_fso_id[$row[csf("fso_id")]] =$row[csf("fso_id")];
			execute_query("insert into tmp_po_id (user_id, po_id) values ($user_id,".$row[csf("fso_id")].")");
		}
	}
	oci_commit($con);

	$sql_program = sql_select("SELECT b.po_id, a.knitting_source, sum(b.program_qnty) as program_qnty from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b, tmp_po_id c where a.id=b.dtls_id and b.po_id=c.po_id and c.user_id=$user_id and b.is_sales=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_id, a.knitting_source");

	foreach ($sql_program as $val) 
	{
		$fso_program_arr[$val[csf("po_id")]][$val[csf("knitting_source")]] += $val[csf("program_qnty")];
	}

	$alloc_qty_arr = return_library_array("SELECT sum(a.qnty) as alloc_qty, a.po_break_down_id from inv_material_allocation_dtls a, tmp_po_id b  where a.po_break_down_id =b.po_id and b.user_id=$user_id group by a.po_break_down_id","po_break_down_id","alloc_qty");
	
	$requisition_sql = sql_select("SELECT b.po_id, c.knitting_source, sum(a.yarn_qnty) as requ_qnty from ppl_yarn_requisition_entry a, ppl_planning_entry_plan_dtls b, ppl_planning_info_entry_dtls c, tmp_po_id d where a.knit_id=b.dtls_id and b.is_sales=1 and a.status_active=1 and b.status_active=1 and b.dtls_id=c.id and b.po_id=d.po_id and d.user_id=$user_id and b.is_sales=1 group by b.po_id,c.knitting_source");

	foreach ($requisition_sql as $val) 
	{
		$requisition_qty_arr[$val[csf("po_id")]][$val[csf("knitting_source")]] += $val[csf("requ_qnty")];
	}

	$delivery_rcv_sql = sql_select("SELECT a.po_breakdown_id, a.entry_form, sum(a.qnty) as qnty from pro_roll_details a, tmp_po_id b where a.entry_form in (56,58,22,133) and a.po_breakdown_id=b.po_id and b.user_id=$user_id and a.status_active=1 and a.is_deleted=0 and a.is_sales=1 group by a.po_breakdown_id, a.entry_form");
	
	foreach ($delivery_rcv_sql as $val) 
	{
		if($val[csf("entry_form")] == 22 || $val[csf("entry_form")] ==58)
		{
			$delivery_rcv_arr[$val[csf("po_breakdown_id")]]['rcv'] +=$val[csf("qnty")];
		}
		else if($val[csf("entry_form")] == 56)
		{
			$delivery_rcv_arr[$val[csf("po_breakdown_id")]]['delivery'] +=$val[csf("qnty")];
		}
		else
		{
			$delivery_rcv_arr[$val[csf("po_breakdown_id")]]['trans_in'] +=$val[csf("qnty")];
		}
			
	}

	$trans_out_arr = return_library_array("SELECT a.from_order_id, sum(qnty) as qnty from inv_item_transfer_mst a, pro_roll_details b, tmp_po_id c where a.id=b.mst_id and a.entry_form in (133) and b.entry_form in (133) and a.from_order_id=c.po_id and c.user_id=$user_id and b.status_active=1 and b.is_deleted=0 and b.is_sales=1 group by a.from_order_id","from_order_id","qnty");

	$internal_ref_sql = sql_select("SELECT a.id as fso_id, c.grouping
	from fabric_sales_order_mst a, wo_booking_dtls b, wo_po_break_down c, tmp_po_id d
	where a.booking_id=b.booking_mst_id and a.within_group =1 and a.booking_without_order=0 
	and b.booking_type in (1,4) and b.po_break_down_id=c.id and b.status_active=1 and c.status_active=1 and c.grouping is not null
	and a.id=d.po_id and d.user_id=$user_id 
	group by a.id, c.grouping");

	foreach ($internal_ref_sql as $row)
	{
		$int_ref_arr[$row[csf('fso_id')]] .=$row[csf('grouping')].",";
	}

	//print_r($trans_out_arr);

	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id ";
	$data_array=sql_select($sql_deter);

	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
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
				$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				$constructionArr[$row[csf('id')]]=$row[csf('construction')];
				list($cst,$cps)=explode(',',$composition_arr[$row[csf('id')]]);
				$copmpositionArr[$row[csf('id')]]=$cps;
			}
		}
	}

	?>
	<style type="text/css">
		.word_wrap {
			word-wrap:break-word;
			word-break: break-all;
		}
	</style>
	<?
	ob_start();
	?>
	
	<fieldset style="width:3610px;">
		<table width="3630" cellspacing="0" cellpadding="0" border="0" rules="all" >
			<tr class="form_caption">
				<td colspan="20" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
			</tr>
			<tr class="form_caption">
				<td colspan="20" align="center"><? echo $company_library[$company_name]; ?></td>
			</tr>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="3630" class="rpt_table" >
			<thead>
				<tr>
					<th width="30" rowspan="2">SL No</th>
					<th width="100" rowspan="2">Company Name</th>
					<th width="100" rowspan="2">Buyer Name</th>
					<th width="110" rowspan="2">Internal Ref. Number</th>
					<th width="100" rowspan="2">Fabric Booking No</th>
					<th width="100" rowspan="2">Booking Type</th>
					<th width="120" rowspan="2">Textile Ref NO</th>
					<th width="100" rowspan="2">Job No</th>
					<th width="110" rowspan="2">Style No</th>

					<th width="110" rowspan="2">Composition</th>
					<th width="110" rowspan="2">Constraction</th>
					<th width="120" rowspan="2">Color Name</th>
					<th width="120" rowspan="2">Body Part</th>
					<th width="100" rowspan="2">Finish Dia</th>
					<th width="100" rowspan="2">Finish GSM</th>
					<th width="100" rowspan="2">Finish Qty.</th>
					<th width="100" rowspan="2">Grey Qty.</th>
					<th width="100" rowspan="2">Allocated Qty</th>
					<th colspan="2">SR Qty.</th>
					<th colspan="2">Prog Qty.</th>
					<th width="100" rowspan="2">Color Prog. Qty</th>
					<th width="100" rowspan="2">Color Prog. Balance</th>
					<th colspan="2">Knitting Production</th>
					<th width="100" rowspan="2">Color Prog. Qty</th>
					<th colspan="2">Production Balance</th>
					<th width="100" rowspan="2">Color Prog. Balance</th>
					<th width="100" rowspan="2">Knitting Delivery TO Store</th>
					<th width="100" rowspan="2">Delivery Balance</th>
					<th width="100" rowspan="2">Store Rcvd</th>
					<th width="100" rowspan="2">Grey Rcvd Balance</th>
					<th colspan="2">Sales order</th>	
				</tr>
				<tr>
					<th width="100">In House</th>
					<th width="100">Outbound</th>
					<th width="100">In House</th>
					<th width="100">Outbound</th>
					<th width="100">In House</th>
					<th width="100">Outbound</th>
					<th width="100">In House</th>
					<th width="100">Outbound</th>
					<th width="100">Trans In</th>
					<th width="100">Trans Out</th>
				</tr>				
																		

			</thead>
		</table>
		<div style="width:3650px; overflow-y:scroll; max-height:450px;" id="scroll_body">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="3630" class="rpt_table" id="tbl_list_search">
			<?
			$i=1;
			//$details_data[$row[csf("job_no")]][$row[csf("sales_booking_no")]][$fabric_string]["buyer_id"];
			foreach($details_data as $fso_no=>$fso_data)
			{
				foreach($fso_data as $fso_booking=>$fso_booking_data)
				{
					foreach($fso_booking_data as $fabStr=>$row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$knit_qnty=$grey_delivery=$grey_receive=$grey_issue=$knit_balance=$delivery_balance=$receive_balance=$in_hand=$transfer_in=$transfer_out=0;
						
						
						//$fabric_string = $row[csf("determination_id")]."*".$row[csf("color_id")]."*".$row[csf("body_part_id")]."*".$row[csf("gsm_weight")]."*".$row[csf("dia")];

						$fab_str_arr = explode("*",$fabStr);

						$determination_id 	= $fab_str_arr[0];
						$color_id 			= $fab_str_arr[1];
						$body_part_id 		= $fab_str_arr[2];
						$gsm_weight 		= $fab_str_arr[3];
						$dia 				= $fab_str_arr[4];

						//$fabStr

						$knit_in = $details_data_knit_production[$fso_no]['knit_in'];
						$knit_out = $details_data_knit_production[$fso_no]['knit_out'];
						$program_in = $fso_program_arr[$row['fso_id']][1];
						$program_out =$fso_program_arr[$row['fso_id']][3];
						

						$color_program_qnty = $program_in+$program_out;
						$color_program_balance = $row["grey_qty"]-$color_program_qnty;

						$color_production_qty = $knit_in+$knit_out;

						$production_balance_in = $program_in-$knit_in;
						$production_balance_out = $program_out-$knit_out;

						$color_production_balance = $color_program_qnty-$color_production_qty;

						$alloc_qty = $alloc_qty_arr[$row['fso_id']];
						$requ_qty_in = $requisition_qty_arr[$row['fso_id']][1];
						$requ_qty_out = $requisition_qty_arr[$row['fso_id']][3];

						$rcv_qnty = $delivery_rcv_arr[$row['fso_id']]['rcv'];
						$delivery_qnty = $delivery_rcv_arr[$row['fso_id']]['delivery'];
						$trans_in = $delivery_rcv_arr[$row['fso_id']]['trans_in'];

						$delivery_balance = $color_production_balance - $delivery_qnty;
						$receive_balance = $color_program_qnty - $rcv_qnty;

						$trans_out = $trans_out_arr[$row['fso_id']];
						$int_reference = implode(",",array_filter(array_unique(explode(",",$int_ref_arr[$row['fso_id']]))));

						$row_span = $fso_row_count[$row['fso_id']];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
							<td width="30"><? echo $i;?></td>
							<td width="100"><? echo $company_library[$row["company_id"]];?></td>
							<td width="100"><? echo $buyer_library[$row["buyer_id"]];?></td>
							<td width="110" class="word_wrap"><? echo $int_reference;?></td>
							<td width="100"><? echo $fso_booking;?></td>
							<td width="100"><? echo $row["booking_type"];?></td>
							<td width="120"><? echo $fso_no;?></td>
							<td width="100" class="word_wrap"><? echo $row["po_job_no"];?></td>
							<td width="110" class="word_wrap"><? echo $row["style_ref_no"];?></td>

							<td width="110" class="word_wrap"><? echo $composition_arr[$determination_id]; ?></td>
							<td width="110" class="word_wrap"><? echo $constructionArr[$determination_id];?></td>
							<td width="120" class="word_wrap"><? echo $color_library[$color_id]; ?></td>
							<td width="120"><? echo $body_part[$body_part_id];?></td>
							<td width="100"><? echo $dia;?></td>
							<td width="100"><? echo $gsm_weight;?></td>
							<td width="100" align="right"><? echo number_format($row["finish_qty"],2);?></td>
							<td width="100" align="right"><? echo number_format($row["grey_qty"],2);?></td>
							<td width="100" align="right"><? echo number_format($alloc_qty,2); ?></td>
							<td width="100" align="right">
								<a href='#report_details' onClick="openmypage_knitting('<? echo $row['fso_id']."=".$fso_no."=".$row["po_job_no"]."=".$fso_booking."=".$int_reference."=".$fabStr; ?>','1','requ_popup');"><? echo number_format($requ_qty_in,2);?></a>
							</td>
							<td width="100" align="right">
								<a href='#report_details' onClick="openmypage_knitting('<? echo $row['fso_id']."=".$fso_no."=".$row["po_job_no"]."=".$fso_booking."=".$int_reference."=".$fabStr; ?>','3','requ_popup');"><? echo number_format($requ_qty_out,2);?></a>
							</td>
							<td width="100" align="right">
								<a href='#report_details' onClick="openmypage_knitting('<? echo $row['fso_id']."=".$fso_no."=".$row["po_job_no"]."=".$fso_booking."=".$int_reference."=".$fabStr; ?>','1','program_popup');"><? echo number_format($program_in,2);?></a>
							</td>
							<td width="100" align="right">
								<a href='#report_details' onClick="openmypage_knitting('<? echo $row['fso_id']."=".$fso_no."=".$row["po_job_no"]."=".$fso_booking."=".$int_reference."=".$fabStr; ?>','3','program_popup');"><? echo number_format($program_out,2);?></a>
							</td>
							<td width="100" align="right"><? echo number_format($color_program_qnty,2);?></td>
							<td width="100" align="right"><? echo number_format($color_program_balance,2)?></td>
							<td width="100" align="right">
								<a href='#report_details' onClick="openmypage_knitting('<? echo $row['fso_id']."=".$fso_no."=".$row["po_job_no"]."=".$fso_booking."=".$int_reference."=".$fabStr; ?>','1','knitting_popup');"><? echo number_format($knit_in,2);?></a>
							</td>
							<td width="100" align="right">
								<a href='#report_details' onClick="openmypage_knitting('<? echo $row['fso_id']."=".$fso_no."=".$row["po_job_no"]."=".$fso_booking."=".$int_reference."=".$fabStr; ?>','3','knitting_popup');"><? echo number_format($knit_out,2);?></a>
							</td>
							<td width="100" align="right"><? echo number_format($color_production_qty,2);?></td>
							<td width="100" align="right"><? echo number_format($production_balance_in,2);?></td>
							<td width="100" align="right"><? echo number_format($production_balance_out,2);?></td>
							<td width="100" align="right"><? echo number_format($color_production_balance,2);?></td>
							<td width="100" align="right">
								<a href='#report_details' onClick="openmypage_knitting('<? echo $row['fso_id']."=".$fso_no."=".$row["po_job_no"]."=".$fso_booking."=".$int_reference."=".$fabStr; ?>','1','delivery_popup');"><? echo number_format($delivery_qnty,2);?></a></td>
							<td width="100" align="right"><? echo number_format($delivery_balance,2);?></td>
							<td width="100" align="right"><? echo number_format($rcv_qnty,2);?></td>
							<td width="100" align="right"><? echo number_format($receive_balance,2);?></td>
							<? 
							if($duplicate_chk[$row['fso_id']]=="")
							{
								$duplicate_chk[$row['fso_id']]=$row['fso_id'];
								?>
								<td width="100" align="right" rowspan="<? echo $row_span;?>">
									<a href='#report_details' onClick="openmypage_knitting('<? echo $row['fso_id']."=".$fso_no."=".$row["po_job_no"]."=".$fso_booking."=".$int_reference."=".$fabStr; ?>','1','trans_in_popup');"><? echo number_format($trans_in,2);?></a>
								</td>
								<td width="100" align="right" rowspan="<? echo $row_span;?>">
									<a href='#report_details' onClick="openmypage_knitting('<? echo $row['fso_id']."=".$fso_no."=".$row["po_job_no"]."=".$fso_booking."=".$int_reference."=".$fabStr; ?>','2','trans_out_popup');"><? echo number_format($trans_out,2);?></a>
								</td>
								<?
								$total_trans_in +=$trans_in;
								$total_trans_out +=$trans_out;
							}
							?>
						</tr>
						<?
						$total_finish_qty += $row["finish_qty"];
						$total_grey_qty += $row["grey_qty"];
						$total_alloc_qty += $alloc_qty;
						$total_requ_qty_in += $requ_qty_in;
						$total_requ_qty_out += $requ_qty_out;

						$total_program_in += $program_in;
						$total_program_out += $program_out;
						$total_color_program_qnty += $color_program_qnty;
						$total_color_program_balance += $color_program_balance;
						$total_knit_in += $knit_in;
						$total_knit_out += $knit_out;
						$total_color_production_qty += $color_production_qty;
						$total_production_balance_in += $production_balance_in;
						$total_production_balance_out += $production_balance_out;
						$total_color_production_balance += $color_production_balance;
						$total_delivery_qnty += $delivery_qnty;
						$total_delivery_balance += $delivery_balance;
						$total_rcv_qnty += $rcv_qnty;
						$total_receive_balance += $receive_balance;						
						$i++; 
					}
				}
			}
	?>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="3630" class="rpt_table">
			<tfoot>

			<th width="30">&nbsp;</th>
			<th width="100"></th>
			<th width="100"></th>
			<th width="110"></th>
			<th width="100"></th>
			<th width="100"></th>
			<th width="120"></th>
			<th width="100"></th>
			<th width="110"></th>

			<th width="110"></th>
			<th width="110"></th>
			<th width="120"></th>
			<th width="120"></th>
			<th width="100"></th>
			<th width="100"></th>
			<th width="100"><? echo number_format($total_finish_qty,2);?></th>
			<th width="100"><? echo number_format($total_grey_qty,2);?></th>
			<th width="100"><? echo number_format($total_alloc_qty,2); ?></th>
			<th width="100"><? echo number_format($total_requ_qty_in,2);?></th>
			<th width="100"><? echo number_format($total_requ_qty_out,2);?></th>

			<th width="100"><? echo number_format($total_program_in,2);?></th>
			<th width="100"><? echo number_format($total_program_out,2);?></th>
			<th width="100"><? echo number_format($total_color_program_qnty,2);?></th>
			<th width="100"><? echo number_format($total_color_program_balance,2);?></th>
			<th width="100"><? echo number_format($total_knit_in,2);?></th>
			<th width="100"><? echo number_format($total_knit_out,2);?></th>

			<th width="100"><? echo number_format($total_color_production_qty,2);?></th>
			<th width="100"><? echo number_format($total_production_balance_in,2);?></th>
			<th width="100"><? echo number_format($total_production_balance_out,2);?></th>
			<th width="100"><? echo number_format($total_color_production_balance,2);?></th>

			<th width="100"><? echo number_format($total_delivery_qnty,2);?></th>
			<th width="100"><? echo number_format($total_delivery_balance,2);?></th>
			<th width="100"><? echo number_format($total_rcv_qnty,2);?></th>
			<th width="100"><? echo number_format($total_receive_balance,2);?></th>



			<th width="100"><? echo number_format($total_trans_in,2);?></th>
			<th width="100"><? echo number_format($total_trans_out,2);?></th>
			</tfoot>
		</table>
		</div>
	</fieldset>
	<?
	 $html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    	@unlink($filename);

    }
    //---------end------------//
    $name=time();
    $filename=$user_name."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$filename";
	exit();
}

if($action=="recv_popup")
{
 	echo load_html_head_contents("Receive Details", "../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	$data=explode("_",$data);
	$job_no=$data[0];
	$body_part_id=$data[1];
	$construction=$data[2];
	$fabric_color_id=$data[3];
	$barcode_nos=$data[4];
	?>
	<script>
	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		$('#table_body tbody tr:first').hide();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+ '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		$('#table_body tbody tr:first').show();
		d.close();
	}
	var tableFilters = 
	{
		col_10: "none",
		col_operation: {
		id: ["value_total_balance"],
		col: [5],
		operation: ["sum"],
		write_method: ["innerHTML"]
		}
	}	
</script>	
	<fieldset style="width:640px; margin-left:3px">
        <p><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></p>
        <br>
        <div id="report_container" style="width:100%">
        	<table border="0" rules="all" width="620" cellpadding="0" cellspacing="0">
            	<tr>
                	<td>Job No : <? echo $job_no; ?></td>
                    <td>Body part : <? echo $body_part[$body_part_id]; ?></td>
                    <td>Constraction : <? echo $construction; ?></td>
                    <td>Color : <? echo $color_library[$fabric_color_id]; ?></td>
                </tr>
            </table>
            <br>
        
        <table cellpadding="0" width="620" class="rpt_table" rules="all" border="1">
            <thead>
                <th width="50">SL</th>
                <th width="120">Purpose</th>
                <th width="130">Transaction No</th>
                <th width="100">Bacode No</th>
                <th width="100">Roll No</th>
                <th>Roll Weight</th>
            </thead>
        </table>
        <div style="width:640px; max-height:250px; overflow-y:scroll">
            <table cellpadding="0" width="620" class="rpt_table" rules="all" border="1" id="tbl_list_search">
			<?
			
				$i=0; $tot_grey_qnty=0;
                $sql="select a.recv_number, c.barcode_no, c.roll_no, c.qnty
				from inv_receive_master a,  pro_grey_prod_entry_dtls b, pro_roll_details c 
				WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,58) and c.entry_form in(2,58) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos)";
               //echo $sql."<br>";//die;
			   	$tot_qnty=0;
                $result= sql_select($sql);
                foreach($result as $row)
                {
					$i++;
                    if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
               	?>
                   <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="50"><? echo $i; ?></td>
                        <td width="120"><p><? echo "Receive"; ?>&nbsp;</p></td>
                        <td width="130"><p><? echo $row[csf('recv_number')]; ?>&nbsp;</p></td>
                        <td width="100"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
                        <td width="100" align="center"><p><? echo $row[csf('roll_no')]; ?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
                    </tr>
                <? 
					$tot_qnty+=$row[csf('qnty')];
                } 
				$trans_sql="select a.transfer_system_id, c.barcode_no, c.roll_no, c.qnty 
				from order_wise_pro_details p, inv_item_transfer_mst a,  inv_item_transfer_dtls b,  pro_roll_details c where p.trans_id=b.to_trans_id and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form=83 and p.entry_form=83 and p.trans_type=5 and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos) ";
				//echo $trans_sql."<br>";
				$trans_result=sql_select($trans_sql);
				foreach($trans_result as $row)
                {
					$i++;
                    if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					
               	?>
                   <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="50"><? echo $i; ?></td>
                        <td width="120"><p><? echo "Transfer"; ?>&nbsp;</p></td>
                        <td width="130"><p><? echo $row[csf("transfer_system_id")]; ?>&nbsp;</p></td>
                        <td width="100"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
                        <td width="100" align="center"><p><? echo $row[csf('roll_no')]; ?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
                    </tr>
                <? 
					$tot_qnty+=$row[csf('qnty')];
                }
            ?>
            </table>
		</div> 
        <table cellpadding="0" width="620" class="rpt_table" rules="all" border="1">
            <tfoot>
                <th colspan="4">Roll Total :</th>
                <th width="100" style="text-align:center"><? echo $i; ?></th>
                <th width="113"><? echo number_format($tot_qnty,2); ?></th>
            </tfoot>
        </table>
        </div>
    </fieldset>
<?
exit();
}


if($action=="requ_popup")
{
 	echo load_html_head_contents("Issue Details", "../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	$data=explode("=",$data);

	//$row['fso_id']."=".$fso_no."=".$row["po_job_no"]."=".$fso_booking."=".$int_reference."=".$fabStr

	$fso_id=$data[0];
	$fso_no=$data[1];
	$po_job_no=$data[2];
	$fso_booking=$data[3];
	$int_reference=$data[4];
	$fabric_string=$data[5];
	$fabric_color=$data[6];

	$fab_str_arr		=explode("*",$fabric_string);
	$determination_id 	= $fab_str_arr[0];
	$color_id 			= $fab_str_arr[1];
	$body_part_id 		= $fab_str_arr[2];
	$gsm_weight 		= $fab_str_arr[3];
	$dia 				= $fab_str_arr[4];

	//echo $companyID . '> ' .$type . '> ' . $fso_no . '> ' . $fso_booking . '> ' . $int_reference . '> ' . $fabric_string;
	//die;
	?>
	<script>
	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		$('#table_body tbody tr:first').hide();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+ '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		$('#table_body tbody tr:first').show();
		d.close();
	}
	var tableFilters = 
	{
		col_10: "none",
		col_operation: {
		id: ["value_total_balance"],
		col: [5],
		operation: ["sum"],
		write_method: ["innerHTML"]
		}
	}	
	</script>	
	<fieldset style="width:640px; margin-left:3px">
        <p><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></p>
        <br>
        <div id="report_container" style="width:100%">
        	<table border="0" rules="all" width="600" cellpadding="0" cellspacing="0">
            	<tr>
                	<td><b>Job No : <? echo $po_job_no . ", Booking : ". $fso_booking.", Textile Ref : ".$fso_no; ?></b></td>
                </tr>
            </table>
            <br>
        <table cellpadding="0" width="850" class="rpt_table" rules="all" border="1">
            <thead>
                <th width="40">SL</th>
                <th width="90">SR No</th>
                <th width="100">SR Date</th>
                <th width="100">Brand</th>
                <th width="100">Lot No</th>
                <th width="120">Yarn Description</th>
                <th width="100">Color</th>
                <th width="100">SR Qty.</th>
                <th width="100">No Of Cone</th>
            </thead>
        </table>
        <div style="width:870px; max-height:250px; overflow-y:scroll">
            <table cellpadding="0" width="850" class="rpt_table" rules="all" border="1" id="tbl_list_search">
			<?
			
				$i=0; $tot_qnty=0;
				
				$requisition_sql = sql_select("SELECT b.po_id, c.knitting_source, a.requisition_no ,requisition_date, d.lot, e.brand_name, d.product_name_details, f.color_name,sum(a.yarn_qnty) as requ_qnty , sum(a.no_of_cone) as no_of_cone
				from ppl_yarn_requisition_entry a, ppl_planning_entry_plan_dtls b, ppl_planning_info_entry_dtls c, 
				product_details_master d left join lib_brand e on d.brand=e.id left join lib_color f on d.color=f.id
				where a.knit_id=b.dtls_id and b.is_sales=1 and a.status_active=1 and b.status_active=1 and b.dtls_id=c.id and b.po_id=$fso_id and c.color_id=$fabric_color and b.is_sales=1 and a.prod_id=d.id and c.knitting_source=$type group by b.po_id, c.knitting_source, a.requisition_no ,requisition_date, d.lot, e.brand_name, d.product_name_details, f.color_name");

                foreach($requisition_sql as $row)
                {
					$i++;
                    if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
               	?>
                   <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="90"><p><? echo $row[csf('requisition_no')]; ?>&nbsp;</p></td>
                        <td width="130"><p><? echo $row[csf('requisition_date')]; ?>&nbsp;</p></td>
                        <td width="100"><p><? echo $row[csf('brand_name')]; ?>&nbsp;</p></td>
                        <td width="100"><p><? echo $row[csf('lot')]; ?>&nbsp;</p></td>
                        <td width="120"><p><? echo $row[csf('product_name_details')]; ?>&nbsp;</p></td>
                        <td width="100"><p><? echo $row[csf('color_name')]; ?>&nbsp;</p></td>
                        <td width="100" align="right"><? echo number_format($row[csf('requ_qnty')],2); ?></td>
                        <td width="100" align="right"><? echo number_format($row[csf('no_of_cone')],2); ?></td>
                    </tr>
                <? 
					$tot_qnty+=$row[csf('requ_qnty')];
					$tot_no_of_cone+=$row[csf('no_of_cone')];
                } 
				
            ?>
            </table>
		</div> 
        <table cellpadding="0" width="850" class="rpt_table" rules="all" border="1">
            <tfoot>
                <th width="650">Total :</th>
                <th width="100"><? echo number_format($tot_qnty,2); ?></th>
                <th width="100"><? echo number_format($tot_no_of_cone,2); ?></th>
            </tfoot>
        </table>
        </div>
    </fieldset>
	<?
	exit();
}

if($action=="alloc_popup")
{
 	echo load_html_head_contents("Alocation Details", "../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	$data=explode("=",$data);

	//$row['fso_id']."=".$fso_no."=".$row["po_job_no"]."=".$fso_booking."=".$int_reference."=".$fabStr

	$fso_id=$data[0];
	$fso_no=$data[1];
	$po_job_no=$data[2];
	$fso_booking=$data[3];
	$int_reference=$data[4];
	$fabric_string=$data[5];
	$fabric_color=$data[6];

	$fab_str_arr		=explode("*",$fabric_string);
	$determination_id 	= $fab_str_arr[0];
	$color_id 			= $fab_str_arr[1];
	$body_part_id 		= $fab_str_arr[2];
	$gsm_weight 		= $fab_str_arr[3];
	$dia 				= $fab_str_arr[4];

	//echo $companyID . '> ' .$type . '> ' . $fso_no . '> ' . $fso_booking . '> ' . $int_reference . '> ' . $fabric_string;
	//die;
	?>
	<script>
	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		$('#table_body tbody tr:first').hide();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+ '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		$('#table_body tbody tr:first').show();
		d.close();
	}
	var tableFilters = 
	{
		col_10: "none",
		col_operation: {
		id: ["value_total_balance"],
		col: [5],
		operation: ["sum"],
		write_method: ["innerHTML"]
		}
	}	
	</script>	
	<fieldset style="width:350px; margin-left:3px">
        <p><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></p>
        <br>
        <div id="report_container" style="width:100%">
        	
			<table cellpadding="0" width="340" class="rpt_table" rules="all" border="1">
				<thead>
					<th width="40">SL</th>
					<th width="100">Date</th>
					<th width="100">Lot</th>
					<th width="100">Allocation Qty</th>
				</thead>
			
				<?
				
					$i=0; $tot_alloc_qty=0;
					
					//for product information
					$sql_product = "SELECT id AS ID, supplier_id AS SUPPLIER_ID, lot AS LOT, product_name_details AS PRODUCT_NAME FROM product_details_master WHERE id IN(SELECT item_id FROM inv_material_allocation_mst WHERE PO_BREAK_DOWN_ID = '".$fso_id."' AND item_category=1 AND status_active=1 AND is_deleted=0)";
					//echo $sql_product;
					$sql_product_rslt = sql_select($sql_product);
					$product_data_arr = array();
					foreach($sql_product_rslt as $row)
					{
						$product_data_arr[$row['ID']]['lot'] = $row['LOT'];
					}
					unset($sql_product_rslt);

					$alloc_qty_arr = sql_select("SELECT a.allocation_date, a.qnty as alloc_qty, a.po_break_down_id, a.item_id from inv_material_allocation_dtls a where a.po_break_down_id = '$fso_id' and booking_no='$fso_booking'");

					foreach($alloc_qty_arr as $row)
					{
						$i++;
						if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="40"><? echo $i; ?></td>
								<td width="100"><? echo $row[csf('allocation_date')]; ?>&nbsp;</td>
								<td width="100"><? echo $product_data_arr[$row[csf('item_id')]]['lot']; ?>&nbsp;</td>
								<td width="100" align="right"><? echo number_format($row[csf('alloc_qty')],2); ?></td>
							</tr>
						<? 
						$tot_alloc_qty+=$row[csf('alloc_qty')];
					} 
					
				?>
				<tfoot>
					<th colspan="3">Total :</th>
					<th width="100"><? echo number_format($tot_alloc_qty,2); ?></th>
				</tfoot>
				</table>
			</div> 
        </div>
    </fieldset>
	<?
	exit();
}

if($action=="program_popup")
{
 	echo load_html_head_contents("Issue Details", "../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	$data=explode("=",$data);

	$fso_id=$data[0];
	$fso_no=$data[1];
	$po_job_no=$data[2];
	$fso_booking=$data[3];
	$int_reference=$data[4];
	$fabric_string=$data[5];
	$fabric_color=$data[6];

	$fab_str_arr		=explode("*",$fabric_string);
	$determination_id 	= $fab_str_arr[0];
	$color_id 			= $fab_str_arr[1];
	$body_part_id 		= $fab_str_arr[2];
	$gsm_weight 		= $fab_str_arr[3];
	$dia 				= $fab_str_arr[4];

	?>
	<script>
	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		$('#table_body tbody tr:first').hide();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+ '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		$('#table_body tbody tr:first').show();
		d.close();
	}
	var tableFilters = 
	{
		col_10: "none",
		col_operation: {
		id: ["value_total_balance"],
		col: [5],
		operation: ["sum"],
		write_method: ["innerHTML"]
		}
	}	
</script>	
	<fieldset style="width:640px; margin-left:3px">
        <p><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></p>
        <br>
        <div id="report_container" style="width:100%">
        	<table border="0" rules="all" width="600" cellpadding="0" cellspacing="0">
            	<tr>
                	<td><b>Job No : <? echo $po_job_no . ", Booking : ". $fso_booking.", Textile Ref : ".$fso_no; ?></b></td>
                </tr>
            </table>
            <br>
        <table cellpadding="0" width="1570" class="rpt_table" rules="all" border="1">
            <thead>
                <th width="40">SL</th>
                <th width="90">Program Date</th>
                <th width="70">Program No</th>
                <th width="100">Knitting Party</th>
                <th width="100">Fabrication</th>
                <th width="50">GSM</th>
                <th width="50">F. Dia</th>
                <th width="100">Dia Type</th>
                <th width="100">Floor</th>
                <th width="100">M/c. No</th>
                <th width="80">Dia & GG</th>
                <th width="120">Color</th>
                <th width="100">Color Range</th>
                <th width="70">S/L</th>
                <th width="100">Prpgram Qty.</th>
                <th width="120">Yarn Description</th>
                <th width="100">Lot</th>
                <th width="80">Yarn Qty.(KG)</th>
            </thead>
        </table>
        <div style="width:1590px; max-height:250px; overflow-y:scroll">
            <table cellpadding="0" width="1570" class="rpt_table" rules="all" border="1" id="tbl_list_search">
			<?
				$i=0; $tot_qnty=0;
				$requisition_sql = sql_select("SELECT c.id as knit_id, d.lot, d.product_name_details, sum(a.yarn_qnty) as yarn_qnty
				from ppl_yarn_requisition_entry a, ppl_planning_entry_plan_dtls b, ppl_planning_info_entry_dtls c, 
				product_details_master d left join lib_brand e on d.brand=e.id left join lib_color f on d.color=f.id
				where a.knit_id=b.dtls_id and b.is_sales=1 and a.status_active=1 and b.status_active=1 and b.dtls_id=c.id and b.po_id=$fso_id and c.color_id=$fabric_color and b.is_sales=1 and a.prod_id=d.id and c.knitting_source=$type group by c.id, d.lot, d.product_name_details");


				foreach($requisition_sql as $row)
				{
					$req_data_arr[$row[csf('knit_id')]]['lot'] = $row[csf('knit_id')];
					$reqsDataArr[$row[csf('knit_id')]]['product_name_details'] .= $row[csf('product_name_details')]."==";
					$reqsDataArr[$row[csf('knit_id')]]['yarn_qnty'] += $row[csf('yarn_qnty')];
				}

				

				$program_floor=array();
				$sql_floor = sql_select("select a.dtls_id, d.floor_name from ppl_planning_entry_plan_dtls a, ppl_planning_info_machine_dtls b, lib_machine_name c, lib_prod_floor d where a.dtls_id=b.dtls_id and b.machine_id=c.id and c.floor_id=d.id and a.po_id=$fso_id and a.is_sales=1 group by a.dtls_id, d.floor_name");
				foreach ($sql_floor as  $val) 
				{
					if($program_floor[$val[csf("dtls_id")]] =="")
					{
						$program_floor[$val[csf("dtls_id")]] =$val[csf("floor_name")];
					}
					else
					{
						$program_floor[$val[csf("dtls_id")]] .=$val[csf("floor_name")].",";
					}
					
				}
				$machine_arr = return_library_array("select id, machine_no from lib_machine_name", 'id', 'machine_no');
				$sql_program = sql_select("SELECT b.id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type,  b.knitting_source, b.knitting_party, b.color_id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.stitch_length, b.machine_id
				from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c, fabric_sales_order_mst e   
				where a.id=b.mst_id and b.id=c.dtls_id and c.po_id=e.id and c.is_sales=1 and a.company_id=$companyID and e.id=$fso_id and b.color_id=$fabric_color and b.knitting_source=$type and a.is_sales=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 group by b.id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.knitting_source, b.knitting_party, b.color_id,b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.stitch_length, b.machine_id");
				

                foreach($sql_program as $row)
                {
					$i++;
                    if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";

					$knitting_source = $row[csf('knitting_source')];    						
					if ($knitting_source == 1) {
						$knitting_party = $company_library[$row[csf('knitting_party')]];
					} else {
						$knitting_party = $supplier_details[$row[csf('knitting_party')]];
					}
    				
					$machine_dia_gg = $row[csf('machine_dia')] . 'X' . $row[csf('machine_gg')];
    						
					$machine_no = '';
					$machine_id = explode(",", $row[csf("machine_id")]);
					foreach ($machine_id as $val) {
						if ($machine_no == '')
							$machine_no = $machine_arr[$val];
						else
							$machine_no .= "," . $machine_arr[$val];
					}
    		
					$gmts_color = '';
					$color_id = explode(",", $row[csf("color_id")]);
					foreach ($color_id as $val) {
						if ($gmts_color == '')
							$gmts_color = $color_library[$val];
						else
							$gmts_color .= "," . $color_library[$val];
					}

					$product_name_details = implode(",",array_unique(explode("==",chop($reqsDataArr[$row[csf("id")]]['product_name_details'],"=="))));


               	?>
                   <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="90"><p><? echo $row[csf('program_date')]; ?>&nbsp;</p></td>
                        <td width="70"><p><? echo $row[csf('id')]; ?>&nbsp;</p></td>
                        <td width="100"><p><? echo $knitting_party; ?>&nbsp;</p></td>
                        <td width="100"><p><? echo $row[csf('fabric_desc')]; ?>&nbsp;</p></td>
                        <td width="50"><p><? echo $row[csf('gsm_weight')]; ?>&nbsp;</p></td>
                        <td width="50"><p><? echo $row[csf('dia')]; ?>&nbsp;</p></td>
                        <td width="100"><p><? echo $fabric_typee[$row[csf('width_dia_type')]]; ?>&nbsp;</p></td>
                        <td width="100"><p><? echo $program_floor[$row[csf("id")]]; ?>&nbsp;</p></td>
                        <td width="100"><p><? echo $machine_no; ?>&nbsp;</p></td>
                        <td width="80"><p><? echo $machine_dia_gg; ?>&nbsp;</p></td>
                        <td width="120"><p><? echo $gmts_color; ?>&nbsp;</p></td>
                        <td width="100"><p><? echo $color_range[$row[csf("color_range")]]; ?>&nbsp;</p></td>
                        <td width="70"><p><? echo $row[csf("stitch_length")]; ?>&nbsp;</p></td>
                        <td width="100" align="right"><p><? echo $row[csf("program_qnty")]; ?>&nbsp;</p></td>

                        <td width="120"><p><? echo $product_name_details; ?>&nbsp;</p></td>
                        <td width="100"><p><? echo $reqsDataArr[$row[csf("id")]]['lot']; ?>&nbsp;</p></td>
                        <td width="80" align="right"><p><? echo $reqsDataArr[$row[csf("id")]]['yarn_qnty']; ?>&nbsp;</p></td>
                    </tr>
                <? 
					$tot_program_qnty+= $row[csf("program_qnty")];
					$tot_yarn_qnty+=$reqsDataArr[$row[csf("id")]]['yarn_qnty'];
                } 
				
            ?>
            </table>
		</div> 
        <table cellpadding="0" width="1570" class="rpt_table" rules="all" border="1">
            <tfoot>
                <th width="1170">Total :</th>
                <th width="100"><? echo number_format($tot_program_qnty,2); ?></th>
                <th width="120"></th>
                <th width="100"></th>
                <th width="80"><? echo number_format($tot_yarn_qnty,2); ?></th>
            </tfoot>
        </table>
        </div>
    </fieldset>
<?
exit();
}

if($action=="knitting_popup")
{
 	echo load_html_head_contents("Issue Details", "../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	$data=explode("=",$data);

	$fso_id=$data[0];
	$fso_no=$data[1];
	$po_job_no=$data[2];
	$fso_booking=$data[3];
	$int_reference=$data[4];
	$fabric_string=$data[5];
	$fabric_color=$data[6];

	$fab_str_arr		=explode("*",$fabric_string);
	$determination_id 	= $fab_str_arr[0];
	$color_id 			= $fab_str_arr[1];
	$body_part_id 		= $fab_str_arr[2];
	$gsm_weight 		= $fab_str_arr[3];
	$dia 				= $fab_str_arr[4];

	?>
	<script>
	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		$('#table_body tbody tr:first').hide();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+ '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		$('#table_body tbody tr:first').show();
		d.close();
	}
	var tableFilters = 
	{
		col_10: "none",
		col_operation: {
		id: ["value_total_balance"],
		col: [5],
		operation: ["sum"],
		write_method: ["innerHTML"]
		}
	}	
</script>	
	<fieldset style="width:640px; margin-left:3px">
        <p><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></p>
        <br>
        <div id="report_container" style="width:100%">
        	<table border="0" rules="all" width="600" cellpadding="0" cellspacing="0">
            	<tr>
                	<td><b>Job No : <? echo $po_job_no . ", Booking : ". $fso_booking.", Textile Ref : ".$fso_no; ?></b></td>
                </tr>
            </table>
            <br>
        <table cellpadding="0" width="800" class="rpt_table" rules="all" border="1">
            <thead>
                <th width="100">Prod Date</th>
                <th width="100">Prod. Floor</th>
                <th width="100">Barcode No</th>
                <th width="100">Prod. Qty.</th>
                <th width="100">Reject Qty.</th>
                <th width="100">Fabric Grade</th>
                <th width="100">Del. Qty</th>
                <th width="100">WIP</th>
            </thead>
        </table>
        <div style="width:820px; max-height:250px; overflow-y:scroll">
            <table cellpadding="0" width="800" class="rpt_table" rules="all" border="1" id="tbl_list_search">
			<?

				if($txt_date_from!="" && $txt_date_to!="")
				{

					if($db_type==0)
					{
						$date_cond=" and a.receive_date between '".change_date_format(trim($txt_date_from), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($txt_date_to), "yyyy-mm-dd", "-")."'";
					}
					else
					{
						$date_cond=" and a.receive_date between '".change_date_format(trim($txt_date_from),'','',1)."' and '".change_date_format(trim($txt_date_to),'','',1)."'";
					}
				}
				$i=0; $tot_qnty=0;
				
	

				// echo "SELECT a.receive_date, b.barcode_no, b.qnty, b.reject_qnty, c.floor_id
				// from inv_receive_master a, pro_roll_details b, pro_grey_prod_entry_dtls c,fabric_sales_order_dtls d, order_wise_pro_details e
				// where a.id=b.mst_id and b.dtls_id=c.id and d.mst_id=e.po_breakdown_id and e.dtls_id= c.id and a.entry_form=2 and b.po_breakdown_id=$fso_id and d.color_id=$fabric_color and b.is_sales=1 and a.knitting_source=$type and a.status_active=1 and b.status_active=1 and b.entry_form=2 and e.entry_form=2 $date_cond";

				$sql_knit = sql_select("SELECT a.receive_date, b.barcode_no, b.qnty, b.reject_qnty, c.floor_id
				from inv_receive_master a, pro_roll_details b, pro_grey_prod_entry_dtls c,fabric_sales_order_dtls d, order_wise_pro_details e
				where a.id=b.mst_id and b.dtls_id=c.id and d.mst_id=e.po_breakdown_id and e.dtls_id= c.id and a.entry_form=2 and b.po_breakdown_id=$fso_id and d.color_id=$fabric_color and b.is_sales=1 and a.knitting_source=$type and a.status_active=1 and b.status_active=1 and b.entry_form=2 and e.entry_form=2 $date_cond");

				if(empty($sql_knit))
				{
					echo "Data not found";
					die;
				}

				$sql_qc = sql_select("select  b.barcode_no, c.fabric_grade
				from pro_grey_prod_entry_dtls a, pro_roll_details b, pro_qc_result_mst c
				where a.id=b.dtls_id and b.entry_form=2 and b.po_breakdown_id=$fso_id  and b.is_sales=1 and b.barcode_no = c.barcode_no and a.id=c.pro_dtls_id and a.status_active=1 and b.status_active=1 and c.status_active=1");
				foreach ($sql_qc as  $val) 
				{
					$knit_qc[$val[csf("barcode_no")]] =$val[csf("fabric_grade")];
				}

				$sql_del = sql_select("SELECT b.barcode_no, b.qnty from  pro_roll_details b where b.po_breakdown_id=$fso_id and b.is_sales=1 and b.status_active=1 and b.entry_form=56");
				foreach ($sql_del as  $val) 
				{
					$del_arr[$val[csf("barcode_no")]] =$val[csf("qnty")];
				}

                foreach($sql_knit as $row)
                {
					$i++;
                    if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
               	?>
                   <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
				   		<td width="100" align="center"><? echo $row[csf("receive_date")];?></td>
						<td width="100" align="center"><? echo $row[csf("floor_id")];?></td>
						<td width="100" align="center"><? echo $row[csf("barcode_no")];?></td>
						<td width="100" align="right"><? echo $row[csf("qnty")];?></td>
						<td width="100" align="right"><? echo $row[csf("reject_qnty")];?></td>
						<td width="100" align="right"><? echo $knit_qc[$row[csf("barcode_no")]];?></td>
						<td width="100" align="right"><? echo $del_arr[$row[csf("barcode_no")]];?></td>
						<td width="100" align="right"><? echo $row[csf("qnty")] - $del_arr[$row[csf("barcode_no")]];?></td>
                    </tr>
                <? 
					$tot_qnty+= $row[csf("qnty")];
					$tot_reject_qnty+= $row[csf("reject_qnty")];
					$tot_del_qnty+= $del_arr[$row[csf("barcode_no")]];
					$tot_wip +=$row[csf("qnty")] - $del_arr[$row[csf("barcode_no")]];
                } 
				
            ?>
            </table>
		</div> 
        <table cellpadding="0" width="800" class="rpt_table" rules="all" border="1">
            <tfoot>
                <th width="300">Total :</th>
                <th width="100"><? echo number_format($tot_qnty,2); ?></th>
                <th width="100"><? echo number_format($tot_reject_qnty,2); ?></th>
                <th width="100"></th>
                <th width="100"><? echo number_format($tot_del_qnty,2); ?></th>
                <th width="100"><? echo number_format($tot_wip,2); ?></th>
            </tfoot>
        </table>
        </div>
    </fieldset>
<?
exit();
}


if($action=="delivery_popup")
{
 	echo load_html_head_contents("Issue Details", "../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	$data=explode("=",$data);

	$fso_id=$data[0];
	$fso_no=$data[1];
	$po_job_no=$data[2];
	$fso_booking=$data[3];
	$int_reference=$data[4];
	$fabric_string=$data[5];
	$fabric_color=$data[6];

	$fab_str_arr		=explode("*",$fabric_string);
	$determination_id 	= $fab_str_arr[0];
	$color_id 			= $fab_str_arr[1];
	$body_part_id 		= $fab_str_arr[2];
	$gsm_weight 		= $fab_str_arr[3];
	$dia 				= $fab_str_arr[4];

	?>
	<script>
	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		$('#table_body tbody tr:first').hide();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+ '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		$('#table_body tbody tr:first').show();
		d.close();
	}
	var tableFilters = 
	{
		col_10: "none",
		col_operation: {
		id: ["value_total_balance"],
		col: [5],
		operation: ["sum"],
		write_method: ["innerHTML"]
		}
	}	
</script>	
	<fieldset style="width:640px; margin-left:3px">
        <p><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></p>
        <br>
        <div id="report_container" style="width:100%">
        	<table border="0" rules="all" width="600" cellpadding="0" cellspacing="0">
            	<tr>
                	<td><b>Job No : <? echo $po_job_no . ", Booking : ". $fso_booking.", Textile Ref : ".$fso_no; ?></b></td>
                </tr>
            </table>
            <br>
        <table cellpadding="0" width="820" class="rpt_table" rules="all" border="1">
            <thead>
                <th width="30">SL</th>
                <th width="100">Company</th>
                <th width="100">Location</th>
                <th width="100">Knitting Floor</th>
                <th width="50">Challan No</th>
                <th width="50">Year</th>
                <th width="100">Source</th>
                <th width="100">Knitting Company</th>
                <th width="90">Delivery Date</th>
                <th width="100">Delivery Qty</th>
            </thead>
        </table>
        <div style="width:840px; max-height:250px; overflow-y:scroll">
            <table cellpadding="0" width="820" class="rpt_table" rules="all" border="1" id="tbl_list_search">
			<?
				$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  );


				if($txt_date_from!="" && $txt_date_to!="")
				{

					if($db_type==0)
					{
						$date_cond=" and a.receive_date between '".change_date_format(trim($txt_date_from), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($txt_date_to), "yyyy-mm-dd", "-")."'";
					}
					else
					{
						$date_cond=" and a.receive_date between '".change_date_format(trim($txt_date_from),'','',1)."' and '".change_date_format(trim($txt_date_to),'','',1)."'";
					}
				}
				$i=0; $tot_qnty=0;
				

				$delivery_data_array = sql_select("SELECT c.po_breakdown_id, c.barcode_no, b.color_id from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=$fso_id and b.color_id=$fabric_color and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0  ");
			
				$barcodeArr = array();
				foreach($delivery_data_array as $row)
                {
					array_push($barcodeArr,$row[csf("barcode_no")]);
				}

				$sql_deli = sql_select("SELECT a.company_id, a.sys_number_prefix_num, a.delevery_date,  floor_ids, a.location_id, a.knitting_source, a.knitting_company, sum(c.qnty) deli_qnty
				from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b, pro_roll_details c
				where a.id= b.mst_id and a.entry_form=56 and b.id=c.dtls_id and c.entry_form=56 and c.status_active=1 and c.po_breakdown_id=$fso_id and c.is_sales=1 ".where_con_using_array($barcodeArr,0,'c.barcode_no')."
				group by a.company_id, a.sys_number_prefix_num, a.delevery_date,  floor_ids, a.location_id, a.knitting_source, a.knitting_company");

                foreach($sql_deli as $row)
                {
					$i++;
                    if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";

					if($row[csf("knitting_source")]==1)
					{
						$knitting_company = $company_library[$row[csf("knitting_company")]];
					}else{
						$knitting_company =  $supplier_details[$row[csf("knitting_company")]];
					}

					$floor_ids = explode(",",$row[csf("floor_ids")]);
					foreach ($floor_ids as $val) 
					{
						$floors = $floor_name[$val];
					}
               	?>
                   <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
				   		<td width="30" align="center"><? echo $i;?></td>
						<td width="100" align="center"><? echo $company_library[$row[csf("company_id")]];?></td>
						<td width="100" align="center"><p><? echo $location_library[$row[csf("location_id")]];?></p></td>
						<td width="100" align="right"><p><? echo $floors;?></p></td>
						<td width="50" align="right"><? echo $row[csf("sys_number_prefix_num")];?></td>
						<td width="50" align="right"><? echo date("Y", strtotime($row[csf("delevery_date")]));?></td>
						<td width="100" align="right"><? echo $knitting_source[$row[csf("knitting_source")]];?></td>
						<td width="100" align="right"><? echo $knitting_company;?></td>
						<td width="90" align="right"><? echo change_date_format($row[csf("delevery_date")]);?></td>
						<td width="100" align="right"><? echo $row[csf("deli_qnty")];?></td>
                    </tr>
                <? 
					$deli_qnty +=$row[csf("deli_qnty")];
                } 
				
            ?>
            </table>
		</div> 
        <table cellpadding="0" width="820" class="rpt_table" rules="all" border="1">
            <tfoot>
                <th width="720">Total :</th>
                <th width="100"><? echo number_format($deli_qnty,2); ?></th>
            </tfoot>
        </table>
        </div>
    </fieldset>
<?
exit();
}

if($action=="trans_in_popup" || $action=="trans_out_popup")
{
 	echo load_html_head_contents("Issue Details", "../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	$data=explode("=",$data);

	$fso_id=$data[0];
	$fso_no=$data[1];
	$po_job_no=$data[2];
	$fso_booking=$data[3];
	$int_reference=$data[4];
	$fabric_string=$data[5];

	$fab_str_arr		=explode("*",$fabric_string);
	$determination_id 	= $fab_str_arr[0];
	$color_id 			= $fab_str_arr[1];
	$body_part_id 		= $fab_str_arr[2];
	$gsm_weight 		= $fab_str_arr[3];
	$dia 				= $fab_str_arr[4];

	?>
	<script>
	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		$('#table_body tbody tr:first').hide();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+ '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		$('#table_body tbody tr:first').show();
		d.close();
	}
	var tableFilters = 
	{
		col_10: "none",
		col_operation: {
		id: ["value_total_balance"],
		col: [5],
		operation: ["sum"],
		write_method: ["innerHTML"]
		}
	}	
	</script>	
	<fieldset style="width:640px; margin-left:3px">
        <p><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></p>
        <br>
        <div id="report_container" style="width:100%">
        	<table border="0" rules="all" width="600" cellpadding="0" cellspacing="0">
            	<tr>
                	<td><b>Job No : <? echo $po_job_no . ", Booking : ". $fso_booking.", Textile Ref : ".$fso_no; ?></b></td>
                </tr>
            </table>
            <br>
        <table cellpadding="0" width="1100" class="rpt_table" rules="all" border="1">
            <thead>
			<tr>
				<th colspan="6">From Order</th>
				<th colspan="7">To Order</th>
			</tr>
			<tr>
			
			</tr>
                <th width="30">SL</th>
                <th width="70">Buyer</th>
                <th width="100">Sales Order No</th>
                <th width="100">Style Ref</th>
                <th width="100">Fabric Booking No</th>
                <th width="100">Fabric Desc</th>
                <th width="100">Transfer ID</th>
                <th width="80">Transfer Date</th>
                <th width="70">Buyer</th>
                <th width="100">Sales Order No</th>
                <th width="100">Style Ref</th>
                <th width="100">Fabric Booking No</th>
                <th width="50">QTY</th>
            </thead>							

		</table>
        <div style="width:1120px; max-height:250px; overflow-y:scroll">
            <table cellpadding="0" width="1100" class="rpt_table" rules="all" border="1" id="tbl_list_search"> 
			<?
				if($type==1)
				{
					$fso_cond = " and a.to_order_id=$fso_id";
				}else{
					$fso_cond = " and a.from_order_id=$fso_id";
				}
				
				$i=0; $transfer_qnty=0;
				$sql = "SELECT e.product_name_details, a.transfer_system_id, a.transfer_date, a.to_order_id, a.from_order_id, b.to_prod_id, sum(b.transfer_qnty) as transfer_qnty,
				c.job_no as to_sales_no, c.within_group as to_within_group, c.sales_booking_no as to_booking_no, c.style_ref_no as to_style, c.buyer_id as to_buyer, c.po_buyer as to_po_buyer,
				d.job_no as from_sales_no, d.within_group as from_within_group, d.sales_booking_no as from_booking_no, d.style_ref_no as from_style, d.buyer_id as from_buyer, d.po_buyer as from_po_buyer
				from inv_item_transfer_mst a 
				left join fabric_sales_order_mst c on a.to_order_id=c.id 
				left join fabric_sales_order_mst d on a.from_order_id=d.id, inv_item_transfer_dtls b, product_details_master e
				where a.id=b.mst_id and a.entry_form=133 and b.to_prod_id=e.id and b.status_active=1 $fso_cond and a.status_active=1 group by e.product_name_details, a.transfer_system_id, a.transfer_date, a.to_order_id, a.from_order_id, b.to_prod_id, c.job_no, c.within_group, c.sales_booking_no, c.style_ref_no, c.buyer_id, c.po_buyer,d.job_no, d.within_group, d.sales_booking_no, d.style_ref_no, d.buyer_id, d.po_buyer";
				$sql_trans = sql_select($sql);

                foreach($sql_trans as $row)
                {
					$i++;
                    if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";

					if($row[csf("from_within_group")]==1)
					{
						$from_buyer = $row[csf("from_po_buyer")];
					}else{
						$from_buyer = $row[csf("from_buyer")];
					}

					if($row[csf("to_within_group")]==1)
					{
						$to_buyer = $row[csf("to_po_buyer")];
					}else{
						$to_buyer = $row[csf("to_buyer")];
					}


					$from_sales_no = $row[csf("from_sales_no")];
					$from_style = $row[csf("from_style")];
					$from_booking_no = $row[csf("from_booking_no")];


               	?>
                   <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
				   		<td width="30" align="center"><? echo $i;?></td>
						<td width="70" align="center"><p><? echo $buyer_library[$from_buyer];?></p></td>
						<td width="100" align="center"><p><? echo $row[csf("from_sales_no")];?></p></td>
						<td width="100" align="right"><p><? echo $row[csf("from_style")];?></p></td>
						<td width="100" align="right"><p><? echo $row[csf("from_booking_no")];?></p></td>
						<td width="100"><p><? echo $row[csf("product_name_details")];?></p></td>
						
						<td width="100" align="right"><? echo $row[csf("transfer_system_id")];?></td>
						<td width="80" align="right"><? echo change_date_format($row[csf("transfer_date")]);?></td>
						<td width="70" align="center"><p><? echo $buyer_library[$to_buyer];?></p></td>
						<td width="100" align="right"><p><? echo $row[csf("to_sales_no")];?></p></td>
						<td width="100" align="right"><? echo $row[csf("to_style")];?></td>
						<td width="100" align="right"><p><? echo $row[csf("from_booking_no")];?></p></td>
						<td width="50" align="right"><p><? echo $row[csf("transfer_qnty")];?></p></td>
                    </tr>
                <? 
					$transfer_qnty +=$row[csf("transfer_qnty")];
                } 
				
            ?>
            </table>
		</div>
        <table cellpadding="0" width="1100" class="rpt_table" rules="all" border="1">
            <tfoot>
                <th width="1050">Total :</th>
                <th width="50"><? echo number_format($transfer_qnty,2); ?></th>
            </tfoot>
        </table>
        </div>
    </fieldset>
	<?
	exit();
}

if ($action == "FSO_No_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	<script>
		var hide_fso_id='<? echo $hide_fso_id; ?>';
		var selected_id = new Array, selected_name = new Array();

		function check_all_data(is_checked)
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}

		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			if ( x.style )
			{
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function set_all()
		{
			var old=document.getElementById('txt_fso_row_id').value;
			if(old!="")
			{
				old=old.split(",");
				for(var i=0; i<old.length; i++)
				{
					js_set_value( old[i] )
				}
			}
		}

		function js_set_value( str)
		{

			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );


			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 )
			{
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );

			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id =''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#hide_fso_id').val( id );
			$('#hide_fso_no').val( name );
		}

	</script>

</head>
<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:710px;">
				<table width="700" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th>Company</th>
						<th>Buyer Name</th>
						<th>Job Year</th>
						<th>Within Group</th>
						<th>FSO NO.</th>
						<th>Booking NO.</th>
						<th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th>
						<input type="hidden" name="hide_fso_no" id="hide_fso_no" value="" />
						<input type="hidden" name="hide_fso_id" id="hide_fso_id" value="" />

					</thead>
					<tbody>
						<tr>
							<td>
								<?
								echo create_drop_down( "cbo_company_name", 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $cbo_company_id, "",1 );
								?>
							</td>
							<td align="center">
								<?
								echo create_drop_down( "cbo_buyer_name", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and b.tag_company=$cbo_company_id and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
								?>
							</td>
							<td><? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?></td>
							<td><? echo create_drop_down( "cbo_within_group", 80, $yes_no,"", 1, "-- Select --", $cbo_within_group, "",0,"" );?></td>
							<td>
								<input type="text" style="width:100px" class="text_boxes" name="txt_fso_no" id="txt_fso_no" />
							</td>
							<td>
								<input type="text" style="width:100px" class="text_boxes" name="txt_booking_no" id="txt_booking_no" />
							</td>
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $cbo_company_id; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_year').value+'**'+document.getElementById('cbo_within_group').value+'**'+document.getElementById('txt_fso_no').value+'**'+document.getElementById('txt_booking_no').value+'**'+'<? echo $hidden_fso_id;?>', 'create_fso_no_search_list_view', 'search_div', 'color_wise_knitting_production_report_sales_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
							</td>
						</tr>
					</tbody>
				</table>
				<div style="margin-top:15px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_fso_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$buyer_id=$data[1];
	$year=$data[2];
	$within_group=$data[3];
	$fso_no=trim($data[4]);
	$booking_no=trim($data[5]);

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	$search_cond = "";

	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
				$buyer_cond_with_1=" and c.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
				$buyer_cond_with_2=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else
			{
				$buyer_cond_with_1 =  "";
				$buyer_cond_with_2 =  "";
			}
		}
		else
		{
			$buyer_cond_with_1 =  "";
			$buyer_cond_with_2 =  "";
		}
	}
	else
	{
		$buyer_cond_with_1 =  " and c.buyer_id=$data[1]";
		$buyer_cond_with_2 =  " and a.buyer_id=$data[1]";
	}


	if($fso_no != "")
	{
		$search_cond .= " and a.job_no_prefix_num = '$fso_no'" ;
	}
	if($booking_no != "")
	{
		$search_cond .= " and a.sales_booking_no like '%$booking_no%'" ;
	}
	if($db_type==0)
	{
		if($year!=0) $search_cond .=" and YEAR(a.insert_date)=$year"; else $search_cond .="";
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(a.insert_date,'YYYY')";
		if($year!=0) $search_cond .=" $year_field_con=$year"; else $search_cond .="";
	}

	$sql_2 ="select a.id, a.job_no, a.sales_booking_no,a.within_group,a.company_id,a.buyer_id
	from fabric_sales_order_mst a, fabric_sales_order_dtls b
	where a.id = b.mst_id and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and a.company_id = $company_id $search_cond and a.within_group = '2' $buyer_cond_with_2
	group by a.id, a.job_no, a.sales_booking_no,a.within_group,a.company_id,a.buyer_id
	order by id desc";

	$sql_1 = "select a.id, a.job_no, a.sales_booking_no,a.within_group,a.company_id,c.buyer_id
	from fabric_sales_order_mst a, fabric_sales_order_dtls b ,wo_booking_mst c
	where a.id = b.mst_id and a.sales_booking_no = c.booking_no and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1
	and b.is_deleted = 0 and a.company_id = $company_id $search_cond and a.within_group = '1' $buyer_cond_with_1
	group by a.id, a.job_no, a.sales_booking_no,a.within_group,a.company_id,c.buyer_id";

	if($within_group == 1)
	{
		$sql = $sql_1 ;
	}
	else if($within_group == 2)
	{
		$sql = $sql_2;
	}else
	{
		$sql = $sql_1." union all ". $sql_2 ;
	}
	//echo $sql;
	?>

	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="700" class="rpt_table" align="left">
		<thead>
			<th width="40">SL</th>
			<th width="130">Company</th>
			<th width="120">Buyer</th>
			<th width="150">FSO No</th>
			<th width="">Booking No</th>
		</thead>
	</table>
	<div style="width:700px; overflow-y:scroll; max-height:250px; float: left;" id="buyer_list_view" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="682" class="rpt_table" id="tbl_list_search" >
			<?php
			$i=1; $fso_row_id="";
			$nameArray=sql_select( $sql );
			foreach ($nameArray as $selectResult)
			{
				if ($i%2==0)
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";
				?>

				<tr height="20" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
					<td width="40" align="center"><?php echo "$i"; ?>
					<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $selectResult[csf('id')]; ?>"/>
					<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<?php echo $selectResult[csf('job_no')]; ?>"/>
				</td>
				<td width="130"><p><?php echo $company_arr[$selectResult[csf('company_id')]]; ?></p></td>
				<td width="120" title="<? echo $selectResult[csf('buyer_id')];?>"><?php echo $buyer_arr[$selectResult[csf('buyer_id')]]; ?></td>
				<td width="150"><p><?php echo $selectResult[csf('job_no')];?></p></td>
				<td width=""><?php echo $selectResult[csf('sales_booking_no')];?></td>
			</tr>
			<?
			$i++;
		}
		?>
	</table>
</div>

<table width="600" cellspacing="0" cellpadding="0" style="border:none" align="left">
	<tr>
		<td align="center" height="30" valign="bottom">
			<div style="width:100%">
				<div style="width:50%; float:left" align="left">
					<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
				</div>
				<div style="width:50%; float:left" align="left">
					<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
				</div>
			</div>
		</td>
	</tr>
</table>

<?
exit();
}
?>