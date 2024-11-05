<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action']; 

if ($action=="load_drop_down_buyer")
{ 
	list($company,$type)=explode("_",$data);
	if($type==1)
	{
		echo create_drop_down( "cbo_customer_name", 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $company, "$load_function");
	}
	else
	{
		echo create_drop_down( "cbo_customer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", "", "" );
	}	
	exit();	 
} 

if ($action=="load_drop_down_member")
{
	$data=explode("_",$data);
	$sql="select b.id,b.team_member_name  from lib_marketing_team a, lib_mkt_team_member_info b where a.id=b.team_id and   a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.project_type=3 and team_id='$data[0]'";
	echo create_drop_down( "cbo_team_member", 100, $sql,"id,team_member_name", 1, "-- Select Member --", $selected, "" );	
	exit();
}

if($action=="generate_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$cbo_company_id=str_replace("'","", $cbo_company_id);
	$cbo_customer_source=str_replace("'","", $cbo_customer_source);
	$cbo_customer_name=str_replace("'","", $cbo_customer_name);
	$txt_order_no=str_replace("'","", $txt_order_no);
	$internal_no=str_replace("'","", $txt_internal_no);
	$cbo_section_id=str_replace("'","", $cbo_section_id);
	$cbo_sub_section_id=str_replace("'","", $cbo_sub_section_id);
	$txt_item_description=str_replace("'","", $txt_item_description);
	$cbo_date_category=str_replace("'","", $cbo_date_category);
	$txt_date_from=str_replace("'","", $txt_date_from);
	$txt_date_to=str_replace("'","", $txt_date_to);
	$cbo_delivery_status=str_replace("'","", $cbo_delivery_status);
	$cbo_customer_buyer=str_replace("'","", $cbo_customer_buyer);
	$cbo_team_leader=str_replace("'","", $cbo_team_leader);
	$cbo_team_member=str_replace("'","", $cbo_team_member);


	if($cbo_delivery_status== 0){$delivery_status_con="";}
	else{
		$delivery_status_con=" and c.delivery_status=$cbo_delivery_status and b.delivery_status=$cbo_delivery_status";
	}
		
	if($db_type==0) 
	{
		$txt_date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
		$txt_date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
	}
	else if($db_type==2) 
	{
		$txt_date_from=change_date_format($txt_date_from,'','',1);
		$txt_date_to=change_date_format($txt_date_to,'','',1);
	}
	
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0 ","id","buyer_name");
	$trimsGroupArr = return_library_array("select id, item_name from lib_item_group where item_category=4 and status_active=1","id","item_name");
	$colorNameArr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	//$leaderArr = return_library_array("select id, team_leader_name from lib_marketing_team where status_active=1 and is_deleted=0 and project_type=3","id","team_leader_name");
	//$memberArr = return_library_array("select id, team_member_name from lib_mkt_team_member_info","id","team_member_name");

	$currency_rate=return_field_value( "conversion_rate", "currency_conversion_rate", "currency=2 and status_active=1  and id in(select max(id) from currency_conversion_rate where currency=2 and is_deleted=0 and status_active=1)" , "conversion_rate" );
	
	if($txt_date_from!="" and $txt_date_to!=""){	
		if($cbo_date_category==1){$where_con.=" and a.receive_date between '$txt_date_from' and '$txt_date_to'";}	
		else if($cbo_date_category==2){$where_con.=" and a.delivery_date between '$txt_date_from' and '$txt_date_to'";}
	}
	if($cbo_section_id){$where_con.=" and b.section='$cbo_section_id'";} 
	if($cbo_sub_section_id){$where_con.=" and b.sub_section='$cbo_sub_section_id'";} 
	if($cbo_customer_source){$where_con.=" and a.within_group='$cbo_customer_source'";} 
	if($cbo_customer_name){$where_con.=" and a.party_id='$cbo_customer_name'";} 
	if($cbo_customer_buyer){$where_con.=" and b.buyer_buyer='$cbo_customer_buyer'";} 
	if($cbo_team_leader){$where_con.=" and a.team_leader='$cbo_team_leader'";} 
	if($cbo_team_member){$where_con.=" and a.team_member='$cbo_team_member'";} 
	
	
	//echo $internal_no;
 	$buyer_po_id_cond = '';
    if($cbo_customer_source==1 || $cbo_customer_source==0)
    {
		if($internal_no !="") $internal_no_cond = " and grouping like('%$internal_no%')";
		
		$buyer_po_arr=array();
		$buyer_po_id_arr=array();
		 $po_sql ="Select id,grouping from  wo_po_break_down  where is_deleted=0 and status_active=1 $internal_no_cond "; 
		$po_sql_res=sql_select($po_sql);
		foreach ($po_sql_res as $row)
		{
			$buyer_po_arr[$row[csf("id")]]['grouping']=$row[csf("grouping")];
			$buyer_po_id_arr[]=$row[csf("id")];
		}
		unset($po_sql_res);
        //$buyer_po_lib = return_library_array("select id, id as id2 from wo_po_break_down where grouping like '%$internal_no%'",'id','id2');
		if($internal_no !="")
		{
			$buyer_po_id = implode(",", $buyer_po_id_arr);
			if ($buyer_po_id=="")
			{
				echo "Not Found."; die;
			}
       	 	if($buyer_po_id !="") $buyer_po_id_cond = " and b.buyer_po_id in($buyer_po_id)";
		}
    }
	//echo $buyer_po_id_cond; die;
	
    $sqlBreak_result =sql_select("select  a.id, a.mst_id, a.order_id, a.job_no_mst, a.book_con_dtls_id, a.product_id, a.description, a.specification, a.pcs_unit, a.cons_qty, a.process_loss, a.process_loss_qty, a.req_qty, a.remarks,b.unit_of_measure as unit from trims_job_card_breakdown a , product_details_master b where a.product_id=b.id and a.status_active=1 and a.is_deleted=0");
			
	$break_arr=array(); $break_arr_summery=array();
	foreach($sqlBreak_result as $row)
	{
		$break_arr[$row[csf('mst_id')]]['info'].=$row[csf('description')]."_".$row[csf('pcs_unit')]."_".$row[csf('unit')]."_".$row[csf('req_qty')]."_".$row[csf('process_loss')]."_".$row[csf('remarks')]."_".$row[csf('product_id')]."**";
		$break_arr_summery[$row[csf('description')]][$row[csf('unit')]][$row[csf('product_id')]]+=$row[csf('req_qty')];
	}

	if($txt_order_no !="") $order_no_cond = " and b.order_no like('%$txt_order_no%')";
	
	//print_r($buyer_po_arr); die;
	//$sql_chcek = sql_select("select id from  inv_issue_master	where status_active=1 and is_deleted=0 $entry_form_cond  and company_id=".$company."   and id not in(select poid from tmp_poid where userid=$user_id) $date_cond  $iss_number_cond   $is_approved_cond order by id  desc");

	  /*  $trims_order_sql="select a.id,a.subcon_job,a.order_no as cust_order_no,a.party_id,a.within_group,a.trims_ref,b.section,b.sub_section,b.item_group,b.buyer_buyer,b.job_no_mst,b.order_no,b.booked_uom,b.order_uom,b.buyer_po_no,b.buyer_po_id,c.id as break_id,c.description,c.order_id,c.item_id,c.color_id,c.size_id ,c.qnty,c.rate ,c.amount,c.booked_qty,e.job_no_mst as job_card_no_mst,f.product_id, f.description as row_desc, f.specification, f.unit, f.pcs_unit, f.cons_qty, f.req_qty,d.id as job_id from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c,trims_job_card_mst d,trims_job_card_breakdown f, trims_job_card_dtls e where a.subcon_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id and a.id=d.received_id and d.id=e.mst_id and e.id=f.mst_id and a.entry_form=255 and a.company_id =$cbo_company_id  $buyer_po_id_cond  $order_no_cond $where_con $delivery_status_con 
	and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and f.is_deleted=0 and f.status_active=1 group by a.id,a.subcon_job,a.order_no ,a.party_id,a.within_group,a.trims_ref,b.section,b.sub_section,b.item_group,b.buyer_buyer,b.job_no_mst,b.order_no,b.booked_uom,b.order_uom,b.buyer_po_no,b.buyer_po_id,c.id,c.description,c.order_id,c.item_id,c.color_id,c.size_id,c.qnty,c.rate,c.amount,c.booked_qty,e.job_no_mst,f.description,f.specification,f.unit,f.pcs_unit,f.cons_qty,f.req_qty,d.id,f.product_id ";*/
	
	  $trims_order_sql="SELECT a.id,a.subcon_job,a.order_no as cust_order_no,a.party_id,a.within_group,a.trims_ref,b.section,b.sub_section,b.item_group,b.buyer_buyer,b.job_no_mst,b.order_no,b.booked_uom,b.booked_conv_fac,b.order_uom,b.buyer_po_no,b.buyer_po_id,c.id as break_id,c.description,c.order_id,c.item_id,c.size_id ,c.qnty,c.rate ,c.amount,c.booked_qty,e.job_no_mst as job_card_no_mst,f.product_id, f.description as row_desc, f.specification, f.unit, f.pcs_unit, f.cons_qty, f.req_qty,d.id as job_id from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c,trims_job_card_mst d,trims_job_card_breakdown f, trims_job_card_dtls e where a.subcon_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id and a.id=d.received_id and d.id=e.mst_id and c.description=e.item_description and e.id=f.mst_id and a.entry_form=255 and a.company_id =$cbo_company_id  $buyer_po_id_cond  $order_no_cond $where_con $delivery_status_con 
	and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and f.is_deleted=0 and f.status_active=1 group by a.id,a.subcon_job,a.order_no ,a.party_id,a.within_group,a.trims_ref,b.section,b.sub_section,b.item_group,b.buyer_buyer,b.job_no_mst,b.order_no,b.booked_uom,b.booked_conv_fac,b.order_uom,b.buyer_po_no,b.buyer_po_id,c.id,c.description,c.order_id,c.item_id,c.size_id,c.qnty,c.rate,c.amount,c.booked_qty,e.job_no_mst,f.description,f.specification,f.unit,f.pcs_unit,f.cons_qty,f.req_qty,d.id,f.product_id ";
	
	//,e.job_no_mst as job_card_no_mst,f.product_id, f.description as row_desc, f.specification, f.unit, f.pcs_unit, f.cons_qty, f.req_qty,d.id as job_id
	
	
	 /*$trims_order_sql="select a.id,a.subcon_job,a.order_no as cust_order_no,a.party_id,a.within_group,a.trims_ref,b.section,b.sub_section,b.item_group,b.buyer_buyer,b.job_no_mst,b.order_no,b.booked_uom,b.order_uom,b.buyer_po_no,b.buyer_po_id,c.id as break_id,c.description,c.order_id,c.item_id,c.color_id,c.size_id ,c.qnty,c.rate ,c.amount,c.booked_qty,f.specification as row_desc  from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c ,trims_job_card_mst d, trims_job_card_dtls e,trims_job_card_breakdown f where a.subcon_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id    and a.entry_form=255 and a.id=d.received_id  and d.id=e.mst_id and e.id=f.mst_id  and a.company_id =$cbo_company_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $buyer_po_id_cond  $order_no_cond $where_con $delivery_status_con group by a.id,a.subcon_job,a.order_no ,a.party_id,a.within_group,a.trims_ref,b.section,b.sub_section,b.item_group,b.buyer_buyer,b.job_no_mst,b.order_no,b.booked_uom,b.order_uom,b.buyer_po_no,b.buyer_po_id,c.id ,c.description,c.order_id,c.item_id,c.color_id,c.size_id ,c.qnty,c.rate ,c.amount,c.booked_qty,f.specification";*/
	
	
	
	
	$trims_receive_id_arr=array();
	$trims_data_arr=array();	
	$result_trims_order_sql = sql_select($trims_order_sql);
	foreach($result_trims_order_sql as $row)
	{
	 

		$key=$row[csf("id")].'*'.$row[csf("order_id")].'*'.$row[csf("item_group")].'*'.$row[csf("item_id")].'*'.$row[csf("description")].'*'.$row[csf("booked_uom")];
		
		$trims_data_arr[$key][qnty]+=$row[csf("qnty")];
		$trims_data_arr[$key][amount]+=$row[csf("amount")];
		$trims_data_arr[$key][booked_qty]+=$row[csf("booked_qty")];
		$trims_data_arr[$key][break_ids].=$row[csf("break_id")].',';
		$trims_data_arr[$key][buyer_po_no].=$row[csf("buyer_po_no")].',';

		$buyer_po_no=chop(implode(',',array_unique(explode(",",$trims_data_arr[$key][buyer_po_no]))),',');
		$order_rate=$trims_data_arr[$key][amount]/$trims_data_arr[$key][qnty];

		$date_array[$row[csf("id")]][$row[csf("order_id")]][$row[csf("item_group")]][$row[csf("item_id")]][$row[csf("description")]][$row[csf("booked_uom")]][$row[csf("job_id")]][$row[csf("row_desc")]][$row[csf("unit")]]['subcon_job']=$row[csf("subcon_job")];
		$date_array[$row[csf("id")]][$row[csf("order_id")]][$row[csf("item_group")]][$row[csf("item_id")]][$row[csf("description")]][$row[csf("booked_uom")]][$row[csf("job_id")]][$row[csf("row_desc")]][$row[csf("unit")]]['item_group']=$row[csf("item_group")];
		$date_array[$row[csf("id")]][$row[csf("order_id")]][$row[csf("item_group")]][$row[csf("item_id")]][$row[csf("description")]][$row[csf("booked_uom")]][$row[csf("job_id")]][$row[csf("row_desc")]][$row[csf("unit")]]['order_qty'] =$row[csf("qnty")];
		$date_array[$row[csf("id")]][$row[csf("order_id")]][$row[csf("item_group")]][$row[csf("item_id")]][$row[csf("description")]][$row[csf("booked_uom")]][$row[csf("job_id")]][$row[csf("row_desc")]][$row[csf("unit")]]['rate'] +=$row[csf("rate")];
		$date_array[$row[csf("id")]][$row[csf("order_id")]][$row[csf("item_group")]][$row[csf("item_id")]][$row[csf("description")]][$row[csf("booked_uom")]][$row[csf("job_id")]][$row[csf("row_desc")]][$row[csf("unit")]]['booked_qty'] +=$row[csf("booked_qty")];
		$date_array[$row[csf("id")]][$row[csf("order_id")]][$row[csf("item_group")]][$row[csf("item_id")]][$row[csf("description")]][$row[csf("booked_uom")]][$row[csf("job_id")]][$row[csf("row_desc")]][$row[csf("unit")]]['cust_order_no']=$row[csf("cust_order_no")];
		$date_array[$row[csf("id")]][$row[csf("order_id")]][$row[csf("item_group")]][$row[csf("item_id")]][$row[csf("description")]][$row[csf("booked_uom")]][$row[csf("job_id")]][$row[csf("row_desc")]][$row[csf("unit")]]['party_id']=$row[csf("party_id")];
		$date_array[$row[csf("id")]][$row[csf("order_id")]][$row[csf("item_group")]][$row[csf("item_id")]][$row[csf("description")]][$row[csf("booked_uom")]][$row[csf("job_id")]][$row[csf("row_desc")]][$row[csf("unit")]]['within_group']=$row[csf("within_group")];
		$date_array[$row[csf("id")]][$row[csf("order_id")]][$row[csf("item_group")]][$row[csf("item_id")]][$row[csf("description")]][$row[csf("booked_uom")]][$row[csf("job_id")]][$row[csf("row_desc")]][$row[csf("unit")]]['buyer_buyer']=$row[csf("buyer_buyer")];
		$date_array[$row[csf("id")]][$row[csf("order_id")]][$row[csf("item_group")]][$row[csf("item_id")]][$row[csf("description")]][$row[csf("booked_uom")]][$row[csf("job_id")]][$row[csf("row_desc")]][$row[csf("unit")]]['booked_uom']=$row[csf("booked_uom")];
		$date_array[$row[csf("id")]][$row[csf("order_id")]][$row[csf("item_group")]][$row[csf("item_id")]][$row[csf("description")]][$row[csf("booked_uom")]][$row[csf("job_id")]][$row[csf("row_desc")]][$row[csf("unit")]]['booked_conv_fac']=$row[csf("booked_conv_fac")];
		$date_array[$row[csf("id")]][$row[csf("order_id")]][$row[csf("item_group")]][$row[csf("item_id")]][$row[csf("description")]][$row[csf("booked_uom")]][$row[csf("job_id")]][$row[csf("row_desc")]][$row[csf("unit")]]['buyer_po_id']=$row[csf("buyer_po_id")];
		$date_array[$row[csf("id")]][$row[csf("order_id")]][$row[csf("item_group")]][$row[csf("item_id")]][$row[csf("description")]][$row[csf("booked_uom")]][$row[csf("job_id")]][$row[csf("row_desc")]][$row[csf("unit")]]['order_uom']=$row[csf("order_uom")];
		$date_array[$row[csf("id")]][$row[csf("order_id")]][$row[csf("item_group")]][$row[csf("item_id")]][$row[csf("description")]][$row[csf("booked_uom")]][$row[csf("job_id")]][$row[csf("row_desc")]][$row[csf("unit")]]['req_qty'] +=$row[csf("req_qty")]*$row[csf("booked_qty")];// $row[csf("req_qty")]*$row[csf("qnty")]; 

		$date_array[$row[csf("id")]][$row[csf("order_id")]][$row[csf("item_group")]][$row[csf("item_id")]][$row[csf("description")]][$row[csf("booked_uom")]][$row[csf("job_id")]][$row[csf("row_desc")]][$row[csf("unit")]]['section_id'] =$row[csf("section")]; 
		$date_array[$row[csf("id")]][$row[csf("order_id")]][$row[csf("item_group")]][$row[csf("item_id")]][$row[csf("description")]][$row[csf("booked_uom")]][$row[csf("job_id")]][$row[csf("row_desc")]][$row[csf("unit")]]['sub_section'] =$row[csf("sub_section")]; 
		$date_array[$row[csf("id")]][$row[csf("order_id")]][$row[csf("item_group")]][$row[csf("item_id")]][$row[csf("description")]][$row[csf("booked_uom")]][$row[csf("job_id")]][$row[csf("row_desc")]][$row[csf("unit")]]['size_id'] =$row[csf("size_id")]; 
		$date_array[$row[csf("id")]][$row[csf("order_id")]][$row[csf("item_group")]][$row[csf("item_id")]][$row[csf("description")]][$row[csf("booked_uom")]][$row[csf("job_id")]][$row[csf("row_desc")]][$row[csf("unit")]]['product_id'] =$row[csf("product_id")]; 
		
		$date_array[$row[csf("id")]][$row[csf("order_id")]][$row[csf("item_group")]][$row[csf("item_id")]][$row[csf("description")]][$row[csf("booked_uom")]][$row[csf("job_id")]][$row[csf("row_desc")]][$row[csf("unit")]]['trims_ref'] =$row[csf("trims_ref")];
		$date_array[$row[csf("id")]][$row[csf("order_id")]][$row[csf("item_group")]][$row[csf("item_id")]][$row[csf("description")]][$row[csf("booked_uom")]][$row[csf("job_id")]][$row[csf("row_desc")]][$row[csf("unit")]]['job_card_no_mst'] =$row[csf("job_card_no_mst")];
		
		$trims_receive_id_arr[$row[csf("id")]]=$row[csf("id")];
		$trims_job_id_arr[$row[csf("job_id")]]=$row[csf("job_id")];
	}
	
	
	$trims_production_sql="select a.section_id,c.sub_section,a.received_id,a.job_id,b.job_dtls_id,c.id as order_receive_dtls_id,c.item_description,c.size_id,c.uom,c.job_quantity,b.production_qty,b.qc_qty  from trims_production_mst a,trims_production_dtls b,trims_job_card_dtls c where a.id=b.mst_id and c.id=b.job_dtls_id $trimsjobid_cond $trimsreceiveid_cond and a.entry_form=269
	and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0"; 
	
	$trims_production_data_arr=array();	
	$result_trims_production_sql = sql_select($trims_production_sql);
	foreach($result_trims_production_sql as $row)
	{
		//$key=$row[csf("received_id")].'*'.$row[csf("section_id")].'*'.$row[csf("sub_section")].'*'.$row[csf("item_description")].'*'.$row[csf("color_id")].'*'.$row[csf("size_id")].'*'.$row[csf("uom")];
		
		$key=$row[csf("received_id")].'*'.$row[csf("section_id")].'*'.$row[csf("sub_section")].'*'.$row[csf("item_description")].'*'.$row[csf("uom")];
		
		$trims_production_data_arr[$key][qc_qty]+=$row[csf("qc_qty")];
		//$trims_production_data_cost_arr[$key][qc_qty]+=$row[csf("qc_qty")]*$row[csf("avg_rate_per_unit")];
		$trims_production_data_arr[$key][job_quantity]+=$row[csf("job_quantity")];
	}	
		
	  $iss_sql= "SELECT d.job_id as job_card_id,b.prod_id,b.cons_quantity,e.requisition_qty,c.avg_rate_per_unit from inv_issue_master a, inv_transaction b, product_details_master c,trims_raw_mat_requisition_mst d,trims_raw_mat_requisition_dtls e where a.id=b.mst_id and b.transaction_type=2   and a.entry_form=265  and a.req_no=d.requisition_no and d.id=e.mst_id  and  b.prod_id=c.id  and  b.prod_id=e.product_id and  e.product_id=c.id and a.status_active=1 and b.status_active=1 and  d.status_active=1 and d.status_active=1 and  e.status_active=1 and e.status_active=1   group by d.job_id,b.prod_id,b.cons_quantity,c.avg_rate_per_unit,e.requisition_qty";
	$iss_data_array=sql_select($iss_sql); //and d.id=132
	foreach($iss_data_array as $row)
	{
		$issue_qty_arr[$row[csf("job_card_id")]][$row[csf("prod_id")]]+=$row[csf("cons_quantity")];
		$average_rate_arr[$row[csf("job_card_id")]][$row[csf("prod_id")]]=$row[csf("avg_rate_per_unit")];
		$requssition_qty_arr[$row[csf("job_card_id")]][$row[csf("prod_id")]]+=$row[csf("requisition_qty")];
	}
	//echo "<pre>"; 
	//print_r($date_array); die;
	$section_rowspan_arr=array(); $order_rowspan_arr=array(); $item_group_rowspan_arr=array();  $item_rowspan_arr=array(); $description_rowspan_arr=array(); $color_rowspan_arr=array();  $order_uom_rowspan_arr=array(); $job_id_rowspan_arr=array(); $row_desc_rowspan_arr=array();
	
	
    foreach($date_array as $rcv_id=> $rcv_id_data)
	{
		$rcv_id_rowspan=0;
		foreach($rcv_id_data as $order_id=> $order_id_data)
		{
			$order_id_rowspan=0;	
			foreach($order_id_data as $item_group_id=> $item_group_data)
			{
				$item_group_rowspan=0;	
				foreach($item_group_data as $item_id=> $item_id_data)
				{
					$item_rowspan=0;
					foreach($item_id_data as $description=> $description_data)
					{
						$description_rowspan=0;	
						foreach($description_data as $order_uom=> $order_uom_data)
						{
							// $color_id_rowspan=0;
							// foreach($color_id_data as $order_uom=> $order_uom_data)
							// {
								$order_uom_rowspan=0; 
								foreach($order_uom_data as $job_id=> $job_id_data)
								{
									$job_id_rowspan=0;
									foreach($job_id_data as $row_desc=> $row_desc_data)
									{
										$row_desc_rowspan=0;//$product_cost_rowspan_arr=0;
										foreach($row_desc_data as $unit=> $row)
										{
										  
									//	$key=$row[csf("received_id")].'*'.$row[csf("section_id")].'*'.$row[csf("sub_section")].'*'.$row[csf("item_description")].'*'.$row[csf("color_id")].'*'.$row[csf("uom")];
										 // $average_rate_arr[$row[csf("job_card_id")]][$row[csf("prod_id")]];
											
										// echo "mahbub"."==";	
											
											$order_id_rowspan++;
											$item_group_rowspan++;
											$item_rowspan++;
											$description_rowspan++;
										//	$color_id_rowspan++;
											$order_uom_rowspan++;
											$job_id_rowspan++;
											$row_desc_rowspan++;
											$rcv_id_rowspan++;
											//$product_cost_rowspan_arr++;
										}
										
 										$row_desc_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description][$order_uom][$job_id][$row_desc]=$row_desc_rowspan;
										
 									    $key=$rcv_id.'*'.$row['section_id'].'*'.$row['sub_section'].'*'.$description.'*'.$order_uom;
										$product_cost_qty_arr[$key]+=$trims_production_data_arr[$key][qc_qty]*$average_rate_arr[$job_id][$row['product_id']];
  									}
									$job_id_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description][$order_uom][$job_id]=$job_id_rowspan;
								}
								
								       
												
										 
								
								$order_uom_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description][$order_uom]=$order_uom_rowspan;
							// }
							// $color_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description][$color_id]=$color_id_rowspan;
						}
						$description_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description]=$description_rowspan;
					}
					$item_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id]=$item_rowspan;
				}
				$item_group_rowspan_arr[$rcv_id][$order_id][$item_group_id]=$item_group_rowspan;
			}
			$order_rowspan_arr[$rcv_id][$order_id]=$order_id_rowspan;
		}
		$rcv_rowspan_arr[$rcv_id]=$rcv_id_rowspan;
	}

//die;
	$trims_receive_id=implode(',',$trims_receive_id_arr);
	if($trims_receive_id!='')
	{
		$trimsreceiveid=chop($trims_receive_id,','); 
		$trimsreceiveid_cond="";
		$trimsreceive_ids=count(array_unique(explode(",",$trimsreceiveid)));
			if($db_type==2 && $trimsreceive_ids>1000)
			{
				$trimsreceiveid_cond=" and (";
				$trimsreceiveidArr=array_chunk(explode(",",$trimsreceiveid),999);
				foreach($trimsreceiveidArr as $ids)
				{
					$ids=implode(",",$ids);
					$trimsreceiveid_cond.=" a.received_id in($ids) or"; 
				}
				$trimsreceiveid_cond=chop($trimsreceiveid_cond,'or ');
				$trimsreceiveid_cond.=")";
			}
			else
			{
				if($trimsreceiveid!="")
				{
					$issue_ids=implode(",",array_unique(explode(",",$trimsreceiveid)));
					$trimsreceiveid_cond=" and a.received_id in($issue_ids)";
				}
				else { $trimsreceiveid_cond ="";}
			}
		
		}
		
	//Job-------------------------------	
	/*$trims_job_sql="select a.id,a.trims_job,a.received_no from trims_job_card_mst a where a.status_active=1 and a.is_deleted=0 $trimsreceiveid_cond "; 
	$trims_job_no_arr=array();	
	$result_trims_job_sql = sql_select($trims_job_sql);
	foreach($result_trims_job_sql as $row)
	{
		$trims_job_no_arr[$row[csf("received_no")]]=$row[csf("trims_job")];
		$trims_job_id_arr[$row[csf("id")]]=$row[csf("id")];
	}
		*/
	$trims_job_id=implode(',',$trims_job_id_arr);
	if($trims_job_id!='')
	{
		
		$trimsjobid=chop($trims_job_id,','); 
		$trimsjobid_cond="";
		$trimsjobids=count(array_unique(explode(",",$trimsjobid)));
		
		
		if($db_type==2 && $trimsjobids>1000)
		{
			$trimsjobid_cond=" and (";
			$trimsreceiveidArr=array_chunk(explode(",",$trimsjobid),999);
			foreach($trimsreceiveidArr as $jobids)
			{
				$jobids=implode(",",$jobids);
				$trimsjobid_cond.=" a.job_id in($jobids) or"; 
			}
			$trimsjobid_cond=chop($trimsjobid_cond,'or ');
			$trimsjobid_cond.=")";
		}
		else
		{
			if($trimsjobid!="")
			{
				$issue_ids=implode(",",array_unique(explode(",",$trimsjobid)));
				$trimsjobid_cond=" and a.job_id in($issue_ids)";
			}
			else { $trimsjobid_cond ="";}
		}
	
	}

	/*$sqlBreak_result =sql_select("select  a.id, a.mst_id, a.order_id, a.job_no_mst, a.book_con_dtls_id, a.product_id, a.description, a.specification, a.pcs_unit, a.cons_qty, a.process_loss, a.process_loss_qty, a.req_qty, a.remarks,b.unit_of_measure as unit from trims_job_card_breakdown a , product_details_master b where a.product_id=b.id and a.job_no_mst='$trims_job' and a.status_active=1 and a.is_deleted=0");
	$break_arr=array(); $break_arr_summery=array();
	foreach($sqlBreak_result as $row)
	{
		//$break_arr[$row[csf('mst_id')]]['info'].=$row[csf('description')]."_".$row[csf('pcs_unit')]."_".$row[csf('unit')]."_".$row[csf('req_qty')]."_".$row[csf('process_loss')]."_".$row[csf('remarks')]."_".$row[csf('product_id')]."**";
		$break_arr_summery[$row[csf('product_id')]]+=$row[csf('req_qty')];
	}*/
		
	//production.................................
	
	//  $iss_sql= "SELECT d.job_id as job_card_id,b.prod_id,b.cons_quantity,e.requisition_qty,c.avg_rate_per_unit from inv_issue_master a, inv_transaction b, product_details_master c,trims_raw_mat_requisition_mst d,trims_raw_mat_requisition_dtls e where a.id=b.mst_id and b.transaction_type=2   and a.entry_form=265  and a.req_no=d.requisition_no and d.id=e.mst_id  and  b.prod_id=c.id  and  b.prod_id=e.product_id and  e.product_id=c.id and a.status_active=1 and b.status_active=1 and  d.status_active=1 and d.status_active=1 and  e.status_active=1 and e.status_active=1   group by d.job_id,b.prod_id,b.cons_quantity,c.avg_rate_per_unit,e.requisition_qty";

	  /*$trims_production_sql="select a.section_id,c.sub_section,a.received_id,a.job_id,b.job_dtls_id,c.id as order_receive_dtls_id,c.item_description,c.color_id,c.size_id,c.uom,c.job_quantity,b.production_qty,b.qc_qty ,d.avg_rate_per_unit  from trims_production_mst a,trims_production_dtls b,trims_job_card_dtls c,trims_raw_mat_requisition_dtls e, product_details_master d where a.id=b.mst_id and c.id=b.job_dtls_id and c.mst_id=e.job_id  and  e.product_id=d.id $trimsjobid_cond $trimsreceiveid_cond and a.entry_form=269  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 group by a.section_id,c.sub_section,a.received_id,a.job_id,b.job_dtls_id,c.id,c.item_description,c.color_id,c.size_id,c.uom,c.job_quantity,b.production_qty,b.qc_qty,d.avg_rate_per_unit"; */
	  
	  	
	
		
	//Delivery.................................
	//$trims_delivery_sql="select a.delivery_date,a.received_id,b.delevery_qty,b.order_receive_rate,c.uom,b.section,c.sub_section,b.item_group,b.color_id,b.size_id,b.description,b.delevery_status,b.break_down_details_id,c.id from trims_delivery_mst a, trims_delivery_dtls b, trims_job_card_dtls c  where a.id=b.mst_id and c.id=b.job_dtls_id $trimsreceiveid_cond  and a.entry_form=208 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.delivery_date"; 
	$trims_delivery_sql="select a.delivery_date,a.received_id,b.delevery_qty,b.order_receive_rate,c.booked_uom as uom,b.section,c.sub_section,b.item_group,b.size_id,b.description,b.delevery_status,b.break_down_details_id,c.id from trims_delivery_mst a, trims_delivery_dtls b, subcon_ord_dtls c where a.id=b.mst_id and c.id=b.receive_dtls_id $trimsreceiveid_cond  and a.entry_form=208 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.delivery_date"; 
	$trims_delivery_data_arr=array();	
	$result_trims_delivery_sql = sql_select($trims_delivery_sql);
	foreach($result_trims_delivery_sql as $row)
	{
		//$key=$row[csf("item_group")].'*'.$row[csf("received_id")].'*'.$row[csf("section")].'*'.$row[csf("sub_section")].'*'.$row[csf("description")].'*'.$row[csf("color_id")].'*'.$row[csf("size_id")].'*'.$row[csf("uom")];
		
		$key=$row[csf("item_group")].'*'.$row[csf("received_id")].'*'.$row[csf("section")].'*'.$row[csf("sub_section")].'*'.$row[csf("description")].'*'.$row[csf("uom")];
		//c.id
		
		$trims_delivery_data_arr[$key][delevery_qty]+=$row[csf("delevery_qty")];
		$trims_delivery_data_arr[$key][delevery_val]+=$row[csf("delevery_qty")]*$row[csf("order_receive_rate")];
		$trims_delivery_data_arr[$key][delevery_last_date]=$row[csf("delivery_date")];
		$trims_delivery_data_arr[$key][id]=$row[csf("id")];
		$trims_delivery_data_arr[$key][delevery_status]=$row[csf("delevery_status")];
		$trims_delivery_data_arr[$key][break_ids].=$row[csf("break_down_details_id")].',';
	}
	//echo "<pre>";
	//print_r($trims_delivery_data_arr);

	$trims_bill_sql="select c.mst_id as received_id,c.section,c.sub_section,b.item_description,b.size_id,c.booked_uom as uom ,total_delv_qty,b.quantity,b.bill_amount,b.id from trims_bill_mst d, trims_bill_dtls b, subcon_ord_dtls c, subcon_ord_mst a where d.id=b.mst_id and c.mst_id=a.id and c.order_no=b.order_no and d.entry_form=276 and a.entry_form=255 and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.mst_id ,received_id,c.section,c.sub_section,b.item_description,b.size_id,c.booked_uom ,total_delv_qty,b.quantity,b.bill_amount,b.id";
	$trims_bill_data_arr=array();	
	$result_trims_bill_sql = sql_select($trims_bill_sql);
	foreach($result_trims_bill_sql as $row)
	{
		//$key=$row[csf("received_id")].'*'.$row[csf("section")].'*'.$row[csf("sub_section")].'*'.$row[csf("item_description")].'*'.$row[csf("color_id")].'*'.$row[csf("size_id")].'*'.$row[csf("uom")];
		
		$key=$row[csf("received_id")].'*'.$row[csf("section")].'*'.$row[csf("sub_section")].'*'.$row[csf("item_description")].'*'.$row[csf("uom")];
		
		$trims_bill_data_arr[$key][bill_qty]+=$row[csf("quantity")];
		$trims_bill_data_arr[$key][bill_val]+=$row[csf("bill_amount")];
		$trims_bill_data_arr[$key][dtls_ids].=$row[csf("id")].',';
	}

	   // $iss_sql= "SELECT a.req_id as job_card_id,b.prod_id,b.cons_quantity,c.avg_rate_per_unit from inv_issue_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and b.transaction_type=2   and a.entry_form=265 and  a.issue_basis=15 and   and b.prod_id=c.id and a.status_active=1 and b.status_active=1";
	
	
	 /* $req_iss_sql= "SELECT d.job_id as job_card_id,e.product_id,e.requisition_qty from  trims_raw_mat_requisition_mst d,trims_raw_mat_requisition_dtls e where  d.id=e.mst_id  and      d.status_active=1 and d.status_active=1 and  e.status_active=1 and e.status_active=1  and d.id=132 group by d.job_id,e.requisition_qty,e.product_id";
	$req_data_array=sql_select($req_iss_sql);
	foreach($req_data_array as $row)
	{
 		$requssition_qty_arr[$row[csf("job_card_id")]][$row[csf("product_id")]]+=$row[csf("requisition_qty")];
	}*/
	//echo "<pre>";
	//print_r($requssition_qty_arr);
	// and a.req_id=$data[2]
	
	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	/*echo "<pre>";
	print_r($issue_qty_arr);*/
	$width=2195;
	ob_start();
	?>
	<div align="center" style="height:auto; width:<? echo $width+20;?>px; margin:0 auto; padding:0;">
		<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="caption" align="center">
			<thead class="form_caption" >
				<tr>
					<td colspan="19" align="center" style="font-size:20px;"><? echo $companyArr[$cbo_company_id]; ?></td>
				</tr>
				<tr>
					<td colspan="19" align="center" style="font-size:14px; font-weight:bold" ><? echo $report_title; ?></td>
				</tr>
				<tr>
					<td colspan="19" align="center" style="font-size:14px; font-weight:bold">
						<? echo " From : ".$txt_date_from." To : ". $txt_date_to ;?>
					</td>
				</tr>
			</thead>
		</table>
		<table cellspacing="0" cellpadding="0" width="<? echo $width;?>" class="rpt_table" rules="all" border="1">
			<thead>
				<tr>
					<th align="canter" ><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
				</tr>
			</head>
	   </table>
        <table  border="1" cellpadding="0" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
			<thead>
					
						<th width="100">Work Order</th>
						<th width="120">Job Card</th>
						<th width="100">Item Name</th>
						<th width="200">Item Description</th>
						<th width="60">Item UOM</th>
						<th width="100">Order Qty</th>
						<th width="100">Raw Materials</th>
						<th width="60">Material UOM</th>
						<th width="100">Total Req.Qty</th>
						<th width="100">Material Issued Qty</th>
						<th width="100">Issued Balance Qty</th>
						<th width="100">Raw Cost</th>
						<th width="100">Prod. Qty</th>
						<th width="100">Prod. Cost</th>
						<th width="100">Prod. Bal.Qty</th>
						<th width="100">Delivery Qty</th>
						<th width="100">Delivery Amount(USD)</th>
						<th width="100">Delivery Bal.Qty</th>
						<th width="100">Cust. Buyer</th>
						<th width="100">Trims Ref.</th>
				    
			</thead>
		</table>
		<div style="width:<? echo $width+18;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
		<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id_two" width="<? echo $width;?>" rules="all" align="left">
            <? 
			$i=1;
			$total_order_qty=0;$total_order_val=0;$total_booked_qty=0;$total_production_qty=0;$total_req_qty=0;$total_issued_qty=0;$total_issued_bal=0;
			$total_production_qty=0;$total_production_cost=0;$total_prod_bal=0;$total_del_qty=0;$total_del_qty_usd=0;$total_del_bal=0;$total_issued_bal_cost=0;
			$del_qty_rate=0;$del_qty_rate_usd=0;
			$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
			foreach($date_array as $rcv_id=> $rcv_id_data)
			{
				$rcv_id_rowspan=0; 
				foreach($rcv_id_data as $order_id=> $order_id_data)
				{
					$order_id_rowspan=0;	
					foreach($order_id_data as $item_group_id=> $item_group_data)
					{
						$item_group_rowspan=0;	
						foreach($item_group_data as $item_id=> $item_id_data)
						{
							$item_rowspan=0;
							foreach($item_id_data as $description=> $description_data)
							{
								$description_rowspan=0;	
								foreach($description_data as $order_uom=> $order_uom_data)
								{
									// $color_id_rowspan=0;
									// foreach($color_id_data as $order_uom=> $order_uom_data)
									// {
										$order_uom_rowspan=0;
										foreach($order_uom_data as $job_id=> $job_id_data)
										{
											$job_id_rowspan=0;
											foreach($job_id_data as $row_desc=> $row_desc_data)
											{
												$row_desc_rowspan=0; 
												foreach($row_desc_data as $unit=> $row)
												{
													if($row['within_group']==1)
													{
															$party_name=$companyArr[$row['party_id']];
															$buyer_buyer=$party_arr[$row['buyer_buyer']] ;
													}else{
														$party_name=$party_arr[$row['party_id']]; 
														$buyer_buyer=$row['buyer_buyer'] ;
													}

													//$key=$rcv_id.'*'.$row['section_id'].'*'.$row['sub_section'].'*'.$description.'*'.$color_id.'*'.$row['size_id'].'*'.$order_uom;
		
													$key=$rcv_id.'*'.$row['section_id'].'*'.$row['sub_section'].'*'.$description.'*'.$order_uom;
		
													$booked_conv_fac=$row['booked_conv_fac'];
													$order_qty=$row['order_qty']*$booked_conv_fac;
													$production_qty=$trims_production_data_arr[$key][qc_qty];
													$prod_bal=$order_qty-$production_qty;
													$del_qty=$trims_delivery_data_arr[$item_group_id.'*'.$key][delevery_qty]*$booked_conv_fac;
													$del_bal=$order_qty-$del_qty;
													$req_qty=$row['req_qty'];
													$issued_qty=$issue_qty_arr[$job_id][$row['product_id']];
													$avg_rate_per_unit=$average_rate_arr[$job_id][$row['product_id']];
													
													//$total_production_qty_cost+=$trims_production_data_arr[$key][qc_qty]*$avg_rate_per_unit;
													//$req_qty=$requssition_qty_arr[$job_id][$row['product_id']];
													$issued_bal=$req_qty-$issued_qty;
													$internal_no=$buyer_po_arr[$row['buyer_po_id']]['grouping'];
													//echo $trims_production_data_arr[$key][qc_qty]*$avg_rate_per_unit."==";
													$rate=$row['rate'];
													
													$total_production_qty_cost=$product_cost_qty_arr[$key];
													

													/*$job_Qnty=0;
													for($j=0; $j<count($subcon_break_ids); $j++)
													{
														$job_Qnty +=$job_qnty_arr[$subcon_break_ids[$j]][$row[csf("conv_factor")]]['qnty'];
													}*/

													/*$order_key=$rcv_id.'*'.$order_id.'*'.$item_group_id.'*'.$item_id.'*'.$description.'*'.$color_id.'*'.$order_uom;
		
													$break_ids=$trims_data_arr[$order_key][break_ids];*/

												//echo $job_id."=".$row['product_id'];
													?>
													<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="left">
                                                         <? if($rcv_id_rowspan==0){ ?> <td width="100" rowspan="<? echo $rcv_rowspan_arr[$rcv_id]; ?>" align="center"><p><?  echo $row['cust_order_no'] ; ?></p></td><? } ?>
                                                       <? if($job_id_rowspan==0){ ?> <td width="120" rowspan="<? echo $job_id_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description][$order_uom][$job_id]; ?>" align="center"><p><?  echo $row['job_card_no_mst']; ?></p></td><? } ?>
                                                        <? if($item_group_rowspan==0){ ?> <td width="100" rowspan="<? echo $item_group_rowspan_arr[$rcv_id][$order_id][$item_group_id]; ?>" align="center"><p><?  echo $trimsGroupArr[$item_group_id] ; ?></p></td><? } ?>
                                                        <? if($description_rowspan==0){ ?> <td width="200" rowspan="<? echo $description_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description]; ?>" align="center"><p><?  echo $description; ?></p></td><? } ?>
                                                        <?/* if($color_id_rowspan==0){ ?> <td width="120" rowspan="<? echo $color_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description]; ?>" align="center"><p><?  echo $color_arr; ?></p></td><? } */?>
                                                        <? if($order_uom_rowspan==0){ ?> <td width="60" rowspan="<? echo $order_uom_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description][$order_uom]; ?>" align="center"><p><?  echo $unit_of_measurement[$order_uom]  ; ?></p></td><? } ?>
                                                        <? if($order_uom_rowspan==0){ ?> <td align="right" width="100" rowspan="<? echo $order_uom_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description][$order_uom]; ?>"><p><?  echo number_format($order_qty);  $total_order_qty+=$order_qty; ?></p></td><? } ?>
                                                        <? if($row_desc_rowspan==0)
													   { ?> 
                                                        
                                                        <td width="100" rowspan="<? echo $row_desc_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description][$order_uom][$job_id][$row_desc]; ?>" align="center"><p><?  echo $row_desc ; ?></p></td>
														 <td width="60" rowspan="<? echo $row_desc_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description][$order_uom][$job_id][$row_desc]; ?>" align="center"><p><?  echo $unit_of_measurement[$unit]; ?></p></td>
														 <td align="right" width="100" rowspan="<? echo $row_desc_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description][$order_uom][$job_id][$row_desc]; ?>" ><p><? echo number_format($req_qty,4,'.',''); $total_req_qty+=$req_qty; ?></p></td>
														<td align="right" width="100" rowspan="<? echo $row_desc_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description][$order_uom][$job_id][$row_desc]; ?>" ><p><?  echo number_format($issued_qty,4,'.','');  $total_issued_qty+=$issued_qty;?></p></td>
														<td align="right" width="100" rowspan="<? echo $row_desc_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description][$order_uom][$job_id][$row_desc]; ?>" ><p><?  echo number_format($issued_bal,4,'.','');   $total_issued_bal+=$issued_bal; ?></p></td>
                                                        <td align="right" width="100" rowspan="<? echo $row_desc_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description][$order_uom][$job_id][$row_desc]; ?>" ><p><?  echo number_format($issued_qty*$avg_rate_per_unit,4,'.','');   $total_issued_bal_cost+=$issued_qty*$avg_rate_per_unit; 
							 // $total_production_qty_cost+=$production_qty*$avg_rate_per_unit;							
														
														?></p></td>
 													<? } ?>
                                                        
                                                        <? if($order_uom_rowspan==0){ ?> <td align="right" width="100" rowspan="<? echo $order_uom_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description][$order_uom]; ?>"><p><?  echo number_format($production_qty,4,'.','');   $total_production_qty+=$production_qty;  ?></p></td>
 														    
														<? } ?>
                                                         <? if($order_uom_rowspan==0){ ?> <td align="right" width="100" rowspan="<? echo $order_uom_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description][$order_uom]; ?>"><p><?  echo  number_format($total_production_qty_cost,4,'.','');   $total_production_cost+=$total_production_qty_cost; ?></p></td>
 														     
														<? } ?>
                                                          <? if($order_uom_rowspan==0){ ?> <td align="right" width="100" rowspan="<? echo $order_uom_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description][$order_uom]; ?>"><p><?   echo number_format($prod_bal,4,'.','');   $total_prod_bal+=$prod_bal; ?></p></td><? } ?>
                                                        <? if($order_uom_rowspan==0){ ?> <td align="right" width="100" rowspan="<? echo $order_uom_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description][$order_uom]; ?>"><p><? echo number_format($del_qty,2,'.','');     $total_del_qty+=$del_qty;  ?></p></td>
 														<? } ?>
                                                         <? if($order_uom_rowspan==0){ ?> <td align="right" width="100" rowspan="<? echo $order_uom_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description][$order_uom]; ?>"><p><?  
														 
 														$del_qty_rate=$del_qty*$rate;
														$del_qty_rate_usd=$del_qty_rate*$booked_conv_fac;
 														 echo number_format($del_qty_rate_usd,2,'.','');   $total_del_qty_usd+=$del_qty_rate_usd; ?></p></td>
 														<? } ?>
                                                        
                                                       <? if($order_uom_rowspan==0){ ?> <td width="100" align="right" rowspan="<? echo $order_uom_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description][$order_uom]; ?>"><p><?  echo number_format($del_bal,4,'.',''); $total_del_bal+=$del_bal;   ?></p></td><? } ?>
                                                        <? if($rcv_id_rowspan==0){ ?> <td width="100" align="right" rowspan="<? echo $rcv_rowspan_arr[$rcv_id]; ?>"><p><?  echo $buyer_buyer  ; ?></p></td><? } ?>
                                                       <? if($rcv_id_rowspan==0){ ?> <td width="100" align="right" rowspan="<? echo $rcv_rowspan_arr[$rcv_id]; ?>"><p><?  echo $row['trims_ref']  ; ?></p></td><? } ?>
 													</tr>
													<?
													$order_id_rowspan++;
													$item_group_rowspan++;
													$item_rowspan++;
													$description_rowspan++;
													//$color_id_rowspan++;
													$order_uom_rowspan++;
													$job_id_rowspan++;
													$row_desc_rowspan++;
													$rcv_id_rowspan++;
													$i++;
												}
											}
										}
									//}
								}
							}
						}
					}
				}
			}?>
		</table>
		</div>
		<table width="<? echo $width;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_footer" align="left">
			<tfoot>
                <th width="100"></th>
                <th width="120"></th>
                <th width="100"></th>
                <th width="200"></th>
                <th width="60"></th>
                <th width="100"  id="total_order_qty"><? echo number_format($total_order_qty,4,'.','');   ?></th>
                 <th width="100"></th>
                <th width="60"></th>
                <th width="100" id="total_req_qty"><? echo number_format($total_req_qty,4,'.','');  ?></th>  
                <th width="100" id="total_issue_qty"><? echo number_format($total_issued_qty,4,'.','');   ?></th>
                <th width="100" id="total_issue_balence"><? echo number_format($total_issued_bal,4,'.','');  ?></th>  
                <th width="100" id="total_issue_balence_cost"><? echo number_format($total_issued_bal_cost,4,'.','');  ?></th>
                <th width="100" id="total_production_qty"><? echo number_format($total_production_qty,4,'.',''); ?></th>
                <th width="100" id="total_production_cost"><? echo number_format($total_production_cost,4,'.','');  ?></th>
                <th width="100" id="total_prod_bal"><? echo number_format($total_prod_bal,4,'.','');   ?></th>
                 <th width="100" id="total_del_qty"><? echo number_format($total_del_qty,4,'.',''); ?></th>
                <th width="100" id="total_del_qty_usd"><? echo number_format($total_del_qty_usd,4,'.','');  ?></th>
                <th width="100" id="total_del_bal"><? echo number_format($total_del_bal,4,'.','');  ?></th>
                <th width="100"></th>
                <th width="100"></th>
 			</tfoot>
		</table>
    </div>
   
   
   
    
    <div align="center" style="height:auto; width:<? echo 860+20;?>px; margin:0 auto; padding:0;">
         <table  cellpadding="0" cellspacing="0" class="rpt_table" width="<? echo 860;?>" rules="all" id="rpt_table_header2" align="left">
			<thead>
              <tr>
				 <td colspan="9" align="center" style="font-size:20px;">Summary</td>
			 </tr>
                <th width="100">Item Name</th>
                <th width="100">Raw Materials</th>
                <th width="60">Material UOM</th>
                <th width="100">Total Req.Qty</th>
                <th width="100">Raw Cost</th>
                <th width="100">Order Qty</th>
                <th width="100">Order Value</th>
                <th width="100">Delivery Qty</th>
                <th width="100">Delivery Amount(USD)</th>
 			</thead>
		</table>
		<div style="width:<? echo 860+18;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
		<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="<? echo 860;?>" rules="all" align="left">
            <? 
			$i=1;
			$total_order_qty=0;$total_order_val=0;$total_booked_qty=0;$total_production_qty=0;$total_req_qty=0;$total_issued_qty=0;$total_issued_bal=0;
			$total_production_qty=0;$total_production_cost=0;$total_prod_bal=0;$total_del_qty=0;$total_del_qty_usd=0;$total_del_bal=0;$total_issued_bal_cost=0;$total_order_qty_value=0;
			$del_qty_rate=0;$del_qty_rate_usd=0;
			$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
			foreach($date_array as $rcv_id=> $rcv_id_data)
			{
				$rcv_id_rowspan=0; 
				foreach($rcv_id_data as $order_id=> $order_id_data)
				{
					$order_id_rowspan=0;	
					foreach($order_id_data as $item_group_id=> $item_group_data)
					{
						$item_group_rowspan=0;	
						foreach($item_group_data as $item_id=> $item_id_data)
						{
							$item_rowspan=0;
							foreach($item_id_data as $description=> $description_data)
							{
								$description_rowspan=0;	
								foreach($description_data as $color_id=> $color_id_data)
								{
									$color_id_rowspan=0;
									foreach($color_id_data as $order_uom=> $order_uom_data)
									{
										$order_uom_rowspan=0;
										foreach($order_uom_data as $job_id=> $job_id_data)
										{
											$job_id_rowspan=0;
											foreach($job_id_data as $row_desc=> $row_desc_data)
											{
												$row_desc_rowspan=0; 
												foreach($row_desc_data as $unit=> $row)
												{
													if($row['within_group']==1)
													{
															$party_name=$companyArr[$row['party_id']];
															$buyer_buyer=$party_arr[$row['buyer_buyer']] ;
													}else{
														$party_name=$party_arr[$row['party_id']]; 
														$buyer_buyer=$row['buyer_buyer'] ;
													}

													//$key=$rcv_id.'*'.$row['section_id'].'*'.$row['sub_section'].'*'.$description.'*'.$color_id.'*'.$row['size_id'].'*'.$order_uom;
		
													$key=$rcv_id.'*'.$row['section_id'].'*'.$row['sub_section'].'*'.$description.'*'.$color_id.'*'.$order_uom;
		
													$production_qty=$trims_production_data_arr[$key][qc_qty];
													$prod_bal=$row['order_qty']-$production_qty;
													$del_qty=$trims_delivery_data_arr[$item_group_id.'*'.$key][delevery_qty];
													$del_bal=$row['order_qty']-$del_qty;
													$req_qty=$row['req_qty'];
													$issued_qty=$issue_qty_arr[$job_id][$row['product_id']];
													$avg_rate_per_unit=$average_rate_arr[$job_id][$row['product_id']];
													
													//$total_production_qty_cost+=$trims_production_data_arr[$key][qc_qty]*$avg_rate_per_unit;
													//$req_qty=$requssition_qty_arr[$job_id][$row['product_id']];
													$issued_bal=$req_qty-$issued_qty;
													$internal_no=$buyer_po_arr[$row['buyer_po_id']]['grouping'];
													//echo $trims_production_data_arr[$key][qc_qty]*$avg_rate_per_unit."==";
													$booked_conv_fac=$row['booked_conv_fac'];
													$rate=$row['rate'];
													
													$total_production_qty_cost=$product_cost_qty_arr[$key];
													

													/*$job_Qnty=0;
													for($j=0; $j<count($subcon_break_ids); $j++)
													{
														$job_Qnty +=$job_qnty_arr[$subcon_break_ids[$j]][$row[csf("conv_factor")]]['qnty'];
													}*/

													/*$order_key=$rcv_id.'*'.$order_id.'*'.$item_group_id.'*'.$item_id.'*'.$description.'*'.$color_id.'*'.$order_uom;
		
													$break_ids=$trims_data_arr[$order_key][break_ids];*/

												//echo $job_id."=".$row['product_id'];
													?>
													<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="left">
                                                         
                                                        <? if($item_group_rowspan==0){ ?> <td width="100" rowspan="<? echo $item_group_rowspan_arr[$rcv_id][$order_id][$item_group_id]; ?>" align="center"><p><?  echo $trimsGroupArr[$item_group_id] ; ?></p></td><? } ?>
                                                         
                                                        <? if($row_desc_rowspan==0)
													   { ?> 
                                                        
                                                        <td width="100" rowspan="<? echo $row_desc_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description][$color_id][$order_uom][$job_id][$row_desc]; ?>" align="center"><p><?  echo $row_desc ; ?></p></td>
														 <td width="60" rowspan="<? echo $row_desc_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description][$color_id][$order_uom][$job_id][$row_desc]; ?>" align="center"><p><?  echo $unit_of_measurement[$unit]; ?></p></td>
														 <td align="right" width="100" rowspan="<? echo $row_desc_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description][$color_id][$order_uom][$job_id][$row_desc]; ?>" ><p><? echo number_format($req_qty,4,'.',''); $total_req_qty+=$req_qty; ?></p></td>
                                                        <td align="right" width="100" rowspan="<? echo $row_desc_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description][$color_id][$order_uom][$job_id][$row_desc]; ?>" ><p><?  echo number_format($issued_qty*$avg_rate_per_unit,4,'.','');   $total_issued_bal_cost+=$issued_qty*$avg_rate_per_unit; 
							 // $total_production_qty_cost+=$production_qty*$avg_rate_per_unit;							
														
														?></p></td>
 													<? } ?>
                                                    <? if($order_uom_rowspan==0){ ?> <td align="right" width="100" rowspan="<? echo $order_uom_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description][$color_id][$order_uom]; ?>"><p><?  echo number_format($row['order_qty'],4,'.','');  $total_order_qty+=$row['order_qty']; ?></p></td><? } ?>
                                                         <? if($order_uom_rowspan==0){ ?> <td align="right" width="100" rowspan="<? echo $order_uom_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description][$color_id][$order_uom]; ?>"><p><?  echo number_format($row['order_qty']*$rate,4,'.','');  $total_order_qty_value+=$row['order_qty']*$rate; ?></p></td><? } ?>
                                                          
                                                          <? if($order_uom_rowspan==0){ ?> <td align="right" width="100" rowspan="<? echo $order_uom_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description][$color_id][$order_uom]; ?>"><p><? echo number_format($del_qty,4,'.','');     $total_del_qty+=$del_qty;  ?></p></td>
 														<? } ?>
                                                         <? if($order_uom_rowspan==0){ ?> <td align="right" width="100" rowspan="<? echo $order_uom_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description][$color_id][$order_uom]; ?>"><p><?  
														 
 														$del_qty_rate=$del_qty*$rate;
														$del_qty_rate_usd=$del_qty_rate*$booked_conv_fac;
 														 echo number_format($del_qty_rate_usd,4,'.','');   $total_del_qty_usd+=$del_qty_rate_usd; ?></p></td>
 														<? } ?>
  													</tr>
													<?
													$order_id_rowspan++;
													$item_group_rowspan++;
													$item_rowspan++;
													$description_rowspan++;
													$color_id_rowspan++;
													$order_uom_rowspan++;
													$job_id_rowspan++;
													$row_desc_rowspan++;
													$rcv_id_rowspan++;
													$i++;
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}?>
		</table>
		</div>
		<table width="<? echo 860;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_footer" align="left">
			<tfoot>
                 <th width="100"> </th>
                 <th width="100"> </th>
                 <th width="60"> </th>
                <th width="100"><? echo number_format($total_req_qty,4,'.','');  ?></th>  
                 <th width="100"><? echo number_format($total_issued_bal_cost,4,'.','');  ?></th>
                 <th width="100"><? echo number_format($total_order_qty,4,'.','');   ?></th>
                 <th width="100"><? echo number_format($total_order_qty_value,4,'.','');   ?></th>
                 <th width="100"><? echo number_format($total_del_qty,4,'.',''); ?></th>
                <th width="100"><? echo number_format($total_del_qty_usd,4,'.','');  ?></th>
   			</tfoot>
		</table>
    </div>
    <?
    
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
    echo "$html**$filename**$report_type";
    exit();
}

if($action=="generate_report_2")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$cbo_company_id=str_replace("'","", $cbo_company_id);
	$cbo_customer_source=str_replace("'","", $cbo_customer_source);
	$cbo_customer_name=str_replace("'","", $cbo_customer_name);
	$txt_order_no=str_replace("'","", $txt_order_no);
	$internal_no=str_replace("'","", $txt_internal_no);
	$cbo_section_id=str_replace("'","", $cbo_section_id);
	$cbo_sub_section_id=str_replace("'","", $cbo_sub_section_id);
	$txt_item_description=str_replace("'","", $txt_item_description);
	$cbo_date_category=str_replace("'","", $cbo_date_category);
	$txt_date_from=str_replace("'","", $txt_date_from);
	$txt_date_to=str_replace("'","", $txt_date_to);
	$cbo_delivery_status=str_replace("'","", $cbo_delivery_status);
	$cbo_customer_buyer=str_replace("'","", $cbo_customer_buyer);
	$cbo_team_leader=str_replace("'","", $cbo_team_leader);
	$cbo_team_member=str_replace("'","", $cbo_team_member);


	if($cbo_delivery_status== 0){$delivery_status_con="";}
	else{
		$delivery_status_con=" and c.delivery_status=$cbo_delivery_status and b.delivery_status=$cbo_delivery_status";
	}
		
	if($db_type==0) 
	{
		$txt_date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
		$txt_date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
	}
	else if($db_type==2) 
	{
		$txt_date_from=change_date_format($txt_date_from,'','',1);
		$txt_date_to=change_date_format($txt_date_to,'','',1);
	}
	
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0 ","id","buyer_name");
	$trimsGroupArr = return_library_array("select id, item_name from lib_item_group where item_category=4 and status_active=1","id","item_name");
	$colorNameArr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	//$leaderArr = return_library_array("select id, team_leader_name from lib_marketing_team where status_active=1 and is_deleted=0 and project_type=3","id","team_leader_name");
	//$memberArr = return_library_array("select id, team_member_name from lib_mkt_team_member_info","id","team_member_name");

	$currency_rate=return_field_value( "conversion_rate", "currency_conversion_rate", "currency=2 and status_active=1  and id in(select max(id) from currency_conversion_rate where currency=2 and is_deleted=0 and status_active=1)" , "conversion_rate" );
	
	if($txt_date_from!="" and $txt_date_to!=""){	
		if($cbo_date_category==1){$where_con.=" and a.receive_date between '$txt_date_from' and '$txt_date_to'";}	
		else if($cbo_date_category==2){$where_con.=" and a.delivery_date between '$txt_date_from' and '$txt_date_to'";}
	}
	if($cbo_section_id){$where_con.=" and b.section='$cbo_section_id'";} 
	if($cbo_sub_section_id){$where_con.=" and b.sub_section='$cbo_sub_section_id'";} 
	if($cbo_customer_source){$where_con.=" and a.within_group='$cbo_customer_source'";} 
	if($cbo_customer_name){$where_con.=" and a.party_id='$cbo_customer_name'";} 
	if($cbo_customer_buyer){$where_con.=" and b.buyer_buyer='$cbo_customer_buyer'";} 
	if($cbo_team_leader){$where_con.=" and a.team_leader='$cbo_team_leader'";} 
	if($cbo_team_member){$where_con.=" and a.team_member='$cbo_team_member'";} 
	
	
	//echo $internal_no;
 	$buyer_po_id_cond = '';
    if($cbo_customer_source==1 || $cbo_customer_source==0)
    {
		if($internal_no !="") $internal_no_cond = " and grouping like('%$internal_no%')";
		
		$buyer_po_arr=array();
		$buyer_po_id_arr=array();
		 $po_sql ="Select id,grouping from  wo_po_break_down  where is_deleted=0 and status_active=1 $internal_no_cond "; 
		$po_sql_res=sql_select($po_sql);
		foreach ($po_sql_res as $row)
		{
			$buyer_po_arr[$row[csf("id")]]['grouping']=$row[csf("grouping")];
			$buyer_po_id_arr[]=$row[csf("id")];
		}
		unset($po_sql_res);
        //$buyer_po_lib = return_library_array("select id, id as id2 from wo_po_break_down where grouping like '%$internal_no%'",'id','id2');
		if($internal_no !="")
		{
			$buyer_po_id = implode(",", $buyer_po_id_arr);
			if ($buyer_po_id=="")
			{
				echo "Not Found."; die;
			}
       	 	if($buyer_po_id !="") $buyer_po_id_cond = " and b.buyer_po_id in($buyer_po_id)";
		}
    }
	//echo $buyer_po_id_cond; die;
	
    $sqlBreak_result =sql_select("select  a.id, a.mst_id, a.order_id, a.job_no_mst, a.book_con_dtls_id, a.product_id, a.description, a.specification, a.pcs_unit, a.cons_qty, a.process_loss, a.process_loss_qty, a.req_qty, a.remarks,b.unit_of_measure as unit from trims_job_card_breakdown a , product_details_master b where a.product_id=b.id and a.status_active=1 and a.is_deleted=0");
			
	$break_arr=array(); $break_arr_summery=array();
	foreach($sqlBreak_result as $row)
	{
		$break_arr[$row[csf('mst_id')]]['info'].=$row[csf('description')]."_".$row[csf('pcs_unit')]."_".$row[csf('unit')]."_".$row[csf('req_qty')]."_".$row[csf('process_loss')]."_".$row[csf('remarks')]."_".$row[csf('product_id')]."**";
		$break_arr_summery[$row[csf('description')]][$row[csf('unit')]][$row[csf('product_id')]]+=$row[csf('req_qty')];
	}

	if($txt_order_no !="") $order_no_cond = " and b.order_no like('%$txt_order_no%')";
	
	//print_r($buyer_po_arr); die;
	//$sql_chcek = sql_select("select id from  inv_issue_master	where status_active=1 and is_deleted=0 $entry_form_cond  and company_id=".$company."   and id not in(select poid from tmp_poid where userid=$user_id) $date_cond  $iss_number_cond   $is_approved_cond order by id  desc");

	$trims_order_sql="SELECT a.id,a.subcon_job,a.order_no as cust_order_no,a.party_id,a.within_group,a.trims_ref,b.section,b.sub_section,b.item_group,b.buyer_buyer,b.job_no_mst,b.order_no,b.booked_uom,b.order_uom,b.buyer_po_no,b.buyer_po_id,b.booked_conv_fac,c.id as break_id,c.description,c.order_id,c.item_id,c.color_id,c.size_id ,c.qnty,c.rate ,c.amount,c.booked_qty,e.job_no_mst as job_card_no_mst,f.product_id, f.description as row_desc, f.specification, f.unit, f.pcs_unit, f.cons_qty, f.req_qty,d.id as job_id from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c,trims_job_card_mst d,trims_job_card_breakdown f, trims_job_card_dtls e where a.subcon_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id and a.id=d.received_id and d.id=e.mst_id and e.id=f.mst_id and a.entry_form=255 and a.company_id =$cbo_company_id  $buyer_po_id_cond  $order_no_cond $where_con $delivery_status_con 
	and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and f.is_deleted=0 and f.status_active=1 group by a.id,a.subcon_job,a.order_no ,a.party_id,a.within_group,a.trims_ref,b.section,b.sub_section,b.item_group,b.buyer_buyer,b.job_no_mst,b.order_no,b.booked_uom,b.order_uom,b.buyer_po_no,b.buyer_po_id,b.booked_conv_fac, c.id,c.description,c.order_id,c.item_id,c.color_id,c.size_id,c.qnty,c.rate,c.amount,c.booked_qty,e.job_no_mst,f.description,f.specification,f.unit,f.pcs_unit,f.cons_qty,f.req_qty,d.id,f.product_id ";
	
	$trims_receive_id_arr=array();
	$trims_data_arr=array();	
	$result_trims_order_sql = sql_select($trims_order_sql);
	foreach($result_trims_order_sql as $row)
	{
		//Raw Material 	Material UOM	Total Req.Qty	Job Card	Work Order	Customer Name	Cust. Buyer	Trims Ref.	Internal Ref.	Item Name	Item Description	Item Color	Item UOM	Order Qty	Prod. Qty	Prod. Bal.Qty	Deli. Qty	Deli. Bal. Qty

		$key=$row[csf("id")].'*'.$row[csf("order_id")].'*'.$row[csf("item_group")].'*'.$row[csf("item_id")].'*'.$row[csf("description")].'*'.$row[csf("color_id")].'*'.$row[csf("booked_uom")];
		
		$trims_data_arr[$key][qnty]+=$row[csf("qnty")];
		$trims_data_arr[$key][amount]+=$row[csf("amount")];
		$trims_data_arr[$key][booked_qty]+=$row[csf("booked_qty")];
		$trims_data_arr[$key][break_ids].=$row[csf("break_id")].',';
		$trims_data_arr[$key][buyer_po_no].=$row[csf("buyer_po_no")].',';

		$buyer_po_no=chop(implode(',',array_unique(explode(",",$trims_data_arr[$key][buyer_po_no]))),',');
		$order_rate=$trims_data_arr[$key][amount]/$trims_data_arr[$key][qnty];

		$date_array[$row[csf("id")]][$row[csf("order_id")]][$row[csf("item_group")]][$row[csf("item_id")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("booked_uom")]][$row[csf("job_id")]][$row[csf("row_desc")]][$row[csf("unit")]]['subcon_job']=$row[csf("subcon_job")];
		$date_array[$row[csf("id")]][$row[csf("order_id")]][$row[csf("item_group")]][$row[csf("item_id")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("booked_uom")]][$row[csf("job_id")]][$row[csf("row_desc")]][$row[csf("unit")]]['item_group']=$row[csf("item_group")];
		$date_array[$row[csf("id")]][$row[csf("order_id")]][$row[csf("item_group")]][$row[csf("item_id")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("booked_uom")]][$row[csf("job_id")]][$row[csf("row_desc")]][$row[csf("unit")]]['order_qty'] =$row[csf("qnty")];
		$date_array[$row[csf("id")]][$row[csf("order_id")]][$row[csf("item_group")]][$row[csf("item_id")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("booked_uom")]][$row[csf("job_id")]][$row[csf("row_desc")]][$row[csf("unit")]]['booked_conv_fac'] =$row[csf("booked_conv_fac")];
		$date_array[$row[csf("id")]][$row[csf("order_id")]][$row[csf("item_group")]][$row[csf("item_id")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("booked_uom")]][$row[csf("job_id")]][$row[csf("row_desc")]][$row[csf("unit")]]['booked_qty'] +=$row[csf("booked_qty")];
		$date_array[$row[csf("id")]][$row[csf("order_id")]][$row[csf("item_group")]][$row[csf("item_id")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("booked_uom")]][$row[csf("job_id")]][$row[csf("row_desc")]][$row[csf("unit")]]['cust_order_no']=$row[csf("cust_order_no")];
		$date_array[$row[csf("id")]][$row[csf("order_id")]][$row[csf("item_group")]][$row[csf("item_id")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("booked_uom")]][$row[csf("job_id")]][$row[csf("row_desc")]][$row[csf("unit")]]['party_id']=$row[csf("party_id")];
		$date_array[$row[csf("id")]][$row[csf("order_id")]][$row[csf("item_group")]][$row[csf("item_id")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("booked_uom")]][$row[csf("job_id")]][$row[csf("row_desc")]][$row[csf("unit")]]['within_group']=$row[csf("within_group")];
		$date_array[$row[csf("id")]][$row[csf("order_id")]][$row[csf("item_group")]][$row[csf("item_id")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("booked_uom")]][$row[csf("job_id")]][$row[csf("row_desc")]][$row[csf("unit")]]['buyer_buyer']=$row[csf("buyer_buyer")];
		$date_array[$row[csf("id")]][$row[csf("order_id")]][$row[csf("item_group")]][$row[csf("item_id")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("booked_uom")]][$row[csf("job_id")]][$row[csf("row_desc")]][$row[csf("unit")]]['booked_uom']=$row[csf("booked_uom")];
		$date_array[$row[csf("id")]][$row[csf("order_id")]][$row[csf("item_group")]][$row[csf("item_id")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("booked_uom")]][$row[csf("job_id")]][$row[csf("row_desc")]][$row[csf("unit")]]['buyer_po_id']=$row[csf("buyer_po_id")];
		$date_array[$row[csf("id")]][$row[csf("order_id")]][$row[csf("item_group")]][$row[csf("item_id")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("booked_uom")]][$row[csf("job_id")]][$row[csf("row_desc")]][$row[csf("unit")]]['order_uom']=$row[csf("order_uom")];
		$date_array[$row[csf("id")]][$row[csf("order_id")]][$row[csf("item_group")]][$row[csf("item_id")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("booked_uom")]][$row[csf("job_id")]][$row[csf("row_desc")]][$row[csf("unit")]]['req_qty'] +=$row[csf("req_qty")]*$row[csf("qnty")]; 

		$date_array[$row[csf("id")]][$row[csf("order_id")]][$row[csf("item_group")]][$row[csf("item_id")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("booked_uom")]][$row[csf("job_id")]][$row[csf("row_desc")]][$row[csf("unit")]]['section_id'] =$row[csf("section")]; 
		$date_array[$row[csf("id")]][$row[csf("order_id")]][$row[csf("item_group")]][$row[csf("item_id")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("booked_uom")]][$row[csf("job_id")]][$row[csf("row_desc")]][$row[csf("unit")]]['sub_section'] =$row[csf("sub_section")]; 
		$date_array[$row[csf("id")]][$row[csf("order_id")]][$row[csf("item_group")]][$row[csf("item_id")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("booked_uom")]][$row[csf("job_id")]][$row[csf("row_desc")]][$row[csf("unit")]]['size_id'] =$row[csf("size_id")]; 
		$date_array[$row[csf("id")]][$row[csf("order_id")]][$row[csf("item_group")]][$row[csf("item_id")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("booked_uom")]][$row[csf("job_id")]][$row[csf("row_desc")]][$row[csf("unit")]]['product_id'] =$row[csf("product_id")]; 
		
		$date_array[$row[csf("id")]][$row[csf("order_id")]][$row[csf("item_group")]][$row[csf("item_id")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("booked_uom")]][$row[csf("job_id")]][$row[csf("row_desc")]][$row[csf("unit")]]['trims_ref'] =$row[csf("trims_ref")];
		$date_array[$row[csf("id")]][$row[csf("order_id")]][$row[csf("item_group")]][$row[csf("item_id")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("booked_uom")]][$row[csf("job_id")]][$row[csf("row_desc")]][$row[csf("unit")]]['job_card_no_mst'] =$row[csf("job_card_no_mst")];
		
		$trims_receive_id_arr[$row[csf("id")]]=$row[csf("id")];
		$trims_job_id_arr[$row[csf("job_id")]]=$row[csf("job_id")];
	}
	//echo "<pre>"; print_r($date_array); die;
	$section_rowspan_arr=array(); $order_rowspan_arr=array(); $item_group_rowspan_arr=array();  $item_rowspan_arr=array(); $description_rowspan_arr=array(); $color_rowspan_arr=array();  $order_uom_rowspan_arr=array(); $job_id_rowspan_arr=array(); $row_desc_rowspan_arr=array();
    foreach($date_array as $rcv_id=> $rcv_id_data)
	{
		$rcv_id_rowspan=0;
		foreach($rcv_id_data as $order_id=> $order_id_data)
		{
			$order_id_rowspan=0;	
			foreach($order_id_data as $item_group_id=> $item_group_data)
			{
				$item_group_rowspan=0;	
				foreach($item_group_data as $item_id=> $item_id_data)
				{
					$item_rowspan=0;
					foreach($item_id_data as $description=> $description_data)
					{
						$description_rowspan=0;	
						foreach($description_data as $color_id=> $color_id_data)
						{
							$color_id_rowspan=0;
							foreach($color_id_data as $order_uom=> $order_uom_data)
							{
								$order_uom_rowspan=0;
								foreach($order_uom_data as $job_id=> $job_id_data)
								{
									$job_id_rowspan=0;
									foreach($job_id_data as $row_desc=> $row_desc_data)
									{
										$row_desc_rowspan=0;
										foreach($row_desc_data as $unit=> $row)
										{
											$order_id_rowspan++;
											$item_group_rowspan++;
											$item_rowspan++;
											$description_rowspan++;
											$color_id_rowspan++;
											$order_uom_rowspan++;
											$job_id_rowspan++;
											$row_desc_rowspan++;
											$rcv_id_rowspan++;
										}
										$row_desc_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description][$color_id][$order_uom][$job_id][$row_desc]=$row_desc_rowspan;
									}
									$job_id_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description][$color_id][$order_uom][$job_id]=$job_id_rowspan;
								}
								$order_uom_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description][$color_id][$order_uom]=$order_uom_rowspan;
							}
							$color_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description][$color_id]=$color_id_rowspan;
						}
						$description_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description]=$description_rowspan;
					}
					$item_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id]=$item_rowspan;
				}
				$item_group_rowspan_arr[$rcv_id][$order_id][$item_group_id]=$item_group_rowspan;
			}
			$order_rowspan_arr[$rcv_id][$order_id]=$order_id_rowspan;
		}
		$rcv_rowspan_arr[$rcv_id]=$rcv_id_rowspan;
	}


	$trims_receive_id=implode(',',$trims_receive_id_arr);
	if($trims_receive_id!='')
	{
		$trimsreceiveid=chop($trims_receive_id,','); 
		$trimsreceiveid_cond="";
		$trimsreceive_ids=count(array_unique(explode(",",$trimsreceiveid)));
			if($db_type==2 && $trimsreceive_ids>1000)
			{
				$trimsreceiveid_cond=" and (";
				$trimsreceiveidArr=array_chunk(explode(",",$trimsreceiveid),999);
				foreach($trimsreceiveidArr as $ids)
				{
					$ids=implode(",",$ids);
					$trimsreceiveid_cond.=" a.received_id in($ids) or"; 
				}
				$trimsreceiveid_cond=chop($trimsreceiveid_cond,'or ');
				$trimsreceiveid_cond.=")";
			}
			else
			{
				if($trimsreceiveid!="")
				{
					$issue_ids=implode(",",array_unique(explode(",",$trimsreceiveid)));
					$trimsreceiveid_cond=" and a.received_id in($issue_ids)";
				}
				else { $trimsreceiveid_cond ="";}
			}
		
		}
		
	//Job-------------------------------	
	/*$trims_job_sql="select a.id,a.trims_job,a.received_no from trims_job_card_mst a where a.status_active=1 and a.is_deleted=0 $trimsreceiveid_cond "; 
	$trims_job_no_arr=array();	
	$result_trims_job_sql = sql_select($trims_job_sql);
	foreach($result_trims_job_sql as $row)
	{
		$trims_job_no_arr[$row[csf("received_no")]]=$row[csf("trims_job")];
		$trims_job_id_arr[$row[csf("id")]]=$row[csf("id")];
	}
		*/
	$trims_job_id=implode(',',$trims_job_id_arr);
	if($trims_job_id!='')
	{
		
		$trimsjobid=chop($trims_job_id,','); 
		$trimsjobid_cond="";
		$trimsjobids=count(array_unique(explode(",",$trimsjobid)));
		
		
		if($db_type==2 && $trimsjobids>1000)
		{
			$trimsjobid_cond=" and (";
			$trimsreceiveidArr=array_chunk(explode(",",$trimsjobid),999);
			foreach($trimsreceiveidArr as $jobids)
			{
				$jobids=implode(",",$jobids);
				$trimsjobid_cond.=" a.job_id in($jobids) or"; 
			}
			$trimsjobid_cond=chop($trimsjobid_cond,'or ');
			$trimsjobid_cond.=")";
		}
		else
		{
			if($trimsjobid!="")
			{
				$issue_ids=implode(",",array_unique(explode(",",$trimsjobid)));
				$trimsjobid_cond=" and a.job_id in($issue_ids)";
			}
			else { $trimsjobid_cond ="";}
		}
	
	}

	/*$sqlBreak_result =sql_select("select  a.id, a.mst_id, a.order_id, a.job_no_mst, a.book_con_dtls_id, a.product_id, a.description, a.specification, a.pcs_unit, a.cons_qty, a.process_loss, a.process_loss_qty, a.req_qty, a.remarks,b.unit_of_measure as unit from trims_job_card_breakdown a , product_details_master b where a.product_id=b.id and a.job_no_mst='$trims_job' and a.status_active=1 and a.is_deleted=0");
	$break_arr=array(); $break_arr_summery=array();
	foreach($sqlBreak_result as $row)
	{
		//$break_arr[$row[csf('mst_id')]]['info'].=$row[csf('description')]."_".$row[csf('pcs_unit')]."_".$row[csf('unit')]."_".$row[csf('req_qty')]."_".$row[csf('process_loss')]."_".$row[csf('remarks')]."_".$row[csf('product_id')]."**";
		$break_arr_summery[$row[csf('product_id')]]+=$row[csf('req_qty')];
	}*/
		
	//production.................................
	$trims_production_sql="select a.section_id,c.sub_section,a.received_id,a.job_id,b.job_dtls_id,c.id as order_receive_dtls_id,c.item_description,c.color_id,c.size_id,c.uom,c.job_quantity,b.production_qty,b.qc_qty  from trims_production_mst a,trims_production_dtls b,trims_job_card_dtls c where a.id=b.mst_id and c.id=b.job_dtls_id $trimsjobid_cond $trimsreceiveid_cond and a.entry_form=269
	and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0"; 
	$trims_production_data_arr=array();	
	$result_trims_production_sql = sql_select($trims_production_sql);
	foreach($result_trims_production_sql as $row)
	{
		$key=$row[csf("received_id")].'*'.$row[csf("section_id")].'*'.$row[csf("sub_section")].'*'.$row[csf("item_description")].'*'.$row[csf("color_id")].'*'.$row[csf("size_id")].'*'.$row[csf("uom")];
		
		$trims_production_data_arr[$key][qc_qty]+=$row[csf("qc_qty")];
		$trims_production_data_arr[$key][job_quantity]+=$row[csf("job_quantity")];
	}	
		
	//Delivery.................................
	//$trims_delivery_sql="select a.delivery_date,a.received_id,b.delevery_qty,b.order_receive_rate,c.uom,b.section,c.sub_section,b.item_group,b.color_id,b.size_id,b.description,b.delevery_status,b.break_down_details_id,c.id from trims_delivery_mst a, trims_delivery_dtls b, trims_job_card_dtls c  where a.id=b.mst_id and c.id=b.job_dtls_id $trimsreceiveid_cond  and a.entry_form=208 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.delivery_date"; 
	$trims_delivery_sql="select a.delivery_date,a.received_id,b.delevery_qty,b.order_receive_rate,c.booked_uom as uom,b.section,c.sub_section,b.item_group,b.color_id,b.size_id,b.description,b.delevery_status,b.break_down_details_id,c.id from trims_delivery_mst a, trims_delivery_dtls b, subcon_ord_dtls c where a.id=b.mst_id and c.id=b.receive_dtls_id $trimsreceiveid_cond  and a.entry_form=208 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.delivery_date"; 
	$trims_delivery_data_arr=array();	
	$result_trims_delivery_sql = sql_select($trims_delivery_sql);
	foreach($result_trims_delivery_sql as $row)
	{
		$key=$row[csf("item_group")].'*'.$row[csf("received_id")].'*'.$row[csf("section")].'*'.$row[csf("sub_section")].'*'.$row[csf("description")].'*'.$row[csf("color_id")].'*'.$row[csf("size_id")].'*'.$row[csf("uom")];
		//c.id
		
		$trims_delivery_data_arr[$key][delevery_qty]+=$row[csf("delevery_qty")];
		$trims_delivery_data_arr[$key][delevery_val]+=$row[csf("delevery_qty")]*$row[csf("order_receive_rate")];
		$trims_delivery_data_arr[$key][delevery_last_date]=$row[csf("delivery_date")];
		$trims_delivery_data_arr[$key][id]=$row[csf("id")];
		$trims_delivery_data_arr[$key][delevery_status]=$row[csf("delevery_status")];
		$trims_delivery_data_arr[$key][break_ids].=$row[csf("break_down_details_id")].',';
	}
	//echo "<pre>";
	//print_r($trims_delivery_data_arr);

	$trims_bill_sql="select c.mst_id as received_id,c.section,c.sub_section,b.item_description,b.color_id,b.size_id,c.booked_uom as uom ,total_delv_qty,b.quantity,b.bill_amount,b.id from trims_bill_mst d, trims_bill_dtls b, subcon_ord_dtls c, subcon_ord_mst a where d.id=b.mst_id and c.mst_id=a.id and c.order_no=b.order_no and d.entry_form=276 and a.entry_form=255 and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.mst_id ,received_id,c.section,c.sub_section,b.item_description,b.color_id,b.size_id,c.booked_uom ,total_delv_qty,b.quantity,b.bill_amount,b.id";
	$trims_bill_data_arr=array();	
	$result_trims_bill_sql = sql_select($trims_bill_sql);
	foreach($result_trims_bill_sql as $row)
	{
		$key=$row[csf("received_id")].'*'.$row[csf("section")].'*'.$row[csf("sub_section")].'*'.$row[csf("item_description")].'*'.$row[csf("color_id")].'*'.$row[csf("size_id")].'*'.$row[csf("uom")];
		
		$trims_bill_data_arr[$key][bill_qty]+=$row[csf("quantity")];
		$trims_bill_data_arr[$key][bill_val]+=$row[csf("bill_amount")];
		$trims_bill_data_arr[$key][dtls_ids].=$row[csf("id")].',';
	}

	$iss_sql= "SELECT a.req_id,b.prod_id, sum(b.cons_quantity) as cons_quantity from inv_issue_master a, inv_transaction b where a.id=b.mst_id and b.transaction_type=2 and a.issue_basis=15 and a.entry_form=265 and a.status_active=1 and b.status_active=1 group by a.req_id,b.prod_id";
	$iss_data_array=sql_select($iss_sql);
	foreach($iss_data_array as $row){
		$issue_qty_arr[$row[csf("req_id")]][$row[csf("prod_id")]]+=$row[csf("cons_quantity")];
	}
	// and a.req_id=$data[2]
	
	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	/*echo "<pre>";
	print_r($issue_qty_arr);*/
	$width=2100;
	ob_start();
	?>
	<div align="center" style="height:auto; width:<? echo $width+20;?>px; margin:0 auto; padding:0;">
		<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="caption" align="center">
			<thead class="form_caption" >
				<tr>
					<td colspan="19" align="center" style="font-size:20px;"><? echo $companyArr[$cbo_company_id]; ?></td>
				</tr>
				<tr>
					<td colspan="19" align="center" style="font-size:14px; font-weight:bold" ><? echo $report_title; ?></td>
				</tr>
				<tr>
					<td colspan="19" align="center" style="font-size:14px; font-weight:bold">
						<? echo " From : ".$txt_date_from." To : ". $txt_date_to ;?>
					</td>
				</tr>
			</thead>
			<table cellspacing="0" cellpadding="0" width="<? echo $width;?>" class="rpt_table" rules="all" border="1">
			  <thead>
				<tr>
					<th align="canter" ><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
				</tr>
			 </head>
	       </table>
		</table>
        <table  border="1" cellpadding="0" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
			<thead>
                <th width="35">SL</th>
                <th width="100">Raw Material</th>
                <th width="60">Material UOM</th>
                <th width="100">Total Req.Qty</th>
                <th width="100">Material Issued Qty</th>
                <th width="100">Issued Balance Qty</th>
                <th width="120">Job Card</th>
                <th width="100">Work Order</th>
                <th width="100">Customer Name</th>
                <th width="100">Cust. Buyer</th>
                <th width="100">Trims Ref.</th>
                <th width="100">Internal Ref.</th>
                <th width="100">Item Name</th>
                <th width="200">Item Description</th>
                <th width="100">Item Color</th>
                <th width="60">Item UOM</th>
                <th width="100">Order Qty</th>
                <th width="100">Prod. Qty</th>
                <th width="100">Prod. Bal.Qty</th>
                <th width="100">Deli. Qty</th>
                <th>Deli. Bal. Qty</th>
			</thead>
		</table>
		<div style="width:<? echo $width+18;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
		<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="<? echo $width;?>" rules="all" align="left">
            <? 
			$i=1;
			$total_order_qty=0;$total_order_val=0;$total_booked_qty=0;$total_production_qty=0;
			$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
			foreach($date_array as $rcv_id=> $rcv_id_data)
			{
				$rcv_id_rowspan=0;
				foreach($rcv_id_data as $order_id=> $order_id_data)
				{
					$order_id_rowspan=0;	
					foreach($order_id_data as $item_group_id=> $item_group_data)
					{
						$item_group_rowspan=0;	
						foreach($item_group_data as $item_id=> $item_id_data)
						{
							$item_rowspan=0;
							foreach($item_id_data as $description=> $description_data)
							{
								$description_rowspan=0;	
								foreach($description_data as $color_id=> $color_id_data)
								{
									$color_id_rowspan=0;
									foreach($color_id_data as $order_uom=> $order_uom_data)
									{
										$order_uom_rowspan=0;
										foreach($order_uom_data as $job_id=> $job_id_data)
										{
											$job_id_rowspan=0;
											foreach($job_id_data as $row_desc=> $row_desc_data)
											{
												$row_desc_rowspan=0;
												foreach($row_desc_data as $unit=> $row)
												{
													if($row['within_group']==1){
															$party_name=$companyArr[$row['party_id']];
															$buyer_buyer=$party_arr[$row['buyer_buyer']] ;
													}else{
														$party_name=$party_arr[$row['party_id']]; 
														$buyer_buyer=$row['buyer_buyer'] ;
													}

													$key=$rcv_id.'*'.$row['section_id'].'*'.$row['sub_section'].'*'.$description.'*'.$color_id.'*'.$row['size_id'].'*'.$order_uom;

													$order_qty=$row['order_qty']*$row['booked_conv_fac'];
		
													$production_qty=$trims_production_data_arr[$key][qc_qty];
													$prod_bal=$order_qty-$production_qty;
													$del_qty=$trims_delivery_data_arr[$item_group_id.'*'.$key][delevery_qty]*$row['booked_conv_fac'];
													$del_bal=$order_qty-$del_qty;
													$req_qty=$row['req_qty'];
													$issued_qty=$issue_qty_arr[$job_id][$row['product_id']];
													$issued_bal=$req_qty-$issued_qty;
													$internal_no=$buyer_po_arr[$row['buyer_po_id']]['grouping'];

													/*$job_Qnty=0;
													for($j=0; $j<count($subcon_break_ids); $j++)
													{
														$job_Qnty +=$job_qnty_arr[$subcon_break_ids[$j]][$row[csf("conv_factor")]]['qnty'];
													}*/

													/*$order_key=$rcv_id.'*'.$order_id.'*'.$item_group_id.'*'.$item_id.'*'.$description.'*'.$color_id.'*'.$order_uom;
		
													$break_ids=$trims_data_arr[$order_key][break_ids];*/


													?>
													<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="left">
														<td width="35"><p><?  echo $i; ?></p></td>
														<? if($row_desc_rowspan==0){ ?> <td width="100" rowspan="<? echo $row_desc_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description][$color_id][$order_uom][$job_id][$row_desc]; ?>"><p><?  echo $row_desc ; ?></p></td>
														 <td width="60" rowspan="<? echo $row_desc_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description][$color_id][$order_uom][$job_id][$row_desc]; ?>"><p><?  echo $unit_of_measurement[$unit]; ?></p></td>
														 <td align="right" width="100" rowspan="<? echo $row_desc_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description][$color_id][$order_uom][$job_id][$row_desc]; ?>" ><p><?  echo $req_qty ; ?></p></td>
														<td align="right" width="100" rowspan="<? echo $row_desc_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description][$color_id][$order_uom][$job_id][$row_desc]; ?>" ><p><?  echo $issued_qty ; ?></p></td>
														<td align="right" width="100" rowspan="<? echo $row_desc_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description][$color_id][$order_uom][$job_id][$row_desc]; ?>" ><p><?  echo $issued_bal ; ?></p></td>
													<? } ?>

														<? if($job_id_rowspan==0){ ?> <td width="120" rowspan="<? echo $job_id_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description][$color_id][$order_uom][$job_id]; ?>"><p><?  echo $row['job_card_no_mst']; ?></p></td><? } ?>

														<? if($rcv_id_rowspan==0){ ?> <td width="100" rowspan="<? echo $rcv_rowspan_arr[$rcv_id]; ?>"><p><?  echo $row['cust_order_no'] ; ?></p></td><? } ?>
														<? if($rcv_id_rowspan==0){ ?> <td width="100" rowspan="<? echo $rcv_rowspan_arr[$rcv_id]; ?>"><p><?  echo $party_name  ; ?></p></td><? } ?>
														<? if($rcv_id_rowspan==0){ ?> <td width="100" rowspan="<? echo $rcv_rowspan_arr[$rcv_id]; ?>"><p><?  echo $buyer_buyer  ; ?></p></td><? } ?>
														<? if($rcv_id_rowspan==0){ ?> <td width="100" rowspan="<? echo $rcv_rowspan_arr[$rcv_id]; ?>"><p><?  echo $row['trims_ref']  ; ?></p></td><? } ?>
														<? if($rcv_id_rowspan==0){ ?> <td width="100" rowspan="<? echo $rcv_rowspan_arr[$rcv_id]; ?>"><p><?  echo $internal_no  ; ?></p></td><? } ?>
														
														<? if($item_group_rowspan==0){ ?> <td width="100" rowspan="<? echo $item_group_rowspan_arr[$rcv_id][$order_id][$item_group_id]; ?>"><p><?  echo $trimsGroupArr[$item_group_id] ; ?></p></td><? } ?>
														<? if($description_rowspan==0){ ?> <td width="200" rowspan="<? echo $description_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description]; ?>"><p><?  echo $description; ?></p></td><? } ?>
														<? if($color_id_rowspan==0){ ?> <td width="100" rowspan="<? echo $color_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description][$color_id]; ?>"><p><?  echo $color_arr[$color_id]; ?></p></td><? } ?>
														<? if($order_uom_rowspan==0){ ?> <td width="60" rowspan="<? echo $order_uom_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description][$color_id][$order_uom]; ?>"><p><?  echo $unit_of_measurement[$order_uom]  ; ?></p></td><? } ?>
														<? if($order_uom_rowspan==0){ ?> <td align="right" width="100" rowspan="<? echo $order_uom_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description][$color_id][$order_uom]; ?>"><p><?  echo number_format($order_qty) ; ?></p></td><? } ?>
														
														<? if($order_uom_rowspan==0){ ?> <td align="right" width="100" rowspan="<? echo $order_uom_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description][$color_id][$order_uom]; ?>"><p><?  echo $production_qty ; ?></p></td><? } ?>
														<? if($order_uom_rowspan==0){ ?> <td align="right" width="100" rowspan="<? echo $order_uom_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description][$color_id][$order_uom]; ?>"><p><?  echo $prod_bal ; ?></p></td><? } ?>
														<? if($order_uom_rowspan==0){ ?> <td align="right" width="100" rowspan="<? echo $order_uom_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description][$color_id][$order_uom]; ?>"><p><?  echo number_format( $del_qty) ; ?></p></td><? } ?>
														<? if($order_uom_rowspan==0){ ?> <td align="right" rowspan="<? echo $order_uom_rowspan_arr[$rcv_id][$order_id][$item_group_id][$item_id][$description][$color_id][$order_uom]; ?>"><p><?  echo number_format($del_bal) ; ?></p></td><? } ?>
														<!-- Order Qty   Prod. Qty  Prod. Bal.Qty   Deli. Qty    Deli. Bal. Qty -->
													</tr>
													<?
													$order_id_rowspan++;
													$item_group_rowspan++;
													$item_rowspan++;
													$description_rowspan++;
													$color_id_rowspan++;
													$order_uom_rowspan++;
													$job_id_rowspan++;
													$row_desc_rowspan++;
													$rcv_id_rowspan++;
													$i++;
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}?>
		</table>
		</div>
		<table width="<? echo $width;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_footer" align="left">
			<tfoot>
                <th width="35"></th>
                <th width="100"></th>
                <th width="60"></th>
                <th width="100"></th>
                <th width="100"></th>
                <th width="100"></th>
                <th width="120"></th>
                <th width="100"></th>
                <th width="100"></th>
                <th width="100"></th>
                <th width="100"></th>
                <th width="100"></th>
                <th width="100"></th>
                <th width="200"></th>
                <th width="100"></th>
                <th width="60"></th>
                <th width="100"></th>
                <th width="100"></th>
                <th width="100"></th>
                <th width="100"></th>
                <th></th>
			</tfoot>
		</table>
    </div>
    <?
    
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
    echo "$html**$filename**$report_type";
    exit();
}



if($action=="delivery_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	//$item_group_name = return_field_value("item_name","lib_item_group","id=".$product_sql[0][csf("item_group_id")],"item_name");	?>
    <table width="480" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all">
    	<thead>
        	<tr> 					
            	<th width="30">S.L</th>
                <th width="100">System ID</th>
                <th width="100">Challan No</th>
                <th width="100">Delivery Date</th>
                <th width="70">Delivery  Qty</th>
                <th>Delivery  Amount</th>
            </tr>
        </thead>
        <tbody>
		<?
		$details_sql="select a.trims_del, a.challan_no, a.delivery_date,sum(b.delevery_qty) as delevery_qty from trims_delivery_mst a, trims_delivery_dtls b, trims_job_card_dtls c  where a.id=b.mst_id and c.id=b.job_dtls_id and b.break_down_details_id  in(".chop($ids,',').")  and a.entry_form=208 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.trims_del, a.challan_no, a.delivery_date";
		//echo $details_sql;
		$sql_result=sql_select($details_sql); $t=1;
		foreach($sql_result as $row)
		{
			if ($t%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$delevery_amt=$row[csf("delevery_qty")]*$rate; ?>
        	<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $t; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $t; ?>">
        		<td width="30"><p><? echo $t; ?>&nbsp;</p></td>
                <td width="100"><p><? echo $row[csf('trims_del')]; ?>&nbsp;</p></td>
                <td width="100"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                <td width="100"><p><? echo change_date_format($row[csf('delivery_date')]); ?>&nbsp;</p></td>
                <td width="70" align="right"><p><? echo number_format($row[csf('delevery_qty')],0); $total_delevery_qty+=$row[csf("delevery_qty")]; ?>&nbsp;</p></td>
                <td align="right"><p><? echo number_format($delevery_amt,2); $total_delevery_amt+=$delevery_amt; ?>&nbsp;</p></td>
            </tr>
            <?
			$t++;
		}
		?>
        </tbody>
        <tfoot>
        	<tr>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
               	<th >&nbsp;</th>
                <th >Total</th>
               	<th align="right"><? echo number_format($total_delevery_qty,0); ?></th>
              	<th align="right"><? echo number_format($total_delevery_amt,2); ?></th>
            </tr>
        </tfoot>
    </table>
    <?
}

if($action=="bill_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	//$item_group_name = return_field_value("item_name","lib_item_group","id=".$product_sql[0][csf("item_group_id")],"item_name");	?>
    <table width="480" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all">
    	<thead>
        	<tr> 					
            	<th width="30">S.L</th>
                <th width="100">System ID</th>
                <th width="100">Bill No</th>
                <th width="100">Bill Date</th>
                <th width="70">Bill  Qty</th>
                <th>Bill  Amount</th>
            </tr>
        </thead>
        <tbody>
		<?
		$details_sql="select a.trims_bill ,a.bill_date, a.bill_no, sum(b.quantity) as quantity from trims_bill_mst a, trims_bill_dtls b,trims_job_card_dtls c, trims_job_card_mst d  where a.id=b.mst_id and c.id=b.job_dtls_id and c.job_no_mst=d.trims_job and a.entry_form=276 and b.id in($ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.trims_bill ,a.bill_date, a.bill_no";
		$sql_result=sql_select($details_sql); $t=1;
		foreach($sql_result as $row)
		{
			echo $rate;
			if ($t%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$bill_amount=$row[csf("quantity")]*$rate; ?>
        	<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $t; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $t; ?>">
        		<td width="30"><p><? echo $t; ?>&nbsp;</p></td>
                <td width="100"><p><? echo $row[csf('trims_bill')]; ?>&nbsp;</p></td>
                <td width="100"><p><? echo $row[csf('bill_no')]; ?>&nbsp;</p></td>
                <td width="100"><p><? echo change_date_format($row[csf('bill_date')]); ?>&nbsp;</p></td>
                <td width="70" align="right"><p><? echo number_format($row[csf('quantity')],0); $total_bill_qty+=$row[csf("quantity")]; ?>&nbsp;</p></td>
                <td align="right"><p><? echo number_format($bill_amount,2); $total_bill_amt+=$bill_amount; ?>&nbsp;</p></td>
            </tr>
            <?
			$t++;
		}
		?>
        </tbody>
        <tfoot>
        	<tr>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
               	<th >&nbsp;</th>
                <th >Total</th>
               	<th align="right"><? echo number_format($total_bill_qty,0); ?></th>
              	<th align="right"><? echo number_format($total_bill_amt,2); ?></th>
            </tr>
        </tfoot>
    </table>
    <?
}
?>
