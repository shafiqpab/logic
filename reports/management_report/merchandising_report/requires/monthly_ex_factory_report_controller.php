<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.commisions.php');
require_once('../../../../includes/class4/class.trims.php');
require_once('../../../../includes/class4/class.fabrics.php');
require_once('../../../../includes/class4/class.yarns.php');
require_once('../../../../includes/class4/class.conversions.php');
require_once('../../../../includes/class4/class.others.php');
require_once('../../../../includes/class4/class.emblishments.php');
require_once('../../../../includes/class4/class.commercials.php');
require_once('../../../../includes/class4/class.washes.php');
require_once('../../../../includes/class4/cm_gmt_class.php');

include ("../../../../ext_resource/excel/excel/vendor/autoload.php");
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
if (!function_exists('pre')){
	function pre($arr)
	{
		echo "<pre>";
		print_r($arr);
		echo "</pre>";
	}
}
$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer where is_deleted=0 order by sequence_no","id","buyer_name");
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );

$supp_library=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name"  );
$lib_country=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );

$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  );
$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  );


if($action=="print_button_variable_setting")
{
	if($data==0) $comp_cond=""; else $comp_cond="and template_name in ($data)";
	$print_report_format_arr = return_library_array("select format_id,format_id from lib_report_template where 1=1 $comp_cond and module_id=11 and report_id=74 and is_deleted=0 and status_active=1","format_id","format_id");
	echo "print_report_button_setting('".implode(',',$print_report_format_arr)."');\n";
	exit();
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 100, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "load_drop_down( 'requires/monthly_ex_factory_report_controller', this.value, 'load_drop_down_brand', 'brand_td');load_drop_down( 'requires/monthly_ex_factory_report_controller', this.value, 'load_drop_down_season_buyer', 'season_td');" );
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

if ($action=="load_drop_down_brand")
{
	echo create_drop_down( "cbo_brand_id", 100, "select id, brand_name from lib_buyer_brand brand where buyer_id='$data' and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC","id,brand_name", 1, "--Brand--", $selected, "" );
	exit();
}

if ($action=="load_drop_down_season_buyer")
{
	echo create_drop_down( "cbo_season_name", 100, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- Select Season--", "", "" );
	exit();
}

//$job_smv_arr=return_library_array( "select job_no, set_smv from wo_po_details_master",'job_no','set_smv');

//return_field_value("sum(a.ex_factory_qnty) as po_quantity"," pro_ex_factory_mst a, wo_po_break_down b","a.po_break_down_id=b.id and b.id='".$row[csf("po_id")]."' and a.is_deleted=0 and a.status_active=1","po_quantity");
//$lc_sc=return_field_value("b.contract_no as export_lc_no"," com_sales_contract b"," b.id in($sc_lc_id)' ","export_lc_no");
//$lc_sc=return_field_value("b.export_lc_no as export_lc_no","com_export_lc b"," b.id in($sc_lc_id) ","export_lc_no");
//$lc_type=return_field_value("is_lc","com_export_invoice_ship_mst","id in(".$row[csf('invoice_no')].")","is_lc");
//$last_ex_factory_date=return_field_value(" max(ex_factory_date) as ex_factory_date","pro_ex_factory_mst","po_break_down_id in(".$row[csf('po_id')].")","ex_factory_date");

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$reportType=str_replace("'","",$reportType);
	$cbo_delivery_company_name=str_replace("'","",$cbo_delivery_company_name);
	$cbo_location_name=str_replace("'","",$cbo_location_name);
	$cbo_delivery_floor=str_replace("'","",$cbo_del_floor);
	$internal_ref=str_replace("'","",$txt_internal_ref);
	$cbo_shipping_status=str_replace("'","",$cbo_shipping_status);
	$cbo_source=str_replace("'","",$cbo_source);
	$cbo_brand_id=str_replace("'","",$cbo_brand_id);
	$cbo_season_name=str_replace("'","",$cbo_season_name);
	$cbo_season_year=str_replace("'","",$cbo_season_year);
	$_SESSION["source"]="";
	$_SESSION["source"]=$cbo_source;
	$buyer_cond = '';


	$source_cond="";
	if($cbo_source)$source_cond=" and d.source='$cbo_source'";
	$shiping_status_cond=($cbo_shipping_status>0)? " and a.shiping_status= $cbo_shipping_status " : " ";

	if($txt_date_from!="" && $txt_date_to!="")
	{
		$po_date_cond="and b.po_received_date between '$txt_date_from' and  '$txt_date_to' ";
		$str_cond="and a.ex_factory_date between '$txt_date_from' and  '$txt_date_to' ";
		$str_cond_sub=" and a.delivery_date between '$txt_date_from' and  '$txt_date_to' ";
	}
	else
	{
		$str_cond="";
		$po_date_cond="";
	}

	if($cbo_delivery_company_name)
	{
		 $del_comp_cond="and d.delivery_company_id in( $cbo_delivery_company_name) ";
		 $str_cond_sub.=" and a.company_id in( $cbo_delivery_company_name) ";
		 $str_cond_sub_total.=" and a.company_id in( $cbo_delivery_company_name) ";
	}
	else $del_comp_cond="";

	if($cbo_location_name)
	{
		 $str_cond_sub.="and a.location_id='$cbo_location_name' ";
		 $str_cond_sub_total.="and a.location_id='$cbo_location_name' ";
		 $del_location_cond="and d.delivery_location_id='$cbo_location_name'";
	}
	else $del_location_cond="";

	if($cbo_company_name)
	{
		 $company_cond=" and c.company_name like '$cbo_company_name' ";
		 $str_cond_sub.=" and a.company_id in( $cbo_company_name) ";
		 $str_cond_sub_total.=" and a.company_id in( $cbo_company_name) ";
	}

	if($cbo_buyer_name) {
		$buyer_cond=" and c.buyer_name = $cbo_buyer_name";
	}
	// else $company_cond="";

	if($cbo_delivery_floor) $del_floor_cond="and d.delivery_floor_id='$cbo_delivery_floor' "; else $del_floor_cond="";
	if($internal_ref !="") $internal_ref_cond="and b.grouping='$internal_ref'"; else $internal_ref_cond="";
	$search_cond='';
	if($cbo_brand_id !=0) $search_cond.="and c.brand_id='$cbo_brand_id'";
	if($cbo_season_name !=0) $search_cond.="and c.season_buyer_wise='$cbo_season_name'";
	if($cbo_season_year !=0) $search_cond.="and c.season_year='$cbo_season_year'";


	if(str_replace("'","", $cbo_buyer_name))
	{
		$str_cond_sub.=" and b.party_id in( ".str_replace("'", "",  $cbo_buyer_name).") ";
		$str_cond_sub_total.=" and b.party_id in( ".str_replace("'", "",  $cbo_buyer_name).") ";
		$buyer_conds.=" and c.buyer_name = ".str_replace("'", "",  $cbo_buyer_name) ;
		$buyer_conds2.=" and b.buyer_id = ".str_replace("'", "",  $cbo_buyer_name) ;
		$po_buyer_cond.=" and a.buyer_name = ".str_replace("'", "",  $cbo_buyer_name) ;
	}

	$details_report="";
	$dtls_width= 4815+200;
	$master_data=array();
	$current_date=date("Y-m-d");
	$date=date("Y-m-d");$break_id=0;$sc_lc_id=0;
	$sy = date('Y',strtotime($txt_date_from));
	$basic_smv_arr=return_library_array( "select comapny_id, basic_smv from lib_capacity_calc_mst where year=$sy",'comapny_id','basic_smv');

	$tot_cost_arr = return_library_array("select job_no, cm_for_sipment_sche from wo_pre_cost_dtls","job_no","cm_for_sipment_sche");
	$costing_per_arr = return_library_array("select job_no, costing_per from wo_pre_cost_mst","job_no","costing_per");

	ob_start();
	if($reportType==1)//Details Button
	{
        if($cbo_style_owner_company_name > 0){
            $company_cond .= " and c.style_owner = $cbo_style_owner_company_name ";
        }
		$tot_cost_arr = return_library_array("select job_no, cm_cost from wo_pre_cost_dtls","job_no","cm_cost");
		$inspection_date_arr=return_library_array( "select po_break_down_id, max(inspection_date) as inspection_date from pro_buyer_inspection where status_active=1 and is_deleted=0 group by po_break_down_id", "po_break_down_id", "inspection_date");
		$invoice_array=return_library_array( "select id,invoice_no from com_export_invoice_ship_mst", "id", "invoice_no"  );
		$shipping_mode_array=return_library_array( "select id,shipping_mode from com_export_invoice_ship_mst", "id", "shipping_mode"  );
		$lc_sc_type_arr=return_library_array( "select id,is_lc from com_export_invoice_ship_mst", "id", "is_lc"  );
		$lc_sc_id_array=return_library_array( "select id,lc_sc_id from com_export_invoice_ship_mst", "id", "lc_sc_id"  );
		$lc_num_arr=return_library_array( "select id,export_lc_no from com_export_lc", "id", "export_lc_no"  );
		$sc_num_arr=return_library_array( "select id,contract_no from com_sales_contract", "id", "contract_no"  );

		$forwarder_arr=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name");


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
		$details_report .='<table width="4825" cellspacing="0" cellpadding="0"  border="1"  class="rpt_table" rules="all" id="table_body2">';
		$i=1;

		if($db_type==0)
		{
			$sql= "SELECT b.id as po_id, max(a.lc_sc_no) as lc_sc_arr_no, group_concat(distinct a.invoice_no) as invoice_no, group_concat(distinct a.item_number_id) as itm_num_id, group_concat(distinct a.foc_or_claim) as foc_or_claim,
			sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty, sum(a.total_carton_qnty) as carton_qnty, max(a.ex_factory_date)  as ex_factory_date, group_concat(distinct  a.lc_sc_no) as lc_sc_no, max(a.shiping_mode) as shiping_mode, a.delivery_mst_id as challan_id, d.delivery_floor_id as del_floor,  b.shipment_date, b.po_number, b.po_quantity as po_quantity, (b.unit_price/c.total_set_qnty) as unit_price, b.up_charge, b.shiping_status, c.id, c.company_name, c.buyer_name, c.client_id, c.job_no_prefix_num,c.job_no,c.ship_mode , YEAR(c.insert_date) as year, c.style_ref_no, c.style_description, c.set_smv, d.delivery_company_id as del_company,d.source,d.delivery_location_id as del_location ,c.total_set_qnty,a.item_number_id,c.set_break_down,c.order_uom,c.DESIGN_SOURCE_ID,d.sys_number
			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c ,pro_ex_factory_delivery_mst d
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no $company_cond $str_cond $del_comp_cond $del_location_cond $del_floor_cond $shiping_status_cond $source_cond $buyer_conds $internal_ref_cond $search_cond
			and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active  in(1,2,3) and c.is_deleted=0 and c.status_active  in(1,2,3) and a.delivery_mst_id=d.id
			group by
					b.id, a.delivery_mst_id, d.delivery_floor_id, b.shipment_date, b.po_number, b.po_quantity, c.total_set_qnty, b.unit_price, b.up_charge, b.shiping_status, c.id, c.company_name, c.buyer_name, c.client_id, c.job_no_prefix_num, c.job_no, c.ship_mode, c.insert_date, c.style_ref_no, c.style_description, c.set_smv, d.delivery_company_id, d.delivery_location_id, d.source, a.item_number_id, c.set_break_down, c.order_uom,c.DESIGN_SOURCE_ID,,d.sys_number
			order by c.buyer_name, b.shipment_date ASC";
		}
		else if($db_type==2)
		{
		 	$sql= "SELECT b.id as po_id, max(a.lc_sc_no) as lc_sc_arr_no, LISTAGG(CAST( a.invoice_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.invoice_no) as invoice_no, LISTAGG(CAST( a.foc_or_claim AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.foc_or_claim) as foc_or_claim, LISTAGG(CAST( a.item_number_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.item_number_id) as itm_num_id,
			sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,

			sum(a.total_carton_qnty) as carton_qnty,
			 max(a.ex_factory_date) as ex_factory_date, LISTAGG(CAST( a.lc_sc_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.lc_sc_no) as lc_sc_no, max(a.shiping_mode) as shiping_mode, a.delivery_mst_id as challan_id, d.delivery_floor_id as del_floor, b.shipment_date, b.po_number, b.po_quantity as po_quantity, (b.unit_price/c.total_set_qnty) as unit_price, b.up_charge, b.shiping_status, c.id, c.company_name, c.buyer_name, c.client_id, c.job_no_prefix_num, c.job_no, c.ship_mode, to_char(c.insert_date,'YYYY') as year, c.style_ref_no, c.style_description, c.set_smv, d.delivery_company_id as del_company, d.source, d.delivery_location_id as del_location, c.total_set_qnty, a.item_number_id, c.set_break_down, c.order_uom,c.DESIGN_SOURCE_ID,d.sys_number,a.challan_no,a.country_id
			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c ,pro_ex_factory_delivery_mst d
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no $company_cond $str_cond $del_comp_cond $del_location_cond $del_floor_cond $shiping_status_cond $source_cond $buyer_conds $internal_ref_cond $search_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,2,3) and c.is_deleted=0 and c.status_active in(1,2,3)and d.is_deleted=0 and d.status_active in(1) and a.delivery_mst_id=d.id and a.entry_form!=85
			group by
					b.id, a.delivery_mst_id, d.delivery_floor_id, b.shipment_date, b.po_number, b.po_quantity, c.total_set_qnty, b.unit_price, b.up_charge, b.shiping_status, c.id, c.company_name, c.buyer_name, c.client_id, c.job_no_prefix_num, c.job_no, c.ship_mode, c.insert_date, c.style_ref_no, c.style_description, c.set_smv, d.delivery_company_id, d.source, d.delivery_location_id, a.item_number_id, c.set_break_down, c.order_uom,c.DESIGN_SOURCE_ID,d.sys_number,a.challan_no,a.country_id
			order by c.buyer_name, b.shipment_date ASC";
		}
		// echo $sql;die();
		$sql_result=sql_select($sql);
		$poExQtyArray = array();
		$poChkArray = array();
		$poClientArray = array();
		$sysNumberArray = array();
		$po_id_Ar= array();
		foreach($sql_result as $k=>$v)
		{
			$all_job_arr[trim($v[csf("job_no")])] = trim($v[csf("job_no")]);
			if(!in_array($v[csf("po_id")], $poChkArray))
			{
				$poExQtyArray[$v[csf("po_id")]]['poQty'] += $v[csf("po_quantity")];
				$poChkArray[$v[csf("po_id")]] = $v[csf("po_id")];
			}
			$poExQtyArray[$v[csf("po_id")]]['exQty'] += $v[csf("ex_factory_qnty")];
			// $poExQty_RtnArray[$v[csf("po_id")]]['ex_rtn_qnty'] += $v[csf("rtn_ex_factory_qnty")];
			$po_id_Ar[$v[csf("po_id")]] = $v[csf("po_id")];
			$poClientArray[$v[csf("po_id")]] = $v[csf("client_id")];
			$sysNumberArray[$v[csf("sys_number")]] = $v[csf("sys_number")];
		}
		// echo "<pre>";print_r($poExQty_RtnArray);die();
		$all_job="'".implode("','", array_unique($all_job_arr))."'";

		$order_item_qnty_sql="SELECT po_break_down_id,item_number_id,sum(order_quantity) as order_quantity from wo_po_color_size_breakdown where status_active  in(1,2,3) and is_deleted=0 and job_no_mst in($all_job) group by po_break_down_id,item_number_id";
		foreach(sql_select($order_item_qnty_sql) as $key=>$val)
		{
			$order_item_qnty_arr[$val[csf("po_break_down_id")]][$val[csf("item_number_id")]]=$val[csf("order_quantity")];

		}
		$gr_po_qnty_pcs=0; $gr_po_qnty_val=0; $gr_po_qnty_val_perc=0; $gr_ttl_ex_qnty=0; $gr_ttl_ex_qnty_val=0; $gr_sales_min=0; $gr_ttl_carton=0; $gr_ttl_basic_qty=0; $gr_ttl_ex_fac_per=0; $gr_ttl_short_qty=0; $gr_ttl_short_val=0; $gr_ttl_sales_cm=0;

		$sql_cond_3 = ($txt_date_from && $txt_date_to) ?" and b.EX_FACTORY_DATE between '".change_date_format($txt_date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($txt_date_to,'dd-mm-yyyy','-',1)."'"  : "";
		$po_id_data = implode(",",$po_id_Ar);


		$sys_no_cond = where_con_using_array($sysNumberArray,1,"b.challan_no");
        $gm_rtn_qty = "SELECT a.production_qnty,c.buyer_id,b.PO_BREAK_DOWN_ID,b.ITEM_NUMBER_ID,b.COUNTRY_ID,b.INVOICE_NO,c.sys_number,  b.challan_no,b.delivery_mst_id as challan_id
		FROM pro_ex_factory_dtls          a,
			 PRO_EX_FACTORY_MST           b,
			 PRO_EX_FACTORY_DELIVERY_MST  c
	   WHERE     a.mst_id = b.id
			 AND c.id = b.DELIVERY_MST_ID
			  and b.PO_BREAK_DOWN_ID IN($po_id_data)
			 and a.status_active =1  AND a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.entry_form=85 $sys_no_cond";//  $sql_cond_3
			//echo $gm_rtn_qty;die;
			$sql_selectData = sql_select($gm_rtn_qty);
		   $exFct_Rtn_Qty = array();
			foreach($sql_selectData as $Row)
			{
				$exFct_Rtn_Qty[$Row['PO_BREAK_DOWN_ID']][$Row['ITEM_NUMBER_ID']][$Row['COUNTRY_ID']][$Row['CHALLAN_NO']]['ex_rtn_qnty'] += $Row['PRODUCTION_QNTY'];
				$exFct_Rtn_Qty_Buyer_wise[$Row['BUYER_ID']][$poClientArray[$Row['PO_BREAK_DOWN_ID']]]['PrQtyBuyer'] += $Row['PRODUCTION_QNTY'];
				$challanArrData[$Row['BUYER_ID']][$Row['PO_BREAK_DOWN_ID']]['PrQty'] += $Row['PRODUCTION_QNTY'];
			}
        //  echo "<pre>";
		// print_r($exFct_Rtn_Qty_Buyer_wise);die;

		//$po_exist_arr=array();
		$po_wise_ttl_ex_qty = array();
		$po_wise_ttl_up_charge = array();
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
			//print_r($challan_id);

			//$diff=($row[csf('shiping_status')]!=3)?datediff("d",$current_date, $row[csf("shipment_date")])-1:datediff("d",$row[csf("ex_factory_date")], $row[csf("shipment_date")])-1;
			$todate=date("d-M")."-".substr(date("Y"), 2) ;
 			$todate=explode("-", $todate);
 			$todate=$todate[0]."-".strtoupper($todate[1])."-".$todate[2];
 			$diff=datediff("d",$todate, $row[csf("shipment_date")])-2;
		    $diff_color=($diff>0) ?" color:black;":"color:red;";

			foreach($challan_id as $val)
			{
				if($challan_no=="") $challan_no=$challan_mst_arr[$val][$row[csf('po_id')]]['challan']; else $challan_no.=','.$challan_mst_arr[$val][$row[csf('po_id')]]['challan'];
				if($floor_no=="") $floor_no=$floor_library[$challan_mst_arr[$val][$row[csf('po_id')]]['floor']]; else $floor_no.=','.$floor_library[$challan_mst_arr[$val][$row[csf('po_id')]]['floor']];

				if($forwarder=="") $forwarder=$forwarder_arr[$challan_mst_arr[$val][$row[csf('po_id')]]['forwarder']]; else $forwarder.=','.$forwarder_arr[$challan_mst_arr[$val][$row[csf('po_id')]]['forwarder']];
				if($vehi_no=="") $vehi_no=$challan_mst_arr[$val][$row[csf('po_id')]]['truck_no']; else $vehi_no.=','.$challan_mst_arr[$val][$row[csf('po_id')]]['truck_no'];
				if($dirver_info=="") $dirver_info="Name: ".$challan_mst_arr[$val][$row[csf('po_id')]]['driver_name']."<br>Mob No: ".$challan_mst_arr[$val][$row[csf('po_id')]]['mobile_no']."<br>DL No: ".$challan_mst_arr[$val][$row[csf('po_id')]]['dl_no']; else $dirver_info.=','."Name: ".$challan_mst_arr[$val][$row[csf('po_id')]]['driver_name']."<br>Mob No: ".$challan_mst_arr[$val][$row[csf('po_id')]]['mobile_no']."<br>DL No: ".$challan_mst_arr[$val][$row[csf('po_id')]]['dl_no'];
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

			$current_ex_up_charge = 0;
			$current_ex_up_charge_value = 0;

			$total_ex_up_charge = 0;
			$total_ex_up_charge_value = 0;




			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$comapny_id=$row[csf("company_name")];
			$delv_comp=($row[csf("source")]!=3)?  $company_library[$row[csf("del_company")]] : $supp_library[$row[csf("del_company")]];

			$onclick=" change_color('tr_".$i."','".$bgcolor."')";
			$details_report .='<tr bgcolor="'.$bgcolor.'" onclick="'.$onclick.'" id="tr_'.$i.'">';
			$details_report .='<td width="30" align="center">'.$i.'</td>
								<td width="130" align="center" ><p>'.$company_library[$row[csf("company_name")]].'</p></td>
								<td width="60" align="center" ><p>'.$row[csf("job_no_prefix_num")].'</p></td>
								<td width="60" align="center" ><p>'.$row[csf("year")].'</p></td>
								<td width="100" align="center" ><p>'.$buyer_arr[$row[csf("buyer_name")]].'</p></td>
								<td width="100" align="center" ><p>'.$buyer_arr[$row[csf("client_id")]].'</p></td>
								<td width="110" align="center"><p>'.$row[csf("po_number")].'</p></td>
								<td width="125" align="center" ><p>'.$delv_comp.'</p></td>
								<td width="125" align="center" ><p>'.$location_library[$row[csf("del_location")]].'</p></td>
								<td width="125" align="center" ><p>'.$floor_no.'</p></td>
								<td width="120" align="center"><p>'.$challan_no.'</p></td>
								<td width="100" align="center"><p>';
								$inv_id=""; $lc_sc_no=""; $ship_mode="";
								foreach($invoce_id_arr as $invoice_id)
								{
									if($inv_id=="") $inv_id=$invoice_array[$invoice_id]; else $inv_id=$inv_id.",".$invoice_array[$invoice_id];
									if($ship_mode=="") $ship_mode=$shipment_mode[$shipping_mode_array[$invoice_id]]; else $ship_mode=$ship_mode.",".$shipment_mode[$shipping_mode_array[$invoice_id]];
									if($lc_sc_type_arr[$invoice_id]==1)
									{
										if($lc_sc_no=="") $lc_sc_no=$lc_num_arr[$lc_sc_id_array[$invoice_id]]; else $lc_sc_no.=",".$lc_num_arr[$lc_sc_id_array[$invoice_id]];
									}
									else
									{
										if($lc_sc_no=="") $lc_sc_no=$sc_num_arr[$lc_sc_id_array[$invoice_id]]; else $lc_sc_no.=",".$sc_num_arr[$lc_sc_id_array[$invoice_id]];
									}
								}

			$details_report .=$inv_id.'</p></td>
								<td width="100" align="center"><p>'.$lc_sc_no.'</p></td>
								<td width="80"><p>'.$design_source_arr[$row[csf("DESIGN_SOURCE_ID")]].'</p></td>
								<td width="100"><p>'.$row[csf("style_ref_no")].'</p></td>
								<td width="100"><p>'.$row[csf("style_description")].'</p></td>
								<td width="110" align="center"><p>';
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
								$current_ex_Fact_Qty=$row[csf("ex_factory_qnty")];
								$ex_rtn =$exFct_Rtn_Qty[$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("sys_number")]]['ex_rtn_qnty'];

								$total_current_ex_Fact_Qty= ($row[csf("ex_factory_qnty")]-$ex_rtn);
								//  echo $total_current_ex_Fact_Qty."<br>";
										// echo "<pre>";
								//print_r($exFct_Rtn_Qty);die;
								$basic_qnty=($total_ex_fact_qty*$item_smv)/$basic_smv_arr[$row[csf("company_name")]];
								$cm_per_pcs_tot+=($cm_per_pcs*$current_ex_Fact_Qty);
								$short_excess=$total_ex_fact_qty-$po_quantity;

								$excess_msg=($short_excess>0) ?" color:green;":"color:black;";
								$excess_val_msg=(($total_ex_fact_qty*$unit_price)-$po_quantity>0) ?" color:green;":"color:black;";
								$ttl_ex_qty=($total_ex_fact_qty/$po_quantity)*100;
								$ttl_ex_qty_msg=($ttl_ex_qty>100) ?" color:green;":"color:black;";

								// ============================ calculate upcharge ===============================
								$po_wise_ttl_ex_qty[$row[csf("po_id")]] += $current_ex_Fact_Qty;
								// echo $poExQtyArray[$row[csf("po_id")]]['poQty'] ."<=". $po_wise_ttl_ex_qty[$row[csf("po_id")]]."<br>";

								if($poExQtyArray[$row[csf("po_id")]]['poQty'] >= $po_wise_ttl_ex_qty[$row[csf("po_id")]])
								{
									$current_ex_up_charge = ($row[csf('up_charge')]/$po_quantity)*$current_ex_Fact_Qty;
									$current_ex_up_charge_value = $current_ex_up_charge + ($current_ex_Fact_Qty*$unit_price);

									$po_wise_ttl_up_charge[$row[csf("po_id")]] += $current_ex_up_charge;

									// echo $current_ex_up_charge ."+((".$current_ex_Fact_Qty."-".$excessExQty.")*".$unit_price.")<br>";

									if($total_ex_fact_qty>$poExQtyArray[$row[csf("po_id")]]['poQty'])
									{
										$total_ex_up_charge = ($row[csf('up_charge')]/$po_quantity)*$poExQtyArray[$row[csf("po_id")]]['poQty'];
										$total_ex_up_charge_value = $total_ex_up_charge + ($total_ex_fact_qty*$unit_price);
									}
									else
									{
										$total_ex_up_charge = ($row[csf('up_charge')]/$po_quantity)*$total_ex_fact_qty;
										$total_ex_up_charge_value = $total_ex_up_charge + ($total_ex_fact_qty*$unit_price);
									}

									$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['current_ex_up_charge_value'] += $current_ex_up_charge_value;
								}
								else
								{
									$excessExQty = $po_wise_ttl_ex_qty[$row[csf("po_id")]] - $poExQtyArray[$row[csf("po_id")]]['poQty'];
									$bal_qty = $current_ex_Fact_Qty-$excessExQty;
									if($bal_qty<1) $bal_qty=0;
									$current_ex_up_charge = ($row[csf('up_charge')]/$po_quantity)*$bal_qty;
									// echo $excess_ex_up_charge = ($row[csf('up_charge')]/$po_quantity)*$excessExQty;


									$current_ex_up_charge_value = $current_ex_up_charge + ($current_ex_Fact_Qty*$unit_price);

									if($bal_qty<1) $bal_qty=1;
									$current_ex_up_charge = ($row[csf('up_charge')]/$po_quantity)*$bal_qty;

									// echo $po_wise_ttl_up_charge[$row[csf("po_id")]];

									// echo $current_ex_up_charge ."+".$current_ex_Fact_Qty."*".$unit_price."<br>";

									$total_ex_up_charge = $row[csf('up_charge')];
									// $total_ex_up_charge = ($row[csf('up_charge')]/$po_quantity)*($total_ex_fact_qty-$excessExQty);
									// echo "(".$row[csf('up_charge')]."/".$po_quantity.")*(".$total_ex_fact_qty."-".$excessExQty.")<br>";
									$total_ex_up_charge_value = $total_ex_up_charge + ($total_ex_fact_qty*$unit_price);
									// echo $total_ex_up_charge ."+".$total_ex_fact_qty."*".$unit_price."<br>";

									$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['current_ex_up_charge_value'] += $current_ex_up_charge_value;
								}

			$total_sales_minutes=($current_ex_Fact_Qty*$item_smv);
			$gr_sales_min+=$total_sales_minutes;

								$temp_focclaim_arr=array();
								foreach(explode(',',$row[csf("foc_or_claim")]) as $foc_id){
									$temp_focclaim_arr[$foc_id]=$foc_claim_arr[$foc_id];
								}
								$row[csf("foc_or_claim")]=implode(',',$temp_focclaim_arr);



			$details_report .=$item_name_all.'</p></td>
								<td width="80" align="center"><p>'.number_format($item_smv,2).'</p></td>
								<td width="70" align="center"><p>'.change_date_format($row[csf("shipment_date")]).'</p></td>
								<td width="70" align="center"><a href="##" onclick="openmypage_ex_date('.$comapny_id.",'".$row[csf('po_id')]."','".$row[csf('item_number_id')]."','".$ex_fact_date_range."','ex_date_popup','".$row[csf('challan_id')]."'".",'1'".')">'.change_date_format($row[csf('ex_factory_date')]).'</a></td>
								<td width="100" align="center"><p>'.$shipment_mode[$row[csf('ship_mode')]].'</p></td>
								<td width="70" align="center"><p>'.$shipment_mode[$row[csf("shiping_mode")]].'</p></td>
								<td width="100" align="center"><p>'.$row[csf("foc_or_claim")].'</p></td>

								<td width="60" align="center" style="'.$diff_color.'"><p>('.$diff.')</p></td>
								<td width="100" align="center"><p>'.$unit_of_measurement[$row[csf('order_uom')]].'</p></td>
								<td width="80" align="right"><p>'. number_format($po_quantity,0,'', '').'</p></td>
								<td width="70" align="right"><p>'. number_format($unit_price,4).'</p></td>
								<td width="100" align="right"><p>'. number_format(($po_quantity*$unit_price),2).'</p></td>
								<td width="80" align="right"><p>'.number_format($row[csf("up_charge")],2,'.', '').'</p></td>
								<td width="80" align="right"><p><a href="##" onclick="openmypage_ex_date('.$comapny_id.",'".$row[csf('po_id')]."','".$row[csf('item_number_id')]."','".$ex_fact_date_range."','ex_date_popup','".$row[csf('challan_id')]."'".",'1'".')">'. number_format($current_ex_Fact_Qty,0,'.', '').'</a></p></td>

								<td width="80" align="right">'.number_format($ex_rtn ,2).'</td>

								<td width="80" align="right">'.number_format($total_current_ex_Fact_Qty =($current_ex_Fact_Qty-$ex_rtn),2).'</td>
								<td width="100" align="right"><p>'. number_format( $current_ex_fact_value=$total_current_ex_Fact_Qty*$unit_price,2).'</p></td>



								<td width="100" align="right">'.number_format($current_ex_up_charge,2).'</td>
								<td width="100" align="right">'.number_format($current_ex_up_charge_value,2).'</td>

								<td width="80" align="right"><p>'. number_format($row[csf("carton_qnty")],0,'.', '').'</p></td>
								<td width="80" align="right"><p><a href="##" onclick="openmypage_ex_date('.$comapny_id.",'".$row[csf('po_id')]."','".$row[csf('item_number_id')]."','".$total_exface_qnty."','ex_date_popup','".$row[csf('challan_id')]."'".",'1'".')">'.number_format($total_ex_fact_qty,0,'.', '').'</a></p></td>
								<td width="100" align="right"><p>'. number_format($total_ex_fact_value=$total_ex_fact_qty*$unit_price,2).'</p></td>

								<td width="100" align="right">'.number_format($total_ex_up_charge,2).'</td>
								<td width="100" align="right">'.number_format($total_ex_up_charge_value,2).'</td>

								<td width="80" align="right"><p>'.number_format($total_cartoon_qty,0,'.', '').'</p></td>
								<td width="100" align="right"><p>'. number_format($total_sales_minutes,0,'', '').'</p></td>
								<td width="80" align="right"><p>'.number_format($basic_qnty,0,'','').'</p></td>
								<td width="80" align="right" style="'.$excess_msg.'" ><p>'. number_format($excess_shortage_qty=$total_ex_fact_qty-$po_quantity,0,'', '').'</p></td>
								<td width="100" align="right" style="'.$excess_val_msg.'"><p>'. number_format($excess_shortage_value=($excess_shortage_qty*$unit_price),2).'</p></td>
								<td align="center" style="'.$ttl_ex_qty_msg.'" width="80"><p>'. number_format($total_ex_fact_qty_parcent=($total_ex_fact_qty/$po_quantity)*100,0).'</p></td>
								<td width="60" align="center" title="CM per pcs: '.number_format($cm_per_pcs,4).'*'.$current_ex_Fact_Qty.'"><p>'.number_format($cm_per_pcs*$current_ex_Fact_Qty,2).'</p></td>
								<td width="100" align="center"><p>'.$forwarder.'</p></td>
								<td width="80" align="center"><p>'.$vehi_no.'</p></td>
								<td width="130"><p>'.$dirver_info.'</p></td>
								<td width="70" align="center"><p>'.(change_date_format($inspection_date_arr[$row[csf('po_id')]]) == '0000-00-00' || change_date_format($inspection_date_arr[$row[csf('po_id')]]) == '' ? '' : change_date_format(change_date_format($inspection_date_arr[$row[csf('po_id')]]))).'</p></td>
								<td align="center"><p>'.$shipment_status[$row[csf('shiping_status')]].'</p></td>
							</tr>';

			//$master_data[$row[csf("buyer_name")]]['po_qnty'] +=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]];
 	  		//$master_data[$row[csf("buyer_name")]]['po_value'] +=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]]*$row[csf("unit_price")];
 	  		$total_po_qty+=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]];
 	  		$total_po_valu+=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]]*$row[csf("unit_price")];

			$total_Ex_Qty_data += $ex_rtn;
			//$totalEx_Rtn_Data += ($current_ex_Fact_Qty-$ex_rtn );
			//echo $totalEx_Rtn_Data;
			$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['ex_factory_qnty'] +=$row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]][$row[csf("challan_id")]];
			$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['ex_factory_value'] +=($row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]][$row[csf("challan_id")]])*$row[csf("unit_price")];

			$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['SYS_NUMBER'] = $row[csf("sys_number")];

			$CrntExFctVlue += $current_ex_fact_value ;


			$total_basic_qty+=$basic_qnty;

			$total_ex_qty+=$row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]][$row[csf("challan_id")]];
			$total_crtn_qty+=$row[csf("carton_qnty")];
			$total_ex_valu+=($row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]][$row[csf("challan_id")]])*$row[csf("unit_price")];
			$g_total_ex_qty+=$total_ex_fact_qty;
			$g_total_ex_crtn+=$total_cartoon_qty;
			$g_total_ex_val+=$total_ex_fact_qty*$row[csf("unit_price")];
			$g_sales_minutes+=$total_sales_minutes;

			// $g_sales_minutes+=$current_ex_Fact_Qty*$item_smv;
			$total_eecess_storage_qty+=$excess_shortage_qty;
			$total_eecess_storage_val+=$excess_shortage_value;
			// if($po_check_arr[$row[csf("po_id")]][$row[csf("item_number_id")]]=="")  //if condition applied some Duplicate PO,Item can't count and gives Worong qty
			// {
				$po_check_arr[$row[csf("po_id")]][$row[csf("item_number_id")]]=$row[csf("po_id")];
				$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['b_id']=$row[csf("buyer_name")];
				$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['org_po_qnty'] +=$row[csf("po_quantity")];
				$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['upchage'] +=$row[csf("up_charge")];
				$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['po_qnty'] +=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]];
				$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['po_value'] +=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]]*$row[csf("unit_price")];
				$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['basic_qnty'] +=$basic_qnty;
				$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['total_ex_fact_qty'] +=$total_ex_fact_qty;
				$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['total_ex_fact_value'] +=$total_ex_fact_qty*$row[csf("unit_price")];


				$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['current_ex_up_charge'] += $current_ex_up_charge;
				$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['current_ex_fact_value'] += $current_ex_fact_value;
				// $master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['current_ex_up_charge_value'] += $current_ex_up_charge_value;
				$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['total_ex_up_charge'] += $total_ex_up_charge;
				$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['total_ex_up_charge_value'] += $total_ex_up_charge_value;

 	  			$gr_po_qnty_pcs+=$po_quantity;
	  			$gr_po_qnty_val+=$po_quantity*$unit_price;
 	  			$gr_ttl_ex_qnty+=$total_ex_fact_qty;
	  			$gr_ttl_ex_qnty_val+=$total_ex_fact_value;
	  			$gr_ttl_carton_qt+=$total_cartoon_qty;
	  			//$gr_sales_min+=$total_sales_minutes;
	  			$gr_ttl_basic_qty+=$basic_qnty;
	  			$gr_ttl_ex_fac_per+=$total_ex_fact_qty_parcent;
	  			$gr_ttl_short_qty+=$excess_shortage_qty;
	  			$gr_ttl_short_val+=$excess_shortage_value;
	  			$gr_ttl_sales_cm+=$cm_per_pcs*$total_ex_fact_qty;
				$total_po_val+=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]]*$row[csf("unit_price")];
				$gr_upcharge+=$row[csf("up_charge")];

				$gr_current_ex_up_charge += $current_ex_up_charge;
				$gr_current_ex_up_charge_value += $current_ex_up_charge_value;

				$gr_total_ex_up_charge += $total_ex_up_charge;
				$gr_total_ex_up_charge_value += $total_ex_up_charge_value;


				//$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['sales_min'] += $item_smv*$total_ex_fact_qty;

			// }


			$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['sales_min'] += $item_smv*($row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]][$row[csf("challan_id")]]);

			$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['sales_cm'] += $cm_per_pcs*$current_ex_Fact_Qty;
			// echo $cm_per_pcs."*".$current_ex_Fact_Qty."<br>";




			$i++; $item_name_all="";
		}
		$pp=$i;
		// echo "<pre>";print_r($master_data);die();
		?>
        <div style="width:4800x;">
            <div style="width:1910px" >
                <table width="1910"  cellspacing="0"  align="center">
                    <tr>
                        <td align="center" colspan="15" class="form_caption">
                            <strong style="font-size:16px;">Company Name:<? echo  $company_library[$cbo_company_name] ;?></strong>
                        </td>
                    </tr>
                    <tr class="form_caption">
                        <td colspan="15" align="center" class="form_caption"> <strong style="font-size:15px;">Ex-Factory Report</strong></td>
                    </tr>
                     <tr align="center">
                        <td colspan="15" align="center" class="form_caption"> <strong style="font-size:15px;">Total Summary</strong></td>
                    </tr>
                </table>
                <table width="2110" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1">
                    <thead>
                        <th width="30">SL</th>
                        <th width="110">Buyer Name</th>
                        <th width="100">Client</th>
                        <th width="100">PO Qty</th>
                        <th width="100">PO Qty (pcs)</th>
                        <th width="130">PO Value</th>
                        <th width="100">PO Value(%)</th>
                        <th width="100">PO TTL Up-Charge</th>
                        <th width="100">Current Ex-Fact. Qty.</th>
						<th width="100">Current Return Ex-Fact.Qty (Pcs).</th>
						<th width="100">Total Current Ex-Fact. Qty (pcs).</th>

                        <th width="130">Current Ex-Fact. Value</th>
                        <th width="130">Up Charge with Current Ex-Fact. Value</th>
                        <th width="100">Total Ex-Fact. Qty.</th>
                        <th width="130">Total Ex-Fact. Value</th>
                        <th width="130">Up Charge with Total Ex-Fact. Value</th>
                        <th width="100" title="Item SMV*Current Ex-Fact. Qty.">Sales Minutes</th>
                        <th width="100">Sales CM</th>
                        <th width="100">Total Ex-Fact. (Basic Qty)</th>
                        <th>Total Ex-Fact. Value %</th>
                    </thead>
					<?
                $m=1; $grand_sales_minute =0;
                foreach($master_data as $buyid=>$buyData)
                {
					foreach($buyData as $clientid=>$cdata)
					{
						// echo "<pre>";
						// print_r($cdata);
						if ($m%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$buyer_po_quantity=0; $buyer_po_value=0; $current_ex_Fact_Qty=0; $current_ex_fact_value=0; $total_ex_fact_qty=0; $total_ex_fact_value=0; $g_sales_min=0;$g_sales_cm=0;


						$po_quantity=$cdata['po_qnty'];
						$buyer_po_value=$cdata["po_value"];
						$parcentages+=($buyer_po_value/$total_po_val)*100;
						$current_ex_Fact_Qty=$cdata['ex_factory_qnty'];
						$current_ex_fact_value=$cdata['ex_factory_value'];
						$total_ex_fact_qty=$cdata['total_ex_fact_qty'];
						$total_ex_fact_value=$cdata['total_ex_fact_value'];
						$buyer_basic_qnty=$cdata["basic_qnty"];
						$total_ex_fact_value_parcentage=($total_ex_fact_value/$buyer_po_value)*100;
						$CrntExVl=$cdata['current_ex_fact_value'];
						?>
						<tr bgcolor="<?=$bgcolor;?>" onclick="change_color('tr_<?=$i;?>','<?=$bgcolor;?>')" id="tr_<?=$i;?>" >
							<td width="30" align="center"><?=$m;?></td>
							<td width="110" style="word-break:break-all"><?=$buyer_arr[$buyid];?></td>
							<td width="100" style="word-break:break-all"><?=$buyer_arr[$clientid];?></td>
							<td width="100" style="word-break:break-all" align="right"><?=number_format($cdata["org_po_qnty"], 0);?></td>
							<td width="100" style="word-break:break-all" align="right"><?=number_format($po_quantity, 0);?></td>
							<td width="130" style="word-break:break-all" align="right"><p id="value_<?=$i;?>"><?=number_format($buyer_po_value, 2, '.', '');?></p></td>
							<td width="100" style="word-break:break-all" align="right"><?=number_format(($buyer_po_value / $total_po_val) * 100, 2, '.', '');?></td>
                            <td width="100" style="word-break:break-all" align="right"><?=number_format($cdata["upchage"], 2, '.', '');?> </td>
							<td width="100" style="word-break:break-all" align="right"><?=number_format($current_ex_Fact_Qty, 0, '', '');?></td>


							<td width="130" style="word-break:break-all" align="right"><?=number_format( $total_ExFctQty= $exFct_Rtn_Qty_Buyer_wise[$buyid][$clientid]['PrQtyBuyer'], 2, '.', '');?></td>
							<td width="130" style="word-break:break-all" align="right"><?=number_format($final=$current_ex_Fact_Qty-$total_ExFctQty, 2, '.', '');?></td>



							<td width="130" style="word-break:break-all" align="right"><?=number_format($CrntExVl, 2, '.', '');?></td>



							<td width="130" style="word-break:break-all" align="right"><?=number_format($cdata['current_ex_up_charge_value'], 2, '.', '');?></td>
							<td width="100" style="word-break:break-all" align="right"><?=number_format($total_ex_fact_qty, 0, '', '');?></td>
							<td width="130" style="word-break:break-all" align="right"><?=number_format($total_ex_fact_value, 2, '.', '');?> </td>
							<td width="130" style="word-break:break-all" align="right"><?=number_format($cdata['total_ex_up_charge_value'], 2, '.', '');?> </td>
							<td width="100" style="word-break:break-all" align="right"><? echo $g_sales_min+= number_format($cdata["sales_min"],0,'',''); ?></td>
							<td width="100" style="word-break:break-all" align="right"><? echo $g_sales_cm+= number_format($cdata["sales_cm"],0,'','');?></td>
							<td width="100" style="word-break:break-all" align="right"><?=number_format($buyer_basic_qnty, 0, '', '');?></td>
							<td style="word-break:break-all" align="right"><?=number_format($total_ex_fact_value_parcentage, 0)?> %</td>
						</tr>
						<?
						$i++; $m++;

						$grand_sales_minute +=number_format($cdata["sales_min"],0,'','');
						$grand_sales_cm +=number_format($cdata["sales_cm"],0,'','');
						$total_buyer_org_po_quantity+=$cdata["org_po_qnty"];
						$total_buyer_po_quantity+=$po_quantity;
						$total_buyer_po_value+=$buyer_po_value;
						$ttl_current_ex_Fact_Qty +=$current_ex_Fact_Qty;
						$total_current_ex_fact_value+=$CrntExVl;
						$mt_total_ex_fact_qty+=$total_ex_fact_qty;
						$mt_total_ex_fact_value+=$total_ex_fact_value;
						$total_buyer_basic_qnty +=$buyer_basic_qnty;
						$buyerTotUpCharge +=$cdata["upchage"];
						$totalByer_ext += $total_ExFctQty ;
						$final_data += $final ;

					}
                }

				?>
				  <tfoot style="background-color: #DCF2F1;font-weight:bold">
                    	<td width="30">&nbsp;</td>
                        <td width="110" align="right"><b>Total:</b></td>
                        <td width="100">&nbsp;</td>
                        <td width="100" align="right" id="total_buyer_org_po_quantity"><?=number_format($total_buyer_org_po_quantity, 0);?></td>
                        <td width="100"align="right" id="total_buyer_po_quantity"><?=number_format($total_buyer_po_quantity, 0);?></td>
                        <td width="130" align="right" id="value_total_buyer_po_value"><?=number_format($total_buyer_po_value, 2, '.', '');?></td>
                        <td width="100" align="right" id="parcentages"><?=ceil($parcentages);?></td>
                        <td width="100" align="right" id="value_upcharge"><?=number_format($buyerTotUpCharge, 2);?></td>
                        <td width="100" align="right" id="total_current_ex_Fact_Qty"><?=number_format($ttl_current_ex_Fact_Qty, 0);?></td>


						<td width="130" align="right" id="value_total_current_ex_fact_value"><?=number_format($totalByer_ext, 2);?></td>
						<td width="130" align="right" id="value_total_current_ex_fact_value"><?=number_format($final_data, 2);?></td>



                        <td width="130" align="right" id="value_total_current_ex_fact_value"><?=number_format($total_current_ex_fact_value, 2);?></td>
                        <td width="130" align="right" id="value_total_current_ex_fact_value_with_up_charge"><? //=number_format($total_current_ex_fact_value, 2);?></td>
                        <td width="100" align="right" id="mt_total_ex_fact_qty"><?=number_format($mt_total_ex_fact_qty, 0);?></td>
                        <td width="130" align="right" id="value_mt_total_ex_fact_value"><?=number_format($mt_total_ex_fact_value, 2);?></td>
                        <td width="130" align="right" id="value_mt_total_ex_fact_value_with_up_charge"><? //=number_format($mt_total_ex_fact_value, 2);?></td>
                        <td width="100" align="right" id="sales_minutes"><?=number_format($grand_sales_minute, 2);?></td>
                        <td width="100" align="right" id="sales_cm"><?=number_format($grand_sales_cm, 2);?></td>
                        <td width="100" align="right" id="total_buyer_basic_qnty"><?=number_format($total_buyer_basic_qnty, 0);?></td>
                        <td>&nbsp;</td>
                    </tfoot>
             </table>

            <br />
            <!-- ==================================== details part ================================== -->
            <div>
                <table width="4820">
                    <tr>
                    	<td colspan="49" class="form_caption"><strong style="font-size:16px;">Shipped Out Order Details Report</strong></td>
                    </tr>
                </table>
                <table width="4825" border="1" class="rpt_table" rules="all" id="table_header_2">
                    <thead>
                        <th width="30">SL</th>
                        <th width="130">Company</th>
                        <th width="60">Job</th>
                        <th width="60">Year</th>
                        <th width="100">Buyer Name</th>
                        <th width="100">Client</th>
                        <th width="110">Order NO</th>
                        <th width="125">Del Company</th>
                        <th width="125">Del Location</th>
                        <th width="125">Del Floor</th>
                        <th width="120">Challan NO</th>
                        <th width="100">Invoice NO</th>
                        <th width="100">LC/SC NO</th>
                        <th width="80">Design Source</th>
                        <th width="100">Style Ref. no.</th>
                        <th width="100">Style Description</th>
                        <th width="110">Item Name</th>
                        <th width="80">Item SMV</th>
                        <th width="70">Shipment Date</th>
                        <th width="70">Ex-Fac. Date</th>
                        <th width="100"><p>Po Rcv.Ship Mode</p></th>
                        <th width="70">Shipping Mode</th>
                        <th width="100">FOC/Claim</th>
                        <th width="60">Days in Hand</th>
                        <th width="100">UOM</th>
                        <th width="80">PO Qtny. (pcs)</th>
                        <th width="70">Unit Price</th>
                        <th width="100">PO Value</th>
                        <th width="80">PO TTL Up-Charge</th>
                        <th width="80">Current Ex-Fact. Qty (pcs)</th>


						<th width="80">Current Return Ex-Fact.Qty (Pcs)</th>
						<th width="80">Total Current Ex-Fact. Qty (pcs)</th>

                        <th width="100">Current Ex-Fact. Value</th>

                        <th width="100">UP Charge Based on Current Ex-facotry</th>
                        <th width="100">Current Ex-Fact. Value with Up Charge</th>

                        <th width="80">Current Carton Qty</th>
                        <th width="80">Total Ex-Fact. Qty.</th>
                        <th width="100">Total Ex-Fact. Value</th>

                        <th width="100">Up Charge Based on TTL Ex-factory</th>
                        <th width="100">Up charge with Total Ex-Fact. Value</th>

                        <th width="80">Total Carton Qty</th>
                        <th width="100" title="Item SMV*Current Ex-Fact. Qty.">Sales Minute</th>
                        <th width="80">Total Ex-Fact. (Basic Qty)</th>
                        <th width="80">Excess/ Shortage Qty</th>
                        <th width="100">Excess/ Shortage Value</th>
                        <th width="80">Total Ex-Fact. Qty. %</th>
                        <th width="60">Sales CM</th>
                        <th width="100">C & F Name</th>
                        <th width="80">Vehicle No</th>
                        <th width="130">Driver Info</th>
                        <th width="70">Inspection Date</th>
                        <th>Ex-Fact Status</th>
                    </thead>
                </table>
            <div style="width:4825px; overflow-y:scroll; overflow-x:hidden; max-height:300px;"  id="scroll_body" >
            <? echo $details_report;
            	foreach($subcon_exfactory_arr___ as $po_id=>$item_data)
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
	            						<tr onclick="<?=$onclick;?>" id="tr2_<?=$jj;?>" >
	            							<td width="30" align="center"><? echo $pp++;?></td>
	            							<td width="130" align="center"><p><? echo $company_library[$row["company_name"]]; ?> </p></td>
	            							<td width="60" align="center"><p><? echo $row["job"]; ?> </p></td>
	            							<td width="60" align="center"><p><? $arr_year=explode('-',change_date_format($row["insert_date"]));echo $arr_year[2]; ?> </p></td>
	            							<td width="100" align="center"><p><? echo $row["buyer_name"]; ?> </p></td>
                                            <td width="100" align="center">&nbsp;</td>
	            							<td width="110" align="center"><p><? echo $row["po_number"]; ?> (In-Sub) </p></td>
	            							<td width="125" align="center"><p><? echo $company_library[$delivery_company_id]; ?> </p></td>
	            							<td width="125" align="center"><p><? echo $location_library[$delivery_loc_id]; ?> </p></td>
	            							<td width="125" align="center"><p>&nbsp;</p></td>
	            							<td width="120" align="center"><p><? echo $row["delivery_no"]; ?> </p></td>
	            							<td width="100" align="center"><p>&nbsp;</p></td>
	            							<td width="100" align="center"><p>&nbsp;<? //echo $lc_sc_no; ?> </p></td>
	            							<td width="100"><p><? echo $row["style_ref_no"]; ?> </p></td>
	            							<td width="100"><p>&nbsp;<? //echo $row["style_description")]; ?> </p></td>
	            							<td width="110" align="center"><p><? echo $garments_item[$item_id];?></p></td>
	            							<td width="80" align="center"><p><? echo $item_smv=$row["smv"]; ?> </p></td>
	            							<td width="70" align="center"><p><? echo change_date_format($row["shipment_date"]); ?> </p></td>
	            							<td width="70" align="center"><a href="##" onclick="openmypage_ex_date(<? echo $row["company_name"].",'".$po_id."','".$item_id."','".$date_id."','ex_date_popup','".$row['delivery_no']."'".',2'; ?> )"><? echo change_date_format($date_id); ?> </a></td>
	            							<td width="100" align="center"><p><? echo $shipment_mode[$row['ship_mode']]; ?> </p></td>
	            							<td width="70" align="center"><p><? echo $shipment_mode[$row["shiping_mode"]]; ?> </p></td>
	            							<td width="100" align="center"><p><? echo $foc_or_claim[$row["foc_or_claim"]]; ?> </p></td>
	            							<td width="60" align="center" style=" <?=$diff_color;?>"><p>(<? echo $diff; ?> )</p></td>
	            							<td width="100" align="center"><p> <? echo $unit_of_measurement[$row["order_uom"]]; ?>  </p></td>
	            							<td width="80" align="right"><p><? echo  number_format($po_quantity,0,'', ''); ?> </p></td>
	            							<td width="70" align="right"><p><? echo  number_format($unit_price,4); ?> </p></td>
	            							<td width="100" align="right"><p><? echo  number_format(($po_quantity*$unit_price),2); ?> </p></td>


	            							<td width="80" align="right"><p><a href="##" onclick="openmypage_ex_date(<? echo $row["company_name"].",'".$po_id."','".$item_id."','".$date_id."','ex_date_popup','".$row['delivery_no']."'".',2'; ?> )"><?
	            							$current_ex_Fact_Qty=$row["prod_qty"]; echo  number_format($current_ex_Fact_Qty,0,'.', ''); ?> </a></p></td>
	            							<td width="100" align="right"><p><? echo  number_format( $current_ex_fact_value=$current_ex_Fact_Qty*$unit_price,2); ?> </p></td>

                                            <td width="80" align="right">&nbsp;</td>
	            							<td width="80" align="right"><p><? echo  number_format($row["total_carton_qnty"],0,'', ''); ?> </p></td>
	            							<td width="80" align="right"><p><a href="##" onclick="openmypage_ex_date(<? echo $row["company_name"].",'".$po_id."','".$item_id."','".$all_date."','ex_date_popup','".$delivery_company_id.'_'.$delivery_loc_id."'".',2'; ?> )" ><? echo number_format($total_ex_fact_qty,0,' ', ''); ?> </a></p></td>
	            							<td width="100" align="right"><p><? echo  number_format($total_ex_fact_value=$total_ex_fact_qty*$unit_price,2); ?> </p></td>
	            							<td width="80" align="right"><p><? echo number_format($total_cartoon_qty,0,'.', ''); ?> </p></td>
	            							<td width="100" align="right"><p><? echo  number_format($total_sales_minutes=$total_ex_fact_qty*$item_smv); ?> </p></td>
	            							<td width="80" align="right"><p><? echo number_format($basic_qnty,0,'',''); ?> </p></td>
	            							<td width="80" align="right" style=" <? echo $excess_msg; ?> " ><p><? echo  number_format($excess_shortage_qty=$total_ex_fact_qty-$po_quantity,0,'', ''); ?> </p></td>
	            							<td width="100" align="right" style=" <? echo $excess_val_msg; ?> "><p><? echo  number_format($excess_shortage_value=($total_ex_fact_qty*$unit_price)-$po_quantity,2); ?> </p></td>
	            							<td align="center" style=" <? echo $ttl_ex_qty_msg; ?> " width="80"><p><? echo  number_format($total_ex_fact_qty_parcent=($total_ex_fact_qty/$po_quantity)*100,0); ?> </p></td>
	            							<td width="60" align="center" title="<?=$cm_per_pcs."*".$current_ex_Fact_Qty;?>"><p><? echo number_format($cm_per_pcs*$current_ex_Fact_Qty,2); ?> </p></td>
	            							<td width="100" align="center"><p><? echo $forwarder; ?> </p></td>
	            							<td width="80" align="center"><p><? echo $row["vehical_no"]; ?> </p></td>
	            							<td width="130"><p><? echo $dirver_info; ?> </p></td>
	            							<td width="70" align="center"><p><? echo (change_date_format($inspection_date_arr[$row['po_id']]) == '0000-00-00' || change_date_format($inspection_date_arr[$row['po_id']]) == '' ? '' : change_date_format(change_date_format($inspection_date_arr[$row['po_id']]))); ?> </p></td>
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

            <table width="4825" cellspacing="0" cellpadding="0"  border="1"  class="rpt_table" rules="all" id="table_footer">
                <tfoot>
                    <tr>
                    	<th width="30">&nbsp;</th>
                        <th width="130">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="125">&nbsp;</th>
                        <th width="125">&nbsp;</th>
                        <th width="125">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100" >&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="100" align="right"><strong>Total</strong></th>
                        <th width="80" id="total_po_qtybk" align="right"><? echo  number_format($gr_po_qnty_pcs,0);?></th>
                        <th width="70" align="right">&nbsp;</th>
                        <th width="100" align="right" id="value_total_po_valubk"><? echo  number_format($gr_po_qnty_val,2); ?></th>

                        <th width="80" align="right" id="value_tdupcharge"><? echo number_format($gr_upcharge,0);?></th>
                        <th width="80" align="right" id="total_ex_qty"><? echo number_format($total_ex_qty,0); ?></th>
                        <th width="80" align="right" id="total_ex_qty2"><? echo number_format($total_Ex_Qty_data,0); ?></th>
                        <th width="80" align="right" id="total_ex_qty3"><? echo number_format($total_ex_qty-$total_Ex_Qty_data,0); ?></th>
                        <th width="100" align="right" id="value_total_ex_valu"><? echo number_format((($total_ex_qty-$total_Ex_Qty_data)*$unit_price),2);?></th>

                        <th width="100" align="right" id="value_current_ex_up_charge"><? echo number_format($gr_current_ex_up_charge,2); ?></th>
                        <th width="100" align="right" id="value_current_ex_up_charge_value"><? echo number_format($gr_current_ex_up_charge_value,2); ?></th>

                        <th width="80" align="right" id="total_crtn_qty"><? echo number_format($gr_ttl_carton,0); ?></th>
                        <th width="80" align="right" id="g_total_ex_qtybk"><? echo number_format($gr_ttl_ex_qnty,0);?></th>
                        <th width="100" align="right" id="value_g_total_ex_valbk"><? echo number_format($gr_ttl_ex_qnty_val,2);?></th>

                        <th width="100" align="right" id="value_total_ex_up_charge"><? echo number_format($gr_total_ex_up_charge,2); ?></th>
                        <th width="100" align="right" id="value_total_ex_up_charge_value"><? echo number_format($gr_total_ex_up_charge_value,2); ?></th>

                        <th width="80" align="right" id="g_total_ex_crtnbk"><? echo number_format($gr_ttl_carton_qt,0);?></th>
                        <th width="100" align="right" id="value_sales_minutesbk"><? echo number_format($gr_sales_min);?></th>

                        <th width="80" align="right" id="total_basic_qtybk"><? echo number_format($gr_ttl_basic_qty,0); ?></th>
                        <th width="80" align="right" id="total_eecess_storage_qtybk"><? echo number_format($gr_ttl_short_qty,0);?></th>
                        <th width="100" align="right" id="value_total_eecess_storage_valbk"><? echo number_format($gr_ttl_short_val,0);?></th>
                        <th width="80" id="total_ex_perbk"><? echo number_format($gr_ttl_ex_fac_per,0);?></th>
                        <th width="60" align="right" id="value_cm_per_pcs_totbk"><? echo number_format($gr_ttl_sales_cm,2);?></th>
                        <th width="100">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="130">&nbsp;</th>
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
	else if($reportType==2) //Monthly button
	{

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


		$sql_res=sql_select("SELECT b.po_break_down_id as po_id,
				sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as return_qnty
			from pro_ex_factory_mst b  where  b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id");

		$ex_factory_qty_arr=array();
		foreach($sql_res as $row)
		{
			$ex_factory_qty_arr[$row[csf('po_id')]]['return_qty']	=	$row[csf('return_qnty')];
		}

		$sql= "SELECT b.id as po_id, (b.unit_price/c.total_set_qnty) as unit_price, c.total_set_qnty, c.id as job_id, c.job_no, c.buyer_name, c.company_name, c.set_smv,a.ex_factory_date,
		sum(CASE WHEN a.entry_form=85 THEN e.production_qnty ELSE 0 END) as return_qnty,
		sum(CASE WHEN a.entry_form!=85 THEN e.production_qnty ELSE 0 END) as qnty

		from pro_ex_factory_mst a, pro_ex_factory_dtls e, wo_po_break_down b, wo_po_details_master c,pro_ex_factory_delivery_mst d

		where a.po_break_down_id=b.id and a.id=e.mst_id and b.job_no_mst=c.job_no   $del_location_cond $del_floor_cond $del_comp_cond  $str_cond $company_cond2 $buyer_conds $internal_ref_cond $search_cond  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and d.is_deleted=0 and e.is_deleted=0 and c.status_active=1 and d.status_active=1 and e.status_active=1 and a.delivery_mst_id= d.id
		group by
         b.id, b.unit_price, c.total_set_qnty,c.id,
         c.job_no,
         c.buyer_name,
         c.company_name,
         c.set_smv,
         a.ex_factory_date
		order by a.ex_factory_date ASC ";//c.job_no

		//echo $sql; exit();

		$sql_result=sql_select($sql);
		$po_chk_arr = array();
		//print_r($sql_result);die;
		$buyer_qty_arr = array();
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

			$exfactreturn_qty = 0;
			if($po_chk_arr[$row[csf('po_id')]]=="")
			{
				$exfactreturn_qty=$ex_factory_qty_arr[$row[csf('po_id')]]['return_qty'];
				$po_chk_arr[$row[csf('po_id')]] = $row[csf('po_id')];
			}
			$basic_qnty=($row[csf("ex_factory_qnty")]*$row[csf("set_smv")])/$basic_smv_arr[$row[csf("company_name")]];
			$cm_val=$cm_value_rate*$row[csf("ex_factory_qnty")];
			$result_data_arr[date("Y-m",strtotime($row[csf("ex_factory_date")]))][$row[csf("buyer_name")]]['cm_value'] +=$cm_val;
			$result_data_arr[date("Y-m",strtotime($row[csf("ex_factory_date")]))][$row[csf("buyer_name")]]['ex_factory_qnty'] +=$row[csf("ex_factory_qnty")]-$exfactreturn_qty;
			$result_data_arr[date("Y-m",strtotime($row[csf("ex_factory_date")]))][$row[csf("buyer_name")]]['basic_qnty'] +=$basic_qnty;
			$result_data_arr[date("Y-m",strtotime($row[csf("ex_factory_date")]))][$row[csf("buyer_name")]]['lib_basic_qnty'] =$target_basic_qnty[$row[csf("buyer_name")]][date("Y-m",strtotime($row[csf("ex_factory_date")]))];

			$result_data_arr[date("Y-m",strtotime($row[csf("ex_factory_date")]))][$row[csf("buyer_name")]]['ex_factory_value'] +=($row[csf("ex_factory_qnty")]*$row[csf("unit_price")]);

			$buyer_tem_arr[$row[csf("buyer_name")]]						=		$row[csf("buyer_name")];
			$buyer_qty_arr[$row[csf("buyer_name")]]['qnty']				+=		$row[csf("qnty")];
			$buyer_qty_arr[$row[csf("buyer_name")]]['return_qnty']		+=		$row[csf("return_qnty")];
			$buyer_qty_arr[$row[csf("buyer_name")]]['unit_price']		=		$row[csf("unit_price")];
			$buyer_qty_arr[$row[csf("buyer_name")]]['ex_factory_value'] +=(($row[csf("qnty")]-$row[csf("return_qnty")])*$row[csf("unit_price")]);

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

			$buyer_wisi_data[$buyer_id]['lib_basic_qnty'] += $tot_lib_basic_qnty;
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
                        <th width="100" title="Ex-Factory Value-Commision Cost">Ex-Fac value without comm</th>
                        <th title="CM Value=((CM Cost+Margin Dzn)/Costing Par)*Exfactory Qty" >CM Value</th>
                    </tr>
                </thead>
                <tbody>
                <?
				$p=1;

				foreach($buyer_wisi_data as $buyer_id_ref=>$row)
				{
					$buyerXFactQty = $buyer_qty_arr[$buyer_id_ref]['qnty']	- $buyer_qty_arr[$buyer_id_ref]['return_qnty'];
					$totalXFactQty += $buyerXFactQty;
					$xFactVal = $buyer_qty_arr[$buyer_id_ref]['ex_factory_value'];
					$totalXFactVal += $xFactVal;
					//echo $buyer_qty_arr[$buyer_id_ref]['unit_price']; exit();
					if ($p%2==0)
					$bgcolor="#E9F3FF";
					else
					$bgcolor="#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                    	<td><? echo $buyer_arr[$buyer_id_ref]; ?></td>
                        <td align="right"><? echo number_format($row["lib_basic_qnty"],0); ?></td>
                        <td align="right"><? echo number_format($buyerXFactQty,0); ?></td>
                        <td align="right"><? echo number_format($xFactVal,2); ?></td>
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
                        <th><? echo number_format($totalXFactQty,0); ?></th>
                        <th><? echo number_format($totalXFactVal,2); ?></th>
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
                        <th width="100" title="Ex-Factory Value-Commision Cost">Ex-Fac value without comm</th>
                        <th width="100" title="CM Value=((CM Cost+Margin Dzn)/Costing Par)*Exfactory Qty">CM Value</th>
                        <?
                    }
                    ?>
               </tr>
            </thead>
         </table>
        <table width="<? echo $width;?>" cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table" id="" align="left">
			<?
            	echo $main_data;
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


		/*$exfact_sql=sql_select("select po_break_down_id,
		sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as return_qnty,
		 sum(total_carton_qnty) as carton_qnty from pro_ex_factory_mst where status_active=1 and is_deleted=0 group by po_break_down_id");*/
		 //pro_ex_factory_delivery_mst
		 $exfact_sql=sql_select("select b.po_break_down_id,
		sum(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as return_qnty,
		 sum(b.total_carton_qnty) as carton_qnty from pro_ex_factory_mst b,pro_ex_factory_delivery_mst a  where a.id=b.delivery_mst_id and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id");

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
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no $company_cond $str_cond $del_comp_cond $del_location_cond $del_floor_cond $buyer_conds $internal_ref_cond $search_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active  in(1,2,3) and c.is_deleted=0 and c.status_active  in(1,2,3) and a.delivery_mst_id=d.id
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
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no $company_cond $str_cond $del_comp_cond $del_location_cond $del_floor_cond $buyer_conds $internal_ref_cond $search_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active  in(1,2,3) and c.is_deleted=0 and c.status_active  in(1,2,3)  and a.delivery_mst_id=d.id
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
									//$po_id_arr[$row["po_id"]]=$row["po_id"];

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
								$master_data[$row["buyer_name"]]['ex_factory_qnty'] +=$row["ex_factory_qnty"]-$exfact_return_qty_arr[$row[csf("po_id")]];
								$master_data[$row["buyer_name"]]['ex_factory_value'] +=$row["ex_factory_qnty"]*$row["unit_price"];
								if($po_check_arr[$row[("po_id")]]=="")
								{
									//echo $total_ex_fact_qty.', ';
									$po_check_arr[$row[("po_id")]]=$row[("po_id")];
								$master_data[$row["buyer_name"]]['b_id']=$row["buyer_name"];
								$master_data[$row["buyer_name"]]['po_qnty'] +=$row["po_quantity"];
								$master_data[$row["buyer_name"]]['po_value'] +=$row["po_quantity"]*$row["unit_price"];
								$master_data[$row["buyer_name"]]['basic_qnty'] +=$basic_qnty;

								//if($po_id_arr[$row["po_id"]]=='')
									//{
									$master_data[$row["buyer_name"]]['total_ex_fact_qty'] +=$total_ex_fact_qty;
									$master_data[$row["buyer_name"]]['total_ex_fact_value'] +=$total_ex_fact_qty*$row["unit_price"];
									//$po_id_arr[$row["po_id"]]=101;
									//}


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

								}

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
					//echo implode(",",$po_id_arr);

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

                    	<th width="70">Inspection Date</th>
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
	else if($reportType==4) //Country button
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
		$all_conds.=($buyer_name)? " and d.buyer_name='$buyer_name'" : " ";
		$all_conds.=($delv_comp)? " and a.delivery_company_id in($delv_comp)" : " ";
		$all_conds.=($location_name)? " and a.delivery_location_id in($location_name)" : " ";
		$all_conds.=($delv_floor)? " and a.delivery_floor_id in($delv_floor)" : " ";
		$all_conds.=($shipping_status)? " and b.shiping_status =$shipping_status" : " ";
		$all_conds.=($cbo_brand_id)? " and d.brand_id =$cbo_brand_id" : " ";
		$all_conds.=($cbo_season_name)? " and d.season_buyer_wise =$cbo_season_name" : " ";
		$all_conds.=($cbo_season_year)? " and d.season_year =$cbo_season_year" : " ";
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
			  $ex_fac_sql="SELECT b.po_break_down_id as po_id,b.country_id,sum( case when b.entry_form<>85 then  c.production_qnty else 0 end ) as qnty ,sum( case when b.entry_form=85 then  c.production_qnty else 0 end ) as ret_qnty,max(b.ex_factory_date) as dates ,b.shiping_mode,b.shiping_status from pro_ex_factory_delivery_mst a,pro_ex_factory_mst b, pro_ex_factory_dtls c,wo_po_details_master d,wo_po_break_down e where a.id=b.delivery_mst_id and b.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.is_deleted=0 and e.is_deleted=0 and d.id=e.job_id and e.id=b.po_break_down_id and d.status_active=1 and e.status_active=1 $all_conds group by  b.po_break_down_id,b.country_id,b.shiping_mode,b.shiping_status ";
		}
		else
		{
			$ex_fac_sql="SELECT b.po_break_down_id as po_id,b.country_id ,sum( case when b.entry_form<>85 then  c.production_qnty else 0 end ) as qnty ,sum( case when b.entry_form=85 then  c.production_qnty else 0 end ) as ret_qnty  ,max(b.ex_factory_date) as dates,b.shiping_mode,b.shiping_status from pro_ex_factory_delivery_mst a,pro_ex_factory_mst b,pro_ex_factory_dtls c,wo_po_details_master d,wo_po_break_down e where a.id=b.delivery_mst_id and b.id=c.mst_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.is_deleted=0 and e.is_deleted=0 and d.id=e.job_id and e.id=b.po_break_down_id and d.status_active=1 and e.status_active=1 $all_conds group by  b.po_break_down_id,b.country_id,b.shiping_mode,b.shiping_status";
		}
		//echo $ex_fac_sql;//die;
		$exfac_arrs=sql_select($ex_fac_sql);
		$order_cnty_wise_ex_arr=array();
		$all_po_ex_arr=array();
		foreach($exfac_arrs as $values)
		{
			//$order_cnty_wise_ex_arr[$values[csf("po_id")]][$values[csf("country_id")]]["qnty"]	+=	$values[csf("qnty")]-$values[csf("ret_qnty")];
			//correct block
			$order_cnty_wise_ex_arr[$values[csf("po_id")]][$values[csf("country_id")]]["qnty"] 	+= $values[csf("qnty")];
			$order_cnty_wise_ex_arr[$values[csf("po_id")]][$values[csf("country_id")]]["ret_qnty"] 	+= $values[csf("ret_qnty")];
			//correct block

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
		<div style="width:1440px" >
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
			<div  style="max-height:225px;float: left; overflow-y:scroll;overflow-x: hidden; width:1440px" id="scroll_body" >

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
											$xFactQty = $order_cnty_wise_ex_arr[$po_id][$country_id]["qnty"] - $order_cnty_wise_ex_arr[$po_id][$country_id]["ret_qnty"]



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
			                  	 				<td  align="center" width="100" style="word-wrap: break-word;word-break: break-all;"><? echo $xFactQty;?></td>
			                  	 				<td align="center"  width="95" style="word-wrap: break-word;word-break: break-all;"><? echo $xFactQty*$rows['unit_price'];?></td>
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
		a.COUNTRY_ID, E.ORDER_QUANTITY AS PO_QUANTITY, (E.ORDER_RATE) AS UNIT_PRICE, E.SHIPING_STATUS, F.current_invoice_qnty as INVOICE_QUANTITY

		from wo_po_break_down b, wo_po_details_master c, pro_ex_factory_delivery_mst d,wo_po_color_size_breakdown e,pro_ex_factory_mst a
		left join com_export_invoice_ship_dtls f on a.invoice_no=f.mst_id and a.po_break_down_id=f.po_breakdown_id and f.is_deleted=0 and f.status_active=1
		where a.po_break_down_id=b.id and b.job_id=c.id and c.id=e.job_id and b.id=e.po_break_down_id and a.delivery_mst_id=d.id  $company_cond $str_cond $del_comp_cond $del_location_cond $del_floor_cond $shiping_status_cond $source_cond $buyer_conds $internal_ref_cond $search_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in (1,2,3) and c.is_deleted=0 and c.status_active in (1,2,3) and e.status_active in (1,2,3) and e.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and A.ex_factory_qnty>0 and E.ORDER_QUANTITY>0 and A.ENTRY_FORM!=85
		order by a.ex_factory_date";//and e.country_id=a.country_id  and a.item_number_id=e.item_number_id  and A.ENTRY_FORM!=85

		// echo $sql;die;
		$sql_result=sql_select($sql);
		$all_po_id_arr = array();
		$job_id_arr = array();
		$invoice_id_arr = array();
		$lc_sc_id_arr = array();
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

		$inspection_date_arr=return_library_array( "SELECT b.po_break_down_id, max(b.inspection_date) as inspection_date from pro_buyer_inspection a, gbl_temp_engine d where status_active=1 and is_deleted=0 and b.po_break_down_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=17 and d.ref_from=11 group by b.po_break_down_id", "po_break_down_id", "inspection_date");

		$lc_num_arr=return_library_array( "SELECT id,export_lc_no from com_export_lc where status_active=1 $lc_sc_id_cond", "id", "export_lc_no"  );
		$sc_num_arr=return_library_array( "SELECT id,contract_no from com_sales_contract where status_active=1 $lc_sc_id_cond", "id", "contract_no"  );

		$forwarder_arr=return_library_array( "SELECT id,supplier_name from lib_supplier", "id", "supplier_name");
		// ======================================================================


		$data_array = array();
		$data_array_details=array();
		$all_challan_id_arr=array();
		$po_wise_buyer_arr=array();
		$po_wise_unit_price_arr=array();
		$mst_id_chk_arr = array();
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
			$data_array[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']]['del_company'] .= $company_library[$val['DEL_COMPANY']].",";
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
				if($mst_id_chk_arr[$val['EX_ID']]=="")
				{
					$data_array[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']]['ex_factory_qnty'] += $val['EX_FACTORY_QNTY'];
					$data_array_details[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']][$val['CHALLAN_ID']]['sys_number'] = $val['SYS_NUMBER'];
					$data_array_details[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']][$val['CHALLAN_ID']]['ex_factory_qnty'][$val['EX_ID']] += $val['EX_FACTORY_QNTY'];
					$data_array_details[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']][$val['CHALLAN_ID']]['ex_fac_qty'] += $val['EX_FACTORY_QNTY'];
					$mst_id_chk_arr[$val['EX_ID']] = $val['EX_ID'];
				}
			}
			else if($val['ENTRY_FORM']==85)
			{
				// $data_array[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']]['ex_factory_rtn_qnty'] += $val['EX_FACTORY_QNTY'];
				// $data_array_details[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']][$val['CHALLAN_ID']]['rtn_sys_number'] = $val['SYS_NUMBER'];
				// $data_array_details[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']][$val['CHALLAN_ID']]['ex_factory_rtn_qnty'][$val['EX_ID']] += $val['EX_FACTORY_QNTY'];
			}


			$data_array_details[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']][$val['CHALLAN_ID']]['invoice_no'] .= $invoice_array[$val['INVOICE_NO']].",";
			$data_array_details[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']][$val['CHALLAN_ID']]['invoice_qnty'][$val['INVOICE_NO']] = $val['INVOICE_QUANTITY'];
			$data_array_details[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']][$val['CHALLAN_ID']]['is_posted_account'] = $val['IS_POSTED_ACCOUNT'];

			$all_challan_id_arr[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']][$val['CHALLAN_ID']]++;

			$po_wise_buyer_arr[$val['PO_ID']]=$val['BUYER_NAME'];
			$po_wise_unit_price_arr[$val['PO_ID']]=$val['UNIT_PRICE'];


		}
		//  echo "<pre>";print_r($data_array_details);die();
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
            						$del_company = implode(", ", array_unique(array_filter(explode(",", $row['del_company']))));
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
										<td rowspan="<?=$rowspan;?>" width="125" align="center" ><p><?=$del_company;?></p></td>
										<td rowspan="<?=$rowspan;?>" width="125" align="center" ><p><?=$del_location;?></p></td>
										<td rowspan="<?=$rowspan;?>" width="125" align="center" ><p><?=$del_floor;?></p></td>

										<?
											$j=0;
											foreach($data_array_details[$job_key][$po_key][$country_key] as $challan_no=>$val)
											{
												if($j==0)
												{
													?>
													<td width="120" align="center"><?=$val['sys_number'];
													if (!empty($val['rtn_sys_number']))
													{
														echo $val['rtn_sys_number'];
													}
													?>
													</td>
													<td width="100" align="right">
														<?
														$ex_factory_qnty=$val['ex_fac_qty'];
														if(!empty($ex_factory_qnty))
														{
															echo fn_number_format($ex_factory_qnty,0);
															$job_total_ex_factory_qnty+=$ex_factory_qnty;
															$total_ex_factory_qnty+=$ex_factory_qnty;
														}
														else
														{
															$ex_factory_rtn_qnty=array_sum($val['ex_factory_rtn_qnty']);
															echo fn_number_format($ex_factory_rtn_qnty,2);
															$job_total_ex_factory_qnty+=$ex_factory_rtn_qnty;
															$total_ex_factory_qnty+=$ex_factory_rtn_qnty;
														}
														?>
													</td>
													<td width="100" align="center">
														<?
															// echo implode(", ",array_unique(explode(",",rtrim($val['invoice_no'],','))));
															$invoice_no=array_unique(explode(",",rtrim($val['invoice_no'],',')));
															// foreach($invoice_no as $value){
															// 	echo $value.'<br>';
															// }
															$invoice_no_val= '';
															foreach($invoice_no as $value)
															{
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
														foreach($invoice_qnty as $value)
														{
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
														$ex_factory_qnty=$val['ex_fac_qty'];
														if(!empty($ex_factory_qnty)){
															echo fn_number_format($ex_factory_qnty,0);
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
	else if($reportType==555) // backup FOR MICROFIBER (Country wise 2)
	{
		$tot_cost_arr = return_library_array("select job_no, cm_cost from wo_pre_cost_dtls","job_no","cm_cost");


		//print_r($master_data);

		$inspection_date_arr=return_library_array( "select po_break_down_id, max(inspection_date) as inspection_date from pro_buyer_inspection where status_active=1 and is_deleted=0 group by po_break_down_id", "po_break_down_id", "inspection_date");
		$invoice_array=return_library_array( "select id,invoice_no from com_export_invoice_ship_mst", "id", "invoice_no"  );
		$shipping_mode_array=return_library_array( "select id,shipping_mode from com_export_invoice_ship_mst", "id", "shipping_mode"  );
		$lc_sc_type_arr=return_library_array( "select id,is_lc from com_export_invoice_ship_mst", "id", "is_lc"  );
		$lc_sc_id_array=return_library_array( "select id,lc_sc_id from com_export_invoice_ship_mst", "id", "lc_sc_id"  );
		$lc_num_arr=return_library_array( "select id,export_lc_no from com_export_lc", "id", "export_lc_no"  );
		$sc_num_arr=return_library_array( "select id,contract_no from com_sales_contract", "id", "contract_no"  );

		$forwarder_arr=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name");

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
		$details_report .='<table width="4220" cellspacing="0" cellpadding="0"  border="1"  class="rpt_table" rules="all" id="table_body2">';
		$i=1;

		if($db_type==0)
		{
			$sql= "SELECT b.id as po_id,b.grouping, max(a.lc_sc_no) as lc_sc_arr_no,
			group_concat(distinct a.invoice_no) as invoice_no,
			group_concat(distinct a.item_number_id) as itm_num_id,
			sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
			sum(a.total_carton_qnty) as carton_qnty, max(a.ex_factory_date)  as ex_factory_date,
			group_concat(distinct  a.lc_sc_no) as lc_sc_no,
			max(a.shiping_mode) as shiping_mode,
			a.delivery_mst_id as challan_id,
			d.delivery_floor_id as del_floor,  b.shipment_date, b.po_number,b.po_quantity as po_quantity, (b.unit_price/c.total_set_qnty) as unit_price, b.shiping_status, c.id, c.company_name, c.buyer_name, c.job_no_prefix_num,c.job_no,c.ship_mode , YEAR(c.insert_date) as year, c.style_ref_no, c.style_description, c.set_smv, d.delivery_company_id as del_company,d.source,d.delivery_location_id as del_location ,c.total_set_qnty,a.item_number_id,c.set_break_down,c.order_uom,e.country_id,d.lock_no
			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c ,pro_ex_factory_delivery_mst d,wo_po_color_size_breakdown e
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no $company_cond $str_cond $del_comp_cond $del_location_cond $del_floor_cond $shiping_status_cond $source_cond $buyer_conds $internal_ref_cond $search_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,2,3) and c.is_deleted=0 and c.status_active  in(1,2,3) and a.delivery_mst_id=d.id
			group by
					b.id , a.delivery_mst_id,d.delivery_floor_id,b.shipment_date, b.po_number,b.po_quantity,c.total_set_qnty,b.unit_price,b.shiping_status, c.id, c.company_name, c.buyer_name, c.job_no_prefix_num,c.job_no, c.ship_mode ,c.insert_date, c.style_ref_no, c.style_description, c.set_smv, d.delivery_company_id, d.delivery_location_id ,d.source,a.item_number_id ,c.set_break_down,c.order_uom ,b.grouping,e.country_id,d.lock_no
			order by b.id,c.buyer_name, b.shipment_date ASC";
		}
		else if($db_type==2)
		{

			$sql= "SELECT a.country_id, b.id as po_id,b.grouping, max(a.lc_sc_no) as lc_sc_arr_no,
			LISTAGG(CAST( a.invoice_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.invoice_no) as invoice_no,
			LISTAGG(CAST( a.item_number_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.item_number_id) as itm_num_id,
			sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
			sum(a.total_carton_qnty) as carton_qnty, max(a.ex_factory_date) as ex_factory_date,
			LISTAGG(CAST( a.lc_sc_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.lc_sc_no) as lc_sc_no, max(a.shiping_mode) as shiping_mode,
			a.delivery_mst_id as challan_id,d.delivery_floor_id as del_floor,b.shipment_date, b.po_number,b.po_quantity as po_quantity,(b.unit_price/c.total_set_qnty) as unit_price,b.shiping_status, c.id, c.company_name, c.buyer_name, c.job_no_prefix_num,c.job_no,c.ship_mode ,to_char(c.insert_date,'YYYY') as year, c.style_ref_no, c.style_description, c.set_smv, d.delivery_company_id as del_company,d.source, d.delivery_location_id as del_location,c.total_set_qnty,a.item_number_id,c.set_break_down,c.order_uom,d.lock_no
			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c ,pro_ex_factory_delivery_mst d
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no $company_cond $str_cond $del_comp_cond $del_location_cond $del_floor_cond $shiping_status_cond $source_cond $buyer_conds $internal_ref_cond $search_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active  in(1,2,3) and c.is_deleted=0 and c.status_active  in(1,2,3) and a.delivery_mst_id=d.id and a.entry_form != 85
			group by
					a.country_id,b.id , a.delivery_mst_id,d.delivery_floor_id,b.shipment_date, b.po_number,b.po_quantity,c.total_set_qnty,b.unit_price,b.shiping_status, c.id, c.company_name, c.buyer_name, c.job_no_prefix_num,c.job_no,c.ship_mode , c.insert_date, c.style_ref_no, c.style_description, c.set_smv, d.delivery_company_id, d.source,d.delivery_location_id ,a.item_number_id ,c.set_break_down,c.order_uom,b.grouping,d.lock_no
			order by b.id,c.buyer_name, b.shipment_date ASC";
		}
		  // echo $sql;
		$sql_result=sql_select($sql);
		foreach($sql_result as $k=>$v)
		{
			$all_job_arr[trim($v[csf("job_no")])]=trim($v[csf("job_no")]);

		}
		$all_job="'".implode("','", array_unique($all_job_arr))."'";
		$order_item_qnty_sql="SELECT COUNTRY_ID,po_break_down_id,item_number_id,min(COUNTRY_SHIP_DATE) as COUNTRY_SHIP_DATE,sum(order_quantity) as order_quantity from wo_po_color_size_breakdown where status_active  in(1,2,3) and is_deleted=0 and job_no_mst in($all_job) group by COUNTRY_ID,po_break_down_id,item_number_id,COUNTRY_SHIP_DATE";
		// echo $order_item_qnty_sql;die();
		foreach(sql_select($order_item_qnty_sql) as $key=>$val)
		{
			$order_item_qnty_arr[$val[csf("po_break_down_id")]][$val[csf("item_number_id")]][$val[csf("COUNTRY_ID")]]+=$val[csf("order_quantity")];
			$country_ship_date_arr[$val[csf("po_break_down_id")]][$val[csf("COUNTRY_ID")]]=$val[csf("COUNTRY_SHIP_DATE")];
		}



		$gr_po_qnty_pcs=0;
		$gr_po_qnty_val=0;
		$gr_po_qnty_val_perc=0;
		$gr_ttl_ex_qnty=0;
		$gr_ttl_ex_qnty_val=0;
		$gr_sales_min=0;
		$gr_ttl_carton=0;
		$gr_ttl_basic_qty=0;
		$gr_ttl_ex_fac_per=0;
		$gr_ttl_short_qty=0;
		$gr_ttl_short_val=0;
		$gr_ttl_sales_cm=0;

		$tmp = array();
		$po_exist_arr=array();
		$sl = 1;
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

			$todate=date("d-M")."-".substr(date("Y"), 2) ;
 			$todate=explode("-", $todate);
 			$todate=$todate[0]."-".strtoupper($todate[1])."-".$todate[2];
 			$diff=datediff("d",$todate, $row[csf("shipment_date")])-2;
		    $diff_color=($diff>0) ?" color:black;":"color:red;";

			foreach($challan_id as $val)
			{
				if($challan_no=="") $challan_no=$challan_mst_arr[$val][$row[csf('po_id')]]['challan']; else $challan_no.=','.$challan_mst_arr[$val][$row[csf('po_id')]]['challan'];
				if($floor_no=="") $floor_no=$floor_library[$challan_mst_arr[$val][$row[csf('po_id')]]['floor']]; else $floor_no.=','.$floor_library[$challan_mst_arr[$val][$row[csf('po_id')]]['floor']];

				if($forwarder=="") $forwarder=$forwarder_arr[$challan_mst_arr[$val][$row[csf('po_id')]]['forwarder']]; else $forwarder.=','.$forwarder_arr[$challan_mst_arr[$val][$row[csf('po_id')]]['forwarder']];
				if($vehi_no=="") $vehi_no=$challan_mst_arr[$val][$row[csf('po_id')]]['truck_no']; else $vehi_no.=','.$challan_mst_arr[$val][$row[csf('po_id')]]['truck_no'];
				if($dirver_info=="") $dirver_info="Name: ".$challan_mst_arr[$val][$row[csf('po_id')]]['driver_name']."<br>Mob No: ".$challan_mst_arr[$val][$row[csf('po_id')]]['mobile_no']."<br>DL No: ".$challan_mst_arr[$val][$row[csf('po_id')]]['dl_no']; else $dirver_info.=','."Name: ".$challan_mst_arr[$val][$row[csf('po_id')]]['driver_name']."<br>Mob No: ".$challan_mst_arr[$val][$row[csf('po_id')]]['mobile_no']."<br>DL No: ".$challan_mst_arr[$val][$row[csf('po_id')]]['dl_no'];
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

			if ($i%2==0)
			$bgcolor="#E9F3FF";
			else
			$bgcolor="#FFFFFF";
			$comapny_id=$row[csf("company_name")];
			$delv_comp=($row[csf("source")]!=3)?  $company_library[$row[csf("del_company")]] : $supp_library[$row[csf("del_company")]];

			$group_by = $row[csf("po_id")]; // 42
			if(!in_array($group_by, $po_exist_arr))
			{
				if($sl !=1)
				{
					$details_report .="<tr class='gd-color'>";
					$details_report .="<td colspan='25' align='right'>Order Wise Sub Total </td>
										<td align='right'>$sub_po_qnty_pcs</td>
										<td align='right'></td>
										<td align='right'>".number_format($sub_po_qnty_val,0)."</td>
										<td align='right'>".number_format($sub_current_ex_Fact_Qty,0)."</td>
										<td align='right'>".number_format($sub_current_ex_fact_value,0)."</td>
										<td align='right'>".number_format($sub_ttl_carton_qt,0)."</td>
										<td align='right'>".number_format($sub_total_exface_qnty,0)."</td>
										<td align='right'>".number_format($sub_total_ex_fact_value,2)."</td>
										<td align='right'>".number_format($sub_total_cartoon_qty,0)."</td>
										<td align='right'>".number_format($sub_total_sales_minutes,0)."</td>
										<td align='right'>".number_format($sub_total_basic_qnty,0)."</td>
										<td align='right'>".number_format($sub_total_excess_shortage_qty,0)."</td>
										<td align='right'>".number_format($sub_total_excess_shortage_value,0)."</td>
										<td align='right'>".number_format($sub_total_excess_shortage_parcent,0)."</td>
										<td align='right'>".number_format($sub_total_sales_cm,2)."</td>
										<td colspan='6'></td>
										";
					$details_report .="</tr>";

				}
				$po_exist_arr[] = $group_by;
				$sl++;
				unset($sub_po_qnty_pcs);
				unset($sub_po_qnty_val);
				unset($sub_ttl_ex_qnty);
				unset($sub_current_ex_Fact_Qty);
				unset($sub_current_ex_fact_value);
				unset($sub_ttl_carton_qt);
				unset($sub_total_exface_qnty);
				unset($sub_total_ex_fact_value);
				unset($sub_total_cartoon_qty);
				unset($sub_total_sales_minutes);
				unset($sub_total_basic_qnty);
				unset($sub_total_excess_shortage_qty);
				unset($sub_total_excess_shortage_value);
				unset($sub_total_excess_shortage_parcent);
			}

			$onclick=" change_color('tr_".$i."','".$bgcolor."')";
			$details_report .='<tr bgcolor="'.$bgcolor.'" onclick="'.$onclick.'" id="tr_'.$i.'">';
			$details_report .='<td width="40" align="center">'.$i.'</td>
								<td width="150" align="center" ><p>'.$company_library[$row[csf("company_name")]].'</p></td>
								<td width="60" align="center" ><p>'.$row[csf("job_no_prefix_num")].'</p></td>
								<td width="60" align="center" ><p>'.$row[csf("year")].'</p></td>
								<td width="100" align="center" ><p>'.$buyer_arr[$row[csf("buyer_name")]].'</p></td>
								<td width="110" align="center" title="'.$row[csf("po_id")].'"><p>'.$row[csf("po_number")].'</p></td>
								<td width="125" align="left"><p>'.$lib_country[$row[csf("country_id")]].'</p></td>
								<td width="110" align="left"><p>'.$row[csf("grouping")].'</p></td>
								<td width="125" align="center" ><p>'.$delv_comp.'</p></td>
								<td width="125" align="center" ><p>'.$location_library[$row[csf("del_location")]].'</p></td>
								<td width="125" align="center" ><p>'.$floor_no.'</p></td>
								<td width="120" align="center"><p>'.$challan_no.'</p></td>
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
								<td width="100"><p>'.$row[csf("style_ref_no")].'</p></td>
								<td width="100"><p>'.$row[csf("style_description")].'</p></td>
								<td width="110" align="center"><p>';
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
								$po_quantity=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]];
								$unit_price=$row[csf("unit_price")];
								$current_ex_Fact_Qty=$row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]][$row[csf("challan_id")]];
								$basic_qnty=($total_ex_fact_qty*$item_smv)/$basic_smv_arr[$row[csf("company_name")]];
								$cm_per_pcs_tot+=($cm_per_pcs*$current_ex_Fact_Qty);
								$short_excess=$total_ex_fact_qty-$po_quantity;

								$excess_msg=($short_excess>0) ?" color:green;":"color:black;";
								$excess_val_msg=(($total_ex_fact_qty*$unit_price)-$po_quantity>0) ?" color:green;":"color:black;";
								$ttl_ex_qty=($total_ex_fact_qty/$po_quantity)*100;
								$ttl_ex_qty_msg=($ttl_ex_qty>100) ?" color:green;":"color:black;";



			$details_report .=$item_name_all.'</p></td>
								<td width="80" align="center"><p>'.$item_smv.'</p></td>
								<td width="70" align="center"><p>'.change_date_format($country_ship_date_arr[$row[csf("po_id")]][$row[csf("country_id")]]).'</p></td>
								<td width="70" align="center"><p>'.change_date_format($row[csf("shipment_date")]).'</p></td>
								<td width="70" align="center"><a href="##" onclick="openmypage_ex_date('.$comapny_id.",'".$row[csf('po_id')]."','".$row[csf('item_number_id')]."','".$ex_fact_date_range."','ex_date_popup','".$row[csf('challan_id')]."'".",'1'".')">'.change_date_format($row[csf('ex_factory_date')]).'</a></td>
								<td width="100" align="center"><p>'.$shipment_mode[$row[csf('ship_mode')]].'</p></td>
								<td width="70" align="center"><p>'.$shipment_mode[$row[csf("shiping_mode")]].'</p></td>

								<td width="60" align="center" style="'.$diff_color.'"><p>('.$diff.')</p></td>
								<td width="100" align="center"><p>'.$unit_of_measurement[$row[csf('order_uom')]].'</p></td>
								<td width="80" align="right"><p>'. number_format($po_quantity,0,'', '').'</p></td>
								<td width="70" align="right"><p>'. number_format($unit_price,4).'</p></td>
								<td width="100" align="right"><p>'. number_format(($po_quantity*$unit_price),2).'</p></td>
								<td width="80" align="right"><p><a href="##" onclick="openmypage_ex_date('.$comapny_id.",'".$row[csf('po_id')]."','".$row[csf('item_number_id')]."','".$ex_fact_date_range."','ex_date_popup','".$row[csf('challan_id')]."'".",'1'".')">'. number_format($current_ex_Fact_Qty,0,'.', '').'</a></p></td>
								<td width="100" align="right"><p>'. number_format( $current_ex_fact_value=$current_ex_Fact_Qty*$unit_price,2).'</p></td>
								<td width="80" align="right"><p>'. number_format($row[csf("carton_qnty")],0,'.', '').'</p></td>
								<td width="80" align="right"><p><a href="##" onclick="openmypage_ex_date('.$comapny_id.",'".$row[csf('po_id')]."','".$row[csf('item_number_id')]."','".$total_exface_qnty."','ex_date_popup','".$row[csf('challan_id')]."'".",'1'".')">'.number_format($total_ex_fact_qty,0,'.', '').'</a></p></td>
								<td width="100" align="right"><p>'. number_format($total_ex_fact_value=$total_ex_fact_qty*$unit_price,2).'</p></td>
								<td width="80" align="right"><p>'.number_format($total_cartoon_qty,0,'.', '').'</p></td>
								<td width="100" align="right" title="Total Ex.Qty*SMV"><p>'. number_format($total_sales_minutes=$total_ex_fact_qty*$item_smv).'</p></td>
								<td width="80" align="right"><p>'.number_format($basic_qnty,0,'','').'</p></td>
								<td width="80" align="right" style="'.$excess_msg.'" ><p>'. number_format($excess_shortage_qty=$total_ex_fact_qty-$po_quantity,0,'', '').'</p></td>
								<td width="100" align="right" style="'.$excess_val_msg.'"><p>'. number_format($excess_shortage_value=($excess_shortage_qty*$unit_price),2).'</p></td>
								<td align="center" style="'.$ttl_ex_qty_msg.'" width="80"><p>'. number_format($total_ex_fact_qty_parcent=($total_ex_fact_qty/$po_quantity)*100,0).'</p></td>
								<td width="60" align="center" title="CM per pcs: '.number_format($cm_per_pcs,4).'"><p>'.number_format($cm_per_pcs*$total_ex_fact_qty,2).'</p></td>
								<td width="100" align="center"><p>'.$forwarder.'</p></td>
								<td width="80" align="center"><p>'.$vehi_no.'</p></td>
								<td width="80" align="center"><p>'.$row[csf('lock_no')].'</p></td>
								<td width="130"><p>'.$dirver_info.'</p></td>
								<td width="70" align="center"><p>'.(change_date_format($inspection_date_arr[$row[csf('po_id')]]) == '0000-00-00' || change_date_format($inspection_date_arr[$row[csf('po_id')]]) == '' ? '' : change_date_format(change_date_format($inspection_date_arr[$row[csf('po_id')]]))).'</p></td>
								<td align="center"><p>'.$shipment_status[$row[csf('shiping_status')]].'</p></td>
							</tr>';

			if($po_check_arr[$row[csf("po_id")]]=="")
			{
				$po_check_arr[$row[csf("po_id")]]=$row[csf("po_id")];
				$master_data[$row[csf("buyer_name")]]['b_id']=$row[csf("buyer_name")];
	  			//$master_data[$row[csf("buyer_name")]]['po_qnty'] +=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]];
	  			$master_data[$row[csf("buyer_name")]]['org_po_qnty'] +=$row[csf("po_quantity")];
	  			//$master_data[$row[csf("buyer_name")]]['po_value'] +=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]]*$row[csf("unit_price")];


				//$total_po_qty+=$row[csf("po_quantity")];
				//$total_po_valu+=$row[csf("po_quantity")]*$row[csf("unit_price")];
			}

			//$master_data[$row[csf("buyer_name")]]['po_qnty'] +=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]];
 	  		//$master_data[$row[csf("buyer_name")]]['po_value'] +=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]]*$row[csf("unit_price")];
 	  		$master_data[$row[csf("buyer_name")]]['po_qnty'] +=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]];
 	  		$master_data[$row[csf("buyer_name")]]['po_value'] +=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]]*$row[csf("unit_price")];
 	  		$total_po_qty+=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]];
 	  		$total_po_valu+=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]]*$row[csf("unit_price")];

			$master_data[$row[csf("buyer_name")]]['basic_qnty'] +=$basic_qnty;
			$master_data[$row[csf("buyer_name")]]['ex_factory_qnty'] +=$row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]][$row[csf("challan_id")]];
			$master_data[$row[csf("buyer_name")]]['ex_factory_value'] +=($row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]][$row[csf("challan_id")]])*$row[csf("unit_price")];
			$master_data[$row[csf("buyer_name")]]['total_ex_fact_qty'] +=$total_ex_fact_qty;
			$master_data[$row[csf("buyer_name")]]['total_ex_fact_value'] +=$total_ex_fact_qty*$row[csf("unit_price")];
			$master_data[$row[csf("buyer_name")]]['sales_min'] += $item_smv*$total_ex_fact_qty;



			$total_basic_qty+=$basic_qnty;

			$total_ex_qty+=$row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]][$row[csf("challan_id")]];
			$total_crtn_qty+=$row[csf("carton_qnty")];
			$total_ex_valu+=($row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]][$row[csf("challan_id")]])*$row[csf("unit_price")];
			$g_total_ex_qty+=$total_ex_fact_qty;
			$g_total_ex_crtn+=$total_cartoon_qty;
			$g_total_ex_val+=$total_ex_fact_qty*$row[csf("unit_price")];
			$g_sales_minutes+=$total_sales_minutes;
			// $g_sales_minutes+=$current_ex_Fact_Qty*$item_smv;
			$total_eecess_storage_qty+=$excess_shortage_qty;
			$total_eecess_storage_val+=$excess_shortage_value;


  			$sub_po_qnty_pcs=$po_quantity;
  			$sub_po_qnty_val=$po_quantity*$row[csf("unit_price")];
	  		$sub_current_ex_Fact_Qty+=$current_ex_Fact_Qty;
  			$sub_current_ex_fact_value+=$current_ex_fact_value;
  			$sub_ttl_carton_qt+=$row[csf("carton_qnty")];
  			$sub_total_exface_qnty+=$total_ex_fact_qty;
  			$sub_total_ex_fact_value+=$total_ex_fact_value;
  			$sub_total_cartoon_qty+=$total_cartoon_qty;
  			$sub_total_sales_minutes+=$total_sales_minutes;
  			$sub_total_basic_qnty+=$basic_qnty;
  			$sub_total_excess_shortage_qty+=$excess_shortage_qty;
  			$sub_total_excess_shortage_value+=$excess_shortage_value;
  			$sub_total_excess_shortage_parcent+=$total_ex_fact_qty_parcent;
  			$sub_total_sales_cm+=$cm_per_pcs*$total_ex_fact_qty;

	  		/*$gr_po_qnty_pcs+=$po_quantity;
  			$gr_po_qnty_val+=$po_quantity*$unit_price;
	  		$gr_ttl_ex_qnty+=$total_ex_fact_qty;
  			$gr_ttl_ex_qnty_val+=$total_ex_fact_value;
  			$gr_ttl_carton_qt+=$total_cartoon_qty;
  			$gr_sales_min+=$total_sales_minutes;
  			$gr_ttl_basic_qty+=$basic_qnty;
  			$gr_ttl_ex_fac_per+=$total_ex_fact_qty_parcent;
  			$gr_ttl_short_qty+=$excess_shortage_qty;
  			$gr_ttl_short_val+=$excess_shortage_value;
  			$gr_ttl_sales_cm+=$cm_per_pcs*$total_ex_fact_qty;*/


			$i++;$item_name_all="";
		}
		$pp=$i;
		$details_report .="<tr class='gd-color'>";
		$details_report .="<td colspan='25' align='right'>Order Wise Sub Total </td>
							<td align='right'>$sub_po_qnty_pcs</td>
							<td align='right'></td>
							<td align='right'>".number_format($sub_po_qnty_val,0)."</td>
							<td align='right'>".number_format($sub_current_ex_Fact_Qty,0)."</td>
							<td align='right'>".number_format($sub_current_ex_fact_value,0)."</td>
							<td align='right'>".number_format($sub_ttl_carton_qt,0)."</td>
							<td align='right'>".number_format($sub_total_exface_qnty,0)."</td>
							<td align='right'>".number_format($sub_total_ex_fact_value,2)."</td>
							<td align='right'>".number_format($sub_total_cartoon_qty,0)."</td>
							<td align='right'>".number_format($sub_total_sales_minutes,0)."</td>
							<td align='right'>".number_format($sub_total_basic_qnty,0)."</td>
							<td align='right'>".number_format($sub_total_excess_shortage_qty,0)."</td>
							<td align='right'>".number_format($sub_total_excess_shortage_value,0)."</td>
							<td align='right'>".number_format($sub_total_excess_shortage_parcent,0)."</td>
							<td align='right'>".number_format($sub_total_sales_cm,2)."</td>
							<td colspan='6'></td>
							";
		$details_report .="</tr>";

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
                 // echo "<pre>";
                 // print_r($master_data);
                 // echo "</pre>";
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
                <table width="4220"  >
                    <tr>
                    <td colspan="33" class="form_caption"><strong style="font-size:16px;">Shipped Out Order Details Report</strong></td>
                    </tr>
                </table>
                <table width="4220" border="1" class="rpt_table" rules="all" id="table_header_2">
                    <thead>
                        <th width="40">SL</th>
                        <th width="150">Company</th>
                        <th width="60">Job</th>
                        <th width="60">Year</th>
                        <th width="100">Buyer Name</th>
                        <th width="110">Order NO</th>
                        <th width="125">Country</th>
                        <th width="110">Internal ref.</th>
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
                        <th width="70">Country Shipment Date</th>
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
                        <th width="80">Lock No</th>
                        <th width="130">Driver Info</th>
                        <th width="70">Inspection Date</th>
                        <th>Ex-Fact Status</th>
                    </thead>
                </table>
            <div style="width:4240px; overflow-y:scroll; overflow-x:hidden; max-height:300px;"  id="scroll_body" >
            <? echo $details_report;

            	$details_report .='</table>';
            ?>




            <table width="4220" cellspacing="0" cellpadding="0"  border="1"  class="rpt_table" rules="all" id="table_footer">
                <tfoot>
                    <tr>
                    	<th width="40">&nbsp;</th>
                        <th width="150">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="125">&nbsp;</th>
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
                        <th width="70">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="100" align="right"><strong>Grand Total </strong></th>
                        <th width="80" id="" align="right"><? echo  number_format($total_po_qty,0);?></th>
                        <th width="70" align="right">&nbsp;</th>
                        <th width="100" align="right" id=""><? echo  number_format($total_po_valu,2); ?></th>
                        <th width="80" align="right" id=""><? echo number_format($total_ex_qty,0); ?></th>
                        <th width="100" align="right" id=""><? echo number_format($total_ex_valu,2);?></th>
                        <th width="80" align="right" id=""><? echo number_format($total_crtn_qty,0); ?></th>
                        <th width="80" align="right" id=""><? echo number_format($g_total_ex_qty,0);?></th>
                        <th width="100" align="right" id=""><? echo number_format($g_total_ex_val,2);?></th>
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
            </div>
        </div>

		<?
	}
	else if($reportType==6)
	{
		$print_report_format=return_library_array( "select template_name, format_id from lib_report_template where module_id=7 and report_id=86 and is_deleted=0 and status_active=1",'template_name','format_id');

		//echo $str_cond_sub;die;

	   	$subcon_sql_exfac="SELECT  c.order_uom,a.vehical_no,a.delivery_no,a.within_group,b.subcon_job ,b.job_no_prefix_num as job,b.party_id as buyer_name,b.company_id as company_name,a.company_id as delivery_company_id,a.location_id,c.cust_style_ref as style_ref_no,c.order_no as po_number,c.id as po_id, a.delivery_date   ,c.order_quantity as po_quantity ,c.delivery_date as shipment_date,c.smv,d.item_id,d.total_carton_qnty,  d.delivery_qty as prod_qty,b.insert_date,c.rate as unit_price from subcon_delivery_mst a,subcon_delivery_dtls d, subcon_ord_mst b,subcon_ord_dtls c   where  a.id=d.mst_id and d.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0   and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form<>4 and a.process_id=3 $str_cond_sub ";
		$subcon_exfactory_arr=array();
		$duplicate_challan_check_arr=array();
		$duplicate_vehicle_check_arr=array();
		foreach(sql_select($subcon_sql_exfac__) as $vals)
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

			$subcon_exfactory_arr[$vals[csf("po_id")]][$vals[csf("item_id")]][$vals[csf("delivery_company_id")]][$vals[csf("location_id")]][$vals[csf("delivery_date")]]["job_no"]=$vals[csf("subcon_job")];




			$subcon_exfactory_arr[$vals[csf("po_id")]][$vals[csf("item_id")]][$vals[csf("delivery_company_id")]][$vals[csf("location_id")]][$vals[csf("delivery_date")]]["buyer_name"]=$buyer_arr[$vals[csf("buyer_name")]];

			$subcon_exfactory_arr[$vals[csf("po_id")]][$vals[csf("item_id")]][$vals[csf("delivery_company_id")]][$vals[csf("location_id")]][$vals[csf("delivery_date")]]["buyer_name2"]=$vals[csf("buyer_name")];

			$subcon_exfactory_arr[$vals[csf("po_id")]][$vals[csf("item_id")]][$vals[csf("delivery_company_id")]][$vals[csf("location_id")]][$vals[csf("delivery_date")]]["po_quantity"]=$vals[csf("po_quantity")];



			$subcon_exfactory_arr[$vals[csf("po_id")]][$vals[csf("item_id")]][$vals[csf("delivery_company_id")]][$vals[csf("location_id")]][$vals[csf("delivery_date")]]["prod_qty"]+=$vals[csf("prod_qty")];

			$subcon_exfactory_arr[$vals[csf("po_id")]][$vals[csf("item_id")]][$vals[csf("delivery_company_id")]][$vals[csf("location_id")]][$vals[csf("delivery_date")]]["total_carton_qnty"]+=$vals[csf("total_carton_qnty")];

			$subcon_exfactory_arr[$vals[csf("po_id")]][$vals[csf("item_id")]][$vals[csf("delivery_company_id")]][$vals[csf("location_id")]][$vals[csf("delivery_date")]]["shipment_date"]=$vals[csf("shipment_date")];

			$subcon_exfactory_arr[$vals[csf("po_id")]][$vals[csf("item_id")]][$vals[csf("delivery_company_id")]][$vals[csf("location_id")]][$vals[csf("delivery_date")]]["po_number"]=$vals[csf("po_number")];
			$subcon_exfactory_arr[$vals[csf("po_id")]][$vals[csf("item_id")]][$vals[csf("delivery_company_id")]][$vals[csf("location_id")]][$vals[csf("delivery_date")]]["style_ref_no"]=$vals[csf("style_ref_no")];

			$subcon_exfactory_arr[$vals[csf("po_id")]][$vals[csf("item_id")]][$vals[csf("delivery_company_id")]][$vals[csf("location_id")]][$vals[csf("delivery_date")]]["smv"]=$vals[csf("smv")];


			$subcon_exfactory_arr[$vals[csf("po_id")]][$vals[csf("item_id")]][$vals[csf("delivery_company_id")]][$vals[csf("location_id")]][$vals[csf("delivery_date")]]["within_group"]=$vals[csf("within_group")];

		}

		$subcon_sql_exfac_total="SELECT  a.vehical_no,a.delivery_no,b.subcon_job ,b.job_no_prefix_num as job,b.party_id as buyer_name,b.company_id as company_name,a.company_id as delivery_company_id,a.location_id,c.cust_style_ref as style_ref_no,c.order_no as po_number,c.id as po_id, a.delivery_date ,c.order_quantity as po_quantity ,c.delivery_date as shipment_date,c.smv,d.item_id,d.total_carton_qnty,  d.delivery_qty as prod_qty,b.insert_date,c.rate as unit_price from subcon_delivery_mst a,subcon_delivery_dtls d, subcon_ord_mst b,subcon_ord_dtls c   where  a.id=d.mst_id and d.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0  and a.process_id=3 and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form<>4 and a.process_id=3  $str_cond_sub_total ";
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
		$details_report .='<table align="left" width="1850" cellspacing="0" cellpadding="0"  border="1"  class="rpt_table" rules="all" id="table_body2">';
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
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no $company_cond $str_cond $del_comp_cond $del_location_cond $del_floor_cond $shiping_status_cond $source_cond $buyer_conds $internal_ref_cond $search_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active  in(1,2,3) and c.is_deleted=0 and c.status_active  in(1,2,3) and a.delivery_mst_id=d.id
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
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no $company_cond $str_cond $del_comp_cond $del_location_cond $del_floor_cond $shiping_status_cond $source_cond $buyer_conds $internal_ref_cond $search_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active  in(1,2,3) and c.is_deleted=0 and c.status_active  in(1,2,3) and a.delivery_mst_id=d.id
			group by
					b.id , a.delivery_mst_id,d.delivery_floor_id,b.shipment_date, b.po_number,b.po_quantity,c.total_set_qnty,b.unit_price,b.shiping_status, c.id, c.company_name, c.buyer_name, c.job_no_prefix_num,c.job_no,c.ship_mode , c.insert_date, c.style_ref_no, c.style_description, c.set_smv, d.delivery_company_id, d.source,d.delivery_location_id ,a.item_number_id ,c.set_break_down,c.order_uom
			order by c.buyer_name, b.shipment_date ASC";
		}

		//echo $del_comp_cond;die;

		$sql_result=sql_select($sql);
		foreach($sql_result as $k=>$v)
		{
			$all_job_arr[trim($v[csf("job_no")])]=trim($v[csf("job_no")]);
			$fabric_source=$fabric_source_arr[$v[csf("job_no")]];
			$all_po_id.=$v[csf("po_id")].',';
			$all_company_id.=$v[csf("company_name")].',';

		}
		//print_r($all_po_arr);
		$all_job="'".implode("','", array_unique($all_job_arr))."'";
		$all_po_ids=rtrim($all_po_id,',');
		$all_company_id=rtrim($all_company_id,',');
		$all_po_ids=implode(",",array_unique(explode(",",$all_po_ids)));
		$all_company_ids=implode(",",array_unique(explode(",",$all_company_id)));
		//echo $all_po_ids.'DD';die;
		$order_item_qnty_sql="SELECT po_break_down_id,item_number_id,sum(order_quantity) as order_quantity from wo_po_color_size_breakdown where status_active  in(1,2,3) and is_deleted=0 and job_no_mst in($all_job) group by po_break_down_id,item_number_id";
		foreach(sql_select($order_item_qnty_sql) as $key=>$val)
		{
			$order_item_qnty_arr[$val[csf("po_break_down_id")]][$val[csf("item_number_id")]]=$val[csf("order_quantity")];
		}

		/******************************************************************************************************
		*																									  *
		*								GETTING PRICE QUOTATION WISE CM VALU							      *
		*																									  *
		*******************************************************************************************************/
		$quotation_qty_sql="SELECT a.id  as quotation_id,a.mkt_no,a.sew_smv,a.sew_effi_percent,a.gmts_item_id,a.company_id,a.buyer_id,a.costing_per, a.style_desc as style_desc, a.style_ref, a.order_uom,a.offer_qnty, a.total_set_qnty as ratio, a.quot_date,a.est_ship_date,b.costing_per_id,b.price_with_commn_pcs,b.total_cost,b.costing_per_id,c.job_no from wo_price_quotation a,wo_price_quotation_costing_mst b,wo_po_details_master c  where a.id=b.quotation_id and a.id=c.quotation_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and a.offer_qnty>0 and c.job_no in($all_job) order  by a.id ";
		// echo $quotation_qty_sql;die();
		$quotation_qty_sql_res = sql_select($quotation_qty_sql);
		$quotation_qty_array = array();
		$quotation_id_array = array();
		$all_jobs_array = array();
		$jobs_wise_quot_array = array();
		foreach ($quotation_qty_sql_res as $val)
		{
			$quotation_qty_array[$val['JOB_NO']]['QTY_PCS'] += $val['OFFER_QNTY']*$val['RATIO'];
			$quotation_qty_array[$val['JOB_NO']]['COSTING_PER_ID'] += $val['COSTING_PER_ID'];
			$quotation_id_array[$val['QUOTATION_ID']] = $val['QUOTATION_ID'];
			$all_jobs_array[$val['JOB_NO']] = $val['JOB_NO'];
			$jobs_wise_quot_array[$val['JOB_NO']] = $val['QUOTATION_ID'];

			$quot_wise_arr[$val[csf("quotation_id")]]['offer_qnty']=$val[csf("offer_qnty")];
			$quot_wise_arr[$val[csf("quotation_id")]]['costing_per_id']=$val[csf("costing_per_id")];

			$style_wise_arr[$val[csf("job_no")]]['costing_per']=$val[csf("costing_per")];
			$style_wise_arr[$val[csf("job_no")]]['gmts_item_id']=$val[csf("gmts_item_id")];
			$style_wise_arr[$val[csf("job_no")]]['sew_smv']=$val[csf("sew_smv")];
			$style_wise_arr[$val[csf("job_no")]]['sew_effi_percent']=$val[csf("sew_effi_percent")];
			$style_wise_arr[$val[csf("job_no")]]['shipment_date'].=$val[csf('est_ship_date')].',';
			$style_wise_arr[$val[csf("job_no")]]['quotation_id']=$val[csf("quotation_id")];
			$style_wise_arr[$val[csf("job_no")]]['buyer_name']=$val[csf("buyer_id")];
			$offer_qnty_pcs=$val[csf('offer_qnty')]*$val[csf('ratio')];
			$style_wise_arr[$val[csf("job_no")]]['qty_pcs']+=$val[csf('offer_qnty')]*$val[csf('ratio')];
			$style_wise_arr[$val[csf("job_no")]]['qty']+=$val[csf('offer_qnty')];
			$style_wise_arr[$val[csf("job_no")]]['final_cost_pcs']+=$val[csf('price_with_commn_pcs')];
			$style_wise_arr[$val[csf("job_no")]]['total_cost']+=$offer_qnty_pcs*$val[csf('price_with_commn_pcs')];
		}
		$all_quot_id = implode(",", $quotation_id_array);

		// print_r($style_wise_arr);die();
		// ===============================================================================
		$sql_fab = "SELECT a.quotation_id,sum(a.avg_cons) as cons_qnty, sum(a.amount) as amount,b.job_no from wo_pri_quo_fabric_cost_dtls a,wo_po_details_master b where a.quotation_id=b.quotation_id and a.quotation_id in($all_quot_id) and a.fabric_source=2 and a.status_active=1 and b.status_active=1 group by  a.quotation_id,b.job_no";
		// echo $sql_fab;die();
		$data_array_fab=sql_select($sql_fab);
		foreach($data_array_fab as $row)
		{
			$costing_per_id=$quot_wise_arr[$row[csf("quotation_id")]]['costing_per_id'];
			if($costing_per_id==1){$fab_order_price_per_dzn=12;}
			else if($costing_per_id==2){$fab_order_price_per_dzn=1;}
			else if($costing_per_id==3){$fab_order_price_per_dzn=24;}
			else if($costing_per_id==4){$fab_order_price_per_dzn=36;}
			else if($costing_per_id==5){$fab_order_price_per_dzn=48;}

			$fab_order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
			$fab_summary_data[$row[csf("job_no")]]['fab_amount_dzn']+=$row[csf("amount")];
			$fab_summary_data[$row[csf("job_no")]]['fab_amount_total_value']+=($row[csf("amount")]/$fab_order_price_per_dzn)*$fab_order_job_qnty;
			//$yarn_amount_dzn+=$row[csf('amount')];
		}
		// ==================================================================================
		$sql_yarn = "SELECT a.quotation_id,sum(a.cons_qnty) as cons_qnty, sum(a.amount) as amount,b.job_no from wo_pri_quo_fab_yarn_cost_dtls a,wo_po_details_master b where a.quotation_id=b.quotation_id and a.quotation_id in($all_quot_id) and a.status_active=1 group by  a.quotation_id,b.job_no";
		// echo $sql_yarn;die();
		$data_array_yarn=sql_select($sql_yarn);
		foreach($data_array_yarn as $row)
		{
			$costing_per_id=$quot_wise_arr[$row[csf("quotation_id")]]['costing_per_id'];
			if($costing_per_id==1){$yarn_order_price_per_dzn=12;}
			else if($costing_per_id==2){$yarn_order_price_per_dzn=1;}
			else if($costing_per_id==3){$yarn_order_price_per_dzn=24;}
			else if($costing_per_id==4){$yarn_order_price_per_dzn=36;}
			else if($costing_per_id==5){$yarn_order_price_per_dzn=48;}
			$yarn_order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
			//$yarn_summary_dzn=$yarn_summary_data[$row[csf("quotation_id")]]['yarn_amount_dzn'];
			 $yarn_summary_data[$row[csf("job_no")]]['yarn_amount_dzn']+=$row[csf("amount")];
			// $summary_data['yarn_amount_dzn']+=$yarn_summary_dzn;
			 //$yarn_summary_data['yarn_amount_total_value']+=($row[csf("amount")]/$yarn_order_price_per_dzn)*$yarn_order_job_qnty;
		}
		// ===================================================================================
		$conversion_cost_arr=array();
		$sql_conversion = "SELECT a.id, a.quotation_id, a.cons_type, a.req_qnty, a.charge_unit, a.amount, a.status_active,b.body_part_id,b.fab_nature_id,b.color_type_id,b.construction ,b.composition,c.job_no
		from wo_po_details_master c, wo_pri_quo_fab_conv_cost_dtls a left join wo_pri_quo_fabric_cost_dtls b on a.quotation_id=b.quotation_id and a.cost_head=b.id
		where a.quotation_id in($all_quot_id) and a.quotation_id=c.quotation_id and a.status_active=1  ";
		// echo $sql_conversion;die();
		$data_array_conversion=sql_select($sql_conversion);
		foreach($data_array_conversion as $row)
		{
			$costing_per_id=$quot_wise_arr[$row[csf("quotation_id")]]['costing_per_id'];
			if($costing_per_id==1){$conv_order_price_per_dzn=12;}
			else if($costing_per_id==2){$conv_order_price_per_dzn=1;}
			else if($costing_per_id==3){$conv_order_price_per_dzn=24;}
			else if($costing_per_id==4){$conv_order_price_per_dzn=36;}
			else if($costing_per_id==5){$conv_order_price_per_dzn=48;}
			$conv_order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
			$conv_summary_data[$row[csf("job_no")]]['conv_amount_dzn']+=$row[csf("amount")];

			$conversion_cost_arr[$row[csf("job_no")]][$row[csf('cons_type')]]['conv_amount_dzn']+=$row[csf('amount')];
			$conversion_cost_arr[$row[csf("job_no")]][$row[csf('cons_type')]]['conv_amount_total_value']+=($row[csf("amount")]/$conv_order_price_per_dzn)*$conv_order_job_qnty;
		}
		// print_r($conversion_cost_arr);die();
		if($db_type==0)
		{
			$sql = "SELECT MAX(a.id),a.quotation_id,a.fabric_cost,a.fabric_cost_percent,a.trims_cost,a.trims_cost_percent,a.embel_cost,a.embel_cost_percent,a.wash_cost,a.wash_cost_percent,a.comm_cost,a.comm_cost_percent,a.commission,a.commission_percent,a.lab_test,a.lab_test_percent,a.inspection,a.inspection_percent,a.cm_cost,a.cm_cost_percent,a.freight,a.freight_percent,a.currier_pre_cost,a.currier_percent ,a.certificate_pre_cost,a.certificate_percent,a.common_oh,a.common_oh_percent,a.depr_amor_pre_cost,a.depr_amor_po_price,a.interest_pre_cost,a.interest_po_price,a.income_tax_pre_cost,a.income_tax_po_price,a.total_cost ,a.total_cost_percent,a.final_cost_dzn ,a.final_cost_dzn_percent ,a.confirm_price_dzn ,a.confirm_price_dzn_percent,a.final_cost_pcs,a.margin_dzn,a.margin_dzn_percent,a.a1st_quoted_price,a.confirm_price,a.revised_price,a.price_with_commn_dzn,a.costing_per_id,a.design_pre_cost,a.design_percent,a.studio_pre_cost,a.studio_percent,a.offer_qnty,b.job_no
			from wo_price_quotation_costing_mst a,wo_po_details_master b
			where a.quotation_id in($all_quot_id) and a.status_active=1 and a.quotation_id=b.quotation_id and b.status_active=1 ";
		}
		if($db_type==2)
		{
			$sql = "SELECT MAX(a.id),a.fabric_cost,a.quotation_id,a.fabric_cost_percent,a.trims_cost,a.trims_cost_percent,a.embel_cost,a.embel_cost_percent,a.wash_cost,a.wash_cost_percent,a.comm_cost,a.comm_cost_percent,a.commission,a.commission_percent,a.lab_test,a.lab_test_percent,a.inspection,a.inspection_percent,a.cm_cost,a.cm_cost_percent,a.freight,a.freight_percent,a.currier_pre_cost,a.currier_percent ,a.certificate_pre_cost,a.certificate_percent,a.common_oh,a.common_oh_percent,a.depr_amor_pre_cost,a.depr_amor_po_price,a.interest_pre_cost,a.interest_po_price,a.income_tax_pre_cost,a.income_tax_po_price,a.total_cost ,a.total_cost_percent,a.final_cost_dzn ,a.final_cost_dzn_percent ,a.confirm_price_dzn ,a.confirm_price_dzn_percent,a.final_cost_pcs,a.margin_dzn,a.margin_dzn_percent,a.a1st_quoted_price,a.confirm_price,a.revised_price,a.price_with_commn_dzn,a.costing_per_id,a.design_pre_cost,a.design_percent,a.studio_pre_cost,a.studio_percent,b.job_no
			from wo_price_quotation_costing_mst a,wo_po_details_master b
			where a.quotation_id=b.quotation_id and a.quotation_id in($all_quot_id) and a.status_active=1 and b.status_active=1   group by a.fabric_cost,a.quotation_id,a.fabric_cost_percent,a.trims_cost,a.trims_cost_percent,a.embel_cost,a.embel_cost_percent,a.wash_cost,a.wash_cost_percent,a.comm_cost,a.comm_cost_percent,a.commission,a.commission_percent,a.lab_test,a.lab_test_percent,a.inspection,a.inspection_percent,a.cm_cost,a.cm_cost_percent,a.freight,a.freight_percent,a.currier_pre_cost,a.currier_percent ,a.certificate_pre_cost,a.certificate_percent,a.common_oh,a.common_oh_percent,a.depr_amor_pre_cost,a.depr_amor_po_price,a.interest_pre_cost,a.interest_po_price,a.income_tax_pre_cost,a.income_tax_po_price,a.total_cost ,a.total_cost_percent,a.final_cost_dzn ,a.final_cost_dzn_percent ,a.confirm_price_dzn ,a.confirm_price_dzn_percent,a.final_cost_pcs,a.margin_dzn,a.margin_dzn_percent,a.a1st_quoted_price,a.confirm_price,a.revised_price,a.price_with_commn_dzn,a.costing_per_id,a.design_pre_cost,a.design_percent,a.studio_pre_cost,a.studio_percent,b.job_no";
		}
		// echo $sql;die();
		$data_array=sql_select($sql);

        foreach( $data_array as $row )
        {
			//$sl=$sl+1;
			if($row[csf("costing_per_id")]==1){$order_price_per_dzn=12;$costing_val=" DZN";}
			else if($row[csf("costing_per_id")]==2){$order_price_per_dzn=1;$costing_per=" PCS";}
			else if($row[csf("costing_per_id")]==3){$order_price_per_dzn=24;$costing_val=" 2 DZN";}
			else if($row[csf("costing_per_id")]==4){$order_price_per_dzn=36;$costing_val=" 3 DZN";}
			else if($row[csf("costing_per_id")]==5){$order_price_per_dzn=48;$costing_val=" 4 DZN";}
			$order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
			$quot_price_per_dzn_arr[$row[csf("quotation_id")]]['order_price_per_dzn']=$order_price_per_dzn;
			$price_dzn=$row[csf("confirm_price_dzn")];
			$others_cost_value=$row[csf("total_cost")]-$row[csf("cm_cost")]-$row[csf("freight")]-$row[csf("comm_cost")]-$row[csf("commission")];
			$summary_data[$row[csf('job_no')]]['price_with_commn_dzn']+=$row[csf("price_with_commn_dzn")];
			$summary_data[$row[csf('job_no')]]['price_with_total_value']+=($row[csf("price_with_commn_dzn")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['price_dzn']+=$row[csf("confirm_price_dzn")];
		    $summary_data[$row[csf('job_no')]]['commission_dzn']+=$row[csf("commission")];
			$summary_data[$row[csf('job_no')]]['commission_total_value']+=($row[csf("commission")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['trims_cost_dzn']+=$row[csf("trims_cost")];
			$summary_data[$row[csf('job_no')]]['trims_cost_total_value']+=($row[csf("trims_cost")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['embel_cost_dzn']+=$row[csf("embel_cost")];
			$summary_data[$row[csf('job_no')]]['embel_cost_total_value']+=($row[csf("embel_cost")]/$order_price_per_dzn)*$order_job_qnty;
			//$row[csf("commission")]
			$other_direct_expenses=$row[csf("wash_cost")]+$row[csf("lab_test")]+$row[csf("inspection")]+$row[csf("currier_pre_cost")]+$row[csf("certificate_pre_cost")]+$row[csf("design_pre_cost")]+$row[csf("studio_pre_cost")];

			$summary_data[$row[csf('job_no')]]['other_direct_dzn']+=$other_direct_expenses;
			$summary_data[$row[csf('job_no')]]['other_direct_total_value']+=($other_direct_expenses/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['wash_cost_dzn']+=$row[csf("wash_cost")];
			$summary_data[$row[csf('job_no')]]['wash_cost_total_value']+=($row[csf("wash_cost")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['lab_test_dzn']+=$row[csf("lab_test")];
			$summary_data[$row[csf('job_no')]]['lab_test_total_value']+=($row[csf("lab_test")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['inspection_dzn']+=$row[csf("inspection")];
			$summary_data[$row[csf('job_no')]]['inspection_total_value']+=($row[csf("inspection")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['freight_dzn']+=$row[csf("freight")];
			$summary_data[$row[csf('job_no')]]['freight_total_value']+=($row[csf("freight")]/$order_price_per_dzn)*$order_job_qnty;
			$freight_cost_data[$row[csf("job_no")]]['freight_total_value']=($row[csf("freight")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['currier_pre_cost_dzn']+=$row[csf("currier_pre_cost")];
			$summary_data[$row[csf('job_no')]]['currier_pre_cost_total_value']+=($row[csf("currier_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['certificate_pre_cost_dzn']+=$row[csf("certificate_pre_cost")];
			$summary_data[$row[csf('job_no')]]['certificate_pre_cost_total_value']+=($row[csf("certificate_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;

			$summary_data[$row[csf('job_no')]]['design_pre_cost_dzn']+=$row[csf("design_pre_cost")];
			$summary_data[$row[csf('job_no')]]['design_pre_cost_total_value']+=($row[csf("design_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['studio_pre_cost_dzn']+=$row[csf("studio_pre_cost")];
			$summary_data[$row[csf('job_no')]]['studio_pre_cost_total_value']+=($row[csf("studio_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;

			$quot_studio_cost_dzn_arr[$row[csf("job_no")]]['studio_dzn_cost']=$row[csf("studio_percent")];
			$quot_studio_cost_dzn_arr[$row[csf("job_no")]]['common_oh']=$row[csf("common_oh")];

			$fab_amount_dzn=$fab_summary_data[$row[csf("job_no")]]['fab_amount_dzn'];
			$summary_data[$row[csf('job_no')]]['fab_amount_dzn']+=$fab_amount_dzn;
			$summary_data[$row[csf('job_no')]]['fab_amount_total_value']+=($fab_amount_dzn/$order_price_per_dzn)*$order_job_qnty;
			$yarn_amount_dzn=$yarn_summary_data[$row[csf("job_no")]]['yarn_amount_dzn'];
			//echo ($yarn_amount_dzn/$order_price_per_dzn)*$order_job_qnty.'d';
			$summary_data[$row[csf('job_no')]]['yarn_amount_dzn']+=$yarn_amount_dzn;
			$summary_data[$row[csf('job_no')]]['yarn_amount_total_value']+=($yarn_amount_dzn/$order_price_per_dzn)*$order_job_qnty;
			$conv_amount_dzn=$conv_summary_data[$row[csf("job_no")]]['conv_amount_dzn'];
			$summary_data[$row[csf('job_no')]]['conversion_cost_dzn']+=$conv_amount_dzn;
			$summary_data[$row[csf('job_no')]]['conversion_cost_total_value']+=($conv_amount_dzn/$order_price_per_dzn)*$order_job_qnty;

			//$NetFOBValue=($row[csf("price_with_commn_dzn")]-$row[csf("commission")]);
			$net_value_dzn=$row[csf("price_with_commn_dzn")];

			$summary_data[$row[csf('job_no')]]['netfobvalue_dzn']+=($row[csf("price_with_commn_dzn")]);
			$summary_data[$row[csf('job_no')]]['netfobvalue']+=(($row[csf("price_with_commn_dzn")])/$order_price_per_dzn)*$order_job_qnty;

			//yarn_amount_total_value
			$all_cost_dzn=$yarn_amount_dzn+$fab_amount_dzn+$conv_amount_dzn+$row[csf("trims_cost")]+$row[csf("embel_cost")]+$other_direct_expenses;
			//echo $yarn_amount_dzn.'Y='.$fab_amount_dzn.'F='.$conv_amount_dzn.'Cnv='.$row[csf("trims_cost")].'Tr='.$row[csf("embel_cost")].'Em='.$other_direct_expenses;
			$summary_data[$row[csf('job_no')]]['cost_of_material_service']+=$all_cost_dzn;
			$summary_data[$row[csf('job_no')]]['cost_of_material_service_total_value']+=($all_cost_dzn/$order_price_per_dzn)*$order_job_qnty;
			$contribute_netfob_value_dzn=$net_value_dzn-($fab_amount_dzn+$yarn_amount_dzn+$conv_amount_dzn+$row[csf("trims_cost")]+$row[csf("embel_cost")]+$other_direct_expenses);
			$summary_data[$row[csf('job_no')]]['contribution_margin_dzn']+=$contribute_netfob_value_dzn;
			$summary_data[$row[csf('job_no')]]['contribution_margin_total_value']+=(($contribute_netfob_value_dzn)/$order_price_per_dzn)*$order_job_qnty;

			$summary_data[$row[csf('job_no')]]['cm_cost_dzn']+=$row[csf("cm_cost")];
			$summary_data[$row[csf('job_no')]]['cm_cost_total_value']+=($row[csf("cm_cost")]/$order_price_per_dzn)*$order_job_qnty;

			$summary_data[$row[csf('job_no')]]['comm_cost_dzn']+=$row[csf("comm_cost")];
			$summary_data[$row[csf('job_no')]]['comm_cost_total_value']+=($row[csf("comm_cost")]/$order_price_per_dzn)*$order_job_qnty;

			$summary_data[$row[csf('job_no')]]['common_oh_dzn']+=$row[csf("common_oh")];
			$summary_data[$row[csf('job_no')]]['common_oh_total_value']+=($row[csf("common_oh")]/$order_price_per_dzn)*$order_job_qnty;
			//echo $netfob_value_dzn.'='.$row[csf("cm_cost")];
			$Contribution_Margin=$netfob_value_dzn-$LessCostOfMaterialServices;
			$tot_gross_profit_dzn=$contribute_netfob_value_dzn-$row[csf("cm_cost")];
			$summary_data[$row[csf('job_no')]]['gross_profit_dzn']+=$tot_gross_profit_dzn;
			$summary_data[$row[csf('job_no')]]['gross_profit_total_value']+=(($tot_gross_profit_dzn)/$order_price_per_dzn)*$order_job_qnty;

			//$Gross_Profit= $Contribution_Margin-$row[csf("cm_cost")];
			$operate_profit_loss_dzn=$tot_gross_profit_dzn;//-($row[csf("comm_cost")]+$row[csf("common_oh")]);
			$summary_data[$row[csf('job_no')]]['operating_profit_loss_dzn']+=$operate_profit_loss_dzn;
			$summary_data[$row[csf('job_no')]]['operating_profit_loss_total_value']+=($operate_profit_loss_dzn/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['depr_amor_pre_cost_dzn']+=$row[csf("depr_amor_pre_cost")];
			$summary_data[$row[csf('job_no')]]['depr_amor_pre_cost_total_value']+=($row[csf("depr_amor_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['interest_pre_cost_dzn']+=$row[csf("interest_pre_cost")];
			$summary_data[$row[csf('job_no')]]['interest_pre_cost_total_value']+=($row[csf("interest_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['income_tax_pre_cost_dzn']+=$row[csf("income_tax_pre_cost")];
			$summary_data[$row[csf('job_no')]]['income_tax_pre_cost_total_value']+=($row[csf("income_tax_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
			$net_profit_dzn=$operate_profit_loss_dzn-($row[csf("depr_amor_pre_cost")]+$row[csf("interest_pre_cost")]+$row[csf("income_tax_pre_cost")]);
			$summary_data[$row[csf('job_no')]]['net_profit_dzn']+=$net_profit_dzn;
			$summary_data[$row[csf('job_no')]]['net_profit_dzn_total_value']+=($net_profit_dzn/$order_price_per_dzn)*$order_job_qnty;
		}
		// echo "<pre>";
		// print_r($summary_data);
		// die();
		//======================================================================

		$sql_commi = "SELECT a.id,a.quotation_id,a.particulars_id,a.commission_base_id,a.commision_rate,a.commission_amount,a.status_active,b.job_no
		from  wo_pri_quo_commiss_cost_dtls a,wo_po_details_master b
		where  a.quotation_id=b.quotation_id and a.quotation_id in($all_quot_id) and a.status_active=1 and a.commission_amount>0 and b.status_active=1";
		// echo $sql_commi;die();
		$result_commi=sql_select($sql_commi);
		$CommiData_foreign_cost=$CommiData_lc_cost=$foreign_dzn_commission_amount=$local_dzn_commission_amount=0;
		foreach($result_commi as $row)
		{
			$order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
			$order_price_per_dzn=$quot_price_per_dzn_arr[$row[csf("quotation_id")]]['order_price_per_dzn'];

			if($row[csf("particulars_id")]==1) //Foreign
			{
				$CommiData_foreign_cost+=($row[csf("commission_amount")]/$order_price_per_dzn)*$order_job_qnty;
				$foreign_dzn_commission_amount+=$row[csf("commission_amount")];
				$CommiData_foreign_quot_cost_arr[$row[csf("job_no")]]+=($row[csf("commission_amount")]/$order_price_per_dzn)*$order_job_qnty;
			}
			else
			{
				$CommiData_lc_cost+=($row[csf("commission_amount")]/$order_price_per_dzn)*$order_job_qnty;
				$local_dzn_commission_amount+=$row[csf("commission_amount")];
				$commision_local_quot_cost_arr[$row[csf("job_no")]]=$row[csf("commision_rate")];
			}
		}
		//=====================================================================================
		$sql_comm="SELECT a.item_id,a.quotation_id,sum(a.amount) as amount,b.job_no from wo_pri_quo_comarcial_cost_dtls a,wo_po_details_master b where a.quotation_id=b.quotation_id and a.quotation_id in($all_quot_id) and a.status_active=1 group by a.quotation_id,a.item_id,b.job_no";
		// echo $sql_comm;die();
		$tot_lc_dzn_Commer=$tot_without_lc_dzn_Commer=0;
		// $summary_data['comm_cost_dzn']=0;
		// $summary_data['comm_cost_total_value']=0;
		$result_comm=sql_select($sql_comm);
		$commer_lc_cost = array();
		$commer_without_lc_cost = array();
		foreach($result_comm as $row)
		{
			$order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
			$order_price_per_dzn=$quot_price_per_dzn_arr[$row[csf("quotation_id")]]['order_price_per_dzn'];
			//$summary_data['comm_cost_dzn']=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;
			$comm_amtPri=$row[csf('amount')];
			$item_id=$row[csf('item_id')];
			if($item_id==1)//LC
			{
				$commer_lc_cost[$row[csf('job_no')]]+=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;

				$commer_lc_cost_quot_arr[$row[csf("job_no")]]+=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;
			}
			else
			{
				$commer_without_lc_cost[$row[csf('job_no')]]+=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;
			}
		}
		// echo "<pre>";print_r($summary_data);die();

		// echo $total_quot_commision_local_val;die();
		/********************************************************************************************************
		*																										*
		*													END													*
		*																										*
		********************************************************************************************************/

		$gr_po_qnty_pcs=0;
		$gr_po_qnty_val=0;
		$gr_po_qnty_val_perc=0;
		$gr_ttl_ex_qnty=0;
		$gr_ttl_ex_qnty_val=0;
		$gr_sales_min=0;
		$gr_ttl_carton=0;
		$gr_ttl_basic_qty=0;
		$gr_ttl_ex_fac_per=0;
		$gr_ttl_short_qty=0;
		$gr_ttl_short_val=0;
		$gr_ttl_sales_cm=0;
		$total_cm_pcs_value=0;
		$total_fob_value=0;

		//$po_exist_arr=array();
		if($all_po_ids!="")
		{
			$cm_gmt_cost_dzn_arr=array();
			$cm_gmt_cost_dzn_arr=fnc_po_wise_cm_gmt_class($all_company_ids,$all_po_ids);
		}

		foreach($sql_result as $row)
		{
			/*========================================================================================
			*																						  *
			*								Calculate cm valu 										  *
			*																					  	  *
			*========================================================================================*/
			$tot_dye_chemi_process_amount 	= $conversion_cost_arr[$row[csf('job_no')]][101]['conv_amount_total_value'];
			$tot_yarn_dye_process_amount 	= $conversion_cost_arr[$row[csf('job_no')]][30]['conv_amount_total_value'];
			$tot_aop_process_amount 		= $conversion_cost_arr[$row[csf('job_no')]][35]['conv_amount_total_value'];

			$total_quot_qty=$total_quot_pcs_qty=$total_quot_amount=$total_sew_smv=$total_quot_amount_cal=0;
			$all_last_shipdates='';

            foreach($style_wise_arr as $style_key=>$val)
			{
				$total_cost=$val[('qty')]*$val[('final_cost_pcs')];
				$total_quot_qty+=$val[('qty')];
				$total_quot_pcs_qty+=$val[('qty_pcs')];
				$total_sew_smv+=$val[('sew_smv')];
				$total_quot_amount+=$total_cost;
				$total_quot_amount_arr[$val[('quotation_id')]]+=$total_cost;
			}
			$total_quot_amount_cal = $style_wise_arr[$row[csf('job_no')]]['qty']*$style_wise_arr[$row[csf('job_no')]]['final_cost_pcs'];
			$tot_cm_for_fab_cost=$summary_data[$row[csf('job_no')]]['conversion_cost_total_value']-($tot_yarn_dye_process_amount+$tot_dye_chemi_process_amount+$tot_aop_process_amount);
			// echo $row[csf('job_no')]."==".$summary_data[$row[csf('job_no')]]['conversion_cost_total_value']."-(".$tot_yarn_dye_process_amount."+".$tot_dye_chemi_process_amount."+".$tot_aop_process_amount.")<br>";
			$commision_quot_local=$commision_local_quot_cost_arr[$row[csf('job_no')]];
			$tot_sum_amount_quot_calc=$total_quot_amount_cal-($CommiData_foreign_quot_cost_arr[$row[csf('job_no')]]+$commer_lc_cost_quot_arr[$row[csf('job_no')]]+$freight_cost_data[$row[csf('job_no')]]['freight_total_value']);
			$tot_sum_amount_quot_calccc = ($tot_sum_amount_quot_calc*$commision_quot_local)/100;
			$tot_inspect_cour_certi_cost=$summary_data[$row[csf('job_no')]]['inspection_total_value']+$summary_data[$row[csf('job_no')]]['currier_pre_cost_total_value']+$summary_data[$row[csf('job_no')]]['certificate_pre_cost_total_value']+$tot_sum_amount_quot_calccc+$summary_data[$row[csf('job_no')]]['design_pre_cost_total_value'];
			// echo $summary_data[$row[csf('job_no')]]['inspection_total_value']."+".$summary_data[$row[csf('job_no')]]['currier_pre_cost_total_value']."+".$summary_data[$row[csf('job_no')]]['certificate_pre_cost_total_value']."+".$tot_sum_amount_quot_calccc."+".$summary_data[$row[csf('job_no')]]['design_pre_cost_total_value']."<br>";

			$tot_emblish_cost=$summary_data[$row[csf('job_no')]]['embel_cost_total_value'];
			$pri_freight_cost_per=$summary_data[$row[csf('job_no')]]['freight_total_value'];
			$pri_commercial_per=$commer_lc_cost[$row[csf('job_no')]];
			$CommiData_foreign_cost=$CommiData_foreign_quot_cost_arr[$row[csf('job_no')]];

			$total_btb=$summary_data[$row[csf('job_no')]]['lab_test_total_value']+$tot_emblish_cost+$summary_data[$row[csf('job_no')]]['comm_cost_total_value']+$summary_data[$row[csf('job_no')]]['trims_cost_total_value']+$tot_yarn_dye_process_amount+$tot_dye_chemi_process_amount+$summary_data[$row[csf('job_no')]]['yarn_amount_total_value']+$tot_aop_process_amount+$summary_data[$row[csf('job_no')]]['common_oh_total_value']+$summary_data[$row[csf('job_no')]]['studio_pre_cost_total_value']+$tot_inspect_cour_certi_cost;
			// echo $summary_data[$row[csf('job_no')]]['lab_test_total_value']."+".$tot_emblish_cost."+".$summary_data[$row[csf('job_no')]]['comm_cost_total_value']."+".$summary_data[$row[csf('job_no')]]['trims_cost_total_value']."+".$tot_yarn_dye_process_amount."+".$tot_dye_chemi_process_amount."+".$summary_data[$row[csf('job_no')]]['yarn_amount_total_value']."+".$tot_aop_process_amount."+".$summary_data[$row[csf('job_no')]]['common_oh_total_value']."+".$summary_data[$row[csf('job_no')]]['studio_pre_cost_total_value']."+".$tot_inspect_cour_certi_cost."<br>";
			$tot_quot_sum_amount=$total_quot_amount_cal-($CommiData_foreign_cost+$pri_freight_cost_per+$pri_commercial_per);
			// echo $total_quot_amount_cal."-(".$CommiData_foreign_cost."+".$pri_freight_cost_per."+".$pri_commercial_per.")<br>";
			$NetFOBValue_job = $tot_quot_sum_amount;
			// echo $NetFOBValue_job."<br>";
			$total_cm_for_gmt=($NetFOBValue_job-$tot_cm_for_fab_cost-$total_btb);

			$total_quot_pcs_qty = $quotation_qty_array[$row[csf('job_no')]]['QTY_PCS'];
			// echo $total_cm_for_gmt;echo "<br>";
			$cm_valu_lc = 0;
			$cm_valu_lc = $total_cm_for_gmt/$total_quot_pcs_qty;
			// echo number_format(($total_cm_for_gmt/$total_quot_pcs_qty)*12,2); echo "<br>";
			/*========================================================================================
			*																						  *
			*											END											  *
			*																					  	  *
			*========================================================================================*/
			$cm_gmt_cost_dzn=$cm_gmt_cost_dzn_arr[$row[csf('po_id')]]['dzn'];
			$costing_per=$costing_per_arr[$row[csf('job_no')]];
			if($costing_per==1) $dzn_qnty=12;
			else if($costing_per==3) $dzn_qnty=12*2;
			else if($costing_per==4) $dzn_qnty=12*3;
			else if($costing_per==5) $dzn_qnty=12*4;
			else $dzn_qnty=1;
			$cm_per_pcs=$cm_gmt_cost_dzn/$dzn_qnty;

			$dzn_qnty2=$dzn_qnty ;
			$dzn_qnty=$dzn_qnty*$row[csf('total_set_qnty')];

			//$cm_per_pcs=$tot_cost_arr[$row[csf('job_no')]]/$dzn_qnty2;

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


			list($first_button)=explode(',',$print_report_format[$row[csf("company_name")]]);

			foreach($challan_id as $val)
			{
				$first_button=($first_button=='')?0:$first_button;
				$fv=$first_button.",".$val.",".$row[csf("company_name")].",".$row[csf("del_company")].",'".$row[csf('ex_factory_date')]."'";

				if($challan_no=="") $challan_no='<a href="javascript:fn_generate_print('.$fv.');">'.$challan_mst_arr[$val][$row[csf('po_id')]]['challan'].'<a/>'; else $challan_no.=','.'<a href="javascript:fn_generate_print('.$fv.');">'.$challan_mst_arr[$val][$row[csf('po_id')]]['challan'].'<a/>';


				if($floor_no=="") $floor_no=$floor_library[$challan_mst_arr[$val][$row[csf('po_id')]]['floor']]; else $floor_no.=','.$floor_library[$challan_mst_arr[$val][$row[csf('po_id')]]['floor']];

				if($forwarder=="") $forwarder=$forwarder_arr[$challan_mst_arr[$val][$row[csf('po_id')]]['forwarder']]; else $forwarder.=','.$forwarder_arr[$challan_mst_arr[$val][$row[csf('po_id')]]['forwarder']];
				if($vehi_no=="") $vehi_no=$challan_mst_arr[$val][$row[csf('po_id')]]['truck_no']; else $vehi_no.=','.$challan_mst_arr[$val][$row[csf('po_id')]]['truck_no'];
				if($dirver_info=="") $dirver_info="Name: ".$challan_mst_arr[$val][$row[csf('po_id')]]['driver_name']."<br>Mob No: ".$challan_mst_arr[$val][$row[csf('po_id')]]['mobile_no']."<br>DL No: ".$challan_mst_arr[$val][$row[csf('po_id')]]['dl_no']; else $dirver_info.=','."Name: ".$challan_mst_arr[$val][$row[csf('po_id')]]['driver_name']."<br>Mob No: ".$challan_mst_arr[$val][$row[csf('po_id')]]['mobile_no']."<br>DL No: ".$challan_mst_arr[$val][$row[csf('po_id')]]['dl_no'];
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

			$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
			$comapny_id=$row[csf("company_name")];
			$delv_comp=($row[csf("source")]!=3)?  $company_library[$row[csf("del_company")]] : $supp_library[$row[csf("del_company")]];

			$onclick=" change_color('tr_".$i."','".$bgcolor."')";
			$details_report .='<tr bgcolor="'.$bgcolor.'" onclick="'.$onclick.'" id="tr_'.$i.'">';
			$details_report .='<td width="40" align="center">'.$i.'</td>
								<td width="125" align="center" ><p>'.$delv_comp.'</p></td>
								<td width="125" align="center" ><p>'.$floor_no.'</p></td>
								<td width="100" align="center" ><p>'.$buyer_arr[$row[csf("buyer_name")]].'</p></td>
								<td width="60" align="center" ><p>'.$row[csf("job_no_prefix_num")].'</p></td>
								<td width="110" align="center"><p>'.$row[csf("po_number")].'</p></td>
								<td width="150"><p>'.$garments_item[$row[csf("item_number_id")]].'</p></td>
								';


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


								$total_ex_fact_qty=$exfact_qty_arr_without_current[$row[csf("po_id")]][$row[csf("item_number_id")]]["ex_fact"] ;

								$total_cartoon_qty=$exfact_qty_arr_without_current[$row[csf("po_id")]][$row[csf("item_number_id")]]["carton"] ;
								//$po_quantity=$row[csf("po_quantity")];
								$po_quantity=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]];
								$unit_price=$row[csf("unit_price")];




								$current_ex_Fact_Qty=$row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]][$row[csf("challan_id")]];
								$total_ex_qty+=$row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]][$row[csf("challan_id")]];



								$basic_qnty=($total_ex_fact_qty*$item_smv)/$basic_smv_arr[$row[csf("company_name")]];
								$cm_per_pcs_tot+=($cm_per_pcs*$current_ex_Fact_Qty);
								$short_excess=$total_ex_fact_qty-$po_quantity;

								$excess_msg=($short_excess>0) ?" color:green;":"color:black;";
								$excess_val_msg=(($total_ex_fact_qty*$unit_price)-$po_quantity>0) ?" color:green;":"color:black;";
								$ttl_ex_qty=($total_ex_fact_qty/$po_quantity)*100;
								$ttl_ex_qty_msg=($ttl_ex_qty>100) ?" color:green;":"color:black;";


								$gr_order_fob_value=$current_ex_Fact_Qty*$unit_price;




			//$cm_per_pcs=(($unit_price*$dzn_qnty2)-$total_cost_arr[$row[csf('job_no')]])+$cm_cost_arr[$row[csf('job_no')]];
			//$cm_per_pcs=$cm_per_pcs/$dzn_qnty2;


			$details_report .='
								<td width="80" align="right"><p>'. number_format($po_quantity,0,'', '').'</p></td>
								<td width="70" align="right"><p>'. number_format($unit_price,4).'</p></td>
								<td width="100" align="right"><p>'. number_format(($po_quantity*$unit_price),2).'</p></td>
								<td width="70" align="center"><p>'.change_date_format($row[csf("shipment_date")]).'</p></td>
								<td width="70" align="center"><a href="##" onclick="openmypage_ex_date('.$comapny_id.",'".$row[csf('po_id')]."','".$row[csf('item_number_id')]."','".$ex_fact_date_range."','ex_date_popup','".$row[csf('challan_id')]."'".",'1'".')">'.change_date_format($row[csf('ex_factory_date')]).'</a></td>


								<td width="100" align="right" title="CM Per Dzn: '.number_format($cm_gmt_cost_dzn,4).'"><p><a href="##" onclick="openmypage_ex_date('.$comapny_id.",'".$row[csf('po_id')]."','".$row[csf('item_number_id')]."','".$ex_fact_date_range."','ex_date_popup','".$row[csf('challan_id')]."'".",'1'".')">'. number_format($current_ex_Fact_Qty,0,'.', '').'</a></p></td>


								<td width="100" align="right" title="CM Per Pcs: '.number_format($cm_per_pcs,4).'"><p>'.number_format($cm_per_pcs*$current_ex_Fact_Qty,2).'</p></td>
								<td width="100" align="right" title="CM Per Pcs: '.number_format($cm_valu_lc,4).',CM Per Dzn: '.number_format($cm_valu_lc*12,4).'"><p>'.number_format($cm_valu_lc*$current_ex_Fact_Qty,2).'</p></td>
								<td width="100" align="right"  ><p>'.number_format($gr_order_fob_value,2).'</p></td>
								<td width="70" align="center"><p>'.$shipment_mode[$row[csf("shiping_mode")]].'</p></td>
								<td width="70" align="center"><p>'.$shipment_status[$row[csf('shiping_status')]].'</p></td>
								<td width="100" align="center"><p>'.$challan_no.'</p></td>
								<td align="center"><p>'.$vehi_no.'</p></td>
							</tr>';

			$total_cm_pcs_value+=($cm_per_pcs*$current_ex_Fact_Qty);
			$total_fob_value+=$gr_order_fob_value;


			$total_po_qty+=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]];
 	  		$total_po_valu+=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]]*$row[csf("unit_price")];


			$master_data[$row[csf("buyer_name")]]['ex_factory_qnty'] +=$row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]][$row[csf("challan_id")]];
			$master_data[$row[csf("buyer_name")]]['ex_factory_value'] +=($row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]][$row[csf("challan_id")]])*$row[csf("unit_price")];


			$master_data[$row[csf("buyer_name")]]['cm_value'] +=($cm_per_pcs*$current_ex_Fact_Qty);
			$master_data[$row[csf("buyer_name")]]['cm_value_lc'] +=($cm_valu_lc*$current_ex_Fact_Qty);




			$total_basic_qty+=$basic_qnty;

			$total_crtn_qty+=$row[csf("carton_qnty")];
			$total_ex_valu+=($row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]][$row[csf("challan_id")]])*$row[csf("unit_price")];
			$g_total_ex_qty+=$total_ex_fact_qty;
			$g_total_ex_crtn+=$total_cartoon_qty;
			$g_total_ex_val+=$total_ex_fact_qty*$row[csf("unit_price")];
			$g_sales_minutes+=$total_sales_minutes;
			// $g_sales_minutes+=$current_ex_Fact_Qty*$item_smv;
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



		foreach($master_data as $rows)
		{
			$total_po_val+=$rows["po_value"];
		}

		?>
        <div style="width:3100x;">
            <div style="width:850px" id="summary" >
                <table width="850"  cellspacing="0"  align="center">
                    <tr>
                        <td align="center" colspan="8" class="form_caption">
                            <strong style="font-size:16px;">
							<?
							foreach(explode(',',$cbo_delivery_company_name) as $com_id){
								$company_text[$com_id]= $company_library[$com_id];
							}
							echo ($cbo_company_name)?$company_library[$cbo_company_name]:implode(',',$company_text);?>

                            </strong>
                        </td>
                    </tr>
                    <tr class="form_caption">
                        <td colspan="8" align="center" class="form_caption"> <strong style="font-size:15px;">Daily Ex-Factory Report</strong></td>
                    </tr>
                     <tr align="center">
                        <td colspan="8" align="center" class="form_caption"> <strong style="font-size:15px;">Total Summary</strong></td>
                    </tr>
                    </table>



                    <table width="820" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1">
                    <thead>
                        <th width="40" height="34">SL</th>
                        <th width="130">Buyer Name</th>
                        <th width="100">PO Qty (pcs)</th>
                        <th width="130">PO Value</th>
                        <th width="100">Export Qty.</th>
                        <th width="100">Export FOB Value </th>
                        <th width="100">Export CM Value BoM</th>
                        <th>Export CM Value LC</th>
                    </thead>
                 </table>
                	<table width="820" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body">
                 <?
                 $m=1;
                 $grand_sales_minute =0;
                 foreach($master_data as $rows)
                {
                     $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";

					 ?>
                  	<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" >
                        <td width="40" align="center"><? echo $m; ?></td>
                        <td width="130">
                        <p><?
                        echo $buyer_arr[$rows[b_id]]. $master_data[$rows[b_id]]['in_sub'];
                        ?></p>
                        </td>
                        <td width="100" align="right"><p><? $po_quantity=$rows[po_qnty];echo number_format($po_quantity,0); $total_buyer_po_quantity+=$po_quantity; ?></p></td>
                        <td width="130" align="right" ><p  id="value_<? echo $i ; ?>"><? $buyer_po_value=$rows["po_value"]; echo number_format($buyer_po_value,2 ,'.', '');  $total_buyer_po_value+=$buyer_po_value; ?></p></td>



                        <td align="right" width="100">
                        <p><?
                         $ex_factory_qnty=$rows[ex_factory_qnty]; echo number_format($ex_factory_qnty,0,'',''); $mt_total_ex_fact_qty+=$ex_factory_qnty;

                        ?></p>
                        </td>
                        <td align="right" width="100">
                        <p><?
                         $ex_factory_value=$rows[ex_factory_value];  echo  number_format($ex_factory_value,2,'.',''); $mt_total_ex_fact_value+=$ex_factory_value;
                        ?></p>
                        </td>
                        <td width="100" align="right"><? echo number_format($rows[cm_value],2,'.','');?></td>
                        <td align="right"><? echo number_format($rows[cm_value_lc],2,'.','');?></td>

                    </tr>
                    <?
                    $i++;$m++;
                    $buyer_po_quantity=0;
                    $buyer_po_value=0;
                    $current_ex_Fact_Qty=0;
                    $current_ex_fact_value=0;
                    $ex_factory_qnty=0;
                    $ex_factory_value=0;
                    $g_sales_min=0;
                    $grand_sales_minute +=number_format($rows["sales_min"],0,'','');
					$tot_cm_value+=$rows[cm_value];
					$tot_cm_value_lc+=$rows[cm_value_lc];

                }
                    ?>
                    <input type="hidden" name="total_i" id="total_i" value="<? echo $i; ?>" />
                    <tfoot>
                        <th align="right" colspan="2"><b>Total:</b></th>
                        <th  align="right" id="total_buyer_po_quantity"><? echo number_format($total_buyer_po_quantity,0);  ?></th>
                        <th  align="right" id="value_total_buyer_po_value"><? echo number_format($total_buyer_po_value,2 ,'.', ''); ?> </th>
                        <th align="right" id="mt_total_ex_fact_qty"><? echo number_format($mt_total_ex_fact_qty,0); ?></th>
                        <th align="right" id="value_mt_total_ex_fact_value"><? echo number_format($mt_total_ex_fact_value,2); ?></th>
                        <th align="right"><? echo number_format($tot_cm_value,2); ?></th>
                        <th align="right"><? echo number_format($tot_cm_value_lc,2); ?></th>
                    </tfoot>
                </table>


            </div>
            <br />
            <div>
                <table width="1850"  >
                    <tr>
                    <td colspan="20" class="form_caption"><strong style="font-size:16px;">Shipped Out Order Details Report</strong></td>
                    </tr>
                </table>
                <table width="1850" border="1" class="rpt_table" rules="all" id="table_header_2">
                    <thead>
                        <th width="40">SL</th>
                        <th width="125">Delv. Company</th>
                        <th width="125">Delv. Floor</th>
                        <th width="100">Buyer Name</th>
                        <th width="60">Job</th>
                        <th width="110">Order NO</th>
                        <th width="150">Item Name</th>
                        <th width="80">PO Qtny. (pcs)</th>
                        <th width="70">Unit Price</th>
                        <th width="100">PO Value</th>
                        <th width="70">Shipment Date</th>
                        <th width="70">Ex-Fac. Date</th>
                        <th width="100">Export Qty(pcs)</th>
                        <th width="100">Export CM Value BoM</th>
                        <th width="100">Export CM Value LC</th>
                        <th width="100">Export FOB Value</th>
                        <th width="70">Ship. Mode</th>
                        <th width="70">Ex-Fact Status</th>
                        <th width="100">Challan NO</th>
                        <th>Vehicle No</th>
                    </thead>
                </table>

           <div style="width:1870px; overflow-y:scroll; overflow-x:hidden; max-height:300px;"  id="scroll_body" >
			<? echo $details_report;
            	foreach($subcon_exfactory_arr____ as $po_id=>$item_data)
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
									$bgcolor=($jj%2==0)?"#E9F3FF":"#FFFFFF";
            						$onclick=" change_color('tr2_".$jj."','".$bgcolor."')";


							//$cm_per_pcs=(($unit_price*12)-$total_cost_arr[$row[csf('job_no')]])+$cm_cost_arr[$row[csf('job_no')]];
							//$cm_per_pcs=$cm_per_pcs/12;




							$cm_gmt_cost_dzn=$cm_gmt_cost_dzn_arr[$po_id]['dzn'];
							$costing_per=$costing_per_arr[$row[csf('job_no')]];
							if($costing_per==1) $dzn_qnty=12;
							else if($costing_per==3) $dzn_qnty=12*2;
							else if($costing_per==4) $dzn_qnty=12*3;
							else if($costing_per==5) $dzn_qnty=12*4;
							else $dzn_qnty=1;
							$cm_per_pcs=$cm_gmt_cost_dzn/$dzn_qnty;


							list($first_button)=explode(',',$print_report_format[$row["company_name"]]);


								//if($row["within_group"]==1){$companyArr=$company_library;}
								//else{$companyArr=$supp_library;}

									?>
										<tr onclick="<? echo $onclick;?>"  id="tr2_<? echo $jj;?>" >
	            							<td width="40" align="center"><? echo $pp++;?></td>
	            							<td width="125" align="center" ><p><? echo $company_library[$delivery_company_id]; ?> </p></td>
	            							<td width="125" align="center" ><p></p></td>
	            							<td width="100" align="center" ><p><? echo $row["buyer_name"]; ?> </p></td>
	            							<td width="60" align="center" ><p><? echo $row["job"]; ?> </p></td>
	            							<td width="110" align="center"><p><? echo $row["po_number"]; ?> (In-Sub) </p></td>
	            							<td width="150"><p><? echo $garments_item[$item_id];?></p></td>
                                            <td width="80" align="right"><p><? echo  number_format($po_quantity,0,'', ''); ?> </p></td>
	            							<td width="70" align="right"><p><? echo  number_format($unit_price,4); ?> </p></td>
	            							<td width="100" align="right"><p><? echo  number_format(($po_quantity*$unit_price),2); ?> </p></td>
	            							<td width="70" align="center"><p><? echo change_date_format($row["shipment_date"]); ?> </p></td>
	            							<td width="70" align="center"><a href="##" onclick="openmypage_ex_date(<? echo $row["company_name"].",'".$po_id."','".$item_id."','".$date_id."','ex_date_popup','".$row['delivery_no']."'".',2'; ?> )"><? echo change_date_format($date_id); ?> </a></td>

                                            <td width="100" align="right"><p><a href="##" onclick="openmypage_ex_date(<? echo $row["company_name"].",'".$po_id."','".$item_id."','".$date_id."','ex_date_popup','".$row['delivery_no']."'".',2'; ?> )"><?
                                                                                        $current_ex_Fact_Qty=$row["prod_qty"]; echo  number_format($current_ex_Fact_Qty,0,'.', ''); ?> </a></p></td>
                                            <td width="100" align="right" title="CM per pcs: <? echo number_format($cm_per_pcs,4); ?> "><? echo number_format(($cm_per_pcs*$current_ex_Fact_Qty),2); ?></td>
                                            <td width="100" align="right" title="CM per pcs: <? echo number_format($cm_per_pcs,4); ?> "><? echo number_format(($cm_per_pcs*$current_ex_Fact_Qty),2); ?></td>

											<td width="100" align="right"><? echo number_format($current_ex_Fact_Qty*$unit_price,2);?></td>
	            							<td width="70" align="center"><p><? echo $shipment_mode[$row["shiping_mode"]]; ?> </p></td>
	            							<td width="70" align="center"><p><? echo $shipment_status[$row['shiping_status']];?></p></p></td>
	            							<td width="100" align="center"><p><? echo $row["delivery_no"]; ?> </p></td>
	            							<td align="center"><p><? echo $row["vehical_no"]; ?> </p></td>
	            						</tr>
            						<?


            						if($po_check_arr2[$row['po_id']][$item_id]=="")
            						{
            							$po_check_arr2[$row['po_id']][$item_id]=$row['po_id'];
            							$gr_po_qnty_pcs+=$po_quantity;
            							$gr_po_qnty_val+=$po_quantity*$unit_price;
            							$gr_ttl_ex_qnty_val+=$total_ex_fact_value;
            							$gr_ttl_carton_qt+=$total_cartoon_qty;
            							$gr_sales_min+=$total_sales_minutes;
            							$gr_ttl_basic_qty+=$basic_qnty;
            							$gr_ttl_ex_fac_per+=$total_ex_fact_qty_parcent;
            							$gr_ttl_short_qty+=$excess_shortage_qty;
            							$gr_ttl_short_val+=$excess_shortage_value;
            							$gr_ttl_sales_cm+=$cm_per_pcs*$total_ex_fact_qty;
										$total_ex_qty+= $current_ex_Fact_Qty;
										$total_cm_pcs_value+=($cm_per_pcs*$current_ex_Fact_Qty);
										$total_fob_value+=$current_ex_Fact_Qty*$unit_price;
            						}

            					}

            				}

            			}
            		}
            	}
            ?>

           </table>
            </div>
            </div>

        <table width="1850" cellspacing="0" cellpadding="0"  border="1"  class="rpt_table" rules="all" id="table_footer" align="left">
                <tfoot>
                    <tr>
                    	<th width="40">&nbsp;</th>
                        <th width="125">&nbsp;</th>
                        <th width="125">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="60" >&nbsp;</th>
                        <th width="110"></th>
                        <th width="150" align="right"><strong>Total</strong></th>
                        <th width="80" align="right"></th>
                        <th width="70">&nbsp;</th>
                        <th width="100" align="right"></th>
                        <th width="70">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="100" align="right" id="total_ex_qty_td"><? echo number_format($total_ex_qty,0); ?></th>

						<th width="100" align="right" id="value_total_cm_pcs_td"><? echo number_format($total_cm_pcs_value,2);?></th>
						<th width="100" align="right" id="value_total_cm_pcs_lc_td"><? echo number_format($total_cm_pcs_value,2);?></th>
						<th width="100" align="right" id="value_total_fob_td"><? echo number_format($total_fob_value,2);?></th>
                        <th width="70">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th>&nbsp;</th>
                    </tr>
                </tfoot>
            </table>

        </div>

		<?

	}

	else if($reportType==7) // details2 button
	{

		$com_export_sql_result=sql_select("SELECT a.id,a.buyer_id,a.invoice_no,a.shipping_mode,a.lc_sc_id,a.is_lc,b.po_breakdown_id,b.current_invoice_value from com_export_invoice_ship_mst a, com_export_invoice_ship_dtls b where a.id=b.mst_id");
		foreach($com_export_sql_result as $row)
		{
			$invoice_array[$row[csf('id')]]=$row[csf('invoice_no')];
			$shipping_mode_array[$row[csf('id')]]=$row[csf('shipping_mode')];
			$lc_sc_id_array[$row[csf('id')]]=$row[csf('lc_sc_id')];
			$lc_sc_type_arr[$row[csf('id')]]=$row[csf('is_lc')];

			$buyer_invoice_value_arr[$row[csf('buyer_id')]]+=$row[csf('current_invoice_value')];
			$po_invoice_value_arr[$row[csf('po_breakdown_id')]]+=$row[csf('current_invoice_value')];

		}



		//$tot_cost_arr = return_library_array("select job_no, cm_cost from wo_pre_cost_dtls","job_no","cm_cost");
		$com_export_sql_result=sql_select("SELECT (a.total_set_qnty*a.job_quantity) as job_qty,a.total_set_qnty,a.job_quantity,b.job_no,b.cm_cost,b.margin_pcs_set,b.total_cost from wo_pre_cost_dtls b, wo_po_details_master a where b.job_no=a.job_no");
		foreach($com_export_sql_result as $row)
		{
			$tot_cost_arr[$row[csf('job_no')]]=$row[csf('cm_cost')];
			$job_qty_arr[$row[csf('job_no')]]=$row[csf('job_qty')];
			$job_margin_arr[$row[csf('job_no')]]=$row[csf('margin_pcs_set')]*$row[csf('total_set_qnty')];
		}


		$inspection_date_arr=return_library_array( "SELECT po_break_down_id, max(inspection_date) as inspection_date from pro_buyer_inspection where status_active=1 and is_deleted=0 group by po_break_down_id", "po_break_down_id", "inspection_date");
		$lc_num_arr=return_library_array( "SELECT id,export_lc_no from com_export_lc", "id", "export_lc_no"  );
		$sc_num_arr=return_library_array( "SELECT id,contract_no from com_sales_contract", "id", "contract_no"  );






		if($source_cond)
		$source_cond2=str_replace("d.","a.",$source_cond);
		$challan_mst_arr=array();
		$challan_sql="SELECT a.id,b.delivery_mst_id, a.sys_number_prefix_num,a.sys_number, a.forwarder, a.truck_no, a.driver_name, a.mobile_no, a.dl_no, b.po_break_down_id,b.item_number_id,a.delivery_floor_id,
		(CASE WHEN a.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty,
		b.total_carton_qnty as carton_qnty
		from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b
		where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $source_cond2";//  and b.po_break_down_id = 41247
		// echo $challan_sql;die();
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
			$exfact_qty_arr_without_current[$row[csf("delivery_mst_id")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]]["ex_fact"]+=$row[csf("ex_factory_qnty")]-$row[csf("ex_factory_return_qnty")];
			$exfact_qty_arr_without_current[$row[csf("delivery_mst_id")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]]["carton"]+=$row[csf("carton_qnty")];
		}
		// print_r($exfact_qty_arr_without_current);die();
		$details_report .='<table width="4405" cellspacing="0" cellpadding="0"  border="1"  class="rpt_table" rules="all" id="table_body2">';
		$i=1;

		if($db_type==0)
		{
			$sql= "SELECT b.id as po_id, max(a.lc_sc_no) as lc_sc_arr_no,
			group_concat(distinct a.invoice_no) as invoice_no,
			group_concat(distinct a.item_number_id) as itm_num_id,
			group_concat(distinct a.foc_or_claim) as foc_or_claim,
			sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
			sum(a.total_carton_qnty) as carton_qnty, max(a.ex_factory_date)  as ex_factory_date,
			group_concat(distinct  a.lc_sc_no) as lc_sc_no,
			max(a.shiping_mode) as shiping_mode,
			a.delivery_mst_id as challan_id,
			d.delivery_floor_id as del_floor,  b.shipment_date, b.po_number,b.po_quantity as po_quantity, (b.unit_price/c.total_set_qnty) as unit_price, b.shiping_status, c.id, c.company_name, c.buyer_name, c.job_no_prefix_num,c.job_no,c.ship_mode , YEAR(c.insert_date) as year, c.style_ref_no, c.style_description, c.set_smv, d.delivery_company_id as del_company,d.source,d.delivery_location_id as del_location ,c.total_set_qnty,a.item_number_id,c.set_break_down,c.order_uom
			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c ,pro_ex_factory_delivery_mst d
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no $company_cond $str_cond $del_comp_cond $del_location_cond $del_floor_cond $shiping_status_cond $source_cond $buyer_conds $internal_ref_cond $search_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active  in(1,2,3) and c.is_deleted=0 and c.status_active  in(1,2,3) and a.delivery_mst_id=d.id
			group by
					b.id , a.delivery_mst_id,d.delivery_floor_id,b.shipment_date, b.po_number,b.po_quantity,c.total_set_qnty,b.unit_price,b.shiping_status, c.id, c.company_name, c.buyer_name, c.job_no_prefix_num,c.job_no, c.ship_mode ,c.insert_date, c.style_ref_no, c.style_description, c.set_smv, d.delivery_company_id, d.delivery_location_id ,d.source,a.item_number_id ,c.set_break_down,c.order_uom
			order by c.buyer_name, b.shipment_date ASC";
		}
		else if($db_type==2)
		{
		 	$sql= "SELECT b.id as po_id,max(a.lc_sc_no) as lc_sc_arr_no,
			LISTAGG(CAST( a.invoice_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.invoice_no) as invoice_no,
			LISTAGG(CAST( a.foc_or_claim AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.foc_or_claim) as foc_or_claim,
			LISTAGG(CAST( a.item_number_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.item_number_id) as itm_num_id,
			sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
			sum(a.total_carton_qnty) as carton_qnty, max(a.ex_factory_date) as ex_factory_date,
			LISTAGG(CAST( a.lc_sc_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.lc_sc_no) as lc_sc_no, max(a.shiping_mode) as shiping_mode,
			a.delivery_mst_id as challan_id,d.delivery_floor_id as del_floor,b.shipment_date, b.po_number,b.po_quantity as po_quantity,(b.unit_price/c.total_set_qnty) as unit_price,b.shiping_status, c.id, c.company_name, c.buyer_name, c.job_no_prefix_num,c.job_no,c.ship_mode ,to_char(c.insert_date,'YYYY') as year, c.style_ref_no, c.style_description, c.set_smv, d.delivery_company_id as del_company,d.source, d.delivery_location_id as del_location,c.total_set_qnty,a.item_number_id,c.set_break_down,c.order_uom
			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c ,pro_ex_factory_delivery_mst d
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no $company_cond $str_cond $del_comp_cond $del_location_cond $del_floor_cond $shiping_status_cond $source_cond $buyer_conds $internal_ref_cond $search_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active  in(1,2,3) and c.is_deleted=0 and c.status_active  in(1,2,3) and a.delivery_mst_id=d.id
			group by
					b.id , a.delivery_mst_id,d.delivery_floor_id,b.shipment_date, b.po_number,b.po_quantity,c.total_set_qnty,b.unit_price,b.shiping_status, c.id, c.company_name, c.buyer_name, c.job_no_prefix_num,c.job_no,c.ship_mode , c.insert_date, c.style_ref_no, c.style_description, c.set_smv, d.delivery_company_id, d.source,d.delivery_location_id ,a.item_number_id ,c.set_break_down,c.order_uom
			order by c.buyer_name, b.shipment_date ASC";
		}
		// echo $sql;die();
		$sql_result=sql_select($sql);
		foreach($sql_result as $k=>$v)
		{
			$all_job_arr[trim($v[csf("job_no")])]=trim($v[csf("job_no")]);

		}
		$all_job="'".implode("','", array_unique($all_job_arr))."'";
		$order_item_qnty_sql="SELECT po_break_down_id,item_number_id,sum(order_quantity) as order_quantity from wo_po_color_size_breakdown where status_active  in(1,2,3) and is_deleted=0 and job_no_mst in($all_job) group by po_break_down_id,item_number_id";
		foreach(sql_select($order_item_qnty_sql) as $key=>$val)
		{
			$order_item_qnty_arr[$val[csf("po_break_down_id")]][$val[csf("item_number_id")]]=$val[csf("order_quantity")];
			$poArr[$val[csf("po_break_down_id")]]=$val[csf("po_break_down_id")];
		}


		//for totoal actual cost............................................start;
		/*$condition= new condition();

		if($cbo_company_name>0){
			$condition->company_name("=$cbo_company_name");
		}
		if(str_replace("'","",$cbo_buyer_name)>0){
			$condition->buyer_name("=$cbo_buyer_name");
		}
		if(count($poArr)>0)
		{
			$condition->po_id_in(implode(',',$poArr));
		}



		$condition->init();
		$fabric= new fabric($condition);
		$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
		$yarn= new yarn($condition);
		$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
		$conversion= new conversion($condition);
		$conversion_costing_arr_process=$conversion->getAmountArray_by_orderAndProcess();
		// print_r($conversion_costing_arr_process);
		$trims= new trims($condition);
		$trims_costing_arr=$trims->getAmountArray_by_order();
		$emblishment= new emblishment($condition);
		$emblishment_costing_arr_name=$emblishment->getAmountArray_by_orderAndEmbname();
		$commission= new commision($condition);
		$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();
		$commercial= new commercial($condition);
		$commercial_costing_arr=$commercial->getAmountArray_by_order();
		$other= new other($condition);
		$other_costing_arr=$other->getAmountArray_by_order();
		$wash= new wash($condition);
		$emblishment_costing_arr_name_wash=$wash->getAmountArray_by_orderAndEmbname();

		//for total actual cost ............................................end;


		$tot_yarn_cost_actual=$yarnTrimsCostArray[$po_id][1];



		$tot_actual_all_cost=$tot_yarn_cost_actual+$tot_knit_cost_actual+$tot_dye_finish_cost_actual+$tot_yarn_dye_cost_actual+$tot_aop_cost_actual+$tot_trims_cost_actual+$tot_embell_cost_actual+$tot_wash_cost_actual+$tot_commission_cost_actual+$tot_comm_cost_actual+$tot_freight_cost_actual+$tot_test_cost_actual+$tot_inspection_cost_actual+$tot_currier_cost_actual+$tot_cm_cost_actual+$tot_fabric_purchase_cost_actual;*/










		$gr_po_qnty_pcs=0;
		$gr_po_qnty_val=0;
		$gr_po_qnty_val_perc=0;
		$gr_ttl_ex_qnty=0;
		$gr_ttl_ex_qnty_val=0;
		$gr_sales_min=0;
		$gr_ttl_carton=0;
		$gr_ttl_basic_qty=0;
		$gr_ttl_ex_fac_per=0;
		$gr_ttl_short_qty=0;
		$gr_ttl_short_val=0;
		$gr_ttl_sales_cm=0;

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
			$total_exface_qnty=$txt_date_from."*".$txt_date_to."_". 1;
			if($break_id==0) $break_id=$row[csf('po_id')]; else $break_id=$break_id.",".$row[csf('po_id')];
			if($sc_lc_id==0) $sc_lc_id=$row[csf('lc_sc_no')]; else $sc_lc_id=$sc_lc_id.",".$row[csf('lc_sc_no')];

			$invoce_id_arr=array_unique(explode(",",$row[csf('invoice_no')]));
		    $challan_id=array_unique(explode(",",$row[csf("challan_id")]));
 			$floor_id=array_unique(explode(",",$row[csf("del_floor")]));
			$challan_no=""; $forwarder=""; $vehi_no=""; $dirver_info="";  $floor_no="";

			$todate=date("d-M")."-".substr(date("Y"), 2) ;
 			$todate=explode("-", $todate);
 			$todate=$todate[0]."-".strtoupper($todate[1])."-".$todate[2];
 			$diff=datediff("d",$todate, $row[csf("shipment_date")])-2;
		    $diff_color=($diff>0) ?" color:black;":"color:red;";


			foreach($challan_id as $val)
			{
				if($challan_no=="") $challan_no=$challan_mst_arr[$val][$row[csf('po_id')]]['challan']; else $challan_no.=','.$challan_mst_arr[$val][$row[csf('po_id')]]['challan'];
				if($floor_no=="") $floor_no=$floor_library[$challan_mst_arr[$val][$row[csf('po_id')]]['floor']]; else $floor_no.=','.$floor_library[$challan_mst_arr[$val][$row[csf('po_id')]]['floor']];

				if($forwarder=="") $forwarder=$supp_library[$challan_mst_arr[$val][$row[csf('po_id')]]['forwarder']]; else $forwarder.=','.$supp_library[$challan_mst_arr[$val][$row[csf('po_id')]]['forwarder']];
				if($vehi_no=="") $vehi_no=$challan_mst_arr[$val][$row[csf('po_id')]]['truck_no']; else $vehi_no.=','.$challan_mst_arr[$val][$row[csf('po_id')]]['truck_no'];
				if($dirver_info=="") $dirver_info="Name: ".$challan_mst_arr[$val][$row[csf('po_id')]]['driver_name']."<br>Mob No: ".$challan_mst_arr[$val][$row[csf('po_id')]]['mobile_no']."<br>DL No: ".$challan_mst_arr[$val][$row[csf('po_id')]]['dl_no']; else $dirver_info.=','."Name: ".$challan_mst_arr[$val][$row[csf('po_id')]]['driver_name']."<br>Mob No: ".$challan_mst_arr[$val][$row[csf('po_id')]]['mobile_no']."<br>DL No: ".$challan_mst_arr[$val][$row[csf('po_id')]]['dl_no'];
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

			$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";


			$comapny_id=$row[csf("company_name")];
			$delv_comp=($row[csf("source")]!=3)?  $company_library[$row[csf("del_company")]] : $supp_library[$row[csf("del_company")]];

			$onclick=" change_color('tr_".$i."','".$bgcolor."')";
			$details_report .='<tr bgcolor="'.$bgcolor.'" onclick="'.$onclick.'" id="tr_'.$i.'">';
			$details_report .='<td width="40" align="center">'.$i.'</td>
								<td width="150" align="center" ><p>'.$company_library[$row[csf("company_name")]].'</p></td>
								<td width="60" align="center" ><p>'.$row[csf("job_no_prefix_num")].'</p></td>
								<td width="60" align="center" ><p>'.$row[csf("year")].'</p></td>
								<td width="100" align="center" ><p>'.$buyer_arr[$row[csf("buyer_name")]].'</p></td>
								<td width="110" align="center"><p>'.$row[csf("po_number")].'</p></td>
								<td width="125" align="center" ><p>'.$delv_comp.'</p></td>
								<td width="125" align="center" ><p>'.$location_library[$row[csf("del_location")]].'</p></td>
								<td width="125" align="center" ><p>'.$floor_no.'</p></td>
								<td width="120" align="center"><p>'.$challan_no.'</p></td>
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
								<td width="100"><p>'.$row[csf("style_ref_no")].'</p></td>
								<td width="100"><p>'.$row[csf("style_description")].'</p></td>
								<td width="110" align="center"><p>';
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

								$total_ex_fact_qty=$exfact_qty_arr_without_current[$row[csf("challan_id")]][$row[csf("po_id")]][$row[csf("item_number_id")]]["ex_fact"] ;

								$total_cartoon_qty=$exfact_qty_arr_without_current[$row[csf("challan_id")]][$row[csf("po_id")]][$row[csf("item_number_id")]]["carton"] ;
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

								$temp_focclaim_arr=array();
								foreach(explode(',',$row[csf("foc_or_claim")]) as $foc_id){
									$temp_focclaim_arr[$foc_id]=$foc_claim_arr[$foc_id];
								}
								$row[csf("foc_or_claim")]=implode(',',$temp_focclaim_arr);

			$details_report .=$item_name_all.'</p></td>
								<td width="80" align="center"><p>'.$item_smv.'</p></td>
								<td width="70" align="center"><p>'.change_date_format($row[csf("shipment_date")]).'</p></td>
								<td width="70" align="center"><a href="##" onclick="openmypage_ex_date('.$comapny_id.",'".$row[csf('po_id')]."','".$row[csf('item_number_id')]."','".$ex_fact_date_range."','ex_date_popup','".$row[csf('challan_id')]."'".",'1'".')">'.change_date_format($row[csf('ex_factory_date')]).'</a></td>
								<td width="100" align="center"><p>'.$shipment_mode[$row[csf('ship_mode')]].'</p></td>
								<td width="70" align="center"><p>'.$shipment_mode[$row[csf("shiping_mode")]].'</p></td>
								<td width="100" align="center"><p>'.$row[csf("foc_or_claim")].'</p></td>

								<td width="60" align="center" style="'.$diff_color.'"><p>('.$diff.')</p></td>
								<td width="100" align="center"><p>'.$unit_of_measurement[$row[csf('order_uom')]].'</p></td>
								<td width="80" align="right"><p>'. number_format($po_quantity,0,'', '').'</p></td>
								<td width="70" align="right"><p>'. number_format($unit_price,4).'</p></td>
								<td width="100" align="right"><p>'. number_format(($po_quantity*$unit_price),2).'</p></td>
								<td width="80" align="right"><p><a href="##" onclick="openmypage_ex_date('.$comapny_id.",'".$row[csf('po_id')]."','".$row[csf('item_number_id')]."','".$ex_fact_date_range."','ex_date_popup','".$row[csf('challan_id')]."'".",'1'".')">'. number_format($current_ex_Fact_Qty,0,'.', '').'</a></p></td>
								<td width="100" align="right"><p>'. number_format( $current_ex_fact_value=$current_ex_Fact_Qty*$unit_price,2).'</p></td>
								<td width="80" align="right"><p>'. number_format($row[csf("carton_qnty")],0,'.', '').'</p></td>
								<td width="80" align="right"><p><a href="##" onclick="openmypage_ex_date('.$comapny_id.",'".$row[csf('po_id')]."','".$row[csf('item_number_id')]."','".$total_exface_qnty."','ex_date_popup','".$row[csf('challan_id')]."'".",'1'".')">'.number_format($total_ex_fact_qty,0,'.', '').'</a></p></td>
								<td width="100" align="right"><p>'. number_format($total_ex_fact_value=$total_ex_fact_qty*$unit_price,2).'</p></td>


								<td width="100" align="right"><p>'.number_format(($job_margin_arr[$row[csf('job_no')]]*$po_quantity),2).'</p></td>
								<td width="100" align="right"><p>'.number_format((($po_quantity*$unit_price)-array_sum($tot_cost_arr[$row[csf('job_no')]]))/$po_quantity*$total_ex_fact_qty,2).'</p></td>
								<td width="100" align="right"><p>'.number_format(($po_quantity*$unit_price),2).'</p></td>
								<td width="100" align="right"><p>'.number_format($po_invoice_value_arr[$row[csf('po_id')]],2).'</p></td>



								<td width="80" align="right"><p>'.number_format($total_cartoon_qty,0,'.', '').'</p></td>
								<td width="100" align="right" title="Total Ex.Qty*SMV"><p>'. number_format($total_sales_minutes=$total_ex_fact_qty*$item_smv).'</p></td>
								<td width="80" align="right"><p>'.number_format($basic_qnty,0,'','').'</p></td>
								<td width="80" align="right" style="'.$excess_msg.'" ><p>'. number_format($excess_shortage_qty=$total_ex_fact_qty-$po_quantity,0,'', '').'</p></td>
								<td width="100" align="right" style="'.$excess_val_msg.'"><p>'. number_format($excess_shortage_value=($excess_shortage_qty*$unit_price),2).'</p></td>
								<td align="center" style="'.$ttl_ex_qty_msg.'" width="80"><p>'. number_format($total_ex_fact_qty_parcent=($total_ex_fact_qty/$po_quantity)*100,0).'</p></td>
								<td width="60" align="center" title="CM per pcs: '.number_format($cm_per_pcs,4).'"><p>'.number_format($cm_per_pcs*$total_ex_fact_qty,2).'</p></td>
								<td width="100" align="center"><p>'.$forwarder.'</p></td>
								<td width="80" align="center"><p>'.$vehi_no.'</p></td>
								<td width="130"><p>'.$dirver_info.'</p></td>
								<td width="70" align="center"><p>'.(change_date_format($inspection_date_arr[$row[csf('po_id')]]) == '0000-00-00' || change_date_format($inspection_date_arr[$row[csf('po_id')]]) == '' ? '' : change_date_format(change_date_format($inspection_date_arr[$row[csf('po_id')]]))).'</p></td>
								<td align="center"><p>'.$shipment_status[$row[csf('shiping_status')]].'</p></td>
							</tr>';



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
			$g_sales_minutes+=$total_sales_minutes;
			// $g_sales_minutes+=$current_ex_Fact_Qty*$item_smv;
			$total_eecess_storage_qty+=$excess_shortage_qty;
			$total_eecess_storage_val+=$excess_shortage_value;
			if($po_check_arr[$row[csf("challan_id")]][$row[csf("po_id")]][$row[csf("item_number_id")]]=="")
			{
				$po_check_arr[$row[csf("challan_id")]][$row[csf("po_id")]][$row[csf("item_number_id")]]=$row[csf("po_id")];
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

			$i++;
			$item_name_all="";


			$pre_costing_margin_arr[$row[csf("buyer_name")]][$row[csf('job_no')]]=$job_margin_arr[$row[csf('job_no')]]*$job_qty_arr[$row[csf('job_no')]];
			$pre_costing_cm_arr[$row[csf("buyer_name")]][$row[csf('job_no')]]=$tot_cost_arr[$row[csf('job_no')]];




		}
		$pp=$i;

		foreach($master_data as $rows)
		{
			$total_po_val+=$rows["po_value"];
		}

		?>
        <div style="width:3100x;">
            <div style="width:1820px" >
                <table width="1790"  cellspacing="0"  align="center">
                    <tr>
                        <td align="center" colspan="17" class="form_caption">
                            <strong style="font-size:16px;">Company Name:<? echo  $company_library[$cbo_company_name] ;?></strong>
                        </td>
                    </tr>
                    <tr class="form_caption">
                        <td colspan="17" align="center" class="form_caption"> <strong style="font-size:15px;">Ex-Factory Report</strong></td>
                    </tr>
                     <tr align="center">
                        <td colspan="17" align="center" class="form_caption"> <strong style="font-size:15px;">Total Summary</strong></td>
                    </tr>
              </table>
              <table width="1790" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1">
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
                        <th width="100">Total Ex-Fact. Value %</th>
                        <th width="100">Pre-Costing Margin</th>
                        <th width="100">Actual Margin</th>
                        <th width="100">Pre-Costing FOB Value</th>
                        <th>Commercial Invoice FOB Value</th>
                    </thead>
                 </table>
                 <table width="1790" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body">
                 <?
                 $m=1;
                 $grand_sales_minute =0;
                foreach($master_data as $rows)
                {
                   	$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
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

                        <td align="right" width="100">
                            <p>
                                <?
                                    $total_ex_fact_value_parcentage=($total_ex_fact_value/$buyer_po_value)*100;
                                    echo number_format($total_ex_fact_value_parcentage,0);
                                ?> %
                            </p>
                        </td>
                        <td align="right" width="100"><? $pre_costing=array_sum($pre_costing_margin_arr[$rows[b_id]]);echo number_format($pre_costing,2);?></td>
                        <td align="right" width="100"><? echo number_format(($buyer_po_value-array_sum($pre_costing_cm_arr[$rows[b_id]]))/$po_quantity*$total_ex_fact_qty,2);?></td>
                        <td align="right" width="100"><? echo number_format($buyer_po_value,2);?></td>
                        <td align="right"><? echo number_format($buyer_invoice_value_arr[$rows[b_id]],2);?></td>
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
                        <th align="right"></th>
                        <th align="right"></th>
                        <th align="right"></th>
                        <th align="right"></th>
                    </tfoot>
                </table>
            </div>
            <br />
            <div>
                <table width="4405"  >
                    <tr>
                    <td colspan="33" class="form_caption"><strong style="font-size:16px;">Shipped Out Order Details Report</strong></td>
                    </tr>
                </table>
                <table width="4405" border="1" class="rpt_table" rules="all" id="table_header_2">
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
                        <th width="100">FOC/Claim</th>
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

                        <th width="100">Pre-Costing Margin</th>
                        <th width="100" title="((Buyer Total FOB Value - Buyer Total  Raw Material Booking Value)/Buyer Total Order Qty*Buyer Total Ex-Factory Qty.)">Actual Margin</th>
                        <th width="100">Pre-Costing FOB Value</th>
                        <th width="100">Commercial Invoice FOB Value</th>

                        <th width="80">Total Carton Qty</th>
                        <th width="100">Sales Minute</th>
                        <th width="80">Total Ex-Fact. (Basic Qty)</th>
                        <th width="80">Excess/ Shortage Qty</th>
                        <th width="100">Excess/ Shortage Value</th>
                        <th width="80">Total Ex-Fact. Qty. %</th>
                        <th width="60">Sales CM</th>
                        <th width="100">C & F Name</th>
                        <th width="80">Vehicle No</th>
                        <th width="130">Driver Info</th>
                        <th width="70">Inspection Date</th>
                        <th>Ex-Fact Status</th>
                    </thead>
                </table>
            <div style="width:4405px; overflow-y:scroll; overflow-x:hidden; max-height:300px;"  id="scroll_body" >
            <?

            	$details_report .='</table>';
				echo $details_report;
            ?>




            <table width="4405" cellspacing="0" cellpadding="0"  border="1"  class="rpt_table" rules="all" id="table_footer">
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
                        <th width="100">&nbsp;</th>
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


                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>





                        <th width="80" align="right" id="g_total_ex_crtnbk"><? echo number_format($gr_ttl_carton_qt,0);?></th>
                        <th width="100" align="right" id="value_sales_minutesbk"><? echo number_format($gr_sales_min);?></th>

                        <th width="80" align="right" id="total_basic_qtybk"><? echo number_format($gr_ttl_basic_qty,0); ?></th>
                        <th width="80" align="right" id="total_eecess_storage_qtybk"><? echo number_format($gr_ttl_short_qty,0);?></th>
                        <th width="100" align="right" id="value_total_eecess_storage_valbk"><? echo number_format($gr_ttl_short_val,0);?></th>
                        <th width="80" id="total_ex_perbk"><? echo number_format($gr_ttl_ex_fac_per,0);?></th>
                        <th width="60" align="right" id="value_cm_per_pcs_totbk"><? echo number_format($gr_ttl_sales_cm,2);?></th>
                        <th width="100">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="130">&nbsp;</th>
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
	else if($reportType==8)
	{


		$filter = '';
		if($cbo_buyer_name != 0){
			$filter .= " and a.buyer_name='$cbo_buyer_name'";
		}else{
			$filter .= " ";
		}
		$com_export_sql_result=sql_select("SELECT a.id,a.buyer_id,a.invoice_no,a.shipping_mode,a.lc_sc_id,a.is_lc,b.po_breakdown_id,b.current_invoice_value
			from com_export_invoice_ship_mst a,
				com_export_invoice_ship_dtls b
			where a.id=b.mst_id and a.buyer_id='$cbo_buyer_name' and a.ex_factory_date between '$txt_date_from' and '$txt_date_to' ");

		foreach($com_export_sql_result as $row)
		{
			$invoice_array[$row[csf('id')]]=$row[csf('invoice_no')];
			$shipping_mode_array[$row[csf('id')]]=$row[csf('shipping_mode')];
			$lc_sc_id_array[$row[csf('id')]]=$row[csf('lc_sc_id')];
			$lc_sc_type_arr[$row[csf('id')]]=$row[csf('is_lc')];

			$buyer_invoice_value_arr[$row[csf('buyer_id')]]+=$row[csf('current_invoice_value')];
			$po_invoice_value_arr[$row[csf('po_breakdown_id')]]+=$row[csf('current_invoice_value')];

		}
		//echo "Hello"; exit();
		$com_export_cost_sql_result=sql_select("SELECT (a.total_set_qnty*a.job_quantity) as job_qty,a.total_set_qnty,a.job_quantity,b.job_no,b.cm_cost,b.margin_pcs_set,b.total_cost
		from wo_pre_cost_dtls b, wo_po_details_master a
		where b.job_id=a.id $filter");

		//echo $com_export_cost_sql_result; exit();
		foreach($com_export_cost_sql_result as $row)
		{
			$tot_cost_arr[$row[csf('job_no')]]=$row[csf('cm_cost')];
			$job_qty_arr[$row[csf('job_no')]]=$row[csf('job_qty')];
			$job_margin_arr[$row[csf('job_no')]]=$row[csf('margin_pcs_set')]*$row[csf('total_set_qnty')];
		}


		$inspection_date_arr=return_library_array( "select po_break_down_id, max(inspection_date) as inspection_date from pro_buyer_inspection where status_active=1 and is_deleted=0 group by po_break_down_id", "po_break_down_id", "inspection_date");
		$lc_num_arr=return_library_array( "select id,export_lc_no from com_export_lc", "id", "export_lc_no"  );
		$sc_num_arr=return_library_array( "select id,contract_no from com_sales_contract", "id", "contract_no"  );

		if($source_cond)
		$source_cond2=str_replace("d.","a.",$source_cond);
		$challan_mst_arr=array();
		$challan_sql="SELECT a.id,b.delivery_mst_id, a.sys_number_prefix_num,a.sys_number, a.forwarder, a.truck_no, a.driver_name, a.mobile_no, a.dl_no, b.po_break_down_id,b.item_number_id,a.delivery_floor_id,
		(CASE WHEN a.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty,
		b.total_carton_qnty as carton_qnty
		from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b
		where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $source_cond2";

		//echo $challan_sql; exit();

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
		$details_report .='<table width="2340" cellspacing="0" cellpadding="0"  border="1"  class="rpt_table" rules="all" id="table_body2">';
		$i=1;

		if($db_type==0)
		{
			$sql= "SELECT b.id as po_id,
			group_concat(distinct a.item_number_id) as itm_num_id,
			sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
			sum(a.total_carton_qnty) as carton_qnty, max(a.ex_factory_date)  as ex_factory_date,
			d.delivery_floor_id as del_floor,  b.shipment_date, b.po_number,b.po_quantity as po_quantity, (b.unit_price/c.total_set_qnty) as unit_price, b.shiping_status,b.file_no,b.grouping, c.id, c.company_name, c.buyer_name, c.job_no_prefix_num,c.job_no,c.ship_mode , YEAR(c.insert_date) as year, c.style_ref_no, c.style_description, c.set_smv, d.delivery_company_id as del_company,d.source,d.delivery_location_id as del_location ,c.total_set_qnty,a.item_number_id,c.set_break_down,c.order_uom
			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c ,pro_ex_factory_delivery_mst d
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no $company_cond $str_cond $del_comp_cond $del_location_cond $del_floor_cond $shiping_status_cond $source_cond $buyer_conds $internal_ref_cond $search_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active  in(1,2,3) and c.is_deleted=0 and c.status_active  in(1,2,3) and a.delivery_mst_id=d.id
			group by
					b.id , a.delivery_mst_id,d.delivery_floor_id,b.shipment_date, b.po_number,b.po_quantity,b.file_no,b.grouping,c.total_set_qnty,b.unit_price,b.shiping_status, c.id, c.company_name, c.buyer_name, c.job_no_prefix_num,c.job_no, c.ship_mode ,c.insert_date, c.style_ref_no, c.style_description, c.set_smv, d.delivery_company_id, d.delivery_location_id ,d.source,a.item_number_id ,c.set_break_down,c.order_uom
			order by c.buyer_name, b.shipment_date ASC";
			//max(a.lc_sc_no) as lc_sc_arr_no,	group_concat(distinct a.invoice_no) as invoice_no,group_concat(distinct a.foc_or_claim) as foc_or_claim,group_concat(distinct  a.lc_sc_no) as lc_sc_no,	max(a.shiping_mode) as shiping_mode,	a.delivery_mst_id as challan_id,
		}
		else if($db_type==2)
		{
		 	$sql= "SELECT b.id as po_id,
			LISTAGG(CAST( a.item_number_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.item_number_id) as itm_num_id,
			sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,max(a.ex_factory_date) as ex_factory_date,b.shipment_date, b.po_number,b.po_quantity as po_quantity,(b.unit_price/c.total_set_qnty) as unit_price,b.shiping_status,b.file_no,b.grouping, c.id, c.company_name, c.buyer_name, c.job_no_prefix_num,c.job_no,c.ship_mode ,to_char(c.insert_date,'YYYY') as year, c.style_ref_no, c.style_description, c.set_smv,d.source, c.total_set_qnty,a.item_number_id,c.set_break_down,c.order_uom,e.job_no,e.sew_effi_percent
			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c ,pro_ex_factory_delivery_mst d,wo_pre_cost_mst e
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.job_no=e.job_no $company_cond $str_cond $del_comp_cond $del_location_cond $del_floor_cond $shiping_status_cond $source_cond $buyer_conds $internal_ref_cond $search_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active  in(1,2,3) and c.is_deleted=0 and c.status_active  in(1,2,3) and a.delivery_mst_id=d.id
			group by
					b.id , b.shipment_date, b.po_number,b.po_quantity,b.file_no,b.grouping,c.total_set_qnty,b.unit_price,b.shiping_status, c.id, c.company_name, c.buyer_name, c.job_no_prefix_num,c.job_no,c.ship_mode , c.insert_date, c.style_ref_no, c.style_description, c.set_smv, d.source,a.item_number_id ,c.set_break_down,c.order_uom,e.job_no,e.sew_effi_percent
			order by c.buyer_name, b.shipment_date ASC";
			//max(a.lc_sc_no) as lc_sc_arr_no,			LISTAGG(CAST( a.invoice_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.invoice_no) as invoice_no,			LISTAGG(CAST( a.foc_or_claim AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.foc_or_claim) as foc_or_claim,			LISTAGG(CAST( a.lc_sc_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.lc_sc_no) as lc_sc_no, max(a.shiping_mode) as shiping_mode,a.delivery_mst_id as challan_id,d.delivery_floor_id as del_floor,sum(a.total_carton_qnty) as carton_qnty,
		}
		//echo $sql;die;
		$sql_result=sql_select($sql);
		foreach($sql_result as $k=>$v)
		{
			$all_job_arr[trim($v[csf("job_no")])]=trim($v[csf("job_no")]);

		}

		$count_all_job_arr = count($all_job_arr);
		$all_job="'".implode("','", array_unique($all_job_arr))."'";
		if($db_type==2 && $count_all_job_arr>1000)
		{
			$job_cond=" and (";
			$outIdsArr=array_chunk($all_job_arr,990);
			foreach($outIdsArr as $ids)
			{
				$ids="'".implode("','", array_unique($ids))."'";
				$job_cond.=" job_no_mst in($ids) or ";
			}
			$job_cond=chop($job_cond,'or ');
			$job_cond.=")";
		}
		else
		{
			$job_cond=" and  job_no_mst in($all_job)";
		}
		$order_item_qnty_sql="SELECT job_no_mst, po_break_down_id,item_number_id,sum(order_quantity) as order_quantity, sum(order_total) as order_total from wo_po_color_size_breakdown where status_active  in(1,2,3) and is_deleted=0 $job_cond group by job_no_mst, po_break_down_id,item_number_id";
		$job_order_item_qnty_arr = array();
		$job_order_total_arr = array();

		foreach(sql_select($order_item_qnty_sql) as $key=>$val)
		{
			$job_order_item_qnty_arr[$val[csf("job_no_mst")]] 	+= $val[csf("order_quantity")];
			$job_order_total_arr[$val[csf("job_no_mst")]] 		+= $val[csf("order_total")];
			$order_item_qnty_arr[$val[csf("po_break_down_id")]][$val[csf("item_number_id")]]=$val[csf("order_quantity")];
			$poArr[$val[csf("po_break_down_id")]]=$val[csf("po_break_down_id")];
		}


		//START calculating Pre-Costing Margin = job_value(po_value * count(po)) - job_expenses
		$allPoStr="'".implode("','", array_unique($poArr))."'";

		$condition= new condition();
		if(!empty($all_job)){
			$condition->job_no("in($all_job)");
		}
		if(!empty($allPoStr)){
			$condition->po_id("in($allPoStr)");
		}

		$condition->init();
		$fabric			= new fabric($condition);
		$yarn			= new yarn($condition);
		$conversion		= new conversion($condition);
		$trim			= new trims($condition);
		$emblishment	= new emblishment($condition);
		$wash			= new wash($condition);
		$other			= new other($condition);
		$other_cost		= $other->getAmountArray_by_job();
		$commercial		= new commercial($condition);
		$commision		= new commision($condition);

		$fabric_costing_arr				= $fabric->getAmountArray_by_job_knitAndwoven_greyAndfinish();
		//echo "<pre>"; print_r($fabric_costing_arr2['knit']['grey'][$job_no]);
		$trims_costing_arr				= $trim->getAmountArray_by_job();
		$yarn_costing_arr				= $yarn->getJobWiseYarnAmountArray();
		$conversion_costing_arr			= $conversion->getAmountArray_by_job();

		//$ttl_trims_cost=$trims_costing_arr[$job_no];
		$emblishment_costing_arr		= $emblishment->getAmountArray_by_job();
		$emblishment_costing_arr_wash	= $wash->getAmountArray_by_job();
		//$ttl_emblishment_cost=$emblishment_costing_arr[$job_no]+$emblishment_costing_arr_wash[$job_no];
		$commercial_costing_arr			= $commercial->getAmountArray_by_job();
		$commission_costing_arr			= $commision->getAmountArray_by_job();
		//$ttl_commercial_cost=$commercial_costing_arr[$job_no];
		$other_costing_arr				= $other->getAmountArray_by_job();
		$ttl_cm_cost					= $other_costing_arr[$job_no]['cm_cost'];

		$sql_dtls = "SELECT job_no, fabric_cost, fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent, wash_cost, wash_cost_percent, comm_cost, comm_cost_percent, commission, commission_percent, lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent, common_oh, common_oh_percent, depr_amor_pre_cost, total_cost, total_cost_percent, price_dzn, price_dzn_percent, margin_dzn, margin_dzn_percent, price_pcs_or_set, price_pcs_or_set_percent, margin_pcs_set, margin_pcs_set_percent, cm_for_sipment_sche, margin_pcs_bom
		from wo_pre_cost_dtls where job_no in ($all_job) and status_active=1 and is_deleted=0";

		//echo $sql_dtls; exit();

		$data_array_new=sql_select($sql_dtls);
		$marginPerPieceJob = array();
		$summary_data=array();
		foreach($data_array_new as $row_new ){
			$marginPerPieceJob[$row_new[csf("job_no")]] = $row_new[csf("margin_pcs_bom")];
			$summary_data[$row_new[csf("job_no")]]["price_dzn"]=$row_new[csf("price_dzn")];
			$summary_data[$row_new[csf("job_no")]]["price_dzn_job"]=($po_qty/($total_set_qnty*$order_price_per_dzn))*$row_new[csf("price_dzn")];
			$summary_data[$row_new[csf("job_no")]]["commission"]=$row_new[csf("commission")];
			$summary_data[$row_new[csf("job_no")]]["trims_cost"]=$row_new[csf("trims_cost")];
			$summary_data[$row_new[csf("job_no")]]["emb_cost"]=$row_new[csf("embel_cost")];

			$summary_data[$row_new[csf("job_no")]]["lab_test"]=$row_new[csf("lab_test")];
			$summary_data[$row_new[csf("job_no")]]["lab_test_job"]=$other_costing_arr[$row_new[csf("job_no")]]['lab_test'];

			$summary_data[$row_new[csf("job_no")]]["inspection"]=$row_new[csf("inspection")];
			$summary_data[$row_new[csf("job_no")]]["inspection_job"]=$other_costing_arr[$row_new[csf("job_no")]]['inspection'];

			$summary_data[$row_new[csf("job_no")]]["freight"]=$row_new[csf("freight")];
			$summary_data[$row_new[csf("job_no")]]["freight_job"]=$other_costing_arr[$row_new[csf("job_no")]]['freight'];

			$summary_data[$row_new[csf("job_no")]]["currier_pre_cost"]=$row_new[csf("currier_pre_cost")];
			$summary_data[$row_new[csf("job_no")]]["currier_pre_cost_job"]=$other_costing_arr[$row_new[csf("job_no")]]['currier_pre_cost'];

			$summary_data[$row_new[csf("job_no")]]["certificate_pre_cost"]=$row_new[csf("certificate_pre_cost")];
			$summary_data[$row_new[csf("job_no")]]["certificate_pre_cost_job"]=$other_costing_arr[$row_new[csf("job_no")]]['certificate_pre_cost'];
			$summary_data[$row_new[csf("job_no")]]["wash_cost"]=$row_new[csf("wash_cost")];

			$summary_data[$row_new[csf("job_no")]]["OtherDirectExpenses"]=$row_new[csf("lab_test")]+$row_new[csf("inspection")]+$row_new[csf("freight")]+$row_new[csf("currier_pre_cost")]+$row_new[csf("certificate_pre_cost")]+$row_new[csf("wash_cost")];

			$summary_data[$row_new[csf("job_no")]]["OtherDirectExpenses_job"]=$summary_data[$row_new[csf("job_no")]]["lab_test_job"]+$summary_data[$row_new[csf("job_no")]]["inspection_job"]+$summary_data[$row_new[csf("job_no")]]["freight_job"]+$summary_data[$row_new[csf("job_no")]]["currier_pre_cost_job"]+$summary_data[$row_new[csf("job_no")]]["certificate_pre_cost_job"];

			$summary_data[$row_new[csf("job_no")]]["cm_cost"]=$row_new[csf("cm_cost")];
			$summary_data[$row_new[csf("job_no")]]["cm_cost_job"]=$other_costing_arr[$row_new[csf("job_no")]]['cm_cost'];
			$summary_data[$row_new[csf("job_no")]]["comm_cost"]=$row_new[csf("comm_cost")];
			$summary_data[$row_new[csf("job_no")]]["common_oh"]=$row_new[csf("common_oh")];
			$summary_data[$row_new[csf("job_no")]]["common_oh_job"]=$other_costing_arr[$row_new[csf("job_no")]]['common_oh'];
			$summary_data[$row_new[csf("job_no")]]["depr_amor_pre_cost"]=$row_new[csf("depr_amor_pre_cost")];
			$summary_data[$row_new[csf("job_no")]]["depr_amor_pre_cost_job"]=$other_costing_arr[$row_new[csf("job_no")]]['depr_amor_pre_cost'];
			$summary_data[$row_new[csf("job_no")]]["margindzn"]=$row_new[csf("margin_dzn")];
			$summary_data[$row_new[csf("job_no")]]["fabric_percent"]=$row_new[csf("fabric_cost_percent")];
			$summary_data[$row_new[csf("job_no")]]["trims_percent"]=$row_new[csf("trims_cost_percent")];
			$summary_data[$row_new[csf("job_no")]]["wash_percent"]=$row_new[csf("wash_cost_percent")];
			$summary_data[$row_new[csf("job_no")]]["emb_percent"]=$row_new[csf("embel_cost_percent")];
			$summary_data[$row_new[csf("job_no")]]["commercial_percent"]=$row_new[csf("comm_cost_percent")];
			$summary_data[$row_new[csf("job_no")]]["currier_percent"]=$row_new[csf("currier_percent")];
			$summary_data[$row_new[csf("job_no")]]["commission_percent"]=$row_new[csf("commission_percent")];
			$summary_data[$row_new[csf("job_no")]]["lab_test_percent"]=$row_new[csf("lab_test_percent")];
			$summary_data[$row_new[csf("job_no")]]["freight_percent"]=$row_new[csf("freight_percent")];
		}

		//$Netprofit		= $OperatingProfitLoss-($summary_data[depr_amor_pre_cost]+$interest_expense+$income_tax);
		//$Netprofit_job	= $OperatingProfitLoss_job-($summary_data[depr_amor_pre_cost_job]+$interest_expense_job+$income_tax_job);
		//END calculating Pre-Costing Margin = job_value(po_value * count(po)) - job_expenses

		$gr_po_qnty_pcs=0;
		$gr_po_qnty_val=0;
		$gr_po_qnty_val_perc=0;
		$gr_ttl_ex_qnty=0;
		$gr_ttl_ex_qnty_val=0;
		$gr_sales_min=0;
		$gr_ttl_carton=0;
		$gr_ttl_basic_qty=0;
		$gr_ttl_ex_fac_per=0;
		$gr_ttl_short_qty=0;
		$gr_ttl_short_val=0;
		$gr_ttl_sales_cm=0;
		$gr_ttl_profitMargin=0;

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

			$ex_fact_date_range=$txt_date_from."*".$txt_date_to."_". 1;
			$total_exface_qnty=$txt_date_from."*".$txt_date_to."_". 2;
			if($break_id==0) $break_id=$row[csf('po_id')]; else $break_id=$break_id.",".$row[csf('po_id')];

			$todate=date("d-M")."-".substr(date("Y"), 2) ;
 			$todate=explode("-", $todate);
 			$todate=$todate[0]."-".strtoupper($todate[1])."-".$todate[2];
 			$diff=datediff("d",$todate, $row[csf("shipment_date")])-2;
		    $diff_color=($diff>0) ?" color:black;":"color:red;";




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

			$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";


			$comapny_id=$row[csf("company_name")];
			$delv_comp=($row[csf("source")]!=3)?  $company_library[$row[csf("del_company")]] : $supp_library[$row[csf("del_company")]];

			$onclick=" change_color('tr_".$i."','".$bgcolor."')";
			$details_report .='<tr bgcolor="'.$bgcolor.'" onclick="'.$onclick.'" id="tr_'.$i.'">';
			$details_report .='<td width="40" align="center">'.$i.'</td>
								<td width="100" align="center" ><p>'.$buyer_arr[$row[csf("buyer_name")]].'</p></td>
								<td width="110" align="center"><p>'.$row[csf("po_number")].'</p></td>
								<td width="110" align="center"><p>'.$row[csf("grouping")].'</p></td>
								<td width="110" align="center"><p>'.$row[csf("file_no")].'</p></td>
								<td width="100"><p>'.$row[csf("style_ref_no")].'</p></td>
								<td width="110" align="center"><p>';
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
								$po_quantity	= $order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]];
								$unit_price		= $row[csf("unit_price")];
								$PoVal 			= $po_quantity*$unit_price;


								//$jobValue 		= $job_order_item_qnty_arr[$row[csf("job_no")]] * $job_order_total_arr[$row[csf("job_no")]];
								$jobValue 		= $job_order_total_arr[$row[csf("job_no")]];



								//$jobCost = array_sum($fabric_costing_arr['knit']['grey'][$row[csf('job_no')]]) + array_sum($fabric_costing_arr['woven']['grey'][$row[csf('job_no')]]) + $trims_costing_arr[$row[csf('job_no')]] + $emblishment_costing_arr[$row[csf('job_no')]] + $emblishment_costing_arr_wash[$row[csf('job_no')]] + $commercial_costing_arr[$row[csf('job_no')]] + $commission_costing_arr[$row[csf('job_no')]]  + $other_costing_arr[$row[csf('job_no')]]['cm_cost'] + $summary_data[$row[csf("job_no")]]["OtherDirectExpenses"];

								//Commission: $commission_costing_arr[$row[csf('job_no')]]
								//Fabric purchase cost: array_sum($fabric_costing_arr['knit']['grey'][$row[csf('job_no')]]) + array_sum($fabric_costing_arr['woven']['grey'][$row[csf('job_no')]])
								//Yarn Cost: $yarn_costing_arr[$row[csf('job_no')]]

								//$profitMargin = $jobValue - $jobCost;
								//$profitMargin = $jobQty * $netMarginPerPiece;
								$profitMargin = $job_order_item_qnty_arr[$row[csf("job_no")]] * $marginPerPieceJob[$row[csf("job_no")]];

								$gr_ttl_profitMargin += $profitMargin;

								$current_ex_Fact_Qty=$row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]][$row[csf("challan_id")]];
								$basic_qnty=($total_ex_fact_qty*$item_smv)/$basic_smv_arr[$row[csf("company_name")]];
								$cm_per_pcs_tot+=($cm_per_pcs*$current_ex_Fact_Qty);
								$short_excess=$total_ex_fact_qty-$po_quantity;

								$excess_msg=($short_excess>0) ?" color:green;":"color:black;";
								$excess_val_msg=(($total_ex_fact_qty*$unit_price)-$po_quantity>0) ?" color:green;":"color:black;";
								$ttl_ex_qty=($total_ex_fact_qty/$po_quantity)*100;
								$ttl_ex_qty_msg=($ttl_ex_qty>100) ?" color:green;":"color:black;";
								$excess_shortage_qty=$total_ex_fact_qty-$po_quantity;
							    	$excess_shortage_value=$excess_shortage_qty*$unit_price;
							    	$total_ex_fact_qty_parcent=($total_ex_fact_qty/$po_quantity)*100;

								$details_report .=$item_name_all.'</p>
								</td>
								<td width="70" align="center"><p>'.$item_smv.'</p></td>
								<td width="70" align="center"><p>'.$row[csf("sew_effi_percent")].'</p></td>
								<td width="100"><p>'.$row[csf("style_description")].'</p></td>
								<td width="70" align="center">'.change_date_format($row[csf("shipment_date")],"","",1).'</td>
								<td width="70" align="center"><a href="##" onclick="openmypage_ex_date('.$comapny_id.",'".$row[csf('po_id')]."','".$row[csf('item_number_id')]."','".$ex_fact_date_range."','ex_date_popup','".$row[csf('challan_id')]."'".",'1'".')">'.change_date_format($row[csf('ex_factory_date')],'','',1).'</a></td>
								<td width="80" align="right"><p>'. number_format($po_quantity,0,'', '').'</p></td>
								<td width="70" align="right"><p>'. number_format($unit_price,4).'</p></td>
								<td width="100" align="right"><p>'. number_format($PoVal,2).'</p></td>
								<td width="100" align="right"><p>'. number_format($profitMargin,2).'</p></td>
								<td width="80" align="right"><p><a href="##" onclick="openmypage_ex_date('.$comapny_id.",'".$row[csf('po_id')]."','".$row[csf('item_number_id')]."','".$ex_fact_date_range."','ex_date_popup','".$row[csf('challan_id')]."'".",'1'".')">'. number_format($current_ex_Fact_Qty,0,'.', '').'</a></p></td>
								<td width="100" align="right"><p>'. number_format( $current_ex_fact_value=$current_ex_Fact_Qty*$unit_price,2).'</p></td>
								<td width="80" align="right"><p><a href="##" onclick="openmypage_ex_date('.$comapny_id.",'".$row[csf('po_id')]."','".$row[csf('item_number_id')]."','".$total_exface_qnty."','ex_date_popup','".$row[csf('challan_id')]."'".",'1'".')">'.number_format($total_ex_fact_qty,0,'.', '').'</a></p></td>
								<td width="100" align="right"><p>'. number_format($total_ex_fact_value=$total_ex_fact_qty*$unit_price,2).'</p></td>
								<td width="80" align="right" style="'.$excess_msg.'" ><p>'. number_format($excess_shortage_qty,0,'', '').'</p></td>
								<td width="100" align="right" style="'.$excess_val_msg.'"><p>'. number_format($excess_shortage_value,2).'</p></td>
								<td align="center" style="'.$ttl_ex_qty_msg.'" width="80"><p>'. number_format($total_ex_fact_qty_parcent,0).'</p></td>
								<td align="center" width="150"><p>'.$shipment_status[$row[csf('shiping_status')]].'</p></td>
							</tr>';



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
			$g_sales_minutes+=$total_sales_minutes;
			// $g_sales_minutes+=$current_ex_Fact_Qty*$item_smv;
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
	  			if($row[csf('shiping_status')] ==3){
					$gr_ttl_ex_fac_per+=$total_ex_fact_qty_parcent;
	  				$gr_ttl_short_qty+=$excess_shortage_qty;
	  				$gr_ttl_short_val+=$excess_shortage_value;
				}
	  			$gr_ttl_sales_cm+=$cm_per_pcs*$total_ex_fact_qty;
			}

			$i++;
			$item_name_all="";


			//$pre_costing_margin_arr[$row[csf("buyer_name")]][$row[csf('job_no')]]=$job_margin_arr[$row[csf('job_no')]]*$job_qty_arr[$row[csf('job_no')]];
			//$pre_costing_cm_arr[$row[csf("buyer_name")]][$row[csf('job_no')]]=$tot_cost_arr[$row[csf('job_no')]];




		}
		$pp=$i;

		foreach($master_data as $rows)
		{
			$total_po_val+=$rows["po_value"];
		}

		?>
        <div style="width:3200x;">
            <div style="width:1820px" >
                <table width="1790"  cellspacing="0"  align="center">
                    <tr>
                        <td align="center" colspan="17" class="form_caption">
                            <strong style="font-size:16px;">Company Name:<? echo  $company_library[$cbo_company_name] ;?></strong>
                        </td>
                    </tr>
                    <tr class="form_caption">
                        <td colspan="17" align="center" class="form_caption"> <strong style="font-size:15px;">Shipped Out Order and Ref wise Details Report</strong></td>
                    </tr>
              </table>
            </div>
            <br />
            <div>
                <table width="2340" border="1" class="rpt_table" rules="all" id="table_header_2">
                    <thead>
                        <th width="40">SL</th>
                        <th width="100">Buyer Name</th>
                        <th width="110">Order NO</th>
                        <th width="110">Internal Ref</th>
                        <th width="110">File No</th>
                        <th width="100">Style Ref. no.</th>
                        <th width="110">GMT Item</th>
						<th width="70">Item SMV</th>
						<th width="70">Sew Efficiency %</th>
                        <th width="100">Style Description</th>
                        <th width="70">Shipment Date</th>
                        <th width="70">Last Ex-Fac. Date</th>
                        <th width="80">PO Qtny. (pcs)</th>
                        <th width="70">Unit Price/Pcs</th>
                        <th width="100">PO Value</th>
                        <th width="100">Pre-Costing Margin</th>
                        <th width="80">Current Ex-Fact. Qty (pcs)</th>
                        <th width="100">Current Ex-Fact. Value</th>
                        <th width="80">Total Ex-Fact. Qty.</th>
                        <th width="100">Total Ex-Fact. Value</th>
                        <th width="80">Excess/ Shortage Qty</th>
                        <th width="100">Excess/ Shortage Value</th>
                        <th width="80">Total Ex-Fact. Qty. %</th>
                        <th width="150">Ex-Fact Status</th>
                    </thead>
                </table>
            <div style="width:2340px; overflow-y:scroll; overflow-x:hidden; max-height:300px;"  id="scroll_body" >
            <?

            	$details_report .='</table>';
				echo $details_report;
            ?>




            <table width="2340" cellspacing="0" cellpadding="0"  border="1"  class="rpt_table" rules="all" id="table_footer">
                <tfoot>
                    <tr>
                    	<th width="40">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="110">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="70">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="70" align="right"><strong>Total</strong></th>
                        <th width="80" id="total_po_qtybk" align="right"><? echo  number_format($gr_po_qnty_pcs,0);?></th>
                        <th width="70" align="right">&nbsp;</th>
                        <th width="100" align="right" id="value_total_po_valubk"><? echo  number_format($gr_po_qnty_val,2); ?></th>
                        <th width="100" align="right" id="value_pre_costing_margin"><? echo number_format($gr_ttl_profitMargin,0); ?></th>
                        <th width="80" align="right" id="total_ex_qty"><? echo number_format($total_ex_qty,0); ?></th>
                        <th width="100" align="right" id="value_total_ex_valu"><? echo number_format($total_ex_valu,2);?></th>
                        <th width="80" align="right" id="g_total_ex_qtybk"><? echo number_format($gr_ttl_ex_qnty,0);?></th>
                        <th width="100" align="right" id="value_g_total_ex_valbk"><? echo number_format($gr_ttl_ex_qnty_val,2);?></th>
                        <th width="80" align="right" id="total_eecess_storage_qtybk"><? echo number_format($gr_ttl_short_qty,0);?></th>
                        <th width="100" align="right" id="value_total_eecess_storage_valbk"><? echo number_format($gr_ttl_short_val,0);?></th>
                        <th width="80" id="total_ex_perbk"><? echo number_format($gr_ttl_ex_fac_per,0);?></th>
                        <th width="150">&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
            </div>
            </div>
        </div>

		<?
	}
	else if($reportType==9) // Monthly 2 Button
	{
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
			 $company_cond="and a.company_name = '$cbo_company_name'";
			 $company_cond2="and c.company_name = '$cbo_company_name'";
		}
		else
		{
			 $company_cond="";
		}

		$costing_per_arr = return_library_array("select job_no, costing_per from wo_pre_cost_mst","job_no","costing_per");

		$sql_res=sql_select("SELECT b.po_break_down_id as po_id, sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as return_qnty from pro_ex_factory_mst b  where  b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id");

		$ex_factory_qty_arr=array();
		foreach($sql_res as $row)
		{
			$ex_factory_qty_arr[$row[csf('po_id')]]['return_qty']=$row[csf('return_qnty')];
		}

		$sql= "SELECT b.id as po_id, (b.unit_price/c.total_set_qnty) as unit_price,b.po_quantity,b.po_received_date, c.total_set_qnty, c.id as job_id, c.job_no, c.buyer_name, c.company_name, c.set_smv, a.ex_factory_qnty as ex_factory_qnty,a.ex_factory_date
		from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c,pro_ex_factory_delivery_mst d
		where a.po_break_down_id=b.id and b.job_no_mst=c.job_no   $del_location_cond $del_floor_cond $del_comp_cond  $str_cond $company_cond2 $buyer_conds $internal_ref_cond $search_cond and  a.entry_form!=85  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active  in(1,2,3) and c.is_deleted=0 and c.status_active  in(1,2,3) and a.delivery_mst_id= d.id
		order by a.ex_factory_date ASC ";//c.job_no

		//PRO_EX_FACTORY_DELIVERY_MST

		// echo $sql;die();
		$sql_result=sql_select($sql);
		$poIdArray = array();
		$all_job_arr = array();
		foreach ($sql_result as $val)
		{
			$all_job_arr[trim($val[csf("job_no")])]=trim($val[csf("job_no")]);
			$poIdArray[$val[csf('po_id')]] = $val[csf('po_id')];
		}
		$allPoIds = implode(",", $poIdArray);

		$all_job="'".implode("','", array_unique($all_job_arr))."'";

		if($allPoIds!="")
		{
			$cm_gmt_cost_dzn_arr=array();
			$cm_gmt_cost_dzn_arr=fnc_po_wise_cm_gmt_class($all_company_ids,$allPoIds);
		}

		// print_r($cm_gmt_cost_dzn_arr);die;

		/******************************************************************************************************
		*																									  *
		*								GETTING PRICE QUOTATION WISE CM VALU							      *
		*																									  *
		*******************************************************************************************************/
		$quotation_qty_sql="SELECT a.id  as quotation_id,a.mkt_no,a.sew_smv,a.sew_effi_percent,a.gmts_item_id,a.company_id,a.buyer_id,a.costing_per, a.style_desc as style_desc, a.style_ref, a.order_uom,a.offer_qnty, a.total_set_qnty as ratio, a.quot_date,a.est_ship_date,b.costing_per_id,b.price_with_commn_pcs,b.total_cost,b.costing_per_id,c.job_no from wo_price_quotation a,wo_price_quotation_costing_mst b,wo_po_details_master c  where a.id=b.quotation_id and a.id=c.quotation_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and a.offer_qnty>0 and c.job_no in($all_job) order  by a.id ";
		// echo $quotation_qty_sql;die();
		$quotation_qty_sql_res = sql_select($quotation_qty_sql);
		$quotation_qty_array = array();
		$quotation_id_array = array();
		$all_jobs_array = array();
		$jobs_wise_quot_array = array();
		foreach ($quotation_qty_sql_res as $val)
		{
			$quotation_qty_array[$val['JOB_NO']]['QTY_PCS'] += $val['OFFER_QNTY']*$val['RATIO'];
			$quotation_qty_array[$val['JOB_NO']]['COSTING_PER_ID'] += $val['COSTING_PER_ID'];
			$quotation_id_array[$val['QUOTATION_ID']] = $val['QUOTATION_ID'];
			$all_jobs_array[$val['JOB_NO']] = $val['JOB_NO'];
			$jobs_wise_quot_array[$val['JOB_NO']] = $val['QUOTATION_ID'];

			$quot_wise_arr[$val[csf("quotation_id")]]['offer_qnty']=$val[csf("offer_qnty")];
			$quot_wise_arr[$val[csf("quotation_id")]]['costing_per_id']=$val[csf("costing_per_id")];

			$style_wise_arr[$val[csf("job_no")]]['costing_per']=$val[csf("costing_per")];
			$style_wise_arr[$val[csf("job_no")]]['gmts_item_id']=$val[csf("gmts_item_id")];
			$style_wise_arr[$val[csf("job_no")]]['sew_smv']=$val[csf("sew_smv")];
			$style_wise_arr[$val[csf("job_no")]]['sew_effi_percent']=$val[csf("sew_effi_percent")];
			$style_wise_arr[$val[csf("job_no")]]['shipment_date'].=$val[csf('est_ship_date')].',';
			$style_wise_arr[$val[csf("job_no")]]['quotation_id']=$val[csf("quotation_id")];
			$style_wise_arr[$val[csf("job_no")]]['buyer_name']=$val[csf("buyer_id")];
			$offer_qnty_pcs=$val[csf('offer_qnty')]*$val[csf('ratio')];
			$style_wise_arr[$val[csf("job_no")]]['qty_pcs']+=$val[csf('offer_qnty')]*$val[csf('ratio')];
			$style_wise_arr[$val[csf("job_no")]]['qty']+=$val[csf('offer_qnty')];
			$style_wise_arr[$val[csf("job_no")]]['final_cost_pcs']+=$val[csf('price_with_commn_pcs')];
			$style_wise_arr[$val[csf("job_no")]]['total_cost']+=$offer_qnty_pcs*$val[csf('price_with_commn_pcs')];
		}
		$all_quot_id = implode(",", $quotation_id_array);

		// print_r($style_wise_arr);die();
		// ===============================================================================
		$sql_fab = "SELECT a.quotation_id,sum(a.avg_cons) as cons_qnty, sum(a.amount) as amount,b.job_no from wo_pri_quo_fabric_cost_dtls a,wo_po_details_master b where a.quotation_id=b.quotation_id and a.quotation_id in($all_quot_id) and a.fabric_source=2 and a.status_active=1 and b.status_active=1 group by  a.quotation_id,b.job_no";
		// echo $sql_fab;die();
		$data_array_fab=sql_select($sql_fab);
		foreach($data_array_fab as $row)
		{
			$costing_per_id=$quot_wise_arr[$row[csf("quotation_id")]]['costing_per_id'];
			if($costing_per_id==1){$fab_order_price_per_dzn=12;}
			else if($costing_per_id==2){$fab_order_price_per_dzn=1;}
			else if($costing_per_id==3){$fab_order_price_per_dzn=24;}
			else if($costing_per_id==4){$fab_order_price_per_dzn=36;}
			else if($costing_per_id==5){$fab_order_price_per_dzn=48;}

			$fab_order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
			$fab_summary_data[$row[csf("job_no")]]['fab_amount_dzn']+=$row[csf("amount")];
			$fab_summary_data[$row[csf("job_no")]]['fab_amount_total_value']+=($row[csf("amount")]/$fab_order_price_per_dzn)*$fab_order_job_qnty;
			//$yarn_amount_dzn+=$row[csf('amount')];
		}
		// ==================================================================================
		$sql_yarn = "SELECT a.quotation_id,sum(a.cons_qnty) as cons_qnty, sum(a.amount) as amount,b.job_no from wo_pri_quo_fab_yarn_cost_dtls a,wo_po_details_master b where a.quotation_id=b.quotation_id and a.quotation_id in($all_quot_id) and a.status_active=1 group by  a.quotation_id,b.job_no";
		// echo $sql_yarn;die();
		$data_array_yarn=sql_select($sql_yarn);
		foreach($data_array_yarn as $row)
		{
			$costing_per_id=$quot_wise_arr[$row[csf("quotation_id")]]['costing_per_id'];
			if($costing_per_id==1){$yarn_order_price_per_dzn=12;}
			else if($costing_per_id==2){$yarn_order_price_per_dzn=1;}
			else if($costing_per_id==3){$yarn_order_price_per_dzn=24;}
			else if($costing_per_id==4){$yarn_order_price_per_dzn=36;}
			else if($costing_per_id==5){$yarn_order_price_per_dzn=48;}
			$yarn_order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
			//$yarn_summary_dzn=$yarn_summary_data[$row[csf("quotation_id")]]['yarn_amount_dzn'];
			 $yarn_summary_data[$row[csf("job_no")]]['yarn_amount_dzn']+=$row[csf("amount")];
			// $summary_data['yarn_amount_dzn']+=$yarn_summary_dzn;
			 //$yarn_summary_data['yarn_amount_total_value']+=($row[csf("amount")]/$yarn_order_price_per_dzn)*$yarn_order_job_qnty;
		}
		// ===================================================================================
		$conversion_cost_arr=array();
		$sql_conversion = "SELECT a.id, a.quotation_id, a.cons_type, a.req_qnty, a.charge_unit, a.amount, a.status_active,b.body_part_id,b.fab_nature_id,b.color_type_id,b.construction ,b.composition,c.job_no
		from wo_po_details_master c, wo_pri_quo_fab_conv_cost_dtls a left join wo_pri_quo_fabric_cost_dtls b on a.quotation_id=b.quotation_id and a.cost_head=b.id
		where a.quotation_id in($all_quot_id) and a.quotation_id=c.quotation_id and a.status_active=1  ";
		// echo $sql_conversion;die();
		$data_array_conversion=sql_select($sql_conversion);
		foreach($data_array_conversion as $row)
		{
			$costing_per_id=$quot_wise_arr[$row[csf("quotation_id")]]['costing_per_id'];
			if($costing_per_id==1){$conv_order_price_per_dzn=12;}
			else if($costing_per_id==2){$conv_order_price_per_dzn=1;}
			else if($costing_per_id==3){$conv_order_price_per_dzn=24;}
			else if($costing_per_id==4){$conv_order_price_per_dzn=36;}
			else if($costing_per_id==5){$conv_order_price_per_dzn=48;}
			$conv_order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
			$conv_summary_data[$row[csf("job_no")]]['conv_amount_dzn']+=$row[csf("amount")];

			$conversion_cost_arr[$row[csf("job_no")]][$row[csf('cons_type')]]['conv_amount_dzn']+=$row[csf('amount')];
			$conversion_cost_arr[$row[csf("job_no")]][$row[csf('cons_type')]]['conv_amount_total_value']+=($row[csf("amount")]/$conv_order_price_per_dzn)*$conv_order_job_qnty;
		}
		// print_r($conversion_cost_arr);die();
		if($db_type==0)
		{
			$sql = "SELECT MAX(a.id),a.quotation_id,a.fabric_cost,a.fabric_cost_percent,a.trims_cost,a.trims_cost_percent,a.embel_cost,a.embel_cost_percent,a.wash_cost,a.wash_cost_percent,a.comm_cost,a.comm_cost_percent,a.commission,a.commission_percent,a.lab_test,a.lab_test_percent,a.inspection,a.inspection_percent,a.cm_cost,a.cm_cost_percent,a.freight,a.freight_percent,a.currier_pre_cost,a.currier_percent ,a.certificate_pre_cost,a.certificate_percent,a.common_oh,a.common_oh_percent,a.depr_amor_pre_cost,a.depr_amor_po_price,a.interest_pre_cost,a.interest_po_price,a.income_tax_pre_cost,a.income_tax_po_price,a.total_cost ,a.total_cost_percent,a.final_cost_dzn ,a.final_cost_dzn_percent ,a.confirm_price_dzn ,a.confirm_price_dzn_percent,a.final_cost_pcs,a.margin_dzn,a.margin_dzn_percent,a.a1st_quoted_price,a.confirm_price,a.revised_price,a.price_with_commn_dzn,a.costing_per_id,a.design_pre_cost,a.design_percent,a.studio_pre_cost,a.studio_percent,a.offer_qnty,b.job_no
			from wo_price_quotation_costing_mst a,wo_po_details_master b
			where a.quotation_id in($all_quot_id) and a.status_active=1 and a.quotation_id=b.quotation_id and b.status_active=1 ";
		}
		if($db_type==2)
		{
			$sql = "SELECT MAX(a.id),a.fabric_cost,a.quotation_id,a.fabric_cost_percent,a.trims_cost,a.trims_cost_percent,a.embel_cost,a.embel_cost_percent,a.wash_cost,a.wash_cost_percent,a.comm_cost,a.comm_cost_percent,a.commission,a.commission_percent,a.lab_test,a.lab_test_percent,a.inspection,a.inspection_percent,a.cm_cost,a.cm_cost_percent,a.freight,a.freight_percent,a.currier_pre_cost,a.currier_percent ,a.certificate_pre_cost,a.certificate_percent,a.common_oh,a.common_oh_percent,a.depr_amor_pre_cost,a.depr_amor_po_price,a.interest_pre_cost,a.interest_po_price,a.income_tax_pre_cost,a.income_tax_po_price,a.total_cost ,a.total_cost_percent,a.final_cost_dzn ,a.final_cost_dzn_percent ,a.confirm_price_dzn ,a.confirm_price_dzn_percent,a.final_cost_pcs,a.margin_dzn,a.margin_dzn_percent,a.a1st_quoted_price,a.confirm_price,a.revised_price,a.price_with_commn_dzn,a.costing_per_id,a.design_pre_cost,a.design_percent,a.studio_pre_cost,a.studio_percent,b.job_no
			from wo_price_quotation_costing_mst a,wo_po_details_master b
			where a.quotation_id=b.quotation_id and a.quotation_id in($all_quot_id) and a.status_active=1 and b.status_active=1   group by a.fabric_cost,a.quotation_id,a.fabric_cost_percent,a.trims_cost,a.trims_cost_percent,a.embel_cost,a.embel_cost_percent,a.wash_cost,a.wash_cost_percent,a.comm_cost,a.comm_cost_percent,a.commission,a.commission_percent,a.lab_test,a.lab_test_percent,a.inspection,a.inspection_percent,a.cm_cost,a.cm_cost_percent,a.freight,a.freight_percent,a.currier_pre_cost,a.currier_percent ,a.certificate_pre_cost,a.certificate_percent,a.common_oh,a.common_oh_percent,a.depr_amor_pre_cost,a.depr_amor_po_price,a.interest_pre_cost,a.interest_po_price,a.income_tax_pre_cost,a.income_tax_po_price,a.total_cost ,a.total_cost_percent,a.final_cost_dzn ,a.final_cost_dzn_percent ,a.confirm_price_dzn ,a.confirm_price_dzn_percent,a.final_cost_pcs,a.margin_dzn,a.margin_dzn_percent,a.a1st_quoted_price,a.confirm_price,a.revised_price,a.price_with_commn_dzn,a.costing_per_id,a.design_pre_cost,a.design_percent,a.studio_pre_cost,a.studio_percent,b.job_no";
		}
		// echo $sql;die();
		$data_array=sql_select($sql);

        foreach( $data_array as $row )
        {
			//$sl=$sl+1;
			if($row[csf("costing_per_id")]==1){$order_price_per_dzn=12;$costing_val=" DZN";}
			else if($row[csf("costing_per_id")]==2){$order_price_per_dzn=1;$costing_per=" PCS";}
			else if($row[csf("costing_per_id")]==3){$order_price_per_dzn=24;$costing_val=" 2 DZN";}
			else if($row[csf("costing_per_id")]==4){$order_price_per_dzn=36;$costing_val=" 3 DZN";}
			else if($row[csf("costing_per_id")]==5){$order_price_per_dzn=48;$costing_val=" 4 DZN";}
			$order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
			$quot_price_per_dzn_arr[$row[csf("quotation_id")]]['order_price_per_dzn']=$order_price_per_dzn;
			$price_dzn=$row[csf("confirm_price_dzn")];
			$others_cost_value=$row[csf("total_cost")]-$row[csf("cm_cost")]-$row[csf("freight")]-$row[csf("comm_cost")]-$row[csf("commission")];
			$summary_data[$row[csf('job_no')]]['price_with_commn_dzn']+=$row[csf("price_with_commn_dzn")];
			$summary_data[$row[csf('job_no')]]['price_with_total_value']+=($row[csf("price_with_commn_dzn")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['price_dzn']+=$row[csf("confirm_price_dzn")];
		    $summary_data[$row[csf('job_no')]]['commission_dzn']+=$row[csf("commission")];
			$summary_data[$row[csf('job_no')]]['commission_total_value']+=($row[csf("commission")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['trims_cost_dzn']+=$row[csf("trims_cost")];
			$summary_data[$row[csf('job_no')]]['trims_cost_total_value']+=($row[csf("trims_cost")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['embel_cost_dzn']+=$row[csf("embel_cost")];
			$summary_data[$row[csf('job_no')]]['embel_cost_total_value']+=($row[csf("embel_cost")]/$order_price_per_dzn)*$order_job_qnty;
			//$row[csf("commission")]
			$other_direct_expenses=$row[csf("wash_cost")]+$row[csf("lab_test")]+$row[csf("inspection")]+$row[csf("currier_pre_cost")]+$row[csf("certificate_pre_cost")]+$row[csf("design_pre_cost")]+$row[csf("studio_pre_cost")];

			$summary_data[$row[csf('job_no')]]['other_direct_dzn']+=$other_direct_expenses;
			$summary_data[$row[csf('job_no')]]['other_direct_total_value']+=($other_direct_expenses/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['wash_cost_dzn']+=$row[csf("wash_cost")];
			$summary_data[$row[csf('job_no')]]['wash_cost_total_value']+=($row[csf("wash_cost")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['lab_test_dzn']+=$row[csf("lab_test")];
			$summary_data[$row[csf('job_no')]]['lab_test_total_value']+=($row[csf("lab_test")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['inspection_dzn']+=$row[csf("inspection")];
			$summary_data[$row[csf('job_no')]]['inspection_total_value']+=($row[csf("inspection")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['freight_dzn']+=$row[csf("freight")];
			$summary_data[$row[csf('job_no')]]['freight_total_value']+=($row[csf("freight")]/$order_price_per_dzn)*$order_job_qnty;
			$freight_cost_data[$row[csf("job_no")]]['freight_total_value']=($row[csf("freight")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['currier_pre_cost_dzn']+=$row[csf("currier_pre_cost")];
			$summary_data[$row[csf('job_no')]]['currier_pre_cost_total_value']+=($row[csf("currier_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['certificate_pre_cost_dzn']+=$row[csf("certificate_pre_cost")];
			$summary_data[$row[csf('job_no')]]['certificate_pre_cost_total_value']+=($row[csf("certificate_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;

			$summary_data[$row[csf('job_no')]]['design_pre_cost_dzn']+=$row[csf("design_pre_cost")];
			$summary_data[$row[csf('job_no')]]['design_pre_cost_total_value']+=($row[csf("design_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['studio_pre_cost_dzn']+=$row[csf("studio_pre_cost")];
			$summary_data[$row[csf('job_no')]]['studio_pre_cost_total_value']+=($row[csf("studio_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;

			$quot_studio_cost_dzn_arr[$row[csf("job_no")]]['studio_dzn_cost']=$row[csf("studio_percent")];
			$quot_studio_cost_dzn_arr[$row[csf("job_no")]]['common_oh']=$row[csf("common_oh")];

			$fab_amount_dzn=$fab_summary_data[$row[csf("job_no")]]['fab_amount_dzn'];
			$summary_data[$row[csf('job_no')]]['fab_amount_dzn']+=$fab_amount_dzn;
			$summary_data[$row[csf('job_no')]]['fab_amount_total_value']+=($fab_amount_dzn/$order_price_per_dzn)*$order_job_qnty;
			$yarn_amount_dzn=$yarn_summary_data[$row[csf("job_no")]]['yarn_amount_dzn'];
			//echo ($yarn_amount_dzn/$order_price_per_dzn)*$order_job_qnty.'d';
			$summary_data[$row[csf('job_no')]]['yarn_amount_dzn']+=$yarn_amount_dzn;
			$summary_data[$row[csf('job_no')]]['yarn_amount_total_value']+=($yarn_amount_dzn/$order_price_per_dzn)*$order_job_qnty;
			$conv_amount_dzn=$conv_summary_data[$row[csf("job_no")]]['conv_amount_dzn'];
			$summary_data[$row[csf('job_no')]]['conversion_cost_dzn']+=$conv_amount_dzn;
			$summary_data[$row[csf('job_no')]]['conversion_cost_total_value']+=($conv_amount_dzn/$order_price_per_dzn)*$order_job_qnty;

			//$NetFOBValue=($row[csf("price_with_commn_dzn")]-$row[csf("commission")]);
			$net_value_dzn=$row[csf("price_with_commn_dzn")];

			$summary_data[$row[csf('job_no')]]['netfobvalue_dzn']+=($row[csf("price_with_commn_dzn")]);
			$summary_data[$row[csf('job_no')]]['netfobvalue']+=(($row[csf("price_with_commn_dzn")])/$order_price_per_dzn)*$order_job_qnty;

			//yarn_amount_total_value
			$all_cost_dzn=$yarn_amount_dzn+$fab_amount_dzn+$conv_amount_dzn+$row[csf("trims_cost")]+$row[csf("embel_cost")]+$other_direct_expenses;
			//echo $yarn_amount_dzn.'Y='.$fab_amount_dzn.'F='.$conv_amount_dzn.'Cnv='.$row[csf("trims_cost")].'Tr='.$row[csf("embel_cost")].'Em='.$other_direct_expenses;
			$summary_data[$row[csf('job_no')]]['cost_of_material_service']+=$all_cost_dzn;
			$summary_data[$row[csf('job_no')]]['cost_of_material_service_total_value']+=($all_cost_dzn/$order_price_per_dzn)*$order_job_qnty;
			$contribute_netfob_value_dzn=$net_value_dzn-($fab_amount_dzn+$yarn_amount_dzn+$conv_amount_dzn+$row[csf("trims_cost")]+$row[csf("embel_cost")]+$other_direct_expenses);
			$summary_data[$row[csf('job_no')]]['contribution_margin_dzn']+=$contribute_netfob_value_dzn;
			$summary_data[$row[csf('job_no')]]['contribution_margin_total_value']+=(($contribute_netfob_value_dzn)/$order_price_per_dzn)*$order_job_qnty;

			$summary_data[$row[csf('job_no')]]['cm_cost_dzn']+=$row[csf("cm_cost")];
			$summary_data[$row[csf('job_no')]]['cm_cost_total_value']+=($row[csf("cm_cost")]/$order_price_per_dzn)*$order_job_qnty;

			$summary_data[$row[csf('job_no')]]['comm_cost_dzn']+=$row[csf("comm_cost")];
			$summary_data[$row[csf('job_no')]]['comm_cost_total_value']+=($row[csf("comm_cost")]/$order_price_per_dzn)*$order_job_qnty;

			$summary_data[$row[csf('job_no')]]['common_oh_dzn']+=$row[csf("common_oh")];
			$summary_data[$row[csf('job_no')]]['common_oh_total_value']+=($row[csf("common_oh")]/$order_price_per_dzn)*$order_job_qnty;
			//echo $netfob_value_dzn.'='.$row[csf("cm_cost")];
			$Contribution_Margin=$netfob_value_dzn-$LessCostOfMaterialServices;
			$tot_gross_profit_dzn=$contribute_netfob_value_dzn-$row[csf("cm_cost")];
			$summary_data[$row[csf('job_no')]]['gross_profit_dzn']+=$tot_gross_profit_dzn;
			$summary_data[$row[csf('job_no')]]['gross_profit_total_value']+=(($tot_gross_profit_dzn)/$order_price_per_dzn)*$order_job_qnty;

			//$Gross_Profit= $Contribution_Margin-$row[csf("cm_cost")];
			$operate_profit_loss_dzn=$tot_gross_profit_dzn;//-($row[csf("comm_cost")]+$row[csf("common_oh")]);
			$summary_data[$row[csf('job_no')]]['operating_profit_loss_dzn']+=$operate_profit_loss_dzn;
			$summary_data[$row[csf('job_no')]]['operating_profit_loss_total_value']+=($operate_profit_loss_dzn/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['depr_amor_pre_cost_dzn']+=$row[csf("depr_amor_pre_cost")];
			$summary_data[$row[csf('job_no')]]['depr_amor_pre_cost_total_value']+=($row[csf("depr_amor_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['interest_pre_cost_dzn']+=$row[csf("interest_pre_cost")];
			$summary_data[$row[csf('job_no')]]['interest_pre_cost_total_value']+=($row[csf("interest_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
			$summary_data[$row[csf('job_no')]]['income_tax_pre_cost_dzn']+=$row[csf("income_tax_pre_cost")];
			$summary_data[$row[csf('job_no')]]['income_tax_pre_cost_total_value']+=($row[csf("income_tax_pre_cost")]/$order_price_per_dzn)*$order_job_qnty;
			$net_profit_dzn=$operate_profit_loss_dzn-($row[csf("depr_amor_pre_cost")]+$row[csf("interest_pre_cost")]+$row[csf("income_tax_pre_cost")]);
			$summary_data[$row[csf('job_no')]]['net_profit_dzn']+=$net_profit_dzn;
			$summary_data[$row[csf('job_no')]]['net_profit_dzn_total_value']+=($net_profit_dzn/$order_price_per_dzn)*$order_job_qnty;
		}
		// echo "<pre>";
		// print_r($summary_data);
		// die();
		//======================================================================

		$sql_commi = "SELECT a.id,a.quotation_id,a.particulars_id,a.commission_base_id,a.commision_rate,a.commission_amount,a.status_active,b.job_no
		from  wo_pri_quo_commiss_cost_dtls a,wo_po_details_master b
		where  a.quotation_id=b.quotation_id and a.quotation_id in($all_quot_id) and a.status_active=1 and a.commission_amount>0 and b.status_active=1";
		// echo $sql_commi;die();
		$result_commi=sql_select($sql_commi);
		$CommiData_foreign_cost=$CommiData_lc_cost=$foreign_dzn_commission_amount=$local_dzn_commission_amount=0;
		foreach($result_commi as $row)
		{
			$order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
			$order_price_per_dzn=$quot_price_per_dzn_arr[$row[csf("quotation_id")]]['order_price_per_dzn'];

			if($row[csf("particulars_id")]==1) //Foreign
			{
				$CommiData_foreign_cost+=($row[csf("commission_amount")]/$order_price_per_dzn)*$order_job_qnty;
				$foreign_dzn_commission_amount+=$row[csf("commission_amount")];
				$CommiData_foreign_quot_cost_arr[$row[csf("job_no")]]+=($row[csf("commission_amount")]/$order_price_per_dzn)*$order_job_qnty;
			}
			else
			{
				$CommiData_lc_cost+=($row[csf("commission_amount")]/$order_price_per_dzn)*$order_job_qnty;
				$local_dzn_commission_amount+=$row[csf("commission_amount")];
				$commision_local_quot_cost_arr[$row[csf("job_no")]]=$row[csf("commision_rate")];
			}
		}
		//=====================================================================================
		$sql_comm="SELECT a.item_id,a.quotation_id,sum(a.amount) as amount,b.job_no from wo_pri_quo_comarcial_cost_dtls a,wo_po_details_master b where a.quotation_id=b.quotation_id and a.quotation_id in($all_quot_id) and a.status_active=1 group by a.quotation_id,a.item_id,b.job_no";
		// echo $sql_comm;die();
		$tot_lc_dzn_Commer=$tot_without_lc_dzn_Commer=0;
		// $summary_data['comm_cost_dzn']=0;
		// $summary_data['comm_cost_total_value']=0;
		$result_comm=sql_select($sql_comm);
		$commer_lc_cost = array();
		$commer_without_lc_cost = array();
		foreach($result_comm as $row)
		{
			$order_job_qnty=$quot_wise_arr[$row[csf("quotation_id")]]['offer_qnty'];
			$order_price_per_dzn=$quot_price_per_dzn_arr[$row[csf("quotation_id")]]['order_price_per_dzn'];
			//$summary_data['comm_cost_dzn']=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;
			$comm_amtPri=$row[csf('amount')];
			$item_id=$row[csf('item_id')];
			if($item_id==1)//LC
			{
				$commer_lc_cost[$row[csf('job_no')]]+=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;

				$commer_lc_cost_quot_arr[$row[csf("job_no")]]+=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;
			}
			else
			{
				$commer_without_lc_cost[$row[csf('job_no')]]+=($row[csf("amount")]/$order_price_per_dzn)*$order_job_qnty;
			}
		}
		// echo "<pre>";print_r($summary_data);die();

		// echo $total_quot_commision_local_val;die();
		/********************************************************************************************************
		*																										*
		*													END													*
		*																										*
		********************************************************************************************************/

		$poChkArray = array();
		$monthWisePoChkArray = array();
		$buyer_wisi_data=array();
		$result_data_arr=array();
		$po_data_arr=array();
		$month_wise_po_qty_arr=array();
		foreach($sql_result as $row)
		{

			/*========================================================================================
			*																						  *
			*								Calculate cm valu 										  *
			*																					  	  *
			*========================================================================================*/
			$tot_dye_chemi_process_amount 	= $conversion_cost_arr[$row[csf('job_no')]][101]['conv_amount_total_value'];
			$tot_yarn_dye_process_amount 	= $conversion_cost_arr[$row[csf('job_no')]][30]['conv_amount_total_value'];
			$tot_aop_process_amount 		= $conversion_cost_arr[$row[csf('job_no')]][35]['conv_amount_total_value'];

			$total_quot_qty=$total_quot_pcs_qty=$total_quot_amount=$total_sew_smv=$total_quot_amount_cal=0;
			$all_last_shipdates='';

            foreach($style_wise_arr as $style_key=>$val)
			{
				$total_cost=$val[('qty')]*$val[('final_cost_pcs')];
				$total_quot_qty+=$val[('qty')];
				$total_quot_pcs_qty+=$val[('qty_pcs')];
				$total_sew_smv+=$val[('sew_smv')];
				$total_quot_amount+=$total_cost;
				$total_quot_amount_arr[$val[('quotation_id')]]+=$total_cost;
			}
			$total_quot_amount_cal = $style_wise_arr[$row[csf('job_no')]]['qty']*$style_wise_arr[$row[csf('job_no')]]['final_cost_pcs'];
			$tot_cm_for_fab_cost=$summary_data[$row[csf('job_no')]]['conversion_cost_total_value']-($tot_yarn_dye_process_amount+$tot_dye_chemi_process_amount+$tot_aop_process_amount);
			// echo $row[csf('job_no')]."==".$summary_data[$row[csf('job_no')]]['conversion_cost_total_value']."-(".$tot_yarn_dye_process_amount."+".$tot_dye_chemi_process_amount."+".$tot_aop_process_amount.")<br>";
			$commision_quot_local=$commision_local_quot_cost_arr[$row[csf('job_no')]];
			$tot_sum_amount_quot_calc=$total_quot_amount_cal-($CommiData_foreign_quot_cost_arr[$row[csf('job_no')]]+$commer_lc_cost_quot_arr[$row[csf('job_no')]]+$freight_cost_data[$row[csf('job_no')]]['freight_total_value']);
			$tot_sum_amount_quot_calccc = ($tot_sum_amount_quot_calc*$commision_quot_local)/100;
			$tot_inspect_cour_certi_cost=$summary_data[$row[csf('job_no')]]['inspection_total_value']+$summary_data[$row[csf('job_no')]]['currier_pre_cost_total_value']+$summary_data[$row[csf('job_no')]]['certificate_pre_cost_total_value']+$tot_sum_amount_quot_calccc+$summary_data[$row[csf('job_no')]]['design_pre_cost_total_value'];
			// echo $summary_data[$row[csf('job_no')]]['inspection_total_value']."+".$summary_data[$row[csf('job_no')]]['currier_pre_cost_total_value']."+".$summary_data[$row[csf('job_no')]]['certificate_pre_cost_total_value']."+".$tot_sum_amount_quot_calccc."+".$summary_data[$row[csf('job_no')]]['design_pre_cost_total_value']."<br>";

			$tot_emblish_cost=$summary_data[$row[csf('job_no')]]['embel_cost_total_value'];
			$pri_freight_cost_per=$summary_data[$row[csf('job_no')]]['freight_total_value'];
			$pri_commercial_per=$commer_lc_cost[$row[csf('job_no')]];
			$CommiData_foreign_cost=$CommiData_foreign_quot_cost_arr[$row[csf('job_no')]];

			$total_btb=$summary_data[$row[csf('job_no')]]['lab_test_total_value']+$tot_emblish_cost+$summary_data[$row[csf('job_no')]]['comm_cost_total_value']+$summary_data[$row[csf('job_no')]]['trims_cost_total_value']+$tot_yarn_dye_process_amount+$tot_dye_chemi_process_amount+$summary_data[$row[csf('job_no')]]['yarn_amount_total_value']+$tot_aop_process_amount+$summary_data[$row[csf('job_no')]]['common_oh_total_value']+$summary_data[$row[csf('job_no')]]['studio_pre_cost_total_value']+$tot_inspect_cour_certi_cost;
			// echo $summary_data[$row[csf('job_no')]]['lab_test_total_value']."+".$tot_emblish_cost."+".$summary_data[$row[csf('job_no')]]['comm_cost_total_value']."+".$summary_data[$row[csf('job_no')]]['trims_cost_total_value']."+".$tot_yarn_dye_process_amount."+".$tot_dye_chemi_process_amount."+".$summary_data[$row[csf('job_no')]]['yarn_amount_total_value']."+".$tot_aop_process_amount."+".$summary_data[$row[csf('job_no')]]['common_oh_total_value']."+".$summary_data[$row[csf('job_no')]]['studio_pre_cost_total_value']."+".$tot_inspect_cour_certi_cost."<br>";
			$tot_quot_sum_amount=$total_quot_amount_cal-($CommiData_foreign_cost+$pri_freight_cost_per+$pri_commercial_per);
			// echo $total_quot_amount_cal."-(".$CommiData_foreign_cost."+".$pri_freight_cost_per."+".$pri_commercial_per.")<br>";
			$NetFOBValue_job = $tot_quot_sum_amount;
			// echo $NetFOBValue_job."<br>";
			$total_cm_for_gmt=($NetFOBValue_job-$tot_cm_for_fab_cost-$total_btb);

			$total_quot_pcs_qty = $quotation_qty_array[$row[csf('job_no')]]['QTY_PCS'];
			// echo $total_cm_for_gmt;echo "<br>";
			$cm_valu_lc = 0;
			$cm_valu_lc = $total_cm_for_gmt/$total_quot_pcs_qty;
			// echo $total_cm_for_gmt."/".$total_quot_pcs_qty.")"; echo "<br>";
			// echo number_format(($total_cm_for_gmt/$total_quot_pcs_qty)*12,2); echo "<br>";
			/*========================================================================================
			*																						  *
			*											END											  *
			*																					  	  *
			*========================================================================================*/

			$cm_val=0;
			//if(!in_array($row[csf('job_no')],$temp_arr)){

				$dzn_qnty=$cm_value=$cm_value_rate=0;
				if($costing_per_arr[$row[csf('job_no')]]==1) $dzn_qnty=12;
				else if($costing_per_arr[$row[csf('job_no')]]==3) $dzn_qnty=12*2;
				else if($costing_per_arr[$row[csf('job_no')]]==4) $dzn_qnty=12*3;
				else if($costing_per_arr[$row[csf('job_no')]]==5) $dzn_qnty=12*4;
				else $dzn_qnty=1;

				$dzn_qnty=$dzn_qnty*$row[csf('total_set_qnty')];
				$cm_value_rate=($cm_gmt_cost_dzn_arr[$row[csf('po_id')]]['dzn']/$dzn_qnty);

				//$temp_arr[]=$row[csf('job_no')];

			//}

			$exfactreturn_qty=$ex_factory_qty_arr[$row[csf('po_id')]]['return_qty'];
			$basic_qnty=($row[csf("ex_factory_qnty")]*$row[csf("set_smv")])/$basic_smv_arr[$row[csf("company_name")]];
			// $cm_val=$cm_value_rate*$row[csf("ex_factory_qnty")];
			$cm_val=$cm_valu_lc*$row[csf("ex_factory_qnty")];
			$result_data_arr[date("Y-m",strtotime($row[csf("ex_factory_date")]))][$row[csf("buyer_name")]]['cm_value'] +=$cm_val;
			$result_data_arr[date("Y-m",strtotime($row[csf("ex_factory_date")]))][$row[csf("buyer_name")]]['ex_factory_qnty'] +=$row[csf("ex_factory_qnty")]-$exfactreturn_qty;
			$result_data_arr[date("Y-m",strtotime($row[csf("ex_factory_date")]))][$row[csf("buyer_name")]]['basic_qnty'] +=$basic_qnty;
			$result_data_arr[date("Y-m",strtotime($row[csf("ex_factory_date")]))][$row[csf("buyer_name")]]['lib_basic_qnty'] =$target_basic_qnty[$row[csf("buyer_name")]][date("Y-m",strtotime($row[csf("ex_factory_date")]))];

			$result_data_arr[date("Y-m",strtotime($row[csf("ex_factory_date")]))][$row[csf("buyer_name")]]['ex_factory_value'] +=($row[csf("ex_factory_qnty")]*$row[csf("unit_price")]);
			$buyer_tem_arr[$row[csf("buyer_name")]]=$row[csf("buyer_name")];

			$result_data_arr[date("Y-m",strtotime($row[csf("ex_factory_date")]))][$row[csf("buyer_name")]]['commision_cost'] +=($commision_per_pic*($row[csf("ex_factory_qnty")]-$exfactreturn_qty));

			/*if(!in_array($row[csf('po_id')],$monthWisePoChkArray))
			{

				$monthWisePoChkArray[date("Y-m",strtotime($row[csf("ex_factory_date")]))][$row[csf('po_id')]]=$row[csf('po_id')];
			}*/

			if(!in_array($row[csf('po_id')],$poChkArray))
			{
				$month_wise_po_qty_arr[date("Y-m",strtotime($row[csf("ex_factory_date")]))][$row[csf("buyer_name")]]['po_quantity'] +=$row[csf("po_quantity")];

				$month_wise_po_qty_arr[date("Y-m",strtotime($row[csf("ex_factory_date")]))][$row[csf("buyer_name")]]['po_value'] +=($row[csf("po_quantity")]*$row[csf("unit_price")]);

				$po_data_arr[$row[csf("buyer_name")]]['po_quantity'] +=$row[csf("po_quantity")];

				$po_data_arr[$row[csf("buyer_name")]]['po_value'] +=($row[csf("po_quantity")]*$row[csf("unit_price")]);
				$poChkArray[$row[csf('po_id')]]=$row[csf('po_id')];

			}

			//-----------------------------
			$key=date('M',strtotime($row[csf("ex_factory_date")]));
			$key2=date('Y',strtotime($row[csf("ex_factory_date")]));
			$monthArr[$key]=$key;
			$yearArr[$key2]=$key2;

			$yearMonthDataArr[EXF_QTY][$key2][$key] +=$row[csf("ex_factory_qnty")]-$exfactreturn_qty;
			$yearMonthDataArr[EXF_VAL][$key2][$key] +=($row[csf("ex_factory_qnty")])*$row[csf("unit_price")];
			$allBuyerArr[$row[csf("buyer_name")]]=$row[csf("buyer_name")];


			//------------------------




		}
		// $all_po_ids = implode(",", $poChkArray)
		//asort($result_data_arr);

		// echo "<pre>";print_r($month_wise_po_qty_arr);

		$total_month=count($result_data_arr);
		$width=($total_month*500)+130;
		$colspan=$total_month*5;
		$main_data="";
		$i=1;
		// $buyer_tem_arr = array_filter($buyer_tem_arr);

		foreach($buyer_tem_arr as $buyer_id=>$val)
        {
			if ($i%2==0)
			$bgcolor="#E9F3FF";
			else
			$bgcolor="#FFFFFF";

			$main_data.='<tr bgcolor="'.$bgcolor.'" onclick="change_color(\'tr_'.$i.'\',\''.$bgcolor.'\')" id="tr_'.$i.'" >
			<td width="30">'.$i.'</td>
			<td width="100">'.$buyer_arr[$buyer_id].'</td>';
			$tot_po_qty=$tot_po_val=$tot_ex_factory_qnty=$tot_ex_factory_value=$tot_cm_value=0;
			foreach($result_data_arr as $month_id=>$result)
			{
				$po_qnty=$month_wise_po_qty_arr[$month_id][$buyer_id]['po_quantity'];
				$po_value=$month_wise_po_qty_arr[$month_id][$buyer_id]['po_value'];
				$ex_factory_qnty=$result_data_arr[$month_id][$buyer_id]['ex_factory_qnty'];
				$ex_factory_value=$result_data_arr[$month_id][$buyer_id]['ex_factory_value'];
				$cm_value=$result_data_arr[$month_id][$buyer_id]['cm_value'];

				$main_data.='<td width="100" align="right">'. number_format($po_qnty,0).' </td>
				<td width="100" align="right">'.  number_format($po_value,0).' </td>
				<td width="100" align="right">'.  number_format($ex_factory_qnty,0).' </td>
				<td width="100" align="right">'.  number_format($ex_factory_value,2).' </td>
				<td width="100" align="right">'.  number_format($cm_value,2).' </td>';

				$total_mon_data[$month_id]['po_quantity'] += $po_qnty;
				$total_mon_data[$month_id]['po_value'] += $po_value;
				$total_mon_data[$month_id]['ex_factory_qnty'] += $ex_factory_qnty;
				$total_mon_data[$month_id]['ex_factory_value'] += $ex_factory_value;
				$total_mon_data[$month_id]['cm_val'] += $cm_value;

				$tot_po_qty+=$po_qnty;
				$tot_po_val+=$po_value;
				$tot_ex_factory_qnty+=$ex_factory_qnty;
				$tot_ex_factory_value+=$ex_factory_value;
				$tot_cm_value+=$cm_value;
				$tot_commision+=($ex_factory_value-$commision_cost);
			}

			//$buyer_wisi_data[$buyer_id]['lib_basic_qnty'] += $tot_lib_basic_qnty;
			$buyer_wisi_data[$buyer_id]['po_quantity'] += $po_qnty;
			$buyer_wisi_data[$buyer_id]['po_value'] += $po_value;
			$buyer_wisi_data[$buyer_id]['ex_factory_qnty'] += $tot_ex_factory_qnty;
			$buyer_wisi_data[$buyer_id]['ex_factory_value'] += $tot_ex_factory_value;
			$buyer_wisi_data[$buyer_id]['cm_val'] += $tot_cm_value;
			$main_data.='</tr>';
			$i++;
        }
		//echo $main_data;die
		//echo $total_month;die;





		ob_start();

		?>
        <div class="main">
        	<div style="width: 630px">
	            <table width="630"  cellspacing="0" align="left">
	                <tr>
	                    <td align="center" colspan="7" class="form_caption">
	                    <strong style="font-size:16px;">Company Name:<? echo  $company_library[$cbo_company_name] ;?></strong>
	                	</td>
	                </tr>
	                <tr>
	                    <td align="center" colspan="7" class="form_caption">
	                    <strong style="font-size:15px;">Ex-Factory Report</strong>
	                	</td>
	                </tr>
	                <tr class="form_caption">
	                	<td colspan="7" align="center"><br />Month Wise Export Qty With Value Summary: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Total Buyer: <?=count($allBuyerArr);?></td>
	                </tr>
	            </table>
	        </div>
            <br clear="all">

            <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
            	<thead>
                	<th>Year</th>
                	<th></th>
                	<th>Total</th>
                	<? foreach($monthArr as $key=>$rows){ ?>
						<th><?=$key;?></th>
                    <? } ?>
                </thead>
            	<tbody>
                	<?
					$i=1;
					$grandTotalDataArr=array();
					foreach($yearArr as $y=>$yv){
					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
						$grandTotalDataArr[TOT_EXF_QTY]+=array_sum($yearMonthDataArr[EXF_QTY][$y]);
						$grandTotalDataArr[TOT_EXF_VAL]+=array_sum($yearMonthDataArr[EXF_VAL][$y]);

					?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center" valign="middle" width="35"><?=$y;?></td>
                        <td align="right" width="120">Total Shipment Qty <br /> Total Shipment Value $</td>
                        <td align="right" width="70">
                            <?=number_format(array_sum($yearMonthDataArr[EXF_QTY][$y]));?> <br />
                            $<?=number_format(array_sum($yearMonthDataArr[EXF_VAL][$y]),2);?>
                        </td>
                        <? foreach($monthArr as $key=>$val){ ?>
                            <td align="right" width="60">
								<?
                                $grandTotalDataArr[MONTH_EXF_QTY][$key]+=$yearMonthDataArr[EXF_QTY][$y][$key];
                                $grandTotalDataArr[MONTH_EXF_VAL][$key]+=$yearMonthDataArr[EXF_VAL][$y][$key];
                                ?>


								<?=number_format($yearMonthDataArr[EXF_QTY][$y][$key]);?> <br />
								$<?=number_format($yearMonthDataArr[EXF_VAL][$y][$key],2);?>
                            </td>
                        <? } ?>
                    </tr>
                    <? $i++;} ?>


                    <tfoot>
                        <th colspan="2" align="right">Grand Total Shipment Qty <br />Grand Total Shipment Value $</th>
                        <th align="right">
                            <?=number_format($grandTotalDataArr[TOT_EXF_QTY]);?> <br />
                            $<?=number_format($grandTotalDataArr[TOT_EXF_VAL],2);?>
                        </th>
                        <? foreach($monthArr as $key=>$val){ ?>
                            <th align="right" width="60">
								<?=number_format($grandTotalDataArr[MONTH_EXF_QTY][$key]);?> <br />
								$<?=number_format($grandTotalDataArr[MONTH_EXF_VAL][$key],2);?>
                            </th>
                        <? }
						unset($monthArr);
						unset($yearArr);
						unset($yearMonthDataArr);
						unset($yearMonthDataArr);
						unset($grandTotalDataArr);
						?>
                    </tfoot>



                </tbody>
            </table>



            <br clear="all">
	        <div class="summary_part" style="width: 630px;" id="summary">
	            <table width="630" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="" align="left">
	            	<thead>
	                	<tr>
	                		<th width="30">Sl</th>
	                        <th width="100">Buyer</th>
	                        <th width="100">PO Qty(pcs)</th>
	                        <th width="100">PO Value</th>
	                        <th width="100">Ex-factory Qty</th>
	                        <th width="100">Ex-factory Value</th>
	                        <th width="100">Export CM Value</th>
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
						$po_qnty=$po_data_arr[$buyer_id_ref]['po_quantity'];
						$po_value=$po_data_arr[$buyer_id_ref]['po_value'];
						?>
	                    <tr bgcolor="<? echo $bgcolor; ?>">
	                    	<td><? echo $p;?></td>
	                    	<td><? echo $buyer_arr[$buyer_id_ref]; ?></td>
	                        <td align="right"><? echo number_format($po_qnty,0); ?></td>
	                        <td align="right"><? echo number_format($po_value,0); ?></td>
	                        <td align="right"><? echo number_format($row["ex_factory_qnty"],0); ?></td>
	                        <td align="right"><? echo number_format($row["ex_factory_value"],2); ?></td>
	                        <td align="right"><? echo number_format($row["cm_val"],2); ?></td>
	                    </tr>
	                    <?
						$p++;
						$gt_po_qnty+=$po_qnty;
						$gt_po_val+=$po_value;
						$gt_ex_factory_value+=$row["ex_factory_value"];
						$gt_basic_qnty+=$row["basic_qnty"];
						$gt_cm_val+=$row["cm_val"];
						$gt_commision_cost+=$row["commision_cost"];
						$gt_ex_factory_qnty += $row["ex_factory_qnty"];
					}
					?>
	                </tbody>
	                <tfoot>
	                	<tr>
	                        <th colspan="2">Grand Total:</th>
	                        <th><? echo number_format($gt_po_qnty,0); ?></th>
	                        <th><? echo number_format($gt_po_val,0); ?></th>
	                        <th><? echo number_format($gt_ex_factory_qnty,0); ?></th>
	                        <th><? echo number_format($gt_ex_factory_value,2); ?></th>
	                        <th><? echo number_format($gt_cm_val,2); ?></th>
	                    </tr>
	                </tfoot>
	            </table>
	        </div>
	        <br clear="all">
	        <div class="details_part" style="width: <? echo $width+20;?>px;margin-top: 10px;" id="scroll_body">
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
								<th width="630" colspan="7"><? echo $months[$month_val]; ?></th>
								<?
							}
							else
							{
								?>
								<th width="500" colspan="5"><? echo $months[$month_val]; ?></th>
								<?
							}
							$m++;
	                    }
	                    ?>
	                </tr>
	               <tr>
	                    <th width="30">Sl</th>
	                    <th width="100">Buyer</th>
	                    <?
	                    foreach($result_data_arr as $yearMonth=>$vale)
	                    {
	                        $month_arr=explode("-",$yearMonth);
	                        ?>
	                        <th width="100">PO Qty(pcs)</th>
	                        <th width="100">PO Value</th>
	                        <th width="100">Ex-factory Qty</th>
	                        <th width="100">Ex-factory Value</th>
	                        <th width="100">Export CM Value</th>
	                        <?
	                    }
	                    ?>
	               </tr>
	            </thead>
	         	</table>
		        <table width="<? echo $width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="" align="left">
					<?
		            echo $main_data;
		            ?>
		            <tfoot>
		                <th colspan="2">Total:&nbsp;</th>
		                <?
		                foreach($total_mon_data as $row)
		                {
		                    ?>
		                    <th><? echo number_format($row['po_quantity'],0); ?></th>
		                    <th><? echo number_format($row['po_value'],0); ?></th>
		                    <th><? echo number_format($row['ex_factory_qnty'],0); ?></th>
		                    <th><? echo number_format($row['ex_factory_value'],2); ?></th>
		                    <th><? echo number_format($row['cm_val'],2); ?></th>
		                    <?
		                }
		                ?>
		            </tfoot>
		        </table>
	        </div>
        </div>
		<?
	}
	else if($reportType==10)//New Button Aziz (Show Country)
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
		$all_conds.=($buyer_name)? " and d.buyer_name='$buyer_name'" : " ";
		$all_conds.=($delv_comp)? " and a.delivery_company_id in($delv_comp)" : " ";
		$all_conds.=($location_name)? " and a.delivery_location_id in($location_name)" : " ";
		$all_conds.=($delv_floor)? " and a.delivery_floor_id in($delv_floor)" : " ";
		$all_conds.=($shipping_status)? " and b.shiping_status =$shipping_status" : " ";
		$all_conds.=($cbo_brand_id)? " and d.brand_id =$cbo_brand_id" : " ";
		$all_conds.=($cbo_season_name)? " and d.season_buyer_wise =$cbo_season_name" : " ";
		$all_conds.=($cbo_season_year)? " and d.season_year =$cbo_season_year" : " ";
		$buyer_conds=($buyer_name)? " and a.buyer_name='$buyer_name'" : " ";
		$date_conds="";
		if($txt_date_from!="" && $txt_date_to!="")
		{
			$all_conds.=" and b.ex_factory_date between '$txt_date_from' and  '$txt_date_to ' ";
			$date_conds=" and b.ex_factory_date between '$txt_date_from' and  '$txt_date_to ' ";
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
			$ex_fac_sql="SELECT b.po_break_down_id as po_id,b.country_id,b.delivery_mst_id as challan_id,
			  ( case when b.entry_form<>85 then  b.ex_factory_qnty else 0 end ) as qnty ,
			  ( case when b.entry_form<>85 then  b.total_carton_qnty else 0 end ) as ctn_qnty ,
			  ( case when b.entry_form<>85 then  b.carton_qnty else 0 end ) as ctn_qnty_pcs,
			  ( case when b.entry_form=85 then  b.ex_factory_qnty else 0 end ) as ret_qnty,
			  ( case when b.entry_form=85 then  b.total_carton_qnty else 0 end ) as ret_qnty_ctn ,
			  ( case when b.entry_form=85 then  b.carton_qnty else 0 end ) as ret_qnty_ctn_pcs ,

			  (b.ex_factory_date) as dates ,b.shiping_mode,b.shiping_status,a.lock_no,a.driver_name,a.truck_no,a.dl_no,a.mobile_no,b.remarks from pro_ex_factory_delivery_mst a,pro_ex_factory_mst b,wo_po_details_master d,wo_po_break_down e where a.id=b.delivery_mst_id and d.id=e.job_id and e.id=b.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and d.status_active=1 and e.status_active=1 $all_conds ";//
		}
		else
		{   //total_carton_qnty
			$ex_fac_sql="SELECT b.po_break_down_id as po_id,b.country_id ,b.delivery_mst_id as challan_id,
			( case when b.entry_form<>85 then  c.production_qnty else 0 end ) as qnty ,
			( case when b.entry_form<>85 then  b.total_carton_qnty else 0 end ) as ctn_qnty ,
			( case when b.entry_form<>85 then  b.carton_qnty else 0 end ) as ctn_qnty_pcs,
			( case when b.entry_form=85 then  c.production_qnty else 0 end ) as ret_qnty ,
			( case when b.entry_form=85 then  b.total_carton_qnty else 0 end ) as ret_qnty_ctn ,
			( case when b.entry_form=85 then  b.carton_qnty else 0 end ) as ret_qnty_ctn_pcs ,
			(b.ex_factory_date) as dates,b.shiping_mode,b.shiping_status,a.lock_no,a.driver_name,a.truck_no,a.dl_no,a.mobile_no,b.remarks from pro_ex_factory_delivery_mst a,pro_ex_factory_mst b,pro_ex_factory_dtls c,wo_po_details_master d,wo_po_break_down e where a.id=b.delivery_mst_id and b.id=c.mst_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.id=e.job_id and e.id=b.po_break_down_id and d.status_active=1 and e.status_active=1 $all_conds ";
		}//group by  b.po_break_down_id,b.ex_factory_date,b.country_id,b.shiping_mode,b.shiping_status,a.lock_no,a.driver_name,a.truck_no,a.dl_no,a.mobile_no,a.remarks
		// echo $ex_fac_sql;die;
		$exfac_arrs=sql_select($ex_fac_sql);
		$order_cnty_wise_ex_arr=array();
		$all_po_ex_arr=array();
		foreach($exfac_arrs as $values)
		{
			if($order_cnty_wise_ex_arr[$values[csf("po_id")]][$values[csf("country_id")]]["lock_no"]=="")
			{
				$order_cnty_wise_ex_arr[$values[csf("po_id")]][$values[csf("country_id")]]["lock_no"] =$values[csf("lock_no")];
			}
			else
			{
				$order_cnty_wise_ex_arr[$values[csf("po_id")]][$values[csf("country_id")]]["lock_no"] .=",".$values[csf("lock_no")];
			}
			if($order_cnty_wise_ex_arr[$values[csf("po_id")]][$values[csf("country_id")]]["driver_name"]=="")
			{
				$order_cnty_wise_ex_arr[$values[csf("po_id")]][$values[csf("country_id")]]["driver_name"] =$values[csf("driver_name")];
			}
			else
			{
				$order_cnty_wise_ex_arr[$values[csf("po_id")]][$values[csf("country_id")]]["driver_name"] .=",".$values[csf("driver_name")];
			}
			if($order_cnty_wise_ex_arr[$values[csf("po_id")]][$values[csf("country_id")]]["truck_no"]=="")
			{
				$order_cnty_wise_ex_arr[$values[csf("po_id")]][$values[csf("country_id")]]["truck_no"] =$values[csf("truck_no")];
			}
			else
			{
				$order_cnty_wise_ex_arr[$values[csf("po_id")]][$values[csf("country_id")]]["truck_no"] .=",".$values[csf("truck_no")];
			}
			if($order_cnty_wise_ex_arr[$values[csf("po_id")]][$values[csf("country_id")]]["challan_id"]=="")
			{
				$order_cnty_wise_ex_arr[$values[csf("po_id")]][$values[csf("country_id")]]["challan_id"] =$values[csf("challan_id")];
			}
			else
			{
				$order_cnty_wise_ex_arr[$values[csf("po_id")]][$values[csf("country_id")]]["challan_id"] .=",".$values[csf("challan_id")];
			}
			if($order_cnty_wise_ex_arr[$values[csf("po_id")]][$values[csf("country_id")]]["dl_no"]=="")
			{
				$order_cnty_wise_ex_arr[$values[csf("po_id")]][$values[csf("country_id")]]["dl_no"] =$values[csf("dl_no")];
			}
			else
			{
				$order_cnty_wise_ex_arr[$values[csf("po_id")]][$values[csf("country_id")]]["dl_no"] .=",".$values[csf("dl_no")];
			}
			if($order_cnty_wise_ex_arr[$values[csf("po_id")]][$values[csf("country_id")]]["mobile_no"]=="")
			{
				$order_cnty_wise_ex_arr[$values[csf("po_id")]][$values[csf("country_id")]]["mobile_no"] =$values[csf("mobile_no")];
			}
			else
			{
				$order_cnty_wise_ex_arr[$values[csf("po_id")]][$values[csf("country_id")]]["mobile_no"] .=",".$values[csf("mobile_no")];
			}
			if($order_cnty_wise_ex_arr[$values[csf("po_id")]][$values[csf("country_id")]]["remarks"]=="")
			{
				$order_cnty_wise_ex_arr[$values[csf("po_id")]][$values[csf("country_id")]]["remarks"] =$values[csf("remarks")];
			}
			else
			{
				$order_cnty_wise_ex_arr[$values[csf("po_id")]][$values[csf("country_id")]]["remarks"] .=",".$values[csf("remarks")];
			}

			// $order_cnty_wise_ex_arr[$values[csf("po_id")]][$values[csf("country_id")]]["challan_id"] =$values[csf("challan_id")];
			// $order_cnty_wise_ex_arr[$values[csf("po_id")]][$values[csf("country_id")]]["driver_name"] =$values[csf("driver_name")];
			// $order_cnty_wise_ex_arr[$values[csf("po_id")]][$values[csf("country_id")]]["truck_no"] =$values[csf("truck_no")];
			// $order_cnty_wise_ex_arr[$values[csf("po_id")]][$values[csf("country_id")]]["dl_no"] =$values[csf("dl_no")];
			// $order_cnty_wise_ex_arr[$values[csf("po_id")]][$values[csf("country_id")]]["mobile_no"] =$values[csf("mobile_no")];
			// $order_cnty_wise_ex_arr[$values[csf("po_id")]][$values[csf("country_id")]]["remarks"] =$values[csf("remarks")];
			$order_cnty_wise_ex_arr[$values[csf("po_id")]][$values[csf("country_id")]]["dates"] .=$values[csf("dates")].',';
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




		$order_sql="SELECT a.total_set_qnty, b.id,b.shiping_status ,a.style_description,a.job_no_prefix_num,a.job_no,a.style_ref_no,b.po_number,b.grouping as ref_no,b.file_no,c.country_id,c.country_ship_date,a.order_uom,c.cutup,c.item_number_id,sum(c.order_quantity) as cnty_qnty,b.unit_price,a.ship_mode,a.season_buyer_wise,a.buyer_name from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.job_no=c.job_no_mst and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $all_po_ex_cond $all_country_ex_cond $buyer_conds $internal_ref_cond group by a.total_set_qnty, b.id ,b.grouping,b.file_no,a.job_no_prefix_num,b.shiping_status ,a.job_no,a.style_ref_no,a.style_description,b.po_number,c.item_number_id,c.country_id,c.country_ship_date,a.order_uom,c.cutup,b.unit_price,a.ship_mode,a.season_buyer_wise,a.buyer_name";
		 //echo $order_sql;//die;
		$order_arrs=sql_select($order_sql);
		$ex_factory_data=array();
		foreach($order_arrs as $keys=>$vals)
		{
			$ex_factory_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("id")]][$vals[csf("country_id")]]["cnty_qnty"] +=$vals[csf("cnty_qnty")];
			$ex_factory_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("id")]][$vals[csf("country_id")]]["dates"] =$vals[csf("country_ship_date")];
			$ex_factory_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("id")]][$vals[csf("country_id")]]["cutup"] =$vals[csf("cutup")];
			$ex_factory_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("id")]][$vals[csf("country_id")]]["ref_no"] =$vals[csf("ref_no")];
			$ex_factory_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("id")]][$vals[csf("country_id")]]["file_no"] =$vals[csf("file_no")];
			$ex_factory_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("id")]][$vals[csf("country_id")]]["order_uom"] =$vals[csf("order_uom")];
			$ex_factory_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("id")]][$vals[csf("country_id")]]["item_number_id"] .=$vals[csf("item_number_id")].",";
			$ex_factory_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("id")]][$vals[csf("country_id")]]["style_description"] =$vals[csf("style_description")];
			//$ex_factory_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("id")]][$vals[csf("country_id")]]["unit_price"] =$vals[csf("unit_price")];
			$ex_factory_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("id")]][$vals[csf("country_id")]]["unit_price"] =($vals[csf("unit_price")]/$vals[csf("total_set_qnty")]);
			$ex_factory_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("id")]][$vals[csf("country_id")]]["ship_mode"] =$vals[csf("ship_mode")];
			$ex_factory_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("id")]][$vals[csf("country_id")]]["season_buyer_wise"] =$vals[csf("season_buyer_wise")];
			$ex_factory_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("id")]][$vals[csf("country_id")]]["shiping_status"] =$vals[csf("shiping_status")];
			$ex_factory_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("id")]][$vals[csf("country_id")]]["po_number"] =$vals[csf("po_number")];
			$ex_factory_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("id")]][$vals[csf("country_id")]]["job"] =$vals[csf("job_no_prefix_num")];
			$ex_factory_data[$vals[csf("job_no")]][$vals[csf("style_ref_no")]][$vals[csf("id")]][$vals[csf("country_id")]]["buyer_name"] =$vals[csf("buyer_name")];
			$all_po_ex_curr_arr[$values[csf("po_id")]]=$values[csf("po_id")];
			$all_country_ex_curr_arr[$values[csf("country_id")]]=$values[csf("country_id")];
		}
		$all_po_ex_curr_ids=implode(",", $all_po_ex_curr_arr);
		$all_po_ex_curr_cond="";
		if($db_type==2 &&  count($all_po_ex_curr_ids)>999)
		{
			$po_chunk=array_chunk($all_po_ex_curr_ids, 999);
			foreach($po_chunk as $row)
			{
				$po_ids=implode(",", $row);
				if($all_po_ex_curr_cond=="")
				{
					$all_po_ex_curr_cond.=" and (b.po_break_down_id in ($po_ids)";
				}
				else
				{
					$all_po_ex_curr_cond.=" or b.po_break_down_id in ($po_ids)";
				}

			}
			$all_po_ex_curr_cond.=")";

		}
		else
		{

			$all_po_ex_curr_cond=" and b.po_break_down_id in ($all_po_ex_ids)";
			//echo "<pre>";var_dump($all_po_ex_cond);echo "</pre> in else"; //die;
		}


		 $ex_fac_curr_sql="SELECT b.po_break_down_id as po_id,b.country_id ,
			( case when b.entry_form<>85  then  b.ex_factory_qnty else 0 end ) as qnty ,
			( case when b.entry_form<>85 $date_conds then  b.ex_factory_qnty else 0 end ) as curr_qnty ,
			( case when b.entry_form<>85  $date_conds then  b.total_carton_qnty else 0 end ) as curr_ctn_qnty ,
			( case when b.entry_form<>85  then  b.total_carton_qnty else 0 end ) as ctn_qnty ,
			( case when b.entry_form<>85 $date_conds then  b.carton_qnty else 0 end ) as ctn_qnty_pcs,
			( case when b.entry_form=85  then  b.ex_factory_qnty else 0 end ) as ret_qnty ,
			( case when b.entry_form=85  $date_conds then  b.ex_factory_qnty else 0 end ) as curr_ret_qnty ,
			( case when b.entry_form=85 $date_conds then  b.total_carton_qnty else 0 end ) as ret_qnty_ctn ,
			( case when b.entry_form=85 $date_conds then  b.carton_qnty else 0 end ) as ret_qnty_ctn_pcs ,
			(b.ex_factory_date) as dates,b.shiping_mode,b.shiping_status,a.lock_no,a.driver_name,a.truck_no,a.dl_no,a.mobile_no,a.remarks from pro_ex_factory_delivery_mst a,pro_ex_factory_mst b where a.id=b.delivery_mst_id  and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   $all_po_ex_curr_cond ";
			//echo $ex_fac_curr_sql;
			$exfac_curr_arrs=sql_select($ex_fac_curr_sql);

		$all_po_ex_curr_arr=array();
		foreach($exfac_curr_arrs as $values)
		{
			//$order_cnty_wise_ex_arr[$values[csf("po_id")]][$values[csf("country_id")]]["qnty"]+=$values[csf("qnty")]-$values[csf("ret_qnty")];
			$order_cnty_wise_ex_curr_arr[$values[csf("po_id")]][$values[csf("country_id")]]["curr_qnty"]+=$values[csf("curr_qnty")]-$values[csf("curr_ret_qnty")];
			$order_cnty_wise_ex_curr_arr[$values[csf("po_id")]][$values[csf("country_id")]]["curr_qnty_ctn"]+=$values[csf("curr_ctn_qnty")]-$values[csf("ret_qnty_ctn")];
			$order_cnty_wise_ex_curr_arr2[$values[csf("po_id")]][$values[csf("country_id")]][$values[csf("dates")]]["curr_qnty"]+=$values[csf("qnty")]-$values[csf("ret_qnty")];
			$order_cnty_wise_ex_curr_arr2[$values[csf("po_id")]][$values[csf("country_id")]][$values[csf("dates")]]["curr_qnty_ctn_pcs"]+=$values[csf("ctn_qnty")]-$values[csf("ret_qnty_ctn")];
			$order_cnty_wise_ex_arr[$values[csf("po_id")]][$values[csf("country_id")]]["qnty"]+=$values[csf("qnty")]-$values[csf("ret_qnty")];
			$order_cnty_wise_ex_arr[$values[csf("po_id")]][$values[csf("country_id")]]["curr_qnty_ctn"]+=$values[csf("ctn_qnty")]-$values[csf("ret_qnty_ctn")];
			$order_cnty_wise_ex_arr2[$values[csf("po_id")]][$values[csf("country_id")]]["curr_qnty_ctn"]+=$values[csf("ctn_qnty")]-$values[csf("ret_qnty_ctn")];
			$order_cnty_wise_ex_arr2[$values[csf("po_id")]][$values[csf("country_id")]]["dates"] .=$values[csf("dates")].',';

		}


		ob_start();
		?>
		<script type="text/javascript">
			 //setFilterGrid("table_body",-1)
		</script>
		<div style="width:2200px" >
			<table width="2170"  cellspacing="0"  align="center">
				<tr>
					<td height="11" colspan="29"></td>
				</tr>
				<tr class="form_caption">
					<td colspan="29" align="center" class="form_caption"> <strong style="font-size:15px;">Country Wise Ex-Factory Report</strong></td>
				</tr>
				<tr>
					<td align="center" colspan="29" class="form_caption">
						<strong style="font-size:16px;">Company Name:<? echo  $company_library[$company] ;?></strong>
					</td>
				</tr>
				<tr>
					<td height="11" colspan="29"></td>
				</tr>


			</table>

					<table align="left" width="2170" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1"  >
						<thead>
                        <tr>
		 					<th  rowspan="2" width="30" style="word-wrap: break-word;word-break: break-all;">SL</th>
                            <th  rowspan="2" width="100" style="word-wrap: break-word;word-break: break-all;">Buyer</th>
                            <th  rowspan="2" width="70" style="word-wrap: break-word;word-break: break-all;">Order No</th>
                            <th  rowspan="2" width="70" style="word-wrap: break-word;word-break: break-all;">Internal Ref</th>
                            <th  rowspan="2" width="70" style="word-wrap: break-word;word-break: break-all;">File No</th>
							<th  rowspan="2" width="100" style="word-wrap: break-word;word-break: break-all;">Style</th>
                            <th  rowspan="2" width="100" style="word-wrap: break-word;word-break: break-all;">Style Description</th>
                            <th  rowspan="2" width="100" style="word-wrap: break-word;word-break: break-all;">Gmts Item </th>

							<th  rowspan="2" width="60" style="word-wrap: break-word;word-break: break-all;">Season</th>
                            <th  rowspan="2" width="60" style="word-wrap: break-word;word-break: break-all;">Country</th>
							<th  rowspan="2" width="100" style="word-wrap: break-word;word-break: break-all;">Country Ship Date</th>
							<th rowspan="2"  width="30" style="word-wrap: break-word;word-break: break-all;">Week</th>
							<th  rowspan="2" width="60" style="word-wrap: break-word;word-break: break-all;">Cut Off</th>

							<th  rowspan="2" width="60" style="word-wrap: break-word;word-break: break-all;">Order qty(Pcs)</th>

							<th  rowspan="2" width="70" style="word-wrap: break-word;word-break: break-all;">Last Ex Factory Date</th>
							<th  rowspan="2" width="80" style="word-wrap: break-word;word-break: break-all;">Current EX-Factory Qty</th>
                            <th  rowspan="2" width="80" style="word-wrap: break-word;word-break: break-all;">Total Ex Factory Qty</th>
							<th  rowspan="2" width="95" style="word-wrap: break-word;word-break: break-all;">Excess/ Shortage Qty </th>


							<th  rowspan="2" width="80" style="word-wrap: break-word;word-break: break-all;">Current Ctn </th>
                            <th  rowspan="2" width="80" style="word-wrap: break-word;word-break: break-all;">Total Ctn </th>
							<th  rowspan="2" width="50" style="word-wrap: break-word;word-break: break-all;">Delay</th>
							<th  rowspan="2" width="35" style="word-wrap: break-word;word-break: break-all;">Ship Mode</th>
                            <th  rowspan="2" width="60" style="word-wrap: break-word;word-break: break-all;">Track No</th>
                            <th  rowspan="2" width="60" style="word-wrap: break-word;word-break: break-all;">Lock No</th>
                            <th  width="180" colspan="3" style="word-wrap: break-word;word-break: break-all;">Driver Information</th>

							<th  rowspan="2" width="110" style="word-wrap: break-word;word-break: break-all;">Shipment Status</th>
							<th  rowspan="2" width="" style="word-wrap: break-word;word-break: break-all;">Remark</th>
                            </tr>
                            <tr>
                             <th width="60" style="word-wrap: break-word;word-break: break-all;">Name</th>
                             <th width="60" style="word-wrap: break-word;word-break: break-all;">Mobile No</th>
                             <th width="60" style="word-wrap: break-word;word-break: break-all;">DL No</th>
                            </tr>
						</thead>
					</table>
			<div  style="max-height:225px;float: left; overflow-y:scroll;overflow-x: hidden; width:2190px" id="scroll_body" >

					<table align="left" width="2170" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body">
						  <?
						  $ex_fact_date_range=$txt_date_from."*".$txt_date_to."_". 1;
		                  $i=1;
		                  $gr_order_qnty=0;
		                  $gr_order_fob_value=0;
		                  $gr_ex_fac_qnty=0;
		                  $gr_ex_fac_fob=0;$total_ex_curr_qnty=$total_curr_qnty_ctn=$total_tot_qnty_ctn=$total_order_cnty_wise_ex=0;
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
											$ex_fact_date_range=$txt_date_from."*".$txt_date_to."_". 1;
											$ex_fac_date_max= rtrim($order_cnty_wise_ex_arr[$po_id][$country_id]["dates"],',');
											$ex_fac_dateArr=array_unique(explode(",",$ex_fac_date_max));
											$ex_fac_date=max($ex_fac_dateArr);

										if($order_cnty_wise_ex_curr_arr[$po_id][$country_id]["curr_qnty"])
										{
											$gr_order_qnty+=$rows['cnty_qnty'];
											$gr_order_fob_value+=$rows['cnty_qnty']*$rows['unit_price'];
											$gr_ex_fac_qnty+= $order_cnty_wise_ex_arr[$po_id][$country_id]["qnty"];
											$challan_id=$order_cnty_wise_ex_arr[$po_id][$country_id]["challan_id"];

											$gr_ex_fac_fob+=$order_cnty_wise_ex_arr[$po_id][$country_id]["qnty"]*$rows['unit_price'];

											$curr_qnty=$order_cnty_wise_ex_curr_arr[$po_id][$country_id]["curr_qnty"];
											$curr_qnty_ctn=$order_cnty_wise_ex_curr_arr[$po_id][$country_id]["curr_qnty_ctn"];
											$total_curr_qnty+=$curr_qnty;
											//$curr_qnty_ctn=$curr_qnty_ctn;
											$total_curr_qnty_ctn+=$curr_qnty_ctn;
											$tot_qnty_ctn=$order_cnty_wise_ex_arr[$po_id][$country_id]["curr_qnty_ctn"];
											$total_tot_qnty_ctn+=$tot_qnty_ctn;
											$tot_order_ex_qty=$order_cnty_wise_ex_arr[$po_id][$country_id]["qnty"];
											$total_order_cnty_wise_ex+=$tot_order_ex_qty;

											$lock_no= implode(",", array_unique(explode(",", $order_cnty_wise_ex_arr[$po_id][$country_id]["lock_no"])));
											$driver_name=implode(",", array_unique(explode(",",$order_cnty_wise_ex_arr[$po_id][$country_id]["driver_name"])));
											$truck_no=implode(",", array_unique(explode(",",$order_cnty_wise_ex_arr[$po_id][$country_id]["truck_no"])));
											$dl_no=implode(",", array_unique(explode(",",$order_cnty_wise_ex_arr[$po_id][$country_id]["dl_no"])));
											$mobile_no=implode(",", array_unique(explode(",",$order_cnty_wise_ex_arr[$po_id][$country_id]["mobile_no"])));
											$remarks=implode(",", array_unique(explode(",",$order_cnty_wise_ex_arr[$po_id][$country_id]["remarks"])));

											//a.lock_no,a.driver_name,a.truck_no,a.dl_no,a.mobile_no




			                   	 			?>
			                  	 			<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" >
			                  	 				<td align="center" width="30" style="word-wrap: break-word;word-break: break-all;"><? echo $i;?></td>
                                                <td align="center" width="100" style="word-wrap: break-word;word-break: break-all;"><? echo $buyer_arr[$rows['buyer_name']];?></td>
			                  	 				<td align="center"  width="70" style="word-wrap: break-word;word-break: break-all;"><? echo $rows['po_number'];?></td>
                                                <td align="center"  width="70" style="word-wrap: break-word;word-break: break-all;"><? echo $rows['ref_no'];?></td>
                                                <td align="center"  width="70" style="word-wrap: break-word;word-break: break-all;"><? echo $rows['file_no'];?></td>

			                  	 				<td align="center"  width="100" style="word-wrap: break-word;word-break: break-all;"><? echo $style_id;?></td>
                                                <td align="center"  width="100" style="word-wrap: break-word;word-break: break-all;"><? echo $rows['style_description'];?></td>
                                                 <td align="center"  width="100" style="word-wrap: break-word;word-break: break-all;">
												 <?
												 $itm_id_arr = array_unique(explode(",",chop($rows['item_number_id'],',')));
												 $item_name = "";
												 $item_id = "";
												 foreach($itm_id_arr as $itm_id)
												 {
													 if($item_name == "")
													 {
												  		$item_name = $garments_item[$itm_id];
														$item_id = $itm_id;
													 }
													 else
													 {
														 $item_name .= ", ".$garments_item[$itm_id];
														 $item_id .= ", ".$itm_id;
													 }
												 }
												 echo $item_name;
												  ?>
                                                 </td>
                                                  <td align="center"  width="60" style="word-wrap: break-word;word-break: break-all;"><?  echo $season_arrs[$rows['season_buyer_wise']];?></td>

			                  	 				<td align="center"  width="60" title="Country=<? echo $country_id;?>" style="word-wrap: break-word;word-break: break-all;"><? echo $country_short_arr[$country_id];?></td>
			                  	 				<td align="center"  width="100" style="word-wrap: break-word;word-break: break-all;"><? echo $c_shipdate=change_date_format($rows['dates']);?></td>
			                  	 				<td  align="center" width="30" style="word-wrap: break-word;word-break: break-all;">
			                  	 					<?
			                  	 					$week_de= $no_of_week_for_header[$rows['dates']];

			                  	 					echo $week_de;
			                  	 					?>

			                  	 				</td>

			                  	 				<td align="center"  width="60" style="word-wrap: break-word;word-break: break-all;"><? echo $cut_up_array[$rows['cutup']]; ?></td>

			                  	 				<td   width="60" align="center" style="word-wrap: break-word;word-break: break-all;"><? echo $rows['cnty_qnty'];?></td>
			                  	 				<td  align="center" width="70" style="word-wrap: break-word;word-break: break-all;">



                                                 <a href="##" onclick="openmypage_ex_date('<? echo $company ?>','<? echo $po_id; ?>','<? echo $item_id; ?>','<? echo $ex_fact_date_range; ?>','ex_date_country_popup','<? echo $country_id; ?>','10')"> <? echo $ex_fac_date=change_date_format($ex_fac_date); ?></a></td>
			                  	 				<td   width="80" align="center" style="word-wrap: break-word;word-break: break-all;"><? echo $curr_qnty;?></td>
                                                <td   width="80" align="center" style="word-wrap: break-word;word-break: break-all;"><? echo $tot_order_ex_qty;?></td>
			                  	 				<td   width="95" align="center" title="Tot Ex Fact Qty-Po Qty" style="word-wrap: break-word;word-break: break-all;"><? echo $tot_order_ex_qty-$rows['cnty_qnty'];?></td>

                                                <td  align="right" width="80" style="word-wrap: break-word;word-break: break-all;"><? echo $curr_qnty_ctn; ?></td>
                                                <td align="right"  width="80" style="word-wrap: break-word;word-break: break-all;"><? echo $tot_qnty_ctn;?></td>


			                  	 				<td   align="center" width="50" style="word-wrap: break-word;word-break: break-all;<? echo $background_color;?>">
			                  	 				<?

													$date1=strtotime($c_shipdate);
													$date2=strtotime($ex_fac_date);
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


													echo $delay_count;
			                  	 					$shiping_st=$order_cnty_wise_ex_arr[$po_id][$country_id]["shiping_status"];
        //a.lock_no,a.driver_name,a.truck_no,a.dl_no,a.mobile_no
			                  	 				?>

			                  	 				</td>
			                  	 				<td  align="center" width="35" style="word-wrap: break-word;word-break: break-all;"><?  echo implode(",",array_unique(explode(",",$order_cnty_wise_ex_arr[$po_id][$country_id]['shiping_mode'])));; ?></td>
			                  	 				<td  align="center" width="60" style="word-wrap: break-word;word-break: break-all;"><? echo $truck_no;?></td>
                                                <td  align="center" width="60" style="word-wrap: break-word;word-break: break-all;"><? echo $lock_no;?></td>
                                                 <td  align="center" width="60" style="word-wrap: break-word;word-break: break-all;"><? echo $driver_name;?></td>
                                                  <td  align="center" width="60" style="word-wrap: break-word;word-break: break-all;"><? echo $mobile_no;?></td>
                                                   <td  align="center" width="60" style="word-wrap: break-word;word-break: break-all;"><? echo $dl_no;?></td>

                                                <td  align="center" width="110" style="word-wrap: break-word;word-break: break-all;"><? echo $shiping_st;?></td>
			                  	 				<td  align="center" width="" style="word-wrap: break-word;word-break: break-all;"><? echo $remarks;?></td>

			                  	 			</tr>


			                  	 			<?
			                  	 			$i++;
											$total_short_ex_fact_qty+=$tot_order_ex_qty-$rows['cnty_qnty'];
											$total_ex_curr_qnty+=$curr_qnty;
			                  	 		}

		                  	 		}

		                  	 	}

		                  	 }


		                  }
		                  ?>


					</table>



			</div>

			<table align="left" width="2170" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_footer"  >

				<tfoot>
					<tr>
	 					<th width="30" style="word-wrap: break-word;word-break: break-all;">&nbsp;</th>
                        <th width="100" style="word-wrap: break-word;word-break: break-all;">&nbsp;</th>
	 					<th width="70" style="word-wrap: break-word;word-break: break-all;">&nbsp;</th>
                        <th width="70" style="word-wrap: break-word;word-break: break-all;">&nbsp;</th>
                       <th width="70" style="word-wrap: break-word;word-break: break-all;">&nbsp;</th>

						<th width="100" style="word-wrap: break-word;word-break: break-all;">&nbsp;</th>
                        <th width="100" style="word-wrap: break-word;word-break: break-all;">&nbsp;</th>
                         <th width="100" style="word-wrap: break-word;word-break: break-all;">&nbsp;</th>
                         <th width="60" style="word-wrap: break-word;word-break: break-all;">&nbsp;</th>

						<th width="60" style="word-wrap: break-word;word-break: break-all;">&nbsp;</th>
						<th width="100" style="word-wrap: break-word;word-break: break-all;">&nbsp;</th>
						<th width="30" style="word-wrap: break-word;word-break: break-all;">&nbsp;</th>
						<th width="60" style="word-wrap: break-word;word-break: break-all;"><strong>Grand Total</strong></th>

						<th   id="gr_order_qnty_id" align="right" width="60" style="word-wrap: break-word;word-break: break-all;"><strong><? echo $gr_order_qnty;?></strong></th>
                        <th width="70" style="word-wrap: break-word;word-break: break-all;">&nbsp;</th>

						<th align="right"  id="gr_ex_qnty_id_curr"  width="80" style="word-wrap: break-word;word-break: break-all;"><strong>DDD<? echo $total_ex_curr_qnty;?></strong></th>
                        <th align="right"  id="gr_ex_qnty_id"  width="80" style="word-wrap: break-word;word-break: break-all;"><strong><? echo $gr_ex_fac_qnty;?></strong></th>
						<th align="right"  id="gr_ex_fob_id_short"  width="95" style="word-wrap: break-word;word-break: break-all;"><strong><? echo $total_short_ex_fact_qty;?></strong></th>

                        <th width="80" id="gr_ex_fob_id_ctn_curr" align="right" style="word-wrap: break-word;word-break: break-all;"><strong><? echo $total_curr_qnty_ctn;?></strong></th>
                        <th width="80" id="gr_ex_fob_id_ctn" align="right" style="word-wrap: break-word;word-break: break-all;"><strong><? echo $total_tot_qnty_ctn;?></strong></th>
						<th width="50" style="word-wrap: break-word;word-break: break-all;">&nbsp;</th>
						<th width="35" style="word-wrap: break-word;word-break: break-all;">&nbsp;</th>
                        <th width="60" style="word-wrap: break-word;word-break: break-all;">&nbsp;</th>
                        <th width="60" style="word-wrap: break-word;word-break: break-all;">&nbsp;</th>
                        <th width="60" style="word-wrap: break-word;word-break: break-all;">&nbsp;</th>
                        <th width="60" style="word-wrap: break-word;word-break: break-all;">&nbsp;</th>
                         <th width="60" style="word-wrap: break-word;word-break: break-all;">&nbsp;</th>
						<th width="110" style="word-wrap: break-word;word-break: break-all;">&nbsp;</th>
						<th width="" style="word-wrap: break-word;word-break: break-all;">&nbsp;</th>


					</tr>

				</tfoot>
			</table>

		</div>

		<?

	}
	else if($reportType==11) //Details 4
	{
		$exfact_sql=sql_select("select po_break_down_id,additional_info_id,
		sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty,
		sum(total_carton_qnty) as carton_qnty from pro_ex_factory_mst where status_active=1 and is_deleted=0 group by po_break_down_id,additional_info_id");
		$exfact_qty_arr=$exfact_return_qty_arr=$exfact_cartoon_arr=array();
		foreach($exfact_sql as $row)
		{
			$exfact_qty_arr[$row[csf("po_break_down_id")]]+=$row[csf("ex_factory_qnty")]-$row[csf("ex_factory_return_qnty")];
			$exfact_cartoon_arr[$row[csf("po_break_down_id")]]+=$row[csf("carton_qnty")];
			$exfact_return_qty_arr[$row[csf("po_break_down_id")]]+=$row[csf("ex_factory_return_qnty")];

			$additional_info_id_arr[$row[csf("po_break_down_id")]][$row[csf("additional_info_id")]]=$row[csf("additional_info_id")];

		}
		//print_r($additional_info_id_arr);

		$inspection_date_arr=return_library_array( "select po_break_down_id, max(inspection_date) as inspection_date from pro_buyer_inspection where status_active=1 and is_deleted=0 group by po_break_down_id", "po_break_down_id", "inspection_date");
		$invoice_array=return_library_array( "select id,invoice_no from com_export_invoice_ship_mst", "id", "invoice_no"  );
		//$shipping_mode_array=return_library_array( "select id,shipping_mode from com_export_invoice_ship_mst", "id", "shipping_mode"  );
		$lc_sc_type_arr=return_library_array( "select id,is_lc from com_export_invoice_ship_mst", "id", "is_lc"  );
		$lc_sc_id_array=return_library_array( "select id,lc_sc_id from com_export_invoice_ship_mst", "id", "lc_sc_id"  );
		$lc_num_arr=return_library_array( "select id,export_lc_no from com_export_lc", "id", "export_lc_no"  );
		$sc_num_arr=return_library_array( "select id,contract_no from com_sales_contract", "id", "contract_no"  );

		$forwarder_arr=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name");
		$costing_per_arr = return_library_array("select job_no, costing_per from wo_pre_cost_mst","job_no","costing_per");
		$tot_cost_arr = return_library_array("select job_no, cm_for_sipment_sche from wo_pre_cost_dtls","job_no","cm_for_sipment_sche");

		$user_arr=return_library_array( "select id, user_name from user_passwd", 'id', 'user_name' );

		$challan_mst_arr=array();
		$challan_sql="select a.id, a.sys_number_prefix_num,a.sys_number, a.forwarder, a.truck_no, a.mobile_no, b.po_break_down_id,a.lock_no,a.do_no,b.remarks from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
		//echo $challan_sql;
		$challan_sql_result=sql_select($challan_sql);
		foreach($challan_sql_result as $row)
		{
			//$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['challan']=$row[csf("sys_number_prefix_num")];
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['challan']=$row[csf("sys_number")];
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['forwarder']=$row[csf("forwarder")];
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['truck_no']=$row[csf("truck_no")];
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['mobile_no']=$row[csf("mobile_no")];
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['lock_no']=$row[csf("lock_no")];
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['do_no']=$row[csf("do_no")];
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['remarks']=$row[csf("remarks")];
		}

		$width=4165;

		$details_report .='<table width="'.$width.'" cellspacing="0" cellpadding="0"  border="1"  class="rpt_table" rules="all" id="table_body2">';
		$i=1;


		if($db_type==0)
		{
			$sql= "SELECT b.id as po_id, b.grouping, max(a.lc_sc_no) as lc_sc_arr_no, group_concat(distinct a.invoice_no) as invoice_no, group_concat(distinct a.item_number_id) as itm_num_id, max(a.INSERT_DATE) as INSERT_DATE,max(a.INSERTED_BY) as INSERTED_BY,

			sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
			sum(a.total_carton_qnty) as carton_qnty, max(a.ex_factory_date)  as ex_factory_date, group_concat(distinct  a.lc_sc_no) as lc_sc_no, group_concat(distinct a.delivery_mst_id) as challan_id,  b.shipment_date, b.po_number, (b.po_quantity*c.total_set_qnty) as po_quantity, (b.unit_price/c.total_set_qnty) as unit_price, b.shiping_status, c.id, c.company_name, c.buyer_name, c.job_no_prefix_num,c.job_no, YEAR(c.insert_date) as year, c.style_ref_no, c.style_description, c.set_smv,c.total_set_qnty,
			group_concat(distinct a.shiping_mode) as shiping_mode

			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name like '$cbo_company_name' $str_cond $buyer_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,3) and c.is_deleted=0 and c.status_active=1
			group by
					b.id, b.grouping, b.shipment_date, b.po_number, b.unit_price, c.id, c.company_name, c.buyer_name, c.style_ref_no, c.style_description, c.set_smv
			order by a.ex_factory_date ASC";
			//c.buyer_name, b.shipment_date,
		}
		else if($db_type==2)
		{
			 $sql= "SELECT b.id as po_id, b.grouping, max(a.lc_sc_no) as lc_sc_arr_no,max(a.INSERT_DATE) as INSERT_DATE,max(a.INSERTED_BY) as INSERTED_BY,
			LISTAGG(CAST( a.invoice_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.invoice_no) as invoice_no,
			LISTAGG(CAST( a.item_number_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.item_number_id) as itm_num_id,
			sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
			sum(a.total_carton_qnty) as carton_qnty, max(a.ex_factory_date) as ex_factory_date,
			LISTAGG(CAST( a.lc_sc_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.lc_sc_no) as lc_sc_no,
			LISTAGG(CAST( a.delivery_mst_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.delivery_mst_id) as challan_id,
			b.shipment_date, b.po_number, (b.po_quantity*c.total_set_qnty) as po_quantity,(b.unit_price/c.total_set_qnty) as unit_price,b.shiping_status,
			c.id, c.company_name, c.buyer_name, c.job_no_prefix_num,c.job_no, to_char(c.insert_date,'YYYY') as year, c.style_ref_no, c.style_description, c.set_smv,c.total_set_qnty,
			LISTAGG(CAST( a.shiping_mode AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.shiping_mode) as shiping_mode

			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name like '$cbo_company_name' $str_cond $buyer_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,3) and c.is_deleted=0 and c.status_active=1
			group by
					b.id, b.grouping, b.shipment_date, b.po_number, b.unit_price,b.po_quantity,b.shiping_status,c.total_set_qnty,c.id,c.company_name, c.buyer_name, c.job_no_prefix_num,c.job_no, c.insert_date, c.style_ref_no, c.style_description,c.total_set_qnty, c.set_smv
			order by max(a.ex_factory_date) asc";
		}
		//c.buyer_name, b.shipment_date ASC, max(a.ex_factory_date) asc

		//echo $sql;
		$sql_result=sql_select($sql);
		//print_r($sql_result);die;
		foreach($sql_result as $row)
		{

			$costing_per=$costing_per_arr[$row[csf('job_no')]];
			if($costing_per==1) $dzn_qnty=12;
			else if($costing_per==3) $dzn_qnty=12*2;
			else if($costing_per==4) $dzn_qnty=12*3;
			else if($costing_per==5) $dzn_qnty=12*4;
			else $dzn_qnty=1;
			$dzn_qnty=$dzn_qnty*$row[csf('total_set_qnty')];
			$cm_per_pcs=$tot_cost_arr[$row[csf('job_no')]]/$dzn_qnty;

			//$last_ex_factory_date=return_field_value(" max(ex_factory_date) as ex_factory_date","pro_ex_factory_mst","po_break_down_id in(".$row[csf('po_id')].")","ex_factory_date");
			$ex_fact_date_range=$txt_date_from."*".$txt_date_to."_". 1;
			$total_exface_qnty=$txt_date_from."*".$txt_date_to."_". 2;
			if($break_id==0) $break_id=$row[csf('po_id')]; else $break_id=$break_id.",".$row[csf('po_id')];
			if($sc_lc_id==0) $sc_lc_id=$row[csf('lc_sc_no')]; else $sc_lc_id=$sc_lc_id.",".$row[csf('lc_sc_no')];
			//$lc_type=return_field_value("is_lc","com_export_invoice_ship_mst","id in(".$row[csf('invoice_no')].")","is_lc");
			$invoce_id_arr=array_unique(explode(",",$row[csf('invoice_no')]));
			$challan_id=array_unique(explode(",",$row[csf("challan_id")]));
			$challan_no=""; $forwarder=""; $vehi_no=""; $mobile_no="";
			$lock_no=$do_no=$remarks="";

			$diff=($row[csf('shiping_status')]!=3)?datediff("d",$current_date, $row[csf("shipment_date")])-1:datediff("d",$row[csf("ex_factory_date")], $row[csf("shipment_date")])-1;// Count Days in Hand Update By REZA;


			foreach($challan_id as $val)
			{
				//echo $val;
				if($challan_no=="") $challan_no=$challan_mst_arr[$val][$row[csf('po_id')]]['challan']; else $challan_no.='***'.$challan_mst_arr[$val][$row[csf('po_id')]]['challan'];
				if($forwarder=="") $forwarder=$forwarder_arr[$challan_mst_arr[$val][$row[csf('po_id')]]['forwarder']]; else $forwarder.='***'.$forwarder_arr[$challan_mst_arr[$val][$row[csf('po_id')]]['forwarder']];
				if($vehi_no=="") $vehi_no=$challan_mst_arr[$val][$row[csf('po_id')]]['truck_no']; else $vehi_no.='***'.$challan_mst_arr[$val][$row[csf('po_id')]]['truck_no'];
				if($mobile_no=="") $mobile_no=$challan_mst_arr[$val][$row[csf('po_id')]]['mobile_no']; else $mobile_no.='***'.$challan_mst_arr[$val][$row[csf('po_id')]]['mobile_no'];

				if($lock_no=="") $lock_no=$challan_mst_arr[$val][$row[csf('po_id')]]['lock_no']; else $lock_no.='***'.$challan_mst_arr[$val][$row[csf('po_id')]]['lock_no'];
				if($do_no=="") $do_no=$challan_mst_arr[$val][$row[csf('po_id')]]['do_no']; else $do_no.='***'.$challan_mst_arr[$val][$row[csf('po_id')]]['do_no'];
				if($remarks=="") $remarks=$challan_mst_arr[$val][$row[csf('po_id')]]['remarks']; else $remarks.='***'.$challan_mst_arr[$val][$row[csf('po_id')]]['remarks'];
			}

			$challan_no=implode(",", array_filter(array_unique(explode("***", $challan_no))));
			$forwarder=implode(",", array_filter(array_unique(explode("***", $forwarder))));
			$vehi_no=implode(",", array_filter(array_unique(explode("***", $vehi_no))));
			$mobile_no=implode(",", array_filter(array_unique(explode("***", $mobile_no))));
			$lock_no=implode(",", array_filter(array_unique(explode("***", $lock_no))));
			$do_no=implode(",", array_filter(array_unique(explode("***", $do_no))));
			$remarks=implode(",", array_filter(array_unique(explode("***", $remarks))));



			$tot_cbm=0;
			foreach($additional_info_id_arr[$row[csf('po_id')]] as $addStr){
				list($truck_type,$trans_type,$sizes,$chassis_no,$courier_name,$cbm)=explode('___',$addStr);
				$tot_cbm+=($cbm*1);
			}


			//echo $additional_info_id_arr[$row[csf('po_id')]];

			$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
			$comapny_id=$row[csf("company_name")];

			$onclick=" change_color('tr_".$i."','".$bgcolor."')";
			$details_report .='<tr bgcolor="'.$bgcolor.'" onclick="'.$onclick.'" id="tr_'.$i.'">';
			$details_report .='<td width="40" align="center">'.$i.'</td>
								<td width="60" align="center" ><p>'.$row[csf("job_no_prefix_num")].'</p></td>
								<td width="60" align="center" ><p>'.$row[csf("year")].'</p></td>
								<td width="100" align="center" ><p>'.$buyer_arr[$row[csf("buyer_name")]].'</p></td>
								<td width="110" align="center"><p>'.$row[csf("po_number")].'</p></td>
								<td width="80" align="center"><p>'.$row[csf("grouping")].'</p></td>
								<td width="120" align="center"><p>'.$challan_no.'</p></td>
								<td width="100" align="center"><p>';
								$inv_id=""; $lc_sc_no=""; $ship_mode="";
								foreach($invoce_id_arr as $invoice_id)
								{
									if($inv_id=="") $inv_id=$invoice_array[$invoice_id]; else $inv_id=$inv_id.",".$invoice_array[$invoice_id];
									/*if($ship_mode=="") $ship_mode=$shipment_mode[$shipping_mode_array[$invoice_id]]; else $ship_mode=$ship_mode.",".$shipment_mode[$shipping_mode_array[$invoice_id]];*/
									$ship_mode_arr=array();
									foreach(explode(',',$row[csf("shiping_mode")]) as $sm){
										$ship_mode_arr[$sm]=$shipment_mode[$sm];
									}
									$ship_mode=implode(',',$ship_mode_arr);


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
								<td width="100"><p>'.$row[csf("style_ref_no")].'</p></td>
								<td width="100"><p>'.$row[csf("style_description")].'</p></td>
								<td width="110" align="center"><p>';//$garments_item
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

								$total_ex_fact_qty=$exfact_qty_arr[$row[csf("po_id")]];
								$total_cartoon_qty=$exfact_cartoon_arr[$row[csf("po_id")]];
								$po_quantity=$row[csf("po_quantity")];
								$unit_price=$row[csf("unit_price")];
								$current_ex_Fact_Qty=$row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]];
								$basic_qnty=($total_ex_fact_qty*$row[csf("set_smv")])/$basic_smv_arr[$row[csf("company_name")]];
								$cm_per_pcs_tot+=($cm_per_pcs*$current_ex_Fact_Qty);

								$excess_shortage_qty=$po_quantity-$total_ex_fact_qty;
								$excess_shortage_value=$excess_shortage_qty*$unit_price;


			$details_report .=$item_name_all.'</p></td>
								<td width="80" align="center"><p>'.$row[csf("set_smv")].'</p></td>
								<td width="70" align="center"><p>'.change_date_format($row[csf("shipment_date")]).'</p></td>
								<td width="70" align="center"><a href="##" onclick="openmypage_ex_date('.$comapny_id.",'".$row[csf('po_id')]."','".$row[csf('item_number_id')]."','".$ex_fact_date_range."','ex_date_popup','".implode(',',$challan_id)."',11".')">'.change_date_format($row[csf('ex_factory_date')]).'</a></td>
								<td width="70" align="center"><p>'.$ship_mode.'</p></td>
								<td width="60" align="center"><p>'.$diff.'</p></td>
								<td width="80" align="right"><p>'. number_format($po_quantity,0,'', '').'</p></td>
								<td width="70" align="right"><p>'. number_format($unit_price,4).'</p></td>
								<td width="100" align="right"><p>'. number_format(($po_quantity*$unit_price),2).'</p></td>
								<td width="80" align="right"><p><a href="##" onclick="openmypage_ex_date('.$comapny_id.",'".$row[csf('po_id')]."','".$row[csf('item_number_id')]."','".$ex_fact_date_range."','ex_date_popup','".implode(',',$challan_id)."',11".')">'. number_format($current_ex_Fact_Qty,0,'.', '').'</a></p></td>
								<td width="100" align="right"><p>'. number_format( $current_ex_fact_value=$current_ex_Fact_Qty*$unit_price,2).'</p></td>
								<td width="80" align="right"><p>'. number_format($row[csf("carton_qnty")],0,'.', '').'</p></td>
								<td width="80" align="right"><p><a href="##" onclick="openmypage_ex_date('.$comapny_id.",'".$row[csf('po_id')]."','".$row[csf('item_number_id')]."','".$total_exface_qnty."','ex_date_popup','".implode(',',$challan_id)."',11".')">'.number_format($total_ex_fact_qty,0,'.', '').'</a></p></td>
								<td width="100" align="right"><p>'. number_format($total_ex_fact_value=$total_ex_fact_qty*$unit_price,2).'</p></td>
								<td width="80" align="right"><p>'.number_format($total_cartoon_qty,0,'.', '').'</p></td>
								<td width="100" align="right" title="Current Ex.Qty*SMV"><p>'. number_format($total_sales_minutes=$current_ex_Fact_Qty*$row[csf("set_smv")]).'</p></td>


								<td width="80" align="right"><p>'.number_format($basic_qnty,0,'','').'</p></td>

								<td width="80" align="right"><p>'.number_format($excess_qty=($row[csf('shiping_status')]==3 && ($total_ex_fact_qty-$po_quantity)>0)?$total_ex_fact_qty-$po_quantity:'',0,'','').'</p></td>
								<td width="80" align="right"><p>'.number_format($excess_val=($row[csf('shiping_status')]==3 && ($excess_qty*$unit_price)>0)?($excess_qty*$unit_price):'',0,'','').'</p></td>
								<td width="80" align="right"><p>'.number_format($shortage_qty=($row[csf('shiping_status')]==3 && ($po_quantity-$total_ex_fact_qty)>0)?($po_quantity-$total_ex_fact_qty):'',0,'','').'</p></td>
								<td width="80" align="right"><p>'.number_format($shortage_val=($row[csf('shiping_status')]==3 && ($shortage_qty*$unit_price)>0)?($shortage_qty*$unit_price):'',0,'','').'</p></td>
								<td width="80" align="right"><p>'.number_format($balance_qty=($row[csf('shiping_status')]!=3 && ($po_quantity-$total_ex_fact_qty)>0)?($po_quantity-$total_ex_fact_qty):'',0,'','').'</p></td>
								<td width="80" align="right"><p>'.number_format($balance_val=($row[csf('shiping_status')]!=3 && ($balance_qty*$unit_price))?($balance_qty*$unit_price):'',0,'','').'</p></td>



								<td align="center" width="80"><p>'. number_format($total_ex_fact_qty_parcent=($total_ex_fact_qty/$po_quantity)*100,0).'</p></td>

								<td width="80" align="right"><p>'.$tot_cbm.'</p></td>


								<td width="120" align="right" style="word-break:break-all">'.$lock_no.'</td>
								<td width="120" align="right" style="word-break:break-all">'.$do_no.'</td>
								<td width="120" align="right" style="word-break:break-all">'.$remarks.'</td>
								<td width="100" align="center"><p>'.$forwarder.'</p></td>
								<td width="80" align="center"><p>'.$vehi_no.'</p></td>
								<td width="80" align="center"><p>'.$mobile_no.'</p></td>
								<td width="70" align="center"><p>'.(change_date_format($inspection_date_arr[$row[csf('po_id')]]) == '0000-00-00' || change_date_format($inspection_date_arr[$row[csf('po_id')]]) == '' ? '' : change_date_format(change_date_format($inspection_date_arr[$row[csf('po_id')]]))).'</p></td>
								<td width="80" align="center"><p>'.$shipment_status[$row[csf('shiping_status')]].'</p></td>

								<td width="80" align="center"><p>'.$user_arr[$row[INSERTED_BY]].'</p></td>
								<td align="center"><p>'.$row[INSERT_DATE].'</p></td>


							</tr>';



			$master_data[$row[csf("buyer_name")]]['b_id']=$row[csf("buyer_name")];
			$master_data[$row[csf("buyer_name")]]['po_qnty'] +=$row[csf("po_quantity")];
			$master_data[$row[csf("buyer_name")]]['po_value'] +=$row[csf("po_quantity")]*$row[csf("unit_price")];
			$master_data[$row[csf("buyer_name")]]['basic_qnty'] +=$basic_qnty;
			$master_data[$row[csf("buyer_name")]]['ex_factory_qnty'] +=$row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]];
			$master_data[$row[csf("buyer_name")]]['ex_factory_value'] +=($row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]])*$row[csf("unit_price")];
			$master_data[$row[csf("buyer_name")]]['total_ex_fact_qty'] +=$total_ex_fact_qty;
			$master_data[$row[csf("buyer_name")]]['total_ex_fact_value'] +=$total_ex_fact_qty*$row[csf("unit_price")];

			$total_po_qty+=$row[csf("po_quantity")];
			$total_basic_qty+=$basic_qnty;
			$total_po_valu+=$row[csf("po_quantity")]*$row[csf("unit_price")];
			$total_ex_qty+=$row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]];
			$total_crtn_qty+=$row[csf("carton_qnty")];
			$total_ex_valu=($row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]])*$row[csf("unit_price")];
			$g_total_ex_qty+=$total_ex_fact_qty;
			$g_total_ex_crtn+=$total_cartoon_qty;
			$g_total_ex_val+=$total_ex_fact_qty*$row[csf("unit_price")];
			$g_sales_minutes+=$current_ex_Fact_Qty*$row[csf("set_smv")];
			$total_eecess_storage_qty+=$excess_shortage_qty;
			$total_eecess_storage_val+=$excess_shortage_value;


			$total_eecess_qty+=$excess_qty;
			$total_eecess_val+=$excess_val;
			$total_storage_qty+=$shortage_qty;
			$total_storage_val+=$shortage_val;
			$total_balance_qty+=$balance_qty;
			$total_balance_val+=$balance_val;
			$total_cbm+=$tot_cbm;

			$i++;$item_name_all="";
		}



		$details_report .='
						</table>';

		foreach($master_data as $rows)
		{
			$total_po_val+=$rows[po_value];
		}

		?>
        <div style="width:3250x;">
            <div style="width:1120px" id="summary">
                <table width="1090"  cellspacing="0"  align="center">
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
                    <table width="1090" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" >
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

	                        <th >Total Ex-Fact. Value %</th>
	                    </thead>
	                <tbody>
			                <?
				                $m=1;
				                foreach($master_data as $rows)
				                {
				                    $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
				                     ?>
				                  	<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" >
				                        <td width="40" align="center"><? echo $m; ?></td>
				                        <td width="130"><p><? echo $buyer_arr[$rows[b_id]]; ?></p></td>
				                        <td width="100" align="right">
				                        	<p><?  $po_quantity=$rows[po_qnty];echo number_format($po_quantity,0); $total_buyer_po_quantity+=$po_quantity; ?></p>
				                        </td>
				                        <td width="130" align="right" >
				                        	<p  id="value_<? echo $i ; ?>"><? $buyer_po_value=$rows[po_value]; echo number_format($buyer_po_value,2 ,'.', '');  $total_buyer_po_value+=$buyer_po_value; ?></p>
				                        </td>
				                        <td width="100" align="right">
				                         	<? echo number_format(($buyer_po_value/$total_po_val)*100,2,'.','');$parcentages+=($buyer_po_value/$total_po_val)*100; ?>
				                        </td>
				                        <td width="100" align="right">
				                        	<p>
				                        		<?
						                         $current_ex_Fact_Qty=$rows[ex_factory_qnty];  echo number_format($current_ex_Fact_Qty,0,'',''); $total_current_ex_Fact_Qty +=$current_ex_Fact_Qty;
						                        ?>

					                        </p>
				                        </td>
				                        <td width="130" align="right">
					                        <p>
					                        	<?
						                        $current_ex_fact_value=$rows[ex_factory_value]; echo number_format($current_ex_fact_value,2,'.',''); $total_current_ex_fact_value+=$current_ex_fact_value;
						                        ?>

					                        </p>
				                        </td>
				                        <td align="right" width="100">
					                        <p>
					                        	<?
						                         	$total_ex_fact_qty=$rows[total_ex_fact_qty]; echo number_format($total_ex_fact_qty,0,'',''); $mt_total_ex_fact_qty+=$total_ex_fact_qty;
						                        ?>

					                        </p>
				                        </td>
				                        <td align="right" width="130">
					                        <p>
					                        	<?
						                         $total_ex_fact_value=$rows[total_ex_fact_value];  echo  number_format($total_ex_fact_value,2,'.',''); $mt_total_ex_fact_value+=$total_ex_fact_value;
						                        ?>
					                        </p>
				                        </td>

				                        <td align="right">
					                        <p>
					                        	<?
							                        $total_ex_fact_value_parcentage=($total_ex_fact_value/$buyer_po_value)*100;
							                        echo number_format($total_ex_fact_value_parcentage,0)
						                        ?> %
						                    </p>
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
	                </tbody>
                    <input type="hidden" name="total_i" id="total_i" value="<? echo $i; ?>" />
                    <tfoot>
                        <th align="right" colspan="2"><b>Total:</b></th>
                        <th  align="right" id="total_buyer_po_quantity"><? echo number_format($total_buyer_po_quantity,0);  ?></th>
                        <th  align="right" id="value_total_buyer_po_value"><? echo number_format($total_buyer_po_value,2 ,'.', ''); ?> </th>
                        <th align="right" id="parcentages"><? echo ceil($parcentages); ?></th>
                        <th  align="right" id="total_current_ex_Fact_Qty"><? echo number_format($total_current_ex_Fact_Qty,0); ?></th>
                        <th  align="right" id="value_total_current_ex_fact_value"><? echo  number_format($total_current_ex_fact_value,2); ?></th>
                        <th align="right" id="mt_total_ex_fact_qty"><? echo number_format($mt_total_ex_fact_qty,0); ?></th>
                        <th align="right" id="value_mt_total_ex_fact_value"><? echo number_format($mt_total_ex_fact_value,2); ?></th>

                        <th align="right"></th>
                    </tfoot>
                </table>
            </div>
            <br />

            <div>
                <table width="<?= $width;?>"  >
                    <tr>
                    <td colspan="41" class="form_caption"><strong style="font-size:16px;">Shipped Out Order Details Report</strong></td>
                    </tr>
                </table>
                <table width="<?= $width;?>" border="1" class="rpt_table" rules="all" id="table_header_2">
                    <thead>
                        <th width="40">SL</th>
                        <th width="60">Job</th>
                        <th width="60">Year</th>
                        <th width="100">Buyer Name</th>
                        <th width="110">Order NO</th>
                        <th width="80">Internal Ref.</th>
                        <th width="120">Challan NO</th>
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
                        <th width="80">PO Qtny. (pcs)</th>
                        <th width="70">Unit Price</th>
                        <th width="100">PO Value</th>
                        <th width="80">Current Ex-Fact. Qty (pcs)</th>
                        <th width="100">Current Ex-Fact. Value</th>
                        <th width="80">Current cartoon Qty</th>
                        <th width="80">Total Ex-Fact. Qty.</th>
                        <th width="100">Total Ex-Fact. Value</th>
                        <th width="80">Total cartoon Qty</th>
                        <th width="100">Sales Minute</th>
                        <th width="80">Total Ex-Fact. (Basic Qty)</th>

                        <th width="80">Excess Qty</th>
                        <th width="80">Excess Value</th>
                        <th width="80">Shortage Qty</th>
                        <th width="80">Shortage Value</th>
                        <th width="80">Balance Qty</th>
                        <th width="80">Balance Value</th>

                        <th width="80">Total Ex-Fact. Qty. %</th>
                        <th width="80">Total CBM</th>
                        <th width="120">Lock No</th>
                        <th width="120">DO No</th>
                        <th width="120">Remarks</th>
                        <th width="100">C & F Name</th>
                        <th width="80">Vehicle No</th>
                        <th width="80">Car Mobile No</th>
                        <th width="70">Inspaction Date</th>
                        <th width="80">Ex-Fact Status</th>
                        <th width="80">Insert User Name</th>
                        <th>Insert Date and time</th>



                    </thead>
                </table>
            <div style="width:<?= $width+20;?>px; overflow-y:scroll; overflow-x:hidden; max-height:300px;"  id="scroll_body" >
            <? echo $details_report; ?>
            <table width="<?= $width;?>" cellspacing="0" cellpadding="0"  border="1"  class="rpt_table" rules="all" id="table_footer">
                <tfoot>
                    <tr>
                    	<th width="40">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="60" align="right"><strong>Total</strong></th>
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

                        <th width="80" id="total_eecess_qty"><?= $total_eecess_qty;?></th>
                        <th width="80" id="total_eecess_val"><?= $total_eecess_val;?></th>
                        <th width="80" id="total_storage_qty"><?= $total_storage_qty;?></th>
                        <th width="80" id="total_storage_val"><?= $total_storage_val;?></th>
                        <th width="80" id="total_balance_qty"><?= $total_balance_qty;?></th>
                        <th width="80" id="total_balance_val"><?= $total_balance_val;?></th>


                        <th width="80">&nbsp;</th>
                        <th width="80" id="total_cbm"><?= $total_cbm;?></th>
                        <th width="120" align="right" ></th>
                        <th width="120" align="right" ></th>
                        <th width="120" align="right"></th>
                        <th width="100">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th>&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
            </div>
            </div>
        </div>

		<?
	}
	else if($reportType==12) // Monthly 3 Button
	{
		$condition= new condition();

		$target_basic_qnty=array();
		$month_id_start = date('m',strtotime($txt_date_from));
		$month_id_end = date('m',strtotime($txt_date_to));
		$year_id_start = date('Y',strtotime($txt_date_from));
		$year_id_end = date('Y',strtotime($txt_date_to));
		$month_date_cond="";

		// $total_fabric_amt=$total_yarn_costing=$total_conversion_cost=$total_trims_amt=$total_embl_amt=$total_comercial_amt=$total_commisssion=$total_wash_costing=$total_cm_cost=$total_lab_test_cost=$total_inspection_cost=$total_currier_cost=$total_certificate_cost=$total_common_oh_cost=$total_freight_cost=0;


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

		$sql_con = "SELECT b.buyer_id, d.month_id, a.year_id, SUM((d.capacity_month_pcs* b.allocation_percentage)/100) AS cap_qnty FROM lib_capacity_allocation_mst a,lib_capacity_allocation_dtls b, lib_capacity_calc_mst c, lib_capacity_year_dtls d
		WHERE a.id=b.mst_id AND a.year_id=c.year AND a.month_id=d.month_id AND c.id=d.mst_id $company_cond AND $month_date_cond AND a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $buyer_conds2
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

		$sql= "SELECT b.id as po_id, (b.unit_price/c.total_set_qnty) as unit_price, c.total_set_qnty, c.id as job_id, c.job_no, c.buyer_name, c.company_name, c.set_smv, a.ex_factory_qnty as ex_factory_qnty,a.ex_factory_date, c.id as po_dtls_id
		from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c,pro_ex_factory_delivery_mst d
		where a.po_break_down_id=b.id and b.job_no_mst=c.job_no   $del_location_cond $del_floor_cond $del_comp_cond  $str_cond $company_cond2 $buyer_conds $internal_ref_cond $search_cond and  a.entry_form!=85  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active  in(1,2,3) and c.is_deleted=0 and c.status_active  in(1,2,3) and a.delivery_mst_id= d.id
		order by a.ex_factory_date ASC ";//c.job_no

		// echo $sql;

		//PRO_EX_FACTORY_DELIVERY_MST

		// echo $sql;
		$sql_result=sql_select($sql);
		//print_r($sql_result);die;
		$po_dtls_id_arr = array();
		$po_id_arr = array();
		foreach($sql_result as $row)
		{
			$cm_val=0;
			//if(!in_array($row[csf('job_no')],$temp_arr)){

				$costing_per=$cm_value=$cm_value_rate=0;
				if($costing_per_arr[$row[csf('job_no')]]==1) $costing_per=12;
				else if($costing_per_arr[$row[csf('job_no')]]==3) $costing_per=12*2;
				else if($costing_per_arr[$row[csf('job_no')]]==4) $costing_per=12*3;
				else if($costing_per_arr[$row[csf('job_no')]]==5) $costing_per=12*4;
				else $costing_per=1;

				$costing_per_commission=$costing_per*$row[csf('total_set_qnty')];
				$commision_per_pic=$tot_commision_rate_arr[$row[csf('job_no')]]/$costing_per_commission;
				// echo "$commision_per_pic=".$tot_commision_rate_arr[$row[csf('job_no')]]."/$costing_per<br>";

				/*$dzn_qnty=$dzn_qnty*$row[csf('total_set_qnty')];
				$cm_value_rate=($tot_cost_arr[$row[csf('job_no')]]/$dzn_qnty);*/

				//$temp_arr[]=$row[csf('job_no')];

			//}

			$exfactreturn_qty=$ex_factory_qty_arr[$row[csf('po_id')]]['return_qty'];
			$basic_qnty=($row[csf("ex_factory_qnty")]*$row[csf("set_smv")])/$basic_smv_arr[$row[csf("company_name")]];
			/*$cm_val=$cm_value_rate*$row[csf("ex_factory_qnty")];
			$result_data_arr[date("Y-m",strtotime($row[csf("ex_factory_date")]))][$row[csf("buyer_name")]]['cm_value'] +=$cm_val;*/
			$result_data_arr[date("Y-m",strtotime($row[csf("ex_factory_date")]))][$row[csf("buyer_name")]]['ex_factory_qnty'] +=$row[csf("ex_factory_qnty")]-$exfactreturn_qty;
			$result_data_arr[date("Y-m",strtotime($row[csf("ex_factory_date")]))][$row[csf("buyer_name")]]['basic_qnty'] +=$basic_qnty;
			$result_data_arr[date("Y-m",strtotime($row[csf("ex_factory_date")]))][$row[csf("buyer_name")]]['lib_basic_qnty'] =$target_basic_qnty[$row[csf("buyer_name")]][date("Y-m",strtotime($row[csf("ex_factory_date")]))];

			$result_data_arr[date("Y-m",strtotime($row[csf("ex_factory_date")]))][$row[csf("buyer_name")]]['ex_factory_value'] +=($row[csf("ex_factory_qnty")]*$row[csf("unit_price")]);
			$result_data_arr[date("Y-m",strtotime($row[csf("ex_factory_date")]))][$row[csf("buyer_name")]]['commision_cost'] += ($commision_per_pic*($row[csf("ex_factory_qnty")]-$exfactreturn_qty));

			//echo "$commision_per_pic*(".$row[csf("ex_factory_qnty")]."-$exfactreturn_qty <br>";

			$buyer_tem_arr[$row[csf("buyer_name")]]=$row[csf("buyer_name")];

			// $result_data_arr[date("Y-m",strtotime($row[csf("ex_factory_date")]))][$row[csf("buyer_name")]]['commision_cost'] +=($commision_per_pic*($row[csf("ex_factory_qnty")]-$exfactreturn_qty));

			$po_dtls_id_arr[] = $row[csf('po_dtls_id')];
			$po_id_arr[] = $row[csf('po_id')];
		}

		$po_dtls_id_arr = array_unique($po_dtls_id_arr);
		$po_dtls_ids = implode(',', $po_dtls_id_arr);

		$po_id_arr = array_unique($po_id_arr);
		$po_ids = implode(',', $po_id_arr);


		/*$po_result= sql_select("select distinct e.po_break_down_id,
		        e.order_total as total_order_total,
		        e.order_quantity as total_order_qty
		    from wo_pre_cost_fab_yarn_cost_dtls d, wo_po_color_size_breakdown e
		   where e.po_break_down_id in ($po_ids) and d.job_id = e.job_id and d.status_active = 1 and d.is_deleted = 0 and e.status_active = 1 and e.is_deleted = 0");*/

		$order_sql = "select a.job_no_prefix_num as job_prefix, b.id as po_break_down_id, a.job_no, a.company_name, a.buyer_name, a.style_description as style_desc, a.style_ref_no, a.order_uom, a.total_set_qnty as ratio, b.id as po_id, b.po_quantity, b.po_total_price, b.po_number, b.unit_price, c.order_rate, b.pub_shipment_date, c.order_quantity as total_order_qty, c.order_total as total_order_total
			from wo_po_details_master a, wo_po_break_down b,  wo_po_color_size_breakdown c
			where a.job_no = b.job_no_mst and a.job_no = c.job_no_mst and c.po_break_down_id = b.id and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and a.id in ($po_dtls_ids)";

		// echo $order_sql;
		$po_result= sql_select($order_sql);

   		foreach ($po_result as $row) {
   			$order_qty_pcs_arr[$row[csf('job_no')]] += $row[csf('total_order_qty')];
			$total_fob_value[$row[csf('job_no')]] += $row[csf('total_order_total')];
			// $total_yarn_costing[$row[csf('po_break_down_id')]] +=$row[csf('yarn_amount')];
   		}

   		$condition->company_name("= $cbo_company_name");
   		if(str_replace("'","",$cbo_buyer_name)>0){
			$condition->buyer_name("=$cbo_buyer_name");
		}
		if($po_ids!='' || $po_ids!=0) {
			// $condition->po_id("in($po_ids)");
			$condition->jobid_in($po_dtls_ids);
		}
		$condition->init();

		// echo $condition->getCond();die;

   		$conversion = new conversion($condition);
   		$other = new other($condition);
   		$wash = new wash($condition);
   		$commission = new commision($condition);
   		$commercial = new commercial($condition);
   		$emblishment = new emblishment($condition);
   		$trim = new trims($condition);
   		$yarn = new yarn($condition);
   		$fabric = new fabric($condition);

   		// echo $trim->getQuery() . "<br>";

   		$other_costing_arr=$other->getAmountArray_by_job();
   		$wash_costing_arr=$wash->getAmountArray_by_job();
   		$conversion_costing_arr=$conversion->getAmountArray_by_job();
   		$commission_costing_sum_arr=$commission->getAmountArray_by_job();
   		$commercial_costing_arr=$commercial->getAmountArray_by_job();
   		$emblishment_costing_arr=$emblishment->getAmountArray_by_job();
   		$trims_costing_arr=$trim->getAmountArray_by_job();
   		$yarn_fabric_cost_data_arr=$yarn->getJobWiseYarnAmountArray();
		$fabric_costing_arr=$fabric->getAmountArray_by_job_knitAndwoven_greyAndfinish();
		$conversion_process_costing_arr=$conversion->getAmountArray_by_jobAndProcess();

		/*echo '<pre>';
		print_r($trims_costing_arr);
		echo '</pre>';*/

   		foreach ($conversion_costing_arr as $poId => $poArr) {
   			foreach ($poArr as $value) {
   				$total_conversion_cost[$poId] += $value;
   				$total_fabric_amt[$poId] += array_sum($fabric_costing_arr['knit']['grey'][$poId])+array_sum($fabric_costing_arr['woven']['grey'][$poId]);
   			}
   		}

   		$cm_cost_dzn_arr = array();
   		$dzn_qnty_arr = array();
   		$costing_per_arr = array();
   		foreach($sql_result as $row) {
			$job_no = $row[csf('job_no')];
			$po_id = $row[csf('po_id')];
			$buyer_id = $row[csf('buyer_name')];
			$total_fabric_amt = $total_fabric_amt[$job_no];
			$conversion_costing = array_sum($conversion_costing_arr[$job_no]);
			$total_yarn_costing = $yarn_fabric_cost_data_arr[$job_no];
			$total_conversion_cost = $conversion_costing;
			$total_trims_amt = $trims_costing_arr[$job_no];
			$total_embl_amt = $emblishment_costing_arr[$job_no];
			$total_comercial_amt = $commercial_costing_arr[$job_no];
			$total_commisssion = $commission_costing_sum_arr[$job_no];
			$total_wash_costing = $wash_costing_arr[$job_no];
			$total_cm_cost = $other_costing_arr[$job_no]['cm_cost'];
			$total_lab_test_cost = $other_costing_arr[$job_no]['lab_test'];
			$total_inspection_cost = $other_costing_arr[$job_no]['inspection'];
			$total_currier_cost = $other_costing_arr[$job_no]['currier_pre_cost'];
			$total_certificate_cost = $other_costing_arr[$job_no]['certificate_pre_cost'];
			$total_common_oh_cost = $other_costing_arr[$job_no]['common_oh'];
			$total_freight_cost = $other_costing_arr[$job_no]['freight'];
			$tot_conversion_aop_costing = array_sum($conversion_process_costing_arr[$job_no][35]);
			$tot_conversion_yarn_dyeing_costing = array_sum($conversion_process_costing_arr[$job_no][30]);

			$total_all_cost = $total_fabric_amt+$total_yarn_costing+$total_conversion_cost+$total_trims_amt+$total_embl_amt+$total_comercial_amt+$total_commisssion+$total_wash_costing+$total_cm_cost+$total_lab_test_cost+$total_inspection_cost+$total_currier_cost+$total_certificate_cost+$total_common_oh_cost+$total_freight_cost;

			$tot_aop_trim_yd_cost=$tot_conversion_aop_costing+$total_trims_amt+$tot_conversion_yarn_dyeing_costing;
			$total_aop_trim_yd_cost=($tot_aop_trim_yd_cost*10)/100;
			$total_all_cost+=$total_aop_trim_yd_cost;

			/*echo "$total_all_cost = $total_fabric_amt+$total_yarn_costing+$total_conversion_cost+$total_trims_amt+$total_embl_amt+$total_comercial_amt+$total_commisssion+$total_wash_costing+$total_cm_cost+$total_lab_test_cost+$total_inspection_cost+$total_currier_cost+$total_certificate_cost+$total_common_oh_cost+$total_freight_cost+$total_aop_trim_yd_cost";
			echo "<br>";*/

			$total_margin = $total_fob_value[$job_no]-$total_all_cost;
			/*echo "$total_margin = $total_fob_value[$job_no]-$total_all_cost";
			echo "<br>";*/
			$order_qty_pcs = $order_qty_pcs_arr[$job_no];

			$dzn_qnty = ($total_margin/$order_qty_pcs)*12;
			/*echo "$dzn_qnty = ($total_margin/$order_qty_pcs)*12";
			echo "<br>";*/
			$order_qty_pcs = is_infinite($order_qty_pcs) || is_nan($order_qty_pcs) ? 0 : $order_qty_pcs;
			$cm_cost_dzn = $total_cm_cost/$order_qty_pcs*12;

			//echo "$order_qty_pcs<br>";

			$cm_cost_dzn_arr[$buyer_id] = $cm_cost_dzn;
			$dzn_qnty_arr[$buyer_id] = $dzn_qnty;


			/*echo "$job_no: $commision_per_pic=" .$tot_commision_rate_arr[$row[csf('job_no')]] . "/$costing_per";
			echo "<br>";*/
			$commision_per_pic = is_infinite($commision_per_pic) || is_nan($commision_per_pic) ? 0 : $commision_per_pic;
			$cm_value_rate=($tot_cost_arr[$row[csf('job_no')]]/$dzn_qnty);
			$cm_value_rate = is_infinite($cm_value_rate) || is_nan($cm_value_rate) ? 0 : $cm_value_rate;

			$exfactreturn_qty=$ex_factory_qty_arr[$row[csf('po_id')]]['return_qty'];
			$basic_qnty=($row[csf("ex_factory_qnty")]*$row[csf("set_smv")])/$basic_smv_arr[$row[csf("company_name")]];
			// $cm_val=$cm_value_rate*$row[csf("ex_factory_qnty")];
			$cm_val = (($cm_cost_dzn + $dzn_qnty) / $costing_per) * $row[csf("ex_factory_qnty")];
			/*echo "$job_no: $cm_val = (($cm_cost_dzn + $dzn_qnty) / $costing_per) * ".$row[csf("ex_factory_qnty")];
			echo "<br>";*/
			$result_data_arr[date("Y-m",strtotime($row[csf("ex_factory_date")]))][$row[csf("buyer_name")]]['cm_value'] += $cm_val;
			/*echo "$job_no: $cm_val = $commision_per_pic*(" . $row[csf("ex_factory_qnty")] . "-$exfactreturn_qty)";
			echo "<br>";*/
		}

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
                $cm_value = is_infinite($cm_value) || is_nan($cm_value) ? 0 : $cm_value;
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

				$po_id = $result_data_arr[$month_id][$buyer_id]['po_id'];
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

		ob_start();

		?>
        <div id="scroll_body">
            <table width="700"  cellspacing="0" align="left">
                <tr>
                    <td align="center" colspan="7" class="form_caption">
                    <strong style="font-size:16px;">Company Name:<? echo $company_library[$cbo_company_name] ;?></strong>
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
                        <th width="100" title="Ex-Factory Value-Commision Cost">Ex-Fac value without comm</th>
                        <th title="CM Value=((CM Cost+Margin Dzn)/Costing Par)*Exfactory Qty">CM Value</th>
                    </tr>
                </thead>
                <tbody>
                <?
				$p=1;
				foreach($buyer_wisi_data as $buyer_id_ref=>$row)
				{
					$cm_cost_dzn = $cm_cost_dzn_arr[$buyer_id_ref];
					$dzn_qnty = $dzn_qnty_arr[$buyer_id_ref];
					// $cm_val = (($cm_cost_dzn + $dzn_qnty) / $costing_per) * $row["ex_factory_qnty"];
					$cm_val = $row["cm_val"];
					$cm_val = is_infinite($cm_val) || is_nan($cm_val) ? 0 : $cm_val;
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
                        <td align="right"><? echo number_format($cm_val, 2); // number_format($row["cm_val"],2); ?></td>
                    </tr>
                    <?
					$p++;
					$gt_lib_basic_qnty+=$row["lib_basic_qnty"];
					$gt_ex_factory_qnty+=$row["ex_factory_qnty"];
					$gt_ex_factory_value+=$row["ex_factory_value"];
					$gt_basic_qnty+=$row["basic_qnty"];
					$gt_cm_val+=$cm_val;
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
                        <th width="100" title="Ex-Factory Value-Commision Cost">Ex-Fac value without comm</th>
                        <th width="100" title="CM Value=((CM Cost+Margin Dzn)/Costing Par)*Exfactory Qty">CM Value</th>
                        <?
                    }
                    ?>
               </tr>
            </thead>
         </table>
        <table width="<? echo $width;?>" cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table" id="" align="left">
			<?
            echo $main_data;
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

	else if($reportType==13) // Details 5 Button
	{
		$tot_cost_arr = return_library_array("select job_no, cm_cost from wo_pre_cost_dtls","job_no","cm_cost");
		$inspection_date_arr=return_library_array( "select po_break_down_id, max(inspection_date) as inspection_date from pro_buyer_inspection where status_active=1 and is_deleted=0 group by po_break_down_id", "po_break_down_id", "inspection_date");
		$invoice_array=return_library_array( "select id,invoice_no from com_export_invoice_ship_mst", "id", "invoice_no"  );
		$shipping_mode_array=return_library_array( "select id,shipping_mode from com_export_invoice_ship_mst", "id", "shipping_mode"  );
		$lc_sc_type_arr=return_library_array( "select id,is_lc from com_export_invoice_ship_mst", "id", "is_lc"  );
		$lc_sc_id_array=return_library_array( "select id,lc_sc_id from com_export_invoice_ship_mst", "id", "lc_sc_id"  );
		$lc_num_arr=return_library_array( "select id,export_lc_no from com_export_lc", "id", "export_lc_no"  );
		$sc_num_arr=return_library_array( "select id,contract_no from com_sales_contract", "id", "contract_no"  );

		$forwarder_arr=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name");



		$details_report .='<table width="5025" cellspacing="0" cellpadding="0"  border="1"  class="rpt_table" rules="all" id="table_body2">';
		$i=1;

		if($db_type==0)
		{
			$sql= "SELECT b.id as po_id, max(a.lc_sc_no) as lc_sc_arr_no, group_concat(distinct a.invoice_no) as invoice_no, group_concat(distinct a.item_number_id) as itm_num_id, group_concat(distinct a.foc_or_claim) as foc_or_claim,
			sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty, sum(a.total_carton_qnty) as carton_qnty, max(a.ex_factory_date)  as ex_factory_date, group_concat(distinct  a.lc_sc_no) as lc_sc_no, max(a.shiping_mode) as shiping_mode, a.delivery_mst_id as challan_id, d.delivery_floor_id as del_floor,  b.shipment_date, b.po_number, b.po_quantity as po_quantity, (b.unit_price/c.total_set_qnty) as unit_price, b.up_charge, b.shiping_status, c.id, c.company_name, c.buyer_name, c.client_id, c.job_no_prefix_num,c.job_no,c.ship_mode , YEAR(c.insert_date) as year, c.style_ref_no, c.style_description, c.set_smv, d.delivery_company_id as del_company,d.source,d.delivery_location_id as del_location ,c.total_set_qnty,a.item_number_id,c.set_break_down,c.order_uom,c.DESIGN_SOURCE_ID, D.SYS_NUMBER as challan_no
			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c ,pro_ex_factory_delivery_mst d
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no $company_cond $str_cond $del_comp_cond $del_location_cond $del_floor_cond $shiping_status_cond $source_cond $buyer_conds $internal_ref_cond $search_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active  in(1,2,3) and c.is_deleted=0 and c.status_active  in(1,2,3) and a.delivery_mst_id=d.id
			group by
					b.id, a.delivery_mst_id, d.delivery_floor_id, b.shipment_date, b.po_number, b.po_quantity, c.total_set_qnty, b.unit_price, b.up_charge, b.shiping_status, c.id, c.company_name, c.buyer_name, c.client_id, c.job_no_prefix_num, c.job_no, c.ship_mode, c.insert_date, c.style_ref_no, c.style_description, c.set_smv, d.delivery_company_id, d.delivery_location_id, d.source, a.item_number_id, c.set_break_down, c.order_uom,c.DESIGN_SOURCE_ID, D.SYS_NUMBER
			order by c.buyer_name, b.shipment_date ASC";
		}
		else if($db_type==2)
		{
		 	$sql= "SELECT b.id as po_id, max(a.lc_sc_no) as lc_sc_arr_no, LISTAGG(CAST( a.invoice_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.invoice_no) as invoice_no, LISTAGG(CAST( a.foc_or_claim AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.foc_or_claim) as foc_or_claim, LISTAGG(CAST( a.item_number_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.item_number_id) as itm_num_id, sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty, sum(a.total_carton_qnty) as carton_qnty, max(a.ex_factory_date) as ex_factory_date, LISTAGG(CAST( a.lc_sc_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.lc_sc_no) as lc_sc_no, max(a.shiping_mode) as shiping_mode, a.delivery_mst_id as challan_id, d.delivery_floor_id as del_floor, b.shipment_date, b.po_number, b.po_quantity as po_quantity, (b.unit_price/c.total_set_qnty) as unit_price, b.up_charge, b.shiping_status, c.id, c.company_name, c.buyer_name, c.client_id, c.job_no_prefix_num, c.job_no, c.ship_mode, to_char(c.insert_date,'YYYY') as year, c.style_ref_no, c.style_description, c.set_smv, d.delivery_company_id as del_company, d.source, d.delivery_location_id as del_location, c.total_set_qnty, a.item_number_id, c.set_break_down, c.order_uom,c.DESIGN_SOURCE_ID, e.sequence_no, D.SYS_NUMBER as challan_no
			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c ,pro_ex_factory_delivery_mst d, lib_buyer e
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.buyer_name=e.id $company_cond $str_cond $del_comp_cond $del_location_cond $del_floor_cond $shiping_status_cond $source_cond $buyer_conds $internal_ref_cond $search_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,2,3) and c.is_deleted=0 and c.status_active in(1,2,3) and a.delivery_mst_id=d.id
			group by
					b.id, a.delivery_mst_id, d.delivery_floor_id, b.shipment_date, b.po_number, b.po_quantity, c.total_set_qnty, b.unit_price, b.up_charge, b.shiping_status, c.id, c.company_name, c.buyer_name, c.client_id, c.job_no_prefix_num, c.job_no, c.ship_mode, c.insert_date, c.style_ref_no, c.style_description, c.set_smv, d.delivery_company_id, d.source, d.delivery_location_id, a.item_number_id, c.set_break_down, c.order_uom,c.DESIGN_SOURCE_ID, e.sequence_no, D.SYS_NUMBER
			order by e.sequence_no, c.buyer_name,b.shipment_date ASC";
		}
		//  echo $sql;
		$sql_result=sql_select($sql);
		foreach($sql_result as $k=>$v)
		{
			$all_job_arr[trim($v[csf("job_no")])]=trim($v[csf("job_no")]);
			$all_po_arr[trim($v[csf("po_id")])]=$v[csf("po_id")];
		}




		if($source_cond){$source_cond2=str_replace("d.","a.",$source_cond);}
		$challan_mst_arr=array();
		$challan_sql="SELECT a.id,b.delivery_mst_id, a.sys_number_prefix_num,a.sys_number, a.forwarder, a.truck_no, a.driver_name, a.mobile_no, a.dl_no, b.po_break_down_id,b.item_number_id,a.delivery_floor_id,
		(CASE WHEN a.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty,
		b.total_carton_qnty as carton_qnty, a.challan_no
		from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b
		where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $source_cond2 ".where_con_using_array($all_po_arr,0,'b.po_break_down_id')."";
		 //echo $challan_sql;
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

			//$exfact_return_qty_arr[$row[csf("po_break_down_id")]][$row[csf("challan_no")]]+=$row[csf("ex_factory_return_qnty")];
			$exfact_return_qty_arr[$row[csf("po_break_down_id")]][$row[csf("challan_no")]][$row[csf("item_number_id")]]+=$row[csf("ex_factory_return_qnty")];



			$exfact_qty_arr_without_current[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]]["ex_fact"]+=$row[csf("ex_factory_qnty")]-$row[csf("ex_factory_return_qnty")];
			$exfact_qty_arr_without_current[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]]["carton"]+=$row[csf("carton_qnty")];
		}



		$all_job="'".implode("','", array_unique($all_job_arr))."'";
		$order_item_qnty_sql="SELECT po_break_down_id,item_number_id,sum(order_quantity) as order_quantity from wo_po_color_size_breakdown where status_active  in(1,2,3) and is_deleted=0 and job_no_mst in($all_job) group by po_break_down_id,item_number_id";
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

			foreach($challan_id as $val)
			{
				if($challan_no=="") $challan_no=$challan_mst_arr[$val][$row[csf('po_id')]]['challan']; else $challan_no.=','.$challan_mst_arr[$val][$row[csf('po_id')]]['challan'];
				if($floor_no=="") $floor_no=$floor_library[$challan_mst_arr[$val][$row[csf('po_id')]]['floor']]; else $floor_no.=','.$floor_library[$challan_mst_arr[$val][$row[csf('po_id')]]['floor']];

				if($forwarder=="") $forwarder=$forwarder_arr[$challan_mst_arr[$val][$row[csf('po_id')]]['forwarder']]; else $forwarder.=','.$forwarder_arr[$challan_mst_arr[$val][$row[csf('po_id')]]['forwarder']];
				if($vehi_no=="") $vehi_no=$challan_mst_arr[$val][$row[csf('po_id')]]['truck_no']; else $vehi_no.=','.$challan_mst_arr[$val][$row[csf('po_id')]]['truck_no'];
				if($dirver_info=="") $dirver_info="Name: ".$challan_mst_arr[$val][$row[csf('po_id')]]['driver_name']."<br>Mob No: ".$challan_mst_arr[$val][$row[csf('po_id')]]['mobile_no']."<br>DL No: ".$challan_mst_arr[$val][$row[csf('po_id')]]['dl_no']; else $dirver_info.=','."Name: ".$challan_mst_arr[$val][$row[csf('po_id')]]['driver_name']."<br>Mob No: ".$challan_mst_arr[$val][$row[csf('po_id')]]['mobile_no']."<br>DL No: ".$challan_mst_arr[$val][$row[csf('po_id')]]['dl_no'];
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

			$current_ex_up_charge = 0;
			$current_ex_up_charge_value = 0;

			$total_ex_up_charge = 0;
			$total_ex_up_charge_value = 0;

			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$comapny_id=$row[csf("company_name")];
			$delv_comp=($row[csf("source")]!=3)?  $company_library[$row[csf("del_company")]] : $supp_library[$row[csf("del_company")]];

			$onclick=" change_color('tr_".$i."','".$bgcolor."')";
			$details_report .='<tr bgcolor="'.$bgcolor.'" onclick="'.$onclick.'" id="tr_'.$i.'">';
			$details_report .='<td width="30" align="center">'.$i.'</td>
								<td width="130" align="center" ><p>'.$company_library[$row[csf("company_name")]].'</p></td>
								<td width="60" align="center" ><p>'.$row[csf("job_no_prefix_num")].'</p></td>
								<td width="60" align="center" ><p>'.$row[csf("year")].'</p></td>
								<td width="100" align="center" ><p>'.$buyer_arr[$row[csf("buyer_name")]].'</p></td>
								<td width="100" align="center" ><p>'.$buyer_arr[$row[csf("client_id")]].'</p></td>
								<td width="110" align="center"><p>'.$row[csf("po_number")].'</p></td>
								<td width="125" align="center" ><p>'.$delv_comp.'</p></td>
								<td width="125" align="center" ><p>'.$location_library[$row[csf("del_location")]].'</p></td>
								<td width="125" align="center" ><p>'.$floor_no.'</p></td>
								<td width="120" align="center"><p>'.$challan_no.'</p></td>
								<td width="100" align="center"><p>';
								$inv_id=""; $lc_sc_no=""; $ship_mode="";
								foreach($invoce_id_arr as $invoice_id)
								{
									if($inv_id=="") $inv_id=$invoice_array[$invoice_id]; else $inv_id=$inv_id.",".$invoice_array[$invoice_id];
									if($ship_mode=="") $ship_mode=$shipment_mode[$shipping_mode_array[$invoice_id]]; else $ship_mode=$ship_mode.",".$shipment_mode[$shipping_mode_array[$invoice_id]];
									if($lc_sc_type_arr[$invoice_id]==1)
									{
										if($lc_sc_no=="") $lc_sc_no=$lc_num_arr[$lc_sc_id_array[$invoice_id]]; else $lc_sc_no.=",".$lc_num_arr[$lc_sc_id_array[$invoice_id]];
									}
									else
									{
										if($lc_sc_no=="") $lc_sc_no=$sc_num_arr[$lc_sc_id_array[$invoice_id]]; else $lc_sc_no.=",".$sc_num_arr[$lc_sc_id_array[$invoice_id]];
									}
								}

			$details_report .=$inv_id.'</p></td>
								<td width="100" align="center"><p>'.$lc_sc_no.'</p></td>
								<td width="80"><p>'.$design_source_arr[$row[csf("DESIGN_SOURCE_ID")]].'</p></td>
								<td width="100"><p>'.$row[csf("style_ref_no")].'</p></td>
								<td width="100"><p>'.$row[csf("style_description")].'</p></td>
								<td width="110" align="center"><p>';
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
								// $current_ex_Fact_Qty=$row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]][$row[csf("challan_no")]];
								$current_ex_Fact_Qty=$row[csf("ex_factory_qnty")];
								$basic_qnty=($total_ex_fact_qty*$item_smv)/$basic_smv_arr[$row[csf("company_name")]];
								$cm_per_pcs_tot+=($cm_per_pcs*$current_ex_Fact_Qty);
								$short_excess=$total_ex_fact_qty-$po_quantity;

								$excess_msg=($short_excess>0) ?" color:green;":"color:black;";
								$excess_val_msg=(($total_ex_fact_qty*$unit_price)-$po_quantity>0) ?" color:green;":"color:black;";
								$ttl_ex_qty=($total_ex_fact_qty/$po_quantity)*100;
								$ttl_ex_qty_msg=($ttl_ex_qty>100) ?" color:green;":"color:black;";

								$current_ex_up_charge = ($row[csf('up_charge')]/$po_quantity)*$current_ex_Fact_Qty;
								$current_ex_up_charge_value = $current_ex_up_charge + ($current_ex_Fact_Qty*$unit_price);

								$total_ex_up_charge = ($row[csf('up_charge')]/$po_quantity)*$total_ex_fact_qty;
								$total_ex_up_charge_value = $total_ex_up_charge + ($total_ex_fact_qty*$unit_price);

								//$exfact_return_qty = $exfact_return_qty_arr[$row[csf('po_id')]][$row[csf('challan_no')]];
								$exfact_return_qty = $exfact_return_qty_arr[$row[csf('po_id')]][$row[csf('challan_no')]][$row[csf('item_number_id')]];

								$cumulative_qty = $current_ex_Fact_Qty-$exfact_return_qty;

								$temp_focclaim_arr=array();
								foreach(explode(',',$row[csf("foc_or_claim")]) as $foc_id){
									$temp_focclaim_arr[$foc_id]=$foc_claim_arr[$foc_id];
								}
								$row[csf("foc_or_claim")]=implode(',',$temp_focclaim_arr);



			$details_report .=$item_name_all.'</p></td>
								<td width="80" align="center"><p>'.$item_smv.'</p></td>
								<td width="70" align="center"><p>'.change_date_format($row[csf("shipment_date")]).'</p></td>
								<td width="70" align="center"><a href="##" onclick="openmypage_ex_date('.$comapny_id.",'".$row[csf('po_id')]."','".$row[csf('item_number_id')]."','".$ex_fact_date_range."','ex_date_popup','".$row[csf('challan_id')]."'".",'1'".')">'.change_date_format($row[csf('ex_factory_date')]).'</a></td>
								<td width="100" align="center"><p>'.$shipment_mode[$row[csf('ship_mode')]].'</p></td>
								<td width="70" align="center"><p>'.$shipment_mode[$row[csf("shiping_mode")]].'</p></td>
								<td width="100" align="center"><p>'.$row[csf("foc_or_claim")].'</p></td>

								<td width="60" align="center" style="'.$diff_color.'"><p>('.$diff.')</p></td>
								<td width="100" align="center"><p>'.$unit_of_measurement[$row[csf('order_uom')]].'</p></td>
								<td width="80" align="right"><p>'. number_format($po_quantity,0,'', '').'</p></td>
								<td width="70" align="right"><p>'. number_format($unit_price,4).'</p></td>
								<td width="100" align="right"><p>'. number_format(($po_quantity*$unit_price),2).'</p></td>
								<td width="80" align="right"><p>'.number_format($row[csf("up_charge")],2,'.', '').'</p></td>
								<td width="80" align="right"><p><a href="##" onclick="openmypage_ex_date('.$comapny_id.",'".$row[csf('po_id')]."','".$row[csf('item_number_id')]."','".$ex_fact_date_range."','ex_date_popup','".$row[csf('challan_id')]."'".",'1'".')">'. number_format($current_ex_Fact_Qty,0,'.', '').'</a></p></td>



								<td width="80" align="right"><p><a href="##" onclick="openmypage_ex_date('.$comapny_id.",'".$row[csf('po_id')]."','".$row[csf('item_number_id')]."','".$ex_fact_date_range."','ex_return_popup','".str_replace(",","*",$challan_no)."'".",'1'".')">'. $exfact_return_qty.'</a></p></td>

								<td width="80" align="right"><p>'.$cumulative_qty.'</p></td>


								<td width="100" align="right"><p>'. number_format( $current_ex_fact_value=$current_ex_Fact_Qty*$unit_price,2).'</p></td>


								<td width="100" align="right"><p>'. number_format( $exfact_return_val=($exfact_return_qty*$row[csf("unit_price")]),2).'</p></td>
								<td width="100" align="right"><p>'. number_format(($current_ex_fact_value-$exfact_return_val),2).'</p></td>


								<td width="100" align="right">'.number_format($current_ex_up_charge,2).'</td>
								<td width="100" align="right">'.number_format($current_ex_up_charge_value,2).'</td>

								<td width="80" align="right"><p>'. number_format($row[csf("carton_qnty")],0,'.', '').'</p></td>
								<td width="80" align="right"><p><a href="##" onclick="openmypage_ex_date('.$comapny_id.",'".$row[csf('po_id')]."','".$row[csf('item_number_id')]."','".$total_exface_qnty."','ex_date_popup','".$row[csf('challan_id')]."'".",'1'".')">'.number_format($total_ex_fact_qty,0,'.', '').'</a></p></td>
								<td width="100" align="right"><p>'. number_format($total_ex_fact_value=$total_ex_fact_qty*$unit_price,2).'</p></td>

								<td width="100" align="right">'.number_format($total_ex_up_charge,2).'</td>
								<td width="100" align="right">'.number_format($total_ex_up_charge_value,2).'</td>

								<td width="80" align="right"><p>'.number_format($total_cartoon_qty,0,'.', '').'</p></td>
								<td width="100" align="right" title="Total Ex.Qty*SMV"><p>'. number_format($total_sales_minutes=$total_ex_fact_qty*$item_smv).'</p></td>
								<td width="80" align="right"><p>'.number_format($basic_qnty,0,'','').'</p></td>
								<td width="80" align="right" style="'.$excess_msg.'" ><p>'. number_format($excess_shortage_qty=$total_ex_fact_qty-$po_quantity,0,'', '').'</p></td>
								<td width="100" align="right" style="'.$excess_val_msg.'"><p>'. number_format($excess_shortage_value=($excess_shortage_qty*$unit_price),2).'</p></td>
								<td align="center" style="'.$ttl_ex_qty_msg.'" width="80"><p>'. number_format($total_ex_fact_qty_parcent=($total_ex_fact_qty/$po_quantity)*100,0).'</p></td>
								<td width="60" align="center" title="CM per pcs: '.number_format($cm_per_pcs,4).'"><p>'.number_format($cm_per_pcs*$total_ex_fact_qty,2).'</p></td>
								<td width="100" align="center"><p>'.$forwarder.'</p></td>
								<td width="80" align="center"><p>'.$vehi_no.'</p></td>
								<td width="130"><p>'.$dirver_info.'</p></td>
								<td width="70" align="center"><p>'.(change_date_format($inspection_date_arr[$row[csf('po_id')]]) == '0000-00-00' || change_date_format($inspection_date_arr[$row[csf('po_id')]]) == '' ? '' : change_date_format(change_date_format($inspection_date_arr[$row[csf('po_id')]]))).'</p></td>
								<td align="center"><p>'.$shipment_status[$row[csf('shiping_status')]].'</p></td>
							</tr>';

			//$master_data[$row[csf("buyer_name")]]['po_qnty'] +=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]];
 	  		//$master_data[$row[csf("buyer_name")]]['po_value'] +=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]]*$row[csf("unit_price")];
 	  		$total_po_qty+=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]];
 	  		$total_po_valu+=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]]*$row[csf("unit_price")];


			// $master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['ex_factory_qnty'] += $row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]][$row[csf("challan_id")]];
			$master_data[$row[csf("sequence_no")]][$row[csf("buyer_name")]][$row[csf("client_id")]]['ex_factory_qnty'] += $row[csf("ex_factory_qnty")];
			// $master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['ex_factory_value'] +=($row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]][$row[csf("challan_id")]])*$row[csf("unit_price")];
			$master_data[$row[csf("sequence_no")]][$row[csf("buyer_name")]][$row[csf("client_id")]]['ex_factory_value'] += $row[csf("ex_factory_qnty")]*$row[csf("unit_price")];
			$master_data[$row[csf("sequence_no")]][$row[csf("buyer_name")]][$row[csf("client_id")]]['exfact_return_qty'] += $exfact_return_qty;
			$master_data[$row[csf("sequence_no")]][$row[csf("buyer_name")]][$row[csf("client_id")]]['exfact_return_val'] += $exfact_return_qty*$row[csf("unit_price")];

			$total_basic_qty+=$basic_qnty;

			// $total_ex_qty+=$row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]][$row[csf("challan_id")]];
			$total_ex_qty+=$row[csf("ex_factory_qnty")];
			$total_exreturn_qty+=$exfact_return_qty;
			$total_cumulative_qty+=$cumulative_qty;
			$total_crtn_qty+=$row[csf("carton_qnty")];
			// $total_ex_valu+=($row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]][$row[csf("challan_id")]])*$row[csf("unit_price")];
			$total_ex_valu+=$row[csf("ex_factory_qnty")]*$row[csf("unit_price")];
			$g_total_ex_qty+=$total_ex_fact_qty;
			$g_total_ex_crtn+=$total_cartoon_qty;
			$g_total_ex_val+=$total_ex_fact_qty*$row[csf("unit_price")];
			$g_sales_minutes+=$total_sales_minutes;

			// $g_sales_minutes+=$current_ex_Fact_Qty*$item_smv;
			$total_eecess_storage_qty+=$excess_shortage_qty;
			$total_eecess_storage_val+=$excess_shortage_value;


			$total_exfact_return_val+=$exfact_return_val;


			if($po_check_arr[$row[csf("po_id")]][$row[csf("item_number_id")]]=="")
			{
				$po_check_arr[$row[csf("po_id")]][$row[csf("item_number_id")]]=$row[csf("po_id")];
				$master_data[$row[csf("sequence_no")]][$row[csf("buyer_name")]][$row[csf("client_id")]]['b_id']=$row[csf("buyer_name")];
				$master_data[$row[csf("sequence_no")]][$row[csf("buyer_name")]][$row[csf("client_id")]]['org_po_qnty'] +=$row[csf("po_quantity")];
				$master_data[$row[csf("sequence_no")]][$row[csf("buyer_name")]][$row[csf("client_id")]]['upchage'] +=$row[csf("up_charge")];
				$master_data[$row[csf("sequence_no")]][$row[csf("buyer_name")]][$row[csf("client_id")]]['po_qnty'] +=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]];
				$master_data[$row[csf("sequence_no")]][$row[csf("buyer_name")]][$row[csf("client_id")]]['po_value'] +=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]]*$row[csf("unit_price")];
				$master_data[$row[csf("sequence_no")]][$row[csf("buyer_name")]][$row[csf("client_id")]]['basic_qnty'] +=$basic_qnty;
				$master_data[$row[csf("sequence_no")]][$row[csf("buyer_name")]][$row[csf("client_id")]]['total_ex_fact_qty'] +=$total_ex_fact_qty;
				$master_data[$row[csf("sequence_no")]][$row[csf("buyer_name")]][$row[csf("client_id")]]['total_ex_fact_value'] +=$total_ex_fact_qty*$row[csf("unit_price")];
				$master_data[$row[csf("sequence_no")]][$row[csf("buyer_name")]][$row[csf("client_id")]]['sales_min'] += $item_smv*$total_ex_fact_qty;

				$master_data[$row[csf("sequence_no")]][$row[csf("buyer_name")]][$row[csf("client_id")]]['current_ex_up_charge'] += $current_ex_up_charge;
				$master_data[$row[csf("sequence_no")]][$row[csf("buyer_name")]][$row[csf("client_id")]]['current_ex_up_charge_value'] += $current_ex_up_charge_value;
				$master_data[$row[csf("sequence_no")]][$row[csf("buyer_name")]][$row[csf("client_id")]]['total_ex_up_charge'] += $total_ex_up_charge;
				$master_data[$row[csf("sequence_no")]][$row[csf("buyer_name")]][$row[csf("client_id")]]['total_ex_up_charge_value'] += $total_ex_up_charge_value;

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
				$total_po_val+=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]]*$row[csf("unit_price")];
				$gr_upcharge+=$row[csf("up_charge")];

				$gr_current_ex_up_charge += $current_ex_up_charge;
				$gr_current_ex_up_charge_value += $current_ex_up_charge_value;

				$gr_total_ex_up_charge += $total_ex_up_charge;
				$gr_total_ex_up_charge_value += $total_ex_up_charge_value;
			}
			$i++; $item_name_all="";
		}
		$pp=$i;
		$width=2250;
		?>
        <style>
			#scroll_body table tr td:nth-child(30),
			#scroll_body table tr td:nth-child(32),
			#scroll_body table tr td:nth-child(37),

			#scroll_body1 table tr td:nth-child(9),
			#scroll_body1 table tr td:nth-child(11),
			#scroll_body1 table tr td:nth-child(14)
			{
			  background: #ccc;
			}
		</style>
        <div>
            <div style="width:<?=$width+20;?>px; margin-left:5px;" >
                <table width="<?=$width+20;?>"  cellspacing="0"  align="center">
                    <tr>
                        <td align="center" colspan="17" class="form_caption">
                            <strong style="font-size:16px;">Company Name:<? echo  $company_library[$cbo_company_name] ;?></strong>
                        </td>
                    </tr>
                    <tr class="form_caption">
                        <td colspan="17" align="center" class="form_caption"> <strong style="font-size:15px;">Ex-Factory Report</strong></td>
                    </tr>
                     <tr align="center">
                        <td colspan="17" align="center" class="form_caption"> <strong style="font-size:15px;">Total Summary</strong></td>
                    </tr>
                </table>
                <table width="<?=$width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
                    <thead>
                        <th width="30">SL</th>
                        <th width="110">Buyer Name</th>
                        <th width="100">Client</th>
                        <th width="100">PO Qty</th>
                        <th width="100">PO Qty (pcs)</th>
                        <th width="130">PO Value</th>
                        <th width="100">PO Value(%)</th>
                        <th width="100">PO TTL Up-Charge</th>
                        <th width="100">Current Ex-Fact. Qty</th>
                        <th width="100">Return Qty</th>
                        <th width="100">Current Cumulative Qty</th>
                        <th width="130">Current Ex-Fact. Value</th>

                        <th width="100">Return Qty Value</th>
                        <th width="100">Current Cumulative Ex-Fact. Value</th>

                        <th width="130">Up Charge with Current Ex-Fact. Value</th>
                        <th width="100">Total Ex-Fact. Qty.</th>
                        <th width="130">Total Ex-Fact. Value</th>
                        <th width="130">Up Charge with Total Ex-Fact. Value</th>
                        <th width="100">Sales Minutes</th>
                        <th width="100">Total Ex-Fact. (Basic Qty)</th>
                        <th>Total Ex-Fact. Value %</th>
                    </thead>
             </table>
             <div style="width:<?=$width+20;?>px; max-height:225px; overflow-y:scroll" id="scroll_body1">
             <table width="<?=$width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body" align="left">
                 <?
                $m=1; $grand_sales_minute =0;
                foreach($master_data as $sequence_no=>$sequence_arr)
	            {
	                foreach($sequence_arr as $buyid=>$buyData)
	                {
						foreach($buyData as $clientid=>$cdata)
						{
							if ($m%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$buyer_po_quantity=0; $buyer_po_value=0; $current_ex_Fact_Qty=0; $current_ex_fact_value=0; $total_ex_fact_qty=0; $total_ex_fact_value=0; $g_sales_min=0;

							$po_quantity=$cdata['po_qnty'];
							$buyer_po_value=$cdata["po_value"];
							$parcentages+=($buyer_po_value/$total_po_val)*100;
							$current_ex_Fact_Qty=$cdata['ex_factory_qnty'];
							$current_ex_fact_value=$cdata['ex_factory_value'];
							$total_ex_fact_qty=$cdata['total_ex_fact_qty'];
							$total_ex_fact_value=$cdata['total_ex_fact_value'];
							$buyer_basic_qnty=$cdata["basic_qnty"];
							$total_ex_fact_value_parcentage=($total_ex_fact_value/$buyer_po_value)*100;
							$current_ex_fact_return = $cdata["exfact_return_qty"];
							$current_ex_fact_return_val = $cdata["exfact_return_val"];
							$current_cumulative_qty = $current_ex_Fact_Qty-$current_ex_fact_return;

							$current_ex_up_charge_value = $cdata["current_ex_up_charge_value"];
							$total_ex_up_charge_value = $cdata["total_ex_up_charge_value"];

							?>
							<tr bgcolor="<?=$bgcolor;?>" onclick="change_color('tr_<?=$i;?>','<?=$bgcolor;?>')" id="tr_<?=$i;?>" >
								<td width="30" align="center"><?=$m;?></td>
								<td width="110" style="word-break:break-all"><?=$buyer_arr[$buyid];?></td>
								<td width="100" style="word-break:break-all"><?=$buyer_arr[$clientid];?></td>
								<td width="100" style="word-break:break-all" align="right"><?=number_format($cdata["org_po_qnty"], 0);?></td>
								<td width="100" style="word-break:break-all" align="right"><?=number_format($po_quantity, 0);?></td>
								<td width="130" style="word-break:break-all" align="right"><p id="value_<?=$i;?>"><?=number_format($buyer_po_value, 2, '.', '');?></p></td>
								<td width="100" style="word-break:break-all" align="right"><?=number_format(($buyer_po_value / $total_po_val) * 100, 2, '.', '');?></td>
	                            <td width="100" style="word-break:break-all" align="right"><?=number_format($cdata["upchage"], 2, '.', '');?> </td>
								<td width="100" style="word-break:break-all" align="right"><?=number_format($current_ex_Fact_Qty, 0, '', '');?></td>
								<td width="100" style="word-break:break-all" align="right"><?php echo $current_ex_fact_return; ?></td>
								<td width="100" style="word-break:break-all" align="right"><?php echo $current_cumulative_qty; ?></td>
								<td width="130" style="word-break:break-all" align="right"><?=number_format($current_ex_fact_value, 2, '.', '');?></td>

	                            <td width="100" align="right"><?=number_format($current_ex_fact_return_val,2);?></td>
	                            <td width="100" align="right"><?=number_format($current_ex_fact_value-$current_ex_fact_return_val,2);?></td>


	                            <td width="130" style="word-break:break-all" align="right"><?=number_format($current_ex_up_charge_value, 2, '.', '');?></td>
								<td width="100" style="word-break:break-all" align="right"><?=number_format($total_ex_fact_qty, 0, '', '');?></td>
								<td width="130" style="word-break:break-all" align="right"><?=number_format($total_ex_fact_value, 2, '.', '');?> </td>
								<td width="130" style="word-break:break-all" align="right"><?=number_format($total_ex_up_charge_value, 2, '.', '');?> </td>
								<td width="100" style="word-break:break-all" align="right"><? echo $g_sales_min+= number_format($cdata["sales_min"],0,'',''); ?></td>
								<td width="100" style="word-break:break-all" align="right"><?=number_format($buyer_basic_qnty, 0, '', '');?></td>
								<td style="word-break:break-all" align="right"><?=number_format($total_ex_fact_value_parcentage, 0)?> %</td>
							</tr>
							<?
							$i++; $m++;

							$grand_sales_minute +=number_format($cdata["sales_min"],0,'','');
							$total_buyer_org_po_quantity+=$cdata["org_po_qnty"];
							$total_buyer_po_quantity+=$po_quantity;
							$total_buyer_po_value+=$buyer_po_value;
							$total_current_ex_Fact_Qty +=$current_ex_Fact_Qty;
							$value_total_exfact_return+=$current_ex_fact_return;
							$value_total_cumulative_qty+=$current_cumulative_qty;
							$total_current_ex_fact_value+=$current_ex_fact_value;
							$mt_total_ex_fact_qty+=$total_ex_fact_qty;
							$mt_total_ex_fact_value+=$total_ex_fact_value;
							$total_buyer_basic_qnty +=$buyer_basic_qnty;
							$buyerTotUpCharge +=$cdata["upchage"];

							$tot_current_ex_fact_return_val+=$current_ex_fact_return_val;


						}
	                }
	            }
				?>
                </table>
                </div>
				<input type="hidden" name="total_i" id="total_i" value="<?=$i;?>" />
                <table class="tbl_bottom" width="<?=$width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                    <thead>
                    	<td width="30">&nbsp;</td>
                        <td width="110" align="right"><b>Total:</b></td>
                        <td width="100">&nbsp;</td>
                        <td width="100" align="right" id="total_buyer_org_po_quantity"><?=number_format($total_buyer_org_po_quantity, 0);?></td>
                        <td width="100"align="right" id="total_buyer_po_quantity"><?=number_format($total_buyer_po_quantity, 0);?></td>
                        <td width="130" align="right" id="value_total_buyer_po_value"><?=number_format($total_buyer_po_value, 2, '.', '');?></td>
                        <td width="100" align="right" id="parcentages"><?=ceil($parcentages);?></td>
                        <td width="100" align="right" id="value_upcharge"><?=number_format($buyerTotUpCharge, 2);?></td>
                        <td width="100" align="right" id="total_current_ex_Fact_Qty"><?=number_format($total_current_ex_Fact_Qty, 0);?></td>
                        <td width="100" align="right" id="value_total_exfact_return"><?php echo number_format($value_total_exfact_return); ?></td>
                        <td width="100" align="right" id="value_total_cumulative_qty"><?php echo number_format($value_total_cumulative_qty); ?></td>
                        <td width="130" align="right" id="value_total_current_ex_fact_value"><?=number_format($total_current_ex_fact_value, 2);?></td>

                         <td width="100" align="right"><?=number_format($tot_current_ex_fact_return_val, 2);?></td>
                         <td width="100" align="right"><?=number_format($total_current_ex_fact_value-$tot_current_ex_fact_return_val, 2);?></td>

                        <td width="130" align="right" id="value_total_current_ex_fact_value_with_up_charge"><? //=number_format($total_current_ex_fact_value, 2);?></td>
                        <td width="100" align="right" id="mt_total_ex_fact_qty"><?=number_format($mt_total_ex_fact_qty, 0);?></td>
                        <td width="130" align="right" id="value_mt_total_ex_fact_value"><?=number_format($mt_total_ex_fact_value, 2);?></td>
                        <td width="130" align="right" id="value_mt_total_ex_fact_value_with_up_charge"><? //=number_format($mt_total_ex_fact_value, 2);?></td>
                        <td width="100" align="right"><?=number_format($grand_sales_minute, 2);?></td>
                        <td width="100" align="right" id="total_buyer_basic_qnty"><?=number_format($total_buyer_basic_qnty, 0);?></td>
                        <td>&nbsp;</td>
                    </tfoot>
                </table>
            </div>
            <br />
            <!-- ==================================== details part ================================== -->
            <div style="margin-left:5px;">
                <table width="5020">
                    <tr>
                    	<td colspan="51" class="form_caption"><strong style="font-size:16px;">Shipped Out Order Details Report</strong></td>
                    </tr>
                </table>
                <table width="5025" border="1" class="rpt_table" rules="all" id="table_header_2">
                    <thead>
                        <th width="30">SL</th>
                        <th width="130">Company</th>
                        <th width="60">Job</th>
                        <th width="60">Year</th>
                        <th width="100">Buyer Name</th>
                        <th width="100">Client</th>
                        <th width="110">Order NO</th>
                        <th width="125">Del Company</th>
                        <th width="125">Del Location</th>
                        <th width="125">Del Floor</th>
                        <th width="120">Challan NO</th>
                        <th width="100">Invoice NO</th>
                        <th width="100">LC/SC NO</th>
                        <th width="80">Design Source</th>
                        <th width="100">Style Ref. no.</th>
                        <th width="100">Style Description</th>
                        <th width="110">Item Name</th>
                        <th width="80">Item SMV</th>
                        <th width="70">Shipment Date</th>
                        <th width="70">Ex-Fac. Date</th>
                        <th width="100"><p>Po Rcv.Ship Mode</p></th>
                        <th width="70">Shipping Mode</th>
                        <th width="100">FOC/Claim</th>
                        <th width="60">Days in Hand</th>
                        <th width="100">UOM</th>
                        <th width="80">PO Qtny. (pcs)</th>
                        <th width="70">Unit Price</th>
                        <th width="100">PO Value</th>
                        <th width="80">PO TTL Up-Charge</th>
                        <th width="80">Current Ex-Fact. Qty (pcs)</th>
                        <th width="80">Return Qty</th>
                        <th width="80">Current Cumulative Qty</th>
                        <th width="100">Current Ex-Fact. Value</th>


                        <th width="100">Return Qty Value</th>
                        <th width="100">Current Cumulative Ex-Fact. Value</th>


                        <th width="100">UP Charge Based on Current Ex-facotry</th>
                        <th width="100">Current Ex-Fact. Value with Up Charge</th>
                        <th width="80">Current Carton Qty</th>
                        <th width="80">Total Ex-Fact. Qty.</th>
                        <th width="100">Total Ex-Fact. Value</th>
                        <th width="100">Up Charge Based on TTL Ex-factory</th>
                        <th width="100">Up charge with Total Ex-Fact. Value</th>
                        <th width="80">Total Carton Qty</th>
                        <th width="100">Sales Minute</th>
                        <th width="80">Total Ex-Fact. (Basic Qty)</th>
                        <th width="80">Excess/ Shortage Qty</th>
                        <th width="100">Excess/ Shortage Value</th>
                        <th width="80">Total Ex-Fact. Qty. %</th>
                        <th width="60">Sales CM</th>
                        <th width="100">C & F Name</th>
                        <th width="80">Vehicle No</th>
                        <th width="130">Driver Info</th>
                        <th width="70">Inspection Date</th>
                        <th>Ex-Fact Status</th>
                    </thead>
                </table>
            <div style="width:5045px; overflow-y:scroll; overflow-x:hidden; max-height:300px;" id="scroll_body" >
            <? echo $details_report;
            	$details_report .='</table>';
            ?>

            <table width="5025" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" id="table_footer">
                <tfoot>
                    <tr>
                    	<th width="30">&nbsp;</th>
                        <th width="130">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="125">&nbsp;</th>
                        <th width="125">&nbsp;</th>
                        <th width="125">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="100" align="right"><strong>Total</strong></th>
                        <th width="80" id="total_po_qtybk" align="right"><? echo  number_format($gr_po_qnty_pcs,0);?></th>
                        <th width="70" align="right">&nbsp;</th>
                        <th width="100" align="right" id="value_total_po_valubk"><? echo  number_format($gr_po_qnty_val,2); ?></th>

                        <th width="80" align="right" id="value_tdupcharge"><? echo number_format($gr_upcharge,0);?></th>
                        <th width="80" align="right" id="total_ex_qty"><? echo number_format($total_ex_qty,0); ?></th>
                        <th width="80" align="right" id="total_exreturn_qty"><?php echo number_format($total_exreturn_qty,0); ?></th>
                        <th width="80" align="right" id="total_cumulative_qty"><?php echo number_format($total_cumulative_qty,0); ?></th>
                        <th width="100" align="right" id="value_total_ex_valu"><? echo number_format($total_ex_valu,2);?></th>

                        <th width="100" align="right"><?=number_format($total_exfact_return_val,2);?></th>
                        <th width="100" align="right"><?=number_format($total_ex_valu-$total_exfact_return_val,2);?></th>


                        <th width="100" align="right" id="value_current_ex_up_charge"><? echo number_format($gr_current_ex_up_charge,2); ?></th>
                        <th width="100" align="right" id="value_current_ex_up_charge_value"><? echo number_format($gr_current_ex_up_charge_value,2); ?></th>

                        <th width="80" align="right" id="total_crtn_qty"><? echo number_format($gr_ttl_carton,0); ?></th>
                        <th width="80" align="right" id="g_total_ex_qtybk"><? echo number_format($gr_ttl_ex_qnty,0);?></th>
                        <th width="100" align="right" id="value_g_total_ex_valbk"><? echo number_format($gr_ttl_ex_qnty_val,2);?></th>

                        <th width="100" align="right" id="value_total_ex_up_charge"><? echo number_format($gr_total_ex_up_charge,2); ?></th>
                        <th width="100" align="right" id="value_total_ex_up_charge_value"><? echo number_format($gr_total_ex_up_charge_value,2); ?></th>

                        <th width="80" align="right" id="g_total_ex_crtnbk"><? echo number_format($gr_ttl_carton_qt,0);?></th>
                        <th width="100" align="right" id="value_sales_minutesbk"><? echo number_format($gr_sales_min);?></th>

                        <th width="80" align="right" id="total_basic_qtybk"><? echo number_format($gr_ttl_basic_qty,0); ?></th>
                        <th width="80" align="right" id="total_eecess_storage_qtybk"><? echo number_format($gr_ttl_short_qty,0);?></th>
                        <th width="100" align="right" id="value_total_eecess_storage_valbk"><? echo number_format($gr_ttl_short_val,0);?></th>
                        <th width="80" id="total_ex_perbk"><? echo number_format($gr_ttl_ex_fac_per,0);?></th>
                        <th width="60" align="right" id="value_cm_per_pcs_totbk"><? echo number_format($gr_ttl_sales_cm,2);?></th>
                        <th width="100">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="130">&nbsp;</th>
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
	else if($reportType==14) // details6 button for norban
	{


		$print_report_format=return_library_array( "select template_name, format_id from lib_report_template where module_id=7 and report_id=86 and is_deleted=0 and status_active=1",'template_name','format_id');



		if($cbo_company_name!=0)
		{
			 $company_cond=" and c.company_name=$cbo_company_name";
		}

		// print_r($exfact_qty_arr_without_current);die();
		$details_report .='<table width="4750" cellspacing="0" cellpadding="0"  border="1"  class="rpt_table" rules="all" id="table_body2" align="left">';
		$i=1;

		if($db_type==0)
		{
			$sql= "SELECT b.id as po_id, max(a.lc_sc_no) as lc_sc_arr_no,
			group_concat(distinct a.invoice_no) as invoice_no,
			group_concat(distinct a.item_number_id) as itm_num_id,
			group_concat(distinct a.foc_or_claim) as foc_or_claim,
			sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
			sum(a.total_carton_qnty) as carton_qnty, max(a.ex_factory_date)  as ex_factory_date,
			group_concat(distinct  a.lc_sc_no) as lc_sc_no,
			max(a.shiping_mode) as shiping_mode,
			a.delivery_mst_id as challan_id,
			d.delivery_floor_id as del_floor,  b.shipment_date, b.po_number,b.po_quantity as po_quantity, (b.unit_price/c.total_set_qnty) as unit_price, b.shiping_status, c.id, c.company_name, c.buyer_name, c.job_no_prefix_num,c.job_no,c.ship_mode , YEAR(c.insert_date) as year, c.style_ref_no, c.style_description, c.set_smv, d.delivery_company_id as del_company,d.source,d.delivery_location_id as del_location ,c.total_set_qnty,a.item_number_id,c.set_break_down,c.order_uom
			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c ,pro_ex_factory_delivery_mst d
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no $company_cond $str_cond $del_comp_cond $del_location_cond $del_floor_cond $shiping_status_cond $source_cond $buyer_conds $internal_ref_cond $search_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active  in(1,2,3) and c.is_deleted=0 and c.status_active  in(1,2,3) and a.delivery_mst_id=d.id
			group by
					b.id , a.delivery_mst_id,d.delivery_floor_id,b.shipment_date, b.po_number,b.po_quantity,c.total_set_qnty,b.unit_price,b.shiping_status, c.id, c.company_name, c.buyer_name, c.job_no_prefix_num,c.job_no, c.ship_mode ,c.insert_date, c.style_ref_no, c.style_description, c.set_smv, d.delivery_company_id, d.delivery_location_id ,d.source,a.item_number_id ,c.set_break_down,c.order_uom
			order by c.buyer_name, b.shipment_date ASC";
		}
		else if($db_type==2)
		{
		 	$sql= "SELECT b.id as po_id,max(a.lc_sc_no) as lc_sc_arr_no,
			LISTAGG(CAST( a.invoice_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.invoice_no) as invoice_no,
			LISTAGG(CAST( a.foc_or_claim AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.foc_or_claim) as foc_or_claim,
			LISTAGG(CAST( a.item_number_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.item_number_id) as itm_num_id,
			sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
			sum(a.total_carton_qnty) as carton_qnty, max(a.ex_factory_date) as ex_factory_date,
			LISTAGG(CAST( a.lc_sc_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.lc_sc_no) as lc_sc_no, max(a.shiping_mode) as shiping_mode,
			a.delivery_mst_id as challan_id,d.delivery_floor_id as del_floor,b.shipment_date, b.po_number,b.po_quantity as po_quantity,(b.unit_price/c.total_set_qnty) as unit_price,b.shiping_status, c.id, c.company_name, c.buyer_name, c.job_no_prefix_num,c.job_no,c.ship_mode ,to_char(c.insert_date,'YYYY') as year, c.style_ref_no, c.style_description, c.set_smv, d.delivery_company_id as del_company,d.source, d.delivery_location_id as del_location,c.total_set_qnty,a.item_number_id,c.set_break_down,c.order_uom
			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c ,pro_ex_factory_delivery_mst d
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no $company_cond $str_cond $del_comp_cond $del_location_cond $del_floor_cond $shiping_status_cond $source_cond $buyer_conds $internal_ref_cond $search_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active  in(1,2,3) and c.is_deleted=0 and c.status_active  in(1,2,3) and a.delivery_mst_id=d.id
			group by
					b.id , a.delivery_mst_id,d.delivery_floor_id,b.shipment_date, b.po_number,b.po_quantity,c.total_set_qnty,b.unit_price,b.shiping_status, c.id, c.company_name, c.buyer_name, c.job_no_prefix_num,c.job_no,c.ship_mode , c.insert_date, c.style_ref_no, c.style_description, c.set_smv, d.delivery_company_id, d.source,d.delivery_location_id ,a.item_number_id ,c.set_break_down,c.order_uom
			order by c.buyer_name, b.shipment_date ASC";
		}
		 //echo $sql;die();
		$sql_result=sql_select($sql);
		$all_po_arr=array();
		$all_job_id_arr=array();
		foreach($sql_result as $k=>$v)
		{
			$all_job_arr[trim($v[csf("job_no")])]=trim($v[csf("job_no")]);
			$all_po_arr[trim($v[csf("po_id")])]=$v[csf("po_id")];
			$all_job_id_arr[$v[csf('id')]] = $v[csf('id')];

		}
		$jobs_ids = implode(',', $all_job_id_arr);
		// echo $jobs_ids;die;
		$condition= new condition();
		$condition->jobid_in("$jobs_ids");
		$condition->init();
   		$commission = new commision($condition);
		// echo $commission->getQuery();die;
   		$commission_sum_arr=$commission->getAmountArray_by_job();

		$inspection_date_arr=return_library_array( "SELECT po_break_down_id, max(inspection_date) as inspection_date from pro_buyer_inspection where status_active=1 and is_deleted=0 ".where_con_using_array($all_po_arr,0,'po_break_down_id')." group by po_break_down_id", "po_break_down_id", "inspection_date");


		$order_item_qnty_sql="SELECT po_break_down_id,item_number_id,sum(order_quantity) as order_quantity from wo_po_color_size_breakdown where status_active  in(1,2,3) and is_deleted=0 ".where_con_using_array($all_job_arr,1,'job_no_mst')."  group by po_break_down_id,item_number_id";
		foreach(sql_select($order_item_qnty_sql) as $key=>$val)
		{
			$order_item_qnty_arr[$val[csf("po_break_down_id")]][$val[csf("item_number_id")]]=$val[csf("order_quantity")];
			$poArr[$val[csf("po_break_down_id")]]=$val[csf("po_break_down_id")];
		}


		//for totoal actual cost............................................start;


		$com_export_sql_result=sql_select("SELECT a.id,a.buyer_id,a.invoice_no,a.shipping_mode,a.lc_sc_id,a.is_lc,b.po_breakdown_id,b.current_invoice_value from com_export_invoice_ship_mst a, com_export_invoice_ship_dtls b where a.id=b.mst_id ".where_con_using_array($all_po_arr,0,'b.po_breakdown_id')."");
		foreach($com_export_sql_result as $row)
		{
			$invoice_array[$row[csf('id')]]=$row[csf('invoice_no')];
			$shipping_mode_array[$row[csf('id')]]=$row[csf('shipping_mode')];
			$lc_sc_id_array[$row[csf('id')]]=$row[csf('lc_sc_id')];
			$lc_sc_type_arr[$row[csf('id')]]=$row[csf('is_lc')];

			$buyer_invoice_value_arr[$row[csf('buyer_id')]]+=$row[csf('current_invoice_value')];
			$po_invoice_value_arr[$row[csf('po_breakdown_id')]]+=$row[csf('current_invoice_value')];

		}


		$lc_num_arr=return_library_array( "SELECT id,export_lc_no from com_export_lc", "id", "export_lc_no"  );
		$sc_num_arr=return_library_array( "SELECT id,contract_no from com_sales_contract", "id", "contract_no"  );



		$com_export_sql_result=sql_select("SELECT (a.total_set_qnty*a.job_quantity) as job_qty,a.total_set_qnty,a.job_quantity,b.job_no,b.cm_cost,b.margin_pcs_set,b.total_cost from wo_pre_cost_dtls b, wo_po_details_master a where b.job_no=a.job_no ".where_con_using_array($all_job_arr,1,'a.job_no')."");
		foreach($com_export_sql_result as $row)
		{
			$tot_cost_arr[$row[csf('job_no')]]=$row[csf('cm_cost')];
			$job_qty_arr[$row[csf('job_no')]]=$row[csf('job_qty')];
			$job_margin_arr[$row[csf('job_no')]]=$row[csf('margin_pcs_set')]*$row[csf('total_set_qnty')];
		}



		if($source_cond)
		$source_cond2=str_replace("d.","a.",$source_cond);
		$challan_mst_arr=array();
		$challan_sql="SELECT a.id,b.delivery_mst_id, a.sys_number_prefix_num,a.sys_number, a.forwarder, a.truck_no, a.driver_name, a.mobile_no, a.dl_no, b.po_break_down_id,b.item_number_id,a.delivery_floor_id,
		(CASE WHEN a.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty,
		b.total_carton_qnty as carton_qnty,a.REMARKS
		from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b
		where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $source_cond2 ".where_con_using_array($all_po_arr,0,'b.po_break_down_id')." ";//  and b.po_break_down_id = 41247
		// echo $challan_sql;die();

		//echo $challan_sql;die;

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
		  	$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['REMARKS']=$row[csf("REMARKS")];


			$exfact_qty_arr[$row[csf("po_break_down_id")]][$row[csf("delivery_mst_id")]]+=$row[csf("ex_factory_qnty")]-$row[csf("ex_factory_return_qnty")];
			$exfact_cartoon_arr[$row[csf("po_break_down_id")]][$row[csf("delivery_mst_id")]]+=$row[csf("carton_qnty")];
			$exfact_return_qty_arr[$row[csf("po_break_down_id")]][$row[csf("delivery_mst_id")]]+=$row[csf("ex_factory_return_qnty")];
			$exfact_qty_arr_without_current[$row[csf("delivery_mst_id")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]]["ex_fact"]+=$row[csf("ex_factory_qnty")]-$row[csf("ex_factory_return_qnty")];

			$exfact_qty_arr_without_current[$row[csf("delivery_mst_id")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]]["carton"]+=$row[csf("carton_qnty")];
		}





		$gr_short_qty=$gr_short_val=$gr_excess_qty=$gr_excess_val=0;

		$gr_po_qnty_pcs=0;$gr_po_qnty_val=0;$gr_po_qnty_val_perc=0;
		$gr_ttl_ex_qnty=0;$gr_ttl_ex_qnty_val=0;$gr_sales_min=0;
		$gr_ttl_carton=0;$gr_ttl_basic_qty=0;$gr_ttl_ex_fac_per=0;$gr_ttl_short_qty=0;
		$gr_ttl_short_val=0;$gr_ttl_sales_cm=0;

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
			$total_exface_qnty=$txt_date_from."*".$txt_date_to."_". 1;
			if($break_id==0) $break_id=$row[csf('po_id')]; else $break_id=$break_id.",".$row[csf('po_id')];
			if($sc_lc_id==0) $sc_lc_id=$row[csf('lc_sc_no')]; else $sc_lc_id=$sc_lc_id.",".$row[csf('lc_sc_no')];

			$invoce_id_arr=array_unique(explode(",",$row[csf('invoice_no')]));
		    $challan_id=array_unique(explode(",",$row[csf("challan_id")]));
 			$floor_id=array_unique(explode(",",$row[csf("del_floor")]));
			$challan_no=""; $forwarder=""; $vehi_no=""; $dirver_info="";  $floor_no="";

			$todate=date("d-M")."-".substr(date("Y"), 2) ;
 			$todate=explode("-", $todate);
 			$todate=$todate[0]."-".strtoupper($todate[1])."-".$todate[2];
 			$diff=datediff("d",$todate, $row[csf("shipment_date")])-2;
		    $diff_color=($diff>0) ?" color:black;":"color:red;";



			list($first_button)=explode(',',$print_report_format[$row[csf("company_name")]]);

			/*echo "<pre>";
			print_r($first_button); die;*/

			$remarksArr=array();
			foreach($challan_id as $val)
			{

				$fv=$first_button.",".$val.",".$row[csf("company_name")].",".$row[csf("del_company")].",'".$row[csf('ex_factory_date')]."'";
				$challanFunction='<a href="##" onclick="fn_generate_print('.$fv.')">'.$challan_mst_arr[$val][$row[csf('po_id')]]['challan'].'</a>';
				if($challan_no==""){$challan_no=$challanFunction;}else {$challan_no.=', '.$challanFunction;}

				if($floor_no=="") $floor_no=$floor_library[$challan_mst_arr[$val][$row[csf('po_id')]]['floor']]; else $floor_no.=','.$floor_library[$challan_mst_arr[$val][$row[csf('po_id')]]['floor']];

				$remarksArr[]=$challan_mst_arr[$val][$row[csf('po_id')]]['REMARKS'];



				if($forwarder=="") $forwarder=$supp_library[$challan_mst_arr[$val][$row[csf('po_id')]]['forwarder']]; else $forwarder.=','.$supp_library[$challan_mst_arr[$val][$row[csf('po_id')]]['forwarder']];
				if($vehi_no=="") $vehi_no=$challan_mst_arr[$val][$row[csf('po_id')]]['truck_no']; else $vehi_no.=','.$challan_mst_arr[$val][$row[csf('po_id')]]['truck_no'];
				if($dirver_info=="") $dirver_info="Name: ".$challan_mst_arr[$val][$row[csf('po_id')]]['driver_name']."<br>Mob No: ".$challan_mst_arr[$val][$row[csf('po_id')]]['mobile_no']."<br>DL No: ".$challan_mst_arr[$val][$row[csf('po_id')]]['dl_no']; else $dirver_info.=','."Name: ".$challan_mst_arr[$val][$row[csf('po_id')]]['driver_name']."<br>Mob No: ".$challan_mst_arr[$val][$row[csf('po_id')]]['mobile_no']."<br>DL No: ".$challan_mst_arr[$val][$row[csf('po_id')]]['dl_no'];
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


			$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";


			$comapny_id=$row[csf("company_name")];
			$delv_comp=($row[csf("source")]!=3)?  $company_library[$row[csf("del_company")]] : $supp_library[$row[csf("del_company")]];

			$onclick=" change_color('tr_".$i."','".$bgcolor."')";
			$details_report .='<tr bgcolor="'.$bgcolor.'" onclick="'.$onclick.'" id="tr_'.$i.'">';
			$details_report .='<td width="40" align="center">'.$i.'</td>
								<td width="150" align="center" ><p>'.$company_library[$row[csf("company_name")]].'</p></td>
								<td width="60" align="center" ><p>'.$row[csf("job_no_prefix_num")].'</p></td>
								<td width="60" align="center" ><p>'.$row[csf("year")].'</p></td>
								<td width="100" align="center" ><p>'.$buyer_arr[$row[csf("buyer_name")]].'</p></td>
								<td width="110" align="center"><p>'.$row[csf("po_number")].'</p></td>
								<td width="125" align="center" ><p>'.$delv_comp.'</p></td>
								<td width="125" align="center" ><p>'.$location_library[$row[csf("del_location")]].'</p></td>
								<td width="125" align="center" ><p>'.$floor_no.'</p></td>
								<td width="120" align="center"><p>'.$challan_no.'</p></td>
								<td width="100" align="center"><p>';
								$inv_id=""; $lc_sc_no=""; $ship_mode="";
								foreach($invoce_id_arr as $invoice_id)
								{
									$invoiceFunction='<a href="##" onclick="generate_print_button('.$invoice_id.')">'.$invoice_array[$invoice_id].'</a>';
									if($inv_id==""){$inv_id=$invoiceFunction; }else{ $inv_id=$inv_id.",".$invoiceFunction;}

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
								<td width="100"><p>'.$row[csf("style_ref_no")].'</p></td>
								<td width="100"><p>'.$row[csf("style_description")].'</p></td>
								<td width="110" align="center"><p>';
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

								$total_ex_fact_qty=$exfact_qty_arr_without_current[$row[csf("challan_id")]][$row[csf("po_id")]][$row[csf("item_number_id")]]["ex_fact"] ;

								$total_cartoon_qty=$exfact_qty_arr_without_current[$row[csf("challan_id")]][$row[csf("po_id")]][$row[csf("item_number_id")]]["carton"] ;
								//$po_quantity=$row[csf("po_quantity")];
								$po_quantity=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]];
								$unit_price=$row[csf("unit_price")];
								$current_ex_Fact_Qty=$row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]][$row[csf("challan_id")]];
								$basic_qnty=($total_ex_fact_qty*$item_smv)/$basic_smv_arr[$row[csf("company_name")]];
								$cm_per_pcs_tot+=($cm_per_pcs*$current_ex_Fact_Qty);
								$short_excess=$total_ex_fact_qty-$po_quantity;

								//$excess_msg=($short_excess>0) ?" color:green;":"color:black;";
								//$excess_val_msg=(($total_ex_fact_qty*$unit_price)-$po_quantity>0) ?" color:green;":"color:black;";
								$ttl_ex_qty=($total_ex_fact_qty/$po_quantity)*100;
								$ttl_ex_qty_msg=($ttl_ex_qty>100) ?" color:green;":"color:black;";

								$short_qty=($po_quantity>$total_ex_fact_qty)?($po_quantity-$total_ex_fact_qty):0;
								$short_val=$short_qty*$unit_price;

								$excess_qty=($po_quantity<$total_ex_fact_qty)?($total_ex_fact_qty-$po_quantity):0;
								$excess_val=$excess_qty*$unit_price;

								$temp_focclaim_arr=array();
								foreach(explode(',',$row[csf("foc_or_claim")]) as $foc_id){
									$temp_focclaim_arr[$foc_id]=$foc_claim_arr[$foc_id];
								}
								$row[csf("foc_or_claim")]=implode(',',$temp_focclaim_arr);


			$details_report .=$item_name_all.'</p></td>
								<td width="80" align="center"><p>'.$item_smv.'</p></td>
								<td width="70" align="center"><p>'.change_date_format($row[csf("shipment_date")]).'</p></td>
								<td width="70" align="center"><a href="##" onclick="openmypage_ex_date('.$comapny_id.",'".$row[csf('po_id')]."','".$row[csf('item_number_id')]."','".$ex_fact_date_range."','ex_date_popup','".$row[csf('challan_id')]."'".",'1'".')">'.change_date_format($row[csf('ex_factory_date')]).'</a></td>
								<td width="100" align="center"><p>'.$shipment_mode[$row[csf('ship_mode')]].'</p></td>
								<td width="70" align="center"><p>'.$shipment_mode[$row[csf("shiping_mode")]].'</p></td>
								<td width="100" align="center"><p>'.$row[csf("foc_or_claim")].'</p></td>

								<td width="60" align="center" style="'.$diff_color.'"><p>('.$diff.')</p></td>
								<td width="100" align="center"><p>'.$unit_of_measurement[$row[csf('order_uom')]].'</p></td>
								<td width="80" align="right"><p>'. number_format($po_quantity,0,'', '').'</p></td>
								<td width="70" align="right"><p>'. number_format($unit_price,4).'</p></td>
								<td width="100" align="right"><p>'. number_format(($po_quantity*$unit_price),2).'</p></td>
								<td width="100" align="right"><p>'. number_format((($po_quantity*$unit_price)-$commission_sum_arr[$row[csf("job_no")]]),2).'</p></td>
								<td width="80" align="right"><p><a href="##" onclick="openmypage_ex_date('.$comapny_id.",'".$row[csf('po_id')]."','".$row[csf('item_number_id')]."','".$ex_fact_date_range."','ex_date_popup','".$row[csf('challan_id')]."'".",'1'".')">'. number_format($current_ex_Fact_Qty,0,'.', '').'</a></p></td>
								<td width="100" align="right"><p>'. number_format( $current_ex_fact_value=$current_ex_Fact_Qty*$unit_price,2).'</p></td>
								<td width="80" align="right"><p>'. number_format($row[csf("carton_qnty")],0,'.', '').'</p></td>
								<td width="80" align="right"><p><a href="##" onclick="openmypage_ex_date('.$comapny_id.",'".$row[csf('po_id')]."','".$row[csf('item_number_id')]."','".$total_exface_qnty."','ex_date_popup','".$row[csf('challan_id')]."'".",'1'".')">'.number_format($total_ex_fact_qty,0,'.', '').'</a></p></td>
								<td width="100" align="right"><p>'. number_format($total_ex_fact_value=$total_ex_fact_qty*$unit_price,2).'</p></td>


								<td width="100" align="right"><p>'.number_format(($job_margin_arr[$row[csf('job_no')]]*$po_quantity),2).'</p></td>
								<td width="100" align="right"><p>'.number_format((($po_quantity*$unit_price)-array_sum($tot_cost_arr[$row[csf('job_no')]]))/$po_quantity*$total_ex_fact_qty,2).'</p></td>
								<td width="100" align="right"><p>'.number_format(($po_quantity*$unit_price),2).'</p></td>
								<td width="100" align="right"><p>'.number_format($po_invoice_value_arr[$row[csf('po_id')]],2).'</p></td>



								<td width="80" align="right"><p>'.number_format($total_cartoon_qty,0,'.', '').'</p></td>
								<td width="100" align="right" title="Total Ex.Qty*SMV"><p>'. number_format($total_sales_minutes=$total_ex_fact_qty*$item_smv).'</p></td>
								<td width="80" align="right"><p>'.number_format($basic_qnty,0,'','').'</p></td>


								<td align="center" style="'.$ttl_ex_qty_msg.'" width="80"><p>'. number_format($total_ex_fact_qty_parcent=($total_ex_fact_qty/$po_quantity)*100,0).'</p></td>


								<td width="80" align="right"><p>'.$short_qty.'</p></td>
								<td width="80" align="right"><p>'.number_format($short_val,2).'</p></td>
								<td width="80" align="right"><p>'.$excess_qty.'</p></td>
								<td width="80" align="right"><p>'.number_format($excess_val,2).'</p></td>


								<td width="60" align="center" title="CM per pcs: '.number_format($cm_per_pcs,4).'"><p>'.number_format($cm_per_pcs*$total_ex_fact_qty,2).'</p></td>
								<td width="100" align="center"><p>'.$forwarder.'</p></td>
								<td width="80" align="center"><p>'.$vehi_no.'</p></td>
								<td width="130"><p>'.$dirver_info.'</p></td>
								<td width="70" align="center"><p>'.(change_date_format($inspection_date_arr[$row[csf('po_id')]]) == '0000-00-00' || change_date_format($inspection_date_arr[$row[csf('po_id')]]) == '' ? '' : change_date_format(change_date_format($inspection_date_arr[$row[csf('po_id')]]))).'</p></td>
								<td align="center" width="100"><p>'.$shipment_status[$row[csf('shiping_status')]].'</p></td>
								<td><p>'.implode(', ',$remarksArr).'</p></td>
							</tr>';


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
			$g_sales_minutes+=$total_sales_minutes;
			// $g_sales_minutes+=$current_ex_Fact_Qty*$item_smv;

			//$total_eecess_storage_qty+=$excess_shortage_qty;
			//$total_eecess_storage_val+=$excess_shortage_value;

			if($po_check_arr[$row[csf("challan_id")]][$row[csf("po_id")]][$row[csf("item_number_id")]]=="")
			{
				$po_check_arr[$row[csf("challan_id")]][$row[csf("po_id")]][$row[csf("item_number_id")]]=$row[csf("po_id")];
				$master_data[$row[csf("buyer_name")]]['b_id']=$row[csf("buyer_name")];
				$master_data[$row[csf("buyer_name")]]['org_po_qnty'] +=$row[csf("po_quantity")];
				$master_data[$row[csf("buyer_name")]]['po_qnty'] +=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]];
				$master_data[$row[csf("buyer_name")]]['po_value'] +=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]]*$row[csf("unit_price")];
				$master_data[$row[csf("buyer_name")]]['basic_qnty'] +=$basic_qnty;
				$master_data[$row[csf("buyer_name")]]['total_ex_fact_qty'] +=$total_ex_fact_qty;
				$master_data[$row[csf("buyer_name")]]['total_ex_fact_value'] +=$total_ex_fact_qty*$row[csf("unit_price")];
				$master_data[$row[csf("buyer_name")]]['sales_min'] += $item_smv*$total_ex_fact_qty;


				$master_data[$row[csf("buyer_name")]]['short_qty'] += $short_qty;
				$master_data[$row[csf("buyer_name")]]['short_val'] += $short_val;
				$master_data[$row[csf("buyer_name")]]['excess_qty'] += $excess_qty;
				$master_data[$row[csf("buyer_name")]]['excess_val'] += $excess_val;



				$gr_short_qty+=$short_qty;
				$gr_short_val+=$short_val;
				$gr_excess_qty+=$excess_qty;
				$gr_excess_val+=$excess_val;

				$gr_po_qnty_pcs+=$po_quantity;
	  			$gr_po_qnty_val+=$po_quantity*$unit_price;
	  			$net_po_qnty_val+=($po_quantity*$unit_price)-$commission_sum_arr[$row[csf("job_no")]];
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

			$i++;
			$item_name_all="";


			$pre_costing_margin_arr[$row[csf("buyer_name")]][$row[csf('job_no')]]=$job_margin_arr[$row[csf('job_no')]]*$job_qty_arr[$row[csf('job_no')]];
			$pre_costing_cm_arr[$row[csf("buyer_name")]][$row[csf('job_no')]]=$tot_cost_arr[$row[csf('job_no')]];




		}
		$pp=$i;
 		$details_report .='</table>';
		foreach($master_data as $rows)
		{
			$total_po_val+=$rows["po_value"];
		}

		?>
        <div style="width:3100x;">
            <div style="width:1820px" id="summary">
                <table width="1790"  cellspacing="0"  align="center">
                    <tr>
                        <td align="center" colspan="17" class="form_caption">
                            <strong style="font-size:16px;">Company Name:<? echo  $company_library[$cbo_company_name] ;?></strong>
                        </td>
                    </tr>
                    <tr class="form_caption">
                        <td colspan="17" align="center" class="form_caption"> <strong style="font-size:15px;">Ex-Factory Report</strong></td>
                    </tr>
                     <tr align="center">
                        <td colspan="17" align="center" class="form_caption"> <strong style="font-size:15px;">Total Summary</strong></td>
                    </tr>
              </table>
              <table width="2190" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1">
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
                        <th width="100">Total Ex-Fact. Value %</th>
                        <th width="100">Short/Balance Qty</th>
                        <th width="100">Short/Balance Value</th>
                        <th width="100">Excess Qty</th>
                        <th width="100">Excess Value</th>
                        <th width="100">Pre-Costing Margin</th>
                        <th width="100">Actual Margin</th>
                        <th width="100">Pre-Costing FOB Value</th>
                        <th>Commercial Invoice FOB Value</th>
                    </thead>
                 <?
                 $m=1;
                $grand_sales_minute =0;
                foreach($master_data as $rows)
                {
                   	$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                     ?>
                  	<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" >
                        <td align="center"><? echo $m; ?></td>
                        <td>
                        <p><?
                        echo $buyer_arr[$rows[b_id]]. $master_data[$rows[b_id]]['in_sub'];
                        ?></p>
                        </td>
                        <td align="right"><p><?  $po_quantity_org=$rows["org_po_qnty"];echo number_format($po_quantity_org,0); $total_buyer_org_po_quantity+=$po_quantity_org; ?></p></td>
                        <td align="right"><p><?  $po_quantity=$rows[po_qnty];echo number_format($po_quantity,0); $total_buyer_po_quantity+=$po_quantity; ?></p></td>
                        <td align="right" ><p  id="value_<? echo $i ; ?>"><? $buyer_po_value=$rows["po_value"]; echo number_format($buyer_po_value,2 ,'.', '');  $total_buyer_po_value+=$buyer_po_value; ?></p></td>
                        <td align="right">
                         <? echo number_format(($buyer_po_value/$total_po_val)*100,2,'.','');$parcentages+=($buyer_po_value/$total_po_val)*100; ?>
                        </td>
                        <td align="right">
                        <p><?
                         $current_ex_Fact_Qty=$rows[ex_factory_qnty];  echo number_format($current_ex_Fact_Qty,0,'',''); $total_current_ex_Fact_Qty +=$current_ex_Fact_Qty;
                        ?></p>
                        </td>
                        <td align="right">
                        <p><?
                        $current_ex_fact_value=$rows[ex_factory_value]; echo number_format($current_ex_fact_value,2,'.',''); $total_current_ex_fact_value+=$current_ex_fact_value;
                        ?></p>
                        </td>
                        <td align="right">
                        <p><?
                         $total_ex_fact_qty=$rows[total_ex_fact_qty]; echo number_format($total_ex_fact_qty,0,'',''); $mt_total_ex_fact_qty+=$total_ex_fact_qty;
                        ?></p>
                        </td>
                        <td align="right">
                        <p><?
                         $total_ex_fact_value=$rows[total_ex_fact_value];  echo  number_format($total_ex_fact_value,2,'.',''); $mt_total_ex_fact_value+=$total_ex_fact_value;
                        ?></p>
                        </td>
                        <td align="right"><p>
                        	<?
                        	echo $g_sales_min+= number_format($rows["sales_min"],0,'','');

                        	?>
                        </p></td>
                        <td align="right">
                        <p><?
                         $buyer_basic_qnty=$rows["basic_qnty"];  echo number_format($buyer_basic_qnty,0,'',''); $total_buyer_basic_qnty +=$buyer_basic_qnty;
                        ?></p>
                        </td>

                        <td align="right">
                            <p>
                                <?
                                    $total_ex_fact_value_parcentage=($total_ex_fact_value/$buyer_po_value)*100;
                                    echo number_format($total_ex_fact_value_parcentage,0);
                                ?> %
                            </p>
                        </td>

                        <td align="right"><?=$rows[short_qty];?></td>
                        <td align="right"><?=number_format($rows[short_val],2);?></td>
                        <td align="right"><?=$rows[excess_qty];?></td>
                        <td align="right"><?=number_format($rows[excess_val],2);?></td>

                        <td align="right"><? $pre_costing=array_sum($pre_costing_margin_arr[$rows[b_id]]);echo number_format($pre_costing,2);?></td>
                        <td align="right"><? echo number_format(($buyer_po_value-array_sum($pre_costing_cm_arr[$rows[b_id]]))/$po_quantity*$total_ex_fact_qty,2);?></td>
                        <td align="right"><? echo number_format($buyer_po_value,2);?></td>
                        <td align="right"><? echo number_format($buyer_invoice_value_arr[$rows[b_id]],2);?></td>
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
                        <th align="right"><?=$gr_short_qty;?></th>
                        <th align="right"><?=number_format($gr_short_val,2);?></th>
                        <th align="right"><?=$gr_excess_qty;?></th>
                        <th align="right"><?=number_format($gr_excess_val,2);?></th>
                        <th align="right"></th>
                        <th align="right"></th>
                        <th align="right"></th>
                        <th align="right"></th>
                    </tfoot>
                </table>
            </div>
            <br />
            <div>
                <table width="4750"  >
                    <tr>
                    <td colspan="34" class="form_caption"><strong style="font-size:16px;">Shipped Out Order Details Report</strong></td>
                    </tr>
                </table>
                <table width="4750" border="1" class="rpt_table" rules="all" id="table_header_2">
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
                        <th width="100">FOC/Claim</th>
                        <th width="60">Days in Hand</th>
                        <th width="100">UOM</th>
                        <th width="80">PO Qtny. (pcs)</th>
                        <th width="70">Unit Price</th>
                        <th width="100">Gross PO Value</th>
                        <th width="100">Net PO Value</th>
                        <th width="80">Current Ex-Fact. Qty (pcs)</th>
                        <th width="100">Current Ex-Fact. Value</th>
                        <th width="80">Current Carton Qty</th>
                        <th width="80">Total Ex-Fact. Qty.</th>
                        <th width="100">Total Ex-Fact. Value</th>

                        <th width="100">Pre-Costing Margin</th>
                        <th width="100" title="((Buyer Total FOB Value - Buyer Total  Raw Material Booking Value)/Buyer Total Order Qty*Buyer Total Ex-Factory Qty.)">Actual Margin</th>
                        <th width="100">Pre-Costing FOB Value</th>
                        <th width="100">Commercial Invoice FOB Value</th>

                        <th width="80">Total Carton Qty</th>
                        <th width="100">Sales Minute</th>
                        <th width="80">Total Ex-Fact. (Basic Qty)</th>
                        <th width="80">Total Ex-Fact. Qty. %</th>

                        <th width="80">Short/Balance Qty</th>
                        <th width="80">Short/Balance Value</th>
                        <th width="80">Excess Qty</th>
                        <th width="80">Excess Value</th>


                        <th width="60">Sales CM</th>
                        <th width="100">C & F Name</th>
                        <th width="80">Vehicle No</th>
                        <th width="130">Driver Info</th>
                        <th width="70">Inspection Date</th>
                        <th width="100">Ex-Fact Status</th>
                        <th>Remarks</th>
                    </thead>
                </table>
            <div style="width:4770px; overflow-y:scroll; overflow-x:hidden; max-height:300px;"  id="scroll_body" >
            <? echo $details_report; ?>
            </div>
            <table width="4750" cellspacing="0" cellpadding="0"  border="1"  class="rpt_table" rules="all" id="table_footer">
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
                        <th width="100">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="100" align="right"><strong>Total</strong></th>
                        <th width="80" id="total_po_qtybk" align="right"><? echo  number_format($gr_po_qnty_pcs,0);?></th>
                        <th width="70" align="right">&nbsp;</th>
                        <th width="100" align="right" id="value_total_po_valubk"><? echo  number_format($gr_po_qnty_val,2); ?></th>
                        <th width="100" align="right" id="value_total_po_valu_net"><? echo  number_format($net_po_qnty_val,2); ?></th>
                        <th width="80" align="right" id="total_ex_qty"><? echo number_format($total_ex_qty,0); ?></th>
                        <th width="100" align="right" id="value_total_ex_valu"><? echo number_format($total_ex_valu,2);?></th>
                        <th width="80" align="right" id="total_crtn_qty"><? echo number_format($gr_ttl_carton,0); ?></th>
                        <th width="80" align="right" id="g_total_ex_qtybk"><? echo number_format($gr_ttl_ex_qnty,0);?></th>
                        <th width="100" align="right" id="value_g_total_ex_valbk"><? echo number_format($gr_ttl_ex_qnty_val,2);?></th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="80" align="right" id="g_total_ex_crtnbk"><? echo number_format($gr_ttl_carton_qt,0);?></th>
                        <th width="100" align="right" id="value_sales_minutesbk"><? echo number_format($gr_sales_min);?></th>
                        <th width="80" align="right" id="total_basic_qtybk"><? echo number_format($gr_ttl_basic_qty,0); ?></th>
                        <th width="80" id="total_ex_perbk"><? echo number_format($gr_ttl_ex_fac_per,0);?></th>
                        <th width="80" id="total_short_qty">&nbsp;</th>
                        <th width="80" id="value_total_short">&nbsp;</th>
                        <th width="80" id="total_excess_qty">&nbsp;</th>
                        <th width="80" id="value_total_excess">&nbsp;</th>
                        <th width="60" align="right"><? echo number_format($gr_ttl_sales_cm,2);?></th>
                        <th width="100">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="130">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th>&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
            </div>
        </div>

		<?
	}
	else if($reportType==15)//Details 7
	{
		$tot_cost_arr = return_library_array("select job_no, cm_cost from wo_pre_cost_dtls","job_no","cm_cost");
		$inspection_date_arr=return_library_array( "select po_break_down_id, max(inspection_date) as inspection_date from pro_buyer_inspection where status_active=1 and is_deleted=0 group by po_break_down_id", "po_break_down_id", "inspection_date");
		$invoice_array=return_library_array( "select id,invoice_no from com_export_invoice_ship_mst", "id", "invoice_no"  );
		$shipping_mode_array=return_library_array( "select id,shipping_mode from com_export_invoice_ship_mst", "id", "shipping_mode"  );
		$lc_sc_type_arr=return_library_array( "select id,is_lc from com_export_invoice_ship_mst", "id", "is_lc"  );
		$lc_sc_id_array=return_library_array( "select id,lc_sc_id from com_export_invoice_ship_mst", "id", "lc_sc_id"  );
		$lc_num_arr=return_library_array( "select id,export_lc_no from com_export_lc", "id", "export_lc_no"  );
		$sc_num_arr=return_library_array( "select id,contract_no from com_sales_contract", "id", "contract_no"  );

		$forwarder_arr=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name");


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

		$i=1;

		if($db_type==0)
		{
			$sql= "SELECT b.id as po_id, max(a.lc_sc_no) as lc_sc_arr_no, group_concat(distinct a.invoice_no) as invoice_no, group_concat(distinct a.item_number_id) as itm_num_id, group_concat(distinct a.foc_or_claim) as foc_or_claim,
			sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty, sum(a.total_carton_qnty) as carton_qnty, max(a.ex_factory_date)  as ex_factory_date, group_concat(distinct  a.lc_sc_no) as lc_sc_no, max(a.shiping_mode) as shiping_mode, a.delivery_mst_id as challan_id, d.delivery_floor_id as del_floor,  b.shipment_date, b.po_number, b.po_quantity as po_quantity, (b.unit_price/c.total_set_qnty) as unit_price, b.up_charge, b.shiping_status, c.id, c.company_name, c.buyer_name, c.client_id, c.job_no_prefix_num,c.job_no,c.ship_mode , YEAR(c.insert_date) as year, c.style_ref_no, c.style_description, c.set_smv, d.delivery_company_id as del_company,d.source,d.delivery_location_id as del_location ,c.total_set_qnt,c.set_break_down,c.order_uom,c.DESIGN_SOURCE_ID
			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c ,pro_ex_factory_delivery_mst d
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no $company_cond $str_cond $del_comp_cond $del_location_cond $del_floor_cond $shiping_status_cond $source_cond $buyer_conds $internal_ref_cond $search_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active  in(1,2,3) and c.is_deleted=0 and c.status_active  in(1,2,3) and a.delivery_mst_id=d.id
			group by
					b.id, a.delivery_mst_id, d.delivery_floor_id, b.shipment_date, b.po_number, b.po_quantity, c.total_set_qnty, b.unit_price, b.up_charge, b.shiping_status, c.id, c.company_name, c.buyer_name, c.client_id, c.job_no_prefix_num, c.job_no, c.ship_mode, c.insert_date, c.style_ref_no, c.style_description, c.set_smv, d.delivery_company_id, d.delivery_location_id, d.source, c.set_break_down, c.order_uom,c.DESIGN_SOURCE_ID
			order by c.buyer_name, b.shipment_date ASC";
		}
		else if($db_type==2)
		{
		 	$sql= "SELECT b.id as po_id, max(a.lc_sc_no) as lc_sc_arr_no, LISTAGG(CAST( a.invoice_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.invoice_no) as invoice_no, LISTAGG(CAST( a.foc_or_claim AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.foc_or_claim) as foc_or_claim, LISTAGG(CAST( a.item_number_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.item_number_id) as itm_num_id, sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty, sum(a.total_carton_qnty) as carton_qnty, max(a.ex_factory_date) as ex_factory_date, LISTAGG(CAST( a.lc_sc_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.lc_sc_no) as lc_sc_no, max(a.shiping_mode) as shiping_mode, a.delivery_mst_id as challan_id, d.delivery_floor_id as del_floor, b.shipment_date, b.po_number, b.po_quantity as po_quantity, (b.unit_price/c.total_set_qnty) as unit_price, b.up_charge, b.shiping_status, c.id, c.company_name, c.buyer_name, c.client_id, c.job_no_prefix_num, c.job_no, c.ship_mode, to_char(c.insert_date,'YYYY') as year, c.style_ref_no, c.style_description, c.set_smv, d.delivery_company_id as del_company, d.source, d.delivery_location_id as del_location, c.total_set_qnty, c.set_break_down, c.order_uom,c.DESIGN_SOURCE_ID,a.actual_po,d.lock_no,d.gp_no
			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c ,pro_ex_factory_delivery_mst d
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no $company_cond $str_cond $del_comp_cond $del_location_cond $del_floor_cond $shiping_status_cond $source_cond $buyer_conds $internal_ref_cond $search_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,2,3) and c.is_deleted=0 and c.status_active in(1,2,3) and a.delivery_mst_id=d.id
			group by
					b.id, a.delivery_mst_id, d.delivery_floor_id, b.shipment_date, b.po_number, b.po_quantity, c.total_set_qnty, b.unit_price, b.up_charge, b.shiping_status, c.id, c.company_name, c.buyer_name, c.client_id, c.job_no_prefix_num, c.job_no, c.ship_mode, c.insert_date, c.style_ref_no, c.style_description, c.set_smv, d.delivery_company_id, d.source, d.delivery_location_id, c.set_break_down, c.order_uom,c.DESIGN_SOURCE_ID,a.actual_po,d.lock_no,d.gp_no
			order by c.buyer_name, b.shipment_date ASC";
		}
		// echo $sql;die();
		$sql_result=sql_select($sql);
		$poExQtyArray = array();
		$poChkArray = array();
		foreach($sql_result as $k=>$v)
		{
			$po_id_array[trim($v[csf("po_id")])] = trim($v[csf("po_id")]);
			if(!in_array($v[csf("po_id")], $poChkArray))
			{
				$poExQtyArray[$v[csf("po_id")]]['poQty'] += $v[csf("po_quantity")];
				$poChkArray[$v[csf("po_id")]] = $v[csf("po_id")];
			}
			$poExQtyArray[$v[csf("po_id")]]['exQty'] += $v[csf("ex_factory_qnty")];
		}
		// echo "<pre>";print_r($poExQtyArray);die();
		// $all_job="'".implode("','", array_unique($po_id_array))."'";
		$po_id_cond=where_con_using_array($po_id_array,0,"po_break_down_id");
		$actual_po_arr=return_library_array( "select id, acc_po_no  from  wo_po_acc_po_info where status_active=1  $po_id_cond",'id','acc_po_no');
		$order_item_qnty_sql="SELECT po_break_down_id,item_number_id,sum(order_quantity) as order_quantity from wo_po_color_size_breakdown where status_active  in(1,2,3) and is_deleted=0 $po_id_cond group by po_break_down_id,item_number_id";
		// echo $order_item_qnty_sql;
		foreach(sql_select($order_item_qnty_sql) as $key=>$val)
		{
			$order_item_qnty_arr[$val[csf("po_break_down_id")]][$val[csf("item_number_id")]]=$val[csf("order_quantity")];
		}
		$gr_po_qnty_pcs=0; $gr_po_qnty_val=0; $gr_po_qnty_val_perc=0; $gr_ttl_ex_qnty=0; $gr_ttl_ex_qnty_val=0; $gr_sales_min=0; $gr_ttl_carton=0; $gr_ttl_basic_qty=0; $gr_ttl_ex_fac_per=0; $gr_ttl_short_qty=0; $gr_ttl_short_val=0; $gr_ttl_sales_cm=0;

		//$po_exist_arr=array();
		$po_wise_ttl_ex_qty = array();
		$po_wise_ttl_up_charge = array();
		$data_array = array();
		$master_data = array();
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

			foreach($challan_id as $val)
			{
				if($challan_no=="") $challan_no=$challan_mst_arr[$val][$row[csf('po_id')]]['challan']; else $challan_no.=','.$challan_mst_arr[$val][$row[csf('po_id')]]['challan'];
				if($floor_no=="") $floor_no=$floor_library[$challan_mst_arr[$val][$row[csf('po_id')]]['floor']]; else $floor_no.=','.$floor_library[$challan_mst_arr[$val][$row[csf('po_id')]]['floor']];

				if($forwarder=="") $forwarder=$forwarder_arr[$challan_mst_arr[$val][$row[csf('po_id')]]['forwarder']]; else $forwarder.=','.$forwarder_arr[$challan_mst_arr[$val][$row[csf('po_id')]]['forwarder']];
				if($vehi_no=="") $vehi_no=$challan_mst_arr[$val][$row[csf('po_id')]]['truck_no']; else $vehi_no.=','.$challan_mst_arr[$val][$row[csf('po_id')]]['truck_no'];
				if($dirver_info=="") $dirver_info="Name: ".$challan_mst_arr[$val][$row[csf('po_id')]]['driver_name']."<br>Mob No: ".$challan_mst_arr[$val][$row[csf('po_id')]]['mobile_no']."<br>DL No: ".$challan_mst_arr[$val][$row[csf('po_id')]]['dl_no']; else $dirver_info.=','."Name: ".$challan_mst_arr[$val][$row[csf('po_id')]]['driver_name']."<br>Mob No: ".$challan_mst_arr[$val][$row[csf('po_id')]]['mobile_no']."<br>DL No: ".$challan_mst_arr[$val][$row[csf('po_id')]]['dl_no'];
			}
		    $set_break_down=explode("__", $row[csf("set_break_down")]);
			foreach($set_break_down as $k=>$v)
			{
				if($v)
				{
					$val=explode("_", $v);
					if( trim($val[0])== $row[csf("itm_num_id")] )
					{
						$item_smv=$val[2];
					}
				}
			}

			$current_ex_up_charge = 0;
			$current_ex_up_charge_value = 0;

			$total_ex_up_charge = 0;
			$total_ex_up_charge_value = 0;

			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$comapny_id=$row[csf("company_name")];
			$delv_comp=($row[csf("source")]!=3)?  $company_library[$row[csf("del_company")]] : $supp_library[$row[csf("del_company")]];

			$onclick=" change_color('tr_".$i."','".$bgcolor."')";

			$inv_id=""; $lc_sc_no=""; $ship_mode="";
			foreach($invoce_id_arr as $invoice_id)
			{
				if($inv_id=="") $inv_id=$invoice_array[$invoice_id]; else $inv_id=$inv_id.",".$invoice_array[$invoice_id];
				if($ship_mode=="") $ship_mode=$shipment_mode[$shipping_mode_array[$invoice_id]]; else $ship_mode=$ship_mode.",".$shipment_mode[$shipping_mode_array[$invoice_id]];
				if($lc_sc_type_arr[$invoice_id]==1)
				{
					if($lc_sc_no=="") $lc_sc_no=$lc_num_arr[$lc_sc_id_array[$invoice_id]]; else $lc_sc_no.=",".$lc_num_arr[$lc_sc_id_array[$invoice_id]];
				}
				else
				{
					if($lc_sc_no=="") $lc_sc_no=$sc_num_arr[$lc_sc_id_array[$invoice_id]]; else $lc_sc_no.=",".$sc_num_arr[$lc_sc_id_array[$invoice_id]];
				}
			}


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

			$total_ex_fact_qty=$exfact_qty_arr_without_current[$row[csf("po_id")]][$row[csf("itm_num_id")]]["ex_fact"] ;

			$total_cartoon_qty=$exfact_qty_arr_without_current[$row[csf("po_id")]][$row[csf("itm_num_id")]]["carton"] ;
			//$po_quantity=$row[csf("po_quantity")];
			$po_quantity=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("itm_num_id")]];
			$unit_price=$row[csf("unit_price")];
			$current_ex_Fact_Qty=$row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]][$row[csf("challan_id")]];
			$basic_qnty=($total_ex_fact_qty*$item_smv)/$basic_smv_arr[$row[csf("company_name")]];
			$cm_per_pcs_tot+=($cm_per_pcs*$current_ex_Fact_Qty);
			$short_excess=$total_ex_fact_qty-$po_quantity;

			$excess_msg=($short_excess>0) ?" color:green;":"color:black;";
			$excess_val_msg=(($total_ex_fact_qty*$unit_price)-$po_quantity>0) ?" color:green;":"color:black;";
			$ttl_ex_qty=($total_ex_fact_qty/$po_quantity)*100;
			$ttl_ex_qty_msg=($ttl_ex_qty>100) ?" color:green;":"color:black;";

			// ============================ calculate upcharge ===============================
			$po_wise_ttl_ex_qty[$row[csf("po_id")]] += $current_ex_Fact_Qty;
			// echo $poExQtyArray[$row[csf("po_id")]]['poQty'] ."<=". $po_wise_ttl_ex_qty[$row[csf("po_id")]]."<br>";

			if($poExQtyArray[$row[csf("po_id")]]['poQty'] >= $po_wise_ttl_ex_qty[$row[csf("po_id")]])
			{
				$current_ex_up_charge = ($row[csf('up_charge')]/$po_quantity)*$current_ex_Fact_Qty;
				$current_ex_up_charge_value = $current_ex_up_charge + ($current_ex_Fact_Qty*$unit_price);

				$po_wise_ttl_up_charge[$row[csf("po_id")]] += $current_ex_up_charge;

				// echo $current_ex_up_charge ."+((".$current_ex_Fact_Qty."-".$excessExQty.")*".$unit_price.")<br>";

				if($total_ex_fact_qty>$poExQtyArray[$row[csf("po_id")]]['poQty'])
				{
					$total_ex_up_charge = ($row[csf('up_charge')]/$po_quantity)*$poExQtyArray[$row[csf("po_id")]]['poQty'];
					$total_ex_up_charge_value = $total_ex_up_charge + ($total_ex_fact_qty*$unit_price);
				}
				else
				{
					$total_ex_up_charge = ($row[csf('up_charge')]/$po_quantity)*$total_ex_fact_qty;
					$total_ex_up_charge_value = $total_ex_up_charge + ($total_ex_fact_qty*$unit_price);
				}

				$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['current_ex_up_charge_value'] += $current_ex_up_charge_value;
			}
			else
			{
				$excessExQty = $po_wise_ttl_ex_qty[$row[csf("po_id")]] - $poExQtyArray[$row[csf("po_id")]]['poQty'];
				$bal_qty = $current_ex_Fact_Qty-$excessExQty;
				if($bal_qty<1) $bal_qty=0;
				$current_ex_up_charge = ($row[csf('up_charge')]/$po_quantity)*$bal_qty;
				// echo $excess_ex_up_charge = ($row[csf('up_charge')]/$po_quantity)*$excessExQty;


				$current_ex_up_charge_value = $current_ex_up_charge + ($current_ex_Fact_Qty*$unit_price);

				if($bal_qty<1) $bal_qty=1;
				$current_ex_up_charge = ($row[csf('up_charge')]/$po_quantity)*$bal_qty;

				// echo $po_wise_ttl_up_charge[$row[csf("po_id")]];

				// echo $current_ex_up_charge ."+".$current_ex_Fact_Qty."*".$unit_price."<br>";

				$total_ex_up_charge = $row[csf('up_charge')];
				// $total_ex_up_charge = ($row[csf('up_charge')]/$po_quantity)*($total_ex_fact_qty-$excessExQty);
				// echo "(".$row[csf('up_charge')]."/".$po_quantity.")*(".$total_ex_fact_qty."-".$excessExQty.")<br>";
				$total_ex_up_charge_value = $total_ex_up_charge + ($total_ex_fact_qty*$unit_price);
				// echo $total_ex_up_charge ."+".$total_ex_fact_qty."*".$unit_price."<br>";

				$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['current_ex_up_charge_value'] += $current_ex_up_charge_value;
			}

			$total_sales_minutes=($current_ex_Fact_Qty*$item_smv);
			$gr_sales_min+=$total_sales_minutes;


			$data_array[$buyer_arr[$row[csf("buyer_name")]]][$challan_no][$row[csf('po_id')]]['company'] = $company_library[$row[csf("company_name")]];
			$data_array[$buyer_arr[$row[csf("buyer_name")]]][$challan_no][$row[csf('po_id')]]['job'] = $row[csf("job_no_prefix_num")];
			$data_array[$buyer_arr[$row[csf("buyer_name")]]][$challan_no][$row[csf('po_id')]]['year'] = $row[csf("year")];
			$data_array[$buyer_arr[$row[csf("buyer_name")]]][$challan_no][$row[csf('po_id')]]['client_id'] = $row[csf("client_id")];
			$data_array[$buyer_arr[$row[csf("buyer_name")]]][$challan_no][$row[csf('po_id')]]['po_number'] = $row[csf("po_number")];
			$data_array[$buyer_arr[$row[csf("buyer_name")]]][$challan_no][$row[csf('po_id')]]['vehi_no'] = $vehi_no;
			$data_array[$buyer_arr[$row[csf("buyer_name")]]][$challan_no][$row[csf('po_id')]]['del_location'] = $row[csf("del_location")];
			$data_array[$buyer_arr[$row[csf("buyer_name")]]][$challan_no][$row[csf('po_id')]]['mobile_no'] = $challan_mst_arr[$row[csf('challan_id')]][$row[csf('po_id')]]['mobile_no'];
			$data_array[$buyer_arr[$row[csf("buyer_name")]]][$challan_no][$row[csf('po_id')]]['driver_name'] = $challan_mst_arr[$row[csf('challan_id')]][$row[csf('po_id')]]['driver_name'];
			$data_array[$buyer_arr[$row[csf("buyer_name")]]][$challan_no][$row[csf('po_id')]]['floor_no'] = $floor_no;
			$data_array[$buyer_arr[$row[csf("buyer_name")]]][$challan_no][$row[csf('po_id')]]['inv_id'] = $inv_id;
			$data_array[$buyer_arr[$row[csf("buyer_name")]]][$challan_no][$row[csf('po_id')]]['lc_sc_no'] = $lc_sc_no;
			$data_array[$buyer_arr[$row[csf("buyer_name")]]][$challan_no][$row[csf('po_id')]]['design_source'] = $design_source_arr[$row[csf("DESIGN_SOURCE_ID")]];
			$data_array[$buyer_arr[$row[csf("buyer_name")]]][$challan_no][$row[csf('po_id')]]['style_ref_no'] = $row[csf("style_ref_no")];
			$data_array[$buyer_arr[$row[csf("buyer_name")]]][$challan_no][$row[csf('po_id')]]['style_description'] = $row[csf("style_description")];
			$data_array[$buyer_arr[$row[csf("buyer_name")]]][$challan_no][$row[csf('po_id')]]['item_name'] = $item_name_all;
			$data_array[$buyer_arr[$row[csf("buyer_name")]]][$challan_no][$row[csf('po_id')]]['item_smv'] = $item_smv;
			$data_array[$buyer_arr[$row[csf("buyer_name")]]][$challan_no][$row[csf('po_id')]]['shipment_date'] = $row[csf("shipment_date")];
			$data_array[$buyer_arr[$row[csf("buyer_name")]]][$challan_no][$row[csf('po_id')]]['ex_factory_date'] = $row[csf("ex_factory_date")];
			$data_array[$buyer_arr[$row[csf("buyer_name")]]][$challan_no][$row[csf('po_id')]]['ship_mode'] = $shipment_mode[$row[csf("ship_mode")]];
			$data_array[$buyer_arr[$row[csf("buyer_name")]]][$challan_no][$row[csf('po_id')]]['shiping_mode'] = $shipment_mode[$row[csf("shiping_mode")]];
			$data_array[$buyer_arr[$row[csf("buyer_name")]]][$challan_no][$row[csf('po_id')]]['foc_or_claim'] = $row[csf("foc_or_claim")];
			$data_array[$buyer_arr[$row[csf("buyer_name")]]][$challan_no][$row[csf('po_id')]]['actual_po'] = $actual_po_arr[$row[csf("actual_po")]];
			$data_array[$buyer_arr[$row[csf("buyer_name")]]][$challan_no][$row[csf('po_id')]]['diff'] = $diff;
			$data_array[$buyer_arr[$row[csf("buyer_name")]]][$challan_no][$row[csf('po_id')]]['order_uom'] = $row[csf("order_uom")];
			$data_array[$buyer_arr[$row[csf("buyer_name")]]][$challan_no][$row[csf('po_id')]]['po_quantity'] = $po_quantity;
			$data_array[$buyer_arr[$row[csf("buyer_name")]]][$challan_no][$row[csf('po_id')]]['unit_price'] = $unit_price;
			$data_array[$buyer_arr[$row[csf("buyer_name")]]][$challan_no][$row[csf('po_id')]]['po_value'] = $po_quantity*$unit_price;
			$data_array[$buyer_arr[$row[csf("buyer_name")]]][$challan_no][$row[csf('po_id')]]['up_charge'] = $row[csf("up_charge")];
			$data_array[$buyer_arr[$row[csf("buyer_name")]]][$challan_no][$row[csf('po_id')]]['cur_ex_qty'] = $current_ex_Fact_Qty;
			$data_array[$buyer_arr[$row[csf("buyer_name")]]][$challan_no][$row[csf('po_id')]]['cur_ex_val'] = $current_ex_Fact_Qty*$unit_price;
			$data_array[$buyer_arr[$row[csf("buyer_name")]]][$challan_no][$row[csf('po_id')]]['gp_no'] = $row[csf("gp_no")];
			$data_array[$buyer_arr[$row[csf("buyer_name")]]][$challan_no][$row[csf('po_id')]]['lock_no'] = $row[csf("lock_no")];
			$data_array[$buyer_arr[$row[csf("buyer_name")]]][$challan_no][$row[csf('po_id')]]['carton_qnty'] = $row[csf("carton_qnty")];




 	  		$total_po_qty+=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("itm_num_id")]];
 	  		$total_po_valu+=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("itm_num_id")]]*$row[csf("unit_price")];


			$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['ex_factory_qnty'] +=$row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]][$row[csf("challan_id")]];
			$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['ex_factory_value'] +=($row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]][$row[csf("challan_id")]])*$row[csf("unit_price")];



			$total_basic_qty+=$basic_qnty;

			$total_ex_qty+=$row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]][$row[csf("challan_id")]];
			$total_crtn_qty+=$row[csf("carton_qnty")];
			$total_ex_valu+=($row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]][$row[csf("challan_id")]])*$row[csf("unit_price")];
			$g_total_ex_qty+=$total_ex_fact_qty;
			$g_total_ex_crtn+=$total_cartoon_qty;
			$g_total_ex_val+=$total_ex_fact_qty*$row[csf("unit_price")];
			$g_sales_minutes+=$total_sales_minutes;

			// $g_sales_minutes+=$current_ex_Fact_Qty*$item_smv;
			$total_eecess_storage_qty+=$excess_shortage_qty;
			$total_eecess_storage_val+=$excess_shortage_value;
			if($po_check_arr[$row[csf("po_id")]][$row[csf("itm_num_id")]]=="")
			{
				$po_check_arr[$row[csf("po_id")]][$row[csf("itm_num_id")]]=$row[csf("po_id")];
				$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['b_id']=$row[csf("buyer_name")];
				$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['org_po_qnty'] +=$row[csf("po_quantity")];
				$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['upchage'] +=$row[csf("up_charge")];
				$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['po_qnty'] +=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("itm_num_id")]];
				$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['po_value'] +=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("itm_num_id")]]*$row[csf("unit_price")];
				$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['basic_qnty'] +=$basic_qnty;
				$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['total_ex_fact_qty'] +=$total_ex_fact_qty;
				$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['total_ex_fact_value'] +=$total_ex_fact_qty*$row[csf("unit_price")];


				$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['current_ex_up_charge'] += $current_ex_up_charge;
				// $master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['current_ex_up_charge_value'] += $current_ex_up_charge_value;
				$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['total_ex_up_charge'] += $total_ex_up_charge;
				$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['total_ex_up_charge_value'] += $total_ex_up_charge_value;

 	  			$gr_po_qnty_pcs+=$po_quantity;
	  			$gr_po_qnty_val+=$po_quantity*$unit_price;
 	  			$gr_ttl_ex_qnty+=$total_ex_fact_qty;
	  			$gr_ttl_ex_qnty_val+=$total_ex_fact_value;
	  			$gr_ttl_carton_qt+=$total_cartoon_qty;
	  			//$gr_sales_min+=$total_sales_minutes;
	  			$gr_ttl_basic_qty+=$basic_qnty;
	  			$gr_ttl_ex_fac_per+=$total_ex_fact_qty_parcent;
	  			$gr_ttl_short_qty+=$excess_shortage_qty;
	  			$gr_ttl_short_val+=$excess_shortage_value;
	  			$gr_ttl_sales_cm+=$cm_per_pcs*$total_ex_fact_qty;
				$total_po_val+=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("itm_num_id")]]*$row[csf("unit_price")];
				$gr_upcharge+=$row[csf("up_charge")];

				$gr_current_ex_up_charge += $current_ex_up_charge;
				$gr_current_ex_up_charge_value += $current_ex_up_charge_value;

				$gr_total_ex_up_charge += $total_ex_up_charge;
				$gr_total_ex_up_charge_value += $total_ex_up_charge_value;


				//$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['sales_min'] += $item_smv*$total_ex_fact_qty;

			}


			$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['sales_min'] += $item_smv*($row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]][$row[csf("challan_id")]]);

			$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['sales_cm'] += $cm_per_pcs*$current_ex_Fact_Qty;
			// echo $cm_per_pcs."*".$current_ex_Fact_Qty."<br>";




			$i++; $item_name_all="";
		}
		$pp=$i;
		// echo "<pre>";print_r($buyer_arr);die();
		?>
        <div style="width:1930x;">
            <div style="width:1910px" >
                <table width="1910"  cellspacing="0"  align="center">
                    <tr>
                        <td align="center" colspan="15" class="form_caption">
                            <strong style="font-size:16px;">Company Name:<? echo  $company_library[$cbo_company_name] ;?></strong>
                        </td>
                    </tr>
                    <tr class="form_caption">
                        <td colspan="15" align="center" class="form_caption"> <strong style="font-size:15px;">Ex-Factory Report</strong></td>
                    </tr>
                     <tr align="center">
                        <td colspan="15" align="center" class="form_caption"> <strong style="font-size:15px;">Total Summary</strong></td>
                    </tr>
                </table>
                <table width="1910" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1">
                    <thead>
                        <th width="30">SL</th>
                        <th width="110">Buyer Name</th>
                        <th width="100">Client</th>
                        <th width="100">PO Qty</th>
                        <th width="100">PO Qty (pcs)</th>
                        <th width="130">PO Value</th>
                        <th width="100">PO Value(%)</th>
                        <th width="100">PO TTL Up-Charge</th>
                        <th width="100">Current Ex-Fact. Qty.</th>
                        <th width="130">Current Ex-Fact. Value</th>
                        <th width="130">Up Charge with Current Ex-Fact. Value</th>
                        <th width="100">Total Ex-Fact. Qty.</th>
                        <th width="130">Total Ex-Fact. Value</th>
                        <th width="130">Up Charge with Total Ex-Fact. Value</th>
                        <th width="100" title="Item SMV*Current Ex-Fact. Qty.">Sales Minutes</th>
                        <th width="100">Sales CM</th>
                        <th width="100">Total Ex-Fact. (Basic Qty)</th>
                        <th>Total Ex-Fact. Value %</th>
                    </thead>
             </table>
             <div style="width:1930px; max-height:225px; overflow-y:scroll" id="scroll_body1">
             <table width="1910" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body">
                 <?
                $m=1; $grand_sales_minute =0;
                foreach($master_data as $buyid=>$buyData)
                {
					foreach($buyData as $clientid=>$cdata)
					{
						if ($m%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$buyer_po_quantity=0; $buyer_po_value=0; $current_ex_Fact_Qty=0; $current_ex_fact_value=0; $total_ex_fact_qty=0; $total_ex_fact_value=0; $g_sales_min=0;$g_sales_cm=0;


						$po_quantity=$cdata['po_qnty'];
						$buyer_po_value=$cdata["po_value"];
						$parcentages+=($buyer_po_value/$total_po_val)*100;
						$current_ex_Fact_Qty=$cdata['ex_factory_qnty'];
						$current_ex_fact_value=$cdata['ex_factory_value'];
						$total_ex_fact_qty=$cdata['total_ex_fact_qty'];
						$total_ex_fact_value=$cdata['total_ex_fact_value'];
						$buyer_basic_qnty=$cdata["basic_qnty"];
						$total_ex_fact_value_parcentage=($total_ex_fact_value/$buyer_po_value)*100;
						?>
						<tr bgcolor="<?=$bgcolor;?>" onclick="change_color('tr_<?=$i;?>','<?=$bgcolor;?>')" id="tr_<?=$i;?>" >
							<td width="30" align="center"><?=$m;?></td>
							<td width="110" style="word-break:break-all"><?=$buyer_arr[$buyid];?></td>
							<td width="100" style="word-break:break-all"><?=$buyer_arr[$clientid];?></td>
							<td width="100" style="word-break:break-all" align="right"><?=number_format($cdata["org_po_qnty"], 0);?></td>
							<td width="100" style="word-break:break-all" align="right"><?=number_format($po_quantity, 0);?></td>
							<td width="130" style="word-break:break-all" align="right"><p id="value_<?=$i;?>"><?=number_format($buyer_po_value, 2, '.', '');?></p></td>
							<td width="100" style="word-break:break-all" align="right"><?=number_format(($buyer_po_value / $total_po_val) * 100, 2, '.', '');?></td>
                            <td width="100" style="word-break:break-all" align="right"><?=number_format($cdata["upchage"], 2, '.', '');?> </td>
							<td width="100" style="word-break:break-all" align="right"><?=number_format($current_ex_Fact_Qty, 0, '', '');?></td>
							<td width="130" style="word-break:break-all" align="right"><?=number_format($current_ex_fact_value, 2, '.', '');?></td>
							<td width="130" style="word-break:break-all" align="right"><?=number_format($cdata['current_ex_up_charge_value'], 2, '.', '');?></td>
							<td width="100" style="word-break:break-all" align="right"><?=number_format($total_ex_fact_qty, 0, '', '');?></td>
							<td width="130" style="word-break:break-all" align="right"><?=number_format($total_ex_fact_value, 2, '.', '');?> </td>
							<td width="130" style="word-break:break-all" align="right"><?=number_format($cdata['total_ex_up_charge_value'], 2, '.', '');?> </td>
							<td width="100" style="word-break:break-all" align="right"><? echo $g_sales_min+= number_format($cdata["sales_min"],0,'',''); ?></td>
							<td width="100" style="word-break:break-all" align="right"><? echo $g_sales_cm+= number_format($cdata["sales_cm"],0,'','');?></td>
							<td width="100" style="word-break:break-all" align="right"><?=number_format($buyer_basic_qnty, 0, '', '');?></td>
							<td style="word-break:break-all" align="right"><?=number_format($total_ex_fact_value_parcentage, 0)?> %</td>
						</tr>
						<?
						$i++; $m++;

						$grand_sales_minute +=number_format($cdata["sales_min"],0,'','');
						$grand_sales_cm +=number_format($cdata["sales_cm"],0,'','');
						$total_buyer_org_po_quantity+=$cdata["org_po_qnty"];
						$total_buyer_po_quantity+=$po_quantity;
						$total_buyer_po_value+=$buyer_po_value;
						$total_current_ex_Fact_Qty +=$current_ex_Fact_Qty;
						$total_current_ex_fact_value+=$current_ex_fact_value;
						$mt_total_ex_fact_qty+=$total_ex_fact_qty;
						$mt_total_ex_fact_value+=$total_ex_fact_value;
						$total_buyer_basic_qnty +=$buyer_basic_qnty;
						$buyerTotUpCharge +=$cdata["upchage"];

					}
                }
				?>
                </table>
                </div>
				<input type="hidden" name="total_i" id="total_i" value="<?=$i;?>" />
                <table class="tbl_bottom" width="1910" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                    	<td width="30">&nbsp;</td>
                        <td width="110" align="right"><b>Total:</b></td>
                        <td width="100">&nbsp;</td>
                        <td width="100" align="right" id="total_buyer_org_po_quantity"><?=number_format($total_buyer_org_po_quantity, 0);?></td>
                        <td width="100"align="right" id="total_buyer_po_quantity"><?=number_format($total_buyer_po_quantity, 0);?></td>
                        <td width="130" align="right" id="value_total_buyer_po_value"><?=number_format($total_buyer_po_value, 2, '.', '');?></td>
                        <td width="100" align="right" id="parcentages"><?=ceil($parcentages);?></td>
                        <td width="100" align="right" id="value_upcharge"><?=number_format($buyerTotUpCharge, 2);?></td>
                        <td width="100" align="right" id="total_current_ex_Fact_Qty"><?=number_format($total_current_ex_Fact_Qty, 0);?></td>
                        <td width="130" align="right" id="value_total_current_ex_fact_value"><?=number_format($total_current_ex_fact_value, 2);?></td>
                        <td width="130" align="right" id="value_total_current_ex_fact_value_with_up_charge"><? //=number_format($total_current_ex_fact_value, 2);?></td>
                        <td width="100" align="right" id="mt_total_ex_fact_qty"><?=number_format($mt_total_ex_fact_qty, 0);?></td>
                        <td width="130" align="right" id="value_mt_total_ex_fact_value"><?=number_format($mt_total_ex_fact_value, 2);?></td>
                        <td width="130" align="right" id="value_mt_total_ex_fact_value_with_up_charge"><? //=number_format($mt_total_ex_fact_value, 2);?></td>
                        <td width="100" align="right" id="sales_minutes"><?=number_format($grand_sales_minute, 2);?></td>
                        <td width="100" align="right" id="sales_cm"><?=number_format($grand_sales_cm, 2);?></td>
                        <td width="100" align="right" id="total_buyer_basic_qnty"><?=number_format($total_buyer_basic_qnty, 0);?></td>
                        <td>&nbsp;</td>
                    </tfoot>
                </table>
            </div>
            <br />
            <!-- ==================================== details part ================================== -->
            <div>
                <table width="1630" border="1" class="rpt_table" rules="all" id="table_header_2">
                	<caption style="font-weight: bold;font-size: 18px;">Details Part</caption>
                    <thead>
                        <th width="30"><p>SL</p></th>
                        <th width="100"><p>Buyer</p></th>
                        <th width="110"><p>Order NO</p></th>
                        <th width="110"><p>Actual PO</p></th>
                        <th width="100"><p>Style</p></th>
                        <th width="110"><p>Item Name</p></th>
                        <th width="80"><p>Ship qty Pcs Incl Sets</p></th>
                        <th width="80"><p>Total Ship Qty pcs</p></th>
                        <th width="80"><p>Total CTN</p></th>
                        <th width="100"><p>Del Location</p></th>
                        <th width="80"><p>Vehicle No</p></th>
                        <th width="80"><p>Mobile No</p></th>
                        <th width="130"><p>Driver Name</p></th>
                        <th width="100"><p>Ship Mod</p></th>
                        <th width="100"><p>Chal No.</p></th>
                        <th width="100"><p>GP No</p></th>
                        <th width="100"><p>Lock No</p></th>
                        <th width="70"><p>Ex- Factory Date</p></th>
                        <th width="100"><p>Name of Tn</p></th>
                    </thead>
                </table>
            <div style="width:1650px; overflow-y:scroll; overflow-x:hidden; max-height:300px;"  id="scroll_body" >
           		<table width="1630" cellspacing="0" cellpadding="0"  border="1"  class="rpt_table" rules="all" id="table_body2" align="left">
           			<?
           			$i=1;
           			$tot_ex_qty_set = 0;
           			$tot_ex_qty_pcs = 0;
           			$tot_carton_qty = 0;
           			foreach ($data_array as $buyer_name => $buyer_data)
           			{
	           			$byr_tot_ex_qty_set = 0;
	           			$byr_tot_ex_qty_pcs = 0;
	           			$byr_tot_carton_qty = 0;
           				foreach ($buyer_data as $challan_no => $challan_data)
           				{
           					foreach ($challan_data as $po_id => $row)
           					{
           						?>
           						<tr>
           							<td width="30"><?=$i;?></td>
			                        <td width="100"><p><?=$buyer_name;?></p></td>
			                        <td width="110"><p><?=$row['po_number'];?></p></td>
			                        <td width="110"><p><?=$row['actual_po'];?></p></td>
			                        <td width="100"><p><?=$row['style_ref_no'];?></p></td>
			                        <td width="110"><p><?=$row['item_name'];?></p></td>
			                        <td width="80" align="right"><p><?=number_format($row['cur_ex_qty'],0);?></p></td>
			                        <td width="80" align="right"><p><?=number_format($row['cur_ex_qty'],0);?></p></td>
			                        <td width="80" align="right"><p><?=number_format($row['carton_qnty'],0);?></p></td>
			                        <td width="100"><p><?=$location_library[$row["del_location"]];?></p></td>
			                        <td width="80"><p><?=$row['vehi_no'];?></p></td>
			                        <td width="80"><p><?=$row['mobile_no'];?></p></td>
			                        <td width="130"><p><?=$row['driver_name'];?></p></td>
			                        <td width="100"><p><?=$row['ship_mode'];?></p></td>
			                        <td width="100"><p><?=$challan_no;?></p></td>
			                        <td width="100"><p><?=$row['gp_no'];?></p></td>
			                        <td width="100"><p><?=$row['lock_no'];?></p></td>
			                        <td width="70"><p><?=change_date_format($row['ex_factory_date']);?></p></td>
			                        <td width="100"><p><?=$row[''];?></p></td>
           						</tr>
           						<?
           						$i++;
			           			$tot_ex_qty_set += $row['cur_ex_qty'];
			           			$tot_ex_qty_pcs += $row['cur_ex_qty'];
			           			$tot_carton_qty += $row['carton_qnty'];
			           			$byr_tot_ex_qty_set += $row['cur_ex_qty'];
			           			$byr_tot_ex_qty_pcs += $row['cur_ex_qty'];
			           			$byr_tot_carton_qty += $row['carton_qnty'];
           					}
           				}
           				?>
           				<tr style="background: #cddcdc;text-align: right;font-weight: bold;">

	                        <td colspan="6">Sub Total</td>
	                        <td><?=number_format($byr_tot_ex_qty_set,0);?></td>
	                        <td><?=number_format($byr_tot_ex_qty_pcs,0);?></td>
	                        <td><?=number_format($byr_tot_carton_qty,0);?></td>
	                        <td></td>
	                        <td></td>
	                        <td></td>
	                        <td></td>
	                        <td></td>
	                        <td></td>
	                        <td></td>
	                        <td></td>
	                        <td></td>
	                        <td></td>
	                        <td"></td>
           				</tr>
           				<?
           			}
           			?>
           		</table>
            </div>
                <table width="1630" border="1" class="rpt_table" rules="all" id="table_header_2">
                    <tfoot>
                        <th width="30">.</th>
                        <th width="100"></th>
                        <th width="110"></th>
                        <th width="110"></th>
                        <th width="100"></th>
                        <th width="110"></th>
                        <th width="80"><?=number_format($tot_ex_qty_set,0);?></th>
                        <th width="80"><?=number_format($tot_ex_qty_pcs,0);?></th>
                        <th width="80"><?=number_format($tot_carton_qty,0);?></th>
                        <th width="100"></th>
                        <th width="80"></th>
                        <th width="80"></th>
                        <th width="130"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="70"></th>
                        <th width="100"></th>
                    </tfoot>
                </table>
            </div>
        </div>

		<?
	}
	else if($reportType==16)//Details Button tmp done
	{
        if($cbo_style_owner_company_name > 0){
            $company_cond .= " and c.style_owner = $cbo_style_owner_company_name ";
        }
		$tot_cost_arr = return_library_array("select job_no, cm_cost from wo_pre_cost_dtls","job_no","cm_cost");

		$comSql="select id, invoice_no, shipping_mode, is_lc, lc_sc_id from com_export_invoice_ship_mst";
		$comSqlRes=sql_select($comSql); $invoice_array= array(); $shipping_mode_array= array(); $lc_sc_type_arr= array(); $lc_sc_id_array= array();
		foreach($comSqlRes as $row)
		{
			$invoice_array[$row[csf('id')]]=$row[csf('invoice_no')];
			$shipping_mode_array[$row[csf('id')]]=$row[csf('shipping_mode')];
			$lc_sc_type_arr[$row[csf('id')]]=$row[csf('is_lc')];
			$lc_sc_id_array[$row[csf('id')]]=$row[csf('lc_sc_id')];
		}
		unset($comSqlRes);

		$lc_num_arr=return_library_array( "select id,export_lc_no from com_export_lc", "id", "export_lc_no"  );
		$sc_num_arr=return_library_array( "select id,contract_no from com_sales_contract", "id", "contract_no"  );
		$forwarder_arr=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name");

		if($source_cond)
		$source_cond2=str_replace("d.","a.",$source_cond);

		$details_report .='<table width="4890" cellspacing="0" cellpadding="0"  border="1"  class="rpt_table" rules="all" id="table_body2">';
		$i=1;

		$sql= "SELECT b.id as po_id,
		max(a.lc_sc_no) as lc_sc_arr_no,
		LISTAGG(CAST( a.invoice_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.invoice_no) as invoice_no,
		LISTAGG(CAST( a.foc_or_claim AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.foc_or_claim) as foc_or_claim,
		LISTAGG(CAST( a.item_number_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.item_number_id) as itm_num_id,
		sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(a.total_carton_qnty) as carton_qnty,
		max(a.ex_factory_date) as ex_factory_date,
		LISTAGG(CAST( a.lc_sc_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.lc_sc_no) as lc_sc_no,
		max(a.shiping_mode) as shiping_mode, a.delivery_mst_id as challan_id, d.delivery_floor_id as del_floor, b.shipment_date, b.po_number, b.po_quantity as po_quantity, (b.unit_price/c.total_set_qnty) as unit_price, b.up_charge, b.shiping_status, c.id, c.company_name, c.buyer_name, c.client_id, c.job_no_prefix_num, c.job_no, c.ship_mode, to_char(c.insert_date,'YYYY') as year, c.style_ref_no, c.style_description, c.set_smv, d.delivery_company_id as del_company, d.source, d.delivery_location_id as del_location, b.grouping, c.total_set_qnty, a.item_number_id, c.set_break_down, c.order_uom, c.DESIGN_SOURCE_ID
		from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c, pro_ex_factory_delivery_mst d
		where a.po_break_down_id=b.id and b.job_no_mst=c.job_no $company_cond $str_cond $del_comp_cond $del_location_cond $del_floor_cond $shiping_status_cond $source_cond $buyer_conds $internal_ref_cond $search_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,2,3) and c.is_deleted=0 and c.status_active in(1,2,3) and a.delivery_mst_id=d.id and a.entry_form!=85
		group by
				b.id,b.grouping, a.delivery_mst_id, d.delivery_floor_id, b.shipment_date, b.po_number, b.po_quantity, c.total_set_qnty, b.unit_price, b.up_charge, b.shiping_status, c.id, c.company_name, c.buyer_name, c.client_id, c.job_no_prefix_num, c.job_no, c.ship_mode, c.insert_date, c.style_ref_no, c.style_description, c.set_smv, d.delivery_company_id, d.source, d.delivery_location_id, a.item_number_id, c.set_break_down, c.order_uom,c.DESIGN_SOURCE_ID
		order by c.buyer_name, b.shipment_date ASC";
		//		 echo $sql;die();
		$sql_result=sql_select($sql);
		$poExQtyArray = array(); $poChkArray = array(); $po_id_array = array();
		foreach($sql_result as $k=>$v)
		{
			$all_job_arr[trim($v[csf("job_no")])] = trim($v[csf("job_no")]);
			$po_id_array[$v[csf("po_id")]] = $v[csf("po_id")];
			if(!in_array($v[csf("po_id")], $poChkArray))
			{
				$poExQtyArray[$v[csf("po_id")]]['poQty'] += $v[csf("po_quantity")];
				$poChkArray[$v[csf("po_id")]] = $v[csf("po_id")];
			}
			$poExQtyArray[$v[csf("po_id")]]['exQty'] += $v[csf("ex_factory_qnty")];
		}

		$con = connect();
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (11) and ENTRY_FORM=17");
		oci_commit($con);

		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 17, 11, $po_id_array, $empty_arr);//Po ID
		disconnect($con);

		/*$challan_sql="SELECT a.id,b.delivery_mst_id, a.sys_number_prefix_num,a.sys_number, a.forwarder, a.truck_no, a.driver_name, a.mobile_no, a.dl_no, b.po_break_down_id,b.item_number_id,a.delivery_floor_id,
		(CASE WHEN a.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty,
		b.total_carton_qnty as carton_qnty
		from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b
		where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $source_cond2";*/

		if($source_cond)
		$source_cond2=str_replace("d.","a.",$source_cond);
		$exfact_sql=sql_select("SELECT a.id, a.sys_number_prefix_num, a.sys_number, a.forwarder, a.truck_no, a.driver_name, a.mobile_no, a.dl_no, a.delivery_floor_id,

		b.item_number_id,b.country_id, b.delivery_mst_id, b.po_break_down_id, b.entry_form, b.ex_factory_qnty, b.total_carton_qnty,b.challan_no

		from pro_ex_factory_mst b, pro_ex_factory_delivery_mst a, gbl_temp_engine d where b.delivery_mst_id=a.id and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=17 and d.ref_from=11 $source_cond2");

		$exfact_qty_arr=$exfact_return_qty_arr=$exfact_return_qty_arr2=$exfact_cartoon_arr=array(); $challan_mst_arr=array(); $exfact_qty_arr_without_current=array();
		$challan_chk_arr = array();
		foreach($exfact_sql as $row)
		{
			if($row[csf("entry_form")]!=85)
			{
				$exfact_qty_arr[$row[csf("po_break_down_id")]][$row[csf("delivery_mst_id")]]+=$row[csf("ex_factory_qnty")];
				$exfact_qty_arr_without_current[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]]["ex_fact"]+=$row[csf("ex_factory_qnty")]-$row[csf("ex_factory_return_qnty")];
			}
			else if($row[csf("entry_form")]==85)
			{
				$exfact_qty_arr[$row[csf("po_break_down_id")]][$row[csf("delivery_mst_id")]]-=$row[csf("ex_factory_qnty")];//return
				$exfact_qty_arr_without_current[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]]["ex_fact"]-=$row[csf("ex_factory_qnty")];
				$exfact_return_qty_arr[$row[csf("po_break_down_id")]][$row[csf("delivery_mst_id")]]+=$row[csf("ex_factory_qnty")];

				// if($challan_chk_arr[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("challan_no")]]=="")
				// {
					$exfact_return_qty_arr2[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("challan_no")]]+=$row[csf("ex_factory_qnty")];
					$challan_chk_arr[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("challan_no")]] = $row[csf("challan_no")];
					// echo $row[csf("po_break_down_id")]."=".$row[csf("item_number_id")]."=".$row[csf("country_id")]."=".$row[csf("challan_no")]."<br>";
				// }
			}
			$exfact_cartoon_arr[$row[csf("po_break_down_id")]][$row[csf("delivery_mst_id")]]+=$row[csf("carton_qnty")];
			$exfact_qty_arr_without_current[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]]["carton"]+=$row[csf("carton_qnty")];

			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['challan']=$row[csf("sys_number")];
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['forwarder']=$row[csf("forwarder")];
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['truck_no']=$row[csf("truck_no")];
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['driver_name']=$row[csf("driver_name")];
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['mobile_no']=$row[csf("mobile_no")];
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['dl_no']=$row[csf("dl_no")];
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['floor']=$row[csf("delivery_floor_id")];
		}
		// echo "<pre>";print_r($exfact_return_qty_arr);
		$order_item_qnty_sql="SELECT a.po_break_down_id, a.item_number_id, a.order_quantity as order_quantity from wo_po_color_size_breakdown a, gbl_temp_engine d where a.status_active in(1,2,3) and a.is_deleted=0 and a.po_break_down_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=17 and d.ref_from=11";
		// echo $order_item_qnty_sql;
		$sqlitemqty=sql_select($order_item_qnty_sql);
		foreach($sqlitemqty as $key=>$val)
		{
			$order_item_qnty_arr[$val[csf("po_break_down_id")]][$val[csf("item_number_id")]]+=$val[csf("order_quantity")];
		}
		unset($sqlitemqty);

		$inspection_date_arr=return_library_array( "select b.po_break_down_id, max(b.inspection_date) as inspection_date from pro_buyer_inspection a, gbl_temp_engine d where status_active=1 and is_deleted=0 and b.po_break_down_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=17 and d.ref_from=11 group by b.po_break_down_id", "po_break_down_id", "inspection_date");

		$con = connect();
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (11) and ENTRY_FORM=17");
		oci_commit($con);
		disconnect($con);

		$gr_po_qnty_pcs=0; $gr_po_qnty_val=0; $gr_po_qnty_val_perc=0; $gr_ttl_ex_qnty=0; $gr_ttl_ex_qnty_val=0; $gr_sales_min=0; $gr_ttl_carton=0; $gr_ttl_basic_qty=0; $gr_ttl_ex_fac_per=0; $gr_ttl_short_qty=0; $gr_ttl_short_val=0; $gr_ttl_sales_cm=0;

		//$po_exist_arr=array();
		$po_wise_ttl_ex_qty = array(); $po_wise_ttl_up_charge = array();
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
			// echo $exfact_return_qty."ddd<br>";

			//$diff=($row[csf('shiping_status')]!=3)?datediff("d",$current_date, $row[csf("shipment_date")])-1:datediff("d",$row[csf("ex_factory_date")], $row[csf("shipment_date")])-1;
			$todate=date("d-M")."-".substr(date("Y"), 2) ;
 			$todate=explode("-", $todate);
 			$todate=$todate[0]."-".strtoupper($todate[1])."-".$todate[2];
 			$diff=datediff("d",$todate, $row[csf("shipment_date")])-2;
		    $diff_color=($diff>0) ?" color:black;":"color:red;";

			foreach($challan_id as $val)
			{
				if($challan_no=="") $challan_no=$challan_mst_arr[$val][$row[csf('po_id')]]['challan']; else $challan_no.=','.$challan_mst_arr[$val][$row[csf('po_id')]]['challan'];
				if($floor_no=="") $floor_no=$floor_library[$challan_mst_arr[$val][$row[csf('po_id')]]['floor']]; else $floor_no.=','.$floor_library[$challan_mst_arr[$val][$row[csf('po_id')]]['floor']];

				if($forwarder=="") $forwarder=$forwarder_arr[$challan_mst_arr[$val][$row[csf('po_id')]]['forwarder']]; else $forwarder.=','.$forwarder_arr[$challan_mst_arr[$val][$row[csf('po_id')]]['forwarder']];
				if($vehi_no=="") $vehi_no=$challan_mst_arr[$val][$row[csf('po_id')]]['truck_no']; else $vehi_no.=','.$challan_mst_arr[$val][$row[csf('po_id')]]['truck_no'];
				if($dirver_info=="") $dirver_info="Name: ".$challan_mst_arr[$val][$row[csf('po_id')]]['driver_name']."<br>Mob No: ".$challan_mst_arr[$val][$row[csf('po_id')]]['mobile_no']."<br>DL No: ".$challan_mst_arr[$val][$row[csf('po_id')]]['dl_no']; else $dirver_info.=','."Name: ".$challan_mst_arr[$val][$row[csf('po_id')]]['driver_name']."<br>Mob No: ".$challan_mst_arr[$val][$row[csf('po_id')]]['mobile_no']."<br>DL No: ".$challan_mst_arr[$val][$row[csf('po_id')]]['dl_no'];
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


			$exfact_return_qty = $exfact_return_qty_arr2[$row[csf("po_id")]][$row[csf("item_number_id")]][$challan_no];
			$current_ex_up_charge = 0; $current_ex_up_charge_value = 0;
			$total_ex_up_charge = 0; $total_ex_up_charge_value = 0;

			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$comapny_id=$row[csf("company_name")];
			$delv_comp=($row[csf("source")]!=3)?  $company_library[$row[csf("del_company")]] : $supp_library[$row[csf("del_company")]];

			$onclick=" change_color('tr_".$i."','".$bgcolor."')";
			$details_report .='<tr bgcolor="'.$bgcolor.'" onclick="'.$onclick.'" id="tr_'.$i.'">';
			$details_report .='<td width="30" align="center">'.$i.'</td>
								<td width="130" align="center" ><p>'.$company_library[$row[csf("company_name")]].'</p></td>
								<td width="60" align="center" ><p>'.$row[csf("job_no_prefix_num")].'</p></td>
								<td width="60" align="center" ><p>'.$row[csf("year")].'</p></td>
								<td width="100" align="center" ><p>'.$buyer_arr[$row[csf("buyer_name")]].'</p></td>
								<td width="100" align="center" ><p>'.$buyer_arr[$row[csf("client_id")]].'</p></td>
								<td width="110" align="center"><p>'.$row[csf("po_number")].'</p></td>
								<td width="125" align="center"><p>'.$row[csf("grouping")].'</p></td>
								<td width="125" align="center" ><p>'.$delv_comp.'</p></td>
								<td width="125" align="center" ><p>'.$location_library[$row[csf("del_location")]].'</p></td>
								<td width="125" align="center" ><p>'.$floor_no.'</p></td>
								<td width="120" align="center"><p>'.$challan_no.'</p></td>
								<td width="100" align="center"><p>';
								$inv_id=""; $lc_sc_no=""; $ship_mode="";
								foreach($invoce_id_arr as $invoice_id)
								{
									if($inv_id=="") $inv_id=$invoice_array[$invoice_id]; else $inv_id=$inv_id.",".$invoice_array[$invoice_id];
									if($ship_mode=="") $ship_mode=$shipment_mode[$shipping_mode_array[$invoice_id]]; else $ship_mode=$ship_mode.",".$shipment_mode[$shipping_mode_array[$invoice_id]];
									if($lc_sc_type_arr[$invoice_id]==1)
									{
										if($lc_sc_no=="") $lc_sc_no=$lc_num_arr[$lc_sc_id_array[$invoice_id]]; else $lc_sc_no.=",".$lc_num_arr[$lc_sc_id_array[$invoice_id]];
									}
									else
									{
										if($lc_sc_no=="") $lc_sc_no=$sc_num_arr[$lc_sc_id_array[$invoice_id]]; else $lc_sc_no.=",".$sc_num_arr[$lc_sc_id_array[$invoice_id]];
									}
								}

			$details_report .=$inv_id.'</p></td>
								<td width="100" align="center"><p>'.$lc_sc_no.'</p></td>
								<td width="80"><p>'.$design_source_arr[$row[csf("DESIGN_SOURCE_ID")]].'</p></td>
								<td width="100"><p>'.$row[csf("style_ref_no")].'</p></td>
								<td width="100"><p>'.$row[csf("style_description")].'</p></td>
								<td width="110" align="center"><p>';
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
								// $current_ex_Fact_Qty=$row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]][$row[csf("challan_id")]];
								$current_ex_Fact_Qty=$row[csf("ex_factory_qnty")]-$exfact_return_qty;
								$basic_qnty=($total_ex_fact_qty*$item_smv)/$basic_smv_arr[$row[csf("company_name")]];
								$cm_per_pcs_tot+=($cm_per_pcs*$current_ex_Fact_Qty);
								$short_excess=$total_ex_fact_qty-$po_quantity;

								$excess_msg=($short_excess>0) ?" color:green;":"color:black;";
								$excess_val_msg=(($total_ex_fact_qty*$unit_price)-$po_quantity>0) ?" color:green;":"color:black;";
								$ttl_ex_qty=($total_ex_fact_qty/$po_quantity)*100;
								$ttl_ex_qty_msg=($ttl_ex_qty>100) ?" color:green;":"color:black;";

								// ============================ calculate upcharge ===============================
								$po_wise_ttl_ex_qty[$row[csf("po_id")]] += $current_ex_Fact_Qty;
								// echo $poExQtyArray[$row[csf("po_id")]]['poQty'] ."<=". $po_wise_ttl_ex_qty[$row[csf("po_id")]]."<br>";

								if($poExQtyArray[$row[csf("po_id")]]['poQty'] >= $po_wise_ttl_ex_qty[$row[csf("po_id")]])
								{
									$current_ex_up_charge = ($row[csf('up_charge')]/$po_quantity)*$current_ex_Fact_Qty;
									$current_ex_up_charge_value = $current_ex_up_charge + ($current_ex_Fact_Qty*$unit_price);

									$po_wise_ttl_up_charge[$row[csf("po_id")]] += $current_ex_up_charge;

									// echo $current_ex_up_charge ."+((".$current_ex_Fact_Qty."-".$excessExQty.")*".$unit_price.")<br>";

									if($total_ex_fact_qty>$poExQtyArray[$row[csf("po_id")]]['poQty'])
									{
										$total_ex_up_charge = ($row[csf('up_charge')]/$po_quantity)*$poExQtyArray[$row[csf("po_id")]]['poQty'];
										$total_ex_up_charge_value = $total_ex_up_charge + ($total_ex_fact_qty*$unit_price);
									}
									else
									{
										$total_ex_up_charge = ($row[csf('up_charge')]/$po_quantity)*$total_ex_fact_qty;
										$total_ex_up_charge_value = $total_ex_up_charge + ($total_ex_fact_qty*$unit_price);
									}

									$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['current_ex_up_charge_value'] += $current_ex_up_charge_value;
								}
								else
								{
									$excessExQty = $po_wise_ttl_ex_qty[$row[csf("po_id")]] - $poExQtyArray[$row[csf("po_id")]]['poQty'];
									$bal_qty = $current_ex_Fact_Qty-$excessExQty;
									if($bal_qty<1) $bal_qty=0;
									$current_ex_up_charge = ($row[csf('up_charge')]/$po_quantity)*$bal_qty;
									// echo $excess_ex_up_charge = ($row[csf('up_charge')]/$po_quantity)*$excessExQty;
									$current_ex_up_charge_value = $current_ex_up_charge + ($current_ex_Fact_Qty*$unit_price);

									if($bal_qty<1) $bal_qty=1;
									$current_ex_up_charge = ($row[csf('up_charge')]/$po_quantity)*$bal_qty;

									$total_ex_up_charge = $row[csf('up_charge')];
									// $total_ex_up_charge = ($row[csf('up_charge')]/$po_quantity)*($total_ex_fact_qty-$excessExQty);
									// echo "(".$row[csf('up_charge')]."/".$po_quantity.")*(".$total_ex_fact_qty."-".$excessExQty.")<br>";
									$total_ex_up_charge_value = $total_ex_up_charge + ($total_ex_fact_qty*$unit_price);
									// echo $total_ex_up_charge ."+".$total_ex_fact_qty."*".$unit_price."<br>";

									$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['current_ex_up_charge_value'] += $current_ex_up_charge_value;
								}

			$total_sales_minutes=($current_ex_Fact_Qty*$item_smv);
			$gr_sales_min+=$total_sales_minutes;

								$temp_focclaim_arr=array();
								foreach(explode(',',$row[csf("foc_or_claim")]) as $foc_id){
									$temp_focclaim_arr[$foc_id]=$foc_claim_arr[$foc_id];
								}
								$row[csf("foc_or_claim")]=implode(',',$temp_focclaim_arr);

			$details_report .=$item_name_all.'</p></td>
								<td width="80" align="center"><p>'.number_format($item_smv,2).'</p></td>
								<td width="70" align="center"><p>'.change_date_format($row[csf("shipment_date")]).'</p></td>
								<td width="70" align="center"><a href="##" onclick="openmypage_ex_date('.$comapny_id.",'".$row[csf('po_id')]."','".$row[csf('item_number_id')]."','".$ex_fact_date_range."','ex_date_popup','".$row[csf('challan_id')]."'".",'1'".')">'.change_date_format($row[csf('ex_factory_date')]).'</a></td>
								<td width="100" align="center"><p>'.$shipment_mode[$row[csf('ship_mode')]].'</p></td>
								<td width="70" align="center"><p>'.$shipment_mode[$row[csf("shiping_mode")]].'</p></td>
								<td width="100" align="center"><p>'.$row[csf("foc_or_claim")].'</p></td>

								<td width="60" align="center" style="'.$diff_color.'"><p>('.$diff.')</p></td>
								<td width="100" align="center"><p>'.$unit_of_measurement[$row[csf('order_uom')]].'</p></td>
								<td width="80" align="right"><p>'. number_format($po_quantity,0,'', '').'</p></td>
								<td width="70" align="right"><p>'. number_format($unit_price,4).'</p></td>
								<td width="100" align="right"><p>'. number_format(($po_quantity*$unit_price),2).'</p></td>
								<td width="80" align="right"><p>'.number_format($row[csf("up_charge")],2,'.', '').'</p></td>
								<td width="80" align="right"><p><a href="##" onclick="openmypage_ex_date('.$comapny_id.",'".$row[csf('po_id')]."','".$row[csf('item_number_id')]."','".$ex_fact_date_range."','current_ex_date_popup','".$row[csf('challan_id')]."'".",'1'".')">'. number_format($current_ex_Fact_Qty,0,'.', '').'</a></p></td>
								<td width="100" align="right"><p>'. number_format( $current_ex_fact_value=$current_ex_Fact_Qty*$unit_price,2).'</p></td>

								<td width="100" align="right">'.number_format($current_ex_up_charge,2).'</td>
								<td width="100" align="right">'.number_format($current_ex_up_charge_value,2).'</td>

								<td width="80" align="right"><p>'. number_format($row[csf("carton_qnty")],0,'.', '').'</p></td>
								<td width="80" align="right"><p><a href="##" onclick="openmypage_ex_date('.$comapny_id.",'".$row[csf('po_id')]."','".$row[csf('item_number_id')]."','".$total_exface_qnty."','ex_date_popup','".$row[csf('challan_id')]."'".",'1'".')">'.number_format($total_ex_fact_qty,0,'.', '').'</a></p></td>
								<td width="100" align="right"><p>'. number_format($total_ex_fact_value=$total_ex_fact_qty*$unit_price,2).'</p></td>
								<td width="100" align="right">'.number_format($exfact_return_qty,0).'</td>

								<td width="100" align="right">'.number_format($total_ex_up_charge,2).'</td>
								<td width="100" align="right">'.number_format($total_ex_up_charge_value,2).'</td>

								<td width="80" align="right"><p>'.number_format($total_cartoon_qty,0,'.', '').'</p></td>
								<td width="100" align="right"><p>'. number_format($total_sales_minutes,0,'', '').'</p></td>
								<td width="80" align="right"><p>'.number_format($basic_qnty,0,'','').'</p></td>
								<td width="80" align="right" style="'.$excess_msg.'" ><p>'. number_format($excess_shortage_qty=$total_ex_fact_qty-$po_quantity,0,'', '').'</p></td>
								<td width="100" align="right" style="'.$excess_val_msg.'"><p>'. number_format($excess_shortage_value=($excess_shortage_qty*$unit_price),2).'</p></td>
								<td align="center" style="'.$ttl_ex_qty_msg.'" width="80"><p>'. number_format($total_ex_fact_qty_parcent=($total_ex_fact_qty/$po_quantity)*100,0).'</p></td>
								<td width="60" align="center" title="CM per pcs: '.number_format($cm_per_pcs,4).'*'.$current_ex_Fact_Qty.'"><p>'.number_format($cm_per_pcs*$current_ex_Fact_Qty,2).'</p></td>
								<td width="100" align="center"><p>'.$forwarder.'</p></td>
								<td width="80" align="center"><p>'.$vehi_no.'</p></td>
								<td width="130"><p>'.$dirver_info.'</p></td>
								<td width="70" align="center"><p>'.(change_date_format($inspection_date_arr[$row[csf('po_id')]]) == '0000-00-00' || change_date_format($inspection_date_arr[$row[csf('po_id')]]) == '' ? '' : change_date_format(change_date_format($inspection_date_arr[$row[csf('po_id')]]))).'</p></td>
								<td align="center"><p>'.$shipment_status[$row[csf('shiping_status')]].'</p></td>
							</tr>';

			//$master_data[$row[csf("buyer_name")]]['po_qnty'] +=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]];
 	  		//$master_data[$row[csf("buyer_name")]]['po_value'] +=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]]*$row[csf("unit_price")];
 	  		$total_po_qty+=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]];
 	  		$total_po_valu+=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]]*$row[csf("unit_price")];

			// $master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['ex_factory_qnty'] +=$row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]][$row[csf("challan_id")]];
			$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['ex_factory_qnty'] +=$row[csf("ex_factory_qnty")]-$exfact_return_qty;
			// $master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['ex_factory_value'] +=($row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]][$row[csf("challan_id")]])*$row[csf("unit_price")];
			$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['ex_factory_value'] +=($row[csf("ex_factory_qnty")]-$exfact_return_qty)*$row[csf("unit_price")];

			$total_basic_qty+=$basic_qnty;

			$total_ex_qty+=$row[csf("ex_factory_qnty")]-$exfact_return_qty;
			$total_crtn_qty+=$row[csf("carton_qnty")];
			$total_ex_valu+=($row[csf("ex_factory_qnty")]-$exfact_return_qty)*$row[csf("unit_price")];
			$g_total_ex_qty+=$total_ex_fact_qty;
			$g_total_ex_crtn+=$total_cartoon_qty;
			$g_total_ex_val+=$total_ex_fact_qty*$row[csf("unit_price")];
			$g_sales_minutes+=$total_sales_minutes;

			// $g_sales_minutes+=$current_ex_Fact_Qty*$item_smv;
			$total_eecess_storage_qty+=$excess_shortage_qty;
			$total_eecess_storage_val+=$excess_shortage_value;
			if($po_check_arr[$row[csf("po_id")]][$row[csf("item_number_id")]]=="")
			{
				$po_check_arr[$row[csf("po_id")]][$row[csf("item_number_id")]]=$row[csf("po_id")];
				$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['b_id']=$row[csf("buyer_name")];
				$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['org_po_qnty'] +=$row[csf("po_quantity")];
				$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['upchage'] +=$row[csf("up_charge")];
				$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['po_qnty'] +=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]];
				$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['po_value'] +=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]]*$row[csf("unit_price")];
				$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['basic_qnty'] +=$basic_qnty;
				$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['total_ex_fact_qty'] +=$total_ex_fact_qty;
				$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['total_ex_fact_value'] +=$total_ex_fact_qty*$row[csf("unit_price")];



				$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['current_ex_up_charge'] += $current_ex_up_charge;
				// $master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['current_ex_up_charge_value'] += $current_ex_up_charge_value;
				$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['total_ex_up_charge'] += $total_ex_up_charge;
				$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['total_ex_up_charge_value'] += $total_ex_up_charge_value;

 	  			$gr_po_qnty_pcs+=$po_quantity;
	  			$gr_po_qnty_val+=$po_quantity*$unit_price;
 	  			$gr_ttl_ex_qnty+=$total_ex_fact_qty;
	  			$gr_ttl_ex_qnty_val+=$total_ex_fact_value;
	  			$gr_ttl_carton_qt+=$total_cartoon_qty;
	  			//$gr_sales_min+=$total_sales_minutes;
	  			$gr_ttl_basic_qty+=$basic_qnty;
	  			$gr_ttl_ex_fac_per+=$total_ex_fact_qty_parcent;
	  			$gr_ttl_short_qty+=$excess_shortage_qty;
	  			$gr_ttl_short_val+=$excess_shortage_value;
	  			$gr_ttl_sales_cm+=$cm_per_pcs*$total_ex_fact_qty;
				$total_po_val+=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]]*$row[csf("unit_price")];
				$gr_upcharge+=$row[csf("up_charge")];

				$gr_current_ex_up_charge += $current_ex_up_charge;
				$gr_current_ex_up_charge_value += $current_ex_up_charge_value;

				$gr_total_ex_up_charge += $total_ex_up_charge;
				$gr_total_ex_up_charge_value += $total_ex_up_charge_value;
			}
			$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['total_ex_rtn_qty'] += $exfact_return_qty;
			$gr_exfact_return_qty+=$exfact_return_qty;

			$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['sales_min'] += $item_smv*($row[csf("ex_factory_qnty")]-$exfact_return_qty);

			$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['sales_cm'] += $cm_per_pcs*$current_ex_Fact_Qty;
			// echo $cm_per_pcs."*".$current_ex_Fact_Qty."<br>";
			$i++; $item_name_all="";
		}
		$pp=$i;
		// echo "<pre>";print_r($master_data);die();
		?>
        <div style="width:4900x;">
            <div style="width:2010px" >
                <table width="2010"  cellspacing="0"  align="center">
                    <tr>
                        <td align="center" colspan="15" class="form_caption">
                            <strong style="font-size:16px;">Company Name:<? echo  $company_library[$cbo_company_name] ;?></strong>
                        </td>
                    </tr>
                    <tr class="form_caption">
                        <td colspan="15" align="center" class="form_caption"> <strong style="font-size:15px;">Ex-Factory Report</strong></td>
                    </tr>
                     <tr align="center">
                        <td colspan="15" align="center" class="form_caption"> <strong style="font-size:15px;">Total Summary</strong></td>
                    </tr>
                </table>
                <table width="2010" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1">
                    <thead>
                        <th width="30">SL</th>
                        <th width="110">Buyer Name</th>
                        <th width="100">Client</th>
                        <th width="100">PO Qty</th>
                        <th width="100">PO Qty (pcs)</th>
                        <th width="130">PO Value</th>
                        <th width="100">PO Value(%)</th>
                        <th width="100">PO TTL Up-Charge</th>
                        <th width="100">Current Ex-Fact. Qty.</th>
                        <th width="130">Current Ex-Fact. Value</th>
                        <th width="130">Up Charge with Current Ex-Fact. Value</th>
                        <th width="100">Total Ex-Fact. Qty.</th>
                        <th width="130">Total Ex-Fact. Value</th>
                        <th width="80">Ex-Fact. Rtn Qty</th>
                        <th width="130">Up Charge with Total Ex-Fact. Value</th>
                        <th width="100" title="Item SMV*Current Ex-Fact. Qty.">Sales Minutes</th>
                        <th width="100">Sales CM</th>
                        <th width="100">Total Ex-Fact. (Basic Qty)</th>
                        <th>Total Ex-Fact. Value %</th>
                    </thead>
             </table>
             <div style="width:2030px; max-height:225px; overflow-y:scroll" id="scroll_body1">
             <table width="2010" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body">
                 <?
                $m=1; $grand_sales_minute =0;
                foreach($master_data as $buyid=>$buyData)
                {
					foreach($buyData as $clientid=>$cdata)
					{
						if ($m%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$buyer_po_quantity=0; $buyer_po_value=0; $current_ex_Fact_Qty=0; $current_ex_fact_value=0; $total_ex_fact_qty=0; $total_ex_fact_value=0; $g_sales_min=0;$g_sales_cm=0;


						$po_quantity=$cdata['po_qnty'];
						$buyer_po_value=$cdata["po_value"];
						$parcentages+=($buyer_po_value/$total_po_val)*100;
						$current_ex_Fact_Qty=$cdata['ex_factory_qnty'];
						$current_ex_fact_value=$cdata['ex_factory_value'];
						$total_ex_fact_qty=$cdata['total_ex_fact_qty'];
						$total_ex_fact_value=$cdata['total_ex_fact_value'];
						$total_ex_rtn_qty = $cdata['total_ex_rtn_qty'];
						$buyer_basic_qnty=$cdata["basic_qnty"];
						$total_ex_fact_value_parcentage=($total_ex_fact_value/$buyer_po_value)*100;
						?>
						<tr bgcolor="<?=$bgcolor;?>" onclick="change_color('tr_<?=$i;?>','<?=$bgcolor;?>')" id="tr_<?=$i;?>" >
							<td width="30" align="center"><?=$m;?></td>
							<td width="110" style="word-break:break-all"><?=$buyer_arr[$buyid];?></td>
							<td width="100" style="word-break:break-all"><?=$buyer_arr[$clientid];?></td>
							<td width="100" style="word-break:break-all" align="right"><?=number_format($cdata["org_po_qnty"], 0);?></td>
							<td width="100" style="word-break:break-all" align="right"><?=number_format($po_quantity, 0);?></td>
							<td width="130" style="word-break:break-all" align="right"><p id="value_<?=$i;?>"><?=number_format($buyer_po_value, 2, '.', '');?></p></td>
							<td width="100" style="word-break:break-all" align="right"><?=number_format(($buyer_po_value / $total_po_val) * 100, 2, '.', '');?></td>
                            <td width="100" style="word-break:break-all" align="right"><?=number_format($cdata["upchage"], 2, '.', '');?> </td>
							<td width="100" style="word-break:break-all" align="right"><?=number_format($current_ex_Fact_Qty, 0, '', '');?></td>
							<td width="130" style="word-break:break-all" align="right"><?=number_format($current_ex_fact_value, 2, '.', '');?></td>
							<td width="130" style="word-break:break-all" align="right"><?=number_format($cdata['current_ex_up_charge_value'], 2, '.', '');?></td>
							<td width="100" style="word-break:break-all" align="right"><?=number_format($total_ex_fact_qty, 0, '', '');?></td>
							<td width="130" style="word-break:break-all" align="right"><?=number_format($total_ex_fact_value, 2, '.', '');?> </td>
							<td width="80" style="word-break:break-all" align="right"><?=number_format($total_ex_rtn_qty, 0, '.', '');?> </td>
							<td width="130" style="word-break:break-all" align="right"><?=number_format($cdata['total_ex_up_charge_value'], 2, '.', '');?> </td>
							<td width="100" style="word-break:break-all" align="right"><? echo $g_sales_min+= number_format($cdata["sales_min"],0,'',''); ?></td>
							<td width="100" style="word-break:break-all" align="right"><? echo $g_sales_cm+= number_format($cdata["sales_cm"],0,'','');?></td>
							<td width="100" style="word-break:break-all" align="right"><?=number_format($buyer_basic_qnty, 0, '', '');?></td>
							<td style="word-break:break-all" align="right"><?=number_format($total_ex_fact_value_parcentage, 0)?> %</td>
						</tr>
						<?
						$i++; $m++;

						$grand_sales_minute +=number_format($cdata["sales_min"],0,'','');
						$grand_sales_cm +=number_format($cdata["sales_cm"],0,'','');
						$total_buyer_org_po_quantity+=$cdata["org_po_qnty"];
						$total_buyer_po_quantity+=$po_quantity;
						$total_buyer_po_value+=$buyer_po_value;
						$total_current_ex_Fact_Qty +=$current_ex_Fact_Qty;
						$total_current_ex_fact_value+=$current_ex_fact_value;
						$mt_total_ex_fact_qty+=$total_ex_fact_qty;
						$mt_total_ex_fact_value+=$total_ex_fact_value;
						$total_buyer_basic_qnty +=$buyer_basic_qnty;
						$buyerTotUpCharge +=$cdata["upchage"];

					}
                }
				?>
                </table>
                </div>
				<input type="hidden" name="total_i" id="total_i" value="<?=$i;?>" />
                <table class="tbl_bottom" width="2010" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                    	<td width="30">&nbsp;</td>
                        <td width="110" align="right"><b>Total:</b></td>
                        <td width="100">&nbsp;</td>
                        <td width="100" align="right" id="total_buyer_org_po_quantity"><?=number_format($total_buyer_org_po_quantity, 0);?></td>
                        <td width="100"align="right" id="total_buyer_po_quantity"><?=number_format($total_buyer_po_quantity, 0);?></td>
                        <td width="130" align="right" id="value_total_buyer_po_value"><?=number_format($total_buyer_po_value, 2, '.', '');?></td>
                        <td width="100" align="right" id="parcentages"><?=ceil($parcentages);?></td>
                        <td width="100" align="right" id="value_upcharge"><?=number_format($buyerTotUpCharge, 2);?></td>
                        <td width="100" align="right" id="total_current_ex_Fact_Qty"><?=number_format($total_current_ex_Fact_Qty, 0);?></td>
                        <td width="130" align="right" id="value_total_current_ex_fact_value"><?=number_format($total_current_ex_fact_value, 2);?></td>
                        <td width="130" align="right" id="value_total_current_ex_fact_value_with_up_charge"><? //=number_format($total_current_ex_fact_value, 2);?></td>
                        <td width="100" align="right" id="mt_total_ex_fact_qty"><?=number_format($mt_total_ex_fact_qty, 0);?></td>
                        <td width="130" align="right" id="value_mt_total_ex_fact_value"><?=number_format($mt_total_ex_fact_value, 2);?></td>
                        <td width="80" align="right" id="value_mt_total_ex_fact_rtn"><?=number_format($mt_total_ex_fact_value, 2);?></td>
                        <td width="130" align="right" id="value_mt_total_ex_fact_value_with_up_charge"><? //=number_format($mt_total_ex_fact_value, 2);?></td>
                        <td width="100" align="right" id="sales_minutes"><?=number_format($grand_sales_minute, 2);?></td>
                        <td width="100" align="right" id="sales_cm"><?=number_format($grand_sales_cm, 2);?></td>
                        <td width="100" align="right" id="total_buyer_basic_qnty"><?=number_format($total_buyer_basic_qnty, 0);?></td>
                        <td>&nbsp;</td>
                    </tfoot>
                </table>
            </div>
            <br />
            <!-- ==================================== details part ================================== -->
            <div>
                <table width="4760">
                    <tr>
                    	<td colspan="50" class="form_caption"><strong style="font-size:16px;">Shipped Out Order Details Report</strong></td>
                    </tr>
                </table>
                <table width="4890" border="1" class="rpt_table" rules="all" id="table_header_2">
                    <thead>
                        <th width="30">SL</th>
                        <th width="130">Company</th>
                        <th width="60">Job</th>
                        <th width="60">Year</th>
                        <th width="100">Buyer Name</th>
                        <th width="100">Client</th>
                        <th width="110">Order NO</th>
						<th width="125">Internal ref.</th>
                        <th width="125">Del Company</th>
                        <th width="125">Del Location</th>
                        <th width="125">Del Floor</th>
                        <th width="120">Challan NO</th>
                        <th width="100">Invoice NO</th>
                        <th width="100">LC/SC NO</th>
                        <th width="80">Design Source</th>
                        <th width="100">Style Ref. no.</th>
                        <th width="100">Style Description</th>
                        <th width="110">Item Name</th>
                        <th width="80">Item SMV</th>
                        <th width="70">Shipment Date</th>
                        <th width="70">Ex-Fac. Date</th>
                        <th width="100"><p>Po Rcv.Ship Mode</p></th>
                        <th width="70">Shipping Mode</th>
                        <th width="100">FOC/Claim</th>
                        <th width="60">Days in Hand</th>
                        <th width="100">UOM</th>
                        <th width="80">PO Qtny. (pcs)</th>
                        <th width="70">Unit Price</th>
                        <th width="100">PO Value</th>
                        <th width="80">PO TTL Up-Charge</th>
                        <th width="80">Current Ex-Fact. Qty (pcs)</th>
                        <th width="100">Current Ex-Fact. Value</th>

                        <th width="100">UP Charge Based on Current Ex-facotry</th>
                        <th width="100">Current Ex-Fact. Value with Up Charge</th>

                        <th width="80">Current Carton Qty</th>
                        <th width="80">Total Ex-Fact. Qty.</th>
                        <th width="100">Total Ex-Fact. Value</th>
                        <th width="100">Ex-Fact. Rtn Qty</th>

                        <th width="100">Up Charge Based on TTL Ex-factory</th>
                        <th width="100">Up charge with Total Ex-Fact. Value</th>

                        <th width="80">Total Carton Qty</th>
                        <th width="100" title="Item SMV*Current Ex-Fact. Qty.">Sales Minute</th>
                        <th width="80">Total Ex-Fact. (Basic Qty)</th>
                        <th width="80">Excess/ Shortage Qty</th>
                        <th width="100">Excess/ Shortage Value</th>
                        <th width="80">Total Ex-Fact. Qty. %</th>
                        <th width="60">Sales CM</th>
                        <th width="100">C & F Name</th>
                        <th width="80">Vehicle No</th>
                        <th width="130">Driver Info</th>
                        <th width="70">Inspection Date</th>
                        <th>Ex-Fact Status</th>
                    </thead>
                </table>
            <div style="width:4890px; overflow-y:scroll; overflow-x:hidden; max-height:300px;"  id="scroll_body" >
            <? echo $details_report;
            	foreach($subcon_exfactory_arr___ as $po_id=>$item_data)
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
	            						<tr onclick="<?=$onclick;?>" id="tr2_<?=$jj;?>" >
	            							<td width="30" align="center"><? echo $pp++;?></td>
	            							<td width="130" align="center"><p><? echo $company_library[$row["company_name"]]; ?> </p></td>
	            							<td width="60" align="center"><p><? echo $row["job"]; ?> </p></td>
	            							<td width="60" align="center"><p><? $arr_year=explode('-',change_date_format($row["insert_date"]));echo $arr_year[2]; ?> </p></td>
	            							<td width="100" align="center"><p><? echo $row["buyer_name"]; ?> </p></td>
                                            <td width="100" align="center">&nbsp;</td>
	            							<td width="110" align="center"><p><? echo $row["po_number"]; ?> (In-Sub) </p></td>
	            							<td width="125" align="center"><p><? echo $company_library[$delivery_company_id]; ?> </p></td>
	            							<td width="125" align="center"><p><? echo $location_library[$delivery_loc_id]; ?> </p></td>
	            							<td width="125" align="center"><p>&nbsp;</p></td>
	            							<td width="120" align="center"><p><? echo $row["delivery_no"]; ?> </p></td>
	            							<td width="100" align="center"><p>&nbsp;</p></td>
	            							<td width="100" align="center"><p>&nbsp;<? //echo $lc_sc_no; ?> </p></td>
	            							<td width="100"><p><? echo $row["style_ref_no"]; ?> </p></td>
	            							<td width="100"><p>&nbsp;<? //echo $row["style_description")]; ?> </p></td>
	            							<td width="110" align="center"><p><? echo $garments_item[$item_id];?></p></td>
	            							<td width="80" align="center"><p><? echo $item_smv=$row["smv"]; ?> </p></td>
	            							<td width="70" align="center"><p><? echo change_date_format($row["shipment_date"]); ?> </p></td>
	            							<td width="70" align="center"><a href="##" onclick="openmypage_ex_date(<? echo $row["company_name"].",'".$po_id."','".$item_id."','".$date_id."','ex_date_popup','".$row['delivery_no']."'".',2'; ?> )"><? echo change_date_format($date_id); ?> </a></td>
	            							<td width="100" align="center"><p><? echo $shipment_mode[$row['ship_mode']]; ?> </p></td>
	            							<td width="70" align="center"><p><? echo $shipment_mode[$row["shiping_mode"]]; ?> </p></td>
	            							<td width="100" align="center"><p><? echo $foc_or_claim[$row["foc_or_claim"]]; ?> </p></td>
	            							<td width="60" align="center" style=" <?=$diff_color;?>"><p>(<? echo $diff; ?> )</p></td>
	            							<td width="100" align="center"><p> <? echo $unit_of_measurement[$row["order_uom"]]; ?>  </p></td>
	            							<td width="80" align="right"><p><? echo  number_format($po_quantity,0,'', ''); ?> </p></td>
	            							<td width="70" align="right"><p><? echo  number_format($unit_price,4); ?> </p></td>
	            							<td width="100" align="right"><p><? echo  number_format(($po_quantity*$unit_price),2); ?> </p></td>


	            							<td width="80" align="right"><p><a href="##" onclick="openmypage_ex_date(<? echo $row["company_name"].",'".$po_id."','".$item_id."','".$date_id."','ex_date_popup','".$row['delivery_no']."'".',2'; ?> )"><?
	            							$current_ex_Fact_Qty=$row["prod_qty"]; echo  number_format($current_ex_Fact_Qty,0,'.', ''); ?> </a></p></td>
	            							<td width="100" align="right"><p><? echo  number_format( $current_ex_fact_value=$current_ex_Fact_Qty*$unit_price,2); ?> </p></td>

                                            <td width="80" align="right">&nbsp;</td>
	            							<td width="80" align="right"><p><? echo  number_format($row["total_carton_qnty"],0,'', ''); ?> </p></td>
											<td width="100" align="right">&nbsp;</td>
	            							<td width="80" align="right"><p><a href="##" onclick="openmypage_ex_date(<? echo $row["company_name"].",'".$po_id."','".$item_id."','".$all_date."','ex_date_popup','".$delivery_company_id.'_'.$delivery_loc_id."'".',2'; ?> )" ><? echo number_format($total_ex_fact_qty,0,' ', ''); ?> </a></p></td>
	            							<td width="100" align="right"><p><? echo  number_format($total_ex_fact_value=$total_ex_fact_qty*$unit_price,2); ?> </p></td>
	            							<td width="80" align="right"><p><? echo number_format($total_cartoon_qty,0,'.', ''); ?> </p></td>
	            							<td width="100" align="right"><p><? echo  number_format($total_sales_minutes=$total_ex_fact_qty*$item_smv); ?> </p></td>
	            							<td width="80" align="right"><p><? echo number_format($basic_qnty,0,'',''); ?> </p></td>
	            							<td width="80" align="right" style=" <? echo $excess_msg; ?> " ><p><? echo  number_format($excess_shortage_qty=$total_ex_fact_qty-$po_quantity,0,'', ''); ?> </p></td>
	            							<td width="100" align="right" style=" <? echo $excess_val_msg; ?> "><p><? echo  number_format($excess_shortage_value=($total_ex_fact_qty*$unit_price)-$po_quantity,2); ?> </p></td>
	            							<td align="center" style=" <? echo $ttl_ex_qty_msg; ?> " width="80"><p><? echo  number_format($total_ex_fact_qty_parcent=($total_ex_fact_qty/$po_quantity)*100,0); ?> </p></td>
	            							<td width="60" align="center" title="<?=$cm_per_pcs."*".$current_ex_Fact_Qty;?>"><p><? echo number_format($cm_per_pcs*$current_ex_Fact_Qty,2); ?> </p></td>
	            							<td width="100" align="center"><p><? echo $forwarder; ?> </p></td>
	            							<td width="80" align="center"><p><? echo $row["vehical_no"]; ?> </p></td>
	            							<td width="130"><p><? echo $dirver_info; ?> </p></td>
	            							<td width="70" align="center"><p><? echo (change_date_format($inspection_date_arr[$row['po_id']]) == '0000-00-00' || change_date_format($inspection_date_arr[$row['po_id']]) == '' ? '' : change_date_format(change_date_format($inspection_date_arr[$row['po_id']]))); ?> </p></td>
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

            <table width="4890" cellspacing="0" cellpadding="0"  border="1"  class="rpt_table" rules="all" id="table_footer">
                <tfoot>
                    <tr>
                    	<th width="30">&nbsp;</th>
                        <th width="130">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="125">&nbsp;</th>
                        <th width="125">&nbsp;</th>
                        <th width="125">&nbsp;</th>
                        <th width="125">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100" >&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="100" align="right"><strong>Total</strong></th>
                        <th width="80" id="total_po_qtybk" align="right"><? echo  number_format($gr_po_qnty_pcs,0);?></th>
                        <th width="70" align="right">&nbsp;</th>
                        <th width="100" align="right" id="value_total_po_valubk"><? echo  number_format($gr_po_qnty_val,2); ?></th>

                        <th width="80" align="right" id="value_tdupcharge"><? echo number_format($gr_upcharge,0);?></th>
                        <th width="80" align="right" id="total_ex_qty"><? echo number_format($total_ex_qty,0); ?></th>
                        <th width="100" align="right" id="value_total_ex_valu"><? echo number_format($total_ex_valu,2);?></th>

                        <th width="100" align="right" id="value_current_ex_up_charge"><? echo number_format($gr_current_ex_up_charge,2); ?></th>
                        <th width="100" align="right" id="value_current_ex_up_charge_value"><? echo number_format($gr_current_ex_up_charge_value,2); ?></th>

                        <th width="80" align="right" id="total_crtn_qty"><? echo number_format($gr_ttl_carton,0); ?></th>
                        <th width="80" align="right" id="g_total_ex_qtybk"><? echo number_format($gr_ttl_ex_qnty,0);?></th>
                        <th width="100" align="right" id="value_g_total_ex_valbk"><? echo number_format($gr_ttl_ex_qnty_val,2);?></th>
                        <th width="100" align="right" id="value_g_total_ex_rtn"><? echo number_format($gr_exfact_return_qty,0);?></th>

                        <th width="100" align="right" id="value_total_ex_up_charge"><? echo number_format($gr_total_ex_up_charge,2); ?></th>
                        <th width="100" align="right" id="value_total_ex_up_charge_value"><? echo number_format($gr_total_ex_up_charge_value,2); ?></th>

                        <th width="80" align="right" id="g_total_ex_crtnbk"><? echo number_format($gr_ttl_carton_qt,0);?></th>
                        <th width="100" align="right" id="value_sales_minutesbk"><? echo number_format($gr_sales_min);?></th>

                        <th width="80" align="right" id="total_basic_qtybk"><? echo number_format($gr_ttl_basic_qty,0); ?></th>
                        <th width="80" align="right" id="total_eecess_storage_qtybk"><? echo number_format($gr_ttl_short_qty,0);?></th>
                        <th width="100" align="right" id="value_total_eecess_storage_valbk"><? echo number_format($gr_ttl_short_val,0);?></th>
                        <th width="80" id="total_ex_perbk"><? echo number_format($gr_ttl_ex_fac_per,0);?></th>
                        <th width="60" align="right" id="value_cm_per_pcs_totbk"><? echo number_format($gr_ttl_sales_cm,2);?></th>
                        <th width="100">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="130">&nbsp;</th>
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

	else if($reportType==18)//EX VS INV
	{

		$company=str_replace("'", "", $cbo_company_name);
		$buyer_name=str_replace("'", "", $cbo_buyer_name);
		$delv_comp=str_replace("'", "", $cbo_delivery_company_name);
		$delv_floor=str_replace("'", "", $cbo_del_floor);
		$location_name=str_replace("'", "", $cbo_location_name);

		$sql_cond="";

		$sql_cond.=($company)? " and a.company_id='$company'" : " ";
		$buyer_cond1.=($buyer_name)? " and d.buyer_name='$buyer_name'" : " ";
		$buyer_cond2.=($buyer_name)? " and a.buyer_id='$buyer_name'" : " ";
		$sql_cond.=($delv_comp)? " and a.delivery_company_id in($delv_comp)" : " ";
		$sql_cond.=($location_name)? " and a.delivery_location_id in($location_name)" : " ";
		$sql_cond.=($delv_floor)? " and a.delivery_floor_id in($delv_floor)" : " ";

		if($txt_date_from!="" && $txt_date_to!="")
		{
			$sql_cond.=" and b.ex_factory_date between '$txt_date_from' and  '$txt_date_to ' ";
		}
		$buyer_arr=return_library_array( "select id,buyer_name from  lib_buyer", "id", "buyer_name");
		$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
		$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
		$location_library=return_library_array( "select id,location_name from  lib_location", "id", "location_name"  );
		$invoice_array=return_library_array( "select id,invoice_no from com_export_invoice_ship_mst", "id", "invoice_no"  );

		$sql_for_summary="SELECT a.delivery_company_id,a.delivery_location_id,a.delivery_floor_id,b.po_break_down_id,
		(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		c.po_quantity,c.unit_price,d.buyer_name from pro_ex_factory_delivery_mst a,pro_ex_factory_mst b,wo_po_break_down c,wo_po_details_master d WHERE a.id=b.delivery_mst_id and c.id=b.po_break_down_id and c.job_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $sql_cond $buyer_cond1";

	//   echo $sql_for_summary;die();

		$tot_sql_summary=sql_select($sql_for_summary);
		$summary_arr=array();

		foreach($tot_sql_summary as $row)
		{
             $summary_arr[$row[csf('delivery_company_id')]][$row[csf('delivery_location_id')]][$row[csf('buyer_name')]]['po_quantity']=$row[csf('po_quantity')];

			 $summary_arr[$row[csf('delivery_company_id')]][$row[csf('delivery_location_id')]][$row[csf('buyer_name')]]['unit_price']+=$row[csf('unit_price')];
			//  $summary_arr[$row[csf('delivery_company_id')]][$row[csf('delivery_location_id')]][$row[csf('buyer_name')]]['current_invoice_qnty']+=$row[csf('current_invoice_qnty')];
			 $summary_arr[$row[csf('delivery_company_id')]][$row[csf('delivery_location_id')]][$row[csf('buyer_name')]]['ex_factory_qnty']+=$row[csf('ex_factory_qnty')];
			 $summary_arr[$row[csf('delivery_company_id')]][$row[csf('delivery_location_id')]][$row[csf('buyer_name')]]['delivery_floor_id']=$row[csf('delivery_floor_id')];
			//  $po_arr[$row[csf('po_break_down_id')]][$row[csf('delivery_company_id')]][$row[csf('delivery_location_id')]][$row[csf('buyer_name')]]['po_quantity']=$row[csf('po_quantity')];
		}

		// echo '<pre>';
		// print_r($summary_arr);
		// echo '</pre>';
		// =================================================Invoice Qnty==========================================//

         $sql_for_invoice_qnty="SELECT a.delivery_company_id,a.delivery_location_id,a.delivery_floor_id,a.buyer_id,e.current_invoice_rate,e.current_invoice_qnty from pro_ex_factory_delivery_mst a,pro_ex_factory_mst b,com_export_invoice_ship_dtls e WHERE a.id=b.delivery_mst_id and b.invoice_no=e.mst_id and b.po_break_down_id=e.po_breakdown_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $sql_cond $buyer_cond2";

         $sql_invoice=sql_select($sql_for_invoice_qnty);
		 $invoice_arr=array();

		 foreach($sql_invoice as $row)
		 {
			 $invoice_arr[$row[csf('delivery_company_id')]][$row[csf('delivery_location_id')]][$row[csf('buyer_id')]]['current_invoice_rate']+=$row[csf('current_invoice_rate')];
			 $invoice_arr[$row[csf('delivery_company_id')]][$row[csf('delivery_location_id')]][$row[csf('buyer_id')]]['current_invoice_qnty']+=$row[csf('current_invoice_qnty')];

		 }



        	// =================================================Details Part==========================================//


	  	$sql_for_details="SELECT a.company_id,a.delivery_company_id,a.delivery_location_id,a.delivery_floor_id,c.job_no_mst,c.po_number,c.pub_shipment_date,d.style_ref_no,a.sys_number,b.invoice_no,
		(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		c.po_quantity,c.unit_price,d.buyer_name from pro_ex_factory_delivery_mst a,pro_ex_factory_mst b,wo_po_break_down c,wo_po_details_master d WHERE a.id=b.delivery_mst_id and c.id=b.po_break_down_id and c.job_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $sql_cond $buyer_cond1";

		$sql_details=sql_select($sql_for_details);
		$details_arr=array();

		foreach($sql_details as $row)
		{
			$details_arr[$row[csf('company_id')]][$row[csf('delivery_company_id')]][$row[csf('delivery_location_id')]][$row[csf('delivery_floor_id')]][$row[csf('job_no_mst')]][$row[csf('po_number')]][$row[csf('style_ref_no')]][$row[csf('buyer_name')]]['pub_shipment_date']=$row[csf('pub_shipment_date')];
			$details_arr[$row[csf('company_id')]][$row[csf('delivery_company_id')]][$row[csf('delivery_location_id')]][$row[csf('delivery_floor_id')]][$row[csf('job_no_mst')]][$row[csf('po_number')]][$row[csf('style_ref_no')]][$row[csf('buyer_name')]]['po_quantity']=$row[csf('po_quantity')];

			$details_arr[$row[csf('company_id')]][$row[csf('delivery_company_id')]][$row[csf('delivery_location_id')]][$row[csf('delivery_floor_id')]][$row[csf('job_no_mst')]][$row[csf('po_number')]][$row[csf('style_ref_no')]][$row[csf('buyer_name')]]['ex_factory_qnty']+=$row[csf('ex_factory_qnty')];

			$details_arr[$row[csf('company_id')]][$row[csf('delivery_company_id')]][$row[csf('delivery_location_id')]][$row[csf('delivery_floor_id')]][$row[csf('job_no_mst')]][$row[csf('po_number')]][$row[csf('style_ref_no')]][$row[csf('buyer_name')]]['unit_price']+=$row[csf('unit_price')];

			$details_arr[$row[csf('company_id')]][$row[csf('delivery_company_id')]][$row[csf('delivery_location_id')]][$row[csf('delivery_floor_id')]][$row[csf('job_no_mst')]][$row[csf('po_number')]][$row[csf('style_ref_no')]][$row[csf('buyer_name')]]['sys_number'].=$row[csf('sys_number')].",";

			$details_arr[$row[csf('company_id')]][$row[csf('delivery_company_id')]][$row[csf('delivery_location_id')]][$row[csf('delivery_floor_id')]][$row[csf('job_no_mst')]][$row[csf('po_number')]][$row[csf('style_ref_no')]][$row[csf('buyer_name')]]['invoice_no'].=$invoice_array[$row[csf('invoice_no')]].",";


		}


		// echo '<pre>';
		// print_r($details_arr);
		// echo '</pre>';

		$details_invoice_sql="SELECT a.company_id,a.delivery_company_id,a.delivery_location_id,a.delivery_floor_id,a.sys_number,b.invoice_no,e.current_invoice_rate,e.current_invoice_qnty,a.buyer_id from pro_ex_factory_delivery_mst a,pro_ex_factory_mst b,com_export_invoice_ship_dtls e,com_export_invoice_ship_mst f WHERE a.id=b.delivery_mst_id and b.invoice_no=e.mst_id and b.po_break_down_id=e.po_breakdown_id and f.id=e.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $sql_cond $buyer_cond2";

		$details_invoice=sql_select($details_invoice_sql);
		$details_invoice_arr=array();

		foreach($details_invoice as $row)
		{
			$details_invoice_arr[$row[csf('company_id')]][$row[csf('delivery_company_id')]][$row[csf('delivery_location_id')]][$row[csf('delivery_floor_id')]][$row[csf('buyer_id')]]['current_invoice_rate']+=$row[csf('current_invoice_rate')];
			$details_invoice_arr[$row[csf('company_id')]][$row[csf('delivery_company_id')]][$row[csf('delivery_location_id')]][$row[csf('delivery_floor_id')]][$row[csf('buyer_id')]]['current_invoice_qnty']+=$row[csf('current_invoice_qnty')];
		}

		// echo '<pre>';
		// print_r($details_invoice_arr);
		// echo '</pre>';



       $rowspan_arr=array();

	   foreach($summary_arr as $dev_comp=>$dev_comp_val)
	   {
			foreach($dev_comp_val as $dev_location=>$dev_location_val)
			{
				foreach($dev_location_val as $buyer_name=>$row)
				{
					$rowspan_arr[$dev_comp][$dev_location]++;
				}
			}
	   }






      ?>
	  <br>
       	<div style="width:1140px">
			<table class="rpt_table" width="1120" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<th colspan="12">Summary Part</th>
				</thead>
				<thead>
					<tr>
						<th width="20">SI</th>
						<th width="100">Ex-Factory Company</th>
						<th width="100">Ex-Factory Location</th>
						<th width="100">Ex-Factory Floor</th>
						<th width="100">Buyer</th>
						<th width="100">Po.Qty(Pcs)</th>
						<th width="100">Ex-Factory Qty(Pcs)</th>
						<th width="100">Ex.Amount</th>
						<th width="100">Invoice Qty (Pcs)</th>
						<th width="100">Inv.Amount</th>
						<th width="100">Qty Variance</th>
						<th width="100">Amt Variance</th>
	               </tr>
			    </thead>

			  <tbody id="">

	         <?
                 $i=1;

					foreach($summary_arr as $dev_comp=>$dev_comp_val)
				    {
						$comp_po_qty=0;
						$comp_ex_fac_qty=0;
						$comp_ex_amt=0;
						$comp_ex_invoice_qty=0;
						$comp_inv_amt=0;
						$comp_inv_variance=0;
						$comp_amt_variance=0;
						foreach($dev_comp_val as $dev_location=>$dev_location_val)
						{
							$l=0;

							foreach($dev_location_val as $buyer_name=>$row)
							{

								if ($i%2==0)
								$bgcolor="#E9F3FF";
								else
								$bgcolor="#FFFFFF";
								?>
							      <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								  <td><? echo $i;?></td>
								  <?
								  if($l==0)
								  {
									?>
									<td rowspan="<? echo $rowspan_arr[$dev_comp][$dev_location];?>"><p><? echo $company_library[$dev_comp];?></p></td>
									<td rowspan="<? echo $rowspan_arr[$dev_comp][$dev_location];?>"><p><? echo $location_library[$dev_location];?></p></td>
                                  <?
								  }
								  ?>

								  <td><p><? echo $floorArr[$row['delivery_floor_id']];?></p></td>
								  <td><p><? echo $buyer_arr[$buyer_name];?></p></td>
								  <td align="right"><? echo $row['po_quantity'];?></td>
								  <td align="right"><? echo $row['ex_factory_qnty'];?></td>
								  <td align="right"><? $ex_amount=$row['unit_price']*$row['ex_factory_qnty'];echo fn_number_format($ex_amount);?></td>
								  <td align="right"><? echo $invoice_arr[$dev_comp][$dev_location][$buyer_name]['current_invoice_qnty'];?></td>
								  <td align="right"><? $inv_amount=$invoice_arr[$dev_comp][$dev_location][$buyer_name]['current_invoice_rate']*$invoice_arr[$dev_comp][$dev_location][$buyer_name]['current_invoice_qnty'];  echo fn_number_format($inv_amount);?></td>
								  <td align="right"><? $qty_variance=$row['ex_factory_qnty']-$invoice_arr[$dev_comp][$dev_location][$buyer_name]['current_invoice_qnty']; echo fn_number_format($qty_variance);?></td>
								  <td align="right"><? $amt_variance=$ex_amount-$inv_amount; echo fn_number_format($amt_variance);?></td>
								  </tr>
								<?
								$i++;
								$l++;
								$comp_po_qty+=$row['po_quantity'];
								$comp_ex_fac_qty+=$row['ex_factory_qnty'];
								$comp_ex_amt+=$ex_amount;
								$comp_ex_invoice_qty+=$invoice_arr[$dev_comp][$dev_location][$buyer_name]['current_invoice_qnty'];
								$comp_inv_amt+=$inv_amount;
								$comp_inv_variance+=$qty_variance;
								$comp_amt_variance+=$amt_variance;


							}

						}
						?>
						<tr style="text-align: right;font-weight:bold;background:#cddcdc">
						<th colspan="5">Ex-Factory Company Wise Total:</th>
						<th align="right"><? echo fn_number_format($comp_po_qty);?></th>
						<th align="right"><? echo fn_number_format($comp_ex_fac_qty);?></th>
						<th align="right"><? echo fn_number_format($comp_ex_amt);?></th>
						<th align="right"><? echo fn_number_format($comp_ex_invoice_qty);?></th>
						<th align="right"><? echo fn_number_format($comp_inv_amt);?></th>
						<th align="right"><? echo fn_number_format($comp_inv_variance);?></th>
						<th align="right"><? echo fn_number_format($comp_amt_variance);?></th>
					    </tr>

                     <?
					}
                    ?>
			  </tbody>


			</table>
		</div>

		<br><br>
		<div style="width:1840px">
		    <table class="rpt_table" width="1820" cellpadding="0" cellspacing="0" border="1" rules="all" >
			<heading><b>Details Part</b></heading>
			<thead>
				  <th width="20">SL</th>
				  <th width="100">Company</th>
				  <th width="100">Ex-Factory Company</th>
				  <th width="100">Ex-Factory Location</th>
				  <th width="100">Ex-Factory Floor</th>
				  <th width="100">Buyer</th>
				  <th width="100">Job</th>
				  <th width="100">Style</th>
				  <th width="100">PO No</th>
				  <th width="100">Ex-Factory Challan No</th>
				  <th width="100">Invoice No</th>
				  <th width="100">PO .Qty (Pcs)</th>
				  <th width="100">PO Ship Date</th>
				  <th width="100">Ex Factory Qty (Pcs)</th>
				  <th width="100">Ex.Amount</th>
				  <th width="100">Invoice Qty (Pcs)</th>
				  <th width="100">Inv.Amount</th>
				  <th width="100">Qty Variance</th>
				  <th width="100">Amt Variance</th>
			</thead>
			<tbody id="table_body_id">
				<?
				  $i=1;
                  foreach($details_arr as $comp_id=>$comp_val)
				  {
					 foreach($comp_val as $dev_comp_id=>$dev_comp_val)
					 {
						foreach($dev_comp_val as $dev_location=>$dev_loca_val)
						{
							foreach($dev_loca_val as $dev_floor=>$dev_floor_val)
							{
                                foreach($dev_floor_val as $job_id=>$job_val)
								{
									foreach($job_val as $po_id=>$po_val)
									{
										foreach($po_val as $style_ref=>$style_val)
										{
											foreach($style_val as $buyer_name=>$row)
											{

												if ($i%2==0)
												$bgcolor="#E9F3FF";
												else
												$bgcolor="#FFFFFF";

												$sys_number= implode(",",array_unique(array_filter(explode(",", $row['sys_number']))));
												$invoice_no = implode(",",array_unique(array_filter(explode(",", $row['invoice_no']))));
												?>
												<tr>
												<td><? echo $i;?></td>
												<td><p><? echo $company_library[$comp_id];?></p></td>
												<td><p><? echo $company_library[$dev_comp_id];?></p></td>
												<td><p><? echo $location_library[$dev_location];?></p></td>
												<td><p><? echo $floorArr[$dev_floor];?></p></td>
												<td><p><? echo $buyer_arr[$buyer_name];?></p></td>
												<td><p><? echo $job_id;?></p></td>
												<td><p><? echo $style_ref;?></p></td>
												<td><p><? echo $po_id;?></p></td>
												<td><p><? echo $sys_number;?></p></td>
												<td><p><? echo $invoice_no;?></p></td>
												<td align="right"><? echo $row["po_quantity"];?></td>
												<td align="right"><? echo $row["pub_shipment_date"];?></td>
												<td align="right"><? echo $row["ex_factory_qnty"];?></td>
												<td align="right"><? $ex_amount=$row['unit_price']* $row["ex_factory_qnty"]; echo fn_number_format($ex_amount);?></td>
												<td align="right"><?  echo $details_invoice_arr[$comp_id][$dev_comp_id][$dev_location][$dev_floor][$buyer_name]["current_invoice_qnty"];?></td>
												<td align="right"><? $inv_amount=$details_invoice_arr[$comp_id][$dev_comp_id][$dev_location][$dev_floor][$buyer_name]["current_invoice_qnty"]*$details_invoice_arr[$comp_id][$dev_comp_id][$dev_location][$dev_floor][$buyer_name]["current_invoice_rate"]; echo fn_number_format($inv_amount); ?></td>

												<td align="right"><? $qty_variance= $row["ex_factory_qnty"]-$details_invoice_arr[$comp_id][$dev_comp_id][$dev_location][$dev_floor][$buyer_name]["current_invoice_qnty"]; echo fn_number_format($qty_variance);  ?></td>

												<td align="right"><? $amt_variance=$ex_amount-$inv_amount ; echo fn_number_format($amt_variance);  ?></td>
												</tr>

												<?
												$i++;
												$details_po_quantity+= $row["po_quantity"];
												$details_ex_quantity+= $row["ex_factory_qnty"];
												$details_ex_amount+= $ex_amount;
												$details_invoice_qnty+=$details_invoice_arr[$comp_id][$dev_comp_id][$dev_location][$dev_floor][$buyer_name]["current_invoice_qnty"];
												$details_inv_amount+=$inv_amount;
												$details_qty_variance+=$qty_variance;
												$details_amt_variance+=$amt_variance;




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
			<tfoot>
				   <tr>
					   <th colspan="11">Ex-Factory Company Wise Total</th>
					   <th><? ?></th>
					   <th></th>
					   <th><? echo fn_number_format($details_ex_quantity);?></th>
					   <th><? echo fn_number_format($details_ex_amount);?></th>
					   <th><? echo fn_number_format($details_invoice_qnty);?></th>
					   <th><? echo fn_number_format($details_inv_amount);?></th>
					   <th><? echo fn_number_format($details_qty_variance);?></th>
					   <th><? echo fn_number_format($details_amt_variance);?></th>
				   </tr>
			</tfoot>
		</table>
		</div>



















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

if($action=="report_generate_excel")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$reportType=str_replace("'","",$reportType);
	$cbo_delivery_company_name=str_replace("'","",$cbo_delivery_company_name);
	$cbo_location_name=str_replace("'","",$cbo_location_name);
	$cbo_delivery_floor=str_replace("'","",$cbo_del_floor);
	$internal_ref=str_replace("'","",$txt_internal_ref);
	$cbo_shipping_status=str_replace("'","",$cbo_shipping_status);
	$cbo_source=str_replace("'","",$cbo_source);
	$cbo_brand_id=str_replace("'","",$cbo_brand_id);
	$cbo_season_name=str_replace("'","",$cbo_season_name);
	$cbo_season_year=str_replace("'","",$cbo_season_year);
	$_SESSION["source"]="";
	$_SESSION["source"]=$cbo_source;
	$buyer_cond = '';


	$source_cond="";
	if($cbo_source)$source_cond=" and d.source='$cbo_source'";
	$shiping_status_cond=($cbo_shipping_status>0)? " and a.shiping_status= $cbo_shipping_status " : " ";

	if($txt_date_from!="" && $txt_date_to!="")
	{
		$po_date_cond="and b.po_received_date between '$txt_date_from' and  '$txt_date_to' ";
		$str_cond="and a.ex_factory_date between '$txt_date_from' and  '$txt_date_to' ";
		$str_cond_sub=" and a.delivery_date between '$txt_date_from' and  '$txt_date_to' ";
	}
	else
	{
		$str_cond="";
		$po_date_cond="";
	}

	if($cbo_delivery_company_name)
	{
		 $del_comp_cond="and d.delivery_company_id in( $cbo_delivery_company_name) ";
		 $str_cond_sub.=" and a.company_id in( $cbo_delivery_company_name) ";
		 $str_cond_sub_total.=" and a.company_id in( $cbo_delivery_company_name) ";
	}
	else $del_comp_cond="";

	if($cbo_location_name)
	{
		 $str_cond_sub.="and a.location_id='$cbo_location_name' ";
		 $str_cond_sub_total.="and a.location_id='$cbo_location_name' ";
		 $del_location_cond="and d.delivery_location_id='$cbo_location_name'";
	}
	else $del_location_cond="";

	if($cbo_company_name)
	{
		 $company_cond=" and c.company_name like '$cbo_company_name' ";
		 $str_cond_sub.=" and a.company_id in( $cbo_company_name) ";
		 $str_cond_sub_total.=" and a.company_id in( $cbo_company_name) ";
	}

	if($cbo_buyer_name) {
		$buyer_cond=" and c.buyer_name = $cbo_buyer_name";
	}
	// else $company_cond="";

	if($cbo_delivery_floor) $del_floor_cond="and d.delivery_floor_id='$cbo_delivery_floor' "; else $del_floor_cond="";
	if($internal_ref !="") $internal_ref_cond="and b.grouping='$internal_ref'"; else $internal_ref_cond="";
	$search_cond='';
	if($cbo_brand_id !=0) $search_cond.="and c.brand_id='$cbo_brand_id'";
	if($cbo_season_name !=0) $search_cond.="and c.season_buyer_wise='$cbo_season_name'";
	if($cbo_season_year !=0) $search_cond.="and c.season_year='$cbo_season_year'";


	if(str_replace("'","", $cbo_buyer_name))
	{
		$str_cond_sub.=" and b.party_id in( ".str_replace("'", "",  $cbo_buyer_name).") ";
		$str_cond_sub_total.=" and b.party_id in( ".str_replace("'", "",  $cbo_buyer_name).") ";
		$buyer_conds.=" and c.buyer_name = ".str_replace("'", "",  $cbo_buyer_name) ;
		$buyer_conds2.=" and b.buyer_id = ".str_replace("'", "",  $cbo_buyer_name) ;
		$po_buyer_cond.=" and a.buyer_name = ".str_replace("'", "",  $cbo_buyer_name) ;
	}

	$details_report="";
	$master_data=array();
	$current_date=date("Y-m-d");
	$date=date("Y-m-d");$break_id=0;$sc_lc_id=0;
	$sy = date('Y',strtotime($txt_date_from));
	$basic_smv_arr=return_library_array( "select comapny_id, basic_smv from lib_capacity_calc_mst where year=$sy",'comapny_id','basic_smv');

	$tot_cost_arr = return_library_array("select job_no, cm_for_sipment_sche from wo_pre_cost_dtls","job_no","cm_for_sipment_sche");
	$costing_per_arr = return_library_array("select job_no, costing_per from wo_pre_cost_mst","job_no","costing_per");

	if($reportType==1)//Details Button
	{
		$tot_cost_arr = return_library_array("select job_no, cm_cost from wo_pre_cost_dtls","job_no","cm_cost");
		$inspection_date_arr=return_library_array( "select po_break_down_id, max(inspection_date) as inspection_date from pro_buyer_inspection where status_active=1 and is_deleted=0 group by po_break_down_id", "po_break_down_id", "inspection_date");
		$invoice_array=return_library_array( "select id,invoice_no from com_export_invoice_ship_mst", "id", "invoice_no"  );
		$shipping_mode_array=return_library_array( "select id,shipping_mode from com_export_invoice_ship_mst", "id", "shipping_mode"  );
		$lc_sc_type_arr=return_library_array( "select id,is_lc from com_export_invoice_ship_mst", "id", "is_lc"  );
		$lc_sc_id_array=return_library_array( "select id,lc_sc_id from com_export_invoice_ship_mst", "id", "lc_sc_id"  );
		$lc_num_arr=return_library_array( "select id,export_lc_no from com_export_lc", "id", "export_lc_no"  );
		$sc_num_arr=return_library_array( "select id,contract_no from com_sales_contract", "id", "contract_no"  );

		$forwarder_arr=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name");


		if($source_cond)
		$source_cond2=str_replace("d.","a.",$source_cond);
		$challan_mst_arr=array();
		$challan_sql="SELECT a.id,b.delivery_mst_id, a.sys_number_prefix_num,a.sys_number, a.forwarder, a.truck_no, a.driver_name, a.mobile_no, a.dl_no, b.po_break_down_id,b.item_number_id,a.delivery_floor_id,
		(CASE WHEN a.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty,
		b.total_carton_qnty as carton_qnty
		from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b
		where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $source_cond2";
		// echo $challan_sql;die;
		$challan_sql_result=sql_select($challan_sql);
		$gr_ttl_sales_cm_cost = 0;
		$total_salesCM = 0;
		$job_check_array = array();
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
			$exfact_return_qty_arr[$row[csf("po_break_down_id")]][$row[csf("delivery_mst_id")]]=$row[csf("ex_factory_return_qnty")];
			$exfact_qty_arr_without_current[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]]["ex_fact"]+=$row[csf("ex_factory_qnty")]-$row[csf("ex_factory_return_qnty")];
			$exfact_qty_arr_without_current[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]]["carton"]+=$row[csf("carton_qnty")];
		}
		$details_report .='<table>';
		$i=1;

		if($db_type==0)
		{
			$sql= "SELECT b.id as po_id, max(a.lc_sc_no) as lc_sc_arr_no, group_concat(distinct a.invoice_no) as invoice_no, group_concat(distinct a.item_number_id) as itm_num_id, group_concat(distinct a.foc_or_claim) as foc_or_claim,
			sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
			sum(CASE WHEN a.entry_form=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty,
			 sum(a.total_carton_qnty) as carton_qnty, max(a.ex_factory_date)  as ex_factory_date, group_concat(distinct  a.lc_sc_no) as lc_sc_no, max(a.shiping_mode) as shiping_mode, a.delivery_mst_id as challan_id, d.delivery_floor_id as del_floor,  b.shipment_date, b.po_number, b.po_quantity as po_quantity, (b.unit_price/c.total_set_qnty) as unit_price, b.up_charge, b.shiping_status, c.id, c.company_name, c.buyer_name, c.client_id, c.job_no_prefix_num,c.job_no,c.ship_mode , YEAR(c.insert_date) as year, c.style_ref_no, c.style_description, c.set_smv, d.delivery_company_id as del_company,d.source,d.delivery_location_id as del_location ,c.total_set_qnty,a.item_number_id,c.set_break_down,c.order_uom,c.DESIGN_SOURCE_ID
			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c ,pro_ex_factory_delivery_mst d
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no $company_cond $str_cond $del_comp_cond $del_location_cond $del_floor_cond $shiping_status_cond $source_cond $buyer_conds $internal_ref_cond $search_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active  in(1,2,3) and c.is_deleted=0 and c.status_active  in(1,2,3) and a.delivery_mst_id=d.id
			group by
					b.id, a.delivery_mst_id, d.delivery_floor_id, b.shipment_date, b.po_number, b.po_quantity, c.total_set_qnty, b.unit_price, b.up_charge, b.shiping_status, c.id, c.company_name, c.buyer_name, c.client_id, c.job_no_prefix_num, c.job_no, c.ship_mode, c.insert_date, c.style_ref_no, c.style_description, c.set_smv, d.delivery_company_id, d.delivery_location_id, d.source, a.item_number_id, c.set_break_down, c.order_uom,c.DESIGN_SOURCE_ID
			order by c.buyer_name, b.shipment_date ASC";
		}
		else if($db_type==2)
		{
		 	$sql= "SELECT b.id as po_id, max(a.lc_sc_no) as lc_sc_arr_no, LISTAGG(CAST( a.invoice_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.invoice_no) as invoice_no, LISTAGG(CAST( a.foc_or_claim AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.foc_or_claim) as foc_or_claim, LISTAGG(CAST( a.item_number_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.item_number_id) as itm_num_id,
			sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
			sum(CASE WHEN a.entry_form=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty,
			sum(a.total_carton_qnty) as carton_qnty, max(a.ex_factory_date) as ex_factory_date, LISTAGG(CAST( a.lc_sc_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.lc_sc_no) as lc_sc_no, max(a.shiping_mode) as shiping_mode, a.delivery_mst_id as challan_id, d.delivery_floor_id as del_floor, b.shipment_date, b.po_number, b.po_quantity as po_quantity, (b.unit_price/c.total_set_qnty) as unit_price, b.up_charge, b.shiping_status, c.id, c.company_name, c.buyer_name, c.client_id, c.job_no_prefix_num, c.job_no, c.ship_mode, to_char(c.insert_date,'YYYY') as year, c.style_ref_no, c.style_description, c.set_smv, d.delivery_company_id as del_company, d.source, d.delivery_location_id as del_location, c.total_set_qnty, a.item_number_id, c.set_break_down, c.order_uom,c.DESIGN_SOURCE_ID, c.TOTAL_PRICE, c.JOB_QUANTITY
			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c ,pro_ex_factory_delivery_mst d
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no $company_cond $str_cond $del_comp_cond $del_location_cond $del_floor_cond $shiping_status_cond $source_cond $buyer_conds $internal_ref_cond $search_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,2,3) and c.is_deleted=0 and c.status_active in(1,2,3) and a.delivery_mst_id=d.id
			group by
					b.id, a.delivery_mst_id, d.delivery_floor_id, b.shipment_date, b.po_number, b.po_quantity, c.total_set_qnty, b.unit_price, b.up_charge, b.shiping_status, c.id, c.company_name, c.buyer_name, c.client_id, c.job_no_prefix_num, c.job_no, c.ship_mode, c.insert_date, c.style_ref_no, c.style_description, c.set_smv, d.delivery_company_id, d.source, d.delivery_location_id, a.item_number_id, c.set_break_down, c.order_uom,c.DESIGN_SOURCE_ID, c.TOTAL_PRICE, c.JOB_QUANTITY
			order by c.buyer_name, b.shipment_date ASC";
		}
		// echo $sql;die();
		$sql_result=sql_select($sql);
		$poExQtyArray = array();
		$poChkArray = array();
		foreach($sql_result as $k=>$v)
		{
			$all_job_arr[trim($v[csf("job_no")])] = trim($v[csf("job_no")]);
			if(!in_array($v[csf("po_id")], $poChkArray))
			{
				$poExQtyArray[$v[csf("po_id")]]['poQty'] += $v[csf("po_quantity")];
				$poChkArray[$v[csf("po_id")]] = $v[csf("po_id")];
			}
			$poExQtyArray[$v[csf("po_id")]]['exQty'] += $v[csf("ex_factory_qnty")];
		}
		// echo "<pre>";print_r($poExQtyArray);die();
		$all_job="'".implode("','", array_unique($all_job_arr))."'";
		$order_item_qnty_sql="SELECT po_break_down_id,item_number_id,sum(order_quantity) as order_quantity from wo_po_color_size_breakdown where status_active  in(1,2,3) and is_deleted=0 and job_no_mst in($all_job) group by po_break_down_id,item_number_id";
		foreach(sql_select($order_item_qnty_sql) as $key=>$val)
		{
			$order_item_qnty_arr[$val[csf("po_break_down_id")]][$val[csf("item_number_id")]]=$val[csf("order_quantity")];
		}
		$gr_po_qnty_pcs=0; $gr_po_qnty_val=0; $gr_po_qnty_val_perc=0; $gr_ttl_ex_qnty=0; $gr_ttl_ex_qnty_val=0; $gr_sales_min=0; $gr_ttl_carton=0; $gr_ttl_basic_qty=0; $gr_ttl_ex_fac_per=0; $gr_ttl_short_qty=0; $gr_ttl_short_val=0; $gr_ttl_sales_cm=0;

		//$po_exist_arr=array();
		$po_wise_ttl_ex_qty = array();
		$po_wise_ttl_up_charge = array();

		//Calculating job wise expenses
		$condition= new condition();
		if(str_replace("'","",$all_job) !=''){
			$condition->job_no("in($all_job)");
		   }
		$condition->init();
		$commission= new commision($condition);
		$commission_costing_arr=$commission->getAmountArray_by_job();
		$commercial= new commercial($condition);
		$commercial_costing_arr=$commercial->getAmountArray_by_job();
		$other= new other($condition);
		$other_costing_arr=$other->getAmountArray_by_job();
		$fabric	= new fabric($condition);
		$fabric_costing_arr2 = $fabric->getAmountArray_by_job_knitAndwoven_greyAndfinish();
		$trims= new trims($condition);
		$trims_costing_arr=$trims->getAmountArray_by_job();

		$emblishment= new emblishment($condition);
		$emblishment_costing_arr=$emblishment->getAmountArray_by_job();
		$wash= new wash($condition);
		$emblishment_costing_arr_wash=$wash->getAmountArray_by_job();
		$yarn= new yarn($condition);
		$yarn_costing_arr=$yarn->getJobWiseYarnAmountArray();
		$conversion= new conversion($condition);
		$conversion_costing_arr_process=$conversion->getAmountArray_by_job();

		foreach($sql_result as $row)
		{
			$inv_mst_id .= $row[csf('invoice_no')].',';
		}
		$all_inv_mst_id = ltrim(implode(",", array_unique(explode(",", chop($inv_mst_id, ",")))), ',');

		$inv_sql = "SELECT ID,INVOICE_NO,INVOICE_QUANTITY,NET_INVO_VALUE FROM COM_EXPORT_INVOICE_SHIP_MST WHERE id in ($all_inv_mst_id) and status_active =1 and is_deleted =0";
		//echo $inv_sql; die();
		$inv_sql_result = sql_select($inv_sql);
		$inv_arr = array();
		foreach($inv_sql_result as $row)
		{
			$inv_arr[$row['ID']]['INVOICE_QUANTITY'] = $row['INVOICE_QUANTITY'];
			$inv_arr[$row['ID']]['NET_INVO_VALUE'] = $row['NET_INVO_VALUE'];
		}

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

			foreach($challan_id as $val)
			{
				if($challan_no=="") $challan_no=$challan_mst_arr[$val][$row[csf('po_id')]]['challan']; else $challan_no.=','.$challan_mst_arr[$val][$row[csf('po_id')]]['challan'];
				if($floor_no=="") $floor_no=$floor_library[$challan_mst_arr[$val][$row[csf('po_id')]]['floor']]; else $floor_no.=','.$floor_library[$challan_mst_arr[$val][$row[csf('po_id')]]['floor']];

				if($forwarder=="") $forwarder=$forwarder_arr[$challan_mst_arr[$val][$row[csf('po_id')]]['forwarder']]; else $forwarder.=','.$forwarder_arr[$challan_mst_arr[$val][$row[csf('po_id')]]['forwarder']];
				if($vehi_no=="") $vehi_no=$challan_mst_arr[$val][$row[csf('po_id')]]['truck_no']; else $vehi_no.=','.$challan_mst_arr[$val][$row[csf('po_id')]]['truck_no'];
				if($dirver_info=="") $dirver_info="Name: ".$challan_mst_arr[$val][$row[csf('po_id')]]['driver_name']."<br>Mob No: ".$challan_mst_arr[$val][$row[csf('po_id')]]['mobile_no']."<br>DL No: ".$challan_mst_arr[$val][$row[csf('po_id')]]['dl_no']; else $dirver_info.=','."Name: ".$challan_mst_arr[$val][$row[csf('po_id')]]['driver_name']."<br>Mob No: ".$challan_mst_arr[$val][$row[csf('po_id')]]['mobile_no']."<br>DL No: ".$challan_mst_arr[$val][$row[csf('po_id')]]['dl_no'];
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

			//calculating sales CM
			//salesCM = (orderValue - (LessCommission + LessCommercialCost + LessFreight + Other Cost))/jobQTY * 12;
			//otherCost = totalFabricCostKnitWoven + trimsCost + EmbellishmentCost + LabTestCost + InspectionCost +  OfficeOHCost

			$otherCost = array_sum($fabric_costing_arr2['knit']['grey'][$row['JOB_NO']]) + array_sum($fabric_costing_arr2['woven']['grey'][$row['JOB_NO']]) + $yarn_costing_arr[$row['JOB_NO']] + array_sum($conversion_costing_arr_process[$row['JOB_NO']]) + $trims_costing_arr[$row['JOB_NO']] + $emblishment_costing_arr[$row['JOB_NO']] + $emblishment_costing_arr_wash[$row['JOB_NO']] + $other_costing_arr[$row['JOB_NO']]['lab_test']+ $other_costing_arr[$row['JOB_NO']]['cm_cost'] + $other_costing_arr[$row['JOB_NO']]['currier_pre_cost'] + $other_costing_arr[$row['JOB_NO']]['inspection'] + $other_costing_arr[$row['JOB_NO']]['common_oh'] + $other_costing_arr[$row['JOB_NO']]['certificate_pre_cost'] + $other_costing_arr[$row['JOB_NO']]['depr_amor_pre_cost']  + $other_costing_arr[$row['JOB_NO']]['interest_cost']   + $other_costing_arr[$row['JOB_NO']]['incometax_cost']  + $other_costing_arr[$row['JOB_NO']]['deffdlc_cost']  + $other_costing_arr[$row['JOB_NO']]['incentives_cost'];

			$salesCM = (($row['TOTAL_PRICE'] - ($commission_costing_arr[$row['JOB_NO']] + $commercial_costing_arr[$row['JOB_NO']] + $other_costing_arr[$row['JOB_NO']]['freight'] + $otherCost))/$row['JOB_QUANTITY']) * 12;

			if($job_check_array[$row['JOB_NO']] == ""){
				$job_check_array[$row['JOB_NO']] = $row['JOB_NO'];
				$total_salesCM +=$salesCM;
			}


			$current_ex_up_charge = 0;
			$current_ex_up_charge_value = 0;
			$qt = 0;

			$total_ex_up_charge = 0;
			$total_ex_up_charge_value = 0;
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$comapny_id=$row[csf("company_name")];
			$delv_comp=($row[csf("source")]!=3)?  $company_library[$row[csf("del_company")]] : $supp_library[$row[csf("del_company")]];

			$onclick=" change_color('tr_".$i."','".$bgcolor."')";
			$details_report .='<tr bgcolor="'.$bgcolor.'" onclick="'.$onclick.'" id="tr_'.$i.'">';
			$details_report .='<td >'.$i.'</td>
								<td>'.$company_library[$row[csf("company_name")]].'</td>
								<td>'.$row[csf("job_no_prefix_num")].'</td>
								<td>'.$row[csf("year")].'</td>
								<td>'.$buyer_arr[$row[csf("buyer_name")]].'</td>
								<td>'.$buyer_arr[$row[csf("client_id")]].'</td>
								<td>'.$row[csf("po_number")].'</td>
								<td>'.$delv_comp.'</td>
								<td>'.$location_library[$row[csf("del_location")]].'</td>
								<td>'.$floor_no.'</td>
								<td>'.$challan_no.'</td>
								<td';
								$inv_id=""; $lc_sc_no=""; $ship_mode="";  $inv_qty=""; $inv_val="";
								foreach($invoce_id_arr as $invoice_id)
								{
									if($inv_id=="") $inv_id=$invoice_array[$invoice_id]; else $inv_id=$inv_id.",".$invoice_array[$invoice_id];
									if($ship_mode=="") $ship_mode=$shipment_mode[$shipping_mode_array[$invoice_id]]; else $ship_mode=$ship_mode.",".$shipment_mode[$shipping_mode_array[$invoice_id]];
									if($lc_sc_type_arr[$invoice_id]==1)
									{
										if($lc_sc_no=="") $lc_sc_no=$lc_num_arr[$lc_sc_id_array[$invoice_id]]; else $lc_sc_no.=",".$lc_num_arr[$lc_sc_id_array[$invoice_id]];
									}
									else
									{
										if($lc_sc_no=="") $lc_sc_no=$sc_num_arr[$lc_sc_id_array[$invoice_id]]; else $lc_sc_no.=",".$sc_num_arr[$lc_sc_id_array[$invoice_id]];
									}
									$inv_qty = $inv_arr[$invoice_id]['INVOICE_QUANTITY'];
									$inv_val = $inv_arr[$invoice_id]['NET_INVO_VALUE'];

								}

			$details_report .=$inv_id.'</td>

								<td>'.$inv_qty.'</td>
								<td>'.number_format($inv_val,2).'</td>
								<td>'.$lc_sc_no.'</td>
								<td>'.$design_source_arr[$row[csf("DESIGN_SOURCE_ID")]].'</td>
								<td>'.$row[csf("style_ref_no")].'</td>
								<td>'.$row[csf("style_description")].'</td>
								<td>';
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
								//
								$po_quantity=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]];
								$unit_price=$row[csf("unit_price")];
								//$current_ex_Fact_Qty=$row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]][$row[csf("challan_id")]];

								$current_ex_Fact_Qty=$row[csf("ex_factory_qnty")]-$row[csf("ex_factory_return_qnty")];
								$basic_qnty=($total_ex_fact_qty*$item_smv)/$basic_smv_arr[$row[csf("company_name")]];
								$cm_per_pcs_tot+=($cm_per_pcs*$current_ex_Fact_Qty);
								$short_excess=$total_ex_fact_qty-$po_quantity;

								$excess_msg=($short_excess>0) ?" color:green;":"color:black;";
								$excess_val_msg=(($total_ex_fact_qty*$unit_price)-$po_quantity>0) ?" color:green;":"color:black;";
								$ttl_ex_qty=($total_ex_fact_qty/$po_quantity)*100;
								$ttl_ex_qty_msg=($ttl_ex_qty>100) ?" color:green;":"color:black;";

								// ============================ calculate upcharge ===============================
								$po_wise_ttl_ex_qty[$row[csf("po_id")]] += $current_ex_Fact_Qty;
								// echo $poExQtyArray[$row[csf("po_id")]]['poQty'] ."<=". $po_wise_ttl_ex_qty[$row[csf("po_id")]]."<br>";

								if($poExQtyArray[$row[csf("po_id")]]['poQty'] >= $po_wise_ttl_ex_qty[$row[csf("po_id")]])
								{
									$current_ex_up_charge = ($row[csf('up_charge')]/$po_quantity)*$current_ex_Fact_Qty;
									$current_ex_up_charge_value = $current_ex_up_charge + ($current_ex_Fact_Qty*$unit_price);
									$qt=$row[csf("carton_qnty")];
									$po_wise_ttl_up_charge[$row[csf("po_id")]] += $current_ex_up_charge;

									// echo $current_ex_up_charge ."+((".$current_ex_Fact_Qty."-".$excessExQty.")*".$unit_price.")<br>";

									if($total_ex_fact_qty>$poExQtyArray[$row[csf("po_id")]]['poQty'])
									{
										$total_ex_up_charge = ($row[csf('up_charge')]/$po_quantity)*$poExQtyArray[$row[csf("po_id")]]['poQty'];
										$total_ex_up_charge_value = $total_ex_up_charge + ($total_ex_fact_qty*$unit_price);
									}
									else
									{
										$total_ex_up_charge = ($row[csf('up_charge')]/$po_quantity)*$total_ex_fact_qty;
										$total_ex_up_charge_value = $total_ex_up_charge + ($total_ex_fact_qty*$unit_price);
									}

									$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['current_ex_up_charge_value'] += $current_ex_up_charge_value;
								}
								else
								{
									$excessExQty = $po_wise_ttl_ex_qty[$row[csf("po_id")]] - $poExQtyArray[$row[csf("po_id")]]['poQty'];
									$bal_qty = $current_ex_Fact_Qty-$excessExQty;
									if($bal_qty<1) $bal_qty=0;
									$current_ex_up_charge = ($row[csf('up_charge')]/$po_quantity)*$bal_qty;
									// echo $excess_ex_up_charge = ($row[csf('up_charge')]/$po_quantity)*$excessExQty;


									$current_ex_up_charge_value = $current_ex_up_charge + ($current_ex_Fact_Qty*$unit_price);
									$qt = $row[csf("carton_qnty")] ;
									if($bal_qty<1) $bal_qty=1;
									$current_ex_up_charge = ($row[csf('up_charge')]/$po_quantity)*$bal_qty;

									// echo $po_wise_ttl_up_charge[$row[csf("po_id")]];

									// echo $current_ex_up_charge ."+".$current_ex_Fact_Qty."*".$unit_price."<br>";

									$total_ex_up_charge = $row[csf('up_charge')];
									// $total_ex_up_charge = ($row[csf('up_charge')]/$po_quantity)*($total_ex_fact_qty-$excessExQty);
									// echo "(".$row[csf('up_charge')]."/".$po_quantity.")*(".$total_ex_fact_qty."-".$excessExQty.")<br>";
									$total_ex_up_charge_value = $total_ex_up_charge + ($total_ex_fact_qty*$unit_price);
									// echo $total_ex_up_charge ."+".$total_ex_fact_qty."*".$unit_price."<br>";

									$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['current_ex_up_charge_value'] += $current_ex_up_charge_value;
								}

			$total_sales_minutes=($current_ex_Fact_Qty*$item_smv);
			$gr_sales_min+=$total_sales_minutes;

								$temp_focclaim_arr=array();
								foreach(explode(',',$row[csf("foc_or_claim")]) as $foc_id){
									$temp_focclaim_arr[$foc_id]=$foc_claim_arr[$foc_id];
								}
								$row[csf("foc_or_claim")]=implode(',',$temp_focclaim_arr);



			$details_report .=$item_name_all.'</td>
								<td>'.number_format($item_smv,2, '.', '').'</td>
								<td'.change_date_format($row[csf("shipment_date")]).'</td>
								<td>'.change_date_format($row[csf('ex_factory_date')]).'</td>
								<td>'.$shipment_mode[$row[csf('ship_mode')]].'</td>
								<td >'.$shipment_mode[$row[csf("shiping_mode")]].'</td>
								<td>'.$row[csf("foc_or_claim")].'</td>

								<td>('.$diff.')</td>
								<td>'.$unit_of_measurement[$row[csf('order_uom')]].'</td>
								<td>'. number_format($po_quantity,0,'', '').'</td>
								<td>'. number_format($unit_price,4, '.', '').'</td>
								<td>'. number_format(($po_quantity*$unit_price),2, '.', '').'</td>
								<td>'.number_format($row[csf("up_charge")],2,'.', '').'</td>
								<td>'. number_format($current_ex_Fact_Qty,0,'.', '').'</td>
								<td>'. number_format( $current_ex_fact_value=$current_ex_Fact_Qty*$unit_price,2, '.', '').'</td>

								<td>'.number_format($current_ex_up_charge,2, '.', '').'</td>
								<td>'.number_format($current_ex_up_charge_value,2, '.', '').'</td>

								<td>'. number_format($row[csf("carton_qnty")],0,'.', '').'</td>
								<td>'.number_format($total_ex_fact_qty,0,'.', '').'</td>
								<td>'. number_format($total_ex_fact_value=$total_ex_fact_qty*$unit_price,2, '.', '').'</td>

								<td>'.number_format($total_ex_up_charge,2, '.', '').'</td>
								<td>'.number_format($total_ex_up_charge_value,2, '.', '').'</td>

								<td>'.number_format($total_cartoon_qty,0,'.', '').'</td>
								<td>'. number_format($total_sales_minutes,0,'', '').'</td>
								<td>'.number_format($basic_qnty,0,'','').'</td>
								<td>'. number_format($excess_shortage_qty=$total_ex_fact_qty-$po_quantity,0,'', '').'</td>
								<td>'. number_format($excess_shortage_value=($excess_shortage_qty*$unit_price),2, '.', '').'</td>
								<td>'. number_format($total_ex_fact_qty_parcent=($total_ex_fact_qty/$po_quantity)*100,0, '.', '').'</td>
								<td>'.number_format($cm_per_pcs*$current_ex_Fact_Qty,2, '.', '').'</td>
								<td >'.number_format($salesCM,4, '.', '').'</td>
								<td>'.$forwarder.'</td>
								<td>'.$vehi_no.'</td>
								<td>'.$dirver_info.'</td>
								<td>'.(change_date_format($inspection_date_arr[$row[csf('po_id')]]) == '0000-00-00' || change_date_format($inspection_date_arr[$row[csf('po_id')]]) == '' ? '' : change_date_format(change_date_format($inspection_date_arr[$row[csf('po_id')]]))).'</td>
								<td>'.$shipment_status[$row[csf('shiping_status')]].'</td>
							</tr>';

 	  		$total_po_qty+=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]];
 	  		$total_po_valu+=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]]*$row[csf("unit_price")];


			$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['ex_factory_qnty'] +=$row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]][$row[csf("challan_id")]];

			$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['ex_factory_ret_qnty'] +=$row[csf("ex_factory_return_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]][$row[csf("challan_id")]];

			$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['ex_factory_value'] +=($row[csf("ex_factory_qnty")]-$row[csf("ex_factory_return_qnty")])*$row[csf("unit_price")];



			$total_basic_qty+=$basic_qnty;
			$total_inv_qty+=$inv_qty;
			$total_inv_val+=$inv_val;

			//$total_ex_qty+=$row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]][$row[csf("challan_id")]];
			$total_ex_qty+=$current_ex_Fact_Qty;
			$total_crtn_qty+=$row[csf("carton_qnty")];
			$total_ex_valu+=$current_ex_Fact_Qty*$row[csf("unit_price")];
			$g_total_ex_qty+=$total_ex_fact_qty;
			$g_total_ex_crtn+=$total_cartoon_qty;
			$g_total_ex_val+=$total_ex_fact_qty*$row[csf("unit_price")];
			$g_sales_minutes+=$total_sales_minutes;

			// $g_sales_minutes+=$current_ex_Fact_Qty*$item_smv;
			$total_eecess_storage_qty+=$excess_shortage_qty;
			$total_eecess_storage_val+=$excess_shortage_value;
			if($po_check_arr[$row[csf("po_id")]][$row[csf("item_number_id")]]=="")
			{
				$po_check_arr[$row[csf("po_id")]][$row[csf("item_number_id")]]=$row[csf("po_id")];
				$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['b_id']=$row[csf("buyer_name")];
				$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['org_po_qnty'] +=$row[csf("po_quantity")];
				$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['upchage'] +=$row[csf("up_charge")];
				$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['po_qnty'] +=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]];
				$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['po_value'] +=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]]*$row[csf("unit_price")];
				$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['basic_qnty'] +=$basic_qnty;
				$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['total_ex_fact_qty'] +=$total_ex_fact_qty;
				$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['total_ex_fact_value'] +=$total_ex_fact_qty*$row[csf("unit_price")];


				$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['current_ex_up_charge'] += $current_ex_up_charge;
				// $master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['current_ex_up_charge_value'] += $current_ex_up_charge_value;
				$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['total_ex_up_charge'] += $total_ex_up_charge;
				$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['total_ex_up_charge_value'] += $total_ex_up_charge_value;

 	  			$gr_po_qnty_pcs+=$po_quantity;
	  			$gr_po_qnty_val+=$po_quantity*$unit_price;
 	  			$gr_ttl_ex_qnty+=$total_ex_fact_qty;
	  			$gr_ttl_ex_qnty_val+=$total_ex_fact_value;
	  			$gr_ttl_carton_qt+=$total_cartoon_qty;
	  			//$gr_sales_min+=$total_sales_minutes;
	  			$gr_ttl_basic_qty+=$basic_qnty;
	  			$gr_ttl_ex_fac_per+=$total_ex_fact_qty_parcent;
	  			$gr_ttl_short_qty+=$excess_shortage_qty;
	  			$gr_ttl_short_val+=$excess_shortage_value;
	  			$gr_ttl_sales_cm+=$cm_per_pcs*$total_ex_fact_qty;

				$total_po_val+=$order_item_qnty_arr[$row[csf("po_id")]][$row[csf("item_number_id")]]*$row[csf("unit_price")];
				$gr_upcharge+=$row[csf("up_charge")];

				$gr_current_ex_up_charge += $current_ex_up_charge;
				$gr_current_ex_up_charge_value += $current_ex_up_charge_value;
                $total_qty += $row[csf("carton_qnty")] ;
				$gr_total_ex_up_charge += $total_ex_up_charge;
				$gr_total_ex_up_charge_value += $total_ex_up_charge_value;

			}

			$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['sales_min'] += $item_smv*($row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]][$row[csf("challan_id")]]);

			$master_data[$row[csf("buyer_name")]][$row[csf("client_id")]]['sales_cm'] += $cm_per_pcs*$current_ex_Fact_Qty;
			// echo $cm_per_pcs."*".$current_ex_Fact_Qty."<br>";
			$i++; $item_name_all="";
		}
		  $pp=$i;
         $html ='<div>
            <div>
                <table>
                    <tr>
                        <td >
                            Company Name:'.  $company_library[$cbo_company_name] .'</strong>
                        </td>
                    </tr>
                    <tr >
                        <td> Ex-Factory Report</strong></td>
                    </tr>
                     <tr>
                        <td>Total Summary</strong></td>
                    </tr>
                </table>
                <table>
                    <thead>
                        <th>SL</th>
                        <th>Buyer Name</th>
                        <th>Client</th>
                        <th>PO Qty</th>
                        <th>PO Qty (pcs)</th>
                        <th>PO Value</th>
                        <th>PO Value(%)</th>
                        <th>PO TTL Up-Charge</th>
                        <th>Current Ex-Fact. Qty.</th>
                        <th>Current Ex-Fact. Value</th>
                        <th>Up Charge with Current Ex-Fact. Value</th>
                        <th>Total Ex-Fact. Qty.</th>
                        <th>Total Ex-Fact. Value</th>
                        <th>Up Charge with Total Ex-Fact. Value</th>
                        <th>Sales Minutes</th>
                        <th>Sales CM Cost</th>
                        <th>Total Ex-Fact. (Basic Qty)</th>
                        <th>Total Ex-Fact. Value %</th>
                    </thead>
             </table>
             <div>
             <table>';
                $m=1; $grand_sales_minute =0;
                foreach($master_data as $buyid=>$buyData)
                {
					foreach($buyData as $clientid=>$cdata)
					{
						if ($m%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$buyer_po_quantity=0; $buyer_po_value=0; $current_ex_Fact_Qty=0; $current_ex_fact_value=0; $total_ex_fact_qty=0; $total_ex_fact_value=0; $g_sales_min=0;$g_sales_cm=0;

						$po_quantity=$cdata['po_qnty'];
						$buyer_po_value=$cdata["po_value"];
						$parcentages+=($buyer_po_value/$total_po_val)*100;
						$current_ex_Fact_Qty=$cdata['ex_factory_qnty'] - $cdata['ex_factory_ret_qnty'];
						$current_ex_fact_value=$cdata['ex_factory_value'];
						$total_ex_fact_qty=$cdata['total_ex_fact_qty'];
						$total_ex_fact_value=$cdata['total_ex_fact_value'];
						$buyer_basic_qnty=$cdata["basic_qnty"];
						$total_ex_fact_value_parcentage=($total_ex_fact_value/$buyer_po_value)*100;

						$html .= '<tr>
							<td >'.$m.'</td>
							<td >'.$buyer_arr[$buyid].'</td>
							<td >'.$buyer_arr[$clientid].'</td>
							<td>'.number_format($cdata["org_po_qnty"], 0, '.', '').'</td>
							<td>'.number_format($po_quantity, 0, '.', '').'</td>
							<td>'.number_format($buyer_po_value, 2, '.', '').'</p></td>
							<td>'.number_format(($buyer_po_value / $total_po_val) * 100, 2, '.', '').'</td>
                            <td>'.number_format($cdata["upchage"], 2, '.', '').'</td>
							<td>'.number_format($current_ex_Fact_Qty, 0, '', '').'</td>
							<td>'.number_format($current_ex_fact_value, 2, '.', '').'</td>
							<td>'.number_format($cdata['current_ex_up_charge_value'], 2, '.', '').'</td>
							<td>'.number_format($total_ex_fact_qty, 0, '', '').'</td>
							<td>'.number_format($total_ex_fact_value, 2, '.', '').' </td>
							<td>'.number_format($cdata['total_ex_up_charge_value'], 2, '.', '').' </td>
							<td>'. $g_sales_min+= number_format($cdata["sales_min"],0, '.', '').'</td>
							<td>'. $g_sales_cm+= number_format($cdata["sales_cm"],0, '.', '').'</td>
							<td>'.number_format($buyer_basic_qnty, 0, '', '').'</td>
							<td>'.number_format($total_ex_fact_value_parcentage, 0, '.', '').' %</td>
						</tr>';

						$i++; $m++;

						$grand_sales_minute +=number_format($cdata["sales_min"],0, '.', '');
						$grand_sales_cm +=number_format($cdata["sales_cm"],0, '.', '');
						$total_buyer_org_po_quantity+=$cdata["org_po_qnty"];
						$total_buyer_po_quantity+=$po_quantity;
						$total_buyer_po_value+=$buyer_po_value;
						$total_current_ex_Fact_Qty +=$current_ex_Fact_Qty;
						$total_current_ex_fact_value+=$current_ex_fact_value;
						$mt_total_ex_fact_qty+=$total_ex_fact_qty;
						$mt_total_ex_fact_value+=$total_ex_fact_value;
						$total_buyer_basic_qnty +=$buyer_basic_qnty;
						$buyerTotUpCharge +=$cdata["upchage"];

					}
                }

               $html .= '</table>';
               $html .= '  </div>';
			   $html .= ' <table>
                    <thead>
                    	<td></td>
                        <td>Total:</td>
                        <td></td>
                        <td >'.number_format($total_buyer_org_po_quantity, 0, '.', '').'</td>
                        <td>'.number_format($total_buyer_po_quantity, 0, '.', '').'</td>
                        <td>'.number_format($total_buyer_po_value, 2, '.', '').'</td>
                        <td>'.ceil($parcentages).'</td>
                        <td>'.number_format($buyerTotUpCharge, 2, '.', '').'</td>
                        <td>'.number_format($total_current_ex_Fact_Qty, 0, '.', '').'</td>
                        <td>'.number_format($total_current_ex_fact_value, 2, '.', '').'</td>
                        <td></td>
                        <td>'.number_format($mt_total_ex_fact_qty, 0, '.', '').'</td>
                        <td>'.number_format($mt_total_ex_fact_value, 2, '.', '').'</td>
                        <td></td>
                        <td >'.number_format($grand_sales_minute, 2, '.', '').'</td>
                        <td>'.number_format($grand_sales_cm, 2, '.', '').'</td>
                        <td>'.number_format($total_buyer_basic_qnty, 0, '.', '').'</td>
                        <td></td>
                    </tfoot>
                </table>
            </div>';
			$html .= '<br />';

			$html .= ' <div>
                <table>
                    <tr>
                    	<td>Shipped Out Order Details Report</strong></td>
                    </tr>
                </table>
                <table>
                    <thead>
                        <th>SL</th>
                        <th>Company</th>
                        <th>Job</th>
                        <th>Year</th>
                        <th>Buyer Name</th>
                        <th>Client</th>
                        <th>Order NO</th>
                        <th>Del Company</th>
                        <th>Del Location</th>
                        <th>Del Floor</th>
                        <th>Challan NO</th>
                        <th>Invoice NO</th>
                        <th>Invoice Qty</th>
                        <th>Invoice Value</th>
                        <th>LC/SC NO</th>
                        <th>Design Source</th>
                        <th>Style Ref. no.</th>
                        <th>Style Description</th>
                        <th>Item Name</th>
                        <th>Item SMV</th>
                        <th>Shipment Date</th>
                        <th>Ex-Fac. Date</th>
                        <th><p>Po Rcv.Ship Mode</p></th>
                        <th>Shipping Mode</th>
                        <th>FOC/Claim</th>
                        <th>Days in Hand</th>
                        <th>UOM</th>
                        <th>PO Qtny. (pcs)</th>
                        <th>Unit Price</th>
                        <th>PO Value</th>
                        <th>PO TTL Up-Charge</th>
                        <th>Current Ex-Fact. Qty (pcs)</th>
                        <th>Current Ex-Fact. Value</th>

                        <th>UP Charge Based on Current Ex-facotry</th>
                        <th>Current Ex-Fact. Value with Up Charge</th>

                        <th>Current Carton Qty</th>
                        <th>Total Ex-Fact. Qty.</th>
                        <th>Total Ex-Fact. Value</th>

                        <th>Up Charge Based on TTL Ex-factory</th>
                        <th>Up charge with Total Ex-Fact. Value</th>

                        <th>Total Carton Qty</th>
                        <th>Sales Minute</th>
                        <th>Total Ex-Fact. (Basic Qty)</th>
                        <th>Excess/ Shortage Qty</th>
                        <th>Excess/ Shortage Value</th>
                        <th>Total Ex-Fact. Qty. %</th>
                        <th>Sales CM Cost</th>
                        <th>Sales CM</th>
                        <th>C & F Name</th>
                        <th>Vehicle No</th>
                        <th>Driver Info</th>
                        <th>Inspection Date</th>
                        <th>Ex-Fact Status</th>
                    </thead>
                </table>
            <div>';

			$html .=  $details_report;

			$html .='</table>';

			$html .=' <table>
                <tfoot>
                    <tr>
                    	<th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>Total</th>
                        <th>'.number_format($total_inv_qty,0, '.', '').'</th>
                        <th>'.number_format($total_inv_val,0, '.', '').'</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>'.  number_format($gr_po_qnty_pcs,0, '.', '').'</th>
                        <th></th>
                        <th></th>'. number_format($gr_po_qnty_val,2, '.', '').'</th>
                        <th>'. number_format($gr_upcharge,0, '.', '').'</th>
                        <th>'.number_format($total_ex_qty,0, '.', '').'</th>
                        <th>'. number_format($total_ex_valu,2, '.', '').'</th>
                        <th>'.number_format($gr_current_ex_up_charge,2, '.', '').'</th>
                        <th>'.number_format($gr_current_ex_up_charge_value,2, '.', '').'</th>
                        <th>'.$total_qty .'</th>
                        <th>'.number_format($gr_ttl_ex_qnty,0, '.', '').'</th>
                        <th>'.number_format($gr_ttl_ex_qnty_val,2, '.', '').'</th>
                        <th>'.number_format($gr_total_ex_up_charge,2, '.', '').'</th>
                        <th>'.number_format($gr_total_ex_up_charge_value,2, '.', '').'</th>
                        <th>'.number_format($gr_ttl_carton_qt,0, '.', '').'</th>
                        <th>'.number_format($gr_sales_min,0, '.', '').'</th>
                        <th>'.number_format($gr_ttl_basic_qty,0, '.', '').'</th>
                        <th>'.number_format($gr_ttl_short_qty,0, '.', '').'</th>
                        <th>'.number_format($gr_ttl_short_val,0, '.', '').'</th>
                        <th>'.number_format($gr_ttl_ex_fac_per,0, '.', '').'</th>
                        <th>'.number_format($gr_ttl_sales_cm_cost,2, '.', '').'</th>
                        <th>'.number_format($total_salesCM,4, '.', '').'</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
            </div>
            </div>
        </div>';
	}else if($reportType==2) // FOR MICROFIBER (Country wise 2) tmp done
	{
		if($source_cond) $source_cond2=str_replace("d.","a.",$source_cond);
		$challan_mst_arr=array();
		$details_report .='<table width="4220" cellspacing="0" cellpadding="0"  border="1"  class="rpt_table" rules="all" id="table_body2">';
		$i=1;
		$sql= "SELECT A.id AS EX_ID, A.LC_SC_NO AS LC_SC_ARR_NO, A.INVOICE_NO AS INVOICE_NO, A.ITEM_NUMBER_ID AS ITM_NUM_ID, A.SHIPING_MODE AS SHIPING_MODE, A.DELIVERY_MST_ID AS CHALLAN_ID, A.IS_POSTED_ACCOUNT, A.ENTRY_FORM, A.EX_FACTORY_QNTY, A.TOTAL_CARTON_QNTY AS CARTON_QNTY, A.EX_FACTORY_DATE AS EX_FACTORY_DATE, d.SYS_NUMBER,

		B.ID AS PO_ID, B.GROUPING, B.SHIPMENT_DATE, B.PO_NUMBER, c.id as job_id, C.COMPANY_NAME, C.BUYER_NAME, C.JOB_NO_PREFIX_NUM, C.JOB_NO, C.SHIP_MODE, TO_CHAR(C.INSERT_DATE,'YYYY') AS YEAR, C.STYLE_REF_NO, C.STYLE_DESCRIPTION, C.SET_SMV, c.TOTAL_SET_QNTY, c.SET_BREAK_DOWN, C.ORDER_UOM,
		D.DELIVERY_FLOOR_ID AS DEL_FLOOR, D.DELIVERY_COMPANY_ID AS DEL_COMPANY, D.SOURCE, D.DELIVERY_LOCATION_ID AS DEL_LOCATION, D.LOCK_NO, D.FORWARDER, D.TRUCK_NO, D.DRIVER_NAME, D.MOBILE_NO, D.DL_NO,
		a.COUNTRY_ID, E.ORDER_QUANTITY AS PO_QUANTITY, (E.ORDER_RATE) AS UNIT_PRICE, E.SHIPING_STATUS, F.current_invoice_qnty as INVOICE_QUANTITY

		from wo_po_break_down b, wo_po_details_master c, pro_ex_factory_delivery_mst d,wo_po_color_size_breakdown e,pro_ex_factory_mst a
		left join com_export_invoice_ship_dtls f on a.invoice_no=f.mst_id and a.po_break_down_id=f.po_breakdown_id and f.is_deleted=0 and f.status_active=1
		where a.po_break_down_id=b.id and b.job_id=c.id and c.id=e.job_id and b.id=e.po_break_down_id and a.delivery_mst_id=d.id  $company_cond $str_cond $del_comp_cond $del_location_cond $del_floor_cond $shiping_status_cond $source_cond $buyer_conds $internal_ref_cond $search_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in (1,2,3) and c.is_deleted=0 and c.status_active in (1,2,3) and e.status_active in (1,2,3) and e.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and A.ex_factory_qnty>0 and E.ORDER_QUANTITY>0 and A.ENTRY_FORM!=85
		order by a.ex_factory_date";//and e.country_id=a.country_id  and a.item_number_id=e.item_number_id  and A.ENTRY_FORM!=85

		// echo $sql;die;
		$sql_result=sql_select($sql);
		$all_po_id_arr = array();
		$job_id_arr = array();
		$invoice_id_arr = array();
		$lc_sc_id_arr = array();
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

		$inspection_date_arr=return_library_array( "SELECT b.po_break_down_id, max(b.inspection_date) as inspection_date from pro_buyer_inspection a, gbl_temp_engine d where status_active=1 and is_deleted=0 and b.po_break_down_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=17 and d.ref_from=11 group by b.po_break_down_id", "po_break_down_id", "inspection_date");

		$lc_num_arr=return_library_array( "SELECT id,export_lc_no from com_export_lc where status_active=1 $lc_sc_id_cond", "id", "export_lc_no"  );
		$sc_num_arr=return_library_array( "SELECT id,contract_no from com_sales_contract where status_active=1 $lc_sc_id_cond", "id", "contract_no"  );

		$forwarder_arr=return_library_array( "SELECT id,supplier_name from lib_supplier", "id", "supplier_name");
		// ======================================================================


		$data_array = array();
		$data_array_details=array();
		$all_challan_id_arr=array();
		$po_wise_buyer_arr=array();
		$po_wise_unit_price_arr=array();
		$mst_id_chk_arr = array();
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
			$data_array[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']]['del_company'] .= $company_library[$val['DEL_COMPANY']].",";
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
				if($mst_id_chk_arr[$val['EX_ID']]=="")
				{
					$data_array[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']]['ex_factory_qnty'] += $val['EX_FACTORY_QNTY'];
					$data_array_details[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']][$val['CHALLAN_ID']]['sys_number'] = $val['SYS_NUMBER'];
					$data_array_details[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']][$val['CHALLAN_ID']]['ex_factory_qnty'][$val['EX_ID']] += $val['EX_FACTORY_QNTY'];
					$data_array_details[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']][$val['CHALLAN_ID']]['ex_fac_qty'] += $val['EX_FACTORY_QNTY'];
					$mst_id_chk_arr[$val['EX_ID']] = $val['EX_ID'];
				}
			}
			else if($val['ENTRY_FORM']==85)
			{
				// $data_array[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']]['ex_factory_rtn_qnty'] += $val['EX_FACTORY_QNTY'];
				// $data_array_details[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']][$val['CHALLAN_ID']]['rtn_sys_number'] = $val['SYS_NUMBER'];
				// $data_array_details[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']][$val['CHALLAN_ID']]['ex_factory_rtn_qnty'][$val['EX_ID']] += $val['EX_FACTORY_QNTY'];
			}


			$data_array_details[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']][$val['CHALLAN_ID']]['invoice_no'] .= $invoice_array[$val['INVOICE_NO']].",";
			$data_array_details[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']][$val['CHALLAN_ID']]['invoice_qnty'][$val['INVOICE_NO']] = $val['INVOICE_QUANTITY'];
			$data_array_details[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']][$val['CHALLAN_ID']]['is_posted_account'] = $val['IS_POSTED_ACCOUNT'];

			$all_challan_id_arr[$val['JOB_NO']][$val['PO_ID']][$val['COUNTRY_ID']][$val['CHALLAN_ID']]++;

			$po_wise_buyer_arr[$val['PO_ID']]=$val['BUYER_NAME'];
			$po_wise_unit_price_arr[$val['PO_ID']]=$val['UNIT_PRICE'];


		}
		//  echo "<pre>";print_r($data_array_details);die();
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
							if( trim($val[0])== implode(",",array_unique(array_filter(explode(",", $row["itm_num_id"])))))
							{
								$item_smv=$val[2];
							}
						}
					}
					$master_data[$row["buyer_name"]]['b_id']=$row["buyer_name"];
		  			$master_data[$row["buyer_name"]]['org_po_qnty'] +=$order_country_qnty_arr[$po_key][$country_key];

					$master_data[$row["buyer_name"]]['po_qnty'] +=$order_country_qnty_arr[$po_key][$country_key];
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
		 	  		$total_po_valu+=($order_country_qnty_arr[$po_key][$country_key]*$unit_price_country_qnty_arr[$job_key][$po_key][$country_key]);
					$master_data[$row["buyer_name"]]['basic_qnty'] +=$basic_qnty;
					$master_data[$row["buyer_name"]]['ex_factory_qnty'] +=$exfact_country_qnty_arr[$po_key][$country_key]['ex']-$ex_rtn_qty;
					$master_data[$row["buyer_name"]]['total_ex_fact_qty'] +=$exfact_country_qnty_arr[$po_key][$country_key]['tot_ex']-$ex_rtn_qty;
					$master_data[$row["buyer_name"]]['total_ex_fact_value'] +=($exfact_country_qnty_arr[$po_key][$country_key]['tot_ex']-$tot_ex_rtn_qty)*$row["unit_price"];
					$master_data[$row["buyer_name"]]['sales_min'] += $item_smv*$exfact_country_qnty_arr[$po_key][$country_key]['ex'];
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


        $html .= '<fieldset>
            <div>
                <table>
                    <tr>
                        <td>
                            Company Name:'.  $company_library[$cbo_company_name].'
                        </td>
                    </tr>
                    <tr>
                        <td> Ex-Factory Report</td>
                    </tr>
                     <tr>
                        <td> Total Summary</td>
                    </tr>
                    </table>
                    <table>
                    <thead>
                        <th>SL</th>
                        <th>Buyer Name</th>
                        <th>PO Qty</th>
                        <th>PO Qty (pcs)</th>
                        <th>PO Value</th>
                        <th>PO Value(%)</th>
                        <th>Current Ex-Fact. Qty.</th>
                        <th>Current Ex-Fact. Value</th>
                        <th>Total Ex-Fact. Qty.</th>
                        <th>Total Ex-Fact. Value </th>
                        <th width="80">Ex-Fact. Rtn Qty </th>
                        <th>Sales Minutes</th>
                        <th>Total Ex-Fact. (Basic Qty)</th>
                        <th>Total Ex-Fact. Value %</th>
                    </thead>
                </table>
                <table>';

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
					$po_quantity_org=$rows["org_po_qnty"];
					$total_buyer_org_po_quantity+=$po_quantity_org;
					$po_quantity=$rows["po_qnty"];
					$total_buyer_po_quantity+=$po_quantity;
					$buyer_po_value=$rows["po_value"];
					$total_buyer_po_value+=$buyer_po_value;
					$total_ex_factory_rtn_qnty=$rows["ex_factory_rtn_qnty"];
					$mt_total_ex_factory_rtn_qnty+=$total_ex_factory_rtn_qnty;
					$buyer_basic_qnty=$rows["basic_qnty"];
					$total_buyer_basic_qnty +=$buyer_basic_qnty;
					$total_ex_fact_value=$rows["total_ex_fact_value"];
					$mt_total_ex_fact_value+=$total_ex_fact_value;

					$html .= '<tr>
                        <td>'. $m.'</td>
                        <td>'.$buyer_arr[$rows["b_id"]]. $master_data[$rows["b_id"]]['in_sub'].'</td>
                        <td>'. number_format($po_quantity_org,0 , '.', '').'</td>';
						$html .= '<td>'. number_format($po_quantity,0 , '.', '').'</td>';

						$html .= '<td>'. number_format($buyer_po_value,2 , '.', '').'</td>';

						$html .= '<td>'; if($buyer_po_value>0 && $total_po_val>0){
							$html .= number_format(($buyer_po_value/$total_po_val)*100,2,' ','');$parcentages+=($buyer_po_value/$total_po_val)*100;
							 }else{
								$html .= 0.00;
							 };
						$html .= '</td>';

                        $html .= '<td>'.$current_ex_Fact_Qty=$rows["ex_factory_qnty"];  $html .= number_format($current_ex_Fact_Qty,0, '.', ''); $total_current_ex_Fact_Qty +=$current_ex_Fact_Qty.'</td>';

						$html .= '<td>'. $current_ex_fact_value=$rows["ex_factory_value"]; $html .= number_format($current_ex_fact_value,2,' ',''); $total_current_ex_fact_value+=$current_ex_fact_value.'
                        </td>';
						$html .= '<td>'. $total_ex_fact_qty=$rows["total_ex_fact_qty"]; $html .= number_format($total_ex_fact_qty,0 , '.', ''); $mt_total_ex_fact_qty+=$total_ex_fact_qty.'</p>
                        </td>';
						$html .= '<td>'.number_format($total_ex_fact_value,2,' ','').'</td>';
						$html .= '<td>'. number_format($total_ex_factory_rtn_qnty,0, '.', '').'</td>';
						$html .= '<td>'.$g_sales_min+= number_format($rows["sales_min"],0,' ','').'</td>';

						$html .= ' <td>'. number_format($buyer_basic_qnty,0,' ','').'</td>';

						$html .= '<td>';
						if($total_ex_fact_value>0 && $buyer_po_value>0){
							$total_ex_fact_value_parcentage=($total_ex_fact_value/$buyer_po_value)*100;
							$html .= number_format($total_ex_fact_value_parcentage,0, '.', '');
						}else{
							 0.00;
						}

                $html .= '</td>';
				$html .= '</tr>';
                    $i++;$m++;
                    $buyer_po_quantity=0;
                    $buyer_po_value=0;
                    $current_ex_Fact_Qty=0;
                    $current_ex_fact_value=0;
                    $total_ex_fact_qty=0;
                    $total_ex_fact_value=0;
                    $g_sales_min=0;
                    $grand_sales_minute +=number_format($rows["sales_min"],0,' ','');
                }
			$html .= '</table>';
			$html .= '<table>';
					$html .= '<th></th>';
					$html .= '<th>Total:</th>';
					$html .= '<th>'. number_format($total_buyer_org_po_quantity,0 , '.', '').'</th>';
					$html .= '<th>'. number_format($total_buyer_po_quantity,0 , '.', '').'</th>';
					$html .= ' <th>'. number_format($total_buyer_po_value,2 ,'.', '').'</th>';
					$html .= '<th>'. ceil($parcentages).'</th>';
					$html .= '<th>'. number_format($total_current_ex_Fact_Qty,0, '.', '').'</th>';
					$html .= ' <th>'.  number_format($total_current_ex_fact_value,2, '.', '').'</th>';
					$html .= '<th>'. number_format($mt_total_ex_fact_qty,0, '.', '').'</th>';
					$html .= '<th>'. number_format($mt_total_ex_fact_value,2, '.', '').'</th>';
					$html .= '<th>'. number_format($mt_total_ex_factory_rtn_qnty,0, '.', '').'</th>';
					$html .= '<th>'. number_format($grand_sales_minute ,2, '.', '').'</th>';
					$html .= '<th>'. number_format($total_buyer_basic_qnty,0, '.', '').'</th>';
					$html .= '<th></th>';
			$html .= '</table>';
			$html .= '<br>';
			$html .= ' </div>
            <div>
                <table>
                    <tr>
                    <td>Shipped Out Order Details Report</strong></td>
                    </tr>
                </table>
                <table>
                    <thead>
						<tr>
							<th>SL</th>
							<th>Company</th>
							<th>Job</th>
							<th>Year</th>
							<th>Buyer Name</th>
							<th>Order NO</th>
							<th>Actual PO</th>
							<th>Country</th>
							<th>Internal ref.</th>
							<th>Del Company</th>
							<th>Del Location</th>
							<th>Del Floor</th>
							<th>Challan NO</th>
							<th>Challan Qty</th>
							<th>Invoice NO</th>
							<th>Invoice Qty</th>
							<th>Accounting Posting</th>
							<th>LC/SC NO</th>
							<th>Style Ref. no.</th>
							<th>Style Description</th>
							<th>Item Name</th>
							<th>Item SMV</th>
							<th>Country Shipment Date</th>
							<th>Shipment Date</th>
							<th>Ex-Fac. Date</th>
							<th><p>Po Rcv.Ship Mode</p></th>
							<th>Shipping Mode</th>
							<th>Days in Hand</th>
							<th>UOM</th>
							<th>PO/Country Qty. (pcs)</th>
							<th>Unit Price</th>
							<th>PO Value</th>
							<th>Current Ex-Fact. Qty (pcs)</th>
							<th>Current Ex-Fact. Value</th>
							<th>Current Carton Qty</th>
							<th>Total Ex-Fact. Qty.</th>
							<th>Total Ex-Fact. Value</th>
							<th>Ex-Fact. Rtn Qty</th>
							<th>Total Carton Qty</th>
							<th>Sales Minute</th>
							<th>Total Ex-Fact. (Basic Qty)</th>
							<th>Excess/ Shortage Qty</th>
							<th>Excess/ Shortage Value</th>
							<th>Total Ex-Fact. Qty. %</th>
							<th>Sales CM</th>
							<th>C & F Name</th>
							<th>Vehicle No</th>
							<th>Lock No</th>
							<th>Driver Info</th>
							<th>Inspection Date</th>
							<th>Ex-Fact Status</th>
						</tr>
                    </thead>
                </table>
            <div>
            	<table>
            		<tbody>';

            			$i +=$i;
		                $total_po_qty=0; $total_po_valu=0;$total_ex_qty=0;$total_ex_valu=0;$total_crtn_qty=0;
		                $g_total_ex_qty=0;$g_total_ex_val=0;$g_total_ex_rtn_qty=0;$g_total_ex_crtn=0;$g_sales_minutes=0;
		                $total_basic_qty=0;$total_eecess_storage_qty=0;$total_eecess_storage_val=0; $gr_ttl_ex_fac_per=0;
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
											if( trim($val[0])== implode(",",array_unique(array_filter(explode(",", $row["itm_num_id"])))))
											{
												$item_smv=$val[2];
											}
										}
									}
									$item_number_id = implode("_",array_unique(array_filter(explode(",", $row["itm_num_id"]))));
            						$challan_no = implode(", ", array_unique(array_filter(explode(",", $row['sys_number']))));
            						$rtn_challan_no = implode(", ", array_unique(array_filter(explode(",", $row['rtn_sys_number']))));
            						$del_company = implode(", ", array_unique(array_filter(explode(",", $row['del_company']))));
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

			             $html .=' <tr>
			            				<td>'.$i.'</td>
										<td>'.$company_library[$row["company_name"]].'</td>
										<td>'.$row["job_no_prefix_num"].'</td>
										<td>'.$row["year"].'</td>
										<td>'.$buyer_arr[$row["buyer_name"]].'</td>
										<td>'.$row["po_number"].'</td>
										<td>'.$acc_po_arr[$job_key][$po_key][$country_key].'</td>
										<td>'.$lib_country[$country_key].'</td>
										<td>'.$row["grouping"].'</td>
										<td>'.$del_company.'</td>
										<td>'.$del_location.'</td>
										<td>'.$del_floor.'</td>';

											$j=0;
											foreach($data_array_details[$job_key][$po_key][$country_key] as $challan_no=>$val)
											{
												if($j==0)
												{

											$html .= '<td>'.$val['sys_number'];
																if (!empty($val['rtn_sys_number']))
																{
																	echo $val['rtn_sys_number'];
																}

											$html .= '</td>';
											$html .= '<td>';
														$ex_factory_qnty=$val['ex_fac_qty'];
														if(!empty($ex_factory_qnty))
														{
															echo number_format($ex_factory_qnty,0, '.', '');
															$job_total_ex_factory_qnty+=$ex_factory_qnty;
															$total_ex_factory_qnty+=$ex_factory_qnty;
														}
														else
														{
															$ex_factory_rtn_qnty=array_sum($val['ex_factory_rtn_qnty']);
															echo number_format($ex_factory_rtn_qnty,2, '.', '');
															$job_total_ex_factory_qnty+=$ex_factory_rtn_qnty;
															$total_ex_factory_qnty+=$ex_factory_rtn_qnty;
														}

								            $html .= '</td>';
														$invoice_no=array_unique(explode(",",rtrim($val['invoice_no'],',')));
														$invoice_no_val= '';
														foreach($invoice_no as $value)
														{
															$invoice_no_val.= $value.', ';
														}
											$html .= '<td>'. rtrim($invoice_no_val,', ').'</td>';

											$invoice_qnty=$val['invoice_qnty'];
											$invoice_qnty_val='';
											foreach($invoice_qnty as $value)
											{
												$invoice_qnty_val.= number_format($value,0, '.', '');
											}

											$html .= '<td>'. rtrim($invoice_qnty_val,'- ').'</td>';
											$html .= '<td>'.$yes_no[$val['is_posted_account']].'</td>';
												}
												$j++;
											}

								$html .= '<td>'.$lc_sc_no.'</td>
										<td>'.$row["style_ref_no"].'</td>
										<td>'.$row["style_description"].'</td>
										<td>'.$item_name.'</td>
										<td>'.$item_smv.'</td>
										<td>'.change_date_format($country_ship_date_arr[$po_key][$country_key]).'</td>
										<td>'.change_date_format($row["shipment_date"]).'</td>
										<td>'.change_date_format($row['ex_factory_date']).'</td>
										<td>'.$shipment_mode[$row['ship_mode']].'</td>
										<td>'.$shipment_mode[$row["shiping_mode"]].'</td>
										<td>'.$diff.'</td>
										<td>'.$unit_of_measurement[$row['order_uom']].'</td>
										<td>'.number_format($order_country_qnty_arr[$po_key][$country_key], 0, '', '').'</td>
										<td>'.number_format($unit_price, 4, '.', '').'</td>
										<td>'.number_format(($order_country_qnty_arr[$po_key][$country_key] * $unit_price), 2, '.', '').'</td>
										<td >'.number_format($current_ex_Fact_Qty, 0,'.','').'</p>
									    </td>
										<td>'.number_format($current_ex_fact_value = $current_ex_Fact_Qty * $unit_price, 2, '.', '').'</td>
										<td>'.number_format($carton_qnty, 0,'.','').'</td>
										<td>'.number_format($total_ex_fact_qty, 0,'.','').'</td>
										<td>'.number_format($total_ex_fact_value = $total_ex_fact_qty * $unit_price, 2, '.', '').'</td>

										<td>'.number_format($ex_rtn_qty, 0,'.','').'</td>
										<td>'.number_format($total_cartoon_qty, 0,'.','').'</td>
										<td>'.number_format($total_sales_minutes = $total_ex_fact_qty * $item_smv,0, '.', '').'</td>
										<td>'.number_format($basic_qnty, 0,'.','').'</td>
										<td>'.number_format($excess_shortage_qty = $total_ex_fact_qty - $order_country_qnty_arr[$po_key][$country_key], 0,'.','').'</td>
										<td>'.number_format($excess_shortage_value = ($excess_shortage_qty * $unit_price), 2, '.', '').'</td>
										<td>';

												$ex_fact_qty_parcent = 0;
												if($total_ex_fact_qty>0 && $order_country_qnty_arr[$po_key][$country_key]>0)$ex_fact_qty_parcent = $total_ex_fact_qty / $order_country_qnty_arr[$po_key][$country_key];

												$html .= number_format($total_ex_fact_qty_parcent = $ex_fact_qty_parcent * 100, 0, '.', '');

							  $html .= '</td>';
							  $html .= '<td>'.number_format($cm_per_pcs * $total_ex_fact_qty, 2,'.', '').'</td>';
							  $html .= '<td>'.$forwarder.'</td>';
							  $html .= '<td>'.$vehi_no.'</td>
										<td>'.$lock_no.'</td>
										<td>'.$dirver_info.'</td>
										<td>'.(change_date_format($inspection_date_arr[$po_key]) == '0000-00-00' || change_date_format($inspection_date_arr[$po_key]) == '' ? '' : change_date_format(change_date_format($inspection_date_arr[$po_key]))).'</td>
										<td>'.$shipment_status[$row['shiping_status']].'</td>
			            			</tr>';
										$k=0;
										foreach($data_array_details[$job_key][$po_key][$country_key] as $val)
										{
											if($k!=0){
									$html .= '<tr>
													<td>'.$val['sys_number'];if (!empty($val['rtn_sys_number'])) {$html .= $val['rtn_sys_number'];}
										$html .= '</td>
												  <td>';
														$ex_factory_qnty=$val['ex_fac_qty'];
														if(!empty($ex_factory_qnty)){
															$html .= fn_number_format($ex_factory_qnty,0, '.', '');
															$job_total_ex_factory_qnty+=$ex_factory_qnty;
															$total_ex_factory_qnty+=$ex_factory_qnty;
														}
														else{
															$ex_factory_rtn_qnty=array_sum($val['ex_factory_rtn_qnty']);
															$html .= fn_number_format($ex_factory_rtn_qnty,2, '.', '');
															$job_total_ex_factory_qnty+=$ex_factory_rtn_qnty;
															$total_ex_factory_qnty+=$ex_factory_rtn_qnty;
														}
											$html .= '</td>';
															$invoice_no=array_unique(explode(",",rtrim($val['invoice_no'],',')));
															$invoice_no_val= '';
															foreach($invoice_no as $value){
																$invoice_no_val.= $value.', ';
															}
											$html .= '<td>'. rtrim($invoice_no_val,', ').'</td>';
											$invoice_qnty=$val['invoice_qnty'];
											$invoice_qnty_val='';
											foreach($invoice_qnty as $value){
												$invoice_qnty_val.= fn_number_format($value,0, '.', '').' - ';
											}
											$html .= '<td>'.rtrim($invoice_qnty_val,'- ').'</td>';
											$html .= '<td>'.$yes_no[$val['is_posted_account']].'</td>';
									$html .= '</tr>';
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


		        $html .= '<tr>
		                        <td></td>
		                        <td></td>
		                        <td></td>
		                        <td></td>
		                        <td></td>
		                        <td></td>
		                        <td></td>
		                        <td></td>
		                        <td></td>
		                        <td></td>
		                        <td></td>
		                        <td></td>
		                    	<td>Job Total</td>
		                    	<td>'.number_format($job_total_ex_factory_qnty,0, '.', '').'</td>
								<td></td>
		                        <td></td>
		                        <td></td>
								<td></td>
		                        <td></td>
		                        <td></td>
								<td></td>
		                        <td></td>
		                        <td></td>
								<td></td>
		                        <td></td>
		                        <td></td>
								<td></td>
		                        <td></td>
		                        <td></td>
		                        <td>'.number_format($job_total_po_qty,0, '.', '').'</td>
		                        <td></td>
		                        <td>'.number_format($job_total_po_valu,2, '.', '').'</td>
		                        <td>'.number_format($job_total_ex_qty,0, '.', '').'</td>
		                        <td>'.number_format($job_total_ex_valu,2, '.', '').'</td>
		                        <td>'.number_format($job_total_crtn_qty,0, '.', '').'</td>
		                        <td>'.number_format($job_g_total_ex_qty,0, '.', '').'</td>
		                        <td>'.number_format($job_g_total_ex_val,2, '.', '').'</td>
		                        <td>'.number_format($job_total_ex_rtn_qty,0, '.', '').'</td>
		                        <td>'.number_format($job_g_total_ex_crtn,0, '.', '').'</td>
		                        <td>'.number_format($job_g_sales_minutes,0, '.', '').'</td>

		                        <td>'.number_format($job_total_basic_qty,0, '.', '').'</td>
		                        <td>'.number_format($job_total_eecess_storage_qty,0, '.', '').'</td>
		                        <td>'.number_format($job_total_eecess_storage_val,0, '.', '').'</td>
		                        <td>'.number_format($job_gr_ttl_ex_fac_per,0, '.', '').'</td>
		                        <td>'.number_format($job_cm_per_pcs_tot,2).'</td>
		                        <td></td>
		                        <td></td>
		                        <td></td>
		                        <td></td>
		                        <td></td>
		                        <td></td>';

            			}

		   $html .= '</tbody>
            	</table>
            </div>
            <table>
                <tfoot>
                    <tr>
                    	<th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>Grand Total</th>
                        <th>'.number_format($total_ex_factory_qnty,0, '.', '').'</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>'.number_format($total_po_qty,0, '.', '').'</th>
                        <th></th>
                        <th>'.number_format($total_po_valu,2, '.', '').'</th>
                        <th>'. number_format($total_ex_qty,0, '.', '').'</th>
                        <th>'. number_format($total_ex_valu,2, '.', '').'</th>
                        <th>'. number_format($total_crtn_qty,0, '.', '').'</th>
                        <th>'. number_format($g_total_ex_qty,0, '.', '').'</th>
                        <th>'. number_format($g_total_ex_val,2, '.', '').'</th>
                        <th>'. number_format($g_total_ex_rtn_qty,0, '.', '').'</th>
                        <th>'. number_format($g_total_ex_crtn,0, '.', '').'</th>
                        <th>'. number_format($g_sales_minutes,0, '.', '').'</th>
                        <th>'. number_format($total_basic_qty,0, '.', '').'</th>
                        <th>'. number_format($total_eecess_storage_qty,0, '.', '').'</th>
                        <th>'. number_format($total_eecess_storage_val,0, '.', '').'</th>
                        <th>'. number_format($gr_ttl_ex_fac_per,0, '.', '').'</th>
                        <th>'. number_format($cm_per_pcs_tot,2, '.', '').'</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
            </div>
        </fieldset>';

	}


	foreach (glob("DailyExFactory_$user_id*.xlsx") as $filename) {
		@unlink($filename);
	}
	$name=time();
	$filename='DailyExFactory_'.$user_id."_".$name.".xlsx";
	//echo "$html####$filename"; die;
	//echo $filename;die;

	$reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
	$spreadsheet = $reader->loadFromString($html);


	$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
	$writer->save($filename);
    echo "$filename####$filename####$reportType";
	exit();


}


if($action=="ex_return_popup")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$ex_factory_date=str_replace("'","",$ex_factory_date);
	$ex_factory_date_ref=explode("_",$ex_factory_date);
	$exfact_date=explode("*",$ex_factory_date_ref[0]);
	$item_number_id=str_replace("'","",$item_number_id);
	$order_id=str_replace("'","",$order_id);
	$challan_id=str_replace("*","'",$challan_id);


	$returnExSql="SELECT a.SYS_NUMBER, b.CHALLAN_NO,B.EX_FACTORY_DATE,B.PO_BREAK_DOWN_ID AS PO_ID,  B.ITEM_NUMBER_ID,  B.EX_FACTORY_QNTY AS RETURN_QNTY
		from  pro_ex_factory_delivery_mst a,pro_ex_factory_mst b  where a.id=b.DELIVERY_MST_ID and  b.status_active=1 and b.is_deleted=0 and b.po_break_down_id=$order_id and B.ITEM_NUMBER_ID =$item_number_id and b.entry_form=85 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and b.CHALLAN_NO in('$challan_id')";
	  //echo $returnExSql;
	$returnExSqlResult=sql_select($returnExSql);



	?>
	<div style="width:100%" align="center">
		<fieldset style="width:500px">
        <div class="form_caption" align="center"><strong>Ex-Factory Return Details</strong></div><br />
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="100">Date</th>
                        <th width="120">SYS ID</th>
                        <th width="120">Delivery Challan No</th>
                        <th width="">Return Qty</th>
                     </tr>
                </thead>
                <tbody>
					<?
						$i=1;
						foreach($returnExSqlResult as $rows)
						{
							?>
                            <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
                                <td><? echo $i; ?></td>
                                <td align="center"><?= change_date_format($rows[EX_FACTORY_DATE]); ?></td>
                                <td align="center"><?= $rows[SYS_NUMBER]; ?></td>
                                <td align="center"><?= $rows[CHALLAN_NO]; ?></td>
                                <td align="right"><?= $rows[RETURN_QNTY]; ?></td>
                            </tr>
							<?
							$i++;
						}
                    ?>
                </tbody>
            </table>
        </fieldset>
    </div>
    <?
}

if($action=="ex_date_popup")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$ex_factory_date=str_replace("'","",$ex_factory_date);
	$ex_factory_date_ref=explode("_",$ex_factory_date);
	$exfact_date=explode("*",$ex_factory_date_ref[0]);
	$item_number_ids=str_replace("'","",$item_number_id);
	$itm_ex = explode("__", $item_number_ids);
	$item_number_id = implode(",", explode("_", $itm_ex[0]));
	$country_cond = '';
	$country_cond_rtn = '';
	if($itm_ex[1]!="")
	{
		$country_cond = " and a.country_id=$itm_ex[1]";
		$country_cond_rtn = " and b.country_id=$itm_ex[1]";
	}

	if($itm_ex[2]!="" && $itm_ex[3]!="") // for country2 button
	{
		$ex_date_cond = " and a.ex_factory_date between  '$itm_ex[2]' and '$itm_ex[3]'";
	}

	$exfact_date_cond='';
	if($exfact_date[0]!="" && $exfact_date[1]!="")
	{
		$exfact_date_cond = " and a.ex_factory_date between  '$exfact_date[0]' and '$exfact_date[1]'";
	}

	// echo $exfact_date_cond;die();
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
                        <?php
                        	// if($type != 1) {
						?>
                        	<th width="">Return Qty</th>
                        <?php
                        	// }
                        ?>

                     </tr>
                </thead>
                <tbody>
					<?

					if($type==1)
					{
						 if($challan_id!=""){$challan_id_cond= "and a.delivery_mst_id=$challan_id"; }else{$challan_id_cond= "";}
						$sql_res=sql_select("SELECT b.po_break_down_id as po_id,  b.item_number_id,
							sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as return_qnty
							from  pro_ex_factory_mst b  where  b.po_break_down_id=$order_id and b.item_number_id=$item_number_id $country_cond_rtn and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id,b.item_number_id");
						$ex_factory_qty_arr=array();
						foreach($sql_res as $row)
						{

							$ex_factory_qty_arr[$row[csf('po_id')]][$row[csf('item_number_id')]]['return_qty']=$row[csf('return_qnty')];
						}

						$i=1;
						if($ex_factory_date_ref[1]==2)
						{
							$sql_qnty="SELECT a.po_break_down_id,a.item_number_id,a.ex_factory_date,b.SYS_NUMBER as challan_no,a.country_id,
							sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty__,
							sum(CASE WHEN a.ENTRY_FORM!=85 $ex_date_cond THEN a.EX_FACTORY_QNTY ELSE 0 END) AS EX_FACTORY_QNTY,

							sum(CASE WHEN a.entry_form=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_ret_qnty

							from pro_ex_factory_mst a,pro_ex_factory_delivery_mst b where a.delivery_mst_id=b.id and  a.po_break_down_id=$order_id and a.item_number_id=$item_number_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 $source_cond group by a.po_break_down_id,a.item_number_id,a.ex_factory_date,b.SYS_NUMBER,a.country_id order by a.ex_factory_date ";
						}
						else
						{

							$sql_qnty="SELECT a.po_break_down_id,a.item_number_id,a.ex_factory_date,b.SYS_NUMBER as challan_no,a.country_id,

							sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty__,
							sum(CASE WHEN a.ENTRY_FORM!=85 $ex_date_cond THEN a.EX_FACTORY_QNTY ELSE 0 END) AS EX_FACTORY_QNTY,
							sum(CASE WHEN a.entry_form=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_ret_qnty

							from pro_ex_factory_mst a ,pro_ex_factory_delivery_mst b where a.delivery_mst_id=b.id and a.po_break_down_id=$order_id and a.item_number_id=$item_number_id and a.status_active=1 and a.is_deleted=0 $exfact_date_cond $country_cond $challan_id_cond $source_cond and b.status_active=1  group by a.po_break_down_id,a.item_number_id,a.ex_factory_date,b.SYS_NUMBER,a.country_id order by a.ex_factory_date ";


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
 						 $sql_qnty="SELECT  a.vehical_no,a.delivery_no as challan_no,b.subcon_job ,b.job_no_prefix_num as job,b.party_id as buyer_name,b.company_id as company_name,a.company_id as delivery_company_id,a.location_id,c.cust_style_ref as style_ref_no,c.order_no as po_number,c.id as po_id, a.delivery_date as ex_factory_date   ,c.order_quantity as po_quantity ,c.delivery_date as shipment_date,c.smv,d.item_id,d.total_carton_qnty,  d.delivery_qty as ex_factory_qnty,b.insert_date,c.rate as unit_price from subcon_delivery_mst a,subcon_delivery_dtls d, subcon_ord_mst b,subcon_ord_dtls c   where  a.id=d.mst_id and d.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0   and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form<>4 and a.process_id=3 and c.id='$order_id' and d.item_id='$item_number_id' and b.company_id='$company_id'  $date_cond  $challan_cond";


					}
					else if($type==11)
					{
						if($challan_id!=""){$challan_id_cond= "and a.delivery_mst_id in($challan_id)"; }else{$challan_id_cond= "";}
						$sql="SELECT b.po_break_down_id as po_id,  b.item_number_id,
							sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as return_qnty
							from  pro_ex_factory_mst b  where  b.status_active=1 and b.delivery_mst_id in($challan_id) and b.is_deleted=0 group by b.po_break_down_id,b.item_number_id";
							 //echo $sql;

						$sql_res=sql_select($sql);
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

							from pro_ex_factory_mst a,pro_ex_factory_delivery_mst b where a.delivery_mst_id=b.id and  a.po_break_down_id=$order_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 $source_cond  group by a.po_break_down_id,a.item_number_id,a.ex_factory_date,a.challan_no,a.country_id order by a.ex_factory_date ";
						}
						else
						{

							$sql_qnty="SELECT a.po_break_down_id,a.item_number_id,a.ex_factory_date,a.challan_no,a.country_id,

							sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty

							from pro_ex_factory_mst a ,pro_ex_factory_delivery_mst b where a.delivery_mst_id=b.id and a.po_break_down_id=$order_id and a.status_active=1 and a.is_deleted=0 and a.ex_factory_date between  '$exfact_date[0]' and '$exfact_date[1]' $challan_id_cond $source_cond and b.status_active=1 group by a.po_break_down_id,a.item_number_id,a.ex_factory_date,a.challan_no,a.country_id order by a.ex_factory_date ";
						// and a.item_number_id=$item_number_id

						}

					}

						//print_r($challan_id);

						// echo $sql_qnty;
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
                                    <?php
			                        if($type != 1) {
									?>
                                    <td width="100" align="right"><? echo number_format($row_real[csf("ex_factory_qnty")]-$row_real[csf("ex_factory_ret_qnty")],2); ?>&nbsp;</td>
                                    <?php
                                    	} else {
                                    ?>
                                    	<td width="100" align="right"><? echo number_format($row_real[csf("ex_factory_qnty")],2); ?>&nbsp;</td>
                                    <?php
                                    	}
                                    ?>

									<?php
			                        	// if($type != 1) {
									?>
                                    <td width="" align="right"><? echo number_format($row_real[csf("ex_factory_ret_qnty")],2); ?>&nbsp;</td>
                                    <?php
                                    	// }
                                    ?>
								</tr>
							<?
							$total_ex_qnty+=$row_real[csf("ex_factory_qnty")];
							$total_return_ex_qnty+=$row_real[csf("ex_factory_ret_qnty")];
							$i++;
						}
                    ?>
                </tbody>
                <tfoot>
                	<tr>
                    	<th colspan="4" align="right"><strong>Total :</strong></th>
                        <th align="right"><? echo number_format($total_ex_qnty,2); ?></th>
                        <?php
                        	// if($type != 1) {
						?>
                        <th align="right"><? echo number_format($total_return_ex_qnty,2); ?> </th>
                        <?php
                        	// }
                        ?>
                    </tr>
                    <tr>
                    	<th colspan="4" align="right"><strong>Total Balance:</strong></th>
                        <!-- <th align="right" colspan="<?php echo $type == 1 ? 1 : 2; ?>">
                        	<?php
	                        	if($type != 1) {
	                        		echo number_format($total_ex_qnty-$total_return_ex_qnty,2);
                            	} else {
                            		echo number_format($total_ex_qnty,2);
                            	}
                            ?>
                        </th> -->
                        <th align="right"><? echo number_format($total_ex_qnty-$total_return_ex_qnty,2);?></th>
                        <th align="right"><? //echo number_format($total_return_ex_qnty,2);?></th>

                    </tr>
                </tfoot>
            </table>
        </fieldset>
    </div>
    <?
}

if($action=="current_ex_date_popup")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$ex_factory_date=str_replace("'","",$ex_factory_date);
	$challan_id=str_replace("'","",$challan_id);
	$ex_factory_date_ref=explode("_",$ex_factory_date);
	$exfact_date=explode("*",$ex_factory_date_ref[0]);
	$item_number_ids=str_replace("'","",$item_number_id);
	$itm_ex = explode("__", $item_number_ids);
	$item_number_id = implode(",", explode("_", $itm_ex[0]));
	$country_cond = '';
	$country_cond_rtn = '';
	if($itm_ex[1]!="")
	{
		$country_cond = " and a.country_id=$itm_ex[1]";
		$country_cond_rtn = " and b.country_id=$itm_ex[1]";
	}

	if($itm_ex[2]!="" && $itm_ex[3]!="") // for country2 button
	{
		$ex_date_cond = " and a.ex_factory_date between  '$itm_ex[2]' and '$itm_ex[3]'";
	}

	$exfact_date_cond='';
	if($exfact_date[0]!="" && $exfact_date[1]!="")
	{
		$exfact_date_cond = " and a.ex_factory_date between  '$exfact_date[0]' and '$exfact_date[1]'";
	}

	// echo $exfact_date_cond;die();
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
                        <?php
                        	// if($type != 1) {
						?>
                        	<th width="">Return Qty</th>
                        <?php
                        	// }
                        ?>

                     </tr>
                </thead>
                <tbody>
					<?

					if($type==1)
					{
						// if($challan_id!=""){$challan_id_cond= "and a.delivery_mst_id=$challan_id"; }else{$challan_id_cond= "";}
						$sql_res=sql_select("SELECT b.po_break_down_id as po_id,  b.item_number_id,
							sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as return_qnty
							from  pro_ex_factory_mst b  where  b.po_break_down_id=$order_id and b.item_number_id=$item_number_id $country_cond_rtn and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id,b.item_number_id");
						$ex_factory_qty_arr=array();
						foreach($sql_res as $row)
						{

							$ex_factory_qty_arr[$row[csf('po_id')]][$row[csf('item_number_id')]]['return_qty']=$row[csf('return_qnty')];
						}

						$i=1;
						if($ex_factory_date_ref[1]==2)
						{
							$sql_qnty="SELECT a.po_break_down_id,a.item_number_id,a.ex_factory_date,b.SYS_NUMBER as challan_no,a.country_id,
							sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty__,
							sum(CASE WHEN a.ENTRY_FORM!=85 $ex_date_cond THEN a.EX_FACTORY_QNTY ELSE 0 END) AS EX_FACTORY_QNTY,

							sum(CASE WHEN a.entry_form=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_ret_qnty

							from pro_ex_factory_mst a,pro_ex_factory_delivery_mst b where a.delivery_mst_id=b.id and  a.po_break_down_id=$order_id and a.item_number_id=$item_number_id and b.id=$challan_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 $source_cond group by a.po_break_down_id,a.item_number_id,a.ex_factory_date,b.SYS_NUMBER,a.country_id order by a.ex_factory_date ";
						}
						else
						{

							$sql_qnty="SELECT a.po_break_down_id,a.item_number_id,a.ex_factory_date,b.SYS_NUMBER as challan_no,a.country_id,

							sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty__,
							sum(CASE WHEN a.ENTRY_FORM!=85 $ex_date_cond THEN a.EX_FACTORY_QNTY ELSE 0 END) AS EX_FACTORY_QNTY,
							sum(CASE WHEN a.entry_form=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_ret_qnty

							from pro_ex_factory_mst a ,pro_ex_factory_delivery_mst b where a.delivery_mst_id=b.id and a.po_break_down_id=$order_id and a.item_number_id=$item_number_id and b.id=$challan_id and a.status_active=1 and a.is_deleted=0 $exfact_date_cond $country_cond $challan_id_cond $source_cond and b.status_active=1  group by a.po_break_down_id,a.item_number_id,a.ex_factory_date,b.SYS_NUMBER,a.country_id order by a.ex_factory_date ";


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
 						 $sql_qnty="SELECT  a.vehical_no,a.delivery_no as challan_no,b.subcon_job ,b.job_no_prefix_num as job,b.party_id as buyer_name,b.company_id as company_name,a.company_id as delivery_company_id,a.location_id,c.cust_style_ref as style_ref_no,c.order_no as po_number,c.id as po_id, a.delivery_date as ex_factory_date   ,c.order_quantity as po_quantity ,c.delivery_date as shipment_date,c.smv,d.item_id,d.total_carton_qnty,  d.delivery_qty as ex_factory_qnty,b.insert_date,c.rate as unit_price from subcon_delivery_mst a,subcon_delivery_dtls d, subcon_ord_mst b,subcon_ord_dtls c   where  a.id=d.mst_id and d.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0   and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form<>4 and a.process_id=3 and c.id='$order_id' and d.item_id='$item_number_id' and b.company_id='$company_id'  $date_cond  $challan_cond";


					}
					else if($type==11)
					{
						if($challan_id!=""){$challan_id_cond= "and a.delivery_mst_id in($challan_id)"; }else{$challan_id_cond= "";}
						$sql="SELECT b.po_break_down_id as po_id,  b.item_number_id,
							sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as return_qnty
							from  pro_ex_factory_mst b  where  b.status_active=1 and b.delivery_mst_id in($challan_id) and b.is_deleted=0 group by b.po_break_down_id,b.item_number_id";
							 //echo $sql;

						$sql_res=sql_select($sql);
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

							from pro_ex_factory_mst a,pro_ex_factory_delivery_mst b where a.delivery_mst_id=b.id and  a.po_break_down_id=$order_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 $source_cond  group by a.po_break_down_id,a.item_number_id,a.ex_factory_date,a.challan_no,a.country_id order by a.ex_factory_date ";
						}
						else
						{

							$sql_qnty="SELECT a.po_break_down_id,a.item_number_id,a.ex_factory_date,a.challan_no,a.country_id,

							sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty

							from pro_ex_factory_mst a ,pro_ex_factory_delivery_mst b where a.delivery_mst_id=b.id and a.po_break_down_id=$order_id and a.status_active=1 and a.is_deleted=0 and a.ex_factory_date between  '$exfact_date[0]' and '$exfact_date[1]' $challan_id_cond $source_cond and b.status_active=1 group by a.po_break_down_id,a.item_number_id,a.ex_factory_date,a.challan_no,a.country_id order by a.ex_factory_date ";
						// and a.item_number_id=$item_number_id

						}

					}

						//print_r($challan_id);

						// echo $sql_qnty;
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
                                    <?php
			                        if($type != 1) {
									?>
                                    <td width="100" align="right"><? echo number_format($row_real[csf("ex_factory_qnty")]-$row_real[csf("ex_factory_ret_qnty")],2); ?>&nbsp;</td>
                                    <?php
                                    	} else {
                                    ?>
                                    	<td width="100" align="right"><? echo number_format($row_real[csf("ex_factory_qnty")],2); ?>&nbsp;</td>
                                    <?php
                                    	}
                                    ?>

									<?php
			                        	// if($type != 1) {
									?>
                                    <td width="" align="right"><? echo number_format($row_real[csf("ex_factory_ret_qnty")],2); ?>&nbsp;</td>
                                    <?php
                                    	// }
                                    ?>
								</tr>
							<?
							$total_ex_qnty+=$row_real[csf("ex_factory_qnty")];
							$total_return_ex_qnty+=$row_real[csf("ex_factory_ret_qnty")];
							$i++;
						}
                    ?>
                </tbody>
                <tfoot>
                	<tr>
                    	<th colspan="4" align="right"><strong>Total :</strong></th>
                        <th align="right"><? echo number_format($total_ex_qnty,2); ?></th>
                        <?php
                        	// if($type != 1) {
						?>
                        <th align="right"><? echo number_format($total_return_ex_qnty,2); ?> </th>
                        <?php
                        	// }
                        ?>
                    </tr>
                    <tr>
                    	<th colspan="4" align="right"><strong>Total Balance:</strong></th>
                        <!-- <th align="right" colspan="<?php echo $type == 1 ? 1 : 2; ?>">
                        	<?php
	                        	if($type != 1) {
	                        		echo number_format($total_ex_qnty-$total_return_ex_qnty,2);
                            	} else {
                            		echo number_format($total_ex_qnty,2);
                            	}
                            ?>
                        </th> -->
                        <th align="right"><? echo number_format($total_ex_qnty-$total_return_ex_qnty,2);?></th>
                        <th align="right"><? //echo number_format($total_return_ex_qnty,2);?></th>

                    </tr>
                </tfoot>
            </table>
        </fieldset>
    </div>
    <?
}

if($action=="ex_date_country_popup")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$ex_factory_date=str_replace("'","",$ex_factory_date);
	$ex_factory_date_ref=explode("_",$ex_factory_date);
	$exfact_date=explode("*",$ex_factory_date_ref[0]);
	//echo $challan_id.'d';
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


						if($challan_id!=""){$challan_id_cond= "and a.country_id=$challan_id"; }else{$challan_id_cond= "";}
						$sql_res=sql_select("SELECT b.po_break_down_id as po_id,  b.item_number_id,
							sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as return_qnty
							from  pro_ex_factory_mst b  where  b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id,b.item_number_id");
						$ex_factory_qty_arr=array();
						foreach($sql_res as $row)
						{

							$ex_factory_qty_arr[$row[csf('po_id')]][$row[csf('item_number_id')]]['return_qty']=$row[csf('return_qnty')];
						}

						$i=1;
						/*if($ex_factory_date_ref[1]==2)
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

							from pro_ex_factory_mst a ,pro_ex_factory_delivery_mst b where a.delivery_mst_id=b.id and a.po_break_down_id=$order_id and a.item_number_id=$item_number_id and a.status_active=1 and a.is_deleted=0 and a.ex_factory_date between  '$ex_factory_date' and '$ex_factory_date' $challan_id_cond $source_cond and b.status_active=1 group by a.po_break_down_id,a.item_number_id,a.ex_factory_date,a.challan_no,a.country_id order by a.ex_factory_date ";


						}*/

					if($exfact_date[0]!="")
					{
						$date_cond=" and a.ex_factory_date between  '$exfact_date[0]' and '$exfact_date[1]'";
					}
					else $date_cond="";

					 $sql_qnty="SELECT a.po_break_down_id,a.item_number_id,max(a.ex_factory_date) as ex_factory_date,a.challan_no,a.country_id,

							sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty

							from pro_ex_factory_mst a ,pro_ex_factory_delivery_mst b where a.delivery_mst_id=b.id and a.po_break_down_id=$order_id and a.item_number_id in($item_number_id) and a.status_active=1 and a.is_deleted=0  $challan_id_cond $source_cond and b.status_active=1 group by a.po_break_down_id,a.item_number_id,a.challan_no,a.country_id order by a.challan_no ";

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
