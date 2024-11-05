<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Style Wise materials Follow up Report (Woven)
Functionality	:	
JS Functions	:
Created by		:	Zakaria joy
Creation date 	: 	22-05-2021
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.fabrics.php');
require_once('../../../../includes/class4/class.trims.php');
require_once('../../../../includes/class4/class.others.php');
$user_id = $_SESSION['logic_erp']["user_id"];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$fabric_nature = $_SESSION['fabric_nature'];
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');
if($db_type==2) $select_date=" to_char(a.insert_date,'YYYY')";
else if ($db_type==0) $select_date=" year(a.insert_date)";

//--------------------------------------------------------------------------------------------------------------------
if($action=="report_formate_setting")
    {
        $print_report_format_arr = return_library_array("select format_id,format_id from lib_report_template where template_name =$data and module_id=2 and report_id=242 and is_deleted=0 and status_active=1","format_id","format_id");
		// print_r($print_report_format_arr);die;
        echo "print_report_button_setting('".implode(',',$print_report_format_arr)."');\n";
        exit();
    }

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "load_drop_down( 'requires/accessories_followup_budget2_report_controller', this.value, 'load_drop_down_season', 'season_td');" );
	exit();
}

if ($action=="load_drop_down_season")
{
	echo create_drop_down( "cbo_season_id", 130, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 group by id, season_name order by season_name ASC","id,season_name", 1, "-Select Season-", "", "" );
	exit();
}

if ($action=="style_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data); 
	?>
	 <script>
	var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
	function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		function check_all_data()
		{
			var row_num=$('#list_view tr').length-1;
			for(var i=1;  i<=row_num;  i++)
			{
				$("#tr_"+i).click();
			}
			
		}
	function js_set_value(id)
	{
		var str=id.split("_");
		toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
		var strdt=str[2];
		str=str[1];
	
		if( jQuery.inArray(  str , selected_id ) == -1 ) {
			selected_id.push( str );
			selected_name.push( strdt );
		}
		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == str  ) break;
			}
			selected_id.splice( i, 1 );
			selected_name.splice( i,1 );
		}
		var id = '';
		var ddd='';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
			ddd += selected_name[i] + ',';
		}
		id = id.substr( 0, id.length - 1 );
		ddd = ddd.substr( 0, ddd.length - 1 );
		$('#txt_po_id').val( id );
		$('#txt_po_val').val( ddd );
	} 
	</script>
 <input type="hidden" id="txt_po_id" />
 <input type="hidden" id="txt_po_val" />
     <?
	if ($data[0]==0) $company_name=""; else $company_name="company_name='$data[0]'";
	if ($data[1]==0) $buyer_name=""; else $buyer_name=" and buyer_name='$data[1]'";
	if($db_type==0) $year_field="YEAR(insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year";
	else $year_field="";
	//$type_id=str_replace("'","",$type_id);
	//echo $data[2].'d,';
	if($data[2]==1) $type_con="id,job_prefix";else $type_con="id,style_ref_no";
	$sql ="select id,style_ref_no,job_no_prefix_num as job_prefix,$year_field from wo_po_details_master where $company_name $buyer_name"; 
	echo create_list_view("list_view", "Style Ref. No.,Job No,Year","200,100,100","450","310",0, $sql , "js_set_value", "$type_con", "", 1, "0", $arr, "style_ref_no,job_prefix,year", "","setFilterGrid('list_view',-1)","0","",1) ;	
	exit();	 
}
if ($action=="order_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	$company=$data[0];
	$buyer=$data[1];
	$style=$data[2];
	
	//print ($data[1]);
	?>
	 <script>
	var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
	 
	function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		function check_all_data()
		{
			var row_num=$('#list_view tr').length-1;
			for(var i=1;  i<=row_num;  i++)
			{
				$("#tr_"+i).click();
			}
			
		}
		
	function js_set_value(id)
	{ //alert(id);
		var str=id.split("_");
		toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
		var strdt=str[2];
		str=str[1];
	
		if( jQuery.inArray(  str , selected_id ) == -1 ) {
			selected_id.push( str );
			selected_name.push( strdt );
		}
		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == str  ) break;
			}
			selected_id.splice( i, 1 );
			selected_name.splice( i,1 );
		}
		var id = '';
		var ddd='';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
			ddd += selected_name[i] + ',';
		}
		id = id.substr( 0, id.length - 1 );
		ddd = ddd.substr( 0, ddd.length - 1 );
		$('#txt_po_id').val( id );
		$('#txt_po_val').val( ddd );
	} 
	</script>
      <input type="hidden" id="txt_po_id" />
     <input type="hidden" id="txt_po_val" />
 <?
	if ($data[0]==0) $company_id=""; else $company_id=" and company_name=$data[0]";
	if ($data[1]==0) $buyer_id=""; else $buyer_id=" and buyer_name=$data[1]";
	if ($data[2]==0) $style=""; else $style=" and b.id in($data[2])";
	
	/*if($db_type==0) $year_cond="and year(a.insert_date)='$data[3]'"; 
	else if($db_type==2) $year_cond="and to_char(a.insert_date,'YYYY')='$data[3]'";*/
	
	if($db_type==0) $year_field="YEAR(b.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(b.insert_date,'YYYY') as year";
	else $year_field="";
	
	//$sql ="select distinct a.id,a.po_number,b.job_no_prefix_num as job_prefix,$year_field from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no $company_id  $buyer_id $style $year_cond";
	
	$sql ="select a.id,a.po_number,b.job_no_prefix_num as job_prefix,$year_field from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.is_deleted=0 $company_id  $buyer_id $style";
	
	//echo $sql;
	 
	echo create_list_view("list_view", "Order Number,Job No, Year","150,100,50","440","310",0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "po_number,job_prefix,year", "","setFilterGrid('list_view',-1)","0","",1) ;	
	exit();
}

$tmplte=explode("**",$data);
if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;
if($action=="report_generate2")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
	$country_name_library=return_library_array( "select id, country_name from lib_country", "id", "country_name"  );
	$lib_supplier_arr=return_library_array( "select id, short_name from lib_supplier", "id", "short_name"  );
	$lib_season_arr=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name"  );
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");

	$company_name=str_replace("'","",$cbo_company_name);
	$buyer_id_cond="";
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
				$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
	}

	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_job_no=trim($txt_job_no);

	if($txt_job_no!="" || $txt_job_no!=0) $jobcond="and a.job_no_prefix_num in($txt_job_no)"; else $jobcond="";
	if(str_replace("'","",$txt_style_ref)!="") $style_ref_cond=" and a.style_ref_no like '%".str_replace("'","",$txt_style_ref)."%'"; else $style_ref_cond="";


	$date_type=str_replace("'","",$cbo_date_type);

	$date_cond='';
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		$start_date=(str_replace("'","",$txt_date_from));
		$end_date=(str_replace("'","",$txt_date_to));
		$date_cond="and b.pub_shipment_date between '$start_date' and '$end_date'";
	}

	$cbo_year=str_replace("'","",$cbo_year);
	if($db_type==0)
	{
		if(trim($cbo_year)!=0) $year_cond=" and YEAR(a.insert_date)=$cbo_year"; else $year_cond="";
	}
	else if($db_type==2)
	{
		if(trim($cbo_year)!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year"; else $year_cond="";
	}

	$main_data_sql=sql_select("SELECT a.id, a.job_no, a.buyer_name, a.job_no_prefix_num, a.season_buyer_wise, a.style_ref_no, a.job_quantity, a.order_uom, b.id as po_id, b.po_number, c.color_number_id, c.order_quantity, e.id as fabric_cost_id, e.lib_yarn_count_deter_id,e.body_part_id, e.fabric_description, e.uom as fabric_uom, avg(f.requirment) as avg_cons, e.color_size_sensitive, e.color, e.color_break_down, g.contrast_color_id, h.stripe_color from wo_po_details_master a join wo_po_break_down b on a.id=b.job_id join wo_po_color_size_breakdown c on a.id=c.job_id and b.id=c.po_break_down_id join wo_pre_cost_mst d on a.id=d.job_id join wo_pre_cost_fabric_cost_dtls e on a.id=e.job_id join wo_pre_cos_fab_co_avg_con_dtls f on e.id=f.pre_cost_fabric_cost_dtls_id left join wo_pre_cos_fab_co_color_dtls g on a.id=g.job_id and e.id=g.pre_cost_fabric_cost_dtls_id and c.color_number_id =g.gmts_color_id and g.status_active=1 and g.is_deleted=0 left join wo_pre_stripe_color h on a.id=h.job_id and  c.item_number_id= h.item_number_id and e.id=h.pre_cost_fabric_cost_dtls_id and f.color_number_id =h.color_number_id and f.po_break_down_id=h.po_break_down_id and f.gmts_sizes=h.size_number_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.company_name=$company_name $year_cond $date_cond $style_ref_cond $jobcond $buyer_id_cond group by a.id, a.job_no, a.buyer_name, a.job_no_prefix_num, a.season_buyer_wise, a.style_ref_no, a.job_quantity, a.order_uom, b.id, c.color_number_id, c.order_quantity, e.lib_yarn_count_deter_id,e.body_part_id, e.fabric_description, e.uom, e.id, e.color_size_sensitive, e.color, e.color_break_down,g.contrast_color_id, h.stripe_color, b.po_number order by e.id asc");

    if(count($main_data_sql) > 0) {
        $main_attribute = array('job_no', 'buyer_name', 'job_no_prefix_num', 'season_buyer_wise', 'style_ref_no', 'job_quantity', 'order_uom');
        foreach ($main_data_sql as $row) {
            foreach ($main_attribute as $attr) {
                $main_data_arr[$row[csf('id')]][$attr] = $row[csf($attr)];
            }
            $fabricColorId = $row[csf('stripe_color')];
            if (!$fabricColorId) {
                $fabricColorId = $row[csf('contrast_color_id')];
            }
            if (!$fabricColorId) {
                $fabricColorId = $row[csf('color_number_id')];
            }
			 $jobIdArr[$row[csf('po_id')]] = $row[csf('id')];
			 
            $main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['item_des'] = $body_part[$row[csf('body_part_id')]] . ',' . $row[csf('fabric_description')];
            $main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['color_id'] = $row[csf('color_number_id')];
            $main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['fabric_uom'] = $row[csf('fabric_uom')];
            $main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['po_number'] = $row[csf('po_number')];
			$main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['po_id'] = $row[csf('po_id')];
			$main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['po_ids'].= $row[csf('po_id')].",";
            $main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['avg_cons'] = $row[csf('avg_cons')];
            $main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['sensitive'] = $row[csf('color_size_sensitive')];
            $main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['color'] = $fabricColorId;
            $job_id_arr[$row[csf('id')]] = $row[csf('id')];
            $fabric_id_arr[$row[csf('fabric_cost_id')]] = $row[csf('fabric_cost_id')];
            $po_id_arr[$row[csf('po_id')]] = $row[csf('po_id')];
        }
        /*echo '<pre>';
        print_r($main_data_arr); die;*/
        $po_id = array_chunk($po_id_arr, 1000, true);
        $order_cond = "";
        $po_cond_for_in2 = "";
        $ji = 0;
        foreach ($po_id as $key => $value) {
            if ($ji == 0) {
                $order_cond = " and c.po_breakdown_id  in(" . implode(",", $value) . ")";
                $po_cond_for_in2 = " and a.po_breakdown_id  in(" . implode(",", $value) . ")";
            } else {
                $order_cond .= " or c.po_breakdown_id  in(" . implode(",", $value) . ")";
                $po_cond_for_in2 .= " or a.po_breakdown_id  in(" . implode(",", $value) . ")";
            }
            $ji++;
        }
        $job_id_chunk = array_chunk($job_id_arr, 1000, true);
        $jobid_cond = "";
        $jobid_cond1 = "";
        $jobid_cond3 = "";
        $i = 0;
        foreach ($job_id_chunk as $key => $value) {
            if ($i == 0) {
                $jobid_cond = " and b.job_id  in(" . implode(",", $value) . ")";
                $jobid_cond1 = " and a.job_id  in(" . implode(",", $value) . ")";
                $jobid_cond3 = " and job_id  in(" . implode(",", $value) . ")";
                $jobid_cond4 = " and i.job_id  in(" . implode(",", $value) . ")";
            } else {
                $jobid_cond .= " or b.job_id  in(" . implode(",", $value) . ")";
                $jobid_cond1 .= " or a.job_id  in(" . implode(",", $value) . ")";
                $jobid_cond3 .= " or job_id  in(" . implode(",", $value) . ")";
                $jobid_cond4 .= " or i.job_id  in(" . implode(",", $value) . ")";
            }
            $i++;
        }

        $rowspan = array();
        foreach ($main_data_arr as $job_id => $jod_arr) {
            foreach ($jod_arr['color_data'] as $color_data) {
                foreach ($color_data['fabric_color'] as $row) {
                    $rowspan[$job_id]++;
                }
            }
        }
        $fabric_id_str = implode(",", $fabric_id_arr);

        $wo_data_sql = sql_select("SELECT a.job_id, a.lib_yarn_count_deter_id, b.id, b.booking_date, b.booking_no, c.gmts_color_id, c.fin_fab_qnty, c.amount,	b.supplier_id, c.fabric_color_id FROM wo_pre_cost_fabric_cost_dtls a join wo_booking_dtls c on a.id=c.pre_cost_fabric_cost_dtls_id join wo_booking_mst b on b.booking_no=c.booking_no where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id in ($fabric_id_str) and c.fin_fab_qnty is not null ");
        foreach ($wo_data_sql as $row) {
            $wo_data_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]]['fin_fab_qnty'] += $row[csf('fin_fab_qnty')];
            $wo_data_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]]['amount'] += $row[csf('amount')];
            $wo_data_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]]['wo_date'][$row[csf('id')]] = $row[csf('booking_date')];
            $wo_data_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]]['booking_no'].= $row[csf('booking_no')].',';
            $wo_data_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]]['supplier'].= $lib_supplier_arr[$row[csf('supplier_id')]].',';
        }

	
		 $receive_qty_data = sql_select("SELECT c.po_breakdown_id as po_id,d.detarmination_id, d.color, c.quantity as order_qnty, b.order_amount,   b.order_rate	from inv_receive_master a , inv_transaction b , order_wise_pro_details c , product_details_master d   where  a.id = b.mst_id and  b.id = c.trans_id  and  d.id=c.prod_id  and  d.id=b.prod_id  and a.entry_form=17 and a.receive_basis in (1,2) and a.status_active=1 and a.is_deleted=0 and b.receive_basis in (1,2) and b.transaction_type=1 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.trans_type=1 and c.entry_form=17 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $order_cond");


        $receive_qty_arr = array();
        foreach ($receive_qty_data as $row) {
            $job_id= $jobIdArr[$row[csf('po_id')]];
			$receive_qty_arr[$job_id][$row[csf('detarmination_id')]][$row[csf('color')]]['qty'] += $row[csf('order_qnty')];
            $receive_qty_arr[$job_id][$row[csf('detarmination_id')]][$row[csf('color')]]['amount'] +=$row[csf('order_qnty')]*$row[csf('order_rate')];
            $receive_qty_arr[$job_id][$row[csf('detarmination_id')]][$row[csf('color')]]['rate'] = $row[csf('order_rate')];
            $receive_qty_arr[$job_id][$row[csf('detarmination_id')]][$row[csf('color')]]['po_id'] = $row[csf('po_id')];
        }
        $issue_qty_data = sql_select("SELECT d.detarmination_id, d.color, c.quantity, b.cons_amount, e.job_id, e.id as po_id,d.supplier_id from inv_issue_master a join inv_transaction b on a.id = b.mst_id join order_wise_pro_details c on b.id = c.trans_id join product_details_master d on d.id=c.prod_id join wo_po_break_down e on e.id=c.po_breakdown_id where a.entry_form=19 and a.issue_basis=1 and a.status_active=1 and a.is_deleted=0 and b.transaction_type=2 and b.status_active=1 and b.is_deleted=0 and c.trans_type=2 and c.entry_form=19 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $order_cond");

        $issue_qty_arr = array(); $issue_po_arr = array(); $issue_po_chk_arr = array();
        foreach ($issue_qty_data as $row) {
            $rate = $receive_qty_arr[$row[csf('job_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['rate'];
			/*$issue_qty_arr[$row[csf('job_id')]][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['qty'] += $row[csf('cons_quantity')];
            $issue_qty_arr[$row[csf('job_id')]][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['amount'] += $row[csf('cons_quantity')] * $rate;
            $issue_qty_arr[$row[csf('job_id')]][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['po_id'] = $row[csf('po_id')];
            $issue_qty_arr[$row[csf('job_id')]][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['supplier'] = $lib_supplier_arr[$row[csf('supplier_id')]];*/
            $issue_qty_arr[$row[csf('job_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['qty'] += $row[csf('quantity')];
            $issue_qty_arr[$row[csf('job_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['amount'] += $row[csf('quantity')] * $rate;
            if($issue_po_chk_arr[$row[csf('job_id')]][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]!=$row[csf('po_id')])
            {
            	$issue_po_arr[$row[csf('job_id')]][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['po_id']= $row[csf('po_id')];
            	$issue_po_chk_arr[$row[csf('job_id')]][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]=$row[csf('po_id')];
            }
            $issue_qty_arr[$row[csf('job_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['supplier'] = $lib_supplier_arr[$row[csf('supplier_id')]];
        }

        $fabric_supplier_data = sql_select("SELECT a.job_id, a.supplier_id, b.lib_yarn_count_deter_id from wo_pre_cost_fabric_supplier a join wo_pre_cost_fabric_cost_dtls b on a.JOB_ID=b.JOB_ID and b.id=a.fabric_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $jobid_cond");

        $fabric_supplier_arr = array();
        foreach ($fabric_supplier_data as $row) {
            $fabric_supplier_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('supplier_id')]] = $lib_supplier_arr[$row[csf('supplier_id')]];
        }
        $pi_number_data = sql_select("SELECT f.style_ref_no,d.pi_number,f.job_no,i.job_id,c.determination_id, c.amount, c.color_id as color_id from wo_booking_dtls b left join  wo_pre_cost_fabric_cost_dtls i on   b.job_no = i.job_no,com_pi_item_details c , com_pi_master_details d ,wo_po_details_master f	where b.booking_no=c.work_order_no   and b.job_no = f.job_no and d.id=c.pi_id and b.status_active=1 and b.is_deleted=0 	and c.status_active=1 and c.is_deleted=0 and d.pi_basis_id in (1,2) and d.status_active=1 and d.is_deleted=0 and d.importer_id in ($company_name)  $jobid_cond4 and d.entry_form=166	group by f.style_ref_no,c.determination_id,d.pi_number,f.job_no ,i.job_id, c.amount, c.color_id");

        $pi_data_arr = array();
        foreach ($pi_number_data as $key => $row) {
            $pi_data_arr[$row[csf('job_id')]][$row[csf('determination_id')]][$row[csf('color_id')]]['pi_no'].= $row[csf('pi_number')].',';
            $pi_data_arr[$row[csf('job_id')]][$row[csf('determination_id')]][$row[csf('color_id')]]['amount'] += $row[csf('amount')];
            //$pi_data_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('color_id')]]['blc_no']=$row[csf('lc_number')];
        }

        $pi_number_data_lc = sql_select("SELECT f.style_ref_no,d.pi_number,g.lc_number,f.job_no,i.job_id, c.determination_id, c.amount, c.color_id as color_id from wo_booking_dtls b left join  wo_pre_cost_fabric_cost_dtls i on   b.job_no = i.job_no,com_pi_item_details c , com_pi_master_details d ,wo_po_details_master f,com_btb_lc_master_details g,com_btb_lc_pi h 	where b.booking_no=c.work_order_no and g.id=h.com_btb_lc_master_details_id and h.pi_id=d.id and b.job_no = f.job_no and d.id=c.pi_id and b.status_active=1 and b.is_deleted=0 	and c.status_active=1 and c.is_deleted=0 and g.item_basis_id in (1,2) and d.status_active=1 and d.is_deleted=0 and g.importer_id in ($company_name)  $jobid_cond4 and d.entry_form=166	group by f.style_ref_no,d.pi_number,g.lc_number,f.job_no ,i.job_id, c.determination_id, c.amount, c.color_id");

        foreach ($pi_number_data_lc as $key => $row) {

            $pi_data_arr[$row[csf('job_id')]][$row[csf('determination_id')]][$row[csf('color_id')]]['blc_no'].=$row[csf('lc_number')].',';
        }
        unset($pi_number_data_lc);
        $max_shipment_date_sql = sql_select("SELECT MAX(pub_shipment_date) as pub_shipment_date ,job_id from wo_po_break_down where  status_active=1 and is_deleted=0 $jobid_cond3 group by job_id");
        foreach ($max_shipment_date_sql as $row) {
            $max_ship_arr[$row[csf('job_id')]] = $row[csf('pub_shipment_date')];
        }

        $condition = new condition();
        if (count($job_id_arr) > 0) {
            $job_id_str = implode(",", $job_id_arr);
            $condition->jobid_in($job_id_str);
        }
        $condition->init();
        $fabric = new fabric($condition);
		//echo $fabric->getQuery();
		//die;
        $fabric_qty_arr = $fabric->getQtyArray_by_JobIdLibYarnCountDeterIdAndGmtscolor_knitAndwoven_greyAndfinish();
		//echo '<pre>';
        //print_r($fabric_qty_arr); //die;
		
        $fabric_amount_arr = $fabric->getAmountArray_by_JobIdLibYarnCountDeterIdAndGmtscolor_knitAndwoven_greyAndfinish();
		 //echo '<pre>';
        //print_r( $fabric_amount_arr); die;
		
    }
	/*Trims Data Start from Here*/

	if($db_type==0)
	{
		$trim_sql_qry="SELECT a.id as job_id, a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, a.season_buyer_wise, b.file_no, b.grouping, b.id, b.po_number, a.order_uom, a.job_quantity, a.total_set_qnty, b.pub_shipment_date, b.shiping_status, d.costing_per, e.id as trim_dtla_id, e.trim_group, e.remark, e.description, e.brand_sup_ref, e.cons_uom, e.cons_dzn_gmts, e.rate, e.amount, e.apvl_req, e.nominated_supp, e.insert_date from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c left join  wo_pre_cost_trim_co_cons_dtls f on c.job_no_mst=f.job_no and c.po_break_down_id=f.po_break_down_id and f.cons > 0 join wo_pre_cost_trim_cost_dtls e on  f.job_no=e.job_no and e.id=f.wo_pre_cost_trim_cost_dtls_id $item_group_cond join wo_pre_cost_mst d on e.job_no =d.job_no where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and e.is_deleted=0 and e.status_active=1 and a.company_name=$company_name $buyer_id_cond ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $date_cond $style_ref_cond $year_cond $jobcond group by a.id, a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, a.season_buyer_wise, b.file_no, b.grouping, b.id, b.po_number, a.order_uom, a.total_set_qnty, b.pub_shipment_date, b.shiping_status, d.costing_per, e.id, e.trim_group, e.remark, e.description, e.brand_sup_ref, e.cons_uom, e.cons_dzn_gmts, e.rate, e.amount, e.apvl_req, e.nominated_supp, e.insert_date, a.job_quantity order by e.id, e.trim_group"; 
	}
	else
	{
		$trim_sql_qry = "SELECT a.id as job_id,a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, a.season_buyer_wise, b.id, b.po_number, a.order_uom, a.job_quantity, a.total_set_qnty, b.pub_shipment_date, d.costing_per, e.id as trim_dtla_id, e.trim_group, e.remark, e.description, e.brand_sup_ref, e.cons_uom, e.cons_dzn_gmts, e.rate, e.amount, e.apvl_req, e.nominated_supp, e.insert_date from wo_po_details_master a join wo_po_break_down b on a.id=b.job_id join wo_po_color_size_breakdown c on a.id=c.job_id and b.id=c.po_break_down_id join wo_pre_cost_mst d on a.id =d.job_id join wo_pre_cost_trim_cost_dtls e on e.job_id = d.job_id $item_group_cond left join  wo_pre_cost_trim_co_cons_dtls f on c.job_id=f.job_id and c.po_break_down_id=f.po_break_down_id and f.job_no=e.job_no and e.id=f.wo_pre_cost_trim_cost_dtls_id where f.cons > 0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and e.is_deleted=0 and e.status_active=1 and a.company_name=$company_name $buyer_id_cond ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $date_cond $style_ref_cond $year_cond $jobcond $jobid_cond group by a.id, a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, a.season_buyer_wise,  b.id, b.po_number, a.order_uom, a.total_set_qnty, b.pub_shipment_date,  d.costing_per, e.id, e.trim_group, e.remark, e.description, e.brand_sup_ref, e.cons_uom, e.cons_dzn_gmts, e.rate, e.amount, e.apvl_req, e.nominated_supp, e.insert_date, a.job_quantity order by e.id, e.trim_group";
	}
	//echo $trim_sql_qry;
	$trims_sql_data= sql_select($trim_sql_qry);
    if(count($trims_sql_data) > 1) {
        $trims_main_attribute = array('job_no', 'buyer_name', 'job_no_prefix_num', 'style_ref_no', 'season_buyer_wise', 'order_uom', 'job_quantity');
        $trims_dtls_attribute = array('trim_dtla_id', 'trim_group', 'description', 'brand_sup_ref', 'cons_uom', 'cons_dzn_gmts', 'po_number');
        foreach ($trims_sql_data as $row) {
            foreach ($trims_main_attribute as $attr) {
               // $trims_main_data[$row[csf('job_id')]][$attr] = $row[csf($attr)];
            }
            foreach ($trims_dtls_attribute as $tattr) {
                //$trims_main_data[$row[csf('job_id')]]['trims_data'][$row[csf('trim_dtla_id')]][$tattr] = $row[csf($tattr)];
            }
			$trims_main_data[$row[csf('job_id')]]['job_no'] = $row[csf('job_no')];
			$trims_main_data[$row[csf('job_id')]]['buyer_name'] = $row[csf('buyer_name')];
			$trims_main_data[$row[csf('job_id')]]['job_no_prefix_num'] = $row[csf('job_no_prefix_num')];
			$trims_main_data[$row[csf('job_id')]]['style_ref_no'] = $row[csf('style_ref_no')];
			$trims_main_data[$row[csf('job_id')]]['season_buyer_wise'] = $row[csf('season_buyer_wise')];
			$trims_main_data[$row[csf('job_id')]]['order_uom'] = $row[csf('order_uom')];
			$trims_main_data[$row[csf('job_id')]]['job_quantity'] = $row[csf('job_quantity')];
			
			$trims_main_data[$row[csf('job_id')]]['trims_data'][$row[csf('trim_dtla_id')]]['trim_group'] = $row[csf('trim_group')];
			$trims_main_data[$row[csf('job_id')]]['trims_data'][$row[csf('trim_dtla_id')]]['description'] = $row[csf('description')];
			$trims_main_data[$row[csf('job_id')]]['trims_data'][$row[csf('trim_dtla_id')]]['brand_sup_ref'] = $row[csf('brand_sup_ref')];
			$trims_main_data[$row[csf('job_id')]]['trims_data'][$row[csf('trim_dtla_id')]]['cons_uom'] = $row[csf('cons_uom')];
			$trims_main_data[$row[csf('job_id')]]['trims_data'][$row[csf('trim_dtla_id')]]['cons_dzn_gmts'] = $row[csf('cons_dzn_gmts')];
			
            $trimjob_id_arr[$row[csf('job_id')]] = $row[csf('job_id')];
            $trim_id_arr[$row[csf('trim_dtla_id')]] = $row[csf('trim_dtla_id')];
            $trim_poid_arr[$row[csf('id')]] = $row[csf('id')];

        }
        $trimjob_id_chunk = array_chunk($trimjob_id_arr, 1000, true);
        $jobid_cond1 = "";
        $jobid_cond = "";
        $i = 0;
        foreach ($trimjob_id_chunk as $key => $value) {
            if ($i == 0) {
                $jobid_cond = " and b.job_id  in(" . implode(",", $value) . ")";
                $jobid_cond1 = " and a.job_id  in(" . implode(",", $value) . ")";
            } else {
                $jobid_cond = " or b.job_id  in(" . implode(",", $value) . ")";
                $jobid_cond1 .= " or a.job_id  in(" . implode(",", $value) . ")";
            }
            $i++;
        }

        $trim_id_chunk = array_chunk($trim_id_arr, 1000, true);
        $trimid_cond = "";
        $ji = 0;
        foreach ($trim_id_chunk as $key => $value) {
            if ($ji == 0) {
                $trimid_cond = " and a.id  in(" . implode(",", $value) . ")";
            } else {
                $trimid_cond .= " or a.id  in(" . implode(",", $value) . ")";
            }
            $ji++;
        }
        $trim_wo_data_sql = sql_select("SELECT a.id as trim_id, a.job_id, b.id as booking_id, b.booking_date, b.booking_no, d.requirment as wo_qnty,d.amount, d.description,
        b.supplier_id,c.id as booking_dtls_id FROM wo_pre_cost_trim_cost_dtls a , wo_booking_dtls c  , wo_booking_mst b,wo_trim_book_con_dtls d 
		 where a.id=c.pre_cost_fabric_cost_dtls_id and d.wo_trim_booking_dtls_id=c.id and d.po_break_down_id=c.po_break_down_id and d.booking_no=b.booking_no and  b.booking_no=c.booking_no and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.booking_type=2 $trimid_cond ");

        $trim_booking_idArr = array();
        foreach ($trim_wo_data_sql as $row) {
            $trim_wo_data_arr[$row[csf('job_id')]][$row[csf('trim_id')]]['description'].= $row[csf('description')].'**';
			$trim_wo_data_arr[$row[csf('job_id')]][$row[csf('trim_id')]]['wo_qnty'] += $row[csf('wo_qnty')];
            $trim_wo_data_arr[$row[csf('job_id')]][$row[csf('trim_id')]]['wo_amount'] += $row[csf('amount')];
            $trim_wo_data_arr[$row[csf('job_id')]][$row[csf('trim_id')]]['wo_date'][$row[csf('booking_id')]] = $row[csf('booking_date')];
            $trim_wo_data_arr[$row[csf('job_id')]][$row[csf('trim_id')]]['booking_no'][$row[csf('booking_id')]] = $row[csf('booking_no')];
            $trim_wo_data_arr[$row[csf('job_id')]][$row[csf('trim_id')]]['supplier'][$row[csf('booking_id')]] = $lib_supplier_arr[$row[csf('supplier_id')]];
            $trim_booking_idArr[$row[csf('booking_dtls_id')]] = $row[csf('booking_dtls_id')];
        }

        $po_id_chunk = array_chunk($trim_poid_arr, 1000, true);
        $order_cond = "";
        $pi = 0;
        foreach ($po_id_chunk as $key => $value) {
            if ($pi == 0) {
                $order_cond = " and b.po_breakdown_id  in(" . implode(",", $value) . ")";
            } else {
                $order_cond .= " or b.po_breakdown_id  in(" . implode(",", $value) . ")";
            }
            $pi++;
        }
 		//[$row['description']]
        $receive_qty_data = sql_select("SELECT b.po_breakdown_id,a.item_description,a.item_group_id, sum(b.quantity) as quantity, a.rate, e.job_id from inv_receive_master c,product_details_master d,inv_trims_entry_dtls a , order_wise_pro_details b, wo_po_break_down e where a.mst_id=c.id and a.trans_id=b.trans_id and a.id=b.dtls_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and d.status_active=1 and d.is_deleted=0 and e.id=b.po_breakdown_id $order_cond group by b.po_breakdown_id, a.item_description,a.item_group_id,a.rate, e.job_id order by a.item_group_id ");
        $trim_inhouse_qty = array();
        foreach ($receive_qty_data as $row) {
            $trim_inhouse_qty[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['qty'] += $row[csf('quantity')];
            $trim_inhouse_qty[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['amount'] += $row[csf('quantity')] * $row[csf('rate')];
            $trim_inhouse_qty[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['rate'] = $row[csf('rate')];
            $trim_inhouse_qty[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['po_id'] = $row[csf('po_breakdown_id')];
			
            $trims_po_id_arr[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
			
			//$trim_rec_des_qty[$row[csf('job_id')]][$row[csf('item_group_id')]]['po_id'] = $row[csf('item_description')];
        }

        $trim_issue_qty_data = sql_select("SELECT b.po_breakdown_id, p.item_description as item_description,a.item_group_id, sum(b.quantity) as quantity, a.rate, e.job_id  from  inv_issue_master d,product_details_master p,inv_trims_issue_dtls a , order_wise_pro_details b, wo_po_break_down e where a.mst_id=d.id and a.prod_id=p.id and a.trans_id=b.trans_id and b.trans_type=2 and b.entry_form=25 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and p.status_active=1 and p.is_deleted=0 and e.id=b.po_breakdown_id  $order_cond group by b.po_breakdown_id, a.item_group_id,p.item_description, e.job_id,a.rate");
        $trim_issue_qty = array();
        foreach ($trim_issue_qty_data as $row) {
            $rate = $trim_inhouse_qty[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['rate'];
            $trim_issue_qty[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['qty'] += $row[csf('quantity')];
            $trim_issue_qty[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['amount'] += $row[csf('quantity')] * $rate;
            $trim_issue_qty[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['po_id'] = $row[csf('po_breakdown_id')];
        }

        $trim_supplier_data = sql_select("SELECT a.job_id, a.supplier_id, b.id as trim_cost_id from wo_pre_cost_trim_supplier a join wo_pre_cost_trim_cost_dtls b on a.job_id=b.job_id and b.id=a.trimid where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $jobid_cond");

        $fabric_supplier_arr = array();
        foreach ($fabric_supplier_data as $row) {
            $trim_supplier_arr[$row[csf('job_id')]][$row[csf('trim_cost_id')]][$row[csf('supplier_id')]] = $lib_supplier_arr[$row[csf('supplier_id')]];
        }

		$trims_pi_data=sql_select("SELECT a.id, a.lc_number,a.lc_type_id,a.payterm_id,a.tenor, c.pi_number 	FROM com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_master_details c WHERE a.id=b.com_btb_lc_master_details_id and b.pi_id=c.id and a.is_deleted = 0 	 and a.item_basis_id=1 and a.importer_id=$company_name group by a.id, a.lc_number,a.lc_type_id,a.payterm_id,a.tenor,c.pi_number UNION ALL SELECT a.id, a.lc_number,a.lc_type_id,a.payterm_id,a.tenor,c.pi_number  FROM com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_master_details c	 WHERE a.id=b.com_btb_lc_master_details_id and b.pi_id=c.id and a.is_deleted = 0 and a.item_basis_id=2 and a.importer_id=$company_name   group by a.id, a.lc_number,a.lc_type_id,a.payterm_id,a.tenor, c.pi_number order by id desc ");
			$trim_pi_lc_data_arr = array();$fab_pi_lc_data_arr = array();
			foreach ($trims_pi_data as $key => $row) {
				$trim_pi_lc_data_arr[$row[csf('pi_number')]]['lc_number']= $row[csf('lc_number')];;
				$fab_pi_lc_data_arr[$row[csf('pi_number')]]['lc_number']= $row[csf('lc_number')];
			}

        $trim_booking_cond_in = where_con_using_array($trim_booking_idArr, 0, 'c.work_order_dtls_id');
        $trims_pi_number_data = sql_select("SELECT b.pre_cost_fabric_cost_dtls_id, a.job_id,d.pi_number, c.amount, e.lc_number, c.item_group, c.item_color, c.item_size from wo_pre_cost_trim_cost_dtls a join wo_booking_dtls b on a.id=b.pre_cost_fabric_cost_dtls_id join com_pi_item_details c on b.booking_no=c.work_order_no  join com_pi_master_details d on d.id=c.pi_id left join com_btb_lc_master_details e on TO_CHAR(c.pi_id)=e.pi_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.entry_form=167 $jobid_cond1 $trim_booking_cond_in group by b.pre_cost_fabric_cost_dtls_id, a.job_id, d.pi_number, c.amount, e.lc_number, c.item_group, c.item_color, c.item_size");
        $trim_pi_data_arr = array();
        foreach ($trims_pi_number_data as $key => $row) {
            $trim_pi_data_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('job_id')]][$row[csf('item_group')]]['pi_no'].= $row[csf('pi_number')].',';
            $trim_pi_data_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('job_id')]][$row[csf('item_group')]]['amount'] += $row[csf('amount')];
            $trim_pi_data_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('job_id')]][$row[csf('item_group')]]['blc_no'].= $row[csf('lc_number')].',';
        }

        if (count($trimjob_id_arr) > 0) {
            $trimjob_id_str = implode(",", $trimjob_id_arr);
            $condition->jobid_in($trimjob_id_str);
        }
        $condition->init();
        $trim = new trims($condition);
        $trim_group_qty_arr = $trim->getQtyArray_by_jobAndPrecostdtlsid();

        //$trim_group_amount_arr=$trim->getAmountArray_by_jobAndPrecostdtlsid();

        $trim_amountSourcing_arr = $trim->getAmountArray_precostdtlsidSourcing();
        /*echo'<pre>';
        print_r($trim_group_qty_arr); die;*/
    }

	ob_start();
	?>
	<div style="width:2600px">
		<table width="2500">
	        <tr class="form_caption"><td colspan="18" align="center"><? echo $report_title; ?></td></tr>
	        <tr class="form_caption"><td colspan="18" align="center"><? echo $company_library[$company_name]; ?></td></tr>
		</table>
        <?
        if(count($main_data_sql) > 0) {
        ?>
		<table class="rpt_table" width="2500" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
					<tr>
						<td colspan="30" align="left"><strong>Fabric Details</strong></td>
					</tr>
					<tr>
					<th width="30">SL</th>
					<th width="100">Buyer</th>
					<th width="100">Job No</th>
					<th width="80">Season</th>
					<th width="130">Style Ref</th>
					<th width="100">Order Qty</th>
					<th width="50">UOM</th>
					<th width="60">Max.Ship Date</th>
					<th width="200">Item Description</th>
					<th width="100">Fabric Color</th>
					<th width="100">Garments Color</th>
					<th width="80">PO Date</th>
					<th width="60">Avg. Cons</th>
					<th width="80">Req Qty</th>
					<th width="60">BOM Value (USD)</th>					
					<th width="80">PO Qty</th>
					<th width="80">PO NO.</th>
					<th width="50">UOM</th>
					<th width="80">PO Value (USD)</th>
					<th width="60">In-House Qty</th>
					<th width="60">In-House Value [USD]</th>
					<th width="80">Receive Balance</th>
					<th width="80">Issue to Cutting</th>
					<th width="80">Issue Value[USD]</th>
					<th width="80">Issue Balance</th>
					<th width="80">Issue Balance Value (USD)</th>
					<th width="150">Supplier</th>
					<th width="160">PI No.</th>
					<th width="80">PI Value (USD)</th>
					<th width="140">BTB LC N0</th>
				</tr>
			</thead>
			<tbody>
				<?  
				/*echo '<pre>';
				print_r($main_data_arr); die;*/
				$sl=1;
				foreach ($main_data_arr as $job_id => $job_data) {
					if($sl%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					//if($i==1){ ?>
					<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $sl; ?>">
					<td rowspan="<?= $rowspan[$job_id] ?>" width="30"><?= $sl; ?></td>
					<td rowspan="<?= $rowspan[$job_id] ?>" width="100"><?= $buyer_short_name_library[$job_data['buyer_name']]; ?></td>
					<td rowspan="<?= $rowspan[$job_id] ?>" width="100"><?= $job_data['job_no'] ?></td>
					<td rowspan="<?= $rowspan[$job_id] ?>"><?= $lib_season_arr[$job_data['season_buyer_wise']] ?></td>
					<td rowspan="<?= $rowspan[$job_id] ?>"><p>
					<a href='#report_details' onClick="order_summery_popup('<? echo $job_id; ?>' ,'order_summery_data');"><?= $job_data['style_ref_no'] ?></a></p></td>
					<td rowspan="<?= $rowspan[$job_id] ?>" align="right"><p>
					<a href='#report_details' onClick="order_summery_popup('<? echo $job_id; ?>' ,'order_qty_data');"><?= $job_data['job_quantity'] ?></a></p></td>
					<td rowspan="<?= $rowspan[$job_id] ?>"><?= $unit_of_measurement[$job_data['order_uom']] ?></td>
					<td rowspan="<?= $rowspan[$job_id] ?>"><?= $max_ship_arr[$job_id];  ?></td>
					<?	
					$i=1;			
					foreach ($job_data['color_data'] as $lib_yarn_id=>$color_data) {
						
							// if($i!=1) echo '<tr>';						
							if($i!=1) echo '<tr onclick="change_color(\'tr_'.$sl.'\',\''.$bgcolor.'\')" id="tr_'.$sl.'">';
							 ?>
							<td align="left" rowspan="<?= count($color_data['fabric_color']) ?>"><?= $color_data['item_des'] ?></td>
						<?						
						$k=1;
						foreach ($color_data['fabric_color'] as $fcolor_id=>$fcolor_data) {
							
							// if($k!=1) echo '<tr>';
							if($k!=1) echo '<tr onclick="change_color(\'tr_'.$sl.'\',\''.$bgcolor.'\')" id="tr_'.$sl.'">';
							$gmts_color=array();
							$gmts_color_id=array();$booking_noArr=array();$supplierArr=array();$issuePOidArr=array();
							$bom_qty=0;	$wo_qty=0; $bom_value=0; $wo_amount=0; $pi_amount=0;$issueqty=0;$issuepo="";
							foreach ($fcolor_data as $gcolor_id => $row) {
								$fabric_color_id=$row['color'];
								$color_id = $gcolor_id;
								$gmts_color[$gcolor_id]=$color_arr[$gcolor_id];
								$gmts_color_id[$gcolor_id]=$gcolor_id;
								$wo_qty += $wo_data_arr[$job_id][$lib_yarn_id][$color_id][$fabric_color_id]['fin_fab_qnty'];
								$wo_amount+= $wo_data_arr[$job_id][$lib_yarn_id][$color_id][$fabric_color_id]['amount'];
								
								/*$booking_no=$wo_data_arr[$job_id][$lib_yarn_id][$color_id][$fabric_color_id]['booking_no'];
								$suppliers=$wo_data_arr[$job_id][$lib_yarn_id][$color_id][$fabric_color_id]['supplier'];
								$booking_noArr[$booking_no]=$booking_no;
								$supplierArr[$suppliers]=$suppliers;*/
								 $booking_no=rtrim($wo_data_arr[$job_id][$lib_yarn_id][$color_id][$fabric_color_id]['booking_no'],',');
								$booking_no_chk=implode(",",array_unique(explode(",",chop($booking_no,","))));
								$suppliers=rtrim($wo_data_arr[$job_id][$lib_yarn_id][$color_id][$fabric_color_id]['supplier'],',');
								$suppliers_chk=implode(",",array_unique(explode(",",chop($suppliers,","))));
								$booking_noArr[$booking_no_chk].=$booking_no_chk.',';
								$supplierArr[$suppliers_chk].=$suppliers_chk.',';
								/*$issueqty= $issue_qty_arr[$job_id][$row['po_id']][$lib_yarn_id][$fabric_color_id]['qty'];
								$issueamount= $issue_qty_arr[$job_id][$row['po_id']][$lib_yarn_id][$fabric_color_id]['amount'];
								$issuepo= $issue_qty_arr[$job_id][$row['po_id']][$lib_yarn_id][$fabric_color_id]['po_id'];
								$supplier= $issue_qty_arr[$job_id][$row['po_id']][$lib_yarn_id][$fabric_color_id]['supplier'];*/
								$issueqty+= $issue_qty_arr[$job_id][$lib_yarn_id][$fabric_color_id]['qty'];
								$issueamount= $issue_qty_arr[$job_id][$lib_yarn_id][$fabric_color_id]['amount'];
								//$issuepo= $issue_qty_arr[$job_id][$lib_yarn_id][$fabric_color_id]['po_id'];

								$issuePoidsExp=array_unique(explode(",",chop($row['po_ids'],",")));
								foreach ($issuePoidsExp as $issuePoid) {
									if($issue_po_arr[$job_id][$issuePoid][$lib_yarn_id][$fabric_color_id]['po_id'])
									{
									//$issuepo.= $issue_po_arr[$job_id][$issuePoid][$lib_yarn_id][$fabric_color_id]['po_id'].",";
									$issuePOidArr[$issuePoid]=$issue_po_arr[$job_id][$issuePoid][$lib_yarn_id][$fabric_color_id]['po_id'];
									}
								}
								//$issuepo=chop($issuepo,",");

								$supplier= $issue_qty_arr[$job_id][$lib_yarn_id][$fabric_color_id]['supplier'];
								$bom_value+= $fabric_amount_arr['woven']['grey'][$job_id][$lib_yarn_id][$color_id][$fabric_color_id];
								$bom_qty +=$fabric_qty_arr['woven']['grey'][$job_id][$lib_yarn_id][$color_id][$fabric_color_id];
								$pi_amount =$pi_data_arr[$job_id][$lib_yarn_id][$fcolor_id]['amount'];

								$inhouseqty= $receive_qty_arr[$job_id][$lib_yarn_id][$fabric_color_id]['qty'];
								$inhousepo=$receive_qty_arr[$job_id][$lib_yarn_id][$fabric_color_id]['po_id'];
								$inhouseamount=$receive_qty_arr[$job_id][$lib_yarn_id][$fabric_color_id]['amount'];
								$rcv_balance=$wo_qty-$inhouseqty;
								$issue_balance=$inhouseqty-$issueqty;
								$rcv_rate=$receive_qty_arr[$job_id][$lib_yarn_id][$fabric_color_id]['rate'];
							}							
						$pi_no=rtrim($pi_data_arr[$job_id][$lib_yarn_id][$fabric_color_id]['pi_no'],',');
						$pi_nos=implode(", ",array_unique(explode(",",$pi_no)));
						$blc_no=rtrim($pi_data_arr[$job_id][$lib_yarn_id][$fabric_color_id]['blc_no'],',');
						$blc_nos=implode(", ",array_unique(explode(",",$blc_no)));
						$lc_number_lcArr=array();
						$pi_nosArr=array_unique(explode(",",$pi_no));
							foreach($pi_nosArr as $pid)
							{
								$lc_number=$fab_pi_lc_data_arr[$pid]['lc_number'];
								if($lc_number!="")
								{
									$lc_number_lcArr[$lc_number]=$lc_number;
								}
							}
							$issuepo=implode(",",$issuePOidArr);
							 $booking_noall=implode(", ",$booking_noArr);
							$booking_noall=rtrim($booking_noall,',');
							 $supplierAll=implode(", ",$supplierArr);
							 $supplierAll=rtrim($supplierAll,',');
						 	?>
							<td align="left"><?= $color_arr[$fcolor_id] ?></td>
							<td align="left"><?= implode(", ", $gmts_color) ?></td>
							<td align="left"><?= implode(",",$wo_data_arr[$job_id][$lib_yarn_id][$color_id][$fabric_color_id]['wo_date'] )  ?></td>
							<td align="right"><?= fn_number_format($row['avg_cons'],4) ?></td>
							<td align="right"><a href='#report_details' onClick="order_req_qty_popup('<? echo $job_id; ?>','<? echo $lib_yarn_id; ?>','<? echo $fabric_color_id; ?>' ,'order_req_qty_data');"><?= fn_number_format($bom_qty,2)?></a></td>
							<td align="right"><?= fn_number_format($bom_value,2);  ?></td>
							<td align="right" title="Wo Qty"><a href='#report_details' onClick="order_wo_qty_popup('<? echo $job_id; ?>','<? echo $lib_yarn_id; ?>','<? echo $fabric_color_id; ?>','<? echo implode(",", $gmts_color_id) ?>' ,'order_wo_qty_data');"><?= fn_number_format($wo_qty,2)  ?></a></td>							
							<td align="left"><?=$booking_noall; ?></td>
							<td align="right"><?= $unit_of_measurement[$row['fabric_uom']] ?></td>							
							<td align="right"><?= fn_number_format($wo_amount,2) ?></td>
							<td align="right"><a href='#report_details' onClick="openmypage_inhouse('<? echo $job_id; ?>','<? echo $lib_yarn_id; ?>','<? echo $inhousepo; ?>','<? echo $fabric_color_id ?>' ,'booking_inhouse_info');"><?= fn_number_format($inhouseqty,2) ?></a></td>
							<td align="right"><?= fn_number_format($inhouseamount,2) ?></td>
							<td align="right"><?= fn_number_format($rcv_balance,2)  ?></td>
							<td align="right"><? if($issueqty>0){ ?><a href='#report_details' onClick="openmypage_issue('<? echo $job_id; ?>','<? echo $lib_yarn_id; ?>','<? echo $issuepo; ?>','<? echo $fabric_color_id?>', '<?echo $rcv_rate?>' ,'booking_issue_info');"><?= fn_number_format($issueqty,2)  ?></a><? } else echo '0.00'; ?></td>
							<td align="right"><?= fn_number_format($issueamount,2) ?></td>
							<td align="right"><?= fn_number_format($issue_balance,2) ?></td>
							<td align="right"><?= fn_number_format($inhouseamount-$issueamount,2) ?></td>
							<td align="left"><?=$supplierAll; ?></td>
							<td align="left"><p><?= $pi_nos; ?> &nbsp; </p></td>
							<td align="right"><?= fn_number_format($pi_amount,2); ?></td>
							<td align="left"><?= implode(", ",$lc_number_lcArr); ?></td>
							

							<?
							$k++;
							$i++;
							$sl++;
							$total_bom_value += $bom_value;
							$total_wo_value += $wo_amount;
							$total_inhouseamount +=$inhouseamount;
							$total_issueamount +=$issueamount;
							$left_over_amount +=$inhouseamount-$issueamount;
							$pi_amount_total +=$pi_amount;
							$total_issueqty +=$issueqty;
							$total_issue_balance +=$issue_balance;
							$total_rcv_balance +=$rcv_balance;
						 }
							
						} ?>
							</tr>
						<?
						
						}
				?>
				
			</tbody>
			<tfoot>
				<tr style="font-weight: bold; font-size: 20px;">
					<td colspan="14" align="right">Grand Total </td>
					<td align="right"><?= fn_number_format($total_bom_value,2); ?></td>
					<td></td>
					<td></td>
					<td></td>
					<td align="right"><?= fn_number_format($total_wo_value,2); ?></td>
					<td></td>
					<td align="right"><?= fn_number_format($total_inhouseamount,2); ?></td>
					<td align="right"><?= fn_number_format($total_rcv_balance,2); ?></td>
					<td align="right"><?= fn_number_format($total_issueqty,2); ?></td>
					<td align="right"><?= fn_number_format($total_issueamount,2); ?></td>
					<td align="right"><?= fn_number_format($total_issue_balance,2); ?></td>
					<td align="right"><?= fn_number_format($left_over_amount,2); ?></td>
					<td></td>
					<td></td>
					<td ><?= fn_number_format($pi_amount_total,2); ?></td>
					<td align="right"> </td>
                    
				</tr>
			</tfoot>
		</table>
        <?
        }
        if(count($trims_sql_data) > 1) {
        ?>
		<table class="rpt_table" width="2500" cellpadding="0" cellspacing="0" border="1" rules="all" style="margin-top: 10px">
			<thead>
				<tr>
					<td colspan="23" align="left"><strong>Trims Details</strong></td>
				</tr>
				<tr>
					<th width="35">SL</th>
					<th width="95">Buyer</th>
					<th width="95">Job No</th>
					<th width="95">Season</th>
	                <th width="95">Style Ref</th>
					<th width="60">Order Qnty</th>
					<th width="50">UOM</th>
					<th width="60">Max.Ship Date</th>
					<th width="100">Trims Name</th>
					<th width="150">Item Description</th>
					<th width="100">Brand/Sup Ref</th>
					<th width="80">PO Date</th>
					<th width="80">Avg. Cons</th>
					<th width="100">Req Qnty</th>
					<th width="100">BOM Value (USD)</th>
					<th width="90">PO Qty</th>
					<th width="90">PO NO.</th>
	                <th width="50">UOM</th>
	                <th width="60">PO Value (USD)</th>
	                <th width="80">In-House Qty</th>
	                <th width="80">In-House Value [USD]</th>
	                <th width="80">Receive Balance</th>
	                <th width="70">Issue to Prod.</th>
	                <th width="70">Issue Value[USD]</th>
					<th width="90">Issue Balance</th>
					<th width="90">Issue Balance Value (USD)</th>
	                <th width="90">Supplier</th>
					<th width="120">PI No.</th>
					<th width="90">PI Value (USD)</th>
					<th width="90">BTB LC N0</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$tsl=1;
				?>
				<?php foreach ($trims_main_data as $job_id=>$value){
					$rowspan= count($value['trims_data']);
					if($tsl%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				 ?>
					<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('ftr_<? echo $tsl; ?>','<? echo $bgcolor;?>')" id="ftr_<? echo $tsl; ?>">
						<td width="35"  rowspan="<?= $rowspan ?>"><?= $tsl; ?></td>
						<td width="95" align="left" rowspan="<?= $rowspan ?>"><?= $buyer_short_name_library[$value['buyer_name']]; ?></td>
						<td width="95" align="left" rowspan="<?= $rowspan ?>"><?= $value['job_no']; ?></td>
						<td width="95" align="left" rowspan="<?= $rowspan ?>"><?= $lib_season_arr[$value['season_buyer_wise']] ?></td>
		                <td width="95" align="left" rowspan="<?= $rowspan ?>">
					<a href='#report_details' onClick="order_summery_popup('<? echo $job_id; ?>' ,'order_summery_data');"><?= $value['style_ref_no']; ?></a></td>
						<td width="60"  align="right" rowspan="<?= $rowspan ?>"><a href='#report_details' onClick="order_summery_popup('<? echo $job_id; ?>' ,'order_qty_data');"><?= $value['job_quantity']; ?></a></td>
						<td width="50" align="left" rowspan="<?= $rowspan ?>"><?= $unit_of_measurement[$value['order_uom']]; ?></th>
						<td width="60" align="left" rowspan="<?= $rowspan ?>"><?= $max_ship_arr[$job_id];  ?></td>
						<?
						$z=1;
						foreach ($value['trims_data'] as $trims_id=>$row) {
							// if($z!=1) echo '<tr>';
							if($z!=1) echo '<tr onclick="change_color(\'ftr_'.$z.'\',\''.$bgcolor.'\')" id="ftr_'.$z.'">';
							
							$req_qty = $trim_group_qty_arr[$value['job_no']][$trims_id];
							//$req_amount = $trim_group_amount_arr[$value['job_no']][$trims_id];
							$req_amount = $trim_amountSourcing_arr[$trims_id];
							$wo_qty= $trim_wo_data_arr[$job_id][$trims_id]['wo_qnty'];
							$wo_amount= $trim_wo_data_arr[$job_id][$trims_id]['wo_amount'];
							$description= rtrim($trim_wo_data_arr[$job_id][$trims_id]['description'],'**');
							$descriptions=implode(", ",array_unique(explode("**",$description)));
							$descriptionsArr=array_unique(explode("**",$description));
							 
							//if($descriptions!='') $desc=$descriptions;else $desc=0;
							$inhouse_qty=$inhouse_amount=$issue_qty=$issue_amount=0;
							foreach($descriptionsArr as $desc)
							{
								$inhouse_qty+= $trim_inhouse_qty[$job_id][$row['trim_group']][$desc]['qty'];
								$inhouse_amount+= $trim_inhouse_qty[$job_id][$row['trim_group']][$desc]['amount'];
								$issue_qty+= $trim_issue_qty[$job_id][$row['trim_group']][$desc]['qty'];
								$issue_amount+= $trim_issue_qty[$job_id][$row['trim_group']][$desc]['amount'];
								$po_id= $trim_inhouse_qty[$job_id][$row['trim_group']][$desc]['po_id'];
								$tpo_id= $trim_issue_qty[$job_id][$row['trim_group']][$desc]['po_id'];
							}
							
							$pi_amount=$trim_pi_data_arr[$trims_id][$job_id][$row['trim_group']]['amount'];
							$trimrcv_balance = $wo_qty-$inhouse_qty;
							$trims_poid=implode(",",$trims_po_id_arr[$job_id][$row['trim_group']]);
							
							$trim_pi_no=rtrim($trim_pi_data_arr[$trims_id][$job_id][$row['trim_group']]['pi_no'],',');
							$trim_pi_nos=implode(", ",array_unique(explode(",",$trim_pi_no)));
							
							$trim_blc_no=rtrim($trim_pi_data_arr[$trims_id][$job_id][$row['trim_group']]['blc_no'],',');
							$trim_blc_nos=implode(", ",array_unique(explode(",",$trim_blc_no)));
							$trim_pi_btb_lc_arr=array();
							foreach(array_unique(explode(",",$trim_pi_no)) as $tpi)
							{
								//echo $tpi.'D';
								$lc_number=$trim_pi_lc_data_arr[$tpi]['lc_number'];
								if($lc_number!="")
								{
								$trim_pi_btb_lc_arr[$lc_number]=$lc_number;
								}
							}
							$trim_btb_lc_nos=implode(", ",$trim_pi_btb_lc_arr);
						?>
						<td width="100" align="left"><?= $item_library[$row['trim_group']]; ?></td>
						<td width="150" align="left"><?= $descriptions; ?></td>
						<td width="100" align="left"><?= $row['brand_sup_ref']; ?></td>
						<td width="80" align="left"><?= implode(",", $trim_wo_data_arr[$job_id][$trims_id]['wo_date'])?></td>
						<td width="80" align="right"><?= fn_number_format($row['cons_dzn_gmts'],4); ?></td>
						<td width="100" align="right"><a href='#report_details' onClick="trim_req_qty_popup('<? echo $job_id; ?>','<? echo $trims_id; ?>' ,'trim_req_qty_data');"><?= fn_number_format($req_qty,2); ?></a></td>
						<td width="100" align="right"><?= fn_number_format($req_amount,2); ?></td>
						<td width="90" align="right" title="Wo Qty"><a href='#report_details' onClick="trim_wo_qty_popup('<? echo $job_id; ?>','<? echo $trims_id; ?>' ,'trim_wo_qty_data');"><?= fn_number_format($wo_qty,2) ?></a></td>
		                <td width="50" align="left"><?= implode(", ", $trim_wo_data_arr[$job_id][$trims_id]['booking_no'])?></td>
		                <td width="50" align="right"><?= $unit_of_measurement[$row['cons_uom']]; ?></td>
		                <td align="right"><?= fn_number_format($wo_amount,2);  ?></td>
		                <td width="80" align="right"><a href='#report_details' onClick="openmypage_trim_inhouse('<? echo $trims_poid; ?>','<? echo $row['trim_group'].'__'.$descriptions; ?>','trim_booking_inhouse_info');"><?= fn_number_format($inhouse_qty,2);  ?></a></td>
		                <td align="right"><?= fn_number_format($inhouse_amount,2); ?></td>
		                <td width="80" align="right"><?= fn_number_format($trimrcv_balance,2); ?></td>
		                <td width="70" align="right"><a href='#report_details' onClick="openmypage_trim_issue('<? echo $trims_poid; ?>','<? echo $row['trim_group'].'__'.$descriptions; ?>','trim_booking_issue_info');"><?= fn_number_format($issue_qty,2);  ?></a></td>
		                <td align="right"><?= fn_number_format($issue_amount,2); ?></td>
						<td width="90" align="right"><?= fn_number_format($inhouse_qty-$issue_qty,2); ?></td>
						<td width="90" align="right"><?= fn_number_format($inhouse_amount-$issue_amount,2); ?></td>
		                <td width="150" align="left"><?=implode(",", $trim_wo_data_arr[$job_id][$trims_id]['supplier'])
						
						//implode(",", $trim_supplier_arr[$job_id][$trims_id]);  ?></td>
						<td width="160" align="left"><p><?=$trim_pi_nos;?></p></td>
						<td width="90" align="right"><?= fn_number_format($pi_amount,2); ?></td>
						<td width="140" align="left"><p><?=$trim_btb_lc_nos;//$trim_blc_nos;?></p></td>
						<? $z++;

							$total_req_amount += $req_amount;
							$total_wo_amount += $wo_amount;
							$total_inhouse_amount += $inhouse_amount;
							$total_issue_amount += $issue_amount;
							$tatal_left_amount +=$inhouse_amount-$issue_amount;
							$total_pi_amount += $pi_amount;
							$total_trimrcv_balance += $trimrcv_balance;
							$total_trimissue_balance += $inhouse_qty-$issue_qty;
						 } ?>
					</tr>
				<?php 
					$tsl++;
					} 
				?>								
			</tbody>
			<tfoot>
				<tr style="font-size: 20px; font-weight: bold;">
					<td colspan="14" align="right">Grand Total</td>
					<td align="right"><?= fn_number_format($total_req_amount,2);  ?></td>
					<td></td>
					<td></td>
					<td></td>
					<td align="right"><?= fn_number_format($total_wo_amount,2);  ?></td>
					<td></td>
					<td align="right"><?= fn_number_format($total_inhouse_amount,2);  ?></td>
					<td align="right"><?= fn_number_format($total_trimrcv_balance,2);  ?></td>
					<td></td>
					<td align="right"><?= fn_number_format($total_issue_amount,2);  ?></td>
					<td align="right"><?= fn_number_format($total_trimissue_balance,2);  ?></td>
					<td align="right"><?= fn_number_format($tatal_left_amount,2);  ?></td>
					<td></td>
					<td></td>
					<td align="right"><?= fn_number_format($total_pi_amount,2);  ?></td>
					<td></td>
				</tr>
			</tfoot>
		</table>
        <?
        }
        ?>
		<div style="width:2660px; max-height:400px; overflow-y:scroll" id="scroll_body">
		</div>
	</div>
	<?
	foreach (glob("*.xls") as $filename) {
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****$filename";
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
	$country_name_library=return_library_array( "select id, country_name from lib_country", "id", "country_name"  );

	$lib_supplier_arr=return_library_array( "select id, short_name from lib_supplier", "id", "short_name"  );
	$lib_season_arr=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name"  );
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");

	$company_name=str_replace("'","",$cbo_company_name);
	$buyer_id_cond="";
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
				$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
	}

	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_job_no=trim($txt_job_no);

	if($txt_job_no!="" || $txt_job_no!=0) $jobcond="and a.job_no_prefix_num in($txt_job_no)"; else $jobcond="";
	if(str_replace("'","",$txt_style_ref)!="") $style_ref_cond=" and a.style_ref_no like '%".str_replace("'","",$txt_style_ref)."%'"; else $style_ref_cond="";


	$date_type=str_replace("'","",$cbo_date_type);

	$date_cond='';
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		$start_date=(str_replace("'","",$txt_date_from));
		$end_date=(str_replace("'","",$txt_date_to));
		$date_cond="and b.pub_shipment_date between '$start_date' and '$end_date'";
	}

	$cbo_year=str_replace("'","",$cbo_year);
	if($db_type==0)
	{
		if(trim($cbo_year)!=0) $year_cond=" and YEAR(a.insert_date)=$cbo_year"; else $year_cond="";
	}
	else if($db_type==2)
	{
		if(trim($cbo_year)!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year"; else $year_cond="";
	}

	$main_data_sql=sql_select("SELECT a.id, a.job_no, a.buyer_name, a.job_no_prefix_num, a.season_buyer_wise, a.style_ref_no, a.job_quantity, a.order_uom, a.company_name,  b.id as po_id, b.po_number,  c.color_number_id, c.order_quantity, e.id as fabric_cost_id, e.lib_yarn_count_deter_id, e.body_part_id,e.fabric_description, e.uom as fabric_uom, avg(f.requirment) as avg_cons, e.color_size_sensitive, e.color, e.color_break_down, g.contrast_color_id, h.stripe_color,e.rate from wo_po_details_master a join wo_po_break_down b on a.id=b.job_id join wo_po_color_size_breakdown c on a.id=c.job_id and b.id=c.po_break_down_id join wo_pre_cost_mst d on a.id=d.job_id join wo_pre_cost_fabric_cost_dtls e on a.id=e.job_id join wo_pre_cos_fab_co_avg_con_dtls f on e.id=f.pre_cost_fabric_cost_dtls_id left join wo_pre_cos_fab_co_color_dtls g on a.id=g.job_id and e.id=g.pre_cost_fabric_cost_dtls_id and c.color_number_id =g.gmts_color_id and g.status_active=1 and g.is_deleted=0 left join wo_pre_stripe_color h on a.id=h.job_id and  c.item_number_id= h.item_number_id and e.id=h.pre_cost_fabric_cost_dtls_id and f.color_number_id =h.color_number_id and f.po_break_down_id=h.po_break_down_id and f.gmts_sizes=h.size_number_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.company_name=$company_name $year_cond $date_cond $style_ref_cond $jobcond $buyer_id_cond group by a.id, a.job_no, a.buyer_name, a.job_no_prefix_num, a.season_buyer_wise, a.style_ref_no, a.job_quantity, a.order_uom, b.id,b.po_number, c.color_number_id, c.order_quantity, e.lib_yarn_count_deter_id, e.fabric_description, e.uom, e.id, e.color_size_sensitive, e.color, e.body_part_id, e.color_break_down,g.contrast_color_id, h.stripe_color, a.company_name,e.rate  order by e.id asc");

    if(count($main_data_sql) > 0) {
        $main_attribute = array('job_no', 'buyer_name', 'job_no_prefix_num', 'season_buyer_wise', 'style_ref_no', 'job_quantity', 'order_uom', 'company_name');
        foreach ($main_data_sql as $row) {
            foreach ($main_attribute as $attr) {
                $main_data_arr[$row[csf('id')]][$attr] = $row[csf($attr)];
            }
            $fabricColorId = $row[csf('stripe_color')];
            if (!$fabricColorId) {
                $fabricColorId = $row[csf('contrast_color_id')];
            }
            if (!$fabricColorId) {
                $fabricColorId = $row[csf('color_number_id')];
            }
			 $po_idArr[$row[csf('po_id')]] = $row[csf('id')];
			 
            $main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['item_des'] = $body_part[$row[csf('body_part_id')]] . ',' . $row[csf('fabric_description')];
            $main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['color_id'] = $row[csf('color_number_id')];
            $main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['po_number'] = $row[csf('po_number')];
			$main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['po_id'][$row[csf('po_id')]] = $row[csf('po_id')];
			$main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['po_ids'].= $row[csf('po_id')].",";
            $main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['fabric_uom'] = $row[csf('fabric_uom')];
            $main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['avg_cons'] = $row[csf('avg_cons')];
            $main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['sourcing_rate'] = $row[csf('rate')];
            $main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['sensitive'] = $row[csf('color_size_sensitive')];
            $main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['color'] = $fabricColorId;
            $job_id_arr[$row[csf('id')]] = $row[csf('id')];
            $fabric_id_arr[$row[csf('fabric_cost_id')]] = $row[csf('fabric_cost_id')];
            $po_id_arr[$row[csf('po_id')]] = $row[csf('po_id')];
        }
        /* echo '<pre>';
        print_r($main_data_arr); die; */
        $po_id = array_chunk($po_id_arr, 1000, true);
        $order_cond = "";
        $po_cond_for_in2 = "";
        $ji = 0;
        foreach ($po_id as $key => $value) {
            if ($ji == 0) {
                $order_cond = " and c.po_breakdown_id  in(" . implode(",", $value) . ")";
                $po_cond_for_in2 = " and a.po_breakdown_id  in(" . implode(",", $value) . ")";
            } else {
                $order_cond .= " or c.po_breakdown_id  in(" . implode(",", $value) . ")";
                $po_cond_for_in2 .= " or a.po_breakdown_id  in(" . implode(",", $value) . ")";
            }
            $ji++;
        }
        $job_id_chunk = array_chunk($job_id_arr, 1000, true);
        $jobid_cond = "";
        $jobid_cond1 = "";
        $jobid_cond3 = "";
        $i = 0;
        foreach ($job_id_chunk as $key => $value) {
            if ($i == 0) {
                $jobid_cond = " and b.job_id  in(" . implode(",", $value) . ")";
                $jobid_cond1 = " and a.job_id  in(" . implode(",", $value) . ")";
                $jobid_cond3 = " and job_id  in(" . implode(",", $value) . ")";
                $jobid_cond4 = " and i.job_id  in(" . implode(",", $value) . ")";
            } else {
                $jobid_cond .= " or b.job_id  in(" . implode(",", $value) . ")";
                $jobid_cond1 .= " or a.job_id  in(" . implode(",", $value) . ")";
                $jobid_cond3 .= " or job_id  in(" . implode(",", $value) . ")";
                $jobid_cond4 .= " or i.job_id  in(" . implode(",", $value) . ")";
            }
            $i++;
        }

        $rowspan = array();
        foreach ($main_data_arr as $job_id => $jod_arr) {
            foreach ($jod_arr['color_data'] as $color_data) {
                foreach ($color_data['fabric_color'] as $row) {
                    $rowspan[$job_id]++;
                }
            }
        }
        $fabric_id_str = implode(",", $fabric_id_arr);

        $wo_data_sql = sql_select("SELECT a.job_id, a.lib_yarn_count_deter_id, b.id, b.booking_date,b.booking_no, c.gmts_color_id, c.fin_fab_qnty, c.amount,	b.supplier_id, c.fabric_color_id FROM wo_pre_cost_fabric_cost_dtls a , wo_booking_dtls c ,wo_booking_mst b where  a.id=c.pre_cost_fabric_cost_dtls_id and  b.booking_no=c.booking_no  and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id in ($fabric_id_str) and c.fin_fab_qnty is not null ");
        foreach ($wo_data_sql as $row) {
            $wo_data_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]]['fin_fab_qnty'] += $row[csf('fin_fab_qnty')];
            $wo_data_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]]['amount'] += $row[csf('amount')];
            $wo_data_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]]['wo_date'][$row[csf('id')]] = $row[csf('booking_date')];
            $wo_data_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]]['booking_no'].= $row[csf('booking_no')].',';
            $wo_data_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]]['supplier'].= $lib_supplier_arr[$row[csf('supplier_id')]].',';
        }
 		unset($wo_data_sql);
        $receive_qty_data = sql_select("SELECT c.po_breakdown_id as po_id,d.detarmination_id, d.color, c.quantity as order_qnty, b.order_amount,   b.order_rate	from inv_receive_master a , inv_transaction b , order_wise_pro_details c , product_details_master d   where  a.id = b.mst_id and  b.id = c.trans_id  and  d.id=c.prod_id  and  d.id=b.prod_id  and a.entry_form=17 and a.receive_basis in (1,2) and a.status_active=1 and a.is_deleted=0 and b.receive_basis in (1,2) and b.transaction_type=1 and b.status_active=1 and b.is_deleted=0 and c.trans_type=1 and c.entry_form=17 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $order_cond");
        $receive_qty_arr = array();$tot_aty=0;
        foreach ($receive_qty_data as $row) {
			$job_id=$po_idArr[$row[csf('po_id')]];
            $receive_qty_arr[$job_id][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['qty'] += $row[csf('order_qnty')];
            $receive_qty_arr[$job_id][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['amount'] += $row[csf('order_amount')];
            $receive_qty_arr[$job_id][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['rate'] = $row[csf('order_rate')];
            $receive_qty_arr[$job_id][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['po_id'] = $row[csf('po_id')];
			$tot_aty+=$row[csf('order_qnty')];
        }
		//echo $tot_aty.'d';
		unset($receive_qty_data);
        $issue_qty_data = sql_select("SELECT d.detarmination_id, d.color, c.quantity, b.cons_amount, e.job_id, e.id as po_id,d.supplier_id from inv_issue_master a join inv_transaction b on a.id = b.mst_id join order_wise_pro_details c on b.id = c.trans_id join product_details_master d on d.id=c.prod_id join wo_po_break_down e on e.id=c.po_breakdown_id where a.entry_form=19 and a.issue_basis=1 and a.status_active=1 and a.is_deleted=0 and b.transaction_type=2 and b.status_active=1 and b.is_deleted=0 and c.trans_type=2 and c.entry_form=19 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $order_cond");

        $issue_qty_arr = array();$issue_po_arr = array(); $issue_po_chk_arr = array();
        foreach ($issue_qty_data as $row) {
            $rate = $receive_qty_arr[$row[csf('job_id')]][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['rate'];
            $issue_qty_arr[$row[csf('job_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['qty'] += $row[csf('quantity')];
            $issue_qty_arr[$row[csf('job_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['amount'] += $row[csf('quantity')] * $rate;
            if($issue_po_chk_arr[$row[csf('job_id')]][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]!=$row[csf('po_id')])
            {
            	$issue_po_arr[$row[csf('job_id')]][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['po_id']= $row[csf('po_id')];
            	$issue_po_chk_arr[$row[csf('job_id')]][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]=$row[csf('po_id')];
            }
            $issue_qty_arr[$row[csf('job_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['supplier'] = $lib_supplier_arr[$row[csf('supplier_id')]];
        }
		unset($issue_qty_data);

        $fabric_supplier_data = sql_select("SELECT a.job_id, a.supplier_id, b.lib_yarn_count_deter_id from wo_pre_cost_fabric_supplier a , wo_pre_cost_fabric_cost_dtls b where  a.JOB_ID=b.JOB_ID and b.id=a.fabric_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $jobid_cond");

        $fabric_supplier_arr = array();
        foreach ($fabric_supplier_data as $row) {
            $fabric_supplier_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('supplier_id')]] = $lib_supplier_arr[$row[csf('supplier_id')]];
        }
 		unset($fabric_supplier_data);

        $pi_number_data = sql_select("SELECT f.style_ref_no,d.pi_number,f.job_no,i.job_id,c.determination_id, c.amount, c.color_id as color_id from wo_booking_dtls b left join  wo_pre_cost_fabric_cost_dtls i on   b.job_no = i.job_no,com_pi_item_details c , com_pi_master_details d ,wo_po_details_master f	where b.booking_no=c.work_order_no   and b.job_no = f.job_no and d.id=c.pi_id and b.status_active=1 and b.is_deleted=0 	and c.status_active=1 and c.is_deleted=0 and d.pi_basis_id in (1,2) and d.status_active=1 and d.is_deleted=0 and d.importer_id in ($company_name)  $jobid_cond4 and d.entry_form=166	group by f.style_ref_no,c.determination_id,d.pi_number,f.job_no ,i.job_id, c.amount, c.color_id");

        $pi_data_arr = array();
        foreach ($pi_number_data as $key => $row) {
            $pi_data_arr[$row[csf('job_id')]][$row[csf('determination_id')]][$row[csf('color_id')]]['pi_no'].= $row[csf('pi_number')].',';
            $pi_data_arr[$row[csf('job_id')]][$row[csf('determination_id')]][$row[csf('color_id')]]['amount'] += $row[csf('amount')];
            //$pi_data_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('color_id')]]['blc_no']=$row[csf('lc_number')];
        }
 		unset($pi_number_data);
        $pi_number_data_lc = sql_select("SELECT f.style_ref_no,d.pi_number,g.lc_number,f.job_no,i.job_id, c.determination_id, c.amount, c.color_id as color_id from wo_booking_dtls b left join  wo_pre_cost_fabric_cost_dtls i on   b.job_no = i.job_no,com_pi_item_details c , com_pi_master_details d ,wo_po_details_master f,com_btb_lc_master_details g,com_btb_lc_pi h 	where b.booking_no=c.work_order_no and g.id=h.com_btb_lc_master_details_id and h.pi_id=d.id and b.job_no = f.job_no and d.id=c.pi_id and b.status_active=1 and b.is_deleted=0 	and c.status_active=1 and c.is_deleted=0 and g.item_basis_id in (1,2) and d.status_active=1 and d.is_deleted=0 and g.importer_id in ($company_name)  $jobid_cond4 and d.entry_form=167	group by f.style_ref_no,d.pi_number,g.lc_number,f.job_no ,i.job_id, c.determination_id, c.amount, c.color_id");
	

        foreach ($pi_number_data_lc as $key => $row) {

            $pi_data_arr[$row[csf('job_id')]][$row[csf('determination_id')]][$row[csf('color_id')]]['blc_no'].= $row[csf('lc_number')].',';
        }
        unset($pi_number_data_lc);


        /*echo '<pre>';
        print_r($pi_data_arr); die;*/
        $max_shipment_date_sql = sql_select("SELECT MAX(pub_shipment_date) as pub_shipment_date ,job_id from wo_po_break_down where  status_active=1 and is_deleted=0 $jobid_cond3 group by job_id");
        foreach ($max_shipment_date_sql as $row) {
            $max_ship_arr[$row[csf('job_id')]] = $row[csf('pub_shipment_date')];
        }

        $condition = new condition();
        if (count($job_id_arr) > 0) {
            $job_id_str = implode(",", $job_id_arr);
            $condition->jobid_in($job_id_str);
        }
        $condition->init();
        $fabric = new fabric($condition);
        $fabric_qty_arr = $fabric->getQtyArray_by_JobIdLibYarnCountDeterIdAndGmtscolor_knitAndwoven_greyAndfinish();
        //$fabric_amount_arr=$fabric->getAmountArray_by_JobIdLibYarnCountDeterIdAndGmtscolor_knitAndwoven_greyAndfinish();
        $fabric_amount_arr = $fabric->getAmountArr_by_JobIdYarnCountIdGmtsAndFabricColor_source();
    }
	/*Trims Data Start from Here*/


	$trim_sql_qry = "SELECT a.id as job_id,a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, a.season_buyer_wise, b.id, b.po_number, a.order_uom, a.job_quantity, a.total_set_qnty, b.pub_shipment_date, d.costing_per, d.costing_date, e.id as trim_dtla_id, e.trim_group, e.remark, e.description, e.brand_sup_ref, e.cons_uom, e.cons_dzn_gmts, e.rate, e.amount, e.apvl_req, e.nominated_supp, e.insert_date,e.sourcing_rate from wo_po_details_master a join wo_po_break_down b on a.job_no=b.job_no_mst join wo_po_color_size_breakdown c on a.job_no=c.job_no_mst and b.id=c.po_break_down_id join wo_pre_cost_mst d on a.job_no =d.job_no join wo_pre_cost_trim_cost_dtls e on e.job_no = d.job_no $item_group_cond left join  wo_pre_cost_trim_co_cons_dtls f on c.job_no_mst=f.job_no and c.po_break_down_id=f.po_break_down_id and f.job_no=e.job_no and e.id=f.wo_pre_cost_trim_cost_dtls_id where f.cons > 0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and e.is_deleted=0 and e.status_active=1 and a.company_name=$company_name $buyer_id_cond ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $date_cond $style_ref_cond $year_cond $jobcond $jobid_cond group by a.id, a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, a.season_buyer_wise,  b.id, b.po_number, a.order_uom, a.total_set_qnty, b.pub_shipment_date,  d.costing_per, d.costing_date, e.id, e.trim_group, e.remark, e.description, e.brand_sup_ref, e.cons_uom, e.cons_dzn_gmts, e.rate, e.amount, e.apvl_req, e.nominated_supp, e.insert_date, a.job_quantity,e.sourcing_rate order by e.id, e.trim_group";


	
	//echo $trim_sql_qry; die;
	$trims_sql_data= sql_select($trim_sql_qry);
    if(count($trims_sql_data) > 1) {
        $trims_main_attribute = array('job_no', 'buyer_name', 'job_no_prefix_num', 'style_ref_no', 'season_buyer_wise', 'order_uom', 'job_quantity');
        $trims_dtls_attribute = array('trim_dtla_id', 'trim_group', 'description', 'brand_sup_ref', 'cons_uom', 'cons_dzn_gmts', 'po_number', 'sourcing_rate');
        foreach ($trims_sql_data as $row) {

            foreach ($trims_main_attribute as $attr) {
                $trims_main_data[$row[csf('job_id')]][$attr] = $row[csf($attr)];
            }
            foreach ($trims_dtls_attribute as $tattr) {
                $trims_main_data[$row[csf('job_id')]]['trims_data'][$row[csf('trim_dtla_id')]][$tattr] = $row[csf($tattr)];
            }
            $trimjob_id_arr[$row[csf('job_id')]] = $row[csf('job_id')];
            $trim_id_arr[$row[csf('trim_dtla_id')]] = $row[csf('trim_dtla_id')];
            $trim_poid_arr[$row[csf('id')]] = $row[csf('id')];

        }
        $trimjob_id_chunk = array_chunk($trimjob_id_arr, 1000, true);
        $jobid_cond1 = "";
        $jobid_cond = "";
        $i = 0;
        foreach ($trimjob_id_chunk as $key => $value) {
            if ($i == 0) {
                $jobid_cond = " and b.job_id  in(" . implode(",", $value) . ")";
                $jobid_cond1 = " and a.job_id  in(" . implode(",", $value) . ")";
            } else {
                $jobid_cond = " or b.job_id  in(" . implode(",", $value) . ")";
                $jobid_cond1 .= " or a.job_id  in(" . implode(",", $value) . ")";
            }
            $i++;
        }

        $trim_id_chunk = array_chunk($trim_id_arr, 1000, true);
        $trimid_cond = "";
        $ji = 0;
        foreach ($trim_id_chunk as $key => $value) {
            if ($ji == 0) {
                $trimid_cond = " and a.id  in(" . implode(",", $value) . ")";
            } else {
                $trimid_cond .= " or a.id  in(" . implode(",", $value) . ")";
            }
            $ji++;
        }
      /*  $trim_wo_data_sql = sql_select("SELECT a.id as trim_id, a.job_id, b.id as booking_id, b.booking_date, b.booking_no, c.wo_qnty,c.amount, 
	b.supplier_id FROM wo_pre_cost_trim_cost_dtls a ,wo_booking_dtls c , wo_booking_mst b  where   b.booking_no=c.booking_no and a.id=c.pre_cost_fabric_cost_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.booking_type=2 $trimid_cond ");*/
	 $trim_wo_data_sql = sql_select("SELECT a.id as trim_id, a.job_id, b.id as booking_id, b.booking_date, b.booking_no, d.requirment as wo_qnty,d.amount, d.description,
        b.supplier_id,c.id as booking_dtls_id FROM wo_pre_cost_trim_cost_dtls a , wo_booking_dtls c  , wo_booking_mst b,wo_trim_book_con_dtls d 
		 where a.id=c.pre_cost_fabric_cost_dtls_id and d.wo_trim_booking_dtls_id=c.id and d.po_break_down_id=c.po_break_down_id and d.booking_no=b.booking_no and  b.booking_no=c.booking_no and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.booking_type=2 $trimid_cond ");
		 
        foreach ($trim_wo_data_sql as $row) {
            $trim_wo_data_arr[$row[csf('job_id')]][$row[csf('trim_id')]]['wo_qnty'] += $row[csf('wo_qnty')];
            $trim_wo_data_arr[$row[csf('job_id')]][$row[csf('trim_id')]]['wo_amount'] += $row[csf('amount')];
			$trim_wo_data_arr[$row[csf('job_id')]][$row[csf('trim_id')]]['description'].= $row[csf('description')].'**';
            $trim_wo_data_arr[$row[csf('job_id')]][$row[csf('trim_id')]]['wo_date'][$row[csf('booking_id')]] = $row[csf('booking_date')];
            $trim_wo_data_arr[$row[csf('job_id')]][$row[csf('trim_id')]]['booking_no'][$row[csf('booking_id')]] = $row[csf('booking_no')];
            $trim_wo_data_arr[$row[csf('job_id')]][$row[csf('trim_id')]]['supplier'][$row[csf('booking_id')]] = $lib_supplier_arr[$row[csf('supplier_id')]];
        }

        $po_id_chunk = array_chunk($trim_poid_arr, 1000, true);
        $order_cond = "";
        $pi = 0;
        foreach ($po_id_chunk as $key => $value) {
            if ($pi == 0) {
                $order_cond = " and b.po_breakdown_id  in(" . implode(",", $value) . ")";
            } else {
                $order_cond .= " or b.po_breakdown_id  in(" . implode(",", $value) . ")";
            }
            $pi++;
        }

        $receive_qty_data = sql_select("SELECT b.po_breakdown_id, a.item_group_id,a.item_description, (b.quantity) as quantity, a.rate, e.job_id from inv_receive_master c,product_details_master d,inv_trims_entry_dtls a , order_wise_pro_details b, wo_po_break_down e where a.mst_id=c.id and a.trans_id=b.trans_id and a.id=b.dtls_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and d.status_active=1 and d.is_deleted=0 and e.id=b.po_breakdown_id $order_cond  order by a.item_group_id ");
		
        $trim_inhouse_qty = array();
        foreach ($receive_qty_data as $row) { 
            $trim_inhouse_qty[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['qty'] += $row[csf('quantity')];
            $trim_inhouse_qty[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['amount'] += $row[csf('quantity')] * $row[csf('rate')];
            $trim_inhouse_qty[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['rate'] = $row[csf('rate')];
            $trim_inhouse_qty[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['po_id'] = $row[csf('po_breakdown_id')];
            $trims_po_id_arr[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
        }
 unset($receive_qty_data);
        $trim_issue_qty_data = sql_select("SELECT b.po_breakdown_id, a.item_group_id,p.item_description as item_description, (b.quantity) as quantity, a.rate, e.job_id  from  inv_issue_master d,product_details_master p,inv_trims_issue_dtls a , order_wise_pro_details b, wo_po_break_down e where a.mst_id=d.id and a.prod_id=p.id and a.trans_id=b.trans_id and b.trans_type=2 and b.entry_form=25 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and p.status_active=1 and p.is_deleted=0 and e.id=b.po_breakdown_id  $order_cond ");
		//echo "SELECT b.po_breakdown_id, a.item_group_id,p.item_description as item_description, sum(b.quantity) as quantity, a.rate, e.job_id  from  inv_issue_master d,product_details_master p,inv_trims_issue_dtls a , order_wise_pro_details b, wo_po_break_down e where a.mst_id=d.id and a.prod_id=p.id and a.trans_id=b.trans_id and b.trans_type=2 and b.entry_form=25 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.id=b.po_breakdown_id  $order_cond group by b.po_breakdown_id, a.item_group_id,p.item_description, e.job_id,a.rate";
		
        $trim_issue_qty = array();
        foreach ($trim_issue_qty_data as $row) {
            $rate = $trim_inhouse_qty[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['rate'];
            $trim_issue_qty[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['qty'] += $row[csf('quantity')];
            $trim_issue_qty[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['amount'] += $row[csf('quantity')] * $rate;
            $trim_issue_qty[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['po_id'] = $row[csf('po_breakdown_id')];
        }
 unset($trim_issue_qty_data);
        $trim_supplier_data = sql_select("SELECT a.job_id, a.supplier_id, b.id as trim_cost_id from wo_pre_cost_trim_supplier a join wo_pre_cost_trim_cost_dtls b on a.job_id=b.job_id and b.id=a.trimid where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $jobid_cond");

        $fabric_supplier_arr = array();
        foreach ($fabric_supplier_data as $row) {
            $trim_supplier_arr[$row[csf('job_id')]][$row[csf('trim_cost_id')]][$row[csf('supplier_id')]] = $lib_supplier_arr[$row[csf('supplier_id')]];
        }
 unset($fabric_supplier_data);
		$trims_pi_data=sql_select("SELECT a.id, a.lc_number,a.lc_type_id,a.payterm_id,a.tenor, c.pi_number 
		FROM com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_master_details c WHERE a.id=b.com_btb_lc_master_details_id and b.pi_id=c.id and a.is_deleted = 0 
		 and a.item_basis_id=1 and a.importer_id=$company_name 
		  UNION ALL
		  SELECT a.id, a.lc_number,a.lc_type_id,a.payterm_id,a.tenor,c.pi_number
		  FROM com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_master_details c
		 WHERE a.id=b.com_btb_lc_master_details_id and b.pi_id=c.id and a.is_deleted = 0 and a.item_basis_id=2 and a.importer_id=$company_name  
		 order by id desc ");
			$trim_pi_lc_data_arr = array();$fab_pi_lc_data_arr = array();
			foreach ($trims_pi_data as $key => $row) {
				$trim_pi_lc_data_arr[$row[csf('pi_number')]]['lc_number']= $row[csf('lc_number')];;
				$fab_pi_lc_data_arr[$row[csf('pi_number')]]['lc_number']= $row[csf('lc_number')];
			}
			unset($trims_pi_data);





        $trims_pi_number_data = sql_select("SELECT a.job_id,d.pi_number, c.amount, e.lc_number, c.item_group, b.pre_cost_fabric_cost_dtls_id, c.item_color, c.item_size from wo_pre_cost_trim_cost_dtls a join wo_booking_dtls b on a.id=b.pre_cost_fabric_cost_dtls_id join com_pi_item_details c on b.booking_no=c.work_order_no  join com_pi_master_details d on d.id=c.pi_id left join com_btb_lc_master_details e on TO_CHAR(c.pi_id)=e.pi_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.entry_form=167 $jobid_cond1 group by a.job_id, d.pi_number, c.amount, e.lc_number, c.item_group, b.pre_cost_fabric_cost_dtls_id, c.item_color, c.item_size");
		
 //    echo "SELECT a.job_id,d.pi_number, c.amount, e.lc_number, c.item_group, c.item_color, c.item_size from wo_pre_cost_trim_cost_dtls a join wo_booking_dtls b on a.job_no=b.job_no join com_pi_item_details c on b.booking_no=c.work_order_no  join com_pi_master_details d on d.id=c.pi_id left join com_btb_lc_master_details e on TO_CHAR(c.pi_id)=e.pi_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.entry_form=167 $jobid_cond1 group by a.job_id, d.pi_number, c.amount, e.lc_number, c.item_group, c.item_color, c.item_size";
        $trim_pi_data_arr = array();
        foreach ($trims_pi_number_data as $key => $row) {
            $trim_pi_data_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('job_id')]][$row[csf('item_group')]]['pi_no'].= $row[csf('pi_number')].',';
            $trim_pi_data_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('job_id')]][$row[csf('item_group')]]['amount'] += $row[csf('amount')];
            $trim_pi_data_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('job_id')]][$row[csf('item_group')]]['blc_no'].= $row[csf('lc_number')].',';
        }
        if (count($trimjob_id_arr) > 0) {
           // $trimjob_id_str = implode(",", $trimjob_id_arr);
           // $condition->jobid_in($trimjob_id_str);
        }
      //  $condition->init();
        $trim = new trims($condition);
        $trim_group_qty_arr = $trim->getQtyArray_by_jobAndPrecostdtlsid();

        //$trim_group_amount_arr=$trim->getAmountArray_by_jobAndPrecostdtlsid();

        $trim_amountSourcing_arr = $trim->getAmountArray_precostdtlsidSourcing();

        $partial_fabric_report_type = array(84 => 'show_fabric_booking_report_urmi_per_job', 85 => 'print_booking_3', 143 => 'show_fabric_booking_report_urmi', 151 => 'show_fabric_booking_report_advance_attire_ltd', 160 => 'print_booking_5', 175 => 'print_booking_6', 155 => 'fabric_booking_report', 235 => 'print_9', 191 => 'print_booking_7');

        $print_report_format = return_field_value("format_id", " lib_report_template", "template_name ='" . $company_name . "'  and module_id=2 and report_id=35 and is_deleted=0 and status_active=1");
        $print_report_format_arr = explode(",", $print_report_format);
        $fabric_report_first_id = $print_report_format_arr[0];
    }
	ob_start();
	?>
	<div style="width:2000px">
		<table width="2560">
	        <tr class="form_caption"><td colspan="18" align="center"><? echo $report_title; ?></td></tr>
	        <tr class="form_caption"><td colspan="18" align="center"><? echo $company_library[$company_name]; ?></td></tr>
		</table>
        <?
        if(count($main_data_sql) > 0) {
        ?>
		<table class="rpt_table" width="2560" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
					<tr>
						<td colspan="25" align="left"><strong>Fabric Details</strong></td>
					</tr>
					<tr>
					<th width="30">SL</th>
					<th width="100">Buyer</th>
					<th width="100">Job No</th>
					<th width="80">Season</th>
					<th width="130">Style Ref</th>
					<th width="100">Order Qty</th>
					<th width="50">UOM</th>
					<th width="60">Max.Ship Date</th>
					<th width="200">Item Description</th>
					<th width="100">Fabric Color</th>
					<th width="100">Garments Color</th>
					<th width="80">PO Date</th>
					<th width="60">Avg. Cons</th>
					<th width="60">Rate</th>
					<th width="80">Req Qty</th>					
					<th width="80">PO Qty</th>
					<th width="80">PO NO.</th>
					<th width="50">UOM</th>
					<th width="80">In-House Qty</th>
					<th width="80">Receive Balance</th>
					<th width="80">Issue to Cutting</th>
					<th width="80">Issue Balance</th>
					<th width="100">Supplier</th>
					<th width="120">PI No.</th>
					<th width="100">BTB LC N0</th>
				</tr>
			</thead>
			<tbody>
				<? $sl=1;  
				/* echo '<pre>';
				print_r($main_data_arr); die; */
				foreach ($main_data_arr as $job_id => $job_data) {
					if($sl%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";					
					//if($i==1){ ?>
					<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $sl; ?>">
					<td rowspan="<?= $rowspan[$job_id]; ?>" width="30" align="center"><?= $sl; ?></td>
					<td rowspan="<?= $rowspan[$job_id]; ?>" width="80"  align="left"><?= $buyer_short_name_library[$job_data['buyer_name']]; ?></td>
					<td rowspan="<?= $rowspan[$job_id]; ?>" width="80"  align="left"><?= $job_data['job_no']; ?></td>
					<td rowspan="<?= $rowspan[$job_id]; ?>" align="left"><?= $lib_season_arr[$job_data['season_buyer_wise']]; ?></td>
					<td rowspan="<?= $rowspan[$job_id]; ?>"><p>
					<a href='#report_details' onClick="order_summery_popup('<? echo $job_id; ?>' ,'order_summery_data');"><?= $job_data['style_ref_no']; ?></a></p></td>
					<td rowspan="<?= $rowspan[$job_id]; ?>"  align="right"><p>
					<a href='#report_details' onClick="order_summery_popup('<? echo $job_id; ?>' ,'order_qty_data');"><?= $job_data['job_quantity']; ?></a></p></td>
					<td rowspan="<?= $rowspan[$job_id]; ?>"><?= $unit_of_measurement[$job_data['order_uom']]; ?></td>
					<td rowspan="<?= $rowspan[$job_id]; ?>"><?= $max_ship_arr[$job_id];  ?></td>
					<?	
					$i=1;			
					foreach ($job_data['color_data'] as $lib_yarn_id=>$color_data) {
						// if($i!=1) echo '<tr>';
						if($i!=1) echo '<tr onclick="change_color(\'tr_'.$i.'\',\''.$bgcolor.'\')" id="tr_'.$i.'">';
							
							 ?>
							<td rowspan="<?= count($color_data['fabric_color']) ?>"><?= $color_data['item_des'] ?></td>
						<?						
						$k=1;
						foreach ($color_data['fabric_color'] as $fcolor_id=>$fcolor_data) {
							// if($k!=1) echo '<tr>';
							if($k!=1) echo '<tr onclick="change_color(\'tr_'.$i.'\',\''.$bgcolor.'\')" id="tr_'.$i.'">';

							$gmts_color=array();
							$gmts_color_id=array();$booking_noArr=array();$supplierArr=array();$issuePOidArr=array();
							$bom_qty=0;	$wo_qty=0; $bom_value=0; $wo_amount=0; $pi_amount=0;$issueqty=$inhouseqty=0;$issuepo="";
							foreach ($fcolor_data as $gcolor_id => $row) {
								$fabric_color_id=$row['color'];
								$color_id = $gcolor_id;
								$gmts_color[$gcolor_id]=$color_arr[$gcolor_id];
								$gmts_color_id[$gcolor_id]=$gcolor_id;
								$wo_qty += $wo_data_arr[$job_id][$lib_yarn_id][$color_id][$fabric_color_id]['fin_fab_qnty'];
								$booking_no=rtrim($wo_data_arr[$job_id][$lib_yarn_id][$color_id][$fabric_color_id]['booking_no'],',');
								$booking_no_chk=implode(",",array_unique(explode(",",chop($booking_no,","))));
								$suppliers=rtrim($wo_data_arr[$job_id][$lib_yarn_id][$color_id][$fabric_color_id]['supplier'],',');
								$suppliers_chk=implode(",",array_unique(explode(",",chop($suppliers,","))));
								$booking_noArr[$booking_no_chk].=$booking_no_chk.',';
								$supplierArr[$suppliers_chk].=$suppliers_chk.',';
								/*$issueqty+= $issue_qty_arr[$job_id][$row['po_id']][$lib_yarn_id][$fabric_color_id]['qty'];
								$issuepo= $issue_qty_arr[$job_id][$row['po_id']][$lib_yarn_id][$fabric_color_id]['po_id'];
								$supplier= $issue_qty_arr[$job_id][$row['po_id']][$lib_yarn_id][$fabric_color_id]['supplier'];*/
								$issueqty+= $issue_qty_arr[$job_id][$lib_yarn_id][$fabric_color_id]['qty'];
								//$issuepo= $issue_qty_arr[$job_id][$lib_yarn_id][$fabric_color_id]['po_id'];

								$issuePoidsExp=array_unique(explode(",",chop($row['po_ids'],",")));
								foreach ($issuePoidsExp as $issuePoid) {
									if($issue_po_arr[$job_id][$issuePoid][$lib_yarn_id][$fabric_color_id]['po_id'])
									{
										$issuePOidArr[$issuePoid]=$issue_po_arr[$job_id][$issuePoid][$lib_yarn_id][$fabric_color_id]['po_id'];
									}
								}
								$supplier= $issue_qty_arr[$job_id][$lib_yarn_id][$fabric_color_id]['supplier'];
								$bom_value+= $fabric_amount_arr['woven']['grey'][$job_id][$lib_yarn_id][$color_id][$fabric_color_id];
								$bom_qty +=$fabric_qty_arr['woven']['grey'][$job_id][$lib_yarn_id][$color_id][$fabric_color_id];
								foreach($row['po_id'] as $fabricpoId){
									$inhouseqty+= $receive_qty_arr[$job_id][$fabricpoId][$lib_yarn_id][$fabric_color_id]['qty'];
								}
								
								//$inhousepo=$receive_qty_arr[$job_id][$lib_yarn_id][$fabric_color_id]['po_id'];
								$inhousepo=implode(",",$row['po_id']);
							
								$rcv_balance=$wo_qty-$inhouseqty;
								
								$rcv_rate=$receive_qty_arr[$job_id][$lib_yarn_id][$fabric_color_id]['rate'];
							}	
							//$inhouseqty= $receive_qty_arr[$job_id][$lib_yarn_id][$fcolor_id]['qty'];
								//echo $inhouseqty.',';
							$issue_balance=$inhouseqty-$issueqty;
													
							$pi_no=rtrim($pi_data_arr[$job_id][$lib_yarn_id][$fabric_color_id]['pi_no'],',');
							$pi_nos=implode(", ",array_unique(explode(",",$pi_no)));
							$blc_no=rtrim($pi_data_arr[$job_id][$lib_yarn_id][$fabric_color_id]['blc_no'],',');
							$blc_nos=implode(", ",array_unique(explode(",",$blc_no)));
							$lc_number_lcArr=array();
							$pi_nosArr=array_unique(explode(",",$pi_no));
							foreach($pi_nosArr as $pid)
							{
								$lc_number=$fab_pi_lc_data_arr[$pid]['lc_number'];
								if($lc_number!="")
								{
									$lc_number_lcArr[$lc_number]=$lc_number;
								}
							}
							$issuepo=implode(",",$issuePOidArr);
							
							$booking_noall=implode(", ",$booking_noArr);
							$booking_noall=rtrim($booking_noall,',');
							 $supplierAll=implode(", ",$supplierArr);
							 $supplierAll=rtrim($supplierAll,',');
						 	?>
							<td align="left"><?= $color_arr[$fcolor_id];?></td>
							<td align="left"><?= implode(", ", $gmts_color); ?></td>
							<td align="left"><?= implode(",",$wo_data_arr[$job_id][$lib_yarn_id][$color_id][$fabric_color_id]['wo_date']);  ?></td>
							<td align="right"><?= fn_number_format($row['avg_cons'],4) ?></td>
							<td align="right" title="Avg Rate<?=$bom_value;?>"><?= fn_number_format($bom_value/$bom_qty,4) ?></td>
							<td align="right"><a href='#report_details' onClick="order_req_qty_popup('<? echo $job_id; ?>','<? echo $lib_yarn_id; ?>','<? echo $fabric_color_id; ?>' ,'order_req_qty_data');"><?= fn_number_format($bom_qty,2)?></a></td>
							<td align="right" title="Wo Qty"><a href='#report_details' onClick="order_wo_qty_popup('<? echo $job_id; ?>','<? echo $lib_yarn_id; ?>','<? echo $fabric_color_id; ?>','<? echo implode(",", $gmts_color_id) ?>' ,'order_wo_qty_data');"><?= fn_number_format($wo_qty,2)  ?></a></td>
							<td align="left"><?=$booking_noall; ?></td>
							<td align="left"><?= $unit_of_measurement[$row['fabric_uom']] ?></td>
							<td align="right"><a href='#report_details' onClick="openmypage_inhouse('<? echo $job_id; ?>','<? echo $lib_yarn_id; ?>','<? echo $inhousepo; ?>','<? echo $fabric_color_id ?>' ,'booking_inhouse_info');"><?= fn_number_format($inhouseqty,2) ?></a></td>
							<td align="right"><?= fn_number_format($rcv_balance,2)  ?></td>
							<td align="right"><? if($issueqty>0){ ?><a href='#report_details' onClick="openmypage_issue('<? echo $job_id; ?>','<? echo $lib_yarn_id; ?>','<? echo $issuepo; ?>','<? echo $fabric_color_id?>', '<?echo $rcv_rate?>' ,'booking_issue_info');"><?= fn_number_format($issueqty,2)  ?></a><? } else echo '0.00'; ?></td>
							<td align="right"><?= fn_number_format($issue_balance,2) ?></td>
							<td align="left"><?= $supplierAll; ?></td>
							<td align="left"><p> &nbsp; <?=$pi_nos; ?></p></td>
							<td align="left"><?= implode(", ",$lc_number_lcArr); ?></td>
							<?
							$k++;
							$i++;
						 }
						 
							
						} ?>
							</tr>
						<?
						$sl++;
						}
				?>
				
			</tbody>
		</table>
        <?
        }
        if(count($trims_sql_data) > 1) {
        ?>
		<table class="rpt_table" width="2580" cellpadding="0" cellspacing="0" border="1" rules="all" style="margin-top: 10px">
			<thead>
				<tr>
					<td colspan="24" align="left"><strong>Trims Details</strong></td>
				</tr>
				<tr>
					<th width="35">SL</th>
					<th width="95">Buyer</th>
					<th width="95">Job No</th>
					<th width="95">Season</th>
	                <th width="95">Style Ref</th>
					<th width="60">Order Qnty</th>
					<th width="50">UOM</th>
					<th width="60">Max.Ship Date</th>
					<th width="100">Trims Name</th>
					<th width="150">Item Description</th>
					<th width="100">Brand/Sup Ref</th>
					<th width="80">PO Date</th>
					<th width="80">Avg. Cons</th>
					<th width="80">Rate</th>
					<th width="100">Req Qnty</th>
					<th width="90">PO Qty</th>
					<th width="90">PO NO.</th>
	                <th width="50">UOM</th>
	                <th width="80">In-House Qty</th>
	                <th width="80">Receive Balance</th>
	                <th width="70">Issue to Prod.</th>
					<th width="90">Issue Balance</th>
	                <th width="90">Supplier</th>
					<th width="120">PI No.</th>
					<th width="90">BTB LC N0</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$tsl=1;
				?>
				<?php foreach ($trims_main_data as $job_id=>$value){
					$rowspan= count($value['trims_data']);
					if($tsl%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				 ?>
					<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr2_<? echo $tsl; ?>','<? echo $bgcolor;?>')" id="tr2_<? echo $tsl; ?>">
						<td width="35" rowspan="<?= $rowspan ?>"><?= $tsl; ?></td>
						<td width="95" rowspan="<?= $rowspan ?>"align="left"><?= $buyer_short_name_library[$value['buyer_name']]; ?></td>
						<td width="95" rowspan="<?= $rowspan ?>"align="left"><?= $value['job_no']; ?></td>
						<td width="95" rowspan="<?= $rowspan ?>"align="left"><?= $lib_season_arr[$value['season_buyer_wise']] ?></td>
		                <td width="95" rowspan="<?= $rowspan ?>" align="left">
					<a href='#report_details' onClick="order_summery_popup('<? echo $job_id; ?>' ,'order_summery_data');"><?= $value['style_ref_no']; ?></a></td>
						<td width="60"  align="right" rowspan="<?= $rowspan ?>"><a href='#report_details' onClick="order_summery_popup('<? echo $job_id; ?>' ,'order_qty_data');"><?= $value['job_quantity']; ?></a></td>
						<td width="50" rowspan="<?= $rowspan ?>" align="left"><?= $unit_of_measurement[$value['order_uom']]; ?></th>
						<td width="60" rowspan="<?= $rowspan ?>" align="left"><?= $max_ship_arr[$job_id];  ?></td>
						<?

						$z=1;
						foreach ($value['trims_data'] as $trims_id=>$row) {
							if($z!=1) echo '<tr onclick="change_color(\'trs_'.$z.'\',\''.$bgcolor.'\')" id="trs_'.$z.'">';
							
							$req_qty = $trim_group_qty_arr[$value['job_no']][$trims_id];
							$wo_qty= $trim_wo_data_arr[$job_id][$trims_id]['wo_qnty'];
							
							$description= rtrim($trim_wo_data_arr[$job_id][$trims_id]['description'],'**');
							$descriptions=implode(", ",array_unique(explode("**",$description)));
							$descriptionsArr=array_unique(explode("**",$description));
							$inhouse_qty=$issue_qty=0;
							foreach($descriptionsArr as $desc)
							{
								$inhouse_qty+= $trim_inhouse_qty[$job_id][$row['trim_group']][$desc]['qty'];
								$issue_qty+= $trim_issue_qty[$job_id][$row['trim_group']][$desc]['qty'];
								$po_id= $trim_inhouse_qty[$job_id][$row['trim_group']][$desc]['po_id'];
								$tpo_id= $trim_issue_qty[$job_id][$row['trim_group']][$desc]['po_id'];
							} 

							
							//	if($row['description']!='') $desc=$row['description'];else $desc=0;
							
							$trimrcv_balance = $wo_qty-$inhouse_qty;
							$trims_poid=implode(",",$trims_po_id_arr[$job_id][$row['trim_group']]);
							//echo $row['description'].'D'.$trims_poid.',';
							$trim_pi_no=rtrim($trim_pi_data_arr[$trims_id][$job_id][$row['trim_group']]['pi_no'],',');
							$trim_pi_nos=implode(", ",array_unique(explode(",",$trim_pi_no)));
							
							$trim_blc_no=rtrim($trim_pi_data_arr[$trims_id][$job_id][$row['trim_group']]['blc_no'],',');
							$trim_blc_nos=implode(", ",array_unique(explode(",",$trim_blc_no)));
							
							$trim_pi_btb_lc_arr=array();
							foreach(array_unique(explode(",",$trim_pi_no)) as $tpi)
							{
								//echo $tpi.'D';
								$lc_number=$trim_pi_lc_data_arr[$tpi]['lc_number'];
								if($lc_number!="")
								{
								$trim_pi_btb_lc_arr[$lc_number]=$lc_number;
								}
							}
							$trim_btb_lc_nos=implode(", ",$trim_pi_btb_lc_arr);
						?>
						<td width="100" align="left"><?= $item_library[$row['trim_group']]; ?></td>
						<td width="150" align="left"><?= $descriptions; ?></td>
						<td width="100" align="left"><?= $row['brand_sup_ref']; ?></td>
						<td width="80" align="left"><?= implode(",", $trim_wo_data_arr[$job_id][$trims_id]['wo_date'])?></td>
						<td width="80" align="left"><?= fn_number_format($row['cons_dzn_gmts'],4); ?></td>
						<td width="80" align="left"><?= fn_number_format($row['sourcing_rate'],4); ?></td>
						<td width="100" align="right"><a href='#report_details' onClick="trim_req_qty_popup('<? echo $job_id; ?>','<? echo $trims_id; ?>' ,'trim_req_qty_data');"><?= fn_number_format($req_qty,2); ?></a></td>
						<td width="90" align="right"><a href='#report_details' onClick="trim_wo_qty_popup('<? echo $job_id; ?>','<? echo $trims_id; ?>' ,'trim_wo_qty_data');"><?= fn_number_format($wo_qty,2) ?></a></td>
						<td width="50" align="left"><?= implode(",", $trim_wo_data_arr[$job_id][$trims_id]['booking_no'])?></td>
		                <td width="50" align="right"><?= $unit_of_measurement[$row['cons_uom']]; ?></td>	                
		                <td width="80" align="right" title="Des=<? echo $descriptions;?>"><a href='#report_details' onClick="openmypage_trim_inhouse('<? echo $trims_poid;  ?>','<? echo $row['trim_group'].'__'.$row['description']; ?>','trim_booking_inhouse_info');"><?= fn_number_format($inhouse_qty,2);  ?></a></td>
		                <td width="80" align="right"><?= fn_number_format($trimrcv_balance,2); ?></td>
		                <td width="70" align="right"><a href='#report_details' onClick="openmypage_trim_issue('<? echo $trims_poid; ?>','<? echo $row['trim_group'].'__'.$descriptions; ?>','trim_booking_issue_info');"><?= fn_number_format($issue_qty,2);  ?></a></td>
						<td width="90" align="right"><?= fn_number_format($inhouse_qty-$issue_qty,2); ?></td>
		                <td width="90" align="left"><?=implode(",", $trim_wo_data_arr[$job_id][$trims_id]['supplier'])?></td>
						<td width="120" align="left"><p> &nbsp; <?=$trim_pi_nos;?></p></td>
						<td width="90" title="<?= $trims_id.'='.$job_id.'='.$row['trim_group'];?>" align="left"><p>&nbsp;<?=$trim_btb_lc_nos;//$trim_blc_nos;?></p></td>
						<? $z++;
						 } ?>
					</tr>
				<?php 
					$tsl++;
					} 
				?>								
			</tbody>
		</table>
        <?
        }
        ?>
		<div style="width:2660px; max-height:400px; overflow-y:scroll" id="scroll_body">
		</div>
	</div>
	<?
		$total_data = ob_get_contents();
		ob_clean();
		foreach (glob("*.xls") as $filename) {
		@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename=$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc,$total_data);
		echo "$total_data****$filename";
		exit();
}
if($action=="report_generate3")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
	$country_name_library=return_library_array( "select id, country_name from lib_country", "id", "country_name"  );

	$lib_supplier_arr=return_library_array( "select id, short_name from lib_supplier", "id", "short_name"  );
	$lib_season_arr=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name"  );
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1", "id", "color_name");

	$company_name=str_replace("'","",$cbo_company_name);
	$buyer_id_cond="";
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
				$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
	}

	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_job_no=trim($txt_job_no);

	if($txt_job_no!="" || $txt_job_no!=0) $jobcond="and a.job_no_prefix_num in($txt_job_no)"; else $jobcond="";
	if(str_replace("'","",$txt_style_ref)!="") $style_ref_cond=" and a.style_ref_no like '%".str_replace("'","",$txt_style_ref)."%'"; else $style_ref_cond="";


	$date_type=str_replace("'","",$cbo_date_type);

	$date_cond='';
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		$start_date=(str_replace("'","",$txt_date_from));
		$end_date=(str_replace("'","",$txt_date_to));
		$date_cond="and b.pub_shipment_date between '$start_date' and '$end_date'";
	}

	$cbo_year=str_replace("'","",$cbo_year);
	if($db_type==0)
	{
		if(trim($cbo_year)!=0) $year_cond=" and YEAR(a.insert_date)=$cbo_year"; else $year_cond="";
	}
	else if($db_type==2)
	{
		if(trim($cbo_year)!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year"; else $year_cond="";
	}


	$main_data_sql=sql_select("SELECT a.id, a.job_no, a.buyer_name,d.costing_per, d.costing_date, a.job_no_prefix_num, a.season_buyer_wise, a.style_ref_no, a.job_quantity, a.order_uom, a.company_name,  b.id as po_id, b.po_number,  c.color_number_id, c.order_quantity, e.id as fabric_cost_id, e.lib_yarn_count_deter_id, e.body_part_id,e.fabric_description, e.uom as fabric_uom, avg(f.requirment) as avg_cons, e.color_size_sensitive, e.color, e.color_break_down, g.contrast_color_id, h.stripe_color,e.rate,e.fab_nature_id from wo_po_details_master a join wo_po_break_down b on a.id=b.job_id join wo_po_color_size_breakdown c on a.id=c.job_id and b.id=c.po_break_down_id join wo_pre_cost_mst d on a.id=d.job_id join wo_pre_cost_fabric_cost_dtls e on a.id=e.job_id join wo_pre_cos_fab_co_avg_con_dtls f on e.id=f.pre_cost_fabric_cost_dtls_id left join wo_pre_cos_fab_co_color_dtls g on a.id=g.job_id and e.id=g.pre_cost_fabric_cost_dtls_id and c.color_number_id =g.gmts_color_id and g.status_active=1 and g.is_deleted=0 left join wo_pre_stripe_color h on a.id=h.job_id and  c.item_number_id= h.item_number_id and e.id=h.pre_cost_fabric_cost_dtls_id and f.color_number_id =h.color_number_id and f.po_break_down_id=h.po_break_down_id and f.gmts_sizes=h.size_number_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.company_name=$company_name $year_cond $date_cond $style_ref_cond $jobcond $buyer_id_cond group by a.id, a.job_no, a.buyer_name, a.job_no_prefix_num, a.season_buyer_wise, a.style_ref_no, a.job_quantity, a.order_uom, b.id,b.po_number, c.color_number_id, c.order_quantity, e.lib_yarn_count_deter_id, e.fabric_description, e.uom, e.id, e.color_size_sensitive, e.color, e.body_part_id, e.color_break_down,g.contrast_color_id, h.stripe_color, a.company_name,e.rate,e.fab_nature_id,d.costing_per, d.costing_date  order by e.id asc");
	

    if(count($main_data_sql) > 0) {
        $main_attribute = array('job_no', 'buyer_name', 'job_no_prefix_num', 'season_buyer_wise', 'style_ref_no', 'job_quantity', 'order_uom', 'company_name', 'costing_per', 'costing_date');
        foreach ($main_data_sql as $row) {
            foreach ($main_attribute as $attr) {
                $main_data_arr[$row[csf('id')]][$attr] = $row[csf($attr)];
            }
            $fabricColorId = $row[csf('stripe_color')];
            if (!$fabricColorId) {
                $fabricColorId = $row[csf('contrast_color_id')];
            }
            if (!$fabricColorId) {
                $fabricColorId = $row[csf('color_number_id')];
            }
			 $po_idArr[$row[csf('po_id')]] = $row[csf('id')];
			 
            $main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['item_des'] = $body_part[$row[csf('body_part_id')]] . ',' . $row[csf('fabric_description')];
            $main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['color_id'] = $row[csf('color_number_id')];
            $main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['po_number'] = $row[csf('po_number')];
			$main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['po_id'] .= $row[csf('po_id')].',';
			
            $main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['fabric_uom'] = $row[csf('fabric_uom')];
            $main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['avg_cons'] = $row[csf('avg_cons')];
            $main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['sourcing_rate'] = $row[csf('rate')];
            $main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['sensitive'] = $row[csf('color_size_sensitive')];
            $main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['color'] = $fabricColorId;
           $main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['fab_nature_id'] = $row[csf('fab_nature_id')];

            $job_id_arr[$row[csf('id')]] = $row[csf('id')];
            $fabric_id_arr[$row[csf('fabric_cost_id')]] = $row[csf('fabric_cost_id')];
            $po_id_arr[$row[csf('po_id')]] = $row[csf('po_id')];
        }
        /*echo '<pre>';
        print_r($main_data_arr); die;*/
        $po_id = array_chunk($po_id_arr, 1000, true);
        $order_cond = "";
        $po_cond_for_in2 = "";
        $ji = 0;
        foreach ($po_id as $key => $value) {
            if ($ji == 0) {
                $order_cond = " and c.po_breakdown_id  in(" . implode(",", $value) . ")";
                $po_cond_for_in2 = " and a.po_breakdown_id  in(" . implode(",", $value) . ")";
            } else {
                $order_cond .= " or c.po_breakdown_id  in(" . implode(",", $value) . ")";
                $po_cond_for_in2 .= " or a.po_breakdown_id  in(" . implode(",", $value) . ")";
            }
            $ji++;
        }
        $job_id_chunk = array_chunk($job_id_arr, 1000, true);
        $jobid_cond = "";
        $jobid_cond1 = "";
        $jobid_cond3 = "";
        $i = 0;
        foreach ($job_id_chunk as $key => $value) {
            if ($i == 0) {
                $jobid_cond = " and b.job_id  in(" . implode(",", $value) . ")";
                $jobid_cond1 = " and a.job_id  in(" . implode(",", $value) . ")";
                $jobid_cond3 = " and job_id  in(" . implode(",", $value) . ")";
                $jobid_cond4 = " and i.job_id  in(" . implode(",", $value) . ")";
            } else {
                $jobid_cond .= " or b.job_id  in(" . implode(",", $value) . ")";
                $jobid_cond1 .= " or a.job_id  in(" . implode(",", $value) . ")";
                $jobid_cond3 .= " or job_id  in(" . implode(",", $value) . ")";
                $jobid_cond4 .= " or i.job_id  in(" . implode(",", $value) . ")";
            }
            $i++;
        }

        $rowspan = array();
        foreach ($main_data_arr as $job_id => $jod_arr) {
            foreach ($jod_arr['color_data'] as $color_data) {
                foreach ($color_data['fabric_color'] as $row) {
                    $rowspan[$job_id]++;
                }
            }
        }
        $fabric_id_str = implode(",", $fabric_id_arr);

        $wo_data_sql = sql_select("SELECT a.job_id, a.lib_yarn_count_deter_id, b.id, b.booking_date,b.booking_no, c.gmts_color_id, c.fin_fab_qnty, c.amount,	b.supplier_id, b.fabric_source,b.is_approved,b.item_category, c.fabric_color_id FROM wo_pre_cost_fabric_cost_dtls a join wo_booking_dtls c on a.id=c.pre_cost_fabric_cost_dtls_id join wo_booking_mst b on b.booking_no=c.booking_no where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id in ($fabric_id_str) and c.fin_fab_qnty is not null ");
        foreach ($wo_data_sql as $row) {
            $wo_data_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]]['fin_fab_qnty'] += $row[csf('fin_fab_qnty')];
            $wo_data_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]]['amount'] += $row[csf('amount')];
            $wo_data_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]]['wo_date'][$row[csf('id')]] = $row[csf('booking_date')];
            $wo_data_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]]['booking_no'][$row[csf('id')]] = $row[csf('booking_no')];
			$wo_data_array[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]][$row[csf('booking_no')]]['booking_no'] = $row[csf('booking_no')];

			$wo_data_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]]['fabric_source'][$row[csf('id')]] = $row[csf('fabric_source')];
			$wo_data_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]]['is_approved'][$row[csf('id')]] = $row[csf('is_approved')];
			$wo_data_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]]['fabric_nature'][$row[csf('id')]] = $row[csf('item_category')];
            $wo_data_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]]['supplier'][$row[csf('id')]] = $lib_supplier_arr[$row[csf('supplier_id')]];
			$bookArr[$row[csf('job_id')]]['Booking_No'] = $row[csf('booking_no')]; 
        }
		unset($wo_data_sql);

		$receive_qty_data = sql_select("SELECT c.po_breakdown_id as po_id,d.detarmination_id, d.color, c.quantity as order_qnty, b.order_amount,   b.order_rate	from inv_receive_master a , inv_transaction b , order_wise_pro_details c , product_details_master d   where  a.id = b.mst_id and  b.id = c.trans_id  and  d.id=c.prod_id  and  d.id=b.prod_id  and a.entry_form=17 and a.receive_basis in (1,2) and a.status_active=1 and a.is_deleted=0 and b.receive_basis in (1,2) and b.transaction_type=1 and b.status_active=1 and b.is_deleted=0 and c.trans_type=1 and c.entry_form=17 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $order_cond
		union all SELECT c.po_breakdown_id as po_id,d.detarmination_id, d.color, c.quantity as order_qnty, b.order_amount, b.order_rate from inv_receive_master a , inv_transaction b , order_wise_pro_details c , product_details_master d where a.id = b.mst_id and b.id = c.trans_id and d.id=c.prod_id and d.id=b.prod_id and a.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.transaction_type=1 and b.status_active=1 and b.is_deleted=0 and a.item_category=2 and c.trans_type=1 and c.entry_form=37 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $order_cond");	 
 
        $receive_qty_arr = array();
        foreach ($receive_qty_data as $row) {
			$job_id=$po_idArr[$row[csf('po_id')]];
            $receive_qty_arr[$job_id][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['qty'] += $row[csf('order_qnty')];
            $receive_qty_arr[$job_id][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['amount'] += $row[csf('order_amount')];
            $receive_qty_arr[$job_id][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['rate'] = $row[csf('order_rate')];
            $receive_qty_arr[$job_id][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['po_id'] = $row[csf('po_id')];
        }
		unset($receive_qty_data);
        $receive_rtn_qty_data_wvn = sql_select("SELECT d.detarmination_id, c.color_id as color, c.quantity as cons_quantity, b.cons_amount, e.job_id, e.id as po_id,d.supplier_id from inv_issue_master a join inv_transaction b on a.id = b.mst_id join order_wise_pro_details c on b.id = c.trans_id join product_details_master d on d.id=c.prod_id join wo_po_break_down e on e.id=c.po_breakdown_id where a.entry_form=202 and a.status_active=1 and a.is_deleted=0 and b.transaction_type=3 and b.status_active=1 and b.is_deleted=0 and c.trans_type=3 and c.entry_form=202 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $order_cond");

        /*union all 
		  	SELECT d.detarmination_id, c.color_id as color, c.quantity as cons_quantity, b.cons_amount, e.job_id, e.id as po_id,d.supplier_id 
		  	from inv_issue_master a join inv_transaction b on a.id = b.mst_id join order_wise_pro_details c on b.id = c.trans_id join product_details_master d on d.id=c.prod_id join wo_po_break_down e on e.id=c.po_breakdown_id where a.entry_form=18 and a.status_active=1 and a.is_deleted=0 and b.transaction_type=2 and b.status_active=1  and a.item_category=2 and b.is_deleted=0 and c.trans_type=2 and c.entry_form=18 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $order_cond"*/

        $receive_rtn_qty_arr = array();
        foreach ($receive_rtn_qty_data_wvn as $row) {
            $receive_rtn_qty_arr[$row[csf('job_id')]][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['qty'] += $row[csf('cons_quantity')];

        }
		unset($receive_rtn_qty_data_wvn);


      
		  $issue_qty_data = sql_select("SELECT d.detarmination_id, c.color_id as color, c.quantity as cons_quantity, b.cons_amount, e.job_id, e.id as po_id,d.supplier_id from inv_issue_master a join inv_transaction b on a.id = b.mst_id join order_wise_pro_details c on b.id = c.trans_id join product_details_master d on d.id=c.prod_id join wo_po_break_down e on e.id=c.po_breakdown_id where a.entry_form=19 and a.issue_basis=1 and a.status_active=1 and a.is_deleted=0 and b.transaction_type=2 and b.status_active=1 and b.is_deleted=0 and c.trans_type=2 and c.entry_form=19 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $order_cond
		  	union all 
		  	SELECT d.detarmination_id, c.color_id as color, c.quantity as cons_quantity, b.cons_amount, e.job_id, e.id as po_id,d.supplier_id 
		  	from inv_issue_master a join inv_transaction b on a.id = b.mst_id join order_wise_pro_details c on b.id = c.trans_id join product_details_master d on d.id=c.prod_id join wo_po_break_down e on e.id=c.po_breakdown_id where a.entry_form=18 and a.status_active=1 and a.is_deleted=0 and b.transaction_type=2 and b.status_active=1  and a.item_category=2 and b.is_deleted=0 and c.trans_type=2 and c.entry_form=18 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $order_cond" );

        $issue_qty_arr = array();$ttqty=0;$issue_po_arr = array();$issue_po_chk_arr = array();
        foreach ($issue_qty_data as $row) {
            $rate = $receive_qty_arr[$row[csf('job_id')]][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['rate'];
            $issue_qty_arr[$row[csf('job_id')]][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['qty'] += $row[csf('cons_quantity')];
            $issue_qty_arr[$row[csf('job_id')]][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['amount'] += $row[csf('cons_quantity')] * $rate;
             if($issue_po_chk_arr[$row[csf('job_id')]][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]!=$row[csf('po_id')])
            {
            	$issue_po_arr[$row[csf('job_id')]][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['po_id']= $row[csf('po_id')];
            	$issue_po_chk_arr[$row[csf('job_id')]][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]=$row[csf('po_id')];
            }
            $issue_qty_arr[$row[csf('job_id')]][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['supplier'] = $lib_supplier_arr[$row[csf('supplier_id')]];
			
			//$ttqty+= $row[csf('cons_quantity')];
        }
		unset($issue_qty_data);
		//echo $ttqty.'D';

		$issue_rtn_qty_arr_wvn = sql_select("SELECT c.po_breakdown_id as po_id,d.detarmination_id, d.color, c.quantity as order_qnty, b.order_amount,   b.order_rate	from inv_receive_master a , inv_transaction b , order_wise_pro_details c , product_details_master d   where  a.id = b.mst_id and  b.id = c.trans_id  and  d.id=c.prod_id  and  d.id=b.prod_id  and a.entry_form=209  and a.status_active=1 and a.is_deleted=0  and b.transaction_type=4 and b.status_active=1 and b.is_deleted=0 and c.trans_type=4 and c.entry_form=209 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $order_cond");	

		$issue_rtnQty_arr_wvn = array();
        foreach ($issue_rtn_qty_arr_wvn as $row) {
            $issue_rtnQty_arr_wvn[$job_id][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['qty'] += $row[csf('order_qnty')];
        }
		unset($issue_rtn_qty_arr_wvn);
        /*echo "<pre>";
        	print_r($issue_rtnQty_arr_wvn);
        echo "</pre>";*/

		/*union all SELECT c.po_breakdown_id as po_id,d.detarmination_id, d.color, c.quantity as order_qnty, b.order_amount, b.order_rate from inv_receive_master a , inv_transaction b , order_wise_pro_details c , product_details_master d where a.id = b.mst_id and b.id = c.trans_id and d.id=c.prod_id and d.id=b.prod_id and a.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.transaction_type=1 and b.status_active=1 and b.is_deleted=0 and a.item_category=2 and c.trans_type=1 and c.entry_form=37 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $order_cond */



        $fabric_supplier_data = sql_select("SELECT a.job_id, a.supplier_id, b.lib_yarn_count_deter_id from wo_pre_cost_fabric_supplier a join wo_pre_cost_fabric_cost_dtls b on a.JOB_ID=b.JOB_ID and b.id=a.fabric_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $jobid_cond");

        $fabric_supplier_arr = array();
        foreach ($fabric_supplier_data as $row) {
            $fabric_supplier_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('supplier_id')]] = $lib_supplier_arr[$row[csf('supplier_id')]];
        }
		unset($fabric_supplier_data);
        $pi_number_data = sql_select("SELECT f.style_ref_no,d.pi_number,f.job_no,i.job_id,c.determination_id, c.amount, c.color_id as color_id from wo_booking_dtls b left join  wo_pre_cost_fabric_cost_dtls i on   b.job_no = i.job_no,com_pi_item_details c , com_pi_master_details d ,wo_po_details_master f	where b.booking_no=c.work_order_no   and b.job_no = f.job_no and d.id=c.pi_id and b.status_active=1 and b.is_deleted=0 	and c.status_active=1 and c.is_deleted=0 and d.pi_basis_id in (1,2) and d.status_active=1 and d.is_deleted=0 and d.importer_id in ($company_name)  $jobid_cond4 and d.entry_form=166	group by f.style_ref_no,c.determination_id,d.pi_number,f.job_no ,i.job_id, c.amount, c.color_id");

        $pi_data_arr = array();
        foreach ($pi_number_data as $key => $row) {
            $pi_data_arr[$row[csf('job_id')]][$row[csf('determination_id')]][$row[csf('color_id')]]['pi_no'].= $row[csf('pi_number')].',';
            $pi_data_arr[$row[csf('job_id')]][$row[csf('determination_id')]][$row[csf('color_id')]]['amount'] += $row[csf('amount')];
            //$pi_data_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('color_id')]]['blc_no']=$row[csf('lc_number')];
        }
		unset($pi_number_data);
        $pi_number_data_lc = sql_select("SELECT f.style_ref_no,d.pi_number,g.lc_number,f.job_no,i.job_id, c.determination_id, c.amount, c.color_id as color_id from wo_booking_dtls b left join  wo_pre_cost_fabric_cost_dtls i on   b.job_no = i.job_no,com_pi_item_details c , com_pi_master_details d ,wo_po_details_master f,com_btb_lc_master_details g,com_btb_lc_pi h 	where b.booking_no=c.work_order_no and g.id=h.com_btb_lc_master_details_id and h.pi_id=d.id and b.job_no = f.job_no and d.id=c.pi_id and b.status_active=1 and b.is_deleted=0 	and c.status_active=1 and c.is_deleted=0 and g.item_basis_id in (1,2) and d.status_active=1 and d.is_deleted=0 and g.importer_id in ($company_name)  $jobid_cond4 and d.entry_form=167	group by f.style_ref_no,d.pi_number,g.lc_number,f.job_no ,i.job_id, c.determination_id, c.amount, c.color_id");
	

        foreach ($pi_number_data_lc as $key => $row) {

            $pi_data_arr[$row[csf('job_id')]][$row[csf('determination_id')]][$row[csf('color_id')]]['blc_no'].= $row[csf('lc_number')].',';
        }
        unset($pi_number_data_lc);


        /*echo '<pre>';
        print_r($pi_data_arr); die;*/
        $max_shipment_date_sql = sql_select("SELECT MAX(pub_shipment_date) as pub_shipment_date ,job_id from wo_po_break_down where  status_active=1 and is_deleted=0 $jobid_cond3 group by job_id");
        foreach ($max_shipment_date_sql as $row) {
            $max_ship_arr[$row[csf('job_id')]] = $row[csf('pub_shipment_date')];
        }
		unset($max_shipment_date_sql);
        $condition = new condition();
        if (count($job_id_arr) > 0) {
            $job_id_str = implode(",", $job_id_arr);
            $condition->jobid_in($job_id_str);
        }
        $condition->init();
        $fabric = new fabric($condition);
        $fabric_qty_arr = $fabric->getQtyArray_by_JobIdLibYarnCountDeterIdAndGmtscolor_knitAndwoven_greyAndfinish();
        /*echo "<pre>";
        	print_r($fabric_qty_arr);
        echo "</pre>";*/
        //$fabric_amount_arr=$fabric->getAmountArray_by_JobIdLibYarnCountDeterIdAndGmtscolor_knitAndwoven_greyAndfinish();
        $fabric_amount_arr = $fabric->getAmountArr_by_JobIdYarnCountIdGmtsAndFabricColor_source();
    }
	/*Trims Data Start from Here*/


		$trim_sql_qry = "SELECT a.id as job_id,a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, a.season_buyer_wise, b.id, b.po_number, a.order_uom, a.job_quantity, a.total_set_qnty, b.pub_shipment_date, d.costing_per,d.costing_date, e.id as trim_dtla_id, e.trim_group, e.remark, e.description, e.brand_sup_ref, e.cons_uom, e.cons_dzn_gmts, e.rate, e.amount, e.apvl_req, e.nominated_supp, e.insert_date,e.sourcing_rate from wo_po_details_master a join wo_po_break_down b on a.job_no=b.job_no_mst join wo_po_color_size_breakdown c on a.id=c.job_id and b.id=c.po_break_down_id join wo_pre_cost_mst d on a.id =d.job_id join wo_pre_cost_trim_cost_dtls e on e.job_id = d.job_id $item_group_cond left join  wo_pre_cost_trim_co_cons_dtls f on c.job_id=f.job_id and c.po_break_down_id=f.po_break_down_id and f.job_id=e.job_id and e.id=f.wo_pre_cost_trim_cost_dtls_id where f.cons > 0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and e.is_deleted=0 and e.status_active=1 and a.company_name=$company_name $buyer_id_cond ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $date_cond $style_ref_cond $year_cond $jobcond $jobid_cond group by a.id, a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, a.season_buyer_wise,  b.id, b.po_number, a.order_uom, a.total_set_qnty, b.pub_shipment_date,  d.costing_per, e.id, e.trim_group, e.remark, e.description, e.brand_sup_ref, e.cons_uom, e.cons_dzn_gmts, e.rate, e.amount, e.apvl_req, e.nominated_supp, e.insert_date, a.job_quantity, d.costing_date,e.sourcing_rate order by e.id, e.trim_group";


	
	//echo $trim_sql_qry; die;
	
	$trims_sql_data= sql_select($trim_sql_qry);
    if(count($trims_sql_data) > 1) {
        $trims_main_attribute = array('job_no', 'buyer_name', 'job_no_prefix_num', 'style_ref_no', 'season_buyer_wise', 'order_uom', 'job_quantity', 'costing_per', 'costing_date');
        $trims_dtls_attribute = array('trim_dtla_id', 'trim_group', 'description', 'brand_sup_ref', 'cons_uom', 'cons_dzn_gmts', 'po_number', 'sourcing_rate','rate','id');
        foreach ($trims_sql_data as $row) {

            foreach ($trims_main_attribute as $attr) {
                $trims_main_data[$row[csf('job_id')]][$attr] = $row[csf($attr)];
            }
            foreach ($trims_dtls_attribute as $tattr) {
                $trims_main_data[$row[csf('job_id')]]['trims_data'][$row[csf('trim_dtla_id')]][$tattr] = $row[csf($tattr)];
            }
            $trimjob_id_arr[$row[csf('job_id')]] = $row[csf('job_id')];
            $trim_id_arr[$row[csf('trim_dtla_id')]] = $row[csf('trim_dtla_id')];
            $trim_poid_arr[$row[csf('id')]] = $row[csf('id')];
			$trim_poid_arr[$row[csf('id')]] = $row[csf('id')];
			$trimGroupArr[$row[csf('trim_group')]] = $row[csf('trim_group')];

        }
		$trimPoids=implode(",",$trim_poid_arr);
		$trimitemids=implode(",",$trimGroupArr);
        $trimjob_id_chunk = array_chunk($trimjob_id_arr, 1000, true);
        $jobid_cond1 = "";
        $jobid_cond = "";
        $i = 0;
        foreach ($trimjob_id_chunk as $key => $value) {
            if ($i == 0) {
                $jobid_cond = " and b.job_id  in(" . implode(",", $value) . ")";
                $jobid_cond1 = " and a.job_id  in(" . implode(",", $value) . ")";
            } else {
                $jobid_cond = " or b.job_id  in(" . implode(",", $value) . ")";
                $jobid_cond1 .= " or a.job_id  in(" . implode(",", $value) . ")";
            }
            $i++;
        }

        $trim_id_chunk = array_chunk($trim_id_arr, 1000, true);
        $trimid_cond = "";
        $ji = 0;
        foreach ($trim_id_chunk as $key => $value) {
            if ($ji == 0) {
                $trimid_cond = " and a.id  in(" . implode(",", $value) . ")";
            } else {
                $trimid_cond .= " or a.id  in(" . implode(",", $value) . ")";
            }
            $ji++;
        }
	  $trim_wo_data_sql = sql_select("SELECT a.id as trim_id, a.job_id, b.id as booking_id, b.booking_date, b.booking_no, d.requirment as wo_qnty,d.amount, d.description, b.supplier_id,b.buyer_id,b.is_approved,b.cbo_level,c.id as booking_dtls_id FROM wo_pre_cost_trim_cost_dtls a , wo_booking_dtls c  , wo_booking_mst b,wo_trim_book_con_dtls d where a.id=c.pre_cost_fabric_cost_dtls_id and d.wo_trim_booking_dtls_id=c.id and d.po_break_down_id=c.po_break_down_id and d.booking_no=b.booking_no and  b.booking_no=c.booking_no and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.booking_type=2 and c.is_workable=1 $trimid_cond ");
		 
        foreach ($trim_wo_data_sql as $row) {
            $trim_wo_data_arr[$row[csf('job_id')]][$row[csf('trim_id')]]['wo_qnty'] += $row[csf('wo_qnty')];
            $trim_wo_data_arr[$row[csf('job_id')]][$row[csf('trim_id')]]['wo_amount'] += $row[csf('amount')];
			$trim_wo_data_arr[$row[csf('job_id')]][$row[csf('trim_id')]]['description'].= $row[csf('description')].'**';
            $trim_wo_data_arr[$row[csf('job_id')]][$row[csf('trim_id')]]['wo_date'][$row[csf('booking_id')]] = $row[csf('booking_date')];
            $trim_wo_data_arr[$row[csf('job_id')]][$row[csf('trim_id')]]['booking_no'][$row[csf('booking_id')]] = $row[csf('booking_no')];
			
			$trim_wo_data_arr[$row[csf('job_id')]][$row[csf('trim_id')]]['buyer_id'][$row[csf('booking_id')]] = $row[csf('buyer_id')];
			$trim_wo_data_arr[$row[csf('job_id')]][$row[csf('trim_id')]]['is_approved'][$row[csf('booking_id')]] = $row[csf('is_approved')];
			$trim_wo_data_arr[$row[csf('job_id')]][$row[csf('trim_id')]]['cbo_level'][$row[csf('booking_id')]] = $row[csf('cbo_level')];
            $trim_wo_data_arr[$row[csf('job_id')]][$row[csf('trim_id')]]['supplier'][$row[csf('booking_id')]] = $lib_supplier_arr[$row[csf('supplier_id')]];

			$trim_wo_data_array[$row[csf('job_id')]][$row[csf('trim_id')]][$row[csf('booking_no')]]['booking_no'] = $row[csf('booking_no')];

			$BookingArr[$row[csf('job_id')]]['BOOKNO'] = $row[csf('booking_no')] ;
			
        }
		unset($trim_wo_data_sql);
		$trim_wo_data_sql2 = sql_select("SELECT a.id as trim_id, a.job_id, b.id as booking_id, b.booking_no,c.description,d.description booking_desc,c.trim_group,c.po_break_down_id FROM wo_pre_cost_trim_cost_dtls a join wo_booking_dtls c on a.id=c.pre_cost_fabric_cost_dtls_id join wo_booking_mst b on b.booking_no=c.booking_no join   wo_trim_book_con_dtls d on c.id=d.wo_trim_booking_dtls_id	where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.booking_type=2 and c.is_workable=1 $trimid_cond and c.po_break_down_id in ($trimPoids) and c.trim_group in ($trimitemids)	group by a.id , a.job_id, b.id , b.booking_no,c.description,d.description,c.trim_group,c.po_break_down_id");
			foreach ($trim_wo_data_sql2 as $row) {
				$trim_wo_data_arr2[$row[csf('job_id')]][$row[csf('trim_id')]][$row[csf('trim_group')]][$row[csf('booking_desc')]] = $row[csf('booking_desc')];
			}
			unset($trim_wo_data_sql2);

        $po_id_chunk = array_chunk($trim_poid_arr, 1000, true);
        $order_cond = "";
        $pi = 0;
        foreach ($po_id_chunk as $key => $value) {
            if ($pi == 0) {
                $order_cond = " and b.po_breakdown_id  in(" . implode(",", $value) . ")";
            } else {
                $order_cond .= " or b.po_breakdown_id  in(" . implode(",", $value) . ")";
            }
            $pi++;
        }

        $receive_qty_data = sql_select("SELECT b.po_breakdown_id, a.item_group_id,a.item_description, sum(b.quantity) as quantity, a.rate, e.job_id from inv_receive_master c,product_details_master d,inv_trims_entry_dtls a , order_wise_pro_details b, wo_po_break_down e where a.mst_id=c.id and a.trans_id=b.trans_id and a.id=b.dtls_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and d.status_active=1 and d.is_deleted=0 and e.id=b.po_breakdown_id $order_cond group by b.po_breakdown_id, a.item_group_id,a.item_description,a.rate, e.job_id order by a.item_group_id ");
        $trim_inhouse_qty = array();
        foreach ($receive_qty_data as $row) { 
            $trim_inhouse_qty[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['qty'] += $row[csf('quantity')];
            $trim_inhouse_qty[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['amount'] += $row[csf('quantity')] * $row[csf('rate')];
            $trim_inhouse_qty[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['rate'] = $row[csf('rate')];
            $trim_inhouse_qty[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['po_id'] = $row[csf('po_breakdown_id')];
            $trims_po_id_arr[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
        }
		unset($receive_qty_data);
		$receive_rtn_qty_data=sql_select("select e.job_id,b.po_breakdown_id, c.item_group_id,c.item_description, b.quantity as quantity, b.order_rate as rate
			from product_details_master c,order_wise_pro_details b,wo_po_break_down e 
			where b.prod_id=c.id  and e.id=b.po_breakdown_id and b.trans_type=3 and b.entry_form=49 and b.status_active=1 and b.is_deleted=0 $order_cond");
			foreach($receive_rtn_qty_data as $row)
			{
				 $receive_rtnArr[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['qty'] += $row[csf('quantity')];
				 $receive_rtnArr[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['amount'] += $row[csf('quantity')]* $row[csf('rate')];
			}
			//echo "<pre>";print_r($style_data_arr);
			unset($receive_rtn_qty_data);

        $trim_issue_qty_data = sql_select("SELECT b.po_breakdown_id, a.item_group_id,p.item_description as item_description, sum(b.quantity) as quantity, a.rate, e.job_id  from  inv_issue_master d,product_details_master p,inv_trims_issue_dtls a , order_wise_pro_details b, wo_po_break_down e where a.mst_id=d.id and a.prod_id=p.id and a.trans_id=b.trans_id and b.trans_type=2 and b.entry_form=25 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and p.status_active=1 and p.is_deleted=0 and e.id=b.po_breakdown_id  $order_cond group by b.po_breakdown_id, a.item_group_id,p.item_description, e.job_id,a.rate");
		//echo "SELECT b.po_breakdown_id, a.item_group_id,p.item_description as item_description, sum(b.quantity) as quantity, a.rate, e.job_id  from  inv_issue_master d,product_details_master p,inv_trims_issue_dtls a , order_wise_pro_details b, wo_po_break_down e where a.mst_id=d.id and a.prod_id=p.id and a.trans_id=b.trans_id and b.trans_type=2 and b.entry_form=25 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.id=b.po_breakdown_id  $order_cond group by b.po_breakdown_id, a.item_group_id,p.item_description, e.job_id,a.rate";
		
        $trim_issue_qty = array();
        foreach ($trim_issue_qty_data as $row) {
            $rate = $trim_inhouse_qty[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['rate'];
            $trim_issue_qty[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['qty'] += $row[csf('quantity')];
            $trim_issue_qty[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['amount'] += $row[csf('quantity')] * $rate;
            $trim_issue_qty[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['po_id'] = $row[csf('po_breakdown_id')];
        }
		unset($trim_issue_qty_data);
		$issue_rtn_qty_data = sql_select("SELECT b.po_breakdown_id, d.item_group_id,d.item_description, (b.quantity) as quantity,  e.job_id from inv_receive_master c,inv_transaction a, order_wise_pro_details b, product_details_master d, wo_po_break_down e where c.id=a.mst_id and a.id=b.trans_id and a.prod_id=b.prod_id and b.prod_id=d.id and a.prod_id=d.id and e.id=b.po_breakdown_id $order_cond and b.trans_type=4 and b.entry_form=73 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and d.status_active=1 and d.is_deleted=0 order by d.item_group_id");

        $trim_issue_rtn_qty = array();
        foreach ($issue_rtn_qty_data as $row) {
        	$trim_issue_rtn_qty[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['qty'] += $row[csf('quantity')];
        }
        unset($issue_rtn_qty_data);
		
        $trim_supplier_data = sql_select("SELECT a.job_id, a.supplier_id, b.id as trim_cost_id from wo_pre_cost_trim_supplier a join wo_pre_cost_trim_cost_dtls b on a.job_id=b.job_id and b.id=a.trimid where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $jobid_cond");

        $trim_supplier_arr = array();
        foreach ($trim_supplier_data as $row) {
            $trim_supplier_arr[$row[csf('job_id')]][$row[csf('trim_cost_id')]][$row[csf('supplier_id')]] = $lib_supplier_arr[$row[csf('supplier_id')]];
        }
		unset($trim_supplier_data);
		$trims_pi_data=sql_select("SELECT a.id, a.lc_number,a.lc_type_id,a.payterm_id,a.tenor, c.pi_number FROM com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_master_details c WHERE a.id=b.com_btb_lc_master_details_id and b.pi_id=c.id and a.is_deleted = 0 and a.item_basis_id=1 and a.importer_id=$company_name group by a.id, a.lc_number,a.lc_type_id,a.payterm_id,a.tenor,c.pi_number UNION ALL SELECT a.id, a.lc_number,a.lc_type_id,a.payterm_id,a.tenor,c.pi_number FROM com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_master_details c WHERE a.id=b.com_btb_lc_master_details_id and b.pi_id=c.id and a.is_deleted = 0 and a.item_basis_id=2 and a.importer_id=$company_name group by a.id, a.lc_number,a.lc_type_id,a.payterm_id,a.tenor, c.pi_number order by id desc ");
			$trim_pi_lc_data_arr = array();$fab_pi_lc_data_arr = array();
			foreach ($trims_pi_data as $key => $row) {
				$trim_pi_lc_data_arr[$row[csf('pi_number')]]['lc_number']= $row[csf('lc_number')];;
				$fab_pi_lc_data_arr[$row[csf('pi_number')]]['lc_number']= $row[csf('lc_number')];
			}
			unset($trims_pi_data);
        $trims_pi_number_data = sql_select("SELECT a.job_id,d.pi_number, c.amount, e.lc_number, c.item_group, b.pre_cost_fabric_cost_dtls_id, c.item_color, c.item_size from wo_pre_cost_trim_cost_dtls a join wo_booking_dtls b on a.id=b.pre_cost_fabric_cost_dtls_id join com_pi_item_details c on b.booking_no=c.work_order_no  join com_pi_master_details d on d.id=c.pi_id left join com_btb_lc_master_details e on TO_CHAR(c.pi_id)=e.pi_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.entry_form=167 $jobid_cond1 and b.is_workable=1 group by a.job_id, d.pi_number, c.amount, e.lc_number, c.item_group, b.pre_cost_fabric_cost_dtls_id, c.item_color, c.item_size");
        $trim_pi_data_arr = array();
        foreach ($trims_pi_number_data as $key => $row) {
            $trim_pi_data_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('job_id')]][$row[csf('item_group')]]['pi_no'].= $row[csf('pi_number')].',';
            $trim_pi_data_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('job_id')]][$row[csf('item_group')]]['amount'] += $row[csf('amount')];
            $trim_pi_data_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('job_id')]][$row[csf('item_group')]]['blc_no'].= $row[csf('lc_number')].',';
        }
		unset($trims_pi_number_data);
        if (count($trimjob_id_arr) > 0) {
            $trimjob_id_str = implode(",", $trimjob_id_arr);
            $condition->jobid_in($trimjob_id_str);
        }
        $condition->init();
        $trim = new trims($condition);
        $trim_group_qty_arr = $trim->getQtyArray_by_jobAndPrecostdtlsid();

        //$trim_group_amount_arr=$trim->getAmountArray_by_jobAndPrecostdtlsid();

        $trim_amountSourcing_arr = $trim->getAmountArray_precostdtlsidSourcing();

        $partial_fabric_report_type = array(84 => 'show_fabric_booking_report_urmi_per_job', 85 => 'print_booking_3', 143 => 'show_fabric_booking_report_urmi', 151 => 'show_fabric_booking_report_advance_attire_ltd', 160 => 'print_booking_5', 175 => 'print_booking_6', 155 => 'fabric_booking_report', 235 => 'print_9', 191 => 'print_booking_7');

        $print_report_format = return_field_value("format_id", " lib_report_template", "template_name ='" . $company_name . "'  and module_id=2 and report_id=35 and is_deleted=0 and status_active=1");
        $print_report_format_arr = explode(",", $print_report_format);
        $fabric_report_first_id = $print_report_format_arr[0];
    }
	ob_start();
	?>
	<div style="width:2000px">
		<table width="2560">
	        <tr class="form_caption"><td colspan="18" align="center"><? echo $report_title; ?></td></tr>
	        <tr class="form_caption"><td colspan="18" align="center"><? echo $company_library[$company_name]; ?></td></tr>
		</table>
        <?   
        if(count($main_data_sql) > 0)
		 {
        ?>
		<table class="rpt_table" width="2560" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
					<tr>
						<td colspan="25" align="left"><strong>Fabric Details</strong></td>
					</tr>
					<tr>
					<th width="30">SL</th>
					<th width="100">Buyer</th>
					<th width="100">Job No</th>
					<th width="80">Season</th>
					<th width="130">Style Ref</th>
					<th width="100">Order Qty</th>
					<th width="50">UOM</th>
					<th width="60">Max.Ship Date</th>
					<th width="200">Item Description</th>
					<th width="100">Fabric Color</th>
					<th width="100">Garments Color</th>
					<th width="80">WO Date</th>
					<th width="60">Avg. Cons</th>
					<th width="60">Rate</th>
					<th width="80">Req Qty</th>					
					<th width="80">WO Qty</th>
					<th width="80">WO NO.</th>
					<th width="50">UOM</th>
					<th width="80">In-House Qty</th>
					<th width="80">Receive Balance</th>
					<th width="80">Issue to Cutting</th>
					<th width="80">Issue Balance</th>
					<th width="100">Supplier</th>
					<th width="120">PI No.</th>
					<th width="100">BTB LC N0</th>
				</tr>
			</thead>
			<tbody>
				<? $sl=1;  
			      	//===================start===============Print Report Format======================================
					$print_sql_data=sql_select("select format_id,template_name from lib_report_template where template_name in ($company_name) and module_id=2 and report_id=122 and is_deleted=0 and status_active=1");

					$report_action="";$report_arr=array();
					foreach($print_sql_data as $report_val){

						$reportIds=explode(",",$report_val[csf('format_id')]);		
							if($reportIds[0]==51)  $report_action='preCostRpt2';		
							if($reportIds[0]==158) $report_action='preCostRptWoven';
							if($reportIds[0]==159) $report_action='bomRptWoven';
							if($reportIds[0]==170) $report_action='preCostRpt3';
							if($reportIds[0]==192) $report_action='checkListRpt';
							if($reportIds[0]==307) $report_action='basic_cost';
							if($reportIds[0]==311) $report_action='bom_epm_woven';
							if($reportIds[0]==313) $report_action='mkt_source_cost';
							if($reportIds[0]==381) $report_action='mo_sheet_2';
							if($reportIds[0]==260) $report_action='bomRptWoven_2';
							if($reportIds[0]==761) $report_action='bom_pcs_woven';
							if($reportIds[0]==403) $report_action='mo_sheet_3';
							if($reportIds[0]==770) $report_action='bom_pcs_woven2';
							if($reportIds[0]==473) $report_action='slgCostRpt';
							if($reportIds[0]==852) $report_action='bom_pcs_woven4';
							if($reportIds[0]==197) $report_action='bom_pcs_woven3';
							if($reportIds[0]==873) $report_action='bom_pcs_woven6';
						$report_arr[$report_val[csf('template_name')]][122]=$report_action;

					}
					///////Pre-Costing V2-Woven

							//---------------------woven partial booking------------------------


				$wvp_sql_data=sql_select("select format_id,template_name from lib_report_template where template_name in ($company_name) and module_id=2 and report_id=138 and is_deleted=0 and status_active=1");

				foreach($wvp_sql_data as $report_val){

					$reportIds=explode(",",$report_val[csf('format_id')]);	
						if($reportIds[0]==143){ $report_action='show_fabric_booking_report_urmi';}
						//echo $reportIds[0].'FD';
						if($reportIds[0]==84){ $report_action='show_fabric_booking_report_urmi_per_job';}
						if($reportIds[0]==85){ $report_action='print_booking_3';}
						if($reportIds[0]==151){ $report_action='show_fabric_booking_report_advance_attire_ltd';}
						if($reportIds[0]==160){ $report_action='print_booking_5';}
						if($reportIds[0]==175){ $report_action='print_booking_6';}
						if($reportIds[0]==241){ $report_action='print_booking_11';}
						if($reportIds[0]==155){ $report_action='fabric_booking_report';}
						if($reportIds[0]==274){ $report_action='print_booking_10';}
						if($reportIds[0]==72){ $report_action='print6booking';}
						if($reportIds[0]==428){ $report_action='print_booking_eg1';}
					$report_arr[$report_val[csf('template_name')]][138]=$report_action;


				}
				//echo $report_action.'S';

				//--------------------trims booking------------------------


				$trims_booking_data=sql_select("select format_id,template_name from lib_report_template where template_name in ($company_name) and module_id=2 and report_id in(219) and is_deleted=0 and status_active=1");

				foreach($trims_booking_data as $report_val){

					$reportIds=explode(",",$report_val[csf('format_id')]);	
						
						if($reportIds[0]==183){
							$report_action='show_trim_booking_report2';
						}
						if($reportIds[0]==67){
							$report_action='show_trim_booking_report';
						}
						if($reportIds[0]==177){
							$report_action='show_trim_booking_report4';
						}
						if($reportIds[0]==175){
							$report_action='show_trim_booking_report5';
						}
						if($reportIds[0]==235){
							$report_action='show_trim_booking_report9';
						}
						if($reportIds[0]==85){
							$report_action='print_t';
						}
						if($reportIds[0]==746){
							$report_action='print_t7';
						}
						if($reportIds[0]==774){
							$report_action='show_trim_booking_report_wg';
						}
						if($reportIds[0]==14){
							$report_action='show_trim_booking_report16';
						}
						if($reportIds[0]==72){
							$report_action='show_trim_booking_report6';
						}
					$report_arr[$report_val[csf('template_name')]][219]=$report_action;


				}
				  
				  
				foreach ($main_data_arr as $job_id => $job_data) {

					 $approvalId = 0;
					 $cbo_level = 2;
					 $reportType=$report_arr[$company_name][122];
					 $costing_date=change_date_format($job_data['costing_date'], "yyyy-mm-dd", "-");
					
					if($sl%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";					
					//if($i==1){ ?>
					<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $sl; ?>">
					<td rowspan="<?= $rowspan[$job_id]; ?>" width="30" align="center"><?= $sl; ?></td>
					<td rowspan="<?= $rowspan[$job_id]; ?>" width="80"  align="left"><?= $buyer_short_name_library[$job_data['buyer_name']]; ?></td>

					<td rowspan="<?= $rowspan[$job_id]; ?>"  width="80" align="center"><a href="#report_details" onClick="job_report_generate('<?=$company_name; ?>','<?=$job_data['job_no']; ?>','<?=$job_data['buyer_name']; ?>','<?=$job_data['style_ref_no']; ?>','<?=$job_data['costing_per']; ?>','','<?=$costing_date; ?>','<?=$reportType; ?>','425')"><?=$job_data['job_no']?></a></td>


					<td rowspan="<?= $rowspan[$job_id]; ?>" align="left"><?= $lib_season_arr[$job_data['season_buyer_wise']]; ?></td>
					<td rowspan="<?= $rowspan[$job_id]; ?>"><p>
					<a href='#report_details' onClick="order_summery_popup('<? echo $job_id; ?>' ,'order_summery_data');"><?= $job_data['style_ref_no']; ?></a></p></td>
					<td rowspan="<?= $rowspan[$job_id]; ?>"  align="right"><p>
					<a href='#report_details' onClick="order_summery_popup('<? echo $job_id; ?>' ,'order_qty_data');"><?= $job_data['job_quantity']; ?></a></p></td>
					<td rowspan="<?= $rowspan[$job_id]; ?>"><?= $unit_of_measurement[$job_data['order_uom']]; ?></td>
					<td rowspan="<?= $rowspan[$job_id]; ?>"><?= $max_ship_arr[$job_id];  ?></td>
					<?	
					$i=1;			
					foreach ($job_data['color_data'] as $lib_yarn_id=>$color_data) {
						// if($i!=1) echo '<tr>';
						if($i!=1) echo '<tr onclick="change_color(\'tr_'.$i.'\',\''.$bgcolor.'\')" id="tr_'.$i.'">';
							
							 ?>
							<td rowspan="<?= count($color_data['fabric_color']) ?>"><?= $color_data['item_des'] ?></td>
						<?						
						$k=1;
						foreach ($color_data['fabric_color'] as $fcolor_id=>$fcolor_data) {
							// if($k!=1) echo '<tr>';
							if($k!=1) echo '<tr onclick="change_color(\'tr_'.$i.'\',\''.$bgcolor.'\')" id="tr_'.$i.'">';

							$gmts_color=array();
							$gmts_color_id=array();
							$trims_inhouse_qty_chk=array();
							$bom_qty=0;$bom_qty_finish=0;	$wo_qty=0; $bom_value=0; $wo_amount=0; $pi_amount=0;$issueqty=$inhouseqty=0;
							foreach ($fcolor_data as $gcolor_id => $row) {
								$fabric_color_id=$row['color'];
								$po_ids=rtrim($row['po_id'],',');
								$po_idsArr=array_unique(explode(",",$po_ids));
								$issuepo=implode(",",array_unique(explode(",",$po_ids)));
								$color_id = $gcolor_id;
								$gmts_color[$gcolor_id]=$color_arr[$gcolor_id];
								$gmts_color_id[$gcolor_id]=$gcolor_id;
								$wo_qty += $wo_data_arr[$job_id][$lib_yarn_id][$color_id][$fabric_color_id]['fin_fab_qnty'];
								$receive_qty=$receive_ret_qty=0;$issue_qty=$issue_ret_qty=0;
								foreach($po_idsArr as $pid)
								{
									//$issueqty+= $issue_qty_arr[$job_id][$pid][$lib_yarn_id][$fabric_color_id]['qty']-$issue_rtnQty_arr_wvn[$job_id][$pid][$lib_yarn_id][$fabric_color_id]['qty'];
									$receive_qty+=$receive_qty_arr[$job_id][$pid][$lib_yarn_id][$fabric_color_id]['qty'];
									$receive_ret_qty+=$receive_rtn_qty_arr[$job_id][$pid][$lib_yarn_id][$fabric_color_id]['qty'];
									//$issuepo= $issue_qty_arr[$job_id][$pid][$lib_yarn_id][$fabric_color_id]['po_id'];
									$supplier= $issue_qty_arr[$job_id][$pid][$lib_yarn_id][$fabric_color_id]['supplier'];
									$receive_qty+=$receive_qty_arr[$job_id][$pid][$lib_yarn_id][$fabric_color_id]['qty'];
									$receive_ret_qty+=$receive_rtn_qty_arr[$job_id][$pid][$lib_yarn_id][$fabric_color_id]['qty'];
								}
								$issueqty=$issue_qty-$issue_ret_qty;
								
								$bom_value+= $fabric_amount_arr['woven']['grey'][$job_id][$lib_yarn_id][$color_id][$fabric_color_id];
								
								//echo $job_id.'='.$lib_yarn_id.'='.$color_id.'='.$fabric_color_id.'<br/>';
								$bom_qty_finish +=$fabric_qty_arr['knit']['grey'][$job_id][$lib_yarn_id][$color_id][$fabric_color_id];							
								$bom_qty +=$fabric_qty_arr['woven']['grey'][$job_id][$lib_yarn_id][$color_id][$fabric_color_id];												

								if($trims_inhouse_qty_chk[$job_id.'*'.$lib_yarn_id.'*'.$fabric_color_id]==''){
									$trims_inhouse_qty_chk[$job_id.'*'.$lib_yarn_id.'*'.$fabric_color_id]=$job_id.'*'.$pid.'*'.$lib_yarn_id.'*'.$fabric_color_id;
									//$inhouseqty+= $receive_qty_arr[$job_id][$lib_yarn_id][$fabric_color_id]['qty']-$receive_rtn_qty_arr[$job_id][$pid][$lib_yarn_id][$fabric_color_id]['qty'];
								}
								$inhouseqty= $receive_qty-$receive_ret_qty;
								
								$inhousepo=$receive_qty_arr[$job_id][$lib_yarn_id][$fabric_color_id]['po_id'];
								//echo $inhouseqty.'Dx';
								$rcv_balance=$wo_qty-$inhouseqty;
								
								$rcv_rate=$receive_qty_arr[$job_id][$lib_yarn_id][$fabric_color_id]['rate'];
							}	
							$issue_balance=$inhouseqty-$issueqty;
													
							$pi_no=rtrim($pi_data_arr[$job_id][$lib_yarn_id][$fabric_color_id]['pi_no'],',');
							$pi_nos=implode(", ",array_unique(explode(",",$pi_no)));
							$blc_no=rtrim($pi_data_arr[$job_id][$lib_yarn_id][$fabric_color_id]['blc_no'],',');
							$blc_nos=implode(", ",array_unique(explode(",",$blc_no)));

							
						 	?>
							<td align="left"><?= $color_arr[$fcolor_id] ?></td>
							<td align="left"><?= implode(", ", $gmts_color) ?></td>
							<td align="left"><?= implode(",",$wo_data_arr[$job_id][$lib_yarn_id][$color_id][$fabric_color_id]['wo_date'] )  ?></td>
							<td align="right"><?= fn_number_format($row['avg_cons'],4) ?></td>
							<td align="right" title="Avg Rate<?=$bom_value;?>"><?=$row['sourcing_rate'] ;//fn_number_format($bom_value/$bom_qty,4) ?></td>
							
							<td align="right"><a href='#report_details' onClick="order_req_qty_popup('<? echo $job_id; ?>','<? echo $lib_yarn_id; ?>','<? echo $fabric_color_id; ?>' ,'order_req_qty_data');">

								<? 
								if($row['fab_nature_id']==2){echo fn_number_format($bom_qty_finish,2);}else{echo  fn_number_format($bom_qty,2);}
								 ?>
									
								</a></td>

							<td align="right"><a href='#report_details' onClick="order_wo_qty_popup('<? echo $job_id; ?>','<? echo $lib_yarn_id; ?>','<? echo $fabric_color_id; ?>','<? echo implode(",", $gmts_color_id) ?>' ,'order_wo_qty_data');"><?= fn_number_format($wo_qty,2)  ?></a></td>

							<?php
							$booking_number=implode(",",$wo_data_arr[$job_id][$lib_yarn_id][$color_id][$fabric_color_id]['booking_no']);
							$is_approved=implode(",",$wo_data_arr[$job_id][$lib_yarn_id][$color_id][$fabric_color_id]['is_approved']);
							$fabric_source=implode(",",$wo_data_arr[$job_id][$lib_yarn_id][$color_id][$fabric_color_id]['fabric_source']);
							$fabric_nature=implode(",",$wo_data_arr[$job_id][$lib_yarn_id][$color_id][$fabric_color_id]['fabric_nature']);
							?>
							<td align="center">
							<?
							$wvnType=$report_arr[$company_name][138];
							foreach($wo_data_array[$job_id][$lib_yarn_id][$color_id][$fabric_color_id] as $fabric=>$val){						
								?>
								<a href="#report_details" onClick="booking_report_generate('<?=$company_name; ?>','<?=$fabric; ?>','<?=$fabric_nature; ?>','<?=$fabric_source; ?>','<?=$is_approved; ?>','<?=$po_ids; ?>','<?=$wvnType; ?>','271')"><?= $fabric,"  ,";?></a>
								<?}
							
							?>
							</td>

							<td align="left"><?= $unit_of_measurement[$row['fabric_uom']] ?></td>
							
							<td align="right"><a href='#report_details' onClick="openmypage_inhouse_btn3('<? echo $job_id; ?>','<? echo $lib_yarn_id; ?>','<? echo $inhousepo; ?>','<? echo $fabric_color_id ?>' ,'booking_inhouse_info_btn3');"><?= fn_number_format($inhouseqty,2) ?></a></td>

							<td align="right"><?= fn_number_format($rcv_balance,2)  ?></td>
							<td align="right"><? if($issueqty>0){ ?><a href='#report_details' onClick="openmypage_issue_btn3('<? echo $job_id; ?>','<? echo $lib_yarn_id; ?>','<? echo $issuepo; ?>','<? echo $fabric_color_id?>', '<?echo $rcv_rate?>' ,'booking_issue_info_btn3');"><?= fn_number_format($issueqty,2)  ?></a><? } else echo '0.00'; ?></td>
							<td align="right"><?= fn_number_format($issue_balance,2) ?></td>
							<td align="left"><?= implode(",",$wo_data_arr[$job_id][$lib_yarn_id][$color_id][$fabric_color_id]['supplier']); ?></td>
							<td align="left"><p> &nbsp; <?=$pi_nos; ?></p></td>
							<td align="left"><p>&nbsp;<?=$fab_pi_lc_data_arr[$pi_nos]['lc_number'];//$blc_nos; ?></p></td>
							<?
							$k++;
							$i++;
						 }
						 
							
						} ?>
							</tr>
						<?
						$sl++;
						}
				?>
				
			</tbody>
		</table>
        <?
        }
        if(count($trims_sql_data) > 1) {
        ?>
		<table class="rpt_table" width="2580" cellpadding="0" cellspacing="0" border="1" rules="all" style="margin-top: 10px">
			<thead>
				<tr>
					<td colspan="24" align="left"><strong>Trims Details</strong></td>
				</tr>
				<tr>
					<th width="35">SL</th>
					<th width="95">Buyer</th>
					<th width="95">Job No</th>
					<th width="95">Season</th>
	                <th width="95">Style Ref</th>
					<th width="60">Order Qnty</th>
					<th width="50">UOM</th>
					<th width="60">Max.Ship Date</th>
					<th width="100">Trims Name</th>
					<th width="150">Item Description</th>
					<th width="100">Brand/Sup Ref</th>
					<th width="80">WO Date</th>
					<th width="80">Avg. Cons</th>
					<th width="80">Rate</th>
					<th width="100">Req Qnty</th>
					<th width="90">WO Qty</th>
					<th width="90">WO NO.</th>
	                <th width="50">UOM</th>
	                <th width="80">In-House Qty</th>
	                <th width="80">Receive Balance</th>
	                <th width="70">Issue to Prod.</th>
					<th width="90">Issue Balance</th>
	                <th width="90">Supplier</th>
					<th width="120">PI No.</th>
					<th width="90">BTB LC N0</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$tsl=1;
				?>
				<?php foreach ($trims_main_data as $job_id=>$value){
					$reportType=$report_arr[$company_name][122];
					$rowspan= count($value['trims_data']);
					if($tsl%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				 ?>
					<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr2_<? echo $tsl; ?>','<? echo $bgcolor;?>')" id="tr2_<? echo $tsl; ?>">
						<td width="35" rowspan="<?= $rowspan ?>"><?= $tsl; ?></td>
						<td width="95" rowspan="<?= $rowspan ?>"align="left"><?= $buyer_short_name_library[$value['buyer_name']]; ?></td>
						<td rowspan="<?= $rowspan ?>"  width="95" align="center"><a href="#report_details" onClick="job_report_generate('<?=$company_name; ?>','<?= $value['job_no']; ?>','<?=$value['buyer_name']; ?>','<?=$value['style_ref_no']; ?>','<?=$value['costing_per']; ?>','','<?=$value['costing_date']; ?>','<?=$reportType; ?>','425')"><?= $value['job_no']?></a></td>

						<td width="95" rowspan="<?= $rowspan ?>"align="left"><?= $lib_season_arr[$value['season_buyer_wise']] ?></td>
		                <td width="95" rowspan="<?= $rowspan ?>" align="left">
					<a href='#report_details' onClick="order_summery_popup('<? echo $job_id; ?>' ,'order_summery_data');"><?= $value['style_ref_no']; ?></a></td>
						<td width="60"  align="right" rowspan="<?= $rowspan ?>"><a href='#report_details' onClick="order_summery_popup('<? echo $job_id; ?>' ,'order_qty_data');"><?= $value['job_quantity']; ?></a></td>
						<td width="50" rowspan="<?= $rowspan ?>" align="left"><?= $unit_of_measurement[$value['order_uom']]; ?></th>
						<td width="60" rowspan="<?= $rowspan ?>" align="left"><?= $max_ship_arr[$job_id];  ?></td>
						<?

						$z=1;
						foreach ($value['trims_data'] as $trims_id=>$row) {
							// if($z!=1) echo '<tr>';
							
							if($z!=1) echo '<tr onclick="change_color(\'trs_'.$z.'\',\''.$bgcolor.'\')" id="trs_'.$z.'">';
							
							$req_qty = $trim_group_qty_arr[$value['job_no']][$trims_id];
							$wo_qty= $trim_wo_data_arr[$job_id][$trims_id]['wo_qnty'];
							
							$description= rtrim($trim_wo_data_arr[$job_id][$trims_id]['description'],'**');
							$descriptions=implode("__",array_unique(explode("**",$description)));
							$descriptionsArr=array_unique(explode("**",$description));
							
							$inhouse_qty=0;$tot_issue_qty=0;$receive_rtnQty=0;$issue_rtnQty=0;
							foreach($descriptionsArr as $desc) 
							{
								$inhouse_qty+=$trim_inhouse_qty[$job_id][$row['trim_group']][$desc]['qty'];
								$tot_issue_qty += $trim_issue_qty[$job_id][$row['trim_group']][$desc]['qty'];
								$po_id= $trim_inhouse_qty[$job_id][$row['trim_group']][$desc]['po_id'];
								$tpo_id= $trim_issue_qty[$job_id][$row['trim_group']][$desc]['po_id'];
								$receive_rtnQty+=$receive_rtnArr[$job_id][$row['trim_group']][$desc]['qty'];
								$issue_rtnQty+=$trim_issue_rtn_qty[$job_id][$row['trim_group']][$desc]['qty'];
							}
							//echo $receive_rtnQty.'D';
							 $issue_qty=$tot_issue_qty-$issue_rtnQty;
							 
						//	if($row['description']!='') $desc=$row['description'];else $desc=0;
							// $inhouse_qty= $trim_inhouse_qty[$job_id][$row['trim_group']][$desc]['qty'];
							// $issue_qty= $trim_issue_qty[$job_id][$row['trim_group']][$desc]['qty'];
							
						
							$trims_poid=implode(",",$trims_po_id_arr[$job_id][$row['trim_group']]);
							//echo $row['description'].'D'.$trims_poid.',';
							$trim_pi_no=rtrim($trim_pi_data_arr[$trims_id][$job_id][$row['trim_group']]['pi_no'],',');
							$trim_pi_nos=implode(", ",array_unique(explode(",",$trim_pi_no)));
							
							$trim_blc_no=rtrim($trim_pi_data_arr[$trims_id][$job_id][$row['trim_group']]['blc_no'],',');
							$trim_blc_nos=implode(", ",array_unique(explode(",",$trim_blc_no)));

							//  print_r($trim_wo_data_arr2[$job_id][$trims_id][$row['trim_group']][$row['description']]);
							//$inhouse_qty=0;$issue_qty=0;
							foreach($trim_wo_data_arr2[$job_id][$row['id']][$trims_id][$row['trim_group']] as $bdesc){
								//$inhouse_qty+=$trim_inhouse_qty[$job_id][$row['trim_group']][$bdesc]['qty'];
								//$issue_qty += $trim_issue_qty[$job_id][$row['trim_group']][$bdesc]['qty'];
								//$bDesc=$bdesc;
							}
							$inhouse_qty=$inhouse_qty-$receive_rtnQty;
							$trimrcv_balance = $wo_qty-$inhouse_qty;
							
						?>
						<td width="100" align="left"><?= $item_library[$row['trim_group']]; ?></td>
						<td width="150" align="left"><?= $descriptions; ?></td>
						<td width="100" align="left"><?= $row['brand_sup_ref']; ?></td>
						<td width="80" align="left"><?= implode(",", $trim_wo_data_arr[$job_id][$trims_id]['wo_date'])?></td>
						<td width="80" align="right"><?= fn_number_format($row['cons_dzn_gmts'],4); ?></td>
						<td width="80" align="right"><?= fn_number_format($row['rate'],4); ?></td>
						<td width="100" align="right"><a href='#report_details' onClick="trim_req_qty_popup('<? echo $job_id; ?>','<? echo $trims_id; ?>' ,'trim_req_qty_data');"><?= fn_number_format($req_qty,2); ?></a></td>
						<td width="90" align="right"><a href='#report_details' onClick="trim_wo_qty_popup('<? echo $job_id; ?>','<? echo $trims_id; ?>' ,'trim_wo_qty_data');"><?= fn_number_format($wo_qty,2) ?></a></td>

						<?php
							/* $booking_number=implode(",", $trim_wo_data_arr[$job_id][$trims_id]['booking_no']);
							$is_approved=implode(",", $trim_wo_data_arr[$job_id][$trims_id]['is_approved']);
							$cbo_level=implode(",", $trim_wo_data_arr[$job_id][$trims_id]['cbo_level']);
							$buyer_name=implode(",", $trim_wo_data_arr[$job_id][$trims_id]['buyer_id']);
							$trbType=$report_arr[$company_name][219];
							?>						
							<td align="center" width="50"><a href="#report_details" onClick="booking_report_generate('<?=$company_name; ?>','<?=$booking_number; ?>','<?=$buyer_name; ?>','<?=$cbo_level; ?>','<?=$is_approved; ?>','','<?=$trbType; ?>','272')"><?=$booking_number;?></td> */
							$booking_number=implode(",", $trim_wo_data_arr[$job_id][$trims_id]['booking_no']);
							$is_approved=implode(",", $trim_wo_data_arr[$job_id][$trims_id]['is_approved']);
							$cbo_level=implode(",", $trim_wo_data_arr[$job_id][$trims_id]['cbo_level']);
							$buyer_name=implode(",", $trim_wo_data_arr[$job_id][$trims_id]['buyer_id']);
							?>						

							<td align="center" width="50">
							<?
							$trbType=$report_arr[$company_name][219];
							foreach($trim_wo_data_array[$job_id][$trims_id] as $trims=>$val){						
								?>
								<a href="#report_details" onClick="booking_report_generate('<?=$company_name; ?>','<?=$trims; ?>','<?=$buyer_name; ?>','<?=$cbo_level; ?>','<?=$is_approved; ?>','','<?=$trbType; ?>','272')"><?=$trims,"  ,";?></a>
								<?}
							
							?> 
		                <td width="50" align="right"><?= $unit_of_measurement[$row['cons_uom']]; ?></td>	                
		                <td width="80" align="right" title="Recv Return=<? echo $receive_rtnQty.', =Des'.$descriptions;?>"><a href='#report_details' onClick="openmypage_trim_inhouse('<? echo $trims_poid;  ?>',<?= $row['trim_group']?>,'trim_booking_inhouse_info2');"><?= fn_number_format($inhouse_qty,2);  ?></a></td>
		                <td width="80" align="right"><?= fn_number_format($trimrcv_balance,2); ?></td>
		                <td width="70" align="right"><a href='#report_details' onClick="openmypage_trim_issue('<? echo $trims_poid; ?>','<? echo $row['trim_group'].'__'.$descriptions; ?>','trim_booking_issue_info');"><?= fn_number_format($issue_qty,2);  ?></a></td>
						<td width="90" align="right"><?= fn_number_format($inhouse_qty-$issue_qty,2); ?></td>
		                <td width="90" align="left"><?=implode(",", $trim_wo_data_arr[$job_id][$trims_id]['supplier'])?></td>
						<td width="120" align="left"><p> &nbsp; <?=$trim_pi_nos;?></p></td>
						<td width="90" title="<?= $trims_id.'='.$job_id.'='.$row['trim_group'];?>" align="left"><p>&nbsp;<?=$trim_pi_lc_data_arr[$trim_pi_nos]['lc_number'];//$trim_blc_nos;?></p></td>
						<? $z++;
						 } ?>
					</tr>
				<?php 
					$tsl++;
					} 
				?>								
			</tbody>
		</table>
        <?
        }
        ?>
		<div style="width:2660px; max-height:400px; overflow-y:scroll" id="scroll_body">
		</div>
	</div>
	<?
		$total_data = ob_get_contents();
		ob_clean();
		foreach (glob("*.xls") as $filename) {
		@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename=$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc,$total_data);
		echo "$total_data****$filename";
		exit();
}

if($action=="report_generate4")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
	$country_name_library=return_library_array( "select id, country_name from lib_country", "id", "country_name"  );

	$lib_supplier_arr=return_library_array( "select id, short_name from lib_supplier", "id", "short_name"  );
	$lib_season_arr=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name"  );
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");

	$company_name=str_replace("'","",$cbo_company_name);
	$buyer_id_cond="";
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
				$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
	}

	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_job_no=trim($txt_job_no);

	if($txt_job_no!="" || $txt_job_no!=0) $jobcond="and a.job_no_prefix_num in($txt_job_no)"; else $jobcond="";
	if(str_replace("'","",$txt_style_ref)!="") $style_ref_cond=" and a.style_ref_no like '%".str_replace("'","",$txt_style_ref)."%'"; else $style_ref_cond="";


	$date_type=str_replace("'","",$cbo_date_type);

	$date_cond='';
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		$start_date=(str_replace("'","",$txt_date_from));
		$end_date=(str_replace("'","",$txt_date_to));
		$date_cond="and b.pub_shipment_date between '$start_date' and '$end_date'";
	}

	$cbo_year=str_replace("'","",$cbo_year);
	if($db_type==0)
	{
		if(trim($cbo_year)!=0) $year_cond=" and YEAR(a.insert_date)=$cbo_year"; else $year_cond="";
	}
	else if($db_type==2)
	{
		if(trim($cbo_year)!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year"; else $year_cond="";
	}

	$main_data_sql=sql_select("SELECT a.id, a.job_no, a.buyer_name, a.job_no_prefix_num, a.season_buyer_wise, a.style_ref_no, a.job_quantity, a.order_uom, a.company_name,  b.id as po_id, b.po_number,  c.color_number_id, c.order_quantity, e.id as fabric_cost_id, e.lib_yarn_count_deter_id, e.body_part_id,e.fabric_description, e.uom as fabric_uom,e.gsm_weight,e.gsm_weight_type,f.item_size as cutable_width, avg(f.requirment) as avg_cons, e.color_size_sensitive, e.color, e.color_break_down, g.contrast_color_id, h.stripe_color,e.rate from wo_po_details_master a join wo_po_break_down b on a.id=b.job_id join wo_po_color_size_breakdown c on a.id=c.job_id and b.id=c.po_break_down_id join wo_pre_cost_mst d on a.id=d.job_id join wo_pre_cost_fabric_cost_dtls e on a.id=e.job_id join wo_pre_cos_fab_co_avg_con_dtls f on e.id=f.pre_cost_fabric_cost_dtls_id left join wo_pre_cos_fab_co_color_dtls g on a.id=g.job_id and e.id=g.pre_cost_fabric_cost_dtls_id and c.color_number_id =g.gmts_color_id and g.status_active=1 and g.is_deleted=0 left join wo_pre_stripe_color h on a.id=h.job_id and  c.item_number_id= h.item_number_id and e.id=h.pre_cost_fabric_cost_dtls_id and f.color_number_id =h.color_number_id and f.po_break_down_id=h.po_break_down_id and f.gmts_sizes=h.size_number_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.company_name=$company_name $year_cond $date_cond $style_ref_cond $jobcond $buyer_id_cond group by a.id, a.job_no, a.buyer_name, a.job_no_prefix_num, a.season_buyer_wise, a.style_ref_no, a.job_quantity, a.order_uom, b.id,b.po_number, c.color_number_id, c.order_quantity, e.lib_yarn_count_deter_id, e.fabric_description, e.uom, e.id, e.color_size_sensitive, e.color, e.body_part_id, e.color_break_down,g.contrast_color_id, h.stripe_color, a.company_name,e.gsm_weight,e.gsm_weight_type,e.rate,f.item_size  order by e.id asc");

    if(count($main_data_sql) > 0) {
        $main_attribute = array('job_no', 'buyer_name', 'job_no_prefix_num', 'season_buyer_wise', 'style_ref_no', 'job_quantity', 'order_uom', 'company_name');
        foreach ($main_data_sql as $row) {
            foreach ($main_attribute as $attr) {
                $main_data_arr[$row[csf('id')]][$attr] = $row[csf($attr)];
            }
            $fabricColorId = $row[csf('stripe_color')];
            if (!$fabricColorId) {
                $fabricColorId = $row[csf('contrast_color_id')];
            }
            if (!$fabricColorId) {
                $fabricColorId = $row[csf('color_number_id')];
            }
			 $po_idArr[$row[csf('po_id')]] = $row[csf('id')];
			 
            $main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['item_des'] = $body_part[$row[csf('body_part_id')]] . ',' . $row[csf('fabric_description')];
			$main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['gsm_weight'].= $row[csf('gsm_weight')].",";
			$main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['cutable_width'].= $row[csf('cutable_width')].",";
			$main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['gsm_weight_type'].= $fabric_weight_type[$row[csf('gsm_weight_type')]].",";
            $main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['color_id'] = $row[csf('color_number_id')];
            $main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['po_number'] = $row[csf('po_number')];
			$main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['po_id'][$row[csf('po_id')]] = $row[csf('po_id')];
			$main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['po_ids'].= $row[csf('po_id')].",";
			$main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['gsm_weight'].= $row[csf('gsm_weight')].",";
			$main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['cutable_width'].= $row[csf('cutable_width')].",";
			$main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['gsm_weight_type'].= $fabric_weight_type[$row[csf('gsm_weight_type')]].",";
            $main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['fabric_uom'] = $row[csf('fabric_uom')];
            $main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['avg_cons'] = $row[csf('avg_cons')];
            $main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['sourcing_rate'] = $row[csf('rate')];
            $main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['sensitive'] = $row[csf('color_size_sensitive')];
            $main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['color'] = $fabricColorId;
            $job_id_arr[$row[csf('id')]] = $row[csf('id')];
            $fabric_id_arr[$row[csf('fabric_cost_id')]] = $row[csf('fabric_cost_id')];
            $po_id_arr[$row[csf('po_id')]] = $row[csf('po_id')];
        }
       /*  echo '<pre>';
        print_r($main_data_arr); die; */
        $po_id = array_chunk($po_id_arr, 1000, true);
        $order_cond = "";
        $po_cond_for_in2 = "";
        $ji = 0;
        foreach ($po_id as $key => $value) {
            if ($ji == 0) {
                $order_cond = " and c.po_breakdown_id  in(" . implode(",", $value) . ")";
                $po_cond_for_in2 = " and a.po_breakdown_id  in(" . implode(",", $value) . ")";
            } else {
                $order_cond .= " or c.po_breakdown_id  in(" . implode(",", $value) . ")";
                $po_cond_for_in2 .= " or a.po_breakdown_id  in(" . implode(",", $value) . ")";
            }
            $ji++;
        }
        $job_id_chunk = array_chunk($job_id_arr, 1000, true);
        $jobid_cond = "";
        $jobid_cond1 = "";
        $jobid_cond3 = "";
        $i = 0;
        foreach ($job_id_chunk as $key => $value) {
            if ($i == 0) {
                $jobid_cond = " and b.job_id  in(" . implode(",", $value) . ")";
                $jobid_cond1 = " and a.job_id  in(" . implode(",", $value) . ")";
                $jobid_cond3 = " and job_id  in(" . implode(",", $value) . ")";
                $jobid_cond4 = " and i.job_id  in(" . implode(",", $value) . ")";
            } else {
                $jobid_cond .= " or b.job_id  in(" . implode(",", $value) . ")";
                $jobid_cond1 .= " or a.job_id  in(" . implode(",", $value) . ")";
                $jobid_cond3 .= " or job_id  in(" . implode(",", $value) . ")";
                $jobid_cond4 .= " or i.job_id  in(" . implode(",", $value) . ")";
            }
            $i++;
        }

        $rowspan = array();
        foreach ($main_data_arr as $job_id => $jod_arr) {
            foreach ($jod_arr['color_data'] as $color_data) {
                foreach ($color_data['fabric_color'] as $row) {
                    $rowspan[$job_id]++;
                }
            }
        }
        $fabric_id_str = implode(",", $fabric_id_arr);

        $wo_data_sql = sql_select("SELECT a.job_id, a.lib_yarn_count_deter_id, b.id, b.booking_date,b.booking_no, c.gmts_color_id, c.fin_fab_qnty, c.amount,	b.supplier_id, c.fabric_color_id FROM wo_pre_cost_fabric_cost_dtls a , wo_booking_dtls c ,wo_booking_mst b where  a.id=c.pre_cost_fabric_cost_dtls_id and  b.booking_no=c.booking_no  and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id in ($fabric_id_str) and c.fin_fab_qnty is not null ");
        foreach ($wo_data_sql as $row) {
            $wo_data_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]]['fin_fab_qnty'] += $row[csf('fin_fab_qnty')];
            $wo_data_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]]['amount'] += $row[csf('amount')];
            $wo_data_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]]['wo_date'][$row[csf('id')]] = $row[csf('booking_date')];
            $wo_data_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]]['booking_no'].= $row[csf('booking_no')].',';
            $wo_data_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]]['supplier'].= $lib_supplier_arr[$row[csf('supplier_id')]].',';
        }
 		unset($wo_data_sql);
        // $receive_qty_data=sql_select("SELECT d.detarmination_id, d.color, b.order_qnty, b.order_amount, e.job_id, e.id as po_id, b.order_rate from inv_receive_master a join inv_transaction b on a.id = b.mst_id join order_wise_pro_details c on b.id = c.trans_id join product_details_master d on d.id=c.prod_id join wo_po_break_down e on e.id=c.po_breakdown_id where a.entry_form=17 and a.receive_basis=1 and a.status_active=1 and a.is_deleted=0 and b.receive_basis=1 and b.transaction_type=1 and b.status_active=1 and b.is_deleted=0 and c.trans_type=1 and c.entry_form=17 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $order_cond");
		
		/*$receive_qty_data=sql_select("SELECT a.recv_number, d.id as prod_id, a.receive_date, a.challan_no, a.supplier_id, b.body_part_id, a.inserted_by, d.detarmination_id, d.color, d.product_name_details,c.quantity as order_qnty,b.order_rate, b.order_amount,b.cons_uom, a.booking_no 	from inv_receive_master a join inv_transaction b on a.id = b.mst_id join order_wise_pro_details c on b.id = c.trans_id join product_details_master d on d.id=c.prod_id where a.entry_form=17 and a.receive_basis=1 and a.status_active=1 and a.is_deleted=0 and b.receive_basis=1  and b.transaction_type=1 and b.status_active=1 and b.is_deleted=0 and c.trans_type=1 and c.entry_form=17 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.po_breakdown_id in(".implode(",",$poIdArr).") and d.detarmination_id=$yarn_id and d.color=$color");*/

        $receive_qty_data = sql_select("SELECT c.po_breakdown_id as po_id,d.detarmination_id, d.color, c.quantity as order_qnty, b.order_amount,   b.order_rate	from inv_receive_master a , inv_transaction b , order_wise_pro_details c , product_details_master d   where  a.id = b.mst_id and  b.id = c.trans_id  and  d.id=c.prod_id  and  d.id=b.prod_id  and a.entry_form=17 and a.receive_basis in (1,2) and a.status_active=1 and a.is_deleted=0 and b.receive_basis in (1,2) and b.transaction_type=1 and b.status_active=1 and b.is_deleted=0 and c.trans_type=1 and c.entry_form=17 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $order_cond");
		
		
        $receive_qty_arr = array();$tot_aty=0;
        foreach ($receive_qty_data as $row) {
			$job_id=$po_idArr[$row[csf('po_id')]];
            $receive_qty_arr[$job_id][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['qty'] += $row[csf('order_qnty')];
            $receive_qty_arr[$job_id][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['amount'] += $row[csf('order_amount')];
            $receive_qty_arr[$job_id][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['rate'] = $row[csf('order_rate')];
            $receive_qty_arr[$job_id][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['po_id'] = $row[csf('po_id')];
			$tot_aty+=$row[csf('order_qnty')];
        }
        //echo $tot_aty.'d';
		unset($receive_qty_data);

		$issue_rtn_qty_data = sql_select(" SELECT c.po_breakdown_id as po_id,d.detarmination_id, d.color, c.quantity as order_qnty, b.order_amount, b.order_rate
		from inv_receive_master a , inv_transaction b , order_wise_pro_details c , product_details_master d 
		where a.id = b.mst_id and b.id = c.trans_id and d.id=c.prod_id and d.id=b.prod_id and a.entry_form=209
		and a.status_active=1 and a.is_deleted=0 
		and b.transaction_type=4 and b.status_active=1 and b.is_deleted=0
		and c.trans_type=4 and c.entry_form=209 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $order_cond ");
		$issue_rtn_qty_arr = array();
        foreach ($issue_rtn_qty_data as $row) {
			$job_id=$po_idArr[$row[csf('po_id')]];
            $issue_rtn_qty_arr[$job_id][$row[csf('detarmination_id')]][$row[csf('color')]]['qty'] += $row[csf('order_qnty')];
            $issue_rtn_qty_arr[$job_id][$row[csf('detarmination_id')]][$row[csf('color')]]['po_id'] = $row[csf('po_id')];
        }
        unset($issue_rtn_qty_data);


		
		
        $issue_qty_data = sql_select("SELECT d.detarmination_id, d.color, b.cons_quantity, b.cons_amount, e.job_id, e.id as po_id,d.supplier_id from inv_issue_master a join inv_transaction b on a.id = b.mst_id join order_wise_pro_details c on b.id = c.trans_id join product_details_master d on d.id=c.prod_id join wo_po_break_down e on e.id=c.po_breakdown_id where a.entry_form=19 and a.issue_basis=1 and a.status_active=1 and a.is_deleted=0 and b.transaction_type=2 and b.status_active=1 and b.is_deleted=0 and c.trans_type=2 and c.entry_form=19 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $order_cond");

        $issue_qty_arr = array();$issue_po_arr = array();$issue_po_chk_arr = array();
        foreach ($issue_qty_data as $row) {
            $rate = $receive_qty_arr[$row[csf('job_id')]][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['rate'];
            $issue_qty_arr[$row[csf('job_id')]][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['qty'] += $row[csf('cons_quantity')];
            $issue_qty_arr[$row[csf('job_id')]][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['amount'] += $row[csf('cons_quantity')] * $rate;
            if($issue_po_chk_arr[$row[csf('job_id')]][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]!=$row[csf('po_id')])
            {
            	$issue_po_arr[$row[csf('job_id')]][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['po_id']= $row[csf('po_id')];
            	$issue_po_chk_arr[$row[csf('job_id')]][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]=$row[csf('po_id')];
            }
            $issue_qty_arr[$row[csf('job_id')]][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['supplier'] = $lib_supplier_arr[$row[csf('supplier_id')]];
        }
		unset($issue_qty_data);


		 $recv_rtn_qty_data = sql_select("SELECT d.detarmination_id, d.color, b.cons_quantity, b.cons_amount, e.job_id, e.id as po_id,d.supplier_id from inv_issue_master a join inv_transaction b on a.id = b.mst_id join order_wise_pro_details c on b.id = c.trans_id join product_details_master d on d.id=c.prod_id join wo_po_break_down e on e.id=c.po_breakdown_id where a.entry_form=202  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  and c.entry_form=202 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $order_cond");

        $recv_rtn_qty_arr = array();
        foreach ($recv_rtn_qty_data as $row) {
        	$job_id=$po_idArr[$row[csf('po_id')]];
            $recv_rtn_qty_arr[$row[csf('job_id')]][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['qty'] += $row[csf('cons_quantity')];
            $recv_rtn_qty_arrNew[$row[csf('job_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['po_id'].= $row[csf('po_id')].",";
        }
		unset($recv_rtn_qty_data);

		$trans_in_qty_data = sql_select("SELECT d.detarmination_id, d.color, b.cons_quantity, b.cons_amount, e.job_id, e.id as po_id from inv_item_transfer_mst a join inv_transaction b on a.id = b.mst_id join order_wise_pro_details c on b.id = c.trans_id join product_details_master d on d.id=c.prod_id join wo_po_break_down e on e.id=c.po_breakdown_id where a.entry_form=258  and a.status_active=1 and a.is_deleted=0 and b.transaction_type=5  and b.status_active=1 and b.is_deleted=0 and c.trans_type=5 and c.entry_form=258 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $order_cond");

        $transIn_qty_arr = array();
        foreach ($trans_in_qty_data as $row) {
            $transIn_qty_arr[$row[csf('job_id')]][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['qty'] += $row[csf('cons_quantity')];
            $transIn_qty_arr[$row[csf('job_id')]][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['po_id'] = $row[csf('po_id')];
        }
		unset($trans_in_qty_data);


		$trans_out_qty_data = sql_select("SELECT d.detarmination_id, d.color, b.cons_quantity, b.cons_amount, e.job_id, e.id as po_id from inv_item_transfer_mst a join inv_transaction b on a.id = b.mst_id join order_wise_pro_details c on b.id = c.trans_id join product_details_master d on d.id=c.prod_id join wo_po_break_down e on e.id=c.po_breakdown_id where a.entry_form=258  and a.status_active=1 and a.is_deleted=0 and b.transaction_type=6  and b.status_active=1 and b.is_deleted=0 and c.trans_type=6 and c.entry_form=258 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $order_cond");

        $transOut_qty_arr = array();
        foreach ($trans_out_qty_data as $row) {
            $transOut_qty_arr[$row[csf('job_id')]][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['qty'] += $row[csf('cons_quantity')];
            $transOut_qty_arr[$row[csf('job_id')]][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['po_id'] = $row[csf('po_id')];
        }
		unset($trans_out_qty_data);




        $fabric_supplier_data = sql_select("SELECT a.job_id, a.supplier_id, b.lib_yarn_count_deter_id from wo_pre_cost_fabric_supplier a , wo_pre_cost_fabric_cost_dtls b where  a.JOB_ID=b.JOB_ID and b.id=a.fabric_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $jobid_cond");

        $fabric_supplier_arr = array();
        foreach ($fabric_supplier_data as $row) {
            $fabric_supplier_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('supplier_id')]] = $lib_supplier_arr[$row[csf('supplier_id')]];
        }
 		unset($fabric_supplier_data);
        $pi_number_data = sql_select("SELECT f.style_ref_no,d.pi_number,f.job_no,i.job_id,c.determination_id, c.amount, c.color_id as color_id from wo_booking_dtls b left join  wo_pre_cost_fabric_cost_dtls i on   b.job_no = i.job_no,com_pi_item_details c , com_pi_master_details d ,wo_po_details_master f	where b.booking_no=c.work_order_no   and b.job_no = f.job_no and d.id=c.pi_id and b.status_active=1 and b.is_deleted=0 	and c.status_active=1 and c.is_deleted=0 and d.pi_basis_id in (1,2) and d.status_active=1 and d.is_deleted=0 and d.importer_id in ($company_name)  $jobid_cond4 and d.entry_form=166	group by f.style_ref_no,c.determination_id,d.pi_number,f.job_no ,i.job_id, c.amount, c.color_id");

        $pi_data_arr = array();
        foreach ($pi_number_data as $key => $row) {
            $pi_data_arr[$row[csf('job_id')]][$row[csf('determination_id')]][$row[csf('color_id')]]['pi_no'].= $row[csf('pi_number')].',';
            $pi_data_arr[$row[csf('job_id')]][$row[csf('determination_id')]][$row[csf('color_id')]]['amount'] += $row[csf('amount')];
            //$pi_data_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('color_id')]]['blc_no']=$row[csf('lc_number')];
        }
 		unset($pi_number_data);
        $pi_number_data_lc = sql_select("SELECT f.style_ref_no,d.pi_number,g.lc_number,f.job_no,i.job_id, c.determination_id, c.amount, c.color_id as color_id from wo_booking_dtls b left join  wo_pre_cost_fabric_cost_dtls i on   b.job_no = i.job_no,com_pi_item_details c , com_pi_master_details d ,wo_po_details_master f,com_btb_lc_master_details g,com_btb_lc_pi h 	where b.booking_no=c.work_order_no and g.id=h.com_btb_lc_master_details_id and h.pi_id=d.id and b.job_no = f.job_no and d.id=c.pi_id and b.status_active=1 and b.is_deleted=0 	and c.status_active=1 and c.is_deleted=0 and g.item_basis_id in (1,2) and d.status_active=1 and d.is_deleted=0 and g.importer_id in ($company_name)  $jobid_cond4 and d.entry_form=167	group by f.style_ref_no,d.pi_number,g.lc_number,f.job_no ,i.job_id, c.determination_id, c.amount, c.color_id");
	

        foreach ($pi_number_data_lc as $key => $row) {

            $pi_data_arr[$row[csf('job_id')]][$row[csf('determination_id')]][$row[csf('color_id')]]['blc_no'].= $row[csf('lc_number')].',';
        }
        unset($pi_number_data_lc);


        /*echo '<pre>';
        print_r($pi_data_arr); die;*/
        $max_shipment_date_sql = sql_select("SELECT MAX(pub_shipment_date) as pub_shipment_date ,job_id from wo_po_break_down where  status_active=1 and is_deleted=0 $jobid_cond3 group by job_id");
        foreach ($max_shipment_date_sql as $row) {
            $max_ship_arr[$row[csf('job_id')]] = $row[csf('pub_shipment_date')];
        }

        $condition = new condition();
        if (count($job_id_arr) > 0) {
            $job_id_str = implode(",", $job_id_arr);
            $condition->jobid_in($job_id_str);
        }
        $condition->init();
        $fabric = new fabric($condition);
        $fabric_qty_arr = $fabric->getQtyArray_by_JobIdLibYarnCountDeterIdAndGmtscolor_knitAndwoven_greyAndfinish();
        //$fabric_amount_arr=$fabric->getAmountArray_by_JobIdLibYarnCountDeterIdAndGmtscolor_knitAndwoven_greyAndfinish();
        $fabric_amount_arr = $fabric->getAmountArr_by_JobIdYarnCountIdGmtsAndFabricColor_source();
    }
	/*Trims Data Start from Here*/

	if($db_type==0)
	{
		$trim_sql_qry="SELECT a.id as job_id, a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, a.season_buyer_wise, b.file_no, b.grouping, b.id, b.po_number, a.order_uom, a.job_quantity, a.total_set_qnty, b.pub_shipment_date, b.shiping_status, d.costing_per, e.id as trim_dtla_id, e.trim_group, e.remark, e.description, e.brand_sup_ref, e.cons_uom, e.cons_dzn_gmts, e.rate, e.amount, e.apvl_req, e.nominated_supp, e.insert_date,e.sourcing_rate from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c left join  wo_pre_cost_trim_co_cons_dtls f on c.job_no_mst=f.job_no and c.po_break_down_id=f.po_break_down_id and f.cons > 0 join wo_pre_cost_trim_cost_dtls e on  f.job_no=e.job_no and e.id=f.wo_pre_cost_trim_cost_dtls_id $item_group_cond join wo_pre_cost_mst d on e.job_no =d.job_no where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and e.is_deleted=0 and e.status_active=1 and a.company_name=$company_name $buyer_id_cond ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $date_cond $style_ref_cond $year_cond $jobcond group by a.id, a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, a.season_buyer_wise, b.file_no, b.grouping, b.id, b.po_number, a.order_uom, a.total_set_qnty, b.pub_shipment_date, b.shiping_status, d.costing_per, e.id, e.trim_group, e.remark, e.description, e.brand_sup_ref, e.cons_uom, e.cons_dzn_gmts, e.rate, e.amount, e.apvl_req, e.nominated_supp, e.insert_date, a.job_quantity,e.sourcing_rate order by e.id, e.trim_group"; 

	}
	else
	{

		$trim_sql_qry = "SELECT a.id as job_id,a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, a.season_buyer_wise, b.id, b.po_number, a.order_uom, a.job_quantity, a.total_set_qnty, b.pub_shipment_date, d.costing_per, e.id as trim_dtla_id, e.trim_group, e.remark, e.description, e.brand_sup_ref, e.cons_uom, e.cons_dzn_gmts, e.rate, e.amount, e.apvl_req, e.nominated_supp, e.insert_date,e.sourcing_rate from wo_po_details_master a join wo_po_break_down b on a.job_no=b.job_no_mst join wo_po_color_size_breakdown c on a.job_no=c.job_no_mst and b.id=c.po_break_down_id join wo_pre_cost_mst d on a.job_no =d.job_no join wo_pre_cost_trim_cost_dtls e on e.job_no = d.job_no $item_group_cond left join  wo_pre_cost_trim_co_cons_dtls f on c.job_no_mst=f.job_no and c.po_break_down_id=f.po_break_down_id and f.job_no=e.job_no and e.id=f.wo_pre_cost_trim_cost_dtls_id where f.cons > 0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and e.is_deleted=0 and e.status_active=1 and a.company_name=$company_name $buyer_id_cond ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $date_cond $style_ref_cond $year_cond $jobcond $jobid_cond group by a.id, a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, a.season_buyer_wise,  b.id, b.po_number, a.order_uom, a.total_set_qnty, b.pub_shipment_date,  d.costing_per, e.id, e.trim_group, e.remark, e.description, e.brand_sup_ref, e.cons_uom, e.cons_dzn_gmts, e.rate, e.amount, e.apvl_req, e.nominated_supp, e.insert_date, a.job_quantity,e.sourcing_rate order by e.id, e.trim_group";

	}
	
	//echo $trim_sql_qry; die;
	$trims_sql_data= sql_select($trim_sql_qry);
    if(count($trims_sql_data) > 1) {
        $trims_main_attribute = array('job_no', 'buyer_name', 'job_no_prefix_num', 'style_ref_no', 'season_buyer_wise', 'order_uom', 'job_quantity');
        $trims_dtls_attribute = array('trim_dtla_id', 'trim_group', 'description', 'brand_sup_ref', 'cons_uom', 'cons_dzn_gmts', 'po_number', 'sourcing_rate');
        foreach ($trims_sql_data as $row) {

            foreach ($trims_main_attribute as $attr) {
                $trims_main_data[$row[csf('job_id')]][$attr] = $row[csf($attr)];
            }
            foreach ($trims_dtls_attribute as $tattr) {
                $trims_main_data[$row[csf('job_id')]]['trims_data'][$row[csf('trim_dtla_id')]][$tattr] = $row[csf($tattr)];
            }
            $trimjob_id_arr[$row[csf('job_id')]] = $row[csf('job_id')];
            $trim_id_arr[$row[csf('trim_dtla_id')]] = $row[csf('trim_dtla_id')];
            $trim_poid_arr[$row[csf('id')]] = $row[csf('id')];

        }
        $trimjob_id_chunk = array_chunk($trimjob_id_arr, 1000, true);
        $jobid_cond1 = "";
        $jobid_cond = "";
        $i = 0;
        foreach ($trimjob_id_chunk as $key => $value) {
            if ($i == 0) {
                $jobid_cond = " and b.job_id  in(" . implode(",", $value) . ")";
                $jobid_cond1 = " and a.job_id  in(" . implode(",", $value) . ")";
            } else {
                $jobid_cond = " or b.job_id  in(" . implode(",", $value) . ")";
                $jobid_cond1 .= " or a.job_id  in(" . implode(",", $value) . ")";
            }
            $i++;
        }

        $trim_id_chunk = array_chunk($trim_id_arr, 1000, true);
        $trimid_cond = "";
        $ji = 0;
        foreach ($trim_id_chunk as $key => $value) {
            if ($ji == 0) {
                $trimid_cond = " and a.id  in(" . implode(",", $value) . ")";
            } else {
                $trimid_cond .= " or a.id  in(" . implode(",", $value) . ")";
            }
            $ji++;
        }
	 $trim_wo_data_sql = sql_select("SELECT a.id as trim_id, a.job_id, b.id as booking_id, b.booking_date, b.booking_no, d.requirment as wo_qnty,d.amount, d.description,
        b.supplier_id,c.id as booking_dtls_id FROM wo_pre_cost_trim_cost_dtls a , wo_booking_dtls c  , wo_booking_mst b,wo_trim_book_con_dtls d 
		 where a.id=c.pre_cost_fabric_cost_dtls_id and d.wo_trim_booking_dtls_id=c.id and d.po_break_down_id=c.po_break_down_id and d.booking_no=b.booking_no and  b.booking_no=c.booking_no and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.booking_type=2 $trimid_cond ");
		 
        foreach ($trim_wo_data_sql as $row) {
            $trim_wo_data_arr[$row[csf('job_id')]][$row[csf('trim_id')]]['wo_qnty'] += $row[csf('wo_qnty')];
            $trim_wo_data_arr[$row[csf('job_id')]][$row[csf('trim_id')]]['wo_amount'] += $row[csf('amount')];
			$trim_wo_data_arr[$row[csf('job_id')]][$row[csf('trim_id')]]['description'].= $row[csf('description')].'**';
            $trim_wo_data_arr[$row[csf('job_id')]][$row[csf('trim_id')]]['wo_date'][$row[csf('booking_id')]] = $row[csf('booking_date')];
            $trim_wo_data_arr[$row[csf('job_id')]][$row[csf('trim_id')]]['booking_no'][$row[csf('booking_id')]] = $row[csf('booking_no')];
            $trim_wo_data_arr[$row[csf('job_id')]][$row[csf('trim_id')]]['supplier'][$row[csf('booking_id')]] = $lib_supplier_arr[$row[csf('supplier_id')]];
        }

        $po_id_chunk = array_chunk($trim_poid_arr, 1000, true);
        $order_cond = "";
        $pi = 0;
        foreach ($po_id_chunk as $key => $value) {
            if ($pi == 0) {
                $order_cond = " and b.po_breakdown_id  in(" . implode(",", $value) . ")";
                $order_cond2 = " and a.to_order_id  in(" . implode(",", $value) . ")";
                $order_cond3 = " and a.from_order_id  in(" . implode(",", $value) . ")";
            } else {
                $order_cond .= " or b.po_breakdown_id  in(" . implode(",", $value) . ")";
                $order_cond2 .= " or a.to_order_id  in(" . implode(",", $value) . ")";
                $order_cond3 .= " or a.from_order_id  in(" . implode(",", $value) . ")";
            }
            $pi++;
        }

        $receive_qty_data = sql_select("SELECT b.po_breakdown_id, a.item_group_id,a.item_description, (b.quantity) as quantity, a.rate, e.job_id from inv_receive_master c,product_details_master d,inv_trims_entry_dtls a , order_wise_pro_details b, wo_po_break_down e where a.mst_id=c.id and a.trans_id=b.trans_id and a.id=b.dtls_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and d.status_active=1 and d.is_deleted=0 and e.id=b.po_breakdown_id $order_cond  order by a.item_group_id ");
		
        $trim_inhouse_qty = array();
        foreach ($receive_qty_data as $row) { 
            $trim_inhouse_qty[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['qty'] += $row[csf('quantity')];
            $trim_inhouse_qty[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['amount'] += $row[csf('quantity')] * $row[csf('rate')];
            $trim_inhouse_qty[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['rate'] = $row[csf('rate')];
            $trim_inhouse_qty[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['po_id'] = $row[csf('po_breakdown_id')];
            $trims_po_id_arr[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
        }
        unset($receive_qty_data);
        
        $issue_rtn_qty_data = sql_select("SELECT b.po_breakdown_id, d.item_group_id,d.item_description, (b.quantity) as quantity,  e.job_id from inv_receive_master c,inv_transaction a, order_wise_pro_details b, product_details_master d, wo_po_break_down e where c.id=a.mst_id and a.id=b.trans_id and a.prod_id=b.prod_id and b.prod_id=d.id and a.prod_id=d.id and e.id=b.po_breakdown_id $order_cond and b.trans_type=4 and b.entry_form=73 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and d.status_active=1 and d.is_deleted=0 order by d.item_group_id");
        $trim_issue_rtn_qty = array();
        foreach ($issue_rtn_qty_data as $row) {
        	$trim_issue_rtn_qty[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['qty'] += $row[csf('quantity')];
        }

        unset($issue_rtn_qty_data);

        $trim_issue_qty_data = sql_select("SELECT b.po_breakdown_id, a.item_group_id,p.item_description as item_description, (b.quantity) as quantity, a.rate, e.job_id  from  inv_issue_master d,product_details_master p,inv_trims_issue_dtls a , order_wise_pro_details b, wo_po_break_down e where a.mst_id=d.id and a.prod_id=p.id and a.trans_id=b.trans_id and b.trans_type=2 and b.entry_form=25 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and p.status_active=1 and p.is_deleted=0 and e.id=b.po_breakdown_id  $order_cond ");
		//echo "SELECT b.po_breakdown_id, a.item_group_id,p.item_description as item_description, sum(b.quantity) as quantity, a.rate, e.job_id  from  inv_issue_master d,product_details_master p,inv_trims_issue_dtls a , order_wise_pro_details b, wo_po_break_down e where a.mst_id=d.id and a.prod_id=p.id and a.trans_id=b.trans_id and b.trans_type=2 and b.entry_form=25 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.id=b.po_breakdown_id  $order_cond group by b.po_breakdown_id, a.item_group_id,p.item_description, e.job_id,a.rate";
		
        $trim_issue_qty = array();
        foreach ($trim_issue_qty_data as $row) {
            $rate = $trim_inhouse_qty[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['rate'];
            $trim_issue_qty[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['qty'] += $row[csf('quantity')];
            $trim_issue_qty[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['amount'] += $row[csf('quantity')] * $rate;
            $trim_issue_qty[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['po_id'] = $row[csf('po_breakdown_id')];
        }
 		unset($trim_issue_qty_data);


 		$trim_recv_rtn_qty_data = sql_select("SELECT b.po_breakdown_id, a.item_group_id,p.item_description as item_description, (b.quantity) as quantity, a.rate, e.job_id  from  inv_issue_master d,product_details_master p,inv_trims_issue_dtls a , order_wise_pro_details b, wo_po_break_down e where a.mst_id=d.id and a.prod_id=p.id and a.trans_id=b.trans_id and b.trans_type=3 and b.entry_form=49 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and p.status_active=1 and p.is_deleted=0 and e.id=b.po_breakdown_id  $order_cond ");
 		$trim_recv_rtn_qty = array();
        foreach ($trim_recv_rtn_qty_data as $row) {
        	$trim_recv_rtn_qty[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['qty'] += $row[csf('quantity')];
        }
        unset($trim_recv_rtn_qty_data);

		$trim_trnsf_in_qty_data = sql_select("SELECT b.po_breakdown_id,p.item_group_id,p.item_description as item_description, (b.quantity) as quantity, a.rate, e.job_id from inv_item_transfer_mst d,inv_item_transfer_dtls a,inv_transaction f, order_wise_pro_details b,product_details_master p, wo_po_break_down e where d.id=a.mst_id and a.mst_id=f.mst_id and f.id=b.trans_id and a.id=b.dtls_id and b.prod_id=p.id and f.prod_id=p.id and a.to_prod_id=p.id and b.po_breakdown_id=e.id  and b.trans_type=5 and b.entry_form=112 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and p.status_active=1 and p.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and f.item_category=4 $order_cond");
		$trim_transf_in_qty = array();
        foreach ($trim_trnsf_in_qty_data as $row) {
        	$trim_transf_in_qty[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['qty'] += $row[csf('quantity')];
        }
        unset($trim_trnsf_in_qty_data);

		$trim_trnsf_out_qty_data = sql_select("SELECT b.po_breakdown_id,p.item_group_id,p.item_description as item_description, (b.quantity) as quantity, a.rate, e.job_id from inv_item_transfer_mst d,inv_item_transfer_dtls a,inv_transaction f, order_wise_pro_details b,product_details_master p, wo_po_break_down e where d.id=a.mst_id and a.mst_id=f.mst_id and f.id=b.trans_id and a.id=b.dtls_id and b.prod_id=p.id and f.prod_id=p.id and a.to_prod_id=p.id and b.po_breakdown_id=e.id  and b.trans_type=6 and b.entry_form=112 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and p.status_active=1 and p.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and f.item_category=4 $order_cond");
		$trim_transf_out_qty = array();
        foreach ($trim_trnsf_out_qty_data as $row) {
        	$trim_transf_out_qty[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['qty'] += $row[csf('quantity')];
        }
        unset($trim_trnsf_out_qty_data);

        $trim_supplier_data = sql_select("SELECT a.job_id, a.supplier_id, b.id as trim_cost_id from wo_pre_cost_trim_supplier a join wo_pre_cost_trim_cost_dtls b on a.job_id=b.job_id and b.id=a.trimid where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $jobid_cond");

        $fabric_supplier_arr = array();
        foreach ($fabric_supplier_data as $row) {
            $trim_supplier_arr[$row[csf('job_id')]][$row[csf('trim_cost_id')]][$row[csf('supplier_id')]] = $lib_supplier_arr[$row[csf('supplier_id')]];
        }
 		unset($fabric_supplier_data);
		$trims_pi_data=sql_select("SELECT a.id, a.lc_number,a.lc_type_id,a.payterm_id,a.tenor, c.pi_number 
		FROM com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_master_details c WHERE a.id=b.com_btb_lc_master_details_id and b.pi_id=c.id and a.is_deleted = 0 
		 and a.item_basis_id=1 and a.importer_id=$company_name 
		  UNION ALL
		  SELECT a.id, a.lc_number,a.lc_type_id,a.payterm_id,a.tenor,c.pi_number
		  FROM com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_master_details c
		 WHERE a.id=b.com_btb_lc_master_details_id and b.pi_id=c.id and a.is_deleted = 0 and a.item_basis_id=2 and a.importer_id=$company_name  
		 order by id desc ");
			$trim_pi_lc_data_arr = array();$fab_pi_lc_data_arr = array();
			foreach ($trims_pi_data as $key => $row) {
				$trim_pi_lc_data_arr[$row[csf('pi_number')]]['lc_number']= $row[csf('lc_number')];;
				$fab_pi_lc_data_arr[$row[csf('pi_number')]]['lc_number']= $row[csf('lc_number')];
			}
			unset($trims_pi_data);

        $trims_pi_number_data = sql_select("SELECT a.job_id,d.pi_number, c.amount, e.lc_number, c.item_group, b.pre_cost_fabric_cost_dtls_id, c.item_color, c.item_size from wo_pre_cost_trim_cost_dtls a join wo_booking_dtls b on a.id=b.pre_cost_fabric_cost_dtls_id join com_pi_item_details c on b.booking_no=c.work_order_no  join com_pi_master_details d on d.id=c.pi_id left join com_btb_lc_master_details e on TO_CHAR(c.pi_id)=e.pi_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.entry_form=167 $jobid_cond1 group by a.job_id, d.pi_number, c.amount, e.lc_number, c.item_group, b.pre_cost_fabric_cost_dtls_id, c.item_color, c.item_size");
        $trim_pi_data_arr = array();
        foreach ($trims_pi_number_data as $key => $row) {
            $trim_pi_data_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('job_id')]][$row[csf('item_group')]]['pi_no'].= $row[csf('pi_number')].',';
            $trim_pi_data_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('job_id')]][$row[csf('item_group')]]['amount'] += $row[csf('amount')];
            $trim_pi_data_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('job_id')]][$row[csf('item_group')]]['blc_no'].= $row[csf('lc_number')].',';
        }
        if (count($trimjob_id_arr) > 0) {
           // $trimjob_id_str = implode(",", $trimjob_id_arr);
           // $condition->jobid_in($trimjob_id_str);
        }
      //  $condition->init();
        $trim = new trims($condition);
        $trim_group_qty_arr = $trim->getQtyArray_by_jobAndPrecostdtlsid();

        //$trim_group_amount_arr=$trim->getAmountArray_by_jobAndPrecostdtlsid();

        $trim_amountSourcing_arr = $trim->getAmountArray_precostdtlsidSourcing();

        $partial_fabric_report_type = array(84 => 'show_fabric_booking_report_urmi_per_job', 85 => 'print_booking_3', 143 => 'show_fabric_booking_report_urmi', 151 => 'show_fabric_booking_report_advance_attire_ltd', 160 => 'print_booking_5', 175 => 'print_booking_6', 155 => 'fabric_booking_report', 235 => 'print_9', 191 => 'print_booking_7');

        $print_report_format = return_field_value("format_id", " lib_report_template", "template_name ='" . $company_name . "'  and module_id=2 and report_id=35 and is_deleted=0 and status_active=1");
        $print_report_format_arr = explode(",", $print_report_format);
        $fabric_report_first_id = $print_report_format_arr[0];
    }
	ob_start();
	?>
	<div style="width:2000px">
		<table width="3040">
	        <tr class="form_caption"><td colspan="18" align="center"><? echo $report_title; ?></td></tr>
	        <tr class="form_caption"><td colspan="18" align="center"><? echo $company_library[$company_name]; ?></td></tr>
		</table>
        <?
        if(count($main_data_sql) > 0) {
        ?>
		<table class="rpt_table" width="3040" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
					<tr>
						<td colspan="29" align="left"><strong>Fabric Details</strong></td>
					</tr>
					<tr>
					<th width="30">SL</th>
					<th width="100">Buyer</th>
					<th width="100">Job No</th>
					<th width="80">Season</th>
					<th width="130">Style Ref</th>
					<th width="100">Order Qty</th>
					<th width="50">UOM</th>
					<th width="60">Max.Ship Date</th>
					<th width="200">Item Description</th>
					<th width="100">Fabric Color</th>
					<th width="100">Garments Color</th>
					<th width="80">PO Date</th>
					<th width="60">Avg. Cons</th>
					<th width="60">Rate</th>
					<th width="80">Req Qty</th>					
					<th width="80">PO Qty</th>
					<th width="80">PO NO.</th>
					<th width="50">UOM</th>
					<th width="80">In-House Qty</th>
					<th width="80">Issue Return Qty</th>
					<th width="80">Transfer In Qty</th>
					<th width="80">Total In-House Qty</th>
					<th width="80">Receive Balance</th>
					<th width="80">Issue to Cutting</th>
					<th width="80">Receive Return Qty</th>
					<th width="80">Transfer Out Qty</th>
					<th width="80">Total Issue to Cutting</th>
					<th width="80">Issue Balance</th>
					<th width="100">Supplier</th>
					<th width="120">PI No.</th>
					<th width="100">BTB LC N0</th>
				</tr>
			</thead>
			<tbody>
				<? $sl=1;  
				/*echo '<pre>';
				print_r($main_data_arr); die;*/
				foreach ($main_data_arr as $job_id => $job_data) {
					if($sl%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";					
					//if($i==1){ ?>
					<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $sl; ?>">
					<td rowspan="<?= $rowspan[$job_id]; ?>" width="30" align="center"><?= $sl; ?></td>
					<td rowspan="<?= $rowspan[$job_id]; ?>" width="80"  align="left"><?= $buyer_short_name_library[$job_data['buyer_name']]; ?></td>
					<td rowspan="<?= $rowspan[$job_id]; ?>" width="80"  align="left"><?= $job_data['job_no']; ?></td>
					<td rowspan="<?= $rowspan[$job_id]; ?>" align="left"><?= $lib_season_arr[$job_data['season_buyer_wise']]; ?></td>
					<td rowspan="<?= $rowspan[$job_id]; ?>"><p>
					<a href='#report_details' onClick="order_summery_popup('<? echo $job_id; ?>' ,'order_summery_data');"><?= $job_data['style_ref_no']; ?></a></p></td>
					<td rowspan="<?= $rowspan[$job_id]; ?>"  align="right"><p>
					<a href='#report_details' onClick="order_summery_popup('<? echo $job_id; ?>' ,'order_qty_data');"><?= $job_data['job_quantity']; ?></a></p></td>
					<td rowspan="<?= $rowspan[$job_id]; ?>"><?= $unit_of_measurement[$job_data['order_uom']]; ?></td>
					<td rowspan="<?= $rowspan[$job_id]; ?>"><?= $max_ship_arr[$job_id];  ?></td>
					<?	
					$i=1;			
					foreach ($job_data['color_data'] as $lib_yarn_id=>$color_data) {
						// if($i!=1) echo '<tr>';
						if($i!=1) echo '<tr onclick="change_color(\'tr_'.$i.'\',\''.$bgcolor.'\')" id="tr_'.$i.'">';
						$gsm_weight=implode(",",array_unique(explode(",",chop($color_data['gsm_weight'],","))));
						$gsm_weight_type=implode(",",array_unique(explode(",",chop($color_data['gsm_weight_type'],","))));
						$cutable_width=implode(",",array_unique(explode(",",chop($color_data['cutable_width'],","))));
							 ?>
							<td rowspan="<?= count($color_data['fabric_color']) ?>"><?= $color_data['item_des'] ?>&nbsp;(<?= $gsm_weight,','; ?>&nbsp;<?= $gsm_weight_type,','; ?>&nbsp;<?= $cutable_width; ?>&nbsp;Cutable Width)</td>
						<?						
						$k=1;
						foreach ($color_data['fabric_color'] as $fcolor_id=>$fcolor_data) {
							// if($k!=1) echo '<tr>';
							if($k!=1) echo '<tr onclick="change_color(\'tr_'.$i.'\',\''.$bgcolor.'\')" id="tr_'.$i.'">';

							$gmts_color=array();
							$gmts_color_id=array();$issuePoidChkArr=array();$booking_noArr=array();$supplierArr=array();$issuePOidArr=array();
							$bom_qty=0;	$wo_qty=0; $bom_value=0; $wo_amount=0; $pi_amount=0;$issueqty=$inhouseqty=$issue_rtn_qty=$recv_rtn_qty=$transIn_qnty=$transOut_qnty=0;$issuepo="";$transIn_po="";$booking_noArr=array();$supplierArr=array();
							foreach ($fcolor_data as $gcolor_id => $row) {
								$fabric_color_id=$row['color'];
								$color_id = $gcolor_id;
								$gmts_color[$gcolor_id]=$color_arr[$gcolor_id];
								$gmts_color_id[$gcolor_id]=$gcolor_id;
								$wo_qty += $wo_data_arr[$job_id][$lib_yarn_id][$color_id][$fabric_color_id]['fin_fab_qnty'];
								
								/*$booking_no=$wo_data_arr[$job_id][$lib_yarn_id][$color_id][$fabric_color_id]['booking_no'];
								$suppliers=$wo_data_arr[$job_id][$lib_yarn_id][$color_id][$fabric_color_id]['supplier'];
								$booking_noArr[$booking_no]=$booking_no;
								$supplierArr[$suppliers]=$suppliers;*/
								 $booking_no=rtrim($wo_data_arr[$job_id][$lib_yarn_id][$color_id][$fabric_color_id]['booking_no'],',');
								$booking_no_chk=implode(",",array_unique(explode(",",chop($booking_no,","))));
								$suppliers=rtrim($wo_data_arr[$job_id][$lib_yarn_id][$color_id][$fabric_color_id]['supplier'],',');
								$suppliers_chk=implode(",",array_unique(explode(",",chop($suppliers,","))));
								$booking_noArr[$booking_no_chk].=$booking_no_chk.',';
								$supplierArr[$suppliers_chk].=$suppliers_chk.',';
								
								//$issueqty+= $issue_qty_arr[$job_id][$row['po_id']][$lib_yarn_id][$fabric_color_id]['qty'];
								//$issuepo= $issue_qty_arr[$job_id][$row['po_id']][$lib_yarn_id][$fabric_color_id]['po_id'];
								$supplier= $issue_qty_arr[$job_id][$row['po_id']][$lib_yarn_id][$fabric_color_id]['supplier'];

								$issuePoidsExp=array_unique(explode(",",chop($row['po_ids'],",")));
								foreach ($issuePoidsExp as $issuePoid) {
									if($issue_po_arr[$job_id][$issuePoid][$lib_yarn_id][$fabric_color_id]['po_id'])
									{
									//$issuepo.= $issue_po_arr[$job_id][$issuePoid][$lib_yarn_id][$fabric_color_id]['po_id'].",";
									$issuePOidArr[$issuePoid]=$issue_po_arr[$job_id][$issuePoid][$lib_yarn_id][$fabric_color_id]['po_id'];
									}
									//$issuepo.= $issue_po_arr[$job_id][$issuePoid][$lib_yarn_id][$fabric_color_id]['po_id'].",";
									$transIn_qnty+= $transIn_qty_arr[$job_id][$issuePoid][$lib_yarn_id][$fabric_color_id]['qty'];
									//$transIn_po.= $transIn_qty_arr[$job_id][$issuePoid][$lib_yarn_id][$fabric_color_id]['po_id'].",";
									$issueqty+= $issue_qty_arr[$job_id][$issuePoid][$lib_yarn_id][$fabric_color_id]['qty'];
									$issueqtyval= $issue_qty_arr[$job_id][$issuePoid][$lib_yarn_id][$fabric_color_id]['qty'];
									$transOut_qnty+= $transOut_qty_arr[$job_id][$issuePoid][$lib_yarn_id][$fabric_color_id]['qty'];
									$issuePoidChkArr[$issuePoid]=$issuePoid;
								}
								//$issuepo=chop($issuepo,",");
								//$transIn_po=chop($transIn_po,",");
								

								$recv_rtn_qty+= $recv_rtn_qty_arr[$job_id][$row['po_id']][$lib_yarn_id][$fabric_color_id]['qty'];
								$recv_rtn_po= $recv_rtn_qty_arrNew[$job_id][$lib_yarn_id][$fabric_color_id]['po_id'];
								$recv_rtn_po=chop($recv_rtn_po,",");

								$bom_value+= $fabric_amount_arr['woven']['grey'][$job_id][$lib_yarn_id][$color_id][$fabric_color_id];
								$bom_qty +=$fabric_qty_arr['woven']['grey'][$job_id][$lib_yarn_id][$color_id][$fabric_color_id];
								foreach($row['po_id'] as $fabricpoid){
									$inhouseqty+= $receive_qty_arr[$job_id][$fabricpoid][$lib_yarn_id][$fabric_color_id]['qty'];
								}
								

								//$inhousepo=$receive_qty_arr[$job_id][$lib_yarn_id][$fabric_color_id]['po_id'];
								$inhousepo=implode(",", $row['po_id']);

								$issue_rtn_qty+= $issue_rtn_qty_arr[$job_id][$lib_yarn_id][$fabric_color_id]['qty'];
								$issue_rtn_po=$issue_rtn_qty_arr[$job_id][$lib_yarn_id][$fabric_color_id]['po_id'];


								//$transIn_qnty+= $transIn_qty_arr[$job_id][$row['po_id']][$lib_yarn_id][$fabric_color_id]['qty'];
								//$transIn_po= $transIn_qty_arr[$job_id][$row['po_id']][$lib_yarn_id][$fabric_color_id]['po_id'];

								
								$transOut_po= $transOut_qty_arr[$job_id][$row['po_id']][$lib_yarn_id][$fabric_color_id]['po_id'];

							
								$rcv_balance=$wo_qty-$inhouseqty;
								
								$rcv_rate=$receive_qty_arr[$job_id][$lib_yarn_id][$fabric_color_id]['rate'];
							}	
							//$inhouseqty= $receive_qty_arr[$job_id][$lib_yarn_id][$fcolor_id]['qty'];
								//echo $inhouseqty.',';
							//$issue_balance=$inhouseqty-$issueqty;
							$transIn_po=implode(",",$issuePoidChkArr);
							$issue_balance=($transIn_qnty+$inhouseqty+$issue_rtn_qty)-($transOut_qnty+$recv_rtn_qty+$issueqtyval);
													
							$pi_no=rtrim($pi_data_arr[$job_id][$lib_yarn_id][$fabric_color_id]['pi_no'],',');
							$pi_nos=implode(", ",array_unique(explode(",",$pi_no)));
							$blc_no=rtrim($pi_data_arr[$job_id][$lib_yarn_id][$fabric_color_id]['blc_no'],',');
							$blc_nos=implode(", ",array_unique(explode(",",$blc_no)));
							
							$pi_nosArr=array_unique(explode(",",$pi_no));
							$lc_number_lcArr=array();
							foreach($pi_nosArr as $pid)
							{
								$lc_number=$fab_pi_lc_data_arr[$pid]['lc_number'];
								if($lc_number!="")
								{
									$lc_number_lcArr[$lc_number]=$lc_number;
								}
							}
							$issuepo=implode(",",$issuePOidArr);
							 $booking_noall=implode(", ",$booking_noArr);
							$booking_noall=rtrim($booking_noall,',');
							 $supplierAll=implode(", ",$supplierArr);
							 $supplierAll=rtrim($supplierAll,',');
							
						 	?>
							<td align="left"><?= $color_arr[$fcolor_id] ?></td>
							<td align="left"><?= implode(", ", $gmts_color) ?></td>
							<td align="left"><?= implode(",",$wo_data_arr[$job_id][$lib_yarn_id][$color_id][$fabric_color_id]['wo_date'] )  ?></td>
							<td align="right"><?= fn_number_format($row['avg_cons'],4) ?></td>
							<td align="right" title="Avg Rate<?=$bom_value;?>"><?= fn_number_format($bom_value/$bom_qty,4) ?></td>
							<td align="right"><a href='#report_details' onClick="order_req_qty_popup('<? echo $job_id; ?>','<? echo $lib_yarn_id; ?>','<? echo $fabric_color_id; ?>' ,'order_req_qty_data');"><?= fn_number_format($bom_qty,2)?></a></td>
							<td align="right"><a href='#report_details' onClick="order_wo_qty_popup('<? echo $job_id; ?>','<? echo $lib_yarn_id; ?>','<? echo $fabric_color_id; ?>','<? echo implode(",", $gmts_color_id) ?>' ,'order_wo_qty_data');"><?= fn_number_format($wo_qty,2)  ?></a></td>
							<td align="left"><?=$booking_noall;  ?></td>
							<td align="left"><?= $unit_of_measurement[$row['fabric_uom']] ?></td>
							

							<td align="right"><a href='#report_details' onClick="openmypage_inhouse('<? echo $job_id; ?>','<? echo $lib_yarn_id; ?>','<? echo $inhousepo; ?>','<? echo $fabric_color_id ?>' ,'booking_inhouse_info');"><?= fn_number_format($inhouseqty,2) ?></a></td>
							<td align="right"><a href='#report_details' onClick="openmypage_inhouse('<? echo $job_id; ?>','<? echo $lib_yarn_id; ?>','<? echo $issue_rtn_po; ?>','<? echo $fabric_color_id ?>' ,'booking_inhouse_info_issue_rtn');"><?= fn_number_format($issue_rtn_qty,2) ?></a></td>
							<td align="right"><a href='#report_details' onClick="openmypage_inhouse2('<? echo $job_id; ?>','<? echo $lib_yarn_id; ?>','<? echo $transIn_po ; ?>','<? echo $transOut_po; ?>','<? echo $fabric_color_id ?>' ,'booking_inhouse_info_trans_in');"><?= fn_number_format($transIn_qnty,2) ?></a></td>
							<td align="right"><?= fn_number_format($transIn_qnty+$inhouseqty+$issue_rtn_qty,2); ?></td>


							<td align="right"><?= fn_number_format($rcv_balance,2)  ?></td>
							

							<td align="right"><? if($issueqty>0){ ?><a href='#report_details' onClick="openmypage_issue('<? echo $job_id; ?>','<? echo $lib_yarn_id; ?>','<? echo $issuepo; ?>','<? echo $fabric_color_id?>', '<?echo $rcv_rate?>' ,'booking_issue_info');"><?= fn_number_format($issueqtyval,2)  ?></a><? } else echo '0.00'; ?></td>
							<td align="right"><? if($recv_rtn_qty>0){ ?><a href='#report_details' onClick="openmypage_issue('<? echo $job_id; ?>','<? echo $lib_yarn_id; ?>','<? echo $recv_rtn_po; ?>','<? echo $fabric_color_id?>', '<?echo $rcv_rate?>' ,'booking_issue_info_recv_rtn');"><?= fn_number_format($recv_rtn_qty,2)  ?></a><? } else echo '0.00'; ?></td>
							<td align="right"><? if($transOut_qnty>0){ ?><a href='#report_details' onClick="openmypage_issue2('<? echo $job_id; ?>','<? echo $lib_yarn_id; ?>','<? echo $transOut_po; ?>','<? echo $transIn_po ; ?>','<? echo $fabric_color_id?>', '<?echo $rcv_rate?>' ,'booking_inhouse_info_trans_out');"><?= fn_number_format($transOut_qnty,2)  ?></a><? } else echo '0.00'; ?></td>
							<td align="right"><? echo fn_number_format($transOut_qnty+$recv_rtn_qty+$issueqtyval,2); ?></td>
							


							<td align="right"><?= fn_number_format($issue_balance,2) ?></td>
							<td align="left"><?= $supplierAll; ?></td>
							<td align="left"><p> &nbsp; <?=$pi_nos; ?></p></td>
							<td align="left"><p>&nbsp;<?=implode(", ",$lc_number_lcArr);//$blc_nos; ?></p></td>
							<?
							$k++;
							$i++;
						 }
						 
							
						} ?>
							</tr>
						<?
						$sl++;
						}
				?>
				
			</tbody>
		</table>
        <?
        }
        if(count($trims_sql_data) > 1) {
        ?>
		<table class="rpt_table" width="3030" cellpadding="0" cellspacing="0" border="1" rules="all" style="margin-top: 10px">
			<thead>
				<tr>
					<td colspan="30" align="left"><strong>Trims Details</strong></td>
				</tr>
				<tr>
					<th width="35">SL</th>
					<th width="95">Buyer</th>
					<th width="95">Job No</th>
					<th width="95">Season</th>
	                <th width="95">Style Ref</th>
					<th width="60">Order Qnty</th>
					<th width="50">UOM</th>
					<th width="60">Max.Ship Date</th>
					<th width="100">Trims Name</th>
					<th width="150">Item Description</th>
					<th width="100">Brand/Sup Ref</th>
					<th width="80">PO Date</th>
					<th width="80">Avg. Cons</th>
					<th width="80">Rate</th>
					<th width="100">Req Qnty</th>
					<th width="90">PO Qty</th>
					<th width="90">PO NO.</th>
	                <th width="50">UOM</th>
	                <th width="80">In-House Qty</th>
	                <th width="80">Issue Return Qty</th> 	 	
	                <th width="80">Transfer In Qty</th>
	                <th width="80">Total In-House Qty</th>
	                <th width="80">Receive Balance</th>
	                <th width="70">Issue to Prod.</th>
	                <th width="70">Receive Return Qty</th>
	                <th width="70">Transfer Out Qty </th>
	                <th width="70">Total Issue to Cutting</th> 		
					<th width="90">Issue Balance</th>
	                <th width="90">Supplier</th>
					<th width="120">PI No.</th>
					<th width="90">BTB LC N0</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$tsl=1;
				?>
				<?php foreach ($trims_main_data as $job_id=>$value){
					$rowspan= count($value['trims_data']);
					if($tsl%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				 ?>
					<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr2_<? echo $tsl; ?>','<? echo $bgcolor;?>')" id="tr2_<? echo $tsl; ?>">
						<td width="35" rowspan="<?= $rowspan ?>"><?= $tsl; ?></td>
						<td width="95" rowspan="<?= $rowspan ?>"align="left"><?= $buyer_short_name_library[$value['buyer_name']]; ?></td>
						<td width="95" rowspan="<?= $rowspan ?>"align="left"><?= $value['job_no']; ?></td>
						<td width="95" rowspan="<?= $rowspan ?>"align="left"><?= $lib_season_arr[$value['season_buyer_wise']] ?></td>
		                <td width="95" rowspan="<?= $rowspan ?>" align="left">
					<a href='#report_details' onClick="order_summery_popup('<? echo $job_id; ?>' ,'order_summery_data');"><?= $value['style_ref_no']; ?></a></td>
						<td width="60"  align="right" rowspan="<?= $rowspan ?>"><a href='#report_details' onClick="order_summery_popup('<? echo $job_id; ?>' ,'order_qty_data');"><?= $value['job_quantity']; ?></a></td>
						<td width="50" rowspan="<?= $rowspan ?>" align="left"><?= $unit_of_measurement[$value['order_uom']]; ?></th>
						<td width="60" rowspan="<?= $rowspan ?>" align="left"><?= $max_ship_arr[$job_id];  ?></td>
						<?

						$z=1;
						foreach ($value['trims_data'] as $trims_id=>$row) {
							// if($z!=1) echo '<tr>';
							if($z!=1) echo '<tr onclick="change_color(\'trs_'.$z.'\',\''.$bgcolor.'\')" id="trs_'.$z.'">';
							
							$req_qty = $trim_group_qty_arr[$value['job_no']][$trims_id];
							$wo_qty= $trim_wo_data_arr[$job_id][$trims_id]['wo_qnty'];
							
							$description= rtrim($trim_wo_data_arr[$job_id][$trims_id]['description'],'**');
							$descriptions=implode(", ",array_unique(explode("**",$description)));
							$descriptionsArr=array_unique(explode("**",$description));
							$inhouse_qty=$issue_qty=0;$trims_recv_rtn_qty=0;$trims_transf_out_qty=0;$trims_transf_in_qty=0;
							foreach($descriptionsArr as $desc)
							{
								
								$inhouse_qty+= $trim_inhouse_qty[$job_id][$row['trim_group']][$desc]['qty'];
								$issue_qty+= $trim_issue_qty[$job_id][$row['trim_group']][$desc]['qty'];
								$po_id= $trim_inhouse_qty[$job_id][$row['trim_group']][$desc]['po_id'];
								$tpo_id= $trim_issue_qty[$job_id][$row['trim_group']][$desc]['po_id'];


								$trims_issue_rtn_qty= $trim_issue_rtn_qty[$job_id][$row['trim_group']][$desc]['qty'];
								//echo $trim_issue_rtn_qty[$job_id][$row['trim_group']][$desc]['qty']."<br/>";
								//echo $trims_issue_rtn_qty."<br/>";
								$trims_recv_rtn_qty+= $trim_recv_rtn_qty[$job_id][$row['trim_group']][$desc]['qty'];
								$trims_transf_out_qty+= $trim_transf_out_qty[$job_id][$row['trim_group']][$desc]['qty'];
								$trims_transf_in_qty+= $trim_transf_in_qty[$job_id][$row['trim_group']][$desc]['qty'];

							}

							$total_trims_recvQnty=$inhouse_qty+$trims_transf_in_qty+$trims_issue_rtn_qty;
							$total_trims_issueQnty=$issue_qty+$trims_recv_rtn_qty+$trims_transf_out_qty;
							 
							
							//	if($row['description']!='') $desc=$row['description'];else $desc=0;
							
							$trimrcv_balance = $wo_qty-$inhouse_qty;
							$trims_poid=implode(",",$trims_po_id_arr[$job_id][$row['trim_group']]);
							//echo $row['description'].'D'.$trims_poid.',';
							$trim_pi_no=rtrim($trim_pi_data_arr[$trims_id][$job_id][$row['trim_group']]['pi_no'],',');
							$trim_pi_nos=implode(", ",array_unique(explode(",",$trim_pi_no)));
							
							$trim_blc_no=rtrim($trim_pi_data_arr[$trims_id][$job_id][$row['trim_group']]['blc_no'],',');
							$trim_blc_nos=implode(", ",array_unique(explode(",",$trim_blc_no)));
						?>
						<td width="100" title="<? echo $row['trim_group']; ?>" align="left"><?= $item_library[$row['trim_group']]; ?></td>
						<td width="150" align="left"><?= $descriptions; ?></td>
						<td width="100" align="left"><?= $row['brand_sup_ref']; ?></td>
						<td width="80" align="left"><?= implode(",", $trim_wo_data_arr[$job_id][$trims_id]['wo_date'])?></td>
						<td width="80" align="left"><?= fn_number_format($row['cons_dzn_gmts'],4); ?></td>
						<td width="80" align="left"><?= fn_number_format($row['sourcing_rate'],4); ?></td>
						<td width="100" align="right"><a href='#report_details' onClick="trim_req_qty_popup('<? echo $job_id; ?>','<? echo $trims_id; ?>' ,'trim_req_qty_data');"><?= fn_number_format($req_qty,2); ?></a></td>
						<td width="90" align="right"><a href='#report_details' onClick="trim_wo_qty_popup('<? echo $job_id; ?>','<? echo $trims_id; ?>' ,'trim_wo_qty_data');"><?= fn_number_format($wo_qty,2) ?></a></td>
						<td width="50" align="left"><?= implode(",", $trim_wo_data_arr[$job_id][$trims_id]['booking_no'])?></td>
		                <td width="50" align="right"><?= $unit_of_measurement[$row['cons_uom']]; ?></td>	                
		                

		                <td width="80" align="right" title="Des=<? echo $descriptions;?>"><a href='#report_details' onClick="openmypage_trim_inhouse('<? echo $trims_poid;  ?>','<? echo $row['trim_group'].'__'.$row['description']; ?>','trim_booking_inhouse_info');"><?= fn_number_format($inhouse_qty,2);  ?></a></td>
		                <td width="80" align="right" title="Des=<? echo $descriptions;?>"><a href='#report_details' onClick="openmypage_trim_inhouse('<? echo $trims_poid; ?>','<? echo $row['trim_group'].'__'.$descriptions; ?>','trim_booking_issueRtn_info');"><?= fn_number_format($trims_issue_rtn_qty,2);  ?></a></td>
		                <td width="80" align="right" title="Des=<? echo $descriptions;?>"><a href='#report_details' onClick="openmypage_trim_inhouse2('<? echo $trims_poid;  ?>','<? echo $trims_poid;  ?>','<? echo $row['trim_group'];  ?>','<? echo $row['description']; ?>','trim_booking_transfIn_info');"><?= fn_number_format($trims_transf_in_qty,2);  ?></a></td>
		                <td width="80" align="right" title="Des=<? echo $descriptions;?>"><?= fn_number_format($total_trims_recvQnty,2);  ?></td>

		                <td width="80" align="right"><?= fn_number_format($trimrcv_balance,2); ?></td>
		                
		                <td width="70" align="right"><a href='#report_details' onClick="openmypage_trim_issue('<? echo $trims_poid; ?>','<? echo $row['trim_group'].'__'.$descriptions; ?>','trim_booking_issue_info');"><?= fn_number_format($issue_qty,2);  ?></a></td>
		                <td width="70" align="right"><a href='#report_details' onClick="openmypage_trim_issue('<? echo $trims_poid; ?>','<? echo $row['trim_group'].'__'.$descriptions; ?>','trim_booking_recvRtn_info');"><?= fn_number_format($trims_recv_rtn_qty,2);  ?></a></td>
		                <td width="70" align="right"><a href='#report_details' onClick="openmypage_trim_issue2('<? echo $trims_poid; ?>','<? echo $trims_poid; ?>','<? echo $row['trim_group']; ?>','<? echo $descriptions; ?>','trim_booking_trnsfOut_info');"><?= fn_number_format($trims_transf_out_qty,2);  ?></a></td>
		                <td width="70" align="right"><?= fn_number_format($total_trims_issueQnty,2);  ?></td>
						

						<td width="90" align="right"><?= fn_number_format($total_trims_recvQnty-$total_trims_issueQnty,2); ?></td>
		                <td width="90" align="left"><?=implode(",", $trim_wo_data_arr[$job_id][$trims_id]['supplier'])?></td>
						<td width="120" align="left"><p> &nbsp; <?=$trim_pi_nos;?></p></td>
						<td width="90" title="<?= $trims_id.'='.$job_id.'='.$row['trim_group'];?>" align="left"><p>&nbsp;<?=$trim_pi_lc_data_arr[$trim_pi_nos]['lc_number'];//$trim_blc_nos;?></p></td>
						<? $z++;
						 } ?>
					</tr>
				<?php 
					$tsl++;
					} 
				?>								
			</tbody>
		</table>
        <?
        }
        ?>
		<div style="width:2660px; max-height:400px; overflow-y:scroll" id="scroll_body">
		</div>
	</div>
	<?
		$total_data = ob_get_contents();
		ob_clean();
		foreach (glob("*.xls") as $filename) {
		@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename=$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc,$total_data);
		echo "$total_data****$filename";
		exit();
}

if($action=="report_generate5")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
	$country_name_library=return_library_array( "select id, country_name from lib_country", "id", "country_name"  );

	$lib_supplier_arr=return_library_array( "select id, short_name from lib_supplier", "id", "short_name"  );
	$lib_season_arr=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name"  );
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1", "id", "color_name");

	$company_name=str_replace("'","",$cbo_company_name);
	$based_on=str_replace("'","",$cbo_based_on);
	$buyer_id_cond="";
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
				$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
	}

	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_job_no=trim($txt_job_no);

	if($txt_job_no!="" || $txt_job_no!=0) $jobcond="and a.job_no_prefix_num in($txt_job_no)"; else $jobcond="";
	if(str_replace("'","",$txt_style_ref)!="") $style_ref_cond=" and a.style_ref_no like '%".str_replace("'","",$txt_style_ref)."%'"; else $style_ref_cond="";


	$date_type=str_replace("'","",$cbo_date_type);
	
	$date_cond='';
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!=""){
	if($based_on==1){
		$start_date=(str_replace("'","",$txt_date_from));
		$end_date=(str_replace("'","",$txt_date_to));
		$date_cond="and b.pub_shipment_date between '$start_date' and '$end_date'";
	}
	else{
		$start_date=(str_replace("'","",$txt_date_from));
		$end_date=(str_replace("'","",$txt_date_to));
		$date_cond="and t.actual_start_date between '$start_date' and '$end_date'";
	}
	}

	$cbo_year=str_replace("'","",$cbo_year);
	if($db_type==0)
	{
		if(trim($cbo_year)!=0) $year_cond=" and YEAR(a.insert_date)=$cbo_year"; else $year_cond="";
	}
	else if($db_type==2)
	{
		if(trim($cbo_year)!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year"; else $year_cond="";
	}
	$main_data_sql=sql_select("SELECT a.id, a.job_no, a.buyer_name, a.job_no_prefix_num, a.season_buyer_wise, a.style_ref_no, a.job_quantity, a.order_uom, a.company_name,  b.id as po_id, b.po_number,  c.color_number_id, c.order_quantity, e.id as fabric_cost_id, e.lib_yarn_count_deter_id, e.body_part_id,e.fabric_description, e.uom as fabric_uom, avg(f.requirment) as avg_cons, e.color_size_sensitive, e.color, e.color_break_down, g.contrast_color_id, h.stripe_color,e.rate,e.fab_nature_id from wo_po_details_master a join wo_po_break_down b on a.id=b.job_id join wo_po_color_size_breakdown c on a.id=c.job_id and b.id=c.po_break_down_id join wo_pre_cost_mst d on a.id=d.job_id join wo_pre_cost_fabric_cost_dtls e on a.id=e.job_id join wo_pre_cos_fab_co_avg_con_dtls f on e.id=f.pre_cost_fabric_cost_dtls_id left join wo_pre_cos_fab_co_color_dtls g on a.id=g.job_id and e.id=g.pre_cost_fabric_cost_dtls_id and c.color_number_id =g.gmts_color_id and g.status_active=1 and g.is_deleted=0 left join wo_pre_stripe_color h on a.id=h.job_id and  c.item_number_id= h.item_number_id and e.id=h.pre_cost_fabric_cost_dtls_id and f.color_number_id =h.color_number_id and f.po_break_down_id=h.po_break_down_id and f.gmts_sizes=h.size_number_id left join tna_process_mst t on b.id=t.po_number_id and t.task_number=354 where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.company_name=$company_name $year_cond $date_cond $style_ref_cond $jobcond $buyer_id_cond group by a.id, a.job_no, a.buyer_name, a.job_no_prefix_num, a.season_buyer_wise, a.style_ref_no, a.job_quantity, a.order_uom, b.id,b.po_number, c.color_number_id, c.order_quantity, e.lib_yarn_count_deter_id, e.fabric_description, e.uom, e.id, e.color_size_sensitive, e.color, e.body_part_id, e.color_break_down,g.contrast_color_id, h.stripe_color, a.company_name,e.rate,e.fab_nature_id  order by e.id asc");
    if(count($main_data_sql) > 0) {
        $main_attribute = array('job_no', 'buyer_name', 'job_no_prefix_num', 'season_buyer_wise', 'style_ref_no', 'job_quantity', 'order_uom', 'company_name');
        foreach ($main_data_sql as $row) {
            foreach ($main_attribute as $attr) {
                $main_data_arr[$row[csf('id')]][$attr] = $row[csf($attr)];
            }
            $fabricColorId = $row[csf('stripe_color')];
            if (!$fabricColorId) {
                $fabricColorId = $row[csf('contrast_color_id')];
            }
            if (!$fabricColorId) {
                $fabricColorId = $row[csf('color_number_id')];
            }
			 $po_idArr[$row[csf('po_id')]] = $row[csf('id')];
			 
            $main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['item_des'] = $body_part[$row[csf('body_part_id')]] . ',' . $row[csf('fabric_description')];
            $main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['color_id'] = $row[csf('color_number_id')];
            $main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['po_number'] = $row[csf('po_number')];
			$main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['po_id'] .= $row[csf('po_id')].',';
			
            $main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['fabric_uom'] = $row[csf('fabric_uom')];
            $main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['avg_cons'] = $row[csf('avg_cons')];
            $main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['sourcing_rate'] = $row[csf('rate')];
            $main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['sensitive'] = $row[csf('color_size_sensitive')];
            $main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['color'] = $fabricColorId;
           $main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['fab_nature_id'] = $row[csf('fab_nature_id')];

            $job_id_arr[$row[csf('id')]] = $row[csf('id')];
            $fabric_id_arr[$row[csf('fabric_cost_id')]] = $row[csf('fabric_cost_id')];
            $po_id_arr[$row[csf('po_id')]] = $row[csf('po_id')];
        }
        /*echo '<pre>';
        print_r($main_data_arr); die;*/
        $po_id = array_chunk($po_id_arr, 1000, true);
        $order_cond = "";
        $po_cond_for_in2 = "";
        $ji = 0;
        foreach ($po_id as $key => $value) {
            if ($ji == 0) {
                $order_cond = " and c.po_breakdown_id  in(" . implode(",", $value) . ")";
                $po_cond_for_in2 = " and a.po_breakdown_id  in(" . implode(",", $value) . ")";
            } else {
                $order_cond .= " or c.po_breakdown_id  in(" . implode(",", $value) . ")";
                $po_cond_for_in2 .= " or a.po_breakdown_id  in(" . implode(",", $value) . ")";
            }
            $ji++;
        }
        $job_id_chunk = array_chunk($job_id_arr, 1000, true);
        $jobid_cond = "";
        $jobid_cond1 = "";
        $jobid_cond3 = "";
        $i = 0;
        foreach ($job_id_chunk as $key => $value) {
            if ($i == 0) {
                $jobid_cond = " and b.job_id  in(" . implode(",", $value) . ")";
                $jobid_cond1 = " and a.job_id  in(" . implode(",", $value) . ")";
                $jobid_cond3 = " and job_id  in(" . implode(",", $value) . ")";
                $jobid_cond4 = " and i.job_id  in(" . implode(",", $value) . ")";
            } else {
                $jobid_cond .= " or b.job_id  in(" . implode(",", $value) . ")";
                $jobid_cond1 .= " or a.job_id  in(" . implode(",", $value) . ")";
                $jobid_cond3 .= " or job_id  in(" . implode(",", $value) . ")";
                $jobid_cond4 .= " or i.job_id  in(" . implode(",", $value) . ")";
            }
            $i++;
        }

        $rowspan = array();
        foreach ($main_data_arr as $job_id => $jod_arr) {
            foreach ($jod_arr['color_data'] as $color_data) {
                foreach ($color_data['fabric_color'] as $row) {
                    $rowspan[$job_id]++;
                }
            }
        }
        $fabric_id_str = implode(",", $fabric_id_arr);

        $wo_data_sql = sql_select("SELECT a.job_id, a.lib_yarn_count_deter_id, b.id, b.booking_date,b.booking_no, c.gmts_color_id, c.fin_fab_qnty, c.amount,	b.supplier_id, c.fabric_color_id FROM wo_pre_cost_fabric_cost_dtls a join wo_booking_dtls c on a.id=c.pre_cost_fabric_cost_dtls_id join wo_booking_mst b on b.booking_no=c.booking_no where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id in ($fabric_id_str) and c.fin_fab_qnty is not null ");
        foreach ($wo_data_sql as $row) {
            $wo_data_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]]['fin_fab_qnty'] += $row[csf('fin_fab_qnty')];
            $wo_data_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]]['amount'] += $row[csf('amount')];
            $wo_data_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]]['wo_date'][$row[csf('id')]] = $row[csf('booking_date')];
            $wo_data_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]]['booking_no'][$row[csf('id')]] = $row[csf('booking_no')];
            $wo_data_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]]['supplier'][$row[csf('id')]] = $lib_supplier_arr[$row[csf('supplier_id')]];
        }
		unset($wo_data_sql);

		$receive_qty_data = sql_select("SELECT c.po_breakdown_id as po_id,d.detarmination_id, d.color, c.quantity as order_qnty, b.order_amount,   b.order_rate	from inv_receive_master a , inv_transaction b , order_wise_pro_details c , product_details_master d   where  a.id = b.mst_id and  b.id = c.trans_id  and  d.id=c.prod_id  and  d.id=b.prod_id  and a.entry_form=17 and a.receive_basis in (1,2) and a.status_active=1 and a.is_deleted=0 and b.receive_basis in (1,2) and b.transaction_type=1 and b.status_active=1 and b.is_deleted=0 and c.trans_type=1 and c.entry_form=17 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $order_cond
		union all SELECT c.po_breakdown_id as po_id,d.detarmination_id, d.color, c.quantity as order_qnty, b.order_amount, b.order_rate from inv_receive_master a , inv_transaction b , order_wise_pro_details c , product_details_master d where a.id = b.mst_id and b.id = c.trans_id and d.id=c.prod_id and d.id=b.prod_id and a.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.transaction_type=1 and b.status_active=1 and b.is_deleted=0 and a.item_category=2 and c.trans_type=1 and c.entry_form=37 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $order_cond");	 
 
        $receive_qty_arr = array();
        foreach ($receive_qty_data as $row) {
			$job_id=$po_idArr[$row[csf('po_id')]];
            $receive_qty_arr[$job_id][$row[csf('detarmination_id')]][$row[csf('color')]]['qty'] += $row[csf('order_qnty')];
            $receive_qty_arr[$job_id][$row[csf('detarmination_id')]][$row[csf('color')]]['amount'] += $row[csf('order_amount')];
            $receive_qty_arr[$job_id][$row[csf('detarmination_id')]][$row[csf('color')]]['rate'] = $row[csf('order_rate')];
            $receive_qty_arr[$job_id][$row[csf('detarmination_id')]][$row[csf('color')]]['po_id'] = $row[csf('po_id')];
        }
		unset($receive_qty_data);

        $receive_rtn_qty_data_wvn = sql_select("SELECT d.detarmination_id, c.color_id as color, c.quantity as cons_quantity, b.cons_amount, e.job_id, e.id as po_id,d.supplier_id from inv_issue_master a join inv_transaction b on a.id = b.mst_id join order_wise_pro_details c on b.id = c.trans_id join product_details_master d on d.id=c.prod_id join wo_po_break_down e on e.id=c.po_breakdown_id where a.entry_form=202 and a.status_active=1 and a.is_deleted=0 and b.transaction_type=3 and b.status_active=1 and b.is_deleted=0 and c.trans_type=3 and c.entry_form=202 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $order_cond");

        /*union all 
		  	SELECT d.detarmination_id, c.color_id as color, c.quantity as cons_quantity, b.cons_amount, e.job_id, e.id as po_id,d.supplier_id 
		  	from inv_issue_master a join inv_transaction b on a.id = b.mst_id join order_wise_pro_details c on b.id = c.trans_id join product_details_master d on d.id=c.prod_id join wo_po_break_down e on e.id=c.po_breakdown_id where a.entry_form=18 and a.status_active=1 and a.is_deleted=0 and b.transaction_type=2 and b.status_active=1  and a.item_category=2 and b.is_deleted=0 and c.trans_type=2 and c.entry_form=18 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $order_cond"*/

        $receive_rtn_qty_arr = array();
        foreach ($receive_rtn_qty_data_wvn as $row) {
            $receive_rtn_qty_arr[$row[csf('job_id')]][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['qty'] += $row[csf('cons_quantity')];

        }
		unset($receive_rtn_qty_data_wvn);


      
		  $issue_qty_data = sql_select("SELECT d.detarmination_id, c.color_id as color, c.quantity as cons_quantity, b.cons_amount, e.job_id, e.id as po_id,d.supplier_id from inv_issue_master a join inv_transaction b on a.id = b.mst_id join order_wise_pro_details c on b.id = c.trans_id join product_details_master d on d.id=c.prod_id join wo_po_break_down e on e.id=c.po_breakdown_id where a.entry_form=19 and a.issue_basis=1 and a.status_active=1 and a.is_deleted=0 and b.transaction_type=2 and b.status_active=1 and b.is_deleted=0 and c.trans_type=2 and c.entry_form=19 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $order_cond
		  	union all 
		  	SELECT d.detarmination_id, c.color_id as color, c.quantity as cons_quantity, b.cons_amount, e.job_id, e.id as po_id,d.supplier_id 
		  	from inv_issue_master a join inv_transaction b on a.id = b.mst_id join order_wise_pro_details c on b.id = c.trans_id join product_details_master d on d.id=c.prod_id join wo_po_break_down e on e.id=c.po_breakdown_id where a.entry_form=18 and a.status_active=1 and a.is_deleted=0 and b.transaction_type=2 and b.status_active=1  and a.item_category=2 and b.is_deleted=0 and c.trans_type=2 and c.entry_form=18 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $order_cond" );

        $issue_qty_arr = array();$ttqty=0;$issue_po_arr = array();$issue_po_chk_arr = array();
        foreach ($issue_qty_data as $row) {
            $rate = $receive_qty_arr[$row[csf('job_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['rate'];
            $issue_qty_arr[$row[csf('job_id')]][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['qty'] += $row[csf('cons_quantity')];
            $issue_qty_arr[$row[csf('job_id')]][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['amount'] += $row[csf('cons_quantity')] * $rate;
             if($issue_po_chk_arr[$row[csf('job_id')]][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]!=$row[csf('po_id')])
            {
            	$issue_po_arr[$row[csf('job_id')]][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['po_id']= $row[csf('po_id')];
            	$issue_po_chk_arr[$row[csf('job_id')]][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]=$row[csf('po_id')];
            }
            $issue_qty_arr[$row[csf('job_id')]][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['supplier'] = $lib_supplier_arr[$row[csf('supplier_id')]];
			
			//$ttqty+= $row[csf('cons_quantity')];
        }
		unset($issue_qty_data);
		//echo $ttqty.'D';

		$issue_rtn_qty_arr_wvn = sql_select("SELECT c.po_breakdown_id as po_id,d.detarmination_id, d.color, c.quantity as order_qnty, b.order_amount,   b.order_rate	from inv_receive_master a , inv_transaction b , order_wise_pro_details c , product_details_master d   where  a.id = b.mst_id and  b.id = c.trans_id  and  d.id=c.prod_id  and  d.id=b.prod_id  and a.entry_form=209  and a.status_active=1 and a.is_deleted=0  and b.transaction_type=4 and b.status_active=1 and b.is_deleted=0 and c.trans_type=4 and c.entry_form=209 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $order_cond");	

		$issue_rtnQty_arr_wvn = array();
        foreach ($issue_rtn_qty_arr_wvn as $row) {
            $issue_rtnQty_arr_wvn[$job_id][$row[csf('po_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['qty'] += $row[csf('order_qnty')];
        }
		unset($issue_rtn_qty_arr_wvn);
        /*echo "<pre>";
        	print_r($issue_rtnQty_arr_wvn);
        echo "</pre>";*/

		/*union all SELECT c.po_breakdown_id as po_id,d.detarmination_id, d.color, c.quantity as order_qnty, b.order_amount, b.order_rate from inv_receive_master a , inv_transaction b , order_wise_pro_details c , product_details_master d where a.id = b.mst_id and b.id = c.trans_id and d.id=c.prod_id and d.id=b.prod_id and a.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.transaction_type=1 and b.status_active=1 and b.is_deleted=0 and a.item_category=2 and c.trans_type=1 and c.entry_form=37 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $order_cond */



        $fabric_supplier_data = sql_select("SELECT a.job_id, a.supplier_id, b.lib_yarn_count_deter_id from wo_pre_cost_fabric_supplier a join wo_pre_cost_fabric_cost_dtls b on a.JOB_ID=b.JOB_ID and b.id=a.fabric_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $jobid_cond");

        $fabric_supplier_arr = array();
        foreach ($fabric_supplier_data as $row) {
            $fabric_supplier_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('supplier_id')]] = $lib_supplier_arr[$row[csf('supplier_id')]];
        }
		unset($fabric_supplier_data);
        $pi_number_data = sql_select("SELECT f.style_ref_no,d.pi_number,f.job_no,i.job_id,c.determination_id, c.amount, c.color_id as color_id from wo_booking_dtls b left join  wo_pre_cost_fabric_cost_dtls i on   b.job_no = i.job_no,com_pi_item_details c , com_pi_master_details d ,wo_po_details_master f	where b.booking_no=c.work_order_no   and b.job_no = f.job_no and d.id=c.pi_id and b.status_active=1 and b.is_deleted=0 	and c.status_active=1 and c.is_deleted=0 and d.pi_basis_id in (1,2) and d.status_active=1 and d.is_deleted=0 and d.importer_id in ($company_name)  $jobid_cond4 and d.entry_form=166	group by f.style_ref_no,c.determination_id,d.pi_number,f.job_no ,i.job_id, c.amount, c.color_id");

        $pi_data_arr = array();
        foreach ($pi_number_data as $key => $row) {
            $pi_data_arr[$row[csf('job_id')]][$row[csf('determination_id')]][$row[csf('color_id')]]['pi_no'].= $row[csf('pi_number')].',';
            $pi_data_arr[$row[csf('job_id')]][$row[csf('determination_id')]][$row[csf('color_id')]]['amount'] += $row[csf('amount')];
            //$pi_data_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('color_id')]]['blc_no']=$row[csf('lc_number')];
        }
		unset($pi_number_data);
        $pi_number_data_lc = sql_select("SELECT f.style_ref_no,d.pi_number,g.lc_number,f.job_no,i.job_id, c.determination_id, c.amount, c.color_id as color_id from wo_booking_dtls b left join  wo_pre_cost_fabric_cost_dtls i on   b.job_no = i.job_no,com_pi_item_details c , com_pi_master_details d ,wo_po_details_master f,com_btb_lc_master_details g,com_btb_lc_pi h 	where b.booking_no=c.work_order_no and g.id=h.com_btb_lc_master_details_id and h.pi_id=d.id and b.job_no = f.job_no and d.id=c.pi_id and b.status_active=1 and b.is_deleted=0 	and c.status_active=1 and c.is_deleted=0 and g.item_basis_id in (1,2) and d.status_active=1 and d.is_deleted=0 and g.importer_id in ($company_name)  $jobid_cond4 and d.entry_form=167	group by f.style_ref_no,d.pi_number,g.lc_number,f.job_no ,i.job_id, c.determination_id, c.amount, c.color_id");
	

        foreach ($pi_number_data_lc as $key => $row) {

            $pi_data_arr[$row[csf('job_id')]][$row[csf('determination_id')]][$row[csf('color_id')]]['blc_no'].= $row[csf('lc_number')].',';
        }
        unset($pi_number_data_lc);


        /*echo '<pre>';
        print_r($pi_data_arr); die;*/
        $max_shipment_date_sql = sql_select("SELECT MAX(pub_shipment_date) as pub_shipment_date ,job_id from wo_po_break_down where  status_active=1 and is_deleted=0 $jobid_cond3 group by job_id");
        foreach ($max_shipment_date_sql as $row) {
            $max_ship_arr[$row[csf('job_id')]] = $row[csf('pub_shipment_date')];
        }

        $condition = new condition();
        if (count($job_id_arr) > 0) {
            $job_id_str = implode(",", $job_id_arr);
            $condition->jobid_in($job_id_str);
        }
        $condition->init();
        $fabric = new fabric($condition);
        $fabric_qty_arr = $fabric->getQtyArray_by_JobIdLibYarnCountDeterIdAndGmtscolor_knitAndwoven_greyAndfinish();
        /*echo "<pre>";
        	print_r($fabric_qty_arr);
        echo "</pre>";*/
        //$fabric_amount_arr=$fabric->getAmountArray_by_JobIdLibYarnCountDeterIdAndGmtscolor_knitAndwoven_greyAndfinish();
        $fabric_amount_arr = $fabric->getAmountArr_by_JobIdYarnCountIdGmtsAndFabricColor_source();
    }
	/*Trims Data Start from Here*/


		$trim_sql_qry = "SELECT a.id as job_id,a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, a.season_buyer_wise, b.id, b.po_number, a.order_uom, a.job_quantity, a.total_set_qnty, b.pub_shipment_date, d.costing_per, e.id as trim_dtla_id, e.trim_group, e.remark, e.description, e.brand_sup_ref, e.cons_uom, e.cons_dzn_gmts, e.rate, e.amount, e.apvl_req, e.nominated_supp, e.insert_date,e.sourcing_rate from wo_po_details_master a join wo_po_break_down b on a.job_no=b.job_no_mst join wo_po_color_size_breakdown c on a.id=c.job_id and b.id=c.po_break_down_id join wo_pre_cost_mst d on a.id =d.job_id join wo_pre_cost_trim_cost_dtls e on e.job_id = d.job_id $item_group_cond left join  wo_pre_cost_trim_co_cons_dtls f on c.job_id=f.job_id and c.po_break_down_id=f.po_break_down_id and f.job_id=e.job_id and e.id=f.wo_pre_cost_trim_cost_dtls_id left join tna_process_mst t on b.id=t.po_number_id and t.task_number=354 where f.cons > 0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and e.is_deleted=0 and e.status_active=1 and a.company_name=$company_name $buyer_id_cond ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $date_cond $style_ref_cond $year_cond $jobcond $jobid_cond group by a.id, a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, a.season_buyer_wise,  b.id, b.po_number, a.order_uom, a.total_set_qnty, b.pub_shipment_date,  d.costing_per, e.id, e.trim_group, e.remark, e.description, e.brand_sup_ref, e.cons_uom, e.cons_dzn_gmts, e.rate, e.amount, e.apvl_req, e.nominated_supp, e.insert_date, a.job_quantity,e.sourcing_rate order by e.id, e.trim_group";


	
	//echo $trim_sql_qry; die;
	
	$trims_sql_data= sql_select($trim_sql_qry);
    if(count($trims_sql_data) > 1) {
        $trims_main_attribute = array('job_no', 'buyer_name', 'job_no_prefix_num', 'style_ref_no', 'season_buyer_wise', 'order_uom', 'job_quantity');
        $trims_dtls_attribute = array('trim_dtla_id', 'trim_group', 'description', 'brand_sup_ref', 'cons_uom', 'cons_dzn_gmts', 'po_number', 'sourcing_rate','rate','id');
        foreach ($trims_sql_data as $row) {

            foreach ($trims_main_attribute as $attr) {
                $trims_main_data[$row[csf('job_id')]][$attr] = $row[csf($attr)];
            }
            foreach ($trims_dtls_attribute as $tattr) {
                $trims_main_data[$row[csf('job_id')]]['trims_data'][$row[csf('trim_dtla_id')]][$tattr] = $row[csf($tattr)];
            }
            $trimjob_id_arr[$row[csf('job_id')]] = $row[csf('job_id')];
            $trim_id_arr[$row[csf('trim_dtla_id')]] = $row[csf('trim_dtla_id')];
            $trim_poid_arr[$row[csf('id')]] = $row[csf('id')];
			$trim_poid_arr[$row[csf('id')]] = $row[csf('id')];
			$trimGroupArr[$row[csf('trim_group')]] = $row[csf('trim_group')];

        }
		unset($trims_sql_data);
		$trimPoids=implode(",",$trim_poid_arr);
		$trimitemids=implode(",",$trimGroupArr);
        $trimjob_id_chunk = array_chunk($trimjob_id_arr, 1000, true);
        $jobid_cond1 = "";
        $jobid_cond = "";
        $i = 0;
        foreach ($trimjob_id_chunk as $key => $value) {
            if ($i == 0) {
                $jobid_cond = " and b.job_id  in(" . implode(",", $value) . ")";
                $jobid_cond1 = " and a.job_id  in(" . implode(",", $value) . ")";
            } else {
                $jobid_cond = " or b.job_id  in(" . implode(",", $value) . ")";
                $jobid_cond1 .= " or a.job_id  in(" . implode(",", $value) . ")";
            }
            $i++;
        }

        $trim_id_chunk = array_chunk($trim_id_arr, 1000, true);
        $trimid_cond = "";
        $ji = 0;
        foreach ($trim_id_chunk as $key => $value) {
            if ($ji == 0) {
                $trimid_cond = " and a.id  in(" . implode(",", $value) . ")";
            } else {
                $trimid_cond .= " or a.id  in(" . implode(",", $value) . ")";
            }
            $ji++;
        }
	  $trim_wo_data_sql = sql_select("SELECT a.id as trim_id, a.job_id, b.id as booking_id, b.booking_date, b.booking_no, d.requirment as wo_qnty,d.amount, d.description, b.supplier_id,c.id as booking_dtls_id FROM wo_pre_cost_trim_cost_dtls a , wo_booking_dtls c  , wo_booking_mst b,wo_trim_book_con_dtls d where a.id=c.pre_cost_fabric_cost_dtls_id and d.wo_trim_booking_dtls_id=c.id and d.po_break_down_id=c.po_break_down_id and d.booking_no=b.booking_no and  b.booking_no=c.booking_no and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.booking_type=2 and c.is_workable=1 $trimid_cond ");
		 
        foreach ($trim_wo_data_sql as $row) {
            $trim_wo_data_arr[$row[csf('job_id')]][$row[csf('trim_id')]]['wo_qnty'] += $row[csf('wo_qnty')];
            $trim_wo_data_arr[$row[csf('job_id')]][$row[csf('trim_id')]]['wo_amount'] += $row[csf('amount')];
			$trim_wo_data_arr[$row[csf('job_id')]][$row[csf('trim_id')]]['description'].= $row[csf('description')].'**';
            $trim_wo_data_arr[$row[csf('job_id')]][$row[csf('trim_id')]]['wo_date'][$row[csf('booking_id')]] = $row[csf('booking_date')];
            $trim_wo_data_arr[$row[csf('job_id')]][$row[csf('trim_id')]]['booking_no'][$row[csf('booking_id')]] = $row[csf('booking_no')];
            $trim_wo_data_arr[$row[csf('job_id')]][$row[csf('trim_id')]]['supplier'][$row[csf('booking_id')]] = $lib_supplier_arr[$row[csf('supplier_id')]];
			
        }
		unset($trim_wo_data_sql);
		$trim_wo_data_sql2 = sql_select("SELECT a.id as trim_id, a.job_id, b.id as booking_id, b.booking_no,c.description,d.description booking_desc,c.trim_group,c.po_break_down_id FROM wo_pre_cost_trim_cost_dtls a join wo_booking_dtls c on a.id=c.pre_cost_fabric_cost_dtls_id join wo_booking_mst b on b.booking_no=c.booking_no join   wo_trim_book_con_dtls d on c.id=d.wo_trim_booking_dtls_id	where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.booking_type=2 and c.is_workable=1 $trimid_cond and c.po_break_down_id in ($trimPoids) and c.trim_group in ($trimitemids)	group by a.id , a.job_id, b.id , b.booking_no,c.description,d.description,c.trim_group,c.po_break_down_id");
			foreach ($trim_wo_data_sql2 as $row) {
				$trim_wo_data_arr2[$row[csf('job_id')]][$row[csf('trim_id')]][$row[csf('trim_group')]][$row[csf('booking_desc')]] = $row[csf('booking_desc')];
			}
			unset($trim_wo_data_sql2);

        $po_id_chunk = array_chunk($trim_poid_arr, 1000, true);
        $order_cond = "";
        $pi = 0;
        foreach ($po_id_chunk as $key => $value) {
            if ($pi == 0) {
                $order_cond = " and b.po_breakdown_id  in(" . implode(",", $value) . ")";
            } else {
                $order_cond .= " or b.po_breakdown_id  in(" . implode(",", $value) . ")";
            }
            $pi++;
        }

        $receive_qty_data = sql_select("SELECT b.po_breakdown_id, a.item_group_id,a.item_description, sum(b.quantity) as quantity, a.rate, e.job_id from inv_receive_master c,product_details_master d,inv_trims_entry_dtls a , order_wise_pro_details b, wo_po_break_down e where a.mst_id=c.id and a.trans_id=b.trans_id and a.id=b.dtls_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and d.status_active=1 and d.is_deleted=0 and e.id=b.po_breakdown_id $order_cond group by b.po_breakdown_id, a.item_group_id,a.item_description,a.rate, e.job_id order by a.item_group_id ");
        $trim_inhouse_qty = array();
        foreach ($receive_qty_data as $row) { 
            $trim_inhouse_qty[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['qty'] += $row[csf('quantity')];
            $trim_inhouse_qty[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['amount'] += $row[csf('quantity')] * $row[csf('rate')];
            $trim_inhouse_qty[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['rate'] = $row[csf('rate')];
            $trim_inhouse_qty[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['po_id'] = $row[csf('po_breakdown_id')];
            $trims_po_id_arr[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
        }
		unset($receive_qty_data);
		$receive_rtn_qty_data=sql_select("select e.job_id,b.po_breakdown_id, c.item_group_id,c.item_description, b.quantity as quantity, b.order_rate as rate
			from product_details_master c,order_wise_pro_details b,wo_po_break_down e 
			where b.prod_id=c.id  and e.id=b.po_breakdown_id and b.trans_type=3 and b.entry_form=49 and b.status_active=1 and b.is_deleted=0 $order_cond");
			foreach($receive_rtn_qty_data as $row)
			{
				 $receive_rtnArr[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['qty'] += $row[csf('quantity')];
				 $receive_rtnArr[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['amount'] += $row[csf('quantity')]* $row[csf('rate')];
			}
			//echo "<pre>";print_r($style_data_arr);
			unset($receive_rtn_qty_data);

        $trim_issue_qty_data = sql_select("SELECT b.po_breakdown_id, a.item_group_id,p.item_description as item_description, sum(b.quantity) as quantity, a.rate, e.job_id  from  inv_issue_master d,product_details_master p,inv_trims_issue_dtls a , order_wise_pro_details b, wo_po_break_down e where a.mst_id=d.id and a.prod_id=p.id and a.trans_id=b.trans_id and b.trans_type=2 and b.entry_form=25 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and p.status_active=1 and p.is_deleted=0 and e.id=b.po_breakdown_id  $order_cond group by b.po_breakdown_id, a.item_group_id,p.item_description, e.job_id,a.rate");
		//echo "SELECT b.po_breakdown_id, a.item_group_id,p.item_description as item_description, sum(b.quantity) as quantity, a.rate, e.job_id  from  inv_issue_master d,product_details_master p,inv_trims_issue_dtls a , order_wise_pro_details b, wo_po_break_down e where a.mst_id=d.id and a.prod_id=p.id and a.trans_id=b.trans_id and b.trans_type=2 and b.entry_form=25 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.id=b.po_breakdown_id  $order_cond group by b.po_breakdown_id, a.item_group_id,p.item_description, e.job_id,a.rate";
		
        $trim_issue_qty = array();
        foreach ($trim_issue_qty_data as $row) {
            $rate = $trim_inhouse_qty[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['rate'];
            $trim_issue_qty[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['qty'] += $row[csf('quantity')];
            $trim_issue_qty[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['amount'] += $row[csf('quantity')] * $rate;
            $trim_issue_qty[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['po_id'] = $row[csf('po_breakdown_id')];
        }
		unset($trim_issue_qty_data);
		$issue_rtn_qty_data = sql_select("SELECT b.po_breakdown_id, d.item_group_id,d.item_description, (b.quantity) as quantity,  e.job_id from inv_receive_master c,inv_transaction a, order_wise_pro_details b, product_details_master d, wo_po_break_down e where c.id=a.mst_id and a.id=b.trans_id and a.prod_id=b.prod_id and b.prod_id=d.id and a.prod_id=d.id and e.id=b.po_breakdown_id $order_cond and b.trans_type=4 and b.entry_form=73 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and d.status_active=1 and d.is_deleted=0 order by d.item_group_id");

        $trim_issue_rtn_qty = array();
        foreach ($issue_rtn_qty_data as $row) {
        	$trim_issue_rtn_qty[$row[csf('job_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['qty'] += $row[csf('quantity')];
        }
        unset($issue_rtn_qty_data);
		
        $trim_supplier_data = sql_select("SELECT a.job_id, a.supplier_id, b.id as trim_cost_id from wo_pre_cost_trim_supplier a join wo_pre_cost_trim_cost_dtls b on a.job_id=b.job_id and b.id=a.trimid where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $jobid_cond");

        $trim_supplier_arr = array();
        foreach ($trim_supplier_data as $row) {
            $trim_supplier_arr[$row[csf('job_id')]][$row[csf('trim_cost_id')]][$row[csf('supplier_id')]] = $lib_supplier_arr[$row[csf('supplier_id')]];
        }
		unset($trim_supplier_data);

		$trims_pi_data=sql_select("SELECT a.id, a.lc_number,a.lc_type_id,a.payterm_id,a.tenor, c.pi_number FROM com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_master_details c WHERE a.id=b.com_btb_lc_master_details_id and b.pi_id=c.id and a.is_deleted = 0 and a.item_basis_id=1 and a.importer_id=$company_name group by a.id, a.lc_number,a.lc_type_id,a.payterm_id,a.tenor,c.pi_number UNION ALL SELECT a.id, a.lc_number,a.lc_type_id,a.payterm_id,a.tenor,c.pi_number FROM com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_master_details c WHERE a.id=b.com_btb_lc_master_details_id and b.pi_id=c.id and a.is_deleted = 0 and a.item_basis_id=2 and a.importer_id=$company_name group by a.id, a.lc_number,a.lc_type_id,a.payterm_id,a.tenor, c.pi_number order by id desc ");
			$trim_pi_lc_data_arr = array();$fab_pi_lc_data_arr = array();
			foreach ($trims_pi_data as $key => $row) {
				$trim_pi_lc_data_arr[$row[csf('pi_number')]]['lc_number']= $row[csf('lc_number')];;
				$fab_pi_lc_data_arr[$row[csf('pi_number')]]['lc_number']= $row[csf('lc_number')];
			}
			unset($trims_pi_data);

        $trims_pi_number_data = sql_select("SELECT a.job_id,d.pi_number, c.amount, e.lc_number, c.item_group, b.pre_cost_fabric_cost_dtls_id, c.item_color, c.item_size from wo_pre_cost_trim_cost_dtls a join wo_booking_dtls b on a.id=b.pre_cost_fabric_cost_dtls_id join com_pi_item_details c on b.booking_no=c.work_order_no  join com_pi_master_details d on d.id=c.pi_id left join com_btb_lc_master_details e on TO_CHAR(c.pi_id)=e.pi_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.entry_form=167 $jobid_cond1 and b.is_workable=1 group by a.job_id, d.pi_number, c.amount, e.lc_number, c.item_group, b.pre_cost_fabric_cost_dtls_id, c.item_color, c.item_size");
        $trim_pi_data_arr = array();
        foreach ($trims_pi_number_data as $key => $row) {
            $trim_pi_data_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('job_id')]][$row[csf('item_group')]]['pi_no'].= $row[csf('pi_number')].',';
            $trim_pi_data_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('job_id')]][$row[csf('item_group')]]['amount'] += $row[csf('amount')];
            $trim_pi_data_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('job_id')]][$row[csf('item_group')]]['blc_no'].= $row[csf('lc_number')].',';
        }
		unset($trims_pi_number_data);
        if (count($trimjob_id_arr) > 0) {
            $trimjob_id_str = implode(",", $trimjob_id_arr);
            $condition->jobid_in($trimjob_id_str);
        }
        $condition->init();
        $trim = new trims($condition);
        $trim_group_qty_arr = $trim->getQtyArray_by_jobAndPrecostdtlsid();

        //$trim_group_amount_arr=$trim->getAmountArray_by_jobAndPrecostdtlsid();

        $trim_amountSourcing_arr = $trim->getAmountArray_precostdtlsidSourcing();

        $partial_fabric_report_type = array(84 => 'show_fabric_booking_report_urmi_per_job', 85 => 'print_booking_3', 143 => 'show_fabric_booking_report_urmi', 151 => 'show_fabric_booking_report_advance_attire_ltd', 160 => 'print_booking_5', 175 => 'print_booking_6', 155 => 'fabric_booking_report', 235 => 'print_9', 191 => 'print_booking_7');

        $print_report_format = return_field_value("format_id", " lib_report_template", "template_name ='" . $company_name . "'  and module_id=2 and report_id=35 and is_deleted=0 and status_active=1");
        $print_report_format_arr = explode(",", $print_report_format);
        $fabric_report_first_id = $print_report_format_arr[0];
    }
	ob_start();
	?>
	<div style="width:2000px">
		<table width="2560">
	        <tr class="form_caption"><td colspan="18" align="center"><? echo $report_title; ?></td></tr>
	        <tr class="form_caption"><td colspan="18" align="center"><? echo $company_library[$company_name]; ?></td></tr>
		</table>
        <?
        if(count($main_data_sql) > 0)
		 {
        ?>
		<table class="rpt_table" width="2560" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
					<tr>
						<td colspan="25" align="left"><strong>Fabric Details</strong></td>
					</tr>
					<tr>
					<th width="30">SL</th>
					<th width="100">Buyer</th>
					<th width="100">Job No</th>
					<th width="80">Season</th>
					<th width="130">Style Ref</th>
					<th width="100">Order Qty</th>
					<th width="50">UOM</th>
					<th width="60">Max.Ship Date</th>
					<th width="200">Item Description</th>
					<th width="100">Fabric Color</th>
					<th width="100">Garments Color</th>
					<th width="80">WO Date</th>
					<th width="60">Avg. Cons</th>
					<th width="60">Rate</th>
					<th width="80">Req Qty</th>					
					<th width="80">WO Qty</th>
					<th width="80">WO NO.</th>
					<th width="50">UOM</th>
					<th width="80">In-House Qty</th>
					<th width="80">Receive Balance</th>
					<th width="80">Issue to Cutting</th>
					<th width="80">Issue Balance</th>
					<th width="100">Supplier</th>
					<th width="120">PI No.</th>
					<th width="100">BTB LC N0</th>
				</tr>
			</thead>
			<tbody>
				<? $sl=1;  
				/*echo '<pre>';
				print_r($main_data_arr); die;*/
				foreach ($main_data_arr as $job_id => $job_data) {
					if($sl%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";					
					//if($i==1){ ?>
					<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $sl; ?>">
					<td rowspan="<?= $rowspan[$job_id]; ?>" width="30" align="center"><?= $sl; ?></td>
					<td rowspan="<?= $rowspan[$job_id]; ?>" width="80"  align="left"><?= $buyer_short_name_library[$job_data['buyer_name']]; ?></td>
					<td rowspan="<?= $rowspan[$job_id]; ?>" width="80"  align="left"><?= $job_data['job_no']; ?></td>
					<td rowspan="<?= $rowspan[$job_id]; ?>" align="left"><?= $lib_season_arr[$job_data['season_buyer_wise']]; ?></td>
					<td rowspan="<?= $rowspan[$job_id]; ?>"><p>
					<a href='#report_details' onClick="order_summery_popup('<? echo $job_id; ?>' ,'order_summery_data');"><?= $job_data['style_ref_no']; ?></a></p></td>
					<td rowspan="<?= $rowspan[$job_id]; ?>"  align="right"><p>
					<a href='#report_details' onClick="order_summery_popup('<? echo $job_id; ?>' ,'order_qty_data2');"><?= $job_data['job_quantity']; ?></a></p></td>
					<td rowspan="<?= $rowspan[$job_id]; ?>"><?= $unit_of_measurement[$job_data['order_uom']]; ?></td>
					<td rowspan="<?= $rowspan[$job_id]; ?>"><?= $max_ship_arr[$job_id];  ?></td>
					<?	
					$i=1;			
					foreach ($job_data['color_data'] as $lib_yarn_id=>$color_data) {
						// if($i!=1) echo '<tr>';
						if($i!=1) echo '<tr onclick="change_color(\'tr_'.$i.'\',\''.$bgcolor.'\')" id="tr_'.$i.'">';
							
							 ?>
							<td rowspan="<?= count($color_data['fabric_color']) ?>"><?= $color_data['item_des'] ?></td>
						<?						
						$k=1;
						foreach ($color_data['fabric_color'] as $fcolor_id=>$fcolor_data) {
							// if($k!=1) echo '<tr>';
							if($k!=1) echo '<tr onclick="change_color(\'tr_'.$i.'\',\''.$bgcolor.'\')" id="tr_'.$i.'">';

							$gmts_color=array();
							$gmts_color_id=array();
							$trims_inhouse_qty_chk=array();
							$bom_qty=0;$bom_qty_finish=0;	$wo_qty=0; $bom_value=0; $wo_amount=0; $pi_amount=0;$issueqty=$inhouseqty=0;
							foreach ($fcolor_data as $gcolor_id => $row) {
								$fabric_color_id=$row['color'];
								$po_ids=rtrim($row['po_id'],',');
								$po_idsArr=array_unique(explode(",",$po_ids));
								$issuepo=implode(",",array_unique(explode(",",$po_ids)));
								$color_id = $gcolor_id;
								$gmts_color[$gcolor_id]=$color_arr[$gcolor_id];
								$gmts_color_id[$gcolor_id]=$gcolor_id;
								$wo_qty += $wo_data_arr[$job_id][$lib_yarn_id][$color_id][$fabric_color_id]['fin_fab_qnty'];
								foreach($po_idsArr as $pid)
								{
									$issueqty+= $issue_qty_arr[$job_id][$pid][$lib_yarn_id][$fabric_color_id]['qty']-$issue_rtnQty_arr_wvn[$job_id][$pid][$lib_yarn_id][$fabric_color_id]['qty'];
									//$issuepo= $issue_qty_arr[$job_id][$pid][$lib_yarn_id][$fabric_color_id]['po_id'];
									$supplier= $issue_qty_arr[$job_id][$pid][$lib_yarn_id][$fabric_color_id]['supplier'];
									//echo $issueqty.'='.$fabric_color_id.'='.$pid.'<br>'; 
									//echo $job_id.'='.$pid.'='.$lib_yarn_id.'='.$fabric_color_id.'<br/>';
								}
								
								
								$bom_value+= $fabric_amount_arr['woven']['grey'][$job_id][$lib_yarn_id][$color_id][$fabric_color_id];
								
								//echo $job_id.'='.$lib_yarn_id.'='.$color_id.'='.$fabric_color_id.'<br/>';
								$bom_qty_finish +=$fabric_qty_arr['knit']['grey'][$job_id][$lib_yarn_id][$color_id][$fabric_color_id];							
								$bom_qty +=$fabric_qty_arr['woven']['grey'][$job_id][$lib_yarn_id][$color_id][$fabric_color_id];												

								if($trims_inhouse_qty_chk[$job_id.'*'.$lib_yarn_id.'*'.$fabric_color_id]==''){
									$trims_inhouse_qty_chk[$job_id.'*'.$lib_yarn_id.'*'.$fabric_color_id]=$job_id.'*'.$pid.'*'.$lib_yarn_id.'*'.$fabric_color_id;
									$inhouseqty+= $receive_qty_arr[$job_id][$lib_yarn_id][$fabric_color_id]['qty']-$receive_rtn_qty_arr[$job_id][$pid][$lib_yarn_id][$fabric_color_id]['qty'];
								}
								
								$inhousepo=$receive_qty_arr[$job_id][$lib_yarn_id][$fabric_color_id]['po_id'];
								//echo $inhouseqty.'Dx';
								$rcv_balance=$wo_qty-$inhouseqty;
								
								$rcv_rate=$receive_qty_arr[$job_id][$lib_yarn_id][$fabric_color_id]['rate'];
							}	
							$issue_balance=$inhouseqty-$issueqty;
													
							$pi_no=rtrim($pi_data_arr[$job_id][$lib_yarn_id][$fabric_color_id]['pi_no'],',');
							$pi_nos=implode(", ",array_unique(explode(",",$pi_no)));
							$blc_no=rtrim($pi_data_arr[$job_id][$lib_yarn_id][$fabric_color_id]['blc_no'],',');
							$blc_nos=implode(", ",array_unique(explode(",",$blc_no)));
						 	?>
							<td align="left"><?= $color_arr[$fcolor_id] ?></td>
							<td align="left"><?= implode(", ", $gmts_color) ?></td>
							<td align="left"><?= implode(",",$wo_data_arr[$job_id][$lib_yarn_id][$color_id][$fabric_color_id]['wo_date'] )  ?></td>
							<td align="right"><?= fn_number_format($row['avg_cons'],4) ?></td>
							<td align="right" title="Avg Rate<?=$bom_value;?>"><?=$row['sourcing_rate'] ;//fn_number_format($bom_value/$bom_qty,4) ?></td>
							
							<td align="right"><a href='#report_details' onClick="order_req_qty_popup('<? echo $job_id; ?>','<? echo $lib_yarn_id; ?>','<? echo $fabric_color_id; ?>' ,'order_req_qty_data');">

								<? 
								if($row['fab_nature_id']==2){echo fn_number_format($bom_qty_finish,2);}else{echo  fn_number_format($bom_qty,2);}
								 ?>
									
								</a></td>

							<td align="right"><a href='#report_details' onClick="order_wo_qty_popup('<? echo $job_id; ?>','<? echo $lib_yarn_id; ?>','<? echo $fabric_color_id; ?>','<? echo implode(",", $gmts_color_id) ?>' ,'order_wo_qty_data');"><?= fn_number_format($wo_qty,2)  ?></a></td>
							<td align="left"><?= implode(",",$wo_data_arr[$job_id][$lib_yarn_id][$color_id][$fabric_color_id]['booking_no'] )  ?></td>
							<td align="left"><?= $unit_of_measurement[$row['fabric_uom']] ?></td>
							
							<td align="right"><a href='#report_details' onClick="openmypage_inhouse_btn3('<? echo $job_id; ?>','<? echo $lib_yarn_id; ?>','<? echo $inhousepo; ?>','<? echo $fabric_color_id ?>' ,'booking_inhouse_info_btn3');"><?= fn_number_format($inhouseqty,2) ?></a></td>

							<td align="right"><?= fn_number_format($rcv_balance,2)  ?></td>
							<td align="right"><? if($issueqty>0){ ?><a href='#report_details' onClick="openmypage_issue_btn3('<? echo $job_id; ?>','<? echo $lib_yarn_id; ?>','<? echo $issuepo; ?>','<? echo $fabric_color_id?>', '<?echo $rcv_rate?>' ,'booking_issue_info_btn3');"><?= fn_number_format($issueqty,2)  ?></a><? } else echo '0.00'; ?></td>
							<td align="right"><?= fn_number_format($issue_balance,2) ?></td>
							<td align="left"><?= implode(",",$wo_data_arr[$job_id][$lib_yarn_id][$color_id][$fabric_color_id]['supplier']); ?></td>
							<td align="left"><p> &nbsp; <?=$pi_nos; ?></p></td>
							<td align="left"><p>&nbsp;<?=$fab_pi_lc_data_arr[$pi_nos]['lc_number'];//$blc_nos; ?></p></td>
							<?
							$k++;
							$i++;
						 }
						 
							
						} ?>
							</tr>
						<?
						$sl++;
						}
				?>
				
			</tbody>
		</table>
        <?
        }
        if(count($trims_sql_data) > 1) {
        ?>
		<table class="rpt_table" width="2580" cellpadding="0" cellspacing="0" border="1" rules="all" style="margin-top: 10px">
			<thead>
				<tr>
					<td colspan="24" align="left"><strong>Trims Details</strong></td>
				</tr>
				<tr>
					<th width="35">SL</th>
					<th width="95">Buyer</th>
					<th width="95">Job No</th>
					<th width="95">Season</th>
	                <th width="95">Style Ref</th>
					<th width="60">Order Qnty</th>
					<th width="50">UOM</th>
					<th width="60">Max.Ship Date</th>
					<th width="100">Trims Name</th>
					<th width="150">Item Description</th>
					<th width="100">Brand/Sup Ref</th>
					<th width="80">WO Date</th>
					<th width="80">Avg. Cons</th>
					<th width="80">Rate</th>
					<th width="100">Req Qnty</th>
					<th width="90">WO Qty</th>
					<th width="90">WO NO.</th>
	                <th width="50">UOM</th>
	                <th width="80">In-House Qty</th>
	                <th width="80">Receive Balance</th>
	                <th width="70">Issue to Prod.</th>
					<th width="90">Issue Balance</th>
	                <th width="90">Supplier</th>
					<th width="120">PI No.</th>
					<th width="90">BTB LC N0</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$tsl=1;
				?>
				<?php foreach ($trims_main_data as $job_id=>$value){
					$rowspan= count($value['trims_data']);
					if($tsl%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				 ?>
					<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr2_<? echo $tsl; ?>','<? echo $bgcolor;?>')" id="tr2_<? echo $tsl; ?>">
						<td width="35" rowspan="<?= $rowspan ?>"><?= $tsl; ?></td>
						<td width="95" rowspan="<?= $rowspan ?>"align="left"><?= $buyer_short_name_library[$value['buyer_name']]; ?></td>
						<td width="95" rowspan="<?= $rowspan ?>"align="left"><?= $value['job_no']; ?></td>
						<td width="95" rowspan="<?= $rowspan ?>"align="left"><?= $lib_season_arr[$value['season_buyer_wise']] ?></td>
		                <td width="95" rowspan="<?= $rowspan ?>" align="left">
					<a href='#report_details' onClick="order_summery_popup('<? echo $job_id; ?>' ,'order_summery_data');"><?= $value['style_ref_no']; ?></a></td>
						<td width="60"  align="right" rowspan="<?= $rowspan ?>"><a href='#report_details' onClick="order_summery_popup('<? echo $job_id; ?>' ,'order_qty_data2');"><?= $value['job_quantity']; ?></a></td>
						<td width="50" rowspan="<?= $rowspan ?>" align="left"><?= $unit_of_measurement[$value['order_uom']]; ?></th>
						<td width="60" rowspan="<?= $rowspan ?>" align="left"><?= $max_ship_arr[$job_id];  ?></td>
						<?

						$z=1;
						foreach ($value['trims_data'] as $trims_id=>$row) {
							// if($z!=1) echo '<tr>';
							
							if($z!=1) echo '<tr onclick="change_color(\'trs_'.$z.'\',\''.$bgcolor.'\')" id="trs_'.$z.'">';
							
							$req_qty = $trim_group_qty_arr[$value['job_no']][$trims_id];
							$wo_qty= $trim_wo_data_arr[$job_id][$trims_id]['wo_qnty'];
							
							$description= rtrim($trim_wo_data_arr[$job_id][$trims_id]['description'],'**');
							$descriptions=implode("__",array_unique(explode("**",$description)));
							$descriptionsArr=array_unique(explode("**",$description));
							
							$inhouse_qty=0;$tot_issue_qty=0;$receive_rtnQty=0;$issue_rtnQty=0;
							foreach($descriptionsArr as $desc) 
							{
								$inhouse_qty+=$trim_inhouse_qty[$job_id][$row['trim_group']][$desc]['qty'];
								$tot_issue_qty += $trim_issue_qty[$job_id][$row['trim_group']][$desc]['qty'];
								$po_id= $trim_inhouse_qty[$job_id][$row['trim_group']][$desc]['po_id'];
								$tpo_id= $trim_issue_qty[$job_id][$row['trim_group']][$desc]['po_id'];
								$receive_rtnQty+=$receive_rtnArr[$job_id][$row['trim_group']][$desc]['qty'];
								$issue_rtnQty+=$trim_issue_rtn_qty[$job_id][$row['trim_group']][$desc]['qty'];
							}
							//echo $receive_rtnQty.'D';
							 $issue_qty=$tot_issue_qty-$issue_rtnQty;
							 
						//	if($row['description']!='') $desc=$row['description'];else $desc=0;
							// $inhouse_qty= $trim_inhouse_qty[$job_id][$row['trim_group']][$desc]['qty'];
							// $issue_qty= $trim_issue_qty[$job_id][$row['trim_group']][$desc]['qty'];
							
						
							$trims_poid=implode(",",$trims_po_id_arr[$job_id][$row['trim_group']]);
							//echo $row['description'].'D'.$trims_poid.',';
							$trim_pi_no=rtrim($trim_pi_data_arr[$trims_id][$job_id][$row['trim_group']]['pi_no'],',');
							$trim_pi_nos=implode(", ",array_unique(explode(",",$trim_pi_no)));
							
							$trim_blc_no=rtrim($trim_pi_data_arr[$trims_id][$job_id][$row['trim_group']]['blc_no'],',');
							$trim_blc_nos=implode(", ",array_unique(explode(",",$trim_blc_no)));

							//  print_r($trim_wo_data_arr2[$job_id][$trims_id][$row['trim_group']][$row['description']]);
							//$inhouse_qty=0;$issue_qty=0;
							foreach($trim_wo_data_arr2[$job_id][$row['id']][$trims_id][$row['trim_group']] as $bdesc){
								//$inhouse_qty+=$trim_inhouse_qty[$job_id][$row['trim_group']][$bdesc]['qty'];
								//$issue_qty += $trim_issue_qty[$job_id][$row['trim_group']][$bdesc]['qty'];
								//$bDesc=$bdesc;
							}
							$inhouse_qty=$inhouse_qty-$receive_rtnQty;
							$trimrcv_balance = $wo_qty-$inhouse_qty;
							
						?>
						<td width="100" align="left"><?= $item_library[$row['trim_group']]; ?></td>
						<td width="150" align="left"><?= $descriptions; ?></td>
						<td width="100" align="left"><?= $row['brand_sup_ref']; ?></td>
						<td width="80" align="left"><?= implode(",", $trim_wo_data_arr[$job_id][$trims_id]['wo_date'])?></td>
						<td width="80" align="right"><?= fn_number_format($row['cons_dzn_gmts'],4); ?></td>
						<td width="80" align="right"><?= fn_number_format($row['rate'],4); ?></td>
						<td width="100" align="right"><a href='#report_details' onClick="trim_req_qty_popup('<? echo $job_id; ?>','<? echo $trims_id; ?>' ,'trim_req_qty_data');"><?= fn_number_format($req_qty,2); ?></a></td>
						<td width="90" align="right"><a href='#report_details' onClick="trim_wo_qty_popup('<? echo $job_id; ?>','<? echo $trims_id; ?>' ,'trim_wo_qty_data');"><?= fn_number_format($wo_qty,2) ?></a></td>
						<td width="50" align="left"><?= implode(",", $trim_wo_data_arr[$job_id][$trims_id]['booking_no'])?></td>
		                <td width="50" align="right"><?= $unit_of_measurement[$row['cons_uom']]; ?></td>	                
		                <td width="80" align="right" title="Recv Return=<? echo $receive_rtnQty.', =Des'.$descriptions;?>"><a href='#report_details' onClick="openmypage_trim_inhouse('<? echo $trims_poid;  ?>',<?= $row['trim_group']?>,'trim_booking_inhouse_info2');"><?= fn_number_format($inhouse_qty,2);  ?></a></td>
		                <td width="80" align="right"><?= fn_number_format($trimrcv_balance,2); ?></td>
		                <td width="70" align="right"><a href='#report_details' onClick="openmypage_trim_issue('<? echo $trims_poid; ?>','<? echo $row['trim_group'].'__'.$descriptions; ?>','trim_booking_issue_info');"><?= fn_number_format($issue_qty,2);  ?></a></td>
						<td width="90" align="right"><?= fn_number_format($inhouse_qty-$issue_qty,2); ?></td>
		                <td width="90" align="left"><?=implode(",", $trim_wo_data_arr[$job_id][$trims_id]['supplier'])?></td>
						<td width="120" align="left"><p> &nbsp; <?=$trim_pi_nos;?></p></td>
						<td width="90" title="<?= $trims_id.'='.$job_id.'='.$row['trim_group'];?>" align="left"><p>&nbsp;<?=$trim_pi_lc_data_arr[$trim_pi_nos]['lc_number'];//$trim_blc_nos;?></p></td>
						<? $z++;
						 } ?>
					</tr>
				<?php 
					$tsl++;
					} 
				?>								
			</tbody>
		</table>
        <?
        }
        ?>
		<div style="width:2660px; max-height:400px; overflow-y:scroll" id="scroll_body">
		</div>
	</div>
	<?
		$total_data = ob_get_contents();
		ob_clean();
		foreach (glob("*.xls") as $filename) {
		@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename=$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc,$total_data);
		echo "$total_data****$filename";
		exit();
}




if ($action=="job_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data);

  ?>
    <script>
	var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();

	function toggle( x, origColor ) {
		var newColor = 'yellow';
		if ( x.style ) {
			x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}

	function check_all_data()
	{
		var row_num=$('#list_view tr').length-1;
		for(var i=1;  i<=row_num;  i++)
		{
			if($("#tr_"+i).css("display") != "none")
			{
				$("#tr_"+i).click();
			}
		}
	}

	function js_set_value(id)
	{
		var str=id.split("_");
		toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
		var strdt=str[2];
		str=str[1];

		if( jQuery.inArray(  str , selected_id ) == -1 ) {
			selected_id.push( str );
			selected_name.push( strdt );
		}
		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == str  ) break;
			}
			selected_id.splice( i, 1 );
			selected_name.splice( i,1 );
		}
		var id = '';
		var ddd='';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
			ddd += selected_name[i] + ',';
		}
		id = id.substr( 0, id.length - 1 );
		ddd = ddd.substr( 0, ddd.length - 1 );
		$('#txt_job_id').val( id );
		$('#txt_job_val').val( ddd );
	}

	</script>
     <input type="hidden" id="txt_job_id" />
     <input type="hidden" id="txt_job_val" />
     <?
	// echo $data[0];
	 if ($data[0]==0) $company_name=""; else $company_name=" and a.company_name='$data[0]'";
	 if ($data[1]==0) $buyer_name=""; else $buyer_name=" and a.buyer_name='$data[1]'";
	 if ($data[2]=="") $job_num=""; else $job_num=" and a.job_no_prefix_num='$data[2]'";
	if($db_type==0)
	{
		if(str_replace("'","",$data[3])!=0) $year_cond=" and year(a.insert_date)=".str_replace("'","",$data[3]).""; else $year_cond="";
	}
	else
	{
		if(str_replace("'","",$data[3])!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=".str_replace("'","",$data[3]).""; else $year_cond="";
	}

	$order_type=str_replace("'","",$data[4]);
	
	$sql = "select a.id,a.style_ref_no,a.buyer_name,a.job_no,a.season_buyer_wise,a.job_no_prefix_num,$select_date as job_year from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst $company_name $buyer_name and a.is_deleted=0 and b.is_deleted=0 group by a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num,a.buyer_name,a.season_buyer_wise,a.insert_date order by a.id DESC";



	echo  create_list_view("list_view", "Style Ref No,Job No,Year", "100,100,80","450","360",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "0,0,0", "" , "style_ref_no,job_no_prefix_num,job_year", "",'setFilterGrid("list_view",-1);','0,0,0,0','',1) ;
	exit();
}




if($action=="order_summery_data")
{
	echo load_html_head_contents("Job Color Size Wise Summery", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$company_name_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$color_library=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$size_library=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	$nameArray_size=sql_select( "SELECT  size_number_id,min(id) as id,	min(size_order) as size_order from wo_po_color_size_breakdown where job_id in(".$job_id.") and	is_deleted=0 and status_active=1 group by size_number_id order by size_order");

	$nameArray_color=sql_select( "select  color_number_id,min(id) as id,min(color_order) as color_order from wo_po_color_size_breakdown where  job_id in(".$job_id.") and is_deleted=0 and status_active=1 group by color_number_id  order by color_order");
	$job_data = sql_select("SELECT a.company_name, a.job_no, a.job_quantity, a.buyer_name, a.style_ref_no, a.product_dept, b.gmts_item_id from wo_po_details_master a join wo_po_details_mas_set_details b on a.id=b.job_id where a.status_active=1 and a.is_deleted=0 and a.id=$job_id");
	$job_attribute=array('company_name', 'job_no', 'job_quantity', 'buyer_name', 'style_ref_no', 'product_dept');
	foreach ($job_data as $row) {
		foreach ($job_attribute as $attr) {
			$$attr= $row[csf($attr)];
		}
		$item_arr[$row[csf('gmts_item_id')]] = $garments_item[$row[csf('gmts_item_id')]];
		
	}
	//echo $job_quantity; die;
	?>
	<fieldset style="width:770px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="750" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<td colspan="<? echo count($nameArray_size)+2 ?>"><strong>Company:</strong> <?= $company_name_library[$company_name]?>    <strong>Job No:</strong> <?= $job_no ?>   <strong>Job Qnty:</strong> <?= $job_quantity ?>(Pcs)   <strong>Buyer:</strong>  <?= $buyer_short_name_library[$buyer_name] ;?>  <strong>Style Ref:</strong>  <?= $style_ref_no ?>    <strong>Prod. Dept:</strong> <? $product_dept[$product_dept] ?>    <strong>Item Name:</strong> <?echo implode(",", $item_arr) ?></td>
					</tr>
					<tr>
	                    <th width="100">Color</th>	                    
	                    <?  				
						foreach($nameArray_size  as $result_size)
                        { ?>
                        <th align="center" width="60"><? echo $size_library[$result_size[csf('size_number_id')]];?></th>
                    	<?	}  ?>
                    	<th width="100">Color Total Qnty</th>	
                    </tr>
				</thead>
                <tbody>
               		<?
					foreach($nameArray_color as $result_color)
                    { ?>
                		<tr>
                        <td align="center"><? echo $color_library[$result_color[csf('color_number_id')]];  ?></td>
                        <? 
                        $color_total_order=0;
                        foreach($nameArray_size  as $result_size)
						{
							$nameArray_color_size_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as  order_quantity  from wo_po_color_size_breakdown where  job_id in(".$job_id.") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$result_color[csf('color_number_id')]." and  status_active=1 and is_deleted =0");

							foreach($nameArray_color_size_qnty as $result_color_size_qnty)
                        	{ ?>
                        		<td align="right">
							<? 
								if($result_color_size_qnty[csf('plan_cut_qnty')]!= "")
								{
									 echo fn_number_format($result_color_size_qnty[csf('order_quantity')],0);
									 $color_total += $result_color_size_qnty[csf('plan_cut_qnty')] ;
									 $color_total_order += $result_color_size_qnty[csf('order_quantity')] ;
									 $item_grand_total+=$result_color_size_qnty[csf('plan_cut_qnty')];
									 $item_grand_total_order+=$result_color_size_qnty[csf('order_quantity')];
								     $grand_total +=$result_color_size_qnty[csf('plan_cut_qnty')];
									 $grand_total_order +=$result_color_size_qnty[csf('order_quantity')];
									 
									 $color_size_qnty_array[$result_size[csf('size_number_id')]][$result_color[csf('color_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')];
									 $color_size_order_qnty_array[$result_size[csf('size_number_id')]][$result_color[csf('color_number_id')]]=$result_color_size_qnty[csf('order_quantity')];
									 if (array_key_exists($result_size[csf('size_number_id')], $size_tatal))
									 {
										$size_tatal[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('plan_cut_qnty')];
										$size_tatal_order[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('order_quantity')];
									 }
									 else
									 {
										$size_tatal[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')]; 
										$size_tatal_order[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('order_quantity')]; 
									 }
									 if (array_key_exists($result_size[csf('size_number_id')], $item_size_tatal))
									 {
										$item_size_tatal[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('plan_cut_qnty')];
										$item_size_tatal_order[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('order_quantity')];
									 }
									 else
									 {
										$item_size_tatal[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')]; 
										$item_size_tatal_order[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('order_quantity')]; 
									 }
								}
								else echo " ";
							 ?>
							</td>
                        	<? }
						 ?>
						<? } ?>
						<td align="right"><? if(round($color_total_order)>0){ echo fn_number_format(round($color_total_order),0);} ?></td>
						</tr>
                    <? }					
                    ?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td align="right">Total</td>
                    	<?
                    	foreach($nameArray_size  as $result_size)
                        { ?>
                        <td><? echo $item_size_tatal_order[$result_size[csf('size_number_id')]];  ?></td>
                    	<?	}  ?>
                    	<td align="right"><?  if(round($item_grand_total_order)>0){ echo fn_number_format(round($item_grand_total_order),0); } ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}
if($action=="order_req_qty_data")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$buyer_short_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$country_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name");
	$po_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number");
	$main_data_sql=sql_select("SELECT e.id as fabric_cost_id, e.lib_yarn_count_deter_id, e.fabric_description, e.uom as fabric_uom, sum(e.avg_cons) as total_cons from wo_pre_cost_mst d  join wo_pre_cost_fabric_cost_dtls e on d.job_id=e.job_id where e.status_active=1 and e.is_deleted=0 and d.job_id=$job_id and e.lib_yarn_count_deter_id=$yarn_id group by e.lib_yarn_count_deter_id, e.fabric_description, e.uom, e.id");

	$condition= new condition();
	$condition->jobid_in($job_id);
	$condition->init();
	$fabric= new fabric($condition);
	$fabric_qty_arr=$fabric->getQtyArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
	foreach ($main_data_sql as $row) {
		$description = $row[csf('fabric_description')];
		$total_cons += $row[csf('total_cons')];
		$uom = $row[csf('fabric_uom')];
		$req_qty += $fabric_qty_arr['woven']['grey'][$row[csf('fabric_cost_id')]][$row[csf('fabric_uom')]];
	}


	?>
	<fieldset style="width:570px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="550" cellpadding="0" cellspacing="0" align="center">
				<thead>
				<tr>
					<td colspan="5"><strong>Fabric Details</strong></td>
				</tr>
				<tr></tr>
                    <th width="30">Sl</th>
                    <th width="250">ITEM DESCRIPTION</th>
                    <th width="100">Total Cons/Dzn</th>
                    <th width="60">UOM</th>
                    <th width="60">Req. Qty</th>
				</thead>
                <tbody>
                	<tr>
                		<td>1</td>
                		<td><? echo $description; ?></td>
                		<td><? echo $total_cons; ?></td>
                		<td><? echo $unit_of_measurement[$uom]; ?></td>
                		<td><? echo number_format($req_qty,4);; ?></td>
                	</tr>
                </tbody>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}
if($action=="job_style_popup")
{
	echo load_html_head_contents("Style Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
	$season_arr=return_library_array("select id,season_name from  lib_buyer_season","id","season_name");
	?>
    <script>
		function js_set_value( strCon )
		{
			$('#txt_selected_data').val(strCon);
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <? 
    	$search_con='';
    	if(trim($cbo_year)!=0)
		{
			if($db_type==0)
			{
				$search_con.=" and YEAR(a.insert_date)=$cbo_year";
			}
			else
			{
				$search_con.=" and to_char(a.insert_date,'YYYY')=$cbo_year";
			}
		}
		if($buyer_name!=0){
			$search_con.=" and a.buyer_name=$buyer_name";
		}
		if($from_date!="" && $to_date!="")
		{
			if($db_type==0)
			{
				$search_con.="and b.pub_shipment_date between '".change_date_format(trim($from_date),"yyyy-mm-dd")."' and '".change_date_format(trim($to_date),"yyyy-mm-dd")."'";
			}
			else
			{
				$search_con.="and b.pub_shipment_date between '".change_date_format(trim($from_date),'','',1)."' and '".change_date_format(trim($to_date),'','',1)."'";
			}
		}

		$sql = "select a.id,a.style_ref_no,a.buyer_name,a.job_no,a.season_buyer_wise,a.job_no_prefix_num,$select_date as job_year from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$companyID $search_con and a.is_deleted=0 and b.is_deleted=0 group by a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num,a.buyer_name,a.season_buyer_wise,a.insert_date order by a.id DESC";
		//echo $sql; die;
    ?>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
		<?
			//$arr=array(0=>$buyer_arr,1=>$season_arr);
            echo create_list_view("list_view", "Style Ref No,Job No,Year","220,70,60","530","390",0, $sql , "js_set_value", "id,job_no_prefix_num,style_ref_no", "", 1, "0,0,0", "", "style_ref_no,job_no_prefix_num,job_year", "","setFilterGrid('list_view',-1)","0","","") ;
            echo "<input type='hidden' id='txt_selected_data' />";
            echo "<input type='hidden' id='txt_selected_type' value='".$type."' />";
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
if($action=="order_qty_data")
{
	echo load_html_head_contents("Order Details", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$country_name_library=return_library_array( "select id, short_name from lib_country", "id", "short_name"  );
	?>
	<fieldset style="width:770px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="750" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
					<td colspan="8"><strong>Order Details</strong></td>
					</tr>
					<tr>
						<th width="30">Sl</th>
	                    <th width="80">Order No</th>
	                    <th width="100">PHD</th>
	                    <th width="60">Country</th>
	                    <th width="80">Country Ship</th>
	                    <th width="80">Country Wise QTY</th>
	                    <th width="80">Plan Cut %</th>
	                    <th width="80">Plan Cut QTY</th>
					</tr>
				</thead>
                <tbody>
                <?
					$i=1;
					$sql="SELECT a.po_number, a.pack_handover_date,  sum( c.order_quantity) as po_quantity ,c.country_id, c.excess_cut_perc, sum(c.plan_cut_qnty) as plan_cut_qty, c.country_ship_date, c.po_break_down_id from wo_po_break_down a join wo_po_color_size_breakdown c on a.id=c.po_break_down_id where c.JOB_ID in($job_id) and c.status_active=1 and c.is_deleted=0 group by c.country_id,c.po_break_down_id,a.po_number, a.pack_handover_date, c.excess_cut_perc, c.country_ship_date";
					$dtlsArray=sql_select($sql);

					foreach($dtlsArray as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";

						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><? echo $i; ?></td>
                            <td width="80" align="center"><? echo $row[csf('po_number')]; ?></td>
                            <td width="100" align="center"><? echo change_date_format($row[csf('pack_handover_date')]); ?></td>
                            <td width="60" align="center"><? echo $country_name_library[$row[csf('country_id')]]; ?></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('country_ship_date')]); ?></td>
                            <td width="80" align="center"><? echo $row[csf('po_quantity')]; ?></td>
                            <td width="80" align="center"><? echo $row[csf('excess_cut_perc')]; ?></td>
                            <td width="80" align="center"><? echo $row[csf('plan_cut_qty')]; ?></td>

                        </tr>
						<?
						$tot_qty+=$row[csf('po_quantity')];
						$tot_plan_qty+=$row[csf('plan_cut_qty')];
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                        <td align="right" colspan="5">Total</td>
                        <td><? echo number_format($tot_qty,2); ?></td>
                        <td></td>
                        <td><? echo number_format($tot_plan_qty,2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}

if($action=="order_qty_data2")
{
	echo load_html_head_contents("Order Details", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$country_name_library=return_library_array( "select id, short_name from lib_country", "id", "short_name"  );
	?>
	<fieldset style="width:770px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="750" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
					<td colspan="8"><strong>Order Details</strong></td>
					</tr>
					<tr>
						<th width="30">Sl</th>
	                    <th width="80">Order No</th>
	                    <th width="100">PHD/PCD</th>
	                    <th width="60">Country</th>
	                    <th width="80">Country Ship</th>
	                    <th width="80">Country Wise QTY</th>
	                    <th width="80">Plan Cut %</th>
	                    <th width="80">Plan Cut QTY</th>
					</tr>
				</thead>
                <tbody>
                <?
					$i=1;
					$sql="SELECT a.po_number, t.actual_start_date as pack_handover_date,  sum( c.order_quantity) as po_quantity ,c.country_id, c.excess_cut_perc, sum(c.plan_cut_qnty) as plan_cut_qty, c.country_ship_date, c.po_break_down_id from wo_po_break_down a join wo_po_color_size_breakdown c on a.id=c.po_break_down_id left join tna_process_mst t on a.id=t.po_number_id and t.task_number=354 where c.JOB_ID in($job_id) and c.status_active=1 and c.is_deleted=0  group by c.country_id,c.po_break_down_id,a.po_number, c.excess_cut_perc, c.country_ship_date,t.task_number,t.actual_start_date";
					$dtlsArray=sql_select($sql);

					foreach($dtlsArray as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";

						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><? echo $i; ?></td>
                            <td width="80" align="center"><? echo $row[csf('po_number')]; ?></td>
                            <td width="100" align="center"><? echo change_date_format($row[csf('pack_handover_date')]); ?></td>
                            <td width="60" align="center"><? echo $country_name_library[$row[csf('country_id')]]; ?></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('country_ship_date')]); ?></td>
                            <td width="80" align="center"><? echo $row[csf('po_quantity')]; ?></td>
                            <td width="80" align="center"><? echo $row[csf('excess_cut_perc')]; ?></td>
                            <td width="80" align="center"><? echo $row[csf('plan_cut_qty')]; ?></td>

                        </tr>
						<?
						$tot_qty+=$row[csf('po_quantity')];
						$tot_plan_qty+=$row[csf('plan_cut_qty')];
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                        <td align="right" colspan="5">Total</td>
                        <td><? echo number_format($tot_qty,2); ?></td>
                        <td></td>
                        <td><? echo number_format($tot_plan_qty,2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}
if($action=="order_wo_qty_data")
{
	echo load_html_head_contents("PO Summary", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$lib_supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
	$body_part_arr=return_library_array("select id, body_part_full_name from lib_body_part","id","body_part_full_name");

	$wo_data_sql=sql_select("SELECT a.id as fabric_id, a.fabric_description, a.body_part_id, a.uom,  b.booking_date, c.booking_no, c.fin_fab_qnty,b.supplier_id FROM wo_pre_cost_fabric_cost_dtls a join wo_booking_dtls c on a.id=c.pre_cost_fabric_cost_dtls_id join wo_booking_mst b on b.booking_no=c.booking_no where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.lib_yarn_count_deter_id=$yarn_id and a.job_id=$job_id and c.fin_fab_qnty is not null and c.fabric_color_id=$fcolor_id and c.gmts_color_id in ($color_id)");
	$i=1;
	?>
	<fieldset style="width:770px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="750" cellpadding="0" cellspacing="0" align="center">
				<thead>
				<tr>
					<td colspan="8"><strong>PO Summary</strong></td>
				</tr>
				<tr></tr>
                    <th width="30">Sl</th>
                    <th width="80">PO No</th>
                    <th width="60">Po Date</th>
                    <th width="100">Body Part</th>
                    <th width="150">Item Description</th>
                    <th width="60">Po Qty</th>
                    <th width="60">Uom</th>
                    <th width="80">Supplier</th>
				</thead>
                <tbody>
                	<? foreach ($wo_data_sql as $row) {
            			?>
            			<tr>
            				<td><?= $i; ?></td>
            				<td><?= $row[csf('booking_no')]?></td>
            				<td><?= $row[csf('booking_date')]?></td>
            				<td><?= $body_part_arr[$row[csf('body_part_id')]]?></td>
            				<td><?= $row[csf('fabric_description')]?></td>
            				<td><? $total_po+= $row[csf('fin_fab_qnty')]; echo fn_number_format($row[csf('fin_fab_qnty')],2) ?></td>
            				<td><?= $unit_of_measurement[$row[csf('uom')]] ?></td>
            				<td><?= $lib_supplier_arr[$row[csf('supplier_id')]] ?></td>
                		</tr>
            			<?
            			$i++;
                	} ?>                	
                </tbody>
                <tfoot>
                	<td></td>
                	<td></td>
                	<td></td>
                	<td></td>
                	<td align="right"><strong>Total</strong></td>
                	<td align="right"><strong><?= fn_number_format($total_po,2); ?></strong></td>
                	<td></td>
                	<td></td>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}
if($action=="booking_inhouse_info_btn3")
{
	echo load_html_head_contents("Recevied Details", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$lib_supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
	$user_arr=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name"  );
	$body_part_arr=return_library_array("select id, body_part_full_name from lib_body_part","id","body_part_full_name");

	?>
	<fieldset style="width:870px; margin-left:3px" >
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="13">Receive</th>
					</tr>
                    <th width="30">Sl</th>
                    <th width="60">Prod. ID</th>
                    <th width="100">MRR NO</th>
                    <th width="60">PO</th>
                    <th width="60">Status</th>
                    <th width="60">Challan NO</th>
                    <th width="60">Recv. Date</th>
                    <th width="70">Style No</th>
                    <th width="70">PO/PI No</th>
                    <th width="80">Body Part</th>
                    <th width="120">Item Description</th>
                    <th width="60">Recv. Qty</th>
                    <!-- <th width="60">Rate</th>
                    <th width="60">Amount</th>
                    <th width="60">Uom</th>
                    <th width="60">Supplier</th> -->
                    <th width="60">Insert By</th>
				</thead>
                <tbody>
                <?
				 $order_sql=sql_select("SELECT b.id,b.po_number,a.style_ref_no,b.status_active from wo_po_break_down b,wo_po_details_master a where a.id=b.job_id and  b.job_id=$job_id and b.status_active=1");
				 //echo "SELECT b.id,b.po_number,a.style_ref_no from wo_po_break_down b,wo_po_details_master a where a.id=b.job_id and  b.job_id=$job_id and b.status_active=1";
				    $poIdArr=array();
				    foreach ($order_sql as $row) 
				    {
				    	$poNoArr[$row[csf('id')]]['po_number']=$row[csf('po_number')];
				    	$poNoArr[$row[csf('id')]]['status']=$row[csf('status_active')];
				    	$poIdArr[$row[csf('id')]]=$row[csf('id')];
				    	$style_ref_no=$row[csf('style_ref_no')];
				    }
					
					$i=1;
					$receive_qty_data=sql_select("SELECT a.recv_number, d.id as prod_id, a.receive_date, a.challan_no, a.supplier_id, b.body_part_id, a.inserted_by, d.detarmination_id, d.color, d.product_name_details,sum(c.quantity) as order_qnty,sum(c.quantity*b.order_rate) as order_amount,b.cons_uom, a.booking_no,c.po_breakdown_id 	from inv_receive_master a join inv_transaction b on a.id = b.mst_id join order_wise_pro_details c on b.id = c.trans_id join product_details_master d on d.id=c.prod_id where a.entry_form=17 and a.receive_basis in (1,2) and a.status_active=1 and a.is_deleted=0 and b.receive_basis in (1,2)  and b.transaction_type=1 and b.status_active=1 and b.is_deleted=0 and c.trans_type=1 and c.entry_form=17 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.po_breakdown_id in(".implode(",",$poIdArr).") and d.detarmination_id=$yarn_id and d.color=$color group by a.recv_number, d.id , a.receive_date, a.challan_no, a.supplier_id, b.body_part_id, a.inserted_by, d.detarmination_id, d.color, d.product_name_details,b.cons_uom, a.booking_no,c.po_breakdown_id 
						union all 
						SELECT a.recv_number, d.id as prod_id, a.receive_date, a.challan_no, a.supplier_id, b.body_part_id, a.inserted_by, d.detarmination_id, d.color, d.product_name_details,sum(c.quantity) as order_qnty,sum(c.quantity*b.order_rate) as order_amount,b.cons_uom, a.booking_no,c.po_breakdown_id 	from inv_receive_master a join inv_transaction b on a.id = b.mst_id join order_wise_pro_details c on b.id = c.trans_id join product_details_master d on d.id=c.prod_id where a.entry_form=37 and a.status_active=1 and a.is_deleted=0  and b.transaction_type=1 and b.status_active=1 and b.is_deleted=0 and c.trans_type=1 and c.entry_form=37 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.po_breakdown_id in(".implode(",",$poIdArr).") and d.detarmination_id=$yarn_id and d.color=$color group by a.recv_number, d.id , a.receive_date, a.challan_no, a.supplier_id, b.body_part_id, a.inserted_by, d.detarmination_id, d.color, d.product_name_details,b.cons_uom, a.booking_no,c.po_breakdown_id");
					
					foreach($receive_qty_data as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$qty=0;
						$qty=$row[csf('order_qnty')];
						$amout=$row[csf('order_amount')];

						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td><? echo $i; ?></td>
                            <td><? echo $row[csf('prod_id')]; ?></td>
                            <td><? echo $row[csf('recv_number')]; ?></td>
                            <td><? echo $poNoArr[$row[csf('po_breakdown_id')]]['po_number']; ?></td>
                            <td><? echo $row_status[$poNoArr[$row[csf('po_breakdown_id')]]['status']]; ?></td>
                            <td><? echo $row[csf('challan_no')]; ?></td>
                            <td><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td><? echo $style_ref_no; ?></td>
                            <td><? echo $row[csf('booking_no')]; ?></td>
                            <td><? echo $body_part_arr[$row[csf('body_part_id')]]; ?></td>
                            <td><? echo $row[csf('product_name_details')]; ?></td>
                            <td align="right"><? echo number_format($qty,2); ?></td>
                            <!-- <td align="right"><? //echo number_format($amout/$qty,2); ?></td>
                            <td align="right"><? //echo number_format($amout,2); ?></td>
                            <td><? //echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></td>
                            <td><? //echo $lib_supplier_arr[$row[csf('supplier_id')]]; ?></td> -->
                            <td><? echo $user_arr[$row[csf('inserted_by')]]; ?></td>
                        </tr>
						<?
						$tot_qty+=$qty;
						$tot_amount+=$amout;
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="10" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_qty,2); ?></td>
                         <!--<td align="right"></td>
                        <td><? //echo number_format($tot_amount,2); ?></td>
                        <td align="right"></td>
                        <td align="right"></td> -->
                        <td align="right"></td>
                    </tr>
                </tfoot>
            </table>

            <!-- Recv return table -->
            <table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="7">Receive Return</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
	                    <th width="60">Prod. ID</th>
	                    <th width="100">Return NO</th>  
	                    <th width="100">Chalan No</th>  
	                    <th width="60">Return. Date</th>
	                    <th width="120">Item Description</th>
	                    <th>Return. Qty</th>
					</tr>                
				</thead>
                <tbody>
                <?
				 $order_sql=sql_select("SELECT b.id,b.po_number,a.style_ref_no,b.status_active from wo_po_break_down b,wo_po_details_master a where a.id=b.job_id and  b.job_id=$job_id and b.status_active=1");
				 //echo "SELECT b.id,b.po_number,a.style_ref_no from wo_po_break_down b,wo_po_details_master a where a.id=b.job_id and  b.job_id=$job_id and b.status_active=1";
				    $poIdArr=array();
				    foreach ($order_sql as $row) 
				    {
				    	$poNoArr[$row[csf('id')]]['po_number']=$row[csf('po_number')];
				    	$poNoArr[$row[csf('id')]]['status']=$row[csf('status_active')];
				    	$poIdArr[$row[csf('id')]]=$row[csf('id')];
				    	$style_ref_no=$row[csf('style_ref_no')];
				    }
					
					$i=1;
					$receive_rtn_qty_data=sql_select("SELECT a.issue_number, d.id as prod_id, a.issue_date, a.challan_no, a.inserted_by, d.detarmination_id, d.color, 
					d.product_name_details,sum(c.quantity) as order_qnty,sum(c.quantity*b.order_rate) as order_amount,b.cons_uom, a.booking_no,c.po_breakdown_id 
					from inv_issue_master a join inv_transaction b on a.id = b.mst_id join order_wise_pro_details c on b.id = c.trans_id join product_details_master d 
					on d.id=c.prod_id where a.entry_form=202 and a.status_active=1 and a.is_deleted=0 and b.transaction_type=3 and b.status_active=1 
					and b.is_deleted=0 and c.trans_type=3 and c.entry_form=202 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 
					and c.po_breakdown_id in(".implode(",",$poIdArr).") and d.detarmination_id=$yarn_id and d.color=$color
					group by a.issue_number, d.id , a.issue_date, a.challan_no, a.inserted_by, d.detarmination_id, d.color, d.product_name_details,b.cons_uom, a.booking_no,c.po_breakdown_id");
					
					foreach($receive_rtn_qty_data as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$qty=0;
						$qty=$row[csf('order_qnty')];
						$amout=$row[csf('order_amount')];

						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td><? echo $i; ?></td>
                            <td><? echo $row[csf('prod_id')]; ?></td>
                            <td><? echo $row[csf('issue_number')]; ?></td>
                            <td><? echo $row[csf('challan_no')]; ?></td>
                            <td><? echo change_date_format($row[csf('issue_date')]); ?></td>
                            <td><? echo $row[csf('product_name_details')]; ?></td>
                            <td align="right"><? echo number_format($qty,2); ?></td>
                        </tr>
						<?
						$tot_qtys+=$qty;
						$i++;
					}
				?>
				<tr>
					<th colspan="5" align="right"></th>
                    <th align="right">Total</th>
                    <th align="right"><? echo number_format($tot_qtys,2); ?></th>
				</tr>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="5" align="right"></td>
                        <td align="right">Balance</td>
                        <td align="right"><? echo number_format($tot_qty-$tot_qtys,2); ?></td>
                    </tr>
                </tfoot>
                
            </table>
           



        </div>
    </fieldset>
    <?
	exit();
}
if($action=="booking_inhouse_info")
{
	echo load_html_head_contents("Recevied Details", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$lib_supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
	$user_arr=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name"  );
	$body_part_arr=return_library_array("select id, body_part_full_name from lib_body_part","id","body_part_full_name");

	?>
	<fieldset style="width:870px; margin-left:3px" >
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="13">Receive</th>
					</tr>
                    <th width="30">Sl</th>
                    <th width="60">Prod. ID</th>
                    <th width="100">MRR NO</th>
                    <th width="60">PO</th>
                    <th width="60">Status</th>
                    <th width="60">Challan NO</th>
                    <th width="60">Recv. Date</th>
                    <th width="70">Style No</th>
                    <th width="70">PO/PI No</th>
                    <th width="80">Body Part</th>
                    <th width="120">Item Description</th>
                    <th width="60">Recv. Qty</th>
                    <!-- <th width="60">Rate</th>
                    <th width="60">Amount</th>
                    <th width="60">Uom</th>
                    <th width="60">Supplier</th> -->
                    <th width="60">Insert By</th>
				</thead>
                <tbody>
                <?
				 $order_sql=sql_select("SELECT b.id,b.po_number,a.style_ref_no,b.status_active from wo_po_break_down b,wo_po_details_master a where a.id=b.job_id and  b.job_id=$job_id and b.status_active=1");
				 //echo "SELECT b.id,b.po_number,a.style_ref_no from wo_po_break_down b,wo_po_details_master a where a.id=b.job_id and  b.job_id=$job_id and b.status_active=1";
				    $poIdArr=array();
				    foreach ($order_sql as $row) 
				    {
				    	$poNoArr[$row[csf('id')]]['po_number']=$row[csf('po_number')];
				    	$poNoArr[$row[csf('id')]]['status']=$row[csf('status_active')];
				    	$poIdArr[$row[csf('id')]]=$row[csf('id')];
				    	$style_ref_no=$row[csf('style_ref_no')];
				    }
					
					$i=1;
					$receive_qty_data=sql_select("SELECT a.recv_number, d.id as prod_id, a.receive_date, a.challan_no, a.supplier_id, b.body_part_id, a.inserted_by, d.detarmination_id, d.color, d.product_name_details,sum(c.quantity) as order_qnty,sum(c.quantity*b.order_rate) as order_amount,b.cons_uom, a.booking_no,c.po_breakdown_id 	from inv_receive_master a join inv_transaction b on a.id = b.mst_id join order_wise_pro_details c on b.id = c.trans_id join product_details_master d on d.id=c.prod_id where a.entry_form=17 and a.receive_basis in (1,2) and a.status_active=1 and a.is_deleted=0 and b.receive_basis in (1,2)  and b.transaction_type=1 and b.status_active=1 and b.is_deleted=0 and c.trans_type=1 and c.entry_form=17 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.po_breakdown_id in(".implode(",",$poIdArr).") and d.detarmination_id=$yarn_id and d.color=$color group by a.recv_number, d.id , a.receive_date, a.challan_no, a.supplier_id, b.body_part_id, a.inserted_by, d.detarmination_id, d.color, d.product_name_details,b.cons_uom, a.booking_no,c.po_breakdown_id ");
					
					foreach($receive_qty_data as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$qty=0;
						$qty=$row[csf('order_qnty')];
						$amout=$row[csf('order_amount')];

						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td><? echo $i; ?></td>
                            <td><? echo $row[csf('prod_id')]; ?></td>
                            <td><? echo $row[csf('recv_number')]; ?></td>
                            <td><? echo $poNoArr[$row[csf('po_breakdown_id')]]['po_number']; ?></td>
                            <td><? echo $row_status[$poNoArr[$row[csf('po_breakdown_id')]]['status']]; ?></td>
                            <td><? echo $row[csf('challan_no')]; ?></td>
                            <td><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td><? echo $style_ref_no; ?></td>
                            <td><? echo $row[csf('booking_no')]; ?></td>
                            <td><? echo $body_part_arr[$row[csf('body_part_id')]]; ?></td>
                            <td><? echo $row[csf('product_name_details')]; ?></td>
                            <td align="right"><? echo number_format($qty,2); ?></td>
                            <!-- <td align="right"><? //echo number_format($amout/$qty,2); ?></td>
                            <td align="right"><? //echo number_format($amout,2); ?></td>
                            <td><? //echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></td>
                            <td><? //echo $lib_supplier_arr[$row[csf('supplier_id')]]; ?></td> -->
                            <td><? echo $user_arr[$row[csf('inserted_by')]]; ?></td>
                        </tr>
						<?
						$tot_qty+=$qty;
						$tot_amount+=$amout;
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="10" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_qty,2); ?></td>
                         <!--<td align="right"></td>
                        <td><? //echo number_format($tot_amount,2); ?></td>
                        <td align="right"></td>
                        <td align="right"></td> -->
                        <td align="right"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}
if($action=="booking_inhouse_info_issue_rtn")
{
	echo load_html_head_contents("Recevied Details", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$lib_supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
	$user_arr=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name"  );
	$body_part_arr=return_library_array("select id, body_part_full_name from lib_body_part","id","body_part_full_name");

	?>
	<fieldset style="width:870px; margin-left:3px" >
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="30">Sl</th>
                    <th width="60">Prod. ID</th>
                    <th width="100">MRR NO</th>
                    <th width="60">Challan NO</th>
                    <th width="60">Issue Return Date</th>
                    <th width="70">Style No</th>
                    <th width="70">PO/PI No</th>
                    <th width="80">Body Part</th>
                    <th width="120">Item Description</th>
                    <th width="60">Issue Return Qty</th>
                    <th width="60">Rate</th>
                    <th width="60">Amount</th>
                    <th width="60">Uom</th>
                    <th width="60">Supplier</th>
                    <th width="60">Insert By</th>
				</thead>
                <tbody>
                <?
				 $order_sql=sql_select("SELECT b.id,b.po_number,a.style_ref_no from wo_po_break_down b,wo_po_details_master a where a.id=b.job_id and  b.job_id=$job_id and b.status_active=1");
				    $poIdArr=array();
				    foreach ($order_sql as $row) 
				    {
				    	$poIdArr[$row[csf('id')]]=$row[csf('id')];
				    	$style_ref_no=$row[csf('style_ref_no')];
				    }
					
					$i=1;

					
					$issue_rtn_qty_data=sql_select("SELECT a.recv_number, d.id as prod_id, a.receive_date, a.challan_no, a.supplier_id, b.body_part_id, a.inserted_by, d.detarmination_id, d.color, d.product_name_details,sum(c.quantity) as order_qnty,sum(c.quantity*b.order_rate) as order_amount,b.cons_uom, a.booking_no 	from inv_receive_master a join inv_transaction b on a.id = b.mst_id join order_wise_pro_details c on b.id = c.trans_id join product_details_master d on d.id=c.prod_id where a.entry_form=209 and a.status_active=1 and a.is_deleted=0   and b.transaction_type=4 and b.status_active=1 and b.is_deleted=0 and c.trans_type=4 and c.entry_form=209 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.po_breakdown_id in(".implode(",",$poIdArr).") and d.detarmination_id=$yarn_id and d.color=$color group by a.recv_number, d.id , a.receive_date, a.challan_no, a.supplier_id, b.body_part_id, a.inserted_by, d.detarmination_id, d.color, d.product_name_details,b.cons_uom, a.booking_no ");
					
					
					foreach($issue_rtn_qty_data as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$qty=0;
						$qty=$row[csf('order_qnty')];
						$amout=$row[csf('order_amount')];

						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td><? echo $i; ?></td>
                            <td><? echo $row[csf('prod_id')]; ?></td>
                            <td><? echo $row[csf('recv_number')]; ?></td>
                            <td><? echo $row[csf('challan_no')]; ?></td>
                            <td><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td><? echo $style_ref_no; ?></td>
                            <td><? echo $row[csf('booking_no')]; ?></td>
                            <td><? echo $body_part_arr[$row[csf('body_part_id')]]; ?></td>
                            <td><? echo $row[csf('product_name_details')]; ?></td>
                            <td align="right"><? echo number_format($qty,2); ?></td>
                            <td align="right"><? echo number_format($amout/$qty,2); ?></td>
                            <td align="right"><? echo number_format($amout,2); ?></td>
                            <td><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></td>
                            <td><? echo $lib_supplier_arr[$row[csf('supplier_id')]]; ?></td>
                            <td><? echo $user_arr[$row[csf('inserted_by')]]; ?></td>
                        </tr>
						<?
						$tot_qty+=$qty;
						$tot_amount+=$amout;
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="8" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_qty,2); ?></td>
                        <td align="right"></td>
                        <td><? echo number_format($tot_amount,2); ?></td>
                        <td align="right"></td>
                        <td align="right"></td>
                        <td align="right"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}
if($action=="booking_inhouse_info_trans_in")
{
	echo load_html_head_contents("Recevied Details", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$lib_supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
	$user_arr=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name"  );
	$body_part_arr=return_library_array("select id, body_part_full_name from lib_body_part","id","body_part_full_name");

	?>
	<fieldset style="width:870px; margin-left:3px" >
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="30">Sl</th>
                    <th width="60">Prod. ID</th>
                    <th width="100">MRR NO</th>
                    <th width="60">Transfer. Date</th>
                    <th width="70">From Style No</th>
                    <th width="70">From PO</th>
                    <th width="60">Transfer. Qty</th>
                    <th width="60">Remarks</th>
				</thead>
                <tbody>
                <?
				 $order_sql=sql_select("SELECT b.id,b.po_number,a.style_ref_no from wo_po_break_down b,wo_po_details_master a where a.id=b.job_id and  b.job_id=$job_id and b.id in($po_id) and b.status_active=1");
				    $poIdArr=array();
				    foreach ($order_sql as $row) 
				    {
				    	$poIdArr[$row[csf('id')]]=$row[csf('id')];
				    	$style_ref_no=$row[csf('style_ref_no')];
				    }

				   
					
					$i=1;
					$trans_out_qty_data=sql_select("select  a.transfer_system_id as recv_number,b.from_order_id  from inv_item_transfer_mst a, inv_item_transfer_dtls b where a.id=b.mst_id and  a.entry_form=258 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and b.to_order_id in(".implode(",",$poIdArr).") group by a.transfer_system_id,b.from_order_id ");
					$formOrder="";
					foreach($trans_out_qty_data as $row)
					{
						$formOrder.=$row[csf('from_order_id')].",";
						$transOutArr[$row[csf('recv_number')]]['from_order_id']=$row[csf('from_order_id')];
					}
					$formOrder=chop($formOrder,",");
					$order_sql_from_po=sql_select("SELECT b.id,b.po_number,a.style_ref_no from wo_po_break_down b,wo_po_details_master a where a.id=b.job_id  and b.id in($formOrder) and b.status_active=1");
				    $poIdArrFrom=array();
				    foreach ($order_sql_from_po as $row) 
				    {
				    	$poIdArrFrom[$row[csf('id')]]=$row[csf('id')];
				    	$poIdArrNoFrom[$row[csf('id')]]=$row[csf('po_number')];
				    	$style_ref_noFrom[$row[csf('id')]]=$row[csf('style_ref_no')];
				    }


					$trans_in_qty_data=sql_select("SELECT a.transfer_system_id as  recv_number, d.id as prod_id, a.transfer_date, a.challan_no, b.body_part_id, a.inserted_by, d.detarmination_id, d.color, d.product_name_details,sum(c.quantity) as order_qnty,sum(c.quantity*b.order_rate) as order_amount,b.cons_uom,a.from_order_id,a.remarks	from inv_item_transfer_mst a join inv_transaction b on a.id = b.mst_id join order_wise_pro_details c on b.id = c.trans_id join product_details_master d on d.id=c.prod_id where a.entry_form=258 and a.status_active=1 and a.is_deleted=0   and b.transaction_type=5 and b.status_active=1 and b.is_deleted=0 and c.trans_type=5 and c.entry_form=258 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.po_breakdown_id in(".implode(",",$poIdArr).") and d.detarmination_id=$yarn_id and d.color=$color group by a.transfer_system_id, d.id , a.transfer_date, a.challan_no, b.body_part_id, a.inserted_by, d.detarmination_id, d.color, d.product_name_details,b.cons_uom,a.from_order_id,a.remarks ");
					//$trans_in_qty_data=sql_select("SELECT a.transfer_system_id as  recv_number, d.id as prod_id, a.receive_date, a.challan_no, a.supplier_id, b.body_part_id, a.inserted_by, d.detarmination_id, d.color, d.product_name_details,sum(c.quantity) as order_qnty,sum(c.quantity*b.order_rate) as order_amount,b.cons_uom, a.booking_no 	from inv_item_transfer_mst a join inv_transaction b on a.id = b.mst_id join order_wise_pro_details c on b.id = c.trans_id join product_details_master d on d.id=c.prod_id where a.entry_form=258 and a.status_active=1 and a.is_deleted=0   and b.transaction_type=5 and b.status_active=1 and b.is_deleted=0 and c.trans_type=5 and c.entry_form=258 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.po_breakdown_id in(".implode(",",$poIdArr).") and d.detarmination_id=$yarn_id and d.color=$color group by a.recv_number, d.id , a.receive_date, a.challan_no, a.supplier_id, b.body_part_id, a.inserted_by, d.detarmination_id, d.color, d.product_name_details,b.cons_uom, a.booking_no ");
					
					
					foreach($trans_in_qty_data as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$qty=0;
						$qty=$row[csf('order_qnty')];
						$amout=$row[csf('order_amount')];

						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td><? echo $i; ?></td>
                            <td><? echo $row[csf('prod_id')]; ?></td>
                            <td><? echo $row[csf('recv_number')]; ?></td>
                            <td><? echo change_date_format($row[csf('transfer_date')]); ?></td>
                            <td><? echo $style_ref_noFrom[$transOutArr[$row[csf('recv_number')]]['from_order_id']]; ?></td>
                            <td><? echo $poIdArrNoFrom[$transOutArr[$row[csf('recv_number')]]['from_order_id']]; ?></td>
                            <td align="right"><? echo number_format($qty,2); ?></td>
                            <td><? echo $row[csf('remarks')]; ?></td>
                        </tr>
						<?
						$tot_qty+=$qty;
						$tot_amount+=$amout;
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="5" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_qty,2); ?></td>
                        <td align="right"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}
if($action=="booking_issue_info_btn3")
{
	echo load_html_head_contents("Issue Details", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$lib_supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
	$user_arr=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name"  );
	$body_part_arr=return_library_array("select id, body_part_full_name from lib_body_part","id","body_part_full_name");
	?>
	<fieldset style="width:870px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="13"><strong>Issue Details</strong></th>
					</tr>
					<tr>
	                    <th width="30">Sl</th>
	                    <th width="80">Prod. ID</th>
	                    <th width="80">Issue No</th>
	                     <th width="80">Issue. Date</th>
	                     <th width="80">Job No</th>
	                    <th width="80">Style No</th>
	                    <th width="80">Body Part</th>
	                    <th width="100">Item Description</th>
	                    <th width="60">Issue. Qty</th>
	                    <th width="60">Rate</th>
	                    <th width="60">Amount</th>
	                    <th width="60">Uom</th>
	                    <th width="60">Insert By</th>
                    </tr>
				</thead>
                <tbody>
                <?
					$i=1;

				  $issue_qty_data="SELECT a.issue_number, a.issue_date, a.inserted_by, b.prod_id, b.cons_uom, d.detarmination_id, d.color, c.quantity as cons_quantity, b.cons_rate, b.body_part_id,  b.cons_amount, d.product_name_details, e.job_id, e.id as po_id, f.job_no, f.style_ref_no  from inv_issue_master a join inv_transaction b on a.id = b.mst_id join order_wise_pro_details c on b.id = c.trans_id join product_details_master d on d.id=c.prod_id join wo_po_break_down e on e.id=c.po_breakdown_id join wo_po_details_master f on f.id=e.job_id where a.entry_form=19 and a.issue_basis=1 and a.status_active=1 and a.is_deleted=0 and b.transaction_type=2 and b.status_active=1 and b.is_deleted=0 and c.trans_type=2 and c.entry_form=19 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.id in($po_id) and d.detarmination_id=$yarn_id and d.color=$color 
				  union all 
				  SELECT a.issue_number, a.issue_date, a.inserted_by, b.prod_id, b.cons_uom, d.detarmination_id, d.color, c.quantity as cons_quantity, b.cons_rate, b.body_part_id,  b.cons_amount, d.product_name_details, e.job_id, e.id as po_id, f.job_no, f.style_ref_no  from inv_issue_master a join inv_transaction b on a.id = b.mst_id join order_wise_pro_details c on b.id = c.trans_id join product_details_master d on d.id=c.prod_id join wo_po_break_down e on e.id=c.po_breakdown_id join wo_po_details_master f on f.id=e.job_id where a.entry_form=18 and a.status_active=1 and a.is_deleted=0 and b.transaction_type=2 and b.status_active=1 and b.is_deleted=0 and c.trans_type=2 and c.entry_form=18 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.id in($po_id) and a.item_category=2 and d.detarmination_id=$yarn_id and d.color=$color";
				  //echo $issue_qty_data; die;

					$dtlsArray=sql_select($issue_qty_data);

					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						//$rate=$rcv_rate*1;
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td><? echo $i; ?></td>
                            <td><? echo $row[csf('prod_id')]; ?></td>
                            <td><? echo $row[csf('issue_number')]; ?></td>
                            <td><? echo  change_date_format($row[csf('issue_date')]); ?></td>                            
                            <td><? echo $row[csf('job_no')]; ?></td>
                            <td><? echo $row[csf('style_ref_no')]; ?></td>
                            <td><? echo $body_part_arr[$row[csf('body_part_id')]]; ?></td>
                            <td><? echo $row[csf('product_name_details')]; ?></td>
                            <td><? echo $row[csf('cons_quantity')]; ?></td>
                            <td title="<?= $rate ?>"><? echo $rate; ?></td>
                            <td><? echo $row[csf('cons_quantity')]*$rate; ?></td>
                            <td><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></td>
                            <td><? echo $user_arr[$row[csf('inserted_by')]]; ?></td>
                        </tr>
						<?
						$tot_qty+=$row[csf('cons_quantity')];
						$tot_amount+=$row[csf('cons_quantity')]*$rate;
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="7" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_qty,2); ?></td>
                        <td></td>
                        <td><? echo number_format($tot_amount,2); ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
            <!-- Issue Return -->
            <table border="1" class="rpt_table" rules="all" width="550" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="7"><strong>Issue Return</strong></th>
					</tr>
					<tr>
	                    <th width="30">Sl</th>
	                    <th width="80">Prod. ID</th>
	                    <th width="80">Return No</th>
	                    <th width="80">Challan No</th>
	                    <th width="80">Return. Date</th>
	                    <th width="100">Item Description</th>
	                    <th>Return. Qty</th>
                    </tr>
				</thead>
                <tbody>
                <?
					$i=1;

				  $issue_qty_data="SELECT a.recv_number, a.receive_date,a.challan_no, a.inserted_by, b.prod_id, b.cons_uom, d.detarmination_id, d.color, c.quantity as cons_quantity, b.cons_rate, b.body_part_id,  b.cons_amount, d.product_name_details, e.job_id, e.id as po_id, f.job_no, f.style_ref_no  from inv_receive_master a join inv_transaction b on a.id = b.mst_id join order_wise_pro_details c on b.id = c.trans_id join product_details_master d on d.id=c.prod_id join wo_po_break_down e on e.id=c.po_breakdown_id join wo_po_details_master f on f.id=e.job_id where a.entry_form=209 and a.status_active=1 and a.is_deleted=0 and b.transaction_type=4 and b.status_active=1 and b.is_deleted=0 and c.trans_type=4 and c.entry_form=209 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.id in($po_id) and d.detarmination_id=$yarn_id and d.color=$color";
				  //echo $issue_qty_data; die;

					$dtlsArray=sql_select($issue_qty_data);

					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						//$rate=$rcv_rate*1;
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td><? echo $i; ?></td>
                            <td><? echo $row[csf('prod_id')]; ?></td>
                            <td><? echo $row[csf('recv_number')]; ?></td>
                            <td><? echo $row[csf('challan_no')]; ?></td>
                            <td><? echo change_date_format($row[csf('receive_date')]); ?></td>                           
                            <td><? echo $row[csf('product_name_details')]; ?></td>
                            <td align="right"><? echo $row[csf('cons_quantity')]; ?></td>
                        </tr>
						<?
						$tot_qtys+=$row[csf('cons_quantity')];
						//$tot_amount+=$row[csf('cons_quantity')]*$rate;
						$i++;
					}
				?>
				<tr class="tbl_bottom">
	            	<th colspan="5" align="right"></th>
	                <th align="right">Total</th>
	                <th align="right"><? echo number_format($tot_qtys,2); ?></th>
				</tr>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="5" align="right"></td>
                        <td align="right">Balance</td>
                        <td><? echo number_format($tot_qty-$tot_qtys,2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}
if($action=="booking_issue_info")
{
	echo load_html_head_contents("Issue Details", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$lib_supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
	$user_arr=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name"  );
	$body_part_arr=return_library_array("select id, body_part_full_name from lib_body_part","id","body_part_full_name");
	?>
	<fieldset style="width:870px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<td colspan="13"><strong>Issue Details</strong></td>
					</tr>
					<tr>
	                    <th width="30">Sl</th>
	                    <th width="80">Prod. ID</th>
	                    <th width="80">Issue No</th>
	                     <th width="80">Issue. Date</th>
	                     <th width="80">Job No</th>
	                    <th width="80">Style No</th>
	                    <th width="80">Body Part</th>
	                    <th width="100">Item Description</th>
	                    <th width="60">Issue. Qty</th>
	                    <th width="60">Rate</th>
	                    <th width="60">Amount</th>
	                    <th width="60">Uom</th>
	                    <th width="60">Insert By</th>
                    </tr>
				</thead>
                <tbody>
                <?
					$i=1;

				  $issue_qty_data="SELECT a.issue_number, a.issue_date, a.inserted_by, b.prod_id, b.cons_uom, d.detarmination_id, d.color, c.quantity as cons_quantity, b.cons_rate, b.body_part_id,  b.cons_amount, d.product_name_details, e.job_id, e.id as po_id, f.job_no, f.style_ref_no  from inv_issue_master a join inv_transaction b on a.id = b.mst_id join order_wise_pro_details c on b.id = c.trans_id join product_details_master d on d.id=c.prod_id join wo_po_break_down e on e.id=c.po_breakdown_id join wo_po_details_master f on f.id=e.job_id where a.entry_form=19 and a.issue_basis=1 and a.status_active=1 and a.is_deleted=0 and b.transaction_type=2 and b.status_active=1 and b.is_deleted=0 and c.trans_type=2 and c.entry_form=19 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.id in($po_id) and d.detarmination_id=$yarn_id and d.color=$color";
				  //echo $issue_qty_data; die;

					$dtlsArray=sql_select($issue_qty_data);

					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						//$rate=$rcv_rate*1;
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td><? echo $i; ?></td>
                            <td><? echo $row[csf('prod_id')]; ?></td>
                            <td><? echo $row[csf('issue_number')]; ?></td>
                            <td><? echo  change_date_format($row[csf('issue_date')]); ?></td>                            
                            <td><? echo $row[csf('job_no')]; ?></td>
                            <td><? echo $row[csf('style_ref_no')]; ?></td>
                            <td><? echo $body_part_arr[$row[csf('body_part_id')]]; ?></td>
                            <td><? echo $row[csf('product_name_details')]; ?></td>
                            <td><? echo $row[csf('cons_quantity')]; ?></td>
                            <td title="<?= $rate ?>"><? echo $rate; ?></td>
                            <td><? echo $row[csf('cons_quantity')]*$rate; ?></td>
                            <td><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></td>
                            <td><? echo $user_arr[$row[csf('inserted_by')]]; ?></td>
                        </tr>
						<?
						$tot_qty+=$row[csf('cons_quantity')];
						$tot_amount+=$row[csf('cons_quantity')]*$rate;
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="7" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_qty,2); ?></td>
                        <td></td>
                        <td><? echo number_format($tot_amount,2); ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}
if($action=="booking_issue_info_recv_rtn")
{
	echo load_html_head_contents("Issue Details", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$lib_supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
	$user_arr=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name"  );
	$body_part_arr=return_library_array("select id, body_part_full_name from lib_body_part","id","body_part_full_name");
	?>
	<fieldset style="width:870px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<td colspan="13"><strong>Receive Return Details</strong></td>
					</tr>
					<tr>
	                    <th width="30">Sl</th>
	                    <th width="80">Prod. ID</th>
	                    <th width="80">Receive Return No</th>
	                     <th width="80">Receive Return Date</th>
	                     <th width="80">Job No</th>
	                    <th width="80">Style No</th>
	                    <th width="80">Body Part</th>
	                    <th width="100">Item Description</th>
	                    <th width="60">Receive Return. Qty</th>
	                    <th width="60">Rate</th>
	                    <th width="60">Amount</th>
	                    <th width="60">Uom</th>
	                    <th width="60">Insert By</th>
                    </tr>
				</thead>
                <tbody>
                <?
					$i=1;

				  $recv_rtn_qty_data="SELECT a.issue_number, a.issue_date, a.inserted_by, b.prod_id, b.cons_uom, d.detarmination_id, d.color, c.quantity as cons_quantity, b.cons_rate, b.body_part_id,  b.cons_amount, d.product_name_details, e.job_id, e.id as po_id, f.job_no, f.style_ref_no  from inv_issue_master a join inv_transaction b on a.id = b.mst_id join order_wise_pro_details c on b.id = c.trans_id join product_details_master d on d.id=c.prod_id join wo_po_break_down e on e.id=c.po_breakdown_id join wo_po_details_master f on f.id=e.job_id where a.entry_form=202  and a.status_active=1 and a.is_deleted=0 and b.transaction_type=3 and b.status_active=1 and b.is_deleted=0 and c.trans_type=3 and c.entry_form=202 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.id in($po_id) and d.detarmination_id=$yarn_id and d.color=$color";
				  //echo $issue_qty_data; die;

					$dtlsArray=sql_select($recv_rtn_qty_data);

					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						//$rate=$rcv_rate*1;
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td><? echo $i; ?></td>
                            <td><? echo $row[csf('prod_id')]; ?></td>
                            <td><? echo $row[csf('issue_number')]; ?></td>
                            <td><? echo  change_date_format($row[csf('issue_date')]); ?></td>                            
                            <td><? echo $row[csf('job_no')]; ?></td>
                            <td><? echo $row[csf('style_ref_no')]; ?></td>
                            <td><? echo $body_part_arr[$row[csf('body_part_id')]]; ?></td>
                            <td><? echo $row[csf('product_name_details')]; ?></td>
                            <td><? echo number_format($row[csf('cons_quantity')],2); ?></td>
                            <td title="<?= $rate ?>"><? echo $rate; ?></td>
                            <td><? echo $row[csf('cons_quantity')]*$rate; ?></td>
                            <td><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></td>
                            <td><? echo $user_arr[$row[csf('inserted_by')]]; ?></td>
                        </tr>
						<?
						$tot_qty+=$row[csf('cons_quantity')];
						$tot_amount+=$row[csf('cons_quantity')]*$rate;
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="7" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_qty,2); ?></td>
                        <td></td>
                        <td><? echo number_format($tot_amount,2); ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}
if($action=="booking_inhouse_info_trans_out")
{
	echo load_html_head_contents("Recevied Details", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$lib_supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
	$user_arr=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name"  );
	$body_part_arr=return_library_array("select id, body_part_full_name from lib_body_part","id","body_part_full_name");

	?>
	<fieldset style="width:870px; margin-left:3px" >
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<thead>
                   <th width="30">Sl</th>
                    <th width="60">Prod. ID</th>
                    <th width="100">MRR NO</th>
                    <th width="60">Transfer. Date</th>
                    
                    <th width="70">To PO</th>
                    <th width="70">To Style No</th>
                    <th width="60">Transfer. Qty</th>
                    <th width="60">Remarks</th>
				</thead>
                <tbody>
                <?
				 $order_sql=sql_select("SELECT b.id,b.po_number,a.style_ref_no from wo_po_break_down b,wo_po_details_master a where a.id=b.job_id and  b.job_id=$job_id and b.status_active=1");
				    $poIdArr=array();
				    foreach ($order_sql as $row) 
				    {
				    	$poIdArr[$row[csf('id')]]=$row[csf('id')];
				    	$style_ref_no=$row[csf('style_ref_no')];
				    }

				   
					
					$i=1;


					$trans_in_qty_data=sql_select("select  a.transfer_system_id as recv_number,b.to_order_id  from inv_item_transfer_mst a, inv_item_transfer_dtls b where a.id=b.mst_id and  a.entry_form=258 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and b.from_order_id in(".implode(",",$poIdArr).") group by a.transfer_system_id,b.to_order_id ");
					$toOrder="";
					foreach($trans_in_qty_data as $row)
					{
						$toOrder.=$row[csf('to_order_id')].",";
						$transToArr[$row[csf('recv_number')]]['to_order_id'].=$row[csf('to_order_id')].',';
					}
					
					$toOrder=chop($toOrder,",");
					 $order_sql_to=sql_select("SELECT b.id,b.po_number,a.style_ref_no from wo_po_break_down b,wo_po_details_master a where a.id=b.job_id and  b.id in($toOrder) and b.status_active=1");
				    $poIdArrTo=array();
				    foreach ($order_sql_to as $row) 
				    {
				    	$poIdArrTo[$row[csf('id')]]=$row[csf('id')];
				    	$poIdArrToNo[$row[csf('id')]]=$row[csf('po_number')];
				    	$style_ref_no_to[$row[csf('id')]]=$row[csf('style_ref_no')];
				    }
				    //print_r($poIdArrToNo);

					$trans_out_qty_data=sql_select("SELECT a.transfer_system_id as  recv_number, d.id as prod_id, a.transfer_date, a.challan_no, b.body_part_id, a.inserted_by, d.detarmination_id, d.color, d.product_name_details,sum(c.quantity) as order_qnty,sum(c.quantity*b.order_rate) as order_amount,b.cons_uom,a.to_order_id,a.remarks 	from inv_item_transfer_mst a join inv_transaction b on a.id = b.mst_id join order_wise_pro_details c on b.id = c.trans_id join product_details_master d on d.id=c.prod_id where a.entry_form=258 and a.status_active=1 and a.is_deleted=0   and b.transaction_type=6 and b.status_active=1 and b.is_deleted=0 and c.trans_type=6 and c.entry_form=258 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.po_breakdown_id in(".implode(",",$poIdArr).") and d.detarmination_id=$yarn_id and d.color=$color group by a.transfer_system_id, d.id , a.transfer_date, a.challan_no, b.body_part_id, a.inserted_by, d.detarmination_id, d.color, d.product_name_details,b.cons_uom,a.to_order_id,a.remarks ");
					
					
					foreach($trans_out_qty_data as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$qty=0;
						$qty=$row[csf('order_qnty')];
						$amout=$row[csf('order_amount')];

						$topoExp=array_unique(explode(",",$transToArr[$row[csf('recv_number')]]['to_order_id']));
						//$inPoStrArr="";
						$arrChk=array();
						foreach($topoExp as $poIds)
						{
							$inPoStrArr[$row[csf('recv_number')]]['po'].=$poIdArrToNo[$poIds].",";
							if($arrChk[$inPoStrArr[$row[csf('recv_number')]]['style']]!=$style_ref_no_to[$poIds])
							{
								$inPoStrArr[$row[csf('recv_number')]]['style'].=$style_ref_no_to[$poIds].",";
							 	$arrChk[$inPoStrArr[$row[csf('recv_number')]]['style']]=$style_ref_no_to[$poIds];
							}
							
						}
						//print_r($inPoStrArr);
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td><? echo $i; ?></td>
                            <td><? echo $row[csf('prod_id')]; ?></td>
                            <td><? echo $row[csf('recv_number')]; ?></td>
                            <td><? echo change_date_format($row[csf('transfer_date')]); ?></td>
                            <td><? echo chop($inPoStrArr[$row[csf('recv_number')]]['po'],","); ?></td>
                            <td><? echo chop($inPoStrArr[$row[csf('recv_number')]]['style'],","); ?></td>
                            <td align="right"><? echo number_format($qty,2); ?></td>
                            <td><? echo $row[csf('remarks')]; ?></td>
                        </tr>
						<?
						$tot_qty+=$qty;
						$tot_amount+=$amout;
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="5" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_qty,2); ?></td>
                        <td align="right"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}


if($action=="trim_req_qty_data")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$lib_item_group_arr = return_library_array("select item_name,id from lib_item_group where item_category=4 order by item_name", "id", "item_name");

	$main_data_sql=sql_select("SELECT id, job_no, trim_group, description, brand_sup_ref, tot_cons, cons_uom,cons_dzn_gmts from wo_pre_cost_trim_cost_dtls where status_active=1 and is_deleted=0 and job_id=$job_id and id=$trim_id");

	$condition= new condition();
	$condition->jobid_in($job_id);
	$condition->init();
	$trim= new trims($condition);
	$trim_group_qty_arr=$trim->getQtyArray_by_jobAndPrecostdtlsid();
	/*echo '<pre>';
	print_r($trim_group_qty_arr); die;*/


	?>
	<fieldset style="width:570px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="550" cellpadding="0" cellspacing="0" align="center">
				<thead>
				<tr>
					<td colspan="7"><strong>Trims Details</strong></td>
				</tr>
				<tr></tr>
                    <th width="30">Sl</th>
                    <th width="100">Trims Name</th>
                    <th width="150">Item Description</th>
                    <th width="60">Brand/Sup Ref</th>
                    <th width="60">Total Cons/Dzn</th>
                    <th width="60">UOM</th>
                    <th width="60">Req. Qty</th>
				</thead>
                <tbody>
                	<? foreach ($main_data_sql as $row) { ?>                	
                	<tr>
                		<td>1</td>
                		<td><? echo $lib_item_group_arr[$row[csf('trim_group')]]; ?></td>
                		<td><? echo $row[csf('description')]; ?></td>
                		<td><? echo $row[csf('brand_sup_ref')]; ?></td>
                		<td><? echo number_format($row[csf('cons_dzn_gmts')],4); ?></td>
                		<td><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></td>
                		<td><? echo number_format($trim_group_qty_arr[$row[csf('job_no')]][$trim_id],4); ?></td>
                	</tr>
                	<? } ?>
                </tbody>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}
if($action=="trim_wo_qty_data")
{
	echo load_html_head_contents("PO Summary", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$lib_item_group_arr = return_library_array("select item_name,id from lib_item_group where item_category=4 order by item_name", "id", "item_name");
	$lib_supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );

	$is_short_arr=array(1=>"Short",2=>"Main");

	$main_data_sql=sql_select("SELECT a.is_short, a.booking_no, a.booking_date, a.supplier_id, a.pay_mode, b.job_no,b.country_id_string, b.po_break_down_id,sum(b.wo_qnty) as wo_qnty,b.uom,c.description from wo_booking_mst a join wo_booking_dtls b on a.booking_no=b.booking_no join wo_pre_cost_trim_cost_dtls c on b.pre_cost_fabric_cost_dtls_id=c.id where   a.item_category=4 and b.status_active=1 and b.is_deleted=0 and b.pre_cost_fabric_cost_dtls_id in($trim_id) and a.is_deleted=0 and a.status_active=1 group by b.po_break_down_id,b.job_no, a.booking_no, a.booking_date, a.supplier_id, a.pay_mode, b.uom,b.country_id_string, a.is_short,c.description");
	?>
	<fieldset style="width:670px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" align="center">
				<thead>
				<tr>
					<td colspan="7"><strong>PO Summary</strong></td>
				</tr>
				<tr></tr>
                    <th width="30">Sl</th>
                    <th width="100">PO NO</th>
                    <th width="150">PO Type</th>
                    <th width="60">PO Date</th>
                    <th width="60">Item Description</th>
                    <th width="60">PO Qty</th>
                    <th width="60">UOM</th>
                    <th width="160">Supplier</th>
				</thead>
                <tbody>
                	<? $i=1;
					foreach ($main_data_sql as $row) { ?>                	
                	<tr>
                		<td><?= $i; ?></td>
                		<td><? echo $row[csf('booking_no')]; ?></td>
                		<td><? echo $is_short_arr[$row[csf('is_short')]]; ?></td>
                		<td><? echo  change_date_format($row[csf('booking_date')]); ?></td>
                		<td><? echo $row[csf('description')]; ?></td>
                		<td><? echo number_format($row[csf('wo_qnty')],2); ?></td>
                		<td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                		<td><? echo $lib_supplier_arr[$row[csf('supplier_id')]]; ?></td>
                	</tr>
                	<? 
                		$total_qty+=$row[csf('wo_qnty')];
						$i++;
                	}

                	 ?>
                </tbody>
                <tfoot>
                	<tr>
                		<td colspan="5"><strong>Total</strong></td>
                		<td><strong><?= number_format($total_qty,2) ?></strong></td>
                		<td></td>
                		<td></td>
                	</tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}
if($action=="trim_booking_inhouse_info2") //For Show 3 button
{
	echo load_html_head_contents("Received Details", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:870px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<caption align="center">Received Details</caption>
				
				<thead>
                    <th width="30">Sl</th>
                    <th width="80">Prod. ID</th>
                    <th width="100">Recv. ID</th>
                    <th width="100">PO</th>
                    <th width="100">Status</th>
                    <th width="100">PO/PI No</th>
                    <th width="100">Chalan No</th>
                    <th width="100">Recv. Date</th>
                    <th width="80">Item Description.</th>
                    <th width="80">Recv. Qty.</th>
                    <th width="80">Reject Qty.</th>
				</thead>
                <tbody>
                <?
					//$item_nameArr=explode("__",$item_name);
					//$item_name=$item_nameArr[0];
					//$item_desc=$item_nameArr[1];
					//echo $item_desc;
					$item_description_ref=explode("__",$item_name);
					$description="";
					foreach($item_description_ref as $des)
						{
							$description.="'".$des."',";
						}
						$description=chop($description,",");
					$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
					$i=1;
					//if($item_desc=='') $item_desc_cond=" and  b.item_description is null ";else $item_desc_cond="and  b.item_description='$item_desc'";
					if($item_desc=='') $item_desc_cond=" and  ((b.item_description is null ) or b.item_description=0)";else $item_desc_cond="and  b.item_description='$item_desc'";

					$po_in_sql=sql_select("select a.id,a.po_number,a.status_active from wo_po_break_down a where  a.id in($po_id)");
					foreach($po_in_sql as $row)
					{
						$po_no_arr[$row[csf('id')]]['po_number']=$row[csf('po_number')];
						$po_no_arr[$row[csf('id')]]['status_active']=$row[csf('status_active')];
					}
					$receive_rtn_data=array();
					$receive_rtn_qty_data=sql_select("select a.issue_number,a.issue_date,e.id,d.po_breakdown_id, c.item_group_id,sum(d.quantity) as quantity   from  inv_issue_master a,inv_transaction b, product_details_master c, order_wise_pro_details d,inv_receive_master e  where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and e.id=a.received_id   and b.transaction_type=3 and a.entry_form=49 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and d.po_breakdown_id in($po_id) and c.item_group_id in($item_name) and c.status_active=1 and c.is_deleted=0  group by a.issue_number,a.issue_date,e.id,d.po_breakdown_id, c.item_group_id order by c.item_group_id");

					foreach($receive_rtn_qty_data as $row)
					{
						$receive_rtn_data[$row[csf('id')]][issue_number]=$row[csf('issue_number')];
						$receive_rtn_data[$row[csf('id')]][issue_date]=$row[csf('issue_date')];
						$receive_rtn_data[$row[csf('id')]][quantity]=$row[csf('quantity')];
					}

					$receive_qty_data="SELECT a.id, c.po_breakdown_id,b.item_group_id,b.prod_id as prod_id,a.challan_no,b.item_description, a.recv_number, a.receive_date, SUM(c.quantity) as quantity, sum(reject_receive_qnty) as reject_receive_qnty, b.booking_no from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c , product_details_master d where a.id=b.mst_id  and a.entry_form=24 and  a.item_category=4  and b.id=c.dtls_id and b.prod_id=d.id and b.trans_id=c.trans_id and c.trans_type=1 and  c.po_breakdown_id in($po_id)  and b.item_group_id in($item_name) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by  c.po_breakdown_id,b.item_group_id,b.prod_id,a.id,b.item_description, a.recv_number,a.challan_no, a.receive_date, b.booking_no";
					//echo $receive_qty_data; die;

					$dtlsArray=sql_select($receive_qty_data);

					foreach($dtlsArray as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";

						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="80"><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('recv_number')]; ?></p></td>                
                            <td width="100" align="center"><p><? echo $po_no_arr[$row[csf('po_breakdown_id')]]['po_number']; ?></p></td>
                            <td width="100" align="center"><p><? echo $row_status[$po_no_arr[$row[csf('po_breakdown_id')]]['status_active']]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('booking_no')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="100" align="center"><p><? echo  change_date_format($row[csf('receive_date')]); ?></p></td>
                            <td width="80" align="center"><p><? echo $row[csf('item_description')]; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                             <td width="80" align="right"><p><? echo number_format($row[csf('reject_receive_qnty')],2); ?></p></td>
                        </tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$tot_rej_qty+=$row[csf('reject_receive_qnty')];
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="8" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_qty,2); ?></td>
                         <td><? echo number_format($tot_rej_qty,2); ?></td>
                    </tr>
                </tfoot>
            </table>
            <? //die; ?>
            <table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center" style="margin-top: 10px;">
            	<caption align="center">Received Return Details</caption>
				<thead>
                    <th width="30">Sl</th>
                    <th width="80">Prod. ID</th>
                    <th width="100">Return. ID</th>
                    <th width="100">Chalan No</th>
                    <th width="100">Return Date</th>
                    <th width="80">Item Description.</th>
                    <th width="160">Return Qty.</th>
				</thead>
                <tbody>
                <?
					$receive_rtn_qty_data=sql_select("select a.issue_number, a.issue_date , d.po_breakdown_id, c.item_group_id, d.quantity as quantity, b.prod_id, c.item_description
					from inv_issue_master a, inv_transaction b, product_details_master c, order_wise_pro_details d
					where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and b.transaction_type=3 and d.trans_type=3 and a.entry_form=49 and d.entry_form=49 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0  and d.status_active=1 and d.is_deleted=0 and d.po_breakdown_id in($po_id) and c.item_group_id='$item_name'");


					foreach($receive_rtn_qty_data as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";

						$qty=0;
						$qty=$row[csf('quantity')];
						//$qty=$row[csf('quantity')]/$item_arr[$row[csf('item_group_id')]]['order_uom'];
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="80"><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="100" align="center"><p><? echo  change_date_format($row[csf('issue_date')]); ?></p></td>
                            <td width="80" align="center"><p><? echo $row[csf('item_description')]; ?></p></td>
                            <td width="160" align="right"><p><? echo number_format($qty,2); ?></p></td>
                        </tr>
						<?
						$tot_rtn_qty+=$qty;
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="5" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_rtn_qty,2); ?></td>
                    </tr>
                    <tr class="tbl_bottom">
                    	<td colspan="5" align="right"></td>
                        <td align="right"> Balance</td>
                        <td><? echo number_format($tot_qty-$tot_rtn_qty,2); ?></td>
                    </tr>
                </tfoot>
            </table>
 <? die; ?>
            <table border="1" class="rpt_table" rules="all" width="870" cellpadding="0" cellspacing="0" align="center" style="margin-top: 10px;">
            	<caption align="center">Transfer In</caption>
				<thead>
                    <th width="30">Sl</th>
                    <th width="60">Prod. ID</th>
                    <th width="120">Transfer Id</th>
                    <th width="60">Transfer Date</th>
                    <th width="100">From Order</th>
                    <th width="80">Internal ref</th>
                    <th width="160">Item Description</th>
                    <th width="80">Transfer Qnty</th>
                    <th >Remarks</th>
				</thead>
                <tbody>
                <?
                	
					$transfer_sql="SELECT  a.from_order_id,
									       a.to_order_id,
									       b.item_group,
									       b.transfer_qnty as qnty,
									       b.from_prod_id as prod_id,
									       a.transfer_system_id as system_no,
									       a.transfer_date,
									       b.remarks
										FROM inv_item_transfer_mst a, inv_item_transfer_dtls b
										WHERE a.id = b.mst_id and a.to_order_id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_group=$item_name";
					//echo $transfer_sql;
				    $transfer_res=sql_select($transfer_sql);

				    $prod_id_arr=array();
				    $order_id_arr=array();
				    foreach($transfer_res as $row)
					{
				    	array_push($prod_id_arr, $row[csf('prod_id')]);
				    	array_push($order_id_arr, $row[csf('from_order_id')]);
				    	array_push($order_id_arr, $row[csf('to_order_id')]);
				    }
				    $prod_cond=where_con_using_array($prod_id_arr,0,"id");
				    $order_id_cond=where_con_using_array($order_id_arr,0,"id");
				    //echo "SELECT grouping,id,po_number from wo_po_break_down where 1=1 $order_id_cond";
				    $order_sql=sql_select("SELECT grouping,id,po_number from wo_po_break_down where 1=1 $order_id_cond");
				    $order_data=array();
				    foreach ($order_sql as $row) 
				    {
				    	$order_data[$row[csf('id')]]['grouping']=$row[csf('grouping')];
				    	$order_data[$row[csf('id')]]['po_number']=$row[csf('po_number')];
				    }

				    $product_arr = return_library_array("select id, product_name_details from product_details_master where status_active=1 and is_deleted=0 $prod_cond","id","product_name_details");
				    $total_trans_in=0;
					foreach($transfer_res as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";

						$qty=0;
						$qty=$row[csf('qnty')];
						
						?>

                  
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td ><p><? echo $i; ?></p></td>
                            <td ><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td  align="center"><p><? echo $row[csf('system_no')]; ?></p></td>
                            <td  align="center"><p><? echo  change_date_format($row[csf('transfer_date')]); ?></p></td>
                            <td  align="center"><p><? echo $order_data[$row[csf('from_order_id')]]['po_number']; ?></p></td>
                            <td  align="center"><p><? echo $order_data[$row[csf('from_order_id')]]['grouping']; ?></p></td>
                            <td  align="center"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
                            <td  align="right"><p><? echo number_format($qty,2); ?></p></td>
                            <td  align="center"><p><? echo $row[csf('remarks')]; ?></p></td>
                        </tr>
						<?
						$total_trans_in+=$qty;
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="6" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($total_trans_in,2); ?></td>
                        <td></td>
                    </tr>
                    <tr class="tbl_bottom">
                    	<td colspan="6" align="right"></td>
                        <td align="right">Total Balance</td>
                        <td><? echo number_format($tot_qty+$total_trans_in-$tot_rtn_qty,2); ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}
if($action=="trim_booking_inhouse_info")
{
	echo load_html_head_contents("Received Details", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:870px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<caption align="center">Received Details</caption>
				
				<thead>
                    <th width="30">Sl</th>
                    <th width="80">Prod. ID</th>
                    <th width="100">Recv. ID</th>
                    <th width="100">PO</th>
                    <th width="100">Status</th>
                    <th width="100">PO/PI No</th>
                    <th width="100">Chalan No</th>
                    <th width="100">Recv. Date</th>
                    <th width="80">Item Description.</th>
                    <th width="80">Recv. Qty.</th>
                    <th width="80">Reject Qty.</th>
				</thead>
                <tbody>
                <?
					$item_nameArr=explode("__",$item_name);
					$item_name=$item_nameArr[0];
					$item_desc=$item_nameArr[1];
					$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
					$i=1;
					//if($item_desc=='') $item_desc_cond=" and  b.item_description is null ";else $item_desc_cond="and  b.item_description='$item_desc'";
					if($item_desc=='') $item_desc_cond=" and  ((b.item_description is null ) or b.item_description=0)";else $item_desc_cond="and  b.item_description='$item_desc'";

					$po_in_sql=sql_select("select a.id,a.po_number,a.status_active from wo_po_break_down a where  a.id in($po_id)");
					foreach($po_in_sql as $row)
					{
						$po_no_arr[$row[csf('id')]]['po_number']=$row[csf('po_number')];
						$po_no_arr[$row[csf('id')]]['status_active']=$row[csf('status_active')];
					}
					$receive_rtn_data=array();
					$receive_rtn_qty_data=sql_select("select a.issue_number,a.issue_date,e.id,d.po_breakdown_id, c.item_group_id,sum(d.quantity) as quantity   from  inv_issue_master a,inv_transaction b, product_details_master c, order_wise_pro_details d,inv_receive_master e  where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and e.id=a.received_id   and b.transaction_type=3 and a.entry_form=49 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and d.po_breakdown_id in($po_id) and c.item_group_id='$item_name' and c.status_active=1 and c.is_deleted=0  group by a.issue_number,a.issue_date,e.id,d.po_breakdown_id, c.item_group_id order by c.item_group_id");

					foreach($receive_rtn_qty_data as $row)
					{
						$receive_rtn_data[$row[csf('id')]][issue_number]=$row[csf('issue_number')];
						$receive_rtn_data[$row[csf('id')]][issue_date]=$row[csf('issue_date')];
						$receive_rtn_data[$row[csf('id')]][quantity]=$row[csf('quantity')];
					}

					$receive_qty_data="SELECT a.id, c.po_breakdown_id,b.item_group_id,b.prod_id as prod_id,a.challan_no,b.item_description, a.recv_number, a.receive_date, SUM(c.quantity) as quantity, sum(reject_receive_qnty) as reject_receive_qnty, b.booking_no from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c , product_details_master d
					where a.id=b.mst_id  and a.entry_form=24 and  a.item_category=4  and b.id=c.dtls_id and b.prod_id=d.id and b.trans_id=c.trans_id and c.trans_type=1 and  c.po_breakdown_id in($po_id)  and b.item_group_id='$item_name'  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by  c.po_breakdown_id,b.item_group_id,b.prod_id,a.id,b.item_description, a.recv_number,a.challan_no, a.receive_date, b.booking_no";


				


					$dtlsArray=sql_select($receive_qty_data);

					foreach($dtlsArray as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";

						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="80"><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('recv_number')]; ?></p></td>                
                            <td width="100" align="center"><p><? echo $po_no_arr[$row[csf('po_breakdown_id')]]['po_number']; ?></p></td>
                            <td width="100" align="center"><p><? echo $row_status[$po_no_arr[$row[csf('po_breakdown_id')]]['status_active']]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('booking_no')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="100" align="center"><p><? echo  change_date_format($row[csf('receive_date')]); ?></p></td>
                            <td width="80" align="center"><p><? echo $row[csf('item_description')]; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                             <td width="80" align="right"><p><? echo number_format($row[csf('reject_receive_qnty')],2); ?></p></td>
                        </tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$tot_rej_qty+=$row[csf('reject_receive_qnty')];
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="8" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_qty,2); ?></td>
                         <td><? echo number_format($tot_rej_qty,2); ?></td>
                    </tr>
                </tfoot>
            </table>
            <? die; ?>
            <table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center" style="margin-top: 10px;">
            	<caption align="center">Received Return Details</caption>
				<thead>
                    <th width="30">Sl</th>
                    <th width="80">Prod. ID</th>
                    <th width="100">Return. ID</th>
                    <th width="100">Chalan No</th>
                    <th width="100">Return Date</th>
                    <th width="80">Item Description.</th>
                    <th width="160">Return Qty.</th>
				</thead>
                <tbody>
                <?
					$receive_rtn_qty_data=sql_select("select a.issue_number, a.issue_date , d.po_breakdown_id, c.item_group_id, d.quantity as quantity, b.prod_id, c.item_description
					from inv_issue_master a, inv_transaction b, product_details_master c, order_wise_pro_details d
					where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and b.transaction_type=3 and d.trans_type=3 and a.entry_form=49 and d.entry_form=49 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and d.po_breakdown_id in($po_id) and c.item_group_id='$item_name'");


					foreach($receive_rtn_qty_data as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";

						$qty=0;
						$qty=$row[csf('quantity')];
						//$qty=$row[csf('quantity')]/$item_arr[$row[csf('item_group_id')]]['order_uom'];
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="80"><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="100" align="center"><p><? echo  change_date_format($row[csf('issue_date')]); ?></p></td>
                            <td width="80" align="center"><p><? echo $row[csf('item_description')]; ?></p></td>
                            <td width="160" align="right"><p><? echo number_format($qty,2); ?></p></td>
                        </tr>
						<?
						$tot_rtn_qty+=$qty;
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="5" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_rtn_qty,2); ?></td>
                    </tr>
                    <tr class="tbl_bottom">
                    	<td colspan="5" align="right"></td>
                        <td align="right"> Balance</td>
                        <td><? echo number_format($tot_qty-$tot_rtn_qty,2); ?></td>
                    </tr>
                </tfoot>
            </table>

            <table border="1" class="rpt_table" rules="all" width="870" cellpadding="0" cellspacing="0" align="center" style="margin-top: 10px;">
            	<caption align="center">Transfer In</caption>
				<thead>
                    <th width="30">Sl</th>
                    <th width="60">Prod. ID</th>
                    <th width="120">Transfer Id</th>
                    <th width="60">Transfer Date</th>
                    <th width="100">From Order</th>
                    <th width="80">Internal ref</th>
                    <th width="160">Item Description</th>
                    <th width="80">Transfer Qnty</th>
                    <th >Remarks</th>
				</thead>
                <tbody>
                <?
                	
					$transfer_sql="SELECT  a.from_order_id,
									       a.to_order_id,
									       b.item_group,
									       b.transfer_qnty as qnty,
									       b.from_prod_id as prod_id,
									       a.transfer_system_id as system_no,
									       a.transfer_date,
									       b.remarks
										FROM inv_item_transfer_mst a, inv_item_transfer_dtls b
										WHERE a.id = b.mst_id and a.to_order_id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_group=$item_name";
					//echo $transfer_sql;
				    $transfer_res=sql_select($transfer_sql);

				    $prod_id_arr=array();
				    $order_id_arr=array();
				    foreach($transfer_res as $row)
					{
				    	array_push($prod_id_arr, $row[csf('prod_id')]);
				    	array_push($order_id_arr, $row[csf('from_order_id')]);
				    	array_push($order_id_arr, $row[csf('to_order_id')]);
				    }
				    $prod_cond=where_con_using_array($prod_id_arr,0,"id");
				    $order_id_cond=where_con_using_array($order_id_arr,0,"id");
				    //echo "SELECT grouping,id,po_number from wo_po_break_down where 1=1 $order_id_cond";
				    $order_sql=sql_select("SELECT grouping,id,po_number from wo_po_break_down where 1=1 $order_id_cond");
				    $order_data=array();
				    foreach ($order_sql as $row) 
				    {
				    	$order_data[$row[csf('id')]]['grouping']=$row[csf('grouping')];
				    	$order_data[$row[csf('id')]]['po_number']=$row[csf('po_number')];
				    }

				    $product_arr = return_library_array("select id, product_name_details from product_details_master where status_active=1 and is_deleted=0 $prod_cond","id","product_name_details");
				    $total_trans_in=0;
					foreach($transfer_res as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";

						$qty=0;
						$qty=$row[csf('qnty')];
						
						?>

                  
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td ><p><? echo $i; ?></p></td>
                            <td ><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td  align="center"><p><? echo $row[csf('system_no')]; ?></p></td>
                            <td  align="center"><p><? echo  change_date_format($row[csf('transfer_date')]); ?></p></td>
                            <td  align="center"><p><? echo $order_data[$row[csf('from_order_id')]]['po_number']; ?></p></td>
                            <td  align="center"><p><? echo $order_data[$row[csf('from_order_id')]]['grouping']; ?></p></td>
                            <td  align="center"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
                            <td  align="right"><p><? echo number_format($qty,2); ?></p></td>
                            <td  align="center"><p><? echo $row[csf('remarks')]; ?></p></td>
                        </tr>
						<?
						$total_trans_in+=$qty;
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="6" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($total_trans_in,2); ?></td>
                        <td></td>
                    </tr>
                    <tr class="tbl_bottom">
                    	<td colspan="6" align="right"></td>
                        <td align="right">Total Balance</td>
                        <td><? echo number_format($tot_qty+$total_trans_in-$tot_rtn_qty,2); ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}
if($action=="trim_booking_issueRtn_info")
{
	echo load_html_head_contents("Received Details", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:870px; margin-left:3px">
		<div id="scroll_body" align="center">
             <table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" align="left">
            <caption> Return Details</caption>
				<thead>
                    <th width="30">Sl</th>
                    <th width="70">Prod. ID</th>
                    <th width="120">Return. ID</th>
                     <th width="100">Chalan No</th>
                     <th width="80">Return Date</th>
                    <th width="170">Item Description.</th>
                    <th >Return. Qty.</th>
				</thead>
                <tbody>
                <?
					$k=1;$ret_tot_qty=0;

					$item_nameArr=explode("__",$item_name);
					$item_name=$item_nameArr[0];
					$item_desc=$item_nameArr[1];
					$conversion_factor_array=array();	$item_arr=array();
					$conversion_factor=sql_select("select id ,trim_uom,order_uom,conversion_factor from  lib_item_group  ");
					foreach($conversion_factor as $row_f)
					{
					 $conversion_factor_array[$row_f[csf('id')]]['con_factor']=$row_f[csf('conversion_factor')];
					 $conversion_factor_array[$row_f[csf('id')]]['cons_uom']=$row_f[csf('trim_uom')];
					 $item_arr[$row_f[csf('id')]]['order_uom']=$row_f[csf('order_uom')];
					}
					$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );

				 $mrr_sql_ret="SELECT a.id, a.recv_number,a.challan_no,b.prod_id, p.item_group_id,a.receive_date,p.product_name_details,SUM(c.quantity) as quantity from   inv_receive_master a,inv_transaction b, order_wise_pro_details c,product_details_master p where a.id=b.mst_id  and a.entry_form=73 and c.entry_form=73 and p.id=b.prod_id  and b.id=c.trans_id and b.transaction_type=4 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and  c.po_breakdown_id in($po_id) and p.item_group_id='$item_name' and p.status_active=1 and p.is_deleted=0 group by a.id, a.recv_number, a.challan_no, b.prod_id, p.item_group_id, a.receive_date, p.product_name_details "; 
				 $dtlsArray_data=sql_select($mrr_sql_ret);

					foreach($dtlsArray_data as $row)
					{
						if ($k%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
							$conv_fact=$conversion_factor_array[$row[csf('item_group_id')]]['con_factor'];

						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k;?>">
							<td ><p><? echo $k; ?></p></td>
                            <td  align="center"><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td ><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td  align="center"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td  align="center"><p><? echo  change_date_format($row[csf('receive_date')]); ?></p></td>
                            <td  align="center"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                            <td  align="right"><p><? echo number_format($row[csf('quantity')]/$conv_fact,2); ?></p></td>
                        </tr>
						<?
						$ret_tot_qty+=$row[csf('quantity')]/$conv_fact;
						$k++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="5" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($ret_tot_qty,2); ?></td>
                    </tr>

                    
                </tfoot>
            </table>  
        </div>
    </fieldset>
    <?
	exit();
}
if($action=="trim_booking_transfIn_info")
{
	echo load_html_head_contents("Received Details", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:870px; margin-left:3px">
		<div id="scroll_body" align="center">
			
            <table border="1" class="rpt_table" rules="all" width="870" cellpadding="0" cellspacing="0" align="center" style="margin-top: 10px;">
            	<caption align="center">Transfer In</caption>
				<thead>
                    <th width="30">Sl</th>
                    <th width="60">Prod. ID</th>
                    <th width="120">Transfer Id</th>
                    <th width="60">Transfer Date</th>
                    <th width="100">From Order</th>
                    <th width="80">Internal ref</th>
                    <!-- <th width="160">Item Description</th> -->
                    <th width="80">Transfer Qnty</th>
                    <th >Remarks</th>
				</thead>
                <tbody>
                <?
                	
				
					/*$transfer_sql="SELECT  a.from_order_id,
									       a.to_order_id,
									       b.item_group,
									       b.transfer_qnty as qnty,
									       b.from_prod_id as prod_id,
									       a.transfer_system_id as system_no,
									       a.transfer_date,
									       b.remarks
										FROM inv_item_transfer_mst a, inv_item_transfer_dtls b
										WHERE a.id = b.mst_id and a.to_order_id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_group=$item_name";*/


					$transfer_sql = "SELECT a.from_order_id,a.to_order_id,a.from_prod_id as prod_id,d.transfer_system_id as system_no,d.transfer_date, d.remarks ,b.po_breakdown_id,p.item_group_id as item_group,p.item_description as item_description, (b.quantity) as qnty, a.rate, e.job_id from inv_item_transfer_mst d,inv_item_transfer_dtls a,inv_transaction f, order_wise_pro_details b,product_details_master p, wo_po_break_down e where d.id=a.mst_id and a.mst_id=f.mst_id and f.id=b.trans_id and a.id=b.dtls_id and b.prod_id=p.id and f.prod_id=p.id and a.to_prod_id=p.id and b.po_breakdown_id=e.id  and b.trans_type=5 and b.entry_form=112 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and p.status_active=1 and p.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and f.item_category=4 and b.po_breakdown_id in($po_id) and p.item_group_id in($item_name)";

					//echo $transfer_sql;
				    $transfer_res=sql_select($transfer_sql);

				    $prod_id_arr=array();
				    $order_id_arr=array();
				    foreach($transfer_res as $row)
					{
				    	array_push($prod_id_arr, $row[csf('prod_id')]);
				    	array_push($order_id_arr, $row[csf('from_order_id')]);
				    	array_push($order_id_arr, $row[csf('to_order_id')]);
				    }
				    $prod_cond=where_con_using_array($prod_id_arr,0,"id");
				    $order_id_cond=where_con_using_array($order_id_arr,0,"id");
				    //echo "SELECT grouping,id,po_number from wo_po_break_down where 1=1 $order_id_cond";
				    $order_sql=sql_select("SELECT grouping,id,po_number from wo_po_break_down where 1=1 $order_id_cond");
				    $order_data=array();
				    foreach ($order_sql as $row) 
				    {
				    	$order_data[$row[csf('id')]]['grouping']=$row[csf('grouping')];
				    	$order_data[$row[csf('id')]]['po_number']=$row[csf('po_number')];
				    }

				    $product_arr = return_library_array("select id, product_name_details from product_details_master where status_active=1 and is_deleted=0 $prod_cond","id","product_name_details");
				    $total_trans_in=0;
					foreach($transfer_res as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";

						$qty=0;
						$qty=$row[csf('qnty')];
						
						?>

                  
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td ><p><? echo $i; ?></p></td>
                            <td ><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td  align="center"><p><? echo $row[csf('system_no')]; ?></p></td>
                            <td  align="center"><p><? echo  change_date_format($row[csf('transfer_date')]); ?></p></td>
                            <td  align="center"><p><? echo $order_data[$row[csf('from_order_id')]]['po_number']; ?></p></td>
                            <td  align="center"><p><? echo $order_data[$row[csf('from_order_id')]]['grouping']; ?></p></td>
                            <!-- <td  align="center"><p><? //echo $product_arr[$row[csf('prod_id')]]; ?></p></td> -->
                            <td  align="right"><p><? echo number_format($qty,2); ?></p></td>
                            <td  align="center"><p><? echo $row[csf('remarks')]; ?></p></td>
                        </tr>
						<?
						$total_trans_in+=$qty;
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="5" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($total_trans_in,2); ?></td>
                        <td></td>
                    </tr>
                    <tr class="tbl_bottom">
                    	<td colspan="5" align="right"></td>
                        <td align="right">Total Balance</td>
                        <td><? echo number_format($tot_qty+$total_trans_in-$tot_rtn_qty,2); ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}
if($action=="trim_booking_issue_info")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	
	<fieldset style="width:870px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" align="left">
				 <caption>Issue Details </caption>
                <thead>
                    <th width="30">Sl</th>
                    <th width="80">Prod. ID</th>
                    <th width="120">Issue. ID</th>
                     <th width="90">Chalan No</th>
                     <th width="70">Issue. Date</th>
                    <th width="170">Item Description.</th>
                    <th >Issue. Qty.</th>
				</thead>
                <tbody>
                <?
					$item_nameArr=explode("__",$item_name);
					$item_name=$item_nameArr[0];
					$item_desc=$item_nameArr[1];
					
					$conversion_factor_array=array();	$item_arr=array();
					$conversion_factor=sql_select("select id ,trim_uom,order_uom,conversion_factor from  lib_item_group  ");
					foreach($conversion_factor as $row_f)
					{
					 $conversion_factor_array[$row_f[csf('id')]]['con_factor']=$row_f[csf('conversion_factor')];
					 $conversion_factor_array[$row_f[csf('id')]]['cons_uom']=$row_f[csf('trim_uom')];
					 $item_arr[$row_f[csf('id')]]['order_uom']=$row_f[csf('order_uom')];
					}
					$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
					$i=1;
					$description_arr=explode(",",$item_desc);
					foreach($description_arr as $des){
						$trim_des_arr[]=trim($des);
					}
					$item_desc_cond='';
					if(count($trim_des_arr)>0){
						$item_desc_cond = where_con_using_array($trim_des_arr,1,"p.item_description");
						//if($item_desc=='') $item_desc_cond="";else $item_desc_cond="and  p.item_description='$item_desc'";
					}

				 $mrr_sql=("SELECT a.id, a.issue_number,a.challan_no,b.prod_id,p.item_group_id, a.issue_date,p.item_description,SUM(c.quantity) as quantity
					from  inv_issue_master a,inv_trims_issue_dtls b, order_wise_pro_details c,product_details_master p
					where a.id=b.mst_id  and a.entry_form=25 and p.id=b.prod_id and b.id=c.dtls_id and b.trans_id=c.trans_id and c.trans_type=2 and a.is_deleted=0 and a.status_active=1 and
					b.status_active=1 and b.is_deleted=0 and  c.po_breakdown_id in($po_id) and p.item_group_id='$item_name' and p.status_active=1 and p.is_deleted=0$item_desc_cond  group by c.po_breakdown_id,p.item_group_id,p.item_description,a.issue_number,a.id,a.issue_date,b.prod_id,a.challan_no ");
					$dtlsArray=sql_select($mrr_sql);
	
			
			if(count($dtlsArray)<=0)
			{		
			$general_item_issue_sql="select e.issue_number,e.challan_no,e.issue_date, b.prod_id,a.item_group_id as item_group_id, a.item_description as item_description, b.cons_quantity as quantity, b.cons_amount
			from product_details_master a, inv_transaction b, wo_po_break_down c, wo_po_details_master d,inv_issue_master e 
			where a.id=b.prod_id and b.order_id=c.id and c.job_no_mst=d.job_no and e.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   and b.transaction_type=2 and c.id in($po_id) and a.item_group_id='$item_name'";
			 $dtlsArray=sql_select($general_item_issue_sql);
			}

					foreach($dtlsArray as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
							$conv_fact=$conversion_factor_array[$row[csf('item_group_id')]]['con_factor'];

						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td ><p><? echo $i; ?></p></td>
                            <td  align="center"><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td ><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td  align="center"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td  align="center"><p><? echo  change_date_format($row[csf('issue_date')]); ?></p></td>
                            <td  align="center"><p><? echo $row[csf('item_description')]; ?></p></td>
                            <td align="right"><p><? echo number_format($row[csf('quantity')]/$conv_fact,2); ?></p></td>
                        </tr>
						<?
						$tot_qty+=$row[csf('quantity')]/$conv_fact;
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="5" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_qty,2); ?></td>
                    </tr>
                </tfoot>
            </table>
            <br>
            <table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" align="left">
            <caption> Return Details</caption>
				<thead>
                    <th width="30">Sl</th>
                    <th width="70">Prod. ID</th>
                    <th width="120">Return. ID</th>
                     <th width="100">Chalan No</th>
                     <th width="80">Return Date</th>
                    <th width="170">Item Description.</th>
                    <th >Return. Qty.</th>
				</thead>
                <tbody>
                <?
					$k=1;$ret_tot_qty=0;

				 $mrr_sql_ret="SELECT a.id, a.recv_number,a.challan_no,b.prod_id, p.item_group_id,a.receive_date,p.product_name_details,SUM(c.quantity) as quantity from   inv_receive_master a,inv_transaction b, order_wise_pro_details c,product_details_master p where a.id=b.mst_id  and a.entry_form=73 and c.entry_form=73 and p.id=b.prod_id  and b.id=c.trans_id and b.transaction_type=4 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and  c.po_breakdown_id in($po_id) and p.item_group_id='$item_name' and p.status_active=1 and p.is_deleted=0 group by a.id, a.recv_number, a.challan_no, b.prod_id, p.item_group_id, a.receive_date, p.product_name_details "; 
				 $dtlsArray_data=sql_select($mrr_sql_ret);

					foreach($dtlsArray_data as $row)
					{
						if ($k%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
							$conv_fact=$conversion_factor_array[$row[csf('item_group_id')]]['con_factor'];

						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k;?>">
							<td ><p><? echo $k; ?></p></td>
                            <td  align="center"><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td ><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td  align="center"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td  align="center"><p><? echo  change_date_format($row[csf('receive_date')]); ?></p></td>
                            <td  align="center"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                            <td  align="right"><p><? echo number_format($row[csf('quantity')]/$conv_fact,2); ?></p></td>
                        </tr>
						<?
						$ret_tot_qty+=$row[csf('quantity')]/$conv_fact;
						$k++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="5" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($ret_tot_qty,2); ?></td>
                    </tr>

                    <tr class="tbl_bottom">
                    	<td colspan="5" align="right"></td>
                        <td align="right">Total Balance</td>
                        <td><? echo number_format($tot_qty-$ret_tot_qty,2); ?></td>
                    </tr>
                </tfoot>
            </table>

            <table border="1" class="rpt_table" rules="all" width="870" cellpadding="0" cellspacing="0" align="left" style="margin-top: 10px;">
              <caption align="center">Transfer Out</caption>
		        <thead>
		                    <th width="30">Sl</th>
		                    <th width="60">Prod. ID</th>
		                    <th width="120">Transfer Id</th>
		                    <th width="60">Transfer Date</th>
		                    <th width="100">To Order</th>
		                    <th width="80">Internal ref</th>
		                    <th width="160">Item Description</th>
		                    <th width="80">Transfer Qnty</th>
		                    <th >Remarks</th>
		        </thead>
		                <tbody>
		                <?
		                  
		          $transfer_sql="SELECT  a.from_order_id,
		                         a.to_order_id,
		                         b.item_group,
		                         b.transfer_qnty as qnty,
		                         b.from_prod_id as prod_id,
		                         a.transfer_system_id as system_no,
		                         a.transfer_date,
		                         b.remarks
		                    FROM inv_item_transfer_mst a, inv_item_transfer_dtls b
		                    WHERE a.id = b.mst_id and a.from_order_id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_group=$item_name";
		            $transfer_res=sql_select($transfer_sql);

		            $prod_id_arr=array();
		            $order_id_arr=array();
		            foreach($transfer_res as $row)
		          	{
		              array_push($prod_id_arr, $row[csf('prod_id')]);
		              array_push($order_id_arr, $row[csf('from_order_id')]);
		              array_push($order_id_arr, $row[csf('to_order_id')]);
		            }
		            $prod_cond=where_con_using_array($prod_id_arr,0,"id");
		            $order_id_cond=where_con_using_array($order_id_arr,0,"id");
		            $order_sql=sql_select("SELECT grouping,id,po_number from wo_po_break_down where 1=1 $order_id_cond");
		            $order_data=array();
		            foreach ($order_sql as $row) 
		            {
		              $order_data[$row[csf('id')]]['grouping']=$row[csf('grouping')];
		              $order_data[$row[csf('id')]]['po_number']=$row[csf('po_number')];
		            }

		            $product_arr = return_library_array("select id, product_name_details from product_details_master where status_active=1 and is_deleted=0 $prod_cond","id","product_name_details");
		            $total_trans_in=0;
		          foreach($transfer_res as $row)
		          {
		            if ($i%2==0)
		              $bgcolor="#E9F3FF";
		            else
		              $bgcolor="#FFFFFF";

		            $qty=0;
		            $qty=$row[csf('qnty')];
		            
		            ?>

		                  
		            <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
		              <td ><p><? echo $i; ?></p></td>
		                            <td ><p><? echo $row[csf('prod_id')]; ?></p></td>
		                            <td  align="center"><p><? echo $row[csf('system_no')]; ?></p></td>
		                            <td  align="center"><p><? echo  change_date_format($row[csf('transfer_date')]); ?></p></td>
		                            <td  align="center"><p><? echo $order_data[$row[csf('to_order_id')]]['po_number']; ?></p></td>
		                            <td  align="center"><p><? echo $order_data[$row[csf('to_order_id')]]['grouping']; ?></p></td>
		                            <td  align="center"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
		                            <td  align="right"><p><? echo number_format($qty,2); ?></p></td>
		                            <td  align="center"><p><? echo $row[csf('remarks')]; ?></p></td>
		                        </tr>
		            <?
		            $total_trans_in+=$qty;
		            $i++;
		          }
		        ?>
                </tbody>
                <tfoot>
                  <tr class="tbl_bottom">
                      <td colspan="6" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($total_trans_in,2); ?></td>
                        <td></td>
                    </tr>
                    <tr class="tbl_bottom">
                      <td colspan="6" align="right"></td>
                        <td align="right">Balance</td>
                        <td><? echo number_format($tot_qty+$total_trans_in-$ret_tot_qty,2); ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table> 
        </div>
    </fieldset>
    <?
	exit();
}
if($action=="trim_booking_recvRtn_info")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	
	<fieldset style="width:870px; margin-left:3px">
		<div id="scroll_body" align="center">
            <table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center" style="margin-top: 10px;">
            	<caption align="center">Received Return Details</caption>
				<thead>
                    <th width="30">Sl</th>
                    <th width="80">Prod. ID</th>
                    <th width="100">Return. ID</th>
                    <th width="100">Chalan No</th>
                    <th width="100">Return Date</th>
                    <th width="80">Item Description.</th>
                    <th width="160">Return Qty.</th>
				</thead>
                <tbody>
                <?

                	$item_nameArr=explode("__",$item_name);
					$item_name=$item_nameArr[0];
					$item_desc=$item_nameArr[1];
					$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
					$i=1;
					//if($item_desc=='') $item_desc_cond=" and  b.item_description is null ";else $item_desc_cond="and  b.item_description='$item_desc'";
					if($item_desc=='') $item_desc_cond=" and  ((b.item_description is null ) or b.item_description=0)";else $item_desc_cond="and  b.item_description='$item_desc'";
					$receive_rtn_qty_data=sql_select("select a.issue_number, a.issue_date , d.po_breakdown_id, c.item_group_id, d.quantity as quantity, b.prod_id, c.item_description
					from inv_issue_master a, inv_transaction b, product_details_master c, order_wise_pro_details d
					where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and b.transaction_type=3 and d.trans_type=3 and a.entry_form=49 and d.entry_form=49 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and d.po_breakdown_id in($po_id) and c.item_group_id='$item_name' and c.status_active=1 and c.is_deleted=0");


					foreach($receive_rtn_qty_data as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";

						$qty=0;
						$qty=$row[csf('quantity')];
						//$qty=$row[csf('quantity')]/$item_arr[$row[csf('item_group_id')]]['order_uom'];
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="80"><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="100" align="center"><p><? echo  change_date_format($row[csf('issue_date')]); ?></p></td>
                            <td width="80" align="center"><p><? echo $row[csf('item_description')]; ?></p></td>
                            <td width="160" align="right"><p><? echo number_format($qty,2); ?></p></td>
                        </tr>
						<?
						$tot_rtn_qty+=$qty;
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="5" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_rtn_qty,2); ?></td>
                    </tr>
                   
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}
if($action=="trim_booking_trnsfOut_info")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	
	<fieldset style="width:870px; margin-left:3px">
		<div id="scroll_body" align="center">
            <table border="1" class="rpt_table" rules="all" width="870" cellpadding="0" cellspacing="0" align="left" style="margin-top: 10px;">
              <caption align="center">Transfer Out</caption>
		        <thead>
		                    <th width="30">Sl</th>
		                    <th width="60">Prod. ID</th>
		                    <th width="120">Transfer Id</th>
		                    <th width="60">Transfer Date</th>
		                    <th width="100">To Order</th>
		                    <th width="80">Internal ref</th>
		                    <!-- <th width="160">Item Description</th> -->
		                    <th width="80">Transfer Qnty</th>
		                    <th >Remarks</th>
		        </thead>
		                <tbody>
		                <?

		                $item_nameArr=explode("__",$item_name);
						$item_name=$item_nameArr[0];
						$item_desc=$item_nameArr[1];
						
						$conversion_factor_array=array();	$item_arr=array();
						$conversion_factor=sql_select("select id ,trim_uom,order_uom,conversion_factor from  lib_item_group  ");
						foreach($conversion_factor as $row_f)
						{
						 $conversion_factor_array[$row_f[csf('id')]]['con_factor']=$row_f[csf('conversion_factor')];
						 $conversion_factor_array[$row_f[csf('id')]]['cons_uom']=$row_f[csf('trim_uom')];
						 $item_arr[$row_f[csf('id')]]['order_uom']=$row_f[csf('order_uom')];
						}
						$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );


		                  
		          		/*$transfer_sql="SELECT a.from_order_id, a.to_order_id, d.item_group_id, b.transfer_qnty as qnty, b.from_prod_id as prod_id, a.transfer_system_id as system_no, a.transfer_date, b.remarks FROM inv_item_transfer_mst a, inv_item_transfer_dtls b,order_wise_pro_details c,product_details_master d WHERE a.id = b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and c.po_breakdown_id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.item_group_id=$item_name and c.trans_type=6";*/

		          		$transfer_sql ="SELECT a.from_order_id, a.to_order_id,b.po_breakdown_id,p.item_group_id, a.from_prod_id as prod_id,p.item_description as item_description, (b.quantity) as qnty, a.rate, e.job_id,d.transfer_system_id as system_no,d.transfer_date, a.remarks from inv_item_transfer_mst d,inv_item_transfer_dtls a,inv_transaction f, order_wise_pro_details b,product_details_master p, wo_po_break_down e where d.id=a.mst_id and a.mst_id=f.mst_id and f.id=b.trans_id and a.id=b.dtls_id and b.prod_id=p.id and f.prod_id=p.id and a.to_prod_id=p.id and b.po_breakdown_id=e.id  and b.trans_type=6 and b.entry_form=112 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and p.status_active=1 and p.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and f.item_category=4 and b.po_breakdown_id in($po_id) and p.item_group_id in($item_name)";



		            $transfer_res=sql_select($transfer_sql);

		            $prod_id_arr=array();
		            $order_id_arr=array();
		            foreach($transfer_res as $row)
		          	{
		              array_push($prod_id_arr, $row[csf('prod_id')]);
		              array_push($order_id_arr, $row[csf('from_order_id')]);
		              array_push($order_id_arr, $row[csf('to_order_id')]);
		            }
		            $prod_cond=where_con_using_array($prod_id_arr,0,"id");
		            $order_id_cond=where_con_using_array($order_id_arr,0,"id");
		            $order_sql=sql_select("SELECT grouping,id,po_number from wo_po_break_down where 1=1 $order_id_cond");
		            $order_data=array();
		            foreach ($order_sql as $row) 
		            {
		              $order_data[$row[csf('id')]]['grouping']=$row[csf('grouping')];
		              $order_data[$row[csf('id')]]['po_number']=$row[csf('po_number')];
		            }

		            $product_arr = return_library_array("select id, product_name_details from product_details_master where status_active=1 and is_deleted=0 $prod_cond","id","product_name_details");
		            $total_trans_in=0;
		          foreach($transfer_res as $row)
		          {
		            if ($i%2==0)
		              $bgcolor="#E9F3FF";
		            else
		              $bgcolor="#FFFFFF";

		            $qty=0;
		            $qty=$row[csf('qnty')];
		            
		            ?>

		                  
		            <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
		              <td ><p><? echo $i; ?></p></td>
		                            <td ><p><? echo $row[csf('prod_id')]; ?></p></td>
		                            <td  align="center"><p><? echo $row[csf('system_no')]; ?></p></td>
		                            <td  align="center"><p><? echo  change_date_format($row[csf('transfer_date')]); ?></p></td>
		                            <td  align="center"><p><? echo $order_data[$row[csf('to_order_id')]]['po_number']; ?></p></td>
		                            <td  align="center"><p><? echo $order_data[$row[csf('to_order_id')]]['grouping']; ?></p></td>
		                            <!-- <td  align="center"><p><? //echo $product_arr[$row[csf('prod_id')]]; ?></p></td> -->
		                            <td  align="right"><p><? echo number_format($qty,2); ?></p></td>
		                            <td  align="center"><p><? echo $row[csf('remarks')]; ?></p></td>
		                        </tr>
		            <?
		            $total_trans_in+=$qty;
		            $i++;
		          }
		        ?>
                </tbody>
                <tfoot>
                  <tr class="tbl_bottom">
                      <td colspan="5" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($total_trans_in,2); ?></td>
                        <td></td>
                    </tr>
                    <tr class="tbl_bottom">
                      <td colspan="5" align="right"></td>
                        <td align="right">Balance</td>
                        <td><? echo number_format($tot_qty+$total_trans_in-$ret_tot_qty,2); ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table> 
        </div>
    </fieldset>
    <?
	exit();
}
disconnect($con);
?>