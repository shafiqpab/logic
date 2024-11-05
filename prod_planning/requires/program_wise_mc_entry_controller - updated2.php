<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

require_once('../../includes/common.php');
$user_name = $_SESSION['logic_erp']['user_id'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
$permission = $_SESSION['page_permission'];

// get buyer condition according to priviledge
if ($_SESSION['logic_erp']["data_level_secured"] == 1)
{
	if ($_SESSION['logic_erp']["buyer_id"] != "")
	{
		$buyer_id_cond = " and buy.id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
	}
	else
	{
		$buyer_id_cond = "";
	}
}
else
{
	$buyer_id_cond = "";
}



if ($action == "load_drop_down_buyer")
{
	$data = explode("_", $data);
	if ($data[1] == 1)
	{
		echo create_drop_down("cbo_buyer_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Buyer --", "0", "", 0);
	}
	else if ($data[1] == 2)
	{
		echo create_drop_down("cbo_buyer_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='" . $data[0] . "' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_id_cond group by buy.id,buy.buyer_name order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "", 0);
	}
	else
	{
		echo create_drop_down("cbo_buyer_name", 140, $blank_array, "", 1, "-- Select Buyer --", 0, "");
	}
	exit();
}



	
if ($action == "booking_item_details") 
{

	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$company_name = str_replace("'", "", $cbo_company_name);
	$within_group = str_replace("'", "", $cbo_within_group);
	$buyer_name = str_replace("'", "", $cbo_buyer_name);
	$planning_status = str_replace("'", "", $cbo_planning_status);
	$barcode = str_replace("'", "", trim($txt_barcode));

	$job_no_cond = "";
	$booking_cond = "";
	if (str_replace("'", "", $hide_job_id) != "")
	{
		$job_no_cond = "and a.id in(" . str_replace("'", "", $hide_job_id) . ")";
		$ppl_job_no_cond = "and c.po_id in(" . str_replace("'", "", $hide_job_id) . ")";
	}
	
	$txt_booking = "%" . str_replace("'", "", trim($txt_booking_no)) . "%";
	if (str_replace("'", "", trim($txt_booking_no)) != "")
	{
		$booking_cond = "and a.sales_booking_no like '$txt_booking'";
		$ppl_booking_cond = "and a.booking_no like '$txt_booking'";
	}
	//internal ref 
	$txt_internalref = "%" . str_replace("'", "", trim($txt_internal_ref)) . "%";
	if (str_replace("'", "", trim($txt_internal_ref)) != "")
	{
			//for internal ref.
			$internalRef_cond = '';$booking_nos_internal_ref_cond = '';
			$internalRef_cond = " and a.grouping like '$txt_internalref'";
			$sql_bookings=sql_select("select b.booking_no from wo_po_break_down a,wo_booking_dtls b where a.job_no_mst=b.job_no and a.id=b.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $internalRef_cond");
			$booking_nos="";$bookingArrChk=array();
			foreach ($sql_bookings as $row) {
				if($bookingArrChk[$row[csf('booking_no')]]!=$row[csf('booking_no')])
				{
					$booking_nos.="'".$row[csf('booking_no')]."',";
					$bookingArrChk[$row[csf('booking_no')]]=$row[csf('booking_no')];
				}
			}
			$booking_nos=chop($booking_nos,",");
			$booking_nos_internal_ref_cond = "and a.sales_booking_no in($booking_nos)";
			unset($sql_bookings);
	}
	//echo $booking_nos_internal_ref_cond;
	
	if ($within_group == 0)
		$within_group_cond = "";
	else
		$within_group_cond = " and a.within_group=$within_group";
	
	if ($within_group == 1)
	{
		if ($buyer_name == 0)
			$buyer_id_cond_to = "";
		else
			$buyer_id_cond_to = " and a.buyer_id=$buyer_name";
	}

	if ($barcode != "")
	{
		$barcode_cond = "and b.barcode_no in($barcode)";
	}
	
	$date_cond = '';
	$date_from = str_replace("'", "", trim($txt_date_from));
	$date_to = str_replace("'", "", trim($txt_date_to));
	if ($date_from != "" && $date_to != "")
	{
		if ($db_type == 0)
		{
			$date_cond = "and a.insert_date between '" . change_date_format(trim($date_from), "yyyy-mm-dd", "-") . "' and '" . change_date_format(trim($date_to), "yyyy-mm-dd", "-") . "'";
		}
		else
		{
			$date_cond = "and a.insert_date between '" . change_date_format(trim($date_from), '', '', 1) . "' and '" . change_date_format(trim($date_to), '', '', 1) . "'";
		}
	}

	$machineLibArr = return_library_array("select id, machine_no from lib_machine_name where category_id=1 and status_active=1 and is_deleted=0 order by seq_no", 'id', 'machine_no');
	$company_arr = return_library_array("select id,company_short_name from lib_company", 'id', 'company_short_name');
	$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");
	$print_report_format =return_field_value("format_id"," lib_report_template","template_name ='".$company_name."' and module_id=4 and report_id=88 and is_deleted=0 and status_active=1");
	$print_report_format_arr = explode(",",$print_report_format);

	if( $print_report_format_arr[0]!="" )
	{
		if( $print_report_format_arr[0]==272 )
		{
			$program_info_format_id = 272;

		}
		else if($print_report_format_arr[0]==273)
		{
			$program_info_format_id = 273;
		}
	}
	else
	{
		$program_info_format_id = 272;
	}
	?>
	<script>
		function openpage_machine2(dataStr) {
		
			

			var datas=dataStr.split('_');

			var slrow= datas[0];
			var planId= datas[1];
			var update_dtls_id= datas[2];
			var companyID= datas[3];
			var cbo_knitting_party= datas[4];
			var txt_machine_gg= datas[5];
			var txt_machine_dia= datas[6];
			var txt_program_qnty= datas[7];

			var save_string = $('#save_data_'+slrow).val();
			var updated_id = $('#update_id_'+slrow).val();

			var page_link = 'requires/program_wise_mc_entry_controller.php?action=machine_info_popup&save_string=' + save_string + '&planId='+planId + '&update_dtls_id=' + update_dtls_id + '&companyID='+companyID + '&cbo_knitting_party='+cbo_knitting_party + '&txt_machine_gg='+txt_machine_gg  + '&txt_machine_dia=' + txt_machine_dia  + '&txt_program_qnty='+txt_program_qnty+ '&updated_id='+updated_id ;
			var title = 'Machine Info';

			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=840px,height=360px,center=1,resize=1,scrolling=0', '../');
			emailwindow.onclose = function () {
				var theform = this.contentDoc.forms[0];
				var hidden_machine_no = this.contentDoc.getElementById("hidden_machine_no").value;
				var hidden_machine_id = this.contentDoc.getElementById("hidden_machine_id").value;
				var save_string = this.contentDoc.getElementById("save_string").value;
				var updateId = this.contentDoc.getElementById("updateId").value;
				var hidden_machine_capacity = this.contentDoc.getElementById("hidden_machine_capacity").value;
				var hidden_distribute_qnty = this.contentDoc.getElementById("hidden_distribute_qnty").value;
				var hidden_min_date = this.contentDoc.getElementById("hidden_min_date").value;
				var hidden_max_date = this.contentDoc.getElementById("hidden_max_date").value;

				$('#txt_machine_no_'+slrow).val(hidden_machine_no);
				$('#machine_id_'+slrow).val(hidden_machine_id);
				$('#save_data_'+slrow).val(save_string);
				$('#update_id_'+slrow).val(updateId);
				$('#txt_machine_capacity').val(hidden_machine_capacity);
				$('#distribution_qnty_'+slrow).val(hidden_distribute_qnty);
				$('#txt_start_date').val(hidden_min_date);
				$('#txt_end_date').val(hidden_max_date);
				days_req();
			}
		}
	</script>
	<?
	if ($type == 1)
	{
		$active_status_sql = "and b.status_active=1 and b.is_deleted=0";
		if ($db_type==0) 
		{
			$sales_order_dtls_id="group_concat(b.id) as sales_order_dtls_id";
			$po_break_down_id_cast="cast(c.po_break_down_id as char(4000)) po_break_down_id";
		}
		else
		{
			//$sales_order_dtls_id="listagg(b.id, ',') within group (order by b.id) as sales_order_dtls_id";
			//tmp solution
			$sales_order_dtls_id = "RTRIM(XMLAGG(XMLELEMENT(e,b.id,',').EXTRACT('//text()') ORDER BY b.id).GETCLOBVAL(),',') AS sales_order_dtls_id";
			$po_break_down_id_cast="cast(c.po_break_down_id as varchar2(4000)) po_break_down_id";
		}
		
		if($within_group==1)
		{ // within_group yes

			/*$sql = " SELECT a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type, sum(b.finish_qty) finish_qty, sum(b.grey_qty) grey_qty,$po_break_down_id_cast, a.is_apply_last_update,a.is_master_part_updated,$sales_order_dtls_id,b.pre_cost_fabric_cost_dtls_id, a.po_job_no from fabric_sales_order_mst a,fabric_sales_order_dtls b,wo_booking_mst c where a.id=b.mst_id and a.sales_booking_no=c.booking_no $active_status_sql and a.company_id=$company_name $booking_cond $job_no_cond $buyer_id_cond_to $within_group_cond $date_cond $barcode_cond $booking_nos_internal_ref_cond and c.fabric_source in(1,2) and a.booking_without_order=0 group by a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type,a.is_apply_last_update,a.is_master_part_updated,b.pre_cost_fabric_cost_dtls_id,c.po_break_down_id, a.po_job_no
			union all
			select a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type, sum(b.finish_qty) finish_qty, sum(b.grey_qty) grey_qty, '' as po_break_down_id,a.is_apply_last_update,a.is_master_part_updated,$sales_order_dtls_id,b.pre_cost_fabric_cost_dtls_id, a.po_job_no from fabric_sales_order_mst a,fabric_sales_order_dtls b,wo_non_ord_samp_booking_mst c,wo_non_ord_samp_booking_dtls d where a.id=b.mst_id and a.sales_booking_no=c.booking_no and c.booking_no=d.booking_no $active_status_sql and a.company_id=$company_name $booking_cond $job_no_cond $buyer_id_cond_to $within_group_cond $date_cond $barcode_cond $booking_nos_internal_ref_cond and (c.fabric_source in(1,2) or d.fabric_source in(1,2))  and a.booking_without_order=1 group by a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type,a.is_apply_last_update,a.is_master_part_updated,b.pre_cost_fabric_cost_dtls_id, a.po_job_no  order by dia";*/

			$sql = " SELECT a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type, sum(b.finish_qty) finish_qty, sum(b.grey_qty) grey_qty,$po_break_down_id_cast, a.is_apply_last_update,a.is_master_part_updated,$sales_order_dtls_id,b.pre_cost_fabric_cost_dtls_id, a.po_job_no,d.mst_id as plan_id,d.dtls_id as program_no,d.program_qnty,e.knitting_party,e.machine_gg,e.machine_dia  from fabric_sales_order_mst a,fabric_sales_order_dtls b,wo_booking_mst c, ppl_planning_entry_plan_dtls d,ppl_planning_info_entry_dtls e where a.id=b.mst_id and a.sales_booking_no=c.booking_no and c.booking_no=d.booking_no and d.po_id=a.id and d.dtls_id=e.id and d.mst_id=e.mst_id $active_status_sql and a.company_id=$company_name $booking_cond $job_no_cond $buyer_id_cond_to $within_group_cond $date_cond $barcode_cond $booking_nos_internal_ref_cond and c.fabric_source in(1,2) and a.booking_without_order=0 group by a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type,a.is_apply_last_update,a.is_master_part_updated,b.pre_cost_fabric_cost_dtls_id,c.po_break_down_id, a.po_job_no,d.mst_id,d.dtls_id,d.program_qnty,e.knitting_party,e.machine_gg,e.machine_dia 
			";
				/*$sql="select c.po_id as id,a.company_id,c.within_group,null as job_no,null as sales_booking_no ,null as booking_id, null as buyer_id, null as style_ref_no,null as booking_date,null as po_job_no, sum(c.program_qnty) as program_qnty,
				c.body_part_id,c.color_type_id,c.fabric_desc,c.determination_id,c.gsm_weight, c.dia, c.width_dia_type, c.pre_cost_fabric_cost_dtls_id, c.mst_id as plan_id,c.dtls_id as program_no,b.knitting_party,b.machine_gg,b.machine_dia 
				from   ppl_planning_info_entry_mst a,ppl_planning_info_entry_dtls b,ppl_planning_entry_plan_dtls c ,wo_booking_mst d 
				where a.id=b.mst_id and b.mst_id=c.mst_id and b.id=c.dtls_id and c.booking_no=d.booking_no $buyer_id_cond_to $within_group_cond 
				and c.company_id=$company_name 
				$date_cond2 and d.fabric_source in(1,2) 
				and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
				group by c.po_id,a.company_id,c.within_group,
				c.body_part_id,c.color_type_id,c.fabric_desc,c.determination_id,c.gsm_weight, c.dia, c.width_dia_type, c.pre_cost_fabric_cost_dtls_id, c.mst_id,c.dtls_id,b.knitting_party,b.machine_gg,b.machine_dia ";
				*/

			/*union all
			select a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type, sum(b.finish_qty) finish_qty, sum(b.grey_qty) grey_qty, '' as po_break_down_id,a.is_apply_last_update,a.is_master_part_updated,$sales_order_dtls_id,b.pre_cost_fabric_cost_dtls_id, a.po_job_no from fabric_sales_order_mst a,fabric_sales_order_dtls b,wo_non_ord_samp_booking_mst c,wo_non_ord_samp_booking_dtls d where a.id=b.mst_id and a.sales_booking_no=c.booking_no and c.booking_no=d.booking_no $active_status_sql and a.company_id=$company_name $booking_cond $job_no_cond $buyer_id_cond_to $within_group_cond $date_cond $barcode_cond $booking_nos_internal_ref_cond and (c.fabric_source in(1,2) or d.fabric_source in(1,2))  and a.booking_without_order=1 group by a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type,a.is_apply_last_update,a.is_master_part_updated,b.pre_cost_fabric_cost_dtls_id, a.po_job_no  order by dia*/
		}
		else
		{
			$sql = "SELECT a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type, sum(b.finish_qty) finish_qty, sum(b.grey_qty) grey_qty,(select c.po_break_down_id from wo_booking_mst c where a.sales_booking_no = c.booking_no) po_break_down_id,a.is_apply_last_update,a.is_master_part_updated,$sales_order_dtls_id,b.pre_cost_fabric_cost_dtls_id, a.po_job_no from fabric_sales_order_mst a,fabric_sales_order_dtls b where a.id=b.mst_id $active_status_sql and a.company_id=$company_name $booking_cond $job_no_cond $buyer_id_cond_to $within_group_cond $date_cond $barcode_cond $booking_nos_internal_ref_cond group by a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type,a.is_apply_last_update,a.is_master_part_updated,b.pre_cost_fabric_cost_dtls_id, a.po_job_no order by b.dia";
			//echo $sql;
		}
	}
		
	//echo $sql;
	
	$all_sales_booking_arr=array();
	$nameArray = sql_select($sql);
	//$salesMstIdArr = array();
	//$salesDtlsIdArr = array();
	$po_job_no_arr=array();
	foreach ($nameArray as $value)
	{
		/*if ($value[csf('within_group')]==1)
		{
			$all_sales_booking_arr[]=$value[csf('sales_booking_no')];
			$sales_booking_arr[] = "'".$value[csf('sales_booking_no')]."'";
		}*/
		$all_sales_booking_arr[]=$value[csf('sales_booking_no')];
		$sales_booking_arr[] = "'".$value[csf('sales_booking_no')]."'";
		$program_no_arr[] = "'".$value[csf('program_no')]."'";
		
		//for sales mst id
		//$salesMstIdArr[$value[csf('id')]] = $value[csf('id')];
		
		//for sales dtls id
		/*$expSalesDtlsId = explode(',', $value[csf('sales_order_dtls_id')]->load());
		foreach($expSalesDtlsId as $dtlsID)
		{
			$salesDtlsIdArr[$dtlsID] = $dtlsID;
		}*/

		array_push($po_job_no_arr,$value[csf('po_job_no')]);

	}
	//echo "<pre>";
	//print_r($salesDtlsIdArr); die;

	$break_down_arr = array();
	$break_down_cond = '';
	if(!empty($po_job_no_arr))
	{
		$break_down_cond = where_con_using_array($po_job_no_arr, '1', 'job_no_mst');
	}
	
	$poBreakData = "select job_no_mst, grouping from wo_po_break_down where status_active=1 and is_deleted=0 $break_down_cond";
	//echo $poBreakData;

	foreach (sql_select($poBreakData) as $rows) 
	{
		$break_down_arr[$rows[csf('job_no_mst')]]['grouping'] = $rows[csf('grouping')];
	}
	//var_dump($break_down_arr);

	$booking_data_array = array();
	$program_data_array = array();
	$booking_program_arr = array();
	if(!empty($sales_booking_arr))
	{ 
		$pre_cost_sql = sql_select("select a.id, a.booking_no, a.po_break_down_id, a.entry_form, b.pre_cost_fabric_cost_dtls_id, b.job_no, b.construction, b.copmposition, b.gsm_weight, b.dia_width, b.color_type
		from wo_booking_mst a inner join wo_booking_dtls b on b.booking_no = a.booking_no
		where a.booking_no in(".implode(",",$sales_booking_arr).")
		group by a.id, a.booking_no, a.po_break_down_id, a.entry_form, b.pre_cost_fabric_cost_dtls_id, b.job_no, b.construction, b.copmposition, b.gsm_weight, b.dia_width, b.color_type");
	}
	
	foreach ($pre_cost_sql as $row)
	{
		$desc = $row[csf('construction')] . " " . $row[csf('copmposition')];
		$booking_data_array[$row[csf('booking_no')]][$desc][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_type')]] = $row[csf('pre_cost_fabric_cost_dtls_id')];
	}
	
	if(!empty($sales_booking_arr))
	{
		$sales_booking = implode(",",$sales_booking_arr);
	    $sales_booking=implode(",",array_filter(array_unique(explode(",",$sales_booking))));
	    if($sales_booking!="")
	    {
	        $sales_booking=explode(",",$sales_booking);  
	        $sales_booking_chnk=array_chunk($sales_booking,999);
	        $sales_booking_cond=" and";
	        foreach($sales_booking_chnk as $dtls_id)
	        {
	        	if($sales_booking_cond==" and")
					$sales_booking_cond.="(b.booking_no in(".implode(',',$dtls_id).")";
				else
					$sales_booking_cond.=" or b.booking_no in(".implode(',',$dtls_id).")";
	        }
	        $sales_booking_cond.=")";
	        //echo $sales_booking_cond;die;
	    }	
		//$sales_booking_cond = "and b.booking_no in(".implode(",",$sales_booking_arr).")";
	}


	if(!empty($program_no_arr))
	{
		$programNos = implode(",",$program_no_arr);
	    $programNos=implode(",",array_filter(array_unique(explode(",",$programNos))));
	    if($programNos!="")
	    {
	        $programNos=explode(",",$programNos);  
	        $programNos_chnk=array_chunk($programNos,999);
	        $programNos_cond=" and";
	        $programNos_cond2=" and";
	        foreach($programNos_chnk as $progNos)
	        {
	        	if($programNos_cond==" and")
	        	{
					$programNos_cond.="(f.dtls_id in(".implode(',',$progNos).")";
					$programNos_cond2.="(a.booking_id in(".implode(',',$progNos).")";
	        	}
				else
				{
					$programNos_cond.=" or f.dtls_id in(".implode(',',$progNos).")";
					$programNos_cond2.=" or a.booking_id in(".implode(',',$progNos).")";
				}
	        }
	        $programNos_cond.=")";
	        $programNos_cond2.=")";
	        //echo $programNos_cond;die;
	    }	
		//$programNos_cond = "and b.booking_no in(".implode(",",$sales_booking_arr).")";
	}

	$machine_info_sql=sql_select("SELECT a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type, sum(b.finish_qty) finish_qty, sum(b.grey_qty) grey_qty,$po_break_down_id_cast, a.is_apply_last_update,a.is_master_part_updated,$sales_order_dtls_id,b.pre_cost_fabric_cost_dtls_id, a.po_job_no,d.mst_id as plan_id,d.dtls_id as program_no,d.program_qnty,e.knitting_party,e.machine_gg,e.machine_dia,f.id as update_id,f.mst_id,f.dtls_id,f.machine_id,f.dia as mc_dia,f.capacity,f.distribution_qnty,f.no_of_days,f.start_date,f.end_date  from fabric_sales_order_mst a,fabric_sales_order_dtls b,wo_booking_mst c, ppl_planning_entry_plan_dtls d,ppl_planning_info_entry_dtls e,ppl_planning_info_machine_dtls f LEFT JOIN ppl_entry_machine_datewise g ON f.mst_id=g.mst_id and f.dtls_id=g.dtls_id where a.id=b.mst_id and a.sales_booking_no=c.booking_no and c.booking_no=d.booking_no and d.po_id=a.id and d.dtls_id=e.id and d.mst_id=e.mst_id and f.dtls_id=e.id and d.dtls_id=f.dtls_id and f.mst_id=e.mst_id  $active_status_sql and a.company_id=$company_name $booking_cond $job_no_cond $buyer_id_cond_to $within_group_cond $date_cond $barcode_cond $booking_nos_internal_ref_cond and c.fabric_source in(1,2) and a.booking_without_order=0  and  f.status_active=1 and f.is_deleted=0  $programNos_cond group by a.id, a.company_id, a.within_group, a.job_no, a.sales_booking_no, a.booking_id, a.buyer_id, a.style_ref_no, a.booking_date, b.body_part_id, b.color_type_id, b.fabric_desc, b.determination_id, b.gsm_weight, b.dia, b.width_dia_type,a.is_apply_last_update,a.is_master_part_updated,b.pre_cost_fabric_cost_dtls_id,c.po_break_down_id, a.po_job_no,d.mst_id,d.dtls_id,d.program_qnty,e.knitting_party,e.machine_gg,e.machine_dia,f.id,f.mst_id,f.dtls_id,f.machine_id,f.dia,f.capacity,f.distribution_qnty,f.no_of_days,f.start_date,f.end_date 
			");


	//$machine_info_sql=sql_select("select a.id,a.mst_id,a.dtls_id,a.machine_id,a.dia,a.capacity,a.distribution_qnty,a.no_of_days,a.start_date,a.end_date from ppl_planning_info_machine_dtls a LEFT JOIN ppl_entry_machine_datewise b  ON a.mst_id=b.mst_id and a.dtls_id=b.dtls_id where a.status_active=1 and a.is_deleted=0 $programNos_cond ");


	foreach($machine_info_sql as $rows)
	{
		$machineInfoArr[$rows[csf('dtls_id')]][$rows[csf('plan_id')]][$rows[csf('job_no')]][$rows[csf('sales_booking_no')]][$rows[csf('booking_id')]][$rows[csf('buyer_id')]][$rows[csf('style_ref_no')]][$rows[csf('body_part_id')]][$rows[csf('color_type_id')]][$rows[csf('fabric_desc')]][$rows[csf('determination_id')]][$rows[csf('gsm_weight')]][$rows[csf('dia')]][$rows[csf('width_dia_type')]][$rows[csf('po_job_no')]]['mc_saved_string'].=$rows[csf('machine_id')]."_".$machineLibArr[$rows[csf('machine_id')]]."_".$rows[csf('capacity')]."_".$rows[csf('distribution_qnty')]."_".$rows[csf('no_of_days')]."_".$rows[csf('start_date')]."_".$rows[csf('end_date')]."_".$rows[csf('dtls_id')].",";
		
		$machineInfoArr[$rows[csf('dtls_id')]][$rows[csf('plan_id')]][$rows[csf('job_no')]][$rows[csf('sales_booking_no')]][$rows[csf('booking_id')]][$rows[csf('buyer_id')]][$rows[csf('style_ref_no')]][$rows[csf('body_part_id')]][$rows[csf('color_type_id')]][$rows[csf('fabric_desc')]][$rows[csf('determination_id')]][$rows[csf('gsm_weight')]][$rows[csf('dia')]][$rows[csf('width_dia_type')]][$rows[csf('po_job_no')]]['update_id']=$rows[csf('id')];
		
		$machineInfoArr[$rows[csf('dtls_id')]][$rows[csf('plan_id')]][$rows[csf('job_no')]][$rows[csf('sales_booking_no')]][$rows[csf('booking_id')]][$rows[csf('buyer_id')]][$rows[csf('style_ref_no')]][$rows[csf('body_part_id')]][$rows[csf('color_type_id')]][$rows[csf('fabric_desc')]][$rows[csf('determination_id')]][$rows[csf('gsm_weight')]][$rows[csf('dia')]][$rows[csf('width_dia_type')]][$rows[csf('po_job_no')]]['machine_id'].=$rows[csf('machine_id')].",";
		
		$machineInfoArr[$rows[csf('dtls_id')]][$rows[csf('plan_id')]][$rows[csf('job_no')]][$rows[csf('sales_booking_no')]][$rows[csf('booking_id')]][$rows[csf('buyer_id')]][$rows[csf('style_ref_no')]][$rows[csf('body_part_id')]][$rows[csf('color_type_id')]][$rows[csf('fabric_desc')]][$rows[csf('determination_id')]][$rows[csf('gsm_weight')]][$rows[csf('dia')]][$rows[csf('width_dia_type')]][$rows[csf('po_job_no')]]['machine_no'].=$machineLibArr[$rows[csf('machine_id')]].",";
		
		$machineInfoArr[$rows[csf('dtls_id')]][$rows[csf('plan_id')]][$rows[csf('job_no')]][$rows[csf('sales_booking_no')]][$rows[csf('booking_id')]][$rows[csf('buyer_id')]][$rows[csf('style_ref_no')]][$rows[csf('body_part_id')]][$rows[csf('color_type_id')]][$rows[csf('fabric_desc')]][$rows[csf('determination_id')]][$rows[csf('gsm_weight')]][$rows[csf('dia')]][$rows[csf('width_dia_type')]][$rows[csf('po_job_no')]]['distribution_qnty']+=$rows[csf('distribution_qnty')];
	}
	unset($machine_info_sql);


	//save_string += "," + machineId + "_" + machineNo + "_" + capacity + "_" + distributionQnty + "_" + noOfDays + "_" + startDate + "_" + endDate + "_" + dtls_id;




	$sql_production_qnty=sql_select("select a.booking_no,sum(b.grey_receive_qnty) as grey_receive_qnty from inv_receive_master a,pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.entry_form=2 and a.item_category=13 and a.receive_basis=2 $programNos_cond2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.booking_no");
	foreach($sql_production_qnty as $rows)
	{
		$productionQntyArr[$rows[csf('booking_no')]]['grey_receive_qnty']=$rows[csf('grey_receive_qnty')];
	}
	unset($sql_production_qnty);


	if ($db_type == 0)
	{
		$sql_plan = "SELECT a.id,a.mst_id,a.booking_no, a.po_id, a.yarn_desc as job_dtls_id, a.body_part_id, a.fabric_desc, a.gsm_weight, a.dia, a.color_type_id, group_concat(a.dtls_id) as prog_no,sum(a.program_qnty) as program_qnty,a.sales_order_dtls_ids,a.pre_cost_fabric_cost_dtls_id,b.recv_number,a.status_active from ppl_planning_entry_plan_dtls a left join inv_receive_master b on a.id=b.booking_id where a.status_active=1 and a.is_deleted=0 and a.is_sales=1 and a.is_revised=0 $sales_booking_cond  group by a.id,a.mst_id,booking_no, a.po_id, a.yarn_desc, a.body_part_id, a.fabric_desc, a.gsm_weight, a.dia, a.color_type_id,a.sales_order_dtls_ids,a.pre_cost_fabric_cost_dtls_id,b.recv_number,a.status_active ";
	}
	else
	{
		//$sql_plan = "SELECT a.id,a.mst_id,a.booking_no, a.po_id, a.yarn_desc as job_dtls_id, a.body_part_id, a.fabric_desc, a.gsm_weight, a.dia, a.color_type_id, listagg(a.dtls_id, ',') within group (order by a.dtls_id) as prog_no,sum(a.program_qnty) as program_qnty,a.sales_order_dtls_ids, a.pre_cost_fabric_cost_dtls_id,a.status_active from ppl_planning_entry_plan_dtls a where a.is_sales=1 and a.is_revised=0 $sales_booking_cond group by a.id,a.mst_id,a.booking_no, a.po_id, a.yarn_desc, a.body_part_id, a.fabric_desc, a.gsm_weight, a.dia, a.color_type_id,a.sales_order_dtls_ids,a.pre_cost_fabric_cost_dtls_id,a.status_active";

		$sql_plan = "SELECT b.id,b.mst_id,b.booking_no, b.po_id, b.yarn_desc as job_dtls_id, b.body_part_id, b.fabric_desc, b.gsm_weight, b.dia,b.width_dia_type, b.color_type_id, listagg(b.dtls_id, ',') within group (order by b.dtls_id) as prog_no,sum(b.program_qnty) as program_qnty,b.sales_order_dtls_ids, b.pre_cost_fabric_cost_dtls_id,b.status_active,a.determination_id 
		from ppl_planning_info_entry_mst a,ppl_planning_entry_plan_dtls b 
		where a.id=b.mst_id and b.is_sales=1 and b.is_revised=0  $sales_booking_cond
		group by b.id,b.mst_id,b.booking_no, b.po_id, b.yarn_desc, b.body_part_id, b.fabric_desc, b.gsm_weight, b.dia,b.width_dia_type, b.color_type_id,b.sales_order_dtls_ids,b.pre_cost_fabric_cost_dtls_id,b.status_active ,a.determination_id";


	}
	//echo $sql_plan;
	$res_plan = sql_select($sql_plan);
	foreach ($res_plan as $rowPlan)
	{
		/*$program_data_array[$rowPlan[csf('booking_no')]][$rowPlan[csf('po_id')]][$rowPlan[csf('body_part_id')]][$rowPlan[csf('fabric_desc')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]][$rowPlan[csf('color_type_id')]][$rowPlan[csf('pre_cost_fabric_cost_dtls_id')]][$rowPlan[csf('status_active')]]['mst_id'] = $rowPlan[csf('mst_id')];

		$program_data_array[$rowPlan[csf('booking_no')]][$rowPlan[csf('po_id')]][$rowPlan[csf('body_part_id')]][$rowPlan[csf('fabric_desc')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]][$rowPlan[csf('color_type_id')]][$rowPlan[csf('pre_cost_fabric_cost_dtls_id')]][$rowPlan[csf('status_active')]]['prog_no'][] = $rowPlan[csf('prog_no')];
		$program_data_array[$rowPlan[csf('booking_no')]][$rowPlan[csf('po_id')]][$rowPlan[csf('body_part_id')]][$rowPlan[csf('fabric_desc')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]][$rowPlan[csf('color_type_id')]][$rowPlan[csf('pre_cost_fabric_cost_dtls_id')]][$rowPlan[csf('status_active')]]['program_qnty'] += $rowPlan[csf('program_qnty')];
		$program_data_array[$rowPlan[csf('booking_no')]][$rowPlan[csf('po_id')]][$rowPlan[csf('body_part_id')]][$rowPlan[csf('fabric_desc')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]][$rowPlan[csf('color_type_id')]]['sales_order_dtls_ids'] = $rowPlan[csf('sales_order_dtls_ids')];*/
		

		//$program_data_array[$rowPlan[csf('booking_no')]][$rowPlan[csf('po_id')]][$rowPlan[csf('body_part_id')]][$rowPlan[csf('determination_id')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]][$rowPlan[csf('width_dia_type')]][$rowPlan[csf('color_type_id')]][$rowPlan[csf('pre_cost_fabric_cost_dtls_id')]][$rowPlan[csf('status_active')]]['mst_id'] = $rowPlan[csf('mst_id')];

		//$program_data_array[$rowPlan[csf('booking_no')]][$rowPlan[csf('po_id')]][$rowPlan[csf('body_part_id')]][$rowPlan[csf('determination_id')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]][$rowPlan[csf('width_dia_type')]][$rowPlan[csf('color_type_id')]][$rowPlan[csf('pre_cost_fabric_cost_dtls_id')]][$rowPlan[csf('status_active')]]['prog_no'][] = $rowPlan[csf('prog_no')];
		$program_data_array[$rowPlan[csf('booking_no')]][$rowPlan[csf('po_id')]][$rowPlan[csf('body_part_id')]][$rowPlan[csf('determination_id')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]][$rowPlan[csf('width_dia_type')]][$rowPlan[csf('color_type_id')]][$rowPlan[csf('pre_cost_fabric_cost_dtls_id')]][$rowPlan[csf('status_active')]][$rowPlan[csf('prog_no')]]['program_qnty'] += $rowPlan[csf('program_qnty')];
		//$program_data_array[$rowPlan[csf('booking_no')]][$rowPlan[csf('po_id')]][$rowPlan[csf('body_part_id')]][$rowPlan[csf('determination_id')]][$rowPlan[csf('gsm_weight')]][$rowPlan[csf('dia')]][$rowPlan[csf('width_dia_type')]][$rowPlan[csf('color_type_id')]]['sales_order_dtls_ids'] = $rowPlan[csf('sales_order_dtls_ids')];
		
		$program_data_array1[$rowPlan[csf('booking_no')]][$rowPlan[csf('po_id')]]['program'] .= $rowPlan[csf('prog_no')] . ",";
		$booking_program_arr[$rowPlan[csf('booking_no')]] .= $rowPlan[csf('prog_no')] . ",";

		// for sales order if within group no
		$sales_order_dtls_ids = explode(",",$rowPlan[csf('sales_order_dtls_ids')]);
		foreach ($sales_order_dtls_ids as $sales_dtls_row)
		{
			//$program_data_sales_array[$sales_dtls_row][$rowPlan[csf('status_active')]]['mst_id'] = $rowPlan[csf('mst_id')];
			//$program_data_sales_array[$sales_dtls_row][$rowPlan[csf('status_active')]]['prog_no'] .= $rowPlan[csf('prog_no')].",";
			$program_data_sales_array[$sales_dtls_row][$rowPlan[csf('status_active')]][$rowPlan[csf('prog_no')]]['program_qnty'] += $rowPlan[csf('program_qnty')];
		}
	}
	
	//for show
	if ($type == 1)
	{

		if(!empty($all_sales_booking_arr))
		{
			$booking_list=implode(",", array_unique($all_sales_booking_arr));
			$is_approved_status_arr = return_library_array( "select booking_no, is_approved from wo_booking_mst where booking_no in ('".$booking_list."')",'booking_no','is_approved');
		}
		
		if(!empty($all_sales_booking_arr))
		{
			$job_no_array=array();
			$booking_list=implode(",", array_unique($all_sales_booking_arr));
			$sql_data=sql_select("select a.id, b.buyer_name,c.booking_no from wo_po_break_down a, wo_po_details_master b, wo_booking_dtls c where b.job_no=a.job_no_mst and a.id=c.po_break_down_id and c.booking_no in ('".$booking_list."') and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
				union all
				select 0 as id, buyer_id,booking_no from wo_non_ord_samp_booking_mst where booking_no in ('".$booking_list."') and status_active=1 and is_deleted=0");
			foreach ($sql_data as $row)
			{
				$job_no_array[$row[csf('booking_no')]]['buyer_id']=$row[csf('buyer_name')];
			}
		}
		
	
	
		?>
		<form name="palnningEntry_2" id="palnningEntry_2">
			<fieldset>
				<legend>Fabric Description Details</legend>
				<input type="hidden" value="<? echo $type; ?>" name="txt_type" id="txt_type">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list_search">
					<thead>
						<th width="40">SL</th>
						
						<th width="60">Prog. No</th>
						<th width="120">Booking No</th>
						<th width="70">Booking Date</th>
						<th title="Internal Ref." width="100">IR/IB</th>
						<th width="60">Buyer</th>
						<th width="120">Sales Order No</th>
						<th width="100">Style</th>
						<th width="100">Body Part</th>
						<th width="70">Color Type</th>
						<th width="160">Fabric Desc.</th>
						<th width="50">Gsm</th>
						<th width="50">Dia</th>
						<th width="70">Width/Dia Type</th>
						<th width="70">Prog. Qnty</th>
						<th width="100">MC No</th>
						<th width="70">MC. Dist. Qty</th>
						<th width="70">Knitting Qty</th>
						<th>Balance Prog. Qnty
							<input type="hidden" name="action_type" id="action_type" value="<? echo $type; ?>"/>
						</th>
						
					</thead>
					<tbody>
						<?
						$i = 1;
						$k = 1;
						$z = 1;
						$dia_array = array();
						$nameArray = sql_select($sql);
						$a = '';
						foreach ($nameArray as $row)
						{
							$plan_id = '';
							//$compId = $row[csf('company_id')];
							$job_no = $row[csf('job_no')];
							$style_ref_no = $row[csf('style_ref_no')];
							$sales_booking_no = $row[csf('sales_booking_no')];
							$booking_date = change_date_format($row[csf('booking_date')]);
							$gsm = $row[csf('gsm_weight')];
							$dia = $row[csf('dia')];
							$desc = trim($row[csf('fabric_desc')]);
							$determination_id = $row[csf('determination_id')];
							$body_part_id = $row[csf('body_part_id')];
							$color_type_id = $row[csf('color_type_id')];
							$width_dia_type = $row[csf('width_dia_type')];
							$internal_ref = $break_down_arr[$row[csf('po_job_no')]]['grouping'];
							$programNo = $row[csf('program_no')];
							$productionQnty=$productionQntyArr[$programNo]['grey_receive_qnty'];
							
							//add date 30.06.2020
							$expSalesDtlsIdArr = array();
							$alesDtlsIdArr =array();
							//tmp solution
							$row[csf('sales_order_dtls_id')] = $row[csf('sales_order_dtls_id')]->load();
							$expSalesDtlsIdArr = explode(',', $row[csf('sales_order_dtls_id')]);
							for($zs =0; $zs<count($expSalesDtlsIdArr); $zs++)
							{
								$alesDtlsIdArr[$expSalesDtlsIdArr[$zs]] = $expSalesDtlsIdArr[$zs];
							}
							$sales_order_dtls_id = implode(',', $alesDtlsIdArr);
							//$sales_order_dtls_id = $row[csf('sales_order_dtls_id')];
							
							$sales_id = $row[csf('id')];
							$within_group = $row[csf('within_group')];
							$pre_cost_fabric_cost_dtls_id = $row[csf('pre_cost_fabric_cost_dtls_id')];
							$buyer_id = $row[csf('buyer_id')];
							$buyer_name = $job_no_array[$sales_booking_no]['buyer_id'];
							
							//for grey qty
							//$grey_qty = $row[csf('grey_qty')];
							//echo $sales_booking_no."=".$sales_id."=".$body_part_id."=".$determination_id."=".$gsm."=".$dia."=".$color_type_id."=".$pre_cost_fabric_cost_dtls_id."<br/>";
							//$grey_qty = $greyQtyData[$sales_booking_no][$sales_id][$body_part_id][$desc][$gsm][$dia][$color_type_id][$pre_cost_fabric_cost_dtls_id]['grey_qty'];
							$grey_qty = $greyQtyData[$sales_booking_no][$sales_id][$body_part_id][$determination_id][$gsm][$dia][$width_dia_type][$color_type_id][$pre_cost_fabric_cost_dtls_id]['grey_qty'];

							$status = ($type == 1) ? 1 : 0;
							if($within_group == 1)
							{
								/*$program_qnty = $program_data_array[$sales_booking_no][$sales_id][$body_part_id][$desc][$gsm][$dia][$color_type_id][$pre_cost_fabric_cost_dtls_id][$status]['program_qnty'];
								$plan_id = $program_data_array[$sales_booking_no][$sales_id][$body_part_id][$desc][$gsm][$dia][$color_type_id][$pre_cost_fabric_cost_dtls_id][$status]['mst_id'];
								$prog_no = $program_data_array[$sales_booking_no][$sales_id][$body_part_id][$desc][$gsm][$dia][$color_type_id][$pre_cost_fabric_cost_dtls_id][$status]['prog_no'];*/

								$program_qnty = $program_data_array[$sales_booking_no][$sales_id][$body_part_id][$determination_id][$gsm][$dia][$width_dia_type][$color_type_id][$pre_cost_fabric_cost_dtls_id][$status][$programNo]['program_qnty'];

								$program_qnty = $row[csf('program_qnty')];
								//$plan_id = $program_data_array[$sales_booking_no][$sales_id][$body_part_id][$determination_id][$gsm][$dia][$width_dia_type][$color_type_id][$pre_cost_fabric_cost_dtls_id][$status]['mst_id'];
								//$prog_no = $program_data_array[$sales_booking_no][$sales_id][$body_part_id][$determination_id][$gsm][$dia][$width_dia_type][$color_type_id][$pre_cost_fabric_cost_dtls_id][$status]['prog_no'];

								//$prog_no = implode(",", $prog_no);
							}
							else
							{
								$sales_dtls_id = array_unique(explode(",",$sales_order_dtls_id));
								$program_qnty = 0;
								$prog_no='';
								//print_r($sales_dtls_id);
								foreach ($sales_dtls_id as $rows)
								{
									
									//$plan_id .= $program_data_sales_array[$rows][$status]['mst_id'].",";
									//$prog_no .= $program_data_sales_array[$rows][$status]['prog_no'].",";
									$program_qnty = $program_data_sales_array[$rows][$status][$programNo]['program_qnty'];
									
								}
							}



							//$balance_qnty = number_format($grey_qty - $program_qnty,2,".","");
							$pre_cost_id = $booking_data_array[$sales_booking_no][$desc][$gsm][$dia][$color_type_id];
							if (($planning_status == 2 ) || ($planning_status == 1))
							{
								if ($z % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
								if (!in_array($dia, $dia_array)) {
									if ($k != 1) {
										?>
										<tr bgcolor="#CCCCCC" id="tr_<? echo $i; ?>">
											<td colspan="14" align="right"><b>Sub Total</b></td>
										
											<td align="right">
												<b><? echo number_format($total_program_qnty, 2, '.', ''); ?></b></td>
												<td align="right"><b><? //echo number_format($total_balance, 2, '.', ''); ?></b>
												</td>
												<td align="right"><b><? //echo number_format($total_balance, 2, '.', ''); ?></b>
												</td>
												<td align="right"><b><? //echo number_format($total_balance, 2, '.', ''); ?></b>
												</td>
												<td align="right"><b><? echo number_format($total_balance, 2, '.', ''); ?></b>
												</td>
											</tr>
											<?
											
											$total_program_qnty = 0;
											$total_balance = 0;
											$i++;
										}
										?>
										<tr bgcolor="#EFEFEF" id="tr_<? echo $i; ?>">
											<td colspan="19">
												<b>Dia/Width: <?php echo $dia; ?></b>
											</td>
										</tr>
										<?
										$dia_array[] = $row[csf('dia')];
										$k++;
										$i++;
									}

									if ($within_group == 1) {
										$buyer = $buyer_arr[$buyer_name];
									} else {
										$buyer = $buyer_arr[$buyer_id];
									}
									

									if($machineInfoArr[$programNo][$row[csf('plan_id')]][$row[csf('job_no')]][$row[csf('sales_booking_no')]][$row[csf('booking_id')]][$row[csf('buyer_id')]][$row[csf('style_ref_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][$row[csf('fabric_desc')]][$row[csf('determination_id')]][$row[csf('gsm_weight')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('po_job_no')]]['update_id']>0)
									{

										$mc_save_string=$machineInfoArr[$programNo][$row[csf('plan_id')]][$row[csf('job_no')]][$row[csf('sales_booking_no')]][$row[csf('booking_id')]][$row[csf('buyer_id')]][$row[csf('style_ref_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][$row[csf('fabric_desc')]][$row[csf('determination_id')]][$row[csf('gsm_weight')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('po_job_no')]]['mc_saved_string'];
										
										$mc_update_id=$machineInfoArr[$programNo][$row[csf('plan_id')]][$row[csf('job_no')]][$row[csf('sales_booking_no')]][$row[csf('booking_id')]][$row[csf('buyer_id')]][$row[csf('style_ref_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][$row[csf('fabric_desc')]][$row[csf('determination_id')]][$row[csf('gsm_weight')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('po_job_no')]]['update_id'];

										$mc_machine_id=chop($machineInfoArr[$programNo][$row[csf('plan_id')]][$row[csf('job_no')]][$row[csf('sales_booking_no')]][$row[csf('booking_id')]][$row[csf('buyer_id')]][$row[csf('style_ref_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][$row[csf('fabric_desc')]][$row[csf('determination_id')]][$row[csf('gsm_weight')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('po_job_no')]]['machine_id'],",");
										
										$mc_machine_no=chop($machineInfoArr[$programNo][$row[csf('plan_id')]][$row[csf('job_no')]][$row[csf('sales_booking_no')]][$row[csf('booking_id')]][$row[csf('buyer_id')]][$row[csf('style_ref_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][$row[csf('fabric_desc')]][$row[csf('determination_id')]][$row[csf('gsm_weight')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('po_job_no')]]['machine_no'],",");
										
										$mc_machine_distribution_qnty=$machineInfoArr[$programNo][$row[csf('plan_id')]][$row[csf('job_no')]][$row[csf('sales_booking_no')]][$row[csf('booking_id')]][$row[csf('buyer_id')]][$row[csf('style_ref_no')]][$row[csf('body_part_id')]][$row[csf('color_type_id')]][$row[csf('fabric_desc')]][$row[csf('determination_id')]][$row[csf('gsm_weight')]][$row[csf('dia')]][$row[csf('width_dia_type')]][$row[csf('po_job_no')]]['distribution_qnty'];

									}
									
									
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" >
											<!-- onClick="selected_row('<? echo $i; ?>','<? echo $status_arr[$approval_status]; ?>')" id="tr_<? echo $i; ?>" -->
												
											<?
											//$plan_id = implode(",",array_filter(array_unique(explode(",", chop($plan_id,",")))));
											?>
											<td width="40" align='center'><? echo $z; ?></td>
											
											<td width="60" align='center' id="prog_no_<? echo $i; ?>"><p>
												<?
												//echo rtrim($plan_id,", ");

												echo "<a href='##' onclick=\"generate_report2(" . $company_name . "," . $programNo . "," . $program_info_format_id . ")\">" . $programNo . "</a>";


												
												
												?>
											</p></td>
											<td id="booking_no_<? echo $i; ?>" align='center'><? echo $sales_booking_no; ?></td>
											<td width="70" align="center"><? echo $booking_date; ?></td>
											<td width="100" align='center'><p><? echo $internal_ref; ?></p></td>
											<td width="60" align='center'><p><? echo $buyer; ?></p></td>
											<td align='center'><? echo $job_no; ?></td>
											<td width="100" align='center'><p><? echo $style_ref_no; ?></p></td>
											<td align='center'><p><? echo $body_part[$body_part_id]; ?></p></td>
											<td width="70" align='center'><p><? echo $color_type[$color_type_id]; ?></p></td>
											<td align='center' id="desc_<? echo $i; ?>" title="<? echo "Yarn Count Determination ID: ".$determination_id; ?>"><p><? echo $desc; ?></p></td>
											<td width="50" align='center' id="gsm_weight_<? echo $i; ?>"><p><? echo $gsm; ?></p></td>
											<td width="50" align='center' id="dia_width_<? echo $i; ?>"><p><? echo $dia; ?></p></td>
											<td width="70" align='center'><? echo $fabric_typee[$width_dia_type]; ?></td>
											
											<td align="right" width="70">
												<? if ($program_qnty > 0) echo number_format($program_qnty, 2, '.', ''); ?>
											</td>
											<td align="right" width="100">
												<input type="text" name="txt_machine_no[]" id="txt_machine_no_<? echo $i; ?>" class="text_boxes"
													placeholder="Machine Entry Popup" style="width:100px;"
													onDblClick="openpage_machine2('<? echo $i.'_'.$row[csf('plan_id')].'_'.$programNo.'_'.$company_name.'_'.$row[csf('knitting_party')].'_'.$row[csf('machine_gg')].'_'.$row[csf('machine_dia')].'_'.$program_qnty; ?>');" value="<? echo $mc_machine_no; ?>" readonly/>
												<input type="hidden" name="machine_id[]" id="machine_id_<? echo $i; ?>" class="text_boxes" value="<? echo $mc_machine_id; ?>" readonly/>
												<input type="hidden" name="save_data[]" id="save_data_<? echo $i; ?>" class="text_boxes" value="<? echo $mc_save_string; ?>" readonly/>
												<input type="hidden" name="update_id[]" id="update_id_<? echo $i; ?>" class="text_boxes" value="<? echo $mc_update_id; ?>" readonly/>
											</td>

											<td align="right" width="70">
												<input type="text" name="distribution_qnty[]" id="distribution_qnty_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $mc_machine_distribution_qnty; ?>" style="width:60px;" readonly disabled/>
											</td>
											<td align="right" width="70">
												<? echo number_format($productionQnty, 2, '.', ''); ?>
											</td>


											<td align="right" id="ballance_qnty_<? echo $i; ?>"><? $balance_qnty=($program_qnty-$productionQnty); echo number_format($balance_qnty, 2, '.', ''); ?></td>
													
										</tr>
										<?
										if($hidden=="")
										{
											//$total_dia_qnty += $row[csf('grey_qty')];
										
											$total_program_qnty += $program_qnty;
											$total_balance += $balance_qnty;

											//$total_qnty += $row[csf('grey_qty')];
											
											$grand_total_program_qnty += $program_qnty;
											$grand_total_balance += $balance_qnty;

											$i++;
											$z++;
										}
									}
									$mc_machine_no="";
									$mc_machine_id="";
									$mc_update_id="";
									$mc_save_string="";
									$mc_machine_distribution_qnty ="";
								}

								if ($i > 1) {
									?>
									<tr bgcolor="#CCCCCC" id="tr_<? echo $i; ?>">
										<td colspan="14" align="right"><b>Sub Total</b></td>
										
										<td align="right"><b><? echo number_format($total_program_qnty, 2, '.', ''); ?></b></td>
										<td align="right"><b><? //echo number_format($total_program_qnty, 2, '.', ''); ?></b></td>
										<td align="right"><b><? //echo number_format($total_program_qnty, 2, '.', ''); ?></b></td>
										<td align="right"><b><? //echo number_format($total_program_qnty, 2, '.', ''); ?></b></td>
										<td align="right"><b><? echo number_format($total_balance, 2, '.', ''); ?></b></td>
									</tr>
									<?
								}
								?>
							</tbody>
							<tfoot>
								<th colspan="14" align="right">Grand Total<input type="hidden" name="company_id" id="company_id" value="<? echo $company_name; ?>"/></th>
						
								<th align="right"><? echo number_format($grand_total_program_qnty, 2, '.', ''); ?></th>
								<th align="right"><? //echo number_format($grand_total_program_qnty, 2, '.', ''); ?></th>
								<th align="right"><? //echo number_format($grand_total_program_qnty, 2, '.', ''); ?></th>
								<th align="right"><? //echo number_format($grand_total_program_qnty, 2, '.', ''); ?></th>
								<th align="right"><? echo number_format($grand_total_balance, 2, '.', ''); ?></th>
							</tfoot>
						</table>
					</div>
				</fieldset>
			</form>
		<?
	}
	
	exit();
}

if ($action == "save_update_delete")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	if ($operation == 0)  // Insert Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
			$start_date = change_date_format(str_replace("'", "", trim($start_date)), "yyyy-mm-dd", "");
			$end_date = change_date_format(str_replace("'", "", trim($end_date)), "yyyy-mm-dd", "");
		} else {
			$start_date = change_date_format(str_replace("'", "", trim($start_date)), '', '', 1);
			$end_date = change_date_format(str_replace("'", "", trim($end_date)), '', '', 1);
		}

		$hidden_plan_id=str_replace("'", "", $hidden_plan_id);
		$hidden_prog_id=str_replace("'", "", $hidden_prog_id);
		
		$machine_dtls_id = return_next_id("id", "ppl_planning_info_machine_dtls", 1);
		$field_array_machine_dtls = "id, mst_id, dtls_id, machine_id, dia, capacity, distribution_qnty, no_of_days, start_date, end_date, is_sales, inserted_by, insert_date";

		$machine_dtls_datewise_id = return_next_id("id", "ppl_entry_machine_datewise", 1);
		$field_array_machine_dtls_datewise = "id, mst_id, dtls_id, machine_id, distribution_date, fraction_date, days_complete, qnty, machine_plan_id, is_sales, inserted_by, insert_date";

		$save_string = str_replace("'", "", $save_string);
		if ($save_string != "") {
			$save_string = explode(",", $save_string);
			for ($i = 0; $i < count($save_string); $i++) {
				$machine_wise_data = explode("_", $save_string[$i]);
				$machine_id = $machine_wise_data[0];
				$dia = $machine_wise_data[1];
				$capacity = $machine_wise_data[2];
				$qnty = $machine_wise_data[3];
				$noOfDays = $machine_wise_data[4];

				$dateWise_qnty = 0;
				$bl_qnty = $qnty;

				if ($machine_wise_data[5] != "") $startDate = date("Y-m-d", strtotime($machine_wise_data[5]));
				if ($machine_wise_data[6] != "") $endDate = date("Y-m-d", strtotime($machine_wise_data[6]));

				if ($startDate != "" && $endDate != "") {
					$sCurrentDate = date("Y-m-d", strtotime("-1 day", strtotime($startDate)));
					$days = $noOfDays;
					$fraction = 0;
					$days_complete = 0;
					while ($sCurrentDate < $endDate) {
						$sCurrentDate = date("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));
						if ($days >= 1) {
							$fraction = 0;
							$days_complete = 1;
							$dateWise_qnty = $capacity;
						} else {
							$fraction = 1;
							$days_complete = $days;
							$dateWise_qnty = $bl_qnty;
						}

						$days = $days - 1;
						$bl_qnty = $bl_qnty - $capacity;

						if ($db_type == 0) $curr_date = $sCurrentDate; else $curr_date = change_date_format($sCurrentDate, '', '', 1);

						if ($data_array_machine_dtls_datewise != "") $data_array_machine_dtls_datewise .= ",";
						$data_array_machine_dtls_datewise .= "(" . $machine_dtls_datewise_id . "," . $hidden_plan_id . "," . $hidden_prog_id . ",'" . $machine_id . "','" . $curr_date . "','" . $fraction . "','" . $days_complete . "','" . $dateWise_qnty . "','" . $machine_dtls_id . "',1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
						$machine_dtls_datewise_id = $machine_dtls_datewise_id + 1;
					}
				}

				if ($db_type == 0) {
					$mstartDate = $startDate;
					$mendDate = $endDate;
				} else {
					$mstartDate = change_date_format($startDate, '', '', 1);
					$mendDate = change_date_format($endDate, '', '', 1);
				}

				if ($data_array_machine_dtls != "") $data_array_machine_dtls .= ",";
				$data_array_machine_dtls .= "(" . $machine_dtls_id . "," . $hidden_plan_id . "," . $hidden_prog_id . ",'" . $machine_id . "','" . $dia . "','" . $capacity . "','" . $qnty . "','" . $noOfDays . "','" . $mstartDate . "','" . $mendDate . "',1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				$machine_dtls_id = $machine_dtls_id + 1;
			}
		}
		
		/*oci_rollback($con);
		echo "5**0**0**".$data_array_color_wise_break_down;
		disconnect($con);
		die;*/

		//echo "10**insert into ppl_planning_info_entry_dtls (".$field_array_dtls.") Values ".$data_array_dtls."";die;

		if ($save_string != "") {
			if ($data_array_machine_dtls != "") {
				//echo "10**insert into ppl_planning_info_machine_dtls (".$field_array_machine_dtls.") Values ".$data_array_machine_dtls."";die;
				$rID = sql_insert("ppl_planning_info_machine_dtls", $field_array_machine_dtls, $data_array_machine_dtls, 0);
				if ($rID) $flag = 1; else $flag = 0;
			}

			if ($data_array_machine_dtls_datewise != "") {
				//echo "10**insert into ppl_entry_machine_datewise (".$field_array_machine_dtls_datewise.") Values ".$data_array_machine_dtls_datewise."";die;
				$rID2 = sql_insert("ppl_entry_machine_datewise", $field_array_machine_dtls_datewise, $data_array_machine_dtls_datewise, 0);
				if ($flag == 1) {
					if ($rID2) $flag = 1; else $flag = 0;
				}
			}
		}

		
		/*echo "10**".$rID . "_" . $rID2 ."_".$flag;
		disconnect($con);
		die();*/

		if ($db_type == 0) {
			if ($flag == 1) {
				mysql_query("COMMIT");
				echo "0**" . $machine_dtls_id ."**". $hidden_plan_id . "**0";
			} else {
				mysql_query("ROLLBACK");
				echo "5**0**0";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($flag == 1) {
				oci_commit($con);
				echo "0**" . $machine_dtls_id ."**". $hidden_plan_id . "**0";
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
		
 		$hidden_plan_id=str_replace("'", "", $hidden_plan_id);
		$hidden_prog_id=str_replace("'", "", $hidden_prog_id);

		$machine_dtls_id = return_next_id("id", "ppl_planning_info_machine_dtls", 1);
		$field_array_machine_dtls = "id, mst_id, dtls_id, machine_id, dia, capacity, distribution_qnty, no_of_days, start_date, end_date, is_sales, inserted_by, insert_date";
		//$field_array_machine_dtls_update = "machine_id*dia*capacity*distribution_qnty*no_of_days*start_date*end_date*updated_by*update_date";

		$machine_dtls_datewise_id = return_next_id("id", "ppl_entry_machine_datewise", 1);
		$field_array_machine_dtls_datewise = "id, mst_id, dtls_id, machine_id, distribution_date, fraction_date, days_complete, qnty, machine_plan_id, is_sales, inserted_by, insert_date";

		$save_string = str_replace("'", "", $save_string);
		if ($save_string != "") {
			$save_string = explode(",", $save_string);
			for ($i = 0; $i < count($save_string); $i++) {
				$machine_wise_data = explode("_", $save_string[$i]);
				$machine_id = $machine_wise_data[0];
				$dia = $machine_wise_data[1];
				$capacity = $machine_wise_data[2];
				$qnty = $machine_wise_data[3];
				$noOfDays = $machine_wise_data[4];
				$dtls_id = $machine_wise_data[7];

				$dateWise_qnty = 0;
				$bl_qnty = $qnty;

				if ($machine_wise_data[5] != "") $startDate = date("Y-m-d", strtotime($machine_wise_data[5]));
				if ($machine_wise_data[6] != "") $endDate = date("Y-m-d", strtotime($machine_wise_data[6]));

				if ($db_type == 0) {
					$mstartDate = $startDate;
					$mendDate = $endDate;
				} else {
					$mstartDate = change_date_format($startDate, '', '', 1);
					$mendDate = change_date_format($endDate, '', '', 1);
				}

				if ($data_array_machine_dtls != "") $data_array_machine_dtls .= ",";
				$data_array_machine_dtls .= "(" . $machine_dtls_id . "," . $hidden_plan_id . "," . $hidden_prog_id . ",'" . $machine_id . "','" . $dia . "','" . $capacity . "','" . $qnty . "','" . $noOfDays . "','" . $mstartDate . "','" . $mendDate . "',1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

				$machine_plan_id = $machine_dtls_id;
				$machine_dtls_id = $machine_dtls_id + 1;

				if ($startDate != "" && $endDate != "") {
					$sCurrentDate = date("Y-m-d", strtotime("-1 day", strtotime($startDate)));
					$days = $noOfDays;
					$fraction = 0;
					$days_complete = 0;

					while ($sCurrentDate < $endDate) {
						$sCurrentDate = date("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));

						if ($days >= 1) {
							$fraction = 0;
							$days_complete = 1;
							$dateWise_qnty = $capacity;
						} else {
							$fraction = 1;
							$days_complete = $days;
							$dateWise_qnty = $bl_qnty;
						}

						$days = $days - 1;
						$bl_qnty = $bl_qnty - $capacity;

						if ($db_type == 0) $curr_date = $sCurrentDate; else $curr_date = change_date_format($sCurrentDate, '', '', 1);

						if ($data_array_machine_dtls_datewise != "") $data_array_machine_dtls_datewise .= ",";

						$data_array_machine_dtls_datewise .= "(" . $machine_dtls_datewise_id . "," . $hidden_plan_id . "," . $hidden_prog_id . ",'" . $machine_id . "','" . $curr_date . "','" . $fraction . "','" . $days_complete . "','" . $dateWise_qnty . "','" . $machine_plan_id . "',1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
						$machine_dtls_datewise_id = $machine_dtls_datewise_id + 1;
					}
				}
			}
		}

		$delete_datewise = execute_query("delete from ppl_entry_machine_datewise where dtls_id=$hidden_prog_id", 0);
		if ($delete_datewise) $flag = 1; else $flag = 0;
		
		//echo "10**";
		$delete_machine = execute_query("delete from ppl_planning_info_machine_dtls where dtls_id=$hidden_prog_id", 0);

		if ($flag == 1) {
			if ($delete_machine) $flag = 1; else $flag = 0;
		}

		if ($save_string != "") {
			if ($data_array_machine_dtls != "") {
				//echo"insert into ppl_planning_info_machine_dtls (".$field_array_machine_dtls.") Values ".$data_array_machine_dtls."";die;
				$rID = sql_insert("ppl_planning_info_machine_dtls", $field_array_machine_dtls, $data_array_machine_dtls, 0);
				if ($flag == 1) {
					if ($rID) $flag = 1; else $flag = 0;
				}
			}

			if ($data_array_machine_dtls_datewise != "") {
				//echo "10**insert into ppl_entry_machine_datewise (".$field_array_machine_dtls_datewise.") Values ".$data_array_machine_dtls_datewise."";die;
				$rID2 = sql_insert("ppl_entry_machine_datewise", $field_array_machine_dtls_datewise, $data_array_machine_dtls_datewise, 0);
				if ($flag == 1) {
					if ($rID2) $flag = 1; else $flag = 0;
				}
			}
		}

		if ($db_type == 0) {
			if ($flag == 1) {
				mysql_query("COMMIT");
				echo "1**" . $machine_dtls_id ."**". str_replace("'", "", $hidden_plan_id) . "**0";
			} else {
				mysql_query("ROLLBACK");
				echo "6**0**1";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($flag == 1) {
				oci_commit($con);
				echo "1**" . $machine_dtls_id ."**". str_replace("'", "", $hidden_plan_id) . "**0";
			} else {
				oci_rollback($con);
				echo "6**0**1";
			}
		}
		disconnect($con);
		die;
	} 
	else if ($operation == 2) 
	{
		die;
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		$knit_qty = sql_select("select a.knit_id from ppl_yarn_requisition_entry a where a.knit_id=$update_dtls_id and a.is_deleted=0 and a.status_active=1");
		if ($knit_qty[0][csf('knit_id')] != "") {
			echo "14**Program already used in Requisition. So it can not be deleted";
			disconnect($con);
			exit();
		}

		$is_knitting_production = return_field_value("sum(b.grey_receive_qnty) as knitting_qnty", "inv_receive_master a, pro_grey_prod_entry_dtls b", "a.id=b.mst_id and a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and a.booking_id=$update_dtls_id and b.status_active=1 and b.is_deleted=0", "knitting_qnty");

		if ($is_knitting_production != "") {
			echo "14**Program can not be deleted. Program Already used in Knitting Production. Production Quantity is = $is_knitting_production";
			disconnect($con);
			exit();
		}

		$field_array_update = "status_active*is_deleted*updated_by*update_date";
		$data_array_update = "0*1*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

		$rID = sql_update("ppl_planning_info_entry_dtls", $field_array_update, $data_array_update, "id", $update_dtls_id, 0);
		if ($rID) $flag = 1; else $flag = 0;

		$rID2 = sql_update("ppl_planning_entry_plan_dtls", $field_array_update, $data_array_update, "dtls_id", $update_dtls_id, 0);
		if ($flag == 1) {
			if ($rID2) $flag = 1; else $flag = 0;
		}
		
		$rID13 = sql_update("ppl_color_wise_break_down", $field_array_update, $data_array_update, "program_no", $update_dtls_id, 0);
		if ($flag == 1)
		{
			if ($rID13) $flag = 1; else $flag = 0;
		}

		$delete = execute_query("delete from ppl_planning_info_machine_dtls where dtls_id=$update_dtls_id", 0);
		if ($flag == 1) {
			if ($delete) $flag = 1; else $flag = 0;
		}

		$delete_datewise = execute_query("delete from ppl_entry_machine_datewise where dtls_id=$update_dtls_id", 0);
		if ($flag == 1) {
			if ($delete_datewise) $flag = 1; else $flag = 0;
		}

		$cam_design_field_array_update = "status_active*is_deleted*updated_by*update_date";
		$cam_design_data_array_update = "0*1*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
		$rID3 = sql_update("ppl_planning_cam_design_dtls", $cam_design_field_array_update, $cam_design_data_array_update, "dtls_id", $update_dtls_id, 0);
		if ($rID3) $flag = 1; else $flag = 0;

		$needle_layout_field_array_update = "status_active*is_deleted*updated_by*update_date";
		$needle_layout_data_array_update = "0*1*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
		$rID3 = sql_update("ppl_planning_needle_layout", $needle_layout_field_array_update, $needle_layout_data_array_update, "program_no", $update_dtls_id, 0);
		if ($rID3) $flag = 1; else $flag = 0;

		/*$delete_feeder=execute_query( "delete from ppl_planning_feeder_dtls where dtls_id=$update_dtls_id",1);
		if($flag==1)
		{
			if($delete_feeder) $flag=1; else $flag=0;
		}*/

		if ($db_type == 0) {
			if ($flag == 1) {
				mysql_query("COMMIT");
				echo "2**" . str_replace("'", "", $updateId) . "**0";
			} else {
				mysql_query("ROLLBACK");
				echo "7**0**1";
			}
		}

		if ($db_type == 2 || $db_type == 1) {
			if ($flag == 1) {
				oci_commit($con);
				echo "2**" . str_replace("'", "", $updateId) . "**0";
			} else {
				oci_rollback($con);
				echo "7**0**1";
			}
		}
		disconnect($con);
		die;
	}
}



if ($action == "machine_info_popup")
{
	echo load_html_head_contents("Machine Info", "../../", 1, 1, '', '', '');

	extract($_REQUEST);
	$machine_mixing_in_knittingplan = return_field_value("machine_mixing", "variable_settings_production", "company_name=$companyID and variable_list=157");
	$is_machine_mixing= $machine_mixing_in_knittingplan[0]['machine_mixing'];


	?>

	<style type="text/css">
		.highlight {
			background: #2e9500;
		}

		.highlight a {
			background-color: #42B373 !important;
			background-image :none !important;
			color: #ffffff !important;
			opacity: 0.7;
		}

		.program_calendar {
			height: 18px;
			font-size: 11px;
			line-height: 16px;
			padding: 0 5px;
			text-align:left;
			border: 1px solid #676767;
			border-radius: 3px;
			border-radius: .5em;
		}
	</style>

	<script>

		var permission = '<? echo $permission; ?>';

		$(document).ready(function (e) {
			setFilterGrid('tbl_list_search', -1);
		});

		function calculate_qnty(tr_id) {
			var distribution_qnty = $('#txt_distribution_qnty_' + tr_id).val() * 1;

			//Function return for Machine mixing varialbe if No
			var fnc_return=fnc_check_machine_mixing_variable();
			if(fnc_return==1)
			{
				alert('Machine Mixing Not Allowed. Check Variable');
				$('#txt_noOfDays_' + tr_id).val('');
				$('#txt_startDate_' + tr_id).val('');
				$('#txt_endDate_' + tr_id).val('');
				$('#txt_distribution_qnty_' + tr_id).val('');
				return;
			}

			if (distribution_qnty > 0) {
				$('#search' + tr_id).css('background-color', 'yellow');
			}
			else {
				$('#search' + tr_id).css('background-color', '#FFFFCC');
			}

			calculate_total_qnty('txt_distribution_qnty_', 'txt_total_distribution_qnty');
		}

		function calculate_total_qnty(field_id, total_field_id) {
			var tot_row = $("#tbl_list_search tbody tr").length - 1;

			var ddd = {dec_type: 2, comma: 0, currency: ''}

			math_operation(total_field_id, field_id, "+", tot_row, ddd);

		}
		function fnc_check_machine_mixing_variable() {
			var machine_mixing_variable='<? echo $is_machine_mixing; ?>';
			var tot_rows = $("#tbl_list_search tbody tr").length - 1;
			var increment_counter=0;
			for (var x = 1; x <= tot_rows; x++) {
				var distribution_qnty = $('#txt_distribution_qnty_' + x).val() * 1;
				if(distribution_qnty>0)
				{
					increment_counter+=1;
				}	
			}
			if(machine_mixing_variable==2 && increment_counter>1)
			{
				//alert('Machine Mixing Variable NO');
				return 1;
			}
		}
		

		function fnc_close() {
			var save_string = '';
			var allMachineId = '';
			var allMachineNo = '';
			var tot_capacity = '';
			var tot_distribution_qnty = '';
			var min_date = '';
			var max_date = '';
			var hidden_prog_qnty = $('#hidden_prog_qnty').val();
			var updateId = $('#updateId').val();
			var tot_row = $("#tbl_list_search tbody tr").length - 1;

			for (var i = 1; i <= tot_row; i++) {
				var machineId = $('#txt_individual_id' + i).val();
				var machineNo = $('#txt_individual' + i).val();
				var capacity = $('#txt_capacity_' + i).val();
				var distributionQnty = $('#txt_distribution_qnty_' + i).val();
				var noOfDays = $('#txt_noOfDays_' + i).val();
				var startDate = $('#txt_startDate_' + i).val();
				var endDate = $('#txt_endDate_' + i).val();
				var dtls_id = $('#dtls_id_' + i).val();

				if (distributionQnty * 1 > 0) {
					if (save_string == "") {
						save_string = machineId + "_" + machineNo + "_" + capacity + "_" + distributionQnty + "_" + noOfDays + "_" + startDate + "_" + endDate + "_" + dtls_id;
						allMachineId = machineId;
						allMachineNo = machineNo;
					}
					else {
						save_string += "," + machineId + "_" + machineNo + "_" + capacity + "_" + distributionQnty + "_" + noOfDays + "_" + startDate + "_" + endDate + "_" + dtls_id;
						allMachineId += "," + machineId;
						allMachineNo += "," + machineNo;
					}

					if (min_date == '') {
						min_date = startDate;
					}

					if (date_compare(min_date, startDate) == false) {
						min_date = startDate;
					}

					if (date_compare(min_date, endDate) == false) {
						min_date = endDate;
					}

					if (max_date == '') {
						max_date = startDate;
					}

					if (date_compare(max_date, startDate) == true) {
						max_date = startDate;
					}

					if (date_compare(max_date, endDate) == true) {
						max_date = endDate;
					}

					tot_capacity = tot_capacity * 1 + capacity * 1;
					tot_distribution_qnty = tot_distribution_qnty * 1 + distributionQnty * 1;
				}
			}

			if(tot_distribution_qnty > hidden_prog_qnty){
				alert("Distribution quantity can not be greater than Program quantity");
				return;
			}else{
				$('#hidden_machine_id').val(allMachineId);
				$('#hidden_machine_no').val(allMachineNo);
				$('#save_string').val(save_string);
				$('#updateId').val(updateId);
				$('#hidden_machine_capacity').val(tot_capacity);
				$('#hidden_distribute_qnty').val(tot_distribution_qnty);
				$('#hidden_min_date').val(min_date);
				$('#hidden_max_date').val(max_date);
			}

			parent.emailwindow.hide();
		}

		function fn_add_date_field(row_no) {
			var distribute_qnty = $('#txt_distribution_qnty_' + row_no).val() * 1;

			if (distribute_qnty == 0 || distribute_qnty < 0) {
				alert("Please Insert Distribution Qnty First.");
				$('#txt_startDate_' + row_no).val('');
				$('#txt_distribution_qnty_' + row_no).focus();
				return;
			}

			if ($('#txt_startDate_' + row_no).val() != "") {
				var days_req = $('#txt_noOfDays_' + row_no).val();

				days_req = Math.ceil(days_req);
				if (days_req > 0) {
					days_req = days_req - 1;
					$("#txt_endDate_" + row_no).val(add_days($('#txt_startDate_' + row_no).val(), days_req));
				}

				var txt_startDate = $('#txt_startDate_' + row_no).val();
				var txt_endDate = $('#txt_endDate_' + row_no).val();
				var machine_id = $('#txt_individual_id' + row_no).val();

				var data = machine_id + "**" + txt_startDate + "**" + txt_endDate + "**" + '<? echo $update_dtls_id; ?>';
				var response = return_global_ajax_value(data, 'date_duplication_check', '', 'program_wise_mc_entry_controller');
				var response = response.split("_");
                //alert(response);return;
                if (response[0] != 0) {
                	alert("Date Overlaping for this machine. Dates Are (" + response[1] + ").");
                	$('#txt_startDate_' + row_no).val('');
                	$('#txt_endDate_' + row_no).val('');
                	return;
                }
            }
        }

        function calculate_noOfDays(row_no) {
        	var distribute_qnty = $('#txt_distribution_qnty_' + row_no).val();
        	var machine_capacity = $('#txt_capacity_' + row_no).val();

        	var days_req = distribute_qnty * 1 / machine_capacity * 1;
        	$('#txt_noOfDays_' + row_no).val(days_req.toFixed(2));

        	if (distribute_qnty * 1 > 0) {
        		fn_add_date_field(row_no);
        	}
        	else {
        		$('#txt_noOfDays_' + row_no).val('');
        		$('#txt_startDate_' + row_no).val('');
        		$('#txt_endDate_' + row_no).val('');
        	}
        }


        // declare bookedDays global
        var bookedDays = [];
		// perform initial json request for free days
		fn_machine_book_dates();

		$(document).ready(function()
		{
			// fairly standard configuration, importantly containing beforeShowDay and onChangeMonthYear custom methods
			$('.program_calendar').datepicker({
				dateFormat: 'dd-mm-yy',
				changeMonth: true,
				changeYear: true,
				beforeShowDay:highlightDays,
				onChangeMonthYear: fn_machine_book_dates
			});
		});


		function fn_machine_book_dates(row_no)
		{
			var machine_id = $('#txt_individual_id' + row_no).val();

			if(machine_id!="" && machine_id!="undefined")
			{
				var update_dtls_id = '<? echo $update_dtls_id; ?>';
				var data ={"machine_id":machine_id,"update_dtls_id":update_dtls_id}

				$.ajax({
					type: "POST",
					url: "program_wise_mc_entry_controller.php?action=machine_allready_book_dates",
					data: data,
					cache: false,
					dataType: "json",
					success: function(response_data){
						$.each(response_data, function(index, value) {
							if (value!= "") {
	  							bookedDays.push(value); // add this date to the bookedDays array
	  						}
	  					});
					}
				})
			}
		}


		function highlightDays(date)
		{
			for (var i = 0; i < bookedDays.length; i++)
			{
				if (bookedDays[i] == $.datepicker.formatDate('dd-mm-yy', date))
				{
					return [true, 'highlight', 'All ready book this date'];
				}
			}
			return [true,''];
		}



		function fnc_machine_entry(operation)
		{

			$("#hdn_operation").val(operation);	
			var save_string = '';
			var allMachineId = '';
			var allMachineNo = '';
			var tot_capacity = '';
			var tot_distribution_qnty = '';
			var min_date = '';
			var max_date = '';
			var hidden_prog_qnty = $('#hidden_prog_qnty').val();
			var tot_row = $("#tbl_list_search tbody tr").length - 1;

			for (var i = 1; i <= tot_row; i++) {
				var machineId = $('#txt_individual_id' + i).val();
				var machineNo = $('#txt_individual' + i).val();
				var capacity = $('#txt_capacity_' + i).val();
				var distributionQnty = $('#txt_distribution_qnty_' + i).val();
				var noOfDays = $('#txt_noOfDays_' + i).val();
				var startDate = $('#txt_startDate_' + i).val();
				var endDate = $('#txt_endDate_' + i).val();
				var dtls_id = $('#dtls_id_' + i).val();

				if (distributionQnty * 1 > 0) {
					if (save_string == "") {
						save_string = machineId + "_" + machineNo + "_" + capacity + "_" + distributionQnty + "_" + noOfDays + "_" + startDate + "_" + endDate + "_" + dtls_id;
						allMachineId = machineId;
						allMachineNo = machineNo;
					}
					else {
						save_string += "," + machineId + "_" + machineNo + "_" + capacity + "_" + distributionQnty + "_" + noOfDays + "_" + startDate + "_" + endDate + "_" + dtls_id;
						allMachineId += "," + machineId;
						allMachineNo += "," + machineNo;
					}

					if (min_date == '') {
						min_date = startDate;
					}

					if (date_compare(min_date, startDate) == false) {
						min_date = startDate;
					}

					if (date_compare(min_date, endDate) == false) {
						min_date = endDate;
					}

					if (max_date == '') {
						max_date = startDate;
					}

					if (date_compare(max_date, startDate) == true) {
						max_date = startDate;
					}

					if (date_compare(max_date, endDate) == true) {
						max_date = endDate;
					}

					tot_capacity = tot_capacity * 1 + capacity * 1;
					tot_distribution_qnty = tot_distribution_qnty * 1 + distributionQnty * 1;
				}
			}

			/*if(tot_distribution_qnty > hidden_prog_qnty){
				alert("Distribution quantity can not be greater than Program quantity");
				return;
			}else{
				$('#hidden_machine_id').val(allMachineId);
				$('#hidden_machine_no').val(allMachineNo);
				$('#save_string').val(save_string);
				$('#hidden_machine_capacity').val(tot_capacity);
				$('#hidden_distribute_qnty').val(tot_distribution_qnty);
				$('#hidden_min_date').val(min_date);
				$('#hidden_max_date').val(max_date);
			}*/

			

			
			//data = "action=save_update_delete&operation=" + operation + get_submitted_data_string('machine_id*txt_machine_capacity*txt_distribution_qnty*txt_start_date*txt_end_date*save_data*updateId*update_dtls_id', "../../") ;

			data = "action=save_update_delete&operation=" + operation + get_submitted_data_string('hidden_plan_id*hidden_prog_id*updateId*update_dtls_id', "../../")+ '&save_string=' + save_string ;
			
			//+'&sales_order_dtls_id=<? echo $sales_order_dtls_id; ?>' + '&pre_cost_id=<? echo $pre_cost_id; ?>' + '&pre_cost=<? echo $pre_cost; ?>' + '&hdn_booking_qnty=' + booking_qnty

			freeze_window(operation);
			//alert(data);return;
			http.open("POST", "program_wise_mc_entry_controller.php", true);
			http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_machine_entry_Reply_info;
		}

		function fnc_machine_entry_Reply_info()
		{
			if (http.readyState == 4)
			{
				var reponse = trim(http.responseText).split('**');
				show_msg(reponse[0]);

				if ((reponse[0] == 0 || reponse[0] == 1 || reponse[0] == 2))
				{
					
                	if(reponse[0] == 0 )
					{
                		alert("Save Success");
                	}
                	else if(reponse[0] == 1 )
                	{
                		alert("Update Success");
                	}
                	set_button_status(1, permission, 'fnc_machine_entry', 1);
                	release_freezing();
					
					$('#updateId').val(reponse[1]);
					
					
					
                   // $("#txt_program_qnty").val(progBalance.toFixed(2));
                    //$("#balanceProgramQnty").val(progBalance.toFixed(2));
				}
				
			}
		}
		



	</script>
</head>
<body>
	<div style="width:830px;">

		<? echo load_freeze_divs("../../../", $permission, 1); ?>
		<form name="searchwofrm" id="searchwofrm">
			<fieldset style="width:820px; margin-top:10px; margin-left:5px">
				<input type="text" name="save_string" id="save_string" class="text_boxes" value="">
				<input type="hidden" name="hidden_machine_id" id="hidden_machine_id" class="text_boxes" value="">
				<input type="hidden" name="hidden_machine_no" id="hidden_machine_no" class="text_boxes" value="">
				<input type="hidden" name="hidden_machine_capacity" id="hidden_machine_capacity" class="text_boxes" value="">
				<input type="hidden" name="hidden_distribute_qnty" id="hidden_distribute_qnty" class="text_boxes" value="">
				<input type="hidden" name="hidden_min_date" id="hidden_min_date" class="text_boxes" value="">
				<input type="hidden" name="hidden_max_date" id="hidden_max_date" class="text_boxes" value="">
				<input type="text" name="hidden_plan_id" id="hidden_plan_id" class="text_boxes_numeric" value="<? echo $planId; ?>">
                <input type="text" name="hidden_prog_id" id="hidden_prog_id" class="text_boxes_numeric" value="<? echo $update_dtls_id; ?>">
                <input type="text" name="hidden_prog_qnty" id="hidden_prog_qnty" class="text_boxes_numeric" value="<? echo $txt_program_qnty; ?>">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
					<thead>
						<th width="40">SL</th>
						<th width="80">Floor</th>
						<th width="60">Machine No</th>
						<th width="60">Dia</th>
						<th width="60">GG</th>
						<th width="80">Group</th>
						<th width="90">Capacity</th>
						<th width="90">Distribution Qnty</th>
						<th width="60">No. Of Days</th>
						<th width="80">Start Date</th>
						<th>End Date</th>
					</thead>
				</table>
				<div style="width:818px; overflow-y:scroll; max-height:220px;" id="buyer_list_view">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table"
					id="tbl_list_search">
					<tbody>
						<?
						$qnty_array = array();
						$save_string = explode(",", $save_string);
						for ($i = 0; $i < count($save_string); $i++)
						{
							$machine_wise_data = explode("_", $save_string[$i]);
							$machine_id = $machine_wise_data[0];
							$capacity = $machine_wise_data[2];
							$distribution_qnty = $machine_wise_data[3];
							$noOfDays = $machine_wise_data[4];
							$startDate = $machine_wise_data[5];
							$endDate = $machine_wise_data[6];
							$dtls_id = $machine_wise_data[7];

							$qnty_array[$machine_id]['capacity'] = $capacity;
							$qnty_array[$machine_id]['distribution'] = $distribution_qnty;
							$qnty_array[$machine_id]['noOfDays'] = $noOfDays;
							$qnty_array[$machine_id]['startDate'] = $startDate;
							$qnty_array[$machine_id]['endDate'] = $endDate;
							$qnty_array[$machine_id]['dtls_id'] = $dtls_id;
						}

						$floor_arr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');
						if($txt_machine_gg!="")
						{
							$machinCond = "and gauge='$txt_machine_gg'";
						}
						
						/*
						|---------------------------------------------------------------
						| if textile sales maintain is no then
						| the machine no will be party machine no otherwise
						| LC company michine no
						|---------------------------------------------------------------
						*/						
						$variable_textile_sales_maintain = sql_select("select production_entry from variable_settings_production where company_name = ".$companyID." and variable_list=66 and status_active=1");
						if($variable_textile_sales_maintain[0][csf('production_entry')] ==2)
						{
							$companyID = $cbo_knitting_party;
						}

						$vs_sql = "select id, machine_no, dia_width, gauge, machine_group, prod_capacity, floor_id from lib_machine_name where company_id=$companyID and category_id=1 and status_active=1 and is_deleted=0 $machinCond order by seq_no";// and dia_width='$txt_machine_dia'
						$vs_result = sql_select($vs_sql);
						$i = 1;
						$tot_capacity = 0;
						$tot_distribution_qnty = 0;
						foreach ($vs_result as $row)
						{
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";

							$capacity = $qnty_array[$row[csf('id')]]['capacity'];
							if ($capacity == "")
							{
								$capacity = $row[csf('prod_capacity')];
							}

							$distribution_qnty = $qnty_array[$row[csf('id')]]['distribution'];
							if($distribution_qnty > 0) $bgcolor = "yellow"; else $bgcolor = $bgcolor;

							$noOfDays = $qnty_array[$row[csf('id')]]['noOfDays'];
							$startDate = $qnty_array[$row[csf('id')]]['startDate'];
							$endDate = $qnty_array[$row[csf('id')]]['endDate'];
							$dtls_id = $qnty_array[$row[csf('id')]]['dtls_id'];

							$tot_capacity += $capacity;
							$tot_distribution_qnty += $distribution_qnty;

							?>
							<tr valign="middle" bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
								<td width="40" align="center"><? echo $i; ?>
								<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>"
								value="<? echo $row[csf('id')]; ?>"/>
								<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>"
								value="<? echo $row[csf('machine_no')]; ?>"/>
							</td>
							<td width="80"><p><? echo $floor_arr[$row[csf('floor_id')]]; ?></p></td>
							<td width="60"><p><? echo $row[csf('machine_no')]; ?></p></td>
							<td width="60" align="center"><p><? echo $row[csf('dia_width')]; ?></p></td>
							<td width="60" align="center"><p><? echo $row[csf('gauge')]; ?></p></td>
							<td width="80" align="center"><p><? echo $row[csf('machine_group')]; ?></p></td>
							<td width="90" align="center">
								<input type="text" name="txt_capacity[]" id="txt_capacity_<? echo $i; ?>"
								class="text_boxes_numeric" style="width:75px" value="<? echo $capacity; ?>"
								onKeyUp="calculate_total_qnty('txt_capacity_','txt_total_capacity');calculate_noOfDays(<? echo $i; ?>);"/>
							</td>
							<td align="center" width="90">
								<input type="text" name="txt_distribution_qnty[]"
								id="txt_distribution_qnty_<? echo $i; ?>" class="text_boxes_numeric"
								style="width:75px" value="<? echo $distribution_qnty; ?>"
								onKeyUp="calculate_qnty(<? echo $i; ?>);calculate_noOfDays(<? echo $i; ?>);"/>
							</td>
							<td align="center" width="60">
								<input type="text" name="txt_noOfDays[]" id="txt_noOfDays_<? echo $i; ?>"
								class="text_boxes_numeric" style="width:45px" value="<? echo $noOfDays; ?>"
								onKeyUp="calculate_noOfDays(<? echo $i; ?>);" disabled="disabled"/>
							</td>
							<td align="center" width="80">
								<input type="text" name="txt_startDate[]" id="txt_startDate_<? echo $i; ?>"
								class="program_calendar" style="width:67px" value="<? echo $startDate; ?>"
								onChange="fn_add_date_field(<? echo $i; ?>);" onClick="fn_machine_book_dates(<? echo $i; ?>)"/>
								<!-- onChange="fn_add_date_field(<? //echo $i; ?>);" -->
							</td>
							<td align="center">
								<input type="text" name="txt_endDate[]" id="txt_endDate_<? echo $i; ?>"
								class="datepicker" style="width:67px" value="<? echo $endDate; ?>"
								disabled="disabled"/>
								<input type="hidden" name="dtls_id[]" id="dtls_id_<? echo $i; ?>"
								value="<? echo $dtls_id; ?>" disabled="disabled"/>
							</td>
						</tr>
						<?
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="6" align="right"><b>Total</b></th>
						<th align="center"><input type="text" name="txt_total_capacity" id="txt_total_capacity"
							class="text_boxes_numeric" style="width:75px" readonly
							disabled="disabled" value="<? echo $tot_capacity; ?>"/></th>
							<th align="center"><input type="text" name="txt_total_distribution_qnty"
								id="txt_total_distribution_qnty" class="text_boxes_numeric"
								style="width:75px" readonly disabled="disabled"
								value="<? echo $tot_distribution_qnty; ?>"/></th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
							</tr>
						</tfoot>
					</table>
				</div>
				<table width="700" id="tbl_close">
					<tr>
						<td colspan="4" align="right" class="button_container">
	                        <?
	                        if(str_replace("'", '', $updated_id)>0)
	                        {
	                        	echo load_submit_buttons($permission, "fnc_machine_entry", 1, 0, "", 1);
	                        }
	                        else
	                        {
	                        	echo load_submit_buttons($permission, "fnc_machine_entry", 0, 0, "", 1);
	                        }
	                        ?>

	                        <input type="hidden" name="save_data" id="save_data" class="text_boxes">
	                        <input type="hidden" name="updateId" id="updateId" class="text_boxes"
	                        value="<? echo trim(str_replace("'", '', $plan_id)); ?>">
	                        <input type="hidden" name="update_dtls_id" id="update_dtls_id" class="text_boxes">
	                        <input type="hidden" name="hdn_operation" id="hdn_operation" class="text_boxes">
	                       

	                       

                    	</td >
						<td align="center" valign="top" class="button_container">
							<input type="button" name="close" class="formbutton" value="Close" id="main_close"
							onClick="fnc_close();" style="width:100px"/>

						</td>
					</tr>
				</table>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action == "date_duplication_check")
{
	$data = explode("**", $data);
	$machine_id = $data[0];
	if ($db_type == 0) {
		$startDate = change_date_format(trim($data[1]), "yyyy-mm-dd", "");
		$endDate = change_date_format(trim($data[2]), "yyyy-mm-dd", "");
	} else {
		$startDate = change_date_format(trim($data[1]), '', '', 1);
		$endDate = change_date_format(trim($data[2]), '', '', 1);
	}
	$update_dtls_id = $data[3];

	if ($update_dtls_id == "") {
		$sql = "select distribution_date, sum(days_complete) as days_complete from ppl_entry_machine_datewise where machine_id='$machine_id' and distribution_date between '$startDate' and '$endDate' group by distribution_date";
	} else {
		$sql = "select distribution_date, sum(days_complete) as days_complete from ppl_entry_machine_datewise where machine_id='$machine_id' and distribution_date between '$startDate' and '$endDate' and dtls_id<>$update_dtls_id group by distribution_date";
	}
	//echo $sql;die;
	$data_array = sql_select($sql);
	$data = '';
	if (count($data_array) > 0) {
		foreach ($data_array as $row) {
			if ($row[csf('days_complete')] >= 1) {
				if ($data == '') $data = change_date_format($row[csf('distribution_date')]); else $data .= "," . change_date_format($row[csf('distribution_date')]);
			}
		}

		if ($data == '') echo "0_"; else echo "1" . "_" . $data;
	} else {
		echo "0_";
	}

	exit();
}

if ($action == "machine_allready_book_dates")
{
	extract($_REQUEST);
	if ($update_dtls_id == "") {
		$sql = "select distribution_date, sum(days_complete) as days_complete from ppl_entry_machine_datewise where machine_id='$machine_id' group by distribution_date";
	} else {
		$sql = "select distribution_date, sum(days_complete) as days_complete from ppl_entry_machine_datewise where machine_id='$machine_id' and dtls_id<>$update_dtls_id group by distribution_date";
	}
	//echo $sql;die;
	$data_array = sql_select($sql);
	if (count($data_array) > 0) {
		$dateslist = array();
		foreach ($data_array as $row) {
			if ($row[csf('days_complete')] >= 1) {
				$dateslist[] = date("d-m-Y", strtotime($row[csf('distribution_date')]));
			}
		}
	}

	if(!empty($dateslist))
	{
		header('Content-type: application/json');
		echo json_encode($dateslist);
	}
	exit();
}

if ($action == "style_ref_search_popup")
{
	echo load_html_head_contents("Style Reference / Job No. Info", "../../", 1, 1, '', '', '');
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
			<fieldset style="width:600px;">
				<table width="590" cellspacing="0" cellpadding="0" border="1" rules="all" align="center"
				class="rpt_table" id="tbl_list">
				<thead>
					<th>PO Buyer</th>
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
							echo create_drop_down("cbo_po_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$companyID' $buyer_id_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
							?>
						</td>
						<td align="center">
							<?
							$search_by_arr = array(1 => "Sales Order No", 2 => "Style Ref");
							$dd = "change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down("cbo_search_by", 110, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
							?>
						</td>
						<td align="center" id="search_by_td">
							<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
							id="txt_search_common"/>
						</td>
						<td align="center">
							<input type="button" name="button" class="formbutton" value="Show"
							onClick="show_list_view ('<? echo $companyID; ?>**' +'<? echo $buyerID; ?>'+'**'+document.getElementById('cbo_po_buyer_name').value + '**'+'<? echo $within_group; ?>**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**', 'create_job_search_list_view', 'search_div', 'program_wise_mc_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1)');"
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
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
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
		} else {
			$search_field_cond = " and LOWER(a.style_ref_no) like LOWER('" . $search_string . "%')";
		}
	}

	if ($within_group == 0) $within_group_cond = ""; else $within_group_cond = " and within_group=$within_group";
	if ($buyer_id == 0) $buyer_id_cond = ""; else $buyer_id_cond = " and a.buyer_id=$buyer_id";
	if ($po_buyer_id == 0) {
		if ($_SESSION['logic_erp']["buyer_id"] != "") {
			$po_buyer_id_cond = " and b.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
		} else {
			$po_buyer_id_cond = "";
		}
	} else {
		$po_buyer_id_cond = " and b.buyer_id=$po_buyer_id";
	}

	if ($db_type == 0) $year_field = "YEAR(a.insert_date) as year";
	else if ($db_type == 2) $year_field = "to_char(a.insert_date,'YYYY') as year";
	else $year_field = "";//defined Later
	if ($within_group == 1) {
		$sql = " select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id, b.buyer_id po_buyer,b.booking_no_prefix_num from fabric_sales_order_mst a,wo_booking_mst b where a.sales_booking_no = b.booking_no and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id and a.within_group=$within_group $search_field_cond $po_buyer_id_cond and a.buyer_id=$buyer_id and fabric_source in(1,2)
		union all
		select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id, b.buyer_id po_buyer,b.booking_no_prefix_num from fabric_sales_order_mst a,wo_non_ord_samp_booking_mst b,wo_non_ord_samp_booking_dtls c where a.sales_booking_no = b.booking_no and b.booking_no=c.booking_no and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id and a.within_group=$within_group $search_field_cond $po_buyer_id_cond  and a.buyer_id=$buyer_id and (b.fabric_source in(1,2) or c.fabric_source in(1,2)) group by a.id, a.insert_date, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id, b.buyer_id,b.booking_no_prefix_num";

	} else {
		$sql = " select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no booking_no_prefix_num, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id  and a.within_group=$within_group $search_field_cond order by a.id";
	}
	//echo $sql;//die;
	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="70">Sales Order No</th>
			<th width="60">Year</th>
			<th width="80">Within Group</th>
			<th width="70">PO Buyer</th>
			<th width="70">PO Company</th>
			<th width="120">Sales/ Booking No</th>
			<th>Style Ref.</th>
		</thead>
	</table>
	<div style="width:600px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="580" class="rpt_table"
		id="tbl_list_search">
		<?
		$i = 1;
		foreach ($result as $row) {
			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

			if ($row[csf('within_group')] == 1)
				$buyer = $company_arr[$row[csf('buyer_id')]];
			else
				$buyer = $buyer_arr[$row[csf('buyer_id')]];
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
				onClick="js_set_value(<? echo $i; ?>);" id="search<? echo $i; ?>">
				<td width="40"><? echo $i; ?>
				<input type="hidden" name="txt_job_id" id="txt_job_id<?php echo $i ?>"
				value="<? echo $row[csf('id')]; ?>"/>
				<input type="hidden" name="txt_job_no" id="txt_job_no<?php echo $i ?>"
				value="<? echo $row[csf('job_no')]; ?>"/>
			</td>
			<td width="70"><p>&nbsp;<? echo $row[csf('job_no_prefix_num')]; ?></p></td>
			<td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
			<td width="80" align="center"><p><? echo $yes_no[$row[csf('within_group')]]; ?>&nbsp;</p></td>
			<td width="70"><p><? echo $buyer_arr[$row[csf('po_buyer')]]; ?>&nbsp;</p></td>
			<td width="70" align="center"><p><? echo $buyer; ?>&nbsp;</p></td>
			<td width="120" align="center"><p><? echo $row[csf('booking_no_prefix_num')]; ?></p></td>
			<td><p><? echo $row[csf('style_ref_no')]; ?></p></td>
		</tr>
		<?
		$i++;
	}
	?>
</table>
</div>
<table width="600" cellspacing="0" cellpadding="0" style="border:none" align="center">
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


if ($action == "internal_ref_no_search_popup")
{
	echo load_html_head_contents("Booking Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(internal_ref)
		{
			$('#hidden_internal_ref').val(internal_ref);
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
	<div align="center" style="width:750px;">
		<form name="searchwofrm" id="searchwofrm" autocomplete=off>
			<fieldset style="width:100%;">
				<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" width="835" class="rpt_table" border="1" rules="all">
					<thead>
						<th>Po Buyer</th>
						<th>Booking Date</th>
						<th>Booking Type</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="150">Please Enter Booking No</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:90px"
							class="formbutton"/>
							<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes"
							value="<? echo $companyID; ?>">
							<input type="hidden" name="cbo_within_group" id="cbo_within_group" class="text_boxes"
							value="<? echo $cbo_within_group; ?>">
							<input type="hidden" name="hidden_internal_ref" id="hidden_internal_ref" class="text_boxes"
							value="">
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
							$booking_type_arr = array(1 => "Fabric Booking", 2 => "Sample Booking");
							echo create_drop_down("cbo_booking_type", 100, $booking_type_arr, "", 0, '', '', '');
							?>
						</td>
						<td align="center">
							<?
							$search_by_arr = array(1 => "Booking No", 2 => "Job No", 3 => "IR/IB");
							$dd = "change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
							echo create_drop_down("cbo_search_by", 100, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
							?>
						</td>
						<td align="center" id="search_by_td">
							<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
							id="txt_search_common"/>
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show"
							onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_po_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_booking_type').value, 'create_internal_ref_search_list_view', 'search_div', 'program_wise_mc_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
							style="width:90px;"/>
						</td>
					</tr>
					<tr>
						<td colspan="6" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
					</tr>
				</table>
				<div style="width:100%; margin-top:5px; margin-left:3px" id="search_div" align="left"></div>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action == "create_internal_ref_search_list_view")
{
	$data = explode("_", $data);
	$search_string = "%" . trim($data[0]) . "%";
	$search_by = $data[1];
	$company_id = $data[2];
	$buyer_id = $data[3];
	$date_from = trim($data[4]);
	$date_to = trim($data[5]);
	$cbo_within_group = trim($data[6]);
	$booking_type = trim($data[7]);

	if ($buyer_id == 0)
	{
		$buyer_id_cond = "";
	}
	else
	{
		$buyer_id_cond = " and a.buyer_id=$buyer_id";
	}

	$search_field_cond = "";
	$search_field_cond_2 = "";

	if (trim($data[0]) != "")
	{
		if ($search_by == 1)
		{
			if ($cbo_within_group == 1)
			{
				$search_field_cond = "and a.booking_no like '$search_string'";
				$search_field_cond_2 = "and a.booking_no like '$search_string'";
			}
			else
			{
				$search_field_cond = "and c.sales_booking_no like '$search_string'";
				$search_field_cond_2 = "and b.sales_booking_no like '$search_string'";
			}
		}
		else if($search_by == 3 || $search_by == 1) {
				//for internal ref.
				$internalRef_cond = '';$booking_nos_cond = '';$booking_nos_cond2 = '';
				$internalRef_cond = " and a.grouping like '$search_string'";
				$sql_bookings=sql_select("select b.booking_no,a.job_no_mst,a.grouping from wo_po_break_down a,wo_booking_dtls b where a.job_no_mst=b.job_no and a.id=b.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $internalRef_cond group by b.booking_no,a.job_no_mst,a.grouping");
				$booking_nos="";$bookingArrChk=array();$internalRefArr=array();
				foreach ($sql_bookings as $row) {
					$internalRefArr[$row[csf('booking_no')]][$row[csf('job_no_mst')]]['grouping']=$row[csf('grouping')];
					if($bookingArrChk[$row[csf('booking_no')]]!=$row[csf('booking_no')])
					{
						$booking_nos.="'".$row[csf('booking_no')]."',";
						$bookingArrChk[$row[csf('booking_no')]]=$row[csf('booking_no')];
					}
				}
				$booking_nos=chop($booking_nos,",");
				$booking_nos_cond = "and a.booking_no in($booking_nos)";
				$booking_nos_cond2 = "and c.sales_booking_no in($booking_nos)";
				unset($sql_bookings);
		}
		else
		{
			$search_field_cond = "and a.job_no like '$search_string'";
			//for internal ref.
			$internalRef_cond = '';$booking_nos_cond = '';$booking_nos_cond2 = '';
			$internalRef_cond = " and a.job_no_mst like '$search_string'";
			$sql_bookings=sql_select("select b.booking_no,a.job_no_mst,a.grouping from wo_po_break_down a,wo_booking_dtls b where a.job_no_mst=b.job_no and a.id=b.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $internalRef_cond group by b.booking_no,a.job_no_mst,a.grouping");
			$booking_nos="";$bookingArrChk=array();$internalRefArr=array();
			foreach ($sql_bookings as $row) {
				$internalRefArr[$row[csf('booking_no')]][$row[csf('job_no_mst')]]['grouping']=$row[csf('grouping')];
				if($bookingArrChk[$row[csf('booking_no')]]!=$row[csf('booking_no')])
				{
					$booking_nos.="'".$row[csf('booking_no')]."',";
					$bookingArrChk[$row[csf('booking_no')]]=$row[csf('booking_no')];
				}
			}
			$booking_nos=chop($booking_nos,",");
			$booking_nos_cond = "and a.booking_no in($booking_nos)";
			$booking_nos_cond2 = "and c.sales_booking_no in($booking_nos)";
			unset($sql_bookings);
		}
	}
	/*else
	{
			//for internal ref.
			$booking_nos_cond = '';$booking_nos_cond2 = '';
			$sql_bookings=sql_select("select b.booking_no,a.job_no_mst,a.grouping from wo_po_break_down a,wo_booking_dtls b where a.job_no_mst=b.job_no and a.id=b.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.booking_no,a.job_no_mst,a.grouping");
			$booking_nos="";$bookingArrChk=array();$internalRefArr=array();
			foreach ($sql_bookings as $row) {
				$internalRefArr[$row[csf('booking_no')]][$row[csf('job_no_mst')]]['grouping']=$row[csf('grouping')];
				if($bookingArrChk[$row[csf('booking_no')]]!=$row[csf('booking_no')])
				{
					$booking_nos.="'".$row[csf('booking_no')]."',";
					$bookingArrChk[$row[csf('booking_no')]]=$row[csf('booking_no')];
				}
			}
			$booking_nos=chop($booking_nos,",");
			$booking_nos_cond = "and a.booking_no in($booking_nos)";
			$booking_nos_cond2 = "and c.sales_booking_no in($booking_nos)";
			unset($sql_bookings);
	}
*/
	$date_cond = '';
	if ($cbo_within_group == 1)
	{

	}
	$date_field = ($cbo_within_group == 2) ? "c.booking_date" : "a.booking_date";
	if ($date_from != "" && $date_to != "")
	{
		if ($db_type == 0)
		{
			$date_cond = "and $date_field between '" . change_date_format(trim($date_from), "yyyy-mm-dd", "-") . "' and '" . change_date_format(trim($date_to), "yyyy-mm-dd", "-") . "'";
		}
		else
		{
			$date_cond = "and $date_field between '" . change_date_format(trim($date_from), '', '', 1) . "' and '" . change_date_format(trim($date_to), '', '', 1) . "'";
		}
	}

	if ($cbo_within_group == 1)
	{
		//for fabric booking
		if($booking_type == 1)
		{
			$sql = "select a.id, a.booking_no, a.booking_date, a.buyer_id, a.company_id, a.delivery_date, a.currency_id, a.po_break_down_id, b.job_no, b.style_ref_no from wo_booking_mst a, wo_po_details_master b, fabric_sales_order_mst c where a.job_no=b.job_no and a.booking_no=c.sales_booking_no and a.supplier_id=$company_id and a.pay_mode=5 and a.fabric_source in(1,2) and a.status_active =1 and a.is_deleted =0 and a.item_category=2 $buyer_id_cond $search_field_cond $date_cond $booking_nos_cond group by a.id, a.booking_no, a.booking_date, a.buyer_id, a.company_id, a.delivery_date, a.currency_id, a.po_break_down_id, b.job_no,b.style_ref_no";
		}
		//for sample booking
		else
		{
			$sql = "select a.id, a.booking_no, a.booking_date, a.buyer_id, a.company_id, a.delivery_date, a.currency_id, a.po_break_down_id, a.job_no, d.style_ref_no from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, fabric_sales_order_mst c, sample_development_mst d where a.booking_no=b.booking_no and a.booking_no=c.sales_booking_no and b.style_id = d.id and a.supplier_id=$company_id and a.pay_mode=5 and a.fabric_source in(1,2) and a.status_active =1 and a.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and a.item_category=2 $buyer_id_cond $search_field_cond $date_cond $booking_nos_cond group by a.id, a.booking_no, a.booking_date, a.buyer_id, a.company_id, a.delivery_date, a.currency_id, a.po_break_down_id, a.job_no, d.style_ref_no";
		}
	}
	else
	{
		$sql = "select c.id, c.sales_booking_no booking_no, c.booking_date,c.buyer_id, c.company_id,c.job_no, c.style_ref_no from fabric_sales_order_mst c where c.company_id=$company_id and c.status_active =1 and c.is_deleted=0 $date_cond $search_field_cond $booking_nos_cond2 and c.within_group=2 group by c.id, c.sales_booking_no, c.booking_date, c.buyer_id, c.company_id, c.job_no, c.style_ref_no";
	}
	//echo $sql;
	
	$result = sql_select($sql);
	$poArr = array();
	$buyerArr = array();
	$jobsArrChks = array();
	$jobs_nos="";
	foreach ($result as $row)
	{
		//for buyer
		$buyerArr[$row[csf('buyer_id')]] = $row[csf('buyer_id')];
		
		//for po
		if ($row[csf('po_break_down_id')] != "")
		{
			$po_ids = explode(",", $row[csf('po_break_down_id')]);
			foreach ($po_ids as $po_id)
			{
				$poArr[$po_id] = $po_id;
			}
		}

		if ($row[csf('job_no')] != "")
		{
			if($jobsArrChks[$row[csf('job_no')]]!=$row[csf('job_no')])
			{
				$jobs_nos.="'".$row[csf('job_no')]."',";
				$jobsArrChks[$row[csf('job_no')]]=$row[csf('job_no')];
			}	
		}
	}

	
	
	//for partial
	if($db_type==0)
	{
		$sql_partial = "select a.id, a.booking_no, a.booking_date,a.buyer_id, a.company_id, a.delivery_date, a.currency_id, group_concat(c.po_break_down_id) as po_break_down_id, c.job_no from wo_booking_mst a, wo_booking_dtls c, fabric_sales_order_mst b where a.booking_no=c.booking_no and a.booking_no=b.sales_booking_no and a.status_active =1 and a.is_deleted =0 and a.pay_mode=5 and a.fabric_source in(1,2) and a.item_category=2 $buyer_id_cond $search_field_cond_2 $date_cond $booking_nos_cond and a.entry_form=108 group by a.id, a.booking_no,a.booking_date,a.buyer_id,a.company_id,a.delivery_date,a.currency_id,c.job_no";
	}
	else
	{
		//for fabric booking
		if($booking_type == 1)
		{
			$sql_partial = "select a.id, a.booking_no, a.booking_date,a.buyer_id, a.company_id, a.delivery_date, a.currency_id, listagg(c.po_break_down_id, ',') within group (order by c.po_break_down_id) as po_break_down_id, c.job_no from wo_booking_mst a, wo_booking_dtls c, fabric_sales_order_mst b where a.booking_no=c.booking_no and a.booking_no=b.sales_booking_no and a.status_active =1 and a.is_deleted =0 and a.pay_mode=5 and a.fabric_source in(1,2) and a.item_category=2 $buyer_id_cond $search_field_cond_2 $date_cond $booking_nos_cond and a.entry_form=108 group by a.id, a.booking_no, a.booking_date, a.buyer_id, a.company_id, a.delivery_date, a.currency_id, c.job_no";
		}
		//for sample booking
		else
		{
			//$sql_partial = "select a.id, a.booking_no, a.booking_date,a.buyer_id, a.company_id, a.delivery_date, a.currency_id, listagg(c.po_break_down_id, ',') within group (order by c.po_break_down_id) as po_break_down_id, c.job_no from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls c, fabric_sales_order_mst b where a.booking_no=c.booking_no and a.booking_no=b.sales_booking_no and a.status_active =1 and a.is_deleted =0 and a.pay_mode=5 and a.fabric_source in(1,2) and a.item_category=2 $buyer_id_cond $search_field_cond_2 $date_cond and a.entry_form=108 group by a.id, a.booking_no, a.booking_date, a.buyer_id, a.company_id, a.delivery_date, a.currency_id, c.job_no";
			$sql_partial = "select a.id, a.booking_no, a.booking_date,a.buyer_id, a.company_id, a.delivery_date, a.currency_id from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls c, fabric_sales_order_mst b where a.booking_no=c.booking_no and a.booking_no=b.sales_booking_no and a.status_active =1 and a.is_deleted =0 and a.pay_mode=5 and a.fabric_source in(1,2) and a.item_category=2 $buyer_id_cond $search_field_cond_2 $date_cond $booking_nos_cond  group by a.id, a.booking_no, a.booking_date, a.buyer_id, a.company_id, a.delivery_date, a.currency_id";
		}
	}
	//echo $sql_partial;
	$result_partial = sql_select($sql_partial);
	foreach ($result_partial as $row)
	{
		//for buyer
		$buyerArr[$row[csf('buyer_id')]] = $row[csf('buyer_id')];
		
		//for po
		if ($row[csf('po_break_down_id')] != "")
		{
			$po_ids = explode(",", $row[csf('po_break_down_id')]);
			foreach ($po_ids as $po_id)
			{
				$poArr[$po_id] = $po_id;
			}
		}
		if ($row[csf('job_no')] != "")
		{
			if($jobsArrChks[$row[csf('job_no')]]!=$row[csf('job_no')])
			{
				$jobs_nos.="'".$row[csf('job_no')]."',";
				$jobsArrChks[$row[csf('job_no')]]=$row[csf('job_no')];
			}	
		}
	}
	//echo "<pre>";
	//print_r($buyerArr);
	$jobs_nos=chop($jobs_nos,",");
	if (trim($data[0]) == "")
	{
		//for internal ref.
		$sql_bookings=sql_select("select b.booking_no,a.job_no_mst,a.grouping from wo_po_break_down a,wo_booking_dtls b where a.job_no_mst=b.job_no and a.id=b.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no_mst in($jobs_nos) group by b.booking_no,a.job_no_mst,a.grouping");
		$internalRefArr=array();
		foreach ($sql_bookings as $row) {
			$internalRefArr[$row[csf('booking_no')]][$row[csf('job_no_mst')]]['grouping']=$row[csf('grouping')];			
		}
		unset($sql_bookings);
	}
	
	//for company details
	$company_arr = return_library_array("select id,company_short_name from lib_company", 'id', 'company_short_name');
	
	//for buyer details
	//$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$buyer_arr = array();
	if(!empty($buyerArr))
	{
		$buyer_arr = return_library_array("select id, buyer_name from lib_buyer where 1=1".where_con_using_array($buyerArr,0,'id'), "id", "buyer_name");
	}
	
	//for buyer details
	//$po_arr = return_library_array("select id, po_number from wo_po_break_down", "id", "buyer_name");
	$po_arr = array();
	if(!empty($poArr))
	{
		$po_arr = return_library_array("select id, po_number from wo_po_break_down where 1=1".where_con_using_array($poArr,0,'id'), "id", "buyer_name");
	}
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="840" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="80">PO Buyer</th>
			<th width="120">Booking No</th>
			<th width="90">Job No</th>
			<th width="120">Style Ref.</th>
			<th width="80">Booking Date</th>
			<th width="100">IR/IB</th>
			<th>PO No.</th>
		</thead>
	</table>
	<div style="width:840px; max-height:270px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table"
		id="tbl_list_search">
		<?
		$i = 1;
		$j = 1;
		foreach ($result as $row)
		{
			if ($i % 2 == 0)
				$bgcolor = "#E9F3FF";
			else
				$bgcolor = "#FFFFFF";

			if ($row[csf('po_break_down_id')] != "")
			{
				$po_no = '';
				$po_ids = explode(",", $row[csf('po_break_down_id')]);
				foreach ($po_ids as $po_id)
				{
					if ($po_no == "")
						$po_no = $po_arr[$po_id];
					else
						$po_no .= "," . $po_arr[$po_id];
				}
			}
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
				onClick="js_set_value('<? echo $internalRefArr[$row[csf('booking_no')]][$row[csf('job_no')]]['grouping']; ?>')">
				<td width="40"><? echo $i; ?></td>
				<td width="80" align="center"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
				<td width="120"><p><? echo $row[csf('booking_no')]; ?></p></td>
				<td width="90" align="center"><p><? echo $row[csf('job_no')]; ?>&nbsp;</p></td>
				<td width="120"><p><? echo $row[csf('style_ref_no')]; ?>&nbsp;</p></td>
				<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
				<td width="100"><p><? echo $internalRefArr[$row[csf('booking_no')]][$row[csf('job_no')]]['grouping']; ?></p></td>
				<td><p><? echo $po_no; ?>&nbsp;</p></td>
			</tr>
			<?
			$i++;
		}

		//for partial
		foreach ($result_partial as $row)
		{
			if ($j % 2 == 0)
				$bgcolor = "#E9F3FF";
			else
				$bgcolor = "#FFFFFF";

			if ($row[csf('po_break_down_id')] != "")
			{
				$po_no = '';
				$po_ids = array_unique(explode(",", $row[csf('po_break_down_id')]));
				foreach ($po_ids as $po_id)
				{
					if ($po_no == "")
						$po_no = $po_arr[$po_id];
					else
						$po_no .= "," . $po_arr[$po_id];
				}
			}
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
				onClick="js_set_value('<? echo $internalRefArr[$row[csf('booking_no')]][$row[csf('job_no')]]['grouping']; ?>')">
				<td width="40"><? echo $j; ?></td>
				<td width="80" align="center"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
				<td width="120"><p><? echo $row[csf('booking_no')]; ?></p></td>
				<td width="90" align="center"><p><? echo $row[csf('job_no')]; ?>&nbsp;</p></td>
				<td width="120"><p><? echo $row[csf('style_ref_no')]; ?>&nbsp;</p></td>
				<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
				<td width="100"><p><? echo $internalRefArr[$row[csf('booking_no')]][$row[csf('job_no')]]['grouping']; ?></p></td>
				<td><p><? echo $po_no; ?>&nbsp;</p></td>
			</tr>
			<?
			$j++;
		}
		?>
	</table>
</div>
<?
exit();
}


if ($action == "booking_no_search_popup")
{
	echo load_html_head_contents("Booking Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(booking_no)
		{
			$('#hidden_booking_no').val(booking_no);
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
	<div align="center" style="width:750px;">
		<form name="searchwofrm" id="searchwofrm" autocomplete=off>
			<fieldset style="width:100%;">
				<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" width="835" class="rpt_table" border="1" rules="all">
					<thead>
						<th>Po Buyer</th>
						<th>Booking Date</th>
						<th>Booking Type</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="150">Please Enter Booking No</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:90px"
							class="formbutton"/>
							<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes"
							value="<? echo $companyID; ?>">
							<input type="hidden" name="cbo_within_group" id="cbo_within_group" class="text_boxes"
							value="<? echo $cbo_within_group; ?>">
							<input type="hidden" name="hidden_booking_no" id="hidden_booking_no" class="text_boxes"
							value="">
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
							$booking_type_arr = array(1 => "Fabric Booking", 2 => "Sample Booking");
							echo create_drop_down("cbo_booking_type", 100, $booking_type_arr, "", 0, '', '', '');
							?>
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
							onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_po_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_booking_type').value, 'create_booking_search_list_view', 'search_div', 'program_wise_mc_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
							style="width:90px;"/>
						</td>
					</tr>
					<tr>
						<td colspan="6" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
					</tr>
				</table>
				<div style="width:100%; margin-top:5px; margin-left:3px" id="search_div" align="left"></div>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action == "create_booking_search_list_view")
{
	$data = explode("_", $data);
	$search_string = "%" . trim($data[0]) . "%";
	$search_by = $data[1];
	$company_id = $data[2];
	$buyer_id = $data[3];
	$date_from = trim($data[4]);
	$date_to = trim($data[5]);
	$cbo_within_group = trim($data[6]);
	$booking_type = trim($data[7]);

	if ($buyer_id == 0)
	{
		$buyer_id_cond = "";
	}
	else
	{
		$buyer_id_cond = " and a.buyer_id=$buyer_id";
	}

	$search_field_cond = "";
	$search_field_cond_2 = "";

	if (trim($data[0]) != "")
	{
		if ($search_by == 1)
		{
			if ($cbo_within_group == 1)
			{
				$search_field_cond = "and a.booking_no like '$search_string'";
				$search_field_cond_2 = "and a.booking_no like '$search_string'";
			}
			else
			{
				$search_field_cond = "and c.sales_booking_no like '$search_string'";
				$search_field_cond_2 = "and b.sales_booking_no like '$search_string'";
			}
		}
		else
		{
			$search_field_cond = "and a.job_no like '$search_string'";
		}
	}

	$date_cond = '';
	if ($cbo_within_group == 1)
	{

	}
	$date_field = ($cbo_within_group == 2) ? "c.booking_date" : "a.booking_date";
	if ($date_from != "" && $date_to != "")
	{
		if ($db_type == 0)
		{
			$date_cond = "and $date_field between '" . change_date_format(trim($date_from), "yyyy-mm-dd", "-") . "' and '" . change_date_format(trim($date_to), "yyyy-mm-dd", "-") . "'";
		}
		else
		{
			$date_cond = "and $date_field between '" . change_date_format(trim($date_from), '', '', 1) . "' and '" . change_date_format(trim($date_to), '', '', 1) . "'";
		}
	}

	if ($cbo_within_group == 1)
	{
		//for fabric booking
		if($booking_type == 1)
		{
			$sql = "select a.id, a.booking_no, a.booking_date, a.buyer_id, a.company_id, a.delivery_date, a.currency_id, a.po_break_down_id, b.job_no, b.style_ref_no from wo_booking_mst a, wo_po_details_master b, fabric_sales_order_mst c where a.job_no=b.job_no and a.booking_no=c.sales_booking_no and a.supplier_id=$company_id and a.pay_mode=5 and a.fabric_source in(1,2) and a.status_active =1 and a.is_deleted =0 and a.item_category=2 $buyer_id_cond $search_field_cond $date_cond group by a.id, a.booking_no, a.booking_date, a.buyer_id, a.company_id, a.delivery_date, a.currency_id, a.po_break_down_id, b.job_no,b.style_ref_no";
		}
		//for sample booking
		else
		{
			$sql = "select a.id, a.booking_no, a.booking_date, a.buyer_id, a.company_id, a.delivery_date, a.currency_id, a.po_break_down_id, a.job_no, d.style_ref_no from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, fabric_sales_order_mst c, sample_development_mst d where a.booking_no=b.booking_no and a.booking_no=c.sales_booking_no and b.style_id = d.id and a.supplier_id=$company_id and a.pay_mode=5 and a.fabric_source in(1,2) and a.status_active =1 and a.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and a.item_category=2 $buyer_id_cond $search_field_cond $date_cond group by a.id, a.booking_no, a.booking_date, a.buyer_id, a.company_id, a.delivery_date, a.currency_id, a.po_break_down_id, a.job_no, d.style_ref_no";
		}
	}
	else
	{
		$sql = "select c.id, c.sales_booking_no booking_no, c.booking_date,c.buyer_id, c.company_id,c.job_no, c.style_ref_no from fabric_sales_order_mst c where c.company_id=$company_id and c.status_active =1 and c.is_deleted=0 $date_cond $search_field_cond and c.within_group=2 group by c.id, c.sales_booking_no, c.booking_date, c.buyer_id, c.company_id, c.job_no, c.style_ref_no";
	}
	//echo $sql;
	
	$result = sql_select($sql);
	$poArr = array();
	$buyerArr = array();
	foreach ($result as $row)
	{
		//for buyer
		$buyerArr[$row[csf('buyer_id')]] = $row[csf('buyer_id')];
		
		//for po
		if ($row[csf('po_break_down_id')] != "")
		{
			$po_ids = explode(",", $row[csf('po_break_down_id')]);
			foreach ($po_ids as $po_id)
			{
				$poArr[$po_id] = $po_id;
			}
		}
	}
	
	//for partial
	if($db_type==0)
	{
		$sql_partial = "select a.id, a.booking_no, a.booking_date,a.buyer_id, a.company_id, a.delivery_date, a.currency_id, group_concat(c.po_break_down_id) as po_break_down_id, c.job_no from wo_booking_mst a, wo_booking_dtls c, fabric_sales_order_mst b where a.booking_no=c.booking_no and a.booking_no=b.sales_booking_no and a.status_active =1 and a.is_deleted =0 and a.pay_mode=5 and a.fabric_source in(1,2) and a.item_category=2 $buyer_id_cond $search_field_cond_2 $date_cond and a.entry_form=108 group by a.id, a.booking_no,a.booking_date,a.buyer_id,a.company_id,a.delivery_date,a.currency_id,c.job_no";
	}
	else
	{
		//for fabric booking
		if($booking_type == 1)
		{
			$sql_partial = "select a.id, a.booking_no, a.booking_date,a.buyer_id, a.company_id, a.delivery_date, a.currency_id, listagg(c.po_break_down_id, ',') within group (order by c.po_break_down_id) as po_break_down_id, c.job_no from wo_booking_mst a, wo_booking_dtls c, fabric_sales_order_mst b where a.booking_no=c.booking_no and a.booking_no=b.sales_booking_no and a.status_active =1 and a.is_deleted =0 and a.pay_mode=5 and a.fabric_source in(1,2) and a.item_category=2 $buyer_id_cond $search_field_cond_2 $date_cond and a.entry_form=108 group by a.id, a.booking_no, a.booking_date, a.buyer_id, a.company_id, a.delivery_date, a.currency_id, c.job_no";
		}
		//for sample booking
		else
		{
			//$sql_partial = "select a.id, a.booking_no, a.booking_date,a.buyer_id, a.company_id, a.delivery_date, a.currency_id, listagg(c.po_break_down_id, ',') within group (order by c.po_break_down_id) as po_break_down_id, c.job_no from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls c, fabric_sales_order_mst b where a.booking_no=c.booking_no and a.booking_no=b.sales_booking_no and a.status_active =1 and a.is_deleted =0 and a.pay_mode=5 and a.fabric_source in(1,2) and a.item_category=2 $buyer_id_cond $search_field_cond_2 $date_cond and a.entry_form=108 group by a.id, a.booking_no, a.booking_date, a.buyer_id, a.company_id, a.delivery_date, a.currency_id, c.job_no";
			$sql_partial = "select a.id, a.booking_no, a.booking_date,a.buyer_id, a.company_id, a.delivery_date, a.currency_id from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls c, fabric_sales_order_mst b where a.booking_no=c.booking_no and a.booking_no=b.sales_booking_no and a.status_active =1 and a.is_deleted =0 and a.pay_mode=5 and a.fabric_source in(1,2) and a.item_category=2 $buyer_id_cond $search_field_cond_2 $date_cond  group by a.id, a.booking_no, a.booking_date, a.buyer_id, a.company_id, a.delivery_date, a.currency_id";
		}
	}
	//echo $sql_partial;
	$result_partial = sql_select($sql_partial);
	foreach ($result_partial as $row)
	{
		//for buyer
		$buyerArr[$row[csf('buyer_id')]] = $row[csf('buyer_id')];
		
		//for po
		if ($row[csf('po_break_down_id')] != "")
		{
			$po_ids = explode(",", $row[csf('po_break_down_id')]);
			foreach ($po_ids as $po_id)
			{
				$poArr[$po_id] = $po_id;
			}
		}
	}
	//echo "<pre>";
	//print_r($buyerArr);
	
	//for company details
	$company_arr = return_library_array("select id,company_short_name from lib_company", 'id', 'company_short_name');
	
	//for buyer details
	//$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$buyer_arr = array();
	if(!empty($buyerArr))
	{
		$buyer_arr = return_library_array("select id, buyer_name from lib_buyer where 1=1".where_con_using_array($buyerArr,0,'id'), "id", "buyer_name");
	}
	
	//for buyer details
	//$po_arr = return_library_array("select id, po_number from wo_po_break_down", "id", "buyer_name");
	$po_arr = array();
	if(!empty($poArr))
	{
		$po_arr = return_library_array("select id, po_number from wo_po_break_down where 1=1".where_con_using_array($poArr,0,'id'), "id", "buyer_name");
	}
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="740" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="80">PO Buyer</th>
			<th width="120">Booking No</th>
			<th width="90">Job No</th>
			<th width="120">Style Ref.</th>
			<th width="80">Booking Date</th>
			<th>PO No.</th>
		</thead>
	</table>
	<div style="width:740px; max-height:270px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table"
		id="tbl_list_search">
		<?
		$i = 1;
		$j = 1;
		foreach ($result as $row)
		{
			if ($i % 2 == 0)
				$bgcolor = "#E9F3FF";
			else
				$bgcolor = "#FFFFFF";

			if ($row[csf('po_break_down_id')] != "")
			{
				$po_no = '';
				$po_ids = explode(",", $row[csf('po_break_down_id')]);
				foreach ($po_ids as $po_id)
				{
					if ($po_no == "")
						$po_no = $po_arr[$po_id];
					else
						$po_no .= "," . $po_arr[$po_id];
				}
			}
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
				onClick="js_set_value('<? echo $row[csf('booking_no')]; ?>')">
				<td width="40"><? echo $i; ?></td>
				<td width="80" align="center"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
				<td width="120"><p><? echo $row[csf('booking_no')]; ?></p></td>
				<td width="90" align="center"><p><? echo $row[csf('job_no')]; ?>&nbsp;</p></td>
				<td width="120"><p><? echo $row[csf('style_ref_no')]; ?>&nbsp;</p></td>
				<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
				<td><p><? echo $po_no; ?>&nbsp;</p></td>
			</tr>
			<?
			$i++;
		}

		//for partial
		foreach ($result_partial as $row)
		{
			if ($j % 2 == 0)
				$bgcolor = "#E9F3FF";
			else
				$bgcolor = "#FFFFFF";

			if ($row[csf('po_break_down_id')] != "")
			{
				$po_no = '';
				$po_ids = array_unique(explode(",", $row[csf('po_break_down_id')]));
				foreach ($po_ids as $po_id)
				{
					if ($po_no == "")
						$po_no = $po_arr[$po_id];
					else
						$po_no .= "," . $po_arr[$po_id];
				}
			}
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
				onClick="js_set_value('<? echo $row[csf('booking_no')]; ?>')">
				<td width="40"><? echo $j; ?></td>
				<td width="80" align="center"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
				<td width="120"><p><? echo $row[csf('booking_no')]; ?></p></td>
				<td width="90" align="center"><p><? echo $row[csf('job_no')]; ?>&nbsp;</p></td>
				<td width="120"><p><? echo $row[csf('style_ref_no')]; ?>&nbsp;</p></td>
				<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
				<td><p><? echo $po_no; ?>&nbsp;</p></td>
			</tr>
			<?
			$j++;
		}
		?>
	</table>
</div>
<?
exit();
}

if ($action == "load_drop_down_knitting_party")
{
	$data = explode("**", $data);
	if ($data[0] == 1) {
		echo create_drop_down("cbo_knitting_party", 177, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "--Select Knit Party--", $data[1], "load_drop_down( 'program_wise_mc_entry_controller', this.value, 'load_drop_down_location','location_td');", "");
	} else if ($data[0] == 3) {
		if ($data[2] == 1) $selected_id = $data[1]; else $selected_id = 0;
		echo create_drop_down("cbo_knitting_party", 177, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 and a.is_deleted=0 group by a.id, a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select Knit Party--", $selected_id, "load_drop_down( 'program_wise_mc_entry_controller', this.value, 'load_drop_down_location','location_td');");
	} else {
		echo create_drop_down("cbo_knitting_party", 177, $blank_array, "", 1, "--Select Knit Party--", 0, "load_drop_down( 'program_wise_mc_entry_controller', this.value, 'load_drop_down_location','location_td');");
	}
	exit();
}

if($action=="load_drop_down_location")
{
    extract($_REQUEST);
    $company_ids = str_replace("'","",$data);

    $check_location_sql=sql_select("SELECT id,location_name from lib_location where company_id in($company_ids)  and status_active =1 and is_deleted=0 order by location_name");

    if(count($check_location_sql)==1){
    	echo create_drop_down( "cbo_location_name", 152, "SELECT id,location_name from lib_location where company_id in($company_ids)  and status_active =1 and is_deleted=0 order by location_name","id,location_name", 0, "--Select Location--", 0, "","" );
    }
    else{
    	echo create_drop_down( "cbo_location_name", 152, "SELECT id,location_name from lib_location where company_id in($company_ids)  and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", 0, "","" );
    }
	exit();
}





if ($action == "prog_info_print")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company_id = $data[0];
	$program_id = $data[1];
	$path = $data[2];
	//echo $path;die;
	echo load_html_head_contents("Program Qnty Info", $path, 1, 1, '', '', '');

	$company_details = return_library_array("select id,company_name from lib_company where id=".$company_id."", "id", "company_name");
	$supllier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	$country_arr = return_library_array("select id, country_name from lib_country where status_active=1 and is_deleted=0", 'id', 'country_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0 order by yarn_count", 'id', 'yarn_count');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
	$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");

	if($program_id!="")
	{
		$plan_info=sql_select("SELECT a.style_ref_no, a.within_group, a.job_no, a.company_id,a.location_id as sales_order_location_id, a.po_company_id, a.buyer_id, a.po_buyer, b.dtls_id, b.booking_no, b.gsm_weight as fin_gsm, b.dia as fin_dia, b.fabric_desc, c.program_date, c.end_date, c.knitting_source, c.knitting_party, c.location_id, c.color_range, c.stitch_length, c.machine_dia, c.machine_gg, c.program_qnty, c.machine_id, c.width_dia_type, c.color_id, c.fabric_dia, c.remarks, c.advice, c.no_of_ply
		from fabric_sales_order_mst a, ppl_planning_entry_plan_dtls b, ppl_planning_info_entry_dtls c
		where a.id=b.po_id and b.dtls_id=c.id and b.dtls_id=".$program_id." and b.status_active=1 and b.is_deleted=0");
	}
	//echo "<pre>";
	//print_r($plan_info); die;

	$product_details_array = array();
	//$sql = "SELECT id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 and company_id=$company_id and status_active=1 and is_deleted=0";
	$sql = "SELECT id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand FROM product_details_master WHERE item_category_id=1 AND company_id=".$company_id." AND status_active=1 AND is_deleted=0 AND id IN(SELECT prod_id FROM ppl_yarn_requisition_entry WHERE knit_id='".$program_id."' AND status_active=1 AND is_deleted=0)";
	$result = sql_select($sql);
	foreach ($result as $row)
	{
		$compos = '';
		if ($row[csf('yarn_comp_percent2nd')] != 0) {
			$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
		} else {
			$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
		}

		$product_details_array[$row[csf('id')]]['count'] = $count_arr[$row[csf('yarn_count_id')]];
		$product_details_array[$row[csf('id')]]['comp'] = $compos;
		$product_details_array[$row[csf('id')]]['type'] = $yarn_type[$row[csf('yarn_type')]];
		$product_details_array[$row[csf('id')]]['lot'] = $row[csf('lot')];
		$product_details_array[$row[csf('id')]]['brand'] = $brand_arr[$row[csf('brand')]];
		$product_details_array[$row[csf('id')]]['color'] = $color_library[$row[csf('color')]];
	}
	//echo "<pre>";
	//print_r($product_details_array);

	$sql_machin = "SELECT dtls_id, machine_id, SUM(distribution_qnty) AS distribution_qnty FROM ppl_planning_info_machine_dtls WHERE status_active=1 AND is_deleted=0 AND dtls_id IN($program_id) GROUP BY dtls_id, machine_id ORDER BY machine_id";
	$machine_datas = sql_select($sql_machin);
	$machineData = array();
	foreach ($machine_datas as $mcrow)
	{
		$machineData[$mcrow[csf('dtls_id')]][$mcrow[csf('machine_id')]] = $mcrow[csf('distribution_qnty')];
	}
	?>
	<div style="width:1000px;">
		<style>
			table, th, td {
				/*border-bottom:1px solid black;*/
				border-collapse: collapse;
			}
		</style>
		<div style="width:100%;">
			<table style="width:800px; border-bottom:1px solid black;">
				<tr>
					<td width="60%" align="center" style="font-size: 16px; font-family: arial; font-weight: bolder;"><? echo $company_details[$company_id]; ?></td>
					<td width="15%">&nbsp;</td>
					<td width="25%">&nbsp;</td>
				</tr>
				<tr>
					<td width="60%" align="center">
						<b>
							<?
							$compAddressArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=".$company_id."");
							$address = "";
							foreach ($compAddressArray as $result) {
								$address .=  "Plot No:". $result[csf('plot_no')] ." Level No: " .$result[csf('level_no')]." Road No: ".$result[csf('road_no')]." Block No: ".$result[csf('block_no')]." City No: ".$result[csf('city')]." Zip Code: ".$result[csf('zip_code')]. "<br>".$country_arr[$result[csf('country_id')]] ."<br>".$result[csf('email')]."<br>".$result[csf('website')]  ;
							}
							echo $address;
							?>
						</b>
					</td>
					<td width="15%" style="border-top: 1px solid black; border-left: 1px solid black; padding-left: 5px;"><b>Program No:</b></td>
					<td width="25%" style="border-top: 1px solid black; border-right: 1px solid black;"><b><? echo $plan_info[0][csf('dtls_id')];?></b></td>
				</tr>
				<tr>
					<td width="60%" align="center">&nbsp;</td>
					<td width="15%" style="border-left: 1px solid black; padding-left: 5px;"><b>Sales Order No:</b></td>
					<td width="25%" style="border-right: 1px solid black;"><b><? echo $plan_info[0][csf('job_no')];?></b></td>
				</tr>
				<tr>
					<td width="60%" align="center" style="font-size: 20px; font-weight: bold;">Knitting Program Slip</td>
					<td width="15%" style="border-left: 1px solid black; padding-left: 5px;" ><b>Fabric/Booking No:</b></td>
					<td width="25%" style="border-right: 1px solid black;"><b><? echo $plan_info[0][csf('booking_no')];?></b></td>
				</tr>
			</table>
			<br><br>
			<table width="800" style="float: left;font-weight: bold; margin-bottom:25px;">
				<tr>
					<td colspan="4"><b>Attention- Knitting Manager</b></td>
				</tr>
				<tr>
					<td style="padding:0px 10px 0px 20px;">Factory</td>
					<td style="border-bottom: 1px solid black; font-size: 20px;">
						<?php
						if ($plan_info[0][csf('knitting_source')] == 1)
							echo $company_details[$plan_info[0][csf('knitting_party')]];
						else if ($plan_info[0][csf('knitting_source')] == 3)
							echo $supllier_arr[$plan_info[0][csf('knitting_party')]];
						?>
					</td>
					<td width="50">&nbsp;</td>
					<td> Program Date:  <b><?php echo change_date_format($plan_info[0][csf('program_date')]);?></b></td>
				</tr>
				<tr style="padding-left:15px;">
					<td style="padding:0px 10px 0px 20px;">Address</td>
					<td style="border-bottom: 1px solid black; padding-top:20px;">
						<?
						$address = '';
						if ($plan_info[0][csf('knitting_source')] == 1)
						{
							foreach ($compAddressArray as $result)
							{
								$address .=  $result[csf('plot_no')]." ".$result[csf('level_no')]." ".$result[csf('road_no')]." ".$result[csf('block_no')]." ".$result[csf('city')]." ".$result[csf('zip_code')]."<br>";
								$address .= $country_arr[$result[csf('country_id')]]."<br>";
								$address .= $result[csf('email')]."<br>";
								$address .= $result[csf('website')];
							}

						}
						else if ($plan_info[0][csf('knitting_source')] == 3)
						{
							$address = return_field_value("address_1", "lib_supplier", "id=" . $plan_info[0][csf('knitting_party')]);
						}
						echo $address;
						?>
					</td>
					<td width="50">&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr style="padding-left:15px;">
					<td style="padding:0px 10px 0px 20px;">PO Company:</td>
					<td style="border-bottom: 1px solid black;">
						<?
						echo $company_details[$plan_info[0][csf('po_company_id')]];
						?>
					</td>
					<td width="50">&nbsp;</td>
					<td style="border:1px solid black;text-align:center;">Target Date of Completion</td>
				</tr>
				<tr style="padding-left:15px;">
					<td style="padding:0px 10px 0px 20px;">Location:</td>
					<td>
						<?
						if ($plan_info[0][csf('knitting_source')] == 1)
						{
							$location = return_field_value("location_name", "lib_location", "id='" . $plan_info[0][csf('location_id')] . "'");
						}
						else if ($plan_info[0][csf('knitting_source')] == 3)
						{
							$location = return_field_value("location_name", "lib_location", "id='" . $plan_info[0][csf('sales_order_location_id')] . "'");
						}
						echo $location;
						?>
					</td>
					<td width="50">&nbsp;</td>
					<td style="border:1px solid black;text-align:center;"><? echo change_date_format($plan_info[0][csf('end_date')]);?></td>
				</tr>
			</table>
			<table class="rpt_table" width="800" cellspacing="0" cellpadding="0" border="1" rules="all">
				<thead>
					<tr>
						<th>SL</th>
						<th>Requisition No</th>
						<th>Lot No</th>
						<th>Yarn Description</th>
						<th>Brand</th>
						<th>Requisition Qnty</th>
						<th>Yarn Color</th>
						<th>Remarks</th>
					</tr>
				</thead>
				<tbody>
					<?
					$i = 1;
					$tot_reqsn_qnty = 0;
					$sql = "select requisition_no, prod_id, yarn_qnty from ppl_yarn_requisition_entry where knit_id='".$program_id."' and status_active=1 and is_deleted=0";
					//echo $sql;
					$nameArray = sql_select($sql);
					if(!empty($nameArray))
					{
						foreach ($nameArray as $selectResult)
						{
							?>
							<tr bgcolor="<? echo $bgcolor; ?>">

								<td><? echo $i; ?></td>
								<td align="center"><? echo $selectResult[csf('requisition_no')]; ?></td>
								<td><? echo $product_details_array[$selectResult[csf('prod_id')]]['lot']; ?></td>
								<td><? echo $product_details_array[$selectResult[csf('prod_id')]]['count'] . " " . $product_details_array[$selectResult[csf('prod_id')]]['comp'] . " " . $product_details_array[$selectResult[csf('prod_id')]]['type']; ?></td>
								<td><? echo $product_details_array[$selectResult[csf('prod_id')]]['brand']; ?></td>
								<td align="right"><? echo number_format($selectResult[csf('yarn_qnty')], 2); ?></td>
								<td align="center"><? echo $product_details_array[$selectResult[csf('prod_id')]]['color']; ?></td>
								<td>&nbsp;</td>
							</tr>
							<?
							$tot_reqsn_qnty += $selectResult[csf('yarn_qnty')];
							$i++;
						}

					}else{
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<?
					}
					?>
				</tbody>

				<tfoot>
					<th colspan="5" align="right"><b>Total</b></th>
					<th align="right"><? echo number_format($tot_reqsn_qnty, 2); ?></th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
				</tfoot>
			</table>
			<br>
			<table class="rpt_table" width="100%" cellspacing="0" cellpadding="0" border="1" rules="all">

				<thead>
					<tr>
						<th width="50">MC. No/SL</th>
						<th width="85">Buyer</th>
						<th width="80">Style No</th>
						<th width="100">Fab Type</th>
						<th width="90">Garments Color</th>
						<th width="50">MC Dia & Gauge</th>
						<th width="20">No Of Ply</th>
						<th width="20">Fin Dia</th>
						<th width="20">Fin GSM</th>
						<th width="50">SL</th>
						<th width="50">Colour Range</th>
						<th width="50">Program Quantity</th>
						<th width="50">Remarks</th>
					</tr>
				</thead>

				<tbody>
					<?php
					$total_distribution_qty = 0;
					$fabric_arr = explode(",",$plan_info[0][csf('fabric_desc')]);
					$machine_idarr = explode(",", $plan_info[0][csf("machine_id")]);
					$prog_distriqty = 0;
					foreach ($machine_idarr as $machineid)
					{
						$distributionQnty = $machineData[$plan_info[0][csf("dtls_id")]][$machineid];
						if($distributionQnty>0)
						{
							$prog_distriqty = $distributionQnty;
						}
						else
						{
							$prog_distriqty = $plan_info[0][csf("program_qnty")];
						}

						if($machineid!="")
						{
							$machineSl = $machine_arr[$machineid];
						}
						else
						{
							$machineSl = 1;
						}
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td width="65" align="center">
								<? echo $machineSl;?>
							</td>
							<td width="100">
								<?
								if ($plan_info[0][csf('within_group')] == 1)
								{
									$buyer = $buyer_arr[$plan_info[0][csf("po_buyer")]];
								}
								else
								{
									$buyer = $buyer_arr[$plan_info[0][csf("buyer_id")]];
								}

								echo $buyer;
								?>
							</td>
							<td width="100"><? echo $plan_info[0][csf("style_ref_no")];  ?></td>
							<td width="100"><? echo $fabric_arr[0]; ?></td>
							<td width="65">
								<?
								$color_id_arr = array_unique(explode(",", $plan_info[0][csf('color_id')]));
								$all_color = "";
								foreach ($color_id_arr as $color_id)
								{
									$all_color .= $color_library[$color_id] . ",";
								}
								$all_color = chop($all_color, ",");
								echo $all_color;
								?>
							</td>
							<td width="50"><? echo $plan_info[0][csf('machine_dia')] . "X" . $plan_info[0][csf('machine_gg')]; ?></td>
							<td width="10"><? echo $plan_info[0][csf('no_of_ply')];?></td>
							<td width="10"><? echo $plan_info[0][csf('fabric_dia')];?></td>
							<td width="10"><? echo $plan_info[0][csf('fin_gsm')];?></td>
							<td width="10" align="center"><? echo $plan_info[0][csf('stitch_length')];?></td>
							<td width="50"><? echo $color_range[$plan_info[0][csf('color_range')]]; ?></td>
							<td width="50" align="right"><? echo number_format($prog_distriqty, 2); ?></td>
							<td width="50"><? echo $plan_info[0][csf('remarks')]; ?></td>
						</tr>
						<?
						//$total_distribution_qty += $row[csf('distribution_qnty')];
						$total_distribution_qty += $prog_distriqty;

						if($machineid!="")
						{
							$machineSl++;
						}
					}
					?>
					<tfoot>
						<th colspan="11" align="right"><b>Total</b></th>
						<th style="text-align: right;"><? echo number_format($total_distribution_qty, 2); ?></th>
						<th>&nbsp;</th>
					</tfoot>
				</tbody>
			</table>
			<br>
			<span> Advice:  <? echo $plan_info[0][csf('advice')]; ?> </span>
			<div style="width:100%; float:left;padding-top:10px;">
				<?
				//$sql_stripe_feeder = sql_select("select a.id, a.color_id, a.stripe_color_id, a.no_of_feeder, max(b.measurement) as measurement, max(b.uom) as uom from ppl_planning_feeder_dtls a, wo_pre_stripe_color b where a.pre_cost_id=b.pre_cost_fabric_cost_dtls_id and b.stripe_color=a.stripe_color_id and a.status_active=1 and a.is_deleted=0 and a.dtls_id=".$program_id." and a.no_of_feeder>0   group by a.id, a.color_id, a.stripe_color_id, a.no_of_feeder");

				$sql_stripe_feeder = sql_select("select b.pre_cost_fabric_cost_dtls_id as pre_cost_id, b.color_number_id as color_id, b.stripe_color as stripe_color_id,a.no_of_feeder, b.measurement, b.uom
				from ppl_planning_feeder_dtls a,wo_pre_stripe_color  b
				where a.pre_cost_id=b.pre_cost_fabric_cost_dtls_id  and b.stripe_color=a.stripe_color_id 
				and b.status_active=1 and b.is_deleted=0 
				and a.dtls_id=".$program_id." and a.no_of_feeder>0   
				and b.sales_dtls_id is not null 
				group by  b.pre_cost_fabric_cost_dtls_id, b.color_number_id, b.stripe_color,a.no_of_feeder, b.measurement, b.uom ");
				//order by b.color_number_id, b.stripe_color,b.measurement

				if (count($sql_stripe_feeder) > 0)
				{
					?>
					<table style="width:48%; float:left;" class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all">
						<thead>
							<tr>
								<th colspan="6" align="center">Stripe Measurement Information:</th>
							</tr>
							<tr>
								<th width="50">SL</th>
								<th width="100">Combo Color</th>
								<th width="100">Stripe Color</th>
								<th width="100">Measurement </th>
								<th width="50">Uom</th>
								<th width="100">No Of Feeder</th>
							</tr>
						</thead>

						<tbody>
							<?
							$i = 1;
							$total_feeder = 0;
							foreach ($sql_stripe_feeder as $row)
							{
								if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>">
									<td width="50" align="center"><? echo $i; ?></td>
									<td width="100"><? echo $color_library[$row[csf('color_id')]]; ?></td>
									<td width="100"><? echo $color_library[$row[csf('stripe_color_id')]]; ?></td>
									<td width="100" align="center">
										<?
										echo number_format($row[csf('measurement')], 2);
										$total_measurement += $row[csf('measurement')];
										?>
									</td>
									<td width="50" align="center"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
									<td width="100" align="center">
										<? echo number_format($row[csf('no_of_feeder')], 0);
										$total_feeder += $row[csf('no_of_feeder')]; ?>
									</td>
								</tr>
								<?
								$i++;
							}
							?>
						</tbody>

						<tfoot>
							<th colspan="3" align="right"><b>Total</b></th>
							<th style="text-align: center;"><? echo number_format($total_measurement, 0); ?></th>
							<th>&nbsp;</th>
							<th style="text-align: center;"><? echo number_format($total_feeder, 0); ?></th>
						</tfoot>
					</table>
					<?
				}

				$sql_collar_cuff_dtls = sql_select("select body_part_id, grey_size, finish_size, qty_pcs, needle_per_cm from ppl_planning_collar_cuff_dtls where status_active=1 and is_deleted=0 and dtls_id=$program_id");

				if (count($sql_collar_cuff_dtls) > 0)
				{
					?>
					<table style="width:48%; float:left; margin-left:40px;" class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all">
						<thead>
							<tr>
								<th colspan="6" align="center">Collar & Cuff Measurement Information:</th>
							</tr>
							<tr>
								<th width="50">SL</th>
								<th width="100">Body Part</th>
								<th width="100">Grey Size</th>
								<th width="100">Finish Size</th>
								<th width="50">Qty. Pcs</th>
								<th width="100">Needle Per CM</th>
							</tr>
						</thead>

						<tbody>
							<?
							$k = 1;
							$total_cuff_qty = 0;
							foreach ($sql_collar_cuff_dtls as $cuff_row)
							{
								if ($k % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor ; ?>">
									<td width="50" align="center"><? echo $k; ?></td>
									<td width="100" align="center"><? echo $body_part[$cuff_row[csf('body_part_id')]]; ?></td>
									<td width="100" align="center"><? echo $cuff_row[csf('grey_size')]; ?></td>
									<td width="100" align="center"><? echo $cuff_row[csf('finish_size')]; ?></td>
									<td width="50" align="right"><? echo number_format($cuff_row[csf('qty_pcs')], 0); ?></td>
									<td width="100" align="center"><? echo $cuff_row[csf('needle_per_cm')]; ?></td>
								</tr>
								<?
								$total_qty_pcs += $cuff_row[csf('qty_pcs')];
								$k++;
							}
							?>
						</tbody>

						<tfoot>
							<th colspan="4" align="right"><b>Total</b></th>
							<th align="right"><? echo number_format($total_qty_pcs,0);?></th>
							<th>&nbsp;</th>
						</tfoot>
					</table>
					<?
				}
				?>
			</div>

			<div style="width:100%; float:left; padding-top:20px;">

				<?
				$sql_stripe_colorwise = sql_select("select a.stripe_color_id, a.no_of_feeder,sum(b.fabreqtotkg) as fabreqtotkg , max(b.measurement) as measurement, max(b.uom) as uom from ppl_planning_feeder_dtls a, wo_pre_stripe_color b where a.pre_cost_id=b.pre_cost_fabric_cost_dtls_id and b.stripe_color=a.stripe_color_id and a.status_active=1 and a.is_deleted=0 and a.dtls_id=$program_id and a.no_of_feeder>0 group by a.stripe_color_id, a.no_of_feeder");

				if (count($sql_stripe_colorwise) > 0)
				{

					?>
					<table style="width:48%; float:left;" class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all">
						<thead>
							<tr>
								<th colspan="5" align="center">Colour Wise Quantity</th>
							</tr>
							<tr>
								<th width="100">Stripe Color</th>
								<th width="100">Measurement</th>
								<th width="100">UOM</th>
								<th width="100">Total Feeder</th>
								<th width="100">Quantity(Kg)</th>
							</tr>
						</thead>

						<tbody>
							<?
							$y = 1;
							foreach ($sql_stripe_colorwise as $colorwise_row)
							{
								if ($y % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>">
									<td width="100"><? echo $color_library[$colorwise_row[csf('stripe_color_id')]]; ?></td>
									<td width="100" align="center"><? echo number_format($colorwise_row[csf('measurement')], 2);?></td>
									<td width="100" align="center"><? echo $unit_of_measurement[$colorwise_row[csf('uom')]]; ?></td>
									<td width="100" align="center"><? echo number_format($colorwise_row[csf('no_of_feeder')], 0);?></td>
									<td width="100" align="right"><? echo number_format($colorwise_row[csf('fabreqtotkg')], 0);?></td>
								</tr>
								<?
								$y++;
							}
							?>

						</tbody>
					</table>
					<?
				}

				$sql_count_feed = "select seq_no,count_id,feeding_id from ppl_planning_count_feed_dtls where dtls_id=$program_id and status_active=1 and is_deleted=0 order by seq_no";
				$data_array_count_feed = sql_select($sql_count_feed);
				if(count($data_array_count_feed)>0)
				{
					?>
					<table style="width:48%; float:left; margin-left:40px;" class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all">
						<thead>
							<tr>
								<th colspan="4" align="center">Count Feeding</th>
							</tr>
							<tr>
								<th width="50">Seq. No</th>
								<th width="100">Count</th>
								<th width="100">Feeding</th>
								<th width="100">Percentage</th>
							</tr>
						</thead>

						<tbody>
							<?
							$feeding_arr = array(1 => 'Knit', 2 => 'Binding', 3 => 'Loop');
							$j=1;
							foreach ($data_array_count_feed as $count_feed_row)
							{
								if ($j % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
								?>
								<tr>
									<td width="50" align="center"><? echo $count_feed_row[csf('seq_no')]; ?></td>
									<td width="100" align="center"><? echo $count_arr[$count_feed_row[csf('count_id')]];?></td>
									<td width="100"><? echo $feeding_arr[$count_feed_row[csf('feeding_id')]];?></td>
									<td width="100">&nbsp;</td>
								</tr>
								<?
								$j++;
							}
							?>
						</tbody>
					</table>
					<?
				}
				?>
			</div>

			<div style="width:100%; float:left;padding-top:10px;">
				<?
				$sql_cam_design = "select id,cmd1, cmd2, cmd3, cmd4, cmd5, cmd6, cmd7, cmd8, cmd9, cmd10, cmd11, cmd12, cmd13, cmd14, cmd15, cmd16, cmd17, cmd18, cmd19, cmd20, cmd21, cmd22, cmd23, cmd24 from ppl_planning_cam_design_dtls where dtls_id=$program_id and status_active=1 and is_deleted=0 order by id";
				$data_cam_design = sql_select($sql_cam_design);
				if (count($data_cam_design) > 0)
				{

					?>
					<table width="100%" class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all">
						<thead>
							<tr>
								<th colspan="25" align="center">Cam Design Information</th>
							</tr>
							<tr>
								<th width="4%">SL</th>
								<?
								for ($i=1; $i<=24; $i++)
								{
									?>
									<th width="4%"><? echo $i; ?></th>
									<?
								}
								?>
							</tr>
						</thead>
					</table>

					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table"
					id="tbl_came_design">
					<tbody>
						<?
						$sl=1;
						foreach ($data_cam_design as $row)
						{
							if ($sl % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td width="4%" align="center"><? echo $sl; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd1')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd2')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd3')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd4')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd5')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd6')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd7')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd8')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd9')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd10')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd11')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd12')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd13')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd14')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd15')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd16')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd17')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd18')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd19')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd20')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd21')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd22')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd23')]; ?></td>
								<td width="4%" align="center"><? echo $row[csf('cmd24')]; ?></td>
							</tr>
							<?
							$sl++;
						}
						?>
					</tbody>
				</table>
				<?
			}
			?>
			</div>



			<!--- Needle layout --->
			<div  style="width:100%; float:left; padding-top: 20px;">
				<style type="text/css">    
				    #needle-layout{
				        border-collapse:collapse;
				        table-layout:fixed;
				        width:600pt;
				        font-size: 20px;
				    }
				</style>
				<?
				$sql_needle_layout = "select PLAN_ID, PROGRAM_NO, DIAL, CYLINDER, DIAL_ROW1, DIAL_ROW2, NO_OF_FEEDER, CYLINDER_ROW1, CYLINDER_ROW2, CYLINDER_ROW3, CYLINDER_ROW4, YARN_ENDS, LFA, YARN_TENSION, GREY_GSM, T_DRY_WEIGHT, T_DRY_WIDTH, RPM, F_ROLL_WIDTH, LAID_WIDTH,ACTIVE_FEEDER, REV_PER_KG, DIAL_HEIGHT from ppl_planning_needle_layout where PROGRAM_NO=$program_id AND STATUS_ACTIVE=1 AND IS_DELETED=0";

				$data_needle_layout = sql_select($sql_needle_layout);
				
				if (count($data_needle_layout) > 0)
				{
					foreach ($data_needle_layout as $row)
					{
						if($row['DIAL_ROW1']!="")
						{
							$dial_row1_data_arr = explode("__", $row['DIAL_ROW1']);

							$dial_row1col1 = ($dial_row1_data_arr[0]!="")?$dial_row1_data_arr[0]:"";
							$dial_row1col2 = ($dial_row1_data_arr[1]!="")?$dial_row1_data_arr[1]:"";
							$dial_row1col3 = ($dial_row1_data_arr[2]!="")?$dial_row1_data_arr[2]:"";
							$dial_row1col4 = ($dial_row1_data_arr[3]!="")?$dial_row1_data_arr[3]:"";
							$dial_row1col5 = ($dial_row1_data_arr[4]!="")?$dial_row1_data_arr[4]:"";
							$dial_row1col6 = ($dial_row1_data_arr[5]!="")?$dial_row1_data_arr[5]:"";
						}

						if($row['DIAL_ROW2']!="")
						{
							$dial_row2_data_arr = explode("__", $row['DIAL_ROW2']);

							$dial_row2col1 = ($dial_row2_data_arr[0]!="")?$dial_row2_data_arr[0]:"";
							$dial_row2col2 = ($dial_row2_data_arr[1]!="")?$dial_row2_data_arr[1]:"";
							$dial_row2col3 = ($dial_row2_data_arr[2]!="")?$dial_row2_data_arr[2]:"";
							$dial_row2col4 = ($dial_row2_data_arr[3]!="")?$dial_row2_data_arr[3]:"";
							$dial_row2col5 = ($dial_row2_data_arr[4]!="")?$dial_row2_data_arr[4]:"";
							$dial_row2col6 = ($dial_row2_data_arr[5]!="")?$dial_row2_data_arr[5]:"";
						}

						if($row['NO_OF_FEEDER']!="")
						{
							$no_of_feeder_data_arr = explode("__", $row['NO_OF_FEEDER']);

							$no_of_feeder_col1 = ($no_of_feeder_data_arr[0]!="")?$no_of_feeder_data_arr[0]:"";
							$no_of_feeder_col2 = ($no_of_feeder_data_arr[1]!="")?$no_of_feeder_data_arr[1]:"";
							$no_of_feeder_col3 = ($no_of_feeder_data_arr[2]!="")?$no_of_feeder_data_arr[2]:"";
							$no_of_feeder_col4 = ($no_of_feeder_data_arr[3]!="")?$no_of_feeder_data_arr[3]:"";
							$no_of_feeder_col5 = ($no_of_feeder_data_arr[4]!="")?$no_of_feeder_data_arr[4]:"";
							$no_of_feeder_col6 = ($no_of_feeder_data_arr[5]!="")?$no_of_feeder_data_arr[5]:"";
						}

						if($row['CYLINDER_ROW1']!="")
						{
							$cylinder_row1_data_arr = explode("__", $row['CYLINDER_ROW1']);

							$cylinder_row1col1 = ($cylinder_row1_data_arr[0]!="")?$cylinder_row1_data_arr[0]:"";
							$cylinder_row1col2 = ($cylinder_row1_data_arr[1]!="")?$cylinder_row1_data_arr[1]:"";
							$cylinder_row1col3 = ($cylinder_row1_data_arr[2]!="")?$cylinder_row1_data_arr[2]:"";
							$cylinder_row1col4 = ($cylinder_row1_data_arr[3]!="")?$cylinder_row1_data_arr[3]:"";
							$cylinder_row1col5 = ($cylinder_row1_data_arr[4]!="")?$cylinder_row1_data_arr[4]:"";
							$cylinder_row1col6 = ($cylinder_row1_data_arr[5]!="")?$cylinder_row1_data_arr[5]:"";
						}

						if($row['CYLINDER_ROW2']!="")
						{
							$cylinder_row2_data_arr = explode("__", $row['CYLINDER_ROW2']);

							$cylinder_row2col1 = ($cylinder_row2_data_arr[0]!="")?$cylinder_row2_data_arr[0]:"";
							$cylinder_row2col2 = ($cylinder_row2_data_arr[1]!="")?$cylinder_row2_data_arr[1]:"";
							$cylinder_row2col3 = ($cylinder_row2_data_arr[2]!="")?$cylinder_row2_data_arr[2]:"";
							$cylinder_row2col4 = ($cylinder_row2_data_arr[3]!="")?$cylinder_row2_data_arr[3]:"";
							$cylinder_row2col5 = ($cylinder_row2_data_arr[4]!="")?$cylinder_row2_data_arr[4]:"";
							$cylinder_row2col6 = ($cylinder_row2_data_arr[5]!="")?$cylinder_row2_data_arr[5]:"";
						}

						if($row['CYLINDER_ROW3']!="")
						{
							$cylinder_row3_data_arr = explode("__", $row['CYLINDER_ROW3']);

							$cylinder_row3col1 = ($cylinder_row3_data_arr[0]!="")?$cylinder_row3_data_arr[0]:"";
							$cylinder_row3col2 = ($cylinder_row3_data_arr[1]!="")?$cylinder_row3_data_arr[1]:"";
							$cylinder_row3col3 = ($cylinder_row3_data_arr[2]!="")?$cylinder_row3_data_arr[2]:"";
							$cylinder_row3col4 = ($cylinder_row3_data_arr[3]!="")?$cylinder_row3_data_arr[3]:"";
							$cylinder_row3col5 = ($cylinder_row3_data_arr[4]!="")?$cylinder_row3_data_arr[4]:"";
							$cylinder_row3col6 = ($cylinder_row3_data_arr[5]!="")?$cylinder_row3_data_arr[5]:"";
						}

						if($row['CYLINDER_ROW4']!="")
						{
							$cylinder_row4_data_arr = explode("__", $row['CYLINDER_ROW4']);

							$cylinder_row4col1 = ($cylinder_row4_data_arr[0]!="")?$cylinder_row4_data_arr[0]:"";
							$cylinder_row4col2 = ($cylinder_row4_data_arr[1]!="")?$cylinder_row4_data_arr[1]:"";
							$cylinder_row4col3 = ($cylinder_row4_data_arr[2]!="")?$cylinder_row4_data_arr[2]:"";
							$cylinder_row4col4 = ($cylinder_row4_data_arr[3]!="")?$cylinder_row4_data_arr[3]:"";
							$cylinder_row4col5 = ($cylinder_row4_data_arr[4]!="")?$cylinder_row4_data_arr[4]:"";
							$cylinder_row4col6 = ($cylinder_row4_data_arr[5]!="")?$cylinder_row4_data_arr[5]:"";
						}

						if($row['YARN_ENDS']!="")
						{
							$yarn_ends_data_arr = explode("__", $row['YARN_ENDS']);

							$yarn_ends_col1 = ($yarn_ends_data_arr[0]!="")?$yarn_ends_data_arr[0]:"";
							$yarn_ends_col2 = ($yarn_ends_data_arr[1]!="")?$yarn_ends_data_arr[1]:"";
							$yarn_ends_col3 = ($yarn_ends_data_arr[2]!="")?$yarn_ends_data_arr[2]:"";
							$yarn_ends_col4 = ($yarn_ends_data_arr[3]!="")?$yarn_ends_data_arr[3]:"";
							$yarn_ends_col5 = ($yarn_ends_data_arr[4]!="")?$yarn_ends_data_arr[4]:"";
						}

						if($row['LFA']!="") 
						{
							$lfa_data_arr = explode("__", $row['LFA']);

							$lfa_col1 = ($lfa_data_arr[0]!="")?$lfa_data_arr[0]:"";
							$lfa_col2 = ($lfa_data_arr[1]!="")?$lfa_data_arr[1]:"";
							$lfa_col3 = ($lfa_data_arr[2]!="")?$lfa_data_arr[2]:"";
							$lfa_col4 = ($lfa_data_arr[3]!="")?$lfa_data_arr[3]:"";
							$lfa_col5 = ($lfa_data_arr[4]!="")?$lfa_data_arr[4]:"";
						}

						if($row['YARN_TENSION']!="")
						{
							$yarn_tension_data_arr = explode("__", $row['YARN_TENSION']);

							$yarn_tension_col1 = ($yarn_tension_data_arr[0]!="")?$yarn_tension_data_arr[0]:"";
							$yarn_tension_col2 = ($yarn_tension_data_arr[1]!="")?$yarn_tension_data_arr[1]:"";
							$yarn_tension_col3 = ($yarn_tension_data_arr[2]!="")?$yarn_tension_data_arr[2]:"";
							$yarn_tension_col4 = ($yarn_tension_data_arr[3]!="")?$yarn_tension_data_arr[3]:"";
							$yarn_tension_col5 = ($yarn_tension_data_arr[4]!="")?$yarn_tension_data_arr[4]:"";
						}
					?>
						<table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table"  id="needle-layout">
							
					        <tr height='21'>
						        <td colspan='8' height='21' width='237' style='background-color: #00b0f0; text-align: center;'>Needle Layout</td>
						    </tr>
						    <tr height='20'>
						        <td colspan="2" height='20' style='background-color: #ffcc00;'>Dial</td>
						        <td colspan="2">
						        	<? echo $row['DIAL'];?>
						        </td>
						    </tr>
						    <tr height='20'>
						        <td colspan="2" height='20' style='background-color: #ffcc00;'>Cylinder</td>
						        <td colspan="2">
						        	<? echo $row['CYLINDER'];?>
						        </td>
						    </tr>				

						    <tr height='20'>
						        <td rowspan='7' style="vertical-align: middle;transform: rotate(270deg);">
						        	Cam Setting
						        </td>
						        <td rowspan='2' align="center">Dial</td>
						        <td align="center"> 
						        	<? echo $dial_row1col1; ?>
						        </td>
						        <td align="center"> 
						        	<? echo $dial_row1col2; ?>
						        </td>
						        <td align="center"> 
						        	<? echo $dial_row1col3; ?>
						        </td>
						        <td align="center"> 
						        	<? echo $dial_row1col4; ?>
						        </td>
						        <td align="center"> 
						        	<? echo $dial_row1col5; ?>
						        </td>
						        <td align="center"> 
						        	<? echo $dial_row1col6; ?>
						        </td>
						    </tr>

						    <tr height='20'>
						        <td height='20' align="center"> 
						        	<? echo $dial_row2col1; ?>
						        </td>
						        <td align="center">
						        	<? echo $dial_row2col2; ?>
						        </td>
						        <td align="center"> 
						        	<? echo $dial_row2col3; ?>
						        </td>
						        <td align="center"> 
						        	<? echo $dial_row2col4; ?>
						        </td>
						        <td align="center"> 
						        	<? echo $dial_row2col5; ?>
						        </td>
						        <td align="center"> 
						        	<? echo $dial_row2col6; ?>
						        </td>
						    </tr>

						    <tr height='20'>
						        <td height='20' style='text-align: center;'>No Of Feeder</td>
						        <td align="center"> 
						        	<? echo $no_of_feeder_col1; ?>
						        </td>
						        <td align="center"> 
						        	<? echo $no_of_feeder_col2; ?>
						        </td>
						        <td align="center"> 
						        	<? echo $no_of_feeder_col3; ?>
						        </td>
						        <td align="center"> 
						        	<? echo $no_of_feeder_col4; ?>
						        </td>
						        <td align="center"> 
						        	<? echo $no_of_feeder_col5; ?>
						        </td>
						        <td align="center"> 
						        	<? echo $no_of_feeder_col6; ?>
						        </td>
						    </tr>
						    <tr height='20'>
						        <td rowspan='4'> <div style='vertical-align: middle; transform: rotate(270deg);'> Cylinder </dive></td>
						        <td align="center"> 
						        	<? echo $cylinder_row1col1; ?>
						        </td>
						        <td align="center"> 
						        	<? echo $cylinder_row1col2; ?>
						        </td>
						        <td align="center"> 
						        	<? echo $cylinder_row1col3; ?>
						        </td>
						        <td align="center"> 
						        	<? echo $cylinder_row1col4; ?>
						        </td>
						        <td align="center"> 
						        	<? echo $cylinder_row1col5; ?>
						        </td>
						        <td align="center"> 
						        	<? echo $cylinder_row1col6; ?>
						        </td>
						    </tr>
						    <tr height='20'>
						        <td align="center"> 
						        	<? echo $cylinder_row2col1; ?>
						        </td>
						       
						        <td align="center"> 
						        	<? echo $cylinder_row2col2; ?>
						        </td>
						        <td align="center"> 
						        	<? echo $cylinder_row2col3; ?>
						        </td>
						        <td align="center"> 
						        	<? echo $cylinder_row2col4; ?>
						        </td>
						        <td align="center"> 
						        	<? echo $cylinder_row2col5; ?>
						        </td>
						        <td align="center"> 
						        	<? echo $cylinder_row2col6; ?>
						        </td>
						    </tr>
						    <tr height='20'>
						        <td align="center"> 
						        	<? echo $cylinder_row3col1; ?>
						        </td>
						        
						        <td align="center"> 
						        	<? echo $cylinder_row3col2; ?>
						        </td>
						        <td align="center"> 
						        	<? echo $cylinder_row3col3; ?>
						        </td>
						        <td align="center"> 
						        	<? echo $cylinder_row3col4; ?>
						        </td>
						        <td align="center"> 
						        	<? echo $cylinder_row3col5; ?>
						        </td>
						        <td align="center"> 
						        	<? echo $cylinder_row3col6; ?>
						        </td>
						    </tr>
						    <tr height='20'>
						        <td align="center"> 
						        	<? echo $cylinder_row4col1; ?>
						        </td>
						        <td align="center"> 
						        	<? echo $cylinder_row4col2; ?>
						        </td>
						        <td align="center"> 
						        	<? echo $cylinder_row4col3; ?>
						        </td>
						        <td align="center"> 
						        	<? echo $cylinder_row4col4; ?>
						        </td>
						        <td align="center"> 
						        	<? echo $cylinder_row4col5; ?>
						        </td>
						        <td align="center"> 
						        	<? echo $cylinder_row4col6; ?>
						        </td>
						    </tr>
						    <tr height='20'>
						        <td colspan='3' height='21' style='background-color: #c2d69a;'>Yarn Ends</td>
						        <td align="center"> 
						        	<? echo  $yarn_ends_col1; ?>
						        </td>
						        <td align="center"> 
						        	<? echo  $yarn_ends_col2; ?>
						        </td>
						        <td align="center"> 
						        	<? echo  $yarn_ends_col3; ?>
						        </td>
						        <td align="center"> 
						        	<? echo  $yarn_ends_col4; ?>
						        </td>
						        <td align="center"> 
						        	<? echo  $yarn_ends_col5; ?>
						        </td>
						    </tr>
						    <tr height='20'>
						        <td colspan='3' height='21' style='background-color: #75923c;'>LFA</td>
						        <td align="center"> 
						        	<? echo  $lfa_col1; ?>
						        </td>
						        <td align="center"> 
						        	<? echo  $lfa_col2; ?>
						        </td>
						        <td align="center"> 
						        	<? echo  $lfa_col3; ?>
						        </td>
						        <td align="center"> 
						        	<? echo  $lfa_col4; ?>
						        </td>
						        <td align="center"> 
						        	<? echo  $lfa_col5; ?>
						        </td>
						    </tr>
						    <tr height='20'>
						        <td colspan='3' height='21' style='background-color: #ccc0da;'>Yarn Tension</td>
						        <td align="center"> 
						        	<? echo  $yarn_tension_col1; ?>
						        </td>
						        <td align="center"> 
						        	<? echo  $yarn_tension_col2; ?>
						        </td>
						        <td align="center"> 
						        	<? echo  $yarn_tension_col3; ?>
						        </td>
						        <td align="center"> 
						        	<? echo  $yarn_tension_col4; ?>
						        </td>
						        <td align="center"> 
						        	<? echo  $yarn_tension_col5; ?>
						        </td>
						    </tr>
						    <tr height='20'>		
						        <td colspan='3' height='20' style="background-color: #31849b;">Grey GSM</td>
						        <td align="center"> 
						        	<? echo $row['GREY_GSM'];?>
						        </td>
						        <td style="background-color: #b8cce4;">T.Dry Weight</td>
						        <td align="center"> 
						        	<? echo $row['T_DRY_WEIGHT'];?>
						        </td>
						        <td style="background-color: #bfbfbf;">T.Dry Width</td>
						        <td align="center"> 
						        	<? echo $row['T_DRY_WIDTH'];?>
						        </td>
						    </tr>
						    <tr height='20'>
						        <td colspan='3' height='21' style="background-color: #93cddd;">RPM</td>
						        <td align="center"> 
						        	<? echo $row['RPM'];?>
						        </td>
						        <td style="background-color: #538ed5;">F.Roll Width</td>
						        <td align="center"> 
						        	<? echo $row['F_ROLL_WIDTH'];?>
						        </td>
						        <td style="background-color: #a5a5a5;">Laid Width</td>
						        <td align="center"> 
						        	<? echo $row['LAID_WIDTH'];?>
						        </td>
						    </tr>
						    <tr height='20'>
						        <td colspan='3' height='21' style="background-color: #93cddd;">Total Active Feeder</td>
						        <td align="center"> 
						        	<? echo $row['ACTIVE_FEEDER'];?>
						        </td>
						        <td style="background-color: #538ed5;">Rev per Kg</td>
						        <td align="center"> 
						        	<? echo $row['REV_PER_KG'];?>
						        </td>
						        <td style="background-color: #a5a5a5;">Dial Height </td>
						        <td align="center"> 
						        	<? echo $row['DIAL_HEIGHT'];?>
						        </td>
						    </tr>

						</table>
					<?
					}
				}
				?>

			</div>

		<!-- Needle layout end -->
		
		<div style="width:100%; float:left; padding-top:30px;">
			<table style="width:100%; float:left;" class="rpt_table">
				<tr>
					<td rowspan="3" valign="top">Special Instruction:</td>
					<td>Any type of fabric faults is not acceptable.(Patta,Sinker/Needle Mark,Loop/Hole,Tara,Fly,Oil Sport )</td>
				</tr>
				<tr>
					<td>Factory must mention the Program Number on the Delivery Challan and Bill/ Invoice.</td>
				</tr>
				<tr>
					<td>Roll marking must be done with Parmanent marker</td>
				</tr>
			</table>
		</div>

		<div style="width:100%; float:left; padding-top:20px;">
			<table style="width:100%; float:left;">
				<tr>
					<td>Received & Accepted by: </td>
					<td>&nbsp;</td>
					<td>Prepared By: </td>
					<td>&nbsp;</td>
					<td>Authorized Signature: </td>
					<td>&nbsp;</td>
				</tr>
			</table>
		</table>
	</div>
	</div>
	<?
}


?>