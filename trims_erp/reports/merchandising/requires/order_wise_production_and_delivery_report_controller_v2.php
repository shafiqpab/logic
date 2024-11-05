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
	//$leaderArr = return_library_array("select id, team_leader_name from lib_marketing_team where status_active=1 and is_deleted=0 and project_type=3","id","team_leader_name");
	//$memberArr = return_library_array("select id, team_member_name from lib_mkt_team_member_info","id","team_member_name");

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
	if($cbo_section_id){$where_con.=" and b.section='$cbo_section_id'";} 
	if($cbo_order_source){$where_con.=" and a.within_group='$cbo_order_source'";} 
	if($cbo_customer_name){$where_con.=" and a.party_id='$cbo_customer_name'";}
	if($cbo_location_name){$where_con.=" and a.location_id = $cbo_location_name";}
	
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
		 
		 
		/*$trims_buyer_po_id=implode(",", $buyer_po_id_arr);
		if($trims_buyer_po_id!='')
		{
			
			$trimsbuyerpoid=chop($trims_buyer_po_id,','); 
			$buyer_po_id_cond="";
			$trimsbuyerpoids=count(array_unique(explode(",",$trimsbuyerpoid)));
			if($db_type==2 && $trimsbuyerpoids>1000)
			{
				$buyer_po_id_cond=" and (";
				$trimsbuyerpoidArr=array_chunk(explode(",",$trimsbuyerpoid),999); 
				foreach($trimsbuyerpoidArr as $bpoids)
				{
					$bpoids=implode(",",$bpoids);
					$buyer_po_id_cond.=" b.buyer_po_id in($bpoids) or"; 
				}
				$buyer_po_id_cond=chop($buyer_po_id_cond,'or ');
				$buyer_po_id_cond.=")";
			}
			else
			{
				if($trimsbuyerpoid!="")
				{
					$bpoids=implode(",",array_unique(explode(",",$trimsbuyerpoid)));
					$buyer_po_id_cond=" and b.buyer_po_id in($bpoids)";
				}
				else { $buyer_po_id_cond ="";}
			}
		
		}*/
		}
    }
	//echo $buyer_po_id_cond; die;
	$txt_style_cond="";
	if($txt_order_no !="") $order_no_cond = " and b.order_no like('%$txt_order_no%')";
	if($txt_style_ref !="") $txt_style_cond = " and b.buyer_style_ref like('%$txt_style_ref%')";
	
	//print_r($buyer_po_arr); die;
	
	$trims_order_sql="select a.id,a.subcon_job,a.receive_date,a.delivery_date,a.order_no as cust_order_no,a.party_id,a.within_group,a.currency_id,a.team_leader,a.team_member,a.exchange_rate, b.item_group,b.buyer_buyer,b.section,b.sub_section,b.job_no_mst,b.order_no,b.booked_uom,b.order_uom,b.buyer_po_no,b.buyer_po_id,c.id as break_id,b.cust_style_ref, c.description,c.order_id,c.item_id,c.color_id,c.size_id,c.qnty,c.rate ,c.amount,c.booked_qty,c.delivery_status, b.buyer_style_ref, a.trims_ref
	from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c
	where a.subcon_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id and a.entry_form=255 and a.company_id =$cbo_company_id  $buyer_po_id_cond $order_no_cond $where_con $delivery_status_con $txt_style_cond
	and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 "; 
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
		$trims_data_arr[$key][break_ids].=$row[csf("break_id")].',';

		//$dataKey="."[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("buyer_po_id")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]]".";
		
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("buyer_po_id")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['subcon_job']=$row[csf("subcon_job")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("buyer_po_id")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['id']=$row[csf("id")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("buyer_po_id")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['order_id']=$row[csf("order_id")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("buyer_po_id")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['item_id']=$row[csf("item_id")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("buyer_po_id")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['color_id']=$row[csf("color_id")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("buyer_po_id")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['item_group']=$row[csf("item_group")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("buyer_po_id")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['receive_date']=$row[csf("receive_date")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("buyer_po_id")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['delivery_date']=$row[csf("delivery_date")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("buyer_po_id")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['order_qty']=$trims_data_arr[$key][qnty];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("buyer_po_id")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['order_rate']=$row[csf("rate")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("buyer_po_id")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['order_amount']=$trims_data_arr[$key][amount];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("buyer_po_id")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['booked_qty']=$trims_data_arr[$key][booked_qty];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("buyer_po_id")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['cust_order_no']=$row[csf("cust_order_no")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("buyer_po_id")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['party_id']=$row[csf("party_id")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("buyer_po_id")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['within_group']=$row[csf("within_group")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("buyer_po_id")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['buyer_buyer']=$row[csf("buyer_buyer")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("buyer_po_id")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['section']=$row[csf("section")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("buyer_po_id")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['sub_section']=$row[csf("sub_section")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("buyer_po_id")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['booked_uom']=$row[csf("booked_uom")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("buyer_po_id")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['order_uom']=$row[csf("order_uom")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("buyer_po_id")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['description']=$row[csf("description")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("buyer_po_id")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['delivery_status']=$row[csf("delivery_status")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("buyer_po_id")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['order_no']=$row[csf("order_no")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("buyer_po_id")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['buyer_po_no']=$row[csf("buyer_po_no")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("buyer_po_id")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['currency_id']=$row[csf("currency_id")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("buyer_po_id")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['team_leader']=$row[csf("team_leader")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("buyer_po_id")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['team_member']=$row[csf("team_member")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("buyer_po_id")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['break_ids']=$row[csf("break_ids")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("buyer_po_id")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['buyer_po_id']=$row[csf("buyer_po_id")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("buyer_po_id")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['style']=$row[csf("cust_style_ref")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("buyer_po_id")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['size_id']=$row[csf("size_id")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("buyer_po_id")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['exchange_rate']=$row[csf("exchange_rate")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("buyer_po_id")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['rate']=$row[csf("rate")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("buyer_po_id")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['buyer_style_ref']=$row[csf("buyer_style_ref")];
		$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("buyer_po_id")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]][$row[csf("size_id")]]['trims_ref']=$row[csf("trims_ref")];


		/*array(
		subcon_job=>$row[csf("subcon_job")],,
		id=>$row[csf("id")],
		order_id=>$row[csf("order_id")],
		item_id=>$row[csf("item_id")],
		color_id=>$row[csf("color_id")],
		item_group=>$row[csf("item_group")],
		receive_date=>$row[csf("receive_date")],
		delivery_date=>$row[csf("delivery_date")],
		order_qty=>$trims_data_arr[$key][qnty],
		order_rate=>$row[csf("rate")],
		order_amount=>$trims_data_arr[$key][amount],
		booked_qty=>$trims_data_arr[$key][booked_qty],
		cust_order_no=>$row[csf("cust_order_no")],
		party_id=>$row[csf("party_id")],
		within_group=>$row[csf("within_group")],
		buyer_buyer=>$row[csf("buyer_buyer")],
		section=>$row[csf("section")],
		sub_section=>$row[csf("sub_section")],
		booked_uom=>$row[csf("booked_uom")],
		order_uom=>$row[csf("order_uom")],
		description=>$row[csf("description")],
		delivery_status=>$row[csf("delivery_status")],
		order_no=>$row[csf("order_no")],
		buyer_po_no=>$row[csf("buyer_po_no")],
		currency_id=>$row[csf("currency_id")],
		team_leader=>$row[csf("team_leader")],
		team_member=>$row[csf("team_member")],
		break_ids=>$trims_data_arr[$key][break_ids],
		buyer_po_id=>$row[csf("buyer_po_id")],
		style=>$row[csf('cust_style_ref')],
		size_id=>$row[csf('size_id')]
		);*/
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
	$trims_job_sql="select a.id,a.trims_job,a.received_no from trims_job_card_mst a where a.status_active=1 and a.is_deleted=0 $trimsreceiveid_cond "; 
	// echo $trims_job_sql;
	$trims_job_no_arr=array();	
	$result_trims_job_sql = sql_select($trims_job_sql);
	foreach($result_trims_job_sql as $row)
	{
		$trims_job_no_arr[$row[csf("received_no")]]=$row[csf("trims_job")];
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
	$trims_production_sql="select a.section_id,c.sub_section,a.received_id,a.job_id,b.job_dtls_id,c.id as job_card_dtls_id,c.item_description,c.color_id,c.size_id,c.uom,c.job_quantity,c.break_id,b.production_qty,b.qc_qty, c.buyer_style_ref
	from trims_production_mst a,trims_production_dtls b,trims_job_card_dtls c
	where a.id=b.mst_id and c.id=b.job_dtls_id $trimsjobid_cond $trimsreceiveid_cond and a.entry_form=269 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $prod_where_con";

	// echo $trims_production_sql;
	// subcon_ord_mst

	$trims_production_data_arr=array();	
	$result_trims_production_sql = sql_select($trims_production_sql);
	foreach($result_trims_production_sql as $row)
	{
		$key=$row[csf("received_id")].'*'.$row[csf("section_id")].'*'.$row[csf("sub_section")].'*'.$row[csf("item_description")].'*'.$row[csf("color_id")].'*'.$row[csf("size_id")].'*'.$row[csf("uom")];
		
		$trims_production_data_arr[$key][qc_qty]+=$row[csf("qc_qty")];
		$trims_production_data_arr[$key][job_quantity]+=$row[csf("job_quantity")];
		$trims_production_data_arr[$key][production_qty]+=$row[csf("production_qty")];
		//$trims_production_data_arr[$key][production_qty]+=$row[csf("production_qty")];
		$trims_production_data_arr[$key][job_dtls_id]=$row[csf("job_dtls_id")];
		$trims_production_data_arr[$key][buyer_style_ref]=$row[csf('buyer_style_ref')];
	}
	
	//Delivery.................................
	/*$trims_delivery_sql="select a.delivery_date,a.received_id,b.delevery_qty,b.order_receive_rate,c.uom,b.section,c.sub_section,b.item_group,b.color_id,b.size_id,b.description,b.delevery_status,b.break_down_details_id
	from trims_delivery_mst a, trims_delivery_dtls b, trims_job_card_dtls c
	where a.id=b.mst_id and c.id=b.job_dtls_id $trimsreceiveid_cond and a.entry_form=208 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $delivery_where_con order by a.delivery_date"; */

	$trims_delivery_sql="select a.delivery_date,a.received_id,b.delevery_qty,b.order_receive_rate,c.booked_uom as uom,b.section,c.sub_section,b.item_group,b.color_id,b.size_id,b.description,b.delevery_status,b.break_down_details_id,c.id from trims_delivery_mst a, trims_delivery_dtls b, subcon_ord_dtls c where a.id=b.mst_id and c.id=b.receive_dtls_id $trimsreceiveid_cond  and a.entry_form=208 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.delivery_date"; 

	// echo $trims_delivery_sql;

	$trims_delivery_data_arr=array();
	$result_trims_delivery_sql = sql_select($trims_delivery_sql);
	foreach($result_trims_delivery_sql as $row)
	{
		$key=$row[csf("item_group")].'*'.$row[csf("received_id")].'*'.$row[csf("section")].'*'.$row[csf("sub_section")].'*'.$row[csf("description")].'*'.$row[csf("color_id")].'*'.$row[csf("size_id")].'*'.$row[csf("uom")];
		
		$trims_delivery_data_arr[$key][delevery_qty]+=$row[csf("delevery_qty")];
		$trims_delivery_data_arr[$key][delevery_val]+=$row[csf("delevery_qty")]*$row[csf("order_receive_rate")];
		$trims_delivery_data_arr[$key][delevery_last_date]=$row[csf("delivery_date")];
		$trims_delivery_data_arr[$key][delevery_status]=$row[csf("delevery_status")];
		$trims_delivery_data_arr[$key][break_ids].=$row[csf("break_down_details_id")].',';
	}
	// echo "<pre>";
	// print_r($trims_delivery_data_arr);
	// echo "</pre>";
	//Bill.................................
	//select SECTION,ITEM_DESCRIPTION,COLOR_ID,SIZE_ID,ORDER_UOM,TOTAL_DELV_QTY,b.QUANTITY     from TRIMS_BILL_MST a, TRIMS_BILL_dtls b where a.id=b.mst_id and a.ENTRY_FORM=276	
	//bill.................................
	/*$trims_bill_sql="select d.received_id,b.section,c.sub_section,b.item_description,b.color_id,b.size_id,c.uom,total_delv_qty,b.quantity,b.bill_amount,b.id from trims_bill_mst a, trims_bill_dtls b,trims_job_card_dtls c, trims_job_card_mst d  where a.id=b.mst_id and c.id=b.job_dtls_id and c.job_no_mst=d.trims_job and a.entry_form=276 and d.received_id in(".implode(',',$trims_receive_id_arr).") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";*/
	/*$trims_bill_sql="select a.received_id,b.section,c.sub_section,b.item_description,b.color_id,b.size_id,c.uom,total_delv_qty,b.quantity,b.bill_amount,b.id
	from trims_bill_mst d, trims_bill_dtls b,trims_job_card_dtls c, trims_job_card_mst a
	where d.id=b.mst_id and c.id=b.job_dtls_id and c.job_no_mst=a.trims_job and d.entry_form=276 and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $bill_where_con";*/

	$trims_bill_sql="select c.mst_id as received_id,c.section,c.sub_section,b.item_description,b.color_id,b.size_id,c.booked_uom as uom ,total_delv_qty,b.quantity,b.bill_amount,b.id from trims_bill_mst d, trims_bill_dtls b, subcon_ord_dtls c, subcon_ord_mst a where d.id=b.mst_id and c.mst_id=a.id and c.order_no=b.order_no and d.entry_form=276 and a.entry_form=255  $bill_where_con and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ";

	// echo $trims_bill_sql;

	$trims_bill_data_arr=array();
	$result_trims_bill_sql = sql_select($trims_bill_sql);
	foreach($result_trims_bill_sql as $row)
	{
		// $key=$row[csf("received_id")].'*'.$row[csf("section")].'*'.$row[csf("sub_section")].'*'.$row[csf("item_description")].'*'.$row[csf("color_id")].'*'.$row[csf("size_id")].'*'.$row[csf("uom")];
		$subSection = $row[csf("sub_section")] ? $row[csf("sub_section")] : '-';
		$key=$row[csf("received_id")].'*'.$row[csf("section")].'*'.$subSection.'*'.$row[csf("item_description")].'*'.$row[csf("color_id")].'*'.$row[csf("size_id")].'*'.$row[csf("uom")];
		// $id.'*'.$sectionId.'*'.$sub_section.'*'.$description.'*'.$colorId.'*'.$sizeId.'*'.$booked_uom;
		
		$trims_bill_data_arr[$key][bill_qty]+=$row[csf("quantity")];
		$trims_bill_data_arr[$key][bill_val]+=$row[csf("bill_amount")];
		$trims_bill_data_arr[$key][dtls_ids].=$row[csf("id")].',';
	}

	//$sizeArr = return_library_array("select master_tble_id, size_name from lib_size where status_active=1 and is_deleted=0", 'master_tble_id','size_name');
	//$img_val =  return_field_value("master_tble_id","common_photo_library","form_name='trims_order_receive'","master_tble_id");

	/*echo '<pre>';
	print_r($trims_bill_data_arr);
	echo '</pre>';*/
	//5647*3*4*dd*4990*23076*2
	$width=3700;
	ob_start();
	?>
	<style>
		table#rpt_table_header tr td {
		    word-break: break-all;
		}
	</style>
	<div style="width:<?php echo $width+18;?>px;" id="scroll_body">
		<table width="<?php echo $width;?>" cellpadding="0" cellspacing="0" id="caption" align="center">
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
        <table border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<?php echo $width;?>" rules="all" id="rpt_table_header" align="left">
			<thead>
				<th width="35">SL</th>
                <th width="100">Customer Name</th>
                <th width="100">Cust. Buyer</th>
                <th width="100">Cust. Work Order</th>
                <th width="100">Attached File</th>
                <th width="100">Trims Job No</th>
                <th width="100">Style</th>
                <th width="100">Internal Ref</th>
                <th width="100">Trims Ref</th>
                <th width="100">Section</th>
                <th width="100">Item Group</th>
                <th width="130">Item Description</th>
                <th width="100">Item Color</th>
                <th width="100">Item Size</th>
                <th width="100">Order UOM</th>
                <th width="100">Req. Qty/Ord Rcv</th>
                <th width="100">Booked UOM</th>
                <th width="100">Booked Qty</th>
                <th width="100">Rate</th>
                <th width="100">Ord Rcv Currency</th>
                <th width="100">Req. Value [Tk]</th>
                <th width="100">Prod. Qty</th>
                <th width="100">Prod. Value [Tk]</th>
                <th width="100">Prod. Bal. Qty</th>
                <th width="100">Prod. Bal. Value [Tk]</th>
                <th width="100">QC. Qty</th>
                <th width="100">QC. Bal. Qty</th>
                <th width="100">Deli. Qty.</th>
                <th width="100">Deli. Value [Tk]</th>
                <th width="100">Deli. Balance Qty</th>
                <th width="100">Deli. Balance Value [Tk]</th>
                <th width="100">Bill Qty</th>
                <th width="100">Bill Amount [Tk]</th>
                <th width="100">Bill Balance Qty</th>
                <th width="100">Bill Balance Amount [Tk]</th>
			</thead>
			<tbody>
		<!-- <div style="width:<?php // echo $width;?>px; float:left; overflow-y:scroll;" id="scroll_body">
		<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="<?php echo $width;?>" rules="all" align="left"> -->
            <?php 
			$i=1;
			$total_order_qty=0;$total_order_val=0;$total_booked_qty=0;$total_production_qty=0;

			$row_partyArray=$row_bBArray=$row_ordNoArray=$row_subcon_jobArray=$row_cusStyleArray=$row_buyerPoIdArray=$row_sectionArray=$row_itemGroupArray=$row_orderUomArray=$row_descriptionArray=$row_colorArray=array(); 

			//$pi_rowspan_arr=array(); $pi_dtls_rowspan_arr=array();
			foreach($data_array as $party_id =>$partyArray)
			{
				$party_rowspan=0;		
				foreach( $partyArray as $buyerBuyer =>$bBArray )
			    {   	
			    	$buyer_rowspan=0;
					foreach( $bBArray as $ordNo =>$ordNoArray )
			    	{   
			    		$ordNo_rowspan=0;		
			    		foreach( $ordNoArray as $subcon_job =>$subcon_jobArray )
			        	{
			        		$subcon_job_rowspan = 0;
			        		foreach ($subcon_jobArray as $cusStyle => $cusStyleArray)
			        		{
			        			$cusStyle_rowspan = 0;
			        			foreach ($cusStyleArray as $buyerPOId => $buyerPoIdArray)
				        		{
				        			$buyerPoId_rowspan = 0;
				        			foreach ($buyerPoIdArray as $section => $sectionArray)
				        			{
				        				$section_rowspan = 0;
						        		foreach ($sectionArray as $itemGroup => $itemGroupArray)
						        		{
						        			$itemGroup_rowspan = 0;
						        			foreach ($itemGroupArray as $orderUom => $orderUomArray)
						        			{
						        				$orderUom_rowspan = 0;
						        				foreach ($orderUomArray as $description => $descriptionArray)
							        			{
							        				$description_rowspan = 0;
							        				foreach ($descriptionArray as $colorId => $colorIdArray)
								        			{
								        				$color_rowspan = 0;
								        				foreach ($colorIdArray as $sizeId => $dataRow)
									        			{
									        				//echo  $dataRow.'==';
									        				$party_rowspan++;
									        				$buyer_rowspan++;
									        				$ordNo_rowspan++;
									        				$subcon_job_rowspan++;
									        				$cusStyle_rowspan++;
									        				$buyerPoId_rowspan++;
									        				$section_rowspan++;
									        				$itemGroup_rowspan++;
									        				$orderUom_rowspan++;
									        				$description_rowspan++;
									        				$color_rowspan++;
									        			}
									        			$row_colorArray[$party_id][$buyerBuyer][$ordNo][$subcon_job][$cusStyle][$buyerPOId][$section][$itemGroup][$orderUom][$description][$colorId]=$color_rowspan;
									        		}
								        			$row_descriptionArray[$party_id][$buyerBuyer][$ordNo][$subcon_job][$cusStyle][$buyerPOId][$section][$itemGroup][$orderUom][$description]=$description_rowspan;
								        		}
								        		$row_orderUomArray[$party_id][$buyerBuyer][$ordNo][$subcon_job][$cusStyle][$buyerPOId][$section][$itemGroup][$orderUom]=$orderUom_rowspan;
						        			}
						        			$row_itemGroupArray[$party_id][$buyerBuyer][$ordNo][$subcon_job][$cusStyle][$buyerPOId][$section][$itemGroup]=$itemGroup_rowspan;
						        		}
						        		$row_sectionArray[$party_id][$buyerBuyer][$ordNo][$subcon_job][$cusStyle][$buyerPOId][$section]=$section_rowspan;
				        			}
				        			$row_buyerPoIdArray[$party_id][$buyerBuyer][$ordNo][$subcon_job][$cusStyle][$buyerPOId]=$buyerPoId_rowspan;
			        			}
			        			$row_cusStyleArray[$party_id][$buyerBuyer][$ordNo][$subcon_job][$cusStyle]=$cusStyle_rowspan;
			        		}
			        		$row_subcon_jobArray[$party_id][$buyerBuyer][$ordNo][$subcon_job]=$subcon_job_rowspan;
			            }
			            $row_ordNoArray[$party_id][$buyerBuyer][$ordNo]=$ordNo_rowspan;
				    }
				    $row_bBArray[$party_id][$buyerBuyer]=$buyer_rowspan;
				}
				$row_partyArray[$party_id]=$party_rowspan;
			}
			/*echo '<pre>';
			print_r($data_array);
			echo '</pre>'; die;*/
			//$data_array[$row[csf("party_id")]][$row[csf("buyer_buyer")]][$row[csf("cust_order_no")]][$row[csf("subcon_job")]][$row[csf("cust_style_ref")]][$row[csf("section")]][$row[csf("item_group")]][$row[csf("order_uom")]][$row[csf("description")]][$row[csf("color_id")]]
			$totalReqValueTk=0;
			$totalProdValueTk=0;
			$totalProdBalanceValueTk=0;
			$totalDeliveryValueTk=0;
			$totalDeliveryBalanceValueTk=0;
			$totalBillAmountTk=0;
			$totalBillBalanceAmountTk=0;
			foreach($data_array as $party_id =>$partyArray)
			{
				$party_rowspan=0;		
				foreach( $partyArray as $buyerBuyer =>$bBArray )
			    {   	
			    	$buyer_rowspan=0;
					foreach( $bBArray as $ordNo =>$ordNoArray )
			    	{   
			    		$ordNo_rowspan=0;		
			    		foreach( $ordNoArray as $subcon_job =>$subcon_jobArray )
			        	{
			        		$subcon_job_rowspan = 0;
			        		foreach ($subcon_jobArray as $cusStyle => $cusStyleArray)
			        		{
			        			$cusStyle_rowspan = 0;
			        			foreach ($cusStyleArray as $buyerPOId => $buyerPoIdArray)
			        			{
			        				$buyerPoId_rowspan = 0;
				        			foreach ($buyerPoIdArray as $sectionId => $sectionArray)
				        			{
				        				$section_rowspan = 0;
						        		foreach ($sectionArray as $itemGroup => $itemGroupArray)
						        		{
						        			$itemGroup_rowspan = 0;
						        			foreach ($itemGroupArray as $orderUom => $orderUomArray)
						        			{
						        				$orderUom_rowspan = 0;
						        				foreach ($orderUomArray as $description => $descriptionArray)
							        			{
							        				$description_rowspan = 0;
							        				foreach ($descriptionArray as $colorId => $colorIdArray)
								        			{
								        				$color_rowspan = 0;
								        				foreach ($colorIdArray as $sizeId => $row)
									        			{
									        				// echo $sizeId.'==';
									        				//list($id,$order_id,$section,$sub_section,$item_group,$item_id,$description,$colorId,$sizeId,$booked_uom,$rate)=explode('*',$keysss);
															//$key=$row[csf("id")].'*'.$row[csf("order_id")].'*'.$row[csf("section")].'*'.$row[csf("sub_section")].'*'.$row[csf("item_group")].'*'.$row[csf("item_id")].'*'.$row[csf("description")].'*'.$row[csf("color_id")].'*'.$row[csf("size_id")].'*'.$row[csf("booked_uom")].'*'.$row[csf("rate")];
															
															$id=$row['id'];
															$sub_section=$row['sub_section'];
															//$sizeId=$row["size_id"];
															$booked_uom=$row['booked_uom'];

															$key=$id.'*'.$sectionId.'*'.$sub_section.'*'.$description.'*'.$colorId.'*'.$sizeId.'*'.$booked_uom;
															$orderKey=$id.'*'.$row[order_id].'*'.$sectionId.'*'.$sub_section.'*'.$itemGroup.'*'.$row[item_id].'*'.$row[description].'*'.$row[color_id].'*'.$row[size_id].'*'.$row[booked_uom].'*'.$row[rate];
															// $row[csf("item_group")].'*'.$row[csf("received_id")].'*'.$row[csf("section")].'*'.$row[csf("sub_section")].'*'.$row[csf("description")].'*'.$row[csf("color_id")].'*'.$row[csf("size_id")].'*'.$row[csf("uom")];
															//$production_qty_on_order_parcent=($trims_production_data_arr[$key][qc_qty]/$trims_production_data_arr[$key][job_quantity])*$row[order_qty];
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
										               		$trims_ref = $row['trims_ref'];
										               		$prodValueTk = $prod_qty*$orderRate*$exchangeRate;
										               		$buyer_style_ref = $trims_production_data_arr[$key][buyer_style_ref] ? $trims_production_data_arr[$key][buyer_style_ref] : '';
										               		$prod_jobdtls_id = $trims_production_data_arr[$key][job_dtls_id];
										               		$prodBalanceQty = $row[booked_qty] - $prod_qty;
										               		$prodBalanceValTk = $prodBalanceQty*$rate*$exchangeRate;
										               		$qc_qty = $trims_production_data_arr[$key][qc_qty];
										               		$qcBalanceQty = $row[booked_qty] - $prod_qty;
										               		$delv_qty = $trims_delivery_data_arr[$itemGroup.'*'.$key][delevery_qty];
										               		$deliValueTk = $delv_qty*$rate*$exchangeRate;
										               		$delv_balance_qty = $orderQty - $delv_qty;
										               		$delv_balance_value = $delv_balance_qty*$rate*$exchangeRate;
										               		//echo $key;
										               		$bill_qty = $trims_bill_data_arr[$key][bill_qty];
										               		//echo $bill_qty.'='.$key;
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
												            	
												            	<? if($party_rowspan==0){ ?>
												                	<td width="35" align="center" align="center" rowspan="<? echo $row_partyArray[$party_id]; ?>" > <?php echo $i;?></td>
												                	<td width="100" align="center" rowspan="<? echo $row_partyArray[$party_id]; ?>" ><p><?php echo $party;?></p></td>
												                <? } ?>
																<? if($buyer_rowspan==0){ ?>
												                	<td width="100" align="center" rowspan="<? echo $row_bBArray[$party_id][$buyerBuyer]; ?>" ><p><?php echo $buyer_buyer;?></p></td>
												                <? } ?>
												                <? if($ordNo_rowspan==0){ ?>
												                	<td width="100" align="center" rowspan="<? echo $row_ordNoArray[$party_id][$buyerBuyer][$ordNo]; ?>" align="center"><p><?php echo $row[cust_order_no];?></p></td>
												                	<td width="100" align="center" rowspan="<? echo $row_ordNoArray[$party_id][$buyerBuyer][$ordNo]; ?>" align="center"><a href="javascript:void()" onClick="downloiadFile('<? echo $id; ?>','<? echo $cbo_company_id; ?>');">
                                    								<? if ($img_val != '') echo 'View File'; ?></a></td>
												                <? } ?>
												                <? if($ordNo_rowspan==0){ ?>
												                	<td width="100" align="center" rowspan="<? echo $row_subcon_jobArray[$party_id][$buyerBuyer][$ordNo][$subcon_job]; ?>" align="center"><p><?php echo $row[subcon_job]; ?></p></td>
												                <? } ?>
												                <? if($cusStyle_rowspan==0){ ?>
												                	<td width="100" align="center" rowspan="<? echo $row_cusStyleArray[$party_id][$buyerBuyer][$ordNo][$subcon_job][$cusStyle]; ?>" align="center"><p><?php echo $row[buyer_style_ref]; ?></p></td>
												                <? } ?>
												                <? if($buyerPoId_rowspan==0){ ?>
												                	<td width="100" align="center" rowspan="<? echo $row_buyerPoIdArray[$party_id][$buyerBuyer][$ordNo][$subcon_job][$cusStyle][$buyerPOId]; ?>" align="center"><p><?php echo $internal_ref; ?></p></td>
												                	<td width="100" align="center" rowspan="<? echo $row_buyerPoIdArray[$party_id][$buyerBuyer][$ordNo][$subcon_job][$cusStyle][$buyerPOId]; ?>" align="center"><p><?php echo $trims_ref; ?></p>
												                	</td>
												                <? } ?>
												                <? if($section_rowspan==0){
												                	//echo $party_id.'=='.$buyerBuyer.'=='.$ordNo.'=='.$subcon_job.'=='.$cusStyle.'=='.$buyerPOId.'=='.$sectionId.'++';
												                ?>
												                	<td width="100" align="center" rowspan="<? echo $row_sectionArray[$party_id][$buyerBuyer][$ordNo][$subcon_job][$cusStyle][$buyerPOId][$sectionId]; ?>"><p><?php echo $section_name; ?></p></td>
												                <? } ?>
												                <? if($itemGroup_rowspan==0){
												                	//echo $party_id.'=='.$buyerBuyer.'=='.$ordNo.'=='.$subcon_job.'=='.$cusStyle.'=='.$buyerPOId.'=='.$sectionId.'=='.$itemGroup.'<br>';
												                	// echo $row_itemGroupArray[$party_id][$buyerBuyer][$ordNo][$subcon_job][$cusStyle][$buyerPOId][$sectionId] . '==========';
												                	?>
												                	<td width="100" align="center" rowspan="<? echo $row_itemGroupArray[$party_id][$buyerBuyer][$ordNo][$subcon_job][$cusStyle][$buyerPOId][$sectionId][$itemGroup]; ?>"><p><?php echo $trimsGroupArr[$itemGroup];?></p></td>
												                <? } ?>
												                <? if($description_rowspan==0){ 
												                	//echo $party_id.'=='.$buyerBuyer.'=='.$ordNo.'=='.$subcon_job.'=='.$cusStyle.'=='.$buyerPOId.'=='.$sectionId.'=='.$itemGroup.'=='.$orderUom.'=='.$description.'<br>';
												                	?>
												                	<td width="130" align="center" rowspan="<? echo $row_descriptionArray[$party_id][$buyerBuyer][$ordNo][$subcon_job][$cusStyle][$buyerPOId][$sectionId][$itemGroup][$orderUom][$description]; ?>" ><p><?php echo $description ? $description : '';?></p></td>
												                <? } ?>	
												                <? if($color_rowspan==0){ ?>
												                	<td width="100" rowspan="<? echo $row_colorArray[$party_id][$buyerBuyer][$ordNo][$subcon_job][$cusStyle][$buyerPOId][$sectionId][$itemGroup][$orderUom][$description][$colorId]; ?>" ><p><?php echo $colorNameArr[$colorId];?></p></td>
												               <? } ?>
												                <td width="100" align="center"><p><?php echo $sizeArr[$sizeId]; ?></p></td>
												                <? // if($orderUom_rowspan==0){ ?>
												                	<!-- <td width="100" rowspan="<? // echo $row_orderUomArray[$party_id][$buyerBuyer][$ordNo][$subcon_job][$cusStyle][$buyerPOId][$sectionId][$itemGroup][$orderUom]; ?>" align="center"><?php // echo $unit_of_measurement[$orderUom];?></td> -->
												                <? // } ?>
												                <td width="100" align="center">
												                	<?php echo $unit_of_measurement[$orderUom];?>
												                </td>
												                <td width="100" align="center"><?php echo number_format($row[order_qty], 2); ?></td>
												                <td width="100" align="center">
												                	<?php echo $unit_of_measurement[$row[booked_uom]]; ?>
												                </td>
												                <td width="100" align="center">
												                	<?php echo number_format($row[booked_qty], 2); ?>
												                </td>
												                <td width="100" align="center"><?php echo number_format($row[rate], 4);?></td>
												                <td width="100" align="center"><p><?php echo $currency[$row['currency_id']]; ?></p></td>
												                <td width="100" align="right"><p><?php echo number_format($req_value_tk, 2);?></p></td>
												                <td width="100" align="center">
												                	<?php
												                		if(!$prod_qty) {
													                		echo $prod_qty;
													                	} else {
													                ?>
													                <a href="##" onclick="fnc_amount_details(<?php echo "$prod_jobdtls_id, $row[rate], $exchangeRate, '$internal_ref', '$section_name'"; ?>, 'production_qty_popup', 'Production Quantity')">
												                		<p><?php echo $prod_qty; ?></p>
												                	</a>
													                <?php
													                	}
													                ?>
												                </td>
												                <td width="100" align="right"><p><?php echo number_format($prodValueTk, 2); ?></p></td>
												                <td width="100" align="center"><p><?php echo number_format($prodBalanceQty, 2); ?></p></td>
												                <td width="100" align="right"><p><?php echo number_format($prodBalanceValTk, 2); ?></p></td>
												                <td width="100" align="center">
												                	<a href="##" onclick="fnc_amount_details(<?php echo "$prod_jobdtls_id, $row[rate], $exchangeRate, '$internal_ref', '$section_name'"; ?>, 'qc_qty_popup', 'QC Quantity')">
												                		<p><?php echo $qc_qty; ?></p>
												                	</a>
												                </td>
												                <td width="100" align="center"><p><?php echo number_format($qcBalanceQty, 2); ?></p></td>
												                <td width="100" align="center">
												                	<a href="##" onclick="fnc_amount_details(<?php echo "$prod_jobdtls_id, $row[rate], $exchangeRate, '$internal_ref', '$section_name'"; ?>, 'delivery_qty_popup', 'Delivery Quantity')">
													                	<p><?php echo $delv_qty; ?></p>
													                </a>
												                </td>
												                <td width="100" align="right">
												                	<p>
													                	<?php
																		//$DelvBalanceQty=number_format($row[order_qty]-$trims_delivery_data_arr[$item_group.'*'.$key][delevery_qty],0);
																		// $DelvBalanceQty=$row[order_qty]-$trims_delivery_data_arr[$item_group.'*'.$key][delevery_qty];
																		// if($delv_balance_qty<=1){
																		//  	echo $DelvBalanceQty=0;
																		// }else{
																		// 	$DelvBalanceQty=number_format($row[order_qty]-$trims_delivery_data_arr[$item_group.'*'.$key][delevery_qty],0);
																		// 	echo $DelvBalanceQty;
																		// }
																		// echo number_format($row[order_qty],0)-number_format($trims_delivery_data_arr[$item_group.'*'.$key][delevery_qty],0);
																		//echo number_format($row[booked_qty]-$trims_delivery_data_arr[$item_group.'*'.$key][delevery_qty],0);
													                		echo number_format($deliValueTk, 2);
																			// echo $delv_balance_qty;
																		?>
																	</p>
																</td>
																<td width="100" align="center">
																<?php
												                if($delv_balance_qty>0){
												                	?>
												                	<!-- <a href="##" onclick="fnc_amount_details('<?php //echo $trims_delivery_data_arr[$item_group.'*'.$key][break_ids];?>','<?php //echo $row[order_rate];?>','delivery_popup')">
												                		<p><?php //echo number_format($delv_balance_qty, 2);?></p>
												                		</a> -->
												                	<p><?php echo number_format($delv_balance_qty, 2);?></p>
												                	<?php
												                }else{
												                	?><?php echo '0'; ?><?php
												                } ?>
												                </td>
												                <td width="100" align="right">
												                	<p>
												                		<?php echo number_format($delv_balance_value, 2); ?>          			
												                	</p>
												                </td>
												                <td width="100" align="center">
												                	<p>
														                <?php // echo $orderKey;
														                	if($bill_qty > 0) {
														                	?>
																				<a href="##" onclick="fnc_amount_details(<?php echo "$prod_jobdtls_id, $row[rate], $exchangeRate, '$internal_ref', '$section_name'"; ?>, 'bill_qty_popup', 'Bill Quantity')">
														                			<?php echo $bill_qty; ?>
														                		</a>
														                	<?php 
														                	} else {
														                		echo 0;
														                	}
													                	?>
												            		</p>
												            	</td>
												                <td width="100" align="right">
												                <?php
												                if($bill_amt>0){
												                	?>
												                	<p><?php echo number_format($bill_amt, 2); ?></p>
												                	<?
												                }else{
												                	?><?php echo '0'; ?><?php
												                }?>
												                </td>
												                <td width="100" align="center">
												                	<?php echo
													                	$billBalanceQty;
													                 //number_format($trims_delivery_data_arr[$item_group.'*'.$key][delevery_qty]-$trims_bill_data_arr[$key][bill_qty]);?>        	
												                </td>
												                <td width="100" align="right">
												                	<?php echo number_format($billBalanceAmtTk, 2); ?>
												                </td>
												            </tr>
												            <?php 
															$party_rowspan++; 
															$buyer_rowspan++;
															$ordNo_rowspan++;
															$subcon_job_rowspan++;
															$cusStyle_rowspan++;
															$buyerPoId_rowspan++;
															$section_rowspan++;
															$itemGroup_rowspan++;
															$orderUom_rowspan++;
															$description_rowspan++;
															$color_rowspan++;
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
				$i++;
			}
		?>
			</tbody>
			<tfoot>
				<tr>
					<th width="35" colspan="11" align="right">Total</th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100" align="center"><?php echo number_format($totalReqValueTk, 2); ?></th>
					<th width="100"></th>
					<th width="100" align="center"><?php echo number_format($totalProdValueTk, 2); ?></th>
					<th width="100"></th>
					<th width="100" align="center"><?php echo number_format($totalProdBalanceValueTk, 2); ?></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100" align="center"><?php echo number_format($totalDeliveryValueTk, 2); ?></th>
					<th width="100"></th>
					<th width="100" align="center"><?php echo number_format($totalDeliveryBalanceValueTk, 2); ?></th>
					<th width="100"></th>
					<th width="100" align="center"><?php echo number_format($totalBillAmountTk, 2); ?></th>
					<th width="100"></th>
					<th width="100" align="center"><?php echo number_format($totalBillBalanceAmountTk, 2); ?></th>

				</tr>
			</tfoot>
		</table>
		</div>
		<!-- <table width="<?php // echo $width;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_footer" align="left">
			<tfoot>
		                <th width="35"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="130"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
			</tfoot>
		</table> -->
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
                <th width="120">Work Order No</th>
                <th width="50">Internal Ref</th>
                <th width="60">Item Name</th>
                <th width="100">Item Description</th>
                <th width="100">Item Color</th>
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
                <td><p><?php echo $internalRef; ?></p></td>
                <td><p><?php echo $section; ?></p></td>
                <td><p><?php echo $trimsGroupArr[$row[csf('item_description')]]; ?></p></td>
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
                <th width="120">Work Order No</th>
                <th width="50">Internal Ref</th>
                <th width="60">Item Name</th>
                <th width="100">Item Description</th>
                <th width="100">Item Color</th>
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
                <td><p><?php echo $internalRef; ?></p></td>
                <td><p><?php echo $section; ?></p></td>
                <td><p><?php echo $trimsGroupArr[$row[csf('item_description')]]; ?></p></td>
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
                <th width="120">Work Order No</th>
                <th width="50">Internal Ref</th>
                <th width="60">Item Name</th>
                <th width="100">Item Description</th>
                <th width="100">Item Color</th>
                <th width="50">Item Size</th>
                <th width="50">Order UOM</th>
                <th width="30">Delivery Qty</th>
                <th>Delivery Amount [Tk]</th>
            </tr>
        </thead>
        <tbody>
		<?php
		$details_sql="select a.trims_production, a.production_date, a.party_id, c.job_no_mst, b.description, b.color_id, b.size_id, b.order_uom, b.delevery_qty
		from trims_production_mst a, trims_delivery_dtls b, trims_job_card_dtls c
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
                <td><p><?php echo $row[csf('trims_production')]; ?></p></td>
                <td><p><?php echo change_date_format($row[csf('production_date')]); ?></p></td>
                <td><p><?php echo $buyerArr[$row[csf('party_id')]]; ?></p></td>
                <td><p><?php echo $row[csf('job_no_mst')]; ?></p></td>
                <td><p><?php echo $internalRef; ?></p></td>
                <td><p><?php echo $section; ?></p></td>
                <td><p><?php echo $trimsGroupArr[$row[csf('description')]]; ?></p></td>
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
                <th width="180">Work Order No</th>
                <th width="50">Internal Ref</th>
                <th width="60">Item Name</th>
                <th width="70">Item Description</th>
                <th width="100">Item Color</th>
                <th width="30">Item Size</th>
                <th width="50">Order UOM</th>
                <th width="50">Bill Qty</th>
                <th>Bill Amount [Tk]</th>
            </tr>
        </thead>
        <tbody>
		<?php
		$details_sql="select a.trims_production, a.production_date, a.party_id, c.job_no_mst, b.item_description, b.color_id, b.size_id, b.order_uom, b.quantity
		from trims_production_mst a, trims_bill_dtls b, trims_job_card_dtls c
		where b.job_dtls_id=$jobDtlsId and a.id=b.mst_id and c.id=b.job_dtls_id ";

		// echo $details_sql;
		$sql_result=sql_select($details_sql); $t=1;
		$total_bill_qty=0;
		$total_bill_amt=0;
		foreach($sql_result as $row)
		{
			$bill_qty = $row[csf('quantity')];
			if ($t%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			// $bill_amt=$row[csf("bill_qty")]*$rate; ?>
        	<tr bgcolor="<?php echo $bgcolor; ?>" onclick="change_color('tr_<?php echo $t; ?>','<?php echo $bgcolor; ?>')" id="tr_<?php echo $t; ?>">
        		<td><p><?php echo $t; ?></p></td>
                <td><p><?php echo $row[csf('trims_production')]; ?></p></td>
                <td><p><?php echo change_date_format($row[csf('production_date')]); ?></p></td>
                <td><p><?php echo $buyerArr[$row[csf('party_id')]]; ?></p></td>
                <td><p><?php echo $row[csf('job_no_mst')]; ?></p></td>
                <td><p><?php echo $internalRef; ?></p></td>
                <td><p><?php echo $section; ?></p></td>
                <td><p><?php echo $row[csf('item_description')]; ?></p></td>
                <td><p><?php echo $colorNameArr[$row[csf('color_id')]]; ?></p></td>
                <td><p><?php echo $sizeArr[$row[csf('size_id')]]; ?></p></td>
                <td><p><?php echo $unit_of_measurement[$row[csf('order_uom')]]; ?></p></td>
                <td align="right"><p><?php echo $bill_qty; ?></p></td>
                <td><p></p></td>
            </tr>
            <?php
            $total_bill_qty += $bill_qty;
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
               	<th>&nbsp;</th>
                <th>Total</th>
               	<th align="right"><?php echo number_format($total_bill_qty, 2); ?></th>
              	<th align="right"><?php echo number_format($total_bill_amt, 2); ?></th>
            </tr>
        </tfoot>
    </table>
    <?php
}

?>