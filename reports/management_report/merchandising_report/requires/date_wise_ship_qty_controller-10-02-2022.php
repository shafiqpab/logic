<?

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
require_once('../../../../includes/class3/class.conditions.php');
require_once('../../../../includes/class3/class.reports.php');
require_once('../../../../includes/class3/class.fabrics.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];
$shipment_status_arr=array(1=>"Running Full Order Qty",2=>"Running Order Balance Qty",3=>"Fully Shipped",4=>"Cancelled Order");
//--------------------------------------------------------------------------------------------------------------------
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_library=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
$color_library=return_library_array( "select id,color_name from lib_color",'id','color_name');
$location_lib=return_library_array( "select id,location_name from lib_location" ,'id','location_name');
$season_arr=return_library_array( "select id,season_name from  lib_buyer_season",'id','season_name');
$dealing_marchant_arr=return_library_array( "select id,team_member_name from  lib_mkt_team_member_info",'id','team_member_name');

//$ord_qty_arr=return_library_array( "select id,po_quantity from wo_po_break_down",'id','po_quantity');
$report_type_arr=array(1=>"Buyer Wise Summary",2=>"Order Wise Detail",3=>"Order Wise Summary"); 
 

if ($action=="load_drop_down_buyer")
{
	if($data!=0)
	{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0); 
	}
	else
	{
		echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0  $buyer_cond  group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",0,"" );
	}
	exit();
}
if ($action=="cbo_dealing_merchant")
{
	echo create_drop_down( "cbo_dealing_merchant", 100, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
	exit();	
}

if($action=="search_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Season No Info", "../../../../", 1, 1,'','','');
	?>
	<script>
		$(document).ready(function(e) {
            setFilterGrid('tbl_list_search',-1);
        });
		var selected_name = new Array; var selected_id = new Array;
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
			}
		}
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( str ) {
			if (str!="") str=str.split("_");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			if( jQuery.inArray( str[2], selected_id ) == -1 ) {
				selected_id.push( str[2] );
				selected_name.push( str[1] );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[2] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hide_season_id').val( id );
			$('#hide_season').val( name );
		}

		/*function js_set_value( str ) {
			if (str!="") str=str.split("_");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			if( jQuery.inArray( str[1], selected_name ) == -1 ) {
				selected_name.push( str[1] );
			}
			else {
				for( var i = 0; i < selected_name.length; i++ ) {
					if( selected_name[i] == str[1] ) break;
				}
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_name.length; i++ ) {
				name += selected_name[i] + ',';
			}
			
			name = name.substr( 0, name.length - 1 );
			
			$('#hide_season').val( name );
		}*/
    </script>
    </head>
    <body>
        <div align="center">
            <form name="styleRef_form" id="styleRef_form">
                <fieldset style="width:350px;">
                    <input type="hidden" name="hide_season" id="hide_season" value="" />
                    <input type="hidden" name="hide_season_id" id="hide_season_id" value="" />
                    <?
                        if($buyerID==0)
                        {
                            if ($_SESSION['logic_erp']["data_level_secured"]==1)
                            {
                                if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
                            }
                            else $buyer_id_cond="";
                        }
                        else $buyer_id_conds=" and a.id=$buyerID";
                        
                        
                        if($job_no!=0) $jobno=" and job_no_prefix_num in (".$job_no.")"; else $jobno="";
                        if($db_type==0)
                        {
                            $sql="select  b.season_name as season , a.tag_company, b.id  from lib_buyer a ,lib_buyer_season b where a.id=b.buyer_id and find_in_set(a.tag_company,$companyID)>0 and   a.status_active=1
 and b.status_active=1 $buyer_id_conds  group by  b.season_name , a.tag_company, b.id";
                        }
                        if($db_type==2)
                        {
                            
                            $sql="select  b.season_name as season , a.tag_company, b.id  from lib_buyer a ,lib_buyer_season b where a.id=b.buyer_id and find_in_set(a.tag_company,$companyID)>0 and   a.status_active=1
 and b.status_active=1 $buyer_id_conds  group by  b.season_name , a.tag_company, b.id ";
                        }
                        	
                        echo create_list_view("tbl_list_search", "Season", "200","300","280",0, $sql , "js_set_value", "season,id", "", 1, "0", $arr , "season", "","",'0','',1) ;
                        ?>
                </fieldset>
            </form>
        </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}

//report_generate_order
if($action=="report_generate_order")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	//echo $cbo_year_from;die;
	$company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_date_category=str_replace("'","",$cbo_date_category);
	$season=str_replace("'","",$txt_season);
	$cbo_report_type=str_replace("'","",$cbo_report_type);
	$order_status_id=str_replace("'","",$cbo_order_status);
	$shipment_status_id=str_replace("'","",$cbo_shipment_status);
	$all_date_button=$all_date;
	$dealing_merchant_id=str_replace("'","",$cbo_dealing_merchant);
	//dealing_marchant
	if($dealing_merchant_id>0) 
	{
		 $marchd_cond="and a.dealing_marchant in($dealing_merchant_id)";
	}
	else $marchd_cond="";
	//echo $cbo_dealing_merchant.', ';die;
	//shiping_status
	//echo $shipment_status_id;die;
	$order_status_cond='';
	if($order_status_id==0) $order_status_cond=" and c.is_confirmed in(1,2)";
	else if($order_status_id!=0) $order_status_cond=" and c.is_confirmed=$order_status_id";	
	
	$shipment_status_cond='';
	if($shipment_status_id==1) // Running Full Order Qty
	{
		$shipment_status_cond=" and a.status_active=1 and b.status_active=1 and c.status_active=1  ";
	}
	else if($shipment_status_id==2) //Running Order Balance Qty
	{
		$shipment_status_cond=" and a.status_active=1 and b.status_active=1 and c.status_active=1 and c.shiping_status <> 3 ";	
	}
	else if($shipment_status_id==3) //Fully Shipped
	{
		$shipment_status_cond="and c.shiping_status=$shipment_status_id  and a.status_active=1 and b.status_active=1 and c.status_active=1 ";	
	}
	else if($shipment_status_id==4) //Cancelled Order
	{
		$shipment_status_cond=" and a.status_active=1  and c.status_active=3 and c.shiping_status <> 3 ";	
	}
	
	if($season=="") $season_cond=""; else $season_cond=" and a.season_buyer_wise in('".implode("','",explode(",",$season))."')";

	//echo $cbo_buyer_name;die;
	if($cbo_buyer_name!=0) $buyer_cond=" and a.buyer_name='$cbo_buyer_name'"; else $buyer_cond="";
	if($company_name!=0) $company_con=" and a.company_name='$company_name'"; else $company_con="";
	if($company_name!=0) $company_con2=" and a.template_name='$company_name'"; else $company_con2="";
	$date_cond='';
	if(str_replace("'","",$cbo_year_from)!=0 && str_replace("'","",$cbo_month_from)!=0)
	{
		$start_year=str_replace("'","",$cbo_year_from);
		$start_month=str_replace("'","",$cbo_month_from);
		$start_date=$start_year."-".$start_month."-01";
		
		$end_year=str_replace("'","",$cbo_year_to);
		$end_month=str_replace("'","",$cbo_month_to);
		$num_days = cal_days_in_month(CAL_GREGORIAN, $end_month, $end_year);
		$end_date=$end_year."-".$end_month."-$num_days";
		if($cbo_date_category==1)
		{
			if($db_type==0) 
			{
				$date_cond=" and b.country_ship_date between '$start_date' and '$end_date'";
				$date_cond2=" and d.country_ship_date between '$start_date' and '$end_date'";
				$order_by_cond="DATE_FORMAT(b.country_ship_date, '%Y%m')";
			}
			if($db_type==2) 
			{
				$date_cond=" and b.country_ship_date between '".date("j-M-Y",strtotime(str_replace("'","",$start_date)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$end_date)))."'";
				$date_cond2=" and d.country_ship_date between '".date("j-M-Y",strtotime(str_replace("'","",$start_date)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$end_date)))."'";
				$order_by_cond="to_char(b.country_ship_date,'YYYY-MM')";
			}
			//$date_type="b.country_ship_date";
		}
		else if($cbo_date_category==2) //Cut-Off Date
		{
			if($db_type==0) 
			{
				$date_cond=" and b.cutup_date between '$start_date' and '$end_date'";
				$date_cond2=" and d.cutup_date between '$start_date' and '$end_date'";
				$order_by_cond="DATE_FORMAT(b.cutup_date, '%Y%m')";
			}
			if($db_type==2) 
			{
				$date_cond=" and b.cutup_date between '".date("j-M-Y",strtotime(str_replace("'","",$start_date)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$end_date)))."'";
				$date_cond2=" and d.cutup_date between '".date("j-M-Y",strtotime(str_replace("'","",$start_date)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$end_date)))."'";
				$order_by_cond="to_char(b.cutup_date,'YYYY-MM')";
			}
			//$date_type="b.cutup_date";
		}
	}
	
	
	$sql_color_no_arr=return_library_array("select po_break_down_id, count(distinct color_number_id) as color_id from wo_po_color_size_breakdown group by po_break_down_id","po_break_down_id","color_id");
	
	if($db_type==2) $fab_full_name="a.fabric_description || ',' || a.gsm_weight";
	else if ($db_type==0) $fab_full_name="a.fabric_description,',',a.gsm_weight";
	$body_part_arr=return_library_array("select c.id, $fab_full_name as fabrication from wo_pre_cost_fabric_cost_dtls a ,wo_po_details_master b,  wo_po_break_down c where a.job_no=b.job_no and b.job_no=c.job_no_mst and a.body_part_id in(1,20)","id","fabrication");
	//var_dump($body_part_arr);die;
	$weak_of_year=return_library_array( "select week_date,week from  week_of_year",'week_date','week');
	
	$costing_per_arr=return_library_array( "select job_no, costing_per from wo_pre_cost_mst",'job_no','costing_per');
	$supplier_name_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	
	if($db_type==2) $color_type_group="LISTAGG(CAST(color_type_id AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY color_type_id) as color_type_id";
	else if($db_type==0) $color_type_group="group_concat(color_type_id) as color_type_id";
	
	$sql_pre="select  b.job_no,$color_type_group from wo_pre_cost_fabric_cost_dtls  b,wo_po_details_master a where a.job_no=b.job_no and b.status_active=1 and b.is_deleted=0 group by b.job_no";
	$pre_result=sql_select($sql_pre);
	$fab_color_type_arr=array();
	foreach($pre_result as $row)
	{
		$fab_color_type_arr[$row[csf("job_no")]]['color_type_id']=$row[csf("color_type_id")];
	}
	unset($pre_result);
	$sql_sum="select  b.job_no, b.fab_knit_req_kg, b.fab_knit_fin_req_kg from wo_pre_cost_sum_dtls  b,wo_po_details_master a where a.job_no=b.job_no and b.status_active=1 and b.is_deleted=0 $marchd_cond";
	$pre_result_sum=sql_select($sql_sum);
	$fab_grey_cons_arr=array();
	foreach($pre_result_sum as $row)
	{
		$fab_grey_cons_arr[$row[csf("job_no")]]['fab_knit_req_kg']=$row[csf("fab_knit_req_kg")];
		$fab_grey_cons_arr[$row[csf("job_no")]]['fab_knit_fin_req_kg']=$row[csf("fab_knit_fin_req_kg")];
	}
	unset($pre_result_sum);
	
	if($db_type==2) $embl_group="LISTAGG(CAST(emb_name AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY emb_name) as emb_name";
	else if($db_type==0) $embl_group="group_concat(emb_name) as emb_name";
	$sql_ebl="select  b.job_no,$embl_group from wo_pre_cost_embe_cost_dtls  b,wo_po_details_master a where a.job_no=b.job_no and b.status_active=1 and b.is_deleted=0 and cons_dzn_gmts>0 group by b.job_no";
	//echo $sql_ebl;
	$ebl_result=sql_select($sql_ebl);
	$embl_fab_type_arr=array();
	foreach($ebl_result as $row)
	{
		$embl_fab_type_arr[$row[csf("job_no")]]['emb_name']=$row[csf("emb_name")];
	}
	unset($ebl_result);
	
	$embl_order_arr=array();
	$sql_embl="select job_no, gmts_item_id, embelishment, embro, wash, spworks, gmtsdying from wo_po_details_mas_set_details";
	$embl_result=sql_select($sql_embl);
	$embl_ord_type_arr=array();
	foreach($embl_result as $row)
	{
		$embl_type='';
		
		if($row[csf("embelishment")]==1) $embl_type.='1,';
		if($row[csf("embro")]==1) $embl_type.='2,';
		if($row[csf("wash")]==1) $embl_type.='3,';
		if($row[csf("spworks")]==1) $embl_type.='4,';
		if($row[csf("gmtsdying")]==1) $embl_type.='5,';
		
		$embl_ord_type_arr[$row[csf("job_no")]]['emb_name'].=$embl_type.',';
	}
	unset($embl_result);
	//print_r($embl_fab_type_arr); die;
	
	
	
	$sql_book="SELECT  a.entry_form,a.is_short, a.item_category,a.fabric_source,a.is_approved,b.po_break_down_id as po_id,b.job_no,a.booking_no,a.booking_no_prefix_num as booking_no_pre from wo_booking_dtls  b, wo_booking_mst a where a.booking_no=b.booking_no and b.booking_type=1 and b.status_active=1 and b.is_deleted=0  group by  a.entry_form,a.is_short, a.item_category,a.fabric_source,a.is_approved,b.po_break_down_id  ,b.job_no,a.booking_no,a.booking_no_prefix_num order by a.is_short asc ";
	$pre_book=sql_select($sql_book);
	$fab_booking_arr=array();
	$fab_booking_type_arr=array();
	$is_booking_exist=array();
	foreach($pre_book as $row)
	{
		if(  $is_booking_exist[$row[csf("po_id")]][$row[csf("booking_no_pre")]]=="")
		{


			if($fab_booking_arr[$row[csf("po_id")]]['booking_no_pre']=="")
			{
				if($row[csf("is_short")]==1){ 
					$fab_booking_arr[$row[csf("po_id")]]['booking_no_pre'].="S  ".$row[csf("booking_no_pre")];
					$fab_booking_type_arr[$row[csf("po_id")]]='s';
				}
				else if($row[csf("entry_form")]==108){
					$fab_booking_arr[$row[csf("po_id")]]['booking_no_pre'].="P  ".$row[csf("booking_no_pre")];
					$fab_booking_type_arr[$row[csf("po_id")]]='p';
				}
				else{
					$fab_booking_arr[$row[csf("po_id")]]['booking_no_pre'].="M  ".$row[csf("booking_no_pre")];
					$fab_booking_type_arr[$row[csf("po_id")]]='m';
				}

			}
			else
			{
				if($row[csf("is_short")]==1){ 
					$fab_booking_arr[$row[csf("po_id")]]['booking_no_pre'].="<br>S  ".$row[csf("booking_no_pre")];
					$fab_booking_type_arr[$row[csf("po_id")]]='s';
				}
				else if($row[csf("entry_form")]==108){
					$fab_booking_arr[$row[csf("po_id")]]['booking_no_pre'].="<br>P  ".$row[csf("booking_no_pre")];
					$fab_booking_type_arr[$row[csf("po_id")]]='p';
				}
				else{
					$fab_booking_arr[$row[csf("po_id")]]['booking_no_pre'].="<br>M  ".$row[csf("booking_no_pre")];
					$fab_booking_type_arr[$row[csf("po_id")]]='m';
				}
			}
			$is_booking_exist[$row[csf("po_id")]][$row[csf("booking_no_pre")]]=$row[csf("booking_no_pre")];
		}
		$fab_booking_arr[$row[csf("po_id")]]['booking_no']=$row[csf("booking_no")];
		$fab_booking_arr[$row[csf("po_id")]]['item_category']=$row[csf("item_category")];
		$fab_booking_arr[$row[csf("po_id")]]['fabric_source']=$row[csf("fabric_source")];
		$fab_booking_arr[$row[csf("po_id")]]['is_approved']=$row[csf("is_approved")];
	}
	//print_r($fab_booking_arr);
	unset($pre_book);
	
	
	$sql_print2="select  a.template_name as company,a.format_id from lib_report_template a  where a.module_id=2 and a.report_id=1 and a.is_deleted=0 and a.status_active=1 $company_con2";
	$results2=sql_select($sql_print2);
	$fab_booking_button_arr=array();
	foreach($results2 as $row)
	{
		$fab_booking_button_arr2[$row[csf("company")]]['print']=$row[csf("format_id")];
	}
	unset($results2);
	
	
	
	$sql_print="select  a.template_name as company,a.format_id from lib_report_template a  where a.module_id=2 and a.report_id=35 and a.is_deleted=0 and a.status_active=1 $company_con2";
	$results=sql_select($sql_print);
	$fab_booking_button_arr=array();
	foreach($results as $row)
	{
		$fab_booking_button_arr[$row[csf("company")]]['partial_fb_print']=$row[csf("format_id")];
	}
	unset($results);
	
	  $sql_sewing="select  b.location,b.production_source as prod_source,b.serving_company,d.production_qnty as production_qnty,b.company_id,c.country_ship_date as country_ship_date,c.cutup_date,c.po_break_down_id as po_id,e.buyer_name from pro_garments_production_mst  b,pro_garments_production_dtls d,wo_po_color_size_breakdown c,wo_po_details_master e where b.id=d.mst_id and c.id=d.color_size_break_down_id and e.job_no=c.job_no_mst and b.production_type=5 and d.production_type=5 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 ";
	$sew_result=sql_select($sql_sewing);
	$sewing_outCompany_arr=array();
	foreach($sew_result as $row)
	{
		$sewing_outCompany_arr[$row[csf("po_id")]]['company_id']=$row[csf("company_id")];
		$sewing_outCompany_arr[$row[csf("po_id")]]['prod_source']=$row[csf("prod_source")];
		$sewing_outCompany_arr[$row[csf("po_id")]]['location']=$row[csf("location")];
		$sewing_outCompany_arr[$row[csf("po_id")]]['serving_company']=$row[csf("serving_company")];
		if($cbo_date_category==1)
		{
			$row[csf("country_ship_date")]=$row[csf("country_ship_date")];
		}
		else //Cut-Off
		{
			$row[csf("country_ship_date")]=$row[csf("cutup_date")];	
		}
		$sewing_outCompany_bal_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$row[csf("buyer_name")]][$row[csf("po_id")]][date("Y-m-d",strtotime($row[csf("country_ship_date")]))]+=$row[csf("production_qnty")];
		
		//$sewing_outCompany_arr[$row[csf("po_id")]]['prod_qty']+=$row[csf("production_qnty")];
	}
	unset($sew_result);
	//print_r($fab_booking_button_arr2);die;
	
	if($shipment_status_id==2) //Running Order Balance Qty
	{
		
			
		 $sql_exf_c_date="select d.id as color_break_id,a.buyer_name,d.po_break_down_id as po_id,d.country_ship_date as country_ship_date,d.cutup_date, c.is_confirmed
		 from wo_po_break_down c,wo_po_details_master a,wo_po_color_size_breakdown d  where  c.job_no_mst=a.job_no and  d.status_active=1 and d.is_deleted=0 and c.id=d.po_break_down_id  and  a.job_no=d.job_no_mst  $company_con  $buyer_cond $marchd_cond $season_cond $date_cond2";
		$result_c=sql_select($sql_exf_c_date);
		foreach($result_c as $row)
		{
			if($cbo_date_category==1)
			{
				$row[csf("country_ship_date")]=$row[csf("country_ship_date")];
			}
			else //Cut-Off
			{
				$row[csf("country_ship_date")]=$row[csf("cutup_date")];	
			}
			
			if($row[csf("is_confirmed")]==1)
			{
				$c_date_con_arr[$row[csf("buyer_name")]][$row[csf("po_id")]][$row[csf("color_break_id")]]['country_date']=$row[csf("country_ship_date")];
				$c_date_con_arr[$row[csf("buyer_name")]][$row[csf("po_id")]][$row[csf("color_break_id")]]['cut_date']=$row[csf("country_ship_date")];
			}
			else
			{
				$c_date_con_arr[$row[csf("buyer_name")]][$row[csf("po_id")]][$row[csf("color_break_id")]]['country_date']=$row[csf("country_ship_date")];
				$c_date_con_arr[$row[csf("buyer_name")]][$row[csf("po_id")]][$row[csf("color_break_id")]]['cut_date']=$row[csf("country_ship_date")];
			}
		}
			
		$sql_exf="SELECT a.buyer_name,b.po_break_down_id as po_id,d.color_size_break_down_id as color_break_id, c.is_confirmed, b.shiping_status,
		(CASE WHEN b.entry_form!=85 THEN d.production_qnty 	 ELSE 0 END) as ex_fact_qty,
		(CASE WHEN b.entry_form=85 THEN d.production_qnty  ELSE 0 END) as ex_fact_ret_qty
		 from wo_po_break_down c,pro_ex_factory_mst b,wo_po_details_master a,pro_ex_factory_dtls d  where c.id=b.po_break_down_id and c.job_no_mst=a.job_no and b.id=d.mst_id and b.po_break_down_id=b.po_break_down_id and  b.status_active=1 and b.is_deleted=0 and  d.status_active=1 and d.is_deleted=0   $marchd_cond $company_con  $buyer_cond $season_cond ";
		$result=sql_select($sql_exf);
		$ex_fact_con_arr=array();$ex_fact_proj_arr=array(); $ship_status_arr=array();
		foreach($result as $row)
		{
			if($cbo_date_category==1)
			{
				$row[csf("country_ship_date")]=$c_date_con_arr[$row[csf("buyer_name")]][$row[csf("po_id")]][$row[csf("color_break_id")]]['country_date'];
			}
			else //Cut-Off
			{
				$row[csf("country_ship_date")]=$c_date_con_arr[$row[csf("buyer_name")]][$row[csf("po_id")]][$row[csf("color_break_id")]]['cut_date'];	
			}
			//echo $row[csf("country_ship_date")];
			if($row[csf("is_confirmed")]==1)
			{
			$ex_fact_con_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$row[csf("buyer_name")]][$row[csf("po_id")]][date("Y-m-d",strtotime($row[csf("country_ship_date")]))]+=$row[csf("ex_fact_qty")]-$row[csf("ex_fact_ret_qty")];
			}
			else if($row[csf("is_confirmed")]==2)
			{
				$ex_fact_proj_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$row[csf("buyer_name")]][$row[csf("po_id")]][date("Y-m-d",strtotime($row[csf("country_ship_date")]))]+=$row[csf("ex_fact_qty")]-$row[csf("ex_fact_ret_qty")];
			}
			$ship_status_arr[$row[csf("po_id")]]=$row[csf("shiping_status")];
		}
		//print_r($ex_fact_con_arr);
	}
	$smv_arr=array();
	$smv_sql=sql_select("select job_no, set_item_ratio, smv_pcs, gmts_item_id from wo_po_details_mas_set_details");
	foreach($smv_sql as $row)
	{
		$smv_arr[$row[csf("job_no")]][$row[csf("gmts_item_id")]]=$row[csf("smv_pcs")];
	}
	unset($smv_sql);
	
	 $data_array="SELECT a.id as job_id, a.job_no, a.style_ref_no,a.dealing_marchant, a.company_name,  a.buyer_name, a.ship_mode,a.total_set_qnty as ratio, a.gmts_item_id,	 a.set_smv, a.season_buyer_wise as season, a.insert_date,
	a.update_date, a.order_uom, b.id as color_break_id, b.po_break_down_id,b.order_quantity as po_quantity_pcs,c.po_quantity as po_qty, b.country_ship_date, b.cutup_date, b.order_total,b.item_number_id, c.details_remarks, c.unit_price,c.is_confirmed,c.po_number,c.po_quantity,c.po_received_date from wo_po_details_master a,wo_po_color_size_breakdown b,wo_po_break_down c	where a.job_no=b.job_no_mst	and po_break_down_id=c.id and b.status_active=1  and a.status_active=1 and c.status_active=1 and b.is_deleted=0 and c.is_deleted=0 	$company_con $date_cond $buyer_cond	$order_status_cond	$season_cond $shipment_status_cond $marchd_cond	order by  $order_by_cond,c.is_confirmed,a.job_no ASC";

	

		
	//echo $data_array;die; 
	$all_po_array=array();
	$result_po=sql_select($data_array);
	$row_data_dtls=array();
	$tmp_arr=array();
	//$num_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
	$day_total_con=array(); $item_wise_qty_arr=array();
		foreach($result_po as $row)
		{
			$all_po_array[$row[csf("po_break_down_id")]]=$row[csf("po_break_down_id")];
			if($cbo_date_category==1)
			{
				$row[csf("country_ship_date")]=$row[csf("country_ship_date")];
			}
			else //Cut-Off
			{
				$row[csf("country_ship_date")]=$row[csf("cutup_date")];	
			}
			$coutry_ship_date=date("Y-m",strtotime($row[csf("country_ship_date")]));
			$poId=$row[csf("po_break_down_id")];
			$buyerId=$row[csf("buyer_name")];
			$is_confirmed=$row[csf("is_confirmed")];
			$order_qty=0;
			//$order_qty=$row[csf("po_quantity_pcs")]*$row[csf("ratio")];
			$tmp_arr[$coutry_ship_date][$buyerId][$is_confirmed][$poId]["po_qty_pcs"]+=$row[csf("po_quantity_pcs")];
			$tmp_arr[$coutry_ship_date][$buyerId][$is_confirmed][$poId]["buyer"] =$buyerId;
			$tmp_arr[$coutry_ship_date][$buyerId][$is_confirmed][$poId]["dealing_marchant"] =$row[csf("dealing_marchant")];
			$tmp_arr[$coutry_ship_date][$buyerId][$is_confirmed][$poId]["is_confirmed"] =$is_confirmed;
			$tmp_arr[$coutry_ship_date][$buyerId][$is_confirmed][$poId]["job_no"] =$row[csf("job_no")];
			$tmp_arr[$coutry_ship_date][$buyerId][$is_confirmed][$poId]["po_break_down_id"] =$poId;
			$tmp_arr[$coutry_ship_date][$buyerId][$is_confirmed][$poId]["po_number"] =$row[csf("po_number")];
			$tmp_arr[$coutry_ship_date][$buyerId][$is_confirmed][$poId]["style_ref_no"] =$row[csf("style_ref_no")];
			$tmp_arr[$coutry_ship_date][$buyerId][$is_confirmed][$poId]["set_smv"] =$row[csf("set_smv")];
			$tmp_arr[$coutry_ship_date][$buyerId][$is_confirmed][$poId]["order_uom"] =$row[csf("order_uom")];
			$tmp_arr[$coutry_ship_date][$buyerId][$is_confirmed][$poId]["gmts_item_id"] =$row[csf("gmts_item_id")];
			$tmp_arr[$coutry_ship_date][$buyerId][$is_confirmed][$poId]["unit_price"] =$row[csf("unit_price")];
			$tmp_arr[$coutry_ship_date][$buyerId][$is_confirmed][$poId]["details_remarks"] =$row[csf("details_remarks")];
			$tmp_arr[$coutry_ship_date][$buyerId][$is_confirmed][$poId]["season"] =$row[csf("season")];
			$tmp_arr[$coutry_ship_date][$buyerId][$is_confirmed][$poId]["ratio"] =$row[csf("ratio")];
			$tmp_arr[$coutry_ship_date][$buyerId][$is_confirmed][$poId]["company_name"] =$row[csf("company_name")];			
			//$tmp_arr[$coutry_ship_date][$buyerId][$is_confirmed][$poId]["po_quantity"] =$row[csf("po_quantity")];
			$tmp_arr[$coutry_ship_date][$buyerId][$is_confirmed][$poId]["ship_mode"] =$row[csf("ship_mode")];
			
			$tmp_arr[$coutry_ship_date][$buyerId][$is_confirmed][$poId]["update_date"] =$row[csf("update_date")];
			$tmp_arr[$coutry_ship_date][$buyerId][$is_confirmed][$poId]["insert_date"] =$row[csf("insert_date")];
			
			$tmp_arr[$coutry_ship_date][$buyerId][$is_confirmed][$poId]["po_received_date"] =$row[csf("po_received_date")];
			$item_wise_qty_arr[$poId][$row[csf("item_number_id")]]+=$row[csf("po_quantity_pcs")];
			if($is_confirmed==1)
			{
				$po_qty_arr[$poId]["po_qty"]=$row[csf("po_qty")];
				$row_data_dtls[$coutry_ship_date][$buyerId][$poId][date("Y-m-d",strtotime($row[csf("country_ship_date")]))]+=$row[csf("po_quantity_pcs")];
				$day_total_con[date("Y-m-d",strtotime($row[csf("country_ship_date")]))][$buyerId]+=$row[csf("po_quantity_pcs")];
			}
			else
			{
				$po_qty_arr[$poId]["po_qty"]=$row[csf("po_qty")];
				$row_data_dtls_project[$coutry_ship_date][$buyerId][$poId][date("Y-m-d",strtotime($row[csf("country_ship_date")]))]+=$row[csf("po_quantity_pcs")];
				$day_total_proj[date("Y-m-d",strtotime($row[csf("country_ship_date")]))][$buyerId]+=$row[csf("po_quantity_pcs")];
			}
			
			/*$po_total_price+= $row[csf("po_total_price")];
			$quantity_tot+=$row[csf("po_quantity_pcs")];
			
			if( $job_smv_arr[$row[csf("job_no")]] !=0)
			{
				$booked_basic_qnty=($row[csf("po_quantity")]*($job_smv_arr[$row[csf("job_no")]]))/$basic_smv_arr[$row[csf("company_name")]];
				$row_data_dtls[$coutry_ship_date][$row[csf("buyer_name")]]['boking_basic_qty']+=$booked_basic_qnty;
				$booked_basic_qnty_tot+=$booked_basic_qnty;
			}*/
		}
		unset($result_po);
		//print_r($day_total_con);
		
		//var_dump($tmp_arr);die;
		ob_start();
		//echo $shipment_status_id;
		
		if($db_type==2 && count($all_po_array)>999)
		{
			$all_po_ids_cond="";
			$chnk=array_chunk($all_po_array,999);
			foreach($chnk as $vals)
			{
				$ids=implode(",", $vals);
				if($all_po_ids_cond=="") $all_po_ids_cond.=" and (  c.id in ($ids) ";
				else $all_po_ids_cond.=" or (  c.id in ($ids) ";
				$all_po_ids_cond.=")";
			}

		}
		else
		{
			$all_po_ids_cond=" and c.id in (".implode(",", $all_po_array).")";
		}
	if(!$all_po_ids_cond)$all_po_ids_cond="";

	$total_pcs_sql="SELECT   c.id,b.order_quantity from wo_po_details_master a, wo_po_color_size_breakdown b,wo_po_break_down c where 	a.job_no=b.job_no_mst	and b.po_break_down_id=c.id and b.status_active=1 and c.status_active=1 and b.is_deleted=0 and c.is_deleted=0	$marchd_cond $all_po_ids_cond";

	$total_pcs_arr=array();
	foreach(sql_select($total_pcs_sql) as $v)
	{
		$total_pcs_arr[$v[csf("id")]]+=$v[csf("order_quantity")];
	}
	 $sample_sql="SELECT   c.id as po_id,d.sample_type,a.approval_status from wo_po_sample_approval_info a, wo_po_break_down c,lib_sample d where a.po_break_down_id=c.id and d.id=a.sample_type_id and a.status_active=1 and c.status_active=1 and c.is_deleted=0  and d.status_active=1 and d.sample_type=13  $all_po_ids_cond";

	$sample_arr=array();
	foreach(sql_select($sample_sql) as $row)
	{
		if($row[csf("approval_status")]==3)//approve
		{
			$yes_no_msg="Yes";
			//echo "AAAA,";
		}
		else
		{
			$yes_no_msg="No";
		}
		if($yes_no_msg)
		{
		$sample_arr[$row[csf("po_id")]]=$yes_no_msg;
		}
	}
	
	if($company_name==0)
	{
		echo '<font color="#FF0000">Company selection is mandatory to get the value of following column – Grey Fab (Kg) and Fin. Fab (Kg).</font>';
	}
	?>
		<table width="1200">

			<tr class="form_caption">
				<td colspan="8" align="center" style="font-size:18px;"><strong>
					<? 

					if($company_name)
					{
						echo $company_library[$company_name]; 
					}else
					{
						if($company_library[1] == "Metro Knitting and Dyeing Mills Ltd.")
						{
							echo $company_library[1]; 
						}
					}

					?>
				</strong> 
			</td>
			<td colspan="5" style="color: black;"> Generate Date: <? echo date("Y-m-d");?> 
			</td>

		</tr>
		<tr class="form_caption">
			<td colspan="8" align="center" style="font-size:12px;">Monthly Order Summary /
				<? echo $shipment_status_arr[$shipment_status_id]."/". $report_type_arr[$cbo_report_type];?>
				<td colspan="5" style="color: black;"> 
					Generate Time: <? echo date("h:i:sa");?> 

				</td>

			</tr>
			 

		</table>
        <?
		 	$condition= new condition();
		 	$condition->company_name("=$company_name");
			 if(str_replace("'","",$cbo_buyer_name)>0){
				  $condition->buyer_name("=$cbo_buyer_name");
			 }
			 
			if($cbo_date_category==1 && str_replace("'","",$start_date)!='' && str_replace("'","",$end_date)!=''){
				if($db_type==0) 
				{
					//$date_cond=" and b.country_ship_date between '$start_date' and '$end_date'";
					$condition->country_ship_date(" between '$start_date' and '$end_date'");
				}
				else if($db_type==2)
				{
					$condition->country_ship_date(" between '".date("j-M-Y",strtotime(str_replace("'","",$start_date)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$end_date)))."'");
				}
				  
			 }
			 if($cbo_date_category==2 && str_replace("'","",$start_date)!='' && str_replace("'","",$end_date)!=''){
				 
				 if($db_type==0) 
					{
						//$date_cond=" and b.country_ship_date between '$start_date' and '$end_date'";
						$condition->cutup_date(" between '$start_date' and '$end_date'");
					}
					else if($db_type==2)
					{
						$condition->cutup_date(" between '".date("j-M-Y",strtotime(str_replace("'","",$start_date)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$end_date)))."'");
					}
			 }
			  
		$condition->init();$x++;
		$fabric= new fabric($condition);
			//echo $fabric->getQuery(); die;
		$fabric_qty_arr=$fabric->getQtyArray_by_order_knitAndwoven_greyAndfinish();
		$grand_day_qty_arr=array(); $buyer_total_order_grey=array();
		foreach($tmp_arr as $date_key=>$buyer_id_arr)
		{
            $month_value=explode("-",$date_key);
			$num_days = cal_days_in_month(CAL_GREGORIAN, $month_value[1], $month_value[0]);
			//echo $num_days;
			//arsort($buyer_id_arr);
			//var_dump($date_key);die;
				
				foreach($buyer_id_arr as $buyer_id=>$is_confirm_arr)
				{
					$confirm_project_buyer_total=array();
					?>
                    Month  <? echo $months[$month_value[1]*1]."-".$month_value[0];  ?><br />
                    Buyer : <? echo $buyer_library[$buyer_id]; ?><br />
                    <?
					foreach($is_confirm_arr as $is_confirm_id=>$order_id_arr)
					{
						if($is_confirm_id==1)
						{
							echo "Confirm Order :";
							?>
							<br />
                             <table align="center" style="margin-left:400px">
                            <tr>
                            <td>&nbsp; </td> <td>&nbsp; </td>
                            <td bgcolor="#00CED1" height="15" width="30"></td>
							<td>&nbsp;New Job</td>
                            <td bgcolor="#1AA995" height="15" width="30">&nbsp;</td>
                            <td>&nbsp;Updated Job</td>
                            </tr>
                            </table>
							<table  width="<? if($all_date_button==""){ echo 2760;}else { echo 3360;} ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" >
                                <thead> 
                                    <tr style="font-family:'Arial Narrow'" >
                                        <th width="80">Job No</th>
                                        <th width="70" title="counter sample approval">C/S Approval</th>
                                        <th width="80">Order No</th>
                                        <th width="80">Style Ref.</th>
                                        <th width="80">Season</th>
                                        <th width="100">Dealing Marchant</th>
                                        <th width="80">Fabric (body)</th>
                                        
                                        <th width="80">Type</th> 
                                        <th width="80">Gmt Item</th>
                                        <th width="80">Embellish. Type</th>
                                        <th width="80">Working Factory</th>
                                        
                                        <th width="40">Avg. SMV</th>
                                        <th width="50">SMV Min.</th>
                                        <th width="35">Price</th>
                                        
                                        <th width="50">No of Color</th>
                                        <th width="70">Booking No</th>
                                        <th width="50">Grey Cons.</th>
                                        <th width="70">Grey Fab (Kg)</th>
                                        <th width="50">Fin. Cons.</th>
                                        <th width="70">Fin. Fab (Kg)</th>
                                        
                                        <th width="70">PO Receive Date</th>
                                        <th width="50">Week</th>
                                        <th width="70">Order Qty</th>
                                        <th width="70">Order Qty (Pcs)</th>
                                        <th width="70">C.Month Total</th>
                                        <th width="70">C.Month Bal</th>
                                        
                                        <th width="60">Ship mode</th>
                                        <th width="70">Sewing</th>
                                       
                                        <?
                                        for($m=1;$m<=$num_days;$m++)
                                        {
											$day=($m<=9)? '0'.$m:$m;
											//echo $date_key."-".$day."=".$day_total_con[$date_key."-".$day]."<br/>";
											
											if($all_date_button=="")
											{
												if($day_total_con[$date_key."-".$day][$buyer_id]>0){
													if($m==$num_days)
													{
														
														?>
														<th width="45" ><? echo  ($m<=9)? '0'.$m:$m; ?></th>
														<?
													}
													else
													{
														?>
														<th width="45"><? echo  ($m<=9)? '0'.$m:$m; ?></th>
														<?
													}
												}
											}
											else
											{
												if($m==$num_days)
												{
													
													?>
													<th width="45" ><? echo  ($m<=9)? '0'.$m:$m; ?></th>
													<?
												}
												else
												{
													?>
													<th width="45"><? echo  ($m<=9)? '0'.$m:$m; ?></th>
													<?
												}
											}
                                        }
                                        ?>
                                        <th>Remark</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?
                                //($m<=9)? '0'.$m:$m.$month_value[1].$month_value[0];
                                $k=1;$total_val="";$total_buyer_qty="";$confirm_total_qty=array();$total_grey_qty=0;$total_finish_qty=0;$total_conf_sewing_output=0;
                                foreach($order_id_arr as $order_id=>$row)
                                {
									if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									
									$dzn_qnty=0;
									$costing_per_id=$costing_per_arr[$row["job_no"]];
									if($costing_per_id==1) $dzn_qnty=12;
									else if($costing_per_id==3) $dzn_qnty=12*2;
									else if($costing_per_id==4) $dzn_qnty=12*3;
									else if($costing_per_id==5) $dzn_qnty=12*4;
									else $dzn_qnty=1;
									$dzn_qnty=$dzn_qnty*$row['ratio'];
									$order_qty_pcs=$row["po_qty"]*$row['ratio'];
									$update_date_arr=explode(" ",$row["update_date"]);
									$insert_date_arr=explode(" ",$row["insert_date"]);
									$update_date=date("d-m-Y",strtotime($update_date_arr[0]));
									$insert_date=date("d-m-Y",strtotime($insert_date_arr[0]));
									$today_date=date("d-m-Y");//date("d-m-Y");
									
									
									
									if($update_date=='' || $update_date=='01-01-1970') $date_diff_insert=datediff( "d", $insert_date , $today_date);
									if($update_date!='' || $update_date!='01-01-1970') $date_diff_update=datediff( "d", $update_date , $today_date);
									 //echo 'sdsdsd';
									if($date_diff_insert<=3) $bg_color="#00CED1";	
									else if($date_diff_update<=3) $bg_color="#1AA995";	
									else $bg_color=$bgcolor;
									//echo $date_diff.'<br/>';
									
									//$fab_grey_knit=($fabric_qty_arr['knit']['grey'][$row["po_break_down_id"]]/$order_qty_pcs)*$dzn_qnty;
									//$fab_grey_woven=($fabric_qty_arr['woven']['grey'][$row["po_break_down_id"]]/$order_qty_pcs)*$dzn_qnty;
									
									$fab_grey_cons=$fab_grey_cons_arr[$row["job_no"]]['fab_knit_req_kg'];//$fab_grey_knit+$fab_grey_woven;
									$total_grey_cons+=$fab_grey_cons;
									$fab_grey_knit=($fabric_qty_arr['knit']['grey'][$row["po_break_down_id"]]);
									$fab_grey_woven=($fabric_qty_arr['woven']['grey'][$row["po_break_down_id"]]);
									$fab_grey_qty=$fab_grey_knit+$fab_grey_woven;
									$total_grey_qty+=$fab_grey_qty;
									
									$fab_fin_cons=$fab_grey_cons_arr[$row["job_no"]]['fab_knit_fin_req_kg'];//$fab_grey_knit+$fab_grey_woven;
									$total_fin_qty+=$fab_fin_cons;
									
									$fab_finish_knit=($fabric_qty_arr['knit']['finish'][$row["po_break_down_id"]]);
									$fab_finish_woven=($fabric_qty_arr['woven']['finish'][$row["po_break_down_id"]]);
									$fab_finish_cons=$fab_finish_knit+$fab_finish_woven;
									$total_finish_qty+=$fab_finish_cons;
									$prod_source=$sewing_outCompany_arr[$row["po_break_down_id"]]['prod_source'];
									$serving_company=$sewing_outCompany_arr[$row["po_break_down_id"]]['serving_company'];
									$location_id=$sewing_outCompany_arr[$row["po_break_down_id"]]['location'];
									if($prod_source==1)
									{
										$sewing_company=$company_library[$serving_company];
									}
									else
									{
										$sewing_company=$supplier_name_arr[$serving_company];
									}
									if($location_id)
									{
										$locationName="<br>".$location_lib[$location_id];
									}
									else $locationName="";
									//locationName
									//$confirm_total_qty =array();
									$item_category=$fab_booking_arr[$row["po_break_down_id"]]['item_category'];
									$fabric_source=$fab_booking_arr[$row["po_break_down_id"]]['fabric_source'];
									$is_approved=$fab_booking_arr[$row["po_break_down_id"]]['is_approved'];
								 $booking=$fab_booking_arr[$row["po_break_down_id"]]['booking_no'];
								$print_report_id=$fab_booking_button_arr2[$row["company_name"]]['print'];
								 $partial_fb_print_report_id=$fab_booking_button_arr[$row["company_name"]]['partial_fb_print'];
								 //echo $partial_fb_print_report_id.', **';
								
								$variable=$booking;
								if($booking)
								{
									
									if($fab_booking_type_arr[$row["po_break_down_id"]]=='p'){
										$format_ids=explode(",",$partial_fb_print_report_id);
										$row_id=$format_ids[0];
										if($row_id)
										{
											$variable='';
											if($row_id==220)
											{ 
												$variable="<a href='#' onClick=\"generate_worder_report('".$booking."','".$row["company_name"]."','".$row["po_break_down_id"]."','".$item_category."','".$fabric_source."','".$row["job_no"]."','".$is_approved."','print_booking_northern_new','".$k."','".$fab_booking_type_arr[$row["po_break_down_id"]]."')\"> ".$fab_booking_arr[$row["po_break_down_id"]]['booking_no_pre']." <a/>";
											}
											
										}
										
									}
									else
									{
										$format_ids=explode(",",$print_report_id);
										$row_id=$format_ids[0];
										if($row_id)
										{
											if($row_id==1)
											{ 
												$variable="<a href='#' onClick=\"generate_worder_report('".$booking."','".$row["company_name"]."','".$row["po_break_down_id"]."','".$item_category."','".$fabric_source."','".$row["job_no"]."','".$is_approved."','show_fabric_booking_report_gr','".$k."')\"> ".$fab_booking_arr[$row["po_break_down_id"]]['booking_no_pre']." <a/>";
											}
											elseif($row_id==2)
											{ 
												$variable="<a href='#' onClick=\"generate_worder_report('".$booking."','".$row["company_name"]."','".$row["po_break_down_id"]."','".$item_category."','".$fabric_source."','".$row["job_no"]."','".$is_approved."','show_fabric_booking_report','".$k."')\"> ".$fab_booking_arr[$row["po_break_down_id"]]['booking_no_pre']." <a/>";
											}
											elseif($row_id==3)
											{ 
												$variable="<a href='#' onClick=\"generate_worder_report('".$booking."','".$row["company_name"]."','".$row["po_break_down_id"]."','".$item_category."','".$fabric_source."','". $row["job_no"]."','".$is_approved."','show_fabric_booking_report3','".$k."')\">".$fab_booking_arr[$row["po_break_down_id"]]['booking_no_pre']." <a/>";
											}
											elseif($row_id==4)
											{ 
												$variable="<a href='#' onClick=\"generate_worder_report('".$booking."','".$row["company_name"]."','".$row["po_break_down_id"]."','".$item_category."','".$fabric_source."','". $row["job_no"]."','".$is_approved."','show_fabric_booking_report1','".$k."')\"> ".$fab_booking_arr[$row["po_break_down_id"]]['booking_no_pre']." <a/>";
											}
											elseif($row_id==5)
											{ 
												$variable="<a href='#'  onClick=\"generate_worder_report('".$booking."','".$row["company_name"]."','".$row["po_break_down_id"]."','".$item_category."','".$fabric_source."','". $row["job_no"]."','".$is_approved."','show_fabric_booking_report2','".$k."')\"> ".$fab_booking_arr[$row["po_break_down_id"]]['booking_no_pre']." <a/>";
											}
											elseif($row_id==6)
											{ 
												$variable="<a href='#'  onClick=\"generate_worder_report('".$booking."','".$row["company_name"]."','".$row["po_break_down_id"]."','".$item_category."','".$fabric_source."','". $row["job_no"]."','".$is_approved."','show_fabric_booking_report4','".$k."','".$fab_booking_type_arr[$row["po_break_down_id"]]."')\"> ".$fab_booking_arr[$row["po_break_down_id"]]['booking_no_pre']." <a/>";
											}
											elseif($row_id==7)
											{ 
												$variable="<a href='#'  onClick=\"generate_worder_report('".$booking."','".$row["company_name"]."','".$row["po_break_down_id"]."','".$item_category."','".$fabric_source."','". $row["job_no"]."','".$is_approved."','show_fabric_booking_report5','".$k."')\"> ".$fab_booking_arr[$row["po_break_down_id"]]['booking_no_pre']." <a/>";
											}
											else
											{
												$variable=$fab_booking_arr[$row["po_break_down_id"]]['booking_no_pre'];
											}
										}
										
									}
									
									
									
								}
								else
								{
									$variable=$booking;	
								}//Booking end
								
								
								
								$c_tot_exfact_qty_con=$sewing_outQty=0;
								for($p=1;$p<=$num_days;$p++)
								{
									$day=($p<=9)? '0'.$p:$p;									
									$sewing_outQty+=$sewing_outCompany_bal_arr[$date_key][$buyer_id][$order_id][$date_key."-".$day];
									if($day_total_con[$date_key."-".$day][$buyer_id]>0)
									{
										$c_tot_exfact_qty_con+=$ex_fact_con_arr[$date_key][$buyer_id][$order_id][$date_key."-".$day];	
									}
									if($shipment_status_id==2) //Balance Qty
									{
										
										$c_tot_bal_qty=$row["po_qty_pcs"]-$c_tot_exfact_qty_con;
										if($c_tot_bal_qty<0) $c_days_qty=0;else $c_days_qty=$c_tot_bal_qty;
									}
									else
									{
										$c_days_qty=$row["po_qty_pcs"]-$c_tot_exfact_qty_con;
									}
								}
								if(!$c_days_qty)continue;
								if($shipment_status_id==2)
								{
									//if( 0<$c_days_qty && $ship_status_arr[$row["po_break_down_id"]]!=3)
									//{
										?>
										<tr style="font-family:'Arial Narrow'" bgcolor="<? echo $bg_color; ?>" onClick="change_color('tr_<? echo $x; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $x; ?>">
                                            <td><p><? echo $row["job_no"];?></p></td>
                                            <td><p><? echo $sample_arr[$order_id];//$sewing_outCompany_arr[$row["po_break_down_id"]]['prod_qty']; ?></p></td>
                                            <td><p><? echo $row["po_number"];?></p></td>
                                            <td><p><? echo $row["style_ref_no"];?></p></td>
                                            <td><p><? echo $season_arr[$row["season"]];?></p></td>
                                            <td><p><? echo $dealing_marchant_arr[$row["dealing_marchant"]];?></p></td>
                                            <td><p><? echo $body_part_arr[$row["po_break_down_id"]]; ?></p></td>
                                            <td><p><? //echo $fab_type_arr[$row["job_no"]]['composition'];
												$color_type_id=array_unique(explode(",",$fab_color_type_arr[$row["job_no"]]['color_type_id']));						
												$color_id='';
												foreach($color_type_id as $c_id)
												{
													if($color_id=="") $color_id=$color_type[$c_id]; else $color_id.=", ".$color_type[$c_id];
												}
												echo $color_id;
                                            ?></p></td>
                                            <td><p><? 
                                            $gmts_item=''; $gmts_item_id=explode(",",$row["gmts_item_id"]);
											 $smv_min=0;
                                            foreach($gmts_item_id as $item_id)
                                            {
												$item_smv=0; $item_qty=0; $temp_smv_min=0;
												$item_smv=$item_smv=$smv_arr[$row["job_no"]][$item_id];
												$item_qty=$item_wise_qty_arr[$order_id][$item_id];
												$temp_smv_min=$item_smv*$item_qty;
												$smv_min+=$temp_smv_min;
                                            	if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=", ".$garments_item[$item_id];
                                            }
											$avg_smv=0;
											//$avg_smv=$smv_min/$row["po_qty_pcs"]; //old
											$avg_smv=$row['set_smv']/$row['ratio']; //new
											$smv_min=$avg_smv*$c_days_qty; //new
                                            echo $gmts_item;?></p></td>
                                            <td><p><? //if($embl_ord_type_arr[$row["job_no"]]['emb_name']!="") { $embl_name_id=array_unique(explode(",",chop($embl_ord_type_arr[$row["job_no"]]['emb_name'],','))); echo 'k' } else 
											$embl_name_id=array_unique(explode(",",$embl_fab_type_arr[$row["job_no"]]['emb_name']));								
                                            $emble_names='';
                                            foreach($embl_name_id as $emb_id)
                                            {
                                            	if($emble_names=="") $emble_names=$emblishment_name_array[$emb_id]; else $emble_names.=", ".$emblishment_name_array[$emb_id];
                                            }
                                            echo $emble_names;
                                            ?></p></td>
                                            <td><p><? echo $sewing_company.$locationName;?></p></td>
                                            <td align="center">
                                            	<p>
                                            		<a href="##" onClick="openmypage_smv('<? echo $row["job_no"];?>',<? echo $row["order_uom"];?>,<? echo $order_id;?>)" ><? echo  number_format($avg_smv,3);?> </a>
                                            	</p>
                                            </td>
                                            <td align="right"><p><? echo $smv_min;?></p></td>
                                            <td align="center"><p><? echo $row["unit_price"];?></p></td>
                                            
                                            <td align="center"><p><? echo $sql_color_no_arr[$row["po_break_down_id"]]; ?></p></td>
                                            <td><p><? echo $variable;?></p></td>
                                            <td align="center"><p><? echo number_format($fab_grey_cons,2);?></p></td>
                                            <td align="right"><p><? echo number_format($fab_grey_qty,2);?></p></td>
                                            <td align="center"><p><? echo number_format($fab_fin_cons,2);?></p></td>
                                            <td align="right"><p><? echo number_format($fab_finish_cons,2);?></p></td>
                                            
                                            <td align="center"><p><? if($row["po_received_date"]!="" && $row["po_received_date"]!='0000-00-00') echo change_date_format($row["po_received_date"]); ?></p></td>
                                            <td align="center"><p><? if($row["po_received_date"]!="" && $row["po_received_date"]!='0000-00-00') echo $weak_of_year[$row["po_received_date"]]; ?></p></td>
                                            <td align="right"><p><? echo number_format($po_qty_arr[$order_id]["po_qty"],0); $total_ordset_qty +=$po_qty_arr[$order_id]["po_qty"]; ?></p></td>
                                            <td align="right"><p><? echo number_format( $total_pcs_arr[$row["po_break_down_id"]] ,0); $total_ord_qty +=$total_pcs_arr[$row["po_break_down_id"]]; ?></p></td>
                                            <td align="right"><p><? echo number_format( $row["po_qty_pcs"] ,0); $total_c_month_qty +=$row["po_qty_pcs"]; ?></p></td>
                                            <td align="right" title="<? echo $c_tot_exfact_qty_con;?>"><p><? echo number_format($c_days_qty,0); $total_month_qty +=$c_days_qty; ?></p></td>
                                            <td align="center"><p><? echo $shipment_mode[$row["ship_mode"]]; ?></p></td>
                                            <td align="right"><p><? echo $sewing_outQty;//$sewing_outCompany_arr[$row["po_break_down_id"]]['prod_qty']; ?></p></td>
                                             
                                            <?
												for($p=1;$p<=$num_days;$p++)
												{
													$day=($p<=9)? '0'.$p:$p;
													if($shipment_status_id==2) //Balance Qty
													{
														$exfact_qty_con=$ex_fact_con_arr[$date_key][$buyer_id][$order_id][$date_key."-".$day];
														$tot_bal_qty=$row_data_dtls[$date_key][$buyer_id][$order_id][$date_key."-".$day]-$exfact_qty_con;
														if($tot_bal_qty<0) $days_qty=0;else $days_qty=$tot_bal_qty;
													}
													else
													{
														$days_qty=$row_data_dtls[$date_key][$buyer_id][$order_id][$date_key."-".$day]-$exfact_qty_con;
													}
														if($all_date_button=="")
														{
															if($day_total_con[$date_key."-".$day][$buyer_id]>0)
															{
																?>
																<td title="<? echo $exfact_qty_con;?>" style="height:60px;" width="45" align="center"  valign="bottom"><div><? if($days_qty=="")  echo ""; else echo  number_format($days_qty,0); ?></div></td>
																<?
																$confirm_total_qty[$p] +=$days_qty;
																$confirm_project_buyer_total[$buyer_id][$p] +=$days_qty;
															}
														}
														else
														{
															?>
															<td title="<? echo $exfact_qty_con;?>" style="height:60px;" width="45" align="center"  valign="bottom"><div><? if($days_qty=="")  echo ""; else echo  number_format($days_qty,0); ?></div></td>
															<?
															$confirm_total_qty[$p] +=$days_qty;
															$confirm_project_buyer_total[$buyer_id][$p] +=$days_qty;
														}
												}
                                            ?>
                                            <td><p><? echo $row["details_remarks"]; ?></p></td>
										</tr>
										<?
										$k++; $x++;
										//$total_conf_sewing_output+=$sewing_outCompany_arr[$row["po_break_down_id"]]['prod_qty'];
										$total_conf_sewing_output+=$sewing_outQty;
									//}
								}
								else
								{
									?>
                                        <tr style="font-family:'Arial Narrow'" bgcolor="<? echo $bg_color; ?>" onClick="change_color('tr_<? echo $x; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $x; ?>">
                                            <td width="80"><p><? echo $row["job_no"];?></p></td>
                                            <td width="70"><p><? echo $sample_arr[$order_id]; ?></p></td>
                                            <td width="80"><p><? echo $row["po_number"];?></p></td>
                                            <td width="80"><p><? echo $row["style_ref_no"];?></p></td>
                                            <td width="80"><p><? echo $season_arr[$row["season"]];?></p></td>
                                            <td  width="100"><p><? echo $dealing_marchant_arr[$row["dealing_marchant"]];?></p></td>
                                            <td width="80"><p><? echo $body_part_arr[$row["po_break_down_id"]]; ?></p></td>
                                            <td width="80"><p><? //echo $fab_type_arr[$row["job_no"]]['composition'];
												$color_type_id=array_unique(explode(",",$fab_color_type_arr[$row["job_no"]]['color_type_id']));						
												$color_id='';
												foreach($color_type_id as $c_id)
												{
												if($color_id=="") $color_id=$color_type[$c_id]; else $color_id.=", ".$color_type[$c_id];
												}
												echo $color_id;
												?></p></td>
                                            	<td width="80"><p><? 
												$gmts_item=''; $gmts_item_id=explode(",",$row["gmts_item_id"]);
												$smv_min=0;
												foreach($gmts_item_id as $item_id)
												{
													$item_smv=0; $item_qty=0; $temp_smv_min=0;
													$item_smv=$item_smv=$smv_arr[$row["job_no"]][$item_id];
													$item_qty=$item_wise_qty_arr[$order_id][$item_id];
													$temp_smv_min=$item_smv*$item_qty;
													$smv_min+=$temp_smv_min;
													if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=", ".$garments_item[$item_id];
												}
												$avg_smv=0;
												//$avg_smv=$smv_min/$row["po_qty_pcs"]; //old
												$avg_smv=$row['set_smv']/$row['ratio']; //new
											  	$smv_min=$avg_smv*$c_days_qty; //new
												echo $gmts_item;?></p></td>
                                            <td width="80"><p><? //if($embl_ord_type_arr[$row["job_no"]]['emb_name']!="") $embl_name_id=array_unique(explode(",",chop($embl_ord_type_arr[$row["job_no"]]['emb_name'],','))); else 
											$embl_name_id=array_unique(explode(",",$embl_fab_type_arr[$row["job_no"]]['emb_name']));								
												$emble_names='';
												foreach($embl_name_id as $emb_id)
												{
													if($emble_names=="") $emble_names=$emblishment_name_array[$emb_id]; else $emble_names.=", ".$emblishment_name_array[$emb_id];
												}
												echo $emble_names;
												?></p></td>
                                            <td width="80"><p><? echo $sewing_company.$locationName;?></p></td>
                                            <!-- <td width="30" align="center"><p><? //echo $row["set_smv"];?></p></td> -->
                                            <td align="center" width="40" title="<? echo 'order=='.$row["order_uom"];?>">
                                            <p>
                                                <a href="##" onClick="openmypage_smv('<? echo $row["job_no"];?>',<? echo $row["order_uom"];?>,<? echo $order_id;?>)" ><? echo  number_format($avg_smv,3);?> </a>
                                            </p>
                                            	
                                            </td>
                                            <td width="50" align="right"><p><? echo $smv_min;?></p></td>
                                            <td width="35" align="center"><p><? echo $row["unit_price"];?></p></td>
                                            <td width="50" align="center"><p><? echo $sql_color_no_arr[$row["po_break_down_id"]]; ?></p></td>
                                            <td width="70"><p><? echo $variable;?></p></td>
                                            <td width="50" align="center"><p><? echo number_format($fab_grey_cons,2);?></p></td>
                                            <td width="70" align="right"><p><? echo number_format($fab_grey_qty,2);?></p></td>
                                            <td width="50" align="center"><p><? echo number_format($fab_fin_cons,2);?></p></td>
                                            <td width="70" align="right"><p><? echo number_format($fab_finish_cons,2);?></p></td>
                                            
                                            <td width="70" align="center"><p><? if($row["po_received_date"]!="" && $row["po_received_date"]!='0000-00-00') echo change_date_format($row["po_received_date"]); ?></p></td>
                                            <td width="50" align="center"><p><? if($row["po_received_date"]!="" && $row["po_received_date"]!='0000-00-00') echo $weak_of_year[$row["po_received_date"]]; ?></p></td>
                                            <td align="right"><p><? echo number_format($po_qty_arr[$order_id]["po_qty"],0); $total_ordset_qty +=$po_qty_arr[$order_id]["po_qty"]; ?></p></td>
                                            <td width="70" align="right"><p><? echo number_format($total_pcs_arr[$row["po_break_down_id"]],0); $total_ord_qty +=$total_pcs_arr[$row["po_break_down_id"]]; ?></p></td>
                                            <td width="70" align="right"><p><? echo number_format($row["po_qty_pcs"],0); $total_c_month_qty +=$row["po_qty_pcs"]; ?></p></td>
                                            <td width="70" align="right" title="<? echo $c_tot_exfact_qty_con;?>"><p><? echo number_format($c_days_qty,0); $total_month_qty +=$c_days_qty; ?></p></td>
                                            <td width="60" align="center"><p><? echo $shipment_mode[$row["ship_mode"]]; ?></p></td>
                                            <?
											$sewing_outQty=0;
                                             for($p=1;$p<=$num_days;$p++)
                                            {
												$day=($p<=9)? '0'.$p:$p;
												$sewing_outQty+=$sewing_outCompany_bal_arr[$date_key][$buyer_id][$order_id][$date_key."-".$day];
											}
											?>
                                            <td width="70" align="right"><p><? echo $sewing_outQty;//$sewing_outCompany_arr[$row["po_break_down_id"]]['prod_qty']; ?></p></td>
                                            
                                            <?
											
                                            for($p=1;$p<=$num_days;$p++)
                                            {
												$day=($p<=9)? '0'.$p:$p;
												if($shipment_status_id==2) //Balance Qty
												{
													$exfact_qty_con=$ex_fact_con_arr[$date_key][$buyer_id][$order_id][$date_key."-".$day];
													$tot_bal_qty=$row_data_dtls[$date_key][$buyer_id][$order_id][$date_key."-".$day]-$exfact_qty_con;
													if($tot_bal_qty<0) $days_qty=0;else $days_qty=$tot_bal_qty;
												}
												else
												{
													$days_qty=$row_data_dtls[$date_key][$buyer_id][$order_id][$date_key."-".$day]-$exfact_qty_con;
												}
												if($all_date_button=="")
												{
													if($day_total_con[$date_key."-".$day][$buyer_id]>0){
														?>
														<td title="<? echo $exfact_qty_con;?>" style="height:60px;" width="45" align="center" valign="bottom"><div><? if($days_qty=="") echo ""; else echo  number_format($days_qty,0); ?></div></td>
														<?
														$confirm_total_qty[$p] +=$days_qty;
														$confirm_project_buyer_total[$buyer_id][$p] +=$days_qty;
													}
												}
												else
												{
													?>
													<td title="<? echo $exfact_qty_con;?>" style="height:60px;" width="45" align="center" valign="bottom"><div><? if($days_qty=="") echo ""; else echo  number_format($days_qty,0); ?></div></td>
													<?
													$confirm_total_qty[$p] +=$days_qty;
													$confirm_project_buyer_total[$buyer_id][$p] +=$days_qty;
												}
                                            }
                                            ?>
                                            <td><p><? echo $row["details_remarks"]; ?></p></td>
                                        </tr>
										<?
                                        $k++; $x++;
                                        $total_conf_sewing_output+=$sewing_outQty;
									}
								}
                                ?>
                                <tr bgcolor="#CCCCCC" style="font-family:'Arial Narrow'">
                                    <td colspan="16"  align="right" style="font-weight:bold;">Confirm Total:</td>
                                    <td align="right" style="font-weight:bold;"><?  //$buyer_total_order_grey[$buyer_id] +=$total_grey_cons; $total_grey_cons=''; ?></td>
                                    <td align="right" style="font-weight:bold;"><?  echo number_format($total_grey_qty,2); $buyer_total_order_grey[$buyer_id]+=$total_grey_qty; $total_grey_qty=''; ?></td>
                                    <td align="right" style="font-weight:bold;"><?  //$buyer_total_order_grey[$buyer_id] +=$total_grey_qty; $total_grey_qty=''; ?></td>
                                    <td align="right" style="font-weight:bold;"><? echo number_format($total_finish_qty,2);$buyer_total_order_finish[$buyer_id] +=$total_finish_qty; $total_finish_qty='';?></td>
                                    <td align="right" style="font-weight:bold;"><? //echo number_format($total_grey_qty,2);?></td>
                                    <td align="right" style="font-weight:bold;"><? //echo number_format($total_finish_qty,2);?></td>
                                    <td  align="right" style="font-weight:bold;"><p><? echo number_format($total_ordset_qty,0); $buyer_totalset_order [$buyer_id] +=$total_ordset_qty;  $total_ordset_qty=''; ?></p></td>
                                    <td align="right" style="font-weight:bold;"><p><? echo number_format($total_ord_qty,0); $buyer_total_order [$buyer_id] +=$total_ord_qty;  $total_ord_qty=''; ?></p></td>
                                    <td align="right" style="font-weight:bold;"><p><? echo number_format($total_c_month_qty,0); $buyer_total_c_month [$buyer_id] +=$total_c_month_qty;  $total_c_month_qty=0; ?></p></td>
                                    <td align="right" style="font-weight:bold;"><p><? echo number_format($total_month_qty,0);  $buyer_total_month [$buyer_id] +=$total_month_qty;  $total_month_qty=''; ?></p></td>
                                    <td align="right" style="font-weight:bold;">&nbsp;</td>
                                    <td align="right" style="font-weight:bold;"><? echo number_format($total_conf_sewing_output,2);?></td>
                                    
                                    <?
                                    for($p=1;$p<=$num_days;$p++)
                                    {
										$day=($p<=9)? '0'.$p:$p;
										if($all_date_button=="")
										{
											if($day_total_con[$date_key."-".$day][$buyer_id]>0){
											?>
											<td  style="height:60px;"  width="45" align="center"  valign="bottom"><div><? if($confirm_total_qty[$p]=="") echo ""; else echo number_format($confirm_total_qty[$p],0); ?></div></td>
											<?
											}
										}
										else
										{
											?>
											<td  style="height:60px;"  width="45" align="center"  valign="bottom"><div><? if($confirm_total_qty[$p]=="") echo ""; else echo number_format($confirm_total_qty[$p],0); ?></div></td>
											<?
										}
                                    }
                                    ?>
                                    <td align="right" style="font-weight:bold;">&nbsp;</td>
                                </tr>
                            </tbody>
                        </table>
						<?
					}
					else if($is_confirm_id==2)//project start here
					{
						echo "Projected :";
						?>
						<br />
						<table align="center" style="margin-left:400px">
                            <tr>
                                <td>&nbsp; </td> <td>&nbsp; </td>
                                <td bgcolor="#00CED1" height="15" width="30"></td>
                                <td>&nbsp;New Job</td>
                                <td bgcolor="#1AA995" height="15" width="30">&nbsp;</td>
                                <td>&nbsp;Updated Job</td>
                            </tr>
						</table>
						<table  width="<? if($all_date_button==""){ echo 2760;}else { echo 3360;} ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                            <thead> 
                                <tr style="font-family:'Arial Narrow'">
                                    <th width="80">Job No</th>
                                    <th width="70" title="counter sample approval">C/S Approval</th>
                                    <th width="80">Order No</th>
                                    <th width="80">Style Ref.</th>
                                    <th width="80">Season</th> 
                                    <th width="100">Dealing Marchant</th>
                                    <th width="80">Fabric (body)</th>
                                    
                                    <th width="80">Fabric Type</th> 
                                    <th width="80">Gmt Item</th>
                                    <th width="80">Embellish. Type</th>
                                    <th width="80">Working Factory</th>
                                    
                                    <th width="40">Avg. SMV</th>
                                    <th width="50">SMV Min.</th>
                                    <th width="35">Price</th>
                                    
                                    <th width="50">No of Color</th>
                                    <th width="70">Booking No</th>
                                    <th width="50">Grey Cons.</th>
                                    <th width="70">Grey Fab (Kg)</th>
                                    <th width="50">Fin. Cons.</th>
                                    <th width="70">Fin. Fab (Kg)</th>
                                    
                                    <th width="70">PO Receive Date</th>
                                    <th width="50">Week</th>
                                    <th width="70">Order Qty</th>
                                    <th width="70">Order Qty (Pcs)</th>
                                    <th width="70">C.Month Total</th>
                                    <th width="70">C.Month Bal</th>
                                    <th width="60">Ship mode</th>
                                    <th width="70">Sewing</th>
                                   
                                    <?
                                    for($m=1;$m<=$num_days;$m++)
                                    {
										$day=($m<=9)? '0'.$m:$m;
										//echo $project_qty_total[$m];
										if($all_date_button=="")
										{
											if($day_total_proj[$date_key."-".$day]>0){
												if($m==$num_days)
												{
													?>
													<th width="45"><? echo  ($m<=9)? '0'.$m:$m; ?></th>
													<?
												}
												else
												{
													?>
													<th width="45"><? echo  ($m<=9)? '0'.$m:$m; ?></th>
													<?
												}
											}
										}
										else
										{
											if($m==$num_days)
											{
												?>
												<th width="45"><? echo  ($m<=9)? '0'.$m:$m; ?></th>
												<?
											}
											else
											{
												?>
												<th width="45"><? echo  ($m<=9)? '0'.$m:$m; ?></th>
												<?
											}
										}
                                    }
                                    ?>
                                    <th>Remarks</th>
                                </tr>
							</thead>
                            <tbody>
                            <?
                            //($m<=9)? '0'.$m:$m.$month_value[1].$month_value[0];
                            $m=1;$total_val="";$total_buyer_qty="";$confirm_total_qty_project=array();
                            $total_grey_qty_proj=0;	$total_finish_qty_proj=0;$total_proj_sewing_output=0;
                            foreach($order_id_arr as $order_id=>$row)
                            {
								if ($m%2==0) $bgcolors="#E9F3FF"; else $bgcolors="#FFFFFF";
								//$confirm_total_qty =array();
								$dzn_qnty=0;
								$costing_per_id=$costing_per_arr[$row["job_no"]];
								if($costing_per_id==1) $dzn_qnty=12;
								else if($costing_per_id==3) $dzn_qnty=12*2;
								else if($costing_per_id==4) $dzn_qnty=12*3;
								else if($costing_per_id==5) $dzn_qnty=12*4;
								else $dzn_qnty=1;
								$dzn_qnty=$dzn_qnty*$row['ratio'];
								$order_qty_pcs=$row["po_qty"]*$row['ratio'];
								$update_date_arr=explode(" ",$row["update_date"]);
								$insert_date_arr=explode(" ",$row["insert_date"]);
								$update_date=date("d-m-Y",strtotime($update_date_arr[0]));
								$insert_date=date("d-m-Y",strtotime($insert_date_arr[0]));
								$today_date=date("d-m-Y");//date("d-m-Y");
								
								if($update_date=='' || $update_date=='01-01-1970') $date_diff_insert=datediff( "d", $insert_date , $today_date);
								if($update_date!='' || $update_date!='01-01-1970') $date_diff_update=datediff( "d", $update_date , $today_date);
								
								if($date_diff_insert<=3) $bg_colors="#00CED1";	
								else if($date_diff_update<=3)  $bg_colors="#1AA995";	
								else $bg_colors=$bgcolors;
								
								//$fab_grey_knit_proj=($fabric_qty_arr['knit']['grey'][$row["po_break_down_id"]]/$order_qty_pcs)*$dzn_qnty;
								//$fab_grey_woven_proj=($fabric_qty_arr['woven']['grey'][$row["po_break_down_id"]]/$order_qty_pcs)*$dzn_qnty;
								$fab_grey_cons_proj=$fab_grey_cons_arr[$row["job_no"]]['fab_knit_req_kg'];//$fab_grey_knit_proj+$fab_grey_woven_proj;
								$total_grey_cons_proj+=$fab_grey_cons_proj;//+$total_grey_cons_proj
								
								
								$fab_grey_knit_proj=($fabric_qty_arr['knit']['grey'][$row["po_break_down_id"]]);
								$fab_grey_woven_proj=($fabric_qty_arr['woven']['grey'][$row["po_break_down_id"]]);
								$fab_grey_qty_proj=$fab_grey_knit_proj+$fab_grey_woven_proj;
								$total_grey_qty_proj+=$fab_grey_qty_proj;
								
								$fab_fin_cons_proj=$fab_grey_cons_arr[$row["job_no"]]['fab_knit_fin_req_kg'];//$fab_grey_knit+$fab_grey_woven;
								$total_fin_qty_proj+=$fab_fin_cons_proj;
								
								$fab_finish_knit_proj=$fabric_qty_arr['knit']['finish'][$row["po_break_down_id"]];
								$fab_finish_woven_proj=$fabric_qty_arr['woven']['finish'][$row["po_break_down_id"]];
								$fab_finish_cons_proj=$fab_finish_knit_proj+$fab_finish_woven_proj;
								$total_finish_qty_proj+=$fab_finish_knit_proj+$fab_finish_woven_proj;
								
								$prod_source=$sewing_outCompany_arr[$row["po_break_down_id"]]['prod_source'];
								$serving_company=$sewing_outCompany_arr[$row["po_break_down_id"]]['serving_company'];
								if($prod_source==1) $sewing_company=$company_library[$serving_company];
								else $sewing_company=$supplier_name_arr[$serving_company];
								$item_category=$fab_booking_arr[$row["po_break_down_id"]]['item_category'];
								$fabric_source=$fab_booking_arr[$row["po_break_down_id"]]['fabric_source'];
								$is_approved=$fab_booking_arr[$row["po_break_down_id"]]['is_approved'];
								$booking=$fab_booking_arr[$row["po_break_down_id"]]['booking_no'];
								$print_report_id=$fab_booking_button_arr2[$row["company_name"]]['print'];
								//echo $print_report_id."F=".$row["company_name"];die;
								 $partial_fb_print_report_id=$fab_booking_button_arr[$row["company_name"]]['partial_fb_print'];
								// echo  $partial_fb_print_report_id.'ddd';die;
								//$format_ids=explode(",",$print_report_id);
								$variable=$fab_booking_type_arr[$row["po_break_down_id"]];
							//echo $fab_booking_type_arr[$row["po_break_down_id"]].'='.$booking.'='.$partial_fb_print_report_id.',';
								if($booking)
								{
									if($fab_booking_type_arr[$row["po_break_down_id"]]=='p'){
										$format_ids=explode(",",$partial_fb_print_report_id);
										$row_id=$format_ids[0];
										if($row_id)
										{
											//echo $row_id.'A,';
											$variable='';
											if($row_id==220)
											{ 
												$variable="<a href='#' onClick=\"generate_worder_report('".$booking."','".$row["company_name"]."','".$row["po_break_down_id"]."','".$item_category."','".$fabric_source."','".$row["job_no"]."','".$is_approved."','print_booking_northern_new','".$k."','".$fab_booking_type_arr[$row["po_break_down_id"]]."')\"> ".$fab_booking_arr[$row["po_break_down_id"]]['booking_no_pre']." <a/>";
											}
											
										}
										
									}
									else
									{
										$format_idsArr=explode(",",$print_report_id);
										$row_id2=$format_idsArr[0];
										//$format_idsArr
									if($row_id2)
									{
										//echo  $row_id2.'B,';
										$variable='';
										if($row_id2==1)
										{ 
											$variable="<a href='#' onClick=\"generate_worder_report('".$booking."','".$row["company_name"]."','".$row["po_break_down_id"]."','".$item_category."','".$fabric_source."','".$row["job_no"]."','".$is_approved."','show_fabric_booking_report_gr','".$k."')\"> ".$fab_booking_arr[$row["po_break_down_id"]]['booking_no_pre']." <a/>";
										}
										if($row_id2==2)
										{ 
											$variable="<a href='#' onClick=\"generate_worder_report('".$booking."','".$row["company_name"]."','".$row["po_break_down_id"]."','".$item_category."','".$fabric_source."','".$row["job_no"]."','".$is_approved."','show_fabric_booking_report','".$k."')\"> ".$fab_booking_arr[$row["po_break_down_id"]]['booking_no_pre']." <a/>";
										}
										 if($row_id2==3)
										{ 
											$variable="<a href='#' onClick=\"generate_worder_report('".$booking."','".$row["company_name"]."','".$row["po_break_down_id"]."','".$item_category."','".$fabric_source."','". $row["job_no"]."','".$is_approved."','show_fabric_booking_report3','".$k."')\"> ".$fab_booking_arr[$row["po_break_down_id"]]['booking_no_pre']." <a/>";
										}
										if($row_id2==4)
										{ 
											$variable="<a href='#' onClick=\"generate_worder_report('".$booking."','".$row["company_name"]."','".$row["po_break_down_id"]."','".$item_category."','".$fabric_source."','". $row["job_no"]."','".$is_approved."','show_fabric_booking_report1','".$k."')\"> ".$fab_booking_arr[$row["po_break_down_id"]]['booking_no_pre']." <a/>";
										}
										if($row_id2==5)
										{ 
											$variable="<a href='#' onClick=\"generate_worder_report('".$booking."','".$row["company_name"]."','".$row["po_break_down_id"]."','".$item_category."','".$fabric_source."','". $row["job_no"]."','".$is_approved."','show_fabric_booking_report2','".$k."')\"> ".$fab_booking_arr[$row["po_break_down_id"]]['booking_no_pre']." <a/>";
										}
										if($row_id2==6)
										{ 
											$variable="<a href='#' onClick=\"generate_worder_report('".$booking."','".$row["company_name"]."','".$row["po_break_down_id"]."','".$item_category."','".$fabric_source."','". $row["job_no"]."','".$is_approved."','show_fabric_booking_report4','".$k."')\"> ".$fab_booking_arr[$row["po_break_down_id"]]['booking_no_pre']." <a/>";
										}
										if($row_id2==7)
										{ 
											$variable="<a href='#' onClick=\"generate_worder_report('".$booking."','".$row["company_name"]."','".$row["po_break_down_id"]."','".$item_category."','".$fabric_source."','". $row["job_no"]."','".$is_approved."','show_fabric_booking_report5','".$k."')\"> ".$fab_booking_arr[$row["po_break_down_id"]]['booking_no_pre']." <a/>";
										}
										//else $variable=$fab_booking_arr[$row["po_break_down_id"]]['booking_no_pre'];
									 }
									}
								}
								else
								{
									$variable=$booking;	
								}//Booking end
                            
                            $c_tot_exfact_qty_proj=$sewing_outQty=0;
                            for($p=1;$p<=$num_days;$p++)
                            {
								$day=($p<=9)? '0'.$p:$p;
								if($shipment_status_id==2) //Balance Qty
								{
									
									if($day_total_proj[$date_key."-".$day]>0)//sewing_outQty
									{
										$c_tot_exfact_qty_proj+=$ex_fact_proj_arr[$date_key][$buyer_id][$order_id][$date_key."-".$day];				
									}
									$proj_tot_bal_qty=$row["po_qty_pcs"]-$c_tot_exfact_qty_proj;
									if($proj_tot_bal_qty<0) $proj_days_qty=0;else $proj_days_qty=$proj_tot_bal_qty;
								}
								else
								{
									$proj_days_qty=$row["po_qty_pcs"]-$c_tot_exfact_qty_proj;
								}
								$sewing_outQty+=$sewing_outCompany_bal_arr[$date_key][$buyer_id][$order_id][$date_key."-".$day];
                            }
                            if(!$proj_days_qty)continue;
                            ?>
                            <tr style="font-family:'Arial Narrow'" bgcolor="<? echo $bg_colors; ?>" onClick="change_color('tr_<? echo $x; ?>','<? echo $bgcolors;?>')" id="tr_<? echo $x; ?>">
                                <td><p><? echo $row["job_no"];?></p></td>
                                <td><p><? echo $sample_arr[$order_id]; ?></p></td>
                                <td><p><? echo $row["po_number"];?></p></td>
                                <td><p><? echo $row["style_ref_no"];?></p></td>
                                <td><p><? echo $season_arr[$row["season"]];?></p></td>
                                <td><p><? echo $dealing_marchant_arr[$row["dealing_marchant"]];?></p></td>
                                <td><p><? echo $body_part_arr[$row["po_break_down_id"]]; ?></p></td>
                                <td><p><?  $color_type_id=array_unique(explode(",",$fab_color_type_arr[$row["job_no"]]['color_type_id'])); $color_id='';
									foreach($color_type_id as $c_id)
									{
										if($color_id=="") $color_id=$color_type[$c_id]; else $color_id.=", ".$color_type[$c_id];
									}
									echo $color_id;
									?></p></td>
                                <td><p><? $gmts_item=''; $gmts_item_id=explode(",",$row["gmts_item_id"]);
								$smv_min=0;
									foreach($gmts_item_id as $item_id)
									{
										$item_smv=0; $item_qty=0; $temp_smv_min=0;
										$item_smv=$item_smv=$smv_arr[$row["job_no"]][$item_id];
										$item_qty=$item_wise_qty_arr[$order_id][$item_id];
										$temp_smv_min=$item_smv*$item_qty;
										$smv_min+=$temp_smv_min;
										if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=", ".$garments_item[$item_id];
									}
									$avg_smv=0;
									//$avg_smv=$smv_min/$row["po_qty_pcs"]; //old
									$avg_smv=$row['set_smv']/$row['ratio']; //new
									$smv_min=$avg_smv*$c_days_qty; //new
									echo $gmts_item; ?></p></td>
                                <td><p><?
									$embl_name_id=array_unique(explode(",",$embl_fab_type_arr[$row["job_no"]]['emb_name'])); $emble_names='';
									foreach($embl_name_id as $emb_id)
									{
										if($emble_names=="") $emble_names=$emblishment_name_array[$emb_id]; else $emble_names.=", ".$emblishment_name_array[$emb_id];
									}
									echo $emble_names;
									?></p></td>
                                <td><p><? echo $company_library[$sewing_outCompany_arr[$row["po_break_down_id"]]['company_id']];?></p></td>
                                <td align="center">
                                    <p>
                                        <a href="##" onClick="openmypage_smv('<? echo $row["job_no"];?>',<? echo $row["order_uom"];?>,<? echo $order_id;?>)" ><? echo number_format($avg_smv,3);?> </a>
                                    </p>
                                </td>
                                <td align="right"><p><? echo $smv_min;?></p></td>
                                <td align="center"><p><? echo $row["unit_price"]?></p></td>
                                <td align="center"><p><? echo $sql_color_no_arr[$row["po_break_down_id"]]; ?></p></td>
                                <td><p><?  echo $variable;?></p></td>
                                
                                <td align="center"><p><? echo number_format($fab_grey_cons_proj,2);?></p></td>
                                <td align="right"><p><? echo number_format($fab_grey_qty_proj,2);?></p></td>
                                <td align="center"><p><? echo number_format($fab_fin_cons_proj,2);?></p></td>
                                <td align="right"><p><? echo number_format($fab_finish_cons_proj,2);?></p></td>
                                
                                <td align="center"><p><? if($row["po_received_date"]!="" && $row["po_received_date"]!='0000-00-00') echo change_date_format($row["po_received_date"]); ?></p></td>
                                <td align="center"><p><? if($row["po_received_date"]!="" && $row["po_received_date"]!='0000-00-00') echo $weak_of_year[$row["po_received_date"]]; ?></p></td>
                                <td align="right"><p><? echo number_format($po_qty_arr[$order_id]["po_qty"],0); $total_ordset_qty_proj +=$po_qty_arr[$order_id]["po_qty"]; ?></p></td>
                                <td align="right"><p><? echo number_format($total_pcs_arr[$row["po_break_down_id"]],0);  $total_ord_proj_qty +=$total_pcs_arr[$row["po_break_down_id"]]; ?></p></td>
                                <td align="right"><p><? echo number_format($row["po_qty_pcs"],0); $total_c_month_proj_qty +=$row["po_qty_pcs"]; ?></p></td>
                                <td align="right" title="<? echo $c_tot_exfact_qty_proj;?>"><p><? echo number_format($proj_days_qty,0); $total_month_proj_qty +=$proj_days_qty; ?></p></td>
                                <td><p><? echo $shipment_mode[$row["ship_mode"]]; ?></p></td>
                                <td align="right"><p><? echo $sewing_outQty;//$sewing_company; ?></p></td>
                               
                                <?
                                $exfact_qty_proj=0;
                                for($p=1;$p<=$num_days;$p++)
                                {
									$day=($p<=9)? '0'.$p:$p;
									if($shipment_status_id==2) //Balance Qty
									{
										$exfact_qty_proj=$ex_fact_proj_arr[$date_key][$buyer_id][$order_id][$date_key."-".$day];
										$proj_tot_bal_qty=$row_data_dtls_project[$date_key][$buyer_id][$order_id][$date_key."-".$day]-$exfact_qty_proj;
										if($proj_tot_bal_qty<0) $proj_days_qty=0;else $proj_days_qty=$proj_tot_bal_qty;
									}
									else
									{
										$proj_days_qty=$row_data_dtls_project[$date_key][$buyer_id][$order_id][$date_key."-".$day]-$exfact_qty_proj;
									}
									if($all_date_button=="")
									{
										if($day_total_proj[$date_key."-".$day]>0)
										{
											?>
											<td style="height:60px;" title="<? echo $exfact_qty_proj;?>"  align="center"  valign="bottom"><div><? if($proj_days_qty=="") echo ""; else echo number_format($proj_days_qty,0); ?></div></td>
											<?
											$confirm_total_qty_project[$p] +=$proj_days_qty;
											$confirm_project_buyer_total[$buyer_id][$p] +=$proj_days_qty;
										}
									}
									else
									{
										?>
										<td style="height:60px;" title="<? echo $exfact_qty_proj;?>"  align="center"  valign="bottom"><div><? if($proj_days_qty=="") echo ""; else echo number_format($proj_days_qty,0); ?></div></td>
										<?
										$confirm_total_qty_project[$p] +=$proj_days_qty;
										$confirm_project_buyer_total[$buyer_id][$p] +=$proj_days_qty;
									}
								}
							?>
                                <td><p><? echo $row["details_remarks"];?></p></td>
                            </tr>
                            <?
							$m++; $x++;
							$total_proj_sewing_output+=$sewing_outQty;//$sewing_outCompany_arr[$row["po_break_down_id"]]['prod_qty'];
						}
						?>
						<tr style="font-family:'Arial Narrow'" bgcolor="#CCCCCC">
                            <td colspan="16"  align="right" style="font-weight:bold;">Projected Total:</td>
                            <td align="right" style="font-weight:bold;"><? //$buyer_total_order_gery[$buyer_id] +=$total_grey_qty_proj; $total_grey_qty_proj=''; ?></td>
                            <td align="right" style="font-weight:bold;"><? echo number_format($total_grey_qty_proj,2); $buyer_total_order_grey[$buyer_id]+=$total_grey_qty_proj; $total_grey_qty_proj=''; ?></td>
                            <td align="right" style="font-weight:bold;"><? //$buyer_total_order_gery[$buyer_id] +=$total_grey_qty_proj; $total_grey_qty_proj=''; ?></td>
                            <td align="right" style="font-weight:bold;"><? echo number_format($total_finish_qty_proj,2); $buyer_total_order_finish[$buyer_id] +=$total_finish_qty_proj; $total_finish_qty_proj='';  ?></td>
                            <td align="right" style="font-weight:bold;">&nbsp;</td>
                            <td align="right" style="font-weight:bold;">&nbsp;</td>
                            <td  align="right" style="font-weight:bold;"><p><? echo number_format($total_ordset_qty_proj,0);  $buyer_totalset_order [$buyer_id] +=$total_ordset_qty_proj;  $total_ordset_qty_proj=''; ?></p></td>
                            <td align="right" style="font-weight:bold;"><p><? echo number_format($total_ord_proj_qty,0);  $buyer_total_order [$buyer_id] +=$total_ord_proj_qty;  $total_ord_proj_qty=''; ?></p></td>

                            <td align="right" style="font-weight:bold;"><p><? echo number_format($total_c_month_proj_qty,0);  $buyer_total_c_month[$buyer_id] +=$total_c_month_proj_qty;  $total_c_month_proj_qty=''; ?></p></td>

                            <td align="right" style="font-weight:bold;"><p><? echo number_format($total_month_proj_qty,0);  $buyer_total_month [$buyer_id] +=$total_month_proj_qty; $total_month_proj_qty=''; ?></p></td>
                            <td align="right" style="font-weight:bold;">&nbsp;</td>
                            <td align="right" style="font-weight:bold;"><? echo number_format($total_proj_sewing_outpu,2);?></td>
                            
                            <?
                            for($p=1;$p<=$num_days;$p++)
                            {
								$day=($p<=9)? '0'.$p:$p;
								if($all_date_button=="")
								{
									if($day_total_proj[$date_key."-".$day]>0)
									{
										?>
											<td  style="height:20px;" width="45" align="center"><div style="word-break:break-all"><? echo number_format($confirm_total_qty_project[$p],0); ?></div></td>
										<?
										$project_qty_total[$p]+=$confirm_total_qty_project[$p];
										//$total_val[]
									}
								}
								else
								{
									?>
										<td  style="height:20px;" width="45" align="center"><div style="word-break:break-all"><? echo number_format($confirm_total_qty_project[$p],0); ?></div></td>
									<?
									$project_qty_total[$p]+=$confirm_total_qty_project[$p];
									//$total_val[]
								}
                            }
                            ?>
                            <td align="right"  style="font-weight:bold;"><? //echo $project_qty_total;?>&nbsp;</td>
						</tr>
                    </tbody>
                  		</table>
				<?
                }
			}
			//print_r($buyer_total_order_grey);
			?>
            
            
            <table  width="<? if($all_date_button==""){ echo 2690;}else { echo 3290;} ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" >
                <tr bgcolor="#FFFFCC" style="font-family:'Arial Narrow';" >
                    <td width="80">&nbsp;</td>
                    <td width="70">&nbsp;</td>
                    <td width="80">&nbsp;</td>
                    <td width="80">&nbsp;</td>
                    <td width="80">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="80">&nbsp;</td>
                    <td width="80">&nbsp;</td> 
                    <td width="80">&nbsp;</td>
                    <td width="80">&nbsp;</td>
                    <td width="80">&nbsp;</td>
                    <td width="40">&nbsp;</td>
                    <td width="50">&nbsp;</td>
                    <td width="35">&nbsp;</td>
                    <td width="50">&nbsp;</td>
                    <td width="70"><p>Buyer Total:</p></td>
                    <td width="50">&nbsp;</td>
                    <td align="right" width="70"><p><? echo number_format($buyer_total_order_grey[$buyer_id],0); $grand_grey_kg+=$buyer_total_order_grey[$buyer_id]; ?></p></td>
                    <td width="50">&nbsp;</td>
                    <td align="right" width="70"><p><? echo number_format($buyer_total_order_finish[$buyer_id],0); $grand_fin_kg+=$buyer_total_order_finish[$buyer_id]; ?></p></td>
                    <td width="70">&nbsp;</td>
                    <td width="50">&nbsp;</td>
                    <td align="right" width="70" style="font-weight:bold;"><p><? echo number_format($buyer_totalset_order [$buyer_id],0); $grand_orderset_qty+=$buyer_totalset_order [$buyer_id]; ?></p></td>

                    <td align="right" width="70" style="font-weight:bold;"><p><? echo number_format($buyer_total_order [$buyer_id],0); $grand_order_qty+=$buyer_total_order [$buyer_id]; ?></p></td>

                    <td align="right" width="70" style="font-weight:bold;"><p><? echo number_format($buyer_total_c_month [$buyer_id],0); $grand_c_month_qty+=$buyer_total_c_month[$buyer_id]; ?></p></td>

                    <td align="right" width="70" style="font-weight:bold;"><p><? echo number_format($buyer_total_month [$buyer_id],0); $grand_month_qty+=$buyer_total_month [$buyer_id]; ?></p></td>
                    <td align="right" width="60" style="font-weight:bold;">&nbsp;</td>
                    <td align="right" width="70" style="font-weight:bold;">&nbsp;</td>
                   
                   
                    <?
                    for($m=1;$m<=$num_days;$m++)
                    {
                        $day=($m<=9)? '0'.$m:$m;
                        if($is_confirm_id==1) $ship_status_total=$day_total_con[$date_key."-".$day][$buyer_id];//Confirm
                        else $ship_status_total=$day_total_proj[$date_key."-".$day][$buyer_id];
                        
						if($all_date_button=="")
						{
							if($ship_status_total>0)
							{
								if($m==$num_days)
								{
									?>
									<td style="height:20px;"  align="center"  width="45"><div style="word-break:break-all"><p><? echo number_format($confirm_project_buyer_total[$buyer_id][$m],0);  ?></p></div></td>
									<?
									$grand_day_qty_arr[$m]+=$confirm_project_buyer_total[$buyer_id][$m];
								}
								else
								{
									?>
									<td  style="height:20px;"  align="center"   width="45"><div style="word-break:break-all"><p><? echo number_format($confirm_project_buyer_total[$buyer_id][$m],0);  ?></p></div></td>
									<?
									$grand_day_qty_arr[$m]+=$confirm_project_buyer_total[$buyer_id][$m];
								}
							}
						}
						else
						{
							if($m==$num_days)
							{
								?>
								<td style="height:60px;"  align="center"  width="45"><div style="word-break:break-all"><p><? echo number_format($confirm_project_buyer_total[$buyer_id][$m],0);  ?></p></div></td>
								<?
								$grand_day_qty_arr[$m]+=$confirm_project_buyer_total[$buyer_id][$m];
							}
							else
							{
								?>
								<td  style="height:60px;"  align="center"   width="45"><div style="word-break:break-all"><p><? echo number_format($confirm_project_buyer_total[$buyer_id][$m],0);  ?></p></div></td>
								<?
								$grand_day_qty_arr[$m]+=$confirm_project_buyer_total[$buyer_id][$m];
							}
								
						}
                    }
                    ?>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                </tr>
            </table>
			
			<?
			//buyer loop end
		}
	}
	?>
    <table  width="<? if($all_date_button==""){ echo 2760;}else { echo 3360;} ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" >
        <tr bgcolor="#CCCCFF" style="font-family:'Arial Narrow';" >
            <td width="80">&nbsp;</td>
            <td width="70">&nbsp;</td>
            <td width="80">&nbsp;</td>
            <td width="80">&nbsp;</td>
            <td width="80">&nbsp;</td>
            <td width="100">&nbsp;</td>
            <td width="80">&nbsp;</td>
            <td width="80">&nbsp;</td> 
            <td width="80">&nbsp;</td>
            <td width="80">&nbsp;</td>
            <td width="80">&nbsp;</td>
            <td width="40">&nbsp;</td>
            <td width="50">&nbsp;</td>
            <td width="35">&nbsp;</td>
            <td width="50">&nbsp;</td>
            <td width="70"><p>Grand Total:</p></td>
            <td width="50">&nbsp;</td>
            <td align="right" width="70"><p><? echo number_format($grand_grey_kg,0); ?></p></td>
            <td width="50">&nbsp;</td>
            <td align="right" width="70"><p><? echo number_format($grand_fin_kg,0); ?></p></td>
            <td width="70">&nbsp;</td>
            <td width="50">&nbsp;</td>
             <td align="right" width="70" style="font-weight:bold;"><p><? echo number_format($grand_orderset_qty,0); ?></p></td>
            <td align="right" width="70" style="font-weight:bold;"><p><? echo number_format($grand_order_qty,0); ?></p></td>

            <td align="right" width="70" style="font-weight:bold;"><p><? echo number_format($grand_c_month_qty,0); ?></p></td>
            <td align="right" width="70" style="font-weight:bold;"><p><? echo number_format($grand_month_qty,0); ?></p></td>
            <td align="right" width="60" style="font-weight:bold;">&nbsp;</td>
            <td align="right" width="70" style="font-weight:bold;">&nbsp;</td>
           
            <?
            for($m=1;$m<=$num_days;$m++)
            {
                $day=($m<=9)? '0'.$m:$m;
                if($is_confirm_id==1) $ship_status_total=$day_total_con[$date_key."-".$day][$buyer_id];//Confirm
                else $ship_status_total=$day_total_proj[$date_key."-".$day][$buyer_id];
                
				if($all_date_button=="")
				{
					if($ship_status_total>0)
					{
						if($m==$num_days)
						{
							?>
							<td style="height:20px;" align="center" width="45"><div style="word-break:break-all"><p><? //echo number_format($grand_day_qty_arr[$m],0); ?></p></div></td>
							<?
						}
						else
						{
							?>
							<td style="height:20px;" align="center" width="45"><div style="word-break:break-all"><p><? //echo number_format($grand_day_qty_arr[$m],0); ?></p></div></td>
							<?
						}
					}
				}
				else
				{
					if($m==$num_days)
					{
						?>
						<td style="height:60px;" align="center" width="45"><div style="word-break:break-all"><p><? echo number_format($grand_day_qty_arr[$m],0); ?></p></div></td>
						<?
					}
					else
					{
						?>
						<td style="height:60px;" align="center" width="45"><div style="word-break:break-all"><p><? echo number_format($grand_day_qty_arr[$m],0); ?></p></div></td>
						<?
					}
				}
            }
            ?>
            <td>&nbsp;</td>
        </tr>
    </table>
    
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
	echo "$total_data****$filename";
	exit();
	//disconnect($con);
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	//echo $cbo_year_from;die;

	$company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_date_category=str_replace("'","",$cbo_date_category);
	$shipment_status_id=str_replace("'","",$cbo_shipment_status);
	$season=str_replace("'","",$txt_season);
	$cbo_report_type=str_replace("'","",$cbo_report_type);
	$order_status_id=str_replace("'","",$cbo_order_status);
	$dealing_merchant_id=str_replace("'","",$cbo_dealing_merchant);
	$all_date_button=$all_date;
	//dealing_marchant
	if($dealing_merchant_id>0) $marchd_cond="and a.dealing_marchant in($dealing_merchant_id)";
	else $marchd_cond="";
	$order_status_cond='';
	if($order_status_id==0)
	{
		$order_status_cond=" and c.is_confirmed in(1,2)";
	}
	else if($order_status_id!=0)
	{
		$order_status_cond=" and c.is_confirmed=$order_status_id";	
	}
	$shipment_status_cond='';
	if($shipment_status_id==1) // Running Full Order Qty
	{
		$shipment_status_cond=" and a.status_active=1 and b.status_active=1 and c.status_active=1";
	}
	else if($shipment_status_id==2) //Running Order Balance Qty
	{
		$shipment_status_cond=" and a.status_active=1 and b.status_active=1 and c.status_active=1 and c.shiping_status <> 3";	
		$shipment_status_cond2=" and c.status_active=1 and a.status_active=1 and d.status_active=1 and c.shiping_status <> 3";	
		
		//$date_cond2=" and b.country_ship_date between '".date("j-M-Y",strtotime(str_replace("'","",$start_date)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$end_date)))."'";
	}
	else if($shipment_status_id==3) //Fully Shipped
	{
		$shipment_status_cond="and c.shiping_status=$shipment_status_id  and a.status_active=1 and b.status_active=1 and c.status_active=1 ";	
	}
	else if($shipment_status_id==4) //Cancelled Order
	{
		$shipment_status_cond=" and a.status_active=1  and c.status_active=3";	
	}
	
	if($season=="") $season_cond=""; else $season_cond=" and a.season_buyer_wise in('".implode("','",explode(",",$season))."')";

	//echo $cbo_buyer_name;die;
	if($cbo_buyer_name!=0) $buyer_cond=" and a.buyer_name='$cbo_buyer_name'"; else $buyer_cond="";
	if($company_name!=0) $company_con=" and a.company_name='$company_name'"; else $company_con="";
	$date_cond='';
	if(str_replace("'","",$cbo_year_from)!=0 && str_replace("'","",$cbo_month_from)!=0)
	{
		$start_year=str_replace("'","",$cbo_year_from);
		$start_month=str_replace("'","",$cbo_month_from);
		$start_date=$start_year."-".$start_month."-01";
		
		$end_year=str_replace("'","",$cbo_year_to);
		$end_month=str_replace("'","",$cbo_month_to);
		$num_days = cal_days_in_month(CAL_GREGORIAN, $end_month, $end_year);
		$end_date=$end_year."-".$end_month."-$num_days";
		$month_order_by="";
		if($cbo_date_category==1)
		{
			if($db_type==0) 
			{
				$date_cond=" and b.country_ship_date between '$start_date' and '$end_date'";
				$date_cond2=" and d.country_ship_date between '$start_date' and '$end_date'";
				$order_by_cond="DATE_FORMAT(b.country_ship_date, '%Y%m')";
				$month_order_by.=" b.country_ship_date asc,";
				
			}
			if($db_type==2) 
			{
				$date_cond=" and b.country_ship_date between '".date("j-M-Y",strtotime(str_replace("'","",$start_date)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$end_date)))."'";
				$date_cond2=" and d.country_ship_date between '".date("j-M-Y",strtotime(str_replace("'","",$start_date)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$end_date)))."'";
				$order_by_cond="to_char(b.country_ship_date,'YYYY-MM')";
				$month_order_by.=" b.country_ship_date asc,";
			}
		}
		else if($cbo_date_category==2) //Cut-Off
		{
			if($db_type==0) 
			{
				$date_cond=" and b.cutup_date between '$start_date' and '$end_date'";
				$date_cond2=" and b.cutup_date between '$start_date' and '$end_date'";
				$order_by_cond="DATE_FORMAT(b.cutup_date, '%Y%m')";
				$month_order_by.=" b.cutup_date asc,";
				
			}
			if($db_type==2) 
			{
				$date_cond=" and b.cutup_date between '".date("j-M-Y",strtotime(str_replace("'","",$start_date)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$end_date)))."'";
				$date_cond2=" and d.cutup_date between '".date("j-M-Y",strtotime(str_replace("'","",$start_date)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$end_date)))."'";
				$order_by_cond="to_char(b.cutup_date,'YYYY-MM')";
				$month_order_by.=" b.cutup_date asc,";
			}
		}
	}
	if($shipment_status_id==2) //Running Order Balance Qty
		{
			
			
			$sql_exf_c_date="SELECT d.id as color_break_id,a.buyer_name,a.dealing_marchant,d.po_break_down_id as po_id,d.country_ship_date as country_ship_date,d.cutup_date, c.is_confirmed
			 from wo_po_break_down c,wo_po_details_master a,wo_po_color_size_breakdown d  where  c.job_no_mst=a.job_no and  d.status_active=1 and d.is_deleted=0 and c.id=d.po_break_down_id  and  a.job_no=d.job_no_mst  and c.status_active=1 and c.is_deleted=0   $company_con  $marchd_cond  $buyer_cond $season_cond $date_cond2";
			$result_c=sql_select($sql_exf_c_date);
			foreach($result_c as $row)
			{
				if($cbo_date_category==1)
				{
					$row[csf("country_ship_date")]=$row[csf("country_ship_date")];
				}
				else //Cut-Off
				{
					$row[csf("country_ship_date")]=$row[csf("cutup_date")];	
				}
				
				if($row[csf("is_confirmed")]==1)
				{
				$c_date_con_arr[$row[csf("buyer_name")]][$row[csf("po_id")]][$row[csf("color_break_id")]]['country_date']=$row[csf("country_ship_date")];
				$c_date_con_arr[$row[csf("buyer_name")]][$row[csf("po_id")]][$row[csf("color_break_id")]]['cut_date']=$row[csf("country_ship_date")];
				}
				else
				{
				$c_date_con_arr[$row[csf("buyer_name")]][$row[csf("po_id")]][$row[csf("color_break_id")]]['country_date']=$row[csf("country_ship_date")];
				$c_date_con_arr[$row[csf("buyer_name")]][$row[csf("po_id")]][$row[csf("color_break_id")]]['cut_date']=$row[csf("country_ship_date")];
				}
			}
			
			 
		
			
			
		}
		
	 $data_array="SELECT a.id as job_id, a.job_no,  a.company_name, a.buyer_name,a.dealing_marchant,a.total_set_qnty,b.id as color_break_id,b.po_break_down_id,b.order_quantity as po_quantity,b.order_quantity as po_quantity_pcs,c.po_quantity as po_qty,b.country_ship_date,b.cutup_date,b.order_total,c.is_confirmed
			from wo_po_details_master a, wo_po_color_size_breakdown b,wo_po_break_down c  where	a.job_no=b.job_no_mst and b.po_break_down_id=c.id  and b.status_active=1 and b.is_deleted=0  $company_con $date_cond $buyer_cond $order_status_cond $shipment_status_cond $season_cond $marchd_cond order by	$month_order_by a.buyer_name ASC";
 	$po_id_array=array();
	$result_po=sql_select($data_array);
	$row_data_dtls=array();
	$tmp_arr=array();
		foreach($result_po as $row)
		{	
			$po_id_array[$row[csf("po_break_down_id")]]=$row[csf("po_break_down_id")];
 			if($cbo_date_category==1)
			{
				$row[csf("country_ship_date")]=$row[csf("country_ship_date")];
			}
			else //Cut-Off
			{
				$row[csf("country_ship_date")]=$row[csf("cutup_date")];	
			}
			$tmp_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$row[csf("buyer_name")]][$row[csf("is_confirmed")]]["po_qty"]+=$row[csf("po_quantity_pcs")];
			$tmp_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$row[csf("buyer_name")]][$row[csf("is_confirmed")]]["buyer"] =$row[csf("buyer_name")];
			$tmp_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$row[csf("buyer_name")]][$row[csf("is_confirmed")]]["dealing_marchant"] =$row[csf("dealing_marchant")];
			$tmp_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$row[csf("buyer_name")]][$row[csf("is_confirmed")]]["is_confirmed"] =$row[csf("is_confirmed")];
			$month_wise_total[date("Y-m",strtotime($row[csf("country_ship_date")]))] +=$row[csf("po_quantity_pcs")];
			if($row[csf("is_confirmed")]==1)
			{
				$row_data_dtls[date("Y-m",strtotime($row[csf("country_ship_date")]))][$row[csf("buyer_name")]][date("Y-m-d",strtotime($row[csf("country_ship_date")]))]+=$row[csf("po_quantity_pcs")];
				$day_total_con[date("Y-m-d",strtotime($row[csf("country_ship_date")]))]+=$row[csf("po_quantity_pcs")];
			}
			else if($row[csf("is_confirmed")]==2)
			{
				$row_data_dtls_project[date("Y-m",strtotime($row[csf("country_ship_date")]))][$row[csf("buyer_name")]][date("Y-m-d",strtotime($row[csf("country_ship_date")]))]+=$row[csf("po_quantity_pcs")];
				$day_total_proj[date("Y-m-d",strtotime($row[csf("country_ship_date")]))]+=$row[csf("po_quantity_pcs")];
			}
			
			if($row[csf("is_confirmed")]==1 || $row[csf("is_confirmed")]==2)
			{
			$day_total_cons_all[date("Y-m-d",strtotime($row[csf("country_ship_date")]))]+=$row[csf("po_quantity_pcs")];
			}
			
				
		}
		
		$po_ids=implode(",", $po_id_array);
		$po_ids_cond="";
		if($db_type==2 && count($po_id_array)>999)
		{

			$chnk_arr=array_chunk($po_id_array,999);
			foreach($chnk_arr as $v)
			{
				$val=implode(",",$v);
				if($po_ids_cond=="") $po_ids_cond.= " and ( c.id in ($val) ";

				else $po_ids_cond.=" or   c.id in ($val) ";
			}
			if($po_ids_cond)$po_ids_cond.=")";

		}
		else
		{
			$po_ids_cond=" and c.id in($po_ids)";
		}
		if(!$po_ids) $po_ids=0;
		if($shipment_status_id==2)
		{
			  $sql_exf="SELECT f.country_ship_date, a.buyer_name,b.po_break_down_id as po_id,d.color_size_break_down_id as color_break_id, c.is_confirmed,
			(CASE WHEN b.entry_form!=85 THEN d.production_qnty 	 ELSE 0 END) as ex_fact_qty,
			(CASE WHEN b.entry_form=85 THEN d.production_qnty  ELSE 0 END) as ex_fact_ret_qty
			from wo_po_break_down c,pro_ex_factory_mst b,wo_po_details_master a,pro_ex_factory_dtls d ,wo_po_color_size_breakdown f where c.id=b.po_break_down_id and c.job_no_mst=a.job_no and b.id=d.mst_id and b.po_break_down_id=b.po_break_down_id and f.id=d.color_size_break_down_id and f.po_break_down_id=c.id and  b.status_active=1 and b.is_deleted=0 $po_ids_cond  and  d.status_active=1 and d.is_deleted=0  $company_con  $buyer_cond $season_cond ";
			$result=sql_select($sql_exf);
			$ex_fact_con_arr=array();$ex_fact_proj_arr=array();
			foreach($result as $row)
			{
				if($cbo_date_category==1)
				{
					$row[csf("country_ship_date")]=$c_date_con_arr[$row[csf("buyer_name")]][$row[csf("po_id")]][$row[csf("color_break_id")]]['country_date'];
				}
				else //Cut-Off
				{
					$row[csf("country_ship_date")]=$c_date_con_arr[$row[csf("buyer_name")]][$row[csf("po_id")]][$row[csf("color_break_id")]]['cut_date'];	
				}
				//echo $row[csf("country_ship_date")];
				if($row[csf("is_confirmed")]==1)
				{
					$ex_fact_con_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$row[csf("buyer_name")]][date("Y-m-d",strtotime($row[csf("country_ship_date")]))]+=$row[csf("ex_fact_qty")]-$row[csf("ex_fact_ret_qty")];
				}
				else if($row[csf("is_confirmed")]==2)
				{
					$ex_fact_proj_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$row[csf("buyer_name")]][date("Y-m-d",strtotime($row[csf("country_ship_date")]))]+=$row[csf("ex_fact_qty")]-$row[csf("ex_fact_ret_qty")];
				}
			}
			//print_r($ex_fact_con_arr);

		}
		ob_start();
?>
	
        <table width="1150">
        	<tr class="form_caption">
        		<td colspan="10" width="200"></td>
        		<td colspan="12" align="center" style="font-size:18px;" width="200">
        			<strong>
        				<? 

        				if($company_name)
        				{
        					echo $company_library[$company_name]; 
        				}else
        				{
        					if($company_library[1] == "Metro Knitting and Dyeing Mills Ltd.")
        					{
        						echo $company_library[1]; 
        					}
        				}

        				?>
        			</strong>
        		</td>
        		<td colspan="10" align="right" style="color: black">Generate Date: <? echo date("Y-m-d");?></td>
        	</tr>

        	<tr class="form_caption">
        		<td colspan="10" width="200"></td>
        		<td colspan="12" align="center" style="font-size:12px;" width="200">Monthly Order Summary /
        		 <? echo $shipment_status_arr[$shipment_status_id]."/". $report_type_arr[$cbo_report_type];?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>

        		<td colspan="10" align="right" style="color: black">Generate Time: <? echo date("h:i:sa");?></td>
        	</tr>														
            
        </table>
        
        <?
		foreach($tmp_arr as $date_key=>$buyer_id_arr)
		{
            $month_value=explode("-",$date_key);
			$num_days = cal_days_in_month(CAL_GREGORIAN, $month_value[1], $month_value[0]);
			//echo $num_days;die;
			//arsort($buyer_id_arr);
			//var_dump($date_key);die;
				?>
                <br />
                <table class="rpt_table" width="1150" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <tr bgcolor="#FFFFFF">
                        <td align="right" style="font-weight:bold;" width="84" >Month:</td>
                        <td style="font-weight:bold;" width="85">
                        <?
                        echo $months[$month_value[1]*1]."-".$month_value[0]; 
                        ?>
                        </td>
                        <td  style="font-weight:bold;" width="50" align="right">Total &nbsp; =</td>
                        <td  style="font-weight:bold;" colspan="29">&nbsp;
                        <? 
                            echo number_format($month_wise_total[$date_key],0)." "."Pcs"; 
                        ?>
                        </td>
                    </tr>
                </table>
               <table  width="1150" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                   <thead> 
                        <tr>
                            <th width="80">Buyer</th>
                            <th width="80">Total</th>
                            <th width="80"><? if($shipment_status_id==2) echo "C.Month Balance";else echo "C.Month Total"; ?></th>
                            <?
                            for($m=1;$m<=$num_days;$m++)
                            {
                               $day=($m<=9)? '0'.$m:$m;
							    //echo $m.'='.$day;
								
	  							if($all_date_button==""){	
	  							  if($day_total_cons_all[$date_key."-".$day]>0){
	  								  
	  							    ?>
	                                  <th width="35"><? echo  ($m<=9)? '0'.$m:$m; ?></th>
	                                  <?
	  							   }
								}
								else
								{ 
									?>
	                                  <th width="35"><? echo  ($m<=9)? '0'.$m:$m; ?></th>
	                               <? 
								}
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
					<?
                    //($m<=9)? '0'.$m:$m.$month_value[1].$month_value[0];
					$k=1;$total_val="";$total_buyer_qty="";$total_month_qty=0;
                    foreach($buyer_id_arr as $buyer_id=>$is_confirm)
                    {
						foreach($is_confirm as $row)
						{
							if ($k%2==0)
							$bgcolor="#E9F3FF";
							else
							$bgcolor="#FFFFFF";
							if($row['is_confirmed']==1)
							{
								
								$c_total_exfact_qty_con=0;$tot_c_bal_qty=0; $total_cqty=0;  $total_cqty_po=0;
								for($p=1;$p<=$num_days;$p++)
								{
									$day=($p<=9)? '0'.$p:$p;
									if($shipment_status_id==2) //Balance Qty
										{
											$c_total_exfact_qty_con=$ex_fact_con_arr[$date_key][$buyer_id][$date_key."-".$day];	
											$tot_c_bal_qty=$row_data_dtls[$date_key][$buyer_id][$date_key."-".$day];
											//if($tot_c_bal_qty<0) $c_day_qty=0;else $c_day_qty=$tot_c_bal_qty;
											 $total_cqty_po=$tot_c_bal_qty-$c_total_exfact_qty_con;
											  if($total_cqty_po<0) $totc_day_qty=0;else $totc_day_qty=$total_cqty_po;
										}
										else
										{
											$totc_day_qty=$row_data_dtls[$date_key][$buyer_id][$date_key."-".$day];
										}
									 /* if($day_total_con[$date_key."-".$day]>0 || $day_total_proj[$date_key."-".$day]>0){
										  
										  $total_c_po_qty+=$tot_c_bal_qty;
										  
									  }*/
									   $total_cqty+=$totc_day_qty;
									  
								}
								//echo $tot_c_bal_qty.'='.$c_total_exfact_qty_con;
								$tot_c_mon_qty=$total_cqty;
								if($shipment_status_id==2)
									{
										//if( 0<$tot_c_mon_qty && $ship_status_arr[$row["po_break_down_id"]]!=3)
										//{
							?>
							<tr bgcolor="<? echo $bgcolor ; ?>" style="font-family:'Arial Narrow'">
								<td ><? echo $buyer_library[$row['buyer']]; ?></td>
								<td align="right"><? echo number_format($row['po_qty'],0); $total_buyer_qty+=$row['po_qty']; ?>&nbsp;</td>
                                <td align="right" title="<? echo  'Po Qty='.$tot_c_mon_qtys.', Ship qty '.$c_total_exfact_qty_con;?>" ><p><? echo number_format(($tot_c_mon_qty),0); $total_month_qty +=$tot_c_mon_qty; ?></p></td>
								<?
								$exfact_qty_con=0;$tot_bal_qty=0;
								for($p=1;$p<=$num_days;$p++)
								{
									$day=($p<=9)? '0'.$p:$p;
									if($shipment_status_id==2) //Balance Qty
										{
											$exfact_qty_con=$ex_fact_con_arr[$date_key][$buyer_id][$date_key."-".$day];	
											//echo $date_key.'='.$buyer_id.'='.$date_key."-".$day.'<br>';
											$tot_bal_qty=$row_data_dtls[$date_key][$buyer_id][$date_key."-".$day];
											//if($tot_bal_qty<0) $day_qty=0;else $day_qty=$tot_bal_qty;
										}
										else
										{
											$tot_bal_qty=$row_data_dtls[$date_key][$buyer_id][$date_key."-".$day]-$exfact_qty_con;
										}
										
											if($all_date_button==0)
											{
												if($day_total_con[$date_key."-".$day]>0 || $day_total_proj[$date_key."-".$day]>0){
													
													$day_qtys=$tot_bal_qty-$exfact_qty_con;
													if($day_qtys<0) $day_qty=0;else $day_qty=$day_qtys;
													
													?>
													<td  style="height:20px;" title="<? echo 'PO Qty='.$day_qtys.', Ship Qty'.$exfact_qty_con;?>" width="35" align="center"><div><? if($row_data_dtls[$date_key][$buyer_id][$date_key."-".$day]>0) {if($day_qty=="") echo ""; else echo number_format($day_qty,0);} else echo ""; ?></div></td>
													<?
													$total_val[$day] +=$day_qty;
												}
											}
											else
											{
												$day_qtys=$tot_bal_qty-$exfact_qty_con;
												if($day_qtys<0) $day_qty=0;else $day_qty=$day_qtys;
												
												?>
												<td  style="height:20px;" title="<? echo 'PO Qty='.$day_qtys.', Ship Qty'.$exfact_qty_con;?>" width="35" align="center"><div><? if($row_data_dtls[$date_key][$buyer_id][$date_key."-".$day]>0) {if($day_qty=="") echo ""; else echo number_format($day_qty,0);} else echo ""; ?></div></td>
												<?
												$total_val[$day] +=$day_qty;
												
											}
								}
								
								?>
							</tr>
							<?
										//}
									}
									else 
									{
										?>
							<tr bgcolor="<? echo $bgcolor ; ?>" style="font-family:'Arial Narrow'">
								<td><? echo $buyer_library[$row['buyer']]; ?></td>
								<td align="right"><? echo number_format($row['po_qty'],0); $total_buyer_qty+=$row['po_qty']; ?>&nbsp;</td>
                                <td align="right" title="<? echo  'Po Qty='.$tot_c_mon_qtys.', Ship qty '.$c_total_exfact_qty_con;?>" ><p><? echo number_format(($tot_c_mon_qty),0); $total_month_qty +=$tot_c_mon_qty; ?></p></td>
								<?
								$exfact_qty_con=0;$tot_bal_qty=0;
								for($p=1;$p<=$num_days;$p++)
								{
									$day=($p<=9)? '0'.$p:$p;
									if($shipment_status_id==2) //Balance Qty
										{
											$exfact_qty_con=$ex_fact_con_arr[$date_key][$buyer_id][$date_key."-".$day];	
											//echo $date_key.'='.$buyer_id.'='.$date_key."-".$day.'<br>';
											$tot_bal_qty=$row_data_dtls[$date_key][$buyer_id][$date_key."-".$day];
											//if($tot_bal_qty<0) $day_qty=0;else $day_qty=$tot_bal_qty;
										}
										else
										{
											$tot_bal_qty=$row_data_dtls[$date_key][$buyer_id][$date_key."-".$day]-$exfact_qty_con;
										}
										
										
										if($all_date_button==""){
											
											if($day_total_con[$date_key."-".$day]>0 || $day_total_proj[$date_key."-".$day]>0){
												
												$day_qtys=$tot_bal_qty-$exfact_qty_con;
												if($day_qtys<0) $day_qty=0;else $day_qty=$day_qtys;
												
												?>
												<td  style="height:20px;" title="<? echo 'PO Qty='.$day_qtys.', Ship Qty'.$exfact_qty_con;?>" width="35" align="center"><div><? if($row_data_dtls[$date_key][$buyer_id][$date_key."-".$day]>0) {if($day_qty=="") echo ""; else echo number_format($day_qty,0);} else echo ""; ?></div></td>
												<?
												$total_val[$day] +=$day_qty;
											 }
										}
										else
										{
											$day_qtys=$tot_bal_qty-$exfact_qty_con;
											if($day_qtys<0) $day_qty=0;else $day_qty=$day_qtys;
											
											?>
											<td  style="height:20px;" title="<? echo 'PO Qty='.$day_qtys.', Ship Qty'.$exfact_qty_con;?>" width="35" align="center"><div><? if($row_data_dtls[$date_key][$buyer_id][$date_key."-".$day]>0) {if($day_qty=="") echo ""; else echo number_format($day_qty,0);} else echo ""; ?></div></td>
											<?
											$total_val[$day] +=$day_qty;
										}
								}
								
								?>
							</tr>
							<?
									}
							}
							else if($row['is_confirmed']==2)
							{
								$c_total_exfact_qty_proj=0;
								for($p=1;$p<=$num_days;$p++)
								{
									$day=($p<=9)? '0'.$p:$p;
									if($shipment_status_id==2) //Balance Qty
										{
											 if($day_total_proj[$date_key."-".$day]>0 || $day_total_con[$date_key."-".$day]>0)
											{
												$c_total_exfact_qty_proj+=$ex_fact_proj_arr[$date_key][$buyer_id][$date_key."-".$day];	
											}
											
											//echo $date_key.'='.$buyer_id.'='.$date_key."-".$day.'<br>';
										}
										
										
								}
								?>
								<tr bgcolor="<? echo $bgcolor ; ?>" style="font-family:'Arial Narrow'">
									<td ><? echo $buyer_library[$row['buyer']]."(bk)"; ?></td>
									<td  align="right"><? echo number_format($row['po_qty'],0); $total_buyer_qty+=$row['po_qty']; ?>&nbsp;</td>
                                    <td align="right" title="<? echo $c_total_exfact_qty_proj;?>"><p><? echo number_format($row["po_qty"]-$c_total_exfact_qty_proj,0); $total_month_qty +=$row["po_qty"]-$c_total_exfact_qty_proj; ?></p></td>
									<?
									$exfact_qty_proj=0;
									for($p=1;$p<=$num_days;$p++)
									{
										$day=($p<=9)? '0'.$p:$p;
										if($shipment_status_id==2) //Balance Qty
										{
											$exfact_qty_proj=$ex_fact_proj_arr[$date_key][$buyer_id][$date_key."-".$day];
											$tot_bal_qty=$row_data_dtls_project[$date_key][$buyer_id][$date_key."-".$day]-$exfact_qty_proj;
											if($tot_bal_qty<0) $days_qty=0;else $days_qty=$tot_bal_qty;
										}
										else
										{
											$days_qty=$row_data_dtls_project[$date_key][$buyer_id][$date_key."-".$day]-$exfact_qty_proj;
										}
											if($all_date_button=="")
											{
												 if($day_total_proj[$date_key."-".$day]>0 || $day_total_con[$date_key."-".$day]>0){
												//echo $day.'a';
												?>
												<td  style="height:20px;" title="<? echo $exfact_qty_proj;?>" width="35" align="center" ><div><?  if($row_data_dtls_project[$date_key][$buyer_id][$date_key."-".$day] >0) { if($days_qty=="") echo ""; else echo number_format($days_qty,0); } else echo "";  ?></div></td>
												<?
												$total_val[$day] +=$days_qty;
												}
											}
											else
											{
												?>
												<td  style="height:20px;" title="<? echo $exfact_qty_proj;?>" width="35" align="center" ><div><?  if($row_data_dtls_project[$date_key][$buyer_id][$date_key."-".$day] >0) { if($days_qty=="") echo ""; else echo number_format($days_qty,0); } else echo "";  ?></div></td>
												<?
												$total_val[$day] +=$days_qty;
												
											}
									}
									
									?>
								</tr>
								<?	
							}
							$k++;//font-weight:bold; 
						}
                    }
					?>
                    	<tr bgcolor="#CCCCCC" style="font-family:'Arial Narrow';">
                            <td  align="right" style="font-weight:bold;">Total:</td>
                            <td  align="right" style="font-weight:bold;"><? echo number_format($total_buyer_qty,0); ?>&nbsp;</td>
                             <td  align="right" style="font-weight:bold;"><?  echo number_format($total_month_qty,0); ?>&nbsp;</td>
                            <?
                            for($p=1;$p<=$num_days;$p++)
                            {
								//echo $day.'z';
								$day=($p<=9)? '0'.$p:$p;
								if($all_date_button==0)
								{
									if($day_total_cons_all[$date_key."-".$day]>0){
									//echo $day.'z';
	                                ?>
	                                <td  style="height:20px;" width="35"  align="center" ><div><?  echo number_format($total_val[$day],0); ?></div></td>
	                                <?
									//$total_val[]
									 }
								}
								else
								{
	                                ?>
	                                <td  style="height:20px;" width="35"  align="center" ><div><?  echo number_format($total_val[$day],0); ?></div></td>
	                                <?
								}
                            }
                            ?>
                        </tr>
                    </tbody>
             </table>
             <?
		}
	
		
	$html = ob_get_contents();
	ob_clean();
	 
	foreach (glob($_SESSION['logic_erp']['user_id']."*.xls") as $filename) {
	@unlink($filename);
	}
	
	$name=time();
	$filename=$_SESSION['logic_erp']['user_id']."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,$html);
	echo "$html****$filename";
	exit();
	
	
	/*foreach (glob("$user_id*.xls") as $filename) 
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
	echo "$total_data****$filename";
	exit();*/
	//disconnect($con);
	
	/*foreach (glob("$user_id*.xls") as $filename) 
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
	echo "$total_data****$filename";
	exit();
	disconnect($con);*/
}

//report_generate_order
if($action=="report_generate_order_summary")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	//echo $cbo_year_from;die;
	$company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_date_category=str_replace("'","",$cbo_date_category);
	$season=str_replace("'","",$txt_season);
	$order_status_id=str_replace("'","",$cbo_order_status);
	$cbo_report_type=str_replace("'","",$cbo_report_type);
	$shipment_status_id=str_replace("'","",$cbo_shipment_status);
	$all_date_button=$all_date;
	//shiping_status
	$dealing_merchant_id=str_replace("'","",$cbo_dealing_merchant);
	//dealing_marchant
	if($dealing_merchant_id>0) $marchd_cond="and a.dealing_marchant in($dealing_merchant_id)";
	else $marchd_cond="";
	$order_status_cond='';
	if($order_status_id==0)
	{
		$order_status_cond=" and c.is_confirmed in(1,2)";
	}
	else if($order_status_id!=0)
	{
		$order_status_cond=" and c.is_confirmed=$order_status_id";	
	}
	//$shipment_status_arr=array(1=>"Running Full Order Qty",2=>"Running Order Balance Qty",3=>"Fully Shipped",4=>"Cancelled Order"); 
	$shipment_status_cond='';
	if($shipment_status_id==1) // Running Full Order Qty
	{
		$shipment_status_cond=" and a.status_active=1 and b.status_active=1 and c.status_active=1 ";
	}
	else if($shipment_status_id==2) //Running Order Balance Qty
	{
		$shipment_status_cond=" and a.status_active=1 and b.status_active=1 and c.status_active=1 and c.shiping_status <> 3";	
	}
	else if($shipment_status_id==3) //Fully Shipped
	{
		$shipment_status_cond=" and c.shiping_status=$shipment_status_id  and a.status_active=1 and b.status_active=1 and c.status_active=1 ";	
	}
	else if($shipment_status_id==4) //Cancelled Order
	{
		$shipment_status_cond=" and a.status_active=1  and c.status_active=3 and c.shiping_status <> 3";	
	}
	
	
	if($season=="") $season_cond=""; else $season_cond=" and a.season_buyer_wise in('".implode("','",explode(",",$season))."')";

	//echo $cbo_buyer_name;die;
	if($cbo_buyer_name!=0) $buyer_cond=" and a.buyer_name='$cbo_buyer_name'"; else $buyer_cond="";
	if($company_name!=0) $company_con=" and a.company_name='$company_name'"; else $company_con="";
	if($company_name!=0) $company_con2=" and a.template_name='$company_name'"; else $company_con2="";
	$date_cond='';
	if(str_replace("'","",$cbo_year_from)!=0 && str_replace("'","",$cbo_month_from)!=0)
	{
		$start_year=str_replace("'","",$cbo_year_from);
		$start_month=str_replace("'","",$cbo_month_from);
		$start_date=$start_year."-".$start_month."-01";
		
		$end_year=str_replace("'","",$cbo_year_to);
		$end_month=str_replace("'","",$cbo_month_to);
		$num_days = cal_days_in_month(CAL_GREGORIAN, $end_month, $end_year);
		$end_date=$end_year."-".$end_month."-$num_days";
		if($cbo_date_category==1)
		{
			if($db_type==0) 
			{
				$date_cond=" and b.country_ship_date between '$start_date' and '$end_date'";
				$date_cond2=" and d.country_ship_date between '$start_date' and '$end_date'";
				$order_by_cond="DATE_FORMAT(b.country_ship_date, '%Y%m')";
				
			}
			if($db_type==2) 
			{
				$date_cond=" and b.country_ship_date between '".date("j-M-Y",strtotime(str_replace("'","",$start_date)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$end_date)))."'";
					$date_cond2=" and d.country_ship_date between '".date("j-M-Y",strtotime(str_replace("'","",$start_date)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$end_date)))."'";
				$order_by_cond="to_char(b.country_ship_date,'YYYY-MM')";
			}
			//$date_type="b.country_ship_date";
		}
		else if($cbo_date_category==2) //Cut-Off Date
		{
			if($db_type==0) 
			{
				$date_cond=" and b.cutup_date between '$start_date' and '$end_date'";
				$date_cond2=" and d.cutup_date between '$start_date' and '$end_date'";
				$order_by_cond="DATE_FORMAT(b.cutup_date, '%Y%m')";
				
			}
			if($db_type==2) 
			{
				$date_cond=" and b.cutup_date between '".date("j-M-Y",strtotime(str_replace("'","",$start_date)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$end_date)))."'";
				$date_cond2=" and d.cutup_date between '".date("j-M-Y",strtotime(str_replace("'","",$start_date)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$end_date)))."'";
				$order_by_cond="to_char(b.cutup_date,'YYYY-MM')";
			}
			//$date_type="b.cutup_date";
		}
	}
	
	
	$sql_color_no_arr=return_library_array("select po_break_down_id, count(distinct color_number_id) as color_id from wo_po_color_size_breakdown group by po_break_down_id","po_break_down_id","color_id");
	
	if($db_type==2) $fab_full_name="a.fabric_description || ',' || a.gsm_weight";
	else if ($db_type==0) $fab_full_name="a.fabric_description,',',a.gsm_weight";
	$body_part_arr=return_library_array("select c.id, $fab_full_name as fabrication from wo_pre_cost_fabric_cost_dtls a ,wo_po_details_master b,  wo_po_break_down c where a.job_no=b.job_no and b.job_no=c.job_no_mst and a.body_part_id in(1,20)","id","fabrication");
	
	$weak_of_year=return_library_array( "select week_date,week from  week_of_year",'week_date','week');
	$costing_per_arr=return_library_array( "select job_no, costing_per from wo_pre_cost_mst",'job_no','costing_per');
	$supplier_name_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	if($db_type==2) $color_type_group="LISTAGG(CAST(color_type_id AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY color_type_id) as color_type_id";
	else if($db_type==0) $color_type_group="group_concat(color_type_id) as color_type_id";
	
	$sql_pre="select  b.job_no,$color_type_group from wo_pre_cost_fabric_cost_dtls  b,wo_po_details_master a where a.job_no=b.job_no and b.status_active=1 and b.is_deleted=0 group by b.job_no";
	$pre_result=sql_select($sql_pre);
	$fab_color_type_arr=array();
	foreach($pre_result as $row)
	{
		$fab_color_type_arr[$row[csf("job_no")]]['color_type_id']=$row[csf("color_type_id")];
	}
	$sql_sum="select  b.job_no, b.fab_knit_req_kg, b.fab_knit_fin_req_kg from wo_pre_cost_sum_dtls  b,wo_po_details_master a where a.job_no=b.job_no and b.status_active=1 and b.is_deleted=0";
	$pre_result_sum=sql_select($sql_sum);
	$fab_grey_cons_arr=array();
	foreach($pre_result_sum as $row)
	{
		$fab_grey_cons_arr[$row[csf("job_no")]]['fab_knit_req_kg']=$row[csf("fab_knit_req_kg")];
		$fab_grey_cons_arr[$row[csf("job_no")]]['fab_knit_fin_req_kg']=$row[csf("fab_knit_fin_req_kg")];
	}
	if($db_type==2) $embl_group="LISTAGG(CAST(emb_name AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY emb_name) as emb_name";
	else if($db_type==0) $embl_group="group_concat(emb_name) as emb_name";
	$sql_ebl="select  b.job_no,$embl_group from wo_pre_cost_embe_cost_dtls  b,wo_po_details_master a where a.job_no=b.job_no and b.status_active=1 and b.is_deleted=0 and cons_dzn_gmts>0 group by b.job_no";
	$ebl_result=sql_select($sql_ebl);
	$embl_fab_type_arr=array();
	foreach($ebl_result as $row)
		{
			$embl_fab_type_arr[$row[csf("job_no")]]['emb_name']=$row[csf("emb_name")];
		}
		$sql_sewing="select b.production_source as prod_source,b.serving_company,d.production_qnty as production_qnty,b.company_id,c.po_break_down_id as po_id from pro_garments_production_mst  b,pro_garments_production_dtls d,wo_po_color_size_breakdown c where b.id=d.mst_id and c.id=d.color_size_break_down_id and b.production_type=5 and d.production_type=5 and b.status_active=1 and b.is_deleted=0";
	$sew_result=sql_select($sql_sewing);
	$sewing_outCompany_arr=array();
	foreach($sew_result as $row)
		{
			$sewing_outCompany_arr[$row[csf("po_id")]]['company_id']=$row[csf("company_id")];
			$sewing_outCompany_arr[$row[csf("po_id")]]['prod_source']=$row[csf("prod_source")];
			$sewing_outCompany_arr[$row[csf("po_id")]]['serving_company']=$row[csf("serving_company")];
			$sewing_outCompany_arr[$row[csf("po_id")]]['prod_qty']+=$row[csf("production_qnty")];
		}
		$sql_book="select  a.item_category,a.fabric_source,a.is_approved,b.po_break_down_id as po_id,b.job_no,a.booking_no,a.booking_no_prefix_num as booking_no_pre from wo_booking_dtls  b, wo_booking_mst a where a.booking_no=b.booking_no and b.booking_type=1 and b.status_active=1 and b.is_deleted=0";
		$pre_book=sql_select($sql_book);
		$fab_booking_arr=array();
		foreach($pre_book as $row)
		{
			$fab_booking_arr[$row[csf("po_id")]]['booking_no_pre']=$row[csf("booking_no_pre")];
			$fab_booking_arr[$row[csf("po_id")]]['booking_no']=$row[csf("booking_no")];
			$fab_booking_arr[$row[csf("po_id")]]['item_category']=$row[csf("item_category")];
			$fab_booking_arr[$row[csf("po_id")]]['fabric_source']=$row[csf("fabric_source")];
			$fab_booking_arr[$row[csf("po_id")]]['is_approved']=$row[csf("is_approved")];
		}
		$sql_print="select  a.template_name as company,a.format_id from lib_report_template a  where a.module_id=2 and a.report_id=1 and a.is_deleted=0 and a.status_active=1 $company_con2";
		$results=sql_select($sql_print);
		$fab_booking_button_arr=array();
		foreach($results as $row)
		{
			$fab_booking_button_arr[$row[csf("company")]]['print']=$row[csf("format_id")];
		}
		if($shipment_status_id==2) //Running Order Balance Qty
		{
			/*$sql_exf="select a.buyer_name,b.po_break_down_id as po_id,b.ex_factory_date as ex_fact_date, c.is_confirmed,
			(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_fact_qty,
			(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_fact_ret_qty
			 from wo_po_break_down c,pro_ex_factory_mst b,wo_po_details_master a  where c.id=b.po_break_down_id and c.job_no_mst=a.job_no and  b.status_active=1 and b.is_deleted=0 $company_con  $buyer_cond $season_cond";*/
			  $sql_exf_c_date="select d.id as color_break_id,a.buyer_name,d.po_break_down_id as po_id,d.country_ship_date as country_ship_date,d.cutup_date, c.is_confirmed
			 from wo_po_break_down c,wo_po_details_master a,wo_po_color_size_breakdown d  where  c.job_no_mst=a.job_no and  d.status_active=1 and d.is_deleted=0 and c.id=d.po_break_down_id  and  a.job_no=d.job_no_mst  $company_con $marchd_cond $buyer_cond $season_cond $date_cond2";
			$result_c=sql_select($sql_exf_c_date);
			foreach($result_c as $row)
			{
				if($cbo_date_category==1)
				{
					$row[csf("country_ship_date")]=$row[csf("country_ship_date")];
				}
				else //Cut-Off
				{
					$row[csf("country_ship_date")]=$row[csf("cutup_date")];	
				}
				
				if($row[csf("is_confirmed")]==1)
				{
				$c_date_con_arr[$row[csf("buyer_name")]][$row[csf("po_id")]][$row[csf("color_break_id")]]['country_date']=$row[csf("country_ship_date")];
				$c_date_con_arr[$row[csf("buyer_name")]][$row[csf("po_id")]][$row[csf("color_break_id")]]['cut_date']=$row[csf("country_ship_date")];
				}
				else
				{
				$c_date_con_arr[$row[csf("buyer_name")]][$row[csf("po_id")]][$row[csf("color_break_id")]]['country_date']=$row[csf("country_ship_date")];
				$c_date_con_arr[$row[csf("buyer_name")]][$row[csf("po_id")]][$row[csf("color_break_id")]]['cut_date']=$row[csf("country_ship_date")];
				}
			}
			
			 $sql_exf="SELECT a.buyer_name,b.po_break_down_id as po_id,d.color_size_break_down_id as color_break_id, c.is_confirmed, b.shiping_status,
			(CASE WHEN b.entry_form!=85 THEN d.production_qnty 	 ELSE 0 END) as ex_fact_qty,
			(CASE WHEN b.entry_form=85 THEN d.production_qnty  ELSE 0 END) as ex_fact_ret_qty
			 from wo_po_break_down c,pro_ex_factory_mst b,wo_po_details_master a,pro_ex_factory_dtls d  where c.id=b.po_break_down_id and c.job_no_mst=a.job_no and b.id=d.mst_id and b.po_break_down_id=b.po_break_down_id and  b.status_active=1 and b.is_deleted=0 and  d.status_active=1 and d.is_deleted=0   $company_con  $marchd_cond $buyer_cond $season_cond ";
			$result=sql_select($sql_exf);
			$ex_fact_con_arr=array();$ex_fact_proj_arr=array(); $ship_status_arr=array();
			foreach($result as $row)
			{
				if($cbo_date_category==1)
				{
					$row[csf("country_ship_date")]=$c_date_con_arr[$row[csf("buyer_name")]][$row[csf("po_id")]][$row[csf("color_break_id")]]['country_date'];
				}
				else //Cut-Off
				{
					$row[csf("country_ship_date")]=$c_date_con_arr[$row[csf("buyer_name")]][$row[csf("po_id")]][$row[csf("color_break_id")]]['cut_date'];	
				}
				
				if($row[csf("is_confirmed")]==1)
				{
				$ex_fact_con_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$row[csf("buyer_name")]][$row[csf("po_id")]][date("Y-m-d",strtotime($row[csf("country_ship_date")]))]+=$row[csf("ex_fact_qty")]-$row[csf("ex_fact_ret_qty")];
				}
				else
				{
					$ex_fact_proj_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$row[csf("buyer_name")]][$row[csf("po_id")]][date("Y-m-d",strtotime($row[csf("country_ship_date")]))]+=$row[csf("ex_fact_qty")]-$row[csf("ex_fact_ret_qty")];
				}
				$ship_status_arr[$row[csf("po_id")]]=$row[csf("shiping_status")];
			}
		}
		
		
	
	 $data_array="SELECT   a.id as job_id,  a.job_no, a.style_ref_no,a.dealing_marchant,  a.company_name,  a.buyer_name,  a.ship_mode,	 a.total_set_qnty as ratio,  a.gmts_item_id,  a.set_smv,  a.season_buyer_wise as season, a.insert_date,  a.update_date, a.order_uom, b.id as color_break_id, b.po_break_down_id,  b.order_quantity as po_quantity_pcs,	 c.po_quantity as po_qty,b.country_ship_date, b.cutup_date,	 b.order_total, c.details_remarks, c.unit_price, c.is_confirmed,c.po_number,c.po_quantity, c.po_received_date		from wo_po_details_master a, wo_po_color_size_breakdown b,wo_po_break_down c where 	a.job_no=b.job_no_mst	and b.po_break_down_id=c.id  and  b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0	$company_con $marchd_cond $buyer_cond $order_status_cond $season_cond $date_cond $shipment_status_cond	order by  $order_by_cond,c.is_confirmed,a.job_no ASC";
		
	//echo $data_array;
	$result_po=sql_select($data_array);
	$row_data_dtls=array();
	$tmp_arr=array();
	$all_po_array=array();
	//$num_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
	$day_total_con=array();
		foreach($result_po as $row)
		{
			$all_po_array[$row[csf("po_break_down_id")]]=$row[csf("po_break_down_id")];
			if($row[csf("color_break_id")])
			{
				if($cbo_date_category==1)
				{
					$row[csf("country_ship_date")]=$row[csf("country_ship_date")];
				}
				else //Cut-Off
				{
					$row[csf("country_ship_date")]=$row[csf("cutup_date")];	
				} 

				$tmp_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$row[csf("buyer_name")]][$row[csf("is_confirmed")]][$row[csf("po_break_down_id")]]["c_month_pcs"]+=$row[csf("c_month_pcs")];
				$tmp_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$row[csf("buyer_name")]][$row[csf("is_confirmed")]][$row[csf("po_break_down_id")]]["po_qty"]+=$row[csf("po_quantity_pcs")];
				$tmp_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$row[csf("buyer_name")]][$row[csf("is_confirmed")]][$row[csf("po_break_down_id")]]["buyer"] =$row[csf("buyer_name")];
				$tmp_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$row[csf("buyer_name")]][$row[csf("is_confirmed")]][$row[csf("po_break_down_id")]]["dealing_marchant"] =$row[csf("dealing_marchant")];
				$tmp_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$row[csf("buyer_name")]][$row[csf("is_confirmed")]][$row[csf("po_break_down_id")]]["is_confirmed"] =$row[csf("is_confirmed")];
				$tmp_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$row[csf("buyer_name")]][$row[csf("is_confirmed")]][$row[csf("po_break_down_id")]]["job_no"] =$row[csf("job_no")];
				$tmp_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$row[csf("buyer_name")]][$row[csf("is_confirmed")]][$row[csf("po_break_down_id")]]["order_uom"] =$row[csf("order_uom")];
				$tmp_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$row[csf("buyer_name")]][$row[csf("is_confirmed")]][$row[csf("po_break_down_id")]]["po_break_down_id"] =$row[csf("po_break_down_id")];
				$tmp_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$row[csf("buyer_name")]][$row[csf("is_confirmed")]][$row[csf("po_break_down_id")]]["po_number"] =$row[csf("po_number")];
				$tmp_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$row[csf("buyer_name")]][$row[csf("is_confirmed")]][$row[csf("po_break_down_id")]]["style_ref_no"] =$row[csf("style_ref_no")];
				$tmp_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$row[csf("buyer_name")]][$row[csf("is_confirmed")]][$row[csf("po_break_down_id")]]["set_smv"] =$row[csf("set_smv")];
				$tmp_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$row[csf("buyer_name")]][$row[csf("is_confirmed")]][$row[csf("po_break_down_id")]]["gmts_item_id"] =$row[csf("gmts_item_id")];
				$tmp_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$row[csf("buyer_name")]][$row[csf("is_confirmed")]][$row[csf("po_break_down_id")]]["unit_price"] =$row[csf("unit_price")];
				$tmp_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$row[csf("buyer_name")]][$row[csf("is_confirmed")]][$row[csf("po_break_down_id")]]["details_remarks"] =$row[csf("details_remarks")];
				$tmp_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$row[csf("buyer_name")]][$row[csf("is_confirmed")]][$row[csf("po_break_down_id")]]["season"] =$row[csf("season")];
				$tmp_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$row[csf("buyer_name")]][$row[csf("is_confirmed")]][$row[csf("po_break_down_id")]]["ratio"] =$row[csf("ratio")];
				$tmp_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$row[csf("buyer_name")]][$row[csf("is_confirmed")]][$row[csf("po_break_down_id")]]["company_name"] =$row[csf("company_name")];			//$tmp_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$row[csf("buyer_name")]][$row[csf("is_confirmed")]][$row[csf("po_break_down_id")]]["po_quantity"] =$row[csf("po_quantity")];
				$tmp_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$row[csf("buyer_name")]][$row[csf("is_confirmed")]][$row[csf("po_break_down_id")]]["update_date"] =$row[csf("update_date")];
				$tmp_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$row[csf("buyer_name")]][$row[csf("is_confirmed")]][$row[csf("po_break_down_id")]]["insert_date"] =$row[csf("insert_date")];

				$tmp_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$row[csf("buyer_name")]][$row[csf("is_confirmed")]][$row[csf("po_break_down_id")]]["ship_mode"] =$row[csf("ship_mode")];
				$tmp_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$row[csf("buyer_name")]][$row[csf("is_confirmed")]][$row[csf("po_break_down_id")]]["po_received_date"] =$row[csf("po_received_date")];

				if($row[csf("is_confirmed")]==1)
				{
					$po_qty_arr[$row[csf("po_break_down_id")]]["po_qty"]=$row[csf("po_qty")]; 
					$po_qty_arr[$row[csf("po_break_down_id")]]["po_qty"]=$row[csf("po_qty")]; 
					$row_data_dtls[date("Y-m",strtotime($row[csf("country_ship_date")]))][$row[csf("buyer_name")]][$row[csf("po_break_down_id")]][date("Y-m-d",strtotime($row[csf("country_ship_date")]))]+=$row[csf("po_quantity_pcs")];
					$day_total_con[date("Y-m-d",strtotime($row[csf("country_ship_date")]))][$row[csf("buyer_name")]]+=$row[csf("po_quantity_pcs")];
				}
				else
				{
					$po_qty_arr[$row[csf("po_break_down_id")]]["po_qty"]=$row[csf("po_qty")];
					$row_data_dtls_project[date("Y-m",strtotime($row[csf("country_ship_date")]))][$row[csf("buyer_name")]][$row[csf("po_break_down_id")]][date("Y-m-d",strtotime($row[csf("country_ship_date")]))]+=$row[csf("po_quantity_pcs")];
					$day_total_proj[date("Y-m-d",strtotime($row[csf("country_ship_date")]))][$row[csf("buyer_name")]]+=$row[csf("po_quantity_pcs")];
				} 

			}
			
		}
		if($db_type==2 && count($all_po_array)>999)
		{
			$all_po_ids_cond="";
			$chnk=array_chunk($all_po_array,999);
			foreach($chnk as $vals)
			{
				$ids=implode(",", $vals);
				if($all_po_ids_cond=="") $all_po_ids_cond.=" and (  c.id in ($ids) ";
				else $all_po_ids_cond.=" or (  c.id in ($ids) ";
				$all_po_ids_cond.=")";
			}

		}
		else
		{
			$all_po_ids_cond=" and c.id in (".implode(",", $all_po_array).")";
		}
	   if(!$all_po_ids_cond)$all_po_ids_cond="";

	 

		$total_pcs_sql="SELECT   c.id,b.order_quantity from wo_po_details_master a, wo_po_color_size_breakdown b,wo_po_break_down c where 	a.job_no=b.job_no_mst	and b.po_break_down_id=c.id	and b.status_active=1 and c.status_active=1	and b.is_deleted=0 and c.is_deleted=0 $all_po_ids_cond";// and  b.status_active=1 and b.is_deleted=0 and  d.status_active=1 and d.is_deleted=0
		$total_pcs_arr=array();
		foreach(sql_select($total_pcs_sql) as $v)
		{
			$total_pcs_arr[$v[csf("id")]]+=$v[csf("order_quantity")];
		}
		//print_r($total_pcs_arr);
		 
		ob_start();
		?>
        <table width="1080">
        	<tr class="form_caption">
        		<td colspan="12" align="center" style="font-size:16px;">
        			<strong>
        				<? 

        				if($company_name)
        				{
        					echo $company_library[$company_name]; 
        				}else
        				{
        					if($company_library[1] == "Metro Knitting and Dyeing Mills Ltd.")
        					{
        						echo $company_library[1]; 
        					}
        				}

        				?>
        			</strong>
        		</td>
        		<td colspan="5" style="color: black;"> Generate Date: <? echo date("Y-m-d");?>
        	</tr>

            <tr class="form_caption">
                <td colspan="12" align="center" style="font-size:12px;">  Monthly Order Summary /
        		 <? echo $shipment_status_arr[$shipment_status_id]."/". $report_type_arr[$cbo_report_type];?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
        		 <td colspan="5" style="color: black;"> 
					Generate Time: <? echo date("h:i:sa");?> 

				</td>
            </tr>

        </table>
        
        <?
		 	$condition= new condition();
		 	$condition->company_name("=$company_name");
			 if(str_replace("'","",$cbo_buyer_name)>0){
				  $condition->buyer_name("=$cbo_buyer_name");
			 }
			 
			 if($cbo_date_category==1 && str_replace("'","",$start_date)!='' && str_replace("'","",$end_date)!=''){
				 if($db_type==0) 
					{
						//$date_cond=" and b.country_ship_date between '$start_date' and '$end_date'";
						$condition->country_ship_date(" between '$start_date' and '$end_date'");
					}
					else if($db_type==2)
					{
						$condition->country_ship_date(" between '".date("j-M-Y",strtotime(str_replace("'","",$start_date)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$end_date)))."'");
						
					}
				  
			 }
			 if($cbo_date_category==2 && str_replace("'","",$start_date)!='' && str_replace("'","",$end_date)!=''){
				 
				 if($db_type==0) 
					{
						//$date_cond=" and b.country_ship_date between '$start_date' and '$end_date'";
						$condition->cutup_date(" between '$start_date' and '$end_date'");
					}
					else if($db_type==2)
					{
						$condition->cutup_date(" between '".date("j-M-Y",strtotime(str_replace("'","",$start_date)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$end_date)))."'");
						
					}
				  
			 }
			  
		$condition->init();
		$fabric= new fabric($condition);
 		$fabric_qty_arr=$fabric->getQtyArray_by_order_knitAndwoven_greyAndfinish();
		foreach($tmp_arr as $date_key=>$buyer_id_arr)
		{
            $month_value=explode("-",$date_key);
			$num_days = cal_days_in_month(CAL_GREGORIAN, $month_value[1], $month_value[0]);
			 
				?>
               
                <?
				foreach($buyer_id_arr as $buyer_id=>$is_confirm_arr)
				{
					$confirm_project_buyer_total=array();
					?>
                     Month: <? echo $months[$month_value[1]*1]."-".$month_value[0];  ?><br />
                    Buyer : <? echo $buyer_library[$buyer_id]; ?><br />
                    <?
					foreach($is_confirm_arr as $is_confirm_id=>$order_id_arr)
					{
						if($is_confirm_id==1)
						{
							echo "Confirm Order :";
							?>
							<br />
                             <table align="center" style="margin-left:400px">
                            <tr>
                            <td>&nbsp; </td> <td>&nbsp; </td>
                            <td bgcolor="#00CED1" height="15" width="30"></td>
							<td>&nbsp;New Job</td>
                            <td bgcolor="#1AA995" height="15" width="30">&nbsp;</td>
                            <td>&nbsp;Updated Job</td>
                            </tr>
                            </table>
							<table  width="<? if($all_date_button==""){ echo 2150;}else { echo 2990;} ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" >
                                <thead> 
                                    <tr style="font-family:'Arial Narrow'" >
                                        <th width="100">Job No</th>
                                        <th width="100">Order No</th>
                                        <th width="80">Style Ref.</th>
                                       
                                        <th width="80">Fabric (body)</th>
                                        
                                        <th width="60">SMV</th>
                                        <th width="60">SMV Min.</th>
                                        <th width="60">Price</th>
                                        
                                        <th width="70">No of Color</th>
                                        <th width="60">Ship mode</th>
                                        <th width="70">Order Qty</th>
                                        <th width="70">Qty in Pcs</th>
                                        <th width="70">C.Month Total</th>
                                        <th width="70">C.Month Bal</th>
                                      
                                        
                                        
                                        <?
                                        for($m=1;$m<=$num_days;$m++)
                                        {
											$day=($m<=9)? '0'.$m:$m;
											//echo $date_key."-".$day."=".$day_total_con[$date_key."-".$day]."<br/>";
											if($all_date_button=="")
											{
												if($day_total_con[$date_key."-".$day][$buyer_id]>0){
													if($m==$num_days)
													{
														
														?>
														<th width="55"><? echo  ($m<=9)? '0'.$m:$m; ?></th>
														<?
													}
													else
													{
														?>
														<th width="55"><? echo  ($m<=9)? '0'.$m:$m; ?></th>
														<?
													}
												}
											}
											else
											{
												if($m==$num_days)
												{
													
													?>
													<th width="55"><? echo  ($m<=9)? '0'.$m:$m; ?></th>
													<?
												}
												else
												{
													?>
													<th width="55"><? echo  ($m<=9)? '0'.$m:$m; ?></th>
													<?
												}
											
											}
                                        }
                                        ?>
                                        <th>Remark</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?
                                //($m<=9)? '0'.$m:$m.$month_value[1].$month_value[0];
                                $k=1;$total_val="";$total_buyer_qty="";$confirm_total_qty=array();$total_grey_qty=0;$total_finish_qty=0;$total_conf_sewing_output=0;$total_month_qty=0;
                                foreach($order_id_arr as $order_id=>$row)
                                {
									if ($k%2==0)
									$bgcolor="#E9F3FF";
									else
									$bgcolor="#FFFFFF";
									
									$dzn_qnty=0;
									$costing_per_id=$costing_per_arr[$row["job_no"]];
									if($costing_per_id==1) $dzn_qnty=12;
									else if($costing_per_id==3) $dzn_qnty=12*2;
									else if($costing_per_id==4) $dzn_qnty=12*3;
									else if($costing_per_id==5) $dzn_qnty=12*4;
									else $dzn_qnty=1;
									$dzn_qnty=$dzn_qnty*$row['ratio'];
									$order_qty_pcs=$row["po_qty"]*$row['ratio'];
									$update_date_arr=explode(" ",$row["update_date"]);
									$insert_date_arr=explode(" ",$row["insert_date"]);
									 $update_date=date("d-m-Y",strtotime($update_date_arr[0]));
									 $insert_date=date("d-m-Y",strtotime($insert_date_arr[0]));
									 $today_date=date("d-m-Y");//date("d-m-Y");
									
									 if($update_date=='' || $update_date=='01-01-1970')
									 {
										 $date_diff_insert=datediff( "d", $insert_date , $today_date);
										  
									 }
									 if($update_date!='' || $update_date!='01-01-1970')
									 {
										 $date_diff_update=datediff( "d", $update_date , $today_date);
										  
									 }
									 if($date_diff_insert<=3)
										{
											$bg_color="#00CED1";	
										}
									else if($date_diff_update<=3)
										{
											$bg_color="#1AA995";	
										}
										else
										{
											$bg_color=$bgcolor;
										}
									
									//echo $date_diff.'<br/>';
									
									
									$fab_grey_knit=($fabric_qty_arr['knit']['grey'][$row["po_break_down_id"]]/$order_qty_pcs)*$dzn_qnty;
									$fab_grey_woven=($fabric_qty_arr['woven']['grey'][$row["po_break_down_id"]]/$order_qty_pcs)*$dzn_qnty;
									$fab_grey_cons=$fab_grey_cons_arr[$row["job_no"]]['fab_knit_req_kg'];//$fab_grey_knit+$fab_grey_woven;
									$total_grey_qty+=$fab_grey_cons;
									$fab_finish_cons=$fab_grey_cons_arr[$row["job_no"]]['fab_knit_fin_req_kg'];//$fab_grey_knit+$fab_grey_woven;
									$total_finish_qty+=$fab_finish_cons;
									
									$fab_finish_knit=($fabric_qty_arr['knit']['finish'][$row["po_break_down_id"]]);
									$fab_finish_woven=($fabric_qty_arr['woven']['finish'][$row["po_break_down_id"]]);
									$fab_finish_cons=$fab_finish_knit+$fab_finish_woven;
									$total_finish_qty+=$fab_finish_cons;
									$prod_source=$sewing_outCompany_arr[$row["po_break_down_id"]]['prod_source'];
									$serving_company=$sewing_outCompany_arr[$row["po_break_down_id"]]['serving_company'];
									if($prod_source==1)
									{
										$sewing_company=$company_library[$serving_company];
									}
									else
									{
										$sewing_company=$supplier_name_arr[$serving_company];
									}
									//$confirm_total_qty =array();
									$item_category=$fab_booking_arr[$row["po_break_down_id"]]['item_category'];
									$fabric_source=$fab_booking_arr[$row["po_break_down_id"]]['fabric_source'];
									$is_approved=$fab_booking_arr[$row["po_break_down_id"]]['is_approved'];
								 $booking=$fab_booking_arr[$row["po_break_down_id"]]['booking_no'];
								$print_report_id=$fab_booking_button_arr[$row["company_name"]]['print'];
								$format_ids=explode(",",$print_report_id);
								if($booking!="" || $booking!=0)
								{
								foreach($format_ids as $row_id)
								{
								
									if($row_id==1)
									{ 
									
									 $variable="<a href='#' onClick=\"generate_worder_report('".$booking."','".$row["company_name"]."','".$row["po_break_down_id"]."','".$item_category."','".$fabric_source."','".$row["job_no"]."','".$is_approved."','show_fabric_booking_report_gr','".$k."')\"> ".$fab_booking_arr[$row["po_break_down_id"]]['booking_no_pre']." <a/>";
									?>
									
									<?
										
									}
									if($row_id==2)
									{ 
									
									
									 $variable="<a href='#' onClick=\"generate_worder_report('".$booking."','".$row["company_name"]."','".$row["po_break_down_id"]."','".$item_category."','".$fabric_source."','".$row["job_no"]."','".$is_approved."','show_fabric_booking_report','".$k."')\"> ".$fab_booking_arr[$row["po_break_down_id"]]['booking_no_pre']." <a/>";
									?>
									  
							  
									
								   <? }
								   if($row_id==3)
									{ 
									 $variable="<a href='#' onClick=\"generate_worder_report('".$booking."','".$row["company_name"]."','".$row["po_break_down_id"]."','".$item_category."','".$fabric_source."','". $row["job_no"]."','".$is_approved."','show_fabric_booking_report3','".$k."')\"> ".$fab_booking_arr[$row["po_break_down_id"]]['booking_no_pre']." <a/>";
									?>
									
									
								   <? }
								   
									if($row_id==4)
									{ 
									 $variable="<a href='#' onClick=\"generate_worder_report('".$booking."','".$row["company_name"]."','".$row["po_break_down_id"]."','".$item_category."','".$fabric_source."','". $row["job_no"]."','".$is_approved."','show_fabric_booking_report1','".$k."')\"> ".$fab_booking_arr[$row["po_break_down_id"]]['booking_no_pre']." <a/>";
									?>
									
									
								   <? }
								   
								   if($row_id==5)
									{ 
									 $variable="<a href='#' onClick=\"generate_worder_report('".$booking."','".$row["company_name"]."','".$row["po_break_down_id"]."','".$item_category."','".$fabric_source."','". $row["job_no"]."','".$is_approved."','show_fabric_booking_report2','".$k."')\"> ".$fab_booking_arr[$row["po_break_down_id"]]['booking_no_pre']." <a/>";
									?>
									
									
									<?
									}
									if($row_id==6)
									{ 
									 $variable="<a href='#' onClick=\"generate_worder_report('".$booking."','".$row["company_name"]."','".$row["po_break_down_id"]."','".$item_category."','".$fabric_source."','". $row["job_no"]."','".$is_approved."','show_fabric_booking_report4','".$k."')\"> ".$fab_booking_arr[$row["po_break_down_id"]]['booking_no_pre']." <a/>";
									?>
									
								   <? }
								   
								   if($row_id==7)
									{ 
									 $variable="<a href='#' onClick=\"generate_worder_report('".$booking."','".$row["company_name"]."','".$row["po_break_down_id"]."','".$item_category."','".$fabric_source."','". $row["job_no"]."','".$is_approved."','show_fabric_booking_report5','".$k."')\"> ".$fab_booking_arr[$row["po_break_down_id"]]['booking_no_pre']." <a/>";
									?>
									
								   <? }
								   
								}
								}
								else
								
								{
								 $variable='';	
								}//Booking end
								
								$c_tot_exfact_qty_con=0;
											for($p=1;$p<=$num_days;$p++)
                                            {
												
												$day=($p<=9)? '0'.$p:$p;
												if($shipment_status_id==2) //Balance Qty
												{
													
													if($day_total_con[$date_key."-".$day][$buyer_id]>0)
													{
														$c_tot_exfact_qty_con+=$ex_fact_con_arr[$date_key][$buyer_id][$order_id][$date_key."-".$day];
													}
													$c_tot_qty_bal=$row["po_qty"]-$c_tot_exfact_qty_con;
													if($c_tot_qty_bal<0) $c_days_qty_con=0;else $c_days_qty_con=$c_tot_qty_bal;
												}
												else
												{
													$c_days_qty_con=$row["po_qty"]-$c_tot_exfact_qty_con;	
												}
												
											}
									if(!$c_days_qty_con)continue;
									if($shipment_status_id==2)
									{
										//if( 0<$c_days_qty_con && $ship_status_arr[$row["po_break_down_id"]]!=3)
										//{
                                    ?>
                                    <tr style="font-family:'Arial Narrow'" bgcolor="<? echo $bg_color; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>">
                                        <td><p><? echo $row["job_no"];?></p></td>
                                        <td><p><? echo $row["po_number"];?></p></td>
                                        <td><p><? echo $row["style_ref_no"];?></p></td>
                                        
                                        <td><p>
										<?
										//$job="'".$row["job_no"]."'";
										 echo $body_part_arr[$row["po_break_down_id"]]; 
										 ?></p>
                                        </td>
                                        
                                         <td align="center">
                                         	<p>
                                            	<a href="##" onClick="openmypage_smv('<? echo $row["job_no"];?>',<? echo $row["order_uom"];?>,<? echo $order_id;?>)" ><? echo $row["set_smv"]/$row['ratio'];//$row["set_smv"];?> </a>
                                            </p>
                                         </td>
                                         
                                         <td align="right"><p><? echo $row["set_smv"]/$row['ratio']*$c_days_qty_con;//$row["set_smv"]*$row["po_qty"];?></p></td>
                                         <td align="center"><p><? echo $row["unit_price"];?></p></td>
                                         
                                        <td align="center"><p><? echo $sql_color_no_arr[$row["po_break_down_id"]]; ?></p></td>
                                       
                                        <td align="center"><p><? echo $shipment_mode[$row["ship_mode"]]; ?></p></td>
                                        <td align="right"><p><? echo number_format($po_qty_arr[$order_id]["po_qty"],0); $total_ord_qty +=$po_qty_arr[$order_id]["po_qty"]; ?></p></td>
                                        <td align="right"><p><? echo number_format($total_pcs_arr[$row["po_break_down_id"]],0); $total_ord_qty_in_pcs += $total_pcs_arr[$row["po_break_down_id"]]; ?></p></td>
                                        <td align="right"><p><? echo number_format($row["po_qty"],0); $total_c_month_qty_in_pcs += $row["po_qty"]; ?></p></td>
                                       
                                          <td align="right" title="<? echo $c_tot_exfact_qty_con;?>"><p><?  echo number_format($c_days_qty_con,0); $total_month_qty +=$c_days_qty_con;
										   ?></p></td>
                                          
                                         
                                       
                                       	<?
                                            $exfact_qty_con=0;
											for($p=1;$p<=$num_days;$p++)
                                            {
												
												$day=($p<=9)? '0'.$p:$p;
												if($shipment_status_id==2) //Balance Qty
												{
													$exfact_qty_con=$ex_fact_con_arr[$date_key][$buyer_id][$order_id][$date_key."-".$day];
													$tot_bal_qty=$row_data_dtls[$date_key][$buyer_id][$order_id][$date_key."-".$day]-$exfact_qty_con;
													if($tot_bal_qty<0) $days_qty=0;else $days_qty=$tot_bal_qty;
												}
												else
												{
													$days_qty=$row_data_dtls[$date_key][$buyer_id][$order_id][$date_key."-".$day]-$exfact_qty_con;
												}
												if($all_date_button=="")
												{
													if($day_total_con[$date_key."-".$day][$buyer_id]>0){
													?>
													<td title="<? echo $exfact_qty_con;?>" width="55" style="height:20px;" align="center" valign="bottom"><div><? if($days_qty=="") echo ""; else echo number_format($days_qty,0); ?></div></td>
													<?
													$confirm_total_qty[$p] +=$days_qty;
													$confirm_project_buyer_total[$buyer_id][$p] +=$days_qty;
													}
												}
												else
												{
													?>
													<td title="<? echo $exfact_qty_con;?>" width="55" style="height:20px;" align="center" valign="bottom"><div><? if($days_qty=="") echo ""; else echo number_format($days_qty,0); ?></div></td>
													<?
													$confirm_total_qty[$p] +=$days_qty;
													$confirm_project_buyer_total[$buyer_id][$p] +=$days_qty;
												}
                                            }
                                         ?>
                                          <td><p><? echo $row["details_remarks"]; ?></p></td>
                                    </tr>
                                    <?
									$k++;
									$total_conf_sewing_output+=$sewing_outCompany_arr[$row["po_break_down_id"]]['prod_qty'];
										//}
									}
									else
									{
									?>
                                    <tr style="font-family:'Arial Narrow';" bgcolor="<? echo $bg_color; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>">
                                    
                                        <td width="100"><p><? echo $row["job_no"];?></p></td>
                                        <td width="100"><p><? echo $row["po_number"];?></p></td>
                                        <td width="80"><p><? echo $row["style_ref_no"];?></p></td>
                                        
                                        <td width="80"><p>
										<?
										//$job="'".$row["job_no"]."'";
										 echo $body_part_arr[$row["po_break_down_id"]]; 
										 ?></p>
                                        </td>
                                         
                                         <td align="center" width="60">
                                         	<p>
                                            	<a href="##" onClick="openmypage_smv('<? echo $row["job_no"];?>',<? echo $row["order_uom"];?>,<? echo $order_id;?>)" >
                                            		<? 
	                                            		$smv = $row["set_smv"]/$row['ratio'];
	                                            		$data_type = gettype($smv);
	                                            		echo ($data_type=="double") ? number_format($smv,2) : $smv;
	                                            	?> 
	                                            </a>
                                            </p>
                                         </td>
                                         
                                         
                                         
                                         <td width="60" align="right"><p>
                                         	<? 
                                         		$smvMin = $row["set_smv"]/$row['ratio']*$c_days_qty_con;
                                         		$dataType = gettype($smvMin);
	                                            echo ($dataType=="double") ? number_format($smvMin,2) : $smvMin;
                                         	?>
                                         		
                                         	</p></td>
                                         <td width="60" align="center"><p><? echo $row["unit_price"];?></p></td>
                                         
                                        <td width="70" align="center"><p><? echo $sql_color_no_arr[$row["po_break_down_id"]]; ?></p></td>
                                       
                                        <td width="60" align="center"><p><? echo $shipment_mode[$row["ship_mode"]]; ?></p></td>
                                        <td width="70" align="right"><p><? echo number_format($po_qty_arr[$order_id]["po_qty"],0); $total_ord_qty +=$po_qty_arr[$order_id]["po_qty"]; ?></p></td>
                                        <td width="70" align="right"><p><? echo number_format($total_pcs_arr[$row["po_break_down_id"]],0); $total_ord_qty_in_pcs +=$total_pcs_arr[$row["po_break_down_id"]]; ?></p></td>

                                        <td width="70" align="right"><p><? echo number_format($row["po_qty"],0); $total_c_month_qty_in_pcs +=$row["po_qty"]; ?></p></td>
                                       
                                          <td width="70" align="right" title="<? echo $c_tot_exfact_qty_con;?>"><p><?  echo number_format($c_days_qty_con,0); $total_month_qty +=$c_days_qty_con;
										   ?></p></td>
                                          
                                         
                                       
                                       	<?
                                            $exfact_qty_con=0;
											for($p=1;$p<=$num_days;$p++)
                                            {
												
												$day=($p<=9)? '0'.$p:$p;
												if($shipment_status_id==2) //Balance Qty
												{
													$exfact_qty_con=$ex_fact_con_arr[$date_key][$buyer_id][$order_id][$date_key."-".$day];
													$tot_bal_qty=$row_data_dtls[$date_key][$buyer_id][$order_id][$date_key."-".$day]-$exfact_qty_con;
													if($tot_bal_qty<0) $days_qty=0;else $days_qty=$tot_bal_qty;
												}
												else
												{
													$days_qty=$row_data_dtls[$date_key][$buyer_id][$order_id][$date_key."-".$day]-$exfact_qty_con;
												}
												if($all_date_button=="")
												{
													if($day_total_con[$date_key."-".$day][$buyer_id]>0){
													?>
													<td title="<? echo $exfact_qty_con;?>" width="55" style="height:20px;"  align="center"  valign="bottom"><div><? if($days_qty=="") echo ""; else echo  number_format($days_qty,0); ?></div></td>
													<?
													$confirm_total_qty[$p] +=$days_qty;
													$confirm_project_buyer_total[$buyer_id][$p] +=$days_qty;
													}
												}
												else
												{
													?>
													<td title="<? echo $exfact_qty_con;?>" width="55" style="height:20px;"  align="center"  valign="bottom"><div><? if($days_qty=="") echo ""; else echo  number_format($days_qty,0); ?></div></td>
													<?
													$confirm_total_qty[$p] +=$days_qty;
													$confirm_project_buyer_total[$buyer_id][$p] +=$days_qty;
												}
                                            }
                                         ?>
                                          <td><p><? echo $row["details_remarks"]; ?></p></td>
                                    </tr>
                                    <?
									$k++;
									$total_conf_sewing_output+=$sewing_outCompany_arr[$row["po_break_down_id"]]['prod_qty'];
									}
                                }
                                ?>
                                	<tr bgcolor="#CCCCCC" style="font-family:'Arial Narrow'">
                                        <td colspan="8"  align="right" style="font-weight:bold;">Confirm Total:</td>
                                       
                                        <td  align="right" style="font-weight:bold;"><p><? //echo number_format($total_ord_qty,0); $buyer_total_order [$buyer_id] +=$total_ord_qty;  $total_ord_qty=''; ?></p></td>
                                         <td align="right" style="font-weight:bold;"><? echo number_format($total_ord_qty,0); $buyer_total_order [$buyer_id] +=$total_ord_qty;  $total_ord_qty=''; ?></td> 

                                         <td align="right" style="font-weight:bold;"><? echo number_format($total_ord_qty_in_pcs,0); $buyer_total_order_in_pcs [$buyer_id] +=$total_ord_qty_in_pcs;  $total_ord_qty_in_pcs=''; ?></td> 
                                         <td align="right" style="font-weight:bold;"><? echo number_format($total_c_month_qty_in_pcs,0); $buyer_total_c_month_in_pcs [$buyer_id] +=$total_c_month_qty_in_pcs;  $total_c_month_qty_in_pcs=0; ?></td> 

                                         <td align="right" style="font-weight:bold;"><? echo number_format($total_month_qty,0); $buyer_total_month[$buyer_id] +=$total_month_qty;  $total_month_qty=''; ?></td>
                                       
                                        <?
                                        for($p=1;$p<=$num_days;$p++)
                                        {
                                            $day=($p<=9)? '0'.$p:$p;
											if($all_date_button=="")
											{
												if($day_total_con[$date_key."-".$day][$buyer_id]>0){
	                                            //echo $day;
	                                            ?>
	                                            <td  style="height:20px;" width="40"  align="center"  valign="bottom"><div><?  echo number_format($confirm_total_qty[$p],0); ?></div></td>
	                                           
	                                            <?
												}
                                            	//$total_val[]
											}
											else
											{
	                                            ?>
	                                            <td  style="height:20px;" width="40"  align="center"  valign="bottom"><div><?  echo number_format($confirm_total_qty[$p],0); ?></div></td>
	                                           
	                                            <?
											}
                                        }
                                        ?>
                                         <td align="right" style="font-weight:bold;">&nbsp;</td>
                                	</tr>
                                </tbody>
							</table>
							<?
						}
						//project start here
						else if($is_confirm_id==2)
						{
							//
							//$project_qty_total=0;
							echo "Projected :";
							?>
							<br />
                             <table align="center" style="margin-left:400px">
                            <tr>
                            <td>&nbsp; </td> <td>&nbsp; </td>
                            <td bgcolor="#00CED1" height="15" width="30"></td>
							<td>&nbsp;New Job</td>
                            <td bgcolor="#1AA995" height="15" width="30">&nbsp;</td>
                            <td>&nbsp;Updated Job</td>
                            </tr>
                            </table>
							<table  width="<? if($all_date_button==""){ echo 2150;}else { echo 2990;} ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                                <thead> 
                                    <tr style="font-family:'Arial Narrow';">
                                        <th style="width:100px;">Job No</th>
                                        <th width="100">Order No</th>
                                        <th width="80">Style Ref.</th>
                                        <th width="80">Fabric (body)</th>
                                        <th width="60">SMV</th>
                                        <th width="60">SMV Min.</th>
                                        <th width="60">Price</th>
                                        <th width="70">No of Color</th>
                                        <th width="60">Ship mode</th>
                                        <th width="70">Order Qty</th>
                                        <th width="70">Qty in Pcs</th>
                                         <th width="70">C.Month Total</th>
                                         <th width="70">C.month Bal</th>
                                       
                                        <?
                                        for($m=1;$m<=$num_days;$m++)
                                        {
											$day=($m<=9)? '0'.$m:$m;
											//echo $project_qty_total[$m];
											
											if($all_date_button=="")
											{
												if($day_total_proj[$date_key."-".$day]>0){
												if($m==$num_days)
												{
													?>
													<th width="55"><? echo  ($m<=9)? '0'.$m:$m; ?></th>
													<?
												}
												else
												{
													?>
													<th width="55"><? echo  ($m<=9)? '0'.$m:$m; ?></th>
													<?
												}
												}
											}
											else
											{
												if($m==$num_days)
												{
													?>
													<th width="55"><? echo  ($m<=9)? '0'.$m:$m; ?></th>
													<?
												}
												else
												{
													?>
													<th width="55"><? echo  ($m<=9)? '0'.$m:$m; ?></th>
													<?
												}
											}
                                        }
                                        ?>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?
                                //($m<=9)? '0'.$m:$m.$month_value[1].$month_value[0];
                                $m=1;$total_val="";$total_buyer_qty="";$confirm_total_qty_project=array();
								$total_grey_qty_proj=0;	$total_finish_qty_proj=0;$total_proj_sewing_output=0;
                                foreach($order_id_arr as $order_id=>$row)
                                {
									if ($m%2==0)
									$bgcolors="#E9F3FF";
									else
									$bgcolors="#FFFFFF";
									//$confirm_total_qty =array();
									$dzn_qnty=0;
									$costing_per_id=$costing_per_arr[$row["job_no"]];
									if($costing_per_id==1) $dzn_qnty=12;
									else if($costing_per_id==3) $dzn_qnty=12*2;
									else if($costing_per_id==4) $dzn_qnty=12*3;
									else if($costing_per_id==5) $dzn_qnty=12*4;
									else $dzn_qnty=1;
									$dzn_qnty=$dzn_qnty*$row['ratio'];
									$order_qty_pcs=$row["po_qty"]*$row['ratio'];
									$update_date_arr=explode(" ",$row["update_date"]);
									$insert_date_arr=explode(" ",$row["insert_date"]);

									 $update_date=date("d-m-Y",strtotime($update_date_arr[0]));
									 $insert_date=date("d-m-Y",strtotime($insert_date_arr[0]));
									 $today_date=date("d-m-Y");//date("d-m-Y");
									
									 if($update_date=='' || $update_date=='01-01-1970')
									 {
										 $date_diff_insert=datediff( "d", $insert_date , $today_date);
										  
									 }
									 if($update_date!='' || $update_date!='01-01-1970')
									 {
										 $date_diff_update=datediff( "d", $update_date , $today_date);
										  
									 }
									 if($date_diff_insert<=3)
										{
											$bg_colors="#00CED1";	
										}
									else if($date_diff_update<=3)
										{
											$bg_colors="#1AA995";	
										}
										else
										{
											$bg_colors=$bgcolors;
										}
									
									$fab_grey_knit_proj=($fabric_qty_arr['knit']['grey'][$row["po_break_down_id"]]/$order_qty_pcs)*$dzn_qnty;
									$fab_grey_woven_proj=($fabric_qty_arr['woven']['grey'][$row["po_break_down_id"]]/$order_qty_pcs)*$dzn_qnty;
									$fab_grey_cons_proj=$fab_grey_cons_arr[$row["job_no"]]['fab_knit_req_kg'];//$fab_grey_knit_proj+$fab_grey_woven_proj;
									$total_grey_qty_proj+=$fab_grey_cons_proj+$total_grey_qty_proj;
									
									$fab_finish_knit_proj=$fabric_qty_arr['knit']['finish'][$row["po_break_down_id"]];
									$fab_finish_woven_proj=$fabric_qty_arr['woven']['finish'][$row["po_break_down_id"]];
									$fab_finish_cons_proj=$fab_finish_knit_proj+$fab_finish_woven_proj;
									$total_finish_qty_proj+=$fab_finish_knit_proj+$fab_finish_woven_proj;
									
									$prod_source=$sewing_outCompany_arr[$row["po_break_down_id"]]['prod_source'];
									$serving_company=$sewing_outCompany_arr[$row["po_break_down_id"]]['serving_company'];
									if($prod_source==1)
									{
										$sewing_company=$company_library[$serving_company];
									}
									else
									{
										$sewing_company=$supplier_name_arr[$serving_company];
									}
									$item_category=$fab_booking_arr[$row["po_break_down_id"]]['item_category'];
									$fabric_source=$fab_booking_arr[$row["po_break_down_id"]]['fabric_source'];
									$is_approved=$fab_booking_arr[$row["po_break_down_id"]]['is_approved'];
								 $booking=$fab_booking_arr[$row["po_break_down_id"]]['booking_no'];
								$print_report_id=$fab_booking_button_arr[$row["company_name"]]['print'];
								$format_ids=explode(",",$print_report_id);
								if($booking!="" || $booking!=0)
								{
								foreach($format_ids as $row_id)
								{
								
									if($row_id==1)
									{ 
									
									 $variable="<a href='#' onClick=\"generate_worder_report('".$booking."','".$row["company_name"]."','".$row["po_break_down_id"]."','".$item_category."','".$fabric_source."','".$row["job_no"]."','".$is_approved."','show_fabric_booking_report_gr','".$k."')\"> ".$fab_booking_arr[$row["po_break_down_id"]]['booking_no_pre']." <a/>";
									?>
									
									<?
										
									}
									if($row_id==2)
									{ 
									
									
									 $variable="<a href='#' onClick=\"generate_worder_report('".$booking."','".$row["company_name"]."','".$row["po_break_down_id"]."','".$item_category."','".$fabric_source."','".$row["job_no"]."','".$is_approved."','show_fabric_booking_report','".$k."')\"> ".$fab_booking_arr[$row["po_break_down_id"]]['booking_no_pre']." <a/>";
									?>
									  
							  
									
								   <? }
								   if($row_id==3)
									{ 
									 $variable="<a href='#' onClick=\"generate_worder_report('".$booking."','".$row["company_name"]."','".$row["po_break_down_id"]."','".$item_category."','".$fabric_source."','". $row["job_no"]."','".$is_approved."','show_fabric_booking_report3','".$k."')\"> ".$fab_booking_arr[$row["po_break_down_id"]]['booking_no_pre']." <a/>";
									?>
									
									
								   <? }
								   
									if($row_id==4)
									{ 
									 $variable="<a href='#' onClick=\"generate_worder_report('".$booking."','".$row["company_name"]."','".$row["po_break_down_id"]."','".$item_category."','".$fabric_source."','". $row["job_no"]."','".$is_approved."','show_fabric_booking_report1','".$k."')\"> ".$fab_booking_arr[$row["po_break_down_id"]]['booking_no_pre']." <a/>";
									?>
									
									
								   <? }
								   
								   if($row_id==5)
									{ 
									 $variable="<a href='#' onClick=\"generate_worder_report('".$booking."','".$row["company_name"]."','".$row["po_break_down_id"]."','".$item_category."','".$fabric_source."','". $row["job_no"]."','".$is_approved."','show_fabric_booking_report2','".$k."')\"> ".$fab_booking_arr[$row["po_break_down_id"]]['booking_no_pre']." <a/>";
									?>
									
									
									<?
									}
									if($row_id==6)
									{ 
									 $variable="<a href='#' onClick=\"generate_worder_report('".$booking."','".$row["company_name"]."','".$row["po_break_down_id"]."','".$item_category."','".$fabric_source."','". $row["job_no"]."','".$is_approved."','show_fabric_booking_report4','".$k."')\"> ".$fab_booking_arr[$row["po_break_down_id"]]['booking_no_pre']." <a/>";
									?>
									
								   <? }
								   
								   if($row_id==7)
									{ 
									 $variable="<a href='#' onClick=\"generate_worder_report('".$booking."','".$row["company_name"]."','".$row["po_break_down_id"]."','".$item_category."','".$fabric_source."','". $row["job_no"]."','".$is_approved."','show_fabric_booking_report5','".$k."')\"> ".$fab_booking_arr[$row["po_break_down_id"]]['booking_no_pre']." <a/>";
									?>
									
								   <? }
								   
								}
								}
								else
								{
								 $variable='';	
								}//Booking end
											$c_tot_exfact_qty_proj=0;
											for($p=1;$p<=$num_days;$p++)
                                            {
												$day=($p<=9)? '0'.$p:$p;
												if($shipment_status_id==2) //Balance Qty
												{
												if($day_total_proj[$date_key."-".$day]>0)
													{
														$c_tot_exfact_qty_proj+=$ex_fact_proj_arr[$date_key][$buyer_id][$order_id][$date_key."-".$day];		
													}
												}
											}
											
											if(!($row["po_qty"]-$c_tot_exfact_qty_proj))continue;
                                    ?>
                                    <tr style="font-family:'Arial Narrow';" bgcolor="<? echo $bg_colors; ?>" onClick="change_color('tr_<? echo $m; ?>','<? echo $bgcolors;?>')" id="tr_<? echo $m; ?>">

                                        <td style="width:100px;"><p><? echo $row["job_no"];?></p></td>
                                        <td width="100"><p><? echo $row["po_number"];?></p></td>
                                        <td width="80"><p><? echo $row["style_ref_no"];?></p></td>
                                        
                                        <td width="80">
										<p><?
											echo $body_part_arr[$row["po_break_down_id"]]; 
										 ?></p>
                                        </td>
                                        
                                        <td width="60" align="center">
                                        <p>
                                        	<a href="##" onClick="openmypage_smv('<? echo $row["job_no"];?>',<? echo $row["order_uom"];?>,<? echo $order_id;?>)" ><? echo $row["set_smv"];?> </a>
                                        </p>
                                        </td>
                                        
                                        <td width="60" align="right"><p><? echo $row["set_smv"]*$row["po_qty"];?></p></td>
                                        <td width="60" align="center"><p><? echo $row["unit_price"]?></p></td>
                                        
                                        <td width="70" align="center"><p><? echo $sql_color_no_arr[$row["po_break_down_id"]]; ?></p></td>
                                         
                                        <td width="60"  align="center"><p><? echo $shipment_mode[$row["ship_mode"]]; ?></p></td>
                                        <td width="70" align="right"><p><? echo number_format($po_qty_arr[$order_id]["po_qty"],0); $total_ord_proj_qty +=$po_qty_arr[$order_id]["po_qty"]; ?></p></td>
                                                                                
                                        <td width="70" align="right"><p><? echo number_format($total_pcs_arr[$row["po_break_down_id"]],0); $total_ord_proj_qty_in_pcs +=$total_pcs_arr[$row["po_break_down_id"]]; ?></p></td>

                                        <td width="70" align="right"><p><? echo number_format($row["po_qty"],0); $total_ord_proj_cmonth_in_pcs +=$row["po_qty"]; ?></p></td>

                                         <td width="70" align="right" title="<? echo $c_tot_exfact_qty_proj;?>"><p><? echo number_format($row["po_qty"]-$c_tot_exfact_qty_proj,0); $total_month_proj_qty +=$row["po_qty"]-$c_tot_exfact_qty_proj; ?></p></td>
                                        
                                        <?
                                            $exfact_qty_proj=0;
											for($p=1;$p<=$num_days;$p++)
                                            {
												$day=($p<=9)? '0'.$p:$p;
												if($shipment_status_id==2) //Balance Qty
												{
													$exfact_qty_proj=$ex_fact_proj_arr[$date_key][$buyer_id][$order_id][$date_key."-".$day];
													
													$tot_bal_qty_proj=$row_data_dtls_project[$date_key][$buyer_id][$order_id][$date_key."-".$day]-$exfact_qty_proj;
													if($tot_bal_qty_proj<0) $proj_days_qty=0;else $proj_days_qty=$tot_bal_qty_proj;
												}
												else
												{
													$proj_days_qty=$row_data_dtls_project[$date_key][$buyer_id][$order_id][$date_key."-".$day]-$exfact_qty_proj;
												}
													if($all_date_button=="")
													{
														if($day_total_proj[$date_key."-".$day]>0)
														{
														?>
														<td style="height:20px;"  title="<? echo $exfact_qty_proj;?>" width="55"  align="center"  valign="bottom"><div><? if($proj_days_qty=="") echo ""; else echo  number_format($proj_days_qty,0); ?></div></td>
														<?
														$confirm_total_qty_project[$p] +=$proj_days_qty;
														$confirm_project_buyer_total[$buyer_id][$p] +=$proj_days_qty;
														}
													}
													else
													{
														
														?>
														<td style="height:20px;"  title="<? echo $exfact_qty_proj;?>" width="55"  align="center"  valign="bottom"><div><? if($proj_days_qty=="") echo ""; else echo  number_format($proj_days_qty,0); ?></div></td>
														<?
														$confirm_total_qty_project[$p] +=$proj_days_qty;
														$confirm_project_buyer_total[$buyer_id][$p] +=$proj_days_qty;
														
													}
													
                                            }
                                         ?>
                                         <td><p><? echo $row["details_remarks"];?></p></td>
                                    </tr>
                                    <?
									$m++;
									$total_proj_sewing_output+=$sewing_outCompany_arr[$row["po_break_down_id"]]['prod_qty'];
									
                                }
                                ?>
                                <tr style="font-family:'Arial Narrow'" bgcolor="#CCCCCC">
                                    <td colspan="8"  align="right" style="font-weight:bold;">Projected Total:</td>
                                   
                                     <td align="right" style="font-weight:bold;">&nbsp;</td>
                                    <td  align="right" style="font-weight:bold;"><p><? echo number_format($total_ord_proj_qty,0); $total_ord_qty=''; $buyer_total_order [$buyer_id] +=$total_ord_proj_qty;  $total_ord_proj_qty=''; ?></p></td>
                                    
                                    <td  align="right" style="font-weight:bold;"><p><? echo number_format($total_ord_proj_qty_in_pcs,0); $total_ord_qty_in_pcs=''; $buyer_total_order_in_pcs [$buyer_id] +=$total_ord_proj_qty_in_pcs;  $total_ord_proj_qty_in_pcs=''; ?></p></td>

                                    <td  align="right" style="font-weight:bold;"><p><? echo number_format($total_ord_proj_cmonth_in_pcs,0);  $buyer_total_c_month_in_pcs [$buyer_id] +=$total_ord_proj_cmonth_in_pcs;  $total_ord_proj_cmonth_in_pcs=''; ?></p></td>
                                     
                                      <td align="right" style="font-weight:bold;"><p><? echo number_format($total_month_proj_qty,0);  $buyer_total_month[$buyer_id] +=$total_month_proj_qty;  $total_month_proj_qty=''; ?></p></td>
                                    <?
									
                                    for($p=1;$p<=$num_days;$p++)
                                    {
										$day=($p<=9)? '0'.$p:$p;
										if($all_date_button=="")
										{
											if($day_total_proj[$date_key."-".$day]>0)
											{
												?>
												<td  style="height:20px;" width="55" align="center"><div><? echo number_format($confirm_total_qty_project[$p],0); ?></div></td>
											   
												<?
												 $project_qty_total[$p]+=$confirm_total_qty_project[$p];
												//$total_val[]
											}
										}
										else
										{
											?>
											<td  style="height:20px;" width="55" align="center"><div><? echo number_format($confirm_total_qty_project[$p],0); ?></div></td>
										   
											<?
											 $project_qty_total[$p]+=$confirm_total_qty_project[$p];
											//$total_val[]
										}
                                    }
                                    ?>
                                    <td align="right" width="" style="font-weight:bold;"><? //echo $project_qty_total;?>&nbsp;</td>
                                </tr>
                                </tbody>
							</table>
							<?
						
						}
						
					}
					?>
					<table  width="<? if($all_date_button==""){ echo 2150;}else { echo 2990;} ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                        <tr bgcolor="#FFFFCC" style="font-family:'Arial Narrow';">
                        	<td width="100">&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="80">&nbsp;</td>
                            <td width="80">&nbsp;</td>
                            <td width="60">&nbsp;</td>
                            <td width="60">&nbsp;</td>
                            <td width="60">&nbsp;</td>
                            <td width="70">&nbsp;</td>
                            
                            <td align="right" width="60"><p>Buyer Ttl.</p></td>
                           
                            <td  align="right" style="font-weight:bold;" width="70"><p><? echo number_format($buyer_total_order [$buyer_id],0); ?></p></td>
                            <td  align="right" style="font-weight:bold;" width="70"><p><? echo number_format($buyer_total_order_in_pcs [$buyer_id],0); ?></p></td>
                            <td  align="right" style="font-weight:bold;" width="70"><p><? echo number_format($buyer_total_c_month_in_pcs[$buyer_id],0); ?></p></td>
                            <td  align="right" style="font-weight:bold;" width="70"><p><? echo number_format($buyer_total_month [$buyer_id],0); ?></p></td>
                            
                            
							<?
							
                            for($m=1;$m<=$num_days;$m++)
                            {
								$day=($m<=9)? '0'.$m:$m;
								if($is_confirm_id==1) $ship_status_total=$day_total_con[$date_key."-".$day][$buyer_id];//Confirm
								else $ship_status_total=$day_total_proj[$date_key."-".$day][$buyer_id];
								if($all_date_button=="")
								{
									if($ship_status_total>0)
									 {
										if($m==$num_days)
										{
											?>
											<td style="height:20px;" width="55"  align="center"><div style="word-break:break-all"><p><? echo number_format($confirm_project_buyer_total[$buyer_id][$m],0);  ?></p></div></td>
											<?
										}
										else
										{
											?>
											<td  style="height:20px;"  align="center"  width="55"><div style="word-break:break-all"><p><? echo number_format($confirm_project_buyer_total[$buyer_id][$m],0);  ?></p></div></td>
											<?
										}
									}
								}
								else
								{
									if($m==$num_days)
									{
										?>
										<td style="height:20px;" width="55"  align="center"><div style="word-break:break-all"><p><? echo number_format($confirm_project_buyer_total[$buyer_id][$m],0);  ?></p></div></td>
										<?
									}
									else
									{
										?>
										<td  style="height:20px;"  align="center"  width="55"><div style="word-break:break-all"><p><? echo number_format($confirm_project_buyer_total[$buyer_id][$m],0);  ?></p></div></td>
										<?
									}
								}
                            }
							
                            ?>
                            
                            <td>&nbsp;</td>
                        </tr>
                    </table>
                    <?
					
					//buyer loop end
				}

		}
		?>
    
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
	echo "$total_data****$filename";
	exit();
	//disconnect($con);
}
if($action=="report_generate_com_buyer_ord_allocation")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	//echo $cbo_year_from;die;

	$company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_date_category=str_replace("'","",$cbo_date_category);
	$shipment_status_id=str_replace("'","",$cbo_shipment_status);
	$season=str_replace("'","",$txt_season);
	$cbo_report_type=str_replace("'","",$cbo_report_type);
	$order_status_id=str_replace("'","",$cbo_order_status);
	
	$order_status_cond='';
	if($order_status_id==0)
	{
		$order_status_cond=" and c.is_confirmed in(1,2)";
	}
	else if($order_status_id!=0)
	{
		$order_status_cond=" and c.is_confirmed=$order_status_id";	
	}
	$shipment_status_cond='';
	if($shipment_status_id==1) // Running Full Order Qty
	{
		$shipment_status_cond=" and a.status_active=1 and b.status_active=1 and c.status_active=1";
	}
	else if($shipment_status_id==2) //Running Order Balance Qty
	{
		$shipment_status_cond=" and a.status_active=1 and b.status_active=1 and c.status_active=1";	
		
		//$date_cond2=" and b.country_ship_date between '".date("j-M-Y",strtotime(str_replace("'","",$start_date)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$end_date)))."'";
	}
	else if($shipment_status_id==3) //Fully Shipped
	{
		$shipment_status_cond="and c.shiping_status=$shipment_status_id  and a.status_active=1 and b.status_active=1 and c.status_active=1 ";	
	}
	else if($shipment_status_id==4) //Cancelled Order
	{
		$shipment_status_cond=" and a.status_active=1  and c.status_active=3";	
	}
	
	if($season=="") $season_cond=""; else $season_cond=" and a.season_buyer_wise in('".implode("','",explode(",",$season))."')";

	//echo $cbo_buyer_name;die;
	if($cbo_buyer_name!=0) $buyer_cond=" and a.buyer_name='$cbo_buyer_name'"; else $buyer_cond="";
	if($company_name!=0) $company_con=" and a.company_name='$company_name'"; else $company_con="";
	$date_cond='';
	if(str_replace("'","",$cbo_year_from)!=0 && str_replace("'","",$cbo_month_from)!=0)
	{
		$start_year=str_replace("'","",$cbo_year_from);
		$start_month=str_replace("'","",$cbo_month_from);
		$start_date=$start_year."-".$start_month."-01";
		
		$end_year=str_replace("'","",$cbo_year_to);
		$end_month=str_replace("'","",$cbo_month_to);
		$num_days = cal_days_in_month(CAL_GREGORIAN, $end_month, $end_year);
		$end_date=$end_year."-".$end_month."-$num_days";
		if($cbo_date_category==1)
		{
			if($db_type==0) 
			{
				$date_cond=" and b.country_ship_date between '$start_date' and '$end_date'";
				$date_cond2=" and d.country_ship_date between '$start_date' and '$end_date'";
				$order_by_cond="DATE_FORMAT(b.country_ship_date, '%Y%m')";
				
			}
			if($db_type==2) 
			{
				$date_cond=" and b.country_ship_date between '".date("j-M-Y",strtotime(str_replace("'","",$start_date)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$end_date)))."'";
				$date_cond2=" and d.country_ship_date between '".date("j-M-Y",strtotime(str_replace("'","",$start_date)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$end_date)))."'";
				$order_by_cond="to_char(b.country_ship_date,'YYYY-MM')";
			}
		}
		else if($cbo_date_category==2) //Cut-Off
		{
			if($db_type==0) 
			{
				$date_cond=" and b.cutup_date between '$start_date' and '$end_date'";
				$date_cond2=" and b.cutup_date between '$start_date' and '$end_date'";
				$order_by_cond="DATE_FORMAT(b.cutup_date, '%Y%m')";
				
			}
			if($db_type==2) 
			{
				$date_cond=" and b.cutup_date between '".date("j-M-Y",strtotime(str_replace("'","",$start_date)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$end_date)))."'";
				$date_cond2=" and d.cutup_date between '".date("j-M-Y",strtotime(str_replace("'","",$start_date)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$end_date)))."'";
				$order_by_cond="to_char(b.cutup_date,'YYYY-MM')";
			}
		}
	}
	if($shipment_status_id==2) //Running Order Balance Qty
		{
			/* $sql_exf="select a.buyer_name,b.po_break_down_id as po_id,d.country_ship_date as country_ship_date,d.cutup_date, c.is_confirmed,
			(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_fact_qty,
			(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_fact_ret_qty
			 from wo_po_break_down c,pro_ex_factory_mst b,wo_po_details_master a,wo_po_color_size_breakdown d  where c.id=b.po_break_down_id and c.job_no_mst=a.job_no and  b.status_active=1 and b.is_deleted=0 and c.id=d.po_break_down_id and b.po_break_down_id=d.po_break_down_id $company_con  $buyer_cond $season_cond";
			$result=sql_select($sql_exf);
			$ex_fact_con_arr=array();$ex_fact_proj_arr=array();
			foreach($result as $row)
			{
				if($cbo_date_category==1)
				{
					$row[csf("country_ship_date")]=$row[csf("country_ship_date")];
				}
				else //Cut-Off
				{
					$row[csf("country_ship_date")]=$row[csf("cutup_date")];	
				}
				if($row[csf("is_confirmed")]==1)
				{
				$ex_fact_con_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$row[csf("buyer_name")]][date("Y-m-d",strtotime($row[csf("country_ship_date")]))]+=$row[csf("ex_fact_qty")]-$row[csf("ex_fact_ret_qty")];
				}
				else
				{
					$ex_fact_proj_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$row[csf("buyer_name")]][date("Y-m-d",strtotime($row[csf("country_ship_date")]))]+=$row[csf("ex_fact_qty")]-$row[csf("ex_fact_ret_qty")];
				}
			}*/
			
			  $sql_exf_c_date="select d.id as color_break_id,a.buyer_name,d.po_break_down_id as po_id,d.country_ship_date as country_ship_date,d.cutup_date, c.is_confirmed
			 from wo_po_break_down c,wo_po_details_master a,wo_po_color_size_breakdown d  where  c.job_no_mst=a.job_no and  d.status_active=1 and d.is_deleted=0 and c.id=d.po_break_down_id  and  a.job_no=d.job_no_mst  $company_con  $buyer_cond $season_cond $date_cond2";
			$result_c=sql_select($sql_exf_c_date);
			foreach($result_c as $row)
			{
				if($cbo_date_category==1)
				{
					$row[csf("country_ship_date")]=$row[csf("country_ship_date")];
				}
				else //Cut-Off
				{
					$row[csf("country_ship_date")]=$row[csf("cutup_date")];	
				}
				
				if($row[csf("is_confirmed")]==1)
				{
				$c_date_con_arr[$row[csf("buyer_name")]][$row[csf("po_id")]][$row[csf("color_break_id")]]['country_date']=$row[csf("country_ship_date")];
				$c_date_con_arr[$row[csf("buyer_name")]][$row[csf("po_id")]][$row[csf("color_break_id")]]['cut_date']=$row[csf("country_ship_date")];
				}
				else
				{
				$c_date_con_arr[$row[csf("buyer_name")]][$row[csf("po_id")]][$row[csf("color_break_id")]]['country_date']=$row[csf("country_ship_date")];
				$c_date_con_arr[$row[csf("buyer_name")]][$row[csf("po_id")]][$row[csf("color_break_id")]]['cut_date']=$row[csf("country_ship_date")];
				}
			}
			
			 $sql_exf="SELECT a.buyer_name,b.po_break_down_id as po_id,d.color_size_break_down_id as color_break_id, c.is_confirmed,
			(CASE WHEN b.entry_form!=85 THEN d.production_qnty 	 ELSE 0 END) as ex_fact_qty,
			(CASE WHEN b.entry_form=85 THEN d.production_qnty  ELSE 0 END) as ex_fact_ret_qty
			 from wo_po_break_down c,pro_ex_factory_mst b,wo_po_details_master a,pro_ex_factory_dtls d  where c.id=b.po_break_down_id and c.job_no_mst=a.job_no and b.id=d.mst_id and b.po_break_down_id=b.po_break_down_id and  b.status_active=1 and b.is_deleted=0 and  d.status_active=1 and d.is_deleted=0  $company_con  $buyer_cond $season_cond ";
			$result=sql_select($sql_exf);
			$ex_fact_con_arr=array();$ex_fact_proj_arr=array();
			foreach($result as $row)
			{
				if($cbo_date_category==1)
				{
					$row[csf("country_ship_date")]=$c_date_con_arr[$row[csf("buyer_name")]][$row[csf("po_id")]][$row[csf("color_break_id")]]['country_date'];
				}
				else //Cut-Off
				{
					$row[csf("country_ship_date")]=$c_date_con_arr[$row[csf("buyer_name")]][$row[csf("po_id")]][$row[csf("color_break_id")]]['cut_date'];	
				}
				//echo $row[csf("country_ship_date")];
				if($row[csf("is_confirmed")]==1)
				{
				$ex_fact_con_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$row[csf("buyer_name")]][date("Y-m-d",strtotime($row[csf("country_ship_date")]))]+=$row[csf("ex_fact_qty")]-$row[csf("ex_fact_ret_qty")];
				}
				else if($row[csf("is_confirmed")]==2)
				{
					$ex_fact_proj_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$row[csf("buyer_name")]][date("Y-m-d",strtotime($row[csf("country_ship_date")]))]+=$row[csf("ex_fact_qty")]-$row[csf("ex_fact_ret_qty")];
				}
			}
			//print_r($ex_fact_con_arr);
		
			
			
		}
		
	/* $data_array="select
					 a.id as job_id,
					 a.job_no, 
					 a.company_name, 
					 a.buyer_name,
					 a.total_set_qnty, 
					 b.id as color_break_id, 
					 b.po_break_down_id, 
					 b.order_quantity as po_quantity, 
					 b.order_quantity as po_quantity_pcs, 
					 c.po_quantity as po_qty, 
					 b.country_ship_date, 
					 b.cutup_date, 
					 b.order_total,
					 c.is_confirmed
			from 
					wo_po_details_master a, 
					wo_po_color_size_breakdown b,
					wo_po_break_down c 
			where  
					a.job_no=b.job_no_mst
					and b.po_break_down_id=c.id 
					$company_con
					$date_cond
					$buyer_cond 
					$order_status_cond 
					$shipment_status_cond
					$season_cond 
					
			order by 
					 $order_by_cond ASC";*/
					/* $data_array="select
					  a.id as job_id,
					  a.job_no, 
					  a.company_name, 
					  a.buyer_name,
					  a.total_set_qnty, 
					  b.id as color_break_id, 
					  b.po_break_down_id, 
					  b.order_quantity as po_quantity, 
					  b.order_quantity as po_quantity_pcs, 
					  c.po_quantity as po_qty, 
					  b.country_ship_date, 
					  b.cutup_date, 
					  b.order_total,
					  c.is_confirmed, 
					  d.company_id as factory_name,
					  d.location_name as factory_location, 
					  e.qty as allocation_qty,
					  d.shipment_date,
					  e.date_name
				from 
					wo_po_details_master a,
					wo_po_color_size_breakdown b,
					wo_po_break_down c, 
					ppl_order_allocation_mst d, 
					ppl_order_allocation_dtls e
			
				where  
					a.job_no=b.job_no_mst
					and b.po_break_down_id=c.id 
					$company_con
					$date_cond
					$buyer_cond 
					$order_status_cond 
					$shipment_status_cond
					$season_cond 
					and c.id=d.po_no 
					and d.id=e.mst_id
					and a.job_no=d.job_no 
				order by d.company_id, d.location_name";*/
				$data_array="select
					  a.id as job_id,
					  a.job_no, 
					  a.company_name, 
					  a.buyer_name,
					  b.id as color_break_id, 
					  b.po_break_down_id, 
					  b.order_quantity as po_quantity, 
					  b.order_quantity as po_quantity_pcs, 
					  c.po_quantity as po_qty, 
					  b.country_ship_date, 
					  b.cutup_date, 
					  b.order_total,
					  c.is_confirmed, 
					  d.company_id as factory_name,
					  d.location_name as factory_location, 
					  e.qty as allocation_qty,
					  d.shipment_date,
					  e.date_name,
					  d.po_no as allocate_po_no  
				from 
					wo_po_details_master a,
					wo_po_color_size_breakdown b,
					wo_po_break_down c, 
					ppl_order_allocation_mst d, 
					ppl_order_allocation_dtls e
			
				where  
					a.job_no=b.job_no_mst
					and b.po_break_down_id=c.id
					$company_con 
					$date_cond
					$buyer_cond 
					$order_status_cond 
					$shipment_status_cond
					$season_cond 
					and c.id=d.po_no 
					and d.id=e.mst_id
					and a.job_no=d.job_no 
					and d.status_active=1 and d.is_deleted=0 
					and e.status_active=1 and e.is_deleted=0  
			 order by d.company_id,a.buyer_name desc";
				//order by d.company_id, d.location_name";
		////order by  $order_by_cond,c.is_confirmed ASC";
	//echo $data_array;//die;
	$result_po=sql_select($data_array);
	//$allocated_po=array();
	//$allocated_poo="";
	$row_data_dtls=array();//$company_check=array();
	$tmp_arr=array();$row_span_arr=array();
	//print_r($row_span_arr);
	//$num_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
	//$kk=1;
		foreach($result_po as $row)
		{
			if($cbo_date_category==1)
			{
				$row[csf("shipment_date")]=$row[csf("country_ship_date")];
			}
			else //Cut-Off
			{
				$row[csf("shipment_date")]=$row[csf("cutup_date")];	
			}
		  	//$allocated_poo.= $row[csf("allocate_po_no")].",";
			//$allocated_po[]=$row[csf("allocate_po_no")];
			$tmp_arr[date("Y-m",strtotime($row[csf("shipment_date")]))][$row[csf("buyer_name")]][$row[csf("is_confirmed")]]["po_qty"]+=$row[csf("po_quantity_pcs")];
			$tmp_arr[date("Y-m",strtotime($row[csf("shipment_date")]))][$row[csf("buyer_name")]][$row[csf("is_confirmed")]]["allocation_qty"]+=$row[csf("allocation_qty")];
			$tmp_arr[date("Y-m",strtotime($row[csf("shipment_date")]))][$row[csf("buyer_name")]][$row[csf("is_confirmed")]]["buyer"] =$row[csf("buyer_name")];
			$tmp_arr[date("Y-m",strtotime($row[csf("shipment_date")]))][$row[csf("buyer_name")]][$row[csf("is_confirmed")]]["factory"]=$row[csf("factory_name")];
			$tmp_arr[date("Y-m",strtotime($row[csf("shipment_date")]))][$row[csf("buyer_name")]][$row[csf("is_confirmed")]]["location"]=$row[csf("factory_location")];
			$tmp_arr[date("Y-m",strtotime($row[csf("shipment_date")]))][$row[csf("buyer_name")]][$row[csf("is_confirmed")]]["is_confirmed"] =$row[csf("is_confirmed")];
			$month_wise_total[date("Y-m",strtotime($row[csf("shipment_date")]))] +=$row[csf("allocation_qty")];
			$key=date("Y-m",strtotime($row[csf("shipment_date")])).$row[csf("factory_name")].$row[csf("is_confirmed")];
			
			
			if($row[csf("is_confirmed")]==1)
			{
				$row_data_dtls[date("Y-m",strtotime($row[csf("shipment_date")]))][$row[csf("buyer_name")]][date("Y-m-d",strtotime($row[csf("shipment_date")]))]+=$row[csf("allocation_qty")];
				$day_total_con[date("Y-m-d",strtotime($row[csf("shipment_date")]))]+=$row[csf("allocation_qty")];
				if(!in_array($key,$company_check) )
				{
					//echo $row[csf("factory_name")];
					//$row_span_arr[$row[csf("factory_name")]][$row[csf("is_confirmed")]]["rowspan"]+=1;
					//$company_check[]=$key;
					//echo 'sd'.'<br>';
					
				}
				if($company_check[$row[csf("factory_name")]]=='')
					{
						//$week_counter_header[date("Y-M",strtotime($row_week_header[csf("week_date")]))][$row_week_header[csf("week")]]=$row_week_header[csf("week")];
						//echo $row[csf("factory_name")];
						$row_span_arr[$row[csf("factory_name")]][$row[csf("is_confirmed")]]["rowspan"]+=1;
						$company_check[$row[csf("factory_name")]]=$key;
					
					}
	
			}
			else if($row[csf("is_confirmed")]==2)
			{
				$row_data_dtls_project[date("Y-m",strtotime($row[csf("shipment_date")]))][$row[csf("buyer_name")]][date("Y-m-d",strtotime($row[csf("shipment_date")]))]+=$row[csf("allocation_qty")];
				$day_total_proj[date("Y-m-d",strtotime($row[csf("shipment_date")]))]+=$row[csf("allocation_qty")];
				if($company_check[$row[csf("factory_name")]]=='')
					{
						//$week_counter_header[date("Y-M",strtotime($row_week_header[csf("week_date")]))][$row_week_header[csf("week")]]=$row_week_header[csf("week")];
						//echo $row[csf("factory_name")];
						$row_span_arr[$row[csf("factory_name")]][$row[csf("is_confirmed")]]["rowspan"]+=1;
						$company_check[$row[csf("factory_name")]]=$key;
					
					}
			}
			
			if($row[csf("is_confirmed")]==1 || $row[csf("is_confirmed")]==2)
			{
			$day_total_cons_all[date("Y-m-d",strtotime($row[csf("shipment_date")]))]+=$row[csf("allocation_qty")];
			if($company_check[$row[csf("factory_name")]]=='')
					{
						//$week_counter_header[date("Y-M",strtotime($row_week_header[csf("week_date")]))][$row_week_header[csf("week")]]=$row_week_header[csf("week")];
						//echo $row[csf("factory_name")];
						$row_span_arr[$row[csf("factory_name")]][$row[csf("is_confirmed")]]["rowspan"]+=1;
						$company_check[$row[csf("factory_name")]]=$key;
					
					}
			}
			
				
		}
		$allocated_poo=rtrim($allocated_poo,',');
		$allocated_poo= implode(',',array_unique(explode(",",$allocated_poo))); //die;
		//echo $allocated_poo;
		//print_r($row_span_arr);
		//var_dump($tmp_arr);die;
		
		
		ob_start();
?>
	
        <table width="1250">
            <tr class="form_caption">
                <td colspan="32" align="center" style="font-size:16px;">Date Wise Ship Quantity Report/<? echo $report_type_arr[$cbo_report_type];?></td>
            </tr>
            <tr class="form_caption">
                <td colspan="32" align="center" style="font-size:16px;"><? echo $company_library[$company_name]; ?></td>
            </tr>
        </table>
        
        <?
		foreach($tmp_arr as $date_key=>$buyer_id_arr)
		{
            $month_value=explode("-",$date_key);
			$num_days = cal_days_in_month(CAL_GREGORIAN, $month_value[1], $month_value[0]);
			//echo $num_days;die;
			//arsort($buyer_id_arr);
			//var_dump($date_key);die;
				?>
                <br />
                <table class="rpt_table" width="1170" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <tr bgcolor="#FFFFFF">
                        <td align="right" style="font-weight:bold;" width="84" >Month:</td>
                        <td style="font-weight:bold;" width="85">
                        <?
                        echo $months[$month_value[1]*1]."-".$month_value[0]; 
                        ?>
                        </td>
                        <td  style="font-weight:bold;" width="50" align="right">Total &nbsp; =</td>
                        <td  style="font-weight:bold;" colspan="32">&nbsp;
                        <? 
                            echo number_format($month_wise_total[$date_key],0)." "."Pcs"; 
                        ?>
                        </td>
                    </tr>
                </table>
               <table  width="1170" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                   <thead> 
                        <tr>
	                        <th width="50">Factory</th>
	                        <th width="50">Location</th>
                            <th width="80">Buyer</th>
                            <th width="80">Total</th>
                            <?
                            for($m=1;$m<=$num_days;$m++)
                            {
                               $day=($m<=9)? '0'.$m:$m;
							    //echo $m.'='.$day;
							  if($day_total_cons_all[$date_key."-".$day]>0){
								  
							    ?>
                                <th width="35"><? echo  ($m<=9)? '0'.$m:$m; ?></th>
                                <?
							   }
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
					<?
                    //($m<=9)? '0'.$m:$m.$month_value[1].$month_value[0];
					$k=1;$total_val="";$total_buyer_qty="";$total_month_qty=0;$row_span=0;$company_check=array();
                    foreach($buyer_id_arr as $buyer_id=>$is_confirm)
                    {
						foreach($is_confirm as  $key_confirm=>$row)
						{
							if ($k%2==0)
							$bgcolor="#E9F3FF";
							else
							$bgcolor="#FFFFFF";
						
							if($key_confirm==1)
							{
							/*	$c_total_exfact_qty_con=0;
								for($p=1;$p<=$num_days;$p++)
								{
									$day=($p<=9)? '0'.$p:$p;
									if($shipment_status_id==2) //Balance Qty

										{
											if($day_total_con[$date_key."-".$day]>0 || $day_total_proj[$date_key."-".$day]>0)
											{
												$c_total_exfact_qty_con+=$ex_fact_con_arr[$date_key][$buyer_id][$date_key."-".$day];	
											}
											
											//echo $date_key.'='.$buyer_id.'='.$date_key."-".$day.'<br>';
										}
										
								}*/
								$row_span=$row_span_arr[$row['factory']][$key_confirm]["rowspan"];
								//echo $row_span=$row_span_arr[$row['factory']][$key_confirm]["rowspan"];
								//echo $row_span;
								
							?>
							<tr bgcolor="<? echo $bgcolor ; ?>" style="font-family:'Arial Narrow'">
                          <? 
                          if (!in_array($row['factory'],$company_check) )
							{
							?>
                            	<td><? echo $company_library[$row['factory']]; ?></td>
                                <?
								$company_check[]=$row['factory'];
							}
							else
							{
								?>
								<td> <? // echo $i;?> </td>
                            	<?
							}
							
								?>
								<td><? echo $location_lib[$row['location']]; ?></td>
								<td><? echo $buyer_library[$row['buyer']]; ?></td>
								<td align="right"><? echo number_format($row['allocation_qty'],0); $total_buyer_qty+=$row['allocation_qty']; ?>&nbsp;</td>
								<?
								$exfact_qty_con=0;
								for($p=1;$p<=$num_days;$p++)
								{
									$day=($p<=9)? '0'.$p:$p;
									if($shipment_status_id==2) //Balance Qty
										{
											$exfact_qty_con=$ex_fact_con_arr[$date_key][$buyer_id][$date_key."-".$day];	
											//echo $date_key.'='.$buyer_id.'='.$date_key."-".$day.'<br>';
											$tot_bal_qty=$row_data_dtls[$date_key][$buyer_id][$date_key."-".$day]-$exfact_qty_con;
											if($tot_bal_qty<0) $days_qty=0;else $days_qty=$tot_bal_qty;
										}
										else
										{
											$days_qty=$row_data_dtls[$date_key][$buyer_id][$date_key."-".$day]-$exfact_qty_con;
										}
									  if($day_total_con[$date_key."-".$day]>0 || $day_total_proj[$date_key."-".$day]>0){
									
									//$ex_fact_con_arr[date("Y-m",strtotime($row[csf("ex_fact_date")]))][$row[csf("buyer_name")]][date("Y-m-d",strtotime($row[csf("ex_fact_date")]))];
									?>
									<td  style="height:20px;" title="<? echo $exfact_qty_con;?>" width="35" align="center"><div><? if($days_qty>0) echo number_format($days_qty,0); else echo "0"; ?></div></td>
									<?
									$total_val[$day] +=$days_qty;
									 }
								}
								
								?>
							</tr>
							<?
							}
							else if($key_confirm==2)
							{
								/*$c_total_exfact_qty_proj=0;
								for($p=1;$p<=$num_days;$p++)
								{
									$day=($p<=9)? '0'.$p:$p;
									if($shipment_status_id==2) //Balance Qty
										{
											 if($day_total_proj[$date_key."-".$day]>0 || $day_total_con[$date_key."-".$day]>0)
											{
												$c_total_exfact_qty_proj+=$ex_fact_proj_arr[$date_key][$buyer_id][$date_key."-".$day];	
											}
											
											//echo $date_key.'='.$buyer_id.'='.$date_key."-".$day.'<br>';
										}
										
								}*/
								?>
								<tr bgcolor="<? echo $bgcolor ; ?>" style="font-family:'Arial Narrow'">
                                	
		                                     <? 
					                          if (!in_array($row['factory'],$company_check) )
												{
												?>
					                            	<td><? echo $company_library[$row['factory']]; ?></td>
					                                <?
													$company_check[]=$row['factory'];
												}
												else
												{
													?>
													<td> <? // echo $i;?> </td>
					                            	<?
												}
									
											?>
                                    
                            		<td><? echo $location_lib[$row['location']]; ?></td>
									<td><? echo $buyer_library[$row['buyer']]."(bk)"; ?></td>
									<td  align="right"><? echo number_format($row['po_qty'],0); $total_buyer_qty+=$row['po_qty']; ?>&nbsp;</td>
									<?
									$exfact_qty_proj=0;
									for($p=1;$p<=$num_days;$p++)
									{
										$day=($p<=9)? '0'.$p:$p;
										if($shipment_status_id==2) //Balance Qty
										{
											$exfact_qty_proj=$ex_fact_proj_arr[$date_key][$buyer_id][$date_key."-".$day];
											$proj_tot_bal_qty=$row_data_dtls_project[$date_key][$buyer_id][$date_key."-".$day]-$exfact_qty_proj;
											if($proj_tot_bal_qty<0) $p_days_qty=0;else $p_days_qty=$proj_tot_bal_qty;	
										}
										else
										{
											
										}
										 if($day_total_proj[$date_key."-".$day]>0 || $day_total_con[$date_key."-".$day]>0){
										//echo $day.'a';
										?>
										<td  style="height:20px;" title="<? echo $exfact_qty_proj;?>" width="35" align="center" ><div><?  if($row_data_dtls_project[$date_key][$buyer_id][$date_key."-".$day] >0) echo number_format($p_days_qty-$exfact_qty_proj,0); else echo "0";  ?></div></td>
										<?
										$total_val[$day] +=$p_days_qty-$exfact_qty_proj;
										}
									}
									
									?>
								</tr>
								<?	
							}
							$k++;//font-weight:bold; 
						}
                    }
					?>
                    	<tr bgcolor="#CCCCCC" style="font-family:'Arial Narrow'">
                        	<td  align="right" style="font-weight:bold;">&nbsp;</td>
                        	<td  align="right" style="font-weight:bold;">&nbsp;</td>
                            <td  align="right" style="font-weight:bold;">Total:</td>

                            <td  align="right" style="font-weight:bold;"><?  echo number_format($total_buyer_qty,0); ?>&nbsp;</td>
                            <?
                            for($p=1;$p<=$num_days;$p++)
                            {
								//echo $day.'z';
								$day=($p<=9)? '0'.$p:$p;
								if($day_total_cons_all[$date_key."-".$day]>0){
								//echo $day.'z';
                                ?>
                                <td  style="height:20px;" width="35"  align="center" ><div><?  echo number_format($total_val[$day],0); ?></div></td>
                                <?
								//$total_val[]
								 }
                            }
                            ?>
                        </tr>
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                    </tbody>
             </table>
             <?
		}
		
		
	//query for unalocated qty.

/*$data_array_unalocate="select
					  a.id as job_id,
					  a.job_no, 
					  a.company_name, 
					  a.buyer_name,
					  b.id as color_break_id, 
					  b.po_break_down_id, 
					  b.order_quantity as po_quantity, 
					  b.order_quantity as po_quantity_pcs, 
					  c.po_quantity as po_qty, 
					  b.country_ship_date, 
					  b.cutup_date, 
					  b.order_total,
					  c.is_confirmed, 
					  d.company_id as factory_name,
					  d.location_name as factory_location, 
					  e.qty as allocation_qty,
					  d.shipment_date,
					  e.date_name,
					  d.po_no as allocate_po_no  

				from 
					wo_po_details_master a,
					wo_po_color_size_breakdown b,
					wo_po_break_down c, 
					ppl_order_allocation_mst d, 
					ppl_order_allocation_dtls e
			
				where  
					a.job_no=b.job_no_mst
					and b.po_break_down_id=c.id
					$company_con  
					$date_cond
					$buyer_cond 
					$order_status_cond 
					$shipment_status_cond
					$season_cond 
					and c.id=d.po_no 
					and d.id=e.mst_id
					and a.job_no=d.job_no 
					and d.status_active=1 and d.is_deleted=0 
					and e.status_active=1 and e.is_deleted=0 
					and c.id not in(select po_no from ppl_order_allocation_mst)   
			 order by d.company_id,a.buyer_name desc";*/
	 $data_array_unalocate="select
					 a.id as job_id,
					 a.job_no, 
					 a.company_name, 
					 a.buyer_name,
					 a.total_set_qnty, 
					 b.id as color_break_id, 
					 b.po_break_down_id, 
					 b.order_quantity as allocation_qty,
					 b.order_quantity as po_quantity, 
					 b.order_quantity as po_quantity_pcs, 
					 c.po_quantity as po_qty, 
					 b.country_ship_date, 
					 b.cutup_date, 
					 b.order_total,
					 c.is_confirmed
			from 
					wo_po_details_master a, 
					wo_po_color_size_breakdown b,
					wo_po_break_down c 
			where  
					a.job_no=b.job_no_mst
					and b.po_break_down_id=c.id 
					$company_con
					$date_cond
					$buyer_cond 
					$order_status_cond 
					$shipment_status_cond
					$season_cond 
					and c.id not in(select po_no from ppl_order_allocation_mst) 
					
			order by 
					 $order_by_cond DESC";
					 
					 //echo $data_array_unalocate;
					 
				//order by d.company_id, d.location_name";
		////order by  $order_by_cond,c.is_confirmed ASC";
	//echo $data_array_unalocate;//die;
	$result_po=sql_select($data_array_unalocate);
	//$allocated_po=array();
	//$allocated_poo="";
	$row_data_dtls=array();//$company_check=array();
	$tmp_arr=array();$row_span_arr=array();
	//print_r($row_span_arr);
	//$num_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
	//$kk=1;
		foreach($result_po as $row)
		{
			if($cbo_date_category==1)
			{
				$row[csf("shipment_date")]=$row[csf("country_ship_date")];
			}
			else //Cut-Off
			{
				$row[csf("shipment_date")]=$row[csf("cutup_date")];	
			}
		  	//$allocated_poo.= $row[csf("allocate_po_no")].",";
			//$allocated_po[]=$row[csf("allocate_po_no")];
			$tmp_arr[date("Y-m",strtotime($row[csf("shipment_date")]))][$row[csf("buyer_name")]][$row[csf("is_confirmed")]]["po_qty"]+=$row[csf("po_quantity_pcs")];
			$tmp_arr[date("Y-m",strtotime($row[csf("shipment_date")]))][$row[csf("buyer_name")]][$row[csf("is_confirmed")]]["allocation_qty"]+=$row[csf("allocation_qty")];
			$tmp_arr[date("Y-m",strtotime($row[csf("shipment_date")]))][$row[csf("buyer_name")]][$row[csf("is_confirmed")]]["buyer"] =$row[csf("buyer_name")];
			$tmp_arr[date("Y-m",strtotime($row[csf("shipment_date")]))][$row[csf("buyer_name")]][$row[csf("is_confirmed")]]["factory"]=$row[csf("factory_name")];
			$tmp_arr[date("Y-m",strtotime($row[csf("shipment_date")]))][$row[csf("buyer_name")]][$row[csf("is_confirmed")]]["location"]=$row[csf("factory_location")];
			$tmp_arr[date("Y-m",strtotime($row[csf("shipment_date")]))][$row[csf("buyer_name")]][$row[csf("is_confirmed")]]["is_confirmed"] =$row[csf("is_confirmed")];
			$month_wise_total_new[date("Y-m",strtotime($row[csf("shipment_date")]))] +=$row[csf("allocation_qty")];
			$key=date("Y-m",strtotime($row[csf("shipment_date")])).$row[csf("factory_name")].$row[csf("is_confirmed")];
			
			
			if($row[csf("is_confirmed")]==1)
			{
				$row_data_dtls[date("Y-m",strtotime($row[csf("shipment_date")]))][$row[csf("buyer_name")]][date("Y-m-d",strtotime($row[csf("shipment_date")]))]+=$row[csf("allocation_qty")];
				$day_total_con[date("Y-m-d",strtotime($row[csf("shipment_date")]))]+=$row[csf("allocation_qty")];
				if(!in_array($key,$company_check) )
				{
					//echo $row[csf("factory_name")];
					//$row_span_arr[$row[csf("factory_name")]][$row[csf("is_confirmed")]]["rowspan"]+=1;
					//$company_check[]=$key;
					//echo 'sd'.'<br>';
					
				}
				if($company_check[$row[csf("factory_name")]]=='')
					{
						//$week_counter_header[date("Y-M",strtotime($row_week_header[csf("week_date")]))][$row_week_header[csf("week")]]=$row_week_header[csf("week")];
						//echo $row[csf("factory_name")];
						$row_span_arr[$row[csf("factory_name")]][$row[csf("is_confirmed")]]["rowspan"]+=1;
						$company_check[$row[csf("factory_name")]]=$key;
					
					}
	
			}
			else if($row[csf("is_confirmed")]==2)
			{
				$row_data_dtls_project[date("Y-m",strtotime($row[csf("shipment_date")]))][$row[csf("buyer_name")]][date("Y-m-d",strtotime($row[csf("shipment_date")]))]+=$row[csf("allocation_qty")];
				$day_total_proj[date("Y-m-d",strtotime($row[csf("shipment_date")]))]+=$row[csf("allocation_qty")];
				if($company_check[$row[csf("factory_name")]]=='')
					{
						//$week_counter_header[date("Y-M",strtotime($row_week_header[csf("week_date")]))][$row_week_header[csf("week")]]=$row_week_header[csf("week")];
						//echo $row[csf("factory_name")];
						$row_span_arr[$row[csf("factory_name")]][$row[csf("is_confirmed")]]["rowspan"]+=1;
						$company_check[$row[csf("factory_name")]]=$key;
					
					}
			}
			
			if($row[csf("is_confirmed")]==1 || $row[csf("is_confirmed")]==2)
			{
			$day_total_cons_all[date("Y-m-d",strtotime($row[csf("shipment_date")]))]+=$row[csf("allocation_qty")];
			if($company_check[$row[csf("factory_name")]]=='')
					{
						//$week_counter_header[date("Y-M",strtotime($row_week_header[csf("week_date")]))][$row_week_header[csf("week")]]=$row_week_header[csf("week")];
						//echo $row[csf("factory_name")];
						$row_span_arr[$row[csf("factory_name")]][$row[csf("is_confirmed")]]["rowspan"]+=1;
						$company_check[$row[csf("factory_name")]]=$key;
					
					}
			}
			
				
		}
		//$allocated_poo=rtrim($allocated_poo,',');
		//$allocated_poo= implode(',',array_unique(explode(",",$allocated_poo))); //die;
		//print_r($row_span_arr);
		//var_dump($tmp_arr);die;
		
		
		
?>
	
        <table width="1250">
            <tr class="form_caption">
                <td colspan="32" align="center" style="font-size:16px;">Unallocated<? //echo $report_type_arr[$cbo_report_type];?></td>
            </tr>
           <!-- <tr class="form_caption">
                <td colspan="32" align="center" style="font-size:16px;"><? //echo $company_library[$company_name]; ?></td>
            </tr>-->
        </table>
        
        <?
		foreach($tmp_arr as $date_key=>$buyer_id_arr)
		{
            $month_value=explode("-",$date_key);
			$num_days = cal_days_in_month(CAL_GREGORIAN, $month_value[1], $month_value[0]);
			//echo $num_days;die;
			//arsort($buyer_id_arr);
			//var_dump($date_key);die;
				?>
                <br />
                <table class="rpt_table" width="1170" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <tr bgcolor="#FFFFFF">
                        <td align="right" style="font-weight:bold;" width="84" >Month:</td>
                        <td style="font-weight:bold;" width="85">
                        <?
                          	echo $months[$month_value[1]*1]."-".$month_value[0]; 
                        ?>
                        </td>
                        <td  style="font-weight:bold;" width="50" align="right">Total &nbsp; =</td>
                        <td  style="font-weight:bold;" colspan="32">&nbsp;
                        <? 
                            echo number_format($month_wise_total_new[$date_key],0)." "."Pcs"; 
                        ?>
                        </td>
                    </tr>
                </table>
               <table  width="1170" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                   <thead> 
                        <tr>
	                        <th width="50">Factory</th>
	                        <th width="50">Location</th>
                            <th width="80">Buyer</th>
                            <th width="80">Total</th>
                            <?
                            for($m=1;$m<=$num_days;$m++)
                            {
                               $day=($m<=9)? '0'.$m:$m;
							    //echo $m.'='.$day;
							  if($day_total_cons_all[$date_key."-".$day]>0){
								  
							    ?>
                                <th width="35"><? echo  ($m<=9)? '0'.$m:$m; ?></th>
                                <?
							   }
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
					<?
                    //($m<=9)? '0'.$m:$m.$month_value[1].$month_value[0];
					$k=1;$total_val="";$total_buyer_qty="";$total_month_qty=0;$row_span=0;$company_check=array();
                    foreach($buyer_id_arr as $buyer_id=>$is_confirm)
                    {
						foreach($is_confirm as  $key_confirm=>$row)
						{
							if ($k%2==0)
							$bgcolor="#E9F3FF";
							else
							$bgcolor="#FFFFFF";
						
							if($key_confirm==1)
							{
							/*	$c_total_exfact_qty_con=0;
								for($p=1;$p<=$num_days;$p++)
								{
									$day=($p<=9)? '0'.$p:$p;
									if($shipment_status_id==2) //Balance Qty
										{
											if($day_total_con[$date_key."-".$day]>0 || $day_total_proj[$date_key."-".$day]>0)
											{
												$c_total_exfact_qty_con+=$ex_fact_con_arr[$date_key][$buyer_id][$date_key."-".$day];	
											}
											
											//echo $date_key.'='.$buyer_id.'='.$date_key."-".$day.'<br>';
										}
										
								}*/
								$row_span=$row_span_arr[$row['factory']][$key_confirm]["rowspan"];
								//echo $row_span=$row_span_arr[$row['factory']][$key_confirm]["rowspan"];
								//echo $row_span;
								
							?>
							<tr bgcolor="<? echo $bgcolor ; ?>" style="font-family:'Arial Narrow'">
                          <? 
                          if (!in_array($row['factory'],$company_check) )
							{
							?>
                            	<td><? echo $company_library[$row['factory']]; ?></td>
                                <?
								$company_check[]=$row['factory'];
							}
							else
							{
								?>
								<td> <? // echo $i;?> </td>
                            	<?
							}
							
								?>
								<td><? echo $location_lib[$row['location']]; ?></td>
								<td><? echo $buyer_library[$row['buyer']]; ?></td>
								<td align="right"><? echo number_format($row['allocation_qty'],0); $total_buyer_qty+=$row['allocation_qty']; ?>&nbsp;</td>
								<?
								$exfact_qty_con=0;
								for($p=1;$p<=$num_days;$p++)
								{
									$day=($p<=9)? '0'.$p:$p;
									if($shipment_status_id==2) //Balance Qty
										{
											$exfact_qty_con=$ex_fact_con_arr[$date_key][$buyer_id][$date_key."-".$day];	
											//echo $date_key.'='.$buyer_id.'='.$date_key."-".$day.'<br>';
											
											$tot_bal_qty=$row_data_dtls[$date_key][$buyer_id][$date_key."-".$day]-$exfact_qty_con;
											if($tot_bal_qty<0) $days_qty=0;else $days_qty=$tot_bal_qty;
										}
										else
										{
											$days_qty=$row_data_dtls[$date_key][$buyer_id][$date_key."-".$day]-$exfact_qty_con;
										}
									  if($day_total_con[$date_key."-".$day]>0 || $day_total_proj[$date_key."-".$day]>0){
									
									//$ex_fact_con_arr[date("Y-m",strtotime($row[csf("ex_fact_date")]))][$row[csf("buyer_name")]][date("Y-m-d",strtotime($row[csf("ex_fact_date")]))];
									?>
									<td  style="height:20px;" title="<? echo $exfact_qty_con;?>" width="35" align="center"><div><? if($row_data_dtls[$date_key][$buyer_id][$date_key."-".$day]>0) echo number_format($days_qty,0); else echo "0"; ?></div></td>
									<?
									$total_val[$day] +=$days_qty;
									 }
								}
								
								?>
							</tr>
							<?
							}
							else if($key_confirm==2)
							{
								/*$c_total_exfact_qty_proj=0;
								for($p=1;$p<=$num_days;$p++)
								{
									$day=($p<=9)? '0'.$p:$p;
									if($shipment_status_id==2) //Balance Qty
										{
											 if($day_total_proj[$date_key."-".$day]>0 || $day_total_con[$date_key."-".$day]>0)
											{
												$c_total_exfact_qty_proj+=$ex_fact_proj_arr[$date_key][$buyer_id][$date_key."-".$day];	
											}
											
											//echo $date_key.'='.$buyer_id.'='.$date_key."-".$day.'<br>';
										}
										
								}*/
								?>
								<tr bgcolor="<? echo $bgcolor ; ?>" style="font-family:'Arial Narrow'">
                                	
		                                     <? 
					                          if (!in_array($row['factory'],$company_check) )
												{
												?>
					                            	<td><? echo $company_library[$row['factory']]; ?></td>
					                                <?
													$company_check[]=$row['factory'];
												}
												else
												{
													?>
													<td> <? // echo $i;?> </td>
					                            	<?
												}
									
											?>
                                    
                            		<td><? echo $location_lib[$row['location']]; ?></td>
									<td><? echo $buyer_library[$row['buyer']]."(bk)"; ?></td>
									<td  align="right"><? echo number_format($row['po_qty'],0); $total_buyer_qty+=$row['po_qty']; ?>&nbsp;</td>
									<?
									$exfact_qty_proj=0;
									for($p=1;$p<=$num_days;$p++)
									{
										$day=($p<=9)? '0'.$p:$p;
										if($shipment_status_id==2) //Balance Qty
										{
											$exfact_qty_proj=$ex_fact_proj_arr[$date_key][$buyer_id][$date_key."-".$day];
											
											$proj_tot_bal_qty=$row_data_dtls_project[$date_key][$buyer_id][$date_key."-".$day]-$exfact_qty_proj;
											if($proj_tot_bal_qty<0) $proj_days_qty=0;else $proj_days_qty=$proj_tot_bal_qty;	
										}
										else
										{
											
										}
										 if($day_total_proj[$date_key."-".$day]>0 || $day_total_con[$date_key."-".$day]>0){
										//echo $day.'a';
										?>
										<td  style="height:20px;" title="<? echo $exfact_qty_proj;?>" width="35" align="center" ><div><?  if($row_data_dtls_project[$date_key][$buyer_id][$date_key."-".$day] >0) echo number_format($proj_days_qty,0); else echo "0";  ?></div></td>
										<?
										$total_val[$day] +=$proj_days_qty;
										}
									}
									
									?>
								</tr>
								<?	
							}
							$k++;//font-weight:bold; 
						}
                    }
					?>
                    	<tr bgcolor="#CCCCCC" style="font-family:'Arial Narrow'">
                        	<td  align="right" style="font-weight:bold;">&nbsp;</td>
                        	<td  align="right" style="font-weight:bold;">&nbsp;</td>
                            <td  align="right" style="font-weight:bold;">Total:</td>

                            <td  align="right" style="font-weight:bold;"><?  echo number_format($total_buyer_qty,0); ?>&nbsp;</td>
                            <?
                            for($p=1;$p<=$num_days;$p++)
                            {
								//echo $day.'z';
								$day=($p<=9)? '0'.$p:$p;
								if($day_total_cons_all[$date_key."-".$day]>0){
								//echo $day.'z';
                                ?>
                                <td  style="height:20px;" width="35"  align="center" ><div><?  echo number_format($total_val[$day],0); ?></div></td>
                                <?
								//$total_val[]
								 }
                            }
                            ?>
                        </tr>
                    </tbody>
             </table>
             <?
		}
		
	
	// $sql_data="select c.location_id,c.line_id,c.plan_qnty,c.plan_id,c.start_hour,c.end_hour,c.duration,$lead_day,c.comp_level,c.first_day_output,c.increment_qty,c.terget,c.day_wise_plan,c.company_id,c.item_number_id ,c.off_day_plan,c.extra_param,  b.id as po_id,b.pub_shipment_date,b.po_quantity,c.start_date,c.end_date, b.po_number,a.buyer_name,a.job_no_prefix_num,a.style_ref_no as style 
		//from  wo_po_break_down b,wo_po_details_master a, ppl_sewing_plan_board c,lib_sewing_line e where  a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.company_id=$company_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.line_id=e.id  $buyer_id_cond $po_cond $date_cond $job_no_cond $location_id_cond
		//order by e.line_name";
		
		// $sql_unplan=("select $lead_day, b.id as po_id,b.pub_shipment_date,b.po_quantity,b.po_number,a.buyer_name,a.job_no_prefix_num,a.style_ref_no as style 
		//from  wo_po_break_down b,wo_po_details_master a where  a.job_no=b.job_no_mst   and a.company_name=$company_id and b.id not in(select a.po_break_down_id  from ppl_sewing_plan_board a,ppl_sewing_plan_board_dtls d where  a.plan_id=d.plan_id and status_active=1 $date_cond ) and b.status_active=1 and b.is_deleted=0  $buyer_id_cond $po_cond $job_no_cond $location_id_cond ");
	
	
	
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
	echo "$total_data****$filename";
	exit();
	disconnect($con);
}

if($action=="smv_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("SMV Info", "../../../../", 1, 1,'','','');
	?>
    </head>
    <body>
        <div align="center">
            <fieldset style="width:450px;">
                <table width="430" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" style="float: left">
                	<thead>
                		<tr>
                			<th width="30">Sl No.</th>
                			<th width="130">Item</th>
                            <th width="50">Set Ratio</th>
                			<th width="50">Sew SMV/ Pcs</th>
                            <th width="80">Order Qty (Pcs)</th>
                            <th>SMV Minute</th>
                		</tr>
                	</thead>
                </table>
                <div style="width:448px; max-height:350px; overflow-y:scroll" id="scroll_body" > 
	                <table cellspacing="0" width="430"  border="1" rules="all" class="rpt_table" id="tbl_list" style="float: left">
	                	<tbody>
	                		<?
							$sql_qty="select job_no_mst, item_number_id, order_quantity from wo_po_color_size_breakdown where job_no_mst='$job_no' and po_break_down_id='$po_id' and status_active=1 and is_deleted=0";
							$qty_result=sql_select($sql_qty);
							$po_qty_arr=array();
							foreach($qty_result as $row)
							{
								$po_qty_arr[$row[csf("item_number_id")]]+=$row[csf("order_quantity")];
							}
							unset($qty_result);
	                		$result = sql_select("select id, set_item_ratio,smv_set, smv_pcs, gmts_item_id from wo_po_details_mas_set_details where job_no = '$job_no' ");
							
	                		$i=1;
	                		foreach ($result as $value) 
	                		{
								$po_qty=0; $smv_min=0;
								$po_qty=$po_qty_arr[$value[csf("gmts_item_id")]];
								//$smv_min=$value[csf("smv_pcs")]*$po_qty;
								$smv_mins= ($value[csf("smv_set")]/$value[csf("set_item_ratio")])*$po_qty;
								$smv_min_type = gettype($smv_mins);
								$smv_min = ($smv_min_type=="double") ? number_format($smv_mins,2) : $smv_mins;
		                		?>
		                		<tr>
		                			<td align="center" width="30"><? echo $i;?></td>
		                			<td align="center" width="130"><? echo $garments_item[$value[csf("gmts_item_id")]];?></td>
                                    <td align="center" width="50"><? 
                                    $ratio = $value[csf("set_item_ratio")];
                                    $ratio_type = gettype($ratio);
                                    echo ($ratio_type=="double") ? number_format($ratio,2) : $ratio;
                                    ?></td>
		                			<td align="center" width="50"><? 
		                			$itmRatio = $value[csf("smv_set")]/$value[csf("set_item_ratio")];
		                			$itmRatios = gettype($itmRatio);
                                    echo ($itmRatios=="double") ? number_format($itmRatio,2) : $itmRatio;
		                			?></td>
                                    <td align="center" width="80"><? echo $po_qty;?></td>
                                    <td align="center"><? echo $smv_min;?></td>
		                		</tr>
		                		<?  $tot_poqty+=$po_qty;
									$tot_smv_min+=$smv_min;
		                		$i++;
								
	                		}
	                		?>
	                	</tbody>
	                </table>
            	</div>
                <table width="430" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" style="float: left">
                	<thead>
                		<tr>
                			<th width="30">&nbsp;</th>
                			<th width="130">&nbsp;</th>
                            <th width="50">&nbsp;</th>
                			<th width="50">Total:</th>
                            <th width="80" align="right"><? echo $tot_poqty;?></th>
                            <th align="right"><? echo $tot_smv_min;?></th>
                		</tr>
                        <tr>
                			<th width="30">&nbsp;</th>
                			<th width="130">&nbsp;</th>
                            <th width="50">&nbsp;</th>
                			<th width="50">&nbsp;</th>
                            <th width="80">Average SMV:</th>
                            <th align="right"><? echo number_format(($tot_smv_min/$tot_poqty),3);?></th>
                		</tr>
                	</thead>
                </table>
            </fieldset>
        </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>
    	setFilterGrid('tbl_list',-1);
    </script>
    </html>
    <?
	exit(); 
}
?>