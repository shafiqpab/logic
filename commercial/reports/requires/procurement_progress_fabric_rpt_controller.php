<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.fabrics.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];

if ($action == "load_drop_down_buyer") 
{
    echo create_drop_down("cbo_buyer_id", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1)) and b.tag_company='$data' $buyer_cond  order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "", 0);
	exit();
}

if ($action=="job_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
    ?>	
    <script>
        function js_set_value(str)
        {
            $("#hdn_job_info").val(str); 
            parent.emailwindow.hide();
        }  
	</script>
    <input type="hidden" id="hdn_job_info" />
    <?
	if ($data[1]==0) $buyer_name=""; else $buyer_name=" and buyer_name=$data[1]";
	$job_no=str_replace("'","",$txt_job_id);
	if ($data[2]=="") $job_no_cond=""; else $job_no_cond="  and job_no_prefix_num in('$data[2]')";
	
	$sql="SELECT id,job_no_prefix_num, job_no, buyer_name, style_ref_no from wo_po_details_master where company_name=$data[0] and is_deleted=0 $buyer_name $job_no_cond ORDER BY id desc";
	// echo $sql;
	$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	$arr=array(2=>$buyer);
	
	echo  create_list_view("list_view", "Job No,Job no,Buyer,Style Ref.", "110,110,150,180","610","350",0, $sql, "js_set_value", "id,job_no,style_ref_no", "", 1, "0,0,buyer_name,0", $arr , "job_no_prefix_num,job_no,buyer_name,style_ref_no", "pi_variance_report_controller",'setFilterGrid("list_view",-1);','0,0,0,0','') ;
	disconnect($con);
	exit();
}

if ($action=="booking_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
    ?>	
    <script>
        function js_set_value(str)
        {
            $("#hdn_booking_info").val(str); 
            parent.emailwindow.hide();
        }  
	</script>
    <input type="hidden" id="hdn_booking_info" />
    <?
	if ($data[1]==0) $buyer_name=""; else $buyer_name=" and buyer_id=$data[1]";
	
	$sql= "SELECT a.id, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.booking_no_prefix_num from wo_booking_mst a where a.company_id=$data[0] $buyer_name and a.booking_type=1 and a.status_active=1 and a.is_deleted=0 order by a.id DESC";
	// echo $sql;

	$buyer=return_library_array( "SELECT id,buyer_name from lib_buyer", "id", "buyer_name"  );
	$arr=array(1=>$buyer);
	
	echo  create_list_view("list_view", "Booking No,Buyer,Booking No,Booking Date", "110,110,150,180","610","350",0, $sql, "js_set_value", "id,booking_no", "", 1, "0,buyer_id,0,0", $arr , "booking_no_prefix_num,buyer_id,booking_no,booking_date", "procurement_progress_fabric_rpt_controller",'setFilterGrid("list_view",-1);','0,0,0,3','') ;
	disconnect($con);
	exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
	$txt_job_id=str_replace("'","",$txt_job_id);
	$txt_booking_id=str_replace("'","",$txt_booking_id);
	$cbo_shipment_status=str_replace("'","",$cbo_shipment_status);
	$cbo_date_type=str_replace("'","",$cbo_date_type);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$cbo_rcv_status=str_replace("'","",$cbo_rcv_status);

	$lib_company=return_library_array( "SELECT id, company_short_name from lib_company",'id','company_short_name');
	$lib_buyer=return_library_array( "SELECT id, buyer_name from lib_buyer",'id','buyer_name');
	$lib_color=return_library_array( "SELECT id, color_name from lib_color",'id','color_name');
	$lib_supplier=return_library_array( "SELECT id, supplier_name from lib_supplier",'id','supplier_name');

	$composition_arr=array();
	$sql_deter="SELECT a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id order by b.id asc";
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

	if($db_type==0)
	{
		$date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
		$date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
	}
	else if($db_type==2)
	{
		$date_from=change_date_format($txt_date_from,'','',-1);
		$date_to=change_date_format($txt_date_to,'','',-1);
	}

	$sql_cond="";
	if($cbo_company_name){ $sql_cond.=" and a.company_name = $cbo_company_name"; }
	if($cbo_buyer_id){ $sql_cond.=" and a.buyer_name = $cbo_buyer_id"; }
	if($txt_job_id !=""){ $sql_cond.=" and a.id = $txt_job_id"; }
	if($txt_booking_id !=""){ $sql_cond.=" and e.id = $txt_booking_id"; }
	if($cbo_shipment_status){ $sql_cond.=" and b.shiping_status = $cbo_shipment_status"; }
	if($txt_date_from !="" && $txt_date_to !="" )
	{
		if($cbo_date_type==1)
		{
			$sql_cond.=" and e.booking_date between '$date_from' and '$date_to'";
		}
		elseif($cbo_date_type==2)
		{
			$sql_cond.=" and b.pub_shipment_date '$date_from' and '$date_to'";
		}
		elseif($cbo_date_type==3)
		{
			$sql_cond.=" and b.po_received_date between '$date_from' and '$date_to'";
		}
	}

	// e.id=f.booking_mst_id
	$main_sql="SELECT a.id as JOB_ID, a.JOB_NO, a.COMPANY_NAME, a.BUYER_NAME, a.STYLE_REF_NO, b.id as PO_ID, b.PO_QUANTITY, b.PO_RECEIVED_DATE, b.PUB_SHIPMENT_DATE, c.id as PRE_COST_MST_ID, c.costing_date as BUDGET_DATE, d.id as FABRIC_COST_DTLS,  d.ITEM_NUMBER_ID, d.BODY_PART_ID, d.BODY_PART_TYPE, d.FAB_NATURE_ID, d.COLOR_TYPE_ID, d.CONSTRUCTION, d.COMPOSITION, d.GSM_WEIGHT, d.WIDTH_DIA_TYPE, d.UOM, d.FABRIC_DESCRIPTION, d.LIB_YARN_COUNT_DETER_ID, d.GSM_WEIGHT_TYPE, d.COLOR_SIZE_SENSITIVE, e.id as BOOKING_ID, e.BOOKING_NO, e.BOOKING_DATE, e.CURRENCY_ID, e.SUPPLIER_ID, f.FIN_FAB_QNTY, (f.FIN_FAB_QNTY*f.RATE) as BOOKING_AMOUNT, f.fabric_color_id as COLOR_ID,f.gmts_color_id as GMTS_COLOR_ID, f.DIA_WIDTH
	from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c, wo_pre_cost_fabric_cost_dtls d, wo_booking_mst e, wo_booking_dtls f
	where a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and b.id=f.po_break_down_id and d.id=f.pre_cost_fabric_cost_dtls_id and e.booking_no=f.booking_no and d.fabric_source=2 and e.booking_type=1 and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 and f.status_active=1  $sql_cond order by b.id";

	// echo $main_sql;
	$main_data=sql_select($main_sql);
	$all_data_arr=array();$po_info=array();$po_chk=array();
	foreach($main_data as $row)
	{
		$key=$row['ITEM_NUMBER_ID']."*".$row['BODY_PART_ID']."*".$row['BODY_PART_TYPE']."*".$row['FAB_NATURE_ID']."*".$row['COLOR_TYPE_ID']."*".$row['CONSTRUCTION']."*".$row['COMPOSITION']."*".$row['GSM_WEIGHT']."*".$row['WIDTH_DIA_TYPE']."*".$row['UOM']."*".$row['LIB_YARN_COUNT_DETER_ID']."*".$row['COLOR_ID']."*".$row['GMTS_COLOR_ID']."*".$row['DIA_WIDTH'];

		$all_data_arr[$row['JOB_ID']][$key][$row['BOOKING_ID']]['COMPANY_NAME']=$row['COMPANY_NAME'];
		$all_data_arr[$row['JOB_ID']][$key][$row['BOOKING_ID']]['JOB_NO']=$row['JOB_NO'];
		$all_data_arr[$row['JOB_ID']][$key][$row['BOOKING_ID']]['BUYER_NAME']=$row['BUYER_NAME'];
		$all_data_arr[$row['JOB_ID']][$key][$row['BOOKING_ID']]['STYLE_REF_NO']=$row['STYLE_REF_NO'];
		$all_data_arr[$row['JOB_ID']][$key][$row['BOOKING_ID']]['PRE_COST_MST_ID']=$row['PRE_COST_MST_ID'];
		$all_data_arr[$row['JOB_ID']][$key][$row['BOOKING_ID']]['BUDGET_DATE']=$row['BUDGET_DATE'];
		$all_data_arr[$row['JOB_ID']][$key][$row['BOOKING_ID']]['GARMENTS_ITEM']=$row['ITEM_NUMBER_ID'];
		$all_data_arr[$row['JOB_ID']][$key][$row['BOOKING_ID']]['FABRIC_COST_DTLS']=$row['FABRIC_COST_DTLS'];
		$all_data_arr[$row['JOB_ID']][$key][$row['BOOKING_ID']]['BODY_PART']=$row['BODY_PART_ID'];
		$all_data_arr[$row['JOB_ID']][$key][$row['BOOKING_ID']]['BODY_PART_TYPE']=$row['BODY_PART_TYPE'];
		$all_data_arr[$row['JOB_ID']][$key][$row['BOOKING_ID']]['ITEM_CATEGORY']=$row['FAB_NATURE_ID'];
		$all_data_arr[$row['JOB_ID']][$key][$row['BOOKING_ID']]['COLOR_TYPE']=$row['COLOR_TYPE_ID'];
		$all_data_arr[$row['JOB_ID']][$key][$row['BOOKING_ID']]['CONSTRUCTION']=$row['CONSTRUCTION'];
		$all_data_arr[$row['JOB_ID']][$key][$row['BOOKING_ID']]['COMPOSITION']=$row['COMPOSITION'];
		$all_data_arr[$row['JOB_ID']][$key][$row['BOOKING_ID']]['FABRIC_DESCRIPTION']=$row['FABRIC_DESCRIPTION'];
		$all_data_arr[$row['JOB_ID']][$key][$row['BOOKING_ID']]['COUNT_DETER_ID']=$row['LIB_YARN_COUNT_DETER_ID'];
		$all_data_arr[$row['JOB_ID']][$key][$row['BOOKING_ID']]['WIDTH_DIA_TYPE']=$row['WIDTH_DIA_TYPE'];
		$all_data_arr[$row['JOB_ID']][$key][$row['BOOKING_ID']]['DIA_WIDTH']=$row['DIA_WIDTH'];
		$all_data_arr[$row['JOB_ID']][$key][$row['BOOKING_ID']]['GSM_WEIGHT']=$row['GSM_WEIGHT'];
		$all_data_arr[$row['JOB_ID']][$key][$row['BOOKING_ID']]['COLOR_ID']=$row['COLOR_ID'];
		$all_data_arr[$row['JOB_ID']][$key][$row['BOOKING_ID']]['GMTS_COLOR_ID']=$row['GMTS_COLOR_ID'];
		$all_data_arr[$row['JOB_ID']][$key][$row['BOOKING_ID']]['UOM']=$row['UOM'];
		$all_data_arr[$row['JOB_ID']][$key][$row['BOOKING_ID']]['COLOR_SENSITIVE']=$row['COLOR_SIZE_SENSITIVE'];
		$all_data_arr[$row['JOB_ID']][$key][$row['BOOKING_ID']]['BOOKING_ID']=$row['BOOKING_ID'];
		$all_data_arr[$row['JOB_ID']][$key][$row['BOOKING_ID']]['BOOKING_NO']=$row['BOOKING_NO'];
		$all_data_arr[$row['JOB_ID']][$key][$row['BOOKING_ID']]['BOOKING_DATE']=$row['BOOKING_DATE'];
		$all_data_arr[$row['JOB_ID']][$key][$row['BOOKING_ID']]['FIN_FAB_QNTY']+=$row['FIN_FAB_QNTY'];
		$all_data_arr[$row['JOB_ID']][$key][$row['BOOKING_ID']]['BOOKING_AMOUNT']+=$row['BOOKING_AMOUNT'];
		$all_data_arr[$row['JOB_ID']][$key][$row['BOOKING_ID']]['CURRENCY_ID']=$row['CURRENCY_ID'];
		$all_data_arr[$row['JOB_ID']][$key][$row['BOOKING_ID']]['SUPPLIER_ID']=$row['SUPPLIER_ID'];

		if(!in_array($row['JOB_ID'].'__'.$key.'__'.$row['PO_ID'],$po_chk))
		{
			$po_chk[]=$row['JOB_ID'].'__'.$key.'__'.$row['PO_ID'];
			$po_info[$row['JOB_ID']][$key]['PO_ID'].=$row['PO_ID'].',';
			$po_info[$row['JOB_ID']][$key]['PO_QNTY']+=$row['PO_QUANTITY'];
			$po_info[$row['JOB_ID']][$key]['PO_RECEIVED_DATE'].=$row['PO_RECEIVED_DATE'].',';
			$po_info[$row['JOB_ID']][$key]['LAST_PO_RECEIVED_DATE']=$row['PO_RECEIVED_DATE'];
			$po_info[$row['JOB_ID']][$key]['PUB_SHIPMENT_DATE'].=$row['PUB_SHIPMENT_DATE'].',';
			$po_info[$row['JOB_ID']][$key]['LAST_PUB_SHIPMENT_DATE']=$row['PUB_SHIPMENT_DATE'];
		}
		$all_job_id.=$row['JOB_ID'].',';
		$all_book_id[$row['BOOKING_ID']]=$row['BOOKING_ID'];
		$all_pre_cost_id[$row['PRE_COST_MST_ID']]=$row['PRE_COST_MST_ID'];
	}
	unset($main_data);
	unset($po_chk);

	$all_pre_cost_arr=where_con_using_array($all_pre_cost_id,0,'mst_id');
	$pre_cost_approval_sql= "SELECT MST_ID, APPROVED_DATE, APPROVED_NO, CURRENT_APPROVAL_STATUS from approval_history where entry_form=15 $all_pre_cost_arr order by approved_no";
	// echo $pre_cost_approval_sql;

	$pre_cost_approval_data=sql_select($pre_cost_approval_sql);
	$pre_cost_approval_info=array();
	foreach($pre_cost_approval_data as $row)
	{
		$pre_cost_approval_info[$row['MST_ID']]['CURRENT_APPROVAL_STATUS']=$row['CURRENT_APPROVAL_STATUS'];
		$pre_cost_approval_info[$row['MST_ID']]['APPROVED_NO']=$row['APPROVED_NO'];
		$pre_cost_approval_info[$row['MST_ID']]['PRE_COST_APPROVED_DATE'].=$row['APPROVED_DATE'].',';
	}

	/* $book_id_arr=where_con_using_array($all_book_id,0,'d.id');
	"group by a.id, a.booking_no, b.fabric_color_id, c.construction, c.composition, c.gsm_weight, b.dia_width,c.lib_yarn_count_deter_id,c.uom";
	$booking_sql="SELECT c.CONSTRUCTION, c.COMPOSITION, c.GSM_WEIGHT, c.UOM, c.LIB_YARN_COUNT_DETER_ID, d.id as BOOKING_ID, sum(e.fin_fab_qnty) as FIN_FAB_QNTY, e.FABRIC_COLOR_ID, e.DIA_WIDTH
	from wo_pre_cost_fabric_cost_dtls c, wo_booking_mst d, wo_booking_dtls e
	where c.id=e.pre_cost_fabric_cost_dtls_id and d.id=e.booking_mst_id and d.booking_type=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 $book_id_arr";
	// echo $booking_sql;

	$booking_data=sql_select($booking_sql);
	$booking_info=array();
	foreach($booking_data as $row)
	{
		$pi_key=$row['CONSTRUCTION']."*".$row['COMPOSITION']."*".$row['GSM_WEIGHT']."*".$row['DIA_WIDTH']."*".$row['UOM']."*".$row['LIB_YARN_COUNT_DETER_ID']."*".$row['FABRIC_COLOR_ID'];
		$booking_info[$pi_key][$row["BOOKING_ID"]]["BOOKING_QNTY"]+=$row["FIN_FAB_QNTY"];
	}
	unset($booking_data);
	unset($book_id_arr); */

	$all_book_arr=where_con_using_array($all_book_id,0,'b.work_order_id');
	$pi_sql="SELECT a.id as PI_ID, a.PI_NUMBER, a.ITEM_CATEGORY_ID, a.PI_DATE, a.GOODS_RCV_STATUS, a.INSERT_DATE, b.work_order_id as BOOKING_ID, b.COLOR_ID, b.FABRIC_CONSTRUCTION, b.FABRIC_COMPOSITION, b.GSM, b.DIA_WIDTH, b.FAB_WEIGHT, b.DETERMINATION_ID, b.UOM, b.AMOUNT
	from com_pi_master_details a, com_pi_item_details b
	where a.id=b.pi_id and a.item_category_id in (2,3) and a.status_active=1 and b.status_active=1 $all_book_arr order by a.id";
	// echo $pi_sql;

	$pi_data=sql_select($pi_sql);
	$pi_info=array();$pi_status=array();
	foreach($pi_data as $row)
	{
		if($row['ITEM_CATEGORY_ID']==2)
		{
			$pi_key=$row['FABRIC_CONSTRUCTION']."*".$row['FABRIC_COMPOSITION']."*".$row['GSM']."*".$row['DIA_WIDTH']."*".$row['UOM']."*".$row['DETERMINATION_ID']."*".$row['COLOR_ID'];
		}
		else
		{
			$pi_key=$row['FABRIC_CONSTRUCTION']."*".$row['FABRIC_COMPOSITION']."*".$row['FAB_WEIGHT']."*".$row['DIA_WIDTH']."*".$row['UOM']."*".$row['DETERMINATION_ID']."*".$row['COLOR_ID'];
		}

		$pi_info[$pi_key][$row['BOOKING_ID']]['PI_ID'].=$row['PI_ID'].',';
		$pi_info[$pi_key][$row['BOOKING_ID']]['PI_NUMBER'].=$row['PI_NUMBER'].', ';
		$pi_info[$pi_key][$row['BOOKING_ID']]['PI_DATE'].=change_date_format($row['PI_DATE']).', ';
		$pi_info[$pi_key][$row['BOOKING_ID']]['PI_AMOUNT']+=$row['AMOUNT'];
		$pi_info[$pi_key][$row['BOOKING_ID']]['INSERT_DATE'].=change_date_format($row['INSERT_DATE']).', ';
		$pi_status[$row['PI_ID']]['GOODS_RCV_STATUS']=$row['GOODS_RCV_STATUS'];
		$all_pi_id[$row['PI_ID']]=$row['PI_ID'];
	}
	unset($pi_sql);
	unset($pi_data);
	unset($all_book_arr);

	if(count($all_pi_id)>0)
	{
		$all_pi_arr=where_con_using_array($all_pi_id,0,'master_tble_id');
		$pi_file_sql="SELECT ID, MASTER_TBLE_ID, INSERT_DATE from common_photo_library where form_name='proforma_invoice' and is_deleted=0 and file_type=2 $all_pi_arr group by master_tble_id, insert_date order by id desc";
		// echo $pi_file_sql;

		$pi_file_data=sql_select($pi_file_sql);
		$pi_file_info=array();
		foreach($pi_file_data as $row)
		{
			$pi_file_info[$row['MASTER_TBLE_ID']]['PI_FILE_DATE']=$row['INSERT_DATE'];
		}
		unset($all_pi_arr);

		$all_pi_arr=where_con_using_array($all_pi_id,0,'mst_id');
		$pi_approval_sql= "SELECT MST_ID, APPROVED_DATE from approval_history where entry_form in(21,27) and current_approval_status=1 $all_pi_arr";
		// echo $pi_approval_sql;

		$pi_approval_data=sql_select($pi_approval_sql);
		$pi_approval_info=array();
		foreach($pi_approval_data as $row)
		{
			$pi_approval_info[$row['MST_ID']]['PI_APPROVED_DATE']=$row['APPROVED_DATE'];
		}
		unset($all_pi_arr);

		$all_pi_arr=where_con_using_array($all_pi_id,0,'a.pi_id');
		$btb_sql="SELECT a.PI_ID, b.ID, b.LC_NUMBER, b.LC_DATE, d.export_lc_no as LC_SC_NO, d.LAST_SHIPMENT_DATE, d.EXPIRY_DATE
		from com_btb_lc_pi a, com_btb_lc_master_details b, com_btb_export_lc_attachment c, com_export_lc d
		where a.com_btb_lc_master_details_id=b.id and b.id=c.import_mst_id and c.lc_sc_id=d.id and c.is_lc_sc=0 and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 $all_pi_arr
		union all 
		SELECT a.PI_ID, b.ID, b.LC_NUMBER, b.LC_DATE, d.contract_no as LC_SC_NO, d.LAST_SHIPMENT_DATE, d.EXPIRY_DATE
		from com_btb_lc_pi a, com_btb_lc_master_details b, com_btb_export_lc_attachment c, com_sales_contract d
		where a.com_btb_lc_master_details_id=b.id and b.id=c.import_mst_id and c.lc_sc_id=d.id and c.is_lc_sc=1 and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 $all_pi_arr
		order by ID";
		// echo $btb_sql;

		$btb_data=sql_select($btb_sql);
		$dtb_info=array();
		foreach($btb_data as $row)
		{
			$dtb_info[$row['PI_ID']]['LC_NUMBER']=$row['LC_NUMBER'];
			$dtb_info[$row['PI_ID']]['LC_DATE']=$row['LC_DATE'];
			$dtb_info[$row['PI_ID']]['LC_SC_NO'].=$row['LC_SC_NO'].',';
			$dtb_info[$row['PI_ID']]['LAST_SHIPMENT_DATE'].=change_date_format($row['LAST_SHIPMENT_DATE']).',';
			$dtb_info[$row['PI_ID']]['EXPIRY_DATE'].=change_date_format($row['EXPIRY_DATE']).',';
			$all_btb_id[$row['ID']]=$row['ID'];
		}

		unset($btb_sql);
		unset($btb_data);
		unset($all_pi_arr);

		if(count($all_btb_id)>0)
		{
			$all_btb_arr=where_con_using_array($all_btb_id,0,'master_tble_id');
			$btb_file_sql="SELECT ID, MASTER_TBLE_ID, INSERT_DATE from common_photo_library where form_name='BTBMargin LC' and is_deleted=0 and file_type=2 $all_btb_arr group by master_tble_id, insert_date order by id desc";
			// echo $btb_file_sql;
	
			$btb_file_data=sql_select($btb_file_sql);
			$btb_file_info=array();
			foreach($btb_file_data as $row)
			{
				$btb_file_info[$row['MASTER_TBLE_ID']]['BTB_FILE_DATE']=$row['INSERT_DATE'];
			}
			unset($all_btb_arr);
		}

		$all_pi_arr=where_con_using_array($all_pi_id,0,'a.booking_id');
		$all_book_arr=where_con_using_array($all_book_id,0,'a.booking_id');
		/* $receive_qty_sql = "SELECT a.ID, a.RECV_NUMBER,a.RECEIVE_DATE, a.ITEM_CATEGORY, a.RECEIVE_BASIS, a.BOOKING_ID, sum(b.order_qnty) as RECEIVE_QNTY,sum(b.order_amount) as RECEIVE_AMOUNT,b.PROD_ID, c.ITEM_GROUP_ID, c.COLOR, c.GSM, c.DIA_WIDTH, c.ITEM_DESCRIPTION, c.PRODUCT_NAME_DETAILS, c.unit_of_measure as UOM, c.DETARMINATION_ID, c.WEIGHT
		from inv_receive_master a, inv_transaction b, product_details_master c
		where a.id=b.mst_id and b.prod_id=c.id and a.receive_basis=1 $all_pi_arr and b.transaction_type=1 and a.item_category in (2,3) and a.status_active=1 and b.status_active=1 and c.status_active=1 group by a.id, a.recv_number, a.receive_date, a.item_category, a.receive_basis, a.booking_id, c.item_group_id,b.prod_id, c.color, c.gsm, c.dia_width, c.item_description,c.product_name_details, c.unit_of_measure, c.detarmination_id, c.weight
		union all
		SELECT a.ID, a.RECV_NUMBER,a.RECEIVE_DATE, a.ITEM_CATEGORY, a.RECEIVE_BASIS, a.BOOKING_ID, sum(b.order_qnty) as RECEIVE_QNTY,sum(b.order_amount) as RECEIVE_AMOUNT,b.PROD_ID, c.ITEM_GROUP_ID, c.COLOR, c.GSM, c.DIA_WIDTH, c.ITEM_DESCRIPTION, c.PRODUCT_NAME_DETAILS, c.unit_of_measure as UOM, c.DETARMINATION_ID, c.WEIGHT
		from inv_receive_master a, inv_transaction b, product_details_master c
		where a.id=b.mst_id and b.prod_id=c.id and a.receive_basis=2 $all_book_arr and b.transaction_type=1 and a.item_category in (2,3) and a.status_active=1 and b.status_active=1 and c.status_active=1 
		group by a.id, a.recv_number, a.receive_date, a.item_category, a.receive_basis, a.booking_id, c.item_group_id,b.prod_id, c.color, c.gsm, c.dia_width, c.item_description,c.product_name_details, c.unit_of_measure, c.detarmination_id, c.weight
		order by ID"; */
		$receive_qty_sql = "SELECT a.ID, a.RECV_NUMBER,a.RECEIVE_DATE, a.ITEM_CATEGORY, a.RECEIVE_BASIS, a.BOOKING_ID, sum(b.order_qnty) as RECEIVE_QNTY,sum(b.order_amount) as RECEIVE_AMOUNT,b.PROD_ID, c.ITEM_GROUP_ID, c.COLOR, c.GSM, c.DIA_WIDTH, c.ITEM_DESCRIPTION, c.PRODUCT_NAME_DETAILS, c.unit_of_measure as UOM, c.DETARMINATION_ID, c.WEIGHT, d.FABRIC_DESCRIPTION_ID
		from inv_receive_master a, inv_transaction b, product_details_master c, pro_finish_fabric_rcv_dtls d
		where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and a.receive_basis=1 $all_pi_arr and b.transaction_type=1 and a.item_category in (2,3) and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 
		group by a.id, a.recv_number, a.receive_date, a.item_category, a.receive_basis, a.booking_id, c.item_group_id,b.prod_id, c.color, c.gsm, c.dia_width, c.item_description,c.product_name_details, c.unit_of_measure, c.detarmination_id, c.weight, d.fabric_description_id
		union all
		SELECT a.ID, a.RECV_NUMBER,a.RECEIVE_DATE, a.ITEM_CATEGORY, a.RECEIVE_BASIS, a.BOOKING_ID, sum(b.order_qnty) as RECEIVE_QNTY,sum(b.order_amount) as RECEIVE_AMOUNT,b.PROD_ID, c.ITEM_GROUP_ID, c.COLOR, c.GSM, c.DIA_WIDTH, c.ITEM_DESCRIPTION, c.PRODUCT_NAME_DETAILS, c.unit_of_measure as UOM, c.DETARMINATION_ID, c.WEIGHT, d.FABRIC_DESCRIPTION_ID
		from inv_receive_master a, inv_transaction b, product_details_master c, pro_finish_fabric_rcv_dtls d
		where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and a.receive_basis=2 $all_book_arr and b.transaction_type=1 and a.item_category in (2,3) and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1
		group by a.id, a.recv_number, a.receive_date, a.item_category, a.receive_basis, a.booking_id, c.item_group_id,b.prod_id, c.color, c.gsm, c.dia_width, c.item_description,c.product_name_details, c.unit_of_measure, c.detarmination_id, c.weight, d.fabric_description_id
		order by ID";
		// echo $receive_qty_sql;

		$receive_qty_result=sql_select($receive_qty_sql);
		$rcv_info=array();$rcv_date=array();
		foreach($receive_qty_result as $row)
		{

			if($row['FABRIC_DESCRIPTION_ID']==0 || $row['FABRIC_DESCRIPTION_ID']=="")
			{
				$item_description_arr=explode(',',$row['ITEM_DESCRIPTION']);
			}
			else
			{
				$item_description_arr=explode(',',$composition_arr[$row['FABRIC_DESCRIPTION_ID']]);
			}

			if($row['ITEM_CATEGORY']==2)
			{
				$key=$row['RECEIVE_BASIS']."*".$row['ITEM_CATEGORY']."*".$row['BOOKING_ID']."*".trim($item_description_arr[0])."*".trim($item_description_arr[1])."*".$row['GSM']."*".$row['DIA_WIDTH']."*".$row['UOM']."*".$row['DETARMINATION_ID']."*".$row['COLOR'];
			}
			else
			{
				$key=$row['RECEIVE_BASIS']."*".$row['ITEM_CATEGORY']."*".$row['BOOKING_ID']."*".trim($item_description_arr[0])."*".trim($item_description_arr[1])."*".$row['WEIGHT']."*".$row['DIA_WIDTH']."*".$row['UOM']."*".$row['DETARMINATION_ID']."*".$row['COLOR'];
			}

			$rcv_info[$key]['RCV_ID'].= $row['ID'].',';
			$rcv_info[$key]['PROD_ID'].= $row['PROD_ID'].',';
			$rcv_info[$key]['QNTY']+= $row['RECEIVE_QNTY'];
			$rcv_info[$key]['AMOUNT']+= $row['RECEIVE_AMOUNT'];
			$rcv_date[$row['ID']]['RECEIVE_DATE']= $row['RECEIVE_DATE'];
		}
		unset($receive_qty_result);
		unset($all_pi_id);
		unset($all_book_id);
		unset($all_pi_arr);
		unset($all_book_arr);
	}	

	$all_job_id=implode(",",array_unique(explode(",",rtrim($all_job_id,','))));
	$condition= new condition();     
	if($all_job_id!='')
	{
		$condition->jobid_in("$all_job_id");
		$condition->init();
		$fabric= new fabric($condition);
		$fabric_qty_arr= $fabric->getQtyArray_by_OrderFabriccostidFabriccolorAndDiaWidth_knitAndwoven_greyAndfinish();
		$fabric_amount_arr= $fabric->getAmountArray_by_OrderFabriccostidFabriccolorAndDiaWidth_knitAndwoven_greyAndfinish();
		// var_dump($fabric_qty_arr['woven']['finish']);
	}

	foreach($all_data_arr as $job_id=>$job_value)
	{
		foreach($job_value as $key=>$book_id)
		{
			foreach($book_id as $row)
			{
				$pi_key=$row['CONSTRUCTION']."*".$row['COMPOSITION']."*".$row['GSM_WEIGHT']."*".$row['DIA_WIDTH']."*".$row['UOM']."*".$row['COUNT_DETER_ID']."*".$row['COLOR_ID'];

				$rcv_booking_key="2*".$row['ITEM_CATEGORY']."*".$row['BOOKING_ID']."*".$row['CONSTRUCTION']."*".$row['COMPOSITION']."*".$row['GSM_WEIGHT']."*".$row['DIA_WIDTH']."*".$row['UOM']."*".$row['COUNT_DETER_ID']."*".$row['COLOR_ID'];

				$pi_arr=array_unique(explode(",",rtrim($pi_info[$pi_key][$row['BOOKING_ID']]['PI_ID'],',')));
				$rcv_qnty=$rcv_amount=0;
				foreach($pi_arr as $val)
				{										
					if($pi_status[$val]['GOODS_RCV_STATUS']==1)
					{
						$rcv_qnty+=$rcv_info[$rcv_booking_key]['QNTY'];
						$rcv_amount+=$rcv_info[$rcv_booking_key]['AMOUNT'];
					}
					else
					{
						$rcv_pi_key="1*".$row['ITEM_CATEGORY']."*".$val."*".$row['CONSTRUCTION']."*".$row['COMPOSITION']."*".$row['GSM_WEIGHT']."*".$row['DIA_WIDTH']."*".$row['UOM']."*".$row['COUNT_DETER_ID']."*".$row['COLOR_ID'];
						$rcv_qnty+=$rcv_info[$rcv_pi_key]['QNTY'];
						$rcv_amount+=$rcv_info[$rcv_pi_key]['AMOUNT'];
					}
				}

				$total_mrr_blance_qty=$row["FIN_FAB_QNTY"]-$rcv_qnty;
				if(($cbo_rcv_status==1 && $total_mrr_blance_qty==0) || ($cbo_rcv_status==2 && $total_mrr_blance_qty>0))
				{
					$total_booking_qnty[$job_id][$key]+=$row["FIN_FAB_QNTY"];
					$total_booking_amount[$job_id]+=$row["BOOKING_AMOUNT"];
					$total_mrr_qnty[$job_id][$key]+=$rcv_qnty;
					$total_mrr_amount[$job_id]+=$rcv_amount;
					$total_mrr_blance_qnty[$job_id][$key]+=$row["FIN_FAB_QNTY"]-$rcv_qnty;
					$total_mrr_blance_amount[$job_id]+=$row["BOOKING_AMOUNT"]-$rcv_amount;
					
					$job_count[$job_id]++;
					$key_count[$job_id][$key]++;
				}
			}
		}
	}

	ob_start();

	?>
	<style>
		.wrd_brk{word-break: break-all;}
		.left{text-align: left;}
		.center{text-align: center;}
		.right{text-align: right;}
	</style>

	<div style="width:6470px; margin-left:10px">
		<fieldset style="width:100%;">	 
			<table width="6470px" cellpadding="0" cellspacing="0" id="caption">
				<tr>  
					<td width="100%" align="left" colspan="64" class="form_caption" style="font-size:16px"><strong>Procurement Progress Report (Fabric)</strong></td>
				</tr>  
			</table>
			<br />
            <table width="6450" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <tr>
						<!-- Job -->
                        <th width="60">Company</th>
                        <th width="100">Job Number</th>
                        <th width="120">Buyer</th>
                        <th width="120">Style</th>
                        <th width="100">Style Qty/Job Quatity</th>
						<!-- PO -->
                        <th width="100">Buyer PO Number</th>
                        <th width="100">1st Rcv Date (PO)</th>
                        <th width="100">Latest Rcv Date (PO)</th>
                        <th width="100">1st Shipment Date </th>
                        <th width="100">Last Shipment Date</th>
                        <th width="100">Gmts Item</th>
                        <!-- Budget -->
                        <th width="100">Body Part</th>
                        <th width="100">Body Part Type</th>
                        <th width="100">Fab Nature</th>
                        <th width="100">Color Type</th>
                        <th width="100">Fab Description</th>
                        <th width="100">Width/ Dia Type</th>
                        <th width="100">Fabric Weight</th>
                        <th width="100">Color</th>
                        <th width="100">UOM</th>
                        <th width="100">Pre-cost/ Budgeted Qty.</th>
                        <th width="100">Pre-cost/ Budgeted Price</th>
                        <th width="100">Pre-cost approval date (1st date)</th>
                        <th width="100">Pre-cost approval date (Last date)</th>
                        <th width="100">Pre-cost/ Budgeted Price (Revised)</th>
                        <!-- Booking -->
                        <th width="100">Booking No.</th>
                        <th width="100">Booking Date</th>
                        <th width="100">Booking QTY</th> 
                        <th width="100">Total Booking  Qty</th> 
                        <th width="100">Booking balance Qty</th> 
                        <th width="100">Booking Price</th> 
                        <th width="100">Currency</th> 
                        <th width="100">Supplier</th> 
                        <!-- PI -->
                        <th width="100">PI No.</th> 
                        <th width="100">PI Date</th> 
                        <th width="100">PI Value</th> 
                        <th width="100">PI Insert Date</th> 
                        <th width="100">PI Apr Date & Time</th> 
                        <!-- BTB -->
                        <th width="100">B2B LC </th> 
                        <th width="100">B2B LC Date</th> 
                        <th width="100">LC/SC Number</th> 
                        <th width="100">LC/SC Last shipment date</th> 
                        <th width="100">LC/SC Expire date</th> 
                        <!-- MRM -->
                        <th width="100">MRR No.</th> 
                        <th width="100">MRR Qty</th> 
                        <th width="100">Balance Qty (Yet to Receive)</th> 
                        <th width="100">Total MRR Qty</th> 
                        <th width="100">Total Balance Qty</th> 
                        <!-- Others -->
                        <th width="100">Pre-Cost (Value)</th> 
                        <th width="100">Booking (Value) US$</th> 
                        <th width="100">Total Booking (Value) US$</th> 
                        <th width="100">MMR (Value) US$</th> 
                        <th width="100">Total MMR (Value) US$</th> 
                        <th width="100">Differnce Value (Booking-MRR)</th> 
                        <th width="100">Total Differnce Value</th> 
                        <th width="100">1st Rcv Date (Material)</th> 
                        <th width="100">Last Rcv Date (Material)</th> 
                        <th width="100">Remarks</th> 
                        <th width="100">Booking Time (Days)</th> 
                        <th width="100">PI Uploaing Time (Days)</th> 
                        <th width="100">PI Final Approval Time (Days)</th> 
                        <th width="100">BTB LC open Time</th> 
                        <th width="100">Actual Delivery Time(days)</th> 
                        <th >Total Procurement Time(days)</th> 
                    </tr>
                </thead>
                <tbody>
					<?
						$i=1;
						$job_chk=$job_chk2=$job_chk3=array();$key_chk=$key_chk2=$key_chk3=array();
						
						foreach($all_data_arr as $job_id=>$job_value)
						{
							foreach($job_value as $key=>$book_id)
							{
								foreach($book_id as $row)
								{
									
									if( $i % 2 == 0 ){ $bgcolor="#E9F3FF"; }else{ $bgcolor = "#FFFFFF"; }
									$job_rowspan=$job_count[$job_id];
									$key_rowspan=$key_count[$job_id][$key];

									$pi_key=$row['CONSTRUCTION']."*".$row['COMPOSITION']."*".$row['GSM_WEIGHT']."*".$row['DIA_WIDTH']."*".$row['UOM']."*".$row['COUNT_DETER_ID']."*".$row['COLOR_ID'];

									$rcv_booking_key="2*".$row['ITEM_CATEGORY']."*".$row['BOOKING_ID']."*".$row['CONSTRUCTION']."*".$row['COMPOSITION']."*".$row['GSM_WEIGHT']."*".$row['DIA_WIDTH']."*".$row['UOM']."*".$row['COUNT_DETER_ID']."*".$row['COLOR_ID'];

									$pi_arr=array_unique(explode(",",rtrim($pi_info[$pi_key][$row['BOOKING_ID']]['PI_ID'],',')));
									$btb_number=$btb_date=$lc_sc_no=$last_shipment_date=$expiry_date=$rcv_number=$rcv_first_date=$rcv_last_date=$pi_file_first_date=$pi_approval_date=$btb_file_first_date=$all_pi_approval_date=$pre_cost_first_date=$pre_last_approval_date=$rcv_id=$rcv_prod_id="";
									$rcv_qnty=$rcv_amount=$fabric_qty=$fabric_amount=0;
									foreach($pi_arr as $val)
									{
										$btb_number.=$dtb_info[$val]['LC_NUMBER'].',';
										$btb_date.=$dtb_info[$val]['LC_DATE'].',';
										$lc_sc_no.=$dtb_info[$val]['LC_SC_NO'].',';
										$last_shipment_date.=$dtb_info[$val]['LAST_SHIPMENT_DATE'].',';
										$expiry_date.=$dtb_info[$val]['EXPIRY_DATE'].',';
										if($pi_file_info[$val]['PI_FILE_DATE']){ $pi_file_date_arr[$val]=$pi_file_info[$val]['PI_FILE_DATE'];}
										if($pi_approval_info[$val]['PI_APPROVED_DATE']){ $pi_approval_arr[$val]=$pi_approval_info[$val]['PI_APPROVED_DATE'];}
										$btb_id_arr=array_unique(explode(",",rtrim($dtb_info[$val]['LC_NUMBER'],',')));
										foreach($btb_id_arr as $btb_id)
										{
											$btb_file_date_arr[$btb_id]=$btb_file_info[$btb_id]['BTB_FILE_DATE'];
										}
										
										if($pi_status[$val]['GOODS_RCV_STATUS']==1)
										{
											$rcv_id.=$rcv_info[$rcv_booking_key]['RCV_ID'].',';
											$rcv_prod_id.=$rcv_info[$rcv_booking_key]['PROD_ID'].',';
											$rcv_number.=$rcv_info[$rcv_booking_key]['RCV_NUMBER'].',';
											$rcv_qnty+=$rcv_info[$rcv_booking_key]['QNTY'];
											$rcv_amount+=$rcv_info[$rcv_booking_key]['AMOUNT'];
											$rcv_id_arr=array_unique(explode(",",rtrim($rcv_info[$rcv_booking_key]['RCV_ID'],',')));
											foreach($rcv_id_arr as $rcv_id_key)
											{
												$rcv_date_arr[$rcv_id_key]=$rcv_date[$rcv_id_key]['RECEIVE_DATE'];
											}
										}
										else
										{
											$rcv_pi_key="1*".$row['ITEM_CATEGORY']."*".$val."*".$row['CONSTRUCTION']."*".$row['COMPOSITION']."*".$row['GSM_WEIGHT']."*".$row['DIA_WIDTH']."*".$row['UOM']."*".$row['COUNT_DETER_ID']."*".$row['COLOR_ID'];
											$rcv_id.=$rcv_info[$rcv_pi_key]['RCV_ID'].',';
											$rcv_prod_id.=$rcv_info[$rcv_pi_key]['PROD_ID'].',';
											$rcv_number.=$rcv_info[$rcv_pi_key]['RCV_NUMBER'].',';
											$rcv_qnty+=$rcv_info[$rcv_pi_key]['QNTY'];
											$rcv_amount+=$rcv_info[$rcv_pi_key]['AMOUNT'];
											$rcv_id_arr=array_unique(explode(",",rtrim($rcv_info[$rcv_pi_key]['RCV_ID'],',')));
											foreach($rcv_id_arr as $rcv_id_key)
											{
												$rcv_date_arr[$rcv_id_key]=$rcv_date[$rcv_id_key]['RECEIVE_DATE'];
											}
										}
									}

									if(count($rcv_date_arr)>0)
									{
										ksort($rcv_date_arr);
										$k=1;
										foreach($rcv_date_arr as $val)
										{
											if($k==1){$rcv_first_date=$val;}
											$rcv_last_date=$val;
											$k++;
										}
										unset($rcv_date_arr);
									}

									if(count($pi_file_date_arr)>0)
									{
										ksort($pi_file_date_arr);
										$k=1;
										foreach($pi_file_date_arr as $val)
										{
											if($k==1){$pi_file_first_date=$val;}
											$k++;
										}
										unset($pi_file_date_arr);
									}

									if(count($btb_file_date_arr)>0)
									{
										ksort($btb_file_date_arr);
										$k=1;
										foreach($btb_file_date_arr as $val)
										{
											if($k==1){$btb_file_first_date=$val;}
											$k++;
										}
										unset($btb_file_date_arr);
									}

									if(count($pi_approval_arr)>0)
									{
										ksort($pi_approval_arr);
										$k=1;
										foreach($pi_approval_arr as $val)
										{
											$all_pi_approval_date.=$val.', ';
											if($k==1){$pi_approval_date=$val;}
											$k++;
										}
										unset($pi_approval_arr);
									}

									if($pre_cost_approval_info[$row['PRE_COST_MST_ID']]['CURRENT_APPROVAL_STATUS']==1)
									{
										$pre_cost_approval_arr=explode(",",$pre_cost_approval_info[$row['PRE_COST_MST_ID']]['PRE_COST_APPROVED_DATE']);
										$k=1;
										foreach($pre_cost_approval_arr as $val)
										{
											if($k==1){$pre_cost_first_date=change_date_format($val);}
											$pre_last_approval_date=change_date_format($val);
											$k++;
										}
									}
									
									$rcv_id=implode(",",array_unique(explode(",",rtrim($rcv_id,','))));
									$rcv_prod_id=implode(",",array_unique(explode(",",rtrim($rcv_prod_id,','))));
									$total_mrr_blance_qty=$row["FIN_FAB_QNTY"]-$rcv_qnty;
									if(($cbo_rcv_status==1 && $total_mrr_blance_qty==0) || ($cbo_rcv_status==2 && $total_mrr_blance_qty>0) )
									{
										
									?>
										<tr bgcolor="<?=$bgcolor;?>"  onclick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>">
											<?
												if(!in_array($job_id.'__'.$key,$key_chk))
												{
													$key_chk[]=$job_id.'__'.$key;
													$po_received_date=explode(",",$po_info[$job_id][$key]['PO_RECEIVED_DATE']);
													$pub_shipment_date=explode(",",$po_info[$job_id][$key]['PUB_SHIPMENT_DATE']);
													$po_id=rtrim($po_info[$job_id][$key]['PO_ID'],",");
													$po_id_arr=array_unique(explode(",",$po_id));
													$FabricColorId="";
													if($row['COLOR_SENSITIVE']==3)
													{
														$FabricColorId=$row['COLOR_ID'];
													}
													foreach($po_id_arr as $po_val)
													{
														if($row['ITEM_CATEGORY']==2)
														{
															$fabric_qty+=array_sum($fabric_qty_arr['knit']['finish'][$po_val][$row['FABRIC_COST_DTLS']][$row['GMTS_COLOR_ID']][$FabricColorId][$row['DIA_WIDTH']]);
															$fabric_amount+=array_sum($fabric_amount_arr['knit']['finish'][$po_val][$row['FABRIC_COST_DTLS']][$row['GMTS_COLOR_ID']][$FabricColorId][$row['DIA_WIDTH']]);
														}
														else
														{
															$fabric_qty+=array_sum($fabric_qty_arr['woven']['finish'][$po_val][$row['FABRIC_COST_DTLS']][$row['GMTS_COLOR_ID']][$FabricColorId][$row['DIA_WIDTH']]);
															$fabric_amount+=array_sum($fabric_amount_arr['woven']['finish'][$po_val][$row['FABRIC_COST_DTLS']][$row['GMTS_COLOR_ID']][$FabricColorId][$row['DIA_WIDTH']]);
														}
													}
													?>
														<!----- Job ----->
														<td rowspan="<?=$key_rowspan;?>"  valign="middel"><?=$lib_company[$row["COMPANY_NAME"]];?></td>
														<td rowspan="<?=$key_rowspan;?>"  valign="middel"><?=$row["JOB_NO"];?></td>
														<td rowspan="<?=$key_rowspan;?>"  valign="middel"><?=$lib_buyer[$row["BUYER_NAME"]];?></td>
														<td rowspan="<?=$key_rowspan;?>"  valign="middel"><?=$row["STYLE_REF_NO"];?></td>
														<td rowspan="<?=$key_rowspan;?>"  valign="middel" class="right"><?=$po_info[$job_id][$key]['PO_QNTY'];?></td>
														<!----- PO ----->
														<td rowspan="<?=$key_rowspan;?>" class="center"  valign="middel"><a href="##" onClick="openmypage_po('<? echo $po_id; ?>','PO Information','po_popup');" >View</a></td>
														<td rowspan="<?=$key_rowspan;?>" class="center"  valign="middel"><?=change_date_format($po_received_date[0]);?></td>
														<td rowspan="<?=$key_rowspan;?>" class="center"  valign="middel"><?=change_date_format($po_info[$job_id][$key]['LAST_PO_RECEIVED_DATE']);?></td>
														<td rowspan="<?=$key_rowspan;?>" class="center"  valign="middel"><?=change_date_format($pub_shipment_date[0]);?></td>
														<td rowspan="<?=$key_rowspan;?>" class="center"  valign="middel"><?=change_date_format($po_info[$job_id][$key]['LAST_PUB_SHIPMENT_DATE']);?></td>
														<td rowspan="<?=$key_rowspan;?>" class="center"  valign="middel"><?=$garments_item[$row["GARMENTS_ITEM"]];?></td>
														<td rowspan="<?=$key_rowspan;?>" class="center"  valign="middel"><?=$body_part[$row["BODY_PART"]];?></td>
														<!----- Budget ----->
														<td rowspan="<?=$key_rowspan;?>" class="center"  valign="middel"><?=$body_part_type[$row["BODY_PART_TYPE"]];?></td>
														<td rowspan="<?=$key_rowspan;?>" class="center"  valign="middel"><?=$item_category[$row["ITEM_CATEGORY"]];?></td>
														<td rowspan="<?=$key_rowspan;?>" class="center"  valign="middel"><?=$color_type[$row["COLOR_TYPE"]];?></td>
														<td rowspan="<?=$key_rowspan;?>" class="center"  valign="middel"><?=$row["FABRIC_DESCRIPTION"];?></td>
														<td rowspan="<?=$key_rowspan;?>" class="center"  valign="middel"><?=$fabric_typee[$row["WIDTH_DIA_TYPE"]];?></td>
														<td rowspan="<?=$key_rowspan;?>" class="center"  valign="middel"><?=$row["GSM_WEIGHT"];?></td>
														<td rowspan="<?=$key_rowspan;?>" class="center"  valign="middel"><?=$lib_color[$row["COLOR_ID"]];?></td>
														<td rowspan="<?=$key_rowspan;?>" class="center"  valign="middel"><?=$unit_of_measurement[$row["UOM"]];?></td>
														<td rowspan="<?=$key_rowspan;?>" class="right"><?=number_format($fabric_qty,2);?></td>
														<td rowspan="<?=$key_rowspan;?>" class="right"><?=number_format($fabric_amount/$fabric_qty,2);?></td>
														<td rowspan="<?=$key_rowspan;?>" class="center"  valign="middel"><?=$pre_cost_first_date;?></td>
														<td rowspan="<?=$key_rowspan;?>" class="center"  valign="middel"><?=$pre_last_approval_date;?></td>
														<td rowspan="<?=$key_rowspan;?>" class="center"  valign="middel"><?=$pre_cost_approval_info[$row['PRE_COST_MST_ID']]['APPROVED_NO'];?></td>
													<?
												}
											?>
											<!----- Booking ----->
											<td class="center"><?=$row["BOOKING_NO"];?></td>
											<td class="center"><?=change_date_format($row["BOOKING_DATE"]);?></td>
											<td class="right"><?=number_format($row["FIN_FAB_QNTY"],2);?></td>
											<?
												if(!in_array($job_id.'__'.$key,$key_chk2))
												{
													$key_chk2[]=$job_id.'__'.$key;
													?>
														<td rowspan="<?=$key_rowspan;?>" class="right" valign="middel"><?=number_format($total_booking_qnty[$job_id][$key],2);?></td>
														<td rowspan="<?=$key_rowspan;?>" class="right" valign="middel"><?=number_format($fabric_qty-$total_booking_qnty[$job_id][$key],2);?></td>
													<?
												}
											?>
											<td class="right"><?=number_format($row["BOOKING_AMOUNT"]/$row["FIN_FAB_QNTY"],2);?></td>
											<td class="center"><?=$currency[$row["CURRENCY_ID"]];?></td>
											<td ><?=$lib_supplier[$row["SUPPLIER_ID"]];?></td>
											<!----- PI ----->
											<td ><?=rtrim($pi_info[$pi_key][$row['BOOKING_ID']]['PI_NUMBER'],', ');?></td>
											<td ><?=rtrim($pi_info[$pi_key][$row['BOOKING_ID']]['PI_DATE'],', ');?></td>
											<td ><?=number_format($pi_info[$pi_key][$row['BOOKING_ID']]['PI_AMOUNT'],2);?></td>
											<td ><?=rtrim($pi_info[$pi_key][$row['BOOKING_ID']]['INSERT_DATE'],', ');?></td>
											<td ><?=rtrim($all_pi_approval_date,", ");?></td>
											<!----- BTB ----->
											<td ><?=implode(", ",array_unique(explode(",",rtrim($btb_number,','))));?></td>
											<td ><?=implode(", ",array_unique(explode(",",rtrim($btb_date,','))));?></td>
											<td ><?=implode(", ",array_unique(explode(",",rtrim($lc_sc_no,','))));?></td>
											<td ><?=implode(", ",array_unique(explode(",",rtrim($last_shipment_date,','))));?></td>
											<td ><?=implode(", ",array_unique(explode(",",rtrim($expiry_date,','))));?></td>
											<!----- MRM ----->
											<td class="center"><a href="##" onClick="openmypage_rcv('<? echo $rcv_id; ?>','<? echo $rcv_prod_id; ?>','Receive Information','rcv_popup');" >View</a></td>
											<td class="right"><?=number_format($rcv_qnty,2);?></td>
											<td class="right"><?=number_format($row["FIN_FAB_QNTY"]-$rcv_qnty,2)?></td>
											<?
												if(!in_array($job_id.'__'.$key,$key_chk3))
												{
													$key_chk3[]=$job_id.'__'.$key;
													?>
														<td rowspan="<?=$key_rowspan;?>" class="right" valign="middel"><?=number_format($total_mrr_qnty[$job_id][$key],2);?></td>
														<td rowspan="<?=$key_rowspan;?>" class="right" valign="middel"><?=number_format($total_mrr_blance_qnty[$job_id][$key],2);?></td>
														<!----- Others ----->
														<td rowspan="<?=$key_rowspan;?>" class="right" valign="middel"><?=number_format($fabric_amount,2);?></td>
													<?
												}
											?>
											<td class="right"><?=number_format($row["BOOKING_AMOUNT"],2);?></td>
											<?
												if(!in_array($job_id,$job_chk))
												{
													$job_chk[]=$job_id;
													?>
														<td rowspan="<?=$job_rowspan;?>" class="right" valign="middel"><?=number_format($total_booking_amount[$job_id],2);?></td>
													<?
												}
											?>
											<td class="right"><?=number_format($rcv_amount,2);?></td>
											<?
												if(!in_array($job_id,$job_chk2))
												{
													$job_chk2[]=$job_id;
													?>
														<td rowspan="<?=$job_rowspan;?>" class="right" valign="middel"><?=number_format($total_mrr_amount[$job_id],2);?></td>
													<?
												}
											?>
											<td class="right"><?=number_format($row["BOOKING_AMOUNT"]-$rcv_amount,2);?></td>
											<?
												if(!in_array($job_id,$job_chk3))
												{
													$job_chk3[]=$job_id;
													?>
														<td rowspan="<?=$job_rowspan;?>" class="right" valign="middel"><?=number_format($total_mrr_blance_amount[$job_id],2);?></td>
													<?
												}
											?>
											<td class="center"><?=change_date_format($rcv_first_date);?></td>
											<td class="center"><?=change_date_format($rcv_last_date);?></td>
											<td >&nbsp;</td>
											<td class="center">
												<?
													$diff=date_diff(date_create(date("Y-m-d",strtotime($row["BUDGET_DATE"]))),date_create(date("Y-m-d",strtotime($row["BOOKING_DATE"]))));
													echo $diff->days." days";
												?>
											</td>
											<td class="center">
												<?
													if($pi_file_first_date)
													{
														$diff=date_diff(date_create(date("Y-m-d",strtotime($row["BOOKING_DATE"]))),date_create(date("Y-m-d",strtotime($pi_file_first_date))));
														echo $diff->days." days";
													}
												?>
											</td>
											<td class="center">
												<?
													if($pi_file_first_date!='' && $pi_approval_date!='')
													{
														$diff=date_diff(date_create(date("Y-m-d",strtotime($pi_file_first_date))),date_create(date("Y-m-d",strtotime($pi_approval_date))));
														echo $diff->days." days";
													}
												?>
											</td>
											<td class="center">
												<?
													if($pi_approval_date!='' && $btb_file_first_date!='')
													{
														$diff=date_diff(date_create(date("Y-m-d",strtotime($pi_approval_date))),date_create(date("Y-m-d",strtotime($btb_file_first_date))));
														echo $diff->days." days";
													}
												?>
											</td>
											<td class="center">
												<?
													if($rcv_first_date)
													{
														$diff=date_diff(date_create(date("Y-m-d",strtotime($row["BOOKING_DATE"]))),date_create(date("Y-m-d",strtotime($rcv_first_date))));
														echo $diff->days." days";
													}
												?>
											</td>
											<td class="center">
												<?
													if($rcv_last_date)
													{
														$diff=date_diff(date_create(date("Y-m-d",strtotime($row["BOOKING_DATE"]))),date_create(date("Y-m-d",strtotime($rcv_last_date))));
														echo $diff->days." days";
													}
												?>
											</td>
										</tr>
									<?
									$i++;
									}
								}
							}
						}
					?>
                </tbody>	
			</table>
		</fieldset>
	</div>
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
	echo "$total_data####$filename";
	exit();
}

if($action=="po_popup")
{
	echo load_html_head_contents("PO Information", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);

	$lib_country=return_library_array( "SELECT id, country_name from lib_country",'id','country_name');

	if($db_type==0){ $country_clm=" group_concat(distinct(b.country_id)) as COUNTRY_ID";}
	else{ $country_clm=" listagg(cast(b.country_id as varchar(4000)),',') within group(order by b.id) as COUNTRY_ID";}

	$sql="SELECT a.id as PO_ID, a.PO_NUMBER, a.PO_QUANTITY, a.PO_RECEIVED_DATE, a.PUB_SHIPMENT_DATE, $country_clm
	from wo_po_break_down a, wo_po_color_size_breakdown b
	where a.id=b.po_break_down_id and a.status_active=1 and b.status_active=1 and a.id in($po_id)
	group by a.id, a.po_number, a.po_quantity, a.po_received_date, a.pub_shipment_date
	order by a.id";
	// echo $sql;
	$data=sql_select($sql);
	?>
	<style>
		.wrd_brk{word-break: break-all;}
		.center{text-align: center;}
		.right{text-align: right;}
	</style>
	<fieldset style="width:550px; margin-left:10px" >
		<table width="540" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
				<tr>
					<th width="100">Buyer PO No</th>
					<th width="100">Buyer PO Quantity</th>
					<th width="120">Country</th>
					<th width="120">Buyer PO Receive date</th>
					<th>Shipment date</th>
				</tr>
			</thead>
			<tbody>
				<?
					$i=1;
					foreach($data as $row)
					{
						if( $i % 2 == 0 ){ $bgcolor="#E9F3FF"; }else{ $bgcolor = "#FFFFFF"; }
						$country_id_arr=array_unique(explode(",",$row["COUNTRY_ID"]));
						$country_name="";
						foreach($country_id_arr as $val)
						{
							$country_name.=$lib_country[$val].", ";
						}
						?>
							<tr bgcolor="<?=$bgcolor;?>">
								<td class="wrd_brk"><?=$row["PO_NUMBER"];?></td>
								<td class="right"><?=number_format($row["PO_QUANTITY"],2);?></td>
								<td class="wrd_brk"><?=rtrim($country_name,", ");?></td>
								<td class="center"><?=change_date_format($row["PO_RECEIVED_DATE"]);?></td>
								<td class="center"><?=change_date_format($row["PUB_SHIPMENT_DATE"]);?></td>
							</tr>
						<?
						$i++;
					}
				?>
			</tbody>
		</table>
	</fieldset>
	<?
	exit();	
}

if($action=="rcv_popup")
{
	echo load_html_head_contents("Receive Information", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);

	$sql="SELECT a.ID, a.BOOKING_NO, a.RECV_NUMBER, a.RECEIVE_DATE, a.CHALLAN_NO, sum(b.order_qnty) as RECEIVE_QNTY 
	from inv_receive_master a, inv_transaction b
	where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.id in($rcv_id) and b.prod_id in($prod_id)
	group by a.id, a.booking_no, a.recv_number, a.receive_date, a.challan_no
	order by a.id";
	// echo $sql;
	$data=sql_select($sql);
	?>
	<style>
		.wrd_brk{word-break: break-all;}
		.center{text-align: center;}
		.right{text-align: right;}
	</style>
	<fieldset style="width:550px; margin-left:10px" >
		<table width="540" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
				<tr>
					<th width="120">WO/PI Number</th>
					<th width="120">MRR No</th>
					<th width="80">MRR Date</th>
					<th width="120">Challan No.</th>
					<th>MRR Quantity</th>
				</tr>
			</thead>
			<tbody>
				<?
					$i=1;
					foreach($data as $row)
					{
						if( $i % 2 == 0 ){ $bgcolor="#E9F3FF"; }else{ $bgcolor = "#FFFFFF"; }
						$country_id_arr=array_unique(explode(",",$row["COUNTRY_ID"]));
						$country_name="";
						foreach($country_id_arr as $val)
						{
							$country_name.=$lib_country[$val].", ";
						}
						?>
							<tr bgcolor="<?=$bgcolor;?>">
								<td class="wrd_brk"><?=$row["BOOKING_NO"];?></td>
								<td class="wrd_brk"><?=$row["RECV_NUMBER"];?></td>
								<td class="center"><?=change_date_format($row["RECEIVE_DATE"]);?></td>
								<td class="wrd_brk"><?=$row["CHALLAN_NO"];?></td>
								<td class="right"><?=number_format($row["RECEIVE_QNTY"],2);?></td>
							</tr>
						<?
						$i++;
					}
				?>
			</tbody>
		</table>
	</fieldset>
	<?
	exit();	
}


disconnect($con);