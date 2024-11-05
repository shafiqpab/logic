<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
function pre($array){
	echo '<pre>';
	print_r($array);
	echo '</pre>';
}
require_once('../../../includes/common.php');
$user_id = $_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$actions = explode("__",$_REQUEST['actions']);

require_once("../../../ext_resource/excel/excel/vendor/autoload.php");
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

//--------------------------------------------------------------------------------------------------------------------
if($action=="print_button_variable_setting")
{

    $print_report_format_arr = return_library_array("select format_id,format_id from lib_report_template where template_name in($data) and module_id=7 and report_id=81 and is_deleted=0 and status_active=1","format_id","format_id");
    echo "print_report_button_setting('".implode(',',$print_report_format_arr)."');\n";
    exit();
}

if ($actions[0]=="load_drop_down_location" || $actions[1]=="load_drop_down_floor")
{
    extract($_REQUEST);
    $choosenCompany = $choosenCompany;
	echo create_drop_down( "cbo_location", 110, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id in($choosenCompany) order by location_name","id,location_name", 0, "-- Select --", $selected, "load_drop_down( 'requires/date_wise_prod_without_cm_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/date_wise_prod_without_cm_report_controller' );",0 )."**".create_drop_down( "cbo_floor", 110, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id in($choosenCompany) order by floor_name","id,floor_name", 1, "-- Select --", $selected, "",0 );
	exit();
}

if ($action=="load_drop_down_floor")
{
	extract($_REQUEST);
    $choosenLocation = $choosenLocation;
	echo create_drop_down( "cbo_floor", 110, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id in($choosenLocation) order by floor_name","id,floor_name", 0, "-- Select --", $selected, "",0 );
	exit();
}

if ($action == "eval_multi_select") {
    echo "set_multiselect('cbo_floor','0','0','','0');\n";
    exit();
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 110, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/date_wise_prod_without_cm_report_controller', this.value, 'load_drop_down_season', 'season_td');load_drop_down( 'requires/date_wise_prod_without_cm_report_controller', this.value, 'load_drop_down_brand', 'brand_td');" );
	exit();
}


if ($action=="load_drop_down_season")
{
	echo create_drop_down( "cbo_season", 110, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-Season-", "", "" );
	exit();
}
if ($action=="load_drop_down_brand")
{
	// echo create_drop_down( "cbo_brand", 110, "select id, brand_name from lib_buyer_brand where buyer_id='$data' and status_active =1 and is_deleted=0 $userbrand_idCond order by brand_name ASC","id,brand_name", 1, "--Select--", "", "" );
	echo create_drop_down( "cbo_brand", 110, "select id, brand_name from lib_buyer_brand brand where buyer_id='$data' and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC","id,brand_name", 1, "--Brand--", $selected, "" );
	exit();
}

if($action=="report_generate")
{
	ob_start();
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$company_short_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name"  );
	$supplier_arr=return_library_array( "select id,supplier_name from  lib_supplier", "id", "supplier_name");
 	$buyer_short_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name"  );
 	$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  );
	$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  );

	$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );


	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$color_library_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$buyer_brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand",'id','brand_name');
	$buyer_season_arr=return_library_array( "select id, season_name from  lib_buyer_season",'id','season_name');
	$type = str_replace("'","",$cbo_type);
	$cbo_com_fac_name= str_replace("'","",$cbo_com_fac_name);
	$lc_comp=str_replace("'", "", $cbo_company_name);

	if($lc_comp)$subcon_lc_comp=" and a.company_id in ($lc_comp) ";
	if($cbo_com_fac_name)$subcon_work_comp=" and a.company_id in ($cbo_com_fac_name) ";

	$ReportType=str_replace("'","",$ReportType);
	if($type==1)
	{
		$report_name="Date Wise Production Report";
		$colSpan="36";
		$div_width="3140";
	}
	else if($type==2)
	{
		$report_name="Date, Location, Floor & Line Wise Production Report";
		$colSpan="39";
		$div_width="3700";
	}

	if($ReportType==1) // show button
	{
		$garments_nature=str_replace("'","",$cbo_garments_nature);
		$cbo_floor=str_replace("'","",$cbo_floor);
		//echo $cbo_floor;
		//if($garments_nature==1)$garments_nature="";
		 if($garments_nature==1) $garmentsNature=""; else $garmentsNature=" and garments_nature=$garments_nature";

		$location = str_replace("'","",$cbo_location);
		if(str_replace("'","",$cbo_company_name)==0) $company_name=""; else $company_name=" and b.company_name=$cbo_company_name";
		if(str_replace("'","",$cbo_buyer_name)==0)$buyer_name="";else $buyer_name=" and a.buyer_name=$cbo_buyer_name";
		if($cbo_floor==0) $floor_name="";else $floor_name=" and floor_id in($cbo_floor)";
		//echo $floor_name;die;
		if($cbo_floor==0) $floor_id="";else $floor_id=" and floor_id in($cbo_floor)";
		if ($location==0) $location_cond=""; else $location_cond=" and location in($location) ";

		if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="")$txt_date="";
		else $txt_date=" and production_date between $txt_date_from and $txt_date_to";

		$fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
		$toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );
		//cbo_garments_nature
		$file_no = str_replace("'","",$txt_file_no);
		$internal_ref = str_replace("'","",$txt_internal_ref);
		$cbo_company_name = str_replace("'","",$cbo_company_name);
		$cbo_com_fac_name = str_replace("'","",$cbo_com_fac_name);
		$cbo_buyer_name = str_replace("'","",$cbo_buyer_name);
		if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='".trim($file_no)."' ";
		if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='".trim($internal_ref)."' ";
		if ($location=="" || $location==0 ) $location_cond=""; else $location_cond=" and location in($location) ";
		if($cbo_com_fac_name=="") $working_factory_cond=""; else $working_factory_cond=" and serving_company in($cbo_com_fac_name)";
		// echo $working_factory_cond;
		if(str_replace("'","",$cbo_company_name)==0) $company_name_cond=""; else $company_name_cond=" and company_id=$cbo_company_name";

		$variable_setting = sql_select("select EX_FACTORY from variable_settings_production where company_name=$cbo_company_name and variable_list=1 and status_active=1");

		$exfactory_level = $variable_setting[0]["EX_FACTORY"];

        $job_arr=array();
        $job_arr2=array();
        if(str_replace("'","",$cbo_company_name)==0) $job_comp_cond=""; else $job_comp_cond="and a.company_name=$cbo_company_name ";
		$style_ref = str_replace("'","",$txt_style_ref);
		if ($style_ref=="") $style_ref_cond=""; else $style_ref_cond=" and a.style_ref_no='".trim($style_ref)."' ";
        /*==========================================================================================/
        /										main query											/
        /========================================================================================= */
		$job_sql="SELECT a.job_no_prefix_num, a.buyer_name, a.style_ref_no, a.company_name, b.id, b.po_number, b.grouping, b.file_no, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b, pro_garments_production_mst c where a.id=b.job_id and b.id=c.po_break_down_id and a.is_deleted=0 and c.is_deleted=0 and c.status_active=1 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $job_comp_cond $buyer_name $file_no_cond $internal_ref_cond $style_ref_cond $txt_date";

		// echo $job_sql;

		//and c.production_date between $txt_date_from and $txt_date_to

        $job_sql_res=sql_select($job_sql);

        $tot_rows=0;
        $poIds='';
        $poIds_array = array();
        foreach($job_sql_res as $row)
        {
            $tot_rows++;
            $poIds.=$row[csf("id")].",";
            $poIds_array[$row[csf("id")]]=$row[csf("id")];
            $job_arr[$row[csf("id")]]['job']=$row[csf("job_no_prefix_num")];
            $job_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
            $job_arr[$row[csf("id")]]['buyer']=$row[csf("buyer_name")];
            $job_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
            $job_arr[$row[csf("id")]]['file']=$row[csf("file_no")];
            $job_arr[$row[csf("id")]]['ref']=$row[csf("grouping")];
            $job_arr2[$row[csf("id")]]['pub_shipment_date']=$row[csf("pub_shipment_date")];
        }
        unset($job_sql_res);

        $txt_date_ex = str_replace("production_date", "ex_factory_date", $txt_date);
        $job_sql_ex="SELECT a.job_no_prefix_num, a.buyer_name, a.style_ref_no, a.company_name, b.id, b.po_number, b.grouping, b.file_no, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c where a.id=b.job_id and b.id=c.po_break_down_id and a.is_deleted=0 and c.is_deleted=0 and c.status_active=1 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $job_comp_cond $buyer_name $file_no_cond $internal_ref_cond $style_ref_cond $txt_date_ex";

		// echo $job_sql_ex;

        $job_sql_res_ex=sql_select($job_sql_ex);

        foreach($job_sql_res_ex as $row)
        {
        	$poIds_array[$row[csf("id")]]=$row[csf("id")];
            $job_arr[$row[csf("id")]]['job']=$row[csf("job_no_prefix_num")];
            $job_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
            $job_arr[$row[csf("id")]]['buyer']=$row[csf("buyer_name")];
            $job_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
            $job_arr[$row[csf("id")]]['file']=$row[csf("file_no")];
            $job_arr[$row[csf("id")]]['ref']=$row[csf("grouping")];
            $job_arr2[$row[csf("id")]]['pub_shipment_date']=$row[csf("pub_shipment_date")];
        }
        unset($job_sql_res_ex);

        $poIds_cond="";

       	$poIds=implode(",", $poIds_array);
         if($db_type==2 && count($poIds_array)>=1000)
        {
            $poIds_cond=" and (";
            $poIdsArr=array_chunk($poIds_array,999);
            foreach($poIdsArr as $ids)
            {
                $ids=implode(",",$ids);
                $poIds_cond.=" po_break_down_id in ($ids) or ";
            }
            $poIds_cond=chop($poIds_cond,'or ');
            $poIds_cond.=")";
        }
        else
        {
            $poIds_cond=" and po_break_down_id in ($poIds)";
        }

		// ============================== data store to gbl table ==================================
		$con = connect();
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from=1 and ENTRY_FORM=41");
		oci_commit($con);

		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 41, 1, $poIds_array, $empty_arr);//PO ID
		// ============================== end data store to gbl table ==================================


        $buyer_fullQty_arr=array();
        $prod_date_qty_arr=array();
        $prod_dlfl_qty_arr=array();
		$all_data_arr=array();
		$all_data_arrr=array();
        /*==========================================================================================/
        /										production data										/
        /========================================================================================= */
        $poIds_conds=str_replace("po_break_down_id", "a.po_break_down_id ", $poIds_cond);
        $sql_dtls="SELECT a.location, a.company_id, a.serving_company, a.floor_id, a.sewing_line, a.po_break_down_id,a.re_production_qty, a.production_date, a.item_number_id, a.production_type, a.production_source, a.embel_name, (a.prod_reso_allo) as prod_reso_allo, (b.production_qnty) as production_quantity,  (b.reject_qty) as reject_qnty, a.carton_qty as carton_qty
        from pro_garments_production_mst a ,pro_garments_production_dtls b ,wo_po_color_size_breakdown c,GBL_TEMP_ENGINE tmp
        where a.id=b.mst_id and b.color_size_break_down_id=c.id and c.po_break_down_id=a.po_break_down_id and c.po_break_down_id=tmp.ref_val and tmp.entry_form=41 and tmp.ref_from=1 and tmp.user_id = $user_id  and c.status_active in(1,2,3) and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  a.is_deleted=0 and a.status_active=1 $company_name_cond $working_factory_cond $txt_date $floor_name $location_cond $garmentsNature order by a.production_date ASC";

		// echo $sql_dtls;

		$sql_dtls_res=sql_select($sql_dtls);


		$w_company_cond='';
		if(!empty($cbo_com_fac_name))
		{
			$w_company_cond=" and a.company_id in($cbo_com_fac_name)";
			$delivery_company_cond=" and d.delivery_company_id in($cbo_com_fac_name)";
		}
		$company_cond='';
		if(!empty($cbo_company_name))
		{
			$company_cond=" and c.lc_company_id=$cbo_company_name";
		}
		$buyer_cond='';
		if(!empty($cbo_buyer_name))
		{
			$buyer_cond=" and d.buyer_name=$cbo_buyer_name";
		}
		$file_no_cond='';
		if(!empty($file_no))
		{
			$file_no_cond=" and b.file_no=$file_no";
		}
		$internal_ref_cond='';
		$internal_ref=str_replace("'", "", $internal_ref);
		if(!empty($internal_ref))
		{
			$internal_ref_cond=" and b.grouping='$internal_ref'";
		}
		$location_cond='';
		if(!empty($location))
		{
			$location_cond=" and a.fini_location_id=$location";
		}
		$floor_cond='';
		if(!empty($cbo_floor))
		{
			$floor_cond=" and a.floor_id=$cbo_floor";
		}


		$poIds_conds=str_replace("a.po_break_down_id", "b.po_break_down_id ", $poIds_conds);
		$txt_rcv_date = str_replace("production_date", "a.receive_date", $txt_date);
		if ($style_ref=="") $style_ref_cond2=""; else $style_ref_cond2=" and d.style_ref_no='".trim($style_ref)."' ";
        $fin_rcv_sql=" SELECT c.po_break_down_id, (c.fin_receive_qnty) as qnty,
					         a.receive_date,d.job_no_prefix_num as job_no,d.buyer_name,b.shipment_date,b.file_no,
					         b.grouping,d.style_ref_no,d.gmts_item_id,b.po_number,a.company_id,c.item_id
					    FROM gmt_finishing_receive_mst a, gmt_finishing_receive_dtls c,wo_po_break_down b,wo_po_details_master d
					   WHERE     a.id = c.mst_id
					         and b.id=c.po_break_down_id
					         and d.id=b.job_id
					         and B.STATUS_ACTIVE=1
					         and b.is_deleted=0
					         and d.is_deleted=0
					         AND a.status_active = 1
					         AND a.is_deleted = 0
					         AND c.status_active = 1
					         AND c.is_deleted = 0
					        $w_company_cond
					        $company_cond
					        $buyer_cond
					        $txt_rcv_date
					        $location_cond
					        $floor_cond
					        $file_no_cond
					        $internal_ref_cond $style_ref_cond2";
		//echo $fin_rcv_sql; exit();
		$fin_rcv_res= sql_select( $fin_rcv_sql);
        $order_wise_fin_rec_qnty=array();
        foreach ($fin_rcv_res as $row)
        {
        	$order_wise_fin_rec_qnty[$row[csf('po_break_down_id')]][$row[csf('receive_date')]][$row[csf('item_id')]]['qnty']+=$row[csf('qnty')];
        	$order_wise_fin_rec_qnty[$row[csf('po_break_down_id')]][$row[csf('receive_date')]][$row[csf('item_id')]]['job_no']=$row[csf('job_no')];
        	$order_wise_fin_rec_qnty[$row[csf('po_break_down_id')]][$row[csf('receive_date')]][$row[csf('item_id')]]['buyer_name']=$row[csf('buyer_name')];
        	$order_wise_fin_rec_qnty[$row[csf('po_break_down_id')]][$row[csf('receive_date')]][$row[csf('item_id')]]['shipment_date']=$row[csf('shipment_date')];
        	$order_wise_fin_rec_qnty[$row[csf('po_break_down_id')]][$row[csf('receive_date')]][$row[csf('item_id')]]['file_no']=$row[csf('file_no')];
        	$order_wise_fin_rec_qnty[$row[csf('po_break_down_id')]][$row[csf('receive_date')]][$row[csf('item_id')]]['grouping']=$row[csf('grouping')];
        	$order_wise_fin_rec_qnty[$row[csf('po_break_down_id')]][$row[csf('receive_date')]][$row[csf('item_id')]]['style_ref_no']=$row[csf('style_ref_no')];
        	$order_wise_fin_rec_qnty[$row[csf('po_break_down_id')]][$row[csf('receive_date')]][$row[csf('item_id')]]['gmts_item_id']=$row[csf('gmts_item_id')];
        	$order_wise_fin_rec_qnty[$row[csf('po_break_down_id')]][$row[csf('receive_date')]][$row[csf('item_id')]]['po_number']=$row[csf('po_number')];
        	$order_wise_fin_rec_qnty[$row[csf('po_break_down_id')]][$row[csf('receive_date')]][$row[csf('item_id')]]['company_id']=$row[csf('company_id')];




        }




		// var_dump($sql_dtls_res);
        // echo $sql_dtls;  die;//$poIds_cond
		if( count($sql_dtls_res) > 0)
		{

			$poIds_cond3=str_replace("po_break_down_id","b.id", $poIds_cond);
			$po_wise_unit_price_sql="SELECT a.buyer_name,b.id,b.unit_price from wo_po_details_master a, wo_po_break_down b,GBL_TEMP_ENGINE tmp where a.id=b.job_id and b.id=tmp.ref_val and tmp.entry_form=41 and tmp.ref_from=1 and tmp.user_id = $user_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			foreach( sql_select($po_wise_unit_price_sql) as $key=>$val)
			{
				$po_wise_unit_price[$val[csf("id")]]=$val[csf("unit_price")];
				$buyer_wise_unit_price[$val[csf("buyer_name")]]+=$val[csf("unit_price")];
			}

		}
		/*==========================================================================================/
        /										shipment data										/
        /========================================================================================= */
        $txt_date2=str_replace("production_date", "e.ex_factory_date", $txt_date);
        if($exfactory_level==1) // gross level
		{
			$sql_ex="SELECT a.buyer_name,b.unit_price,d.delivery_location_id as location, d.company_id, d.delivery_company_id, d.delivery_floor_id as floor_id, e.po_break_down_id, e.ex_factory_date, e.item_number_id, (e.ex_factory_qnty) as ex_factory_qnty
        	from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c, pro_ex_factory_delivery_mst d ,pro_ex_factory_mst e
        	where d.id=e.delivery_mst_id and a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and b.id=e.po_break_down_id and e.status_active=1 and e.is_deleted=0 and  d.is_deleted=0 and d.status_active=1 $job_comp_cond $delivery_company_cond $buyer_name $file_no_cond $internal_ref_cond $style_ref_cond $txt_date2
        	order by e.ex_factory_date ASC";
		}
		else // color or color and size level
		{
        	$sql_ex="SELECT a.buyer_name,b.unit_price, d.delivery_location_id as location, d.company_id, d.delivery_company_id, d.delivery_floor_id as floor_id, e.po_break_down_id, e.ex_factory_date, e.item_number_id, (f.production_qnty) as ex_factory_qnty
        	from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c, pro_ex_factory_delivery_mst d ,pro_ex_factory_mst e ,pro_ex_factory_dtls f
        	where d.id=e.delivery_mst_id and e.id=f.mst_id and a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and b.id=e.po_break_down_id and f.status_active in(1) and f.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and  d.is_deleted=0 and d.status_active=1 $job_comp_cond $delivery_company_cond $buyer_name $file_no_cond $internal_ref_cond $style_ref_cond $txt_date2 and f.color_size_break_down_id=c.id order by e.ex_factory_date ASC";
        }
        // echo $sql_ex;
        $ex_res=sql_select($sql_ex);

		/*==========================================================================================/
        /										carton qty											/
        /========================================================================================= */
		// GETTING CARTON QNTY
		$sql_carton="SELECT a.location,a.floor_id,a.sewing_line, a.po_break_down_id, a.production_date, a.item_number_id,a.serving_company,sum(a.carton_qty) as carton_qty
    	from pro_garments_production_mst a,GBL_TEMP_ENGINE tmp
    	where a.po_break_down_id=tmp.ref_val and tmp.entry_form=41 and tmp.ref_from=1 and tmp.user_id = $user_id and a.is_deleted=0 and a.status_active=1 $company_name_cond $working_factory_cond $txt_date $floor_name $location_cond $garmentsNature 
    	group by a.location,a.floor_id,a.sewing_line,a.po_break_down_id, a.production_date, a.item_number_id,a.serving_company order by a.production_date ASC";
    	// echo $sql_carton;
    	$sql_carton_res = sql_select($sql_carton);
    	if($type==1)
		{
			foreach ($sql_carton_res as $row)
			{
				$prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("serving_company")]][$row[csf("production_date")]][$row[csf("item_number_id")]]['0']['0']['crtQty']+=$row[csf("carton_qty")];
			}
		}
		else // type 2
		{
			foreach ($sql_carton_res as $row)
			{
				$prod_dlfl_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("location")]][$row[csf("floor_id")]]['0']['0']['0']['crtQty']+=$row[csf("carton_qty")];
			}

		}
		// ================== for production data ======================
		$buyer_po_date_wise_gmts_data_arr = array();
		$check_fin_rcv = array();
        if($type==1)
        {
            foreach($sql_dtls_res as $row)
            {
                // var_dump($row);
                $buyer_name_dat=""; $job_no=''; $po_no=''; $buyer_id=''; $style_ref=''; $ref_no=''; $file_no='';
                $buyer_name_dat=$job_arr[$row[csf("po_break_down_id")]]['buyer'];
                $job_no=$job_arr[$row[csf("po_break_down_id")]]['job'];
                $po_no=$job_arr[$row[csf("po_break_down_id")]]['po'];
                $style_ref=$job_arr[$row[csf("po_break_down_id")]]['style'];
                $ref_no=$job_arr[$row[csf("po_break_down_id")]]['ref'];
                $file_no=$job_arr[$row[csf("po_break_down_id")]]['file'];

                //Buyer Wise Summary array start
                $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]]['0']['pQty']+=$row[csf("production_quantity")];
                $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("embel_name")]]['embQty']+=$row[csf("production_quantity")];
                $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("production_source")]]['sQty']+=$row[csf("production_quantity")];
                $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]]['0']['reQty']+=$row[csf("re_production_qty")];
                $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("production_source")]]['rejectQty']+=$row[csf("reject_qnty")];



             //     $arr_bind=$row[csf("po_break_down_id")]."***".$row[csf("production_date")]."***".[$row[csf("item_number_id")]];
             //    if(!in_array($arr_bind, $check_fin_rcv))
             //    {
	            //     $buyer_fullQty_arr[$buyer_name_dat][0]['0']['fin_rcv_qnty']+=$order_wise_fin_rec_qnty[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]]['qnty'];
	            //     array_push($check_fin_rcv, $arr_bind);
	            // }

                $buyer_po_date_wise_gmts_data_arr[$buyer_name_dat][$row[csf("po_break_down_id")]][$row[csf("production_type")]] += $row[csf("production_quantity")];

                //Details array start
                $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("production_type")]]['0']['pQty']+=$row[csf("production_quantity")];

                //for serving company
                $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("serving_company")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("production_type")]]['0']['pQty']+=$row[csf("production_quantity")];
                $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("serving_company")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("production_type")]][$row[csf("production_source")]]['sQty']+=$row[csf("production_quantity")];
                $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("serving_company")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("production_type")]]['0']['rejectQty']+=$row[csf("reject_qnty")];
                $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("serving_company")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("production_type")]]['0']['reQty']+=$row[csf("re_production_qty")];
                // $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("serving_company")]][$row[csf("production_date")]][$row[csf("item_number_id")]]['0']['0']['crtQty']+=$row[csf("carton_qty")];
                $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("serving_company")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("production_type")]][$row[csf("embel_name")]]['embQty']+=$row[csf("production_quantity")];


                $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("production_type")]][$row[csf("embel_name")]]['embQty']+=$row[csf("production_quantity")];
                $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("production_type")]][$row[csf("production_source")]]['sQty']+=$row[csf("production_quantity")];
                $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("production_type")]]['0']['reQty']+=$row[csf("re_production_qty")];
                $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("production_type")]]['0']['rejectQty']+=$row[csf("reject_qnty")];
                // $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]]['0']['0']['crtQty']+=$row[csf("carton_qty")];
                if($row[csf("production_source")]==0) $row[csf("production_source")]=1;
                $all_data_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]].=$row[csf("item_number_id")].'**'.$buyer_name_dat.'**'.$job_no.'**'.$po_no.'**'.$style_ref.'**'.$ref_no.'**'.$file_no.'**'.$row[csf("company_id")].'**'.$row[csf("production_source")].'**'.$row[csf("serving_company")].'__';//.'**'.$row[csf("floor_id")]
                // echo $row[csf("carton_qty")];
            }
        }
        else if($type==2)
        {
            foreach($sql_dtls_res as $row)
            {
				// var_dump($row);
                $buyer_name_dat=""; $job_no=''; $po_no=''; $buyer_id=''; $style_ref=''; $ref_no=''; $file_no='';
                $buyer_name_dat=$job_arr[$row[csf("po_break_down_id")]]['buyer'];
                $job_no=$job_arr[$row[csf("po_break_down_id")]]['job'];
                $po_no=$job_arr[$row[csf("po_break_down_id")]]['po'];
                $style_ref=$job_arr[$row[csf("po_break_down_id")]]['style'];
                $ref_no=$job_arr[$row[csf("po_break_down_id")]]['ref'];
                $file_no=$job_arr[$row[csf("po_break_down_id")]]['file'];
                //Buyer Wise Summary array start
                $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]]['0']['pQty']+=$row[csf("production_quantity")];
                $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("embel_name")]]['embQty']+=$row[csf("production_quantity")];
                $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("production_source")]]['sQty']+=$row[csf("production_quantity")];
                $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]]['0']['reQty']+=$row[csf("re_production_qty")];
                $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("production_source")]]['rejectQty']+=$row[csf("reject_qnty")];



             //    $arr_bind=$row[csf("po_break_down_id")]."***".$row[csf("production_date")]."***".$row[csf("item_number_id")];
             //    if(!in_array($arr_bind, $check_fin_rcv))
             //    {
	            //      $buyer_fullQty_arr[$buyer_name_dat][0]['0']['fin_rcv_qnty']+=$order_wise_fin_rec_qnty[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]]['qnty'];
	            //     array_push($check_fin_rcv, $arr_bind);
	            // }

                $buyer_po_date_wise_gmts_data_arr[$buyer_name_dat][$row[csf("po_break_down_id")]][$row[csf("production_type")]] += $row[csf("production_quantity")];

                //Details array start
                if($row[csf("sewing_line")]=="") $row[csf("sewing_line")]=0;

                if($row[csf("floor_id")]=="") $row[csf("floor_id")]=0;

                if($row[csf("location")]=="") $row[csf("location")]=0;

                $prod_dlfl_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]][$row[csf("production_type")]]['0']['pQty']+=$row[csf("production_quantity")];
                $prod_dlfl_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]][$row[csf("production_type")]][$row[csf("embel_name")]]['embQty']+=$row[csf("production_quantity")];
                $prod_dlfl_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]][$row[csf("production_type")]][$row[csf("production_source")]]['sQty']+=$row[csf("production_quantity")];
                $prod_dlfl_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]][$row[csf("production_type")]]['0']['reQty']+=$row[csf("re_production_qty")];
                $prod_dlfl_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]][$row[csf("production_type")]]['0']['rejectQty']+=$row[csf("reject_qnty")];
                // $prod_dlfl_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]]['0']['0']['crtQty']+=$row[csf("carton_qty")];
                if($row[csf("production_source")]==0) $row[csf("production_source")]=1;
                if($row[csf("production_source")]==3)
                {
                	$all_data_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][0][0].= $row[csf("sewing_line")].'##'.$buyer_name_dat.'##'.$job_no.'##'.$po_no.'##'.$style_ref.'##'.$ref_no.'##'.$file_no.'##'.$row[csf("production_source")].'##0##'.$row[csf("serving_company")].'__';
                }
                else
                {
                	$all_data_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("location")]][$row[csf("floor_id")]].= $row[csf("sewing_line")].'##'.$buyer_name_dat.'##'.$job_no.'##'.$po_no.'##'.$style_ref.'##'.$ref_no.'##'.$file_no.'##'.$row[csf("production_source")].'##'.$row[csf("prod_reso_allo")].'##'.$row[csf("serving_company")].'__';
                }
            }
        }
        // echo "<br>"; print_r($prod_date_qty_arr); die;
        unset($sql_dtls_res);
        // unset($job_arr);

        // ============================ for shipment data ========================
        $ex_fac_arr_buyerwise = array();
        $prod_date_qty_arr_ex = array();
        $prod_date_qty_arr_ex2 = array();
        $po_id_arr = array();
        if($type==1)
        {
            foreach($ex_res as $row)
            {

                $buyer_name_dat=""; $job_no=''; $po_no=''; $buyer_id=''; $style_ref=''; $ref_no=''; $file_no='';
                $buyer_name_dat=$job_arr[$row[csf("po_break_down_id")]]['buyer'];
                $job_no=$job_arr[$row[csf("po_break_down_id")]]['job'];
                $po_no=$job_arr[$row[csf("po_break_down_id")]]['po'];
                $style_ref=$job_arr[$row[csf("po_break_down_id")]]['style'];
                $ref_no=$job_arr[$row[csf("po_break_down_id")]]['ref'];
                $file_no=$job_arr[$row[csf("po_break_down_id")]]['file'];

                $ex_fac_arr_po_datewise[$row[csf("po_break_down_id")]][$row[csf("ex_factory_date")]]+=$row[csf("ex_factory_qnty")];
				$ex_fac_arr_buyerwise[$buyer_name_dat]+=$row[csf("ex_factory_qnty")];

				$buyer_po_date_wise_gmts_data_arr[$buyer_name_dat][$row[csf("po_break_down_id")]][0] += $row[csf("ex_factory_qnty")];

                //Buyer Wise Summary array start
                // $ex_fac_arr_buyerwise[$buyer_name_dat]+=$row[csf("ex_factory_qnty")];
                $buyer_fullQty_arr[$buyer_name_dat][0]['0']['ex_factory_qnty']+=$row[csf("ex_factory_qnty")];

             //    $arr_bind=$row[csf("po_break_down_id")]."***".$row[csf("ex_factory_date")]."***".$row[csf("item_number_id")];
             //    if(!in_array($arr_bind, $check_fin_rcv))
             //    {
	            //     $buyer_fullQty_arr[$buyer_name_dat][0]['0']['fin_rcv_qnty']+=$order_wise_fin_rec_qnty[$row[csf("po_break_down_id")]][$row[csf("ex_factory_date")]][$row[csf("item_number_id")]]['qnty'];
	            //     array_push($check_fin_rcv, $arr_bind);
	            // }
                //Details array start
                // $prod_date_qty_arr_ex[$row[csf("po_break_down_id")]][$row[csf("ex_factory_date")]][$row[csf("item_number_id")]]['ex_factory_qnty']+=$row[csf("ex_factory_qnty")];

                //for serving company
                $prod_date_qty_arr_ex[$row[csf("po_break_down_id")]][$row[csf("delivery_company_id")]][$row[csf("ex_factory_date")]][$row[csf("item_number_id")]]['ex_factory_qnty']+=$row[csf("ex_factory_qnty")];

                $all_data_arr[$row[csf("po_break_down_id")]][$row[csf("ex_factory_date")]].=$row[csf("item_number_id")].'**'.$buyer_name_dat.'**'.$job_no.'**'.$po_no.'**'.$style_ref.'**'.$ref_no.'**'.$file_no.'**'.$row[csf("company_id")].'**1**'.$row[csf("delivery_company_id")].'__';//.'**'.$row[csf("floor_id")]
                array_push($po_id_arr, $row[csf("po_break_down_id")]);
            }
        }
        else if($type==2)
        {
            foreach($ex_res as $row)
            {
                $buyer_name_dat=""; $job_no=''; $po_no=''; $buyer_id=''; $style_ref=''; $ref_no=''; $file_no='';
                $buyer_name_dat=$job_arr[$row[csf("po_break_down_id")]]['buyer'];
                $job_no=$job_arr[$row[csf("po_break_down_id")]]['job'];
                $po_no=$job_arr[$row[csf("po_break_down_id")]]['po'];
                $style_ref=$job_arr[$row[csf("po_break_down_id")]]['style'];
                $ref_no=$job_arr[$row[csf("po_break_down_id")]]['ref'];
                $file_no=$job_arr[$row[csf("po_break_down_id")]]['file'];

                $ex_fac_arr_po_datewise[$row[csf("po_break_down_id")]][$row[csf("ex_factory_date")]]+=$row[csf("ex_factory_qnty")];
				$ex_fac_arr_buyerwise[$buyer_name_dat]+=$row[csf("ex_factory_qnty")];

				$buyer_po_date_wise_gmts_data_arr[$buyer_name_dat][$row[csf("po_break_down_id")]][0] += $row[csf("ex_factory_qnty")];

                //Buyer Wise Summary array start
                // $ex_fac_arr_buyerwise[$buyer_name_dat]+=$row[csf("ex_factory_qnty")];
                // $buyer_fullQty_arr[$buyer_name_dat][0]['0']['ex_factory_qnty']+=$row[csf("ex_factory_qnty")];
                // $arr_bind=$row[csf("po_break_down_id")]."***".$row[csf("ex_factory_date")]."***".$row[csf("item_number_id")];
                // if(!in_array($arr_bind, $check_fin_rcv))
                // {
                // 	$buyer_fullQty_arr[$buyer_name_dat][0]['0']['fin_rcv_qnty']+=$order_wise_fin_rec_qnty[$row[csf("po_break_down_id")]][$row[csf("ex_factory_date")]][$row[csf("item_number_id")]]['qnty'];
                // 	array_push($check_fin_rcv, $arr_bind);
                // }

                //Details array start

                $prod_date_qty_arr_ex2[$row[csf("po_break_down_id")]][$row[csf("ex_factory_date")]][$row[csf("item_number_id")]][$row[csf("delivery_location_id")]][$row[csf("delivery_floor_id")]]['ex_factory_qnty']+=$row[csf("ex_factory_qnty")];

                $all_data_arr[$row[csf("po_break_down_id")]][$row[csf("ex_factory_date")]][$row[csf("item_number_id")]][$row[csf("delivery_location_id")]][$row[csf("delivery_floor_id")]].='0##'.$buyer_name_dat.'##'.$job_no.'##'.$po_no.'##'.$style_ref.'##'.$ref_no.'##'.$file_no.'##1##0##'.$row[csf("delivery_company_id")].'__';
                array_push($po_id_arr, $row[csf("po_break_down_id")]);
            }
        }

        // echo "<pre>";print_r($all_data_arr);die();
        unset($ex_res);
        unset($job_arr);
        $b=1; //date_wise Summary

        //$rcv_data_need_to_show=array();
        foreach ($order_wise_fin_rec_qnty as $po_id=> $po_wise_data)
        {

        	foreach ($po_wise_data as $receive_date => $rcv_date_wise_data)
        	{
        		foreach ($rcv_date_wise_data as $item_id => $item_data)
        		{
        			$buyer_fullQty_arr[$item_data['buyer_name']][0]['0']['fin_rcv_qnty']+=$item_data['qnty'];
        		}
        	}
        }





        foreach ($buyer_po_date_wise_gmts_data_arr as $b_key => $b_value)
        {
        	foreach ($b_value as $po_key => $po_value)
        	{
        		$sewing_output_total = $po_value[5];
        		$ex_fact = $po_value[0];

                $ex_fact_fob=$ex_fact*$po_wise_unit_price[$po_key];

                $ex_fact_bal= $sewing_output_total-$ex_fact;
                $ex_fact_bal_fob= $ex_fact_bal*$po_wise_unit_price[$po_key];
                $ex_fac_arr_buyerwise[$b_key] += $ex_fact;
                $ex_fac_fob_arr_buyerwise[$b_key]+=$ex_fact_fob;
                $ex_fac_bal_arr_buyerwise[$b_key]+=$ex_fact_bal;
                $ex_fac_bal_fob_arr_buyerwise[$b_key]+=$ex_fact_bal_fob;
        	}
        }

       /* foreach($all_data_arr as $po_id=>$po_data)
        {
            foreach($po_data as $prod_date=>$prod_date_data)
            {
                $ex_itemdata='';
                $ex_itemdata=array_filter(array_unique(explode('__',$prod_date_data)));
                foreach($ex_itemdata as $data_all)
                {
                    $buyer_name=''; $item_id='';
                    $ex_data=array_filter(explode('**',$data_all));
                    if($ex_data[1] !="")
                    {
                        $buyer_name=$ex_data[1];
                        $item_id=$ex_data[0];
                        $sewOut_inQty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['5']['1']['sQty'];
                        $sewOut_outQty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['5']['3']['sQty'];

                        // $sewing_output_total=$sewOut_inQty+$sewOut_outQty;

                        $sewing_output_total = $date_po_wise_gmts_data_arr[$po_id][$prod_date][5];

                        $ex_fact = 0;
                        $ex_fact=$ex_fac_arr_po_datewise[$po_id][$prod_date];
                        $ex_fact_fob=$ex_fact*$po_wise_unit_price[$po_id];
                        $ex_fact_bal= $sewing_output_total-$ex_fact;
                        echo "$sewing_output_total-$ex_fact<br>";
                        $ex_fact_bal_fob= $ex_fact_bal*$po_wise_unit_price[$po_id];
                        $ex_fac_arr_buyerwise[$buyer_name] += $ex_fact;
                        $ex_fac_fob_arr_buyerwise[$buyer_name]+=$ex_fact_fob;
                        $ex_fac_bal_arr_buyerwise[$buyer_name]+=$ex_fact_bal;
                        $ex_fac_bal_fob_arr_buyerwise[$buyer_name]+=$ex_fact_bal_fob;


                    }
                }
            }
        }*/
		// print_r($ex_fac_arr_buyerwise);
		// ============================ delete GBL_TEMP_ENGINE data ====================================
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from=1 and ENTRY_FORM=41");
		oci_commit($con);
		disconnect($con);
		?>
       <div>
			<div style="width:<? echo $div_width; ?>px">
				<table width="<? echo $div_width; ?>" cellspacing="0">
					<tr class="form_caption" style="border:none;">
						<td colspan="<? echo $colSpan; ?>" align="center" style="border:none;font-size:14px; font-weight:bold" ><? //echo $report_name; ?></td>
					 </tr>
					<tr style="border:none;">
						<td colspan="<? echo $colSpan; ?>" align="center" style="border:none; font-size:16px; font-weight:bold">
							Company Name:
							<?
							echo $company_library[str_replace("'","",$cbo_company_name)];
							/*$workingComp="";
							$workingFactCom=explode(",", $cbo_com_fac_name);
							foreach($workingFactCom as $workingFactCompany)
							{
								$workingComp.=$company_library[$workingFactCompany].', ';
							}
							echo chop($workingComp,',');*/
							?>
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="<? echo $colSpan; ?>" align="center" style="border:none;font-size:12px; font-weight:bold">
							<? echo "From $fromDate To $toDate" ;?>
						</td>
					</tr>
				</table>
				<!-- ==========================================================================================/
		        /										summary part										   /
		        /=========================================================================================== -->
				<table width="2240" cellspacing="0" border="1" rules="all" align="left">
					<tr>
						<td width="1920" align="left" valign="top">
	                    <div style="width:300px; float:left; background-color:#FCF"><strong>Production-Regular Order Summary</strong></div>
	                    <br/>
						<div style="clear:both;">
							<table width="1950" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
								<thead>
									<tr>
										<th style="word-wrap: break-word;word-break: break-all;" width="30">Sl.</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Buyer Name</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Cut Quantity</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Sent to Print</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Received from Print</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Sent to Embroidery </th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Received from Embroidery</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Sent to Wash</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Rev Wash</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Sent to Sp. Works</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Rev Sp. Works</th>

										<th style="word-wrap: break-word;word-break: break-all;" width="80">Sewing Input</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Sew Input (Outbound)</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Sewing Output</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Sew Output (Outbound)</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Finishing Received</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Total Iron Production</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Total Re-Iron</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Total Poly (Inhouse)</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Total Poly (Outbond)</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Total Packing/ Finishing</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">EXF Quantity</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">EXF Value</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Ex-Fac. Bal. Qty</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Ex- Fac. Bal. FOB Value</th>
									</tr>
								</thead>
							</table>
                            <div style="overflow-y:scroll; max-height:225px; width:2240px" >
                                <table cellspacing="0" border="1" class="rpt_table"  width="1950" rules="all" id="" >
                                <?
								// echo "<pre>";
								// print_r($buyer_fullQty_arr);
								$tot_fin_rcv_qnty=0;
								$tot_buyer_fin_rcv_qnty=0;
                                foreach($buyer_fullQty_arr as $buyer_id=>$buyer_data)
                                {
                                    if($buyer_id!="")
                                    {
                                        if ($b%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                        $cutting_qty=$printing_qty=$printreceived_qty=$emb_qty=$embRec_qty=$wash_qty=$washRec_qty=$special_qty=$specialRec_qty=$sewIn_inQty=$sewIn_outQty=$sewOut_inQty=$sewOut_outQty=$iron_qty=$reIron_qty=$finish_qty=$polyIn_inQty=$polyOut_outQty=0;
                                        $cutting_qty=$buyer_data['1']['0']['pQty'];
                                        $printing_qty=$buyer_data['2']['1']['embQty'];
                                        $printreceived_qty=$buyer_data['3']['1']['embQty'];
                                        $emb_qty=$buyer_data['2']['2']['embQty'];
                                        $embRec_qty=$buyer_data['3']['2']['embQty'];
                                        $wash_qty=$buyer_data['2']['3']['embQty'];
                                        $washRec_qty=$buyer_data['3']['3']['embQty'];
                                        $special_qty=$buyer_data['2']['4']['embQty'];
                                        $specialRec_qty=$buyer_data['3']['4']['embQty'];
                                        $sewIn_inQty=$buyer_data['4']['0']['pQty'];
                                        $sewIn_outQty=$buyer_data['4']['3']['sQty'];
                                        $sewOut_inQty=$buyer_data['5']['0']['pQty'];
                                        $sewOut_outQty=$buyer_data['5']['3']['sQty'];
                                        $polyIn_inQty=$buyer_data['11']['1']['sQty'];
                                        $polyOut_outQty=$buyer_data['11']['3']['sQty'];
                                        $iron_qty=$buyer_data['7']['0']['pQty'];
                                        $reIron_qty=$buyer_data['7']['0']['reQty'];
                                        $finish_qty=$buyer_data['8']['0']['pQty'];
                                        // $ex_fact_buy=$ex_fac_arr_buyerwise[$buyer_id];
                                        $ex_fact_buy=$buyer_data[0]['0']['ex_factory_qnty'];
                                        $fin_rcv_qnty=$buyer_data[0]['0']['fin_rcv_qnty'];
                                        $ex_fact_buy_fob=$ex_fac_fob_arr_buyerwise[$buyer_id];
                                        $ex_fact_buy_bal=$ex_fac_bal_arr_buyerwise[$buyer_id];
                                        $ex_fact_buy_bal_fob=$ex_fac_bal_fob_arr_buyerwise[$buyer_id];

                                        ?>
                                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $b; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $b; ?>">
                                            <td style="word-wrap: break-word;word-break: break-all;" width="30"><? echo $b;?></td>
                                            <td style="word-wrap: break-word;word-break: break-all;" width="80"><p><? echo $buyer_short_library[$buyer_id]; ?>&nbsp;</p></td>
                                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($cutting_qty); ?></td>
                                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($printing_qty); ?></td>
                                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($printreceived_qty); ?></td>
                                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($emb_qty); ?></td>
                                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($embRec_qty); ?></td>
                                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($wash_qty); ?></td>
                                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($washRec_qty);  ?></td>
                                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($special_qty); ?></td>
                                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($specialRec_qty);  ?></td>
                                            <td  style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sewIn_inQty); ?></td>
                                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sewIn_outQty); ?></td>
                                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sewOut_inQty); ?></td>
                                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sewOut_outQty); ?></td>
                                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($fin_rcv_qnty); ?></td>
                                            <td  style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($iron_qty); ?></td>
                                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($reIron_qty); ?></td>
                                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($polyIn_inQty); ?></td>
                                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($polyOut_outQty); ?></td>
                                            <td width="80" style="word-wrap: break-word;word-break: break-all;"  align="right"><? echo number_format($finish_qty); ?></td>
                                            <td width="80" style="word-wrap: break-word;word-break: break-all;"  align="right"><? echo number_format($ex_fact_buy); ?></td>
                                            <td width="80" style="word-wrap: break-word;word-break: break-all;"  align="right"><? echo number_format($ex_fact_buy_fob); ?></td>
                                            <td width="80" style="word-wrap: break-word;word-break: break-all;"  align="right"><? echo number_format($ex_fact_buy_bal); ?></td>
                                            <td   style="word-wrap: break-word;word-break: break-all;"  align="right" width="80"><? echo number_format($ex_fact_buy_bal_fob); ?></td>
                                        </tr>
                                        <?
                                        $sumCutting_qty+=$cutting_qty;
                                        $sumPrinting_qty+=$printing_qty;
                                        $sumPrintreceived_qty+=$printreceived_qty;
                                        $sumEmb_qty+=$emb_qty;
                                        $sumEmbRec_qty+=$embRec_qty;
                                        $sumWash_qty+=$wash_qty;
                                        $sumWashRec_qty+=$washRec_qty;
                                        $sumSpecial_qty+=$special_qty;
                                        $sumSpecialRec_qty+=$specialRec_qty;
                                        $sumSewIn_inQty+=$sewIn_inQty;
                                        $sumSewIn_outQty+=$sewIn_outQty;
                                        $sumSewOut_inQty+=$sewOut_inQty;
                                        $sumSewOut_outQty+=$sewOut_outQty;
                                        $sumIron_qty+=$iron_qty;
                                        $sumReIron_qty+=$reIron_qty;
                                        $sumFinish_qty+=$finish_qty;
                                        $sumPolyIn_inQty+=$polyIn_inQty;
                                        $sumPolyOut_outQty+=$polyOut_outQty;


                                        $sum_exfact+=$ex_fact_buy;
                                        $sum_exfact_fob+=$ex_fact_buy_fob;
                                        $sum_exfact_bal+=$ex_fact_buy_bal;
                                        $sum_exfact_bal_fob+=$ex_fact_buy_bal_fob ;
                                        $tot_fin_rcv_qnty+=$fin_rcv_qnty;
                                        $tot_buyer_fin_rcv_qnty+=$fin_rcv_qnty;
                                        $b++;
                                    }
                                }
                                ?>
                                </table>
                            </div>
                            	<table border="1" class="tbl_bottom"  width="1950" rules="all" id="" >
                                    <tr>
                                    	<td style="word-wrap: break-word;word-break: break-all;" width="30">&nbsp;</td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" lign="right">Total</td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumCutting_qty); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumPrinting_qty); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumPrintreceived_qty); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumEmb_qty); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumEmbRec_qty); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumWash_qty); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumWashRec_qty); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumSpecial_qty); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumSpecialRec_qty); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumSewIn_inQty); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumSewIn_outQty); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumSewOut_inQty); ?></td>

                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumSewOut_outQty); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($tot_buyer_fin_rcv_qnty); ?></td>

                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumIron_qty); ?></td>
                                        <td  style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumReIron_qty); ?></td>
                                        <td  style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumPolyIn_inQty); ?></td>
                                        <td  style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumPolyOut_outQty); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumFinish_qty); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sum_exfact); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sum_exfact_fob); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sum_exfact_bal); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" align="right" width="80"><? echo number_format($sum_exfact_bal_fob); ?></td>
                                    </tr>
                                </table>
	                 	</div>
						</td>
					<?
					if(str_replace("'","",trim($cbo_subcon))==2)
					{
					?>
					<td width="550" align="left" valign="top"><div align="left" style="width:350px; background-color:#FCF"><strong>Production-Subcontract Order(Inbound)Summary </strong></div>
					<div style="float:left; width:550px">
						<table width="550" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
							<thead>
								<tr>
									<th width="30">Sl.</th>
									<th width="120">Buyer</th>
									<th width="80">Total Cut Qty</th>
									<th width="80">Total Sew Input</th>
									<th width="80">Total Sew Qty</th>
									<th width="80">Total Iron Qty</th>
									<th>Total Gmt. Fin. Qty</th>
								</tr>
							</thead>
						</table>
						<div style="max-height:425px; width:550px" >
						<table cellspacing="0" border="1" class="rpt_table"  width="550" rules="all" id="" >
						<?
						if(str_replace("'","",$cbo_floor)==0) $sub_floor_name="";else $sub_floor_name=" and b.floor_id in($cbo_floor)";
						if ($location==0) $sub_location_cond=""; else $sub_location_cond=" and b.location_id in($location) ";

						$total_po_quantity=0;$total_po_value=0;$total_cut_subcon=0;$total_sew_out_subcon=0;$total_ex_factory=0;
						$i=1;
						if(str_replace("'","",$cbo_company_name)==0) $company_name_sub=""; else $company_name_sub="and a.company_id = $cbo_company_name";
						if($db_type==0)
						{
							$ex_factory_sql="SELECT a.party_id, sum(c.order_quantity) as order_quantity
							from subcon_ord_mst a, subcon_gmts_prod_dtls b, subcon_ord_dtls c
							where a.id=c.MST_ID and c.id=b.order_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and b.production_date between $txt_date_from and $txt_date_to $company_name_sub $subcon_work_comp $sub_floor_name $sub_location_cond group by a.party_id";
						}
						else
						{
							 $ex_factory_sql="SELECT a.party_id, sum(c.order_quantity) as order_quantity
							from subcon_ord_mst a, subcon_gmts_prod_dtls b, subcon_ord_dtls c
							where a.id=c.MST_ID and c.id=b.order_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and b.production_date between $txt_date_from and $txt_date_to $company_name_sub  $subcon_work_comp $sub_floor_name $sub_location_cond group by a.party_id";
						}
						//echo  $exfactory_sql;
						$ex_factory_sql_result=sql_select($ex_factory_sql);
						$ex_factory_arr=array();
						foreach($ex_factory_sql_result as $resRow)
						{
							$ex_factory_arr[$resRow[csf("party_id")]] = $resRow[csf("order_quantity")];
						}
						//var_dump($exfactory_arr);die;
						//print_r($ex_factory_arr);die;

						//@@@@@@@@@@@@@@@@@@@@@
						$sub_cut_sew_array=array();

						if($db_type==0)
						{
							$production_mst_sql= sql_select("SELECT  a.party_id,
							sum(CASE WHEN production_type ='1' THEN production_qnty ELSE 0 END) AS cutting_qnty,
							sum(CASE WHEN production_type ='7' THEN production_qnty ELSE 0 END) AS sewing_input_qnty,
							sum(CASE WHEN production_type ='2' THEN production_qnty ELSE 0 END) AS sewingout_qnty,
							sum(CASE WHEN production_type ='3' THEN production_qnty  ELSE 0 END) AS ironout_qnty,
							sum(CASE WHEN production_type ='4' THEN production_qnty  ELSE 0 END) AS gmts_fin_qnty
							from subcon_ord_mst a, subcon_ord_dtls c, subcon_gmts_prod_dtls b

							where a.id=c.MST_ID and c.id=b.order_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and  c.is_deleted=0 and c.status_active=1 and b.production_date between $txt_date_from and $txt_date_to $company_name_sub  $subcon_work_comp $sub_location_cond $sub_floor_name group by a.party_id");
						}
						else
						{
							$production_mst_sql=sql_select("SELECT  a.party_id,
							sum(CASE WHEN production_type ='1' THEN production_qnty ELSE 0 END) AS cutting_qnty,
							sum(CASE WHEN production_type ='7' THEN production_qnty ELSE 0 END) AS sewing_input_qnty,

							sum(CASE WHEN production_type ='2' THEN production_qnty ELSE 0 END) AS sewingout_qnty,
							sum(CASE WHEN production_type ='3' THEN production_qnty  ELSE 0 END) AS ironout_qnty,
							sum(CASE WHEN production_type ='4' THEN production_qnty  ELSE 0 END) AS gmts_fin_qnty
							from subcon_ord_mst a, subcon_ord_dtls c, subcon_gmts_prod_dtls b
							where a.id=c.MST_ID and c.id=b.order_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.production_date between $txt_date_from and $txt_date_to $company_name_sub $subcon_work_comp $sub_location_cond $sub_floor_name group by a.party_id");

						}
						foreach($production_mst_sql as $sql_result)
						{
							$sub_cut_sew_array[$sql_result[csf("party_id")]]['1']=$sql_result[csf("cutting_qnty")];
							$sub_cut_sew_array[$sql_result[csf("party_id")]]['7']=$sql_result[csf("sewing_input_qnty")];
							$sub_cut_sew_array[$sql_result[csf("party_id")]]['2']=$sql_result[csf("sewingout_qnty")];
							$sub_cut_sew_array[$sql_result[csf("party_id")]]['3']=$sql_result[csf("ironout_qnty")];
							$sub_cut_sew_array[$sql_result[csf("party_id")]]['4']=$sql_result[csf("gmts_fin_qnty")];
						}
						//var_dump($cutting_array);
						//@@@@@@@@@@@@@@@@@@@@@
						if($db_type==0)
						{
							$production_date_sql="SELECT a.party_id, sum(c.order_quantity) as po_quantity, sum(c.amount) as po_total_price
							from subcon_ord_mst a, subcon_gmts_prod_dtls b, subcon_ord_dtls c
							where c.job_no_mst=a.subcon_job and c.id=b.order_id and b.production_type in (1,2,7) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.production_date between $txt_date_from and $txt_date_to $company_name_sub $subcon_work_comp $sub_location_cond $sub_floor_name  group by a.party_id order by a.party_id ASC";
						}
						else
						{
							$production_date_sql="SELECT a.party_id, sum(c.order_quantity) as po_quantity, sum(c.amount) as po_total_price
							from subcon_ord_mst a, subcon_ord_dtls c , subcon_gmts_prod_dtls b
							where a.id=c.mst_id and c.id=b.order_id and b.production_type in (1,2,7) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.production_date between $txt_date_from and $txt_date_to $company_name_sub $subcon_work_comp $sub_location_cond $sub_floor_name group by a.party_id order by a.party_id ASC";
						}
						//echo $production_date_sql;//die;
						$pro_sql_result=sql_select($production_date_sql);
						foreach($pro_sql_result as $pro_date_sql_row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
								<td width="30"><? echo $i;?></td>
								<td width="120"><? echo $buyer_short_library[$pro_date_sql_row[csf("party_id")]]; ?></td>
								<td width="80" align="right"><? echo number_format($sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['1']); ?></td>
								<td width="80" align="right"><? echo number_format($sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['7']); ?></td>
								<td width="80" align="right"><? echo number_format($sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['2']); ?></td>
								<td width="80" align="right"><? echo number_format($sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['3']); ?></td>
								<td align="right"><? echo number_format($sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['4']); ?></td>
							</tr>
							<?
							$total_cut_subcon+=$sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['1'];
							$total_input_subcon+=$sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['7'];
							$total_sew_out_subcon+=$sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['2'];
							$total_iron_subcon+=$sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['3'];
							$total_gmts_fin_subcon+=$sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['4'];
							$i++;
						}//end foreach 1st
						//$chart_data_qnty="Order Qty;".$total_po_quantity."\n"."Cutting;".$total_cut."\n"."Sew Out ;".$total_sew_out."\n"."Ex-Fact;".$total_ex_factory."\n";
						?>
						</table>
						<table border="1" class="tbl_bottom"  width="550" rules="all" id="" >
							<tr>
								<td width="30">&nbsp;</td>
								<td width="120" align="right">Total</td>
								<td width="80" id="tot_cutting"><? echo number_format($total_cut_subcon); ?></td>
								<td width="80" id="tot_input"><? echo number_format($total_input_subcon); ?></td>
								<td width="80" id="tot_sew_out"><? echo number_format($total_sew_out_subcon); ?></td>
								<td width="80" id="tot_iron_out"><? echo number_format($total_iron_subcon); ?></td>
								<td id="tot_gmt_fin_out"><? echo number_format($total_gmts_fin_subcon); ?></td>
							</tr>
						</table>
						<br />
							<div style="background-color:#FF9; width:400px; font-size:16px"><strong>Total Cutting: <? echo number_format($all_production_cutt=$sumCutting_qty+$total_cut_subcon,0); ?> (Pcs)&nbsp;&nbsp; & Total Sewing: <? echo number_format($all_production_sewing=$sumSewOut_inQty+$total_sew_out_subcon,0); ?> (Pcs)</strong></div><br />
							<div style="background-color:#FF9; width:400px; font-size:16px"><strong>Total Iron: <? echo number_format($all_production_iron=$sumIron_qty+$total_iron_subcon,0); ?> (Pcs)&nbsp;&nbsp; & Total Gmts. Fin.: <? echo number_format($all_production_gmts_fin=$sumFinish_qty+$total_gmts_fin_subcon,0); ?> (Pcs)</strong></div>
							</div>
						</div>
					</td>
					<?
					}
					?>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				</table>

			</div>
			<div>&nbsp;</div>
			<br />
			<!-- ==========================================================================================/
	        /										Details part										   /
	        /=========================================================================================== -->
			<? if($type==1) // Date Wise
			{

				?>
			   <h5 style="width:600px; background-color:#FCF; float:left;"><strong>Production-Regular Order</strong></h5>
				<table width="3915" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
					<thead>
					<tr>
						<th width="30" style="word-wrap: break-word;word-break: break-all;">Sl.</th>
						<th width="100" style="word-wrap: break-word;word-break: break-all;">Working Factory</th>
						<th width="100" style="word-wrap: break-word;word-break: break-all;">Job No</th>
						<th width="130" style="word-wrap: break-word;word-break: break-all;">Order Number</th>
						<th width="130" style="word-wrap: break-word;word-break: break-all;">Ship Date</th>
						<th width="100" style="word-wrap: break-word;word-break: break-all;">Buyer Name</th>
						<th width="130" style="word-wrap: break-word;word-break: break-all;">Style Name</th>
						<th width="100" style="word-wrap: break-word;word-break: break-all;">File No</th>
						<th width="100" style="word-wrap: break-word;word-break: break-all;">Internal Ref</th>
						<th width="130" style="word-wrap: break-word;word-break: break-all;">Item Name</th>
						<th width="100" style="word-wrap: break-word;word-break: break-all;">Production Date</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Cutting</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Sent to prnt</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Rev prn/Emb</th>

						<th width="80" style="word-wrap: break-word;word-break: break-all;">Sent to Emb</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Rev Emb</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Sent to Wash</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Rev Wash</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Sent to Sp. Works</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Rev Sp. Works</th>

						<th width="80" style="word-wrap: break-word;word-break: break-all;">Sewing In (Inhouse)</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Sewing In (Out-bound)</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Total Sewing Input</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Sewing Out (Inhouse)</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Sewing Out (Out-bound)</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Total Sewing Out</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Finishing<br>Rcv</th>

						<th width="80" style="word-wrap: break-word;word-break: break-all;">Iron Qty (Inhouse)</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Iron Qty (Out-bound)</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Total Iron Qty</th>

						<th width="80" style="word-wrap: break-word;word-break: break-all;">Re-Iron Qty </th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Poly Qty (Inhouse)</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Poly Qty (Out-bound)</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Total Poly Qty</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Finish Qty (Inhouse)</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Finish Qty (Out-bound)</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Total Finish Qty</th>

						<th width="80" style="word-wrap: break-word;word-break: break-all;">Today Carton</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Prod/Dzn</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Reject Qty</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Ex-Fac. Qty</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Ex-Fac. FOB Value</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Ex-Fac Bal. Qty</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Ex- Fac. Bal. FOB Value</th>
						<th style="word-wrap: break-word;word-break: break-all;">Remarks</th>
						</tr>
					 </thead>
				</table>
				<div style="width:3933px; overflow-y: scroll; max-height:400px;" id="scroll_body" >
					<table cellspacing="0" border="1" class="rpt_table"  width="3915" rules="all" id="table_body" >
						<?
						// var_dump($all_data_arr);
						$i=1;
						$fin_rcv_total=0;
						$check_fin_rcv=array();
						foreach($all_data_arr as $po_id=>$po_data)
						// var_dump($all_data_arr);
						// var_dump($po_data);
						{
							foreach($po_data as $prod_date=>$prod_date_data)
							{
								$ex_itemdata='';
								$ex_itemdata=array_filter(array_unique(explode('__',$prod_date_data)));
								foreach($ex_itemdata as $data_all)
								{
									$item_id=''; $buyer_name=''; $job_no=''; $po_no=''; $style_ref=''; $ref_no=''; $file_no=''; $company_id='';
										$ex_data=array_filter(explode('**',$data_all));
										// print_r($ex_data);

									if($ex_data[1] !="")
									{
										$item_id=$ex_data[0];
										$buyer_name=$ex_data[1];
										$job_no=$ex_data[2];
										$po_no=$ex_data[3];
										$style_ref=$ex_data[4];
										$ref_no=$ex_data[5];
										$file_no=$ex_data[6];
										$company_id=$ex_data[7];
										//$floor_id=$ex_data[10];
										$serving_comp_id=$ex_data[9];
										$serving_company='';
										//  echo $serving_comp_id."</br>";
										if($ex_data[8]==1)
										{
											$serving_company=$company_short_library[$serving_comp_id];
										}
										else if($ex_data[8]==3)
										{
											$serving_company=$supplier_arr[$serving_comp_id];
										}

										if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										$cutting_qty=$print_qty=$printRec_qty=$emb_qty=$embRec_qty=$wash_qty=$washRec_qty=$special_qty=$specialRec_qty=$sewIn_inQty=$sewIn_outQty=$sewing_input_total=$sewOut_inQty=$sewOut_outQty=$sewing_output_total=$ironIn_qty=$ironOut_qty=$iron_qty_total=$reIron_qty=$finishIn_qty=$finishOut_qty=$finishing_qty=$carton_qty=$rejFinish_qty=$rejSewing_qty=$reject_Qty=$polyIn_Qty=$polyOut_Qty=0;
								  		//$prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("production_type")]]['0']['pQty']+=$row[csf("production_quantity")];

										$cutting_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['1']['0']['pQty'];
										$print_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['2']['1']['embQty'];
										$printRec_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['3']['1']['embQty'];
										$emb_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['2']['2']['embQty'];
										$embRec_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['3']['2']['embQty'];
										$wash_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['2']['3']['embQty'];
										$washRec_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['3']['3']['embQty'];
										$special_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['2']['4']['embQty'];
										$specialRec_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['3']['4']['embQty'];
										$sewIn_inQty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['4']['1']['sQty'];
										$sewIn_outQty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['4']['3']['sQty'];
										$sewOut_inQty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['5']['1']['sQty'];
										$sewOut_outQty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['5']['3']['sQty'];
										$ironIn_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['7']['1']['sQty'];
										$ironOut_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['7']['3']['sQty'];
										$reIron_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['7']['0']['reQty'];
										$finishIn_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['8']['1']['sQty'];
										$finishOut_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['8']['3']['sQty'];
										$carton_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['0']['0']['crtQty'];
										$rejFinish_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['8']['0']['rejectQty'];
										$rejSewing_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['5']['0']['rejectQty'];

										$polyIn_Qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['11']['1']['sQty'];
										$polyOut_Qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['11']['3']['sQty'];

										$rejIron_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['7']['0']['rejectQty'];
										$rejPrint_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['3']['0']['rejectQty'];
										$rejCutting_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['1']['0']['rejectQty'];
										//echo $rejFinish_qty.'='.$rejSewing_qty.'='.$rejIron_qty.'='.$rejPrint_qty.'='.$rejCutting_qty;
										$ex_fact = $prod_date_qty_arr_ex[$po_id][$ex_data[9]][$prod_date][$item_id]['ex_factory_qnty'];
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
											<td width="30" style="word-wrap: break-word;word-break: break-all;"><? echo $i;?></td>
											<td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo $serving_company; ?></p></td>
											<td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo $job_no;?></p></td>
											<td width="130" style="word-wrap: break-word;word-break: break-all;"><p><a href="##" onclick="openmypage_order(<? echo $po_id;?>,<? echo $item_id;?>,'orderQnty_popup');" ><? echo $po_no; ?></a></p></td>
											<td width="130" style="word-wrap: break-word;word-break: break-all;"><p><?php echo change_date_format($job_arr2[$po_id]['pub_shipment_date']); ?></p></td>
											<td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo $buyer_short_library[$buyer_name]; ?></p></td>
											<td width="130" style="word-wrap: break-word;word-break: break-all;"><p><? echo $style_ref; ?></p></td>
											<td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo $file_no; ?></p></td>
											<td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo $ref_no; ?></p></td>
											<td width="130" style="word-wrap: break-word;word-break: break-all;"><p><? echo $garments_item[$item_id]; ?></p></td>
											<td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo change_date_format($prod_date); ?></p></td>

											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id;?>','<? echo $location; ?>','<? //echo $floor_id; ?>','Cutting Info','cutting_popup');" ><? echo $cutting_qty; ?></a></td>

		                                    <td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" title="Here"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id;?>','<? echo $location; ?>','<? //echo $floor_id; ?>','Printing Issue Info','printing_issue_popup');" ><? echo $print_qty; ?></a></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id;?>','<? echo $location; ?>','<? //echo $floor_id; ?>','Priniting Receive Info','printing_receive_popup');" ><? echo $printRec_qty; ?></a></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id;?>','<? echo $location; ?>','<? echo $floor_id; ?>','Embroidery Issue Info','embroi_issue_popup');" ><? echo $emb_qty; ?></a></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id;?>','<? echo $location; ?>','<? echo $floor_id; ?>','Embroidery Receive Info','embroi_receive_popup');" ><? echo $embRec_qty; ?></a></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id;?>','<? echo $location; ?>','<? echo $floor_id; ?>','Wash Issue Info','wash_issue_popup');" ><? echo $wash_qty; ?></a></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id;?>','<? echo $location; ?>','<? echo $floor_id; ?>','Wash Receive Info','wash_receive_popup');" ><? echo $washRec_qty; ?></a></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id;?>','<? echo $location; ?>','<? echo $floor_id; ?>','Special Works Issue Info','sp_issue_popup');" ><? echo $special_qty; ?></a></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id;?>','<? echo $location; ?>','<? echo $floor_id; ?>','Special Works Receive Info','sp_receive_popup');" ><? echo $specialRec_qty; ?></a> </td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__0';?>','1','4','sewingQnty_popup');" ><? echo $sewIn_inQty; ?></a></td>
											<td width="80"  style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__0';?>','3','4','sewingQnty_popup');" ><? echo $sewIn_outQty; ?></a></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><? $sewing_input_total=$sewIn_inQty+$sewIn_outQty; echo $sewing_input_total; ?></td>

											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__'.$serving_comp_id; ?>','1','5','sewingQnty_popup');" ><? echo $sewOut_inQty; ?></a></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__'.$serving_comp_id;?>','3','5','sewingQnty_popup');" ><? echo $sewOut_outQty; ?></a></td>

											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><? $sewing_output_total=$sewOut_inQty+$sewOut_outQty; echo $sewing_output_total; ?></td>

											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right">
												<?
											$fin_rcv_qnty=$order_wise_fin_rec_qnty[$po_id][$prod_date][$item_id]['qnty'];
											$company_id=$order_wise_fin_rec_qnty[$po_id][$prod_date][$item_id]['company_id'];
											$fin_rcv_total+=$fin_rcv_qnty;
											$arr_bind=$po_id."***".$prod_date."***".$item_id;
											array_push($check_fin_rcv, $arr_bind);

											 ?>

											<a href="##" onclick="openmyfinrcv('<?=$po_id;?>','<?=$company_id;?>','<?=$prod_date;?>','<?=$item_id;?>')"><? echo fn_number_format($fin_rcv_qnty,0,".",","); ?></a>

											</td>

											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'1','0','ironQnty_popup');" ><? echo $ironIn_qty; ?></a></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'3','0','ironQnty_popup');" ><? echo $ironOut_qty; ?></a></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><? $iron_qty_total=$ironIn_qty+$ironOut_qty; echo $iron_qty_total; ?></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><? echo $reIron_qty; ?></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__'.$serving_comp_id;?>','1','11','sewingQnty_popup');" ><? echo $polyIn_Qty; ?></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__'.$serving_comp_id;?>','3','11','sewingQnty_popup');" ><? echo $polyOut_Qty; ?></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><? $poly_qty_total=$polyIn_Qty+$polyOut_Qty; echo $poly_qty_total; ?></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><? echo $finishIn_qty; ?></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><? echo $finishOut_qty; ?></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'0','0','finishQnty_popup');" ><? $finishing_qty=$finishIn_qty+$finishOut_qty; echo $finishing_qty; ?></a></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><? echo $carton_qty; ?></td>
											<? $prod_dzn=0;
											if($sewing_output_total!=0)
											{
												$prod_dzn=($sewing_output_total)/12;
											}
											?>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><? if($prod_dzn!=0) echo number_format($prod_dzn,2); else echo "0"; ?></td>
											<? //$cm_per=0; $cm_per=$cm_per_dzn[$rows[csf("job_no_mst")]] ;
											$rej_title='Fin '.$rejFinish_qty.', Sew '.$rejSewing_qty.', Iron '.$rejIron_qty.', Print '.$rejPrint_qty.', Cut '.$rejCutting_qty;
											 ?>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" title="<? echo $rej_title;?>" >
		                                    <a href="##" onclick="openmypage_rej(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $ex_data[9];?>,'0','reject_qty');" ><? $reject_Qty=$rejFinish_qty+$rejSewing_qty+$rejIron_qty+$rejPrint_qty+$rejCutting_qty; echo $reject_Qty;  ?></a>
											</td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><? echo $ex_fact; //$ex_fact=$ex_fac_arr_po_datewise[$po_id][$prod_date]; ?></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><? echo $ex_fact_fob=$ex_fact*$po_wise_unit_price[$po_id]; ?></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><? echo $ex_fact_bal= $sewing_output_total-$ex_fact; ?></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><? echo $ex_fact_bal_fob= $ex_fact_bal*$po_wise_unit_price[$po_id]; ?></td>

											<td style="word-wrap: break-word;word-break: break-all;" width="">
												<a href="##"  onclick="openmypage_remark(<? echo $po_id;?>,'date_wise_production_report');" > Veiw </a>
											</td>
										</tr>
										<?
										$tot_cutting_qty+=$cutting_qty;
										$tot_print_qty+=$print_qty;
										$tot_printRec_qty+=$printRec_qty;
										$tot_emb_qty+=$emb_qty;
										$tot_embRec_qty+=$embRec_qty;
										$tot_wash_qty+=$wash_qty;
										$tot_washRec_qty+=$washRec_qty;
										$tot_special_qty+=$special_qty;
										$tot_specialRec_qty+=$specialRec_qty;
										$tot_sewIn_inQty+=$sewIn_inQty;
										$tot_sewIn_outQty+=$sewIn_outQty;
										$tot_sewing_input+=$sewing_input_total;
										$tot_sewOut_inQty+=$sewOut_inQty;
										$tot_sewOut_outQty+=$sewOut_outQty;
										$tot_sewing_output+=$sewing_output_total;
										$tot_ironIn_qty+=$ironIn_qty;
										$tot_ironOut_qty+=$ironOut_qty;
										$tot_iron_qty+=$iron_qty_total;
										$tot_reIron_qty+=$reIron_qty;
										$tot_polyIn_Qty+=$polyIn_Qty;
										$tot_polyOut_Qty+=$polyOut_Qty;
										$tot_poly_qty_sum+=$poly_qty_total;
										$tot_finishIn_qty+=$finishIn_qty;
										$tot_finishOut_qty+=$finishOut_qty;
										$tot_finishing_qty+=$finishing_qty;
										$tot_carton_qty+=$carton_qty;
										$total_prod_dzn+=$prod_dzn;
										$tot_rejFinish_qty+=$rejFinish_qty;
										$tot_rejSewing_qty+=$rejSewing_qty;
										$tot_reject_Qty+=$reject_Qty;
										$tot_ex_fac+=$ex_fact;
										$tot_ex_fac_fob+=$ex_fact_fob;
										$tot_fac_bal+=$ex_fact_bal;
										$tot_fac_bal_fob+=$ex_fact_bal_fob;
										$i++;
									}
								}
							}
						}
						//unset($date_sql_result);

						// echo "<pre>";
						// print_r($rcv_data_need_to_show);
						// echo "</pre>";

						$rcv_data_need_to_show=array();
				        foreach ($order_wise_fin_rec_qnty as $po_id=> $po_wise_data)
				        {

				        	foreach ($po_wise_data as $receive_date => $rcv_date_wise_data)
				        	{
				        		foreach ($rcv_date_wise_data as $item_id => $item_data)
				        		{

				        			$arr_bind=$po_id."***".$receive_date."***".$item_id;
					        		if(!in_array($arr_bind, $check_fin_rcv))
					                {
					                	$buyer_fullQty_arr[$item_data['buyer_name']][0]['0']['fin_rcv_qnty']+=$item_data['qnty'];
					                	$rcv_data_need_to_show[$po_id][$receive_date][$item_id]['qnty']=$item_data['qnty'];
							        	$rcv_data_need_to_show[$po_id][$receive_date][$item_id]['job_no']=$item_data['job_no'];
							        	$rcv_data_need_to_show[$po_id][$receive_date][$item_id]['po_number']=$item_data['po_number'];
							        	$rcv_data_need_to_show[$po_id][$receive_date][$item_id]['buyer_name']=$item_data['buyer_name'];
							        	$rcv_data_need_to_show[$po_id][$receive_date][$item_id]['shipment_date']=$item_data['shipment_date'];
							        	$rcv_data_need_to_show[$po_id][$receive_date][$item_id]['file_no']=$item_data['file_no'];
							        	$rcv_data_need_to_show[$po_id][$receive_date][$item_id]['grouping']=$item_data['grouping'];
							        	$rcv_data_need_to_show[$po_id][$receive_date][$item_id]['style_ref_no']=$item_data['style_ref_no'];
							        	$rcv_data_need_to_show[$po_id][$receive_date][$item_id]['gmts_item_id']=$item_data['gmts_item_id'];
							        	$rcv_data_need_to_show[$po_id][$receive_date][$item_id]['item_id']=$item_data['item_id'];
							        	$rcv_data_need_to_show[$po_id][$receive_date][$item_id]['company_id']=$item_data['company_id'];
					                	array_push($check_fin_rcv, $arr_bind);
					                }
				        		}
				        	}
				        }


						foreach ($rcv_data_need_to_show as $po_id => $po_d)
						{
							foreach ($po_d as $receive_date => $date_data)
							{
								foreach ($date_data as $item_id => $rcv_data)
								{
									?>
										<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
											<td width="30" style="word-wrap: break-word;word-break: break-all;"><? echo $i;?></td>
											<td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo $company_short_library[$rcv_data['company_id']]; ?></p></td>
											<td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo $rcv_data['job_no'];?></p></td>
											<td width="130" style="word-wrap: break-word;word-break: break-all;">
												<p><a href="##" onclick="openmypage_order(<? echo $po_id;?>,<? echo $item_id;?>,'orderQnty_popup');" ><? echo $rcv_data['po_number'];?></a></p>
											</td>
											<td width="130" style="word-wrap: break-word;word-break: break-all;"><p><?php echo change_date_format($rcv_data['shipment_date']); ?></p></td>
											<td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo $buyer_short_library[$rcv_data['buyer_name']]; ?></p></td>
											<td width="130" style="word-wrap: break-word;word-break: break-all;"><p><? echo $rcv_data['style_ref_no']; ?></p></td>
											<td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo $rcv_data['file_no']; ?></p></td>
											<td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo $rcv_data['grouping']; ?></p></td>
											<td width="130" style="word-wrap: break-word;word-break: break-all;"><p><? echo $garments_item[$item_id]; ?></p></td>
											<td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo change_date_format($receive_date); ?></p></td>

											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>

		                                    <td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" title="Here"></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"> </td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
											<td width="80"  style="word-wrap: break-word;word-break: break-all;" align="right"></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>

											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>

											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>

											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right">
												<?
											$fin_rcv_qnty=$rcv_data['qnty'];
											$company_id=$rcv_data['company_id'];
											$fin_rcv_total+=$fin_rcv_qnty;  ?>
											<a href="##" onclick="openmyfinrcv('<?=$po_id;?>','<?=$company_id;?>','<?=$receive_date;?>','<?=$item_id;?>')"><? echo fn_number_format($fin_rcv_qnty,0,".",","); ?></a>
											</td>

											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></a></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>

											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>

											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" >

											</td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>

											<td style="word-wrap: break-word;word-break: break-all;" width="">

											</td>
										</tr>
									<?
									$i++;
								}
							}
						}
					?>

					</table>
					<table width="3915" cellspacing="0" border="1" class="tbl_bottom" rules="all" id="report_table_footer" >
						<tr>

							<td width="30" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
							<td width="100" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
							<td width="100" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
							<td width="130" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
							<td width="130" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
							<td width="100" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
							<td width="130" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
							<td width="100" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
							<td width="100" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
							<td width="130" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
							<td width="100" style="word-wrap: break-word;word-break: break-all;" align="right">Total</td>
							<td width="80"  style="word-wrap: break-word;word-break: break-all;" align="right" id="total_cut_td" ><? echo $tot_cutting_qty;?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_printissue_td"><? echo $tot_print_qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_printrcv_td"><?  echo $tot_printRec_qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_emb_iss"><? echo $tot_emb_qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_emb_re"><? echo $tot_embRec_qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_wash_iss"><? echo $tot_wash_qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_wash_re"><? echo $tot_washRec_qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_sp_iss"><? echo $tot_special_qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_sp_re"><? echo $tot_specialRec_qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_sewin_inhouse_td"><? echo $tot_sewIn_inQty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_sewin_outbound_td"><? echo $tot_sewIn_outQty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_sewin_td"><? echo $tot_sewing_input; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_sewout_inhouse_td"><? echo $tot_sewOut_inQty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_sewout_outbound_td"><? echo $tot_sewOut_outQty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_sewout_td"><? echo $tot_sewing_output; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_fin_rcv_td"><? echo $fin_rcv_total; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_iron_in_td"><?  echo $tot_ironIn_qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_iron_out_td"><?  echo $tot_ironOut_qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_iron_td"><?  echo $tot_iron_qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_re_iron_td"><?  echo $tot_reIron_qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id=""><? echo $tot_polyIn_Qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id=""><? echo $tot_polyOut_Qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id=""><? echo $tot_poly_qty_sum; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_finishin_td"><? echo $tot_finishIn_qty; ?></td>
							<td width="80"  style="word-wrap: break-word;word-break: break-all;"align="right" id="total_finishout_td"><? echo $tot_finishOut_qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_finish_td"><? echo $tot_finishing_qty; ?></td>
							<td width="80"  style="word-wrap: break-word;word-break: break-all;"align="right" id="total_carton_td"><? echo $tot_carton_qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_prod_dzn_td"><?  echo number_format($total_prod_dzn,2); ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_rej_value_td"><? echo number_format($tot_reject_Qty,2); ?></td >
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_ex_fac_td1"><? echo number_format($tot_ex_fac,2); ?></td >
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_ex_fac_td2"><? echo number_format($tot_ex_fac_fob,2); ?></td >
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_ex_fac_td3"><? echo number_format($tot_fac_bal,2); ?></td >
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_ex_fac_td4"><? echo number_format($tot_fac_bal_fob,2); ?></td >
							<td>&nbsp;</td>
					</tr>
					</table>

				</div>


				<?
			}
			else if($type==2)// Date Location Floor & Line
			{
			 	?>
				<h5 style="width:600px; float:left; background-color:#FCF"><strong>Production-Regular Order</strong></h5>
				<table width="3700px" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
				   <thead>
						   <tr>
								<th width="30">Sl.</th>
								<th width="100">Working Factory</th>
								<th width="100">Job No</th>
								<th width="130">Order Number</th>
								<th width="100">Buyer Name</th>
								<th width="130">Style Name</th>
								<th width="100">File No</th>
								<th width="100">Internal Ref</th>
								<th width="130">Item Name</th>
								<th width="100">Production Date</th>
								<th width="100">Status</th>
								<th width="100">Location</th>
								<th width="100">Floor</th>
								<th width="100">Sewing Line No</th>
								<th width="80">Cutting</th>
								<th width="80">Sent to prnt</th>
								<th width="80">Rev prn/Emb</th>

								<th width="80">Sent to Emb</th>
								<th width="80">Rev Emb</th>
								<th width="80">Sent to Wash</th>
								<th width="80">Rev Wash</th>
								<th width="80">Sent to Sp. Works</th>
								<th width="80">Rev Sp. Works</th>

								<th width="80">Sewing In (Inhouse)</th>
								<th width="80">Sewing In (Out-bound)</th>
								<th width="80">Total Sewing Input</th>
								<th width="80">Sewing Out (Inhouse)</th>
								<th width="80">Sewing Out (Out-bound)</th>
								<th width="80">Total Sewing Out</th>

								<th width="80">Iron Qty (Inhouse)</th>
								<th width="80">Iron Qty (Out-bound)</th>
								<th width="80">Iron Qty</th>
								<th width="80">Re-Iron Qty </th>
								<th width="80">Poly Qty (Inhouse)</th>
						        <th width="80">Poly Qty (Out-bound)</th>
						        <th width="80">Total Poly Qty</th>
								<th width="80">Finish Qty (Inhouse)</th>
								<th width="80">Finish Qty (Out-bound)</th>
								<th width="80">Total Finish Qty</th>
								<th width="80">Today Carton</th>
								<th width="80">Prod/Dzn</th>
								<th width="">Remarks</th>
						   </tr>
					</thead>
				</table>
				<div style="max-height:425px; overflow-y:scroll; width:3718px"  id="scroll_body">
					<table cellspacing="0" border="1" class="rpt_table"  width="3700px" rules="all" id="table_body" >
						<?
							$i=1;
							foreach($all_data_arr as $po_id=>$po_data)
							{
								foreach($po_data as $prod_date=>$prod_date_data)
								{
									foreach($prod_date_data as $item_id=>$item_data)
									{
										foreach($item_data as $location_id=>$location_data)
										{
											foreach($location_data as $floor_id=>$floor_data)
											{
												$ex_linedata='';
												$ex_linedata=array_filter(array_unique(explode('__',$floor_data)));
												foreach($ex_linedata as $data_all)
												{
														$line_id=''; $buyer_name=''; $job_no=''; $po_no=''; $style_ref=''; $ref_no=''; $file_no=''; $company_id=''; $prod_source=''; $resource_allo='';
														$ex_data=array_filter(explode('##',$data_all));
														//echo $floor_id.'='; //die;
													if($ex_data[1]!="")
													{
														if($ex_data[0]=="") $line_id=0;
														else $line_id=$ex_data[0];
														$buyer_name=$ex_data[1];
														$job_no=$ex_data[2];
														$po_no=$ex_data[3];
														$style_ref=$ex_data[4];
														$ref_no=$ex_data[5];
														$file_no=$ex_data[6];
														//$company_id=$ex_data[7];
														$prod_source=$ex_data[7];
														$resource_allo=$ex_data[8];
														$serving_comp_id=$ex_data[9];
														$serving_company='';
														// new development
														if($ex_data[7]==1)
														{
															$serving_company=$company_short_library[$serving_comp_id];
														}
														else if($ex_data[7]==3)
														{
															$serving_company=$supplier_arr[$serving_comp_id];
														}

														/*if($prod_source==1)
														{
															$serving_company=$company_short_library[$ex_data[10]];
														}
														else if($prod_source==3)
														{
															$serving_company=$supplier_arr[$ex_data[10]];
														}*/

														if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

														$cutting_qty=$print_qty=$printRec_qty=$emb_qty=$embRec_qty=$wash_qty=$washRec_qty=$special_qty=$specialRec_qty=$sewIn_inQty=$sewIn_outQty=$sewing_input_total=$sewOut_inQty=$sewOut_outQty=$sewing_output_total=$ironIn_qty=$ironOut_qty=$iron_qty_total=$reIron_qty=$finishIn_qty=$finishOut_qty=$finishing_qty=$carton_qty=$rejFinish_qty=$rejSewing_qty=$reject_Qty=0;
														$cutting_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['1']['0']['pQty'];
														$print_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['2']['1']['embQty'];
														$printRec_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['3']['1']['embQty'];
														$emb_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['2']['2']['embQty'];
														$embRec_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['3']['2']['embQty'];
														$wash_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['2']['3']['embQty'];
														$washRec_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['3']['3']['embQty'];
														$special_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['2']['4']['embQty'];
														$specialRec_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['3']['4']['embQty'];

														$sewIn_inQty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['4']['1']['sQty'];
														$sewIn_outQty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['4']['3']['sQty'];
														$sewOut_inQty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['5']['1']['sQty'];
														$sewOut_outQty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['5']['3']['sQty'];

														$ironIn_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['7']['1']['sQty'];
														$ironOut_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['7']['3']['sQty'];
														$reIron_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['7']['0']['reQty'];

														$polyIn_Qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['11']['1']['sQty'];
										                $polyOut_Qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['11']['3']['sQty'];

														$finishIn_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['8']['1']['sQty'];
														$finishOut_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['8']['3']['sQty'];
														$carton_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id]['0']['0']['0']['crtQty'];
														$rejFinish_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['8']['0']['rejectQty'];
														$rejSewing_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['5']['0']['rejectQty'];

														$sewing_line='';
														if($resource_allo==1)
														{
															$line_number=explode(",",$prod_reso_arr[$line_id]);
															foreach($line_number as $val)
															{
																if($sewing_line=='') $sewing_line=$line_library[$val]; else $sewing_line.=", ".$line_library[$val];
															}
														}
														else $sewing_line=$line_library[$line_id];
														if($line_id!="") $swing_line_id=$line_id; else $swing_line_id=0;
													?>
														<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
															<td width="30"><? echo $i;?></td>
															<td width="100"><p><? echo $serving_company; ?></p></td>
															<td width="100"><p><? echo $job_no;?></p></td>
															<td width="130"><p><a href="##" onclick="openmypage_order(<? echo $po_id;?>,<? echo $item_id; ?>,'orderQnty_popup');" ><? echo $po_no; ?></a></p></td>
															<td width="100"><p><? echo $buyer_short_library[$buyer_name]; ?></p></td>
															<td width="130"><p><? echo $style_ref; ?></p></td>
															<td width="100"><p><? echo $file_no; ?></p></td>
															<td width="100"><p><? echo $ref_no; ?></p></td>
															<td width="130"><p><? echo $garments_item[$item_id]; ?></p></td>
															<td width="100"><p><? echo change_date_format($prod_date); ?></p></td>
															<td width="100"><p><? echo $knitting_source[$prod_source]; ?></p></td>

															<td width="100"><p><? echo $location_library[$location_id]; ?></p></td>
															<td width="100"><p><? echo $floor_library[$floor_id]; ?></p></td>
															<td width="100" align="center"><p><? echo $sewing_line; ?></p></td>

															<td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location_id;?>,<? echo $floor_id;?>,<? echo $swing_line_id;?>,'Cutting Info','cutting_popup_location');" ><? echo $cutting_qty; ?></a></td>
															<td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location_id;?>,<? echo $floor_id;?>,'<? echo $line_id;?>','Printing Issue Info','printing_issue_popup_location');" ><? echo $print_qty; ?></a></td>
															<td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location_id;?>,<? echo $floor_id;?>,'<? echo $line_id;?>','Printing Receive Info','printing_receive_popup_location');" ><? echo $printRec_qty; ?></a></td>
															<td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location_id;?>,<? echo $floor_id;?>,'<? echo $line_id;?>','Embroidery Issue Info','embroi_issue_popup_location');" ><? echo $emb_qty; ?></a></td>
															<td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location_id;?>,<? echo $floor_id;?>,'<? echo $line_id;?>','Embroidery Receive Info','embroi_receive_popup_location');" ><? echo $embRec_qty; ?></a></td>
															<td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location_id;?>,<? echo $floor_id;?>,'<? echo $line_id;?>','Wash Issue Info','wash_issue_popup_location');" ><? echo $wash_qty; ?></a></td>
															<td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location_id;?>,<? echo $floor_id;?>,'<? echo $line_id;?>','Wash Receive Info','wash_receive_popup_location');" ><? echo $washRec_qty; ?></a></td>
															<td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location_id;?>,<? echo $floor_id;?>,'<? echo $line_id;?>','Spetial Work Info','sp_issue_popup_location');" ><? echo $special_qty; ?></a></td>
															<td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location_id;?>,<? echo $floor_id;?>,'<? echo $line_id;?>','Spetial Work Info','sp_receive_popup_location');" ><? echo $specialRec_qty; ?></a></td>

															<td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__0';?>','1','4','sewingQnty_popup');" ><? echo $sewIn_inQty; ?></a></td>
															<td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__0';?>','3','4','sewingQnty_popup');" ><? echo $sewIn_outQty; ?></a></td>
															<td width="80" align="right"><? $sewing_input_total=$sewIn_inQty+$sewIn_outQty; echo $sewing_input_total; ?></td>
															<td width="80" align="right"><a href="##" onclick="openmypage_sew_output2(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__'.$serving_comp_id;?>','1','5','sewingQnty_popup',<? echo $location_id;?>,<? echo $floor_id;?>,'<? echo $line_id; ?>');" ><? echo $sewOut_inQty; ?></a></td>
															<td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__'.$serving_comp_id;?>','3','5','sewingQnty_popup');" ><? echo $sewOut_outQty; ?></a></td>
															<td width="80" align="right"><? $sewing_output_total=$sewOut_inQty+$sewOut_outQty; echo $sewing_output_total; ?></td>
															<td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'1','0','ironQnty_popup');" ><? echo $ironIn_qty; ?></a></td>
															<td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'3','0','ironQnty_popup');" ><? echo $ironOut_qty; ?></a></td>
															<td width="80" align="right"><? $iron_qty_total=$ironIn_qty+$ironOut_qty; echo $iron_qty_total; ?></a></td>
															<td width="80" align="right"><? echo $reIron_qty; ?></td>
															<td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__'.$serving_comp_id;?>','1','11','sewingQnty_popup');" ><? echo $polyIn_Qty; ?></td>
											                <td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__'.$serving_comp_id;?>','3','11','sewingQnty_popup');" ><? echo $polyOut_Qty; ?></td>
											                <td width="80" align="right"><? $poly_qty_total=$polyIn_Qty+$polyOut_Qty; echo $poly_qty_total; ?></td>
															<td width="80" align="right"><? echo $finishIn_qty; ?></td>
															<td width="80" align="right"><? echo $finishOut_qty; ?></td>
															<td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'0','0','finishQnty_popup');" ><? $finishing_qty=$finishIn_qty+$finishOut_qty; echo $finishing_qty; ?></a></td>
															<td width="80" align="right"><? echo $carton_qty; ?></td>
															<? $prod_dzn=0; $prod_dzn=$sewing_output_total / 12 ; $total_prod_dzn=0; $total_prod_dzn+=$prod_dzn; ?>
															<td width="80" align="right"><? if($prod_dzn!=0) echo number_format($prod_dzn,2); else echo "0"; ?></td>
															<td><a href="##"  onclick="openmypage_remark(<? echo $po_id;?>,'date_wise_production_report');" > Veiw </a></td>
													</tr>
													<?
														$tot_cutting_qty+=$cutting_qty;
														$tot_print_qty+=$print_qty;
														$tot_printRec_qty+=$printRec_qty;
														$tot_emb_qty+=$emb_qty;
														$tot_embRec_qty+=$embRec_qty;
														$tot_wash_qty+=$wash_qty;
														$tot_washRec_qty+=$washRec_qty;
														$tot_special_qty+=$special_qty;
														$tot_specialRec_qty+=$specialRec_qty;
														$tot_sewIn_inQty+=$sewIn_inQty;
														$tot_sewIn_outQty+=$sewIn_outQty;
														$tot_sewing_input+=$sewing_input_total;
														$tot_sewOut_inQty+=$sewOut_inQty;
														$tot_sewOut_outQty+=$sewOut_outQty;
														$tot_sewing_output+=$sewing_output_total;
														$tot_ironIn_qty+=$ironIn_qty;
														$tot_ironOut_qty+=$ironOut_qty;
														$tot_iron_qty+=$iron_qty_total;
														$tot_polyIn_Qty+=$polyIn_Qty;
														$tot_polyOut_Qty+=$polyOut_Qty;
														$tot_poly_qty_sum+=$poly_qty_total;
														$tot_reIron_qty+=$reIron_qty;
														$tot_finishIn_qty+=$finishIn_qty;
														$tot_finishOut_qty+=$finishOut_qty;
														$tot_finishing_qty+=$finishing_qty;
														$tot_carton_qty+=$carton_qty;
														$tot_rejFinish_qty+=$rejFinish_qty;
														$tot_rejSewing_qty+=$rejSewing_qty;
														$tot_reject_Qty+=$reject_Qty;
														// $total_prod_dzn+=$prod_dzn;
														$i++;
													}
												}
											}
										}
									}
								}
							}//end foreach 1st
						//unset($pro_dlfl_sql_res);
						?>
				    </table>
					<table width="3700" cellspacing="0" border="1" class="tbl_bottom" rules="all" id="report_table_footer" >

							<tr>
								<td width="30">&nbsp;</td>
								<td width="100">&nbsp;</td>
								<td width="100">&nbsp;</td>
								<td width="130">&nbsp;</td>
								<td width="100">&nbsp;</td>
								<td width="130">&nbsp;</td>
								<td width="100">&nbsp;</td>
								<td width="100">&nbsp;</td>
								<td width="130">&nbsp;</td>
								<td width="100">&nbsp;</td>
								<td width="100">&nbsp;</td>
								<td width="100">&nbsp;</td>
								<td width="100">&nbsp;</td>
								<td width="100">Totals</td>
								<td width="80" align="right" id="total_cut_td"><? echo $tot_cutting_qty;?></td>
								<td width="80" align="right" id="total_printissue_td"><? echo $tot_print_qty; ?> </td>
								<td width="80" align="right" id="total_printrcv_td"><? echo $tot_printRec_qty;  ?>  </td>
								<td width="80" align="right" id="total_emb_iss"><? echo $tot_emb_qty; ?> </td>
								<td width="80" align="right" id="total_emb_re"><? echo $tot_embRec_qty;  ?>  </td>
								<td width="80" align="right" id="total_wash_iss"><? echo $tot_wash_qty; ?> </td>
								<td width="80" align="right" id="total_wash_re"><? echo $tot_washRec_qty;  ?>  </td>
								<td width="80" align="right" id="total_sp_iss"><? echo $tot_special_qty; ?> </td>
								<td width="80" align="right" id="total_sp_re"><? echo $tot_specialRec_qty;  ?>  </td>
								<td width="80" align="right" id="total_sewin_inhouse_td"><? echo $tot_sewIn_inQty; ?></td>
								<td width="80" align="right" id="total_sewin_outbound_td"><? echo $tot_sewIn_outQty; ?></td>
								<td width="80" align="right" id="total_sewin_td"><? echo $tot_sewing_input;  ?> </th>
								<td width="80" align="right" id="total_sewout_inhouse_td"><? echo $tot_sewOut_inQty; ?></td>
								<td width="80" align="right" id="total_sewout_outbound_td"><? echo $tot_sewOut_outQty; ?></td>
								<td width="80" align="right" id="total_sewout_td"><? echo $tot_sewing_output; ?> </td>

								<td width="80" align="right" id="total_iron_in_td"><?  echo $tot_ironIn_qty; ?></td>
								<td width="80" align="right" id="total_iron_out_td"><?  echo $tot_ironOut_qty; ?></td>
								<td width="80" align="right" id="total_iron_td"><?  echo $tot_iron_qty; ?></td>
								<td width="80" align="right" id="total_re_iron_td"><? echo $tot_reIron_qty; ?>aaaa</td>
								<td width="80" align="right" id=""><? echo $tot_polyIn_Qty; ?></td>
							    <td width="80" align="right" id=""><? echo $tot_polyOut_Qty; ?></td>
							    <td width="80" align="right" id=""><? echo $tot_poly_qty_sum; ?></td>
								<td width="80" align="right" id=""><? echo $tot_finishIn_qty; ?></td>
								<td width="80" align="right" id=""><? echo $tot_finishOut_qty; ?></td>
								<td width="80" align="right" id=""><?  echo $tot_finishing_qty; ?></td>
								<td width="80" align="right" id=""><? echo $tot_carton_qty; ?></td>
								<td width="80" align="right" id=""><?  echo $total_prod_dzn; ?></td>
								<td>&nbsp;</td>
							 </tr>

					</table>
				</div>


				<?
			}// end if condition of type

			/*=============================================================================================/
	        /											Subcon part										   /
	        /============================================================================================ */

			if(str_replace("'","",trim($cbo_subcon))==2) //yes
			{
				$fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
				$toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );

				if(str_replace("'","",$cbo_floor)==0) $sub_floor_name="";else $sub_floor_name=" and b.floor_id in($cbo_floor)";
				if ($location==0) $sub_location_cond=""; else $sub_location_cond=" and b.location_id in($location) ";
				?>

					<h5 style="width:800px;float: left; background-color:#FCF"><strong>Production-Subcontract Order (Inbound) Details</strong></h5>

						<table width="1590" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_2">
							<thead>
								<tr>
									<th width="30">Sl.</th>
									<th width="100">Working Factory</th>
									<th width="100">Job No</th>
									<th width="130">Order No</th>
									<th width="100">Buyer </th>
									<th width="130">Style </th>
									<th width="130">Item Name</th>
									<th width="75">Production Date</th>

									<th width="100">Floor</th>
									<th width="100">Sewing Line</th>

									<th width="90">Cutting</th>
									<th width="90">Sewing Input</th>
									<th width="90">Sewing Output</th>
									<th width="90">Iron Output</th>
									<th width="90">Gmts. Finishing</th>
									<th width="">Remarks</th>
								</tr>
							</thead>
						</table>
					<div style="max-height:300px; overflow-y:scroll; width:1610px" id="scroll_body2">
						<table border="1"  class="rpt_table"  width="1590" rules="all" id="sub_list_view">
							  <?
								$item_id_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
								$subcon_lc_comp2=str_replace("a.", "c.", $subcon_lc_comp);
								$subcon_work_comp2=str_replace("a.", "c.", $subcon_work_comp);

								$production_array=array();
								if($db_type==0)
								{
									$prod_sql= "SELECT c.order_id, c.production_date,c.floor_id,c.line_id,
										sum(CASE WHEN c.production_type ='1' THEN  c.production_qnty  ELSE 0 END) AS cutting_qnty,
										sum(CASE WHEN c.production_type ='7' THEN  c.production_qnty  ELSE 0 END) AS sewing_input_qnty,
										sum(CASE WHEN c.production_type ='2' THEN  c.production_qnty  ELSE 0 END) AS sewingout_qnty,
										sum(CASE WHEN c.production_type ='3' THEN  c.production_qnty  ELSE 0 END) AS ironout_qnty,
										sum(CASE WHEN c.production_type ='4' THEN  c.production_qnty  ELSE 0 END) AS gmts_fin_qnty
									from
										subcon_gmts_prod_dtls c
									where c.status_active=1 and c.is_deleted=0 and c.production_date between $txt_date_from and $txt_date_to $subcon_lc_comp2 $subcon_work_comp2  group by c.order_id, c.production_date,c.floor_id";
								}
								else
								{
									 $prod_sql= "SELECT c.order_id,c.gmts_item_id, c.production_date,c.floor_id,c.line_id,
										NVL(sum(CASE WHEN c.production_type ='1' THEN  c.production_qnty  ELSE 0 END),0) AS cutting_qnty,
										sum(CASE WHEN c.production_type ='7' THEN  c.production_qnty  ELSE 0 END) AS sewing_input_qnty,

										NVL(sum(CASE WHEN c.production_type ='2' THEN  c.production_qnty  ELSE 0 END),0) AS sewingout_qnty,
										NVL(sum(CASE WHEN c.production_type ='3' THEN  c.production_qnty  ELSE 0 END),0) AS ironout_qnty,
										NVL(sum(CASE WHEN c.production_type ='4' THEN  c.production_qnty  ELSE 0 END),0) AS gmts_fin_qnty
									from
										subcon_gmts_prod_dtls c
									where c.status_active=1 and c.is_deleted=0 and c.production_date between $txt_date_from and $txt_date_to $subcon_lc_comp2 $subcon_work_comp2 group by c.order_id,c.gmts_item_id, c.production_date,c.floor_id,c.line_id";
								}
								$prod_sql_result= sql_select($prod_sql);
								// echo $prod_sql;//die;
								foreach($prod_sql_result as $proRes)
								{
									$production_array[$proRes[csf("order_id")]][$proRes[csf("gmts_item_id")]][$proRes[csf("production_date")]][$proRes[csf("floor_id")]][$proRes[csf("line_id")]]['cutting_qnty']=$proRes[csf("cutting_qnty")];
									$production_array[$proRes[csf("order_id")]][$proRes[csf("gmts_item_id")]][$proRes[csf("production_date")]][$proRes[csf("floor_id")]][$proRes[csf("line_id")]]['sewing_input_qnty']=$proRes[csf("sewing_input_qnty")];
									$production_array[$proRes[csf("order_id")]][$proRes[csf("gmts_item_id")]][$proRes[csf("production_date")]][$proRes[csf("floor_id")]][$proRes[csf("line_id")]]['sewingout_qnty']=$proRes[csf("sewingout_qnty")];
									$production_array[$proRes[csf("order_id")]][$proRes[csf("gmts_item_id")]][$proRes[csf("production_date")]][$proRes[csf("floor_id")]][$proRes[csf("line_id")]]['ironout_qnty']=$proRes[csf("ironout_qnty")];
									$production_array[$proRes[csf("order_id")]][$proRes[csf("gmts_item_id")]][$proRes[csf("production_date")]][$proRes[csf("floor_id")]][$proRes[csf("line_id")]]['gmts_fin_qnty']=$proRes[csf("gmts_fin_qnty")];
								}
								// echo "<pre>";
								// print_r($production_array);
								if($db_type==0)
								{
									$order_sql= "SELECT c.id, c.order_no, c.cust_style_ref, a.company_id, a.party_id, a.location_id, a.job_no_prefix_num, b.gmts_item_id, sum(CASE WHEN b.order_id=c.id THEN b.production_qnty ELSE 0 END) AS production_qnty, b.production_date, b.line_id,b.floor_id,b.prod_reso_allo
									from subcon_ord_mst a, subcon_gmts_prod_dtls b, subcon_ord_dtls c
									where b.order_id=c.id and b.production_date between $txt_date_from and $txt_date_to $subcon_lc_comp $subcon_work_comp $sub_floor_name $sub_location_cond and a.subcon_job=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
									group by b.order_id, b.production_date
									order by b.production_date";
								}
								else
								{
									 $order_sql= "SELECT c.id, c.order_no, c.cust_style_ref, a.company_id, a.party_id, a.location_id, a.job_no_prefix_num, b.gmts_item_id, sum(CASE WHEN b.order_id=c.id THEN b.production_qnty ELSE 0 END) AS production_qnty,  b.production_date, b.line_id,b.floor_id,b.prod_reso_allo
									 from subcon_ord_mst a, subcon_ord_dtls c , subcon_gmts_prod_dtls b
									 where a.id=c.mst_id and b.order_id=c.id and  b.production_date between $txt_date_from and $txt_date_to  $subcon_lc_comp $subcon_work_comp	 $sub_floor_name $sub_location_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
									 group by b.order_id, c.id, c.order_no, c.cust_style_ref, a.company_id, a.job_no_prefix_num, a.party_id, a.company_id, a.party_id, a.location_id, b.gmts_item_id, b.production_date, b.line_id,b.floor_id,b.prod_reso_allo
									 order by c.id";
								}

							   // echo $order_sql; die;

								$order_sql_result=sql_select($order_sql);
								   $j=0;$k=0;
								   $total_cutt = 0;
								   //$po_item_line_array=array();
								   foreach($order_sql_result as $orderRes)
								   {

									   	//if( $po_item_line_array[$orderRes[csf("id")]][$orderRes[csf("gmt_item_id")]][$orderRes[csf("line_id")]]=="" )
									   	//{
									   		$j++;
									   		if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

									   		$sewing_line='';
									   		if($orderRes[csf('prod_reso_allo')]==1)
									   		{
									   			$line_number=explode(",",$prod_reso_arr[$orderRes[csf("line_id")]]);
									   			foreach($line_number as $val)
									   			{
									   				if($sewing_line=='') $sewing_line=$line_library[$val]; else $sewing_line.=", ".$line_library[$val];
									   			}
									   		}
									   		else {$sewing_line=$line_library[$orderRes[csf("line_id")]];}
									   		$po_id=$orderRes[csf("id")];
									   		$item_id=$orderRes[csf("gmts_item_id")];
									   		$prod_date=$orderRes[csf("production_date")];


									   		?>
									   		<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_3nd<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_3nd<? echo $j; ?>" style="height:20px">
									   			<td width="30" ><? echo $j; ?></td>
									   			<td width="100"><p><? echo $company_short_library[$orderRes[csf("company_id")]]; ?></p></td>
									   			<td width="100" align="center"><p><? echo $orderRes[csf("job_no_prefix_num")]; ?></p></td>
									   			<td width="130"><p><? echo $orderRes[csf("order_no")]; ?></p></td>
									   			<td width="100"><? echo $buyer_short_library[$orderRes[csf("party_id")]]; ?></td>
									   			<td width="130"><p><? echo $orderRes[csf("cust_style_ref")]; ?></p></td>
									   			<td width="130"><p><? echo $garments_item[$orderRes[csf("gmts_item_id")]];?></p></td>
									   			<td width="75" bgcolor="<? echo $color; ?>"><? echo change_date_format($orderRes[csf("production_date")]);  ?></td>

									   			<td width="100" align="left"><p><? echo $floor_library[$orderRes[csf('floor_id')]]; ?></p></td>

									   			<td width="100" align="left"><p><? echo $sewing_line; ?></p></td>

									   			<td width="90" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__0';?>','1','1','production_popup_subcon');" ><? echo $cutting= $production_array[$orderRes[csf("id")]][$orderRes[csf("gmts_item_id")]][$orderRes[csf("production_date")]][$orderRes[csf("floor_id")]][$orderRes[csf("line_id")]]['cutting_qnty']; $total_cutt+=$cutting; ?></a></td>

									   			<td width="90" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__0';?>','1','7','production_popup_subcon');" ><? echo $input= $production_array[$orderRes[csf("id")]][$orderRes[csf("gmts_item_id")]][$orderRes[csf("production_date")]][$orderRes[csf("floor_id")]][$orderRes[csf("line_id")]]['sewing_input_qnty']; $total_sewinput+=$input; ?></a></td>

									   			<td width="90" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__0';?>','1','2','production_popup_subcon');" ><? echo $output=$production_array[$orderRes[csf("id")]][$orderRes[csf("gmts_item_id")]][$orderRes[csf("production_date")]][$orderRes[csf("floor_id")]][$orderRes[csf("line_id")]]['sewingout_qnty']; $total_sew+=$output; ?></a></td>
									   			<td width="90" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__0';?>','1','3','production_popup_subcon');" ><? echo $iron= $production_array[$orderRes[csf("id")]][$orderRes[csf("gmts_item_id")]][$orderRes[csf("production_date")]][$orderRes[csf("floor_id")]][$orderRes[csf("line_id")]]['ironout_qnty']; $total_iron_sub+=$iron; ?></a></td>
									   			<td width="90" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__0';?>','1','4','production_popup_subcon');" ><? echo $fin= $production_array[$orderRes[csf("id")]][$orderRes[csf("gmts_item_id")]][$orderRes[csf("production_date")]][$orderRes[csf("floor_id")]][$orderRes[csf("line_id")]]['gmts_fin_qnty']; $total_gmtfin+=$fin; ?></a></td>

									   			<td width="">&nbsp;</td>
									   		</tr>
									   		<?
									   		//$po_item_line_array[$orderRes[csf("id")]][$orderRes[csf("gmt_item_id")]]=$orderRes[csf("line_id")];

									   	//}


								   }
								  ?>
								</table>

								<table border="1" class="tbl_bottom"  width="1590" rules="all" id="report_table_footer2" >
									<tr>
										<td width="30">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="130">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="130">&nbsp;</td>
										<td width="130">&nbsp;</td>

										<td width="75"></td>
										<td width="100">&nbsp;</td>
										<td width="100">Total:</td>

										<td width="90" id="total_cutt"><? echo $total_cutt; ?></td>
										<td width="90" id="total_sew_input"><? echo $total_sew_input; ?></td>
										<td width="90" id="total_sew"><? echo $total_sew; ?></td>
										<td width="90" id="total_iron_sub"><? echo $total_iron_sub; ?></td>
										<td width="90" id="total_gmtfin"><? echo $total_gmtfin; ?></td>
										<td width=""></td>
									 </tr>
							 </table>

					</div>


				<?
			}
			//-------------------------------------------END Show Date Location Floor & Line Wise------------------------
			?>
		</div><?
		$html = ob_get_contents();
		ob_clean();
		//$new_link=create_delete_report_file( $html, 1, 1, "../../../" );

		// echo "$html####";
		foreach (glob($user_id."_*.xls") as $filename)
		{
			@unlink($filename);
		}
		$name=$user_id."_".time().".xls";
		$create_new_excel = fopen($name, 'w');
		$is_created = fwrite($create_new_excel,$html);
		//$new_link=create_delete_report_file( $html, 1, 1, "../../../" );
		echo $html."####".$name;
		exit();
	}
	elseif($ReportType==3)// show2 button
	{
		$garments_nature=str_replace("'","",$cbo_garments_nature);
		$cbo_floor=str_replace("'","",$cbo_floor);
		//echo $cbo_floor;
		//if($garments_nature==1)$garments_nature="";
		 if($garments_nature==1) $garmentsNature=""; else $garmentsNature=" and garments_nature=$garments_nature";

		$location = str_replace("'","",$cbo_location);
		if(str_replace("'","",$cbo_company_name)==0) $company_name=""; else $company_name=" and b.company_name=$cbo_company_name";
		if(str_replace("'","",$cbo_buyer_name)==0)$buyer_name="";else $buyer_name=" and a.buyer_name=$cbo_buyer_name";
		if($cbo_floor==0) $floor_name="";else $floor_name=" and floor_id in($cbo_floor)";
		//echo $floor_name;die;
		if($cbo_floor==0) $floor_id="";else $floor_id=" and floor_id in($cbo_floor)";
		if ($location==0) $location_cond=""; else $location_cond=" and location in($location) ";

		if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="")$txt_date="";
		else $txt_date=" and production_date between $txt_date_from and $txt_date_to";

		if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="")$txt_fin_rcv_date="";
		else $txt_fin_rcv_date=" and b.receive_date between $txt_date_from and $txt_date_to";

		$fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
		$toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );
		//cbo_garments_nature
		$file_no = str_replace("'","",$txt_file_no);
		$internal_ref = str_replace("'","",$txt_internal_ref);
		$style_ref = str_replace("'","",$txt_style_ref);
		if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='".trim($file_no)."' ";
		if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='".trim($internal_ref)."' ";
		if ($style_ref=="") $style_ref_cond=""; else $style_ref_cond=" and a.style_ref_no='".trim($style_ref)."' ";
		if ($location==0) $location_cond=""; else $location_cond=" and location in($location) ";
		if($cbo_com_fac_name==0) $working_factory_cond=""; else $working_factory_cond=" and serving_company in($cbo_com_fac_name)";
		if(str_replace("'","",$cbo_company_name)==0) $company_name_cond=""; else $company_name_cond=" and company_id=$cbo_company_name";
		$variable_setting = sql_select("select EX_FACTORY from variable_settings_production where company_name=$cbo_company_name and variable_list=1 and status_active=1");

		$exfactory_level = $variable_setting[0]["EX_FACTORY"];
		?>
       <div>
			<div style="width:2700px">
				<table width="2700" cellspacing="0">
					<tr class="form_caption" style="border:none;">
						<td colspan="<? echo $colSpan; ?>" align="center" style="border:none;font-size:14px; font-weight:bold" ><? //echo $report_name; ?></td>
					 </tr>
					<tr style="border:none;">
						<td colspan="<? echo $colSpan; ?>" align="center" style="border:none; font-size:16px; font-weight:bold">
							Company Name:
							<?
							echo $company_library[str_replace("'","",$cbo_company_name)];
							/*$workingComp="";
							$workingFactCom=explode(",", $cbo_com_fac_name);
							foreach($workingFactCom as $workingFactCompany)
							{
								$workingComp.=$company_library[$workingFactCompany].', ';
							}
							echo chop($workingComp,',');*/
							?>
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="<? echo $colSpan; ?>" align="center" style="border:none;font-size:12px; font-weight:bold">
							<? echo "From $fromDate To $toDate" ;?>
						</td>
					</tr>
				</table>
				<table width="2080" cellspacing="0" border="1" rules="all" align="left">
					<tr>
						<td width="1760" align="left" valign="top">
	                    <div style="width:300px; float:left; background-color:#FCF"><strong>Production-Regular Order Summary</strong></div>
	                    <br/>
						<div style="clear:both;">
							<table width="1750" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
								<thead>
									<tr>
										<th style="word-wrap: break-word;word-break: break-all;" width="30">Sl.</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Buyer Name</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Cut Qty</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Sent to Print</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Rcv Print</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Sent to Emb</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Rcv Emb</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Sent to Wash</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Rcv Wash</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Sent to Sp. Works</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Rcv Sp. Works</th>

										<th style="word-wrap: break-word;word-break: break-all;" width="80">Sew Input</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Sew Input (Outbound)</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Sew Output</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Sew Output (Outbound)</th>

										<th style="word-wrap: break-word;word-break: break-all;" width="80">Finishing Rcv</th>

										<th style="word-wrap: break-word;word-break: break-all;" width="80">Total Iron</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Total Finish</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Ex-Fac. Qty</th>
									</tr>
								</thead>
							</table>
                            <div id="hide_scroll" style="overflow-y:auto; max-height:225px; width:1768px" >
                                <table cellspacing="0" border="1" class="rpt_table"  width="1750" rules="all" id="" >
                                <?
                                $job_arr=array();
                                if(str_replace("'","",$cbo_company_name)==0) $job_comp_cond=""; else $job_comp_cond="and a.company_name=$cbo_company_name ";

                               //$job_sql="select a.job_no_prefix_num, a.buyer_name, a.style_ref_no, a.company_name, b.id, b.po_number, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $job_comp_cond $buyer_name $file_no_cond $internal_ref_cond";

								$job_sql="SELECT a.job_no_prefix_num, a.buyer_name, a.style_ref_no, a.company_name, b.id, b.po_number, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b, pro_garments_production_mst c where a.id=b.job_id and b.id=c.po_break_down_id and a.is_deleted=0 and c.is_deleted=0 and c.status_active=1 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $job_comp_cond $buyer_name $style_ref_cond $file_no_cond $internal_ref_cond $txt_date";

								//and c.production_date between $txt_date_from and $txt_date_to

                                $job_sql_res=sql_select($job_sql); $tot_rows=0; $poIds='';
                                foreach($job_sql_res as $row)
                                {
                                    $tot_rows++;
                                    $poIds.=$row[csf("id")].",";
                                    $poIds_array[$row[csf("id")]]=$row[csf("id")];
                                    $job_arr[$row[csf("id")]]['job']=$row[csf("job_no_prefix_num")];
                                    $job_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
                                    $job_arr[$row[csf("id")]]['buyer']=$row[csf("buyer_name")];
                                    $job_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
                                    $job_arr[$row[csf("id")]]['file']=$row[csf("file_no")];
                                    $job_arr[$row[csf("id")]]['ref']=$row[csf("grouping")];
                                }

                                unset($job_sql_res);
                                $poIds_cond="";
                                //if ($file_no!="" || $internal_ref!="")
                                //{
                                   $poIds=implode(",", $poIds_array);
                                     if($db_type==2 && count($poIds_array)>=1000)
                                    {
                                        $poIds_cond=" and (";
                                        $poIdsArr=array_chunk($poIds_array,999);
                                        foreach($poIdsArr as $ids)
                                        {
                                            $ids=implode(",",$ids);
                                            $poIds_cond.=" po_break_down_id in ($ids) or ";
                                        }
                                        $poIds_cond=chop($poIds_cond,'or ');
                                        $poIds_cond.=")";
                                    }
                                    else
                                    {
                                        $poIds_cond=" and po_break_down_id in ($poIds)";
                                    }
                                //}
								// ============================== data store to gbl table ==================================
								$con = connect();
								execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from=1 and ENTRY_FORM=41");
								oci_commit($con);

								fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 41, 1, $poIds_array, $empty_arr);//PO ID
								// ============================== end data store to gbl table ==================================

                                $buyer_fullQty_arr=array();
                                $prod_date_qty_arr=array();
                                $prod_dlfl_qty_arr=array();
                                $all_data_arr=array();
                                $poIds_conds=str_replace("po_break_down_id", "a.po_break_down_id ", $poIds_cond);

                                $sql_dtls="SELECT  a.location, a.company_id, a.serving_company, a.floor_id, a.sewing_line, a.po_break_down_id,a.re_production_qty, a.production_date, a.item_number_id, a.production_type, a.production_source, a.embel_name, (a.prod_reso_allo) as prod_reso_allo, (b.production_qnty) as production_quantity,  (a.reject_qnty) as reject_qnty, (a.carton_qty) as carton_qty,c.color_number_id ,c.size_number_id,

                                (case when a.production_type=1 and b.production_type=1 and a.production_source=1  then b.production_qnty else 0 end ) as cutting_inbound,
                                (case when a.production_type=1 and b.production_type=1 and a.production_source=3  then b.production_qnty else 0 end ) as cutting_outbound,

                                (case when a.production_type=2 and b.production_type=2 and a.production_source in(1,3)  and a.embel_name=1 then b.production_qnty else 0 end ) as print_inbound,
                                (case when a.production_type=3 and b.production_type=3 and a.production_source in(1,3)  and a.embel_name=1 then b.production_qnty else 0 end ) as print_outbound,


                                (case when a.production_type=2 and b.production_type=2 and a.production_source in(1,3) and a.embel_name=2  then b.production_qnty else 0 end ) as sent_embel,
                                (case when a.production_type=3 and b.production_type=3 and a.production_source in(1,3)  and a.embel_name=2 then b.production_qnty else 0 end ) as recive_embel,

                                (case when a.production_type=2 and b.production_type=2 and a.production_source in(1,3) and a.embel_name=3  then b.production_qnty else 0 end ) as sent_wash,
                                (case when a.production_type=3 and b.production_type=3 and a.production_source in(1,3)  and a.embel_name=3 then b.production_qnty else 0 end ) as recive_wash,

                                (case when a.production_type=2 and b.production_type=2 and a.production_source in(1,3) and a.embel_name=4  then b.production_qnty else 0 end ) as sent_sp,
                                (case when a.production_type=3 and b.production_type=3 and a.production_source in(1,3)  and a.embel_name=4 then b.production_qnty else 0 end ) as recive_sp,

                                (case when a.production_type=4 and b.production_type=4 and a.production_source=1  then b.production_qnty else 0 end ) as sewing_in_inbound,
                                (case when a.production_type=4 and b.production_type=4 and a.production_source=3  then b.production_qnty else 0 end ) as sewing_in_outbound,

                                (case when a.production_type=5 and b.production_type=5 and a.production_source=1  then b.production_qnty else 0 end ) as sewing_out_inbound,
                                (case when a.production_type=5 and b.production_type=5 and a.production_source=3  then b.production_qnty else 0 end ) as sewing_out_outbound,

                                (case when a.production_type=8 and b.production_type=8 and a.production_source=1  then b.production_qnty else 0 end ) as finish_inbound,
                                (case when a.production_type=8 and b.production_type=8 and a.production_source=3  then b.production_qnty else 0 end ) as finish_outbound,

                                (case when a.production_type=7 and b.production_type=7 and a.production_source=1  then b.production_qnty else 0 end ) as iron_inbound,
                                (case when a.production_type=7 and b.production_type=7 and a.production_source=3  then b.production_qnty else 0 end ) as iron_outbound

                                from pro_garments_production_mst a ,pro_garments_production_dtls b ,wo_po_color_size_breakdown c,GBL_TEMP_ENGINE tmp
                                where a.id=b.mst_id and b.color_size_break_down_id=c.id and c.po_break_down_id=a.po_break_down_id  and a.po_break_down_id=tmp.ref_val and tmp.entry_form=41 and tmp.ref_from=1 and tmp.user_id = $user_id and c.status_active in(1,2,3) and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  a.is_deleted=0 and a.status_active=1 $company_name_cond $working_factory_cond $txt_date $floor_name $location_cond $garmentsNature  order by a.production_date ASC";


								//  echo  $sql_dtls;die;
                                $sql_dtls_res=sql_select($sql_dtls);

								$finish_rcv_sql="SELECT
								a.item_id,
								a.fin_receive_qnty,
								a.color_id,
								b.fini_location_id,
								b.floor_id,
								b.receive_date,
								a.po_break_down_id,
								a.qc_pass_qnty,
								b.company_id,
								c.buyer_name,
								a.production_date,
								a.size_id
								FROM
								gmt_finishing_receive_dtls a,
								gmt_finishing_receive_mst b,
								wo_po_details_master c,
								wo_po_break_down d, GBL_TEMP_ENGINE tmp
								WHERE
								b.id = a.mst_id
								and c.id=d.job_id
								and d.id=a.po_break_down_id
								and d.id=tmp.ref_val and tmp.entry_form=41 and tmp.ref_from=1 and tmp.user_id = $user_id
								AND a.status_active IN (1, 2, 3)
								AND a.is_deleted = 0
								AND b.status_active IN (1)
								AND b.is_deleted = 0
								$company_name_fin_rcv $txt_fin_rcv_date $location_cond_fin_rcv $floor_id_fin_rcv
								ORDER BY b.receive_date ASC";
								  // echo $finish_rcv_sql;die;
							  $sqlData = sql_select($finish_rcv_sql);
							  foreach($sqlData as $rowData){

								$all_data_arr3[$rowData[csf("po_break_down_id")]][$rowData[csf("production_date")]][$rowData[csf("fini_location_id")]][$rowData[csf("floor_id")]][$rowData[csf("color_id")]]['fin_receive_qnty'] += $rowData[csf('fin_receive_qnty')];
								$all_data_arr3[$rowData[csf("po_break_down_id")]][$rowData[csf("production_date")]][$rowData[csf("fini_location_id")]][$rowData[csf("floor_id")]][$rowData[csf("color_id")]]['item_id'] = $rowData[csf('item_id')];
								$all_data_arr3[$rowData[csf("po_break_down_id")]][$rowData[csf("production_date")]][$rowData[csf("fini_location_id")]][$rowData[csf("floor_id")]][$rowData[csf("color_id")]]['size_id'] = $rowData[csf('size_id')];
								$all_data_arr3[$rowData[csf("po_break_down_id")]][$rowData[csf("production_date")]][$rowData[csf("fini_location_id")]][$rowData[csf("floor_id")]][$rowData[csf("color_id")]]['company_id'] = $rowData[csf('company_id')];


							  }

					// echo "<pre>";
					// print_r($all_data_arr3);

					//topbar finishing recv .....
					$order_wise_fin_rec_qnty=array();
					foreach ($sqlData as $row)
					{
						$order_wise_fin_rec_qnty[$row[csf('po_break_down_id')]][$row[csf('production_date')]][$row[csf('item_id')]]['fin_receive_qnty']+=$row[csf('fin_receive_qnty')];
						$buyer_fullQty_arr[$row['BUYER_NAME']][0][0]['fin_receive_qnty']+=$row['FIN_RECEIVE_QNTY'];

					}
								   // for finishing rcv data ends here
								if( count($sql_dtls_res) > 0)
								{
									foreach($sql_dtls_res as $key=>$vals)
									{
										$all_production_po_arr[$vals[csf("po_break_down_id")]]=$vals[csf("po_break_down_id")];
									}
									$txt_date2=str_replace("production_date", "a.ex_factory_date", $txt_date);

									$all_production_poids=implode(",", $all_production_po_arr) ;
									if($exfactory_level==1) // gross level
									{
										$ex_fac_sql="SELECT c.buyer_id, a.po_break_down_id as po,a.ex_factory_date as dates,sum(a.ex_factory_qnty) as qnty from pro_ex_factory_delivery_mst c, pro_ex_factory_mst a,GBL_TEMP_ENGINE tmp where c.id=a.delivery_mst_id and a.po_break_down_id=tmp.ref_val and tmp.entry_form=41 and tmp.ref_from=1 and tmp.user_id = $user_id and a.status_active=1 and a.is_deleted=0  $txt_date2  and c.status_active=1 and a.entry_form<>85   group by c.buyer_id,a.po_break_down_id,a.ex_factory_date";
									}
									else
									{
										$ex_fac_sql="SELECT c.buyer_id, a.po_break_down_id as po,a.ex_factory_date as dates,sum(b.production_qnty) as qnty from pro_ex_factory_delivery_mst c, pro_ex_factory_mst a,pro_ex_factory_dtls b,GBL_TEMP_ENGINE tmp where c.id=a.delivery_mst_id and a.id=b.mst_id and a.po_break_down_id=tmp.ref_val and tmp.entry_form=41 and tmp.ref_from=1 and tmp.user_id = $user_id and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_deleted=0  $txt_date2  and c.status_active=1 and a.entry_form<>85   group by c.buyer_id,a.po_break_down_id,a.ex_factory_date";
									}
									// echo $ex_fac_sql;
									foreach(sql_select($ex_fac_sql) as $key=>$value)
									{
										$ex_fac_arr_po_datewise[$value[csf("po")]][$value[csf("dates")]]+=$value[csf("qnty")];
										//$ex_fac_arr_buyerwise[$value[csf("buyer_id")]]+=$value[csf("qnty")];
										if($value[csf("qnty")]*1>0)
										{
											$ex_fac_all_po[$value[csf("po")]] =$value[csf("po")];
										}
									}


									$exfac_poids=implode(",", $ex_fac_all_po) ;
									$all_exfac_poids="";
									if($db_type==2 && count($ex_fac_all_po)>1000)
									{
										$all_exfac_poids=" and (";
										$exIdsArr=array_chunk(explode(",",$ex_fac_all_po),999);
										foreach($exIdsArr as $ids)
										{
											$ids=implode(",",$ids);
											$all_exfac_poids.=" b.id in ($ids) or ";
										}
										$all_exfac_poids=chop($all_exfac_poids,'or ');
										$all_exfac_poids.=")";
									}
									else
									{
										$all_exfac_poids=" and b.id in ($exfac_poids)";
									}

									fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 41, 1, $ex_fac_all_po, $empty_arr);//PO ID

									$poIds_cond3=str_replace("po_break_down_id","b.id", $poIds_cond);
									$po_wise_unit_price_sql="SELECT a.buyer_name,b.id,b.unit_price from wo_po_details_master a, wo_po_break_down b,GBL_TEMP_ENGINE tmp where a.id=b.job_id and b.id=tmp.ref_val and tmp.entry_form=41 and tmp.ref_from=1 and tmp.user_id = $user_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
									foreach( sql_select($po_wise_unit_price_sql) as $key=>$val)
									{
										$po_wise_unit_price[$val[csf("id")]]=$val[csf("unit_price")];
										$buyer_wise_unit_price[$val[csf("buyer_name")]]+=$val[csf("unit_price")];
									}

           						}

                                if($type==1)
                                {
                                    foreach($sql_dtls_res as $row)
                                    {

                                        $buyer_name_dat=""; $job_no=''; $po_no=''; $buyer_id=''; $style_ref=''; $ref_no=''; $file_no='';
                                        $buyer_name_dat=$job_arr[$row[csf("po_break_down_id")]]['buyer'];
                                        $job_no=$job_arr[$row[csf("po_break_down_id")]]['job'];
                                        $po_no=$job_arr[$row[csf("po_break_down_id")]]['po'];
                                        $style_ref=$job_arr[$row[csf("po_break_down_id")]]['style'];
                                        $ref_no=$job_arr[$row[csf("po_break_down_id")]]['ref'];
                                        $file_no=$job_arr[$row[csf("po_break_down_id")]]['file'];

                                        //Buyer Wise Summary array start
                                        $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]]['0']['pQty']+=$row[csf("production_quantity")];
                                        $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("embel_name")]]['embQty']+=$row[csf("production_quantity")];
                                        $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("production_source")]]['sQty']+=$row[csf("production_quantity")];
                                        $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]]['0']['reQty']+=$row[csf("re_production_qty")];
                                        $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("production_source")]]['rejectQty']+=$row[csf("reject_qnty")];
                                        //Details array start
                                        $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("production_type")]]['0']['pQty']+=$row[csf("production_quantity")];

                                        //for serving company
                                        $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("serving_company")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("production_type")]]['0']['pQty']+=$row[csf("production_quantity")];

                                        $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("serving_company")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("production_type")]][$row[csf("production_source")]]['sQty']+=$row[csf("production_quantity")];

										// $sewOut_outQty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['5']['3']['sQty'];

                                        $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("serving_company")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("production_type")]]['0']['rejectQty']+=$row[csf("reject_qnty")];
                                        $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("serving_company")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("production_type")]]['0']['reQty']+=$row[csf("re_production_qty")];
                                        $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("serving_company")]][$row[csf("production_date")]][$row[csf("item_number_id")]]['0']['0']['crtQty']+=$row[csf("carton_qty")];
                                        $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("serving_company")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("production_type")]][$row[csf("embel_name")]]['embQty']+=$row[csf("production_quantity")];


                                        $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("production_type")]][$row[csf("embel_name")]]['embQty']+=$row[csf("production_quantity")];

                                        $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("production_type")]][$row[csf("production_source")]]['sQty']+=$row[csf("production_quantity")];

                                        $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("production_type")]]['0']['reQty']+=$row[csf("re_production_qty")];
                                        $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("production_type")]]['0']['rejectQty']+=$row[csf("reject_qnty")];
                                        $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]]['0']['0']['crtQty']+=$row[csf("carton_qty")];

                                        $all_data_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("color_number_id")]].=$row[csf("item_number_id")].'**'.$buyer_name_dat.'**'.$job_no.'**'.$po_no.'**'.$style_ref.'**'.$ref_no.'**'.$file_no.'**'.$row[csf("company_id")].'**'.$row[csf("production_source")].'**'.$row[csf("serving_company")].'__';//.'**'.$row[csf("floor_id")]

                                        // date wise
                                        $all_data_arr2[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("color_number_id")]]['company'] = $row[csf('company_id')];
                                        $all_data_arr2[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("color_number_id")]]['location'] = $row[csf('location')];
                                        $all_data_arr2[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("color_number_id")]]['floor'] = $row[csf('floor_id')];
                                        $all_data_arr2[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("color_number_id")]]['item_id'] = $row[csf('item_number_id')];
                                        $all_data_arr2[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("color_number_id")]]['color_id'] = $row[csf('color_number_id')];
                                        $all_data_arr2[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("color_number_id")]]['size_id'] = $row[csf('size_number_id')];
                                        $all_data_arr2[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("color_number_id")]]['production_source'] = $row[csf('production_source')];

                                        $all_data_arr2[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("color_number_id")]]['cutting_inbound'] += $row[csf('cutting_inbound')];
                                        $all_data_arr2[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("color_number_id")]]['cutting_outbound'] += $row[csf('cutting_outbound')];

                                        $all_data_arr2[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("color_number_id")]]['sewing_in_inbound'] += $row[csf('sewing_in_inbound')];
                                        $all_data_arr2[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("color_number_id")]]['sewing_in_outbound'] += $row[csf('sewing_in_outbound')];

                                        $all_data_arr2[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("color_number_id")]]['sewing_out_inbound'] += $row[csf('sewing_out_inbound')];
                                        $all_data_arr2[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("color_number_id")]]['sewing_out_outbound'] += $row[csf('sewing_out_outbound')];

                                        $all_data_arr2[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("color_number_id")]]['iron_inbound'] += $row[csf('iron_inbound')];
                                        $all_data_arr2[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("color_number_id")]]['iron_outbound'] += $row[csf('iron_outbound')];

                                        $all_data_arr2[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("color_number_id")]]['print_inbound'] += $row[csf('print_inbound')];
                                        $all_data_arr2[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("color_number_id")]]['print_outbound'] += $row[csf('print_outbound')];

                                        $all_data_arr2[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("color_number_id")]]['sent_embel'] += $row[csf('sent_embel')];
                                        $all_data_arr2[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("color_number_id")]]['recive_embel'] += $row[csf('recive_embel')];

                                        $all_data_arr2[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("color_number_id")]]['sent_wash'] += $row[csf('sent_wash')];
                                        $all_data_arr2[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("color_number_id")]]['recive_wash'] += $row[csf('recive_wash')];

                                        $all_data_arr2[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("color_number_id")]]['sent_sp'] += $row[csf('sent_sp')];
                                        $all_data_arr2[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("color_number_id")]]['recive_sp'] += $row[csf('recive_sp')];

                                        $all_data_arr2[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("color_number_id")]]['finish_inbound'] += $row[csf('finish_inbound')];
                                        $all_data_arr2[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("color_number_id")]]['finish_outbound'] += $row[csf('finish_outbound')];

                                    }
                                    // echo "<pre>";
                                    // print_r($all_data_arr2);
                                }
                                else if($type==2)
                                {
                                    foreach($sql_dtls_res as $row)
                                    {
                                        $buyer_name_dat=""; $job_no=''; $po_no=''; $buyer_id=''; $style_ref=''; $ref_no=''; $file_no='';
                                        $buyer_name_dat=$job_arr[$row[csf("po_break_down_id")]]['buyer'];
                                        $job_no=$job_arr[$row[csf("po_break_down_id")]]['job'];
                                        $po_no=$job_arr[$row[csf("po_break_down_id")]]['po'];
                                        $style_ref=$job_arr[$row[csf("po_break_down_id")]]['style'];
                                        $ref_no=$job_arr[$row[csf("po_break_down_id")]]['ref'];
                                        $file_no=$job_arr[$row[csf("po_break_down_id")]]['file'];
                                        //Buyer Wise Summary array start
                                        $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]]['0']['pQty']+=$row[csf("production_quantity")];
                                        $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("embel_name")]]['embQty']+=$row[csf("production_quantity")];
                                        $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("production_source")]]['sQty']+=$row[csf("production_quantity")];
                                        $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]]['0']['reQty']+=$row[csf("re_production_qty")];
                                        $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("production_source")]]['rejectQty']+=$row[csf("reject_qnty")];
                                        //Details array start
                                        if($row[csf("sewing_line")]=="") $row[csf("sewing_line")]=0;

                                        if($row[csf("floor_id")]=="") $row[csf("floor_id")]=0;

                                        if($row[csf("location")]=="") $row[csf("location")]=0;

                                        $prod_dlfl_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]][$row[csf("production_type")]]['0']['pQty']+=$row[csf("production_quantity")];
                                        $prod_dlfl_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]][$row[csf("production_type")]][$row[csf("embel_name")]]['embQty']+=$row[csf("production_quantity")];
                                        $prod_dlfl_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]][$row[csf("production_type")]][$row[csf("production_source")]]['sQty']+=$row[csf("production_quantity")];
                                        $prod_dlfl_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]][$row[csf("production_type")]]['0']['reQty']+=$row[csf("re_production_qty")];
                                        $prod_dlfl_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]][$row[csf("production_type")]]['0']['rejectQty']+=$row[csf("reject_qnty")];
                                        $prod_dlfl_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]]['0']['0']['crtQty']+=$row[csf("carton_qty")];

                                        $all_data_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("location")]][$row[csf("floor_id")]].=$row[csf("sewing_line")].'##'.$buyer_name_dat.'##'.$job_no.'##'.$po_no.'##'.$style_ref.'##'.$ref_no.'##'.$file_no.'##'.$row[csf("company_id")].'##'.$row[csf("production_source")].'##'.$row[csf("prod_reso_allo")].'##'.$row[csf("serving_company")].'__';

                                        // date, location and floor wise
                                        $all_data_arr3[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("color_number_id")]]['company'] = $row[csf('company_id')];
                                        $all_data_arr3[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("color_number_id")]]['location'] = $row[csf('location')];
                                        $all_data_arr3[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("color_number_id")]]['floor'] = $row[csf('floor_id')];
                                        $all_data_arr3[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("color_number_id")]]['item_id'] = $row[csf('item_number_id')];

										$all_data_arr3[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("color_number_id")]]['prod_reso_allo'] = $row[csf('prod_reso_allo')];
										$all_data_arr3[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("color_number_id")]]['sewing_line'] = $row[csf('sewing_line')];

                                        $all_data_arr3[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("color_number_id")]]['color_id'] = $row[csf('color_number_id')];
                                        $all_data_arr3[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("color_number_id")]]['size_id'] = $row[csf('size_number_id')];

                                        $all_data_arr3[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("color_number_id")]]['cutting_inbound'] += $row[csf('cutting_inbound')];
                                        $all_data_arr3[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("color_number_id")]]['cutting_outbound'] += $row[csf('cutting_outbound')];

                                        $all_data_arr3[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("color_number_id")]]['sewing_in_inbound'] += $row[csf('sewing_in_inbound')];
                                        $all_data_arr3[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("color_number_id")]]['sewing_in_outbound'] += $row[csf('sewing_in_outbound')];



                                        $all_data_arr3[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("color_number_id")]]['sewing_out_inbound'] += $row[csf('sewing_out_inbound')];


                                        $all_data_arr3[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("color_number_id")]]['sewing_out_outbound'] += $row[csf('sewing_out_outbound')];

                                        $all_data_arr3[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("color_number_id")]]['iron_inbound'] += $row[csf('iron_inbound')];
                                        $all_data_arr3[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("color_number_id")]]['iron_outbound'] += $row[csf('iron_outbound')];

                                        $all_data_arr3[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("color_number_id")]]['print_inbound'] += $row[csf('print_inbound')];
                                        $all_data_arr3[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("color_number_id")]]['print_outbound'] += $row[csf('print_outbound')];

                                        $all_data_arr3[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("color_number_id")]]['sent_embel'] += $row[csf('sent_embel')];
                                        $all_data_arr3[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("color_number_id")]]['recive_embel'] += $row[csf('recive_embel')];

                                        $all_data_arr3[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("color_number_id")]]['sent_wash'] += $row[csf('sent_wash')];
                                        $all_data_arr3[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("color_number_id")]]['recive_wash'] += $row[csf('recive_wash')];

                                        $all_data_arr3[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("color_number_id")]]['sent_sp'] += $row[csf('sent_sp')];
                                        $all_data_arr3[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("color_number_id")]]['recive_sp'] += $row[csf('recive_sp')];

                                        $all_data_arr3[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("color_number_id")]]['finish_inbound'] += $row[csf('finish_inbound')];
                                        $all_data_arr3[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("color_number_id")]]['finish_outbound'] += $row[csf('finish_outbound')];
                                    }
                                }



								//echo $finish_rcv_sql;die;
                                // print_r($all_data_arr3); die;
                                unset($sql_dtls_res);
                                // unset($job_arr);
                                //die;
                                //$cm_per_dzn=return_library_array( "select job_no, cm_for_sipment_sche from wo_pre_cost_dtls where is_deleted=0 and status_active=1",'job_no','cm_for_sipment_sche');
                                $b=1; //date_wise Summary
                                $ex_fac_arr_buyerwise = array();
                                $rowspan_arr = array();
                                // print_r($all_data_arr);die();
                                foreach($all_data_arr as $po_id=>$po_data)
                                {
                                    foreach($po_data as $prod_date=>$prod_date_data)
                                    {
                                    	foreach ($prod_date_data as $item_id => $item_data)
                                    	{
                                    		/*if(isset($rowspan_arr[$job_arr[$po_id]['job'].$prod_date]))
                                    		{
                                    			$rowspan_arr[$job_arr[$po_id]['job'].$prod_date] +=1;
                                    		}
                                    		else
                                    		{
                                    			$rowspan_arr[$job_arr[$po_id]['job'].$prod_date] =1;
                                    		}*/
                                    	    $rowspan_arr[$po_id][$prod_date]++;
                                            $ex_itemdata='';
                                            $ex_itemdata=array_filter(array_unique(explode('__',$item_data)));
                                            // print_r($ex_itemdata);
                                            foreach($ex_itemdata as $data_all)
                                            {
                                                $buyer_name=''; $item_id='';
                                                $ex_data=array_filter(explode('**',$data_all));
                                                // print_r($ex_data);
                                                if($ex_data[1] !="")
                                                {
                                                    $buyer_name=$ex_data[1];
                                                    $item_id=$ex_data[0];
                                                    $sewOut_inQty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['5']['1']['sQty'];
                                                    $sewOut_outQty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['5']['3']['sQty'];
                                                    $sewing_output_total=$sewOut_inQty+$sewOut_outQty;
                                                    $ex_fact=$ex_fac_arr_po_datewise[$po_id][$prod_date];
                                                    $ex_fact_fob=$ex_fact*$po_wise_unit_price[$po_id];
                                                    $ex_fact_bal= $sewing_output_total-$ex_fact;
                                                    $ex_fact_bal_fob= $ex_fact_bal*$po_wise_unit_price[$po_id];

                                                    $ex_fac_arr_buyerwise[$buyer_name]+=$ex_fact;
                                                    $ex_fac_fob_arr_buyerwise[$buyer_name]+=$ex_fact_fob;
                                                    $ex_fac_bal_arr_buyerwise[$buyer_name]+=$ex_fact_bal;
                                                    $ex_fac_bal_fob_arr_buyerwise[$buyer_name]+=$ex_fact_bal_fob;


                                                }
                                            }
                                        }
                                    }
                                }
                                // print_r($ex_fac_arr_buyerwise);
                                $rowspan_arr2 = array();
                                foreach($all_data_arr3 as $po_id=>$po_data)
                                {
                                    foreach($po_data as $prod_date=>$prod_date_data)
                                    {
                                    	foreach($prod_date_data as $location_id=>$location_data)
                                    	{
	                                    	foreach($location_data as $floor_id=>$floor_data)
	                                    	{
		                                    	foreach ($floor_data as $color_id => $color_data)
		                                    	{
		                                    		// if(isset($rowspan_arr2[$job_arr[$po_id]['job'].$prod_date]))
		                                    		// {
		                                    		// 	$rowspan_arr2[$job_arr[$po_id]['job'].$prod_date] +=1;
		                                    		// }
		                                    		// else
		                                    		// {
		                                    		// 	$rowspan_arr2[$job_arr[$po_id]['job'].$prod_date] =1;
		                                    		// }
													 $rowspan_arr2[$po_id][$prod_date]++;
	                                    		}
                                    		}
                                        }
                                    }
                                }

                              	// echo "<pre>";
                              	// print_r($rowspan_arr2);

								  $fin_rcv_qnty=$fin_Total_rcv=0;
                                foreach($buyer_fullQty_arr as $buyer_id=>$buyer_data)
                                {
                                    if($buyer_id!="")
                                    {
                                        if ($b%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                        $cutting_qty=$printing_qty=$printreceived_qty=$emb_qty=$embRec_qty=$wash_qty=$washRec_qty=$special_qty=$specialRec_qty=$sewIn_inQty=$sewIn_outQty=$sewOut_inQty=$sewOut_outQty=$iron_qty=$reIron_qty=$finish_qty=0;
                                        $cutting_qty=$buyer_data['1']['0']['pQty'];
                                        $printing_qty=$buyer_data['2']['1']['embQty'];
                                        $printreceived_qty=$buyer_data['3']['1']['embQty'];
                                        $emb_qty=$buyer_data['2']['2']['embQty'];
                                        $embRec_qty=$buyer_data['3']['2']['embQty'];
                                        $wash_qty=$buyer_data['2']['3']['embQty'];
                                        $washRec_qty=$buyer_data['3']['3']['embQty'];
                                        $special_qty=$buyer_data['2']['4']['embQty'];
                                        $specialRec_qty=$buyer_data['3']['4']['embQty'];
                                        $sewIn_inQty=$buyer_data['4']['0']['pQty'];
                                        $sewIn_outQty=$buyer_data['4']['3']['sQty'];
                                        $sewOut_inQty=$buyer_data['5']['0']['pQty'];
                                        $sewOut_outQty=$buyer_data['5']['3']['sQty'];
                                        $iron_qty=$buyer_data['7']['0']['pQty'];
                                        $reIron_qty=$buyer_data['7']['0']['reQty'];
                                        $finish_qty=$buyer_data['8']['0']['pQty'];
                                        $ex_fact_buy=$ex_fac_arr_buyerwise[$buyer_id];
                                        $ex_fact_buy_fob=$ex_fac_fob_arr_buyerwise[$buyer_id];
                                        $ex_fact_buy_bal=$ex_fac_bal_arr_buyerwise[$buyer_id];
                                        $ex_fact_buy_bal_fob=$ex_fac_bal_fob_arr_buyerwise[$buyer_id];

										$fin_rcv_qnty=$buyer_data[0]['0']['fin_receive_qnty'];






                                        ?>
                                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $b; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $b; ?>">
                                            <td style="word-wrap: break-word;word-break: break-all;" width="30"><? echo $b;?></td>
                                            <td style="word-wrap: break-word;word-break: break-all;" width="80"><p><? echo $buyer_short_library[$buyer_id]; ?>&nbsp;</p></td>
                                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($cutting_qty); ?></td>
                                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($printing_qty); ?></td>
                                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($printreceived_qty); ?></td>
                                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($emb_qty); ?></td>
                                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($embRec_qty); ?></td>
                                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($wash_qty); ?></td>
                                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($washRec_qty);  ?></td>
                                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($special_qty); ?></td>
                                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($specialRec_qty);  ?></td>
                                            <td  style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sewIn_inQty); ?></td>
                                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sewIn_outQty); ?></td>

                                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sewOut_inQty); ?></td>

                                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sewOut_outQty); ?></td>
											<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($fin_rcv_qnty,0) ?></td>


											<td  style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($iron_qty); ?></td>
                                            <td width="80" style="word-wrap: break-word;word-break: break-all;"  align="right"><? echo number_format($finish_qty); ?></td>
                                            <td width="80" style="word-wrap: break-word;word-break: break-all;"  align="right"><? echo number_format($ex_fact_buy); ?></td>
                                        </tr>
                                        <?
                                        $sumCutting_qty+=$cutting_qty;
                                        $sumPrinting_qty+=$printing_qty;
                                        $sumPrintreceived_qty+=$printreceived_qty;
                                        $sumEmb_qty+=$emb_qty;
                                        $sumEmbRec_qty+=$embRec_qty;
                                        $sumWash_qty+=$wash_qty;
                                        $sumWashRec_qty+=$washRec_qty;
                                        $sumSpecial_qty+=$special_qty;
                                        $sumSpecialRec_qty+=$specialRec_qty;
                                        $sumSewIn_inQty+=$sewIn_inQty;
                                        $sumSewIn_outQty+=$sewIn_outQty;
                                        $sumSewOut_inQty+=$sewOut_inQty;
                                        $sumSewOut_outQty+=$sewOut_outQty;
                                        $sumIron_qty+=$iron_qty;
                                        $sumReIron_qty+=$reIron_qty;
                                        $sumFinish_qty+=$finish_qty;


                                        $sum_exfact+=$ex_fact_buy;
                                        $sum_exfact_fob+=$ex_fact_buy_fob;
                                        $sum_exfact_bal+=$ex_fact_buy_bal;
                                        $sum_exfact_bal_fob+=$ex_fact_buy_bal_fob ;
										$fin_Total_rcv +=$fin_rcv_qnty  ;
                                        $b++;
                                    }
                                }
								//   echo "<pre>";
								//   print_r($all_data_arr3);die;
								execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from=1 and ENTRY_FORM=41");
								oci_commit($con);
								disconnect($con);
                                ?>
                                </table>
                            </div>
                            	<table border="1" class="tbl_bottom"  width="1750" rules="all" id="" >
                                    <tr>
                                    	<td style="word-wrap: break-word;word-break: break-all;" width="30">&nbsp;</td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" lign="right">Total</td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumCutting_qty); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumPrinting_qty); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumPrintreceived_qty); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumEmb_qty); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumEmbRec_qty); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumWash_qty); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumWashRec_qty); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumSpecial_qty); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumSpecialRec_qty); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumSewIn_inQty); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumSewIn_outQty); ?></td>



                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumSewOut_inQty); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumSewOut_outQty); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($fin_Total_rcv,0) ?></td>

										<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumIron_qty); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumFinish_qty); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sum_exfact); ?></td>
                                    </tr>
                                </table>
	                 	</div>
						</td>
					<?
					if(str_replace("'","",trim($cbo_subcon))==2)
					{
					?>
					<td width="650" align="left" valign="top"><div align="left" style="width:350px; background-color:#FCF"><strong>Production-Subcontract Order(Inbound)Summary </strong></div>
					<div style="float:left; width:700px">
						<table width="650" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
							<thead>
								<tr>
									<th width="30">Sl.</th>
									<th width="120">Buyer</th>
									<th width="80">Total Cut Qty</th>
									<th width="80">Total Sew Input</th>
									<th width="80">Total Sew Qty</th>
									<th width="80">Total Iron Qty</th>
									<th width="80">Total Gmt. Fin. Qty</th>
									<th>Total Delivery</th>
								</tr>
							</thead>
						</table>
						<div style="max-height:425px; width:650px" >
						<table cellspacing="0" border="1" class="rpt_table"  width="650" rules="all" id="" >
						<?
						if(str_replace("'","",$cbo_floor)==0) $sub_floor_name="";else $sub_floor_name=" and b.floor_id in($cbo_floor)";
						if ($location==0) $sub_location_cond=""; else $sub_location_cond=" and b.location_id in($location) ";

						$total_po_quantity=0;$total_po_value=0;$total_cut_subcon=0;$total_sew_out_subcon=0;$total_ex_factory=0;
						$i=1;
						if(str_replace("'","",$cbo_company_name)==0) $company_name_sub=""; else $company_name_sub="and a.company_id = $cbo_company_name";
						if($db_type==0)
						{
							$ex_factory_sql="select a.party_id, sum(c.order_quantity) as order_quantity
							from subcon_ord_mst a, subcon_gmts_prod_dtls b, subcon_ord_dtls c
							where a.subcon_job=c.job_no_mst and c.id=b.order_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and b.production_date between $txt_date_from and $txt_date_to $company_name_sub $subcon_work_comp $sub_floor_name $sub_location_cond group by a.party_id";
						}
						else
						{
							 $ex_factory_sql="select a.party_id, sum(c.order_quantity) as order_quantity
							from subcon_ord_mst a, subcon_gmts_prod_dtls b, subcon_ord_dtls c
							where a.subcon_job=c.job_no_mst and c.id=b.order_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and b.production_date between $txt_date_from and $txt_date_to $company_name_sub  $subcon_work_comp $sub_floor_name $sub_location_cond group by a.party_id";
						}
						//echo  $exfactory_sql;
						$ex_factory_sql_result=sql_select($ex_factory_sql);
						$ex_factory_arr=array();
						foreach($ex_factory_sql_result as $resRow)
						{
							$ex_factory_arr[$resRow[csf("party_id")]] = $resRow[csf("order_quantity")];
						}
						//var_dump($exfactory_arr);die;
						//print_r($ex_factory_arr);die;

						//@@@@@@@@@@@@@@@@@@@@@
						$sub_cut_sew_array=array();

						if($db_type==0)
						{
							$production_mst_sql= sql_select("SELECT  a.party_id,
							sum(CASE WHEN production_type ='1' THEN production_qnty ELSE 0 END) AS cutting_qnty,
							sum(CASE WHEN production_type ='7' THEN production_qnty ELSE 0 END) AS sewing_input_qnty,
							sum(CASE WHEN production_type ='2' THEN production_qnty ELSE 0 END) AS sewingout_qnty,
							sum(CASE WHEN production_type ='3' THEN production_qnty  ELSE 0 END) AS ironout_qnty,
							sum(CASE WHEN production_type ='4' THEN production_qnty  ELSE 0 END) AS gmts_fin_qnty
							from subcon_ord_mst a, subcon_gmts_prod_dtls b, subcon_ord_dtls c

							where c.job_no_mst=a.subcon_job and c.id=b.order_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and  c.is_deleted=0 and c.status_active=1 and b.production_date between $txt_date_from and $txt_date_to $company_name_sub  $subcon_work_comp $sub_location_cond $sub_floor_name group by a.party_id");
						}
						else
						{
							$production_mst_sql=sql_select("SELECT  a.party_id,
							sum(CASE WHEN production_type ='1' THEN production_qnty ELSE 0 END) AS cutting_qnty,
							sum(CASE WHEN production_type ='7' THEN production_qnty ELSE 0 END) AS sewing_input_qnty,

							sum(CASE WHEN production_type ='2' THEN production_qnty ELSE 0 END) AS sewingout_qnty,
							sum(CASE WHEN production_type ='3' THEN production_qnty  ELSE 0 END) AS ironout_qnty,
							sum(CASE WHEN production_type ='4' THEN production_qnty  ELSE 0 END) AS gmts_fin_qnty
							from subcon_ord_mst a, subcon_gmts_prod_dtls b, subcon_ord_dtls c
							where c.job_no_mst=a.subcon_job and c.id=b.order_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.production_date between $txt_date_from and $txt_date_to $company_name_sub $subcon_work_comp $sub_location_cond $sub_floor_name group by a.party_id");

						}
						foreach($production_mst_sql as $sql_result)
						{
							$sub_cut_sew_array[$sql_result[csf("party_id")]]['1']=$sql_result[csf("cutting_qnty")];
							$sub_cut_sew_array[$sql_result[csf("party_id")]]['7']=$sql_result[csf("sewing_input_qnty")];
							$sub_cut_sew_array[$sql_result[csf("party_id")]]['2']=$sql_result[csf("sewingout_qnty")];
							$sub_cut_sew_array[$sql_result[csf("party_id")]]['3']=$sql_result[csf("ironout_qnty")];
							$sub_cut_sew_array[$sql_result[csf("party_id")]]['4']=$sql_result[csf("gmts_fin_qnty")];
						}
						//var_dump($cutting_array);
						//@@@@@@@@@@@@@@@@@@@@@
						if($db_type==0)
						{
							$production_date_sql="SELECT a.party_id, sum(c.order_quantity) as po_quantity, sum(c.amount) as po_total_price
							from subcon_ord_mst a, subcon_gmts_prod_dtls b, subcon_ord_dtls c
							where c.job_no_mst=a.subcon_job and c.id=b.order_id and b.production_type in (1,2,7) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.production_date between $txt_date_from and $txt_date_to $company_name_sub $subcon_work_comp $sub_location_cond $sub_floor_name  group by a.party_id order by a.party_id ASC";
						}
						else
						{
							$production_date_sql="SELECT a.party_id, sum(c.order_quantity) as po_quantity, sum(c.amount) as po_total_price
							from subcon_ord_mst a, subcon_gmts_prod_dtls b, subcon_ord_dtls c
							where c.job_no_mst=a.subcon_job and c.id=b.order_id and b.production_type in (1,2,7) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.production_date between $txt_date_from and $txt_date_to $company_name_sub $subcon_work_comp $sub_location_cond $sub_floor_name group by a.party_id order by a.party_id ASC";
						}
						//echo $production_date_sql;//die;
						$pro_sql_result=sql_select($production_date_sql);
						foreach($pro_sql_result as $pro_date_sql_row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
								<td width="30"><? echo $i;?></td>
								<td width="120"><? echo $buyer_short_library[$pro_date_sql_row[csf("party_id")]]; ?></td>
								<td width="80" align="right"><? echo number_format($sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['1']); ?></td>
								<td width="80" align="right"><? echo number_format($sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['7']); ?></td>
								<td width="80" align="right"><? echo number_format($sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['2']); ?></td>
								<td width="80" align="right"><? echo number_format($sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['3']); ?></td>
								<td width="80" align="right"><? echo number_format($sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['4']); ?></td>
								<td align="right"><? echo number_format($sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['4']); ?></td>
							</tr>
							<?
							$total_cut_subcon+=$sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['1'];
							$total_input_subcon+=$sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['7'];
							$total_sew_out_subcon+=$sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['2'];
							$total_iron_subcon+=$sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['3'];
							$total_gmts_fin_subcon+=$sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['4'];
							$i++;
						}//end foreach 1st
						//$chart_data_qnty="Order Qty;".$total_po_quantity."\n"."Cutting;".$total_cut."\n"."Sew Out ;".$total_sew_out."\n"."Ex-Fact;".$total_ex_factory."\n";
						?>
						</table>
						<table border="1" class="tbl_bottom"  width="650" rules="all" id="" >
							<tr>
								<td width="30">&nbsp;</td>
								<td width="120" align="right">Total</td>
								<td width="80" id="tot_cutting"><? echo number_format($total_cut_subcon); ?></td>
								<td width="80" id="tot_input"><? echo number_format($total_input_subcon); ?></td>
								<td width="80" id="tot_sew_out"><? echo number_format($total_sew_out_subcon); ?></td>
								<td width="80" id="tot_iron_out"><? echo number_format($total_iron_subcon); ?></td>
								<td width="80" id="tot_gmt_fin_out"><? echo number_format($total_gmts_fin_subcon); ?></td>
								<td id="tot_delivery"><? echo number_format($total_gmts_fin_subcon); ?></td>
							</tr>
						</table>

							</div>
						</div>

					<?
					}
					?>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				</table>

			</div>
			<div>&nbsp;</div>
			<br />
			<? if($type==1) //--------------------------------------------Show Date Wise
			{

				?>
			   <h5 style="width:600px; background-color:#FCF; float:left;"><strong>Production-Regular Order</strong></h5>
				<table width="2642" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
					<thead>
					<tr>
						<th width="30" style="word-wrap: break-word;word-break: break-all;">Sl.</th>
						<th width="100" style="word-wrap: break-word;word-break: break-all;">Working Factory</th>
						<th width="100" style="word-wrap: break-word;word-break: break-all;">Job No</th>
						<th width="130" style="word-wrap: break-word;word-break: break-all;">Order Number</th>
						<th width="100" style="word-wrap: break-word;word-break: break-all;">Buyer Name</th>
						<th width="130" style="word-wrap: break-word;word-break: break-all;">Style Name</th>
						<th width="100" style="word-wrap: break-word;word-break: break-all;">File No</th>
						<th width="100" style="word-wrap: break-word;word-break: break-all;">Internal Ref</th>
						<th width="100" style="word-wrap: break-word;word-break: break-all;">Production Date</th>
						<th width="130" style="word-wrap: break-word;word-break: break-all;">Garments Color</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Cutting Inhouse</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Cutting Outbound</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Sent to prnt</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Rcv prn/Emb</th>

						<th width="80" style="word-wrap: break-word;word-break: break-all;">Sent to Emb</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Rcv Emb</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Sent to Wash</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Rcv Wash</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Sent to Sp. Works</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Rcv Sp. Works</th>

						<th width="80" style="word-wrap: break-word;word-break: break-all;">Sewing In (Inhouse)</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Sewing In (Out-bound)</th>
						<!-- <th width="80" style="word-wrap: break-word;word-break: break-all;">Total Sewing Input</th> -->
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Sewing Out (Inhouse)</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Sewing Out (Out-bound)</th>
						<!-- <th width="80" style="word-wrap: break-word;word-break: break-all;">Total Sewing Out</th> -->

						<th width="80" style="word-wrap: break-word;word-break: break-all;">Iron Qty (Inhouse)</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Iron Qty (Out-bound)</th>
						<!-- <th width="80" style="word-wrap: break-word;word-break: break-all;">Total Iron Qty</th> -->

						<!-- <th width="80" style="word-wrap: break-word;word-break: break-all;">Re-Iron Qty </th> -->
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Finish Qty (Inhouse)</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Finish Qty (Out-bound)</th>
						<!-- <th width="80" style="word-wrap: break-word;word-break: break-all;">Total Finish Qty</th> -->

						<!-- <th width="80" style="word-wrap: break-word;word-break: break-all;">Today Carton</th> -->
						<!-- <th width="80" style="word-wrap: break-word;word-break: break-all;">Prod/Dzn</th> -->
						<!-- <th width="80" style="word-wrap: break-word;word-break: break-all;">Reject Qty</th> -->
						<th style="word-wrap: break-word;word-break: break-all;">Ex-Fac. Qty</th>
						<!-- <th width="80" style="word-wrap: break-word;word-break: break-all;">Ex-Fac. FOB Value</th> -->
						<!-- <th width="80" style="word-wrap: break-word;word-break: break-all;">Ex-Fac Bal. Qty</th> -->
						<!-- <th width="80" style="word-wrap: break-word;word-break: break-all;">Ex- Fac. Bal. FOB Value</th> -->
						<!-- <th style="word-wrap: break-word;word-break: break-all;">Remarks</th> -->
						</tr>
					 </thead>
				</table>
				<div style="width:2660px; overflow-y: scroll; max-height:400px;" id="scroll_body" >
					<table cellspacing="0" border="1" class="rpt_table"  width="2642" rules="all" id="table_body" >
						<?
						$i=1;
						foreach($all_data_arr2 as $po_id=>$po_data)
						{
							foreach($po_data as $prod_date=>$prod_date_data)
							{	$r=0;
								foreach ($prod_date_data as $color_id => $row)
								{


									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									$cutting_qty=$print_qty=$printRec_qty=$emb_qty=$embRec_qty=$wash_qty=$washRec_qty=$special_qty=$specialRec_qty=$sewIn_inQty=$sewIn_outQty=$sewing_input_total=$sewOut_inQty=$sewOut_outQty=$sewing_output_total=$ironIn_qty=$ironOut_qty=$iron_qty_total=$reIron_qty=$finishIn_qty=$finishOut_qty=$finishing_qty=$carton_qty=$rejFinish_qty=$rejSewing_qty=$reject_Qty=0;

							  		$item_id  = $row['item_id'];
							  		$color_id = $row['color_id'];
							  		$size_id  = $row['size_id'];
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
										<td width="30" style="word-wrap: break-word;word-break: break-all;"><? echo $i;?></td>
										<td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo $company_library[$row['company']]; ?></p></td>
										<td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo $job_arr[$po_id]['job'];?></p></td>
										<td width="130" align="left" style="word-wrap: break-word;word-break: break-all;"><p><a href="##" onclick="openmypage_order(<? echo $po_id;?>,<? echo $item_id;?>,'orderQnty_popup');" ><? echo $job_arr[$po_id]['po']; ?></a></p></td>
										<td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo $buyer_short_library[$job_arr[$po_id]['buyer']]; ?></p></td>
										<td width="130" style="word-wrap: break-word;word-break: break-all;"><p><? echo $job_arr[$po_id]['style']; ?></p></td>
										<td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo $job_arr[$po_id]['file']; ?></p></td>
										<td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo $job_arr[$po_id]['ref']; ?></p></td>
										<? if($r==0){?>
										<td align="center" rowspan="<? echo $rowspan_arr[$po_id][$prod_date];?>"  width="100" style="word-wrap: break-word;word-break: break-all;vertical-align:middle;"><div><? echo change_date_format($prod_date); ?></div></td>
										<? $r++;} ?>
										<td width="130" style="word-wrap: break-word;word-break: break-all;"><p><? echo $color_library_arr[$color_id]; ?></p></td>

										<td title="<? echo $row['production_source'];?>" width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup2(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id;?>',<? echo $color_id; ?>,1,'cutting',1,'Cutting Info Inhouse','popup_bound');" ><? echo $row['cutting_inbound']; ?></a></td>

										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup2(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id;?>','<? echo $color_id; ?>',3,'cutting',2,'Cutting Info','popup_bound');" ><? echo $row['cutting_outbound']; ?></a></td>

	                                    <td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" title="Here"><a href="##" onClick="openmypage_popup2(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $color_id; ?>,1,'print',1,'Printing Issue Info','popup_bound');" ><? echo $row['print_inbound']; ?></a></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup2(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $color_id; ?>,0,'print',2,'Priniting Receive Info','popup_bound');" ><? echo $row['print_outbound']; ?></a></td>

										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup2(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $color_id; ?>,0,'embroidery',1,'Embroidery Issue Info','popup_bound');" ><? echo $row['sent_embel']; ?></a></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup2(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $color_id; ?>,0,'embroidery',2,'Embroidery Receive Info','popup_bound');" ><? echo $row['recive_embel']; ?></a></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup2(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $color_id; ?>,0,'wash',1,'Wash Issue Info','popup_bound');" ><? echo $row['sent_wash']; ?></a></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $color_id; ?>,0,'wash',2,'Wash Receive Info','popup_bound');" ><? echo $row['recive_wash']; ?></a></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup2(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $color_id; ?>,0,'sp',1,'Special Works Issue Info','popup_bound');" ><? echo $row['sent_sp']; ?></a></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup2(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $color_id; ?>,0,'sp',2,'Special Works Receive Info','popup_bound');" ><? echo $row['recive_sp']; ?></a> </td>

										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup2(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $color_id; ?>,1,'sewingin',1,'Sewing In Inhouse Receive Info','popup_bound');" ><? echo $row['sewing_in_inbound']; ?></a></td>
										<td width="80"  style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup2(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $color_id; ?>,3,'sewingin',2,'Sewing In Outbound Info','popup_bound');" ><? echo $row['sewing_in_outbound']; ?></a></td>


										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup2(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $color_id; ?>,1,'sewingout',1,'Sewing Out Inhouse Info','popup_bound');" ><? echo $row['sewing_out_inbound']; ?></a></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup2(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $color_id; ?>,3,'sewingout',2,'Sewing In Outbound Info','popup_bound');" ><? echo $row['sewing_out_outbound']; ?></a></td>

										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup2(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $color_id; ?>,0,'iron',1,'Iron Inhouse Info','popup_bound');" ><? echo $row['iron_inbound']; ?></a></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup2(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $color_id; ?>,0,'iron',2,'Iron Outbound Info','popup_bound');" ><? echo $row['iron_outbound']; ?></a></td>

										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup2(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $color_id; ?>,1,'finish',1,'Finishing Inhouse Info','popup_bound');"><? echo $row['finish_inbound']; ?></a></td>
										<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"> <a href="##"onClick="openmypage_popup2(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $color_id; ?>,3,'finish',2,'Finishing Outbound Info','popup_bound');"><? echo $row['finish_outbound']; ?></a></td>

										<td style="word-wrap: break-word;word-break: break-all;" align="right"><? echo $ex_fact=$ex_fac_arr_po_datewise[$po_id][$prod_date]; ?></td>

									</tr>
									<?
									$tot_cutting_qty_in+=$row['cutting_inbound'];
									$tot_cutting_qty_out+=$row['cutting_outbound'];
									$tot_print_in_qty+=$row['print_inbound'];
									$tot_print_out_qty+=$row['print_outbound'];
									$tot_emb_qty+=$row['sent_embel'];
									$tot_embRec_qty+=$row['recive_embel'];
									$tot_wash_qty+=$row['sent_wash'];
									$tot_washRec_qty+=$row['recive_wash'];
									$tot_special_qty+=$row['sent_sp'];
									$tot_specialRec_qty+=$row['recive_sp'];
									$tot_sewIn_inQty+=$row['sewing_in_inbound'];
									$tot_sewIn_outQty+=$row['sewing_in_outbound'];
									$tot_sewOut_inQty+=$row['sewing_out_inbound'];
									$tot_sewOut_outQty+=$row['sewing_out_outbound'];
									$tot_ironIn_qty+=$row['iron_inbound'];
									$tot_ironOut_qty+=$row['iron_outbound'];
									$tot_finishIn_qty+=$row['finish_inbound'];
									$tot_finishOut_qty+=$row['finish_outbound'];
									$tot_ex_fac+=$ex_fact;
									$i++;
								}
							}
						}
						//unset($date_sql_result);
					?>

					</table>
					<table width="2642" cellspacing="0" border="1" class="tbl_bottom" rules="all" id="report_table_footer" >
						<tr>

							<td width="30" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
							<td width="100" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
							<td width="100" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
							<td width="130" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
							<td width="100" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
							<td width="130" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
							<td width="100" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
							<td width="100" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
							<td width="100" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
							<td width="130" style="word-wrap: break-word;word-break: break-all;" align="right">Total</td>
							<td width="80"  style="word-wrap: break-word;word-break: break-all;" align="right" id="" ><? echo $tot_cutting_qty_in;?></td>
							<td width="80"  style="word-wrap: break-word;word-break: break-all;" align="right" id="" ><? echo $tot_cutting_qty_out;?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id=""><? echo $tot_print_in_qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id=""><?  echo $tot_print_out_qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id=""><? echo $tot_emb_qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id=""><? echo $tot_embRec_qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id=""><? echo $tot_wash_qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id=""><? echo $tot_washRec_qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id=""><? echo $tot_special_qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id=""><? echo $tot_specialRec_qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id=""><? echo $tot_sewIn_inQty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id=""><? echo $tot_sewIn_outQty; ?></td>

							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id=""><? echo $tot_sewOut_inQty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id=""><? echo $tot_sewOut_outQty; ?></td>

							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id=""><?  echo $tot_ironIn_qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id=""><?  echo $tot_ironOut_qty; ?></td>

							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id=""><? echo $tot_finishIn_qty; ?></td>
							<td width="80"  style="word-wrap: break-word;word-break: break-all;"align="right" id=""><? echo $tot_finishOut_qty; ?></td>

							<td style="word-wrap: break-word;word-break: break-all;" align="right" id=""><? echo number_format($tot_ex_fac,2); ?></td >

					</tr>
					</table>

				</div>


				<?
			}
			// end if condition of type //-------------------------------------------END Show Date Wise------------------------
			else if($type==2)//-------------------------------------------Show Date Location Floor & Line
			{

				?>
			   <h5 style="width:600px; background-color:#FCF; float:left;"><strong>Production-Regular Order</strong></h5>
				<table width="2952" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
					<thead>
					<tr>
						<th width="40" style="word-wrap: break-word;word-break: break-all;">Sl</th>
						<th width="100" style="word-wrap: break-word;word-break: break-all;">Working Factory</th>
						<th width="100" style="word-wrap: break-word;word-break: break-all;">Job No</th>
						<th width="130" style="word-wrap: break-word;word-break: break-all;">Order Number</th>
						<th width="100" style="word-wrap: break-word;word-break: break-all;">Buyer Name</th>
						<th width="130" style="word-wrap: break-word;word-break: break-all;">Style Name</th>
						<th width="100" style="word-wrap: break-word;word-break: break-all;">File No</th>
						<th width="100" style="word-wrap: break-word;word-break: break-all;">Internal Ref</th>
						<th width="100" style="word-wrap: break-word;word-break: break-all;">Location</th>
						<th width="100" style="word-wrap: break-word;word-break: break-all;">Floor</th>

						<th width="100" style="word-wrap: break-word;word-break: break-all;">Sewing Line</th>

						<th width="100" style="word-wrap: break-word;word-break: break-all;">Production Date</th>
						<th width="130" style="word-wrap: break-word;word-break: break-all;">Garments Color</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Cutting Inhouse</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Cutting Outbound</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Sent to prnt</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Rcv prn/Emb</th>

						<th width="80" style="word-wrap: break-word;word-break: break-all;">Sent to Emb</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Rcv Emb</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Sent to Wash</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Rcv Wash</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Sent to Sp. Works</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Rcv Sp. Works</th>

						<th width="80" style="word-wrap: break-word;word-break: break-all;">Sewing In (Inhouse)</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Sewing In (Out-bound)</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Sewing Out (Inhouse)</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Sewing Out (Out-bound)</th>


						<th width="80" style="word-wrap: break-word;word-break: break-all;">Finishing Rcv</th>

						<th width="80" style="word-wrap: break-word;word-break: break-all;">Iron Qty (Inhouse)</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Iron Qty (Out-bound)</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Finish Qty (Inhouse)</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Finish Qty (Out-bound)</th>
						<th style="word-wrap: break-word;word-break: break-all;">Ex-Fac. Qty</th>
						</tr>
					 </thead>
				</table>

				<div style="width:2960px; overflow-y: scroll; max-height:400px;" id="scroll_body" >
					<table cellspacing="0" border="1" class="rpt_table"  width="2942" rules="all" id="table_body" >
						<?
						$i=1;
						$fin_rcv_qty=0;
						foreach($all_data_arr3 as $po_id=>$po_data)
						{
							foreach($po_data as $prod_date=>$prod_date_data)
							{	$r=0;
								foreach($prod_date_data as $location_id=>$location_data)
								{
									foreach($location_data as $floor_id=>$floor_data)
									{
										foreach ($floor_data as $color_id => $row)
										{

											$line_id = $row['sewing_line'];

										if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										$cutting_qty=$print_qty=$printRec_qty=$emb_qty=$embRec_qty=$wash_qty=$washRec_qty=$special_qty=$specialRec_qty=$sewIn_inQty=$sewIn_outQty=$sewing_input_total=$sewOut_inQty=$sewOut_outQty=$sewing_output_total=$ironIn_qty=$ironOut_qty=$iron_qty_total=$reIron_qty=$finishIn_qty=$finishOut_qty=$finishing_qty=$carton_qty=$rejFinish_qty=$rejSewing_qty=$reject_Qty=0;

										$sewing_line='';
														if($row['prod_reso_allo']==1)
														{
															$line_number=explode(",",$prod_reso_arr[$line_id]);

															foreach($line_number as $val)
															{
																if($sewing_line=='') $sewing_line=$line_library[$val]; else $sewing_line.=", ".$line_library[$val];
															}
														}
														else $sewing_line=$line_library[$line_id];


														$item_id  = $row['item_id'];
														// $color_id = $row['color_id'];
														$size_id  = $row['size_id'];
													  $company = $row['company_id'];
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
											<td width="30" style="word-wrap: break-word;word-break: break-all;"><? echo $i;?></td>
											<td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo $company_library[$row['company']]; ?></p></td>
											<td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo $job_arr[$po_id]['job'];?></p></td>
											<td width="130" style="word-wrap: break-word;word-break: break-all;"><p><a href="##" onclick="openmypage_order(<? echo $po_id;?>,<? echo $item_id;?>,'orderQnty_popup');" ><? echo $job_arr[$po_id]['po']; ?></a></p></td>
											<td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo $buyer_short_library[$job_arr[$po_id]['buyer']]; ?></p></td>
											<td width="130" style="word-wrap: break-word;word-break: break-all;"><p><? echo $job_arr[$po_id]['style']; ?></p></td>
											<td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo $job_arr[$po_id]['file']; ?></p></td>
											<td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo $job_arr[$po_id]['ref']; ?></p></td>
											<td align="left" valign="middle" width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo $location_library[$location_id]; ?></p></td>
											<td align="left" valign="middle" width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo $floor_library[$floor_id]; ?></p></td>
											<td align="left" valign="middle" width="100" style="word-wrap: break-word;word-break: break-all;"><p><?= rtrim($sewing_line,",") ;?></p></td>


											<? if($r==0){?>
											<td  valign="middle" rowspan="<? echo $rowspan_arr2[$po_id][$prod_date];?>"  width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo change_date_format($prod_date); ?></p></td>
											<? $r++;  }  ?>
											<td width="130" style="word-wrap: break-word;word-break: break-all;"><p><? echo $color_library_arr[$color_id]; ?></p></td>


											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup2(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id;?>',<? echo $color_id; ?>,<? echo $size_id; ?>,'cutting',1,'Cutting Info Inhouse','popup_bound');" ><? echo $row['cutting_inbound']; ?></a></td>



											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup2(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id;?>','<? echo $color_id; ?>','<? echo $size_id; ?>','cutting',2,'Cutting Info','popup_bound');" ><? echo $row['cutting_outbound']; ?></a></td>

		                                    <td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" title="Here"><a href="##" onClick="openmypage_popup2(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $color_id; ?>,<? echo $size_id; ?>,'print',1,'Printing Issue Info','popup_bound');" ><? echo $row['print_inbound']; ?></a></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup2(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $color_id; ?>,<? echo $size_id; ?>,'print',2,'Priniting Receive Info','popup_bound');" ><? echo $row['print_outbound']; ?></a></td>

											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup2(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $color_id; ?>,<? echo $size_id; ?>,'embroidery',1,'Embroidery Issue Info','popup_bound');" ><? echo $row['sent_embel']; ?></a></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup2(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $color_id; ?>,<? echo $size_id; ?>,'embroidery',2,'Embroidery Receive Info','popup_bound');" ><? echo $row['recive_embel']; ?></a></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup2(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $color_id; ?>,<? echo $size_id; ?>,'wash',1,'Wash Issue Info','popup_bound');" ><? echo $row['sent_wash']; ?></a></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $color_id; ?>,<? echo $size_id; ?>,'wash',2,'Wash Receive Info','popup_bound');" ><? echo $row['recive_wash']; ?></a></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup2(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $color_id; ?>,<? echo $size_id; ?>,'sp',1,'Special Works Issue Info','popup_bound');" ><? echo $row['sent_sp']; ?></a></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup2(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $color_id; ?>,<? echo $size_id; ?>,'sp',2,'Special Works Receive Info','popup_bound');" ><? echo $row['recive_sp']; ?></a> </td>

											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup2(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $color_id; ?>,<? echo $size_id; ?>,'sewingin',1,'Sewing In Inhouse Receive Info','popup_bound');" ><? echo $row['sewing_in_inbound']; ?></a></td>
											<td width="80"  style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup2(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $color_id; ?>,<? echo $size_id; ?>,'sewingin',2,'Sewing In Outbound Info','popup_bound');" ><? echo $row['sewing_in_outbound']; ?></a></td>


											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup2(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $color_id; ?>,<? echo $size_id; ?>,'sewingout',1,'Sewing Out Inhouse Info','popup_bound');" ><? echo $row['sewing_out_inbound']; ?></a></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup2(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $color_id; ?>,<? echo $size_id; ?>,'sewingout',2,'Sewing In Outbound Info','popup_bound');" ><? echo $row['sewing_out_outbound']; ?></a></td>




											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_rec_popup2(<? echo $po_id; ?>,<? echo $company; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $color_id; ?>,<? echo $size_id; ?>,'sewingout',2,'finishing Rcv','fin_rcv_popup');" ><? echo number_format($row['fin_receive_qnty'],0) ?></a></td>




											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup2(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $color_id; ?>,<? echo $size_id; ?>,'iron',1,'Iron Inhouse Info','popup_bound');" ><? echo $row['iron_inbound']; ?></a></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup2(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $color_id; ?>,<? echo $size_id; ?>,'iron',2,'Iron Outbound Info','popup_bound');" ><? echo $row['iron_outbound']; ?></a></td>

											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup2(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $color_id; ?>,<? echo $size_id; ?>,'finish',1,'Finishing Inhouse Info','popup_bound');"><? echo $row['finish_inbound']; ?></a></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"> <a href="##"onClick="openmypage_popup2(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $color_id; ?>,<? echo $size_id; ?>,'finish',2,'Finishing Outbound Info','popup_bound');"><? echo $row['finish_outbound']; ?></a></td>

											<td  style="word-wrap: break-word;word-break: break-all;" align="right"><? echo $ex_fact=$ex_fac_arr_po_datewise[$po_id][$prod_date]; ?></td>

										</tr>
										<?
										$tot_cutting_qty_in+=$row['cutting_inbound'];
										$tot_cutting_qty_out+=$row['cutting_outbound'];
										$tot_print_in_qty+=$row['print_inbound'];
										$tot_print_out_qty+=$row['print_outbound'];
										$tot_emb_qty+=$row['sent_embel'];
										$tot_embRec_qty+=$row['recive_embel'];
										$tot_wash_qty+=$row['sent_wash'];
										$tot_washRec_qty+=$row['recive_wash'];
										$tot_special_qty+=$row['sent_sp'];
										$tot_specialRec_qty+=$row['recive_sp'];
										$tot_sewIn_inQty+=$row['sewing_in_inbound'];
										$tot_sewIn_outQty+=$row['sewing_in_outbound'];
										$tot_sewOut_inQty+=$row['sewing_out_inbound'];
										$tot_sewOut_outQty+=$row['sewing_out_outbound'];
										$tot_ironIn_qty+=$row['iron_inbound'];
										$tot_ironOut_qty+=$row['iron_outbound'];
										$tot_finishIn_qty+=$row['finish_inbound'];
										$tot_finishOut_qty+=$row['finish_outbound'];
										$fin_rcv_qty+=$row['fin_receive_qnty'] ;
										$tot_ex_fac+=$ex_fact;
										$i++;
										}
									}
								}
							}
						}
						//unset($date_sql_result);
					?>

					</table>
					<table width="2942" cellspacing="0" border="1" class="tbl_bottom" rules="all" id="report_table_footer" >
						<tr>

							<td width="30" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
							<td width="100" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
							<td width="100" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
							<td width="130" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
							<td width="100" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
							<td width="130" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
							<td width="100" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
							<td width="100" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
							<td width="100" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
							<td width="100" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
							<td width="100" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
							<td width="230" style="word-wrap: break-word;word-break: break-all;" align="right">Total</td>
							<td width="80"  style="word-wrap: break-word;word-break: break-all;" align="right" id="" ><? echo $tot_cutting_qty_in;?></td>
							<td width="80"  style="word-wrap: break-word;word-break: break-all;" align="right" id="" ><? echo $tot_cutting_qty_out;?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id=""><? echo $tot_print_in_qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id=""><?  echo $tot_print_out_qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id=""><? echo $tot_emb_qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id=""><? echo $tot_embRec_qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id=""><? echo $tot_wash_qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id=""><? echo $tot_washRec_qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id=""><? echo $tot_special_qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id=""><? echo $tot_specialRec_qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id=""><? echo $tot_sewIn_inQty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id=""><? echo $tot_sewIn_outQty; ?></td>

							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id=""><? echo $tot_sewOut_inQty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id=""><? echo $tot_sewOut_outQty; ?></td>

							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id=""><?  echo $fin_rcv_qty ; ?></td>

							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id=""><?  echo $tot_ironIn_qty; ?></td>


							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id=""><?  echo $tot_ironOut_qty; ?></td>

							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id=""><? echo $tot_finishIn_qty; ?></td>
							<td width="80"  style="word-wrap: break-word;word-break: break-all;"align="right" id=""><? echo $tot_finishOut_qty; ?></td>

							<td style="word-wrap: break-word;word-break: break-all;" align="right" id=""><? echo number_format($tot_ex_fac,2); ?></td >

					</tr>
					</table>

				</div>


				<?
			}// end if condition of type

			if(str_replace("'","",trim($cbo_subcon))==2) //yes
			{
				$fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
				$toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );
				$buyer_name = str_replace("'","",trim($cbo_buyer_name));

				if(str_replace("'","",$cbo_floor)==0) $sub_floor_name="";else $sub_floor_name=" and b.floor_id in($cbo_floor)";
				if ($location==0) $sub_location_cond=""; else $sub_location_cond=" and b.location_id in($location) ";
				if ($buyer_name==0) $sub_buyer_cond=""; else $sub_buyer_cond=" and a.party_id in($buyer_name) ";
				?>

					<h5 style="width:800px;float: left; background-color:#FCF"><strong>Production-Subcontract Order (Inbound) Details</strong></h5>

						<table width="1610" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_3">
							<thead>
								<tr>
									<th width="30">Sl.</th>
									<th width="100">Working Factory</th>
									<th width="100">Job No</th>
									<th width="130">Order No</th>
									<th width="100">Buyer </th>
									<th width="130">Style </th>
									<th width="130">Item Name</th>
									<th width="75">Production Date</th>
									<th width="130">Garments Color</th>

									<th width="90">Cutting</th>
									<th width="90">Sewing Input</th>
									<th width="90">Sewing Output</th>
									<th width="90">Iron Output</th>
									<th width="90">Gmts. Finishing</th>
									<th width="90">Total Delivery</th>
									<th width="">Remarks</th>
								</tr>
							</thead>
						</table>
					<div style="max-height:300px; overflow-y:scroll; width:1630px" id="scroll_body2">
						<table border="1"  class="rpt_table"  width="1610" rules="all" id="sub_list_views">
							  <?
								$item_id_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
								$subcon_lc_comp2=str_replace("a.", "c.", $subcon_lc_comp);
								$subcon_work_comp2=str_replace("a.", "c.", $subcon_work_comp);

								// ====================== FOR PRODUCTION ===========================
								$production_array=array();
								if($db_type==0)
								{
									$prod_sql= "SELECT c.order_id, c.production_date,
										NVL(sum(CASE WHEN c.production_type ='1' THEN  c.production_qnty  ELSE 0 END),0) AS cutting_qnty,
										sum(CASE WHEN c.production_type ='7' THEN  c.production_qnty  ELSE 0 END) AS sewing_input_qnty,

										NVL(sum(CASE WHEN c.production_type ='2' THEN  c.production_qnty  ELSE 0 END),0) AS sewingout_qnty,
										NVL(sum(CASE WHEN c.production_type ='3' THEN  c.production_qnty  ELSE 0 END),0) AS ironout_qnty,
										NVL(sum(CASE WHEN c.production_type ='4' THEN  c.production_qnty  ELSE 0 END),0) AS gmts_fin_qnty,
										d.color_id, d.item_id, d.size_id
									from
										subcon_gmts_prod_dtls c, subcon_ord_breakdown d
									where c.order_id=d.order_id and c.status_active=1 and c.is_deleted=0 and c.production_date between $txt_date_from and $txt_date_to $subcon_lc_comp2 $subcon_work_comp2 group by c.order_id, c.production_date,d.color_id, d.item_id, d.size_id ";
								}
								else
								{
									 $prod_sql= "SELECT c.order_id, c.production_date,c.company_id,c.location_id,c.floor_id,
										NVL(sum(CASE WHEN c.production_type ='1' THEN  c.production_qnty  ELSE 0 END),0) AS cutting_qnty,
										sum(CASE WHEN c.production_type ='7' THEN  c.production_qnty  ELSE 0 END) AS sewing_input_qnty,

										NVL(sum(CASE WHEN c.production_type ='2' THEN  c.production_qnty  ELSE 0 END),0) AS sewingout_qnty,
										NVL(sum(CASE WHEN c.production_type ='3' THEN  c.production_qnty  ELSE 0 END),0) AS ironout_qnty,
										NVL(sum(CASE WHEN c.production_type ='4' THEN  c.production_qnty  ELSE 0 END),0) AS gmts_fin_qnty,
										d.color_id, d.item_id, d.size_id
									from
										subcon_gmts_prod_dtls c, subcon_ord_breakdown d
									where c.order_id=d.order_id and c.status_active=1 and c.is_deleted=0 and c.production_date between $txt_date_from and $txt_date_to $subcon_lc_comp2 $subcon_work_comp2 group by c.order_id, c.production_date,c.company_id,c.location_id,c.floor_id,d.color_id, d.item_id, d.size_id ";
								}
								$prod_sql_result= sql_select($prod_sql);
								 // echo $prod_sql;//die;
								foreach($prod_sql_result as $proRes)
								{
									$production_array[$proRes[csf("order_id")]][$proRes[csf("production_date")]][$proRes[csf("color_id")]]['cutting_qnty']=$proRes[csf("cutting_qnty")];
									$production_array[$proRes[csf("order_id")]][$proRes[csf("production_date")]][$proRes[csf("color_id")]]['sewing_input_qnty']=$proRes[csf("sewing_input_qnty")];
									$production_array[$proRes[csf("order_id")]][$proRes[csf("production_date")]][$proRes[csf("color_id")]]['sewingout_qnty']=$proRes[csf("sewingout_qnty")];
									$production_array[$proRes[csf("order_id")]][$proRes[csf("production_date")]][$proRes[csf("color_id")]]['ironout_qnty']=$proRes[csf("ironout_qnty")];
									$production_array[$proRes[csf("order_id")]][$proRes[csf("production_date")]][$proRes[csf("color_id")]]['gmts_fin_qnty']=$proRes[csf("gmts_fin_qnty")];

								}
								// echo "<pre>";
								// print_r($production_array);
								// ******************* FOR DELIVERY ***********************
								$delivery_array=array();
								if($db_type==0)
								{
									$del_sql= "SELECT  e.order_id, SUM (e.delivery_qty) AS delivery_qty
									from
										subcon_delivery_mst d, subcon_delivery_dtls e
									where d.id = e.mst_id and d.status_active=1 and d.is_deleted=0
									group by e.order_id";
								}
								else
								{
									$del_sql= "SELECT  e.order_id, SUM (e.delivery_qty) AS delivery_qty
									from
										subcon_delivery_mst d, subcon_delivery_dtls e
									where d.id = e.mst_id and d.status_active=1 and d.is_deleted=0
									group by e.order_id";
								}
								$del_sql_result= sql_select($del_sql);
								//echo $del_sql;//die;
								foreach($del_sql_result as $delRes)
								{
									$delivery_array[$delRes[csf("order_id")]]['delivery_qty']=$delRes[csf("delivery_qty")];
								}
								// echo "<pre>";
								// print_r($delivery_array);
								if($db_type==0)
								{
									$order_sql= "SELECT c.id, c.order_no, c.cust_style_ref, a.company_id, a.party_id, a.location_id, a.job_no_prefix_num, b.gmts_item_id, sum(CASE WHEN b.order_id=c.id THEN b.production_qnty ELSE 0 END) AS production_qnty, b.production_date,d.color_id
									from
										subcon_ord_mst a, subcon_gmts_prod_dtls b, subcon_ord_dtls c, subcon_ord_breakdown d
									where
										 b.order_id=c.id and b.order_id=d.order_id and b.production_date between $txt_date_from and $txt_date_to $subcon_lc_comp $subcon_work_comp $sub_floor_name $sub_location_cond and a.subcon_job=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
										 group by b.order_id, b.production_date order by b.production_date,d.color_id";
								}
								else
								{
									 $order_sql= "SELECT c.id, c.order_no, c.cust_style_ref, a.company_id, a.party_id, a.location_id, a.job_no_prefix_num, b.gmts_item_id, sum(CASE WHEN b.order_id=c.id THEN b.production_qnty ELSE 0 END) AS production_qnty, b.production_date,d.color_id
									from
										subcon_ord_mst a, subcon_gmts_prod_dtls b, subcon_ord_dtls c, subcon_ord_breakdown d
									where
										 b.order_id=c.id and b.order_id=d.order_id and b.production_date between $txt_date_from and $txt_date_to $subcon_lc_comp $subcon_work_comp $sub_floor_name $sub_location_cond $sub_buyer_cond and a.subcon_job=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
										 group by c.id, c.order_no,c.cust_style_ref,a.company_id,a.party_id,a.location_id,a.job_no_prefix_num, b.gmts_item_id, b.production_date, d.color_id order by b.production_date";
								}

							    // echo $order_sql; //die;

								$order_sql_result=sql_select($order_sql);
								   $j=0;$k=0;
								   //$po_item_line_array=array();
								   $order_data_arr = array();
								   foreach($order_sql_result as $orderRes)
								   {
										$order_data_arr[$orderRes[csf("id")]][$orderRes[csf("production_date")]][$orderRes[csf("color_id")]]['company'] = $orderRes[csf("company_id")];
										$order_data_arr[$orderRes[csf("id")]][$orderRes[csf("production_date")]][$orderRes[csf("color_id")]]['party_id'] = $orderRes[csf("party_id")];
										$order_data_arr[$orderRes[csf("id")]][$orderRes[csf("production_date")]][$orderRes[csf("color_id")]]['style'] = $orderRes[csf("cust_style_ref")];
										$order_data_arr[$orderRes[csf("id")]][$orderRes[csf("production_date")]][$orderRes[csf("color_id")]]['job'] = $orderRes[csf("job_no_prefix_num")];
										$order_data_arr[$orderRes[csf("id")]][$orderRes[csf("production_date")]][$orderRes[csf("color_id")]]['order_no'] = $orderRes[csf("order_no")];
										$order_data_arr[$orderRes[csf("id")]][$orderRes[csf("production_date")]][$orderRes[csf("color_id")]]['item_id'] = $orderRes[csf("gmts_item_id")];


								   	}
							   		// echo "<pre>";
							   		// print_r($order_data_arr);
								   	$subcon_rowspan_arr = array();
								   	foreach($order_data_arr as $po_id=>$order_data)
								    {
									   	foreach ($order_data as $prod_date => $date_data)
									   	{
									   		foreach ($date_data as $color_id => $row)
									   		{
									   			if(isset($subcon_rowspan_arr[$po_id.$prod_date]))
									   			{
									   				$subcon_rowspan_arr[$po_id.$prod_date] +=1;
									   			}
									   			else
									   			{
									   				$subcon_rowspan_arr[$po_id.$prod_date] =1;
									   			}

										  	}
								   		}
								    }
								    // echo "<pre>";
								    // print_r($subcon_rowspan_arr);
								   	$total_cutt = 0;
									$total_sew_input = 0;
									$total_sew = 0;
									$total_iron_sub = 0;
									$total_gmtfin = 0;
									$total_del = 0;
								   foreach($order_data_arr as $po_id=>$order_data)
								   {
									   	foreach ($order_data as $prod_date => $date_data)
									   	{
									   		$rr=0;
									   		foreach ($date_data as $color_id => $row)
									   		{
											   		$j++;
											   		if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
											   		$item_id = $row["item_id"];
											   		?>
											   		<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_3nd<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_3nd<? echo $j; ?>" style="height:20px">
											   			<td width="30" ><? echo $j; ?></td>
											   			<td width="100"><p><? echo $company_library[$row["company"]]; ?></p></td>
											   			<td width="100" align="center"><p><? echo $row["job"]; ?></p></td>
											   			<td width="130"><p><? echo $row["order_no"]; ?></p></td>
											   			<td width="100"><? echo $buyer_short_library[$row["party_id"]]; ?></td>
											   			<td width="130"><p><? echo $row["style"]; ?></p></td>
											   			<td width="130"><p><? echo $garments_item[$row["item_id"]];?></p></td>
											   			<? if($rr==0){?>
											   			<td style="vertical-align:middle;" rowspan="<? echo $subcon_rowspan_arr[$po_id.$prod_date];?>" valing="middle" align="center" width="75" bgcolor="<? echo $color; ?>"><? echo change_date_format($prod_date);  ?></td>
											   			<?}$rr++;?>
											   			<td width="130" bgcolor="<? echo $color; ?>"><? echo $color_library_arr[$color_id];  ?></td>

											   			<td width="90" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__0';?>','1','1','production_popup_subcon');" ><? echo $cutting= $production_array[$po_id][$prod_date][$color_id]['cutting_qnty'];  ?></a></td>
											   			<td width="90" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__0';?>','1','7','production_popup_subcon');" ><? echo $input= $production_array[$po_id][$prod_date][$color_id]['sewing_input_qnty']; ?></a></td>

											   			<td width="90" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__0';?>','1','2','production_popup_subcon');" ><? echo $output=$production_array[$po_id][$prod_date][$color_id]['sewingout_qnty'];?></a></td>
											   			<td width="90" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__0';?>','1','3','production_popup_subcon');" ><? echo $iron= $production_array[$po_id][$prod_date][$color_id]['ironout_qnty']; ?></a></td>
											   			<td width="90" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__0';?>','1','4','production_popup_subcon');" ><? echo $fin= $production_array[$po_id][$prod_date][$color_id]['gmts_fin_qnty']; ?></a></td>
											   			<td align="right" width="90" title="Total Delivery"><? $delivery = $delivery_array[$row[csf('id')]]['delivery_qty']; echo number_format($delivery); ?></td>
											   			<td width="">&nbsp;</td>
											   		</tr>
											   		<?
											   		//$po_item_line_array[$orderRes[csf("id")]][$orderRes[csf("gmt_item_id")]]=$orderRes[csf("line_id")];

											   	//}
											   	$total_cutt+=$cutting;
												$total_sew_input += $input;
												$total_sew += $output;
												$total_iron_sub += $iron;
												$total_gmtfin += $fin;
											  	$total_del += $delivery;
										  	}
								   		}
								   }
								  ?>
								</table>

								<table border="1" class="tbl_bottom"  width="1610" rules="all" id="report_table_footer2" >
									<tr>
										<td width="30">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="130">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="130">&nbsp;</td>
										<td width="130">&nbsp;</td>

										<td width="75">&nbsp;</td>
										<td width="130">Total:</td>

										<td width="90" id=""><? echo $total_cutt; ?></td>
										<td width="90" id=""><? echo $total_sew_input; ?></td>
										<td width="90" id=""><? echo $total_sew; ?></td>
										<td width="90" id=""><? echo $total_iron_sub; ?></td>
										<td width="90" id=""><? echo $total_gmtfin; ?></td>
										<td width="90" id=""><? echo $total_del; ?></td>
										<td width=""></td>
									 </tr>
							 </table>

					</div>


				<?
			}
			//-------------------------------------------END Show Date Location Floor & Line Wise------------------------
			?>
		</div><?
		$html = ob_get_contents();
		ob_clean();
		//$new_link=create_delete_report_file( $html, 1, 1, "../../../" );

		// echo "$html####";
		foreach (glob($user_id."_*.xls") as $filename)
		{
			@unlink($filename);
		}
		$name=$user_id."_".time().".xls";
		$create_new_excel = fopen($name, 'w');
		$is_created = fwrite($create_new_excel,$html);
		//$new_link=create_delete_report_file( $html, 1, 1, "../../../" );
		echo $html."####".$name;
		exit();
	}
	elseif($ReportType==4)// Show Button 4 by thorat
	{
		$garments_nature=str_replace("'","",$cbo_garments_nature);
		$cbo_floor=str_replace("'","",$cbo_floor);
		//echo $cbo_floor;
		//if($garments_nature==1)$garments_nature="";
		 if($garments_nature==1) $garmentsNature=""; else $garmentsNature=" and garments_nature=$garments_nature";

		$location = str_replace("'","",$cbo_location);
		if(str_replace("'","",$cbo_company_name)==0) $company_name=""; else $company_name=" and b.company_name=$cbo_company_name";
		if(str_replace("'","",$cbo_buyer_name)==0)$buyer_name="";else $buyer_name=" and a.buyer_name=$cbo_buyer_name";
		if($cbo_floor==0) $floor_name="";else $floor_name=" and floor_id in($cbo_floor)";
		//echo $floor_name;die;
		if($cbo_floor==0) $floor_id="";else $floor_id=" and floor_id in($cbo_floor)";
		if ($location==0) $location_cond=""; else $location_cond=" and location in($location) ";

		if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="")$txt_date="";
		else $txt_date=" and production_date between $txt_date_from and $txt_date_to";

		$fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
		$toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );
		//cbo_garments_nature
		$file_no = str_replace("'","",$txt_file_no);
		$internal_ref = str_replace("'","",$txt_internal_ref);
		$cbo_company_name = str_replace("'","",$cbo_company_name);
		$cbo_com_fac_name = str_replace("'","",$cbo_com_fac_name);
		$cbo_buyer_name = str_replace("'","",$cbo_buyer_name);
		$txt_style_ref  =  str_replace("'","",$txt_style_ref);

		if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='".trim($file_no)."' ";

		if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='".trim($internal_ref)."' ";

		if ($txt_style_ref=="") $style_ref_cond=""; else $style_ref_cond=" and a.style_ref_no='".trim($txt_style_ref)."' ";

		if ($location=="" || $location==0 ) $location_cond=""; else $location_cond=" and location in($location) ";

		if($cbo_com_fac_name=="") $working_factory_cond=""; else $working_factory_cond=" and serving_company in($cbo_com_fac_name)";
		// echo $working_factory_cond;
		if(str_replace("'","",$cbo_company_name)==0) $company_name_cond=""; else $company_name_cond=" and company_id=$cbo_company_name";

		$variable_setting = sql_select("select EX_FACTORY from variable_settings_production where company_name=$cbo_company_name and variable_list=1 and status_active=1");

		$exfactory_level = $variable_setting[0]["EX_FACTORY"];

        $job_arr=array();
        $job_arr2=array();
        if(str_replace("'","",$cbo_company_name)==0) $job_comp_cond=""; else $job_comp_cond="and a.company_name=$cbo_company_name ";
        /*==========================================================================================/
        /										main query											/
        /========================================================================================= */
		$job_sql="SELECT a.job_no_prefix_num, a.buyer_name, a.style_ref_no, a.company_name, b.id, b.po_number, b.grouping, b.file_no, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b, pro_garments_production_mst c where a.id=b.job_id and b.id=c.po_break_down_id and a.is_deleted=0 and c.is_deleted=0 and c.status_active=1 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $job_comp_cond $buyer_name $file_no_cond $internal_ref_cond $style_ref_cond $txt_date";

		// echo $job_sql;

		//and c.production_date between $txt_date_from and $txt_date_to

        $job_sql_res=sql_select($job_sql);

        $tot_rows=0;
        $poIds='';
        $poIds_array = array();
        foreach($job_sql_res as $row)
        {
            $tot_rows++;
            $poIds.=$row[csf("id")].",";
            $poIds_array[$row[csf("id")]]=$row[csf("id")];
            $job_arr[$row[csf("id")]]['job']=$row[csf("job_no_prefix_num")];
            $job_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
            $job_arr[$row[csf("id")]]['buyer']=$row[csf("buyer_name")];
            $job_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
            $job_arr[$row[csf("id")]]['file']=$row[csf("file_no")];
            $job_arr[$row[csf("id")]]['ref']=$row[csf("grouping")];
            $job_arr2[$row[csf("id")]]['pub_shipment_date']=$row[csf("pub_shipment_date")];
        }
        unset($job_sql_res);

        $txt_date_ex = str_replace("production_date", "ex_factory_date", $txt_date);
        $job_sql_ex="SELECT a.job_no_prefix_num, a.buyer_name, a.style_ref_no, a.company_name, b.id, b.po_number, b.grouping, b.file_no, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c where a.id=b.job_id and b.id=c.po_break_down_id and a.is_deleted=0 and c.is_deleted=0 and c.status_active=1 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $job_comp_cond $buyer_name $file_no_cond $internal_ref_cond $style_ref_cond $txt_date_ex";

		// echo $job_sql_ex;

        $job_sql_res_ex=sql_select($job_sql_ex);

        foreach($job_sql_res_ex as $row)
        {
        	$poIds_array[$row[csf("id")]]=$row[csf("id")];
            $job_arr[$row[csf("id")]]['job']=$row[csf("job_no_prefix_num")];
            $job_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
            $job_arr[$row[csf("id")]]['buyer']=$row[csf("buyer_name")];
            $job_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
            $job_arr[$row[csf("id")]]['file']=$row[csf("file_no")];
            $job_arr[$row[csf("id")]]['ref']=$row[csf("grouping")];
            $job_arr2[$row[csf("id")]]['pub_shipment_date']=$row[csf("pub_shipment_date")];
        }
        unset($job_sql_res_ex);
		// ============================== data store to gbl table ==================================
		$con = connect();
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from=1 and ENTRY_FORM=41");
		oci_commit($con);

		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 41, 1, $poIds_array, $empty_arr);//PO ID
		// ============================== end data store to gbl table ==================================

        $poIds_cond="";

       	$poIds=implode(",", $poIds_array);
         if($db_type==2 && count($poIds_array)>=1000)
        {
            $poIds_cond=" and (";
            $poIdsArr=array_chunk($poIds_array,999);
            foreach($poIdsArr as $ids)
            {
                $ids=implode(",",$ids);
                $poIds_cond.=" po_break_down_id in ($ids) or ";
            }
            $poIds_cond=chop($poIds_cond,'or ');
            $poIds_cond.=")";
        }
        else
        {
            $poIds_cond=" and po_break_down_id in ($poIds)";
        }


        $buyer_fullQty_arr=array();
        $prod_date_qty_arr=array();
        $prod_dlfl_qty_arr=array();
		$all_data_arr=array();
		$all_data_arrr=array();
        /*==========================================================================================/
        /										production data										/
        /========================================================================================= */
        $poIds_conds=str_replace("po_break_down_id", "a.po_break_down_id ", $poIds_cond);
        $sql_dtls="SELECT a.location, a.company_id, a.serving_company, a.floor_id, a.sewing_line, a.po_break_down_id,a.re_production_qty, a.production_date, a.item_number_id,c.color_number_id, a.production_type, a.production_source, a.embel_name, (a.prod_reso_allo) as prod_reso_allo, (b.production_qnty) as production_quantity,  (b.reject_qty) as reject_qnty, a.carton_qty as carton_qty
        from pro_garments_production_mst a ,pro_garments_production_dtls b ,wo_po_color_size_breakdown c,GBL_TEMP_ENGINE tmp
        where a.id=b.mst_id and b.color_size_break_down_id=c.id and c.po_break_down_id=a.po_break_down_id  and c.po_break_down_id =tmp.ref_val and tmp.entry_form=41 and tmp.ref_from=1 and tmp.user_id = $user_id and c.status_active in(1,2,3) and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  a.is_deleted=0 and a.status_active=1 $company_name_cond $working_factory_cond $txt_date $floor_name $location_cond $garmentsNature order by a.production_date ASC";

		// echo $sql_dtls;

		$sql_dtls_res=sql_select($sql_dtls);


		$w_company_cond='';
		if(!empty($cbo_com_fac_name))
		{
			$w_company_cond=" and a.company_id=$cbo_com_fac_name";
		}
		$company_cond='';
		if(!empty($cbo_company_name))
		{
			$company_cond=" and c.lc_company_id=$cbo_company_name";
		}
		$buyer_cond='';
		if(!empty($cbo_buyer_name))
		{
			$buyer_cond=" and d.buyer_name=$cbo_buyer_name";
		}
		$file_no_cond='';
		if(!empty($file_no))
		{
			$file_no_cond=" and b.file_no=$file_no";
		}
		$internal_ref_cond='';
		$internal_ref=str_replace("'", "", $internal_ref);
		if(!empty($internal_ref))
		{
			$internal_ref_cond=" and b.grouping='$internal_ref'";
		}

		$style_ref_cond='';
		$txt_style_ref=str_replace("'", "", $txt_style_ref);
		if(!empty($txt_style_ref))
		{
			$style_ref_cond=" and d.style_ref_no='$txt_style_ref'";
		}

		$location_cond='';
		if(!empty($location))
		{
			$location_cond=" and a.fini_location_id=$location";
		}
		$floor_cond='';
		if(!empty($cbo_floor))
		{
			$floor_cond=" and a.floor_id=$cbo_floor";
		}


		$poIds_conds=str_replace("a.po_break_down_id", "b.po_break_down_id ", $poIds_conds);
		$txt_rcv_date = str_replace("production_date", "a.receive_date", $txt_date);
        $fin_rcv_sql=" SELECT c.po_break_down_id, sum (c.fin_receive_qnty) as qnty,
					         a.receive_date,d.job_no_prefix_num as job_no,d.buyer_name,b.shipment_date,b.file_no,
					         b.grouping,d.style_ref_no,d.gmts_item_id,b.po_number,a.company_id,c.item_id
					    FROM gmt_finishing_receive_mst a, gmt_finishing_receive_dtls c,wo_po_break_down b,wo_po_details_master d
					   WHERE     a.id = c.mst_id
					         and b.id=c.po_break_down_id
					         and d.job_no=b.job_no_mst
					         and B.STATUS_ACTIVE=1
					         and b.is_deleted=0
					         and d.is_deleted=0
					         AND a.status_active = 1
					         AND a.is_deleted = 0
					         AND c.status_active = 1
					         AND c.is_deleted = 0
					        $w_company_cond
					        $company_cond
					        $buyer_cond
					        $txt_rcv_date
					        $location_cond
					        $floor_cond
					        $file_no_cond
					        $internal_ref_cond
							$style_ref_cond

					GROUP BY  c.po_break_down_id, a.receive_date,d.job_no_prefix_num,d.buyer_name,b.shipment_date,b.file_no,b.grouping,d.style_ref_no,d.gmts_item_id,b.po_number,a.company_id,c.item_id";
		//echo $fin_rcv_sql;
		$fin_rcv_res= sql_select( $fin_rcv_sql);
        $order_wise_fin_rec_qnty=array();
        foreach ($fin_rcv_res as $row)
        {
        	$order_wise_fin_rec_qnty[$row[csf('po_break_down_id')]][$row[csf('receive_date')]][$row[csf('item_id')]]['qnty']+=$row[csf('qnty')];
        	$order_wise_fin_rec_qnty[$row[csf('po_break_down_id')]][$row[csf('receive_date')]][$row[csf('item_id')]]['job_no']=$row[csf('job_no')];
        	$order_wise_fin_rec_qnty[$row[csf('po_break_down_id')]][$row[csf('receive_date')]][$row[csf('item_id')]]['buyer_name']=$row[csf('buyer_name')];
        	$order_wise_fin_rec_qnty[$row[csf('po_break_down_id')]][$row[csf('receive_date')]][$row[csf('item_id')]]['shipment_date']=$row[csf('shipment_date')];
        	$order_wise_fin_rec_qnty[$row[csf('po_break_down_id')]][$row[csf('receive_date')]][$row[csf('item_id')]]['file_no']=$row[csf('file_no')];
        	$order_wise_fin_rec_qnty[$row[csf('po_break_down_id')]][$row[csf('receive_date')]][$row[csf('item_id')]]['grouping']=$row[csf('grouping')];
        	$order_wise_fin_rec_qnty[$row[csf('po_break_down_id')]][$row[csf('receive_date')]][$row[csf('item_id')]]['style_ref_no']=$row[csf('style_ref_no')];
        	$order_wise_fin_rec_qnty[$row[csf('po_break_down_id')]][$row[csf('receive_date')]][$row[csf('item_id')]]['gmts_item_id']=$row[csf('gmts_item_id')];
        	$order_wise_fin_rec_qnty[$row[csf('po_break_down_id')]][$row[csf('receive_date')]][$row[csf('item_id')]]['po_number']=$row[csf('po_number')];
        	$order_wise_fin_rec_qnty[$row[csf('po_break_down_id')]][$row[csf('receive_date')]][$row[csf('item_id')]]['company_id']=$row[csf('company_id')];

        }

		// var_dump($sql_dtls_res);
        // echo $sql_dtls;  die;//$poIds_cond
		if( count($sql_dtls_res) > 0)
		{
			/*foreach($sql_dtls_res as $key=>$vals)
			{
				$all_production_po_arr[$vals[csf("po_break_down_id")]]=$vals[csf("po_break_down_id")];
			}
			$txt_date2=str_replace("production_date", "a.ex_factory_date", $txt_date);

			$all_production_poids=implode(",", $all_production_po_arr) ;
			if($exfactory_level==1) // gross level
			{

				$ex_fac_sql="SELECT c.buyer_id, a.po_break_down_id as po,a.ex_factory_date as dates,sum(a.ex_factory_qnty) as qnty from pro_ex_factory_delivery_mst c, pro_ex_factory_mst a where c.id=a.delivery_mst_id and a.status_active=1 and a.is_deleted=0  $txt_date2 and a.po_break_down_id in($all_production_poids) and c.status_active=1 and a.entry_form<>85   group by c.buyer_id,a.po_break_down_id,a.ex_factory_date";
			}
			else
			{

				$ex_fac_sql="SELECT c.buyer_id, a.po_break_down_id as po,a.ex_factory_date as dates,sum(b.production_qnty) as qnty from pro_ex_factory_delivery_mst c, pro_ex_factory_mst a,pro_ex_factory_dtls b where c.id=a.delivery_mst_id and a.id=b.mst_id and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_deleted=0  $txt_date2 and a.po_break_down_id in($all_production_poids) and c.status_active=1 and a.entry_form<>85   group by c.buyer_id,a.po_break_down_id,a.ex_factory_date";
			}
			// echo $ex_fac_sql;
			foreach(sql_select($ex_fac_sql) as $key=>$value)
			{
				$ex_fac_arr_po_datewise[$value[csf("po")]][$value[csf("dates")]]+=$value[csf("qnty")];
				$ex_fac_arr_buyerwise[$value[csf("buyer_id")]]+=$value[csf("qnty")];
				if($value[csf("qnty")]*1>0)
				{
					$ex_fac_all_po[$value[csf("po")]] =$value[csf("po")];
				}
			}


			if( count($ex_fac_all_po) != 0 )
			{
				$exfac_poids=implode(",", $ex_fac_all_po) ;
				$all_exfac_poids="";
				if($db_type==2 && count($ex_fac_all_po)>1000)
				{
					$all_exfac_poids=" and (";
					$exIdsArr=array_chunk(explode(",",$ex_fac_all_po),999);
					foreach($exIdsArr as $ids)
					{
						$ids=implode(",",$ids);
						$all_exfac_poids.=" b.id in ($ids) or ";
					}
					$all_exfac_poids=chop($all_exfac_poids,'or ');
					$all_exfac_poids.=")";
				}
				else
				{
					$all_exfac_poids=" and b.id in ($exfac_poids)";
				}
			}*/



			$poIds_cond3=str_replace("po_break_down_id","b.id", $poIds_cond);
			$po_wise_unit_price_sql="SELECT a.buyer_name,b.id,b.unit_price from wo_po_details_master a, wo_po_break_down b,GBL_TEMP_ENGINE tmp where a.id=b.job_id and b.id=tmp.ref_val and tmp.entry_form=41 and tmp.ref_from=1 and tmp.user_id = $user_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			foreach( sql_select($po_wise_unit_price_sql) as $key=>$val)
			{
				$po_wise_unit_price[$val[csf("id")]]=$val[csf("unit_price")];
				$buyer_wise_unit_price[$val[csf("buyer_name")]]+=$val[csf("unit_price")];
			}

		}
		/*==========================================================================================/
        /										shipment data										/
        /========================================================================================= */
        $txt_date2=str_replace("production_date", "e.ex_factory_date", $txt_date);
        if($exfactory_level==1) // gross level
		{
			$sql_ex="SELECT a.buyer_name,b.unit_price,d.delivery_location_id as location, d.company_id, d.delivery_company_id, d.delivery_floor_id as floor_id, e.po_break_down_id, e.ex_factory_date, e.item_number_id, sum(e.ex_factory_qnty) as ex_factory_qnty
        	from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c, pro_ex_factory_delivery_mst d ,pro_ex_factory_mst e
        	where d.id=e.delivery_mst_id and a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and b.id=e.po_break_down_id and e.status_active=1 and e.is_deleted=0 and  d.is_deleted=0 and d.status_active=1 $job_comp_cond $buyer_name $file_no_cond $internal_ref_cond $style_ref_cond $txt_date2
        	group by a.buyer_name,b.unit_price,d.delivery_location_id, d.company_id, d.delivery_company_id, d.delivery_floor_id, e.po_break_down_id, e.ex_factory_date, e.item_number_id
        	order by e.ex_factory_date ASC";
		}
		else // color or color and size level
		{
        	$sql_ex="SELECT a.buyer_name,b.unit_price, d.delivery_location_id as location, d.company_id, d.delivery_company_id, d.delivery_floor_id as floor_id, e.po_break_down_id, e.ex_factory_date, e.item_number_id, sum(f.production_qnty) as ex_factory_qnty
        	from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c, pro_ex_factory_delivery_mst d ,pro_ex_factory_mst e ,pro_ex_factory_dtls f
        	where d.id=e.delivery_mst_id and e.id=f.mst_id and a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and b.id=e.po_break_down_id and f.status_active in(1) and f.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and  d.is_deleted=0 and d.status_active=1 $job_comp_cond $buyer_name $file_no_cond $internal_ref_cond $style_ref_cond $txt_date2 and f.color_size_break_down_id=c.id
        	group by a.buyer_name,b.unit_price,d.delivery_location_id, d.company_id, d.delivery_company_id, d.delivery_floor_id, e.po_break_down_id, e.ex_factory_date, e.item_number_id
        	order by e.ex_factory_date ASC";
        }
        // echo $sql_ex;
        $ex_res=sql_select($sql_ex);

		/*==========================================================================================/
        /										carton qty											/
        /========================================================================================= */
		// GETTING CARTON QNTY
		$sql_carton="SELECT a.location,a.floor_id,a.sewing_line, a.po_break_down_id, a.production_date, a.item_number_id,a.serving_company,sum(a.carton_qty) as carton_qty
    	from pro_garments_production_mst a,GBL_TEMP_ENGINE tmp
    	where a.po_break_down_id=tmp.ref_val and tmp.entry_form=41 and tmp.ref_from=1 and tmp.user_id = $user_id and a.is_deleted=0 and a.status_active=1 $company_name_cond $working_factory_cond $txt_date $floor_name $location_cond $garmentsNature order by a.production_date ASC";
    	// echo $sql_carton;
    	$sql_carton_res = sql_select($sql_carton);
    	if($type==1)
		{
			foreach ($sql_carton_res as $row)
			{
				$prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("serving_company")]][$row[csf("production_date")]][$row[csf("item_number_id")]]['0']['0']['0']['crtQty']+=$row[csf("carton_qty")];
			}
		}
		else // type 2
		{
			foreach ($sql_carton_res as $row)
			{
				$prod_dlfl_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("location")]][$row[csf("floor_id")]]['0']['0']['0']['crtQty']=$row[csf("carton_qty")];
			}

		}
		// ================== for production data ======================
		$buyer_po_date_wise_gmts_data_arr = array();
		$check_fin_rcv = array();
        if($type==1)
        {
            foreach($sql_dtls_res as $row)
            {
                // var_dump($row);
                $buyer_name_dat=""; $job_no=''; $po_no=''; $buyer_id=''; $style_ref=''; $ref_no=''; $file_no='';
                $buyer_name_dat=$job_arr[$row[csf("po_break_down_id")]]['buyer'];
                $job_no=$job_arr[$row[csf("po_break_down_id")]]['job'];
                $po_no=$job_arr[$row[csf("po_break_down_id")]]['po'];
                $style_ref=$job_arr[$row[csf("po_break_down_id")]]['style'];
                $ref_no=$job_arr[$row[csf("po_break_down_id")]]['ref'];
                $file_no=$job_arr[$row[csf("po_break_down_id")]]['file'];

                //Buyer Wise Summary array start
                $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]]['0']['pQty']+=$row[csf("production_quantity")];
                $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("embel_name")]]['embQty']+=$row[csf("production_quantity")];
                $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("production_source")]]['sQty']+=$row[csf("production_quantity")];
                $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]]['0']['reQty']+=$row[csf("re_production_qty")];
                $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("production_source")]]['rejectQty']+=$row[csf("reject_qnty")];



             //     $arr_bind=$row[csf("po_break_down_id")]."***".$row[csf("production_date")]."***".[$row[csf("item_number_id")]];
             //    if(!in_array($arr_bind, $check_fin_rcv))
             //    {
	            //     $buyer_fullQty_arr[$buyer_name_dat][0]['0']['fin_rcv_qnty']+=$order_wise_fin_rec_qnty[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]]['qnty'];
	            //     array_push($check_fin_rcv, $arr_bind);
	            // }

                $buyer_po_date_wise_gmts_data_arr[$buyer_name_dat][$row[csf("po_break_down_id")]][$row[csf("production_type")]] += $row[csf("production_quantity")];

                //Details array start
                $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("production_type")]][$row[csf("color_number_id")]]['0']['pQty']+=$row[csf("production_quantity")];

                //for serving company
                $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("serving_company")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("production_type")]]['0']['pQty']+=$row[csf("production_quantity")];
                $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("serving_company")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("production_type")]][$row[csf("production_source")]]['sQty']+=$row[csf("production_quantity")];
                $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("serving_company")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("production_type")]]['0']['rejectQty']+=$row[csf("reject_qnty")];
                $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("serving_company")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("production_type")]]['0']['reQty']+=$row[csf("re_production_qty")];
                // $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("serving_company")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]['0']['0']['crtQty']+=$row[csf("carton_qty")];
                $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("serving_company")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("production_type")]][$row[csf("embel_name")]]['embQty']+=$row[csf("production_quantity")];


                $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("production_type")]][$row[csf("color_number_id")]][$row[csf("embel_name")]]['embQty']+=$row[csf("production_quantity")];
                $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("production_type")]][$row[csf("color_number_id")]][$row[csf("production_source")]]['sQty']+=$row[csf("production_quantity")];
                $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("production_type")]][$row[csf("color_number_id")]]['0']['reQty']+=$row[csf("re_production_qty")];
                $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("production_type")]][$row[csf("color_number_id")]]['0']['rejectQty']+=$row[csf("reject_qnty")];
                // $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]]['0']['0']['crtQty']+=$row[csf("carton_qty")];
                if($row[csf("production_source")]==0) $row[csf("production_source")]=1;
				$all_data_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]].=$row[csf("item_number_id")].'**'.$buyer_name_dat.'**'.$job_no.'**'.$po_no.'**'.$style_ref.'**'.$ref_no.'**'.$file_no.'**'.$row[csf("company_id")].'**'.$row[csf("production_source")].'**'.$row[csf("serving_company")].'**'.$row[csf("color_number_id")].'__';//.'**'.$row[csf("floor_id")]
                // echo $row[csf("carton_qty")];
            }
        }
        else if($type==2)
        {
            foreach($sql_dtls_res as $row)
            {
				// var_dump($row);
                $buyer_name_dat=""; $job_no=''; $po_no=''; $buyer_id=''; $style_ref=''; $ref_no=''; $file_no='';
                $buyer_name_dat=$job_arr[$row[csf("po_break_down_id")]]['buyer'];
                $job_no=$job_arr[$row[csf("po_break_down_id")]]['job'];
                $po_no=$job_arr[$row[csf("po_break_down_id")]]['po'];
                $style_ref=$job_arr[$row[csf("po_break_down_id")]]['style'];
                $ref_no=$job_arr[$row[csf("po_break_down_id")]]['ref'];
                $file_no=$job_arr[$row[csf("po_break_down_id")]]['file'];
                //Buyer Wise Summary array start
                $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]]['0']['pQty']+=$row[csf("production_quantity")];
                $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("embel_name")]]['embQty']+=$row[csf("production_quantity")];
                $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("production_source")]]['sQty']+=$row[csf("production_quantity")];
                $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]]['0']['reQty']+=$row[csf("re_production_qty")];
                $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("production_source")]]['rejectQty']+=$row[csf("reject_qnty")];



             //    $arr_bind=$row[csf("po_break_down_id")]."***".$row[csf("production_date")]."***".$row[csf("item_number_id")];
             //    if(!in_array($arr_bind, $check_fin_rcv))
             //    {
	            //      $buyer_fullQty_arr[$buyer_name_dat][0]['0']['fin_rcv_qnty']+=$order_wise_fin_rec_qnty[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]]['qnty'];
	            //     array_push($check_fin_rcv, $arr_bind);
	            // }

                $buyer_po_date_wise_gmts_data_arr[$buyer_name_dat][$row[csf("po_break_down_id")]][$row[csf("production_type")]] += $row[csf("production_quantity")];

                //Details array start
                if($row[csf("sewing_line")]=="") $row[csf("sewing_line")]=0;

                if($row[csf("floor_id")]=="") $row[csf("floor_id")]=0;

                if($row[csf("location")]=="") $row[csf("location")]=0;

                $prod_dlfl_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]][$row[csf("production_type")]]['0']['pQty']+=$row[csf("production_quantity")];

                $prod_dlfl_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]][$row[csf("production_type")]][$row[csf("embel_name")]]['embQty']+=$row[csf("production_quantity")];

                $prod_dlfl_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]][$row[csf("production_type")]][$row[csf("production_source")]]['sQty']+=$row[csf("production_quantity")];

                $prod_dlfl_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]][$row[csf("production_type")]]['0']['reQty']+=$row[csf("re_production_qty")];

                $prod_dlfl_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]][$row[csf("production_type")]]['0']['rejectQty']+=$row[csf("reject_qnty")];
                // $prod_dlfl_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]]['0']['0']['crtQty']+=$row[csf("carton_qty")];
                if($row[csf("production_source")]==0) $row[csf("production_source")]=1;
                if($row[csf("production_source")]==3)
                {
                	$all_data_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][0][0].= $row[csf("sewing_line")].'##'.$buyer_name_dat.'##'.$job_no.'##'.$po_no.'##'.$style_ref.'##'.$ref_no.'##'.$file_no.'##'.$row[csf("production_source")].'##0##'.$row[csf("serving_company")].'__';
                }
                else
                {
                	$all_data_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("location")]][$row[csf("floor_id")]].= $row[csf("sewing_line")].'##'.$buyer_name_dat.'##'.$job_no.'##'.$po_no.'##'.$style_ref.'##'.$ref_no.'##'.$file_no.'##'.$row[csf("production_source")].'##'.$row[csf("prod_reso_allo")].'##'.$row[csf("serving_company")].'__';
                }
            }
        }
        // echo "<br>"; print_r($prod_date_qty_arr); die;
        unset($sql_dtls_res);
        // unset($job_arr);

        // ============================ for shipment data ========================
        $ex_fac_arr_buyerwise = array();
        $prod_date_qty_arr_ex = array();
        $prod_date_qty_arr_ex2 = array();
        $po_id_arr = array();
        if($type==1)
        {
            foreach($ex_res as $row)
            {

                $buyer_name_dat=""; $job_no=''; $po_no=''; $buyer_id=''; $style_ref=''; $ref_no=''; $file_no='';
                $buyer_name_dat=$job_arr[$row[csf("po_break_down_id")]]['buyer'];
                $job_no=$job_arr[$row[csf("po_break_down_id")]]['job'];
                $po_no=$job_arr[$row[csf("po_break_down_id")]]['po'];
                $style_ref=$job_arr[$row[csf("po_break_down_id")]]['style'];
                $ref_no=$job_arr[$row[csf("po_break_down_id")]]['ref'];
                $file_no=$job_arr[$row[csf("po_break_down_id")]]['file'];

                $ex_fac_arr_po_datewise[$row[csf("po_break_down_id")]][$row[csf("ex_factory_date")]]+=$row[csf("ex_factory_qnty")];
				$ex_fac_arr_buyerwise[$buyer_name_dat]+=$row[csf("ex_factory_qnty")];

				$buyer_po_date_wise_gmts_data_arr[$buyer_name_dat][$row[csf("po_break_down_id")]][0] += $row[csf("ex_factory_qnty")];

                //Buyer Wise Summary array start
                // $ex_fac_arr_buyerwise[$buyer_name_dat]+=$row[csf("ex_factory_qnty")];
                $buyer_fullQty_arr[$buyer_name_dat][0]['0']['ex_factory_qnty']+=$row[csf("ex_factory_qnty")];

             //    $arr_bind=$row[csf("po_break_down_id")]."***".$row[csf("ex_factory_date")]."***".$row[csf("item_number_id")];
             //    if(!in_array($arr_bind, $check_fin_rcv))
             //    {
	            //     $buyer_fullQty_arr[$buyer_name_dat][0]['0']['fin_rcv_qnty']+=$order_wise_fin_rec_qnty[$row[csf("po_break_down_id")]][$row[csf("ex_factory_date")]][$row[csf("item_number_id")]]['qnty'];
	            //     array_push($check_fin_rcv, $arr_bind);
	            // }
                //Details array start
                // $prod_date_qty_arr_ex[$row[csf("po_break_down_id")]][$row[csf("ex_factory_date")]][$row[csf("item_number_id")]]['ex_factory_qnty']+=$row[csf("ex_factory_qnty")];

                //for serving company
                $prod_date_qty_arr_ex[$row[csf("po_break_down_id")]][$row[csf("delivery_company_id")]][$row[csf("ex_factory_date")]][$row[csf("item_number_id")]]['ex_factory_qnty']+=$row[csf("ex_factory_qnty")];

				$all_data_arr[$row[csf("po_break_down_id")]][$row[csf("ex_factory_date")]].=$row[csf("item_number_id")].'**'.$buyer_name_dat.'**'.$job_no.'**'.$po_no.'**'.$style_ref.'**'.$ref_no.'**'.$file_no.'**'.$row[csf("company_id")].'**1**'.$row[csf("delivery_company_id")].'**'.$row[csf("color_number_id")].'__';//.'**'.$row[csf("floor_id")]
                array_push($po_id_arr, $row[csf("po_break_down_id")]);
                array_push($po_id_arr, $row[csf("po_break_down_id")]);
            }
        }
        else if($type==2)
        {
            foreach($ex_res as $row)
            {
                $buyer_name_dat=""; $job_no=''; $po_no=''; $buyer_id=''; $style_ref=''; $ref_no=''; $file_no='';
                $buyer_name_dat=$job_arr[$row[csf("po_break_down_id")]]['buyer'];
                $job_no=$job_arr[$row[csf("po_break_down_id")]]['job'];
                $po_no=$job_arr[$row[csf("po_break_down_id")]]['po'];
                $style_ref=$job_arr[$row[csf("po_break_down_id")]]['style'];
                $ref_no=$job_arr[$row[csf("po_break_down_id")]]['ref'];
                $file_no=$job_arr[$row[csf("po_break_down_id")]]['file'];

                $ex_fac_arr_po_datewise[$row[csf("po_break_down_id")]][$row[csf("ex_factory_date")]]+=$row[csf("ex_factory_qnty")];
				$ex_fac_arr_buyerwise[$buyer_name_dat]+=$row[csf("ex_factory_qnty")];

				$buyer_po_date_wise_gmts_data_arr[$buyer_name_dat][$row[csf("po_break_down_id")]][0] += $row[csf("ex_factory_qnty")];

                //Buyer Wise Summary array start
                // $ex_fac_arr_buyerwise[$buyer_name_dat]+=$row[csf("ex_factory_qnty")];
                // $buyer_fullQty_arr[$buyer_name_dat][0]['0']['ex_factory_qnty']+=$row[csf("ex_factory_qnty")];
                // $arr_bind=$row[csf("po_break_down_id")]."***".$row[csf("ex_factory_date")]."***".$row[csf("item_number_id")];
                // if(!in_array($arr_bind, $check_fin_rcv))
                // {
                // 	$buyer_fullQty_arr[$buyer_name_dat][0]['0']['fin_rcv_qnty']+=$order_wise_fin_rec_qnty[$row[csf("po_break_down_id")]][$row[csf("ex_factory_date")]][$row[csf("item_number_id")]]['qnty'];
                // 	array_push($check_fin_rcv, $arr_bind);
                // }

                //Details array start

                $prod_date_qty_arr_ex2[$row[csf("po_break_down_id")]][$row[csf("ex_factory_date")]][$row[csf("item_number_id")]][$row[csf("delivery_location_id")]][$row[csf("delivery_floor_id")]]['ex_factory_qnty']+=$row[csf("ex_factory_qnty")];

                $all_data_arr[$row[csf("po_break_down_id")]][$row[csf("ex_factory_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("delivery_location_id")]][$row[csf("delivery_floor_id")]].='0##'.$buyer_name_dat.'##'.$job_no.'##'.$po_no.'##'.$style_ref.'##'.$ref_no.'##'.$file_no.'##1##0##'.$row[csf("delivery_company_id")].'__';
                array_push($po_id_arr, $row[csf("po_break_down_id")]);
            }
        }

        // echo "<pre>";print_r($all_data_arr);die();
        unset($ex_res);
        unset($job_arr);
        $b=1; //date_wise Summary

        //$rcv_data_need_to_show=array();
        foreach ($order_wise_fin_rec_qnty as $po_id=> $po_wise_data)
        {

        	foreach ($po_wise_data as $receive_date => $rcv_date_wise_data)
        	{
        		foreach ($rcv_date_wise_data as $item_id => $item_data)
        		{
        			$buyer_fullQty_arr[$item_data['buyer_name']][0]['0']['fin_rcv_qnty']+=$item_data['qnty'];
        		}
        	}
        }

        foreach ($buyer_po_date_wise_gmts_data_arr as $b_key => $b_value)
        {
        	foreach ($b_value as $po_key => $po_value)
        	{
        		$sewing_output_total = $po_value[5];
        		$ex_fact = $po_value[0];

                $ex_fact_fob=$ex_fact*$po_wise_unit_price[$po_key];

                $ex_fact_bal= $sewing_output_total-$ex_fact;
                $ex_fact_bal_fob= $ex_fact_bal*$po_wise_unit_price[$po_key];
                $ex_fac_arr_buyerwise[$b_key] += $ex_fact;
                $ex_fac_fob_arr_buyerwise[$b_key]+=$ex_fact_fob;
                $ex_fac_bal_arr_buyerwise[$b_key]+=$ex_fact_bal;
                $ex_fac_bal_fob_arr_buyerwise[$b_key]+=$ex_fact_bal_fob;
        	}
        }

       /* foreach($all_data_arr as $po_id=>$po_data)
        {
            foreach($po_data as $prod_date=>$prod_date_data)
            {
                $ex_itemdata='';
                $ex_itemdata=array_filter(array_unique(explode('__',$prod_date_data)));
                foreach($ex_itemdata as $data_all)
                {
                    $buyer_name=''; $item_id='';
                    $ex_data=array_filter(explode('**',$data_all));
                    if($ex_data[1] !="")
                    {
                        $buyer_name=$ex_data[1];
                        $item_id=$ex_data[0];
                        $sewOut_inQty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['5']['1']['sQty'];
                        $sewOut_outQty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['5']['3']['sQty'];

                        // $sewing_output_total=$sewOut_inQty+$sewOut_outQty;

                        $sewing_output_total = $date_po_wise_gmts_data_arr[$po_id][$prod_date][5];

                        $ex_fact = 0;
                        $ex_fact=$ex_fac_arr_po_datewise[$po_id][$prod_date];
                        $ex_fact_fob=$ex_fact*$po_wise_unit_price[$po_id];
                        $ex_fact_bal= $sewing_output_total-$ex_fact;
                        echo "$sewing_output_total-$ex_fact<br>";
                        $ex_fact_bal_fob= $ex_fact_bal*$po_wise_unit_price[$po_id];
                        $ex_fac_arr_buyerwise[$buyer_name] += $ex_fact;
                        $ex_fac_fob_arr_buyerwise[$buyer_name]+=$ex_fact_fob;
                        $ex_fac_bal_arr_buyerwise[$buyer_name]+=$ex_fact_bal;
                        $ex_fac_bal_fob_arr_buyerwise[$buyer_name]+=$ex_fact_bal_fob;


                    }
                }
            }
        }*/
		// print_r($ex_fac_arr_buyerwise);

		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from=1 and ENTRY_FORM=41");
		oci_commit($con);
		disconnect($con);
		?>
       <div>
			<div style="width:<? echo $div_width; ?>px">
				<table width="<? echo $div_width; ?>" cellspacing="0">
					<tr class="form_caption" style="border:none;">
						<td colspan="<? echo $colSpan; ?>" align="center" style="border:none;font-size:14px; font-weight:bold" ><? //echo $report_name; ?></td>
					 </tr>
					<tr style="border:none;">
						<td colspan="<? echo $colSpan; ?>" align="center" style="border:none; font-size:16px; font-weight:bold">
							Company Name:
							<?
							echo $company_library[str_replace("'","",$cbo_company_name)];
							/*$workingComp="";
							$workingFactCom=explode(",", $cbo_com_fac_name);
							foreach($workingFactCom as $workingFactCompany)
							{
								$workingComp.=$company_library[$workingFactCompany].', ';
							}
							echo chop($workingComp,',');*/
							?>
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="<? echo $colSpan; ?>" align="center" style="border:none;font-size:12px; font-weight:bold">
							<? echo "From $fromDate To $toDate" ;?>
						</td>
					</tr>
				</table>
				<!-- ==========================================================================================/
		        /										summary part										   /
		        /=========================================================================================== -->
				<table width="2240" cellspacing="0" border="1" rules="all" align="left">
					<tr>
						<td width="1920" align="left" valign="top">
	                    <div style="width:300px; float:left; background-color:#FCF"><strong>Production-Regular Order Summary</strong></div>
	                    <br/>
						<div style="clear:both;">
							<table width="2010" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
								<thead>
									<tr>
										<th style="word-wrap: break-word;word-break: break-all;" width="30">Sl.</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Buyer Name</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Cut Quantity</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Sent to Print</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Received from Print</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Sent to Embroidery </th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Received from Embroidery</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Sent to Wash</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Rev Wash</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Sent to Sp. Works</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Rev Sp. Works</th>

										<th style="word-wrap: break-word;word-break: break-all;" width="80">Sewing Input</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Sew Input (Outbound)</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Sewing Output</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Sew Output (Outbound)</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Finishing Received</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Total Iron Production</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Total Re-Iron</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Total Poly (Inhouse)</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Total Poly (Outbond)</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Total Packing/ Finishing</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">EXF Quantity</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">EXF Value</th>
										<th style="word-wrap: break-word;word-break: break-all;" width="80">Ex-Fac. Bal. Qty</th>
										<th style="word-wrap: break-word;word-break: break-all;" >Ex- Fac. Bal. FOB Value</th>
									</tr>
								</thead>
							</table>
                            <div style="overflow-y:scroll; max-height:225px; width:2025px" >
                                <table cellspacing="0" border="1" class="rpt_table"  width="2008" rules="all" id="" >
                                <?
								// echo "<pre>";
								// print_r($buyer_fullQty_arr);
								$tot_fin_rcv_qnty=0;
								$tot_buyer_fin_rcv_qnty=0;
                                foreach($buyer_fullQty_arr as $buyer_id=>$buyer_data)
                                {
                                    if($buyer_id!="")
                                    {
                                        if ($b%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                        $cutting_qty=$printing_qty=$printreceived_qty=$emb_qty=$embRec_qty=$wash_qty=$washRec_qty=$special_qty=$specialRec_qty=$sewIn_inQty=$sewIn_outQty=$sewOut_inQty=$sewOut_outQty=$iron_qty=$reIron_qty=$finish_qty=$polyIn_inQty=$polyOut_outQty=0;
                                        $cutting_qty=$buyer_data['1']['0']['pQty'];
                                        $printing_qty=$buyer_data['2']['1']['embQty'];
                                        $printreceived_qty=$buyer_data['3']['1']['embQty'];
                                        $emb_qty=$buyer_data['2']['2']['embQty'];
                                        $embRec_qty=$buyer_data['3']['2']['embQty'];
                                        $wash_qty=$buyer_data['2']['3']['embQty'];
                                        $washRec_qty=$buyer_data['3']['3']['embQty'];
                                        $special_qty=$buyer_data['2']['4']['embQty'];
                                        $specialRec_qty=$buyer_data['3']['4']['embQty'];
                                        $sewIn_inQty=$buyer_data['4']['0']['pQty'];
                                        $sewIn_outQty=$buyer_data['4']['3']['sQty'];
                                        $sewOut_inQty=$buyer_data['5']['0']['pQty'];
                                        $sewOut_outQty=$buyer_data['5']['3']['sQty'];
                                        $polyIn_inQty=$buyer_data['11']['1']['sQty'];
                                        $polyOut_outQty=$buyer_data['11']['3']['sQty'];
                                        $iron_qty=$buyer_data['7']['0']['pQty'];
                                        $reIron_qty=$buyer_data['7']['0']['reQty'];
                                        $finish_qty=$buyer_data['8']['0']['pQty'];
                                        // $ex_fact_buy=$ex_fac_arr_buyerwise[$buyer_id];
                                        $ex_fact_buy=$buyer_data[0]['0']['ex_factory_qnty'];
                                        $fin_rcv_qnty=$buyer_data[0]['0']['fin_rcv_qnty'];
                                        $ex_fact_buy_fob=$ex_fac_fob_arr_buyerwise[$buyer_id];
                                        $ex_fact_buy_bal=$ex_fac_bal_arr_buyerwise[$buyer_id];
                                        $ex_fact_buy_bal_fob=$ex_fac_bal_fob_arr_buyerwise[$buyer_id];

                                        ?>
                                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $b; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $b; ?>">
                                            <td style="word-wrap: break-word;word-break: break-all;" width="30"><? echo $b;?></td>
                                            <td style="word-wrap: break-word;word-break: break-all;" width="80"><p><? echo $buyer_short_library[$buyer_id]; ?>&nbsp;</p></td>
                                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($cutting_qty); ?></td>
                                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($printing_qty); ?></td>
                                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($printreceived_qty); ?></td>
                                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($emb_qty); ?></td>
                                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($embRec_qty); ?></td>
                                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($wash_qty); ?></td>
                                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($washRec_qty);  ?></td>
                                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($special_qty); ?></td>
                                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($specialRec_qty);  ?></td>
                                            <td  style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sewIn_inQty); ?></td>
                                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sewIn_outQty); ?></td>
                                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sewOut_inQty); ?></td>
                                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sewOut_outQty); ?></td>
                                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($fin_rcv_qnty); ?></td>
                                            <td  style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($iron_qty); ?></td>
                                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($reIron_qty); ?></td>
                                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($polyIn_inQty); ?></td>
                                            <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($polyOut_outQty); ?></td>
                                            <td width="80" style="word-wrap: break-word;word-break: break-all;"  align="right"><? echo number_format($finish_qty); ?></td>
                                            <td width="80" style="word-wrap: break-word;word-break: break-all;"  align="right"><? echo number_format($ex_fact_buy); ?></td>
                                            <td width="80" style="word-wrap: break-word;word-break: break-all;"  align="right"><? echo number_format($ex_fact_buy_fob); ?></td>
                                            <td width="80" style="word-wrap: break-word;word-break: break-all;"  align="right"><? echo number_format($ex_fact_buy_bal); ?></td>
                                            <td   style="word-wrap: break-word;word-break: break-all;"  align="right"><? echo number_format($ex_fact_buy_bal_fob); ?></td>
                                        </tr>
                                        <?
                                        $sumCutting_qty+=$cutting_qty;
                                        $sumPrinting_qty+=$printing_qty;
                                        $sumPrintreceived_qty+=$printreceived_qty;
                                        $sumEmb_qty+=$emb_qty;
                                        $sumEmbRec_qty+=$embRec_qty;
                                        $sumWash_qty+=$wash_qty;
                                        $sumWashRec_qty+=$washRec_qty;
                                        $sumSpecial_qty+=$special_qty;
                                        $sumSpecialRec_qty+=$specialRec_qty;
                                        $sumSewIn_inQty+=$sewIn_inQty;
                                        $sumSewIn_outQty+=$sewIn_outQty;
                                        $sumSewOut_inQty+=$sewOut_inQty;
                                        $sumSewOut_outQty+=$sewOut_outQty;
                                        $sumIron_qty+=$iron_qty;
                                        $sumReIron_qty+=$reIron_qty;
                                        $sumFinish_qty+=$finish_qty;
                                        $sumPolyIn_inQty+=$polyIn_inQty;
                                        $sumPolyOut_outQty+=$polyOut_outQty;


                                        $sum_exfact+=$ex_fact_buy;
                                        $sum_exfact_fob+=$ex_fact_buy_fob;
                                        $sum_exfact_bal+=$ex_fact_buy_bal;
                                        $sum_exfact_bal_fob+=$ex_fact_buy_bal_fob ;
                                        $tot_fin_rcv_qnty+=$fin_rcv_qnty;
                                        $tot_buyer_fin_rcv_qnty+=$fin_rcv_qnty;
                                        $b++;
                                    }
                                }
                                ?>
                                </table>
                            </div>
                            	<table border="1" class="tbl_bottom"  width="2010" rules="all" id="" >
                                    <tr>
                                    	<td style="word-wrap: break-word;word-break: break-all;" width="30">&nbsp;</td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" lign="right">Total</td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumCutting_qty); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumPrinting_qty); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumPrintreceived_qty); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumEmb_qty); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumEmbRec_qty); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumWash_qty); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumWashRec_qty); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumSpecial_qty); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumSpecialRec_qty); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumSewIn_inQty); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumSewIn_outQty); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumSewOut_inQty); ?></td>

                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumSewOut_outQty); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($tot_buyer_fin_rcv_qnty); ?></td>

                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumIron_qty); ?></td>
                                        <td  style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumReIron_qty); ?></td>
                                        <td  style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumPolyIn_inQty); ?></td>
                                        <td  style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumPolyOut_outQty); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sumFinish_qty); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sum_exfact); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sum_exfact_fob); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" width="80" align="right"><? echo number_format($sum_exfact_bal); ?></td>
                                        <td style="word-wrap: break-word;word-break: break-all;" align="right"><? echo number_format($sum_exfact_bal_fob); ?></td>
                                    </tr>
                                </table>
	                 	</div>
						</td>
					<?
					if(str_replace("'","",trim($cbo_subcon))==2)
					{
					?>
					<td width="550" align="left" valign="top"><div align="left" style="width:350px; background-color:#FCF"><strong>Production-Subcontract Order(Inbound)Summary </strong></div>
					<div style="float:left; width:550px">
						<table width="550" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
							<thead>
								<tr>
									<th width="30">Sl.</th>
									<th width="120">Buyer</th>
									<th width="80">Total Cut Qty</th>
									<th width="80">Total Sew Input</th>
									<th width="80">Total Sew Qty</th>
									<th width="80">Total Iron Qty</th>
									<th>Total Gmt. Fin. Qty</th>
								</tr>
							</thead>
						</table>
						<div style="max-height:425px; width:550px" >
						<table cellspacing="0" border="1" class="rpt_table"  width="550" rules="all" id="" >
						<?
						if(str_replace("'","",$cbo_floor)==0) $sub_floor_name="";else $sub_floor_name=" and b.floor_id in($cbo_floor)";
						if ($location==0) $sub_location_cond=""; else $sub_location_cond=" and b.location_id in($location) ";

						$total_po_quantity=0;$total_po_value=0;$total_cut_subcon=0;$total_sew_out_subcon=0;$total_ex_factory=0;
						$i=1;
						if(str_replace("'","",$cbo_company_name)==0) $company_name_sub=""; else $company_name_sub="and a.company_id = $cbo_company_name";
						if($db_type==0)
						{
							$ex_factory_sql="select a.party_id, sum(c.order_quantity) as order_quantity
							from subcon_ord_mst a, subcon_gmts_prod_dtls b, subcon_ord_dtls c
							where a.subcon_job=c.job_no_mst and c.id=b.order_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and b.production_date between $txt_date_from and $txt_date_to $company_name_sub $subcon_work_comp $sub_floor_name $sub_location_cond group by a.party_id";
						}
						else
						{
							 $ex_factory_sql="select a.party_id, sum(c.order_quantity) as order_quantity
							from subcon_ord_mst a, subcon_gmts_prod_dtls b, subcon_ord_dtls c
							where a.subcon_job=c.job_no_mst and c.id=b.order_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and b.production_date between $txt_date_from and $txt_date_to $company_name_sub  $subcon_work_comp $sub_floor_name $sub_location_cond group by a.party_id";
						}
						//echo  $exfactory_sql;
						$ex_factory_sql_result=sql_select($ex_factory_sql);
						$ex_factory_arr=array();
						foreach($ex_factory_sql_result as $resRow)
						{
							$ex_factory_arr[$resRow[csf("party_id")]] = $resRow[csf("order_quantity")];
						}
						//var_dump($exfactory_arr);die;
						//print_r($ex_factory_arr);die;

						//@@@@@@@@@@@@@@@@@@@@@
						$sub_cut_sew_array=array();

						if($db_type==0)
						{
							$production_mst_sql= sql_select("SELECT  a.party_id,
							sum(CASE WHEN production_type ='1' THEN production_qnty ELSE 0 END) AS cutting_qnty,
							sum(CASE WHEN production_type ='7' THEN production_qnty ELSE 0 END) AS sewing_input_qnty,
							sum(CASE WHEN production_type ='2' THEN production_qnty ELSE 0 END) AS sewingout_qnty,
							sum(CASE WHEN production_type ='3' THEN production_qnty  ELSE 0 END) AS ironout_qnty,
							sum(CASE WHEN production_type ='4' THEN production_qnty  ELSE 0 END) AS gmts_fin_qnty
							from subcon_ord_mst a, subcon_gmts_prod_dtls b, subcon_ord_dtls c

							where c.job_no_mst=a.subcon_job and c.id=b.order_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and  c.is_deleted=0 and c.status_active=1 and b.production_date between $txt_date_from and $txt_date_to $company_name_sub  $subcon_work_comp $sub_location_cond $sub_floor_name group by a.party_id");
						}
						else
						{
							$production_mst_sql=sql_select("SELECT  a.party_id,
							sum(CASE WHEN production_type ='1' THEN production_qnty ELSE 0 END) AS cutting_qnty,
							sum(CASE WHEN production_type ='7' THEN production_qnty ELSE 0 END) AS sewing_input_qnty,

							sum(CASE WHEN production_type ='2' THEN production_qnty ELSE 0 END) AS sewingout_qnty,
							sum(CASE WHEN production_type ='3' THEN production_qnty  ELSE 0 END) AS ironout_qnty,
							sum(CASE WHEN production_type ='4' THEN production_qnty  ELSE 0 END) AS gmts_fin_qnty
							from subcon_ord_mst a, subcon_gmts_prod_dtls b, subcon_ord_dtls c
							where c.job_no_mst=a.subcon_job and c.id=b.order_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.production_date between $txt_date_from and $txt_date_to $company_name_sub $subcon_work_comp $sub_location_cond $sub_floor_name group by a.party_id");

						}
						foreach($production_mst_sql as $sql_result)
						{
							$sub_cut_sew_array[$sql_result[csf("party_id")]]['1']=$sql_result[csf("cutting_qnty")];
							$sub_cut_sew_array[$sql_result[csf("party_id")]]['7']=$sql_result[csf("sewing_input_qnty")];
							$sub_cut_sew_array[$sql_result[csf("party_id")]]['2']=$sql_result[csf("sewingout_qnty")];
							$sub_cut_sew_array[$sql_result[csf("party_id")]]['3']=$sql_result[csf("ironout_qnty")];
							$sub_cut_sew_array[$sql_result[csf("party_id")]]['4']=$sql_result[csf("gmts_fin_qnty")];
						}
						//var_dump($cutting_array);
						//@@@@@@@@@@@@@@@@@@@@@
						if($db_type==0)
						{
							$production_date_sql="SELECT a.party_id, sum(c.order_quantity) as po_quantity, sum(c.amount) as po_total_price
							from subcon_ord_mst a, subcon_gmts_prod_dtls b, subcon_ord_dtls c
							where c.job_no_mst=a.subcon_job and c.id=b.order_id and b.production_type in (1,2,7) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.production_date between $txt_date_from and $txt_date_to $company_name_sub $subcon_work_comp $sub_location_cond $sub_floor_name  group by a.party_id order by a.party_id ASC";
						}
						else
						{
							$production_date_sql="SELECT a.party_id, sum(c.order_quantity) as po_quantity, sum(c.amount) as po_total_price
							from subcon_ord_mst a, subcon_gmts_prod_dtls b, subcon_ord_dtls c
							where c.job_no_mst=a.subcon_job and c.id=b.order_id and b.production_type in (1,2,7) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.production_date between $txt_date_from and $txt_date_to $company_name_sub $subcon_work_comp $sub_location_cond $sub_floor_name group by a.party_id order by a.party_id ASC";
						}
						//echo $production_date_sql;//die;
						$pro_sql_result=sql_select($production_date_sql);
						foreach($pro_sql_result as $pro_date_sql_row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
								<td width="30"><? echo $i;?></td>
								<td width="120"><? echo $buyer_short_library[$pro_date_sql_row[csf("party_id")]]; ?></td>
								<td width="80" align="right"><? echo number_format($sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['1']); ?></td>
								<td width="80" align="right"><? echo number_format($sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['7']); ?></td>
								<td width="80" align="right"><? echo number_format($sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['2']); ?></td>
								<td width="80" align="right"><? echo number_format($sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['3']); ?></td>
								<td align="right"><? echo number_format($sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['4']); ?></td>
							</tr>
							<?
							$total_cut_subcon+=$sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['1'];
							$total_input_subcon+=$sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['7'];
							$total_sew_out_subcon+=$sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['2'];
							$total_iron_subcon+=$sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['3'];
							$total_gmts_fin_subcon+=$sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['4'];
							$i++;
						}//end foreach 1st
						//$chart_data_qnty="Order Qty;".$total_po_quantity."\n"."Cutting;".$total_cut."\n"."Sew Out ;".$total_sew_out."\n"."Ex-Fact;".$total_ex_factory."\n";
						?>
						</table>
						<table border="1" class="tbl_bottom"  width="550" rules="all" id="" >
							<tr>
								<td width="30">&nbsp;</td>
								<td width="120" align="right">Total</td>
								<td width="80" id="tot_cutting"><? echo number_format($total_cut_subcon); ?></td>
								<td width="80" id="tot_input"><? echo number_format($total_input_subcon); ?></td>
								<td width="80" id="tot_sew_out"><? echo number_format($total_sew_out_subcon); ?></td>
								<td width="80" id="tot_iron_out"><? echo number_format($total_iron_subcon); ?></td>
								<td id="tot_gmt_fin_out"><? echo number_format($total_gmts_fin_subcon); ?></td>
							</tr>
						</table>
						<br />
							<div style="background-color:#FF9; width:400px; font-size:16px"><strong>Total Cutting: <? echo number_format($all_production_cutt=$sumCutting_qty+$total_cut_subcon,0); ?> (Pcs)&nbsp;&nbsp; & Total Sewing: <? echo number_format($all_production_sewing=$sumSewOut_inQty+$total_sew_out_subcon,0); ?> (Pcs)</strong></div><br />
							<div style="background-color:#FF9; width:400px; font-size:16px"><strong>Total Iron: <? echo number_format($all_production_iron=$sumIron_qty+$total_iron_subcon,0); ?> (Pcs)&nbsp;&nbsp; & Total Gmts. Fin.: <? echo number_format($all_production_gmts_fin=$sumFinish_qty+$total_gmts_fin_subcon,0); ?> (Pcs)</strong></div>
							</div>
						</div>
					</td>
					<?
					}
					?>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				</table>

			</div>
			<div>&nbsp;</div>
			<br />
			<!-- ==========================================================================================/
	        /										Details part										   /
	        /=========================================================================================== -->
			<? if($type==1) // Date Wise
			{

				?>
			   <h5 style="width:600px; background-color:#FCF; float:left;"><strong>Production-Regular Order</strong></h5>
				<table width="4395" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
					<thead>
					<tr>
						<th width="30" style="word-wrap: break-word;word-break: break-all;">Sl.</th>
						<th width="100" style="word-wrap: break-word;word-break: break-all;">Working Factory</th>
						<th width="100" style="word-wrap: break-word;word-break: break-all;">Job No</th>
						<th width="130" style="word-wrap: break-word;word-break: break-all;">Order Number</th>
						<th width="130" style="word-wrap: break-word;word-break: break-all;">Ship Date</th>
						<th width="100" style="word-wrap: break-word;word-break: break-all;">Buyer Name</th>
						<th width="130" style="word-wrap: break-word;word-break: break-all;">Style Name</th>
						<th width="100" style="word-wrap: break-word;word-break: break-all;">File No</th>
						<th width="100" style="word-wrap: break-word;word-break: break-all;">Internal Ref</th>
						<th width="130" style="word-wrap: break-word;word-break: break-all;">Item Name</th>
						<th width="130" style="word-wrap: break-word;word-break: break-all;">Gtms Color</th>
						<th width="100" style="word-wrap: break-word;word-break: break-all;">Production Date</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Cutting</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Sent to prnt</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Rev prn/Emb</th>

						<th width="80" style="word-wrap: break-word;word-break: break-all;">Sent to Emb</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Rev Emb</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Sent to Wash</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Rev Wash</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Sent to Sp. Works</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Rev Sp. Works</th>

						<th width="80" style="word-wrap: break-word;word-break: break-all;">Sewing In (Inhouse)</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Sewing In (Out-bound)</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Total Sewing Input</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Sewing Out (Inhouse)</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Sewing Out (Out-bound)</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Total Sewing Out</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Finishing<br>Rcv</th>

						<th width="80" style="word-wrap: break-word;word-break: break-all;">Iron Qty (Inhouse)</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Iron Qty (Out-bound)</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Total Iron Qty</th>

						<th width="80" style="word-wrap: break-word;word-break: break-all;">Re-Iron Qty </th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Poly Qty (Inhouse)</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Poly Qty (Out-bound)</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Total Poly Qty</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Finish Qty (Inhouse)</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Finish Qty (Out-bound)</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Total Finish Qty</th>

						<th width="80" style="word-wrap: break-word;word-break: break-all;">Today Carton</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Prod/Dzn</th>

						<!-- New Colmun ====================== -->
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Reject Qty</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Cutting Reject</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Print,Emb,Wash Reject</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Sewing Reject</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Iron Reject</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Packing & Finishing Reject</th>

						<th width="80" style="word-wrap: break-word;word-break: break-all;">Ex-Fac. Qty</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Ex-Fac. FOB Value</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Ex-Fac Bal. Qty</th>
						<th width="80" style="word-wrap: break-word;word-break: break-all;">Ex- Fac. Bal. FOB Value</th>
						<th style="word-wrap: break-word;word-break: break-all;">Remarks</th>
						</tr>
					 </thead>
				</table>
				<div style="width:4413px; overflow-y: scroll; max-height:400px;" id="scroll_body" >
					<table cellspacing="0" border="1" class="rpt_table"  width="4395" rules="all" id="table_body" >
						<?
						// var_dump($all_data_arr);
						$i=1;
						$fin_rcv_total=0;
						$check_fin_rcv=array();
						foreach($all_data_arr as $po_id=>$po_data)
						// var_dump($all_data_arr);
						// var_dump($po_data);
						{
							foreach($po_data as $prod_date=>$prod_date_data)
							{
								$ex_itemdata='';
								$ex_itemdata=array_filter(array_unique(explode('__',$prod_date_data)));
								foreach($ex_itemdata as $data_all)
								{
									$item_id=''; $buyer_name=''; $job_no=''; $po_no=''; $style_ref=''; $ref_no=''; $file_no=''; $company_id='';
										$ex_data=array_filter(explode('**',$data_all));
										// print_r($ex_data);

									if($ex_data[1] !="")
									{
										$item_id=$ex_data[0];
										$buyer_name=$ex_data[1];
										$job_no=$ex_data[2];
										$po_no=$ex_data[3];
										$style_ref=$ex_data[4];
										$ref_no=$ex_data[5];
										$file_no=$ex_data[6];
										$company_id=$ex_data[7];
										//$floor_id=$ex_data[10];
										$serving_comp_id=$ex_data[9];
										$serving_company='';
										//  echo $serving_comp_id."</br>";
										if($ex_data[8]==1)
										{
											$serving_company=$company_short_library[$serving_comp_id];
										}
										else if($ex_data[8]==3)
										{
											$serving_company=$supplier_arr[$serving_comp_id];
										}
										$color_id=$ex_data[10];

										if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										$cutting_qty=$print_qty=$printRec_qty=$emb_qty=$embRec_qty=$wash_qty=$washRec_qty=$special_qty=$specialRec_qty=$sewIn_inQty=$sewIn_outQty=$sewing_input_total=$sewOut_inQty=$sewOut_outQty=$sewing_output_total=$ironIn_qty=$ironOut_qty=$iron_qty_total=$reIron_qty=$finishIn_qty=$finishOut_qty=$finishing_qty=$carton_qty=$rejFinish_qty=$rejSewing_qty=$reject_Qty=$polyIn_Qty=$polyOut_Qty=0;
								  		//$prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("production_type")]]['0']['pQty']+=$row[csf("production_quantity")];

										$cutting_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['1']['0']['pQty'];
										$print_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['2']['1']['embQty'];
										$printRec_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['3']['1']['embQty'];
										$emb_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['2']['2']['embQty'];
										$embRec_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['3']['2']['embQty'];
										$wash_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['2']['3']['embQty'];
										$washRec_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['3']['3']['embQty'];
										$special_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['2']['4']['embQty'];
										$specialRec_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['3']['4']['embQty'];
										$sewIn_inQty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['4']['1']['sQty'];
										$sewIn_outQty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['4']['3']['sQty'];
										$sewOut_inQty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['5']['1']['sQty'];
										$sewOut_outQty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['5']['3']['sQty'];
										$ironIn_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['7']['1']['sQty'];
										$ironOut_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['7']['3']['sQty'];
										$reIron_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['7']['0']['reQty'];
										$finishIn_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['8']['1']['sQty'];
										$finishOut_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['8']['3']['sQty'];
										$carton_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['0']['0']['crtQty'];
										$rejFinish_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['8']['0']['rejectQty'];
										$rejSewing_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['5']['0']['rejectQty'];

										$polyIn_Qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['11']['1']['sQty'];
										$polyOut_Qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['11']['3']['sQty'];

										$rejIron_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['7']['0']['rejectQty'];
										$rejPrint_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['3']['0']['rejectQty'];
										$rejCutting_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['1']['0']['rejectQty'];
										//echo $rejFinish_qty.'='.$rejSewing_qty.'='.$rejIron_qty.'='.$rejPrint_qty.'='.$rejCutting_qty;
										$ex_fact = $prod_date_qty_arr_ex[$po_id][$ex_data[9]][$prod_date][$item_id]['ex_factory_qnty'];
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
											<td width="30" style="word-wrap: break-word;word-break: break-all;"><? echo $i;?></td>
											<td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo $serving_company; ?></p></td>
											<td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo $job_no;?></p></td>
											<td width="130" style="word-wrap: break-word;word-break: break-all;"><p><a href="##" onclick="openmypage_order(<? echo $po_id;?>,<? echo $item_id;?>,'orderQnty_popup');" ><? echo $po_no; ?></a></p></td>
											<td width="130" style="word-wrap: break-word;word-break: break-all;"><p><?php echo change_date_format($job_arr2[$po_id]['pub_shipment_date']); ?></p></td>
											<td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo $buyer_short_library[$buyer_name]; ?></p></td>
											<td width="130" style="word-wrap: break-word;word-break: break-all;"><p><? echo $style_ref; ?></p></td>
											<td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo $file_no; ?></p></td>
											<td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo $ref_no; ?></p></td>
											<td width="130" style="word-wrap: break-word;word-break: break-all;"><p><? echo $garments_item[$item_id]; ?></p></td>
											<td width="130" style="word-wrap: break-word;word-break: break-all;"><p><? echo $color_library_arr[$color_id]; ?></p></td>
											<td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo change_date_format($prod_date); ?></p></td>

											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id;?>','<? echo $location; ?>','<? //echo $floor_id; ?>','Cutting Info','cutting_popup');" ><? echo $cutting_qty; ?></a></td>

		                                    <td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" title="Here"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id;?>','<? echo $location; ?>','<? echo $floor_id; ?>','Printing Issue Info','printing_issue_popup');" ><? echo $print_qty; ?></a></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location; ?>,<? echo $floor_id; ?>,'Priniting Receive Info','printing_receive_popup');" ><? echo $printRec_qty; ?></a></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location; ?>,<? echo $floor_id; ?>,'Embroidery Issue Info','embroi_issue_popup');" ><? echo $emb_qty; ?></a></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location; ?>,<? echo $floor_id; ?>,'Embroidery Receive Info','embroi_receive_popup');" ><? echo $embRec_qty; ?></a></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location; ?>,<? echo $floor_id; ?>,'Wash Issue Info','wash_issue_popup');" ><? echo $wash_qty; ?></a></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location; ?>,<? echo $floor_id; ?>,'Wash Receive Info','wash_receive_popup');" ><? echo $washRec_qty; ?></a></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location; ?>,<? echo $floor_id; ?>,'Special Works Issue Info','sp_issue_popup');" ><? echo $special_qty; ?></a></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location; ?>,<? echo $floor_id; ?>,'Special Works Receive Info','sp_receive_popup');" ><? echo $specialRec_qty; ?></a> </td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__0';?>','<? echo $color_id; ?>','1','4','sewingQnty_popup');" ><? echo $sewIn_inQty; ?></a></td>
											<td width="80"  style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__0';?>','3','4','sewingQnty_popup');" ><? echo $sewIn_outQty; ?></a></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><? $sewing_input_total=$sewIn_inQty+$sewIn_outQty; echo $sewing_input_total; ?></td>

											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__'.$serving_comp_id; ?>','<? echo $color_id; ?>','1','5','sewingQnty_popup');" ><? echo $sewOut_inQty; ?></a></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__'.$serving_comp_id;?>','3','5','sewingQnty_popup');" ><? echo $sewOut_outQty; ?></a></td>

											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><? $sewing_output_total=$sewOut_inQty+$sewOut_outQty; echo $sewing_output_total; ?></td>

											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right">
												<?
											$fin_rcv_qnty=$order_wise_fin_rec_qnty[$po_id][$prod_date][$item_id]['qnty'];
											$company_id=$order_wise_fin_rec_qnty[$po_id][$prod_date][$item_id]['company_id'];
											$fin_rcv_total+=$fin_rcv_qnty;
											$arr_bind=$po_id."***".$prod_date."***".$item_id;
											array_push($check_fin_rcv, $arr_bind);

											 ?>

											<a href="##" onclick="openmyfinrcv('<?=$po_id;?>','<?=$company_id;?>','<?=$prod_date;?>','<?=$item_id;?>')"><? echo fn_number_format($fin_rcv_qnty,0,".",","); ?></a>

											</td>

											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'1','0','ironQnty_popup');" ><? echo $ironIn_qty; ?></a></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'3','0','ironQnty_popup');" ><? echo $ironOut_qty; ?></a></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><? $iron_qty_total=$ironIn_qty+$ironOut_qty; echo $iron_qty_total; ?></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><? echo $reIron_qty; ?></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__'.$serving_comp_id;?>','1','11','sewingQnty_popup');" ><? echo $polyIn_Qty; ?></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__'.$serving_comp_id;?>','3','11','sewingQnty_popup');" ><? echo $polyOut_Qty; ?></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><? $poly_qty_total=$polyIn_Qty+$polyOut_Qty; echo $poly_qty_total; ?></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><? echo $finishIn_qty; ?></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><? echo $finishOut_qty; ?></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'0','0','finishQnty_popup');" ><? $finishing_qty=$finishIn_qty+$finishOut_qty; echo $finishing_qty; ?></a></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><? echo $carton_qty; ?></td>
											<? $prod_dzn=0;
											if($sewing_output_total!=0)
											{
												$prod_dzn=($sewing_output_total)/12;
											}
											?>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><? if($prod_dzn!=0) echo number_format($prod_dzn,2); else echo "0"; ?></td>

											<? //$cm_per=0; $cm_per=$cm_per_dzn[$rows[csf("job_no_mst")]] ;
											$rej_title='Fin '.$rejFinish_qty.', Sew '.$rejSewing_qty.', Iron '.$rejIron_qty.', Print '.$rejPrint_qty.', Cut '.$rejCutting_qty;
											 ?>
											 <td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" title="<? echo $rej_title;?>" >
		                                    <? $reject_Qty=$rejFinish_qty+$rejSewing_qty+$rejIron_qty+$rejPrint_qty+$rejCutting_qty; echo $reject_Qty;  ?>
											</td>

											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" title="<? echo $rej_title;?>" >
		                                    <a href="##" onclick="openmypage_rej(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $ex_data[9];?>,'0','cutting_reject_popup');" ><? echo $rejCutting_qty;  ?></a>
											</td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" title="<? echo $rej_title;?>" >
		                                    <a href="##" onclick="openmypage_rej(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $ex_data[9];?>,'0','emb_reject_popup');" ><? echo $rejPrint_qty;  ?></a>
											</td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" title="<? echo $rej_title;?>" >
		                                    <a href="##" onclick="openmypage_rej(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $ex_data[9];?>,'0','sewing_reject_popup');" ><? echo $rejSewing_qty;  ?></a>
											</td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" title="<? echo $rej_title;?>" >
		                                    <a href="##" onclick="openmypage_rej(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $ex_data[9];?>,'0','iron_reject_popup');" ><? echo $rejIron_qty;  ?></a>
											</td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" title="<? echo $rej_title;?>" >
		                                    <a href="##" onclick="openmypage_rej(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $ex_data[9];?>,'0','finishing_reject_popup');" ><? echo $rejFinish_qty;  ?></a>
											</td>

											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><? echo $ex_fact; //$ex_fact=$ex_fac_arr_po_datewise[$po_id][$prod_date]; ?></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><? echo $ex_fact_fob=$ex_fact*$po_wise_unit_price[$po_id]; ?></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><? echo $ex_fact_bal= $sewing_output_total-$ex_fact; ?></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"><? echo $ex_fact_bal_fob= $ex_fact_bal*$po_wise_unit_price[$po_id]; ?></td>

											<td style="word-wrap: break-word;word-break: break-all;" width="">
												<a href="##"  onclick="openmypage_remark(<? echo $po_id;?>,'date_wise_production_report');" > Veiw </a>
											</td>
										</tr>
										<?
										$tot_cutting_qty+=$cutting_qty;
										$tot_print_qty+=$print_qty;
										$tot_printRec_qty+=$printRec_qty;
										$tot_emb_qty+=$emb_qty;
										$tot_embRec_qty+=$embRec_qty;
										$tot_wash_qty+=$wash_qty;
										$tot_washRec_qty+=$washRec_qty;
										$tot_special_qty+=$special_qty;
										$tot_specialRec_qty+=$specialRec_qty;
										$tot_sewIn_inQty+=$sewIn_inQty;
										$tot_sewIn_outQty+=$sewIn_outQty;
										$tot_sewing_input+=$sewing_input_total;
										$tot_sewOut_inQty+=$sewOut_inQty;
										$tot_sewOut_outQty+=$sewOut_outQty;
										$tot_sewing_output+=$sewing_output_total;
										$tot_ironIn_qty+=$ironIn_qty;
										$tot_ironOut_qty+=$ironOut_qty;
										$tot_iron_qty+=$iron_qty_total;
										$tot_reIron_qty+=$reIron_qty;
										$tot_polyIn_Qty+=$polyIn_Qty;
										$tot_polyOut_Qty+=$polyOut_Qty;
										$tot_poly_qty_sum+=$poly_qty_total;
										$tot_finishIn_qty+=$finishIn_qty;
										$tot_finishOut_qty+=$finishOut_qty;
										$tot_finishing_qty+=$finishing_qty;
										$tot_carton_qty+=$carton_qty;
										$total_prod_dzn+=$prod_dzn;

										$tot_reject_Qty+=$reject_Qty;
										$tot_rejCutting_qty+=$rejCutting_qty;
										$tot_rejPrint_qty+=$rejPrint_qty;
										$tot_rejSewing_qty+=$rejSewing_qty;
										$tot_rejIron_qty+=$rejIron_qty;
										$tot_rejFinish_qty+=$rejFinish_qty;

										$tot_ex_fac+=$ex_fact;
										$tot_ex_fac_fob+=$ex_fact_fob;
										$tot_fac_bal+=$ex_fact_bal;
										$tot_fac_bal_fob+=$ex_fact_bal_fob;
										$i++;
									}
								}
							}
						}
						//unset($date_sql_result);

						// echo "<pre>";
						// print_r($rcv_data_need_to_show);
						// echo "</pre>";

						$rcv_data_need_to_show=array();
				        foreach ($order_wise_fin_rec_qnty as $po_id=> $po_wise_data)
				        {

				        	foreach ($po_wise_data as $receive_date => $rcv_date_wise_data)
				        	{
				        		foreach ($rcv_date_wise_data as $item_id => $item_data)
				        		{

				        			$arr_bind=$po_id."***".$receive_date."***".$item_id;
					        		if(!in_array($arr_bind, $check_fin_rcv))
					                {
					                	$buyer_fullQty_arr[$item_data['buyer_name']][0]['0']['fin_rcv_qnty']+=$item_data['qnty'];
					                	$rcv_data_need_to_show[$po_id][$receive_date][$item_id]['qnty']=$item_data['qnty'];
							        	$rcv_data_need_to_show[$po_id][$receive_date][$item_id]['job_no']=$item_data['job_no'];
							        	$rcv_data_need_to_show[$po_id][$receive_date][$item_id]['po_number']=$item_data['po_number'];
							        	$rcv_data_need_to_show[$po_id][$receive_date][$item_id]['buyer_name']=$item_data['buyer_name'];
							        	$rcv_data_need_to_show[$po_id][$receive_date][$item_id]['shipment_date']=$item_data['shipment_date'];
							        	$rcv_data_need_to_show[$po_id][$receive_date][$item_id]['file_no']=$item_data['file_no'];
							        	$rcv_data_need_to_show[$po_id][$receive_date][$item_id]['grouping']=$item_data['grouping'];
							        	$rcv_data_need_to_show[$po_id][$receive_date][$item_id]['style_ref_no']=$item_data['style_ref_no'];
							        	$rcv_data_need_to_show[$po_id][$receive_date][$item_id]['gmts_item_id']=$item_data['gmts_item_id'];
							        	$rcv_data_need_to_show[$po_id][$receive_date][$item_id]['item_id']=$item_data['item_id'];
							        	$rcv_data_need_to_show[$po_id][$receive_date][$item_id]['company_id']=$item_data['company_id'];
					                	array_push($check_fin_rcv, $arr_bind);
					                }
				        		}
				        	}
				        }


						foreach ($rcv_data_need_to_show as $po_id => $po_d)
						{
							foreach ($po_d as $receive_date => $date_data)
							{
								foreach ($date_data as $item_id => $rcv_data)
								{
									?>
										<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
											<td width="30" style="word-wrap: break-word;word-break: break-all;"><? echo $i;?></td>
											<td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo $company_short_library[$rcv_data['company_id']]; ?></p></td>
											<td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo $rcv_data['job_no'];?></p></td>
											<td width="130" style="word-wrap: break-word;word-break: break-all;">
												<p><a href="##" onclick="openmypage_order(<? echo $po_id;?>,<? echo $item_id;?>,'orderQnty_popup');" ><? echo $rcv_data['po_number'];?></a></p>
											</td>
											<td width="130" style="word-wrap: break-word;word-break: break-all;"><p><?php echo change_date_format($rcv_data['shipment_date']); ?></p></td>
											<td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo $buyer_short_library[$rcv_data['buyer_name']]; ?></p></td>
											<td width="130" style="word-wrap: break-word;word-break: break-all;"><p><? echo $rcv_data['style_ref_no']; ?></p></td>
											<td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo $rcv_data['file_no']; ?></p></td>
											<td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo $rcv_data['grouping']; ?></p></td>
											<td width="130" style="word-wrap: break-word;word-break: break-all;"><p><? echo $garments_item[$item_id]; ?></p></td>
											<td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo change_date_format($receive_date); ?></p></td>

											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>

		                                    <td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" title="Here"></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"> </td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
											<td width="80"  style="word-wrap: break-word;word-break: break-all;" align="right"></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>

											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>

											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>

											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right">
												<?
											$fin_rcv_qnty=$rcv_data['qnty'];
											$company_id=$rcv_data['company_id'];
											$fin_rcv_total+=$fin_rcv_qnty;  ?>
											<a href="##" onclick="openmyfinrcv('<?=$po_id;?>','<?=$company_id;?>','<?=$receive_date;?>','<?=$item_id;?>')"><? echo fn_number_format($fin_rcv_qnty,0,".",","); ?></a>
											</td>

											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></a></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>

											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>

											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" ></td>

											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
											<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"></td>
											<td style="word-wrap: break-word;word-break: break-all;" width=""></td>
										</tr>
									<?
									$i++;
								}
							}
						}
					?>

					</table>
					<table width="4395" cellspacing="0" border="1" class="tbl_bottom" rules="all" id="report_table_footer" >
						<tr>

							<td width="30" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
							<td width="100" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
							<td width="100" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
							<td width="130" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
							<td width="130" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
							<td width="100" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
							<td width="130" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
							<td width="100" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
							<td width="100" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
							<td width="130" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
							<td width="130" style="word-wrap: break-word;word-break: break-all;">&nbsp;</td>
							<td width="100" style="word-wrap: break-word;word-break: break-all;" align="right">Total</td>
							<td width="80"  style="word-wrap: break-word;word-break: break-all;" align="right" id="total_cut_td" ><? echo $tot_cutting_qty;?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_printissue_td"><? echo $tot_print_qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_printrcv_td"><?  echo $tot_printRec_qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_emb_iss"><? echo $tot_emb_qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_emb_re"><? echo $tot_embRec_qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_wash_iss"><? echo $tot_wash_qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_wash_re"><? echo $tot_washRec_qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_sp_iss"><? echo $tot_special_qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_sp_re"><? echo $tot_specialRec_qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_sewin_inhouse_td"><? echo $tot_sewIn_inQty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_sewin_outbound_td"><? echo $tot_sewIn_outQty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_sewin_td"><? echo $tot_sewing_input; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_sewout_inhouse_td"><? echo $tot_sewOut_inQty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_sewout_outbound_td"><? echo $tot_sewOut_outQty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_sewout_td"><? echo $tot_sewing_output; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_fin_rcv_td"><? echo $fin_rcv_total; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_iron_in_td"><?  echo $tot_ironIn_qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_iron_out_td"><?  echo $tot_ironOut_qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_iron_td"><?  echo $tot_iron_qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_re_iron_td"><?  echo $tot_reIron_qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id=""><? echo $tot_polyIn_Qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id=""><? echo $tot_polyOut_Qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id=""><? echo $tot_poly_qty_sum; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_finishin_td"><? echo $tot_finishIn_qty; ?></td>
							<td width="80"  style="word-wrap: break-word;word-break: break-all;"align="right" id="total_finishout_td"><? echo $tot_finishOut_qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_finish_td"><? echo $tot_finishing_qty; ?></td>
							<td width="80"  style="word-wrap: break-word;word-break: break-all;"align="right" id="total_carton_td"><? echo $tot_carton_qty; ?></td>
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_prod_dzn_td"><?  echo number_format($total_prod_dzn,2); ?></td>

							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_rej_value_td"><? echo number_format($tot_reject_Qty,2); ?></td >
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_rej_value_td"><? echo number_format($tot_rejCutting_qty,2); ?></td >
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_rej_value_td"><? echo number_format($tot_rejPrint_qty,2); ?></td >
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_rej_value_td"><? echo number_format($tot_rejSewing_qty,2); ?></td >
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_rej_value_td"><? echo number_format($tot_rejIron_qty,2); ?></td >
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_rej_value_td"><? echo number_format($tot_rejFinish_qty,2); ?></td >

							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_ex_fac_td1"><? echo number_format($tot_ex_fac,2); ?></td >
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_ex_fac_td2"><? echo number_format($tot_ex_fac_fob,2); ?></td >
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_ex_fac_td3"><? echo number_format($tot_fac_bal,2); ?></td >
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_ex_fac_td4"><? echo number_format($tot_fac_bal_fob,2); ?></td >
							<td>&nbsp;</td>
					</tr>
					</table>

				</div>


				<?
			}
			else if($type==2)// Date Location Floor & Line
			{
			 	?>
				<h5 style="width:600px; float:left; background-color:#FCF"><strong>Production-Regular Order</strong></h5>
				<table width="4320px" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
				   <thead>
						   <tr>
								<th width="30">Sl.</th>
								<th width="100">Working Factory</th>
								<th width="100">Job No</th>
								<th width="130">Order Number</th>
								<th width="100">Buyer Name</th>
								<th width="130">Style Name</th>
								<th width="100">File No</th>
								<th width="100">Internal Ref</th>
								<th width="130">Item Name</th>
								<th width="130" >Gtms Color</th>
								<th width="100">Production Date</th>
								<th width="100">Status</th>
								<th width="100">Location</th>
								<th width="100">Floor</th>
								<th width="100">Sewing Line No</th>
								<th width="80">Cutting</th>
								<th width="80">Sent to prnt</th>
								<th width="80">Rev prn/Emb</th>

								<th width="80">Sent to Emb</th>
								<th width="80">Rev Emb</th>
								<th width="80">Sent to Wash</th>
								<th width="80">Rev Wash</th>
								<th width="80">Sent to Sp. Works</th>
								<th width="80">Rev Sp. Works</th>

								<th width="80">Sewing In (Inhouse)</th>
								<th width="80">Sewing In (Out-bound)</th>
								<th width="80">Total Sewing Input</th>
								<th width="80">Sewing Out (Inhouse)</th>
								<th width="80">Sewing Out (Out-bound)</th>
								<th width="80">Total Sewing Out</th>
								<th width="80">Iron Qty (Inhouse)</th>
								<th width="80">Iron Qty (Out-bound)</th>
								<th width="80">Iron Qty</th>
								<th width="80">Re-Iron Qty </th>
								<th width="80">Poly Qty (Inhouse)</th>
						        <th width="80">Poly Qty (Out-bound)</th>
						        <th width="80">Total Poly Qty</th>
								<th width="80">Finish Qty (Inhouse)</th>
								<th width="80">Finish Qty (Out-bound)</th>
								<th width="80">Total Finish Qty</th>
								<th width="80">Today Carton</th>
								<th width="80">Prod/Dzn</th>
								<th width="80" >Reject Qty</th>
								<th width="80" >Cutting Reject</th>
								<th width="80" >Print,Emb,Wash Reject</th>
								<th width="80">Sewing Reject</th>
								<th width="80" >Iron Reject</th>
								<th width="80" >Packing & Finishing Reject</th>
								<th width="">Remarks</th>
						   </tr>
					</thead>
				</table>
				<div style="max-height:425px; overflow-y:scroll; width:4340px"  id="scroll_body">
					<table cellspacing="0" border="1" class="rpt_table"  width="4320px" rules="all" id="table_body" >
						<?
							$i=1;
							foreach($all_data_arr as $po_id=>$po_data)
							{
								foreach($po_data as $prod_date=>$prod_date_data)
								{
									foreach($prod_date_data as $item_id=>$item_data)
									{
										foreach($item_data as $color_id=>$color_data)
										{
											foreach($color_data as $location_id=>$location_data)
											{
												foreach($location_data as $floor_id=>$floor_data)
												{
													$ex_linedata='';
													$ex_linedata=array_filter(array_unique(explode('__',$floor_data)));
													foreach($ex_linedata as $data_all)
													{
															$line_id=''; $buyer_name=''; $job_no=''; $po_no=''; $style_ref=''; $ref_no=''; $file_no=''; $company_id=''; $prod_source=''; $resource_allo='';
															$ex_data=array_filter(explode('##',$data_all));
															//echo $floor_id.'='; //die;
														if($ex_data[1]!="")
														{
															if($ex_data[0]=="") $line_id=0;
															else $line_id=$ex_data[0];
															$buyer_name=$ex_data[1];
															$job_no=$ex_data[2];
															$po_no=$ex_data[3];
															$style_ref=$ex_data[4];
															$ref_no=$ex_data[5];
															$file_no=$ex_data[6];
															//$company_id=$ex_data[7];
															$prod_source=$ex_data[7];
															$resource_allo=$ex_data[8];
															$serving_comp_id=$ex_data[9];
															$serving_company='';
															// new development
															if($ex_data[7]==1)
															{
																$serving_company=$company_short_library[$serving_comp_id];
															}
															else if($ex_data[7]==3)
															{
																$serving_company=$supplier_arr[$serving_comp_id];
															}

															/*if($prod_source==1)
															{
																$serving_company=$company_short_library[$ex_data[10]];
															}
															else if($prod_source==3)
															{
																$serving_company=$supplier_arr[$ex_data[10]];
															}*/

															if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

															$cutting_qty=$print_qty=$printRec_qty=$emb_qty=$embRec_qty=$wash_qty=$washRec_qty=$special_qty=$specialRec_qty=$sewIn_inQty=$sewIn_outQty=$sewing_input_total=$sewOut_inQty=$sewOut_outQty=$sewing_output_total=$ironIn_qty=$ironOut_qty=$iron_qty_total=$reIron_qty=$finishIn_qty=$finishOut_qty=$finishing_qty=$carton_qty=$rejFinish_qty=$rejSewing_qty=$reject_Qty=0;

															$cutting_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['1']['0']['pQty'];

															$print_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['2']['1']['embQty'];

															$printRec_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['3']['1']['embQty'];

															$emb_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['2']['2']['embQty'];

															$embRec_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['3']['2']['embQty'];

															$wash_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['2']['3']['embQty'];

															$washRec_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['3']['3']['embQty'];

															$special_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['2']['4']['embQty'];

															$specialRec_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['3']['4']['embQty'];

															$sewIn_inQty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['4']['1']['sQty'];

															$sewIn_outQty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['4']['3']['sQty'];

															$sewOut_inQty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['5']['1']['sQty'];

															$sewOut_outQty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['5']['3']['sQty'];

															$ironIn_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['7']['1']['sQty'];

															$ironOut_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['7']['3']['sQty'];

															$reIron_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['7']['0']['reQty'];


															$polyIn_Qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['11']['1']['sQty'];

															$polyOut_Qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['11']['3']['sQty'];

															$finishIn_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['8']['1']['sQty'];

															$finishOut_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['8']['3']['sQty'];

															$carton_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id]['0']['0']['0']['crtQty'];
															// $rejFinish_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['8']['0']['rejectQty'];
															$rejSewing_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['5']['0']['rejectQty'];

															$rejCutting_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['1']['0']['rejectQty'];

															$rejPrint_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['1']['0']['rejectQty'];

															$rejIron_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['7']['0']['rejectQty'];

															$rejFinish_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['8']['0']['rejectQty'];

															$rejSewing_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['5']['0']['rejectQty'];

															$sewing_line='';
															if($resource_allo==1)
															{
																$line_number=explode(",",$prod_reso_arr[$line_id]);
																foreach($line_number as $val)
																{
																	if($sewing_line=='') $sewing_line=$line_library[$val]; else $sewing_line.=", ".$line_library[$val];
																}
															}
															else $sewing_line=$line_library[$line_id];
															if($line_id!="") $swing_line_id=$line_id; else $swing_line_id=0;
														?>
															<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
																<td width="30"><? echo $i;?></td>
																<td width="100"><p><? echo $serving_company; ?></p></td>
																<td width="100"><p><? echo $job_no;?></p></td>
																<td width="130"><p><a href="##" onclick="openmypage_order(<? echo $po_id;?>,<? echo $item_id; ?>,'orderQnty_popup');" ><? echo $po_no; ?></a></p></td>
																<td width="100"><p><? echo $buyer_short_library[$buyer_name]; ?></p></td>
																<td width="130"><p><? echo $style_ref; ?></p></td>
																<td width="100"><p><? echo $file_no; ?></p></td>
																<td width="100"><p><? echo $ref_no; ?></p></td>
																<td width="130"><p><? echo $garments_item[$item_id]; ?></p></td>
																<td width="130" ><p><? echo $color_library_arr[$color_id]; ?></p></td>
																<td width="100"><p><? echo change_date_format($prod_date); ?></p></td>
																<td width="100"><p><? echo $knitting_source[$prod_source]; ?></p></td>

																<td width="100"><p><? echo $location_library[$location_id]; ?></p></td>
																<td width="100"><p><? echo $floor_library[$floor_id]; ?></p></td>
																<td width="100" align="center"><p><? echo $sewing_line; ?></p></td>

																<td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location_id;?>,<? echo $floor_id;?>,<? echo $swing_line_id;?>,'Cutting Info','cutting_popup_location');" ><? echo $cutting_qty; ?></a></td>
																<td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location_id;?>,<? echo $floor_id;?>,'<? echo $line_id;?>','Printing Issue Info','printing_issue_popup_location');" ><? echo $print_qty; ?></a></td>
																<td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location_id;?>,<? echo $floor_id;?>,'<? echo $line_id;?>','Printing Receive Info','printing_receive_popup_location');" ><? echo $printRec_qty; ?></a></td>
																<td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location_id;?>,<? echo $floor_id;?>,'<? echo $line_id;?>','Embroidery Issue Info','embroi_issue_popup_location');" ><? echo $emb_qty; ?></a></td>
																<td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location_id;?>,<? echo $floor_id;?>,'<? echo $line_id;?>','Embroidery Receive Info','embroi_receive_popup_location');" ><? echo $embRec_qty; ?></a></td>
																<td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location_id;?>,<? echo $floor_id;?>,'<? echo $line_id;?>','Wash Issue Info','wash_issue_popup_location');" ><? echo $wash_qty; ?></a></td>
																<td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location_id;?>,<? echo $floor_id;?>,'<? echo $line_id;?>','Wash Receive Info','wash_receive_popup_location');" ><? echo $washRec_qty; ?></a></td>
																<td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location_id;?>,<? echo $floor_id;?>,'<? echo $line_id;?>','Spetial Work Info','sp_issue_popup_location');" ><? echo $special_qty; ?></a></td>
																<td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location_id;?>,<? echo $floor_id;?>,'<? echo $line_id;?>','Spetial Work Info','sp_receive_popup_location');" ><? echo $specialRec_qty; ?></a></td>

																<td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__0';?>','<? echo $color_id;?>','1','4','sewingQnty_popup');" ><? echo $sewIn_inQty; ?></a></td>
																<td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__0';?>','<? echo $color_id;?>','3','4','sewingQnty_popup');" ><? echo $sewIn_outQty; ?></a></td>
																<td width="80" align="right"><? $sewing_input_total=$sewIn_inQty+$sewIn_outQty; echo $sewing_input_total; ?></td>
																<td width="80" align="right"><a href="##" onclick="openmypage_sew_output2(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__'.$serving_comp_id;?>','<? echo $color_id;?>','1','5','sewingQnty_popup',<? echo $location_id;?>,<? echo $floor_id;?>,'<? echo $line_id; ?>');" ><? echo $sewOut_inQty; ?></a></td>
																<td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__'.$serving_comp_id;?>','3','5','sewingQnty_popup');" ><? echo $sewOut_outQty; ?></a></td>
																<td width="80" align="right"><? $sewing_output_total=$sewOut_inQty+$sewOut_outQty; echo $sewing_output_total; ?></td>
																<td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'1','0','ironQnty_popup');" ><? echo $ironIn_qty; ?></a></td>
																<td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'3','0','ironQnty_popup');" ><? echo $ironOut_qty; ?></a></td>
																<td width="80" align="right"><? $iron_qty_total=$ironIn_qty+$ironOut_qty; echo $iron_qty_total; ?></a></td>
																<td width="80" align="right"><? echo $reIron_qty; ?></td>
																<td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__'.$serving_comp_id;?>','1','11','sewingQnty_popup');" ><? echo $polyIn_Qty; ?></td>
																<td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__'.$serving_comp_id;?>','3','11','sewingQnty_popup');" ><? echo $polyOut_Qty; ?></td>
																<td width="80" align="right"><? $poly_qty_total=$polyIn_Qty+$polyOut_Qty; echo $poly_qty_total; ?></td>
																<td width="80" align="right"><? echo $finishIn_qty; ?></td>
																<td width="80" align="right"><? echo $finishOut_qty; ?></td>
																<td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'0','0','finishQnty_popup');" ><? $finishing_qty=$finishIn_qty+$finishOut_qty; echo $finishing_qty; ?></a></td>
																<td width="80" align="right"><? echo $carton_qty; ?></td>
																<? $prod_dzn=0; $prod_dzn=$sewing_output_total / 12 ; $total_prod_dzn=0; $total_prod_dzn+=$prod_dzn; ?>
																<td width="80" align="right"><? if($prod_dzn!=0) echo number_format($prod_dzn,2); else echo "0"; ?></td>
																				<? //$cm_per=0; $cm_per=$cm_per_dzn[$rows[csf("job_no_mst")]] ;
																$rej_title='Fin '.$rejFinish_qty.', Sew '.$rejSewing_qty.', Iron '.$rejIron_qty.', Print '.$rejPrint_qty.', Cut '.$rejCutting_qty;
																?>
																<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" title="<? echo $rej_title;?>" >
																<? $reject_Qty=$rejFinish_qty+$rejSewing_qty+$rejIron_qty+$rejPrint_qty+$rejCutting_qty; echo $reject_Qty;  ?>
																</td>

																<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" title="<? echo $rej_title;?>" >
																<a href="##" onclick="openmypage_rej(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $ex_data[9];?>,'0','cutting_reject_popup');" ><? echo $rejCutting_qty;  ?></a>
																</td>
																<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" title="<? echo $rej_title;?>" >
																<a href="##" onclick="openmypage_rej(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $ex_data[9];?>,'0','emb_reject_popup');" ><? echo $rejPrint_qty;  ?></a>
																</td>
																<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" title="<? echo $rej_title;?>" >
																<a href="##" onclick="openmypage_rej(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $ex_data[9];?>,'0','sewing_reject_popup');" ><? echo $rejSewing_qty;  ?></a>
																</td>
																<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" title="<? echo $rej_title;?>" >
																<a href="##" onclick="openmypage_rej(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $ex_data[9];?>,'0','iron_reject_popup');" ><? echo $rejIron_qty;  ?></a>
																</td>
																<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" title="<? echo $rej_title;?>" >
																<a href="##" onclick="openmypage_rej(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $ex_data[9];?>,'0','finishing_reject_popup');" ><? echo $rejFinish_qty;  ?></a>
																</td>
																<td><a href="##"  onclick="openmypage_remark(<? echo $po_id;?>,'date_wise_production_report');" > Veiw </a></td>
														</tr>
														<?
															$tot_cutting_qty+=$cutting_qty;
															$tot_print_qty+=$print_qty;
															$tot_printRec_qty+=$printRec_qty;
															$tot_emb_qty+=$emb_qty;
															$tot_embRec_qty+=$embRec_qty;
															$tot_wash_qty+=$wash_qty;
															$tot_washRec_qty+=$washRec_qty;
															$tot_special_qty+=$special_qty;
															$tot_specialRec_qty+=$specialRec_qty;
															$tot_sewIn_inQty+=$sewIn_inQty;
															$tot_sewIn_outQty+=$sewIn_outQty;
															$tot_sewing_input+=$sewing_input_total;
															$tot_sewOut_inQty+=$sewOut_inQty;
															$tot_sewOut_outQty+=$sewOut_outQty;
															$tot_sewing_output+=$sewing_output_total;
															$tot_ironIn_qty+=$ironIn_qty;
															$tot_ironOut_qty+=$ironOut_qty;
															$tot_iron_qty+=$iron_qty_total;
															$tot_polyIn_Qty+=$polyIn_Qty;
															$tot_polyOut_Qty+=$polyOut_Qty;
															$tot_poly_qty_sum+=$poly_qty_total;
															$tot_reIron_qty+=$reIron_qty;
															$tot_finishIn_qty+=$finishIn_qty;
															$tot_finishOut_qty+=$finishOut_qty;
															$tot_finishing_qty+=$finishing_qty;
															$tot_carton_qty+=$carton_qty;
															$tot_rejFinish_qty+=$rejFinish_qty;
															$tot_rejSewing_qty+=$rejSewing_qty;
															$tot_reject_Qty+=$reject_Qty;
															// $total_prod_dzn+=$prod_dzn;
															//$tot_reject_Qty+=$reject_Qty;
															$tot_rejCutting_qty+=$rejCutting_qty;
															$tot_rejPrint_qty+=$rejPrint_qty;
															//$tot_rejSewing_qty+=$rejSewing_qty;
															$tot_rejIron_qty+=$rejIron_qty;
														//$tot_rejFinish_qty+=$rejFinish_qty;

															$i++;
														}
													}
												}
											}
										}
									}
								}
							}//end foreach 1st
						//unset($pro_dlfl_sql_res);
						?>
				    </table>
					<table width="4320px" cellspacing="0" border="1" class="tbl_bottom" rules="all" id="report_table_footer" >

							<tr>
								<td width="30">&nbsp;</td>
								<td width="100">&nbsp;</td>
								<td width="100">&nbsp;</td>
								<td width="130">&nbsp;</td>
								<td width="100">&nbsp;</td>
								<td width="130">&nbsp;</td>
								<td width="130">&nbsp;</td>
								<td width="100">&nbsp;</td>
								<td width="100">&nbsp;</td>
								<td width="130">&nbsp;</td>
								<td width="100">&nbsp;</td>
								<td width="100">&nbsp;</td>
								<td width="100">&nbsp;</td>
								<td width="100">&nbsp;</td>
								<td width="100">Totals</td>
								<td width="80" align="right" id="total_cut_td"><? echo $tot_cutting_qty;?></td>
								<td width="80" align="right" id="total_printissue_td"><? echo $tot_print_qty; ?> </td>
								<td width="80" align="right" id="total_printrcv_td"><? echo $tot_printRec_qty;  ?>  </td>
								<td width="80" align="right" id="total_emb_iss"><? echo $tot_emb_qty; ?> </td>
								<td width="80" align="right" id="total_emb_re"><? echo $tot_embRec_qty;  ?>  </td>
								<td width="80" align="right" id="total_wash_iss"><? echo $tot_wash_qty; ?> </td>
								<td width="80" align="right" id="total_wash_re"><? echo $tot_washRec_qty;  ?>  </td>
								<td width="80" align="right" id="total_sp_iss"><? echo $tot_special_qty; ?> </td>
								<td width="80" align="right" id="total_sp_re"><? echo $tot_specialRec_qty;  ?>  </td>
								<td width="80" align="right" id="total_sewin_inhouse_td"><? echo $tot_sewIn_inQty; ?></td>
								<td width="80" align="right" id="total_sewin_outbound_td"><? echo $tot_sewIn_outQty; ?></td>
								<td width="80" align="right" id="total_sewin_td"><? echo $tot_sewing_input;  ?> </th>
								<td width="80" align="right" id="total_sewout_inhouse_td"><? echo $tot_sewOut_inQty; ?></td>
								<td width="80" align="right" id="total_sewout_outbound_td"><? echo $tot_sewOut_outQty; ?></td>
								<td width="80" align="right" id="total_sewout_td"><? echo $tot_sewing_output; ?> </td>

								<td width="80" align="right" id="total_iron_in_td"><?  echo $tot_ironIn_qty; ?></td>
								<td width="80" align="right" id="total_iron_out_td"><?  echo $tot_ironOut_qty; ?></td>
								<td width="80" align="right" id="total_iron_td"><?  echo $tot_iron_qty; ?></td>
								<td width="80" align="right" id="total_re_iron_td"><? echo $tot_reIron_qty; ?></td>
								<td width="80" align="right" id=""><? echo $tot_polyIn_Qty; ?></td>
							    <td width="80" align="right" id=""><? echo $tot_polyOut_Qty; ?></td>
							    <td width="80" align="right" id=""><? echo $tot_poly_qty_sum; ?></td>
								<td width="80" align="right" id=""><? echo $tot_finishIn_qty; ?></td>
								<td width="80" align="right" id=""><? echo $tot_finishOut_qty; ?></td>
								<td width="80" align="right" id=""><?  echo $tot_finishing_qty; ?></td>
								<td width="80" align="right" id=""><? echo $tot_carton_qty; ?></td>
								<td width="80" align="right" id=""><?  echo number_format($total_prod_dzn,2); ?></td>
								<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_rej_value_td"><? echo number_format($tot_reject_Qty,2); ?></td >
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_rej_value_td"><? echo number_format($tot_rejCutting_qty,2); ?></td >
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_rej_value_td"><? echo number_format($tot_rejPrint_qty,2); ?></td >
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_rej_value_td"><? echo number_format($tot_rejSewing_qty,2); ?></td >
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_rej_value_td"><? echo number_format($tot_rejIron_qty,2); ?></td >
							<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right" id="total_rej_value_td"><? echo number_format($tot_rejFinish_qty,2); ?></td >
						<td>&nbsp;</td>
							 </tr>

					</table>
				</div>


				<?
			}// end if condition of type

			/*=============================================================================================/
	        /											Subcon part										   /
	        /============================================================================================ */

			if(str_replace("'","",trim($cbo_subcon))==2) //yes
			{
				$fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
				$toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );

				if(str_replace("'","",$cbo_floor)==0) $sub_floor_name="";else $sub_floor_name=" and b.floor_id in($cbo_floor)";
				if ($location==0) $sub_location_cond=""; else $sub_location_cond=" and b.location_id in($location) ";
				?>

					<h5 style="width:800px;float: left; background-color:#FCF"><strong>Production-Subcontract Order (Inbound) Details</strong></h5>

						<table width="1590" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_2">
							<thead>
								<tr>
									<th width="30">Sl.</th>
									<th width="100">Working Factory</th>
									<th width="100">Job No</th>
									<th width="130">Order No</th>
									<th width="100">Buyer </th>
									<th width="130">Style </th>
									<th width="130">Item Name</th>
									<th width="75">Production Date</th>

									<th width="100">Floor</th>
									<th width="100">Sewing Line</th>

									<th width="90">Cutting</th>
									<th width="90">Sewing Input</th>
									<th width="90">Sewing Output</th>
									<th width="90">Iron Output</th>
									<th width="90">Gmts. Finishing</th>
									<th width="">Remarks</th>
								</tr>
							</thead>
						</table>
					<div style="max-height:300px; overflow-y:scroll; width:1610px" id="scroll_body2">
						<table border="1"  class="rpt_table"  width="1590" rules="all" id="sub_list_view">
							  <?
								$item_id_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
								$subcon_lc_comp2=str_replace("a.", "c.", $subcon_lc_comp);
								$subcon_work_comp2=str_replace("a.", "c.", $subcon_work_comp);

								$production_array=array();
								if($db_type==0)
								{
									$prod_sql= "SELECT c.order_id, c.production_date,c.floor_id,c.line_id,
										sum(CASE WHEN c.production_type ='1' THEN  c.production_qnty  ELSE 0 END) AS cutting_qnty,
										sum(CASE WHEN c.production_type ='7' THEN  c.production_qnty  ELSE 0 END) AS sewing_input_qnty,
										sum(CASE WHEN c.production_type ='2' THEN  c.production_qnty  ELSE 0 END) AS sewingout_qnty,
										sum(CASE WHEN c.production_type ='3' THEN  c.production_qnty  ELSE 0 END) AS ironout_qnty,
										sum(CASE WHEN c.production_type ='4' THEN  c.production_qnty  ELSE 0 END) AS gmts_fin_qnty
									from
										subcon_gmts_prod_dtls c
									where c.status_active=1 and c.is_deleted=0 and c.production_date between $txt_date_from and $txt_date_to $subcon_lc_comp2 $subcon_work_comp2  group by c.order_id, c.production_date,c.floor_id";
								}
								else
								{
									 $prod_sql= "SELECT c.order_id,c.gmts_item_id, c.production_date,c.floor_id,c.line_id,
										NVL(sum(CASE WHEN c.production_type ='1' THEN  c.production_qnty  ELSE 0 END),0) AS cutting_qnty,
										sum(CASE WHEN c.production_type ='7' THEN  c.production_qnty  ELSE 0 END) AS sewing_input_qnty,

										NVL(sum(CASE WHEN c.production_type ='2' THEN  c.production_qnty  ELSE 0 END),0) AS sewingout_qnty,
										NVL(sum(CASE WHEN c.production_type ='3' THEN  c.production_qnty  ELSE 0 END),0) AS ironout_qnty,
										NVL(sum(CASE WHEN c.production_type ='4' THEN  c.production_qnty  ELSE 0 END),0) AS gmts_fin_qnty
									from
										subcon_gmts_prod_dtls c
									where c.status_active=1 and c.is_deleted=0 and c.production_date between $txt_date_from and $txt_date_to $subcon_lc_comp2 $subcon_work_comp2 group by c.order_id,c.gmts_item_id, c.production_date,c.floor_id,c.line_id";
								}
								$prod_sql_result= sql_select($prod_sql);
								// echo $prod_sql;//die;
								foreach($prod_sql_result as $proRes)
								{
									$production_array[$proRes[csf("order_id")]][$proRes[csf("gmts_item_id")]][$proRes[csf("production_date")]][$proRes[csf("floor_id")]][$proRes[csf("line_id")]]['cutting_qnty']=$proRes[csf("cutting_qnty")];
									$production_array[$proRes[csf("order_id")]][$proRes[csf("gmts_item_id")]][$proRes[csf("production_date")]][$proRes[csf("floor_id")]][$proRes[csf("line_id")]]['sewing_input_qnty']=$proRes[csf("sewing_input_qnty")];
									$production_array[$proRes[csf("order_id")]][$proRes[csf("gmts_item_id")]][$proRes[csf("production_date")]][$proRes[csf("floor_id")]][$proRes[csf("line_id")]]['sewingout_qnty']=$proRes[csf("sewingout_qnty")];
									$production_array[$proRes[csf("order_id")]][$proRes[csf("gmts_item_id")]][$proRes[csf("production_date")]][$proRes[csf("floor_id")]][$proRes[csf("line_id")]]['ironout_qnty']=$proRes[csf("ironout_qnty")];
									$production_array[$proRes[csf("order_id")]][$proRes[csf("gmts_item_id")]][$proRes[csf("production_date")]][$proRes[csf("floor_id")]][$proRes[csf("line_id")]]['gmts_fin_qnty']=$proRes[csf("gmts_fin_qnty")];
								}
								// echo "<pre>";
								// print_r($production_array);
								if($db_type==0)
								{
									$order_sql= "SELECT c.id, c.order_no, c.cust_style_ref, a.company_id, a.party_id, a.location_id, a.job_no_prefix_num, b.gmts_item_id, sum(CASE WHEN b.order_id=c.id THEN b.production_qnty ELSE 0 END) AS production_qnty, b.production_date, b.line_id,b.floor_id,b.prod_reso_allo
									from subcon_ord_mst a, subcon_gmts_prod_dtls b, subcon_ord_dtls c
									where b.order_id=c.id and b.production_date between $txt_date_from and $txt_date_to $subcon_lc_comp $subcon_work_comp $sub_floor_name $sub_location_cond and a.subcon_job=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
									group by b.order_id, b.production_date
									order by b.production_date";
								}
								else
								{
									 $order_sql= "SELECT c.id, c.order_no, c.cust_style_ref, a.company_id, a.party_id, a.location_id, a.job_no_prefix_num, b.gmts_item_id, sum(CASE WHEN b.order_id=c.id THEN b.production_qnty ELSE 0 END) AS production_qnty,  b.production_date, b.line_id,b.floor_id,b.prod_reso_allo
									 from subcon_ord_mst a, subcon_gmts_prod_dtls b, subcon_ord_dtls c
									 where b.order_id=c.id and b.production_date between $txt_date_from and $txt_date_to  $subcon_lc_comp $subcon_work_comp	and a.subcon_job=c.job_no_mst $sub_floor_name $sub_location_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
									 group by b.order_id, c.id, c.order_no, c.cust_style_ref, a.company_id, a.job_no_prefix_num, a.party_id, a.company_id, a.party_id, a.location_id, b.gmts_item_id, b.production_date, b.line_id,b.floor_id,b.prod_reso_allo
									 order by c.id";
								}

							   // echo $order_sql; die;

								$order_sql_result=sql_select($order_sql);
								   $j=0;$k=0;
								   $total_cutt = 0;
								   //$po_item_line_array=array();
								   foreach($order_sql_result as $orderRes)
								   {

									   	//if( $po_item_line_array[$orderRes[csf("id")]][$orderRes[csf("gmt_item_id")]][$orderRes[csf("line_id")]]=="" )
									   	//{
									   		$j++;
									   		if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

									   		$sewing_line='';
									   		if($orderRes[csf('prod_reso_allo')]==1)
									   		{
									   			$line_number=explode(",",$prod_reso_arr[$orderRes[csf("line_id")]]);
									   			foreach($line_number as $val)
									   			{
									   				if($sewing_line=='') $sewing_line=$line_library[$val]; else $sewing_line.=", ".$line_library[$val];
									   			}
									   		}
									   		else {$sewing_line=$line_library[$orderRes[csf("line_id")]];}
									   		$po_id=$orderRes[csf("id")];
									   		$item_id=$orderRes[csf("gmts_item_id")];
									   		$prod_date=$orderRes[csf("production_date")];


									   		?>
									   		<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_3nd<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_3nd<? echo $j; ?>" style="height:20px">
									   			<td width="30" ><? echo $j; ?></td>
									   			<td width="100"><p><? echo $company_short_library[$orderRes[csf("company_id")]]; ?></p></td>
									   			<td width="100" align="center"><p><? echo $orderRes[csf("job_no_prefix_num")]; ?></p></td>
									   			<td width="130"><p><? echo $orderRes[csf("order_no")]; ?></p></td>
									   			<td width="100"><? echo $buyer_short_library[$orderRes[csf("party_id")]]; ?></td>
									   			<td width="130"><p><? echo $orderRes[csf("cust_style_ref")]; ?></p></td>
									   			<td width="130"><p><? echo $garments_item[$orderRes[csf("gmts_item_id")]];?></p></td>
									   			<td width="75" bgcolor="<? echo $color; ?>"><? echo change_date_format($orderRes[csf("production_date")]);  ?></td>

									   			<td width="100" align="left"><p><? echo $floor_library[$orderRes[csf('floor_id')]]; ?></p></td>

									   			<td width="100" align="left"><p><? echo $sewing_line; ?></p></td>

									   			<td width="90" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__0';?>','1','1','production_popup_subcon');" ><? echo $cutting= $production_array[$orderRes[csf("id")]][$orderRes[csf("gmts_item_id")]][$orderRes[csf("production_date")]][$orderRes[csf("floor_id")]][$orderRes[csf("line_id")]]['cutting_qnty']; $total_cutt+=$cutting; ?></a></td>

									   			<td width="90" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__0';?>','1','7','production_popup_subcon');" ><? echo $input= $production_array[$orderRes[csf("id")]][$orderRes[csf("gmts_item_id")]][$orderRes[csf("production_date")]][$orderRes[csf("floor_id")]][$orderRes[csf("line_id")]]['sewing_input_qnty']; $total_sewinput+=$input; ?></a></td>

									   			<td width="90" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__0';?>','1','2','production_popup_subcon');" ><? echo $output=$production_array[$orderRes[csf("id")]][$orderRes[csf("gmts_item_id")]][$orderRes[csf("production_date")]][$orderRes[csf("floor_id")]][$orderRes[csf("line_id")]]['sewingout_qnty']; $total_sew+=$output; ?></a></td>
									   			<td width="90" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__0';?>','1','3','production_popup_subcon');" ><? echo $iron= $production_array[$orderRes[csf("id")]][$orderRes[csf("gmts_item_id")]][$orderRes[csf("production_date")]][$orderRes[csf("floor_id")]][$orderRes[csf("line_id")]]['ironout_qnty']; $total_iron_sub+=$iron; ?></a></td>
									   			<td width="90" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__0';?>','1','4','production_popup_subcon');" ><? echo $fin= $production_array[$orderRes[csf("id")]][$orderRes[csf("gmts_item_id")]][$orderRes[csf("production_date")]][$orderRes[csf("floor_id")]][$orderRes[csf("line_id")]]['gmts_fin_qnty']; $total_gmtfin+=$fin; ?></a></td>

									   			<td width="">&nbsp;</td>
									   		</tr>
									   		<?
									   		//$po_item_line_array[$orderRes[csf("id")]][$orderRes[csf("gmt_item_id")]]=$orderRes[csf("line_id")];

									   	//}


								   }
								  ?>
								</table>

								<table border="1" class="tbl_bottom"  width="1590" rules="all" id="report_table_footer2" >
									<tr>
										<td width="30">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="130">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="130">&nbsp;</td>
										<td width="130">&nbsp;</td>

										<td width="75"></td>
										<td width="100">&nbsp;</td>
										<td width="100">Total:</td>

										<td width="90" id="total_cutt"><? echo $total_cutt; ?></td>
										<td width="90" id="total_sew_input"><? echo $total_sew_input; ?></td>
										<td width="90" id="total_sew"><? echo $total_sew; ?></td>
										<td width="90" id="total_iron_sub"><? echo $total_iron_sub; ?></td>
										<td width="90" id="total_gmtfin"><? echo $total_gmtfin; ?></td>
										<td width=""></td>
									 </tr>
							 </table>

					</div>


				<?
			}
			//-------------------------------------------END Show Date Location Floor & Line Wise------------------------
			?>
	 </div>
		<?
		$html = ob_get_contents();
		ob_clean();
		//$new_link=create_delete_report_file( $html, 1, 1, "../../../" );

		// echo "$html####";
		foreach (glob($user_id."_*.xls") as $filename)
		{
			@unlink($filename);
		}
		$name=$user_id."_".time().".xls";
		$create_new_excel = fopen($name, 'w');
		$is_created = fwrite($create_new_excel,$html);
		//$new_link=create_delete_report_file( $html, 1, 1, "../../../" );
		echo $html."####".$name;
		exit();
	}


	elseif($ReportType==5)// Show4 Button excel by Kamrul
	{
		$garments_nature=str_replace("'","",$cbo_garments_nature);
		$cbo_floor=str_replace("'","",$cbo_floor);
		//echo $cbo_floor;
		//if($garments_nature==1)$garments_nature="";
		 if($garments_nature==1) $garmentsNature=""; else $garmentsNature=" and garments_nature=$garments_nature";

		$location = str_replace("'","",$cbo_location);
		if(str_replace("'","",$cbo_company_name)==0) $company_name=""; else $company_name=" and b.company_name=$cbo_company_name";
		if(str_replace("'","",$cbo_buyer_name)==0)$buyer_name="";else $buyer_name=" and a.buyer_name=$cbo_buyer_name";
		if($cbo_floor==0) $floor_name="";else $floor_name=" and floor_id in($cbo_floor)";
		//echo $floor_name;die;
		if($cbo_floor==0) $floor_id="";else $floor_id=" and floor_id in($cbo_floor)";
		if ($location==0) $location_cond=""; else $location_cond=" and location in($location) ";

		if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="")$txt_date="";
		else $txt_date=" and production_date between $txt_date_from and $txt_date_to";

		$fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
		$toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );
		//cbo_garments_nature
		$file_no = str_replace("'","",$txt_file_no);
		$internal_ref = str_replace("'","",$txt_internal_ref);
		$cbo_company_name = str_replace("'","",$cbo_company_name);
		$cbo_com_fac_name = str_replace("'","",$cbo_com_fac_name);
		$cbo_buyer_name = str_replace("'","",$cbo_buyer_name);
		$txt_style_ref  =  str_replace("'","",$txt_style_ref);

		if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='".trim($file_no)."' ";

		if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='".trim($internal_ref)."' ";

		if ($txt_style_ref=="") $style_ref_cond=""; else $style_ref_cond=" and a.style_ref_no='".trim($txt_style_ref)."' ";

		if ($location=="" || $location==0 ) $location_cond=""; else $location_cond=" and location in($location) ";

		if($cbo_com_fac_name=="") $working_factory_cond=""; else $working_factory_cond=" and serving_company in($cbo_com_fac_name)";
		// echo $working_factory_cond;
		if(str_replace("'","",$cbo_company_name)==0) $company_name_cond=""; else $company_name_cond=" and company_id=$cbo_company_name";

		$variable_setting = sql_select("select EX_FACTORY from variable_settings_production where company_name=$cbo_company_name and variable_list=1 and status_active=1");

		$exfactory_level = $variable_setting[0]["EX_FACTORY"];

        $job_arr=array();
        $job_arr2=array();
        if(str_replace("'","",$cbo_company_name)==0) $job_comp_cond=""; else $job_comp_cond="and a.company_name=$cbo_company_name ";
        /*==========================================================================================/
        /										main query											/
        /========================================================================================= */
		$job_sql="SELECT a.job_no_prefix_num, a.buyer_name, a.style_ref_no, a.company_name, b.id, b.po_number, b.grouping, b.file_no, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b, pro_garments_production_mst c where a.id=b.job_id and b.id=c.po_break_down_id and a.is_deleted=0 and c.is_deleted=0 and c.status_active=1 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $job_comp_cond $buyer_name $file_no_cond $internal_ref_cond $style_ref_cond $txt_date";

		// echo $job_sql;

		//and c.production_date between $txt_date_from and $txt_date_to

        $job_sql_res=sql_select($job_sql);

        $tot_rows=0;
        $poIds='';
        $poIds_array = array();
        foreach($job_sql_res as $row)
        {
            $tot_rows++;
            $poIds.=$row[csf("id")].",";
            $poIds_array[$row[csf("id")]]=$row[csf("id")];
            $job_arr[$row[csf("id")]]['job']=$row[csf("job_no_prefix_num")];
            $job_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
            $job_arr[$row[csf("id")]]['buyer']=$row[csf("buyer_name")];
            $job_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
            $job_arr[$row[csf("id")]]['file']=$row[csf("file_no")];
            $job_arr[$row[csf("id")]]['ref']=$row[csf("grouping")];
            $job_arr2[$row[csf("id")]]['pub_shipment_date']=$row[csf("pub_shipment_date")];
        }
        unset($job_sql_res);

        $txt_date_ex = str_replace("production_date", "ex_factory_date", $txt_date);
        $job_sql_ex="SELECT a.job_no_prefix_num, a.buyer_name, a.style_ref_no, a.company_name, b.id, b.po_number, b.grouping, b.file_no, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c where a.id=b.job_id and b.id=c.po_break_down_id and a.is_deleted=0 and c.is_deleted=0 and c.status_active=1 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $job_comp_cond $buyer_name $file_no_cond $internal_ref_cond $style_ref_cond $txt_date_ex";

		// echo $job_sql_ex;

        $job_sql_res_ex=sql_select($job_sql_ex);

        foreach($job_sql_res_ex as $row)
        {
        	$poIds_array[$row[csf("id")]]=$row[csf("id")];
            $job_arr[$row[csf("id")]]['job']=$row[csf("job_no_prefix_num")];
            $job_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
            $job_arr[$row[csf("id")]]['buyer']=$row[csf("buyer_name")];
            $job_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
            $job_arr[$row[csf("id")]]['file']=$row[csf("file_no")];
            $job_arr[$row[csf("id")]]['ref']=$row[csf("grouping")];
            $job_arr2[$row[csf("id")]]['pub_shipment_date']=$row[csf("pub_shipment_date")];
        }
        unset($job_sql_res_ex);
		// ============================== data store to gbl table ==================================
		$con = connect();
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from=1 and ENTRY_FORM=41");
		oci_commit($con);

		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 41, 1, $poIds_array, $empty_arr);//PO ID
		// ============================== end data store to gbl table ==================================

        $poIds_cond="";

       	$poIds=implode(",", $poIds_array);
         if($db_type==2 && count($poIds_array)>=1000)
        {
            $poIds_cond=" and (";
            $poIdsArr=array_chunk($poIds_array,999);
            foreach($poIdsArr as $ids)
            {
                $ids=implode(",",$ids);
                $poIds_cond.=" po_break_down_id in ($ids) or ";
            }
            $poIds_cond=chop($poIds_cond,'or ');
            $poIds_cond.=")";
        }
        else
        {
            $poIds_cond=" and po_break_down_id in ($poIds)";
        }


        $buyer_fullQty_arr=array();
        $prod_date_qty_arr=array();
        $prod_dlfl_qty_arr=array();
		$all_data_arr=array();
		$all_data_arrr=array();
        /*==========================================================================================/
        /										production data										/
        /========================================================================================= */
        $poIds_conds=str_replace("po_break_down_id", "a.po_break_down_id ", $poIds_cond);
        $sql_dtls="SELECT a.location, a.company_id, a.serving_company, a.floor_id, a.sewing_line, a.po_break_down_id,a.re_production_qty, a.production_date, a.item_number_id,c.color_number_id, a.production_type, a.production_source, a.embel_name, (a.prod_reso_allo) as prod_reso_allo, (b.production_qnty) as production_quantity,  (b.reject_qty) as reject_qnty, a.carton_qty as carton_qty
        from pro_garments_production_mst a ,pro_garments_production_dtls b ,wo_po_color_size_breakdown c,GBL_TEMP_ENGINE tmp
        where a.id=b.mst_id and b.color_size_break_down_id=c.id and c.po_break_down_id=a.po_break_down_id  and c.po_break_down_id =tmp.ref_val and tmp.entry_form=41 and tmp.ref_from=1 and tmp.user_id = $user_id and c.status_active in(1,2,3) and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  a.is_deleted=0 and a.status_active=1 $company_name_cond $working_factory_cond $txt_date $floor_name $location_cond $garmentsNature order by a.production_date ASC";

		// echo $sql_dtls;

		$sql_dtls_res=sql_select($sql_dtls);


		$w_company_cond='';
		if(!empty($cbo_com_fac_name))
		{
			$w_company_cond=" and a.company_id=$cbo_com_fac_name";
		}
		$company_cond='';
		if(!empty($cbo_company_name))
		{
			$company_cond=" and c.lc_company_id=$cbo_company_name";
		}
		$buyer_cond='';
		if(!empty($cbo_buyer_name))
		{
			$buyer_cond=" and d.buyer_name=$cbo_buyer_name";
		}
		$file_no_cond='';
		if(!empty($file_no))
		{
			$file_no_cond=" and b.file_no=$file_no";
		}
		$internal_ref_cond='';
		$internal_ref=str_replace("'", "", $internal_ref);
		if(!empty($internal_ref))
		{
			$internal_ref_cond=" and b.grouping='$internal_ref'";
		}

		$style_ref_cond='';
		$txt_style_ref=str_replace("'", "", $txt_style_ref);
		if(!empty($txt_style_ref))
		{
			$style_ref_cond=" and d.style_ref_no='$txt_style_ref'";
		}

		$location_cond='';
		if(!empty($location))
		{
			$location_cond=" and a.fini_location_id=$location";
		}
		$floor_cond='';
		if(!empty($cbo_floor))
		{
			$floor_cond=" and a.floor_id=$cbo_floor";
		}


		$poIds_conds=str_replace("a.po_break_down_id", "b.po_break_down_id ", $poIds_conds);
		$txt_rcv_date = str_replace("production_date", "a.receive_date", $txt_date);
        $fin_rcv_sql=" SELECT c.po_break_down_id, sum (c.fin_receive_qnty) as qnty,
					         a.receive_date,d.job_no_prefix_num as job_no,d.buyer_name,b.shipment_date,b.file_no,
					         b.grouping,d.style_ref_no,d.gmts_item_id,b.po_number,a.company_id,c.item_id
					    FROM gmt_finishing_receive_mst a, gmt_finishing_receive_dtls c,wo_po_break_down b,wo_po_details_master d
					   WHERE     a.id = c.mst_id
					         and b.id=c.po_break_down_id
					         and d.job_no=b.job_no_mst
					         and B.STATUS_ACTIVE=1
					         and b.is_deleted=0
					         and d.is_deleted=0
					         AND a.status_active = 1
					         AND a.is_deleted = 0
					         AND c.status_active = 1
					         AND c.is_deleted = 0
					        $w_company_cond
					        $company_cond
					        $buyer_cond
					        $txt_rcv_date
					        $location_cond
					        $floor_cond
					        $file_no_cond
					        $internal_ref_cond
							$style_ref_cond

					GROUP BY  c.po_break_down_id, a.receive_date,d.job_no_prefix_num,d.buyer_name,b.shipment_date,b.file_no,b.grouping,d.style_ref_no,d.gmts_item_id,b.po_number,a.company_id,c.item_id";
		//echo $fin_rcv_sql;
		$fin_rcv_res= sql_select( $fin_rcv_sql);
        $order_wise_fin_rec_qnty=array();
        foreach ($fin_rcv_res as $row)
        {
        	$order_wise_fin_rec_qnty[$row[csf('po_break_down_id')]][$row[csf('receive_date')]][$row[csf('item_id')]]['qnty']+=$row[csf('qnty')];
        	$order_wise_fin_rec_qnty[$row[csf('po_break_down_id')]][$row[csf('receive_date')]][$row[csf('item_id')]]['job_no']=$row[csf('job_no')];
        	$order_wise_fin_rec_qnty[$row[csf('po_break_down_id')]][$row[csf('receive_date')]][$row[csf('item_id')]]['buyer_name']=$row[csf('buyer_name')];
        	$order_wise_fin_rec_qnty[$row[csf('po_break_down_id')]][$row[csf('receive_date')]][$row[csf('item_id')]]['shipment_date']=$row[csf('shipment_date')];
        	$order_wise_fin_rec_qnty[$row[csf('po_break_down_id')]][$row[csf('receive_date')]][$row[csf('item_id')]]['file_no']=$row[csf('file_no')];
        	$order_wise_fin_rec_qnty[$row[csf('po_break_down_id')]][$row[csf('receive_date')]][$row[csf('item_id')]]['grouping']=$row[csf('grouping')];
        	$order_wise_fin_rec_qnty[$row[csf('po_break_down_id')]][$row[csf('receive_date')]][$row[csf('item_id')]]['style_ref_no']=$row[csf('style_ref_no')];
        	$order_wise_fin_rec_qnty[$row[csf('po_break_down_id')]][$row[csf('receive_date')]][$row[csf('item_id')]]['gmts_item_id']=$row[csf('gmts_item_id')];
        	$order_wise_fin_rec_qnty[$row[csf('po_break_down_id')]][$row[csf('receive_date')]][$row[csf('item_id')]]['po_number']=$row[csf('po_number')];
        	$order_wise_fin_rec_qnty[$row[csf('po_break_down_id')]][$row[csf('receive_date')]][$row[csf('item_id')]]['company_id']=$row[csf('company_id')];

        }

		// var_dump($sql_dtls_res);
        // echo $sql_dtls;  die;//$poIds_cond
		if( count($sql_dtls_res) > 0)
		{
			/*foreach($sql_dtls_res as $key=>$vals)
			{
				$all_production_po_arr[$vals[csf("po_break_down_id")]]=$vals[csf("po_break_down_id")];
			}
			$txt_date2=str_replace("production_date", "a.ex_factory_date", $txt_date);

			$all_production_poids=implode(",", $all_production_po_arr) ;
			if($exfactory_level==1) // gross level
			{

				$ex_fac_sql="SELECT c.buyer_id, a.po_break_down_id as po,a.ex_factory_date as dates,sum(a.ex_factory_qnty) as qnty from pro_ex_factory_delivery_mst c, pro_ex_factory_mst a where c.id=a.delivery_mst_id and a.status_active=1 and a.is_deleted=0  $txt_date2 and a.po_break_down_id in($all_production_poids) and c.status_active=1 and a.entry_form<>85   group by c.buyer_id,a.po_break_down_id,a.ex_factory_date";
			}
			else
			{

				$ex_fac_sql="SELECT c.buyer_id, a.po_break_down_id as po,a.ex_factory_date as dates,sum(b.production_qnty) as qnty from pro_ex_factory_delivery_mst c, pro_ex_factory_mst a,pro_ex_factory_dtls b where c.id=a.delivery_mst_id and a.id=b.mst_id and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_deleted=0  $txt_date2 and a.po_break_down_id in($all_production_poids) and c.status_active=1 and a.entry_form<>85   group by c.buyer_id,a.po_break_down_id,a.ex_factory_date";
			}
			// echo $ex_fac_sql;
			foreach(sql_select($ex_fac_sql) as $key=>$value)
			{
				$ex_fac_arr_po_datewise[$value[csf("po")]][$value[csf("dates")]]+=$value[csf("qnty")];
				$ex_fac_arr_buyerwise[$value[csf("buyer_id")]]+=$value[csf("qnty")];
				if($value[csf("qnty")]*1>0)
				{
					$ex_fac_all_po[$value[csf("po")]] =$value[csf("po")];
				}
			}


			if( count($ex_fac_all_po) != 0 )
			{
				$exfac_poids=implode(",", $ex_fac_all_po) ;
				$all_exfac_poids="";
				if($db_type==2 && count($ex_fac_all_po)>1000)
				{
					$all_exfac_poids=" and (";
					$exIdsArr=array_chunk(explode(",",$ex_fac_all_po),999);
					foreach($exIdsArr as $ids)
					{
						$ids=implode(",",$ids);
						$all_exfac_poids.=" b.id in ($ids) or ";
					}
					$all_exfac_poids=chop($all_exfac_poids,'or ');
					$all_exfac_poids.=")";
				}
				else
				{
					$all_exfac_poids=" and b.id in ($exfac_poids)";
				}
			}*/



			$poIds_cond3=str_replace("po_break_down_id","b.id", $poIds_cond);
			$po_wise_unit_price_sql="SELECT a.buyer_name,b.id,b.unit_price from wo_po_details_master a, wo_po_break_down b,GBL_TEMP_ENGINE tmp where a.id=b.job_id and b.id=tmp.ref_val and tmp.entry_form=41 and tmp.ref_from=1 and tmp.user_id = $user_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			foreach( sql_select($po_wise_unit_price_sql) as $key=>$val)
			{
				$po_wise_unit_price[$val[csf("id")]]=$val[csf("unit_price")];
				$buyer_wise_unit_price[$val[csf("buyer_name")]]+=$val[csf("unit_price")];
			}

		}
		/*==========================================================================================/
        /										shipment data										/
        /========================================================================================= */
        $txt_date2=str_replace("production_date", "e.ex_factory_date", $txt_date);
        if($exfactory_level==1) // gross level
		{
			$sql_ex="SELECT a.buyer_name,b.unit_price,d.delivery_location_id as location, d.company_id, d.delivery_company_id, d.delivery_floor_id as floor_id, e.po_break_down_id, e.ex_factory_date, e.item_number_id, sum(e.ex_factory_qnty) as ex_factory_qnty
        	from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c, pro_ex_factory_delivery_mst d ,pro_ex_factory_mst e
        	where d.id=e.delivery_mst_id and a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and b.id=e.po_break_down_id and e.status_active=1 and e.is_deleted=0 and  d.is_deleted=0 and d.status_active=1 $job_comp_cond $buyer_name $file_no_cond $internal_ref_cond $style_ref_cond $txt_date2
        	group by a.buyer_name,b.unit_price,d.delivery_location_id, d.company_id, d.delivery_company_id, d.delivery_floor_id, e.po_break_down_id, e.ex_factory_date, e.item_number_id
        	order by e.ex_factory_date ASC";
		}
		else // color or color and size level
		{
        	$sql_ex="SELECT a.buyer_name,b.unit_price, d.delivery_location_id as location, d.company_id, d.delivery_company_id, d.delivery_floor_id as floor_id, e.po_break_down_id, e.ex_factory_date, e.item_number_id, sum(f.production_qnty) as ex_factory_qnty
        	from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c, pro_ex_factory_delivery_mst d ,pro_ex_factory_mst e ,pro_ex_factory_dtls f
        	where d.id=e.delivery_mst_id and e.id=f.mst_id and a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and b.id=e.po_break_down_id and f.status_active in(1) and f.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and  d.is_deleted=0 and d.status_active=1 $job_comp_cond $buyer_name $file_no_cond $internal_ref_cond $style_ref_cond $txt_date2 and f.color_size_break_down_id=c.id
        	group by a.buyer_name,b.unit_price,d.delivery_location_id, d.company_id, d.delivery_company_id, d.delivery_floor_id, e.po_break_down_id, e.ex_factory_date, e.item_number_id
        	order by e.ex_factory_date ASC";
        }
        // echo $sql_ex;
        $ex_res=sql_select($sql_ex);

		/*==========================================================================================/
        /										carton qty											/
        /========================================================================================= */
		// GETTING CARTON QNTY
		$sql_carton="SELECT a.location,a.floor_id,a.sewing_line, a.po_break_down_id, a.production_date, a.item_number_id,a.serving_company,sum(a.carton_qty) as carton_qty
    	from pro_garments_production_mst a,GBL_TEMP_ENGINE tmp
    	where a.po_break_down_id=tmp.ref_val and tmp.entry_form=41 and tmp.ref_from=1 and tmp.user_id = $user_id and a.is_deleted=0 and a.status_active=1 $company_name_cond $working_factory_cond $txt_date $floor_name $location_cond $garmentsNature order by a.production_date ASC";
    	// echo $sql_carton;
    	$sql_carton_res = sql_select($sql_carton);
    	if($type==1)
		{
			foreach ($sql_carton_res as $row)
			{
				$prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("serving_company")]][$row[csf("production_date")]][$row[csf("item_number_id")]]['0']['0']['0']['crtQty']+=$row[csf("carton_qty")];
			}
		}
		else // type 2
		{
			foreach ($sql_carton_res as $row)
			{
				$prod_dlfl_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("location")]][$row[csf("floor_id")]]['0']['0']['0']['crtQty']=$row[csf("carton_qty")];
			}

		}
		// ================== for production data ======================
		$buyer_po_date_wise_gmts_data_arr = array();
		$check_fin_rcv = array();
        if($type==1)
        {
            foreach($sql_dtls_res as $row)
            {
                // var_dump($row);
                $buyer_name_dat=""; $job_no=''; $po_no=''; $buyer_id=''; $style_ref=''; $ref_no=''; $file_no='';
                $buyer_name_dat=$job_arr[$row[csf("po_break_down_id")]]['buyer'];
                $job_no=$job_arr[$row[csf("po_break_down_id")]]['job'];
                $po_no=$job_arr[$row[csf("po_break_down_id")]]['po'];
                $style_ref=$job_arr[$row[csf("po_break_down_id")]]['style'];
                $ref_no=$job_arr[$row[csf("po_break_down_id")]]['ref'];
                $file_no=$job_arr[$row[csf("po_break_down_id")]]['file'];

                //Buyer Wise Summary array start
                $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]]['0']['pQty']+=$row[csf("production_quantity")];
                $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("embel_name")]]['embQty']+=$row[csf("production_quantity")];
                $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("production_source")]]['sQty']+=$row[csf("production_quantity")];
                $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]]['0']['reQty']+=$row[csf("re_production_qty")];
                $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("production_source")]]['rejectQty']+=$row[csf("reject_qnty")];



             //     $arr_bind=$row[csf("po_break_down_id")]."***".$row[csf("production_date")]."***".[$row[csf("item_number_id")]];
             //    if(!in_array($arr_bind, $check_fin_rcv))
             //    {
	            //     $buyer_fullQty_arr[$buyer_name_dat][0]['0']['fin_rcv_qnty']+=$order_wise_fin_rec_qnty[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]]['qnty'];
	            //     array_push($check_fin_rcv, $arr_bind);
	            // }

                $buyer_po_date_wise_gmts_data_arr[$buyer_name_dat][$row[csf("po_break_down_id")]][$row[csf("production_type")]] += $row[csf("production_quantity")];

                //Details array start
                $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("production_type")]]['0']['pQty']+=$row[csf("production_quantity")];

                //for serving company
                $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("serving_company")]][$row[csf("production_date")][$row[csf("color_number_id")]]][$row[csf("item_number_id")]][$row[csf("production_type")]]['0']['pQty']+=$row[csf("production_quantity")];
                $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("serving_company")]][$row[csf("production_date")][$row[csf("color_number_id")]]][$row[csf("item_number_id")]][$row[csf("production_type")]][$row[csf("production_source")]]['sQty']+=$row[csf("production_quantity")];
                $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("serving_company")]][$row[csf("production_date")][$row[csf("color_number_id")]]][$row[csf("item_number_id")]][$row[csf("production_type")]]['0']['rejectQty']+=$row[csf("reject_qnty")];
                $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("serving_company")]][$row[csf("production_date")][$row[csf("color_number_id")]]][$row[csf("item_number_id")]][$row[csf("production_type")]]['0']['reQty']+=$row[csf("re_production_qty")];
                // $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("serving_company")]][$row[csf("production_date")][$row[csf("color_number_id")]]][$row[csf("item_number_id")]]['0']['0']['crtQty']+=$row[csf("carton_qty")];
                $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("serving_company")]][$row[csf("production_date")][$row[csf("color_number_id")]]][$row[csf("item_number_id")]][$row[csf("production_type")]][$row[csf("embel_name")]]['embQty']+=$row[csf("production_quantity")];


                $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("production_type")]][$row[csf("embel_name")]]['embQty']+=$row[csf("production_quantity")];
                $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("production_type")]][$row[csf("production_source")]]['sQty']+=$row[csf("production_quantity")];
                $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("production_type")]]['0']['reQty']+=$row[csf("re_production_qty")];
                $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("production_type")]]['0']['rejectQty']+=$row[csf("reject_qnty")];
                // $prod_date_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]]['0']['0']['crtQty']+=$row[csf("carton_qty")];
                if($row[csf("production_source")]==0) $row[csf("production_source")]=1;
				$all_data_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]].=$row[csf("item_number_id")].'**'.$buyer_name_dat.'**'.$job_no.'**'.$po_no.'**'.$style_ref.'**'.$ref_no.'**'.$file_no.'**'.$row[csf("company_id")].'**'.$row[csf("production_source")].'**'.$row[csf("serving_company")].'**'.$row[csf("color_number_id")].'__';//.'**'.$row[csf("floor_id")]
                // echo $row[csf("carton_qty")];
            }
        }
        else if($type==2)
        {
            foreach($sql_dtls_res as $row)
            {
				// var_dump($row);
                $buyer_name_dat=""; $job_no=''; $po_no=''; $buyer_id=''; $style_ref=''; $ref_no=''; $file_no='';
                $buyer_name_dat=$job_arr[$row[csf("po_break_down_id")]]['buyer'];
                $job_no=$job_arr[$row[csf("po_break_down_id")]]['job'];
                $po_no=$job_arr[$row[csf("po_break_down_id")]]['po'];
                $style_ref=$job_arr[$row[csf("po_break_down_id")]]['style'];
                $ref_no=$job_arr[$row[csf("po_break_down_id")]]['ref'];
                $file_no=$job_arr[$row[csf("po_break_down_id")]]['file'];
                //Buyer Wise Summary array start
                $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]]['0']['pQty']+=$row[csf("production_quantity")];
                $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("embel_name")]]['embQty']+=$row[csf("production_quantity")];
                $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("production_source")]]['sQty']+=$row[csf("production_quantity")];
                $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]]['0']['reQty']+=$row[csf("re_production_qty")];
                $buyer_fullQty_arr[$buyer_name_dat][$row[csf("production_type")]][$row[csf("production_source")]]['rejectQty']+=$row[csf("reject_qnty")];



             //    $arr_bind=$row[csf("po_break_down_id")]."***".$row[csf("production_date")]."***".$row[csf("item_number_id")];
             //    if(!in_array($arr_bind, $check_fin_rcv))
             //    {
	            //      $buyer_fullQty_arr[$buyer_name_dat][0]['0']['fin_rcv_qnty']+=$order_wise_fin_rec_qnty[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]]['qnty'];
	            //     array_push($check_fin_rcv, $arr_bind);
	            // }

                $buyer_po_date_wise_gmts_data_arr[$buyer_name_dat][$row[csf("po_break_down_id")]][$row[csf("production_type")]] += $row[csf("production_quantity")];

                //Details array start
                if($row[csf("sewing_line")]=="") $row[csf("sewing_line")]=0;

                if($row[csf("floor_id")]=="") $row[csf("floor_id")]=0;

                if($row[csf("location")]=="") $row[csf("location")]=0;

                $prod_dlfl_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]][$row[csf("production_type")]]['0']['pQty']+=$row[csf("production_quantity")];

                $prod_dlfl_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]][$row[csf("production_type")]][$row[csf("embel_name")]]['embQty']+=$row[csf("production_quantity")];

                $prod_dlfl_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]][$row[csf("production_type")]][$row[csf("production_source")]]['sQty']+=$row[csf("production_quantity")];

                $prod_dlfl_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]][$row[csf("production_type")]]['0']['reQty']+=$row[csf("re_production_qty")];

                $prod_dlfl_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]][$row[csf("production_type")]]['0']['rejectQty']+=$row[csf("reject_qnty")];
                // $prod_dlfl_qty_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]]['0']['0']['crtQty']+=$row[csf("carton_qty")];
                if($row[csf("production_source")]==0) $row[csf("production_source")]=1;
                if($row[csf("production_source")]==3)
                {
                	$all_data_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][0][0].= $row[csf("sewing_line")].'##'.$buyer_name_dat.'##'.$job_no.'##'.$po_no.'##'.$style_ref.'##'.$ref_no.'##'.$file_no.'##'.$row[csf("production_source")].'##0##'.$row[csf("serving_company")].'__';
                }
                else
                {
                	$all_data_arr[$row[csf("po_break_down_id")]][$row[csf("production_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("location")]][$row[csf("floor_id")]].= $row[csf("sewing_line")].'##'.$buyer_name_dat.'##'.$job_no.'##'.$po_no.'##'.$style_ref.'##'.$ref_no.'##'.$file_no.'##'.$row[csf("production_source")].'##'.$row[csf("prod_reso_allo")].'##'.$row[csf("serving_company")].'__';
                }
            }
        }
        // echo "<br>"; print_r($prod_date_qty_arr); die;
        unset($sql_dtls_res);
        // unset($job_arr);

        // ============================ for shipment data ========================
        $ex_fac_arr_buyerwise = array();
        $prod_date_qty_arr_ex = array();
        $prod_date_qty_arr_ex2 = array();
        $po_id_arr = array();
        if($type==1)
        {
            foreach($ex_res as $row)
            {

                $buyer_name_dat=""; $job_no=''; $po_no=''; $buyer_id=''; $style_ref=''; $ref_no=''; $file_no='';
                $buyer_name_dat=$job_arr[$row[csf("po_break_down_id")]]['buyer'];
                $job_no=$job_arr[$row[csf("po_break_down_id")]]['job'];
                $po_no=$job_arr[$row[csf("po_break_down_id")]]['po'];
                $style_ref=$job_arr[$row[csf("po_break_down_id")]]['style'];
                $ref_no=$job_arr[$row[csf("po_break_down_id")]]['ref'];
                $file_no=$job_arr[$row[csf("po_break_down_id")]]['file'];

                $ex_fac_arr_po_datewise[$row[csf("po_break_down_id")]][$row[csf("ex_factory_date")]]+=$row[csf("ex_factory_qnty")];
				$ex_fac_arr_buyerwise[$buyer_name_dat]+=$row[csf("ex_factory_qnty")];

				$buyer_po_date_wise_gmts_data_arr[$buyer_name_dat][$row[csf("po_break_down_id")]][0] += $row[csf("ex_factory_qnty")];

                //Buyer Wise Summary array start
                // $ex_fac_arr_buyerwise[$buyer_name_dat]+=$row[csf("ex_factory_qnty")];
                $buyer_fullQty_arr[$buyer_name_dat][0]['0']['ex_factory_qnty']+=$row[csf("ex_factory_qnty")];

             //    $arr_bind=$row[csf("po_break_down_id")]."***".$row[csf("ex_factory_date")]."***".$row[csf("item_number_id")];
             //    if(!in_array($arr_bind, $check_fin_rcv))
             //    {
	            //     $buyer_fullQty_arr[$buyer_name_dat][0]['0']['fin_rcv_qnty']+=$order_wise_fin_rec_qnty[$row[csf("po_break_down_id")]][$row[csf("ex_factory_date")]][$row[csf("item_number_id")]]['qnty'];
	            //     array_push($check_fin_rcv, $arr_bind);
	            // }
                //Details array start
                // $prod_date_qty_arr_ex[$row[csf("po_break_down_id")]][$row[csf("ex_factory_date")]][$row[csf("item_number_id")]]['ex_factory_qnty']+=$row[csf("ex_factory_qnty")];

                //for serving company
                $prod_date_qty_arr_ex[$row[csf("po_break_down_id")]][$row[csf("delivery_company_id")]][$row[csf("ex_factory_date")]][$row[csf("item_number_id")]]['ex_factory_qnty']+=$row[csf("ex_factory_qnty")];

				$all_data_arr[$row[csf("po_break_down_id")]][$row[csf("ex_factory_date")]].=$row[csf("item_number_id")].'**'.$buyer_name_dat.'**'.$job_no.'**'.$po_no.'**'.$style_ref.'**'.$ref_no.'**'.$file_no.'**'.$row[csf("company_id")].'**1**'.$row[csf("delivery_company_id")].'**'.$row[csf("color_number_id")].'__';//.'**'.$row[csf("floor_id")]
                array_push($po_id_arr, $row[csf("po_break_down_id")]);
                array_push($po_id_arr, $row[csf("po_break_down_id")]);
            }
        }
        else if($type==2)
        {
            foreach($ex_res as $row)
            {
                $buyer_name_dat=""; $job_no=''; $po_no=''; $buyer_id=''; $style_ref=''; $ref_no=''; $file_no='';
                $buyer_name_dat=$job_arr[$row[csf("po_break_down_id")]]['buyer'];
                $job_no=$job_arr[$row[csf("po_break_down_id")]]['job'];
                $po_no=$job_arr[$row[csf("po_break_down_id")]]['po'];
                $style_ref=$job_arr[$row[csf("po_break_down_id")]]['style'];
                $ref_no=$job_arr[$row[csf("po_break_down_id")]]['ref'];
                $file_no=$job_arr[$row[csf("po_break_down_id")]]['file'];

                $ex_fac_arr_po_datewise[$row[csf("po_break_down_id")]][$row[csf("ex_factory_date")]]+=$row[csf("ex_factory_qnty")];
				$ex_fac_arr_buyerwise[$buyer_name_dat]+=$row[csf("ex_factory_qnty")];

				$buyer_po_date_wise_gmts_data_arr[$buyer_name_dat][$row[csf("po_break_down_id")]][0] += $row[csf("ex_factory_qnty")];

                //Buyer Wise Summary array start
                // $ex_fac_arr_buyerwise[$buyer_name_dat]+=$row[csf("ex_factory_qnty")];
                // $buyer_fullQty_arr[$buyer_name_dat][0]['0']['ex_factory_qnty']+=$row[csf("ex_factory_qnty")];
                // $arr_bind=$row[csf("po_break_down_id")]."***".$row[csf("ex_factory_date")]."***".$row[csf("item_number_id")];
                // if(!in_array($arr_bind, $check_fin_rcv))
                // {
                // 	$buyer_fullQty_arr[$buyer_name_dat][0]['0']['fin_rcv_qnty']+=$order_wise_fin_rec_qnty[$row[csf("po_break_down_id")]][$row[csf("ex_factory_date")]][$row[csf("item_number_id")]]['qnty'];
                // 	array_push($check_fin_rcv, $arr_bind);
                // }

                //Details array start

                $prod_date_qty_arr_ex2[$row[csf("po_break_down_id")]][$row[csf("ex_factory_date")]][$row[csf("item_number_id")]][$row[csf("delivery_location_id")]][$row[csf("delivery_floor_id")]]['ex_factory_qnty']+=$row[csf("ex_factory_qnty")];

                $all_data_arr[$row[csf("po_break_down_id")]][$row[csf("ex_factory_date")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("delivery_location_id")]][$row[csf("delivery_floor_id")]].='0##'.$buyer_name_dat.'##'.$job_no.'##'.$po_no.'##'.$style_ref.'##'.$ref_no.'##'.$file_no.'##1##0##'.$row[csf("delivery_company_id")].'__';
                array_push($po_id_arr, $row[csf("po_break_down_id")]);
            }
        }

        // echo "<pre>";print_r($all_data_arr);die();
        unset($ex_res);
        unset($job_arr);
        $b=1; //date_wise Summary

        //$rcv_data_need_to_show=array();
        foreach ($order_wise_fin_rec_qnty as $po_id=> $po_wise_data)
        {

        	foreach ($po_wise_data as $receive_date => $rcv_date_wise_data)
        	{
        		foreach ($rcv_date_wise_data as $item_id => $item_data)
        		{
        			$buyer_fullQty_arr[$item_data['buyer_name']][0]['0']['fin_rcv_qnty']+=$item_data['qnty'];
        		}
        	}
        }

        foreach ($buyer_po_date_wise_gmts_data_arr as $b_key => $b_value)
        {
        	foreach ($b_value as $po_key => $po_value)
        	{
        		$sewing_output_total = $po_value[5];
        		$ex_fact = $po_value[0];

                $ex_fact_fob=$ex_fact*$po_wise_unit_price[$po_key];

                $ex_fact_bal= $sewing_output_total-$ex_fact;
                $ex_fact_bal_fob= $ex_fact_bal*$po_wise_unit_price[$po_key];
                $ex_fac_arr_buyerwise[$b_key] += $ex_fact;
                $ex_fac_fob_arr_buyerwise[$b_key]+=$ex_fact_fob;
                $ex_fac_bal_arr_buyerwise[$b_key]+=$ex_fact_bal;
                $ex_fac_bal_fob_arr_buyerwise[$b_key]+=$ex_fact_bal_fob;
        	}
        }

       /* foreach($all_data_arr as $po_id=>$po_data)
        {
            foreach($po_data as $prod_date=>$prod_date_data)
            {
                $ex_itemdata='';
                $ex_itemdata=array_filter(array_unique(explode('__',$prod_date_data)));
                foreach($ex_itemdata as $data_all)
                {
                    $buyer_name=''; $item_id='';
                    $ex_data=array_filter(explode('**',$data_all));
                    if($ex_data[1] !="")
                    {
                        $buyer_name=$ex_data[1];
                        $item_id=$ex_data[0];
                        $sewOut_inQty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['5']['1']['sQty'];
                        $sewOut_outQty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id]['5']['3']['sQty'];

                        // $sewing_output_total=$sewOut_inQty+$sewOut_outQty;

                        $sewing_output_total = $date_po_wise_gmts_data_arr[$po_id][$prod_date][5];

                        $ex_fact = 0;
                        $ex_fact=$ex_fac_arr_po_datewise[$po_id][$prod_date];
                        $ex_fact_fob=$ex_fact*$po_wise_unit_price[$po_id];
                        $ex_fact_bal= $sewing_output_total-$ex_fact;
                        echo "$sewing_output_total-$ex_fact<br>";
                        $ex_fact_bal_fob= $ex_fact_bal*$po_wise_unit_price[$po_id];
                        $ex_fac_arr_buyerwise[$buyer_name] += $ex_fact;
                        $ex_fac_fob_arr_buyerwise[$buyer_name]+=$ex_fact_fob;
                        $ex_fac_bal_arr_buyerwise[$buyer_name]+=$ex_fact_bal;
                        $ex_fac_bal_fob_arr_buyerwise[$buyer_name]+=$ex_fact_bal_fob;


                    }
                }
            }
        }*/
		// print_r($ex_fac_arr_buyerwise);

		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from=1 and ENTRY_FORM=41");
		oci_commit($con);
		disconnect($con);
		?>
		<?

			$html.='<div>
						<div>
							<table>
								<tr>
									<td>'. $report_name.'</td>
								</tr>
								<tr>
									<td>
										Company Name:

										'. $company_library[str_replace("'","",$cbo_company_name)].'

									</td>
								</tr>

							</table>
							<!-- ==========================================================================================/
							/										summary part										   /
							/=========================================================================================== -->
							<table>
								<tr>
									<td >
									<div>Production-Regular Order Summary</strong></div>
									<br/>
									<div >
										<table>
											<thead>
												<tr>
													<th >Sl.</th>
													<th>Buyer Name</th>
													<th>Cut Quantity</th>
													<th>Sent to Print</th>
													<th>Received from Print</th>
													<th>Sent to Embroidery </th>
													<th>Received from Embroidery</th>
													<th>Sent to Wash</th>
													<th>Rev Wash</th>
													<th>Sent to Sp. Works</th>
													<th>Rev Sp. Works</th>

													<th>Sewing Input</th>
													<th>Sew Input (Outbound)</th>
													<th>Sewing Output</th>
													<th>Sew Output (Outbound)</th>
													<th>Finishing Received</th>
													<th>Total Iron Production</th>
													<th>Total Re-Iron</th>
													<th>Total Poly (Inhouse)</th>
													<th>Total Poly (Outbond)</th>
													<th>Total Packing/ Finishing</th>
													<th>EXF Quantity</th>
													<th>EXF Value</th>
													<th>Ex-Fac. Bal. Qty</th>
													<th  >Ex- Fac. Bal. FOB Value</th>';
													$html .= '</tr> ;
											</thead>
										</table>
										<div>
											<table>';
											$tot_fin_rcv_qnty=0;
											$tot_buyer_fin_rcv_qnty=0;
											foreach($buyer_fullQty_arr as $buyer_id=>$buyer_data)
											{
												if($buyer_id!="")
												{

													$cutting_qty=$printing_qty=$printreceived_qty=$emb_qty=$embRec_qty=$wash_qty=$washRec_qty=$special_qty=$specialRec_qty=$sewIn_inQty=$sewIn_outQty=$sewOut_inQty=$sewOut_outQty=$iron_qty=$reIron_qty=$finish_qty=$polyIn_inQty=$polyOut_outQty=0;
													$cutting_qty=$buyer_data['1']['0']['pQty'];
													$printing_qty=$buyer_data['2']['1']['embQty'];
													$printreceived_qty=$buyer_data['3']['1']['embQty'];
													$emb_qty=$buyer_data['2']['2']['embQty'];
													$embRec_qty=$buyer_data['3']['2']['embQty'];
													$wash_qty=$buyer_data['2']['3']['embQty'];
													$washRec_qty=$buyer_data['3']['3']['embQty'];
													$special_qty=$buyer_data['2']['4']['embQty'];
													$specialRec_qty=$buyer_data['3']['4']['embQty'];
													$sewIn_inQty=$buyer_data['4']['0']['pQty'];
													$sewIn_outQty=$buyer_data['4']['3']['sQty'];
													$sewOut_inQty=$buyer_data['5']['0']['pQty'];
													$sewOut_outQty=$buyer_data['5']['3']['sQty'];
													$polyIn_inQty=$buyer_data['11']['1']['sQty'];
													$polyOut_outQty=$buyer_data['11']['3']['sQty'];
													$iron_qty=$buyer_data['7']['0']['pQty'];
													$reIron_qty=$buyer_data['7']['0']['reQty'];
													$finish_qty=$buyer_data['8']['0']['pQty'];
													// $ex_fact_buy=$ex_fac_arr_buyerwise[$buyer_id];
													$ex_fact_buy=$buyer_data[0]['0']['ex_factory_qnty'];
													$fin_rcv_qnty=$buyer_data[0]['0']['fin_rcv_qnty'];
													$ex_fact_buy_fob=$ex_fac_fob_arr_buyerwise[$buyer_id];
													$ex_fact_buy_bal=$ex_fac_bal_arr_buyerwise[$buyer_id];
													$ex_fact_buy_bal_fob=$ex_fac_bal_fob_arr_buyerwise[$buyer_id];

													$html.='<tr>
														<td>'.$b.'</td>
														<td>'.$buyer_short_library[$buyer_id].'</td>
														<td>'.($cutting_qty).'</td>
														<td>'.($printing_qty).'</td>
														<td>'.($printreceived_qty).'</td>
														<td>'.($emb_qty).'</td>
														<td>'.($embRec_qty).'</td>
														<td>'.($wash_qty).'</td>
														<td>'.($washRec_qty).'</td>
														<td>'.($special_qty).'</td>
														<td>'.($specialRec_qty).'</td>
														<td >'.($sewIn_inQty).'</td>
														<td>'.($sewIn_outQty).'</td>
														<td>'.($sewOut_inQty).'</td>
														<td>'.($sewOut_outQty).'</td>
														<td>'.($fin_rcv_qnty).'</td>
														<td >'.($iron_qty).'</td>
														<td>'.($reIron_qty).'</td>
														<td>'.($polyIn_inQty).'</td>
														<td>'.($polyOut_outQty).'</td>
														<td >'.($finish_qty).'</td>
														<td >'.number_format($ex_fact_buy).'</td>
														<td >'.number_format($ex_fact_buy_fob).'</td>
														<td >'.number_format($ex_fact_buy_bal).'</td>
														<td>'.number_format($ex_fact_buy_bal_fob).'</td>';
													$html.='</tr>';
													$sumCutting_qty+=$cutting_qty;
													$sumPrinting_qty+=$printing_qty;
													$sumPrintreceived_qty+=$printreceived_qty;
													$sumEmb_qty+=$emb_qty;
													$sumEmbRec_qty+=$embRec_qty;
													$sumWash_qty+=$wash_qty;
													$sumWashRec_qty+=$washRec_qty;
													$sumSpecial_qty+=$special_qty;
													$sumSpecialRec_qty+=$specialRec_qty;
													$sumSewIn_inQty+=$sewIn_inQty;
													$sumSewIn_outQty+=$sewIn_outQty;
													$sumSewOut_inQty+=$sewOut_inQty;
													$sumSewOut_outQty+=$sewOut_outQty;
													$sumIron_qty+=$iron_qty;
													$sumReIron_qty+=$reIron_qty;
													$sumFinish_qty+=$finish_qty;
													$sumPolyIn_inQty+=$polyIn_inQty;
													$sumPolyOut_outQty+=$polyOut_outQty;
													$sum_exfact+=$ex_fact_buy;
													$sum_exfact_fob+=$ex_fact_buy_fob;
													$sum_exfact_bal+=$ex_fact_buy_bal;
													$sum_exfact_bal_fob+=$ex_fact_buy_bal_fob ;
													$tot_fin_rcv_qnty+=$fin_rcv_qnty;
													$tot_buyer_fin_rcv_qnty+=$fin_rcv_qnty;
													$b++;
												}
											}

											$html.=' </table>
										</div>
											<table >
												<tr>
													<td ></td>
													<td   >Total</td>
													<td  >'.($sumCutting_qty).'</td>
													<td  >'.($sumPrinting_qty).'</td>
													<td  >'.($sumPrintreceived_qty).'</td>
													<td  >'.($sumEmb_qty).'</td>
													<td  >'.($sumEmbRec_qty).'</td>
													<td  >'.($sumWash_qty).'</td>
													<td  >'.($sumWashRec_qty).'</td>
													<td  >'.($sumSpecial_qty).'</td>
													<td  >'.($sumSpecialRec_qty).'</td>
													<td  >'.($sumSewIn_inQty).'</td>
													<td  >'.($sumSewIn_outQty).'</td>
													<td  >'.($sumSewOut_inQty).'</td>
													<td  >'.($sumSewOut_outQty).'</td>
													<td  >'.($tot_buyer_fin_rcv_qnty).'</td>
													<td  >'.($sumIron_qty).'</td>
													<td   >'.($sumReIron_qty).'</td>
													<td   >'.($sumPolyIn_inQty).'</td>
													<td   >'.($sumPolyOut_outQty).'</td>
													<td  >'.($sumFinish_qty).'</td>
													<td  >'.number_format($sum_exfact).'</td>
													<td  >'.number_format($sum_exfact_fob).'</td>
													<td  >'.number_format($sum_exfact_bal).'</td>
													<td >'.number_format($sum_exfact_bal_fob).'</td>
												</tr>
											</table>
									</div>';
									$html.='</td>';

								if(str_replace("'","",trim($cbo_subcon))==2)
								{

									$html.='</div>
								<div >
									<table  >
										<thead>
											<tr>
												<th >Sl.</th>
												<th >Buyer</th>
												<th >Total Cut Qty</th>
												<th >Total Sew Input</th>
												<th >Total Sew Qty</th>
												<th >Total Iron Qty</th>
												<th>Total Gmt. Fin. Qty</th>
											</tr>
										</thead>
									</table>
									<div >
									<table cellspacing="0" border="1" class="rpt_table" >';

									if(str_replace("'","",$cbo_floor)==0) $sub_floor_name="";else $sub_floor_name=" and b.floor_id in($cbo_floor)";
									if ($location==0) $sub_location_cond=""; else $sub_location_cond=" and b.location_id in($location) ";

									$total_po_quantity=0;$total_po_value=0;$total_cut_subcon=0;$total_sew_out_subcon=0;$total_ex_factory=0;
									$i=1;
									if(str_replace("'","",$cbo_company_name)==0) $company_name_sub=""; else $company_name_sub="and a.company_id = $cbo_company_name";
									if($db_type==0)
									{
										$ex_factory_sql="select a.party_id, sum(c.order_quantity) as order_quantity
										from subcon_ord_mst a, subcon_gmts_prod_dtls b, subcon_ord_dtls c
										where a.subcon_job=c.job_no_mst and c.id=b.order_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and b.production_date between $txt_date_from and $txt_date_to $company_name_sub $subcon_work_comp $sub_floor_name $sub_location_cond group by a.party_id";
									}
									else
									{
										$ex_factory_sql="select a.party_id, sum(c.order_quantity) as order_quantity
										from subcon_ord_mst a, subcon_gmts_prod_dtls b, subcon_ord_dtls c
										where a.subcon_job=c.job_no_mst and c.id=b.order_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and b.production_date between $txt_date_from and $txt_date_to $company_name_sub  $subcon_work_comp $sub_floor_name $sub_location_cond group by a.party_id";
									}
									//echo  $exfactory_sql;
									$ex_factory_sql_result=sql_select($ex_factory_sql);
									$ex_factory_arr=array();
									foreach($ex_factory_sql_result as $resRow)
									{
										$ex_factory_arr[$resRow[csf("party_id")]] = $resRow[csf("order_quantity")];
									}

									$sub_cut_sew_array=array();

									if($db_type==0)
									{
										$production_mst_sql= sql_select("SELECT  a.party_id,
										sum(CASE WHEN production_type ='1' THEN production_qnty ELSE 0 END) AS cutting_qnty,
										sum(CASE WHEN production_type ='7' THEN production_qnty ELSE 0 END) AS sewing_input_qnty,
										sum(CASE WHEN production_type ='2' THEN production_qnty ELSE 0 END) AS sewingout_qnty,
										sum(CASE WHEN production_type ='3' THEN production_qnty  ELSE 0 END) AS ironout_qnty,
										sum(CASE WHEN production_type ='4' THEN production_qnty  ELSE 0 END) AS gmts_fin_qnty
										from subcon_ord_mst a, subcon_gmts_prod_dtls b, subcon_ord_dtls c

										where c.job_no_mst=a.subcon_job and c.id=b.order_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and  c.is_deleted=0 and c.status_active=1 and b.production_date between $txt_date_from and $txt_date_to $company_name_sub  $subcon_work_comp $sub_location_cond $sub_floor_name group by a.party_id");
									}
									else
									{
										$production_mst_sql=sql_select("SELECT  a.party_id,
										sum(CASE WHEN production_type ='1' THEN production_qnty ELSE 0 END) AS cutting_qnty,
										sum(CASE WHEN production_type ='7' THEN production_qnty ELSE 0 END) AS sewing_input_qnty,

										sum(CASE WHEN production_type ='2' THEN production_qnty ELSE 0 END) AS sewingout_qnty,
										sum(CASE WHEN production_type ='3' THEN production_qnty  ELSE 0 END) AS ironout_qnty,
										sum(CASE WHEN production_type ='4' THEN production_qnty  ELSE 0 END) AS gmts_fin_qnty
										from subcon_ord_mst a, subcon_gmts_prod_dtls b, subcon_ord_dtls c
										where c.job_no_mst=a.subcon_job and c.id=b.order_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.production_date between $txt_date_from and $txt_date_to $company_name_sub $subcon_work_comp $sub_location_cond $sub_floor_name group by a.party_id");

									}
									foreach($production_mst_sql as $sql_result)
									{
										$sub_cut_sew_array[$sql_result[csf("party_id")]]['1']=$sql_result[csf("cutting_qnty")];
										$sub_cut_sew_array[$sql_result[csf("party_id")]]['7']=$sql_result[csf("sewing_input_qnty")];
										$sub_cut_sew_array[$sql_result[csf("party_id")]]['2']=$sql_result[csf("sewingout_qnty")];
										$sub_cut_sew_array[$sql_result[csf("party_id")]]['3']=$sql_result[csf("ironout_qnty")];
										$sub_cut_sew_array[$sql_result[csf("party_id")]]['4']=$sql_result[csf("gmts_fin_qnty")];
									}

									if($db_type==0)
									{
										$production_date_sql="SELECT a.party_id, sum(c.order_quantity) as po_quantity, sum(c.amount) as po_total_price
										from subcon_ord_mst a, subcon_gmts_prod_dtls b, subcon_ord_dtls c
										where c.job_no_mst=a.subcon_job and c.id=b.order_id and b.production_type in (1,2,7) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.production_date between $txt_date_from and $txt_date_to $company_name_sub $subcon_work_comp $sub_location_cond $sub_floor_name  group by a.party_id order by a.party_id ASC";
									}
									else
									{
										$production_date_sql="SELECT a.party_id, sum(c.order_quantity) as po_quantity, sum(c.amount) as po_total_price
										from subcon_ord_mst a, subcon_gmts_prod_dtls b, subcon_ord_dtls c
										where c.job_no_mst=a.subcon_job and c.id=b.order_id and b.production_type in (1,2,7) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.production_date between $txt_date_from and $txt_date_to $company_name_sub $subcon_work_comp $sub_location_cond $sub_floor_name group by a.party_id order by a.party_id ASC";
									}
									//echo $production_date_sql;//die;
									$pro_sql_result=sql_select($production_date_sql);
									foreach($pro_sql_result as $pro_date_sql_row)
									{



										$html.='<tr>
											<td >'.$i.'</td>
											<td >'.$buyer_short_library[$pro_date_sql_row[csf("party_id")]].'</td>
											<td >'.number_format($sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['1']).'</td>
											<td >'.number_format($sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['7']).'</td>
											<td >'.number_format($sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['2']).'</td>
											<td >'.number_format($sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['3']).'</td>
											<td>'.number_format($sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['4']).'</td>
										</tr>';

										$total_cut_subcon+=$sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['1'];
										$total_input_subcon+=$sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['7'];
										$total_sew_out_subcon+=$sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['2'];
										$total_iron_subcon+=$sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['3'];
										$total_gmts_fin_subcon+=$sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['4'];
										$i++;
									}

									$html.='</table>';
									$html.='<table >
										<tr>
											<td ></td>
											<td >Total</td>
											<td >'.number_format($total_cut_subcon).'</td>
											<td>'.number_format($total_input_subcon).'</td>
											<td  >'.number_format($total_sew_out_subcon).'</td>
											<td  >'.number_format($total_iron_subcon).'></td>
											<td>'.number_format($total_gmts_fin_sucon).'</td>
										</tr>
									</table>
									<br />
										<div>Total Cutting:'.number_format($all_production_cutt=$sumCutting_qty+$total_cut_subcon,0).' (Pcs) & Total Sewing:'.number_format($all_production_sewing=$sumSewOut_inQty+$total_sew_out_subcon,0).' (Pcs)</strong></div><br />
										<div>Total Iron:'.number_format($all_production_iron=$sumIron_qty+$total_iron_subcon,0).'(Pcs) & Total Gmts. Fin.:'.number_format($all_production_gmts_fin=$sumFinish_qty+$total_gmts_fin_subcon,0).' (Pcs)</strong></div>
										</div>
									</div>
								</td>';

								}

							$html.='</tr>
							<tr>
								<td colspan="2"></td>
							</tr>
							</table>

						</div>
						<div></div>';

						if($type==1) // Date Wise
						{


						$html.=' <h5 >Production-Regular Order</h5>
							<table>
								<thead>
								<tr>
									<th >Sl.</th>
									<th >Working Factory</th>
									<th >Job No</th>
									<th >Order Number</th>
									<th >Ship Date</th>
									<th >Buyer Name</th>
									<th >Style Name</th>
									<th >File No</th>
									<th >Internal Ref</th>
									<th >Item Name</th>
									<th >Gtms Color</th>
									<th >Production Date</th>
									<th >Cutting</th>
									<th >Sent to prnt</th>
									<th >Rev prn/Emb</th>

									<th >Sent to Emb</th>
									<th >Rev Emb</th>
									<th >Sent to Wash</th>
									<th >Rev Wash</th>
									<th >Sent to Sp. Works</th>
									<th >Rev Sp. Works</th>

									<th >Sewing In (Inhouse)</th>
									<th >Sewing In (Out-bound)</th>
									<th >Total Sewing Input</th>
									<th >Sewing Out (Inhouse)</th>
									<th >Sewing Out (Out-bound)</th>
									<th >Total Sewing Out</th>
									<th >Finishing<br>Rcv</th>

									<th >Iron Qty (Inhouse)</th>
									<th >Iron Qty (Out-bound)</th>
									<th >Total Iron Qty</th>

									<th >Re-Iron Qty </th>
									<th >Poly Qty (Inhouse)</th>
									<th >Poly Qty (Out-bound)</th>
									<th >Total Poly Qty</th>
									<th >Finish Qty (Inhouse)</th>
									<th >Finish Qty (Out-bound)</th>
									<th >Total Finish Qty</th>

									<th >Today Carton</th>
									<th >Prod/Dzn</th>

									<th >Reject Qty</th>
									<th >Cutting Reject</th>
									<th >Print,Emb,Wash Reject</th>
									<th >Sewing Reject</th>
									<th >Iron Reject</th>
									<th >Packing & Finishing Reject</th>

									<th >Ex-Fac. Qty</th>
									<th >Ex-Fac. FOB Value</th>
									<th >Ex-Fac Bal. Qty</th>
									<th >Ex- Fac. Bal. FOB Value</th>
									<th>Remarks</th>
									</tr>
								</thead>
							</table>
							<div>
								<table >  ';

									$i=1;
									$fin_rcv_total=0;
									$check_fin_rcv=array();
									foreach($all_data_arr as $po_id=>$po_data)
									{
										foreach($po_data as $prod_date=>$prod_date_data)
										{
											$ex_itemdata='';
											$ex_itemdata=array_filter(array_unique(explode('__',$prod_date_data)));
											foreach($ex_itemdata as $data_all)
											{
												$item_id=''; $buyer_name=''; $job_no=''; $po_no=''; $style_ref=''; $ref_no=''; $file_no=''; $company_id='';
													$ex_data=array_filter(explode('**',$data_all));


												if($ex_data[1] !="")
												{
													$item_id=$ex_data[0];
													$buyer_name=$ex_data[1];
													$job_no=$ex_data[2];
													$po_no=$ex_data[3];
													$style_ref=$ex_data[4];
													$ref_no=$ex_data[5];
													$file_no=$ex_data[6];
													$company_id=$ex_data[7];

													$serving_comp_id=$ex_data[9];
													$serving_company='';
													if($ex_data[8]==1)
													{
														$serving_company=$company_short_library[$serving_comp_id];
													}
													else if($ex_data[8]==3)
													{
														$serving_company=$supplier_arr[$serving_comp_id];
													}
													$color_id=$ex_data[10];


													$cutting_qty=$print_qty=$printRec_qty=$emb_qty=$embRec_qty=$wash_qty=$washRec_qty=$special_qty=$specialRec_qty=$sewIn_inQty=$sewIn_outQty=$sewing_input_total=$sewOut_inQty=$sewOut_outQty=$sewing_output_total=$ironIn_qty=$ironOut_qty=$iron_qty_total=$reIron_qty=$finishIn_qty=$finishOut_qty=$finishing_qty=$carton_qty=$rejFinish_qty=$rejSewing_qty=$reject_Qty=$polyIn_Qty=$polyOut_Qty=0;


													$cutting_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['1']['0']['pQty'];
													$print_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['2']['1']['embQty'];
													$printRec_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['3']['1']['embQty'];
													$emb_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['2']['2']['embQty'];
													$embRec_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['3']['2']['embQty'];
													$wash_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['2']['3']['embQty'];
													$washRec_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['3']['3']['embQty'];
													$special_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['2']['4']['embQty'];
													$specialRec_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['3']['4']['embQty'];
													$sewIn_inQty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['4']['1']['sQty'];
													$sewIn_outQty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['4']['3']['sQty'];
													$sewOut_inQty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['5']['1']['sQty'];
													$sewOut_outQty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['5']['3']['sQty'];
													$ironIn_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['7']['1']['sQty'];
													$ironOut_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['7']['3']['sQty'];
													$reIron_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['7']['0']['reQty'];
													$finishIn_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['8']['1']['sQty'];
													$finishOut_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['8']['3']['sQty'];
													$carton_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['0']['0']['crtQty'];
													$rejFinish_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['8']['0']['rejectQty'];
													$rejSewing_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['5']['0']['rejectQty'];

													$polyIn_Qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['11']['1']['sQty'];
													$polyOut_Qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['11']['3']['sQty'];

													$rejIron_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['7']['0']['rejectQty'];
													$rejPrint_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['3']['0']['rejectQty'];
													$rejCutting_qty=$prod_date_qty_arr[$po_id][$ex_data[9]][$prod_date][$item_id][$color_id]['1']['0']['rejectQty'];

													$ex_fact = $prod_date_qty_arr_ex[$po_id][$ex_data[9]][$prod_date][$item_id]['ex_factory_qnty'];
													$sewing_input_total=$sewIn_inQty+$sewIn_outQty;
													$sewing_output_total=$sewOut_inQty+$sewOut_outQty;
													$fin_rcv_qnty=$order_wise_fin_rec_qnty[$po_id][$prod_date][$item_id]['qnty'];
													$company_id=$order_wise_fin_rec_qnty[$po_id][$prod_date][$item_id]['company_id'];
													$fin_rcv_total+=$fin_rcv_qnty;
													$arr_bind=$po_id."***".$prod_date."***".$item_id;
													array_push($check_fin_rcv, $arr_bind);
													$iron_qty_total=$ironIn_qty+$ironOut_qty;
													$poly_qty_total=$polyIn_Qty+$polyOut_Qty;
													$finishing_qty=$finishIn_qty+$finishOut_qty;
													if($sewing_output_total!=0)
														{
															$prod_dzn=($sewing_output_total)/12;
														}
														$prod_dzn=0;

														// $rej_title='Fin '.$rejFinish_qty.', Sew '.$rejSewing_qty.', Iron '.$rejIron_qty.', Print '.$rejPrint_qty.', Cut '.$rejCutting_qty;
														$reject_Qty=$rejFinish_qty+$rejSewing_qty+$rejIron_qty+$rejPrint_qty+$rejCutting_qty;
														$ex_fact_fob=$ex_fact*$po_wise_unit_price[$po_id];
														$ex_fact_bal= $sewing_output_total-$ex_fact;
														$ex_fact_bal_fob= $ex_fact_bal*$po_wise_unit_price[$po_id];



													$html.='<tr>
														<td>'.$i.'</td>
														<td >'.$serving_company.'</td>
														<td >'.$job_no.'</td>
														<td >'.$po_no.'</td>
														<td >'.change_date_format($job_arr2[$po_id]['pub_shipment_date']).'</td>
														<td >'.$buyer_short_library[$buyer_name].'</td>
														<td >'.$style_ref.'</p></td>
														<td >'.$file_no.'</td>
														<td >'.$ref_no.'</p></td>
														<td >'.$garments_item[$item_id].'</td>
														<td >'.$color_library_arr[$color_id].'</td>
														<td >'.change_date_format($prod_date).'</td>
														<td >'.$cutting_qty.'</td>
														<td >'.$print_qty.'</td>
														<td >'.$printRec_qty.'</td>
														<td  >'.$emb_qty.'</td>
														<td >'.$embRec_qty.'</td>
														<td >'.$wash_qty.'</td>
														<td >'.$washRec_qty.'</td>
														<td >'.$special_qty.'</td>
														<td >'.$specialRec_qty.'</td>
														<td >'.$sewIn_inQty.'</td>
														<td  >'.$sewIn_outQty.'</td>
														<td >'.$sewing_input_total.'</td>
														<td >'.$sewOut_inQty.'</td>
														<td >'.$sewOut_outQty.'</td>
														<td >'.$sewing_output_total.'</td>
														<td >'.fn_number_format($fin_rcv_qnty,2).'</td>
														<td >'.$ironIn_qty.'</td>
														<td ><'.$ironOut_qty.'</td>
														<td >'.$iron_qty_total.'</td>
														<td >'.$reIron_qty.'</td>
														<td >'.$polyIn_Qty.'</td>
														<td >'.$polyOut_Qty.'</td>
														<td >'.$poly_qty_total.'</td>
														<td >'.$finishIn_qty.'</td>
														<td >'.$finishOut_qty.'</td>
														<td >' .$finishing_qty.'</td>
														<td >'.$carton_qty.'</td>
														<td >'.number_format($prod_dzn,2).'</td
														<td >'.$reject_Qty.'</td>
														<td >'.$rejCutting_qty.'</td>
														<td >'.$rejPrint_qty.'</td>
														<td >'.$rejSewing_qty.'</td>
														<td >'.$rejIron_qty.'</td>
														<td >'.$rejFinish_qty.'</td>
														<td >'.$ex_fact.'</td>
														<td >'. $ex_fact_fob.'</td>
														<td >'.$ex_fact_bal.'</td>
														<td >'.$ex_fact_bal_fob.'</td>
														<td ""></td>';
														$html.='</tr>';
													$tot_cutting_qty+=$cutting_qty;
													$tot_print_qty+=$print_qty;
													$tot_printRec_qty+=$printRec_qty;
													$tot_emb_qty+=$emb_qty;
													$tot_embRec_qty+=$embRec_qty;
													$tot_wash_qty+=$wash_qty;
													$tot_washRec_qty+=$washRec_qty;
													$tot_special_qty+=$special_qty;
													$tot_specialRec_qty+=$specialRec_qty;
													$tot_sewIn_inQty+=$sewIn_inQty;
													$tot_sewIn_outQty+=$sewIn_outQty;
													$tot_sewing_input+=$sewing_input_total;
													$tot_sewOut_inQty+=$sewOut_inQty;
													$tot_sewOut_outQty+=$sewOut_outQty;
													$tot_sewing_output+=$sewing_output_total;
													$tot_ironIn_qty+=$ironIn_qty;
													$tot_ironOut_qty+=$ironOut_qty;
													$tot_iron_qty+=$iron_qty_total;
													$tot_reIron_qty+=$reIron_qty;
													$tot_polyIn_Qty+=$polyIn_Qty;
													$tot_polyOut_Qty+=$polyOut_Qty;
													$tot_poly_qty_sum+=$poly_qty_total;
													$tot_finishIn_qty+=$finishIn_qty;
													$tot_finishOut_qty+=$finishOut_qty;
													$tot_finishing_qty+=$finishing_qty;
													$tot_carton_qty+=$carton_qty;
													$total_prod_dzn+=$prod_dzn;

													$tot_reject_Qty+=$reject_Qty;
													$tot_rejCutting_qty+=$rejCutting_qty;
													$tot_rejPrint_qty+=$rejPrint_qty;
													$tot_rejSewing_qty+=$rejSewing_qty;
													$tot_rejIron_qty+=$rejIron_qty;
													$tot_rejFinish_qty+=$rejFinish_qty;

													$tot_ex_fac+=$ex_fact;
													$tot_ex_fac_fob+=$ex_fact_fob;
													$tot_fac_bal+=$ex_fact_bal;
													$tot_fac_bal_fob+=$ex_fact_bal_fob;
													$i++;
												}
											}
										}
									}


									$rcv_data_need_to_show=array();
									foreach ($order_wise_fin_rec_qnty as $po_id=> $po_wise_data)
									{

										foreach ($po_wise_data as $receive_date => $rcv_date_wise_data)
										{
											foreach ($rcv_date_wise_data as $item_id => $item_data)
											{

												$arr_bind=$po_id."***".$receive_date."***".$item_id;
												if(!in_array($arr_bind, $check_fin_rcv))
												{
													$buyer_fullQty_arr[$item_data['buyer_name']][0]['0']['fin_rcv_qnty']+=$item_data['qnty'];
													$rcv_data_need_to_show[$po_id][$receive_date][$item_id]['qnty']=$item_data['qnty'];
													$rcv_data_need_to_show[$po_id][$receive_date][$item_id]['job_no']=$item_data['job_no'];
													$rcv_data_need_to_show[$po_id][$receive_date][$item_id]['po_number']=$item_data['po_number'];
													$rcv_data_need_to_show[$po_id][$receive_date][$item_id]['buyer_name']=$item_data['buyer_name'];
													$rcv_data_need_to_show[$po_id][$receive_date][$item_id]['shipment_date']=$item_data['shipment_date'];
													$rcv_data_need_to_show[$po_id][$receive_date][$item_id]['file_no']=$item_data['file_no'];
													$rcv_data_need_to_show[$po_id][$receive_date][$item_id]['grouping']=$item_data['grouping'];
													$rcv_data_need_to_show[$po_id][$receive_date][$item_id]['style_ref_no']=$item_data['style_ref_no'];
													$rcv_data_need_to_show[$po_id][$receive_date][$item_id]['gmts_item_id']=$item_data['gmts_item_id'];
													$rcv_data_need_to_show[$po_id][$receive_date][$item_id]['item_id']=$item_data['item_id'];
													$rcv_data_need_to_show[$po_id][$receive_date][$item_id]['company_id']=$item_data['company_id'];
													array_push($check_fin_rcv, $arr_bind);
												}
											}
										}
									}

									foreach ($rcv_data_need_to_show as $po_id => $po_d)
									{
										foreach ($po_d as $receive_date => $date_data)
										{
											foreach ($date_data as $item_id => $rcv_data)
											{
														$fin_rcv_qnty=$rcv_data['qnty'];
														$company_id=$rcv_data['company_id'];
														$fin_rcv_total+=$fin_rcv_qnty;

													$html.='<tr>
														<td >'.$i.'</td>
														<td  >'.$company_short_library[$rcv_data['company_id']].'</td>
														<td  >'.$rcv_data['job_no'].'</p></td>
														<td  >'.$rcv_data['po_number'].'</td>
														<td  >'.change_date_format($rcv_data['shipment_date']).'</td>
														<td  >'.$buyer_short_library[$rcv_data['buyer_name']].'</td>
														<td  >'.$rcv_data['style_ref_no'].'</td>
														<td  >'.$rcv_data['file_no'].'</td>
														<td  >'.$rcv_data['grouping'].'</td>
														<td  > '.$garments_item[$item_id].'</td>
														<td  > '.change_date_format($receive_date).'</td>

														<td ></td>

														<td></td>
														<td ></td>
														<td ></td>
														<td ></td>
														<td ></td>
														<td ></td>
														<td ></td>
														<td > </td>
														<td ></td>
														<td ></td>
														<td ></td>
														<td ></td>
														<td ></td>
														<td ></td>
														<td >'.fn_number_format($fin_rcv_qnty,2 ).'</td>
														<td ></td>
														<td ></td>
														<td ></td>
														<td ></td>
														<td ></td>
														<td ></td>
														<td ></td>
														<td ></td>
														<td ></td>
														<td ></td>
														<td ></td>

														<td ></td>

														<td  ></td>

														<td ></td>
														<td ></td>
														<td ></td>
														<td ></td>
														<td ""></td
													</tr>';

												$i++;
											}
										}
									}

									$html.='</table>';
									$html.='<table>
										<tr>
										<td ></td>
										<td ></td>
										<td ></td>
										<td ></td>
										<td ></td>
										<td ></td>
										<td ></td>
										<td ></td>
										<td ></td>
										<td ></td>
										<td ></td>
										<td >Total</td>
										<td >'.$tot_cutting_qty.'</td>
										<td >'.$tot_print_qty.'</td>
										<td >'.$tot_printRec_qty.'</td>
										<td >'.$tot_emb_qty.'</td>
										<td >'.$tot_embRec_qty.'</td>
										<td >'.$tot_wash_qty.'</td>
										<td  >'.$tot_washRec_qty.'</td>
										<td  >'.$tot_special_qty.'</td>
										<td  >'.$tot_specialRec_qty.'</td>
										<td >'.$tot_sewIn_inQty.'</td>
										<td >'.$tot_sewIn_outQty.'</td>
										<td  >'.$tot_sewing_input.'</td>
										<td >'.$tot_sewOut_inQty.'</td>
										<td>'.$tot_sewOut_outQty.'</td>
										<td >'.$tot_sewing_output.'</td>
										<td  >'.$fin_rcv_total.'</td>
										<td  >'.$tot_ironIn_qty.'</td>
										<td  >'. $tot_ironOut_qty.'</td>
										<td  >'. $tot_iron_qty.'</td>
										<td>'.$tot_reIron_qty.'</td>
										<td  >'.$tot_polyIn_Qty.'</td>
										<td  ">'.$tot_polyOut_Qty.'</td>
										<td  ">'.$tot_poly_qty_sum.'</td>
										<td >'.$tot_finishIn_qty.'</td>
										<td >'.$tot_finishOut_qty.'</td>
										<td  >'.$tot_finishing_qty.'</td>
										<td >'.$tot_carton_qty.'</td>
										<td >'.number_format($total_prod_dzn,2).'</td>

										<td  >'.number_format($tot_reject_Qty,2).'</td >
										<td >'.number_format($tot_rejCutting_qty,2).'</td >
										<td  >'.number_format($tot_rejPrint_qty,2).'</td >
										<td  >'.number_format($tot_rejSewing_qty,2).'</td >
										<td >'.number_format($tot_rejIron_qty,2).'</td >
										<td >'.number_format($tot_rejFinish_qty,2).'</td >

										<td>'.number_format($tot_ex_fac,2).'</td >
										<td  >'.number_format($tot_ex_fac_fob,2).'</td >
										<td >'.number_format($tot_fac_bal,2).'</td >
										<td >'.number_format($tot_fac_bal_fob,2).'</td >
										<td></td>
								</tr>
								</table>';

								$html.='</div>';
						}
						else if($type==2)// Date Location Floor & Line
					{

						$html.='<h5 >Production-Regular Order</h5>';
						$html.='<table>;
						<thead>
								<tr>
										<th >Sl.</th>
										<th >Working Factory</th>
										<th >Job No</th>
										<th >Order Number</th>
										<th >Buyer Name</th>
										<th >Style Name</th>
										<th >File No</th>
										<th >Internal Ref</th>
										<th >Item Name</th>
										<th  >Gtms Color</th>
										<th >Production Date</th>
										<th >Status</th>
										<th >Location</th>
										<th >Floor</th>
										<th >Sewing Line No</th>
										<th >Cutting</th>
										<th >Sent to prnt</th>
										<th >Rev prn/Emb</th>

										<th >Sent to Emb</th>
										<th >Rev Emb</th>
										<th >Sent to Wash</th>
										<th >Rev Wash</th>
										<th >Sent to Sp. Works</th>
										<th >Rev Sp. Works</th>

										<th >Sewing In (Inhouse)</th>
										<th >Sewing In (Out-bound)</th>
										<th >Total Sewing Input</th>
										<th >Sewing Out (Inhouse)</th>
										<th >Sewing Out (Out-bound)</th>
										<th >Total Sewing Out</th>
										<th >Iron Qty (Inhouse)</th>
										<th >Iron Qty (Out-bound)</th>
										<th >Iron Qty</th>
										<th >Re-Iron Qty </th>
										<th >Poly Qty (Inhouse)</th>
										<th >Poly Qty (Out-bound)</th>
										<th >Total Poly Qty</th>
										<th >Finish Qty (Inhouse)</th>
										<th >Finish Qty (Out-bound)</th>
										<th >Total Finish Qty</th>
										<th >Today Carton</th>
										<th >Prod/Dzn</th>
										<th  >Reject Qty</th>
										<th  >Cutting Reject</th>
										<th  >Print,Emb,Wash Reject</th>
										<th >Sewing Reject</th>
										<th  >Iron Reject</th>
										<th  >Packing & Finishing Reject</th>
										<th "">Remarks</th>
								</tr>
							</thead>';
							$html.='</table>';
							$html.='<div >;';
							$html.='<table>';

									$i=1;
									foreach($all_data_arr as $po_id=>$po_data)
									{
										foreach($po_data as $prod_date=>$prod_date_data)
										{
											foreach($prod_date_data as $item_id=>$item_data)
											{
												foreach($item_data as $color_id=>$color_data)
												{
													foreach($color_data as $location_id=>$location_data)
													{
														foreach($location_data as $floor_id=>$floor_data)
														{
															$ex_linedata='';
															$ex_linedata=array_filter(array_unique(explode('__',$floor_data)));
															foreach($ex_linedata as $data_all)
															{
																	$line_id=''; $buyer_name=''; $job_no=''; $po_no=''; $style_ref=''; $ref_no=''; $file_no=''; $company_id=''; $prod_source=''; $resource_allo='';
																	$ex_data=array_filter(explode('##',$data_all));
																	//echo $floor_id.'='; //die;
																if($ex_data[1]!="")
																{
																	if($ex_data[0]=="") $line_id=0;
																	else $line_id=$ex_data[0];
																	$buyer_name=$ex_data[1];
																	$job_no=$ex_data[2];
																	$po_no=$ex_data[3];
																	$style_ref=$ex_data[4];
																	$ref_no=$ex_data[5];
																	$file_no=$ex_data[6];
																	//$company_id=$ex_data[7];
																	$prod_source=$ex_data[7];
																	$resource_allo=$ex_data[8];
																	$serving_comp_id=$ex_data[9];
																	$serving_company='';
																	// new development
																	if($ex_data[7]==1)
																	{
																		$serving_company=$company_short_library[$serving_comp_id];
																	}
																	else if($ex_data[7]==3)
																	{
																		$serving_company=$supplier_arr[$serving_comp_id];
																	}

																	/*if($prod_source==1)
																	{
																		$serving_company=$company_short_library[$ex_data[10]];
																	}
																	else if($prod_source==3)
																	{
																		$serving_company=$supplier_arr[$ex_data[10]];
																	}*/

																	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

																	$cutting_qty=$print_qty=$printRec_qty=$emb_qty=$embRec_qty=$wash_qty=$washRec_qty=$special_qty=$specialRec_qty=$sewIn_inQty=$sewIn_outQty=$sewing_input_total=$sewOut_inQty=$sewOut_outQty=$sewing_output_total=$ironIn_qty=$ironOut_qty=$iron_qty_total=$reIron_qty=$finishIn_qty=$finishOut_qty=$finishing_qty=$carton_qty=$rejFinish_qty=$rejSewing_qty=$reject_Qty=0;

																	$cutting_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['1']['0']['pQty'];

																	$print_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['2']['1']['embQty'];

																	$printRec_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['3']['1']['embQty'];

																	$emb_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['2']['2']['embQty'];

																	$embRec_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['3']['2']['embQty'];

																	$wash_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['2']['3']['embQty'];

																	$washRec_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['3']['3']['embQty'];

																	$special_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['2']['4']['embQty'];

																	$specialRec_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['3']['4']['embQty'];

																	$sewIn_inQty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['4']['1']['sQty'];

																	$sewIn_outQty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['4']['3']['sQty'];

																	$sewOut_inQty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['5']['1']['sQty'];

																	$sewOut_outQty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['5']['3']['sQty'];

																	$ironIn_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['7']['1']['sQty'];

																	$ironOut_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['7']['3']['sQty'];

																	$reIron_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['7']['0']['reQty'];


																	$polyIn_Qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['11']['1']['sQty'];

																	$polyOut_Qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['11']['3']['sQty'];

																	$finishIn_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['8']['1']['sQty'];

																	$finishOut_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['8']['3']['sQty'];

																	$carton_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id]['0']['0']['0']['crtQty'];
																	// $rejFinish_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$location_id][$floor_id][$line_id]['8']['0']['rejectQty'];
																	$rejSewing_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['5']['0']['rejectQty'];

																	$rejCutting_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['1']['0']['rejectQty'];

																	$rejPrint_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['1']['0']['rejectQty'];

																	$rejIron_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['7']['0']['rejectQty'];

																	$rejFinish_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['8']['0']['rejectQty'];

																	$rejSewing_qty=$prod_dlfl_qty_arr[$po_id][$prod_date][$item_id][$color_id][$location_id][$floor_id][$line_id]['5']['0']['rejectQty'];

																	$sewing_line='';
																	if($resource_allo==1)
																	{
																		$line_number=explode(",",$prod_reso_arr[$line_id]);
																		foreach($line_number as $val)
																		{
																			if($sewing_line=='') $sewing_line=$line_library[$val]; else $sewing_line.=", ".$line_library[$val];
																		}
																	}
																	else $sewing_line=$line_library[$line_id];
																	if($line_id!="") $swing_line_id=$line_id; else $swing_line_id=0;

																	$html.='<tr>
																	<td >'.$i.'</td>
																	<td >'.$serving_company.'</td>
																	<td>'.$job_no.'</td>
																	<td >'.$po_no.'</td>
																	<td >'.$buyer_short_library[$buyer_name].'</td>
																	<td >'.$style_ref.'</td>
																	<td >'.$file_no.'</td>
																	<td >'. $ref_no.'</td>
																	<td >'.$garments_item[$item_id].'</td>
																	<td  >'.$color_library_arr[$color_id].'</td>
																	<td >'.change_date_format($prod_date).'</td>
																	<td >'. $knitting_source[$prod_source].'</td>
																	<td >'.$location_library[$location_id].'</td>
																	<td >'. $floor_library[$floor_id].'</td>
																	<td >'.$sewing_line.'</td>
																	<td  >'.$cutting_qty.'</td>
																	<td  >'.$print_qty.'</td>
																	<td  >'.$emb_qty.'</td>
																	<td  >'.$embRec_qty.'</td>
																	<td  >'.$wash_qty.'</td>
																	<td  >'.$washRec_qty.'</td>
																	<td  >'.$special_qty.'</td>
																	<td  >'.$specialRec_qty.'</td>

																	<td  >'.$sewIn_inQty.'</td>
																	<td  >'.$sewIn_outQty.'</td>
																	<td  >'.$sewing_input_total.'</td>
																	<td  >'.$sewOut_inQty.'</td>
																	<td  >'.$sewOut_outQty.'</td>
																	<td  >'.$sewing_output_total.'</td>
																	<td  >'.$ironIn_qty.'</td>
																	<td  >'.$ironOut_qty.'</td>
																	<td  >'.$iron_qty_total.'</td>
																	<td  >'.$reIron_qty.'</td>
																	<td  >'.$polyIn_Qty.'</td>
																	<td  >'.$polyOut_Qty.'</td>
																	<td  >'.$poly_qty_total.'</td>
																	<td  >'.$finishIn_qty.'</td>
																	<td  >'.$finishOut_qty.'</td>
																	<td  >'.$carton_qty.'</td>
																	<td  >'.number_format($prod_dzn,2).'</td>
																	<td  >'.$reject_Qty.'</td>
																	<td   >'.$rejCutting_qty.'</td>
																	<td  >'.$rejPrint_qty.'</td>
																	<td  >'.$rejSewing_qty.'</td>
																	<td  >'.$rejIron_qty.'</td>
																	<td   >'.$rejFinish_qty.'</td>
																	<td></td>;
																</tr>';

																	$tot_cutting_qty+=$cutting_qty;
																	$tot_print_qty+=$print_qty;
																	$tot_printRec_qty+=$printRec_qty;
																	$tot_emb_qty+=$emb_qty;
																	$tot_embRec_qty+=$embRec_qty;
																	$tot_wash_qty+=$wash_qty;
																	$tot_washRec_qty+=$washRec_qty;
																	$tot_special_qty+=$special_qty;
																	$tot_specialRec_qty+=$specialRec_qty;
																	$tot_sewIn_inQty+=$sewIn_inQty;
																	$tot_sewIn_outQty+=$sewIn_outQty;
																	$tot_sewing_input+=$sewing_input_total;
																	$tot_sewOut_inQty+=$sewOut_inQty;
																	$tot_sewOut_outQty+=$sewOut_outQty;
																	$tot_sewing_output+=$sewing_output_total;
																	$tot_ironIn_qty+=$ironIn_qty;
																	$tot_ironOut_qty+=$ironOut_qty;
																	$tot_iron_qty+=$iron_qty_total;
																	$tot_polyIn_Qty+=$polyIn_Qty;
																	$tot_polyOut_Qty+=$polyOut_Qty;
																	$tot_poly_qty_sum+=$poly_qty_total;
																	$tot_reIron_qty+=$reIron_qty;
																	$tot_finishIn_qty+=$finishIn_qty;
																	$tot_finishOut_qty+=$finishOut_qty;
																	$tot_finishing_qty+=$finishing_qty;
																	$tot_carton_qty+=$carton_qty;
																	$tot_rejFinish_qty+=$rejFinish_qty;
																	$tot_rejSewing_qty+=$rejSewing_qty;
																	$tot_reject_Qty+=$reject_Qty;
																	// $total_prod_dzn+=$prod_dzn;
																	//$tot_reject_Qty+=$reject_Qty;
																	$tot_rejCutting_qty+=$rejCutting_qty;
																	$tot_rejPrint_qty+=$rejPrint_qty;
																	//$tot_rejSewing_qty+=$rejSewing_qty;
																	$tot_rejIron_qty+=$rejIron_qty;
																//$tot_rejFinish_qty+=$rejFinish_qty;

																	$i++;
																}
															}
														}
													}
												}
											}
										}
									}

						$html.='</table>';
						$html.='<table>';

						$html.='<tr>;
										<td ></td>
										<td ></td>
										<td ></td>
										<td ></td>
										<td ></td>
										<td ></td>
										<td ></td>
										<td ></td>
										<td ></td>
										<td ></td>
										<td ></td>
										<td ></td>
										<td ></td>
										<td ></td>
										<td >Totals</td>
										<td  >'.$tot_cutting_qty.'</td>
										<td  >'.$tot_print_qty.'</td>
										<td  >'.$tot_printRec_qty.'</td>
										<td  >'.$tot_emb_qty.'</td>
										<td  >'.$tot_embRec_qty.'</td>
										<td  >'.$tot_wash_qty.'</td>
										<td  >'.$tot_washRec_qty.'</td>
										<td  >'.$tot_special_qty.'</td>
										<td  >'.$tot_specialRec_qty.'</td>
										<td  >'.$tot_sewIn_inQty.'</td>
										<td  >'.$tot_sewIn_outQty.'</td>
										<td  >'.$tot_sewing_input.'</td>
										<td  >'.$tot_sewOut_inQty.'</td>
										<td  >'.$tot_sewOut_outQty.'</td>
										<td  >'.$tot_sewing_output.'</td>
										<td  >'.$tot_ironIn_qty.'</td>
										<td  >'.$tot_ironOut_qty.'</td>
										<td  >'.$tot_iron_qty.'</td>
										<td  >'.$tot_reIron_qty.'</td>
										<td  >'.$tot_polyIn_Qty.'</td>
										<td  >'.$tot_polyOut_Qty.'</td>
										<td  >'.$tot_poly_qty_sum.'</td>
										<td  >'.$tot_finishIn_qty.'</td>
										<td  >'.$tot_finishOut_qty.'</td>
										<td  >'.$tot_finishing_qty.'</td>
										<td  >'.$tot_carton_qty.'</td>
										<td  >'.number_format($total_prod_dzn,2).'</td>
										<td  >'.$tot_reject_Qty.'</td>
										<td  >'.$tot_rejCutting_qty.'</td>
										<td  >'.$tot_rejPrint_qty.'</td>
										<td  >'.$tot_rejSewing_qty.'</td>
										<td  >'.$tot_rejIron_qty.'</td>
										<td  >'.$tot_rejFinish_qty.'</td>

							</tr>

							</table>';
						$html.='</div>';



					}// end if condition of type

						/*=============================================================================================/
						/											Subcon part										   /
						/============================================================================================ */

						if(str_replace("'","",trim($cbo_subcon))==2) //yes
					{


							$html.='<h5 >Production-Subcontract Order (Inbound) Details</h5>';

								$html.='<table>;
									<thead>
										<tr>
											<th >Sl.</th>
											<th >Working Factory</th>
											<th >Job No</th>
											<th >Order No</th>
											<th >Buyer </th>
											<th >Style </th>
											<th >Item Name</th>
											<th >Production Date</th>

											<th >Floor</th>
											<th >Sewing Line</th>

											<th "">Cutting</th>
											<th "">Sewing Input</th>
											<th "">Sewing Output</th>
											<th "">Iron Output</th>
											<th "">Gmts. Finishing</th>
											<th "">Remarks</th>
										</tr>
									</thead>
								</table>';
							$html.='<div>;
								<table>';

										$item_id_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
										$subcon_lc_comp2=str_replace("a.", "c.", $subcon_lc_comp);
										$subcon_work_comp2=str_replace("a.", "c.", $subcon_work_comp);

										$production_array=array();
										if($db_type==0)
										{
											$prod_sql= "SELECT c.order_id, c.production_date,c.floor_id,c.line_id,
												sum(CASE WHEN c.production_type ='1' THEN  c.production_qnty  ELSE 0 END) AS cutting_qnty,
												sum(CASE WHEN c.production_type ='7' THEN  c.production_qnty  ELSE 0 END) AS sewing_input_qnty,
												sum(CASE WHEN c.production_type ='2' THEN  c.production_qnty  ELSE 0 END) AS sewingout_qnty,
												sum(CASE WHEN c.production_type ='3' THEN  c.production_qnty  ELSE 0 END) AS ironout_qnty,
												sum(CASE WHEN c.production_type ='4' THEN  c.production_qnty  ELSE 0 END) AS gmts_fin_qnty
											from
												subcon_gmts_prod_dtls c
											where c.status_active=1 and c.is_deleted=0 and c.production_date between $txt_date_from and $txt_date_to $subcon_lc_comp2 $subcon_work_comp2  group by c.order_id, c.production_date,c.floor_id";
										}
										else
										{
											$prod_sql= "SELECT c.order_id,c.gmts_item_id, c.production_date,c.floor_id,c.line_id,
												NVL(sum(CASE WHEN c.production_type ='1' THEN  c.production_qnty  ELSE 0 END),0) AS cutting_qnty,
												sum(CASE WHEN c.production_type ='7' THEN  c.production_qnty  ELSE 0 END) AS sewing_input_qnty,

												NVL(sum(CASE WHEN c.production_type ='2' THEN  c.production_qnty  ELSE 0 END),0) AS sewingout_qnty,
												NVL(sum(CASE WHEN c.production_type ='3' THEN  c.production_qnty  ELSE 0 END),0) AS ironout_qnty,
												NVL(sum(CASE WHEN c.production_type ='4' THEN  c.production_qnty  ELSE 0 END),0) AS gmts_fin_qnty
											from
												subcon_gmts_prod_dtls c
											where c.status_active=1 and c.is_deleted=0 and c.production_date between $txt_date_from and $txt_date_to $subcon_lc_comp2 $subcon_work_comp2 group by c.order_id,c.gmts_item_id, c.production_date,c.floor_id,c.line_id";
										}
										$prod_sql_result= sql_select($prod_sql);
										// echo $prod_sql;//die;
										foreach($prod_sql_result as $proRes)
										{
											$production_array[$proRes[csf("order_id")]][$proRes[csf("gmts_item_id")]][$proRes[csf("production_date")]][$proRes[csf("floor_id")]][$proRes[csf("line_id")]]['cutting_qnty']=$proRes[csf("cutting_qnty")];
											$production_array[$proRes[csf("order_id")]][$proRes[csf("gmts_item_id")]][$proRes[csf("production_date")]][$proRes[csf("floor_id")]][$proRes[csf("line_id")]]['sewing_input_qnty']=$proRes[csf("sewing_input_qnty")];
											$production_array[$proRes[csf("order_id")]][$proRes[csf("gmts_item_id")]][$proRes[csf("production_date")]][$proRes[csf("floor_id")]][$proRes[csf("line_id")]]['sewingout_qnty']=$proRes[csf("sewingout_qnty")];
											$production_array[$proRes[csf("order_id")]][$proRes[csf("gmts_item_id")]][$proRes[csf("production_date")]][$proRes[csf("floor_id")]][$proRes[csf("line_id")]]['ironout_qnty']=$proRes[csf("ironout_qnty")];
											$production_array[$proRes[csf("order_id")]][$proRes[csf("gmts_item_id")]][$proRes[csf("production_date")]][$proRes[csf("floor_id")]][$proRes[csf("line_id")]]['gmts_fin_qnty']=$proRes[csf("gmts_fin_qnty")];
										}
										// echo "<pre>";
										// print_r($production_array);
										if($db_type==0)
										{
											$order_sql= "SELECT c.id, c.order_no, c.cust_style_ref, a.company_id, a.party_id, a.location_id, a.job_no_prefix_num, b.gmts_item_id, sum(CASE WHEN b.order_id=c.id THEN b.production_qnty ELSE 0 END) AS production_qnty, b.production_date, b.line_id,b.floor_id,b.prod_reso_allo
											from subcon_ord_mst a, subcon_gmts_prod_dtls b, subcon_ord_dtls c
											where b.order_id=c.id and b.production_date between $txt_date_from and $txt_date_to $subcon_lc_comp $subcon_work_comp $sub_floor_name $sub_location_cond and a.subcon_job=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
											group by b.order_id, b.production_date
											order by b.production_date";
										}

										else
										{
											$order_sql= "SELECT c.id, c.order_no, c.cust_style_ref, a.company_id, a.party_id, a.location_id, a.job_no_prefix_num, b.gmts_item_id, sum(CASE WHEN b.order_id=c.id THEN b.production_qnty ELSE 0 END) AS production_qnty,  b.production_date, b.line_id,b.floor_id,b.prod_reso_allo
											from subcon_ord_mst a, subcon_gmts_prod_dtls b, subcon_ord_dtls c
											where b.order_id=c.id and b.production_date between $txt_date_from and $txt_date_to  $subcon_lc_comp $subcon_work_comp	and a.subcon_job=c.job_no_mst $sub_floor_name $sub_location_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
											group by b.order_id, c.id, c.order_no, c.cust_style_ref, a.company_id, a.job_no_prefix_num, a.party_id, a.company_id, a.party_id, a.location_id, b.gmts_item_id, b.production_date, b.line_id,b.floor_id,b.prod_reso_allo
											order by c.id";
										}

									// echo $order_sql; die;

										$order_sql_result=sql_select($order_sql);
										$j=0;$k=0;
										$total_cutt = 0;
										//$po_item_line_array=array();
										foreach($order_sql_result as $orderRes)
										{

												//if( $po_item_line_array[$orderRes[csf("id")]][$orderRes[csf("gmt_item_id")]][$orderRes[csf("line_id")]]=="" )
												//{
													$j++;
													if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

													$sewing_line='';
													if($orderRes[csf('prod_reso_allo')]==1)
													{
														$line_number=explode(",",$prod_reso_arr[$orderRes[csf("line_id")]]);
														foreach($line_number as $val)
														{
															if($sewing_line=='') $sewing_line=$line_library[$val]; else $sewing_line.=", ".$line_library[$val];
														}
													}
													else {$sewing_line=$line_library[$orderRes[csf("line_id")]];}
													$po_id=$orderRes[csf("id")];
													$item_id=$orderRes[csf("gmts_item_id")];
													$prod_date=$orderRes[csf("production_date")];
													$cutting= $production_array[$orderRes[csf("id")]][$orderRes[csf("gmts_item_id")]][$orderRes[csf("production_date")]][$orderRes[csf("floor_id")]][$orderRes[csf("line_id")]]['cutting_qnty'];
													$input= $production_array[$orderRes[csf("id")]][$orderRes[csf("gmts_item_id")]][$orderRes[csf("production_date")]][$orderRes[csf("floor_id")]][$orderRes[csf("line_id")]]['sewing_input_qnty'];
													$output=$production_array[$orderRes[csf("id")]][$orderRes[csf("gmts_item_id")]][$orderRes[csf("production_date")]][$orderRes[csf("floor_id")]][$orderRes[csf("line_id")]]['sewingout_qnty'];
													$iron= $production_array[$orderRes[csf("id")]][$orderRes[csf("gmts_item_id")]][$orderRes[csf("production_date")]][$orderRes[csf("floor_id")]][$orderRes[csf("line_id")]]['ironout_qnty'];
													$fin= $production_array[$orderRes[csf("id")]][$orderRes[csf("gmts_item_id")]][$orderRes[csf("production_date")]][$orderRes[csf("floor_id")]][$orderRes[csf("line_id")]]['gmts_fin_qnty'];


													$html.='
													<tr>;
														<td  >'.$j.'</td>
														<td >'.$company_short_library[$orderRes[csf("company_id")]].'</td>
														<td >'.$orderRes[csf("job_no_prefix_num")].'</td>
														<td '.$orderRes[csf("order_no")].'</td>
														<td >'.$buyer_short_library[$orderRes[csf("party_id")]].'</td>
														<td >'.$orderRes[csf("cust_style_ref")].'</td>
														<td >'.$garments_item[$orderRes[csf("gmts_item_id")]].'</td>
														<td  >'.change_date_format($orderRes[csf("production_date")]).'</td>
														<td  >'.$floor_library[$orderRes[csf('floor_id')]].'</td>
														<td  >'.$sewing_line.'</td>
														<td  >'.$cutting.'</td>
														<td>'.$input.'</td>
														<td>'.$output.'</td>
														<td>'.$iron.'</td>
														<td>'.$fin.'</td>
														<td ""></td>
													</tr>';


													$total_cutt+=$cutting;
													$total_sewinput+=$input;
													$total_sew+=$output;
													$total_iron_sub+=$iron;
													$total_gmtfin+=$fin;




										}

										$html.='</table>';

										$html.='<table  "15" rules="all" report_table_footer2" >';
										$html.='<tr>;
												<td ></td>
												<td ></td>
												<td ></td>
												<td ></td>
												<td ></td>
												<td ></td>
												<td ></td>
												<td ></td>
												<td ></td>
												<td >Total:</td>
												<td>'.$total_cutt.'</td>
												<td>'.$total_sew_input.'</td>
												<td>'.$total_sew.'</td>
												<td>'.$total_iron_sub.'</td
												<td>'.$total_gmtfin.'</td>
												<td ""></td>';
									$html.='</table>';

							$html.'</div>';



					}
						//-------------------------------------------END Show Date Location Floor & Line Wise------------------------

			$html.='</div>';

		foreach (glob("bwffsr_$user_id*.xlsx") as $filename) {
			@unlink($filename);
		}
		$name=time();
		$filename='bwffsr_'.$user_id."_".$name.".xlsx";


		$reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
		$spreadsheet = $reader->loadFromString($html);
		$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
		$writer->save($filename);



		echo "$ReportType####$filename####$filename";
		exit();

		die;
	}
	else // details button
	{
		$garments_nature=str_replace("'","",$cbo_garments_nature);
		$cbo_floor=str_replace("'","",$cbo_floor);
		//if($garments_nature==1)$garments_nature="";
		 if($garments_nature==1) $garmentsNature=""; else $garmentsNature=" and c.garments_nature=$garments_nature";
		$type = str_replace("'","",$cbo_type);
		$location = str_replace("'","",$cbo_location);
		if(str_replace("'","",$cbo_company_name)==0) $company_name=""; else $company_name=" and b.company_name=$cbo_company_name";
		if(str_replace("'","",$cbo_buyer_name)==0)$buyer_name="";else $buyer_name=" and a.buyer_name=$cbo_buyer_name";
		if($cbo_floor=="") $floor_name="";else $floor_name=" and c.floor_id in($cbo_floor)";

		if($cbo_floor=="") $floor_id="";else $floor_id=" and c.floor_id in($cbo_floor)";
		if ($location==0) $location_cond=""; else $location_cond=" and c.location in($location) ";

		if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="")$txt_date="";
		else $txt_date=" and c.production_date between $txt_date_from and $txt_date_to";
		$fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
		$toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );
		//cbo_garments_nature
		$file_no = str_replace("'","",$txt_file_no);
		$internal_ref = str_replace("'","",$txt_internal_ref);
		if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='".trim($file_no)."' ";
		if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='".trim($internal_ref)."' ";

		?>
		<div>
		<div style="width:<? echo $div_width; ?>px" id="">
			<table width="<? echo $div_width; ?>" cellspacing="0"   >
				<tr class="form_caption" style="border:none;">
					<td colspan="<? echo $colSpan; ?>" align="center" style="border:none;font-size:14px; font-weight:bold" ><? echo $report_name; ?></td>
				 </tr>
				<tr style="border:none;">
					<td colspan="<? echo $colSpan; ?>" align="center" style="border:none; font-size:16px; font-weight:bold">
						Company Name:<? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>
					</td>
				</tr>
				<tr style="border:none;">
					<td colspan="<? echo $colSpan; ?>" align="center" style="border:none;font-size:12px; font-weight:bold">
						<? echo "From $fromDate To $toDate" ;?>
					</td>
				</tr>
			</table>
			<table width="2080" cellspacing="0" border="1" rules="all" align="left">
				<tr>
					<td width="1400" align="left" valign="top"><div style="width:300px; float:left; background-color:#FCF"><strong>Production-Regular Order Summary</strong></div>
					<div align="left">
						<table width="1390" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
							<thead>
								<tr>
									<th width="30">Sl.</th>
									<th width="80">Buyer Name</th>
									<th width="80">Cut Qty</th>
									<th width="80">Sent to Print</th>
									<th width="80">Rcv Print</th>
									<th width="80">Sent to Emb</th>
									<th width="80">Rcv Emb</th>
									<th width="80">Sent to Wash</th>
									<th width="80">Rcv Wash</th>
									<th width="80">Sent to Sp. Works</th>
									<th width="80">Rcv Sp. Works</th>

									<th width="80">Sew Input</th>
									<th width="80">Sew Input (Outbound)</th>
									<th width="80">Sew Output</th>
									<th width="80">Sew Output (Outbound)</th>
									<th width="80">Total Iron</th>
									<th width="80">Total Re-Iron</th>
									<th >Total Finish</th>
								 </tr>
							</thead>
						</table>
				<div style="max-height:225px; overflow-y:scroll; width:1408px" >
				<table cellspacing="0" border="1" class="rpt_table"  width="1390" rules="all" id="" >
				<?

				$buyer_fullQty_arr=array();
				$prod_date_qty_arr=array();
				$prod_dlfl_qty_arr=array();
				$all_data_arr=array();
				if($type==1)
				{
					 $sql_dtls="SELECT a.job_no_prefix_num,a.job_no, a.buyer_name, a.style_ref_no, a.company_name, b.id, b.po_number, b.pub_shipment_date, b.grouping, b.file_no, c.po_break_down_id,c.re_production_qty as re_iron_qnty, c.production_date, c.item_number_id, c.prod_reso_allo,
					sum(case when c.production_type=1 and d.production_type=1 then d.production_qnty else 0 end) as cutting_qnty,
					sum(case when c.production_type=2  and d.production_type=2  and c.embel_name=1 then d.production_qnty else 0 end) as print_input_qnty,
					sum(case when c.production_type=3  and d.production_type=3 and c.embel_name=1 then d.production_qnty else 0 end) as print_output_qnty,
					sum(case when c.production_type=2  and d.production_type=2 and c.embel_name=2 then d.production_qnty else 0 end) as embro_input_qnty,
					sum(case when c.production_type=3 and d.production_type=3  and c.embel_name=2 then d.production_qnty else 0 end) as embro_output_qnty,
					sum(case when c.production_type=2 and d.production_type=2  and c.embel_name=3 then d.production_qnty else 0 end) as wash_input_qnty,
					sum(case when c.production_type=3 and d.production_type=3 and c.embel_name=3 then d.production_qnty else 0 end) as wash_output_qnty,
					sum(case when c.production_type=2 and d.production_type=2  and c.embel_name=4 then d.production_qnty else 0 end) as sp_input_qnty,
					sum(case when c.production_type=3 and d.production_type=3  and c.embel_name=4 then d.production_qnty else 0 end) as sp_output_qnty,
					sum(case when c.production_type=4 and d.production_type=4  and c.production_source=1 then d.production_qnty else 0 end) as sewing_inhouse_input_qnty,
					sum(case when c.production_type=4  and d.production_type=4  and c.production_source<>1 then d.production_qnty else 0 end) as sewing_outbound_input_qnty,
					sum(case when c.production_type=5   and d.production_type=5  and c.production_source=1 then d.production_qnty else 0 end) as sewing_inhouse_output_qnty,
					sum(case when c.production_type=5  and d.production_type=5  and c.production_source<>1 then d.production_qnty else 0 end) as sewing_outbound_output_qnty,
					sum(case when c.production_type=7  and d.production_type=7  and c.production_source=1 then d.production_qnty else 0 end) as iron_inhouse_qnty,
					sum(case when c.production_type=7 and d.production_type=7   and c.production_source<>1 then d.production_qnty else 0 end) as iron_outbound_qnty,
					sum(case when c.production_type=8  and d.production_type=8  and c.production_source=1 then d.production_qnty else 0 end) as finish_inhouse_qnty,
					sum(case when c.production_type=8 and d.production_type=8   and c.production_source<>1 then d.production_qnty else 0 end) as finish_outbound_qnty,
					sum(case when c.production_type=5 and d.production_type=5   then c.reject_qnty else 0 end) as sewing_reject_qnty,
					sum(case when c.production_type=8  and d.production_type=8 then c.reject_qnty else 0 end) as finish_reject_qnty,
					sum(c.carton_qty) as carton_qty
					from wo_po_details_master a, wo_po_break_down b, pro_garments_production_mst c ,pro_garments_production_dtls d
					where a.id=b.job_id and b.id=c.po_break_down_id and c.id=d.mst_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1   and d.is_deleted=0 and d.status_active=1  and a.company_name=$cbo_company_name $buyer_name $file_no_cond $internal_ref_cond $txt_date $floor_name $location_cond $garmentsNature
					group by a.job_no_prefix_num,a.job_no, a.buyer_name, a.style_ref_no, a.company_name, b.id, b.po_number, b.pub_shipment_date, b.grouping, b.file_no, c.po_break_down_id,c.re_production_qty, c.production_date, c.item_number_id, c.prod_reso_allo
					order by b.pub_shipment_date";
				}
				else
				{
					 $sql_dtls="SELECT a.job_no_prefix_num,a.job_no, a.buyer_name, a.style_ref_no, a.company_name, b.id, b.po_number, b.pub_shipment_date, b.grouping, b.file_no, c.location, c.floor_id, c.sewing_line, c.po_break_down_id,c.re_production_qty as re_iron_qnty , c.production_date, c.item_number_id, c.prod_reso_allo,c.production_source,
					sum(case when c.production_type=1 and d.production_type=1 then d.production_qnty else 0 end) as cutting_qnty,
					sum(case when c.production_type=2 and d.production_type=2 and c.embel_name=1 then d.production_qnty else 0 end) as print_input_qnty,
					sum(case when c.production_type=3  and d.production_type=3 and c.embel_name=1 then d.production_qnty else 0 end) as print_output_qnty,
					sum(case when c.production_type=2  and d.production_type=2  and c.embel_name=2 then d.production_qnty else 0 end) as embro_input_qnty,
					sum(case when c.production_type=3  and d.production_type=3 and c.embel_name=2 then d.production_qnty else 0 end) as embro_output_qnty,
					sum(case when c.production_type=2   and d.production_type=2  and c.embel_name=3 then d.production_qnty else 0 end) as wash_input_qnty,
					sum(case when c.production_type=3  and d.production_type=3 and c.embel_name=3 then d.production_qnty else 0 end) as wash_output_qnty,
					sum(case when c.production_type=2  and d.production_type=2 and c.embel_name=4 then d.production_qnty else 0 end) as sp_input_qnty,
					sum(case when c.production_type=3  and d.production_type=3 and c.embel_name=4 then d.production_qnty else 0 end) as sp_output_qnty,
					sum(case when c.production_type=4  and d.production_type=4 and c.production_source=1 then d.production_qnty else 0 end) as sewing_inhouse_input_qnty,
					sum(case when c.production_type=4  and d.production_type=4 and c.production_source<>1 then d.production_qnty else 0 end) as sewing_outbound_input_qnty,
					sum(case when c.production_type=5  and d.production_type=5 and c.production_source=1 then d.production_qnty else 0 end) as sewing_inhouse_output_qnty,
					sum(case when c.production_type=5  and d.production_type=5 and c.production_source<>1 then d.production_qnty else 0 end) as sewing_outbound_output_qnty,
					sum(case when c.production_type=7  and d.production_type=7 and c.production_source=1 then d.production_qnty else 0 end) as iron_inhouse_qnty,
					sum(case when c.production_type=7  and d.production_type=7 and c.production_source<>1 then d.production_qnty else 0 end) as iron_outbound_qnty,
					sum(case when c.production_type=8  and d.production_type=8 and c.production_source=1 then d.production_qnty else 0 end) as finish_inhouse_qnty,
					sum(case when c.production_type=8  and d.production_type=8 and c.production_source<>1 then d.production_qnty else 0 end) as finish_outbound_qnty,
					sum(case when c.production_type=5  and d.production_type=5 then c.reject_qnty else 0 end) as sewing_reject_qnty,
					sum(case when c.production_type=8  and d.production_type=8 then c.reject_qnty else 0 end) as finish_reject_qnty,
					sum(c.carton_qty) as carton_qty
					from wo_po_details_master a, wo_po_break_down b, pro_garments_production_mst c,pro_garments_production_dtls d
					where a.id=b.job_id and b.id=c.po_break_down_id and c.id=d.mst_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  and d.is_deleted=0 and d.status_active=1  and a.company_name=$cbo_company_name $buyer_name $file_no_cond $internal_ref_cond $txt_date $floor_name $location_cond $garmentsNature
					group by a.job_no_prefix_num,a.job_no, a.buyer_name, a.style_ref_no, a.company_name, b.id, b.po_number, b.pub_shipment_date, b.grouping, b.file_no, c.location, c.floor_id, c.sewing_line, c.po_break_down_id,c.re_production_qty, c.production_date, c.item_number_id, c.prod_reso_allo,c.production_source
					order by b.pub_shipment_date";
				}


				//echo $sql_dtls;die;


				$sql_dtls_res=sql_select($sql_dtls);
				foreach($sql_dtls_res as $row)
				{
					//Buyer Wise Summary array start
					$buyer_fullQty_arr[$row[csf("buyer_name")]]['cutting_qnty']+=$row[csf("cutting_qnty")];

					$buyer_fullQty_arr[$row[csf("buyer_name")]]['print_input_qnty']+=$row[csf("print_input_qnty")];
					$buyer_fullQty_arr[$row[csf("buyer_name")]]['print_output_qnty']+=$row[csf("print_output_qnty")];

					$buyer_fullQty_arr[$row[csf("buyer_name")]]['embro_input_qnty']+=$row[csf("embro_input_qnty")];
					$buyer_fullQty_arr[$row[csf("buyer_name")]]['embro_output_qnty']+=$row[csf("embro_output_qnty")];

					$buyer_fullQty_arr[$row[csf("buyer_name")]]['wash_input_qnty']+=$row[csf("wash_input_qnty")];
					$buyer_fullQty_arr[$row[csf("buyer_name")]]['wash_output_qnty']+=$row[csf("wash_output_qnty")];

					$buyer_fullQty_arr[$row[csf("buyer_name")]]['sp_input_qnty']+=$row[csf("sp_input_qnty")];
					$buyer_fullQty_arr[$row[csf("buyer_name")]]['sp_output_qnty']+=$row[csf("sp_output_qnty")];

					$buyer_fullQty_arr[$row[csf("buyer_name")]]['sewing_inhouse_input_qnty']+=$row[csf("sewing_inhouse_input_qnty")];
					$buyer_fullQty_arr[$row[csf("buyer_name")]]['sewing_outbound_input_qnty']+=$row[csf("sewing_outbound_input_qnty")];

					$buyer_fullQty_arr[$row[csf("buyer_name")]]['sewing_inhouse_output_qnty']+=$row[csf("sewing_inhouse_output_qnty")];
					$buyer_fullQty_arr[$row[csf("buyer_name")]]['sewing_outbound_output_qnty']+=$row[csf("sewing_outbound_output_qnty")];

					$buyer_fullQty_arr[$row[csf("buyer_name")]]['total_iron_qnty']+=$row[csf("iron_inhouse_qnty")]+$row[csf("iron_outbound_qnty")];
					$buyer_fullQty_arr[$row[csf("buyer_name")]]['re_iron_qnty']+=$row[csf("re_iron_qnty")];
					$buyer_fullQty_arr[$row[csf("buyer_name")]]['total_finish_qnty']+=$row[csf("finish_inhouse_qnty")]+$row[csf("finish_outbound_qnty")];


				}

				$b=1; //date_wise Summary
				foreach($buyer_fullQty_arr as $buyer_id=>$buyer_data)
				{
					if($buyer_id !="")
					{
						if ($b%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $b; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $b; ?>">
							<td width="30"><? echo $b;?></td>
							<td width="80"><p><? echo $buyer_short_library[$buyer_id]; ?>&nbsp;</p></td>
							<td width="80" align="right"><? echo number_format($buyer_data['cutting_qnty'],0); ?></td>
							<td width="80" align="right"><? echo number_format($buyer_data['print_input_qnty'],0); ?></td>
							<td width="80" align="right"><? echo number_format($buyer_data['print_output_qnty'],0); ?></td>
							<td width="80" align="right"><? echo number_format($buyer_data['embro_input_qnty'],0); ?></td>
							<td width="80" align="right"><? echo number_format($buyer_data['embro_output_qnty'],0); ?></td>
							<td width="80" align="right"><? echo number_format($buyer_data['wash_input_qnty'],0); ?></td>
							<td width="80" align="right"><? echo number_format($buyer_data['wash_output_qnty'],0);  ?></td>
							<td width="80" align="right"><? echo number_format($buyer_data['sp_input_qnty'],0); ?></td>
							<td width="80" align="right"><? echo number_format($buyer_data['sp_output_qnty'],0);  ?></td>
							<td width="80" align="right"><? echo number_format($buyer_data['sewing_inhouse_input_qnty'],0); ?></td>
							<td width="80" align="right"><? echo number_format($buyer_data['sewing_outbound_input_qnty'],0); ?></td>
							<td width="80" align="right"><? echo number_format($buyer_data['sewing_inhouse_output_qnty'],0); ?></td>
							<td width="80" align="right"><? echo number_format($buyer_data['sewing_outbound_output_qnty'],0); ?></td>
							<td width="80" align="right"><? echo number_format($buyer_data['total_iron_qnty'],0); ?></td>
							<td width="80" align="right"><? echo number_format($buyer_data['re_iron_qnty'],0); ?></td>
							<td  align="right"><? echo number_format($buyer_data['total_finish_qnty'],0); ?></td>
						</tr>
						<?
						$sumCutting_qty+=$buyer_data['cutting_qnty'];
						$sumPrinting_qty+=$buyer_data['print_input_qnty'];
						$sumPrintreceived_qty+=$buyer_data['print_output_qnty'];
						$sumEmb_qty+=$buyer_data['embro_input_qnty'];
						$sumEmbRec_qty+=$buyer_data['embro_output_qnty'];
						$sumWash_qty+=$buyer_data['wash_input_qnty'];
						$sumWashRec_qty+=$buyer_data['wash_output_qnty'];
						$sumSpecial_qty+=$buyer_data['sp_input_qnty'];
						$sumSpecialRec_qty+=$buyer_data['sp_output_qnty'];
						$sumSewIn_inQty+=$buyer_data['sewing_inhouse_input_qnty'];
						$sumSewIn_outQty+=$buyer_data['sewing_outbound_input_qnty'];
						$sumSewOut_inQty+=$buyer_data['sewing_inhouse_output_qnty'];
						$sumSewOut_outQty+=$buyer_data['sewing_outbound_output_qnty'];
						$sumIron_qty+=$buyer_data['total_iron_qnty'];
						$sumReIron_qty+=$buyer_data['re_iron_qnty'];
						$sumFinish_qty+=$buyer_data['total_finish_qnty'];
						$b++;
					}
				}
				?>
				</table>
				<table border="1" class="tbl_bottom"  width="1390" rules="all" id="" >
					 <tr>
						<td width="30">&nbsp;</td>
						<td width="80">Total</td>
						<td width="80"><? echo number_format($sumCutting_qty); ?></td>
						<td width="80"><? echo number_format($sumPrinting_qty); ?></td>
						<td width="80"><? echo number_format($sumPrintreceived_qty); ?></td>
						<td width="80"><? echo number_format($sumEmb_qty); ?></td>
						<td width="80"><? echo number_format($sumEmbRec_qty); ?></td>
						<td width="80"><? echo number_format($sumWash_qty); ?></td>
						<td width="80"><? echo number_format($sumWashRec_qty); ?></td>
						<td width="80"><? echo number_format($sumSpecial_qty); ?></td>
						<td width="80"><? echo number_format($sumSpecialRec_qty); ?></td>
						<td width="80"><? echo number_format($sumSewIn_inQty); ?></td>
						<td width="80"><? echo number_format($sumSewIn_outQty); ?></td>
						<td width="80"><? echo number_format($sumSewOut_inQty); ?></td>
						<td width="80"><? echo number_format($sumSewOut_outQty); ?></td>
						<td width="80"><? echo number_format($sumIron_qty); ?></td>
						<td width="80"><? echo number_format($sumReIron_qty); ?></td>
						<td><? echo number_format($sumFinish_qty); ?></td>
					 </tr>
				 </table>
                 </div>
                 </div>
				</td>
				<?
				if(str_replace("'","",trim($cbo_subcon))==2)
				{
					?>
					<td width="470" align="left" valign="top"><div align="left" style="width:350px; background-color:#FCF"><strong>Production-Subcontract Order(Inbound)Summary </strong></div>
					<div style="float:left; width:470px">
						<table width="470" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
							<thead>
								<tr>
									<th width="30">Sl.</th>
									<th width="120">Buyer</th>
									<th width="80">Total Cut Qty</th>
									<th width="80">Total Sew Qty</th>
									<th width="80">Total Iron Qty</th>
									<th>Total Gmt. Fin. Qty</th>
								</tr>
							</thead>
						</table>
						<div style="max-height:425px; width:470px" >
						<table cellspacing="0" border="1" class="rpt_table"  width="470" rules="all" id="" >
						<?
						if(str_replace("'","",$cbo_floor)==0) $sub_floor_name="";else $sub_floor_name=" and b.floor_id in($cbo_floor)";
						if ($location==0) $sub_location_cond=""; else $sub_location_cond=" and b.location_id in(".$location.") ";

						$total_po_quantity=0;$total_po_value=0;$total_cut_subcon=0;$total_sew_out_subcon=0;$total_ex_factory=0;
						$i=1;
						if(str_replace("'","",$cbo_company_name)==0) $company_name_sub=""; else $company_name_sub="and a.company_id=$cbo_company_name";
						if($db_type==0)
						{
							$ex_factory_sql="SELECT a.party_id, sum(c.order_quantity) as order_quantity
							from subcon_ord_mst a, subcon_ord_dtls c, subcon_gmts_prod_dtls b
							where a.id=c.mst_id and c.id=b.order_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and b.production_date between $txt_date_from and $txt_date_to $company_name_sub $sub_floor_name $sub_location_cond group by a.party_id";
						}
						else
						{
							$ex_factory_sql="SELECT a.party_id, sum(c.order_quantity) as order_quantity
							from subcon_ord_mst a, subcon_ord_dtls c, subcon_gmts_prod_dtls b
							where a.id=c.mst_id and c.id=b.order_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and b.production_date between $txt_date_from and $txt_date_to $company_name_sub $sub_floor_name $sub_location_cond group by a.party_id";
						}
						//echo  $exfactory_sql;
						$ex_factory_sql_result=sql_select($ex_factory_sql);
						$ex_factory_arr=array();
						foreach($ex_factory_sql_result as $resRow)
						{
							$ex_factory_arr[$resRow[csf("party_id")]] = $resRow[csf("order_quantity")];
						}
						//var_dump($exfactory_arr);die;
						//print_r($ex_factory_arr);die;

						//@@@@@@@@@@@@@@@@@@@@@
						$sub_cut_sew_array=array();

						if($db_type==0)
						{
							$production_mst_sql= sql_select("SELECT  a.party_id,
							sum(CASE WHEN production_type ='1' THEN production_qnty ELSE 0 END) AS cutting_qnty,
							sum(CASE WHEN production_type ='2' THEN production_qnty ELSE 0 END) AS sewingout_qnty,
							sum(CASE WHEN production_type ='3' THEN production_qnty  ELSE 0 END) AS ironout_qnty,
							sum(CASE WHEN production_type ='4' THEN production_qnty  ELSE 0 END) AS gmts_fin_qnty
							from subcon_ord_mst a, subcon_ord_dtls c, subcon_gmts_prod_dtls b

							where a.id=c.mst_id and c.id=b.order_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and  c.is_deleted=0 and c.status_active=1 and b.production_date between $txt_date_from and $txt_date_to $company_name_sub $sub_location_cond $sub_floor_name group by a.party_id");
						}
						else
						{
							$production_mst_sql=sql_select("SELECT  a.party_id,
							sum(CASE WHEN production_type ='1' THEN production_qnty ELSE 0 END) AS cutting_qnty,
							sum(CASE WHEN production_type ='2' THEN production_qnty ELSE 0 END) AS sewingout_qnty,
							sum(CASE WHEN production_type ='3' THEN production_qnty  ELSE 0 END) AS ironout_qnty,
							sum(CASE WHEN production_type ='4' THEN production_qnty  ELSE 0 END) AS gmts_fin_qnty
							from subcon_ord_mst a, subcon_ord_dtls c, subcon_gmts_prod_dtls b
							where a.id=c.mst_id and c.id=b.order_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.production_date between $txt_date_from and $txt_date_to $company_name_sub $sub_location_cond $sub_floor_name group by a.party_id");
						}
						foreach($production_mst_sql as $sql_result)
						{
							$sub_cut_sew_array[$sql_result[csf("party_id")]]['1']=$sql_result[csf("cutting_qnty")];
							$sub_cut_sew_array[$sql_result[csf("party_id")]]['2']=$sql_result[csf("sewingout_qnty")];
							$sub_cut_sew_array[$sql_result[csf("party_id")]]['3']=$sql_result[csf("ironout_qnty")];
							$sub_cut_sew_array[$sql_result[csf("party_id")]]['4']=$sql_result[csf("gmts_fin_qnty")];
						}
						//var_dump($cutting_array);
						//@@@@@@@@@@@@@@@@@@@@@
						if($db_type==0)
						{
							$production_date_sql="SELECT a.party_id, sum(c.order_quantity) as po_quantity, sum(c.amount) as po_total_price
							from subcon_ord_mst a, subcon_ord_dtls c , subcon_gmts_prod_dtls b
							where a.id=c.mst_id and c.id=b.order_id and b.production_type in (1,2) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.production_date between $txt_date_from and $txt_date_to $company_name_sub $sub_location_cond $sub_floor_name  group by a.party_id order by a.party_id ASC";
						}
						else
						{
							$production_date_sql="SELECT a.party_id, sum(c.order_quantity) as po_quantity, sum(c.amount) as po_total_price
							from subcon_ord_mst a, subcon_ord_dtls c, subcon_gmts_prod_dtls b
							where a.id=c.mst_id and c.id=b.order_id and b.production_type in (1,2) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.production_date between $txt_date_from and $txt_date_to $company_name_sub $sub_location_cond $sub_floor_name group by a.party_id order by a.party_id ASC";
						}
						//echo $production_date_sql;//die;
						$pro_sql_result=sql_select($production_date_sql);
						foreach($pro_sql_result as $pro_date_sql_row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $b; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $b; ?>">
								<td width="30"><? echo $i;?></td>
								<td width="120"><? echo $buyer_short_library[$pro_date_sql_row[csf("party_id")]]; ?></td>
								<td width="80" align="right"><? echo number_format($sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['1']); ?></td>
								<td width="80" align="right"><? echo number_format($sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['2']); ?></td>
								<td width="80" align="right"><? echo number_format($sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['3']); ?></td>
								<td align="right"><? echo number_format($sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['4']); ?></td>
							</tr>
							<?
							$total_cut_subcon+=$sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['1'];
							$total_sew_out_subcon+=$sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['2'];
							$total_iron_subcon+=$sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['3'];
							$total_gmts_fin_subcon+=$sub_cut_sew_array[$pro_date_sql_row[csf("party_id")]]['4'];
							$i++;$b++;
						}//end foreach 1st
						//$chart_data_qnty="Order Qty;".$total_po_quantity."\n"."Cutting;".$total_cut."\n"."Sew Out ;".$total_sew_out."\n"."Ex-Fact;".$total_ex_factory."\n";
						?>
						</table>
						<table border="1" class="tbl_bottom"  width="470" rules="all" id="" >
							<tr>
								<td width="30">&nbsp;</td>
								<td width="120" align="right">Total</td>
								<td width="80" id="tot_cutting"><? echo number_format($total_cut_subcon); ?></td>
								<td width="80" id="tot_sew_out"><? echo number_format($total_sew_out_subcon); ?></td>
								<td width="80" id="tot_iron_out"><? echo number_format($total_iron_subcon); ?></td>
								<td id="tot_gmt_fin_out"><? echo number_format($total_gmts_fin_subcon); ?></td>
							</tr>
						</table>
						<br />
							<div style="background-color:#FF9; width:400px; font-size:16px"><strong>Total Cutting: <? echo number_format($all_production_cutt=$sumCutting_qty+$total_cut_subcon,0); ?> (Pcs)&nbsp;&nbsp; & Total Sewing: <? echo number_format($all_production_sewing=$sumSewOut_inQty+$total_sew_out_subcon,0); ?> (Pcs)</strong></div><br />
							<div style="background-color:#FF9; width:400px; font-size:16px"><strong>Total Iron: <? echo number_format($all_production_iron=$sumIron_qty+$total_iron_subcon,0); ?> (Pcs)&nbsp;&nbsp; & Total Gmts. Fin.: <? echo number_format($all_production_gmts_fin=$sumFinish_qty+$total_gmts_fin_subcon,0); ?> (Pcs)</strong></div>
							</div>
						</div>
					</td>
					<?
				}
				?>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
		</table>
		</div>
		<div style="clear:both"></div>
		<br />
		<?
		if($type==1) //--------------------------------------------Show Date Wise
		{
			?>
			<h5 style="width:600px; float:left; background-color:#FCF"><strong>Production-Regular Order</strong></h5>
			<table width="3180" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
				<thead>
					<tr>
						<th width="30">Sl.</th>
						<th width="100">Working Factory</th>
						<th width="100">Job No</th>
						<th width="130">Order Number</th>
	                    <th width="70">Shipment Date</th>
						<th width="100">Buyer Name</th>
						<th width="130">Style Name</th>
						<th width="100">File No</th>
						<th width="100">Internal Ref</th>
						<th width="130">Item Name</th>
						<th width="70">Production Date</th>
						<th width="80">Cutting</th>
						<th width="80">Sent to prnt</th>
						<th width="80">Rcv prn/Emb</th>

						<th width="80">Sent to Emb</th>
						<th width="80">Rcv Emb</th>
						<th width="80">Sent to Wash</th>
						<th width="80">Rcv Wash</th>
						<th width="80">Sent to Sp. Works</th>
						<th width="80">Rcv Sp. Works</th>

						<th width="80">Sewing In (Inhouse)</th>
						<th width="80">Sewing In (Out-bound)</th>
						<th width="80">Total Sewing Input</th>
						<th width="80">Sewing Out (Inhouse)</th>
						<th width="80">Sewing Out (Out-bound)</th>
						<th width="80">Total Sewing Out</th>

						<th width="80">Iron Qty (Inhouse)</th>
						<th width="80">Iron Qty (Out-bound)</th>
						<th width="80">Total Iron Qty</th>

						<th width="80">Re-Iron Qty </th>
						<th width="80">Finish Qty (Inhouse)</th>
						<th width="80">Finish Qty (Out-bound)</th>
						<th width="80">Total Finish Qty</th>

						<th width="80">Today Carton</th>
						<th width="80">Prod/Dzn</th>
						<th width="80">Reject Qty</th>
						<th>Remarks</th>
					</tr>
				</thead>
			</table>
			<div style="width:3180px; overflow-y: scroll; max-height:400px;" id="scroll_body">
				<table cellspacing="0" border="1" class="rpt_table"  width="3160" rules="all" id="table_body" >
					<?
					$i=1;
					$month_check=array();
					$p=1;
					foreach($sql_dtls_res as $row)
					{
						if($row[csf("company_name")] !="")
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							/*$cutting_qty=$print_qty=$printRec_qty=$emb_qty=$embRec_qty=$wash_qty=$washRec_qty=$special_qty=$specialRec_qty=$sewIn_inQty=$sewIn_outQty=$sewing_input_total=$sewOut_inQty=$sewOut_outQty=$sewing_output_total=$ironIn_qty=$ironOut_qty=$iron_qty_total=$reIron_qty=$finishIn_qty=$finishOut_qty=$finishing_qty=$carton_qty=$rejFinish_qty=$rejSewing_qty=$reject_Qty=0;
							$cutting_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['1']['0']['pQty'];
							$print_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['2']['1']['embQty'];
							$printRec_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['3']['1']['embQty'];
							$emb_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['2']['2']['embQty'];
							$embRec_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['3']['2']['embQty'];
							$wash_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['2']['3']['embQty'];
							$washRec_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['3']['3']['embQty'];
							$special_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['2']['4']['embQty'];
							$specialRec_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['3']['4']['embQty'];
							$sewIn_inQty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['4']['1']['sQty'];
							$sewIn_outQty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['4']['3']['sQty'];
							$sewOut_inQty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['5']['1']['sQty'];
							$sewOut_outQty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['5']['3']['sQty'];
							$ironIn_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['7']['1']['sQty'];
							$ironOut_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['7']['3']['sQty'];
							$reIron_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['7']['0']['reQty'];
							$finishIn_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['8']['1']['sQty'];
							$finishOut_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['8']['3']['sQty'];
							$carton_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['0']['0']['crtQty'];
							$rejFinish_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['8']['0']['rejectQty'];
							$rejSewing_qty=$prod_date_qty_arr[$po_id][$prod_date][$item_id]['5']['0']['rejectQty'];*/

							if($month_check[date("Y-m",strtotime($row[csf("pub_shipment_date")]))]=="")
							{
								$month_check[date("Y-m",strtotime($row[csf("pub_shipment_date")]))]=date("Y-m",strtotime($row[csf("pub_shipment_date")]));
								$month=date("F",strtotime($row[csf("pub_shipment_date")]));
								$year=date("Y",strtotime($row[csf("pub_shipment_date")]));
								if($p==1)
								{
									?>
									 <tr bgcolor="#FFFFCC">
										<td colspan="37" style="font-size:18px; font-weight:bold;"><? echo $month." ".$year; ?></td>
									</tr>
									<?
								}
								if($p!=1)
								{

									?>

	                                <tr bgcolor="#CCCCCC">
	                                    <td>&nbsp;</td>
	                                    <td>&nbsp;</td>
	                                    <td>&nbsp;</td>
	                                    <td>&nbsp;</td>
	                                    <td>&nbsp;</td>
	                                    <td>&nbsp;</td>
	                                    <td>&nbsp;</td>
	                                    <td>&nbsp;</td>
	                                    <td>&nbsp;</td>
	                                    <td>&nbsp;</td>
	                                    <td align="right" style="font-weight:bold;">Month Total:</td>
	                                    <td align="right"><? echo $month_cutting_qnty; ?></td>
	                                    <td align="right"><? echo $month_print_input_qnty; ?></td>
	                                    <td align="right"><? echo $month_print_output_qnty; ?></td>
	                                    <td align="right"><? echo $month_embro_input_qnty; ?></td>
	                                    <td align="right"><? echo $month_embro_output_qnty;  ?></td>
	                                    <td align="right"><? echo $month_wash_input_qnty; ?></td>
	                                    <td align="right"><? echo $month_wash_output_qnty; ?></td>
	                                    <td align="right"><? echo $month_sp_input_qnty; ?></td>
	                                    <td align="right"><? echo $month_sp_output_qnty; ?> </td>
	                                    <td align="right"><? echo $month_sewing_inhouse_input_qnty; ?></td>
	                                    <td align="right"><? echo $month_sewing_outbound_input_qnty; ?></td>
	                                    <td align="right"><? echo $month_sewing_input_total; ?></td>
	                                    <td align="right"><? echo $month_sewing_inhouse_output_qnty; ?></td>
	                                    <td align="right"><? echo $month_sewing_outbound_output_qnty; ?></td>
	                                    <td align="right"><? echo $month_sewing_output_total; ?></td>
	                                    <td align="right"><? echo $month_iron_inhouse_qnty; ?></td>
	                                    <td align="right"><? echo $month_iron_outbound_qnty; ?></td>
	                                    <td align="right"><? echo $month_iron_qty_total; ?></td>
	                                    <td align="right"><? echo $month_re_iron_qnty; ?></td>
	                                    <td align="right"><? echo $month_finish_inhouse_qnty; ?></td>
	                                    <td align="right"><? echo $month_finish_outbound_qnty; ?></td>
	                                    <td align="right"><? echo $month_finishing_qty; ?></td>
	                                    <td align="right"><? echo $month_carton_qty; ?></td>
	                                    <td align="right"><? echo number_format($month_prod_dzn,2); ?></td>
	                                    <td align="right" ><? echo $month_reject_Qty; ?></td>
	                                    <td>&nbsp;</td>
	                                </tr>
									 <tr bgcolor="#FFFFCC">
										<td colspan="37" style="font-size:18px; font-weight:bold;"><? echo $month." ".$year; ?></td>
									</tr>
	                                <?
									$month_cutting_qnty=$month_print_input_qnty=$month_print_output_qnty=$month_embro_input_qnty=$month_embro_output_qnty=$month_wash_input_qnty=$month_wash_output_qnty=$month_sp_input_qnty=$month_sp_output_qnty=$month_sewing_inhouse_input_qnty=$month_sewing_outbound_input_qnty=$month_sewing_input_total=$month_sewing_inhouse_output_qnty=$month_sewing_outbound_output_qnty=$month_sewing_output_total=$month_iron_inhouse_qnty=$month_iron_outbound_qnty=$month_iron_qty_total=$month_re_iron_qnty=$month_finish_inhouse_qnty=$month_finish_outbound_qnty=$month_finishing_qty=$month_carton_qty=$month_prod_dzn=$month_reject_Qty=0;
								}
								$p++;
							}


							$rejSewing_qty=$rejFinish_qty=0;

							$rejSewing_qty=$row[csf("sewing_reject_qnty")];
							$rejFinish_qty=$row[csf("finish_reject_qnty")];
							$po_id=$row[csf("id")]; $prod_date=$row[csf("production_date")]; $item_id=$row[csf("item_number_id")];
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $b; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $b; ?>">
								<td width="30"><? echo $i;?></td>
								<td width="100"><p><? echo $company_short_library[$row[csf("company_name")]]; ?></p></td>
								<td width="100"><p><? echo $row[csf("job_no_prefix_num")];?></p></td>
								<td width="130"><p><a href="##" onclick="openmypage_order(<? echo $po_id; ?>,<? echo $item_id;?>,'orderQnty_popup');" ><? echo $row[csf("po_number")]; ?></a></p></td>
	                            <td width="70"><p><? if($row[csf("pub_shipment_date")]!="" && $row[csf("pub_shipment_date")]!="0000-00-00") echo change_date_format($row[csf("pub_shipment_date")]); ?></p></td>
								<td width="100"><p><? echo $buyer_short_library[$row[csf("buyer_name")]]; ?></p></td>
								<td width="130"><p><? echo $row[csf("style_ref_no")]; ?></p></td>
								<td width="100"><p><? echo $row[csf("file_no")]; ?></p></td>
								<td width="100"><p><? echo $row[csf("grouping")]; ?></p></td>
								<td width="130"><p><? echo $garments_item[$row[csf("item_number_id")]]; ?></p></td>
								<td width="70"><p><? if($row[csf("production_date")]!="" && $row[csf("production_date")]!="0000-00-00") echo change_date_format($row[csf("production_date")]); ?></p></td>

								<td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'Cutting Info','cutting_popup');" ><? echo $row[csf("cutting_qnty")]; ?></a></td>
								<td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'Printing Issue Info','printing_issue_popup');" ><? echo $row[csf("print_input_qnty")]; ?></a></td>
								<td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'Priniting Receive Info','printing_receive_popup');" ><? echo $row[csf("print_output_qnty")]; ?></a></td>
								<td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'Embroidery Issue Info','embroi_issue_popup');" ><? echo $row[csf("embro_input_qnty")]; ?></a></td>
								<td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'Embroidery Receive Info','embroi_receive_popup');" ><? echo $row[csf("embro_output_qnty")];  ?></a></td>
								<td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'Wash Issue Info','wash_issue_popup');" ><? echo $row[csf("wash_input_qnty")]; ?></a></td>
								<td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'Wash Receive Info','wash_receive_popup');" ><? echo $row[csf("wash_output_qnty")]; ?></a></td>
								<td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'Special Works Issue Info','sp_issue_popup');" ><? echo $row[csf("sp_input_qnty")]; ?></a></td>
								<td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'Special Works Receive Info','sp_receive_popup');" ><? echo $row[csf("sp_output_qnty")]; ?></a> </td>
								<td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id.'__0';?>,'1','4','sewingQnty_popup');" ><? echo $row[csf("sewing_inhouse_input_qnty")]; ?></a></td>
								<td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id.'__0';?>,'3','4','sewingQnty_popup');" ><? echo $row[csf("sewing_outbound_input_qnty")]; ?></a></td>
								<td width="80" align="right"><? $sewing_input_total=$row[csf("sewing_inhouse_input_qnty")]+$row[csf("sewing_outbound_input_qnty")]; echo $sewing_input_total; ?></td>
								<td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id.'__0';?>,'1','5','sewingQnty_popup');" ><? echo $row[csf("sewing_inhouse_output_qnty")]; ?></a></td>
								<td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id.'__0';?>,'3','5','sewingQnty_popup');" ><? echo $row[csf("sewing_outbound_output_qnty")]; ?></a></td>
								<td width="80" align="right"><? $sewing_output_total=$row[csf("sewing_inhouse_output_qnty")]+$row[csf("sewing_outbound_output_qnty")]; echo $sewing_output_total; ?></td>
								<td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'1','0','ironQnty_popup');" ><? echo $row[csf("iron_inhouse_qnty")]; ?></a></td>
								<td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'3','0','ironQnty_popup');" ><? echo $row[csf("iron_outbound_qnty")]; ?></a></td>
								<td width="80" align="right"><? $iron_qty_total=$row[csf("iron_inhouse_qnty")]+$row[csf("iron_outbound_qnty")]; echo $iron_qty_total; ?></td>
								<td width="80" align="right"><? echo $row[csf("re_iron_qnty")]; ?></td>
								<td width="80" align="right"><? echo $row[csf("finish_inhouse_qnty")]; ?></td>
								<td width="80" align="right"><? echo $row[csf("finish_outbound_qnty")]; ?></td>
								<td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'0','0','finishQnty_popup');" ><? $finishing_qty=$row[csf("finish_inhouse_qnty")]+$row[csf("finish_outbound_qnty")]; echo $finishing_qty; ?></a></td>
								<td width="80" align="right"><? echo $row[csf("carton_qty")]; ?></td>
								<? $prod_dzn=0;
								if($sewing_output_total!=0)
								{
									$prod_dzn=($sewing_output_total)/12;
								}
								?>
								<td width="80" align="right"><? if($prod_dzn!=0) echo number_format($prod_dzn,2); else echo "0"; ?></td>
								<? //$cm_per=0; $cm_per=$cm_per_dzn[$rows[csf("job_no_mst")]] ; ?>
								<td width="80" align="right" ><? $reject_Qty=$rejFinish_qty+$rejSewing_qty; echo $reject_Qty; ?></td>

								<td width="">
									<a href="##"  onclick="openmypage_remark(<? echo $po_id;?>,'date_wise_production_report');" > Veiw </a>
								</td>
							</tr>
							<?
							$month_cutting_qnty+=$row[csf("cutting_qnty")];
							$month_print_input_qnty+=$row[csf("print_input_qnty")];
							$month_print_output_qnty+=$row[csf("print_output_qnty")];
							$month_embro_input_qnty+=$row[csf("embro_input_qnty")];
							$month_embro_output_qnty+=$row[csf("embro_output_qnty")];
							$month_wash_input_qnty+=$row[csf("wash_input_qnty")];
							$month_wash_output_qnty+=$row[csf("wash_output_qnty")];
							$month_sp_input_qnty+=$row[csf("sp_input_qnty")];
							$month_sp_output_qnty+=$row[csf("sp_output_qnty")];
							$month_sewing_inhouse_input_qnty+=$row[csf("sewing_inhouse_input_qnty")];
							$month_sewing_outbound_input_qnty+=$row[csf("sewing_outbound_input_qnty")];
							$month_sewing_input_total+=$sewing_input_total;
							$month_sewing_inhouse_output_qnty+=$row[csf("sewing_inhouse_output_qnty")];
							$month_sewing_outbound_output_qnty+=$row[csf("sewing_outbound_output_qnty")];
							$month_sewing_input_total+=$sewing_input_total;
							$month_iron_inhouse_qnty+=$row[csf("iron_inhouse_qnty")];
							$month_iron_outbound_qnty+=$row[csf("iron_outbound_qnty")];
							$month_iron_qty_total+=$iron_qty_total;
							$month_re_iron_qnty+=$row[csf("re_iron_qnty")];
							$month_finish_inhouse_qnty+=$row[csf("finish_inhouse_qnty")];
							$month_finish_outbound_qnty+=$row[csf("finish_outbound_qnty")];
							$month_finishing_qty+=$finishing_qty;
							$month_carton_qty+=$row[csf("carton_qty")];
							$month_prod_dzn+=$prod_dzn;
							$month_rejFinish_qty+=$rejFinish_qty;
							$month_rejSewing_qty+=$rejSewing_qty;
							$month_reject_Qty+=$reject_Qty;


							$tot_cutting_qty+=$row[csf("cutting_qnty")];
							$tot_print_qty+=$row[csf("print_input_qnty")];
							$tot_printRec_qty+=$row[csf("print_output_qnty")];
							$tot_emb_qty+=$row[csf("embro_input_qnty")];
							$tot_embRec_qty+=$row[csf("embro_output_qnty")];
							$tot_wash_qty+=$row[csf("wash_input_qnty")];
							$tot_washRec_qty+=$row[csf("wash_output_qnty")];
							$tot_special_qty+=$row[csf("sp_input_qnty")];
							$tot_specialRec_qty+=$row[csf("sp_output_qnty")];
							$tot_sewIn_inQty+=$row[csf("sewing_inhouse_input_qnty")];
							$tot_sewIn_outQty+=$row[csf("sewing_outbound_input_qnty")];
							$tot_sewing_input+=$sewing_input_total;
							$tot_sewOut_inQty+=$row[csf("sewing_inhouse_output_qnty")];
							$tot_sewOut_outQty+=$row[csf("sewing_outbound_output_qnty")];
							$tot_sewing_output+=$sewing_output_total;
							$tot_ironIn_qty+=$row[csf("iron_inhouse_qnty")];
							$tot_ironOut_qty+=$row[csf("iron_outbound_qnty")];
							$tot_iron_qty+=$iron_qty_total;
							$tot_reIron_qty+=$row[csf("re_iron_qnty")];
							$tot_finishIn_qty+=$row[csf("finish_inhouse_qnty")];
							$tot_finishOut_qty+=$row[csf("finish_outbound_qnty")];
							$tot_finishing_qty+=$finishing_qty;
							$tot_carton_qty+=$row[csf("carton_qty")];
							$total_prod_dzn+=$prod_dzn;
							$tot_rejFinish_qty+=$rejFinish_qty;
							$tot_rejSewing_qty+=$rejSewing_qty;
							$tot_reject_Qty+=$reject_Qty;
							$i++;$b++;
						}
					}
					//unset($date_sql_result);
				?>
                	<tr bgcolor="#CCCCCC">
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td align="right" style="font-weight:bold;">Month Total:</td>
                        <td align="right"><? echo $month_cutting_qnty; ?></td>
                        <td align="right"><? echo $month_print_input_qnty; ?></td>
                        <td align="right"><? echo $month_print_output_qnty; ?></td>
                        <td align="right"><? echo $month_embro_input_qnty; ?></td>
                        <td align="right"><? echo $month_embro_output_qnty;  ?></td>
                        <td align="right"><? echo $month_wash_input_qnty; ?></td>
                        <td align="right"><? echo $month_wash_output_qnty; ?></td>
                        <td align="right"><? echo $month_sp_input_qnty; ?></td>
                        <td align="right"><? echo $month_sp_output_qnty; ?> </td>
                        <td align="right"><? echo $month_sewing_inhouse_input_qnty; ?></td>
                        <td align="right"><? echo $month_sewing_outbound_input_qnty; ?></td>
                        <td align="right"><? $month_sewing_input_total; ?></td>
                        <td align="right"><? echo $month_sewing_inhouse_output_qnty; ?></td>
                        <td align="right"><? echo $month_sewing_outbound_output_qnty; ?></td>
                        <td align="right"><? echo $month_sewing_output_total; ?></td>
                        <td align="right"><? echo $month_iron_inhouse_qnty; ?></td>
                        <td align="right"><? echo $month_iron_outbound_qnty; ?></td>
                        <td align="right"><? echo $month_iron_qty_total; ?></td>
                        <td align="right"><? echo $month_re_iron_qnty; ?></td>
                        <td align="right"><? echo $month_finish_inhouse_qnty; ?></td>
                        <td align="right"><? echo $month_finish_outbound_qnty; ?></td>
                        <td align="right"><? echo $month_finishing_qty; ?></td>
                        <td align="right"><? echo $month_carton_qty; ?></td>
                        <td align="right"><? echo number_format($month_prod_dzn,2); ?></td>
                        <td align="right" ><? echo $month_reject_Qty; ?></td>
                        <td>&nbsp;</td>
                    </tr>
				</table>
				<table width="3180" cellspacing="0" border="1" class="tbl_bottom" rules="all" id="report_table_footer" >
					<tr>
						<td width="30">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="130">&nbsp;</td>
                        <td width="70">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="130">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="130">&nbsp;</td>
						<td width="70">Total</td>
						<td width="80" align="right" id="total_cut_td" ><? echo $tot_cutting_qty;?></td>
						<td width="80" align="right" id="total_printissue_td"><? echo $tot_print_qty; ?></td>
						<td width="80" align="right" id="total_printrcv_td"><?  echo $tot_printRec_qty; ?></td>
						<td width="80" align="right" id="total_emb_iss"><? echo $tot_emb_qty; ?></td>
						<td width="80" align="right" id="total_emb_re"><? echo $tot_embRec_qty; ?></td>
						<td width="80" align="right" id="total_wash_iss"><? echo $tot_wash_qty; ?></td>
						<td width="80" align="right" id="total_wash_re"><? echo $tot_washRec_qty; ?></td>
						<td width="80" align="right" id="total_sp_iss"><? echo $tot_special_qty; ?></td>
						<td width="80" align="right" id="total_sp_re"><? echo $tot_specialRec_qty; ?></td>
						<td width="80" align="right" id="total_sewin_inhouse_td"><? echo $tot_sewIn_inQty; ?></td>
						<td width="80" align="right" id="total_sewin_outbound_td"><? echo $tot_sewIn_outQty; ?></td>
						<td width="80" align="right" id="total_sewin_td"><? echo $tot_sewing_input; ?></td>
						<td width="80" align="right" id="total_sewout_inhouse_td"><? echo $tot_sewOut_inQty; ?></td>
						<td width="80" align="right" id="total_sewout_outbound_td"><? echo $tot_sewOut_outQty; ?></td>
						<td width="80" align="right" id="total_sewout_td"><? echo $tot_sewing_output; ?></td>
						<td width="80" align="right" id="total_iron_in_td"><?  echo $tot_ironIn_qty; ?></td>
						<td width="80" align="right" id="total_iron_out_td"><?  echo $tot_ironOut_qty; ?></td>
						<td width="80" align="right" id="total_iron_td"><?  echo $tot_iron_qty; ?></td>
						<td width="80" align="right" id="total_re_iron_td"><?  echo $tot_reIron_qty; ?></td>
						<td width="80" align="right" id="total_finishin_td"><? echo $tot_finishIn_qty; ?></td>
						<td width="80" align="right" id="total_finishout_td"><? echo $tot_finishOut_qty; ?></td>
						<td width="80" align="right" id="total_finish_td"><? echo $tot_finishing_qty; ?></td>
						<td width="80" align="right" id="total_carton_td"><? echo $tot_carton_qty; ?></td>
						<td width="80" align="right" id="total_prod_dzn_td"><?  echo number_format($total_prod_dzn,2); ?></td>
						<td width="80" align="right" id="total_rej_value_td"><? echo number_format($tot_reject_Qty,2); ?></td >
						<td>&nbsp;</td>
					 </tr>
				</table>
			</div>


			<?
		}// end if condition of type //-------------------------------------------END Show Date Wise------------------------
		else if($type==2)//-------------------------------------------Show Date Location Floor & Line Wise------------------------
		{
		?>
			<h5 style="width:600px; float:left; background-color:#FCF"><strong>Production-Regular Order</strong></h5>
			<table width="3600px" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
			   <thead>
				   <tr>
						<th width="30">Sl.</th>
						<th width="100">Working Factory</th>
	                    <th width="100">Job Prefix</th>
						<th width="100">Job No</th>
						<th width="130">Order Number</th>
	                    <th width="70">Production Date</th>
						<th width="100">Buyer Name</th>
						<th width="130">Style Name</th>
						<th width="100">File No</th>
						<th width="100">Internal Ref</th>
						<th width="130">Item Name</th>
						<th width="70">Production Date</th>
						<th width="100">Status</th>
						<th width="100">Location</th>
						<th width="100">Floor</th>
						<th width="100">Sewing Line No</th>
						<th width="80">Cutting</th>
						<th width="80">Sent to prnt</th>
						<th width="80">Rcv prn/Emb</th>

						<th width="80">Sent to Emb</th>
						<th width="80">Rcv Emb</th>
						<th width="80">Sent to Wash</th>
						<th width="80">Rcv Wash</th>
						<th width="80">Sent to Sp. Works</th>
						<th width="80">Rcv Sp. Works</th>

						<th width="80">Sewing In (Inhouse)</th>
						<th width="80">Sewing In (Out-bound)</th>
						<th width="80">Total Sewing Input</th>
						<th width="80">Sewing Out (Inhouse)</th>
						<th width="80">Sewing Out (Out-bound)</th>
						<th width="80">Total Sewing Out</th>

						<th width="80">Iron Qty (Inhouse)</th>
						<th width="80">Iron Qty (Out-bound)</th>
						<th width="80">Iron Qty</th>
						<th width="80">Re-Iron Qty </th>
						<th width="80">Finish Qty (Inhouse)</th>
						<th width="80">Finish Qty (Out-bound)</th>
						<th width="80">Total Finish Qty</th>
						<th width="80">Today Carton</th>
						<th width="80">Prod/Dzn</th>
						<th width="">Remarks</th>
				   </tr>
				</thead>
			</table>
			<div style="max-height:425px; overflow-y:scroll; width:3620px" id="scroll_body">
				<table cellspacing="0" border="1" class="rpt_table"  width="3600px" rules="all" id="table_body" >
				<?
				$i=1;$p=1;
				foreach($sql_dtls_res as $row)
				{
					if($row[csf("buyer_name")] !="")
					{
						$line_id=''; $buyer_name=''; $job_no=''; $po_no=''; $style_ref=''; $ref_no=''; $file_no=''; $company_id=''; $prod_source=''; $resource_allo='';
						if($row[csf("sewing_line")]=="") $line_id=0;
						else $line_id=$row[csf("sewing_line")];
						$buyer_name=$row[csf("buyer_name")];
						$job_no=$row[csf("job_no_prefix_num")];
						$full_job_no=$row[csf("job_no")];
						$po_no=$row[csf("po_number")];
						$style_ref=$row[csf("style_ref_no")];
						$ref_no=$row[csf("grouping")];
						$file_no=$row[csf("file_no")];
						$company_id=$row[csf("company_name")];
						$prod_source=$row[csf("production_source")];
						$resource_allo=$row[csf("prod_reso_allo")];

						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$cutting_qty=$print_qty=$printRec_qty=$emb_qty=$embRec_qty=$wash_qty=$washRec_qty=$special_qty=$specialRec_qty=$sewIn_inQty=$sewIn_outQty=$sewing_input_total=$sewOut_inQty=$sewOut_outQty=$sewing_output_total=$ironIn_qty=$ironOut_qty=$iron_qty_total=$reIron_qty=$finishIn_qty=$finishOut_qty=$finishing_qty=$carton_qty=$rejFinish_qty=$rejSewing_qty=$reject_Qty=0;

						$cutting_qty=$row[csf("cutting_qnty")];
						$print_qty=$row[csf("print_input_qnty")];
						$printRec_qty=$row[csf("print_output_qnty")];
						$emb_qty=$row[csf("embro_input_qnty")];
						$embRec_qty=$row[csf("embro_output_qnty")];
						$wash_qty=$row[csf("wash_input_qnty")];
						$washRec_qty=$row[csf("wash_output_qnty")];
						$special_qty=$row[csf("sp_input_qnty")];
						$specialRec_qty=$row[csf("sp_output_qnty")];

						$sewIn_inQty=$row[csf("sewing_inhouse_input_qnty")];
						$sewIn_outQty=$row[csf("sewing_outbound_input_qnty")];
						$sewOut_inQty=$row[csf("sewing_inhouse_output_qnty")];
						$sewOut_outQty=$row[csf("sewing_outbound_output_qnty")];

						$ironIn_qty=$row[csf("iron_inhouse_qnty")];
						$ironOut_qty=$row[csf("iron_outbound_qnty")];
						$reIron_qty=$row[csf("re_iron_qnty")];
						$finishIn_qty=$row[csf("finish_inhouse_qnty")];
						$finishOut_qty=$row[csf("finish_outbound_qnty")];
						$carton_qty=$row[csf("carton_qty")];
						$rejFinish_qty=$row[csf("finish_reject_qnty")];
						$rejSewing_qty=$row[csf("sewing_reject_qnty")];

						$sewing_line='';
						if($resource_allo==1)
						{
							$line_number=explode(",",$prod_reso_arr[$line_id]);
							foreach($line_number as $val)
							{
								if($sewing_line=='') $sewing_line=$line_library[$val]; else $sewing_line.=", ".$line_library[$val];
							}
						}
						else $sewing_line=$line_library[$line_id];
						if($line_id!="") $swing_line_id=$line_id; else $swing_line_id=0;

						$po_id=$row[csf("id")];$prod_date=$row[csf("production_date")];$item_id=$row[csf("item_number_id")];$location_id=$row[csf("location")];
						$floor_id=$row[csf("floor_id")]; $shipment_date=$row[csf("pub_shipment_date")];


						if($month_check[date("Y-m",strtotime($row[csf("pub_shipment_date")]))]=="")
						{
							$month_check[date("Y-m",strtotime($row[csf("pub_shipment_date")]))]=date("Y-m",strtotime($row[csf("pub_shipment_date")]));
							$month=date("F",strtotime($row[csf("pub_shipment_date")]));
							$year=date("Y",strtotime($row[csf("pub_shipment_date")]));
							if($p==1)
							{
								?>
								 <tr bgcolor="#FFFFCC">
									<td colspan="41" style="font-size:18px; font-weight:bold;"><? echo $month." ".$year; ?></td>
								</tr>
								<?
							}
							if($p!=1)
							{

								?>

								<tr bgcolor="#CCCCCC">
									<td>&nbsp;</td>
	                                <td>&nbsp;</td>
	                                <td>&nbsp;</td>
	                                <td>&nbsp;</td>
	                                <td>&nbsp;</td>
	                                <td>&nbsp;</td>
	                                <td>&nbsp;</td>
	                                <td>&nbsp;</td>
	                                <td>&nbsp;</td>
	                                <td>&nbsp;</td>
	                                <td>&nbsp;</td>
	                                <td>&nbsp;</td>
	                                <td>&nbsp;</td>
	                                <td>&nbsp;</td>
	                                <td>&nbsp;</td>
	                                <td align="right">Month Total:</td>
	                                <td align="right"><? echo $month_cutting_qty; ?></td>
	                                <td align="right"><? echo $month_print_qty; ?></td>
	                                <td align="right"><? echo $month_printRec_qty; ?></td>
	                                <td align="right"><? echo $month_emb_qty; ?></td>
	                                <td align="right"><? echo $month_embRec_qty; ?></td>
	                                <td align="right"><? echo $month_wash_qty; ?></td>
	                                <td align="right"><? echo $month_washRec_qty; ?></td>
	                                <td align="right"><? echo $month_special_qty; ?></td>
	                                <td align="right"><? echo $month_specialRec_qty; ?></td>
	                                <td align="right"><? echo $month_sewIn_inQty; ?></td>
	                                <td align="right"><? echo $month_sewIn_outQty; ?></td>
	                                <td align="right"><? echo $month_sewing_input_total; ?></td>
	                                <td align="right"><? echo $month_sewOut_inQty; ?></td>
	                                <td align="right"><? echo $month_sewOut_outQty; ?></td>
	                                <td align="right"><? echo $month_sewing_output_total; ?></td>
	                                <td align="right"><? echo $month_ironIn_qty; ?></td>
	                                <td align="right"><? echo $month_ironOut_qty; ?></td>
	                                <td align="right"><? echo $month_iron_qty_total; ?></td>
	                                <td align="right"><? echo $month_reIron_qty; ?></td>
	                                <td align="right"><? echo $month_finishIn_qty; ?></td>
	                                <td align="right"><? echo $month_finishOut_qty; ?></td>
	                                <td align="right"><? echo $month_finishing_qty; ?></td>
	                                <td align="right"><? echo $month_carton_qty; ?></td>
	                                <td align="right"><? echo number_format($month_prod_dzn,2); ?></td>
	                                <td>&nbsp;</td>
								</tr>
								 <tr bgcolor="#FFFFCC">
									<td colspan="41" style="font-size:18px; font-weight:bold;"><? echo $month." ".$year; ?></td>
								</tr>
								<?
								$month_cutting_qty=$month_print_qty=$month_printRec_qty=$month_emb_qty=$month_embRec_qty=$month_wash_qty=$month_washRec_qty=$month_special_qty=$month_specialRec_qty=$month_sewIn_inQty=$month_sewIn_outQty=$month_sewing_input_total=$month_sewOut_inQty=$month_sewOut_outQty=$month_sewing_output_total=$month_ironIn_qty=$month_ironOut_qty=$month_iron_qty_total=$month_reIron_qty=$month_finishIn_qty=$month_finishOut_qty=$month_finishing_qty=$month_carton_qty=$month_prod_dzn=$month_reject_Qty=0;
							}
							$p++;
						}


						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $b; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $b; ?>">
							<td width="30" align="center"><? echo $i;?></td>
							<td width="100"><p><? echo $company_short_library[$company_id]; ?></p></td>
	                        <td width="100"><p><? echo $job_no;?></p></td>
							<td width="100" title="<? echo $job_no ; ?>"><p><? echo $full_job_no;?></p></td>
							<td width="130"><p><a href="##" onclick="openmypage_order(<? echo $po_id;?>,<? echo $item_id; ?>,'orderQnty_popup');" ><? echo $po_no; ?></a></p></td>
	                        <td width="70" align="center"><p><? echo change_date_format($shipment_date); ?></p></td>
							<td width="100"><p><? echo $buyer_short_library[$buyer_name]; ?></p></td>
							<td width="130"><p><? echo $style_ref; ?></p></td>
							<td width="100"><p><? echo $file_no; ?></p></td>
							<td width="100"><p><? echo $ref_no; ?></p></td>
							<td width="130"><p><? echo $garments_item[$item_id]; ?></p></td>
							<td width="70" align="center"><p><? echo change_date_format($prod_date); ?></p></td>
							<td width="100"><p><? echo $knitting_source[$prod_source]; ?></p></td>

							<td width="100"><p><? echo $location_library[$location_id]; ?></p></td>
							<td width="100"><p><? echo $floor_library[$floor_id]; ?></p></td>
							<td width="100" align="center"><p><? echo $sewing_line; ?></p></td>

							<td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location_id;?>,<? echo $floor_id;?>,<? echo $swing_line_id;?>,'Cutting Info','cutting_popup_location');" ><? echo $cutting_qty; ?></a></td>
							<td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location_id;?>,<? echo $floor_id;?>,'<? echo $line_id;?>','Printing Issue Info','printing_issue_popup_location');" ><? echo $print_qty; ?></a></td>
							<td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location_id;?>,<? echo $floor_id;?>,'<? echo $line_id;?>','Printing Receive Info','printing_receive_popup_location');" ><? echo $printRec_qty; ?></a></td>
							<td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location_id;?>,<? echo $floor_id;?>,'<? echo $line_id;?>','Embroidery Issue Info','embroi_issue_popup_location');" ><? echo $emb_qty; ?></a></td>
							<td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location_id;?>,<? echo $floor_id;?>,'<? echo $line_id;?>','Embroidery Receive Info','embroi_receive_popup_location');" ><? echo $embRec_qty; ?></a></td>
							<td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location_id;?>,<? echo $floor_id;?>,'<? echo $line_id;?>','Wash Issue Info','wash_issue_popup_location');" ><? echo $wash_qty; ?></a></td>
							<td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location_id;?>,<? echo $floor_id;?>,'<? echo $line_id;?>','Wash Receive Info','wash_receive_popup_location');" ><? echo $washRec_qty; ?></a></td>
							<td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location_id;?>,<? echo $floor_id;?>,'<? echo $line_id;?>','Spetial Work Info','sp_issue_popup_location');" ><? echo $special_qty; ?></a></td>
							<td width="80" align="right"><a href="##" onClick="openmypage_popup_location(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,<? echo $location_id;?>,<? echo $floor_id;?>,'<? echo $line_id;?>','Spetial Work Info','sp_receive_popup_location');" ><? echo $specialRec_qty; ?></a></td>

							<td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__0';?>','1','4','sewingQnty_popup');" ><? echo $sewIn_inQty; ?></a></td>
							<td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__0';?>','3','4','sewingQnty_popup');" ><? echo $sewIn_outQty; ?></a></td>
							<td width="80" align="right"><? $sewing_input_total=$sewIn_inQty+$sewIn_outQty; echo $sewing_input_total; ?></td>
							<td width="80" align="right"><a href="##" onclick="openmypage_sew_output2(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__0';?>', '1','5','sewingQnty_popup',<? echo $location_id;?>,<? echo $floor_id;?>,'<? echo $line_id; ?>');" ><? echo $sewOut_inQty; ?></a></td>
							<td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>','<? echo $item_id.'__0';?>', '3','5','sewingQnty_popup');" ><? echo $sewOut_outQty; ?></a></td>
							<td width="80" align="right"><? $sewing_output_total=$sewOut_inQty+$sewOut_outQty; echo $sewing_output_total; ?></td>
							<td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'1','0','ironQnty_popup');" ><? echo $ironIn_qty; ?></a></td>
							<td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'3','0','ironQnty_popup');" ><? echo $ironOut_qty; ?></a></td>
							<td width="80" align="right"><? $iron_qty_total=$ironIn_qty+$ironOut_qty; echo $iron_qty_total; ?></a></td>
							<td width="80" align="right"><? echo $reIron_qty; ?></td>
							<td width="80" align="right"><? echo $finishIn_qty; ?></td>
							<td width="80" align="right"><? echo $finishOut_qty; ?></td>
							<td width="80" align="right"><a href="##" onclick="openmypage_sew_output(<? echo $po_id; ?>,'<? echo $prod_date; ?>',<? echo $item_id;?>,'0','0','finishQnty_popup');" ><? $finishing_qty=$finishIn_qty+$finishOut_qty; echo $finishing_qty; ?></a></td>
							<td width="80" align="right"><? echo $carton_qty; ?></td>
							<? $prod_dzn=0; $prod_dzn=$sewing_output_total / 12 ;  ?>
							<td width="80" align="right"><? if($prod_dzn!=0) echo number_format($prod_dzn,2); else echo "0"; ?></td>
							<td><a href="##"  onclick="openmypage_remark(<? echo $po_id;?>,'date_wise_production_report');" > Veiw </a></td>
					</tr>
					<?

						$month_cutting_qty+=$cutting_qty;
						$month_print_qty+=$print_qty;
						$month_printRec_qty+=$printRec_qty;
						$month_emb_qty+=$emb_qty;
						$month_embRec_qty+=$embRec_qty;
						$month_wash_qty+=$wash_qty;
						$month_washRec_qty+=$washRec_qty;
						$month_special_qty+=$special_qty;
						$month_specialRec_qty+=$specialRec_qty;
						$month_sewIn_inQty+=$sewIn_inQty;
						$month_sewIn_outQty+=$sewIn_outQty;
						$month_sewing_input+=$sewing_input_total;
						$month_sewOut_inQty+=$sewOut_inQty;
						$month_sewOut_outQty+=$sewOut_outQty;
						$month_sewing_output+=$sewing_output_total;
						$month_ironIn_qty+=$ironIn_qty;
						$month_ironOut_qty+=$ironOut_qty;
						$month_iron_qty+=$iron_qty_total;
						$month_reIron_qty+=$reIron_qty;
						$month_finishIn_qty+=$finishIn_qty;
						$month_finishOut_qty+=$finishOut_qty;
						$month_finishing_qty+=$finishing_qty;
						$month_carton_qty+=$carton_qty;
						$month_rejFinish_qty+=$rejFinish_qty;
						$month_rejSewing_qty+=$rejSewing_qty;
						$month_reject_Qty+=$reject_Qty;
						$month_prod_dzn+=$prod_dzn;



						$tot_cutting_qty+=$cutting_qty;
						$tot_print_qty+=$print_qty;
						$tot_printRec_qty+=$printRec_qty;
						$tot_emb_qty+=$emb_qty;
						$tot_embRec_qty+=$embRec_qty;
						$tot_wash_qty+=$wash_qty;
						$tot_washRec_qty+=$washRec_qty;
						$tot_special_qty+=$special_qty;
						$tot_specialRec_qty+=$specialRec_qty;
						$tot_sewIn_inQty+=$sewIn_inQty;
						$tot_sewIn_outQty+=$sewIn_outQty;
						$tot_sewing_input+=$sewing_input_total;
						$tot_sewOut_inQty+=$sewOut_inQty;
						$tot_sewOut_outQty+=$sewOut_outQty;
						$tot_sewing_output+=$sewing_output_total;
						$tot_ironIn_qty+=$ironIn_qty;
						$tot_ironOut_qty+=$ironOut_qty;
						$tot_iron_qty+=$iron_qty_total;
						$tot_reIron_qty+=$reIron_qty;
						$tot_finishIn_qty+=$finishIn_qty;
						$tot_finishOut_qty+=$finishOut_qty;
						$tot_finishing_qty+=$finishing_qty;
						$tot_carton_qty+=$carton_qty;
						$tot_rejFinish_qty+=$rejFinish_qty;
						$tot_rejSewing_qty+=$rejSewing_qty;
						$tot_reject_Qty+=$reject_Qty;
						$total_prod_dzn+=$prod_dzn;
						$i++;$b++;
					}
				}
				//end foreach 1st

			 ?>
                <tr bgcolor="#CCCCCC">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Month Total:</td>
                    <td align="right"><? echo $month_cutting_qty; ?></td>
                    <td align="right"><? echo $month_print_qty; ?></td>
                    <td align="right"><? echo $month_printRec_qty; ?></td>
                    <td align="right"><? echo $month_emb_qty; ?></td>
                    <td align="right"><? echo $month_embRec_qty; ?></td>
                    <td align="right"><? echo $month_wash_qty; ?></td>
                    <td align="right"><? echo $month_washRec_qty; ?></td>
                    <td align="right"><? echo $month_special_qty; ?></td>
                    <td align="right"><? echo $month_specialRec_qty; ?></td>

                    <td align="right"><? echo $month_sewIn_inQty; ?></td>
                    <td align="right"><? echo $month_sewIn_outQty; ?></td>
                    <td align="right"><? echo $month_sewing_input_total; ?></td>
                    <td align="right"><? echo $month_sewOut_inQty; ?></td>
                    <td align="right"><? echo $month_sewOut_outQty; ?></td>
                    <td align="right"><? echo $month_sewing_output_total; ?></td>
                    <td align="right"><? echo $month_ironIn_qty; ?></td>
                    <td align="right"><? echo $month_ironOut_qty; ?></td>
                    <td width="80" align="right"><? echo $month_iron_qty_total; ?></td>

                    <td align="right"><? echo $month_reIron_qty; ?></td>
                    <td align="right"><? echo $month_finishIn_qty; ?></td>
                    <td align="right"><? echo $month_finishOut_qty; ?></td>
                    <td align="right"><? echo $month_finishing_qty; ?></td>
                    <td align="right"><? echo $month_carton_qty; ?></td>
                    <td align="right"><? echo number_format($month_prod_dzn,2); ?></td>
                    <td>&nbsp;</td>
                </tr>
			</table>
			<table width="3600" cellspacing="0" border="1" class="tbl_bottom" rules="all" id="report_table_footer" >

					<tr>
						<td width="30">&nbsp;</td>
						<td width="100">&nbsp;</td>
                        <td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="130">&nbsp;</td>
                        <td width="70">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="130">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="130">&nbsp;</td>
						<td width="70">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">Total</td>
						<td width="80" align="right" id="total_cut_td"><? echo $tot_cutting_qty;?></td>
						<td width="80" align="right" id="total_printissue_td"><? echo $tot_print_qty; ?> </td>
						<td width="80" align="right" id="total_printrcv_td"><? echo $tot_printRec_qty;  ?>  </td>
						<td width="80" align="right" id="total_emb_iss"><? echo $tot_emb_qty; ?> </td>
						<td width="80" align="right" id="total_emb_re"><? echo $tot_embRec_qty;  ?>  </td>
						<td width="80" align="right" id="total_wash_iss"><? echo $tot_wash_qty; ?> </td>
						<td width="80" align="right" id="total_wash_re"><? echo $tot_washRec_qty;  ?>  </td>
						<td width="80" align="right" id="total_sp_iss"><? echo $tot_special_qty; ?> </td>
						<td width="80" align="right" id="total_sp_re"><? echo $tot_specialRec_qty;  ?>  </td>
						<td width="80" align="right" id="total_sewin_inhouse_td"><? echo $tot_sewIn_inQty; ?></td>
						<td width="80" align="right" id="total_sewin_outbound_td"><? echo $tot_sewIn_outQty; ?></td>
						<td width="80" align="right" id="total_sewin_td"><? echo $tot_sewing_input;  ?> </th>
						<td width="80" align="right" id="total_sewout_inhouse_td"><? echo $tot_sewOut_inQty; ?></td>
						<td width="80" align="right" id="total_sewout_outbound_td"><? echo $tot_sewOut_outQty; ?></td>
						<td width="80" align="right" id="total_sewout_td"><? echo $tot_sewing_output; ?> </td>

						<td width="80" align="right" id="total_iron_in_td"><?  echo $tot_ironIn_qty; ?></td>
						<td width="80" align="right" id="total_iron_out_td"><?  echo $tot_ironOut_qty; ?></td>
						<td width="80" align="right" id="total_iron_td"><?  echo $tot_iron_qty; ?></td>
						<td width="80" align="right" id="total_re_iron_td"><? echo $tot_reIron_qty; ?></td>
						<td width="80" align="right" id="total_finishin_td"><? echo $tot_finishIn_qty; ?></td>
						<td width="80" align="right" id="total_finishout_td"><? echo $tot_finishOut_qty; ?></td>
						<td width="80" align="right" id="total_finish_td"><?  echo $tot_finishing_qty; ?></td>
						<td width="80" align="right" id="total_carton_td"><? echo $tot_carton_qty; ?></td>
						<td width="80" align="right" id="total_prod_dzn_td"><?  echo number_format($total_prod_dzn,2); ?></td>
						<td>&nbsp;</td>
					 </tr>

			</table>


		</div>
		<?
		}// end if condition of type

		if(str_replace("'","",trim($cbo_subcon))==2) //yes
		{
			$fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
			$toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );

			if(str_replace("'","",$cbo_floor)==0) $sub_floor_name="";else $sub_floor_name=" and b.floor_id in($cbo_floor)";
			if ($location==0) $sub_location_cond=""; else $sub_location_cond=" and b.location_id in(".$location.") ";
			?>

				<h5 style="width:800px; float:left; background-color:#FCF"><strong>Production-Subcontract Order (Inbound) Details</strong></h5>

					<table width="1500" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_2">
						<thead>
							<tr>
								<th width="30">Sl.</th>
								<th width="100">Working Factory</th>
	                            <th width="100">Job Prefix</th>
								<th width="100">Job No</th>
								<th width="130">Order No</th>
								<th width="100">Buyer </th>
								<th width="130">Style </th>
								<th width="130">Item Name</th>
								<th width="75">Production Date</th>
								<th width="100">Sewing Line</th>
								<th width="90">Cutting</th>
								<th width="90">Sewing Output</th>
								<th width="90">Iron Output</th>
								<th width="90">Gmts. Finishing</th>
								<th width="">Remarks</th>
							</tr>
						</thead>
					</table>
				<div style="max-height:300px; overflow-y:scroll; width:1520px" id="scroll_body2">
					<table border="1"   class="rpt_table"  width="1500" rules="all" id="sub_list_view" >
						  <?
							$item_id_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');

							$production_array=array();
							if($db_type==0)
							{
								$prod_sql= "SELECT c.order_id, c.production_date,
									sum(CASE WHEN c.production_type ='1' THEN  c.production_qnty  ELSE 0 END) AS cutting_qnty,
									sum(CASE WHEN c.production_type ='2' THEN  c.production_qnty  ELSE 0 END) AS sewingout_qnty,
									sum(CASE WHEN c.production_type ='3' THEN  c.production_qnty  ELSE 0 END) AS ironout_qnty,
									sum(CASE WHEN c.production_type ='4' THEN  c.production_qnty  ELSE 0 END) AS gmts_fin_qnty
								from
									subcon_gmts_prod_dtls c
								where c.status_active=1 and c.is_deleted=0 and c.production_date between $txt_date_from and $txt_date_to group by c.order_id, c.production_date";
							}
							else
							{
								 $prod_sql= "SELECT c.order_id, c.production_date,
									NVL(sum(CASE WHEN c.production_type ='1' THEN  c.production_qnty  ELSE 0 END),0) AS cutting_qnty,
									NVL(sum(CASE WHEN c.production_type ='2' THEN  c.production_qnty  ELSE 0 END),0) AS sewingout_qnty,
									NVL(sum(CASE WHEN c.production_type ='3' THEN  c.production_qnty  ELSE 0 END),0) AS ironout_qnty,
									NVL(sum(CASE WHEN c.production_type ='4' THEN  c.production_qnty  ELSE 0 END),0) AS gmts_fin_qnty
								from
									subcon_gmts_prod_dtls c
								where c.status_active=1 and c.is_deleted=0 and c.production_date between $txt_date_from and $txt_date_to group by c.order_id, c.production_date";
							}
							$prod_sql_result= sql_select($prod_sql);
							//echo $prod_sql;//die;
							foreach($prod_sql_result as $proRes)
							{
								$production_array[$proRes[csf("order_id")]][$proRes[csf("production_date")]]['cutting_qnty']=$proRes[csf("cutting_qnty")];
								$production_array[$proRes[csf("order_id")]][$proRes[csf("production_date")]]['sewingout_qnty']=$proRes[csf("sewingout_qnty")];
								$production_array[$proRes[csf("order_id")]][$proRes[csf("production_date")]]['ironout_qnty']=$proRes[csf("ironout_qnty")];
								$production_array[$proRes[csf("order_id")]][$proRes[csf("production_date")]]['gmts_fin_qnty']=$proRes[csf("gmts_fin_qnty")];
							}

							if($db_type==0)
							{
								$order_sql= "select c.id, c.order_no, c.cust_style_ref, a.company_id, a.party_id, a.location_id, a.job_no_prefix_num,a.subcon_job, b.gmts_item_id, b.line_id, sum(CASE WHEN b.order_id=c.id THEN b.production_qnty ELSE 0 END) AS production_qnty, b.production_date, b.prod_reso_allo
							from
								subcon_ord_mst a, subcon_gmts_prod_dtls b, subcon_ord_dtls c
							where
								 b.order_id=c.id and b.production_date between $txt_date_from and $txt_date_to and a.company_id=$cbo_company_name $sub_floor_name $sub_location_cond and a.subcon_job=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.order_id, b.production_date order by b.production_date";
							}
							else
							{
								 $order_sql= "select c.id, c.order_no, c.cust_style_ref, a.company_id, a.party_id, a.location_id, a.job_no_prefix_num,a.subcon_job, b.gmts_item_id, b.line_id, sum(CASE WHEN b.order_id=c.id THEN b.production_qnty ELSE 0 END) AS production_qnty,  b.production_date, b.prod_reso_allo
							from
								subcon_ord_mst a, subcon_gmts_prod_dtls b, subcon_ord_dtls c
							where
								 b.order_id=c.id and b.production_date between $txt_date_from and $txt_date_to  and a.company_id=$cbo_company_name 	and a.subcon_job=c.job_no_mst $sub_floor_name $sub_location_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.order_id, c.id, c.order_no, c.cust_style_ref, a.company_id, a.job_no_prefix_num,a.subcon_job, a.party_id, a.company_id, a.party_id, a.location_id, b.gmts_item_id, b.line_id, b.production_date, b.prod_reso_allo order by c.id";
							}

					   		//echo $order_sql;//die;

							$order_sql_result=sql_select($order_sql);
							   $j=0;$k=0;
							   foreach($order_sql_result as $orderRes)
								{
									if($orderRes[csf("party_id")] !="")
								    {
									   $j++;
									   if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

										$sewing_line='';
										if($pro_date_sql_row[csf('prod_reso_allo')]==1)
										{
											$line_number=explode(",",$prod_reso_arr[$orderRes[csf("line_id")]]);
											foreach($line_number as $val)
											{
												if($sewing_line=='') $sewing_line=$line_library[$val]; else $sewing_line.=", ".$line_library[$val];
											}
										}
										else $sewing_line=$line_library[$orderRes[csf("line_id")]];

										?>
										<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr3_3nd<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr3_3nd<? echo $j; ?>" style="height:20px">
											<td width="30" ><? echo $j; ?></td>
											<td width="100"><p><? echo $company_short_library[$orderRes[csf("company_id")]]; ?></p></td>
	                                        <td width="100" align="center"><p><? echo $orderRes[csf("job_no_prefix_num")]; ?></p></td>
											<td width="100" align="center" title="<? echo $orderRes[csf("job_no_prefix_num")]; ?>"><p><? echo $orderRes[csf("subcon_job")]; ?></p></td>
											<td width="130"><p><? echo $orderRes[csf("order_no")]; ?></p></td>
											<td width="100"><? echo $buyer_short_library[$orderRes[csf("party_id")]]; ?></td>
											<td width="130"><p><? echo $orderRes[csf("cust_style_ref")]; ?></p></td>
											<td width="130"><p><? echo $garments_item[$orderRes[csf("gmts_item_id")]];?></p></td>
											<td width="75" bgcolor="<? echo $color; ?>"><? echo change_date_format($orderRes[csf("production_date")]);  ?></td>
											<td width="100" align="center"><? echo $sewing_line; ?></td>
											<td width="90" align="right"><? echo $production_array[$orderRes[csf("id")]][$orderRes[csf("production_date")]]['cutting_qnty']; $total_cutt+=$production_array[$orderRes[csf("id")]][$orderRes[csf("production_date")]]['cutting_qnty']; ?></td>
											<td width="90" align="right"><? echo $production_array[$orderRes[csf("id")]][$orderRes[csf("production_date")]]['sewingout_qnty']; $total_sew+=$production_array[$orderRes[csf("id")]]['sewingout_qnty']; ?></td>
											<td width="90" align="right"><? echo $production_array[$orderRes[csf("id")]][$orderRes[csf("production_date")]]['ironout_qnty']; $total_iron_sub+=$production_array[$orderRes[csf("id")]]['ironout_qnty']; ?></td>
											<td width="90" align="right"><? echo $production_array[$orderRes[csf("id")]][$orderRes[csf("production_date")]]['gmts_fin_qnty']; $total_gmtfin+=$production_array[$orderRes[csf("id")]]['gmts_fin_qnty']; ?></td>

											<td width="">&nbsp;</td>
										 </tr>
									<?
								   }
								}
							  ?>
							</table>
							<table border="1" class="tbl_bottom"  width="1500" rules="all" id="report_table_footer2" >
								<tr>
									<td width="30"></td>
                                    <td width="100"></td>
									<td width="100"></td>
									<td width="100"></td>
									<td width="130"></td>
									<td width="100"></td>
									<td width="130">Total</td>
									<td width="130" id="total_ord_quantity"><? echo $total_ord_quantity; ?></td>
									<td width="75"></td>
									<td width="100"></td>
									<td width="90" id="total_cutt"><? echo $total_cutt; ?></td>
									<td width="90" id="total_sew"><? echo $total_sew; ?></td>
									<td width="90" id="total_iron_sub"><? echo $total_iron_sub; ?></td>
									<td width="90" id="total_gmtfin"><? echo $total_gmtfin; ?></td>
									<td width=""></td>
								 </tr>
						    </table>
						</div>


			<?
		}
		?>
		</div>
		<?

	}

	$html = ob_get_contents();
	ob_clean();
	//$new_link=create_delete_report_file( $html, 1, 1, "../../../" );

	// echo "$html####";
	foreach (glob($user_id."_*.xls") as $filename)
	{
		@unlink($filename);
	}
	$name=$user_id."_".time().".xls";
	$create_new_excel = fopen($name, 'w');
	$is_created = fwrite($create_new_excel,$html);
	//$new_link=create_delete_report_file( $html, 1, 1, "../../../" );
	echo $html."####".$name;
	exit();
}

if($action=="report_generate2")
{
	ob_start();
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$company_short_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name"  );
	$supplier_arr=return_library_array( "select id,supplier_name from  lib_supplier", "id", "supplier_name");
 	$buyer_short_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name"  );
 	$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  );
	$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  );
	$floor_group=return_library_array( "select id,group_name from lib_prod_floor", "id", "group_name"  );
	$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$color_library_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$buyer_brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand",'id','brand_name');
	$buyer_season_arr=return_library_array( "select id, season_name from  lib_buyer_season",'id','season_name');
	$type = str_replace("'","",$cbo_type);
	$cbo_com_fac_name= str_replace("'","",$cbo_com_fac_name);
	$lc_comp=str_replace("'", "", $cbo_company_name);

	if($lc_comp)$subcon_lc_comp=" and a.company_id in ($lc_comp) ";
	if($cbo_com_fac_name)$subcon_work_comp=" and a.company_id in ($cbo_com_fac_name) ";

	$ReportType=str_replace("'","",$ReportType);
	if($type==1)
	{
		$report_name="Date Wise Production Report";
		$colSpan="36";
		$div_width="3140";
	}
	else if($type==2)
	{
		$report_name="Date, Location, Floor & Line Wise Production Report";
		$colSpan="39";
		$div_width="3700";
	}

	ob_start();
	$garments_nature=str_replace("'","",$cbo_garments_nature);
	$cbo_floor=str_replace("'","",$cbo_floor);
	//echo $cbo_floor;
	//if($garments_nature==1)$garments_nature="";
	 if($garments_nature==1) $garmentsNature=""; else $garmentsNature=" and garments_nature=$garments_nature";

	$location = str_replace("'","",$cbo_location);
	if(str_replace("'","",$cbo_company_name)==0) $company_name=""; else $company_name=" and b.company_name=$cbo_company_name";
	if(str_replace("'","",$cbo_buyer_name)==0)$buyer_name="";else $buyer_name=" and a.buyer_name=$cbo_buyer_name";
	if(str_replace("'","",$cbo_buyer_name)==0)$buyer_name_inspection="";else $buyer_name_inspection=" and c.buyer_name=$cbo_buyer_name";
	if(str_replace("'","",$cbo_season)!=0) $season_cond="and a.season_buyer_wise=$cbo_season "; else $season_cond="";
	if(str_replace("'","",$cbo_brand)!=0) $brand_cond="and a.brand_id=$cbo_brand "; else $brand_cond="";
	if($db_type==0)
	{
		if(str_replace("'","",$cbo_season_year)!=0) $year_cond=" and a.season_year=$cbo_season_year"; else $year_cond="";
	}
	else if ($db_type==2)
	{
		if(str_replace("'","",$cbo_season_year)!=0) $year_cond=" and a.season_year=$cbo_season_year"; else $year_cond="";
	}

	if($cbo_floor==0) $floor_name="";else $floor_name=" and floor_id in($cbo_floor)";
	if($cbo_floor==0) $floor_name_sewing="";else $floor_name_sewing=" and c.floor_id in($cbo_floor)";
	//echo $floor_name;die;
	if($cbo_floor==0) $floor_id="";else $floor_id=" and floor_id in($cbo_floor)";
	if ($location==0) $location_cond=""; else $location_cond=" and location in($location) ";

	if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="")
	{
		$txt_date="";
		$txt_ex_date="";
	}
	else
	{
		$txt_date=" and c.production_date between $txt_date_from and $txt_date_to";
		$txt_ex_date=" and g.delivery_date between $txt_date_from and $txt_date_to";
	}

	$fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
	$toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );
	//cbo_garments_nature
	$file_no = str_replace("'","",$txt_file_no);
	$internal_ref = str_replace("'","",$txt_internal_ref);
	$style_ref = str_replace("'","",$txt_style_ref);
	// echo $internal_ref;
	$cbo_company_name = str_replace("'","",$cbo_company_name);
	$cbo_com_fac_name = str_replace("'","",$cbo_com_fac_name);
	// echo $cbo_com_fac_name."__".$cbo_company_name;
	$cbo_buyer_name = str_replace("'","",$cbo_buyer_name);
	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='".trim($file_no)."' ";
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='".trim($internal_ref)."' ";
	if ($style_ref=="") $style_ref_cond=""; else $style_ref_cond=" and a.style_ref_no='".trim($style_ref)."' ";
	if ($location=="" || $location==0 ) $location_cond=""; else $location_cond=" and location in($location) ";

	// echo $working_factory_cond;
	if(str_replace("'","",$cbo_company_name)==0) $company_name_cond=""; else $company_name_cond=" and a.company_name=$cbo_company_name";
    // echo $company_name_cond;die;

	// // ================================= Production Query ===============================================

	if($cbo_com_fac_name=="") $pro_working_factory_cond=""; else $pro_working_factory_cond=" and c.serving_company in($cbo_com_fac_name)";
	$sql_production="SELECT
	a.job_no_prefix_num,
	a.buyer_name,
	a.brand_id,
	a.season_buyer_wise,
	a.season_year,
	a.style_ref_no,
	a.gmts_item_id,
	c.production_date,
	c.serving_company,
	c.production_type,
	e.job_no_mst,
	e.job_id,
	c.floor_id,
	e.color_number_id,
    b.grouping,
	b.id,
	d.production_qnty,
	d.reject_qty,
	c.embel_name
    FROM wo_po_details_master a, wo_po_break_down b, pro_garments_production_mst c, pro_garments_production_dtls d, wo_po_color_size_breakdown e
    WHERE     c.id = d.mst_id
	AND a.id = e.job_id
	AND a.id = b.job_id
    AND b.id = e.po_break_down_id
	AND e.id = d.color_size_break_down_id
	AND c.PRODUCTION_TYPE IN (1,2,3,4,5,8,80)
	AND a.status_active = 1
	AND a.is_deleted = 0
	AND b.status_active = 1
    AND b.is_deleted = 0
	AND c.status_active = 1
	AND c.is_deleted = 0
	AND d.status_active = 1
	AND d.is_deleted = 0
	AND e.status_active = 1
	AND e.is_deleted = 0
	and c.embel_name in (0,3)
	$txt_date $pro_working_factory_cond $internal_ref_cond $company_name_cond $buyer_name $season_cond $brand_cond $year_cond $file_no_cond $style_ref_cond $floor_name_sewing order by c.production_date";
	//  echo $sql_production;die;
	$sql_production_result = sql_select($sql_production);

	$data_array = array();
	$style_data_array = array();
	$job_wise_buyer_array = array();
	$job_wise_style_array = array();
	foreach($sql_production_result as $row)
	{

		if($row['PRODUCTION_TYPE'] ==1 ){ //Cutting
			$data_array[$row['SERVING_COMPANY']][$floor_group[$row['FLOOR_ID']]][$row['JOB_NO_MST']][$row['COLOR_NUMBER_ID']][$row['PRODUCTION_DATE']]['CUTTING_QC_QNTY'] += $row['PRODUCTION_QNTY'];
			$data_array[$row['SERVING_COMPANY']][$floor_group[$row['FLOOR_ID']]][$row['JOB_NO_MST']][$row['COLOR_NUMBER_ID']][$row['PRODUCTION_DATE']]['REJECT_QNTY'] += $row['REJECT_QTY'];
		}
		if ($row['PRODUCTION_TYPE'] ==2 && $row['EMBEL_NAME'] ==3 ) { //Wash Send
			$data_array[$row['SERVING_COMPANY']][$floor_group[$row['FLOOR_ID']]][$row['JOB_NO_MST']][$row['COLOR_NUMBER_ID']][$row['PRODUCTION_DATE']]['WASH_SEND'] += $row['PRODUCTION_QNTY'];
		}
		if ($row['PRODUCTION_TYPE'] ==3 && $row['EMBEL_NAME'] ==3) {//Wash Received
			$data_array[$row['SERVING_COMPANY']][$floor_group[$row['FLOOR_ID']]][$row['JOB_NO_MST']][$row['COLOR_NUMBER_ID']][$row['PRODUCTION_DATE']]['WASH_RECEIVED'] += $row['PRODUCTION_QNTY'];
		}
		if ($row['PRODUCTION_TYPE'] ==4 ) {//Sewing In
			$data_array[$row['SERVING_COMPANY']][$floor_group[$row['FLOOR_ID']]][$row['JOB_NO_MST']][$row['COLOR_NUMBER_ID']][$row['PRODUCTION_DATE']]['SEWING_INPUT'] += $row['PRODUCTION_QNTY'];

		}
		if ($row['PRODUCTION_TYPE'] ==5 ) {//Sewing Out
			$data_array[$row['SERVING_COMPANY']][$floor_group[$row['FLOOR_ID']]][$row['JOB_NO_MST']][$row['COLOR_NUMBER_ID']][$row['PRODUCTION_DATE']]['SEWING_OUTPUT'] += $row['PRODUCTION_QNTY'];
			$data_array[$row['SERVING_COMPANY']][$floor_group[$row['FLOOR_ID']]][$row['JOB_NO_MST']][$row['COLOR_NUMBER_ID']][$row['PRODUCTION_DATE']]['SEWING_REJECT_QTY'] += $row['REJECT_QTY'];
		}
		if ($row['PRODUCTION_TYPE'] ==80 ) { //Woven Finishing Entry
			$data_array[$row['SERVING_COMPANY']][$floor_group[$row['FLOOR_ID']]][$row['JOB_NO_MST']][$row['COLOR_NUMBER_ID']][$row['PRODUCTION_DATE']]['FINISHING_QNTY'] += $row['PRODUCTION_QNTY'];
		}
		if ($row['PRODUCTION_TYPE'] ==8 ) {//Packing Finishing
			$data_array[$row['SERVING_COMPANY']][$floor_group[$row['FLOOR_ID']]][$row['JOB_NO_MST']][$row['COLOR_NUMBER_ID']][$row['PRODUCTION_DATE']]['PACKING_FINISHING'] += $row['PRODUCTION_QNTY'];
			$data_array[$row['SERVING_COMPANY']][$floor_group[$row['FLOOR_ID']]][$row['JOB_NO_MST']][$row['COLOR_NUMBER_ID']][$row['PRODUCTION_DATE']]['FINISHING_REJECT_QNTY'] += $row['REJECT_QTY'];
		}


       	$data_array[$row['SERVING_COMPANY']][$floor_group[$row['FLOOR_ID']]][$row['JOB_NO_MST']][$row['COLOR_NUMBER_ID']][$row['PRODUCTION_DATE']]['JOB_NO_PREFIX_NUM'] = $row['JOB_NO_PREFIX_NUM'];
       	$data_array[$row['SERVING_COMPANY']][$floor_group[$row['FLOOR_ID']]][$row['JOB_NO_MST']][$row['COLOR_NUMBER_ID']][$row['PRODUCTION_DATE']]['BUYER_NAME'] = $row['BUYER_NAME'];
       	$data_array[$row['SERVING_COMPANY']][$floor_group[$row['FLOOR_ID']]][$row['JOB_NO_MST']][$row['COLOR_NUMBER_ID']][$row['PRODUCTION_DATE']]['BRAND_ID'] = $row['BRAND_ID'];
       	$data_array[$row['SERVING_COMPANY']][$floor_group[$row['FLOOR_ID']]][$row['JOB_NO_MST']][$row['COLOR_NUMBER_ID']][$row['PRODUCTION_DATE']]['SEASON_BUYER_WISE'] = $row['SEASON_BUYER_WISE'];
       	$data_array[$row['SERVING_COMPANY']][$floor_group[$row['FLOOR_ID']]][$row['JOB_NO_MST']][$row['COLOR_NUMBER_ID']][$row['PRODUCTION_DATE']]['SEASON_YEAR'] = $row['SEASON_YEAR'];
       	$data_array[$row['SERVING_COMPANY']][$floor_group[$row['FLOOR_ID']]][$row['JOB_NO_MST']][$row['COLOR_NUMBER_ID']][$row['PRODUCTION_DATE']]['STYLE_REF_NO'] = $row['STYLE_REF_NO'];
       	$data_array[$row['SERVING_COMPANY']][$floor_group[$row['FLOOR_ID']]][$row['JOB_NO_MST']][$row['COLOR_NUMBER_ID']][$row['PRODUCTION_DATE']]['GMTS_ITEM_ID'] = $row['GMTS_ITEM_ID'];
       	$data_array[$row['SERVING_COMPANY']][$floor_group[$row['FLOOR_ID']]][$row['JOB_NO_MST']][$row['COLOR_NUMBER_ID']][$row['PRODUCTION_DATE']]['FLOOR_ID'] = $row['FLOOR_ID'];

	   	$poIds[$row['ID']]=$row['ID'];

	  	//    buyer

		$style_data_array[$row['JOB_NO_MST']][$row['STYLE_REF_NO']]['STYLE_REF_NO'] = $row['STYLE_REF_NO'];
		$style_data_array[$row['JOB_NO_MST']][$row['STYLE_REF_NO']]['BRAND_ID'] = $row['BRAND_ID'];
		$style_data_array[$row['JOB_NO_MST']][$row['STYLE_REF_NO']]['SEASON_BUYER_WISE'] = $row['SEASON_BUYER_WISE'];
		$style_data_array[$row['JOB_NO_MST']][$row['STYLE_REF_NO']]['SEASON_YEAR'] = $row['SEASON_YEAR'];
		$style_data_array[$row['JOB_NO_MST']][$row['STYLE_REF_NO']]['STYLE_REF_NO'] = $row['STYLE_REF_NO'];

		if($row['PRODUCTION_TYPE'] ==1 )//Cutting
		{
			$style_data_array[$row['JOB_NO_MST']][$row['STYLE_REF_NO']]['CUTTING_QC_QNTY'] += $row['PRODUCTION_QNTY'];
			$style_data_array[$row['JOB_NO_MST']][$row['STYLE_REF_NO']]['REJECT_QNTY'] += $row['REJECT_QTY'];
		}
		if ($row['PRODUCTION_TYPE'] ==4 ) {//Sewing In
			$style_data_array[$row['JOB_NO_MST']][$row['STYLE_REF_NO']]['SEWING_INPUT'] += $row['PRODUCTION_QNTY'];
			$style_data_array[$row['JOB_NO_MST']][$row['STYLE_REF_NO']]['SEWING_REJECT_QTY'] += $row['REJECT_QTY'];
		}

		if ($row['PRODUCTION_TYPE'] ==5 ) {//Sewing Out
			$style_data_array[$row['JOB_NO_MST']][$row['STYLE_REF_NO']]['SEWING_OUTPUT'] += $row['PRODUCTION_QNTY'];
		}

		if ($row['PRODUCTION_TYPE'] ==2 && $row['EMBEL_NAME'] ==3 ) { //Wash Send
			$style_data_array[$row['JOB_NO_MST']][$row['STYLE_REF_NO']]['WASH_SEND'] += $row['PRODUCTION_QNTY'];
		}
		if ($row['PRODUCTION_TYPE'] ==3 && $row['EMBEL_NAME'] ==3) {//Wash Received
			$style_data_array[$row['JOB_NO_MST']][$row['STYLE_REF_NO']]['WASH_RECEIVED'] += $row['PRODUCTION_QNTY'];
		}
		if ($row['PRODUCTION_TYPE'] ==80 ) { //Woven Finishing Entry
			$style_data_array[$row['JOB_NO_MST']][$row['STYLE_REF_NO']]['FINISHING_QNTY'] += $row['PRODUCTION_QNTY'];
			$style_data_array[$row['JOB_NO_MST']][$row['STYLE_REF_NO']]['FINISHING_REJECT_QNTY'] += $row['REJECT_QTY'];
		}
		if ($row['PRODUCTION_TYPE'] ==8 ) {//Packing Finishing
			$style_data_array[$row['JOB_NO_MST']][$row['STYLE_REF_NO']]['PACKING_FINISHING'] += $row['PRODUCTION_QNTY'];
		}


		$style_data_array[$row['JOB_NO_MST']][$row['STYLE_REF_NO']]['BUYER_NAME'] = $row['BUYER_NAME'];

		$job_wise_buyer_array[$row['JOB_NO_MST']] = $row['BUYER_NAME'];
		$job_wise_style_array[$row['JOB_NO_MST']] = $row['STYLE_REF_NO'];


	}
	//echo "<pre>"; print_r($style_data_array); die;
	// pre( $poIds); die;
	// ============================== data store to gbl table ==================================
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2) and ENTRY_FORM=41");
	oci_commit($con);

	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 41, 1, $poIds, $empty_arr);//PO ID
	// ============================== end data store to gbl table ==================================
	$poIds_cond = where_con_using_array($poIds,0,"f.po_break_down_id");
	$ins_poIds_cond = where_con_using_array($poIds,0,"a.po_break_down_id");
	$delevery_poIds_cond = where_con_using_array($poIds,0,"f.po_break_down_id");


	$sql_defect="SELECT f.id,
	c.serving_company,
	c.production_type,
	e.job_no_mst,
	e.job_id,
	c.floor_id,
	e.color_number_id,
	c.production_date,
	f.defect_qty,
	(
         CASE
             WHEN f.production_type = 5
             THEN
                 f.defect_qty
             ELSE
                 0
         END)
         AS sewing_defect_qnty,
     (
         CASE
             WHEN f.production_type = 80
             THEN
                 f.defect_qty
             ELSE
                 0
         END)
         AS finishing_defect_qnty
    FROM
	pro_garments_production_mst c,
	pro_garments_production_dtls d,
	wo_po_color_size_breakdown  e,
	pro_gmts_prod_dft           f,GBL_TEMP_ENGINE tmp
    WHERE     c.id = f.mst_id
	AND c.id = d.mst_id
	AND e.id = d.color_size_break_down_id
	AND c.po_break_down_id = tmp.ref_val and tmp.entry_form=41 and tmp.ref_from=1 and tmp.user_id = $user_id
	AND c.PRODUCTION_TYPE IN (5, 80)
	AND c.status_active = 1
	AND c.is_deleted = 0
	AND d.status_active = 1
	AND d.is_deleted = 0
	AND e.status_active = 1
	AND e.is_deleted = 0
	AND f.status_active = 1
	AND f.is_deleted = 0
	and f.defect_qty>0
	$txt_date
	$pro_working_factory_cond ";
	// echo $sql_defect;die;
	$sql_defect_result = sql_select($sql_defect);

	$defect_data_array = array();
	$defect_smry_data_array = array();
	$dfct_tbl_id_chk = array();
	foreach($sql_defect_result as $row)
	{
		if($dfct_tbl_id_chk[$row['ID']]=="")
		{
	       	$defect_data_array[$row['SERVING_COMPANY']][$floor_group[$row['FLOOR_ID']]][$row['JOB_NO_MST']][$row['COLOR_NUMBER_ID']][$row['PRODUCTION_DATE']]['FINISHING_DEFECT_QNTY'] += $row['FINISHING_DEFECT_QNTY'];
	       	$defect_data_array[$row['SERVING_COMPANY']][$floor_group[$row['FLOOR_ID']]][$row['JOB_NO_MST']][$row['COLOR_NUMBER_ID']][$row['PRODUCTION_DATE']]['SEWING_DEFECT_QNTY'] += $row['SEWING_DEFECT_QNTY'];

		  	//    buyer

			$defect_smry_data_array[$row['JOB_NO_MST']][$job_wise_style_array[$row['JOB_NO_MST']]]['SEWING_DEFECT_QNTY'] += $row['SEWING_DEFECT_QNTY'];
			$defect_smry_data_array[$row['JOB_NO_MST']][$job_wise_style_array[$row['JOB_NO_MST']]]['FINISHING_DEFECT_QNTY'] += $row['FINISHING_DEFECT_QNTY'];
			$dfct_tbl_id_chk[$row['ID']] = $row['ID'];
		}



	}
	// echo "<pre>"; print_r($defect_data_array);

	// echo "<pre>";
	// print_r($defect_smry_data_array);

	// ================================= lay Query ===============================================
	if($cbo_com_fac_name=="") $working_factory_cond=""; else $working_factory_cond=" and d.working_company_id in($cbo_com_fac_name)";
	$txt_date_ex = str_replace("c.production_date", "d.entry_date", $txt_date);

	if(str_replace("'","",$cbo_company_name)==0) $company_name_cond=""; else $company_name_cond=" and a.company_name=$cbo_company_name";

	$sql="SELECT a.job_no_prefix_num,
	a.buyer_name,
	a.brand_id,
	a.season_buyer_wise,
	a.season_year,
	a.style_ref_no,
	a.gmts_item_id,
	d.job_no,
	e.color_id,
	d.entry_date,
	d.working_company_id,
	a.buyer_name,
	d.floor_id,
	f.size_qty
	FROM
	wo_po_details_master        a,
	wo_po_break_down            b,
	ppl_cut_lay_mst             d,
	ppl_cut_lay_dtls            e,
	ppl_cut_lay_bundle 			f
	WHERE
	a.job_no=d.job_no
	AND a.id = b.job_id
	AND	d.id = e.mst_id
	and d.id=f.mst_id
	and e.id=f.dtls_id
	and f.order_id=b.id
	and f.status_active=1
	AND d.status_active = 1
	AND d.is_deleted = 0
	AND e.status_active = 1
	AND e.is_deleted = 0 $company_name_cond
	$txt_date_ex $working_factory_cond $buyer_name $internal_ref_cond $season_cond $brand_cond $year_cond $style_ref_cond order by d.entry_date";



	// echo $sql;die;
	$sql_result = sql_select($sql);

	// $data_array = array();
	$buyer_cutting_array = array();

	foreach($sql_result as $row)
	{
		$data_array[$row['WORKING_COMPANY_ID']][$floor_group[$row['FLOOR_ID']]][$row['JOB_NO']][$row['COLOR_ID']][$row['ENTRY_DATE']]['JOB_NO_PREFIX_NUM'] = $row['JOB_NO_PREFIX_NUM'];
       	$data_array[$row['WORKING_COMPANY_ID']][$floor_group[$row['FLOOR_ID']]][$row['JOB_NO']][$row['COLOR_ID']][$row['ENTRY_DATE']]['BUYER_NAME'] = $row['BUYER_NAME'];
       	$data_array[$row['WORKING_COMPANY_ID']][$floor_group[$row['FLOOR_ID']]][$row['JOB_NO']][$row['COLOR_ID']][$row['ENTRY_DATE']]['BRAND_ID'] = $row['BRAND_ID'];
       	$data_array[$row['WORKING_COMPANY_ID']][$floor_group[$row['FLOOR_ID']]][$row['JOB_NO']][$row['COLOR_ID']][$row['ENTRY_DATE']]['SEASON_BUYER_WISE'] = $row['SEASON_BUYER_WISE'];
       	$data_array[$row['WORKING_COMPANY_ID']][$floor_group[$row['FLOOR_ID']]][$row['JOB_NO']][$row['COLOR_ID']][$row['ENTRY_DATE']]['SEASON_YEAR'] = $row['SEASON_YEAR'];
       	$data_array[$row['WORKING_COMPANY_ID']][$floor_group[$row['FLOOR_ID']]][$row['JOB_NO']][$row['COLOR_ID']][$row['ENTRY_DATE']]['STYLE_REF_NO'] = $row['STYLE_REF_NO'];
       	$data_array[$row['WORKING_COMPANY_ID']][$floor_group[$row['FLOOR_ID']]][$row['JOB_NO']][$row['COLOR_ID']][$row['ENTRY_DATE']]['GMTS_ITEM_ID'] = $row['GMTS_ITEM_ID'];
       	$data_array[$row['WORKING_COMPANY_ID']][$floor_group[$row['FLOOR_ID']]][$row['JOB_NO']][$row['COLOR_ID']][$row['ENTRY_DATE']]['FLOOR_ID'] = $row['FLOOR_ID'];

       	$data_array[$row['WORKING_COMPANY_ID']][$floor_group[$row['FLOOR_ID']]][$row['JOB_NO']][$row['COLOR_ID']][$row['ENTRY_DATE']]['CUTTING_QTY'] += $row['SIZE_QTY'];

       	$style_data_array[$row['JOB_NO']][$row['STYLE_REF_NO']]['CUTTING_QTY'] += $row['SIZE_QTY'];
       	$style_data_array[$row['JOB_NO']][$row['STYLE_REF_NO']]['STYLE_REF_NO'] = $row['STYLE_REF_NO'];
		$style_data_array[$row['JOB_NO']][$row['STYLE_REF_NO']]['BRAND_ID'] = $row['BRAND_ID'];
		$style_data_array[$row['JOB_NO']][$row['STYLE_REF_NO']]['SEASON_BUYER_WISE'] = $row['SEASON_BUYER_WISE'];
		$style_data_array[$row['JOB_NO']][$row['STYLE_REF_NO']]['SEASON_YEAR'] = $row['SEASON_YEAR'];
		$style_data_array[$row['JOB_NO']][$row['STYLE_REF_NO']]['STYLE_REF_NO'] = $row['STYLE_REF_NO'];
		$style_data_array[$row['JOB_NO']][$row['STYLE_REF_NO']]['BUYER_NAME'] = $row['BUYER_NAME'];

	}

	// echo "<pre>"; print_r($data_array);

  // ================================= Inspection Query  ===============================================
	if($cbo_com_fac_name=="") $inspection_working_factory_cond=""; else $inspection_working_factory_cond=" and a.working_company in($cbo_com_fac_name)";
	if(str_replace("'","",$cbo_company_name)==0) $company_name_cond_insp=""; else $company_name_cond_insp=" and c.company_name=$cbo_company_name";
	// echo $working_factory_cond;
	$txt_inspection_date_ex = str_replace("c.production_date", "a.inspection_date", $txt_date);

	if($txt_inspection_date_ex=="")
	{

		$sql_inspection="SELECT a.job_no,a.working_company,a.working_floor as floor_id,a.inspection_date, b.color_id, b.ins_qty, c.buyer_name,
		c.brand_id,
		c.season_buyer_wise,
		c.season_year,
		c.style_ref_no,
		c.gmts_item_id
		FROM pro_buyer_inspection a, pro_buyer_inspection_breakdown b,wo_po_details_master c,GBL_TEMP_ENGINE tmp
		WHERE a.id = b.mst_id
			AND a.job_no = c.job_no and a.po_break_down_id=tmp.ref_val and tmp.entry_form=41 and tmp.ref_from=1 and tmp.user_id = $user_id  $company_name_cond_insp
			$txt_inspection_date_ex $inspection_working_factory_cond $buyer_name_inspection";
	}
	else
	{

		$sql_inspection="SELECT a.job_no,a.working_company,a.working_floor as floor_id,a.inspection_date, b.color_id, b.ins_qty, c.buyer_name,
		c.brand_id,
		c.season_buyer_wise,
		c.season_year,
		c.style_ref_no,
		c.gmts_item_id
		FROM pro_buyer_inspection a, pro_buyer_inspection_breakdown b,wo_po_details_master c
		WHERE a.id = b.mst_id
			AND a.job_no = c.job_no $company_name_cond_insp
			$txt_inspection_date_ex $inspection_working_factory_cond $buyer_name_inspection";
	}

	// echo $sql_inspection;die;
	$sql_inspection_result = sql_select($sql_inspection);

	$inspection_data_array = array();
	$inspection_smry_data_array = array();
	foreach($sql_inspection_result as $row)
	{
		// echo $row['JOB_NO'].'**'.$row['INSPECTION_DATE'] .'<br>';
		$data_array[$row['WORKING_COMPANY']][$floor_group[$row['FLOOR_ID']]][$row['JOB_NO']][$row['COLOR_ID']][$row['INSPECTION_DATE']]['JOB_NO_PREFIX_NUM'] = $row['JOB_NO_PREFIX_NUM'];
       	$data_array[$row['WORKING_COMPANY']][$floor_group[$row['FLOOR_ID']]][$row['JOB_NO']][$row['COLOR_ID']][$row['INSPECTION_DATE']]['BUYER_NAME'] = $row['BUYER_NAME'];
       	$data_array[$row['WORKING_COMPANY']][$floor_group[$row['FLOOR_ID']]][$row['JOB_NO']][$row['COLOR_ID']][$row['INSPECTION_DATE']]['BRAND_ID'] = $row['BRAND_ID'];
       	$data_array[$row['WORKING_COMPANY']][$floor_group[$row['FLOOR_ID']]][$row['JOB_NO']][$row['COLOR_ID']][$row['INSPECTION_DATE']]['SEASON_BUYER_WISE'] = $row['SEASON_BUYER_WISE'];
       	$data_array[$row['WORKING_COMPANY']][$floor_group[$row['FLOOR_ID']]][$row['JOB_NO']][$row['COLOR_ID']][$row['INSPECTION_DATE']]['SEASON_YEAR'] = $row['SEASON_YEAR'];
       	$data_array[$row['WORKING_COMPANY']][$floor_group[$row['FLOOR_ID']]][$row['JOB_NO']][$row['COLOR_ID']][$row['INSPECTION_DATE']]['STYLE_REF_NO'] = $row['STYLE_REF_NO'];
       	$data_array[$row['WORKING_COMPANY']][$floor_group[$row['FLOOR_ID']]][$row['JOB_NO']][$row['COLOR_ID']][$row['INSPECTION_DATE']]['GMTS_ITEM_ID'] = $row['GMTS_ITEM_ID'];
       	$data_array[$row['WORKING_COMPANY']][$floor_group[$row['FLOOR_ID']]][$row['JOB_NO']][$row['COLOR_ID']][$row['INSPECTION_DATE']]['FLOOR_ID'] = $row['FLOOR_ID'];

       $data_array[$row['WORKING_COMPANY']][$floor_group[$row['FLOOR_ID']]][$row['JOB_NO']][$row['COLOR_ID']][$row['INSPECTION_DATE']]['INS_QTY'] += $row['INS_QTY'];

       $inspection_smry_data_array[$row['JOB_NO']][$row['STYLE_REF_NO']] += $row['INS_QTY'];

	   $style_data_array[$row['JOB_NO']][$row['STYLE_REF_NO']]['INS_QTY'] += $row['INS_QTY'];
	   $style_data_array[$row['JOB_NO']][$row['STYLE_REF_NO']]['STYLE_REF_NO'] = $row['STYLE_REF_NO'];
	   $style_data_array[$row['JOB_NO']][$row['STYLE_REF_NO']]['BRAND_ID'] = $row['BRAND_ID'];
	   $style_data_array[$row['JOB_NO']][$row['STYLE_REF_NO']]['SEASON_BUYER_WISE'] = $row['SEASON_BUYER_WISE'];
	   $style_data_array[$row['JOB_NO']][$row['STYLE_REF_NO']]['SEASON_YEAR'] = $row['SEASON_YEAR'];
	   $style_data_array[$row['JOB_NO']][$row['STYLE_REF_NO']]['STYLE_REF_NO'] = $row['STYLE_REF_NO'];
	   $style_data_array[$row['JOB_NO']][$row['STYLE_REF_NO']]['BUYER_NAME'] = $row['BUYER_NAME'];

	}
	// echo "<pre>";print_r($inspection_smry_data_array);die();

	// ================================= Delivery Query  ===============================================
	if($cbo_com_fac_name=="") $delivery_working_factory_cond=""; else $delivery_working_factory_cond=" and c.company_id in($cbo_com_fac_name)";
	// echo $working_factory_cond;
	// 	$sql_delivey="SELECT b.job_no_mst, c.delivery_date, b.color_number_id, c.company_id, a.ex_factory_qnty, a.EX_FACTORY_DATE
	// 	FROM pro_ex_factory_mst a, wo_po_color_size_breakdown b, pro_ex_factory_delivery_mst c
	//    WHERE
	// 		 a.po_break_down_id = b.po_break_down_id
	// 		and c.id = a.delivery_mst_id
	// 		and c.delivery_date BETWEEN $txt_date_from and $txt_date_to $delivery_working_factory_cond";

	// 	echo $sql_delivey;//die;
	// 	$sql_delivey_result = sql_select($sql_delivey);

	// 	$delivey_data_array = array();
	// 	foreach($sql_delivey_result as $row)
	// 	{
	// 	$delivey_data_array = array();
	//        $delivey_data_array[$row['COMPANY_ID']][$row['JOB_NO_MST']][$row['COLOR_NUMBER_ID']][$row['EX_FACTORY_DATE']]['EX_FACTORY_QNTY'] += $row['EX_FACTORY_QNTY'];

	// 	}
	if($cbo_com_fac_name=="") $delivery_working_factory_cond=""; else $delivery_working_factory_cond=" and g.delivery_company_id in($cbo_com_fac_name)";
	// echo $working_factory_cond;
	$sql_delivery=" SELECT a.job_no,
	a.buyer_name,
	a.brand_id,
	a.season_buyer_wise,
	a.season_year,
	a.style_ref_no,
	a.gmts_item_id,
	c.color_number_id,
	d.production_qnty,
	g.delivery_date,
	g.delivery_company_id,
	g.delivery_floor_id as floor_id
    FROM wo_po_details_master        a,
	wo_po_break_down            b,
	wo_po_color_size_breakdown  c,
	pro_ex_factory_mst f,
	pro_ex_factory_delivery_mst  g,
	pro_ex_factory_dtls d
    WHERE a.id = c.job_id
	AND a.id = b.job_id
	AND b.id = c.po_break_down_id
	AND b.id = f.po_break_down_id
	AND g.id = f.delivery_mst_id
	and c.id=d.color_size_break_down_id
	and f.id=d.mst_id
	AND a.status_active = 1
	AND a.is_deleted = 0
	AND b.status_active = 1
	AND b.is_deleted = 0
	AND c.status_active = 1
	AND c.is_deleted = 0
	AND d.status_active = 1
	AND d.is_deleted = 0 $company_name_cond
	$txt_ex_date $delivery_working_factory_cond  $buyer_name $season_cond $brand_cond $year_cond $file_no_cond $internal_ref_cond $style_ref_cond order by g.delivery_date";//$delevery_poIds_cond

	// echo $sql_delivery;die;
	$sql_delivey_result = sql_select($sql_delivery);

	$delivey_data_array = array();
	$buyer_delivery_qnty_data_array = array();
	foreach($sql_delivey_result as $row)
	{
		$data_array[$row['DELIVERY_COMPANY_ID']][$floor_group[$row['FLOOR_ID']]][$row['JOB_NO']][$row['COLOR_NUMBER_ID']][$row['DELIVERY_DATE']]['JOB_NO_PREFIX_NUM'] = $row['JOB_NO_PREFIX_NUM'];
       	$data_array[$row['DELIVERY_COMPANY_ID']][$floor_group[$row['FLOOR_ID']]][$row['JOB_NO']][$row['COLOR_NUMBER_ID']][$row['DELIVERY_DATE']]['BUYER_NAME'] = $row['BUYER_NAME'];
       	$data_array[$row['DELIVERY_COMPANY_ID']][$floor_group[$row['FLOOR_ID']]][$row['JOB_NO']][$row['COLOR_NUMBER_ID']][$row['DELIVERY_DATE']]['BRAND_ID'] = $row['BRAND_ID'];
       	$data_array[$row['DELIVERY_COMPANY_ID']][$floor_group[$row['FLOOR_ID']]][$row['JOB_NO']][$row['COLOR_NUMBER_ID']][$row['DELIVERY_DATE']]['SEASON_BUYER_WISE'] = $row['SEASON_BUYER_WISE'];
       	$data_array[$row['DELIVERY_COMPANY_ID']][$floor_group[$row['FLOOR_ID']]][$row['JOB_NO']][$row['COLOR_NUMBER_ID']][$row['DELIVERY_DATE']]['SEASON_YEAR'] = $row['SEASON_YEAR'];
       	$data_array[$row['DELIVERY_COMPANY_ID']][$floor_group[$row['FLOOR_ID']]][$row['JOB_NO']][$row['COLOR_NUMBER_ID']][$row['DELIVERY_DATE']]['STYLE_REF_NO'] = $row['STYLE_REF_NO'];
       	$data_array[$row['DELIVERY_COMPANY_ID']][$floor_group[$row['FLOOR_ID']]][$row['JOB_NO']][$row['COLOR_NUMBER_ID']][$row['DELIVERY_DATE']]['GMTS_ITEM_ID'] = $row['GMTS_ITEM_ID'];
       	$data_array[$row['DELIVERY_COMPANY_ID']][$floor_group[$row['FLOOR_ID']]][$row['JOB_NO']][$row['COLOR_NUMBER_ID']][$row['DELIVERY_DATE']]['FLOOR_ID'] = $row['FLOOR_ID'];

       	$data_array[$row['DELIVERY_COMPANY_ID']][$floor_group[$row['FLOOR_ID']]][$row['JOB_NO']][$row['COLOR_NUMBER_ID']][$row['DELIVERY_DATE']]['EX_FACTORY_QNTY'] += $row['PRODUCTION_QNTY'];

       	$style_data_array[$row['JOB_NO']][$row['STYLE_REF_NO']]['EX_FACTORY_QNTY'] += $row['PRODUCTION_QNTY'];
       	$style_data_array[$row['JOB_NO']][$row['STYLE_REF_NO']]['STYLE_REF_NO'] = $row['STYLE_REF_NO'];
		$style_data_array[$row['JOB_NO']][$row['STYLE_REF_NO']]['BRAND_ID'] = $row['BRAND_ID'];
		$style_data_array[$row['JOB_NO']][$row['STYLE_REF_NO']]['SEASON_BUYER_WISE'] = $row['SEASON_BUYER_WISE'];
		$style_data_array[$row['JOB_NO']][$row['STYLE_REF_NO']]['SEASON_YEAR'] = $row['SEASON_YEAR'];
		$style_data_array[$row['JOB_NO']][$row['STYLE_REF_NO']]['STYLE_REF_NO'] = $row['STYLE_REF_NO'];
		$style_data_array[$row['JOB_NO']][$row['STYLE_REF_NO']]['BUYER_NAME'] = $row['BUYER_NAME'];

	}
	// echo "<pre>"; print_r($data_array);die();

	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from=1 and ENTRY_FORM=41");
	oci_commit($con);
	disconnect($con);
	?>
		<fieldset style="width:1760px">
			<table width="1730" cellpadding="0" cellspacing="0">
				<tr class="form_caption">
					<td align="center"><p style="font-size:22px; font-weight:bold;">Company Name: <? echo  $company_library[str_replace("'","",$cbo_company_name)]; ?><p></td>
				</tr>
				<tr class="form_caption">
					<td align="center"><p style="font-size:14px; font-weight:bold;"><? echo "From: ".change_date_format( str_replace("'","",trim($txt_date_from)) )." To ".change_date_format( str_replace("'","",trim($txt_date_to)) ); ?></p></td>
				</tr>
			</table>
			<br />

			 <!-- ========= Details Part ======== -->
			 <table id="report_container" class="rpt_table" width="1740" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
			 <div style="width:300px; float:left; background-color:#FCF"><strong>Production-Details</strong></div>
			    <thead>
					<tr>
						<th colspan="11" align="right"></th>
						<th colspan="3" align="center">Cutting Status</th>
						<th colspan="3" align="center">Sewing Status</th>
						<th colspan="2" align="center">Wash Status</th>
						<th colspan="3" align="center">Finishing Status</th>
						<th colspan="2" align="center">Insp./Gmt</th>
					</tr>
					<tr height="50">
						<th width="40">Sl.</th>
						<th width="50">Working Company</th>
						<th width="70">Unit</th>
						<th width="60">Job No</th>
						<th width="70">Buyer Name</th>
						<th width="70">Brand</th>
						<th width="70">Season-Year</th>
						<th width="100">Style Name</th>
						<th width="100">Gmts. Color</th>
						<th width="100">Item Name</th>
						<th width="100">Production Date</th>

						<th width="70">Cut & Lay Qty.</th>
						<th width="70">QC Pass Qty.</th>
						<th width="70">Defect Qty.</th>

						<th width="70">Sewing Input</th>
						<th width="70">Sewing Output</th>
						<th width="70">Sewing Defect</th>

						<th width="70">Wash Send</th>
						<th width="70">Wash Rcv.</th>

						<th width="70">Finishing</th>
						<th width="70">Finishing Defect</th>
						<th width="70">Packing </th>

						<th width="70">Buyer Insp.</th>
						<th width="70">Gmts. Delivery</th>

					</tr>
				</thead>
			</table>
			<div style=" max-height:400px; overflow-y:scroll; width:1760px"  align="left" id="scroll_body">
				<table class="rpt_table" width="1740" cellpadding="0" cellspacing="0" border="1" rules="all"  id="table_body" align="left">
					<tbody>
						<?
						$k=1;
						foreach ($data_array as $working_company_key=>$working_company_value)
						{
							ksort($working_company_value);
							foreach ($working_company_value as $unit_name=>$unit_name_value)
							{
								foreach ($unit_name_value as $job_key=>$job_value)
								{
									foreach ($job_value as $color_key=>$color_value)
									{
										foreach ($color_value as $date_key=>$row)
								        {
								        	$sewing_reject_qty = $defect_data_array[$working_company_key][$unit_name][$job_key][$color_key][$date_key]['SEWING_DEFECT_QNTY'];
								        	$defect_data_array[$working_company_key][$unit_name][$job_key][$color_key][$date_key]['SEWING_DEFECT_QNTY']=0;

								        	$finishing_defect_qnty =  $defect_data_array[$working_company_key][$unit_name][$job_key][$color_key][$date_key]['FINISHING_DEFECT_QNTY'];
								        	$defect_data_array[$working_company_key][$unit_name][$job_key][$color_key][$date_key]['FINISHING_DEFECT_QNTY']=0;

								        	// $inspection_qnty =  $inspection_data_array[$working_company_key][$job_key][$color_key][$date_key]['INS_QTY'];
								        	// $inspection_data_array[$working_company_key][$job_key][$color_key][$date_key]['INS_QTY']=0;


											if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
											?>
												<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>">
													<td width="40"><? echo $k; ?></td>
													<td width="50" style="word-break:break-all"><? echo $company_short_library[$working_company_key]; ?></td>
													<td width="70" style="word-break:break-all"><? echo $unit_name; ?></td>
													<td width="60" style="word-break:break-all"><? echo $job_key; ?></td>
													<td width="70" style="word-break:break-all"><? echo $buyer_short_library[$row['BUYER_NAME']]; ?></td>
													<td width="70"style="word-break:break-all"><? echo $buyer_brand_arr[$row['BRAND_ID']]; ?></td>
													<td width="70" style="word-break:break-all"><? echo $buyer_season_arr[$row['SEASON_BUYER_WISE']]."-".$row['SEASON_YEAR']; ?></td>
													<td width="100" style="word-break:break-all"><? echo $row['STYLE_REF_NO']; ?></td>
													<td width="100" style="word-break:break-all"><? echo $color_library_arr[$color_key]; ?></td>
													<td width="100" style="word-break:break-all"><? echo $garments_item[$row['GMTS_ITEM_ID']]; ?></td>
													<td width="100" align="center"><? echo change_date_format($date_key); ?></td>

													<td width="70" align="right"><? echo number_format($row['CUTTING_QTY'],0); ?></td>
													<td width="70" align="right"><? echo $row['CUTTING_QC_QNTY']; ?></td>
													<td width="70" align="right"><? echo number_format($reject_qty = $row['REJECT_QNTY'],0); ?></td>

													<td width="70" align="right"><? echo number_format($sewing_input = $row['SEWING_INPUT'],0); ?></td>
													<td width="70" align="right"><? echo number_format($sewing_output= $row['SEWING_OUTPUT'],0); ?></td>
													<td width="70" align="right"><? echo number_format($sewing_reject_qty,0); ?></td>

													<td width="70" align="right"><? echo number_format($wash_send = $row['WASH_SEND'],0); ?></td>
													<td width="70" align="right"><? echo number_format($wash_received = $row['WASH_RECEIVED'],0); ?></td>

													<td width="70" align="right"><? echo number_format($finishing_qty = $row['FINISHING_QNTY'],0); ?></td>
													<td width="70" align="right"><? echo number_format($finishing_defect_qnty,0); ?></td>
													<td width="70" align="right"><? echo number_format($packing_finishing = $row['PACKING_FINISHING'],0); ?></td>

													<td width="70" align="right"><? echo number_format($row['INS_QTY'],0); ?></td>
													<td width="70" align="right"><? echo number_format($delivery_qnty =  $row['EX_FACTORY_QNTY'],0); ?></td>

												</tr>
											<?
											$k++;
											$total_lay_qty            	 += $row['CUTTING_QTY'];
											$total_qc_qty            	 += $row['CUTTING_QC_QNTY'];
											$total_reject_qty            += $reject_qty;
											$total_sewing_input          += $sewing_input;
											$total_sewing_output         += $sewing_output;
											$total_sewing_reject_qty     += $sewing_reject_qty;
											$total_wash_send             += $wash_send;
											$total_wash_received         += $wash_received;
											$total_finishing_qty         += $finishing_qty;
											$total_finishing_reject_qnty += $finishing_defect_qnty;
											$total_packing_finishing     += $packing_finishing;
											$total_inspection_qnty       += $row['INS_QTY'];
											$total_delivery_qnty         += $delivery_qnty;
										}
									}
								}
							}
					    }
						?>
					</tbody>
				</table>
			</div>
			<table class="rpt_table" width="1740" cellpadding="0" cellspacing="0" border="1" rules="all"  id="table_body_footer" align="left">
				<tfoot>
					<tr>
					    <th width="40"></th>
					    <th width="50"></th>
					    <th width="70"></th>
					    <th width="60"></th>
					    <th width="70"></th>
					    <th width="70"></th>
					    <th width="70"></th>
					    <th width="100"></th>
					    <th width="100"></th>
					    <th width="100"></th>
					    <th width="100">Total</th>
						<th width="70" align="right" id="total_lay_qty"><? echo number_format($total_lay_qty,0); ?></th>
						<th width="70" align="right" id="total_qc_qty"><? echo number_format($total_qc_qty,0); ?></th>
						<th width="70" align="right" id="total_reject_qty"><? echo number_format($total_reject_qty,0); ?></th>
						<th width="70" align="right" id="total_sewing_input"><? echo number_format($total_sewing_input,0); ?></th>
						<th width="70" align="right" id="total_sewing_output"><? echo number_format($total_sewing_output,0); ?></th>
						<th width="70" align="right" id="total_sewing_reject_qty"><? echo number_format($total_sewing_reject_qty,0); ?></th>
						<th width="70" align="right" id="total_wash_send"><? echo number_format($total_wash_send,0); ?></th>
						<th width="70" align="right" id="total_wash_received"><? echo number_format($total_wash_received,0); ?></th>
						<th width="70" align="right" id="total_finishing_qty"><? echo number_format($total_finishing_qty,0); ?></th>
						<th width="70" align="right" id="total_finishing_reject_qnty"><? echo number_format($total_finishing_reject_qnty,0); ?></th>
						<th width="70" align="right" id="total_packing_finishing"><? echo number_format($total_packing_finishing,0); ?></th>
						<th width="70" align="right" id="total_inspection_qnty"><? echo number_format($total_inspection_qnty,0); ?></th>
						<th width="70" align="right" id="total_delivery_qnty"><? echo number_format($total_delivery_qnty,0); ?></th>
					</tr>
				</tfoot>
			</table>
			<br clear="all"><br clear="all">
			<!-- ========= Summery Part ======== -->
			<div style="width:300px; float:left; background-color:#FCF"><strong>Production-Summery</strong></div>
			<br clear="all">
				<table id="report_container2" align="left" class="rpt_table" width="1390" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
					<thead>
						<tr height="50">
							<th width="40">Sl.</th>
							<th width="100">Buyer Name</th>
							<th width="70">Job No</th>
							<th width="100">Style</th>
							<th width="70">Brand</th>
							<th width="100">Season-Year</th>

							<th width="70">Cut & Lay Qty</th>
							<th width="70">QC Pass Qty.</th>
							<th width="70">Defect Qty.</th>
							<th width="70">Sewing Input</th>
							<th width="70">Sewing Output</th>
							<th width="70">Sewing Defect</th>
							<th width="70">Wash Send</th>
							<th width="70">Wash Rcv.</th>
							<th width="70">Finishing</th>
							<th width="70">Finishing Defect</th>
							<th width="70">Packing</th>
							<th width="70">Buyer Insp.</th>
							<th width="70">Gmts. Delivery</th>
						</tr>
					</thead>
				</table>
				<div style=" max-height:300px; overflow-y:scroll; width:1410px;float: left;"  align="left" id="scroll_body_smry">
					<table align="left" class="rpt_table" width="1390" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
					<tbody>
						<?
						$i =1;
						foreach ($style_data_array as $job_key=>$job_data)
						{
							foreach ($job_data as $style_key=>$row)
							{
								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							 	?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
									<td width="40"><? echo $i; ?></td>
									<td width="100" style="word-break:break-all"><? echo $buyer_short_library[$row['BUYER_NAME']]; ?></td>
									<td width="70" style="word-break:break-all"><? echo $job_key; ?></td>
									<td width="100" style="word-break:break-all"><? echo $row['STYLE_REF_NO']; ?></td>
									<td width="70" style="word-break:break-all"><? echo $buyer_brand_arr[$row['BRAND_ID']]; ?></td>
									<td width="100" style="word-break:break-all"><? echo $buyer_season_arr[$row['SEASON_BUYER_WISE']]." - ". $row['SEASON_YEAR']; ?></td>

									<td width="70" align="right"><? echo number_format( $row['CUTTING_QTY'],0); ?></td>

									<td width="70" align="right"><? echo number_format($row['CUTTING_QC_QNTY'],0); ?></td>
									<td width="70" align="right"><? echo number_format($row['REJECT_QNTY'],0); ?></td>
									<td width="70" align="right"><? echo number_format($row['SEWING_INPUT'],0); ?></td>
									<td width="70" align="right"><? echo number_format($row['SEWING_OUTPUT'],0); ?></td>
									<td width="70" align="right"><? echo number_format($defect_smry_data_array[$job_key][$style_key]['SEWING_DEFECT_QNTY'],0); ?></td>
									<td width="70" align="right"><? echo number_format($row['WASH_SEND'],0); ?></td>
									<td width="70" align="right"><? echo number_format($row['WASH_RECEIVED'],0); ?></td>
									<td width="70" align="right"><? echo number_format($row['FINISHING_QNTY'],0); ?></td>
									<td width="70" align="right"><? echo number_format($defect_smry_data_array[$job_key][$style_key]['FINISHING_DEFECT_QNTY'],0); ?></td>
									<td width="70" align="right"><? echo number_format($row['PACKING_FINISHING'],0); ?></td>

									<td width="70" align="right"><? echo number_format($inspection_smry_data_array[$job_key][$style_key],0); ?></td>
									<td width="70" align="right"><? echo number_format($row['EX_FACTORY_QNTY'],0); ?></td>

								</tr>
							 	<?

								$i++;
								$buyer_total_cutting_qty           += $row['CUTTING_QTY'];
								$buyer_total_cutting_qc_qty        += $row['CUTTING_QC_QNTY'];

								$buyer_total_reject_qty            += $row['REJECT_QNTY'];
								$buyer_total_sewing_input          += $row['SEWING_INPUT'];
								$buyer_total_sewing_output         += $row['SEWING_OUTPUT'];
								// $buyer_total_sewing_reject_qty     += $row['SEWING_REJECT_QTY'];
							    $buyer_total_sewing_reject_qty     += $defect_smry_data_array[$job_key][$style_key]['SEWING_DEFECT_QNTY'];
								$buyer_total_wash_send             += $row['WASH_SEND'];
								$buyer_total_wash_received         += $row['WASH_RECEIVED'];
								$buyer_total_finishing_qty         += $row['FINISHING_QNTY'];
								// $buyer_total_finishing_reject_qnty += $row['FINISHING_REJECT_QNTY'];
								$buyer_total_finishing_reject_qnty += $defect_smry_data_array[$job_key][$style_key]['FINISHING_DEFECT_QNTY'];
								$buyer_total_packing_finishing     += $row['PACKING_FINISHING'];

								$buyer_total_inspection_qnty       += $row['INS_QTY'];
							    $buyer_total_delivery_qnty         += $row['EX_FACTORY_QNTY'];
							}
						}
						 ?>
						</tbody>
					</table>
				</div>
				<table align="left" class="rpt_table" width="1390" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
					 <tfoot>
						<tr>
						    <th width="40"></th>
							<th width="100"></th>
							<th width="70"></th>
							<th width="100"></th>
							<th width="70"></th>
							<th width="100">Total</th>
							<th width="70" style="font-weight:bold;" align="right"><? echo number_format($buyer_total_cutting_qty,0); ?></th>
							<th width="70" style="font-weight:bold;" align="right"><? echo number_format($buyer_total_cutting_qc_qty,0); ?></th>

							<th width="70" style="font-weight:bold;" align="right"><? echo number_format($buyer_total_reject_qty,0); ?></th>
							<th width="70" style="font-weight:bold;" align="right"><? echo number_format($buyer_total_sewing_input,0); ?></th>
							<th width="70" style="font-weight:bold;" align="right"><? echo number_format($buyer_total_sewing_output,0); ?></th>
							<th width="70" style="font-weight:bold;" align="right"><? echo number_format($buyer_total_sewing_reject_qty,0); ?></th>
							<th width="70" style="font-weight:bold;" align="right"><? echo number_format($buyer_total_wash_send,0); ?></th>
							<th width="70" style="font-weight:bold;" align="right"><? echo number_format($buyer_total_wash_received,0); ?></th>
							<th width="70" style="font-weight:bold;" align="right"><? echo number_format($buyer_total_finishing_qty,0); ?></th>
							<th width="70" style="font-weight:bold;" align="right"><? echo number_format($buyer_total_finishing_reject_qnty,0); ?></th>
							<th width="70" style="font-weight:bold;" align="right"><? echo number_format($buyer_total_packing_finishing ,0); ?></th>

							<th width="70" style="font-weight:bold;" align="right"><? echo number_format($buyer_total_inspection_qnty,0); ?></th>
							<th width="70" style="font-weight:bold;" align="right"><? echo number_format($buyer_total_delivery_qnty,0); ?></th>


						</tr>
					</tfoot>
			</table>
			<br />
		</fieldset>

	<?

	$html = ob_get_contents();
	ob_clean();
	//$new_link=create_delete_report_file( $html, 1, 1, "../../../" );

	// echo "$html####";
	foreach (glob($user_id."_*.xls") as $filename)
	{
		@unlink($filename);
	}
	$name=$user_id."_".time().".xls";
	$create_new_excel = fopen($name, 'w');
	$is_created = fwrite($create_new_excel,$html);
	//$new_link=create_delete_report_file( $html, 1, 1, "../../../" );
	echo $html."####".$name;
	exit();
}

if($action=="orderQnty_popup")
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);

	//$sql= "SELECT b.id,b.po_number,b.pub_shipment_date,b.po_quantity*(select from wo_po_details_mas_set_details set where set.job_no=a.job_no and set.gmts_item_id=$gmts_item_id) as po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id='$po_break_down_id' and a.garments_nature='$garments_nature' and a.is_deleted=0 and a.status_active=1";
	//echo $sql;
	echo "<br />". create_list_view ( "list_view", "Order No,Order Qnty,Pub Shipment Date", "200,120,220","540","220",1, "SELECT b.id,b.po_number,b.pub_shipment_date,b.po_quantity*a.total_set_qnty as po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id='$po_break_down_id' and a.is_deleted=0 and a.status_active=1", "", "","", 1, '0,0,0', $arr, "po_number,po_quantity,pub_shipment_date","../requires/date_wise_prod_without_cm_report_controller", '','0,1,3');
	exit();
}

if($action=='date_wise_production_report')
{
	extract($_REQUEST);
 	echo load_html_head_contents("Remarks", "../../../", 1, 1,$unicode,'','');
 ?>
 <div id="view_part" class="view_part">
	<fieldset style="width:505px">
    <legend>Cutting</legend>
    	<?
    		$i=1;
			 $sql= "SELECT a.id,a.production_date,sum(b.production_qnty) as production_quantity,a.remarks from pro_garments_production_mst  a,pro_garments_production_dtls b where  a.id=b.mst_id and b.is_deleted=0 and b.status_active=1  and  a.po_break_down_id='$po_break_down_id' and a.production_type='1' and b.production_type='1' and a.is_deleted=0 and a.status_active=1 group by a.id,a.production_date,a.remarks ";
			 $result=sql_select($sql);
 			 $avg_prod_qty="";

			?>
	<table width="500" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
		<thead>
			<th>SL No</th>
			<th>Date</th>
			<th>Production Qnty</th>
			<th>Remarks</th>
		</thead>
		<?
		foreach($result as $row)
		{
			?>
			<tr>
				<td width="50"><? echo $i;?></td>
				<td width="100"><? echo date('d-m-Y',strtotime($row[csf('production_date')]));?></td>
				<td width="120"><p align="right"><? echo $row[csf('production_quantity')];?></p></td>
				<td>
				<? echo $row[csf('remarks')];
					$avg_prod_qty+=$row[csf('production_quantity')];
				?>
				</td>
			</tr>
			<?
			$i++;
		}
		?>
		<tfoot>
			<th align="right" colspan="2">Total</th>
			<th align=""><? echo $avg_prod_qty; ?></th>
		</tfoot>
	</table>
    </fieldset>


    <fieldset style="width:505px">
    <legend>Print/Embr Issue</legend>
    	<?
    		$i=1;
			 $sql= "SELECT a.id,a.production_date,sum(b.production_qnty) as production_quantity,a.remarks from pro_garments_production_mst  a,pro_garments_production_dtls b where  a.id=b.mst_id and b.is_deleted=0 and b.status_active=1  and a.po_break_down_id='$po_break_down_id' and a.production_type='2' and b.production_type='2' and a.is_deleted=0 and a.status_active=1 group by  a.id,a.production_date,a.remarks";
			 $result=sql_select($sql);
 			 $avg_prod_qty="";

			?>
	<table width="500" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
		<thead>
			<th>SL No</th>
			<th>Date</th>
			<th>Production Qnty</th>
			<th>Remarks</th>
		</thead>
		<?
		foreach($result as $row)
		{
			?>
			<tr>
				<td width="50"><? echo $i;?></td>
				<td width="100"><? echo date('d-m-Y',strtotime($row[csf('production_date')]));?></td>
				<td width="120"><p align="right"><? echo $row[csf('production_quantity')];?></p></td>
				<td>
				<? echo $row[csf('remarks')];
					$avg_prod_qty+=$row[csf('production_quantity')];
				?>
				</td>
			</tr>
			<?
			$i++;
		}
		?>
		<tfoot>
			<th align="right" colspan="2">Total</th>
			<th align=""><? echo $avg_prod_qty; ?></th>
		</tfoot>
	</table>
    </fieldset>

    <fieldset style="width:505px">
    <legend>Print/Embr Receive</legend>
    	<?
    		$i=1;
			 $sql= "SELECT a.id,a.production_date,sum(b.production_qnty) as production_quantity,a.remarks from pro_garments_production_mst  a,pro_garments_production_dtls b where  a.id=b.mst_id and b.is_deleted=0 and b.status_active=1 and b.production_type='3' and a.po_break_down_id='$po_break_down_id' and a.production_type='3' and a.is_deleted=0 and a.status_active=1 group by a.id,a.production_date,a.remarks";
			 $result=sql_select($sql);
 			 $avg_prod_qty="";

			?>
	<table width="500" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
		<thead>
			<th>SL No</th>
			<th>Date</th>
			<th>Production Qnty</th>
			<th>Remarks</th>
		</thead>
		<?
		foreach($result as $row)
		{
			?>
			<tr>
				<td width="50"><? echo $i;?></td>
				<td width="100"><? echo date('d-m-Y',strtotime($row[csf('production_date')]));?></td>
				<td width="120"><p align="right"><? echo $row[csf('production_quantity')];?></p></td>
				<td>
				<? echo $row[csf('remarks')];
					$avg_prod_qty+=$row[csf('production_quantity')];
				?>
				</td>
			</tr>
			<?
			$i++;
		}
		?>
		<tfoot>
			<th align="right" colspan="2">Total</th>
			<th align=""><? echo $avg_prod_qty; ?></th>
		</tfoot>
	</table>

    </fieldset>

    <fieldset style="width:505px">
    <legend>Sewing Input</legend>
    	<?
    		$i=1;
			 $sql= "SELECT a.id,a.production_date,sum(b.production_qnty) as production_quantity,a.remarks from pro_garments_production_mst  a,pro_garments_production_dtls b where  a.id=b.mst_id and b.is_deleted=0 and b.status_active=1 and b.production_type='4' and  a.po_break_down_id='$po_break_down_id' and a.production_type='4' and a.is_deleted=0 and a.status_active=1 group by a.id,a.production_date,a.remarks";
			 $result=sql_select($sql);
 			 $avg_prod_qty="";

			?>
	<table width="500" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
		<thead>
			<th>SL No</th>
			<th>Date</th>
			<th>Production Qnty</th>
			<th>Remarks</th>
		</thead>
		<?
		foreach($result as $row)
		{
			?>
			<tr>
				<td width="50"><? echo $i;?></td>
				<td width="100"><? echo date('d-m-Y',strtotime($row[csf('production_date')]));?></td>
				<td width="120"><p align="right"><? echo $row[csf('production_quantity')];?></p></td>
				<td>
				<? echo $row[csf('remarks')];
					$avg_prod_qty+=$row[csf('production_quantity')];
				?>
				</td>
			</tr>
			<?
			$i++;
		}
		?>
		<tfoot>
			<th align="right" colspan="2">Total</th>
			<th align=""><? echo $avg_prod_qty; ?></th>
		</tfoot>
	</table>
    </fieldset>

    <fieldset style="width:505px">
    <legend>Sewing Output</legend>
    	<?
			 /* $sql= "SELECT production_date,production_quantity,remarks from pro_garments_production_mst where po_break_down_id='$po_break_down_id'  and production_type='5' and is_deleted=0 and status_active=1";

			 echo  create_list_view ( "list_view_5", "Date,Production Qnty,Remarks", "100,120,280","500","220",1, $sql, "", "","", 1, '0,0,0', $arr, "production_date,production_quantity,remarks", "../requires/order_wise_production_report_controller", '','3,1,0');*/
		?>

		<?
    		$i=1;
    		 $sql= "SELECT a.production_date,sum(b.production_qnty) as production_quantity,a.remarks from pro_garments_production_mst  a,pro_garments_production_dtls b where  a.id=b.mst_id and b.is_deleted=0 and b.status_active=1 and b.production_type='5' and po_break_down_id='$po_break_down_id'  and a.production_type='5' and a.is_deleted=0 and a.status_active=1 group by a.production_date,a.remarks";

			 $result=sql_select($sql);
 			 $avg_prod_qty="";

			?>
	<table width="500" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
		<thead>
			<th>SL No</th>
			<th>Date</th>
			<th>Production Qnty</th>
			<th>Remarks</th>
		</thead>
		<?
		foreach($result as $row)
		{
			?>
			<tr>
				<td width="50"><? echo $i;?></td>
				<td width="100"><? echo date('d-m-Y',strtotime($row[csf('production_date')]));?></td>
				<td width="120"><p align="right"><? echo $row[csf('production_quantity')];?></p></td>
				<td>
				<? echo $row[csf('remarks')];
					$avg_prod_qty+=$row[csf('production_quantity')];
				?>
				</td>
			</tr>
			<?
			$i++;
		}
		?>
		<tfoot>
			<th align="right" colspan="2">Total</th>
			<th align=""><? echo $avg_prod_qty; ?></th>
		</tfoot>
	</table>
    </fieldset>

    <fieldset style="width:505px">
    <legend>Iron Qty.</legend>
    	<?
    		$i=1;
			 $sql= "SELECT a.id,a.production_date,sum(b.production_qnty) as production_quantity,a.remarks from pro_garments_production_mst  a,pro_garments_production_dtls b where  a.id=b.mst_id and b.is_deleted=0 and b.status_active=1 and b.production_type='7' and   a.po_break_down_id='$po_break_down_id' and a.production_type='7'  and a.is_deleted=0 and a.status_active=1 group by a.id,a.production_date,a.remarks";
			 $result=sql_select($sql);
 			 $avg_prod_qty="";

			?>
	<table width="500" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
		<thead>
			<th>SL No</th>
			<th>Date</th>
			<th>Production Qnty</th>
			<th>Remarks</th>
		</thead>
		<?
		foreach($result as $row)
		{
			?>
			<tr>
				<td width="50"><? echo $i;?></td>
				<td width="100"><? echo date('d-m-Y',strtotime($row[csf('production_date')]));?></td>
				<td width="120"><p align="right"><? echo $row[csf('production_quantity')];?></p></td>
				<td>
				<? echo $row[csf('remarks')];
					$avg_prod_qty+=$row[csf('production_quantity')];
				?>
				</td>
			</tr>
			<?
			$i++;
		}
		?>
		<tfoot>
			<th align="right" colspan="2">Total</th>
			<th align=""><? echo $avg_prod_qty; ?></th>
		</tfoot>
	</table>
    </fieldset>

    <fieldset style="width:505px">
    <legend>Finish Output</legend>
    	<?
    		$i=1;
			 $sql= "SELECT a.id,a.production_date,sum(b.production_qnty) as production_quantity,a.remarks from pro_garments_production_mst  a,pro_garments_production_dtls b where  a.id=b.mst_id and b.is_deleted=0 and b.status_active=1 and b.production_type='8' and   a.po_break_down_id='$po_break_down_id' and a.production_type='8'  and a.is_deleted=0 and a.status_active=1 group by a.id,a.production_date,a.remarks";
			 $result=sql_select($sql);
 			 $avg_prod_qty="";

			?>
	<table width="500" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
		<thead>
			<th>SL No</th>
			<th>Date</th>
			<th>Production Qnty</th>
			<th>Remarks</th>
		</thead>
		<?
		foreach($result as $row)
		{
			?>
			<tr>
				<td width="50"><? echo $i;?></td>
				<td width="100"><? echo date('d-m-Y',strtotime($row[csf('production_date')]));?></td>
				<td width="120"><p align="right"><? echo $row[csf('production_quantity')];?></p></td>
				<td>
				<? echo $row[csf('remarks')];
					$avg_prod_qty+=$row[csf('production_quantity')];
				?>
				</td>
			</tr>
			<?
			$i++;
		}
		?>
		<tfoot>
			<th align="right" colspan="2">Total</th>
			<th align=""><? echo $avg_prod_qty; ?></th>
		</tfoot>
	</table>
    </fieldset>
 </div>

 <div id="view_part2"></div>


 	<script type="text/javascript">
		//var contents=contents.trim();
	    document.getElementById('view_part2').innerHTML='<input type="button" onclick="new_window()" value="Print" name="Print" class="formbutton" style="width:100px;margin-left:200px;"/>';


		function new_window()
	    {

	        var w = window.open("Surprise", "#");
	        var d = w.document.open();
	        d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	    '<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body style="font-size:12px; font-family:Arial Narrow">'+document.getElementById('view_part').innerHTML+'</body</html>');
	        d.close();
	    }

 	</script>
	<?
}//end if

//cutting popup
if($action=='cutting_popup_bk')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );


	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	//and c.status_active=1 and c.is_deleted=0
	if ($location==0) $location_cond=""; else $location_cond=" and location in(".$location.") ";
	if ($floor_id==0 || $floor_id =="") $floor_cond=""; else $floor_cond=" and a.floor_id=".$floor_id." ";

	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=1 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and  a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $location_cond  $floor_cond
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;
	?>
	<script>
		function window_close()
		{
			parent.emailwindow.hide();
		}
	</script>
	<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
	    <div style="100%" id="report_container">

	        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
	        	<tr>
	            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise cutting production</td>
	            </tr>
	        	<tr>
	            	<td style="font-size:16px; font-weight:bold;">
	                Date : <? echo change_date_format($production_date); ?>
	                <br />
	                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
	                </td>
	            </tr>
	        </table>

	        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
	            <thead>
	                <tr>
	                	<th width="40" rowspan="2">SI</th>
	                    <th width="100" rowspan="2">Color</th>
	                    <th width="100" rowspan="2">Country Name</th>
	                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
	                    <th width="80" rowspan="2" >Total</th>
	                </tr>
	                <tr>
	                <?
					$grand_tot_in=0;
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
	                    <?
					}
					?>
	                </tr>

	            </thead>
	            <tbody>
	            <?
				$i=1;
				$k=1;
				// echo "<pre>";
				// print_r($details_data);
				foreach($details_data as $color_id=>$value)
				{
					foreach($value as $country_id=>$row)
					{
						if(!in_array($color_id,$temp_arr))
						{
							$temp_arr[]=$color_id;
							if($k!=1)
							{
								?>
								<tr bgcolor="#CCCCCC">
									<td >&nbsp;</td>
									<td>&nbsp;</td>
									<td align="right" style="font-weight:bold">Color Total:</td>
									<?
									foreach($sizearr_order as $size_id)
									{
										?>
										<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
										<?
									}
									?>
									<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
								</tr>
								<?
								$line_color_size_in = $line_color_total_in ="";
							}
							$k++;
						}

						?>
						<tr>
							<td align="center"><? echo $i;  ?></td>
	                        <?
							if(!in_array($color_id,$temp_arr_color))
							{
								$temp_arr_color[]=$color_id;
								?>
								<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
								<?
							}
							?>
							<td ><p><? echo $country_library[$country_id];  ?></p></td>
							<?
							$color_total_in=0;
							foreach($sizearr_order as $size_id)
							{
								?>
								<td align="right"><p>
								<?
									echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
									 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id];
									 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
									 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 ?>
								</p></td>
								<?
							}
							$line_color_total_in+=$color_total_in;
							?>
							<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
						</tr>
						<?
						$i++;
					}

				}
				?>
	            <tr bgcolor="#CCCCCC">
	                <td >&nbsp;</td>
	                <td>&nbsp;</td>
	                <td align="right" style="font-weight:bold">Color Total:</td>
	                <?
	                foreach($sizearr_order as $size_id)
	                {
	                    ?>
	                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
	                    <?
	                }
	                ?>
	                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
	            </tr>
	            </tbody>
	            <tfoot>
	                <th >&nbsp;</th>
	                <th>&nbsp;</th>
	                <th >Day Total:</th>
	                <?
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th ><? echo $color_size_in[$size_id]; ?></th>
	                    <?
					}
					?>
	                <th ><? echo $grand_tot_in; ?></th>

	            </tfoot>
	        </table>
	        </div>
	    </fieldset>
	<?
}

if($action=='cutting_popup')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );


	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	//and c.status_active=1 and c.is_deleted=0
	if ($location==0) $location_cond=""; else $location_cond=" and location in(".$location.") ";
	if ($floor_id==0 || $floor_id =="") $floor_cond=""; else $floor_cond=" and a.floor_id=".$floor_id." ";

	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=1 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and  a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $location_cond  $floor_cond
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;
	?>
	<script>
		function window_close()
		{
			parent.emailwindow.hide();
		}
	</script>
	<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
	    <div style="100%" id="report_container">

	        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
	        	<tr>
	            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise cutting production</td>
	            </tr>
	        	<tr>
	            	<td style="font-size:16px; font-weight:bold;">
	                Date : <? echo change_date_format($production_date); ?>
	                <br />
	                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
	                </td>
	            </tr>
	        </table>

	        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
	            <thead>
	                <tr>
	                	<th width="40" rowspan="2">SI</th>
	                    <th width="100" rowspan="2">Color</th>
	                    <th width="100" rowspan="2">Country Name</th>
	                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
	                    <th width="80" rowspan="2" >Total</th>
	                </tr>
	                <tr>
	                <?
					$grand_tot_in=0;
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
	                    <?
					}
					?>
	                </tr>

	            </thead>
	            <tbody>
	            <?
				$i=1;
				$k=1;
				// echo "<pre>";
				// print_r($details_data);
				foreach($details_data as $color_id=>$value)
				{
					foreach($value as $country_id=>$row)
					{

						?>
						<tr>
							<td align="center"><? echo $i;  ?></td>
	                        <?

								$temp_arr_color[$color_id]=$color_id;
								?>
								<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="left" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
								<?

							?>
							<td ><p><? echo $country_library[$country_id];  ?></p></td>
							<?
							$color_total_in=0;
							foreach($sizearr_order as $size_id)
							{
								?>
								<td align="right"><p>
								<?
									echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
									 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id];
									 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
									 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 ?>
								</p></td>
								<?
							}
							$line_color_total_in+=$color_total_in;
							?>
							<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
						</tr>
						<?
						$i++;
					}
					?>

		            <tr bgcolor="#CCCCCC">
		                <td >&nbsp;</td>
		                <td>&nbsp;</td>
		                <td align="right" style="font-weight:bold">Color Total:</td>
		                <?
		                foreach($sizearr_order as $size_id)
		                {
		                    ?>
		                    <td align="right"><? echo number_format($line_color_size_in[$size_id],0); ?></td>
		                    <?
		                }
		                ?>
		                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
		            </tr>
		            <?
				}
				?>
	            </tbody>
	            <tfoot>
	                <th >&nbsp;</th>
	                <th>&nbsp;</th>
	                <th >Day Total:</th>
	                <?
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th ><? echo $color_size_in[$size_id]; ?></th>
	                    <?
					}
					?>
	                <th ><? echo $grand_tot_in; ?></th>

	            </tfoot>
	        </table>
	        </div>
	    </fieldset>
	<?
}

if($action=='popup_bound')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
 	// var_dump($_REQUEST);
 	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
 	$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  );
	$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  );
	$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );

	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );


	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	$sql_job = "SELECT a.job_no,a.buyer_name,b.id as po_id,a.style_ref_no FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no = b.job_no_mst and b.id=$po_break_down_id";
	$sql_job_res = sql_select($sql_job);
	foreach ($sql_job_res as $key => $value) {
		$job_array[$value[csf('po_id')]]['job'] = $value[csf('job_no')];
		$job_array[$value[csf('po_id')]]['buyer_name'] = $value[csf('buyer_name')];
		$job_array[$value[csf('po_id')]]['style'] = $value[csf('style_ref_no')];
	}

	if ($location==0) $location_cond=""; else $location_cond=" and location in(".$location.") ";
	if ($floor_id==0 || $floor_id =="") $floor_cond=""; else $floor_cond=" and a.floor_id=".$floor_id." ";
	// if ($size_id==0 || $size_id =="") $production_source_cond=" and a.production_source in(1,3) and c.color_number_id=$color_id "; else $production_source_cond=" and a.production_source=".$size_id." ";

	$sum_qry = "";
	switch ($proces) {
		case 'cutting':
			if($type==1)
			{
				$sum_qry = 'sum(case when a.production_source=1 and b.production_type=1 and a.production_type=1 then b.production_qnty else 0 end) as production_qnty,';
			}
			else
			{
				$sum_qry = 'sum(case when a.production_source=3 and b.production_type=1 and a.production_type=1 then b.production_qnty else 0 end) as production_qnty,';
			}
			break;

		case 'print':
			if($type==1)
			{
				$sum_qry = 'sum(case when a.production_source in(1,3) and a.embel_name = 1 and a.production_type=2 then b.production_qnty else 0 end) as production_qnty,';
			}
			else
			{
				$sum_qry = 'sum(case when a.production_source in(1,3) and a.embel_name = 1 and a.production_type=3 then b.production_qnty else 0 end) as production_qnty,';
			}
			break;

		case 'embroidery':
			if($type==1)
			{
				$sum_qry = 'sum(case when b.production_type=2 and a.production_source in(1,3) and a.embel_name=2  then b.production_qnty else 0 end ) as production_qnty,';
			}
			else
			{
				$sum_qry = 'sum(case when b.production_type=3 and a.production_source in(1,3)  and a.embel_name=2 then b.production_qnty else 0 end ) as production_qnty,';
			}
			break;

		case 'wash':
			if($type==1)
			{
				$sum_qry = 'sum(case when b.production_type=2 and a.production_source in(1,3) and a.embel_name=3  then b.production_qnty else 0 end ) as production_qnty,';
			}
			else
			{
				$sum_qry = 'sum(case when b.production_type=3 and a.production_source in(1,3)  and a.embel_name=3 then b.production_qnty else 0 end ) as production_qnty,';
			}
			break;

		case 'sp':
			if($type==1)
			{
				$sum_qry = 'sum(case when b.production_type=2 and a.production_source in(1,3) and a.embel_name=4  then b.production_qnty else 0 end ) as production_qnty,';
			}
			else
			{
				$sum_qry = 'sum(case when b.production_type=3 and a.production_source in(1,3)  and a.embel_name=4 then b.production_qnty else 0 end ) as production_qnty,';
			}
			break;

		case 'sewingin':
			if($type==1)
			{
				$sum_qry = 'sum(case when a.production_source=1 and b.production_type=4 then b.production_qnty else 0 end) as production_qnty,';
			}
			else
			{
				$sum_qry = 'sum(case when a.production_source=3 and b.production_type=4 then b.production_qnty else 0 end) as production_qnty,';
			}
			break;

		case 'sewingout':
			if($type==1)
			{
				$sum_qry = 'sum(case when a.production_source=1 and b.production_type=5 then b.production_qnty else 0 end) as production_qnty,';
			}
			else
			{
				$sum_qry = 'sum(case when a.production_source=3 and b.production_type=5 then b.production_qnty else 0 end) as production_qnty,';
			}
			break;

		case 'iron':
			if($type==1)
			{
				$sum_qry = 'sum(case when a.production_source=1 and b.production_type=7 then b.production_qnty else 0 end) as production_qnty,';
			}
			else
			{
				$sum_qry = 'sum(case when a.production_source=3 and b.production_type=7 then b.production_qnty else 0 end) as production_qnty,';
			}
			break;

		case 'finish':
			if($type==1)
			{
				$sum_qry = 'sum(case when a.production_source=1 and b.production_type=8 then b.production_qnty else 0 end) as production_qnty,';
			}
			else
			{
				$sum_qry = 'sum(case when a.production_source=3 and b.production_type=8 then b.production_qnty else 0 end) as production_qnty,';
			}
			break;

		default:
			$sum_qry = "";
			break;
	}

	$sql_color_size= "SELECT a.country_id,a.production_source,a.challan_no,a.sewing_line,a.floor_id, $sum_qry c.color_number_id, c.size_number_id, c.job_no_mst
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.production_source IN(1,3)  and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and  a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $location_cond  $floor_cond
	group by   c.color_number_id,a.country_id,a.production_source,a.challan_no,a.sewing_line,a.floor_id,c.size_number_id, c.job_no_mst
	order by c.color_number_id,a.country_id";
	// echo $sql_color_size;
	$res = sql_select($sql_color_size);
	//and c.color_number_id=$color_id and c.size_number_id=$size_id
	foreach($res as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]['country_id']=$row[csf('country_id')];
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]['production_source']=$row[csf('production_source')];
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]['challan_no']=$row[csf('challan_no')];
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]['sewing_line']=$row[csf('sewing_line')];
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]['floor_id']=$row[csf('floor_id')];

		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
		// $comon_arr[]
	}
	// echo "<pre>";
	// print_r($details_data);die();
	$col_width=60*count($sizearr_order);
	$table_width=230+$col_width;
	?>
	<script>
		function window_close()
		{
			parent.emailwindow.hide();
		}
	</script>
	<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
	    <div style="100%" id="report_container">

	        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
	        	<tr>
	            	<td width="100%" colspan="2" align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise  production</td>
	            </tr>
	        	<tr>
	            	<td width="50%" style="font-size:14px; font-weight:bold;">
	                Buyer Name : <? echo $buyer_library[$job_array[$po_break_down_id]['buyer_name']]; ?>
	                </td>
	                <td width="50%"  style="font-size:14px; font-weight:bold;">
	                Job No : <? echo $job_array[$po_break_down_id]['job']; ?>&nbsp;&nbsp;&nbsp;&nbsp;
	                </td>
	            </tr>
	            <tr>
	            	<td width="50%" style="font-size:14px; font-weight:bold;">
	                Style No : <? echo $job_array[$po_break_down_id]['style']; ?>
	                </td>
	                <td width="50%"  style="font-size:14px; font-weight:bold;">
	                Garments Item : <? echo $garments_item[$gmts_item_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
	                </td>
	            </tr>
	            <tr>
	                <td width="50%"  style="font-size:14px; font-weight:bold;">
	                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
	                </td>
	            	<td width="50%" style="font-size:14px; font-weight:bold;">
	                Date : <? echo change_date_format($production_date); ?>
	                </td>
	            </tr>
	        </table>
	        <div><p><b>Summary : </b></p></div>
	        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
	            <thead>
	                <tr>
	                	<th width="40" rowspan="2">SI</th>
	                    <th width="100" rowspan="2">Color</th>
	                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
	                    <th width="80" rowspan="2" >Total</th>
	                </tr>
	                <tr>
	                <?
					$grand_tot_in=0;
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
	                    <?
					}
					?>
	                </tr>

	            </thead>
	            <tbody>
	            <?
				$i=1;
				$k=1;

				foreach($details_data as $color_id=>$value)
				{
					foreach($value as $country_id=>$row)
					{
						?>
						<tr>
							<td align="center"><? echo $i;  ?></td>
	                        <?
							$temp_arr_color[]=$color_id;
							?>
							<td rowspan="<? //echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p>
							</td>
							<?
							$color_total_in=0;
							foreach($sizearr_order as $size_id)
							{
								?>
								<td align="right"><p>
								<?
									echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
									 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id];
									 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
									 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 ?>
								</p></td>
								<?
							}
							$line_color_total_in+=$color_total_in;
							?>
							<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
						</tr>
						<?
						$i++;
					}

				}
				?>
	            <tr bgcolor="#CCCCCC">
	                <td >&nbsp;</td>
	                <td align="right" style="font-weight:bold">Color Total:</td>
	                <?
	                foreach($sizearr_order as $size_id)
	                {
	                    ?>
	                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
	                    <?
	                }
	                ?>
	                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
	            </tr>
	            </tbody>
	            <tfoot>
	                <th >&nbsp;</th>
	                <th >Day Total:</th>
	                <?
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th ><? echo $color_size_in[$size_id]; ?></th>
	                    <?
					}
					?>
	                <th ><? echo $grand_tot_in; ?></th>

	            </tfoot>
	        </table>
	        <!-- =============================== Details ===================================== -->
			<div><p><b>Details : </b></p></div>
	         <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo 500+$table_width; ?>">
	            <thead>
	                <tr>
	                	<th width="40" rowspan="2">SI</th>
	                    <th width="100" rowspan="2">Country</th>
	                    <th width="100" rowspan="2">Source</th>
	                    <th width="100" rowspan="2">Chalan</th>
	                    <th width="100" rowspan="2">Sewing Unit</th>
	                    <th width="100" rowspan="2">Sewing Line</th>
	                    <th width="100" rowspan="2">Color</th>
	                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
	                    <th width="80" rowspan="2" >Total</th>
	                </tr>
	                <tr>
	                <?
					$grand_tot_in=0;
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
	                    <?
					}
					?>
	                </tr>

	            </thead>
	            <tbody>
	            <?
				$i=1;
				$k=1;
				// echo "<pre>";
				// print_r($details_data);
				foreach($details_data as $color_id=>$value)
				{
					foreach($value as $country_id=>$row)
					{
						?>
						<tr>
							<td align="center"><? echo $i;  ?></td>

							<td rowspan="<? //echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $country_library[$country_id];  ?></p>
							<td rowspan="<? //echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $knitting_source[$row['production_source']];  ?></p>
							<td rowspan="<? //echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $row['challan_no'];  ?></p>
							<td rowspan="<? //echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $floor_library[$row['floor_id']];  ?></p>
							<td rowspan="<? //echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $row['sewing_line'];  ?></p>
							<td rowspan="<? //echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p>
							</td>
							<?
							$color_total_in=0;
							foreach($sizearr_order as $size_id)
							{
								?>
								<td align="right"><p>
								<?
									echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
									 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id];
									 $color_size_in2[$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
									 $line_color_size_in2[$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 ?>
								</p></td>
								<?
							}
							$line_color_total_in2+=$color_total_in;
							?>
							<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
						</tr>
						<?
						$i++;
					}

				}
				?>
	            <tr bgcolor="#CCCCCC">
	                <td >&nbsp;</td>
	                <td >&nbsp;</td>
	                <td >&nbsp;</td>
	                <td >&nbsp;</td>
	                <td >&nbsp;</td>
	                <td >&nbsp;</td>
	                <td align="right" style="font-weight:bold">&nbsp;</td>
	                <?
	                foreach($sizearr_order as $size_id)
	                {
	                    ?>
	                    <td align="right"><? echo number_format($line_color_size_in2[$size_id],0); ?></td>
	                    <?
	                }
	                ?>
	                <td align="right"><? echo number_format($line_color_total_in2,0); ?></td>
	            </tr>
	            </tbody>
	            <tfoot>
	                <th >&nbsp;</th>
	                <th >&nbsp;</th>
	                <th >&nbsp;</th>
	                <th >&nbsp;</th>
	                <th >&nbsp;</th>
	                <th >&nbsp;</th>
	                <th >Day Total:</th>
	                <?
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th ><? echo $color_size_in[$size_id]; ?></th>
	                    <?
					}
					?>
	                <th ><? echo $grand_tot_in; ?></th>

	            </tfoot>
	        </table>
	        </div>
	    </fieldset>
	<?
}


if ($action=='gmt_finishing_receive')  // Finishing Rcv Popup
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,1,1);
       		$item_arr=return_library_array( "select id, item_name from lib_garment_item",'id','item_name');
				$company_short_arr=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name"  );
				$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
				$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
				$floor_arr=return_library_array( "select id, floor_name from lib_prod_floor",'id','floor_name');
				$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
				$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
        	   $order_cond= where_con_using_array(array_unique(explode(",",$po_break_down_id)),0,"c.id") ;

        	    $txt_date=" and a.receive_date='$rcv_date'";
        	    $item_id_cond=" and d.item_id='$item_id'";

        		$sql="SELECT d.id,
				       a.sys_no,
				       b.job_no,
				       b.style_ref_no,
				       c.po_number,
				       c.grouping,
				       a.company_id,
				       d.location_id,
				       d.floor_id,
				       d.qc_pass_qnty,
				       d.fin_receive_qnty,
				       d.po_break_down_id,
				       d.lc_company_id,
				       b.buyer_name,
				       a.fini_location_id,
				       a.floor_id AS fin_floor_id,
				       c.shipment_date,
				       c.po_quantity,
				       b.gmts_item_id,
				       a.receive_date,
				       d.color_id,
   					   d.size_id,
   					   a.sys_number_prefix_num,
   					   d.item_id
				  FROM gmt_finishing_receive_mst a,
				       wo_po_details_master b,
				       wo_po_break_down c,
				       gmt_finishing_receive_dtls d
				 WHERE     a.id = d.mst_id
				       AND d.po_break_down_id = c.id
				       AND b.job_no = c.job_no_mst
				       AND a.status_active = 1
				       AND d.status_active = 1
				       AND c.status_active = 1
				       AND d.status_active = 1
				       AND a.company_id = $company_id
				       $order_cond
				        $txt_date
				       $item_id_cond

				";
			//echo $sql;
			$result=sql_select($sql);
			$po_wise_data=array();
			$color_wise_data=array();
			$size_id_arr=array();
			$buyer='';
			$job_no='';
			$buyer='';
			$po_number='';
			$gmts_item_id='';
			$receive_date='';
			foreach ($result as $row)
			{
				$buyer.=$buyer_arr[$row[csf('buyer_name')]]."***";
				$gmts_item_id.=$item_arr[$row[csf('item_id')]]."***";
				$job_no.=$row[csf('buyer_name')]."***";
				$style_ref_no.=$row[csf('style_ref_no')]."***";
				$po_number.=$row[csf('po_number')]."***";
				$receive_date.=change_date_format($row[csf('receive_date')])."***";
				$po_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['buyer_name'].=$buyer_arr[$row[csf('buyer_name')]]."***";
				$po_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['job_no'].=$row[csf('job_no')]."***";
				$po_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['style_ref_no'].=$row[csf('style_ref_no')]."***";
				$po_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['po_number'].=$row[csf('po_number')]."***";
				$po_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['grouping'].=$row[csf('grouping')]."***";
				$po_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['gmts_item_id'].=$item_arr[$row[csf('item_id')]]."***";
				//$po_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['gmts_item_id'].=$item_arr[$row[csf('gmts_item_id')]]."***";
				$po_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['fin_floor_id'].=$floor_arr[$row[csf('fin_floor_id')]]."***";
				$po_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['sew_floor_id'].=$floor_arr[$row[csf('floor_id')]]."***";
				$po_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['fini_location_id'].=$location_arr[$row[csf('fini_location_id')]]."***";
				$po_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['fini_company'].=$company_short_arr[$row[csf('company_id')]]."***";
				$po_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['sew_comapny'].=$company_short_arr[$row[csf('lc_company_id')]]."***";
				$po_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['sew_location'].=$location_arr[$row[csf('location_id')]]."***";

				$po_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['po_quantity']+=$row[csf('po_quantity')];
				$po_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['rcv_no'].=$row[csf('sys_number_prefix_num')]."***";

				$po_size_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]][$row[csf('size_id')]]+=$row[csf('fin_receive_qnty')];
				$color_wise_data[$row[csf('color_id')]][$row[csf('size_id')]]+=$row[csf('fin_receive_qnty')];
				array_push($size_id_arr, $row[csf('size_id')]);
			}
			$size_id_arr=array_unique($size_id_arr);

 	?>

    <fieldset>
		 <div id="data_panel" align="center" style="width:100%">
	        <script>
			      function new_window()
			      {
				       var w = window.open("Surprise", "#");
				       var d = w.document.open();
				       d.write(document.getElementById('details_reports').innerHTML);
				       d.close();
			      }
	        </script>
	 		<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
		</div>
    <div style="margin:2px" id="details_reports">
    	<h4>Buyer Name : <?=implode(", ", array_unique(explode("***", chop($buyer,"***"))));?>     Job No : <?=implode(", ", array_unique(explode("***", chop($job_no,"***"))));?>     Style No : <?=implode(", ", array_unique(explode("***", chop($style_ref_no,"***"))));?>     Garments Item : <?=implode(", ", array_unique(explode("***", chop($gmts_item_id,"***"))));?>  </h4>
    	<h4>Order No : <?=implode(", ", array_unique(explode("***", chop($po_number,"***"))));?>    Date : <?=implode(", ", array_unique(explode("***", chop($receive_date,"***"))));?></h4>
    	<br>
        <table width="95%" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" >
        	<caption style="justify-content: left;text-align: left;font-weight: bold;">Summary</caption>
            <thead>
                    <tr>
                        <th width="50" rowspan="2">Sl.</th>
                        <th width="100" rowspan="2">Color</th>
                        <th colspan="<?=count($size_id_arr);?>" width="<?=count($size_id_arr)*60;?>">Size</th>

                        <th rowspan="2">Total</th>
               		</tr>
               		<tr>
               			<?
               				foreach ($size_id_arr as $size_id)
               				{
               					?>
               					<th width="60"><?=$size_arr[$size_id];?></th>
               					<?
               				}
               			?>
               		</tr>
            </thead>
            <tbody>
            	<?


				$i=1;
				$sum_total=0;
				$size_total_arr=array();
				foreach ($color_wise_data as $color_id => $color_data)
				{
					 if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

            		?>
	            	 <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                    	<td ><? echo $i;?></td>
                    	<td ><? echo $color_arr[$color_id];?></td>
                    	<?
                    		$color_total=0;
               				foreach ($size_id_arr as $size_id)
               				{
               					?>
               					<td><?=fn_number_format($color_data[$size_id],0,".",",");?></td>
               					<?
               					$sum_total+=$color_data[$size_id];
               					// $size_total_arr[$color_id]+=$color_data[$size_id];
               					$size_total_arr[$size_id]+=$color_data[$size_id];
               					$color_total+=$color_data[$size_id];
               				}
               			?>
                    	<td ><? echo fn_number_format($color_total,0,".",",");?></td>

	            	</tr>
	            	<?
	            	$i++;
	            }
	            ?>

            </tbody>
            <tfoot>
            	<tr>
            		<td></td>
            		<td></td>
        		     <?

						foreach ($size_id_arr as $size_id)
						{
							?>
							<td width="60"><?=fn_number_format($size_total_arr[$size_id],0,".",",");?></td>
							<?

						}
					?>
					<td ><? echo fn_number_format($sum_total,0,".",",");?></td>
            	</tr>
           </tfoot>
        </table>
        <br>
       <table width="95%" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" >
       		<caption style="justify-content: left;text-align: left;font-weight: bold;">Details</caption>
            <thead>
                    <tr>
                        <th width="50"  rowspan="2">Sl.</th>
                        <th width="70"  rowspan="2">Rcv ID</th>
                        <th width="110" rowspan="2">Finishing Company</th>
                        <th width="110" rowspan="2">Finishing Floor</th>
                        <th width="110" rowspan="2">Sewing Company</th>
                        <th width="110" rowspan="2">Sewing Floor</th>

                        <th width="110" rowspan="2">Color</th>
                       	<th colspan="<?=count($size_id_arr);?>" width="<?=count($size_id_arr)*60;?>">Size</th>
                         <th width="80" rowspan="2">Total</th>
               		</tr>
               		<tr>
               			<?
               				foreach ($size_id_arr as $size_id)
               				{
               					?>
               					<th width="60"><?=$size_arr[$size_id];?></th>
               					<?
               				}
               			?>
               		</tr>
            </thead>
            <tbody>
            	<?

				$i=1;
				$rcv_qnt=0;
				$sum_total=0;
				$size_total_arr=array();
				foreach ($po_wise_data as $po_id => $po_data)
				{
					foreach ($po_data as $color_id => $color_data)
					{
						# code...

						 if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

	            		?>
		            	 <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
	                    	<td ><? echo $i;?></td>

	                    	<td ><? echo  implode(",", array_unique(explode("***", chop($color_data['rcv_no'],"***"))));;?></td>
	                    	<td ><? echo  implode(",", array_unique(explode("***", chop($color_data['fini_company'],"***"))));;?></td>
	                    	<td ><? echo  implode(",", array_unique(explode("***", chop($color_data['fin_floor_id'],"***"))));;?></td>
	                    	<td ><? echo  implode(",", array_unique(explode("***", chop($color_data['sew_comapny'],"***"))));;?></td>
	                    	<td ><? echo  implode(",", array_unique(explode("***", chop($color_data['sew_floor_id'],"***"))));;?></td>
	                    	<td ><? echo $color_arr[$color_id];?></td>
                	     	<?
                	     		$color_total=0;
                					foreach ($size_id_arr as $size_id)
                					{
                						?>
                						<td><?=fn_number_format($po_size_wise_data[$po_id][$color_id][$size_id],0,".",",");?></td>
                						<?
                						$sum_total+=$po_size_wise_data[$po_id][$color_id][$size_id];
                						// $size_total_arr[$color_id]+=$po_size_wise_data[$po_id][$color_id][$size_id];
                						$size_total_arr[$size_id]+=$po_size_wise_data[$po_id][$color_id][$size_id];
                						$color_total+=$po_size_wise_data[$po_id][$color_id][$size_id];
                					}
                				?>
                			<td ><? echo fn_number_format($color_total,0,".",",");?></td>

		            	<?
		            	$i++;
		            	$rcv_qnt+=$row[csf('fin_receive_qnty')];
		            }
	            }
	            ?>
            </tbody>
            <tfoot>

        		<tr>

            		<td></td>
            		<td></td>
            		<td></td>
            		<td></td>
            		<td></td>
            		<td></td>
            		<td></td>
        		     <?

						foreach ($size_id_arr as $size_id)
						{
							?>
							<td width="60"><?=fn_number_format($size_total_arr[$size_id],0,".",",");?></td>
							<?

						}
					?>
					<td ><? echo fn_number_format($sum_total,0,".",",");?></td>
            	</tr>

            </tfoot>
        </table>
       </div>
     </div>
     </fieldset>
    <?

 exit();

}

//cutting_popup_location
if($action=='cutting_popup_location')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );
	$location=str_replace("'","",$location);
	$floor_id=str_replace("'","",$floor_id);
	$sewing_line=str_replace("'","",$sewing_line);
	$location_cond=$floor_cond=$sewing_cond="";
	if($location!="") $location_cond="and a.location=$location";
	if($floor_id!="") $floor_cond=" and a.floor_id=$floor_id";
	if($sewing_line==0 || $sewing_line=="") $sewing_cond=""; else $sewing_cond=" and a.sewing_line=$sewing_line";

	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=1 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' $location_cond $floor_cond $sewing_cond and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");

	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


	?>
	<script>

		function window_close()
		{
			parent.emailwindow.hide();
		}

	</script>
	<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
	    <div style="100%" id="report_container">

	        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
	        	<tr>
	            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise cutting production</td>
	            </tr>
	        	<tr>
	            	<td style="font-size:16px; font-weight:bold;">
	                Date : <? echo change_date_format($production_date); ?>
	                <br />
	                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
	                </td>
	            </tr>
	        </table>

	        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
	            <thead>
	                <tr>
	                	<th width="40" rowspan="2">SI</th>
	                    <th width="100" rowspan="2">Color</th>
	                    <th width="100" rowspan="2">Country Name</th>
	                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
	                    <th width="80" rowspan="2" >Total</th>
	                </tr>
	                <tr>
	                <?
					$grand_tot_in=0;
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
	                    <?
					}
					?>
	                </tr>

	            </thead>
	            <tbody>
	            <?
				$i=1;
				$k=1;
				//var_dump($result);die;
				foreach($details_data as $color_id=>$value)
				{
					foreach($value as $country_id=>$row)
					{
						if(!in_array($color_id,$temp_arr))
						{
							$temp_arr[]=$color_id;
							if($k!=1)
							{
								?>
								<tr bgcolor="#CCCCCC">
									<td >&nbsp;</td>
									<td>&nbsp;</td>
									<td align="right" style="font-weight:bold">Color Total:</td>
									<?
									foreach($sizearr_order as $size_id)
									{
										?>
										<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
										<?
									}
									?>
									<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
								</tr>
								<?
								$line_color_size_in = $line_color_total_in ="";
							}
							$k++;
						}

						?>
						<tr>
							<td align="center"><? echo $i;  ?></td>
	                        <?
							if(!in_array($color_id,$temp_arr_color))
							{
								$temp_arr_color[]=$color_id;
								?>
								<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
								<?
							}
							?>
							<td ><p><? echo $country_library[$country_id];  ?></p></td>
							<?
							$color_total_in=0;
							foreach($sizearr_order as $size_id)
							{
								?>
								<td align="right"><p>
								<?
									echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
									 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id];
									 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
									 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 ?>
								</p></td>
								<?
							}
							$line_color_total_in+=$color_total_in;
							?>
							<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
						</tr>
						<?
						$i++;
					}

				}
				?>
	            <tr bgcolor="#CCCCCC">
	                <td >&nbsp;</td>
	                <td>&nbsp;</td>
	                <td align="right" style="font-weight:bold">Color Total:</td>
	                <?
	                foreach($sizearr_order as $size_id)
	                {
	                    ?>
	                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
	                    <?
	                }
	                ?>
	                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
	            </tr>
	            </tbody>
	            <tfoot>
	                <th >&nbsp;</th>
	                <th>&nbsp;</th>
	                <th >Day Total:</th>
	                <?
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th ><? echo $color_size_in[$size_id]; ?></th>
	                    <?
					}
					?>
	                <th ><? echo $grand_tot_in; ?></th>

	            </tfoot>
	        </table>
	        </div>
	    </fieldset>
	<?
}

if($action=='printing_issue_popup')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );


	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");

	$location=str_replace("'","",$location);
	$floor_id=str_replace("'","",$floor_id);
	$sewing_line=str_replace("'","",$sewing_line);
	$location_cond=$floor_cond=$sewing_cond="";
	if($location) $location_cond="and a.location in($location)";
	if($floor_id!="") $floor_cond=" and a.floor_id in($floor_id)";
	if($sewing_line!="") $sewing_cond=" and a.sewing_line in($sewing_line)";

	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=2 and a.embel_name=1 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' $location_cond $floor_cond $sewing_cond  and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


	?>
	<script>

		function window_close()
		{
			parent.emailwindow.hide();
		}

	</script>
	<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
	    <div style="100%" id="report_container">

	        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
	        	<tr>
	            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise printing production</td>
	            </tr>
	        	<tr>
	            	<td style="font-size:16px; font-weight:bold;">
	                Date : <? echo change_date_format($production_date); ?>
	                <br />
	                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
	                </td>
	            </tr>
	        </table>

	        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
	            <thead>
	                <tr>
	                	<th width="40" rowspan="2">SI</th>
	                    <th width="100" rowspan="2">Color</th>
	                    <th width="100" rowspan="2">Country Name</th>
	                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
	                    <th width="80" rowspan="2" >Total</th>
	                </tr>
	                <tr>
	                <?
					$grand_tot_in=0;
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
	                    <?
					}
					?>
	                </tr>

	            </thead>
	            <tbody>
	            <?
				$i=1;
				$k=1;
				//var_dump($result);die;
				foreach($details_data as $color_id=>$value)
				{
					foreach($value as $country_id=>$row)
					{
						if(!in_array($color_id,$temp_arr))
						{
							$temp_arr[]=$color_id;
							if($k!=1)
							{
								?>
								<tr bgcolor="#CCCCCC">
									<td >&nbsp;</td>
									<td>&nbsp;</td>
									<td align="right" style="font-weight:bold">Color Total:</td>
									<?
									foreach($sizearr_order as $size_id)
									{
										?>
										<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
										<?
									}
									?>
									<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
								</tr>
								<?
								$line_color_size_in = $line_color_total_in ="";
							}
							$k++;
						}

						?>
						<tr>
							<td align="center"><? echo $i;  ?></td>
	                        <?
							if(!in_array($color_id,$temp_arr_color))
							{
								$temp_arr_color[]=$color_id;
								?>
								<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
								<?
							}
							?>
							<td ><p><? echo $country_library[$country_id];  ?></p></td>
							<?
							$color_total_in=0;
							foreach($sizearr_order as $size_id)
							{
								?>
								<td align="right"><p>
								<?
									echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
									 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id];
									 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
									 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 ?>
								</p></td>
								<?
							}
							$line_color_total_in+=$color_total_in;
							?>
							<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
						</tr>
						<?
						$i++;
					}

				}
				?>
	            <tr bgcolor="#CCCCCC">
	                <td >&nbsp;</td>
	                <td>&nbsp;</td>
	                <td align="right" style="font-weight:bold">Color Total:</td>
	                <?
	                foreach($sizearr_order as $size_id)
	                {
	                    ?>
	                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
	                    <?
	                }
	                ?>
	                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
	            </tr>
	            </tbody>
	            <tfoot>
	                <th >&nbsp;</th>
	                <th>&nbsp;</th>
	                <th >Day Total:</th>
	                <?
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th ><? echo $color_size_in[$size_id]; ?></th>
	                    <?
					}
					?>
	                <th ><? echo $grand_tot_in; ?></th>

	            </tfoot>
	        </table>
	        </div>
	    </fieldset>
	<?
}

if($action=='printing_issue_popup_location')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );


	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");

	$location=str_replace("'","",$location);
	$floor_id=str_replace("'","",$floor_id);
	$sewing_line=str_replace("'","",$sewing_line);
	$location_cond=$floor_cond=$sewing_cond="";
	if($location!="") $location_cond="and a.location=$location";
	if($floor_id!="") $floor_cond=" and a.floor_id=$floor_id";
	if($sewing_line!="") $sewing_cond=" and a.sewing_line=$sewing_line";

	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=2 and a.embel_name=1 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' $location_cond $floor_cond $sewing_cond and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


	?>
	<script>

		function window_close()
		{
			parent.emailwindow.hide();
		}

	</script>
	<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
	    <div style="100%" id="report_container">

	        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
	        	<tr>
	            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise printing production</td>
	            </tr>
	        	<tr>
	            	<td style="font-size:16px; font-weight:bold;">
	                Date : <? echo change_date_format($production_date); ?>
	                <br />
	                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
	                </td>
	            </tr>
	        </table>

	        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
	            <thead>
	                <tr>
	                	<th width="40" rowspan="2">SI</th>
	                    <th width="100" rowspan="2">Color</th>
	                    <th width="100" rowspan="2">Country Name</th>
	                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
	                    <th width="80" rowspan="2" >Total</th>
	                </tr>
	                <tr>
	                <?
					$grand_tot_in=0;
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
	                    <?
					}
					?>
	                </tr>

	            </thead>
	            <tbody>
	            <?
				$i=1;
				$k=1;
				//var_dump($result);die;
				foreach($details_data as $color_id=>$value)
				{
					foreach($value as $country_id=>$row)
					{
						if(!in_array($color_id,$temp_arr))
						{
							$temp_arr[]=$color_id;
							if($k!=1)
							{
								?>
								<tr bgcolor="#CCCCCC">
									<td >&nbsp;</td>
									<td>&nbsp;</td>
									<td align="right" style="font-weight:bold">Color Total:</td>
									<?
									foreach($sizearr_order as $size_id)
									{
										?>
										<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
										<?
									}
									?>
									<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
								</tr>
								<?
								$line_color_size_in = $line_color_total_in ="";
							}
							$k++;
						}

						?>
						<tr>
							<td align="center"><? echo $i;  ?></td>
	                        <?
							if(!in_array($color_id,$temp_arr_color))
							{
								$temp_arr_color[]=$color_id;
								?>
								<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
								<?
							}
							?>
							<td ><p><? echo $country_library[$country_id];  ?></p></td>
							<?
							$color_total_in=0;
							foreach($sizearr_order as $size_id)
							{
								?>
								<td align="right"><p>
								<?
									echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
									 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id];
									 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
									 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 ?>
								</p></td>
								<?
							}
							$line_color_total_in+=$color_total_in;
							?>
							<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
						</tr>
						<?
						$i++;
					}

				}
				?>
	            <tr bgcolor="#CCCCCC">
	                <td >&nbsp;</td>
	                <td>&nbsp;</td>
	                <td align="right" style="font-weight:bold">Color Total:</td>
	                <?
	                foreach($sizearr_order as $size_id)
	                {
	                    ?>
	                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
	                    <?
	                }
	                ?>
	                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
	            </tr>
	            </tbody>
	            <tfoot>
	                <th >&nbsp;</th>
	                <th>&nbsp;</th>
	                <th >Day Total:</th>
	                <?
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th ><? echo $color_size_in[$size_id]; ?></th>
	                    <?
					}
					?>
	                <th ><? echo $grand_tot_in; ?></th>

	            </tfoot>
	        </table>
	        </div>
	    </fieldset>
	<?
}

if($action=='printing_receive_popup')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );


	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");

	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=3 and a.embel_name=1 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


	?>
	<script>

		function window_close()
		{
			parent.emailwindow.hide();
		}

	</script>
	<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
	    <div style="100%" id="report_container">

	        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
	        	<tr>
	            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise printing production</td>
	            </tr>
	        	<tr>
	            	<td style="font-size:16px; font-weight:bold;">
	                Date : <? echo change_date_format($production_date); ?>
	                <br />
	                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
	                </td>
	            </tr>
	        </table>

	        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
	            <thead>
	                <tr>
	                	<th width="40" rowspan="2">SI</th>
	                    <th width="100" rowspan="2">Color</th>
	                    <th width="100" rowspan="2">Country Name</th>
	                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
	                    <th width="80" rowspan="2" >Total</th>
	                </tr>
	                <tr>
	                <?
					$grand_tot_in=0;
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
	                    <?
					}
					?>
	                </tr>

	            </thead>
	            <tbody>
	            <?
				$i=1;
				$k=1;
				//var_dump($result);die;
				foreach($details_data as $color_id=>$value)
				{
					foreach($value as $country_id=>$row)
					{
						if(!in_array($color_id,$temp_arr))
						{
							$temp_arr[]=$color_id;
							if($k!=1)
							{
								?>
								<tr bgcolor="#CCCCCC">
									<td >&nbsp;</td>
									<td>&nbsp;</td>
									<td align="right" style="font-weight:bold">Color Total:</td>
									<?
									foreach($sizearr_order as $size_id)
									{
										?>
										<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
										<?
									}
									?>
									<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
								</tr>
								<?
								$line_color_size_in = $line_color_total_in ="";
							}
							$k++;
						}

						?>
						<tr>
							<td align="center"><? echo $i;  ?></td>
	                        <?
							if(!in_array($color_id,$temp_arr_color))
							{
								$temp_arr_color[]=$color_id;
								?>
								<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
								<?
							}
							?>
							<td ><p><? echo $country_library[$country_id];  ?></p></td>
							<?
							$color_total_in=0;
							foreach($sizearr_order as $size_id)
							{
								?>
								<td align="right"><p>
								<?
									echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
									 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id];
									 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
									 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 ?>
								</p></td>
								<?
							}
							$line_color_total_in+=$color_total_in;
							?>
							<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
						</tr>
						<?
						$i++;
					}

				}
				?>
	            <tr bgcolor="#CCCCCC">
	                <td >&nbsp;</td>
	                <td>&nbsp;</td>
	                <td align="right" style="font-weight:bold">Color Total:</td>
	                <?
	                foreach($sizearr_order as $size_id)
	                {
	                    ?>
	                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
	                    <?
	                }
	                ?>
	                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
	            </tr>
	            </tbody>
	            <tfoot>
	                <th >&nbsp;</th>
	                <th>&nbsp;</th>
	                <th >Day Total:</th>
	                <?
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th ><? echo $color_size_in[$size_id]; ?></th>
	                    <?
					}
					?>
	                <th ><? echo $grand_tot_in; ?></th>

	            </tfoot>
	        </table>
	        </div>
	    </fieldset>
	<?
}

if($action=='printing_receive_popup_location')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );


	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");

	$location=str_replace("'","",$location);
	$floor_id=str_replace("'","",$floor_id);
	$sewing_line=str_replace("'","",$sewing_line);
	$location_cond=$floor_cond=$sewing_cond="";
	if($location!="") $location_cond="and a.location in($location)";
	if($floor_id!="") $floor_cond=" and a.floor_id in($floor_id)";
	if($sewing_line!="") $sewing_cond=" and a.sewing_line in($sewing_line)";

	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=3 and a.embel_name=1 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' $location_cond $floor_cond $sewing_cond and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


	?>
	<script>

		function window_close()
		{
			parent.emailwindow.hide();
		}

	</script>
	<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
	    <div style="100%" id="report_container">

	        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
	        	<tr>
	            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise printing production</td>
	            </tr>
	        	<tr>
	            	<td style="font-size:16px; font-weight:bold;">
	                Date : <? echo change_date_format($production_date); ?>
	                <br />
	                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
	                </td>
	            </tr>
	        </table>

	        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
	            <thead>
	                <tr>
	                	<th width="40" rowspan="2">SI</th>
	                    <th width="100" rowspan="2">Color</th>
	                    <th width="100" rowspan="2">Country Name</th>
	                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
	                    <th width="80" rowspan="2" >Total</th>
	                </tr>
	                <tr>
	                <?
					$grand_tot_in=0;
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
	                    <?
					}
					?>
	                </tr>

	            </thead>
	            <tbody>
	            <?
				$i=1;
				$k=1;
				//var_dump($result);die;
				foreach($details_data as $color_id=>$value)
				{
					foreach($value as $country_id=>$row)
					{
						if(!in_array($color_id,$temp_arr))
						{
							$temp_arr[]=$color_id;
							if($k!=1)
							{
								?>
								<tr bgcolor="#CCCCCC">
									<td >&nbsp;</td>
									<td>&nbsp;</td>
									<td align="right" style="font-weight:bold">Color Total:</td>
									<?
									foreach($sizearr_order as $size_id)
									{
										?>
										<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
										<?
									}
									?>
									<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
								</tr>
								<?
								$line_color_size_in = $line_color_total_in ="";
							}
							$k++;
						}

						?>
						<tr>
							<td align="center"><? echo $i;  ?></td>
	                        <?
							if(!in_array($color_id,$temp_arr_color))
							{
								$temp_arr_color[]=$color_id;
								?>
								<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
								<?
							}
							?>
							<td ><p><? echo $country_library[$country_id];  ?></p></td>
							<?
							$color_total_in=0;
							foreach($sizearr_order as $size_id)
							{
								?>
								<td align="right"><p>
								<?
									echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
									 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id];
									 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
									 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 ?>
								</p></td>
								<?
							}
							$line_color_total_in+=$color_total_in;
							?>
							<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
						</tr>
						<?
						$i++;
					}

				}
				?>
	            <tr bgcolor="#CCCCCC">
	                <td >&nbsp;</td>
	                <td>&nbsp;</td>
	                <td align="right" style="font-weight:bold">Color Total:</td>
	                <?
	                foreach($sizearr_order as $size_id)
	                {
	                    ?>
	                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
	                    <?
	                }
	                ?>
	                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
	            </tr>
	            </tbody>
	            <tfoot>
	                <th >&nbsp;</th>
	                <th>&nbsp;</th>
	                <th >Day Total:</th>
	                <?
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th ><? echo $color_size_in[$size_id]; ?></th>
	                    <?
					}
					?>
	                <th ><? echo $grand_tot_in; ?></th>

	            </tfoot>
	        </table>
	        </div>
	    </fieldset>
	<?
}

if($action=='embroi_issue_popup')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );


	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");

	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=2 and a.embel_name=2 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


	?>
	<script>

		function window_close()
		{
			parent.emailwindow.hide();
		}

	</script>
	<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
	    <div style="100%" id="report_container">

	        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
	        	<tr>
	            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise embroidery production</td>
	            </tr>
	        	<tr>
	            	<td style="font-size:16px; font-weight:bold;">
	                Date : <? echo change_date_format($production_date); ?>
	                <br />
	                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
	                </td>
	            </tr>
	        </table>

	        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
	            <thead>
	                <tr>
	                	<th width="40" rowspan="2">SI</th>
	                    <th width="100" rowspan="2">Color</th>
	                    <th width="100" rowspan="2">Country Name</th>
	                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
	                    <th width="80" rowspan="2" >Total</th>
	                </tr>
	                <tr>
	                <?
					$grand_tot_in=0;
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
	                    <?
					}
					?>
	                </tr>

	            </thead>
	            <tbody>
	            <?
				$i=1;
				$k=1;
				//var_dump($result);die;
				foreach($details_data as $color_id=>$value)
				{
					foreach($value as $country_id=>$row)
					{
						if(!in_array($color_id,$temp_arr))
						{
							$temp_arr[]=$color_id;
							if($k!=1)
							{
								?>
								<tr bgcolor="#CCCCCC">
									<td >&nbsp;</td>
									<td>&nbsp;</td>
									<td align="right" style="font-weight:bold">Color Total:</td>
									<?
									foreach($sizearr_order as $size_id)
									{
										?>
										<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
										<?
									}
									?>
									<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
								</tr>
								<?
								$line_color_size_in = $line_color_total_in ="";
							}
							$k++;
						}

						?>
						<tr>
							<td align="center"><? echo $i;  ?></td>
	                        <?
							if(!in_array($color_id,$temp_arr_color))
							{
								$temp_arr_color[]=$color_id;
								?>
								<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
								<?
							}
							?>
							<td ><p><? echo $country_library[$country_id];  ?></p></td>
							<?
							$color_total_in=0;
							foreach($sizearr_order as $size_id)
							{
								?>
								<td align="right"><p>
								<?
									echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
									 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id];
									 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
									 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 ?>
								</p></td>
								<?
							}
							$line_color_total_in+=$color_total_in;
							?>
							<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
						</tr>
						<?
						$i++;
					}

				}
				?>
	            <tr bgcolor="#CCCCCC">
	                <td >&nbsp;</td>
	                <td>&nbsp;</td>
	                <td align="right" style="font-weight:bold">Color Total:</td>
	                <?
	                foreach($sizearr_order as $size_id)
	                {
	                    ?>
	                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
	                    <?
	                }
	                ?>
	                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
	            </tr>
	            </tbody>
	            <tfoot>
	                <th >&nbsp;</th>
	                <th>&nbsp;</th>
	                <th >Day Total:</th>
	                <?
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th ><? echo $color_size_in[$size_id]; ?></th>
	                    <?
					}
					?>
	                <th ><? echo $grand_tot_in; ?></th>

	            </tfoot>
	        </table>
	        </div>
	    </fieldset>
	<?
}

if($action=='embroi_issue_popup_location')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );


	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");

	$location=str_replace("'","",$location);
	$floor_id=str_replace("'","",$floor_id);
	$sewing_line=str_replace("'","",$sewing_line);
	$location_cond=$floor_cond=$sewing_cond="";
	if($location!="") $location_cond="and a.location in($location)";
	if($floor_id!="") $floor_cond=" and a.floor_id in($floor_id)";
	if($sewing_line!="") $sewing_cond=" and a.sewing_line in($sewing_line)";

	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=2 and a.embel_name=2 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' $location_cond $floor_cond $sewing_cond and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;
	?>
	<script>
		function window_close()
		{
			parent.emailwindow.hide();
		}
	</script>
	<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
	    <div style="100%" id="report_container">
	        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
	        	<tr>
	            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise embroidery production</td>
	            </tr>
	        	<tr>
	            	<td style="font-size:16px; font-weight:bold;">
	                Date : <? echo change_date_format($production_date); ?>
	                <br />
	                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
	                </td>
	            </tr>
	        </table>
	        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
	            <thead>
	                <tr>
	                	<th width="40" rowspan="2">SI</th>
	                    <th width="100" rowspan="2">Color</th>
	                    <th width="100" rowspan="2">Country Name</th>
	                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
	                    <th width="80" rowspan="2" >Total</th>
	                </tr>
	                <tr>
	                <?
					$grand_tot_in=0;
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
	                    <?
					}
					?>
	                </tr>
	            </thead>
	            <tbody>
	            <?
				$i=1;
				$k=1;
				//var_dump($result);die;
				foreach($details_data as $color_id=>$value)
				{
					foreach($value as $country_id=>$row)
					{
						if(!in_array($color_id,$temp_arr))
						{
							$temp_arr[]=$color_id;
							if($k!=1)
							{
								?>
								<tr bgcolor="#CCCCCC">
									<td >&nbsp;</td>
									<td>&nbsp;</td>
									<td align="right" style="font-weight:bold">Color Total:</td>
									<?
									foreach($sizearr_order as $size_id)
									{
										?>
										<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
										<?
									}
									?>
									<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
								</tr>
								<?
								$line_color_size_in = $line_color_total_in ="";
							}
							$k++;
						}
						?>
						<tr>
							<td align="center"><? echo $i;  ?></td>
	                        <?
							if(!in_array($color_id,$temp_arr_color))
							{
								$temp_arr_color[]=$color_id;
								?>
								<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
								<?
							}
							?>
							<td ><p><? echo $country_library[$country_id];  ?></p></td>
							<?
							$color_total_in=0;
							foreach($sizearr_order as $size_id)
							{
								?>
								<td align="right"><p>
								<?
									echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
									 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id];
									 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
									 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 ?>
								</p></td>
								<?
							}
							$line_color_total_in+=$color_total_in;
							?>
							<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
						</tr>
						<?
						$i++;
					}
				}
				?>
	            <tr bgcolor="#CCCCCC">
	                <td >&nbsp;</td>
	                <td>&nbsp;</td>
	                <td align="right" style="font-weight:bold">Color Total:</td>
	                <?
	                foreach($sizearr_order as $size_id)
	                {
	                    ?>
	                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
	                    <?
	                }
	                ?>
	                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
	            </tr>
	            </tbody>
	            <tfoot>
	                <th >&nbsp;</th>
	                <th>&nbsp;</th>
	                <th >Day Total:</th>
	                <?
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th ><? echo $color_size_in[$size_id]; ?></th>
	                    <?
					}
					?>
	                <th ><? echo $grand_tot_in; ?></th>
	            </tfoot>
	        </table>
	        </div>
	    </fieldset>
	<?
}

if($action=='embroi_receive_popup')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );

	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");

	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=3 and a.embel_name=2 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;
	?>
	<script>
		function window_close()
		{
			parent.emailwindow.hide();
		}
	</script>
	<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
	    <div style="100%" id="report_container">
	        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
	        	<tr>
	            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise embroidery production</td>
	            </tr>
	        	<tr>
	            	<td style="font-size:16px; font-weight:bold;">
	                Date : <? echo change_date_format($production_date); ?>
	                <br />
	                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
	                </td>
	            </tr>
	        </table>
	        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
	            <thead>
	                <tr>
	                	<th width="40" rowspan="2">SI</th>
	                    <th width="100" rowspan="2">Color</th>
	                    <th width="100" rowspan="2">Country Name</th>
	                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
	                    <th width="80" rowspan="2" >Total</th>
	                </tr>
	                <tr>
	                <?
					$grand_tot_in=0;
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
	                    <?
					}
					?>
	                </tr>
	            </thead>
	            <tbody>
	            <?
				$i=1;
				$k=1;
				//var_dump($result);die;
				foreach($details_data as $color_id=>$value)
				{
					foreach($value as $country_id=>$row)
					{
						if(!in_array($color_id,$temp_arr))
						{
							$temp_arr[]=$color_id;
							if($k!=1)
							{
								?>
								<tr bgcolor="#CCCCCC">
									<td >&nbsp;</td>
									<td>&nbsp;</td>
									<td align="right" style="font-weight:bold">Color Total:</td>
									<?
									foreach($sizearr_order as $size_id)
									{
										?>
										<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
										<?
									}
									?>
									<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
								</tr>
								<?
								$line_color_size_in = $line_color_total_in ="";
							}
							$k++;
						}
						?>
						<tr>
							<td align="center"><? echo $i;  ?></td>
	                        <?
							if(!in_array($color_id,$temp_arr_color))
							{
								$temp_arr_color[]=$color_id;
								?>
								<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
								<?
							}
							?>
							<td ><p><? echo $country_library[$country_id];  ?></p></td>
							<?
							$color_total_in=0;
							foreach($sizearr_order as $size_id)
							{
								?>
								<td align="right"><p>
								<?
									echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
									 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id];
									 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
									 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 ?>
								</p></td>
								<?
							}
							$line_color_total_in+=$color_total_in;
							?>
							<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
						</tr>
						<?
						$i++;
					}
				}
				?>
	            <tr bgcolor="#CCCCCC">
	                <td >&nbsp;</td>
	                <td>&nbsp;</td>
	                <td align="right" style="font-weight:bold">Color Total:</td>
	                <?
	                foreach($sizearr_order as $size_id)
	                {
	                    ?>
	                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
	                    <?
	                }
	                ?>
	                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
	            </tr>
	            </tbody>
	            <tfoot>
	                <th >&nbsp;</th>
	                <th>&nbsp;</th>
	                <th >Day Total:</th>
	                <?
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th ><? echo $color_size_in[$size_id]; ?></th>
	                    <?
					}
					?>
	                <th ><? echo $grand_tot_in; ?></th>
	            </tfoot>
	        </table>
	        </div>
	    </fieldset>
	<?
}

if($action=='embroi_receive_popup_location')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );

	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");

	$location=str_replace("'","",$location);
	$floor_id=str_replace("'","",$floor_id);
	$sewing_line=str_replace("'","",$sewing_line);
	$location_cond=$floor_cond=$sewing_cond="";
	if($location!="") $location_cond="and a.location in($location)";
	if($floor_id!="") $floor_cond=" and a.floor_id in($floor_id)";
	if($sewing_line!="") $sewing_cond=" and a.sewing_line in($sewing_line)";

	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=3 and a.embel_name=2 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' $location_cond $floor_cond $sewing_cond and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;
	?>
	<script>
		function window_close()
		{
			parent.emailwindow.hide();
		}
	</script>
	<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
	    <div style="100%" id="report_container">
	        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
	        	<tr>
	            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise embroidery production</td>
	            </tr>
	        	<tr>
	            	<td style="font-size:16px; font-weight:bold;">
	                Date : <? echo change_date_format($production_date); ?>
	                <br />
	                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
	                </td>
	            </tr>
	        </table>
	        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
	            <thead>
	                <tr>
	                	<th width="40" rowspan="2">SI</th>
	                    <th width="100" rowspan="2">Color</th>
	                    <th width="100" rowspan="2">Country Name</th>
	                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
	                    <th width="80" rowspan="2" >Total</th>
	                </tr>
	                <tr>
	                <?
					$grand_tot_in=0;
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
	                    <?
					}
					?>
	                </tr>

	            </thead>
	            <tbody>
	            <?
				$i=1;
				$k=1;
				//var_dump($result);die;
				foreach($details_data as $color_id=>$value)
				{
					foreach($value as $country_id=>$row)
					{
						if(!in_array($color_id,$temp_arr))
						{
							$temp_arr[]=$color_id;
							if($k!=1)
							{
								?>
								<tr bgcolor="#CCCCCC">
									<td >&nbsp;</td>
									<td>&nbsp;</td>
									<td align="right" style="font-weight:bold">Color Total:</td>
									<?
									foreach($sizearr_order as $size_id)
									{
										?>
										<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
										<?
									}
									?>
									<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
								</tr>
								<?
								$line_color_size_in = $line_color_total_in ="";
							}
							$k++;
						}
						?>
						<tr>
							<td align="center"><? echo $i;  ?></td>
	                        <?
							if(!in_array($color_id,$temp_arr_color))
							{
								$temp_arr_color[]=$color_id;
								?>
								<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
								<?
							}
							?>
							<td ><p><? echo $country_library[$country_id];  ?></p></td>
							<?
							$color_total_in=0;
							foreach($sizearr_order as $size_id)
							{
								?>
								<td align="right"><p>
								<?
									echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
									 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id];
									 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
									 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 ?>
								</p></td>
								<?
							}
							$line_color_total_in+=$color_total_in;
							?>
							<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
						</tr>
						<?
						$i++;
					}
				}
				?>
	            <tr bgcolor="#CCCCCC">
	                <td >&nbsp;</td>
	                <td>&nbsp;</td>
	                <td align="right" style="font-weight:bold">Color Total:</td>
	                <?
	                foreach($sizearr_order as $size_id)
	                {
	                    ?>
	                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
	                    <?
	                }
	                ?>
	                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
	            </tr>
	            </tbody>
	            <tfoot>
	                <th >&nbsp;</th>
	                <th>&nbsp;</th>
	                <th >Day Total:</th>
	                <?
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th ><? echo $color_size_in[$size_id]; ?></th>
	                    <?
					}
					?>
	                <th ><? echo $grand_tot_in; ?></th>

	            </tfoot>
	        </table>
	        </div>
	    </fieldset>
	<?
}

if($action=='wash_issue_popup')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );

	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");

	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=2 and a.embel_name=3 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;
	?>
	<script>
		function window_close()
		{
			parent.emailwindow.hide();
		}
	</script>
	<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
	    <div style="100%" id="report_container">
	        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
	        	<tr>
	            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise wash production</td>
	            </tr>
	        	<tr>
	            	<td style="font-size:16px; font-weight:bold;">
	                Date : <? echo change_date_format($production_date); ?>
	                <br />
	                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
	                </td>
	            </tr>
	        </table>
	        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
	            <thead>
	                <tr>
	                	<th width="40" rowspan="2">SI</th>
	                    <th width="100" rowspan="2">Color</th>
	                    <th width="100" rowspan="2">Country Name</th>
	                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
	                    <th width="80" rowspan="2" >Total</th>
	                </tr>
	                <tr>
	                <?
					$grand_tot_in=0;
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
	                    <?
					}
					?>
	                </tr>
	            </thead>
	            <tbody>
	            <?
				$i=1;
				$k=1;
				//var_dump($result);die;
				foreach($details_data as $color_id=>$value)
				{
					foreach($value as $country_id=>$row)
					{
						if(!in_array($color_id,$temp_arr))
						{
							$temp_arr[]=$color_id;
							if($k!=1)
							{
								?>
								<tr bgcolor="#CCCCCC">
									<td >&nbsp;</td>
									<td>&nbsp;</td>
									<td align="right" style="font-weight:bold">Color Total:</td>
									<?
									foreach($sizearr_order as $size_id)
									{
										?>
										<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
										<?
									}
									?>
									<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
								</tr>
								<?
								$line_color_size_in = $line_color_total_in ="";
							}
							$k++;
						}
						?>
						<tr>
							<td align="center"><? echo $i;  ?></td>
	                        <?
							if(!in_array($color_id,$temp_arr_color))
							{
								$temp_arr_color[]=$color_id;
								?>
								<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
								<?
							}
							?>
							<td ><p><? echo $country_library[$country_id];  ?></p></td>
							<?
							$color_total_in=0;
							foreach($sizearr_order as $size_id)
							{
								?>
								<td align="right"><p>
								<?
									echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
									 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id];
									 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
									 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 ?>
								</p></td>
								<?
							}
							$line_color_total_in+=$color_total_in;
							?>
							<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
						</tr>
						<?
						$i++;
					}
				}
				?>
	            <tr bgcolor="#CCCCCC">
	                <td >&nbsp;</td>
	                <td>&nbsp;</td>
	                <td align="right" style="font-weight:bold">Color Total:</td>
	                <?
	                foreach($sizearr_order as $size_id)
	                {
	                    ?>
	                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
	                    <?
	                }
	                ?>
	                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
	            </tr>
	            </tbody>
	            <tfoot>
	                <th >&nbsp;</th>
	                <th>&nbsp;</th>
	                <th >Day Total:</th>
	                <?
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th ><? echo $color_size_in[$size_id]; ?></th>
	                    <?
					}
					?>
	                <th ><? echo $grand_tot_in; ?></th>
	            </tfoot>
	        </table>
	        </div>
	    </fieldset>
	<?
}

if($action=='wash_issue_popup_location')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );

	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");

	$location=str_replace("'","",$location);
	$floor_id=str_replace("'","",$floor_id);
	$sewing_line=str_replace("'","",$sewing_line);
	$location_cond=$floor_cond=$sewing_cond="";
	if($location!="") $location_cond="and a.location in($location)";
	if($floor_id!="") $floor_cond=" and a.floor_id in($floor_id)";
	if($sewing_line!="") $sewing_cond=" and a.sewing_line in($sewing_line)";

	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=2 and a.embel_name=3 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' $location_cond $floor_cond $sewing_cond and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;
	?>
	<script>
		function window_close()
		{
			parent.emailwindow.hide();
		}
	</script>
	<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
	    <div style="100%" id="report_container">
	        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
	        	<tr>
	            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise wash production</td>
	            </tr>
	        	<tr>
	            	<td style="font-size:16px; font-weight:bold;">
	                Date : <? echo change_date_format($production_date); ?>
	                <br />
	                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
	                </td>
	            </tr>
	        </table>
	        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
	            <thead>
	                <tr>
	                	<th width="40" rowspan="2">SI</th>
	                    <th width="100" rowspan="2">Color</th>
	                    <th width="100" rowspan="2">Country Name</th>
	                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
	                    <th width="80" rowspan="2" >Total</th>
	                </tr>
	                <tr>
	                <?
					$grand_tot_in=0;
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
	                    <?
					}
					?>
	                </tr>
	            </thead>
	            <tbody>
	            <?
				$i=1;
				$k=1;
				//var_dump($result);die;
				foreach($details_data as $color_id=>$value)
				{
					foreach($value as $country_id=>$row)
					{
						if(!in_array($color_id,$temp_arr))
						{
							$temp_arr[]=$color_id;
							if($k!=1)
							{
								?>
								<tr bgcolor="#CCCCCC">
									<td >&nbsp;</td>
									<td>&nbsp;</td>
									<td align="right" style="font-weight:bold">Color Total:</td>
									<?
									foreach($sizearr_order as $size_id)
									{
										?>
										<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
										<?
									}
									?>
									<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
								</tr>
								<?
								$line_color_size_in = $line_color_total_in ="";
							}
							$k++;
						}
						?>
						<tr>
							<td align="center"><? echo $i;  ?></td>
	                        <?
							if(!in_array($color_id,$temp_arr_color))
							{
								$temp_arr_color[]=$color_id;
								?>
								<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
								<?
							}
							?>
							<td ><p><? echo $country_library[$country_id];  ?></p></td>
							<?
							$color_total_in=0;
							foreach($sizearr_order as $size_id)
							{
								?>
								<td align="right"><p>
								<?
									echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
									 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id];
									 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
									 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 ?>
								</p></td>
								<?
							}
							$line_color_total_in+=$color_total_in;
							?>
							<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
						</tr>
						<?
						$i++;
					}
				}
				?>
	            <tr bgcolor="#CCCCCC">
	                <td >&nbsp;</td>
	                <td>&nbsp;</td>
	                <td align="right" style="font-weight:bold">Color Total:</td>
	                <?
	                foreach($sizearr_order as $size_id)
	                {
	                    ?>
	                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
	                    <?
	                }
	                ?>
	                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
	            </tr>
	            </tbody>
	            <tfoot>
	                <th>&nbsp;</th>
	                <th>&nbsp;</th>
	                <th>Day Total:</th>
	                <?
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th><? echo $color_size_in[$size_id]; ?></th>
	                    <?
					}
					?>
	                <th><? echo $grand_tot_in; ?></th>
	            </tfoot>
	        </table>
	        </div>
	    </fieldset>
	<?
}

if($action=='wash_receive_popup')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );

	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");

	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=3 and a.embel_name=3 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;
	?>
	<script>

		function window_close()
		{
			parent.emailwindow.hide();
		}
	</script>
	<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
	    <div style="100%" id="report_container">
	        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
	        	<tr>
	            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise wash production</td>
	            </tr>
	        	<tr>
	            	<td style="font-size:16px; font-weight:bold;">
	                Date : <? echo change_date_format($production_date); ?>
	                <br />
	                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
	                </td>
	            </tr>
	        </table>
	        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
	            <thead>
	                <tr>
	                	<th width="40" rowspan="2">SI</th>
	                    <th width="100" rowspan="2">Color</th>
	                    <th width="100" rowspan="2">Country Name</th>
	                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
	                    <th width="80" rowspan="2" >Total</th>
	                </tr>
	                <tr>
	                <?
					$grand_tot_in=0;
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
	                    <?
					}
					?>
	                </tr>
	            </thead>
	            <tbody>
	            <?
				$i=1;
				$k=1;
				//var_dump($result);die;
				foreach($details_data as $color_id=>$value)
				{
					foreach($value as $country_id=>$row)
					{
						if(!in_array($color_id,$temp_arr))
						{
							$temp_arr[]=$color_id;
							if($k!=1)
							{
								?>
								<tr bgcolor="#CCCCCC">
									<td >&nbsp;</td>
									<td>&nbsp;</td>
									<td align="right" style="font-weight:bold">Color Total:</td>
									<?
									foreach($sizearr_order as $size_id)
									{
										?>
										<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
										<?
									}
									?>
									<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
								</tr>
								<?
								$line_color_size_in = $line_color_total_in ="";
							}
							$k++;
						}
						?>
						<tr>
							<td align="center"><? echo $i;  ?></td>
	                        <?
							if(!in_array($color_id,$temp_arr_color))
							{
								$temp_arr_color[]=$color_id;
								?>
								<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
								<?
							}
							?>
							<td ><p><? echo $country_library[$country_id];  ?></p></td>
							<?
							$color_total_in=0;
							foreach($sizearr_order as $size_id)
							{
								?>
								<td align="right"><p>
								<?
									echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
									 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id];
									 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
									 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 ?>
								</p></td>
								<?
							}
							$line_color_total_in+=$color_total_in;
							?>
							<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
						</tr>
						<?
						$i++;
					}
				}
				?>
	            <tr bgcolor="#CCCCCC">
	                <td >&nbsp;</td>
	                <td>&nbsp;</td>
	                <td align="right" style="font-weight:bold">Color Total:</td>
	                <?
	                foreach($sizearr_order as $size_id)
	                {
	                    ?>
	                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
	                    <?
	                }
	                ?>
	                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
	            </tr>
	            </tbody>
	            <tfoot>
	                <th >&nbsp;</th>
	                <th>&nbsp;</th>
	                <th >Day Total:</th>
	                <?
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th ><? echo $color_size_in[$size_id]; ?></th>
	                    <?
					}
					?>
	                <th ><? echo $grand_tot_in; ?></th>
	            </tfoot>
	        </table>
	        </div>
	    </fieldset>
	<?
}

if($action=='wash_receive_popup_location')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );

	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");

	$location=str_replace("'","",$location);
	$floor_id=str_replace("'","",$floor_id);
	$sewing_line=str_replace("'","",$sewing_line);
	$location_cond=$floor_cond=$sewing_cond="";
	if($location!="") $location_cond="and a.location in($location)";
	if($floor_id!="") $floor_cond=" and a.floor_id in($floor_id)";
	if($sewing_line!="") $sewing_cond=" and a.sewing_line in$sewing_line)";

	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=3 and a.embel_name=3 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' $location_cond $floor_cond $sewing_cond and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;
	?>
	<script>

		function window_close()
		{
			parent.emailwindow.hide();
		}

	</script>
	<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
	    <div style="100%" id="report_container">
	        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
	        	<tr>
	            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise wash production</td>
	            </tr>
	        	<tr>
	            	<td style="font-size:16px; font-weight:bold;">
	                Date : <? echo change_date_format($production_date); ?>
	                <br />
	                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
	                </td>
	            </tr>
	        </table>
	        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
	            <thead>
	                <tr>
	                	<th width="40" rowspan="2">SI</th>
	                    <th width="100" rowspan="2">Color</th>
	                    <th width="100" rowspan="2">Country Name</th>
	                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
	                    <th width="80" rowspan="2" >Total</th>
	                </tr>
	                <tr>
	                <?
					$grand_tot_in=0;
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
	                    <?
					}
					?>
	                </tr>
	            </thead>
	            <tbody>
	            <?
				$i=1;
				$k=1;
				//var_dump($result);die;
				foreach($details_data as $color_id=>$value)
				{
					foreach($value as $country_id=>$row)
					{
						if(!in_array($color_id,$temp_arr))
						{
							$temp_arr[]=$color_id;
							if($k!=1)
							{
								?>
								<tr bgcolor="#CCCCCC">
									<td >&nbsp;</td>
									<td>&nbsp;</td>
									<td align="right" style="font-weight:bold">Color Total:</td>
									<?
									foreach($sizearr_order as $size_id)
									{
										?>
										<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
										<?
									}
									?>
									<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
								</tr>
								<?
								$line_color_size_in = $line_color_total_in ="";
							}
							$k++;
						}
						?>
						<tr>
							<td align="center"><? echo $i;  ?></td>
	                        <?
							if(!in_array($color_id,$temp_arr_color))
							{
								$temp_arr_color[]=$color_id;
								?>
								<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
								<?
							}
							?>
							<td ><p><? echo $country_library[$country_id];  ?></p></td>
							<?
							$color_total_in=0;
							foreach($sizearr_order as $size_id)
							{
								?>
								<td align="right"><p>
								<?
									echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
									 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id];
									 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
									 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 ?>
								</p></td>
								<?
							}
							$line_color_total_in+=$color_total_in;
							?>
							<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
						</tr>
						<?
						$i++;
					}
				}
				?>
	            <tr bgcolor="#CCCCCC">
	                <td >&nbsp;</td>
	                <td>&nbsp;</td>
	                <td align="right" style="font-weight:bold">Color Total:</td>
	                <?
	                foreach($sizearr_order as $size_id)
	                {
	                    ?>
	                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
	                    <?
	                }
	                ?>
	                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
	            </tr>
	            </tbody>
	            <tfoot>
	                <th >&nbsp;</th>
	                <th>&nbsp;</th>
	                <th >Day Total:</th>
	                <?
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th ><? echo $color_size_in[$size_id]; ?></th>
	                    <?
					}
					?>
	                <th ><? echo $grand_tot_in; ?></th>
	            </tfoot>
	        </table>
	        </div>
	    </fieldset>
	<?
}

if($action=='sp_issue_popup')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );

	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");

	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=2 and a.embel_name=4 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3)  and c.is_deleted=0
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;
	?>
	<script>

		function window_close()
		{
			parent.emailwindow.hide();
		}

	</script>
	<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
	    <div style="100%" id="report_container">
	        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
	        	<tr>
	            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise special works production</td>
	            </tr>
	        	<tr>
	            	<td style="font-size:16px; font-weight:bold;">
	                Date : <? echo change_date_format($production_date); ?>
	                <br />
	                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
	                </td>
	            </tr>
	        </table>
	        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
	            <thead>
	                <tr>
	                	<th width="40" rowspan="2">SI</th>
	                    <th width="100" rowspan="2">Color</th>
	                    <th width="100" rowspan="2">Country Name</th>
	                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
	                    <th width="80" rowspan="2" >Total</th>
	                </tr>
	                <tr>
	                <?
					$grand_tot_in=0;
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
	                    <?
					}
					?>
	                </tr>
	            </thead>
	            <tbody>
	            <?
				$i=1;
				$k=1;
				//var_dump($result);die;
				foreach($details_data as $color_id=>$value)
				{
					foreach($value as $country_id=>$row)
					{
						if(!in_array($color_id,$temp_arr))
						{
							$temp_arr[]=$color_id;
							if($k!=1)
							{
								?>
								<tr bgcolor="#CCCCCC">
									<td >&nbsp;</td>
									<td>&nbsp;</td>
									<td align="right" style="font-weight:bold">Color Total:</td>
									<?
									foreach($sizearr_order as $size_id)
									{
										?>
										<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
										<?
									}
									?>
									<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
								</tr>
								<?
								$line_color_size_in = $line_color_total_in ="";
							}
							$k++;
						}
						?>
						<tr>
							<td align="center"><? echo $i;  ?></td>
	                        <?
							if(!in_array($color_id,$temp_arr_color))
							{
								$temp_arr_color[]=$color_id;
								?>
								<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
								<?
							}
							?>
							<td ><p><? echo $country_library[$country_id];  ?></p></td>
							<?
							$color_total_in=0;
							foreach($sizearr_order as $size_id)
							{
								?>
								<td align="right"><p>
								<?
									echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
									 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id];
									 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
									 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 ?>
								</p></td>
								<?
							}
							$line_color_total_in+=$color_total_in;
							?>
							<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
						</tr>
						<?
						$i++;
					}
				}
				?>
	            <tr bgcolor="#CCCCCC">
	                <td >&nbsp;</td>
	                <td>&nbsp;</td>
	                <td align="right" style="font-weight:bold">Color Total:</td>
	                <?
	                foreach($sizearr_order as $size_id)
	                {
	                    ?>
	                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
	                    <?
	                }
	                ?>
	                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
	            </tr>
	            </tbody>
	            <tfoot>
	                <th >&nbsp;</th>
	                <th>&nbsp;</th>
	                <th >Day Total:</th>
	                <?
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th ><? echo $color_size_in[$size_id]; ?></th>
	                    <?
					}
					?>
	                <th ><? echo $grand_tot_in; ?></th>
	            </tfoot>
	        </table>
	        </div>
	    </fieldset>
	<?
}

if($action=='sp_issue_popup_location')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );

	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");

	$location=str_replace("'","",$location);
	$floor_id=str_replace("'","",$floor_id);
	$sewing_line=str_replace("'","",$sewing_line);
	$location_cond=$floor_cond=$sewing_cond="";
	if($location!="") $location_cond="and a.location in($location)";
	if($floor_id!="") $floor_cond=" and a.floor_id in($floor_id)";
	if($sewing_line!="") $sewing_cond=" and a.sewing_line in($sewing_line)";

	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=2 and a.embel_name=4 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' $location_cond $floor_cond $sewing_cond and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;
	?>
	<script>

		function window_close()
		{
			parent.emailwindow.hide();
		}

	</script>
	<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
	    <div style="100%" id="report_container">
	        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
	        	<tr>
	            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise special works production</td>
	            </tr>
	        	<tr>
	            	<td style="font-size:16px; font-weight:bold;">
	                Date : <? echo change_date_format($production_date); ?>
	                <br />
	                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
	                </td>
	            </tr>
	        </table>
	        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
	            <thead>
	                <tr>
	                	<th width="40" rowspan="2">SI</th>
	                    <th width="100" rowspan="2">Color</th>
	                    <th width="100" rowspan="2">Country Name</th>
	                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
	                    <th width="80" rowspan="2" >Total</th>
	                </tr>
	                <tr>
	                <?
					$grand_tot_in=0;
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
	                    <?
					}
					?>
	                </tr>
	            </thead>
	            <tbody>
	            <?
				$i=1;
				$k=1;
				//var_dump($result);die;
				foreach($details_data as $color_id=>$value)
				{
					foreach($value as $country_id=>$row)
					{
						if(!in_array($color_id,$temp_arr))
						{
							$temp_arr[]=$color_id;
							if($k!=1)
							{
								?>
								<tr bgcolor="#CCCCCC">
									<td >&nbsp;</td>
									<td>&nbsp;</td>
									<td align="right" style="font-weight:bold">Color Total:</td>
									<?
									foreach($sizearr_order as $size_id)
									{
										?>
										<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
										<?
									}
									?>
									<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
								</tr>
								<?
								$line_color_size_in = $line_color_total_in ="";
							}
							$k++;
						}
						?>
						<tr>
							<td align="center"><? echo $i;  ?></td>
	                        <?
							if(!in_array($color_id,$temp_arr_color))
							{
								$temp_arr_color[]=$color_id;
								?>
								<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
								<?
							}
							?>
							<td ><p><? echo $country_library[$country_id];  ?></p></td>
							<?
							$color_total_in=0;
							foreach($sizearr_order as $size_id)
							{
								?>
								<td align="right"><p>
								<?
									echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
									 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id];
									 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
									 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 ?>
								</p></td>
								<?
							}
							$line_color_total_in+=$color_total_in;
							?>
							<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
						</tr>
						<?
						$i++;
					}
				}
				?>
	            <tr bgcolor="#CCCCCC">
	                <td >&nbsp;</td>
	                <td>&nbsp;</td>
	                <td align="right" style="font-weight:bold">Color Total:</td>
	                <?
	                foreach($sizearr_order as $size_id)
	                {
	                    ?>
	                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
	                    <?
	                }
	                ?>
	                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
	            </tr>
	            </tbody>
	            <tfoot>
	                <th >&nbsp;</th>
	                <th>&nbsp;</th>
	                <th >Day Total:</th>
	                <?
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th ><? echo $color_size_in[$size_id]; ?></th>
	                    <?
					}
					?>
	                <th ><? echo $grand_tot_in; ?></th>
	            </tfoot>
	        </table>
	        </div>
	    </fieldset>
	<?
}

if($action=='sp_receive_popup')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );

	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");

	$location=str_replace("'","",$location);
	$floor_id=str_replace("'","",$floor_id);
	$sewing_line=str_replace("'","",$sewing_line);
	$location_cond=$floor_cond=$sewing_cond="";
	if($location) $location_cond="and a.location in($location)";
	if($floor_id!="") $floor_cond=" and a.floor_id in($floor_id)";
	if($sewing_line!="") $sewing_cond=" and a.sewing_line in($sewing_line)";

	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=3 and a.embel_name=4 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' $location_cond $floor_cond $sewing_cond and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3)  and c.is_deleted=0
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;
	?>
	<script>

		function window_close()
		{
			parent.emailwindow.hide();
		}
	</script>
	<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
	    <div style="100%" id="report_container">
	        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
	        	<tr>
	            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise special works production</td>
	            </tr>
	        	<tr>
	            	<td style="font-size:16px; font-weight:bold;">
	                Date : <? echo change_date_format($production_date); ?>
	                <br />
	                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
	                </td>
	            </tr>
	        </table>
	        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
	            <thead>
	                <tr>
	                	<th width="40" rowspan="2">SI</th>
	                    <th width="100" rowspan="2">Color</th>
	                    <th width="100" rowspan="2">Country Name</th>
	                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
	                    <th width="80" rowspan="2" >Total</th>
	                </tr>
	                <tr>
	                <?
					$grand_tot_in=0;
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
	                    <?
					}
					?>
	                </tr>
	            </thead>
	            <tbody>
	            <?
				$i=1;
				$k=1;
				//var_dump($result);die;
				foreach($details_data as $color_id=>$value)
				{
					foreach($value as $country_id=>$row)
					{
						if(!in_array($color_id,$temp_arr))
						{
							$temp_arr[]=$color_id;
							if($k!=1)
							{
								?>
								<tr bgcolor="#CCCCCC">
									<td >&nbsp;</td>
									<td>&nbsp;</td>
									<td align="right" style="font-weight:bold">Color Total:</td>
									<?
									foreach($sizearr_order as $size_id)
									{
										?>
										<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
										<?
									}
									?>
									<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
								</tr>
								<?
								$line_color_size_in = $line_color_total_in ="";
							}
							$k++;
						}
						?>
						<tr>
							<td align="center"><? echo $i;  ?></td>
	                        <?
							if(!in_array($color_id,$temp_arr_color))
							{
								$temp_arr_color[]=$color_id;
								?>
								<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
								<?
							}
							?>
							<td ><p><? echo $country_library[$country_id];  ?></p></td>
							<?
							$color_total_in=0;
							foreach($sizearr_order as $size_id)
							{
								?>
								<td align="right"><p>
								<?
									echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
									 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id];
									 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
									 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 ?>
								</p></td>
								<?
							}
							$line_color_total_in+=$color_total_in;
							?>
							<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
						</tr>
						<?
						$i++;
					}
				}
				?>
	            <tr bgcolor="#CCCCCC">
	                <td >&nbsp;</td>
	                <td>&nbsp;</td>
	                <td align="right" style="font-weight:bold">Color Total:</td>
	                <?
	                foreach($sizearr_order as $size_id)
	                {
	                    ?>
	                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
	                    <?
	                }
	                ?>
	                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
	            </tr>
	            </tbody>
	            <tfoot>
	                <th >&nbsp;</th>
	                <th>&nbsp;</th>
	                <th >Day Total:</th>
	                <?
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th ><? echo $color_size_in[$size_id]; ?></th>
	                    <?
					}
					?>
	                <th ><? echo $grand_tot_in; ?></th>
	            </tfoot>
	        </table>
	        </div>
	    </fieldset>
	<?
	exit();
}

if($action=='sp_receive_popup_location')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );

	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");

	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=3 and a.embel_name=4 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;
	?>
	<script>

		function window_close()
		{
			parent.emailwindow.hide();
		}

	</script>
	<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
	    <div style="100%" id="report_container">

	        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
	        	<tr>
	            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise special works production</td>
	            </tr>
	        	<tr>
	            	<td style="font-size:16px; font-weight:bold;">
	                Date : <? echo change_date_format($production_date); ?>
	                <br />
	                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
	                </td>
	            </tr>
	        </table>
	        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
	            <thead>
	                <tr>
	                	<th width="40" rowspan="2">SI</th>
	                    <th width="100" rowspan="2">Color</th>
	                    <th width="100" rowspan="2">Country Name</th>
	                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
	                    <th width="80" rowspan="2" >Total</th>
	                </tr>
	                <tr>
	                <?
					$grand_tot_in=0;
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
	                    <?
					}
					?>
	                </tr>
	            </thead>
	            <tbody>
	            <?
				$i=1;
				$k=1;
				//var_dump($result);die;
				foreach($details_data as $color_id=>$value)
				{
					foreach($value as $country_id=>$row)
					{
						if(!in_array($color_id,$temp_arr))
						{
							$temp_arr[]=$color_id;
							if($k!=1)
							{
								?>
								<tr bgcolor="#CCCCCC">
									<td >&nbsp;</td>
									<td>&nbsp;</td>
									<td align="right" style="font-weight:bold">Color Total:</td>
									<?
									foreach($sizearr_order as $size_id)
									{
										?>
										<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
										<?
									}
									?>
									<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
								</tr>
								<?
								$line_color_size_in = $line_color_total_in ="";
							}
							$k++;
						}
						?>
						<tr>
							<td align="center"><? echo $i;  ?></td>
	                        <?
							if(!in_array($color_id,$temp_arr_color))
							{
								$temp_arr_color[]=$color_id;
								?>
								<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
								<?
							}
							?>
							<td ><p><? echo $country_library[$country_id];  ?></p></td>
							<?
							$color_total_in=0;
							foreach($sizearr_order as $size_id)
							{
								?>
								<td align="right"><p>
								<?
									echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
									 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id];
									 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
									 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 ?>
								</p></td>
								<?
							}
							$line_color_total_in+=$color_total_in;
							?>
							<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
						</tr>
						<?
						$i++;
					}
				}
				?>
	            <tr bgcolor="#CCCCCC">
	                <td >&nbsp;</td>
	                <td>&nbsp;</td>
	                <td align="right" style="font-weight:bold">Color Total:</td>
	                <?
	                foreach($sizearr_order as $size_id)
	                {
	                    ?>
	                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
	                    <?
	                }
	                ?>
	                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
	            </tr>
	            </tbody>
	            <tfoot>
	                <th >&nbsp;</th>
	                <th>&nbsp;</th>
	                <th >Day Total:</th>
	                <?
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th ><? echo $color_size_in[$size_id]; ?></th>
	                    <?
					}
					?>
	                <th ><? echo $grand_tot_in; ?></th>
	            </tfoot>
	        </table>
	        </div>
	    </fieldset>
	<?
	exit();
}

if($action=="sewingQnty_popup")
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo"<pre>";print_r($_REQUEST);die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name");
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number");
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
	$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name");
	$sewing_line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name");
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');

	$sizearr_order=return_library_array("select size_number_id, size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	//var_dump();die;

	$po_details_sql=sql_select("SELECT a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id,b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.id=$po_break_down_id group by a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id,b.grouping");
	// echo"<pre>";
	// print_r($po_details_sql);die;
	$serving_company_sql=sql_select("select a.company_id from pro_garments_production_mst a, wo_po_break_down b where a.po_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.id=$po_break_down_id group by a.company_id");
	$serving_company_id=$serving_company_sql[0][csf("company_id")];
	// echo"<pre>";
	// print_r($serving_company_id);die;
	// echo $serving_company_id;die;
	//For Show Date Location and Floor
	$ex_item_id=explode("__",$gmts_item_id);
	$gmt_item_id=$ex_item_id[0];
	$serving_comp_id=$ex_item_id[1];

	if($location_id!=0) $location_cond=" and a.location in($location_id)"; else  $location_cond="";
	if($floor_id!=0) $floor_cond=" and a.floor_id in($floor_id)"; else  $floor_cond="";
	if($sewing_line!=0) $sewing_line_cond=" and a.sewing_line in($sewing_line)"; else  $sewing_line_cond="";
	if($serving_comp_id!=0) $serving_comp_cond=" and a.serving_company in($serving_comp_id)"; else $serving_comp_cond="";

	if($db_type==2)
	{
		$sql= "SELECT  a.challan_no, a.floor_id, a.sewing_line, a.country_id, a.production_source, a.serving_company, c.color_number_id, LISTAGG(CAST(c.size_number_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.size_number_id) as size_number_id, a.prod_reso_allo
		from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
		where a.production_type=$page and a.item_number_id=$gmt_item_id  and c.color_number_id=$color and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.production_source='$prod_source' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3)  and c.is_deleted=0 $location_cond  $floor_cond $sewing_line_cond $serving_comp_cond and a.po_break_down_id = c.po_break_down_id
		group by  a.country_id,a.challan_no, a.floor_id, a.sewing_line, a.production_source, a.serving_company, a.prod_reso_allo,c.color_number_id
		order by a.country_id,a.floor_id, a.sewing_line"; // a.challan_no,
	}
	else if($db_type==0)
	{
		$sql= "SELECT a.challan_no, a.floor_id, a.sewing_line, a.country_id, a.production_source, a.serving_company, c.color_number_id, group_concat(c.size_number_id) as size_number_id, a.prod_reso_allo
		from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
		where a.production_type=$page and a.item_number_id=$gmt_item_id and a.production_date='$production_date' and a.production_source='$prod_source' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3)  and c.is_deleted=0 $location_cond  $floor_cond $sewing_line_cond $serving_comp_cond and a.po_break_down_id = c.po_break_down_id
		group by  a.country_id,a.challan_no, a.floor_id, a.sewing_line, a.production_source, a.serving_company,a.prod_reso_allo,c.color_number_id
		order by a.country_id,a.challan_no, a.floor_id, a.sewing_line";
	}
	//echo $sql;die;

	$nameArray = sql_select("select id, auto_update from variable_settings_production where company_name='$serving_company_id' and variable_list=23 and status_active=1 and is_deleted=0");
    $prod_reso_allocation = $nameArray[0][csf('auto_update')];
    // $prod_reso_allocation = 1;

	// echo $sql; //and a.production_date='$production_date'
	$result=sql_select($sql);

	$sql_color_size= sql_select("SELECT a.challan_no, a.floor_id, a.sewing_line, a.country_id, a.production_source, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a, pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=$page and a.item_number_id=$gmt_item_id and a.production_date='$production_date'  and c.color_number_id=$color and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.production_source='$prod_source' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $location_cond  $floor_cond $sewing_line_cond $serving_comp_cond and a.po_break_down_id = c.po_break_down_id and c.status_active in(1,2,3)  and c.is_deleted=0
	group by a.country_id,a.challan_no, a.floor_id, a.sewing_line, a.production_source,c.color_number_id,c.size_number_id");

	foreach($sql_color_size as $row)
	{
		$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
		$summery_data[$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	//$table_width=630+$col_width;
	if($prod_source==3) $table_width=750+$col_width; else $table_width=630+$col_width;
	$summer_table_width=230+$col_width;
	?>
	<script>

		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
		}

		function window_close()
		{
			parent.emailwindow.hide();
		}

	</script>
	<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
			<input type="button" value="Print" onClick="print_window()" style="width:100px; margin-left:400px;"  class="formbutton" /><br />
	    <div style="100%" id="report_container">

	        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
	        	<tr>
	            	<td style="font-size:14px; font-weight:bold;">
	                Buyer Name : <? echo $buyer_library[$po_details_sql[0][csf("buyer_name")]]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Job No : <? echo $po_details_sql[0][csf("job_no")]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Internal Ref : <? echo $po_details_sql[0][csf("grouping")]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Style No : <? echo $po_details_sql[0][csf("style_ref_no")]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Garments Item :
					<?
	                    $item_data="";
	                    $garments_item_arr=array_unique(explode(",",$po_details_sql[0][csf("gmts_item_id")]));
	                    foreach($garments_item_arr as $item_id)
	                    {
	                        if($item_data!="") $item_data .=", ";
	                        $item_data .=$garments_item[$item_id];
	                    }
	                    echo $item_data;
	                ?>
	                <br />
	                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;Date : <? echo change_date_format($production_date); ?>
	                <br />
	                Summary
	                </td>
	            </tr>
	        </table>
	        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $summer_table_width; ?>" style="margin-bottom:20px;">
	            <thead>
	                <tr>
	                	<th width="40" rowspan="2">SI</th>
	                    <th width="100" rowspan="2">Color</th>
	                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
	                    <th width="80" rowspan="2" >Total</th>
	                </tr>
	                <tr>
	                <?
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
	                    <?
					}
					?>
	                </tr>
	            </thead>
	            <tbody>
	            <?
				$i=1;
				//var_dump($result);die;
				foreach($summery_data as $color_id=>$row)
				{
					?>
	                <tr>
	                    <td valign="middle" align="center"><? echo $i;  ?></td>
	                    <td valign="middle" ><? echo $colorarr[$color_id];  ?></td>
	                    <?
						$summry_color_total_in =0;
	                    foreach($sizearr_order as $size_id)
	                    {
	                        ?>
	                        <td valign="middle" align="right"><? echo number_format($row[$size_id],0) ; $summry_color_total_in+= $row[$size_id]; $summry_color_size_in [$size_id]+=$row[$size_id];?></td>
	                        <?
	                    }
	                    ?>
	                    <td valign="middle" align="right"><? echo  number_format( $summry_color_total_in,0); $grand_tot_in+=$summry_color_total_in; ?></td>
	                </tr>
	                <?
					$i++;
				}
				?>
	            </tbody>
	            <tfoot>
	                <th colspan="2">Total :</th>
	                <?
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th ><? echo $summry_color_size_in[$size_id]; ?></th>
	                    <?
					}
					?>
	                <th><? echo $grand_tot_in; ?></th>
	            </tfoot>
	        </table>
	        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px;">
	        	<tr>
	            	<td style="font-size:14px; font-weight:bold;">Details</td>
	            </tr>
	        </table>
	        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
	            <thead>
	                <tr>
	                	<th width="40" rowspan="2">SI</th>
	                    <th width="100" rowspan="2">Country Name</th>
	                    <th width="80" rowspan="2">Source</th>
	                    <?
						if($prod_source==3)
						{
							?>
	                    	<th width="120" rowspan="2">Serving Company</th>
	                        <?
						}
						?>
	                    <th width="70" rowspan="2">Challan</th>

							<?
								if($page==11){
									?>
					    <th width="90" rowspan="2" colspan="2">Floor/Unit</th>
					        <?
								}else{
									?>
						<th width="90" rowspan="2">Sewing Unit</th>
						<th width="70" rowspan="2">Sewing Line</th>
						    <?
								}
							?>
	                    <th width="100" rowspan="2">Color</th>
	                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
	                    <th width="80" rowspan="2" >Total</th>
	                </tr>
	                <tr>
	                <?
					$grand_tot_in=0;
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
	                    <?
					}
					?>
	                </tr>

	            </thead>
	            <tbody>
	            <?
				$i=1;
				$k=1;
				//var_dump($result);die;
				$line_color_size_in = array();
				foreach($result as $row)
				{
					if(!in_array($row[csf("sewing_line")],$temp_arr[$row[csf("country_id")]][$row[csf("floor_id")]]))
					{
						$temp_arr[$row[csf("country_id")]][$row[csf("floor_id")]][]=$row[csf("sewing_line")];
						if($k!=1)
						{
							?>
							<tr bgcolor="#CCCCCC">
                                <td  colspan="<?=$prod_source==3 ? 8: 7;?>" align="right"><strong>(<?=$sewing_line?>) Total :</strong></td>
								<?
								foreach($sizearr_order as $size_id)
								{
									?>
									<td align="right"><? echo number_format($line_color_size_in[$sewing_line][$size_id],0); ?></td>
									<?
								}
								?>
								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
							</tr>
							<?
						}
                        $line_color_total_in = 0;
						$k++;
					}
                    $sewing_line='';
                    if($prod_reso_allocation==1)
                    {
                        $line_number=explode(",",$prod_reso_arr[$row[csf('sewing_line')]]);
                        foreach($line_number as $val)
                        {
                            if($sewing_line=='') $sewing_line=$sewing_line_library[$val]; else $sewing_line.=",".$sewing_line_library[$val];
                        }
                    }
                    else $sewing_line=$sewing_line_library[$row[csf('sewing_line')]];
					?>
	                <tr>
	                    <td valign="middle" align="center"><? echo $i;  ?></td>
	                    <td valign="middle" ><p><? echo $country_library[$row[csf("country_id")]];  ?></p></td>
	                    <td valign="middle" ><p><? echo $knitting_source[$row[csf("production_source")]]; ?><p></td>
	                    <?
						if($prod_source==3)
						{
							?>
	                    	<td valign="middle" ><p><? echo $supplier_arr[$row[csf("serving_company")]];  ?></p></td>
	                        <?
						}
						?>
	                    <td valign="middle" ><p><? echo $row[csf("challan_no")];  ?></p></td>
							<? if($page==11){
								?>
						<td valign="middle" colspan="2"><p><? echo $floor_library[$row[csf("floor_id")]];  ?></p></td>
								<?
							}else{
								?>
                        <td valign="middle" ><p><? echo $floor_library[$row[csf("floor_id")]];  ?></p></td>
						<td valign="middle" align="center"><p><? echo $sewing_line;  ?></p></td>
								<?
							}?>
	                    <td valign="middle" ><p><? echo $colorarr[$row[csf("color_number_id")]];  ?></p></td>
	                    <?
						$color_total_in=0;

	                    foreach($sizearr_order as $size_id)
	                    {
							$Production_qty=0;
	                        ?>
	                        <td valign="middle" align="right"><p>
							<?
								$Production_qty=$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id];
							 	echo number_format($Production_qty,0);
								 $color_total_in+=$Production_qty;
                                 $color_size_in[$size_id]+=$Production_qty;
                                 $line_color_total_in+=$Production_qty;
                                 $line_color_size_in[$sewing_line][$size_id]+=$Production_qty;
							 ?>
	                        </p></td>
	                        <?
	                    }
	                    ?>
	                    <td valign="middle" align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
	                </tr>
	                <?
					$i++;
				}
				?>
	            <tr bgcolor="#CCCCCC">
                    <td  colspan="<?=$prod_source==3 ? 8: 7;?>" align="right"><strong>(<?=$sewing_line?>) Total :</strong></td>

                    <?
	                foreach($sizearr_order as $size_id)
	                {
	                    ?>
	                    <td align="right"><? echo number_format($line_color_size_in[$sewing_line][$size_id],0); ?></td>
	                    <?
	                }
	                ?>
	                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
	            </tr>
	            </tbody>
	            <tfoot>
                <th  colspan="<?=$prod_source==3 ? 8: 7;?>" align="right"><strong>Grand Total :</strong></th>

                <?
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th ><? echo $color_size_in[$size_id]; ?></th>
	                    <?
					}
					?>
	                <th ><? echo $grand_tot_in; ?></th>
	            </tfoot>
	        </table>
	        </div>
	    </fieldset>
	<?
	exit();
}

if($action=="sewingQnty_input_popup")
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	$floor_library=return_library_array( "select id,floor_name from  lib_prod_floor", "id", "floor_name"  );
	$sewing_line_library=return_library_array( "select id,line_name from  lib_sewing_line", "id", "line_name"  );
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');

	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	//var_dump();die;

	$po_details_sql=sql_select("select a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.id=$po_break_down_id group by a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id");
	//echo $po_details_sql;die;

	if($db_type==2)
	{
		$sql= "SELECT  a.challan_no, a.floor_id, a.sewing_line, a.country_id, a.production_source, c.color_number_id, LISTAGG(CAST(c.size_number_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.size_number_id) as size_number_id , a.prod_reso_allo
		from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
		where a.production_type=4 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3)  and c.is_deleted=0
		group by  a.country_id,a.challan_no, a.floor_id, a.sewing_line, a.production_source, a.prod_reso_allo,c.color_number_id
		order by a.country_id,a.challan_no, a.floor_id, a.sewing_line";
	}
	else if($db_type==0)
	{
		$sql= "SELECT a.challan_no,a.floor_id, a.sewing_line,a.country_id,a.production_source, c.color_number_id, group_concat(c.size_number_id) as size_number_id, a.prod_reso_allo
		from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
		where a.production_type=4 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.is_deleted=0
		group by  a.country_id,a.challan_no, a.floor_id, a.sewing_line, a.production_source, a.prod_reso_allo,c.color_number_id
		order by a.country_id,a.challan_no, a.floor_id, a.sewing_line";
	}
	//echo $sql;die;
	//echo $sql; //and a.production_date='$production_date'
	$result=sql_select($sql);

	$sql_color_size= sql_select("SELECT a.challan_no,a.floor_id, a.sewing_line,a.country_id,a.production_source, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=4 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3)  and c.is_deleted=0
	group by   a.country_id,a.challan_no, a.floor_id, a.sewing_line, a.production_source,c.color_number_id,c.size_number_id
	order by a.country_id");
	foreach($sql_color_size as $row)
	{
		$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
		$summery_data[$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=630+$col_width;
	$summer_table_width=230+$col_width;

	?>
	<script>

		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
		}

		function window_close()
		{
			parent.emailwindow.hide();
		}

	</script>
	<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
			<input type="button" value="Print" onClick="print_window()" style="width:100px; margin-left:400px;"  class="formbutton" /><br />
	    <div style="100%" id="report_container">

	        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px;  margin-top:10px;">
	        	<tr>
	            	<td style="font-size:14px; font-weight:bold;">
	                Buyer Name : <? echo $buyer_library[$po_details_sql[0][csf("buyer_name")]]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Job No : <? echo $po_details_sql[0][csf("job_no")]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Style No : <? echo $po_details_sql[0][csf("style_ref_no")]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Garments Item :
					<?
	                    $item_data="";
	                    $garments_item_arr=array_unique(explode(",",$po_details_sql[0][csf("gmts_item_id")]));
	                    foreach($garments_item_arr as $item_id)
	                    {
	                        if($item_data!="") $item_data .=", ";
	                        $item_data .=$garments_item[$item_id];
	                    }
	                    echo $item_data;
	                ?>
	                <br />
	                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;Date : <? echo change_date_format($production_date); ?>
	                <br />
	                Summary
	                </td>
	            </tr>
	        </table>
	        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $summer_table_width; ?>" style="margin-bottom:20px;">
	            <thead>
	                <tr>
	                	<th width="40" rowspan="2">SI</th>
	                    <th width="100" rowspan="2">Color</th>
	                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
	                    <th width="80" rowspan="2" >Total</th>
	                </tr>
	                <tr>
	                <?
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
	                    <?
					}
					?>
	                </tr>
	            </thead>
	            <tbody>
	            <?
				$i=1;
				//var_dump($result);die;
				foreach($summery_data as $color_id=>$row)
				{
					//print_r($row);die;
					?>
	                <tr>
	                    <td align="center"><? echo $i;  ?></td>
	                    <td ><? echo $colorarr[$color_id];  ?></td>
	                    <?
						$summry_color_total_in =0;
	                    foreach($sizearr_order as $size_id)
	                    {
	                        ?>
	                        <td align="right"><? echo number_format($row[$size_id],0) ; $summry_color_total_in+= $row[$size_id]; $summry_color_size_in [$size_id]+=$row[$size_id];?></td>
	                        <?
	                    }
	                    ?>
	                    <td align="right"><? echo  number_format( $summry_color_total_in,0); $grand_tot_in+=$summry_color_total_in; ?></td>
	                </tr>
	                <?
					$i++;
				}
				?>
	            </tbody>
	            <tfoot>
	                <th >&nbsp;</th>
	                <th >&nbsp;</th>
	                <?
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th ><? echo $summry_color_size_in[$size_id]; ?></th>
	                    <?
					}
					?>
	                <th ><? echo $grand_tot_in; ?></th>

	            </tfoot>
	        </table>
	        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px;">
	        	<tr>
	            	<td style="font-size:14px; font-weight:bold;">Details</td>
	            </tr>
	        </table>

	        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
	            <thead>
	                <tr>
	                	<th width="40" rowspan="2">SI</th>
	                    <th width="100" rowspan="2">Country Name</th>
	                    <th width="80" rowspan="2">Source</th>
	                    <th width="70" rowspan="2">Challan</th>
	                    <th width="90" rowspan="2">Sewing Unit</th>
	                    <th width="70" rowspan="2">Sewing Line</th>
	                    <th width="100" rowspan="2">Color</th>
	                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
	                    <th width="80" rowspan="2" >Total</th>
	                </tr>
	                <tr>
	                <?
					$grand_tot_in=0;
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
	                    <?
					}
					?>
	                </tr>

	            </thead>
	            <tbody>
	            <?
				$i=1;
				$k=1;
				//var_dump($result);die;
				foreach($result as $row)
				{
					if(!in_array($row[csf("sewing_line")],$temp_arr[$row[csf("country_id")]]))
					{
						$temp_arr[$row[csf("country_id")]][]=$row[csf("sewing_line")];
						if($k!=1)
						{
							?>
							<tr bgcolor="#CCCCCC">
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<?
								foreach($sizearr_order as $size_id)
								{
									?>
									<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
									<?
								}
								?>
								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
							</tr>
							<?
							$line_color_size_in = $line_color_total_in ="";
						}
						$k++;
					}

					$sewing_line='';
					if($row[csf('prod_reso_allo')]==1)
					{
						$line_number=explode(",",$prod_reso_arr[$row[csf('sewing_line')]]);
						foreach($line_number as $val)
						{
							if($sewing_line=='') $sewing_line=$sewing_line_library[$val]; else $sewing_line.=",".$sewing_line_library[$val];
						}
					}
					else $sewing_line=$sewing_line_library[$row[csf('sewing_line')]];

					?>
	                <tr>
	                    <td align="center"><? echo $i;  ?></td>
	                    <td><p><? echo $country_library[$row[csf("country_id")]];  ?></p></td>
	                    <td><p><? echo $knitting_source[$row[csf("production_source")]]; ?><p></td>
	                    <td><p><? echo $row[csf("challan_no")];  ?></p></td>
	                    <td><p><? echo $floor_library[$row[csf("floor_id")]];  ?></p></td>
	                    <td align="center"><p><? echo $sewing_line; ?></p></td>
	                    <td><p><? echo $colorarr[$row[csf("color_number_id")]];  ?></p></td>
	                    <?
						$color_total_in=0;
	                    foreach($sizearr_order as $size_id)
	                    {
	                        ?>
	                        <td align="right"><p>
							<?
							 	echo number_format($production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id],0) ;
								 $color_total_in+= $production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id];
								 $color_size_in [$size_id]+=$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id];
								 $line_color_total_in+= $production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id];
								 $line_color_size_in [$size_id]+=$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id];
							 ?>
	                        </p></td>
	                        <?
	                    }
	                    ?>
	                    <td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
	                </tr>
	                <?
					$i++;
				}
				?>
	            <tr bgcolor="#CCCCCC">
	                <td>&nbsp;</td>
	                <td>&nbsp;</td>
	                <td>&nbsp;</td>
	                <td>&nbsp;</td>
	                <td>&nbsp;</td>
	                <td>&nbsp;</td>
	                <td>&nbsp;</td>
	                <?
	                foreach($sizearr_order as $size_id)
	                {
	                    ?>
	                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
	                    <?
	                }
	                ?>
	                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
	            </tr>
	            </tbody>
	            <tfoot>
	                <th>&nbsp;</th>
	                <th>&nbsp;</th>
	                <th>&nbsp;</th>
	                <th>&nbsp;</th>
	                <th>&nbsp;</th>
	                <th>&nbsp;</th>
	                <th>&nbsp;</th>
	                <?
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th><? echo $color_size_in[$size_id]; ?></th>
	                    <?
					}
					?>
	                <th><? echo $grand_tot_in; ?></th>

	            </tfoot>
	        </table>
	        </div>
	    </fieldset>
	<?
	exit();
}

if($action=="ironQnty_popup")
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	$floor_library=return_library_array( "select id,floor_name from  lib_prod_floor", "id", "floor_name"  );
	$sewing_line_library=return_library_array( "select id,line_name from  lib_sewing_line", "id", "line_name"  );
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');

	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	//var_dump();die;

	$po_details_sql=sql_select("select a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.id=$po_break_down_id group by a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id");
	//echo $po_details_sql;die;

	if($db_type==2)
	{
		$sql= "SELECT a.challan_no, a.floor_id, a.country_id, c.color_number_id, LISTAGG(CAST(c.size_number_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.size_number_id) as size_number_id
		from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c
		where a.production_type=7 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3)  and c.is_deleted=0
		group by  a.challan_no, a.floor_id, a.country_id, c.color_number_id
		order by a.country_id, a.challan_no, a.floor_id";
	}
	else if($db_type==0)
	{
		$sql= "SELECT a.challan_no, a.floor_id, a.country_id, c.color_number_id, group_concat(c.size_number_id) as size_number_id
		from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
		where a.production_type=7 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3)  and c.is_deleted=0
		group by a.country_id,a.challan_no, a.floor_id, c.color_number_id
		order by a.country_id,a.challan_no, a.floor_id, a.sewing_line";
	}
	//echo $sql;die;

	if($prod_source=='' || $prod_source==0) $prod_source_cond=""; else  $prod_source_cond=" and production_source='$prod_source'";
	//echo $sql; //and a.production_date='$production_date'
	$result=sql_select($sql);

	$sql_color_size= sql_select("SELECT a.challan_no, a.floor_id, a.country_id, c.color_number_id, c.size_number_id, sum(b.production_qnty) as production_qnty
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=7 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3)  and c.is_deleted=0 $prod_source_cond
	group by a.country_id, a.challan_no, a.floor_id, a.sewing_line, c.color_number_id, c.size_number_id");

	foreach($sql_color_size as $row)
	{
		//$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['in'] +=$row[csf('in_quantity')];
		//$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['out'] +=$row[csf('out_quantity')];
		$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('production_qnty')];
		$summery_data[$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=630+$col_width;
	$summer_table_width=230+$col_width;


	?>
	<script>

		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
		}

		function window_close()
		{
			parent.emailwindow.hide();
		}

	</script>
	<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
		<input type="button" value="Print" onClick="print_window()" style="width:100px; margin-left:400px;"  class="formbutton" /><br />
	    <div style="100%" id="report_container">
	        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
	        	<tr>
	            	<td style="font-size:14px; font-weight:bold;">
	                Buyer Name : <? echo $buyer_library[$po_details_sql[0][csf("buyer_name")]]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Job No : <? echo $po_details_sql[0][csf("job_no")]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Style No : <? echo $po_details_sql[0][csf("style_ref_no")]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Garments Item :
					<?
	                    $item_data="";
	                    $garments_item_arr=array_unique(explode(",",$po_details_sql[0][csf("gmts_item_id")]));
	                    foreach($garments_item_arr as $item_id)
	                    {
	                        if($item_data!="") $item_data .=", ";
	                        $item_data .=$garments_item[$item_id];
	                    }
	                    echo $item_data;
	                ?>
	                <br />
	                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;Date : <? echo change_date_format($production_date); ?>
	                <br />
	                Summary
	                </td>
	            </tr>
	        </table>
	        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $summer_table_width; ?>" style="margin-bottom:20px;">
	            <thead>
	                <tr>
	                	<th width="40" rowspan="2">SI</th>
	                    <th width="100" rowspan="2">Color</th>
	                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
	                    <th width="80" rowspan="2" >Total</th>
	                </tr>
	                <tr>
						<?
	                    foreach($sizearr_order as $size_id)
	                    {
	                        ?>
	                        <th width="60"><? echo $sizearr[$size_id]; ?></th>
	                        <?
	                    }
	                    ?>
	                </tr>
	            </thead>
	            <tbody>
	            <?
				$i=1;
				//var_dump($result);die;
				foreach($summery_data as $color_id=>$row)
				{
					//print_r($row);die;
					?>
	                <tr>
	                    <td align="center"><? echo $i;  ?></td>
	                    <td><? echo $colorarr[$color_id];  ?></td>
	                    <?
						$summry_color_total_in =0;
	                    foreach($sizearr_order as $size_id)
	                    {
	                        ?>
	                        <td align="right"><? echo number_format($row[$size_id],0) ; $summry_color_total_in+= $row[$size_id]; $summry_color_size_in [$size_id]+=$row[$size_id];?></td>
	                        <?
	                    }
	                    ?>
	                    <td align="right"><? echo  number_format( $summry_color_total_in,0); $grand_tot_in+=$summry_color_total_in; ?></td>
	                </tr>
	                <?
					$i++;
				}
				?>
	            </tbody>
	            <tfoot>
	                <th>&nbsp;</th>
	                <th>&nbsp;</th>
	                <?
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th><? echo $summry_color_size_in[$size_id]; ?></th>
	                    <?
					}
					?>
	                <th><? echo $grand_tot_in; ?></th>
	            </tfoot>
	        </table>
	        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px;">
	        	<tr>
	            	<td style="font-size:14px; font-weight:bold;">Details</td>
	            </tr>
	        </table>
	        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
	            <thead>
	                <tr>
	                	<th width="40" rowspan="3">SI</th>
	                    <th width="100" rowspan="3">Country Name</th>
	                    <th width="80" rowspan="3">Source</th>
	                    <th width="70" rowspan="3">Challan</th>
	                    <th width="70" rowspan="3">Floor</th>
	                    <th width="100" rowspan="3">Color</th>
	                    <? if($prod_source==1) $prod_source_caption="In-House"; else if($prod_source==3) $prod_source_caption="Out-Bound"; ?>
	                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>"><? echo $prod_source_caption; ?></th>
	                    <th width="80" rowspan="3" >Total</th>
	                </tr>
	                <tr>
	                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
	                </tr>
	                <tr>
	                <?
					$grand_tot_in=0;
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
	                    <?
					}
					?>
	                </tr>
	            </thead>
	            <tbody>
	            <?
				$i=1;
				$k=1;
				//var_dump($result);die;
				foreach($result as $row)
				{
					?>
	                <tr>
	                    <td align="center"><? echo $i;  ?></td>
	                    <td><p><? echo $country_library[$row[csf("country_id")]];  ?></p></td>
	                    <td><p><? echo $knitting_source[$row[csf("production_source")]]; ?><p></td>
	                    <td><p><? echo $row[csf("challan_no")];  ?></p></td>
	                    <td><p><? echo $floor_library[$row[csf("floor_id")]];  ?></p></td>
	                    <td><p><? echo $colorarr[$row[csf("color_number_id")]];  ?></p></td>
	                    <?
						$color_total=0;
	                    foreach($sizearr_order as $size_id)
	                    {
	                        ?>
	                        <td align="right"><p>
							<?
								$production_break_qty=0;
								$production_break_qty=$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('color_number_id')]][$size_id];
							 	echo number_format($production_break_qty,0) ;

								 $color_total+= $production_break_qty;
								 $color_size_in [$size_id]+=$production_break_qty;
							 ?>
	                        </p></td>
	                        <?
	                    }
	                    ?>
	                    <td align="right"><p><? echo number_format($color_total,0); $grand_tot_in+=$color_total; ?></p></td>
	                </tr>
	                <?
					$i++;
				}
				?>
	            </tbody>
	            <tfoot>
	                <th>&nbsp;</th>
	                <th>&nbsp;</th>
	                <th>&nbsp;</th>
	                <th>&nbsp;</th>
	                <th>&nbsp;</th>
	                <th>&nbsp;</th>
	                <?
					foreach($sizearr_order as $size_id)
	                {
	                    ?>
	                    <th align="right"><? echo number_format($color_size_in[$size_id],0); ?></th>
	                    <?
	                }
					?>
	                <th><? echo $grand_tot_in; ?></th>
	            </tfoot>
	        </table>
	        </div>
	    </fieldset>
	<?
	exit();
}

if($action=="finishQnty_popup")
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	$floor_library=return_library_array( "select id,floor_name from  lib_prod_floor", "id", "floor_name"  );
	$sewing_line_library=return_library_array( "select id,line_name from  lib_sewing_line", "id", "line_name"  );
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');

	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	//var_dump();die;

	$po_details_sql=sql_select("select a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.id=$po_break_down_id group by a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id");
	//echo $po_details_sql;die;

	if($db_type==2)
	{
		$sql= "SELECT a.challan_no, a.floor_id, a.country_id, c.color_number_id, LISTAGG(CAST(c.size_number_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.size_number_id) as size_number_id
		from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c
		where a.production_type=8 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3)  and c.is_deleted=0
		group by  a.challan_no, a.floor_id, a.country_id, c.color_number_id
		order by a.country_id, a.challan_no, a.floor_id";
	}
	else if($db_type==0)
	{
		$sql= "SELECT a.challan_no, a.floor_id, a.country_id, c.color_number_id, group_concat(c.size_number_id) as size_number_id
		from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
		where a.production_type=8 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3)  and c.is_deleted=0
		group by a.country_id,a.challan_no, a.floor_id, c.color_number_id
		order by a.country_id,a.challan_no, a.floor_id, a.sewing_line";
	}
	//echo $sql;die;


	//echo $sql; //and a.production_date='$production_date'
	$result=sql_select($sql);

	$sql_color_size= sql_select("SELECT a.challan_no, a.floor_id, a.country_id, c.color_number_id, c.size_number_id,
	 sum(case when production_source=1 then b.production_qnty else 0 end) as in_quantity,
	 sum(case when production_source=3 then b.production_qnty else 0 end) as out_quantity,
	 sum(b.production_qnty) as production_qnty
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=8 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3)  and c.is_deleted=0
	group by a.country_id,a.challan_no, a.floor_id, a.sewing_line, a.production_source,c.color_number_id,c.size_number_id");

	foreach($sql_color_size as $row)
	{
		$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['in'] +=$row[csf('in_quantity')];
		$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['out'] +=$row[csf('out_quantity')];
		$summery_data[$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=630+$col_width;
	$summer_table_width=230+$col_width;
	?>
	<script>

		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
		}

		function window_close()
		{
			parent.emailwindow.hide();
		}

	</script>
	<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
	<input type="button" value="Print" onClick="print_window()" style="width:100px; margin-left:400px;"  class="formbutton" /><br />
    <div style="100%" id="report_container">
        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
        	<tr>
            	<td style="font-size:14px; font-weight:bold;">
                Buyer Name : <? echo $buyer_library[$po_details_sql[0][csf("buyer_name")]]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Job No : <? echo $po_details_sql[0][csf("job_no")]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Style No : <? echo $po_details_sql[0][csf("style_ref_no")]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Garments Item :
				<?
                    $item_data="";
                    $garments_item_arr=array_unique(explode(",",$po_details_sql[0][csf("gmts_item_id")]));
                    foreach($garments_item_arr as $item_id)
                    {
                        if($item_data!="") $item_data .=", ";
                        $item_data .=$garments_item[$item_id];
                    }
                    echo $item_data;
                ?>
                <br />
                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;Date : <? echo change_date_format($production_date); ?>
                <br />
                Summary
                </td>
            </tr>
        </table>
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $summer_table_width; ?>" style="margin-bottom:20px;">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Color</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
					<?
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <th width="60"><? echo $sizearr[$size_id]; ?></th>
                        <?
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
			//var_dump($result);die;
			foreach($summery_data as $color_id=>$row)
			{
				//print_r($row);die;
				?>
                <tr>
                    <td align="center"><? echo $i;  ?></td>
                    <td><? echo $colorarr[$color_id];  ?></td>
                    <?
					$summry_color_total_in =0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><? echo number_format($row[$size_id],0) ; $summry_color_total_in+= $row[$size_id]; $summry_color_size_in [$size_id]+=$row[$size_id];?></td>
                        <?
                    }
                    ?>
                    <td align="right"><? echo  number_format( $summry_color_total_in,0); $grand_tot_in+=$summry_color_total_in; ?></td>
                </tr>
                <?
				$i++;
			}
			?>
            </tbody>
            <tfoot>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th><? echo $summry_color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th><? echo $grand_tot_in; ?></th>
            </tfoot>
        </table>
        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px;">
        	<tr>
            	<td style="font-size:14px; font-weight:bold;">Details</td>
            </tr>
        </table>
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                	<th width="40" rowspan="3">SI</th>
                    <th width="100" rowspan="3">Country Name</th>
                    <th width="80" rowspan="3">Source</th>
                    <th width="70" rowspan="3">Challan</th>
                    <th width="70" rowspan="3">Floor</th>
                    <th width="100" rowspan="3">Color</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">In-House</th>
                    <th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Out-Bound</th>
                    <th width="80" rowspan="3" >Total</th>
                </tr>
                <tr>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                </tr>
                <tr>
                <?
				$grand_tot_in=0;
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
			$k=1;
			//var_dump($result);die;
			foreach($result as $row)
			{
				?>
                <tr>
                    <td align="center"><? echo $i;  ?></td>
                    <td><p><? echo $country_library[$row[csf("country_id")]];  ?></p></td>
                    <td><p><? echo $knitting_source[$row[csf("production_source")]]; ?><p></td>
                    <td><p><? echo $row[csf("challan_no")];  ?></p></td>
                    <td><p><? echo $floor_library[$row[csf("floor_id")]];  ?></p></td>
                    <td><p><? echo $colorarr[$row[csf("color_number_id")]];  ?></p></td>
                    <?
					$color_total_in=0;
                    foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><p>
						<?
							$production_break_qty_in=0;
							$production_break_qty_in=$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('color_number_id')]][$size_id]['in'];
						 	echo number_format($production_break_qty_in,0) ;

							 $color_total_in+= $production_break_qty_in;
							 $color_size_in [$size_id]+=$production_break_qty_in;
						 ?>
                        </p></td>
                        <?
                    }
					$color_total_out=0;
					foreach($sizearr_order as $size_id)
                    {
                        ?>
                        <td align="right"><p>
						<?
							$production_break_qty_out=0;
							$production_break_qty_out=$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('color_number_id')]][$size_id]['out'];
						 	echo number_format($production_break_qty_out,0) ;

							 $color_total_out+= $production_break_qty_out;
							 $color_size_out[$size_id]+=$production_break_qty_out;
						 ?>
                        </p></td>
                        <?
                    }
                    ?>
                    <td align="right"><p><? $color_total=$color_total_in+$color_total_out; echo  number_format( $color_total,0); $grand_tot_in+=$color_total; ?></p></td>
                </tr>
                <?
				$i++;
			}
			?>
            </tbody>
            <tfoot>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <?
				foreach($sizearr_order as $size_id)
                {
                    ?>
                    <th align="right"><? echo number_format($color_size_in[$size_id],0); ?></th>
                    <?
                }
				foreach($sizearr_order as $size_id)
                {
                    ?>
                    <th align="right"><? echo number_format($color_size_out[$size_id],0); ?></th>
                    <?
                }
				?>
                <th><? echo $grand_tot_in; ?></th>
            </tfoot>
        </table>
        </div>
    </fieldset>
	<?
	exit();
}

if ($action=="reject_qty")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,1,1);
	//echo $po_id;
	//echo $company_name;die;
	if($db_type==0)
		{
			$prod_date="and a.production_date ='".change_date_format($production_date,"yyyy-mm-dd", "-")."'";
		}
		else
		{
			$prod_date="and a.production_date = '".change_date_format($production_date,'','',1)."'";
		}

	$sql_variable=sql_select("select cutting_update, printing_emb_production, sewing_production, iron_update, finishing_update from variable_settings_production where company_name=$company_name and variable_list=28 and status_active=1 and is_deleted=0");
	$cutting_variable=$sql_variable[0][csf('cutting_update')];
	$printing_variable=$sql_variable[0][csf('printing_emb_production')];
	$sewing_variable=$sql_variable[0][csf('sewing_production')];
	$iron_variable=$sql_variable[0][csf('iron_update')];
	$finishing_variable=$sql_variable[0][csf('finishing_update')];
	//echo $service_company;
	//$cutting_variable_setting=return_field_value("cutting_update","variable_settings_production","company_name=$company_name and variable_list=28 and status_active=1 and is_deleted=0","cutting_update");
	$po_array=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number");
	$color_Arr_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");

	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id in($po_id)","size_number_id","size_number_id");

	if($cutting_variable==1)
	{
		$sql_cutting=sql_select("Select a.po_break_down_id, sum(a.reject_qnty)  AS cutting_rej_qnty
					from pro_garments_production_mst  a
					where a.production_type =1 and a.po_break_down_id in ($po_id) and a.item_number_id='$item_id' and a.status_active=1
					and a.is_deleted=0 and a.serving_company=$service_company and $prod_date group by po_break_down_id");
	}
	else
	{

		 $sql_cutting=sql_select("Select a.po_break_down_id,c.color_number_id, c.size_number_id,sum(b.reject_qty) AS cutting_rej_qnty
					from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
					where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.production_type=1 and a.po_break_down_id in ($po_id) and a.item_number_id='$item_id' and a.status_active=1 and a.is_deleted=0  and a.serving_company=$service_company $prod_date group by a.po_break_down_id,c.color_number_id, c.size_number_id");

		foreach($sql_cutting as $row)
		{
			if($row[csf('cutting_rej_qnty')]>0)
			{
				$cutting_data[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('cutting_rej_qnty')];
			}
		}
	}

	//var_dump($cutting_data);die;

	if($printing_variable==1)
	{
		$sql_printing=sql_select("Select a.po_break_down_id, sum(a.reject_qnty)  AS printing_rej_qnty
					from pro_garments_production_mst a
					where a.production_type =3 and a.po_break_down_id in ($po_id) and a.item_number_id='$item_id' and a.status_active=1 and a.is_deleted=0 and a.serving_company=$service_company and  $prod_date  group by po_break_down_id");
	}
	else
	{
		$sql_printing=sql_select("Select a.po_break_down_id,c.color_number_id, c.size_number_id,sum(b.reject_qty) AS printing_rej_qnty
					from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
					where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.production_type=3 and a.po_break_down_id in ($po_id) and a.item_number_id='$item_id' and a.status_active=1 and a.is_deleted=0 and a.serving_company=$service_company $prod_date  group by a.po_break_down_id,c.color_number_id, c.size_number_id");

		foreach($sql_printing as $row)
		{
			if($row[csf('printing_rej_qnty')]>0)
			{
				$printing_data[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('printing_rej_qnty')];
			}
		}
	}

	if($sewing_variable==1)
	{
		$sql_sewing=sql_select("Select a.po_break_down_id, sum(a.reject_qnty)  AS sewingout_rej_qnty
					from pro_garments_production_mst a
					where a.production_type =5 and a.po_break_down_id in ($po_id) and a.item_number_id='$item_id' and a.status_active=1 and a.is_deleted=0 and a.serving_company=$service_company $prod_date group by po_break_down_id");
	}
	else
	{
		$sql_sewing=sql_select("Select a.po_break_down_id,c.color_number_id, c.size_number_id,sum(b.reject_qty) AS sewingout_rej_qnty
					from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
					where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.production_type=5 and a.po_break_down_id in ($po_id) and a.item_number_id='$item_id' and a.status_active=1 and a.is_deleted=0 and a.serving_company=$service_company $prod_date group by a.po_break_down_id,c.color_number_id, c.size_number_id");

		foreach($sql_sewing as $row)
		{
			if($row[csf('sewingout_rej_qnty')]>0)
			{
				$sewing_data[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('sewingout_rej_qnty')];
			}
		}
	}

	if($iron_variable==1)
	{
		$sql_iron=sql_select("Select a.po_break_down_id, sum(a.reject_qnty)  AS iron_rej_qnty
					from pro_garments_production_mst a
					where a.production_type =7 and a.po_break_down_id in ($po_id) and a.item_number_id='$item_id' and a.status_active=1 and a.is_deleted=0 and a.serving_company=$service_company $prod_date  group by po_break_down_id");
	}
	else
	{
		$sql_iron=sql_select("Select a.po_break_down_id,c.color_number_id, c.size_number_id,sum(b.reject_qty) AS iron_rej_qnty
					from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
					where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.production_type=7 and a.po_break_down_id in ($po_id) and a.item_number_id='$item_id' and a.status_active=1 and a.is_deleted=0 and a.serving_company=$service_company $prod_date  group by a.po_break_down_id,c.color_number_id, c.size_number_id");

		foreach($sql_iron as $row)
		{
			if($row[csf('iron_rej_qnty')]>0)
			{
				$iron_data[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('iron_rej_qnty')];
			}
		}
	}

	if($finishing_variable==1)
	{
		$sql_finishing=sql_select("Select a.po_break_down_id, sum(a.reject_qnty)  AS finish_rej_qnty
					from pro_garments_production_mst a
					where a.production_type =8 and a.po_break_down_id in ($po_id) and a.item_number_id='$item_id' and a.status_active=1 and a.is_deleted=0 and a.serving_company=$service_company $prod_date  group by po_break_down_id");
	}
	else
	{
		$sql_finishing=sql_select("Select a.po_break_down_id,c.color_number_id, c.size_number_id,sum(b.reject_qty) AS finish_rej_qnty
					from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
					where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.production_type=8 and a.po_break_down_id in ($po_id) and a.item_number_id='$item_id' and a.status_active=1 and a.is_deleted=0 and a.serving_company=$service_company $prod_date  group by a.po_break_down_id,c.color_number_id, c.size_number_id");

		foreach($sql_finishing as $row)
		{
			if($row[csf('finish_rej_qnty')]>0)
			{
				$finishing_data[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('finish_rej_qnty')];
			}
		}
	}


	?>
    <div id="data_panel" align="center" style="width:100%">
		<script>
        function new_window()
        {
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write(document.getElementById('details_reports').innerHTML);
        d.close();
        }
        </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
    </div>
    <div style="color:#FF0000; font-size:14px; font-weight:bold; float:left; width:635px">This Pop-Up Will Be Perfect When Order, Production and All Procedure Follow Color or Color & Size Level.</div>
    <div style="width:635px" align="center" id="details_reports">
    <table id="tbl_id" class="rpt_table" width="" border="1" rules="all" >
    	<thead>
        	<tr>
            	<th width="60">Buyer</th>
                <th width="90">Job Number</th>
                <th width="90">Style Name</th>
                <th width="150">Order Number</th>
                <th width="70">Ship Date</th>
                <th width="100">Item Name</th>
                <th >Order Qty.</th>
            </tr>
        </thead>
       	<?
        	$buyer_short_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name"  );
			if($db_type==0)
			{
 				$sql = "select a.job_no_mst,group_concat(distinct(a.po_number)) as po_number,a.pub_shipment_date, sum(a.po_quantity) as po_quantity,a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio
					from wo_po_break_down a, wo_po_details_master b, wo_po_details_mas_set_details c
					where a.job_no_mst=b.job_no and b.job_no=c.job_no and a.id in ($po_id) and c.gmts_item_id='$item_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			}
			else
			{
				$sql = "select a.job_no_mst, LISTAGG(a.po_number, ',') WITHIN GROUP (ORDER BY a.id) as po_number,max(a.pub_shipment_date) as pub_shipment_date, sum(a.po_quantity) as po_quantity,a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio
					from wo_po_break_down a, wo_po_details_master b, wo_po_details_mas_set_details c
					where a.job_no_mst=b.job_no and b.job_no=c.job_no and a.id in ($po_id) and c.gmts_item_id='$item_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.job_no_mst, a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio";
			}
			//echo $sql;
			$resultRow=sql_select($sql);

 		?>
        <tr>
        	<td><? echo $buyer_short_library[$resultRow[0][csf("buyer_name")]]; ?></td>
            <td><p><? echo $resultRow[0][csf("job_no_mst")]; ?></p></td>
            <td><p><? echo $resultRow[0][csf("style_ref_no")]; ?></p></td>
            <td><p><? echo implode(",",array_unique(explode(",",$resultRow[0][csf("po_number")]))); ?></p></td>
            <td><? echo change_date_format($resultRow[0][csf("pub_shipment_date")]); ?></td>
            <td><? echo $garments_item[$item_id]; ?></td>
            <td><? echo $resultRow[0][csf("po_quantity")]*$resultRow[0][csf("set_item_ratio")]; ?></td>
        </tr>
    </table>
    <br />
    <?


	//Cutting Data Display Here
	if($cutting_variable==1)
	{
		if(!empty($sql_cutting))
		{
			 $tbl_width=200;
			?>
            <span style="font-size:18px; font-weight:bold;">Cutting Reject Quantity</span>
            <table width="<? echo $tbl_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
            	<thead>
                	<th width="50">SL</th>
                	<th>Reject Quantity</th>
                </thead>
                <tbody>
                	<?
					$i=1;
					foreach($sql_cutting as $row)
					{
						?>
                        <tr>
                            <td><? echo $i; ?></td>
                            <td align="right"><? echo number_format($row[csf("cutting_rej_qnty")],2);?></td>
                        </tr>
                        <?
						$i++;
					}
					?>
                </tbody>
            </table>
            <br />
            <?

		}
	}
	else
	{
			$collspan=count($sizearr_order);
			$table_width=(230+($collspan*60));
			$colspan=2;

		if(!empty($sql_cutting))
		{
			?>
            <span style="font-size:18px; font-weight:bold;">Cutting Reject Quantity</span>
            <table width="<? echo $table_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
            	<thead>
                	<th width="50">SL</th>
                    <th width="100">Color</th>
                    <?
					foreach($sizearr_order as $size_id)
					{
						?>
                        <th width="60"><? echo $sizearr[$size_id]; ?></th>
                        <?
					}
					?>
                    <th >Color Total</th>
                </thead>
                <tbody>
                	<?
					$i=1;$grand_total_cutting=0;
					foreach($cutting_data as $order_id=>$value)
					{
						foreach($value as $color_id=>$val)
						{
							?>
							<tr>
								<td><? echo $i; ?></td>
                                <td><? echo $color_Arr_library[$color_id]; ?></td>
                                <?
								$color_total_cutting=0;
								foreach($sizearr_order as $size_id)
								{
									?>
                                    <td align="right">
									<?
										echo number_format($cutting_data[$order_id][$color_id][$size_id],0);
										$color_total_cutting+= $cutting_data[$order_id][$color_id][$size_id];
										$color_size_cutting [$size_id]+=$cutting_data[$order_id][$color_id][$size_id];
									?>
                                    </td>
                                    <?
								}
								?>
                                <td align="right"><? echo number_format($color_total_cutting,0); $grand_total_cutting+=$color_total_cutting;?></td>
							</tr>
							<?
							$i++;
						}
					}
					?>
                    <tr bgcolor="#CCCCCC" style="font-size:16px; font-weight:bold;">
                        <td colspan="<? echo $colspan; ?>" align="right">Total:</td>
                        <?
                        foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><? echo number_format($color_size_cutting [$size_id],0);?></td>
							<?
						}
						?>
                        <td align="right"><? echo number_format($grand_total_cutting,0); ?></td>
                    </tr>
                </tbody>
            </table>
            <br />
            <?

		}
	}
	//emblish Data Display Here
	if($printing_variable==1)
	{
		if(!empty($sql_printing))
		{
			$tbl_width=200;
			?>
            <span style="font-size:18px; font-weight:bold;">Embellishment Reject Quantity</span>
            <table width="<? echo $tbl_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
            	<thead>
                	<th width="50">SL</th>
                	<th>Reject Quantity</th>
                </thead>
                <tbody>
                	<?
					$i=1;
					foreach($sql_printing as $row)
					{
						?>
                        <tr>
                            <td><? echo $i; ?></td>
                            <td align="right"><? echo number_format($row[csf("printing_rej_qnty")],2);?></td>
                        </tr>
                        <?
						$i++;
					}
					?>
                </tbody>
            </table>
            <br />
            <?
		}
	}
	else
	{
		$collspan=count($sizearr_order);
			$table_width=(230+($collspan*60));
			$colspan=2;
		if(!empty($sql_printing))
		{
			?>
            <span style="font-size:18px; font-weight:bold;">Embellishment Reject Quantity</span>
            <table width="<? echo $table_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
            	<thead>
                	<th width="50">SL</th>
                    <th width="100">Color</th>
                    <?
					foreach($sizearr_order as $size_id)
					{
						?>
                        <th width="60"><? echo $sizearr[$size_id]; ?></th>
                        <?
					}
					?>
                    <th>Color Total</th>
                </thead>
                <tbody>
                	<?
					$i=1; $grand_total_printing=0;
					foreach($printing_data as $order_id=>$value)
					{
						foreach($value as $color_id=>$val)
						{
							?>
							<tr>
								<td><? echo $i; ?></td>
                                <td><? echo $color_Arr_library[$color_id]; ?></td>
                                <?
								$color_total_printing=0;
								foreach($sizearr_order as $size_id)
								{
									?>
                                    <td align="right">
									<?
										echo number_format($printing_data[$order_id][$color_id][$size_id],0);
										$color_total_printing+= $printing_data[$order_id][$color_id][$size_id];
										$color_size_printing[$size_id]+=$printing_data[$order_id][$color_id][$size_id];
									?>
                                    </td>
                                    <?
								}
								?>
                                <td align="right"><? echo number_format($color_total_printing,0); $grand_total_printing+=$color_total_printing;?></td>
							</tr>
							<?
							$i++;
						}
					}
					?>
                    <tr bgcolor="#CCCCCC" style="font-size:16px; font-weight:bold;">
                        <td colspan="<? echo $colspan; ?>" align="right">Total:</td>
                        <?
                        foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><? echo number_format($color_size_printing[$size_id],0);?></td>
							<?
						}
						?>
                        <td align="right"><? echo number_format($grand_total_printing,0); ?></td>
                    </tr>
                </tbody>
            </table>
            <br />
            <?

		}
	}

	//Sewing Data Display Here
	if($sewing_variable==1)
	{
		if(!empty($sql_sewing))
		{
			 $tbl_width=250;
			?>
            <span style="font-size:18px; font-weight:bold;">Sewing Reject Quantity</span>
            <table width="<? echo $tbl_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
            	<thead>
                	<th width="50">SL</th>
                	<th>Reject Quantity</th>
                </thead>
                <tbody>
                	<?
					$i=1;
					foreach($sql_sewing as $row)
					{
						?>
                        <tr>
                            <td><? echo $i; ?></td>

                            <td align="right"><? echo number_format($row[csf("sewingout_rej_qnty")],2);?></td>
                        </tr>
                        <?
						$i++;
					}
					?>
                </tbody>
            </table>
            <br />
            <?
		}
	}
	else
	{
		$collspan=count($sizearr_order);
		$table_width=(230+($collspan*60));
		$colspan=2;
		if(!empty($sql_sewing))
		{
			?>
            <span style="font-size:18px; font-weight:bold;">Sewing Reject Quantity</span>
            <table width="<? echo $table_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
            	<thead>
                	<th width="50">SL</th>
                    <th width="100">Color</th>
                    <?
					foreach($sizearr_order as $size_id)
					{
						?>
                        <th width="60"><? echo $sizearr[$size_id]; ?></th>
                        <?
					}
					?>
                    <th >Color Total</th>
                </thead>
                <tbody>
                	<?
					$i=1;$grand_total_sewing=0;
					foreach($sewing_data as $order_id=>$value)
					{
						foreach($value as $color_id=>$val)
						{
							?>
							<tr>
								<td><? echo $i; ?></td>
                                <td><? echo $color_Arr_library[$color_id]; ?></td>
                                <?
								$color_total_sewing=0;
								foreach($sizearr_order as $size_id)
								{
									?>
                                    <td align="right">
									<?
										echo number_format($sewing_data[$order_id][$color_id][$size_id],0);
										$color_total_sewing+= $sewing_data[$order_id][$color_id][$size_id];
										$color_size_sewing[$size_id]+=$sewing_data[$order_id][$color_id][$size_id];
									?>
                                    </td>
                                    <?
								}
								?>
                                <td align="right"><? echo number_format($color_total_sewing,0); $grand_total_sewing+=$color_total_sewing;?></td>
							</tr>
							<?
							$i++;
						}
					}
					?>
                    <tr bgcolor="#CCCCCC" style="font-size:16px; font-weight:bold;">
                        <td colspan="<? echo $colspan; ?>" align="right">Total:</td>
                        <?
                        foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><? echo number_format($color_size_sewing[$size_id],0);?></td>
							<?
						}
						?>
                        <td align="right"><? echo number_format($grand_total_sewing,0); ?></td>
                    </tr>
                </tbody>
            </table>
            <br />
            <?

		}
	}

	//Iron Data Display Here
	if($iron_variable==1)
	{
		if(!empty($sql_iron))
		{
			 $tbl_width=200;
			?>
            <span style="font-size:18px; font-weight:bold;">Iron  Reject Quantity</span>
            <table width="<? echo $tbl_width;?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
            	<thead>
                	<th width="50">SL</th>
                	<th>Reject Quantity</th>
                </thead>
                <tbody>
                	<?
					$i=1;
					foreach($sql_iron as $row)
					{
						?>
                        <tr>
                            <td><? echo $i; ?></td>
                            <td align="right"><? echo number_format($row[csf("iron_rej_qnty")],2);?></td>
                        </tr>
                        <?
						$i++;
					}
					?>
                </tbody>
            </table>
            <br />
            <?

		}
	}
	else
	{
		$collspan=count($sizearr_order);
		$table_width=(230+($collspan*60));
			$colspan=2;
		if(!empty($sql_iron))
		{
			?>
            <span style="font-size:18px; font-weight:bold;">Iron Reject Quantity</span>
            <table width="<? echo $table_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
            	<thead>
                	<th width="50">SL</th>
                    <th width="100">Color</th>
                    <?
					foreach($sizearr_order as $size_id)
					{
						?>
                        <th width="60"><? echo $sizearr[$size_id]; ?></th>
                        <?
					}
					?>
                    <th >Color Total</th>
                </thead>
                <tbody>
                	<?
					$i=1;$grand_total_iron=0;
					foreach($iron_data as $order_id=>$value)
					{
						foreach($value as $color_id=>$val)
						{
							?>
							<tr>
								<td><? echo $i; ?></td>

                                <td><? echo $color_Arr_library[$color_id]; ?></td>
                                <?
								$color_total_iron=0;
								foreach($sizearr_order as $size_id)
								{
									?>
                                    <td align="right">
									<?
										echo number_format($iron_data[$order_id][$color_id][$size_id],0);
										$color_total_iron+= $iron_data[$order_id][$color_id][$size_id];
										$color_size_iron[$size_id]+=$iron_data[$order_id][$color_id][$size_id];
									?>
                                    </td>
                                    <?
								}
								?>
                                <td align="right"><? echo number_format($color_total_iron,0); $grand_total_iron+=$color_total_iron;?></td>
							</tr>
							<?
							$i++;
						}
					}
					?>
                    <tr bgcolor="#CCCCCC" style="font-size:16px; font-weight:bold;">
                        <td colspan="<? echo $colspan; ?>" align="right">Total:</td>
                        <?
                        foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><? echo number_format($color_size_iron[$size_id],0);?></td>
							<?
						}
						?>
                        <td align="right"><? echo number_format($grand_total_iron,0); ?></td>
                    </tr>
                </tbody>
            </table>
            <br />
            <?

		}
	}

	//Finish Data Display Here
	if($finishing_variable==1)
	{
		if(!empty($sql_finishing))
		{
			 $tbl_width=250;
			?>
            <span style="font-size:18px; font-weight:bold;">Finishing  Reject Quantity</span>
            <table width="<? echo $tbl_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
            	<thead>
                	<th width="50">SL</th>
                	<th>Reject Quantity</th>
                </thead>
                <tbody>
                	<?
					$i=1;
					foreach($sql_finishing as $row)
					{
						?>
                        <tr>
                            <td><? echo $i; ?></td>
                            <td align="right"><? echo number_format($row[csf("finish_rej_qnty")],2);?></td>
                        </tr>
                        <?
						$i++;
					}
					?>
                </tbody>
            </table>
            <br />
            <?
		}
	}
	else
	{
		$collspan=count($sizearr_order);
		$table_width=(230+($collspan*60));
		$colspan=2;
		if(!empty($sql_finishing))
		{
			?>
            <span style="font-size:18px; font-weight:bold;">Finishing Reject Quantity</span>
            <table width="<? echo $table_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
            	<thead>
                	<th width="50">SL</th>
                    <th width="100">Color</th>
                    <?
					foreach($sizearr_order as $size_id)
					{
						?>
                        <th width="60"><? echo $sizearr[$size_id]; ?></th>
                        <?
					}
					?>
                    <th>Color Total</th>
                </thead>
                <tbody>
                	<?
					$i=1;$grand_total_finish=0;
					foreach($finishing_data as $order_id=>$value)
					{
						foreach($value as $color_id=>$val)
						{
							?>
							<tr>
								<td><? echo $i; ?></td>
                                <td><? echo $color_Arr_library[$color_id]; ?></td>
                                <?
								$color_total_finish=0;
								foreach($sizearr_order as $size_id)
								{
									?>
                                    <td align="right">
									<?
										echo number_format($finishing_data[$order_id][$color_id][$size_id],0);
										$color_total_finish+= $finishing_data[$order_id][$color_id][$size_id];
										$color_size_finish[$size_id]+=$finishing_data[$order_id][$color_id][$size_id];
									?>
                                    </td>
                                    <?
								}
								?>
                                <td align="right"><? echo number_format($color_total_finish,0); $grand_total_finish+=$color_total_finish;?></td>
							</tr>
							<?
							$i++;
						}
					}
					?>
                    <tr bgcolor="#CCCCCC" style="font-size:16px; font-weight:bold;">
                        <td colspan="<? echo $colspan; ?>" align="right">Total:</td>
                        <?
                        foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><? echo number_format($color_size_finish[$size_id],0);?></td>
							<?
						}
						?>
                        <td align="right"><? echo number_format($grand_total_finish,0); ?></td>
                    </tr>
                </tbody>
            </table>
            <?
		}
	}
	?>
    </div>
    <?
	exit();

}

if($action=="production_popup_subcon")
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	$gmts_item_id=explode("_", $gmts_item_id);
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$order_library=return_library_array( "select id,order_no from  subcon_ord_dtls", "id", "order_no");
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
	$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name");
	$sewing_line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name");
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');

	$order_sql= "SELECT b.id, b.order_no as po_number,  b.cust_style_ref as style_ref_no, a.party_id as buyer_name,  c.item_id as gmts_item_id,b.job_no_mst as job_no,c.color_id as color_number_id ,c.size_id as size_number_id,sum(c.qnty) as  qnty from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.subcon_job=b.job_no_mst and a.id=c.mst_id and b.id=c.order_id and 	 c.order_id=b.id  and b.id=$po_break_down_id and c.item_id='$gmts_item_id[0]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   group by  b.id, b.order_no,  b.cust_style_ref, a.party_id,  c.item_id,b.job_no_mst  ,c.size_id, c.color_id";

	$po_details_sql=sql_select($order_sql);
	$sizearr_order=array();
	foreach($po_details_sql as $val)
	{
		$sizearr_order[$val[csf("size_number_id")]]=$val[csf("size_number_id")];
		$summary_data[$val[csf('color_number_id')]]+=$val[csf('qnty')];
		$summary_data2[$val[csf('color_number_id')]][$val[csf('size_number_id')]] +=$val[csf('qnty')];
	}

	$prod_sql= "SELECT  c.company_id,b.color_id as color_number_id, c.line_id as sewing_line,c.floor_id,c.challan_no,c.prod_reso_allo, c.order_id, c.production_date,
	NVL(sum(CASE WHEN c.production_type ='1' THEN  d.prod_qnty  ELSE 0 END),0) AS cutting_qnty,
	sum(CASE WHEN c.production_type ='7' THEN  d.prod_qnty  ELSE 0 END) AS sewing_input_qnty,

	NVL(sum(CASE WHEN c.production_type ='2' THEN  d.prod_qnty  ELSE 0 END),0) AS sewingout_qnty,
	NVL(sum(CASE WHEN c.production_type ='3' THEN  d.prod_qnty  ELSE 0 END),0) AS ironout_qnty,
	NVL(sum(CASE WHEN c.production_type ='4' THEN  d.prod_qnty  ELSE 0 END),0) AS gmts_fin_qnty
	from
	subcon_gmts_prod_dtls c,subcon_gmts_prod_col_sz d,subcon_ord_breakdown b
	where c.id=d.dtls_id and b.id=d.ord_color_size_id and  c.status_active=1 and c.is_deleted=0 and c.production_date='$production_date' and b.order_id=$po_break_down_id and b.item_id='$gmts_item_id[0]' and c.production_type ='$page' group by c.company_id,b.color_id, c.line_id,c.floor_id,c.challan_no,c.prod_reso_allo, c.order_id, c.production_date ";


	$prod_sql2= "SELECT  c.company_id,b.color_id as color_number_id,b.size_id as size_number_id, c.line_id as sewing_line,c.floor_id,c.challan_no,c.prod_reso_allo, c.order_id, c.production_date,
	sum(CASE WHEN c.production_type ='$page' THEN  d.prod_qnty  ELSE 0 END) AS production_qnty
	from
	subcon_gmts_prod_dtls c,subcon_gmts_prod_col_sz d,subcon_ord_breakdown b
	where c.id=d.dtls_id and b.id=d.ord_color_size_id and  c.status_active=1 and c.is_deleted=0 and c.production_date='$production_date' and b.order_id=$po_break_down_id and b.item_id='$gmts_item_id[0]' and c.production_type ='$page' group by c.company_id,b.color_id,b.size_id, c.line_id,c.floor_id,c.challan_no,c.prod_reso_allo, c.order_id, c.production_date ";




	foreach(sql_select($prod_sql2) as $row)
	{
		$production_break_qnty[$row[csf('company_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
 	}
	$result=sql_select($prod_sql);

	$col_width=60*count($sizearr_order);
	//$table_width=630+$col_width;
	$table_width=630+$col_width;
	$summer_table_width=230+$col_width;
	?>
	<script>

		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
		}

		function window_close()
		{
			parent.emailwindow.hide();
		}

	</script>
	<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
			<input type="button" value="Print" onClick="print_window()" style="width:100px; margin-left:400px;"  class="formbutton" /><br />
	    <div style="100%" id="report_container">

	        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
	        	<tr>
	            	<td style="font-size:14px; font-weight:bold;">
	                Buyer Name : <? echo $buyer_library[$po_details_sql[0][csf("buyer_name")]]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Job No : <? echo $po_details_sql[0][csf("job_no")]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Style No : <? echo $po_details_sql[0][csf("style_ref_no")]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Garments Item :
					<?
	                    $item_data="";
	                    $garments_item_arr=array_unique(explode(",",$po_details_sql[0][csf("gmts_item_id")]));
	                    foreach($garments_item_arr as $item_id)
	                    {
	                        if($item_data!="") $item_data .=", ";
	                        $item_data .=$garments_item[$item_id];
	                    }
	                    echo $item_data;
	                ?>
	                <br />
	                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;Date : <? echo change_date_format($production_date); ?>
	                <br />
	                Summary
	                </td>
	            </tr>
	        </table>
	        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $summer_table_width; ?>" style="margin-bottom:20px;">
	            <thead>
	                <tr>
	                	<th width="40" rowspan="2">SI</th>
	                    <th width="100" rowspan="2">Color</th>
	                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
	                    <th width="80" rowspan="2" >Total</th>
	                </tr>
	                <tr>
	                <?
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
	                    <?
					}
					?>
	                </tr>
	            </thead>
	            <tbody>
	            <?
				$i=1;
				//var_dump($result);die;
				foreach($summary_data as $color_id=>$row)
				{
					?>
	                <tr>
	                    <td align="center"><? echo $i;  ?></td>
	                    <td ><? echo $colorarr[$color_id];  ?></td>
	                    <?
						$summry_color_total_in =0;
	                    foreach($sizearr_order as $size_id)
	                    {
	                        ?>
	                        <td align="right"><? echo number_format($summary_data2[$color_id][$size_id],0) ; $summry_color_total_in+= $summary_data2[$color_id][$size_id]; $summry_color_size_in[$size_id]+=$summary_data2[$color_id][$size_id];?></td>
	                        <?
	                    }
	                    ?>
	                    <td align="right"><? echo  number_format( $summry_color_total_in,0); $grand_tot_in+=$summry_color_total_in; ?></td>
	                </tr>
	                <?
					$i++;
				}
				?>
	            </tbody>
	            <tfoot>
	                <th >&nbsp;</th>
	                <th >&nbsp;</th>
	                <?
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th ><? echo $summry_color_size_in[$size_id]; ?></th>
	                    <?
					}
					?>
	                <th><? echo $grand_tot_in; ?></th>
	            </tfoot>
	        </table>
	        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px;">
	        	<tr>
	            	<td style="font-size:14px; font-weight:bold;">Details</td>
	            </tr>
	        </table>
	        <div>
		        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
		            <thead>
		                <tr>
		                	<th width="40" rowspan="2">SI</th>
		                    <th width="100" rowspan="2">Company Name</th>


		                    <th width="70" rowspan="2">Challan</th>
		                    <th width="90" rowspan="2">Sewing Unit</th>
		                    <th width="70" rowspan="2">Sewing Line</th>
		                    <th width="100" rowspan="2">Color</th>
		                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
		                    <th width="80" rowspan="2" >Total</th>
		                </tr>
		                <tr>
		                <?
						$grand_tot_in=0;
						foreach($sizearr_order as $size_id)
						{
							?>
		                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
		                    <?
						}
						?>
		                </tr>

		            </thead>
		            <tbody>
		            <?
					$i=1;
					$k=1;
					//var_dump($result);die;
					foreach($result as $row)
					{
						if(!in_array($row[csf("sewing_line")],$temp_arr[$row[csf("country_id")]]))
						{
							$temp_arr[$row[csf("country_id")]][]=$row[csf("sewing_line")];
							if($k!=1)
							{
								?>
								<tr bgcolor="#CCCCCC">
									<td >&nbsp;</td>
									<td>&nbsp;</td>

									<td >&nbsp;</td>
									<td >&nbsp;</td>
									<td >&nbsp;</td>
									<td >&nbsp;</td>
									<?
									foreach($sizearr_order as $size_id)
									{
										?>
										<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
										<?
									}
									?>
									<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
								</tr>
								<?
								$line_color_size_in = $line_color_total_in ="";
							}
							$k++;
						}

						$sewing_line='';
						if($row[csf('prod_reso_allo')]==1)
						{
							$line_number=explode(",",$prod_reso_arr[$row[csf('sewing_line')]]);
							foreach($line_number as $val)
							{
								if($sewing_line=='') $sewing_line=$sewing_line_library[$val]; else $sewing_line.=",".$sewing_line_library[$val];
							}
						}
						else $sewing_line=$sewing_line_library[$row[csf('sewing_line')]];

						?>
		                <tr>
		                    <td align="center"><? echo $i;  ?></td>
		                    <td ><p><? echo $company_library[$row[csf("company_id")]];  ?></p></td>

		                    <td ><p><? echo $row[csf("challan_no")];  ?></p></td>
		                    <td ><p><? echo $floor_library[$row[csf("floor_id")]];  ?></p></td>
		                    <td align="center"><p><? echo $sewing_line;  ?></p></td>
		                    <td ><p><? echo $colorarr[$row[csf("color_number_id")]];  ?></p></td>
		                    <?
							$color_total_in=0;
		                    foreach($sizearr_order as $size_id)
		                    {
								$Production_qty=0;
		                        ?>
		                        <td align="right"><p>
								<?
									$Production_qty=$production_break_qnty[$row[csf('company_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('color_number_id')]][$size_id];
								 	echo number_format($Production_qty,0);
									 $color_total_in+=$Production_qty; $color_size_in [$size_id]+=$Production_qty; $line_color_total_in+=$Production_qty; $line_color_size_in [$size_id]+=$Production_qty;
								 ?>
		                        </p></td>
		                        <?
		                    }
		                    ?>
		                    <td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
		                </tr>
		                <?
						$i++;
					}
					?>
		            <tr bgcolor="#CCCCCC">
		                <td >&nbsp;</td>
		                <td>&nbsp;</td>

		                <td >&nbsp;</td>
		                <td >&nbsp;</td>
		                <td >&nbsp;</td>
		                <td >&nbsp;</td>
		                <?
		                foreach($sizearr_order as $size_id)
		                {
		                    ?>
		                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
		                    <?
		                }
		                ?>
		                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
		            </tr>
		            </tbody>
		            <tfoot>
		                <th >&nbsp;</th>
		                <th>&nbsp;</th>

		                <th >&nbsp;</th>
		                <th >&nbsp;</th>
		                <th >&nbsp;</th>
		                <th >&nbsp;</th>
		                <?
						foreach($sizearr_order as $size_id)
						{
							?>
		                	<th ><? echo $color_size_in[$size_id]; ?></th>
		                    <?
						}
						?>
		                <th ><? echo $grand_tot_in; ?></th>
		            </tfoot>
		        </table>
	        </div>
	        </div>
	    </fieldset>
	<?
	exit();
}
if($action=="popup_bound3")
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name");
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number");
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
	$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name");
	$sewing_line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name");
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');

	$sizearr_order=return_library_array("select size_number_id, size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	//var_dump();die;

	$po_details_sql=sql_select("select a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.id=$po_break_down_id group by a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id");
	//echo $po_details_sql;die;
	//For Show Date Location and Floor
	$ex_item_id=explode("__",$gmts_item_id);
	$gmt_item_id=$ex_item_id[0];
	$serving_comp_id=$ex_item_id[1];

	if($location_id!=0) $location_cond=" and a.location in($location_id)"; else  $location_cond="";
	if($floor_id!=0) $floor_cond=" and a.floor_id in($floor_id)"; else  $floor_cond="";
	if($sewing_line!=0) $sewing_line_cond=" and a.sewing_line in($sewing_line)"; else  $sewing_line_cond="";
	if($serving_comp_id!=0) $serving_comp_cond=" and a.serving_company in($serving_comp_id)"; else $serving_comp_cond="";

	if($db_type==2)
	{
		$sql= "SELECT  a.challan_no, a.floor_id, a.sewing_line, a.country_id, a.production_source, a.serving_company, c.color_number_id, LISTAGG(CAST(c.size_number_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.size_number_id) as size_number_id, a.prod_reso_allo
		from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
		where a.production_type=$page and a.item_number_id=$gmt_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.production_source='$prod_source' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3)  and c.is_deleted=0 $location_cond  $floor_cond $sewing_line_cond $serving_comp_cond and a.po_break_down_id = c.po_break_down_id
		group by  a.country_id,a.challan_no, a.floor_id, a.sewing_line, a.production_source, a.serving_company, a.prod_reso_allo,c.color_number_id
		order by a.country_id,a.challan_no, a.floor_id, a.sewing_line";
	}
	else if($db_type==0)
	{
		$sql= "SELECT a.challan_no, a.floor_id, a.sewing_line, a.country_id, a.production_source, a.serving_company, c.color_number_id, group_concat(c.size_number_id) as size_number_id, a.prod_reso_allo
		from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
		where a.production_type=$page and a.item_number_id=$gmt_item_id and a.production_date='$production_date' and a.production_source='$prod_source' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3)  and c.is_deleted=0 $location_cond  $floor_cond $sewing_line_cond $serving_comp_cond and a.po_break_down_id = c.po_break_down_id
		group by  a.country_id,a.challan_no, a.floor_id, a.sewing_line, a.production_source, a.serving_company,a.prod_reso_allo,c.color_number_id
		order by a.country_id,a.challan_no, a.floor_id, a.sewing_line";
	}

	//echo $sql; //and a.production_date='$production_date'
	$result=sql_select($sql);

	$sql_color_size= sql_select("SELECT a.challan_no, a.floor_id, a.sewing_line, a.country_id, a.production_source, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a, pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=$page and a.item_number_id=$gmt_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.production_source='$prod_source' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $location_cond  $floor_cond $sewing_line_cond $serving_comp_cond and a.po_break_down_id = c.po_break_down_id and c.status_active in(1,2,3)  and c.is_deleted=0
	group by a.country_id,a.challan_no, a.floor_id, a.sewing_line, a.production_source,c.color_number_id,c.size_number_id");

	foreach($sql_color_size as $row)
	{
		$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
		$summery_data[$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	//$table_width=630+$col_width;
	if($prod_source==3) $table_width=750+$col_width; else $table_width=630+$col_width;
	$summer_table_width=230+$col_width;
	?>
	<script>

		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
		}

		function window_close()
		{
			parent.emailwindow.hide();
		}

	</script>
	<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
			<input type="button" value="Print" onClick="print_window()" style="width:100px; margin-left:400px;"  class="formbutton" /><br />
	    <div style="100%" id="report_container">

	        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
	        	<tr>
	            	<td style="font-size:14px; font-weight:bold;">
	                Buyer Name : <? echo $buyer_library[$po_details_sql[0][csf("buyer_name")]]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Job No : <? echo $po_details_sql[0][csf("job_no")]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Style No : <? echo $po_details_sql[0][csf("style_ref_no")]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Garments Item :
					<?
	                    $item_data="";
	                    $garments_item_arr=array_unique(explode(",",$po_details_sql[0][csf("gmts_item_id")]));
	                    foreach($garments_item_arr as $item_id)
	                    {
	                        if($item_data!="") $item_data .=", ";
	                        $item_data .=$garments_item[$item_id];
	                    }
	                    echo $item_data;
	                ?>
	                <br />
	                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;Date : <? echo change_date_format($production_date); ?>
	                <br />
	                Summary
	                </td>
	            </tr>
	        </table>
	        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $summer_table_width; ?>" style="margin-bottom:20px;">
	            <thead>
	                <tr>
	                	<th width="40" rowspan="2">SI</th>
	                    <th width="100" rowspan="2">Color</th>
	                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
	                    <th width="80" rowspan="2" >Total</th>
	                </tr>
	                <tr>
	                <?
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
	                    <?
					}
					?>
	                </tr>
	            </thead>
	            <tbody>
	            <?
				$i=1;
				//var_dump($result);die;
				foreach($summery_data as $color_id=>$row)
				{
					?>
	                <tr>
	                    <td align="center"><? echo $i;  ?></td>
	                    <td ><? echo $colorarr[$color_id];  ?></td>
	                    <?
						$summry_color_total_in =0;
	                    foreach($sizearr_order as $size_id)
	                    {
	                        ?>
	                        <td align="right"><? echo number_format($row[$size_id],0) ; $summry_color_total_in+= $row[$size_id]; $summry_color_size_in [$size_id]+=$row[$size_id];?></td>
	                        <?
	                    }
	                    ?>
	                    <td align="right"><? echo  number_format( $summry_color_total_in,0); $grand_tot_in+=$summry_color_total_in; ?></td>
	                </tr>
	                <?
					$i++;
				}
				?>
	            </tbody>
	            <tfoot>
	                <th >&nbsp;</th>
	                <th >&nbsp;</th>
	                <?
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th ><? echo $summry_color_size_in[$size_id]; ?></th>
	                    <?
					}
					?>
	                <th><? echo $grand_tot_in; ?></th>
	            </tfoot>
	        </table>
	        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px;">
	        	<tr>
	            	<td style="font-size:14px; font-weight:bold;">Details</td>
	            </tr>
	        </table>
	        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
	            <thead>
	                <tr>
	                	<th width="40" rowspan="2">SI</th>
	                    <th width="100" rowspan="2">Country Name</th>
	                    <th width="80" rowspan="2">Source</th>
	                    <?
						if($prod_source==3)
						{
							?>
	                    	<th width="120" rowspan="2">Serving Company</th>
	                        <?
						}
						?>
	                    <th width="70" rowspan="2">Challan</th>
	                    <th width="90" rowspan="2">Sewing Unit</th>
	                    <th width="70" rowspan="2">Sewing Line</th>
	                    <th width="100" rowspan="2">Color</th>
	                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
	                    <th width="80" rowspan="2" >Total</th>
	                </tr>
	                <tr>
	                <?
					$grand_tot_in=0;
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
	                    <?
					}
					?>
	                </tr>

	            </thead>
	            <tbody>
	            <?
				$i=1;
				$k=1;
				//var_dump($result);die;
				foreach($result as $row)
				{
					if(!in_array($row[csf("sewing_line")],$temp_arr[$row[csf("country_id")]]))
					{
						$temp_arr[$row[csf("country_id")]][]=$row[csf("sewing_line")];
						if($k!=1)
						{
							?>
							<tr bgcolor="#CCCCCC">
								<td >&nbsp;</td>
								<td>&nbsp;</td>
								<td >&nbsp;</td>
	                            <?
								if($prod_source==3)
								{
									?>
	                            	<td >&nbsp;</td>
	                                <?
								}
								?>
								<td >&nbsp;</td>
								<td >&nbsp;</td>
								<td >&nbsp;</td>
								<td >&nbsp;</td>
								<?
								foreach($sizearr_order as $size_id)
								{
									?>
									<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
									<?
								}
								?>
								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
							</tr>
							<?
							$line_color_size_in = $line_color_total_in ="";
						}
						$k++;
					}

					$sewing_line='';
					if($row[csf('prod_reso_allo')]==1)
					{
						$line_number=explode(",",$prod_reso_arr[$row[csf('sewing_line')]]);
						foreach($line_number as $val)
						{
							if($sewing_line=='') $sewing_line=$sewing_line_library[$val]; else $sewing_line.=",".$sewing_line_library[$val];
						}
					}
					else $sewing_line=$sewing_line_library[$row[csf('sewing_line')]];

					?>
	                <tr>
	                    <td align="center"><? echo $i;  ?></td>
	                    <td ><p><? echo $country_library[$row[csf("country_id")]];  ?></p></td>
	                    <td ><p><? echo $knitting_source[$row[csf("production_source")]]; ?><p></td>
	                    <?
						if($prod_source==3)
						{
							?>
	                    	<td ><p><? echo $supplier_arr[$row[csf("serving_company")]];  ?></p></td>
	                        <?
						}
						?>
	                    <td ><p><? echo $row[csf("challan_no")];  ?></p></td>
	                    <td ><p><? echo $floor_library[$row[csf("floor_id")]];  ?></p></td>
	                    <td align="center"><p><? echo $sewing_line;  ?></p></td>
	                    <td ><p><? echo $colorarr[$row[csf("color_number_id")]];  ?></p></td>
	                    <?
						$color_total_in=0;
	                    foreach($sizearr_order as $size_id)
	                    {
							$Production_qty=0;
	                        ?>
	                        <td align="right"><p>
							<?
								$Production_qty=$production_break_qnty[$row[csf('country_id')]][$row[csf('challan_no')]][$row[csf('floor_id')]][$row[csf('sewing_line')]][$row[csf('production_source')]][$row[csf('color_number_id')]][$size_id];
							 	echo number_format($Production_qty,0);
								 $color_total_in+=$Production_qty; $color_size_in [$size_id]+=$Production_qty; $line_color_total_in+=$Production_qty; $line_color_size_in [$size_id]+=$Production_qty;
							 ?>
	                        </p></td>
	                        <?
	                    }
	                    ?>
	                    <td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
	                </tr>
	                <?
					$i++;
				}
				?>
	            <tr bgcolor="#CCCCCC">
	                <td >&nbsp;</td>
	                <td>&nbsp;</td>
	                <td >&nbsp;</td>
	                <?
					if($prod_source==3)
					{
						?>
						<td >&nbsp;</td>
						<?
					}
					?>
	                <td >&nbsp;</td>
	                <td >&nbsp;</td>
	                <td >&nbsp;</td>
	                <td >&nbsp;</td>
	                <?
	                foreach($sizearr_order as $size_id)
	                {
	                    ?>
	                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
	                    <?
	                }
	                ?>
	                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
	            </tr>
	            </tbody>
	            <tfoot>
	                <th >&nbsp;</th>
	                <th>&nbsp;</th>
	                <th >&nbsp;</th>
	                <?
					if($prod_source==3)
					{
						?>
						<th >&nbsp;</th>
						<?
					}
					?>
	                <th >&nbsp;</th>
	                <th >&nbsp;</th>
	                <th >&nbsp;</th>
	                <th >&nbsp;</th>
	                <?
					foreach($sizearr_order as $size_id)
					{
						?>
	                	<th ><? echo $color_size_in[$size_id]; ?></th>
	                    <?
					}
					?>
	                <th ><? echo $grand_tot_in; ?></th>
	            </tfoot>
	        </table>
	        </div>
	    </fieldset>
	<?
	exit();
}

if ($action=='fin_rcv_popup')  // Finishing Rcv Popup
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,1,1);
       		$item_arr=return_library_array( "select id, item_name from lib_garment_item",'id','item_name');
				$company_short_arr=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name"  );
				$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
				$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
				$floor_arr=return_library_array( "select id, floor_name from lib_prod_floor",'id','floor_name');
				$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
				$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
        	   $order_cond= where_con_using_array(array_unique(explode(",",$po_break_down_id)),0,"c.id") ;

        	    $txt_date=" and a.receive_date='$production_date'";
        	    $item_id_cond=" and d.item_id='$gmts_item_id'";

        		$sql="SELECT d.id,
				       a.sys_no,
				       b.job_no,
				       b.style_ref_no,
				       c.po_number,
				       c.grouping,
				       a.company_id,
				       d.location_id,
				       d.floor_id,
				       d.qc_pass_qnty,
				       d.fin_receive_qnty,
				       d.po_break_down_id,
				       d.lc_company_id,
				       b.buyer_name,
				       a.fini_location_id,
				       a.floor_id AS fin_floor_id,
				       c.shipment_date,
				       c.po_quantity,
				       b.gmts_item_id,
				       a.receive_date,
				       d.color_id,
   					   d.size_id,
   					   a.sys_number_prefix_num,
   					   d.item_id
				  FROM gmt_finishing_receive_mst a,
				       wo_po_details_master b,
				       wo_po_break_down c,
				       gmt_finishing_receive_dtls d
				 WHERE     a.id = d.mst_id
				       AND d.po_break_down_id = c.id
				       AND b.job_no = c.job_no_mst
				       AND a.status_active = 1
				       AND d.status_active = 1
				       AND c.status_active = 1
				       AND d.status_active = 1
				       AND a.company_id = $company_id
				       $order_cond
				        $txt_date
				       $item_id_cond
					   and d.color_id = $color_id

				";
			//echo $sql;
			$result=sql_select($sql);
			$po_wise_data=array();
			$color_wise_data=array();
			$size_id_arr=array();
			$buyer='';
			$job_no='';
			$buyer='';
			$po_number='';
			$gmts_item_id='';
			$receive_date='';
			foreach ($result as $row)
			{
				$buyer.=$buyer_arr[$row[csf('buyer_name')]]."***";
				$gmts_item_id.=$item_arr[$row[csf('item_id')]]."***";
				$job_no.=$row[csf('buyer_name')]."***";
				$style_ref_no.=$row[csf('style_ref_no')]."***";
				$po_number.=$row[csf('po_number')]."***";
				$receive_date.=change_date_format($row[csf('receive_date')])."***";
				$po_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['buyer_name'].=$buyer_arr[$row[csf('buyer_name')]]."***";
				$po_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['job_no'].=$row[csf('job_no')]."***";
				$po_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['style_ref_no'].=$row[csf('style_ref_no')]."***";
				$po_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['po_number'].=$row[csf('po_number')]."***";
				$po_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['grouping'].=$row[csf('grouping')]."***";
				$po_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['gmts_item_id'].=$item_arr[$row[csf('item_id')]]."***";
				//$po_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['gmts_item_id'].=$item_arr[$row[csf('gmts_item_id')]]."***";
				$po_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['fin_floor_id'].=$floor_arr[$row[csf('fin_floor_id')]]."***";
				$po_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['sew_floor_id'].=$floor_arr[$row[csf('floor_id')]]."***";
				$po_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['fini_location_id'].=$location_arr[$row[csf('fini_location_id')]]."***";
				$po_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['fini_company'].=$company_short_arr[$row[csf('company_id')]]."***";
				$po_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['sew_comapny'].=$company_short_arr[$row[csf('lc_company_id')]]."***";
				$po_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['sew_location'].=$location_arr[$row[csf('location_id')]]."***";

				$po_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['po_quantity']+=$row[csf('po_quantity')];
				$po_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]]['rcv_no'].=$row[csf('sys_number_prefix_num')]."***";

				$po_size_wise_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]][$row[csf('size_id')]]+=$row[csf('fin_receive_qnty')];
				$color_wise_data[$row[csf('color_id')]][$row[csf('size_id')]]+=$row[csf('fin_receive_qnty')];
				array_push($size_id_arr, $row[csf('size_id')]);
			}
			$size_id_arr=array_unique($size_id_arr);

 	?>

    <fieldset>
		 <div id="data_panel" align="center" style="width:100%">
	        <script>
			      function new_window()
			      {
				       var w = window.open("Surprise", "#");
				       var d = w.document.open();
				       d.write(document.getElementById('details_reports').innerHTML);
				       d.close();
			      }
	        </script>
	 		<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
		</div>
    <div style="margin:2px" id="details_reports">
    	<h4>Buyer Name : <?=implode(", ", array_unique(explode("***", chop($buyer,"***"))));?>     Job No : <?=implode(", ", array_unique(explode("***", chop($job_no,"***"))));?>     Style No : <?=implode(", ", array_unique(explode("***", chop($style_ref_no,"***"))));?>     Garments Item : <?=implode(", ", array_unique(explode("***", chop($gmts_item_id,"***"))));?>  </h4>
    	<h4>Order No : <?=implode(", ", array_unique(explode("***", chop($po_number,"***"))));?>    Date : <?=implode(", ", array_unique(explode("***", chop($receive_date,"***"))));?></h4>
    	<br>
        <table width="30%" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" >
        	<caption style="justify-content: left;text-align: left;font-weight: bold;">Summary</caption>
            <thead>
                    <tr>
                        <th width="50" rowspan="2">Sl.</th>
                        <th width="100" rowspan="2">Color</th>
                        <th colspan="<?=count($size_id_arr);?>" width="<?=count($size_id_arr)*60;?>">Size</th>

                        <th  rowspan="2">Total</th>
               		</tr>
               		<tr>
               			<?
               				foreach ($size_id_arr as $size_id)
               				{
               					?>
               					<th width="60"><?=$size_arr[$size_id];?></th>
               					<?
               				}
               			?>
               		</tr>
            </thead>
            <tbody>
            	<?


				$i=1;
				$sum_total=0;
				$size_total_arr=array();
				foreach ($color_wise_data as $color_id => $color_data)
				{
					 if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

            		?>
	            	 <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                    	<td ><? echo $i;?></td>
                    	<td ><? echo $color_arr[$color_id];?></td>
                    	<?
                    		$color_total=0;
               				foreach ($size_id_arr as $size_id)
               				{
               					?>
               					<td><?=fn_number_format($color_data[$size_id],0,".",",");?></td>
               					<?
               					$sum_total+=$color_data[$size_id];
               					// $size_total_arr[$color_id]+=$color_data[$size_id];
               					$size_total_arr[$size_id]+=$color_data[$size_id];
               					$color_total+=$color_data[$size_id];
               				}
               			?>
                    	<td ><? echo fn_number_format($color_total,0,".",",");?></td>

	            	</tr>
	            	<?
	            	$i++;
	            }
	            ?>

            </tbody>
            <!-- <tfoot>
            	<tr>
            		<td></td>
            		<td></td>
        		     <?

						// foreach ($size_id_arr as $size_id)
						// {
						// 	?>
						// 	<td width="60"><   ?=   fn_number_format($size_total_arr[$size_id],0,".",",");?></td>
						// 	<?

						// }
					?>
					<td ><?// echo fn_number_format($sum_total,0,".",",");?></td>
            	</tr>
           </tfoot> -->
        </table>
        <br>
       <table width="95%" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" >
       		<caption style="justify-content: left;text-align: left;font-weight: bold;">Details</caption>
            <thead>
                    <tr>
                        <th width="50"  rowspan="2">Sl.</th>
                        <th width="70"  rowspan="2">Rcv ID</th>
                        <th width="110" rowspan="2">Finishing Company</th>
                        <th width="110" rowspan="2">Finishing Floor</th>
                        <th width="110" rowspan="2">Sewing Company</th>
                        <th width="110" rowspan="2">Sewing Floor</th>

                        <th width="110" rowspan="2">Color</th>
                       	<th colspan="<?=count($size_id_arr);?>" width="<?=count($size_id_arr)*60;?>">Size</th>
                         <th width="80" rowspan="2">Total</th>
               		</tr>
               		<tr>
               			<?
               				foreach ($size_id_arr as $size_id)
               				{
               					?>
               					<th width="60"><?=$size_arr[$size_id];?></th>
               					<?
               				}
               			?>
               		</tr>
            </thead>
            <tbody>
            	<?

				$i=1;
				$rcv_qnt=0;
				$sum_total=0;
				$size_total_arr=array();
				foreach ($po_wise_data as $po_id => $po_data)
				{
					foreach ($po_data as $color_id => $color_data)
					{
						# code...

						 if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

	            		?>
		            	 <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
	                    	<td ><? echo $i;?></td>

	                    	<td ><? echo  implode(",", array_unique(explode("***", chop($color_data['rcv_no'],"***"))));;?></td>
	                    	<td ><? echo  implode(",", array_unique(explode("***", chop($color_data['fini_company'],"***"))));;?></td>
	                    	<td ><? echo  implode(",", array_unique(explode("***", chop($color_data['fin_floor_id'],"***"))));;?></td>
	                    	<td ><? echo  implode(",", array_unique(explode("***", chop($color_data['sew_comapny'],"***"))));;?></td>
	                    	<td ><? echo  implode(",", array_unique(explode("***", chop($color_data['sew_floor_id'],"***"))));;?></td>
	                    	<td ><? echo $color_arr[$color_id];?></td>
                	     	<?
                	     		$color_total=0;
                					foreach ($size_id_arr as $size_id)
                					{
                						?>
                						<td><?=fn_number_format($po_size_wise_data[$po_id][$color_id][$size_id],0,".",",");?></td>
                						<?
                						$sum_total+=$po_size_wise_data[$po_id][$color_id][$size_id];
                						// $size_total_arr[$color_id]+=$po_size_wise_data[$po_id][$color_id][$size_id];
                						$size_total_arr[$size_id]+=$po_size_wise_data[$po_id][$color_id][$size_id];
                						$color_total+=$po_size_wise_data[$po_id][$color_id][$size_id];
                					}
                				?>
                			<td ><? echo fn_number_format($color_total,0,".",",");?></td>

		            	<?
		            	$i++;
		            	$rcv_qnt+=$row[csf('fin_receive_qnty')];
		            }
	            }
	            ?>
            </tbody>
            <!-- <tfoot>

        		<tr>

            		<td></td>
            		<td></td>
            		<td></td>
            		<td></td>
            		<td></td>
            		<td></td>
            		<td></td>
        		     <?

						// foreach ($size_id_arr as $size_id)
						// {
						// 	?>
						// 	<td width="60"><? //  fn_number_format($size_total_arr[$size_id],0,".",",");?></td>
						// 	<?

						// }
					?>
					<td ><? // echo // fn_number_format($sum_total,0,".",",");?></td>
            	</tr>

            </tfoot> -->
        </table>
       </div>
     </div>
     </fieldset>
    <?

 exit();

}

if ($action=="cutting_reject_popup")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,1,1);
	//echo $po_id;
	//echo $company_name;die;
	if($db_type==0)
	{
		$prod_date="and a.production_date ='".change_date_format($production_date,"yyyy-mm-dd", "-")."'";
	}
	else
	{
		$prod_date="and a.production_date = '".change_date_format($production_date,'','',1)."'";
	}
	$sql_variable=sql_select("select cutting_update, printing_emb_production, sewing_production, iron_update, finishing_update from variable_settings_production where company_name=$company_name and variable_list=28 and status_active=1 and is_deleted=0");
	$cutting_variable=$sql_variable[0][csf('cutting_update')];

	//echo $service_company;
	//$cutting_variable_setting=return_field_value("cutting_update","variable_settings_production","company_name=$company_name and variable_list=28 and status_active=1 and is_deleted=0","cutting_update");
	$po_array=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number");
	$color_Arr_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id in($po_id)","size_number_id","size_number_id");
	if($cutting_variable==1)
	{
		$sql_cutting=sql_select("SELECT a.po_break_down_id, sum(a.reject_qnty)  AS cutting_rej_qnty
		from pro_garments_production_mst  a
		where a.production_type =1 and a.po_break_down_id in ($po_id) and a.item_number_id='$item_id' and a.status_active=1
		and a.is_deleted=0 and a.serving_company=$service_company and $prod_date group by po_break_down_id");
	}
	else
	{
		$sql_cutting=sql_select("SELECT a.po_break_down_id,c.color_number_id, c.size_number_id,sum(b.reject_qty) AS cutting_rej_qnty
		from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
		where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.production_type=1 and a.po_break_down_id in ($po_id) and a.item_number_id='$item_id' and a.status_active=1 and a.is_deleted=0  and a.serving_company=$service_company $prod_date group by a.po_break_down_id,c.color_number_id, c.size_number_id");

		foreach($sql_cutting as $row)
		{
			if($row[csf('cutting_rej_qnty')]>0)
			{
				$cutting_data[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('cutting_rej_qnty')];
			}
		}
	}
	?>
	<div id="data_panel" align="center" style="width:100%">
		<script>
        function new_window()
        {
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write(document.getElementById('details_reports').innerHTML);
        d.close();
        }
        </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
    </div>
    <div style="color:#FF0000; font-size:14px; font-weight:bold; float:left; width:635px">This Pop-Up Will Be Perfect When Order, Production and All Procedure Follow Color or Color & Size Level.</div>
    <div style="width:635px" align="center" id="details_reports">
		<table id="tbl_id" class="rpt_table" width="" border="1" rules="all" >
			<thead>
				<tr>
					<th width="60">Buyer</th>
					<th width="90">Job Number</th>
					<th width="90">Style Name</th>
					<th width="150">Order Number</th>
					<th width="70">Ship Date</th>
					<th width="100">Item Name</th>
					<th >Order Qty.</th>
				</tr>
			</thead>
			<?
				$buyer_short_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name"  );
				if($db_type==0)
				{
					$sql = "select a.job_no_mst,group_concat(distinct(a.po_number)) as po_number,a.pub_shipment_date, sum(a.po_quantity) as po_quantity,a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio
						from wo_po_break_down a, wo_po_details_master b, wo_po_details_mas_set_details c
						where a.job_no_mst=b.job_no and b.job_no=c.job_no and a.id in ($po_id) and c.gmts_item_id='$item_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
				}
				else
				{
					$sql = "select a.job_no_mst, LISTAGG(a.po_number, ',') WITHIN GROUP (ORDER BY a.id) as po_number,max(a.pub_shipment_date) as pub_shipment_date, sum(a.po_quantity) as po_quantity,a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio
						from wo_po_break_down a, wo_po_details_master b, wo_po_details_mas_set_details c
						where a.job_no_mst=b.job_no and b.job_no=c.job_no and a.id in ($po_id) and c.gmts_item_id='$item_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.job_no_mst, a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio";
				}
				//echo $sql;
				$resultRow=sql_select($sql);

			?>
			<tr>
				<td><? echo $buyer_short_library[$resultRow[0][csf("buyer_name")]]; ?></td>
				<td><p><? echo $resultRow[0][csf("job_no_mst")]; ?></p></td>
				<td><p><? echo $resultRow[0][csf("style_ref_no")]; ?></p></td>
				<td><p><? echo implode(",",array_unique(explode(",",$resultRow[0][csf("po_number")]))); ?></p></td>
				<td><? echo change_date_format($resultRow[0][csf("pub_shipment_date")]); ?></td>
				<td><? echo $garments_item[$item_id]; ?></td>
				<td><? echo $resultRow[0][csf("po_quantity")]*$resultRow[0][csf("set_item_ratio")]; ?></td>
			</tr>
		</table>
		<br />
		<?
		//Cutting Data Display Here
		if($cutting_variable==1)
		{
			if(!empty($sql_cutting))
			{
				$tbl_width=200;
				?>
				<span style="font-size:18px; font-weight:bold;">Cutting Reject Quantity</span>
				<table width="<? echo $tbl_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
					<thead>
						<th width="50">SL</th>
						<th>Reject Quantity</th>
					</thead>
					<tbody>
						<?
						$i=1;
						foreach($sql_cutting as $row)
						{
							?>
							<tr>
								<td><? echo $i; ?></td>
								<td align="right"><? echo number_format($row[csf("cutting_rej_qnty")],2);?></td>
							</tr>
							<?
							$i++;
						}
						?>
					</tbody>
				</table>
				<br />
				<?

			}
		}
		else
		{
				$collspan=count($sizearr_order);
				$table_width=(230+($collspan*60));
				$colspan=2;

			if(!empty($sql_cutting))
			{
				?>
				<span style="font-size:18px; font-weight:bold;">Cutting Reject Quantity</span>
				<table width="<? echo $table_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
					<thead>
						<th width="50">SL</th>
						<th width="100">Color</th>
						<?
						foreach($sizearr_order as $size_id)
						{
							?>
							<th width="60"><? echo $sizearr[$size_id]; ?></th>
							<?
						}
						?>
						<th >Color Total</th>
					</thead>
					<tbody>
						<?
						$i=1;$grand_total_cutting=0;
						foreach($cutting_data as $order_id=>$value)
						{
							foreach($value as $color_id=>$val)
							{
								?>
								<tr>
									<td><? echo $i; ?></td>
									<td><? echo $color_Arr_library[$color_id]; ?></td>
									<?
									$color_total_cutting=0;
									foreach($sizearr_order as $size_id)
									{
										?>
										<td align="right">
										<?
											echo number_format($cutting_data[$order_id][$color_id][$size_id],0);
											$color_total_cutting+= $cutting_data[$order_id][$color_id][$size_id];
											$color_size_cutting [$size_id]+=$cutting_data[$order_id][$color_id][$size_id];
										?>
										</td>
										<?
									}
									?>
									<td align="right"><? echo number_format($color_total_cutting,0); $grand_total_cutting+=$color_total_cutting;?></td>
								</tr>
								<?
								$i++;
							}
						}
						?>
						<tr bgcolor="#CCCCCC" style="font-size:16px; font-weight:bold;">
							<td colspan="<? echo $colspan; ?>" align="right">Total:</td>
							<?
							foreach($sizearr_order as $size_id)
							{
								?>
								<td align="right"><? echo number_format($color_size_cutting [$size_id],0);?></td>
								<?
							}
							?>
							<td align="right"><? echo number_format($grand_total_cutting,0); ?></td>
						</tr>
					</tbody>
				</table>
				<br />
				<?

			}
		}
		?>
	</div>
	<?
}
if ($action=="emb_reject_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,1,1);
   //echo $po_id;
   //echo $company_name;die;
   if($db_type==0)
	   {
		   $prod_date="and a.production_date ='".change_date_format($production_date,"yyyy-mm-dd", "-")."'";
	   }
	   else
	   {
		   $prod_date="and a.production_date = '".change_date_format($production_date,'','',1)."'";
	   }

   $sql_variable=sql_select("select cutting_update, printing_emb_production, sewing_production, iron_update, finishing_update from variable_settings_production where company_name=$company_name and variable_list=28 and status_active=1 and is_deleted=0");
   $printing_variable=$sql_variable[0][csf('printing_emb_production')];
   //echo $service_company;
   //$cutting_variable_setting=return_field_value("cutting_update","variable_settings_production","company_name=$company_name and variable_list=28 and status_active=1 and is_deleted=0","cutting_update");
   $po_array=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number");
   $color_Arr_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
   $sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");

   $sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id in($po_id)","size_number_id","size_number_id");

   if($printing_variable==1)
   {
	   $sql_printing=sql_select("Select a.po_break_down_id, sum(a.reject_qnty)  AS printing_rej_qnty
				   from pro_garments_production_mst a
				   where a.production_type =3 and a.po_break_down_id in ($po_id) and a.item_number_id='$item_id' and a.status_active=1 and a.is_deleted=0 and a.serving_company=$service_company and  $prod_date  group by po_break_down_id");
   }
   else
   {
	   $sql_printing=sql_select("Select a.po_break_down_id,c.color_number_id, c.size_number_id,sum(b.reject_qty) AS printing_rej_qnty
				   from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
				   where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.production_type=3 and a.po_break_down_id in ($po_id) and a.item_number_id='$item_id' and a.status_active=1 and a.is_deleted=0 and a.serving_company=$service_company $prod_date  group by a.po_break_down_id,c.color_number_id, c.size_number_id");

	   foreach($sql_printing as $row)
	   {
		   if($row[csf('printing_rej_qnty')]>0)
		   {
			   $printing_data[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('printing_rej_qnty')];
		   }
	   }
   }
   ?>
   <div id="data_panel" align="center" style="width:100%">
	   <script>
	   function new_window()
	   {
	   var w = window.open("Surprise", "#");
	   var d = w.document.open();
	   d.write(document.getElementById('details_reports').innerHTML);
	   d.close();
	   }
	   </script>
	   <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
   </div>
   <div style="color:#FF0000; font-size:14px; font-weight:bold; float:left; width:635px">This Pop-Up Will Be Perfect When Order, Production and All Procedure Follow Color or Color & Size Level.</div>
   <div style="width:635px" align="center" id="details_reports">
   <table id="tbl_id" class="rpt_table" width="" border="1" rules="all" >
	   <thead>
		   <tr>
			   <th width="60">Buyer</th>
			   <th width="90">Job Number</th>
			   <th width="90">Style Name</th>
			   <th width="150">Order Number</th>
			   <th width="70">Ship Date</th>
			   <th width="100">Item Name</th>
			   <th >Order Qty.</th>
		   </tr>
	   </thead>
		  <?
		   $buyer_short_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name"  );
		   if($db_type==0)
		   {
				$sql = "select a.job_no_mst,group_concat(distinct(a.po_number)) as po_number,a.pub_shipment_date, sum(a.po_quantity) as po_quantity,a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio
				   from wo_po_break_down a, wo_po_details_master b, wo_po_details_mas_set_details c
				   where a.job_no_mst=b.job_no and b.job_no=c.job_no and a.id in ($po_id) and c.gmts_item_id='$item_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		   }
		   else
		   {
			   $sql = "select a.job_no_mst, LISTAGG(a.po_number, ',') WITHIN GROUP (ORDER BY a.id) as po_number,max(a.pub_shipment_date) as pub_shipment_date, sum(a.po_quantity) as po_quantity,a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio
				   from wo_po_break_down a, wo_po_details_master b, wo_po_details_mas_set_details c
				   where a.job_no_mst=b.job_no and b.job_no=c.job_no and a.id in ($po_id) and c.gmts_item_id='$item_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.job_no_mst, a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio";
		   }
		   //echo $sql;
		   $resultRow=sql_select($sql);

		?>
	   <tr>
		   <td><? echo $buyer_short_library[$resultRow[0][csf("buyer_name")]]; ?></td>
		   <td><p><? echo $resultRow[0][csf("job_no_mst")]; ?></p></td>
		   <td><p><? echo $resultRow[0][csf("style_ref_no")]; ?></p></td>
		   <td><p><? echo implode(",",array_unique(explode(",",$resultRow[0][csf("po_number")]))); ?></p></td>
		   <td><? echo change_date_format($resultRow[0][csf("pub_shipment_date")]); ?></td>
		   <td><? echo $garments_item[$item_id]; ?></td>
		   <td><? echo $resultRow[0][csf("po_quantity")]*$resultRow[0][csf("set_item_ratio")]; ?></td>
	   </tr>
   </table>
   <br />
   <?
   //emblish Data Display Here
   if($printing_variable==1)
   {
	   if(!empty($sql_printing))
	   {
		   $tbl_width=200;
		   ?>
		   <span style="font-size:18px; font-weight:bold;">Embellishment Reject Quantity</span>
		   <table width="<? echo $tbl_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
			   <thead>
				   <th width="50">SL</th>
				   <th>Reject Quantity</th>
			   </thead>
			   <tbody>
				   <?
				   $i=1;
				   foreach($sql_printing as $row)
				   {
					   ?>
					   <tr>
						   <td><? echo $i; ?></td>
						   <td align="right"><? echo number_format($row[csf("printing_rej_qnty")],2);?></td>
					   </tr>
					   <?
					   $i++;
				   }
				   ?>
			   </tbody>
		   </table>
		   <br />
		   <?
	   }
   }
   else
   {
	   $collspan=count($sizearr_order);
		   $table_width=(230+($collspan*60));
		   $colspan=2;
	   if(!empty($sql_printing))
	   {
		   ?>
		   <span style="font-size:18px; font-weight:bold;">Embellishment Reject Quantity</span>
		   <table width="<? echo $table_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
			   <thead>
				   <th width="50">SL</th>
				   <th width="100">Color</th>
				   <?
				   foreach($sizearr_order as $size_id)
				   {
					   ?>
					   <th width="60"><? echo $sizearr[$size_id]; ?></th>
					   <?
				   }
				   ?>
				   <th>Color Total</th>
			   </thead>
			   <tbody>
				   <?
				   $i=1; $grand_total_printing=0;
				   foreach($printing_data as $order_id=>$value)
				   {
					   foreach($value as $color_id=>$val)
					   {
						   ?>
						   <tr>
							   <td><? echo $i; ?></td>
							   <td><? echo $color_Arr_library[$color_id]; ?></td>
							   <?
							   $color_total_printing=0;
							   foreach($sizearr_order as $size_id)
							   {
								   ?>
								   <td align="right">
								   <?
									   echo number_format($printing_data[$order_id][$color_id][$size_id],0);
									   $color_total_printing+= $printing_data[$order_id][$color_id][$size_id];
									   $color_size_printing[$size_id]+=$printing_data[$order_id][$color_id][$size_id];
								   ?>
								   </td>
								   <?
							   }
							   ?>
							   <td align="right"><? echo number_format($color_total_printing,0); $grand_total_printing+=$color_total_printing;?></td>
						   </tr>
						   <?
						   $i++;
					   }
				   }
				   ?>
				   <tr bgcolor="#CCCCCC" style="font-size:16px; font-weight:bold;">
					   <td colspan="<? echo $colspan; ?>" align="right">Total:</td>
					   <?
					   foreach($sizearr_order as $size_id)
					   {
						   ?>
						   <td align="right"><? echo number_format($color_size_printing[$size_id],0);?></td>
						   <?
					   }
					   ?>
					   <td align="right"><? echo number_format($grand_total_printing,0); ?></td>
				   </tr>
			   </tbody>
		   </table>
		   <br />
		   <?

	   }
   }

   ?>
   </div>
   <?
   exit();

}
if ($action=="sewing_reject_popup") {
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,1,1);
	//echo $po_id;
	//echo $company_name;die;
	if($db_type==0)
		{
			$prod_date="and a.production_date ='".change_date_format($production_date,"yyyy-mm-dd", "-")."'";
		}
		else
		{
			$prod_date="and a.production_date = '".change_date_format($production_date,'','',1)."'";
		}

	$sql_variable=sql_select("select cutting_update, printing_emb_production, sewing_production, iron_update, finishing_update from variable_settings_production where company_name=$company_name and variable_list=28 and status_active=1 and is_deleted=0");

	$sewing_variable=$sql_variable[0][csf('sewing_production')];

	//echo $service_company;
	//$cutting_variable_setting=return_field_value("cutting_update","variable_settings_production","company_name=$company_name and variable_list=28 and status_active=1 and is_deleted=0","cutting_update");
	$po_array=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number");
	$color_Arr_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");

	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id in($po_id)","size_number_id","size_number_id");

	if($sewing_variable==1)
	{
		$sql_sewing=sql_select("Select a.po_break_down_id, sum(a.reject_qnty)  AS sewingout_rej_qnty
					from pro_garments_production_mst a
					where a.production_type =5 and a.po_break_down_id in ($po_id) and a.item_number_id='$item_id' and a.status_active=1 and a.is_deleted=0 and a.serving_company=$service_company $prod_date group by po_break_down_id");
	}
	else
	{
		$sql_sewing=sql_select("Select a.po_break_down_id,c.color_number_id, c.size_number_id,sum(b.reject_qty) AS sewingout_rej_qnty
					from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
					where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.production_type=5 and a.po_break_down_id in ($po_id) and a.item_number_id='$item_id' and a.status_active=1 and a.is_deleted=0 and a.serving_company=$service_company $prod_date group by a.po_break_down_id,c.color_number_id, c.size_number_id");

		foreach($sql_sewing as $row)
		{
			if($row[csf('sewingout_rej_qnty')]>0)
			{
				$sewing_data[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('sewingout_rej_qnty')];
			}
		}
	}
	?>
    <div id="data_panel" align="center" style="width:100%">
		<script>
        function new_window()
        {
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write(document.getElementById('details_reports').innerHTML);
        d.close();
        }
        </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
    </div>
    <div style="color:#FF0000; font-size:14px; font-weight:bold; float:left; width:635px">This Pop-Up Will Be Perfect When Order, Production and All Procedure Follow Color or Color & Size Level.</div>
    <div style="width:635px" align="center" id="details_reports">
    <table id="tbl_id" class="rpt_table" width="" border="1" rules="all" >
    	<thead>
        	<tr>
            	<th width="60">Buyer</th>
                <th width="90">Job Number</th>
                <th width="90">Style Name</th>
                <th width="150">Order Number</th>
                <th width="70">Ship Date</th>
                <th width="100">Item Name</th>
                <th >Order Qty.</th>
            </tr>
        </thead>
       	<?
        	$buyer_short_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name"  );
			if($db_type==0)
			{
 				$sql = "select a.job_no_mst,group_concat(distinct(a.po_number)) as po_number,a.pub_shipment_date, sum(a.po_quantity) as po_quantity,a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio
					from wo_po_break_down a, wo_po_details_master b, wo_po_details_mas_set_details c
					where a.job_no_mst=b.job_no and b.job_no=c.job_no and a.id in ($po_id) and c.gmts_item_id='$item_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			}
			else
			{
				$sql = "select a.job_no_mst, LISTAGG(a.po_number, ',') WITHIN GROUP (ORDER BY a.id) as po_number,max(a.pub_shipment_date) as pub_shipment_date, sum(a.po_quantity) as po_quantity,a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio
					from wo_po_break_down a, wo_po_details_master b, wo_po_details_mas_set_details c
					where a.job_no_mst=b.job_no and b.job_no=c.job_no and a.id in ($po_id) and c.gmts_item_id='$item_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.job_no_mst, a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio";
			}
			//echo $sql;
			$resultRow=sql_select($sql);

 		?>
        <tr>
        	<td><? echo $buyer_short_library[$resultRow[0][csf("buyer_name")]]; ?></td>
            <td><p><? echo $resultRow[0][csf("job_no_mst")]; ?></p></td>
            <td><p><? echo $resultRow[0][csf("style_ref_no")]; ?></p></td>
            <td><p><? echo implode(",",array_unique(explode(",",$resultRow[0][csf("po_number")]))); ?></p></td>
            <td><? echo change_date_format($resultRow[0][csf("pub_shipment_date")]); ?></td>
            <td><? echo $garments_item[$item_id]; ?></td>
            <td><? echo $resultRow[0][csf("po_quantity")]*$resultRow[0][csf("set_item_ratio")]; ?></td>
        </tr>
    </table>
    <br />
    <?

	//Sewing Data Display Here
	if($sewing_variable==1)
	{
		if(!empty($sql_sewing))
		{
			 $tbl_width=250;
			?>
            <span style="font-size:18px; font-weight:bold;">Sewing Reject Quantity</span>
            <table width="<? echo $tbl_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
            	<thead>
                	<th width="50">SL</th>
                	<th>Reject Quantity</th>
                </thead>
                <tbody>
                	<?
					$i=1;
					foreach($sql_sewing as $row)
					{
						?>
                        <tr>
                            <td><? echo $i; ?></td>

                            <td align="right"><? echo number_format($row[csf("sewingout_rej_qnty")],2);?></td>
                        </tr>
                        <?
						$i++;
					}
					?>
                </tbody>
            </table>
            <br />
            <?
		}
	}
	else
	{
		$collspan=count($sizearr_order);
		$table_width=(230+($collspan*60));
		$colspan=2;
		if(!empty($sql_sewing))
		{
			?>
            <span style="font-size:18px; font-weight:bold;">Sewing Reject Quantity</span>
            <table width="<? echo $table_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
            	<thead>
                	<th width="50">SL</th>
                    <th width="100">Color</th>
                    <?
					foreach($sizearr_order as $size_id)
					{
						?>
                        <th width="60"><? echo $sizearr[$size_id]; ?></th>
                        <?
					}
					?>
                    <th >Color Total</th>
                </thead>
                <tbody>
                	<?
					$i=1;$grand_total_sewing=0;
					foreach($sewing_data as $order_id=>$value)
					{
						foreach($value as $color_id=>$val)
						{
							?>
							<tr>
								<td><? echo $i; ?></td>
                                <td><? echo $color_Arr_library[$color_id]; ?></td>
                                <?
								$color_total_sewing=0;
								foreach($sizearr_order as $size_id)
								{
									?>
                                    <td align="right">
									<?
										echo number_format($sewing_data[$order_id][$color_id][$size_id],0);
										$color_total_sewing+= $sewing_data[$order_id][$color_id][$size_id];
										$color_size_sewing[$size_id]+=$sewing_data[$order_id][$color_id][$size_id];
									?>
                                    </td>
                                    <?
								}
								?>
                                <td align="right"><? echo number_format($color_total_sewing,0); $grand_total_sewing+=$color_total_sewing;?></td>
							</tr>
							<?
							$i++;
						}
					}
					?>
                    <tr bgcolor="#CCCCCC" style="font-size:16px; font-weight:bold;">
                        <td colspan="<? echo $colspan; ?>" align="right">Total:</td>
                        <?
                        foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><? echo number_format($color_size_sewing[$size_id],0);?></td>
							<?
						}
						?>
                        <td align="right"><? echo number_format($grand_total_sewing,0); ?></td>
                    </tr>
                </tbody>
            </table>
            <br />
            <?

		}
	}
	?>
    </div>
    <?
	exit();

}
if ($action=="iron_reject_popup") {

	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,1,1);
	//echo $po_id;
	//echo $company_name;die;
	if($db_type==0)
		{
			$prod_date="and a.production_date ='".change_date_format($production_date,"yyyy-mm-dd", "-")."'";
		}
		else
		{
			$prod_date="and a.production_date = '".change_date_format($production_date,'','',1)."'";
		}
	$sql_variable=sql_select("select cutting_update, printing_emb_production, sewing_production, iron_update, finishing_update from variable_settings_production where company_name=$company_name and variable_list=28 and status_active=1 and is_deleted=0");
	$iron_variable=$sql_variable[0][csf('iron_update')];
	//echo $service_company;
	//$cutting_variable_setting=return_field_value("cutting_update","variable_settings_production","company_name=$company_name and variable_list=28 and status_active=1 and is_deleted=0","cutting_update");
	$po_array=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number");
	$color_Arr_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");

	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id in($po_id)","size_number_id","size_number_id");

	if($iron_variable==1)
	{
		$sql_iron=sql_select("Select a.po_break_down_id, sum(a.reject_qnty)  AS iron_rej_qnty
					from pro_garments_production_mst a
					where a.production_type =7 and a.po_break_down_id in ($po_id) and a.item_number_id='$item_id' and a.status_active=1 and a.is_deleted=0 and a.serving_company=$service_company $prod_date  group by po_break_down_id");
	}
	else
	{
		$sql_iron=sql_select("Select a.po_break_down_id,c.color_number_id, c.size_number_id,sum(b.reject_qty) AS iron_rej_qnty
					from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
					where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.production_type=7 and a.po_break_down_id in ($po_id) and a.item_number_id='$item_id' and a.status_active=1 and a.is_deleted=0 and a.serving_company=$service_company $prod_date  group by a.po_break_down_id,c.color_number_id, c.size_number_id");

		foreach($sql_iron as $row)
		{
			if($row[csf('iron_rej_qnty')]>0)
			{
				$iron_data[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('iron_rej_qnty')];
			}
		}
	}

	?>
    <div id="data_panel" align="center" style="width:100%">
		<script>
        function new_window()
        {
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write(document.getElementById('details_reports').innerHTML);
        d.close();
        }
        </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
    </div>
    <div style="color:#FF0000; font-size:14px; font-weight:bold; float:left; width:635px">This Pop-Up Will Be Perfect When Order, Production and All Procedure Follow Color or Color & Size Level.</div>
    <div style="width:635px" align="center" id="details_reports">
    <table id="tbl_id" class="rpt_table" width="" border="1" rules="all" >
    	<thead>
        	<tr>
            	<th width="60">Buyer</th>
                <th width="90">Job Number</th>
                <th width="90">Style Name</th>
                <th width="150">Order Number</th>
                <th width="70">Ship Date</th>
                <th width="100">Item Name</th>
                <th >Order Qty.</th>
            </tr>
        </thead>
       	<?
        	$buyer_short_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name"  );
			if($db_type==0)
			{
 				$sql = "select a.job_no_mst,group_concat(distinct(a.po_number)) as po_number,a.pub_shipment_date, sum(a.po_quantity) as po_quantity,a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio
					from wo_po_break_down a, wo_po_details_master b, wo_po_details_mas_set_details c
					where a.job_no_mst=b.job_no and b.job_no=c.job_no and a.id in ($po_id) and c.gmts_item_id='$item_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			}
			else
			{
				$sql = "select a.job_no_mst, LISTAGG(a.po_number, ',') WITHIN GROUP (ORDER BY a.id) as po_number,max(a.pub_shipment_date) as pub_shipment_date, sum(a.po_quantity) as po_quantity,a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio
					from wo_po_break_down a, wo_po_details_master b, wo_po_details_mas_set_details c
					where a.job_no_mst=b.job_no and b.job_no=c.job_no and a.id in ($po_id) and c.gmts_item_id='$item_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.job_no_mst, a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio";
			}
			//echo $sql;
			$resultRow=sql_select($sql);

 		?>
        <tr>
        	<td><? echo $buyer_short_library[$resultRow[0][csf("buyer_name")]]; ?></td>
            <td><p><? echo $resultRow[0][csf("job_no_mst")]; ?></p></td>
            <td><p><? echo $resultRow[0][csf("style_ref_no")]; ?></p></td>
            <td><p><? echo implode(",",array_unique(explode(",",$resultRow[0][csf("po_number")]))); ?></p></td>
            <td><? echo change_date_format($resultRow[0][csf("pub_shipment_date")]); ?></td>
            <td><? echo $garments_item[$item_id]; ?></td>
            <td><? echo $resultRow[0][csf("po_quantity")]*$resultRow[0][csf("set_item_ratio")]; ?></td>
        </tr>
    </table>
    <br />
    <?
	//Iron Data Display Here
	if($iron_variable==1)
	{
		if(!empty($sql_iron))
		{
			 $tbl_width=200;
			?>
            <span style="font-size:18px; font-weight:bold;">Iron  Reject Quantity</span>
            <table width="<? echo $tbl_width;?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
            	<thead>
                	<th width="50">SL</th>
                	<th>Reject Quantity</th>
                </thead>
                <tbody>
                	<?
					$i=1;
					foreach($sql_iron as $row)
					{
						?>
                        <tr>
                            <td><? echo $i; ?></td>
                            <td align="right"><? echo number_format($row[csf("iron_rej_qnty")],2);?></td>
                        </tr>
                        <?
						$i++;
					}
					?>
                </tbody>
            </table>
            <br />
            <?

		}
	}
	else
	{
		$collspan=count($sizearr_order);
		$table_width=(230+($collspan*60));
			$colspan=2;
		if(!empty($sql_iron))
		{
			?>
            <span style="font-size:18px; font-weight:bold;">Iron Reject Quantity</span>
            <table width="<? echo $table_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
            	<thead>
                	<th width="50">SL</th>
                    <th width="100">Color</th>
                    <?
					foreach($sizearr_order as $size_id)
					{
						?>
                        <th width="60"><? echo $sizearr[$size_id]; ?></th>
                        <?
					}
					?>
                    <th >Color Total</th>
                </thead>
                <tbody>
                	<?
					$i=1;$grand_total_iron=0;
					foreach($iron_data as $order_id=>$value)
					{
						foreach($value as $color_id=>$val)
						{
							?>
							<tr>
								<td><? echo $i; ?></td>

                                <td><? echo $color_Arr_library[$color_id]; ?></td>
                                <?
								$color_total_iron=0;
								foreach($sizearr_order as $size_id)
								{
									?>
                                    <td align="right">
									<?
										echo number_format($iron_data[$order_id][$color_id][$size_id],0);
										$color_total_iron+= $iron_data[$order_id][$color_id][$size_id];
										$color_size_iron[$size_id]+=$iron_data[$order_id][$color_id][$size_id];
									?>
                                    </td>
                                    <?
								}
								?>
                                <td align="right"><? echo number_format($color_total_iron,0); $grand_total_iron+=$color_total_iron;?></td>
							</tr>
							<?
							$i++;
						}
					}
					?>
                    <tr bgcolor="#CCCCCC" style="font-size:16px; font-weight:bold;">
                        <td colspan="<? echo $colspan; ?>" align="right">Total:</td>
                        <?
                        foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><? echo number_format($color_size_iron[$size_id],0);?></td>
							<?
						}
						?>
                        <td align="right"><? echo number_format($grand_total_iron,0); ?></td>
                    </tr>
                </tbody>
            </table>
            <br />
            <?

		}
	}

	?>
    </div>
    <?
	exit();
}

if ($action=="finishing_reject_popup") {
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,1,1);
	//echo $po_id;
	//echo $company_name;die;
	if($db_type==0)
		{
			$prod_date="and a.production_date ='".change_date_format($production_date,"yyyy-mm-dd", "-")."'";
		}
		else
		{
			$prod_date="and a.production_date = '".change_date_format($production_date,'','',1)."'";
		}

	$sql_variable=sql_select("select cutting_update, printing_emb_production, sewing_production, iron_update, finishing_update from variable_settings_production where company_name=$company_name and variable_list=28 and status_active=1 and is_deleted=0");

	$finishing_variable=$sql_variable[0][csf('finishing_update')];
	//echo $service_company;
	//$cutting_variable_setting=return_field_value("cutting_update","variable_settings_production","company_name=$company_name and variable_list=28 and status_active=1 and is_deleted=0","cutting_update");
	$po_array=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number");
	$color_Arr_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");

	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id in($po_id)","size_number_id","size_number_id");

	if($finishing_variable==1)
	{
		$sql_finishing=sql_select("Select a.po_break_down_id, sum(a.reject_qnty)  AS finish_rej_qnty
					from pro_garments_production_mst a
					where a.production_type =8 and a.po_break_down_id in ($po_id) and a.item_number_id='$item_id' and a.status_active=1 and a.is_deleted=0 and a.serving_company=$service_company $prod_date  group by po_break_down_id");
	}
	else
	{
		$sql_finishing=sql_select("Select a.po_break_down_id,c.color_number_id, c.size_number_id,sum(b.reject_qty) AS finish_rej_qnty
					from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
					where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.production_type=8 and a.po_break_down_id in ($po_id) and a.item_number_id='$item_id' and a.status_active=1 and a.is_deleted=0 and a.serving_company=$service_company $prod_date  group by a.po_break_down_id,c.color_number_id, c.size_number_id");

		foreach($sql_finishing as $row)
		{
			if($row[csf('finish_rej_qnty')]>0)
			{
				$finishing_data[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]] +=$row[csf('finish_rej_qnty')];
			}
		}
	}


	?>
    <div id="data_panel" align="center" style="width:100%">
		<script>
        function new_window()
        {
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write(document.getElementById('details_reports').innerHTML);
        d.close();
        }
        </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
    </div>
    <div style="color:#FF0000; font-size:14px; font-weight:bold; float:left; width:635px">This Pop-Up Will Be Perfect When Order, Production and All Procedure Follow Color or Color & Size Level.</div>
    <div style="width:635px" align="center" id="details_reports">
    <table id="tbl_id" class="rpt_table" width="" border="1" rules="all" >
    	<thead>
        	<tr>
            	<th width="60">Buyer</th>
                <th width="90">Job Number</th>
                <th width="90">Style Name</th>
                <th width="150">Order Number</th>
                <th width="70">Ship Date</th>
                <th width="100">Item Name</th>
                <th >Order Qty.</th>
            </tr>
        </thead>
       	<?
        	$buyer_short_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name"  );
			if($db_type==0)
			{
 				$sql = "select a.job_no_mst,group_concat(distinct(a.po_number)) as po_number,a.pub_shipment_date, sum(a.po_quantity) as po_quantity,a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio
					from wo_po_break_down a, wo_po_details_master b, wo_po_details_mas_set_details c
					where a.job_no_mst=b.job_no and b.job_no=c.job_no and a.id in ($po_id) and c.gmts_item_id='$item_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			}
			else
			{
				$sql = "select a.job_no_mst, LISTAGG(a.po_number, ',') WITHIN GROUP (ORDER BY a.id) as po_number,max(a.pub_shipment_date) as pub_shipment_date, sum(a.po_quantity) as po_quantity,a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio
					from wo_po_break_down a, wo_po_details_master b, wo_po_details_mas_set_details c
					where a.job_no_mst=b.job_no and b.job_no=c.job_no and a.id in ($po_id) and c.gmts_item_id='$item_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.job_no_mst, a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio";
			}
			//echo $sql;
			$resultRow=sql_select($sql);

 		?>
        <tr>
        	<td><? echo $buyer_short_library[$resultRow[0][csf("buyer_name")]]; ?></td>
            <td><p><? echo $resultRow[0][csf("job_no_mst")]; ?></p></td>
            <td><p><? echo $resultRow[0][csf("style_ref_no")]; ?></p></td>
            <td><p><? echo implode(",",array_unique(explode(",",$resultRow[0][csf("po_number")]))); ?></p></td>
            <td><? echo change_date_format($resultRow[0][csf("pub_shipment_date")]); ?></td>
            <td><? echo $garments_item[$item_id]; ?></td>
            <td><? echo $resultRow[0][csf("po_quantity")]*$resultRow[0][csf("set_item_ratio")]; ?></td>
        </tr>
    </table>
    <br />
    <?
	//Finish Data Display Here
	if($finishing_variable==1)
	{
		if(!empty($sql_finishing))
		{
			 $tbl_width=250;
			?>
            <span style="font-size:18px; font-weight:bold;">Finishing  Reject Quantity</span>
            <table width="<? echo $tbl_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
            	<thead>
                	<th width="50">SL</th>
                	<th>Reject Quantity</th>
                </thead>
                <tbody>
                	<?
					$i=1;
					foreach($sql_finishing as $row)
					{
						?>
                        <tr>
                            <td><? echo $i; ?></td>
                            <td align="right"><? echo number_format($row[csf("finish_rej_qnty")],2);?></td>
                        </tr>
                        <?
						$i++;
					}
					?>
                </tbody>
            </table>
            <br />
            <?
		}
	}
	else
	{
		$collspan=count($sizearr_order);
		$table_width=(230+($collspan*60));
		$colspan=2;
		if(!empty($sql_finishing))
		{
			?>
            <span style="font-size:18px; font-weight:bold;">Finishing Reject Quantity</span>
            <table width="<? echo $table_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
            	<thead>
                	<th width="50">SL</th>
                    <th width="100">Color</th>
                    <?
					foreach($sizearr_order as $size_id)
					{
						?>
                        <th width="60"><? echo $sizearr[$size_id]; ?></th>
                        <?
					}
					?>
                    <th>Color Total</th>
                </thead>
                <tbody>
                	<?
					$i=1;$grand_total_finish=0;
					foreach($finishing_data as $order_id=>$value)
					{
						foreach($value as $color_id=>$val)
						{
							?>
							<tr>
								<td><? echo $i; ?></td>
                                <td><? echo $color_Arr_library[$color_id]; ?></td>
                                <?
								$color_total_finish=0;
								foreach($sizearr_order as $size_id)
								{
									?>
                                    <td align="right">
									<?
										echo number_format($finishing_data[$order_id][$color_id][$size_id],0);
										$color_total_finish+= $finishing_data[$order_id][$color_id][$size_id];
										$color_size_finish[$size_id]+=$finishing_data[$order_id][$color_id][$size_id];
									?>
                                    </td>
                                    <?
								}
								?>
                                <td align="right"><? echo number_format($color_total_finish,0); $grand_total_finish+=$color_total_finish;?></td>
							</tr>
							<?
							$i++;
						}
					}
					?>
                    <tr bgcolor="#CCCCCC" style="font-size:16px; font-weight:bold;">
                        <td colspan="<? echo $colspan; ?>" align="right">Total:</td>
                        <?
                        foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><? echo number_format($color_size_finish[$size_id],0);?></td>
							<?
						}
						?>
                        <td align="right"><? echo number_format($grand_total_finish,0); ?></td>
                    </tr>
                </tbody>
            </table>
            <?
		}
	}
	?>
    </div>
    <?
	exit();
}

?>
