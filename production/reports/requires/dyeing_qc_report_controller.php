<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];

//--------------------------------------------------------------------------------------------------------------------

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 140, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "--Select Location--", $selected, "",0 );
	exit();     	 
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 110, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,2,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", 0, "" );
  exit();	 
}

if($action=="report_generate") 
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	$cbo_company=str_replace("'","",$cbo_company_id);
	$working_company_id=str_replace("'","",$cbo_working_company_id);
	$cbo_location=str_replace("'","",$cbo_location_id);
	$buyer_name=str_replace("'","",$cbo_buyer_name);
	$job_no=str_replace("'","",$txt_job_no);
	$booking_no=str_replace("'","",$txt_booking_no);
	$order_no=str_replace("'","",$txt_order_no);
	$batch_no=str_replace("'","",$txt_batch_no);
	$order_type=str_replace("'","",$cbo_order_type);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);

	if ($cbo_location==0 || $cbo_location=='') $location_cond =""; else $location_cond =" and a.location_id=$cbo_location ";
	if ($cbo_company==0) $lc_comp_cond =""; else $lc_comp_cond =" and a.company_id=$cbo_company ";
	if ($working_company_id==0) $working_comp_cond =""; else $working_comp_cond =" and a.knitting_company=$working_company_id ";
	if ($working_company_id!=0)
	{
		$comp_cond=$working_company_id;
	}
	else
	{
		$comp_cond=$cbo_company;
	}
	
	if ($buyer_name==0 || $buyer_name=='') $buyer_cond=""; else $buyer_cond=" and b.buyer_id=$cbo_buyer_name";
	if ($job_no=="") $job_cond=""; else $job_cond =" and e.job_no like '%$job_no%'";
	if ($booking_no=="") $booking_cond=""; else $booking_cond =" and c.booking_no like '%$booking_no%'";
	if ($order_no=="") $order_no_cond=""; else $order_no_cond =" and g.po_number like '%$order_no%'";
	if ($batch_no=="") $batch_cond=""; else $batch_cond =" and c.batch_no='$batch_no'";

	if ($order_no=="") $subcon_order_no_cond=""; else $subcon_order_no_cond =" and e.order_no like '%$order_no%'";
	if ($job_no=="") $subcon_job_cond=""; else $subcon_job_cond =" and e.job_no_mst like '%$job_no%'";
	if ($buyer_name==0 || $buyer_name=='') $party_cond=""; else $party_cond=" and a.party_id=$cbo_buyer_name";



	if($db_type==0)
	{
		if( $date_from!="" && $date_to!="" )
		{
			$date_cond .= " and a.receive_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
		}
	}
	else if($db_type==2)
	{
		if($date_from!="" && $date_to!="") 
		{
			$date_cond .= " and a.receive_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
		}
	}
	if($db_type==0)
	{
		if( $date_from!="" && $date_to!="" )
		{
			$subcon_date_cond .= " and a.product_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
		}
	}
	else if($db_type==2)
	{
		if($date_from!="" && $date_to!="") 
		{
			$subcon_date_cond .= " and a.product_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
		}
	}

	$company_library=return_library_array("select id,company_name from lib_company", "id", "company_name");
	$color_library=return_library_array("select id,color_name from lib_color", "id", "color_name");
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	$company_short_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name"  );
	$party_library=return_library_array( "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$comp_cond' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name", "id", "buyer_name");

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
	ob_start();	

	
    // For Order NO (Only for With Order and Direct Order) => pro_finish_fabric_rcv_dtls.order_id = wo_po_break_down.id
    //echo $order_type;die;
    if ($order_type==1) // All
    {
    	// SQL and array Start
		 $main_sql="SELECT a.id, a.recv_number, a.company_id, a.receive_date, a.knitting_company, b.prod_id, b.order_id, b.batch_id, b.buyer_id, b.fabric_description_id, b.width, b.dia_width_type, b.gsm, b.color_id, b.body_part_id, b.grey_used_qty, b.qc_qnty, b.receive_qnty as qc_pass_qty, b.no_of_roll, b.remarks, c.batch_no, c.booking_no, c.extention_no, sum(d.batch_qnty) as batch_qnty, e.booking_type, e.is_short, c.id as batch_mst_id
		from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c, pro_batch_create_dtls d, wo_booking_mst e, order_wise_pro_details f, wo_po_break_down g
		where a.id=b.mst_id and b.batch_id=c.id and c.id=d.mst_id and c.booking_no_id=e.id and b.id=f.dtls_id and f.po_breakdown_id=g.id and c.booking_without_order=0 $lc_comp_cond $working_comp_cond $location_cond $buyer_cond $job_cond $booking_cond $order_no_cond $batch_cond $date_cond and a.entry_form=7 AND f.entry_form = 7 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0
		group by a.id, a.recv_number, a.company_id, a.receive_date, a.knitting_company, b.prod_id, b.order_id, b.batch_id, b.buyer_id, b.fabric_description_id, b.width, b.dia_width_type, b.gsm, b.color_id, b.body_part_id, b.grey_used_qty, b.qc_qnty, b.receive_qnty, b.no_of_roll, b.remarks, c.batch_no, c.booking_no, c.extention_no, e.booking_type, e.is_short, c.id

		UNION ALL

		SELECT a.id, a.recv_number, a.company_id, a.receive_date, a.knitting_company, b.prod_id, b.order_id, b.batch_id, b.buyer_id, b.fabric_description_id, b.width, b.dia_width_type, b.gsm, b.color_id, b.body_part_id, b.grey_used_qty, b.qc_qnty, b.receive_qnty as qc_pass_qty, b.no_of_roll, b.remarks, c.batch_no, c.booking_no, c.extention_no, sum(d.batch_qnty) as batch_qnty, e.booking_type, e.is_short, c.id as batch_mst_id
		from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c, pro_batch_create_dtls d, wo_non_ord_samp_booking_mst e
		where a.id=b.mst_id and b.batch_id=c.id and c.id=d.mst_id and c.booking_no_id=e.id   and c.booking_without_order=1 $lc_comp_cond $working_comp_cond $location_cond $buyer_cond $job_cond $booking_cond $batch_cond $date_cond and a.entry_form=7 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0
		group by a.id, a.recv_number, a.company_id, a.receive_date, a.knitting_company, b.prod_id, b.order_id, b.batch_id, b.buyer_id, b.fabric_description_id, b.width, b.dia_width_type, b.gsm, b.color_id, b.body_part_id, b.grey_used_qty, b.qc_qnty, b.receive_qnty, b.no_of_roll, b.remarks, c.batch_no, c.booking_no, c.extention_no, e.booking_type, e.is_short, c.id";//die;

    	$subcon_sql="SELECT a.id, a.company_id, a.party_id, a.product_date, a.remarks, b.batch_id, b.fabric_description, b.cons_comp_id as composition_id, b.order_id, b.gsm, b.dia_width, d.width_dia_type, b.color_id, b.no_of_roll, b.product_qnty,
		c.batch_no, c.extention_no, d.batch_qnty, d.prod_id, e.job_no_mst, e.order_no
		from subcon_production_mst a, subcon_production_dtls b, pro_batch_create_mst c, pro_batch_create_dtls d, subcon_ord_dtls e, subcon_ord_mst f
		where a.id=b.mst_id and b.batch_id=c.id and c.id=d.mst_id and d.po_id=e.id and e.job_no_mst=f.subcon_job and a.company_id=$comp_cond and f.entry_form=238 $location_cond $batch_cond $subcon_date_cond $subcon_order_no_cond $subcon_job_cond $party_cond 
		group by a.id, a.company_id, a.party_id, a.product_date, a.remarks, b.batch_id, b.fabric_description, b.cons_comp_id, b.gsm, b.dia_width, d.width_dia_type, b.order_id, b.color_id, b.no_of_roll, b.product_qnty,
		c.batch_no, c.extention_no, d.batch_qnty, d.prod_id, e.job_no_mst, e.order_no";
		
    }
    elseif ($order_type==2) // Sample With Order
    {
    	// SQL and array Start
		$main_sql="SELECT a.id, a.recv_number, a.company_id, a.receive_date, a.knitting_company, b.prod_id, b.order_id, b.batch_id, b.buyer_id, b.fabric_description_id, b.width, b.dia_width_type, b.gsm, b.color_id, b.body_part_id, b.grey_used_qty, b.qc_qnty, b.receive_qnty as qc_pass_qty, b.no_of_roll, b.remarks, c.batch_no, c.booking_no, c.extention_no, sum(d.batch_qnty) as batch_qnty, e.booking_type, e.is_short, c.id as batch_mst_id
		from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c, pro_batch_create_dtls d, wo_booking_mst e, order_wise_pro_details f, wo_po_break_down g
		where a.id=b.mst_id and b.batch_id=c.id and c.id=d.mst_id and c.booking_no_id=e.id and b.id=f.dtls_id and f.po_breakdown_id=g.id  and c.booking_without_order=0 $lc_comp_cond $working_comp_cond $location_cond $buyer_cond $job_cond $booking_cond $order_no_cond $batch_cond $date_cond and a.entry_form=7 AND f.entry_form = 7 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.booking_type=4
		group by a.id, a.recv_number, a.company_id, a.receive_date, a.knitting_company, b.prod_id, b.order_id, b.batch_id, b.buyer_id, b.fabric_description_id, b.width, b.dia_width_type, b.gsm, b.color_id, b.body_part_id, b.grey_used_qty, b.qc_qnty, b.receive_qnty, b.no_of_roll, b.remarks, c.batch_no, c.booking_no, c.extention_no, e.booking_type, e.is_short, c.id";//die;
    }
    elseif ($order_type==4) // Direct Order
    {
    	// SQL and array Start
		$main_sql="SELECT a.id, a.recv_number, a.company_id, a.receive_date, a.knitting_company, b.prod_id, b.order_id, b.batch_id, b.buyer_id, b.fabric_description_id, b.prod_id, b.width, b.dia_width_type, b.gsm, b.color_id, b.body_part_id, b.grey_used_qty, b.qc_qnty, b.receive_qnty as qc_pass_qty, b.no_of_roll, b.remarks, c.batch_no, c.booking_no, c.extention_no, sum(d.batch_qnty) as batch_qnty, e.booking_type, e.is_short, c.id as batch_mst_id
		from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c, pro_batch_create_dtls d, wo_booking_mst e, order_wise_pro_details f, wo_po_break_down g
		where a.id=b.mst_id and b.batch_id=c.id and c.id=d.mst_id and c.booking_no_id=e.id and b.id=f.dtls_id and f.po_breakdown_id=g.id  and c.booking_without_order=0 $lc_comp_cond $working_comp_cond $location_cond $buyer_cond $job_cond $booking_cond $order_no_cond $batch_cond $date_cond and a.entry_form=7 AND f.entry_form = 7 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.booking_type=1
		group by a.id, a.recv_number, a.company_id, a.receive_date, a.knitting_company, b.prod_id, b.order_id, b.batch_id, b.buyer_id, b.fabric_description_id, b.prod_id, b.width, b.dia_width_type, b.gsm, b.color_id, b.body_part_id, b.grey_used_qty, b.qc_qnty, b.receive_qnty, b.no_of_roll, b.remarks, c.batch_no, c.booking_no, c.extention_no, e.booking_type, e.is_short, c.id";
		//echo $main_sql;//die;
    }
    elseif ($order_type==3) // Sample Without Order
    {
    	// SQL and array Start
		/*$main_sql="SELECT a.id, a.recv_number, a.company_id, a.receive_date, a.knitting_company, b.batch_id, b.buyer_id, b.fabric_description_id, b.width, b.dia_width_type, b.gsm, b.color_id, b.body_part_id, b.grey_used_qty, b.qc_qnty, b.receive_qnty as qc_pass_qty, b.no_of_roll, b.remarks, c.batch_no, c.booking_no, c.extention_no, sum(d.batch_qnty) as batch_qnty, e.booking_type, e.is_short
		from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c, pro_batch_create_dtls d, wo_non_ord_samp_booking_mst e
		where a.id=b.mst_id and b.batch_id=c.id and c.id=d.mst_id and c.booking_no_id=e.id  and a.company_id=$cbo_company and c.booking_without_order=1 $location_cond $buyer_cond $job_cond $booking_cond $batch_cond $date_cond and a.entry_form=7 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0
		group by a.id, a.recv_number, a.company_id, a.receive_date, a.knitting_company, b.batch_id, b.buyer_id, b.fabric_description_id, b.width, b.dia_width_type, b.gsm, b.color_id, b.body_part_id, b.grey_used_qty, b.qc_qnty, b.receive_qnty, b.no_of_roll, b.remarks, c.batch_no, c.booking_no, c.extention_no, e.booking_type, e.is_short";*///die;

		$main_sql="SELECT a.id, a.recv_number, a.company_id, a.receive_date, a.knitting_company, b.prod_id, b.order_id, b.batch_id, b.buyer_id, b.fabric_description_id, b.width, b.dia_width_type, b.gsm, b.color_id, b.body_part_id, b.grey_used_qty, b.qc_qnty, b.receive_qnty as qc_pass_qty, b.no_of_roll, b.remarks, c.batch_no, c.booking_no, c.extention_no, sum(d.batch_qnty) as batch_qnty, e.booking_type, e.is_short, c.id as batch_mst_id
		from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c, pro_batch_create_dtls d, wo_non_ord_samp_booking_mst e
		where a.id=b.mst_id and b.batch_id=c.id and c.id=d.mst_id and c.booking_no_id=e.id  and c.booking_without_order=1 $lc_comp_cond $working_comp_cond $location_cond $buyer_cond $job_cond $booking_cond $batch_cond $date_cond and a.entry_form=7 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0
		group by a.id, a.recv_number, a.company_id, a.receive_date, a.knitting_company, b.prod_id, b.order_id, b.batch_id, b.buyer_id, b.fabric_description_id, b.width, b.dia_width_type, b.gsm, b.color_id, b.body_part_id, b.grey_used_qty, b.qc_qnty, b.receive_qnty, b.no_of_roll, b.remarks, c.batch_no, c.booking_no, c.extention_no, e.booking_type, e.is_short, c.id";
    }
    else // Subcontract Order
    {
    	// SQL and array Start
		$subcon_sql="SELECT a.id, a.company_id, a.party_id, a.product_date, a.remarks, b.batch_id, b.fabric_description, b.cons_comp_id as composition_id, b.order_id, b.gsm, b.dia_width, d.width_dia_type, b.color_id, b.no_of_roll, b.product_qnty,
		c.batch_no, c.extention_no, d.batch_qnty, d.prod_id, e.job_no_mst, e.order_no
		from subcon_production_mst a, subcon_production_dtls b, pro_batch_create_mst c, pro_batch_create_dtls d, subcon_ord_dtls e, subcon_ord_mst f
		where a.id=b.mst_id and b.batch_id=c.id and c.id=d.mst_id and d.po_id=e.id and e.job_no_mst=f.subcon_job and a.company_id=$comp_cond and f.entry_form=238 $location_cond $batch_cond $subcon_date_cond $subcon_order_no_cond $subcon_job_cond $party_cond 
		group by a.id, a.company_id, a.party_id, a.product_date, a.remarks, b.batch_id, b.fabric_description, b.cons_comp_id, b.gsm, b.dia_width, d.width_dia_type, b.order_id, b.color_id, b.no_of_roll, b.product_qnty,
		c.batch_no, c.extention_no, d.batch_qnty, d.prod_id, e.job_no_mst, e.order_no";
    }
	

    $main_sql_result=sql_select($main_sql);
	$main_data_arr=array();
	$summary_arr=array();
	$order_arr=array();
	foreach ($main_sql_result as $row)
	{
		$main_data_arr[$row[csf('booking_type')]][$row[csf('buyer_id')]][$row[csf('booking_no')]][$row[csf('batch_no')]][$row[csf('fabric_description_id')]][$row[csf('width')]][$row[csf('dia_width_type')]][$row[csf('gsm')]][$row[csf('color_id')]][$row[csf('body_part_id')]]['recv_number']=$row[csf('recv_number')];		
		$main_data_arr[$row[csf('booking_type')]][$row[csf('buyer_id')]][$row[csf('booking_no')]][$row[csf('batch_no')]][$row[csf('fabric_description_id')]][$row[csf('width')]][$row[csf('dia_width_type')]][$row[csf('gsm')]][$row[csf('color_id')]][$row[csf('body_part_id')]]['prod_id']=$row[csf('prod_id')];
		$main_data_arr[$row[csf('booking_type')]][$row[csf('buyer_id')]][$row[csf('booking_no')]][$row[csf('batch_no')]][$row[csf('fabric_description_id')]][$row[csf('width')]][$row[csf('dia_width_type')]][$row[csf('gsm')]][$row[csf('color_id')]][$row[csf('body_part_id')]]['order_id']=$row[csf('order_id')];
		$main_data_arr[$row[csf('booking_type')]][$row[csf('buyer_id')]][$row[csf('booking_no')]][$row[csf('batch_no')]][$row[csf('fabric_description_id')]][$row[csf('width')]][$row[csf('dia_width_type')]][$row[csf('gsm')]][$row[csf('color_id')]][$row[csf('body_part_id')]]['company_id']=$row[csf('company_id')];
		$main_data_arr[$row[csf('booking_type')]][$row[csf('buyer_id')]][$row[csf('booking_no')]][$row[csf('batch_no')]][$row[csf('fabric_description_id')]][$row[csf('width')]][$row[csf('dia_width_type')]][$row[csf('gsm')]][$row[csf('color_id')]][$row[csf('body_part_id')]]['receive_date']=$row[csf('receive_date')];
		$main_data_arr[$row[csf('booking_type')]][$row[csf('buyer_id')]][$row[csf('booking_no')]][$row[csf('batch_no')]][$row[csf('fabric_description_id')]][$row[csf('width')]][$row[csf('dia_width_type')]][$row[csf('gsm')]][$row[csf('color_id')]][$row[csf('body_part_id')]]['knitting_company']=$row[csf('knitting_company')];
		$main_data_arr[$row[csf('booking_type')]][$row[csf('buyer_id')]][$row[csf('booking_no')]][$row[csf('batch_no')]][$row[csf('fabric_description_id')]][$row[csf('width')]][$row[csf('dia_width_type')]][$row[csf('gsm')]][$row[csf('color_id')]][$row[csf('body_part_id')]]['buyer_id']=$row[csf('buyer_id')];
		$main_data_arr[$row[csf('booking_type')]][$row[csf('buyer_id')]][$row[csf('booking_no')]][$row[csf('batch_no')]][$row[csf('fabric_description_id')]][$row[csf('width')]][$row[csf('dia_width_type')]][$row[csf('gsm')]][$row[csf('color_id')]][$row[csf('body_part_id')]]['fabric_description_id']=$row[csf('fabric_description_id')];
		$main_data_arr[$row[csf('booking_type')]][$row[csf('buyer_id')]][$row[csf('booking_no')]][$row[csf('batch_no')]][$row[csf('fabric_description_id')]][$row[csf('width')]][$row[csf('dia_width_type')]][$row[csf('gsm')]][$row[csf('color_id')]][$row[csf('body_part_id')]]['width']=$row[csf('width')];
		$main_data_arr[$row[csf('booking_type')]][$row[csf('buyer_id')]][$row[csf('booking_no')]][$row[csf('batch_no')]][$row[csf('fabric_description_id')]][$row[csf('width')]][$row[csf('dia_width_type')]][$row[csf('gsm')]][$row[csf('color_id')]][$row[csf('body_part_id')]]['dia_width_type']=$row[csf('dia_width_type')];
		$main_data_arr[$row[csf('booking_type')]][$row[csf('buyer_id')]][$row[csf('booking_no')]][$row[csf('batch_no')]][$row[csf('fabric_description_id')]][$row[csf('width')]][$row[csf('dia_width_type')]][$row[csf('gsm')]][$row[csf('color_id')]][$row[csf('body_part_id')]]['gsm']=$row[csf('gsm')];
		$main_data_arr[$row[csf('booking_type')]][$row[csf('buyer_id')]][$row[csf('booking_no')]][$row[csf('batch_no')]][$row[csf('fabric_description_id')]][$row[csf('width')]][$row[csf('dia_width_type')]][$row[csf('gsm')]][$row[csf('color_id')]][$row[csf('body_part_id')]]['color_id']=$row[csf('color_id')];
		$main_data_arr[$row[csf('booking_type')]][$row[csf('buyer_id')]][$row[csf('booking_no')]][$row[csf('batch_no')]][$row[csf('fabric_description_id')]][$row[csf('width')]][$row[csf('dia_width_type')]][$row[csf('gsm')]][$row[csf('color_id')]][$row[csf('body_part_id')]]['body_part_id']=$row[csf('body_part_id')];
		$main_data_arr[$row[csf('booking_type')]][$row[csf('buyer_id')]][$row[csf('booking_no')]][$row[csf('batch_no')]][$row[csf('fabric_description_id')]][$row[csf('width')]][$row[csf('dia_width_type')]][$row[csf('gsm')]][$row[csf('color_id')]][$row[csf('body_part_id')]]['grey_used_qty']+=$row[csf('grey_used_qty')];
		$main_data_arr[$row[csf('booking_type')]][$row[csf('buyer_id')]][$row[csf('booking_no')]][$row[csf('batch_no')]][$row[csf('fabric_description_id')]][$row[csf('width')]][$row[csf('dia_width_type')]][$row[csf('gsm')]][$row[csf('color_id')]][$row[csf('body_part_id')]]['qc_qnty']+=$row[csf('qc_qnty')];
		$main_data_arr[$row[csf('booking_type')]][$row[csf('buyer_id')]][$row[csf('booking_no')]][$row[csf('batch_no')]][$row[csf('fabric_description_id')]][$row[csf('width')]][$row[csf('dia_width_type')]][$row[csf('gsm')]][$row[csf('color_id')]][$row[csf('body_part_id')]]['qc_pass_qty']+=$row[csf('qc_pass_qty')];
		$main_data_arr[$row[csf('booking_type')]][$row[csf('buyer_id')]][$row[csf('booking_no')]][$row[csf('batch_no')]][$row[csf('fabric_description_id')]][$row[csf('width')]][$row[csf('dia_width_type')]][$row[csf('gsm')]][$row[csf('color_id')]][$row[csf('body_part_id')]]['no_of_roll']=$row[csf('no_of_roll')];
		$main_data_arr[$row[csf('booking_type')]][$row[csf('buyer_id')]][$row[csf('booking_no')]][$row[csf('batch_no')]][$row[csf('fabric_description_id')]][$row[csf('width')]][$row[csf('dia_width_type')]][$row[csf('gsm')]][$row[csf('color_id')]][$row[csf('body_part_id')]]['remarks']=$row[csf('remarks')];
		$main_data_arr[$row[csf('booking_type')]][$row[csf('buyer_id')]][$row[csf('booking_no')]][$row[csf('batch_no')]][$row[csf('fabric_description_id')]][$row[csf('width')]][$row[csf('dia_width_type')]][$row[csf('gsm')]][$row[csf('color_id')]][$row[csf('body_part_id')]]['batch_no']=$row[csf('batch_no')];
		$main_data_arr[$row[csf('booking_type')]][$row[csf('buyer_id')]][$row[csf('booking_no')]][$row[csf('batch_no')]][$row[csf('fabric_description_id')]][$row[csf('width')]][$row[csf('dia_width_type')]][$row[csf('gsm')]][$row[csf('color_id')]][$row[csf('body_part_id')]]['booking_no']=$row[csf('booking_no')];
		$main_data_arr[$row[csf('booking_type')]][$row[csf('buyer_id')]][$row[csf('booking_no')]][$row[csf('batch_no')]][$row[csf('fabric_description_id')]][$row[csf('width')]][$row[csf('dia_width_type')]][$row[csf('gsm')]][$row[csf('color_id')]][$row[csf('body_part_id')]]['extention_no']=$row[csf('extention_no')];
		$main_data_arr[$row[csf('booking_type')]][$row[csf('buyer_id')]][$row[csf('booking_no')]][$row[csf('batch_no')]][$row[csf('fabric_description_id')]][$row[csf('width')]][$row[csf('dia_width_type')]][$row[csf('gsm')]][$row[csf('color_id')]][$row[csf('body_part_id')]]['batch_qnty']+=$row[csf('batch_qnty')];			
		$main_data_arr[$row[csf('booking_type')]][$row[csf('buyer_id')]][$row[csf('booking_no')]][$row[csf('batch_no')]][$row[csf('fabric_description_id')]][$row[csf('width')]][$row[csf('dia_width_type')]][$row[csf('gsm')]][$row[csf('color_id')]][$row[csf('body_part_id')]]['booking_type']=$row[csf('booking_type')];
		$main_data_arr[$row[csf('booking_type')]][$row[csf('buyer_id')]][$row[csf('booking_no')]][$row[csf('batch_no')]][$row[csf('fabric_description_id')]][$row[csf('width')]][$row[csf('dia_width_type')]][$row[csf('gsm')]][$row[csf('color_id')]][$row[csf('body_part_id')]]['is_short']=$row[csf('is_short')];
		$main_data_arr[$row[csf('booking_type')]][$row[csf('buyer_id')]][$row[csf('booking_no')]][$row[csf('batch_no')]][$row[csf('fabric_description_id')]][$row[csf('width')]][$row[csf('dia_width_type')]][$row[csf('gsm')]][$row[csf('color_id')]][$row[csf('body_part_id')]]['batch_mst_id']=$row[csf('batch_mst_id')];

		$summary_arr[$row[csf('buyer_id')]]['grey_used_qty']+=$row[csf('grey_used_qty')];
		$summary_arr[$row[csf('buyer_id')]]['batch_qnty']+=$row[csf('batch_qnty')];
		$summary_arr[$row[csf('buyer_id')]]['qc_qnty']+=$row[csf('qc_qnty')];

		if (isset($row[csf('order_id')])) 
		{
			$order_arr[$row[csf('order_id')]]=$row[csf('order_id')];
		}		
	}
	$order_id = implode(',', $order_arr); 
	/*echo "<pre>";
	print_r($order_arr);//die;*/

	$subcon_result=sql_select($subcon_sql);
	$subcon_data_arr=array();
	$subc_order_arr=array();
	foreach ($subcon_result as $row)
	{
		$subcon_data_arr[$row[csf('order_no')]][$row[csf('party_id')]][$row[csf('batch_no')]][$row[csf('fabric_description')]][$row[csf('dia_width')]][$row[csf('width_dia_type')]][$row[csf('gsm')]][$row[csf('color_id')]]['id']=$row[csf('id')];
		$subcon_data_arr[$row[csf('order_no')]][$row[csf('party_id')]][$row[csf('batch_no')]][$row[csf('fabric_description')]][$row[csf('dia_width')]][$row[csf('width_dia_type')]][$row[csf('gsm')]][$row[csf('color_id')]]['company_id']=$row[csf('company_id')];
		$subcon_data_arr[$row[csf('order_no')]][$row[csf('party_id')]][$row[csf('batch_no')]][$row[csf('fabric_description')]][$row[csf('dia_width')]][$row[csf('width_dia_type')]][$row[csf('gsm')]][$row[csf('color_id')]]['party_id']=$row[csf('party_id')];
		$subcon_data_arr[$row[csf('order_no')]][$row[csf('party_id')]][$row[csf('batch_no')]][$row[csf('fabric_description')]][$row[csf('dia_width')]][$row[csf('width_dia_type')]][$row[csf('gsm')]][$row[csf('color_id')]]['product_date']=$row[csf('product_date')];
		$subcon_data_arr[$row[csf('order_no')]][$row[csf('party_id')]][$row[csf('batch_no')]][$row[csf('fabric_description')]][$row[csf('dia_width')]][$row[csf('width_dia_type')]][$row[csf('gsm')]][$row[csf('color_id')]]['batch_id']=$row[csf('batch_id')];
		$subcon_data_arr[$row[csf('order_no')]][$row[csf('party_id')]][$row[csf('batch_no')]][$row[csf('fabric_description')]][$row[csf('dia_width')]][$row[csf('width_dia_type')]][$row[csf('gsm')]][$row[csf('color_id')]]['fabric_description']=$row[csf('fabric_description')];
		$subcon_data_arr[$row[csf('order_no')]][$row[csf('party_id')]][$row[csf('batch_no')]][$row[csf('fabric_description')]][$row[csf('dia_width')]][$row[csf('width_dia_type')]][$row[csf('gsm')]][$row[csf('color_id')]]['gsm']=$row[csf('gsm')];
		$subcon_data_arr[$row[csf('order_no')]][$row[csf('party_id')]][$row[csf('batch_no')]][$row[csf('fabric_description')]][$row[csf('dia_width')]][$row[csf('width_dia_type')]][$row[csf('gsm')]][$row[csf('color_id')]]['color_id']=$row[csf('color_id')];
		$subcon_data_arr[$row[csf('order_no')]][$row[csf('party_id')]][$row[csf('batch_no')]][$row[csf('fabric_description')]][$row[csf('dia_width')]][$row[csf('width_dia_type')]][$row[csf('gsm')]][$row[csf('color_id')]]['no_of_roll']=$row[csf('no_of_roll')];
		$subcon_data_arr[$row[csf('order_no')]][$row[csf('party_id')]][$row[csf('batch_no')]][$row[csf('fabric_description')]][$row[csf('dia_width')]][$row[csf('width_dia_type')]][$row[csf('gsm')]][$row[csf('color_id')]]['product_qnty']=$row[csf('product_qnty')];
		$subcon_data_arr[$row[csf('order_no')]][$row[csf('party_id')]][$row[csf('batch_no')]][$row[csf('fabric_description')]][$row[csf('dia_width')]][$row[csf('width_dia_type')]][$row[csf('gsm')]][$row[csf('color_id')]]['batch_no']=$row[csf('batch_no')];
		$subcon_data_arr[$row[csf('order_no')]][$row[csf('party_id')]][$row[csf('batch_no')]][$row[csf('fabric_description')]][$row[csf('dia_width')]][$row[csf('width_dia_type')]][$row[csf('gsm')]][$row[csf('color_id')]]['extention_no']=$row[csf('extention_no')];
		$subcon_data_arr[$row[csf('order_no')]][$row[csf('party_id')]][$row[csf('batch_no')]][$row[csf('fabric_description')]][$row[csf('dia_width')]][$row[csf('width_dia_type')]][$row[csf('gsm')]][$row[csf('color_id')]]['batch_qnty']=$row[csf('batch_qnty')];
		$subcon_data_arr[$row[csf('order_no')]][$row[csf('party_id')]][$row[csf('batch_no')]][$row[csf('fabric_description')]][$row[csf('dia_width')]][$row[csf('width_dia_type')]][$row[csf('gsm')]][$row[csf('color_id')]]['prod_id']=$row[csf('prod_id')];
		$subcon_data_arr[$row[csf('order_no')]][$row[csf('party_id')]][$row[csf('batch_no')]][$row[csf('fabric_description')]][$row[csf('dia_width')]][$row[csf('width_dia_type')]][$row[csf('gsm')]][$row[csf('color_id')]]['order_no']=$row[csf('order_no')];
		$subcon_data_arr[$row[csf('order_no')]][$row[csf('party_id')]][$row[csf('batch_no')]][$row[csf('fabric_description')]][$row[csf('dia_width')]][$row[csf('width_dia_type')]][$row[csf('gsm')]][$row[csf('color_id')]]['dia_width']=$row[csf('dia_width')];
		$subcon_data_arr[$row[csf('order_no')]][$row[csf('party_id')]][$row[csf('batch_no')]][$row[csf('fabric_description')]][$row[csf('dia_width')]][$row[csf('width_dia_type')]][$row[csf('gsm')]][$row[csf('color_id')]]['width_dia_type']=$row[csf('width_dia_type')];
		$subcon_data_arr[$row[csf('order_no')]][$row[csf('party_id')]][$row[csf('batch_no')]][$row[csf('fabric_description')]][$row[csf('dia_width')]][$row[csf('width_dia_type')]][$row[csf('gsm')]][$row[csf('color_id')]]['remarks']=$row[csf('remarks')];
		$subcon_data_arr[$row[csf('order_no')]][$row[csf('party_id')]][$row[csf('batch_no')]][$row[csf('fabric_description')]][$row[csf('dia_width')]][$row[csf('width_dia_type')]][$row[csf('gsm')]][$row[csf('color_id')]]['composition_id']=$row[csf('composition_id')];
		$subcon_data_arr[$row[csf('order_no')]][$row[csf('party_id')]][$row[csf('batch_no')]][$row[csf('fabric_description')]][$row[csf('dia_width')]][$row[csf('width_dia_type')]][$row[csf('gsm')]][$row[csf('color_id')]]['order_id']=$row[csf('order_id')];

		if (isset($row[csf('order_id')])) 
		{
			$subc_order_arr[$row[csf('order_id')]]=$row[csf('order_id')];
		}
	}

	$subc_order_id = implode(',', $subc_order_arr);
	/*echo "<pre>";
	print_r($subcon_data_arr);die;*/

	$delevery_sql="SELECT b.product_id, b.order_id, b.gsm, b.dia, b.determination_id as fabric_description_id, b.color_id, b.bodypart_id, b.width_type, b.current_delivery, b.batch_id 
	from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b
	where a.id=b.mst_id and a.company_id=$comp_cond and b.entry_form=54 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	group by b.product_id, b.gsm, b.dia, b.determination_id, b.color_id, b.bodypart_id, b.width_type, b.current_delivery, b.order_id, b.batch_id"; // and b.batch_id in (6228,6225) //  and b.order_id in($order_id)
	// echo $delevery_sql;//die;
	$dlvr_sql_result=sql_select($delevery_sql);
	$dlvr_data_arr=array();
	foreach ($dlvr_sql_result as $row)
	{
		$dlvr_data_arr[$row[csf('batch_id')]][$row[csf('product_id')]][$row[csf('order_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('fabric_description_id')]][$row[csf('color_id')]][$row[csf('width_type')]][$row[csf('bodypart_id')]]['current_delivery']+=$row[csf('current_delivery')];
	}
	/*echo "<pre>";
	print_r($dlvr_data_arr);die;*/

	$subc_delevery_sql="SELECT b.batch_id, b.order_id, b.gsm, b.color_id, b.dia, b.item_id as composition_id, b.gray_qty, b.delivery_qty
	from subcon_delivery_mst a, subcon_delivery_dtls b
	where a.id=b.mst_id and a.company_id=$comp_cond and a.entry_form=0 and b.order_id in($subc_order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	group by b.batch_id, b.order_id, b.gsm, b.color_id, b.dia, b.item_id, b.gray_qty, b.delivery_qty";//die;
	$subc_dlvr_result=sql_select($subc_delevery_sql);
	$subc_dlvr_data_arr=array();
	foreach ($subc_dlvr_result as $row)
	{
		$subc_dlvr_data_arr[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('composition_id')]][$row[csf('color_id')]]['delivery_qty']=$row[csf('delivery_qty')];
		$subc_dlvr_data_arr[$row[csf('batch_id')]][$row[csf('order_id')]][$row[csf('gsm')]][$row[csf('dia')]][$row[csf('composition_id')]][$row[csf('color_id')]]['gray_qty']=$row[csf('gray_qty')];
	}
	/*echo "<pre>";
	print_r($subc_dlvr_data_arr);die;*/

	?>
	<div style="width: 1830px;">
        <table width="1800" cellpadding="0" cellspacing="0" id="caption" align="center">
            <tr>
               <td align="center" width="100%" colspan="6" class="form_caption" ><strong style="font-size:16px"><? echo $company_library[$cbo_company]; ?></strong></td>
            </tr> 
            <tr>  
               <td align="center" width="100%" colspan="6" class="form_caption" ><strong style="font-size:13px"><? echo $report_title;  echo ";<br> From : ".change_date_format(str_replace("'","",$txt_date_from))." To : ".change_date_format(str_replace("'","",$txt_date_to))."" ;?></strong></td>
            </tr>  
        </table>
    </div>
    <?
	if($order_type==1 || $order_type==2 || $order_type==3 || $order_type==4)
	{
		?>
		<style>
			.grad1 {			 
			  background-image: linear-gradient(#e6e6e6, #b1b1cd, #e0e0eb);
			}
		</style>
		<div>
			<h3>Buyer Wise Total (Without Subcontact)</h3>
			<table width="340" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
				<thead>
					<tr>
						<th width="100">Buyer Name</th>					
		                <th width="80">Grey used Qty</th>
		                <th width="80">Batch Qty</th>
		                <th>QC Qty</th>
		            </tr> 
				</thead>
			</table>
		    <div style="width:360px; overflow-y:auto; max-height:400px;" id="scroll_body3">
				<table width="340" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body3" align="left">
					<tbody>
						<?
						$j=1;
						foreach ($summary_arr as $buyer_id => $row) 
						{
							if ($j%2==0) $bgcolors="#EFEFEF"; else $bgcolors="#FFFFFF";								
							?>
							<tr bgcolor="<? echo $bgcolors; ?>">
			                    <td width="100"><? echo $buyer_library[$buyer_id]; ?></th>
			                    <td align="right" width="80"><? echo number_format($row['grey_used_qty'],2); ?></th>
			                    <td align="right" width="80"><? echo number_format($row['batch_qnty'],2); ?></th>
			                    <td align="right"><? echo number_format($row['qc_qnty'],4); ?></th>
							</tr>
							<?
							$j++;	
							$sum_total_grey_used_qty+=$row['grey_used_qty'];
							$sum_total_batch_qnty+=$row['batch_qnty'];
							$sum_total_qc_qnty+=$row['qc_qnty'];
						}
						?>
					</tbody>
			    </table>
			    <table width="340" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body3" align="left">
				    <tfoot>
				        <tr class="tbl_bottom">
				            <td width="100">Total :</td>
				            <td width="80"><strong><? echo number_format($sum_total_grey_used_qty,2); ?></strong></td>
				            <td width="80"><strong><? echo number_format($sum_total_batch_qnty,2); ?></strong></td>
				            <td ><strong><? echo number_format($sum_total_qc_qnty,2); ?></strong></td>
				        </tr>
				    </tfoot>
			    </table>
		    </div>
		</div>
		<br>
		<br>
		<h3>Direct Order/Sample With Order/Sample Without Order</h3>
		<div style="width: 1830px;">
			<table width="1800" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="scroll_body" align="left">
				<thead>
					<tr>
		                <th width="40">SL</th>
						<th width="70">QC Date</th> 
						<th width="100">Buyer</th>					
		                <th width="100">Booking No</th>
		                <th width="100">Batch</th>
		                <th width="30">Ext</th>
		                <th width="100">Fabric Type</th>
		                <th width="150">Fab. Composition</th>
		                <th width="40">F. Dia</th>
		                <th width="100">Dia Type</th>
		                <th width="40">F. GSM</th>
		                <th width="100">Fabric Color</th>
		                <th width="100">Body Part</th>
		                <th width="60">Batch Qty</th>
		                <th width="60">Grey Used Qty</th>
		                <th width="60">QC Qty</th>
		                <th width="60">Batch Balance</th>
		                <th width="60">QC Pass Qty</th>
		                <th width="60">QC Balance</th>
		                <th width="60">Delivery Qty</th>
		                <th width="60">Balance</th>
		                <th width="60">No of Roll</th>
		                <th width="100">Remarks</th>
		                <th >Dyeing Company</th>
		            </tr> 
				</thead>
			</table>
		    <div style="width:1830px; overflow-y:scroll; max-height:290px;font-size:12px; overflow-x:hidden;" id="scroll_body_short" align="left">
				<table width="1800" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
					<tbody>
						<?
						$i=1;
						$grand_total_batch_qnty=$grand_total_grey_used_qty=$grand_total_qc_qnty=$grand_total_batch_balance=
				            $grand_total_qc_pass_qty=$grand_total_qc_balance=$grand_total_delivery_qty=$grand_total_delivery_Balance=$grand_total_no_of_roll=0;
						foreach ($main_data_arr as $booking_type => $booking_val) 
						{
							

							foreach ($booking_val as $buyer_id => $buyer_val) 
							{
								$total_buyer_batch_qnty=$total_buyer_grey_used_qty=$total_buyer_qc_qnty=$total_buyer_batch_balance=$total_buyer_qc_pass_qty=$total_buyer_qc_balance=$total_buyer_delivery_qty=$total_buyer_delivery_Balance=$total_buyer_no_of_roll=0;

								foreach ($buyer_val as $booking_id => $booking_no) 
								{
									$total_booking_batch_qnty=$total_booking_grey_used_qty=$total_booking_qc_qnty=$total_booking_batch_balance=$total_booking_qc_pass_qty=$total_booking_qc_balance=$total_booking_delivery_qty=$total_booking_delivery_Balance=$total_booking_no_of_roll=0;
									foreach ($booking_no as $batch_id => $batch_no) 
									{
										foreach ($batch_no as $fabric_description_id => $fabric_description_val) 
										{
											foreach ($fabric_description_val as $width_id => $width_val) 
											{
												foreach ($width_val as $dia_type_id => $dia_width_type_val) 
												{
													foreach ($dia_width_type_val as $gsm_id => $gsm_val) 
													{
														foreach ($gsm_val as $color_id_key => $color_val) 
														{
															foreach ($color_val as $body_part_id => $row) 
															{
																if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
																$batch_balance=$row['batch_qnty']-$row['qc_qnty'];
																$qc_balance=$row['batch_qnty']-$row['qc_pass_qty'];

																$delivery_qty = $dlvr_data_arr[$row['batch_mst_id']][$row['prod_id']][$row['order_id']][$row['gsm']][$row['width']][$row['fabric_description_id']][$row['color_id']][$row['dia_width_type']][$row['body_part_id']]['current_delivery'];
																
																$delivery_Balance=$row['qc_pass_qty']-$delivery_qty;

																?>
																<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
																	<td width="40" align="center"><? echo $i; ?></td>
																	<td width="70" style="word-wrap:break-word; word-break: break-all;"><? echo change_date_format($row['receive_date']); ?></td>
																	<td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo $buyer_library[$row['buyer_id']]; ?></td>
																	<td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo $row['booking_no']; ?></td>
																	<td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo $row['batch_no']; ?></td>
																	<td width="30" style="word-wrap:break-word; word-break: break-all;"><? echo $row['extention_no']; ?></td>
																	<td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo $construction_arr[$row['fabric_description_id']]; ?></td>
																	<td width="150" style="word-wrap:break-word; word-break: break-all;"><? echo $composition_arr[$row['fabric_description_id']]; ?></td>
																	<td width="40" style="word-wrap:break-word; word-break: break-all;"><? echo $row['width']; ?></td>
																	<td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo $fabric_typee[$row['dia_width_type']]; ?></td>
																	<td width="40" style="word-wrap:break-word; word-break: break-all;"><? echo $row['gsm']; ?></td>
																	<td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo $color_library[$row['color_id']]; ?></td>
																	<td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo $body_part[$row['body_part_id']]; ?></td>
																	<td width="60" align="center" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($row['batch_qnty'],2); ?></td>
																	<td width="60" align="center" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($row['grey_used_qty'],2); ?></td>
																	<td width="60" align="center" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($row['qc_qnty'],2); ?></td>
																	<td width="60" align="center" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($batch_balance,2); ?></td>
																	<td width="60" align="center" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($row['qc_pass_qty'],2); ?></td>
																	<td width="60" align="center" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($qc_balance,2); ?></td>
																	<td width="60" align="center" title="<? echo $row['batch_mst_id']; ?>" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($delivery_qty,2); ?></td>
																	<td width="60" align="center" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($delivery_Balance,2); ?></td>
																	<td width="60" style="word-wrap:break-word; word-break: break-all;"><? echo $row['no_of_roll']; ?></td>
																	<td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo $row['remarks']; ?></td>
																	<td ><? echo $company_short_library[$row['knitting_company']]; ?></td>
																</tr>
																<?

																$i++;
																$total_booking_batch_qnty+=$row['batch_qnty'];
																$total_booking_grey_used_qty+=$row['grey_used_qty'];
																$total_booking_qc_qnty+=$row['qc_qnty'];
																$total_booking_batch_balance+=$batch_balance;
																$total_booking_qc_pass_qty+=$row['qc_pass_qty'];
																$total_booking_qc_balance+=$qc_balance;
																$total_booking_delivery_qty+=$delivery_qty;
																$total_booking_delivery_Balance+=$delivery_Balance;
																$total_booking_no_of_roll+=$row['no_of_roll'];
															}
														}
													}
												}
											}
										}
									}
									?>
									<tr class="grad1">
										<td colspan="13" align="right"><strong>Booking Total : </strong></td>
										<td width="60" align="right"><strong><? //echo $total_booking_batch_qnty; ?></strong></td>
							            <td width="60" align="right"><strong><? echo number_format($total_booking_grey_used_qty,2); ?></strong></td>
							            <td width="60" align="right"><strong><? echo number_format($total_booking_qc_qnty,2); ?></strong></td>
							            <td width="60" align="right"><strong><? echo number_format($total_booking_batch_balance,2); ?></strong></td>
							            <td width="60" align="right"><strong><? echo number_format($total_booking_qc_pass_qty,2); ?></strong></td>
							            <td width="60" align="right"><strong><? echo number_format($total_booking_qc_balance,2); ?></strong></td>
							            <td width="60" align="right"><strong><? echo number_format($total_booking_delivery_qty,2); ?></strong></td>
							            <td width="60" align="right"><strong><? echo number_format($total_booking_delivery_Balance,2); ?></strong></td>
							            <td width="60" align="right"><strong><? echo $total_booking_no_of_roll; ?></strong></td>
							            <td width="60"></td>
							            <td ></td>
									</tr>
									<?
									$total_buyer_batch_qnty+=$total_booking_batch_qnty;
						            $total_buyer_grey_used_qty+=$total_booking_grey_used_qty;
						            $total_buyer_qc_qnty+=$total_booking_qc_qnty;
						            $total_buyer_batch_balance+=$total_booking_batch_balance;
						            $total_buyer_qc_pass_qty+=$total_booking_qc_pass_qty;
						            $total_buyer_qc_balance+=$total_booking_qc_balance;
						            $total_buyer_delivery_qty+=$total_booking_delivery_qty;
						            $total_buyer_delivery_Balance+=$total_booking_delivery_Balance;
						            $total_buyer_no_of_roll+=$total_booking_no_of_roll;
								}
								?>
								<tr class="grad1">
									<td colspan="13" align="right"><strong>Buyer Total : </strong></td>
									<td width="60" align="right"><strong><? //echo $total_buyer_batch_qnty; ?></strong></td>
						            <td width="60" align="right"><strong><? echo number_format($total_buyer_grey_used_qty,2); ?></strong></td>
						            <td width="60" align="right"><strong><? echo number_format($total_buyer_qc_qnty,2); ?></strong></td>
						            <td width="60" align="right"><strong><? echo number_format($total_buyer_batch_balance,2); ?></strong></td>
						            <td width="60" align="right"><strong><? echo number_format($total_buyer_qc_pass_qty,2); ?></strong></td>
						            <td width="60" align="right"><strong><? echo number_format($total_buyer_qc_balance,2); ?></strong></td>
						            <td width="60" align="right"><strong><? echo number_format($total_buyer_delivery_qty,2); ?></strong></td>
						            <td width="60" align="right"><strong><? echo number_format($total_buyer_delivery_Balance,2); ?></strong></td>
						            <td width="60" align="right"><strong><? echo $total_buyer_no_of_roll; ?></strong></td>
						            <td width="60"></td>
						            <td ></td>
								</tr>
								<?
								$grand_total_batch_qnty+=$total_buyer_batch_qnty;
					            $grand_total_grey_used_qty+=$total_buyer_grey_used_qty;
					            $grand_total_qc_qnty+=$total_buyer_qc_qnty;
					            $grand_total_batch_balance+=$total_buyer_batch_balance;
					            $grand_total_qc_pass_qty+=$total_buyer_qc_pass_qty;
					            $grand_total_qc_balance+=$total_buyer_qc_balance;
					            $grand_total_delivery_qty+=$total_buyer_delivery_qty;
					            $grand_total_delivery_Balance+=$total_buyer_delivery_Balance;
					            $grand_total_no_of_roll+=$total_buyer_no_of_roll;
							}
						}							
						?>
					</tbody>
	        	</table>
	        	<table width="1800" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="report_table_footer" align="left">
				    <tfoot>
				        <tr class="tbl_bottom">
				            <td width="40"></td>
				            <td width="70"></td>
				            <td width="100"></td>
				            <td width="100"></td>
				            <td width="100"></td>
				            <td width="30"></td>
				            <td width="100"></td>
				            <td width="150"></td>
				            <td width="40"></td>
				            <td width="100"></td>
				            <td width="40"></td>
				            <td width="100"></td>
				            <td width="100"><strong>Grand Total : </strong></td>
				            <td width="60"><strong><? //echo $grand_total_batch_qnty; ?></strong></td>
				            <td width="60"><strong><? echo number_format($grand_total_grey_used_qty,2); ?></strong></td>
				            <td width="60"><strong><? echo number_format($grand_total_qc_qnty,2); ?></strong></td>
				            <td width="60"><strong><? echo number_format($grand_total_batch_balance,2); ?></strong></td>
				            <td width="60"><strong><? echo number_format($grand_total_qc_pass_qty,2); ?></strong></td>
				            <td width="60"><strong><? echo number_format($grand_total_qc_balance,2); ?></strong></td>
				            <td width="60"><strong><? echo number_format($grand_total_delivery_qty,2); ?></strong></td>
				            <td width="60"><strong><? echo number_format($grand_total_delivery_Balance,2); ?></strong></td>
				            <td width="60"><strong><? echo $grand_total_no_of_roll; ?></strong></td>
				            <td width="100"></td>
				            <td ></td>
				        </tr>
				    </tfoot>
			    </table>
		    </div>
		</div>
		<br>
		<br>
		<?  
	}
	
	if($order_type==1 || $order_type==5) // Subcontract
	{
		?>
		<style>
			.grad1 {			 
			  background-image: linear-gradient(#e6e6e6, #b1b1cd, #e0e0eb);
			}
		</style>
		<br>
		<br>
		<h3>Subcontract</h3>
		<div style="width: 1830px;">
			<table width="1800" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="scroll_body" align="left">
				<thead>
					<tr>
		                <th width="40">SL</th>
						<th width="70">Qc Date</th> 
						<th width="100">Party</th>					
		                <th width="100">Order No</th>
		                <th width="100">Batch</th>
		                <th width="30">Ext</th>
		                <th width="250">Fabric Const. Compo.</th> 
		                <th width="40">F. Dia</th>
		                <th width="100">Dia Type</th>
		                <th width="40">F. GSM</th>
		                <th width="100">Fabric Color</th>
		                <th width="100">Body Part</th>
		                <th width="60">Batch Qty</th>
		                <th width="60">Grey Used Qty</th>
		                <th width="60">QC Qty</th>
		                <th width="60">Batch Balance</th>
		                <th width="60" style="word-wrap:break-word; word-break: break-all;">Production Qty</th>
		                <th width="60" style="word-wrap:break-word; word-break: break-all;">Production Balance</th>
		                <th width="60">Delivery Qty</th>
		                <th width="60">Balance</th>
		                <th width="60">No of Roll</th>
		                <th width="100">Remarks</th>
		                <th >Dyeing Company</th>
		            </tr> 
				</thead>
			</table>
		    <div style="width:1830px; overflow-y:scroll; max-height:290px;font-size:12px; overflow-x:hidden;" id="scroll_body_short2" align="left">
				<table width="1800" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body2" align="left">
					<tbody>
						<?
						$i=1;
						$grand_total_batch_qnty=$grand_total_grey_used_qty=$grand_total_qc_qnty=$grand_total_batch_balance=$grand_total_qc_pass_qty=$grand_total_qc_balance=$grand_total_delivery_qty=$grand_total_delivery_Balance=$grand_total_no_of_roll=0;
						foreach ($subcon_data_arr as $order_key => $order_val) 
						{							
							$total_buyer_batch_qnty=$total_buyer_grey_used_qty=$total_buyer_qc_qnty=$total_buyer_batch_balance=$total_buyer_qc_pass_qty=$total_buyer_qc_balance=$total_buyer_delivery_qty=$total_buyer_delivery_Balance=$total_buyer_no_of_roll=0;
							foreach ($order_val  as $party_id => $party_val) 
							{
								$total_booking_batch_qnty=$total_booking_grey_used_qty=$total_booking_qc_qnty=$total_booking_batch_balance=$total_booking_qc_pass_qty=$total_booking_qc_balance=$total_booking_delivery_qty=$total_booking_delivery_Balance=$total_booking_no_of_roll=0;

								foreach ($party_val as $batch_key => $batch_val) 
								{
									foreach ($batch_val as $fabric_description_id => $fabric_description_val) 
									{
										foreach ($fabric_description_val as $dia_width => $dia_width_val) 
										{
											foreach ($dia_width_val as $dia_type_id => $dia_width_type_val) 
											{
												foreach ($dia_width_type_val as $gsm_id => $gsm_val) 
												{
													foreach ($gsm_val as $color_id_key => $row) 
													{
														if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
														$production_balance=$row['batch_qnty']-$row['product_qnty'];

														$gray_qty = $subc_dlvr_data_arr[$row['batch_id']][$row['order_id']][$row['gsm']][$row['dia_width']][$row['composition_id']][$row['color_id']]['gray_qty'];
														$delivery_qty = $subc_dlvr_data_arr[$row['batch_id']][$row['order_id']][$row['gsm']][$row['dia_width']][$row['composition_id']][$row['color_id']]['delivery_qty'];
														
														$delivery_Balance=$row['product_qnty']-$delivery_qty;

														?>
														<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
															<td width="40" align="center"><? echo $i; ?></td>
															<td width="70" style="word-wrap:break-word; word-break: break-all;"><? echo change_date_format($row['product_date']); ?></td>
															<td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo $party_library[$row['party_id']]; ?></td>
															<td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo $row['order_no']; ?></td>
															<td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo $row['batch_no']; ?></td>
															<td width="30" style="word-wrap:break-word; word-break: break-all;"><? echo $row['extention_no']; ?></td>
															<td width="250" style="word-wrap:break-word; word-break: break-all;"><? echo $row['fabric_description']; ?></td>
															
															<td width="40" style="word-wrap:break-word; word-break: break-all;"><? echo $row['dia_width']; ?></td>
															<td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo $fabric_typee[$row['width_dia_type']]; ?></td>
															<td width="40" style="word-wrap:break-word; word-break: break-all;"><? echo $row['gsm']; ?></td>
															<td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo $color_library[$row['color_id']]; ?></td>
															<td width="100" style="word-wrap:break-word; word-break: break-all;"></td>
															<td width="60" align="center" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($row['batch_qnty'],2); ?></td>
															<td width="60" align="center" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($gray_qty,2); ?></td>
															<td width="60" align="center" style="word-wrap:break-word; word-break: break-all;"></td>
															<td width="60" align="center" style="word-wrap:break-word; word-break: break-all;"></td>
															<td width="60" align="center" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($row['product_qnty'],2); ?></td>
															<td width="60" align="center" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($production_balance,2); ?></td>
															<td width="60" align="center" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($delivery_qty,2); ?></td>
															<td width="60" align="center" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($delivery_Balance,2); ?></td>
															<td width="60" style="word-wrap:break-word; word-break: break-all;"><? echo $row['no_of_roll']; ?></td>
															<td width="100" style="word-wrap:break-word; word-break: break-all;"><? echo $row['remarks']; ?></td>
															<td ><? echo $company_short_library[$row['company_id']]; ?></td>
														</tr>
														<?

														$i++;
														$total_booking_batch_qnty+=$row['batch_qnty'];
														$total_booking_grey_used_qty+=$gray_qty;	
														$total_booking_qc_pass_qty+=$row['product_qnty'];
														$total_booking_qc_balance+=$production_balance;
														$total_booking_delivery_qty+=$delivery_qty;
														$total_booking_delivery_Balance+=$delivery_Balance;
														$total_booking_no_of_roll+=$row['no_of_roll'];
													}
												}
											}
										}
									}
									?>
									<tr class="grad1">
										<td colspan="12" align="right"><strong>Order Total : </strong></td>
										<td width="60" align="right"><strong><? echo number_format($total_booking_batch_qnty,2); ?></strong></td>
							            <td width="60" align="right"><strong><? echo number_format($total_booking_grey_used_qty,2); ?></strong></td>
							            <td width="60" align="right"><strong></strong></td>
							            <td width="60" align="right"><strong></strong></td>
							            <td width="60" align="right"><strong><? echo number_format($total_booking_qc_pass_qty,2); ?></strong></td>
							            <td width="60" align="right"><strong><? echo number_format($total_booking_qc_balance,2); ?></strong></td>
							            <td width="60" align="right"><strong><? echo number_format($total_booking_delivery_qty,2); ?></strong></td>
							            <td width="60" align="right"><strong><? echo number_format($total_booking_delivery_Balance,2); ?></strong></td>
							            <td width="60" align="right"><strong><? echo $total_booking_no_of_roll; ?></strong></td>
							            <td width="60"></td>
							            <td ></td>
									</tr>
									<?
									$total_buyer_batch_qnty+=$total_booking_batch_qnty;
						            $total_buyer_grey_used_qty+=$total_booking_grey_used_qty;
						            $total_buyer_qc_pass_qty+=$total_booking_qc_pass_qty;
						            $total_buyer_qc_balance+=$total_booking_qc_balance;
						            $total_buyer_delivery_qty+=$total_booking_delivery_qty;
						            $total_buyer_delivery_Balance+=$total_booking_delivery_Balance;
						            $total_buyer_no_of_roll+=$total_booking_no_of_roll;
								}
								?>
								<tr class="grad1">
									<td colspan="12" align="right"><strong>Party Total : </strong></td>
									<td width="60" align="right"><strong><? echo number_format($total_buyer_batch_qnty,2); ?></strong></td>
						            <td width="60" align="right"><strong><? echo number_format($total_buyer_grey_used_qty,2); ?></strong></td>
						            <td width="60" align="right"><strong></strong></td>
						            <td width="60" align="right"><strong></strong></td>
						            <td width="60" align="right"><strong><? echo number_format($total_buyer_qc_pass_qty,2); ?></strong></td>
						            <td width="60" align="right"><strong><? echo number_format($total_buyer_qc_balance,2); ?></strong></td>
						            <td width="60" align="right"><strong><? echo number_format($total_buyer_delivery_qty,2); ?></strong></td>
						            <td width="60" align="right"><strong><? echo number_format($total_buyer_delivery_Balance,2); ?></strong></td>
						            <td width="60" align="right"><strong><? echo $total_buyer_no_of_roll; ?></strong></td>
						            <td width="60"></td>
						            <td ></td>
								</tr>
								<?
								$grand_total_batch_qnty+=$total_booking_batch_qnty;
					            $grand_total_grey_used_qty+=$total_booking_grey_used_qty;
					            $grand_total_qc_pass_qty+=$total_booking_qc_pass_qty;
					            $grand_total_qc_balance+=$total_booking_qc_balance;
					            $grand_total_delivery_qty+=$total_booking_delivery_qty;
					            $grand_total_delivery_Balance+=$total_booking_delivery_Balance;
					            $grand_total_no_of_roll+=$total_booking_no_of_roll;
							}
						}							
						?>
					</tbody>
	        	</table>
	        	<table width="1800" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="report_table_footer" align="left">
				    <tfoot>
				        <tr class="tbl_bottom">
				            <td width="40"></td>
				            <td width="70"></td>
				            <td width="100"></td>
				            <td width="100"></td>
				            <td width="100"></td>
				            <td width="30"></td>
				            <td width="250"></td>
				            <td width="40"></td>
				            <td width="100"></td>
				            <td width="40"></td>
				            <td width="100"></td>
				            <td width="100"><strong>Grand Total : </strong></td>
				            <td width="60"><strong><? echo number_format($grand_total_batch_qnty,2); ?></strong></td>
				            <td width="60"><strong><? echo number_format($grand_total_grey_used_qty,2); ?></strong></td>
				            <td width="60"><strong></strong></td>
				            <td width="60"><strong></strong></td>
				            <td width="60"><strong><? echo number_format($grand_total_qc_pass_qty,2); ?></strong></td>
				            <td width="60"><strong><? echo number_format($grand_total_qc_balance,2); ?></strong></td>
				            <td width="60"><strong><? echo number_format($grand_total_delivery_qty,2); ?></strong></td>
				            <td width="60"><strong><? echo number_format($grand_total_delivery_Balance,2); ?></strong></td>
				            <td width="60"><strong><? echo $grand_total_no_of_roll; ?></strong></td>
				            <td width="100"></td>
				            <td ></td>
				        </tr>
				    </tfoot>
			    </table>
		    </div>
		</div>
		<br>
		<br>
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
	$is_created = fwrite($create_new_doc,ob_get_contents());
	//$filename=$user_id."_".$name.".xls";
	echo "$order_type_is****$filename";

	exit();
}

?>
