<?php
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action']; 

if ($action=="load_drop_down_buyer") {
	list($company,$type)=explode("_",$data);
	if($type==1) {
		echo create_drop_down( "cbo_customer_name", 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $company, "$load_function");
	} else {
		echo create_drop_down( "cbo_customer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", "", "" );
	}	
	exit();	 
}

if ($action=="load_drop_down_location") {
	echo create_drop_down( 'cbo_location_name', 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );
	exit();
}

if($action=="generate_report") {
	// reference OG-TB-20-00044, Akram-02012020, FAL-TB-20-00035, 7865
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$cbo_company_id=str_replace("'","", $cbo_company_id);
	$cbo_location_name=str_replace("'","", $cbo_location_name);
	$cbo_order_source=str_replace("'","", $cbo_order_source);
	$cbo_customer_name=str_replace("'","", $cbo_customer_name);
	$internal_no=str_replace("'","", $txt_internal_no);
	$cbo_section_id=str_replace("'","", $cbo_section_id);
	$cbo_delivery_status=str_replace("'","", $cbo_delivery_status);
	$txt_date_from=str_replace("'","", $txt_date_from);
	$txt_date_to=str_replace("'","", $txt_date_to);
	$txt_order_no=str_replace("'","", $txt_order_no);
	$txt_style_ref=str_replace("'","", $txt_style_ref);
	$report_type=str_replace("'","", $report_type);

	if($cbo_delivery_status== 0){$delivery_status_con="";}
	else{$delivery_status_con=" and c.delivery_status=$cbo_delivery_status";}
		
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
	$sizeArr = return_library_array("select id, size_name from lib_size where status_active=1 and is_deleted=0", 'id','size_name');
	
	$currency_rate=return_field_value( "conversion_rate", "currency_conversion_rate", "currency=2 and status_active=1  and id in(select max(id) from currency_conversion_rate where currency=2 and is_deleted=0 and status_active=1)" , "conversion_rate" );
	$where_con='';
	if($txt_date_from!="" and $txt_date_to!=""){
		$where_con.=" and a.receive_date between '$txt_date_from' and '$txt_date_to'";
		$prod_where_con.=" and a.production_date between '$txt_date_from' and '$txt_date_to'";
		// $qc_where_con.=" and a.receive_date between '$txt_date_from' and '$txt_date_to'";
		$delivery_where_con.=" and a.delivery_date between '$txt_date_from' and '$txt_date_to'";
		$bill_where_con.=" and d.bill_date between '$txt_date_from' and '$txt_date_to'";
		// else if($cbo_date_category==2){$where_con.=" and a.delivery_date between '$txt_date_from' and '$txt_date_to'";}
	}
	if($cbo_section_id){$where_cons.=" and b.section='$cbo_section_id'";} 
	if($cbo_order_source){$where_cons.=" and a.within_group='$cbo_order_source'";} 
	if($cbo_customer_name){$where_cons.=" and a.party_id='$cbo_customer_name'";}
	if($cbo_location_name){$where_cons.=" and a.location_id = $cbo_location_name";}
	
 	$buyer_po_id_cond = '';
    if($cbo_order_source==1 || $cbo_order_source==0)
    {
		if($internal_no !="") $internal_no_cond = " and grouping like('%$internal_no%')";
		
		$buyer_po_arr=array();
		$buyer_po_id_arr=array();
		$po_sql ="select id, grouping from wo_po_break_down  where is_deleted=0 and status_active=1 $internal_no_cond "; 
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
	$txt_style_cond="";
	if($txt_order_no !="") $order_no_cond = " and b.order_no like('%$txt_order_no%')";
	if($txt_style_ref !="") $txt_style_cond = " and b.buyer_style_ref like('%$txt_style_ref%')";
	
	//print_r($buyer_po_arr); die;

	$sql="SELECT  c.from_received_id, c.to_received_id, c.trim_group_id, c.section_id, c.quantity, c.from_order_no,c.sub_section_id, c.color_id, c.size_id, c.item_description,d.transfer_system_id, 1 as type 
	FROM  trims_item_transfer_dtls c, trims_item_transfer_mst d
	WHERE  d.id = c.mst_id and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0
	union all
	SELECT  c.from_received_id, c.to_received_id, c.trim_group_id, c.section_id, c.quantity, c.from_order_no,c.sub_section_id, c.color_id, c.size_id, c.item_description, d.transfer_system_id, 2 as type 
	FROM  trims_item_transfer_dtls c , trims_item_transfer_mst d
	WHERE  d.id = c.mst_id and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0";

	$finished_goods_order=array();
	$finished_goods_sql = sql_select($sql);
	foreach($finished_goods_sql as $row){
		if($row[csf("type")]==1){
		 $finished_goods_order[$row[csf("from_received_id")]][$row[csf("trim_group_id")]][$row[csf("color_id")]][$row[csf("size_id")]][$row[csf("item_description")]]["trans_out_qty"]+=$row[csf("quantity")];
		 $finished_goods_order[$row[csf("from_received_id")]][$row[csf("trim_group_id")]][$row[csf("color_id")]][$row[csf("size_id")]][$row[csf("item_description")]]["from_received_id"]=$row[csf("from_received_id")];
		 $finished_goods_order[$row[csf("from_received_id")]][$row[csf("trim_group_id")]][$row[csf("color_id")]][$row[csf("size_id")]][$row[csf("item_description")]]["to_received_id"]=$row[csf("to_received_id")];
		 $finished_goods_order[$row[csf("from_received_id")]][$row[csf("trim_group_id")]][$row[csf("color_id")]][$row[csf("size_id")]][$row[csf("item_description")]]["type"]=$row[csf("type")];
		 $finished_goods_order[$row[csf("from_received_id")]][$row[csf("trim_group_id")]][$row[csf("color_id")]][$row[csf("size_id")]][$row[csf("item_description")]]["transfer_system_id"]=$row[csf("transfer_system_id")];
		 $finished_goods_order[$row[csf("from_received_id")]][$row[csf("trim_group_id")]][$row[csf("color_id")]][$row[csf("size_id")]][$row[csf("item_description")]]["size_id"]=$row[csf("size_id")];
    	}
		else{ 
		 $finished_goods_order[$row[csf("to_received_id")]][$row[csf("trim_group_id")]][$row[csf("color_id")]][$row[csf("size_id")]][$row[csf("item_description")]]["trans_in_qty"]+=$row[csf("quantity")];
		 $finished_goods_order[$row[csf("to_received_id")]][$row[csf("trim_group_id")]][$row[csf("color_id")]][$row[csf("size_id")]][$row[csf("item_description")]]["from_received_id"]=$row[csf("from_received_id")];
		 $finished_goods_order[$row[csf("to_received_id")]][$row[csf("trim_group_id")]][$row[csf("color_id")]][$row[csf("size_id")]][$row[csf("item_description")]]["to_received_id"]=$row[csf("to_received_id")];
		 $finished_goods_order[$row[csf("to_received_id")]][$row[csf("trim_group_id")]][$row[csf("color_id")]][$row[csf("size_id")]][$row[csf("item_description")]]["type"]=$row[csf("type")];
		 $finished_goods_order[$row[csf("to_received_id")]][$row[csf("trim_group_id")]][$row[csf("color_id")]][$row[csf("size_id")]][$row[csf("item_description")]]["transfer_system_id"]=$row[csf("transfer_system_id")];
		 $finished_goods_order[$row[csf("to_received_id")]][$row[csf("trim_group_id")]][$row[csf("color_id")]][$row[csf("size_id")]][$row[csf("item_description")]]["size_id"]=$row[csf("size_id")];
		}
	}
	//echo "<pre>";print_r($finished_goods_order);die;
	// var_dump($finished_goods_order);

	$trims_order_sql="SELECT a.id,a.subcon_job,a.receive_date,a.delivery_date,a.order_no as cust_order_no,a.party_id,a.within_group,a.currency_id,a.team_leader,a.team_member,a.exchange_rate, b.item_group,b.buyer_buyer,b.section,b.sub_section,b.job_no_mst,b.order_no,b.booked_uom,b.order_uom,b.cust_style_ref, c.description,c.order_id,c.item_id,c.color_id,c.size_id,sum(c.qnty) as qnty,c.rate ,sum(c.amount) as amount,sum(c.booked_qty) as booked_qty,c.delivery_status, b.buyer_style_ref, a.trims_ref,b.booked_conv_fac
	from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c
	where a.subcon_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id and a.entry_form=255 and a.company_id =$cbo_company_id  $buyer_po_id_cond $order_no_cond $where_con $delivery_status_con $txt_style_cond $where_cons
	and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.is_deleted=0  group by a.id,a.subcon_job,a.receive_date,a.delivery_date,a.order_no,a.party_id,a.within_group,a.currency_id,a.team_leader,a.team_member,a.exchange_rate, b.item_group,b.buyer_buyer,b.section,b.sub_section,b.job_no_mst,b.order_no,b.booked_uom,b.order_uom,b.cust_style_ref, c.description,c.order_id,c.item_id,c.color_id,c.size_id,c.rate,c.delivery_status, b.buyer_style_ref, a.trims_ref,b.booked_conv_fac"; 
	//echo $trims_order_sql; //die;
	$trims_receive_id_arr=array();
	$trims_data_arr=array();	
	$result_trims_order_sql = sql_select($trims_order_sql);

	foreach($result_trims_order_sql as $row)
	{
		$key=$row[csf("id")].'*'.$row[csf("order_id")].'*'.$row[csf("section")].'*'.$row[csf("sub_section")].'*'.$row[csf("item_group")].'*'.$row[csf("item_id")].'*'.$row[csf("description")].'*'.$row[csf("color_id")].'*'.$row[csf("size_id")].'*'.$row[csf("booked_uom")].'*'.$row[csf("rate")];

		//Customer Name  	Cust Buyer	Cust. Work Order	Trims Job No	Style	Internal Ref	Section	Item Group	Order UOM	Item Description	Item Color
		//echo $rowspan_key=$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]]; die;
		//$data_array[$conRow[csf('cont_id')]][$conRow[csf('con_dtls_id')]][$conRow[csf('pi_id')]][$conRow[csf('pi_dtls_id')]]['contract_no_date'] =...
		
		$trims_data_arr[$key][qnty]+=$row[csf("qnty")];
		$trims_data_arr[$key][amount]+=$row[csf("amount")];
		$trims_data_arr[$key][booked_qty]+=$row[csf("booked_qty")];
		//$trims_data_arr[$key][break_ids].=$row[csf("break_id")].',';

		
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['subcon_job']=$row[csf("subcon_job")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['id']=$row[csf("id")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['order_id']=$row[csf("order_id")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['item_id']=$row[csf("item_id")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['color_id']=$row[csf("color_id")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['item_group']=$row[csf("item_group")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['receive_date']=$row[csf("receive_date")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['delivery_date']=$row[csf("delivery_date")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['order_qty']=$trims_data_arr[$key][qnty];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['order_rate']=$row[csf("rate")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['order_amount']=$trims_data_arr[$key][amount];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['booked_qty']=$trims_data_arr[$key][booked_qty];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['cust_order_no']=$row[csf("cust_order_no")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['party_id']=$row[csf("party_id")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['within_group']=$row[csf("within_group")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['buyer_buyer']=$row[csf("buyer_buyer")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['section']=$row[csf("section")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['sub_section']=$row[csf("sub_section")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['booked_uom']=$row[csf("booked_uom")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['order_uom']=$row[csf("order_uom")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['description']=$row[csf("description")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['delivery_status']=$row[csf("delivery_status")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['order_no']=$row[csf("order_no")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['buyer_po_no']=$row[csf("buyer_po_no")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['currency_id']=$row[csf("currency_id")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['team_leader']=$row[csf("team_leader")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['team_member']=$row[csf("team_member")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['break_ids']=$row[csf("break_ids")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['buyer_po_id']=$row[csf("buyer_po_id")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['style']=$row[csf("cust_style_ref")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['size_id']=$row[csf("size_id")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['exchange_rate']=$row[csf("exchange_rate")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['rate']=$row[csf("rate")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['buyer_style_ref']=$row[csf("buyer_style_ref")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['trims_ref']=$row[csf("trims_ref")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['booked_conv_fac']=$row[csf("booked_conv_fac")];
		
		$trims_receive_id_arr[$row[csf("id")]]=$row[csf("id")];
	}

	$data_array_new=array();
	foreach($result_trims_order_sql as $row)
	{
		$key=$row[csf("id")].'*'.$row[csf("order_id")].'*'.$row[csf("section")].'*'.$row[csf("sub_section")].'*'.$row[csf("item_group")].'*'.$row[csf("item_id")].'*'.$row[csf("description")].'*'.$row[csf("color_id")].'*'.$row[csf("size_id")].'*'.$row[csf("booked_uom")].'*'.$row[csf("rate")];
		
		$trims_data_arr[$key][qnty]+=$row[csf("qnty")];
		$trims_data_arr[$key][amount]+=$row[csf("amount")];
		$trims_data_arr[$key][booked_qty]+=$row[csf("booked_qty")];
		//$trims_data_arr[$key][break_ids].=$row[csf("break_id")].',';
		$data_array_new[$row[csf("currency_id")]][$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['subcon_job']=$row[csf("subcon_job")];
		$data_array_new[$row[csf("currency_id")]][$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['id']=$row[csf("id")];
		$data_array_new[$row[csf("currency_id")]][$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['order_id']=$row[csf("order_id")];
		$data_array_new[$row[csf("currency_id")]][$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['item_id']=$row[csf("item_id")];
		$data_array_new[$row[csf("currency_id")]][$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['color_id']=$row[csf("color_id")];
		$data_array_new[$row[csf("currency_id")]][$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['item_group']=$row[csf("item_group")];
		$data_array_new[$row[csf("currency_id")]][$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['receive_date']=$row[csf("receive_date")];
		$data_array_new[$row[csf("currency_id")]][$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['delivery_date']=$row[csf("delivery_date")];
		$data_array_new[$row[csf("currency_id")]][$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['order_qty']=$row[csf("qnty")];
		// $data_array_new[$row[csf("currency_id")]][$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['order_qty']=$trims_data_arr[$key]["qnty"];
		$data_array_new[$row[csf("currency_id")]][$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['order_rate']=$row[csf("rate")];
		$data_array_new[$row[csf("currency_id")]][$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['order_amount']=$trims_data_arr[$key]["amount"];
		// $data_array_new[$row[csf("currency_id")]][$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['booked_qty']=$trims_data_arr[$key]["booked_qty"];
		$data_array_new[$row[csf("currency_id")]][$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['booked_qty']=$row[csf("booked_qty")];
		$data_array_new[$row[csf("currency_id")]][$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['cust_order_no']=$row[csf("cust_order_no")];
		$data_array_new[$row[csf("currency_id")]][$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['party_id']=$row[csf("party_id")];
		$data_array_new[$row[csf("currency_id")]][$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['within_group']=$row[csf("within_group")];
		$data_array_new[$row[csf("currency_id")]][$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['buyer_buyer']=$row[csf("buyer_buyer")];
		$data_array_new[$row[csf("currency_id")]][$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['section']=$row[csf("section")];
		$data_array_new[$row[csf("currency_id")]][$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['sub_section']=$row[csf("sub_section")];
		$data_array_new[$row[csf("currency_id")]][$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['booked_uom']=$row[csf("booked_uom")];
		$data_array_new[$row[csf("currency_id")]][$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['order_uom']=$row[csf("order_uom")];
		$data_array_new[$row[csf("currency_id")]][$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['description']=$row[csf("description")];
		$data_array_new[$row[csf("currency_id")]][$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['delivery_status']=$row[csf("delivery_status")];
		$data_array_new[$row[csf("currency_id")]][$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['order_no']=$row[csf("order_no")];
		$data_array_new[$row[csf("currency_id")]][$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['buyer_po_no']=$row[csf("buyer_po_no")];
		$data_array_new[$row[csf("currency_id")]][$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['currency_id']=$row[csf("currency_id")];
		$data_array_new[$row[csf("currency_id")]][$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['team_leader']=$row[csf("team_leader")];
		$data_array_new[$row[csf("currency_id")]][$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['team_member']=$row[csf("team_member")];
		$data_array_new[$row[csf("currency_id")]][$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['break_ids']=$row[csf("break_ids")];
		$data_array_new[$row[csf("currency_id")]][$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['buyer_po_id']=$row[csf("buyer_po_id")];
		$data_array_new[$row[csf("currency_id")]][$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['style']=$row[csf("cust_style_ref")];
		$data_array_new[$row[csf("currency_id")]][$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['size_id']=$row[csf("size_id")];
		$data_array_new[$row[csf("currency_id")]][$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['exchange_rate']=$row[csf("exchange_rate")];
		$data_array_new[$row[csf("currency_id")]][$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['rate']=$row[csf("rate")];
		$data_array_new[$row[csf("currency_id")]][$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['buyer_style_ref']=$row[csf("buyer_style_ref")];
		$data_array_new[$row[csf("currency_id")]][$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['trims_ref']=$row[csf("trims_ref")];
		$data_array_new[$row[csf("currency_id")]][$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['booked_conv_fac']=$row[csf("booked_conv_fac")];

		$trims_receive_id_arr[$row[csf("id")]]=$row[csf("id")];
	}
	/*echo "<pre>";
	print_r($data_array); die;*/
	/*echo '<pre>';
	print_r($trims_data_arr);
	echo '</pre>';
	die;*/
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
	$trims_job_sql="select a.id,a.trims_job,a.received_no,to_char(insert_date,'DD-MM-YYYY') as  insert_date from trims_job_card_mst a where a.status_active=1 and a.is_deleted=0 $trimsreceiveid_cond "; 
	//echo $trims_job_sql;
	$trims_job_no_arr=array();	
	$trims_job_date_arr=array();	
	$result_trims_job_sql = sql_select($trims_job_sql);
	foreach($result_trims_job_sql as $row)
	{
		$trims_job_no_arr[$row[csf("received_no")]]=$row[csf("trims_job")];
		$trims_job_date_arr[$row[csf("received_no")]]=$row[csf("insert_date")];
		$trims_job_id_arr[$row[csf("id")]]=$row[csf("id")];
	}
		
		
		
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
		
	//production.................................
	$trims_production_sql="SELECT a.section_id,c.sub_section,a.received_id,a.job_id,b.job_dtls_id,c.id as job_card_dtls_id,c.item_description,c.color_id,c.size_id,c.uom,c.job_quantity,c.break_id,b.production_qty,b.qc_qty,b.reject_qty, c.buyer_style_ref, c.gmts_size_id, c.gmts_color_id
	from trims_production_mst a,trims_production_dtls b,trims_job_card_dtls c
	where a.id=b.mst_id and c.id=b.job_dtls_id $trimsjobid_cond $trimsreceiveid_cond and a.entry_form=269 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";

	//echo $trims_production_sql; exit();
	// subcon_ord_mst

	$trims_production_data_arr=array();	
	$result_trims_production_sql = sql_select($trims_production_sql);
	foreach($result_trims_production_sql as $row)
	{
		$key=$row[csf("received_id")].'*'.$row[csf("section_id")].'*'.$row[csf("sub_section")].'*'.$row[csf("item_description")].'*'.$row[csf("color_id")].'*'.$row[csf("size_id")].'*'.$row[csf("uom")];
		
		$trims_production_data_arr[$key][qc_qty]+=$row[csf("qc_qty")];
		$trims_production_data_arr[$key][job_quantity]+=$row[csf("job_quantity")];
		$trims_production_data_arr[$key][production_qty]+=$row[csf("production_qty")];
		$trims_production_data_arr[$key][reject_qty]+=$row[csf("reject_qty")];
		//$trims_production_data_arr[$key][production_qty]+=$row[csf("production_qty")];
		$trims_production_data_arr[$key][job_dtls_id]=$row[csf("job_dtls_id")];
		$trims_production_data_arr[$key][buyer_style_ref]=$row[csf('buyer_style_ref')];
		$trims_production_data_arr[$key][gmts_size_id]=$row[csf('gmts_size_id')];
		$trims_production_data_arr[$key][gmts_color_id]=$row[csf('gmts_color_id')];
	}
	
	//Delivery.................................
	

	$trims_delivery_sql="select a.id as mst_id, a.delivery_date,a.received_id,a.trims_del,b.delevery_qty,b.order_receive_rate,c.booked_uom as uom,b.section,c.sub_section,b.item_group,b.color_id,b.size_id,b.description,b.delevery_status,b.break_down_details_id,c.id from trims_delivery_mst a, trims_delivery_dtls b, subcon_ord_dtls c where a.id=b.mst_id and c.id=b.receive_dtls_id $trimsreceiveid_cond  and a.entry_form=208 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.delivery_date"; 

	//echo $trims_delivery_sql;

	$trims_delivery_data_arr=array();
	$trims_del_no_arr=array();
	$result_trims_delivery_sql = sql_select($trims_delivery_sql);
	foreach($result_trims_delivery_sql as $row)
	{
		$key=$row[csf("item_group")].'*'.$row[csf("received_id")].'*'.$row[csf("section")].'*'.$row[csf("sub_section")].'*'.$row[csf("description")].'*'.$row[csf("color_id")].'*'.$row[csf("size_id")].'*'.$row[csf("uom")];
		
		$trims_delivery_data_arr[$key][delevery_qty]+=$row[csf("delevery_qty")];
		$trims_delivery_data_arr[$key][trims_del]=$row[csf("trims_del")];
		$trims_delivery_data_arr[$key][delevery_val]+=$row[csf("delevery_qty")]*$row[csf("order_receive_rate")];
		$trims_delivery_data_arr[$key][delevery_last_date]=$row[csf("delivery_date")];
		$trims_delivery_data_arr[$key][delevery_status]=$row[csf("delevery_status")];
		$trims_delivery_data_arr[$key][break_ids].=$row[csf("break_down_details_id")].',';

		$trims_delivery.="'".$row[csf("mst_id")]."',";
	}
	$trims_delivery_nos=implode(',',array_unique(explode(',',rtrim($trims_delivery,','))));
	// echo "<pre>";
	// print_r($trims_del_no_arr);
	// echo "</pre>";

	//Gate Pass.................................
	
	$sql_get_pass_search = "select a.id, a.sys_number, a.challan_no,b.challan_details_id
	from inv_gate_pass_mst a,inv_gate_pass_dtls b
	where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and (a.is_gate_out!=1 or a.is_gate_out is null) and a.basis=50 and a.issue_id in($trims_delivery_nos)
	order by sys_number desc";//$outIds_cond

	//echo $sql_get_pass_search;

	$trims_challan_no_arr=array();	
	$result_sql_get_pass_search = sql_select($sql_get_pass_search);
	foreach($result_sql_get_pass_search as $row)
	{
		$trims_challan_no_arr[$row[csf("challan_no")]]=$row[csf("sys_number")];
	}

	//Bill.................................
	//select SECTION,ITEM_DESCRIPTION,COLOR_ID,SIZE_ID,ORDER_UOM,TOTAL_DELV_QTY,b.QUANTITY     from TRIMS_BILL_MST a, TRIMS_BILL_dtls b 


	$trims_bill_sql="select c.mst_id as received_id,c.section,c.sub_section,b.item_description,b.color_id,b.size_id,c.booked_uom as uom ,total_delv_qty,b.quantity,b.bill_amount,b.id from trims_bill_mst d, trims_bill_dtls b, subcon_ord_dtls c, subcon_ord_mst a where d.id=b.mst_id and c.mst_id=a.id and c.order_no=b.order_no and d.entry_form=276 and a.entry_form=255  $bill_where_con and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and d.status_active=1 and d.is_deleted=0";

	//echo $trims_bill_sql;

	$trims_bill_data_arr=array();
	$result_trims_bill_sql = sql_select($trims_bill_sql);
	foreach($result_trims_bill_sql as $row)
	{
		$key=$row[csf("received_id")].'*'.$row[csf("section")].'*'.$row[csf("sub_section")].'*'.$row[csf("item_description")].'*'.$row[csf("color_id")].'*'.$row[csf("size_id")].'*'.$row[csf("uom")];
		// $id.'*'.$sectionId.'*'.$sub_section.'*'.$description.'*'.$colorId.'*'.$sizeId.'*'.$booked_uom;
		
		$trims_bill_data_arr[$key][bill_qty]=$row[csf("quantity")];
		$trims_bill_data_arr[$key][bill_val]=$row[csf("bill_amount")];
		$trims_bill_data_arr[$key][dtls_ids].=$row[csf("id")].',';
	}
	
	
	$width=4785;
	ob_start();
	?>

		<?php
		if($report_type==1)
		{
			?>
			<div align="center" style="height:auto; width:<? echo $width+20;?>px; margin:0 auto; padding:0;">
	    	<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="caption" align="center">
				<thead class="form_caption" >
					<tr>
						<td colspan="36" align="center" style="font-size:20px;"><?php echo $companyArr[$cbo_company_id]; ?></td>
					</tr>
					<tr>
						<td colspan="36" align="center" style="font-size:14px; font-weight:bold" ><?php echo $report_title; ?></td>
					</tr>
					<tr>
						<td colspan="36" align="center" style="font-size:14px; font-weight:bold">
							<?php echo " From : ".$txt_date_from." To : ". $txt_date_to ;?>
						</td>
					</tr>
				</thead>
			</table>
		
			<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
				<thead>
					<th width="35" >SL</th>
	                <th width="120" >Customer Name</th>
	                <th width="120" >Cust. Buyer</th>
	                <th width="120" >Cust. Work Order</th>
	                <th width="120" >Work Order Date</th>
	                <th width="120" >Delivery Date</th>
	                <th width="120" >Trims Job No</th>
	                <th width="120" >Style</th>
	                <th width="120" >Trims Ref</th>
	                <th width="120" >Section</th>
	                <th width="120" >Item Group</th>
	                <th width="150" >Item Description</th>
	                <th width="120" >Item Color</th>
	                <th width="120" >Item Size</th>
	                <th width="120" >Order UOM</th>
	                <th width="120" >Req. Qty/Ord Rcv</th>
	                <th width="120" >Booked UOM</th>
	                <th width="120" >Booked Qty</th>
	                <th width="120" >Rate</th>
	                <th width="120" >Ord Rcv Currency</th>
	                <th width="120" >Req. Value [Tk]</th>
	                <th width="160" >Job Card No.</th>
	                <th width="120" >Job Card Date</th>
	                <th width="120" >Prod. Qty</th>
	                <th width="120" >Prod. Value [Tk]</th>
	                <th width="120" >Prod. Bal. Qty</th>
	                <th width="120" >Prod. Bal. Value [Tk]</th>
	                <th width="120" >Reject Qty.</th>
	                <th width="120" >Reject Value.</th>
	                <th width="120" >QC. Qty</th>
	                <th width="120" >QC. Bal. Qty</th>
	                <th width="120" >Deli. Qty.</th>
	                <th width="120" >Deli. Value [Tk]</th>
	                <th width="120" >Deli. Balance Qty</th>
	                <th width="120" >Deli. Balance Value [Tk]</th>
	                <th width="120" >Gate Pass</th>
	                <th width="120" >Bill Qty</th>
	                <th width="120" >Bill Amount [Tk]</th>
	                <th width="120" >Bill Balance Qty</th>
	                <th width="120" >Bill Balance Amount [Tk] </th>
				</thead>
				
			</table>
		
				<div style="width:<? echo $width+18;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
	        	<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="<? echo $width;?>" rules="all" align="left">
				<tbody>
					<?php 
					$i=1;
					$total_order_qty=0;$total_order_val=0;$total_booked_qty=0;$total_production_qty=0;

					$row_partyArray=$row_bBArray=$row_ordNoArray=$row_subcon_jobArray=$row_cusStyleArray=$row_buyerPoIdArray=$row_sectionArray=$row_itemGroupArray=$row_orderUomArray=$row_descriptionArray=$row_colorArray=array(); 

					
					$totalReqValueTk=0;
					$totalProdValueTk=0;
					$totalProdBalanceValueTk=0;
					$totalDeliveryValueTk=0;
					$totalDeliveryBalanceValueTk=0;
					$totalBillAmountTk=0;
					$totalBillBalanceAmountTk=0;
					foreach($data_array as $party_id =>$partyArray)
					{	
						foreach( $partyArray as $buyerBuyer =>$bBArray )
						{   	
							foreach( $bBArray as $ordNo =>$ordNoArray )
							{   	
								foreach( $ordNoArray as $subcon_job =>$subcon_jobArray )
								{
									foreach ($subcon_jobArray as $cusStyle => $cusStyleArray)
									{
										foreach ($cusStyleArray as $sectionId => $sectionArray)
										{

											foreach ($sectionArray as $itemGroup => $itemGroupArray)
											{
												foreach ($itemGroupArray as $orderUom => $orderUomArray)
												{
													foreach ($orderUomArray as $description => $descriptionArray)
													{
														foreach ($descriptionArray as $colorId => $colorIdArray)
														{
															foreach ($colorIdArray as $sizeId => $row)
															{
																
																$id=$row['id'];
																$sub_section=$row['sub_section'];
																//$sizeId=$row["size_id"];
																//$booked_uom=$row['booked_uom'];
																$booked_uom=$row['order_uom'];

																$key=$id.'*'.$sectionId.'*'.$sub_section.'*'.$description.'*'.$colorId.'*'.$sizeId.'*'.$booked_uom;
																$orderKey=$id.'*'.$row[order_id].'*'.$sectionId.'*'.$sub_section.'*'.$itemGroup.'*'.$row[item_id].'*'.$row[description].'*'.$row[color_id].'*'.$row[size_id].'*'.$row[booked_uom].'*'.$row[rate];
																//$key=$row[csf("received_id")].'*'.$row[csf("section_id")].'*'.$row[csf("sub_section")].'*'.$row[csf("item_description")].'*'.$row[csf("color_id")].'*'.$row[csf("size_id")].'*'.$row[csf("uom")];
																$production_qty_on_order_parcent=$trims_production_data_arr[$key][qc_qty];
																
																//WORK ORDER NO : 161
																$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
																$party=($row[within_group]==1)?$companyArr[$row[party_id]]:$buyerArr[$row[party_id]];
																$buyer_buyer=($row[within_group]==1)?$buyerArr[$row[buyer_buyer]]:$row[buyer_buyer];
															
																// $row[delivery_status]=$trims_delivery_data_arr[$item_group.'*'.$key][delevery_status];
																// if($row[delivery_status]==2){$bgcolor="#FFCC66";}
																// elseif($row[delivery_status]==3){$bgcolor="#8CD59C";}
																// else{$row[delivery_status]=1;}
																//---------------------------------------
																$exchangeRate = $row[exchange_rate];
																$orderAmount = $row[order_amount];
																$orderQty = $row[order_qty];
																$rate = $row[rate];
																$req_value_tk = $orderQty*$rate*$exchangeRate;
																$total_order_qty+=$orderQty;
																$total_order_val+=$orderAmount;
																$total_booked_qty+=$row[booked_qty];
																$total_production_qty+=$production_qty_on_order_parcent;
																$internal_ref = $buyer_po_arr[$row[buyer_po_id]]['grouping'];
																$section_name = $trims_section[$sectionId];
																$orderRate = $orderAmount / $orderQty;

																if(is_nan($orderRate) || is_infinite($orderRate)) $orderRate = 0;
																// $order_rate = !is_nan($order_rate) ? $order_rate : 0;
																$order_amt_tk = $orderAmount * $exchangeRate;
																$prod_qty = $trims_production_data_arr[$key][production_qty] ? $trims_production_data_arr[$key][production_qty] : 0;
																$reject_qty = $trims_production_data_arr[$key][reject_qty] ? $trims_production_data_arr[$key][reject_qty] : 0;
																$trims_ref = $row['trims_ref'];
																$prodValueTk = $prod_qty*$orderRate*$exchangeRate;
																$rejectValueTk = $reject_qty*$orderRate*$exchangeRate;
																$buyer_style_ref = $trims_production_data_arr[$key][buyer_style_ref] ? $trims_production_data_arr[$key][buyer_style_ref] : '';
																$prod_jobdtls_id = $trims_production_data_arr[$key][job_dtls_id];
																
																$prodBalanceQty = $row[booked_qty] - $prod_qty;
																$prodBalanceValTk = $prodBalanceQty*$rate*$exchangeRate;
																$qc_qty = $trims_production_data_arr[$key][qc_qty];
																$qcBalanceQty = $row[booked_qty] - $prod_qty;
																$delv_qty = $trims_delivery_data_arr[$itemGroup.'*'.$key][delevery_qty];
																$trims_del = $trims_delivery_data_arr[$itemGroup.'*'.$key][trims_del];
																$deliValueTk = $delv_qty*$rate*$exchangeRate;
																$delv_balance_qty = $orderQty - $delv_qty;
																$delv_balance_value = $delv_balance_qty*$rate*$exchangeRate;
																//echo $key;
																
																$bill_qty = $trims_bill_data_arr[$key][bill_qty];
																//echo $bill_qty1.'='.$key;
																$billBalanceQty = number_format( ($delv_qty - $bill_qty), 2 );
																$billBalanceAmtTk = $billBalanceQty*$rate*$exchangeRate;

																$delivery_amt=number_format($row[order_rate]*$trims_delivery_data_arr[$item_group.'*'.$key][delevery_qty], 2);
																$bill_amt=$rate*$trims_bill_data_arr[$key][bill_qty]*$exchangeRate;
																$receive_ids=implode(',',$trims_receive_id_arr);
																// $trims_delivery_data_arr[11*4673*5*6*GGG*6037*2*1];
																// $trims_bill_data_arr[3869*2*0*rew*6954*1*1];

																$totalReqValueTk += $req_value_tk;
																$totalProdValueTk += $prodValueTk;
																$totalDeliveryValueTk += $deliValueTk;
																$totalDeliveryBalanceValueTk += $delv_balance_value;
																$totalBillAmountTk += $bill_amt;
																$totalBillBalanceAmountTk += $billBalanceAmtTk;
																$totalProdBalanceValueTk += $prodBalanceValTk;
																
																if($row["currency_id"]==1){
																	$orderRate=$orderRate/$currency_rate;
																	$orderAmount=$orderAmount/$currency_rate;
																}
																//echo "select master_tble_id from common_photo_library where form_name='trims_order_receive' and master_tble_id='$id'" ;
																$img_val =  return_field_value("master_tble_id","common_photo_library","form_name='trims_order_receive' and master_tble_id='$id'","master_tble_id");
																if($row["within_group"]==1) $orderSource='Internal'; else $orderSource='External';
																?>
																<tr bgcolor="<?php echo $bgcolor; ?>" onclick="change_color('tr_<?php echo $i; ?>','<?php echo $bgcolor; ?>')" id="tr_<?php echo $i; ?>">
																	
																	
																		<td width="35" align="center" align="center" style="word-break: break-all;"> <?php echo $i;?></td>
																		<td width="120" align="center" style="word-break: break-all;"><?php echo $party;?></td>
																	
																		<td width="120" align="center"  style="word-break: break-all;"><?php echo $buyer_buyer;?></td>
																
																		<td width="120" align="center"  align="center"  style="word-break: break-all;"><?php echo $row[cust_order_no];?></td>
																		<td width="120" align="center"  align="center"  style="word-break: break-all;"><?php echo change_date_format($row[receive_date]);?></td>
																		<td width="120" align="center"  align="center"  style="word-break: break-all;"><?php echo change_date_format($row[delivery_date]);?></td>
																
																		<td width="120" align="center"  align="center"  style="word-break: break-all;"><?php echo $row[subcon_job]; ?></td>
																	
																		<td width="120" align="center"  align="center"  style="word-break: break-all;"><?php echo $row[buyer_style_ref]; ?></td>
																		<td width="120" align="center" align="center"  style="word-break: break-all;"><?php echo $trims_ref; ?>
																		</td>
																
																		<td width="120" align="center"  style="word-break: break-all;"><?php echo $section_name; ?></td>
																
																		<td width="120" align="center"  style="word-break: break-all;"><?php echo $trimsGroupArr[$itemGroup];?></td>
																
																		<td width="150" align="center"  style="word-break: break-all;"><?php echo $description ? $description : '';?></td>
																
																		<td width="120"   style="word-break: break-all;"><?php echo $colorNameArr[$colorId];?></td>
																
																	<td width="120" align="center"  style="word-break: break-all;"><?php echo $sizeArr[$sizeId]; ?></td>
																	
																	<td width="120" align="center"  style="word-break: break-all;">
																		<?php echo $unit_of_measurement[$orderUom];?>
																	</td>
																	<td width="120" align="center"  style="word-break: break-all;"><?php echo number_format($row[order_qty], 2); ?></td>
																	<td width="120" align="center"  style="word-break: break-all;">
																		<?php echo $unit_of_measurement[$row[booked_uom]]; ?>
																	</td>
																	<td width="120" align="center"  style="word-break: break-all;">
																		<?php echo number_format($row[booked_qty], 2); ?>
																	</td>
																	<td width="120" align="center"  style="word-break: break-all;"><?php echo number_format($row[rate], 4);?></td>
																	<td width="120" align="center"  style="word-break: break-all;"><?php echo $currency[$row['currency_id']]; ?></td>
																	<td width="120" align="right"  style="word-break: break-all;"><?php echo number_format($req_value_tk, 2);?></td>
																	<td width="160" align="center"  style="word-break: break-all;"><?php echo $trims_job_no_arr[$row[subcon_job]]; ?></td>
																	<td width="120" align="center"  style="word-break: break-all;"><?php echo change_date_format($trims_job_date_arr[$row[subcon_job]]); ?></td>
																	<td width="120" align="center"  style="word-break: break-all;">
																		<?php
																			if(!$prod_qty) {
																				echo number_format($prod_qty, 2);
																			} else {
																		?>
																		<a href="##" onclick="fnc_amount_details(<?php echo "$prod_jobdtls_id, $row[rate], $exchangeRate, '$internal_ref', '$section_name'"; ?>, 'production_qty_popup', 'Production Quantity')">
																			<?php echo $prod_qty; ?>
																		</a>
																		<?php
																			}
																		?>
																	</td>
																	<td width="120" align="right"  style="word-break: break-all;"><?php echo number_format($prodValueTk, 2); ?></td>
																	<td width="120" align="center"  style="word-break: break-all;"><?php echo number_format($prodBalanceQty, 2); ?></td>
																	<td width="120" align="right"  style="word-break: break-all;"><?php echo number_format($prodBalanceValTk, 2); ?></td>
																	<td width="120" align="right"  style="word-break: break-all;">
																	<?php
																		if(!$reject_qty) {
																			echo number_format($reject_qty, 2);
																		} else {
																		echo number_format($reject_qty, 2);
																	
																		}
																		?>
																	</td>
																	<td width="120" align="right"  style="word-break: break-all;"><?php echo number_format($rejectValueTk, 2); ?></td>
																	<td width="120" align="center"  style="word-break: break-all;">
																		<a href="##" onclick="fnc_amount_details(<?php echo "$prod_jobdtls_id, $row[rate], $exchangeRate, '$internal_ref', '$section_name'"; ?>, 'qc_qty_popup', 'QC Quantity')">
																			<p><?php echo $qc_qty; ?></p>
																		</a>
																	</td>
																	<td width="120" align="center"  style="word-break: break-all;"><?php echo number_format($qcBalanceQty, 2); ?></td>
																	<td width="120" align="center"  style="word-break: break-all;" title="<?php echo "Trims Delivery : " .$trims_del;?>">
																		<a href="##" onclick="fnc_amount_details(<?php echo "$prod_jobdtls_id, $row[rate], $exchangeRate, '$internal_ref', '$section_name'"; ?>, 'delivery_qty_popup', 'Delivery Quantity')">
																			<p><?php echo $delv_qty; ?></p>
																		</a>
																	</td>
																	<td width="120" align="right"  style="word-break: break-all;">
																		
																			<?php
																		
																				echo number_format($deliValueTk, 2);
																				
																			?>
																		
																	</td>
																	<td width="120" align="center"  style="word-break: break-all;">
																	<?php
																	if($delv_balance_qty>0){
																		?>
																	
																		<?php echo number_format($delv_balance_qty, 2);?>
																		<?php
																	}else{
																		?><?php echo '0'; ?><?php
																	} ?>
																	</td>
																	<td width="120" align="right"  style="word-break: break-all;">
																		
																			<?php echo number_format($delv_balance_value, 2); ?>          			
																		
																	</td>
																	<td width="120" align="center"  style="word-break: break-all;">
																		
																		<?php
																		
																		$trims_challan_no_dtls = $trims_challan_no_arr[$trims_del];
																		if( $trims_challan_no_dtls!='') {
																			?>
																				<a href="##" onclick="fnc_amount_details(<?php echo "'$trims_challan_no_dtls', $row[rate], $exchangeRate, '$internal_ref', '$section_name'"; ?>, 'gate_pass_popup', 'Gate Pass')">
																					<?php echo 'Yes'; ?>
																				</a>
																			<?php 
																			} else {
																				echo 'No';
																			}
																		?>         			
																		
																	</td>
																	<td width="120" align="center"  style="word-break: break-all;" >
																		
																			<?php // echo $orderKey;
																				if($bill_qty > 0) {
																				?>
																					<a href="##" onclick="fnc_amount_details(<?php echo "$prod_jobdtls_id, $row[rate], $exchangeRate, '$internal_ref', '$section_name'"; ?>, 'bill_qty_popup', 'Bill Quantity')">
																						<?php echo $bill_qty; ?>
																					</a>
																				<?php 
																				} else {
																					echo "0";
																				}
																			?>
																		
																	</td>
																	<td width="120" align="right"  style="word-break: break-all;">
																	<?php
																	if($bill_amt>0){
																		?>
																		<?php echo number_format($bill_amt, 2); ?>
																		<?
																	}else{
																		?><?php echo '0'; ?><?php
																	}?>
																	</td>
																	<td width="120" align="center"  style="word-break: break-all;">
																		<?php 
																		echo $billBalanceQty;
																		?>        	
																	</td>
																	<td width="120" align="right"  style="word-break: break-all;">
																		<?php echo number_format($billBalanceAmtTk, 2); ?>
																	</td>
																</tr>
																<?php 
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
						}
						
					}
					?>
				
				</tbody>	
				</table>
			</div>

			<table width="<? echo $width;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_footer" align="left">
				<tfoot>
					<tr>
						<th width="35" >&nbsp;</th>
						<th width="120" >&nbsp;</th>
						<th width="120" >&nbsp;</th>
						<th width="120" >&nbsp;</th>
						<th width="120" >&nbsp;</th>
						<th width="120" >&nbsp;</th>
						<th width="120" >&nbsp;</th>
						<th width="120" >&nbsp;</th>
						<th width="120" >&nbsp;</th>
						<th width="120" >&nbsp;</th>
						<th width="120" >&nbsp; </th>
						<th width="150" >&nbsp;</th>
						<th width="120" >&nbsp; </th>
						<th width="120" >&nbsp; </th>
						<th width="120" >Total : </th>
						<th width="120" id="value_req_qty"></th>
						<th width="120" >&nbsp; </th>
						<th width="120" id="value_booked_qty"> </th>
						<th width="120" >&nbsp;</th>
						<th width="120" >&nbsp;</th>
						<th width="120" id="value_Req_value"></th>
						<th width="160" >&nbsp;</th>
						<th width="120" >&nbsp;</th>
						<th width="120" id="value_prod_qty"></th>
						<th width="120" id="value_prod_val"></th>
						<th width="120" id="value_prod_bal_qty"></th>
						<th width="120" id="value_prod_bal_val"></th>
						<th width="120" id="value_prod_rej_qty"></th>
						<th width="120" id="value_prod_rej_val"></th>
						<th width="120" id="value_qc_qty"></th>
						<th width="120" id="value_qc_bal_qty"></th>
						<th width="120" id="value_deli_qty"></th>
						<th width="120" id="value_deli_val"></th>
						<th width="120" id="value_deli_bal_qty"></th>
						<th width="120" id="value_deli_bal_val"></th>
						<th width="120" >&nbsp;</th>
						<th width="120" id="value_bill_qty"></th>
						<th width="120" id="value_bill_amount_qty"></th>
						<th width="120" id="value_bill_balance_qty"></th>
						<th width="120" id="value_bill_balance_amount"></th>						
					</tr>
				</tfoot>
			</table>
			</div>
		<?php
		}
		else if($report_type==2)
		{

			?>
				<div align="center" style="height:auto; width:<? echo $width+20;?>px; margin:0 auto; padding:0;">
			<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="caption" align="center">
				<thead class="form_caption" >
					<tr>
						<td colspan="36" align="center" style="font-size:20px;"><?php echo $companyArr[$cbo_company_id]; ?></td>
					</tr>
					<tr>
						<td colspan="36" align="center" style="font-size:14px; font-weight:bold" ><?php echo $report_title; ?></td>
					</tr>
					<tr>
						<td colspan="36" align="center" style="font-size:14px; font-weight:bold">
							<?php echo " From : ".$txt_date_from." To : ". $txt_date_to ;?>
						</td>
					</tr>
				</thead>
			</table>
		
			<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
				<thead>
					<th width="35" >SL</th>
					<th width="120" >Customer Name</th>
					<th width="120" >Cust. Buyer</th>
					<th width="120" >Cust. Work Order</th>
					<th width="120" >Work Order Date</th>
					<th width="120" >Delivery Date</th>
					<th width="120" >Trims Order Receive No</th>
					<th width="120" >Style</th>
					<th width="120" >Section</th>
					<th width="120" >Item Group</th>
					<th width="150" >Item Description</th>
					<th width="100" >Gmts Color</th>
					<th width="100" >Gmts Size</th>
					<th width="120" >Item Color</th>
					<th width="120" >Item Size</th>
					<th width="120" >Order UOM</th>
					<th width="120" >Req. Qty/Ord Rcv</th>
					<th width="120" >Booked UOM</th>
					<th width="120" >Booked Qty</th>
					<th width="120" >Rate</th>
					<th width="120" >Ord Rcv Currency</th>
					<th width="120" >Req. Value [Tk]</th>
					<th width="160" >Job Card No.</th>
					<th width="120" >Job Card Date</th>
					<th width="120" >Prod. Qty</th>
					<th width="120" >Prod. Value [Tk]</th>
					<th width="120" >Prod. Bal. Qty</th>
					<th width="120" >Prod. Bal. Value [Tk]</th>
					<th width="120" >Reject Qty.</th>
					<th width="120" >Reject Value.</th>
					<th width="120" >QC. Qty</th>
					<th width="120" >QC. Bal. Qty</th>
					<th width="120" >Deli. Qty.</th>
					<th width="120" >Deli. Value [Tk]</th>
					<th width="120" >Deli. Balance Qty</th>
					<th width="120" >Deli. Balance Value [Tk]</th>
					<th width="120" >Gate Pass</th>
					<th width="120" >Bill Qty</th>
					<th width="120" >Bill Amount [Tk]</th>
					<th width="120" >Bill Balance Qty</th>
					<th width="120" >Bill Balance Amount [Tk] </th>
				</thead>
			</table>
				
			</table>
		
				<div style="width:<? echo $width+18;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
					<table border="1" cellspacing="0" class="rpt_table" id="table_body_id1" width="<? echo $width;?>" rules="all" align="left">
					<tbody>
						<?php 
						$i=1;
						$total_order_qty=0;$total_order_val=0;$total_booked_qty=0;$total_production_qty=0;

						$row_partyArray=$row_bBArray=$row_ordNoArray=$row_subcon_jobArray=$row_cusStyleArray=$row_buyerPoIdArray=$row_sectionArray=$row_itemGroupArray=$row_orderUomArray=$row_descriptionArray=$row_colorArray=array(); 

						
						$totalReqValueTk=0;
						$totalProdValueTk=0;
						$totalProdBalanceValueTk=0;
						$totalDeliveryValueTk=0;
						$totalDeliveryBalanceValueTk=0;
						$totalBillAmountTk=0;
						$totalBillBalanceAmountTk=0;
						foreach($data_array as $party_id =>$partyArray)
						{	
							foreach( $partyArray as $buyerBuyer =>$bBArray )
							{   	
								foreach( $bBArray as $ordNo =>$ordNoArray )
								{   	
									foreach( $ordNoArray as $subcon_job =>$subcon_jobArray )
									{
										foreach ($subcon_jobArray as $cusStyle => $cusStyleArray)
										{
											foreach ($cusStyleArray as $sectionId => $sectionArray)
											{

												foreach ($sectionArray as $itemGroup => $itemGroupArray)
												{
													foreach ($itemGroupArray as $orderUom => $orderUomArray)
													{
														foreach ($orderUomArray as $description => $descriptionArray)
														{
															foreach ($descriptionArray as $colorId => $colorIdArray)
															{
																foreach ($colorIdArray as $sizeId => $row)
																{
																	
																	$id=$row['id'];
																	$sub_section=$row['sub_section'];
																	//$sizeId=$row["size_id"];
																	//$booked_uom=$row['booked_uom'];
																	$booked_uom=$row['order_uom'];

																	$key=$id.'*'.$sectionId.'*'.$sub_section.'*'.$description.'*'.$colorId.'*'.$sizeId.'*'.$booked_uom;
																	$orderKey=$id.'*'.$row[order_id].'*'.$sectionId.'*'.$sub_section.'*'.$itemGroup.'*'.$row[item_id].'*'.$row[description].'*'.$row[color_id].'*'.$row[size_id].'*'.$row[booked_uom].'*'.$row[rate];
																	// $row[csf("item_group")].'*'.$row[csf("received_id")].'*'.$row[csf("section")].'*'.$row[csf("sub_section")].'*'.$row[csf("description")].'*'.$row[csf("color_id")].'*'.$row[csf("size_id")].'*'.$row[csf("uom")];
																	//$production_qty_on_order_parcent=($trims_production_data_arr[$key][qc_qty]/$trims_production_data_arr[$key][job_quantity])*$row[order_qty];
																	$production_qty_on_order_parcent=$trims_production_data_arr[$key][qc_qty];

																	$gmts_color_id = $trims_production_data_arr[$key][gmts_color_id];
																	$gmts_size_id = $trims_production_data_arr[$key][gmts_size_id];
																	
																	//WORK ORDER NO : 161
																	$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
																	$party=($row[within_group]==1)?$companyArr[$row[party_id]]:$buyerArr[$row[party_id]];
																	$buyer_buyer=($row[within_group]==1)?$buyerArr[$row[buyer_buyer]]:$row[buyer_buyer];
																
																	// $row[delivery_status]=$trims_delivery_data_arr[$item_group.'*'.$key][delevery_status];
																	// if($row[delivery_status]==2){$bgcolor="#FFCC66";}
																	// elseif($row[delivery_status]==3){$bgcolor="#8CD59C";}
																	// else{$row[delivery_status]=1;}
																	//---------------------------------------
																	$exchangeRate = $row[exchange_rate];
																	$orderAmount = $row[order_amount];
																	$orderQty = $row[order_qty];
																	$rate = $row[rate];
																	$req_value_tk = $orderQty*$rate*$exchangeRate;
																	$total_order_qty+=$orderQty;
																	$total_order_val+=$orderAmount;
																	$total_booked_qty+=$row[booked_qty];
																	$total_production_qty+=$production_qty_on_order_parcent;
																	$internal_ref = $buyer_po_arr[$row[buyer_po_id]]['grouping'];
																	$section_name = $trims_section[$sectionId];
																	$orderRate = $orderAmount / $orderQty;

																	if(is_nan($orderRate) || is_infinite($orderRate)) $orderRate = 0;
																	// $order_rate = !is_nan($order_rate) ? $order_rate : 0;
																	$order_amt_tk = $orderAmount * $exchangeRate;
																	$prod_qty = $trims_production_data_arr[$key][production_qty] ? $trims_production_data_arr[$key][production_qty] : 0;
																	$reject_qty = $trims_production_data_arr[$key][reject_qty] ? $trims_production_data_arr[$key][reject_qty] : 0;
																	$trims_ref = $row['trims_ref'];
																	$prodValueTk = $prod_qty*$orderRate*$exchangeRate;
																	$rejectValueTk = $reject_qty*$orderRate*$exchangeRate;
																	$buyer_style_ref = $trims_production_data_arr[$key][buyer_style_ref] ? $trims_production_data_arr[$key][buyer_style_ref] : '';
																	$prod_jobdtls_id = $trims_production_data_arr[$key][job_dtls_id];
																	
																	$prodBalanceQty = $row[booked_qty] - $prod_qty;
																	$prodBalanceValTk = $prodBalanceQty*$rate*$exchangeRate;
																	$qc_qty = $trims_production_data_arr[$key][qc_qty];
																	$qcBalanceQty = $row[booked_qty] - $prod_qty;
																	$delv_qty = $trims_delivery_data_arr[$itemGroup.'*'.$key][delevery_qty];
																	$trims_del = $trims_delivery_data_arr[$itemGroup.'*'.$key][trims_del];
																	$deliValueTk = $delv_qty*$rate*$exchangeRate;
																	$delv_balance_qty = $orderQty - $delv_qty;
																	$delv_balance_value = $delv_balance_qty*$rate*$exchangeRate;
																	//echo $key;
																	
																	$bill_qty = $trims_bill_data_arr[$key][bill_qty];
																	//echo $bill_qty1.'='.$key;
																	$billBalanceQty = number_format( ($delv_qty - $bill_qty), 2 );
																	$billBalanceAmtTk = $billBalanceQty*$rate*$exchangeRate;

																	$delivery_amt=number_format($row[order_rate]*$trims_delivery_data_arr[$item_group.'*'.$key][delevery_qty], 2);
																	$bill_amt=$rate*$trims_bill_data_arr[$key][bill_qty]*$exchangeRate;
																	$receive_ids=implode(',',$trims_receive_id_arr);
																	// $trims_delivery_data_arr[11*4673*5*6*GGG*6037*2*1];
																	// $trims_bill_data_arr[3869*2*0*rew*6954*1*1];

																	$totalReqValueTk += $req_value_tk;
																	$totalProdValueTk += $prodValueTk;
																	$totalDeliveryValueTk += $deliValueTk;
																	$totalDeliveryBalanceValueTk += $delv_balance_value;
																	$totalBillAmountTk += $bill_amt;
																	$totalBillBalanceAmountTk += $billBalanceAmtTk;
																	$totalProdBalanceValueTk += $prodBalanceValTk;
																	
																	if($row["currency_id"]==1){
																		$orderRate=$orderRate/$currency_rate;
																		$orderAmount=$orderAmount/$currency_rate;
																	}
																	//echo "select master_tble_id from common_photo_library where form_name='trims_order_receive' and master_tble_id='$id'" ;
																	$img_val =  return_field_value("master_tble_id","common_photo_library","form_name='trims_order_receive' and master_tble_id='$id'","master_tble_id");
																	if($row["within_group"]==1) $orderSource='Internal'; else $orderSource='External';
																	?>
																	<tr bgcolor="<?php echo $bgcolor; ?>" onclick="change_color('tr_<?php echo $i; ?>','<?php echo $bgcolor; ?>')" id="tr_<?php echo $i; ?>">
																		
																		
																		<td width="35" align="center" align="center" style="word-break: break-all;"> <?php echo $i;?></td>
																		<td width="120" align="center" style="word-break: break-all;"><?php echo $party;?></td>
																	
																		<td width="120" align="center"  style="word-break: break-all;"><?php echo $buyer_buyer;?></td>
																
																		<td width="120" align="center"  align="center"  style="word-break: break-all;"><?php echo $row[cust_order_no];?></td>
																		<td width="120" align="center"  align="center"  style="word-break: break-all;"><?php echo change_date_format($row[receive_date]);?></td>
																		<td width="120" align="center"  align="center"  style="word-break: break-all;"><?php echo change_date_format($row[delivery_date]);?></td>
																
																		<td width="120" align="center"  align="center"  style="word-break: break-all;"><?php echo $row[subcon_job]; ?></td>
																	
																		<td width="120" align="center"  align="center"  style="word-break: break-all;"><?php echo $row[buyer_style_ref]; ?></td>
																		<td width="120" align="center"  style="word-break: break-all;"><?php echo $section_name; ?></td>
																
																		<td width="120" align="center"  style="word-break: break-all;"><?php echo $trimsGroupArr[$itemGroup];?></td>
																
																		<td width="150" align="center"  style="word-break: break-all;"><?php echo $description ? $description : '';?></td>

																		<td width="100" align="center"  style="word-break: break-all;"><?php echo $colorNameArr[$gmts_color_id];?></td>

																		<td width="100" align="center"  style="word-break: break-all;"><?php echo $sizeArr[$gmts_size_id];?></td>

																		<td width="120"   style="word-break: break-all;"><?php echo $colorNameArr[$colorId];?></td>
																	
																		<td width="120" align="center"  style="word-break: break-all;"><?php echo $sizeArr[$sizeId]; ?></td>
																		
																		<td width="120" align="center"  style="word-break: break-all;">
																			<?php echo $unit_of_measurement[$orderUom];?>
																		</td>
																		<td width="120" align="center"  style="word-break: break-all;"><?php echo number_format($row[order_qty], 2); ?></td>
																		<td width="120" align="center"  style="word-break: break-all;">
																			<?php echo $unit_of_measurement[$row[booked_uom]]; ?>
																		</td>
																		<td width="120" align="center"  style="word-break: break-all;">
																			<?php echo number_format($row[booked_qty], 2); ?>
																		</td>
																		<td width="120" align="center"  style="word-break: break-all;"><?php echo number_format($row[rate], 4);?></td>
																		<td width="100" align="center"  style="word-break: break-all;"><?php echo $currency[$row['currency_id']]; ?></td>
																		<td width="120" align="right"  style="word-break: break-all;"><?php echo number_format($req_value_tk, 2);?></td>
																		<td width="160" align="center"  style="word-break: break-all;"><?php echo $trims_job_no_arr[$row[subcon_job]]; ?></td>
																		<td width="120" align="center"  style="word-break: break-all;"><?php echo change_date_format($trims_job_date_arr[$row[subcon_job]]); ?></td>
																		<td width="120" align="center"  style="word-break: break-all;">
																			<?php
																				if(!$prod_qty) {
																					echo number_format($prod_qty, 2);
																				} else {
																			?>
																			<a href="##" onclick="fnc_amount_details(<?php echo "$prod_jobdtls_id, $row[rate], $exchangeRate, '$internal_ref', '$section_name'"; ?>, 'production_qty_popup', 'Production Quantity')">
																				<?php echo number_format($prod_qty,2); ?>
																			</a>
																			<?php
																				}
																			?>
																		</td>
																		<td width="120" align="right"  style="word-break: break-all;"><?php echo number_format($prodValueTk, 2); ?></td>
																		<td width="120" align="center"  style="word-break: break-all;"><?php echo number_format($prodBalanceQty, 2); ?></td>
																		<td width="120" align="right"  style="word-break: break-all;"><?php echo number_format($prodBalanceValTk, 2); ?></td>
																		<td width="120" align="right"  style="word-break: break-all;">
																		<?php
																			if(!$reject_qty) {
																				echo number_format($reject_qty, 2);
																			} else {
																			echo number_format($reject_qty, 2);
																		
																			}
																			?>
																		</td>
																		<td width="120" align="right"  style="word-break: break-all;"><?php echo number_format($rejectValueTk, 2); ?></td>
																		<td width="120" align="center"  style="word-break: break-all;">
																			<a href="##" onclick="fnc_amount_details(<?php echo "$prod_jobdtls_id, $row[rate], $exchangeRate, '$internal_ref', '$section_name'"; ?>, 'qc_qty_popup', 'QC Quantity')">
																				<p><?php echo number_format($qc_qty,2); ?></p>
																			</a>
																		</td>
																		<td width="120" align="center"  style="word-break: break-all;"><?php echo number_format($qcBalanceQty, 2); ?></td>
																		<td width="120" align="center"  style="word-break: break-all;" title="<?php echo "Trims Delivery : " .$trims_del;?>">
																			<a href="##" onclick="fnc_amount_details(<?php echo "$prod_jobdtls_id, $row[rate], $exchangeRate, '$internal_ref', '$section_name'"; ?>, 'delivery_qty_popup', 'Delivery Quantity')">
																				<p><?php echo number_format($delv_qty,2); ?></p>
																			</a>
																		</td>
																		<td width="120" align="right"  style="word-break: break-all;">
																			
																				<?php
																			
																					echo number_format($deliValueTk, 2);
																					
																				?>
																			
																		</td>
																		<td width="120" align="center"  style="word-break: break-all;">
																		<?php
																		if($delv_balance_qty>0){
																			?>
																		
																			<?php echo number_format($delv_balance_qty, 2);?>
																			<?php
																		}else{
																			?><?php echo '0'; ?><?php
																		} ?>
																		</td>
																		<td width="120" align="right"  style="word-break: break-all;">
																			
																				<?php echo number_format($delv_balance_value, 2); ?>          			
																			
																		</td>
																		<td width="120" align="center"  style="word-break: break-all;">
																			
																			<?php
																			
																			$trims_challan_no_dtls = $trims_challan_no_arr[$trims_del];
																			if( $trims_challan_no_dtls!='') {
																				?>
																					<a href="##" onclick="fnc_amount_details(<?php echo "'$trims_challan_no_dtls', $row[rate], $exchangeRate, '$internal_ref', '$section_name'"; ?>, 'gate_pass_popup', 'Gate Pass')">
																						<?php echo 'Yes'; ?>
																					</a>
																				<?php 
																				} else {
																					echo 'No';
																				}
																			?>         			
																			
																		</td>
																		<td width="120" align="center"  style="word-break: break-all;" >
																			
																				<?php // echo $orderKey;
																					if($bill_qty > 0) {
																					?>
																						<a href="##" onclick="fnc_amount_details(<?php echo "$prod_jobdtls_id, $row[rate], $exchangeRate, '$internal_ref', '$section_name'"; ?>, 'bill_qty_popup', 'Bill Quantity')">
																							<?php echo number_format($bill_qty,2); ?>
																						</a>
																					<?php 
																					} else {
																						echo "0";
																					}
																				?>
																			
																		</td>
																		<td width="120" align="right"  style="word-break: break-all;">
																		<?php
																		if($bill_amt>0){
																			?>
																			<?php echo number_format($bill_amt, 2); ?>
																			<?
																		}else{
																			?><?php echo '0'; ?><?php
																		}?>
																		</td>
																		<td width="120" align="center"  style="word-break: break-all;">
																			<?php 
																			echo $billBalanceQty;
																			?>        	
																		</td>
																		<td width="120" align="right"  style="word-break: break-all;">
																			<?php echo number_format($billBalanceAmtTk, 2); ?>
																		</td>
																	</tr>
																	<?php 
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
							}
							
						}
						?>
					
					</tbody>	
					</table>
			</div>
			
			<table border="1" class="rpt_table" id="table_body_footer" width="<? echo $width;?>" rules="all" align="left">
				<tfoot>
					<tr>
						<th width="35" >&nbsp;</th>
						<th width="120" >&nbsp;</th>
						<th width="120" >&nbsp;</th>
						<th width="120" >&nbsp;</th>
						<th width="120" >&nbsp;</th>
						<th width="120" >&nbsp;</th>
						<th width="120" >&nbsp;</th>
						<th width="120" >&nbsp;</th>
						<th width="120" >&nbsp;</th>
						<th width="120" >&nbsp;</th>
						<th width="150" >&nbsp;</th>
						<th width="100" >&nbsp;</th>
						<th width="100" >&nbsp;</th>
						<th width="120" >&nbsp;</th>
						<th width="120" >&nbsp;</th>
						<th width="120" >Total : </th>
						<th width="120" id="value_req_qty"></th>
						<th width="120" >&nbsp; </th>
						<th width="120" id="value_booked_qty"> </th>
						<th width="120" >&nbsp;</th>
						<th width="120" >&nbsp;</th>
						<th width="120" id="value_Req_value"></th>
						<th width="160" >&nbsp;</th>
						<th width="120" >&nbsp;</th>
						<th width="120" id="value_prod_qty"></th>
						<th width="120" id="value_prod_val"></th>
						<th width="120" id="value_prod_bal_qty"></th>
						<th width="120" id="value_prod_bal_val"></th>
						<th width="120" id="value_prod_rej_qty"></th>
						<th width="120" id="value_prod_rej_val"></th>
						<th width="120" id="value_qc_qty"></th>
						<th width="120" id="value_qc_bal_qty"></th>
						<th width="120" id="value_deli_qty"></th>
						<th width="120" id="value_deli_val"></th>
						<th width="120" id="value_deli_bal_qty"></th>
						<th width="120" id="value_deli_bal_val"></th>
						<th width="120" >&nbsp;</th>
						<th width="120" id="value_bill_qty"></th>
						<th width="120" id="value_bill_amount_qty"></th>
						<th width="120" id="value_bill_balance_qty"></th>
						<th width="120" id="value_bill_balance_amount"></th>
					</tr>
				</tfoot>
			</table>
		 </div>
			<?php
		}
		else if($report_type==3)
		{
		  ?>
			<div align="center" style="height:auto; width:2800px; margin:0 auto; padding:0;">
	    	<table width="2800px" cellpadding="0" cellspacing="0" id="caption" align="center">
				<thead class="form_caption" >
					<tr>
						<td colspan="28" align="center" style="font-size:20px;"><?php echo $companyArr[$cbo_company_id]; ?></td>
					</tr>
					<tr>
						<td colspan="28" align="center" style="font-size:14px; font-weight:bold" ><?php echo $report_title; ?></td>
					</tr>
					<tr>
						<td colspan="28" align="center" style="font-size:14px; font-weight:bold">
							<?php echo " From : ".$txt_date_from." To : ". $txt_date_to ;?>
						</td>
					</tr>
				</thead>
			</table>
		
			<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="2780px" rules="all" id="rpt_table_header" align="left">
			<div style="text-align:center;" class="search_type"><? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --",4,"","","2,3,4" ); ?></div>
				<thead>
					<th width="35" >SL</th>
	                <th width="120" >Customer Name</th>
	                <th width="120" >Cust. Buyer</th>
	                <th width="120" >Cust. Work Order</th>
	                <th width="120" >Ord Rcv Date</th>
	                <th width="120" >Section</th>
	                <th width="120" >Item Group</th>
	                <th width="150" >Item Description</th>
	                <th width="80" >Item Color</th>
	                <th width="80" >Item Size</th>
	                <th width="80" >Req. Qty/Ord Rcv</th>
	                <th width="80" >Order UOM</th>
	                <th width="80" >Conv. Factor</th>
	                <th width="80" >Booked Qty</th>
	                <th width="80" >Booked UOM</th>
	                <th width="80" >Rate</th>
	                <th width="80" >Ord Rcv Currency</th>
	                <th width="80" >Req. Value [Ord. Currency]</th>
	                <th width="80" >Req. Value [Tk]</th>
	                <th width="80" >Prod. Qty</th>
	                <th width="80" >Trans IN</th>
	                <th width="80" >Trans Out</th>
	                <th width="80" >Deli. Qty.</th>
	                <th width="80" >Deli. Value [Ord. Currency]</th>
	                <th width="80" >Deli. Value [Tk]</th>
	                <th width="80" >Deli. Balance Qty</th>
	                <th width="80" >Stock Inhand</th>
	                <th width="80" >Deli. Balance Value [Ord. Currency]</th>
	                <th width="80" >Deli. Balance Value [Tk]</th>
				</thead>			
			</table>	
			<div style="width:2800px; max-height:350px; float:left; overflow-y:scroll;" id="scroll_body">
	        	<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id2" width="2780px" rules="all" align="left">
				<tbody>
					<?php 
					$i=1;
					$total_order_qty=0;$total_order_val=0;$total_booked_qty=0;$total_production_qty=0;
					$row_partyArray=$row_bBArray=$row_ordNoArray=$row_subcon_jobArray=$row_cusStyleArray=$row_buyerPoIdArray=$row_sectionArray=$row_itemGroupArray=$row_orderUomArray=$row_descriptionArray=$row_colorArray=array(); 				
					$totalReqValueTk=0;
					$totalProdValueTk=0;
					$totalProdBalanceValueTk=0;
					$totalDeliveryValueTk=0;
					$totalDeliveryBalanceValueTk=0;
					$totalBillAmountTk=0;
					$totalBillBalanceAmountTk=0;
					foreach ($data_array_new as $courrencyId => $party_data)
					{
						$req_valu='';
						$sub_req_valu_tk='';
						$sub_delv_qty='';
						$sub_deliValueTk='';
						$sub_delv_balance_value_usd='';
						$sub_delv_balance_value='';
						foreach($party_data as $party_id =>$partyArray)
						{	
							foreach( $partyArray as $buyerBuyer =>$bBArray )
							{   	
								foreach( $bBArray as $ordNo =>$ordNoArray )
								{   	
									foreach( $ordNoArray as $subcon_job =>$subcon_jobArray )
									{
										foreach ($subcon_jobArray as $cusStyle => $cusStyleArray)
										{
											foreach ($cusStyleArray as $sectionId => $sectionArray)
											{

												foreach ($sectionArray as $itemGroup => $itemGroupArray)
												{
													foreach ($itemGroupArray as $orderUom => $orderUomArray)
													{
														foreach ($orderUomArray as $description => $descriptionArray)
														{
															foreach ($descriptionArray as $colorId => $colorIdArray)
															{
																foreach ($colorIdArray as $sizeId => $row)
																{
																	$id=$row['id'];
																	$sub_section=$row['sub_section'];
																	//$sizeId=$row["size_id"];
																	//$booked_uom=$row['booked_uom'];
																	$booked_uom=$row['order_uom'];

																	$key=$id.'*'.$sectionId.'*'.$sub_section.'*'.$description.'*'.$colorId.'*'.$sizeId.'*'.$booked_uom;
																	$orderKey=$id.'*'.$row[order_id].'*'.$sectionId.'*'.$sub_section.'*'.$itemGroup.'*'.$row[item_id].'*'.$row[description].'*'.$row[color_id].'*'.$row[size_id].'*'.$row[booked_uom].'*'.$row[rate];

																	$production_qty_on_order_parcent=$trims_production_data_arr[$key][qc_qty];
																	
																	//WORK ORDER NO : 161
																	$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
																	$party=($row[within_group]==1)?$companyArr[$row[party_id]]:$buyerArr[$row[party_id]];
																	$buyer_buyer=($row[within_group]==1)?$buyerArr[$row[buyer_buyer]]:$row[buyer_buyer];
									
																	$exchangeRate = $row[exchange_rate];
																	$orderAmount = $row[order_amount];
																	$orderQty = $row[order_qty];
																	$rate = $row[rate];
																
																	$req_value_tk = $orderQty*$rate*$exchangeRate;
																	
																	$req_value_usd = $orderQty*$rate;
																	
																	$total_order_qty+=$orderQty;
																	$total_order_val+=$orderAmount;
																	$total_booked_qty+=$row[booked_qty];
																	$total_production_qty+=$production_qty_on_order_parcent;
																	$internal_ref = $buyer_po_arr[$row[buyer_po_id]]['grouping'];
																	$section_name = $trims_section[$sectionId];
																	$orderRate = $orderAmount / $orderQty;

																	if(is_nan($orderRate) || is_infinite($orderRate)) $orderRate = 0;
																	// $order_rate = !is_nan($order_rate) ? $order_rate : 0;
																	$order_amt_tk = $orderAmount * $exchangeRate;
																	$prod_qty = $trims_production_data_arr[$key][production_qty] ? $trims_production_data_arr[$key][production_qty] : 0;
																	$reject_qty = $trims_production_data_arr[$key][reject_qty] ? $trims_production_data_arr[$key][reject_qty] : 0;
																	$trims_ref = $row['trims_ref'];
																	$prodValueTk = $prod_qty*$orderRate*$exchangeRate;
																	$rejectValueTk = $reject_qty*$orderRate*$exchangeRate;
																	$buyer_style_ref = $trims_production_data_arr[$key][buyer_style_ref] ? $trims_production_data_arr[$key][buyer_style_ref] : '';
																	$prod_jobdtls_id = $trims_production_data_arr[$key][job_dtls_id];
																	
																	$prodBalanceQty = $row[booked_qty] - $prod_qty;
																	$prodBalanceValTk = $prodBalanceQty*$rate*$exchangeRate;
																	$qc_qty = $trims_production_data_arr[$key][qc_qty];
																	$qcBalanceQty = $row[booked_qty] - $prod_qty;
																	$delv_qty = $trims_delivery_data_arr[$itemGroup.'*'.$key][delevery_qty];
																	$trims_del = $trims_delivery_data_arr[$itemGroup.'*'.$key][trims_del];
																	
																	$deliValueTk = $delv_qty*$rate*$exchangeRate;
																	$deliValueUsd = $delv_qty*$rate;
																	
																	$delv_balance_qty = $orderQty - $delv_qty;
																	
																	$delv_balance_value = $delv_balance_qty*$rate*$exchangeRate;
																	$delv_balance_value_usd = $delv_balance_qty*$rate;
																	
																	$trans_in_qty=$finished_goods_order[$row["id"]][$row["item_group"]][$row["color_id"]][$row["size_id"]][$row["description"]]["trans_in_qty"];														
																	$trans_out_qty=$finished_goods_order[$row["id"]][$row["item_group"]][$row["color_id"]][$row["size_id"]][$row["description"]]["trans_out_qty"];														
																	$from_received_id=$finished_goods_order[$row["id"]][$row["item_group"]][$row["color_id"]][$row["size_id"]][$row["description"]]["from_received_id"];
																	$to_received_id=$finished_goods_order[$row["id"]][$row["item_group"]][$row["color_id"]][$row["size_id"]][$row["description"]]["to_received_id"];														
																	$type=$finished_goods_order[$row["id"]][$row["item_group"]][$row["color_id"]][$row["size_id"]][$row["description"]]["type"];		
																	$transfer_system_id=$finished_goods_order[$row["id"]][$row["item_group"]][$row["color_id"]][$row["size_id"]][$row["description"]]["transfer_system_id"];	
																	$size_id=$finished_goods_order[$row["id"]][$row["item_group"]][$row["color_id"]][$row["size_id"]][$row["description"]]["size_id"];	
																	
																	
																													
																
																	$bill_qty = $trims_bill_data_arr[$key][bill_qty];
																	//echo $bill_qty1.'='.$key;
																	$billBalanceQty = number_format( ($delv_qty - $bill_qty), 2 );
																	$billBalanceAmtTk = $billBalanceQty*$rate*$exchangeRate;

																	$delivery_amt=number_format($row[order_rate]*$trims_delivery_data_arr[$item_group.'*'.$key][delevery_qty], 2);
																	$bill_amt=$rate*$trims_bill_data_arr[$key][bill_qty]*$exchangeRate;
																	$receive_ids=implode(',',$trims_receive_id_arr);
																	$totalReqValueTk += $req_value_tk;
																	$totalProdValueTk += $prodValueTk;
																	$totalDeliveryValueTk += $deliValueTk;
																	$totalDeliveryBalanceValueTk += $delv_balance_value;
																	$totalBillAmountTk += $bill_amt;
																	$totalBillBalanceAmountTk += $billBalanceAmtTk;
																	$totalProdBalanceValueTk += $prodBalanceValTk;
																	
																	if($row["currency_id"]==1){
																		$orderRate=$orderRate/$currency_rate;
																		$orderAmount=$orderAmount/$currency_rate;
																	}
																	$inhand=($prod_qty+$trans_in_qty-$trans_out_qty)/$row['booked_conv_fac']-$delv_qty;
																	$img_val =  return_field_value("master_tble_id","common_photo_library","form_name='trims_order_receive' and master_tble_id='$id'","master_tble_id");
																	if($row["within_group"]==1) $orderSource='Internal'; else $orderSource='External';
																	?>
																	<tr bgcolor="<?php echo $bgcolor; ?>" onclick="change_color('tr_<?php echo $i; ?>','<?php echo $bgcolor; ?>')" id="tr_<?php echo $i; ?>">
																		
																		
																		<td width="35" align="center" align="center" style="word-break: break-all;"> <?php echo $i;?></td>
																		<td width="120" align="center" style="word-break: break-all;"><?php echo $party;?></td>
																	
																		<td width="120" align="center"  style="word-break: break-all;"><?php echo $buyer_buyer;?></td>
																
																		<td width="120" align="center"  align="center"  style="word-break: break-all;"><?php echo $row[cust_order_no];?></td>
																		<td width="120" align="center"  align="center"  style="word-break: break-all;"><?php echo change_date_format($row[receive_date]);?></td>																											
																		<td width="120" align="center"  style="word-break: break-all;"><?php echo $section_name; ?></td>
																
																		<td width="120" align="center"  style="word-break: break-all;"><?php echo $trimsGroupArr[$itemGroup];?></td>
																
																		<td width="150" align="center"  style="word-break: break-all;"><?php echo $description ? $description : '';?></td>
																
																		<td width="80"   style="word-break: break-all;"><?php echo $colorNameArr[$colorId];?></td>
																	
																		<td width="80" align="center"  style="word-break: break-all;"><?php echo $sizeArr[$sizeId]; ?></td>

																	   <td width="80" align="center"  style="word-break: break-all;"><?php echo number_format($row["order_qty"], 2); ?></td>

																		<td width="80" align="center"  style="word-break: break-all;">
																			<?php echo $unit_of_measurement[$orderUom];?>
																		</td>	
																		<td width="80" align="center"  style="word-break: break-all;">
																			<?php echo $row['booked_conv_fac']; ?>
																		</td>
																		<td width="80" align="center"  style="word-break: break-all;">
																			<?php echo number_format($row["booked_qty"], 2); ?>
																		</td>

																		<td width="80" align="center"  style="word-break: break-all;">
																			<?php echo $unit_of_measurement[$row["booked_uom"]]; ?>
																		</td>																
																		<td width="80" align="center"  style="word-break: break-all;"><?php echo number_format($row[rate], 4);?></td>
																		<td width="80" align="center"  style="word-break: break-all;"><?php echo $currency[$row['currency_id']]; ?></td>
																		<td width="80" align="center"  style="word-break: break-all;"><?php echo number_format($req_value_usd, 2) ?></td>
																		<td width="80" align="right"  style="word-break: break-all;"><?php echo number_format($req_value_tk, 2);?></td>
																		<td width="80" align="center"  style="word-break: break-all;">
																			<?php
																				if(!$prod_qty) {
																					echo number_format($prod_qty, 2);
																				} else {
																			?>
																			<a href="##" onclick="fnc_amount_details(<?php echo "$prod_jobdtls_id, $row[rate], $exchangeRate, '$internal_ref', '$section_name'"; ?>, 'production_qty_popup', 'Production Quantity')">
																				<?php echo $prod_qty; ?>
																			</a>
																			<?php
																				}
																			?>
																		</td>
																		<td width="80" align="center"  style="word-break: break-all;"> <a href="##"  onClick="fnc_transfer_in_out_details(<?php echo "$from_received_id, $to_received_id, $type,'$transfer_system_id',$size_id"; ?>, 'trans_in_qty_popup', 'Trans IN Quantity')"> <?php echo $trans_in_qty; ?></a></td>																
																		<td width="80" align="right"  style="word-break: break-all;"> <a href="##" onClick="fnc_transfer_in_out_details(<?php echo "$from_received_id, $to_received_id, $type,'$transfer_system_id',$size_id"; ?>, 'trans_out_qty_popup', 'Trans Out Quantity')"> <?php echo $trans_out_qty; ?></a> </td>																
																		<td width="80" align="center"  style="word-break: break-all;" title="<?php echo "Trims Delivery : " .$trims_del;?>">
																			<a href="##" onclick="fnc_amount_details(<?php echo "$prod_jobdtls_id, $row[rate], $exchangeRate, '$internal_ref', '$section_name'"; ?>, 'delivery_qty_popup', 'Delivery Quantity')">
																				<p><?php echo $delv_qty; ?></p>
																			</a>
																		</td>
																		<td width="80" align="right"  style="word-break: break-all;">
																				<?php echo number_format($deliValueUsd, 2);?>
																		</td>

																		<td width="80" align="right"  style="word-break: break-all;">
																			<?php echo number_format($deliValueTk, 2);?>
																		</td>

																		<td width="80" align="center"  style="word-break: break-all;">
																		<?php
																		if($delv_balance_qty>0){
																			?>
																		
																			<?php echo number_format($delv_balance_qty, 2);?>
																			<?php
																		}else{
																			?><?php echo '0'; ?><?php
																		} ?>
																		</td>
																		<td width="80" align="right"  style="word-break: break-all;">
																				<?php echo number_format($inhand, 2);  ?> 							
																		</td>
																		</td>
																		<td width="80" align="right"  style="word-break: break-all;">
																				<?php echo number_format($delv_balance_value_usd, 2); ?> 							
																		</td>
																		<td width="80" align="right"  style="word-break: break-all;">	
																			<?php echo number_format($delv_balance_value, 2); ?> 		
																		</td>
																		
																	</tr>
																	<?php 
																	$i++;
																	$req_valu+=$req_value_usd;
																	$sub_req_valu_tk+=$req_value_tk;
																	$sub_delv_qty+=$deliValueUsd;
																	$sub_deliValueTk+=$deliValueTk;
																	$sub_delv_balance_value_usd+=$delv_balance_value_usd;
																	$sub_delv_balance_value+=$delv_balance_value;
																}
																
																		
																}
																
																
															}
														}
													}
												}
											}
										}
									}
								}
							}
						?>
						<tr bgcolor="<?php echo $bgcolor; ?>">
							<td colspan="17" align="right" width="80" ><b>Sub Total Order Currency <?php echo $currency[$row['currency_id']]; ?></b></td>
							<td width="80" align="right" ><b><?= number_format($req_valu,2)?></b></td>
							<td width="80" align="right"><b><?= number_format($sub_req_valu_tk,2)?></b></td>
							<td width="80" ><b></b></td>
							<td width="80" ></td>
							<td width="80" ></td>
							<td width="80" ></td>
							<td width="80" align="right"><b><?= number_format($sub_delv_qty,2)?></b></td>
							<td width="80" align="right"><b><?= number_format($sub_deliValueTk,2)?></b></td>
							<td width="80"></td>
							<td width="80"></td>
							<td width="80" align="right"><b><?= number_format($sub_delv_balance_value_usd,2)?></b></td>
							<td width="80" align="right"><b><?= number_format($sub_delv_balance_value,2)?></b></td>											
						</tr>
						<?						                                            						
					}
					?>
				
				</tbody>	
				</table>
			</div>

			<table width="2780px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_footer" align="left">
				<tfoot>
					<tr>
						<th width="35" >&nbsp;</th>
						<th width="120" >&nbsp;</th>
						<th width="120" >&nbsp;</th>
						<th width="120" >&nbsp;</th>
						<th width="120" >&nbsp;</th>
						<th width="120" >&nbsp;</th>
						<th width="120" >&nbsp; </th>
						<th width="150" >&nbsp;</th>
						<th width="80" >&nbsp; </th>
						<th width="80" >&nbsp; </th>
						<th width="80" > </th>
						<th width="80"></th>
						<th width="80"></th>
						<th width="80" >&nbsp; </th>
						<th width="80" > </th>
						<th width="80" >&nbsp;</th>
						<th width="80" >Total :</th>
						<th width="80" id="value_Req_value"></th>
						<th width="80" id="value_Req_value_taka"></th>
						<th width="80" id="value_prod_qtyss"></th>
						<th width="80" id="value_prod_bal_qty"></th>
						<th width="80" ></th>
						<th width="80" ></th>
						<th width="80" id="value_deli_val_usd"></th>
						<th width="80" id="value_deli_val_taka"></th>
						<th width="80" id="value_deli_val_taka"></th>
						<th width="80" ></th>
						<th width="80" id="value_deli_bal_usd"></th>
						<th width="80" id="value_deli_bal_taka"></th>											
					</tr>
				</tfoot>
			</table>
			</div>
		 <?php
		}
		  ?>
		
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

if($action=='trans_in_qty_popup') {
	echo load_html_head_contents('Popup Info', '../../../../', 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<style>
		tbody tr td {
			text-align: center;
		}
	</style>
    <table width="795" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all">
    	<thead>
        	<tr> 					
            	<th width="30">SL</th>
                <th width="100">Transfer System ID</th>
                <th width="100">Transfer Date</th>
                <th width="80">Challan No</th>
                <th width="120">From Work Order</th>
                <th width="120">To Work Order</th>
                <th width="60"> Transferred Qty</th>
            </tr>
        </thead>
        <tbody>
		<?php	
		$sql="SELECT c.from_order_no,c.to_order_no,d.transfer_system_id,d.transfer_date,d.challan_no, sum(c.quantity) AS qty from trims_item_transfer_dtls c, trims_item_transfer_mst d WHERE d.id = c.mst_id and c.size_id=$size_id and  c.to_received_id=$to_received_id group by c.from_order_no,c.to_order_no,d.transfer_system_id,d.transfer_date,d.challan_no";
			//c.from_received_id=$from_received_id and
		// echo $sql;
		$sql_result=sql_select($sql); $t=1;
		$total_qc_qty=0;
		foreach($sql_result as $row)
		{
			if ($t%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; ?>
        	<tr bgcolor="<?php echo $bgcolor; ?>" onclick="change_color('tr_<?php echo $t; ?>','<?php echo $bgcolor; ?>')" id="tr_<?php echo $t; ?>">
        		<td><p><?php echo $t;?></p></td>
                <td><p><?php echo $row[csf('transfer_system_id')]; ?></p></td>
                <td><p><?php echo change_date_format($row[csf('transfer_date')]); ?></p></td>
                <td><p><?php echo $row[csf('challan_no')]; ?></p></td>
                <td><p><?php echo $row[csf('from_order_no')]; ?></p></td>
                <td><p><?php echo $row[csf("to_order_no")] ?></p></td>
                <td align="right"><p><?php echo $row[csf('qty')]; ?></p></td>
            </tr>
            <?php
            $total_qc_qty += $row[csf('qty')];
			$t++;
		}
		?>
        </tbody>
        <tfoot>
        	<tr>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>Total</th>
                <th align="right"><?=$total_qc_qty?></th>
            </tr>
        </tfoot>
    </table>
    <?php
}

if($action=='trans_out_qty_popup') {
	echo load_html_head_contents('Popup Info', '../../../../', 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<style>
		tbody tr td {
			text-align: center;
		}
	</style>
    <table width="795" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all">
    	<thead>
        	<tr> 					
            	<th width="30">SL</th>
                <th width="100">Transfer System ID</th>
                <th width="100">Transfer Date</th>
                <th width="80">Challan No</th>
                <th width="120">From Work Order</th>
                <th width="120">To Work Order</th>
                <th width="60"> Transferred Qty</th>
            </tr>
        </thead>
        <tbody>
		<?php
		
		if($type==2){
			$sql="SELECT c.from_order_no,c.to_order_no,d.transfer_system_id,d.transfer_date,d.challan_no, sum(c.quantity) AS qty from trims_item_transfer_dtls c, trims_item_transfer_mst d WHERE d.id = c.mst_id and c.size_id=$size_id and c.from_received_id=$to_received_id  group by c.from_order_no,c.to_order_no,d.transfer_system_id,d.transfer_date,d.challan_no";	
		}else{
			$sql="SELECT c.from_order_no,c.to_order_no,d.transfer_system_id,d.transfer_date,d.challan_no, sum(c.quantity) AS qty from trims_item_transfer_dtls c, trims_item_transfer_mst d WHERE d.id = c.mst_id and c.size_id=$size_id and c.from_received_id=$from_received_id  group by c.from_order_no,c.to_order_no,d.transfer_system_id,d.transfer_date,d.challan_no";
		}
		   //and c.to_received_id=$to_received_id
		// echo $sql;
		$sql_result=sql_select($sql); $t=1;
		$total_qc_qty=0;
		foreach($sql_result as $row)
		{
			if ($t%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; ?>
        	<tr bgcolor="<?php echo $bgcolor; ?>" onclick="change_color('tr_<?php echo $t; ?>','<?php echo $bgcolor; ?>')" id="tr_<?php echo $t; ?>">
        		<td><p><?php echo $t;?></p></td>
                <td><p><?php echo $row[csf('transfer_system_id')]; ?></p></td>
                <td><p><?php echo change_date_format($row[csf('transfer_date')]); ?></p></td>
                <td><p><?php echo $row[csf('challan_no')]; ?></p></td>
                <td><p><?php echo $row[csf('from_order_no')]; ?></p></td>
                <td><p><?php echo $row[csf("to_order_no")] ?></p></td>
                <td align="right"><p><?php echo $row[csf('qty')]; ?></p></td>
            </tr>
            <?php
            $total_qc_qty += $row[csf('qty')];
			$t++;
		}
		?>
        </tbody>
        <tfoot>
        	<tr>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>Total</th>
                <th align="right"><?=$total_qc_qty?></th>
            </tr>
        </tfoot>
    </table>
    <?php
}



if($action=="get_user_pi_file_without_download")
{
    // var_dump($_REQUEST);
    extract($_REQUEST);
    //echo "SELECT id,image_location,master_tble_id,real_file_name,FILE_TYPE from common_photo_library where form_name='trims_order_receive' and master_tble_id='$id'";
  	//$img_val =  return_field_value("master_tble_id","common_photo_library","form_name='trims_order_receive' and master_tble_id=$id","master_tble_id");
    $img_sql = "SELECT id,image_location,master_tble_id,real_file_name,FILE_TYPE from common_photo_library where form_name='trims_order_receive' and master_tble_id='$id'";
    $img_sql_res = sql_select($img_sql);
    if(count($img_sql_res)==0){ echo "<div style='text-align:center;color:red;font-size:18px;'>Image/File is not available.</div>";die();}
    foreach($img_sql_res as $img)
    {
        //if($img[FILE_TYPE]==1){
			echo '<p style="display:inline-block;word-wrap:break-word;word-break:break-all;width:115px;padding-right:10px;vertical-align:top;margin:0px;"><a href="?action=downloiadFile&file='.urlencode($img[csf("image_location")]).'"><img src="../../../../file_upload/blank_file.png" width="89px" height="97px"></a><br>'.$img[csf("real_file_name")].'</p>'; 
		//}
    }
}

if($action=="get_user_pi_file")
{
	echo load_html_head_contents("File View", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	?>
	<fieldset style="width:600px; margin-left:5px">
		<div style="width:100%; word-wrap:break-word" id="scroll_body">
             <table border="0" rules="all" width="100%" cellpadding="2" cellspacing="2">
             	<tr>
					<?
					$i=0;
                    $sql="SELECT id,image_location,master_tble_id,real_file_name,FILE_TYPE from common_photo_library where form_name='trims_order_receive' and master_tble_id='$id'";
                    $result=sql_select($sql);
                    foreach($result as $row)
                    {
						$i++;
                    ?>
                    	<td width="100" align="center"><a target="_blank" href="../../../../<? echo $row[csf('image_location')]; ?>"><img width="89" height="97" src="../../../../file_upload/blank_file.png"><br>File-<? echo $i; ?></a></td>
                    <?
						if($i%6==0) echo "</tr><tr>";
                    }
                    ?>
                </tr>
            </table>
        </div>	
	</fieldset>     
	<?
	exit();
}
if($action=='production_qty_popup') {
	echo load_html_head_contents('Popup Info', '../../../../', 1, 1, $unicode);
	extract($_REQUEST);
	// $supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0", 'id', 'buyer_name');
	$trimsGroupArr = return_library_array("select id, item_name from lib_item_group where item_category=4 and status_active=1","id","item_name");
	$colorNameArr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$sizeArr = return_library_array("select id, size_name from lib_size where status_active=1 and is_deleted=0", 'id', 'size_name');
	//$item_group_name = return_field_value("item_name","lib_item_group","id=".$product_sql[0][csf("item_group_id")],"item_name");	?>
	<style>
		tbody tr td {
			text-align: center;
		}
	</style>
    <table width="995" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all">
    	<thead>
        	<tr> 					
            	<th width="30">S.L</th>
                <th width="100">System ID</th>
                <th width="100">Transection Date</th>
                <th width="80">Customer Name</th>
                <th width="120">Job Card No</th>
                <th width="60">Item Name</th>
                <th width="125">Item Description</th>
                <th width="125">Item Color</th>
                <th width="50">Item Size</th>
                <th width="50">Order UOM</th>
                <th width="50">Production Qty</th>
                <th>Production Amount [Tk]</th>
            </tr>
        </thead>
        <tbody>
		<?php
		$details_sql="select a.trims_production, a.production_date, a.party_id, c.job_no_mst, b.item_description, b.color_id, b.size_id, b.uom, b.production_qty
						from trims_production_mst a, trims_production_dtls b, trims_job_card_dtls c
						where b.job_dtls_id=$jobDtlsId and a.id=b.mst_id and c.id = b.job_dtls_id";
		//echo $details_sql;
		$sql_result=sql_select($details_sql); $t=1;
		$total_prod_qty=0;
		$total_prod_amt=0;
		foreach($sql_result as $row)
		{
			$prod_qty = $row[csf('production_qty')];
			$amount = $prod_qty*$orderRate*$exchangeRate;
			if ($t%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$delevery_amt=$row[csf("delevery_qty")]*$rate; ?>
        	<tr bgcolor="<?php echo $bgcolor; ?>" onclick="change_color('tr_<?php echo $t; ?>','<?php echo $bgcolor; ?>')" id="tr_<?php echo $t; ?>">
        		<td><p><?php echo $t; ?></p></td>
                <td><p><?php echo $row[csf('trims_production')]; ?></p></td>
                <td><p><?php echo change_date_format($row[csf('production_date')]); ?></p></td>
                <td><p><?php echo $buyerArr[$row[csf('party_id')]]; ?></p></td>
                <td><p><?php echo $row[csf('job_no_mst')]; ?></p></td>
                <td><p><?php echo $section; ?></p></td>
                <td><p><?php echo $row[csf('item_description')]; ?></p></td>
                <td><p><?php echo $colorNameArr[$row[csf('color_id')]]; ?></p></td>
                <td><p><?php echo $sizeArr[$row[csf('size_id')]]; ?></p></td>
                <td><p><?php echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
                <td style="text-align: right;"><p><?php echo $prod_qty; ?></p></td>
                <td style="text-align: right;"><p><?php echo number_format($amount, 2); ?></p></td>
            </tr>
            <?php
            $total_prod_qty += $prod_qty;
            $total_prod_amt += $amount;
			$t++;
		}
		?>
        </tbody>
        <tfoot>
        	<tr>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
               	<th>&nbsp;</th>
                <th>Total</th>
               	<th align="right"><?php echo number_format($total_prod_qty, 2); ?></th>
              	<th align="right"><?php echo number_format($total_prod_amt, 2); ?></th>
            </tr>
        </tfoot>
    </table>
    <?php
}

if($action=='qc_qty_popup') {
	echo load_html_head_contents('Popup Info', '../../../../', 1, 1, $unicode);
	extract($_REQUEST);
	// $supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0", 'id', 'buyer_name');
	$trimsGroupArr = return_library_array("select id, item_name from lib_item_group where item_category=4 and status_active=1","id","item_name");
	$colorNameArr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$sizeArr = return_library_array("select id, size_name from lib_size where status_active=1 and is_deleted=0", 'id', 'size_name');
	//$item_group_name = return_field_value("item_name","lib_item_group","id=".$product_sql[0][csf("item_group_id")],"item_name");	?>
	<style>
		tbody tr td {
			text-align: center;
		}
	</style>
    <table width="995" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all">
    	<thead>
        	<tr> 					
            	<th width="30">S.L</th>
                <th width="100">System ID</th>
                <th width="100">Transection Date</th>
                <th width="80">Customer Name</th>
                <th width="120">Job Card No</th>
                <th width="60">Item Name</th>
                <th width="130">Item Description</th>
                <th width="130">Item Color</th>
                <th width="50">Item Size</th>
                <th width="50">Order UOM</th>
                <th width="50">QC Qty</th>
                <th>QC Amount [Tk]</th>
            </tr>
        </thead>
        <tbody>
		<?php
		$details_sql="select a.trims_production, a.production_date, a.party_id, c.job_no_mst, b.item_description, b.color_id, b.size_id, b.uom, b.qc_qty
						from trims_production_mst a, trims_production_dtls b, trims_job_card_dtls c
						where b.job_dtls_id=$jobDtlsId and a.id=b.mst_id and c.id = b.job_dtls_id";
		//echo $details_sql;
		$sql_result=sql_select($details_sql); $t=1;
		$total_qc_qty=0;
		$total_qc_amt=0;
		foreach($sql_result as $row)
		{
			$qc_qty = $row[csf('qc_qty')];
			$amount = $qc_qty*$orderRate*$exchangeRate;
			if ($t%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$delevery_amt=$row[csf("delevery_qty")]*$rate; ?>
        	<tr bgcolor="<?php echo $bgcolor; ?>" onclick="change_color('tr_<?php echo $t; ?>','<?php echo $bgcolor; ?>')" id="tr_<?php echo $t; ?>">
        		<td><p><?php echo $t; ?></p></td>
                <td><p><?php echo $row[csf('trims_production')]; ?></p></td>
                <td><p><?php echo change_date_format($row[csf('production_date')]); ?></p></td>
                <td><p><?php echo $buyerArr[$row[csf('party_id')]]; ?></p></td>
                <td><p><?php echo $row[csf('job_no_mst')]; ?></p></td>
                <td><p><?php echo $section; ?></p></td>
                <td><p><?php echo $row[csf('item_description')]; ?></p></td>
                <td><p><?php echo $colorNameArr[$row[csf('color_id')]]; ?></p></td>
                <td><p><?php echo $sizeArr[$row[csf('size_id')]]; ?></p></td>
                <td><p><?php echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
                <td style="text-align: right;"><p><?php echo $qc_qty; ?></p></td>
                <td style="text-align: right;"><p><?php echo number_format($amount, 2); ?></p></td>
            </tr>
            <?php
            $total_qc_qty += $qc_qty;
            $total_qc_amt += $amount;
			$t++;
		}
		?>
        </tbody>
        <tfoot>
        	<tr>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
               	<th>&nbsp;</th>
                <th>Total</th>
               	<th align="right"><?php echo number_format($total_qc_qty, 2); ?></th>
              	<th align="right"><?php echo number_format($total_qc_amt, 2); ?></th>
            </tr>
        </tfoot>
    </table>
    <?php
}

if($action=='delivery_qty_popup') {
	echo load_html_head_contents('Popup Info', '../../../../', 1, 1, $unicode);
	extract($_REQUEST);
	// $supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0", 'id', 'buyer_name');
	$trimsGroupArr = return_library_array("select id, item_name from lib_item_group where item_category=4 and status_active=1","id","item_name");
	$colorNameArr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$sizeArr = return_library_array("select id, size_name from lib_size where status_active=1 and is_deleted=0", 'id', 'size_name');
	//$item_group_name = return_field_value("item_name","lib_item_group","id=".$product_sql[0][csf("item_group_id")],"item_name");	?>
	<style>
		tbody tr td {
			text-align: center;
		}
	</style>
    <table width="995" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all">
    	<thead>
        	<tr> 					
            	<th width="30">S.L</th>
                <th width="100">System ID</th>
                <th width="100">Transection Date</th>
                <th width="80">Customer Name</th>
                <th width="120">Job Card No</th>
                <th width="60">Item Name</th>
                <th width="130">Item Description</th>
                <th width="120">Item Color</th>
                <th width="50">Item Size</th>
                <th width="50">Order UOM</th>
                <th width="30">Delivery Qty</th>
                <th>Delivery Amount [Tk]</th>
            </tr>
        </thead>
        <tbody>
		<?php
		// $details_sql="select a.trims_production, a.production_date, a.party_id, c.job_no_mst, b.description, b.color_id, b.size_id, b.order_uom, b.delevery_qty
		// from trims_production_mst a, trims_delivery_dtls b, trims_job_card_dtls c
		// where b.job_dtls_id=$jobDtlsId and a.id=b.mst_id and c.id=b.job_dtls_id ";

		$details_sql="select a.trims_del, a.delivery_date, a.party_id, c.job_no_mst, b.description, b.color_id, b.size_id, b.order_uom, b.delevery_qty
		from trims_delivery_mst a, trims_delivery_dtls b, trims_job_card_dtls c
		where b.job_dtls_id=$jobDtlsId and a.id=b.mst_id and c.id=b.job_dtls_id ";

		//echo $details_sql;
		$sql_result=sql_select($details_sql); $t=1;
		$total_delv_qty=0;
		$total_delv_amt=0;
		foreach($sql_result as $row)
		{
			$delv_qty = $row[csf('delevery_qty')];
			$amount = $delv_qty*$orderRate*$exchangeRate;
			if ($t%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
        	<tr bgcolor="<?php echo $bgcolor; ?>" onclick="change_color('tr_<?php echo $t; ?>','<?php echo $bgcolor; ?>')" id="tr_<?php echo $t; ?>">
        		<td><p><?php echo $t; ?></p></td>
                <td><p><?php echo $row[csf('trims_del')]; ?></p></td>
                <td><p><?php echo change_date_format($row[csf('delivery_date')]); ?></p></td>
                <td><p><?php echo $buyerArr[$row[csf('party_id')]]; ?></p></td>
                <td><p><?php echo $row[csf('job_no_mst')]; ?></p></td>
                <td><p><?php echo $section; ?></p></td>
                <td><p><?php echo $row[csf('description')]; ?></p></td>
                <td><p><?php echo $colorNameArr[$row[csf('color_id')]]; ?></p></td>
                <td><p><?php echo $sizeArr[$row[csf('size_id')]]; ?></p></td>
                <td><p><?php echo $unit_of_measurement[$row[csf('order_uom')]]; ?></p></td>
                <td style="text-align: right;"><p><?php echo $delv_qty; ?></p></td>
                <td style="text-align: right;"><p><?php echo number_format($amount, 2); ?></p></td>
            </tr>
            <?php
            $total_delv_qty += $delv_qty;
            $total_delv_amt += $amount;
			$t++;
		}
		?>
        </tbody>
        <tfoot>
        	<tr>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
               	<th>&nbsp;</th>
                <th>Total</th>
               	<th align="right"><?php echo number_format($total_delv_qty, 2); ?></th>
              	<th align="right"><?php echo number_format($total_delv_amt, 2); ?></th>
            </tr>
        </tfoot>
    </table>
    <?php
}

if($action=='bill_qty_popup') {
	echo load_html_head_contents('Popup Info', '../../../../', 1, 1, $unicode);
	extract($_REQUEST);

	// $supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0", 'id', 'buyer_name');
	// $trimsGroupArr = return_library_array("select id, item_name from lib_item_group where item_category=4 and status_active=1","id","item_name");
	$colorNameArr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$sizeArr = return_library_array("select id, size_name from lib_size where status_active=1 and is_deleted=0", 'id', 'size_name');
	//$item_group_name = return_field_value("item_name","lib_item_group","id=".$product_sql[0][csf("item_group_id")],"item_name");	?>
    <table width="995" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all">
    	<thead>
        	<tr> 					
            	<th width="30">S.L</th>
                <th width="130">System ID</th>
                <th width="100">Transection Date</th>
                <th width="80">Customer Name</th>
                <th width="180">Job Card No</th>
                <th width="60">Item Name</th>
                <th width="120">Item Description</th>
                <th width="100">Item Color</th>
                <th width="30">Item Size</th>
                <th width="50">Order UOM</th>
                <th width="50">Bill Qty</th>
                <th>Bill Amount [Tk]</th>
            </tr>
        </thead>
        <tbody>
		<?php
		// $details_sql="select a.trims_production, a.production_date, a.party_id, c.job_no_mst, b.item_description, b.color_id, b.size_id, b.order_uom, b.quantity
		// from trims_production_mst a, trims_bill_dtls b, trims_job_card_dtls c
		// where b.job_dtls_id=$jobDtlsId and a.id=b.mst_id and c.id=b.job_dtls_id ";

		$details_sql="select a.trims_bill, a.bill_date, a.party_id, c.job_no_mst, b.item_description, b.color_id, b.size_id, b.order_uom, b.quantity
		from trims_bill_mst a, trims_bill_dtls b, trims_job_card_dtls c
		where b.job_dtls_id=$jobDtlsId and a.id=b.mst_id and c.id=b.job_dtls_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1";

		// echo $details_sql;
		$sql_result=sql_select($details_sql); $t=1;
		$total_bill_qty=0;
		$total_bill_amt=0;
		foreach($sql_result as $row)
		{
			$bill_qty = $row[csf('quantity')];
			$amount = $bill_qty*$orderRate*$exchangeRate;
			if ($t%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			// $bill_amt=$row[csf("bill_qty")]*$rate; ?>
        	<tr bgcolor="<?php echo $bgcolor; ?>" onclick="change_color('tr_<?php echo $t; ?>','<?php echo $bgcolor; ?>')" id="tr_<?php echo $t; ?>">
        		<td><p><?php echo $t; ?></p></td>
                <td><p><?php echo $row[csf('trims_bill')]; ?></p></td>
                <td><p><?php echo change_date_format($row[csf('bill_date')]); ?></p></td>
                <td><p><?php echo $buyerArr[$row[csf('party_id')]]; ?></p></td>
                <td><p><?php echo $row[csf('job_no_mst')]; ?></p></td>
                <td><p><?php echo $section; ?></p></td>
                <td><p><?php echo $row[csf('item_description')]; ?></p></td>
                <td><p><?php echo $colorNameArr[$row[csf('color_id')]]; ?></p></td>
                <td><p><?php echo $sizeArr[$row[csf('size_id')]]; ?></p></td>
                <td><p><?php echo $unit_of_measurement[$row[csf('order_uom')]]; ?></p></td>
                <td align="right"><p><?php echo $bill_qty; ?></p></td>
                <td align="right"><p><?php echo number_format($amount, 2); ?></p></td>
            </tr>
            <?php
            $total_bill_qty += $bill_qty;
            $total_bill_amt += $amount;
			$t++;
		}
		?>
        </tbody>
        <tfoot>
        	<tr>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
               	<th>&nbsp;</th>
                <th>Total</th>
               	<th align="right"><?php echo number_format($total_bill_qty, 2); ?></th>
              	<th align="right"><?php echo number_format($total_bill_amt, 2); ?></th>
            </tr>
        </tfoot>
    </table>
    <?php
}

if($action=='gate_pass_popup') {
	echo load_html_head_contents('Popup Info', '../../../../', 1, 1, $unicode);
	extract($_REQUEST);

	// $supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0", 'id', 'buyer_name');
	// $trimsGroupArr = return_library_array("select id, item_name from lib_item_group where item_category=4 and status_active=1","id","item_name");
	$colorNameArr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$sizeArr = return_library_array("select id, size_name from lib_size where status_active=1 and is_deleted=0", 'id', 'size_name');
	//$item_group_name = return_field_value("item_name","lib_item_group","id=".$product_sql[0][csf("item_group_id")],"item_name");	?>
    <table width="895" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" style="margin-left: 45px; margin-top:10px">
    	<thead>
        	<tr> 					
            	<th width="50">S.L</th>
                <th width="150">System ID</th>
                <th width="130">Gate Pass Date</th>
                <th width="100">Sent To</th>
                <th width="200">Item Description</th>
                <th width="100">Order UOM</th>
                <th width="">Gate Pass Qty</th>
            </tr>
        </thead>
        <tbody>
		<?php
		// $details_sql="select a.trims_production, a.production_date, a.party_id, c.job_no_mst, b.item_description, b.color_id, b.size_id, b.order_uom, b.quantity
		// from trims_production_mst a, trims_bill_dtls b, trims_job_card_dtls c
		// where b.job_dtls_id=$jobDtlsId and a.id=b.mst_id and c.id=b.job_dtls_id ";

		$sql_system_id_search = "select a.id,a. company_id, a.sys_number_prefix_num, a.sys_number, a.basis,a.within_group, a.sent_by, a.sent_to, a.out_date,a.challan_no,b.item_description,b.uom,b.quantity
		from inv_gate_pass_mst a, inv_gate_pass_dtls b
		where a.id=b.mst_id and a.sys_number='$jobDtlsId' and a.status_active=1 and a.is_deleted=0 and (a.is_gate_out!=1 or a.is_gate_out is null) and a.basis=50
		order by a.sys_number desc";//$outIds_cond

	    //echo $sql_system_id_search;
		$sql_result=sql_select($sql_system_id_search); $t=1;
		$total_bill_qty=0;
		$total_bill_amt=0;
		foreach($sql_result as $row)
		{
			$bill_qty = $row[csf('quantity')];
			if ($t%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			// $bill_amt=$row[csf("bill_qty")]*$rate; ?>
        	<tr bgcolor="<?php echo $bgcolor; ?>" onclick="change_color('tr_<?php echo $t; ?>','<?php echo $bgcolor; ?>')" id="tr_<?php echo $t; ?>">
        		<td><p><?php echo $t; ?></p></td>
                <td><p><?php echo $row[csf('sys_number')]; ?></p></td>
                <td><p><?php echo change_date_format($row[csf('out_date')]); ?></p></td>
                <td><p><?php echo $row[csf('sent_to')]; ?></p></td>
                <td align="center"> <p><?php echo $row[csf('item_description')]; ?></p></td>
                <td align="center"><p><?php echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
                <td align="right"><p><?php echo $row[csf('quantity')]; ?></p></td>
            </tr>
            <?php
            $total_quantity += $row[csf('quantity')];
			$t++;
		}
		?>
        </tbody>
        <tfoot>
        	<tr>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
               	<th>&nbsp;</th>
                <th>&nbsp;</th>
               	<th align="right">Total</th>
              	<th align="right"><?php echo number_format($total_quantity, 2); ?></th>
            </tr>
        </tfoot>
    </table>
    <?php
}

?>