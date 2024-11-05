<?
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
if($action=="print_button_variable_setting")
    {
        $print_report_format_arr = return_library_array("select format_id,format_id from lib_report_template where template_name =$data and module_id=2 and report_id=75 and is_deleted=0 and status_active=1","format_id","format_id");
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

	if($txt_job_no!="" || $txt_job_no!=0) $jobcond="and a.job_no_prefix_num in('".$txt_job_no."')"; else $jobcond="";
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

	$main_data_sql=sql_select("SELECT a.id, a.job_no, a.buyer_name, a.job_no_prefix_num, a.season_buyer_wise, a.style_ref_no, a.job_quantity, a.order_uom, b.id as po_id,  c.color_number_id, c.order_quantity, e.id as fabric_cost_id, e.lib_yarn_count_deter_id, e.fabric_description, e.uom as fabric_uom, avg(f.requirment) as avg_cons, e.color_size_sensitive, e.color, e.color_break_down, g.contrast_color_id, h.stripe_color from wo_po_details_master a join wo_po_break_down b on a.id=b.job_id join wo_po_color_size_breakdown c on a.id=c.job_id and b.id=c.po_break_down_id join wo_pre_cost_mst d on a.id=d.job_id join wo_pre_cost_fabric_cost_dtls e on a.id=e.job_id join wo_pre_cos_fab_co_avg_con_dtls f on e.id=f.pre_cost_fabric_cost_dtls_id left join wo_pre_cos_fab_co_color_dtls g on a.id=g.job_id and e.id=g.pre_cost_fabric_cost_dtls_id and c.color_number_id =g.gmts_color_id and g.status_active=1 and g.is_deleted=0 left join wo_pre_stripe_color h on a.id=h.job_id and  c.item_number_id= h.item_number_id and e.id=h.pre_cost_fabric_cost_dtls_id and f.color_number_id =h.color_number_id and f.po_break_down_id=h.po_break_down_id and f.gmts_sizes=h.size_number_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.company_name=$company_name $year_cond $date_cond $style_ref_cond $jobcond $buyer_id_cond group by a.id, a.job_no, a.buyer_name, a.job_no_prefix_num, a.season_buyer_wise, a.style_ref_no, a.job_quantity, a.order_uom, b.id, c.color_number_id, c.order_quantity, e.lib_yarn_count_deter_id, e.fabric_description, e.uom, e.id, e.color_size_sensitive, e.color, e.color_break_down,g.contrast_color_id, h.stripe_color order by e.id asc");


	$main_attribute=array('job_no', 'buyer_name', 'job_no_prefix_num', 'season_buyer_wise', 'style_ref_no', 'job_quantity' ,'order_uom');
	foreach ($main_data_sql as $row) {
		foreach ($main_attribute as $attr) {
			$main_data_arr[$row[csf('id')]][$attr] = $row[csf($attr)];
		}
		$fabricColorId=$row[csf('stripe_color')];			
		if(!$fabricColorId){
			$fabricColorId=$row[csf('contrast_color_id')];
		}
		if(!$fabricColorId){
			$fabricColorId=$row[csf('color_number_id')];
		}	
		$main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['item_des'] = $row[csf('fabric_description')];
		$main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['color_id'] = $row[csf('color_number_id')];
		$main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['fabric_uom'] = $row[csf('fabric_uom')];
		$main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['avg_cons'] = $row[csf('avg_cons')];
		$main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['sensitive'] = $row[csf('color_size_sensitive')];
		$main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['color'] = $fabricColorId;
		$job_id_arr[$row[csf('id')]]=$row[csf('id')];	
		$fabric_id_arr[$row[csf('fabric_cost_id')]]=$row[csf('fabric_cost_id')];	
		$po_id_arr[$row[csf('po_id')]]=$row[csf('po_id')];	
	}
	/*echo '<pre>';
	print_r($main_data_arr); die;*/
	$po_id=array_chunk($po_id_arr,1000, true);
    $order_cond=""; 
	$po_cond_for_in2="";
    $ji=0;
    foreach($po_id as $key=> $value)
    {
        if($ji==0)
        {
            $order_cond=" and c.po_breakdown_id  in(".implode(",",$value).")";
			$po_cond_for_in2=" and a.po_breakdown_id  in(".implode(",",$value).")";
        }
        else
        {
            $order_cond.=" or c.po_breakdown_id  in(".implode(",",$value).")";
			$po_cond_for_in2.=" or a.po_breakdown_id  in(".implode(",",$value).")";
        }
        $ji++;
    }
    $job_id_chunk=array_chunk($job_id_arr,1000, true);
    $jobid_cond=""; $jobid_cond1=""; $jobid_cond3="";
    $i=0;
    foreach($job_id_chunk as $key=> $value)
    {
        if($i==0)
        {
            $jobid_cond=" and b.job_id  in(".implode(",",$value).")";
            $jobid_cond1=" and a.job_id  in(".implode(",",$value).")";
            $jobid_cond3=" and job_id  in(".implode(",",$value).")";
        }
        else
        {
            $jobid_cond.=" or b.job_id  in(".implode(",",$value).")";
            $jobid_cond1.=" or a.job_id  in(".implode(",",$value).")";
            $jobid_cond3.=" or job_id  in(".implode(",",$value).")";
        }
        $i++;
    }

	$rowspan=array();
	foreach ($main_data_arr as $job_id=>$jod_arr) {
		foreach ($jod_arr['color_data'] as $color_data) {
			foreach ($color_data['fabric_color'] as $row) {
				$rowspan[$job_id]++;
			}
		}
	}
	$fabric_id_str = implode(",", $fabric_id_arr);
	
	$wo_data_sql=sql_select("SELECT a.job_id, a.lib_yarn_count_deter_id, b.id, b.booking_date, c.gmts_color_id, c.fin_fab_qnty, c.amount,	b.supplier_id, c.fabric_color_id FROM wo_pre_cost_fabric_cost_dtls a join wo_booking_dtls c on a.id=c.pre_cost_fabric_cost_dtls_id join wo_booking_mst b on b.booking_no=c.booking_no where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id in ($fabric_id_str) and c.fin_fab_qnty is not null ");
	foreach ($wo_data_sql as $row) {
		$wo_data_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]]['fin_fab_qnty'] += $row[csf('fin_fab_qnty')];
		$wo_data_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]]['amount'] += $row[csf('amount')];
		$wo_data_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]]['wo_date'][$row[csf('id')]] = $row[csf('booking_date')];
		$wo_data_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]]['supplier'][$row[csf('id')]] =$lib_supplier_arr[$row[csf('supplier_id')]];
	}

	$receive_qty_data=sql_select("SELECT d.detarmination_id, d.color, b.order_qnty, b.order_amount, e.job_id, e.id as po_id, b.order_rate from inv_receive_master a join inv_transaction b on a.id = b.mst_id join order_wise_pro_details c on b.id = c.trans_id join product_details_master d on d.id=c.prod_id join wo_po_break_down e on e.id=c.po_breakdown_id where a.entry_form=17 and a.receive_basis=1 and a.status_active=1 and a.is_deleted=0 and b.receive_basis=1 and b.transaction_type=1 and b.status_active=1 and b.is_deleted=0 and c.trans_type=1 and c.entry_form=17 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $order_cond");
	$receive_qty_arr=array();
	foreach ($receive_qty_data as $row) {
		$receive_qty_arr[$row[csf('job_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['qty'] += $row[csf('order_qnty')];
		$receive_qty_arr[$row[csf('job_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['amount'] += $row[csf('order_amount')];
		$receive_qty_arr[$row[csf('job_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['rate'] = $row[csf('order_rate')];
		$receive_qty_arr[$row[csf('job_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['po_id'] = $row[csf('po_id')];
	}
	$issue_qty_data=sql_select("SELECT d.detarmination_id, d.color, b.cons_quantity, b.cons_amount, e.job_id, e.id as po_id,d.supplier_id from inv_issue_master a join inv_transaction b on a.id = b.mst_id join order_wise_pro_details c on b.id = c.trans_id join product_details_master d on d.id=c.prod_id join wo_po_break_down e on e.id=c.po_breakdown_id where a.entry_form=19 and a.issue_basis=1 and a.status_active=1 and a.is_deleted=0 and b.transaction_type=2 and b.status_active=1 and b.is_deleted=0 and c.trans_type=2 and c.entry_form=19 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $order_cond");
	
	$issue_qty_arr=array();
	foreach ($issue_qty_data as $row) {
		$rate=$receive_qty_arr[$row[csf('job_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['rate'];
		$issue_qty_arr[$row[csf('job_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['qty'] += $row[csf('cons_quantity')];
		$issue_qty_arr[$row[csf('job_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['amount'] += $row[csf('cons_quantity')]*$rate;
		$issue_qty_arr[$row[csf('job_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['po_id'] = $row[csf('po_id')];
		$issue_qty_arr[$row[csf('job_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['supplier'] = $lib_supplier_arr[$row[csf('supplier_id')]];
	}

	$fabric_supplier_data = sql_select("SELECT a.job_id, a.supplier_id, b.lib_yarn_count_deter_id from wo_pre_cost_fabric_supplier a join wo_pre_cost_fabric_cost_dtls b on a.JOB_ID=b.JOB_ID and b.id=a.fabric_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $jobid_cond");

	$fabric_supplier_arr= array();
	foreach ($fabric_supplier_data as $row) {
		$fabric_supplier_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('supplier_id')]] = $lib_supplier_arr[$row[csf('supplier_id')]];
	}

	$pi_number_data=sql_select("SELECT a.job_id, a.lib_yarn_count_deter_id, c.color_id as color_id,d.pi_number, c.amount, e.lc_number from wo_pre_cost_fabric_cost_dtls a join wo_booking_dtls b on a.job_no=b.job_no join com_pi_item_details c on b.booking_no=c.work_order_no and c.determination_id=a.lib_yarn_count_deter_id join com_pi_master_details d on d.id=c.pi_id left join com_btb_lc_master_details e on TO_CHAR(c.pi_id)=e.pi_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.entry_form=166 $jobid_cond1  group by  a.job_id, a.lib_yarn_count_deter_id,c.color_id,d.pi_number, c.amount, e.lc_number"); //c.color_id
	$pi_data_arr=array();
	foreach ($pi_number_data as $row) {
		$pi_data_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('color_id')]]['pi_no']=$row[csf('pi_number')];
		$pi_data_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('color_id')]]['amount']=$row[csf('amount')];
		$pi_data_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('color_id')]]['blc_no']=$row[csf('lc_number')];
	}
	/*echo '<pre>';
	print_r($pi_data_arr); die;*/
	$max_shipment_date_sql=sql_select("SELECT MAX(pub_shipment_date) as pub_shipment_date ,job_id from wo_po_break_down where  status_active=1 and is_deleted=0 $jobid_cond3 group by job_id");
	foreach ($max_shipment_date_sql as $row) {
		$max_ship_arr[$row[csf('job_id')]] = $row[csf('pub_shipment_date')];
	}

	$condition= new condition();
	if(count($job_id_arr)>0){
		$job_id_str= implode(",", $job_id_arr);
		$condition->jobid_in($job_id_str);
	}
	$condition->init();
	$fabric= new fabric($condition);
	$fabric_qty_arr=$fabric->getQtyArray_by_JobIdLibYarnCountDeterIdAndGmtscolor_knitAndwoven_greyAndfinish();
	//$fabric_amount_arr=$fabric->getAmountArray_by_JobIdLibYarnCountDeterIdAndGmtscolor_knitAndwoven_greyAndfinish();
	$fabric_amount_arr=$fabric->getAmountArr_by_JobIdYarnCountIdGmtsAndFabricColor_source();

	/*Trims Data Start from Here*/

	if($db_type==0)
	{
		$trim_sql_qry="SELECT a.id as job_id, a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, a.season_buyer_wise, b.file_no, b.grouping, b.id, b.po_number, a.order_uom, a.job_quantity, a.total_set_qnty, b.pub_shipment_date, b.shiping_status, d.costing_per, e.id as trim_dtla_id, e.trim_group, e.remark, e.description, e.brand_sup_ref, e.cons_uom, e.cons_dzn_gmts, e.rate, e.amount, e.apvl_req, e.nominated_supp, e.insert_date from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c left join  wo_pre_cost_trim_co_cons_dtls f on c.job_no_mst=f.job_no and c.po_break_down_id=f.po_break_down_id and f.cons > 0 join wo_pre_cost_trim_cost_dtls e on  f.job_no=e.job_no and e.id=f.wo_pre_cost_trim_cost_dtls_id $item_group_cond join wo_pre_cost_mst d on e.job_no =d.job_no where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and e.is_deleted=0 and e.status_active=1 and a.company_name=$company_name $buyer_id_cond ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $date_cond $style_ref_cond $year_cond $jobcond group by a.id, a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, a.season_buyer_wise, b.file_no, b.grouping, b.id, b.po_number, a.order_uom, a.total_set_qnty, b.pub_shipment_date, b.shiping_status, d.costing_per, e.id, e.trim_group, e.remark, e.description, e.brand_sup_ref, e.cons_uom, e.cons_dzn_gmts, e.rate, e.amount, e.apvl_req, e.nominated_supp, e.insert_date, a.job_quantity order by e.id, e.trim_group"; 
	}
	else
	{
		$trim_sql_qry = "SELECT a.id as job_id,a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, a.season_buyer_wise, b.id, b.po_number, a.order_uom, a.job_quantity, a.total_set_qnty, b.pub_shipment_date, d.costing_per, e.id as trim_dtla_id, e.trim_group, e.remark, e.description, e.brand_sup_ref, e.cons_uom, e.cons_dzn_gmts, e.rate, e.amount, e.apvl_req, e.nominated_supp, e.insert_date from wo_po_details_master a join wo_po_break_down b on a.job_no=b.job_no_mst join wo_po_color_size_breakdown c on a.job_no=c.job_no_mst and b.id=c.po_break_down_id join wo_pre_cost_mst d on a.job_no =d.job_no join wo_pre_cost_trim_cost_dtls e on e.job_no = d.job_no $item_group_cond left join  wo_pre_cost_trim_co_cons_dtls f on c.job_no_mst=f.job_no and c.po_break_down_id=f.po_break_down_id and f.job_no=e.job_no and e.id=f.wo_pre_cost_trim_cost_dtls_id where f.cons > 0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and e.is_deleted=0 and e.status_active=1 and a.company_name=$company_name $buyer_id_cond ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $date_cond $style_ref_cond $year_cond $jobcond $jobid_cond group by a.id, a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, a.season_buyer_wise,  b.id, b.po_number, a.order_uom, a.total_set_qnty, b.pub_shipment_date,  d.costing_per, e.id, e.trim_group, e.remark, e.description, e.brand_sup_ref, e.cons_uom, e.cons_dzn_gmts, e.rate, e.amount, e.apvl_req, e.nominated_supp, e.insert_date, a.job_quantity order by e.id, e.trim_group";
	}
	//echo $trim_sql_qry; die;
	$trims_sql_data= sql_select($trim_sql_qry);
	$trims_main_attribute= array('job_no','buyer_name','job_no_prefix_num','style_ref_no','season_buyer_wise', 'order_uom', 'job_quantity');
	$trims_dtls_attribute = array('trim_dtla_id', 'trim_group', 'description', 'brand_sup_ref', 'cons_uom', 'cons_dzn_gmts');
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
	$trimjob_id_chunk=array_chunk($trimjob_id_arr,1000, true);
    $jobid_cond1=""; $jobid_cond="";
    $i=0;
    foreach($trimjob_id_chunk as $key=> $value)
    {
        if($i==0)
        {       
        	$jobid_cond=" and b.job_id  in(".implode(",",$value).")";     
            $jobid_cond1=" and a.job_id  in(".implode(",",$value).")";
        }
        else
        {
        	$jobid_cond=" or b.job_id  in(".implode(",",$value).")";
            $jobid_cond1.=" or a.job_id  in(".implode(",",$value).")";
        }
        $i++;
    }

	$trim_id_chunk=array_chunk($trim_id_arr,1000, true);
    $trimid_cond=""; 
    $ji=0;
    foreach($trim_id_chunk as $key=> $value)
    {
        if($ji==0)
        {
            $trimid_cond=" and a.id  in(".implode(",",$value).")";
        }
        else
        {
            $trimid_cond.=" or a.id  in(".implode(",",$value).")";
        }
        $ji++;
    }
	$trim_wo_data_sql=sql_select("SELECT a.id as trim_id, a.job_id, b.id as booking_id, b.booking_date, c.wo_qnty,c.amount, 
	b.supplier_id,c.id as booking_dtls_id FROM wo_pre_cost_trim_cost_dtls a join wo_booking_dtls c on a.id=c.pre_cost_fabric_cost_dtls_id join wo_booking_mst b on b.booking_no=c.booking_no where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.booking_type=2 and c.is_short=2 $trimid_cond ");
	$trim_booking_idArr=array();
	foreach ($trim_wo_data_sql as $row) {
		$trim_wo_data_arr[$row[csf('job_id')]][$row[csf('trim_id')]]['wo_qnty'] += $row[csf('wo_qnty')];
		$trim_wo_data_arr[$row[csf('job_id')]][$row[csf('trim_id')]]['wo_amount'] += $row[csf('amount')];
		$trim_wo_data_arr[$row[csf('job_id')]][$row[csf('trim_id')]]['wo_date'][$row[csf('booking_id')]] = change_date_format($row[csf('booking_date')],'','');
		$trim_wo_data_arr[$row[csf('job_id')]][$row[csf('trim_id')]]['supplier'][$row[csf('booking_id')]] = $lib_supplier_arr[$row[csf('supplier_id')]];
		$trim_booking_idArr[$row[csf('booking_dtls_id')]]=$row[csf('booking_dtls_id')];
	}

	$po_id_chunk=array_chunk($trim_poid_arr,1000, true);
    $order_cond="";
    $pi=0;
    foreach($po_id_chunk as $key=> $value)
    {
        if($pi==0)
        {
            $order_cond=" and b.po_breakdown_id  in(".implode(",",$value).")";
        }
        else
        {
            $order_cond.=" or b.po_breakdown_id  in(".implode(",",$value).")";
        }
        $pi++;
    }

	$receive_qty_data=sql_select("SELECT b.po_breakdown_id, a.item_group_id, sum(b.quantity) as quantity, a.rate, e.job_id from inv_receive_master c,product_details_master d,inv_trims_entry_dtls a , order_wise_pro_details b, wo_po_break_down e where a.mst_id=c.id and a.trans_id=b.trans_id and a.id=b.dtls_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and e.id=b.po_breakdown_id $order_cond group by b.po_breakdown_id, a.item_group_id,a.rate, e.job_id order by a.item_group_id ");
	$trim_inhouse_qty=array();
	foreach ($receive_qty_data as $row) {
		$trim_inhouse_qty[$row[csf('job_id')]][$row[csf('item_group_id')]]['qty'] += $row[csf('quantity')];
		$trim_inhouse_qty[$row[csf('job_id')]][$row[csf('item_group_id')]]['amount'] += $row[csf('quantity')]*$row[csf('rate')];
		$trim_inhouse_qty[$row[csf('job_id')]][$row[csf('item_group_id')]]['rate'] = $row[csf('rate')];
		$trim_inhouse_qty[$row[csf('job_id')]][$row[csf('item_group_id')]]['po_id'] = $row[csf('po_breakdown_id')];
	}

	$trim_issue_qty_data=sql_select("SELECT b.po_breakdown_id, a.item_group_id, sum(b.quantity) as quantity, a.rate, e.job_id  from  inv_issue_master d,product_details_master p,inv_trims_issue_dtls a , order_wise_pro_details b, wo_po_break_down e where a.mst_id=d.id and a.prod_id=p.id and a.trans_id=b.trans_id and b.trans_type=2 and b.entry_form=25 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.id=b.po_breakdown_id  $order_cond group by b.po_breakdown_id, a.item_group_id, e.job_id,a.rate");
	$trim_issue_qty=array();
	foreach ($trim_issue_qty_data as $row) {
		$rate=$trim_inhouse_qty[$row[csf('job_id')]][$row[csf('item_group_id')]]['rate'];
		$trim_issue_qty[$row[csf('job_id')]][$row[csf('item_group_id')]]['qty'] += $row[csf('quantity')];
		$trim_issue_qty[$row[csf('job_id')]][$row[csf('item_group_id')]]['amount'] += $row[csf('quantity')]*$rate;
		$trim_issue_qty[$row[csf('job_id')]][$row[csf('item_group_id')]]['po_id'] = $row[csf('po_breakdown_id')];
	}

	$trim_supplier_data = sql_select("SELECT a.job_id, a.supplier_id, b.id as trim_cost_id from wo_pre_cost_trim_supplier a join wo_pre_cost_trim_cost_dtls b on a.job_id=b.job_id and b.id=a.trimid where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $jobid_cond");

	$fabric_supplier_arr= array();
	foreach ($fabric_supplier_data as $row) {
		$trim_supplier_arr[$row[csf('job_id')]][$row[csf('trim_cost_id')]][$row[csf('supplier_id')]] = $lib_supplier_arr[$row[csf('supplier_id')]];
	}

	$trim_booking_cond_in=where_con_using_array($trim_booking_idArr,0,'c.work_order_dtls_id');

	$trims_pi_number_data=sql_select("SELECT a.job_id,d.pi_number, c.amount, e.lc_number, c.item_group, c.item_color, c.item_size from wo_pre_cost_trim_cost_dtls a join wo_booking_dtls b on a.job_no=b.job_no join com_pi_item_details c on b.booking_no=c.work_order_no  join com_pi_master_details d on d.id=c.pi_id left join com_btb_lc_master_details e on TO_CHAR(c.pi_id)=e.pi_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.entry_form=167 $jobid_cond1 $trim_booking_cond_in group by a.job_id, d.pi_number, c.amount, e.lc_number, c.item_group, c.item_color, c.item_size"); 
	$trim_pi_data_arr=array();
	foreach ($trims_pi_number_data as $row) {
		$trim_pi_data_arr[$row[csf('job_id')]][$row[csf('item_group')]]['pi_no']=$row[csf('pi_number')];
		$trim_pi_data_arr[$row[csf('job_id')]][$row[csf('item_group')]]['amount']+=$row[csf('amount')];
		$trim_pi_data_arr[$row[csf('job_id')]][$row[csf('item_group')]]['blc_no']=$row[csf('lc_number')];
	}

	if(count($trimjob_id_arr)>0){
		$trimjob_id_str= implode(",", $trimjob_id_arr);
		$condition->jobid_in($trimjob_id_str);
	}
	$condition->init();
	$trim= new trims($condition);
	$trim_group_qty_arr=$trim->getQtyArray_by_jobAndPrecostdtlsid();

	//$trim_group_amount_arr=$trim->getAmountArray_by_jobAndPrecostdtlsid();

	$trim_amountSourcing_arr=$trim->getAmountArray_precostdtlsidSourcing();
	/*echo'<pre>';
	print_r($trim_group_qty_arr); die;*/

	ob_start();
	?>
	<div style="width:2600px">
		<table width="2500">
	        <tr class="form_caption"><td colspan="18" align="center"><? echo $report_title; ?></td></tr>
	        <tr class="form_caption"><td colspan="18" align="center"><? echo $company_library[$company_name]; ?></td></tr>
		</table>
		<table class="rpt_table" width="2500" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
					<tr>
						<td colspan="24" align="left"><strong>Fabric Details</strong></td>
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
					<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $sl; ?>">
					<td rowspan="<?= $rowspan[$job_id] ?>" width="30"><?= $sl; ?></td>
					<td rowspan="<?= $rowspan[$job_id] ?>" width="100"><?= $buyer_short_name_library[$job_data['buyer_name']]; ?></td>
					<td rowspan="<?= $rowspan[$job_id] ?>" width="100"><?= $job_data['job_no'] ?></td>
					<td rowspan="<?= $rowspan[$job_id] ?>"><?= $lib_season_arr[$job_data['season_buyer_wise']] ?></td>
					<td rowspan="<?= $rowspan[$job_id] ?>"><p>
					<a href='#report_details' onclick="order_summery_popup('<? echo $job_id; ?>' ,'order_summery_data');"><?= $job_data['style_ref_no'] ?></a></p></td>
					<td rowspan="<?= $rowspan[$job_id] ?>" align="right"><p>
					<a href='#report_details' onclick="order_summery_popup('<? echo $job_id; ?>' ,'order_qty_data');"><?= $job_data['job_quantity'] ?></a></p></td>
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
							$gmts_color_id=array();
							$bom_qty=0;	$wo_qty=0; $bom_value=0; $wo_amount=0; $pi_amount=0;
							foreach ($fcolor_data as $gcolor_id => $row) {
								$fabric_color_id=$row['color'];
								$color_id = $gcolor_id;
								$gmts_color[$gcolor_id]=$color_arr[$gcolor_id];
								$gmts_color_id[$gcolor_id]=$gcolor_id;
								$wo_qty += $wo_data_arr[$job_id][$lib_yarn_id][$color_id][$fabric_color_id]['fin_fab_qnty'];
								$wo_amount+= $wo_data_arr[$job_id][$lib_yarn_id][$color_id][$fabric_color_id]['amount'];
								$issueqty= $issue_qty_arr[$job_id][$lib_yarn_id][$fabric_color_id]['qty'];
								$issueamount= $issue_qty_arr[$job_id][$lib_yarn_id][$fabric_color_id]['amount'];
								$issuepo= $issue_qty_arr[$job_id][$lib_yarn_id][$fabric_color_id]['po_id'];
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

						 	?>
							<td align="left"><?= $color_arr[$fcolor_id] ?></td>
							<td align="left"><?= implode(", ", $gmts_color) ?></td>
							<td align="left"><?= implode(",",$wo_data_arr[$job_id][$lib_yarn_id][$color_id][$fabric_color_id]['wo_date'] )  ?></td>
							<td align="right"><?= fn_number_format($row['avg_cons'],4) ?></td>
							<td align="right"><a href='#report_details' onclick="order_req_qty_popup('<? echo $job_id; ?>','<? echo $lib_yarn_id; ?>','<? echo $fabric_color_id; ?>' ,'order_req_qty_data');"><?= fn_number_format($bom_qty,2)?></a></td>
							<td align="right"><?= fn_number_format($bom_value,2);  ?></td>
							<td align="right"><a href='#report_details' onclick="order_wo_qty_popup('<? echo $job_id; ?>','<? echo $lib_yarn_id; ?>','<? echo $fabric_color_id; ?>','<? echo implode(",", $gmts_color_id) ?>' ,'order_wo_qty_data');"><?= fn_number_format($wo_qty,2)  ?></a></td>
							<td align="right"><?= $unit_of_measurement[$row['fabric_uom']] ?></td>
							<td align="right"><?= fn_number_format($wo_amount,2) ?></td>
							<td align="right"><a href='#report_details' onclick="openmypage_inhouse('<? echo $job_id; ?>','<? echo $lib_yarn_id; ?>','<? echo $inhousepo; ?>','<? echo $fabric_color_id ?>' ,'booking_inhouse_info');"><?= fn_number_format($inhouseqty,2) ?></a></td>
							<td align="right"><?= fn_number_format($inhouseamount,2) ?></td>
							<td align="right"><?= fn_number_format($rcv_balance,2)  ?></td>
							<td align="right"><? if($issueqty>0){ ?><a href='#report_details' onclick="openmypage_issue('<? echo $job_id; ?>','<? echo $lib_yarn_id; ?>','<? echo $issuepo; ?>','<? echo $fabric_color_id?>', '<?echo $rcv_rate?>' ,'booking_issue_info');"><?= fn_number_format($issueqty,2)  ?></a><? } else echo '0.00'; ?></td>
							<td align="right"><?= fn_number_format($issueamount,2) ?></td>
							<td align="right"><?= fn_number_format($issue_balance,2) ?></td>
							<td align="right"><?= fn_number_format($inhouseamount-$issueamount,2) ?></td>
							<td align="left"><?= implode(",",$wo_data_arr[$job_id][$lib_yarn_id][$color_id][$fabric_color_id]['supplier'] ); ?></td>
							<td align="left"><?= $pi_data_arr[$job_id][$lib_yarn_id][$fabric_color_id]['pi_no'] ?></td>
							<td align="right"><?= fn_number_format($pi_amount,2); ?></td>
							<td align="left"><?= $pi_data_arr[$job_id][$lib_yarn_id][$fabric_color_id]['blc_no'] ?></td>
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
					<td align="right"><?= fn_number_format($pi_amount_total,2); ?></td>
					<td></td>
				</tr>
			</tfoot>
		</table>
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
					<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('ftr_<? echo $tsl; ?>','<? echo $bgcolor;?>')" id="ftr_<? echo $tsl; ?>">
						<td width="35"  rowspan="<?= $rowspan ?>"><?= $tsl; ?></td>
						<td width="95" align="left" rowspan="<?= $rowspan ?>"><?= $buyer_short_name_library[$value['buyer_name']]; ?></td>
						<td width="95" align="left" rowspan="<?= $rowspan ?>"><?= $value['job_no_prefix_num']; ?></td>
						<td width="95" align="left" rowspan="<?= $rowspan ?>"><?= $lib_season_arr[$value['season_buyer_wise']] ?></td>
		                <td width="95" align="left" rowspan="<?= $rowspan ?>">
					<a href='#report_details' onclick="order_summery_popup('<? echo $job_id; ?>' ,'order_summery_data');"><?= $value['style_ref_no']; ?></a></td>
						<td width="60"  align="right" rowspan="<?= $rowspan ?>"><a href='#report_details' onclick="order_summery_popup('<? echo $job_id; ?>' ,'order_qty_data');"><?= $value['job_quantity']; ?></a></td>
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
							$inhouse_qty= $trim_inhouse_qty[$job_id][$row['trim_group']]['qty'];
							$inhouse_amount= $trim_inhouse_qty[$job_id][$row['trim_group']]['amount'];
							$issue_qty= $trim_issue_qty[$job_id][$row['trim_group']]['qty'];
							$issue_amount= $trim_issue_qty[$job_id][$row['trim_group']]['amount'];
							$po_id= $trim_inhouse_qty[$job_id][$row['trim_group']]['po_id'];
							$tpo_id= $trim_issue_qty[$job_id][$row['trim_group']]['po_id'];
							$pi_amount=$trim_pi_data_arr[$job_id][$row['trim_group']]['amount'];
							$trimrcv_balance = $wo_qty-$inhouse_qty;
						?>
						<td width="100" align="left"><?= $item_library[$row['trim_group']]; ?></td>
						<td width="150" align="left"><?= $row['description']; ?></td>
						<td width="100" align="left"><?= $row['brand_sup_ref']; ?></td>
						<td width="80" align="left"><?= implode(",", $trim_wo_data_arr[$job_id][$trims_id]['wo_date'])?></td>
						<td width="80" align="right"><?= fn_number_format($row['cons_dzn_gmts'],4); ?></td>
						<td width="100" align="right"><a href='#report_details' onclick="trim_req_qty_popup('<? echo $job_id; ?>','<? echo $trims_id; ?>' ,'trim_req_qty_data');"><?= fn_number_format($req_qty,2); ?></a></td>
						<td width="100" align="right"><?= fn_number_format($req_amount,2); ?></td>
						<td width="90" align="right"><a href='#report_details' onclick="trim_wo_qty_popup('<? echo $job_id; ?>','<? echo $trims_id; ?>' ,'trim_wo_qty_data');"><?= fn_number_format($wo_qty,2) ?></a></td>
		                <td width="50" align="right"><?= $unit_of_measurement[$row['cons_uom']]; ?></td>
		                <td align="right"><?= fn_number_format($wo_amount,2);  ?></td>
		                <td width="80" align="right"><a href='#report_details' onclick="openmypage_trim_inhouse('<? echo $po_id; ?>','<? echo $row['trim_group']; ?>','trim_booking_inhouse_info');"><?= fn_number_format($inhouse_qty,2);  ?></a></td>
		                <td align="right"><?= fn_number_format($inhouse_amount,2); ?></td>
		                <td width="80" align="right"><?= fn_number_format($trimrcv_balance,2); ?></td>
		                <td width="70" align="right"><a href='#report_details' onclick="openmypage_trim_issue('<? echo $tpo_id; ?>','<? echo $row['trim_group']; ?>','trim_booking_issue_info');"><?= fn_number_format($issue_qty,2);  ?></a></td>
		                <td align="right"><?= fn_number_format($issue_amount,2); ?></td>
						<td width="90" align="right"><?= fn_number_format($inhouse_qty-$issue_qty,2); ?></td>
						<td width="90" align="right"><?= fn_number_format($inhouse_amount-$issue_amount,2); ?></td>
		                <td width="150" align="left"><?=implode(",", $trim_wo_data_arr[$job_id][$trims_id]['supplier'])
						
						//implode(",", $trim_supplier_arr[$job_id][$trims_id]);  ?></td>
						<td width="160" align="left"><?= $trim_pi_data_arr[$job_id][$row['trim_group']]['pi_no']?></td>
						<td width="90" align="right"><?= fn_number_format($pi_amount,2); ?></td>
						<td width="140" align="left"><?= $trim_pi_data_arr[$job_id][$row['trim_group']]['blc_no']?></td>
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

	if($txt_job_no!="" || $txt_job_no!=0) $jobcond="and a.job_no_prefix_num in('".$txt_job_no."')"; else $jobcond="";
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

	$main_data_sql=sql_select("SELECT a.id, a.job_no, a.buyer_name, a.job_no_prefix_num, a.season_buyer_wise, a.style_ref_no, a.job_quantity, a.order_uom, b.id as po_id,  c.color_number_id, c.order_quantity, e.id as fabric_cost_id, e.lib_yarn_count_deter_id, e.fabric_description, e.uom as fabric_uom, avg(f.requirment) as avg_cons, e.color_size_sensitive, e.color, e.color_break_down, g.contrast_color_id, h.stripe_color from wo_po_details_master a join wo_po_break_down b on a.id=b.job_id join wo_po_color_size_breakdown c on a.id=c.job_id and b.id=c.po_break_down_id join wo_pre_cost_mst d on a.id=d.job_id join wo_pre_cost_fabric_cost_dtls e on a.id=e.job_id join wo_pre_cos_fab_co_avg_con_dtls f on e.id=f.pre_cost_fabric_cost_dtls_id left join wo_pre_cos_fab_co_color_dtls g on a.id=g.job_id and e.id=g.pre_cost_fabric_cost_dtls_id and c.color_number_id =g.gmts_color_id and g.status_active=1 and g.is_deleted=0 left join wo_pre_stripe_color h on a.id=h.job_id and  c.item_number_id= h.item_number_id and e.id=h.pre_cost_fabric_cost_dtls_id and f.color_number_id =h.color_number_id and f.po_break_down_id=h.po_break_down_id and f.gmts_sizes=h.size_number_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.company_name=$company_name $year_cond $date_cond $style_ref_cond $jobcond $buyer_id_cond group by a.id, a.job_no, a.buyer_name, a.job_no_prefix_num, a.season_buyer_wise, a.style_ref_no, a.job_quantity, a.order_uom, b.id, c.color_number_id, c.order_quantity, e.lib_yarn_count_deter_id, e.fabric_description, e.uom, e.id, e.color_size_sensitive, e.color, e.color_break_down,g.contrast_color_id, h.stripe_color  order by e.id asc");

	


	$main_attribute=array('job_no', 'buyer_name', 'job_no_prefix_num', 'season_buyer_wise', 'style_ref_no', 'job_quantity' ,'order_uom');
	foreach ($main_data_sql as $row) {
		foreach ($main_attribute as $attr) {
			$main_data_arr[$row[csf('id')]][$attr] = $row[csf($attr)];
		}
		$fabricColorId=$row[csf('stripe_color')];			
		if(!$fabricColorId){
			$fabricColorId=$row[csf('contrast_color_id')];
		}
		if(!$fabricColorId){
			$fabricColorId=$row[csf('color_number_id')];
		}	
		$main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['item_des'] = $row[csf('fabric_description')];
		$main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['color_id'] = $row[csf('color_number_id')];
		$main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['fabric_uom'] = $row[csf('fabric_uom')];
		$main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['avg_cons'] = $row[csf('avg_cons')];
		$main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['sensitive'] = $row[csf('color_size_sensitive')];
		$main_data_arr[$row[csf('id')]]['color_data'][$row[csf('lib_yarn_count_deter_id')]]['fabric_color'][$fabricColorId][$row[csf('color_number_id')]]['color'] = $fabricColorId;
		$job_id_arr[$row[csf('id')]]=$row[csf('id')];	
		$fabric_id_arr[$row[csf('fabric_cost_id')]]=$row[csf('fabric_cost_id')];	
		$po_id_arr[$row[csf('po_id')]]=$row[csf('po_id')];	
	}
	/*echo '<pre>';
	print_r($main_data_arr); die;*/
	$po_id=array_chunk($po_id_arr,1000, true);
    $order_cond=""; 
	$po_cond_for_in2="";
    $ji=0;
    foreach($po_id as $key=> $value)
    {
        if($ji==0)
        {
            $order_cond=" and c.po_breakdown_id  in(".implode(",",$value).")";
			$po_cond_for_in2=" and a.po_breakdown_id  in(".implode(",",$value).")";
        }
        else
        {
            $order_cond.=" or c.po_breakdown_id  in(".implode(",",$value).")";
			$po_cond_for_in2.=" or a.po_breakdown_id  in(".implode(",",$value).")";
        }
        $ji++;
    }
    $job_id_chunk=array_chunk($job_id_arr,1000, true);
    $jobid_cond=""; $jobid_cond1=""; $jobid_cond3="";
    $i=0;
    foreach($job_id_chunk as $key=> $value)
    {
        if($i==0)
        {
            $jobid_cond=" and b.job_id  in(".implode(",",$value).")";
            $jobid_cond1=" and a.job_id  in(".implode(",",$value).")";
            $jobid_cond3=" and job_id  in(".implode(",",$value).")";
        }
        else
        {
            $jobid_cond.=" or b.job_id  in(".implode(",",$value).")";
            $jobid_cond1.=" or a.job_id  in(".implode(",",$value).")";
            $jobid_cond3.=" or job_id  in(".implode(",",$value).")";
        }
        $i++;
    }

	$rowspan=array();
	foreach ($main_data_arr as $job_id=>$jod_arr) {
		foreach ($jod_arr['color_data'] as $color_data) {
			foreach ($color_data['fabric_color'] as $row) {
				$rowspan[$job_id]++;
			}
		}
	}
	$fabric_id_str = implode(",", $fabric_id_arr);
	
	$wo_data_sql=sql_select("SELECT a.job_id, a.lib_yarn_count_deter_id, b.id, b.booking_date, c.gmts_color_id, c.fin_fab_qnty, c.amount,	b.supplier_id, c.fabric_color_id FROM wo_pre_cost_fabric_cost_dtls a join wo_booking_dtls c on a.id=c.pre_cost_fabric_cost_dtls_id join wo_booking_mst b on b.booking_no=c.booking_no where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id in ($fabric_id_str) and c.fin_fab_qnty is not null ");
	foreach ($wo_data_sql as $row) {
		$wo_data_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]]['fin_fab_qnty'] += $row[csf('fin_fab_qnty')];
		$wo_data_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]]['amount'] += $row[csf('amount')];
		$wo_data_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]]['wo_date'][$row[csf('id')]] = $row[csf('booking_date')];
		$wo_data_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gmts_color_id')]][$row[csf('fabric_color_id')]]['supplier'][$row[csf('id')]] =$lib_supplier_arr[$row[csf('supplier_id')]];
	}

	$receive_qty_data=sql_select("SELECT d.detarmination_id, d.color, b.order_qnty, b.order_amount, e.job_id, e.id as po_id, b.order_rate from inv_receive_master a join inv_transaction b on a.id = b.mst_id join order_wise_pro_details c on b.id = c.trans_id join product_details_master d on d.id=c.prod_id join wo_po_break_down e on e.id=c.po_breakdown_id where a.entry_form=17 and a.receive_basis=1 and a.status_active=1 and a.is_deleted=0 and b.receive_basis=1 and b.transaction_type=1 and b.status_active=1 and b.is_deleted=0 and c.trans_type=1 and c.entry_form=17 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $order_cond");
	$receive_qty_arr=array();
	foreach ($receive_qty_data as $row) {
		$receive_qty_arr[$row[csf('job_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['qty'] += $row[csf('order_qnty')];
		$receive_qty_arr[$row[csf('job_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['amount'] += $row[csf('order_amount')];
		$receive_qty_arr[$row[csf('job_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['rate'] = $row[csf('order_rate')];
		$receive_qty_arr[$row[csf('job_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['po_id'] = $row[csf('po_id')];
	}
	$issue_qty_data=sql_select("SELECT d.detarmination_id, d.color, b.cons_quantity, b.cons_amount, e.job_id, e.id as po_id,d.supplier_id from inv_issue_master a join inv_transaction b on a.id = b.mst_id join order_wise_pro_details c on b.id = c.trans_id join product_details_master d on d.id=c.prod_id join wo_po_break_down e on e.id=c.po_breakdown_id where a.entry_form=19 and a.issue_basis=1 and a.status_active=1 and a.is_deleted=0 and b.transaction_type=2 and b.status_active=1 and b.is_deleted=0 and c.trans_type=2 and c.entry_form=19 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $order_cond");
	
	$issue_qty_arr=array();
	foreach ($issue_qty_data as $row) {
		$rate=$receive_qty_arr[$row[csf('job_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['rate'];
		$issue_qty_arr[$row[csf('job_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['qty'] += $row[csf('cons_quantity')];
		$issue_qty_arr[$row[csf('job_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['amount'] += $row[csf('cons_quantity')]*$rate;
		$issue_qty_arr[$row[csf('job_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['po_id'] = $row[csf('po_id')];
		$issue_qty_arr[$row[csf('job_id')]][$row[csf('detarmination_id')]][$row[csf('color')]]['supplier'] = $lib_supplier_arr[$row[csf('supplier_id')]];
	}

	$fabric_supplier_data = sql_select("SELECT a.job_id, a.supplier_id, b.lib_yarn_count_deter_id from wo_pre_cost_fabric_supplier a join wo_pre_cost_fabric_cost_dtls b on a.JOB_ID=b.JOB_ID and b.id=a.fabric_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $jobid_cond");

	$fabric_supplier_arr= array();
	foreach ($fabric_supplier_data as $row) {
		$fabric_supplier_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('supplier_id')]] = $lib_supplier_arr[$row[csf('supplier_id')]];
	}

	$pi_number_data=sql_select("SELECT a.job_id, a.lib_yarn_count_deter_id, c.color_id as color_id,d.pi_number, c.amount, e.lc_number from wo_pre_cost_fabric_cost_dtls a join wo_booking_dtls b on a.job_no=b.job_no join com_pi_item_details c on b.booking_no=c.work_order_no and c.determination_id=a.lib_yarn_count_deter_id join com_pi_master_details d on d.id=c.pi_id left join com_btb_lc_master_details e on TO_CHAR(c.pi_id)=e.pi_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.entry_form=166 $jobid_cond1  group by  a.job_id, a.lib_yarn_count_deter_id,c.color_id,d.pi_number, c.amount, e.lc_number"); //c.color_id
	$pi_data_arr=array();
	foreach ($pi_number_data as $row) {
		$pi_data_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('color_id')]]['pi_no']=$row[csf('pi_number')];
		$pi_data_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('color_id')]]['amount']=$row[csf('amount')];
		$pi_data_arr[$row[csf('job_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('color_id')]]['blc_no']=$row[csf('lc_number')];
	}
	/*echo '<pre>';
	print_r($pi_data_arr); die;*/
	$max_shipment_date_sql=sql_select("SELECT MAX(pub_shipment_date) as pub_shipment_date ,job_id from wo_po_break_down where  status_active=1 and is_deleted=0 $jobid_cond3 group by job_id");
	foreach ($max_shipment_date_sql as $row) {
		$max_ship_arr[$row[csf('job_id')]] = $row[csf('pub_shipment_date')];
	}

	$condition= new condition();
	if(count($job_id_arr)>0){
		$job_id_str= implode(",", $job_id_arr);
		$condition->jobid_in($job_id_str);
	}
	$condition->init();
	$fabric= new fabric($condition);
	$fabric_qty_arr=$fabric->getQtyArray_by_JobIdLibYarnCountDeterIdAndGmtscolor_knitAndwoven_greyAndfinish();
	//$fabric_amount_arr=$fabric->getAmountArray_by_JobIdLibYarnCountDeterIdAndGmtscolor_knitAndwoven_greyAndfinish();
	$fabric_amount_arr=$fabric->getAmountArr_by_JobIdYarnCountIdGmtsAndFabricColor_source();

	/*Trims Data Start from Here*/

	if($db_type==0)
	{
		$trim_sql_qry="SELECT a.id as job_id, a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, a.season_buyer_wise, b.file_no, b.grouping, b.id, b.po_number, a.order_uom, a.job_quantity, a.total_set_qnty, b.pub_shipment_date, b.shiping_status, d.costing_per, e.id as trim_dtla_id, e.trim_group, e.remark, e.description, e.brand_sup_ref, e.cons_uom, e.cons_dzn_gmts, e.rate, e.amount, e.apvl_req, e.nominated_supp, e.insert_date from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c left join  wo_pre_cost_trim_co_cons_dtls f on c.job_no_mst=f.job_no and c.po_break_down_id=f.po_break_down_id and f.cons > 0 join wo_pre_cost_trim_cost_dtls e on  f.job_no=e.job_no and e.id=f.wo_pre_cost_trim_cost_dtls_id $item_group_cond join wo_pre_cost_mst d on e.job_no =d.job_no where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and e.is_deleted=0 and e.status_active=1 and a.company_name=$company_name $buyer_id_cond ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $date_cond $style_ref_cond $year_cond $jobcond group by a.id, a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, a.season_buyer_wise, b.file_no, b.grouping, b.id, b.po_number, a.order_uom, a.total_set_qnty, b.pub_shipment_date, b.shiping_status, d.costing_per, e.id, e.trim_group, e.remark, e.description, e.brand_sup_ref, e.cons_uom, e.cons_dzn_gmts, e.rate, e.amount, e.apvl_req, e.nominated_supp, e.insert_date, a.job_quantity order by e.id, e.trim_group"; 
	}
	else
	{
		$trim_sql_qry = "SELECT a.id as job_id,a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, a.season_buyer_wise, b.id, b.po_number, a.order_uom, a.job_quantity, a.total_set_qnty, b.pub_shipment_date, d.costing_per, e.id as trim_dtla_id, e.trim_group, e.remark, e.description, e.brand_sup_ref, e.cons_uom, e.cons_dzn_gmts, e.rate, e.amount, e.apvl_req, e.nominated_supp, e.insert_date from wo_po_details_master a join wo_po_break_down b on a.job_no=b.job_no_mst join wo_po_color_size_breakdown c on a.job_no=c.job_no_mst and b.id=c.po_break_down_id join wo_pre_cost_mst d on a.job_no =d.job_no join wo_pre_cost_trim_cost_dtls e on e.job_no = d.job_no $item_group_cond left join  wo_pre_cost_trim_co_cons_dtls f on c.job_no_mst=f.job_no and c.po_break_down_id=f.po_break_down_id and f.job_no=e.job_no and e.id=f.wo_pre_cost_trim_cost_dtls_id where f.cons > 0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and e.is_deleted=0 and e.status_active=1 and a.company_name=$company_name $buyer_id_cond ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $date_cond $style_ref_cond $year_cond $jobcond $jobid_cond group by a.id, a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, a.season_buyer_wise,  b.id, b.po_number, a.order_uom, a.total_set_qnty, b.pub_shipment_date,  d.costing_per, e.id, e.trim_group, e.remark, e.description, e.brand_sup_ref, e.cons_uom, e.cons_dzn_gmts, e.rate, e.amount, e.apvl_req, e.nominated_supp, e.insert_date, a.job_quantity order by e.id, e.trim_group";
	}
	//echo $trim_sql_qry; die;
	$trims_sql_data= sql_select($trim_sql_qry);
	$trims_main_attribute= array('job_no','buyer_name','job_no_prefix_num','style_ref_no','season_buyer_wise', 'order_uom', 'job_quantity');
	$trims_dtls_attribute = array('trim_dtla_id', 'trim_group', 'description', 'brand_sup_ref', 'cons_uom', 'cons_dzn_gmts');
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
	$trimjob_id_chunk=array_chunk($trimjob_id_arr,1000, true);
    $jobid_cond1=""; $jobid_cond="";
    $i=0;
    foreach($trimjob_id_chunk as $key=> $value)
    {
        if($i==0)
        {       
        	$jobid_cond=" and b.job_id  in(".implode(",",$value).")";     
            $jobid_cond1=" and a.job_id  in(".implode(",",$value).")";
        }
        else
        {
        	$jobid_cond=" or b.job_id  in(".implode(",",$value).")";
            $jobid_cond1.=" or a.job_id  in(".implode(",",$value).")";
        }
        $i++;
    }

	$trim_id_chunk=array_chunk($trim_id_arr,1000, true);
    $trimid_cond=""; 
    $ji=0;
    foreach($trim_id_chunk as $key=> $value)
    {
        if($ji==0)
        {
            $trimid_cond=" and a.id  in(".implode(",",$value).")";
        }
        else
        {
            $trimid_cond.=" or a.id  in(".implode(",",$value).")";
        }
        $ji++;
    }
	$trim_wo_data_sql=sql_select("SELECT a.id as trim_id, a.job_id, b.id as booking_id, b.booking_date, c.wo_qnty,c.amount, 
	b.supplier_id FROM wo_pre_cost_trim_cost_dtls a join wo_booking_dtls c on a.id=c.pre_cost_fabric_cost_dtls_id join wo_booking_mst b on b.booking_no=c.booking_no where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.booking_type=2 and c.is_short=2 $trimid_cond ");
	foreach ($trim_wo_data_sql as $row) {
		$trim_wo_data_arr[$row[csf('job_id')]][$row[csf('trim_id')]]['wo_qnty'] += $row[csf('wo_qnty')];
		$trim_wo_data_arr[$row[csf('job_id')]][$row[csf('trim_id')]]['wo_amount'] += $row[csf('amount')];
		$trim_wo_data_arr[$row[csf('job_id')]][$row[csf('trim_id')]]['wo_date'][$row[csf('booking_id')]] = change_date_format($row[csf('booking_date')],'','');
		$trim_wo_data_arr[$row[csf('job_id')]][$row[csf('trim_id')]]['supplier'][$row[csf('booking_id')]] = $lib_supplier_arr[$row[csf('supplier_id')]];
	}

	$po_id_chunk=array_chunk($trim_poid_arr,1000, true);
    $order_cond="";
    $pi=0;
    foreach($po_id_chunk as $key=> $value)
    {
        if($pi==0)
        {
            $order_cond=" and b.po_breakdown_id  in(".implode(",",$value).")";
        }
        else
        {
            $order_cond.=" or b.po_breakdown_id  in(".implode(",",$value).")";
        }
        $pi++;
    }

	$receive_qty_data=sql_select("SELECT b.po_breakdown_id, a.item_group_id, sum(b.quantity) as quantity, a.rate, e.job_id from inv_receive_master c,product_details_master d,inv_trims_entry_dtls a , order_wise_pro_details b, wo_po_break_down e where a.mst_id=c.id and a.trans_id=b.trans_id and a.id=b.dtls_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and e.id=b.po_breakdown_id $order_cond group by b.po_breakdown_id, a.item_group_id,a.rate, e.job_id order by a.item_group_id ");
	$trim_inhouse_qty=array();
	foreach ($receive_qty_data as $row) {
		$trim_inhouse_qty[$row[csf('job_id')]][$row[csf('item_group_id')]]['qty'] += $row[csf('quantity')];
		$trim_inhouse_qty[$row[csf('job_id')]][$row[csf('item_group_id')]]['amount'] += $row[csf('quantity')]*$row[csf('rate')];
		$trim_inhouse_qty[$row[csf('job_id')]][$row[csf('item_group_id')]]['rate'] = $row[csf('rate')];
		$trim_inhouse_qty[$row[csf('job_id')]][$row[csf('item_group_id')]]['po_id'] = $row[csf('po_breakdown_id')];
	}

	$trim_issue_qty_data=sql_select("SELECT b.po_breakdown_id, a.item_group_id, sum(b.quantity) as quantity, a.rate, e.job_id  from  inv_issue_master d,product_details_master p,inv_trims_issue_dtls a , order_wise_pro_details b, wo_po_break_down e where a.mst_id=d.id and a.prod_id=p.id and a.trans_id=b.trans_id and b.trans_type=2 and b.entry_form=25 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.id=b.po_breakdown_id  $order_cond group by b.po_breakdown_id, a.item_group_id, e.job_id,a.rate");
	$trim_issue_qty=array();
	foreach ($trim_issue_qty_data as $row) {
		$rate=$trim_inhouse_qty[$row[csf('job_id')]][$row[csf('item_group_id')]]['rate'];
		$trim_issue_qty[$row[csf('job_id')]][$row[csf('item_group_id')]]['qty'] += $row[csf('quantity')];
		$trim_issue_qty[$row[csf('job_id')]][$row[csf('item_group_id')]]['amount'] += $row[csf('quantity')]*$rate;
		$trim_issue_qty[$row[csf('job_id')]][$row[csf('item_group_id')]]['po_id'] = $row[csf('po_breakdown_id')];
	}

	$trim_supplier_data = sql_select("SELECT a.job_id, a.supplier_id, b.id as trim_cost_id from wo_pre_cost_trim_supplier a join wo_pre_cost_trim_cost_dtls b on a.job_id=b.job_id and b.id=a.trimid where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $jobid_cond");

	$fabric_supplier_arr= array();
	foreach ($fabric_supplier_data as $row) {
		$trim_supplier_arr[$row[csf('job_id')]][$row[csf('trim_cost_id')]][$row[csf('supplier_id')]] = $lib_supplier_arr[$row[csf('supplier_id')]];
	}

	$trims_pi_number_data=sql_select("SELECT a.job_id,d.pi_number, c.amount, e.lc_number, c.item_group, c.item_color, c.item_size from wo_pre_cost_trim_cost_dtls a join wo_booking_dtls b on a.job_no=b.job_no join com_pi_item_details c on b.booking_no=c.work_order_no  join com_pi_master_details d on d.id=c.pi_id left join com_btb_lc_master_details e on TO_CHAR(c.pi_id)=e.pi_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.entry_form=167 $jobid_cond1 group by a.job_id, d.pi_number, c.amount, e.lc_number, c.item_group, c.item_color, c.item_size"); 
	$trim_pi_data_arr=array();
	foreach ($trims_pi_number_data as $row) {
		$trim_pi_data_arr[$row[csf('job_id')]][$row[csf('item_group')]]['pi_no']=$row[csf('pi_number')];
		$trim_pi_data_arr[$row[csf('job_id')]][$row[csf('item_group')]]['amount']+=$row[csf('amount')];
		$trim_pi_data_arr[$row[csf('job_id')]][$row[csf('item_group')]]['blc_no']=$row[csf('lc_number')];
	}

	if(count($trimjob_id_arr)>0){
		$trimjob_id_str= implode(",", $trimjob_id_arr);
		$condition->jobid_in($trimjob_id_str);
	}
	$condition->init();
	$trim= new trims($condition);
	$trim_group_qty_arr=$trim->getQtyArray_by_jobAndPrecostdtlsid();

	//$trim_group_amount_arr=$trim->getAmountArray_by_jobAndPrecostdtlsid();

	$trim_amountSourcing_arr=$trim->getAmountArray_precostdtlsidSourcing();
	/*echo'<pre>';
	print_r($trim_group_qty_arr); die;*/

	ob_start();
	?>
	<div style="width:2000px">
		<table width="2500">
	        <tr class="form_caption"><td colspan="18" align="center"><? echo $report_title; ?></td></tr>
	        <tr class="form_caption"><td colspan="18" align="center"><? echo $company_library[$company_name]; ?></td></tr>
		</table>
		<table class="rpt_table" width="2500" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
					<tr>
						<td colspan="24" align="left"><strong>Fabric Details</strong></td>
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
					<th width="80">PO Qty</th>
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
					<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $sl; ?>">
					<td rowspan="<?= $rowspan[$job_id] ?>" width="30" align="center"><?= $sl; ?></td>
					<td rowspan="<?= $rowspan[$job_id] ?>" width="80"  align="left"><?= $buyer_short_name_library[$job_data['buyer_name']]; ?></td>
					<td rowspan="<?= $rowspan[$job_id] ?>" width="80"  align="left"><?= $job_data['job_no'] ?></td>
					<td rowspan="<?= $rowspan[$job_id] ?>" align="left"><?= $lib_season_arr[$job_data['season_buyer_wise']] ?></td>
					<td rowspan="<?= $rowspan[$job_id] ?>"><p>
					<a href='#report_details' onclick="order_summery_popup('<? echo $job_id; ?>' ,'order_summery_data');"><?= $job_data['style_ref_no'] ?></a></p></td>
					<td rowspan="<?= $rowspan[$job_id] ?>"  align="right"><p>
					<a href='#report_details' onclick="order_summery_popup('<? echo $job_id; ?>' ,'order_qty_data');"><?= $job_data['job_quantity'] ?></a></p></td>
					<td rowspan="<?= $rowspan[$job_id] ?>"><?= $unit_of_measurement[$job_data['order_uom']] ?></td>
					<td rowspan="<?= $rowspan[$job_id] ?>"><?= $max_ship_arr[$job_id];  ?></td>
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
							$bom_qty=0;	$wo_qty=0; $bom_value=0; $wo_amount=0; $pi_amount=0;
							foreach ($fcolor_data as $gcolor_id => $row) {
								$fabric_color_id=$row['color'];
								$color_id = $gcolor_id;
								$gmts_color[$gcolor_id]=$color_arr[$gcolor_id];
								$gmts_color_id[$gcolor_id]=$gcolor_id;
								$wo_qty += $wo_data_arr[$job_id][$lib_yarn_id][$color_id][$fabric_color_id]['fin_fab_qnty'];
								$issueqty= $issue_qty_arr[$job_id][$lib_yarn_id][$fabric_color_id]['qty'];
								$issuepo= $issue_qty_arr[$job_id][$lib_yarn_id][$fabric_color_id]['po_id'];
								$supplier= $issue_qty_arr[$job_id][$lib_yarn_id][$fabric_color_id]['supplier'];
								$bom_value+= $fabric_amount_arr['woven']['grey'][$job_id][$lib_yarn_id][$color_id][$fabric_color_id];
								$bom_qty +=$fabric_qty_arr['woven']['grey'][$job_id][$lib_yarn_id][$color_id][$fabric_color_id];

								$inhouseqty= $receive_qty_arr[$job_id][$lib_yarn_id][$fabric_color_id]['qty'];
								$inhousepo=$receive_qty_arr[$job_id][$lib_yarn_id][$fabric_color_id]['po_id'];
								$rcv_balance=$wo_qty-$inhouseqty;
								$issue_balance=$inhouseqty-$issueqty;
								$rcv_rate=$receive_qty_arr[$job_id][$lib_yarn_id][$fabric_color_id]['rate'];
							}							

						 	?>
							<td align="left"><?= $color_arr[$fcolor_id] ?></td>
							<td align="left"><?= implode(", ", $gmts_color) ?></td>
							<td align="left"><?= implode(",",$wo_data_arr[$job_id][$lib_yarn_id][$color_id][$fabric_color_id]['wo_date'] )  ?></td>
							<td align="right"><?= fn_number_format($row['avg_cons'],4) ?></td>
							<td align="right"><a href='#report_details' onclick="order_req_qty_popup('<? echo $job_id; ?>','<? echo $lib_yarn_id; ?>','<? echo $fabric_color_id; ?>' ,'order_req_qty_data');"><?= fn_number_format($bom_qty,2)?></a></td>
							<td align="right"><a href='#report_details' onclick="order_wo_qty_popup('<? echo $job_id; ?>','<? echo $lib_yarn_id; ?>','<? echo $fabric_color_id; ?>','<? echo implode(",", $gmts_color_id) ?>' ,'order_wo_qty_data');"><?= fn_number_format($wo_qty,2)  ?></a></td>
							<td align="left"><?= $unit_of_measurement[$row['fabric_uom']] ?></td>
							<td align="right"><a href='#report_details' onclick="openmypage_inhouse('<? echo $job_id; ?>','<? echo $lib_yarn_id; ?>','<? echo $inhousepo; ?>','<? echo $fabric_color_id ?>' ,'booking_inhouse_info');"><?= fn_number_format($inhouseqty,2) ?></a></td>
							<td align="right"><?= fn_number_format($rcv_balance,2)  ?></td>
							<td align="right"><? if($issueqty>0){ ?><a href='#report_details' onclick="openmypage_issue('<? echo $job_id; ?>','<? echo $lib_yarn_id; ?>','<? echo $issuepo; ?>','<? echo $fabric_color_id?>', '<?echo $rcv_rate?>' ,'booking_issue_info');"><?= fn_number_format($issueqty,2)  ?></a><? } else echo '0.00'; ?></td>
							<td align="right"><?= fn_number_format($issue_balance,2) ?></td>
							<td align="left"><?= implode(",",$wo_data_arr[$job_id][$lib_yarn_id][$color_id][$fabric_color_id]['supplier'] ); ?></td>
							<td align="left"><?= $pi_data_arr[$job_id][$lib_yarn_id][$fabric_color_id]['pi_no'] ?></td>
							<td align="left"><?= $pi_data_arr[$job_id][$lib_yarn_id][$fabric_color_id]['blc_no'] ?></td>
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
					<th width="90">PO Qty</th>
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
					<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr2_<? echo $tsl; ?>','<? echo $bgcolor;?>')" id="tr2_<? echo $tsl; ?>">
						<td width="35" rowspan="<?= $rowspan ?>"><?= $tsl; ?></td>
						<td width="95" rowspan="<?= $rowspan ?>"align="left"><?= $buyer_short_name_library[$value['buyer_name']]; ?></td>
						<td width="95" rowspan="<?= $rowspan ?>"align="left"><?= $value['job_no_prefix_num']; ?></td>
						<td width="95" rowspan="<?= $rowspan ?>"align="left"><?= $lib_season_arr[$value['season_buyer_wise']] ?></td>
		                <td width="95" rowspan="<?= $rowspan ?>" align="left">
					<a href='#report_details' onclick="order_summery_popup('<? echo $job_id; ?>' ,'order_summery_data');"><?= $value['style_ref_no']; ?></a></td>
						<td width="60"  align="right" rowspan="<?= $rowspan ?>"><a href='#report_details' onclick="order_summery_popup('<? echo $job_id; ?>' ,'order_qty_data');"><?= $value['job_quantity']; ?></a></td>
						<td width="50" rowspan="<?= $rowspan ?>" align="left"><?= $unit_of_measurement[$value['order_uom']]; ?></th>
						<td width="60" rowspan="<?= $rowspan ?>" align="left"><?= $max_ship_arr[$job_id];  ?></td>
						<?

						$z=1;
						foreach ($value['trims_data'] as $trims_id=>$row) {
							// if($z!=1) echo '<tr>';
							if($z!=1) echo '<tr onclick="change_color(\'trs_'.$z.'\',\''.$bgcolor.'\')" id="trs_'.$z.'">';
							
							$req_qty = $trim_group_qty_arr[$value['job_no']][$trims_id];
							$wo_qty= $trim_wo_data_arr[$job_id][$trims_id]['wo_qnty'];
							$inhouse_qty= $trim_inhouse_qty[$job_id][$row['trim_group']]['qty'];
							$issue_qty= $trim_issue_qty[$job_id][$row['trim_group']]['qty'];
							$po_id= $trim_inhouse_qty[$job_id][$row['trim_group']]['po_id'];
							$tpo_id= $trim_issue_qty[$job_id][$row['trim_group']]['po_id'];
							$trimrcv_balance = $wo_qty-$inhouse_qty;
						?>
						<td width="100" align="left"><?= $item_library[$row['trim_group']]; ?></td>
						<td width="150" align="left"><?= $row['description']; ?></td>
						<td width="100" align="left"><?= $row['brand_sup_ref']; ?></td>
						<td width="80" align="left"><?= implode(",", $trim_wo_data_arr[$job_id][$trims_id]['wo_date'])?></td>
						<td width="80" align="left"><?= fn_number_format($row['cons_dzn_gmts'],4);; ?></td>
						<td width="100" align="right"><a href='#report_details' onclick="trim_req_qty_popup('<? echo $job_id; ?>','<? echo $trims_id; ?>' ,'trim_req_qty_data');"><?= fn_number_format($req_qty,2); ?></a></td>
						<td width="90" align="right"><a href='#report_details' onclick="trim_wo_qty_popup('<? echo $job_id; ?>','<? echo $trims_id; ?>' ,'trim_wo_qty_data');"><?= fn_number_format($wo_qty,2) ?></a></td>
		                <td width="50" align="right"><?= $unit_of_measurement[$row['cons_uom']]; ?></td>
		                <td width="80" align="right"><a href='#report_details' onclick="openmypage_trim_inhouse('<? echo $po_id; ?>','<? echo $row['trim_group']; ?>','trim_booking_inhouse_info');"><?= fn_number_format($inhouse_qty,2);  ?></a></td>
		                <td width="80" align="right"><?= fn_number_format($trimrcv_balance,2); ?></td>
		                <td width="70" align="right"><a href='#report_details' onclick="openmypage_trim_issue('<? echo $tpo_id; ?>','<? echo $row['trim_group']; ?>','trim_booking_issue_info');"><?= fn_number_format($issue_qty,2);  ?></a></td>
						<td width="90" align="right"><?= fn_number_format($inhouse_qty-$issue_qty,2); ?></td>
		                <td width="90" align="left"><?=implode(",", $trim_wo_data_arr[$job_id][$trims_id]['supplier'])?></td>
						<td width="120" align="left"><?= $trim_pi_data_arr[$job_id][$row['trim_group']]['pi_no']?></td>
						<td width="90" align="left"><?= $trim_pi_data_arr[$job_id][$row['trim_group']]['blc_no']?></td>
						<? $z++;
						 } ?>
					</tr>
				<?php 
					$tsl++;
					} 
				?>								
			</tbody>
		</table>
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
                    	<td text-align:right"><?  if(round($item_grand_total_order)>0){ echo fn_number_format(round($item_grand_total_order),0); } ?></td>
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
                		<td><? echo $req_qty; ?></td>
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
                	<td align="right"><strong><?= fn_number_format($total_po,2) ?></strong></td>
                	<td></td>
                	<td></td>
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
	<fieldset style="width:870px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="30">Sl</th>
                    <th width="60">Prod. ID</th>
                    <th width="100">MRR NO</th>
                    <th width="60">Challan NO</th>
                    <th width="60">Recv. Date</th>
                    <th width="70">Style No</th>
                    <th width="70">PI No</th>
                    <th width="80">Body Part</th>
                    <th width="120">Item Description</th>
                    <th width="60">Recv. Qty</th>
                    <th width="60">Rate</th>
                    <th width="60">Amount</th>
                    <th width="60">Uom</th>
                    <th width="60">Supplier</th>
                    <th width="60">Insert By</th>
				</thead>
                <tbody>
                <?
					$i=1;

					$receive_qty_data=sql_select("SELECT a.recv_number, d.id as prod_id, a.receive_date, a.challan_no, a.supplier_id, b.body_part_id, a.inserted_by,  d.detarmination_id, d.color, d.product_name_details, b.order_qnty,b.order_rate, b.order_amount, e.job_id, e.id as po_id, b.cons_uom, a.booking_no, f.style_ref_no from inv_receive_master a join inv_transaction b on a.id = b.mst_id join order_wise_pro_details c on b.id = c.trans_id join product_details_master d on d.id=c.prod_id join wo_po_break_down e on e.id=c.po_breakdown_id join wo_po_details_master f on e.job_id=f.id where a.entry_form=17 and a.receive_basis=1 and a.status_active=1 and a.is_deleted=0 and b.receive_basis=1 and b.transaction_type=1 and b.status_active=1 and b.is_deleted=0 and c.trans_type=1 and c.entry_form=17 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and e.job_id=$job_id and d.detarmination_id=$yarn_id and d.color=$color");

					//$dtlsArray=sql_select($receive_qty_data);

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
                            <td><? echo $row[csf('challan_no')]; ?></td>
                            <td><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td><? echo $row[csf('style_ref_no')]; ?></td>
                            <td><? echo $row[csf('booking_no')]; ?></td>
                            <td><? echo $body_part_arr[$row[csf('body_part_id')]]; ?></td>
                            <td><? echo $row[csf('product_name_details')]; ?></td>
                            <td><? echo number_format($qty,2); ?></td>
                            <td><? echo number_format($row[csf('order_rate')],2); ?></td>
                            <td><? echo number_format($row[csf('order_amount')],2); ?></td>
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

				  $issue_qty_data="SELECT a.issue_number, a.issue_date, a.inserted_by, b.prod_id, b.cons_uom, d.detarmination_id, d.color, b.cons_quantity, b.cons_rate, b.body_part_id,  b.cons_amount, d.product_name_details, e.job_id, e.id as po_id, f.job_no, f.style_ref_no  from inv_issue_master a join inv_transaction b on a.id = b.mst_id join order_wise_pro_details c on b.id = c.trans_id join product_details_master d on d.id=c.prod_id join wo_po_break_down e on e.id=c.po_breakdown_id join wo_po_details_master f on f.id=e.job_id where a.entry_form=19 and a.issue_basis=1 and a.status_active=1 and a.is_deleted=0 and b.transaction_type=2 and b.status_active=1 and b.is_deleted=0 and c.trans_type=2 and c.entry_form=19 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.id=$po_id and d.detarmination_id=$yarn_id and d.color=$color";
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
                		<td><? echo $trim_group_qty_arr[$row[csf('job_no')]][$trim_id]; ?></td>
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

	$is_short_arr=array(1=>"Partial",2=>"Main");

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
                	<? foreach ($main_data_sql as $row) { ?>                	
                	<tr>
                		<td>1</td>
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
                    <th width="100">PO/PI No</th>
                    <th width="100">Chalan No</th>
                    <th width="100">Recv. Date</th>
                    <th width="80">Item Description.</th>
                    <th width="80">Recv. Qty.</th>
                    <th width="80">Reject Qty.</th>
				</thead>
                <tbody>
                <?
					$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
					$i=1;

					$receive_rtn_data=array();
					$receive_rtn_qty_data=sql_select("select a.issue_number,a.issue_date,e.id,d.po_breakdown_id, c.item_group_id,sum(d.quantity) as quantity   from  inv_issue_master a,inv_transaction b, product_details_master c, order_wise_pro_details d,inv_receive_master e  where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and e.id=a.received_id   and b.transaction_type=3 and a.entry_form=49 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and d.po_breakdown_id in($po_id) and c.item_group_id='$item_name'  group by a.issue_number,a.issue_date,e.id,d.po_breakdown_id, c.item_group_id order by c.item_group_id");

					foreach($receive_rtn_qty_data as $row)
					{
					$receive_rtn_data[$row[csf('id')]][issue_number]=$row[csf('issue_number')];
					$receive_rtn_data[$row[csf('id')]][issue_date]=$row[csf('issue_date')];
					$receive_rtn_data[$row[csf('id')]][quantity]=$row[csf('quantity')];
					}

					$receive_qty_data="SELECT a.id, c.po_breakdown_id,b.item_group_id,b.prod_id as prod_id,a.challan_no,b.item_description, a.recv_number, a.receive_date, SUM(c.quantity) as quantity, sum(reject_receive_qnty) as reject_receive_qnty, b.booking_no
					from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c , product_details_master d
					where a.id=b.mst_id  and a.entry_form=24 and  a.item_category=4  and b.id=c.dtls_id and b.prod_id=d.id and b.trans_id=c.trans_id and c.trans_type=1 and  c.po_breakdown_id in($po_id)  and b.item_group_id='$item_name' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by  c.po_breakdown_id,b.item_group_id,b.prod_id,a.id,b.item_description, a.recv_number,a.challan_no, a.receive_date, b.booking_no";

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
                    	<td colspan="5" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_qty,2); ?></td>
                         <td><? echo number_format($tot_rej_qty,2); ?></td>
                    </tr>
                </tfoot>
            </table>

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
					where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and b.transaction_type=3 and d.trans_type=3 and a.entry_form=49 and d.entry_form=49 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and d.po_breakdown_id in($po_id) and c.item_group_id='$item_name'");


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

				 $mrr_sql=("SELECT a.id, a.issue_number,a.challan_no,b.prod_id,p.item_group_id, a.issue_date,b.item_description,SUM(c.quantity) as quantity
					from  inv_issue_master a,inv_trims_issue_dtls b, order_wise_pro_details c,product_details_master p
					where a.id=b.mst_id  and a.entry_form=25 and p.id=b.prod_id and b.id=c.dtls_id and b.trans_id=c.trans_id and c.trans_type=2 and a.is_deleted=0 and a.status_active=1 and
					b.status_active=1 and b.is_deleted=0 and  c.po_breakdown_id in($po_id) and p.item_group_id='$item_name' group by c.po_breakdown_id,p.item_group_id,b.item_description,a.issue_number,a.id,a.issue_date,b.prod_id,a.challan_no ");
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

				 $mrr_sql_ret="SELECT a.id, a.recv_number,a.challan_no,b.prod_id, p.item_group_id,a.receive_date,p.product_name_details,SUM(c.quantity) as quantity from   inv_receive_master a,inv_transaction b, order_wise_pro_details c,product_details_master p where a.id=b.mst_id  and a.entry_form=73 and c.entry_form=73 and p.id=b.prod_id  and b.id=c.trans_id and b.transaction_type=4 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and  c.po_breakdown_id in($po_id) and p.item_group_id='$item_name' group by a.id, a.recv_number, a.challan_no, b.prod_id, p.item_group_id, a.receive_date, p.product_name_details "; 
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
disconnect($con);
?>