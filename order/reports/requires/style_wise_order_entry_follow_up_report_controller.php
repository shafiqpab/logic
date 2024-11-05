<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.fabrics.php');
require_once('../../../includes/class4/class.trims.php');
require_once('../../../includes/class4/class.others.php');
require_once('../../../includes/class4/class.emblishments.php');
require_once('../../../includes/class4/class.washes.php');
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
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );
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
 <div align="center"><? echo create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "-- Searching Type --" ); ?></div>
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
     <div align="center"><? echo create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "-- Searching Type --" ); ?></div>
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


if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_library=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
	$country_name_library=return_library_array( "select id, country_name from lib_country", "id", "country_name"  );
	$lib_supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
	$lib_season_arr=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name"  );
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$user_name_arr=return_library_array( "select user_name,id from user_passwd where valid=1 order by user_name ASC", "id", "user_name");	
	$team_leader_name=return_library_array( "select id,team_name from lib_marketing_team where project_type=2 and status_active =1 and is_deleted=0 order by team_name", "id", "team_name");
	$team_member=return_library_array("select id,total_member from lib_marketing_team where project_type=2 and status_active =1 and is_deleted=0 order by total_member", "id", "total_member");
	$merchantArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	
	
	$company_name=str_replace("'","",$cbo_company_name);
		
	$cbo_date_type=str_replace("'","",$cbo_date_type);
	
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
		if($cbo_date_type==2){
		$date_cond="and to_date(to_char(b.pack_handover_date, 'DD-MON-YYYY')) BETWEEN '$start_date' AND '$end_date'";
		}
		else if($cbo_date_type==3){
			$date_cond="and to_date(to_char(b.po_received_date, 'DD-MON-YYYY')) BETWEEN '$start_date' AND '$end_date'";
		}
		else{
			$date_cond="and to_date(to_char(a.insert_date, 'DD-MON-YYYY')) BETWEEN '$start_date' AND '$end_date'";
		}
	}

	 
	$cbo_team_leader=str_replace("'","",$cbo_team_leader);
	

	if ($cbo_team_leader >0) {
		$team_leader_cond = " and a.team_leader=$cbo_team_leader";
	}else{
		$team_leader_cond ="";
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
	$main_sql="SELECT a.id, a.job_no, a.buyer_name, a.job_no_prefix_num, a.season_buyer_wise, a.style_ref_no, a.job_quantity,a.dealing_marchant,a.factory_marchant, b.id as po_id,a.insert_date,a.team_leader,b.po_received_date,a.company_name,a.inserted_by,b.pack_handover_date,a.set_smv,b.unit_price,c.order_total,c.order_quantity,b.doc_sheet_qty,a.gmts_item_id,b.sc_lc,a.order_uom from wo_po_details_master a , wo_po_break_down b ,wo_po_color_size_breakdown c where  a.id=b.job_id and a.id=c.job_id  and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_name in ($company_name) $year_cond $style_ref_cond $jobcond $buyer_id_cond $date_cond $team_leader_cond  order by a.style_ref_no";
		
	$main_data_sql=sql_select($main_sql);

	foreach ($main_data_sql as $row) {
		$style_wise_data_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]]['company']=$company_library[$row[csf('company_name')]];
		$style_wise_data_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]]['company_id']=$row[csf('company_name')];
		$style_wise_data_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]]['team_leader']=$team_leader_name[$row[csf('team_leader')]];
		// $style_wise_data_arr[$row[csf('style_ref_no')]]['team_member']=$team_member[$row[csf('team_leader')]];
		$style_wise_data_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]]['team_member_id']=$row[csf('team_leader')];
		$style_wise_data_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]]['job_no']=$row[csf('job_no')];	
		$style_wise_data_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]]['order_qty']+=$row[csf('order_quantity')];
		$style_wise_data_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]]['order_total']+=$row[csf('order_total')];
		$style_wise_data_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]]['dealing_marchant']=$row[csf('dealing_marchant')];
		$style_wise_data_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]]['factory_marchant']=$row[csf('factory_marchant')];
		$style_wise_data_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]]['insert_date']=change_date_format($row[csf('insert_date')],'','');
		$style_wise_data_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]]['po_received_date']=change_date_format($row[csf('po_received_date')],'','');;
		$style_wise_data_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]]['buyer_name']=$buyer_short_name_library[$row[csf('buyer_name')]];
		$style_wise_data_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]]['buyer_id']=$row[csf('buyer_name')];
		$style_wise_data_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]]['inserted_by']=$user_name_arr[$row[csf('inserted_by')]];
		$style_wise_data_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]]['mst_id']=$row[csf('mst_id')];
		$style_wise_data_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]]['approved']=$row[csf('approved')];
		$style_wise_data_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]]['phd']=change_date_format($row[csf('pack_handover_date')],'','');;
		$style_wise_data_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]]['set_smv']=$row[csf('set_smv')];
		$style_wise_data_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]]['unit_price']=$row[csf('unit_price')];
		$style_wise_data_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]]['style_ref_no']=$row[csf('style_ref_no')];
		//$style_wise_data_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]]['order_qty'] +=$row[csf('doc_sheet_qty')];
		$style_wise_data_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]]['uom']=$row[csf('order_uom')];
		$style_wise_data_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]]['gmts_item_id']=$garments_item[$row[csf('gmts_item_id')]];	
		$style_wise_data_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]]['sc_lc']=$row[csf('sc_lc')];	
		$job_arr[$row[csf('job_no')]]="*".$row[csf('job_no')]."*";
		$poId_arr[$row[csf('po_id')]]=$row[csf('po_id')];

	}
	unset($main_data_sql);
 	//  echo "<pre>";
    //  print_r($style_wises_data_arr);
	$job_list=str_replace("*","'",implode(",",$job_arr));
	// echo $job_list;die;

		
			$sourcing_data_sql=sql_select("select c.insert_date, a.style_ref_no, c.sourcing_approved, c.approved,a.job_no,c.sourcing_date,c.costing_date,c.costing_per,c.ready_to_approved from wo_po_details_master a, wo_po_break_down b ,wo_pre_cost_mst c where a.garments_nature=3 and a.job_no=b.job_no_mst and b.job_no_mst=c.job_no  and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and b.status_active=1 and a.company_name in ($company_name)  $year_cond order by a.id DESC");
			// and c.approved in(1)
			
		
			foreach ($sourcing_data_sql as $row) {
				
				$sourcing_cost_arr[$row[csf('job_no')]]['insert_date'] =change_date_format($row[csf('sourcing_date')],'','');
				$sourcing_cost_arr[$row[csf('job_no')]]['approved'] =$row[csf('sourcing_approved')];
				$pre_cost_arr[$row[csf('job_no')]]['insert_date'] =$row[csf('insert_date')];
				$pre_cost_arr[$row[csf('job_no')]]['approved'] =$row[csf('approved')];
				$pre_cost_arr[$row[csf('job_no')]]['ready_to_approved']=$row[csf('ready_to_approved')];
				$pre_cost_arr[$row[csf('job_no')]]['costing_date'] =change_date_format($row[csf('costing_date')],'','');;
				$pre_cost_arr[$row[csf('job_no')]]['costing_per']=$row[csf('costing_per')];

			}
			// 	echo "<pre>";
			// print_r($sourcing_cost_arr);

			$fabric_trims_booking_data=sql_select("select  a.booking_no_prefix_num,a.booking_no,c.style_ref_no,a.entry_form,a.booking_type,b.job_no, a.fabric_source,a.is_approved,a.item_category,a.cbo_level from wo_booking_mst a,wo_booking_dtls b, wo_po_details_master c,wo_po_break_down d where a.booking_no=b.booking_no and b.job_no=c.job_no and b.job_no=d.job_no_mst and b.po_break_down_id=d.id and a.booking_type in (1,2,6) and a.entry_form in (271,272,201) and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.company_id in ($company_name) group by a.booking_no_prefix_num,a.booking_no,c.style_ref_no,a.entry_form ,a.booking_type,b.job_no, a.fabric_source,a.is_approved,a.item_category,a.cbo_level order by c.style_ref_no asc");
			$ti=0;
			$wi=0;
			$fb=0;
			foreach ($fabric_trims_booking_data as $row) {
				if($row[csf('booking_type')]==2 && $row[csf('entry_form')]==272){
					$trimsBookingArr[$row[csf('job_no')]][$row[csf('style_ref_no')]][$row[csf('booking_no')]]['booking_no']=$row[csf('booking_no_prefix_num')];
					$trimsBookingArr[$row[csf('job_no')]][$row[csf('style_ref_no')]][$row[csf('booking_no')]]['is_approved']=$row[csf('is_approved')];
					$trimsBookingArr[$row[csf('job_no')]][$row[csf('style_ref_no')]][$row[csf('booking_no')]]['cbo_level']=$row[csf('cbo_level')];
					if($ti==0){
						$trims_cost_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]]['booking_no'] .=$row[csf('booking_no_prefix_num')].",";
						$ti++;
					}else{
						$trims_cost_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]]['booking_no'] .=$row[csf('booking_no_prefix_num')].",";
					}
				}else if($row[csf('booking_type')]==1 && $row[csf('entry_form')]==271){

					$fabric_booking_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]][$row[csf('booking_no')]]['entry_form'] =$row[csf('entry_form')];
					$fabric_booking_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]][$row[csf('booking_no')]]['fabric_source'] =$row[csf('fabric_source')];
					$fabric_booking_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]][$row[csf('booking_no')]]['fabric_nature'] =$row[csf('item_category')];
					$fabric_booking_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]][$row[csf('booking_no')]]['is_approved'] =$row[csf('is_approved')];
					$fabric_booking_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]][$row[csf('booking_no')]]['booking_no_prefix_num'] .=$row[csf('booking_no_prefix_num')]." ,";
					if($fb==0){
						$fabric_cost_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]]['booking_no_prefix_num'] .=$row[csf('booking_no_prefix_num')].",";
					
						$fb++;
					}else{
						$fabric_cost_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]]['booking_no_prefix_num'] .=$row[csf('booking_no_prefix_num')].",";
						$fabric_cost_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]]['booking_no'] .=$row[csf('booking_no')].",";
						
					}
				}else{
					if($wi==0){
						$wash_cost_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]]['booking_no'] .=$row[csf('booking_no_prefix_num')].",";
						$wi++;
					}else{
						$wash_cost_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]]['booking_no'] .=$row[csf('booking_no_prefix_num')].",";
					}
				}
			}

	
			$pi_number_data=sql_select("SELECT f.style_ref_no,d.pi_number,d.item_category_id,d.importer_id,d.id,f.job_no from  wo_booking_dtls b 
			,com_pi_item_details c , com_pi_master_details d ,wo_po_details_master f where  b.booking_no=c.work_order_no and b.job_no = f.job_no and  d.id=c.pi_id  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.item_category_id in (3,4) and d.status_active=1 and d.is_deleted=0 group by f.style_ref_no,d.pi_number,d.item_category_id,d.importer_id,d.id,f.job_no"); 
			$pi_data_arr=array();
			$fi=0;
			$ti=0;
			foreach ($pi_number_data as $row) {
			
			
				if($row[csf('item_category_id')]==4){
					$trimsfabric_pi_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]][4][$row[csf('pi_number')]]['pi_no']=$row[csf('pi_number')];
					$trimsfabric_pi_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]][4][$row[csf('pi_number')]]['importer_id']=$row[csf('importer_id')];
					$trimsfabric_pi_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]][4][$row[csf('pi_number')]]['update_id']=$row[csf('id')];
					if($ti==0){
						$trims_pi_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]]['pi_no'] .=$row[csf('pi_number')].",";
						$ti++;
					}else{
						$trims_pi_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]]['pi_no'] .=$row[csf('pi_number')].",";
					}
				}else{
					$trimsfabric_pi_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]][3][$row[csf('pi_number')]]['pi_no']=$row[csf('pi_number')];
					$trimsfabric_pi_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]][3][$row[csf('pi_number')]]['importer_id']=$row[csf('importer_id')];
					$trimsfabric_pi_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]][3][$row[csf('pi_number')]]['update_id']=$row[csf('id')];
					if($fi==0){
						$fabric_pi_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]]['pi_no'] .=$row[csf('pi_number')].",";
						$fi++;
					}else{
						$fabric_pi_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]]['pi_no'] .=$row[csf('pi_number')].",";
					}

				}
				
			}
			
			
			$btb_lc_number_data=sql_select("SELECT f.style_ref_no,d.pi_number,g.lc_number,f.job_no from  wo_booking_dtls b 
			,com_pi_item_details c , com_pi_master_details d ,wo_po_details_master f,com_btb_lc_master_details g,com_btb_lc_pi h
			where  b.booking_no=c.work_order_no and g.id=h.com_btb_lc_master_details_id and h.pi_id=d.id and b.job_no = f.job_no and  d.id=c.pi_id  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and g.item_basis_id in (1,2) and d.status_active=1 and d.is_deleted=0  and g.importer_id in ($company_name)
			group by f.style_ref_no,d.pi_number,g.lc_number,f.job_no"); 
			$bi=0;
			foreach ($btb_lc_number_data as $row){
					if($bi==0){
						$btb_lc_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]]['lc_no'] .=$row[csf('lc_number')].",";
						$bi++;
					}else{
						$btb_lc_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]]['lc_no'] .=$row[csf('lc_number')].",";
					}
			}

				$po_data=sql_select("SELECT a.job_no,a.id as job_id,a.style_ref_no, b.id, b.po_number
					FROM wo_po_details_master a,
						wo_po_break_down b
					WHERE a.job_no=b.job_no_mst  and 
						a.status_active=1 and 
						b.status_active=1 
						".where_con_using_array($poId_arr,1,'b.id')." and 
						a.company_name in ($company_name)
					group by a.job_no,a.id,a.style_ref_no, b.id, b.po_number");
				foreach($po_data as $val){
					$po_wise_data[$val[csf('id')]]['job_no']=$val[csf('job_no')];
					$po_wise_data[$val[csf('id')]]['style_ref_no']=$val[csf('style_ref_no')];
				}

					$fab_trims_rcv_data=sql_select("select a.recv_number_prefix_num,a.id,a.entry_form,a.booking_id as wo_pi ,a.item_category,d.po_break_down_id,c.po_breakdown_id  from inv_receive_master a left join inv_trims_entry_dtls b on a.id=b.mst_id and b.status_active=1 left join  wo_booking_dtls d on a.booking_id=d.booking_mst_id and d.is_short=2 and d.booking_type=1  ".where_con_using_array($poId_arr,1,'d.po_break_down_id')."   left join order_wise_pro_details c on b.id=c.dtls_id and b.trans_id=c.trans_id and b.prod_id=c.prod_id where a.entry_form in (17,24) and a.item_category in (3,4)  and a.company_id in ($company_name) group by a.recv_number_prefix_num,a.id,a.entry_form,a.booking_id ,a.item_category,d.po_break_down_id,c.po_breakdown_id");
					
  

				$tri=0;
				$fab=0;
				foreach ($fab_trims_rcv_data as $row) {
					
					if($row[csf('item_category')]==4 && $row[csf('entry_form')]==24){
						$job_no=$po_wise_data[$row[csf('po_breakdown_id')]]['job_no'];
						$style_ref_no=$po_wise_data[$row[csf('po_breakdown_id')]]['style_ref_no'];
						$trimsRcvArr[$job_no][$style_ref_no][$row[csf('id')]]['rcv_no']=$row[csf('recv_number_prefix_num')];
					}else{
						$job_no=$po_wise_data[$row[csf('po_break_down_id')]]['job_no'];
						$style_ref_no=$po_wise_data[$row[csf('po_break_down_id')]]['style_ref_no'];		
						$fabricRcvArr[$job_no][$style_ref_no][$row[csf('id')]]['rcv_no'] =$row[csf('recv_number_prefix_num')];
						$fabricRcvArr[$job_no][$style_ref_no][$row[csf('id')]]['wo_pi'] =$row[csf('wo_pi')];
					}
				}


				$fabric_trims_rcv_number_data=sql_select("SELECT f.style_ref_no,g.recv_number_prefix_num,g.item_category, g.entry_form,f.job_no,g.id,g.booking_id as  wo_pi  
				from  wo_booking_dtls b,
					com_pi_item_details c,
					com_pi_master_details d ,
					wo_po_details_master f,
					inv_receive_master g 
				where  b.booking_no=c.work_order_no and 
				b.job_no = f.job_no and  
				d.id=c.pi_id and g.booking_no=d.pi_number  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and g.entry_form in (17,24) and  g.item_category  in (3,4) and d.status_active=1 and d.is_deleted=0 and g.status_active=1  and g.company_id in ($company_name)  group by f.style_ref_no,g.recv_number_prefix_num,g.item_category, g.entry_form,f.job_no,g.id,g.booking_id order by g.recv_number_prefix_num asc"); 

				
				$tr=0;
				$fr=0;
				foreach ($fabric_trims_rcv_number_data as $row) {
					if($row[csf('item_category')]==4 && $row[csf('entry_form')]==24){
						$trimsRcvArr[$row[csf('job_no')]][$row[csf('style_ref_no')]][$row[csf('id')]]['rcv_no']=$row[csf('recv_number_prefix_num')];
						
						if($tr==0){
							$trims_rcv_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]]['rcv_no'] .=$row[csf('recv_number_prefix_num')].",";
							
							$tr++;
						}else{
							$trims_rcv_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]]['rcv_no'] .=$row[csf('recv_number_prefix_num')].",";
						}
					}else{
						$fabricRcvArr[$row[csf('job_no')]][$row[csf('style_ref_no')]][$row[csf('id')]]['rcv_no'] =$row[csf('recv_number_prefix_num')];
						$fabricRcvArr[$row[csf('job_no')]][$row[csf('style_ref_no')]][$row[csf('id')]]['wo_pi'] =$row[csf('wo_pi')];
						
						if($fr==0){
							$fabric_rcv_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]]['rcv_no'] .=$row[csf('recv_number_prefix_num')].",";
							$fr++;
						}else{
							$fabric_rcv_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]]['rcv_no'] .=$row[csf('recv_number_prefix_num')].",";
						}
					}
				}


	
				$sales_contact_data=sql_select("select wb.id, ci.id as idd, wm.gmts_item_id, wb.po_number, wb.po_total_price, sl.contract_no, wb.po_quantity, wb.pub_shipment_date as shipment_date,
				wb.job_no_mst, wm.style_ref_no, wm.order_uom, wm.total_set_qnty as ratio, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active, wm.brand_id,wm.job_no 
				from wo_po_break_down wb, wo_po_details_master wm, com_sales_contract_order_info ci ,com_sales_contract sl
				where wb.job_no_mst = wm.job_no and wb.id=ci.wo_po_break_down_id and  wb.job_no_mst in ($job_list) and sl.id=ci.com_sales_contract_id and ci.status_active = '1' and ci.is_deleted = '0' 
				order by ci.id");

				foreach ($sales_contact_data as $row) {

					$sales_contact_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]]=$row[csf('contract_no')];
				}

				$el_contact_data=sql_select("select  el.export_lc_no, wb.job_no_mst, wm.style_ref_no,wm.job_no
				from wo_po_break_down wb, wo_po_details_master wm, com_export_lc_order_info ci ,com_export_lc el
				where wb.job_no_mst = wm.job_no and wb.id=ci.wo_po_break_down_id and ci.com_export_lc_id=el.id and ci.status_active = '1' and ci.is_deleted = '0' and  wb.job_no_mst in ($job_list) order by ci.id ");

				foreach ($el_contact_data as $row) {
					$el_contact_arr[$row[csf('job_no')]][$row[csf('style_ref_no')]]=$row[csf('export_lc_no')];
				}

				


	//  echo'<pre>';
	//    print_r($el_contact_arr); 

	//===================start===============Print Report Format======================================
	$print_report_format=return_field_value("format_id","lib_report_template","template_name in ($company_name) and module_id=2 and report_id=122 and is_deleted=0 and status_active=1");//Pre-Costing V2-Woven

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
		$report_arr[$report_val[csf('template_name')]][122]=$report_action;

	}
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
	//--------------------Trims Rcv------------------------


	$trims_rcv_sql_data=sql_select("select format_id,template_name from lib_report_template where template_name in ($company_name) and module_id=6 and report_id=230 and is_deleted=0 and status_active=1");

	foreach($trims_rcv_sql_data as $report_val){

		$reportIds=explode(",",$report_val[csf('format_id')]);	
			if($reportIds[0]==78){ $report_action='trims_receive_entry_print';}
			if($reportIds[0]==84){ $report_action='trims_receive_entry_print2';}
			
		$report_arr[$report_val[csf('template_name')]][230]=$report_action;


	}
	//--------------------Fabric Rcv------------------------


	$fab_rcv_sql_data=sql_select("select format_id,template_name from lib_report_template where template_name in ($company_name) and module_id=6 and report_id=125 and is_deleted=0 and status_active=1");

	foreach($fab_rcv_sql_data as $report_val){

		$reportIds=explode(",",$report_val[csf('format_id')]);	
			if($reportIds[0]==78){ $report_action='gwoven_finish_fabric_receive_print';}
			if($reportIds[0]==66){ $report_action='gwoven_finish_fabric_receive_print_3';}
			
		$report_arr[$report_val[csf('template_name')]][125]=$report_action;


	}
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

	//-----------------------------Trims and fabric PI----------------------------------------
		$trims_fabric_pi_data=sql_select("select format_id,template_name from lib_report_template where template_name in ($company_name) and module_id=5 and report_id in(183) and is_deleted=0 and status_active=1");

		foreach($trims_fabric_pi_data as $report_val){

			$reportIds=explode(",",$report_val[csf('format_id')]);	

		// if($reportIds[0]==86){
		// 	$report_action='print';
		// }
		// if($reportIds[0]==116){
		// 	$report_action='print_wf';
		// }
		// if($reportIds[0]==85){
		// 	$report_action='print_sf';
		// }
		// if($reportIds[0]==751){
		// 	$report_action='print_pi';
		// }

		$report_arr[$report_val[csf('template_name')]][183]=$reportIds[0];
	}
	
	//================end======================================================

	ob_start();
	?>
	<br>
	<div style="width:3680px">


    <fieldset style="width:3780px; float:left;">
            <legend>Report Details Part</legend>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="3750" class="rpt_table" align="left">
                <thead>
				<tr>
					<th width="30">SL</th>
					<th width="100">Company Name</th>
					<th width="100">Team Name</th>
					<th width="100">Team Leader</th>
					<th width="100">Dealing Merchant</th>
					<th width="100">Factory Merchant</th>
					<th width="100">Buyer</th>
					<th width="100">Style</th>
					<th width="100">Job No</th>
					<th width="100">Item Description</th>
					<th width="100">Insert Date</th>
					<th width="100">OPD Date</th>
					<th width="100">PHD Date</th>
					<th width="100">Pre-Costing/Budget <br>Insert Date</th>
					<th width="100">Pre-Costing/Budget <br>Ready To Approved</th>
					<th width="100">First Pre-Costting <br>Approval Date</th>
					<th width="100">Last Pre-Costting <br>Approval Date</th>
					<th width="100">Pre Costing/Budget <br>Approval status</th>
					<th width="100">Sourcing-Costing <br>Insert Date</th>
					<th width="100">First Sourcing-Costting <br>Approval Date</th>
					<th width="100">Last Sourcing-Costting <br>Approval Date</th>
					<th width="100">Sourcing Costing <br>Approval status</th>
					<th width="100">Fabric Booking No</th>
					<th width="100">Trims Booking No </th>
					<th width="100">Wash Booking No</th>
					<th width="100">Fabric PI No</th>
					<th width="100">Trims PI No </th>
					<th width="100">Sales Contract/Export <br> LC Entry No</th>
					<th width="100">BTB/Margin LC No</th>
					<th width="100">Fabric Receive No </th>
					<th width="100">Trims Receive No</th>
					<th width="100">SMV</th>
					<th width="100">Order Qty. </th>
					<th width="100">UOM</th>
					<th width="100">Total SMV</th>
					<th width="100">Unit Price </th>
					<th width="100">Value</th>
					<th width="100">Insert By</th>
				</tr>
                </thead>
            </table>
			<div style="max-height:425px; overflow-y:scroll; width:3780px;" id="scroll_body">
                <table border="1" class="rpt_table" width="3750" rules="all" id="table_body" align="left">
                    <tbody>
					<? $sl=1;  
				$tmi=0;
				$i=0;
				foreach ($style_wise_data_arr as $job_id => $style_data) {
					foreach ($style_data as $style_id => $row) {

					$team_member_id=$row['team_member_id'];	
					$team_member_data=sql_select("select b.team_member_name,a.id from lib_marketing_team a,lib_mkt_team_member_info b where a.project_type=2 and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.lib_mkt_team_member_info_id=b.id and a.id=$team_member_id order by b.team_member_name");
	
						foreach ($team_member_data as  $row) {
							if($i==0){
								$member_name_arr[$row[csf('id')]].=$row[csf('team_member_name')].",";	
								$i++;							
							}else{
								$member_name_arr[$row[csf('id')]].=$row[csf('team_member_name')].",";							
							}
							
						}
					}
				}
		// 		echo "<pre>";
		// print_r($pre_cost_arr);
				foreach ($style_wise_data_arr as $job_id => $style_data) {
					foreach ($style_data as $style_id => $row) {

					
					$job_no=$row['job_no'];
					$mst_id=return_field_value("id", "wo_pre_cost_mst", "job_no='$job_no'  and status_active=1 and is_deleted=0");
				
					$p_c_approval_first_date=sql_select("select  id,mst_id,approved_date,entry_form,approved_no from approval_history where  current_approval_status=1 and entry_form=15 and mst_id=$mst_id group by mst_id,approved_date,id,entry_form,approved_no order by mst_id asc");
					$p_c_approval_last_date=sql_select("select  id,mst_id,approved_date,entry_form,approved_no from approval_history where  current_approval_status=1 and entry_form=15 and mst_id=$mst_id group by mst_id,approved_date,id,entry_form,approved_no order by mst_id desc");
					$s_c_approval_first_date=sql_select("select  id,mst_id,approved_date,entry_form,approved_no from approval_history where  current_approval_status=1 and entry_form=47 and mst_id=$mst_id group by mst_id,approved_date,id,entry_form,approved_no order by mst_id asc");
					$s_c_approval_lsat_date=sql_select("select  id,mst_id,approved_date,entry_form,approved_no from approval_history where  current_approval_status=1 and entry_form=47 and mst_id=$mst_id group by mst_id,approved_date,id,entry_form,approved_no order by mst_id desc");		
					$bgcolor = ($sl%2==0) ? "#ffffff" : "#f6faff";
					$reportType=$report_arr[$row['company_id']][122];
					$wvnType=$report_arr[$row['company_id']][138];
					$triceRcvType=$report_arr[$row['company_id']][230];
					$fabRcvType=$report_arr[$row['company_id']][125];
					$trbType=$report_arr[$row['company_id']][219];
					$triFabPiType=$report_arr[$row['company_id']][183];
					$costing_per=$pre_cost_arr[$row['job_no']]['costing_per'];
					$costing_date=$pre_cost_arr[$row['job_no']]['costing_date'];
					$avg_rate=$row['order_total']/$row['order_qty'];
					//echo $wvnType.'DDx';
					
					?>

				<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
					<td width="30"><?= $sl; ?></td>
					<td style="word-break:break-all" width="100" align="center"><?= $row['company']; ?></td>
					<td style="word-break:break-all" width="100" align="center"><?= $row['team_leader'] ?></td>
					<td style="word-break:break-all" width="100" align="center"><?= implode(", ",array_unique(explode(",",$member_name_arr[$row['team_member_id']])))?></td>
					<td style="word-break:break-all" width="100" align="center"><?= $merchantArr[$row["dealing_marchant"]] ?></td>
					<td style="word-break:break-all" width="100" align="center"><?= $merchantArr[$row["factory_marchant"]]; ?></td>
					<td style="word-break:break-all" width="100" align="center"><?= $row['buyer_name'] ?></td>
					<td style="word-break:break-all" width="100" align="center"><?= $row['style_ref_no'] ?></td>
					<td style="word-break:break-all" width="100" align="center"><a href="#report_details" onClick="report_generate('<?=$row['company_id']; ?>','<?=$row['job_no']; ?>','<?=$row['buyer_id']; ?>','<?=$row['style_ref_no']; ?>','<?=$costing_date; ?>','','<?=$costing_per; ?>','<?=$reportType; ?>','425')"><?=$row['job_no']?></a></td>
					<td style="word-break:break-all" width="100" align="center"><?= $row['gmts_item_id'] ?></td>				
					<td style="word-break:break-all" width="100" align="center">&nbsp;<?= $row['insert_date'] ?></td>					
					<td style="word-break:break-all" width="100" align="center">&nbsp;<?= $row['po_received_date'] ?></td>
					<td style="word-break:break-all"  width="100" align="center"><?= $row['phd'] ?></td>
					<td style="word-break:break-all" width="100" align="center">&nbsp;<?=$pre_cost_arr[$row['job_no']]['insert_date'] ?></td>
					<td style="word-break:break-all" width="100" align="center"><?
							if($pre_cost_arr[$row['job_no']]['ready_to_approved'] ==1){	echo 'Yes';	}else{	echo 'No';}
					     ?>
					</td>
					<td width="100" align="center">&nbsp;<?=change_date_format($p_c_approval_first_date[0]['APPROVED_DATE'],'',''); ?></td>
					<td width="100" align="center">&nbsp;<?= change_date_format($p_c_approval_last_date[0]['APPROVED_DATE'],'',''); ?></td>
					<td width="100" align="center"><?
							if($pre_cost_arr[$row['job_no']]['approved'] ==1){
									echo 'Yes';
							}else{
								echo 'No';
							}
					     ?>
					</td>
					<td width="100" align="center">&nbsp;<?= $sourcing_cost_arr[$row['job_no']]['insert_date']  ?></td>
					<td width="100" align="center">&nbsp;<?= change_date_format($s_c_approval_first_date[0]['APPROVED_DATE'],'',''); ?></td>
					<td width="100" align="center">&nbsp;<?= change_date_format($s_c_approval_lsat_date[0]['APPROVED_DATE'],'',''); ?></td>
					<td width="100" align="center"><? 
							if($sourcing_cost_arr[$row['job_no']]['approved'] ==1){
									echo 'Yes';
							}else{
								echo 'No';
							}
					     ?>
					</td>

					<td width="100" align="left">
						<?
						$bookingArr=explode(",",$fabric_cost_arr[$job_id][$style_id]['booking_no']);
						//$variable="";
						foreach($bookingArr as $bno){
							$fabric_source=$fabric_booking_arr[$job_id][$style_id][$bno]['fabric_source'];
							$entry_form=$fabric_booking_arr[$job_id][$style_id][$bno]['entry_form'];
							$fabric_nature=$fabric_booking_arr[$job_id][$style_id][$bno]['fabric_nature'];
							$is_approved=$fabric_booking_arr[$job_id][$style_id][$bno]['is_approved'];
							$booking_no_prefix_num=$fabric_booking_arr[$job_id][$style_id][$bno]['booking_no_prefix_num'];
							//company,booking_no,fabric_natu,fabric_source,approved_id,po_id,type,entry_from
							?>
							<a href="#report_details" onClick="booking_report_generate('<?=$row['company_id']; ?>','<?=$bno; ?>','<?=$fabric_nature; ?>','<?=$fabric_source; ?>','<?=$is_approved; ?>','<?=$row['po_id']; ?>','<?=$wvnType; ?>','<?=$entry_form; ?>')"><?=$booking_no_prefix_num."  "?>
						<?}?>

					</td>
					<td width="100">
						
						<? 
						
						foreach($trimsBookingArr[$job_id][$style_id] as $tribNo=>$val){						
							?>
							<a href="#report_details" onClick="booking_report_generate('<?=$val['company_id']; ?>','<?=$tribNo; ?>','<?=$val['buyer_name']; ?>','<?=$val['cbo_level']; ?>','<?=$val['is_approved']; ?>','','<?=$trbType; ?>','272')"><?=$val['booking_no']." ,";?>
							</a>
						<?}
				
						?>
					</td>
					<td width="100"><?= implode(", ",array_unique(explode(",",$wash_cost_arr[$job_id][$style_id]['booking_no']))) ?></td>
					<td width="100"><?
					
					foreach($trimsfabric_pi_arr[$job_id][$style_id][3] as $fabPi=>$val){						
						?>
						<a href="#report_details" onClick="booking_report_generate('<?=$val['importer_id']; ?>','<?=$val['update_id']; ?>','3','','','','<?=$triFabPiType; ?>','167')"><?=$fabPi." ,";?>
							</a>
						<?}
					
					?></td>
					<td width="100"><?
					foreach($trimsfabric_pi_arr[$job_id][$style_id][4] as $triPi=>$val){						
						?>
						<a href="#report_details" onClick="booking_report_generate('<?=$val['importer_id']; ?>','<?=$val['update_id']; ?>','4','','','','<?=$triFabPiType; ?>','167')"><?=$triPi." ,";?>
							</a>
						<?}
					?></td>
					<td width="100"><?
					
					if($sales_contact_arr[$job_id][$style_id][0]==""){
						echo $el_contact_arr[$job_id][$style_id][0];
					}elseif($el_contact_arr[$job_id][$style_id][0]==""){
							echo $sales_contact_arr[$job_id][$style_id][0];
					}else{
						echo $sales_contact_arr[$job_id][$style_id][0]."/".$el_contact_arr[$job_id][$style_id][0];
					}
					
					?></td>
					<td width="100" align="right"><?= implode(", ",array_unique(explode(",",$btb_lc_arr[$job_id][$style_id]['lc_no']))) ?></td>
					<td width="100" align="right">
						
					<? 
						foreach($fabricRcvArr[$job_id][$style_id] as $frcvid=>$val){						
							?>
							<a href="#report_details" onClick="booking_report_generate('<?=$row['company_id']; ?>','<?=$val['wo_pi']; ?>','','','','<?=$frcvid; ?>','<?=$fabRcvType; ?>','17')"><?=$val['rcv_no']." ,";?>
							</a>
						<?}
						?>
					</td> 
					<td width="100" align="right"><?
						foreach($trimsRcvArr[$job_id][$style_id] as $rid=>$val){
							?>
							<a href="#report_details" onClick="booking_report_generate('<?=$row['company_id']; ?>','','','','','<?=$rid; ?>','<?=$triceRcvType; ?>','24')"><?=$val['rcv_no']." ,";?>
							</a>
						<?}
						?>
				
					<td width="100" align="right"><?= number_format($row['set_smv'], 2,'.','') ; ?></td>
					<td width="100" align="right"><?= number_format($row['order_qty'], 2,'.',''); $total_qty=$row['order_qty']; ?></td>
					<td width="100" align="right"><?= $unit_of_measurement[$row['uom']]  ?></td>
					<td width="100" align="right"><?= number_format($row['order_qty']*$row['set_smv'], 2,'.','')  ?></td>
					<td width="100" align="right"><?= number_format($avg_rate, 4,'.','')  ?></td>
					<td width="100" align="right"><?= number_format($row['order_total'], 2,'.','')  ?></td>
					<td width="100" align="center"><?= $row['inserted_by'] ?></td>
					</tr>
				
				<?
						$sl++;
						$tot_order_qty+=$row['order_qty'];
						$tot_smv+=$row['order_qty']*$row['set_smv'];
						$tot_value+=$row['order_total'];
				
					}
				}
				?>
					</tbody>
			</table>
			<table border="1" class="rpt_table" width="3750" rules="all" id="table_body" align="left">
			<tr>
					<td width="30"></td>
					<td width="100"></td>
					<td width="100"></td>
					<td width="100"></td>
					<td width="100"></td>
					<td width="100"></td>
					<td width="100"></td>
					<td width="100"></td>
					<td width="100"></td>
					<td width="100"></td>				
					<td width="100"></td>					
					<td width="100"></td>
					<td width="100"></td>
					<td width="100"></td>
					<td width="100"></td>
					<td width="100"></td>
					<td width="100"></td>
					<td width="100"></td>
					<td width="100"></td>
					<td width="100"></td>
					<td width="100"></td>
					<td width="100"></td>
					<td width="100"></td>
					<td width="100"></td>
					<td width="100"></td>
					<td width="100"></td>
					<td width="100"></td>
					<td width="100"></td>
					<td width="100"></td>
					
					
					<td width="100" align="right"></td>
					<!-- <td width="100"id="tot_order_qty" align="right"></td> -->
					<td width="100" align="right"><b>Total </b></td>
					<td width="100" id="tot_smv" align="right"><?=number_format($tot_smv, 2,'.','');?></td>
					<td width="100" align="right"><?=number_format($tot_order_qty, 2,'.','');?></td>
					
					
					<td width="100" ></td>
					<td width="100" align="right"></td>
					<td width="100"></td>
					<td width="100" align="right"><?=number_format($tot_value, 2,'.','');?></td>
					<td width="100"></td>
					</tr>
			</table>

			</div>
      	  </fieldset>
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
if($action=="report_generate2")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	
	$company_name=str_replace("'","",$cbo_company_name);		
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_job_no=trim($txt_job_no);
	if($txt_job_no!="" || $txt_job_no!=0) $jobcond="and a.job_no_prefix_num in('".$txt_job_no."')"; else $jobcond="";
	if(str_replace("'","",$txt_style_ref)!="") $style_ref_cond=" and a.style_ref_no like '%".str_replace("'","",$txt_style_ref)."%'"; else $style_ref_cond="";
	$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");

	$cbo_year=str_replace("'","",$cbo_year);
	if($db_type==0)
	{
		if(trim($cbo_year)!=0) $year_cond=" and YEAR(a.insert_date)=$cbo_year"; else $year_cond="";
	}
	else if($db_type==2)
	{
		if(trim($cbo_year)!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year"; else $year_cond="";
	}
	$main_sql="SELECT a.id, a.job_no, a.buyer_name, a.job_no_prefix_num, a.season_buyer_wise, a.style_ref_no, a.job_quantity,a.dealing_marchant,a.factory_marchant, b.id as po_id,a.insert_date,a.team_leader,b.po_received_date,a.company_name,a.inserted_by,b.pack_handover_date,a.set_smv,b.unit_price,sum(c.order_quantity) as order_quantity,a.gmts_item_id,b.sc_lc,a.order_uom from wo_po_details_master a , wo_po_break_down b ,wo_po_color_size_breakdown c where  a.id=b.job_id and a.id=c.job_id  and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_name in ($company_name) $year_cond $style_ref_cond $jobcond $buyer_id_cond $date_cond $team_leader_cond  group by  a.id, a.job_no, a.buyer_name, a.job_no_prefix_num, a.season_buyer_wise, a.style_ref_no, a.job_quantity,a.dealing_marchant, b.id ,a.insert_date,a.team_leader,a.factory_marchant,b.po_received_date,a.company_name,a.inserted_by,b.pack_handover_date,a.set_smv,b.unit_price,a.gmts_item_id,b.sc_lc,a.order_uom order by a.style_ref_no";
		
	$main_data_sql=sql_select($main_sql);

	foreach ($main_data_sql as $row) {

		$styleref=$row[csf('style_ref_no')];
		$buyer=$row[csf('buyer_name')];
		$jobNo=$row[csf('job_no')];
		$orderQuantity=$row[csf('job_quantity')];	
		$set_smv =$row[csf('set_smv')];
		$unit_price=$row[csf('unit_price')];
		$order_qty +=$row[csf('order_quantity')];
		$order_uom=$row[csf('order_uom')];
		$gmts_item_id=$garments_item[$row[csf('gmts_item_id')]];	
		$sc_lc=$row[csf('sc_lc')];	

      //==================array value=================================
		$job_arr[$row[csf('job_no')]]="*".$row[csf('job_no')]."*";
		$poId_arr[$row[csf('po_id')]]=$row[csf('po_id')];

	}
	unset($main_data_sql);
		//  echo "<pre>";
		//  print_r($style_wises_data_arr);
		$job_list=str_replace("*","'",implode(",",$job_arr));
		// echo $job_list;die;

		
		

			$fabric_trims_booking_data=sql_select("SELECT c.job_no,c.pre_cost_fabric_cost_dtls_id,c.po_break_down_id,c.sensitivity, c.gmt_item,c.brand_supplier,c.wo_qnty as wo_qnty, c.amount as amount , e.emb_name 
			from wo_po_details_master a, wo_po_break_down  d , wo_booking_dtls c ,wo_booking_mst b , wo_pre_cost_embe_cost_dtls e
			where a.job_no=d.job_no_mst and a.job_no=c.job_no and  d.id=c.po_break_down_id and c.booking_no=b.booking_no and b.entry_form=574  and c.pre_cost_fabric_cost_dtls_id=e.id and c.job_no='$jobNo' and c.booking_type=6 and a.garments_nature=3  and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and e.status_active=1");
			$ti=0;
			$wi=0;
			$fb=0;
			foreach ($fabric_trims_booking_data as $row) {
				if($row[csf('emb_name')]==3){
					$wash_b2b_value+=$row[csf('amount')];
				}else{
					$emblishment_b2b_value+=$row[csf('amount')];
				}
			}

			$pi_number_data=sql_select("SELECT b.booking_mst_id,f.job_no, sum(c.amount) as pi_amount , b.amount as wo_amount from wo_booking_dtls b join com_pi_item_details c on b.booking_no=c.work_order_no join com_pi_master_details d on d.id=c.pi_id join wo_po_details_master f on b.job_no = f.job_no where b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.item_category_id in (3) and d.status_active=1 and d.is_deleted=0 and b.job_no='$jobNo' group by b.booking_mst_id,f.job_no, b.amount");
			
			$pi_data_arr=array();
			$fi=0;
			$ti=0;
			//$fabric_b2b_value+=$row[csf('amount')];
			foreach ($pi_number_data as $row) {
				$net_pi_value=$row[csf('pi_amount')];
				$job_wise_wo_amount+=$row[csf('wo_amount')];
				$booking_id_arr[$row[csf('booking_mst_id')]]=$row[csf('booking_mst_id')];
			}
			$booking_id_str=implode(',',$booking_id_arr);
			$fabric_b2b_value=0;
			if(count($booking_id_arr)>0){
				$fabric_booking_amount=sql_select("SELECT sum(b.amount) as wo_amount from wo_booking_mst a join wo_booking_dtls b on a.id=b.booking_mst_id where a.status_active=1 and b.is_deleted=0 and b.booking_mst_id in ($booking_id_str) and b.booking_type=1");
				
				foreach($fabric_booking_amount as $row){
					$booking_amount=$row[csf('wo_amount')];
				}

				$job_wo_ratio=$job_wise_wo_amount/$booking_amount*100;
				$fabric_b2b_value=$job_wo_ratio*$net_pi_value/100;
			}
			

			$pi_number_data_acc=sql_select("SELECT f.style_ref_no,d.item_category_id,f.job_no,c.amount
			from  wo_booking_dtls b,com_pi_item_details c , com_pi_master_details d ,wo_po_details_master f where  b.booking_no=c.work_order_no and b.id=c.work_order_dtls_id   and b.job_no = f.job_no and  d.id=c.pi_id and  f.style_ref_no= c.buyer_style_ref  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.item_category_id in (4) and d.status_active=1 and d.is_deleted=0 and f.job_no='$jobNo'  and c.order_id in(".implode(",",$poId_arr).")");
			foreach ($pi_number_data_acc as $row) {
				if($row[csf('item_category_id')]==4){
					$accessories_b2b_value+=$row[csf('amount')];
				} 
			}			

			$sql_other = "select fabric_cost, trims_cost, embel_cost, wash_cost, margin_dzn,comm_cost, commission, lab_test, inspection, cm_cost, freight, currier_pre_cost, certificate_pre_cost, design_cost, studio_cost, common_oh,common_oh_percent, depr_amor_pre_cost, interest_cost, incometax_cost, total_cost,price_dzn,a.costing_per from wo_pre_cost_mst a ,wo_pre_cost_dtls b where  a.job_no=b.job_no and a.job_no='$jobNo'  and a.status_active=1 and  a.is_deleted=0";
			$pre_other_result=sql_select($sql_other);//$summ_fob_value_pcs=0;

			foreach( $pre_other_result as $row )
			{

				if($row[csf("costing_per")]==1){$order_price_per_dzn=12;$costing_val=" DZN";}
				else if($row[csf("costing_per")]==2){$order_price_per_dzn=1;$costing_val=" PCS";}
				else if($row[csf("costing_per")]==3){$order_price_per_dzn=24;$costing_val=" 2 DZN";}
				else if($row[csf("costing_per")]==4){$order_price_per_dzn=36;$costing_val=" 3 DZN";}
				else if($row[csf("costing_per")]==5){$order_price_per_dzn=48;$costing_val=" 4 DZN";}			
			 

				$fob_pcs=$row[csf('price_with_commn_pcs')];
				$margin_dzn=$row[csf('margin_dzn')];
				
				
				$cm_cost_dzn=$row[csf('cm_cost')]+$row[csf('margin_dzn')];
				$cm_cost_pcs=$row[csf('cm_cost')]/$order_price_per_dzn;
				$cm_cost_req=($row[csf('cm_cost')]/$order_price_per_dzn)*$order_job_qnty;
				$tot_cm_qty_dzn=$row[csf('cm_cost')]*$po_qty_dzn;
				$cmCost=(($row[csf('cm_cost')]+$row[csf('margin_dzn')])/$order_price_per_dzn)*$ordQtyUom;			
				$tot_summ_fob_pcs=$row[csf('total_cost')];
				

			}

			$condition= new condition();
			if($jobNo !=''){
				$condition->job_no("='$jobNo'");
			}
			$condition->init();
			$fabric= new fabric($condition);
			$trim= new trims($condition);
			$wash= new wash($condition);
			$emblishment= new emblishment($condition);

			$fabric_amount_arr=$fabric->getAmountArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
			$trim_amount_arr=$trim->getAmountArray_precostdtlsid();				
			$emblishment_amountArr=$emblishment->getAmountArray_by_jobAndEmblishmentid();
			$wash_amountArr=$wash->getAmountArray_by_jobAndEmblishmentid();
			$pre_fab_arr="select  b.id, b.job_no,b.body_part_id,b.lib_yarn_count_deter_id as deter_min_id, b.fab_nature_id, b.color_type_id, b.fabric_description as fab_desc,b.uom,b.avg_cons,b.avg_cons_yarn, b.avg_process_loss,b.construction,b.composition,b.fabric_source,b.gsm_weight, b.rate,b.amount,b.avg_finish_cons,b.sourcing_rate,b.sourcing_nominated_supp from wo_pre_cost_fabric_cost_dtls  b where  b.status_active=1 and b.is_deleted=0 and b.job_no='$jobNo' and b.avg_cons>0 order by b.id ";
			$pre_fab_result=sql_select($pre_fab_arr);
			foreach($pre_fab_result as $row)
			{
				$fab_req_amount+=$fabric_amount_arr['knit']['grey'][$row[csf("id")]][$row[csf("uom")]]+$fabric_amount_arr['woven']['grey'][$row[csf("id")]][$row[csf("uom")]];
				 
				
			}
			 
		$pre_trim_arr="select b.seq,b.id,c.trim_type,c.item_name,b.description,b.trim_group,b.tot_cons,b.ex_per ,b.cons_dzn_gmts,b.cons_uom as uom, b.rate,b.amount,b.sourcing_rate,b.sourcing_nominated_supp,b.remark from wo_pre_cost_trim_cost_dtls b,lib_item_group c where  c.id=b.trim_group  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.cons_dzn_gmts>0  and b.job_no='$jobNo' order by b.seq";//and b.fabric_source=2
		$pre_trim_result=sql_select($pre_trim_arr);
		foreach($pre_trim_result as $row){
			$trim_req_amount+=$trim_amount_arr[$row[csf('id')]];
		}

		$pre_wash_arr="select b.job_no,b.id,b.emb_name,b.emb_type,b.cons_dzn_gmts, b.rate,b.amount,b.sourcing_rate,b.sourcing_nominated_supp from wo_pre_cost_embe_cost_dtls  b where  b.status_active=1 and b.is_deleted=0  and b.job_no='$jobNo'  order by b.emb_name";//and b.fabric_source=2
		$pre_wash_result=sql_select($pre_wash_arr);

		foreach($pre_wash_result as $row)
			{
			 
				if($row[csf('emb_name')]==3) //Wash
				{
					$wash_req_amount+=$wash_amountArr[$row[csf('job_no')]][$row[csf('id')]];
				}else{
					$emb_req_amount+=$emblishment_amountArr[$row[csf('job_no')]][$row[csf('id')]];
				}
			
			}

	ob_start();
	$total_b2b_value=$fabric_b2b_value+$accessories_b2b_value+$wash_b2b_value+$emblishment_b2b_value;
	$total_fab_value=$order_qty*$unit_price;
	?>
	<br>
	<style>
		td{
			font-size: 14px;
		}
	</style>
		<div style="width:600px">
		
			
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" align="left">
                <thead>
				
				<tr>
					<td colspan="2" align="center"><h1><b>Order Summary</b></h1> </td>					
				</tr>
				<tr>
					
					<td width="200"> Buyer Name </td>
					<td width="200" align="right" ><?=$buyer_arr[$buyer];?> </td>
				</tr>
				<tr>
					
					<td width="200" bgcolor="yellow" > Style Name </td>
					<td width="200" align="right" ><?=$styleref;?> </td>
				</tr>
				<tr>
					<td >System Job No</td>
					<td align="right" > <?=$jobNo;?></td>
				</tr>
				<tr>
					
					<td width="200">Style Qty.</td>
					<td width="200" align="right"><?=number_format($orderQuantity,0);;?></td>
				</tr>
				<tr>
					
					<td width="200"><p>UOM</p></td>
					<td width="200" align="right"><?=$unit_of_measurement[$order_uom];?></td>
				</tr>
				<tr>
					
					<td width="200"><p>Qty (Pcs)</p></td>
					<td width="200" align="right"><?=number_format($order_qty,0);;?></td>
				</tr>
				<tr>
					
					<td width="200"><p>FOB</p></td>
					<td width="200" align="right">$<?=number_format($unit_price,4);;?></td>
				</tr>
				<tr>
					
					<td width="200"><p>FOB Value</p></td>
					<td width="200" align="right">$<?=number_format($total_fab_value,4);;?> </td>
				</tr>
				<tr>
					
					<td width="200"><p>SAM</p></td>
					<td width="200" align="right"><?=number_format($set_smv,4);;?></td>
				</tr>
				<tr>
					
					<td width="200"><p>Budget Cost</p></td>
					<td width="200" align="right">$<?=number_format($tot_summ_fob_pcs,4);;?></td>
				</tr>
				<tr>
					
					<td width="200"><p>CM Cost</p></td>
					<td width="200" align="right">$<?=$cm_cost_pcs;;?></td>
				</tr>
				<tr>
					
					<td width="200"><p>Total CM Value</p></td>
					<td width="200" align="right">$<?=number_format($order_qty*$cm_cost_dzn,2);;?></td>
				</tr>
				<tr>
					
					<td width="200"><p>Margin Cost</p></td>
					<td width="200" align="right">$<?=number_format($margin_dzn,4);;?></td>
				</tr>
				<tr>
					
					<td width="200" bgcolor="#ff9900"><p>Total Fabric Budget Value</p></td>
					<td width="200" bgcolor="#ff9900" align="right">$<?=number_format($fab_req_amount,2);;?></td>
				</tr>
				<tr>
					
					<td width="200" bgcolor="#ff9900"><p>Total Accessories Budget Value</p></td>
					<td width="200" bgcolor="#ff9900" align="right">$<?=number_format($trim_req_amount,2);;?></td>
				</tr>
				<tr>
					
					<td width="200" bgcolor="#ff9900"><p>Total Wash Budget Value</p></td>
					<td width="200" bgcolor="#ff9900" align="right">$<?=number_format($wash_req_amount,2);;?></td>
				</tr>
				<tr>
					<td width="200" bgcolor="#ff9900" ><p>Total Emblishment Budget Value</p></td>
					<td width="200" bgcolor="#ff9900"  align="right">$<?=number_format($emb_req_amount,2);;?></td>
					<td width="100" align="center"><b>Variance</b></td>
					<td width="100" align="center"><b>Variance %</b></td>
				</tr>
				<tr>
					
					<td width="200" bgcolor="#6699ff"><p>Total Fabric B2B Value</p></td>
					<td width="200" bgcolor="#6699ff" align="right">$<?=number_format($fabric_b2b_value,2);;?></td>
					<td width="100" align="right" title="Total Fabric Budget Value-Total Fabric B2B Value">$<?=number_format($fab_req_amount-$fabric_b2b_value,2);;?></td>
					<td width="100" align="right" title="(Variance/Fabric Budget Value)*100"><?=number_format((($fab_req_amount-$fabric_b2b_value)/$fab_req_amount)*100,2);;?> %</td>

				</tr>
				<tr>
					<td width="200" bgcolor="#6699ff"><p>Total Accessories B2B Value</p></td>
					<td width="200" bgcolor="#6699ff" align="right">$<?=number_format($accessories_b2b_value,2);;?></td>
					<td width="100" align="right" title="Total Trims Budget Value-Total Trims B2B Value">$<?=number_format($trim_req_amount-$accessories_b2b_value,2);;?></td>
					<td width="100" align="right" title="(Variance/Trims Budget Value)*100"><?=number_format((($trim_req_amount-$accessories_b2b_value)/$trim_req_amount)*100,2);;?> %</td>
				</tr>
				<tr>
					<td width="200" bgcolor="#6699ff"><p>Total Wash B2B Value</p></td>
					<td width="200" bgcolor="#6699ff" align="right">$<?=number_format($wash_b2b_value,2);;?> </td>
					<td width="100" align="right" title="Total Wash Budget Value-Total Wash B2B Value">$<?=number_format($wash_req_amount-$wash_b2b_value,2);;?></td>
					<td width="100" align="right" title="(Variance/Wash Budget Value)*100"><?=number_format((($wash_req_amount-$wash_b2b_value)/$wash_req_amount)*100,2);;?> %</td>
				</tr>
				<tr>
					<td width="200" bgcolor="#6699ff"><p>Total Emblishment B2B Value</p></td>
					<td width="200" bgcolor="#6699ff" align="right">$<?=number_format($emblishment_b2b_value,2);;?> </td>
					<td width="100" align="right" title="Total Emblishment Budget Value-Total Emblishment B2B Value">$<?=number_format($emb_req_amount-$emblishment_b2b_value,2);;?></td>
					<td width="100" align="right" title="(Variance/Emblishment Budget Value)*100"><?=number_format((($emb_req_amount-$emblishment_b2b_value)/$emb_req_amount)*100,2);;?> %</td>
				</tr>
				<tr>
					<td width="200" bgcolor="#6699ff"><p>Total B2B Value</p></td>
					<td width="200" bgcolor="#6699ff" align="right">$<?=number_format($total_b2b_value,2);;?></td>
				</tr>
				<tr>
					<td width="200" bgcolor="#6699ff"><p>Total B2B Value %</p></td>
					<td width="200" bgcolor="#6699ff" align="right"><?=number_format(($total_b2b_value/$total_fab_value)*100,2);;?> % </td>
				</tr>
                </thead>
            </table>
		
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


if($action=="job_style_popup")
{
	echo load_html_head_contents("Style Info", "../../../", 1, 1,'','','');
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
        <div align="center"><? echo create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "-- Searching Type --" ); ?></div>
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


disconnect($con);
?>