<?

use PhpOffice\PhpSpreadsheet\Worksheet\Row;

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
 //-------------------START --------------------------------------
$company_arr=return_library_array( "select id, company_name from  lib_company",'id','company_name');
$location_arr=return_library_array( "select id, location_name from  lib_location",'id','location_name');
$buyer_array = return_library_array("select id, buyer_name from  lib_buyer","id","buyer_name");

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_dyeing", 130, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "details_reset();" );
	exit();
}

if ($action=="load_drop_down_location_lc")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_location_id", 140, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select--", 0, "details_reset();" );
	exit();
}

if ($action=="load_drop_down_location_lc_in_popup")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_location_id", 140, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select--", 0, "" );
	exit();
}

if ($action=="load_drop_down_location_deli")
{
	echo create_drop_down( "cbo_deli_location_id", 140, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select--", 0, "" );
	exit();
}

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	exit();
}
if($action=="load_drop_down_buyer_form")
{
	echo create_drop_down( "cbo_buyer_id", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	exit();
}

if($action=="load_drop_down_dyeing_com")
{
	$data = explode("_",$data);
	$company_id=$data[1];

	if($data[0]==1)
	{
		echo create_drop_down( "cbo_dyeing_company", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "--Select Dyeing Company--", 0, "load_location();details_reset();","" );
	}
	else if($data[0]==3)
	{
		echo create_drop_down( "cbo_dyeing_company", 140, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=21 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select Dyeing Company--", 1, "load_location();details_reset();" );
	}
	else
	{
		echo create_drop_down( "cbo_dyeing_company", 140, $blank_array,"",1, "--Select Dyeing Company--", 0, "load_location();details_reset();" );
	}
	exit();
}


$composition_arr=array();
$construction_arr=array();
$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 order by b.id asc";
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

if($action=='company_wise_report_button_setting')
{
	extract($_REQUEST);


	$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=7 and report_id=235 and is_deleted=0 and status_active=1");

	$print_report_format_arr=explode(",",$print_report_format);

	echo "$('#Print1').hide();\n";
	echo "$('#Print2').hide();\n";
	echo "$('#Print3').hide();\n";
	echo "$('#Print4').hide();\n";
	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			if($id==109){echo "$('#Print1').show();\n";}
			if($id==110){echo "$('#Print2').show();\n";}
			if($id==111){echo "$('#Print3').show();\n";}
			if($id==160){echo "$('#Print4').show();\n";}
		}
	}
	else
	{
		echo "$('#Print1').show();\n";
		echo "$('#Print2').show();\n";
		echo "$('#Print3').show();\n";
		echo "$('#Print4').show();\n";
	}
}


if($action=='list_generate_sales')
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$permission=$_SESSION['page_permission'];

	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_location_id=str_replace("'","",$cbo_location_id);

	$cbo_dyeing_source=str_replace("'","",$cbo_dyeing_source);
	$cbo_dyeing_company=str_replace("'","",$cbo_dyeing_company);
	$cbo_location_dyeing=str_replace("'","",$cbo_location_dyeing);

	$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
	$txt_batch_no=str_replace("'","",$txt_batch_no);
	$cbo_year=str_replace("'","",$cbo_year);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_ord_no=str_replace("'","",$txt_ord_no);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$cbo_status=str_replace("'","",$cbo_status);
	$update_mst_id=str_replace("'","",$update_mst_id);
	$cbo_order_status=str_replace("'","",$cbo_order_status);
	$hidden_receive_id=str_replace("'","",$hidden_receive_id);
	$hidden_product_id=str_replace("'","",$hidden_product_id);
	$hidden_order_id=str_replace("'","",$hidden_order_id);
	if($hidden_receive_id=="") $hidden_receive_id=0;
	if($hidden_product_id=="") $hidden_product_id=0;
	if($hidden_order_id=="") $hidden_order_id=0;
	$txt_file_no=str_replace("'","",$txt_file_no);
	$txt_ref_no=str_replace("'","",$txt_ref_no);
	$cbo_is_sales=str_replace("'","",$cbo_is_sales);


	if($txt_batch_no!="")
	{
		$batch_id_result =sql_select("SELECT id as batch_id FROM pro_batch_create_mst WHERE batch_no='$txt_batch_no' and status_active=1 and is_deleted=0");
		foreach($batch_id_result as $row)
		{
			$batch_id_ref .= $row[csf('batch_id')].",";
		}

		$batch_id_ref = chop($batch_id_ref,",");
		$batch_cond="and b.batch_id in ($batch_id_ref)";
	}
	else
	{
		$batch_cond="";
	}

	if($cbo_location_id!=0) $location_cond="and a.location_id=$cbo_location_id"; else $location_cond="";
	if($cbo_buyer_id!=0) $buyer_cond="and d.po_buyer='$cbo_buyer_id'"; else $buyer_cond="";
	if($txt_job_no!="") $job_cond="and d.po_job_no like '%$txt_job_no%'"; else $job_cond="";
	if($txt_ord_no!="") $order_cond="and d.job_no_prefix_num = '$txt_ord_no'"; else $order_cond="";
	if($txt_date_from!="" && $txt_date_to!="") $date_cond="and a.receive_date between '$txt_date_from' and '$txt_date_to'"; else $date_cond="";

	if($cbo_dyeing_source!=0) $dyeing_source_cond="and a.knitting_source='$cbo_dyeing_source'"; else $dyeing_source_cond="";
	if($cbo_dyeing_company!=0) $dyeing_company_cond="and a.knitting_company='$cbo_dyeing_company'"; else $dyeing_company_cond="";
	if($cbo_location_dyeing!=0 && $cbo_dyeing_source == 1) $dyeing_location_cond="and a.knitting_location_id='$cbo_location_dyeing'"; else $dyeing_location_cond="";

	$order_chack_cond="";
	if($db_type==2)
	{
		$select_year="to_char(e.insert_date,'YYYY') as job_year";
		$order_chack_cond="and b.order_id is null";
		$delv_year= "to_char(d.delivery_date ,'YYYY') as delivery_date";
	}
	else if($db_type==0)
	{
		$select_year="year(e.insert_date) as job_year";
		$order_chack_cond=" and b.order_id=' ' ";
		$delv_year="year(d.delivery_date) as delivery_date";
	}
	if($update_mst_id!="" && $update_mst_id > 0)
	{
		$update_mst_id_cond = " and a.id in($hidden_receive_id) and b.prod_id in($hidden_product_id)";
	}else{
		$update_mst_id_cond = "";
	}

	if(str_replace("'","",$txt_sys_prod_id) != "")
	{
		$sys_prod_id_cond = " and a.recv_number_prefix_num=$txt_sys_prod_id";
	}

	$sql="select a.id, a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, b.id as dtls_id, b.fabric_description_id as determination_id, b.fabric_shade, b.gsm as gsm, b.width as dia, b.prod_id,b.uom, b.batch_id as batch_id, b.no_of_roll as no_of_roll, b.grey_used_qty, b.body_part_id, b.color_id, b.dia_width_type, c.po_breakdown_id, sum(c.quantity) as quantity, d.job_no sales_order_no,d.job_no_prefix_num,d.sales_booking_no,d.po_job_no job_no, d.po_buyer buyer_name, $delv_year,d.within_group,d.buyer_id
	from inv_receive_master a, pro_finish_fabric_rcv_dtls b ,order_wise_pro_details c,fabric_sales_order_mst d
	where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.entry_form=7 and c.entry_form=7
	and c.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.company_id=$cbo_company_id $location_cond $batch_cond $date_cond $job_cond $order_cond $update_mst_id_cond $sys_prod_id_cond $dyeing_source_cond $dyeing_company_cond $dyeing_location_cond
	group by b.id, a.id, a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, b.fabric_description_id, b.fabric_shade, b.gsm, b.width, b.prod_id, b.uom, b.batch_id, b.no_of_roll, b.grey_used_qty, b.body_part_id, b.color_id, b.dia_width_type, c.po_breakdown_id, d.job_no, d.job_no_prefix_num, d.sales_booking_no, d.po_job_no, d.po_buyer, d.delivery_date, d.within_group, d.buyer_id
	order by a.id";

	$result=sql_select($sql);
	$batch_id_arr = $booking_no_id_arr = $sys_id_arr = $booking_no_arr = array();
	foreach($result as $row)
	{
		$batch_id_arr[$row[csf("batch_id")]] = $row[csf("batch_id")];
		$sys_id_arr[$row[csf("id")]] = $row[csf("id")];
	}

	$batch_id_arr = array_filter($batch_id_arr);
	$sys_id_arr = array_filter($sys_id_arr);

	if(!empty($batch_id_arr))
	{
		$all_batch_ids = implode(",", $batch_id_arr);
		if($db_type==2 && count($batch_id_arr)>999)
		{
			$all_batch_id_cond=""; $batchCond="";
			$all_batch_id_arr_chunk=array_chunk($batch_id_arr,999) ;
			foreach($all_batch_id_arr_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$batchCond.="  a.id in($chunk_arr_value) or ";
			}

			$all_batch_id_cond.=" and (".chop($batchCond,'or ').")";
		}
		else
		{
			$all_batch_id_cond=" and a.id in($all_batch_ids)";
		}

		$batch_data=sql_select("select a.id, a.batch_no, a.booking_no_id, a.booking_no, a.booking_without_order, sum(b.roll_no) as roll_no, sum(b.batch_qnty) as batch_qnty from  pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and batch_against>0 and batch_for>0 $all_batch_id_cond group by a.id, a.batch_no, a.booking_no_id, a.booking_no, a.booking_without_order");
		//and a.id in(".implode(",",$batch_id_arr).")
		foreach($batch_data as $row)
		{
			$batch_details[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
			$batch_details[$row[csf('id')]]['booking_no_id']=$row[csf('booking_no_id')];
			$batch_details[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
			$batch_details[$row[csf('id')]]['booking_without_order']=$row[csf('booking_without_order')];
			$batch_details[$row[csf('id')]]['roll_no']=$row[csf('roll_no')];
			$batch_details[$row[csf('id')]]['batch_qnty']=$row[csf('batch_qnty')];
			$booking_no_id_arr[$row[csf('booking_no_id')]] = $row[csf('booking_no_id')];
			$booking_no_arr[$row[csf('booking_no')]] = $row[csf('booking_no')];
		}
	}

	$booking_no_id_arr = array_filter($booking_no_id_arr);
	$booking_no_arr = array_filter($booking_no_arr);

	if(!empty($booking_no_id_arr))
	{
		$all_booking_no_ids = implode(",", $booking_no_id_arr);
		if($db_type==2 && count($booking_no_id_arr)>999)
		{
			$all_booking_no_id_cond=""; $bookIdCond="";
			$all_booking_no_id_arr_chunk=array_chunk($booking_no_id_arr,999) ;
			foreach($all_booking_no_id_arr_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$bookIdCond.="  id in($chunk_arr_value) or ";
			}

			$all_booking_no_id_cond.=" and (".chop($bookIdCond,'or ').")";
		}
		else
		{
			$all_booking_no_id_cond=" and id in($all_booking_no_ids)";
		}

		$non_order_buyer=return_library_array("select id, buyer_id from wo_non_ord_samp_booking_mst where booking_type=4 and status_active=1 and is_deleted=0 $all_booking_no_id_cond ","id","buyer_id");
		// and id in(".implode(",",$booking_no_id_arr).")
	}

	if($txt_ref_no!="") $ref_cond="and d.grouping='$txt_ref_no'";else $ref_cond="";
	if($txt_file_no!="") $file_cond="and d.file_no=$txt_file_no";else $file_cond="";

	$update_row_check=array();
	if($update_mst_id!="")
	{
		$sql_update=sql_select("select id,grey_sys_id,product_id,job_no,order_id,program_no,current_delivery,roll,sys_dtls_id from pro_grey_prod_delivery_dtls where mst_id=$update_mst_id and entry_form=54 and status_active=1 and is_deleted=0");
		foreach($sql_update as $row)
		{
			if($cbo_order_status==1)
			{
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("order_id")]."*".$row[csf("sys_dtls_id")]]["current_delivery"] =$row[csf("current_delivery")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("order_id")]."*".$row[csf("sys_dtls_id")]]["id"] =$row[csf("id")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("order_id")]."*".$row[csf("sys_dtls_id")]]["roll"] =$row[csf("roll")];
			}
			else
			{
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("sys_dtls_id")]]["current_delivery"] =$row[csf("current_delivery")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("sys_dtls_id")]]["id"] =$row[csf("id")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("sys_dtls_id")]]["roll"] =$row[csf("roll")];
			}
		}
	}

	if(!empty($sys_id_arr))
	{
		$all_sys_ids = implode(",", $sys_id_arr);
		if($db_type==2 && count($sys_id_arr)>999)
		{
			$all_sys_id_cond=""; $sysIdCond="";
			$all_sys_id_arr_chunk=array_chunk($sys_id_arr,999) ;
			foreach($all_sys_id_arr_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$sysIdCond.="  grey_sys_id in($chunk_arr_value) or ";
			}

			$all_sys_id_cond.=" and (".chop($sysIdCond,'or ').")";
		}
		else
		{
			$all_sys_id_cond=" and grey_sys_id in($all_sys_ids)";
		}

		$sql_production=sql_select("Select grey_sys_id,product_id,order_id,program_no,current_delivery,sys_dtls_id from pro_grey_prod_delivery_dtls where status_active=1 and is_deleted=0 and entry_form=54 $all_sys_id_cond ");
		//and grey_sys_id in(".implode(",",$sys_id_arr).")
		foreach($sql_production as $row)
		{
			if($cbo_order_status==1)
			{
				$sql_production_arr[$row[csf("grey_sys_id")]][$row[csf("product_id")]][$row[csf("order_id")]][$row[csf("sys_dtls_id")]]['prodcut_qty'] +=$row[csf("current_delivery")];
			}
			else
			{
				$sql_production_arr[$row[csf("grey_sys_id")]][$row[csf("product_id")]][$row[csf("sys_dtls_id")]]['prodcut_qty'] +=$row[csf("current_delivery")];
			}
		}
	}
	$sql_for_dia=sql_select("select b.job_no, b.po_break_down_id, b.dia_width FROM wo_pre_cost_fabric_cost_dtls a,
		wo_pre_cos_fab_co_avg_con_dtls b WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.job_no, b.po_break_down_id, b.dia_width");
	foreach($sql_for_dia as $row)
	{
		$dia_arr[$row[csf("job_no")]][$row[csf("po_break_down_id")]]['dia_width']=$row[csf("dia_width")];
	}

	if($cbo_order_status==1)
	{
		if($order_ids!="")
		{
			$sql_machineDia=sql_select("select a.febric_description_id, b.po_breakdown_id, a.machine_dia
				from inv_receive_master p, pro_grey_prod_entry_dtls a, order_wise_pro_details b
			where p.id=a.mst_id and a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and p.status_active=1 and p.is_deleted=0 and b.po_breakdown_id in ($hidden_order_id) and p.entry_form in(2,22) and a.machine_dia is not NULL group by a.febric_description_id, b.po_breakdown_id, a.machine_dia"); //
		}
	}
	else
	{
		if(!empty($booking_no_arr))
		{
			$all_booking_numbers = "'".implode("','", $booking_no_arr)."'";

			$booking_no_arr = explode(",", $all_booking_numbers);

			if($db_type==2 && count($booking_no_arr)>999)
			{
				$all_booking_numbers_cond=""; $bookNumCond="";
				$all_booking_numbers_arr_chunk=array_chunk($booking_no_arr,999) ;
				foreach($all_booking_numbers_arr_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$bookNumCond.="  p.booking_no in($chunk_arr_value) or ";
				}

				$all_booking_numbers_cond.=" and (".chop($bookNumCond,'or ').")";
			}
			else
			{
				$all_booking_numbers_cond=" and p.booking_no in($all_booking_numbers)";
			}

			$sql_machineDia=sql_select("select p.booking_no, a.febric_description_id, a.machine_dia
				from inv_receive_master p, pro_grey_prod_entry_dtls a
				where p.id=a.mst_id $all_booking_numbers_cond and a.status_active=1 and a.is_deleted=0 and p.status_active=1 and p.is_deleted=0 and p.booking_without_order=1 and a.machine_dia is not NULL group by p.booking_no, a.febric_description_id, a.machine_dia");
			//and p.booking_no in(".implode(",",$booking_no_arr).")
		}
	}

	$all_machineDia="";
	$mc_dia_arr=array();
	foreach($sql_machineDia as $rows)
	{
		if($cbo_order_status==1)
			$mc_dia_arr[$rows[csf('po_breakdown_id')]][$rows[csf('febric_description_id')]].=$rows[csf('machine_dia')].',';
		else
			$mc_dia_arr[$rows[csf('booking_no')]][$rows[csf('febric_description_id')]].=$rows[csf('machine_dia')].',';
	}
	unset($sql_machineDia);

	if($update_mst_id!=""){$update_mst_id_cond="and a.booking_id = $update_mst_id ";}else{}
	$receive_sql = sql_select("select a.id, a.recv_number, b.prod_id, b.batch_id, b.body_part_id, b.fabric_description_id, b.gsm, b.fabric_shade, b.dia_width_type, b.uom, sum(b.receive_qnty) as qnty from inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id = b.mst_id and a.entry_form =225 and a.receive_basis =10 and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 $update_mst_id_cond group by a.id, a.recv_number, b.prod_id, b.batch_id, b.body_part_id, b.fabric_description_id, b.gsm, b.fabric_shade, b.dia_width_type, b.uom");

	foreach ($receive_sql as $val)
	{
		$receive_arr[$val[csf("batch_id")]][$val[csf("body_part_id")]][$val[csf("prod_id")]][$val[csf("fabric_shade")]][$val[csf("dia_width_type")]] .= $val[csf("recv_number")].",";
	}

	ob_start();
	?>
	<script type="text/javascript">
		$("#removeQty").click(function()
		{
			// alert('OK');
			if(!$(this).prop('checked'))
			{
				// alert('OK');
				$(".rmvQty").val('');
			}
		})
	</script>
	<div style="width:1880px;" id="main_report_div">
		<form name="delivery_details" id="delivery_details" autocomplete="off" >
			<div id="report_print" style="width:1880px;">
				<table width="1860" class="rpt_table" id="tbl_header" cellpadding="0" cellspacing="1" rules="all">
					<thead>
						<th width="30">Sl</th>
						<th width="100">System Id</th>
						<th width="90">Booking No</th>
						<th width="75">Knitting Source</th>
						<th width="70">Batch No</th>
						<th width="80">Fabric Shade</th>
						<th width="70">Prd. date</th>
						<th width="50">Prod. Id</th>
						<th width="40">Year</th>
						<th width="80">Job No</th>
						<th width="100">Buyer</th>
						<th width="90">Order/FSO No</th>
						<th width="70">File No</th>
						<th width="70">Ref No</th>
						<th width="110">Construction </th>
						<th width="110">Composition</th>
						<th width="40">GSM</th>
						<th width="40">Dia</th>
						<th width="40">UOM</th>
						<th width="100">Grey Dia</th>
						<th width="40">Roll</th>
						<th width="70">Prod. qty</th>
						<th width="70">Cumm. Delivery</th>
						<th width="70">Cumm. Balance</th>
						<th width="75" >Current Delv.<input type="checkbox" name="removeQty" id="removeQty" title="Click for remove quantity" checked="checked"></th>
						<th >Roll</th>
					</thead>
				</table>
				<div style="width:1880px; overflow-y:scroll; max-height:200px;font-size:12px; overflow-x:hidden;" id="scroll_body">
					<table width="1863px" class="rpt_table" id="table_body" cellpadding="0" cellspacing="1" rules="all">
						<tbody>
							<?
							$i=1;
							$current_row_array=array();
							foreach($result as $row)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								if($cbo_order_status==1)
								{
									$tot_delivery=$sql_production_arr[$row[csf("id")]][$row[csf("prod_id")]][$row[csf("po_breakdown_id")]][$row[csf("dtls_id")]]['prodcut_qty'];
									$index_pk=$row[csf("id")]."*".$row[csf("prod_id")]."*".$row[csf("po_breakdown_id")]."*".$row[csf("dtls_id")];
								}
								else
								{
									$tot_delivery=$sql_production_arr[$row[csf("id")]][$row[csf("prod_id")]][$row[csf("dtls_id")]]['prodcut_qty'];
									$index_pk=$row[csf("id")]."*".$row[csf("prod_id")]."*".$row[csf("dtls_id")];
								}
								$current_stock=$row[csf("quantity")];


								if($update_mst_id=="")
								{
									if($cbo_status==1)
									{
										if( $current_stock > $tot_delivery)
										{
											?>
											<tr bgcolor="<? echo $bgcolor; ?>">
												<td width="30" align="center">
													<? echo $i; ?>
													<input type="hidden" id="hidesysid_<? echo $i;?>" name="hidesysid_<? echo $i;?>" value="<? echo $row[csf("id")];?>"  />
													<input type="hidden" id="body_part_id_<? echo $i;?>" name="body_part_id_<? echo $i;?>" value="<? echo $row[csf("body_part_id")];?>"  />
													<input type="hidden" id="color_id_<? echo $i;?>" name="color_id_<? echo $i;?>" value="<? echo $row[csf("color_id")];?>"  />
													<input type="hidden" id="dia_width_type_<? echo $i;?>" name="dia_width_type_<? echo $i;?>" value="<? echo $row[csf("dia_width_type")];?>"  />
												</td>
												<td width="100" align="center">
													<input type="hidden" id="hidesysnum_<? echo $i;?>" name="hidesysnum_<? echo $i;?>" value="<? echo $row[csf("recv_number")];?>"  />
													<? echo $row[csf("recv_number")]; ?>
												</td>
												<td width="90" align="center">
													<input type="hidden" id="hidefindtls_<? echo $i;?>" name="hidefindtls_<? echo $i;?>" value="<? echo $row[csf("dtls_id")];?>"  />
													<input type="hidden" id="hidegreyused_<? echo $i;?>" name="hidegreyused_<? echo $i;?>" value="<? echo $row[csf("grey_used_qty")];?>"  />
													<input type="hidden" id="hidebatch_<? echo $i;?>" name="hidebatch_<? echo $i;?>" value="<? echo $row[csf("batch_id")];?>"  />
													<? echo $batch_details[$row[csf("batch_id")]]['booking_no']; ?>
												</td>
												<td width="75" align="center">
													<?
													if($row[csf("knitting_source")]==1)  echo "In-House"; if($row[csf("knitting_source")]==3) echo "Sub-Contract";
													?>
												</td>
												<td width="70" align="center">
													<input type="hidden" id="hideprogrum_<? echo $i;?>" name="hideprogrum_<? echo $i;?>" value="<? echo $row[csf("batch_id")];?>"  />
													<?
													echo $batch_details[$row[csf("batch_id")]]['batch_no'];
													?>
												</td>
												<td width="80" align="center">
													<input type="hidden" id="hidefabshade_<? echo $i;?>" name="hidefabshade_<? echo $i;?>" value="<? echo $row[csf("fabric_shade")];?>"  />
													<?
													echo $fabric_shade[$row[csf("fabric_shade")]];
													?>
												</td>
												<td width="70" align="center"><?  if($row[csf("receive_date")]!='0000-00-00' && $row[csf("receive_date")]!='')  echo change_date_format($row[csf("receive_date")]); else echo ""; ?></td>
												<td width="50" align="center">
													<input type="hidden" id="hideprodid_<? echo $i;?>" name="hideprodid_<? echo $i;?>" value="<? echo $row[csf("prod_id")];?>"  />
													<? echo $row[csf("prod_id")]; ?>
												</td>
												<td width="40" align="center"><? echo date("Y",strtotime($row[csf("receive_date")])); ?></td>
												<td width="80" align="center">
													<input type="hidden" id="hidejob_<? echo $i;?>" name="hidejob_<? echo $i;?>" value="<? echo $row[csf("job_no")];?>"  />
													<? echo $row[csf("job_no")]; ?>
												</td>
												<td width="100" align="center"><? echo ($row[csf("within_group")]==1)?$buyer_array[$row[csf("buyer_name")]]:$buyer_array[$row[csf("buyer_id")]]; ?></td>
												<td width="90">
													<input type="hidden" id="hideorder_<? echo $i;?>" name="hideorder_<? echo $i;?>" value="<? echo $row[csf("po_breakdown_id")];?>"  />
													<? echo $row[csf("sales_order_no")]; ?>
												</td>
												<td width="70"><? echo $row[csf("file_no")]; ?></td>
												<td width="70"><? echo $row[csf("grouping")]; ?></td>
												<td width="110">
													<input type="hidden" id="hideconstruction_<? echo $i;?>" name="hideconstruction_<? echo $i;?>" value="<? echo $row[csf("determination_id")];?>"  />
													<? echo $construction_arr[$row[csf("determination_id")]]; ?>
												</td>
												<td width="110">
													<input type="hidden" id="hidecomposition_<? echo $i;?>" name="hidecomposition_<? echo $i;?>" value="<? echo $composition_arr[$row[csf("determination_id")]];?>"  />
													<? echo $composition_arr[$row[csf("determination_id")]]; ?>
												</td>
												<td width="40" align="center">
													<input type="hidden" id="hidegsm_<? echo $i;?>" name="hidegsm_<? echo $i;?>" value="<? echo $row[csf("gsm")]; ?>"  />
													<? echo $row[csf("gsm")]; ?>
												</td>
												<td width="40" align="center">
													<input type="hidden" id="hidedia_<? echo $i;?>" name="hidedia_<? echo $i;?>" value="<? echo $row[csf("dia")]; ?>"  />
													<? echo $row[csf("dia")]; ?>
												</td>
												<td width="40" align="center">
													<input type="hidden" id="hideuom_<? echo $i;?>" name="hideuom_<? echo $i;?>" value="<? echo $row[csf("uom")]; ?>"  />
													<? echo $unit_of_measurement[$row[csf("uom")]]; ?>
												</td>
												<td width="100" align="center">
													<? $mc_dia="";
													if($cbo_order_status==1)
														$mc_dia=implode(",",array_unique(explode(",",$mc_dia_arr[$row[csf('po_breakdown_id')]][$row[csf('determination_id')]])));
													else
														$mc_dia=$mc_dia_arr[$batch_details[$row[csf("batch_id")]]['booking_no']][$row[csf('determination_id')]];

													echo chop($mc_dia,','); ?>
												</td>
												<td width="40" align="center">
													<? echo $row[csf("no_of_roll")]; ?>
												</td>
												<td width="70" align="right" ><? echo number_format($row[csf("quantity")],2); $total_stock+=$row[csf("quantity")]; ?>
											</td>
											<td width="70" align="right" >
												<?
												echo number_format($tot_delivery,2);
												$gt_tot_delivery+=$tot_delivery;
												?>
											</td>
											<td width="70"align="right" id="totalqtyTd_<? echo $i; ?>">
												<? $balance=($row[csf("quantity")]-$tot_delivery); echo number_format($balance,2); $total_balance+=$balance; ?>
											</td>
											<td width="75">
												<input type="hidden" id="hiddendtlsid_<? echo $i;?>" name="hiddendtlsid_<? echo $i;?>"  style="width:60px;" value="" />
												<input type="text" id="txtcurrentdelivery_<? echo $i;?>" name="txtcurrentdelivery_<? echo $i;?>" class="text_boxes_numeric rmvQty" style="width:60px;" value="<? echo number_format($balance,2,".",""); ?>" onBlur="setHideval(<? echo $i; ?>)" onKeyup="checkuom(<? echo $i; ?>,<? echo $row[csf("uom")];?>)" />
												<input type="hidden" id="hidden_current_val_<? echo $i;?>" value="">
												<input type="hidden" id="txt_get_value_fins_recv_<? echo $i;?>" value="<? echo $row[csf("quantity")]; ?>" name="txt_get_value_fins_recv_<? echo $i;?>">
											</td>
											<td >
												<input type="text" id="txtroll_<? echo $i;?>" name="txtroll_<? echo $i;?>" class="text_boxes_numeric" style="width:50px;" value="" onBlur="total_roll(<? echo $i; ?>)"  />
												<input type="hidden" id="hideroll_<? echo $i;?>" value=""  >
											</td>
										</tr>
											<?
											$i++;
										}
									}
									else
									{
										if($current_stock<=$tot_delivery)
										{
											?>
											<tr bgcolor="<? echo $bgcolor; ?>">
												<td width="30" align="center"><? echo $i; ?>
												<input type="hidden" id="hidesysid_<? echo $i;?>" name="hidesysid_<? echo $i;?>" value="<? echo $row[csf("id")];?>"  />
												<input type="hidden" id="body_part_id_<? echo $i;?>" name="body_part_id_<? echo $i;?>" value="<? echo $row[csf("body_part_id")];?>"  />
												<input type="hidden" id="color_id_<? echo $i;?>" name="color_id_<? echo $i;?>" value="<? echo $row[csf("color_id")];?>"  />
												<input type="hidden" id="dia_width_type_<? echo $i;?>" name="dia_width_type_<? echo $i;?>" value="<? echo $row[csf("dia_width_type")];?>"  />
											</td>
											<td width="100">
												<input type="hidden" id="hidesysnum_<? echo $i;?>" name="hidesysnum_<? echo $i;?>" value="<? echo $row[csf("recv_number")];?>"  />
												<? echo $row[csf("recv_number")];?>
											</td>
											<td width="90" align="center">
												<input type="hidden" id="hidefindtls_<? echo $i;?>" name="hidefindtls_<? echo $i;?>" value="<? echo $row[csf("dtls_id")];?>"  />
												<input type="hidden" id="hidegreyused_<? echo $i;?>" name="hidegreyused_<? echo $i;?>" value="<? echo $row[csf("grey_used_qty")];?>"  />
												<input type="hidden" id="hidebatch_<? echo $i;?>" name="hidebatch_<? echo $i;?>" value="<? echo $row[csf("batch_id")];?>"  />
												<? echo $batch_details[$row[csf("batch_id")]]['booking_no']; ?>
											</td>
											<td width="75" align="center">
												<?
												if($row[csf("knitting_source")]==1) echo "In-House"; if($row[csf("knitting_source")]==3) echo "Sub-Contract";
												?>
											</td>
											<td width="70" align="center">
												<input type="hidden" id="hideprogrum_<? echo $i;?>" name="hideprogrum_<? echo $i;?>" value="<? echo $row[csf("batch_id")];?>"  />
												<? echo $batch_details[$row[csf("batch_id")]]['batch_no']; ?>
											</td>
											<td width="80" align="center">
												<input type="hidden" id="hidefabshade_<? echo $i;?>" name="hidefabshade_<? echo $i;?>" value="<? echo $row[csf("fabric_shade")];?>"  />
												<? echo $fabric_shade[$row[csf("fabric_shade")]]; ?>
											</td>
											<td width="70" align="center">
												<?  if($row[csf("receive_date")]!='0000-00-00' && $row[csf("receive_date")]!='')  echo change_date_format($row["receive_date"]); else echo ""; ?></td>
												<td width="50" align="center">
													<input type="hidden" id="hideprodid_<? echo $i;?>" name="hideprodid_<? echo $i;?>" value="<? echo $row[csf("prod_id")];?>"  />
													<? echo $row[csf("prod_id")]; ?>
												</td>
												<td width="40" align="center"><p><? echo $row[csf("job_year")]; ?></p></td>
												<td width="80" align="center">
													<input type="hidden" id="hidejob_<? echo $i;?>" name="hidejob_<? echo $i;?>" value="<? echo $row[csf("job_no")];?>"  />
													<? echo $row[csf("job_no")]; ?>
												</td>
												<td width="100" align="center"><? echo ($row[csf("within_group")]==1)?$buyer_array[$row[csf("buyer_name")]]:$buyer_array[$row[csf("buyer_id")]]; ?></td>
												<td width="90">
													<input type="hidden" id="hideorder_<? echo $i;?>" name="hideorder_<? echo $i;?>" value="<? echo $row[csf("po_breakdown_id")];?>"  />
													<? echo $row[csf("sales_order_no")]; ?>
												</td>
												<td width="70"><? echo $row[csf("file_no")]; ?></td>
												<td width="70"><? echo $row[csf("grouping")]; ?></td>
												<td width="110">
													<input type="hidden" id="hideconstruction_<? echo $i;?>" name="hideconstruction_<? echo $i;?>" value="<? echo $row[csf("detarmination_id")];?>"  />
													<? echo $construction_arr[$row[csf("determination_id")]]; ?>
												</td>
												<td width="110">
													<input type="hidden" id="hidecomposition_<? echo $i;?>" name="hidecomposition_<? echo $i;?>" value="<? echo $composition_arr[$row[csf("detarmination_id")]];?>"  />
													<? echo $composition_arr[$row[csf("determination_id")]]; ?>
												</td>
												<td width="40" align="center">
													<input type="hidden" id="hidegsm_<? echo $i;?>" name="hidegsm_<? echo $i;?>" value="<? echo $row[csf("gsm")]; ?>"  />
													<? echo $row["gsm"]; ?>
												</td>
												<td width="40" align="center">
													<input type="hidden" id="hidedia_<? echo $i;?>" name="hidedia_<? echo $i;?>" value="<? echo $row[csf("dia")]; ?>"  />
													<? echo $row[csf("dia")]; ?>
												</td>
												<td width="40" align="center">
													<input type="hidden" id="hideuom_<? echo $i;?>" name="hideuom_<? echo $i;?>" value="<? echo $row[csf("uom")]; ?>"  />
													<? echo $unit_of_measurement[$row[csf("uom")]]; ?>
												</td>
												<td width="100" align="center">
													<? $mc_dia="";
													if($cbo_order_status==1)
														$mc_dia=implode(",",array_unique(explode(",",$mc_dia_arr[$row[csf('po_breakdown_id')]][$row[csf('determination_id')]])));
													else
														$mc_dia=$mc_dia_arr[$batch_details[$row[csf("batch_id")]]['booking_no']][$row[csf('determination_id')]];

													echo chop($mc_dia,','); ?>
												</td>
												<td width="40" align="center">
													<? echo $row[csf("no_of_roll")]; ?>
												</td>
												<td width="70" align="right" ><? echo number_format($row[csf("quantity")],2); $total_stock+=$row[csf("quantity")]; ?></td>
												<td width="70" align="right">
													<?
													echo number_format($tot_delivery,2);
													$gt_tot_delivery+=$tot_delivery;
													?>
												</td>
												<td width="70"align="right" id="totalqtyTd_<? echo $i; ?>"><p><? $balance=($row[csf("quantity")]-$tot_delivery); echo number_format($balance,2); $total_balance+=$balance; ?></p></td>
												<td width="75">
													<input type="hidden" id="hiddendtlsid_<? echo $i;?>" name="hiddendtlsid_<? echo $i;?>"  style="width:60px;" value="" />
													<input type="text" id="txtcurrentdelivery_<? echo $i;?>" name="txtcurrentdelivery_<? echo $i;?>" class="text_boxes_numeric rmvQty" style="width:60px;" value="<? echo number_format($balance,2,".",""); ?>" onBlur="setHideval(<? echo $i; ?>)"  />
													<input type="hidden" id="hidden_current_val_<? echo $i;?>" value="">
												</td>
												<td>
													<input type="text" id="txtroll_<? echo $i;?>" name="txtroll_<? echo $i;?>" class="text_boxes_numeric" style="width:50px;" value="" onBlur="total_roll(<? echo $i; ?>)"  />
													<input type="hidden" id="hideroll_<? echo $i;?>">
												</td>
											</tr>
											<?
											$i++;
										}
									}
								}
								else
								{

									if($update_row_check[$index_pk]["current_delivery"]>0)
									{

										if($receive_arr[$row[csf("batch_id")]][$row[csf("body_part_id")]][$row[csf("prod_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]])
										{
											$disabled = "disabled";
										}
										else
										{
											$disabled = "";
										}


										?>
										<tr bgcolor="<? echo $bgcolor; ?>" style="background-color:#FF6;">
											<td width="30" align="center"><? echo $i; ?>
											<input type="hidden" id="hidesysid_<? echo $i;?>" name="hidesysid_<? echo $i;?>" value="<? echo $row[csf("id")];?>"  />
											<input type="hidden" id="body_part_id_<? echo $i;?>" name="body_part_id_<? echo $i;?>" value="<? echo $row[csf("body_part_id")];?>"  />
											<input type="hidden" id="color_id_<? echo $i;?>" name="color_id_<? echo $i;?>" value="<? echo $row[csf("color_id")];?>"  />
											<input type="hidden" id="dia_width_type_<? echo $i;?>" name="dia_width_type_<? echo $i;?>" value="<? echo $row[csf("dia_width_type")];?>"  />
										</td>
										<td width="100"><input type="hidden" id="hidesysnum_<? echo $i;?>" name="hidesysnum_<? echo $i;?>" value="<? echo $row[csf("recv_number")];?>"  />
											<?
											echo $row[csf("recv_number")];
											?>
										</td>
										<td width="90" align="center">
											<input type="hidden" id="hidefindtls_<? echo $i;?>" name="hidefindtls_<? echo $i;?>" value="<? echo $row[csf("dtls_id")];?>"  />
											<input type="hidden" id="hidegreyused_<? echo $i;?>" name="hidegreyused_<? echo $i;?>" value="<? echo $row[csf("grey_used_qty")];?>"  />
											<input type="hidden" id="hidebatch_<? echo $i;?>" name="hidebatch_<? echo $i;?>" value="<? echo $row[csf("batch_id")];?>"  />
											<? echo $batch_details[$row[csf("batch_id")]]['booking_no']; ?>
										</td>
										<td width="75" align="center">
											<?
											if($row[csf("knitting_source")]==1)  echo "In-House"; if($row[csf("knitting_source")]==3) echo "Sub-Contract";
											?>
										</td>
										<td width="70" align="center">
											<input type="hidden" id="hideprogrum_<? echo $i;?>" name="hideprogrum_<? echo $i;?>" value="<? echo $row[csf("batch_id")];?>"  />
											<? echo  $batch_details[$row[csf("batch_id")]]['batch_no']; ?>
										</td>
										<td width="80" align="center">
											<input type="hidden" id="hidefabshade_<? echo $i;?>" name="hidefabshade_<? echo $i;?>" value="<? echo $row[csf("fabric_shade")];?>"  />
											<? echo $fabric_shade[$row[csf("fabric_shade")]]; ?>
										</td>
										<td width="70" align="center"><? if($row[csf("receive_date")]!='0000-00-00' && $row[csf("receive_date")]!='')  echo change_date_format($row[csf("receive_date")]); else echo ""; ?></td>
										<td width="50" align="center">
											<input type="hidden" id="hideprodid_<? echo $i;?>" name="hideprodid_<? echo $i;?>" value="<? echo $row[csf("prod_id")];?>"  />
											<? echo $row[csf("prod_id")]; ?>
										</td>
										<td width="40" align="center"><p><? echo $row[csf("job_year")]; ?></p></td>
										<td width="80" align="center">
											<input type="hidden" id="hidejob_<? echo $i;?>" name="hidejob_<? echo $i;?>" value="<? echo $row[csf("job_no")];?>"  />
											<? echo $row[csf("job_no")]; ?>
										</td>
										<td width="100" align="center"><? echo ($row[csf("within_group")]==1)?$buyer_array[$row[csf("buyer_name")]]:$buyer_array[$row[csf("buyer_id")]]; ?></td>
										<td width="90">
											<input type="hidden" id="hideorder_<? echo $i;?>" name="hideorder_<? echo $i;?>" value="<? echo $row[csf("po_breakdown_id")];?>"  />
											<? echo $row[csf("sales_order_no")]; ?>
										</td>
										<td width="70"><? echo $row[csf("file_no")]; ?></td>
										<td width="70"><? echo $row[csf("grouping")]; ?></td>
										<td width="110">
											<input type="hidden" id="hideconstruction_<? echo $i;?>" name="hideconstruction_<? echo $i;?>" value="<? echo $row[csf("determination_id")];?>"  />
											<? echo $construction_arr[$row[csf("determination_id")]]; ?>
										</td>
										<td width="110">
											<input type="hidden" id="hidecomposition_<? echo $i;?>" name="hidecomposition_<? echo $i;?>" value="<? echo $composition_arr[$row[csf("determination_id")]];?>"  />
											<? echo $composition_arr[$row[csf("determination_id")]]; ?>
										</td>
										<td width="40" align="center">
											<input type="hidden" id="hidegsm_<? echo $i;?>" name="hidegsm_<? echo $i;?>" value="<? echo $row[csf("gsm")]; ?>"  />
											<? echo $row[csf("gsm")]; ?>
										</td>
										<td width="40" align="center">
											<input type="hidden" id="hidedia_<? echo $i;?>" name="hidedia_<? echo $i;?>" value="<? echo $row[csf("dia")]; ?>"  />
											<? echo $row[csf("dia")]; ?>
										</td>
										<td width="40" align="center">
											<input type="hidden" id="hideuom_<? echo $i;?>" name="hideuom_<? echo $i;?>" value="<? echo $row[csf("uom")]; ?>"  />
											<? echo $unit_of_measurement[$row[csf("uom")]]; ?>
										</td>
										<td width="100" align="center">
											<? $mc_dia="";
											if($cbo_order_status==1)
												$mc_dia=implode(",",array_unique(explode(",",$mc_dia_arr[$row[csf('po_breakdown_id')]][$row[csf('determination_id')]])));
											else
												$mc_dia=$mc_dia_arr[$batch_details[$row[csf("batch_id")]]['booking_no']][$row[csf('determination_id')]];

											echo chop($mc_dia,','); ?>
										</td>
										<td width="40" align="center"><? echo $row[csf("no_of_roll")]; ?></td>
										<td width="70" align="right">
											<? echo number_format($row[csf("quantity")],2); $total_stock+=$row[csf("quantity")];?>
										</td>
										<td width="70" align="right">
											<?
											$tot_delivery=$tot_delivery-$update_row_check[$index_pk]["current_delivery"];
											echo number_format($tot_delivery,2);
											$gt_tot_delivery+=$tot_delivery;
											?>
										</td>
										<td width="70"align="right" id="totalqtyTd_<? echo $i; ?>">
											<?
											$balance=($row[csf("quantity")]-$tot_delivery);
											echo number_format($balance,2); $total_balance+=$balance;
											?>
										</td>
										<td width="75">
											<input type="hidden" id="hiddendtlsid_<? echo $i;?>" name="hiddendtlsid_<? echo $i;?>"  style="width:60px;" value="<? echo $update_row_check[$index_pk]["id"]; ?>" />
											<input type="text" id="txtcurrentdelivery_<? echo $i;?>" name="txtcurrentdelivery_<? echo $i;?>" class="text_boxes_numeric rmvQty" style="width:60px;" value="<? /* echo number_format($balance,2,".",""); */ echo $update_row_check[$index_pk]["current_delivery"]; $to_delivey+=$update_row_check[$index_pk]["current_delivery"]; ?>" onBlur="setHideval(<? echo $i;?>)" <? echo $disabled;?>/>
											<input type="hidden" id="hidden_current_val_<? echo $i;?>" value="<? echo $update_row_check[$index_pk]["current_delivery"]; ?>">
										</td>
										<td >
											<input type="text" id="txtroll_<? echo $i;?>" name="txtroll_<? echo $i;?>" class="text_boxes_numeric" style="width:50px;" value="<? echo $update_row_check[$index_pk]["roll"]; $to_roll+=$update_row_check[$index_pk]["roll"]; ?>" onBlur="total_roll(<? echo $i;?>)" />
											<input type="hidden" id="hideroll_<? echo $i;?>" value="<? echo $update_row_check[$index_pk]["roll"]; ?>"  >
										</td>
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
		<table width="1863" class="rpt_table" id="tbl_footer" cellpadding="0" cellspacing="1" rules="all">
			<tfoot>
				<th colspan="20" align="right">Total:</th>
				<th width="70"><? echo number_format($total_stock,2); ?></th>
				<th width="70"><? echo number_format($gt_tot_delivery,2); ?></th>
				<th width="70"><? echo number_format($total_balance,2); ?></th>
				<th width="75" id="total_current_val" align="right"> <? /* echo number_format($total_balance,2); */ echo number_format($to_delivey,2);?></th>
				<th width="62" id="total_roll" align="right"><? echo number_format($to_roll,0); ?></th>
			</tfoot>
		</table>
	</div>
	<table width="1820" class="rpt_table" id="tbl_foot" cellpadding="0" cellspacing="1" rules="all">

		<tr>
			<td colspan="25" height="30" valign="middle" align="center" class="button_container">
				<?
				echo create_drop_down( "cbo_template_id", 100, $report_template_list,'', 0, '', 0, "");
				echo load_submit_buttons( $permission, "fnc_prod_delivery",0,1 ,"fnResetForm()",1) ;
				?>
				<input id="Print2" class="formbutton" type="button" style="width:80px" onClick="fnc_prod_delivery(5)" name="print2" value="Print 2">
				<input id="Print3" class="formbutton" type="button" style="width:80px" onClick="fnc_prod_delivery(6)" name="print3" value="Print 3">
				<input id="Print4" class="formbutton" type="button" style="width:80px" onClick="fnc_prod_delivery(7)" name="print3" value="Print 4">
			</td>

		</tr>
	</table>
</form>
</div>
<?
}

if($action=='list_generate')
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$permission=$_SESSION['page_permission'];
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_location_id=str_replace("'","",$cbo_location_id);

	$cbo_dyeing_source=str_replace("'","",$cbo_dyeing_source);
	$cbo_dyeing_company=str_replace("'","",$cbo_dyeing_company);
	$cbo_location_dyeing=str_replace("'","",$cbo_location_dyeing);

	$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
	$txt_batch_no=str_replace("'","",$txt_batch_no);
	$cbo_year=str_replace("'","",$cbo_year);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_ord_no=str_replace("'","",$txt_ord_no);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$cbo_status=str_replace("'","",$cbo_status);
	$update_mst_id=str_replace("'","",$update_mst_id);
	$cbo_order_status=str_replace("'","",$cbo_order_status);
	$hidden_receive_id=str_replace("'","",$hidden_receive_id);
	$hidden_product_id=str_replace("'","",$hidden_product_id);
	$hidden_order_id=str_replace("'","",$hidden_order_id);
	if($hidden_receive_id=="") $hidden_receive_id=0;
	if($hidden_product_id=="") $hidden_product_id=0;
	if($hidden_order_id=="") $hidden_order_id=0;
	$txt_file_no=str_replace("'","",$txt_file_no);
	$txt_ref_no=str_replace("'","",$txt_ref_no);
	$cbo_is_sales=str_replace("'","",$cbo_is_sales);

	//$non_order_buyer=return_library_array("select id, buyer_id from wo_non_ord_samp_booking_mst where booking_type=4 and status_active=1 and is_deleted=0","id","buyer_id");
	$non_order_booking_sql = sql_select("select id, buyer_id, grouping from wo_non_ord_samp_booking_mst where booking_type=4 and status_active=1 and is_deleted=0");

	foreach ($non_order_booking_sql as $val)
	{
		$non_order_buyer[$val[csf("id")]] = $val[csf("buyer_id")];
		$non_order_grouping[$val[csf("id")]] = $val[csf("grouping")];
	}

	if($txt_ref_no!="") $ref_cond="and d.grouping='$txt_ref_no'";else $ref_cond="";
	if($txt_file_no!="") $file_cond="and d.file_no=$txt_file_no";else $file_cond="";
	if(str_replace("'","",$txt_sys_prod_id) != "")$sys_prod_id_cond = " and a.recv_number_prefix_num=$txt_sys_prod_id"; else $sys_prod_id_cond = "" ;

	$process_loss_method_variable	=sql_select("select process_loss_method from variable_order_tracking where company_name=$cbo_company_id and variable_list=18 and item_category_id=2 and status_active =1");
	$process_loss_method = ($process_loss_method_variable[0][csf("process_loss_method")] ==2) ? 2: 1;

	/*$sql_roll=sql_select("select mst_id,prod_id,order_id,no_of_roll as no_of_roll from pro_grey_prod_entry_dtls where status_active=1 and is_deleted=0");
	foreach($sql_roll as $row)
	{
		$order_arr=explode(",",$row[csf("order_id")]);
		foreach($order_arr as $ord_id)
		{
			$roll_arr[$row[csf("mst_id")]][$row[csf("prod_id")]][$ord_id] =$row[csf("no_of_roll")];
		}
	}*/

	if($txt_batch_no!="")
	{
		//$batch_id_ref=return_field_value("id as batch_id"," pro_batch_create_mst","batch_no='$txt_batch_no' and status_active=1 and is_deleted=0","batch_id");
		//$batch_cond="and b.batch_id ='$batch_id_ref'";
		$batch_id_ref=return_library_array( "select id, id from pro_batch_create_mst where batch_no='$txt_batch_no' and status_active=1 and is_deleted=0",'id','id');
		$batch_cond="and b.batch_id in(".implode(",",$batch_id_ref).")";
	}
	else
	{
		$batch_cond="";
	}

	if($cbo_location_id!=0) $location_cond="and a.location_id=$cbo_location_id"; else $location_cond="";
	if($cbo_buyer_id!=0) $buyer_cond="and e.buyer_name='$cbo_buyer_id'"; else $buyer_cond="";

	if($cbo_dyeing_source!=0) $dyeing_source_cond="and a.knitting_source='$cbo_dyeing_source'"; else $dyeing_source_cond="";
	if($cbo_dyeing_company!=0) $dyeing_company_cond="and a.knitting_company='$cbo_dyeing_company'"; else $dyeing_company_cond="";
	if($cbo_location_dyeing!=0 && $cbo_dyeing_source == 1) $dyeing_location_cond="and a.knitting_location_id='$cbo_location_dyeing'"; else $dyeing_location_cond="";

	if($txt_job_no!="") $job_cond="and e.job_no like '%$job_no_po_id%'"; else $job_cond="";
	if($txt_ord_no!="") $order_cond="and d.po_number like '%$txt_ord_no%'"; else $order_cond="";
	if($txt_date_from!="" && $txt_date_to!="") $date_cond="and a.receive_date between '$txt_date_from' and '$txt_date_to'"; else $date_cond="";

	$order_chack_cond="";
	if($db_type==2)
	{
		$select_year="to_char(e.insert_date,'YYYY') as job_year";
		$order_chack_cond="and b.order_id is null";

		if($cbo_year) $job_year_cond = " and to_char(e.insert_date,'YYYY') =".$cbo_year;
	}
	else if($db_type==0)
	{
		$select_year="year(e.insert_date) as job_year";
		$order_chack_cond=" and b.order_id=' ' ";
		if($cbo_year) $job_year_cond = " and year(e.insert_date) =".$cbo_year;
	}

	$update_row_check=array();
	if($update_mst_id!="")
	{
		$sql_update=sql_select("select id,grey_sys_id,product_id,job_no,order_id,program_no,current_delivery,roll,sys_dtls_id from pro_grey_prod_delivery_dtls where mst_id=$update_mst_id and entry_form=54 and status_active=1 and is_deleted=0");
		foreach($sql_update as $row)
		{
			if($cbo_order_status==1)
			{
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("order_id")]."*".$row[csf("sys_dtls_id")]]["current_delivery"] =$row[csf("current_delivery")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("order_id")]."*".$row[csf("sys_dtls_id")]]["id"] =$row[csf("id")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("order_id")]."*".$row[csf("sys_dtls_id")]]["roll"] =$row[csf("roll")];
			}
			else
			{
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("sys_dtls_id")]]["current_delivery"] =$row[csf("current_delivery")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("sys_dtls_id")]]["id"] =$row[csf("id")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("sys_dtls_id")]]["roll"] =$row[csf("roll")];
			}

			$update_grey_sys_arr[$row[csf("grey_sys_id")]] = $row[csf("grey_sys_id")];
		}
	}

	$all_update_grey_sys_ids = implode(",", $update_grey_sys_arr);
    $upProduction_id_cond=""; $upProductionIdCond="";
    if($db_type==2 && count($all_update_grey_sys_arr)>999)
    {
    	$all_update_grey_sys_arr_chunk=array_chunk($all_update_grey_sys_arr,999);
    	foreach($all_update_grey_sys_arr_chunk as $chunk_arr)
    	{
    		$chunk_arr_value=implode(",",$chunk_arr);
    		$upProductionIdCond.=" a.id in($chunk_arr_value) or ";
    	}

    	$upProduction_id_cond.=" and (".chop($upProductionIdCond,'or ').")";
    }
    else
    {
    	$upProduction_id_cond=" and a.id in($all_update_grey_sys_ids)";
    }

	if($update_mst_id=="")
	{
		if($cbo_order_status==1)
		{
			$sql="select a.id, a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, b.id as dtls_id, b.fabric_description_id as determination_id, b.fabric_shade, b.gsm as gsm, b.width as dia, b.uom, b.color_id, b.body_part_id, b.dia_width_type, b.prod_id, b.batch_id as batch_id, b.no_of_roll as no_of_roll, c.grey_used_qty as grey_used_qty, c.process_loss_perc, c.po_breakdown_id, sum(c.quantity) as quantity, d.po_number,d.file_no,d.grouping, e.job_no, e.job_no_prefix_num, e.buyer_name, $select_year from inv_receive_master a,  pro_finish_fabric_rcv_dtls b ,order_wise_pro_details c,wo_po_break_down d,wo_po_details_master e where a.id=b.mst_id  and b.id=c.dtls_id and c.po_breakdown_id=d.id and d.job_id=e.id and a.entry_form=7 and c.entry_form=7 and c.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active in (1,3) and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.company_id=$cbo_company_id $location_cond $batch_cond $date_cond $buyer_cond  $job_cond $order_cond $file_cond $ref_cond $sys_prod_id_cond $dyeing_source_cond  $dyeing_company_cond $dyeing_location_cond $job_year_cond group by b.id,a.id,a.recv_number,a.receive_date,a.receive_basis,a.knitting_source,b.fabric_description_id,b.fabric_shade,b.gsm,b.width, b.uom, b.color_id, b.body_part_id, b.dia_width_type, b.prod_id,b.batch_id, b.no_of_roll, c.grey_used_qty, c.process_loss_perc, c.po_breakdown_id, d.po_number,d.file_no,d.grouping, e.job_no,e.job_no_prefix_num,e.buyer_name, e.insert_date order by a.id";
		}
		else
		{
			$sql="select a.id, a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, b.id as dtls_id, b.fabric_description_id as determination_id, b.fabric_shade, b.gsm as gsm, b.uom, b.color_id, b.body_part_id, b.dia_width_type, b.width as dia, b.prod_id, b.batch_id as batch_id, b.no_of_roll as no_of_roll, b.grey_used_qty as grey_used_qty, 0 as process_loss_perc, sum(b.receive_qnty) as quantity, '' as po_breakdown_id, '' as po_number, '' as job_no, '' as job_no_prefix_num, '' as buyer_name, '' as job_year from inv_receive_master a,  pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form=7 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.receive_qnty>0 $order_chack_cond and a.company_id=$cbo_company_id $location_cond $batch_cond $date_cond $sys_prod_id_cond $dyeing_source_cond  $dyeing_company_cond $dyeing_location_cond group by b.id,a.id,a.recv_number,a.receive_date,a.receive_basis,a.knitting_source,b.fabric_description_id,b.fabric_shade,b.gsm,b.width,b.uom,b.color_id, b.body_part_id, b.dia_width_type, b.prod_id,b.batch_id, b.no_of_roll, b.grey_used_qty order by a.id";
		}
	}
	else if($update_mst_id>0)
	{
		if($cbo_order_status==1)
		{
			$sql="select a.id, a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, b.id as dtls_id, b.fabric_description_id as determination_id, b.fabric_shade, b.gsm as gsm, b.width as dia, b.uom, b.color_id, b.body_part_id, b.dia_width_type, b.prod_id, b.batch_id as batch_id, b.no_of_roll as no_of_roll, c.grey_used_qty as grey_used_qty, c.process_loss_perc, c.po_breakdown_id, sum(c.quantity) as quantity, d.po_number,d.file_no,d.grouping, e.job_no, e.job_no_prefix_num, e.buyer_name, $select_year from inv_receive_master a,  pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e where a.id=b.mst_id  and b.id=c.dtls_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and a.entry_form=7 and c.entry_form=7 and c.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active in (1,3) and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.company_id=$cbo_company_id $location_cond $batch_cond $date_cond $buyer_cond  $job_cond $order_cond $file_cond $ref_cond $sys_prod_id_cond $dyeing_source_cond  $dyeing_company_cond $dyeing_location_cond $upProduction_id_cond group by b.id,a.id,a.recv_number,a.receive_date,a.receive_basis,a.knitting_source,b.fabric_description_id,b.fabric_shade,b.gsm,b.width,b.uom,b.color_id, b.body_part_id,b.dia_width_type,b.prod_id,b.batch_id,b.no_of_roll, c.grey_used_qty, c.process_loss_perc, c.po_breakdown_id, d.po_number, d.file_no, d.grouping, e.job_no, e.job_no_prefix_num, e.buyer_name, e.insert_date order by a.id"; //$job_year_cond
		}
		else
		{
			$sql="select a.id, a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, b.id as dtls_id, b.fabric_description_id as determination_id, b.fabric_shade, b.gsm as gsm, b.width as dia, b.uom, b.color_id, b.body_part_id, b.dia_width_type, b.prod_id, b.batch_id as batch_id, b.no_of_roll as no_of_roll, b.grey_used_qty as grey_used_qty, 0 as process_loss_perc, sum(b.receive_qnty) as quantity, '' as po_breakdown_id, '' as po_number, '' as job_no, '' as job_no_prefix_num, '' as buyer_name, '' as job_year from inv_receive_master a,  pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form=7 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and b.receive_qnty>0 $order_chack_cond and a.company_id=$cbo_company_id $location_cond $batch_cond $date_cond  and a.id in($hidden_receive_id) and b.prod_id in($hidden_product_id) $sys_prod_id_cond  $dyeing_source_cond  $dyeing_company_cond $dyeing_location_cond $upProduction_id_cond group by b.id,a.id,a.recv_number,a.receive_date,a.receive_basis,a.knitting_source,b.fabric_description_id,b.fabric_shade,b.gsm,b.width,b.uom,b.color_id, b.body_part_id, b.dia_width_type, b.prod_id,b.batch_id,b.no_of_roll, b.grey_used_qty order by a.id";
		}

	}
	//echo $sql;
	$result=sql_select($sql);

	foreach ($result as $row )
	{
		$all_batch_arr[$row[csf("batch_id")]]=$row[csf("batch_id")];
		$all_production_arr[$row[csf("id")]]=$row[csf("id")];
	}

	if(!empty($all_production_arr))
	{
		$all_production_ids = implode(",", $all_production_arr);
	    $all_production_id_cond="";	$productionIdCond="";
	    if($db_type==2 && count($all_production_arr)>999)
	    {
	    	$all_production_arr_chunk=array_chunk($all_production_arr,999);
	    	foreach($all_production_arr_chunk as $chunk_arr)
	    	{
	    		$chunk_arr_value=implode(",",$chunk_arr);
	    		$productionIdCond.=" grey_sys_id in($chunk_arr_value) or ";
	    	}

	    	$all_production_id_cond.=" and (".chop($productionIdCond,'or ').")";
	    }
	    else
	    {
	    	$all_production_id_cond=" and grey_sys_id in($all_production_ids)";
	    }

		$sql_production=sql_select("Select grey_sys_id,product_id,order_id,program_no,current_delivery,sys_dtls_id from pro_grey_prod_delivery_dtls where status_active=1 and is_deleted=0 and entry_form=54 $all_production_id_cond");
		foreach($sql_production as $row)
		{
			if($cbo_order_status==1)
			{
				$sql_production_arr[$row[csf("grey_sys_id")]][$row[csf("product_id")]][$row[csf("order_id")]][$row[csf("sys_dtls_id")]]['prodcut_qty'] +=$row[csf("current_delivery")];
			}
			else
			{
				$sql_production_arr[$row[csf("grey_sys_id")]][$row[csf("product_id")]][$row[csf("sys_dtls_id")]]['prodcut_qty'] +=$row[csf("current_delivery")];
			}
		}
	}

	$sql_for_dia=sql_select("select b.job_no, b.po_break_down_id, b.dia_width FROM wo_pre_cost_fabric_cost_dtls a,
		wo_pre_cos_fab_co_avg_con_dtls b WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		group by b.job_no, b.po_break_down_id, b.dia_width");
	foreach($sql_for_dia as $row)
	{
		$dia_arr[$row[csf("job_no")]][$row[csf("po_break_down_id")]]['dia_width']=$row[csf("dia_width")];
	}

	if($cbo_order_status==1)
	{
		if($order_ids!="")
		{
			$sql_machineDia=sql_select("select a.febric_description_id, b.po_breakdown_id, a.machine_dia
				from inv_receive_master p, pro_grey_prod_entry_dtls a, order_wise_pro_details b
			where p.id=a.mst_id and a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and p.status_active=1 and p.is_deleted=0 and b.po_breakdown_id in ($hidden_order_id) and p.entry_form in(2,22) and a.machine_dia is not NULL group by a.febric_description_id, b.po_breakdown_id, a.machine_dia"); //
		}
	}
	else
	{
		$sql_machineDia=sql_select("select p.booking_no, a.febric_description_id, a.machine_dia
			from inv_receive_master p, pro_grey_prod_entry_dtls a
			where p.id=a.mst_id  and a.status_active=1 and a.is_deleted=0 and p.status_active=1 and p.is_deleted=0 and p.booking_without_order=1 and a.machine_dia is not NULL group by p.booking_no, a.febric_description_id, a.machine_dia");
	}

	$all_machineDia="";
	$mc_dia_arr=array();
	foreach($sql_machineDia as $rows)
	{
		if($cbo_order_status==1)
			$mc_dia_arr[$rows[csf('po_breakdown_id')]][$rows[csf('febric_description_id')]].=$rows[csf('machine_dia')].',';
		else
			$mc_dia_arr[$rows[csf('booking_no')]][$rows[csf('febric_description_id')]].=$rows[csf('machine_dia')].',';
	}
	unset($sql_machineDia);

    $all_batch_arr = array_filter($all_batch_arr);
    if($all_batch_arr)
    {
	    $all_batch_nos = implode(",", $all_batch_arr);
	    $all_batch_no_cond="";	$batchCond="";
	    if($db_type==2 && count($all_batch_arr)>999)
	    {
	    	$all_batch_arr_chunk=array_chunk($all_batch_arr,999);
	    	foreach($all_batch_arr_chunk as $chunk_arr)
	    	{
	    		$chunk_arr_value=implode(",",$chunk_arr);
	    		$batchCond.=" a.id in($chunk_arr_value) or ";
	    	}

	    	$all_batch_no_cond.=" and (".chop($batchCond,'or ').")";
	    }
	    else
	    {
	    	$all_batch_no_cond=" and a.id in($all_batch_nos)";
	    }
		$batch_data=sql_select("select a.id, a.batch_no, a.booking_no_id, a.booking_no, a.booking_without_order, sum(b.roll_no) as roll_no, sum(b.batch_qnty) as batch_qnty from  pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and batch_against <>4 and batch_for not in (2,3) and a.entry_form in (0,7) $all_batch_no_cond group by a.id, a.batch_no, a.booking_no_id, a.booking_no, a.booking_without_order");
		foreach($batch_data as $row)
		{
			$batch_details[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
			$batch_details[$row[csf('id')]]['booking_no_id']=$row[csf('booking_no_id')];
			$batch_details[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
			$batch_details[$row[csf('id')]]['booking_without_order']=$row[csf('booking_without_order')];
			$batch_details[$row[csf('id')]]['roll_no']=$row[csf('roll_no')];
			$batch_details[$row[csf('id')]]['batch_qnty']=$row[csf('batch_qnty')];
		}
	}
	if($update_mst_id!=""){$update_mst_id_cond="and a.booking_id = $update_mst_id ";}else{}
	$receive_sql = sql_select("select a.id, a.recv_number, b.prod_id, b.batch_id, b.body_part_id, b.fabric_description_id, b.gsm, b.fabric_shade, b.dia_width_type, b.uom, sum(b.receive_qnty) as qnty from inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id = b.mst_id and a.entry_form =37 and a.receive_basis =16 and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 $update_mst_id_cond group by a.id, a.recv_number, b.prod_id, b.batch_id, b.body_part_id, b.fabric_description_id, b.gsm, b.fabric_shade, b.dia_width_type, b.uom");

	foreach ($receive_sql as $val)
	{
		$receive_arr[$val[csf("batch_id")]][$val[csf("body_part_id")]][$val[csf("prod_id")]][$val[csf("fabric_shade")]][$val[csf("dia_width_type")]] .= $val[csf("recv_number")].",";
	}

	ob_start();
	?>
	<script type="text/javascript">
		$("#removeQty").click(function()
		{
			// alert('OK');
			if(!$(this).prop('checked'))
			{
				// alert('OK');
				$(".rmvQty").val('');
			}
		})
	</script>
	<div style="width:2000px;" id="main_report_div">
		<form name="delivery_details" id="delivery_details" autocomplete="off" >
			<div id="report_print" style="width:2000px;">
				<table width="1990" class="rpt_table" id="tbl_header" cellpadding="0" cellspacing="1" rules="all">
					<thead>
						<th width="30">Sl</th>
						<th width="120">System Id</th>
						<th width="90">Booking No</th>
						<th width="75">Knitting Source</th>
						<th width="70">Batch No</th>
						<th width="80">Fabric Shade</th>
						<th width="70">Prd. date</th>
						<th width="50">Prod. Id</th>
						<th width="40">Year</th>
						<th width="60">Job No</th>
						<th width="100">Buyer</th>
						<th width="90">Order No</th>
						<th width="70">File No</th>
						<th width="70">Ref No</th>
						<th width="110">Construction </th>
						<th width="110">Composition</th>
						<th width="40">GSM</th>
						<th width="40">Dia</th>
                        <th width="40">UOM</th>
						<th width="100">Grey Dia</th>
						<th width="40">Roll</th>
						<th width="70">Prod. qty</th>
						<th width="100">Grey Used</th>
						<th width="70">Total Delivery</th>
						<th width="70">Balance</th>
						<th width="75" >Cur. Del <input type="checkbox" name="removeQty" id="removeQty" title="Click for remove quantity" checked="checked"></th>
						<th >Roll</th>
					</thead>
				</table>
				<div style="width:1995px; overflow-y:scroll; max-height:200px;font-size:12px; overflow-x:hidden;" id="scroll_body">
					<table width="1990px" class="rpt_table" id="table_body" cellpadding="0" cellspacing="1" rules="all">
						<tbody>
							<?
							$i=1;
							$current_row_array=array();
							foreach($result as $row)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								if($cbo_order_status==1)
								{
									$tot_delivery=$sql_production_arr[$row[csf("id")]][$row[csf("prod_id")]][$row[csf("po_breakdown_id")]][$row[csf("dtls_id")]]['prodcut_qty'];
									$index_pk=$row[csf("id")]."*".$row[csf("prod_id")]."*".$row[csf("po_breakdown_id")]."*".$row[csf("dtls_id")];
								}
								else
								{
									$tot_delivery=$sql_production_arr[$row[csf("id")]][$row[csf("prod_id")]][$row[csf("dtls_id")]]['prodcut_qty'];
									$index_pk=$row[csf("id")]."*".$row[csf("prod_id")]."*".$row[csf("dtls_id")];
								}
								$current_stock=$row[csf("quantity")];


								if($update_mst_id=="")
								{
									if($cbo_status==1)
									{
										if( $current_stock > $tot_delivery)
										{
											?>
											<tr bgcolor="<? echo $bgcolor; ?>">
												<td width="30" align="center">
													<p><? echo $i; ?>
													<input type="hidden" id="hidesysid_<? echo $i;?>" name="hidesysid_<? echo $i;?>" value="<? echo $row[csf("id")];?>"  />
													<input type="hidden" id="body_part_id_<? echo $i;?>" name="body_part_id_<? echo $i;?>" value="<? echo $row[csf("body_part_id")];?>" />
													<input type="hidden" id="color_id_<? echo $i;?>" name="color_id_<? echo $i;?>" value="<? echo $row[csf("color_id")];?>" />
													<input type="hidden" id="dia_width_type_<? echo $i;?>" name="dia_width_type_<? echo $i;?>" value="<? echo $row[csf("dia_width_type")];?>" />
													<input type="hidden" id="hideuom_<? echo $i;?>" name="hideuom_<? echo $i;?>" />
													</p>
												</td>
												<td width="120">
													<input type="hidden" id="hidesysnum_<? echo $i;?>" name="hidesysnum_<? echo $i;?>" value="<? echo $row[csf("recv_number")];?>"  />
													<?
													echo $row[csf("recv_number")];
													?>
												</td>
												<td width="90" align="center">
													<p>
														<input type="hidden" id="hidefindtls_<? echo $i;?>" name="hidefindtls_<? echo $i;?>" value="<? echo $row[csf("dtls_id")];?>"  />
														<input type="hidden" id="hidegreyused_<? echo $i;?>" name="hidegreyused_<? echo $i;?>" value="<? echo $row[csf("grey_used_qty")];?>"  />
														<input type="hidden" id="hidebatch_<? echo $i;?>" name="hidebatch_<? echo $i;?>" value="<? echo $row[csf("batch_id")];?>"  />
														<? echo $batch_details[$row[csf("batch_id")]]['booking_no']; ?>
													</p>
												</td>
												<td width="75" align="center">
													<p>
														<?
														if($row[csf("knitting_source")]==1)  echo "In-House"; if($row[csf("knitting_source")]==3) echo "Sub-Contract";
														?>
													</p>
												</td>
												<td width="70" align="center">
													<p>
													<input type="hidden" id="hideprogrum_<? echo $i;?>" name="hideprogrum_<? echo $i;?>" value="<? echo $row[csf("batch_id")];?>"  />
													<?
													echo $batch_details[$row[csf("batch_id")]]['batch_no'];
													?></p>
												</td>
												<td width="80" align="center">
													<input type="hidden" id="hidefabshade_<? echo $i;?>" name="hidefabshade_<? echo $i;?>" value="<? echo $row[csf("fabric_shade")];?>"  />
													<?
													echo $fabric_shade[$row[csf("fabric_shade")]];
													?>
												</td>
												<td width="70" align="center"><p><?  if($row[csf("receive_date")]!='0000-00-00' && $row[csf("receive_date")]!='')  echo change_date_format($row[csf("receive_date")]); else echo ""; ?></p></td>
												<td width="50" align="center">
													<input type="hidden" id="hideprodid_<? echo $i;?>" name="hideprodid_<? echo $i;?>" value="<? echo $row[csf("prod_id")];?>"  />
													<? echo $row[csf("prod_id")]; ?>
												</td>
												<td width="40" align="center"><p><? echo $row[csf("job_year")]; ?></p></td>
												<td width="60" align="center">
													<p>
														<input type="hidden" id="hidejob_<? echo $i;?>" name="hidejob_<? echo $i;?>" value="<? echo $row[csf("job_no")];?>"  />
														<? echo $row[csf("job_no")]; ?>
													</p>
												</td>
												<td width="100">
													<p>
														<?
														if($batch_details[$row[csf("batch_id")]]['booking_without_order']==1)
														{
															echo $buyer_array[$non_order_buyer[$batch_details[$row[csf("batch_id")]]['booking_no_id']]];
														}
														else
														{
															echo $buyer_array[$row[csf("buyer_name")]];
														}
														?></p>
													</td>
													<td width="90">
														<p>
															<input type="hidden" id="hideorder_<? echo $i;?>" name="hideorder_<? echo $i;?>" value="<? echo $row[csf("po_breakdown_id")];?>"  />
															<? echo $row[csf("po_number")]; ?>
														</p>
													</td>
													<td width="70"><? echo $row[csf("file_no")]; ?></td>
													<td width="70"><? echo $row[csf("grouping")]; ?></td>
													<td width="110">
														<p>
															<input type="hidden" id="hideconstruction_<? echo $i;?>" name="hideconstruction_<? echo $i;?>" value="<? echo $row[csf("determination_id")];?>"  />
															<? echo $construction_arr[$row[csf("determination_id")]]; ?>
														</p>
													</td>
													<td width="110">
														<p>
															<input type="hidden" id="hidecomposition_<? echo $i;?>" name="hidecomposition_<? echo $i;?>" value="<? echo $composition_arr[$row[csf("determination_id")]];?>"  />
															<? echo $composition_arr[$row[csf("determination_id")]]; ?>
														</p>
													</td>
													<td width="40" align="center">
														<p>
															<input type="hidden" id="hidegsm_<? echo $i;?>" name="hidegsm_<? echo $i;?>" value="<? echo $row[csf("gsm")]; ?>"  />
															<? echo $row[csf("gsm")]; ?>
														</p>
													</td>
												<td width="40" align="center">
													<p>
													<input type="hidden" id="hidedia_<? echo $i;?>" name="hidedia_<? echo $i;?>" value="<? echo $row[csf("dia")]; ?>"  />
													<? echo $row[csf("dia")]; ?>
													</p>
												</td>
                                            	<td width="40" align="center">
													<input type="hidden" id="hideuom_<? echo $i;?>" name="hideuom_<? echo $i;?>" value="<? echo $row[csf("uom")]; ?>"  />
													<? echo $unit_of_measurement[$row[csf("uom")]]; ?>
												</td>
												<td width="100" align="center">
													<p>
														<? $mc_dia="";
														if($cbo_order_status==1)
															$mc_dia=implode(",",array_unique(explode(",",$mc_dia_arr[$row[csf('po_breakdown_id')]][$row[csf('determination_id')]])));
														else
															$mc_dia=$mc_dia_arr[$batch_details[$row[csf("batch_id")]]['booking_no']][$row[csf('determination_id')]];

														echo chop($mc_dia,','); ?>
													</p>
												</td>
												<td width="40" align="center">
													<p>
														<? echo $row[csf("no_of_roll")]; ?>
													</p>
												</td>
												<td width="70" align="right" ><p><? echo number_format($row[csf("quantity")],2); $total_stock+=$row[csf("quantity")]; ?></p></td>
												<td width="100" align="right" id="greyQtyTd_<? echo $i; ?>">
													<p>
														<?
														$balance=($row[csf("quantity")]-$tot_delivery);

														if($process_loss_method==1)
														{
															//grey_qnty = (finish_qnty + reject_qnty) + (finish_qnty + reject_qnty) * txtProcessQnty/100;
															$grey_used_qty = $balance + ($row[csf("process_loss_perc")]*$balance)/100;
														}
														else
														{
															//grey_qnty = (finish_qnty + reject_qnty) / (1 - txtProcessQnty/100);

															$grey_used_qty = $balance / ( 1- $row[csf("process_loss_perc")]/100);
														}

														//$grey_used_qty = $balance + ($row[csf("process_loss_perc")]*$balance)/100;
														echo number_format($grey_used_qty,2);
														$total_grey_used+=$grey_used_qty;

														//echo number_format($row[csf("grey_used_qty")],2);
														//$total_grey_used+=$row[csf("grey_used_qty")];
														?>
													</p>
												</td>
												<td width="70" align="right" ><p>
													<?
													echo number_format($tot_delivery,2);
													$gt_tot_delivery+=$tot_delivery;
													?></p>
												</td>
												<td width="70"align="right" id="totalqtyTd_<? echo $i; ?>">
													<p>
														<?

															echo number_format($balance,2);
															$total_balance+=$balance;
														?>
													</p>
												</td>
												<td width="75">
													<p>
														<input type="hidden" id="hiddendtlsid_<? echo $i;?>" name="hiddendtlsid_<? echo $i;?>"  style="width:60px;" value="" />
														<input type="text" id="txtcurrentdelivery_<? echo $i;?>" name="txtcurrentdelivery_<? echo $i;?>" class="text_boxes_numeric rmvQty" style="width:60px;" value="<? echo number_format($balance,2,".",""); $total_delivey_balance += $balance; ?>" onBlur="setHideval(<? echo $i; ?>)" onkeyup="fncGreyUsedQty(<? echo $i; ?>);"/>
														<input type="hidden" class="hide_cur_qty" id="hidden_current_val_<? echo $i;?>" value="<? echo $balance;?>">
														<input type="hidden" id="txt_get_value_fins_recv_<? echo $i;?>" value="<? echo $row[csf("quantity")]; ?>" name="txt_get_value_fins_recv_<? echo $i;?>">
														<input type="hidden" id="txt_process_loss_perc_<? echo $i;?>" value="<? echo $row[csf("process_loss_perc")]; ?>" name="txt_process_loss_perc_<? echo $i;?>">
													</p>
												</td>
												<td >
													<p>
														<input type="text" id="txtroll_<? echo $i;?>" name="txtroll_<? echo $i;?>" class="text_boxes_numeric" style="width:50px;" value="" onBlur="total_roll(<? echo $i; ?>)"  />
														<input type="hidden" id="hideroll_<? echo $i;?>" value=""  >
													</p>
												</td>
											</tr>
											<?
                                            $i++;
                                        }
									}
									else
									{
										if($current_stock<=$tot_delivery)
										{
											?>
											<tr bgcolor="<? echo $bgcolor; ?>">
												<td width="30" align="center"><p><? echo $i; ?>
												<input type="hidden" id="hidesysid_<? echo $i;?>" name="hidesysid_<? echo $i;?>" value="<? echo $row[csf("id")];?>"  />
												<input type="hidden" id="body_part_id_<? echo $i;?>" name="body_part_id_<? echo $i;?>" value="<? echo $row[csf("body_part_id")];?>" />
												<input type="hidden" id="color_id_<? echo $i;?>" name="color_id_<? echo $i;?>" value="<? echo $row[csf("color_id")];?>" />
												<input type="hidden" id="dia_width_type_<? echo $i;?>" name="dia_width_type_<? echo $i;?>" value="<? echo $row[csf("dia_width_type")];?>" />
												<input type="hidden" id="hideuom_<? echo $i;?>" name="hideuom_<? echo $i;?>" />

											</p></td>
											<td width="120"><p><input type="hidden" id="hidesysnum_<? echo $i;?>" name="hidesysnum_<? echo $i;?>" value="<? echo $row[csf("recv_number")];?>"  />
												<?
												echo $row[csf("recv_number")];
												?>
											</p></td>
											<td width="90" align="center">
												<p>
													<input type="hidden" id="hidefindtls_<? echo $i;?>" name="hidefindtls_<? echo $i;?>" value="<? echo $row[csf("dtls_id")];?>"  />
													<input type="hidden" id="hidegreyused_<? echo $i;?>" name="hidegreyused_<? echo $i;?>" value="<? echo $row[csf("grey_used_qty")];?>"  />
													<input type="hidden" id="hidebatch_<? echo $i;?>" name="hidebatch_<? echo $i;?>" value="<? echo $row[csf("batch_id")];?>"  />
													<? echo $batch_details[$row[csf("batch_id")]]['booking_no']; ?>
												</p>
											</td>
											<td width="75" align="center">
												<p>
													<?
													if($row[csf("knitting_source")]==1)  echo "In-House"; if($row[csf("knitting_source")]==3) echo "Sub-Contract";
													?>
												</p>
											</td>
											<td width="70" align="center">
												<p>
													<input type="hidden" id="hideprogrum_<? echo $i;?>" name="hideprogrum_<? echo $i;?>" value="<? echo $row[csf("batch_id")];?>"  />
													<? echo $batch_details[$row[csf("batch_id")]]['batch_no']; ?></p>
												</td>
												<td width="80" align="center">
													<p>
														<input type="hidden" id="hidefabshade_<? echo $i;?>" name="hidefabshade_<? echo $i;?>" value="<? echo $row[csf("fabric_shade")];?>"  />
														<? echo $fabric_shade[$row[csf("fabric_shade")]]; ?></p>
													</td>
													<td width="70" align="center">
														<p><?  if($row[csf("receive_date")]!='0000-00-00' && $row[csf("receive_date")]!='')  echo change_date_format($row["receive_date"]); else echo ""; ?></p></td>
														<td width="50" align="center">
															<p>
																<input type="hidden" id="hideprodid_<? echo $i;?>" name="hideprodid_<? echo $i;?>" value="<? echo $row[csf("prod_id")];?>"  />
																<? echo $row[csf("prod_id")]; ?>
															</p>
														</td>
														<td width="40" align="center"><p><? echo $row[csf("job_year")]; ?></p></td>
														<td width="60" align="center"><p>
															<input type="hidden" id="hidejob_<? echo $i;?>" name="hidejob_<? echo $i;?>" value="<? echo $row[csf("job_no")];?>"  />
															<? echo $row[csf("job_no")]; ?>
														</p></td>
														<td width="100"><p>
															<?
															if($batch_details[$row[csf("batch_id")]]['booking_without_order']==1)
															{
																echo $buyer_array[$non_order_buyer[$batch_details[$row[csf("batch_id")]]['booking_no_id']]];
															}
															else
															{
																echo $buyer_array[$row[csf("buyer_namey")]];
															}
															?></p></td>
															<td width="90"><p>
																<input type="hidden" id="hideorder_<? echo $i;?>" name="hideorder_<? echo $i;?>" value="<? echo $row[csf("po_breakdown_id")];?>"  />
																<? echo $row[csf("po_number")]; ?>
															</p></td>
															<td width="70"><p>
																<? echo $row[csf("file_no")]; ?>
															</p></td>
															<td width="70">
																<p>
																	<?
																	if($batch_details[$row[csf("batch_id")]]['booking_without_order']==1)
																	{
																		$internal_ref = $non_order_grouping[$batch_details[$row[csf("batch_id")]]['booking_no_id']];
																	}
																	else
																	{
																		$internal_ref = $row[csf("grouping")];
																	}
																	echo $internal_ref;
																	?>
																</p>
															</td>
															<td width="110"><p>
																<input type="hidden" id="hideconstruction_<? echo $i;?>" name="hideconstruction_<? echo $i;?>" value="<? echo $row[csf("detarmination_id")];?>"  />
																<? echo $construction_arr[$row["determination_id"]]; ?>
															</p></td>
															<td width="110"><p>
																<input type="hidden" id="hidecomposition_<? echo $i;?>" name="hidecomposition_<? echo $i;?>" value="<? echo $composition_arr[$row[csf("detarmination_id")]];?>"  />
																<? echo $composition_arr[$row[csf("determination_id")]]; ?>
															</p></td>
															<td width="40" align="center"><p>
																<input type="hidden" id="hidegsm_<? echo $i;?>" name="hidegsm_<? echo $i;?>" value="<? echo $row[csf("gsm")]; ?>"  />
																<? echo $row["gsm"]; ?>
															</p></td>
															<td width="40" align="center"><p>
																<input type="hidden" id="hidedia_<? echo $i;?>" name="hidedia_<? echo $i;?>" value="<? echo $row[csf("dia")]; ?>"  />
																<? echo $row[csf("dia")]; ?>
															</p></td>

															<td width="40" align="center">
																<input type="hidden" id="hideuom_<? echo $i;?>" name="hideuom_<? echo $i;?>" value="<? echo $row[csf("uom")]; ?>"  />
																<? echo $unit_of_measurement[$row[csf("uom")]]; ?>
															</td>

															<td width="100" align="center"><p>
																<? $mc_dia="";
																if($cbo_order_status==1)
																	$mc_dia=implode(",",array_unique(explode(",",$mc_dia_arr[$row[csf('po_breakdown_id')]][$row[csf('determination_id')]])));
																else
																	$mc_dia=$mc_dia_arr[$batch_details[$row[csf("batch_id")]]['booking_no']][$row[csf('determination_id')]];

																echo chop($mc_dia,','); ?>
															</p></td>
															<td width="40" align="center"><p>
																<? echo $row[csf("no_of_roll")]; ?>
															</p></td>
															<td width="70" align="right" ><p><? echo number_format($row[csf("quantity")],2); $total_stock+=$row[csf("quantity")]; ?></p></td>
															<td width="100" align="right" id="greyQtyTd_<? echo $i; ?>">
																<p>
																	<?
																	$balance=($row[csf("quantity")]-$tot_delivery);

																	if($process_loss_method==1)
																	{
																		//grey_qnty = (finish_qnty + reject_qnty) + (finish_qnty + reject_qnty) * txtProcessQnty/100;
																		$grey_used_qty = $balance + ($row[csf("process_loss_perc")]*$balance)/100;
																	}
																	else
																	{
																		//grey_qnty = (finish_qnty + reject_qnty) / (1 - txtProcessQnty/100);

																		$grey_used_qty = $balance / ( 1- $row[csf("process_loss_perc")]/100);
																	}

																	//$grey_used_qty = $balance + ($row[csf("process_loss_perc")]*$balance)/100;
																	echo number_format($grey_used_qty,2);
																	$total_grey_used+=$grey_used_qty;

																	//echo number_format($row[csf("grey_used_qty")],2);
																	//$total_grey_used+=$row[csf("grey_used_qty")];
																	?>
																</p>
															</td>
															<td width="70" align="right" >
																<?
																echo number_format($tot_delivery,2);
																$gt_tot_delivery+=$tot_delivery;
																?>
															</td>
															<td width="70"align="right" id="totalqtyTd_<? echo $i; ?>">
															<p>
																<?
																//$balance=($row[csf("quantity")]-$tot_delivery);
																echo number_format($balance,2);
																$total_balance+=$balance; ?>
															</p>
															</td>
															<td width="75">
																<p><input type="hidden" id="hiddendtlsid_<? echo $i;?>" name="hiddendtlsid_<? echo $i;?>"  style="width:60px;" value="" />
																	<input type="text" id="txtcurrentdelivery_<? echo $i;?>" name="txtcurrentdelivery_<? echo $i;?>" class="text_boxes_numeric rmvQty" style="width:60px;" value="<? echo number_format($balance,2,".",""); $total_delivey_balance += $balance;?>" onBlur="setHideval(<? echo $i; ?>)" onkeyup="fncGreyUsedQty(<? echo $i;?>)" />
																	<input class="hide_cur_qty" type="hidden" id="hidden_current_val_<? echo $i;?>" value="<? echo $balance; ?>">
																	<input type="hidden" id="txt_process_loss_perc_<? echo $i;?>" value="<? echo $row[csf("process_loss_perc")]; ?>" name="txt_process_loss_perc_<? echo $i;?>">
																</p>
															</td>
															<td ><p>
																<input type="text" id="txtroll_<? echo $i;?>" name="txtroll_<? echo $i;?>" class="text_boxes_numeric" style="width:50px;" value="" onBlur="total_roll(<? echo $i; ?>)"  />
																<input type="hidden" id="hideroll_<? echo $i;?>" value=""  >
															</p>
														</td>
													</tr>
											<?
											$i++;
										}
									}
								}
								else
								{
									if($update_row_check[$index_pk]["current_delivery"]>0)
									{

										if($receive_arr[$row[csf("batch_id")]][$row[csf("body_part_id")]][$row[csf("prod_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]])
										{
											$disabled = "disabled";
										}
										else
										{
											$disabled = "";
										}


										?>
										<tr bgcolor="<? echo $bgcolor; ?>" style="background-color:#FF6;">
											<td width="30" align="center"><p><? echo $i; ?>
											<input type="hidden" id="hidesysid_<? echo $i;?>" name="hidesysid_<? echo $i;?>" value="<? echo $row[csf("id")];?>"  />

											<input type="hidden" id="body_part_id_<? echo $i;?>" name="body_part_id_<? echo $i;?>" value="<? echo $row[csf("body_part_id")];?>" />
											<input type="hidden" id="color_id_<? echo $i;?>" name="color_id_<? echo $i;?>" value="<? echo $row[csf("color_id")];?>" />
											<input type="hidden" id="dia_width_type_<? echo $i;?>" name="dia_width_type_<? echo $i;?>" value="<? echo $row[csf("dia_width_type")];?>" />
											<input type="hidden" id="hideuom_<? echo $i;?>" name="hideuom_<? echo $i;?>" />

											</p></td>
											<td width="120"><p><input type="hidden" id="hidesysnum_<? echo $i;?>" name="hidesysnum_<? echo $i;?>" value="<? echo $row[csf("recv_number")];?>"  />
											<?
											echo $row[csf("recv_number")];
											?>
											</p></td>
											<td width="90" align="center"><p>
											<input type="hidden" id="hidefindtls_<? echo $i;?>" name="hidefindtls_<? echo $i;?>" value="<? echo $row[csf("dtls_id")];?>"  />
											<input type="hidden" id="hidegreyused_<? echo $i;?>" name="hidegreyused_<? echo $i;?>" value="<? echo $row[csf("grey_used_qty")];?>"  />
											<input type="hidden" id="hidebatch_<? echo $i;?>" name="hidebatch_<? echo $i;?>" value="<? echo $row[csf("batch_id")];?>"  />
											<? echo $batch_details[$row[csf("batch_id")]]['booking_no']; ?>
											</p></td>
											<td width="75" align="center"><p>
											<?
											if($row[csf("knitting_source")]==1)  echo "In-House"; if($row[csf("knitting_source")]==3) echo "Sub-Contract";
											?>
											</p></td>
											<td width="70" align="center"><p>
											<input type="hidden" id="hideprogrum_<? echo $i;?>" name="hideprogrum_<? echo $i;?>" value="<? echo $row[csf("batch_id")];?>"  />
											<? echo  $batch_details[$row[csf("batch_id")]]['batch_no']; ?></p></td>
											<td width="80" align="center"><p>
												<input type="hidden" id="hidefabshade_<? echo $i;?>" name="hidefabshade_<? echo $i;?>" value="<? echo $row[csf("fabric_shade")];?>"  />
												<? echo $fabric_shade[$row[csf("fabric_shade")]]; ?></p></td>
												<td width="70" align="center"><p><?  if($row[csf("receive_date")]!='0000-00-00' && $row[csf("receive_date")]!='')  echo change_date_format($row[csf("receive_date")]); else echo ""; ?></p></td>
												<td width="50" align="center"><p>
													<input type="hidden" id="hideprodid_<? echo $i;?>" name="hideprodid_<? echo $i;?>" value="<? echo $row[csf("prod_id")];?>"  />
													<? echo $row[csf("prod_id")]; ?>
												</p>
											</td>
											<td width="40" align="center"><p><? echo $row[csf("job_year")]; ?></p></td>
											<td width="60" align="center">
												<input type="hidden" id="hidejob_<? echo $i;?>" name="hidejob_<? echo $i;?>" value="<? echo $row[csf("job_no")];?>"  />
												<? echo $row[csf("job_no")]; ?>
											</td>
											<td width="100"><p>
												<?
												if($batch_details[$row[csf("batch_id")]]['booking_without_order']==1)
												{
													echo $buyer_array[$non_order_buyer[$batch_details[$row[csf("batch_id")]]['booking_no_id']]];
												}
												else
												{
													echo $buyer_array[$row[csf("buyer_name")]];
												}
												?></p>
											</td>
											<td width="90">
												<p>
													<input type="hidden" id="hideorder_<? echo $i;?>" name="hideorder_<? echo $i;?>" value="<? echo $row[csf("po_breakdown_id")];?>"  />
													<? echo $row[csf("po_number")]; ?>
												</p>
											</td>
											<td width="70"><? echo $row[csf("file_no")]; ?></td>
											<?
											if($batch_details[$row[csf("batch_id")]]['booking_without_order']==1)
											{
												$internal_ref = $non_order_grouping[$batch_details[$row[csf("batch_id")]]['booking_no_id']];
											}else{
												$internal_ref = $row[csf("grouping")];
											}
											?>
											<td width="70"><? echo $internal_ref;//$row[csf("grouping")]; ?></td>
											<td width="110">
												<p>
													<input type="hidden" id="hideconstruction_<? echo $i;?>" name="hideconstruction_<? echo $i;?>" value="<? echo $row[csf("determination_id")];?>"  />
													<? echo $construction_arr[$row[csf("determination_id")]]; ?>
												</p>
											</td>
											<td width="110">
												<p>
													<input type="hidden" id="hidecomposition_<? echo $i;?>" name="hidecomposition_<? echo $i;?>" value="<? echo $composition_arr[$row[csf("determination_id")]];?>"  />
													<? echo $composition_arr[$row[csf("determination_id")]]; ?>
												</p>
											</td>
											<td width="40" align="center">
												<p>
													<input type="hidden" id="hidegsm_<? echo $i;?>" name="hidegsm_<? echo $i;?>" value="<? echo $row[csf("gsm")]; ?>"  />
													<? echo $row[csf("gsm")]; ?>
												</p>
											</td>
											<td width="40" align="center"><p>
												<input type="hidden" id="hidedia_<? echo $i;?>" name="hidedia_<? echo $i;?>" value="<? echo $row[csf("dia")]; ?>"  />
												<? echo $row[csf("dia")]; ?>
											</p>
											</td>
											<td width="40" align="center">
											<input type="hidden" id="hideuom_<? echo $i;?>" name="hideuom_<? echo $i;?>" value="<? echo $row[csf("uom")]; ?>"  />
												<? echo $unit_of_measurement[$row[csf("uom")]]; ?>
											</td>

											<td width="100" align="center">
											<p>
												<? $mc_dia="";
												if($cbo_order_status==1)
													$mc_dia=implode(",",array_unique(explode(",",$mc_dia_arr[$row[csf('po_breakdown_id')]][$row[csf('determination_id')]])));
												else
													$mc_dia=$mc_dia_arr[$batch_details[$row[csf("batch_id")]]['booking_no']][$row[csf('determination_id')]];

												echo chop($mc_dia,','); ?>
											</p>
											</td>
											<td width="40" align="center"><? echo $row[csf("no_of_roll")]; ?></td>
											<td width="70" align="right" ><p><? echo number_format($row[csf("quantity")],2); $total_stock+=$row[csf("quantity")]; ?></p></td>
											<td width="100" align="right" id="greyQtyTd_<? echo $i; ?>">
												<p>
													<?
													if($process_loss_method==1)
													{
														//grey_qnty = (finish_qnty + reject_qnty) + (finish_qnty + reject_qnty) * txtProcessQnty/100;
														$grey_used_qty = $update_row_check[$index_pk]["current_delivery"] + ($row[csf("process_loss_perc")]*$update_row_check[$index_pk]["current_delivery"])/100;
													}
													else
													{
														//grey_qnty = (finish_qnty + reject_qnty) / (1 - txtProcessQnty/100);

														$grey_used_qty = $update_row_check[$index_pk]["current_delivery"] / ( 1- $row[csf("process_loss_perc")]/100);
													}

													//$grey_used_qty = $update_row_check[$index_pk]["current_delivery"] + ($row[csf("process_loss_perc")]*$update_row_check[$index_pk]["current_delivery"])/100;
													echo number_format($grey_used_qty,2);
													$total_grey_used+=$grey_used_qty;
													//echo number_format($row[csf("grey_used_qty")],2);
													//$total_grey_used+=$row[csf("grey_used_qty")];
													?>
												</p>
											</td>
											<td width="70" align="right" >
											<p>
												<?
												$tot_delivery=$tot_delivery-$update_row_check[$index_pk]["current_delivery"];
												echo number_format($tot_delivery,2);
												$gt_tot_delivery+=$tot_delivery;
												?>
											</p>
											</td>
											<td width="70"align="right" id="totalqtyTd_<? echo $i; ?>"><p><? $balance=($row[csf("quantity")]-$tot_delivery); echo number_format($balance,2); $total_balance+=$balance; ?></p></td>
											<td width="75">
											<p><input type="hidden" id="hiddendtlsid_<? echo $i;?>" name="hiddendtlsid_<? echo $i;?>"  style="width:60px;" value="<? echo $update_row_check[$index_pk]["id"]; ?>" />
												<input type="text" id="txtcurrentdelivery_<? echo $i;?>" name="txtcurrentdelivery_<? echo $i;?>" class="text_boxes_numeric rmvQty" style="width:60px;" value="<? echo $update_row_check[$index_pk]["current_delivery"]; $total_delivey_balance+=$update_row_check[$index_pk]["current_delivery"]; ?>" onBlur="setHideval(<? echo $i;?>)" onkeyup="fncGreyUsedQty(<? echo $i;?>)" <? echo $disabled;?>/>
												<input type="hidden" class="hide_cur_qty" id="hidden_current_val_<? echo $i;?>" value="<? echo $update_row_check[$index_pk]["current_delivery"]; ?>">
												<input type="hidden" id="txt_process_loss_perc_<? echo $i;?>" value="<? echo $row[csf("process_loss_perc")]; ?>" name="txt_process_loss_perc_<? echo $i;?>">
											</p>
											</td>
											<td >
											<p>
												<input type="text" id="txtroll_<? echo $i;?>" name="txtroll_<? echo $i;?>" class="text_boxes_numeric" style="width:50px;" value="<? echo $update_row_check[$index_pk]["roll"]; $to_roll+=$update_row_check[$index_pk]["roll"]; ?>" onBlur="total_roll(<? echo $i;?>)" />
												<input type="hidden" id="hideroll_<? echo $i;?>" value="<? echo $update_row_check[$index_pk]["roll"]; ?>"  >
											</p>
											</td>
										</tr>
										<?
										$i++;
									}
								}
							}
					?>
				</tbody>
				<tfoot>
					<th colspan="21" align="right">Total:</th>
					<th width="70"><? echo number_format($total_stock,2); ?></th>
					<th width="100" id="total_grey_used" align="right"><? echo number_format($total_grey_used,2); ?></th>
					<th width="70"><? echo number_format($gt_tot_delivery,2); ?></th>
					<th width="70"><? echo number_format($total_balance,2); ?></th>
					<th width="75" id="total_current_val" align="right"> <? echo number_format($total_delivey_balance,2); ?></th>
					<th id="total_roll" align="right"><? echo number_format($to_roll,0); ?></th>
				</tfoot>
			</table>
		</div>
	</div>
	<table width="1990" class="rpt_table" id="tbl_foot" cellpadding="0" cellspacing="1" rules="all">
		<tr>
			<td colspan="26" height="30" valign="middle" align="center" class="button_container">
				<?
				echo create_drop_down( "cbo_template_id", 100, $report_template_list,'', 0, '', 0, "");
				echo load_submit_buttons( $permission, "fnc_prod_delivery",0,1 ,"fnResetForm()",1) ;
				?>
				<input id="Print2" class="formbutton" type="button" style="width:80px" onClick="fnc_prod_delivery(5)" name="print2" value="Print 2">
				<input id="Print3" class="formbutton" type="button" style="width:80px" onClick="fnc_prod_delivery(6)" name="print3" value="Print 3">
				<input id="Print4" class="formbutton" type="button" style="width:80px" onClick="fnc_prod_delivery(7)" name="print3" value="Print 4">
			</td>

		</tr>
	</table>
</form>
</div>
<?

?>

<?
if($action=="check_fin_fab_recv_qty_action")
{
	echo "document.getElementById('txt_get_value_delv_entry').value = '".$row[csf("current_delivery")]."';\n";

	exit();
}
?>
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


if ($action=="save_update_delete")
{

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }

		for($j=1;$j<=$total_row;$j++)
		{
			$hidefindtls="hidefindtls_".$j;
			if ($$hidefindtls!="")
			{
				$all_dtls_ids .= $$hidefindtls.",";
			}
		}

		$all_dtls_ids = chop($all_dtls_ids,",");

		$production_sql = sql_select("SELECT a.id, b.id as dtls_id, c.po_breakdown_id, c.is_sales, b.prod_id, b.batch_id, d.batch_no, b.fabric_shade, sum(c.quantity) as quantity
			from inv_receive_master a, pro_finish_fabric_rcv_dtls b , order_wise_pro_details c, pro_batch_create_mst d
			where a.id=b.mst_id and  b.id=c.dtls_id and c.entry_form=7 and c.trans_type=1 and c.status_active=1 and c.is_deleted=0  and a.entry_form=7
			and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_id and b.id in ($all_dtls_ids) and b.batch_id=d.id
			group by b.id,a.id,b.prod_id, b.batch_id, c.po_breakdown_id, c.is_sales, d.batch_no, b.fabric_shade
			union all
			SELECT a.id, b.id as dtls_id, 0 as po_breakdown_id, 0 as is_sales, b.prod_id, b.batch_id, d.batch_no, b.fabric_shade, sum(b.receive_qnty) as quantity
			from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst d
			where a.id=b.mst_id and a.entry_form=7 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_id and b.id in ($all_dtls_ids) and b.order_id is null and b.batch_id=d.id
			group by b.id,a.id,b.prod_id,b.batch_id, d.batch_no, b.fabric_shade");

		foreach ($production_sql as $val)
		{
			if($val[csf("is_sales")] != 1){
				$production_is_sales=0;
			}else{
				$production_is_sales=1;
			}

			if($val[csf("po_breakdown_id")]==""){
				$production_order = 0;
			}else{
				$production_order = $val[csf("po_breakdown_id")];
			}

			$production_arr[$val[csf("dtls_id")]][$production_is_sales][$production_order]["qnty"] += $val[csf("quantity")];

			$production_data_array[$val[csf("dtls_id")]]['prod_id'] =$val[csf("prod_id")];
			$production_data_array[$val[csf("dtls_id")]]['batch_id'] =$val[csf("batch_id")];
			$production_data_array[$val[csf("dtls_id")]]['batch_no'] =$val[csf("batch_no")];
			$production_data_order_check_array[$val[csf("dtls_id")]][$val[csf("po_breakdown_id")]] =$val[csf("po_breakdown_id")];
			$production_data_array[$val[csf("dtls_id")]]['fabric_shade'] =$val[csf("fabric_shade")]*1;
		}

		$delivery_sql = sql_select("select product_id,order_id,current_delivery,roll,sys_dtls_id, is_sales from pro_grey_prod_delivery_dtls where entry_form=54 and sys_dtls_id in ($all_dtls_ids) and  status_active=1 and is_deleted=0");
		foreach ($delivery_sql as $val)
		{
			if($val[csf("is_sales")] != 1){
				$delivery_is_sales=0;
			}else{
				$delivery_is_sales=1;
			}
			if($val[csf("order_id")]==""){
				$delivery_order = 0;
			}else{
				$delivery_order = $val[csf("order_id")];
			}
			$delivery_arr[$val[csf("sys_dtls_id")]][$delivery_is_sales][$delivery_order]["qnty"] += $val[csf("current_delivery")];
		}

		/*echo "10**";
		print_r($production_arr);
		die;*/

		if( str_replace("'","",$update_mst_id) == "" )
		{
			$id = return_next_id_by_sequence("PRO_GREY_PROD_DELI_MST_PK_SEQ", "pro_grey_prod_delivery_mst", $con);

			$new_mrr_number = explode("*", return_next_id_by_sequence("PRO_GREY_PROD_DELI_MST_PK_SEQ", "pro_grey_prod_delivery_mst",$con,1,$cbo_company_id,'FDS',54,date("Y",time())));

			$field_array="id,sys_number_prefix,sys_number_prefix_num,sys_number,entry_form,fin_pord_type,delevery_date,company_id,location_id,buyer_id,inserted_by,insert_date,remarks,knitting_source,knitting_company,knitting_location,delivery_company,delivery_location";
			$data_array="(".$id.",'".$new_mrr_number[1]."','".$new_mrr_number[2]."','".$new_mrr_number[0]."',54,".$cbo_order_status.",".$txt_delevery_date.",".$cbo_company_id.",".$cbo_location_id.",".$cbo_buyer_id.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."',".$txt_remarks.",".$cbo_dyeing_source.",".$cbo_dyeing_company.",".$cbo_location_dyeing.",".$cbo_deli_company_id.",".$cbo_deli_location_id.")";
		}

		$field_array_dtls="id,mst_id,entry_form,grey_sys_id,sys_dtls_id,grey_sys_number,program_no,product_id,job_no,order_id,determination_id,gsm,dia,current_delivery,roll,batch_id,fabric_shade,inserted_by,insert_date,is_sales,bodypart_id,color_id,width_type,uom,grey_used_qnty";

		$k=1;
		for($i=1;$i<=$total_row;$i++)
		{
			$dtls_id = return_next_id_by_sequence("PRO_GREY_PROD_DELI_DTLS_PK_SEQ", "pro_grey_prod_delivery_dtls", $con);
			$ref_dtls_id=$dtls_id;

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
			$txt_roll="txtroll_".$i;
			$hidebatch="hidebatch_".$i;
			$fabshade_id="hidefabshade_".$i;
			$body_part_id="body_part_id_".$i;
			$color_id="color_id_".$i;
			$dia_width_type="dia_width_type_".$i;
			$hideuom="hideuom_".$i;
			$hidegreyused="hidegreyused_".$i;

			if(str_replace("'","",$$fabshade_id) == "")
			{
				$fabric_shade_id = 0;
			}else{
				$fabric_shade_id = str_replace("'","",$$fabshade_id);
			}

			if(str_replace("'","",$$txtcurrentdelivery)>0)
			{

				if(str_replace("'","",$$hideorder) == "")
				{
					$deli_order_id = 0;
				}else{
					$deli_order_id = str_replace("'","",$$hideorder);
				}


				if($production_data_array[str_replace("'","",$$hidefindtls)]['batch_id'] != str_replace("'","",$$hidebatch)  )
				{
					echo "30**Production Batch does not match with Delivery. \nProduction Batch :".$production_data_array[str_replace("'","",$$hidefindtls)]['batch_no'];
					disconnect($con);
					die;
				}

				if($production_data_array[str_replace("'","",$$hidefindtls)]['prod_id'] != str_replace("'","",$$hideprodid)  )
				{
					echo "30**Production product does not match with Delivery. ";
					disconnect($con);
					die;
				}

				if($production_data_order_check_array[str_replace("'","",$$hidefindtls)][$deli_order_id]=="")
				{
					echo "30**Production order/ref does not match with Delivery.";
					disconnect($con);
					die;
				}

				if($production_data_array[str_replace("'","",$$hidefindtls)]['fabric_shade'] != $fabric_shade_id  )
				{
					echo "30**Production Fabric Shade does not match with Delivery.";
					disconnect($con);
					die;
				}


				$delivery_balance = $production_arr[str_replace("'","",$$hidefindtls)][str_replace("'","",$cbo_is_sales)][$deli_order_id]["qnty"] - ( $delivery_arr[str_replace("'","",$$hidefindtls)][str_replace("'","",$cbo_is_sales)][$deli_order_id]["qnty"] + str_replace("'","",$$txtcurrentdelivery));
				if($delivery_balance<0)
				{
					$validate_rows .= $i.",";
					echo "20**Delivery quantity can not be grater than production quantity. \nproduction quantity=".$production_arr[str_replace("'","",$$hidefindtls)][str_replace("'","",$cbo_is_sales)][$deli_order_id]["qnty"].", \nprevious delivery quantity=".$delivery_arr[str_replace("'","",$$hidefindtls)][str_replace("'","",$cbo_is_sales)][$deli_order_id]["qnty"].", \ncurrent quantity=".str_replace("'","",$$txtcurrentdelivery)."**".chop($validate_rows,",");
					disconnect($con);
					die;
				}


				if ($k!=1) $data_array_dtls .=",";
				$data_array_dtls.="(".$dtls_id.",".$id.",54,".$$sys_id.",".$$hidefindtls.",".$$hidesysnum.",".$$hideprogram.",".$$hideprodid.",".$$hidejob.",".$$hideorder.",".$$hideconstruc.",".$$hidegsm.",".$$hidedia.",".$$txtcurrentdelivery.",".$$txt_roll.",".$$hidebatch.",".$fabric_shade_id.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."',".$cbo_is_sales.",".$$body_part_id.",".$$color_id.",".$$dia_width_type.",".$$hideuom.",".$$hidegreyused.")";
				$k++;
			}
		}

		$rID=$rID2=true;
		if( str_replace("'","",$update_mst_id) == "" )
		{
			$rID=sql_insert("pro_grey_prod_delivery_mst",$field_array,$data_array,1);
		}
		$rID2=sql_insert("pro_grey_prod_delivery_dtls",$field_array_dtls,$data_array_dtls,1);
		//echo "10**insert into pro_grey_prod_delivery_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		//echo "10**".$rID."##".$rID2;oci_rollback($con);disconnect($con); die;
		if($db_type==0)
		{
			if($rID && $rID2 )
			{
				mysql_query("COMMIT");
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$new_mrr_number[0])."**".str_replace("'",'',$ref_dtls_id);
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
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$new_mrr_number[0])."**".str_replace("'",'',$ref_dtls_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$id);
			}
		}
		disconnect($con);
		die;

	}
	else if($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();

		if(str_replace("'", "", $cbo_is_sales) == 1)
		{
			$is_sales_cond = " and a.entry_form =225 and a.receive_basis =10";
		}else{
			$is_sales_cond = " and a.entry_form =37 and a.receive_basis =16";
		}

		$updateMstId=str_replace("'","",$update_mst_id);
		//Validation update restricted when found qnty on 'Dyeing And Finishing Bill Issue' page against this challan. issue id:2126,5536

		$subcon_inbound_bill= sql_select("select b.bill_no from subcon_inbound_bill_dtls a, subcon_inbound_bill_mst b where a.mst_id=b.id and b.party_source=1 and b.company_id=$cbo_dyeing_company and b.party_id=$cbo_company_id and b.process_id=4 and a.delivery_id='$updateMstId' and a.status_active=1 and a.is_deleted=0 group by b.bill_no");
		if(!empty($subcon_inbound_bill))
		{
			echo "30**Bill found against this delivery challan\nBill No: ".$subcon_inbound_bill[0][csf("bill_no")];
			disconnect($con);
			die;
		}
		/*$subcon_inbound_bill_qnty=return_library_array("select delivery_id, sum(delivery_qty) as delivery_qty from subcon_inbound_bill_dtls where delivery_id='$updateMstId' and status_active=1 and is_deleted=0 group by delivery_id",'delivery_id','delivery_qty');
		if($subcon_inbound_bill_qnty[$updateMstId]>0)
		{
			echo "30**Bill found against this delivery challan";
			disconnect($con);
			die;
		}*/


		for($j=1;$j<=$total_row;$j++)
		{
			$hidefindtls="hidefindtls_".$j;

			$all_dtls_ids .= $$hidefindtls.",";
		}

		$all_dtls_ids = chop($all_dtls_ids,",");

		/*$production_sql = sql_select("SELECT a.id, b.id as dtls_id, c.po_breakdown_id, c.is_sales, b.prod_id, sum(c.quantity) as quantity from inv_receive_master a,  pro_finish_fabric_rcv_dtls b left join order_wise_pro_details c on b.id=c.dtls_id  and c.entry_form=7 and c.trans_type=1 and c.status_active=1 and c.is_deleted=0 where a.id=b.mst_id and a.entry_form=7 and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  a.company_id=$cbo_company_id and b.id in ($all_dtls_ids) group by b.id,a.id,b.prod_id, c.po_breakdown_id, c.is_sales");*/

		$production_sql = sql_select("SELECT a.id, b.id as dtls_id, c.po_breakdown_id, c.is_sales, b.prod_id, sum(c.quantity) as quantity
			from inv_receive_master a, pro_finish_fabric_rcv_dtls b , order_wise_pro_details c
			where a.id=b.mst_id and  b.id=c.dtls_id and c.entry_form=7 and c.trans_type=1 and c.status_active=1 and c.is_deleted=0  and a.entry_form=7
			and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_id and b.id in ($all_dtls_ids)
			group by b.id,a.id,b.prod_id, c.po_breakdown_id, c.is_sales
			union all
			SELECT a.id, b.id as dtls_id, 0 as po_breakdown_id, 0 as is_sales, b.prod_id, sum(b.receive_qnty) as quantity
			from inv_receive_master a, pro_finish_fabric_rcv_dtls b
			where a.id=b.mst_id and a.entry_form=7 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_id and b.id in ($all_dtls_ids) and b.order_id is null
			group by b.id,a.id,b.prod_id");

		foreach ($production_sql as $val)
		{
			if($val[csf("is_sales")] != 1){
				$production_is_sales=0;
			}else{
				$production_is_sales=1;
			}

			if($val[csf("po_breakdown_id")]==""){
				$production_order = 0;
			}else{
				$production_order = $val[csf("po_breakdown_id")];
			}

			$production_arr[$val[csf("dtls_id")]][$production_is_sales][$production_order]["qnty"] += $val[csf("quantity")];
		}

		$delivery_sql = sql_select("select product_id,order_id,current_delivery,roll,sys_dtls_id, is_sales from pro_grey_prod_delivery_dtls where entry_form=54 and sys_dtls_id in ($all_dtls_ids) and mst_id !=$update_mst_id and status_active=1 and is_deleted=0");
		foreach ($delivery_sql as $val)
		{
			if($val[csf("is_sales")] != 1){
				$delivery_is_sales=0;
			}else{
				$delivery_is_sales=1;
			}
			if($val[csf("order_id")]==""){
				$delivery_order = 0;
			}else{
				$delivery_order = $val[csf("order_id")];
			}
			$delivery_arr[$val[csf("sys_dtls_id")]][$delivery_is_sales][$delivery_order]["qnty"] += $val[csf("current_delivery")];
		}


		$receive_sql = sql_select("select a.id, a.recv_number, b.prod_id, b.batch_id, b.body_part_id, b.fabric_description_id, b.gsm, b.fabric_shade, b.dia_width_type, b.uom, b.width, sum(b.receive_qnty) as qnty from inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id = b.mst_id $is_sales_cond and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.booking_id = $update_mst_id group by a.id, a.recv_number, b.prod_id, b.batch_id, b.body_part_id, b.fabric_description_id, b.gsm, b.fabric_shade, b.dia_width_type, b.uom, b.width");

		foreach ($receive_sql as $val)
		{
			$receive_arr[$val[csf("batch_id")]][$val[csf("body_part_id")]][$val[csf("prod_id")]][$val[csf("fabric_shade")]][$val[csf("dia_width_type")]][$val[csf("width")]] .= $val[csf("recv_number")].",";
		}


		if($db_type==0)	{ mysql_query("BEGIN"); }
		if( str_replace("'","",$update_mst_id) != "")
		{
			$field_array_mst="delevery_date*fin_pord_type*company_id*location_id*buyer_id*updated_by*update_date*status_active*is_deleted*remarks*delivery_company*delivery_location";
			$data_array_mst="".$txt_delevery_date."*".$cbo_order_status."*".$cbo_company_id."*".$cbo_location_id."*".$cbo_buyer_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0*".$txt_remarks."*".$cbo_deli_company_id."*".$cbo_deli_location_id;
		}
		if( str_replace("'","",$update_mst_id) != "")
		{
			$rID3=1;
			$id_arr=array();
			$data_array_dtls=array();
			$data_array_dtls_in="";
			$field_array_dtls="current_delivery*roll*batch_id*uom*updated_by*update_date";

			$mst_id=str_replace("'",'',$update_mst_id);
			$field_array_dtls_in="id,mst_id,entry_form,grey_sys_id,sys_dtls_id,grey_sys_number,program_no,product_id,job_no,order_id,determination_id,gsm,dia,current_delivery,roll,batch_id,fabric_shade,inserted_by,insert_date,is_sales,bodypart_id,color_id,width_type,uom,grey_used_qnty";
			$coma=0;
			for($i=1; $i<=$total_row; $i++)
			{
				$dtls_id = return_next_id_by_sequence("PRO_GREY_PROD_DELI_DTLS_PK_SEQ", "pro_grey_prod_delivery_dtls", $con);
				$ref_dtls_id=$dtls_id;

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
				$update_id_dtls="hiddendtlsid_".$i;
				$txt_roll="txtroll_".$i;
				$hidebatch="hidebatch_".$i;
				$fabshade_id="hidefabshade_".$i;
				$body_part_id="body_part_id_".$i;
				$color_id="color_id_".$i;
				$dia_width_type="dia_width_type_".$i;
				$hideuom="hideuom_".$i;
				$hidden_current_val="hidden_current_val_".$i;
				$hidegreyused="hidegreyused_".$i;

				if(str_replace("'","",$$fabshade_id) == "")
				{
					$fabric_shade_id = 0;
				}else{
					$fabric_shade_id = str_replace("'","",$$fabshade_id);
				}

				if(str_replace("'",'',$$update_id_dtls)!="")
				{
					if(str_replace("'","",$$hideorder) == "")
					{
						$deli_order_id = 0;
					}else{
						$deli_order_id = str_replace("'","",$$hideorder);
					}

					$delivery_balance = $production_arr[str_replace("'","",$$hidefindtls)][str_replace("'","",$cbo_is_sales)][$deli_order_id]["qnty"] - ( $delivery_arr[str_replace("'","",$$hidefindtls)][str_replace("'","",$cbo_is_sales)][$deli_order_id]["qnty"] + str_replace("'","",$$txtcurrentdelivery));
					if($delivery_balance<0)
					{
						$validate_rows .= $i.",";
						echo "20**Delivery quantity can not be grater than production quantity. \nproduction quantity=".$production_arr[str_replace("'","",$$hidefindtls)][str_replace("'","",$cbo_is_sales)][$deli_order_id]["qnty"].", \nprevious delivery quantity=".$delivery_arr[str_replace("'","",$$hidefindtls)][str_replace("'","",$cbo_is_sales)][$deli_order_id]["qnty"].", \ncurrent quantity=".str_replace("'","",$$txtcurrentdelivery)."**".chop($validate_rows,",");
						disconnect($con);
						die;
					}



					$id_arr[]=str_replace("'",'',$$update_id_dtls);
					$data_array_dtls[str_replace("'",'',$$update_id_dtls)] =explode(",",("".$$txtcurrentdelivery.",".$$txt_roll.",".$$hidebatch.",".$$hideuom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'"));
				}
				else
				{
					if(str_replace("'","",$$txtcurrentdelivery)>0)
					{
						if(str_replace("'","",$$hideorder) == "")
						{
							$deli_order_id = 0;
						}else{
							$deli_order_id = str_replace("'","",$$hideorder);
						}

						$delivery_balance = $production_arr[str_replace("'","",$$hidefindtls)][str_replace("'","",$cbo_is_sales)][$deli_order_id]["qnty"] - ( $delivery_arr[str_replace("'","",$$hidefindtls)][str_replace("'","",$cbo_is_sales)][$deli_order_id]["qnty"] + str_replace("'","",$$txtcurrentdelivery));
						if($delivery_balance<0)
						{
							$validate_rows .= $i.",";
							echo "20**Delivery quantity can not be grater than production quantity. \nproduction quantity=".$production_arr[str_replace("'","",$$hidefindtls)][str_replace("'","",$cbo_is_sales)][$deli_order_id]["qnty"].", \nprevious delivery quantity=".$delivery_arr[str_replace("'","",$$hidefindtls)][str_replace("'","",$cbo_is_sales)][$deli_order_id]["qnty"].", \ncurrent quantity=".str_replace("'","",$$txtcurrentdelivery)."**".chop($validate_rows,",");
							disconnect($con);
							die;
						}


						if ($coma!=0) $data_array_dtls_in .=",";
						$data_array_dtls_in	.="(".$dtls_id.",".$mst_id.",54,".$$sys_id.",".$$hidefindtls.",".$$hidesysnum.",".$$hideprogram.",".$$hideprodid.",".$$hidejob.",".$$hideorder.",".$$hideconstruc.",".$$hidegsm.",".$$hidedia.",".$$txtcurrentdelivery.",".$$txt_roll.",".$$hidebatch.",".$fabric_shade_id.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."',".$cbo_is_sales.",".$$body_part_id.",".$$color_id.",".$$dia_width_type.",".$$hideuom.",".$$hidegreyused.")";
						$coma++;
					}

				}


				if($receive_arr[str_replace("'",'',$$hidebatch)][str_replace("'",'',$$body_part_id)][str_replace("'",'',$$hideprodid)][str_replace("'",'',$$fabshade_id)][str_replace("'",'',$$dia_width_type)][str_replace("'",'',$$hidedia)])
				{
					$validate_rows .= $i.",";

					if(str_replace("'",'',$$txtcurrentdelivery) != str_replace("'",'',$$hidden_current_val))
					{
						$receive_number .= $receive_arr[str_replace("'",'',$$hidebatch)][str_replace("'",'',$$body_part_id)][str_replace("'",'',$$hideprodid)][str_replace("'",'',$$fabshade_id)][str_replace("'",'',$$dia_width_type)][str_replace("'",'',$$hidedia)].",";
					}
				}
			}

			if(chop($receive_number,","))
			{
				echo "20**Receive found against this delivery.\nReceive Challan :".implode(",",array_filter(array_unique(explode(",",chop($receive_number,",")))))."**".chop($validate_rows,",");
				disconnect($con);
				die;
			}


			$rID=$rID2=$rID3=true;
			if( str_replace("'","",$update_mst_id) != "")
			{
				//echo "10**".$field_array_dtls_in.'='.$data_array_dtls_in; die;
				$rID=sql_update("pro_grey_prod_delivery_mst",$field_array_mst,$data_array_mst,"id",$update_mst_id,1);
			}
			//echo bulk_update_sql_statement("pro_grey_prod_delivery_dtls","id", $field_array_dtls,$data_array_dtls,$id_arr); die();
			$rID2=execute_query(bulk_update_sql_statement("pro_grey_prod_delivery_dtls","id", $field_array_dtls,$data_array_dtls,$id_arr),1);
			if($data_array_dtls_in!="")
			{
				$rID3=sql_insert("pro_grey_prod_delivery_dtls",$field_array_dtls_in,$data_array_dtls_in,1);
			}
		}

		//echo "10**$rID && $rID2 && $rID3".oci_rollback($con);disconnect($con); die;

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
		<?
		if(isset(  $_SESSION['logic_erp']['data_arr'][54] ))
		{
			$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][54] );
			echo "var field_level_data= ". $data_arr . ";\n";
		}else{
			echo "var field_level_data= '';\n";
		}
		?>

		function fnc_show()
		{
			if( ($("#txt_date_from").val() != "" && $("#txt_date_to").val() != "") ||  $("#txt_batch_no").val() != ""  ||  $("#txt_order_no").val() != "" ||  $("#txt_file_no").val() != "" ||  $("#txt_ref_no").val() != "" ||  $("#txt_sys_id").val() != "" )
			{
				show_list_view ( document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_location_id').value+'**'+document.getElementById('cbo_buyer_id').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('txt_sys_id').value+'**'+document.getElementById('txt_batch_no').value+'**'+document.getElementById('txt_order_no').value+'**'+document.getElementById('txt_file_no').value+'**'+document.getElementById('txt_ref_no').value+'**'+document.getElementById('cbo_is_sales').value+'**'+document.getElementById('cbo_year_selection').value, 'delivery_search_list_view', 'search_div', 'finish_feb_delivery_entry_controller', "setFilterGrid('tbl_body',-1)");
			}else{
				alert("please give any reference");
			}
		}

		/* show_list_view ( document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_location_id').value+'**'+document.getElementById('cbo_buyer_id').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('txt_sys_id').value+'**'+document.getElementById('txt_batch_no').value+'**'+document.getElementById('txt_order_no').value+'**'+document.getElementById('txt_file_no').value+'**'+document.getElementById('txt_ref_no').value+'**'+document.getElementById('cbo_is_sales').value+'**'+document.getElementById('cbo_year_selection').value, 'delivery_search_list_view', 'search_div', 'finish_feb_delivery_entry_controller', 'setFilterGrid(\'tbl_body\',-1)'); */
	</script>
</head>
<body>
	<div align="center" style="width:1125px;">
		<form name="searchexportinformationfrm"  id="searchexportinformationfrm">
			<fieldset style="width:1120px;">
				<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" width="1120" class="rpt_table" border="1" rules="all" align="center">
					<thead>
						<th width="120" class="must_entry_caption">Company</th>
						<th  width="100">Location</th>
						<th width="120">Buyer</th>
						<th width="50">Is Sales</th>
						<th width="90">Batch No</th>
						<th width="90">Order/FSO No</th>
						<th width="60">File No</th>
						<th width="60">Ref. No</th>
						<th width="90">Deli. Date from</th>
						<th width="90">Deli. Date To</th>
						<th width="100">System Id</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:60px" class="formbutton" />
						</th>
					</thead>
					<tr class="general">
						<td>
							<?
							echo create_drop_down( "cbo_company_id", 120, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "--Select Company--", str_replace("'","",$company) , "load_drop_down( 'finish_feb_delivery_entry_controller', this.value, 'load_drop_down_location_lc_in_popup', 'location_td' );load_drop_down( 'finish_feb_delivery_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
							?>
						</td>
						<td id="location_td">
							<?
							$blank_array="select id,location_name from lib_location where company_id='".str_replace("'","",$company)."' and status_active =1 and is_deleted=0 order by location_name";
							echo create_drop_down( "cbo_location_id",120,$blank_array,"id,location_name", 1, "--Select Location--", $selected, "","","","","","",2);
							?>
						</td>
						<td id="buyer_td">
							<?
							$blank_array="select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='".str_replace("'","",$company)."' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name";
							echo create_drop_down( "cbo_buyer_id",120,$blank_array,"id,buyer_name", 1, "--Select Buyer--", $selected, "","","","","","",2);
							?>
						</td>
						<td>
							<?
							//$is_sales_arr = array(1=>"Yes", 0 =>"No");
							echo create_drop_down( "cbo_is_sales",80,$yes_no,"", 1, "--Select--", "", "","","","","","",0);
							?>
						</td>
						<td>
							<input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:80px;"/>
						</td>
						<td>
							<input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:80px;"/>
						</td>
						<td>
							<input type="text" name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:60px;"/>
						</td>
						<td>
							<input type="text" name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:60px;"/>
						</td>
						<td>
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px;" readonly/>
						</td>
						<td>
							<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px;" readonly/>
						</td>
						<td>
							<input type="text" name="txt_sys_id" id="txt_sys_id" class="text_boxes" style="width:95px;"/>
						</td>
						<td>
							<input type="button" id="search_button" class="formbutton" value="Show" onClick="fnc_show()" style="width:60px;" />
						</td>
					</tr>
					<tr>
						<td colspan="10" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
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
	$cbo_company_id=str_replace("'","",$data[0]);
	$cbo_location_name=str_replace("'","",$data[1]);
	$cbo_buyer_name=str_replace("'","",$data[2]);
	$txt_date_from=str_replace("'","",$data[3]);
	$txt_date_to=str_replace("'","",$data[4]);
	$sys_id=str_replace("'","",$data[5]);
	$txt_batch_no=str_replace("'","",$data[6]);
	$order_no=str_replace("'","",$data[7]);
	$file_no=str_replace("'","",$data[8]);
	$ref_no=str_replace("'","",$data[9]);
	$is_sales=str_replace("'","",$data[10]);
	if($file_no!="") $file_cond=" and file_no=$file_no";else $file_cond="";
	if($ref_no!="") $ref_cond=" and grouping='$ref_no'";else $ref_cond="";

	if($is_sales == 1)
	{
		if($po_ids!="") $order_id_cond=" and b.order_id=$order_no"; else $order_id_cond="";
	}
	else
	{
		if($db_type==0) $group_con="group_concat(id) as po_ids";
		else  $group_con="LISTAGG(CAST( id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY id) as po_ids";
		$po_ids=return_field_value("$group_con","wo_po_break_down","status_active=1 $file_cond $ref_cond ","po_ids");
		if($file_no!="" || $ref_no!="")
		{
			if($po_ids!="") $order_id_cond="and b.order_id in($po_ids)"; else $order_id_cond="";
		}
	}

	if($order_no!="")
	{
		if($db_type==0)
		{
			if($is_sales == 1){
				$po_id=$order_no;
				if($po_id!="") $order_cond="and c.job_no_prefix_num=$order_no"; else $order_cond="";
			}else{
				$po_id=return_field_value("group_concat(id) as po_id","wo_po_break_down","po_number like '%$order_no' and status_active=1","po_id");
			}
		}
		else if($db_type==2)
		{
			if($is_sales == 1){
				$po_id=$order_no;
				if($po_id!="") $order_cond="and c.job_no_prefix_num=$order_no"; else $order_cond="";
			}else{
				$po_id=return_field_value("LISTAGG(CAST( id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY id) as po_id","wo_po_break_down","po_number like '%$order_no' and status_active=1","po_id");
				if($po_id!="") $order_cond="and b.order_id in($po_id)"; else $order_cond="";
			}
		}

	}

	if($txt_batch_no!="")
	{
		//$batch_id_ref=return_field_value("id as batch_id"," pro_batch_create_mst","batch_no='$txt_batch_no'","batch_id");
		//$batch_cond="and b.program_no ='$batch_id_ref'";
		$batch_id_ref=return_library_array( "select id, id from pro_batch_create_mst where batch_no='$txt_batch_no' and status_active=1 and is_deleted=0",'id','id');
		$batch_cond="and b.program_no in(".implode(",",$batch_id_ref).")";

	}
	else
	{
		$batch_cond="";
	}

	if($cbo_company_id!=0) {$cbo_company_name="and a.company_id='$cbo_company_id'";} else {echo "Please select the company";die;}
	if($cbo_location_name!=0) $cbo_location_name="and a.location_id='$cbo_location_name'"; else $cbo_location_name="";
	if($cbo_buyer_name !=0) $cbo_buyer_cond="and a.buyer_id='$cbo_buyer_name'"; else $cbo_buyer_cond="";
	if($db_type==0)
	{
		if($txt_date_from!="" && $txt_date_to !="") $date_condition="and a.delevery_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."'"; else $date_condition="";
	}

	else if($db_type==2)
	{
		if($txt_date_from!="" && $txt_date_to !="") $date_condition="and a.delevery_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'"; else $date_condition="";
	}

	if($sys_id!="") $sys_cond="and a.sys_number_prefix_num like '$sys_id'"; else $sys_cond="";

	if($is_sales == 1)
	{
		$sql_challan="select a.id,a.sys_number_prefix_num,$mrr_date_check as sys_year,a.sys_number,a.delevery_date,a.company_id,a.location_id,
		sum(b.current_delivery) as current_delivery,b.program_no as prog_no, b.order_id, b.is_sales

		from  pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b, fabric_sales_order_mst c
		where a.id=b.mst_id and b.order_id=c.id and a.entry_form=54 and b.entry_form=54 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $cbo_company_name $cbo_location_name $date_condition $sys_cond $batch_cond $order_cond $order_id_cond $cbo_buyer_cond and b.is_sales=$is_sales and b.current_delivery is not null
		group by  a.id,a.sys_number,a.delevery_date,a.company_id,a.location_id,a.sys_number_prefix_num,a.insert_date, b.program_no,b.order_id, b.is_sales order by a.sys_number_prefix_num";
	}else{
		 $sql_challan="select a.id,a.sys_number_prefix_num,$mrr_date_check as sys_year,a.sys_number,a.delevery_date,a.company_id, a.location_id, sum(b.current_delivery) as current_delivery, b.program_no as prog_no, b.order_id, b.is_sales

		from  pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b
		where a.id=b.mst_id and a.entry_form=54 and b.entry_form=54 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $cbo_company_name $cbo_location_name $date_condition $sys_cond $batch_cond $order_cond $order_id_cond $cbo_buyer_cond and b.is_sales=$is_sales and b.current_delivery is not null
		group by  a.id,a.sys_number,a.delevery_date,a.company_id,a.location_id,a.sys_number_prefix_num, a.insert_date, b.program_no, b.order_id, b.is_sales order by a.id ";
	}

	$batch_id_arr = $order_id_arr = array();

	$sql_result=sql_select($sql_challan);
	foreach ($sql_result as  $row)
	{
		$delivery_array[$row[csf("id")]]["id"] = $row[csf("id")];
		$delivery_array[$row[csf("id")]]["sys_number_prefix_num"] = $row[csf("sys_number_prefix_num")];
		$delivery_array[$row[csf("id")]]["sys_year"] = $row[csf("sys_year")];
		$delivery_array[$row[csf("id")]]["sys_number"] = $row[csf("sys_number")];
		$delivery_array[$row[csf("id")]]["delevery_date"] = $row[csf("delevery_date")];
		$delivery_array[$row[csf("id")]]["company_id"] = $row[csf("company_id")];
		$delivery_array[$row[csf("id")]]["location_id"] = $row[csf("location_id")];
		$delivery_array[$row[csf("id")]]["current_delivery"] += $row[csf("current_delivery")];

		$delivery_array[$row[csf("id")]]["prog_no"][] = $row[csf("prog_no")];
		$delivery_array[$row[csf("id")]]["order_id"][] = $row[csf("order_id")];
		$delivery_array[$row[csf("id")]]["is_sales"][] = $row[csf("is_sales")];

		$batch_id_arr[$row[csf('prog_no')]] = $row[csf('prog_no')];
		if($row[csf('order_id')] != ""){
			$order_id_arr[$row[csf('order_id')]] = $row[csf('order_id')];
		}
	}


	if($is_sales != 1){
		if(!empty($order_id_arr)){
			$poDataArray=sql_select("select b.id,b.po_number,b.file_no,b.grouping from  wo_po_break_down b,wo_po_details_master a where b.job_id=a.id and a.company_name=$cbo_company_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.id in(".implode(",",$order_id_arr).") $file_cond $ref_cond");
			$all_po_id='';
			$job_array=array(); $all_job_id='';
			foreach($poDataArray as $row)
			{
				$job_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
				$job_array[$row[csf('id')]]['file']=$row[csf('file_no')];
				$job_array[$row[csf('id')]]['ref']=$row[csf('grouping')];
			}
		}
	}else{
		$sales_arr=array();
		if(!empty($order_id_arr)){
			$po_data=sql_select("select id, job_no,po_buyer,po_job_no from fabric_sales_order_mst where status_active=1 and is_deleted=0 and id in(".implode(",",array_unique($order_id_arr)).")");

			foreach($po_data as $row)
			{
				$sales_arr[$row[csf('id')]]['job_no']=$row[csf('job_no')];
				$sales_arr[$row[csf('id')]]['po_buyer']=$row[csf('po_buyer')];
				$sales_arr[$row[csf('id')]]['po_job_no']=$row[csf('po_job_no')];
			}
		}
	}

	if(!empty($batch_id_arr))
	{
		foreach ($batch_id_arr as $key => $value)
		{
			foreach (explode(",", $value) as $val)
			{
			 	$batch_id_arr_filtered[$val] = $val;
			}
		}

        $all_batch_id_cond = $batchNoCond = "";
        if($db_type==2 && count($batch_id_arr_filtered)>999)
        {
        	$batch_id_chunk_arr=array_chunk($batch_id_arr_filtered,999);
        	foreach($batch_id_chunk_arr as $chunk_arr)
        	{
        		$chunk_arr_value=implode(",",$chunk_arr);
        		$batchNoCond.="  a.id in($chunk_arr_value) or ";
        	}

        	$all_batch_id_cond.=" and (".chop($batchNoCond,'or ').")";
        }
        else
        {
        	$all_batch_id_cond=" and a.id in(".implode(',', $batch_id_arr_filtered).")";
        }

		$batch_data=sql_select("select a.id, a.batch_no,a.extention_no, a.booking_no_id, a.booking_no, a.booking_without_order, sum(b.roll_no) as roll_no, sum(b.batch_qnty) as batch_qnty from  pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and batch_against>0 and batch_for>0 $all_batch_id_cond group by a.id, a.batch_no, a.extention_no, a.booking_no_id, a.booking_no, a.booking_without_order");
		foreach($batch_data as $row)
		{
			$batch_details[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
			$batch_details[$row[csf('id')]]['extention_no']=$row[csf('extention_no')];
			$batch_details[$row[csf('id')]]['booking_no_id']=$row[csf('booking_no_id')];
			$batch_details[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
			$batch_details[$row[csf('id')]]['booking_without_order']=$row[csf('booking_without_order')];
			$batch_details[$row[csf('id')]]['roll_no']=$row[csf('roll_no')];
			$batch_details[$row[csf('id')]]['batch_qnty']=$row[csf('batch_qnty')];
		}
	}
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1200" class="rpt_table">
		<thead>
			<tr>
				<th width="30">SL</th>
				<th width="70">Year</th>
				<th width="70">Delivery Sys.Num</th>
				<th width="110">Company Name</th>
				<th width="110">Location Name</th>
				<th width="150">Batch No</th>
				<th width="100">Extension No</th>
				<th width="250">Order/FSO No</th>
				<th width="70">File No</th>
				<th width="80">Ref. No</th>
				<th width="70">Delivery Date</th>
				<th>Delivery Qty</th>
			</tr>
		</thead>
	</table>
	<div id="scroll_body">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1200" class="rpt_table" id="tbl_body">
			<tbody>
				<style type="text/css">
					.word_wrap_break{
						word-wrap: break-word;
						word-break: break-all;
					}
				</style>
				<?
				$i=1;
				if(!empty($sql_result))
				{
					foreach ($delivery_array as $delivery_id => $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						$batch_no_array=array_unique($row["prog_no"]);
						$po_no_arr=array_unique($row["order_id"]);
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $row["id"] ?>_<? echo $row["company_id"] ?>_<? echo $row["location_id"] ?>_<? echo $row["sys_number"] ?>')" style="cursor:pointer;">
							<td width="30"><? echo $i; ?></td>
							<td align="center" width="70"><p><? echo $row["sys_year"]; ?></p></td>
							<td width="70" align="center"><p><? echo $row["sys_number_prefix_num"]; ?></p></td>
							<td width="110" align="center"><p><? echo $company_arr[$row["company_id"]]; ?></p></td>
							<td width="110"><p><? echo $location_arr[$row["location_id"]]; ?></p></td>
							<td width="150" align="center">
								<p class="word_wrap_break">
								<?
								$batch_all=$ext_no="";
								foreach($batch_no_array as $batch_id)
								{
									if($batch_all=="")
										$batch_all=$batch_details[$batch_id]['batch_no'];
									else
										$batch_all.= ", ".$batch_details[$batch_id]['batch_no'];

									if($ext_no=="")
										$ext_no=$batch_details[$batch_id]['extention_no'];
									else
										$ext_no.=", ".$batch_details[$batch_id]['extention_no'];
								}
								echo $batch_all;
								?>
								</p>
							</td>
							<td width="100" align="center">
								<? echo implode(", ",array_unique(array_filter(explode(", ", $ext_no)))); ?>
							</td>
							<td width="250" align="center">
								<p class="word_wrap_break">
								<?
								$po_group=""; $po_file=""; $po_ref="";
								if($is_sales != 1){
									foreach($po_no_arr as $po)
									{
										if($po_group=="") $po_group=$job_array[$po]['po_number']; else $po_group=$po_group.",".$job_array[$po]['po_number'];
										if($po_file=="") $po_file=$job_array[$po]['file']; else $po_file=$po_file.",".$job_array[$po]['file'];
										if($po_ref=="") $po_ref=$job_array[$po]['ref']; else $po_ref=$po_ref.",".$job_array[$po]['ref'];
									}
								}else{
									foreach($po_no_arr as $po)
									{
										$po_group=$sales_arr[$po]['job_no'];

									}
								}
								echo $po_group;
								?>
								</p>
							</td>
							<td align="center" width="70"><p class="word_wrap_break"><? echo $po_file; ?></p></td>
							<td align="center" width="80"><p class="word_wrap_break"><? echo $po_ref; ?></p></td>
							<td width="70" align="center"><p><? if($row["delevery_date"]!='0000-00-00' || $row["delevery_date"]!="") echo change_date_format($row["delevery_date"]);//echo $i; ?></p></td>
							<td align="right"><p><? echo number_format($row["current_delivery"],2); ?></p></td>
						</tr>
						<?
						$i++;
					}
				}else{
					echo "<tr><td rowspan='11' align='center'><strong>No Data Found</strong></td></tr>";
				}
				?>
			</tbody>
		</table>
	</div>
	<?
}


if($action=="populate_master_from_data")
{
	/*if($db_type==2)
	{
		$sql=sql_select("select a.id, a.delevery_date, a.remarks, a.fin_pord_type, a.company_id, a.location_id, a.buyer_id, a.knitting_source, a.knitting_company, a.knitting_location, a.delivery_company, a.delivery_location, b.grey_sys_id, b.program_no, b.order_id, b.product_id from  pro_grey_prod_delivery_mst a,  pro_grey_prod_delivery_dtls b where a.id=b.mst_id and a.id=$data  and a.entry_form=54 and b.entry_form=54 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.delevery_date, a.remarks,a.fin_pord_type, a.company_id, a.location_id, a.buyer_id, a.knitting_source,a.knitting_company, a.knitting_location, a.delivery_company, a.delivery_location, b.grey_sys_id, b.program_no, b.order_id, b.product_id");
	}
	else if($db_type==0)
	{
		$sql=sql_select("select a.id,a.delevery_date,a.remarks,a.fin_pord_type,a.company_id,a.location_id,a.buyer_id,a.knitting_source,a.knitting_company,a.knitting_location, a.delivery_company, a.delivery_location, group_concat(distinct b.grey_sys_id  ) as grey_sys_id,group_concat(distinct b.program_no ) as program_no,group_concat(distinct b.order_id ) as order_id,group_concat(distinct b.product_id ) as product_id from  pro_grey_prod_delivery_mst a,  pro_grey_prod_delivery_dtls b where a.id=b.mst_id and a.id=$data  and a.entry_form=54 and b.entry_form=54 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id");
	}*/

	$sql=sql_select("select a.id, a.delevery_date, a.remarks, a.fin_pord_type, a.company_id, a.location_id, a.buyer_id, a.knitting_source, a.knitting_company, a.knitting_location, a.delivery_company, a.delivery_location, b.grey_sys_id, b.program_no, b.order_id, b.product_id from  pro_grey_prod_delivery_mst a,  pro_grey_prod_delivery_dtls b where a.id=b.mst_id and a.id=$data  and a.entry_form=54 and b.entry_form=54 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.delevery_date, a.remarks,a.fin_pord_type, a.company_id, a.location_id, a.buyer_id, a.knitting_source, a.knitting_company, a.knitting_location, a.delivery_company, a.delivery_location, b.grey_sys_id, b.program_no, b.order_id, b.product_id");

	foreach ($sql as  $row)
	{
		$delivery_array[$row[csf("id")]]["id"] = $row[csf("id")];
		$delivery_array[$row[csf("id")]]["delevery_date"] = $row[csf("delevery_date")];
		$delivery_array[$row[csf("id")]]["remarks"] = $row[csf("remarks")];
		$delivery_array[$row[csf("id")]]["fin_pord_type"] = $row[csf("fin_pord_type")];
		$delivery_array[$row[csf("id")]]["company_id"] = $row[csf("company_id")];
		$delivery_array[$row[csf("id")]]["location_id"] = $row[csf("location_id")];
		$delivery_array[$row[csf("id")]]["buyer_id"] = $row[csf("buyer_id")];
		$delivery_array[$row[csf("id")]]["knitting_source"] = $row[csf("knitting_source")];
		$delivery_array[$row[csf("id")]]["knitting_company"] = $row[csf("knitting_company")];
		$delivery_array[$row[csf("id")]]["knitting_location"] = $row[csf("knitting_location")];
		$delivery_array[$row[csf("id")]]["delivery_company"] = $row[csf("delivery_company")];
		$delivery_array[$row[csf("id")]]["delivery_location"] = $row[csf("delivery_location")];


		$delivery_array[$row[csf("id")]]["grey_sys_id"][] = $row[csf("grey_sys_id")];
		$delivery_array[$row[csf("id")]]["program_no"][] = $row[csf("program_no")];
		$delivery_array[$row[csf("id")]]["order_id"][] = $row[csf("order_id")];
		$delivery_array[$row[csf("id")]]["product_id"][] = $row[csf("product_id")];

	}

	foreach ($delivery_array as $delivery_id => $row)
	{

		$receive_id=implode(",",array_unique($row["grey_sys_id"]));
		$progrum_id=implode(",",array_unique($row["program_no"]));
		$order_id=implode(",",array_unique($row["order_id"]));
		$tail_id=implode(",",array_unique($row["product_id"]));

		echo "document.getElementById('txt_delevery_date').value 			= '".change_date_format($row["delevery_date"])."';\n";
		echo "document.getElementById('cbo_company_id').value 				= ".$row["company_id"].";\n";
		echo "document.getElementById('cbo_location_id').value 				= ".$row["location_id"].";\n";
		echo "document.getElementById('cbo_buyer_id').value 				= ".$row["buyer_id"].";\n";

		echo "document.getElementById('cbo_dyeing_source').value 			= ".$row["knitting_source"].";\n";

		echo "load_drop_down('requires/finish_feb_delivery_entry_controller', ".$row["knitting_source"]."+'_'+".$row["company_id"].", 'load_drop_down_dyeing_com', 'dyeingcom_td' );\n";

		echo "document.getElementById('cbo_dyeing_company').value 			= ".$row["knitting_company"].";\n";
		echo "load_location();\n";
		echo "document.getElementById('cbo_location_dyeing').value 			= ".$row["knitting_location"].";\n";

		echo "document.getElementById('hidden_receive_id').value 			= '".$receive_id."';\n";
		echo "document.getElementById('hidden_product_id').value 			= '".$tail_id."';\n";
		echo "document.getElementById('hidden_order_id').value 				= '".$order_id."';\n";
		echo "document.getElementById('update_mst_id').value 				= '".$row["id"]."';\n";
		echo "document.getElementById('cbo_order_status').value 			= ".$row["fin_pord_type"].";\n";
		echo "document.getElementById('txt_remarks').value 				 	= '".$row["remarks"]."';\n";
		echo "document.getElementById('cbo_deli_company_id').value 			= '".$row["delivery_company"]."';\n";
		echo "load_drop_down( 'requires/finish_feb_delivery_entry_controller', ".$row["delivery_company"].", 'load_drop_down_location_deli', 'deli_location_td' );\n";
		echo "document.getElementById('cbo_deli_location_id').value 		= '".$row["delivery_location"]."';\n";
	}
}


if($action=="delivery_challan_print")
{
	extract($_REQUEST);

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$datas=explode('_',$data);
	$program_ids = implode(",",array_unique(explode(",",str_replace("'","",$datas[0]))));
	$company = str_replace("'","",$datas[1]);
	$from_date = str_replace("'","",$datas[2]);
	$to_date = str_replace("'","",$datas[3]);
	$product_ids = str_replace("'","",$datas[4]);
	$order_ids = implode(",",array_unique(explode(",",str_replace("'","",$datas[5]))));
	$location= str_replace("'","",$datas[6]);
	$buyer = str_replace("'","",$datas[7]);
	$update_mst_id = str_replace("'","",$datas[8]);
	$delivery_date = str_replace("'","",$datas[9]);
	$Challan_no = str_replace("'","",$datas[10]);
	$fin_prod_type = str_replace("'","",$datas[11]);
	$batch_ids = implode(",",array_unique(explode(",",str_replace("'","",$datas[12]))));
	$operation = str_replace("'","",$datas[13]);
	$txt_remarks = str_replace("'","",$datas[14]);
	$cbo_template_id = str_replace("'","",$datas[15]);
	$deli_company = str_replace("'","",$datas[16]);
	$deli_location = str_replace("'","",$datas[17]);
	$dye_location = str_replace("'","",$datas[18]);
	$txt_remark = str_replace("'","",$datas[19]);


	if($order_ids==""){$order_id_print_cond="";}else{$order_id_print_cond=" and a.order_id in ($order_ids)";}

	$com_sql=sql_select("select id, company_name, plot_no, level_no, road_no, block_no, city, zip_code from lib_company where id=$company");
	$com_address="";
	foreach($com_sql as $row)
	{
		$company_name=$row[csf("company_name")];
		if($row[csf("plot_no")]!="") $com_address.=$row[csf("plot_no")].", ";
		if($row[csf("level_no")]!="") $com_address.=$row[csf("level_no")].", ";
		if($row[csf("road_no")]!="") $com_address.=$row[csf("road_no")].", ";
		if($row[csf("block_no")]!="") $com_address.=$row[csf("block_no")].", ";
		if($row[csf("city")]!="") $com_address.=$row[csf("city")].", ";
		if($row[csf("zip_code")]!="") $com_address.=$row[csf("zip_code")].", ";
	}
	$com_address=chop($com_address," , ");

	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$supplier_arr=return_library_array( "select id, supplier_name from  lib_supplier",'id','supplier_name');
	$yarncount=return_library_array( "select id, yarn_count from  lib_yarn_count",'id','yarn_count');
	$supplier_brand=return_library_array("select id,brand_name from lib_brand", "id","brand_name");
	$non_order_buyer=return_library_array("select id, buyer_id from wo_non_ord_samp_booking_mst where booking_type=4 and status_active=1 and is_deleted=0","id","buyer_id");
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');

	$process_loss_method_variable	=sql_select("select process_loss_method from variable_order_tracking where company_name=$company and variable_list=18 and item_category_id=2 and status_active =1");
	$process_loss_method = ($process_loss_method_variable[0][csf("process_loss_method")] ==2) ? 2: 1;

	$batch_sql=sql_select("SELECT a.id, a.batch_no, a.booking_no_id, a.booking_no, a.booking_without_order, b.prod_id, b.batch_qnty,b.body_part_id, b.program_no, c.gsm, c.detarmination_id, b.po_id, b.color_type 
	from pro_batch_create_mst a, pro_batch_create_dtls b, product_details_master c 
	where a.id=b.mst_id and b.prod_id=c.id and a.id in($batch_ids) 
	group by a.id, a.batch_no, a.booking_no_id, a.booking_no, a.booking_without_order, b.prod_id, b.batch_qnty,b.body_part_id, b.program_no, c.gsm, c.detarmination_id, b.po_id, b.color_type order by b.prod_id ASC");
	$batch_details=array();
	foreach($batch_sql as $row)
	{
		$batch_details[$row[csf("id")]]["batch_no"]=$row[csf("batch_no")];
		$batch_details[$row[csf("id")]]["booking_no"]=$row[csf("booking_no")];
		$batch_details[$row[csf("id")]]["booking_no_id"]=$row[csf("booking_no_id")];
		$batch_details[$row[csf("id")]]["booking_without_order"]=$row[csf("booking_without_order")];
		$batch_details[$row[csf("id")]]["prod_id"]=$row[csf("prod_id")];
		$batch_details[$row[csf("id")]]["batch_qnty"]+=$row[csf("batch_qnty")];
		$batch_details[$row[csf("id")]]["body_part_id"]=$row[csf("body_part_id")];
		$batch_details[$row[csf("id")]]["program_no"]=$row[csf("program_no")];
		$all_booking_no_arr[$row[csf("booking_no")]] = $row[csf("booking_no")];

		$batch_color_type_with_po_arr[$row[csf("id")]][$row[csf("body_part_id")]][$row[csf("gsm")]][$row[csf("detarmination_id")]][$row[csf("po_id")]]["color_type"]=$row[csf("color_type")];
		$batch_color_type_without_po_arr[$row[csf("id")]][$row[csf("body_part_id")]][$row[csf("gsm")]][$row[csf("detarmination_id")]]["color_type"]=$row[csf("color_type")];
	}

	$all_booking_no_arr = array_filter($all_booking_no_arr);
	$all_booking_nos = "'".implode("','", $all_booking_no_arr)."'";
	$all_booking_no_cond=""; $bookCond="";
	$all_booking_no_cond_2=""; $bookCond_2="";
	if($db_type==2 && count($all_booking_no_arr)>999)
	{
		$all_booking_no_arr_chunk=array_chunk($all_booking_no_arr,999) ;
		foreach($all_booking_no_arr_chunk as $chunk_arr)
		{
			$chunk_arr_value=implode(",",$chunk_arr);
			$bookCond.="  a.booking_no in($chunk_arr_value) or ";
			$bookCond_2.="  p.booking_no in($chunk_arr_value) or ";
		}

		$all_booking_no_cond.=" and (".chop($bookCond,'or ').")";
		$all_booking_no_cond_2.=" and (".chop($bookCond_2,'or ').")";
	}
	else
	{
		$all_booking_no_cond=" and a.booking_no in($all_booking_nos)";
		$all_booking_no_cond_2=" and p.booking_no in($all_booking_nos)";
	}

	if(!empty($order_ids))
	{
		$job_data = sql_select("SELECT c.style_owner from order_wise_pro_details a,wo_po_break_down b,wo_po_details_master c where a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and a.entry_form=7 and a.status_active=1 and a.is_deleted=0 and a.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.po_breakdown_id in($order_ids) ");
		//echo $sql;
	}


	if($fin_prod_type==1)
	{
		$yarn_lot_data=sql_select("SELECT  a.brand_id, b.po_breakdown_id, a.prod_id, a.yarn_lot as yarn_lot, a.yarn_count, a.stitch_length as stitch_length,a.machine_no_id as machine_no_id
			from pro_grey_prod_entry_dtls a, order_wise_pro_details b
			where a.id=b.dtls_id and b.entry_form in(2) and b.po_breakdown_id in($order_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");

		foreach($yarn_lot_data as $rows)
		{
			$knit_production_data[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['lot'].=$rows[csf('yarn_lot')].",";
			$knit_production_data[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['yarn_count'].=$rows[csf('yarn_count')].",";
			$knit_production_data[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['stitch_length'].=$rows[csf('stitch_length')].",";
			$knit_production_data[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['brand_id'].=$rows[csf('brand_id')].",";
			$knit_production_data[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['machine_no_id'].=$rows[csf('machine_no_id')].",";
		}
	}
	else
	{
		$yarn_lot_data=sql_select("SELECT  p.booking_no, a.brand_id, a.prod_id, a.yarn_lot, a.yarn_count, a.stitch_length,a.machine_no_id
			from inv_receive_master p, pro_grey_prod_entry_dtls a
			where p.id=a.mst_id and p.booking_without_order=1 and p.entry_form in(2) and a.status_active=1 and a.is_deleted=0 and p.status_active=1 and p.is_deleted=0 $all_booking_no_cond_2");
		foreach($yarn_lot_data as $rows)
		{
			$knit_production_data[$rows[csf('booking_no')]][$rows[csf('prod_id')]]['yarn_count']=$rows[csf('yarn_count')];
			$knit_production_data[$rows[csf('booking_no')]][$rows[csf('prod_id')]]['stitch_length']=$rows[csf('stitch_length')];
			$knit_production_data[$rows[csf('booking_no')]][$rows[csf('prod_id')]]['lot']=$rows[csf('yarn_lot')];
			$knit_production_data[$rows[csf('booking_no')]][$rows[csf('prod_id')]]['machine_no_id']=$rows[csf('machine_no_id')];
			$knit_production_data[$rows[csf('booking_no')]][$rows[csf('prod_id')]]['brand_id']=$rows[csf('brand_id')];
		}
	}


	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 order by b.id asc";
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
		$sql_update=sql_select("SELECT a.DELIVERY_COMPANY,a.delivery_location,b.id,b.grey_sys_id,b.product_id,b.job_no,b.order_id,b.current_delivery,
		b.roll,b.program_no,b.sys_dtls_id FROM pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b WHERE a.id=b.mst_id and b.mst_id = $update_mst_id AND b.entry_form = 54");
		// $sql_update=sql_select("select id,grey_sys_id,product_id,job_no,order_id,current_delivery,roll,program_no,sys_dtls_id from pro_grey_prod_delivery_dtls where mst_id=$update_mst_id and entry_form=54");
		$all_sys_dtls_id="";
		foreach($sql_update as $row)
		{
			$all_sys_dtls_id.=$row[csf("sys_dtls_id")].",";
			if($fin_prod_type==1)
			{
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("order_id")]."*".$row[csf("sys_dtls_id")]]["current_delivery"] =$row[csf("current_delivery")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("order_id")]."*".$row[csf("sys_dtls_id")]]["id"] =$row[csf("id")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("order_id")]."*".$row[csf("sys_dtls_id")]]["roll"] =$row[csf("roll")];
			}
			else
			{
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("sys_dtls_id")]]["current_delivery"] =$row[csf("current_delivery")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("sys_dtls_id")]]["id"] =$row[csf("id")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("sys_dtls_id")]]["roll"] =$row[csf("roll")];
			}
		}
	}

	$all_sys_dtls_id=implode(",",array_unique(explode(",",chop($all_sys_dtls_id,","))));
	if($all_sys_dtls_id=="") $all_sys_dtls_id=0;

	if($fin_prod_type==1)
	{
		$fin_pord_sql=sql_select("select b.id, o.grey_used_qty, b.remarks,o.po_breakdown_id from inv_receive_master a, pro_finish_fabric_rcv_dtls b,order_wise_pro_details o where a.id=b.mst_id and a.entry_form=7 and b.id=o.dtls_id and o.entry_form=7 and b.id in($all_sys_dtls_id)");
		$fin_pord_data=array();
		$$grey_userd_data_arr=array();
		foreach($fin_pord_sql as $row)
		{
			$grey_userd_data_arr[$row[csf("id")]][$row[csf("po_breakdown_id")]]["grey_used_qty"]=$row[csf("grey_used_qty")];
			$fin_pord_data[$row[csf("id")]]["remarks"]=$row[csf("remarks")];
		}
	}else{
		$fin_pord_sql=sql_select("select b.id, b.grey_used_qty, b.remarks from inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form=7 and b.id in($all_sys_dtls_id)");
		$fin_pord_data=array();
		foreach($fin_pord_sql as $row)
		{
			$fin_pord_data[$row[csf("id")]]["grey_used_qty"]=$row[csf("grey_used_qty")];
			$fin_pord_data[$row[csf("id")]]["remarks"]=$row[csf("remarks")];
		}
	}


	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$company'","image_location");

	?>
	<div style="width:1300px;">
		<table width="1300" cellspacing="0" align="center" border="0">
			<tr>
				<td colspan="1" rowspan="3">
					<img src="../<? echo $image_location; ?>" height="70" width="200" style="float:left; margin-left:10px;">
				</td>
				<td colspan="15" align="center" style="font-size:x-large"><strong>LC Company : <? echo $company_name; ?></strong>
				</td>
			</tr>
			<tr>
				<td colspan="16" align="center" style="font-size:18px"><strong>LC Company Location : <? echo $com_address; ?></strong></td>
			</tr>
			<tr>
				<td colspan="16" align="center" style="font-size:18px"><strong>Style Owner : <? echo $company_arr[$job_data[0][csf('style_owner')]]; ?></strong></td>
			</tr>
			<tr>
				<td colspan="16" align="center" style="font-size:18px;padding-left: 270px;"><strong><u>Delivery Challan, Finishing Section.</u></strong></td>
			</tr>
			<tr>
				<td colspan="16" align="center" style="font-size:18px;padding-left: 270px;"><strong><u>Delivery From :  <? echo $location_arr[$dye_location]; ?></u></strong></td>
			</tr>
			<tr>
				<td id="barcode_img_id"></td>
			</tr>
		</table>
		<br>
		<table width="1000" cellspacing="0" align="left" border="0">
			<tr>
				<td   style="font-size:16px; font-weight:bold;" width="110">Delivery To</td>
				<td >:<? echo  $company_arr[$deli_company]; ?></td>
				<td   style="font-size:16px; font-weight:bold;" width="110">Delivery Location</td>
				<td >:<? echo $location_arr[$deli_location]; ?></td>
			</tr>
			<tr>
				<td   style="font-size:16px; font-weight:bold;" width="110">Challan No</td>
				<td >:<? echo $Challan_no; ?></td>
				<td style="font-size:16px; font-weight:bold;" width="150">Delivery Date</td>
				<td  >:<? echo $delivery_date; ?></td>
			</tr>
			<tr>
				<td style="font-size:16px; font-weight:bold;" width="150">Remarks:</td>
				<td colspan="3" >:<? echo $txt_remark; ?></td>
			</tr>
		</table>
	</div>
	<div style="width:2120px">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2120" class="rpt_table" >
			<thead>
				<tr>
					<th width="30" >SL</th>
					<th width="110">Order No</th>
                    <th width="110">Internal Ref</th>
					<th width="60"> Order Qty [Pcs]</th>
					<th width="100">Buyer <br> & Job</th>
					<th width="100">Style No</th>
					<th width="40">System ID</th>
					<th width="100">Batch No</th>
					<th width="80">Fabric Shade</th>
					<th width="100">Booking No</th>
					<th width="80">Yarn Lot</th>
					<th width="110">Fin. Prod.  Company</th>
					<th width="80">Color</th>
					<th width="100">Color Type</th>
					<th width="100">Body Part</th>
					<th width="100">Process Name</th>
					<th width="60">Dia Type</th>
					<th width="200">Fabric Type</th>
					<th width="50">Fin GSM</th>
					<th width="40">Fin. Dia</th>
					<th width="100">Grey Dia</th>
					<th width="40">T. Roll</th>
					<th width="70">Grey Used</th>
					<th width="70">Delivery Qty</th>					
					<?
					if($operation==4)
					{
						?>
						<th width="70">Process Loss%</th>
						<th width="80">Remarks</th>
					<? } ?>
				</tr>
			</thead>
			<tbody>
				<?
				if($db_type==0)
				{
					if($from_date!="" && $to_date!="") $date_con="and a.receive_date between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'"; else $date_con="";
					$select_year="year(e.insert_date) as job_year";
				}
				else if($db_type==2)
				{
					if($from_date!="" && $to_date!="") $date_con="and a.receive_date between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'"; else $date_con="";
					$select_year="to_char(e.insert_date,'YYYY') as job_year";
				}

				if($location!=0) $location_con="and a.location_id=$location"; else $location_con="";
				if($buyer!=0) $buyer_con="and a.buyer_id=$buyer"; else $buyer_con="";

				if($order_ids!="")
				{
					$sql_machineDia=sql_select("select a.febric_description_id, b.po_breakdown_id, a.machine_dia
						from inv_receive_master p, pro_grey_prod_entry_dtls a, order_wise_pro_details b
						where p.id=a.mst_id and a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and p.status_active=1 and p.is_deleted=0 and b.po_breakdown_id in ($order_ids) and p.entry_form in(2,22) and a.machine_dia is not NULL group by a.febric_description_id, b.po_breakdown_id, a.machine_dia");
				}
				else
				{
					$sql_machineDia=sql_select("select p.booking_no, a.febric_description_id, a.machine_dia
						from inv_receive_master p, pro_grey_prod_entry_dtls a
						where p.id=a.mst_id  and a.status_active=1 and a.is_deleted=0 and p.status_active=1 and p.is_deleted=0 and p.booking_without_order=1 and a.machine_dia is not NULL $all_booking_no_cond_2 group by p.booking_no, a.febric_description_id, a.machine_dia");
				}

				$mc_dia_arr=array();
				foreach($sql_machineDia as $rows)
				{
					if($order_ids!="")
						$mc_dia_arr[$rows[csf('po_breakdown_id')]][$rows[csf('febric_description_id')]].=$rows[csf('machine_dia')].',';
					else
						$mc_dia_arr[$rows[csf('booking_no')]][$rows[csf('febric_description_id')]].=$rows[csf('machine_dia')].',';
				}
				unset($sql_machineDia);

				if($fin_prod_type==1)
				{
					$sql="SELECT a.id, a.recv_number_prefix_num, a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.id as dtls_id, b.fabric_description_id as determination_id, b.fabric_shade, b.gsm as gsm, b.width as dia, b.prod_id, b.batch_id as batch_id, b.color_id as color_id, b.body_part_id, b.process_id, b.dia_width_type, c.po_breakdown_id, c.process_loss_perc, sum(c.quantity) as quantity, d.po_number, e.style_ref_no, d.grouping, e.job_no, e.job_no_prefix_num, e.buyer_name, sum(d.po_quantity*e.total_set_qnty) as po_qty, $select_year 
					from inv_receive_master a,  pro_finish_fabric_rcv_dtls b ,order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e 
					where a.id=b.mst_id  and b.id=c.dtls_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and a.entry_form=7 and c.entry_form=7 and c.status_active=1 and c.is_deleted=0 and c.trans_type=1 and a.status_active=1 and a.is_deleted=0 and a.company_id=$company and a.id in ($program_ids) and b.prod_id in ($product_ids) and c.po_breakdown_id in($order_ids) $date_con $location_con 
					group by b.batch_id,b.id,a.id,a.recv_number_prefix_num,a.recv_number,a.receive_date,a.receive_basis,a.knitting_source,a.knitting_company,b.fabric_description_id,b.fabric_shade,b.gsm,b.width,b.prod_id,b.color_id,b.body_part_id,b.process_id,b.dia_width_type,c.po_breakdown_id, c.process_loss_perc, d.po_number, e.style_ref_no, d.grouping,e.job_no,e.job_no_prefix_num,e.buyer_name,e.insert_date order by b.batch_id"; 
				}
				else
				{
					$sql="SELECT a.id, a.recv_number_prefix_num, a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.id as dtls_id, b.fabric_description_id as determination_id, b.fabric_shade, b.gsm as gsm, b.width as dia, b.prod_id, b.batch_id as batch_id, b.color_id as color_id, b.body_part_id, b.process_id, b.dia_width_type, 0 as process_loss_perc, sum(b.receive_qnty) as quantity, '' as po_breakdown_id, '' as po_number, '' as job_no, '' as job_no_prefix_num, '' as buyer_name, '' as job_year, '' as style_ref_no 
					from inv_receive_master a,  pro_finish_fabric_rcv_dtls b 
					where a.id=b.mst_id and a.entry_form=7 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_chack_cond and a.company_id=$company and a.id in ($program_ids) and b.prod_id in ($product_ids) $date_con $location_con 
					group by b.batch_id, b.id,a.id, a.recv_number_prefix_num,a.recv_number,a.receive_date,a.receive_basis,a.knitting_source,a.knitting_company,b.fabric_description_id,b.fabric_shade,b.gsm,b.width,b.prod_id,b.color_id,b.body_part_id,b.process_id,b.dia_width_type order by b.batch_id"; 
				}

				$nameArray=sql_select( $sql);
				$batch_id_arr  = array();
				foreach ($nameArray as $row) {
					array_push($batch_id_arr,$row[csf('batch_id')]);
				}

				/*$color_type_sql=sql_select("select job_no,color_type_id,body_part_id,lib_yarn_count_deter_id from wo_pre_cost_fabric_cost_dtls where is_deleted=0 and status_active=1");
				$arr_color_type=array();
				foreach($color_type_sql as $row)
				{
					$arr_color_type[$row[csf("job_no")]][$row[csf("body_part_id")]][$row[csf("lib_yarn_count_deter_id")]]["color_type_id"]=$row[csf("color_type_id")];
				}*/

				$booking_without_order = sql_select("select a.booking_no_prefix_num, a.buyer_id,b.body_part,b.booking_no,b.color_type_id,b.fabric_color from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where  a.booking_no=b.booking_no and a.company_id=$company and a.booking_type=4 $all_booking_no_cond");
				foreach ($booking_without_order as $row)
				{
					$color_type_array[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('fabric_color')]]['color_type_id'] = $row[csf('color_type_id')];

				}

				$color_sqls = "select  b.booking_no,c.color_type_id,c.body_part_id,b.fabric_color_id from wo_booking_mst a, wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.company_id=$company and a.booking_type=1 and b.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $all_booking_no_cond group by b.booking_no,c.color_type_id,c.body_part_id,b.fabric_color_id";
				$color_sql_result = sql_select($color_sqls);
				foreach ($color_sql_result as $keys=>$row2)
				{
					$color_type_array[$row2[csf('booking_no')]][$row2[csf('body_part_id')]][$row2[csf('fabric_color_id')]]['color_type_id'] = $row2[csf('color_type_id')];
				}

				$yarn_sql = "SELECT e.booking_id, b.prod_id, d.yarn_lot, d.yarn_count, d.brand_id
				from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,  inv_receive_master e
				where a.id=b.mst_id and b.roll_id=c.id and c.dtls_id=d.id and d.mst_id=e.id and a.company_id=$company ".where_con_using_array($batch_id_arr,0,'a.id')." and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22)
				group by e.booking_id, b.prod_id, d.yarn_lot, d.yarn_count, d.brand_id";
				//echo $yarn_sql;
				$sql_data_result = sql_select($yarn_sql);
				$yarn_data_arr=array();
				foreach ($sql_data_result as $row)
				{
					$yarn_data_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['yarn_lot'].=$row[csf('yarn_lot')].',';
				}
				//echo "<pre>";print_r($yarn_data_arr);

				//echo $sql;
				$i=1; $tot_roll=0; $tot_qty=0;
				foreach($nameArray as $row)
				{
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					if($fin_prod_type==1)
					{
						$index_pk=$row[csf("id")]."*".$row[csf("prod_id")]."*".$row[csf("po_breakdown_id")]."*".$row[csf("dtls_id")];
					}
					else
					{
						$index_pk=$row[csf("id")]."*".$row[csf("prod_id")]."*".$row[csf("dtls_id")];
					}

					$process_all=array_unique(explode(",",$row[csf('process_id')]));
					$process_name='';
					$process_id_array=explode(",",$batch_process);
					foreach($process_all as $val)
					{
						if($process_name=="") $process_name=$conversion_cost_head_array[$val]; else $process_name.=",".$conversion_cost_head_array[$val];
					}

					$yarn_lot_all=$yarn_data_arr[$batch_details[$row[csf("batch_id")]]["program_no"]][$batch_details[$row[csf("batch_id")]]["prod_id"]]['yarn_lot'];
					$yarn_lot = implode(",", array_unique(explode(",", chop($yarn_lot_all,','))));

					$supplier_brand_value="";$yarn_count_value="";

					if($fin_prod_type==1)
					{
						// echo $row[csf('po_breakdown_id')].'='.$batch_details[$row[csf("batch_id")]]["prod_id"].'='.$batch_details[$row[csf("batch_id")]]["program_no"];
						$y_count=array_unique(explode(',',chop($knit_production_data[$batch_details[$row[csf("batch_id")]]["prod_id"]][$row[csf('po_breakdown_id')]]['yarn_count'],",")));
						foreach($y_count as $count_id)
						{
							if($count_id>0)
							{
								if($yarn_count_value=='') $yarn_count_value=$yarncount[$count_id]; else $yarn_count_value.=", ".$yarncount[$count_id];
							}
						}

						$supplier_brand_arr=array_unique(explode(',',chop($knit_production_data[$batch_details[$row[csf("batch_id")]]["prod_id"]][$row[csf('po_breakdown_id')]]['brand_id'],",")));
						foreach($supplier_brand_arr as $brand_id)
						{
							if($brand_id>0)
							{
								if($supplier_brand_value=='') $supplier_brand_value=$supplier_brand[$brand_id]; else $supplier_brand_value.=", ".$supplier_brand[$brand_id];
							}
						}
						//echo $y_lot_arr = $batch_details[$row[csf("batch_id")]]["prod_id"];
						// $y_lot_arr=array_unique(explode(',',chop($knit_production_data[$batch_details[$row[csf("batch_id")]]["prod_id"]][$row[csf('po_breakdown_id')]]['lot'],",")));
					}
					else
					{
						$y_count=array_unique(explode(',',chop($knit_production_data[$batch_details[$row[csf("batch_id")]]["booking_no"]][$batch_details[$row[csf("batch_id")]]["brand_id"]]['yarn_count'],",")));
						foreach($y_count as $count_id)
						{
							if($count_id>0)
							{
								if($yarn_count_value=='') $yarn_count_value=$yarncount[$count_id]; else $yarn_count_value.=", ".$yarncount[$count_id];
							}
						}

						$supplier_brand=array_unique(explode(',',chop($knit_production_data[$batch_details[$row[csf("batch_id")]]["booking_no"]][$batch_details[$row[csf("batch_id")]]["brand_id"]]['brand_id'],",")));

						foreach($supplier_brand as $brand_id)
						{
							if($brand_id>0)
							{
								if($supplier_brand_value=='') $supplier_brand_value=$supplier_brand[$brand_id]; else $supplier_brand_value.=", ".$supplier_brand[$brand_id];
							}
						}
					}

					$ms_dia="";
					if($row[csf('po_breakdown_id')]!="") $ms_dia=$mc_dia_arr[$row[csf('po_breakdown_id')]][$row[csf('determination_id')]];
					else $ms_dia=$mc_dia_arr[$batch_details[$row[csf("batch_id")]]['booking_no']][$row[csf('determination_id')]];

					$all_machineDias=chop($ms_dia,',');
					$all_machineDias=implode(",",array_unique(explode(",",$all_machineDias)));

					if($update_row_check[$index_pk]["current_delivery"]>0)
					{
						//$internal_ref_no=return_field_value("a.po_number,b.internal_ref","wo_po_break_down a,  wo_order_entry_internal_ref b","a.job_no_mst=b.job_no and a.po_number='".$row[csf('po_number')]."'","internal_ref");
						$internal_ref_no=$row[csf('grouping')];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td width="30"  align="center"><div style="word-wrap:break-word; width:30px"><? echo $i; ?></div></td>
							<td width="110"><div style="word-wrap:break-word; width:110px"><? echo $row[csf('po_number')];?></div></td>
                            <td width="110"><div style="word-wrap:break-word; width:110px; text-align:center"><? echo $internal_ref_no;?>&nbsp;</div></td>
							<td width="60" align="right"><p><? echo $row[csf('po_qty')];?></p></td>
							<td width="100"><div style="word-wrap:break-word; width:100px">
								<?
								if($batch_details[$row[csf('batch_id')]]['booking_without_order']==1)
								{
									echo $buyer_array[$non_order_buyer[$batch_details[$row[csf("batch_id")]]['booking_no_id']]];
								}
								else
								{
									echo $buyer_array[$row[csf("buyer_name")]];
								}
								echo "<br>";
								if($row[csf('job_no_prefix_num')]!="")
								{
									echo "Job-".$row[csf('job_no_prefix_num')];
								}
								?></div>
							</td>

							<td width="100" align="center"><div style="word-wrap:break-word; width:40px"><? echo $row[csf('style_ref_no')]; ?></div></td>
							<td width="40" align="center"><div style="word-wrap:break-word; width:40px"><? echo $row[csf('recv_number_prefix_num')]; ?></div></td>
							<td width="100" align="center"><div style="word-wrap:break-word; width:50px"><? echo $batch_details[$row[csf('batch_id')]]['batch_no'];?></div></td>
							<td width="80" align="center"><div style="word-wrap:break-word; width:80px"><? echo $fabric_shade[$row[csf('fabric_shade')]]; ?></div></td>
							<td align="center" width="100"><div style="word-wrap:break-word; width:100px"><? echo $batch_details[$row[csf("batch_id")]]['booking_no']; ?></div></td>
							<td width="80" align='center'><div style="word-wrap:break-word; width:200px">
								<?
								if($yarn_lot!="") echo $yarn_lot;
								?></div></td>
							<td width="110"><div style="word-wrap:break-word; width:110px">
								<?
								if($row[csf("knitting_source")]==1)  echo $company_arr[$row[csf("knitting_company")]];
								else if($row[csf("knitting_source")]==3) echo $supplier_arr[$row[csf("knitting_company")]];
								?>
							</div></td>
							<td width="80"><div style="word-wrap:break-word; width:80px"><? echo $color_arr[$row[csf('color_id')]];?></div></td>

							<td width="80">
								<div style="word-wrap:break-word; width:80px">
								<?
								$color_type_id="";
								if($row[csf('po_breakdown_id')])
								{
										$color_type_id= $batch_color_type_with_po_arr[$row[csf("batch_id")]][$row[csf("body_part_id")]][$row[csf('gsm')]][$row[csf('determination_id')]][$row[csf('po_breakdown_id')]]["color_type"];
								}
								else
								{
									$color_type_id= $batch_color_type_without_po_arr[$row[csf("batch_id")]][$row[csf("body_part_id")]][$row[csf('gsm')]][$row[csf('determination_id')]]["color_type"];
								}

								if($color_type_id*1 == 0)
								{
									$color_type_id=$color_type_array[$batch_details[$row[csf("batch_id")]]['booking_no']][$row[csf("body_part_id")]][$row[csf("color_id")]]['color_type_id'];
								}
								echo $color_type[$color_type_id];
								?></div>
							</td>

							<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $body_part[$row[csf("body_part_id")]];?></div></td>

							<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $process_name;?></div></td>
							<td width="60"><div style="word-wrap:break-word; width:60px"><? echo $fabric_typee[$row[csf('dia_width_type')]];?></div></td>
							<td width="200"><div style="word-wrap:break-word; width:200px">
								<?
								
								echo $composition_arr[$row[csf('determination_id')]]."<br>";;
								
								//if($yarn_count_value!="") echo $yarn_count_value;
								//if($supplier_brand_value!="") echo ", ".$supplier_brand_value;
								?></div>
							</td>
							<td width="50" align="center"><div style="word-wrap:break-word; width:50px"><?  echo $row[csf('gsm')]; ?></div></td>
							<td width="40" align="center"><div style="word-wrap:break-word; width:40px"><? echo $row[csf('dia')];?></div></td>

							<td width="100" align="center"><div style="word-wrap:break-word; width:100px"><? echo $all_machineDias; //echo $arr_machineDia[$row[csf('po_breakdown_id')]]['machine_dia'];?></div></td>


							<td width="40" align="center"><div style="word-wrap:break-word; width:40px"><? echo $update_row_check[$index_pk]["roll"]; $tot_roll+=$update_row_check[$index_pk]["roll"]; ?></div></td>
							<td  align="right" width="70">
								<div style="word-wrap:break-word; width:70px">
								<?
								/* if($fin_prod_type==1)
								{
								echo number_format($grey_userd_data_arr[$row[csf("dtls_id")]][$row[csf("po_breakdown_id")]]["grey_used_qty"],2);
									$tot_grey_used+=$grey_userd_data_arr[$row[csf("dtls_id")]][$row[csf("po_breakdown_id")]]["grey_used_qty"];
								}
								else
								{

								  echo number_format($fin_pord_data[$row[csf("dtls_id")]]["grey_used_qty"],2);
								  $tot_grey_used+=$fin_pord_data[$row[csf("dtls_id")]]["grey_used_qty"];
								} */

								if($process_loss_method==1)
								{
									$grey_used_quanity = $update_row_check[$index_pk]["current_delivery"] + ($row[csf("process_loss_perc")]*$update_row_check[$index_pk]["current_delivery"])/100;
								}
								else
								{
									$grey_used_quanity = $update_row_check[$index_pk]["current_delivery"] / ( 1- $row[csf("process_loss_perc")]/100);
								}

								//$grey_used_quanity = $update_row_check[$index_pk]["current_delivery"] + ($update_row_check[$index_pk]["current_delivery"]* $row[csf("process_loss_perc")])/100;

								$tot_grey_used+= $grey_used_quanity;

								echo number_format($grey_used_quanity,2);
								?>
								</div>
							</td>
							<td  align="right" width="70"><div style="word-wrap:break-word; width:70px"><? echo number_format($update_row_check[$index_pk]["current_delivery"],2); $tot_qty+=$update_row_check[$index_pk]["current_delivery"]; ?></div></td>
							<?
							if($operation==4)
							{
								?>
								<td align="right" width="70"><div style="word-wrap:break-word; width:70px"><? echo $row[csf("process_loss_perc")]; ?></div></td>
								<td width="80"><div style="word-wrap:break-word; width:80px;"><? echo $fin_pord_data[$row[csf("dtls_id")]]["remarks"] ?></div></td>
							<? } ?>
						</tr>
						<?
						$i++;
					}
					$batch_dia_type="";
				}
				?>
				<tr>
					<?
					if($operation==4){$colspan=19;}else{$colspan=17;}
					?>
					<td align="right" colspan="21" ><strong>Total:</strong></td>
					<td align="center"><? echo $tot_roll; ?></td>
					<td align="right" ><? echo number_format($tot_grey_used,2,'.',''); ?></td>
					<td align="right" ><? echo number_format($tot_qty,2,'.',''); ?></td>
					<td align="right" ></td>
					<?
					if($operation==4)
					{
						?>
						<td ></td>
						<td ></td>
					<? } ?>
				</tr>
				<? if($operation==5){?>
				<tr>
					<!-- <td colspan="21"> <b>  Remarks : </b> <?// echo// $txt_remarks;?></td> -->
				</tr>
				<?}?>
			</tbody>
		</table>
		<br>
		<?
		echo signature_table(68, $company, "1600px",$cbo_template_id);
		?>

		<script type="text/javascript" src="../includes/functions.js"></script>
		<script type="text/javascript" src="../js/jquery.js"></script>
		<script type="text/javascript" src="../js/jquerybarcode.js"></script>
		<script>
			fnc_generate_Barcode("<? echo $Challan_no;?>","barcode_img_id");
		</script>
	</div>
	<?
	exit();
}

if($action=="delivery_challan_print_sales")
{
	extract($_REQUEST);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$datas=explode('_',$data);

	$company = str_replace("'","",$datas[1]);
	$update_mst_id = str_replace("'","",$datas[8]);
	$delivery_date = str_replace("'","",$datas[9]);
	$Challan_no = str_replace("'","",$datas[10]);
	$txt_remarks = str_replace("'","",$datas[14]);
	$cbo_template_id = str_replace("'","",$datas[15]);

	$com_sql=sql_select("select id, company_name, plot_no, level_no, road_no, block_no, city, zip_code from lib_company where id=$company");
	$com_address="";
	foreach($com_sql as $row)
	{
	$company_name=$row[csf("company_name")];
	if($row[csf("plot_no")]!="") $com_address.=$row[csf("plot_no")].", ";
	if($row[csf("level_no")]!="") $com_address.=$row[csf("level_no")].", ";
	if($row[csf("road_no")]!="") $com_address.=$row[csf("road_no")].", ";
	if($row[csf("block_no")]!="") $com_address.=$row[csf("block_no")].", ";
	if($row[csf("city")]!="") $com_address.=$row[csf("city")].", ";
	if($row[csf("zip_code")]!="") $com_address.=$row[csf("zip_code")].", ";
	}
	$com_address=chop($com_address," , ");

	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$company'","image_location");

	$supplier_arr=return_library_array( "select id, supplier_name from  lib_supplier",'id','supplier_name');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');

	if($update_mst_id!="")
	{
	$sql="select a.sys_number,a.company_id as finish_company_id,a.delevery_date,b.grey_sys_id,b.order_id,b.batch_id,b.fabric_shade,b.color_id,b.bodypart_id,b.width_type,b.determination_id,b.gsm,b.dia,b.uom,
	b.roll,b.remarks,sum(b.current_delivery) as delivery_qty
	from pro_grey_prod_delivery_mst a,pro_grey_prod_delivery_dtls b
	where a.id=b.mst_id and a.id=$update_mst_id

	group by a.company_id,a.sys_number,a.delevery_date,b.grey_sys_id,b.order_id,b.batch_id,b.fabric_shade,b.color_id,b.bodypart_id,b.width_type,b.determination_id,b.gsm,b.dia,b.uom,
	b.roll,b.remarks";
	}

	$result=sql_select($sql);

	foreach($result as $row)
	{
	$salses_order_ids .= $row[csf('order_id')].",";
	$batchIds .= $row[csf('batch_id')].",";
	$determinationIds .= $row[csf('determination_id')].",";
	$finisProductIds .= $row[csf('grey_sys_id')].",";

	}

	$salses_order_ids=implode(",",array_filter(array_unique(explode(",",$salses_order_ids))));
	$finisProductIds=implode(",",array_filter(array_unique(explode(",",$finisProductIds))));


	$prod_result = sql_select("select a.id,a.recv_number_prefix_num,a.knitting_company,a.knitting_source,b.width,b.process_id,b.grey_used_qty from inv_receive_master a,pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.id in($finisProductIds) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	$fininsProductionArr = array();
	$fininsProductionGreyArr = array();

	foreach($prod_result as $row)
	{
	$fininsProductionArr[$row[csf('id')]]['recv_number'] = $row[csf('recv_number_prefix_num')];
	$fininsProductionArr[$row[csf('id')]]['grey_dia'] = $row[csf('width')];
	$fininsProductionArr[$row[csf('id')]]['process_id'] = $row[csf('process_id')];
	//$fininsProductionArr[$row[csf('id')]]['grey_used_qty'] = $row[csf('grey_used_qty')];
	$fininsProductionGreyArr[$row[csf('id')]][$row[csf('width')]]['grey_used_qty'] = $row[csf('grey_used_qty')];


	if($row[csf("knitting_source")]==1) {
	$fininsProductionArr[$row[csf('id')]]['knitting_company'] = $company_arr[$row[csf("knitting_company")]];
	}
	else if ($row[csf("knitting_source")]==3)
	{
	$fininsProductionArr[$row[csf('id')]]['knitting_company'] = $supplier_arr[$row[csf("knitting_company")]];
	}
	}
	unset($prod_result);


	if($salses_order_ids!="")
	{

	$sql_machineDia=sql_select("select a.febric_description_id, b.po_breakdown_id, a.machine_dia
	from inv_receive_master p, pro_grey_prod_entry_dtls a, order_wise_pro_details b
	where p.id=a.mst_id and a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and p.status_active=1 and p.is_deleted=0 and b.po_breakdown_id in ($salses_order_ids) and p.entry_form in(2,22) and a.machine_dia is not NULL group by a.febric_description_id, b.po_breakdown_id, a.machine_dia"); //


	$fabricSaleData = sql_select("select a.id,a.job_no,a.within_group,a.booking_without_order,a.buyer_id,a.po_buyer,a.po_job_no,a.sales_booking_no,sum(b.finish_qty) as order_qty from fabric_sales_order_mst a ,fabric_sales_order_dtls b where a.id=b.mst_id and a.id in ($salses_order_ids) group by a.id,a.job_no,a.within_group,a.booking_without_order,a.buyer_id,a.po_buyer,a.po_job_no,a.sales_booking_no");

	$salesData = array();
	foreach($fabricSaleData as $row)
	{
	$salesData[$row[csf('id')]]['order_qty'] = $row[csf('order_qty')];
	$salesData[$row[csf('id')]]['fso_no'] = $row[csf('job_no')];
	$salesData[$row[csf('id')]]['within_group'] = $row[csf('within_group')];
	$salesData[$row[csf('id')]]['booking_without_order'] = $row[csf('booking_without_order')];

	$salesData[$row[csf('id')]]['sales_booking_no'] = $row[csf('sales_booking_no')];

	$booking_no_arr[] = "'".$row[csf('sales_booking_no')]."'";

	if($row[csf('within_group')]==1)
	{
		$salesData[$row[csf('id')]]['buyer_id'] = $row[csf('po_buyer')];
		$salesData[$row[csf('id')]]['job_no'] = $row[csf('po_job_no')];
	}
	else
	{
		$salesData[$row[csf('id')]]['buyer_id'] = $row[csf('buyer_id')];
		$salesData[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
	}
	}
	}
	unset($fabricSaleData);

	$all_machineDia="";
	$mc_dia_arr=array();
	foreach($sql_machineDia as $rows)
	{
	$mc_dia_arr[$rows[csf('po_breakdown_id')]][$rows[csf('febric_description_id')]].=$rows[csf('machine_dia')].',';
	}
	unset($sql_machineDia);


	$batch_ids=implode(",",array_filter(array_unique(explode(",",$batchIds))));

	if($batch_ids!=""){
	$batch_data=sql_select("select a.id, a.batch_no,a.booking_no from  pro_batch_create_mst a where a.id in($batch_ids)");

	foreach($batch_data as $row)
	{
	$batch_details[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
	$batch_details[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
	}
	}
	unset($batch_data);

	$determinationIds=implode(",",array_filter(array_unique(explode(",",$determinationIds))));

	if($determinationIds!="")
	{
	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id in($determinationIds)";
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
	}
	unset($data_array);
	ob_start();
	?>

	<div style="width:1200px;">
	<table width="1200" cellspacing="0" align="center" border="0">
			<tr>
				<td colspan="1" rowspan="5">
					<img src="../<? echo $image_location; ?>" height="70" width="200" style="float:left; margin-left:10px;">
				</td>
				<td colspan="15" align="center" style="font-size:x-large"><strong>Working Company: <? echo $company_name; ?></strong>
				</td>
			</tr>
			<tr>
				<td colspan="16" align="center" style="font-size:18px"><strong><? echo $com_address; ?></strong></td>
			</tr>
			<tr>
				<td colspan="16" align="center" style="font-size:18px"><strong>LC Company: <? echo $company_name; ?></strong></td>
			</tr>
			<tr>
				<td colspan="16" align="center" style="font-size:18px"><strong>LC Company Location: <? echo $location_arr[$location]; ?></strong></td>
			</tr>
			<tr>
				<td colspan="16" align="center" style="font-size:18px"><strong><u>Delivery Challan, Finishing Section.</u></strong></td>
			</tr>
			<tr>
				<td id="barcode_img_id"></td>
			</tr>
	</table>
	<br>
	<table width="1200" cellspacing="0" align="center" border="0">
	<tr>
		<td   style="font-size:16px; font-weight:bold;" width="110">Challan No</td>
		<td colspan="15">:<? echo $Challan_no; ?></td>
	</tr>
	<tr>
		<td style="font-size:16px; font-weight:bold;" width="110">Delivery Date </td>
		<td colspan="15" >:<? echo $delivery_date; ?></td>
	</tr>
	</table>
	</div>

	<div style="width:1920px">
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1920" class="rpt_table" >
	<thead>
		<tr>
			<th width="30" >SL</th>
			<th width="110">Order/FSO No</th>
			<th width="60"> Order Qty [Pcs]</th>
			<th width="100">Buyer <br> & Job</th>
			<th width="40">System ID</th>
			<th width="50">Batch No</th>
			<th width="40">Fabric Shade</th>
			<th width="100">Booking No</th>
			<th width="110">Fin. Prod.  Company</th>
			<th width="80">Color</th>
			<th width="100">Body Part</th>
			<th width="300">Process Name</th>
			<th width="60">Dia Type</th>
			<th width="200">Fabric Type</th>
			<th width="50">Fin GSM</th>
			<th width="40">Fin. Dia</th>
			<th width="40">UOM</th>
			<th width="100">Grey Dia</th>
			<th width="40">T. Roll</th>
			<th width="70">Grey Used</th>
			<th>Delivery Qty</th>
		</tr>
	</thead>
	<tbody>

		<?php
		$nameArray=sql_select( $sql);
		$i=1;
		$tot_roll=0;
		$tot_qty=0;
		$tot_grey_used = 0;

		foreach($nameArray as $row)
		{
			if ($i%2==0)
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";

			if($row[csf("delivery_qty")]>0){

			$buerJobStr = $buyer_array[$salesData[$row[csf('order_id')]]['buyer_id']]."<br/>".$salesData[$row[csf('order_id')]]['job_no'];

			$processIdArr = explode(",", $fininsProductionArr[$row[csf('grey_sys_id')]]['process_id']);
			$process_name="";
			foreach($processIdArr as $val)
			{
				if($process_name=="") $process_name=$conversion_cost_head_array[$val]; else $process_name.=",".$conversion_cost_head_array[$val];
			}

			$process_name = chop($process_name,",");

			?>
			<tr bgcolor="<? echo $bgcolor; ?>">

				<td width="30"  align="center"><div style="word-wrap:break-word; width:30px"><? echo $i; ?></div></td>
				<td width="110"><div style="word-wrap:break-word; width:110px"><? echo $salesData[$row[csf('order_id')]]['fso_no'];?></div></td>

				<td width="60" align="right"><p><? echo number_format($salesData[$row[csf('order_id')]]['order_qty']); ?></p></td>

				<td width="100">
					<div style="word-wrap:break-word; width:100px">
						<? echo $buerJobStr;?>
					</div>
				</td>

				<td width="40" align="center"><div style="word-wrap:break-word; width:40px"><? echo $fininsProductionArr[$row[csf('grey_sys_id')]]['recv_number'];?></div></td>

				<td width="50" align="center"><? echo $batch_details[$row[csf('batch_id')]]['batch_no'];?></td>

				<td width="40" align="center"><div style="word-wrap:break-word; width:40px"><? echo $fabric_shade[$row[csf('fabric_shade')]];?></div></td>

				<td align="center" width="100"><? echo $salesData[$row[csf('order_id')]]['sales_booking_no'];?></td>
				<td width="110"><? echo $fininsProductionArr[$row[csf('grey_sys_id')]]['knitting_company'];?></td>
				<td width="80"><div style="word-wrap:break-word; width:80px"><? echo $color_arr[$row[csf('color_id')]];?></div></td>

				<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $body_part[$row[csf("bodypart_id")]];?></div></td>

				<td width="300"><div style="word-wrap:break-word; width:300px"><? echo $process_name;?></div></td>
				<td width="60"><div style="word-wrap:break-word; width:60px"><? echo $fabric_typee[$row[csf('width_type')]];?></div></td>
				<td width="200">
					<div style="word-wrap:break-word; width:200px">
						<? echo $composition_arr[$row[csf('determination_id')]]; ?>
					</div>
				</td>
				<td width="50" align="center"><div style="word-wrap:break-word; width:50px"><? echo $row[csf('gsm')]; ?></div></td>
				<td width="40" align="center">
					<div style="word-wrap:break-word; width:40px"><? echo $row[csf('dia')]; ?></div>
				</td>
				<td width="40" align="center">
					<div style="word-wrap:break-word; width:40px"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></div>
				</td>

				<td width="100" align="center">
					<div style="word-wrap:break-word; width:100px">
						<?

						$mc_dia=$mc_dia_arr[$row[csf('order_id')]][$row[csf('determination_id')]];

						echo chop($mc_dia,','); ?>
					</div>
				</td>

				<td width="40" align="center">
					<?
						$tot_roll += $row[csf('roll')];
						echo $row[csf('roll')];
					?>
				</td>

				<td  align="right" width="70">
					<?
					$tot_grey_used+=$fininsProductionGreyArr[$row[csf('grey_sys_id')]][$row[csf('dia')]]['grey_used_qty'];
					echo $fininsProductionGreyArr[$row[csf('grey_sys_id')]][$row[csf('dia')]]['grey_used_qty'];
					?>

				</td>

				<td  align="right">
					<div style="word-wrap:break-word; width:70px">

						<?
						$tot_qty += $row[csf("delivery_qty")];
						echo number_format($row[csf("delivery_qty")],2);
						?>
					</div>
				</td>
			</tr>

			<?php
			$i++;
		}
	}
		?>

		<tr>
			<td align="right" colspan="18" ><strong>Total:</strong></td>
			<td align="center"><? echo $tot_roll; ?></td>
			<td align="right" ><? echo number_format($tot_grey_used,2,'.',''); ?></td>
			<td align="right" ><? echo number_format($tot_qty,2,'.',''); ?></td>
		</tr>

		<tr>
			<td colspan="22"> <b>  Remarks : </b> <? echo $txt_remarks; ?> </td>
		</tr>

		</table>
		<br>
		<?
		echo signature_table(68, $company, "1600px",$cbo_template_id);
		?>

		<script type="text/javascript" src="../includes/functions.js"></script>
		<script type="text/javascript" src="../js/jquery.js"></script>
		<script type="text/javascript" src="../js/jquerybarcode.js"></script>
		<script>
			fnc_generate_Barcode("<? echo $Challan_no;?>","barcode_img_id");
		</script>
	</div>

	<?
	exit();
} // end action

if($action=="delivery_challan_print_3_backup") // 28 sep 2019
{
	extract($_REQUEST);
	//echo '<pre>';print_r($_REQUEST);

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$datas=explode('_',$data);
	$program_ids = implode(",",array_unique(explode(",",str_replace("'","",$datas[0]))));
	$company = str_replace("'","",$datas[1]);
	$from_date = str_replace("'","",$datas[2]);
	$to_date = str_replace("'","",$datas[3]);
	$product_ids = str_replace("'","",$datas[4]);
	$order_ids = implode(",",array_unique(explode(",",str_replace("'","",$datas[5]))));
	$location= str_replace("'","",$datas[6]);
	$buyer = str_replace("'","",$datas[7]);
	$update_mst_id = str_replace("'","",$datas[8]);
	$delivery_date = str_replace("'","",$datas[9]);
	$Challan_no = str_replace("'","",$datas[10]);
	$fin_prod_type = str_replace("'","",$datas[11]);
	$batch_ids = implode(",",array_unique(explode(",",str_replace("'","",$datas[12]))));
	$operation = str_replace("'","",$datas[13]);
	$txt_remarks = str_replace("'","",$datas[14]);
	$dyeing_company = str_replace("'","",$datas[15]);
	$cbo_template_id = str_replace("'","",$datas[16]);


	if($order_ids==""){$order_id_print_cond="";}else{$order_id_print_cond=" and a.order_id in ($order_ids)";}

	if($db_type==0)
	{
		if($from_date!="" && $to_date!="") $date_con="and a.receive_date between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'"; else $date_con="";
		$select_year="year(e.insert_date) as job_year";
	}
	else if($db_type==2)
	{
		if($from_date!="" && $to_date!="") $date_con="and a.receive_date between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'"; else $date_con="";
		$select_year="to_char(e.insert_date,'YYYY') as job_year";
	}

	if($location!=0) $location_con="and a.location_id=$location"; else $location_con="";
	if($buyer!=0) $buyer_con="and a.buyer_id=$buyer"; else $buyer_con="";

	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$supplier_arr=return_library_array( "select id, supplier_name from  lib_supplier",'id','supplier_name');
	$yarncount=return_library_array( "select id, yarn_count from  lib_yarn_count",'id','yarn_count');
	$supplier_brand=return_library_array("select id,brand_name from lib_brand", "id","brand_name");
	$non_order_buyer=return_library_array("select id, buyer_id from wo_non_ord_samp_booking_mst where booking_type=4 and status_active=1 and is_deleted=0","id","buyer_id");

	$sql_forWorkingComp=sql_select("select a.knitting_source,a.knitting_company from inv_receive_master a,  pro_finish_fabric_rcv_dtls b
	where a.id=b.mst_id and a.entry_form=7 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_chack_cond and a.company_id=$company and a.id in ($program_ids) and b.prod_id in ($product_ids) $date_con $location_con
	order by b.batch_id");
	foreach($sql_forWorkingComp as $row)
	{
		if($row[csf("knitting_source")]==1)
		{
			$comID=$row[csf("knitting_company")];
			$knitting_source=$row[csf("knitting_source")];
		}
		else if($row[csf("knitting_source")]==3)
		{
			$comID=$row[csf("knitting_company")];
			$knitting_source=$row[csf("knitting_source")];

		}

	}

	if ($knitting_source==1) {
		$com_sql_working=sql_select("select id, company_name, plot_no, level_no, road_no, block_no, city, zip_code from lib_company where id=$comID");
		$com_address="";
		foreach($com_sql_working as $row)
		{
			$working_company_name=$row[csf("company_name")];
			if($row[csf("plot_no")]!="") $com_address.=$row[csf("plot_no")].", ";
			if($row[csf("level_no")]!="") $com_address.=$row[csf("level_no")].", ";
			if($row[csf("road_no")]!="") $com_address.=$row[csf("road_no")].", ";
			if($row[csf("block_no")]!="") $com_address.=$row[csf("block_no")].", ";
			if($row[csf("city")]!="") $com_address.=$row[csf("city")].", ";
			if($row[csf("zip_code")]!="") $com_address.=$row[csf("zip_code")].", ";
		}
		$com_address=chop($com_address," , ");
	}
	else if ($knitting_source==3) {
		$com_sql_working=sql_select("select id, supplier_name as company_name, address_1 from lib_supplier where id=$comID");
		$com_address="";
		foreach($com_sql_working as $row)
		{
			$working_company_name=$row[csf("company_name")];
			if($row[csf("address_1")]!="") $com_address.=$row[csf("address_1")].", ";
		}
		$com_address=chop($com_address," , ");
	}

	$com_sql=sql_select("select id, company_name, plot_no, level_no, road_no, block_no, city, zip_code from lib_company where id=$company");
	foreach($com_sql as $row)
	{
		$company_name=$row[csf("company_name")];
	}

	$batch_sql=sql_select("SELECT a.id, a.batch_no, a.booking_no_id, a.color_range_id, a.booking_no, a.booking_without_order, b.prod_id, b.batch_qnty,b.body_part_id from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.id in($batch_ids) group by a.id, a.batch_no, a.color_range_id, a.booking_no_id, a.booking_no, a.booking_without_order, b.prod_id, b.batch_qnty,b.body_part_id ");
	$batch_details=array();
	foreach($batch_sql as $row)
	{
		$batch_details[$row[csf("id")]]["batch_no"]=$row[csf("batch_no")];
		$batch_details[$row[csf("id")]]["booking_no"]=$row[csf("booking_no")];
		$batch_details[$row[csf("id")]]["color_range_id"]=$row[csf("color_range_id")];
		$batch_details[$row[csf("id")]]["booking_no_id"]=$row[csf("booking_no_id")];
		$batch_details[$row[csf("id")]]["booking_without_order"]=$row[csf("booking_without_order")];
		$batch_details[$row[csf("id")]]["prod_id"]=$row[csf("prod_id")];
		$batch_details[$row[csf("id")]]["batch_qnty"]+=$row[csf("batch_qnty")];
		$batch_details[$row[csf("id")]]["body_part_id"]=$row[csf("body_part_id")];
		$all_booking_no_arr[$row[csf("booking_no")]] = $row[csf("booking_no")];
	}

	$all_booking_no_arr = array_filter($all_booking_no_arr);
	$all_booking_nos = "'".implode("','", $all_booking_no_arr)."'";
	$all_booking_no_cond=""; $bookCond="";
	$all_booking_no_cond_2=""; $bookCond_2="";
	if($db_type==2 && count($all_booking_no_arr)>999)
	{
		$all_booking_no_arr_chunk=array_chunk($all_booking_no_arr,999) ;
		foreach($all_booking_no_arr_chunk as $chunk_arr)
		{
			$chunk_arr_value=implode(",",$chunk_arr);
			$bookCond.="  a.booking_no in($chunk_arr_value) or ";
			$bookCond_2.="  p.booking_no in($chunk_arr_value) or ";
		}

		$all_booking_no_cond.=" and (".chop($bookCond,'or ').")";
		$all_booking_no_cond_2.=" and (".chop($bookCond_2,'or ').")";
	}
	else
	{
		$all_booking_no_cond=" and a.booking_no in($all_booking_nos)";
		$all_booking_no_cond_2=" and p.booking_no in($all_booking_nos)";
	}



	if($fin_prod_type==1)
	{
		$yarn_lot_data=sql_select("select  a.brand_id, b.po_breakdown_id, a.prod_id, a.yarn_lot as yarn_lot, a.yarn_count, a.stitch_length as stitch_length,a.machine_no_id as machine_no_id
			from pro_grey_prod_entry_dtls a, order_wise_pro_details b
			where a.id=b.dtls_id and b.entry_form in(2) and b.po_breakdown_id in($order_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");

		foreach($yarn_lot_data as $rows)
		{
			$knit_production_data[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['lot'].=$rows[csf('yarn_lot')].",";
			$knit_production_data[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['yarn_count'].=$rows[csf('yarn_count')].",";
			$knit_production_data[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['stitch_length'].=$rows[csf('stitch_length')].",";
			$knit_production_data[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['brand_id'].=$rows[csf('brand_id')].",";
			$knit_production_data[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['machine_no_id'].=$rows[csf('machine_no_id')].",";
		}
	}
	else
	{
		$yarn_lot_data=sql_select("select p.booking_no, a.brand_id, a.prod_id, a.yarn_lot, a.yarn_count, a.stitch_length,a.machine_no_id
			from inv_receive_master p, pro_grey_prod_entry_dtls a
			where p.id=a.mst_id and p.booking_without_order=1 and p.entry_form in(2) and a.status_active=1 and a.is_deleted=0 and p.status_active=1 and p.is_deleted=0 $all_booking_no_cond_2");
		foreach($yarn_lot_data as $rows)
		{
			$knit_production_data[$rows[csf('booking_no')]][$rows[csf('prod_id')]]['yarn_count']=$rows[csf('yarn_count')];
			$knit_production_data[$rows[csf('booking_no')]][$rows[csf('prod_id')]]['stitch_length']=$rows[csf('stitch_length')];
			$knit_production_data[$rows[csf('booking_no')]][$rows[csf('prod_id')]]['lot']=$rows[csf('yarn_lot')];
			$knit_production_data[$rows[csf('booking_no')]][$rows[csf('prod_id')]]['machine_no_id']=$rows[csf('machine_no_id')];
			$knit_production_data[$rows[csf('booking_no')]][$rows[csf('prod_id')]]['brand_id']=$rows[csf('brand_id')];
		}
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
	$update_row_check=array(); $deliveryRemarks=array();
	if($update_mst_id!="")
	{
		$sql_update=sql_select("select b.id,b.grey_sys_id,b.product_id,b.job_no, null order_id, d.grouping, b.current_delivery,b.roll,b.program_no,b.sys_dtls_id,b.color_id,b.determination_id,b.bodypart_id,b.fabric_shade,b.dia,b.width_type,a.remarks,c.process_id, c.batch_id
		from pro_grey_prod_delivery_mst a,pro_grey_prod_delivery_dtls b left join wo_po_break_down d on b.order_id = d.id, pro_finish_fabric_rcv_dtls c
		where a.id=b.mst_id and b.mst_id=$update_mst_id and b.sys_dtls_id = c.id and c.status_active=1 and c.is_deleted=0 and b.entry_form=54 and a.status_active=1 and a.is_deleted=0");


		$all_sys_dtls_id="";
		foreach($sql_update as $row)
		{
			//$all_sys_dtls_id.=$row[csf("sys_dtls_id")].",";
			$all_sys_dtls_id.=$row[csf("grey_sys_id")].",";
			if($fin_prod_type==1)
			{
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("batch_id")]."*".$row[csf("color_id")]."*".$row[csf("determination_id")]."*".$row[csf("bodypart_id")]."*".$row[csf("fabric_shade")]."*".$row[csf("dia")]."*".$row[csf("width_type")]."*".$row[csf("process_id")]][$row[csf("grouping")]]["current_delivery"] +=$row[csf("current_delivery")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("batch_id")]."*".$row[csf("color_id")]."*".$row[csf("determination_id")]."*".$row[csf("bodypart_id")]."*".$row[csf("fabric_shade")]."*".$row[csf("dia")]."*".$row[csf("width_type")]."*".$row[csf("process_id")]][$row[csf("grouping")]]["id"] =$row[csf("id")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("batch_id")]."*".$row[csf("color_id")]."*".$row[csf("determination_id")]."*".$row[csf("bodypart_id")]."*".$row[csf("fabric_shade")]."*".$row[csf("dia")]."*".$row[csf("width_type")]."*".$row[csf("process_id")]][$row[csf("grouping")]]["roll"] +=$row[csf("roll")];
				$deliveryRemarks[$row[csf("grey_sys_id")]]["remarks"]=$row[csf("remarks")];
			}
			else
			{
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("batch_id")]."*".$row[csf("color_id")]."*".$row[csf("determination_id")]."*".$row[csf("bodypart_id")]."*".$row[csf("fabric_shade")]."*".$row[csf("dia")]."*".$row[csf("width_type")]."*".$row[csf("process_id")]][$row[csf("grouping")]]["current_delivery"] +=$row[csf("current_delivery")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("batch_id")]."*".$row[csf("color_id")]."*".$row[csf("determination_id")]."*".$row[csf("bodypart_id")]."*".$row[csf("fabric_shade")]."*".$row[csf("dia")]."*".$row[csf("width_type")]."*".$row[csf("process_id")]][$row[csf("grouping")]]["id"] =$row[csf("id")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("batch_id")]."*".$row[csf("color_id")]."*".$row[csf("determination_id")]."*".$row[csf("bodypart_id")]."*".$row[csf("fabric_shade")]."*".$row[csf("dia")]."*".$row[csf("width_type")]."*".$row[csf("process_id")]][$row[csf("grouping")]]["roll"] +=$row[csf("roll")];
				$deliveryRemarks[$row[csf("grey_sys_id")]]["remarks"]=$row[csf("remarks")];
			}
		}
	}

	$all_sys_dtls_id=implode(",",array_unique(explode(",",chop($all_sys_dtls_id,","))));
	if($all_sys_dtls_id=="") $all_sys_dtls_id=0;

	/*$fin_pord_sql=sql_select("select a.id as mst_id,b.id, b.grey_used_qty,b.prod_id, b.batch_id, b.remarks,b.color_id,b.fabric_description_id,b.body_part_id,b.fabric_shade,b.dia_width_type, b.process_id from inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form=7 and b.mst_id in($all_sys_dtls_id)");*/
	$fin_pord_sql=sql_select("select a.id as mst_id,b.id, c.grey_used_qty, b.grey_used_qty as dtls_grey_used_qty, b.prod_id, b.remarks,b.color_id,b.fabric_description_id,b.body_part_id,b.fabric_shade,b.dia_width_type, b.process_id, c.po_breakdown_id, d.grouping from inv_receive_master a, pro_finish_fabric_rcv_dtls b left join order_wise_pro_details c on  b.id=c.dtls_id and c.entry_form =7 and c.status_active =1 where a.id=b.mst_id and a.entry_form=7 and b.mst_id in($all_sys_dtls_id)");

	$fin_pord_data=array();
	$$grey_userd_data_arr=array();
	foreach($fin_pord_sql as $row)
	{
		//$grey_userd_data_arr[$row[csf("id")]][$row[csf("po_breakdown_id")]]["grey_used_qty"]=$row[csf("grey_used_qty")];

		if($fin_prod_type==1)
		{
			$grey_userd_data_arr[$row[csf("mst_id")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("fabric_description_id")]][$row[csf("body_part_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]][$row[csf("process_id")]][$row[csf("grouping")]]["grey_used_qty"] += $row[csf("grey_used_qty")];
		}
		else
		{
			$grey_userd_data_arr[$row[csf("mst_id")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("fabric_description_id")]][$row[csf("body_part_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]][$row[csf("process_id")]][$row[csf("grouping")]]["grey_used_qty"] = $row[csf("dtls_grey_used_qty")];
		}

		$fin_pord_data[$row[csf("mst_id")]]["remarks"]=$row[csf("remarks")];
	}

	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$company'","image_location");

	?>
	<div style="width:1200px;">
		<table width="1200" cellspacing="0" align="center" border="0">
			<tr>
				<td colspan="1" rowspan="5">
					<img src="../<? echo $image_location; ?>" height="70" width="200" style="float:left; margin-left:10px;">
				</td>
				<td colspan="15" align="center" style="font-size:x-large"><strong>Working Company: <? echo $working_company_name; ?></strong>
				</td>
			</tr>
			<tr>
				<td colspan="16" align="center" style="font-size:18px"><strong><? echo $com_address; ?></strong></td>
			</tr>
			<tr>
				<td colspan="16" align="center" style="font-size:18px"><strong>LC Company: <? echo $company_name; ?></strong></td>
			</tr>
			<tr>
				<td colspan="16" align="center" style="font-size:18px"><strong>LC Company Location: <? echo $location_arr[$location]; ?></strong></td>
			</tr>
			<tr>
				<td colspan="16" align="center" style="font-size:18px"><strong><u>Delivery Challan, Finishing Section.</u></strong></td>
			</tr>
			<tr>
				<td id="barcode_img_id"></td>
			</tr>
		</table>
		<br>
		<table width="1200" cellspacing="0" align="center" border="0">
			<tr>
				<td   style="font-size:16px; font-weight:bold;" width="110">Challan No</td>
				<td colspan="15">:<? echo $Challan_no; ?></td>
			</tr>
			<tr>
				<td style="font-size:16px; font-weight:bold;" width="110">Delivery Date </td>
				<td colspan="15" >:<? echo $delivery_date; ?></td>
			</tr>
		</table>
	</div>
	<div style="width:1900px">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1900" class="rpt_table" >
			<thead>
				<tr>
					<th width="30" >SL</th>
					<!-- <th width="110">Order No</th> -->
					<!-- <th width="200">Fin. Prod. Company</th> -->
					<th width="40">System ID</th>
                    <th width="110">Internal Ref</th>
                	<th width="150">Buyer <!-- <br> & Job --></th>
                	<th width="50">Batch No</th>
                    <th width="80">Percentage of Color</th>
                	<th width="80">Fabric Shade</th>
                	<th width="100">Booking No</th>
                	<th width="80">Color</th>
					<th width="80">Color Type</th>
					<th width="80">Color Range</th>
					<th width="100">Body Part</th>
					<th width="100">Process Name</th>
					<th width="60">Dia Type</th>
					<th width="200">Fabric Type</th>
					<th width="50">Fin GSM</th>
					<th width="40">Fin. Dia</th>
					<!-- <th width="100">Grey Dia</th> -->
					<th width="40">Total Roll</th>
					<th width="70">Grey Used</th>
					<th width="70">Deli. Qty Finish</th>
				</tr>
			</thead>
			<tbody>
				<?
				if($order_ids!="")
				{
					$sql_machineDia=sql_select("select a.febric_description_id, b.po_breakdown_id, a.machine_dia
						from inv_receive_master p, pro_grey_prod_entry_dtls a, order_wise_pro_details b
						where p.id=a.mst_id and a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and p.status_active=1 and p.is_deleted=0 and b.po_breakdown_id in ($order_ids) and p.entry_form in(2,22) and a.machine_dia is not NULL group by a.febric_description_id, b.po_breakdown_id, a.machine_dia");
				}
				else
				{
					$sql_machineDia=sql_select("select p.booking_no, a.febric_description_id, a.machine_dia
						from inv_receive_master p, pro_grey_prod_entry_dtls a
						where p.id=a.mst_id  and a.status_active=1 and a.is_deleted=0 and p.status_active=1 and p.is_deleted=0 and p.booking_without_order=1 and a.machine_dia is not NULL $all_booking_no_cond_2 group by p.booking_no, a.febric_description_id, a.machine_dia");
				}

				$mc_dia_arr=array();
				foreach($sql_machineDia as $rows)
				{
					if($order_ids!="")
						$mc_dia_arr[$rows[csf('po_breakdown_id')]][$rows[csf('febric_description_id')]].=$rows[csf('machine_dia')].',';
					else
						$mc_dia_arr[$rows[csf('booking_no')]][$rows[csf('febric_description_id')]].=$rows[csf('machine_dia')].',';
				}
				unset($sql_machineDia);

				if($fin_prod_type==1)
				{
					$sql="select
					a.id,
					a.recv_number_prefix_num,
					a.recv_number,
					a.receive_date,
					a.receive_basis,
					a.knitting_source,
					a.knitting_company,
					b.fabric_description_id as determination_id,
					b.fabric_shade,
					b.gsm as gsm,
					b.width as dia,
					b.prod_id,
					b.batch_id as batch_id,
					b.color_id as color_id,
					b.body_part_id,
					b.process_id,
					b.dia_width_type,
					sum(c.quantity) as quantity,
					d.grouping,
					e.job_no,
					e.job_no_prefix_num,
					e.buyer_name,
					-- c.po_breakdown_id,
					sum(d.po_quantity*e.total_set_qnty) as po_qty,
					$select_year
					from
					inv_receive_master a,  pro_finish_fabric_rcv_dtls b ,order_wise_pro_details c,wo_po_break_down d,wo_po_details_master e
					where
					a.id=b.mst_id  and b.id=c.dtls_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and a.entry_form=7 and c.entry_form=7 and c.status_active=1 and c.is_deleted=0 and c.trans_type=1 and a.status_active=1 and a.is_deleted=0 and a.company_id=$company and a.id in ($program_ids) and b.prod_id in ($product_ids) and c.po_breakdown_id in($order_ids) $date_con $location_con
					group by b.batch_id,a.id,a.recv_number_prefix_num,a.recv_number,a.receive_date,a.receive_basis,a.knitting_source,a.knitting_company,b.fabric_description_id,b.fabric_shade,b.gsm,b.width,b.prod_id,b.color_id,b.body_part_id,b.process_id,b.dia_width_type, d.grouping,e.job_no,e.job_no_prefix_num,e.buyer_name,e.insert_date
					order by b.batch_id";
					//b.id as dtls_id,
				}
				else
				{
					$sql="select
					a.id,
					a.recv_number_prefix_num,
					a.recv_number,
					a.receive_date,
					a.receive_basis,
					a.knitting_source,
					a.knitting_company,
					b.fabric_description_id as determination_id,
					b.fabric_shade,
					b.gsm as gsm,
					b.width as dia,
					b.prod_id,
					b.batch_id as batch_id,
					b.color_id as color_id,
					b.body_part_id,
					b.process_id,
					b.dia_width_type,
					sum(b.receive_qnty) as quantity,
					'' as job_no,
					'' as job_no_prefix_num,
					'' as buyer_name,
					'' as po_breakdown_id,
					'' as job_year
					from
					inv_receive_master a,  pro_finish_fabric_rcv_dtls b
					where
					a.id=b.mst_id and a.entry_form=7 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_chack_cond and a.company_id=$company and a.id in ($program_ids) and b.prod_id in ($product_ids) $date_con $location_con
					group by b.batch_id,a.id, a.recv_number_prefix_num,a.recv_number,a.receive_date,a.receive_basis,a.knitting_source,a.knitting_company,b.fabric_description_id,b.fabric_shade,b.gsm,b.width,b.prod_id,b.color_id,b.body_part_id,b.process_id,b.dia_width_type
					order by b.batch_id";
					//b.id as dtls_id,
				}


				$booking_without_order = sql_select("select a.booking_no_prefix_num, a.buyer_id,b.body_part,b.booking_no,b.color_type_id,a.grouping, b.lib_yarn_count_deter_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where  a.booking_no=b.booking_no and a.company_id=$company   and a.booking_type=4 $all_booking_no_cond");
				foreach ($booking_without_order as $row)
				{
					$color_type_array[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('lib_yarn_count_deter_id')]]['color_type_id'] .= $row[csf('color_type_id')].",";
					$internalRef_array[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('lib_yarn_count_deter_id')]]['color_type_id'] .= $row[csf('grouping')].",";
				}

				$color_sqls = "select  b.booking_no,c.color_type_id,c.body_part_id, c.lib_yarn_count_deter_id from wo_booking_mst a, wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.company_id=$company and a.booking_type=1 and b.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $all_booking_no_cond group by b.booking_no,c.color_type_id,c.body_part_id , c.lib_yarn_count_deter_id";
				$color_sql_result = sql_select($color_sqls);
				foreach ($color_sql_result as $keys=>$row2)
				{
					$color_type_array[$row2[csf('booking_no')]][$row2[csf('body_part_id')]][$row2[csf('lib_yarn_count_deter_id')]]['color_type_id'] .= $color_type[$row2[csf('color_type_id')]].",";
				}

				$nameArray=sql_select( $sql); $i=1; $tot_roll=0; $tot_qty=0;
				foreach($nameArray as $row)
				{
					if ($i%2==0)  $bgcolor="#E9F3FF";
					else $bgcolor="#FFFFFF";

					if($fin_prod_type==1)
					{
						//$index_pk=$row[csf("id")]."*".$row[csf("prod_id")]."*".$row[csf("po_breakdown_id")]."*".$row[csf("dtls_id")];

						$index_pk=$row[csf("id")]."*".$row[csf("prod_id")]."*".$row[csf("batch_id")]."*".$row[csf("color_id")]."*".$row[csf("determination_id")]."*".$row[csf("body_part_id")]."*".$row[csf("fabric_shade")]."*".$row[csf("dia")]."*".$row[csf("dia_width_type")]."*".$row[csf("process_id")];
					}
					else
					{
						//$index_pk=$row[csf("id")]."*".$row[csf("prod_id")]."*".$row[csf("dtls_id")];
						$index_pk=$row[csf("id")]."*".$row[csf("prod_id")]."*".$row[csf("batch_id")]."*".$row[csf("color_id")]."*".$row[csf("determination_id")]."*".$row[csf("body_part_id")]."*".$row[csf("fabric_shade")]."*".$row[csf("dia")]."*".$row[csf("dia_width_type")]."*".$row[csf("process_id")];
					}

					$process_all=array_unique(explode(",",$row[csf('process_id')]));
					$process_name='';
					$process_id_array=explode(",",$batch_process);
					foreach($process_all as $val)
					{
						if($process_name=="") $process_name=$conversion_cost_head_array[$val]; else $process_name.=",".$conversion_cost_head_array[$val];
					}
					$supplier_brand_value="";$yarn_count_value="";

					if($fin_prod_type==1)
					{
						$y_count=array_unique(explode(',',chop($knit_production_data[$batch_details[$row[csf("batch_id")]]["prod_id"]][$row[csf('po_breakdown_id')]]['yarn_count'],",")));
						foreach($y_count as $count_id)
						{
							if($count_id>0)
							{
								if($yarn_count_value=='') $yarn_count_value=$yarncount[$count_id]; else $yarn_count_value.=", ".$yarncount[$count_id];
							}
						}

						$supplier_brand_arr=array_unique(explode(',',chop($knit_production_data[$batch_details[$row[csf("batch_id")]]["prod_id"]][$row[csf('po_breakdown_id')]]['brand_id'],",")));
						foreach($supplier_brand_arr as $brand_id)
						{
							if($brand_id>0)
							{
								if($supplier_brand_value=='') $supplier_brand_value=$supplier_brand[$brand_id]; else $supplier_brand_value.=", ".$supplier_brand[$brand_id];
							}
						}

					}
					else
					{
						$supplier_brand=array_unique(explode(',',chop($knit_production_data[$batch_details[$row[csf("batch_id")]]["booking_no"]][$batch_details[$row[csf("batch_id")]]["brand_id"]]['yarn_count'],",")));
						foreach($y_count as $count_id)
						{
							if($count_id>0)
							{
								if($yarn_count_value=='') $yarn_count_value=$yarncount[$count_id]; else $yarn_count_value.=", ".$yarncount[$count_id];
							}
						}

						$supplier_brand=array_unique(explode(',',chop($knit_production_data[$batch_details[$row[csf("batch_id")]]["booking_no"]][$batch_details[$row[csf("batch_id")]]["brand_id"]]['brand_id'],",")));

						foreach($supplier_brand as $brand_id)
						{
							if($brand_id>0)
							{
								if($supplier_brand_value=='') $supplier_brand_value=$supplier_brand[$brand_id]; else $supplier_brand_value.=", ".$supplier_brand[$brand_id];
							}
						}
					}
					$ms_dia="";
					if($row[csf('po_breakdown_id')]!="") $ms_dia=$mc_dia_arr[$row[csf('po_breakdown_id')]][$row[csf('determination_id')]];
					else $ms_dia=$mc_dia_arr[$batch_details[$row[csf("batch_id")]]['booking_no']][$row[csf('determination_id')]];

					$all_machineDias=chop($ms_dia,',');
					$all_machineDias=implode(",",array_unique(explode(",",$all_machineDias)));

					if($update_row_check[$index_pk][$row[csf("grouping")]]["current_delivery"]>0)
					{
						$internal_ref_no=$row[csf('grouping')];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">

							<td width="30"  align="center"><div style="word-wrap:break-word; width:30px"><? echo $i; ?></div></td>

							<td width="40" align="center"><div style="word-wrap:break-word; width:40px"><? echo $row[csf('recv_number_prefix_num')]; ?></div></td>
                            <td width="110"><div style="word-wrap:break-word; width:110px; text-align:center">
                            	<?
                            	if($batch_details[$row[csf('batch_id')]]['booking_without_order']==1)
								{
                            		$internalRef_no =  $internalRef_array[$batch_details[$row[csf("batch_id")]]['booking_no']][$row[csf("body_part_id")]][$row[csf("determination_id")]]['color_type_id'];
                            		echo implode(array_unique(explode(",",chop($internalRef_no,","))));
								}
								else
								{
									echo $internal_ref_no;
								}
                            	?>&nbsp;</div></td>
                            <td width="150"><div style="word-wrap:break-word; width:150px">
								<?
								if($batch_details[$row[csf('batch_id')]]['booking_without_order']==1)
								{
									echo $buyer_array[$non_order_buyer[$batch_details[$row[csf("batch_id")]]['booking_no_id']]];
								}
								else
								{
									echo $buyer_array[$row[csf("buyer_name")]];
								}

								?></div></td>
								<td width="50" align="center"><div style="word-wrap:break-word; width:50px"><? echo $batch_details[$row[csf('batch_id')]]['batch_no'];?></div></td>
                                <td width="80" align="center"><?   echo $percentageOfColor; ?></td>
								<td width="80" align="center"><div style="word-wrap:break-word; width:80px"><? echo $fabric_shade[$row[csf('fabric_shade')]]; ?></div></td>
								<td align="center" width="100"><div style="word-wrap:break-word; width:100px"><? echo $batch_details[$row[csf("batch_id")]]['booking_no']; ?></div></td>
								<td width="80"><div style="word-wrap:break-word; width:80px"><? echo $color_arr[$row[csf('color_id')]];?></div></td>

								<td width="80">
									<div style="word-wrap:break-word; width:80px">
										<?
										$color_type_id=$color_type_array[$batch_details[$row[csf("batch_id")]]['booking_no']][$row[csf("body_part_id")]][$row[csf("determination_id")]]['color_type_id'];
								 		echo implode(",",array_unique(explode(",",chop($color_type_id,","))));
								 		?>
									</div>
								</td>
								<td width="80">
									<div style="word-wrap:break-word; width:80px"><? echo $color_range[$batch_details[$row[csf("batch_id")]]['color_range_id']]; ?></div>
								</td>

								<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $body_part[$row[csf("body_part_id")]];?></div></td>

								<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $process_name;?></div></td>
								<td width="60"><div style="word-wrap:break-word; width:60px"><? echo $fabric_typee[$row[csf('dia_width_type')]];?></div></td>
								<td width="200"><div style="word-wrap:break-word; width:200px">
									<?
									echo $composition_arr[$row[csf('determination_id')]]."<br>";
									?></div></td>
								<td width="50" align="center"><div style="word-wrap:break-word; width:50px"><?  echo $row[csf('gsm')]; ?></div></td>
								<td width="40" align="center"><div style="word-wrap:break-word; width:40px"><? echo $row[csf('dia')];?></div></td>

								<td width="40" align="center"><div style="word-wrap:break-word; width:40px"><? echo $update_row_check[$index_pk][$row[csf("grouping")]]["roll"]; $tot_roll+=$update_row_check[$index_pk][$row[csf("grouping")]]["roll"]; ?></div></td>
								<td  align="right" width="70">
									<div style="word-wrap:break-word; width:70px">
										<?
										echo number_format($grey_userd_data_arr[$row[csf("id")]][$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("color_id")]][$row[csf("determination_id")]][$row[csf("body_part_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]][$row[csf("process_id")]][$row[csf("grouping")]]["grey_used_qty"],2);
										$tot_grey_used+=$grey_userd_data_arr[$row[csf("id")]][$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("color_id")]][$row[csf("determination_id")]][$row[csf("body_part_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]][$row[csf("process_id")]][$row[csf("grouping")]]["grey_used_qty"];
										?>
									</div>
								</td>
								<td  align="right" width="70"><div style="word-wrap:break-word; width:70px"><? echo number_format($update_row_check[$index_pk][$row[csf("grouping")]]["current_delivery"],2); $tot_qty+=$update_row_check[$index_pk][$row[csf("grouping")]]["current_delivery"]; ?></div></td>
							</tr>
								<?
								$i++;
							}
					$batch_dia_type="";
				}
						?>
						<tr>
							<td align="right" colspan="17" ><strong>Total:</strong></td>
							<td align="center"><? echo $tot_roll; ?></td>
							<td align="right" ><? echo number_format($tot_grey_used,2,'.',''); ?></td>
							<td align="right" ><? echo number_format($tot_qty,2,'.',''); ?></td>
						</tr>
						<tr>
							<td colspan="20" align="left"><div style="word-wrap:break-word; font-weight: bold;">Remarks: <? echo $deliveryRemarks[$row[csf("id")]]["remarks"]; ?> </div></td>
						</tr>
                        </tbody>
		</table>
        <br>
        <?
			echo signature_table(68, $company, "1820px",$cbo_template_id);
        ?>

        <script type="text/javascript" src="../includes/functions.js"></script>
        <script type="text/javascript" src="../js/jquery.js"></script>
        <script type="text/javascript" src="../js/jquerybarcode.js"></script>
        <script>
        fnc_generate_Barcode("<? echo $Challan_no;?>","barcode_img_id");
        </script>
        </div>
        <?
        exit();
}
if($action=="delivery_challan_print_3") // new report 3 is sale NO
{
	extract($_REQUEST);
	//echo '<pre>';print_r($_REQUEST);

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	//echo "DFFFFFFFFFFFF";die;
	$datas=explode('_',$data);
	$program_ids = implode(",",array_unique(explode(",",str_replace("'","",$datas[0]))));
	$company = str_replace("'","",$datas[1]);
	$from_date = str_replace("'","",$datas[2]);
	$to_date = str_replace("'","",$datas[3]);
	$product_ids = str_replace("'","",$datas[4]);
	//echo $product_ids;die;
	$order_ids = implode(",",array_unique(explode(",",str_replace("'","",$datas[5]))));
	$location= str_replace("'","",$datas[6]);
	$buyer = str_replace("'","",$datas[7]);
	$update_mst_id = str_replace("'","",$datas[8]);
	$delivery_date = str_replace("'","",$datas[9]);
	$Challan_no = str_replace("'","",$datas[10]);
	$fin_prod_type = str_replace("'","",$datas[11]);
	//echo $fin_prod_type;die;
	$batch_ids = implode(",",array_unique(explode(",",str_replace("'","",$datas[12]))));
	$operation = str_replace("'","",$datas[13]);
	$txt_remarks = str_replace("'","",$datas[14]);
	$dyeing_company = str_replace("'","",$datas[15]);
	$cbo_template_id = str_replace("'","",$datas[16]);

	$delivery_company = str_replace("'","",$datas[17]);
	$delivery_location = str_replace("'","",$datas[18]);
	$delivery_location = str_replace("'","",$datas[19]);


	if($order_ids==""){$order_id_print_cond="";}else{$order_id_print_cond=" and a.order_id in ($order_ids)";}

	if($db_type==0)
	{
		if($from_date!="" && $to_date!="") $date_con="and a.delevery_date between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'"; else $date_con="";
		if($from_date!="" && $to_date!="") $date_con_production="and a.receive_date between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'"; else $date_con_production="";
		$select_year="year(e.insert_date) as job_year";
	}
	else if($db_type==2)
	{
		if($from_date!="" && $to_date!="") $date_con="and a.delevery_date between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'"; else $date_con="";
		if($from_date!="" && $to_date!="") $date_con_production="and a.receive_date between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'"; else $date_con_production="";
		$select_year="to_char(e.insert_date,'YYYY') as job_year";
	}

	if($location!=0) $location_con="and a.location_id=$location"; else $location_con="";
	if($buyer!=0) $buyer_con="and a.buyer_id=$buyer"; else $buyer_con="";

	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$supplier_arr=return_library_array( "select id, supplier_name from  lib_supplier",'id','supplier_name');
	$yarncount=return_library_array( "select id, yarn_count from  lib_yarn_count",'id','yarn_count');
	$supplier_brand=return_library_array("select id,brand_name from lib_brand", "id","brand_name");
	$floor_arr = return_library_array("select id,floor_name from lib_prod_floor  where status_active =1 and is_deleted=0", 'id', 'floor_name');

	$sql_forWorkingComp=sql_select("select a.knitting_source,a.knitting_company from inv_receive_master a,  pro_finish_fabric_rcv_dtls b
	where a.id=b.mst_id and a.entry_form=7 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_chack_cond and a.company_id=$company and a.id in ($program_ids) and b.prod_id in ($product_ids) $date_con_production $location_con
	order by b.batch_id");

	foreach($sql_forWorkingComp as $row)
	{
		if($row[csf("knitting_source")]==1)
		{
			$comID=$row[csf("knitting_company")];
			$knitting_source=$row[csf("knitting_source")];
		}
		else if($row[csf("knitting_source")]==3)
		{
			$comID=$row[csf("knitting_company")];
			$knitting_source=$row[csf("knitting_source")];

		}
	}

	if ($knitting_source==1) {
		$com_sql_working=sql_select("select id, company_name, plot_no, level_no, road_no, block_no, city, zip_code from lib_company where id=$comID");
		$com_address="";
		foreach($com_sql_working as $row)
		{
			$working_company_name=$row[csf("company_name")];
			if($row[csf("plot_no")]!="") $com_address.=$row[csf("plot_no")].", ";
			if($row[csf("level_no")]!="") $com_address.=$row[csf("level_no")].", ";
			if($row[csf("road_no")]!="") $com_address.=$row[csf("road_no")].", ";
			if($row[csf("block_no")]!="") $com_address.=$row[csf("block_no")].", ";
			if($row[csf("city")]!="") $com_address.=$row[csf("city")].", ";
			if($row[csf("zip_code")]!="") $com_address.=$row[csf("zip_code")].", ";
		}
		$com_address=chop($com_address," , ");
	}
	else if ($knitting_source==3) {
		$com_sql_working=sql_select("select id, supplier_name as company_name, address_1 from lib_supplier where id=$comID");
		$com_address="";
		foreach($com_sql_working as $row)
		{
			$working_company_name=$row[csf("company_name")];
			if($row[csf("address_1")]!="") $com_address.=$row[csf("address_1")].", ";
		}
		$com_address=chop($com_address," , ");
	}


	$com_sql=sql_select("select id, company_name, plot_no, level_no, road_no, block_no, city, zip_code from lib_company");
	foreach($com_sql as $row)
	{
		$company_arr[$row[csf("id")]]=$row[csf("company_name")];
	}

	$profloor_sql=sql_select("SELECT a.id,c.floor_id from pro_batch_create_mst a, pro_batch_create_dtls b,pro_fab_subprocess c where a.id=b.mst_id and c.batch_id=a.id and c.batch_id=b.mst_id and c.entry_form=35 and c.load_unload_id=2 and  a.id in($batch_ids)  group by a.id,c.floor_id ");
	$profloor_details=array(); //
	foreach($profloor_sql as $row)
	{
		$profloor_details[$row[csf("id")]]["floor_id"]=$row[csf("floor_id")];
	}

	$batch_sql=sql_select("SELECT a.id, a.batch_no, a.booking_no_id, a.color_range_id, a.booking_no, a.booking_without_order,a.floor_id, b.prod_id, b.batch_qnty,b.body_part_id from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.id in($batch_ids) group by a.id, a.batch_no, a.color_range_id, a.booking_no_id, a.booking_no, a.booking_without_order,a.floor_id, b.prod_id, b.batch_qnty,b.body_part_id ");
	$batch_details=array();
	foreach($batch_sql as $row)
	{
		$batch_details[$row[csf("id")]]["batch_no"]=$row[csf("batch_no")];
		$batch_details[$row[csf("id")]]["floor_id"]=$row[csf("floor_id")];
		$batch_details[$row[csf("id")]]["booking_no"]=$row[csf("booking_no")];
		$batch_details[$row[csf("id")]]["color_range_id"]=$row[csf("color_range_id")];
		$batch_details[$row[csf("id")]]["booking_no_id"]=$row[csf("booking_no_id")];
		$batch_details[$row[csf("id")]]["booking_without_order"]=$row[csf("booking_without_order")];
		$batch_details[$row[csf("id")]]["prod_id"]=$row[csf("prod_id")];
		$batch_details[$row[csf("id")]]["batch_qnty"]+=$row[csf("batch_qnty")];
		$batch_details[$row[csf("id")]]["body_part_id"]=$row[csf("body_part_id")];

		if($row[csf("booking_without_order")]==1)
		{
			$all_samp_booking_no_arr[$row[csf("booking_no")]] = "'".$row[csf("booking_no")]."'";
		}
		else{
			$all_booking_no_arr[$row[csf("booking_no")]] = "'".$row[csf("booking_no")]."'";
		}
		
	}

	$all_samp_booking_no_arr = array_filter($all_samp_booking_no_arr);
	$all_booking_no_arr = array_filter($all_booking_no_arr);
	if(!empty($all_booking_no_arr))
	{
		$all_booking_nos = implode(",", $all_booking_no_arr);
		$all_booking_no_cond=""; $bookCond="";
		if($db_type==2 && count($all_booking_no_arr)>999)
		{
			$all_booking_no_arr_chunk=array_chunk($all_booking_no_arr,999) ;
			foreach($all_booking_no_arr_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$bookCond.="  a.booking_no in($chunk_arr_value) or ";
			}
	
			$all_booking_no_cond.=" and (".chop($bookCond,'or ').")";
		}
		else
		{
			$all_booking_no_cond=" and a.booking_no in($all_booking_nos)";
		}
	}

	if(!empty($all_samp_booking_no_arr))
	{
		$all_samp_booking_nos = implode(",", $all_samp_booking_no_arr);
		$all_samp_booking_no_cond=""; $sbookCond="";
		if($db_type==2 && count($all_samp_booking_no_arr)>999)
		{
			$all_samp_booking_no_arr_chunk=array_chunk($all_samp_booking_no_arr,999) ;
			foreach($all_samp_booking_no_arr_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$sbookCond.="  a.booking_no in($chunk_arr_value) or ";
			}
	
			$all_samp_booking_no_cond.=" and (".chop($sbookCond,'or ').")";
		}
		else
		{
			$all_samp_booking_no_cond=" and a.booking_no in($all_samp_booking_nos)";
		}
	}

	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0";
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
	$update_row_check=array(); $deliveryRemarks=array();
	if($update_mst_id!="")
	{
		//wo_po_details_master
		$sql_update=sql_select("select b.id, b.mst_id, b.grey_sys_id,b.product_id,b.job_no, b.order_id as  order_id, d.grouping, b.current_delivery,b.roll,b.program_no,b.sys_dtls_id,b.color_id,b.determination_id,b.bodypart_id,b.fabric_shade,b.dia,b.width_type,a.remarks,c.process_id, c.batch_id
		from pro_grey_prod_delivery_mst a,pro_grey_prod_delivery_dtls b left join wo_po_break_down d on b.order_id = d.id, pro_finish_fabric_rcv_dtls c
		where a.id=b.mst_id and b.mst_id=$update_mst_id and b.sys_dtls_id = c.id and c.status_active=1 and c.is_deleted=0 and b.entry_form=54 and a.status_active=1 and a.is_deleted=0");

		$all_sys_dtls_id="";
		foreach($sql_update as $row)
		{
			//$all_sys_dtls_id.=$row[csf("sys_dtls_id")].",";
			$po_arr[$row[csf("order_id")]]["ref_no"]=$row[csf("grouping")];
			$po_arr[$row[csf("order_id")]]["ref_no"]=$row[csf("grouping")];
			$all_sys_dtls_id.=$row[csf("grey_sys_id")].",";
			if($fin_prod_type==1)
			{
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("batch_id")]."*".$row[csf("color_id")]."*".$row[csf("determination_id")]."*".$row[csf("bodypart_id")]."*".$row[csf("fabric_shade")]."*".$row[csf("dia")]."*".$row[csf("width_type")]."*".$row[csf("process_id")]][$row[csf("grouping")]]["current_delivery"] +=$row[csf("current_delivery")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("batch_id")]."*".$row[csf("color_id")]."*".$row[csf("determination_id")]."*".$row[csf("bodypart_id")]."*".$row[csf("fabric_shade")]."*".$row[csf("dia")]."*".$row[csf("width_type")]."*".$row[csf("process_id")]][$row[csf("grouping")]]["id"] =$row[csf("id")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("batch_id")]."*".$row[csf("color_id")]."*".$row[csf("determination_id")]."*".$row[csf("bodypart_id")]."*".$row[csf("fabric_shade")]."*".$row[csf("dia")]."*".$row[csf("width_type")]."*".$row[csf("process_id")]][$row[csf("grouping")]]["roll"] +=$row[csf("roll")];
				$deliveryRemarks[$row[csf("mst_id")]]["remarks"]=$row[csf("remarks")];
			}
			else
			{
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("batch_id")]."*".$row[csf("color_id")]."*".$row[csf("determination_id")]."*".$row[csf("bodypart_id")]."*".$row[csf("fabric_shade")]."*".$row[csf("dia")]."*".$row[csf("width_type")]."*".$row[csf("process_id")]][$row[csf("grouping")]]["current_delivery"] +=$row[csf("current_delivery")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("batch_id")]."*".$row[csf("color_id")]."*".$row[csf("determination_id")]."*".$row[csf("bodypart_id")]."*".$row[csf("fabric_shade")]."*".$row[csf("dia")]."*".$row[csf("width_type")]."*".$row[csf("process_id")]][$row[csf("grouping")]]["id"] =$row[csf("id")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("batch_id")]."*".$row[csf("color_id")]."*".$row[csf("determination_id")]."*".$row[csf("bodypart_id")]."*".$row[csf("fabric_shade")]."*".$row[csf("dia")]."*".$row[csf("width_type")]."*".$row[csf("process_id")]][$row[csf("grouping")]]["roll"] +=$row[csf("roll")];
				$deliveryRemarks[$row[csf("mst_id")]]["remarks"]=$row[csf("remarks")];
			}
		}
	}

	$all_sys_dtls_id=implode(",",array_unique(explode(",",chop($all_sys_dtls_id,","))));
	if($all_sys_dtls_id=="") $all_sys_dtls_id=0;

	$fin_pord_sql=sql_select("SELECT a.id as mst_id,a.recv_number_prefix_num,b.id, b.grey_used_qty as dtls_grey_used_qty, b.prod_id, b.remarks,b.color_id,b.fabric_description_id,b.body_part_id,b.fabric_shade,b.dia_width_type, b.process_id,b.batch_id from inv_receive_master a, pro_finish_fabric_rcv_dtls b  where a.id=b.mst_id and a.entry_form=7 and b.mst_id in($all_sys_dtls_id)");//, d.grouping

	$fin_pord_data=array();
	$grey_userd_data_arr=array();
	foreach($fin_pord_sql as $row)
	{
		$grey_userd_data_arr[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("fabric_shade")]][$row[csf("color_id")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]]["grey_used_qty"]+=$row[csf("dtls_grey_used_qty")];
		$grey_userd_data_arr[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("fabric_shade")]][$row[csf("color_id")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]]["process_id"] .=$row[csf("process_id")].",";
		$grey_userd_data_arr[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("fabric_shade")]][$row[csf("color_id")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]]["recv_number_prefix_num"] .=$row[csf("recv_number_prefix_num")].",";

		$fin_pord_data[$row[csf("mst_id")]]["remarks"]=$row[csf("remarks")];

		$grey_used_arr[$row[csf('id')]]['grey_used_quantity']+=$row[csf('dtls_grey_used_qty')];
	}
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$company'","image_location");

	?>
	<div style="width:1200px;">
		<table width="1200" cellspacing="0" align="center" border="0">
			<tr>
				<td colspan="1" rowspan="5">
					<img src="../<? echo $image_location; ?>" height="70" width="200" style="float:left; margin-left:10px;">
				</td>
				<td colspan="15" align="center" style="font-size:x-large"><strong>Working Company: <? echo $working_company_name; ?></strong>
				</td>
			</tr>
			<tr>
				<td colspan="16" align="center" style="font-size:18px"><strong><? echo $com_address; ?></strong></td>
			</tr>
			<tr>
				<td colspan="16" align="center" style="font-size:18px"><strong>LC Company: <? echo $company_arr[$company]; ?></strong></td>
			</tr>
			<tr>
				<td colspan="16" align="center" style="font-size:18px"><strong>LC Company Location: <? echo $location_arr[$location]; ?></strong></td>
			</tr>
			<tr>
				<td colspan="16" align="center" style="font-size:18px"><strong><u>Delivery Challan, Finishing Section.</u></strong></td>
			</tr>
			<tr>
				<td id="barcode_img_id"></td>
			</tr>
		</table>
		<br>
		<table width="1200" cellspacing="0" align="center" border="0">
			<tr>
				<td   style="font-size:16px; font-weight:bold;" width="110">Challan No</td>
				<td colspan="15">:<? echo $Challan_no; ?></td>
				<td style="font-size:16px; font-weight:bold;" width="150">Delivery Company</td>
            <td colspan="13"><? echo $company_arr[$delivery_company];?></td>
			</tr>
			<tr>
				<td style="font-size:16px; font-weight:bold;" width="110">Delivery Date </td>
				<td colspan="15" >:<? echo $delivery_date; ?></td>
				<td style="font-size:16px; font-weight:bold;" width="150">Delivery Location</td>
            	<td colspan="13"><? echo $location_arr[$delivery_location];?></td>
			</tr>
		</table>
	</div>
	<div style="width:2030px">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2030" class="rpt_table" >
			<thead>
				<tr>
					<th width="30" >SL</th>
					<!-- <th width="110">Order No</th> -->
					<!-- <th width="200">Fin. Prod. Company</th> -->
					<th width="40">System ID</th>
                    <th width="110">Internal Ref</th>
                	<th width="150">Buyer <!-- <br> & Job --></th>
                	<th width="90">Batch No</th>
                	<th width="90">Dyeing Production Floor</th>
                    <th width="80" title="(total dyes ratio/total batch ratio)*100">Percentage of Dyes</th>
                	<th width="80">Fabric Shade</th>
                	<th width="100">Booking No</th>
                	<th width="80">Color</th>
					<th width="80">Color Type</th>
					<th width="80">Color Range</th>
					<th width="100">Body Part</th>
					<th width="100">Process Name</th>
					<th width="60">Dia Type</th>
					<th width="200">Fabric Type</th>
					<th width="50">Fin GSM</th>
					<th width="40">Fin. Dia</th>
					<!-- <th width="100">Grey Dia</th> -->
					<th width="40">Total Roll</th>
					<th width="70">Grey Used</th>
					<th width="70">Deli. Qty Finish</th>
				</tr>
			</thead>
			<tbody>
				<?
				
				$sql_color_precentage="SELECT a.id, a.batch_id, b.id as dtls_id, b.prod_id, b.sub_process_id, b.dose_base as item_cat, b.ratio, c.item_category_id
				from pro_recipe_entry_mst a, pro_recipe_entry_dtls b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and a.working_company_id=$dyeing_company and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.ratio is not null 
				and b.seq_no is not null and a.batch_id in ($batch_ids)";
				$result_color_precentage=sql_select($sql_color_precentage);
				$batch_item_ratio_total_arr=array();
				$batchTotal_arr= array();
				foreach($result_color_precentage as $row){
				$batch_item_ratio_total_arr[$row[csf('batch_id')]][$product_data_arr[$row[csf('prod_id')]]]['ratio_total'] += $row[csf('ratio')];
				$batch_item_ratio_total_arr[$row[csf('batch_id')]][$product_data_arr[$row[csf('prod_id')]]]['ratio_count'] += 1;
				$batchTotal_arr[$row[csf('batch_id')]]['batch_ratio_total']  += $row[csf('ratio')];

				}
				unset($result_color_precentage);

			    $sql="SELECT a.id,a.sys_number_prefix_num, a.sys_number,a.delevery_date as receive_date, b.grey_sys_number as grey_sys_no, b.determination_id as deter_id, b.fabric_shade, b.gsm as gsm,b.dia as dia,b.product_id as prod_id,b.roll,b.batch_id as batch_id,b.color_id as color_id,b.bodypart_id as body_part,null as  process_id,b.width_type as dia_type, (b.current_delivery) as current_delivery, b.order_id, b.sys_dtls_id, c.booking_no, c.booking_without_order,c.color_range_id ,b.grey_used_qnty
				from pro_grey_prod_delivery_mst a,  pro_grey_prod_delivery_dtls b, pro_batch_create_mst c
				where a.id=b.mst_id  and c.id=b.batch_id and a.entry_form=54 and b.current_delivery>0  and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.company_id=$company  and a.id=".$update_mst_id." $date_con $location_con
				order by b.batch_id";
				//echo $sql;
				$nameArray=sql_select( $sql);
				$all_sys_dtls_id='';
				foreach ($nameArray as $row)
				{
					$fin_del_arr[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$row[csf('color_id')]][$row[csf('body_part')]][$row[csf('dia_type')]]['grey_sys_no']=$row[csf('grey_sys_no')];
					$fin_del_arr[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$row[csf('color_id')]][$row[csf('body_part')]][$row[csf('dia_type')]]['gsm']=$row[csf('gsm')];
					$fin_del_arr[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$row[csf('color_id')]][$row[csf('body_part')]][$row[csf('dia_type')]]['deter_id']=$row[csf('deter_id')];
					$fin_del_arr[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$row[csf('color_id')]][$row[csf('body_part')]][$row[csf('dia_type')]]['dia']=$row[csf('dia')];
					$fin_del_arr[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$row[csf('color_id')]][$row[csf('body_part')]][$row[csf('dia_type')]]['order_id'].=$row[csf('order_id')].',';
					$fin_del_arr[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$row[csf('color_id')]][$row[csf('body_part')]][$row[csf('dia_type')]]['current_delivery']+=$row[csf('current_delivery')];
					$fin_del_arr[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$row[csf('color_id')]][$row[csf('body_part')]][$row[csf('dia_type')]]['id']=$row[csf('id')];
					$fin_del_arr[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$row[csf('color_id')]][$row[csf('body_part')]][$row[csf('dia_type')]]['color_range_id']=$row[csf('color_range_id')];
					$fin_del_arr[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$row[csf('color_id')]][$row[csf('body_part')]][$row[csf('dia_type')]]['sys_dtls_id'].=$row[csf('sys_dtls_id')].",";
					$fin_del_arr[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$row[csf('color_id')]][$row[csf('body_part')]][$row[csf('dia_type')]]['roll']+=$row[csf('roll')];
					$fin_del_arr[$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('fabric_shade')]][$row[csf('color_id')]][$row[csf('body_part')]][$row[csf('dia_type')]]['grey_used_qnty']+=$row[csf('grey_used_qnty')];

					if($all_sys_dtls_id=="") $all_sys_dtls_id=$row[csf('sys_dtls_id')]; else $all_sys_dtls_id.=",".$row[csf('sys_dtls_id')];
				}

				if(!empty($all_samp_booking_no_arr))
				{
					$booking_without_order = sql_select("select a.id, a.booking_no_prefix_num, a.buyer_id,b.body_part,b.booking_no,b.color_type_id,a.grouping, b.lib_yarn_count_deter_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where  a.booking_no=b.booking_no and a.company_id=$company and a.booking_type=4 $all_samp_booking_no_cond");
					foreach ($booking_without_order as $row)
					{
						$color_type_array[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('lib_yarn_count_deter_id')]]['color_type_id'] .= $color_type[$row[csf('color_type_id')]].",";
						$internalRef_array[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('lib_yarn_count_deter_id')]]['color_type_id'] .= $row[csf('grouping')].",";
						$non_order_buyer[$row[csf('id')]]=$row[csf('buyer_id')];
					}
				}
				
				if(!empty($all_booking_no_arr))
				{
					$color_sqls = "select  a.buyer_id,b.booking_no,c.color_type_id,c.body_part_id, c.lib_yarn_count_deter_id from wo_booking_mst a, wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.company_id=$company and a.booking_type in(1,4) and b.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $all_booking_no_cond group by a.buyer_id,b.booking_no,c.color_type_id,c.body_part_id , c.lib_yarn_count_deter_id";
					$color_sql_result = sql_select($color_sqls);
					foreach ($color_sql_result as $keys=>$row2)
					{
						$color_type_array[$row2[csf('booking_no')]][$row2[csf('body_part_id')]][$row2[csf('lib_yarn_count_deter_id')]]['color_type_id'] .= $color_type[$row2[csf('color_type_id')]].",";
						$po_buyer_arr[$row2[csf('booking_no')]]['buyer_id']=$row2[csf('buyer_id')];
					}
				}

				$i=1; $tot_roll=0; $tot_qty=0;
				foreach($fin_del_arr as $prod_id=>$prod_data)
				{
					foreach($prod_data as $batch_id=>$batch_data)
					{
						foreach($batch_data as $shade_id=>$shade_data)
						{
							foreach($shade_data as $color_id=>$color_data)
							{
								foreach($color_data as $body_id=>$body_data)
								{
									foreach($body_data as $dia_type=>$row)
									{
										$sys_dtls_id=chop($row['sys_dtls_id'],',');
										$sys_dtls_idArr=array_unique(explode(",",$sys_dtls_id));
										$greyUsedQty ="";
									    foreach($sys_dtls_idArr as $key => $values)
									    {
									        if ($greyUsedQty=="")
									        {
									            $greyUsedQty.= $grey_used_arr[$values]['grey_used_quantity'];
									        }
									        else
									        {
									            $greyUsedQty.= ','.$grey_used_arr[$values]['grey_used_quantity'];
									        }
									    }
									    // echo $greyUsedQty;
									    $greyUsed_Qty = explode(',',$greyUsedQty);
										$grey_qty_used = array_sum($greyUsed_Qty);
									    // echo '<br><br>';

										if ($i%2==0)  $bgcolor="#E9F3FF";
										else $bgcolor="#FFFFFF";
										$process_id=$grey_userd_data_arr[$prod_id][$batch_id][$shade_id][$color_id][$body_id][$dia_type]["process_id"];
										$recv_number_prefix_num=$grey_userd_data_arr[$prod_id][$batch_id][$shade_id][$color_id][$body_id][$dia_type]["recv_number_prefix_num"];

										$recv_number_prefix_num=implode(",",array_unique(explode(",",chop($recv_number_prefix_num,","))));

										$process_all=array_unique(explode(",",chop($process_id,",")));
										$process_name='';
										$process_id_array=explode(",",$batch_process);
										foreach($process_all as $val)
										{
											if($process_name=="") $process_name=$conversion_cost_head_array[$val]; else $process_name.=",".$conversion_cost_head_array[$val];
										}
										$ms_dia="";
										$order_id=rtrim($row[('order_id')],',');
										$order_ids=array_unique(explode(",",$order_id));
										$ref_nos="";
										foreach($order_ids as $bid)
										{
											if($ref_nos=="") $ref_nos=$po_arr[$bid]["ref_no"];else $ref_nos.=",".$po_arr[$bid]["ref_no"];
										}

										$BatchTotal 				= $batchTotal_arr[$batch_id]['batch_ratio_total'];
										$chemicalsTotal 			= $batch_item_ratio_total_arr[$batch_id][5]['ratio_total'];
										$dyesTotal 					= $batch_item_ratio_total_arr[$batch_id][6]['ratio_total'];
										$auxiChemicalsTotal			= $batch_item_ratio_total_arr[$batch_id][7]['ratio_total'];

										$percentageOfColor = "";
										($dyesTotal>0 && $BatchTotal>0 )? $percentageOfColor .= "Dyes:". ($dyesTotal/$BatchTotal)*100 ."% " : $percentageOfColor .= "" ;

										//echo $index_pk."=".$update_row_check[$index_pk][$row[csf("grouping")]]["current_delivery"]."<br>";
										//if($update_row_check[$index_pk][$row[csf("grouping")]]["current_delivery"]>0)
										//{
											$internal_ref_no=$ref_nos;
										?>
										<tr bgcolor="<? echo $bgcolor; ?>">

											<td width="30"  align="center"><div style="word-wrap:break-word; width:30px"><? echo $i; ?></div></td>

											<td width="40" align="center"><div style="word-wrap:break-word; width:40px"><? echo $recv_number_prefix_num; ?></div></td>
				                            <td width="110">
				                            	<div style="word-wrap:break-word; width:110px; text-align:center">
					                            	<?
					                            	if($batch_details[$batch_id]['booking_without_order']==1)
													{
					                            		$internalRef_no =  $internalRef_array[$batch_details[$batch_id]['booking_no']][$body_id][$row[("deter_id")]]['color_type_id'];
					                            		// echo implode(",",array_unique(explode(",",$internalRef_no)));
					                            		//echo implode(array_unique(explode(",",chop($internalRef_no,","))));


														$booking=$batch_details[$batch_id]['booking_no'];

															echo $int_ref=return_field_value("grouping", "wo_non_ord_samp_booking_mst", "BOOKING_NO='$booking' and is_deleted=0 and status_active=1");	
														
													}
													else
													{
														echo implode(",",array_unique(explode(",",$internal_ref_no)));
														//echo $internal_ref_no;
													}
					                            	?>&nbsp;
				                            	</div>
				                            </td>
				                            <td width="150"><div style="word-wrap:break-word; width:150px">
												<?
												if($batch_details[$batch_id]['booking_without_order']==1)
												{
													echo $buyer_array[$non_order_buyer[$batch_details[$batch_id]['booking_no_id']]];
												}
												else
												{
													echo $buyer_array[$po_buyer_arr[$batch_details[$batch_id]['booking_no']]['buyer_id']];
												}

												?></div>
											</td>
											<td width="90" align="center"><div style="word-wrap:break-word; width:90px"><? echo $batch_details[$batch_id]['batch_no'];?></div></td>
											<td width="90" align="center"><div style="word-wrap:break-word; width:90px"><? echo $floor_arr[$profloor_details[$batch_id]['floor_id']];?></div></td>
			                                <td width="80" align="center"><?   echo number_format($percentageOfColor,2); ?></td>
											<td width="80" align="center"><div style="word-wrap:break-word; width:80px"><? echo $fabric_shade[$shade_id]; ?></div></td>
											<td align="center" width="100"><div style="word-wrap:break-word; width:100px"><? echo $batch_details[$batch_id]['booking_no']; ?></div></td>
											<td width="80"><div style="word-wrap:break-word; width:80px"><? echo $color_arr[$color_id];?></div></td>

											<td width="80">
												<div style="word-wrap:break-word; width:80px">
													<?

													//$color_type_id=$color_type_array[$batch_details[$row[csf("batch_id")]]['booking_no']][$row[csf("body_part_id")]]['color_type_id'];
													//echo $color_type[$color_type_id];

													$color_type_id=$color_type_array[$batch_details[$batch_id]['booking_no']][$body_id][$row[("deter_id")]]['color_type_id'];
											 		echo implode(",",array_unique(explode(",",chop($color_type_id,","))));
											 		?>
												</div>
											</td>
											<td width="80">
												<div style="word-wrap:break-word; width:80px"><? echo $color_range[$batch_details[$batch_id]['color_range_id']];//color_range_id ?></div>
											</td>

											<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $body_part[$body_id];?></div></td>

											<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $process_name;?></div></td>
											<td width="60"><div style="word-wrap:break-word; width:60px"><? echo $fabric_typee[$dia_type];?></div></td>
											<td width="200"><div style="word-wrap:break-word; width:200px">
												<?
												echo $composition_arr[$row[('deter_id')]]."<br>";
												?></div></td>
											<td width="50" align="center"><div style="word-wrap:break-word; width:50px"><?  echo $row[('gsm')]; ?></div></td>
											<td width="40" align="center"><div style="word-wrap:break-word; width:40px"><? echo $row[('dia')];?></div></td>

											<td width="40" align="center"><div style="word-wrap:break-word; width:40px"><? echo $row[('roll')]; $tot_roll+=$row[('roll')]; ?></div></td>
											<td  align="right" width="70" title="<? echo chop($row['sys_dtls_id'],','); ?>">
												<div style="word-wrap:break-word; width:70px">
													<?
													$grey_used_qty=$grey_userd_data_arr[$prod_id][$batch_id][$shade_id][$color_id][$body_id][$dia_type]["grey_used_qty"];

													//echo number_format($grey_userd_data_arr[$row[csf("id")]][$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("color_id")]][$row[csf("determination_id")]][$row[csf("body_part_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]][$row[csf("process_id")]]["grey_used_qty"],2); //[$row[csf("batch_id")]]

													echo number_format($grey_qty_used,2);
													//echo number_format($grey_used_qty,2);
													//echo number_format($row['grey_used_qnty'],2);
													//echo number_format($grey_used_arr[$row['sys_dtls_id']]['grey_used_quantity'],2);

													/*if($qnty_check[$row[("id")]][$row[("prod_id")]][$batch_id][$color_id][$row[("determination_id")]][$body_part][$row[("fabric_shade")]][$row[("dia_width_type")]][$row[("process_id")]]=="")
													{
														$qnty_check[$row[("id")]][$row[("prod_id")]][$batch_id][$row[csf("color_id")]][$row[("determination_id")]][$body_part][$row[("fabric_shade")]][$row[("dia_width_type")]][$row[("process_id")]]=$row[("process_id")];
														//$tot_grey_used+=$grey_userd_data_arr[$row[csf("id")]][$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("color_id")]][$row[csf("determination_id")]][$row[csf("body_part_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]][$row[csf("process_id")]]["grey_used_qty"];
														//$tot_grey_used+=$grey_used_qty;
													}*/
													//$tot_grey_used+=$grey_used_arr[$row['sys_dtls_id']]['grey_used_quantity'];
													$tot_grey_used+=$grey_qty_used;

													?>
												</div>
											</td>
											<td  align="right" width="70"><div style="word-wrap:break-word; width:70px"><? echo number_format($row[("current_delivery")],2); $tot_qty+=$row[("current_delivery")]; ?></div></td>
										</tr>
										<?
										$i++;
									}
								}
							}//	$batch_dia_type="";
						}
					}
				}
				?>
				<tr>
					<td align="right" colspan="18" ><strong>Total:</strong></td>
					<td align="center"><? echo $tot_roll; ?></td>
					<td align="right" ><? echo number_format($tot_grey_used,2,'.',''); ?></td>
					<td align="right" ><? echo number_format($tot_qty,2,'.',''); ?></td>
				</tr>
				<tr>
					<td colspan="20" align="left"><div style="word-wrap:break-word; font-weight: bold;">Remarks: <? echo $deliveryRemarks[$update_mst_id]["remarks"]; ?> </div></td>
				</tr>
            </tbody>
		</table>
        <br>
        <?
			echo signature_table(68, $company, "1820px",$cbo_template_id);
        ?>

        <script type="text/javascript" src="../includes/functions.js"></script>
        <script type="text/javascript" src="../js/jquery.js"></script>
        <script type="text/javascript" src="../js/jquerybarcode.js"></script>
        <script>
        fnc_generate_Barcode("<? echo $Challan_no;?>","barcode_img_id");
        </script>
        </div>
        <?
        exit();
}

if($action=="delivery_challan_print_33333333") // new report 3 is sale NO//Not used==12-17.19
{
	extract($_REQUEST);
	//echo '<pre>';print_r($_REQUEST);

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	//echo "DFFFFFFFFFFFF";die;
	$datas=explode('_',$data);
	$program_ids = implode(",",array_unique(explode(",",str_replace("'","",$datas[0]))));
	$company = str_replace("'","",$datas[1]);
	$from_date = str_replace("'","",$datas[2]);
	$to_date = str_replace("'","",$datas[3]);
	$product_ids = str_replace("'","",$datas[4]);
	$order_ids = implode(",",array_unique(explode(",",str_replace("'","",$datas[5]))));
	$location= str_replace("'","",$datas[6]);
	$buyer = str_replace("'","",$datas[7]);
	$update_mst_id = str_replace("'","",$datas[8]);
	$delivery_date = str_replace("'","",$datas[9]);
	$Challan_no = str_replace("'","",$datas[10]);
	$fin_prod_type = str_replace("'","",$datas[11]);
	$batch_ids = implode(",",array_unique(explode(",",str_replace("'","",$datas[12]))));
	$operation = str_replace("'","",$datas[13]);
	$txt_remarks = str_replace("'","",$datas[14]);
	$dyeing_company = str_replace("'","",$datas[15]);
	$cbo_template_id = str_replace("'","",$datas[16]);
	$delivery_company = str_replace("'","",$datas[17]);
	$delivery_location = str_replace("'","",$datas[18]);


	if($order_ids==""){$order_id_print_cond="";}else{$order_id_print_cond=" and a.order_id in ($order_ids)";}

	if($db_type==0)
	{
		if($from_date!="" && $to_date!="") $date_con="and a.receive_date between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'"; else $date_con="";
		$select_year="year(e.insert_date) as job_year";
	}
	else if($db_type==2)
	{
		if($from_date!="" && $to_date!="") $date_con="and a.receive_date between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'"; else $date_con="";
		$select_year="to_char(e.insert_date,'YYYY') as job_year";
	}

	if($location!=0) $location_con="and a.location_id=$location"; else $location_con="";
	if($buyer!=0) $buyer_con="and a.buyer_id=$buyer"; else $buyer_con="";

	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$supplier_arr=return_library_array( "select id, supplier_name from  lib_supplier",'id','supplier_name');
	$yarncount=return_library_array( "select id, yarn_count from  lib_yarn_count",'id','yarn_count');
	$supplier_brand=return_library_array("select id,brand_name from lib_brand", "id","brand_name");
	$non_order_buyer=return_library_array("select id, buyer_id from wo_non_ord_samp_booking_mst where booking_type=4 and status_active=1 and is_deleted=0","id","buyer_id");
	$floor_arr = return_library_array("select id,floor_name from lib_prod_floor  where status_active =1 and is_deleted=0", 'id', 'floor_name');

	$sql_forWorkingComp=sql_select("select a.knitting_source,a.knitting_company from inv_receive_master a,  pro_finish_fabric_rcv_dtls b
	where a.id=b.mst_id and a.entry_form=7 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_chack_cond and a.company_id=$company and a.id in ($program_ids) and b.prod_id in ($product_ids) $date_con $location_con
	order by b.batch_id");
	foreach($sql_forWorkingComp as $row)
	{
		if($row[csf("knitting_source")]==1)
		{
			$comID=$row[csf("knitting_company")];
			$knitting_source=$row[csf("knitting_source")];
		}
		else if($row[csf("knitting_source")]==3)
		{
			$comID=$row[csf("knitting_company")];
			$knitting_source=$row[csf("knitting_source")];

		}

	}

	if ($knitting_source==1) {
		$com_sql_working=sql_select("select id, company_name, plot_no, level_no, road_no, block_no, city, zip_code from lib_company where id=$comID");
		$com_address="";
		foreach($com_sql_working as $row)
		{
			$working_company_name=$row[csf("company_name")];
			if($row[csf("plot_no")]!="") $com_address.=$row[csf("plot_no")].", ";
			if($row[csf("level_no")]!="") $com_address.=$row[csf("level_no")].", ";
			if($row[csf("road_no")]!="") $com_address.=$row[csf("road_no")].", ";
			if($row[csf("block_no")]!="") $com_address.=$row[csf("block_no")].", ";
			if($row[csf("city")]!="") $com_address.=$row[csf("city")].", ";
			if($row[csf("zip_code")]!="") $com_address.=$row[csf("zip_code")].", ";
		}
		$com_address=chop($com_address," , ");
	}
	else if ($knitting_source==3) {
		$com_sql_working=sql_select("select id, supplier_name as company_name, address_1 from lib_supplier where id=$comID");
		$com_address="";
		foreach($com_sql_working as $row)
		{
			$working_company_name=$row[csf("company_name")];
			if($row[csf("address_1")]!="") $com_address.=$row[csf("address_1")].", ";
		}
		$com_address=chop($com_address," , ");
	}

	$com_sql=sql_select("select id, company_name, plot_no, level_no, road_no, block_no, city, zip_code from lib_company");
	foreach($com_sql as $row)
	{
		$company_arr[$row[csf("id")]]=$row[csf("company_name")];
	}

	$batch_sql=sql_select("SELECT a.id, a.batch_no, a.booking_no_id, a.color_range_id, a.booking_no, a.booking_without_order, b.prod_id, b.batch_qnty,b.body_part_id from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.id in($batch_ids) group by a.id, a.batch_no, a.color_range_id, a.booking_no_id, a.booking_no, a.booking_without_order, b.prod_id, b.batch_qnty,b.body_part_id ");
	$batch_details=array();
	foreach($batch_sql as $row)
	{
		$batch_details[$row[csf("id")]]["batch_no"]=$row[csf("batch_no")];
		$batch_details[$row[csf("id")]]["booking_no"]=$row[csf("booking_no")];
		$batch_details[$row[csf("id")]]["color_range_id"]=$row[csf("color_range_id")];
		$batch_details[$row[csf("id")]]["booking_no_id"]=$row[csf("booking_no_id")];
		$batch_details[$row[csf("id")]]["booking_without_order"]=$row[csf("booking_without_order")];
		$batch_details[$row[csf("id")]]["prod_id"]=$row[csf("prod_id")];
		$batch_details[$row[csf("id")]]["batch_qnty"]+=$row[csf("batch_qnty")];
		$batch_details[$row[csf("id")]]["body_part_id"]=$row[csf("body_part_id")];
		$all_booking_no_arr[$row[csf("booking_no")]] = $row[csf("booking_no")];
	}

	$all_booking_no_arr = array_filter($all_booking_no_arr);
	$all_booking_nos = "'".implode("','", $all_booking_no_arr)."'";
	$all_booking_no_cond=""; $bookCond="";
	$all_booking_no_cond_2=""; $bookCond_2="";
	if($db_type==2 && count($all_booking_no_arr)>999)
	{
		$all_booking_no_arr_chunk=array_chunk($all_booking_no_arr,999) ;
		foreach($all_booking_no_arr_chunk as $chunk_arr)
		{
			$chunk_arr_value=implode(",",$chunk_arr);
			$bookCond.="  a.booking_no in($chunk_arr_value) or ";
			$bookCond_2.="  p.booking_no in($chunk_arr_value) or ";
		}

		$all_booking_no_cond.=" and (".chop($bookCond,'or ').")";
		$all_booking_no_cond_2.=" and (".chop($bookCond_2,'or ').")";
	}
	else
	{
		$all_booking_no_cond=" and a.booking_no in($all_booking_nos)";
		$all_booking_no_cond_2=" and p.booking_no in($all_booking_nos)";
	}



	if($fin_prod_type==1)
	{
		$yarn_lot_data=sql_select("select  a.brand_id, b.po_breakdown_id, a.prod_id, a.yarn_lot as yarn_lot, a.yarn_count, a.stitch_length as stitch_length,a.machine_no_id as machine_no_id
			from pro_grey_prod_entry_dtls a, order_wise_pro_details b
			where a.id=b.dtls_id and b.entry_form in(2) and b.po_breakdown_id in($order_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");

		foreach($yarn_lot_data as $rows)
		{
			$knit_production_data[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['lot'].=$rows[csf('yarn_lot')].",";
			$knit_production_data[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['yarn_count'].=$rows[csf('yarn_count')].",";
			$knit_production_data[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['stitch_length'].=$rows[csf('stitch_length')].",";
			$knit_production_data[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['brand_id'].=$rows[csf('brand_id')].",";
			$knit_production_data[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['machine_no_id'].=$rows[csf('machine_no_id')].",";
		}
	}
	else
	{
		$yarn_lot_data=sql_select("select p.booking_no, a.brand_id, a.prod_id, a.yarn_lot, a.yarn_count, a.stitch_length,a.machine_no_id
			from inv_receive_master p, pro_grey_prod_entry_dtls a
			where p.id=a.mst_id and p.booking_without_order=1 and p.entry_form in(2) and a.status_active=1 and a.is_deleted=0 and p.status_active=1 and p.is_deleted=0 $all_booking_no_cond_2");
		foreach($yarn_lot_data as $rows)
		{
			$knit_production_data[$rows[csf('booking_no')]][$rows[csf('prod_id')]]['yarn_count']=$rows[csf('yarn_count')];
			$knit_production_data[$rows[csf('booking_no')]][$rows[csf('prod_id')]]['stitch_length']=$rows[csf('stitch_length')];
			$knit_production_data[$rows[csf('booking_no')]][$rows[csf('prod_id')]]['lot']=$rows[csf('yarn_lot')];
			$knit_production_data[$rows[csf('booking_no')]][$rows[csf('prod_id')]]['machine_no_id']=$rows[csf('machine_no_id')];
			$knit_production_data[$rows[csf('booking_no')]][$rows[csf('prod_id')]]['brand_id']=$rows[csf('brand_id')];
		}
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
	$update_row_check=array(); $deliveryRemarks=array();
	if($update_mst_id!="")
	{
		$sql_update=sql_select("select b.id,b.grey_sys_id,b.product_id,b.job_no, null order_id, d.grouping, b.current_delivery,b.roll,b.program_no,b.sys_dtls_id,b.color_id,b.determination_id,b.bodypart_id,b.fabric_shade,b.dia,b.width_type,a.remarks,c.process_id, c.batch_id
		from pro_grey_prod_delivery_mst a,pro_grey_prod_delivery_dtls b left join wo_po_break_down d on b.order_id = d.id, pro_finish_fabric_rcv_dtls c
		where a.id=b.mst_id and b.mst_id=$update_mst_id and b.sys_dtls_id = c.id and c.status_active=1 and c.is_deleted=0 and b.entry_form=54 and a.status_active=1 and a.is_deleted=0");


		$all_sys_dtls_id="";
		foreach($sql_update as $row)
		{
			//$all_sys_dtls_id.=$row[csf("sys_dtls_id")].",";
			$all_sys_dtls_id.=$row[csf("grey_sys_id")].",";
			if($fin_prod_type==1)
			{
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("batch_id")]."*".$row[csf("color_id")]."*".$row[csf("determination_id")]."*".$row[csf("bodypart_id")]."*".$row[csf("fabric_shade")]."*".$row[csf("dia")]."*".$row[csf("width_type")]."*".$row[csf("process_id")]][$row[csf("grouping")]]["current_delivery"] +=$row[csf("current_delivery")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("batch_id")]."*".$row[csf("color_id")]."*".$row[csf("determination_id")]."*".$row[csf("bodypart_id")]."*".$row[csf("fabric_shade")]."*".$row[csf("dia")]."*".$row[csf("width_type")]."*".$row[csf("process_id")]][$row[csf("grouping")]]["id"] =$row[csf("id")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("batch_id")]."*".$row[csf("color_id")]."*".$row[csf("determination_id")]."*".$row[csf("bodypart_id")]."*".$row[csf("fabric_shade")]."*".$row[csf("dia")]."*".$row[csf("width_type")]."*".$row[csf("process_id")]][$row[csf("grouping")]]["roll"] +=$row[csf("roll")];
				$deliveryRemarks[$row[csf("grey_sys_id")]]["remarks"]=$row[csf("remarks")];
			}
			else
			{
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("batch_id")]."*".$row[csf("color_id")]."*".$row[csf("determination_id")]."*".$row[csf("bodypart_id")]."*".$row[csf("fabric_shade")]."*".$row[csf("dia")]."*".$row[csf("width_type")]."*".$row[csf("process_id")]][$row[csf("grouping")]]["current_delivery"] +=$row[csf("current_delivery")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("batch_id")]."*".$row[csf("color_id")]."*".$row[csf("determination_id")]."*".$row[csf("bodypart_id")]."*".$row[csf("fabric_shade")]."*".$row[csf("dia")]."*".$row[csf("width_type")]."*".$row[csf("process_id")]][$row[csf("grouping")]]["id"] =$row[csf("id")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("batch_id")]."*".$row[csf("color_id")]."*".$row[csf("determination_id")]."*".$row[csf("bodypart_id")]."*".$row[csf("fabric_shade")]."*".$row[csf("dia")]."*".$row[csf("width_type")]."*".$row[csf("process_id")]][$row[csf("grouping")]]["roll"] +=$row[csf("roll")];
				$deliveryRemarks[$row[csf("grey_sys_id")]]["remarks"]=$row[csf("remarks")];
			}
		}
	}

	$all_sys_dtls_id=implode(",",array_unique(explode(",",chop($all_sys_dtls_id,","))));
	if($all_sys_dtls_id=="") $all_sys_dtls_id=0;

	/*$fin_pord_sql=sql_select("select a.id as mst_id,b.id, b.grey_used_qty,b.prod_id, b.batch_id, b.remarks,b.color_id,b.fabric_description_id,b.body_part_id,b.fabric_shade,b.dia_width_type, b.process_id from inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form=7 and b.mst_id in($all_sys_dtls_id)");*/
	$fin_pord_sql=sql_select("SELECT a.id as mst_id,b.id, c.grey_used_qty, b.grey_used_qty as dtls_grey_used_qty, b.prod_id, b.remarks,b.color_id,b.fabric_description_id,b.body_part_id,b.fabric_shade,b.dia_width_type, b.process_id, c.po_breakdown_id from inv_receive_master a, pro_finish_fabric_rcv_dtls b left join order_wise_pro_details c on  b.id=c.dtls_id and c.entry_form =7 and c.status_active =1 where a.id=b.mst_id and a.entry_form=7 and b.mst_id in($all_sys_dtls_id)");//, d.grouping

	$fin_pord_data=array();
	$$grey_userd_data_arr=array();
	foreach($fin_pord_sql as $row)
	{
		//$grey_userd_data_arr[$row[csf("id")]][$row[csf("po_breakdown_id")]]["grey_used_qty"]=$row[csf("grey_used_qty")];

		if($fin_prod_type==1)
		{
			$grey_userd_data_arr[$row[csf("mst_id")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("fabric_description_id")]][$row[csf("body_part_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]][$row[csf("process_id")]]["grey_used_qty"] += $row[csf("grey_used_qty")];//[$row[csf("grouping")]]
		}
		else
		{
			$grey_userd_data_arr[$row[csf("mst_id")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("fabric_description_id")]][$row[csf("body_part_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]][$row[csf("process_id")]]["grey_used_qty"] = $row[csf("dtls_grey_used_qty")];//[$row[csf("grouping")]]
		}

		$fin_pord_data[$row[csf("mst_id")]]["remarks"]=$row[csf("remarks")];
	}
	// echo"<pre>";print_r($grey_userd_data_arr);die();
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$company'","image_location");

	?>
	<div style="width:1200px;">
		<table width="1200" cellspacing="0" align="center" border="0">
			<tr>
				<td colspan="1" rowspan="5">
					<img src="../<? echo $image_location; ?>" height="70" width="200" style="float:left; margin-left:10px;">
				</td>
				<td colspan="15" align="center" style="font-size:x-large"><strong>Working Company: <? echo $working_company_name; ?></strong>
				</td>
			</tr>
			<tr>
				<td colspan="16" align="center" style="font-size:18px"><strong><? echo $com_address; ?></strong></td>
			</tr>
			<tr>
				<td colspan="16" align="center" style="font-size:18px"><strong>LC Company: <? echo $company_arr[$company]; ?></strong></td>
			</tr>
			<tr>
				<td colspan="16" align="center" style="font-size:18px"><strong>LC Company Location: <? echo $location_arr[$location]; ?></strong></td>
			</tr>
			<tr>
				<td colspan="16" align="center" style="font-size:18px"><strong><u>Delivery Challan, Finishing Section.</u></strong></td>
			</tr>
			<tr>
				<td id="barcode_img_id"></td>
			</tr>
		</table>
		<br>
		<table width="1200" cellspacing="0" align="center" border="0">
			<tr>
				<td style="font-size:16px; font-weight:bold;" width="110">Challan No</td>
				<td width="200">:<? echo $Challan_no; ?></td>
				<td style="font-size:16px; font-weight:bold;" width="150">Delivery Company</td>
				<td colspan="13"><? echo $company_arr[$delivery_company];?></td>
			</tr>
			<tr>
				<td style="font-size:16px; font-weight:bold;" width="110">Delivery Date </td>
				<td width="200">:<? echo $delivery_date; ?></td>
				<td style="font-size:16px; font-weight:bold;" width="150">Delivery Location</td>
				<td colspan="13"><? echo $location_arr[$delivery_location];?></td>
			</tr>
		</table>
	</div>
	<div style="width:1940px">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1940" class="rpt_table" >
			<thead>
				<tr>
					<th width="30" >SL</th>
					<!-- <th width="110">Order No</th> -->
					<!-- <th width="200">Fin. Prod. Company</th> -->
					<th width="40">System ID</th>
                    <th width="110">Internal Ref</th>
                	<th width="150">Buyer <!-- <br> & Job --></th>
                	<th width="90">Batch No</th>
                    <th width="80">Percentage of Color</th>
                	<th width="80">Fabric Shade</th>
                	<th width="100">Booking No</th>
                	<th width="80">Color</th>
					<th width="80">Color Type</th>
					<th width="80">Color Range</th>
					<th width="100">Body Part</th>
					<th width="100">Process Name</th>
					<th width="60">Dia Type</th>
					<th width="200">Fabric Type</th>
					<th width="50">Fin GSM</th>
					<th width="40">Fin. Dia</th>
					<!-- <th width="100">Grey Dia</th> -->
					<th width="40">Total Roll</th>
					<th width="70">Grey Used</th>
					<th width="70">Deli. Qty Finish</th>
				</tr>
			</thead>
			<tbody>
				<?

				if($order_ids!="")
				{
					$sql_machineDia=sql_select("select a.febric_description_id, b.po_breakdown_id, a.machine_dia
						from inv_receive_master p, pro_grey_prod_entry_dtls a, order_wise_pro_details b
						where p.id=a.mst_id and a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and p.status_active=1 and p.is_deleted=0 and b.po_breakdown_id in ($order_ids) and p.entry_form in(2,22) and a.machine_dia is not NULL group by a.febric_description_id, b.po_breakdown_id, a.machine_dia");
				}
				else
				{
					$sql_machineDia=sql_select("select p.booking_no, a.febric_description_id, a.machine_dia
						from inv_receive_master p, pro_grey_prod_entry_dtls a
						where p.id=a.mst_id  and a.status_active=1 and a.is_deleted=0 and p.status_active=1 and p.is_deleted=0 and p.booking_without_order=1 and a.machine_dia is not NULL $all_booking_no_cond_2 group by p.booking_no, a.febric_description_id, a.machine_dia");
				}

				$mc_dia_arr=array();
				foreach($sql_machineDia as $rows)
				{
					if($order_ids!="")
						$mc_dia_arr[$rows[csf('po_breakdown_id')]][$rows[csf('febric_description_id')]].=$rows[csf('machine_dia')].',';
					else
						$mc_dia_arr[$rows[csf('booking_no')]][$rows[csf('febric_description_id')]].=$rows[csf('machine_dia')].',';
				}
				unset($sql_machineDia);
				$sql="select id, item_category_id from product_details_master where item_category_id in(5,6,7) and company_id='$dyeing_company' and status_active=1 and is_deleted=0";
				$nameArray=sql_select($sql);
				foreach($nameArray as $row){
				$product_data_arr[$row[csf('id')]]=$row[csf('item_category_id')];
				}
				unset($nameArray);

			$sql_color_precentage="select a.id, a.batch_id, b.id as dtls_id, b.prod_id, b.sub_process_id, b.dose_base as item_cat, b.ratio from pro_recipe_entry_mst a, pro_recipe_entry_dtls b where a.id=b.mst_id and a.working_company_id=$dyeing_company and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.ratio is not null and b.seq_no is not null";
			$result_color_precentage=sql_select($sql_color_precentage);
			$batch_item_ratio_total_arr=array();
			$batchTotal_arr= array();
			foreach($result_color_precentage as $row){
			$batch_item_ratio_total_arr[$row[csf('batch_id')]][$product_data_arr[$row[csf('prod_id')]]]['ratio_total'] += $row[csf('ratio')];
			$batch_item_ratio_total_arr[$row[csf('batch_id')]][$product_data_arr[$row[csf('prod_id')]]]['ratio_count'] += 1;
			$batchTotal_arr[$row[csf('batch_id')]]['batch_ratio_total']  += $row[csf('ratio')];

			}
			unset($result_color_precentage);



				if($fin_prod_type==1)
				{
					$sql="select
					a.id,
					a.recv_number_prefix_num,
					a.recv_number,
					a.receive_date,
					a.receive_basis,
					a.knitting_source,
					a.knitting_company,
					b.fabric_description_id as determination_id,
					b.fabric_shade,
					b.gsm as gsm,
					b.width as dia,
					b.prod_id,
					b.batch_id as batch_id,
					b.color_id as color_id,
					b.body_part_id,
					b.process_id,
					b.dia_width_type,
					sum(c.quantity) as quantity,
					d.grouping,
					e.job_no,
					e.job_no_prefix_num,
					e.buyer_name,
					-- c.po_breakdown_id,
					sum(d.po_quantity*e.total_set_qnty) as po_qty,
					$select_year
					from
					inv_receive_master a,  pro_finish_fabric_rcv_dtls b ,order_wise_pro_details c,wo_po_break_down d,wo_po_details_master e
					where
					a.id=b.mst_id  and b.id=c.dtls_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and a.entry_form=7 and c.entry_form=7 and c.status_active=1 and c.is_deleted=0 and c.trans_type=1 and a.status_active=1 and a.is_deleted=0 and a.company_id=$company and a.id in ($program_ids) and b.prod_id in ($product_ids) and c.po_breakdown_id in($order_ids) $date_con $location_con
					group by b.batch_id,a.id,a.recv_number_prefix_num,a.recv_number,a.receive_date,a.receive_basis,a.knitting_source,a.knitting_company,b.fabric_description_id,b.fabric_shade,b.gsm,b.width,b.prod_id,b.color_id,b.body_part_id,b.process_id,b.dia_width_type, d.grouping,e.job_no,e.job_no_prefix_num,e.buyer_name,e.insert_date
					order by b.batch_id";
					//b.id as dtls_id,
				}
				else
				{
					$sql="select
					a.id,
					a.recv_number_prefix_num,
					a.recv_number,
					a.receive_date,
					a.receive_basis,
					a.knitting_source,
					a.knitting_company,
					b.fabric_description_id as determination_id,
					b.fabric_shade,
					b.gsm as gsm,
					b.width as dia,
					b.prod_id,
					b.batch_id as batch_id,
					b.color_id as color_id,
					b.body_part_id,
					b.process_id,
					b.dia_width_type,
					sum(b.receive_qnty) as quantity,
					'' as job_no,
					'' as job_no_prefix_num,
					'' as buyer_name,
					'' as po_breakdown_id,
					'' as job_year
					from
					inv_receive_master a,  pro_finish_fabric_rcv_dtls b
					where
					a.id=b.mst_id and a.entry_form=7 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_chack_cond and a.company_id=$company and a.id in ($program_ids) and b.prod_id in ($product_ids) $date_con $location_con
					group by b.batch_id,a.id, a.recv_number_prefix_num,a.recv_number,a.receive_date,a.receive_basis,a.knitting_source,a.knitting_company,b.fabric_description_id,b.fabric_shade,b.gsm,b.width,b.prod_id,b.color_id,b.body_part_id,b.process_id,b.dia_width_type
					order by b.batch_id";
					//b.id as dtls_id,
				}


				// echo $sql;
				$booking_without_order = sql_select("select a.booking_no_prefix_num, a.buyer_id,b.body_part,b.booking_no,b.color_type_id,a.grouping, b.lib_yarn_count_deter_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where  a.booking_no=b.booking_no and a.company_id=$company   and a.booking_type=4 $all_booking_no_cond");
				foreach ($booking_without_order as $row)
				{
					$color_type_array[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('lib_yarn_count_deter_id')]]['color_type_id'] .= $row[csf('color_type_id')].",";
					$internalRef_array[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('lib_yarn_count_deter_id')]]['color_type_id'] .= $row[csf('grouping')].",";
				}

				$color_sqls = "select  b.booking_no,c.color_type_id,c.body_part_id, c.lib_yarn_count_deter_id from wo_booking_mst a, wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.company_id=$company and a.booking_type=1 and b.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $all_booking_no_cond group by b.booking_no,c.color_type_id,c.body_part_id , c.lib_yarn_count_deter_id";
				$color_sql_result = sql_select($color_sqls);
				foreach ($color_sql_result as $keys=>$row2)
				{
					$color_type_array[$row2[csf('booking_no')]][$row2[csf('body_part_id')]][$row2[csf('lib_yarn_count_deter_id')]]['color_type_id'] .= $color_type[$row2[csf('color_type_id')]].",";
				}

				$nameArray=sql_select( $sql); $i=1; $tot_roll=0; $tot_qty=0;
				foreach($nameArray as $row)
				{
					if ($i%2==0)  $bgcolor="#E9F3FF";
					else $bgcolor="#FFFFFF";

					if($fin_prod_type==1)
					{
						//$index_pk=$row[csf("id")]."*".$row[csf("prod_id")]."*".$row[csf("po_breakdown_id")]."*".$row[csf("dtls_id")];

						$index_pk=$row[csf("id")]."*".$row[csf("prod_id")]."*".$row[csf("batch_id")]."*".$row[csf("color_id")]."*".$row[csf("determination_id")]."*".$row[csf("body_part_id")]."*".$row[csf("fabric_shade")]."*".$row[csf("dia")]."*".$row[csf("dia_width_type")]."*".$row[csf("process_id")];
					}
					else
					{
						//$index_pk=$row[csf("id")]."*".$row[csf("prod_id")]."*".$row[csf("dtls_id")];
						$index_pk=$row[csf("id")]."*".$row[csf("prod_id")]."*".$row[csf("batch_id")]."*".$row[csf("color_id")]."*".$row[csf("determination_id")]."*".$row[csf("body_part_id")]."*".$row[csf("fabric_shade")]."*".$row[csf("dia")]."*".$row[csf("dia_width_type")]."*".$row[csf("process_id")];
					}

					$process_all=array_unique(explode(",",$row[csf('process_id')]));
					$process_name='';
					$process_id_array=explode(",",$batch_process);
					foreach($process_all as $val)
					{
						if($process_name=="") $process_name=$conversion_cost_head_array[$val]; else $process_name.=",".$conversion_cost_head_array[$val];
					}
					$supplier_brand_value="";$yarn_count_value="";

					if($fin_prod_type==1)
					{
						$y_count=array_unique(explode(',',chop($knit_production_data[$batch_details[$row[csf("batch_id")]]["prod_id"]][$row[csf('po_breakdown_id')]]['yarn_count'],",")));
						foreach($y_count as $count_id)
						{
							if($count_id>0)
							{
								if($yarn_count_value=='') $yarn_count_value=$yarncount[$count_id]; else $yarn_count_value.=", ".$yarncount[$count_id];
							}
						}

						$supplier_brand_arr=array_unique(explode(',',chop($knit_production_data[$batch_details[$row[csf("batch_id")]]["prod_id"]][$row[csf('po_breakdown_id')]]['brand_id'],",")));
						foreach($supplier_brand_arr as $brand_id)
						{
							if($brand_id>0)
							{
								if($supplier_brand_value=='') $supplier_brand_value=$supplier_brand[$brand_id]; else $supplier_brand_value.=", ".$supplier_brand[$brand_id];
							}
						}

					}
					else
					{
						$supplier_brand=array_unique(explode(',',chop($knit_production_data[$batch_details[$row[csf("batch_id")]]["booking_no"]][$batch_details[$row[csf("batch_id")]]["brand_id"]]['yarn_count'],",")));
						foreach($y_count as $count_id)
						{
							if($count_id>0)
							{
								if($yarn_count_value=='') $yarn_count_value=$yarncount[$count_id]; else $yarn_count_value.=", ".$yarncount[$count_id];
							}
						}

						$supplier_brand=array_unique(explode(',',chop($knit_production_data[$batch_details[$row[csf("batch_id")]]["booking_no"]][$batch_details[$row[csf("batch_id")]]["brand_id"]]['brand_id'],",")));

						foreach($supplier_brand as $brand_id)
						{
							if($brand_id>0)
							{
								if($supplier_brand_value=='') $supplier_brand_value=$supplier_brand[$brand_id]; else $supplier_brand_value.=", ".$supplier_brand[$brand_id];
							}
						}
					}
					$ms_dia="";
					if($row[csf('po_breakdown_id')]!="") $ms_dia=$mc_dia_arr[$row[csf('po_breakdown_id')]][$row[csf('determination_id')]];
					else $ms_dia=$mc_dia_arr[$batch_details[$row[csf("batch_id")]]['booking_no']][$row[csf('determination_id')]];

					$all_machineDias=chop($ms_dia,',');
					$all_machineDias=implode(",",array_unique(explode(",",$all_machineDias)));

					$BatchTotal 				= $batchTotal_arr[$row[csf('batch_id')]]['batch_ratio_total'];
					$chemicalsTotal 			= $batch_item_ratio_total_arr[$row[csf('batch_id')]][5]['ratio_total'];
					$dyesTotal 					= $batch_item_ratio_total_arr[$row[csf('batch_id')]][6]['ratio_total'];
					$auxiChemicalsTotal			= $batch_item_ratio_total_arr[$row[csf('batch_id')]][7]['ratio_total'];

					$percentageOfColor = "";
					($dyesTotal>0 && $BatchTotal>0 )? $percentageOfColor .= "Dyes:". ($dyesTotal/$BatchTotal)*100 ."% " : $percentageOfColor .= "" ;


					if($update_row_check[$index_pk][$row[csf("grouping")]]["current_delivery"]>0)
					{
						$internal_ref_no=$row[csf('grouping')];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">

							<td width="30"  align="center"><div style="word-wrap:break-word; width:30px"><? echo $i; ?></div></td>

							<td width="40" align="center"><div style="word-wrap:break-word; width:40px"><? echo $row[csf('recv_number_prefix_num')]; ?></div></td>
                            <td width="110"><div style="word-wrap:break-word; width:110px; text-align:center">
                            	<?
                            	if($batch_details[$row[csf('batch_id')]]['booking_without_order']==1)
								{
                            		$internalRef_no =  $internalRef_array[$batch_details[$row[csf("batch_id")]]['booking_no']][$row[csf("body_part_id")]][$row[csf("determination_id")]]['color_type_id'];
                            		echo implode(array_unique(explode(",",chop($internalRef_no,","))));
								}
								else
								{
									echo $internal_ref_no;
								}
                            	?>&nbsp;</div></td>
                            <td width="150"><div style="word-wrap:break-word; width:150px">
								<?
								if($batch_details[$row[csf('batch_id')]]['booking_without_order']==1)
								{
									echo $buyer_array[$non_order_buyer[$batch_details[$row[csf("batch_id")]]['booking_no_id']]];
								}
								else
								{
									echo $buyer_array[$row[csf("buyer_name")]];
								}

								?></div></td>
								<td width="90" align="center"><div style="word-wrap:break-word; width:90px"><? echo $batch_details[$row[csf('batch_id')]]['batch_no'];?></div></td>
                                <td width="80" align="center">DD<?   echo $percentageOfColor; ?></td>
								<td width="80" align="center"><div style="word-wrap:break-word; width:80px"><? echo $fabric_shade[$row[csf('fabric_shade')]]; ?></div></td>
								<td align="center" width="100"><div style="word-wrap:break-word; width:100px"><? echo $batch_details[$row[csf("batch_id")]]['booking_no']; ?></div></td>
								<td width="80"><div style="word-wrap:break-word; width:80px"><? echo $color_arr[$row[csf('color_id')]];?></div></td>

								<td width="80">
									<div style="word-wrap:break-word; width:80px">
										<?
										$color_type_id=$color_type_array[$batch_details[$row[csf("batch_id")]]['booking_no']][$row[csf("body_part_id")]][$row[csf("determination_id")]]['color_type_id'];
								 		echo  $color_type[implode(",",array_unique(explode(",",chop($color_type_id,","))))];//implode(",",array_unique(explode(",",chop($color_type_id,","))));
								 		?>
									</div>
								</td>
								<td width="80">
									<div style="word-wrap:break-word; width:80px"><? echo $color_range[$batch_details[$row[csf("batch_id")]]['color_range_id']]; ?></div>
								</td>

								<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $body_part[$row[csf("body_part_id")]];?></div></td>

								<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $process_name;?></div></td>
								<td width="60"><div style="word-wrap:break-word; width:60px"><? echo $fabric_typee[$row[csf('dia_width_type')]];?></div></td>
								<td width="200"><div style="word-wrap:break-word; width:200px">
									<?
									echo $composition_arr[$row[csf('determination_id')]]."<br>";
									?></div></td>
								<td width="50" align="center"><div style="word-wrap:break-word; width:50px"><?  echo $row[csf('gsm')]; ?></div></td>
								<td width="40" align="center"><div style="word-wrap:break-word; width:40px"><? echo $row[csf('dia')];?></div></td>

								<td width="40" align="center"><div style="word-wrap:break-word; width:40px"><? echo $update_row_check[$index_pk][$row[csf("grouping")]]["roll"]; $tot_roll+=$update_row_check[$index_pk][$row[csf("grouping")]]["roll"]; ?></div></td>
								<td  align="right" width="70">
									<div style="word-wrap:break-word; width:70px">
										<?
										echo number_format($grey_userd_data_arr[$row[csf("id")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("determination_id")]][$row[csf("body_part_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]][$row[csf("process_id")]]["grey_used_qty"],2); //[$row[csf("batch_id")]]
										$tot_grey_used+=$grey_userd_data_arr[$row[csf("id")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("determination_id")]][$row[csf("body_part_id")]][$row[csf("fabric_shade")]][$row[csf("dia_width_type")]][$row[csf("process_id")]]["grey_used_qty"];  //[$row[csf("batch_id")]]
										?>
									</div>
								</td>
								<td  align="right" width="70"><div style="word-wrap:break-word; width:70px"><? echo number_format($update_row_check[$index_pk][$row[csf("grouping")]]["current_delivery"],2); $tot_qty+=$update_row_check[$index_pk][$row[csf("grouping")]]["current_delivery"]; ?></div></td>
							</tr>
								<?
								$i++;
							}
					$batch_dia_type="";
				}
						?>
						<tr>
							<td align="right" colspan="17" ><strong>Total:</strong></td>
							<td align="center"><? echo $tot_roll; ?></td>
							<td align="right" ><? echo number_format($tot_grey_used,2,'.',''); ?></td>
							<td align="right" ><? echo number_format($tot_qty,2,'.',''); ?></td>
						</tr>
						<tr>
							<td colspan="20" align="left"><div style="word-wrap:break-word; font-weight: bold;">Remarks: <? echo $deliveryRemarks[$row[csf("id")]]["remarks"]; ?> </div></td>
						</tr>
                        </tbody>
		</table>
        <br>
        <?
			echo signature_table(68, $company, "1820px",$cbo_template_id);
        ?>

        <script type="text/javascript" src="../includes/functions.js"></script>
        <script type="text/javascript" src="../js/jquery.js"></script>
        <script type="text/javascript" src="../js/jquerybarcode.js"></script>
        <script>
        fnc_generate_Barcode("<? echo $Challan_no;?>","barcode_img_id");
        </script>
        </div>
        <?
        exit();
}


if($action=="delivery_challan_print_sales_3") // new report 3 is sale
{
	extract($_REQUEST);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$datas=explode('_',$data);

	$company = str_replace("'","",$datas[1]);
	$update_mst_id = str_replace("'","",$datas[8]);
	$delivery_date = str_replace("'","",$datas[9]);
	$Challan_no = str_replace("'","",$datas[10]);
	$txt_remarks = str_replace("'","",$datas[14]);
	$cbo_template_id = str_replace("'","",$datas[15]);
	$delivery_company = str_replace("'","",$datas[16]);
	$delivery_location = str_replace("'","",$datas[17]);

	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$company'","image_location");
	$supplier_arr=return_library_array( "select id, supplier_name from  lib_supplier",'id','supplier_name');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');

	if($update_mst_id!="")
	{
		$sql="select a.sys_number,a.company_id as finish_company_id,a.delevery_date,b.grey_sys_id,b.order_id,b.batch_id,b.fabric_shade,b.color_id,b.bodypart_id,b.width_type,b.determination_id,b.gsm,b.dia,b.uom,
		b.roll,b.remarks,sum(b.current_delivery) as delivery_qty
		from pro_grey_prod_delivery_mst a,pro_grey_prod_delivery_dtls b
		where a.id=b.mst_id and a.id=$update_mst_id
		group by a.company_id,a.sys_number,a.delevery_date,b.grey_sys_id,b.order_id,b.batch_id,b.fabric_shade,b.color_id,b.bodypart_id,b.width_type,b.determination_id,b.gsm,b.dia,b.uom,
		b.roll,b.remarks";
	}

	$result=sql_select($sql);
	foreach($result as $row)
	{
		$salses_order_ids .= $row[csf('order_id')].",";
		$batchIds .= $row[csf('batch_id')].",";
		$determinationIds .= $row[csf('determination_id')].",";
		$finisProductIds .= $row[csf('grey_sys_id')].",";
	}

	$salses_order_ids=implode(",",array_filter(array_unique(explode(",",$salses_order_ids))));
	$finisProductIds=implode(",",array_filter(array_unique(explode(",",$finisProductIds))));


	$prod_result = sql_select("select a.id,a.recv_number_prefix_num,a.knitting_company,a.knitting_source,b.width,b.process_id,b.grey_used_qty from inv_receive_master a,pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.id in($finisProductIds) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	$fininsProductionArr = array();
	$fininsProductionGreyArr = array();

	foreach($prod_result as $row)
	{
		$fininsProductionArr[$row[csf('id')]]['recv_number'] = $row[csf('recv_number_prefix_num')];
		$fininsProductionArr[$row[csf('id')]]['grey_dia'] = $row[csf('width')];
		$fininsProductionArr[$row[csf('id')]]['process_id'] = $row[csf('process_id')];
		//$fininsProductionArr[$row[csf('id')]]['grey_used_qty'] = $row[csf('grey_used_qty')];
		$fininsProductionGreyArr[$row[csf('id')]][$row[csf('width')]]['grey_used_qty'] = $row[csf('grey_used_qty')];


		if($row[csf("knitting_source")]==1) {
			$fininsProductionArr[$row[csf('id')]]['knitting_company'] = $company_arr[$row[csf("knitting_company")]];
			$fininsProductionArr[$row[csf('id')]]['knitting_company_id'] = $row[csf("knitting_company")];
			$knittingSorce=$row[csf("knitting_source")];
		}
		else if ($row[csf("knitting_source")]==3)
		{
			$fininsProductionArr[$row[csf('id')]]['knitting_company'] = $supplier_arr[$row[csf("knitting_company")]];
			$fininsProductionArr[$row[csf('id')]]['knitting_company_id'] = $row[csf("knitting_company")];
			$knittingSorce=$row[csf("knitting_source")];
		}
	}
	unset($prod_result);
	foreach($result as $row)
	{
		$companyID= $fininsProductionArr[$row[csf('grey_sys_id')]]['knitting_company_id'];
	}


	if ($knittingSorce==1)
	{
		$com_sql_working=sql_select("select id, company_name, plot_no, level_no, road_no, block_no, city, zip_code from lib_company where id=$companyID");
		$com_address="";
		foreach($com_sql_working as $row)
		{
			$working_company_name=$row[csf("company_name")];
			if($row[csf("plot_no")]!="") $com_address.=$row[csf("plot_no")].", ";
			if($row[csf("level_no")]!="") $com_address.=$row[csf("level_no")].", ";
			if($row[csf("road_no")]!="") $com_address.=$row[csf("road_no")].", ";
			if($row[csf("block_no")]!="") $com_address.=$row[csf("block_no")].", ";
			if($row[csf("city")]!="") $com_address.=$row[csf("city")].", ";
			if($row[csf("zip_code")]!="") $com_address.=$row[csf("zip_code")].", ";
		}
		$com_address=chop($com_address," , ");
	}
	else if ($knittingSorce==3)
	{
		$com_sql_working=sql_select("select id, supplier_name as company_name, address_1 from lib_supplier where id=$companyID");
		$com_address="";
		foreach($com_sql_working as $row)
		{
			$working_company_name=$row[csf("company_name")];
			if($row[csf("address_1")]!="") $com_address.=$row[csf("address_1")].", ";
		}
		$com_address=chop($com_address," , ");
	}

	$com_sql=sql_select("select id, company_name, plot_no, level_no, road_no, block_no, city, zip_code from lib_company");
	foreach($com_sql as $row)
	{
		$company_arr[$row[csf("id")]]=$row[csf("company_name")];
	}

	if($salses_order_ids!="")
	{
		$sql_machineDia=sql_select("select a.febric_description_id, b.po_breakdown_id, a.machine_dia
		from inv_receive_master p, pro_grey_prod_entry_dtls a, order_wise_pro_details b
		where p.id=a.mst_id and a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and p.status_active=1 and p.is_deleted=0 and b.po_breakdown_id in ($salses_order_ids) and p.entry_form in(2,22) and a.machine_dia is not NULL group by a.febric_description_id, b.po_breakdown_id, a.machine_dia"); //

		$fabricSaleData = sql_select("select a.id,a.job_no,a.within_group,a.booking_without_order,a.buyer_id,a.po_buyer,a.po_job_no,a.sales_booking_no,sum(b.finish_qty) as order_qty,b.color_type_id from fabric_sales_order_mst a ,fabric_sales_order_dtls b where a.id=b.mst_id and a.id in ($salses_order_ids) group by a.id,a.job_no,a.within_group,a.booking_without_order,a.buyer_id,a.po_buyer,a.po_job_no,a.sales_booking_no,b.color_type_id");
		$salesData = array();
		foreach($fabricSaleData as $row)
		{
			$salesData[$row[csf('id')]]['order_qty'] = $row[csf('order_qty')];
			$salesData[$row[csf('id')]]['fso_no'] = $row[csf('job_no')];
			$salesData[$row[csf('id')]]['within_group'] = $row[csf('within_group')];
			$salesData[$row[csf('id')]]['booking_without_order'] = $row[csf('booking_without_order')];
			$salesData[$row[csf('id')]]['color_type_id'] = $row[csf('color_type_id')];

			$salesData[$row[csf('id')]]['sales_booking_no'] = $row[csf('sales_booking_no')];

			$booking_no_arr[] = "'".$row[csf('sales_booking_no')]."'";

			if($row[csf('within_group')]==1)
			{
				$salesData[$row[csf('id')]]['buyer_id'] = $row[csf('po_buyer')];
				$salesData[$row[csf('id')]]['job_no'] = $row[csf('po_job_no')];
			}
			else
			{
				$salesData[$row[csf('id')]]['buyer_id'] = $row[csf('buyer_id')];
				$salesData[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
			}
		}
	}
	unset($fabricSaleData);

	$all_machineDia="";
	$mc_dia_arr=array();
	foreach($sql_machineDia as $rows)
	{
		$mc_dia_arr[$rows[csf('po_breakdown_id')]][$rows[csf('febric_description_id')]].=$rows[csf('machine_dia')].',';
	}
	unset($sql_machineDia);


	$batch_ids=implode(",",array_filter(array_unique(explode(",",$batchIds))));

	if($batch_ids!=""){
		$batch_data=sql_select("select a.id, a.batch_no, a.booking_no, a.color_range_id from pro_batch_create_mst a where a.id in($batch_ids)");

		foreach($batch_data as $row)
		{
		$batch_details[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
		$batch_details[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
		$batch_details[$row[csf('id')]]['color_range_id']=$row[csf('color_range_id')];
		}
	}
	unset($batch_data);

	$determinationIds=implode(",",array_filter(array_unique(explode(",",$determinationIds))));

	if($determinationIds!="")
	{
		$composition_arr=array();
		$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id in($determinationIds)";
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
	}
	unset($data_array);
	ob_start();
	?>

	<div style="width:1200px;">
	<table width="1200" cellspacing="0" align="center" border="0">
        <tr>
            <td colspan="1" rowspan="4">
                <img src="../<? echo $image_location; ?>" height="70" width="200" style="float:left; margin-left:10px;">
            </td>
            <td colspan="15" align="center" style="font-size:x-large"><strong>Working Company: <? echo $working_company_name; ?></strong>
            </td>
        </tr>
        <tr>
            <td colspan="16" align="center" style="font-size:18px"><strong><? echo $com_address; ?></strong></td>
        </tr>
        <tr>
            <td colspan="16" align="center" style="font-size:18px"><strong><? echo $company_arr[$company]; ?></strong></td>
        </tr>
        <tr>
            <td colspan="16" align="center" style="font-size:18px"><strong><u>Delivery Challan, Finishing Section.</u></strong></td>
        </tr>
        <tr>
            <td id="barcode_img_id"></td>
        </tr>
	</table>
	<br>
	<table width="1200" cellspacing="0" align="center" border="0">
        <tr>
            <td style="font-size:16px; font-weight:bold;" width="110">Challan No</td>
            <td width="200">:<? echo $Challan_no; ?></td>
            <td style="font-size:16px; font-weight:bold;" width="150">Delivery Company</td>
            <td colspan="13"><? echo $company_arr[$delivery_company];?></td>
        </tr>
        <tr>
            <td style="font-size:16px; font-weight:bold;" width="110">Delivery Date </td>
            <td width="200">:<? echo $delivery_date; ?></td>
            <td style="font-size:16px; font-weight:bold;" width="150">Delivery Location</td>
            <td colspan="13"><? echo $location_arr[$delivery_location];?></td>
        </tr>
	</table>
	</div>

	<div style="width:1900px">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1900" class="rpt_table" >
            <thead>
                <tr>
                    <th width="30" >SL</th>
                    <!-- <th width="110" >Order No</th> -->
                    <!-- <th width="200" >Fin. Prod. Company</th> -->
                    <th width="40">System ID</th>
                    <th width="200">Buyer <!-- <br> & Job --></th>
                    <th width="50">Batch No</th>
                    <th width="40">Fabric Shade</th>
                    <th width="100">Booking No</th>
                    <th width="80">Color</th>
                    <th width="80">Color Type</th>
                    <th width="80">Color Range</th>
                    <th width="100">Body Part</th>
                    <th width="350">Process Name</th>
                    <th width="100">Dia Type</th>
                    <th width="250">Fabric Type</th>
                    <th width="50">Fin GSM</th>
                    <th width="40">Fin. Dia</th>
                    <!-- <th width="100">Grey Dia</th> -->
                    <th width="40">T. Roll</th>
                    <th width="70">Grey Used</th>
                    <th>Delivery Qty</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $nameArray=sql_select( $sql);
                $i=1;
                $tot_roll=0;
                $tot_qty=0;
                $tot_grey_used = 0;

                foreach($nameArray as $row)
                {
                    if ($i%2==0)
                        $bgcolor="#E9F3FF";
                    else
                        $bgcolor="#FFFFFF";
         		if($row[csf("delivery_qty")]>0){
                    $buerJobStr = $buyer_array[$salesData[$row[csf('order_id')]]['buyer_id']]."<br/>".$salesData[$row[csf('order_id')]]['job_no'];
                    $buerName= $buyer_array[$salesData[$row[csf('order_id')]]['buyer_id']];
                    $JobNO =$salesData[$row[csf('order_id')]]['job_no'];

                    $processIdArr = explode(",", $fininsProductionArr[$row[csf('grey_sys_id')]]['process_id']);
                    $process_name="";
                    foreach($processIdArr as $val)
                    {
                        if($process_name=="") $process_name=$conversion_cost_head_array[$val]; else $process_name.=",".$conversion_cost_head_array[$val];
                    }

                    $process_name = chop($process_name,",");



                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">

                        <td width="30"  align="center"><div style="word-wrap:break-word; width:30px"><? echo $i; ?></div></td>
                        <!-- <td width="110"><div style="word-wrap:break-word; width:110px"><? //echo $salesData[$row[csf('order_id')]]['fso_no'];?></div></td> -->
                        <!-- <td width="200"><? //echo $fininsProductionArr[$row[csf('grey_sys_id')]]['knitting_company'];?></td> -->
                        <td width="40" align="center"><div style="word-wrap:break-word; width:40px"><? echo $fininsProductionArr[$row[csf('grey_sys_id')]]['recv_number'];?></div></td>
                        <td width="200">
                            <div style="word-wrap:break-word; width:200px">
                                <? echo $buerName;//$buerJobStr;?>
                            </div>
                        </td>
                        <td width="50" align="center"><? echo $batch_details[$row[csf('batch_id')]]['batch_no'];?></td>
                        <td width="40" align="center"><div style="word-wrap:break-word; width:40px"><? echo $fabric_shade[$row[csf('fabric_shade')]];?></div></td>

                        <td align="center" width="100"><? echo $salesData[$row[csf('order_id')]]['sales_booking_no'];?></td>
                        <td width="80"><div style="word-wrap:break-word; width:80px"><? echo $color_arr[$row[csf('color_id')]];?></div></td>
                        <td width="80"><div style="word-wrap:break-word; width:80px"><? echo $color_type[$salesData[$row[csf('order_id')]]['color_type_id']];?></div></td>

                        <td width="80"><div style="word-wrap:break-word; width:80px"><? echo $color_range[$batch_details[$row[csf('batch_id')]]['color_range_id']]; ?></div></td>

                        <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $body_part[$row[csf("bodypart_id")]];?></div></td>
                        <td width="350"><div style="word-wrap:break-word; width:350px"><? echo $process_name;?></div></td>
                        <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $fabric_typee[$row[csf('width_type')]];?></div></td>
                        <td width="250">
                            <div style="word-wrap:break-word; width:250px">
                                <? echo $composition_arr[$row[csf('determination_id')]]; ?>
                            </div>
                        </td>
                        <td width="50" align="center"><div style="word-wrap:break-word; width:50px"><? echo $row[csf('gsm')]; ?></div></td>
                        <td width="40" align="center">
                            <div style="word-wrap:break-word; width:40px"><? echo $row[csf('dia')]; ?></div>
                        </td>
                        <!-- <td width="100" align="center">
                            <div style="word-wrap:break-word; width:100px">
                                <?
                                //$mc_dia=$mc_dia_arr[$row[csf('order_id')]][$row[csf('determination_id')]];
                                //echo chop($mc_dia,','); ?>
                            </div>
                        </td> -->

                        <td width="40" align="center">
                            <?
                                $tot_roll += $row[csf('roll')];
                                echo $row[csf('roll')];
                            ?>
                        </td>

                        <td  align="right" width="70">
                            <?
                            $tot_grey_used+=$fininsProductionGreyArr[$row[csf('grey_sys_id')]][$row[csf('dia')]]['grey_used_qty'];
                            echo $fininsProductionGreyArr[$row[csf('grey_sys_id')]][$row[csf('dia')]]['grey_used_qty'];
                            ?>

                        </td>

                        <td  align="right">
                            <div style="word-wrap:break-word; width:70px">

                                <?
                                $tot_qty += $row[csf("delivery_qty")];
                                echo number_format($row[csf("delivery_qty")],2);
                                ?>
                            </div>
                        </td>
                    </tr>
                    <?php
                    $i++;
                }
			}
                ?>
                <tr>
                    <td align="right" colspan="15" ><strong>Total:</strong></td>
                    <td align="center"><? echo $tot_roll; ?></td>
                    <td align="right" ><? echo number_format($tot_grey_used,2,'.',''); ?></td>
                    <td align="right" ><? echo number_format($tot_qty,2,'.',''); ?></td>
                </tr>
                <tr>
                    <td colspan="16"> <b>  Remarks : </b> <? echo $txt_remarks; ?> </td>
                </tr>
            </tbody>
        </table>
        <br>
        <?
        echo signature_table(68, $company, "1600px",$cbo_template_id);
        ?>

        <script type="text/javascript" src="../includes/functions.js"></script>
        <script type="text/javascript" src="../js/jquery.js"></script>
        <script type="text/javascript" src="../js/jquerybarcode.js"></script>
        <script>
            fnc_generate_Barcode("<? echo $Challan_no;?>","barcode_img_id");
        </script>
	</div>
	<?
	exit();
} // end action

if ($action=="batch_number_popup")
{
	echo load_html_head_contents("Batch Number Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(str)
		{
			$('#hidden_batch_data').val(str);
			parent.emailwindow.hide();
		}
	</script>
</head>

<body>
	<div align="center" style="width:800px;">
		<form name="searchbatchnofrm"  id="searchbatchnofrm">
			<fieldset style="width:790px; margin-left:10px">
				<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" border="1" rules="all" width="770" class="rpt_table">
					<thead>
						<th width="240">Batch Date Range</th>
						<th width="170">Search By</th>
						<th id="search_by_td_up" width="200">Please Enter Batch No</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
							<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
							<input type="hidden" name="txt_dyeing_company_id" id="txt_dyeing_company_id" class="text_boxes" value="<? echo $cbo_dyeing_company; ?>">
							<input type="hidden" name="hidden_batch_data" id="hidden_batch_data" class="text_boxes" value="">
						</th>
					</thead>
					<tr class="general">
						<td>
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px;">To
							<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px;">
						</td>
						<td>
							<?
							$search_by_arr=array(0=>"Batch No",1=>"Fabric Booking no.",2=>"Color");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
						</td>
						<td align="center" id="search_by_td" width="140px">
							<input type="text" style="width:130px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('txt_dyeing_company_id').value, 'create_batch_search_list_view', 'search_div', 'finish_feb_delivery_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td colspan="5" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
					</tr>
				</table>
				<div id="search_div"></div>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_batch_search_list_view")
{
	$data = explode("_",$data);
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];
	$dyeing_company_id =$data[5];

	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.batch_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and a.batch_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else $date_cond="";

	if(trim($data[0])!="")
	{
		if($search_by==0)
			$search_field_cond="and a.batch_no like '$search_string'";
		else if($search_by==1)
			$search_field_cond="and a.booking_no like '$search_string'";
		else
			$search_field_cond="and a.color_id in(select id from lib_color where color_name like '$search_string')";
	}
	else $search_field_cond="";

	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$sql = "select a.id, a.batch_no, a.extention_no, a.batch_date, a.batch_weight, a.booking_no, a.color_id, a.batch_against, a.booking_without_order, a.re_dyeing_from, a.sales_order_id, a.sales_order_no, a.is_sales, b.knitting_location_id, b.knitting_source, b.knitting_company, b.location_id, c.buyer_id  from pro_batch_create_mst a, inv_receive_master b, pro_finish_fabric_rcv_dtls c
	where a.entry_form in (0,7) and b.id=c.mst_id and b.entry_form=7 and a.id=c.batch_id and a.batch_for not in (2,3) and a.batch_against<>4 and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $search_field_cond $date_cond group by a.id, a.batch_no, a.extention_no, a.batch_date, a.batch_weight, a.booking_no, a.color_id, a.batch_against, a.booking_without_order, a.re_dyeing_from, a.sales_order_id, a.sales_order_no, a.is_sales, b.knitting_location_id, b.knitting_source, b.knitting_company, b.location_id, c.buyer_id order by a.id DESC";
	//echo $sql;//die;
	$nameArray=sql_select( $sql );
	$batch_id_arr = array();
	foreach ($nameArray as $selectResult)
	{
		$batch_id_arr[] = $selectResult[csf('id')];
	}

	if(!empty($batch_id_arr)){
		if($db_type==0)
		{
			$order_id_arr=return_library_array( "select mst_id, group_concat(po_id) as po_id from pro_batch_create_dtls where mst_id in(".implode(",",$batch_id_arr).") and status_active=1 and is_deleted=0 group by mst_id",'mst_id','order_id');
		}
		else
		{
			//echo "select mst_id, LISTAGG(cast(po_id as VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY po_id) as po_id from pro_batch_create_dtls where mst_id in(".implode(",",$batch_id_arr).") and status_active=1 and is_deleted=0 group by mst_id";
			$order_id_arr=return_library_array( "select mst_id, LISTAGG(cast(po_id as VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY po_id) as po_id from pro_batch_create_dtls where mst_id in(".implode(",",$batch_id_arr).") and status_active=1 and is_deleted=0 group by mst_id",'mst_id','po_id');
		}
	}

	$po_arr=array(); $tot_rows=0; $poIds_cond="";
	//if(!empty($order_id_arr)){
	$poIds=implode(",",$order_id_arr);
	$poIds=array_unique(explode(",",$poIds));
	$poIdst=implode(",",$poIds);

	$tot_rows=count(array_filter($poIds));

	if($tot_rows != 0)
	{
		if($db_type==2 && $tot_rows>1000)
		{
			$poIds_cond=" and (";
			$poIdsArr=array_chunk(explode(",",$poIdst),999);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$poIds_cond.=" id in ($ids) or ";
			}
			$poIds_cond=chop($poIds_cond,'or ');
			$poIds_cond.=")";
		}
		else
		{
			$poIds_cond=" and id in ($poIdst)";
		}
		//echo "select id, po_number,file_no,grouping as ref, job_no_mst from wo_po_break_down where 1=1 and status_active=1 $poIds_cond";
		$po_data=sql_select("select id, po_number,file_no,grouping as ref, job_no_mst from wo_po_break_down where 1=1 and status_active=1 $poIds_cond");
		foreach($po_data as $row)
		{
			$po_arr[$row[csf('id')]]['po_no']=$row[csf('po_number')];
			$po_arr[$row[csf('id')]]['file']=$row[csf('file_no')];
			$po_arr[$row[csf('id')]]['ref']=$row[csf('ref')];
			$po_arr[$row[csf('id')]]['job_no']=$row[csf('job_no_mst')];
		}
	}
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="920" class="rpt_table" >
		<thead>
			<th width="40">SL</th>
			<th width="90">Batch No</th>
			<th width="80">Extention No</th>
			<th width="80">Batch Date</th>
			<th width="80">Batch Qnty</th>
			<th width="115">Booking No</th>
			<th width="110">Color</th>
			<th width="170">Po/FSO No</th>
			<th width="60">File No</th>
			<th>Ref. No</th>
		</thead>
	</table>
	<div style="width:920px; overflow-y:scroll; max-height:250px;" id="buyer_list_view" align="center">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table" id="tbl_list_search" >
			<?
			$i=1;
			foreach ($nameArray as $selectResult)
			{
				$po_no='';  $file_no='';  $ref_no=''; $job_array=array();
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$is_sales = $selectResult[csf('is_sales')];
				if($is_sales == 1){
					$po_no=$selectResult[csf('sales_order_no')];
					$file_no=""; $ref_no="";
					$job_no=$selectResult[csf('sales_order_no')];
				}else{
					$order_id=array_unique(explode(",",$order_id_arr[$selectResult[csf('id')]]));
					foreach($order_id as $value)
					{
						if($po_no=='') $po_no=$po_arr[$value]['po_no']; else $po_no.=",".$po_arr[$value]['po_no'];
						if($file_no=='') $file_no=$po_arr[$value]['file']; else $file_no.=",".$po_arr[$value]['file'];
						if($ref_no=='') $ref_no=$po_arr[$value]['ref']; else $ref_no.=",".$po_arr[$value]['ref'];
						$job_no=$po_arr[$value]['job_no'];
						if(!in_array($job_no,$job_array))
						{
							$job_array[]=$job_no;
						}
					}
					$job_no=implode(",",$job_array);
				}
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $selectResult[csf('id')].'_'.$selectResult[csf('batch_no')].'_'.$selectResult[csf('knitting_source')].'_'.$selectResult[csf('knitting_company')].'_'.$selectResult[csf('knitting_location_id')].'_'.$selectResult[csf('location_id')].'_'.$selectResult[csf('buyer_id')]; ?>')">
					<td width="40" align="center"><? echo $i; ?></td>
					<td width="90" align="center"><p><? echo $selectResult[csf('batch_no')]; ?></p></td>
					<td width="80"><p><? if($selectResult[csf('extention_no')]!=0) echo $selectResult[csf('extention_no')]; ?></p></td>
					<td width="80" align="center"><? echo change_date_format($selectResult[csf('batch_date')]); ?></td>
					<td width="80" align="right"><? echo $selectResult[csf('batch_weight')]; ?></td>
					<td width="115" align="center"><p><? echo $selectResult[csf('booking_no')]; ?></p></td>
					<td width="110" align="center"><p><? echo $color_arr[$selectResult[csf('color_id')]]; ?></p></td>
					<td width="170" align="center"><p><? echo $po_no; ?></p></td>
					<td width="60"><p><? echo $file_no; ?></p></td>
					<td width=""><p><? echo $ref_no; ?></p></td>
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


if($action=="delivery_challan_print_sales_4")
{
	extract($_REQUEST);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$datas=explode('_',$data);

	$company = str_replace("'","",$datas[1]);
	$update_mst_id = str_replace("'","",$datas[8]);
	$delivery_date = str_replace("'","",$datas[9]);
	$Challan_no = str_replace("'","",$datas[10]);
	$txt_remarks = str_replace("'","",$datas[14]);
	$cbo_template_id = str_replace("'","",$datas[15]);


	$com_sql=sql_select("select id, company_name, plot_no, level_no, road_no, block_no, city, zip_code, email, website, contact_no from lib_company where id=$company");
	$com_address="";
	foreach($com_sql as $row)
	{
	$company_name=$row[csf("company_name")];
	if($row[csf("plot_no")]!="") $com_address.=$row[csf("plot_no")].", ";
	if($row[csf("level_no")]!="") $com_address.=$row[csf("level_no")].", ";
	if($row[csf("road_no")]!="") $com_address.=$row[csf("road_no")].", ";
	if($row[csf("block_no")]!="") $com_address.=$row[csf("block_no")].", ";
	if($row[csf("city")]!="") $com_address.=$row[csf("city")].", ";
	if($row[csf("zip_code")]!="") $com_address.=$row[csf("zip_code")].", ";
	if($row[csf("email")]!="") $com_email_web.=$row[csf("email")].", ";
	if($row[csf("website")]!="") $com_email_web.= $row[csf("website")].", ";
	if($row[csf("contact_no")]!="") $com_number_fax.="TEL#".$row[csf("contact_no")].", ";
	// if($row[csf("")]!="") $com_number_web.="FAX#".$row[csf("")].", ";
	}
	$com_address=chop($com_address," , ");
	$com_number_fax=chop($com_number_fax," , ");
	$com_email_web=chop($com_email_web," , ");



	$supplier_arr=return_library_array( "select id, supplier_name from  lib_supplier",'id','supplier_name');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

	if($update_mst_id!="")
	{
		$sql="select a.sys_number,a.company_id as finish_company_id,a.delevery_date,b.grey_sys_id,b.order_id,b.batch_id,b.fabric_shade,b.color_id,b.bodypart_id, b.width_type,b.determination_id, b.gsm,b.dia,b.uom, b.roll,b.remarks,sum(b.current_delivery) as delivery_qty , c.no_of_roll, c.remarks as remarks_roll from pro_grey_prod_delivery_mst a,pro_grey_prod_delivery_dtls b , pro_finish_fabric_rcv_dtls c where a.id=b.mst_id and a.id=$update_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.mst_id = b.grey_sys_id and c.id=b.sys_dtls_id group by a.company_id,a.sys_number,a.delevery_date,b.grey_sys_id,b.order_id,b.batch_id,b.fabric_shade,b.color_id,b.bodypart_id,b.width_type, b.determination_id,b.gsm,b.dia,b.uom, b.roll,b.remarks, c.no_of_roll, c.remarks  order by c.no_of_roll";
		  
	} 

	$result=sql_select($sql);
	$color_mst=array();
	$width_type=array();
	foreach($result as $row)
	{
		$salses_order_ids .= $row[csf('order_id')].",";
		$batchIds .= $row[csf('batch_id')].",";
		$determinationIds .= $row[csf('determination_id')].",";
		$finisProductIds .= $row[csf('grey_sys_id')].",";
		array_push($color_mst,$row[csf('color_id')]);
		array_push($width_type,$row[csf('determination_id')]);

	}

	$salses_order_ids=implode(",",array_filter(array_unique(explode(",",$salses_order_ids))));
	

	$batch_ids=implode(",",array_filter(array_unique(explode(",",$batchIds))));

	if($batch_ids!=""){
	$batch_data=sql_select("select a.id, a.batch_no,a.booking_no from  pro_batch_create_mst a where a.id in($batch_ids)");
	$barch_for_mst= array();
	foreach($batch_data as $row)
	{
	$batch_details[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
	$batch_details[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
	array_push($barch_for_mst,$row[csf('batch_no')]);
	}
	}
	unset($batch_data);


	if($salses_order_ids!="")
	{

		$fabricSaleData = sql_select("SELECT a.id,a.job_no, a.within_group,a.booking_without_order,a.buyer_id,a.po_buyer,a.po_job_no, a.sales_booking_no, SUM(b.finish_qty) AS order_qty,a.customer_buyer, b.gsm_weight, b.dia, b.color_id, b.fabric_desc, b.determination_id FROM fabric_sales_order_mst a, fabric_sales_order_dtls b WHERE a.id = b.mst_id AND a.id IN ($salses_order_ids) GROUP BY a.id,a.job_no, a.within_group,a.booking_without_order,a.buyer_id,a.po_buyer, a.po_job_no, a.sales_booking_no, a.customer_buyer, b.gsm_weight, b.dia, b.color_id, b.fabric_desc, b.determination_id"); 
		
		$fso_for_mst=array();
		$customer_buyer_mst=array();
		$sales_booking_no_mst= array();
		$gsm_weight_mst=array();
		$dia_mst=array();
		foreach($fabricSaleData as $row)
		{
			array_push($fso_for_mst,$row[csf('job_no')]);
			array_push($customer_buyer_mst,$row[csf('customer_buyer')]);
			array_push($sales_booking_no_mst,$row[csf('sales_booking_no')]);
			// array_push($gsm_weight_mst,$row[csf('gsm_weight')]);
			// array_push($dia_mst,$row[csf('dia')]);
			$gsm_weight_mst[$row[csf('color_id')]][$row[csf('determination_id')]]['gsm_weight']= $row[csf('gsm_weight')];
			$dia_mst[$row[csf('color_id')]][$row[csf('determination_id')]]['dia']= $row[csf('dia')];
		}
	}
	unset($fabricSaleData);

	$determinationIds=implode(",",array_filter(array_unique(explode(",",$determinationIds))));

	if($determinationIds!="")
	{
		$composition_arr=array();
		$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id in($determinationIds)";
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
	}
	unset($data_array);
	ob_start();
	?>

	<div style="width:1060px;">
	<table width="1060" cellspacing="0" align="center" border="0">
			<tr>
				<td colspan="16" align="center" style="font-size:x-large"><strong><? echo $company_name; ?></strong>
				</td>
			</tr>
			<tr>
				<td colspan="16" align="center" style="font-size:12px"><strong><? echo $com_address; ?></strong></td>
			</tr>
			<tr>
				<td colspan="16" align="center" style="font-size:12px"><strong><? echo $com_number_fax; ?></strong></td>
			</tr>
			<tr>
				<td colspan="16" align="center" style="font-size:12px"><strong><? echo $com_email_web; ?></strong></td>
			</tr>
			<tr>
				<td colspan="16" align="center" style="font-size:18px"><strong><u>Packing List</u></strong></td>
			</tr>
	</table>
	<br>

	<table width="1060" cellspacing="0" align="center" border="0">
		<tr>
			<td   style="font-size:15px; font-weight:bold;" width="110">Packing List No</td>
			<td colspan="15" width="200">: <? echo $Challan_no; ?></td>
			<td   style="font-size:15px; font-weight:bold;" width="80">Batch No</td>
			<td colspan="15"  width="150">: <? 
				$barch_for_mst= array_unique($barch_for_mst);
				echo $barch_for_mst[0]; 
			?></td>
			<td   style="font-size:15px; font-weight:bold;" width="120">FSO NO</td>
			<td colspan="15" width="150">: <? 
				$fso_for_mst= array_unique($fso_for_mst); 
				echo $fso_for_mst[0];
			?></td>
			<td   style="font-size:15px; font-weight:bold;" width="130">Date</td>
			<td colspan="15" width="120">: <? echo $delivery_date; ?></td>
		</tr>
		<tr>
			<td   style="font-size:15px; font-weight:bold;" width="110">Buyer Name: </td>
			<td colspan="15" width="200">: <? 
				$customer_buyer_mst= array_unique($customer_buyer_mst); 
				echo $buyer_arr[$customer_buyer_mst[0]]; 
			?></td>
			<td   style="font-size:15px; font-weight:bold;" width="80">Order No</td>
			<td colspan="15" width="150">: <? 
				$sales_booking_no_mst= array_unique($sales_booking_no_mst); 
				echo $sales_booking_no_mst[0];
			?></td>
			<td   style="font-size:15px; font-weight:bold;" width="120">Color</td>
			<td colspan="15" width="150">: <? 
				// $color_mst= array_unique($color_mst); 
				echo $color_arr[$color_mst[0]];
			?></td>
			
			<td   style="font-size:15px; font-weight:bold;" width="130">Required GSM</td>
			<td colspan="15" width="120">: <?
				echo $gsm_weight_mst[$color_mst[0]][$width_type[0]]['gsm_weight'];
			?></td>
		</tr>

		<tr>
			<td   style="font-size:15px; font-weight:bold;" width="110">Fabrication</td>
			<td colspan="15" width="200">: <?
				$width_type = array_unique($width_type); 
				echo $composition_arr[$width_type[0]];
			?></td>
			<td   style="font-size:15px; font-weight:bold;" width="80">Required Width</td>
			<td colspan="15" width="150">: <?
				echo $dia_mst[$color_mst[0]][$width_type[0]]['dia'];
				 ?></td>
			<td   style="font-size:15px; font-weight:bold;" width="120">Fabric Location</td>
			<td colspan="15" width="150">: <? 
				echo $txt_remarks;
			?></td>
			<td   style="font-size:15px; font-weight:bold;" width="130">Greige Fabric Qty</td>
			<td colspan="15" width="120">: <?
				$barch_for_mst= array_unique($barch_for_mst); 
				$batch_weight= return_field_value("batch_weight"," pro_batch_create_mst","batch_no='$barch_for_mst[0]' and is_deleted=0 and status_active=1");
				echo $batch_weight;  
			?></td>
		</tr>

		<tr>
			<td style="font-size:16px; font-weight:bold;"> &nbsp;</td>
		</tr>
	</table>
	</div>
		 
	<div style="width:1060px">
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1060" class="rpt_table" >
	<thead>
		<tr bgcolor="#acb3ab">
			<!-- <th width="30" >SL</th> -->
			<th width="50">Roll No</th>
			<th width="100">Weight in Kgs</th>
			<th width="150">Length Of Roll</th>
			<th width="50">Actual GSM</th>
			<th width="50">Actual Width</th>
			<th width="50">Shade</th>
			<th >Remarks</th>
		</tr>
	</thead>
	<tbody>

		<?php
		$nameArray=sql_select( $sql);
		$i=1;
		$tot_roll=0;
		$tot_qty=0;

		foreach($nameArray as $row)
		{
			if ($i%2==0) {$bgcolor="#E9F3FF";} else{$bgcolor="#FFFFFF";}

			if($row[csf("delivery_qty")]>0){

				?>
				<tr bgcolor="<? echo $bgcolor; ?>">

					<!-- <td width="30"  align="center"><div style="word-wrap:break-word; width:30px"><? // echo $i; ?></div></td> -->
					<td width="50" align="center"><div style="word-wrap:break-word; width:50px"><?
						$tot_roll += 1;
						echo $row[csf('no_of_roll')];
					 
					 ?></div></td>

					<td width="60" align="center"><p><?
						$tot_qty += $row[csf("delivery_qty")];
						echo number_format($row[csf("delivery_qty")],2);
					?></p></td>

					<td width="150" align="center">
						<div style="word-wrap:break-word; width:150px">
							<? 
							echo $row[csf('remarks_roll')];
							?>
						</div>
					</td>

					<td width="40" align="center"><div style="word-wrap:break-word; width:40px"><? echo $row[csf('gsm')]; ?></div></td>

					<td width="50" align="center"><? echo $row[csf('dia')]; ?></td>

					<td width="40" align="center"><div style="word-wrap:break-word; width:40px"><? echo $fabric_shade[$row[csf('fabric_shade')]];?></div></td>

					<td align="center" width="200"><? ?></td>
					
				</tr>
				<?php
				$i++;
			}
		?>
	
		<?
		}
		?>
		<tr bgcolor="#acb3ab">
			<td align="center"> Total: <? echo $tot_roll; ?></td>
			<td align="center" ><? echo number_format($tot_qty,2,'.',''); ?></td>
			<td colspan="5"></td>
		</tr>
	<tbody>
	</table>
		<br>
		<?
		echo signature_table(59, $company, "1060px",$cbo_template_id);
		?>

		<script type="text/javascript" src="../includes/functions.js"></script>
		<script type="text/javascript" src="../js/jquery.js"></script>
		<script type="text/javascript" src="../js/jquerybarcode.js"></script>
		
	</div>

	<?
	exit();
}

if($action=="delivery_challan_print_4_999") // no useed 
{
	extract($_REQUEST);

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$datas=explode('_',$data);
	$program_ids = implode(",",array_unique(explode(",",str_replace("'","",$datas[0]))));
	$company = str_replace("'","",$datas[1]);
	$from_date = str_replace("'","",$datas[2]);
	$to_date = str_replace("'","",$datas[3]);
	$product_ids = str_replace("'","",$datas[4]);
	$order_ids = implode(",",array_unique(explode(",",str_replace("'","",$datas[5]))));
	$location= str_replace("'","",$datas[6]);
	$buyer = str_replace("'","",$datas[7]);
	$update_mst_id = str_replace("'","",$datas[8]);
	$delivery_date = str_replace("'","",$datas[9]);
	$Challan_no = str_replace("'","",$datas[10]);
	$fin_prod_type = str_replace("'","",$datas[11]);
	$batch_ids = implode(",",array_unique(explode(",",str_replace("'","",$datas[12]))));
	$operation = str_replace("'","",$datas[13]);
	$txt_remarks = str_replace("'","",$datas[14]);
	$cbo_template_id = str_replace("'","",$datas[15]);
	$deli_company = str_replace("'","",$datas[16]);
	$deli_location = str_replace("'","",$datas[17]);
	$dye_location = str_replace("'","",$datas[18]);
	$txt_remark = str_replace("'","",$datas[19]);


	if($order_ids==""){$order_id_print_cond="";}else{$order_id_print_cond=" and a.order_id in ($order_ids)";}

	$com_sql=sql_select("select id, company_name, plot_no, level_no, road_no, block_no, city, zip_code from lib_company where id=$company");
	$com_address="";
	foreach($com_sql as $row)
	{
		$company_name=$row[csf("company_name")];
		if($row[csf("plot_no")]!="") $com_address.=$row[csf("plot_no")].", ";
		if($row[csf("level_no")]!="") $com_address.=$row[csf("level_no")].", ";
		if($row[csf("road_no")]!="") $com_address.=$row[csf("road_no")].", ";
		if($row[csf("block_no")]!="") $com_address.=$row[csf("block_no")].", ";
		if($row[csf("city")]!="") $com_address.=$row[csf("city")].", ";
		if($row[csf("zip_code")]!="") $com_address.=$row[csf("zip_code")].", ";
	}
	$com_address=chop($com_address," , ");

	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$supplier_arr=return_library_array( "select id, supplier_name from  lib_supplier",'id','supplier_name');
	$yarncount=return_library_array( "select id, yarn_count from  lib_yarn_count",'id','yarn_count');
	$supplier_brand=return_library_array("select id,brand_name from lib_brand", "id","brand_name");
	$non_order_buyer=return_library_array("select id, buyer_id from wo_non_ord_samp_booking_mst where booking_type=4 and status_active=1 and is_deleted=0","id","buyer_id");
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');

	$process_loss_method_variable	=sql_select("select process_loss_method from variable_order_tracking where company_name=$company and variable_list=18 and item_category_id=2 and status_active =1");
	$process_loss_method = ($process_loss_method_variable[0][csf("process_loss_method")] ==2) ? 2: 1;

	$batch_sql=sql_select("SELECT a.id, a.batch_no, a.booking_no_id, a.booking_no, a.booking_without_order, b.prod_id, b.batch_qnty,b.body_part_id, b.program_no, c.gsm, c.detarmination_id, b.po_id, b.color_type 
	from pro_batch_create_mst a, pro_batch_create_dtls b, product_details_master c 
	where a.id=b.mst_id and b.prod_id=c.id and a.id in($batch_ids) 
	group by a.id, a.batch_no, a.booking_no_id, a.booking_no, a.booking_without_order, b.prod_id, b.batch_qnty,b.body_part_id, b.program_no, c.gsm, c.detarmination_id, b.po_id, b.color_type order by b.prod_id ASC");
	$batch_details=array();
	foreach($batch_sql as $row)
	{
		$batch_details[$row[csf("id")]]["batch_no"]=$row[csf("batch_no")];
		$batch_details[$row[csf("id")]]["booking_no"]=$row[csf("booking_no")];
		$batch_details[$row[csf("id")]]["booking_no_id"]=$row[csf("booking_no_id")];
		$batch_details[$row[csf("id")]]["booking_without_order"]=$row[csf("booking_without_order")];
		$batch_details[$row[csf("id")]]["prod_id"]=$row[csf("prod_id")];
		$batch_details[$row[csf("id")]]["batch_qnty"]+=$row[csf("batch_qnty")];
		$batch_details[$row[csf("id")]]["body_part_id"]=$row[csf("body_part_id")];
		$batch_details[$row[csf("id")]]["program_no"]=$row[csf("program_no")];
		$all_booking_no_arr[$row[csf("booking_no")]] = $row[csf("booking_no")];

		$batch_color_type_with_po_arr[$row[csf("id")]][$row[csf("body_part_id")]][$row[csf("gsm")]][$row[csf("detarmination_id")]][$row[csf("po_id")]]["color_type"]=$row[csf("color_type")];
		$batch_color_type_without_po_arr[$row[csf("id")]][$row[csf("body_part_id")]][$row[csf("gsm")]][$row[csf("detarmination_id")]]["color_type"]=$row[csf("color_type")];
	}

	$all_booking_no_arr = array_filter($all_booking_no_arr);
	$all_booking_nos = "'".implode("','", $all_booking_no_arr)."'";
	$all_booking_no_cond=""; $bookCond="";
	$all_booking_no_cond_2=""; $bookCond_2="";
	if($db_type==2 && count($all_booking_no_arr)>999)
	{
		$all_booking_no_arr_chunk=array_chunk($all_booking_no_arr,999) ;
		foreach($all_booking_no_arr_chunk as $chunk_arr)
		{
			$chunk_arr_value=implode(",",$chunk_arr);
			$bookCond.="  a.booking_no in($chunk_arr_value) or ";
			$bookCond_2.="  p.booking_no in($chunk_arr_value) or ";
		}

		$all_booking_no_cond.=" and (".chop($bookCond,'or ').")";
		$all_booking_no_cond_2.=" and (".chop($bookCond_2,'or ').")";
	}
	else
	{
		$all_booking_no_cond=" and a.booking_no in($all_booking_nos)";
		$all_booking_no_cond_2=" and p.booking_no in($all_booking_nos)";
	}

	if(!empty($order_ids))
	{
		$job_data = sql_select("SELECT c.style_owner from order_wise_pro_details a,wo_po_break_down b,wo_po_details_master c where a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and a.entry_form=7 and a.status_active=1 and a.is_deleted=0 and a.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.po_breakdown_id in($order_ids) ");
		//echo $sql;
	}


	if($fin_prod_type==1)
	{
		$yarn_lot_data=sql_select("SELECT  a.brand_id, b.po_breakdown_id, a.prod_id, a.yarn_lot as yarn_lot, a.yarn_count, a.stitch_length as stitch_length,a.machine_no_id as machine_no_id
			from pro_grey_prod_entry_dtls a, order_wise_pro_details b
			where a.id=b.dtls_id and b.entry_form in(2) and b.po_breakdown_id in($order_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");

		foreach($yarn_lot_data as $rows)
		{
			$knit_production_data[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['lot'].=$rows[csf('yarn_lot')].",";
			$knit_production_data[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['yarn_count'].=$rows[csf('yarn_count')].",";
			$knit_production_data[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['stitch_length'].=$rows[csf('stitch_length')].",";
			$knit_production_data[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['brand_id'].=$rows[csf('brand_id')].",";
			$knit_production_data[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['machine_no_id'].=$rows[csf('machine_no_id')].",";
		}
	}
	else
	{
		$yarn_lot_data=sql_select("SELECT  p.booking_no, a.brand_id, a.prod_id, a.yarn_lot, a.yarn_count, a.stitch_length,a.machine_no_id
			from inv_receive_master p, pro_grey_prod_entry_dtls a
			where p.id=a.mst_id and p.booking_without_order=1 and p.entry_form in(2) and a.status_active=1 and a.is_deleted=0 and p.status_active=1 and p.is_deleted=0 $all_booking_no_cond_2");
		foreach($yarn_lot_data as $rows)
		{
			$knit_production_data[$rows[csf('booking_no')]][$rows[csf('prod_id')]]['yarn_count']=$rows[csf('yarn_count')];
			$knit_production_data[$rows[csf('booking_no')]][$rows[csf('prod_id')]]['stitch_length']=$rows[csf('stitch_length')];
			$knit_production_data[$rows[csf('booking_no')]][$rows[csf('prod_id')]]['lot']=$rows[csf('yarn_lot')];
			$knit_production_data[$rows[csf('booking_no')]][$rows[csf('prod_id')]]['machine_no_id']=$rows[csf('machine_no_id')];
			$knit_production_data[$rows[csf('booking_no')]][$rows[csf('prod_id')]]['brand_id']=$rows[csf('brand_id')];
		}
	}


	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 order by b.id asc";
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
		$sql_update=sql_select("SELECT a.DELIVERY_COMPANY,a.delivery_location,b.id,b.grey_sys_id,b.product_id,b.job_no,b.order_id,b.current_delivery,
		b.roll,b.program_no,b.sys_dtls_id FROM pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b WHERE a.id=b.mst_id and b.mst_id = $update_mst_id AND b.entry_form = 54");
		// $sql_update=sql_select("select id,grey_sys_id,product_id,job_no,order_id,current_delivery,roll,program_no,sys_dtls_id from pro_grey_prod_delivery_dtls where mst_id=$update_mst_id and entry_form=54");
		$all_sys_dtls_id="";
		foreach($sql_update as $row)
		{
			$all_sys_dtls_id.=$row[csf("sys_dtls_id")].",";
			if($fin_prod_type==1)
			{
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("order_id")]."*".$row[csf("sys_dtls_id")]]["current_delivery"] =$row[csf("current_delivery")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("order_id")]."*".$row[csf("sys_dtls_id")]]["id"] =$row[csf("id")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("order_id")]."*".$row[csf("sys_dtls_id")]]["roll"] =$row[csf("roll")];
			}
			else
			{
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("sys_dtls_id")]]["current_delivery"] =$row[csf("current_delivery")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("sys_dtls_id")]]["id"] =$row[csf("id")];
				$update_row_check[$row[csf("grey_sys_id")]."*".$row[csf("product_id")]."*".$row[csf("sys_dtls_id")]]["roll"] =$row[csf("roll")];
			}
		}
	}

	$all_sys_dtls_id=implode(",",array_unique(explode(",",chop($all_sys_dtls_id,","))));
	if($all_sys_dtls_id=="") $all_sys_dtls_id=0;

	if($fin_prod_type==1)
	{
		$fin_pord_sql=sql_select("select b.id, o.grey_used_qty, b.remarks,o.po_breakdown_id from inv_receive_master a, pro_finish_fabric_rcv_dtls b,order_wise_pro_details o where a.id=b.mst_id and a.entry_form=7 and b.id=o.dtls_id and o.entry_form=7 and b.id in($all_sys_dtls_id)");
		$fin_pord_data=array();
		$$grey_userd_data_arr=array();
		foreach($fin_pord_sql as $row)
		{
			$grey_userd_data_arr[$row[csf("id")]][$row[csf("po_breakdown_id")]]["grey_used_qty"]=$row[csf("grey_used_qty")];
			$fin_pord_data[$row[csf("id")]]["remarks"]=$row[csf("remarks")];
		}
	}else{
		$fin_pord_sql=sql_select("select b.id, b.grey_used_qty, b.remarks from inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form=7 and b.id in($all_sys_dtls_id)");
		$fin_pord_data=array();
		foreach($fin_pord_sql as $row)
		{
			$fin_pord_data[$row[csf("id")]]["grey_used_qty"]=$row[csf("grey_used_qty")];
			$fin_pord_data[$row[csf("id")]]["remarks"]=$row[csf("remarks")];
		}
	}


	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$company'","image_location");

	?>
	<div style="width:1300px;">
		<table width="1300" cellspacing="0" align="center" border="0">
			<tr>
				<td colspan="1" rowspan="3">
					<img src="../<? echo $image_location; ?>" height="70" width="200" style="float:left; margin-left:10px;">
				</td>
				<td colspan="15" align="center" style="font-size:x-large"><strong>LC Company : <? echo $company_name; ?></strong>
				</td>
			</tr>
			<tr>
				<td colspan="16" align="center" style="font-size:18px"><strong>LC Company Location : <? echo $com_address; ?></strong></td>
			</tr>
			<tr>
				<td colspan="16" align="center" style="font-size:18px"><strong>Style Owner : <? echo $company_arr[$job_data[0][csf('style_owner')]]; ?></strong></td>
			</tr>
			<tr>
				<td colspan="16" align="center" style="font-size:18px;padding-left: 270px;"><strong><u>Delivery Challan, Finishing Section.</u></strong></td>
			</tr>
			<tr>
				<td colspan="16" align="center" style="font-size:18px;padding-left: 270px;"><strong><u>Delivery From :  <? echo $location_arr[$dye_location]; ?></u></strong></td>
			</tr>
			<tr>
				<td id="barcode_img_id"></td>
			</tr>
		</table>
		<br>
		<table width="1000" cellspacing="0" align="left" border="0">
			<tr>
				<td   style="font-size:16px; font-weight:bold;" width="110">Delivery To</td>
				<td >:<? echo  $company_arr[$deli_company]; ?></td>
				<td   style="font-size:16px; font-weight:bold;" width="110">Delivery Location</td>
				<td >:<? echo $location_arr[$deli_location]; ?></td>
			</tr>
			<tr>
				<td   style="font-size:16px; font-weight:bold;" width="110">Challan No</td>
				<td >:<? echo $Challan_no; ?></td>
				<td style="font-size:16px; font-weight:bold;" width="150">Delivery Date</td>
				<td  >:<? echo $delivery_date; ?></td>
			</tr>
			<tr>
				<td style="font-size:16px; font-weight:bold;" width="150">Remarks:</td>
				<td colspan="3" >:<? echo $txt_remark; ?></td>
			</tr>
		</table>
	</div>
	<div style="width:2120px">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2120" class="rpt_table" >
			<thead>
				<tr>
					<th width="30" >SL</th>
					<th width="110">Order No</th>
                    <th width="110">Internal Ref</th>
					<th width="60"> Order Qty [Pcs]</th>
					<th width="100">Buyer <br> & Job</th>
					<th width="100">Style No</th>
					<th width="40">System ID</th>
					<th width="100">Batch No</th>
					<th width="80">Fabric Shade</th>
					<th width="100">Booking No</th>
					<th width="80">Yarn Lot</th>
					<th width="110">Fin. Prod.  Company</th>
					<th width="80">Color</th>
					<th width="100">Color Type</th>
					<th width="100">Body Part</th>
					<th width="100">Process Name</th>
					<th width="60">Dia Type</th>
					<th width="200">Fabric Type</th>
					<th width="50">Fin GSM</th>
					<th width="40">Fin. Dia</th>
					<th width="100">Grey Dia</th>
					<th width="40">T. Roll</th>
					<th width="70">Grey Used</th>
					<th width="70">Delivery Qty</th>					
					<?
					if($operation==4)
					{
						?>
						<th width="70">Process Loss%</th>
						<th width="80">Remarks</th>
					<? } ?>
				</tr>
			</thead>
			<tbody>
				<?
				if($db_type==0)
				{
					if($from_date!="" && $to_date!="") $date_con="and a.receive_date between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'"; else $date_con="";
					$select_year="year(e.insert_date) as job_year";
				}
				else if($db_type==2)
				{
					if($from_date!="" && $to_date!="") $date_con="and a.receive_date between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'"; else $date_con="";
					$select_year="to_char(e.insert_date,'YYYY') as job_year";
				}

				if($location!=0) $location_con="and a.location_id=$location"; else $location_con="";
				if($buyer!=0) $buyer_con="and a.buyer_id=$buyer"; else $buyer_con="";

				if($order_ids!="")
				{
					$sql_machineDia=sql_select("select a.febric_description_id, b.po_breakdown_id, a.machine_dia
						from inv_receive_master p, pro_grey_prod_entry_dtls a, order_wise_pro_details b
						where p.id=a.mst_id and a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and p.status_active=1 and p.is_deleted=0 and b.po_breakdown_id in ($order_ids) and p.entry_form in(2,22) and a.machine_dia is not NULL group by a.febric_description_id, b.po_breakdown_id, a.machine_dia");
				}
				else
				{
					$sql_machineDia=sql_select("select p.booking_no, a.febric_description_id, a.machine_dia
						from inv_receive_master p, pro_grey_prod_entry_dtls a
						where p.id=a.mst_id  and a.status_active=1 and a.is_deleted=0 and p.status_active=1 and p.is_deleted=0 and p.booking_without_order=1 and a.machine_dia is not NULL $all_booking_no_cond_2 group by p.booking_no, a.febric_description_id, a.machine_dia");
				}

				$mc_dia_arr=array();
				foreach($sql_machineDia as $rows)
				{
					if($order_ids!="")
						$mc_dia_arr[$rows[csf('po_breakdown_id')]][$rows[csf('febric_description_id')]].=$rows[csf('machine_dia')].',';
					else
						$mc_dia_arr[$rows[csf('booking_no')]][$rows[csf('febric_description_id')]].=$rows[csf('machine_dia')].',';
				}
				unset($sql_machineDia);

				if($fin_prod_type==1)
				{
					$sql="SELECT a.id, a.recv_number_prefix_num, a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.id as dtls_id, b.fabric_description_id as determination_id, b.fabric_shade, b.gsm as gsm, b.width as dia, b.prod_id, b.batch_id as batch_id, b.color_id as color_id, b.body_part_id, b.process_id, b.dia_width_type, c.po_breakdown_id, c.process_loss_perc, sum(c.quantity) as quantity, d.po_number, e.style_ref_no, d.grouping, e.job_no, e.job_no_prefix_num, e.buyer_name, sum(d.po_quantity*e.total_set_qnty) as po_qty, $select_year 
					from inv_receive_master a,  pro_finish_fabric_rcv_dtls b ,order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e 
					where a.id=b.mst_id  and b.id=c.dtls_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and a.entry_form=7 and c.entry_form=7 and c.status_active=1 and c.is_deleted=0 and c.trans_type=1 and a.status_active=1 and a.is_deleted=0 and a.company_id=$company and a.id in ($program_ids) and b.prod_id in ($product_ids) and c.po_breakdown_id in($order_ids) $date_con $location_con 
					group by b.batch_id,b.id,a.id,a.recv_number_prefix_num,a.recv_number,a.receive_date,a.receive_basis,a.knitting_source,a.knitting_company,b.fabric_description_id,b.fabric_shade,b.gsm,b.width,b.prod_id,b.color_id,b.body_part_id,b.process_id,b.dia_width_type,c.po_breakdown_id, c.process_loss_perc, d.po_number, e.style_ref_no, d.grouping,e.job_no,e.job_no_prefix_num,e.buyer_name,e.insert_date order by b.batch_id"; 
				}
				else
				{
					$sql="SELECT a.id, a.recv_number_prefix_num, a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.id as dtls_id, b.fabric_description_id as determination_id, b.fabric_shade, b.gsm as gsm, b.width as dia, b.prod_id, b.batch_id as batch_id, b.color_id as color_id, b.body_part_id, b.process_id, b.dia_width_type, 0 as process_loss_perc, sum(b.receive_qnty) as quantity, '' as po_breakdown_id, '' as po_number, '' as job_no, '' as job_no_prefix_num, '' as buyer_name, '' as job_year, '' as style_ref_no 
					from inv_receive_master a,  pro_finish_fabric_rcv_dtls b 
					where a.id=b.mst_id and a.entry_form=7 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_chack_cond and a.company_id=$company and a.id in ($program_ids) and b.prod_id in ($product_ids) $date_con $location_con 
					group by b.batch_id, b.id,a.id, a.recv_number_prefix_num,a.recv_number,a.receive_date,a.receive_basis,a.knitting_source,a.knitting_company,b.fabric_description_id,b.fabric_shade,b.gsm,b.width,b.prod_id,b.color_id,b.body_part_id,b.process_id,b.dia_width_type order by b.batch_id"; 
				}

				$nameArray=sql_select( $sql);
				$batch_id_arr  = array();
				foreach ($nameArray as $row) {
					array_push($batch_id_arr,$row[csf('batch_id')]);
				}

				/*$color_type_sql=sql_select("select job_no,color_type_id,body_part_id,lib_yarn_count_deter_id from wo_pre_cost_fabric_cost_dtls where is_deleted=0 and status_active=1");
				$arr_color_type=array();
				foreach($color_type_sql as $row)
				{
					$arr_color_type[$row[csf("job_no")]][$row[csf("body_part_id")]][$row[csf("lib_yarn_count_deter_id")]]["color_type_id"]=$row[csf("color_type_id")];
				}*/

				$booking_without_order = sql_select("select a.booking_no_prefix_num, a.buyer_id,b.body_part,b.booking_no,b.color_type_id,b.fabric_color from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where  a.booking_no=b.booking_no and a.company_id=$company and a.booking_type=4 $all_booking_no_cond");
				foreach ($booking_without_order as $row)
				{
					$color_type_array[$row[csf('booking_no')]][$row[csf('body_part')]][$row[csf('fabric_color')]]['color_type_id'] = $row[csf('color_type_id')];

				}

				$color_sqls = "select  b.booking_no,c.color_type_id,c.body_part_id,b.fabric_color_id from wo_booking_mst a, wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.company_id=$company and a.booking_type=1 and b.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $all_booking_no_cond group by b.booking_no,c.color_type_id,c.body_part_id,b.fabric_color_id";
				$color_sql_result = sql_select($color_sqls);
				foreach ($color_sql_result as $keys=>$row2)
				{
					$color_type_array[$row2[csf('booking_no')]][$row2[csf('body_part_id')]][$row2[csf('fabric_color_id')]]['color_type_id'] = $row2[csf('color_type_id')];
				}

				$yarn_sql = "SELECT e.booking_id, b.prod_id, d.yarn_lot, d.yarn_count, d.brand_id
				from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,  inv_receive_master e
				where a.id=b.mst_id and b.roll_id=c.id and c.dtls_id=d.id and d.mst_id=e.id and a.company_id=$company ".where_con_using_array($batch_id_arr,0,'a.id')." and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22)
				group by e.booking_id, b.prod_id, d.yarn_lot, d.yarn_count, d.brand_id";
				//echo $yarn_sql;
				$sql_data_result = sql_select($yarn_sql);
				$yarn_data_arr=array();
				foreach ($sql_data_result as $row)
				{
					$yarn_data_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['yarn_lot'].=$row[csf('yarn_lot')].',';
				}
				//echo "<pre>";print_r($yarn_data_arr);

				//echo $sql;
				$i=1; $tot_roll=0; $tot_qty=0;
				foreach($nameArray as $row)
				{
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					if($fin_prod_type==1)
					{
						$index_pk=$row[csf("id")]."*".$row[csf("prod_id")]."*".$row[csf("po_breakdown_id")]."*".$row[csf("dtls_id")];
					}
					else
					{
						$index_pk=$row[csf("id")]."*".$row[csf("prod_id")]."*".$row[csf("dtls_id")];
					}

					$process_all=array_unique(explode(",",$row[csf('process_id')]));
					$process_name='';
					$process_id_array=explode(",",$batch_process);
					foreach($process_all as $val)
					{
						if($process_name=="") $process_name=$conversion_cost_head_array[$val]; else $process_name.=",".$conversion_cost_head_array[$val];
					}

					$yarn_lot_all=$yarn_data_arr[$batch_details[$row[csf("batch_id")]]["program_no"]][$batch_details[$row[csf("batch_id")]]["prod_id"]]['yarn_lot'];
					$yarn_lot = implode(",", array_unique(explode(",", chop($yarn_lot_all,','))));

					$supplier_brand_value="";$yarn_count_value="";

					if($fin_prod_type==1)
					{
						// echo $row[csf('po_breakdown_id')].'='.$batch_details[$row[csf("batch_id")]]["prod_id"].'='.$batch_details[$row[csf("batch_id")]]["program_no"];
						$y_count=array_unique(explode(',',chop($knit_production_data[$batch_details[$row[csf("batch_id")]]["prod_id"]][$row[csf('po_breakdown_id')]]['yarn_count'],",")));
						foreach($y_count as $count_id)
						{
							if($count_id>0)
							{
								if($yarn_count_value=='') $yarn_count_value=$yarncount[$count_id]; else $yarn_count_value.=", ".$yarncount[$count_id];
							}
						}

						$supplier_brand_arr=array_unique(explode(',',chop($knit_production_data[$batch_details[$row[csf("batch_id")]]["prod_id"]][$row[csf('po_breakdown_id')]]['brand_id'],",")));
						foreach($supplier_brand_arr as $brand_id)
						{
							if($brand_id>0)
							{
								if($supplier_brand_value=='') $supplier_brand_value=$supplier_brand[$brand_id]; else $supplier_brand_value.=", ".$supplier_brand[$brand_id];
							}
						}
						//echo $y_lot_arr = $batch_details[$row[csf("batch_id")]]["prod_id"];
						// $y_lot_arr=array_unique(explode(',',chop($knit_production_data[$batch_details[$row[csf("batch_id")]]["prod_id"]][$row[csf('po_breakdown_id')]]['lot'],",")));
					}
					else
					{
						$y_count=array_unique(explode(',',chop($knit_production_data[$batch_details[$row[csf("batch_id")]]["booking_no"]][$batch_details[$row[csf("batch_id")]]["brand_id"]]['yarn_count'],",")));
						foreach($y_count as $count_id)
						{
							if($count_id>0)
							{
								if($yarn_count_value=='') $yarn_count_value=$yarncount[$count_id]; else $yarn_count_value.=", ".$yarncount[$count_id];
							}
						}

						$supplier_brand=array_unique(explode(',',chop($knit_production_data[$batch_details[$row[csf("batch_id")]]["booking_no"]][$batch_details[$row[csf("batch_id")]]["brand_id"]]['brand_id'],",")));

						foreach($supplier_brand as $brand_id)
						{
							if($brand_id>0)
							{
								if($supplier_brand_value=='') $supplier_brand_value=$supplier_brand[$brand_id]; else $supplier_brand_value.=", ".$supplier_brand[$brand_id];
							}
						}
					}

					$ms_dia="";
					if($row[csf('po_breakdown_id')]!="") $ms_dia=$mc_dia_arr[$row[csf('po_breakdown_id')]][$row[csf('determination_id')]];
					else $ms_dia=$mc_dia_arr[$batch_details[$row[csf("batch_id")]]['booking_no']][$row[csf('determination_id')]];

					$all_machineDias=chop($ms_dia,',');
					$all_machineDias=implode(",",array_unique(explode(",",$all_machineDias)));

					if($update_row_check[$index_pk]["current_delivery"]>0)
					{
						//$internal_ref_no=return_field_value("a.po_number,b.internal_ref","wo_po_break_down a,  wo_order_entry_internal_ref b","a.job_no_mst=b.job_no and a.po_number='".$row[csf('po_number')]."'","internal_ref");
						$internal_ref_no=$row[csf('grouping')];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td width="30"  align="center"><div style="word-wrap:break-word; width:30px"><? echo $i; ?></div></td>
							<td width="110"><div style="word-wrap:break-word; width:110px"><? echo $row[csf('po_number')];?></div></td>
                            <td width="110"><div style="word-wrap:break-word; width:110px; text-align:center"><? echo $internal_ref_no;?>&nbsp;</div></td>
							<td width="60" align="right"><p><? echo $row[csf('po_qty')];?></p></td>
							<td width="100"><div style="word-wrap:break-word; width:100px">
								<?
								if($batch_details[$row[csf('batch_id')]]['booking_without_order']==1)
								{
									echo $buyer_array[$non_order_buyer[$batch_details[$row[csf("batch_id")]]['booking_no_id']]];
								}
								else
								{
									echo $buyer_array[$row[csf("buyer_name")]];
								}
								echo "<br>";
								if($row[csf('job_no_prefix_num')]!="")
								{
									echo "Job-".$row[csf('job_no_prefix_num')];
								}
								?></div>
							</td>

							<td width="100" align="center"><div style="word-wrap:break-word; width:40px"><? echo $row[csf('style_ref_no')]; ?></div></td>
							<td width="40" align="center"><div style="word-wrap:break-word; width:40px"><? echo $row[csf('recv_number_prefix_num')]; ?></div></td>
							<td width="100" align="center"><div style="word-wrap:break-word; width:50px"><? echo $batch_details[$row[csf('batch_id')]]['batch_no'];?></div></td>
							<td width="80" align="center"><div style="word-wrap:break-word; width:80px"><? echo $fabric_shade[$row[csf('fabric_shade')]]; ?></div></td>
							<td align="center" width="100"><div style="word-wrap:break-word; width:100px"><? echo $batch_details[$row[csf("batch_id")]]['booking_no']; ?></div></td>
							<td width="80" align='center'><div style="word-wrap:break-word; width:200px">
								<?
								if($yarn_lot!="") echo $yarn_lot;
								?></div></td>
							<td width="110"><div style="word-wrap:break-word; width:110px">
								<?
								if($row[csf("knitting_source")]==1)  echo $company_arr[$row[csf("knitting_company")]];
								else if($row[csf("knitting_source")]==3) echo $supplier_arr[$row[csf("knitting_company")]];
								?>
							</div></td>
							<td width="80"><div style="word-wrap:break-word; width:80px"><? echo $color_arr[$row[csf('color_id')]];?></div></td>

							<td width="80">
								<div style="word-wrap:break-word; width:80px">
								<?
								$color_type_id="";
								if($row[csf('po_breakdown_id')])
								{
										$color_type_id= $batch_color_type_with_po_arr[$row[csf("batch_id")]][$row[csf("body_part_id")]][$row[csf('gsm')]][$row[csf('determination_id')]][$row[csf('po_breakdown_id')]]["color_type"];
								}
								else
								{
									$color_type_id= $batch_color_type_without_po_arr[$row[csf("batch_id")]][$row[csf("body_part_id")]][$row[csf('gsm')]][$row[csf('determination_id')]]["color_type"];
								}

								if($color_type_id*1 == 0)
								{
									$color_type_id=$color_type_array[$batch_details[$row[csf("batch_id")]]['booking_no']][$row[csf("body_part_id")]][$row[csf("color_id")]]['color_type_id'];
								}
								echo $color_type[$color_type_id];
								?></div>
							</td>

							<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $body_part[$row[csf("body_part_id")]];?></div></td>

							<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $process_name;?></div></td>
							<td width="60"><div style="word-wrap:break-word; width:60px"><? echo $fabric_typee[$row[csf('dia_width_type')]];?></div></td>
							<td width="200"><div style="word-wrap:break-word; width:200px">
								<?
								
								echo $composition_arr[$row[csf('determination_id')]]."<br>";;
								
								//if($yarn_count_value!="") echo $yarn_count_value;
								//if($supplier_brand_value!="") echo ", ".$supplier_brand_value;
								?></div>
							</td>
							<td width="50" align="center"><div style="word-wrap:break-word; width:50px"><?  echo $row[csf('gsm')]; ?></div></td>
							<td width="40" align="center"><div style="word-wrap:break-word; width:40px"><? echo $row[csf('dia')];?></div></td>

							<td width="100" align="center"><div style="word-wrap:break-word; width:100px"><? echo $all_machineDias; //echo $arr_machineDia[$row[csf('po_breakdown_id')]]['machine_dia'];?></div></td>


							<td width="40" align="center"><div style="word-wrap:break-word; width:40px"><? echo $update_row_check[$index_pk]["roll"]; $tot_roll+=$update_row_check[$index_pk]["roll"]; ?></div></td>
							<td  align="right" width="70">
								<div style="word-wrap:break-word; width:70px">
								<?
								/* if($fin_prod_type==1)
								{
								echo number_format($grey_userd_data_arr[$row[csf("dtls_id")]][$row[csf("po_breakdown_id")]]["grey_used_qty"],2);
									$tot_grey_used+=$grey_userd_data_arr[$row[csf("dtls_id")]][$row[csf("po_breakdown_id")]]["grey_used_qty"];
								}
								else
								{

								  echo number_format($fin_pord_data[$row[csf("dtls_id")]]["grey_used_qty"],2);
								  $tot_grey_used+=$fin_pord_data[$row[csf("dtls_id")]]["grey_used_qty"];
								} */

								if($process_loss_method==1)
								{
									$grey_used_quanity = $update_row_check[$index_pk]["current_delivery"] + ($row[csf("process_loss_perc")]*$update_row_check[$index_pk]["current_delivery"])/100;
								}
								else
								{
									$grey_used_quanity = $update_row_check[$index_pk]["current_delivery"] / ( 1- $row[csf("process_loss_perc")]/100);
								}

								//$grey_used_quanity = $update_row_check[$index_pk]["current_delivery"] + ($update_row_check[$index_pk]["current_delivery"]* $row[csf("process_loss_perc")])/100;

								$tot_grey_used+= $grey_used_quanity;

								echo number_format($grey_used_quanity,2);
								?>
								</div>
							</td>
							<td  align="right" width="70"><div style="word-wrap:break-word; width:70px"><? echo number_format($update_row_check[$index_pk]["current_delivery"],2); $tot_qty+=$update_row_check[$index_pk]["current_delivery"]; ?></div></td>
							<?
							if($operation==4)
							{
								?>
								<td align="right" width="70"><div style="word-wrap:break-word; width:70px"><? echo $row[csf("process_loss_perc")]; ?></div></td>
								<td width="80"><div style="word-wrap:break-word; width:80px;"><? echo $fin_pord_data[$row[csf("dtls_id")]]["remarks"] ?></div></td>
							<? } ?>
						</tr>
						<?
						$i++;
					}
					$batch_dia_type="";
				}
				?>
				<tr>
					<?
					if($operation==4){$colspan=19;}else{$colspan=17;}
					?>
					<td align="right" colspan="21" ><strong>Total:</strong></td>
					<td align="center"><? echo $tot_roll; ?></td>
					<td align="right" ><? echo number_format($tot_grey_used,2,'.',''); ?></td>
					<td align="right" ><? echo number_format($tot_qty,2,'.',''); ?></td>
					<td align="right" ></td>
					<?
					if($operation==4)
					{
						?>
						<td ></td>
						<td ></td>
					<? } ?>
				</tr>
				<? if($operation==5){?>
				<tr>
					<!-- <td colspan="21"> <b>  Remarks : </b> <?// echo// $txt_remarks;?></td> -->
				</tr>
				<?}?>
			</tbody>
		</table>
		<br>
		<?
		echo signature_table(68, $company, "1600px",$cbo_template_id);
		?>

		<script type="text/javascript" src="../includes/functions.js"></script>
		<script type="text/javascript" src="../js/jquery.js"></script>
		<script type="text/javascript" src="../js/jquerybarcode.js"></script>
		<script>
			fnc_generate_Barcode("<? echo $Challan_no;?>","barcode_img_id");
		</script>
	</div>
	<?
	exit();
}

?>
