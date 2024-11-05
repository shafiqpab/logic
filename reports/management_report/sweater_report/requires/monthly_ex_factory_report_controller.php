<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$supp_library=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name"  );
$lib_country=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );

$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  );
$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  );

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 100, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "" );
	exit();
}

if($action=="load_drop_delivery_company")
{
	$explode_data = explode("**",$data);
	$data = $explode_data[0];
	$selected_company = $explode_data[1];

	if($data==3)
	{
		if($db_type==0)
		{
 			echo create_drop_down( "cbo_delivery_company_name", 150, "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 and find_in_set(22,party_type) order by supplier_name","id,supplier_name", 1, "--- Select ---", $selected, "",0,0 );
		}
		else
		{
			echo create_drop_down( "cbo_delivery_company_name", 150, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=22 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select--", $selected, "" );
		}
	}
 	else if($data==1)
 	{
  		echo create_drop_down( "cbo_delivery_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Delivery Company --", '', "load_drop_down( 'requires/monthly_ex_factory_report_controller', this.value, 'load_drop_down_location', 'location' );",0 );
 	}
 	else
 		echo create_drop_down( "cbo_delivery_company_name", 150, $blank_array,"", 1, "--- Select ---", $selected, "",0,0 );
 	exit();
}


if ($action=="load_drop_down_location")
{
	$companies="'".$data."'";
	echo create_drop_down( "cbo_location_name", 120, "SELECT id,location_name from lib_location where company_id in($data) and status_active =1 and is_deleted=0 group by id,location_name order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/monthly_ex_factory_report_controller', $companies+'**'+this.value, 'load_drop_down_del_floor', 'del_floor_td' );" );
}
if ($action=="load_drop_down_del_floor")
{
	$data=explode('**',$data);
	$data[0]=str_replace("'","",$data[0]);
	echo create_drop_down( "cbo_del_floor", 105, "select id,floor_name from lib_prod_floor where company_id in($data[0]) and location_id='$data[1]' and status_active =1 and is_deleted=0 and production_process=11 order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "" );
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$reportType=str_replace("'","",$reportType);
	$cbo_delivery_company_name=str_replace("'","",$cbo_delivery_company_name);
	$cbo_location_name=str_replace("'","",$cbo_location_name);
	$cbo_delivery_floor=str_replace("'","",$cbo_del_floor);
	$internal_ref=str_replace("'","",$txt_internal_ref);
	$cbo_shipping_status=str_replace("'","",$cbo_shipping_status);
	$cbo_source=str_replace("'","",$cbo_source);
	$_SESSION["source"]="";
	$_SESSION["source"]=$cbo_source;

	$source_cond="";
	if($cbo_source)$source_cond=" and d.source='$cbo_source'";
	$shiping_status_cond=($cbo_shipping_status>0)? " and a.shiping_status= $cbo_shipping_status " : " ";

	if($txt_date_from!="" && $txt_date_to!="")
	{
		$str_cond="and a.ex_factory_date between '$txt_date_from' and  '$txt_date_to ' ";
		$str_cond_sub=" and a.delivery_date between '$txt_date_from' and  '$txt_date_to ' ";
	}
	else
	{
		$str_cond="";
	}
	if($cbo_delivery_company_name)
	{
		 $del_comp_cond="and d.delivery_company_id in( $cbo_delivery_company_name) ";
		 $str_cond_sub.=" and a.company_id in( $cbo_delivery_company_name) ";
		 $str_cond_sub_total.=" and a.company_id in( $cbo_delivery_company_name) ";
	}
	else
	{
		 $del_comp_cond="";
	}
	if($cbo_location_name)
	{
		 $str_cond_sub.="and a.location_id='$cbo_location_name' ";
		 $str_cond_sub_total.="and a.location_id='$cbo_location_name' ";
	}
	else
	{
		 $del_location_cond="";
	}
	if($cbo_company_name)
	{
		 $company_cond=" and c.company_name like '$cbo_company_name' ";
		 $str_cond_sub.=" and a.company_id in( $cbo_company_name) ";
		 $str_cond_sub_total.=" and a.company_id in( $cbo_company_name) ";
	}
	else
	{
		 $company_cond="";
	}
	if($cbo_delivery_floor) $del_floor_cond="and d.delivery_floor_id='$cbo_delivery_floor' "; else $del_floor_cond="";

	if($internal_ref !="") $internal_ref_cond="and b.grouping='$internal_ref'"; else $internal_ref_cond="";

	if(str_replace("'","", $cbo_buyer_name))
	{

		$str_cond_sub.=" and b.party_id in( ".str_replace("'", "",  $cbo_buyer_name).") ";
		$str_cond_sub_total.=" and b.party_id in( ".str_replace("'", "",  $cbo_buyer_name).") ";
		$buyer_conds.=" and c.buyer_name = ".str_replace("'", "",  $cbo_buyer_name) ;
		$buyer_conds2.=" and b.buyer_id = ".str_replace("'", "",  $cbo_buyer_name) ;
	}

	$details_report="";
	$master_data=array();
	$current_date=date("Y-m-d");
	$date=date("Y-m-d");$break_id=0;$sc_lc_id=0;
	$sy = date('Y',strtotime($txt_date_from));
	$basic_smv_arr=return_library_array( "select comapny_id, basic_smv from lib_capacity_calc_mst where year=$sy",'comapny_id','basic_smv');

	$tot_cost_arr = return_library_array("select job_no, cm_for_sipment_sche from wo_pre_cost_dtls","job_no","cm_for_sipment_sche");
	$costing_per_arr = return_library_array("select job_no, costing_per from wo_pre_cost_mst","job_no","costing_per");

	ob_start();
	if($reportType==1)
	{
		$tot_cost_arr = return_library_array("select job_no, cm_cost from wo_pre_cost_dtls","job_no","cm_cost");
	   $subcon_sql_exfac="SELECT c.order_uom, a.vehical_no, a.delivery_no, a.dl_no, a.driver_name, a.mobile_no, b.subcon_job, b.job_no_prefix_num as job, b.party_id as buyer_name, b.company_id as company_name, a.company_id as delivery_company_id, a.location_id, c.cust_style_ref as style_ref_no, c.order_no as po_number, c.id as po_id, a.delivery_date, c.order_quantity as po_quantity, c.delivery_date as shipment_date, c.smv, d.item_id, d.total_carton_qnty, d.delivery_qty as prod_qty, b.insert_date, c.rate as unit_price from subcon_delivery_mst a,subcon_delivery_dtls d, subcon_ord_mst b,subcon_ord_dtls c   where  a.id=d.mst_id and d.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0   and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form<>4  $str_cond_sub ";
		$subcon_exfactory_arr=array();
		$duplicate_challan_check_arr=array();
		$duplicate_vehicle_check_arr=array();
		foreach(sql_select($subcon_sql_exfac) as $vals)
		{
 			$subcon_exfactory_arr[$vals[csf("po_id")]][$vals[csf("item_id")]][$vals[csf("delivery_company_id")]][$vals[csf("location_id")]][$vals[csf("delivery_date")]]["order_uom"]=$vals[csf("order_uom")];

 			$subcon_exfactory_arr[$vals[csf("po_id")]][$vals[csf("item_id")]][$vals[csf("delivery_company_id")]][$vals[csf("location_id")]][$vals[csf("delivery_date")]]["insert_date"]=$vals[csf("insert_date")];

 			$subcon_exfactory_arr[$vals[csf("po_id")]][$vals[csf("item_id")]][$vals[csf("delivery_company_id")]][$vals[csf("location_id")]][$vals[csf("delivery_date")]]["unit_price"]=$vals[csf("unit_price")];

 			$subcon_exfactory_arr[$vals[csf("po_id")]][$vals[csf("item_id")]][$vals[csf("delivery_company_id")]][$vals[csf("location_id")]][$vals[csf("delivery_date")]]["company_name"]=$vals[csf("company_name")];

 			if($subcon_exfactory_arr[$vals[csf("po_id")]][$vals[csf("item_id")]][$vals[csf("delivery_company_id")]][$vals[csf("location_id")]][$vals[csf("delivery_date")]]["vehical_no"]=="")
 			{
 				$subcon_exfactory_arr[$vals[csf("po_id")]][$vals[csf("item_id")]][$vals[csf("delivery_company_id")]][$vals[csf("location_id")]][$vals[csf("delivery_date")]]["vehical_no"]=$vals[csf("vehical_no")];
 				$duplicate_vehicle_check_arr[$vals[csf("vehical_no")]]=$vals[csf("vehical_no")];
 			}
 			else
 			{
 				if(!in_array($vals[csf("vehical_no")], $duplicate_vehicle_check_arr))
 				{

 					$subcon_exfactory_arr[$vals[csf("po_id")]][$vals[csf("item_id")]][$vals[csf("delivery_company_id")]][$vals[csf("location_id")]][$vals[csf("delivery_date")]]["vehical_no"].=','.$vals[csf("vehical_no")];
 					$duplicate_vehicle_check_arr[$vals[csf("vehical_no")]]=$vals[csf("vehical_no")];
 				}
 			}

 			if($subcon_exfactory_arr[$vals[csf("po_id")]][$vals[csf("item_id")]][$vals[csf("delivery_company_id")]][$vals[csf("location_id")]][$vals[csf("delivery_date")]]["delivery_no"]=="")
 			{
 				$subcon_exfactory_arr[$vals[csf("po_id")]][$vals[csf("item_id")]][$vals[csf("delivery_company_id")]][$vals[csf("location_id")]][$vals[csf("delivery_date")]]["delivery_no"]=$vals[csf("delivery_no")];
 				$duplicate_challan_check_arr[$vals[csf("delivery_no")]]=$vals[csf("delivery_no")];

 			}
 			else
 			{
 				if(!in_array($vals[csf("delivery_no")], $duplicate_challan_check_arr))
 				{
 					$subcon_exfactory_arr[$vals[csf("po_id")]][$vals[csf("item_id")]][$vals[csf("delivery_company_id")]][$vals[csf("location_id")]][$vals[csf("delivery_date")]]["delivery_no"].=','.$vals[csf("delivery_no")];
 					$duplicate_challan_check_arr[$vals[csf("delivery_no")]]=$vals[csf("delivery_no")];
 				}
 			}

			$subcon_exfactory_arr[$vals[csf("po_id")]][$vals[csf("item_id")]][$vals[csf("delivery_company_id")]][$vals[csf("location_id")]][$vals[csf("delivery_date")]]["job"]=$vals[csf("job")];

			$subcon_exfactory_arr[$vals[csf("po_id")]][$vals[csf("item_id")]][$vals[csf("delivery_company_id")]][$vals[csf("location_id")]][$vals[csf("delivery_date")]]["buyer_name"]=$buyer_arr[$vals[csf("buyer_name")]];

			$subcon_exfactory_arr[$vals[csf("po_id")]][$vals[csf("item_id")]][$vals[csf("delivery_company_id")]][$vals[csf("location_id")]][$vals[csf("delivery_date")]]["buyer_name2"]=$vals[csf("buyer_name")];

			$subcon_exfactory_arr[$vals[csf("po_id")]][$vals[csf("item_id")]][$vals[csf("delivery_company_id")]][$vals[csf("location_id")]][$vals[csf("delivery_date")]]["po_quantity"]=$vals[csf("po_quantity")];

			$subcon_exfactory_arr[$vals[csf("po_id")]][$vals[csf("item_id")]][$vals[csf("delivery_company_id")]][$vals[csf("location_id")]][$vals[csf("delivery_date")]]["prod_qty"]+=$vals[csf("prod_qty")];

			$subcon_exfactory_arr[$vals[csf("po_id")]][$vals[csf("item_id")]][$vals[csf("delivery_company_id")]][$vals[csf("location_id")]][$vals[csf("delivery_date")]]["total_carton_qnty"]+=$vals[csf("total_carton_qnty")];

			$subcon_exfactory_arr[$vals[csf("po_id")]][$vals[csf("item_id")]][$vals[csf("delivery_company_id")]][$vals[csf("location_id")]][$vals[csf("delivery_date")]]["shipment_date"]=$vals[csf("shipment_date")];

			$subcon_exfactory_arr[$vals[csf("po_id")]][$vals[csf("item_id")]][$vals[csf("delivery_company_id")]][$vals[csf("location_id")]][$vals[csf("delivery_date")]]["po_number"]=$vals[csf("po_number")];
			$subcon_exfactory_arr[$vals[csf("po_id")]][$vals[csf("item_id")]][$vals[csf("delivery_company_id")]][$vals[csf("location_id")]][$vals[csf("delivery_date")]]["style_ref_no"]=$vals[csf("style_ref_no")];

			$subcon_exfactory_arr[$vals[csf("po_id")]][$vals[csf("item_id")]][$vals[csf("delivery_company_id")]][$vals[csf("location_id")]][$vals[csf("delivery_date")]]["smv"]=$vals[csf("smv")];
			$subcon_exfactory_arr[$vals[csf("po_id")]][$vals[csf("item_id")]][$vals[csf("delivery_company_id")]][$vals[csf("location_id")]][$vals[csf("delivery_date")]]["driver"]=$vals[csf("driver_name")];
			$subcon_exfactory_arr[$vals[csf("po_id")]][$vals[csf("item_id")]][$vals[csf("delivery_company_id")]][$vals[csf("location_id")]][$vals[csf("delivery_date")]]["mobile"]=$vals[csf("mobile_no")];
			$subcon_exfactory_arr[$vals[csf("po_id")]][$vals[csf("item_id")]][$vals[csf("delivery_company_id")]][$vals[csf("location_id")]][$vals[csf("delivery_date")]]["dl_no"]=$vals[csf("dl_no")];
		}

		 $subcon_sql_exfac_total="SELECT  a.vehical_no,a.delivery_no,b.subcon_job ,b.job_no_prefix_num as job,b.party_id as buyer_name,b.company_id as company_name,a.company_id as delivery_company_id,a.location_id,c.cust_style_ref as style_ref_no,c.order_no as po_number,c.id as po_id, a.delivery_date   ,c.order_quantity as po_quantity ,c.delivery_date as shipment_date,c.smv,d.item_id,d.total_carton_qnty,  d.delivery_qty as prod_qty,b.insert_date,c.rate as unit_price from subcon_delivery_mst a,subcon_delivery_dtls d, subcon_ord_mst b,subcon_ord_dtls c   where  a.id=d.mst_id and d.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0   and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form<>4  $str_cond_sub_total ";
		$subcon_exfactory_arr_total=array();
		foreach(sql_select($subcon_sql_exfac_total) as $vals)
		{
 			$subcon_exfactory_arr_total[$vals[csf("po_id")]][$vals[csf("item_id")]][$vals[csf("delivery_company_id")]][$vals[csf("location_id")]]["total_ex_fac_sub"]+=$vals[csf("prod_qty")];
		}

		foreach($subcon_exfactory_arr as $po_id=>$item_data)
    	{
    		foreach($item_data as $item_id=>$delivery_company_data)
    		{
    			foreach($delivery_company_data as $delivery_company_id=>$delivery_loc_data)
    			{
    				foreach($delivery_loc_data as $delivery_loc_id=>$delivery_date_data)
    				{
    					foreach($delivery_date_data as $date_id=>$row)
    					{
    						$total_ex_fact_qty=$subcon_exfactory_arr_total[$po_id][$item_id][$delivery_company_id][$delivery_loc_id]["total_ex_fac_sub"];

    						$master_data[$row["buyer_name2"]]['org_po_qnty'] +=$row["po_quantity"];
    						$master_data[$row["buyer_name2"]]['po_qnty'] +=$row["po_quantity"];
    						$master_data[$row["buyer_name2"]]['po_value'] +=$row["po_quantity"]*$row["unit_price"];;
    						$total_po_qty+=$row["po_quantity"];
    						$total_po_valu+=$row["po_quantity"]*$row["unit_price"];

    						$master_data[$row["buyer_name2"]]['basic_qnty'] +=$basic_qnty;
    						$master_data[$row["buyer_name2"]]['ex_factory_qnty'] +=$row["prod_qty"];
    						$master_data[$row["buyer_name2"]]['ex_factory_value'] +=($row["prod_qty"])*$row["unit_price"];
    						$master_data[$row["buyer_name2"]]['total_ex_fact_qty'] +=$total_ex_fact_qty;
    						$master_data[$row["buyer_name2"]]['total_ex_fact_value'] +=$total_ex_fact_qty*$row["unit_price"];
    						$master_data[$row["buyer_name2"]]['sales_min'] += $row["smv"]*$total_ex_fact_qty;
    						$master_data[$row["buyer_name2"]]['b_id'] =$row["buyer_name2"];
    						if($row["prod_qty"]>0)
    						{
    							$master_data[$row["buyer_name2"]]['in_sub']=" (In Sub)";
    						}
    					}
    				}
    			}
    		}
    	}

		//print_r($master_data);

		$inspection_date_arr=return_library_array( "select po_break_down_id, max(inspection_date) as inspection_date from pro_buyer_inspection where status_active=1 and is_deleted=0 group by po_break_down_id", "po_break_down_id", "inspection_date");
		$invoice_array=return_library_array( "select id,invoice_no from com_export_invoice_ship_mst", "id", "invoice_no"  );
		$shipping_mode_array=return_library_array( "select id,shipping_mode from com_export_invoice_ship_mst", "id", "shipping_mode"  );
		$lc_sc_type_arr=return_library_array( "select id,is_lc from com_export_invoice_ship_mst", "id", "is_lc"  );
		$lc_sc_id_array=return_library_array( "select id,lc_sc_id from com_export_invoice_ship_mst", "id", "lc_sc_id"  );
		$lc_num_arr=return_library_array( "select id,export_lc_no from com_export_lc", "id", "export_lc_no"  );
		$sc_num_arr=return_library_array( "select id,contract_no from com_sales_contract", "id", "contract_no"  );

		$forwarder_arr=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name");
		$days_to_realized_arr = return_library_array("select id, delivery_buffer_days from lib_buyer", 'id', 'delivery_buffer_days');


		if($source_cond)
		$source_cond2=str_replace("d.","a.",$source_cond);
		$challan_mst_arr=array();
		$challan_sql="SELECT a.id,b.delivery_mst_id, a.sys_number_prefix_num,a.sys_number, a.forwarder, a.truck_no, a.driver_name, a.mobile_no, a.dl_no, b.po_break_down_id,b.item_number_id,a.delivery_floor_id,
		(CASE WHEN a.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty,
		b.total_carton_qnty as carton_qnty
		from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b
		where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $source_cond2";
		$challan_sql_result=sql_select($challan_sql);
		foreach($challan_sql_result as $row)
		{
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['challan']=$row[csf("sys_number")];
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['forwarder']=$row[csf("forwarder")];
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['truck_no']=$row[csf("truck_no")];
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['driver_name']=$row[csf("driver_name")];
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['mobile_no']=$row[csf("mobile_no")];
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['dl_no']=$row[csf("dl_no")];
		  	$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['floor']=$row[csf("delivery_floor_id")];

			$exfact_qty_arr[$row[csf("po_break_down_id")]][$row[csf("delivery_mst_id")]]+=$row[csf("ex_factory_qnty")]-$row[csf("ex_factory_return_qnty")];
			$exfact_cartoon_arr[$row[csf("po_break_down_id")]][$row[csf("delivery_mst_id")]]+=$row[csf("carton_qnty")];
			$exfact_return_qty_arr[$row[csf("po_break_down_id")]][$row[csf("delivery_mst_id")]]+=$row[csf("ex_factory_return_qnty")];
			$exfact_qty_arr_without_current[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]]["ex_fact"]+=$row[csf("ex_factory_qnty")]-$row[csf("ex_factory_return_qnty")];
			$exfact_qty_arr_without_current[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]]["carton"]+=$row[csf("carton_qnty")];
		}
		$details_report .='<table width="3975" cellspacing="0" cellpadding="0"  border="1"  class="rpt_table" rules="all" id="table_body2">';
		$i=1;

		if($db_type==0)
		{
			$sql= "SELECT b.id as po_id, max(a.lc_sc_no) as lc_sc_arr_no,
			group_concat(distinct a.invoice_no) as invoice_no,
			group_concat(distinct a.item_number_id) as itm_num_id,
			sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
			sum(a.total_carton_qnty) as carton_qnty, max(a.ex_factory_date)  as ex_factory_date,
			group_concat(distinct  a.lc_sc_no) as lc_sc_no,
			max(a.shiping_mode) as shiping_mode,
			a.delivery_mst_id as challan_id,
			d.delivery_floor_id as del_floor,  b.shipment_date, b.po_number,b.po_quantity as po_quantity, (b.unit_price/c.total_set_qnty) as unit_price, b.shiping_status, c.id, c.company_name, c.buyer_name, c.job_no_prefix_num,c.job_no,c.ship_mode , YEAR(c.insert_date) as year, c.style_ref_no, c.style_description, c.set_smv, d.delivery_company_id as del_company,d.source,d.delivery_location_id as del_location ,c.total_set_qnty,a.item_number_id,c.set_break_down,c.order_uom
			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c ,pro_ex_factory_delivery_mst d
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no $company_cond $str_cond $del_comp_cond $del_location_cond $del_floor_cond $shiping_status_cond $source_cond $buyer_conds $internal_ref_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.delivery_mst_id=d.id
			group by
					b.id , a.delivery_mst_id,d.delivery_floor_id,b.shipment_date, b.po_number,b.po_quantity,c.total_set_qnty,b.unit_price,b.shiping_status, c.id, c.company_name, c.buyer_name, c.job_no_prefix_num,c.job_no, c.ship_mode ,c.insert_date, c.style_ref_no, c.style_description, c.set_smv, d.delivery_company_id, d.delivery_location_id ,d.source,a.item_number_id ,c.set_break_down,c.order_uom
			order by c.buyer_name, b.shipment_date ASC";
		}
		else if($db_type==2)
		{
		 	$sql= "SELECT b.id as po_id,max(a.lc_sc_no) as lc_sc_arr_no,
			LISTAGG(CAST( a.invoice_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.invoice_no) as invoice_no,
			LISTAGG(CAST( a.item_number_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.item_number_id) as itm_num_id,
			sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
			sum(a.total_carton_qnty) as carton_qnty, max(a.ex_factory_date) as ex_factory_date,
			LISTAGG(CAST( a.lc_sc_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.lc_sc_no) as lc_sc_no, max(a.shiping_mode) as shiping_mode,
			a.delivery_mst_id as challan_id,d.delivery_floor_id as del_floor,b.shipment_date, b.po_number,b.po_quantity as po_quantity,(b.unit_price/c.total_set_qnty) as unit_price,b.shiping_status, c.id, c.company_name, c.buyer_name, c.job_no_prefix_num,c.job_no,c.ship_mode ,to_char(c.insert_date,'YYYY') as year, c.style_ref_no, c.style_description, c.set_smv, d.delivery_company_id as del_company,d.source, d.delivery_location_id as del_location,c.total_set_qnty,a.item_number_id,c.set_break_down,c.order_uom
			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c ,pro_ex_factory_delivery_mst d
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no $company_cond $str_cond $del_comp_cond $del_location_cond $del_floor_cond $shiping_status_cond $source_cond $buyer_conds $internal_ref_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.delivery_mst_id=d.id
			group by
					b.id , a.delivery_mst_id,d.delivery_floor_id,b.shipment_date, b.po_number,b.po_quantity,c.total_set_qnty,b.unit_price,b.shiping_status, c.id, c.company_name, c.buyer_name, c.job_no_prefix_num,c.job_no,c.ship_mode , c.insert_date, c.style_ref_no, c.style_description, c.set_smv, d.delivery_company_id, d.source,d.delivery_location_id ,a.item_number_id ,c.set_break_down,c.order_uom
			order by c.buyer_name, b.shipment_date ASC";
		}
		$sql_result=sql_select($sql);
		foreach($sql_result as $k=>$v)
		{
			$all_job_arr[trim($v[csf("job_no")])]=trim($v[csf("job_no")]);

		}
		$all_job="'".implode("','", array_unique($all_job_arr))."'";
		$order_item_qnty_sql="SELECT po_break_down_id,item_number_id,sum(order_quantity) as order_quantity from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 and job_no_mst in($all_job) group by po_break_down_id,item_number_id";
		foreach(sql_select($order_item_qnty_sql) as $key=>$val)
		{
			$order_item_qnty_arr[$val[csf("po_break_down_id")]][$val[csf("item_number_id")]]=$val[csf("order_quantity")];
		}
		$gr_po_qnty_pcs=0; $gr_po_qnty_val=0; $gr_po_qnty_val_perc=0; $gr_ttl_ex_qnty=0; $gr_ttl_ex_qnty_val=0; $gr_sales_min=0; $gr_ttl_carton=0; $gr_ttl_basic_qty=0; $gr_ttl_ex_fac_per=0; $gr_ttl_short_qty=0; $gr_ttl_short_val=0; $gr_ttl_sales_cm=0;

		//$po_exist_arr=array();
 		foreach($sql_result as $row)
		{
			$costing_per=$costing_per_arr[$row[csf('job_no')]];
			if($costing_per==1) $dzn_qnty=12;
			else if($costing_per==3) $dzn_qnty=12*2;
			else if($costing_per==4) $dzn_qnty=12*3;
			else if($costing_per==5) $dzn_qnty=12*4;
			else $dzn_qnty=1;
			$dzn_qnty2=$dzn_qnty ;
			$dzn_qnty=$dzn_qnty*$row[csf('total_set_qnty')];

			$cm_per_pcs=$tot_cost_arr[$row[csf('job_no')]]/$dzn_qnty2;

			$ex_fact_date_range=$txt_date_from."*".$txt_date_to."_". 1;
			$total_exface_qnty=$txt_date_from."*".$txt_date_to."_". 2;
			if($break_id==0) $break_id=$row[csf('po_id')]; else $break_id=$break_id.",".$row[csf('po_id')];
			if($sc_lc_id==0) $sc_lc_id=$row[csf('lc_sc_no')]; else $sc_lc_id=$sc_lc_id.",".$row[csf('lc_sc_no')];

			$invoce_id_arr=array_unique(explode(",",$row[csf('invoice_no')]));
		   $challan_id=array_unique(explode(",",$row[csf("challan_id")]));
 			$floor_id=array_unique(explode(",",$row[csf("del_floor")]));
			$challan_no=""; $forwarder=""; $vehi_no=""; $dirver_info="";  $floor_no="";

			//$diff=($row[csf('shiping_status')]!=3)?datediff("d",$current_date, $row[csf("shipment_date")])-1:datediff("d",$row[csf("ex_factory_date")], $row[csf("shipment_date")])-1;
			$todate=date("d-M")."-".substr(date("Y"), 2) ;
 			$todate=explode("-", $todate);
 			$todate=$todate[0]."-".strtoupper($todate[1])."-".$todate[2];
 			$diff=datediff("d",$todate, $row[csf("shipment_date")])-2;
		    $diff_color=($diff>0) ?" color:black;":"color:red;";

			$driver_name=$driver_mobile_no=$driver_dl_no="";
			foreach($challan_id as $val)
			{
				if($challan_no=="") $challan_no=$challan_mst_arr[$val][$row[csf('po_id')]]['challan']; else $challan_no.=','.$challan_mst_arr[$val][$row[csf('po_id')]]['challan'];
				if($floor_no=="") $floor_no=$floor_library[$challan_mst_arr[$val][$row[csf('po_id')]]['floor']]; else $floor_no.=','.$floor_library[$challan_mst_arr[$val][$row[csf('po_id')]]['floor']];

				if($forwarder=="") $forwarder=$forwarder_arr[$challan_mst_arr[$val][$row[csf('po_id')]]['forwarder']]; else $forwarder.=','.$forwarder_arr[$challan_mst_arr[$val][$row[csf('po_id')]]['forwarder']];
				if($vehi_no=="") $vehi_no=$challan_mst_arr[$val][$row[csf('po_id')]]['truck_no']; else $vehi_no.=','.$challan_mst_arr[$val][$row[csf('po_id')]]['truck_no'];
				//if($dirver_info=="") $dirver_info="Name: ".$challan_mst_arr[$val][$row[csf('po_id')]]['driver_name']."<br>Mob No: ".$challan_mst_arr[$val][$row[csf('po_id')]]['mobile_no']."<br>DL No: ".$challan_mst_arr[$val][$row[csf('po_id')]]['dl_no']; else $dirver_info.=','."Name: ".$challan_mst_arr[$val][$row[csf('po_id')]]['driver_name']."<br>Mob No: ".$challan_mst_arr[$val][$row[csf('po_id')]]['mobile_no']."<br>DL No: ".$challan_mst_arr[$val][$row[csf('po_id')]]['dl_no'];

				if($driver_name=="") $driver_name=$challan_mst_arr[$val][$row[csf('po_id')]]['driver_name']; else $driver_name.=','.$challan_mst_arr[$val][$row[csf('po_id')]]['driver_name'];
				if($driver_mobile_no=="") $driver_mobile_no=$challan_mst_arr[$val][$row[csf('po_id')]]['mobile_no']; else $driver_mobile_no.=','.$challan_mst_arr[$val][$row[csf('po_id')]]['mobile_no'];
				if($driver_dl_no=="") $driver_dl_no=$challan_mst_arr[$val][$row[csf('po_id')]]['dl_no']; else $driver_dl_no.=','.$challan_mst_arr[$val][$row[csf('po_id')]]['dl_no'];
			}
		   $set_break_down=explode("__", $row[csf("set_break_down")]);
			foreach($set_break_down as $k=>$v)
			{
				if($v)
				{
					$val=explode("_", $v);
					if( trim($val[0])== $row[csf("item_number_id")] )
					{
						$item_smv=$val[2];
					}
				}
			}

			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$comapny_id=$row[csf("company_name")];
			$delv_comp=($row[csf("source")]==1)?  $company_library[$row[csf("del_company")]] : $supp_library[$row[csf("del_company")]];
			$days_to_realized = add_date($row[csf('ex_factory_date')], $days_to_realized_arr[$row['BUYER_NAME']]);

			$onclick=" change_color('tr_".$i."','".$bgcolor."')";
			$details_report .='<tr bgcolor="'.$bgcolor.'" onclick="'.$onclick.'" id="tr_'.$i.'">';
			$details_report .='<td width="40" align="center">'.$i.'</td>
								<td width="110" align="center" style="word-break:break-all" >'.$company_library[$row[csf("company_name")]].'&nbsp;</td>
								<td width="60" align="center" style="word-break:break-all" >'.$row[csf("job_no_prefix_num")].'&nbsp;</td>
								<td width="60" align="center" style="word-break:break-all" >'.$row[csf("year")].'</td>
								<td width="100" align="center" style="word-break:break-all" >'.$buyer_arr[$row[csf("buyer_name")]].'&nbsp;</td>
								<td width="110" align="center" style="word-break:break-all" >'.$row[csf("po_number")].'&nbsp;</td>
								<td width="125" align="center" style="word-break:break-all" >'.$delv_comp.'&nbsp;</td>
								<td width="125" align="center"  style="word-break:break-all" >'.$location_library[$row[csf("del_location")]].'&nbsp;</td>
								<td width="125" align="center"  style="word-break:break-all" >'.$floor_no.'&nbsp;</td>
								<td width="120" align="center" style="word-break:break-all" >'.$challan_no.'&nbsp;</td>
								<td width="100" align="center" style="word-break:break-all" >';
								$inv_id=""; $lc_sc_no=""; $ship_mode="";
								foreach($invoce_id_arr as $invoice_id)
								{
									if($inv_id=="") $inv_id=$invoice_array[$invoice_id]; else $inv_id=$inv_id.",".$invoice_array[$invoice_id];
									if($ship_mode=="") $ship_mode=$shipment_mode[$shipping_mode_array[$invoice_id]]; else $ship_mode=$ship_mode.",".$shipment_mode[$shipping_mode_array[$invoice_id]];
									if($lc_sc_type_arr[$invoice_id]==1)
									{
										if($lc_sc_no=="") $lc_sc_no=$lc_num_arr[$lc_sc_id_array[$invoice_id]]; else $lc_sc_no=$lc_sc_no.",".$lc_num_arr[$lc_sc_id_array[$invoice_id]];
									}
									else
									{
										if($lc_sc_no=="") $lc_sc_no=$sc_num_arr[$lc_sc_id_array[$invoice_id]]; else $lc_sc_no=$lc_sc_no.",".$sc_num_arr[$lc_sc_id_array[$invoice_id]];
									}
								}

			$details_report .=$inv_id.'&nbsp;</td>
								<td width="100" align="center" style="word-break:break-all" >'.$lc_sc_no.'&nbsp;</td>
								<td width="100" style="word-break:break-all" >'.$row[csf("style_ref_no")].'&nbsp;</td>
								<td width="100" style="word-break:break-all" >'.$row[csf("style_description")].'&nbsp;</td>
								<td width="110" align="center" style="word-break:break-all" >';
								$item_name_arr=explode(",",$row[csf("itm_num_id")]);
								$item_name_arr=array_unique($item_name_arr);
								if(!empty($item_name_arr))
								{
									$p=1;
									foreach($item_name_arr as $item_id)
									{
										if($p!=1) $item_name_all .=",";
										$item_name_all .=$garments_item[$item_id];
										$p++;
									}
								}

								$total_ex_fact_qty=$exfact_qty_arr_without_current[$row[csf("po_id")]][$row[csf("item_number_id")]]["ex_fact"] ;

								$total_cartoon_qty=$exfact_qty_arr_without_current[$row[csf("po_id")]][$row[csf("item_number_id")]]["carton"] ;
								//$po_quantity=$row[csf("po_quantity")];
								$po_quantity=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]];
								$unit_price=$row[csf("unit_price")];
								$current_ex_Fact_Qty=$row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]][$row[csf("challan_id")]];
								$basic_qnty=($total_ex_fact_qty*$item_smv)/$basic_smv_arr[$row[csf("company_name")]];
								$cm_per_pcs_tot+=($cm_per_pcs*$current_ex_Fact_Qty);
								$short_excess=$total_ex_fact_qty-$po_quantity;

								$excess_msg=($short_excess>0) ?" color:green;":"color:black;";
								$excess_val_msg=(($total_ex_fact_qty*$unit_price)-$po_quantity>0) ?" color:green;":"color:black;";
								$ttl_ex_qty=($total_ex_fact_qty/$po_quantity)*100;
								$ttl_ex_qty_msg=($ttl_ex_qty>100) ?" color:green;":"color:black;";


			$details_report .=$item_name_all.'&nbsp;</td>
								<td width="80" align="center" style="word-break:break-all" >'.$item_smv.'</td>
								<td width="70" align="center" style="word-break:break-all" >'.change_date_format($row[csf("shipment_date")]).'</td>
								<td width="70" align="center"><a href="##" onclick="openmypage_ex_date('.$comapny_id.",'".$row[csf('po_id')]."','".$row[csf('item_number_id')]."','".$ex_fact_date_range."','ex_date_popup','".$row[csf('challan_id')]."'".",'1'".')">'.change_date_format($row[csf('ex_factory_date')]).'</a></td>
								<td width="100" align="center" style="word-break:break-all" >'.$shipment_mode[$row[csf('ship_mode')]].'</td>
								<td width="70" align="center" style="word-break:break-all" >'.$shipment_mode[$row[csf("shiping_mode")]].'&nbsp;</td>

								<td width="60" align="center" style="'.$diff_color.'" style="word-break:break-all" >('.$diff.')</td>
								<td width="100" align="center" style="word-break:break-all" >'.$unit_of_measurement[$row[csf('order_uom')]].'</td>
								<td width="80" align="right" style="word-break:break-all" >'. number_format($po_quantity,0,'', '').'</td>
								<td width="70" align="right" style="word-break:break-all" >'. number_format($unit_price,4).'</td>
								<td width="100" align="right" style="word-break:break-all" >'. number_format(($po_quantity*$unit_price),2).'</td>
								<td width="80" align="right" style="word-break:break-all" ><a href="##" onclick="openmypage_ex_date('.$comapny_id.",'".$row[csf('po_id')]."','".$row[csf('item_number_id')]."','".$ex_fact_date_range."','ex_date_popup','".$row[csf('challan_id')]."'".",'1'".')">'. number_format($current_ex_Fact_Qty,0,'.', '').'</a></td>
								<td width="100" align="right" style="word-break:break-all" >'. number_format( $current_ex_fact_value=$current_ex_Fact_Qty*$unit_price,2).'</td>
								<td width="80" align="right" style="word-break:break-all" >'. number_format($row[csf("carton_qnty")],0,'.', '').'</td>
								<td width="80" align="right" style="word-break:break-all" ><a href="##" onclick="openmypage_ex_date('.$comapny_id.",'".$row[csf('po_id')]."','".$row[csf('item_number_id')]."','".$total_exface_qnty."','ex_date_popup','".$row[csf('challan_id')]."'".",'1'".')">'.number_format($total_ex_fact_qty,0,'.', '').'</a></td>
								<td width="100" align="right" style="word-break:break-all" >'. number_format($total_ex_fact_value=$total_ex_fact_qty*$unit_price,2).'</td>
								<td width="80" align="right" style="word-break:break-all" >'.number_format($total_cartoon_qty,0,'.', '').'</td>
								<td width="100" align="right" title="Total Ex.Qty*SMV" style="word-break:break-all" >'. number_format($total_sales_minutes=$total_ex_fact_qty*$item_smv).'</td>
								<td width="80" align="right" style="word-break:break-all" >'.number_format($basic_qnty,0,'','').'</td>
								<td width="80" align="right" style="'.$excess_msg.'"  style="word-break:break-all" >'. number_format($excess_shortage_qty=$total_ex_fact_qty-$po_quantity,0,'', '').'</td>
								<td width="100" align="right" style="'.$excess_val_msg.'" style="word-break:break-all" >'. number_format($excess_shortage_value=($excess_shortage_qty*$unit_price),2).'</td>
								<td align="center" style="'.$ttl_ex_qty_msg.'" width="80" style="word-break:break-all" >'. number_format($total_ex_fact_qty_parcent=($total_ex_fact_qty/$po_quantity)*100,0).'</td>
								<td width="60" align="center" title="CM per pcs: '.number_format($cm_per_pcs,4).'" style="word-break:break-all" >'.number_format($cm_per_pcs*$total_ex_fact_qty,2).'</td>
								<td width="100" align="center" style="word-break:break-all" >'.$forwarder.'&nbsp;</td>
								<td width="80" align="center" style="word-break:break-all" >'.$vehi_no.'&nbsp;</td>
								<td width="100" style="word-break:break-all" >'.$driver_name.'&nbsp;</td>
								<td width="70" style="word-break:break-all" >'.$driver_mobile_no.'&nbsp;</td>
								<td width="100" style="word-break:break-all" >'.$driver_dl_no.'&nbsp;</td>
								<td width="70" align="center" style="word-break:break-all" >'.(change_date_format($inspection_date_arr[$row[csf('po_id')]]) == '0000-00-00' || change_date_format($inspection_date_arr[$row[csf('po_id')]]) == '' ? '' : change_date_format(change_date_format($inspection_date_arr[$row[csf('po_id')]]))).'</td>

								 <td width="70" >'.change_date_format($days_to_realized).'</td>
								<td align="center" style="word-break:break-all" >'.$shipment_status[$row[csf('shiping_status')]].'</td>
							</tr>';



			//$master_data[$row[csf("buyer_name")]]['po_qnty'] +=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]];
 	  		//$master_data[$row[csf("buyer_name")]]['po_value'] +=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]]*$row[csf("unit_price")];
 	  		$total_po_qty+=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]];
 	  		$total_po_valu+=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]]*$row[csf("unit_price")];


			$master_data[$row[csf("buyer_name")]]['ex_factory_qnty'] +=$row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]][$row[csf("challan_id")]];
			$master_data[$row[csf("buyer_name")]]['ex_factory_value'] +=($row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]][$row[csf("challan_id")]])*$row[csf("unit_price")];



			$total_basic_qty+=$basic_qnty;

			$total_ex_qty+=$row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]][$row[csf("challan_id")]];
			$total_crtn_qty+=$row[csf("carton_qnty")];
			$total_ex_valu+=($row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]][$row[csf("challan_id")]])*$row[csf("unit_price")];
			$g_total_ex_qty+=$total_ex_fact_qty;
			$g_total_ex_crtn+=$total_cartoon_qty;
			$g_total_ex_val+=$total_ex_fact_qty*$row[csf("unit_price")];
			$g_sales_minutes+=$current_ex_Fact_Qty*$item_smv;
			$total_eecess_storage_qty+=$excess_shortage_qty;
			$total_eecess_storage_val+=$excess_shortage_value;
			if($po_check_arr[$row[csf("po_id")]][$row[csf("item_number_id")]]=="")
			{
				$po_check_arr[$row[csf("po_id")]][$row[csf("item_number_id")]]=$row[csf("po_id")];
				$master_data[$row[csf("buyer_name")]]['b_id']=$row[csf("buyer_name")];
				$master_data[$row[csf("buyer_name")]]['org_po_qnty'] +=$row[csf("po_quantity")];
				$master_data[$row[csf("buyer_name")]]['po_qnty'] +=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]];
				$master_data[$row[csf("buyer_name")]]['po_value'] +=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]]*$row[csf("unit_price")];
				$master_data[$row[csf("buyer_name")]]['basic_qnty'] +=$basic_qnty;
				$master_data[$row[csf("buyer_name")]]['total_ex_fact_qty'] +=$total_ex_fact_qty;
				$master_data[$row[csf("buyer_name")]]['total_ex_fact_value'] +=$total_ex_fact_qty*$row[csf("unit_price")];
				$master_data[$row[csf("buyer_name")]]['sales_min'] += $item_smv*$total_ex_fact_qty;

 	  			$gr_po_qnty_pcs+=$po_quantity;
	  			$gr_po_qnty_val+=$po_quantity*$unit_price;
 	  			$gr_ttl_ex_qnty+=$total_ex_fact_qty;
	  			$gr_ttl_ex_qnty_val+=$total_ex_fact_value;
	  			$gr_ttl_carton_qt+=$total_cartoon_qty;
	  			$gr_sales_min+=$total_sales_minutes;
	  			$gr_ttl_basic_qty+=$basic_qnty;
	  			$gr_ttl_ex_fac_per+=$total_ex_fact_qty_parcent;
	  			$gr_ttl_short_qty+=$excess_shortage_qty;
	  			$gr_ttl_short_val+=$excess_shortage_value;
	  			$gr_ttl_sales_cm+=$cm_per_pcs*$total_ex_fact_qty;
			}

			$i++;$item_name_all="";
		}
		$pp=$i;

		/*echo "SELECT b.export_lc_no as export_lc_no from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.wo_po_break_down_id in($break_id) and b.id in($sc_lc_id)"."<br>";
		echo "SELECT b.contract_no as export_lc_no from com_export_lc_order_info a, com_sales_contract b where a.com_export_lc_id=b.id and a.wo_po_break_down_id in($break_id) and b.id in($sc_lc_id)"."<br>";*/
		//print_r($master_data);

		//$details_report .='</table>';

		foreach($master_data as $rows)
		{
			$total_po_val+=$rows["po_value"];
		}

		?>
        <div style="width:3100x;">
            <div style="width:1420px" >
                <table width="1390"  cellspacing="0"  align="center">
                    <tr>
                        <td align="center" colspan="13" class="form_caption">
                            <strong style="font-size:16px;">Company Name:<? echo  $company_library[$cbo_company_name] ;?></strong>
                        </td>
                    </tr>
                    <tr class="form_caption">
                        <td colspan="13" align="center" class="form_caption"> <strong style="font-size:15px;">Ex-Factory Report</strong></td>
                    </tr>
                     <tr align="center">
                        <td colspan="13" align="center" class="form_caption"> <strong style="font-size:15px;">Total Summary</strong></td>
                    </tr>
                    </table>
                    <table width="1390" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1">
                    <thead>
                        <th width="40" height="34">SL</th>
                        <th width="130">Buyer Name</th>
                        <th width="100">PO Qty</th>
                        <th width="100">PO Qty (pcs)</th>
                        <th width="130">PO Value</th>
                        <th width="100">PO Value(%)</th>
                        <th width="100">Current Ex-Fact. Qty.</th>
                        <th width="130">Current Ex-Fact. Value</th>
                        <th width="100">Total Ex-Fact. Qty.</th>
                        <th width="130">Total Ex-Fact. Value </th>
                        <th width="100">Sales Minutes</th>
                        <th width="100">Total Ex-Fact. (Basic Qty)</th>
                        <th >Total Ex-Fact. Value %</th>
                    </thead>
                 </table>
                 <table width="1390" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body">
                 <?
                 $m=1;
                 $grand_sales_minute =0;
                 foreach($master_data as $rows)
                {
                    if ($i%2==0)
                    $bgcolor="#E9F3FF";
                    else
                    $bgcolor="#FFFFFF";
                     ?>
                  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" >
                        <td width="40" align="center"><? echo $m; ?></td>
                        <td width="130">
                        <p><?
                        echo $buyer_arr[$rows[b_id]]. $master_data[$rows[b_id]]['in_sub'];
                        ?></p>
                        </td>
                        <td width="100" align="right"><p><?  $po_quantity_org=$rows["org_po_qnty"];echo number_format($po_quantity_org,0); $total_buyer_org_po_quantity+=$po_quantity_org; ?></p></td>
                        <td width="100" align="right"><p><?  $po_quantity=$rows[po_qnty];echo number_format($po_quantity,0); $total_buyer_po_quantity+=$po_quantity; ?></p></td>
                        <td width="130" align="right" ><p  id="value_<? echo $i ; ?>"><? $buyer_po_value=$rows["po_value"]; echo number_format($buyer_po_value,2 ,'.', '');  $total_buyer_po_value+=$buyer_po_value; ?></p></td>
                        <td width="100" align="right">
                         <? echo number_format(($buyer_po_value/$total_po_val)*100,2,'.','');$parcentages+=($buyer_po_value/$total_po_val)*100; ?>
                        </td>
                        <td width="100" align="right">
                        <p><?
                         $current_ex_Fact_Qty=$rows[ex_factory_qnty];  echo number_format($current_ex_Fact_Qty,0,'',''); $total_current_ex_Fact_Qty +=$current_ex_Fact_Qty;
                        ?></p>
                        </td>
                        <td width="130" align="right">
                        <p><?
                        $current_ex_fact_value=$rows[ex_factory_value]; echo number_format($current_ex_fact_value,2,'.',''); $total_current_ex_fact_value+=$current_ex_fact_value;
                        ?></p>
                        </td>
                        <td align="right" width="100">
                        <p><?
                         $total_ex_fact_qty=$rows[total_ex_fact_qty]; echo number_format($total_ex_fact_qty,0,'',''); $mt_total_ex_fact_qty+=$total_ex_fact_qty;
                        ?></p>
                        </td>
                        <td align="right" width="130">
                        <p><?
                         $total_ex_fact_value=$rows[total_ex_fact_value];  echo  number_format($total_ex_fact_value,2,'.',''); $mt_total_ex_fact_value+=$total_ex_fact_value;
                        ?></p>
                        </td>
                        <td width="100" align="right"><p>
                        	<?
                        	echo $g_sales_min+= number_format($rows["sales_min"],0,'','');

                        	?>
                        </p></td>
                        <td width="100" align="right">
                        <p><?
                         $buyer_basic_qnty=$rows["basic_qnty"];  echo number_format($buyer_basic_qnty,0,'',''); $total_buyer_basic_qnty +=$buyer_basic_qnty;
                        ?></p>
                        </td>

                        <td align="right">
                        <p><?
                        $total_ex_fact_value_parcentage=($total_ex_fact_value/$buyer_po_value)*100;
                        echo number_format($total_ex_fact_value_parcentage,0)
                        ?> %</p>
                        </td>
                    </tr>
                    <?
                    $i++;$m++;
                    $buyer_po_quantity=0;
                    $buyer_po_value=0;
                    $current_ex_Fact_Qty=0;
                    $current_ex_fact_value=0;
                    $total_ex_fact_qty=0;
                    $total_ex_fact_value=0;
                    $g_sales_min=0;
                    $grand_sales_minute +=number_format($rows["sales_min"],0,'','');

                }
                    ?>
                    <input type="hidden" name="total_i" id="total_i" value="<? echo $i; ?>" />
                    <tfoot>
                        <th align="right" colspan="2"><b>Total:</b></th>
                        <th  align="right" id="total_buyer_org_po_quantity"><? echo number_format($total_buyer_org_po_quantity,0);  ?></th>
                        <th  align="right" id="total_buyer_po_quantity"><? echo number_format($total_buyer_po_quantity,0);  ?></th>
                        <th  align="right" id="value_total_buyer_po_value"><? echo number_format($total_buyer_po_value,2 ,'.', ''); ?> </th>
                        <th align="right" id="parcentages"><? echo ceil($parcentages); ?></th>
                        <th  align="right" id="total_current_ex_Fact_Qty"><? echo number_format($total_current_ex_Fact_Qty,0); ?></th>
                        <th  align="right" id="value_total_current_ex_fact_value"><? echo  number_format($total_current_ex_fact_value,2); ?></th>
                        <th align="right" id="mt_total_ex_fact_qty"><? echo number_format($mt_total_ex_fact_qty,0); ?></th>
                        <th align="right" id="value_mt_total_ex_fact_value"><? echo number_format($mt_total_ex_fact_value,2); ?></th>
                        <th align="right"><? echo number_format($grand_sales_minute ,2); ?></th>
                        <th  align="right" id="total_buyer_basic_qnty"><? echo number_format($total_buyer_basic_qnty,0); ?></th>
                        <th align="right"></th>
                    </tfoot>
                </table>
            </div>
            <br />
            <div>
                <table width="3850"  >
                    <tr>
                    <td colspan="31" class="form_caption"><strong style="font-size:16px;">Shipped Out Order Details Report</strong></td>
                    </tr>
                </table>
                <table width="3975" border="1" class="rpt_table" rules="all" id="table_header_2">
                    <thead>
                        <th width="40">SL</th>
                        <th width="110">Company</th>
                        <th width="60">Job</th>
                        <th width="60">Year</th>
                        <th width="100">Buyer Name</th>
                        <th width="110">Order NO</th>
                        <th width="125">Del Company</th>
                        <th width="125">Del Location</th>
                        <th width="125">Del Floor</th>
                        <th width="120">Challan NO</th>
                        <th width="100" >Invoice NO</th>
                        <th width="100" >LC/SC NO</th>
                        <th width="100">Style Ref. no.</th>
                        <th width="100">Style Description</th>
                        <th width="110">Item Name</th>
                        <th width="80">Item SMV</th>
                        <th width="70">Shipment Date</th>
                        <th width="70">Ex-Fac. Date</th>
                        <th width="100"><p>Po Rcv.Ship Mode</p></th>
                        <th width="70">Shipping Mode</th>
                        <th width="60">Days in Hand</th>
                        <th width="100">UOM</th>
                        <th width="80">PO Qtny. (pcs)</th>
                        <th width="70">Unit Price</th>
                        <th width="100">PO Value</th>
                        <th width="80">Current Ex-Fact. Qty (pcs)</th>
                        <th width="100">Current Ex-Fact. Value</th>
                        <th width="80">Current Carton Qty</th>
                        <th width="80">Total Ex-Fact. Qty.</th>
                        <th width="100">Total Ex-Fact. Value</th>
                        <th width="80">Total Carton Qty</th>
                        <th width="100">Sales Minute</th>
                        <th width="80">Total Ex-Fact. (Basic Qty)</th>
                        <th width="80">Excess/ Shortage Qty</th>
                        <th width="100">Excess/ Shortage Value</th>
                        <th width="80">Total Ex-Fact. Qty. %</th>
                        <th width="60">Sales CM</th>
                        <th width="100">C & F Name</th>
                        <th width="80">Vehicle No</th>
                        <th width="100">Driver Name</th>
                        <th width="70">Driver Mobile No.</th>
                        <th width="100">Driver DL No.</th>
                        <th width="70">Inspaction Date</th>
						<th width="70">Days To Realized</th>
                        <th>Ex-Fact Status</th>
                    </thead>
                </table>
            <div style="width:3995px; overflow-y:scroll; overflow-x:hidden; max-height:300px;"  id="scroll_body" >
            <? echo $details_report;
            	foreach($subcon_exfactory_arr as $po_id=>$item_data)
            	{
            		foreach($item_data as $item_id=>$delivery_company_data)
            		{
            			foreach($delivery_company_data as $delivery_company_id=>$delivery_loc_data)
            			{
            				foreach($delivery_loc_data as $delivery_loc_id=>$delivery_date_data)
            				{
            					foreach($delivery_date_data as $date_id=>$row)
            					{
            						$po_quantity=$row["po_quantity"];
            						$unit_price=$row["unit_price"];
            						$total_ex_fact_qty=$subcon_exfactory_arr_total[$po_id][$item_id][$delivery_company_id][$delivery_loc_id]["total_ex_fac_sub"];
            						$all_date="";
            						$jj=$pp+1;
            						if ($jj%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            						$onclick=" change_color('tr2_".$jj."','".$bgcolor."')";

            						?>
	            						<tr onclick="<? echo $onclick;?>"  id="tr2_<? echo $jj;?>" >
	            							<td width="40" align="center"><? echo $pp++;?></td>
	            							<td width="110" align="center" ><p><? echo $company_library[$row["company_name"]]; ?> &nbsp;</p></td>
	            							<td width="60" align="center" ><p><? echo $row["job"]; ?> &nbsp;</p></td>
	            							<td width="60" align="center" ><p><?    $arr_year=explode('-',change_date_format($row["insert_date"]));echo $arr_year[2]; ?> </p></td>
	            							<td width="100" align="center" ><p><? echo $row["buyer_name"]; ?> &nbsp;</p></td>
	            							<td width="110" align="center"><p><? echo $row["po_number"]; ?> (In-Sub) &nbsp;</p></td>
	            							<td width="125" align="center" ><p><? echo $company_library[$delivery_company_id]; ?> &nbsp;</p></td>
	            							<td width="125" align="center" ><p><? echo $location_library[$delivery_loc_id]; ?> &nbsp;</p></td>
	            							<td width="125" align="center" ><p>&nbsp;  </p></td>
	            							<td width="120" align="center"><p><? echo $row["delivery_no"]; ?> &nbsp;</p></td>
	            							<td width="100" align="center"><p></p></td>
	            							<td width="100" align="center"><p><? //echo $lc_sc_no; ?> &nbsp;</p></td>
	            							<td width="100"><p><? echo $row["style_ref_no"]; ?> &nbsp;</p></td>
	            							<td width="100"><p><? //echo $row["style_description")]; ?> &nbsp;</p></td>
	            							<td width="110" align="center"><p><? echo $garments_item[$item_id];?></p></td>
	            							<td width="80" align="center"><p><? echo $item_smv=$row["smv"]; ?> </p></td>
	            							<td width="70" align="center"><p><? echo change_date_format($row["shipment_date"]); ?> </p></td>
	            							<td width="70" align="center"><a href="##" onclick="openmypage_ex_date(<? echo $row["company_name"].",'".$po_id."','".$item_id."','".$date_id."','ex_date_popup','".$row['delivery_no']."'".',2'; ?> )"><? echo change_date_format($date_id); ?> </a></td>
	            							<td width="100" align="center"><p><? echo $shipment_mode[$row['ship_mode']]; ?> </p></td>
	            							<td width="70" align="center"><p><? echo $shipment_mode[$row["shiping_mode"]]; ?> &nbsp;</p></td>
	            							<td width="60" align="center" style="<? echo $diff_color; ?> "><p>(<? echo $diff; ?> )</p></td>

	            							<td width="100" align="center"  ><p> <? echo $unit_of_measurement[$row["order_uom"]]; ?>  </p></td>

	            							<td width="80" align="right"><p><? echo  number_format($po_quantity,0,'', ''); ?> </p></td>
	            							<td width="70" align="right"><p><? echo  number_format($unit_price,4); ?> </p></td>
	            							<td width="100" align="right"><p><? echo  number_format(($po_quantity*$unit_price),2); ?> </p></td>
	            							<td width="80" align="right"><p><a href="##" onclick="openmypage_ex_date(<? echo $row["company_name"].",'".$po_id."','".$item_id."','".$date_id."','ex_date_popup','".$row['delivery_no']."'".',2'; ?> )"><?
	            							$current_ex_Fact_Qty=$row["prod_qty"]; echo  number_format($current_ex_Fact_Qty,0,'.', ''); ?> </a></p></td>
	            							<td width="100" align="right"><p><? echo  number_format( $current_ex_fact_value=$current_ex_Fact_Qty*$unit_price,2); ?> </p></td>
	            							<td width="80" align="right"><p><? echo  number_format($row["total_carton_qnty"],0,'', ''); ?> </p></td>
	            							<td width="80" align="right"><p><a href="##" onclick="openmypage_ex_date(<? echo $row["company_name"].",'".$po_id."','".$item_id."','".$all_date."','ex_date_popup','".$delivery_company_id.'_'.$delivery_loc_id."'".',2'; ?> )" ><? echo number_format($total_ex_fact_qty,0,' ', ''); ?> </a></p></td>
	            							<td width="100" align="right"><p><? echo  number_format($total_ex_fact_value=$total_ex_fact_qty*$unit_price,2); ?> </p></td>
	            							<td width="80" align="right"><p><? echo number_format($total_cartoon_qty,0,'.', ''); ?> </p></td>
	            							<td width="100" align="right" title="Total Ex.Qty*SMV"><p><? echo  number_format($total_sales_minutes=$total_ex_fact_qty*$item_smv); ?> </p></td>
	            							<td width="80" align="right"><p><? echo number_format($basic_qnty,0,'',''); ?> </p></td>
	            							<td width="80" align="right" style=" <? echo $excess_msg; ?> " ><p><? echo  number_format($excess_shortage_qty=$total_ex_fact_qty-$po_quantity,0,'', ''); ?> </p></td>
	            							<td width="100" align="right" style=" <? echo $excess_val_msg; ?> "><p><? echo  number_format($excess_shortage_value=($total_ex_fact_qty*$unit_price)-$po_quantity,2); ?> </p></td>
	            							<td align="center" style=" <? echo $ttl_ex_qty_msg; ?> " width="80"><p><? echo  number_format($total_ex_fact_qty_parcent=($total_ex_fact_qty/$po_quantity)*100,0); ?> </p></td>
	            							<td width="60" align="center" title="CM per pcs: <? echo number_format($cm_per_pcs,4); ?> "><p><? echo number_format($cm_per_pcs*$total_ex_fact_qty,2); ?> </p></td>
	            							<td width="100" align="center"><p><? echo $forwarder; ?> &nbsp;</p></td>
	            							<td width="80" align="center"><p><? echo $row["vehical_no"]; ?> &nbsp;</p></td>
	            							<td width="100"><p><? echo $row["driver"]; ?> &nbsp;</p></td>
                                            <td width="70"><p><? echo $row["mobile"]; ?> &nbsp;</p></td>
                                            <td width="100"><p><? echo $row["dl_no"]; ?> &nbsp;</p></td>
	            							<td width="70" align="center"><p><? echo (change_date_format($inspection_date_arr[$row['po_id']]) == '0000-00-00' || change_date_format($inspection_date_arr[$row['po_id']]) == '' ? '' : change_date_format(change_date_format($inspection_date_arr[$row['po_id']]))); ?> </p></td>
											<td width="70"></td>
	            							<td align="center"><p><? echo $shipment_status[$row['shiping_status']];?></p></td>
	            						</tr>
            						<?
            						if($po_check_arr2[$row['po_id']][$item_id]=="")
            						{
            							$po_check_arr2[$row['po_id']][$item_id]=$row['po_id'];
            							$gr_po_qnty_pcs+=$po_quantity;
            							$gr_po_qnty_val+=$po_quantity*$unit_price;
            							$gr_ttl_ex_qnty+=$total_ex_fact_qty;
            							$gr_ttl_ex_qnty_val+=$total_ex_fact_value;
            							$gr_ttl_carton_qt+=$total_cartoon_qty;
            							$gr_sales_min+=$total_sales_minutes;
            							$gr_ttl_basic_qty+=$basic_qnty;
            							$gr_ttl_ex_fac_per+=$total_ex_fact_qty_parcent;
            							$gr_ttl_short_qty+=$excess_shortage_qty;
            							$gr_ttl_short_val+=$excess_shortage_value;
            							$gr_ttl_sales_cm+=$cm_per_pcs*$total_ex_fact_qty;
            						}
            					}
            				}
            			}
            		}
            	}
            	$details_report .='</table>';
            ?>
            <table width="3975" cellspacing="0" cellpadding="0"  border="1"  class="rpt_table" rules="all" id="table_footer">
                <tfoot>
                    <tr>
                    	<th width="40">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="125">&nbsp;</th>
                        <th width="125">&nbsp;</th>
                        <th width="125">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100" >&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="100" align="right"><strong>Total</strong></th>
                        <th width="80" id="total_po_qtybk" align="right"><? echo  number_format($gr_po_qnty_pcs,0);?></th>
                        <th width="70" align="right">&nbsp;</th>
                        <th width="100" align="right" id="value_total_po_valubk"><? echo  number_format($gr_po_qnty_val,2); ?></th>
                        <th width="80" align="right" id="total_ex_qty"><? echo number_format($total_ex_qty,0); ?></th>
                        <th width="100" align="right" id="value_total_ex_valu"><? echo number_format($total_ex_valu,2);?></th>
                        <th width="80" align="right" id="total_crtn_qty"><? echo number_format($gr_ttl_carton,0); ?></th>
                        <th width="80" align="right" id="g_total_ex_qtybk"><? echo number_format($gr_ttl_ex_qnty,0);?></th>
                        <th width="100" align="right" id="value_g_total_ex_valbk"><? echo number_format($gr_ttl_ex_qnty_val,2);?></th>
                        <th width="80" align="right" id="g_total_ex_crtnbk"><? echo number_format($gr_ttl_carton_qt,0);?></th>
                        <th width="100" align="right" id="value_sales_minutesbk"><? echo number_format($gr_sales_min);?></th>

                        <th width="80" align="right" id="total_basic_qtybk"><? echo number_format($gr_ttl_basic_qty,0); ?></th>
                        <th width="80" align="right" id="total_eecess_storage_qtybk"><? echo number_format($gr_ttl_short_qty,0);?></th>
                        <th width="100" align="right" id="value_total_eecess_storage_valbk"><? echo number_format($gr_ttl_short_val,0);?></th>
                        <th width="80" id="total_ex_perbk"><? echo number_format($gr_ttl_ex_fac_per,0);?></th>
                        <th width="60" align="right" id="value_cm_per_pcs_totbk"><? echo number_format($gr_ttl_sales_cm,2);?></th>
                        <th width="100">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="70">&nbsp;</th>
						<th width="70">&nbsp;</th>
                        <th>&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
            </div>
            </div>
        </div>

		<?
	}
	else if($reportType==2)
	{

		/*$cbo_company_name=str_replace("'","",$cbo_company_name);
		$txt_date_from=str_replace("'","",$txt_date_from);
		$txt_date_to=str_replace("'","",$txt_date_to);*/

		$target_basic_qnty=array();
		$month_id_start = date('m',strtotime($txt_date_from));
		$month_id_end = date('m',strtotime($txt_date_to));
		$year_id_start = date('Y',strtotime($txt_date_from));
		$year_id_end = date('Y',strtotime($txt_date_to));
		$month_date_cond="";

		if($year_id_start==$year_id_end)
		{
			 $month_date_cond=" (a.year_id=$year_id_start AND d.month_id between $month_id_start and $month_id_end";
		}
		else
		{
			$year_deve=$year_id_end-$year_id_start;
			if($year_deve>0)
			{
				for($i=0;$i<=$year_deve;$i++)
				{
					$cross_year_month_start=$cross_year_month_end="";
					if($i>0) $month_id_start=1;
					for($k=$month_id_start;$k<=12;$k++)
					{
						if($cross_year_month_start=="") $cross_year_month_start=$month_id_start;
						if($i==$year_deve){ $cross_year_month_end=($month_id_end*1);} else{ if($month_id_start==12) $cross_year_month_end=$month_id_start;}
						$month_id_start=$month_id_start+1;
					}
					if($month_date_cond=="")$month_date_cond.=" ((a.year_id=$year_id_start AND d.month_id between $cross_year_month_start and $cross_year_month_end )"; else $month_date_cond.=" or(a.year_id=$year_id_start AND d.month_id between $cross_year_month_start and $cross_year_month_end )";
					$year_id_start=$year_id_start+1;

				}
			}
		}
		$month_date_cond.=")";
		//echo $month_date_cond;die;
		if($cbo_company_name>0)
		{
			 $company_cond="and a.company_id = '$cbo_company_name'";
			 $company_cond2="and c.company_name = '$cbo_company_name'";
		}
		else
		{
			 $company_cond="";
		}

		 $sql_con = "SELECT  b.buyer_id, d.month_id, a.year_id, SUM((d.capacity_month_pcs* b.allocation_percentage)/100) AS cap_qnty FROM lib_capacity_allocation_mst a,lib_capacity_allocation_dtls b, lib_capacity_calc_mst c,  lib_capacity_year_dtls d
		WHERE
		a.id=b.mst_id AND
		a.year_id=c.year AND
		a.month_id=d.month_id AND
		c.id=d.mst_id
		 $company_cond AND
		$month_date_cond  AND
		a.status_active=1 and
		a.is_deleted=0 and
		b.status_active=1 and
		b.is_deleted=0 and
		c.status_active=1 and
		c.is_deleted=0  $buyer_conds2
		GROUP BY b.buyer_id, d.month_id, a.year_id";

		//echo $sql_con;die;
		$buyer_wisi_data=array();
		$sql_data=sql_select($sql_con);
		foreach( $sql_data as $row)
		{

			$target_basic_qnty[$row[csf("buyer_id")]][$row[csf("year_id")].'-'.str_pad($row[csf("month_id")],2,"0",STR_PAD_LEFT)]+=$row[csf("cap_qnty")];
			if($row[csf("cap_qnty")]>0)
			{
			$buyer_tem_arr[$row[csf("buyer_id")]]=$row[csf("buyer_id")];
			$buyer_wisi_data[$row[csf("buyer_id")]]['lib_basic_qnty']+=$row[csf("cap_qnty")];
			}
		}
		//var_dump($target_basic_qnty);die;

		$tot_commision_rate_arr = return_library_array("select job_no, commission from wo_pre_cost_dtls","job_no","commission");
		$costing_per_arr = return_library_array("select job_no, costing_per from wo_pre_cost_mst","job_no","costing_per");
		$tot_cost_arr = return_library_array("select job_no, cm_for_sipment_sche from wo_pre_cost_dtls","job_no","cm_for_sipment_sche");
		$sql_res=sql_select("select b.po_break_down_id as po_id,
		sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as return_qnty
		from pro_ex_factory_mst b  where  b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id");
		$ex_factory_qty_arr=array();
		foreach($sql_res as $row)
		{
			$ex_factory_qty_arr[$row[csf('po_id')]]['return_qty']=$row[csf('return_qnty')];
		}

		/*$sql= "select b.id as po_id, (b.unit_price/c.total_set_qnty) as unit_price, c.total_set_qnty, c.id as job_id, c.job_no, c.buyer_name, c.company_name, c.set_smv, a.ex_factory_qnty as ex_factory_qnty,a.ex_factory_date
		from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c
		where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name like '$cbo_company_name' $str_cond and a.entry_form!=85  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1
		order by a.ex_factory_date ASC ";*/
		$sql= "SELECT b.id as po_id, (b.unit_price/c.total_set_qnty) as unit_price, c.total_set_qnty, c.id as job_id, c.job_no, c.buyer_name, c.company_name, c.set_smv, a.ex_factory_qnty as ex_factory_qnty,a.ex_factory_date
		from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c,pro_ex_factory_delivery_mst d
		where a.po_break_down_id=b.id and b.job_no_mst=c.job_no   $del_location_cond $del_floor_cond $del_comp_cond  $str_cond $company_cond2 $buyer_conds $internal_ref_cond and  a.entry_form!=85  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.delivery_mst_id= d.id
		order by a.ex_factory_date ASC ";//c.job_no

		//PRO_EX_FACTORY_DELIVERY_MST

		 //echo $sql;
		$sql_result=sql_select($sql);
		//print_r($sql_result);die;
		foreach($sql_result as $row)
		{
			$cm_val=0;
			//if(!in_array($row[csf('job_no')],$temp_arr)){

				$dzn_qnty=$cm_value=$cm_value_rate=0;
				if($costing_per_arr[$row[csf('job_no')]]==1) $dzn_qnty=12;
				else if($costing_per_arr[$row[csf('job_no')]]==3) $dzn_qnty=12*2;
				else if($costing_per_arr[$row[csf('job_no')]]==4) $dzn_qnty=12*3;
				else if($costing_per_arr[$row[csf('job_no')]]==5) $dzn_qnty=12*4;
				else $dzn_qnty=1;

				$dzn_qnty=$dzn_qnty*$row[csf('total_set_qnty')];
				$commision_per_pic=$tot_commision_rate_arr[$row[csf('job_no')]]/$dzn_qnty;
				$cm_value_rate=($tot_cost_arr[$row[csf('job_no')]]/$dzn_qnty);

				//$temp_arr[]=$row[csf('job_no')];

			//}

			$exfactreturn_qty=$ex_factory_qty_arr[$row[csf('po_id')]]['return_qty'];
			$basic_qnty=($row[csf("ex_factory_qnty")]*$row[csf("set_smv")])/$basic_smv_arr[$row[csf("company_name")]];
			$cm_val=$cm_value_rate*$row[csf("ex_factory_qnty")];
			$result_data_arr[date("Y-m",strtotime($row[csf("ex_factory_date")]))][$row[csf("buyer_name")]]['cm_value'] +=$cm_val;
			$result_data_arr[date("Y-m",strtotime($row[csf("ex_factory_date")]))][$row[csf("buyer_name")]]['ex_factory_qnty'] +=$row[csf("ex_factory_qnty")]-$exfactreturn_qty;
			$result_data_arr[date("Y-m",strtotime($row[csf("ex_factory_date")]))][$row[csf("buyer_name")]]['basic_qnty'] +=$basic_qnty;
			$result_data_arr[date("Y-m",strtotime($row[csf("ex_factory_date")]))][$row[csf("buyer_name")]]['lib_basic_qnty'] =$target_basic_qnty[$row[csf("buyer_name")]][date("Y-m",strtotime($row[csf("ex_factory_date")]))];

			$result_data_arr[date("Y-m",strtotime($row[csf("ex_factory_date")]))][$row[csf("buyer_name")]]['ex_factory_value'] +=($row[csf("ex_factory_qnty")]*$row[csf("unit_price")]);
			$buyer_tem_arr[$row[csf("buyer_name")]]=$row[csf("buyer_name")];

			$result_data_arr[date("Y-m",strtotime($row[csf("ex_factory_date")]))][$row[csf("buyer_name")]]['commision_cost'] +=($commision_per_pic*($row[csf("ex_factory_qnty")]-$exfactreturn_qty));
		}

		 //asort($result_data_arr);



		$total_month=count($result_data_arr);
		$width=($total_month*600)+100;
		$colspan=$total_month*6;
		$main_data="";$i=1;

		foreach($buyer_tem_arr as $buyer_id=>$val)
        {
			if ($i%2==0)
			$bgcolor="#E9F3FF";
			else
			$bgcolor="#FFFFFF";
			$main_data.='<tr bgcolor="'.$bgcolor.'" onclick="change_color(\'tr_'.$i.'\',\''.$bgcolor.'\')" id="tr_'.$i.'" >
			<td width="100">'.$buyer_arr[$buyer_id].'</td>';
			$tot_lib_basic_qnty=$tot_basic_qnty=$tot_ex_factory_qnty=$tot_ex_factory_value=$tot_cm_value=$tot_commision=0;
			foreach($result_data_arr as $month_id=>$result)
			{
				$ex_factory_qnty=$result_data_arr[$month_id][$buyer_id]['ex_factory_qnty'];
				$ex_factory_value=$result_data_arr[$month_id][$buyer_id]['ex_factory_value'];
				$cm_value=$result_data_arr[$month_id][$buyer_id]['cm_value'];
				if($result_data_arr[$month_id][$buyer_id]['lib_basic_qnty']>0)
				{
				$lib_basic_qnty=$result_data_arr[$month_id][$buyer_id]['lib_basic_qnty'];
				}
				else
				{
				$lib_basic_qnty=$target_basic_qnty[$buyer_id][$month_id];
				}
				$basic_qnty=$result_data_arr[$month_id][$buyer_id]['basic_qnty'];

				$commision_cost=$result_data_arr[$month_id][$buyer_id]['commision_cost'];
				$main_data.='<td width="100" align="right">'. number_format($lib_basic_qnty,0).' </td>
				<td width="100" align="right">'.  number_format($ex_factory_qnty,0).' </td>
				<td width="100" align="right">'.  number_format($ex_factory_value,2).' </td>
				<td width="100" align="right">'.  number_format($basic_qnty,0).' </td>
				<td width="100" align="right">'.  number_format($ex_factory_value-$commision_cost,2).' </td>
				<td width="100" align="right">'.  number_format($cm_value,2).' </td>';



				$total_mon_data[$month_id]['lib_basic_qnty'] += $lib_basic_qnty;
				$total_mon_data[$month_id]['basic_qnty'] += $basic_qnty;
				$total_mon_data[$month_id]['ex_factory_qnty'] += $ex_factory_qnty;
				$total_mon_data[$month_id]['ex_factory_value'] += $ex_factory_value;
				$total_mon_data[$month_id]['cm_val'] += $cm_value;
				$total_mon_data[$month_id]['commision_cost'] += ($ex_factory_value-$commision_cost);
				$tot_lib_basic_qnty+=$lib_basic_qnty;
				$tot_basic_qnty+=$basic_qnty;
				$tot_ex_factory_qnty+=$ex_factory_qnty;
				$tot_ex_factory_value+=$ex_factory_value;
				$tot_cm_value+=$cm_value;
				$tot_commision+=($ex_factory_value-$commision_cost);
			}

			//$buyer_wisi_data[$buyer_id]['lib_basic_qnty'] += $tot_lib_basic_qnty;
			$buyer_wisi_data[$buyer_id]['basic_qnty'] += $tot_basic_qnty;
			$buyer_wisi_data[$buyer_id]['ex_factory_qnty'] += $tot_ex_factory_qnty;
			$buyer_wisi_data[$buyer_id]['ex_factory_value'] += $tot_ex_factory_value;
			$buyer_wisi_data[$buyer_id]['cm_val'] += $tot_cm_value;
			$buyer_wisi_data[$buyer_id]['commision_cost'] += $tot_commision;
			$main_data.='</tr>';
			$i++;
        }
		//echo $main_data;die
		//echo $total_month;die;
		ob_start();

		?>
        <div id="scroll_body">
            <table width="700"  cellspacing="0" align="left">
                <tr>
                    <td align="center" colspan="7" class="form_caption">
                    <strong style="font-size:16px;">Company Name:<? echo  $company_library[$cbo_company_name] ;?></strong>
                	</td>
                </tr>
                <tr class="form_caption">
                	<td colspan="7" align="center" class="form_caption"> <strong style="font-size:15px;">Ex-Factory Report Summary</strong></td>
                </tr>
            </table>
            <table width="700" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="" align="left">
            	<thead>
                	<tr>
                        <th width="100">Buyer</th>
                        <th width="100">Allocated Basic Qty</th>
                        <th width="100">Exfactory Qty</th>
                        <th width="100">Exfactory Value</th>
                        <th width="100">Ex factory Basic qty</th>
                        <th width="100">Ex-Fac value without comm</th>
                        <th >CM Value</th>
                    </tr>
                </thead>
                <tbody>
                <?
				$p=1;
				foreach($buyer_wisi_data as $buyer_id_ref=>$row)
				{
					if ($p%2==0)
					$bgcolor="#E9F3FF";
					else
					$bgcolor="#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                    	<td><? echo $buyer_arr[$buyer_id_ref]; ?></td>
                        <td align="right"><? echo number_format($row["lib_basic_qnty"],0); ?></td>
                        <td align="right"><? echo number_format($row["ex_factory_qnty"],0); ?></td>
                        <td align="right"><? echo number_format($row["ex_factory_value"],2); ?></td>
                        <td align="right"><? echo number_format($row["basic_qnty"],0); ?></td>
                        <td align="right"><? echo number_format($row["commision_cost"],2); ?></td>
                        <td align="right"><? echo number_format($row["cm_val"],2); ?></td>
                    </tr>
                    <?
					$p++;
					$gt_lib_basic_qnty+=$row["lib_basic_qnty"];
					$gt_ex_factory_qnty+=$row["ex_factory_qnty"];
					$gt_ex_factory_value+=$row["ex_factory_value"];
					$gt_basic_qnty+=$row["basic_qnty"];
					$gt_cm_val+=$row["cm_val"];
					$gt_commision_cost+=$row["commision_cost"];
				}
				?>
                </tbody>
                <tfoot>
                	<tr>
                        <th>Grand Total:</th>
                        <th><? echo number_format($gt_lib_basic_qnty,0); ?></th>
                        <th><? echo number_format($gt_ex_factory_qnty,0); ?></th>
                        <th><? echo number_format($gt_ex_factory_value,2); ?></th>
                        <th><? echo number_format($gt_basic_qnty,0); ?></th>
                        <th><? echo number_format($gt_commision_cost,2); ?></th>
                        <th><? echo number_format($gt_cm_val,2); ?></th>
                    </tr>
                </tfoot>
            </table>
            <table width="700" align="left">
            	<tr><td>&nbsp;</td></tr>
            </table>
            <table width="<? echo $width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="" align="left">
            <thead>
                <tr>
					<?
                    $m=1;
                    foreach($result_data_arr as $yearMonth=>$vale)
                    {
						$month_arr=explode("-",$yearMonth);
						$month_val=($month_arr[1]*1);
						if($m==1)
						{
							?>
							<th width="700" colspan="7"><? echo $months[$month_val]; ?></th>
							<?
						}
						else
						{
							?>
							<th width="600" colspan="6"><? echo $months[$month_val]; ?></th>
							<?
						}
						$m++;
                    }
                    ?>
                </tr>
               <tr>
                    <th width="100">Buyer</th>
                     <?
                    foreach($result_data_arr as $yearMonth=>$vale)
                    {
                        $month_arr=explode("-",$yearMonth);
                        ?>
                        <th width="100">Allocated Basic Qty</th>
                        <th width="100">Exfactory Qty</th>
                        <th width="100">Exfactory Value</th>
                        <th width="100">Ex factory Basic qty</th>
                        <th width="100">Ex-Fac value without comm</th>
                        <th width="100">CM Value</th>
                        <?
                    }
                    ?>
               </tr>
            </thead>
         </table>
        <table width="<? echo $width;?>" cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table" id="" align="left">
			<?
            echo $main_data;

            /*foreach($buyer_tem_arr as $buyer_id=>$val)
            {
                if ($i%2==0)
                $bgcolor="#E9F3FF";
                else
                $bgcolor="#FFFFFF";
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" >
                    <td width="100"><? echo $buyer_arr[$buyer_id]; ?></td>
                    <?
                    foreach($result_data_arr as $month_id=>$result)
                    {
                        $ex_factory_qnty=$result_data_arr[$month_id][$buyer_id]['ex_factory_qnty'];
                        $ex_factory_value=$result_data_arr[$month_id][$buyer_id]['ex_factory_value'];
                        $cm_value=$result_data_arr[$month_id][$buyer_id]['cm_value'];
                        $lib_basic_qnty=$result_data_arr[$month_id][$buyer_id]['lib_basic_qnty'];
                        $basic_qnty=$result_data_arr[$month_id][$buyer_id]['basic_qnty'];

                        $commision_cost=$result_data_arr[$month_id][$buyer_id]['commision_cost'];
                        ?>
                        <td width="100" align="right"><? echo number_format($lib_basic_qnty,0); ?></td>
                        <td width="100" align="right"><? echo number_format($ex_factory_qnty,0); ?></td>
                        <td width="100" align="right"><? echo number_format($ex_factory_value,2); ?></td>
                        <td width="100" align="right"><? echo number_format($basic_qnty,0); ?></td>
                        <td width="100" align="right"><? echo number_format($ex_factory_value-$commision_cost,2); ?></td>
                        <td width="100" align="right"><? echo number_format($cm_value,2); ?></td>
                        <?
                        $total_mon_data[$month_id]['lib_basic_qnty'] += $lib_basic_qnty;
                        $total_mon_data[$month_id]['basic_qnty'] += $basic_qnty;
                        $total_mon_data[$month_id]['ex_factory_qnty'] += $ex_factory_qnty;
                        $total_mon_data[$month_id]['ex_factory_value'] += $ex_factory_value;
                        $total_mon_data[$month_id]['cm_val'] += $cm_value;
                        $total_mon_data[$month_id]['commision_cost'] += ($ex_factory_value-$commision_cost);
                    }
                    ?>
                </tr>
                <?
                $i++;
            }*/
            ?>
            <tfoot>
                <th>Total:&nbsp;</th>
                <?
                foreach($total_mon_data as $row)
                {
                    ?>
                    <th><? echo number_format($row['lib_basic_qnty'],0); ?></th>
                    <th><? echo number_format($row['ex_factory_qnty'],0); ?></th>
                    <th><? echo number_format($row['ex_factory_value'],2); ?></th>
                    <th><? echo number_format($row['basic_qnty'],0); ?></th>
                    <th><? echo number_format($row['commision_cost'],2); ?></th>
                    <th><? echo number_format($row['cm_val'],2); ?></th>
                    <?
                }
                ?>
            </tfoot>
        </table>
        </div>
		<?
	}
	else if($reportType==3)
	{
		$exfact_sql=sql_select("select po_break_down_id,
		sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as return_qnty,
		 sum(total_carton_qnty) as carton_qnty from pro_ex_factory_mst where status_active=1 and is_deleted=0 group by po_break_down_id");
		$exfact_qty_arr=$exfact_return_qty_arr=$exfact_cartoon_arr=array();
		foreach($exfact_sql as $row)
		{
			$exfact_qty_arr[$row[csf("po_break_down_id")]]=$row[csf("ex_factory_qnty")]-$row[csf("return_qnty")];
			$exfact_return_qty_arr[$row[csf("po_break_down_id")]]=$row[csf("return_qnty")];
			$exfact_cartoon_arr[$row[csf("po_break_down_id")]]=$row[csf("carton_qnty")];
		}
		$inspection_date_arr=return_library_array( "select po_break_down_id, max(inspection_date) as inspection_date from pro_buyer_inspection where status_active=1 and is_deleted=0 group by po_break_down_id", "po_break_down_id", "inspection_date");
		$invoice_array=return_library_array( "select id,invoice_no from com_export_invoice_ship_mst", "id", "invoice_no"  );
		$shipping_mode_array=return_library_array( "select id,shipping_mode from com_export_invoice_ship_mst", "id", "shipping_mode"  );
		$lc_sc_type_arr=return_library_array( "select id,is_lc from com_export_invoice_ship_mst", "id", "is_lc"  );
		$lc_sc_id_array=return_library_array( "select id,lc_sc_id from com_export_invoice_ship_mst", "id", "lc_sc_id"  );
		$lc_num_arr=return_library_array( "select id,export_lc_no from com_export_lc", "id", "export_lc_no"  );
		$sc_num_arr=return_library_array( "select id,contract_no from com_sales_contract", "id", "contract_no"  );

		$forwarder_arr=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name");
		$location_arr=return_library_array( "select id,location_name from lib_location", "id", "location_name");

		$challan_mst_arr=array();
		$challan_sql="SELECT a.id, a.sys_number_prefix_num,a.sys_number, a.forwarder, a.truck_no, a.driver_name, a.mobile_no, a.dl_no, a.location_id, a.transport_supplier, a.lock_no, b.remarks, b.po_break_down_id,a.delivery_floor_id
		from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b
		where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
		//echo $challan_sql;
		$challan_sql_result=sql_select($challan_sql);
		foreach($challan_sql_result as $row)
		{
			//$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['challan']=$row[csf("sys_number_prefix_num")];
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['challan']=$row[csf("sys_number")];
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['forwarder']=$row[csf("forwarder")];
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['truck_no']=$row[csf("truck_no")];
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['driver_name']=$row[csf("driver_name")];
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['mobile_no']=$row[csf("mobile_no")];
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['dl_no']=$row[csf("dl_no")];
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['location_id']=$row[csf("location_id")];
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['transport_supplier']=$row[csf("transport_supplier")];
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['lock_no']=$row[csf("lock_no")];
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['remarks']=$row[csf("remarks")];
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['floor']=$row[csf("delivery_floor_id")];
		}
		/*echo "<pre>";
		print_r($challan_mst_arr);
		echo "</pre>";*/

		if($db_type==0)
		{
			$sql= "SELECT b.id as po_id, max(a.lc_sc_no) as lc_sc_arr_no, group_concat(distinct a.invoice_no) as invoice_no, group_concat(distinct a.item_number_id) as itm_num_id,

			sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
			sum(a.total_carton_qnty) as carton_qnty, max(a.ex_factory_date)  as ex_factory_date, group_concat(distinct  a.lc_sc_no) as lc_sc_no, group_concat(distinct a.delivery_mst_id) as challan_id, group_concat(distinct d.delivery_floor_id) as del_floor,  b.shipment_date, b.po_number, (b.po_quantity*c.total_set_qnty) as po_quantity, (b.unit_price/c.total_set_qnty) as unit_price, b.shiping_status, c.id, c.company_name, c.buyer_name, c.job_no_prefix_num, YEAR(c.insert_date) as year, c.style_ref_no, c.style_description, c.set_smv, d.delivery_company_id as del_company,d.delivery_location_id as del_location

			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c,pro_ex_factory_delivery_mst d
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no $company_cond $str_cond $del_comp_cond $del_location_cond $del_floor_cond $shiping_status_cond $buyer_conds $internal_ref_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.delivery_mst_id=d.id
			group by
					b.id , b.shipment_date, b.po_number, b.unit_price, c.id, c.company_name, c.buyer_name, c.style_ref_no, c.style_description, c.set_smv, d.delivery_company_id ,d.delivery_location_id
			order by c.buyer_name, b.shipment_date ASC";
		}
		else if($db_type==2)
		{
			$sql= "SELECT b.id as po_id,max(a.lc_sc_no) as lc_sc_arr_no,
			LISTAGG(CAST( a.invoice_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.invoice_no) as invoice_no,
			LISTAGG(CAST( a.item_number_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.item_number_id) as itm_num_id,

			sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
			sum(a.total_carton_qnty) as carton_qnty, max(a.ex_factory_date) as ex_factory_date,
			LISTAGG(CAST( a.lc_sc_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.lc_sc_no) as lc_sc_no,
			LISTAGG(CAST( a.delivery_mst_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.delivery_mst_id) as challan_id,
			LISTAGG(CAST( d.delivery_floor_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY d.delivery_floor_id) as del_floor,
			b.shipment_date, b.po_number, (b.po_quantity*c.total_set_qnty) as po_quantity,(b.unit_price/c.total_set_qnty) as unit_price,b.shiping_status,
			c.id, c.company_name, c.buyer_name, c.job_no_prefix_num, to_char(c.insert_date,'YYYY') as year, c.style_ref_no, c.style_description, c.set_smv, d.delivery_company_id as del_company,d.delivery_location_id as del_location

			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c,pro_ex_factory_delivery_mst d
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no $company_cond $str_cond $del_comp_cond $del_location_cond $del_floor_cond $shiping_status_cond $buyer_conds $internal_ref_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  and a.delivery_mst_id=d.id
			group by
					b.id , b.shipment_date, b.po_number, b.unit_price,b.po_quantity,b.shiping_status,c.total_set_qnty,c.id,c.company_name, c.buyer_name, c.job_no_prefix_num, c.insert_date, c.style_ref_no, c.style_description,c.total_set_qnty, c.set_smv, d.delivery_company_id, d.delivery_location_id
			order by c.buyer_name, b.shipment_date ASC";

		}



		//echo $sql;

		$i=1;$s=1;

		$details_report .='<table width="3925" cellspacing="0" cellpadding="0"  border="1"  class="rpt_table" rules="all" id="table_body3" align="left">';

		$sql_result=sql_select($sql);
		foreach($sql_result as $row)
		{
		 $challan_id=implode(',',array_unique(explode(",",$row[csf('challan_id')])));
		  $data_arr[$challan_id][]=array(
				'po_id'=>$row[csf('po_id')],
				'lc_sc_no'=>$row[csf('lc_sc_no')],
				'invoice_no'=>$row[csf('invoice_no')],
				'challan_id'=>$row[csf('challan_id')],
				'shiping_status'=>$row[csf('shiping_status')],
				'shipment_date'=>$row[csf('shipment_date')],
				'ex_factory_date'=>$row[csf('ex_factory_date')],
				'job_no_prefix_num'=>$row[csf('job_no_prefix_num')],
				'year'=>$row[csf('year')],
				'buyer_name'=>$row[csf('buyer_name')],
				'company_name'=>$row[csf('company_name')],
				'del_company'=>$row[csf('del_company')],
				'del_location'=>$row[csf('del_location')],
				'po_number'=>$row[csf('po_number')],
				'style_ref_no'=>$row[csf('style_ref_no')],
				'style_description'=>$row[csf('style_description')],
				'po_quantity'=>$row[csf('po_quantity')],
				'unit_price'=>$row[csf('unit_price')],
				'ex_factory_qnty'=>$row[csf('ex_factory_qnty')]-$exfact_return_qty_arr[$row[csf("po_id")]],
				'set_smv'=>$row[csf('set_smv')],
				'itm_num_id'=>$row[csf('itm_num_id')]
			);

		}

		$tmp_challan_no_arr=array();
		foreach($data_arr as $challan=>$sql_result)
		{
			$s=1;
			$current_ex_fact_sub=0;
			$current_ex_fact_val_sub=0;
			$total_ex_fact_sub=0;
			$total_ex_fact_val_sub=0;

			foreach($sql_result as $row)
			{

				$ex_fact_date_range=$txt_date_from."*".$txt_date_to."_". 1;
				$total_exface_qnty=$txt_date_from."*".$txt_date_to."_". 2;
				if($break_id==0) $break_id=$row['po_id']; else $break_id=$break_id.",".$row['po_id'];
				if($sc_lc_id==0) $sc_lc_id=$row['lc_sc_no']; else $sc_lc_id=$sc_lc_id.",".$row['lc_sc_no'];

				$invoce_id_arr=array_unique(explode(",",$row['invoice_no']));
				$challan_id=array_unique(explode(",",$row["challan_id"]));
 				$challan_no=$forwarder=$vehi_no=$dirver_info=$location=$transfort_com=$lock_no=$remarks=$floor_no="";

			    $diff=($row['shiping_status']!=3)?datediff("d",$current_date, $row["shipment_date"])-1:datediff("d",$row["ex_factory_date"], $row["shipment_date"])-1;// Count Days in Hand Update By REZA;


				foreach($challan_id as $val)
				{
					//echo $val;
					if($challan_no=="") $challan_no=$challan_mst_arr[$val][$row['po_id']]['challan']; else $challan_no.=','.$challan_mst_arr[$val][$row['po_id']]['challan'];
					if($forwarder=="") $forwarder=$forwarder_arr[$challan_mst_arr[$val][$row['po_id']]['forwarder']]; else $forwarder.=','.$forwarder_arr[$challan_mst_arr[$val][$row['po_id']]['forwarder']];
					if($vehi_no=="") $vehi_no=$challan_mst_arr[$val][$row['po_id']]['truck_no']; else $vehi_no.=','.$challan_mst_arr[$val][$row['po_id']]['truck_no'];

					if($location=="") $location=$location_arr[$challan_mst_arr[$val][$row['po_id']]['location_id']]; else $location.=','.$location_arr[$challan_mst_arr[$val][$row['po_id']]['location_id']];
					if($transfort_com=="") $transfort_com=$forwarder_arr[$challan_mst_arr[$val][$row['po_id']]['transport_supplier']]; else $transfort_com.=','.$forwarder_arr[$challan_mst_arr[$val][$row['po_id']]['transport_supplier']];
					if($floor_no=="") $floor_no=$floor_library[$challan_mst_arr[$val][$row[('po_id')]]['floor']]; else $floor_no.=','.$floor_library[$challan_mst_arr[$val][$row[('po_id')]]['floor']];

					if($lock_no=="") $lock_no=$challan_mst_arr[$val][$row['po_id']]['lock_no']; else $lock_no.=','.$challan_mst_arr[$val][$row['po_id']]['lock_no'];
					if($remarks=="") $remarks=$challan_mst_arr[$val][$row['po_id']]['remarks']; else $remarks.=','.$challan_mst_arr[$val][$row['po_id']]['remarks'];

					//if($mobile_no=="") $mobile_no=$challan_mst_arr[$val][$row['po_id']]['mobile_no']; else $mobile_no.=','.$challan_mst_arr[$val][$row['po_id']]['mobile_no'];
					if($dirver_info=="") $dirver_info="Name: ".$challan_mst_arr[$val][$row[('po_id')]]['driver_name']."<br>Mob No: ".$challan_mst_arr[$val][$row[('po_id')]]['mobile_no']."<br>DL No: ".$challan_mst_arr[$val][$row[('po_id')]]['dl_no']; else $dirver_info.=','."Name: ".$challan_mst_arr[$val][$row[('po_id')]]['driver_name']."<br>Mob No: ".$challan_mst_arr[$val][$row[('po_id')]]['mobile_no']."<br>DL No: ".$challan_mst_arr[$val][$row[('po_id')]]['dl_no'];

				}

				$challan_no=implode(",",array_unique(explode(",",$challan_no)));
				$forwarder=implode(",",array_unique(explode(",",$forwarder)));
				$vehi_no=implode(",",array_unique(explode(",",$vehi_no)));
				$mobile_no=implode(",",array_unique(explode(",",$mobile_no)));
				$location=implode(",",array_unique(explode(",",$location)));
				$transfort_com=implode(",",array_unique(explode(",",$transfort_com)));
				$lock_no=implode(",",array_unique(explode(",",$lock_no)));
				$remarks=implode(",",array_unique(explode(",",$remarks)));
				$floor_no=implode(",",array_unique(explode(",",$floor_no)));

				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
				$comapny_id=$row["company_name"];
				if(!in_array($challan_no,$tmp_challan_no_arr))
				{
					$details_report .='<tr><td colspan="42" bgcolor="#CCCCCC"> Challan NO: '.$challan_no.'</td></tr>';
				}
				$tmp_challan_no_arr[]=$challan_no;


				$onclick=" change_color('tr_b".$i.$s."','".$bgcolor."')";
				$details_report .='<tr bgcolor="'.$bgcolor.'" onclick="'.$onclick.'" id="tr_b'.$i.$s.'">';
				$details_report .='<td width="40" align="center">'.$s.'</td>
				<td width="150" align="center" ><p>'.$company_library[$row["company_name"]].'</p></td>
				<td width="60" align="center" ><p>'.$row["job_no_prefix_num"].'</p></td>
				<td width="60" align="center" ><p>'.$row["year"].'</p></td>
				<td width="100" align="center" ><p>'.$buyer_arr[$row["buyer_name"]].'</p></td>
				<td width="110" align="center"><p>'.$row["po_number"].'</p></td>

				<td width="125" align="center" ><p>'.$company_library[$row["del_company"]].'</p></td>
				<td width="125" align="center" ><p>'.$location_library[$row["del_location"]].'</p></td>
				<td width="125" align="center" ><p>'.$floor_no.'</p></td>

				<td width="100" align="center"><p>';
					$inv_id=""; $lc_sc_no=""; $ship_mode="";
					foreach($invoce_id_arr as $invoice_id)
					{
						if($inv_id=="") $inv_id=$invoice_array[$invoice_id]; else $inv_id=$inv_id.",".$invoice_array[$invoice_id];
						if($ship_mode=="") $ship_mode=$shipment_mode[$shipping_mode_array[$invoice_id]]; else $ship_mode=$ship_mode.",".$shipment_mode[$shipping_mode_array[$invoice_id]];
						if($lc_sc_type_arr[$invoice_id]==1)
						{
							if($lc_sc_no=="") $lc_sc_no=$lc_num_arr[$lc_sc_id_array[$invoice_id]]; else $lc_sc_no=$lc_sc_no.",".$lc_num_arr[$lc_sc_id_array[$invoice_id]];
						}
						else
						{
							if($lc_sc_no=="") $lc_sc_no=$sc_num_arr[$lc_sc_id_array[$invoice_id]]; else $lc_sc_no=$lc_sc_no.",".$sc_num_arr[$lc_sc_id_array[$invoice_id]];
						}
					}

					$details_report .=$inv_id.'</p></td>
					<td width="100" align="center"><p>'.$lc_sc_no.'</p></td>
					<td width="100"><p>'.$row["style_ref_no"].'</p></td>
					<td width="100"><p>'.$row["style_description"].'</p></td>
								<td width="110" align="center"><p>';//$garments_item
									$item_name_arr=explode(",",$row['itm_num_id']);
									$item_name_arr=array_unique($item_name_arr);
									if(!empty($item_name_arr))
									{
										$p=1;
										foreach($item_name_arr as $item_id)
										{
											if($p!=1) $item_name_all .=",";
											$item_name_all .=$garments_item[$item_id];
											$p++;
										}
									}
									$total_ex_fact_qty=$exfact_qty_arr[$row["po_id"]]-$exfact_return_qty_arr[$row[csf("po_id")]];
									$total_cartoon_qty=$exfact_cartoon_arr[$row["po_id"]];
									$po_quantity=$row["po_quantity"];
									$unit_price=$row["unit_price"];
									$current_ex_Fact_Qty=$row["ex_factory_qnty"]-$exfact_return_qty_arr[$row[csf("po_id")]];
									$basic_qnty=($total_ex_fact_qty*$row["set_smv"])/$basic_smv_arr[$row["company_name"]];
									$details_report .=$item_name_all.'</p></td>
									<td width="80" align="center"><p>'.$row["set_smv"].'</p></td>
									<td width="70" align="center"><p>'.change_date_format($row["shipment_date"]).'</p></td>
									<td width="70" align="center"><a href="##" onclick="openmypage_ex_date('.$comapny_id.",'".$row['po_id']."','".$row['itm_num_id']."','".$ex_fact_date_range."','ex_date_popup','".$row['challan_id']."'".')">'.change_date_format($row['ex_factory_date']).'</a></td>
									<td width="70" align="center"><p>'.$ship_mode.'</p></td>
									<td width="60" align="center"><p>'.$diff.'</p></td>
									<td width="80" align="right"><p>'. number_format($po_quantity,0,'', '').'</p></td>
									<td width="70" align="right"><p>'. number_format($unit_price,4).'</p></td>
									<td width="100" align="right"><p>'. number_format(($po_quantity*$unit_price),2).'</p></td>
									<td width="80" align="right"><p><a href="##" onclick="openmypage_ex_date('.$comapny_id.",'".$row['po_id']."','".$row['itm_num_id']."','".$ex_fact_date_range."','ex_date_popup','".$row['challan_id']."'".')">'. number_format($current_ex_Fact_Qty,0,'.', '').'</a></p></td>
									<td width="100" align="right"><p>'. number_format( $current_ex_fact_value=$current_ex_Fact_Qty*$unit_price,2).'</p></td>
									<td width="80" align="right"><p>'. number_format($row["carton_qnty"],0,'.', '').'</p></td>
									<td width="80" align="right"><p><a href="##" onclick="openmypage_ex_date('.$comapny_id.",'".$row['po_id']."','".$row['itm_num_id']."','".$total_exface_qnty."','ex_date_popup','".$row['challan_id']."'".')">'.number_format($total_ex_fact_qty,0,'.', '').'</a></p></td>
									<td width="100" align="right"><p>'. number_format($total_ex_fact_value=$total_ex_fact_qty*$unit_price,2).'</p></td>
									<td width="80" align="right"><p>'.number_format($total_cartoon_qty,0,'.', '').'</p></td>
									<td width="100" align="right" title="Current Ex.Qty*SMV"><p>'. number_format($total_sales_minutes=$current_ex_Fact_Qty*$row["set_smv"]).'</p></td>


									<td width="80" align="right"><p>'.number_format($basic_qnty,0,'','').'</p></td>
									<td width="80" align="right"><p>'. number_format($excess_shortage_qty=$po_quantity-$total_ex_fact_qty,0,'', '').'</p></td>
									<td width="100" align="right"><p>'. number_format($excess_shortage_value=$excess_shortage_qty*$unit_price,2).'</p></td>
									<td align="center" width="80"><p>'. number_format($total_ex_fact_qty_parcent=($total_ex_fact_qty/$po_quantity)*100,0).'</p></td>
									<td width="110"><p>'.$location.'</p></td>
									<td width="120"><p>'.$transfort_com.'</p></td>
									<td width="80" align="center"><p>'.$lock_no.'</p></td>
									<td width="120" align="center"><p>'.$forwarder.'</p></td>
									<td width="80" align="center"><p>'.$vehi_no.'</p></td>
									<td width="130"><p>'.$dirver_info.'</p></td>
									<td width="70" align="center"><p>'.(change_date_format($inspection_date_arr[$row['po_id']]) == '0000-00-00' || change_date_format($inspection_date_arr[$row['po_id']]) == '' ? '' : change_date_format(change_date_format($inspection_date_arr[$row['po_id']]))).'</p></td>
									<td width="120"><p>'.$shipment_status[$row['shiping_status']].'</p></td>
									<td><p>'.$remarks.'</p></td>
								</tr>';
								$current_ex_fact_sub+=$current_ex_Fact_Qty;
								$current_ex_fact_val_sub+=$current_ex_fact_value;
								$total_ex_fact_sub+=$total_ex_fact_qty;
								$total_ex_fact_val_sub+=$total_ex_fact_value;

								$master_data[$row["buyer_name"]]['b_id']=$row["buyer_name"];
								$master_data[$row["buyer_name"]]['po_qnty'] +=$row["po_quantity"];
								$master_data[$row["buyer_name"]]['po_value'] +=$row["po_quantity"]*$row["unit_price"];
								$master_data[$row["buyer_name"]]['basic_qnty'] +=$basic_qnty;
								$master_data[$row["buyer_name"]]['ex_factory_qnty'] +=$row["ex_factory_qnty"]-$exfact_return_qty_arr[$row[csf("po_id")]];
								$master_data[$row["buyer_name"]]['ex_factory_value'] +=$row["ex_factory_qnty"]*$row["unit_price"];
								$master_data[$row["buyer_name"]]['total_ex_fact_qty'] +=$total_ex_fact_qty;
								$master_data[$row["buyer_name"]]['total_ex_fact_value'] +=$total_ex_fact_qty*$row["unit_price"];

								$total_po_qty+=$row["po_quantity"];
								$total_basic_qty+=$basic_qnty;
								$total_po_valu+=$row["po_quantity"]*$row["unit_price"];
								$total_ex_qty+=$row["ex_factory_qnty"]-$exfact_return_qty_arr[$row[csf("po_id")]];
								$total_crtn_qty+=$row["carton_qnty"];
								$total_ex_valu+=($row["ex_factory_qnty"]-$exfact_return_qty_arr[$row[csf("po_id")]])*$row["unit_price"];
								$g_total_ex_qty+=$total_ex_fact_qty;
								$g_total_ex_crtn+=$total_cartoon_qty;
								$g_total_ex_val+=$total_ex_fact_qty*$row["unit_price"];
								$g_sales_minutes+=$current_ex_Fact_Qty*$row["set_smv"];
								$total_eecess_storage_qty+=$excess_shortage_qty;
								$total_eecess_storage_val+=$excess_shortage_value;

								$s++;$item_name_all="";
							}
							$i++;
							//echo $current_ex_fact_sub;
							$onclick=" change_color('tr_23_b".$i.$s."','".$bgcolor."')";

							$details_report .='<tr bgcolor="#E4E4E4" onclick="'.$onclick.'" id="tr_23_b'.$i.$s.'">';
				$details_report .='<td   colspan="22" align="left"><b>Sub Total</b></td>
									<td width="80" align="right"><p>'. number_format( $current_ex_fact_sub,2).'</p> </td>
									<td width="100" align="right"><p>'. number_format( $current_ex_fact_val_sub,2).'</p></td>
									<td width="80" align="right"><p></p></td>
									<td width="80" align="right"><p>'.number_format($total_ex_fact_sub,0,'.', '').'</p></td>
									<td width="100" align="right"><p>'. number_format($total_ex_fact_val_sub,2).'</p></td>

									<td colspan="15"><p></p></td>

								</tr>';


						}



						$details_report .='
					</table>';

					foreach($master_data as $rows)
					{
						$total_po_val+=$rows[po_value];
					}

					?>
        <div style="width:3400x;">
            <div style="width:1220px" >
		                    <table width="1190"  cellspacing="0"  align="center">
		                    	<tr>
		                    		<td align="center" colspan="10" class="form_caption">
		                    			<strong style="font-size:16px;">Company Name:<? echo  $company_library[$cbo_company_name] ;?></strong>
		                    		</td>
		                    	</tr>
		                    	<tr class="form_caption">
		                    		<td colspan="10" align="center" class="form_caption"> <strong style="font-size:15px;">Ex-Factory Report</strong></td>
		                    	</tr>
		                    	<tr align="center">
		                    		<td colspan="10" align="center" class="form_caption"> <strong style="font-size:15px;">Total Summary</strong></td>
		                    	</tr>
		                    </table>
		                    <table width="1190" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1">
		                    	<thead>
		                    		<th width="40" height="34">SL</th>
		                    		<th width="130">Buyer Name</th>
		                    		<th width="100">PO Qty.</th>
		                    		<th width="130">PO Value</th>
		                    		<th width="100">PO Value(%)</th>
		                    		<th width="100">Current Ex-Fact. Qty.</th>
		                    		<th width="130">Current Ex-Fact. Value</th>
		                    		<th width="100">Total Ex-Fact. Qty.</th>
		                    		<th width="130">Total Ex-Fact. Value </th>
		                    		<th width="100">Total Ex-Fact. (Basic Qty)</th>
		                    		<th >Total Ex-Fact. Value %</th>
		                    	</thead>
		                    </table>
		                    <table width="1190" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body">
		                    	<?
		                    	$m=1;
		                    	foreach($master_data as $rows)
		                    	{
		                    		if ($i%2==0)
		                    			$bgcolor="#E9F3FF";
		                    		else
		                    			$bgcolor="#FFFFFF";
		                    		?>
		                    		<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" >
		                    			<td width="40" align="center"><? echo $m; ?></td>
		                    			<td width="130">
		                    				<p><?
		                    					echo $buyer_arr[$rows[b_id]];
		                    					?></p>
		                    				</td>
		                    				<td width="100" align="right"><p><?  $po_quantity=$rows[po_qnty];echo number_format($po_quantity,0); $total_buyer_po_quantity+=$po_quantity; ?></p></td>
		                    				<td width="130" align="right" ><p  id="value_<? echo $i ; ?>"><? $buyer_po_value=$rows[po_value]; echo number_format($buyer_po_value,2 ,'.', '');  $total_buyer_po_value+=$buyer_po_value; ?></p></td>
		                    				<td width="100" align="right">
		                    					<? echo number_format(($buyer_po_value/$total_po_val)*100,2,'.','');$parcentages+=($buyer_po_value/$total_po_val)*100; ?>
		                    				</td>
		                    				<td width="100" align="right">
		                    					<p><?
		                    						$current_ex_Fact_Qty=$rows[ex_factory_qnty];  echo number_format($current_ex_Fact_Qty,0,'',''); $total_current_ex_Fact_Qty +=$current_ex_Fact_Qty;
		                    						?></p>
		                    					</td>
		                    					<td width="130" align="right">
		                    						<p><?
		                    							$current_ex_fact_value=$rows[ex_factory_value]; echo number_format($current_ex_fact_value,2,'.',''); $total_current_ex_fact_value+=$current_ex_fact_value;
		                    							?></p>
		                    						</td>
		                    						<td align="right" width="100">
		                    							<p><?
		                    								$total_ex_fact_qty=$rows[total_ex_fact_qty]; echo number_format($total_ex_fact_qty,0,'',''); $mt_total_ex_fact_qty+=$total_ex_fact_qty;
		                    								?></p>
		                    							</td>
		                    							<td align="right" width="130">
		                    								<p><?
		                    									$total_ex_fact_value=$rows[total_ex_fact_value];  echo  number_format($total_ex_fact_value,2,'.',''); $mt_total_ex_fact_value+=$total_ex_fact_value;
		                    									?></p>
		                    								</td>
		                    								<td width="100" align="right">
		                    									<p><?
		                    										$buyer_basic_qnty=$rows[basic_qnty];  echo number_format($buyer_basic_qnty,0,'',''); $total_buyer_basic_qnty +=$buyer_basic_qnty;
		                    										?></p>
		                    									</td>
		                    									<td align="right">
		                    										<p><?
		                    											$total_ex_fact_value_parcentage=($total_ex_fact_value/$buyer_po_value)*100;
		                    											echo number_format($total_ex_fact_value_parcentage,0)
		                    											?> %</p>
		                    										</td>
		                    		</tr>
		                    		<?
		                    		$i++;$m++;
		                    		$buyer_po_quantity=0;
		                    		$buyer_po_value=0;
		                    		$current_ex_Fact_Qty=0;
		                    		$current_ex_fact_value=0;
		                    		$total_ex_fact_qty=0;
		                    		$total_ex_fact_value=0;

		                    	}
		                    	?>
                    			<input type="hidden" name="total_i" id="total_i" value="<? echo $i; ?>" />
			                    <tfoot>
			                        <th align="right" colspan="2"><b>Grand Total:</b></th>
			                        <th  align="right" id="total_buyer_po_quantity"><? echo number_format($total_buyer_po_quantity,0);  ?></th>
			                        <th  align="right" id="value_total_buyer_po_value"><? echo number_format($total_buyer_po_value,2 ,'.', ''); ?> </th>
			                        <th align="right" id="parcentages"><? echo ceil($parcentages); ?></th>
			                        <th  align="right" id="total_current_ex_Fact_Qty"><? echo number_format($total_current_ex_Fact_Qty,0); ?></th>
			                        <th  align="right" id="value_total_current_ex_fact_value"><? echo  number_format($total_current_ex_fact_value,2); ?></th>
			                        <th align="right" id="mt_total_ex_fact_qty"><? echo number_format($mt_total_ex_fact_qty,0); ?></th>
			                        <th align="right" id="value_mt_total_ex_fact_value"><? echo number_format($mt_total_ex_fact_value,2); ?></th>
			                        <th  align="right" id="total_buyer_basic_qnty"><? echo number_format($total_buyer_basic_qnty,0); ?></th>
			                        <th align="right"></th>
			                    </tfoot>
                			</table>
            </div>
            <br />
            <div>
                <table width="3800"  >
                    <tr>
                    <td colspan="28" class="form_caption"><strong style="font-size:16px;">Shipped Out Order Details Report</strong></td>
                    </tr>
                </table>
                <table width="3925" border="1" class="rpt_table" rules="all" id="table_header_2">
                    <thead>
                    	<th width="40">SL</th>
                    	<th width="150">Company</th>
                    	<th width="60">Job</th>
                    	<th width="60">Year</th>
                    	<th width="100">Buyer Name</th>
                    	<th width="110">Order NO</th>
                    	<th width="125">Del Company</th>
                    	<th width="125">Del Location</th>
                    	<th width="125">Del Floor</th>
                    	<th width="100" >Invoice NO</th>
                    	<th width="100" >LC/SC NO</th>
                    	<th width="100">Style Ref. no.</th>
                    	<th width="100">Style Description</th>
                    	<th width="110">Item Name</th>
                    	<th width="80">Item SMV</th>
                    	<th width="70">Shipment Date</th>
                    	<th width="70">Ex-Fac. Date</th>
                    	<th width="70">Shipping Mode</th>
                    	<th width="60">Days in Hand</th>
                    	<th width="80">PO Qty Pcs</th>
                    	<th width="70">Unit Price</th>
                    	<th width="100">PO Value</th>
                    	<th width="80">Current Ex-Fact. Qty (pcs)</th>
                    	<th width="100">Current Ex-Fact. Value</th>
                    	<th width="80">Current Carton Qty</th>
                    	<th width="80">Total Ex-Fact. Qty.</th>
                    	<th width="100">Total Ex-Fact. Value</th>
                    	<th width="80">Total Carton Qty</th>
                    	<th width="100">Sales Minute</th>
                    	<th width="80">Total Ex-Fact. (Basic Qty)</th>
                    	<th width="80">Excess/ Shortage Qty</th>
                    	<th width="100">Excess/ Shortage Value</th>
                    	<th width="80">Total Ex-Fact. Qty. %</th>
                    	<th width="110">Location</th>
                    	<th width="120">Transport Company</th>
                    	<th width="80">Lock No</th>
                    	<th width="120">C & F Name</th>
                    	<th width="80">Vehicle No</th>
                    	<th width="130">Driver Info</th>
                    	<th width="70">Inspaction Date</th>
                    	<th width="120">Ex-Fact Status</th>
                    	<th>Remarks</th>
                    </thead>
                </table>
                <div style="width:3945px; overflow-y:scroll; overflow-x:hidden; max-height:300px;"  id="scroll_body" >
                	<? echo $details_report; ?>
                	<table width="3925" cellspacing="0" cellpadding="0"  border="1"  class="rpt_table" rules="all" id="table_footer">
                		<tfoot>
                			<tr>
                				<th width="40">&nbsp;</th>
                				<th width="150">&nbsp;</th>
                				<th width="60">&nbsp;</th>
                				<th width="60">&nbsp;</th>
                				<th width="100">&nbsp;</th>
                				<th width="110">&nbsp;</th>
                				<th width="125">&nbsp;</th>
                				<th width="125">&nbsp;</th>
                				<th width="125">&nbsp;</th>
                				<th width="100">&nbsp;</th>
                				<th width="100" >&nbsp;</th>
                				<th width="100">&nbsp;</th>
                				<th width="100">&nbsp;</th>
                				<th width="110">&nbsp;</th>
                				<th width="80">&nbsp;</th>
                				<th width="70">&nbsp;</th>
                				<th width="70">&nbsp;</th>
                				<th width="70">&nbsp;</th>
                				<th width="60" align="right"><strong>Grand Total</strong></th>
                				<th width="80" id="total_po_qty" align="right"><? echo  number_format($total_po_qty,0);?></th>
                				<th width="70" align="right">&nbsp;</th>
                				<th width="100" align="right" id="value_total_po_valu"><? echo  number_format($total_po_valu,2); ?></th>
                				<th width="80" align="right" id="total_ex_qty"><? echo number_format($total_ex_qty,0); ?></th>
                				<th width="100" align="right" id="value_total_ex_valu"><? echo number_format($total_ex_valu,2);?></th>
                				<th width="80" align="right" id="total_crtn_qty"><? echo number_format($total_crtn_qty,0); ?></th>
                				<th width="80" align="right" id="g_total_ex_qty"><? echo number_format($g_total_ex_qty,0);?></th>
                				<th width="100" align="right" id="value_g_total_ex_val"><? echo number_format($g_total_ex_val,2);?></th>
                				<th width="80" align="right" id="g_total_ex_crtn"><? echo number_format($g_total_ex_crtn,0);?></th>
                				<th width="100" align="right" id="value_sales_minutes"><? echo number_format($g_sales_minutes);?></th>

                				<th width="80" align="right" id="total_basic_qty"><? echo number_format($total_basic_qty,0); ?></th>
                				<th width="80" align="right" id="total_eecess_storage_qty"><? echo number_format($total_eecess_storage_qty,0);?></th>
                				<th width="100" align="right" id="value_total_eecess_storage_val"><? echo number_format($total_eecess_storage_val,0);?></th>
                				<th width="80">&nbsp;</th>
                				<th width="110">&nbsp;</th>
                				<th width="120">&nbsp;</th>
                				<th width="80">&nbsp;</th>
                				<th width="120">&nbsp;</th>
                				<th width="80">&nbsp;</th>
                				<th width="130">&nbsp;</th>
                				<th width="70">&nbsp;</th>
                				<th width="120">&nbsp;</th>
                				<th>&nbsp;</th>
                			</tr>
                		</tfoot>
                	</table>
                </div>
            </div>
        </div>

		<?
	}
	else if($reportType==4)
	{
		//for chaity
		$company=str_replace("'", "", $cbo_company_name);
		$buyer_name=str_replace("'", "", $cbo_buyer_name);
		$delv_comp=str_replace("'", "", $cbo_delivery_company_name);
		$delv_floor=str_replace("'", "", $cbo_del_floor);
		$location_name=str_replace("'", "", $cbo_location_name);
		$shipping_status=str_replace("'", "", $cbo_shipping_status);
		$all_conds="";
		$all_conds.=($company)? " and a.company_id='$company'" : " ";
		$all_conds.=($buyer_name)? " and a.buyer_id='$buyer_name'" : " ";
		$all_conds.=($delv_comp)? " and a.delivery_company_id in($delv_comp)" : " ";
		$all_conds.=($location_name)? " and a.delivery_location_id in($location_name)" : " ";
		$all_conds.=($delv_floor)? " and a.delivery_floor_id in($delv_floor)" : " ";
		$all_conds.=($shipping_status)? " and b.shiping_status =$shipping_status" : " ";
		$buyer_conds=($buyer_name)? " and a.buyer_name='$buyer_name'" : " ";
		if($txt_date_from!="" && $txt_date_to!="")
		{
			$all_conds.=" and b.ex_factory_date between '$txt_date_from' and  '$txt_date_to ' ";
		}
		//echo $all_conds;//die;
		$country_short_arr=return_library_array( "select id,short_name from  lib_country", "id", "short_name"  );
		$season_arrs=return_library_array( "select id,season_name from  lib_buyer_season", "id", "season_name"  );
		$buyer_arr=return_library_array( "select id,buyer_name from  lib_buyer", "id", "buyer_name"  );
		$week_for_header=array();$no_of_week_for_header=array();
		$sql_week_header=sql_select("SELECT week_date,week from week_of_year  ");
		foreach ($sql_week_header as $row_week_header)
		{
			$week_for_header[$row_week_header[csf("week")]]=$row_week_header[csf("week")];
			$no_of_week_for_header[$row_week_header[csf("week_date")]]=$row_week_header[csf("week")];
		}
 		//print_r($no_of_week_for_header);


		$color_or_size_level=return_field_value("ex_factory","variable_settings_production"," variable_list = 1 and company_name = '$company' and is_deleted=0 and status_active=1");
		if($color_or_size_level==1)
		{
			  $ex_fac_sql="SELECT b.po_break_down_id as po_id,b.country_id,sum( case when b.entry_form<>85 then  b.ex_factory_qnty else 0 end ) as qnty ,sum( case when b.entry_form=85 then  b.ex_factory_qnty else 0 end ) as ret_qnty,max(b.ex_factory_date) as dates ,b.shiping_mode,b.shiping_status from pro_ex_factory_delivery_mst a,pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $all_conds group by  b.po_break_down_id,b.country_id,b.shiping_mode,b.shiping_status ";
		}
		else
		{
			$ex_fac_sql="SELECT b.po_break_down_id as po_id,b.country_id ,sum( case when b.entry_form<>85 then  c.production_qnty else 0 end ) as qnty ,sum( case when b.entry_form=85 then  c.production_qnty else 0 end ) as ret_qnty  ,max(b.ex_factory_date) as dates,b.shiping_mode,b.shiping_status from pro_ex_factory_delivery_mst a,pro_ex_factory_mst b,pro_ex_factory_dtls c where a.id=b.delivery_mst_id and b.id=c.mst_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $all_conds group by  b.po_break_down_id,b.country_id,b.shiping_mode,b.shiping_status";
		}
		//echo $ex_fac_sql." __ ex_fac_sql";//die;
		$exfac_arrs=sql_select($ex_fac_sql);
		$order_cnty_wise_ex_arr=array();
		$all_po_ex_arr=array();
		foreach($exfac_arrs as $values)
		{
			$order_cnty_wise_ex_arr[$values[csf("po_id")]][$values[csf("country_id")]]["qnty"]+=$values[csf("qnty")]-$values[csf("ret_qnty")];
			$order_cnty_wise_ex_arr[$values[csf("po_id")]][$values[csf("country_id")]]["dates"] =$values[csf("dates")];
			if($order_cnty_wise_ex_arr[$values[csf("po_id")]][$values[csf("country_id")]]["shiping_mode"]=="")
			{
				$order_cnty_wise_ex_arr[$values[csf("po_id")]][$values[csf("country_id")]]["shiping_mode"].=$shipment_mode[$values[csf("shiping_mode")]];
			}
			else
			{
				$order_cnty_wise_ex_arr[$values[csf("po_id")]][$values[csf("country_id")]]["shiping_mode"].=','.$shipment_mode[$values[csf("shiping_mode")]];
			}

			if($order_cnty_wise_ex_arr[$values[csf("po_id")]][$values[csf("country_id")]]["shiping_status"]=="")
			{
				$order_cnty_wise_ex_arr[$values[csf("po_id")]][$values[csf("country_id")]]["shiping_status"].=$shipment_status[$values[csf("shiping_status")]];
				$duplicate_mode_check[$values[csf("po_id")]][$values[csf("country_id")]][$values[csf("shiping_status")]]=$values[csf("shiping_status")];
			}
			else
			{
				if($duplicate_mode_check[$values[csf("po_id")]][$values[csf("country_id")]][$values[csf("shiping_status")]]=="")
				{
					$order_cnty_wise_ex_arr[$values[csf("po_id")]][$values[csf("country_id")]]["shiping_status"].=','.$shipment_status[$values[csf("shiping_status")]];
				}

			}

			$all_po_ex_arr[$values[csf("po_id")]]=$values[csf("po_id")];
			$all_country_ex_arr[$values[csf("country_id")]]=$values[csf("country_id")];
		}

 		$all_po_ex_ids=implode(",", $all_po_ex_arr);

		$all_po_ex_cond="";

		if($db_type==2 &&  count($all_po_ex_arr)>999)
		{
			$po_chunk=array_chunk($all_po_ex_arr, 999);
			foreach($po_chunk as $row)
			{
				$po_ids=implode(",", $row);
				if($all_po_ex_cond=="")
				{
					$all_po_ex_cond.=" and (b.id in ($po_ids)";
				}
				else
				{
					$all_po_ex_cond.=" or b.id in ($po_ids)";
				}

			}
			$all_po_ex_cond.=")";

		}
		else
		{

			$all_po_ex_cond=" and b.id in ($all_po_ex_ids)";
			//echo "<pre>";var_dump($all_po_ex_cond);echo "</pre> in else"; //die;
		}



		$all_country_ex_ids=implode(",", $all_country_ex_arr);
		$all_country_ex_cond="";

		if($db_type==2 &&  count($all_country_ex_arr)>999)
		{
			$country_chunk=array_chunk($all_country_ex_arr,999);
			foreach($country_chunk as $row)
			{
				$country_ids=implode(",", $row);
				if($all_country_ex_cond=="")
				{
					$all_country_ex_cond.=" and (c.country_id in ($country_ids)";
				}
				else
				{
					$all_country_ex_cond.=" or c.country_id in ($country_ids)";
				}

			}
			$all_country_ex_cond.=")";

		}
		else
		{
			$all_country_ex_cond=" and c.country_id in ($all_country_ex_ids)";
		}




		$order_sql="SELECT a.total_set_qnty, b.id,b.shiping_status ,a.job_no_prefix_num,a.job_no,a.style_ref_no,b.po_number,c.country_id,c.country_ship_date,a.order_uom,c.cutup,sum(c.order_quantity) as cnty_qnty,b.unit_price,a.ship_mode,a.season_buyer_wise,a.buyer_name from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.job_no=c.job_no_mst and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $all_po_ex_cond $all_country_ex_cond $buyer_conds $internal_ref_cond group by a.total_set_qnty, b.id ,a.job_no_prefix_num,b.shiping_status ,a.job_no,a.style_ref_no,b.po_number,c.country_id,c.country_ship_date,a.order_uom,c.cutup,b.unit_price,a.ship_mode,a.season_buyer_wise,a.buyer_name";
		 //echo $order_sql;//die;
		$order_arrs=sql_select($order_sql);
		$ex_factory_data=array();
		foreach($order_arrs as $keys=>$vals)
		{
			$ex_factory_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("id")]][$vals[csf("country_id")]]["cnty_qnty"] +=$vals[csf("cnty_qnty")];
			$ex_factory_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("id")]][$vals[csf("country_id")]]["dates"] =$vals[csf("country_ship_date")];
			$ex_factory_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("id")]][$vals[csf("country_id")]]["cutup"] =$vals[csf("cutup")];
			$ex_factory_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("id")]][$vals[csf("country_id")]]["order_uom"] =$vals[csf("order_uom")];
			//$ex_factory_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("id")]][$vals[csf("country_id")]]["unit_price"] =$vals[csf("unit_price")];
			$ex_factory_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("id")]][$vals[csf("country_id")]]["unit_price"] =($vals[csf("unit_price")]/$vals[csf("total_set_qnty")]);
			$ex_factory_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("id")]][$vals[csf("country_id")]]["ship_mode"] =$vals[csf("ship_mode")];
			$ex_factory_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("id")]][$vals[csf("country_id")]]["season_buyer_wise"] =$vals[csf("season_buyer_wise")];
			$ex_factory_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("id")]][$vals[csf("country_id")]]["shiping_status"] =$vals[csf("shiping_status")];
			$ex_factory_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("id")]][$vals[csf("country_id")]]["po_number"] =$vals[csf("po_number")];
			$ex_factory_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("id")]][$vals[csf("country_id")]]["job"] =$vals[csf("job_no_prefix_num")];
			$ex_factory_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("id")]][$vals[csf("country_id")]]["buyer_name"] =$vals[csf("buyer_name")];


		}
		ob_start();
		?>
		<script type="text/javascript">
			 //setFilterGrid("table_body",-1)
		</script>
		<div style="width:1420px" >
			<table width="1390"  cellspacing="0"  align="center">
				<tr>
					<td height="11" colspan="17"></td>
				</tr>
				<tr class="form_caption">
					<td colspan="17" align="center" class="form_caption"> <strong style="font-size:15px;">Country Wise Ex-Factory Report</strong></td>
				</tr>
				<tr>
					<td align="center" colspan="17" class="form_caption">
						<strong style="font-size:16px;">Company Name:<? echo  $company_library[$company] ;?></strong>
					</td>
				</tr>
				<tr>
					<td height="11" colspan="17"></td>
				</tr>


			</table>

					<table align="left" width="1420" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1"  >
						<thead>
		 					<th width="30" style="word-wrap: break-word;word-break: break-all;">SL</th>
                            <th width="100" style="word-wrap: break-word;word-break: break-all;">Buyer</th>
		 					<th width="40" style="word-wrap: break-word;word-break: break-all;">Job</th>
							<th width="100" style="word-wrap: break-word;word-break: break-all;">Style</th>
							<th width="70" style="word-wrap: break-word;word-break: break-all;">Order No</th>
							<th width="60" style="word-wrap: break-word;word-break: break-all;">Country</th>
							<th width="100" style="word-wrap: break-word;word-break: break-all;">Country Ship Date</th>
							<th width="30" style="word-wrap: break-word;word-break: break-all;">Week</th>
							<th width="60" style="word-wrap: break-word;word-break: break-all;">Cut Off</th>
							<th width="70" style="word-wrap: break-word;word-break: break-all;">UOM</th>
							<th width="60" style="word-wrap: break-word;word-break: break-all;">Order qty</th>
							<th width="30" style="word-wrap: break-word;word-break: break-all;">Unit Price</th>
							<th width="63" style="word-wrap: break-word;word-break: break-all;">Order FOB value </th>
							<th width="100" style="word-wrap: break-word;word-break: break-all;">EX-Factory Qty</th>
							<th width="95" style="word-wrap: break-word;word-break: break-all;">EX-Factory FOB Value</th>
							<th width="100" style="word-wrap: break-word;word-break: break-all;">Ex-Factory Date</th>
							<th width="50" style="word-wrap: break-word;word-break: break-all;">Delay</th>
							<th width="35" style="word-wrap: break-word;word-break: break-all;">Ship Mode</th>
							<th width="110" style="word-wrap: break-word;word-break: break-all;">Shipment Status</th>
							<th width="50" style="word-wrap: break-word;word-break: break-all;">Season</th>
						</thead>
					</table>
			<div  style="max-height:225px;float: left; overflow-y:scroll;overflow-x: hidden; width:1408px" id="scroll_body" >

					<table align="left" width="1420" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body">
						  <?
		                  $i=1;
		                  $gr_order_qnty=0;
		                  $gr_order_fob_value=0;
		                  $gr_ex_fac_qnty=0;
		                  $gr_ex_fac_fob=0;
		                  foreach($ex_factory_data as $job_id=>$style_data)
		                  {
		                  	 foreach($style_data as $style_id=>$po_data)
		                  	 {
		                  	 	foreach($po_data as $po_id=>$county_data)
		                  	 	{
		                  	 		foreach($county_data as $country_id=>$rows)
		                  	 		{
		                  	 			if ($i%2==0)
		                  	 				$bgcolor="#E9F3FF";
		                  	 			else
		                  	 				$bgcolor="#FFFFFF";


										if($order_cnty_wise_ex_arr[$po_id][$country_id]["qnty"])
										{
											$gr_order_qnty+=$rows['cnty_qnty'];
											$gr_order_fob_value+=$rows['cnty_qnty']*$rows['unit_price'];
											$gr_ex_fac_qnty+= $order_cnty_wise_ex_arr[$po_id][$country_id]["qnty"];;
											$gr_ex_fac_fob+=$order_cnty_wise_ex_arr[$po_id][$country_id]["qnty"]*$rows['unit_price'];




			                   	 			?>
			                  	 			<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" >
			                  	 				<td align="center" width="30" style="word-wrap: break-word;word-break: break-all;"><? echo $i;?></td>
                                                <td align="center" width="100" style="word-wrap: break-word;word-break: break-all;"><? echo $buyer_arr[$rows['buyer_name']];?></td>
			                  	 				<td align="center" width="40" style="word-wrap: break-word;word-break: break-all;"><? echo $rows['job'];?></td>
			                  	 				<td align="center"  width="100" style="word-wrap: break-word;word-break: break-all;"><? echo $style_id;?></td>
			                  	 				<td align="center"  width="70" style="word-wrap: break-word;word-break: break-all;"><? echo $rows['po_number'];?></td>
			                  	 				<td align="center"  width="60" style="word-wrap: break-word;word-break: break-all;"><? echo $country_short_arr[$country_id];?></td>
			                  	 				<td align="center"  width="100" style="word-wrap: break-word;word-break: break-all;"><? echo $date1=change_date_format($rows['dates']);?></td>
			                  	 				<td  align="center" width="30" style="word-wrap: break-word;word-break: break-all;">
			                  	 					<?
			                  	 					$week_de= $no_of_week_for_header[$rows['dates']];

			                  	 					echo $week_de;
			                  	 					?>

			                  	 				</td>

			                  	 				<td align="center"  width="60" style="word-wrap: break-word;word-break: break-all;"><? echo $cut_up_array[$rows['cutup']]; ?></td>
			                  	 				<td align="center"  width="70" style="word-wrap: break-word;word-break: break-all;"><? echo $unit_of_measurement[$rows['order_uom']];?></td>
			                  	 				<td align="center"  width="60" style="word-wrap: break-word;word-break: break-all;"><? echo $rows['cnty_qnty'];?></td>
			                  	 				<td align="center"  width="30" style="word-wrap: break-word;word-break: break-all;"><? echo $rows['unit_price'];?></td>
			                  	 				<td align="center"  width="63" style="word-wrap: break-word;word-break: break-all;"><? echo $rows['cnty_qnty']*$rows['unit_price'];?> </td>
			                  	 				<td  align="center" width="100" style="word-wrap: break-word;word-break: break-all;"><? echo $order_cnty_wise_ex_arr[$po_id][$country_id]["qnty"];?></td>
			                  	 				<td align="center"  width="95" style="word-wrap: break-word;word-break: break-all;"><? echo $order_cnty_wise_ex_arr[$po_id][$country_id]["qnty"]*$rows['unit_price'];?></td>
			                  	 				<td  align="center" width="100" style="word-wrap: break-word;word-break: break-all;"><? echo $date2=  change_date_format($order_cnty_wise_ex_arr[$po_id][$country_id]["dates"]) ;
			                  	 				    $date1=strtotime($date1);
													$date2=strtotime($date2);
													$delay_count= ($date1-$date2)/86400;
													if(!$date2)
													{
														$delay_count="";
													}
													$background_color="";
													if($delay_count<0)
													{
														$background_color=" color:crimson;";
													}


													?></td>


			                  	 				<td   align="center" width="50" style="word-wrap: break-word;word-break: break-all;<? echo $background_color;?>">
			                  	 				<?
			                  	 					echo $delay_count;
			                  	 					$shiping_st=$order_cnty_wise_ex_arr[$po_id][$country_id]["shiping_status"];

			                  	 				?>

			                  	 				</td>
			                  	 				<td  align="center" width="35" style="word-wrap: break-word;word-break: break-all;"><? echo $order_cnty_wise_ex_arr[$po_id][$country_id]['shiping_mode']; ?></td>
			                  	 				<td  align="center" width="110" style="word-wrap: break-word;word-break: break-all;"><? echo $shiping_st;?></td>
			                  	 				<td  align="center" width="50" style="word-wrap: break-word;word-break: break-all;"><? echo $season_arrs[$rows['season_buyer_wise']];?></td>

			                  	 			</tr>


			                  	 			<?
			                  	 			$i++;
			                  	 		}

		                  	 		}

		                  	 	}

		                  	 }


		                  }
		                  ?>


					</table>



			</div>

			<table align="left" width="1420" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_footer"  >

						<tfoot>
						<tr>
		 					<th width="30" style="word-wrap: break-word;word-break: break-all;">&nbsp;</th>
                            <th width="100" style="word-wrap: break-word;word-break: break-all;">&nbsp;</th>
		 					<th width="40" style="word-wrap: break-word;word-break: break-all;">&nbsp;</th>
							<th width="100" style="word-wrap: break-word;word-break: break-all;">&nbsp;</th>
							<th width="70" style="word-wrap: break-word;word-break: break-all;">&nbsp;</th>
							<th width="60" style="word-wrap: break-word;word-break: break-all;">&nbsp;</th>
							<th width="100" style="word-wrap: break-word;word-break: break-all;">&nbsp;</th>
							<th width="30" style="word-wrap: break-word;word-break: break-all;">&nbsp;</th>
							<th width="60" style="word-wrap: break-word;word-break: break-all;">&nbsp;</th>
							<th width="70" style="word-wrap: break-word;word-break: break-all;"><strong>Grand Total</strong></th>
							<th   id="gr_order_qnty_id" width="60" style="word-wrap: break-word;word-break: break-all;"><strong> </strong></th>
							<th width="30" style="word-wrap: break-word;word-break: break-all;">&nbsp;</th>
							<th align="center"  id="gr_order_fob_id"  width="63" style="word-wrap: break-word;word-break: break-all;"><strong></strong> </th>
							<th align="center"  id="gr_ex_qnty_id"  width="100" style="word-wrap: break-word;word-break: break-all;"><strong></strong></th>
							<th align="center"  id="gr_ex_fob_id"  width="95" style="word-wrap: break-word;word-break: break-all;"><strong></strong></th>
							<th width="100" style="word-wrap: break-word;word-break: break-all;">&nbsp;</th>
							<th width="50" style="word-wrap: break-word;word-break: break-all;">&nbsp;</th>
							<th width="35" style="word-wrap: break-word;word-break: break-all;">&nbsp;</th>
							<th width="110" style="word-wrap: break-word;word-break: break-all;">&nbsp;</th>
							<th width="50" style="word-wrap: break-word;word-break: break-all;">&nbsp;</th>


						</tr>

						</tfoot>
					</table>



		</div>

		<?

	}
	else if($reportType==5) // FOR MICROFIBER (Country wise 2) tmp done
	{
		if($source_cond) $source_cond2=str_replace("d.","a.",$source_cond);
		$challan_mst_arr=array();
		$details_report .='<table width="4220" cellspacing="0" cellpadding="0"  border="1"  class="rpt_table" rules="all" id="table_body2">';
		$i=1;
		$sql= "SELECT A.id AS EX_ID, A.LC_SC_NO AS LC_SC_ARR_NO, A.INVOICE_NO AS INVOICE_NO, A.ITEM_NUMBER_ID AS ITM_NUM_ID, A.SHIPING_MODE AS SHIPING_MODE, A.DELIVERY_MST_ID AS CHALLAN_ID, A.IS_POSTED_ACCOUNT, A.ENTRY_FORM, A.EX_FACTORY_QNTY, A.TOTAL_CARTON_QNTY AS CARTON_QNTY, A.EX_FACTORY_DATE AS EX_FACTORY_DATE, d.SYS_NUMBER,

		B.ID AS PO_ID, B.GROUPING, B.SHIPMENT_DATE, B.PO_NUMBER, c.id as job_id, C.COMPANY_NAME, C.BUYER_NAME, C.JOB_NO_PREFIX_NUM, C.JOB_NO, C.SHIP_MODE, TO_CHAR(C.INSERT_DATE,'YYYY') AS YEAR, C.STYLE_REF_NO, C.STYLE_DESCRIPTION, C.SET_SMV, c.TOTAL_SET_QNTY, c.SET_BREAK_DOWN, C.ORDER_UOM,
		D.DELIVERY_FLOOR_ID AS DEL_FLOOR, D.DELIVERY_COMPANY_ID AS DEL_COMPANY, D.SOURCE, D.DELIVERY_LOCATION_ID AS DEL_LOCATION, D.LOCK_NO, D.FORWARDER, D.TRUCK_NO, D.DRIVER_NAME, D.MOBILE_NO, D.DL_NO,
		E.COUNTRY_ID, E.ORDER_QUANTITY AS PO_QUANTITY, (E.ORDER_RATE) AS UNIT_PRICE, E.SHIPING_STATUS, F.current_invoice_qnty as INVOICE_QUANTITY

		from wo_po_break_down b, wo_po_details_master c, pro_ex_factory_delivery_mst d,wo_po_color_size_breakdown e,pro_ex_factory_mst a
		left join com_export_invoice_ship_dtls f on a.invoice_no=f.mst_id and a.po_break_down_id=f.po_breakdown_id and f.is_deleted=0 and f.status_active=1
		where a.po_break_down_id=b.id and b.job_id=c.id and c.id=e.job_id and b.id=e.po_break_down_id  and a.delivery_mst_id=d.id  $company_cond $str_cond $del_comp_cond $del_location_cond $del_floor_cond $shiping_status_cond $source_cond $buyer_conds $internal_ref_cond $search_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in (1,2,3) and c.is_deleted=0 and c.status_active in (1,2,3) and e.status_active in (1,2,3) and e.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and A.ex_factory_qnty>0 and E.ORDER_QUANTITY>0 and A.ENTRY_FORM!=85
		order by a.ex_factory_date";//and a.item_number_id=e.item_number_id and e.country_id=a.country_id

		// echo $sql;die;
		$sql_result=sql_select($sql); $all_po_id_arr = array(); $job_id_arr = array(); $invoice_id_arr = array(); $lc_sc_id_arr = array();
		foreach($sql_result as $val)
		{
			$all_po_id_arr[$val['PO_ID']]=$val['PO_ID'];
			$job_id_arr[$val['JOB_ID']]=$val['JOB_ID'];
			if($val['INVOICE_NO']){ $invoice_id_arr[$val['INVOICE_NO']]=$val['INVOICE_NO']; }
			if($val['LC_SC_ARR_NO']){ $lc_sc_id_arr[$val['LC_SC_ARR_NO']]=$val['LC_SC_ARR_NO']; }
		}

		$con = connect();
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (12) and ENTRY_FORM=17");
		oci_commit($con);

		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 17, 12, $all_po_id_arr, $empty_arr);//Po ID
		disconnect($con);


		$job_id_cond = where_con_using_array($job_id_arr,0,'job_id');

		$invoice_id_cond = where_con_using_array($invoice_id_arr,0,'id');
		$lc_sc_id_cond = where_con_using_array($lc_sc_id_arr,0,'id');

		$comSql="select id, invoice_no, shipping_mode, is_lc, lc_sc_id from com_export_invoice_ship_mst where status_active=1 $invoice_id_cond";
		$comSqlRes=sql_select($comSql); $invoice_array= array(); $shipping_mode_array= array(); $lc_sc_type_arr= array(); $lc_sc_id_array= array();
		foreach($comSqlRes as $row)
		{
			$invoice_array[$row[csf('id')]]=$row[csf('invoice_no')];
			$shipping_mode_array[$row[csf('id')]]=$row[csf('shipping_mode')];
			$lc_sc_type_arr[$row[csf('id')]]=$row[csf('is_lc')];
			$lc_sc_id_array[$row[csf('id')]]=$row[csf('lc_sc_id')];
		}
		unset($comSqlRes);

		$tot_cost_arr = return_library_array("select job_no, cm_cost from wo_pre_cost_dtls where status_active=1 $job_id_cond","job_no","cm_cost");

		$inspection_date_arr=return_library_array( "select b.po_break_down_id, max(b.inspection_date) as inspection_date from pro_buyer_inspection a, gbl_temp_engine d where status_active=1 and is_deleted=0 and b.po_break_down_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=17 and d.ref_from=11 group by b.po_break_down_id", "po_break_down_id", "inspection_date");

		$lc_num_arr=return_library_array( "select id,export_lc_no from com_export_lc where status_active=1 $lc_sc_id_cond", "id", "export_lc_no"  );
		$sc_num_arr=return_library_array( "select id,contract_no from com_sales_contract where status_active=1 $lc_sc_id_cond", "id", "contract_no"  );

		$forwarder_arr=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name");
		// ======================================================================


		$data_array = array(); $data_array_details=array(); $all_challan_id_arr=array(); $po_wise_buyer_arr=array(); $po_wise_unit_price_arr=array();
		foreach($sql_result as $val)
		{
			$data_array[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']]['company_name'] = $val['COMPANY_NAME'];
			$data_array[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']]['style_ref_no'] = $val['STYLE_REF_NO'];
			$data_array[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']]['style_description'] = $val['STYLE_DESCRIPTION'];
			$data_array[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']]['year'] = $val['YEAR'];
			$data_array[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']]['total_set_qnty'] = $val['TOTAL_SET_QNTY'];
			$data_array[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']]['set_break_down'] = $val['SET_BREAK_DOWN'];
			$data_array[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']]['buyer_name'] = $val['BUYER_NAME'];
			$data_array[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']]['job_no_prefix_num'] = $val['JOB_NO_PREFIX_NUM'];
			$data_array[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']]['unit_price'] = $val['UNIT_PRICE'];
			$data_array[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']]['grouping'] = $val['GROUPING'];
			$data_array[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']]['job_no'] = $val['JOB_NO'];
			if($lc_sc_type_arr[$val['INVOICE_NO']]==1)//lc
			{
				$data_array[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']]['lc_sc_arr_no'] .= $lc_num_arr[$val['LC_SC_ARR_NO']].",";
			}
			else
			{
				$data_array[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']]['lc_sc_arr_no'] .= $sc_num_arr[$val['LC_SC_ARR_NO']].",";
			}

			$data_array[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']]['invoice_no'] .= $invoice_array[$val['INVOICE_NO']].",";
			$data_array[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']]['item_name'] .= $garments_item[$val['ITM_NUM_ID']].",";
			$data_array[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']]['itm_num_id'] .= $val['ITM_NUM_ID'].",";
			$data_array[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']]['challan_id'] = $val['CHALLAN_ID'];
			$data_array[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']]['lock_no'] .= $val['LOCK_NO'].",";
			$data_array[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']]['shipment_date'] = $val['SHIPMENT_DATE'];
			$data_array[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']]['source'] = $val['SOURCE'];
			$data_array[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']]['po_number'] = $val['PO_NUMBER'];
			$data_array[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']]['order_uom'] = $val['ORDER_UOM'];
			$data_array[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']]['ship_mode'] = $val['SHIP_MODE'];
			$data_array[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']]['shiping_status'] = $val['SHIPING_STATUS'];
			$data_array[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']]['sys_number'] .= $val['SYS_NUMBER'].",";
			$data_array[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']]['rtn_sys_number'] .= $val['RTN_SYS_NUMBER'].",";
			$data_array[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']]['del_company'] = $val['DEL_COMPANY'];
			$data_array[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']]['del_location'] .= $location_library[$val['DEL_LOCATION']].",";
			$data_array[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']]['del_floor'] .= $floor_library[$val['DEL_FLOOR']].",";
			$data_array[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']]['forwarder'] .= $forwarder_arr[$val['FORWARDER']].",";
			$data_array[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']]['truck_no'] .= $val['TRUCK_NO'].",";
			$data_array[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']]['driver_name'] .= $val['DRIVER_NAME'].",";
			$data_array[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']]['mobile_no'] .= $val['MOBILE_NO'].",";
			$data_array[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']]['dl_no'] .= $val['DL_NO'].",";
			$data_array[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']]['shiping_mode'] = $val['SHIPING_MODE'];

			$data_array[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']]['ex_factory_date'] = $val['EX_FACTORY_DATE'];
			$data_array[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']]['carton_qnty'] += $val['CARTON_QNTY'];

			if($val['ENTRY_FORM']!=85)
			{
				$data_array[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']]['ex_factory_qnty'] += $val['EX_FACTORY_QNTY'];
				$data_array_details[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']][$val['CHALLAN_ID']]['sys_number'] = $val['SYS_NUMBER'];
				$data_array_details[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']][$val['CHALLAN_ID']]['ex_factory_qnty'][$val['EX_ID']] += $val['EX_FACTORY_QNTY'];
			}
			else if($val['ENTRY_FORM']==85)
			{
				$data_array[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']]['ex_factory_rtn_qnty'] += $val['EX_FACTORY_QNTY'];
				$data_array_details[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']][$val['CHALLAN_ID']]['rtn_sys_number'] = $val['SYS_NUMBER'];
				$data_array_details[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']][$val['CHALLAN_ID']]['ex_factory_rtn_qnty'][$val['EX_ID']] += $val['EX_FACTORY_QNTY'];
			}

			$data_array_details[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']][$val['CHALLAN_ID']]['invoice_no'] .= $invoice_array[$val['INVOICE_NO']].",";
			$data_array_details[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']][$val['CHALLAN_ID']]['invoice_qnty'][$val['INVOICE_NO']] = $val['INVOICE_QUANTITY'];
			$data_array_details[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']][$val['CHALLAN_ID']]['is_posted_account'] = $val['IS_POSTED_ACCOUNT'];

			$all_challan_id_arr[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']][$val['CHALLAN_ID']]++;

			$po_wise_buyer_arr[$val['PO_ID']]=$val['BUYER_NAME'];
			$po_wise_unit_price_arr[$val['PO_ID']]=$val['UNIT_PRICE'];
		}
		 //echo "<pre>";print_r($data_array_details);die();
		// ================================ order qty ============================
		$order_qnty_sql="SELECT a.COUNTRY_ID, a.PO_BREAK_DOWN_ID, a.COUNTRY_SHIP_DATE, sum(a.order_quantity) as ORDER_QUANTITY from wo_po_color_size_breakdown a, gbl_temp_engine d where a.status_active  in(1,2,3) and a.is_deleted=0 and a.po_break_down_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=17 and d.ref_from=12  group by a.COUNTRY_ID, a.po_break_down_id, a.COUNTRY_SHIP_DATE";
		 //echo $order_qnty_sql;die();
		$order_country_qnty_arr = array();
		$country_ship_date_arr = array();
		foreach(sql_select($order_qnty_sql) as $key=>$val)
		{
			$order_country_qnty_arr[$val["PO_BREAK_DOWN_ID"]][$val["COUNTRY_ID"]]+=$val["ORDER_QUANTITY"];
			$country_ship_date_arr[$val["PO_BREAK_DOWN_ID"]][$val["COUNTRY_ID"]]=$val["COUNTRY_SHIP_DATE"];
		}


		$unit_qnty_sql="SELECT a.COUNTRY_ID, a.PO_BREAK_DOWN_ID, a.JOB_NO_MST, sum(a.ORDER_TOTAL) as ORDER_TOTAL, sum(a.ORDER_QUANTITY) AS ORDER_QUANTITY from wo_po_color_size_breakdown a, gbl_temp_engine d where a.status_active  in(1,2,3) and a.is_deleted=0 and a.po_break_down_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=17 and d.ref_from=12  group by a.COUNTRY_ID, a.PO_BREAK_DOWN_ID, a.JOB_NO_MST";
		 //echo $unit_qnty_sql;die();
		$unit_price_country_qnty_arr = array();
		$unit_price_country_qnty_arr2 = array();
		foreach(sql_select($unit_qnty_sql) as $key=>$val)
		{
			$unit_price_country_qnty_arr[$val["JOB_NO_MST"]][$val["PO_BREAK_DOWN_ID"]][$val["COUNTRY_ID"]]=$val["ORDER_TOTAL"]/$val["ORDER_QUANTITY"];
			$unit_price_country_qnty_arr2[$val["PO_BREAK_DOWN_ID"]][$val["COUNTRY_ID"]]=($val["ORDER_QUANTITY"]>0) ? $val["ORDER_TOTAL"]/$val["ORDER_QUANTITY"] : 0;
		}

		/*$order_qnty_sql="SELECT a.JOB_NO_MST, a.COUNTRY_ID, a.PO_BREAK_DOWN_ID, a.COUNTRY_SHIP_DATE, a.order_quantity as ORDER_QUANTITY, a.ORDER_TOTAL from wo_po_color_size_breakdown a, gbl_temp_engine d where a.status_active  in(1,2,3) and a.is_deleted=0 and a.po_break_down_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=17 and d.ref_from=12 ";
		 //echo $order_qnty_sql;die();
		$order_country_qnty_arr = array(); $country_ship_date_arr = array(); $unit_price_country_qnty_arr = array(); $unit_price_country_qnty_arr2 = array();
		foreach(sql_select($order_qnty_sql) as $key=>$val)
		{
			$order_country_qnty_arr[$val["PO_BREAK_DOWN_ID"]][$val["COUNTRY_ID"]]+=$val["ORDER_QUANTITY"];
			$country_ship_date_arr[$val["PO_BREAK_DOWN_ID"]][$val["COUNTRY_ID"]]=$val["COUNTRY_SHIP_DATE"];
			$unit_price_country_qnty_arr[$val["JOB_NO_MST"]][$val["PO_BREAK_DOWN_ID"]][$val["COUNTRY_ID"]]+=$val["ORDER_TOTAL"]/$val["ORDER_QUANTITY"];
			$unit_price_country_qnty_arr2[$val["PO_BREAK_DOWN_ID"]][$val["COUNTRY_ID"]]=($val["ORDER_QUANTITY"]>0) ? $val["ORDER_TOTAL"]/$val["ORDER_QUANTITY"] : 0;
		}*/


		// ================================ ex-factory qty ============================
		if($txt_date_from!="" && $txt_date_to!="")
		{
			$get_carton_qty="sum(case when a.ex_factory_date between '$txt_date_from' and '$txt_date_to' then TOTAL_CARTON_QNTY else 0 end) AS CARTON_QNTY,";
		}
		else
		{
			$get_carton_qty="sum(a.TOTAL_CARTON_QNTY) AS CARTON_QNTY,";
		}
		$str_cond = str_replace("a.", "", $str_cond);
		$sql_ex="SELECT a.COUNTRY_ID, a.PO_BREAK_DOWN_ID,a.CHALLAN_NO,
		sum(CASE WHEN a.ENTRY_FORM!=85 THEN a.EX_FACTORY_QNTY ELSE 0 END) AS TOT_EX_FACTORY_QNTY,
		sum(CASE WHEN a.ENTRY_FORM!=85 $str_cond THEN a.EX_FACTORY_QNTY ELSE 0 END) AS EX_FACTORY_QNTY,
		sum(CASE WHEN a.ENTRY_FORM=85 THEN a.EX_FACTORY_QNTY ELSE 0 END) AS TOT_EX_FACTORY_RTN_QNTY,
		sum(CASE WHEN a.ENTRY_FORM=85  THEN a.EX_FACTORY_QNTY ELSE 0 END) AS EX_FACTORY_RTN_QNTY,
		$get_carton_qty
		sum(a.TOTAL_CARTON_QNTY) AS TOT_CARTON_QNTY
		from pro_ex_factory_mst a, gbl_temp_engine d
		where a.status_active in(1,2,3) and a.is_deleted=0 and a.po_break_down_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=17 and d.ref_from=12
		group by a.country_id, a.po_break_down_id,a.CHALLAN_NO";//$str_cond
		//    echo $sql_ex;die();
		$exfact_country_qnty_arr = array();
		//$country_ship_date_arr = array();
		foreach(sql_select($sql_ex) as $val)
		{
			$exfact_country_qnty_arr[$val["PO_BREAK_DOWN_ID"]][$val["COUNTRY_ID"]]['tot_ex']+=$val["TOT_EX_FACTORY_QNTY"];
			$exfact_country_qnty_arr[$val["PO_BREAK_DOWN_ID"]][$val["COUNTRY_ID"]]['ex']+=$val["EX_FACTORY_QNTY"];
			$exfact_country_qnty_arr[$val["PO_BREAK_DOWN_ID"]][$val["COUNTRY_ID"]][$val["CHALLAN_NO"]]['tot_ex_rtn']+=$val["TOT_EX_FACTORY_RTN_QNTY"];
			$exfact_country_qnty_arr[$val["PO_BREAK_DOWN_ID"]][$val["COUNTRY_ID"]][$val["CHALLAN_NO"]]['ex_rtn']+=$val["EX_FACTORY_RTN_QNTY"];
			$exfact_country_qnty_arr[$val["PO_BREAK_DOWN_ID"]][$val["COUNTRY_ID"]]['carton_qnty']+=$val["CARTON_QNTY"];
			$exfact_country_qnty_arr[$val["PO_BREAK_DOWN_ID"]][$val["COUNTRY_ID"]]['tot_carton_qnty']+=$val["TOT_CARTON_QNTY"];

			if($val["EX_FACTORY_QNTY"]-$val["EX_FACTORY_RTN_QNTY"] && $unit_price_country_qnty_arr2[$val["PO_BREAK_DOWN_ID"]][$val["COUNTRY_ID"]]>0)
			{
				$master_data[$po_wise_buyer_arr[$val["PO_BREAK_DOWN_ID"]]]['ex_factory_value'] +=($val["EX_FACTORY_QNTY"]-$val["EX_FACTORY_RTN_QNTY"])*$unit_price_country_qnty_arr2[$val["PO_BREAK_DOWN_ID"]][$val["COUNTRY_ID"]];
			}
		}

			// echo "<pre>";print_r($exfact_country_qnty_arr);die();

		foreach ($data_array as $job_key => $job_data)
		{
			foreach ($job_data as $po_key => $po_data)
			{
				foreach ($po_data as $country_key => $row)
				{
					$set_break_down=explode("__", $row["set_break_down"]);
					foreach($set_break_down as $k=>$v)
					{
						if($v)
						{
							$val=explode("_", $v);
							// echo $val[0]."==".implode(",",array_unique(array_filter(explode(",", $row["itm_num_id"]))));die();
							if( trim($val[0])== implode(",",array_unique(array_filter(explode(",", $row["itm_num_id"])))))
							{
								$item_smv=$val[2];
							}
						}
					}
					$master_data[$row["buyer_name"]]['b_id']=$row["buyer_name"];
		  			$master_data[$row["buyer_name"]]['org_po_qnty'] +=$order_country_qnty_arr[$po_key][$country_key];

					$master_data[$row["buyer_name"]]['po_qnty'] +=$order_country_qnty_arr[$po_key][$country_key];
		 	  		//$master_data[$row["buyer_name"]]['po_value'] +=$order_country_qnty_arr[$po_key][$country_key]*$row["unit_price"];
		 	 		//echo $order_country_qnty_arr[$po_key][$country_key].'-'.$unit_price_country_qnty_arr[$job_key][$po_key][$country_key].'<br>';
				  	if($order_country_qnty_arr[$po_key][$country_key]>0){
					    $master_data[$row["buyer_name"]]['po_value'] +=($order_country_qnty_arr[$po_key][$country_key]*$unit_price_country_qnty_arr[$job_key][$po_key][$country_key]);
				  	}

					$ex_rtn_qty = 0;
					$tot_ex_rtn_qty = 0;
					foreach($data_array_details[$job_key][$po_key][$country_key] as $challan_no=>$val)
					{
						$ex_rtn_qty += $exfact_country_qnty_arr[$po_key][$country_key][$val['sys_number']]['ex_rtn'];
						$tot_ex_rtn_qty += $exfact_country_qnty_arr[$po_key][$country_key][$val['sys_number']]['tot_ex_rtn'];
					}

		 	  		$total_po_qty+=$order_country_qnty_arr[$po_key][$country_key];
		 	  		//$total_po_valu+=$order_country_qnty_arr[$po_key][$country_key]*$row["unit_price"];
		 	  		$total_po_valu+=($order_country_qnty_arr[$po_key][$country_key]*$unit_price_country_qnty_arr[$job_key][$po_key][$country_key]);
					$master_data[$row["buyer_name"]]['basic_qnty'] +=$basic_qnty;
					// $master_data[$row["buyer_name"]]['ex_factory_qnty'] +=$exfact_country_qnty_arr[$po_key][$country_key]['ex']-$exfact_country_qnty_arr[$po_key][$country_key]['ex_rtn'];
					$master_data[$row["buyer_name"]]['ex_factory_qnty'] +=$exfact_country_qnty_arr[$po_key][$country_key]['ex']-$ex_rtn_qty;

					// $master_data[$row["buyer_name"]]['total_ex_fact_qty'] +=$exfact_country_qnty_arr[$po_key][$country_key]['tot_ex']-$exfact_country_qnty_arr[$po_key][$country_key]['tot_ex_rtn'];
					$master_data[$row["buyer_name"]]['total_ex_fact_qty'] +=$exfact_country_qnty_arr[$po_key][$country_key]['tot_ex']-$ex_rtn_qty;
					// $master_data[$row["buyer_name"]]['total_ex_fact_value'] +=($exfact_country_qnty_arr[$po_key][$country_key]['tot_ex']-$exfact_country_qnty_arr[$po_key][$country_key]['tot_ex_rtn'])*$row["unit_price"];
					$master_data[$row["buyer_name"]]['total_ex_fact_value'] +=($exfact_country_qnty_arr[$po_key][$country_key]['tot_ex']-$tot_ex_rtn_qty)*$row["unit_price"];
					$master_data[$row["buyer_name"]]['sales_min'] += $item_smv*$exfact_country_qnty_arr[$po_key][$country_key]['ex'];
					// $master_data[$row["buyer_name"]]['ex_factory_rtn_qnty'] += $exfact_country_qnty_arr[$po_key][$country_key]['ex_rtn'];
					$master_data[$row["buyer_name"]]['ex_factory_rtn_qnty'] += $ex_rtn_qty;
				}
			}
		}

		$acc_po_arr=array();
		$sqlselectnew = sql_select("SELECT a.ID, a.JOB_NO, a.PO_BREAK_DOWN_ID, a.ACC_PO_NO, a.COUNTRY_ID
		FROM wo_po_acc_po_info a, gbl_temp_engine d WHERE a.STATUS_ACTIVE=1 and a.po_break_down_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=17 and d.ref_from=12");
		foreach($sqlselectnew as $val){
			$acc_po_arr[$val["JOB_NO"]][$val["PO_BREAK_DOWN_ID"]][$val["COUNTRY_ID"]]=$val["ACC_PO_NO"];
		}

		$con = connect();
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from=12 and ENTRY_FORM=17");
		oci_commit($con);
		disconnect($con);
		?>

        <fieldset style="width:3100x;">
            <div style="width:1420px;float: left;" >
                <table width="1390"  cellspacing="0"  align="left">
                    <tr>
                        <td align="center" colspan="13" class="form_caption">
                            <strong style="font-size:16px;">Company Name:<? echo  $company_library[$cbo_company_name] ;?></strong>
                        </td>
                    </tr>
                    <tr class="form_caption">
                        <td colspan="13" align="center" class="form_caption"> <strong style="font-size:15px;">Ex-Factory Report</strong></td>
                    </tr>
                     <tr align="center">
                        <td colspan="13" align="center" class="form_caption"> <strong style="font-size:15px;">Total Summary</strong></td>
                    </tr>
                    </table>
                    <table width="1390" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1">
                    <thead>
                        <th width="40" height="34">SL</th>
                        <th width="130">Buyer Name</th>
                        <th width="100">PO Qty</th>
                        <th width="100">PO Qty (pcs)</th>
                        <th width="130">PO Value</th>
                        <th width="100">PO Value(%)</th>
                        <th width="100">Current Ex-Fact. Qty.</th>
                        <th width="130">Current Ex-Fact. Value</th>
                        <th width="100">Total Ex-Fact. Qty.</th>
                        <th width="130">Total Ex-Fact. Value </th>
                        <th width="80">Ex-Fact. Rtn Qty </th>
                        <th width="100">Sales Minutes</th>
                        <th width="100">Total Ex-Fact. (Basic Qty)</th>
                        <th >Total Ex-Fact. Value %</th>
                    </thead>
                </table>
                <table width="1390" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body">
                <?
                // echo "<pre>";
                // print_r($master_data);
                // echo "</pre>";
                foreach($master_data as $rows)
				{
					$total_po_val+=$rows["po_value"];
				}
                $m=1;
                $i=1;
                $grand_sales_minute =0;
                foreach($master_data as $rows)
                {
                    if ($i%2==0)
                    $bgcolor="#E9F3FF";
                    else
                    $bgcolor="#FFFFFF";
                     ?>
                  	<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" >
                        <td width="40" align="center"><? echo $m; ?></td>
                        <td width="130">
                        <p><?
                        echo $buyer_arr[$rows[b_id]]. $master_data[$rows[b_id]]['in_sub'];
                        ?></p>
                        </td>
                        <td width="100" align="right"><p><?  $po_quantity_org=$rows["org_po_qnty"];echo number_format($po_quantity_org,0); $total_buyer_org_po_quantity+=$po_quantity_org; ?></p></td>
                        <td width="100" align="right"><p><?  $po_quantity=$rows[po_qnty];echo number_format($po_quantity,0); $total_buyer_po_quantity+=$po_quantity; ?></p></td>

                        <td width="130" align="right" ><p  id="value_<? echo $i ; ?>"><? $buyer_po_value=$rows["po_value"]; echo number_format($buyer_po_value,2 ,'.', '');  $total_buyer_po_value+=$buyer_po_value; ?></p></td>

                        <td width="100" align="right">
                         	<?
							 if($buyer_po_value>0 && $total_po_val>0){
								echo number_format(($buyer_po_value/$total_po_val)*100,2,'.','');$parcentages+=($buyer_po_value/$total_po_val)*100;
							 }else{
								echo 0.00;
							 }

							?>
                        </td>

                        <td width="100" align="right">
                        <p><?
                         $current_ex_Fact_Qty=$rows["ex_factory_qnty"];  echo number_format($current_ex_Fact_Qty,0,'',''); $total_current_ex_Fact_Qty +=$current_ex_Fact_Qty;
                        ?></p>
                        </td>
                        <td width="130" align="right">
                        <p><?
                        $current_ex_fact_value=$rows[ex_factory_value]; echo number_format($current_ex_fact_value,2,'.',''); $total_current_ex_fact_value+=$current_ex_fact_value;
                        ?></p>
                        </td>
                        <td align="right" width="100">
                        <p><?
                         $total_ex_fact_qty=$rows[total_ex_fact_qty]; echo number_format($total_ex_fact_qty,0,'',''); $mt_total_ex_fact_qty+=$total_ex_fact_qty;
                        ?></p>
                        </td>
                        <td align="right" width="130">
                        <p><?
                         $total_ex_fact_value=$rows[total_ex_fact_value];  echo  number_format($total_ex_fact_value,2,'.',''); $mt_total_ex_fact_value+=$total_ex_fact_value;
                        ?></p>
                        </td>
                        <td align="right" width="80">
                        <p><?
                         $total_ex_factory_rtn_qnty=$rows[ex_factory_rtn_qnty];  echo  number_format($total_ex_factory_rtn_qnty,0,'.',''); $mt_total_ex_factory_rtn_qnty+=$total_ex_factory_rtn_qnty;
                        ?></p>
                        </td>
                        <td width="100" align="right"><p>
                        	<?
                        	echo $g_sales_min+= number_format($rows["sales_min"],0,'','');

                        	?>
                        </p></td>
                        <td width="100" align="right">
                        <p><?
                         $buyer_basic_qnty=$rows["basic_qnty"];  echo number_format($buyer_basic_qnty,0,'',''); $total_buyer_basic_qnty +=$buyer_basic_qnty;
                        ?></p>
                        </td>

                        <td align="right">
                        <p><?
						if($total_ex_fact_value>0 && $buyer_po_value>0){
							$total_ex_fact_value_parcentage=($total_ex_fact_value/$buyer_po_value)*100;
							echo number_format($total_ex_fact_value_parcentage,0);
						}else{
							echo 0.00;
						}

                        ?> %</p>
                        </td>
                    </tr>
                    <?
                    $i++;$m++;
                    $buyer_po_quantity=0;
                    $buyer_po_value=0;
                    $current_ex_Fact_Qty=0;
                    $current_ex_fact_value=0;
                    $total_ex_fact_qty=0;
                    $total_ex_fact_value=0;
                    $g_sales_min=0;
                    $grand_sales_minute +=number_format($rows["sales_min"],0,'','');

                }
                    ?>
                    <input type="hidden" name="total_i" id="total_i" value="<? echo $i; ?>" />
                    <tfoot>
                        <th align="right" colspan="2"><b>Total:</b></th>
                        <th  align="right" id="total_buyer_org_po_quantity"><? echo number_format($total_buyer_org_po_quantity,0);  ?></th>
                        <th  align="right" id="total_buyer_po_quantity"><? echo number_format($total_buyer_po_quantity,0);  ?></th>
                        <th  align="right" id="value_total_buyer_po_value"><? echo number_format($total_buyer_po_value,2 ,'.', ''); ?> </th>
                        <th align="right" id="parcentages"><? echo ceil($parcentages); ?></th>
                        <th  align="right" id="total_current_ex_Fact_Qty"><? echo number_format($total_current_ex_Fact_Qty,0); ?></th>
                        <th  align="right" id="value_total_current_ex_fact_value"><? echo  number_format($total_current_ex_fact_value,2); ?></th>
                        <th align="right" id="mt_total_ex_fact_qty"><? echo number_format($mt_total_ex_fact_qty,0); ?></th>
                        <th align="right" id="value_mt_total_ex_fact_value"><? echo number_format($mt_total_ex_fact_value,2); ?></th>
                        <th align="right" id="value_mt_total_ex_fact_value"><? echo number_format($mt_total_ex_factory_rtn_qnty,0); ?></th>
                        <th align="right"><? echo number_format($grand_sales_minute ,2); ?></th>
                        <th  align="right" id="total_buyer_basic_qnty"><? echo number_format($total_buyer_basic_qnty,0); ?></th>
                        <th align="right"></th>
                    </tfoot>
                </table>
            </div>
            <!-- =================================================================================
            /										Details part								  /
            /================================================================================== -->
            <div>
            	<style type="text/css">
            		.rpt_table tr td{vertical-align: middle;}
            	</style>
                <table width="4580"  >
                    <tr>
                    <td colspan="47" class="form_caption"><strong style="font-size:16px;">Shipped Out Order Details Report</strong></td>
                    </tr>
                </table>
                <table width="4705" border="1" class="rpt_table" rules="all" id="table_header_2" align="left">
                    <thead>
						<tr>
							<th width="40">SL</th>
							<th width="150">Company</th>
							<th width="60">Job</th>
							<th width="60">Year</th>
							<th width="100">Buyer Name</th>
							<th width="110">Order NO</th>
							<th width="125">Actual PO</th>
							<th width="125">Country</th>
							<th width="110">Internal ref.</th>
							<th width="125">Del Company</th>
							<th width="125">Del Location</th>
							<th width="125">Del Floor</th>
							<th width="120">Challan NO</th>
							<th width="100" >Challan Qty</th>
							<th width="100" >Invoice NO</th>
							<th width="100" >Invoice Qty</th>
							<th width="100" >Accounting Posting</th>
							<th width="100" >LC/SC NO</th>
							<th width="100">Style Ref. no.</th>
							<th width="100">Style Description</th>
							<th width="110">Item Name</th>
							<th width="80">Item SMV</th>
							<th width="70">Country Shipment Date</th>
							<th width="70">Shipment Date</th>
							<th width="70">Ex-Fac. Date</th>
							<th width="100"><p>Po Rcv.Ship Mode</p></th>
							<th width="70">Shipping Mode</th>
							<th width="60">Days in Hand</th>
							<th width="100">UOM</th>
							<th width="80">PO/Country Qty. (pcs)</th>
							<th width="70">Unit Price</th>
							<th width="100">PO Value</th>
							<th width="80">Current Ex-Fact. Qty (pcs)</th>
							<th width="100">Current Ex-Fact. Value</th>
							<th width="80">Current Carton Qty</th>
							<th width="80">Total Ex-Fact. Qty.</th>
							<th width="100">Total Ex-Fact. Value</th>
							<th width="60">Ex-Fact. Rtn Qty</th>
							<th width="80">Total Carton Qty</th>
							<th width="100">Sales Minute</th>
							<th width="80">Total Ex-Fact. (Basic Qty)</th>
							<th width="80">Excess/ Shortage Qty</th>
							<th width="100">Excess/ Shortage Value</th>
							<th width="80">Total Ex-Fact. Qty. %</th>
							<th width="60">Sales CM</th>
							<th width="100">C & F Name</th>
							<th width="80">Vehicle No</th>
							<th width="80">Lock No</th>
							<th width="130">Driver Info</th>
							<th width="70">Inspection Date</th>
							<th>Ex-Fact Status</th>
						</tr>
                    </thead>
                </table>
            <div style="width:4705px; overflow-y:scroll; overflow-x:hidden; max-height:300px;"  id="scroll_body" >
            	<table width="4705" border="1" class="rpt_table" rules="all" id="table_header_2">
            		<tbody>
            			<?
            			$i +=$i;
		                $total_po_qty=0;
		                $total_po_valu=0;
		                $total_ex_qty=0;
		                $total_ex_valu=0;
		                $total_crtn_qty=0;
		                $g_total_ex_qty=0;
		                $g_total_ex_val=0;
		                $g_total_ex_rtn_qty=0;
		                $g_total_ex_crtn=0;
		                $g_sales_minutes=0;
		                $total_basic_qty=0;
		                $total_eecess_storage_qty=0;
		                $total_eecess_storage_val=0;
		                $gr_ttl_ex_fac_per=0;
		                $cm_per_pcs_tot=0;
            			foreach ($data_array as $job_key => $job_data)
            			{
			                $job_total_po_qty=0;
			                $job_total_po_valu=0;
			                $job_total_ex_qty=0;
			                $job_total_ex_valu=0;
			                $job_total_crtn_qty=0;
			                $job_total_ex_rtn_qty=0;
			                $job_g_total_ex_qty=0;
			                $job_g_total_ex_val=0;
			                $job_g_total_ex_crtn=0;
			                $job_g_sales_minutes=0;
			                $job_total_basic_qty=0;
			                $job_total_eecess_storage_qty=0;
			                $job_total_eecess_storage_val=0;
			                $job_gr_ttl_ex_fac_per=0;
			                $job_cm_per_pcs_tot=0;
							$job_total_invoice_qnty=$job_total_ex_factory_qnty=0;

            				foreach ($job_data as $po_key => $po_data)
            				{
            					foreach ($po_data as $country_key => $row)
            					{
            						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									$rowspan=count($all_challan_id_arr[$job_key][$po_key][$country_key]);
            						$costing_per=$costing_per_arr[$job_key];
									if($costing_per==1) $dzn_qnty=12;
									else if($costing_per==3) $dzn_qnty=12*2;
									else if($costing_per==4) $dzn_qnty=12*3;
									else if($costing_per==5) $dzn_qnty=12*4;
									else $dzn_qnty=1;
									$dzn_qnty2=$dzn_qnty ;
									$dzn_qnty=$dzn_qnty*$row['total_set_qnty'];
									$cm_per_pcs=$tot_cost_arr[$job_key]/$dzn_qnty2;

								    $set_break_down=explode("__", $row["set_break_down"]);
									foreach($set_break_down as $k=>$v)
									{
										if($v)
										{
											$val=explode("_", $v);
											// echo $val[0]."==".implode(",",array_unique(array_filter(explode(",", $row["itm_num_id"]))));die();
											if( trim($val[0])== implode(",",array_unique(array_filter(explode(",", $row["itm_num_id"])))))
											{
												$item_smv=$val[2];
											}
										}
									}
									$item_number_id = implode("_",array_unique(array_filter(explode(",", $row["itm_num_id"]))));
            						$challan_no = implode(", ", array_unique(array_filter(explode(",", $row['sys_number']))));
            						$rtn_challan_no = implode(", ", array_unique(array_filter(explode(",", $row['rtn_sys_number']))));
            						$del_location = implode(", ", array_unique(array_filter(explode(",", $row['del_location']))));
            						$del_floor = implode(", ", array_unique(array_filter(explode(",", $row['del_floor']))));
            						$invoice_no = implode(", ", array_unique(array_filter(explode(",", $row['invoice_no']))));
            						$lc_sc_no = implode(", ", array_unique(array_filter(explode(",", $row['lc_sc_arr_no']))));
            						$item_name = implode(", ", array_unique(array_filter(explode(",", $row['item_name']))));
            						$lock_no = implode(", ", array_unique(array_filter(explode(",", $row['lock_no']))));
            						$driver_name = implode(", ", array_unique(array_filter(explode(",", $row['driver_name']))));
            						$mobile_no = implode(", ", array_unique(array_filter(explode(",", $row['mobile_no']))));
            						$dl_no = implode(", ", array_unique(array_filter(explode(",", $row['dl_no']))));
            						$vehi_no = implode(", ", array_unique(array_filter(explode(",", $row['truck_no']))));
            						$forwarder = implode(", ", array_unique(array_filter(explode(",", $row['forwarder']))));

            						// $dirver_info="Name: ".$driver_name."<br>Mob No: ".$mobile_no."<br>DL No: ".$dl_no;
									$dirver_info="Name: ".$driver_name.", Mob No: ".$mobile_no.", DL No: ".$dl_no;

            						$todate=date("d-M")."-".substr(date("Y"), 2) ;
						 			$todate=explode("-", $todate);
						 			$todate=$todate[0]."-".strtoupper($todate[1])."-".$todate[2];
            						$diff=datediff("d",$todate, $row["shipment_date"])-2;

            						$comapny_id = $row["company_name"];
            						//$unit_price = $row["unit_price"];
            						if($unit_price_country_qnty_arr[$job_key][$po_key][$country_key]>0){
										$unit_price = $unit_price_country_qnty_arr[$job_key][$po_key][$country_key];
									}
            						$current_ex_Fact_Qty = $exfact_country_qnty_arr[$po_key][$country_key]['ex'];
            						$total_ex_fact_qty = $exfact_country_qnty_arr[$po_key][$country_key]['tot_ex'];
            						$carton_qnty = $exfact_country_qnty_arr[$po_key][$country_key]['carton_qnty'];
            						$total_cartoon_qty = $exfact_country_qnty_arr[$po_key][$country_key]['tot_carton_qnty'];
									$ex_rtn_qty = 0;
									$tot_ex_rtn_qty = 0;
									foreach($data_array_details[$job_key][$po_key][$country_key] as $challan_no=>$val)
									{
										$ex_rtn_qty += $exfact_country_qnty_arr[$po_key][$country_key][$val['sys_number']]['ex_rtn'];
										$tot_ex_rtn_qty += $exfact_country_qnty_arr[$po_key][$country_key][$val['sys_number']]['tot_ex_rtn'];
									}

            						$current_ex_Fact_Qty = $current_ex_Fact_Qty - $ex_rtn_qty;

            						$total_ex_fact_qty = $total_ex_fact_qty - $tot_ex_rtn_qty;

            						$basic_qnty= ($basic_smv_arr[$comapny_id]>0) ? ($total_ex_fact_qty*$item_smv)/$basic_smv_arr[$comapny_id] : 0;

									// $actual_po_ex_fact_report2=return_library_array( "select id, acc_po_no  from  wo_po_acc_po_info where job_no= '$row[job_no]'",'id','acc_po_no');
									// $actual_po_ex_fact_report2_final =implode(",",$actual_po_ex_fact_report2);

									?>
			            			<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
			            				<td rowspan="<?=$rowspan;?>" width="40" align="center"><?=$i;?></td>
										<td rowspan="<?=$rowspan;?>" width="150" align="center" ><p><?=$company_library[$row["company_name"]];?></p></td>
										<td rowspan="<?=$rowspan;?>" width="60" align="center" ><p><?=$row["job_no_prefix_num"];?></p></td>
										<td rowspan="<?=$rowspan;?>" width="60" align="center" ><p><?=$row["year"];?></p></td>
										<td rowspan="<?=$rowspan;?>" width="100" align="center" ><p><?=$buyer_arr[$row["buyer_name"]];?></p></td>
										<td rowspan="<?=$rowspan;?>" width="110" align="center" title="<?=$po_key;?>"><p><?=$row["po_number"];?></p></td>
										<td rowspan="<?=$rowspan;?>" width="125" align="center" title="<??>"><p><?echo $acc_po_arr[$job_key][$po_key][$country_key];?></p></td>
										<td rowspan="<?=$rowspan;?>" width="125" align="left" title="<? echo $country_key;?>"><p><?=$lib_country[$country_key];?></p></td>
										<td rowspan="<?=$rowspan;?>" width="110" align="left"><p><?=$row["grouping"];?></p></td>
										<td rowspan="<?=$rowspan;?>" width="125" align="center" ><p><?=$company_library[$row["del_company"]];?></p></td>
										<td rowspan="<?=$rowspan;?>" width="125" align="center" ><p><?=$del_location;?></p></td>
										<td rowspan="<?=$rowspan;?>" width="125" align="center" ><p><?=$del_floor;?></p></td>

										<?
											$j=0;
											foreach($data_array_details[$job_key][$po_key][$country_key] as $challan_no=>$val)
											{
												if($j==0){
												?>
													<td width="120" align="center"><?=$val['sys_number'];if (!empty($val['rtn_sys_number'])) {
    	echo $val['rtn_sys_number'];
		}
		?></td>
													<td width="100" align="right"><?
														$ex_factory_qnty=array_sum($val['ex_factory_qnty']);
														if(!empty($ex_factory_qnty)){
															echo fn_number_format($ex_factory_qnty,2);
															$job_total_ex_factory_qnty+=$ex_factory_qnty;
															$total_ex_factory_qnty+=$ex_factory_qnty;
														}
														else{
															$ex_factory_rtn_qnty=array_sum($val['ex_factory_rtn_qnty']);
															echo fn_number_format($ex_factory_rtn_qnty,2);
															$job_total_ex_factory_qnty+=$ex_factory_rtn_qnty;
															$total_ex_factory_qnty+=$ex_factory_rtn_qnty;
														}?></td>
													<td width="100" align="center">
														<?
															// echo implode(", ",array_unique(explode(",",rtrim($val['invoice_no'],','))));
															$invoice_no=array_unique(explode(",",rtrim($val['invoice_no'],',')));
															// foreach($invoice_no as $value){
															// 	echo $value.'<br>';
															// }
															$invoice_no_val= '';
															foreach($invoice_no as $value){
																$invoice_no_val.= $value.', ';
															}
															echo rtrim($invoice_no_val,', ');
														?>
													</td>
													<td width="100" align="right">
														<?
														// $invoice_qnty=array_sum($val['invoice_qnty']);
														// echo fn_number_format(array_sum($val['invoice_qnty']),2);
														// $job_total_invoice_qnty+=$invoice_qnty;
														// $total_invoice_qnty+=$invoice_qnty;
														// foreach($invoice_qnty as $value){
														// 	echo fn_number_format($value,2).'<br>';
														// }
														$invoice_qnty=$val['invoice_qnty'];
														$invoice_qnty_val='';
														foreach($invoice_qnty as $value){
															$invoice_qnty_val.= fn_number_format($value).' - ';
														}
														echo rtrim($invoice_qnty_val,'- ');
														?>
													</td>
													<td width="100" align="center"><?=$yes_no[$val['is_posted_account']];?></td>
												<?
												}
												$j++;
											}
										?>
										<td rowspan="<?=$rowspan;?>" width="100" align="center"><p><?=$lc_sc_no;?></p></td>
										<td rowspan="<?=$rowspan;?>" width="100"><p><?=$row["style_ref_no"];?></p></td>
										<td rowspan="<?=$rowspan;?>" width="100"><p><?=$row["style_description"];?></p></td>
										<td rowspan="<?=$rowspan;?>" width="110" align="center"><p><?=$item_name;?></p></td>
										<td rowspan="<?=$rowspan;?>" width="80" align="center"><p><?=$item_smv;?></p></td>
										<td rowspan="<?=$rowspan;?>" width="70" align="center"><p><?=change_date_format($country_ship_date_arr[$po_key][$country_key]);?></p></td>
										<td rowspan="<?=$rowspan;?>" width="70" align="center"><p><?=change_date_format($row["shipment_date"]);?></p></td>
										<td rowspan="<?=$rowspan;?>" width="70" align="center">
											<a href="##" onclick="openmypage_ex_date(<?=$comapny_id;?>,'<?=$po_key;?>','<?=$item_number_id . "__" . $country_key;?>','<?=$ex_fact_date_range;?>','ex_date_popup','<?=$row['challan_id'];?>','1')"><?=change_date_format($row['ex_factory_date']);?></a>
										</td>
										<td rowspan="<?=$rowspan;?>" width="100" align="center"><p><?=$shipment_mode[$row['ship_mode']];?></p></td>
										<td rowspan="<?=$rowspan;?>" width="70" align="center"><p><?=$shipment_mode[$row["shiping_mode"]];?></p></td>
										<td rowspan="<?=$rowspan;?>" width="60" align="center" style="<?=$diff_color;?>"><p>(<?=$diff;?>)</p></td>
										<td rowspan="<?=$rowspan;?>" width="100" align="center"><p><?=$unit_of_measurement[$row['order_uom']];?></p></td>
										<td rowspan="<?=$rowspan;?>" width="80" align="right"><p><?=number_format($order_country_qnty_arr[$po_key][$country_key], 0, '', '');?></p></td>

										<td rowspan="<?=$rowspan;?>" width="70" align="right" ><p><?=number_format($unit_price, 4);?></p></td>

										<td rowspan="<?=$rowspan;?>" width="100" align="right"><p><?=number_format(($order_country_qnty_arr[$po_key][$country_key] * $unit_price), 2);?></p></td>
										<td rowspan="<?=$rowspan;?>" width="80" align="right"><p>
											<a href="##" onclick="openmypage_ex_date(<?=$comapny_id;?>,'<?=$po_key;?>','<?=$item_number_id . "__" . $country_key . "__" . $txt_date_from . "__" . $txt_date_to;?>','<?=$ex_fact_date_range;?>','ex_date_popup','<?=$row['challan_id'];?>','1')"><?=number_format($current_ex_Fact_Qty, 0, '.', '');?></a>
										</p></td>
										<td rowspan="<?=$rowspan;?>" width="100" align="right"><p><?=number_format($current_ex_fact_value = $current_ex_Fact_Qty * $unit_price, 2);?></p></td>
										<td rowspan="<?=$rowspan;?>" width="80" align="right"><p><?=number_format($carton_qnty, 0, '.', '');?></p></td>
										<td rowspan="<?=$rowspan;?>" width="80" align="right"><p>
											<a href="##" onclick="openmypage_ex_date(<?=$comapny_id;?>,'<?=$po_key;?>','<?=$item_number_id . "__" . $country_key;?>','<?=$total_exface_qnty;?>','ex_date_popup','<?=$row['challan_id'];?>','1')"><?=number_format($total_ex_fact_qty, 0, '.', '');?></a>
										</p></td>

										<td rowspan="<?=$rowspan;?>" width="100" align="right"><p><?=number_format($total_ex_fact_value = $total_ex_fact_qty * $unit_price, 2);?></p></td>

										<td rowspan="<?=$rowspan;?>" width="60" align="right"><p><?=number_format($ex_rtn_qty, 0, '.', '');?></p></td>
										<td rowspan="<?=$rowspan;?>" width="80" align="right"><p><?=number_format($total_cartoon_qty, 0, '.', '');?></p></td>
										<td rowspan="<?=$rowspan;?>" width="100" align="right" title="Total Ex.Qty*SMV"><p><?=number_format($total_sales_minutes = $total_ex_fact_qty * $item_smv);?></p></td>
										<td rowspan="<?=$rowspan;?>" width="80" align="right"><p><?=number_format($basic_qnty, 0, '', '');?></p></td>
										<td rowspan="<?=$rowspan;?>" width="80" align="right" style="'.$excess_msg.'" ><p><?=number_format($excess_shortage_qty = $total_ex_fact_qty - $order_country_qnty_arr[$po_key][$country_key], 0, '', '');?></p></td>
										<td rowspan="<?=$rowspan;?>" width="100" align="right" style="'.$excess_val_msg.'"><p><?=number_format($excess_shortage_value = ($excess_shortage_qty * $unit_price), 2);?></p></td>

										<td rowspan="<?=$rowspan;?>" align="center" style="'.$ttl_ex_qty_msg.'" width="80">
											<p>
												<?
												$ex_fact_qty_parcent = 0;
												if($total_ex_fact_qty>0 && $order_country_qnty_arr[$po_key][$country_key]>0)$ex_fact_qty_parcent = $total_ex_fact_qty / $order_country_qnty_arr[$po_key][$country_key];

												echo number_format($total_ex_fact_qty_parcent = $ex_fact_qty_parcent * 100, 0);?>
											</p>
										</td>

										<td rowspan="<?=$rowspan;?>" width="60" align="center" title="CM per pcs: '.number_format($cm_per_pcs,4).'"><p><?=number_format($cm_per_pcs * $total_ex_fact_qty, 2);?></p></td>
										<td rowspan="<?=$rowspan;?>" width="100" align="center"><p><?=$forwarder;?></p></td>
										<td rowspan="<?=$rowspan;?>" width="80" align="center"><p><?=$vehi_no;?></p></td>
										<td rowspan="<?=$rowspan;?>" width="80" align="center"><p><?=$lock_no;?></p></td>
										<td rowspan="<?=$rowspan;?>" width="130"><p><?=$dirver_info;?></p></td>
										<td rowspan="<?=$rowspan;?>" width="70" align="center"><p><?=(change_date_format($inspection_date_arr[$po_key]) == '0000-00-00' || change_date_format($inspection_date_arr[$po_key]) == '' ? '' : change_date_format(change_date_format($inspection_date_arr[$po_key])));?></p></td>
										<td rowspan="<?=$rowspan;?>" align="center"><p><?=$shipment_status[$row['shiping_status']];?></p></td>

			            			</tr>
									<?
										$k=0;
										foreach($data_array_details[$job_key][$po_key][$country_key] as $val)
										{
											if($k!=0){
											?>
												<tr>
													<td width="120" align="center"><?=$val['sys_number'];if (!empty($val['rtn_sys_number'])) {
    	echo $val['rtn_sys_number'];
		}
		?></td>
													<td width="100" align="right"><?
														$ex_factory_qnty=array_sum($val['ex_factory_qnty']);
														if(!empty($ex_factory_qnty)){
															echo fn_number_format($ex_factory_qnty,2);
															$job_total_ex_factory_qnty+=$ex_factory_qnty;
															$total_ex_factory_qnty+=$ex_factory_qnty;
														}
														else{
															$ex_factory_rtn_qnty=array_sum($val['ex_factory_rtn_qnty']);
															echo fn_number_format($ex_factory_rtn_qnty,2);
															$job_total_ex_factory_qnty+=$ex_factory_rtn_qnty;
															$total_ex_factory_qnty+=$ex_factory_rtn_qnty;
														}?></td>
													<td width="100" align="center">
														<?
															// echo implode(", ",array_unique(explode(",",rtrim($val['invoice_no'],','))));
															$invoice_no=array_unique(explode(",",rtrim($val['invoice_no'],',')));
															// foreach($invoice_no as $value){
															// 	echo $value.'<br>';
															// }
															$invoice_no_val= '';
															foreach($invoice_no as $value){
																$invoice_no_val.= $value.', ';
															}
															echo rtrim($invoice_no_val,', ');
														?>
													</td>
													<td width="100" align="right">
														<?
															// $invoice_qnty=array_sum($val['invoice_qnty']);
															// echo fn_number_format(array_sum($val['invoice_qnty']),2);
															// $job_total_invoice_qnty+=$invoice_qnty;
															// $total_invoice_qnty+=$invoice_qnty;
															// foreach($invoice_qnty as $value){
															// 	echo fn_number_format($value,2).'<br>';
															// }
															$invoice_qnty=$val['invoice_qnty'];
															$invoice_qnty_val='';
															foreach($invoice_qnty as $value){
																$invoice_qnty_val.= fn_number_format($value).' - ';
															}
															echo rtrim($invoice_qnty_val,'- ');
														?>
													</td>
													<td width="100" align="center"><?=$yes_no[$val['is_posted_account']];?></td>
												</tr>
											<?
											}
											$k++;
										}
									?>
			            			<?
			            			$i++;

					                $job_total_po_qty += $order_country_qnty_arr[$po_key][$country_key];
					                $job_total_po_valu += $order_country_qnty_arr[$po_key][$country_key]*$unit_price;
					                $job_total_ex_qty += $current_ex_Fact_Qty;
					                $job_total_ex_valu += $current_ex_fact_value;
					                $job_total_crtn_qty += $carton_qnty;
					                $job_total_ex_rtn_qty += $ex_rtn_qty;
					                $job_g_total_ex_qty += $total_ex_fact_qty;
					                $job_g_total_ex_val += $total_ex_fact_value;
					                $job_g_total_ex_crtn += $total_cartoon_qty;
					                $job_g_sales_minutes += $total_sales_minutes;
					                $job_total_basic_qty += $basic_qnty;
					                $job_total_eecess_storage_qty += $excess_shortage_qty;
					                $job_total_eecess_storage_val += $excess_shortage_value;
					                $job_gr_ttl_ex_fac_per += $total_ex_fact_qty_parcent;
					                $job_cm_per_pcs_tot += $cm_per_pcs*$total_ex_fact_qty;

					                $total_po_qty += $order_country_qnty_arr[$po_key][$country_key];
					                $total_po_valu += $order_country_qnty_arr[$po_key][$country_key]*$unit_price;
					                $total_ex_qty += $current_ex_Fact_Qty;
					                $total_ex_valu += $current_ex_fact_value;
					                $total_crtn_qty += $carton_qnty;
					                $g_total_ex_qty += $total_ex_fact_qty;
					                $g_total_ex_val += $total_ex_fact_value;
					                $g_total_ex_crtn += $total_cartoon_qty;
					                $g_total_ex_rtn_qty += $ex_rtn_qty;
					                $g_sales_minutes += $total_sales_minutes;
					                $total_basic_qty += $basic_qnty;
					                $total_eecess_storage_qty += $excess_shortage_qty;
					                $total_eecess_storage_val += $excess_shortage_value;
					                $gr_ttl_ex_fac_per += $total_ex_fact_qty_parcent;
					                $cm_per_pcs_tot += $cm_per_pcs*$total_ex_fact_qty;
            					}
            				}
            				?>

		                    <tr style="background: #dccdcd;font-weight: bold;text-align: right;">
		                    	<td colspan="13"><strong>Job Total </strong></td>
		                    	<td align="right"><? echo number_format($job_total_ex_factory_qnty,0);?></td>
		                    	<td >&nbsp;</td>
		                    	<td align="right"><? //echo number_format($job_total_invoice_qnty,0);?></td>
								<td colspan="13">&nbsp;</td>
		                        <td id="" align="right"><? echo  number_format($job_total_po_qty,0);?></td>
		                        <td align="right">&nbsp;</td>
		                        <td align="right" id=""><? echo  number_format($job_total_po_valu,2); ?></td>
		                        <td align="right" id=""><? echo number_format($job_total_ex_qty,0); ?></td>
		                        <td align="right" id=""><? echo number_format($job_total_ex_valu,2);?></td>
		                        <td align="right" id=""><? echo number_format($job_total_crtn_qty,0); ?></td>
		                        <td align="right" id=""><? echo number_format($job_g_total_ex_qty,0);?></td>
		                        <td align="right" id=""><? echo number_format($job_g_total_ex_val,2);?></td>
		                        <td align="right" id=""><? echo number_format($job_total_ex_rtn_qty,0);?></td>
		                        <td align="right" id=""><? echo number_format($job_g_total_ex_crtn,0);?></td>
		                        <td align="right" id=""><? echo number_format($job_g_sales_minutes);?></td>

		                        <td align="right" id=""><? echo number_format($job_total_basic_qty,0); ?></td>
		                        <td align="right" id=""><? echo number_format($job_total_eecess_storage_qty,0);?></td>
		                        <td align="right" id=""><? echo number_format($job_total_eecess_storage_val,0);?></td>
		                        <td id=""><? echo number_format($job_gr_ttl_ex_fac_per,0);?></td>
		                        <td align="right" id=""><? echo number_format($job_cm_per_pcs_tot,2);?></td>
		                        <td>&nbsp;</td>
		                        <td>&nbsp;</td>
		                        <td>&nbsp;</td>
		                        <td>&nbsp;</td>
		                        <td>&nbsp;</td>
		                        <td>&nbsp;</td>
            				<?
            			}
            			?>
            		</tbody>
            	</table>
            </div>
            <table width="4705" cellspacing="0" cellpadding="0"  border="1"  class="rpt_table" rules="all" id="table_footer" align="left">
                <tfoot>
                    <tr>
                    	<th width="40">&nbsp;</th>
                        <th width="150">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="125">&nbsp;</th>
                        <th width="125">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="125">&nbsp;</th>
                        <th width="125">&nbsp;</th>
                        <th width="125">&nbsp;</th>
                        <th width="120" align="right"><strong>Grand Total </strong></th>
                        <th width="100"><? echo  number_format($total_ex_factory_qnty,0);?></th>
                        <th width="100">&nbsp;</th>
                        <th width="100"><? //echo  number_format($total_invoice_qnty,0);?></th>
                        <th width="100">&nbsp;</th>
                        <th width="100" >&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="100" ></th>
                        <th width="80" id="" align="right"><? echo  number_format($total_po_qty,0);?></th>
                        <th width="70" align="right">&nbsp;</th>
                        <th width="100" align="right" id=""><? echo  number_format($total_po_valu,2); ?></th>
                        <th width="80" align="right" id=""><? echo number_format($total_ex_qty,0); ?></th>
                        <th width="100" align="right" id=""><? echo number_format($total_ex_valu,2);?></th>
                        <th width="80" align="right" id=""><? echo number_format($total_crtn_qty,0); ?></th>
                        <th width="80" align="right" id=""><? echo number_format($g_total_ex_qty,0);?></th>
                        <th width="100" align="right" id=""><? echo number_format($g_total_ex_val,2);?></th>
                        <th width="60" align="right" id=""><? echo number_format($g_total_ex_rtn_qty,0);?></th>
                        <th width="80" align="right" id=""><? echo number_format($g_total_ex_crtn,0);?></th>
                        <th width="100" align="right" id=""><? echo number_format($g_sales_minutes);?></th>

                        <th width="80" align="right" id=""><? echo number_format($total_basic_qty,0); ?></th>
                        <th width="80" align="right" id=""><? echo number_format($total_eecess_storage_qty,0);?></th>
                        <th width="100" align="right" id=""><? echo number_format($total_eecess_storage_val,0);?></th>
                        <th width="80" id=""><? echo number_format($gr_ttl_ex_fac_per,0);?></th>
                        <th width="60" align="right" id=""><? echo number_format($cm_per_pcs_tot,2);?></th>
                        <th width="100">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="130">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th>&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
            </div>
        </fieldset>

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
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename####$reportType";
	exit();
}

if($action=="ex_date_popup")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$ex_factory_date=str_replace("'","",$ex_factory_date);
	$ex_factory_date_ref=explode("_",$ex_factory_date);
	$exfact_date=explode("*",$ex_factory_date_ref[0]);
	//echo $ex_factory_date."***".$company_id."***".$order_id."***".$challan_id;
	$country_arr=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$source= $_SESSION["source"];
	$source_cond="";
	if($source)
		$source_cond=" and b.source='$source'";
	?>
	<div style="width:100%" align="center">
		<fieldset style="width:500px">
        <div class="form_caption" align="center"><strong>Ex-Factory Date Details</strong></div><br />
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="100">Date</th>
                        <th width="100">Challan</th>
                        <th width="100">Country</th>
                        <th width="100">Delv. Qty</th>
                        <th width="">Return Qty</th>
                     </tr>
                </thead>
                <tbody>
					<?

					if($type==1)
					{
						if($challan_id!=""){$challan_id_cond= "and a.delivery_mst_id=$challan_id"; }else{$challan_id_cond= "";}
						$sql_res=sql_select("SELECT b.po_break_down_id as po_id,  b.item_number_id,
							sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as return_qnty
							from  pro_ex_factory_mst b  where  b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id,b.item_number_id");
						$ex_factory_qty_arr=array();
						foreach($sql_res as $row)
						{

							$ex_factory_qty_arr[$row[csf('po_id')]][$row[csf('item_number_id')]]['return_qty']=$row[csf('return_qnty')];
						}

						$i=1;
						if($ex_factory_date_ref[1]==2)
						{
							$sql_qnty="SELECT a.po_break_down_id,a.item_number_id,a.ex_factory_date,a.challan_no,a.country_id,
							sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
							sum(CASE WHEN a.entry_form=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_ret_qnty

							from pro_ex_factory_mst a,pro_ex_factory_delivery_mst b where a.delivery_mst_id=b.id and  a.po_break_down_id=$order_id and a.item_number_id=$item_number_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 $source_cond  group by a.po_break_down_id,a.item_number_id,a.ex_factory_date,a.challan_no,a.country_id order by a.ex_factory_date ";
						}
						else
						{

							$sql_qnty="SELECT a.po_break_down_id,a.item_number_id,a.ex_factory_date,a.challan_no,a.country_id,

							sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty

							from pro_ex_factory_mst a ,pro_ex_factory_delivery_mst b where a.delivery_mst_id=b.id and a.po_break_down_id=$order_id and a.item_number_id=$item_number_id and a.status_active=1 and a.is_deleted=0 and a.ex_factory_date between  '$exfact_date[0]' and '$exfact_date[1]' $challan_id_cond $source_cond and b.status_active=1 group by a.po_break_down_id,a.item_number_id,a.ex_factory_date,a.challan_no,a.country_id order by a.ex_factory_date ";


						}

					}
					else if($type==2)
					{
 						$delivery_no_arr=array_unique(explode(",", $challan_id));
						$delivery_no_arr="'".implode("','", $delivery_no_arr)."'";
						if($ex_factory_date)
						{

						 $date_cond=" and a.delivery_date =  '$ex_factory_date'";
						 $challan_cond=" and a.delivery_no in($delivery_no_arr)";
						}
						else
						{
							$challan_id_as_comp_loc=explode('_', $challan_id);
							if($challan_id_as_comp_loc[0]) $challan_cond=" and a.company_id='$challan_id_as_comp_loc[0]'";
							if($challan_id_as_comp_loc[1]) $challan_cond.=" and a.location_id='$challan_id_as_comp_loc[1]'";

						}
 						 $sql_qnty="SELECT  a.vehical_no,a.delivery_no as challan_no,b.subcon_job ,b.job_no_prefix_num as job,b.party_id as buyer_name,b.company_id as company_name,a.company_id as delivery_company_id,a.location_id,c.cust_style_ref as style_ref_no,c.order_no as po_number,c.id as po_id, a.delivery_date as ex_factory_date   ,c.order_quantity as po_quantity ,c.delivery_date as shipment_date,c.smv,d.item_id,d.total_carton_qnty,  d.delivery_qty as ex_factory_qnty,b.insert_date,c.rate as unit_price from subcon_delivery_mst a,subcon_delivery_dtls d, subcon_ord_mst b,subcon_ord_dtls c   where  a.id=d.mst_id and d.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0   and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form<>4 and c.id='$order_id' and d.item_id='$item_number_id' and b.company_id='$company_id'  $date_cond  $challan_cond";


					}

						//echo $sql_qnty;
						$sql_dtls=sql_select($sql_qnty);
						foreach($sql_dtls as $row_real)
						{
							 if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
							 if($ex_factory_date_ref[1]==2)
							 {
								 $return_qty=$row_real[csf("ex_factory_ret_qnty")];
							 }
							 else
							 {
								$return_qty=$ex_factory_qty_arr[$row_real[csf("po_break_down_id")]][$row_real[csf("item_number_id")]]['return_qty'];
							 }


							 ?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
									<td><? echo $i; ?></td>
									<td  align="center"><? echo change_date_format($row_real[csf("ex_factory_date")]); ?></td>
                                    <td ><? echo $row_real[csf("challan_no")]; ?></td>
                                    <td ><? echo $country_arr[$row_real[csf("country_id")]]; ?></td>
									<td width="100" align="right"><? echo number_format($row_real[csf("ex_factory_qnty")]-$return_qty,2); ?>&nbsp;</td>
                                    <td width="" align="right"><? echo number_format($return_qty,2); ?>&nbsp;</td>
								</tr>
							<?
							$total_ex_qnty+=$row_real[csf("ex_factory_qnty")];
							$total_return_ex_qnty+=$return_qty;
							$i++;
						}
                    ?>
                </tbody>
                <tfoot>
                	<tr>
                    	<th colspan="4" align="right"><strong>Total :</strong></th>
                        <th align="right"><? echo number_format($total_ex_qnty,2); ?></th>
                        <th align="right"><? echo number_format($total_return_ex_qnty,2); ?> </th>
                    </tr>
                    <tr>
                    	<th colspan="4" align="right"><strong>Total Balance:</strong></th>
                        <th align="right" colspan="2"><? echo number_format($total_ex_qnty-$total_return_ex_qnty,2); ?></th>

                    </tr>
                </tfoot>
            </table>
        </fieldset>
    </div>
    <?
}
disconnect($con);
?>
