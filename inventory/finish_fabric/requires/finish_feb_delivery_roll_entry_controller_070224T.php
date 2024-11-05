<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

include('../../../includes/common.php');

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

if ($action == "populate_data_from_barcode_bk") 
{
	$data = explode("__", $data);
	$receive_barcode_array = array();
	$nbarcode = "'" . implode("','", explode(",", $data[0])) . "'";
	/*echo "<pre>";
	print_r($data);
	die;*/
	$entry = 66;
	$buyer_name_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");

	$scanned_barcode_data = sql_select("select dtls_id,barcode_no,qnty,reprocess from pro_roll_details where entry_form in(67,68) and status_active=1 and is_deleted=0 and barcode_no in (" . $nbarcode . ")");
	foreach ($scanned_barcode_data as $row) {
		if($row[csf("entry_form")] == 68){
			$scnned[$row[csf("barcode_no")]][$row[csf("reprocess")]] = $row[csf("qnty")];
		}else{
			$self_scnned[$row[csf("barcode_no")]][$row[csf("reprocess")]] = $row[csf("barcode_no")];
		}
	}



	$QC_barcode_sql = sql_select("select a.barcode_no, b.barcode_no as qc_barcode_no, c.company_id from pro_roll_details a left join pro_qc_result_mst b on b.entry_form=267 and a.barcode_no=b.barcode_no and b.status_active=1 and b.is_deleted=0, inv_receive_master c where a.entry_form =66 and a.status_active=1 and  a.barcode_no in (" . $nbarcode . ") and a.mst_id = c.id");
	foreach ($QC_barcode_sql as $row) 
	{
		$QC_barcode_arr[$row[csf('qc_barcode_no')]] = $row[csf('qc_barcode_no')];
		$company_id= $row[csf('company_id')];
	}

	$variable_settingAutoQC = return_field_value("qc_mandatory", "variable_settings_production", "company_name =$company_id and variable_list in(48) and item_category_id=2 and is_deleted=0 and status_active=1", "qc_mandatory");

	if($variable_settingAutoQC ==1 )
	{
		if( empty($QC_barcode_arr[$row[csf('qc_barcode_no')]]))
		{
			echo 'alert("Barcode is not QC passed.\nBarcode No : '.str_replace("'", "", $nbarcode).'");';
			echo "$('#tr_" . $data[4] . "').remove();\n";
			die;
		}
	}

	/*echo "here";
	die;*/

	$issue_barcode=sql_select("select b.issue_number,a.dtls_id,a.barcode_no,a.qnty,max(a.reprocess) as reprocess,a.prev_reprocess,a.po_breakdown_id from pro_roll_details a,inv_issue_master b where b.issue_purpose=44 and a.mst_id=b.id and a.entry_form=71 and a.status_active=1 and a.is_deleted=0 and a.barcode_no in (" . $nbarcode . ")  and a.reprocess>0 group by a.dtls_id,a.barcode_no,a.qnty,a.prev_reprocess,a.po_breakdown_id,b.issue_number");

	foreach($issue_barcode as $ival)
	{
		$issue_barcode_arr[]=$ival[csf('barcode_no')];
		$issue_barcode_data[$ival[csf('barcode_no')]]['dtls_id']=$ival[csf('dtls_id')];
		$issue_barcode_data[$ival[csf('barcode_no')]]['po_breakdown_id']=$ival[csf('po_breakdown_id')];
		$issue_barcode_data[$ival[csf('barcode_no')]]['qnty']=$ival[csf('qnty')];
		$issue_barcode_data[$ival[csf('barcode_no')]]['issue_number']=$ival[csf('issue_number')];
		$issue_barcode_check[$ival[csf('barcode_no')]][$ival[csf('reprocess')]]=$ival[csf('barcode_no')];
	}

	$issuebarcode = "'" . implode("','", $issue_barcode_arr) . "'";
	if(count($issue_barcode_arr)>0)
	{
		$issued_barcode_cond=" and c.barcode_no not in (" . $issuebarcode . ")";
	}

	$sql_data="select a.id,a.entry_form, a.company_id, a.recv_number, a.receive_basis, a.receive_date, a.booking_no, a.knitting_source, a.knitting_company, a.knitting_location_id, b.id as dtls_id,b.prod_id,b.color_id ,b.batch_id, b.fabric_description_id,b.body_part_id,b.dia_width_type, b.gsm,b.production_qty, b.width, c.barcode_no, c.roll_id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.reject_qnty,c.qc_pass_qnty, c.dtls_id as dtlsid,c.reprocess,c.roll_id as previous_roll_id,c.is_sales,c.booking_without_order,c.booking_no from inv_receive_master a, pro_finish_fabric_rcv_dtls b,pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=$entry and c.entry_form=$entry and c.status_active=1 and c.is_deleted=0 and c.barcode_no in (" . $nbarcode . ") $issued_barcode_cond";

	if(count($issue_barcode_arr)>0)
	{
		$sql_data.=" union all
		SELECT a.id, a.entry_form, a.company_id , a.recv_number, a.receive_basis,a.receive_date, a.booking_no, a.knitting_source, a.knitting_company, a.knitting_location_id, b.id as dtls_id, b.prod_id,b.color_id,b.batch_id,b.fabric_description_id, b.body_part_id,b.dia_width_type,  b.gsm,c.qnty  as production_qty,b.width, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id,c.qnty,c.reject_qnty,c.qc_pass_qnty, c.dtls_id as dtlsid,max(c.reprocess) reprocess,c.roll_id as previous_roll_id,c.is_sales,c.booking_without_order,c.booking_no FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(37,7,68) and c.entry_form in(37,7,68) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in(".$issuebarcode.") group by a.id, a.entry_form, a.company_id , a.recv_number, a.receive_basis,a.receive_date, a.booking_no, a.knitting_source, a.knitting_company, a.knitting_location_id, b.id, b.prod_id,b.color_id,b.batch_id,b.fabric_description_id, b.body_part_id,b.dia_width_type,  b.gsm,c.qnty,b.width, c.barcode_no, c.id,c.roll_id, c.roll_no, c.po_breakdown_id,c.qnty,c.reject_qnty, c.qc_pass_qnty,c.dtls_id,c.is_sales, c.booking_without_order,c.booking_no";
	}
	
	$data_array = sql_select($sql_data);
	//echo "alert(".count($data_array).");\n";die;
	foreach ($data_array as $val)
	{
		$all_deter_arr[$val[csf("fabric_description_id")]] =$val[csf("fabric_description_id")];

		if($val[csf("is_sales")] == 1){
			$sales_id_arr[] = $val[csf("po_breakdown_id")];
		}else{

			$po_arr[$val[csf("po_breakdown_id")]] = $val[csf("po_breakdown_id")];
		}
	}

	if(!empty($po_arr)){
		$data_array_po_ref = sql_select("SELECT a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst and b.id in (" . implode(",",$po_arr) . ")");
		$po_details_array = array();
		foreach ($data_array_po_ref as $row) {
			$po_details_array[$row[csf("po_id")]]['job_no'] = $row[csf("job_no_prefix_num")];
			$po_details_array[$row[csf("po_id")]]['buyer_name'] = $buyer_name_array[$row[csf("buyer_name")]];
			$po_details_array[$row[csf("po_id")]]['style_ref_no'] = $row[csf("style_ref_no")];
			$po_details_array[$row[csf("po_id")]]['year'] = date("Y", strtotime($row[csf("insert_date")]));
			$po_details_array[$row[csf("po_id")]]['po_number'] = $row[csf("po_number")];
		}
		unset($data_array_po_ref);
	}

	$sales_arr=$sales_booking_arr=array();
	if(!empty($sales_id_arr)){
		$sql_sales=sql_select("select id,job_no,within_group,sales_booking_no,booking_date,po_job_no,po_buyer from fabric_sales_order_mst where id in (" . implode(",",$sales_id_arr) . ") and status_active=1 and is_deleted=0");
		foreach ($sql_sales as $sales_row) {
			$sales_arr[$sales_row[csf('id')]]["sales_order_no"] 		= $sales_row[csf('job_no')];
			$sales_arr[$sales_row[csf('id')]]["within_group"] 			= $sales_row[csf('within_group')];
			$sales_arr[$sales_row[csf('id')]]["sales_booking_no"] 		= $sales_row[csf('sales_booking_no')];
			$sales_arr[$sales_row[csf('id')]]["po_job_no"] 				= $sales_row[csf('po_job_no')];
			$sales_arr[$sales_row[csf('id')]]["po_buyer"] 				= $sales_row[csf('po_buyer')];
			$sales_arr[$sales_row[csf('id')]]['year'] 					= date("Y", strtotime($sales_row[csf("booking_date")]));
			$sales_booking_arr[] = "'".$sales_row[csf('sales_booking_no')]."'";
		}
		unset($sql_sales);
	}

	$company_name_array = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$color_name_array = return_library_array("select id, color_name from lib_color", "id", "color_name");

	$all_deter_cond="";
	$all_deter_arr = array_filter($all_deter_arr);
	if(count($all_deter_arr)>0)
	{
		$deter_cond="";
		$all_deter_nos=implode(",",$all_deter_arr);
		if($db_type==2 && count($all_deter_arr)>999)
		{
			$all_deter_chunk=array_chunk($all_deter_arr,999) ;
			foreach($all_deter_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$deter_cond.=" a.id in($chunk_arr_value) or ";
			}

			$all_deter_cond.=" and (".chop($deter_cond,'or ').")";
		}
		else
		{
			$all_deter_cond=" and a.id in($all_deter_nos)";
		}
	}

	$composition_arr = array();
	$constructtion_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id $all_deter_cond";
	$data_array_deter = sql_select($sql_deter);
	foreach ($data_array_deter as $row) {
		$constructtion_arr[$row[csf('id')]] = $row[csf('construction')];
		$composition_arr[$row[csf('id')]] .= $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "% ";
	}
	unset($data_array_deter);

	$all_po_cond="";
	$all_po_arr = array_filter($po_arr);
	if(count($all_po_arr)>0)
	{
		$poCond="";
		$all_po_nos=implode(",",$all_po_arr);
		if($db_type==2 && count($all_po_arr)>999)
		{
			$all_po_chunk=array_chunk($all_po_arr,999) ;
			foreach($all_po_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$poCond.=" c.id in($chunk_arr_value) or ";
			}
			$all_po_cond.=" and (".chop($poCond,'or ').")";
		}
		else
		{
			$all_po_cond="  c.id in($all_po_nos)";
		}

		$booking_cond = (!empty($sales_booking_arr))?" and a.booking_no in(".implode(",",$sales_booking_arr).")":"";
		$sql_job="select a.buyer_id, b.job_no as job_no_mst,b.booking_no,b.po_break_down_id,c.po_number, c.shipment_date
		from wo_po_break_down c,wo_booking_dtls b,wo_booking_mst a
		where $all_po_cond and c.id=b.po_break_down_id and b.booking_no=a.booking_no $booking_cond and a.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
		group by b.booking_no,a.buyer_id,b.po_break_down_id, c.po_number,c.shipment_date";

		$job_sql_result = sql_select($sql_job);
		foreach ($job_sql_result as $job_row) {
			$job_arr[$job_row[csf('booking_no')]]["job_no_mst"] 		= $job_row[csf('job_no_mst')];
			$job_arr[$job_row[csf('booking_no')]]['year'] 				= date("Y", strtotime($job_row[csf("shipment_date")]));
			$job_arr[$job_row[csf('booking_no')]]["buyer_name"] 		= $job_row[csf('buyer_name')];
			$job_arr[$job_row[csf('booking_no')]]["po_number"] 			= $job_row[csf('po_number')];
		}
		unset($sql_job);
	}

	$inc = 0;
	if (count($data_array) > 0) 
	{
		foreach ($data_array as $row) 
		{
			if($row[csf("entry_form")]==66)
			{
				$barcode_process=$row[csf("reprocess")];
				$roll_qty=$row[csf("qc_pass_qnty")];
				$isSales=$row[csf("is_sales")];
				$booking_without_order=$row[csf("booking_without_order")];
				$booking_no=$row[csf("booking_no")];
				$po_breakdown_id=$row[csf("po_breakdown_id")];
				$recv_number=$row[csf("recv_number")];

				$sales_booking_no 	= $sales_arr[$po_breakdown_id]["sales_booking_no"];
				$within_group 		= $sales_arr[$po_breakdown_id]["within_group"];

				if($row[csf("is_sales")] == 1) {
					$order 				= $sales_arr[$po_breakdown_id]["sales_order_no"];
					$year 	 			= $sales_arr[$po_breakdown_id]["year"];
					if($within_group==1){
						$job_no 			= $sales_arr[$po_breakdown_id]["po_job_no"];
						$buyer_name 		= $buyer_name_array[$sales_arr[$po_breakdown_id]["po_buyer"]];
					}else{
						$order 				= $sales_arr[$po_breakdown_id]["sales_order_no"];
						$job_no 			= "";
						$buyer_name 		= $buyer_name_array[$job_arr[$sales_booking_no]["buyer_name"]];
					}
				}
				else if($booking_without_order==1)
				{
					$order="";
					$buyer_name="";
					$job_no="";
					$year="";
				}
				else{
					$order 				= $po_details_array[$po_breakdown_id]['po_number'];
					$buyer_name 		= $po_details_array[$po_breakdown_id]['buyer_name'];
					$job_no 			= $po_details_array[$po_breakdown_id]['job_no'];
					$year 				= $po_details_array[$po_breakdown_id]['year'];
				}

				if ($self_scnned[$row[csf("barcode_no")]][$barcode_process]=="")
				{
					if ($data[2] == 1 && $inc == 0) {

						echo "$('#cbo_company_id').val('" . $row[csf("company_id")] . "');\n";
						echo "$('#cbo_knitting_source').val('" . $row[csf("knitting_source")] . "');\n";
						if ($row[csf("knitting_source")] == 1) {
							echo "$('#txt_knit_company').val('" . $company_name_array[$row[csf("knitting_company")]] . "');\n";
							$knit_location_name = return_field_value("location_name", "lib_location", " id=" . $row[csf('knitting_location_id')] . "");
							echo "$('#txt_knitting_location').val('" . $knit_location_name . "');\n";

						} else if ($row[csf("knitting_source")] == 3) {
							echo "$('#txt_knit_company').val('" . $supplier_arr[$row[csf("knitting_company")]] . "');\n";
						}
						echo "$('#knit_company_id').val('" . $row[csf("knitting_company")] . "');\n";
						echo "$('#knit_location_id').val('" . $row[csf('knitting_location_id')] . "');\n";

						$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$row[csf("company_id")]."'  and module_id=7 and report_id=167 and is_deleted=0 and status_active=1");
						$print_report_format_arr=explode(",",$print_report_format);
						echo "$('#print1').hide();\n";
						echo "$('#print2').hide();\n";
						echo "$('#print_barcode').hide();\n";
						echo "$('#btn_fabric_details').hide();\n";
						echo "$('#printFso_v2').hide();\n";
						if($print_report_format != "")
						{
							foreach($print_report_format_arr as $id)
							{
								if($id==86){echo "$('#print1').show();\n";}
								if($id==84){echo "$('#print2').show();\n";}
								if($id==68){echo "$('#print_barcode').show();\n";}
								if($id==69){echo "$('#btn_fabric_details').show();\n";}
								if($id==764){echo "$('#printFso_v2').show();\n";}
							}
						}
						else
						{
							echo "$('#print1').show();\n";
							echo "$('#print2').show();\n";
							echo "$('#print_barcode').show();\n";
							echo "$('#btn_fabric_details').show();\n";
							echo "$('#printFso_v2').show();\n";
						}
					}

					echo "$('#sl_" . $data[1] . "').text('" . $data[1] . "');\n";
					echo "$('#barcode_" . $data[1] . "').text('" . $row[csf("barcode_no")] . "');\n";
					echo "$('#roll_" . $data[1] . "').text('" . $row[csf("roll_no")] . "');\n";
					echo "$('#prodQty_" . $data[1] . "').text('" . $row[csf("production_qty")] . "');\n";
					echo "$('#prodQnty_" . $data[1] . "').val('" . number_format($row[csf("production_qty")], 2) . "');\n";

					echo "$('#rejectQty_" . $data[1] . "').text('" . number_format($row[csf("reject_qnty")], 2) . "');\n";
					echo "$('#rejectQnty_" . $data[1] . "').val('" . number_format($row[csf("reject_qnty")], 2) . "');\n";
					echo "$('#currentDelivery_" . $data[1] . "').val('" . $roll_qty . "');\n";
					echo "$('#rollQty_" . $data[1] . "').val('" . $roll_qty . "');\n";
					echo "$('#IsSalesId_" . $data[1] . "').val('" . $isSales . "');\n";
					echo "$('#bookingWithoutOrder_" . $data[1] . "').val('" . $booking_without_order . "');\n";
					echo "$('#bookingNumber_" . $data[1] . "').val('" . $booking_no . "');\n";
					$batch = return_field_value("batch_no", "pro_batch_create_mst", " id=" . $row[csf('batch_id')] . "");

					echo "$('#batch_" . $data[1] . "').text('" . $batch . "');\n";
					echo "$('#bodypart_" . $data[1] . "').text('" . $body_part[$row[csf("body_part_id")]] . "');\n";
					echo "$('#cons_" . $data[1] . "').text('" . $constructtion_arr[$row[csf("fabric_description_id")]] . "');\n";
					echo "$('#comps_" . $data[1] . "').text('" . $composition_arr[$row[csf("fabric_description_id")]] . "');\n";
					echo "$('#color_" . $data[1] . "').text('" . $color_name_array[$row[csf("color_id")]] . "');\n";
					echo "$('#gsm_" . $data[1] . "').text('" . $row[csf("gsm")] . "');\n";
					echo "$('#dia_" . $data[1] . "').text('" . $row[csf("width")] . "');\n";
					echo "$('#widthTipe_" . $data[1] . "').text('" . $fabric_typee[$row[csf("dia_width_type")]] . "');\n";
					echo "$('#widthTypeId_" . $data[1] . "').val('" . $row[csf("dia_width_type")] . "');\n";
					echo "$('#batchId_" . $data[1] . "').val('" . $row[csf("batch_id")] . "');\n";
					echo "$('#knitSource_" . $data[1] . "').text('" . $knitting_source[$row[csf("knitting_source")]] . "');\n";

					if ($row[csf("knitting_source")] == 1) {
						echo "$('#finishCompany_" . $data[1] . "').text('" . $company_name_array[$row[csf("knitting_company")]] . "');\n";
					} else if ($row[csf("knitting_source")] == 3) {
						echo "$('#finishCompany_" . $data[1] . "').text('" . $supplier_arr[$row[csf("knitting_company")]] . "');\n";
					}
					echo "$('#prodDate_" . $data[1] . "').text('" . $row[csf("receive_date")] . "');\n";
					echo "$('#year_" . $data[1] . "').text('" . $year . "');\n";
					echo "$('#job_" . $data[1] . "').text('" . $job_no . "');\n";
					echo "$('#buyer_" . $data[1] . "').text('" . $buyer_name . "');\n";
					echo "$('#order_" . $data[1] . "').text('" . $order . "');\n";
					echo "$('#prodId_" . $data[1] . "').text('" . $row[csf("prod_id")] . "');\n";
					echo "$('#systemId_" . $data[1] . "').text('" . $recv_number . "');\n";
					echo "$('#barcodeNo_" . $data[1] . "').val('" . $row[csf("barcode_no")] . "');\n";
					echo "$('#productionId_" . $data[1] . "').val('" . $row[csf("id")] . "');\n";
					echo "$('#productionDtlsId_" . $data[1] . "').val('" . $row[csf("dtls_id")] . "');\n";
					echo "$('#deterId_" . $data[1] . "').val('" . $row[csf("fabric_description_id")] . "');\n";
					echo "$('#productId_" . $data[1] . "').val('" . $row[csf("prod_id")] . "');\n";
					echo "$('#orderId_" . $data[1] . "').val('" . $po_breakdown_id . "');\n";
					echo "$('#rollId_" . $data[1] . "').val('" . $row[csf("roll_id")] . "');\n";
					echo "$('#reProcess_" . $data[1] . "').val('" . $barcode_process . "');\n";
					echo "$('#prereProcess_" . $data[1] . "').val('" . $barcode_process . "');\n";
					echo "$('#dtlsId_" . $data[1] . "').val('');\n";
					echo "$('#colorId_" . $data[1] . "').val('" . $row[csf("color_id")] . "');\n";
					echo "$('#bodyPartId_" . $data[1] . "').val('" . $row[csf("body_part_id")] . "');\n";
					echo "$('#finMstId_" . $data[1] . "').val('" . $row[csf("id")] . "');\n";
					echo "$('#decrease_" . $data[1] . "').removeAttr('onclick').attr('onclick','fn_deleteRow(" . $data[1] . ")');\n";
					echo "$('#currentDelivery_" . $data[1] . "').removeAttr('onKeyUp').attr('onKeyUp','check_qty(" . $data[1] . ")');\n";

					if ($scnned[$row[csf("barcode_no")]][$barcode_process] != '')
						echo "$('#currentDelivery_" . $data[1] . "').attr('disabled','disabled');\n";

					$inc++;
					$data[1]++;
				}
			}
			else
			{
				$isSales=$row[csf("is_sales")];
				$booking_without_order=$row[csf("booking_without_order")];
				$booking_no=$row[csf("booking_no")];
				$barcode_process=($row[csf("reprocess")]*1)+1;
				$roll_qty=$issue_barcode_data[$row[csf("barcode_no")]]['qnty'];
				$recv_number=$issue_barcode_data[$row[csf("barcode_no")]]['issue_number'];
				$po_breakdown_id=$issue_barcode_data[$row[csf("barcode_no")]]['po_breakdown_id'];

				$sales_booking_no 	= $sales_arr[$po_breakdown_id]["sales_booking_no"];
				$within_group 		= $sales_arr[$po_breakdown_id]["within_group"];
				if($row[csf("is_sales")] == 1){
					if($within_group==1){
						$order 				= $sales_arr[$po_breakdown_id]["sales_order_no"];
						$job_no 			= $job_arr[$sales_booking_no]["job_no_mst"];
						$year 	 			= $job_arr[$sales_booking_no]["year"];
						$buyer_name 		= $job_arr[$sales_booking_no]["buyer_name"];
					}else{
						$order 				= $sales_arr[$po_breakdown_id]["sales_order_no"];
						$job_no 			= "";
						$year 	 			= $job_arr[$sales_booking_no]["year"];
						$buyer_name 		= $job_arr[$sales_booking_no]["buyer_name"];
					}
				}
				else if($booking_without_order==1)
				{
					$order="";
					$buyer_name="";
					$job_no="";
					$year="";
				}
				else
				{
					$order 				= $po_details_array[$po_breakdown_id]['po_number'];
					$buyer_name 		= $po_details_array[$po_breakdown_id]['buyer_name'];
					$job_no 			= $po_details_array[$po_breakdown_id]['job_no'];
					$year 				= $po_details_array[$po_breakdown_id]['year'];
				}

				if ($self_scnned[$row[csf("barcode_no")]][$barcode_process]=="" && $issue_barcode_check[$row[csf("barcode_no")]][$barcode_process]!="")
				{
					if($row[csf("entry_form")]==68) $roll_id=$row[csf("previous_roll_id")];
					else 							$roll_id=$row[csf("roll_id")];
					if ($data[2] == 1 && $inc == 0) {
						echo "$('#cbo_company_id').val('" . $row[csf("company_id")] . "');\n";
						echo "$('#cbo_knitting_source').val('" . $row[csf("knitting_source")] . "');\n";
						if ($row[csf("knitting_source")] == 1) {
							echo "$('#txt_knit_company').val('" . $company_name_array[$row[csf("knitting_company")]] . "');\n";
							$knit_location_name = return_field_value("location_name", "lib_location", " id=" . $row[csf('knitting_location_id')] . "");
							echo "$('#txt_knitting_location').val('" . $knit_location_name . "');\n";
						} else if ($row[csf("knitting_source")] == 3) {
							echo "$('#txt_knit_company').val('" . $supplier_arr[$row[csf("knitting_company")]] . "');\n";
						}
						echo "$('#knit_company_id').val('" . $row[csf("knitting_company")] . "');\n";
						echo "$('#knit_location_id').val('" . $row[csf('knitting_location_id')] . "');\n";

						$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$row[csf("company_id")]."'  and module_id=7 and report_id=167 and is_deleted=0 and status_active=1");
						$print_report_format_arr=explode(",",$print_report_format);
						echo "$('#print1').hide();\n";
						echo "$('#print2').hide();\n";
						echo "$('#print_barcode').hide();\n";
						echo "$('#btn_fabric_details').hide();\n";
						echo "$('#printFso_v2').hide();\n";
						if($print_report_format != "")
						{
							foreach($print_report_format_arr as $id)
							{
								if($id==86){echo "$('#print1').show();\n";}
								if($id==84){echo "$('#print2').show();\n";}
								if($id==68){echo "$('#print_barcode').show();\n";}
								if($id==69){echo "$('#btn_fabric_details').show();\n";}
								if($id==764){echo "$('#printFso_v2').show();\n";}
							}
						}
						else
						{
							echo "$('#print1').show();\n";
							echo "$('#print2').show();\n";
							echo "$('#print_barcode').show();\n";
							echo "$('#btn_fabric_details').show();\n";
							echo "$('#printFso_v2').show();\n";
						}
					}

					echo "$('#sl_" . $data[1] . "').text('" . $data[1] . "');\n";
					echo "$('#barcode_" . $data[1] . "').text('" . $row[csf("barcode_no")] . "');\n";
					echo "$('#roll_" . $data[1] . "').text('" . $row[csf("roll_no")] . "');\n";
					echo "$('#prodQty_" . $data[1] . "').text('" . $row[csf("production_qty")] . "');\n";
					echo "$('#prodQnty_" . $data[1] . "').val('" . number_format($row[csf("production_qty")], 2) . "');\n";

					echo "$('#rejectQty_" . $data[1] . "').text('" . number_format($row[csf("reject_qnty")], 2) . "');\n";
					echo "$('#rejectQnty_" . $data[1] . "').val('" . number_format($row[csf("reject_qnty")], 2) . "');\n";
					echo "$('#currentDelivery_" . $data[1] . "').val('" . $roll_qty . "');\n";
					echo "$('#rollQty_" . $data[1] . "').val('" . $roll_qty . "');\n";
					echo "$('#IsSalesId_" . $data[1] . "').val('" . $isSales . "');\n";
					echo "$('#bookingWithoutOrder_" . $data[1] . "').val('" . $booking_without_order . "');\n";
					echo "$('#bookingNumber_" . $data[1] . "').val('" . $booking_no . "');\n";
					$batch = return_field_value("batch_no", "pro_batch_create_mst", " id=" . $row[csf('batch_id')] . "");

					echo "$('#batch_" . $data[1] . "').text('" . $batch . "');\n";
					echo "$('#bodypart_" . $data[1] . "').text('" . $body_part[$row[csf("body_part_id")]] . "');\n";
					echo "$('#cons_" . $data[1] . "').text('" . $constructtion_arr[$row[csf("fabric_description_id")]] . "');\n";
					echo "$('#comps_" . $data[1] . "').text('" . $composition_arr[$row[csf("fabric_description_id")]] . "');\n";
					echo "$('#color_" . $data[1] . "').text('" . $color_name_array[$row[csf("color_id")]] . "');\n";
					echo "$('#gsm_" . $data[1] . "').text('" . $row[csf("gsm")] . "');\n";
					echo "$('#dia_" . $data[1] . "').text('" . $row[csf("width")] . "');\n";
					echo "$('#widthTipe_" . $data[1] . "').text('" . $fabric_typee[$row[csf("dia_width_type")]] . "');\n";
					echo "$('#widthTypeId_" . $data[1] . "').val('" . $row[csf("dia_width_type")] . "');\n";
					echo "$('#batchId_" . $data[1] . "').val('" . $row[csf("batch_id")] . "');\n";
					echo "$('#knitSource_" . $data[1] . "').text('" . $knitting_source[$row[csf("knitting_source")]] . "');\n";

					if ($row[csf("knitting_source")] == 1) {
						echo "$('#finishCompany_" . $data[1] . "').text('" . $company_name_array[$row[csf("knitting_company")]] . "');\n";
					} else if ($row[csf("knitting_source")] == 3) {
						echo "$('#finishCompany_" . $data[1] . "').text('" . $supplier_arr[$row[csf("knitting_company")]] . "');\n";
					}
					echo "$('#prodDate_" . $data[1] . "').text('" . $row[csf("receive_date")] . "');\n";
					echo "$('#year_" . $data[1] . "').text('" . $po_details_array[$po_breakdown_id]['year'] . "');\n";
					echo "$('#job_" . $data[1] . "').text('" . $job_no . "');\n";
					echo "$('#buyer_" . $data[1] . "').text('" . $buyer_name . "');\n";
					echo "$('#order_" . $data[1] . "').text('" . $order . "');\n";
					echo "$('#prodId_" . $data[1] . "').text('" . $row[csf("prod_id")] . "');\n";
					echo "$('#systemId_" . $data[1] . "').text('" . $recv_number . "');\n";
					echo "$('#barcodeNo_" . $data[1] . "').val('" . $row[csf("barcode_no")] . "');\n";
					echo "$('#productionId_" . $data[1] . "').val('" . $row[csf("id")] . "');\n";
					echo "$('#productionDtlsId_" . $data[1] . "').val('" . $row[csf("dtls_id")] . "');\n";
					echo "$('#deterId_" . $data[1] . "').val('" . $row[csf("fabric_description_id")] . "');\n";
					echo "$('#productId_" . $data[1] . "').val('" . $row[csf("prod_id")] . "');\n";
					echo "$('#orderId_" . $data[1] . "').val('" . $po_breakdown_id . "');\n";
					echo "$('#rollId_" . $data[1] . "').val('" . $roll_id. "');\n";
					echo "$('#reProcess_" . $data[1] . "').val('" . $barcode_process . "');\n";
					echo "$('#prereProcess_" . $data[1] . "').val('" . $barcode_process . "');\n";
					echo "$('#dtlsId_" . $data[1] . "').val('" . $row[csf("dtlsid")] . "');\n";
					echo "$('#colorId_" . $data[1] . "').val('" . $row[csf("color_id")] . "');\n";
					echo "$('#bodyPartId_" . $data[1] . "').val('" . $row[csf("body_part_id")] . "');\n";
					echo "$('#finMstId_" . $data[1] . "').val('" . $row[csf("id")] . "');\n";
					echo "$('#decrease_" . $data[1] . "').removeAttr('onclick').attr('onclick','fn_deleteRow(" . $data[1] . ")');\n";
					echo "$('#currentDelivery_" . $data[1] . "').removeAttr('onKeyUp').attr('onKeyUp','check_qty(" . $data[1] . ")');\n";

					if ($scnned[$row[csf("barcode_no")]][$barcode_process] != '')
						echo "$('#currentDelivery_" . $data[1] . "').attr('disabled','disabled');\n";
					else 	echo "$('#currentDelivery_" . $data[1] . "').attr('readonly',false);\n";

					$inc++;
					$data[1]++;
				}
			}
		}
	} else {
		echo "alert('Not a valid barcode or process incomplete. please check');\n";
		echo "$('#tr_" . $data[1] . "').remove();";
		die;
	}
	die;
}

if ($action == "populate_data_update_barcode_bk") 
{
	$data = explode("__", $data);
	$receive_barcode_array = array();
	$mst_id=$data[0];
	$nbarcode = "'" . implode("','", explode(",", $data[1])) . "'";
	$update_id=$data[1];
	// echo $data[1].'='.$data[2];die;
	$scanned_barcode_data = sql_select("select barcode_no,reprocess,dtls_id,qc_pass_qnty,qnty,po_breakdown_id ,prev_reprocess from pro_roll_details where mst_id=$mst_id and entry_form=67 and status_active=1 and is_deleted=0 and barcode_no in (" . $nbarcode . ")");
	foreach ($scanned_barcode_data as $row) {
		$scnned[$row[csf("barcode_no")]][$row[csf("reprocess")]] = $row[csf("qnty")];
	}
		//die;

	$selfscanned_barcode_data = sql_select("select barcode_no,reprocess,dtls_id,qc_pass_qnty,qnty,po_breakdown_id ,prev_reprocess from pro_roll_details where mst_id=$mst_id and entry_form=67 and status_active=1 and is_deleted=0 and barcode_no in (" . $nbarcode . ")");
	foreach ($selfscanned_barcode_data as $row) {
		if($row[csf("reprocess")]==0)
		{
			$production_barcode_arr[]=$row[csf('barcode_no')];
		}
		else
		{
			$issue_barcode_arr[]=$row[csf('barcode_no')];
		}

		$self_scnned[$row[csf("barcode_no")]]['prev_reprocess'] = $row[csf("prev_reprocess")];
		$self_scnned[$row[csf("barcode_no")]]['reprocess'] = $row[csf("reprocess")];
		$self_scnned[$row[csf("barcode_no")]]['po_breakdown_id'] = $row[csf("po_breakdown_id")];
		$self_scnned[$row[csf("barcode_no")]]['dtls_id'] = $row[csf("dtls_id")];
		$self_scnned[$row[csf("barcode_no")]]['qc_pass_qnty'] = $row[csf("qc_pass_qnty")];
		$self_scnned[$row[csf("barcode_no")]]['qnty'] = $row[csf("qnty")];
	}
	$issuebarcode = "'" . implode("','", $issue_barcode_arr) . "'";
	$productionbarcode = "'" . implode("','", $production_barcode_arr) . "'";

	$buyer_name_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");

	$data_array = sql_select("SELECT a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id FROM wo_po_details_master a, wo_po_break_down b , pro_roll_details c WHERE a.job_no=b.job_no_mst and b.id=c.po_breakdown_id and c.barcode_no in (" . $nbarcode . ") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ");
	$po_details_array = array();
	foreach ($data_array as $row) {
		$po_details_array[$row[csf("po_id")]]['job_no'] = $row[csf("job_no_prefix_num")];
		$po_details_array[$row[csf("po_id")]]['buyer_name'] = $buyer_name_array[$row[csf("buyer_name")]];
		$po_details_array[$row[csf("po_id")]]['style_ref_no'] = $row[csf("style_ref_no")];
		$po_details_array[$row[csf("po_id")]]['year'] = date("Y", strtotime($row[csf("insert_date")]));
		$po_details_array[$row[csf("po_id")]]['po_number'] = $row[csf("po_number")];
	}

	$sales_arr=array();
	$sql_sales=sql_select("select id,job_no,within_group,sales_booking_no,booking_date,po_job_no,po_buyer from fabric_sales_order_mst where status_active=1 and is_deleted=0");
	foreach ($sql_sales as $sales_row) {
		$sales_arr[$sales_row[csf('id')]]["sales_order_no"] 	= $sales_row[csf('job_no')];
		$sales_arr[$sales_row[csf('id')]]["within_group"] 		= $sales_row[csf('within_group')];
		$sales_arr[$sales_row[csf('id')]]["sales_booking_no"] 	= $sales_row[csf('sales_booking_no')];
		$sales_arr[$sales_row[csf('id')]]["po_job_no"] 			= $sales_row[csf('po_job_no')];
		$sales_arr[$sales_row[csf('id')]]["booking_date"] 		= date("Y", strtotime($sales_row[csf("booking_date")]));
		$sales_arr[$sales_row[csf('id')]]["po_buyer"] 			= $sales_row[csf('po_buyer')];
	}

	$composition_arr = array();
	$constructtion_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		$constructtion_arr[$row[csf('id')]] = $row[csf('construction')];
		$composition_arr[$row[csf('id')]] .= $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "% ";
	}

	$company_name_array = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$color_name_array = return_library_array("select id, color_name from lib_color", "id", "color_name");
	$entry = 66;

	$issue_barcode=sql_select("select b.issue_number,a.dtls_id,a.barcode_no,a.qnty,max(a.reprocess) as reprocess,a.prev_reprocess,a.po_breakdown_id from pro_roll_details a,inv_issue_master b where a.mst_id=b.id and a.entry_form=71 and a.status_active=1 and a.is_deleted=0 and a.barcode_no in (" . $issuebarcode . ")  and a.reprocess>0 group by a.dtls_id,a.barcode_no,a.qnty,a.prev_reprocess,a.po_breakdown_id,b.issue_number");

	foreach($issue_barcode as $ival)
	{
		$issue_barcode_data[$ival[csf('barcode_no')]]['issue_number']=$ival[csf('issue_number')];
	}


	if(count($production_barcode_arr)>0)
	{
		$sql_data="SELECT a.id,a.entry_form, a.company_id, a.recv_number, a.receive_basis, a.receive_date, a.booking_no, a.knitting_source, a.knitting_company, a.knitting_location_id, b.id as dtls_id,b.prod_id,b.color_id ,b.batch_id, b.fabric_description_id,b.body_part_id,b.dia_width_type, b.gsm,b.production_qty, b.width, c.barcode_no, c.roll_id as roll_id, c.roll_no, c.po_breakdown_id,c.is_sales, c.qnty,c.reject_qnty,c.qc_pass_qnty, c.dtls_id as dtlsid,c.reprocess,c.roll_id as previous_roll_id,c.is_sales,c.booking_without_order,c.booking_no
		FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b,pro_roll_details c
		WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=$entry and c.entry_form=$entry and c.status_active=1 and c.is_deleted=0 and c.barcode_no in (" . $productionbarcode . ")";

	}

	if(count($issue_barcode_arr)>0)
	{
		if(count($production_barcode_arr)>0)
		{
			$sql_data.=" union all ";
		}

		$sql_data.="SELECT a.id, a.entry_form, a.company_id , a.recv_number, a.receive_basis,a.receive_date, a.booking_no, a.knitting_source, a.knitting_company, a.knitting_location_id, b.id as dtls_id, b.prod_id,b.color_id,b.batch_id,b.fabric_description_id, b.body_part_id,b.dia_width_type,  b.gsm,c.qnty  as production_qty,b.width, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.is_sales, c.qnty,c.reject_qnty,c.qc_pass_qnty, c.dtls_id as dtlsid,max(c.reprocess) reprocess,c.roll_id as previous_roll_id,c.is_sales,c.booking_without_order,c.booking_no FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(37,7,68) and c.entry_form in(37,7,68) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in(".$issuebarcode.")
		group by a.id, a.entry_form, a.company_id , a.recv_number, a.receive_basis,a.receive_date, a.booking_no, a.knitting_source, a.knitting_company,a.knitting_location_id, b.id, b.prod_id,b.color_id,b.batch_id,b.fabric_description_id, b.body_part_id,b.dia_width_type,  b.gsm,c.qnty,b.width, c.barcode_no, c.id,c.roll_id,c.roll_no, c.po_breakdown_id,c.is_sales,c.qnty,c.reject_qnty,c.qc_pass_qnty, c.dtls_id,c.booking_without_order,c.booking_no";
	}
	// echo $sql_data;
	$data_array = sql_select($sql_data);
	$poIDs="";
	foreach ($data_array as $row)
	{
		$poIDs.=$row[csf('po_breakdown_id')].',';
	}

	$poIDs_all=rtrim($poIDs,",");
	$poIDs_alls=explode(",",$poIDs_all);
	$poIDs_alls=array_chunk($poIDs_alls,999);
	$po_id_cond=" and";
	foreach($poIDs_alls as $dtls_id)
	{
		if($po_id_cond==" and")  $po_id_cond.="(c.id in(".implode(',',$dtls_id).")"; else $po_id_cond.=" or c.id in(".implode(',',$dtls_id).")";
	}
	$po_id_cond.=")";

	$job_arr=array();
	$sql_job=sql_select("select b.booking_no,e.buyer_name, e.job_no_prefix_num,e.insert_date from wo_booking_dtls b,wo_po_break_down c, wo_po_details_master e where b.po_break_down_id=c.id and c.job_no_mst=e.job_no and e.status_active=1 and e.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.booking_type in(1,4) $po_id_cond group by b.booking_no,e.buyer_name, e.job_no_prefix_num,e.insert_date");

	foreach ($sql_job as $job_row) {
		$job_arr[$job_row[csf('booking_no')]]["job_no_mst"] 		= $job_row[csf('job_no_prefix_num')];
		$job_arr[$job_row[csf('booking_no')]]['year'] 				= date("Y", strtotime($job_row[csf("insert_date")]));
		$job_arr[$job_row[csf('booking_no')]]["buyer_name"] 		= $job_row[csf('buyer_name')];
	}

	$inc = 0;
	if (count($data_array) > 0) 
	{
		foreach ($data_array as $row)
		{
			if($row[csf("entry_form")]==66)
			{
				$barcode_process=$row[csf("reprocess")];
				$roll_qty=$row[csf("qc_pass_qnty")];
				$recv_number=$row[csf("recv_number")];
			}
			else
			{
				$recv_number=$issue_barcode_data[$row[csf("barcode_no")]]['issue_number'];
				$barcode_process=($row[csf("reprocess")]*1)+1;
			}

			if($row[csf("entry_form")]==68) $roll_id=$row[csf("previous_roll_id")];
			else 							$roll_id=$row[csf("roll_id")];

			if($self_scnned[$row[csf("barcode_no")]]['reprocess']==$barcode_process)
			{
				$roll_qcPass_qty=$self_scnned[$row[csf("barcode_no")]]['qc_pass_qnty'];
				$roll_qty=$self_scnned[$row[csf("barcode_no")]]['qnty'];
				$barcode_reProcess=$self_scnned[$row[csf("barcode_no")]]['prev_reprocess'];

				$po_breakdown_id=$self_scnned[$row[csf("barcode_no")]]['po_breakdown_id'];
				$details_id=$self_scnned[$row[csf("barcode_no")]]['dtls_id'];

				$sales_booking_no 	= $sales_arr[$po_breakdown_id]["sales_booking_no"];
				$within_group 		= $sales_arr[$po_breakdown_id]["within_group"];
				if($row[csf("is_sales")] == 1) {
					$order 				= $sales_arr[$po_breakdown_id]["sales_order_no"];
					$year 	 			= $sales_arr[$po_breakdown_id]["year"];
					if($within_group==1){
						$job_no 			= $sales_arr[$po_breakdown_id]["po_job_no"];
						$buyer_name 		= $buyer_name_array[$sales_arr[$po_breakdown_id]["po_buyer"]];
					}else{
						$order 				= $sales_arr[$po_breakdown_id]["sales_order_no"];
						$job_no 			= "";
						$buyer_name 		= $buyer_name_array[$job_arr[$sales_booking_no]["buyer_name"]];
					}
				}else{
					if($row[csf("booking_without_order")] !=1){
						$order 				= $po_details_array[$po_breakdown_id]['po_number'];
						$buyer_name 		= $po_details_array[$po_breakdown_id]['buyer_name'];
						$job_no 			= $po_details_array[$po_breakdown_id]['job_no'];
						$year 				= $po_details_array[$po_breakdown_id]['year'];
					}else{
						$order 				= "";
						$buyer_name 		= "";
						$job_no 			= "";
						$year 				= "";
					}

				}

				if ($data[3] == 1 && $inc == 0) {
					echo "$('#cbo_company_id').val('" . $row[csf("company_id")] . "');\n";
					echo "$('#cbo_knitting_source').val('" . $row[csf("knitting_source")] . "');\n";
					if ($row[csf("knitting_source")] == 1) {
						echo "$('#txt_knit_company').val('" . $company_name_array[$row[csf("knitting_company")]] . "');\n";
						$knit_location_name = return_field_value("location_name", "lib_location", " id=" . $row[csf('knitting_location_id')] . "");
						echo "$('#txt_knitting_location').val('" . $knit_location_name . "');\n";
					} else if ($row[csf("knitting_source")] == 3) {
						echo "$('#txt_knit_company').val('" . $supplier_arr[$row[csf("knitting_company")]] . "');\n";
					}
					echo "$('#knit_company_id').val('" . $row[csf("knitting_company")] . "');\n";
					echo "$('#knit_location_id').val('" . $row[csf("knitting_location_id")] . "');\n";
				}

				echo "$('#sl_" . $data[2] . "').text('" . $data[2] . "');\n";
				echo "$('#barcode_" . $data[2] . "').text('" . $row[csf("barcode_no")] . "');\n";
				echo "$('#roll_" . $data[2] . "').text('" . $row[csf("roll_no")] . "');\n";
				echo "$('#prodQty_" . $data[2] . "').text('" . $roll_qty . "');\n";
				echo "$('#prodQnty_" . $data[2] . "').val('" . number_format($roll_qty, 2) . "');\n";

				echo "$('#rejectQty_" . $data[2] . "').text('" . number_format($row[csf("reject_qnty")], 2) . "');\n";
				echo "$('#rejectQnty_" . $data[2] . "').val('" . number_format($row[csf("reject_qnty")], 2) . "');\n";
				echo "$('#currentDelivery_" . $data[2] . "').val('" . $roll_qcPass_qty . "');\n";
				echo "$('#rollQty_" . $data[2] . "').val('" . $roll_qty . "');\n";
				$batch = return_field_value("batch_no", "pro_batch_create_mst", " id=" . $row[csf('batch_id')] . "");

				echo "$('#batch_" . $data[2] . "').text('" . $batch . "');\n";
				echo "$('#bodypart_" . $data[2] . "').text('" . $body_part[$row[csf("body_part_id")]] . "');\n";
				echo "$('#cons_" . $data[2] . "').text('" . $constructtion_arr[$row[csf("fabric_description_id")]] . "');\n";
				echo "$('#comps_" . $data[2] . "').text('" . $composition_arr[$row[csf("fabric_description_id")]] . "');\n";
				echo "$('#color_" . $data[2] . "').text('" . $color_name_array[$row[csf("color_id")]] . "');\n";
				echo "$('#gsm_" . $data[2] . "').text('" . $row[csf("gsm")] . "');\n";
				echo "$('#dia_" . $data[2] . "').text('" . $row[csf("width")] . "');\n";
				echo "$('#widthTipe_" . $data[2] . "').text('" . $fabric_typee[$row[csf("dia_width_type")]] . "');\n";
				echo "$('#widthTypeId_" . $data[2] . "').val('" . $row[csf("dia_width_type")] . "');\n";
				echo "$('#batchId_" . $data[2] . "').val('" . $row[csf("batch_id")] . "');\n";
				echo "$('#knitSource_" . $data[2] . "').text('" . $knitting_source[$row[csf("knitting_source")]] . "');\n";

				if ($row[csf("knitting_source")] == 1) {
					echo "$('#finishCompany_" . $data[2] . "').text('" . $company_name_array[$row[csf("knitting_company")]] . "');\n";
				} else if ($row[csf("knitting_source")] == 3) {
					echo "$('#finishCompany_" . $data[2] . "').text('" . $supplier_arr[$row[csf("knitting_company")]] . "');\n";
				}
				echo "$('#prodDate_" . $data[2] . "').text('" . $row[csf("receive_date")] . "');\n";
				echo "$('#year_" . $data[2] . "').text('" . $year . "');\n";
				echo "$('#job_" . $data[2] . "').text('" . $job_no . "');\n";
				echo "$('#buyer_" . $data[2] . "').text('" . $buyer_name . "');\n";
				echo "$('#order_" . $data[2] . "').text('" . $order . "');\n";
				echo "$('#prodId_" . $data[2] . "').text('" . $row[csf("prod_id")] . "');\n";
				echo "$('#systemId_" . $data[2] . "').text('" . $recv_number . "');\n";
				echo "$('#barcodeNo_" . $data[2] . "').val('" . $row[csf("barcode_no")] . "');\n";
				echo "$('#productionId_" . $data[2] . "').val('" . $row[csf("id")] . "');\n";
				echo "$('#productionDtlsId_" . $data[2] . "').val('" . $row[csf("dtls_id")] . "');\n";
				echo "$('#dtlsId_" . $data[2] . "').val('" . $details_id . "');\n";
				echo "$('#deterId_" . $data[2] . "').val('" . $row[csf("fabric_description_id")] . "');\n";
				echo "$('#productId_" . $data[2] . "').val('" . $row[csf("prod_id")] . "');\n";
				echo "$('#orderId_" . $data[2] . "').val('" . $po_breakdown_id . "');\n";
				echo "$('#rollId_" . $data[2] . "').val('" . $roll_id . "');\n";
				echo "$('#reProcess_" . $data[2] . "').val('" . $barcode_process . "');\n";
				echo "$('#prereProcess_" . $data[2] . "').val('" . $barcode_reProcess . "');\n";
				echo "$('#IsSalesId_" . $data[2] . "').val('" . $row[csf("is_sales")] . "');\n";
				echo "$('#bookingNumber_" . $data[2] . "').val('" . $row[csf("booking_no")] . "');\n";
				echo "$('#bookingWithoutOrder_" . $data[2] . "').val('" . $row[csf("booking_without_order")] . "');\n";
				echo "$('#colorId_" . $data[2] . "').val('" . $row[csf("color_id")] . "');\n";
				echo "$('#bodyPartId_" . $data[2] . "').val('" . $row[csf("body_part_id")] . "');\n";
				echo "$('#finMstId_" . $data[2] . "').val('" . $row[csf("id")] . "');\n";
				echo "$('#decrease_" . $data[2] . "').removeAttr('onclick').attr('onclick','fn_deleteRow(" . $data[2] . ")');\n";
				echo "$('#currentDelivery_" . $data[2] . "').removeAttr('onKeyUp').attr('onKeyUp','check_qty(" . $data[1] . ")');\n";

				if ($scnned[$row[csf("barcode_no")]][$barcode_process] != '')
					echo "$('#currentDelivery_" . $data[2] . "').attr('disabled','disabled');\n";
				else if($barcode_process>0)
					echo "$('#currentDelivery_" . $data[2] . "').attr('readonly',false);\n";
				$inc++;
				$data[2]++;
			}
		}
	}
	else
	{
		echo "alert('Not a valid barcode or process incomplete. please check');";
		echo "$('#tr_" . $data[2] . "').remove();";
		die;
	}
	die;
}

if ($action == "populate_data_from_barcode") 
{
	$data = explode("__", $data);
	$receive_barcode_array = array();
	$nbarcode = "'" . implode("','", explode(",", $data[0])) . "'";
	/*echo "<pre>";
	print_r($data);
	die;*/
	$entry = 66;
	$buyer_name_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");

	$scanned_barcode_data = sql_select("select dtls_id,barcode_no,qnty,reprocess from pro_roll_details where entry_form in(67,68) and status_active=1 and is_deleted=0 and barcode_no in (" . $nbarcode . ")");
	foreach ($scanned_barcode_data as $row) {
		if($row[csf("entry_form")] == 68){
			$scnned[$row[csf("barcode_no")]][$row[csf("reprocess")]] = $row[csf("qnty")];
		}else{
			$self_scnned[$row[csf("barcode_no")]][$row[csf("reprocess")]] = $row[csf("barcode_no")];
		}
	}

	//only QC passed barcode will delivery if variable is set to mandatory QC
	$QC_barcode_sql = sql_select("select a.barcode_no, b.barcode_no as qc_barcode_no, c.company_id from pro_roll_details a left join pro_qc_result_mst b on b.entry_form=267 and a.barcode_no=b.barcode_no and b.status_active=1 and b.is_deleted=0 and b.roll_status=1, inv_receive_master c where a.entry_form =66 and a.mst_id = c.id and a.status_active=1 and a.barcode_no in (" . $nbarcode . ") ");
	foreach ($QC_barcode_sql as $row) 
	{
		$QC_barcode_arr[$row[csf('qc_barcode_no')]] = $row[csf('qc_barcode_no')];
		$company_id= $row[csf('company_id')];
	}

	$variable_settingAutoQC = return_field_value("qc_mandatory", "variable_settings_production", "company_name =$company_id and variable_list in(48) and item_category_id=2 and is_deleted=0 and status_active=1", "qc_mandatory");

	$issue_barcode=sql_select("select b.issue_number,a.dtls_id,a.barcode_no,a.qnty,max(a.reprocess) as reprocess,a.prev_reprocess,a.po_breakdown_id from pro_roll_details a,inv_issue_master b where b.issue_purpose=44 and a.mst_id=b.id and a.entry_form=71 and a.status_active=1 and a.is_deleted=0 and a.barcode_no in (" . $nbarcode . ")  and a.reprocess>0 group by a.dtls_id,a.barcode_no,a.qnty,a.prev_reprocess,a.po_breakdown_id,b.issue_number");

	foreach($issue_barcode as $ival)
	{
		$issue_barcode_arr[]=$ival[csf('barcode_no')];
		$issue_barcode_data[$ival[csf('barcode_no')]]['dtls_id']=$ival[csf('dtls_id')];
		$issue_barcode_data[$ival[csf('barcode_no')]]['po_breakdown_id']=$ival[csf('po_breakdown_id')];
		$issue_barcode_data[$ival[csf('barcode_no')]]['qnty']=$ival[csf('qnty')];
		$issue_barcode_data[$ival[csf('barcode_no')]]['issue_number']=$ival[csf('issue_number')];
		$issue_barcode_check[$ival[csf('barcode_no')]][$ival[csf('reprocess')]]=$ival[csf('barcode_no')];
	}

	$issuebarcode = "'" . implode("','", $issue_barcode_arr) . "'";
	if(count($issue_barcode_arr)>0)
	{
		$issued_barcode_cond=" and c.barcode_no not in (" . $issuebarcode . ")";
	}

	$sql_data="select a.id,a.entry_form, a.company_id, a.recv_number, a.receive_basis, a.receive_date, a.booking_no, a.knitting_source, a.knitting_company, a.knitting_location_id, b.id as dtls_id,b.prod_id,b.color_id ,b.batch_id, b.fabric_description_id,b.body_part_id,b.dia_width_type, b.gsm,b.production_qty, b.width, c.barcode_no, c.roll_id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.reject_qnty,c.qc_pass_qnty, c.dtls_id as dtlsid,c.reprocess,c.roll_id as previous_roll_id,c.is_sales,c.booking_without_order,c.booking_no from inv_receive_master a, pro_finish_fabric_rcv_dtls b,pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=$entry and c.entry_form=$entry and c.status_active=1 and c.is_deleted=0 and c.barcode_no in (" . $nbarcode . ") $issued_barcode_cond";

	if(count($issue_barcode_arr)>0)
	{
		$sql_data.=" union all
		SELECT a.id, a.entry_form, a.company_id , a.recv_number, a.receive_basis,a.receive_date, a.booking_no, a.knitting_source, a.knitting_company, a.knitting_location_id, b.id as dtls_id, b.prod_id,b.color_id,b.batch_id,b.fabric_description_id, b.body_part_id,b.dia_width_type,  b.gsm,c.qnty  as production_qty,b.width, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id,c.qnty,c.reject_qnty,c.qc_pass_qnty, c.dtls_id as dtlsid,max(c.reprocess) reprocess,c.roll_id as previous_roll_id,c.is_sales,c.booking_without_order,c.booking_no FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(37,7,68) and c.entry_form in(37,7,68) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in(".$issuebarcode.") group by a.id, a.entry_form, a.company_id , a.recv_number, a.receive_basis,a.receive_date, a.booking_no, a.knitting_source, a.knitting_company, a.knitting_location_id, b.id, b.prod_id,b.color_id,b.batch_id,b.fabric_description_id, b.body_part_id,b.dia_width_type,  b.gsm,c.qnty,b.width, c.barcode_no, c.id,c.roll_id, c.roll_no, c.po_breakdown_id,c.qnty,c.reject_qnty, c.qc_pass_qnty,c.dtls_id,c.is_sales, c.booking_without_order,c.booking_no";
	}
	
	$data_array = sql_select($sql_data);
	//echo "alert(".count($data_array).");\n";die;
	foreach ($data_array as $val)
	{
		$all_deter_arr[$val[csf("fabric_description_id")]] =$val[csf("fabric_description_id")];

		if($val[csf("is_sales")] == 1){
			$sales_id_arr[] = $val[csf("po_breakdown_id")];
		}else{

			$po_arr[$val[csf("po_breakdown_id")]] = $val[csf("po_breakdown_id")];
		}

		if($variable_settingAutoQC ==1 )
		{
			if( $QC_barcode_arr[$val[csf('barcode_no')]] =="")
			{
				echo "999!!Barcode is not QC passed.\nBarcode No : ".$val[csf('barcode_no')];
				die;
			}
		}
	}

	if(!empty($po_arr)){
		$data_array_po_ref = sql_select("SELECT a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst and b.id in (" . implode(",",$po_arr) . ")");
		$po_details_array = array();
		foreach ($data_array_po_ref as $row) {
			$po_details_array[$row[csf("po_id")]]['job_no'] = $row[csf("job_no_prefix_num")];
			$po_details_array[$row[csf("po_id")]]['buyer_name'] = $buyer_name_array[$row[csf("buyer_name")]];
			$po_details_array[$row[csf("po_id")]]['style_ref_no'] = $row[csf("style_ref_no")];
			$po_details_array[$row[csf("po_id")]]['year'] = date("Y", strtotime($row[csf("insert_date")]));
			$po_details_array[$row[csf("po_id")]]['po_number'] = $row[csf("po_number")];
		}
		unset($data_array_po_ref);
	}

	$sales_arr=$sales_booking_arr=array();
	if(!empty($sales_id_arr)){
		$sql_sales=sql_select("select id,job_no,within_group,sales_booking_no,booking_date,po_job_no,po_buyer from fabric_sales_order_mst where id in (" . implode(",",$sales_id_arr) . ") and status_active=1 and is_deleted=0");
		foreach ($sql_sales as $sales_row) {
			$sales_arr[$sales_row[csf('id')]]["sales_order_no"] 		= $sales_row[csf('job_no')];
			$sales_arr[$sales_row[csf('id')]]["within_group"] 			= $sales_row[csf('within_group')];
			$sales_arr[$sales_row[csf('id')]]["sales_booking_no"] 		= $sales_row[csf('sales_booking_no')];
			$sales_arr[$sales_row[csf('id')]]["po_job_no"] 				= $sales_row[csf('po_job_no')];
			$sales_arr[$sales_row[csf('id')]]["po_buyer"] 				= $sales_row[csf('po_buyer')];
			$sales_arr[$sales_row[csf('id')]]['year'] 					= date("Y", strtotime($sales_row[csf("booking_date")]));
			$sales_booking_arr[] = "'".$sales_row[csf('sales_booking_no')]."'";
		}
		unset($sql_sales);
	}

	$company_name_array = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$color_name_array = return_library_array("select id, color_name from lib_color", "id", "color_name");
	$location_array = return_library_array("select id, location_name from lib_location", "id", "location_name");

	$all_deter_cond="";
	$all_deter_arr = array_filter($all_deter_arr);
	if(count($all_deter_arr)>0)
	{
		$deter_cond="";
		$all_deter_nos=implode(",",$all_deter_arr);
		if($db_type==2 && count($all_deter_arr)>999)
		{
			$all_deter_chunk=array_chunk($all_deter_arr,999) ;
			foreach($all_deter_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$deter_cond.=" a.id in($chunk_arr_value) or ";
			}

			$all_deter_cond.=" and (".chop($deter_cond,'or ').")";
		}
		else
		{
			$all_deter_cond=" and a.id in($all_deter_nos)";
		}
	}

	$composition_arr = array();
	$constructtion_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id $all_deter_cond";
	$data_array_deter = sql_select($sql_deter);
	foreach ($data_array_deter as $row) {
		$constructtion_arr[$row[csf('id')]] = $row[csf('construction')];
		$composition_arr[$row[csf('id')]] .= $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "% ";
	}
	unset($data_array_deter);

	$all_po_cond="";
	$all_po_arr = array_filter($po_arr);
	if(count($all_po_arr)>0)
	{
		$poCond="";
		$all_po_nos=implode(",",$all_po_arr);
		if($db_type==2 && count($all_po_arr)>999)
		{
			$all_po_chunk=array_chunk($all_po_arr,999) ;
			foreach($all_po_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$poCond.=" c.id in($chunk_arr_value) or ";
			}
			$all_po_cond.=" and (".chop($poCond,'or ').")";
		}
		else
		{
			$all_po_cond="  c.id in($all_po_nos)";
		}

		$booking_cond = (!empty($sales_booking_arr))?" and a.booking_no in(".implode(",",$sales_booking_arr).")":"";
		$sql_job="select a.buyer_id, b.job_no as job_no_mst,b.booking_no,b.po_break_down_id,c.po_number, c.shipment_date
		from wo_po_break_down c,wo_booking_dtls b,wo_booking_mst a
		where $all_po_cond and c.id=b.po_break_down_id and b.booking_no=a.booking_no $booking_cond and a.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
		group by b.booking_no,a.buyer_id,b.po_break_down_id, c.po_number,c.shipment_date";

		$job_sql_result = sql_select($sql_job);
		foreach ($job_sql_result as $job_row) {
			$job_arr[$job_row[csf('booking_no')]]["job_no_mst"] 		= $job_row[csf('job_no_mst')];
			$job_arr[$job_row[csf('booking_no')]]['year'] 				= date("Y", strtotime($job_row[csf("shipment_date")]));
			$job_arr[$job_row[csf('booking_no')]]["buyer_name"] 		= $job_row[csf('buyer_name')];
			$job_arr[$job_row[csf('booking_no')]]["po_number"] 			= $job_row[csf('po_number')];
		}
		unset($sql_job);
	}

	$batch_barcode_data = sql_select("SELECT a.id, a.batch_no, b.barcode_no, b.batch_qnty as qnty
	FROM pro_batch_create_mst a, pro_batch_create_dtls b 
	WHERE a.id=b.mst_id and a.entry_form in(0,66,65) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.barcode_no>0 and barcode_no in (" . $nbarcode . ")");
	foreach ($batch_barcode_data as $row) 
	{
		$grey_qty_arr[$row[csf("barcode_no")]]['batch_no'] = $row[csf("batch_no")];
		$grey_qty_arr[$row[csf("barcode_no")]]['grey_qty'] = $row[csf("qnty")];
	}

	$inc = 0; $data_string = "";
	if (count($data_array) > 0) 
	{
		foreach ($data_array as $row) 
		{
			if($row[csf("entry_form")]==66)
			{
				$barcode_process=$row[csf("reprocess")];
				$roll_qty=$row[csf("qc_pass_qnty")];
				$isSales=$row[csf("is_sales")];
				$booking_without_order=$row[csf("booking_without_order")];
				$booking_no=$row[csf("booking_no")];
				$po_breakdown_id=$row[csf("po_breakdown_id")];
				$recv_number=$row[csf("recv_number")];

				$sales_booking_no 	= $sales_arr[$po_breakdown_id]["sales_booking_no"];
				$within_group 		= $sales_arr[$po_breakdown_id]["within_group"];

				if($row[csf("is_sales")] == 1) {
					$order 				= $sales_arr[$po_breakdown_id]["sales_order_no"];
					$year 	 			= $sales_arr[$po_breakdown_id]["year"];
					if($within_group==1){
						$job_no 			= $sales_arr[$po_breakdown_id]["po_job_no"];
						$buyer_name 		= $buyer_name_array[$sales_arr[$po_breakdown_id]["po_buyer"]];
					}else{
						$order 				= $sales_arr[$po_breakdown_id]["sales_order_no"];
						$job_no 			= "";
						$buyer_name 		= $buyer_name_array[$job_arr[$sales_booking_no]["buyer_name"]];
					}
				}
				else if($booking_without_order==1)
				{
					$order="";
					$buyer_name="";
					$job_no="";
					$year="";
				}
				else{
					$order 				= $po_details_array[$po_breakdown_id]['po_number'];
					$buyer_name 		= $po_details_array[$po_breakdown_id]['buyer_name'];
					$job_no 			= $po_details_array[$po_breakdown_id]['job_no'];
					$year 				= $po_details_array[$po_breakdown_id]['year'];
				}

				if ($self_scnned[$row[csf("barcode_no")]][$barcode_process]=="")
				{
					//echo "$('#cbo_company_id').val('" . $row[csf("company_id")] . "');\n";
					//echo "$('#cbo_knitting_source').val('" . $row[csf("knitting_source")] . "');\n";
					if ($row[csf("knitting_source")] == 1) {
						//echo "$('#txt_knit_company').val('" . $company_name_array[$row[csf("knitting_company")]] . "');\n";
						$knit_location_name = $location_array[$row[csf('knitting_location_id')]];
						// return_field_value("location_name", "lib_location", " id=" . $row[csf('knitting_location_id')] . "");
						//echo "$('#txt_knitting_location').val('" . $knit_location_name . "');\n";

						$txt_knit_company =$company_name_array[$row[csf("knitting_company")]];

					} else if ($row[csf("knitting_source")] == 3) {
						//echo "$('#txt_knit_company').val('" . $supplier_arr[$row[csf("knitting_company")]] . "');\n";

						$txt_knit_company =$supplier_arr[$row[csf("knitting_company")]];
					}
					
					$grey_qty=$grey_qty_arr[$row[csf("barcode_no")]]['grey_qty'];
					$batch=$grey_qty_arr[$row[csf("barcode_no")]]['batch_no'];
					// $batch = return_field_value("batch_no", "pro_batch_create_mst", " id=" . $row[csf('batch_id')] . "");
					
					$data_string .= $row[csf("company_id")]."**".$row[csf("knitting_source")]."**".$row[csf("knitting_company")]."**".$txt_knit_company."**".$row[csf('knitting_location_id')]."**".$knit_location_name."**".$row[csf("barcode_no")]."**".$row[csf("roll_no")]."**".$row[csf("production_qty")]."**".number_format($row[csf("reject_qnty")], 2,".","")."**".$roll_qty."**".$isSales."**".$booking_without_order."**".$booking_no."**".$batch."**".$body_part[$row[csf("body_part_id")]]."**".$row[csf("body_part_id")]."**".$constructtion_arr[$row[csf("fabric_description_id")]]."**".$composition_arr[$row[csf("fabric_description_id")]]."**".$row[csf("fabric_description_id")]."**".$color_name_array[$row[csf("color_id")]]."**".$row[csf("color_id")]."**".$row[csf("gsm")]."**".$row[csf("width")]."**".$row[csf("dia_width_type")]."**".$fabric_typee[$row[csf("dia_width_type")]]."**".$row[csf("batch_id")]."**".$knitting_source[$row[csf("knitting_source")]]."**".$row[csf("receive_date")]."**".$year."**".$job_no."**".$buyer_name."**".$order."**".$row[csf("prod_id")]."**".$recv_number."**".$row[csf("id")]."**".$row[csf("dtls_id")]."**".$po_breakdown_id."**".$row[csf("roll_id")]."**".$barcode_process."**".$grey_qty."__";
				}
				else
				{
					echo "999!!Barcode alredy Scanned";
					die;
				}
			}
			else
			{
				$isSales=$row[csf("is_sales")];
				$booking_without_order=$row[csf("booking_without_order")];
				$booking_no=$row[csf("booking_no")];
				$barcode_process=($row[csf("reprocess")]*1)+1;
				$roll_qty=$issue_barcode_data[$row[csf("barcode_no")]]['qnty'];
				$recv_number=$issue_barcode_data[$row[csf("barcode_no")]]['issue_number'];
				$po_breakdown_id=$issue_barcode_data[$row[csf("barcode_no")]]['po_breakdown_id'];

				$sales_booking_no 	= $sales_arr[$po_breakdown_id]["sales_booking_no"];
				$within_group 		= $sales_arr[$po_breakdown_id]["within_group"];
				if($row[csf("is_sales")] == 1){
					if($within_group==1){
						$order 				= $sales_arr[$po_breakdown_id]["sales_order_no"];
						$job_no 			= $job_arr[$sales_booking_no]["job_no_mst"];
						$year 	 			= $job_arr[$sales_booking_no]["year"];
						$buyer_name 		= $job_arr[$sales_booking_no]["buyer_name"];
					}else{
						$order 				= $sales_arr[$po_breakdown_id]["sales_order_no"];
						$job_no 			= "";
						$year 	 			= $job_arr[$sales_booking_no]["year"];
						$buyer_name 		= $job_arr[$sales_booking_no]["buyer_name"];
					}
				}
				else if($booking_without_order==1)
				{
					$order="";
					$buyer_name="";
					$job_no="";
					$year="";
				}
				else
				{
					$order 				= $po_details_array[$po_breakdown_id]['po_number'];
					$buyer_name 		= $po_details_array[$po_breakdown_id]['buyer_name'];
					$job_no 			= $po_details_array[$po_breakdown_id]['job_no'];
					$year 				= $po_details_array[$po_breakdown_id]['year'];
				}

				if ($self_scnned[$row[csf("barcode_no")]][$barcode_process]=="" && $issue_barcode_check[$row[csf("barcode_no")]][$barcode_process]!="")
				{
					if($row[csf("entry_form")]==68) 
					{
						$roll_id=$row[csf("previous_roll_id")];
					}
					else 
					{
						$roll_id=$row[csf("roll_id")];
					}
					

					if ($row[csf("knitting_source")] == 1) {
						//echo "$('#txt_knit_company').val('" . $company_name_array[$row[csf("knitting_company")]] . "');\n";
						$knit_location_name =  $location_array[$row[csf('knitting_location_id')]]; 
						//return_field_value("location_name", "lib_location", " id=" . $row[csf('knitting_location_id')] . "");
						//echo "$('#txt_knitting_location').val('" . $knit_location_name . "');\n";
						$txt_knit_company = $supplier_arr[$row[csf("knitting_company")]];
					} else if ($row[csf("knitting_source")] == 3) {
						//echo "$('#txt_knit_company').val('" . $supplier_arr[$row[csf("knitting_company")]] . "');\n";
						$txt_knit_company = $supplier_arr[$row[csf("knitting_company")]];
					}
						
					$grey_qty=$grey_qty_arr[$row[csf("barcode_no")]]['grey_qty'];
					$batch=$grey_qty_arr[$row[csf("barcode_no")]]['batch_no'];
					// $batch = return_field_value("batch_no", "pro_batch_create_mst", " id=" . $row[csf('batch_id')] . "");

					

					$data_string .= $row[csf("company_id")]."**".$row[csf("knitting_source")]."**".$row[csf("knitting_company")]."**".$txt_knit_company."**".$row[csf('knitting_location_id')]."**".$knit_location_name."**".$row[csf("barcode_no")]."**".$row[csf("roll_no")]."**".$row[csf("production_qty")]."**".number_format($row[csf("reject_qnty")], 2,".","")."**".$roll_qty."**".$isSales."**".$booking_without_order."**".$booking_no."**".$batch."**".$body_part[$row[csf("body_part_id")]]."**".$row[csf("body_part_id")]."**".$constructtion_arr[$row[csf("fabric_description_id")]]."**".$composition_arr[$row[csf("fabric_description_id")]]."**".$row[csf("fabric_description_id")]."**".$color_name_array[$row[csf("color_id")]]."**".$row[csf("color_id")]."**".$row[csf("gsm")]."**".$row[csf("width")]."**".$row[csf("dia_width_type")]."**".$fabric_typee[$row[csf("dia_width_type")]]."**".$row[csf("batch_id")]."**".$knitting_source[$row[csf("knitting_source")]]."**".$row[csf("receive_date")]."**".$year."**".$job_no."**".$buyer_name."**".$order."**".$row[csf("prod_id")]."**".$recv_number."**".$row[csf("id")]."**".$row[csf("dtls_id")]."**".$po_breakdown_id."**".$row[csf("roll_id")]."**".$barcode_process."**".$grey_qty."__";
				}
				else
				{
					echo "999!!Barcode alredy Scanned";
					die;
				}
			}
		}
	} 
	else 
	{
		echo "999!! Not a valid barcode or process incomplete. please check";
		die;
	}

	echo chop($data_string,"__");
	die;
}

if ($action == "populate_data_update_barcode") 
{
	$data = explode("__", $data);
	$receive_barcode_array = array();
	$mst_id=$data[0];
	//$nbarcode = "'" . implode("','", explode(",", $data[1])) . "'";
	//$update_id=$data[1];
	// echo $data[1].'='.$data[2];die;


	$nbarcode="";

	$scanned_barcode_data = sql_select("select barcode_no,reprocess,dtls_id,qc_pass_qnty,qnty,po_breakdown_id ,prev_reprocess from pro_roll_details where mst_id=$mst_id and entry_form=67 and status_active=1 and is_deleted=0");
	foreach ($scanned_barcode_data as $row) {
		$scnned[$row[csf("barcode_no")]][$row[csf("reprocess")]] = $row[csf("qnty")];
		$nbarcode .= $row[csf("barcode_no")].",";
	}

	$nbarcode = chop($nbarcode,",");
		//die;

	$selfscanned_barcode_data = sql_select("select barcode_no,reprocess,dtls_id,qc_pass_qnty,qnty,po_breakdown_id ,prev_reprocess from pro_roll_details where mst_id=$mst_id and entry_form=67 and status_active=1 and is_deleted=0 and barcode_no in (" . $nbarcode . ")");
	foreach ($selfscanned_barcode_data as $row) 
	{
		if($row[csf("reprocess")]==0)
		{
			$production_barcode_arr[]=$row[csf('barcode_no')];
		}
		else
		{
			$issue_barcode_arr[]=$row[csf('barcode_no')];
		}

		$self_scnned[$row[csf("barcode_no")]]['prev_reprocess'] = $row[csf("prev_reprocess")];
		$self_scnned[$row[csf("barcode_no")]]['reprocess'] = $row[csf("reprocess")];
		$self_scnned[$row[csf("barcode_no")]]['po_breakdown_id'] = $row[csf("po_breakdown_id")];
		$self_scnned[$row[csf("barcode_no")]]['dtls_id'] = $row[csf("dtls_id")];
		$self_scnned[$row[csf("barcode_no")]]['qc_pass_qnty'] = $row[csf("qc_pass_qnty")];
		$self_scnned[$row[csf("barcode_no")]]['qnty'] = $row[csf("qnty")];
	}

	$received_barcode_data = sql_select("select barcode_no,reprocess,dtls_id,qc_pass_qnty,qnty,po_breakdown_id ,prev_reprocess from pro_roll_details where  entry_form=68 and status_active=1 and is_deleted=0 and barcode_no in (" . $nbarcode . ")");
	foreach ($received_barcode_data as $row) 
	{
		$received_scnned[$row[csf("barcode_no")]][$row[csf("reprocess")]] = $row[csf("barcode_no")];
	}

	$batch_barcode_data = sql_select("SELECT a.id, a.batch_no, b.barcode_no, b.batch_qnty as qnty
	FROM pro_batch_create_mst a, pro_batch_create_dtls b 
	WHERE a.id=b.mst_id and a.entry_form in(0,66,65) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.barcode_no>0 and barcode_no in (" . $nbarcode . ")");
	foreach ($batch_barcode_data as $row) 
	{
		$grey_qty_arr[$row[csf("barcode_no")]]['batch_no'] = $row[csf("batch_no")];
		$grey_qty_arr[$row[csf("barcode_no")]]['grey_qty'] = $row[csf("qnty")];
	}

	$issuebarcode = "'" . implode("','", $issue_barcode_arr) . "'";
	$productionbarcode = "'" . implode("','", $production_barcode_arr) . "'";

	$buyer_name_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");

	$data_array = sql_select("SELECT a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id FROM wo_po_details_master a, wo_po_break_down b , pro_roll_details c WHERE a.job_no=b.job_no_mst and b.id=c.po_breakdown_id and c.barcode_no in (" . $nbarcode . ") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ");
	$po_details_array = array();
	foreach ($data_array as $row) {
		$po_details_array[$row[csf("po_id")]]['job_no'] = $row[csf("job_no_prefix_num")];
		$po_details_array[$row[csf("po_id")]]['buyer_name'] = $buyer_name_array[$row[csf("buyer_name")]];
		$po_details_array[$row[csf("po_id")]]['style_ref_no'] = $row[csf("style_ref_no")];
		$po_details_array[$row[csf("po_id")]]['year'] = date("Y", strtotime($row[csf("insert_date")]));
		$po_details_array[$row[csf("po_id")]]['po_number'] = $row[csf("po_number")];
	}

	$sales_arr=array();
	$sql_sales=sql_select("select id,job_no,within_group,sales_booking_no,booking_date,po_job_no,po_buyer from fabric_sales_order_mst where status_active=1 and is_deleted=0");
	foreach ($sql_sales as $sales_row) {
		$sales_arr[$sales_row[csf('id')]]["sales_order_no"] 	= $sales_row[csf('job_no')];
		$sales_arr[$sales_row[csf('id')]]["within_group"] 		= $sales_row[csf('within_group')];
		$sales_arr[$sales_row[csf('id')]]["sales_booking_no"] 	= $sales_row[csf('sales_booking_no')];
		$sales_arr[$sales_row[csf('id')]]["po_job_no"] 			= $sales_row[csf('po_job_no')];
		$sales_arr[$sales_row[csf('id')]]["booking_date"] 		= date("Y", strtotime($sales_row[csf("booking_date")]));
		$sales_arr[$sales_row[csf('id')]]["po_buyer"] 			= $sales_row[csf('po_buyer')];
	}

	$composition_arr = array();
	$constructtion_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		$constructtion_arr[$row[csf('id')]] = $row[csf('construction')];
		$composition_arr[$row[csf('id')]] .= $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "% ";
	}

	$company_name_array = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$color_name_array = return_library_array("select id, color_name from lib_color", "id", "color_name");
	$entry = 66;

	$issue_barcode=sql_select("select b.issue_number,a.dtls_id,a.barcode_no,a.qnty,max(a.reprocess) as reprocess,a.prev_reprocess,a.po_breakdown_id from pro_roll_details a,inv_issue_master b where a.mst_id=b.id and a.entry_form=71 and a.status_active=1 and a.is_deleted=0 and a.barcode_no in (" . $issuebarcode . ")  and a.reprocess>0 group by a.dtls_id,a.barcode_no,a.qnty,a.prev_reprocess,a.po_breakdown_id,b.issue_number");

	foreach($issue_barcode as $ival)
	{
		$issue_barcode_data[$ival[csf('barcode_no')]]['issue_number']=$ival[csf('issue_number')];
	}


	if(count($production_barcode_arr)>0)
	{
		$sql_data="SELECT a.id,a.entry_form, a.company_id, a.recv_number, a.receive_basis, a.receive_date, a.booking_no, a.knitting_source, a.knitting_company, a.knitting_location_id, b.id as dtls_id,b.prod_id,b.color_id ,b.batch_id, b.fabric_description_id,b.body_part_id,b.dia_width_type, b.gsm,b.production_qty, b.width, c.barcode_no, c.roll_id as roll_id, c.roll_no, c.po_breakdown_id,c.is_sales, c.qnty,c.reject_qnty,c.qc_pass_qnty, c.dtls_id as dtlsid,c.reprocess,c.roll_id as previous_roll_id,c.is_sales,c.booking_without_order,c.booking_no
		FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b,pro_roll_details c
		WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=$entry and c.entry_form=$entry and c.status_active=1 and c.is_deleted=0 and c.barcode_no in (" . $productionbarcode . ")";

	}

	if(count($issue_barcode_arr)>0)
	{
		if(count($production_barcode_arr)>0)
		{
			$sql_data.=" union all ";
		}

		$sql_data.="SELECT a.id, a.entry_form, a.company_id , a.recv_number, a.receive_basis,a.receive_date, a.booking_no, a.knitting_source, a.knitting_company, a.knitting_location_id, b.id as dtls_id, b.prod_id,b.color_id,b.batch_id,b.fabric_description_id, b.body_part_id,b.dia_width_type,  b.gsm,c.qnty  as production_qty,b.width, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.is_sales, c.qnty,c.reject_qnty,c.qc_pass_qnty, c.dtls_id as dtlsid,max(c.reprocess) reprocess,c.roll_id as previous_roll_id,c.is_sales,c.booking_without_order,c.booking_no FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(37,7,68) and c.entry_form in(37,7,68) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in(".$issuebarcode.")
		group by a.id, a.entry_form, a.company_id , a.recv_number, a.receive_basis,a.receive_date, a.booking_no, a.knitting_source, a.knitting_company,a.knitting_location_id, b.id, b.prod_id,b.color_id,b.batch_id,b.fabric_description_id, b.body_part_id,b.dia_width_type,  b.gsm,c.qnty,b.width, c.barcode_no, c.id,c.roll_id,c.roll_no, c.po_breakdown_id,c.is_sales,c.qnty,c.reject_qnty,c.qc_pass_qnty, c.dtls_id,c.booking_without_order,c.booking_no";
	}
	//echo $sql_data;die;
	$data_array = sql_select($sql_data);
	$poIDs="";
	foreach ($data_array as $row)
	{
		$poIDs.=$row[csf('po_breakdown_id')].',';
	}

	$poIDs_all=rtrim($poIDs,",");
	$poIDs_alls=explode(",",$poIDs_all);
	$poIDs_alls=array_chunk($poIDs_alls,999);
	$po_id_cond=" and";
	foreach($poIDs_alls as $dtls_id)
	{
		if($po_id_cond==" and")  $po_id_cond.="(c.id in(".implode(',',$dtls_id).")"; else $po_id_cond.=" or c.id in(".implode(',',$dtls_id).")";
	}
	$po_id_cond.=")";

	$job_arr=array();
	$sql_job=sql_select("select b.booking_no,e.buyer_name, e.job_no_prefix_num,e.insert_date from wo_booking_dtls b,wo_po_break_down c, wo_po_details_master e where b.po_break_down_id=c.id and c.job_no_mst=e.job_no and e.status_active=1 and e.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.booking_type in(1,4) $po_id_cond group by b.booking_no,e.buyer_name, e.job_no_prefix_num,e.insert_date");

	foreach ($sql_job as $job_row) {
		$job_arr[$job_row[csf('booking_no')]]["job_no_mst"] 		= $job_row[csf('job_no_prefix_num')];
		$job_arr[$job_row[csf('booking_no')]]['year'] 				= date("Y", strtotime($job_row[csf("insert_date")]));
		$job_arr[$job_row[csf('booking_no')]]["buyer_name"] 		= $job_row[csf('buyer_name')];
	}

	$i = count($data_array);
	if (count($data_array) > 0) 
	{
		foreach ($data_array as $row)
		{
			if($row[csf("entry_form")]==66)
			{
				$barcode_process=$row[csf("reprocess")];
				$roll_qty=$row[csf("qc_pass_qnty")];
				$recv_number=$row[csf("recv_number")];
			}
			else
			{
				$recv_number=$issue_barcode_data[$row[csf("barcode_no")]]['issue_number'];
				$barcode_process=($row[csf("reprocess")]*1)+1;
			}

			if($row[csf("entry_form")]==68) $roll_id=$row[csf("previous_roll_id")];
			else 							$roll_id=$row[csf("roll_id")];

			if($self_scnned[$row[csf("barcode_no")]]['reprocess']==$barcode_process)
			{
				$roll_qcPass_qty=$self_scnned[$row[csf("barcode_no")]]['qc_pass_qnty'];
				$roll_qty=$self_scnned[$row[csf("barcode_no")]]['qnty'];
				$barcode_reProcess=$self_scnned[$row[csf("barcode_no")]]['prev_reprocess'];

				$po_breakdown_id=$self_scnned[$row[csf("barcode_no")]]['po_breakdown_id'];
				$details_id=$self_scnned[$row[csf("barcode_no")]]['dtls_id'];

				$sales_booking_no 	= $sales_arr[$po_breakdown_id]["sales_booking_no"];
				$within_group 		= $sales_arr[$po_breakdown_id]["within_group"];
				if($row[csf("is_sales")] == 1) {
					$order 				= $sales_arr[$po_breakdown_id]["sales_order_no"];
					$year 	 			= $sales_arr[$po_breakdown_id]["year"];
					if($within_group==1){
						$job_no 			= $sales_arr[$po_breakdown_id]["po_job_no"];
						$buyer_name 		= $buyer_name_array[$sales_arr[$po_breakdown_id]["po_buyer"]];
					}else{
						$order 				= $sales_arr[$po_breakdown_id]["sales_order_no"];
						$job_no 			= "";
						$buyer_name 		= $buyer_name_array[$job_arr[$sales_booking_no]["buyer_name"]];
					}
				}else{
					if($row[csf("booking_without_order")] !=1){
						$order 				= $po_details_array[$po_breakdown_id]['po_number'];
						$buyer_name 		= $po_details_array[$po_breakdown_id]['buyer_name'];
						$job_no 			= $po_details_array[$po_breakdown_id]['job_no'];
						$year 				= $po_details_array[$po_breakdown_id]['year'];
					}else{
						$order 				= "";
						$buyer_name 		= "";
						$job_no 			= "";
						$year 				= "";
					}
				}

				$grey_qty=$grey_qty_arr[$row[csf("barcode_no")]]['grey_qty'];
				$batch=$grey_qty_arr[$row[csf("barcode_no")]]['batch_no'];

				//$batch = return_field_value("batch_no", "pro_batch_create_mst", " id=" . $row[csf('batch_id')] . "");

				$readonly = "readonly";
				$disabled = "";

				if ($scnned[$row[csf("barcode_no")]][$barcode_process] != '')
				{
					$disabled = "disabled=disabled";
				}
				else if($barcode_process>0) 
				{
					$readonly = "";
				}

				$delete_disabled ="";
				if($received_scnned[$row[csf("barcode_no")]][$barcode_process] )
				{
					$delete_disabled = "disabled=disabled";
				}

				?>
				<tr id="tr_<? echo $i; ?>" align="center" valign="middle">
                    <td width="30" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
                    <td width="80" id="barcode_<? echo $i; ?>"><? echo $row[csf("barcode_no")];?></td>
                    <td width="40" id="roll_<? echo $i; ?>"><? echo $row[csf("roll_no")];?></td>
                    <td width="70" id="greyQty_<? echo $i; ?>" align="right"><? echo $grey_qty;?></td>
                    <td width="70" id="prodQty_<? echo $i; ?>" align="right"><? echo $roll_qty;?></td>
                    <td id="rejectQty_<? echo $i; ?>" width="70" align="center"><? echo number_format($row[csf("reject_qnty")], 2);?></td>
                    <td id="delevQt_<? echo $i; ?>" width="70" align="center">
                    	<input type="text" name="currentDelivery[]" id="currentDelivery_<? echo $i; ?>" value="<? echo  $roll_qcPass_qty;?>" style="width:55px" class="text_boxes_numeric" onKeyUp="check_qty(<? echo $i; ?>)" <? echo $readonly;?> <? echo $disabled;?>/>
                    </td>

                    <td width="80" id="batch_<? echo $i; ?>" style="word-break:break-all;"><? echo $batch;?></td>
                    <td width="100" id="bodypart_<? echo $i; ?>"><? echo $body_part[$row[csf("body_part_id")]];?></td>
                    <td width="80" id="cons_<? echo $i; ?>" style="word-break:break-all;" align="left"><? echo $constructtion_arr[$row[csf("fabric_description_id")]];?></td>
                    <td width="100" id="comps_<? echo $i; ?>" style="word-break:break-all;" align="left"><? echo $composition_arr[$row[csf("fabric_description_id")]];?></td>
                    <td width="70" id="color_<? echo $i; ?>" style="word-break:break-all;"><? echo $color_name_array[$row[csf("color_id")]];?></td>
                    <td width="40" id="gsm_<? echo $i; ?>"><? echo $row[csf("gsm")];?></td>
                    <td width="40" id="dia_<? echo $i; ?>" style="word-break:break-all;"><? echo $row[csf("width")];?></td>
                    <td width="60" id="widthTipe_<? echo $i; ?>" style="word-break:break-all;"><? echo $fabric_typee[$row[csf("dia_width_type")]];?></td>
                    <td width="75" id="knitSource_<? echo $i; ?>" style="word-break:break-all;"><? echo $knitting_source[$row[csf("knitting_source")]];?></td>
                    <?
                    if ($row[csf("knitting_source")] == 1) 
                    {
						?>
						<td width="85" id="finishCompany_<? echo $i; ?>" style="word-break:break-all;"><? echo $company_name_array[$row[csf("knitting_company")]];?></td>
						<?
					} else if ($row[csf("knitting_source")] == 3) {
						?>
						<td width="85" id="finishCompany_<? echo $i; ?>" style="word-break:break-all;"><? echo $supplier_arr[$row[csf("knitting_company")]];?></td>
						<?
					}
					?>

                    <td width="70" id="prodDate_<? echo $i; ?>" style="word-break:break-all;"><? echo $row[csf("receive_date")];?></td>
                    <td width="40" id="year_<? echo $i; ?>" align="center" style="word-break:break-all;"><? echo $year;?></td>
                    <td width="100" id="job_<? echo $i; ?>" style="word-break:break-all;"><? echo $job_no;?></td>
                    <td width="55" id="buyer_<? echo $i; ?>" style="word-break:break-all;"><? echo $buyer_name;?></td>
                    <td width="80" id="order_<? echo $i; ?>" style="word-break:break-all;" align="left"><? echo $order;?></td>
                    <td width="50" id="prodId_<? echo $i; ?>" style="word-break:break-all;"><? echo $row[csf("prod_id")];?></td>
                    <td style="word-break:break-all;" id="systemId_<? echo $i; ?>"><? echo $recv_number;?></td>
                    <td width="40" id="button_<? echo $i; ?>" align="center">
                        <input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" <? echo $delete_disabled;?> />
                        <input type="hidden" value="<? echo $row[csf("barcode_no")]; ?>" name="barcodeNo[]" id="barcodeNo_<? echo $i; ?>"/>
                        <input type="hidden" value="<? echo $row[csf("id")]; ?>" name="productionId[]" id="productionId_<? echo $i; ?>"/>
                        <input type="hidden" value="<? echo $row[csf("dtls_id")]; ?>" name="productionDtlsId[]" id="productionDtlsId_<? echo $i; ?>"/>
                        <input type="hidden" value="<? echo $row[csf("fabric_description_id")]; ?>" name="deterId[]" id="deterId_<? echo $i; ?>"/>
                        <input type="hidden" value="<? echo $row[csf("prod_id")]; ?>" name="productId[]" id="productId_<? echo $i; ?>"/>
                        <input type="hidden" value="<? echo $po_breakdown_id; ?>" name="orderId[]" id="orderId_<? echo $i; ?>"/>
                        <input type="hidden" value="<? echo $roll_id; ?>" name="rollId[]" id="rollId_<? echo $i; ?>"/>
                        <input type="hidden" value="<? echo $details_id; ?>" name="dtlsId[]" id="dtlsId_<? echo $i; ?>"/>
                        <input type="hidden" value="<? echo $row[csf("color_id")]; ?>" name="colorId[]" id="colorId_<? echo $i; ?>"/>
                        <input type="hidden" value="<? echo $row[csf("body_part_id")]; ?>" name="bodyPartId[]" id="bodyPartId_<? echo $i; ?>"/>
                        <input type="hidden" value="<? echo $row[csf("dia_width_type")]; ?>" name="widthTypeId[]" id="widthTypeId_<? echo $i; ?>"/>
                        <input type="hidden" value="<? echo $row[csf("batch_id")]; ?>" name="batchId[]" id="batchId_<? echo $i; ?>"/>
                        <input type="hidden" value="<? echo $row[csf("id")]; ?>" name="finMstId[]" id="finMstId_<? echo $i; ?>"/>
						<input type="hidden" value="<? echo number_format($grey_qty, 2); ?>" name="greyQnty[]" id="greyQnty_<? echo $i; ?>"/>
                        <input type="hidden" value="<? echo $roll_qty; ?>" name="rollQty[]" id="rollQty_<? echo $i; ?>"/>
                        <input type="hidden" value="<? echo number_format($roll_qty, 2); ?>" name="prodQnty[]" id="prodQnty_<? echo $i; ?>"/>
                        <input type="hidden" value="<? echo number_format($row[csf("reject_qnty")], 2);?>" name="rejectQnty[]" id="rejectQnty_<? echo $i; ?>" />
                        <input type="hidden" value="<? echo $barcode_process; ?>" name="reProcess[]" id="reProcess_<? echo $i; ?>"/>
                        <input type="hidden" value="<? echo $barcode_reProcess; ?>" name="prereProcess[]" id="prereProcess_<? echo $i; ?>"/>
                        <input type="hidden" value="<? echo $row[csf("is_sales")]; ?>" name="IsSalesId[]" id="IsSalesId_<? echo $i; ?>"/>
                        <input type="hidden" value="<? echo $row[csf("booking_without_order")];?>" name="bookingWithoutOrder[]" id="bookingWithoutOrder_<? echo $i; ?>"/>
                        <input type="hidden" value="<? echo $row[csf("booking_no")];?>" name="bookingNumber[]" id="bookingNumber_<? echo $i; ?>"/>
                    </td>
                </tr>

				<?
				
				$i--;
			}
		}
	}
	else
	{
		echo "alert('Not a valid barcode or process incomplete. please check');";
		echo "$('#tr_" . $data[2] . "').remove();";
		die;
	}
	die;
}

if($action == "load_print_buttons")
{
	$company_id = $data;

	$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$company_id."'  and module_id=7 and report_id=167 and is_deleted=0 and status_active=1");
	$print_report_format_arr=explode(",",$print_report_format);
	echo "$('#print1').hide();\n";
	echo "$('#print2').hide();\n";
	echo "$('#print_barcode').hide();\n";
	echo "$('#btn_fabric_details').hide();\n";
	echo "$('#printFso_v2').hide();\n";
	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			if($id==86){echo "$('#print1').show();\n";}
			if($id==84){echo "$('#print2').show();\n";}
			if($id==68){echo "$('#print_barcode').show();\n";}
			if($id==69){echo "$('#btn_fabric_details').show();\n";}
			if($id==764){echo "$('#printFso_v2').show();\n";}
		}
	}
	else
	{
		echo "$('#print1').show();\n";
		echo "$('#print2').show();\n";
		echo "$('#print_barcode').show();\n";
		echo "$('#btn_fabric_details').show();\n";
		echo "$('#printFso_v2').show();\n";
	}
}

if ($action == "save_update_delete") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	for($k=1;$k<=$tot_row;$k++)
	{
		$productId = "productId_".$k;
		$prod_ids.=$$productId.",";
		$barcodeNo = "barcodeNo_" . $k;
		$all_barcode_arr[$$barcodeNo] = $$barcodeNo;
	}

	$all_barcode_arr = array_filter($all_barcode_arr);
	$all_barcode_nos = implode(",", $all_barcode_arr);


	$prod_ids=implode(",",array_unique(explode(",",chop($prod_ids,','))));
	$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id in ($prod_ids) and transaction_type in (1,4,5)", "max_date");
	$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
	$issue_date = date("Y-m-d", strtotime(str_replace("'","",$txt_delivery_date)));
	if ($issue_date < $max_recv_date)
	{
		echo "20**Delivery Date Can not Be Less Than Last Receive Date Of These Lot";
		die;
	}


	$scanned_barcode_data = sql_select("select dtls_id,barcode_no,qnty,reprocess from pro_roll_details where entry_form in(67,68) and status_active=1 and is_deleted=0 and barcode_no in (" . $all_barcode_nos . ")");
	foreach ($scanned_barcode_data as $row) 
	{
		if($row[csf("entry_form")] == 68){
			$scnned[$row[csf("barcode_no")]][$row[csf("reprocess")]] = $row[csf("qnty")];
		}else{
			$self_scnned[$row[csf("barcode_no")]][$row[csf("reprocess")]] = $row[csf("barcode_no")];
		}
	}


	$variable_settingAutoQC = return_field_value("qc_mandatory", "variable_settings_production", "company_name =$cbo_company_id and variable_list in(48) and item_category_id=2 and is_deleted=0 and status_active=1", "qc_mandatory");


	$QC_barcode_sql = sql_select("select barcode_no from pro_qc_result_mst where entry_form=267 and roll_status=1 and status_active=1 and is_deleted=0 and barcode_no in ($all_barcode_nos)");
	foreach ($QC_barcode_sql as $row) 
	{
		$QC_barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
	}


	if ($operation == 0)  // Insert Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		if ($db_type == 0) $year_cond = "YEAR(insert_date)";
		else if ($db_type == 2) $year_cond = "to_char(insert_date,'YYYY')";
		else $year_cond = "";//defined Later

		$id = return_next_id_by_sequence("PRO_GREY_PROD_DELI_MST_PK_SEQ", "pro_grey_prod_delivery_mst", $con);
        //print_r($id); die;
		$new_mrr_number = explode("*", return_next_id_by_sequence("PRO_GREY_PROD_DELI_MST_PK_SEQ", "pro_grey_prod_delivery_mst",$con,1,$cbo_company_id,'FDSR',67,date("Y",time()),13 ));

		$field_array = "id,sys_number_prefix,sys_number_prefix_num,sys_number,delevery_date,company_id,knitting_source,knitting_company,location_id,attention,remarks,entry_form,inserted_by,insert_date";
		$data_array = "(" . $id . ",'" . $new_mrr_number[1] . "'," . $new_mrr_number[2] . ",'" . $new_mrr_number[0] . "'," . $txt_delivery_date . "," . $cbo_company_id . "," . $cbo_knitting_source . "," . $knit_company_id. "," . $knit_location_id. "," . $txt_attention. "," . $txt_remarks . ",67," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

		$field_array_dtls = "id, mst_id, entry_form, grey_sys_id, sys_dtls_id,grey_sys_number, product_id,color_id,job_no,gsm,dia, order_id,bodypart_id, determination_id,batch_id,width_type,roll_id, barcode_num, current_delivery, inserted_by, insert_date";

		//$dtls_id = return_next_id("id", "pro_grey_prod_delivery_dtls", 1);

		$field_array_roll = "id, barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, reject_qnty, qc_pass_qnty, roll_no, roll_id,prev_reprocess,reprocess, inserted_by, insert_date,is_sales,booking_without_order,booking_no";
		//$id_roll = return_next_id("id", "pro_roll_details", 1);

		$dupli_barcode_arr=array();

		$barcodeNos = '';
		for ($j = 1; $j <= $tot_row; $j++) {
			$productionId = "productionId_" . $j;
			$productionDtlsId = "productionDtlsId_" . $j;
			$productId = "productId_" . $j;
			$orderId = "orderId_" . $j;
			$deterId = "deterId_" . $j;
			$rollId = "rollId_" . $j;
			$barcodeNo = "barcodeNo_" . $j;
			$currentDelivery = "currentDelivery_" . $j;
			$rollQty = "rollQty_" . $j;
			$rollNo = "rollNo_" . $j;
			$colorId = "colorId_" . $j;
			$jobNo = "jobNo_" . $j;
			$gsm = "gsm_" . $j;
			$dia = "dia_" . $j;
			$rejectQnty = "rejectQnty_" . $j;
			$bodyPartId = "bodyPartId_" . $j;
			$systemId = "systemId_" . $j;
			$batch_id = "batchId_" . $j;
			$widthtype = "widthTypeId_" . $j;
			$preReprocess = "prereProcess_" . $j;
			$reProcess = "reProcess_" . $j;
			$IsSalesId = "IsSalesId_".$j;
			$bookingWithoutOrder = "bookingWithoutOrder_".$j;
			$bookingNumber = "bookingNumber_".$j;

			if($variable_settingAutoQC ==1 && $QC_barcode_arr[$$barcodeNo] =="")
			{
				echo "20**Barcode not QC passed.\nBarcode no: ".$$barcodeNo;
				die;
			}

			if($scnned[$$barcodeNo][$$reProcess] != "")
			{
				echo "20**Barcode already received.\nBarcode no: ".$$barcodeNo;
				die;

			}
			else if($self_scnned[$$barcodeNo][$$reProcess] != "")
			{
				echo "20**Barcode already delivered.\nBarcode no: ".$$barcodeNo;
				die;
			}



			$dtls_id = return_next_id_by_sequence("PRO_GREY_PROD_DELI_DTLS_PK_SEQ", "pro_grey_prod_delivery_dtls", $con);
			$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);

			if ($data_array_dtls != "") $data_array_dtls .= ",";
			$data_array_dtls .= "(" . $dtls_id . "," . $id . ",67," . $$productionId . ",'" . $$productionDtlsId . "','" . $$systemId . "','" . $$productId . "','" . $$colorId . "','" . $$jobNo . "','" . $$gsm . "','" . $$dia . "','" . $$orderId . "','" . $$bodyPartId . "','" . $$deterId . "','" . $$batch_id . "','" . $$widthtype . "','" . $$rollId . "','" . $$barcodeNo . "','" . $$currentDelivery . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

			if ($data_array_roll != "") $data_array_roll .= ",";
			$data_array_roll .= "(" . $id_roll . ",'" . $$barcodeNo . "'," . $id . "," . $dtls_id . ",'" . $$orderId . "',67,'" . $$currentDelivery . "','" . $$rejectQnty . "','" . $$currentDelivery . "','" . $$rollNo . "','" . $$rollId . "','" . $$preReprocess . "','" . $$reProcess . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "','" . $$IsSalesId . "','".$$bookingWithoutOrder. "','".$$bookingNumber."')";
			

			$barcodeNos .= $$barcodeNo . "__" . $dtls_id . "__" . number_format($$currentDelivery, 2) . ",";
			
			if($dupli_barcode_arr[$$barcodeNo] !="")
			{
				echo "20**Duplicate barcode not allowed";
				oci_rollback($con);
				die;
			}else{
				$dupli_barcode_arr[$$barcodeNo]=$$barcodeNo;
			}
		}

		// echo "5** ".$data_array_dtls;die;
		//echo "10**insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll;oci_rollback($con);die;
		$rID = sql_insert("pro_grey_prod_delivery_mst", $field_array, $data_array, 0);
		$rID2 = sql_insert("pro_grey_prod_delivery_dtls", $field_array_dtls, $data_array_dtls, 1);
		$rID3 = sql_insert("pro_roll_details", $field_array_roll, $data_array_roll, 0);
		//	mysql_query("ROLLBACK");
		 	//echo "10**".$rID."==".$rID2."==".$rID3;die;

		if ($db_type == 0) {
			if ($rID && $rID2 && $rID3) {
				mysql_query("COMMIT");
				echo "0**" . $id . "**" . $new_mrr_number[0] . "**" . substr($barcodeNos, 0, -1);
			} else {
				mysql_query("ROLLBACK");
				echo "5**0**0";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $rID2 && $rID3) {
				oci_commit($con);
				echo "0**" . $id . "**" . $new_mrr_number[0] . "**" . substr($barcodeNos, 0, -1);
			} else {
				oci_rollback($con);
				echo "5**0**0";
			}
		}
		disconnect($con);
		die;
	} 
	else if ($operation == 1)   // Update Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		//echo "5**00"; die;
		$field_array = "delevery_date*company_id*knitting_source*knitting_company*location_id*attention*remarks*updated_by*update_date";
		$data_array = $txt_delivery_date . "*" . $cbo_company_id . "*" . $cbo_knitting_source . "*" . $knit_company_id ."*". $knit_location_id ."*". $txt_attention ."*". $txt_remarks. "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
		$field_array_dtls = "id, mst_id, entry_form, grey_sys_id, sys_dtls_id,grey_sys_number, product_id,color_id,job_no,gsm,dia, order_id,bodypart_id, determination_id,batch_id,width_type, roll_id, barcode_num, current_delivery, inserted_by, insert_date";
		//$dtls_id = return_next_id("id", "pro_grey_prod_delivery_dtls", 1);
		$field_array_update = "current_delivery*updated_by*update_date";
		//$field_array_roll = "barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty,reject_qnty, roll_no, roll_id, inserted_by, insert_date";
		$field_array_roll = "id, barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, reject_qnty, qc_pass_qnty, roll_no, roll_id,prev_reprocess,reprocess, inserted_by, insert_date,is_sales,booking_without_order,booking_no";
		//$id_roll = return_next_id("id", "pro_roll_details", 1);
		//echo "5**";
		$barcodeNos = '';
		for ($j = 1; $j <= $tot_row; $j++) {
			$productionId = "productionId_" . $j;
			$productionDtlsId = "productionDtlsId_" . $j;
			$productId = "productId_" . $j;
			$orderId = "orderId_" . $j;
			$deterId = "deterId_" . $j;
			$rollId = "rollId_" . $j;
			$barcodeNo = "barcodeNo_" . $j;
			$currentDelivery = "currentDelivery_" . $j;
			$rollQty = "rollQty_" . $j;
			$rollNo = "rollNo_" . $j;
			$colorId = "colorId_" . $j;
			$jobNo = "jobNo_" . $j;
			$gsm = "gsm_" . $j;
			$dia = "dia_" . $j;
			$rejectQnty = "rejectQnty_" . $j;
			$bodyPartId = "bodyPartId_" . $j;
			$dtlsId = "dtlsId_" . $j;
			$systemId = "systemId_" . $j;
			$batch_id = "batchId_" . $j;
			$widthtype = "widthTypeId_" . $j;
			$preReprocess = "prereProcess_" . $j;
			$reProcess = "reProcess_" . $j;
			$IsSalesId = "IsSalesId_".$j;
			$bookingWithoutOrder = "bookingWithoutOrder_".$j;
			$bookingNumber = "bookingNumber_".$j;

			if($variable_settingAutoQC ==1 && $QC_barcode_arr[$$barcodeNo] =="")
			{
				echo "20**Barcode not QC passed.\nBarcode no: ".$$barcodeNo;
				die;
			}

			if ($$dtlsId > 0) {
				$dtlsId_arr[] = $$dtlsId;
				$data_array_update[$$dtlsId] = explode("*", ("'" . $$currentDelivery . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));
				$barcode_dtls_arr[$$barcodeNo]['dtls_id'] = $$dtlsId;
				$barcode_dtls_arr[$$barcodeNo]['qty'] = $$currentDelivery;
				$barcodeNos .= $$barcodeNo . "__" . $$dtlsId . "__" . number_format($$currentDelivery, 2) . ",";
				$dtls_id_for_roll = $$dtlsId;
			}
			else
			{

				if($scnned[$$barcodeNo][$$reProcess] != "")
				{
					echo "20**Barcode already received.\nBarcode no: ".$$barcodeNo;
					die;

				}
				else if($self_scnned[$$barcodeNo][$$reProcess] != "")
				{
					echo "20**Barcode already delivered.\nBarcode no: ".$$barcodeNo;
					die;
				}


				$dtls_id = return_next_id_by_sequence("PRO_GREY_PROD_DELI_DTLS_PK_SEQ", "pro_grey_prod_delivery_dtls", $con);

				if ($data_array_dtls != "") $data_array_dtls .= ",";

				$data_array_dtls .= "(" . $dtls_id . "," . $update_id . ",67," . $$productionId . ",'" . $$productionDtlsId . "','" . $$systemId . "','" . $$productId . "','" . $$colorId . "','" . $$jobNo . "','" . $$gsm . "','" . $$dia . "','" . $$orderId . "','" . $$bodyPartId . "','" . $$deterId . "','" . $$batch_id . "'," . $$widthtype . ",'" . $$rollId . "','" . $$barcodeNo . "','" . $$currentDelivery . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				$barcodeNos .= $$barcodeNo . "__" . $dtls_id . "__" . number_format($$currentDelivery, 2) . ",";
				$dtls_id_for_roll = $dtls_id;

				$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
				if ($data_array_roll != "") $data_array_roll .= ",";
				$data_array_roll .= "(" . $id_roll . ",'" . $$barcodeNo . "'," . $update_id . "," . $dtls_id_for_roll . ",'" . $$orderId . "',67,'" . $$currentDelivery . "','" . $$rejectQnty . "','" . $$currentDelivery . "','" . $$rollNo . "','" . $$rollId . "','" . $$preReprocess . "','" . $$reProcess . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "','" . $$IsSalesId . "','".$$bookingWithoutOrder. "','".$$bookingNumber."')";
			}
		}
		$rID2 = true;
		$rID3 = true;
		$rID4 = true;
		$statusChange = true;
		$statusChangeBarcode =true;
		$delete_roll=true;

		//echo "10**insert into pro_grey_prod_delivery_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;

		$txt_deleted_id = str_replace("'", "", $txt_deleted_id);
		$txt_deleted_barcode = str_replace("'", "", $txt_deleted_barcode);
		$txt_del_barcode_reprocess = str_replace("'", "", $txt_del_barcode_reprocess);
		
		if($txt_deleted_barcode != "" )
		{
			$del_barcode_reprocess_arr = explode(",", $txt_del_barcode_reprocess);

			foreach ($del_barcode_reprocess_arr as $val) 
			{
				$barcodeReprocessArr = explode("=", $val);

				$del_barcode = $barcodeReprocessArr[0];
				$del_reprocess = $barcodeReprocessArr[1];

				if($scnned[$del_barcode][$del_reprocess])
				{
					echo "20**Delete Not allowed.Barcode already received.\nBarcode no: ".$del_barcode;
					die;
				}
				//echo "20**Delete Not allowed.Barcode already received.\nBarcode no: ".$del_barcode;
				//die;
			}

			$field_array_status = "updated_by*update_date*status_active*is_deleted";
			$data_array_status = $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*0*1";
			$statusChangeBarcode = sql_multirow_update("pro_roll_details", $field_array_status, $data_array_status, "id", $txt_deleted_id, 0);

			$delete_roll = execute_query("update pro_roll_details set status_active = 0, is_deleted =1, updated_by= ".$_SESSION['logic_erp']['user_id'].", update_date= '".$pc_date_time."' where dtls_id in ($txt_deleted_id ) and barcode_no in ($txt_deleted_barcode) and entry_form=67", 0);
		}


		if ($txt_deleted_id != "") {
			$field_array_status = "updated_by*update_date*status_active*is_deleted";
			$data_array_status = $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*0*1";
			$statusChange = sql_multirow_update("pro_grey_prod_delivery_dtls", $field_array_status, $data_array_status, "id", $txt_deleted_id, 0);
		}

		$rID = sql_update("pro_grey_prod_delivery_mst", $field_array, $data_array, "id", $update_id, 0);
		if (count($data_array_update) > 0) {
			$rID2 = execute_query(bulk_update_sql_statement("pro_grey_prod_delivery_dtls", "id", $field_array_update, $data_array_update, $dtlsId_arr));
		}

		if ($data_array_dtls != "") {
			$rID3 = sql_insert("pro_grey_prod_delivery_dtls", $field_array_dtls, $data_array_dtls, 1);
		}



		if($data_array_roll != "")
		{
			$rID4 = sql_insert("pro_roll_details", $field_array_roll, $data_array_roll, 0);
		}

		/*echo "5**".$rID."&&".$rID2."&&".$rID3."&&".$rID4."&&".$delete_roll."&&". $statusChangeBarcode."&&".$db_type;
		oci_rollback($con);
		die;*/

		if ($db_type == 0) {
			if ($rID && $rID2 && $rID3 && $rID4 && $delete_roll && $statusChange && $statusChangeBarcode ) {
				mysql_query("COMMIT");
				echo "1**" . str_replace("'", '', $update_id) . "**" . str_replace("'", '', $txt_challan_no) . "**" . substr($barcodeNos, 0, -1);
			} else {
				mysql_query("ROLLBACK");
				echo "6**" . str_replace("'", '', $update_id) . "**";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $rID2 && $rID3 && $rID4 && $delete_roll && $statusChange && $statusChangeBarcode) {
				oci_commit($con);
				echo "1**" . str_replace("'", '', $update_id) . "**" . str_replace("'", '', $txt_challan_no) . "**" . substr($barcodeNos, 0, -1);
			} else {
				oci_rollback($con);
				echo "6**" . str_replace("'", '', $update_id) . "**1";
			}
		}
		disconnect($con);
		die;
	}
}


if ($action == "challan_popup") {
	echo load_html_head_contents("Challan Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(data, barcode_nos) {
			$('#hidden_data').val(data);
			$('#hidden_barcode_nos').val(barcode_nos);
			parent.emailwindow.hide();
		}
	</script>

</head>

<body>
	<div align="center" style="width:760px;">
		<form name="searchwofrm" id="searchwofrm">
			<fieldset style="width:760px; margin-left:2px;"  align="center">
				<table cellpadding="0" cellspacing="0" width="750" border="1" rules="all" class="rpt_table">
					<thead>
						<th>Company</th>
						<th>Delivery Date Range</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="180">Please Enter Challan No</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px"
							class="formbutton"/>
							<input type="hidden" name="hidden_data" id="hidden_data">
							<input type="hidden" name="hidden_barcode_nos" id="hidden_barcode_nos">
						</th>
					</thead>
					<tr class="general">
						<td align="center">
							<? echo create_drop_down("cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name", 'id,company_name', 1, '-- Select Company --', $comp_id, "", 0); ?>
						</td>
						<td align="center">
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker"
							style="width:70px" readonly>To
							<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"
							readonly>
						</td>
						<td align="center">
							<?
							$search_by_arr = array(1 => "Challan No", 2 => "Batch No");
							$dd = "change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
							echo create_drop_down("cbo_search_by", 100, $search_by_arr, "", 0, "--Select--", 1, $dd, 0);
							?>
						</td>
						<td align="center" id="search_by_td">
							<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
							id="txt_search_common"/>
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show"
							onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_year_selection').value, 'create_challan_search_list_view', 'search_div', 'finish_feb_delivery_roll_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
							style="width:100px;"/>
						</td>
					</tr>
					<tr>
						<td colspan="5" align="center" height="40"
						valign="middle"><? echo load_month_buttons(1); ?></td>
					</tr>
				</table>
				<div style="width:100%; margin-top:5px;" id="search_div" align="left"></div>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if ($action == "create_challan_search_list_view") {
	$data = explode("_", $data);

	$search_string = "%" . trim($data[0])."%";
	$search_by = $data[1];
	$start_date = $data[2];
	$end_date = $data[3];
	$company_id = $data[4];
	$year_id= $data[5];
	if ($company_id == 0) {
		echo "Please Select Company First.";
		die;
	}

	if ($start_date != "" && $end_date != "") {
		if ($db_type == 0) {
			$date_cond = "and delevery_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd", "-") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd", "-") . "'";
		} else {
			$date_cond = "and delevery_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
		}
	} else {
		$date_cond = "";
	}

	$search_field_cond = "";
	if (trim($data[0]) != "") {
		if ($search_by == 1) $search_field_cond = "and a.sys_number like '$search_string'";
		else if ($search_by == 2)
			$search_field_cond = "and c.batch_no like '$search_string'";
	}

	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and YEAR(a.insert_date)=$year_id"; else $year_cond="";
	}
	else if ($db_type==2)
	{
		if($year_id!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=$year_id"; else $year_cond="";
	}

	if ($db_type == 0) {
		$year_field = "YEAR(a.insert_date) as year,";
		$year_field_group = "YEAR(a.insert_date),";
		//$barcode_arr = return_library_array("select mst_id, group_concat(barcode_num order by id desc) as barcode_num from  pro_grey_prod_delivery_dtls where entry_form=67 and status_active=1 and is_deleted=0 group by mst_id", 'mst_id', 'barcode_num');
	} else if ($db_type == 2) {
		$year_field = "to_char(a.insert_date,'YYYY') as year,";
		$year_field_group = "to_char(a.insert_date,'YYYY'),";
		//$barcode_arr = return_library_array("select mst_id, LISTAGG(barcode_num, ',') WITHIN GROUP (ORDER BY id desc) as barcode_num from  pro_grey_prod_delivery_dtls where entry_form=67 and status_active=1 and is_deleted=0 group by mst_id", 'mst_id', 'barcode_num');
	} 
	else {
		$year_field = "";//defined Later
		$year_field_group = "";//defined Later
	}

	$sql = "SELECT a.id, $year_field a.sys_number_prefix_num, a.sys_number, a.company_id, a.knitting_source, a.knitting_company, a.delevery_date, c.batch_no, a.attention, a.remarks, b.barcode_num 
	from pro_grey_prod_delivery_mst a,  pro_grey_prod_delivery_dtls b, pro_batch_create_mst c
	where a.id=b.mst_id and b.batch_id=c.id and a.entry_form=67 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$company_id $search_field_cond $date_cond $year_cond 
	group by a.id, $year_field_group a.sys_number_prefix_num, a.sys_number, a.company_id, a.knitting_source, a.knitting_company, a.delevery_date, c.batch_no, a.attention, a.remarks, b.barcode_num 
	order by a.id";

	// echo $sql;//die;
	$result = sql_select($sql);

	if(empty($result)){
		echo "Data Not Found";die;
	}else{
		foreach ($result as $row) {
			$all_mst_id[$row[csf('id')]] = $row[csf('id')];

			$all_mst_barcode_arr[$row[csf('id')]] .= $row[csf('barcode_num')].",";
			$all_data_arr[$row[csf('id')]][$row[csf('batch_no')]]["barcode_no"] .= $row[csf('barcode_num')].",";
			$all_data_arr[$row[csf('id')]][$row[csf('batch_no')]]["id"] = $row[csf('id')];
			$all_data_arr[$row[csf('id')]][$row[csf('batch_no')]]["year"] = $row[csf('year')];
			$all_data_arr[$row[csf('id')]][$row[csf('batch_no')]]["sys_number_prefix_num"] = $row[csf('sys_number_prefix_num')];
			$all_data_arr[$row[csf('id')]][$row[csf('batch_no')]]["sys_number"] = $row[csf('sys_number')];
			$all_data_arr[$row[csf('id')]][$row[csf('batch_no')]]["company_id"] = $row[csf('company_id')];
			$all_data_arr[$row[csf('id')]][$row[csf('batch_no')]]["knitting_source"] = $row[csf('knitting_source')];
			$all_data_arr[$row[csf('id')]][$row[csf('batch_no')]]["knitting_company"] = $row[csf('knitting_company')];
			$all_data_arr[$row[csf('id')]][$row[csf('batch_no')]]["delevery_date"] = $row[csf('delevery_date')];
			$all_data_arr[$row[csf('id')]][$row[csf('batch_no')]]["batch_no"] = $row[csf('batch_no')];
			$all_data_arr[$row[csf('id')]][$row[csf('batch_no')]]["attention"] = $row[csf('attention')];
			$all_data_arr[$row[csf('id')]][$row[csf('batch_no')]]["remarks"] = $row[csf('remarks')];
		}
	}

	$company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
	$supllier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');

	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="140">Company</th>
			<th width="80">Challan No</th>
			<th width="80">Batch No</th>
			<th width="70">Year</th>
			<th width="120">Knitting Source</th>
			<th width="140">Knitting Company</th>
			<th>Delivery date</th>
		</thead>
	</table>
	<div style="width:820px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table"
		id="tbl_list_search">
		<?
		$i = 1;
		foreach ($all_data_arr as $sys_id => $sys_data) 
		{
			foreach ($sys_data as $batch_no => $row) 
			{
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

				$knit_comp = "&nbsp;";
				if ($row['knitting_source'] == 1)
					$knit_comp = $company_arr[$row['knitting_company']];
				else
					$knit_comp = $supllier_arr[$row['knitting_company']];

				$data = $row['id'] . "**" . $row['sys_number'] . "**" . change_date_format($row['delevery_date']) . "**" . $row['attention'] . "**" . $row['remarks'] . "**" . $row['company_id'];
				//$barcode_nos = chop($row['barcode_no'],",");
				$barcode_nos = chop($all_mst_barcode_arr[$row['id']],",");
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
					onClick="js_set_value('<? echo $data; ?>','<? echo $barcode_nos; ?>');">
					<td width="40"><? echo $i; ?></td>
					<td width="140"><p><? echo $company_arr[$row['company_id']]; ?></p></td>
					<td width="80"><p>&nbsp;<? echo $row['sys_number_prefix_num']; ?></p></td>
					<td width="80"><p>&nbsp;<? echo $row['batch_no']; ?></p></td>
					<td width="70" align="center"><p><? echo $row['year']; ?></p></td>
					<td width="120"><p><? echo $knitting_source[$row['knitting_source']]; ?>&nbsp;</p></td>
					<td width="140"><p><? echo $knit_comp; ?>&nbsp;</p></td>
					<td align="center"><? echo change_date_format($row['delevery_date']); ?></td>
				</tr>
				<?
				$i++;
			}
		}
		?>
	</table>
</div>
<?
exit();
}

if($action=="populate_delivery_master_n_company_wise_report_button_setting")
{
	extract($_REQUEST);

	$data = explode("__", $data);
	$company_id = $data[0];
	$issue_id = $data[1];
	//var_dump($company_id);


	$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$company_id."'  and module_id=7 and report_id=167 and is_deleted=0 and status_active=1");

	$print_report_format_arr=explode(",",$print_report_format);

	echo "$('#print1').hide();\n";
	echo "$('#print2').hide();\n";
	echo "$('#print_barcode').hide();\n";
	echo "$('#btn_fabric_details').hide();\n";
	echo "$('#printFso_v2').hide();\n";
	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			if($id==86){echo "$('#print1').show();\n";}
			if($id==84){echo "$('#print2').show();\n";}
			if($id==68){echo "$('#print_barcode').show();\n";}
			if($id==69){echo "$('#btn_fabric_details').show();\n";}
			if($id==764){echo "$('#printFso_v2').show();\n";}
		}
	}
	else
	{
		echo "$('#print1').show();\n";
		echo "$('#print2').show();\n";
		echo "$('#print_barcode').show();\n";
		echo "$('#btn_fabric_details').show();\n";
		echo "$('#printFso_v2').show();\n";
	}

	$company_name_array = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$location_array = return_library_array("select id, location_name from lib_location", "id", "location_name");

	$sql = "select id,sys_number_prefix,sys_number_prefix_num,sys_number,delevery_date,company_id,knitting_source,knitting_company,location_id,attention,remarks,entry_form,inserted_by,insert_date from pro_grey_prod_delivery_mst where id=$issue_id and entry_form=67";

	$res = sql_select($sql);
	foreach($res as $row)
	{
		echo "$('#cbo_company_id').val(".$row[csf("company_id")].");\n";
		echo "$('#cbo_knitting_source').val(".$row[csf("knitting_source")].");\n";
		if($row[csf("knitting_source")]==1)
		{
			echo "$('#txt_knit_company').val('".$company_name_array[$row[csf("knitting_company")]]."');\n";
			echo "$('#txt_knitting_location').val('".$location_array[$row[csf("location_id")]]."');\n";
			echo "$('#knit_location_id').val(".$row[csf("location_id")].");\n";
		}
		else
		{
			echo "$('#txt_knit_company').val('".$supplier_arr[$row[csf("knitting_company")]]."');\n";
		}
		echo "$('#knit_company_id').val(".$row[csf("knitting_company")].");\n";
	}


	exit();
}

if ($action == "barcode_popup") {
	echo load_html_head_contents("Barcode Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	if ($company_id > 0) $disable = 1; else $disable = 0;
	?>

	<script>

		var selected_id = new Array();
		var not_qc_selected_id = new Array();

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
			}
		}
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}
		function js_set_value(str) 
		{
			toggle(document.getElementById('search' + str), '#FFFFCC');
			var qc_passed =  $('#txt_qc_passed' + str).val()
			if(qc_passed==1)
			{
				if (jQuery.inArray($('#txt_individual_id' + str).val(), selected_id) == -1) {
					selected_id.push($('#txt_individual_id' + str).val());

				}
				else {
					for (var i = 0; i < selected_id.length; i++) {
						if (selected_id[i] == $('#txt_individual_id' + str).val()) break;
					}
					selected_id.splice(i, 1);
				}
				var id = '';
				for (var i = 0; i < selected_id.length; i++) {
					id += selected_id[i] + ',';
				}
				id = id.substr(0, id.length - 1);

				$('#hidden_barcode_nos').val(id);
			}
			else
			{
				if (jQuery.inArray($('#txt_individual_id' + str).val(), not_qc_selected_id) == -1) {
					not_qc_selected_id.push($('#txt_individual_id' + str).val());

				}
				else {
					for (var i = 0; i < not_qc_selected_id.length; i++) {
						if (not_qc_selected_id[i] == $('#txt_individual_id' + str).val()) break;
					}
					not_qc_selected_id.splice(i, 1);
				}
				var not_qc_id = '';
				for (var i = 0; i < not_qc_selected_id.length; i++) {
					not_qc_id += not_qc_selected_id[i] + ',';
				}
				not_qc_id = not_qc_id.substr(0, not_qc_id.length - 1);

				$('#not_qc_passed').val(not_qc_id);
			}

		}

		function fnc_close() 
		{
			if($('#not_qc_passed').val() !="")
			{
				if(confirm('Barcode qc is not completed yet.\nBarcode No : ' + $('#not_qc_passed').val() + ". \nDo you want to continue with remaining Barcodes?"))
                {
                    parent.emailwindow.hide();
                }
			}
			else
			{
				parent.emailwindow.hide();
			}
		}

		function reset_hide_field() {
			$('#hidden_barcode_nos').val('');
			selected_id = new Array();
		}

		function fnc_show()
		{
			if( $("#txt_date_from").val()=="" || $("#txt_date_to").val()=="")
			{
				if(form_validation('cbo_company_id*txt_search_common','Company*Search')==false)
				{
					return;
				}
			}
			else
			{
				if(form_validation('cbo_company_id','Company')==false)
				{
					return;
				}
			}
			show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_barcode_search_list_view', 'search_div', 'finish_feb_delivery_roll_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();');
		}

	</script>

</head>

<body>
	<div align="center" style="width:760px;">
		<form name="searchwofrm" id="searchwofrm">
			<fieldset style="width:760px; margin-left:50px;">
				<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" width="750" border="1" rules="all" class="rpt_table">
					<thead>
						<th>Company</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="180">Please Enter Batch No</th>
						<th width="200">Date Range</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px"
							class="formbutton"/>
							<input type="hidden" name="hidden_barcode_nos" id="hidden_barcode_nos">
							<input type="hidden" name="not_qc_passed" id="not_qc_passed">
						</th>
					</thead>
					<tr class="general">
						<td align="center">
							<? echo create_drop_down("cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name", 'id,company_name', 1, '-- Select Company --', $company_id, "", $disable); ?>
						</td>
						<td align="center">
							<?
							$search_by_arr = array(1 => "Order No", 2 => "Batch No", 3 => "Sales Order No");
							$dd = "change_search_event(this.value, '0*0*0*0', '0*0*0*0', '../../') ";
							echo create_drop_down("cbo_search_by", 100, $search_by_arr, "", 0, "--Select--", 2, $dd, 0);
							?>
						</td>
						<td align="center" id="search_by_td">
							<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
							id="txt_search_common"/>
						</td>
						<td align="center">
	                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
	                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
                    	</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show"
							onClick="fnc_show();"
							style="width:100px;"/>
						</td>
					</tr>
					<tr>
		            	<td align="center" height="40" valign="middle" colspan="5">
							<? echo load_month_buttons(1);  ?>
		                </td>
		            </tr>
				</table>
				<div style="width:100%; margin-top:5px; margin-left:10px" id="search_div" align="left"></div>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}


if ($action == "create_barcode_search_list_view") {
	$data = explode("_", $data);

	$search_string = trim($data[0]);
	$search_by = $data[1];
	$company_id = $data[2];
	$fromDate = $data[3];
	$toDate = $data[4];

	if( $fromDate!="" && $toDate!="" )
	{
		if($db_type==0)
		{
			$sql_date_cond_recv = " and a.receive_date  between '".change_date_format($fromDate,'yyyy-mm-dd')."' and '".change_date_format($toDate,'yyyy-mm-dd')."'";
			$sql_date_cond_issue = " and a.issue_date  between '".change_date_format($fromDate,'yyyy-mm-dd')."' and '".change_date_format($toDate,'yyyy-mm-dd')."'";
		}
		if($db_type==2 || $db_type==1)
		{
			$sql_date_cond_recv = " and a.receive_date  between '".change_date_format($fromDate,'','','1')."' and '".change_date_format($toDate,'','','1')."'";
			$sql_date_cond_issue = " and a.issue_date  between '".change_date_format($fromDate,'','','1')."' and '".change_date_format($toDate,'','','1')."'";
		}
	}


	if ($company_id == 0) {
		echo "Please Select Company First.";
		die;
	}

	$search_field_cond = "";
	if (trim($data[0]) != "") {
		if ($search_by == 1) $search_field_cond = "and d.po_number like '%$search_string%'";
	}

	$batch_con = "";
	if ($search_by == 2 && $data[0] != "") {
		$batch_con = "and e.batch_no LIKE '%$search_string%'";
	}

	if ($search_by == 3 && $data[0] != "") {
		$search_field_cond .= "and d.job_no like '%$search_string%'";
	}

	if ($search_by == 1 || $search_by == 2)     
	{
		$sql = "SELECT a.recv_number, c.barcode_no, c.roll_no, c.qc_pass_qnty as qnty,c.reprocess,c.is_sales,c.po_breakdown_id,c.booking_without_order, e.batch_no as batch_no, b.body_part_id, g.detarmination_id, g.gsm, g.dia_width FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c, wo_po_break_down d,pro_batch_create_mst e, product_details_master g WHERE a.entry_form=66 and a.company_id=$company_id  and a.id=b.mst_id  and c.entry_form=66 and b.id=c.dtls_id and c.po_breakdown_id=d.id and b.batch_id=e.id and b.prod_id=g.id $search_field_cond $batch_con and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.is_sales=0 and e.booking_without_order=0 $sql_date_cond_recv group by a.recv_number, c.barcode_no, c.roll_no, c.qc_pass_qnty,c.reprocess,c.is_sales,c.po_breakdown_id,c.booking_without_order, e.batch_no, b.body_part_id, g.detarmination_id, g.gsm, g.dia_width
 		union all
 
 		SELECT a.issue_number as recv_number,c.barcode_no, c.roll_no, c.qc_pass_qnty as qnty,c.reprocess,c.is_sales,c.po_breakdown_id,c.booking_without_order,  e.batch_no as batch_no, b.body_part_id, g.detarmination_id, g.gsm, g.dia_width FROM inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction f,  pro_roll_details c, wo_po_break_down d , pro_batch_create_mst e, product_details_master g WHERE a.id=b.mst_id and b.trans_id= f.id and b.id=c.dtls_id and a.id=c.mst_id and c.po_breakdown_id=d.id and f.pi_wo_batch_no=e.id and a.company_id=$company_id and a.entry_form=71 and c.entry_form=71 and c.reprocess>0 and c.roll_no>0 and nvl(c.is_sales,0) = 0 and e.booking_without_order=1 and b.prod_id=g.id $search_field_cond $batch_con $sql_date_cond_issue group by a.issue_number,c.barcode_no, c.roll_no, c.qc_pass_qnty,c.reprocess,c.is_sales,c.po_breakdown_id,c.booking_without_order ,e.batch_no, b.body_part_id, g.detarmination_id, g.gsm, g.dia_width";
 	}

 	if ($search_by == 2)
	{
		$sql .= " union all ";
	}

	if ($search_by == 2 || $search_by == 3)
	{
		$sql .= "SELECT a.recv_number, c.barcode_no, c.roll_no, c.qc_pass_qnty as qnty,c.reprocess,c.is_sales,c.po_breakdown_id,c.booking_without_order, e.batch_no as batch_no, b.body_part_id, g.detarmination_id, g.gsm, g.dia_width FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c, fabric_sales_order_mst d,pro_batch_create_mst e, product_details_master g WHERE a.entry_form=66 and a.company_id=$company_id  and a.id=b.mst_id  and c.entry_form=66 and b.id=c.dtls_id and c.po_breakdown_id=d.id and b.batch_id=e.id and b.prod_id=g.id $search_field_cond $batch_con and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.is_sales=1 $sql_date_cond_recv group by a.recv_number, c.barcode_no, c.roll_no, c.qc_pass_qnty,c.reprocess,c.is_sales,c.po_breakdown_id,c.booking_without_order, e.batch_no, b.body_part_id, g.detarmination_id, g.gsm, g.dia_width
 		union all
 
 		SELECT a.issue_number as recv_number,c.barcode_no, c.roll_no, c.qc_pass_qnty as qnty,c.reprocess,c.is_sales,c.po_breakdown_id,c.booking_without_order, e.batch_no as batch_no, b.body_part_id, g.detarmination_id, g.gsm, g.dia_width FROM inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction f,  pro_roll_details c, fabric_sales_order_mst d , pro_batch_create_mst e, product_details_master g WHERE a.id=b.mst_id and b.trans_id= f.id and b.id=c.dtls_id and a.id=c.mst_id and c.po_breakdown_id=d.id and f.pi_wo_batch_no=e.id and a.company_id=$company_id and a.entry_form=71 and c.entry_form=71 and c.reprocess>0 and c.roll_no>0 and nvl(c.is_sales,0) = 1 and b.prod_id=g.id $search_field_cond $batch_con $sql_date_cond_issue group by a.issue_number,c.barcode_no, c.roll_no, c.qc_pass_qnty,c.reprocess,c.is_sales,c.po_breakdown_id,c.booking_without_order ,e.batch_no, b.body_part_id, g.detarmination_id, g.gsm, g.dia_width";
 	}

 	if($search_by == 2 && $data[0] != "")
 	{
 		$sql .= " union all ";
 		$sql .=" SELECT a.recv_number, c.barcode_no, c.roll_no, c.qc_pass_qnty as qnty,c.reprocess,c.is_sales,c.po_breakdown_id,c.booking_without_order, e.batch_no as batch_no, b.body_part_id, g.detarmination_id, g.gsm, g.dia_width
 		FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c, wo_non_ord_samp_booking_mst d,pro_batch_create_mst e, product_details_master g 
 		WHERE a.entry_form=66 and a.company_id=$company_id and a.id=b.mst_id and c.entry_form=66 and b.id=c.dtls_id and e.booking_no_id=d.id and e.booking_without_order=1 and b.batch_id=e.id and b.prod_id=g.id $batch_con and a.status_active=1 and a.is_deleted=0 
 		and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
 		and c.roll_no>0 
 		and c.is_sales=0 and e.booking_no  like '%-SMN-%' $sql_date_cond_recv
 		group by a.recv_number, c.barcode_no, c.roll_no, c.qc_pass_qnty,c.reprocess,c.is_sales,c.po_breakdown_id,c.booking_without_order, e.batch_no, b.body_part_id, g.detarmination_id, g.gsm, g.dia_width
 		union all 
 		SELECT a.issue_number as recv_number,c.barcode_no, c.roll_no, c.qc_pass_qnty as qnty,c.reprocess,c.is_sales,c.po_breakdown_id,c.booking_without_order, e.batch_no as batch_no, b.body_part_id, g.detarmination_id, g.gsm, g.dia_width
 		FROM inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction f, pro_roll_details c, wo_non_ord_samp_booking_mst d , pro_batch_create_mst e, product_details_master g 
 		WHERE a.id=b.mst_id and b.trans_id= f.id and b.id=c.dtls_id and a.id=c.mst_id and e.booking_no_id=d.id and e.booking_without_order=1 and f.pi_wo_batch_no=e.id and b.prod_id=g.id and a.company_id=$company_id and a.entry_form=71 and c.entry_form=71 and c.reprocess>0 and c.roll_no>0 
 		and nvl(c.is_sales,0) = 0 and e.booking_no  like '%-SMN-%' $batch_con $sql_date_cond_issue
 		group by a.issue_number,c.barcode_no, c.roll_no, c.qc_pass_qnty,c.reprocess,c.is_sales,c.po_breakdown_id,c.booking_without_order ,e.batch_no, b.body_part_id, g.detarmination_id, g.gsm, g.dia_width";
 	}


	//echo $sql;//die;
	$result = sql_select($sql);
	if(empty($result))
	{
		echo "Data Not Found";
		die;
	}

	foreach ($result as $val)
	{
		if($val[csf("is_sales")] == 1)
		{
			$all_sales_po_arr[$val[csf("po_breakdown_id")]] = $val[csf("po_breakdown_id")];
		}else{
			$all_po_arr[$val[csf("po_breakdown_id")]] =$val[csf("po_breakdown_id")];
		}
		$barcode_no_arr[$val[csf("barcode_no")]]=$val[csf("barcode_no")];
	}

	$scanned_barcode_arr = array();
	$all_barcode_no_cond=""; $BarCond="";
	$barcode_no_arr = array_filter($barcode_no_arr);
	if(count($barcode_no_arr)>0)
	{
		$barcode_nos=implode(",",$barcode_no_arr);
		if($db_type==2 && count($barcode_no_arr)>999)
		{
			$barcode_no_arr_chunk=array_chunk($barcode_no_arr,999) ;
			foreach($barcode_no_arr_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$BarCond.=" barcode_no in($chunk_arr_value) or ";
			}

			$all_barcode_no_cond.=" and (".chop($BarCond,'or ').")";
		}
		else
		{
			$all_barcode_no_cond=" and barcode_no in($barcode_nos)";
		}

		$barcodeData = sql_select("select barcode_no,reprocess from pro_roll_details where entry_form=67 and status_active=1 and is_deleted=0 $all_barcode_no_cond");

		foreach ($barcodeData as $row) 
		{
			$scanned_barcode_arr[$row[csf('barcode_no')]][$row[csf('reprocess')]] = $row[csf('barcode_no')];
		}

		//echo "select barcode_no from pro_qc_result_mst where entry_form=267 and roll_status=1 and status_active=1 and is_deleted=0 $all_barcode_no_cond";die;
		$QC_barcode_sql = sql_select("select barcode_no from pro_qc_result_mst where entry_form=267 and roll_status=1 and status_active=1 and is_deleted=0 $all_barcode_no_cond");
		foreach ($QC_barcode_sql as $row) 
		{
			$QC_barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
		}
	}

	$ref_po_no_cond=""; $poCond="";
	$job_arr=array();
	$all_po_arr = array_filter($all_po_arr);
	if(count($all_po_arr)>0)
	{
		$all_po_nos=implode(",",$all_po_arr);
		if($db_type==2 && count($all_po_arr)>999)
		{
			$all_po_chunk=array_chunk($all_po_arr,999) ;
			foreach($all_po_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$poCond.=" c.id in($chunk_arr_value) or ";
			}

			$ref_po_no_cond.=" and (".chop($poCond,'or ').")";
		}
		else
		{
			$ref_po_no_cond=" and c.id in($all_po_nos)";
		}

		$sql_job=sql_select("select a.buyer_id, b.job_no as job_no_mst,b.booking_no,b.po_break_down_id,c.po_number, c.shipment_date from wo_booking_mst a, wo_booking_dtls b,wo_po_break_down c where a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.booking_type in(1,4) $ref_po_no_cond group by b.job_no,b.booking_no,a.buyer_id,b.po_break_down_id,c.po_number,c.shipment_date");

		foreach ($sql_job as $job_row) {
			$job_arr[$job_row[csf('booking_no')]]["job_no_mst"] 		= $job_row[csf('job_no_mst')];
			$job_arr[$job_row[csf('booking_no')]]["buyer_id"] 			= $job_row[csf('buyer_id')];
			$job_arr[$job_row[csf('booking_no')]]["po_number"] 			= $job_row[csf('po_number')];

			$job_arr[$job_row[csf('po_break_down_id')]]["job_no_mst"] 	= $job_row[csf('job_no_mst')];
			$job_arr[$job_row[csf('po_break_down_id')]]["buyer_id"] 	= $job_row[csf('buyer_id')];
			$job_arr[$job_row[csf('po_break_down_id')]]["po_number"] 	= $job_row[csf('po_number')];
			$job_arr[$job_row[csf('po_break_down_id')]]["shipment_date"]= $job_row[csf('shipment_date')];
		}
	}

	$ref_sales_po_no_cond=""; $salesCond=""; $chunk_arr_value="";
	$sales_arr=array();
	$all_sales_po_arr = array_filter($all_sales_po_arr);
	if(count($all_sales_po_arr)>0){
		$all_sales_po_nos=implode(",",$all_sales_po_arr);
		if($db_type==2 && count($all_sales_po_arr)>999)
		{
			$all_sales_po_chunk=array_chunk($all_sales_po_nos,999) ;
			foreach($all_sales_po_chunk as $chunk_salesarr)
			{
				$chunk_arr_value=implode(",",$chunk_salesarr);
				$salesCond.=" id in($chunk_arr_value) or ";
			}

			$ref_sales_po_no_cond.=" and (".chop($salesCond,'or ').")";
		}
		else
		{
			$ref_sales_po_no_cond=" and id in($all_sales_po_nos)";
		}

		$sql_sales=sql_select("select id,job_no,within_group,buyer_id,sales_booking_no,delivery_date,po_job_no from fabric_sales_order_mst where status_active=1 and is_deleted=0 $ref_sales_po_no_cond");
		foreach ($sql_sales as $sales_row) {
			$sales_arr[$sales_row[csf('id')]]["job_no_mst"] 		= $sales_row[csf('job_no')];
			$sales_arr[$sales_row[csf('id')]]["buyer_id"] 			= $sales_row[csf('buyer_id')];
			$sales_arr[$sales_row[csf('id')]]["within_group"] 		= $sales_row[csf('within_group')];
			$sales_arr[$sales_row[csf('id')]]["sales_booking_no"] 	= $sales_row[csf('sales_booking_no')];
			$sales_arr[$sales_row[csf('id')]]["delivery_date"] 		= $sales_row[csf('delivery_date')];
			$sales_arr[$sales_row[csf('id')]]["po_job_no"] 			= $sales_row[csf('po_job_no')];
		}
	}

	$composition_arr = array();
	$constructtion_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and b.status_active=1";
	$data_array_deter = sql_select($sql_deter);
	foreach ($data_array_deter as $row) {
		$constructtion_arr[$row[csf('id')]] = $row[csf('construction')];
		$composition_arr[$row[csf('id')]] .= $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "% ";
	}
	unset($data_array_deter);

	//N.B. item_category_id=13 because there is only one Field in Variable settings.
	$variable_settingAutoQC = return_field_value("qc_mandatory", "variable_settings_production", "company_name =$company_id and variable_list in(48) and item_category_id=2 and is_deleted=0 and status_active=1", "qc_mandatory");

	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1080" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="120">System Id</th>
			<th width="110">Job No</th>
			<th width="110">Body Part</th>
			<th width="150">Item Desc.</th>
			<th width="110">Order/FSO No</th>
			<th width="80">Shipment Date</th>
			<th width="100">Batch No</th>
			<th width="100">Barcode No</th>
			<th width="60">Roll No</th>
			<th>Roll Qty.</th>
		</thead>
	</table>
	<div style="width:1100px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1080" class="rpt_table"
		id="tbl_list_search">
		<?
		$i = 1;
		foreach ($result as $row) 
		{
			if ($scanned_barcode_arr[$row[csf('barcode_no')]] [$row[csf('reprocess')]]== "") 
			{
				$is_sales = $row[csf('is_sales')];
				if($search_by == 3)
				{
					$within_group 	= $sales_arr[$row[csf('po_breakdown_id')]]["within_group"];
					if($within_group == 1){
						$sales_booking_no 	= $sales_arr[$row[csf('po_breakdown_id')]]["sales_booking_no"];
						$job_no 			= $sales_arr[$row[csf('po_breakdown_id')]]["po_job_no"];
					}else{
						$job_no 			= "";
					}
					$shipment 	= change_date_format($sales_arr[$row[csf('po_breakdown_id')]]["delivery_date"]);
					$order_no 	= $sales_arr[$row[csf('po_breakdown_id')]]["job_no_mst"];
				}
				else
				{
					if($is_sales == 1)
					{
						$within_group 	= $sales_arr[$row[csf('po_breakdown_id')]]["within_group"];
						if($within_group == 1){
							$sales_booking_no 	= $sales_arr[$row[csf('po_breakdown_id')]]["sales_booking_no"];
							$job_no 	= $job_arr[$sales_booking_no]["job_no_mst"];
						}else{
							$job_no 	= "";
						}
						$order_no 	= $sales_arr[$row[csf('po_breakdown_id')]]["job_no_mst"];
						$shipment 	= change_date_format($sales_arr[$row[csf('po_breakdown_id')]]["delivery_date"]);
						$job_no 			= $sales_arr[$row[csf('po_breakdown_id')]]["po_job_no"];
					}else{
						if($row[csf('booking_without_order')]==1)
						{
							$job_no 	= "";
							$order_no 	= "";
						}else {
							$job_no 	= $job_arr[$row[csf('po_breakdown_id')]]["job_no_mst"];
							$order_no 	= $job_arr[$row[csf('po_breakdown_id')]]["po_number"];
							$shipment 	= change_date_format($job_arr[$row[csf('po_breakdown_id')]]["shipment_date"]);
						}
					}
				}

				if($variable_settingAutoQC ==1)
				{
					//if variable yes then check barcode in qc passed table
					if($QC_barcode_arr[$row[csf('barcode_no')]])
					{
						$qc_passed = 1;
					}else{
						$qc_passed = 0;
					}
				}
				else
				{
					$qc_passed = 1;
				}
				

				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
				$batch_no = $row[csf('batch_no')];//$scanned_batch_arr[$row[csf('barcode_no')]]['batch_no'];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
					id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">
					<td width="40">
						<? echo $i; ?>
						<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
						<input type="hidden" name="txt_qc_passed" id="txt_qc_passed<?php echo $i; ?>" value="<?php echo $qc_passed; ?>"/>
					</td>
					<td width="120"><p><? echo $row[csf('recv_number')]; ?></p></td>
					<td width="110"><p><? echo $job_no; ?></p></td>
					<td width="110"><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
					<td width="150"><p><? echo $constructtion_arr[$row[csf('detarmination_id')]].', '.$composition_arr[$row[csf('detarmination_id')]].', '.$row[csf('gsm')].', '.$row[csf('dia_width')];; ?></p></td>
					<td width="110"><p><? echo $order_no; ?></p></td>
					<td width="80"
					align="center"><? echo $shipment; ?></td>
					<td width="100"><p><? echo $batch_no; ?></p></td>
					<td width="100"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
					<td width="60"><? echo $row[csf('roll_no')]; ?></td>
					<td align="right"><? echo number_format($row[csf('qnty')], 2); ?></td>
				</tr>
				<?
				$i++;
			}
		}
			?>
		</table>
	</div>
	<table width="720">
		<tr>
			<td align="left">
				<input type="checkbox" name="close" class="formbutton" onClick="check_all_data()"/> Check/Uncheck
			</td>
			<td align="center">
				<input type="button" name="close" class="formbutton" value="Close" id="main_close"
				onClick="fnc_close();" style="width:100px"/>
			</td>
		</tr>
	</table>
	<?
	exit();
}


if ($action == "grey_delivery_print") {
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$txt_challan_no = $data[1];
	$update_id = $data[2];

	$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name from lib_company");
	foreach ($company_data as $row) {
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
	}

	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	// $batch_arr = return_library_array("select id,batch_no from  pro_batch_create_mst", 'id', 'batch_no');
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");

	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}


	$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$data[0]'", "image_location");
	?>
	<div style="width:1010px;">
		<table width="1010" cellspacing="0" align="center" border="0">

			<tr>
				<td colspan="2" rowspan="3">
					<img src="../../../<? echo $image_location; ?>" height="70" width="200" style="float:left;">
				</td>
				<td align="center" style="font-size:x-large">
					<strong><? echo $company_array[$company]['name']; ?></strong></td>
				</tr>

				<tr>
					<td align="center" style="font-size:16px"><strong><u>Finish Fabric Roll Delivery To Store</u></strong>
					</td>
				</tr>
				<tr>
					<td align="center" style="font-size:18px"><strong><u>Challan No <? echo $txt_challan_no; ?></u></strong>
					</td>
				</tr>
			</table>
			<br>
			<?
			$sql_data = sql_select("select id, knitting_source, knitting_company, delevery_date, remarks from pro_grey_prod_delivery_mst where entry_form=67 and status_active=1 and is_deleted=0 and company_id=$company  and id=$update_id order by id");

			?>


			<table width="1310" cellspacing="0" align="center" border="0">
				<tr>
					<td style="font-size:16px; font-weight:bold;" width="150">Company</td>
					<td width="200">:&nbsp;<? echo $company_array[$company]['name']; ?></td>
					<td style="font-size:16px; font-weight:bold;" width="150">Delivery Date</td>
					<td width="200" align=""><? echo change_date_format($sql_data[0][csf('delevery_date')]); ?></td>
					<td style="font-size:16px; font-weight:bold;" width="150">Prod. Source</td>
					<td width="200">:&nbsp;<? echo $knitting_source[$sql_data[0][csf('knitting_source')]]; ?></td>
				</tr>
				<tr>

					<td style="font-size:16px; font-weight:bold;" width="150">Dye/Finishing Company</td>
					<td width="200">:&nbsp;
						<?
						if ($sql_data[0][csf('knitting_source')] == 1) echo $company_array[$sql_data[0][csf('knitting_company')]]['name'];
						else  echo $supplier_arr[$sql_data[0][csf('knitting_company')]];
						?>
					</td>
					<td width="" id="barcode_img_id" colspan="2"></td>
					<td style="font-size:16px; font-weight:bold;" width="150">Remarks</td>
					<td width="200">:&nbsp;<? echo $sql_data[0][csf('remarks')]; ?></td>
				</tr>
				<tr>


				</tr>
			</table>
			<br>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1450" class="rpt_table">
				<thead>
					<tr>
						<th width="30">SL</th>
						<th width="100">Barcode No</th>
						<th width="60">Batch No</th>
						<th width="100">Booking No</th>
						<th width="100">Buyer Name</th>
						<th width="90">Order No <br> Style</th>
						<th width="70">Buyer <br> Job</th>
						<!--<th width="70">Knitting Source</th>-->
						<th width="70">Prod. Source</th>
						<th width="100">Dye/Finishing Company</th>
						<th width="50">Product Id</th>
						<th width="80">Body Part</th>
						<th width="150">Fabric Type</th>
						<th width="70"> Color</th>

						<th width="50">GSM</th>
						<th width="40">Dia</th>
						<th width="60">Dia/Width Type</th>

						<th width="40">Roll No</th>
						<th width="40">Grey Qty</th>
						<th width="40">Reject Qty</th>
						<th>QC Pass Qty</th>
					</tr>
				</thead>
				<?

				$data_array = sql_select("SELECT a.id, a.company_id, a.recv_number, a.receive_basis, a.receive_date, a.booking_no, a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id,b.color_id ,b.batch_id, b.fabric_description_id,b.body_part_id,b.dia_width_type, b.gsm, b.width, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.reject_qnty FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=66 and c.entry_form=66 and c.status_active=1 and c.is_deleted=0 and c.barcode_no in (" . $data[4] . ")");

				$roll_details_array = array();
				$barcode_array = array();
				foreach ($data_array as $row) {

					$roll_details_array[$row[csf("barcode_no")]]['knitting_source_id'] = $row[csf("knitting_source")];
					$roll_details_array[$row[csf("barcode_no")]]['knitting_source'] = $knitting_source[$row[csf("knitting_source")]];
					$roll_details_array[$row[csf("barcode_no")]]['knitting_company_id'] = $row[csf("knitting_company")];

					if ($row[csf("knitting_source")] == 1) {
						$roll_details_array[$row[csf("barcode_no")]]['knitting_company'] = $company_array[$row[csf("knitting_company")]]['name'];
					} else if ($row[csf("knitting_source")] == 3) {
						$roll_details_array[$row[csf("barcode_no")]]['knitting_company'] = $supplier_arr[$row[csf("knitting_company")]];
					}

				}
				$i = 1;
				$tot_qty = 0; $tot_reject_qty= 0;
				$receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan");
				
				$sql_update = sql_select("SELECT  b.id,b.grey_sys_number,b.job_no,b.order_id,b.job_no,b.bodypart_id,b.batch_id,b.dia,b.width_type, b.determination_id,b.gsm,b.product_id,b.color_id,c.roll_no,c.barcode_no,b.current_delivery,c.reject_qnty, c.po_breakdown_id, c.booking_without_order, c.booking_no, c.qnty
				from pro_grey_prod_delivery_dtls b, pro_roll_details c
				where  b.id=c.dtls_id and c.barcode_no in (" . $data[4] . ") and  b.entry_form=67 and  c.entry_form=67  and c.mst_id=$update_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by c.roll_no");

				foreach ($sql_update as $row) 
				{
					$batch_id_arr[$row[csf('batch_id')]] = $row[csf('batch_id')];
					if ($row[csf('booking_without_order')]==0) // order
					{
						$order_id_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
					}
					else
					{
						$non_order_id_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
					}
				}

				$non_order_id_arr= array_filter($non_order_id_arr);
				if(!empty($non_order_id_arr))
				{
					$all_non_order_id=implode(',', $non_order_id_arr);
					$non_order_sql="SELECT b.id, b.booking_no, c.style_id, c.style_des,b.buyer_id from wo_non_ord_samp_booking_mst b, wo_non_ord_samp_booking_dtls c 
					where  b.booking_no=c.booking_no and b.status_active = 1 and b.is_deleted=0 and c.status_active = 1  and c.is_deleted=0 and b.id in($all_non_order_id) group by b.id, b.booking_no, c.style_id, c.style_des,b.buyer_id";

					// echo $non_order_sql;
					$non_order_data=sql_select($non_order_sql);
					foreach ($non_order_data as $key => $value) 
					{
						$non_order_array[$value[csf('id')]]['style_id'] = $value[csf('style_id')];
						$style_id_array[$value[csf('style_id')]] = $value[csf('style_id')];
						$style_des_array[$value[csf('id')]]['style_des'] = $value[csf('style_des')];
						$non_order_array[$value[csf('id')]]['buyer_id'] = $value[csf('buyer_id')];
						$non_order_array[$value[csf('id')]]['booking_no'] = $value[csf('booking_no')];
					}
					$style_id_array= array_filter($style_id_array);
					if(!empty($style_id_array))
					{
						$all_style_ids=implode(',', $style_id_array);
						$non_order_style_sql="SELECT id, style_ref_no from SAMPLE_DEVELOPMENT_MST where id in($all_style_ids) and status_active = 1  and is_deleted=0 ";
						$non_order_style_data=sql_select($non_order_style_sql);
						foreach ($non_order_style_data as $key => $value) 
						{
							$style_name_array[$value[csf('id')]] = $value[csf('style_ref_no')];
						}
					}

					// print_r($non_order_array);
					$all_batch_ids= implode(",",array_filter($batch_id_arr));
					$batchSql=sql_select("select a.id,a.booking_no,b.barcode_no from PRO_BATCH_CREATE_MST a,PRO_BATCH_CREATE_DTLS b where a.id=b.mst_id and a.booking_without_order=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id in($all_batch_ids) ");
					foreach ($batchSql as $row) 
					{
						$booking_infoArr[$row[csf("id")]][$row[csf("barcode_no")]]['booking_no']=$row[csf("booking_no")];
					}
					//print_r($booking_infoArr);
				}

				$order_id_arr= array_filter($order_id_arr);
				if(!empty($order_id_arr))
				{
					// echo "SELECT a.buyer_id, b.job_no as job_no_mst,b.booking_no,b.po_break_down_id,c.po_number, c.shipment_date, d.job_no_prefix_num,d.buyer_name,d.style_ref_no
					// from wo_booking_mst a, wo_booking_dtls b,wo_po_break_down c, wo_po_details_master d
					// where a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.booking_type in(1,4) and c.job_no_mst=d.job_no and b.po_break_down_id in (". implode(',', $order_id_arr) .") 
					// group by b.job_no,b.booking_no,a.buyer_id,d.buyer_name, b.po_break_down_id, c.po_number, c.shipment_date, d.job_no_prefix_num, d.buyer_name, d.style_ref_no";
					$sql_job=sql_select("SELECT a.buyer_id, b.job_no as job_no_mst,b.booking_no,b.po_break_down_id,c.po_number, c.shipment_date, d.job_no_prefix_num,d.buyer_name,d.style_ref_no
					from wo_booking_mst a, wo_booking_dtls b,wo_po_break_down c, wo_po_details_master d
					where a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.booking_type in(1,4) and c.job_no_mst=d.job_no and b.po_break_down_id in (". implode(',', $order_id_arr) .") 
					group by b.job_no,b.booking_no,a.buyer_id,d.buyer_name, b.po_break_down_id, c.po_number, c.shipment_date, d.job_no_prefix_num, d.buyer_name, d.style_ref_no");
					$job_sql_result = $sql_job;
					foreach ($job_sql_result as $job_row) 
					{
						$job_array[$job_row[csf('po_break_down_id')]]['job'] = $job_row[csf('job_no_prefix_num')];
						$job_array[$job_row[csf('po_break_down_id')]]['po'] = $job_row[csf('po_number')];
						$job_array[$job_row[csf('po_break_down_id')]]['buyer'] = $job_row[csf('buyer_name')];
						//$job_array[$job_row[csf('po_break_down_id')]]['booking_no'] = $job_row[csf('booking_no')];
						$job_array[$job_row[csf('po_break_down_id')]]['style_ref_no'] = $job_row[csf('style_ref_no')];
					}
				}

				$batch_barcode_data = sql_select("SELECT a.id, a.batch_no, b.barcode_no, b.batch_qnty as qnty,a.booking_no 
				FROM pro_batch_create_mst a, pro_batch_create_dtls b 
				WHERE a.id=b.mst_id and a.entry_form in(0,66) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.barcode_no>0 and barcode_no in (" . $data[4] . ")");
				foreach ($batch_barcode_data as $row) 
				{
					$grey_qty_arr[$row[csf("barcode_no")]]['batch_no'] = $row[csf("batch_no")];
					$grey_qty_arr[$row[csf("barcode_no")]]['grey_qty'] = $row[csf("qnty")];
					$grey_qty_arr[$row[csf("barcode_no")]]['booking_no'] = $row[csf("booking_no")];
				}

				foreach ($sql_update as $row) 
				{
					if ($row[csf('booking_without_order')]==0) // order
					{
						$booking_no=$row[csf('booking_no')];
						if ($booking_no==0) 
						{
							$booking_no=$grey_qty_arr[$row[csf("barcode_no")]]['booking_no'];
						}
						$po_style=$job_array[$row[csf('po_breakdown_id')]]['po'].'<br>'.$job_array[$row[csf('po_breakdown_id')]]['style_ref_no'];
						$buyer_job=$buyer_array[$job_array[$row[csf('po_breakdown_id')]]['buyer']] . "<br>" . $job_array[$row[csf('po_breakdown_id')]]['job'];
						$buyer_name = $buyer_array[$job_array[$row[csf('po_breakdown_id')]]['buyer']];
					}
					else
					{
						//$booking_no=$row[csf('booking_no')];
						if($row[csf('batch_id')]!="")
						{
							
							$booking_no=$booking_infoArr[$row[csf("batch_id")]][$row[csf("barcode_no")]]['booking_no'];
						}
						else{
							$booking_no=$non_order_array[$row[csf('po_breakdown_id')]]['booking_no'];
						}
						if ($non_order_array[$row[csf('po_breakdown_id')]]['style_id']=="") 
						{
							$po_style=$style_des_array[$row[csf('po_breakdown_id')]]['style_des'];
						}
						else
						{
							$po_style=$style_name_array[$non_order_array[$row[csf('po_breakdown_id')]]['style_id']];							
						}
						$buyer_job='';
						$buyer_name = $buyer_array[$non_order_array[$row[csf('po_breakdown_id')]]['buyer_id']];
					}
					$batch_no=$grey_qty_arr[$row[csf("barcode_no")]]['batch_no'];
					$grey_qty=$grey_qty_arr[$row[csf("barcode_no")]]['grey_qty'];
					?>
					<tr>
						<td width="30"><? echo $i; ?></td>
						<td width="100" style="word-break:break-all;"><? echo $row[csf('barcode_no')]; ?></td>
						<td width="60" style="word-break:break-all;" align="center"><? echo $batch_no; //$batch_arr[$row[csf('batch_id')]]; ?></td>

						<td width="100" style="word-break:break-all;" align="center"><? echo $booking_no; ?></td>						
						<td width="100" style="word-break:break-all;" align="center"><? echo $buyer_name; ?></td>						
						<td width="90" style="word-break:break-all;" align="center"><? echo $po_style; ?></td>
						<td width="60" style="word-break:break-all;" align="center"><? echo $buyer_job; ?></td>
						
						<td width="70" style="word-break:break-all;"><? echo $roll_details_array[$row[csf("barcode_no")]]['knitting_source']; ?></td>
						<td width="100"
						style="word-break:break-all;"><? echo $roll_details_array[$row[csf("barcode_no")]]['knitting_company']; ?></td>
						<!-- <td width="70" style="word-break:break-all;"><? echo $knitting_source[$row[csf("knitting_source")]]; ?></td>-->
						<td width="70" align="center"><? echo $row[csf("product_id")]; ?></td>
						<td width="50" style="word-break:break-all;"
						align="center"><? echo $body_part[$row[csf("bodypart_id")]]; ?></td>
						<td width="70" style="word-break:break-all;"
						align="center"><? echo $composition_arr[$row[csf('determination_id')]]; ?></td>
						<td width="60" style="word-break:break-all;"
						align="center"><? echo $color_arr[$row[csf("color_id")]]; ?></td>
						<td width="50" align="center"><? echo $row[csf('gsm')]; ?></td>
						<td width="40" align="center"><? echo $row[csf('dia')]; ?></td>

						<td width="50" style="word-break:break-all;"
						align="center"><? echo $fabric_typee[$row[csf('width_type')]]; ?></td>
						<td width="40" align="center"><? echo $row[csf("roll_no")]; ?></td>
						<td width="40" style="word-break:break-all;" align="right"><? echo $grey_qty; ?></td>
						<td width="40" style="word-break:break-all;" align="right"><? echo $row[csf('reject_qnty')]; ?></td>
						<td align="right"><? echo number_format($row[csf('qnty')], 2); ?></td>
					</tr>
					<?
					$tot_qty += $row[csf('qnty')];
					$tot_reject_qty += $row[csf('reject_qnty')];
					$tot_grey_qty += $grey_qty;
					$i++;
				}
				?>
				<tr>
					<td align="right" colspan="17"><strong>Total</strong></td>
					<td align="right"><? echo number_format($tot_grey_qty, 2, '.', ''); ?></td>
					<td align="right"><? echo number_format($tot_reject_qty, 2, '.', ''); ?></td>
					<td align="right"><? echo number_format($tot_qty, 2, '.', ''); ?></td>
				</tr>

			</table>
		</div>
		<? echo signature_table(107, $company, "1210px"); ?>
		<script type="text/javascript" src="../../../js/jquery.js"></script>
		<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
		<script>
			function generateBarcode(valuess) {
            var value = valuess;//$("#barcodeValue").val();
            //alert(value)
            var btype = 'code39';//$("input[name=btype]:checked").val();
            var renderer = 'bmp';// $("input[name=renderer]:checked").val();

            var settings = {
            	output: renderer,
            	bgColor: '#FFFFFF',
            	color: '#000000',
            	barWidth: 1,
            	barHeight: 40,
            	moduleSize: 5,
            	posX: 10,
            	posY: 20,
            	addQuietZone: 1
            };
            //$("#barcode_img_id").html('11');
            value = {code: value, rect: false};

            $("#barcode_img_id").show().barcode(value, btype, settings);
        }
        generateBarcode('<? echo $txt_challan_no; ?>');
    </script>
    <?
    exit();
}

if ($action == "fso_v2_delivery_print") {
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$txt_challan_no = $data[1];
	$update_id = $data[2];
	

	$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name from lib_company");
	foreach ($company_data as $row) {
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
	}

	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$batch_arr = return_library_array("select id,batch_no from  pro_batch_create_mst", 'id', 'batch_no');
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");

	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}


	$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$data[0]'", "image_location");
	?>
	<div style="width:1110px;">
		<table width="1110" cellspacing="0" align="center" border="0">

				<tr>
					<td colspan="2" rowspan="3">
						<img src="../../../<? echo $image_location; ?>" height="70" width="200" style="float:left;">
					</td>
					<td align="center" style="font-size:x-large">
						<strong><? echo $company_array[$company]['name']; ?></strong>
					</td>
					
				</tr>
				<tr>
					<td colspan="2" align="center">
						<?
						//echo $com_dtls[1];
						// echo "SELECT PLOT_NO, LEVEL_NO, ROAD_NO, BLOCK_NO, COUNTRY_ID, PROVINCE, CITY, ZIP_CODE, CONTACT_NO, EMAIL, WEBSITE, VAT_NUMBER FROM LIB_COMPANY WHERE ID='".$data[0]."' AND STATUS_ACTIVE=1 AND IS_DELETED=0";
						$nameArray_com=sql_select( "SELECT PLOT_NO, LEVEL_NO, ROAD_NO, BLOCK_NO, COUNTRY_ID, PROVINCE, CITY, ZIP_CODE, CONTACT_NO, EMAIL, WEBSITE, VAT_NUMBER FROM LIB_COMPANY WHERE ID='".$data[0]."' AND STATUS_ACTIVE=1 AND IS_DELETED=0");
							$loc = '';
							foreach ($nameArray_com as $result)
							{
								if($result['PLOT_NO'] != '')
								{
									$loc .= $result['PLOT_NO'];
								}
								
								if($result['LEVEL_NO'] != '')
								{
									if($loc != '')
									{
										$loc .= ', '.$result['PLOT_NO'];
									}
									else
									{
										$loc .= $result['PLOT_NO'];
									}
								}
								
								if($result['ROAD_NO'] != '')
								{
									if($loc != '')
									{
										$loc .= ', '.$result['ROAD_NO'];
									}
									else
									{
										$loc .= $result['ROAD_NO'];
									}
								}
								
								if($result['BLOCK_NO'] != '')
								{
									if($loc != '')
									{
										$loc .= ', '.$result['BLOCK_NO'];
									}
									else
									{
										$loc .= $result['BLOCK_NO'];
									}
								}
								
								if($result['CITY'] != '')
								{
									if($loc != '')
									{
										$loc .= ', '.$result['CITY'];
									}
									else
									{
										$loc .= $result['CITY'];
									}
								}
							}
							echo $loc;
						?>
					</td>
				</tr>

				<tr>
					<td align="center" colspan="2" style="font-size:16px"><strong><u>Finish Fabric Roll Delivery To Store</u></strong>
					</td>
				</tr>
				<tr>
					<td></td>
					<td align="center" colspan="2" style="font-size:16px"><strong><u>Challan No <? echo $txt_challan_no; ?></u></strong>
					</td>
				</tr>
			</table>
			<br>
			<?
			$sql_data = sql_select("select id, knitting_source, knitting_company, delevery_date from pro_grey_prod_delivery_mst where entry_form=67 and status_active=1 and is_deleted=0 and company_id=$company  and id=$update_id order by id");

			?>


			<table width="1110" cellspacing="0" align="center" border="0">
				<tr>
					<td style="font-size:16px; font-weight:bold;" width="150">Company</td>
					<td width="200">:&nbsp;<? echo $company_array[$company]['name']; ?></td>
					<td style="font-size:16px; font-weight:bold;" width="150">Delivery Date</td>
					<td width="200" align=""><? echo change_date_format($sql_data[0][csf('delevery_date')]); ?></td>
					<td style="font-size:16px; font-weight:bold;" width="150">Prod. Source</td>
					<td width="200">:&nbsp;<? echo $knitting_source[$sql_data[0][csf('knitting_source')]]; ?></td>
					<td width="" id="barcode_img_id" colspan="2"></td>
				</tr>
			</table>
			<br>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1390" class="rpt_table">
				<thead>
					<tr>
						<th width="30">SL</th>
						<th width="100">Barcode No</th>
						<th width="100">Sales Job/<br>Booking No</th>
						<th width="100">Customer Name</th>
						<th width="90">Cust. Buyer</th>
						<th width="60">Batch No</th>
						<th width="70">Prod. Source</th>
						<th width="100">Dye/Finishing Company</th>
						<th width="50">Product Id</th>
						<th width="80">Body Part</th>
						<th width="150">Fabric Type</th>
						<th width="70"> Color</th>
						<th width="50">GSM</th>
						<th width="40">Dia</th>
						<th width="60">Dia/Width Type</th>
						<th width="40">Roll No</th>
						<th width="40">Reject Qty</th>
						<th>QC Pass Qty</th>
					</tr>
				</thead>
				<?

				$data_array = sql_select("SELECT a.id, a.company_id, a.recv_number, a.receive_basis, a.receive_date, a.booking_no, a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id,b.color_id ,b.batch_id, b.fabric_description_id,b.body_part_id,b.dia_width_type, b.gsm, b.width, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.reject_qnty FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=66 and c.entry_form=66 and c.status_active=1 and c.is_deleted=0 and c.barcode_no in (" . $data[4] . ")");

				$roll_details_array = array();
				$barcode_array = array();
				foreach ($data_array as $row) 
				{

					$roll_details_array[$row[csf("barcode_no")]]['knitting_source_id'] = $row[csf("knitting_source")];
					$roll_details_array[$row[csf("barcode_no")]]['knitting_source'] = $knitting_source[$row[csf("knitting_source")]];
					$roll_details_array[$row[csf("barcode_no")]]['knitting_company_id'] = $row[csf("knitting_company")];

					if ($row[csf("knitting_source")] == 1) {
						$roll_details_array[$row[csf("barcode_no")]]['knitting_company'] = $company_array[$row[csf("knitting_company")]]['name'];
					} else if ($row[csf("knitting_source")] == 3) {
						$roll_details_array[$row[csf("barcode_no")]]['knitting_company'] = $supplier_arr[$row[csf("knitting_company")]];
					}

				}
				$i = 1;
				$tot_qty = 0; $tot_reject_qty= 0;
				$receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan");
				
				$sql_update = sql_select("SELECT  b.id,b.grey_sys_number,b.job_no,b.order_id,b.job_no,b.bodypart_id,b.batch_id,b.dia,b.width_type, b.determination_id,b.gsm,b.product_id,b.color_id,c.roll_no,c.barcode_no,b.current_delivery,c.reject_qnty, c.po_breakdown_id, c.booking_without_order, c.booking_no
				from pro_grey_prod_delivery_dtls b, pro_roll_details c
				where  b.id=c.dtls_id and c.barcode_no in (" . $data[4] . ") and  b.entry_form=67 and  c.entry_form=67  and c.mst_id=$update_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by c.roll_no");

				foreach ($sql_update as $row) 
				{
					$order_id_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
					$barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
				}

				$barcode_arr= array_filter($barcode_arr);
				if(!empty($barcode_arr))
				{
					$all_barcode_no=implode(',', $barcode_arr);
					$non_order_sql=" SELECT a.barcode_no, a.po_breakdown_id, b.booking_no, c.style_id, c.style_des,b.buyer_id
					from pro_roll_details a, wo_non_ord_samp_booking_mst b, wo_non_ord_samp_booking_dtls c 
					where  a.po_breakdown_id=b.id and b.booking_no=c.booking_no and a.barcode_no in($all_barcode_no) and a.booking_without_order=1
					and a.status_active = 1  and a.is_deleted=0 and b.status_active = 1  and b.is_deleted=0 and c.status_active = 1  and c.is_deleted=0";//and  a.entry_form=2
					// echo $non_order_sql;
					$non_order_data=sql_select($non_order_sql);
					foreach ($non_order_data as $key => $value) 
					{
						$non_order_array[$value[csf('barcode_no')]]['style_id'] = $value[csf('style_id')];
						$style_id_array[$value[csf('style_id')]] = $value[csf('style_id')];
						$style_des_array[$value[csf('barcode_no')]]['style_des'] = $value[csf('style_des')];
						$non_order_array[$value[csf('barcode_no')]]['buyer_id'] = $value[csf('buyer_id')];
					}
					$style_id_array= array_filter($style_id_array);
					if(!empty($style_id_array))
					{
						$all_style_ids=implode(',', $style_id_array);
						$non_order_style_sql="SELECT id, style_ref_no from SAMPLE_DEVELOPMENT_MST where id in($all_style_ids) and status_active = 1  and is_deleted=0 ";
						$non_order_style_data=sql_select($non_order_style_sql);
						foreach ($non_order_style_data as $key => $value) 
						{
							$style_name_array[$value[csf('id')]] = $value[csf('style_ref_no')];
						}
					}

					// print_r($non_order_array);
				}

				$order_id_arr= array_filter($order_id_arr);
				if(!empty($order_id_arr))
				{
					// echo "SELECT a.buyer_id, b.job_no as job_no_mst,b.booking_no,b.po_break_down_id,c.po_number, c.shipment_date, d.job_no_prefix_num,d.buyer_name,d.style_ref_no
					// from wo_booking_mst a, wo_booking_dtls b,wo_po_break_down c, wo_po_details_master d
					// where a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.booking_type in(1,4) and c.job_no_mst=d.job_no and b.po_break_down_id in (". implode(',', $order_id_arr) .") 
					// group by b.job_no,b.booking_no,a.buyer_id,d.buyer_name, b.po_break_down_id, c.po_number, c.shipment_date, d.job_no_prefix_num, d.buyer_name, d.style_ref_no";
					$sql_job=sql_select("SELECT a.buyer_id, b.job_no as job_no_mst,b.booking_no,b.po_break_down_id,c.po_number, c.shipment_date, d.job_no_prefix_num,d.buyer_name,d.style_ref_no
					from wo_booking_mst a, wo_booking_dtls b,wo_po_break_down c, wo_po_details_master d
					where a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.booking_type in(1,4) and c.job_no_mst=d.job_no and b.po_break_down_id in (". implode(',', $order_id_arr) .") 
					group by b.job_no,b.booking_no,a.buyer_id,d.buyer_name, b.po_break_down_id, c.po_number, c.shipment_date, d.job_no_prefix_num, d.buyer_name, d.style_ref_no");
					$job_sql_result = $sql_job;

					$job_array=array();
					foreach ($job_sql_result as $job_row) 
					{
						$job_array[$job_row[csf('po_break_down_id')]]['job']   = $job_row[csf('job_no_prefix_num')];
						$job_array[$job_row[csf('po_break_down_id')]]['po']    = $job_row[csf('po_number')];
						$job_array[$job_row[csf('po_break_down_id')]]['buyer'] = $job_row[csf('buyer_name')];
						$job_array[$job_row[csf('po_break_down_id')]]['style_ref_no'] = $job_row[csf('style_ref_no')];
					}
					unset($sql_job);
					
					$sql_sales=sql_select("select id,sales_booking_no,buyer_id,customer_buyer from fabric_sales_order_mst where id in (" . implode(",",$order_id_arr) . ") and status_active=1 and is_deleted=0");

					$sales_arr=array();
					foreach ($sql_sales as $sales_row) 
					{
						$sales_arr[$sales_row[csf('id')]]["buyer_id"] 		= $sales_row[csf('buyer_id')];
						$sales_arr[$sales_row[csf('id')]]["customer_buyer"] = $sales_row[csf('customer_buyer')];
					}
					unset($sql_sales);
				}

				foreach ($sql_update as $row) 
				{
					if ($row[csf('booking_without_order')]==0) // order
					{
						$booking_no=$row[csf('booking_no')];
						$buyer_name = $buyer_array[$sales_arr[$row[csf('po_breakdown_id')]]["buyer_id"]];
						$customer_buyer = $buyer_array[$sales_arr[$row[csf('po_breakdown_id')]]["customer_buyer"]];
					}
					else
					{
						$booking_no=$row[csf('booking_no')];

						$buyer_job='';
						$buyer_name = $buyer_array[$non_order_array[$row[csf('barcode_no')]]['buyer_id']];
					}

					?>
					<tr>
						<td width="30"><? echo $i; ?></td>
						<td width="100" style="word-wrap:break-all;"><? echo $row[csf('barcode_no')]; ?></td>
						<td width="100" style="word-wrap:break-all;" align="center"><? echo $booking_no; ?></td>	
											
						<td width="100" style="word-wrap:break-all;" align="center"><? echo $buyer_name; ?></td>						
						<td width="90" style="word-wrap:break-all;" align="center"><? echo $customer_buyer; ?></td>
						<td width="60" style="word-wrap:break-all;" align="center"><? echo $batch_arr[$row[csf('batch_id')]]; ?></td>
						
						<td width="70" style="word-wrap:break-all;"><? echo $roll_details_array[$row[csf("barcode_no")]]['knitting_source']; ?></td>
						<td width="100"
						style="word-wrap:break-all;"><? echo $roll_details_array[$row[csf("barcode_no")]]['knitting_company']; ?></td>
						<!-- <td width="70" style="word-break:break-all;"><? echo $knitting_source[$row[csf("knitting_source")]]; ?></td>-->
						<td width="70" align="center"><? echo $row[csf("product_id")]; ?></td>
						<td width="50" style="word-wrap:break-all;"
						align="center"><? echo $body_part[$row[csf("bodypart_id")]]; ?></td>
						<td width="70" style="word-wrap:break-all;"
						align="center"><? echo $composition_arr[$row[csf('determination_id')]]; ?></td>
						<td width="60" style="word-wrap:break-all;"
						align="center"><? echo $color_arr[$row[csf("color_id")]]; ?></td>
						<td width="50" align="center"><? echo $row[csf('gsm')]; ?></td>
						<td width="40" align="center"><? echo $row[csf('dia')]; ?></td>

						<td width="50" style="word-wrap: break-all;"
						align="center"><? echo $fabric_typee[$row[csf('width_type')]]; ?></td>
						<td width="40" align="center"><? echo $row[csf("roll_no")]; ?></td>
						<td width="40" style="word-wrap:break-all;" align="right"><? echo $row[csf('reject_qnty')]; ?></td>
						<td align="right"><? echo number_format($row[csf('current_delivery')], 2); ?></td>
					</tr>
					<?
					$tot_roll += $row[csf('roll_no')];
					$tot_qty += $row[csf('current_delivery')];
					$tot_reject_qty += $row[csf('reject_qnty')];
					$i++;
				}
				?>
				<tr>
					<td align="right" colspan="16"><strong>Total</strong></td>
					<td align="right"><? echo number_format($tot_reject_qty, 2, '.', ''); ?></td>
					<td align="right"><? echo number_format($tot_qty, 2, '.', ''); ?></td>
				</tr>

			</table>
		</div>
		<? echo signature_table(107, $company, "1210px"); ?>
		<script type="text/javascript" src="../../../js/jquery.js"></script>
		<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
		<script>
			function generateBarcode(valuess) {
            var value = valuess;//$("#barcodeValue").val();
            //alert(value)
            var btype = 'code39';//$("input[name=btype]:checked").val();
            var renderer = 'bmp';// $("input[name=renderer]:checked").val();

            var settings = {
            	output: renderer,
            	bgColor: '#FFFFFF',
            	color: '#000000',
            	barWidth: 1,
            	barHeight: 40,
            	moduleSize: 5,
            	posX: 10,
            	posY: 20,
            	addQuietZone: 1
            };
            //$("#barcode_img_id").html('11');
            value = {code: value, rect: false};

            $("#barcode_img_id").show().barcode(value, btype, settings);
        }
        generateBarcode('<? echo $txt_challan_no; ?>');
    </script>
    <?
    exit();
}


if ($action == "fabric_details_print") {
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$txt_challan_no = $data[1];
	$update_id = $data[2];

	$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name from lib_company");
	foreach ($company_data as $row) {
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
	}
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$batch_arr = return_library_array("select id,batch_no from  pro_batch_create_mst", 'id', 'batch_no');
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$job_array = array();
	$job_sql = "select a.job_no_prefix_num, a.job_no, b.id, b.po_number,a.buyer_name from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$job_sql_result = sql_select($job_sql);
	foreach ($job_sql_result as $row) {
		$job_array[$row[csf('id')]]['job'] = $row[csf('job_no_prefix_num')];
		$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
		$job_array[$row[csf('id')]]['buyer'] = $row[csf('buyer_name')];
	}

	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}

	?>
	<?
	$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$data[0]'", "image_location");
	?>
	<div style="width:1010px;">
		<table width="1010" cellspacing="0" align="center" border="0">
			<tr>
				<td rowspan="2">
					<img src="../../../<? echo $image_location; ?>" height="70" width="200" style="float:left;">
				</td>
				<td align="center" style="font-size:x-large">
					<strong><? echo $company_array[$company]['name']; ?></strong>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:18px">
					<strong><u>Challan No <? echo $txt_challan_no; ?></u></strong>
				</td>
			</tr>
		</table>

		<br>
		<?
		$sql_data = sql_select("select id, knitting_source, knitting_company, delevery_date from pro_grey_prod_delivery_mst where entry_form=67 and status_active=1 and is_deleted=0 and company_id=$company  and id=$update_id order by id");

		?>
		<table width="1010" cellspacing="0" align="center" border="0">
			<tr>
				<td style="font-size:16px; font-weight:bold;" width="150">Company</td>
				<td width="200">:&nbsp;<? echo $company_array[$company]['name']; ?></td>
				<td style="font-size:16px; font-weight:bold;" width="150">Delivery Date</td>
				<td width="200" align=""><? echo change_date_format($sql_data[0][csf('delevery_date')]); ?></td>
				<td style="font-size:16px; font-weight:bold;" width="150">Prod. Source</td>
				<td width="200">:&nbsp;<? echo $knitting_source[$sql_data[0][csf('knitting_source')]]; ?></td>
			</tr>
			<tr>

				<td width="" align="center" id="barcode_img_id" colspan="6"></td>
			</tr>
			<tr>

			</tr>
		</table>

		<br>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1150" class="rpt_table">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="60">Batch No</th>
					<th width="90">Order No</th>
					<th width="70">Buyer <br> Job</th>
					<th width="80">Prod. Source</th>
					<th width="120">Dye/Finishing Company</th>
					<th width="50">Product Id</th>
					<th width="80">Body Part</th>
					<th width="150">Fabric Type</th>
					<th width="70"> Color</th>
					<th width="50">GSM</th>
					<th width="40">Dia</th>
					<th width="70">Dia/Width Type</th>
					<th width="40">No of Roll</th>
					<th width="40">Reject Qty</th>
					<th>QC Pass Qty</th>
				</tr>
			</thead>
			<?

			$data_array = sql_select("SELECT a.id, a.knitting_source, a.knitting_company FROM inv_receive_master a WHERE  a.entry_form=66 and a.status_active=1
				and a.is_deleted=0");
			$roll_details_array = array();
			$barcode_array = array();
			foreach ($data_array as $row) {

				$roll_details_array[$row[csf("id")]]['knitting_source_id'] = $row[csf("knitting_source")];
				$roll_details_array[$row[csf("id")]]['knitting_source'] = $knitting_source[$row[csf("knitting_source")]];
				$roll_details_array[$row[csf("id")]]['knitting_company_id'] = $row[csf("knitting_company")];

				if ($row[csf("knitting_source")] == 1) {
					$roll_details_array[$row[csf("id")]]['knitting_company'] = $company_array[$row[csf("knitting_company")]]['name'];
				} else if ($row[csf("knitting_source")] == 3) {
					$roll_details_array[$row[csf("id")]]['knitting_company'] = $supplier_arr[$row[csf("knitting_company")]];
				}

			}
			$i = 1;
			$tot_qty = 0; $tot_reject_qty = 0;
			$receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan");

			$sql_update = sql_select("select  b.grey_sys_id,b.order_id,b.job_no,b.bodypart_id,b.batch_id,b.dia,b.width_type,
				b.determination_id,b.gsm,b.product_id,b.color_id,count(c.roll_id) as no_of_roll,sum(b.current_delivery) as current_delivery,
				sum(c.reject_qnty) as reject_qnty,c.po_breakdown_id
				from pro_grey_prod_delivery_dtls b,pro_roll_details c
				where  b.roll_id=c.id and b.mst_id=$update_id and  b.entry_form=67 and  b.status_active=1 and b.is_deleted=0 and c.status_active=1
				and c.is_deleted=0
				group by b.grey_sys_id,b.order_id,b.job_no,b.bodypart_id,b.batch_id,b.dia,b.width_type,b.determination_id,b.gsm,b.product_id,b.color_id");
			foreach ($sql_update as $row) {
				?>
				<tr>
					<td width="30"><? echo $i; ?></td>
					<td width="60" style="word-break:break-all;"
					align="center"><? echo $batch_arr[$row[csf('batch_id')]]; ?></td>
					<td width="90" style="word-break:break-all;"
					align="center"><? echo $job_array[$row[csf('po_breakdown_id')]]['po']; ?></td>
					<td width="60" style="word-break:break-all;"
					align="center"><? echo $buyer_array[$job_array[$row[csf('po_breakdown_id')]]['buyer']] . "<br>" . $job_array[$row[csf('po_breakdown_id')]]['job']; ?></td>
					<td width="80"
					style="word-break:break-all;"><? echo $roll_details_array[$row[csf("grey_sys_id")]]['knitting_source']; ?></td>
					<td width="120"
					style="word-break:break-all;"><? echo $roll_details_array[$row[csf("grey_sys_id")]]['knitting_company']; ?></td>
					<td width="70" align="center"><? echo $row[csf("product_id")]; ?></td>
					<td width="50" style="word-break:break-all;"
					align="center"><? echo $body_part[$row[csf("bodypart_id")]]; ?></td>
					<td width="70" style="word-break:break-all;"
					align="center"><? echo $composition_arr[$row[csf('determination_id')]]; ?></td>
					<td width="60" style="word-break:break-all;"
					align="center"><? echo $color_arr[$row[csf("color_id")]]; ?></td>
					<td width="50" align="center"><? echo $row[csf('gsm')]; ?></td>
					<td width="40" align="center"><? echo $row[csf('dia')]; ?></td>
					<td width="50" style="word-break:break-all;"
					align="center"><? echo $fabric_typee[$row[csf('width_type')]]; ?></td>
					<td width="40" align="center"><? echo $row[csf("no_of_roll")]; ?></td>
					<td width="40" style="word-break:break-all;" align="right"><? echo $row[csf('reject_qnty')]; ?></td>
					<td align="right"><? echo number_format($row[csf('current_delivery')], 2); ?></td>
				</tr>
				<?
				$tot_qty += $row[csf('current_delivery')];
				$tot_reject_qty += $row[csf('reject_qnty')];
				$i++;
			}
			?>
			<tr>
				<td align="right" colspan="14"><strong>Total</strong></td>
				<td align="right"><? echo number_format($tot_reject_qty, 2, '.', ''); ?></td>
				<td align="right"><? echo number_format($tot_qty, 2, '.', ''); ?></td>
			</tr>

		</table>
	</div>
	<? echo signature_table(107, $company, "1020px"); ?>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess) {
            var value = valuess;//$("#barcodeValue").val();
            //alert(value)
            var btype = 'code39';//$("input[name=btype]:checked").val();
            var renderer = 'bmp';// $("input[name=renderer]:checked").val();

            var settings = {
            	output: renderer,
            	bgColor: '#FFFFFF',
            	color: '#000000',
            	barWidth: 1,
            	barHeight: 40,
            	moduleSize: 5,
            	posX: 10,
            	posY: 20,
            	addQuietZone: 1
            };
            //$("#barcode_img_id").html('11');
            value = {code: value, rect: false};

            $("#barcode_img_id").show().barcode(value, btype, settings);
        }
        generateBarcode('<? echo $txt_challan_no; ?>');
    </script>

    <?
    exit();
}


if ($action == "issue_challan_print") {
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$txt_issue_no = $data[1];
	$update_id = $data[2];
	$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name from lib_company where id=$company");
	foreach ($company_data as $row) {
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
	}


	$supplier_arr = return_library_array("select id,short_name from lib_supplier", "id", "short_name");
	$dataArray = sql_select("select count(b.id) as total_roll,sum(b.qnty) as total_qty,a.knitting_source, a.knitting_company, a.delevery_date from pro_grey_prod_delivery_mst  a, pro_roll_details b where a.entry_form=67 and a.id=$update_id and  a.id=b.mst_id and b.entry_form=67 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.knitting_source, a.knitting_company, a.delevery_date");

	?>
	<div align="center">
		<table width="350" cellspacing="0">
			<tr>
				<td colspan="2" align="left" id="barcode_img_id"></td>
			</tr>
			<tr>
				<td width="130"><strong>Issue No :</strong></td>
				<td width="200"><? echo $txt_issue_no; ?></td>
			</tr>

			<tr>
				<td><strong>Issue Date:</strong></td>
				<td width="200"><? echo change_date_format($dataArray[0][csf('delevery_date')]); ?></td>
			</tr>
			<tr>
				<td><strong>No of roll:</strong></td>
				<td width="200"><? echo $dataArray[0][csf('total_roll')]; ?></td>
			</tr>
			<tr>
				<td><strong>Total Quantity:</strong></td>
				<td width="200"><? echo $dataArray[0][csf('total_qty')]; ?></td>
			</tr>
			<tr>
				<td><strong>Dyeing Source:</strong></td>
				<td width="200"><? echo $knitting_source[$dataArray[0][csf('knitting_source')]]; ?></td>
			</tr>
			<tr>
				<td width="130"><strong>Dyeing Company:</strong></td>
				<td width="200">
					<?
					if ($dataArray[0][csf('knitting_source')] == 1) echo $company_array[$dataArray[0][csf('knitting_company')]]['name']; else if ($dataArray[0][csf('knitting_source')] == 3) echo $supplier_arr[$dataArray[0][csf('knitting_company')]];
					?>
				</td>
			</tr>

		</table>
	</div>

	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess) {
            var value = valuess;//$("#barcodeValue").val();
            //alert(value)
            var btype = 'code39';//$("input[name=btype]:checked").val();
            var renderer = 'bmp';// $("input[name=renderer]:checked").val();

            var settings = {
            	output: renderer,
            	bgColor: '#FFFFFF',
            	color: '#000000',
            	barWidth: 1,
            	barHeight: 30,
            	moduleSize: 5,
            	posX: 10,
            	posY: 20,
            	addQuietZone: 1
            };
            //$("#barcode_img_id").html('11');
            value = {code: value, rect: false};

            $("#barcode_img_id").show().barcode(value, btype, settings);
        }
        generateBarcode('<? echo $txt_issue_no; ?>');
    </script>
    <?
    exit();
}

//for norban
if ($action == "roll_delivery_no_of_copy_print") // Print 2, created by Tipu
{
	extract($_REQUEST);
	echo load_html_head_contents("Finish Fabric Roll Delivery To Store", "../../../", 1, 1, '', '', '');
	$data = explode('*', $data);

	$company   		= $data[0];
	$system_no 		= $data[1];
	$report_title 	= $data[2];
	$mst_id     	= $data[3];
	$knit_source    = $data[4];
	$no_copy 		= $data[5];
	$dyeing_company = $data[6];

	$floor_name_arr = return_library_array("select id, floor_name from lib_prod_floor", "id", "floor_name");
	//$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$location_arr = return_library_array("select id,location_name from lib_location", "id", "location_name");
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	// $color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$brand_details = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$body_part_type=return_library_array("select id, body_part_type from lib_body_part where status_active=1",'id','body_part_type');
	$composition_arr = array();$yarn_composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row)
	{
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " :: " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . " :: " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}

		if (array_key_exists($row[csf('id')], $yarn_composition_arr)) {
			$yarn_composition_arr[$row[csf('id')]] = $composition[$row[csf('copmposition_id')]];
		} else {
			$yarn_composition_arr[$row[csf('id')]] = $composition[$row[csf('copmposition_id')]];
		}
	}

	$store_location_id=return_field_value("location_id","lib_store_location","id=$store_id and is_deleted=0","location_id");	
	$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
	$color_name_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');	
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$department_arr = return_library_array("select id, department_name from lib_department", "id", "department_name");
	
	$company_info = sql_select("select ID, COMPANY_NAME, PLOT_NO, ROAD_NO, CITY, CONTACT_NO, COUNTRY_ID from lib_company where status_active=1 and is_deleted=0 order by company_name");
	foreach($company_info as $row)
	{
		$company_library[$row['ID']] = $row['COMPANY_NAME'];
	}
	unset($company_info);

	//for supplier
	$sqlSupplier = sql_select("select id as ID, supplier_name as SUPPLIER_NAME, short_name as SHORT_NAME, ADDRESS_1 from lib_supplier where id=$dyeing_company");
	foreach($sqlSupplier as $row)
	{
		$supplier_arr[$row['ID']] = $row['SHORT_NAME'];
		$supplier_dtls_arr[$row['ID']] = $row['SUPPLIER_NAME'];
		$supplier_address_arr[$row['ID']] = $row['ADDRESS_1'];
	}
	unset($sqlSupplier);
	
	//for gate pass
	$sql_get_pass = "SELECT a.ID, a.SYS_NUMBER, a.BASIS, a.COMPANY_ID, a.GET_PASS_NO, a.DEPARTMENT_ID, a.ATTENTION, a.SENT_BY, a.WITHIN_GROUP, a.SENT_TO, a.CHALLAN_NO, a.OUT_DATE, a.TIME_HOUR, a.TIME_MINUTE, a.RETURNABLE, a.DELIVERY_AS, a.EST_RETURN_DATE, a.INSERTED_BY, a.CARRIED_BY, a.LOCATION_ID, a.COM_LOCATION_ID, a.VHICLE_NUMBER, a.LOCATION_NAME, a.REMARKS, a.DO_NO, a.MOBILE_NO, a.ISSUE_ID, a.RETURNABLE_GATE_PASS_REFF, a.DELIVERY_COMPANY, a.ISSUE_PURPOSE,a.DRIVER_NAME,a.DRIVER_LICENSE_NO,a.SECURITY_LOCK_NO, b.QUANTITY, b.NO_OF_BAGS FROM inv_gate_pass_mst a, INV_GATE_PASS_DTLS b WHERE a.id = b.mst_id AND a.company_id = ".$company." AND a.basis = 11 AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND a.challan_no LIKE '".$system_no."%'";
	// echo $sql_get_pass;
	$sql_get_pass_rslt = sql_select($sql_get_pass);
	$is_gate_pass = 0;
	$is_gate_out = 0;
	$gate_pass_id = '';
	$gatePassDataArr = array();
	foreach($sql_get_pass_rslt as $row)
	{
		$exp = explode(',', $row['CHALLAN_NO']);
		// echo "<pre>"; print_r($exp);
		foreach($exp as $key=>$val)
		{
			if($val == $system_no)
			{
				$is_gate_pass = 1;
				$gate_pass_id = $row['ID'];
				
				$row['OUT_DATE'] = ($row['OUT_DATE']!=''?date('d-m-Y', strtotime($row['OUT_DATE'])):'');
				$row['EST_RETURN_DATE'] = ($row['EST_RETURN_DATE']!=''?date('d-m-Y', strtotime($row['EST_RETURN_DATE'])):'');
				$row['EST_RETURN_DATE'] = ($row['EST_RETURN_DATE']!=''?date('d-m-Y', strtotime($row['EST_RETURN_DATE'])):'');
				
				if($row['WITHIN_GROUP'] == 1)
				{
					//$row['SENT_TO'] = ($row['BASIS']==50?$buyer_dtls_arr[$row['SENT_TO']]:$supplier_dtls_arr[$row['SENT_TO']]);
					$row['SENT_TO'] = $company_library[$row['SENT_TO']];
					$row['LOCATION_NAME'] = $location_arr[$row['LOCATION_ID']];
				}
				
				//for gate pass info
				$gatePassDataArr[$val]['gate_pass_id'] = $row['SYS_NUMBER'];
				$gatePassDataArr[$val]['from_company'] = $company_library[$row['COMPANY_ID']];
				$gatePassDataArr[$val]['from_location'] =$location_arr[ $row['COM_LOCATION_ID']];
				$gatePassDataArr[$val]['gate_pass_date'] = date('d-m-Y', strtotime($row['OUT_DATE']));
				$gatePassDataArr[$val]['returnable'] = $yes_no[$row['RETURNABLE']];
				$gatePassDataArr[$val]['est_return_date'] = $row['EST_RETURN_DATE'];
				
				$gatePassDataArr[$val]['to_company'] = $row['SENT_TO'];
				$gatePassDataArr[$val]['to_location'] = $row['LOCATION_NAME'];
				$gatePassDataArr[$val]['delivery_kg'] += $row['QUANTITY'];
				$gatePassDataArr[$val]['delivery_bag'] += $row['NO_OF_BAGS'];
				
				$gatePassDataArr[$val]['department'] = $department_arr[$row['DEPARTMENT_ID']];
				$gatePassDataArr[$val]['attention'] = $row['ATTENTION'];
				$gatePassDataArr[$val]['issue_purpose'] = $row['ISSUE_PURPOSE'];
				$gatePassDataArr[$val]['remarks'] = $row['REMARKS'];
				$gatePassDataArr[$val]['carried_by'] = $row['CARRIED_BY'];
				$gatePassDataArr[$val]['vhicle_number'] = $row['VHICLE_NUMBER'];
				$gatePassDataArr[$val]['mobile_no'] = $row['MOBILE_NO'];
				$gatePassDataArr[$val]['driver_name'] = $row['DRIVER_NAME'];
				$gatePassDataArr[$val]['driver_license_no'] = $row['DRIVER_LICENSE_NO'];
				$gatePassDataArr[$val]['security_lock_no'] = $row['SECURITY_LOCK_NO'];
			}
		}
	}
	// echo "<pre>";print_r($gatePassDataArr);

	//for gate out
	if($gate_pass_id != '')
	{
		$sql_gate_out="SELECT OUT_DATE, OUT_TIME FROM INV_GATE_OUT_SCAN WHERE STATUS_ACTIVE = 1 AND IS_DELETED = 0 AND INV_GATE_PASS_MST_ID='".$gate_pass_id."'";
		$sql_gate_out_rslt = sql_select($sql_gate_out);
		if(!empty($sql_gate_out_rslt))
		{
			foreach($sql_gate_out_rslt as $row)
			{
				$is_gate_out = 1;
				$gatePassDataArr[$system_no]['out_date'] = date('d-m-Y', strtotime($row['OUT_DATE']));
				$gatePassDataArr[$system_no]['out_time'] = $row['OUT_TIME'];
			}
		}
	}

	//for delivery purpose
	$sqlIssue = "SELECT A.SYS_NUMBER, A.DELEVERY_DATE, A.COMPANY_ID, A.KNITTING_COMPANY, A.KNITTING_SOURCE, A.LOCATION_ID, A.REMARKS, A.ATTENTION
	, b.current_delivery AS CURRENT_DELIVERY, b.PRODUCT_ID AS PROD_ID, c.roll_no AS ROLL_NO, c.roll_id AS ROLL_ID, c.barcode_no AS BARCODE_NO, c.po_breakdown_id AS PO_BREAKDOWN_ID 
	FROM pro_grey_prod_delivery_mst a INNER JOIN pro_grey_prod_delivery_dtls b ON a.id = b.mst_id INNER JOIN pro_roll_details c ON b.id = c.dtls_id 
	WHERE a.id = ".$mst_id." AND c.entry_form=67 AND b.status_active=1 AND b.is_deleted=0 AND c.status_active=1 AND c.is_deleted=0 ORDER BY c.roll_no";	
	// echo $sqlIssue;die; //  and c.barcode_no=21020001547
	$rsltIssue = sql_select($sqlIssue);
	$poBreakdownIdArr = array();
	$barcodeNoArr = array();
	$productIdArr = array();
	foreach($rsltIssue as $row)
	{
		$poBreakdownIdArr[$row['PO_BREAKDOWN_ID']] = $row['PO_BREAKDOWN_ID'];
		$barcodeNoArr[$row['BARCODE_NO']] = $row['BARCODE_NO'];
		$productIdArr[$row['PROD_ID']] = $row['PROD_ID'];

		$challan_number = $row['SYS_NUMBER'];
		$issue_date = $row['DELEVERY_DATE'];
		$knit_dye_source = $row['KNITTING_SOURCE'];
		$location = $location_arr[$row['LOCATION_ID']];
		$attention = $row['ATTENTION'];
		$remarks = $row['REMARKS'];
		
		//for issue to
		$knit_dye_company = '';
		if ($row['KNITTING_SOURCE'] == 1)
			$knit_dye_company = $company_library[$row['KNITTING_COMPANY']];
		else
			$knit_dye_company = $supplier_dtls_arr[$row['KNITTING_COMPANY']];

		$barcode_nums .= $row["BARCODE_NO"].",";
	}
	$barcode_nums = chop($barcode_nums,",");

	//for order details
	$poNoArr=array();
	$sqlPo="SELECT a.buyer_name AS BUYER_NAME, b.id AS ID, b.po_number AS PO_NUMBER, b.grouping AS GROUPING, b.file_no AS FILE_NO, a.job_no as JOB_NO, a.style_ref_no as STYLE_REF_NO FROM wo_po_details_master a, wo_po_break_down b where a.job_no = b.job_no_mst AND a.status_active = 1 AND a.is_deleted = 0 ".where_con_using_array($poBreakdownIdArr, '0', 'b.id');
	$rsltPo=sql_select($sqlPo);
	$buyerIdArr = array();
	foreach($rsltPo as $row)
	{
		$buyerIdArr[$row['BUYER_NAME']] = $row['BUYER_NAME'];
		$poNoArr[$row['ID']]['po_number'] = $row['PO_NUMBER'];
		$poNoArr[$row['ID']]['buyer_name'] = $row['BUYER_NAME'];
		$poNoArr[$row['ID']]['internal_reference'] = $row['GROUPING'];
		$poNoArr[$row['ID']]['file_no'] = $row['FILE_NO'];
		$poNoArr[$row['ID']]['job_no'] = $row['JOB_NO'];
		$poNoArr[$row['ID']]['job_no'] = $row['JOB_NO'];
		$poNoArr[$row['ID']]['style_ref_no'] = $row['STYLE_REF_NO'];
	}

	//for detarmination
	$product_array=array();
	$detarminationIdArr = array();
	$product_sql = sql_select("SELECT id AS ID, detarmination_id AS DETARMINATION_ID, gsm AS GSM, dia_width AS DIA_WIDTH, unit_of_measure AS UNIT_OF_MEASURE FROM product_details_master WHERE item_category_id=2 ".where_con_using_array($productIdArr, '0', 'id'));
	foreach($product_sql as $row)
	{
		$detarminationIdArr[$row['DETARMINATION_ID']]=$row['DETARMINATION_ID'];
		$product_array[$row['ID']]['deter_id']=$row['DETARMINATION_ID'];
	}
	//echo "<pre>"; print_r($product_array);

	//for roll details
	$sqlRcv = "SELECT a.id AS ID, a.entry_form AS ENTRY_FORM, a.recv_number AS RECV_NUMBER, a.company_id AS COMPANY_ID, a.receive_basis AS RECEIVE_BASIS, a.booking_no AS BOOKING_NO, a.booking_id AS BOOKING_ID, a.knitting_source AS KNITTING_SOURCE, a.knitting_company AS KNITTING_COMPANY, a.buyer_id AS BUYER_ID, b.id AS DTLS_ID, b.prod_id AS PROD_ID, b.body_part_id AS BODY_PART_ID, b.trans_id AS TRANS_ID, b.fabric_description_id AS FABRIC_DESCRIPTION_ID, b.gsm AS GSM, b.width AS WIDTH, b.batch_id AS BATCH_ID, b.color_id AS COLOR_ID, c.barcode_no AS BARCODE_NO, b.dia_width_type AS DIA_WIDTH_TYPE, c.id AS ROLL_ID, c.roll_no AS ROLL_NO, c.po_breakdown_id AS PO_BREAKDOWN_ID, c.qnty AS QNTY, C.REJECT_QNTY 
	FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c 
	WHERE a.id=b.mst_id AND b.id=c.dtls_id AND a.entry_form IN(66) AND c.entry_form IN(66) AND c.status_active=1 AND c.is_deleted=0 ".where_con_using_array($barcodeNoArr, '0', 'c.barcode_no');
	// AND b.trans_id<>0 // AND a.entry_form IN(37,7,68,66)
	//echo $sqlRcv;
	$data_array=sql_select($sqlRcv);
	$colorIdArr = array();
	$supplierIdArr = array();
	$batchIdArr = array();
	foreach($data_array as $row)
	{
		$colorIdArr[$row['COLOR_ID']] = $row['COLOR_ID'];
		$supplierIdArr[$row['KNITTING_COMPANY']] = $row['KNITTING_COMPANY'];
		$batchIdArr[$row['BATCH_ID']] = $row['BATCH_ID'];
		$barcodeDeterRef[$row['BARCODE_NO']] = $row['FABRIC_DESCRIPTION_ID'];
		$detarminationIdArr[$row['FABRIC_DESCRIPTION_ID']]=$row['FABRIC_DESCRIPTION_ID'];
	}

	//for composition
	$composition_arr=array();
	$sql_deter="SELECT a.id AS ID, a.construction AS CONSTRUCTION, b.copmposition_id AS COMPOSITION_ID, b.percent AS PERCENT FROM lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b WHERE a.id = b.mst_id ".where_con_using_array($detarminationIdArr, '0', 'a.id');
	$deter_array=sql_select($sql_deter);
	foreach( $deter_array as $row )
	{
		if(array_key_exists($row['ID'],$composition_arr))
		{
			$composition_arr[$row['ID']]=$composition_arr[$row['ID']]." ".$composition[$row['COMPOSITION_ID']]." ".$row['PERCENT']."%";
		}
		else
		{
			$composition_arr[$row['ID']]=$row['CONSTRUCTION'].", ".$composition[$row['COMPOSITION_ID']]." ".$row['PERCENT']."%";
		}
	}
	//echo "<pre>"; print_r($composition_arr);

	//for color details
	$color_arr = return_library_array("select id, color_name from lib_color where 1=1 ".where_con_using_array($colorIdArr,'0','id'),'id','color_name');

	//for batch details	
	$batch_arr=array();
	$batch_sql = sql_select("SELECT ID, BATCH_NO, BOOKING_NO, COLOR_RANGE_ID FROM pro_batch_create_mst WHERE 1=1 ".where_con_using_array($batchIdArr, '0', 'id'));
	foreach($batch_sql as $row)
	{
		$batch_arr[$row['ID']]['batch_no']=$row['BATCH_NO'];
		$batch_arr[$row['ID']]['booking_no']=$row['BOOKING_NO'];
		$batch_arr[$row['ID']]['color_range_id']=$row['COLOR_RANGE_ID'];
	}
	// echo "<pre>";print_r($batch_arr);die;
	
	$roll_details_array=array();
	$barcode_array=array(); 
	foreach($data_array as $row)
	{
		/*if($row['ENTRY_FORM'] != 66 && $row['TRANS_ID'] != 0)
		{
			$roll_details_array[$row['BARCODE_NO']]['body_part_id']=$row['BODY_PART_ID'];
			$roll_details_array[$row['BARCODE_NO']]['color_id']=$row['COLOR_ID'];
			$roll_details_array[$row['BARCODE_NO']]['roll_no']=$row['ROLL_NO'];
			$roll_details_array[$row['BARCODE_NO']]['qnty']=number_format($row['QNTY'],2,'.','');
			$roll_details_array[$row['BARCODE_NO']]['batch_id']=$row['BATCH_ID'];
			$roll_details_array[$row['BARCODE_NO']]['gsm']=$row['GSM'];
			$roll_details_array[$row['BARCODE_NO']]['width']=$row['WIDTH'];
			$roll_details_array[$row['BARCODE_NO']]['reject_qnty']=$row['REJECT_QNTY'];
		}
		else
		{
			$roll_details_array[$row['BARCODE_NO']]['r_gsm']=$row['GSM'];
			$roll_details_array[$row['BARCODE_NO']]['r_dia']=$row['WIDTH'];
		}*/
		$roll_details_array[$row['BARCODE_NO']]['body_part_id']=$row['BODY_PART_ID'];
		$roll_details_array[$row['BARCODE_NO']]['color_id']=$row['COLOR_ID'];
		$roll_details_array[$row['BARCODE_NO']]['roll_no']=$row['ROLL_NO'];
		$roll_details_array[$row['BARCODE_NO']]['qnty']=number_format($row['QNTY'],2,'.','');
		$roll_details_array[$row['BARCODE_NO']]['batch_id']=$row['BATCH_ID'];
		$roll_details_array[$row['BARCODE_NO']]['gsm']=$row['GSM'];
		$roll_details_array[$row['BARCODE_NO']]['width']=$row['WIDTH'];
		$roll_details_array[$row['BARCODE_NO']]['reject_qnty']=$row['REJECT_QNTY'];
	}

	// Kniting production
	$production_sql = "SELECT A.BUYER_ID,A.RECEIVE_BASIS, A.BOOKING_NO, A.KNITTING_SOURCE, A.KNITTING_COMPANY, A.LOCATION_ID, B.FEBRIC_DESCRIPTION_ID, B.GSM, B.WIDTH, B.YARN_COUNT, B.YARN_LOT, B.COLOR_ID, B.COLOR_RANGE_ID, B.STITCH_LENGTH, B.BRAND_ID, B.MACHINE_DIA, B.MACHINE_GG, C.BOOKING_NO AS BWO, C.BOOKING_WITHOUT_ORDER,C.IS_SALES, B.BODY_PART_ID, B.PROD_ID, D.DETARMINATION_ID, C.BARCODE_NO, sum(c.qc_pass_qnty_pcs) as ISSUE_QTY_PCS
	from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, product_details_master d
	where a.id=b.mst_id and b.id=c.dtls_id and b.prod_id=d.id and a.entry_form=2 and c.entry_form=2 and a.status_active = 1 and a.is_deleted=0 and b.status_active = 1 and b.is_deleted=0 and c.status_active = 1 and c.is_deleted = 0 and c.barcode_no in ($barcode_nums)
	group by  A.BUYER_ID,A.RECEIVE_BASIS, A.BOOKING_NO, A.KNITTING_SOURCE, A.KNITTING_COMPANY, A.LOCATION_ID, B.FEBRIC_DESCRIPTION_ID, B.GSM, B.WIDTH, B.YARN_COUNT, B.YARN_LOT, B.COLOR_ID, B.COLOR_RANGE_ID, B.STITCH_LENGTH, B.BRAND_ID, B.MACHINE_DIA, B.MACHINE_GG, C.BOOKING_NO, C.BOOKING_WITHOUT_ORDER, C.IS_SALES, B.BODY_PART_ID, B.PROD_ID, D.DETARMINATION_ID, C.BARCODE_NO
	ORDER BY A.BOOKING_NO";
	// echo $production_sql;die;
	$production_data=sql_select($production_sql);
	$production_roll_array=array();
	foreach($production_data as $row)
	{
		$production_roll_array[$row['BARCODE_NO']]['stitch_length']=$row['STITCH_LENGTH'];
		$production_roll_array[$row['BARCODE_NO']]['yarn_count']=$row['YARN_COUNT'];
		$production_roll_array[$row['BARCODE_NO']]['yarn_lot']=$row['YARN_LOT'];
		$production_roll_array[$row['BARCODE_NO']]['brand_id']=$row['BRAND_ID'];
		$production_roll_array[$row['BARCODE_NO']]['machine_dia']=$row['MACHINE_DIA'];
		$production_roll_array[$row['BARCODE_NO']]['machine_gg']=$row['MACHINE_GG'];
		$production_roll_array[$row['BARCODE_NO']]['issue_qty_pcs']=$row['ISSUE_QTY_PCS'];
	}

	//report data
	$rptDataArr = array();
	$issue_qnty_array = array();
	foreach($rsltIssue as $row)
	{
		$composition = $barcodeDeterRef[$row['BARCODE_NO']];
		$gsm = $roll_details_array[$row['BARCODE_NO']]['gsm'];
		$dia = $roll_details_array[$row['BARCODE_NO']]['width'].'<br>';
		$batch_id = $roll_details_array[$row['BARCODE_NO']]['batch_id'];
		$body_part_id=$roll_details_array[$row['BARCODE_NO']]['body_part_id'];
		$job_no = $poNoArr[$row['PO_BREAKDOWN_ID']]['po_number'];
		$booking_no = $batch_arr[$batch_id]['booking_no'];
		$color_range_id = $batch_arr[$batch_id]['color_range_id'];
		// echo $booking_no.'<br>';

		$booking_no_arr=explode('-', $booking_no);
		// echo $booking_no_arr[1].'<br>';
		if ($booking_no_arr[1]=='SMN') 
		{
			$smn_booking_no_arr[$booking_no]=$booking_no;
		}
		else
		{
			$order_booking_no_arr[$booking_no]=$booking_no;
		}	

		$rptDataArr[$booking_no][$batch_id][$composition][$body_part_id][$color_range_id][$gsm][$dia]['buyer_id'] = $poNoArr[$row['PO_BREAKDOWN_ID']]['buyer_name'];
		$rptDataArr[$booking_no][$batch_id][$composition][$body_part_id][$color_range_id][$gsm][$dia]['job_no'] = $poNoArr[$row['PO_BREAKDOWN_ID']]['job_no'];
		$rptDataArr[$booking_no][$batch_id][$composition][$body_part_id][$color_range_id][$gsm][$dia]['style_ref_no'] = $poNoArr[$row['PO_BREAKDOWN_ID']]['style_ref_no'];

		$rptDataArr[$booking_no][$batch_id][$composition][$body_part_id][$color_range_id][$gsm][$dia]['color_id'] = $roll_details_array[$row['BARCODE_NO']]['color_id'];
		$rptDataArr[$booking_no][$batch_id][$composition][$body_part_id][$color_range_id][$gsm][$dia]['num_of_roll'] += count($row['BARCODE_NO']);
		$rptDataArr[$booking_no][$batch_id][$composition][$body_part_id][$color_range_id][$gsm][$dia]['issue_qnty'] += $row['CURRENT_DELIVERY'];
		$rptDataArr[$booking_no][$batch_id][$composition][$body_part_id][$color_range_id][$gsm][$dia]['reject_qnty'] += $roll_details_array[$row['BARCODE_NO']]['reject_qnty'];
		$rptDataArr[$booking_no][$batch_id][$composition][$body_part_id][$color_range_id][$gsm][$dia]['issue_qty_pcs'] += $production_roll_array[$row['BARCODE_NO']]['issue_qty_pcs'];
		
		$rptDataArr[$booking_no][$batch_id][$composition][$body_part_id][$color_range_id][$gsm][$dia]['r_gsm'] = $roll_details_array[$row['BARCODE_NO']]['r_gsm'];
		$rptDataArr[$booking_no][$batch_id][$composition][$body_part_id][$color_range_id][$gsm][$dia]['r_dia'] = $roll_details_array[$row['BARCODE_NO']]['r_dia'];	
		
		$rptDataArr[$booking_no][$batch_id][$composition][$body_part_id][$color_range_id][$gsm][$dia]['stitch_length'] = $production_roll_array[$row['BARCODE_NO']]['stitch_length'];
		$rptDataArr[$booking_no][$batch_id][$composition][$body_part_id][$color_range_id][$gsm][$dia]['yarn_count'] = $production_roll_array[$row['BARCODE_NO']]['yarn_count'];
		$rptDataArr[$booking_no][$batch_id][$composition][$body_part_id][$color_range_id][$gsm][$dia]['yarn_lot'] = $production_roll_array[$row['BARCODE_NO']]['yarn_lot'];
		$rptDataArr[$booking_no][$batch_id][$composition][$body_part_id][$color_range_id][$gsm][$dia]['brand_id'] = $production_roll_array[$row['BARCODE_NO']]['brand_id'];
		$rptDataArr[$booking_no][$batch_id][$composition][$body_part_id][$color_range_id][$gsm][$dia]['machine_dia'] = $production_roll_array[$row['BARCODE_NO']]['machine_dia'];
		$rptDataArr[$booking_no][$batch_id][$composition][$body_part_id][$color_range_id][$gsm][$dia]['machine_gg'] = $production_roll_array[$row['BARCODE_NO']]['machine_gg'];
	}
	// echo "<pre>"; print_r($order_booking_no_arr);die;
	$smn_booking_no = "'" . implode("','", $smn_booking_no_arr) . "'";
	$order_booking_no = "'" . implode("','", $order_booking_no_arr) . "'";

	$booking_details = sql_select("SELECT a.booking_no, a.buyer_id, b.job_no, b.po_break_down_id, c.grouping as ref_no,d.style_ref_no,d.sustainability_standard,d.fab_material 
	from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c,wo_po_details_master d 
	where a.booking_no=b.booking_no and b.po_break_down_id=c.id and c.job_no_mst=d.job_no and a.status_active=1 and b.status_active=1 and a.booking_no in($order_booking_no)
	group by a.booking_no,a.buyer_id,b.job_no,b.po_break_down_id,c.grouping,d.style_ref_no,d.sustainability_standard,d.fab_material");
    foreach ($booking_details as $booking_row)
    {
		$booking_arr[$booking_row[csf("booking_no")]]["job_no"] = $booking_row[csf("job_no")];
		$booking_arr[$booking_row[csf("booking_no")]]["buyer_id"] = $booking_row[csf("buyer_id")];
		$booking_arr[$booking_row[csf("booking_no")]]["po_break_down_id"] = $booking_row[csf("po_break_down_id")];
		$booking_arr[$booking_row[csf("po_break_down_id")]]["booking_no"] = $booking_row[csf("booking_no")];
		$booking_arr[$booking_row[csf("po_break_down_id")]]["ref_no"] = $booking_row[csf("ref_no")];
		$booking_arr[$booking_row[csf("booking_no")]]["booking_ref_no"] = $booking_row[csf("ref_no")];
		$booking_arr[$booking_row[csf("booking_no")]]["style_ref_no"] = $booking_row[csf("style_ref_no")];
		$booking_arr[$booking_row[csf("booking_no")]]["internal_ref_no"] = $booking_row[csf("ref_no")];
		$booking_arr[$booking_row[csf("booking_no")]]["sustainability_standard"] = $booking_row[csf("sustainability_standard")];
		$booking_arr[$booking_row[csf("booking_no")]]["fab_material"] = $booking_row[csf("fab_material")];
    }

    // Non Order Booking
    $bookings_without_order=chop($bookings_without_order,',');
	$non_order_booking_sql= sql_select("SELECT a.booking_no,a.buyer_id,a.grouping, b.style_id 
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
	where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and a.booking_no in($smn_booking_no) group by  a.booking_no,a.buyer_id,a.grouping, b.style_id");
	foreach ($non_order_booking_sql as $row)
	{
	 	$style_id=$row[csf("style_id")];
	 	$nonOrderBookingData_arr[$row[csf('booking_no')]]['buyer_id']=$row[csf('buyer_id')];
		$nonOrderBookingData_arr[$row[csf('booking_no')]]['sustainability_std_id']=return_field_value("sustainability_std_id", "sample_development_mst", "id=$style_id");
	 	$nonOrderBookingData_arr[$row[csf('booking_no')]]['fabric_material_id']=return_field_value("fabric_material_id", "sample_development_mst", "id=$style_id");
	 	$nonOrderBookingData_arr[$row[csf('booking_no')]]['style_id']=return_field_value("style_ref_no", "sample_development_mst", "id=$style_id");
	}
	// echo "<pre>";print_r($nonOrderBookingData_arr);die;
    // $nonOrderBookingStyle=return_field_value("style_ref_no", "sample_development_mst", "id=$style_id");

    $colarCupArr= sql_select("SELECT id, body_part_type, body_part_full_name from  lib_body_part where status_active=1 and  is_deleted=0 and body_part_type in(40,50)");
	foreach($colarCupArr as $row)
	{
		$body_part_data_arr[$row[csf('id')]]['body_part_full_name']=$row[csf('body_part_full_name')];
		$body_part_data_arr[$row[csf('id')]]['body_part_type']=$row[csf('body_part_type')];
	}

	// For Coller and Cuff data
	$sql_coller_cuff = "SELECT a.receive_basis, a.booking_no, b.body_part_id, c.booking_no as bwo, c.coller_cuff_size, c.qnty, c.qc_pass_qnty_pcs, c.barcode_no
	from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
	where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active = 1 and c.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 and c.barcode_no in ($barcode_nums) order by b.body_part_id, c.coller_cuff_size";
	// echo $sql_coller_cuff;
	$sql_coller_cuff_result = sql_select($sql_coller_cuff);
	foreach ($sql_coller_cuff_result as $row2)
	{
		if($body_part_data_arr[$row2[csf('body_part_id')]]['body_part_type']>0 && $row2[csf('qc_pass_qnty_pcs')]>0)
		{
			$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['qnty'] += $row2[csf('qnty')];
			$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['qc_pass_qnty_pcs'] += $row2[csf('qc_pass_qnty_pcs')];
			$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['receive_basis'] = $row2[csf('receive_basis')];
			$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['no_of_roll'] += count($row2[csf('barcode_no')]);
		}
	}
	//echo "<pre>"; print_r($coller_data_arr);//die;

	$com_dtls = fnc_company_location_address($company, $store_location_id, 2);
	?>
	<style type="text/css">
		table tr td {
			font-size: 16px;
		}
		.rpt_table thead th{
			font-size: 16px;
		}
		.rpt_table tfoot th{
			font-size: 16px;
		}
	</style>
    <?php
	$data_array = sql_select("select image_location  from common_photo_library where master_tble_id='".$data[0]."' and form_name='company_details' and is_deleted=0 and file_type=1");

	$noOfCopy = "";
	for ($x = 1; $x <= $no_copy; $x++)
	{
		if($x==1)
		{
			$sup = 'st';
		}
		else if($x==2)
		{
			$sup = 'nd';
		}
		else if($x==3)
		{
			$sup = 'rd';
		}
		else
		{
			$sup = 'th';
		}
		
		$noOfCopy ="<span style='font-size:x-large;font-weight:bold'>".$x."<sup>".$sup."</sup> Copy</span>";
		?>
    
		<div style="width:1240px;">
			<table width="1240" cellspacing="0" align="right" border="0" style="margin-right:-10px;">
				<tr>
					<td align="left" width="50">
						<?
						foreach ($data_array as $img_row)
						{
							?>
							<img src='../../../<? echo $com_dtls[2]; ?>' height='50' width='50' align="middle"/>
							<?
						}
						?>
					</td>
                    <td align="center" style="font-size:30px" colspan="3"><strong><? echo $com_dtls[0]."<br><span style=\"font-size:14px;\">".$com_dtls[1]."</span>"; ?></strong></td>
					<td width="110" align="right"><?php echo $noOfCopy.($is_gate_pass==1?"<br><span style=\"color:#F00;font-weight:bold;\">Gate Pass Done</span>":'').($is_gate_out==1?"<br><span style=\"color:#F00;font-weight:bold;\">Gate Out Done</span>":''); ?></td>
				</tr>
				<tr>
					<td colspan="3" align="center" height="50" valign="middle" style="font-size:25px">
						<strong>Finish Fabric Final Inspection Delivery Challan</strong>
						<?php
						if ($data[4] == 1)
						{
							?>
							<!-- <span style="color:#0F0; font-weight:bold;">[Approved]</span> -->
							<?php
						}
						?>
					</td>
				</tr>
			</table>
			<div style="width:100%;">
				<div style="clear:both;">
		            <table style="margin-right:-40px;" cellspacing="0" width="1260" border="1" rules="all" class="rpt_table">
						<tr>
							<td width="125"><strong>Company:</strong></td>
							<td width="250px"><? echo $company_library[$company]; ?></td>
							<td width="125"><strong>Attention:</strong></td>
							<td width="150px"><? echo $attention; ?></td>
							<td width="130"><strong>Challan No:</strong></td>
							<td width="130"><? echo $challan_number; ?></td>					
						</tr>
						<tr>
							<td><strong>Dye/Finishing Company:</strong></td>
							<td><? echo $knit_dye_company; ?></td>
							<td><strong>Dye/Finishing Location:</strong></td>
							<td><? echo $location; ?></td>
							<td><strong>Challan Date:</strong></td>
							<td><? echo change_date_format($issue_date); ?></td>                
						</tr>
						<tr>
							<td><strong>Remarks:</strong></td>
							<td colspan="5"><? echo $remarks; ?></td>
						</tr>
						<tr>
							<td align="center" colspan="6" id="barcode_img_id_<?php echo $x; ?>" height="50" valign="middle" style="border-left:hidden;border-right:hidden;border-bottom:hidden;"></td>
						</tr>
					</table>
			
					<table style="margin-right:-40px;" cellspacing="0" width="1260" border="1" rules="all" class="rpt_table">
						<thead bgcolor="#dddddd">
							<tr>
								<th rowspan="2" width="20">SL</th>
								<th rowspan="2" width="120">Buyer, Job, <br>Style and<br>Booking</th>
								<th rowspan="2" width="120">Batch Number::Color</th>
								<th rowspan="2" width="60">Body Part</th>
								<th rowspan="2" width="210">Fabric Details</th>
								<th rowspan="2" width="65">Color Range</th>
								<th rowspan="2" width="180">Yarn Details</th>
								<th rowspan="2" width="60">Fab. Dia<br>& GSM</th>
								<th rowspan="2" width="50">MC DIA <br/> X <br/> M.GAUGE</th>
								<th rowspan="2" width="60">S.L</th>
								<th colspan="2" width="120">Delivery Qty</th>
								<th rowspan="2" width="80">Roll Qty</th>
								<th rowspan="2">Reject Qty</th>
							</tr>
							<tr>
								<th width="60">KG</th>
								<th width="60">PCS</th>
							</tr>
						</thead>
                        <tbody>
							<?
							$i=1;$k=0;	
							$grand_tot_qty_fabric=$grand_tot_issue_qty_pcs=$grand_tot_num_of_roll=$grand_tot_reject_qnty=0;	
							ksort($rptDataArr);					
							foreach($rptDataArr as $booking=>$bookingArr)
							{
								$job_tot_qty_fabric=$job_tot_issue_qty_pcs=$job_tot_num_of_roll=$job_tot_reject_qnty=0;
								foreach($bookingArr as $batchId=>$batchArr)
								{
									$batch_tot_qty_fabric=$batch_tot_issue_qty_pcs=$batch_tot_num_of_roll=$batch_tot_reject_qnty=0;
									foreach($batchArr as $compositionId=>$compositionArr)
									{
										$fab_tot_issue_qnty=$fab_tot_issue_qty_pcs=$fab_tot_num_of_roll=$fab_tot_reject_qnty=0;
										foreach($compositionArr as $body_part_ids=>$body_partArr)
										{
											foreach($body_partArr as $color_range_id=>$color_rangeArr)
											{
												foreach($color_rangeArr as $gsm=>$gsmArr)
												{
													foreach($gsmArr as $dia=>$row)
													{
														if ($i % 2 == 0)
															$bgcolor = "#E9F3FF";
														else
															$bgcolor = "#FFFFFF";
														$fab_material=array(1=>"Organic",2=>"BCI");
														$booking_no_arr=explode('-', $booking);
														$style=$buyer='';
														if ($booking_no_arr[1]=='SMN') 
														{
															$buyer=$nonOrderBookingData_arr[$booking]['buyer_id'];
															$style=$nonOrderBookingData_arr[$booking]['style_id'];
															$sustainability = $sustainability_standard[$nonOrderBookingData_arr[$booking]["sustainability_std_id"]];
															$material = $fab_material[$nonOrderBookingData_arr[$booking]["fabric_material_id"]];
														}
														else
														{
															$buyer=$row['buyer_id'];
															$style=$row['style_ref_no'];
															$sustainability = $sustainability_standard[$booking_arr[$booking]["sustainability_standard"]]; 
															$material = $fab_material[$booking_arr[$booking]["fab_material"]]; 
														}
														
														?>
							                            <tr bgcolor="<? echo $bgcolor; ?>">
							                                <td style="font-size: 15px"><? echo $i; ?></td>
							                                <td style="font-size: 15px">
							                                    <div style="word-wrap:break-word; width:130px"><? 
							                                    echo $buyer_array[$buyer].' ::<br>'.$row['job_no'].' ::<br>'.$style.' ::<br>'.$booking.' ::<br>'.$sustainability.' ::'.$material; ?>
							                                    </div>
							                                </td>
							                                <td style="font-size: 15px">
							                                    <div style="word-wrap:break-word; width:130px"><? 
							                                    echo $batch_arr[$batchId]['batch_no'].' ::<br>'.$color_arr[$row['color_id']]; ?>
							                                    </div>
							                                </td>
							                                <td style="font-size: 15px">
							                                    <div style="word-wrap:break-word; width:60px"><? echo $body_part[$body_part_ids]; ?></div>
							                                </td>
							                                <td style="font-size: 15px" title="<? echo $row['febric_description_id']; ?>">
							                                    <div style="word-wrap:break-word; width:210px">
							                                        <?
																	$color_id_arr = array_unique(explode(",", $row['color_id']));
																	$all_color_name = "";
																	foreach ($color_id_arr as $c_id) {
																		$all_color_name .= $color_arr[$c_id] . ",";
																	}
																	$all_color_name = chop($all_color_name, ",");
																	echo $all_color_name.' :: '.$composition_arr[$compositionId]; ?>
							                                    </div>
							                                </td>
							                                <td style="font-size: 15px">
							                                    <div style="word-wrap:break-word; width:65px"><? echo $color_range[$color_range_id]; ?></div>
							                                </td>
							                                <td style="font-size: 15px" title="Yarn Dtls:<? echo $compositionId; ?>">
							                                    <div style="word-wrap:break-word; width:180">
							                                        <? 
							                                        $yarn_count = explode(",", $row['yarn_count']);
																	$ppl_count_id="";
																	foreach ($yarn_count as $count_id) {
																		if ($ppl_count_id == '') $ppl_count_id = $yarn_count_details[$count_id]; else $ppl_count_id .= "," . $yarn_count_details[$count_id];
																	}
							                                        echo $ppl_count_id.', '.$yarn_composition_arr[$compositionId].', '.$row['yarn_lot'].', '.$brand_details[$row['brand_id']]; ?>
							                                    </div>
							                                </td>
							                                <td style="font-size: 15px; text-align: center;">
							                                    <div style="word-wrap:break-word; width:60px">
							                                        <? echo $dia.' & '.$gsm; ?>
							                                    </div>
							                                </td>
							                                <td style="font-size: 15px">
							                                    <div style="word-wrap:break-word; width:65px;text-align: center;"><? echo $row['machine_dia'].'X'.$row['machine_gg']; ?></div>
							                                </td>
							                                <td style="font-size: 15px; text-align: center;">
							                                    <div style="word-wrap:break-word; width:60px"><? echo $row['stitch_length']; ?></div>
							                                </td>
							                                <td style="font-size: 15px" align="right" style="font-size: 15px;"><? echo number_format($row['issue_qnty'], 2, '.', ''); ?></td>
							                                <td style="font-size: 15px" align="right" style="font-size: 15px;"><? 
							                                	if ($row['issue_qty_pcs']=="") 
							                                	{echo 0;} 
							                                	else{echo $row['issue_qty_pcs'];} ?>		
							                                </td>
							                                <td style="font-size: 15px" align="right"><? echo $row['num_of_roll']; ?></td>
							                                <td style="font-size: 15px" align="right">
							                                    <div style="word-wrap:break-word; width:60px"><? echo number_format($row['reject_qnty'], 2, '.', ''); ?></div>
							                                </td>
							                            </tr>
														<?
														$i++;
														$fab_tot_issue_qnty+=$row['issue_qnty'];
														$fab_tot_issue_qty_pcs+=$row['issue_qty_pcs'];
														$fab_tot_num_of_roll+=$row['num_of_roll'];
														$fab_tot_reject_qnty+=$row['reject_qnty'];

														$batch_tot_qty_fabric+=$row['issue_qnty'];
														$batch_tot_issue_qty_pcs+=$row['issue_qty_pcs'];
														$batch_tot_num_of_roll+=$row['num_of_roll'];
														$batch_tot_reject_qnty+=$row['reject_qnty'];

														$job_tot_qty_fabric+=$row['issue_qnty'];
														$job_tot_issue_qty_pcs+=$row['issue_qty_pcs'];
														$job_tot_num_of_roll+=$row['num_of_roll'];
														$job_tot_reject_qnty+=$row['reject_qnty'];

														$grand_tot_qty_fabric+=$row['issue_qnty'];
														$grand_tot_issue_qty_pcs+=$row['issue_qty_pcs'];
														$grand_tot_num_of_roll+=$row['num_of_roll'];
														$grand_tot_reject_qnty+=$row['reject_qnty'];
													}
												}
											}
										}
										?>
										<tr class="tbl_bottom">
											<td colspan="10" style=" text-align:right;font-size: 14px;"><strong>Fabric Type Total</strong></td>
											<td align="right" style="font-size: 14px;">
												<b><? echo number_format($fab_tot_issue_qnty, 2, '.', ''); ?></b>
											</td>
											<td align="right" style="font-size: 14px;"><? echo $fab_tot_issue_qty_pcs; ?></td>
											<td align="right" style="font-size: 14px;"><? echo number_format($fab_tot_num_of_roll, 2, '.', ''); ?></td>
											<td align="right" style="font-size: 14px;"><? echo number_format($fab_tot_reject_qnty, 2, '.', ''); ?></td>
										</tr>
										<?
									}
									?>
									<tr class="tbl_bottom">
										<td colspan="10" style=" text-align:right;font-size: 14px;"><strong>Batch Total</strong></td>
										<td align="right" style="font-weight: bold;font-size: 14px;"><? echo number_format($batch_tot_qty_fabric,2); ?></td>
										<td align="right" style="font-weight: bold;font-size: 14px;"><? echo $batch_tot_issue_qty_pcs; ?></td>
										<td align="right" style="font-weight: bold;font-size: 14px;"><? echo number_format($batch_tot_num_of_roll,2); ?></td>
										<td align="right" style="font-weight: bold;font-size: 14px;"><? echo number_format($batch_tot_reject_qnty,2); ?></td>
									</tr>
									<?
								}
								$job_total=$k++;
								?>
								<tr class="tbl_bottom">
									<td colspan="10" style=" text-align:right;font-size: 14px;"><strong>Job Total</strong></td>
									<td align="right" style="font-weight: bold;font-size: 14px;"><? echo number_format($job_tot_qty_fabric,2); ?></td>
									<td align="right" style="font-weight: bold;font-size: 14px;"><? echo $job_tot_issue_qty_pcs; ?></td>
									<td align="right" style="font-weight: bold;font-size: 14px;"><? echo number_format($job_tot_num_of_roll,2); ?></td>
									<td align="right" style="font-weight: bold;font-size: 14px;"><? echo number_format($job_tot_reject_qnty,2); ?></td>
								</tr>
								<?
							}
							?>
							<tr class="tbl_bottom">
								<td style="font-size: 16px;" colspan="2" align="center"><b>Total Job: 
	                            <?php echo " ". $job_total += 1; ?></b></td>
								<td align="right" style="font-size: 16px;" colspan="8"><strong>Grand Total</strong></td>
								<td align="center" style="font-weight: bold; font-size: 16px;"><? echo number_format($grand_tot_qty_fabric, 2, '.', ''); ?></td>
								<td align="right" style="font-size: 16px;"><strong><? echo $grand_tot_issue_qty_pcs; ?></strong></td>
								<td align="right" style="font-size: 16px;"><strong><? echo number_format($grand_tot_num_of_roll, 2, '.', ''); ?></strong></td>
								<td align="right" style="font-size: 16px;"><strong><? echo number_format($grand_tot_reject_qnty, 2, '.', ''); ?></strong></td>
							</tr>
	                    </tbody>	
                    </table>
                    <br>
                    <!-- =========== Collar and Cuff Details Start ============= -->
                    <?
			    	//echo '<pre>';print_r($coller_cuff_data_arr);
					$CoCu=1;
					foreach($coller_cuff_data_arr as $coll_cuff_id => $booking_data_arr)
					{
						if( count($booking_data_arr)>0)
						{
						    ?>
			                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="35%" class="rpt_table" style="float:left; margin-bottom:10px;">
			                	<thead bgcolor="#dddddd">
				                    <tr>
				                        <th colspan="3"><? echo $body_part_data_arr[$coll_cuff_id]['body_part_full_name'];?> Details</th>
				                    </tr>
				                    <tr>
				                        <th>Size</th>
				                        <th>Qty Pcs</th>
				                        <th>No. of Roll</th>
				                    </tr>
			                	</thead>
			                    <?
			                    $coller_cuff_qty_total=$coller_cuff_roll_total=0;
			                    foreach($booking_data_arr as $bookingId => $bookingData )
			                    {
			                        foreach($bookingData as $jobId => $jobData )
			                        {
			                            foreach($jobData as $size => $row )
			                            {
			                                ?>
			                                <tr>
			                                    <td align="center"><? echo $size;?></td>
			                                    <td align="center"><? echo $row['qc_pass_qnty_pcs'];?></td>
			                                    <td align="center"><? echo $row['no_of_roll'];?></td>
			                                </tr>
			                                <?
			                                $coller_cuff_qty_total += $row['qc_pass_qnty_pcs'];
			                                $coller_cuff_roll_total += $row['no_of_roll'];
			                            }
			                        }
			                    }
			                    ?>
			                    <tr>
			                        <td align="right"><b>Total</b></td>
			                        <td align="center"><b><? echo $coller_cuff_qty_total; ?></b></td>
			                        <td align="center"><b><? echo $coller_cuff_roll_total; ?></b></td>
			                    </tr>
			                </table>
						    <?
							if($CoCu==1){
								echo "<table width=\"30%\" style=\"float:left\"><tr><td colspan=\"3\">&nbsp;</td></tr></table>";
							}
							$CoCu++;
						}
					}
					?>
					<!-- =========== Collar and Cuff Details End ============= -->
					
                    <!-- ============= Gate Pass Info Start ========= -->
					<table style="margin-right:-40px;" cellspacing="0" width="1260" border="1" rules="all" class="rpt_table">
                        <tr>
                        	<td colspan="15" height="30" style="border-left:hidden;border-right:hidden; text-align: center;">For mishandling or other reason no claim is acceptable in any stage, once the Goods is received in good condition and quality and out from factory premises.</td>
                        </tr>
                        <tr>
                        	<td colspan="4" align="center" valign="middle" style="font-size:25px;"><strong>&lt;&lt;Gate Pass&gt;&gt;</strong></td>
                            <td colspan="9" align="center" valign="middle" id="gate_pass_barcode_img_id_<?php echo $x; ?>" height="50"></td>
                        </tr>
                        <tr>
                        	<td colspan="2" title="<? echo $system_no; ?>"><strong>From Company:</strong></td>
                        	<td colspan="2" width="120"><?php echo $gatePassDataArr[$system_no]['from_company']; ?></td>

                        	<td colspan="2"><strong>To Company:</strong></td>
                        	<td colspan="3" width="120"><?php echo $gatePassDataArr[$system_no]['to_company']; ?></td>

                        	<td colspan="3"><strong>Carried By:</strong></td>
                        	<td colspan="3" width="120"><?php echo $gatePassDataArr[$system_no]['carried_by']; ?></td>
                        </tr>						
                        <tr>
                        	<td colspan="2"><strong>From Location:</strong></td>
                        	<td colspan="2"><?php echo $gatePassDataArr[$system_no]['from_location']; ?></td>
                        	<td colspan="2"><strong>To Location:</strong></td>
                        	<td colspan="3"><?php echo $gatePassDataArr[$system_no]['to_location']; ?></td>
                        	<td colspan="3"><strong>Driver Name:</strong></td>
                        	<td colspan="3"><?php echo $gatePassDataArr[$system_no]['driver_name']; ?></td>
                        </tr>
                        <tr>
                        	<td colspan="2"><strong>Gate Pass ID:</strong></td>
                        	<td colspan="2"><?php echo $gatePassDataArr[$system_no]['gate_pass_id']; ?></td>
                        	<td colspan="2" rowspan="2"><strong>Delivery Qnty</strong></td>
                        	<td align="center"><strong>Kg</strong></td>
                        	<td align="center"><strong>Roll</td>
                        	<td align="center"><strong>PCS</td>
                        	<td colspan="3"><strong>Vehicle Number:</strong></td>
                        	<td colspan="3"><?php echo $gatePassDataArr[$system_no]['vhicle_number']; ?></td>
                        </tr>						
                        <tr>
                        	<td colspan="2"><strong>Gate Pass Date:</strong></td>
                        	<td colspan="2"><?php echo $gatePassDataArr[$system_no]['gate_pass_date']; ?></td>
                        	<td align="center"><?php echo $gatePassDataArr[$system_no]['delivery_kg']; ?></td>
                        	<td align="center"><?php echo $gatePassDataArr[$system_no]['delivery_bag']; ?></td>
                        	<td align="center"><?php 
                        	if ($gatePassDataArr[$system_no]['gate_pass_id'] !="") 
                        	{
                        		if ($grand_tot_issue_qty_pcs>0) {
                        		 	echo $grand_tot_issue_qty_pcs;
                        		 } 
                        	}
                        	?></td>
                        	<td colspan="3"><strong>Driver License No.:</strong></td>
                        	<td colspan="3"><?php echo $gatePassDataArr[$system_no]['driver_license_no']; ?></td>
                        </tr>						
                        <tr>
                        	<td colspan="2"><strong>Out Date:</strong></td>
                        	<td colspan="2"><?php echo $gatePassDataArr[$system_no]['out_date']; ?></td>
                        	<td colspan="2"><strong>Dept. Name:</strong></td>
                        	<td colspan="3"><?php echo $gatePassDataArr[$system_no]['department']; ?></td>
                        	<td colspan="3"><strong>Mobile No.:</strong></td>
                        	<td colspan="3"><?php echo $gatePassDataArr[$system_no]['mobile_no']; ?></td>
                        </tr>						
                        <tr>
                        	<td colspan="2"><strong>Out Time:</strong></td>
                        	<td colspan="2"><?php echo $gatePassDataArr[$system_no]['out_time']; ?></td>
                        	<td colspan="2"><strong>Attention:</strong></td>
                        	<td colspan="3"><?php echo $gatePassDataArr[$system_no]['attention']; ?></td>
                        	<td colspan="3"><strong>Sequrity Lock No.:</strong></td>
                        	<td colspan="3"><?php echo $gatePassDataArr[$system_no]['security_lock_no']; ?></td>
                        </tr>						
                        <tr>
                        	<td colspan="2"><strong>Returnable:</strong></td>
                        	<td colspan="2"><?php echo $gatePassDataArr[$system_no]['returnable']; ?></td>
                        	<td colspan="2"><strong>Purpose:</strong></td>
                        	<td colspan="9"><?php echo $gatePassDataArr[$system_no]['issue_purpose']; ?></td>
                        </tr>						
                        <tr>
                        	<td colspan="2"><strong>Est. Return Date:</strong></td>
                        	<td colspan="2"><?php echo $gatePassDataArr[$system_no]['est_return_date']; ?></td>
                        	<td colspan="2"><strong>Remarks:</strong></td>
                        	<td colspan="9"><?php echo $gatePassDataArr[$system_no]['remarks']; ?></td>
                        </tr>
                    </table>
                    <!-- ============= Gate Pass Info End =========== -->
				</div>
				<br>
				<? echo signature_table(21, $company, "1200px"); ?>
			</div>
		</div>
		<script type="text/javascript" src="../../../js/jquery.js"></script>
        <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
		<script>
			function generateBarcode(valuess)
			{
				var zs = '<?php echo $x; ?>';
				var value = valuess;//$("#barcodeValue").val();
				var btype = 'code39';//$("input[name=btype]:checked").val();
				var renderer = 'bmp';// $("input[name=renderer]:checked").val();
				var settings = {
					output: renderer,
					bgColor: '#FFFFFF',
					color: '#000000',
					barWidth: 1,
					barHeight: 30,
					moduleSize: 5,
					posX: 10,
					posY: 20,
					addQuietZone: 1
				};
				$("#barcode_img_id_"+zs).html('11');
				value = {code: value, rect: false};
				$("#barcode_img_id_"+zs).show().barcode(value, btype, settings);
			}
			generateBarcode('<? echo $data[1]; ?>');
			
			//for gate pass barcode
			function generateBarcodeGatePass(valuess)
			{
				var zs = '<?php echo $x; ?>';
				var value = valuess;//$("#barcodeValue").val();
				var btype = 'code39';//$("input[name=btype]:checked").val();
				var renderer = 'bmp';// $("input[name=renderer]:checked").val();
				var settings = {
					output: renderer,
					bgColor: '#FFFFFF',
					color: '#000000',
					barWidth: 1,
					barHeight: 30,
					moduleSize: 5,
					posX: 10,
					posY: 20,
					addQuietZone: 1
				};
				$("#gate_pass_barcode_img_id_"+zs).html('11');
				value = {code: value, rect: false};
				$("#gate_pass_barcode_img_id_"+zs).show().barcode(value, btype, settings);
			}
			
			if('<? echo $gatePassDataArr[$system_no]['gate_pass_id']; ?>' != '')
			{
				generateBarcodeGatePass('<? echo strtoupper($gatePassDataArr[$system_no]['gate_pass_id']); ?>');
			}
		</script>
        <div style="page-break-after:always;"></div>
    	<?php
	}
    exit();
}



if ($action == "roll_delivery_print_4") {
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$txt_challan_no = $data[1];
	$update_id = $data[2];
	$barcode = $data[4];

	// var_dump($data);
	// die();

	$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name from lib_company");
	foreach ($company_data as $row) {
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
	}

	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	// $batch_arr = return_library_array("select id,batch_no from  pro_batch_create_mst", 'id', 'batch_no');
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name"); 

	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');

	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}

	// $sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	// 		$data_array = sql_select($sql_deter);
	// 		foreach ($data_array as $row) {
	// 			$constructtion_arr[$row[csf('id')]] = $row[csf('construction')];
	// 			$composition_arr[$row[csf('id')]] .= $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "% ";
	// 		}
			
	?>
	<div style="width:1270;">
		<table width="1250" cellspacing="0" align="center" border="0">

			<tr>
				
				<td align="center" style="font-size:x-large">
					<strong><? echo $company_array[$company]['name']; ?></strong></td>
				</tr>

				<tr>
					<td align="center" style="font-size:16px"><strong><u>Finish Fabric Roll Delivery To Store</u></strong>
					</td>
				</tr>
				<tr>
					<td align="center" style="font-size:18px"><strong><u>Challan No <? echo $txt_challan_no; ?></u></strong>
					</td>
				</tr>
			</table>
			<br>
			<?

			$sql_data = sql_select("select a.id, to_char(a.insert_date,'YYYY') as year,  a.company_id, a.knitting_source, a.knitting_company,
			a.delevery_date, c.batch_no, a.attention, a.remarks,  c.color_id, f.customer_buyer,  g.gsm_weight, f.sales_booking_no, g.dia, c.batch_weight
			from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b, pro_batch_create_mst c , pro_roll_details d,fabric_sales_order_mst f, fabric_sales_order_dtls g
			where a.id=b.mst_id and b.batch_id=c.id and a.id=d.mst_id and b.id=d.dtls_id and f.id=d.po_breakdown_id and d.is_sales=1 and g.mst_id=f.id and a.entry_form=67 and g.color_id=c.color_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 
			and f.status_active=1 and f.is_deleted=0 and g.status_active=1 and g.is_deleted=0 
			and a.company_id=$company  and a.id=$update_id
			group by a.id, to_char(a.insert_date,'YYYY'), a.company_id, a.knitting_source, a.knitting_company, 
		   a.delevery_date, c.batch_no, a.attention, a.remarks, c.color_id, f.customer_buyer , gsm_weight, f.sales_booking_no, g.dia,
		    c.batch_weight order by a.id");



		//    $sql = "select a.barcode_no, a.roll_length , a.fabric_shade , a.fabric_grade, a.roll_no, a.roll_weight, a.reject_qnty , a.comments , b.product_id, b.bodypart_id,
		//    b.gsm , b.dia, e.style_ref_no  , f.fabric_description_id, d.qc_pass_qnty
		//   from pro_qc_result_mst a, pro_grey_prod_delivery_dtls b , pro_grey_prod_delivery_mst c , pro_roll_details d, fabric_sales_order_mst e, pro_finish_fabric_rcv_dtls f
		//   where b.barcode_num=a.barcode_no and c.id=b.mst_id and c.id=d.mst_id and b.id=d.dtls_id and e.id=d.po_breakdown_id and f.id=a.pro_dtls_id
		//   and d.is_sales=1 and b.barcode_num in ($barcode) and b.mst_id=$update_id and a.entry_form=267
		//   and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 and c.is_deleted = 0 and c.status_active=1
		//   and d.is_deleted = 0 and d.status_active=1 and e.is_deleted = 0 and e.status_active=1 and f.is_deleted = 0 and f.status_active=1 order by a.barcode_no"; 
		
		// $sql= " SELECT a.barcode_no, a.roll_length, a.fabric_shade, a.fabric_grade, a.roll_no,  a.roll_weight, a.reject_qnty, a.comments,  b.product_id, b.bodypart_id, b.gsm, b.dia, e.style_ref_no, f.fabric_description_id, d.qc_pass_qnty, SUM(g.batch_qnty) AS batch_qnty
		// FROM pro_qc_result_mst a JOIN pro_grey_prod_delivery_dtls b ON b.barcode_num = a.barcode_no JOIN pro_grey_prod_delivery_mst c ON c.id = b.mst_id JOIN pro_roll_details d ON c.id = d.mst_id AND b.id = d.dtls_id JOIN  fabric_sales_order_mst e ON e.id = d.po_breakdown_id
		// JOIN pro_finish_fabric_rcv_dtls f ON f.id = a.pro_dtls_id JOIN pro_batch_create_dtls g ON g.mst_id = b.batch_id
		// WHERE  b.barcode_num IN ($barcode) AND b.mst_id = $update_id AND a.entry_form = 267 AND a.is_deleted = 0 AND a.status_active = 1 AND b.is_deleted = 0 AND b.status_active = 1 AND c.is_deleted = 0 AND c.status_active = 1 AND d.is_deleted = 0 AND d.status_active = 1 AND e.is_deleted = 0 AND e.status_active = 1 AND f.is_deleted = 0 AND f.status_active = 1
		// GROUP BY a.barcode_no, a.roll_length, a.fabric_shade, a.fabric_grade,  a.roll_no, a.roll_weight, a.reject_qnty, a.comments, b.product_id, b.bodypart_id, b.gsm, b.dia, e.style_ref_no, f.fabric_description_id, d.qc_pass_qnty ORDER BY a.barcode_no";
		// // UNION ALL
		// $result= sql_select($sql);
		// if(empty($result)){
		// $sql= " SELECT b.barcode_num AS barcode_no, NULL AS roll_length, NULL AS fabric_shade, NULL AS fabric_grade, NULL AS roll_no, d.qnty AS roll_weight,  d.reject_qnty, NULL AS comments,  b.product_id, b.bodypart_id, b.gsm, b.dia, e.style_ref_no, f.fabric_description_id, d.qc_pass_qnty, SUM(g.batch_qnty) AS batch_qnty
		// FROM  pro_grey_prod_delivery_dtls b JOIN pro_grey_prod_delivery_mst c ON c.id = b.mst_id JOIN pro_roll_details d ON c.id = d.mst_id AND b.id = d.dtls_id JOIN fabric_sales_order_mst e ON e.id = d.po_breakdown_id JOIN  pro_finish_fabric_rcv_dtls f ON f.barcode_no = b.barcode_num JOIN pro_batch_create_dtls g ON g.mst_id = b.batch_id WHERE b.barcode_num IN ($barcode)  AND b.mst_id = $update_id AND b.is_deleted = 0 AND b.status_active = 1 AND c.is_deleted = 0 AND c.status_active = 1 AND d.is_deleted = 0 AND d.status_active = 1  AND e.is_deleted = 0 AND e.status_active = 1 AND f.is_deleted = 0 AND f.status_active = 1
		// GROUP BY b.barcode_num, NULL, NULL, NULL,  NULL, d.qnty, d.reject_qnty, NULL, b.product_id, b.bodypart_id,  b.gsm,  b.dia, e.style_ref_no, f.fabric_description_id, d.qc_pass_qnty ORDER BY b.barcode_num";
		// //   echo $sql;
		//   $result= sql_select($sql);
		// }

		$sql= "SELECT a.barcode_no, a.roll_length, a.fabric_shade,  a.fabric_grade, d.roll_no, a.roll_weight, a.reject_qnty,  a.comments,  b.product_id,  b.bodypart_id, b.gsm,  b.dia, e.style_ref_no, f.fabric_description_id, d.qc_pass_qnty, g.batch_qnty AS batch_qnty
	  FROM pro_qc_result_mst a JOIN  pro_grey_prod_delivery_dtls b ON b.barcode_num = a.barcode_no JOIN pro_grey_prod_delivery_mst c ON c.id = b.mst_id JOIN pro_roll_details d ON c.id = d.mst_id AND b.id = d.dtls_id JOIN fabric_sales_order_mst e ON e.id = d.po_breakdown_id JOIN pro_finish_fabric_rcv_dtls f ON f.id = a.pro_dtls_id JOIN pro_batch_create_dtls g ON g.mst_id = b.batch_id AND b.barcode_num= g.barcode_no
	  WHERE b.barcode_num IN ($barcode) AND b.mst_id = $update_id  AND a.entry_form = 267 AND a.is_deleted = 0 AND a.status_active = 1 AND b.is_deleted = 0 AND b.status_active = 1  AND c.is_deleted = 0 AND c.status_active = 1 AND d.is_deleted = 0 AND d.status_active = 1  AND e.is_deleted = 0 AND e.status_active = 1 AND f.is_deleted = 0 AND f.status_active = 1
	  UNION ALL
	  SELECT b.barcode_num AS barcode_no, NULL AS roll_length, NULL AS fabric_shade, NULL AS fabric_grade, d.roll_no, d.qnty AS roll_weight, d.reject_qnty, NULL AS comments, b.product_id, b.bodypart_id, b.gsm, b.dia, e.style_ref_no, f.fabric_description_id, d.qc_pass_qnty, g.batch_qnty AS batch_qnty FROM  pro_grey_prod_delivery_dtls b JOIN  pro_grey_prod_delivery_mst c ON c.id = b.mst_id
	  JOIN  pro_roll_details d ON c.id = d.mst_id AND b.id = d.dtls_id JOIN fabric_sales_order_mst e ON e.id = d.po_breakdown_id
	  JOIN pro_finish_fabric_rcv_dtls f ON f.barcode_no = b.barcode_num JOIN pro_batch_create_dtls g ON g.mst_id = b.batch_id AND g.barcode_no = f.barcode_no 
	  WHERE b.barcode_num IN ($barcode) AND b.mst_id = $update_id AND b.is_deleted = 0 AND b.status_active = 1  AND c.is_deleted = 0  AND c.status_active = 1 AND d.is_deleted = 0  AND d.status_active = 1 AND e.is_deleted = 0 AND e.status_active = 1  AND f.is_deleted = 0  AND f.status_active = 1 ORDER BY barcode_no";
		// echo $sql;
		$result= sql_select($sql);
		$result_arr=array();
		foreach($result as $row){
			$result_arr[$row[csf('barcode_no')]]['barcode_no']= $row[csf('barcode_no')];
			if($row[csf('roll_length')]!="")
			$result_arr[$row[csf('barcode_no')]]['roll_length']= $row[csf('roll_length')];
			if($row[csf('fabric_shade')]!=null)
			$result_arr[$row[csf('barcode_no')]]['fabric_shade']= $row[csf('fabric_shade')];
			if($row[csf('fabric_grade')]!="")
			$result_arr[$row[csf('barcode_no')]]['fabric_grade']= $row[csf('fabric_grade')];
			$result_arr[$row[csf('barcode_no')]]['roll_no']= $row[csf('roll_no')];
			$result_arr[$row[csf('barcode_no')]]['roll_weight']= $row[csf('roll_weight')];
			$result_arr[$row[csf('barcode_no')]]['reject_qnty']= $row[csf('reject_qnty')];
			if($row[csf('comments')]!="")
			$result_arr[$row[csf('barcode_no')]]['comments']= $row[csf('comments')];
			$result_arr[$row[csf('barcode_no')]]['product_id']= $row[csf('product_id')];
			$result_arr[$row[csf('barcode_no')]]['bodypart_id']= $row[csf('bodypart_id')];
			$result_arr[$row[csf('barcode_no')]]['gsm']= $row[csf('gsm')];
			$result_arr[$row[csf('barcode_no')]]['dia']= $row[csf('dia')];
			$result_arr[$row[csf('barcode_no')]]['style_ref_no']= $row[csf('style_ref_no')];
			$result_arr[$row[csf('barcode_no')]]['fabric_description_id']= $row[csf('fabric_description_id')];
			$result_arr[$row[csf('barcode_no')]]['qc_pass_qnty']= $row[csf('qc_pass_qnty')];
			$result_arr[$row[csf('barcode_no')]]['batch_qnty']= $row[csf('batch_qnty')];
		}
			?>


			<table width="1250" cellspacing="0" align="center" border="0">
				<tr>
					<td style="font-size:16px; font-weight:bold;" width="150">Company</td>
					<td width="200">:&nbsp;<? echo $company_array[$company]['name']; ?></td>
					<td style="font-size:16px; font-weight:bold;" width="100">Delivery Date</td>
					<td width="300" align="">:&nbsp; <? echo change_date_format($sql_data[0][csf('delevery_date')]); ?></td>
					<td style="font-size:16px; font-weight:bold;" width="120">Prod. Source</td>
					<td width="150">:&nbsp;<? echo $knitting_source[$sql_data[0][csf('knitting_source')]]; ?></td>
					<td style="font-size:16px; font-weight:bold;" width="100">Batch Qty</td>
					<td width="150">:&nbsp;<? echo $sql_data[0][csf('batch_weight')]; ?></td> 
				</tr>
				<tr>
					<td style="font-size:16px; font-weight:bold;" width="150">Cust. Buyer Name</td>
					<td width="200">:&nbsp;<? echo $buyer_arr[$sql_data[0][csf('customer_buyer')]]; ?></td> 
					<td style="font-size:16px; font-weight:bold;" width="110">Batch No</td>
					<td width="300">:&nbsp;<? echo $sql_data[0][csf('batch_no')] ; ?></td>
					<td style="font-size:16px; font-weight:bold;" width="120">Required GSM</td>
					<td width="150">:&nbsp;<? echo $sql_data[0][csf('gsm_weight')]; ?></td>
					<td style="font-size:16px; font-weight:bold;" width="100">Batch Color</td>
					<td width="150">:&nbsp; <? echo $color_arr[$sql_data[0][csf('color_id')]]; ?></td>
				</tr>
				<tr>
					<td style="font-size:16px; font-weight:bold;" width="150">Booking No</td>
					<td width="200">:&nbsp;<? echo $sql_data[0][csf('sales_booking_no')]; ?></td> 
					<td style="font-size:16px; font-weight:bold;" width="110">Remarks</td>
					<td width="300">:&nbsp;<? echo $sql_data[0][csf('remarks')]; ?></td>
					<td style="font-size:16px; font-weight:bold;" width="120">Required Dia</td>
					<td width="150">:&nbsp;<? echo $sql_data[0][csf('dia')]; ?></td>
				</tr>
				
			</table>
			<br>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1250" class="rpt_table">
				<thead>
					<tr>
						<th width="30">SL</th>
						<th width="100">Barcode No</th>
						<th width="120">Style</th>
						<th width="100">Product Id</th>
						<th width="100">Body Part</th>
						<th width="150">Fabric Type</th>
						<th width="50">Actual GSM</th>
						<th width="50">Actual Dia</th>
						<th width="70">Length of Roll/Yds</th>
						<th width="50">Roll No</th>
						<th width="70">Grey Qty</th>
						<th width="50">Reject Qty</th>
						<th width="70">QC Pass Qty</th>
						<th width="50">Fabric Shade</th>
						<th width="40">Fabric Grade</th>
						<th >Remarks</th>
					</tr>
				</thead>

					<?
					$total_qc_pass=0;
					$i=1;
					foreach($result_arr as $key => $row){ 
						$total_grey += $row['batch_qnty'];
						if($row['reject_qnty']!=0){
							$total_reje += $row['reject_qnty'];
						}
						else{
							$total_reje +=$row['batch_qnty'] -$row['qc_pass_qnty'];
						}
						$total_qc_pass += $row['qc_pass_qnty']; 

						$data_array = sql_select("SELECT  b.body_part_id, b.febric_description_id, b.machine_no_id, b.gsm, b.width, b.color_id, b.yarn_lot, b.yarn_count FROM  pro_finish_fabric_rcv_dtls b WHERE b.id=" . $row['pro_dtls_id']);
						?>

					<tr>
						<td width="30"><? echo $i++; ?></td>
						<td width="100" style="word-break:break-all;" align="center"><? echo $row['barcode_no']; ?></td>
						<td width="120" style="word-break:break-all;" align="center"><? echo $row['style_ref_no']; ?></td>
						<td width="100" style="word-break:break-all;" align="center"><? echo $row['product_id']; ?></td>						
						<td width="100" style="word-break:break-all;" align="center"><?  echo $body_part[$row['bodypart_id']]; ?></td>						
						<td width="150" style="word-break:break-all;" align="center"><? echo  $composition_arr[$row['fabric_description_id']];?></td>
						<td width="50" style="word-break:break-all;" align="center"><? echo $row['gsm']; ?></td>
						<td width="50" style="word-break:break-all;" align="center"><? echo $row['dia']; ?></td>
						<td width="70" style="word-break:break-all;" align="center"><?  echo $row['roll_length']; ?></td>
						<td width="50" align="center"><? echo $row['roll_no']; ?></td>
						<td width="70" style="word-break:break-all;" align="center"><? echo $row['batch_qnty'];?></td>
						<td width="50" style="word-break:break-all;" align="center"><? 
						if($row['reject_qnty']!=0){
						echo $row['reject_qnty']; 
						}
						else{
							echo $row['batch_qnty'] -$row['qc_pass_qnty'];
						}
						?></td>
						<td width="70" style="word-break:break-all;" align="center"> <? echo $row['qc_pass_qnty']; ?></td>
						<td width="50" align="center"><? echo $fabric_shade[$row['fabric_shade']]; ?></td>
						<td width="50" align="center"><? echo $row['fabric_grade']; ?></td>
						<td style="word-break:break-all;" align="center"><?  echo $row['comments']; ?></td>
						
					</tr>
					<? } ?>
				
				<tr>
					<td align="right" colspan="10"><strong>Total</strong></td>
					<td align="right"><? echo $total_grey; ?></td>
					<td align="right"><? echo $total_reje; ?></td>
					<td align="right"><? echo $total_qc_pass ; ?></td>
					<td align="right"><? ?></td>
					<td align="right"><? ?></td>
					<td align="right"><? ?></td>
				</tr>

			</table>
		</div>
		<? echo signature_table(107, $company, "1210px"); ?>
		<script type="text/javascript" src="../../../js/jquery.js"></script>
		<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
		<script>
			function generateBarcode(valuess) {
            var value = valuess;//$("#barcodeValue").val();
            //alert(value)
            var btype = 'code39';//$("input[name=btype]:checked").val();
            var renderer = 'bmp';// $("input[name=renderer]:checked").val();

            var settings = {
            	output: renderer,
            	bgColor: '#FFFFFF',
            	color: '#000000',
            	barWidth: 1,
            	barHeight: 40,
            	moduleSize: 5,
            	posX: 10,
            	posY: 20,
            	addQuietZone: 1
            };
            //$("#barcode_img_id").html('11');
            value = {code: value, rect: false};

            $("#barcode_img_id").show().barcode(value, btype, settings);
        }
        generateBarcode('<? echo $txt_challan_no; ?>');
    </script>
    <?
    exit();
	
}

?>
