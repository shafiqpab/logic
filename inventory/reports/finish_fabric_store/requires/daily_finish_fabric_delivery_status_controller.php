<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, company_location_id, item_cate_id FROM user_passwd where id=$user_id");
//echo "SELECT unit_id as company_id,store_location_id,item_cate_id FROM user_passwd where id=$user_id";
$company_id = $userCredential[0][csf('company_id')];
$company_location_id = $userCredential[0][csf('company_location_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];
$item_cate_id = $userCredential[0][csf('item_cate_id')];
//var_dump($item_cate_id);
$company_credential_cond = $com_location_credential_cond = $store_location_credential_cond = $item_cate_credential_cond = "";

if ($company_id >0) {
	$company_credential_cond = " and comp.id in($company_id)";
}
if ($company_location_id !='') {
	$com_location_credential_cond = " and id in($company_location_id)";
}
if ($store_location_id !='') {
	$store_location_credential_cond = "and a.id in($store_location_id)";
}
if($item_cate_id !='') {
	$item_cate_credential_cond = $item_cate_id ;
}

//========== user credential end ==========

//====================Location ACTION========
if ($action=="load_drop_down_location_lc")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_location", 110, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 $com_location_credential_cond order by location_name","id,location_name", 1, "-- Select Location --", 0, "" );
	exit();
}

if($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);
	if($data[1]==1) $party="1,3,21,90"; else $party="80";
	echo create_drop_down( "cbo_buyer_id", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in ($party)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","" );
	exit();
}

if($action=="report_generate") // Show Button
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$cbo_report_type=str_replace("'","",$cbo_report_type);
	//$cbo_presentation=str_replace("'","",$cbo_presentation);
	$cbo_search_by=str_replace("'","",$cbo_search_by);
	$txt_search_comm=str_replace("'","",$txt_search_comm);
	$txt_file_no=str_replace("'","",$txt_file_no);
	$txt_ref_no=str_replace("'","",$txt_ref_no);
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_sewing_company=str_replace("'","",$cbo_sewing_company);
	$source=str_replace("'","",$cbo_source);
	$cbo_location=str_replace("'","",$cbo_location);
	$issue_purpose=str_replace("'","",$cbo_issue_purpose);
	$cbo_product_category=str_replace("'","",$cbo_product_category);
	//echo $cbo_report_type;die;
	if(str_replace("'","",$cbo_buyer_id)!="" && str_replace("'","",$cbo_buyer_id)!=0) $buyer_id_cond=" and f.buyer_id=$cbo_buyer_id";
	$job_no=str_replace("'","",$txt_job_no);
	$search_cond='';
	if($cbo_search_by==1)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and a.job_no_prefix_num in ($txt_search_comm) ";
	}
	else if($cbo_search_by==2)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and a.style_ref_no LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==3)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.po_number LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==4)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.file_no LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==5)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.grouping LIKE '%$txt_search_comm%'";
	}
	else
	{
		$search_cond.="";
	}
	
	$cbo_year_val=str_replace("'","",$cbo_year);
	$order_no=str_replace("'","",$txt_order_id);
	if(str_replace("'","",$txt_order_id)!="" && str_replace("'","",$txt_order_id)!=0) $order_id_cond_trans=" and c.id in ($order_no)";
	
	$start_date=str_replace("'","",$txt_date_from);
	$end_date=str_replace("'","",$txt_date_to);
	//if( $date_from=="") $issue_date_trans=""; else $receive_date_trans= " and a.transfer_date <=".$txt_date_from."";

	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and f.issue_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and f.issue_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";	
		}
	}
	else
	{
		$date_cond="";
	}
	if ($cbo_location ==0 ) $search_cond.=""; else $search_cond.=" and f.location_id=$cbo_location ";
	if ($cbo_product_category ==0 ) $search_cond.=""; else $search_cond.=" and a.product_category=$cbo_product_category ";
	if ($cbo_location ==0 ) $location_cond=""; else $location_cond=" and f.location_id=$cbo_location ";
	$source_cond = "";
	if ($source) {
		$source_cond=" and f.knit_dye_source=$source";
	}

	if ($issue_purpose==0) $issuePurposeCond=""; else $issuePurposeCond=" and f.issue_purpose=$issue_purpose";

	//echo $source_cond;
	ob_start();


	if($cbo_report_type==1) // Knit Finish Start
	{
		if($search_cond!='')
		{
			/* $sql_issue="SELECT a.job_no_prefix_num, a.job_no, a.style_ref_no,b.po_number as po_no, d.po_breakdown_id as po_id, d.quantity,f.id,f.issue_number, f.challan_no, f.company_id, f.issue_date, f.issue_purpose, f.buyer_id, f.sample_type, f.knit_dye_source as knitting_source, f.knit_dye_company, f.cutt_req_no,f.location_id ,e.id as dtlsId, e.mst_id, e.trans_id, e.batch_id, e.prod_id,e.sample_type, e.issue_qnty,e.fabric_shade, e.store_id, e.no_of_roll, e.remarks, e.rack_no, e.shelf_no,e.floor,e.room, e.cutting_unit, e.order_id, e.order_save_string, e.roll_save_string, e.body_part_id,e.gmt_item_id,e.uom,g.product_name_details, g.color ,g.dia_width as width ,g.gsm from wo_po_details_master a, wo_po_break_down b, inv_transaction c, order_wise_pro_details d, inv_issue_master f, inv_finish_fabric_issue_dtls e , product_details_master g where a.job_no=b.job_no_mst and d.po_breakdown_id=b.id and c.id = d.trans_id and e.id=d.dtls_id and f.id=e.mst_id and f.id=e.mst_id and e.prod_id=g.id and d.entry_form in (18) and c.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $date_cond $buyer_id_cond $search_cond $source_cond and f.company_id=$cbo_company_id 
			group by a.job_no_prefix_num, a.job_no, a.style_ref_no,b.po_number, d.po_breakdown_id,d.quantity,f.id,f.issue_number, f.challan_no, f.company_id, f.issue_date, f.issue_purpose, f.buyer_id, f.sample_type, f.knit_dye_source, f.knit_dye_company, f.cutt_req_no,f.location_id ,e.id , e.mst_id, e.trans_id, e.batch_id, e.prod_id,e.sample_type, e.issue_qnty,e.fabric_shade, e.store_id, e.no_of_roll, e.remarks, e.rack_no, e.shelf_no,e.floor,e.room, e.cutting_unit, e.order_id, e.order_save_string, e.roll_save_string, e.body_part_id,e.gmt_item_id,e.uom,g.product_name_details, g.color ,g.dia_width  ,g.gsm order by e.id"; */
			$sql_issue="SELECT a.job_no_prefix_num, a.job_no, a.style_ref_no,b.po_number as po_no, d.po_breakdown_id as po_id, d.quantity,f.id,f.issue_number, f.challan_no, f.company_id, f.issue_date, f.issue_purpose, f.buyer_id, f.sample_type, f.knit_dye_source as knitting_source, f.knit_dye_company, f.cutt_req_no, f.location_id ,e.id as dtlsId, e.mst_id, e.trans_id, e.batch_id, e.prod_id,e.sample_type, e.issue_qnty,e.fabric_shade, e.store_id, e.no_of_roll, e.remarks, e.rack_no, e.shelf_no,e.floor,e.room, e.cutting_unit, e.order_id, e.order_save_string, e.roll_save_string, e.body_part_id,e.gmt_item_id,e.uom,g.product_name_details, g.color ,g.dia_width as width ,g.gsm 
			from wo_po_details_master a, wo_po_break_down b, inv_transaction c, order_wise_pro_details d, inv_issue_master f, inv_finish_fabric_issue_dtls e , product_details_master g 
			where a.job_no=b.job_no_mst and d.po_breakdown_id=b.id and c.id = d.trans_id and e.id=d.dtls_id and f.id=e.mst_id and f.id=e.mst_id and e.prod_id=g.id and d.entry_form in (18) and c.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0  $date_cond $buyer_id_cond $search_cond $source_cond and f.company_id=$cbo_company_id $issuePurposeCond
			union all
			SELECT 0 as job_no_prefix_num, null as job_no, null as style_ref_no, null as po_no, null as po_id, e.issue_qnty as quantity , f.id,f.issue_number, f.challan_no, f.company_id, f.issue_date, f.issue_purpose, f.buyer_id, f.sample_type, f.knit_dye_source as knitting_source, f.knit_dye_company, f.cutt_req_no, f.location_id ,e.id as dtlsId, e.mst_id, e.trans_id, e.batch_id, e.prod_id,e.sample_type, e.issue_qnty,e.fabric_shade, e.store_id, e.no_of_roll, e.remarks, e.rack_no, e.shelf_no,e.floor,e.room, e.cutting_unit, e.order_id, e.order_save_string, e.roll_save_string, e.body_part_id,e.gmt_item_id,e.uom, g.product_name_details, g.color ,g.dia_width as width ,g.gsm
			from inv_issue_master f, inv_finish_fabric_issue_dtls e, product_details_master g
			where f.id=e.mst_id and  e.prod_id=g.id and f.entry_form=18 and f.status_active=1 and e.status_active = 1 and e.is_deleted = 0 and f.company_id=$cbo_company_id $date_cond $buyer_id_cond $source_cond $location_cond $issuePurposeCond and f.sample_type !=0";
		}
		else
		{
			$sql_issue="SELECT f.id,f.issue_number, f.challan_no, f.company_id, f.issue_date, f.issue_purpose, f.buyer_id, f.sample_type, f.knit_dye_source as knitting_source, f.knit_dye_company, f.cutt_req_no,f.location_id ,e.id as dtlsId, e.mst_id, e.trans_id, e.batch_id, e.prod_id,e.sample_type, e.issue_qnty as quantity,e.fabric_shade, e.store_id, e.no_of_roll, e.remarks, e.rack_no, e.shelf_no,e.floor,e.room, e.cutting_unit, e.order_id, e.order_save_string, e.roll_save_string, e.body_part_id,e.gmt_item_id,e.uom,g.product_name_details, g.color ,g.dia_width as width ,g.gsm from inv_issue_master f, inv_finish_fabric_issue_dtls  e  , product_details_master g where f.id=e.mst_id and e.prod_id=g.id and f.status_active=1 and e.status_active = 1 and e.is_deleted = 0 and g.status_active = 1 and f.company_id=$cbo_company_id $date_cond $buyer_id_cond $source_cond $issuePurposeCond order by e.id";
		}
	 	//echo $sql_issue; //die;
		$sql_issue_res=sql_select($sql_issue); $data_arr=array(); $data_summary_arr=array();
		foreach ($sql_issue_res as $row)
		{
			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['id']=$row[csf('id')];
			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['issue_date']=change_date_format($row[csf('issue_date')]);
			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['issue_number']=$row[csf('issue_number')];
			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['knitting_source']=$row[csf('knitting_source')];
			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['company_id']=$row[csf('company_id')];
			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['knitting_company']=$row[csf('knitting_company')];
			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['challan_no']=$row[csf('challan_no')];
			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['buyer_id']=$row[csf('buyer_id')];
			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['issue_purpose']=$row[csf('issue_purpose')];
			
			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['batch_id']=$row[csf('batch_id')];
			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['product_name_details']=$row[csf('product_name_details')];
			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['color']=$row[csf('color')];
			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['prod_id']=$row[csf('prod_id')];
			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['gsm']=$row[csf('gsm')];
			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['width']=$row[csf('width')];
			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['issue_qnty']=$row[csf('issue_qnty')];

			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['uom']=$row[csf('uom')];
			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['booking_id']=$row[csf('booking_id')];
			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['booking_no']=$row[csf('booking_no')];
			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['order_id']=$row[csf('order_id')];
			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['knit_dye_company']=$row[csf('knit_dye_company')];

			
			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['job_no'].=$row[csf('job_no')].",";
			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['style_ref_no'].=$row[csf('style_ref_no')].",";
			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['po_number'].=$row[csf('po_no')].",";
			

			if($row[csf('issue_purpose')]==1)
			{
				$data_summary_arr[$row[csf('knitting_source')]][$row[csf('issue_date')]][$row[csf('knit_dye_company')]][$row[csf('buyer_id')]][$row[csf('uom')]]['issue_qty']+= $row[csf('quantity')];

				$data_summary_arr[$row[csf('knitting_source')]][$row[csf('issue_date')]][$row[csf('knit_dye_company')]][$row[csf('buyer_id')]][$row[csf('uom')]]['sales_qty']+= $row[csf('quantity')];
			}
			else
			{
				$data_summary_arr[$row[csf('knitting_source')]][$row[csf('issue_date')]][$row[csf('knit_dye_company')]][$row[csf('buyer_id')]][$row[csf('uom')]]['issue_qty']+= $row[csf('quantity')];

				//$data_summary_arr[$row[csf('knitting_source')]][$row[csf('issue_date')]][$row[csf('knitting_company')]][$row[csf('buyer_id')]][$row[csf('uom')]]['sales_qty']+= 0;
			}
			
			/*echo "=====================================";
			echo "<pre>";
			print_r($data_summary_arr);	*/
		}
		//echo "<pre>";
		//print_r($data_summary_arr);	

		unset($sql_rcv_res);
		$duyeingCom_arr=return_library_array( "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id", "company_name"  );
		$duyeingSup_arr=return_library_array( "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id  and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name"  );
		$buyer_arr=return_library_array( "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name", "id", "buyer_name"  );
		$color_arr = return_library_array("select id, color_name from lib_color","id","color_name");
		$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst","id","batch_no");

		$batch_sql=sql_select("select id, batch_no, booking_no from pro_batch_create_mst where status_active=1 and is_deleted=0");
		$batch_arr=array();
		foreach ($batch_sql as $row) 
		{
			$batch_arr[$row[csf('id')]]['batch_no']= $row[csf('batch_no')];
			$batch_arr[$row[csf('id')]]['booking_no']= $row[csf('booking_no')];
		}
		
		$order_sql=sql_select("select a.job_no, a.style_ref_no,b.po_number as po_no, b.id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1");
		$order_arr=array();
		foreach ($order_sql as $row) 
		{
			$order_arr[$row[csf('id')]]['job_no']= $row[csf('job_no')];
			$order_arr[$row[csf('id')]]['style_ref_no']= $row[csf('style_ref_no')];
			$order_arr[$row[csf('id')]]['po_number']= $row[csf('po_no')];
		}
		
		unset($order_sql);
		?>
		
		<style type="text/css">
		.nsbreak{word-break: break-all;}
		</style>
			<table cellpadding="0" cellspacing="0" width="1860">
				<tr class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="14" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="14" style="font-size:16px"><strong><? echo $duyeingCom_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
				</tr>
				<?
				if($start_date!="" && $end_date!="")
				{
					?>
					<tr  class="form_caption" style="border:none;">
						<td align="center" width="100%" colspan="14" style="font-size:14px"><strong> <? echo change_date_format(str_replace("'","",$start_date))." To ".change_date_format(str_replace("'","",$end_date)) ;?></strong></td>
					</tr>
					<?
				}
				?>
			</table>
			<?
			$grand_total_summary=0; $grand_total_sales_summary=0;  $view_1=0; $view_2=0; $view_3=0; $view_4=0;
			foreach ($data_summary_arr  as $knitting_source_id=>$knitting_source_val)
			{
				if($knitting_source_id==1)
				{
					?>
					<div style="padding: 10px;">
						<table width="720" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_summary" >
							<thead>
								<tr>
									<th colspan="7">Daily T-Shirt  In-House Fabric  Issue Summary</th>
								</tr>
								<tr>
									<th width="30" rowspan="2">SL</th>
									<th width="80" rowspan="2">Issue Date</th>
									<th width="150" rowspan="2">Service Source</th>
									<th width="150" rowspan="2">In-House  Company</th>
									<th width="150" rowspan="2">Buyer</th> 
									<th width="50" rowspan="2">Uom</th>
									<th width="100" rowspan="2">Issue Qty</th>
									<!-- <th width="80" rowspan="2">Issue Sales Qty</th> -->
								</tr>
							</thead>
						</table>
						<div style="width:740px; max-height:200px; overflow-y:scroll;" id="scroll_body">
							<table width="720" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table nsbreak" id="table_summary1">
								<? $y=1;  $total_qty=0; $total_sales_qty=0; $view_1=1;
								foreach ($knitting_source_val  as $issue_date=>$issue_date_val)
								{
									foreach ($issue_date_val  as $knitting_company=>$knitting_company_val)
									{
										foreach ($knitting_company_val  as $buyer_id=>$buyer_id_val)
										{
											foreach ($buyer_id_val  as $uom_id=>$val)
											{
												if ($y%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
												?>
												<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trSum1_<? echo $y;?>','<? echo $bgcolor;?>')" id="trSum1_<? echo $y;?>">
													<td width="30"><? echo $y; ?></td>
													<td width="80"><? echo change_date_format($issue_date); ?></td>
													<td width="150"><? echo $knitting_source[$knitting_source_id]; ?></td>
													<td width="150"><? echo $duyeingCom_arr[$knitting_company]; ?></td>
													<td width="150"><? echo $buyer_arr[$buyer_id]; ?></td>
													<td width="50"><? echo $unit_of_measurement[$uom_id]; ?></td>
													<td width="100" align="right"><? $total_qty+=$val['issue_qty']; echo number_format($val['issue_qty'],2); ?></td>
													<!-- <td width="80" align="right"><? //$total_sales_qty+=$val['sales_qty']; echo number_format($val['sales_qty'],2); ?></td> -->
												</tr>
												<?
												$y++;
											}
										}
									}
								}
								?>
								<tfoot class="tbl_bottom">
									<td width="610" colspan="6" align="right">Total</td>
									<td id="100" align="right"><? $grand_total_summary+= $total_qty; echo number_format($total_qty,2); ?></td>
									<!-- <td id="80" align="right"><? //$grand_total_sales_summary+= $total_sales_qty; echo number_format($total_sales_qty,2); ?></td> -->
								</tfoot>
							</table>
						</div>
					</div>
					<?
				}
				else if($knitting_source_id==3)
				{
					?>
					<div style="padding: 10px;">
						<table width="720" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_summary" >
							<thead>
								<tr>
									<th colspan="7">Daily T-Shirt Out-bound Subcontract Fabric  Issue Summary</th>
								</tr>
								<tr>
									<th width="30" rowspan="2">SL</th>
									<th width="80" rowspan="2">Issue Date</th>
									<th width="150" rowspan="2">Service Source</th>
									<th width="150" rowspan="2">Service Company</th>
									<th width="150" rowspan="2">Buyer</th> 
									<th width="50" rowspan="2">Uom</th>
									<th width="100" rowspan="2">Issue Qty</th>
									<!-- <th width="80" rowspan="2">Issue Sales Qty</th> -->
								</tr>
							</thead>
						</table>
						<div style="width:740px; max-height:200px; overflow-y:scroll;" id="scroll_body1">
							<table  width="720" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table nsbreak" id="table_summary2">
								<? $z=1; $total_qty=0;  $total_sales_qty=0; $view_2=1;
								foreach ($knitting_source_val  as $issue_date=>$issue_date_val)
								{
									foreach ($issue_date_val  as $knitting_company=>$knitting_company_val)
									{
										foreach ($knitting_company_val  as $buyer_id=>$buyer_id_val)
										{
											foreach ($buyer_id_val  as $uom_id=>$val)
											{
												if ($z%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
												?>
												<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trSum2_<? echo $z;?>','<? echo $bgcolor;?>')" id="trSum2_<? echo $z;?>">
													<td width="30"><? echo $z; ?></td>
													<td width="80"><? echo change_date_format($issue_date); ?></td>
													<td width="150"><? echo $knitting_source[$knitting_source_id]; ?></td>
													<td width="150"><? echo $duyeingSup_arr[$knitting_company]; ?></td>
													<td width="150"><? echo $buyer_arr[$buyer_id]; ?></td>
													<td width="50"><? echo $unit_of_measurement[$uom_id]; ?></td>
													<td width="100" align="right"><? $total_qty+=$val['issue_qty']; echo number_format($val['issue_qty'],2); ?></td>
													<!-- <td width="80" align="right"><? //$total_sales_qty+=$val['sales_qty']; echo number_format($val['sales_qty'],2); ?></td> -->
												</tr>
												<?
												$z++;
											}
										}
									}
								}
								?>
								<tfoot class="tbl_bottom">
									<tr>
										<td width="610" colspan="6" align="right">Total</td>
										<td id="100" align="right"><? $grand_total_summary+= $total_qty; echo number_format($total_qty,2); ?></td>
										<!-- <td id="80" align="right"><? //$grand_total_sales_summary+= $total_sales_qty; echo number_format($total_sales_qty,2); ?></td> -->
									</tr>
									
								</tfoot>
							</table>
						</div>
						<table width="720" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table nsbreak" id="table_grand_total1" >
							<tfoot>
								<tr>
									<th width="610" colspan="6" align="right">Grand Total</th>
									<th width="100" align="right"><? echo number_format($grand_total_summary,2); ?></th>
									<!-- <th width="80" align="right"><? //echo number_format($grand_total_sales_summary,2); ?></th> -->
								</tr>
							</tfoot>
						</table>
					</div>
					<?
				}
			}
			?>
		<?
		$i=1; $k=1;  $grand_total=0; $grand_sales_total=0; 
		foreach ($data_arr  as $knitting_source_id=>$knitting_source_val)
		{
			$total_issue_qty=0;
			if($knitting_source_id==1)
			{
				?>
				<div style="padding: 10px;">
					<table width="2060" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" > 
						<thead>
							<tr>
								<th colspan="20">Daily T-Shirt In-House Fabric Issue Status</th>
							</tr>
							<tr>
								<th width="30" rowspan="2">SL</th>
								<th width="70" rowspan="2">Issue Date</th>
								<th width="120" rowspan="2">System ID</th>
								<th width="50" rowspan="2">Service Source</th>
								<th width="145" rowspan="2">In-House Company</th> 
								<th width="80" rowspan="2">Challan No</th>
								<th width="120" rowspan="2">Buyer</th>

								<th width="100" rowspan="2">Job No</th>
								<th width="120" rowspan="2">F.Booking No</th>
								<th width="180" rowspan="2">Style Ref.</th>
								<th width="200" rowspan="2">Order No</th>

								<th width="100" rowspan="2">Color</th>
								<th width="100" rowspan="2">Batch No</th>
								<th width="150" rowspan="2">Fabric Type</th>
								<th width="55" rowspan="2">GSM</th>
								<th width="60" rowspan="2">F.Dia</th>
								<th width="100" rowspan="2">Purpose</th>

								<th width="100" rowspan="2">Issue Qty</th>
								<th width="100" rowspan="2">Issue Sales Qty</th>
								<th rowspan="2">Uom</th>
							</tr>
						</thead>
					</table>
					<div style="width:2080px; max-height:300px; overflow-y:scroll;" id="scroll_body2">
						<table width="2060" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table nsbreak" id="table_body">
							<? $view_3=1;
							foreach ($knitting_source_val  as $mst_id=>$mst_id_val)
							{
								foreach ($mst_id_val  as $batch_id=>$batch_id_val)
								{
									foreach ($batch_id_val  as $dtls_id=>$dtls_id_val)
									{
										foreach ($dtls_id_val  as $issue_qnty=>$val)
										{
											$jobNos=''; $styleRefNos=''; $poNumbers='';
											if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
											?>
											<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trDtl1_<? echo $i;?>','<? echo $bgcolor;?>')" id="trDtl1_<? echo $i;?>">
												<td width="30"><? echo $i; ?></td>
												<td width="70"><? echo $val['issue_date']; ?></td>
												<td width="120"><? echo $val['issue_number']; ?></td>
												<td width="50"><? echo $knitting_source[$knitting_source_id]; ?></td>
												<td width="145"><? echo $duyeingCom_arr[$val['company_id']]; ?></td>
												<td width="80"><? echo $val['challan_no']; ?></td>
												<td width="120"><? echo $buyer_arr[$val['buyer_id']]; ?></td>
												<td width="100"><?
												if($val['order_id']!='')
												{
													$order_ids=explode(",",$val['order_id']); $job_nos=''; $style_ref_nos=''; $po_numbers='';
													foreach ($order_ids as $value) 
													{
														$job_nos.=$order_arr[$value]['job_no'].",";
														$style_ref_nos.=$order_arr[$value]['style_ref_no'].",";
														$po_numbers.=$order_arr[$value]['po_number'].",";
													}
													$jobNos=implode(",",array_unique(explode(",",chop($job_nos,","))));
													$styleRefNos=implode(",",array_unique(explode(",",chop($style_ref_nos,","))));
													$poNumbers=implode(",",array_unique(explode(",",chop($po_numbers,","))));
												}
												
												echo $jobNos; ?></td>
												<td width="120"><? echo $batch_arr[$batch_id]['booking_no']; ?></td>
												<td width="180"><? echo $styleRefNos; ?></td>
												<td width="200"><? echo $poNumbers; ?></td>
												<td width="100"><? echo $color_arr[$val['color']]; ?></td>
												<td width="100"><? echo $batch_arr[$batch_id]['batch_no']; ?></td>
												<td width="150"><? echo $val['product_name_details']; ?></td>
												<td width="55"><? echo $val['gsm']; ?></td>
												<td width="60"><? echo $val['width']; ?></td>
												<td width="100"><p><? echo $yarn_issue_purpose[$val["issue_purpose"]]; ?></p></td>
												<td width="100" align="right"><? $total_issue_qty +=$issue_qnty; echo number_format($issue_qnty,2); ?></td>
												<td width="100" align="right"><? 
												if($val['issue_purpose']==1)
												{
													 $total_sales_issue_qty +=$issue_qnty; echo number_format($issue_qnty,2);
												}
												else 
												{
													echo "&nbsp";
												} ?></td>
												<td width="65"><? echo $unit_of_measurement[$val['uom']]; ?></td>
											</tr>
											<?
											$i++;
										}
									}
								}	
							}
							?>
						</table>
					</div>
					<table width="2060" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
						<tfoot class="tbl_bottom">
							<td colspan="17" width="1695" align="right">Total</td>
							<td id="" width="100" align="right"><? $grand_total+=$total_issue_qty; echo number_format($total_issue_qty,2); ?></td>
							<td id="" width="100" align="right"><? $grand_sales_total+=$total_sales_issue_qty; echo number_format($total_sales_issue_qty,2); ?></td>
							<td width="65" id="">&nbsp;</td>
						</tfoot>
					</table>					
				</div>
				<?
			}
			if($knitting_source_id==3)
			{
				?>
				<div style="padding: 10px;">
					<table width="1960" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" > 
						<thead>
							<tr>
								<th colspan="19">Daily T-Shirt Out-bound Subcontract Fabric Issue Status</th>
							</tr>
							<tr>
								<th width="30" rowspan="2">SL</th>
								<th width="70" rowspan="2">Issue Date</th>
								<th width="120" rowspan="2">System ID</th>
								<th width="120" rowspan="2">Service Source</th>
								<th width="120" rowspan="2">Service Company</th> 
								<th width="80" rowspan="2">Challan No</th>
								<th width="120" rowspan="2">Buyer</th>

								<th width="100" rowspan="2">Job No</th>
								<th width="110" rowspan="2">F.Booking No</th>
								<th width="150" rowspan="2">Style Ref.</th>
								<th width="190" rowspan="2">Order No</th>

								<th width="100" rowspan="2">Color</th>
								<th width="100" rowspan="2">Batch No</th>
								<th width="150" rowspan="2">Fabric Type</th>
								<th width="60" rowspan="2">GSM</th>
								<th width="60" rowspan="2">F.Dia</th>
								<th width="100">Purpose</th>

								<th width="100" rowspan="2">Issue Qty</th>
								<!-- <th width="100" rowspan="2">Issue Sales Qty</th> -->
								<th rowspan="2">Uom</th>
							</tr>
						</thead>
					</table>
					<div style="width:1980px; max-height:300px; overflow-y:scroll;" id="scroll_body3">
						<table width="1960" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table nsbreak" id="table_body">
							<? $total_issue_qty=0; $total_sales_issue_qty=0; $view_4=1;
							foreach ($knitting_source_val  as $mst_id=>$mst_id_val)
							{
								foreach ($mst_id_val  as $batch_id=>$batch_id_val)
								{
									foreach ($batch_id_val  as $dtls_id=>$dtls_id_val)
									{
										foreach ($dtls_id_val  as $issue_qnty=>$val)
										{
											$job_nos=''; $styleRefNos=''; $poNumbers='';
											if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
											?>
											<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trDtl2_<? echo $k;?>','<? echo $bgcolor;?>')" id="trDtl2_<? echo $k;?>">
												<td width="30"><? echo $k; ?></td>
												<td width="70"><? echo $val['issue_date']; ?></td>
												<td width="120"><? echo $val['issue_number']; ?></td>
												<td width="120"><? echo $knitting_source[$knitting_source_id]; ?></td>
												<td width="120" title="<? echo $val['knit_dye_company']; ?>"><? echo $duyeingSup_arr[$val['knit_dye_company']]; ?></td>
												<td width="80"><? echo $val['challan_no']; ?></td>
												<td width="120"><? echo $buyer_arr[$val['buyer_id']]; ?></td>
												<td width="100"><?
												if($val['order_id']!='')
												{
													$order_ids=explode(",",$val['order_id']); $job_nos=''; $style_ref_nos=''; $po_numbers='';
													foreach ($order_ids as $value) 
													{
														$job_nos.=$order_arr[$value]['job_no'].",";
														$style_ref_nos.=$order_arr[$value]['style_ref_no'].",";
														$po_numbers.=$order_arr[$value]['po_number'].",";
													}
													$jobNos=implode(",",array_unique(explode(",",chop($job_nos,","))));
													$styleRefNos=implode(",",array_unique(explode(",",chop($style_ref_nos,","))));
													$poNumbers=implode(",",array_unique(explode(",",chop($po_numbers,","))));
												}
												
												echo $jobNos; ?></td>
												<td width="110"><? echo $batch_arr[$batch_id]['booking_no']; ?></td>
												<td width="150"><? echo $styleRefNos; ?></td>
												<td width="190"><? echo $poNumbers; ?></td>
												<td width="100"><? echo $color_arr[$val['color']]; ?></td>
												<td width="100"><? echo $batch_arr[$batch_id]['batch_no']; ?></td>
												<td width="150"><? echo $val['product_name_details']; ?></td>
												<td width="60"><? echo $val['gsm']; ?></td>
												<td width="60"><? echo $val['width']; ?></td>
												<td width="100"><p><? echo $yarn_issue_purpose[$val["issue_purpose"]]; ?></p></td>
												<td width="100" align="right"><? $total_issue_qty +=$issue_qnty; echo number_format($issue_qnty,2); ?></td>
												<!-- <td width="100" align="right">--><?  
												/*if($val['issue_purpose']==1)
												{
													 $total_sales_issue_qty +=$issue_qnty; echo number_format($issue_qnty,2);
												}
												else 
												{
													echo "&nbsp";
												}*/ ?><!--</td>-->
												<td align="center"><? echo $unit_of_measurement[$val['uom']]; ?></td>
											</tr>
											<?
											$k++;
										}
									}
								}
											
							}
							?>
							<tfoot class="tbl_bottom">
								<td colspan="16" align="right">Total</td>
								<td id="" width="100" align="right"><? $grand_total+=$total_issue_qty; echo number_format($total_issue_qty,2); ?></td>
								<!-- <td id="" align="right" width="100"><? //$grand_sales_total+=$total_sales_issue_qty; echo number_format($total_sales_issue_qty,2); ?></td> -->
								<td id=""  width="60">&nbsp;</td>
							</tfoot>
						</table>
					</div>				
					</table>
					<table width="1960" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table nsbreak" id="table_body">
						<tfoot>
							<tr>
								<th width="1800" colspan="16" align="right">Grand Total</th>
								<th width="100" align="right"><? echo number_format($grand_total,2); ?></th>
								<!-- <th width="100" align="right"><? //echo number_format($grand_sales_total,2); ?></th> -->
								<th width="60">&nbsp;</th>
							</tr>
						</tfoot>
					</table>
				</div>
				<?
			}
		}			
		
	}//Knit end
	if($cbo_report_type==2) // Woven Finish Start
	{
		
		if(str_replace("'","",$cbo_buyer_id)!="" && str_replace("'","",$cbo_buyer_id)!=0) $buyer_id_cond=" and a.buyer_id=$cbo_buyer_id";		
		$start_date=str_replace("'","",$txt_date_from);
		$end_date=str_replace("'","",$txt_date_to);
		//if( $date_from=="") $receive_date_trans=""; else $receive_date_trans= " and a.transfer_date <=".$txt_date_from."";

		if($start_date!="" && $end_date!="")
		{
			if($db_type==0)
			{
				$date_cond="and f.issue_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
				$date_cond2="and a.issue_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
			}
			else
			{
				$date_cond="and f.issue_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";	
				$date_cond2="and a.issue_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";	
			}
		}
		else
		{
			$date_cond="";
		}
			
		$duyeingCom_arr=return_library_array( "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id", "company_name"  );
		$duyeingSup_arr=return_library_array( "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id  and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name"  );
		$buyer_arr=return_library_array( "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name", "id", "buyer_name"  );
		$color_arr = return_library_array("select id, color_name from lib_color","id","color_name");
		//$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst","id","batch_no");

		$batch_sql=sql_select("select id, batch_no, booking_no from pro_batch_create_mst where status_active=1 and is_deleted=0");
		$batch_arr=array();
		foreach ($batch_sql as $row) 
		{
			$batch_arr[$row[csf('id')]]['batch_no']= $row[csf('batch_no')];
			$batch_arr[$row[csf('id')]]['booking_no']= $row[csf('booking_no')];
		}
		
		$item_description_arr=return_library_array( "select id, item_description from product_details_master where status_active=1 and is_deleted=0 ", "id", "item_description"  );
		$determination_sql=sql_select("select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1");

		$determination_arr=array();
		foreach ($determination_sql as $row) 
		{
			if($row[csf('construction')]!='')
			{
				$determination_arr[$row[csf('id')]]['desc']= $row[csf('construction')].", ";
			}
			$determination_arr[$row[csf('id')]]['desc'].= $composition[$d_row[csf('copmposition_id')]]." ".$d_row[csf('percent')]."% ";
		}
		unset($determination_sql);
		
		$order_sql=sql_select("select a.job_no, a.style_ref_no,b.po_number as po_no, b.id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1");
		$order_arr=array();
		foreach ($order_sql as $row) 
		{
			$order_arr[$row[csf('id')]]['job_no']= $row[csf('job_no')];
			$order_arr[$row[csf('id')]]['style_ref_no']= $row[csf('style_ref_no')];
			$order_arr[$row[csf('id')]]['po_number']= $row[csf('po_no')];
		}
		unset($order_sql);

	

		if($search_cond!='')
		{
			//e.gsm,e.width,
			$woven_issue_qry="select f.company_id,f.issue_number,f.issue_date,f.knit_dye_source as knitting_source,f.challan_no,f.buyer_id,f.booking_no,f.booking_id,d.color_id,g.unit_of_measure as uom,e.batch_id,g.detarmination_id as fabric_description_id,d.po_breakdown_id,d.prod_id ,sum(d.quantity) as quantity,a.job_no_prefix_num, a.job_no, a.style_ref_no,b.po_number as po_no 
			from wo_po_details_master a, wo_po_break_down b, inv_transaction c, order_wise_pro_details d,inv_wvn_finish_fab_iss_dtls e, inv_issue_master f,product_details_master g
			where a.job_no=b.job_no_mst and d.po_breakdown_id=b.id and c.id = d.trans_id and e.id=d.dtls_id and  f.id=e.mst_id and d.prod_id=g.id and e.prod_id=g.id  and d.entry_form in (19) and f.entry_form in (19) and c.item_category=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $date_cond $buyer_id_cond $search_cond and f.company_id=$cbo_company_id  group by  f.company_id,f.issue_number,f.issue_date,f.knit_dye_source,f.challan_no,f.buyer_id,f.booking_no,f.booking_id,d.color_id,g.unit_of_measure,e.batch_id,g.detarmination_id,d.po_breakdown_id,d.prod_id,a.job_no_prefix_num, a.job_no, a.style_ref_no,b.po_number";
		}
		else
		{
			/*$woven_issue_qry="select a.company_id,a.recv_number,a.receive_date,a.knitting_source,a.challan_no,a.buyer_id,a.booking_no,a.booking_id,b.color_id,b.gsm,b.width,b.uom,b.batch_id,b.fabric_description_id,d.po_breakdown_id,d.prod_id ,sum(d.quantity) as quantity    
			from  inv_receive_master a,pro_finish_fabric_rcv_dtls b,inv_transaction c,order_wise_pro_details d
			where a.id=b.mst_id and b.trans_id=c.id and c.id=d.trans_id and b.id=d.dtls_id and a.entry_form=17 and d.entry_form=17 and a.item_category=3 and a.status_active=1 and a.is_deleted=0 and a.company_id=$cbo_company_id $buyer_id_cond $date_cond2   group by a.company_id,a.recv_number,receive_date,a.knitting_source,a.challan_no,a.buyer_id,a.booking_no,a.booking_id,b.color_id,b.gsm,b.width,b.uom,b.batch_id,b.fabric_description_id,d.po_breakdown_id,d.prod_id" ;*/


			$woven_issue_qry="select a.company_id,a.issue_number,a.issue_date,a.knit_dye_source as knitting_source ,a.challan_no,a.buyer_id,a.booking_no,a.booking_id,d.color_id,e.unit_of_measure as uom,b.batch_id,e.detarmination_id as fabric_description_id,d.po_breakdown_id,d.prod_id ,sum(d.quantity) as quantity    
            from  inv_issue_master a,inv_wvn_finish_fab_iss_dtls b,inv_transaction c,order_wise_pro_details d,product_details_master e
            where a.id=b.mst_id and b.trans_id=c.id and c.id=d.trans_id and b.id=d.dtls_id and d.prod_id=e.id and a.entry_form=19 and a.item_category=3 and a.status_active=1 and a.is_deleted=0  and a.company_id=$cbo_company_id $buyer_id_cond $date_cond2   
            group by a.company_id,a.issue_number,issue_date,a.knit_dye_source
            ,a.challan_no,a.buyer_id,a.booking_no,a.booking_id,d.color_id,e.unit_of_measure
            ,b.batch_id,e.detarmination_id
            ,d.po_breakdown_id,d.prod_id";  
		}
		
		//echo $woven_issue_qry;die;
		
		$woven_issue_sql=sql_select($woven_issue_qry);

	 	
		?>
		
		
		<fieldset style="width:1890px;">
			<table cellpadding="0" cellspacing="0" width="1870">
				<tr class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="14" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="14" style="font-size:16px"><strong><? echo $duyeingCom_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
				</tr>
				<?
				if($start_date!="" && $end_date!="")
				{
					?>
					<tr  class="form_caption" style="border:none;">
						<td align="center" width="100%" colspan="14" style="font-size:14px"><strong> <? echo change_date_format(str_replace("'","",$start_date))." To ".change_date_format(str_replace("'","",$end_date)) ;?></strong></td>
					</tr>
					<?
				}
				?>
			</table>
			<table cellpadding="0" cellspacing="0" width="670">
				<tr class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="14" style="font-size:18px"><strong>Daily T-Shirt In-House Fabric Issue Summary</strong></td>
				</tr>
			</table>

			<table width="670" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">            	
				<thead>
					<tr>
						<th width="40">SL</th>
						<th width="80">Issue Date</th>
						<th width="100">Dyeing Source</th>
						<th width="100">In-House Company</th>
						<th width="100">Buyer</th>
						<th width="100">UOM</th>
						<th>Issue Qty</th>
					</tr>
					
					
				</thead>
			</table>
			<div style="width: 690px; max-height:350px; overflow-y:scroll;" id="scroll_body">
				<table width="670" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" > 

					<?
					$i=1;

					foreach ($woven_issue_sql as $row)
					{

						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
						
							<td width="40"><? echo $i; ?></td>
							<td width="80" align="center"><p><? echo change_date_format($row[csf('issue_date')]);  ?></p></td>
							<td width="100"><? echo $knitting_source[$row[csf('knitting_source')]]; ?></td>
							<td width="100"><? echo $duyeingCom_arr[$row[csf('company_id')]]; ?></td>
							<td width="100"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
							<td width="100"align="center"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
							<td align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
						</tr>
						<?	
						$i++;

						$grand_qnty_summary+=$row[csf('quantity')];



					}
					?>	
								
					<tr>
						<td colspan="6" align="right" style="font-size: 16px;"><strong>Grand Total : </strong></td>
						<td align="right" style="font-size: 16px;"><? echo number_format($grand_qnty_summary,2); ?></td>
					</tr>
				</table>
			</div>





			<table cellpadding="0" cellspacing="0" width="1870">
				<tr class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="14" style="font-size:18px"><strong>Daily T-Shirt In-House Fabric Issue Status</strong></td>
				</tr>
			</table>

			<table width="1870" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">            	
				<thead>	
					<tr>
						<th width="40">SL</th>
						<th width="80">Issue Date</th>
						<th width="120">System ID</th>
						<th width="100">Dyeing Source</th>
						<th width="100">In-House Company</th>
						<th width="100">Challan No</th>
						<th width="100">Buyer</th>
						<th width="120">Job No</th>
						<th width="100">F.Booking No</th>
						<th width="120">Style Ref.</th>
						<th width="100">Order No</th>
						<th width="100">Color</th>
						<th width="100">Batch No</th>
						<th width="100">Fabric Type</th>
						<th width="100">GSM</th>
						<th width="100">F.Dia</th>
						<th width="100">Issue Qty</th>
						<th>UOM</th>
					</tr>
					
					
				</thead>
			</table>
			<div style="width: 1890px; max-height:350px; overflow-y:scroll;" id="scroll_body">
				<table width="1870" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" > 

					<?
					$i=1;

					foreach ($woven_issue_sql as $row)
					{

						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
						
							<td width="40"><? echo $i; ?></td>
							<td width="80" align="center"><p><? echo change_date_format($row[csf('issue_date')]);  ?></p></td>
							<td width="120" align="center"><p><? echo $row[csf('issue_number')]; ?></p></td>
							<td width="100"><? echo $knitting_source[$row[csf('knitting_source')]]; ?></td>
							<td width="100"><? echo $duyeingCom_arr[$row[csf('company_id')]]; ?></td>
							<td width="100"><? echo $row[csf("challan_no")]; ?></td>
							<td width="100"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
							<td width="120" align="center"><? echo $order_arr[$row[csf('po_breakdown_id')]]['job_no']; ?></td>
							<td width="100"><? echo $row[csf('booking_no')]; ?></td>
							<td width="120"><p><? echo $order_arr[$row[csf('po_breakdown_id')]]['style_ref_no']; ?></p></td>
							<td width="100" align="center"><? echo $order_arr[$row[csf('po_breakdown_id')]]['po_number']; ?> </td>
							<td width="100"align="center"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td width="100"><? echo $batch_arr[$row[csf('batch_id')]]['batch_no']; ?></td>
							<td width="100"><?  echo $determination_arr[$row[csf('fabric_description_id')]]['desc']; ?></td>
							<td width="100" align="center"><? echo $row[csf('gsm')]; ?></td>
							<td width="100"align="center"><? echo $row[csf('width')]; ?></td>
							<td width="100"align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
							<td align="center"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
						</tr>
						<?	
						$i++;

						$grand_qnty+=$row[csf('quantity')];



					}
					?>	
								
					<tr>
						<td colspan="16" align="right" style="font-size: 16px;"><strong>Grand Total : </strong></td>
						<td align="right" style="font-size: 16px;"><? echo number_format($grand_qnty,2); ?></td>
					</tr>
				</table>
			</div>
			
		</fieldset>	
			
		<?
	}//woven end

	

	$html = ob_get_contents();
	ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
	foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
    //---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename**$report_type**$view_1**$view_2**$view_3**$view_4"; 
	exit();
}

if($action=="report_generate_sales") // Sales button, This button only for Sales Issue purpose. Customer: HAMS
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$cbo_report_type=str_replace("'","",$cbo_report_type);
	//$cbo_presentation=str_replace("'","",$cbo_presentation);
	$cbo_search_by=str_replace("'","",$cbo_search_by);
	$txt_search_comm=str_replace("'","",$txt_search_comm);
	$txt_file_no=str_replace("'","",$txt_file_no);
	$txt_ref_no=str_replace("'","",$txt_ref_no);
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_sewing_company=str_replace("'","",$cbo_sewing_company);
	$source=str_replace("'","",$cbo_source);
	$cbo_location=str_replace("'","",$cbo_location);
	$issue_purpose=str_replace("'","",$cbo_issue_purpose);
	$cbo_product_category=str_replace("'","",$cbo_product_category);
	//echo $cbo_report_type;die;
	if(str_replace("'","",$cbo_buyer_id)!="" && str_replace("'","",$cbo_buyer_id)!=0) $buyer_id_cond=" and f.buyer_id=$cbo_buyer_id";
	$job_no=str_replace("'","",$txt_job_no);
	$search_cond='';
	if($cbo_search_by==1)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and a.job_no_prefix_num in ($txt_search_comm) ";
	}
	else if($cbo_search_by==2)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and a.style_ref_no LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==3)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.po_number LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==4)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.file_no LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==5)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.grouping LIKE '%$txt_search_comm%'";
	}
	else
	{
		$search_cond.="";
	}
	
	$cbo_year_val=str_replace("'","",$cbo_year);
	$order_no=str_replace("'","",$txt_order_id);
	if(str_replace("'","",$txt_order_id)!="" && str_replace("'","",$txt_order_id)!=0) $order_id_cond_trans=" and c.id in ($order_no)";
	
	$start_date=str_replace("'","",$txt_date_from);
	$end_date=str_replace("'","",$txt_date_to);
	//if( $date_from=="") $issue_date_trans=""; else $receive_date_trans= " and a.transfer_date <=".$txt_date_from."";

	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and f.issue_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and f.issue_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";	
		}
	}
	else
	{
		$date_cond="";
	}
	if ($cbo_location ==0 ) $search_cond.=""; else $search_cond.=" and f.location_id=$cbo_location ";
	if ($cbo_product_category ==0 ) $search_cond.=""; else $search_cond.=" and a.product_category=$cbo_product_category ";
	if ($cbo_location ==0 ) $location_cond=""; else $location_cond=" and f.location_id=$cbo_location ";
	$source_cond = "";
	if ($source) {
		$source_cond=" and f.knit_dye_source=$source";
	}

	if ($issue_purpose==0) $issuePurposeCond=""; else $issuePurposeCond=" and f.issue_purpose=$issue_purpose";

	//echo $source_cond;
	ob_start();


	if($cbo_report_type==1) // Knit Finish Start
	{
		if($search_cond!='')
		{
			$sql_issue="SELECT a.job_no_prefix_num, a.job_no, a.style_ref_no,b.po_number as po_no, d.po_breakdown_id as po_id, d.quantity,f.id,f.issue_number, f.challan_no, f.company_id, f.issue_date, f.issue_purpose, f.buyer_id, f.sample_type, f.knit_dye_source as knitting_source, f.knit_dye_company, f.cutt_req_no, f.location_id ,e.id as dtlsId, e.mst_id, e.trans_id, e.batch_id, e.prod_id,e.sample_type, e.issue_qnty,e.fabric_shade, e.store_id, e.no_of_roll, e.remarks, e.rack_no, e.shelf_no,e.floor,e.room, e.cutting_unit, e.order_id, e.order_save_string, e.roll_save_string, e.body_part_id,e.gmt_item_id,e.uom,g.product_name_details, g.color ,g.dia_width as width ,g.gsm 
			from wo_po_details_master a, wo_po_break_down b, inv_transaction c, order_wise_pro_details d, inv_issue_master f, inv_finish_fabric_issue_dtls e , product_details_master g 
			where a.job_no=b.job_no_mst and d.po_breakdown_id=b.id and c.id = d.trans_id and e.id=d.dtls_id and f.id=e.mst_id and f.id=e.mst_id and e.prod_id=g.id and d.entry_form in (18) and c.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0  $date_cond $buyer_id_cond $search_cond $source_cond and f.company_id=$cbo_company_id and f.issue_purpose=3 $issuePurposeCond
			union all
			SELECT 0 as job_no_prefix_num, null as job_no, null as style_ref_no, null as po_no, null as po_id, e.issue_qnty as quantity , f.id,f.issue_number, f.challan_no, f.company_id, f.issue_date, f.issue_purpose, f.buyer_id, f.sample_type, f.knit_dye_source as knitting_source, f.knit_dye_company, f.cutt_req_no, f.location_id ,e.id as dtlsId, e.mst_id, e.trans_id, e.batch_id, e.prod_id,e.sample_type, e.issue_qnty,e.fabric_shade, e.store_id, e.no_of_roll, e.remarks, e.rack_no, e.shelf_no,e.floor,e.room, e.cutting_unit, e.order_id, e.order_save_string, e.roll_save_string, e.body_part_id,e.gmt_item_id,e.uom, g.product_name_details, g.color ,g.dia_width as width ,g.gsm
			from inv_issue_master f, inv_finish_fabric_issue_dtls e, product_details_master g
			where f.id=e.mst_id and  e.prod_id=g.id and f.entry_form=18 and f.status_active=1 and e.status_active = 1 and e.is_deleted = 0 and f.company_id=$cbo_company_id and f.issue_purpose=3 $date_cond $buyer_id_cond $source_cond $location_cond $issuePurposeCond and f.sample_type !=0";
		}
		else
		{
			$sql_issue="SELECT f.id,f.issue_number, f.challan_no, f.company_id, f.issue_date, f.issue_purpose, f.buyer_id, f.sample_type, f.knit_dye_source as knitting_source, f.knit_dye_company, f.cutt_req_no,f.location_id ,e.id as dtlsId, e.mst_id, e.trans_id, e.batch_id, e.prod_id,e.sample_type, e.issue_qnty as quantity,e.fabric_shade, e.store_id, e.no_of_roll, e.remarks, e.rack_no, e.shelf_no,e.floor,e.room, e.cutting_unit, e.order_id, e.order_save_string, e.roll_save_string, e.body_part_id,e.gmt_item_id,e.uom,g.product_name_details, g.color ,g.dia_width as width ,g.gsm 
			from inv_issue_master f, inv_finish_fabric_issue_dtls e, product_details_master g 
			where f.id=e.mst_id and e.prod_id=g.id and f.status_active=1 and e.status_active = 1 and e.is_deleted = 0 and g.status_active = 1 and f.company_id=$cbo_company_id and f.issue_purpose=3 $date_cond $buyer_id_cond $source_cond $issuePurposeCond order by e.id";
		}
	 	//echo $sql_issue; //die;
		$sql_issue_res=sql_select($sql_issue); $data_arr=array(); $data_summary_arr=array();
		foreach ($sql_issue_res as $row)
		{
			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['id']=$row[csf('id')];
			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['issue_date']=change_date_format($row[csf('issue_date')]);
			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['issue_number']=$row[csf('issue_number')];
			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['knitting_source']=$row[csf('knitting_source')];
			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['company_id']=$row[csf('company_id')];
			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['knitting_company']=$row[csf('knitting_company')];
			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['challan_no']=$row[csf('challan_no')];
			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['buyer_id']=$row[csf('buyer_id')];
			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['issue_purpose']=$row[csf('issue_purpose')];
			
			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['batch_id']=$row[csf('batch_id')];
			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['product_name_details']=$row[csf('product_name_details')];
			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['color']=$row[csf('color')];
			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['prod_id']=$row[csf('prod_id')];
			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['gsm']=$row[csf('gsm')];
			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['width']=$row[csf('width')];
			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['issue_qnty']=$row[csf('issue_qnty')];

			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['uom']=$row[csf('uom')];
			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['booking_id']=$row[csf('booking_id')];
			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['booking_no']=$row[csf('booking_no')];
			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['order_id']=$row[csf('order_id')];
			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['knit_dye_company']=$row[csf('knit_dye_company')];

			
			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['job_no'].=$row[csf('job_no')].",";
			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['style_ref_no'].=$row[csf('style_ref_no')].",";
			$data_arr[$row[csf('knitting_source')]][$row[csf('id')]][$row[csf('batch_id')]][$row[csf('dtlsId')]][$row[csf('quantity')]]['po_number'].=$row[csf('po_no')].",";
			

			if($row[csf('issue_purpose')]==1)
			{
				$data_summary_arr[$row[csf('knitting_source')]][$row[csf('issue_date')]][$row[csf('knit_dye_company')]][$row[csf('buyer_id')]][$row[csf('uom')]]['issue_qty']+= $row[csf('quantity')];

				$data_summary_arr[$row[csf('knitting_source')]][$row[csf('issue_date')]][$row[csf('knit_dye_company')]][$row[csf('buyer_id')]][$row[csf('uom')]]['sales_qty']+= $row[csf('quantity')];
			}
			else
			{
				$data_summary_arr[$row[csf('knitting_source')]][$row[csf('issue_date')]][$row[csf('knit_dye_company')]][$row[csf('buyer_id')]][$row[csf('uom')]]['issue_qty']+= $row[csf('quantity')];

				//$data_summary_arr[$row[csf('knitting_source')]][$row[csf('issue_date')]][$row[csf('knitting_company')]][$row[csf('buyer_id')]][$row[csf('uom')]]['sales_qty']+= 0;
			}
			
			/*echo "=====================================";
			echo "<pre>";
			print_r($data_summary_arr);	*/
		}
		//echo "<pre>";
		//print_r($data_summary_arr);	

		unset($sql_rcv_res);
		$duyeingCom_arr=return_library_array( "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id", "company_name"  );
		$duyeingSup_arr=return_library_array( "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id  and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name"  );
		$buyer_arr=return_library_array( "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name", "id", "buyer_name"  );
		$color_arr = return_library_array("select id, color_name from lib_color","id","color_name");
		$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst","id","batch_no");

		$batch_sql=sql_select("select id, batch_no, booking_no from pro_batch_create_mst where status_active=1 and is_deleted=0");
		$batch_arr=array();
		foreach ($batch_sql as $row) 
		{
			$batch_arr[$row[csf('id')]]['batch_no']= $row[csf('batch_no')];
			$batch_arr[$row[csf('id')]]['booking_no']= $row[csf('booking_no')];
		}
		
		$order_sql=sql_select("select a.job_no, a.style_ref_no,b.po_number as po_no, b.id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1");
		$order_arr=array();
		foreach ($order_sql as $row) 
		{
			$order_arr[$row[csf('id')]]['job_no']= $row[csf('job_no')];
			$order_arr[$row[csf('id')]]['style_ref_no']= $row[csf('style_ref_no')];
			$order_arr[$row[csf('id')]]['po_number']= $row[csf('po_no')];
		}
		
		unset($order_sql);
		?>
		
		<style type="text/css">
		.nsbreak{word-break: break-all;}
		</style>
		<table cellpadding="0" cellspacing="0" width="1860">
			<tr class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="14" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
			</tr>
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="14" style="font-size:16px"><strong><? echo $duyeingCom_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
			</tr>
			<?
			if($start_date!="" && $end_date!="")
			{
				?>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="14" style="font-size:14px"><strong> <? echo change_date_format(str_replace("'","",$start_date))." To ".change_date_format(str_replace("'","",$end_date)) ;?></strong></td>
				</tr>
				<?
			}
			?>
		</table>
		<?
		// ========================= Details Part Start ========================
		$i=1; $k=1;  $grand_total=0; $grand_sales_total=0; 
		foreach ($data_arr  as $knitting_source_id=>$knitting_source_val)
		{
			$total_issue_qty=0;
			if($knitting_source_id==0)
			{
				?>
				<div style="padding: 10px;">
					<table width="1910" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" > 
						<thead>
							<tr>
								<th colspan="18">Daily T-Shirt In-House Fabric Issue Status</th>
							</tr>
							<tr>
								<th width="30" rowspan="2">SL</th>
								<th width="70" rowspan="2">Issue Date</th>
								<th width="120" rowspan="2">System ID</th>
								<th width="145" rowspan="2">Company</th> 
								<th width="80" rowspan="2">Challan No</th>
								<th width="120" rowspan="2">Buyer</th>

								<th width="100" rowspan="2">Job No</th>
								<th width="120" rowspan="2">F.Booking No</th>
								<th width="180" rowspan="2">Style Ref.</th>
								<th width="200" rowspan="2">Order No</th>

								<th width="100" rowspan="2">Color</th>
								<th width="100" rowspan="2">Batch No</th>
								<th width="150" rowspan="2">Fabric Type</th>
								<th width="55" rowspan="2">GSM</th>
								<th width="60" rowspan="2">F.Dia</th>
								<th width="100" rowspan="2">Purpose</th>

								<th width="100" rowspan="2">Issue Qty</th>
								<th rowspan="2">Uom</th>
							</tr>
						</thead>
					</table>
					<div style="width:1930px; max-height:300px; overflow-y:scroll;" id="scroll_body2">
						<table width="1910" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table nsbreak" id="table_body">
							<? $view_1=1;$view_2=2;$view_3=3;$view_4=4;
							foreach ($knitting_source_val  as $mst_id=>$mst_id_val)
							{
								foreach ($mst_id_val  as $batch_id=>$batch_id_val)
								{
									foreach ($batch_id_val  as $dtls_id=>$dtls_id_val)
									{
										foreach ($dtls_id_val  as $issue_qnty=>$val)
										{
											$jobNos=''; $styleRefNos=''; $poNumbers='';
											if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
											?>
											<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trDtl1_<? echo $i;?>','<? echo $bgcolor;?>')" id="trDtl1_<? echo $i;?>">
												<td width="30"><? echo $i; ?></td>
												<td width="70"><p><? echo $val['issue_date']; ?></p></td>
												<td width="120"><p><? echo $val['issue_number']; ?></p></td>
												<td width="145"><p><? echo $duyeingCom_arr[$val['company_id']]; ?></p></td>
												<td width="80"><p><? echo $val['challan_no']; ?></p></td>
												<td width="120"><p><? echo $buyer_arr[$val['buyer_id']]; ?></p></td>
												<td width="100"><p><?
												if($val['order_id']!='')
												{
													$order_ids=explode(",",$val['order_id']); $job_nos=''; $style_ref_nos=''; $po_numbers='';
													foreach ($order_ids as $value) 
													{
														$job_nos.=$order_arr[$value]['job_no'].",";
														$style_ref_nos.=$order_arr[$value]['style_ref_no'].",";
														$po_numbers.=$order_arr[$value]['po_number'].",";
													}
													$jobNos=implode(",",array_unique(explode(",",chop($job_nos,","))));
													$styleRefNos=implode(",",array_unique(explode(",",chop($style_ref_nos,","))));
													$poNumbers=implode(",",array_unique(explode(",",chop($po_numbers,","))));
												}												
												echo $jobNos; ?></p></td>
												<td width="120"><p><? echo $batch_arr[$batch_id]['booking_no']; ?></p></td>
												<td width="180"><p><? echo $styleRefNos; ?></p></td>
												<td width="200"><p><? echo $poNumbers; ?></p></td>
												<td width="100"><p><? echo $color_arr[$val['color']]; ?></p></td>
												<td width="100"><p><? echo $batch_arr[$batch_id]['batch_no']; ?></p></td>
												<td width="150"><p><? echo $val['product_name_details']; ?></p></td>
												<td width="55"><p><? echo $val['gsm']; ?></p></td>
												<td width="60"><p><? echo $val['width']; ?></p></td>
												<td width="100"><p><? echo $yarn_issue_purpose[$val["issue_purpose"]]; ?></p></td>
												<td width="100" align="right"><? $total_issue_qty +=$issue_qnty; echo number_format($issue_qnty,2); ?></td>
												<td width=""><? echo $unit_of_measurement[$val['uom']]; ?></td>
											</tr>
											<?
											$i++;
										}
									}
								}	
							}
							?>
						</table>
					</div>
					<table width="1910" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
						<tfoot class="tbl_bottom">
							<td width="30"></td>
							<td width="70"></td>
							<td width="120"></td>
							<td width="145"></td> 
							<td width="80"></td>
							<td width="120"></td>

							<td width="100"></td>
							<td width="120"></td>
							<td width="180"></td>
							<td width="200"></td>

							<td width="100"></td>
							<td width="100"></td>
							<td width="150"></td>
							<td width="55"></td>
							<td width="60"></td>
							<td width="100" align="right">Total</td>

							<td width="100" align="right"><? $grand_total+=$total_issue_qty; echo number_format($total_issue_qty,2); ?></td>
							<td></th>
						</tfoot>
					</table>					
				</div>
				<?
			}
		}
		// ========================= Details Part End ==========================
	}//Knit end	

	$html = ob_get_contents();
	ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
	foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
    //---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename**$report_type**$view_1**$view_2**$view_3**$view_4"; 
	exit();
}
?>