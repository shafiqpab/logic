<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
// require_once('../../../includes/class4/class.fabrics.php');
// require_once('../../../includes/class4/class.trims.php');
// require_once('../../../includes/class4/class.others.php');
include('../../../includes/class4/class.washes.php');
include('../../../includes/class4/class.emblishments.php');
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
    <div><? echo create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "-- Searching Type --" ); ?></div>
 
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
     <div><? echo create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "-- Searching Type --" ); ?></div>
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

	$company_lib=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	$buyer_short_name_lib=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );	
	$country_name_lib=return_library_array( "select id, country_name from lib_country", "id", "country_name"  );
	$lib_supplier_arr=return_library_array( "select id, short_name from lib_supplier", "id", "short_name"  );
	$lib_season_arr=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name"  );
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");	
	$team_leader_name=return_library_array( "select id,team_name from lib_marketing_team where project_type=2 and status_active =1 and is_deleted=0 order by team_name", "id", "team_name");

	
	
	$company_name=str_replace("'","",$cbo_company_name);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$embType=str_replace("'","",$cbo_emb_type);
	$cbo_date_type=str_replace("'","",$cbo_date_type);
	$cbo_order_type=str_replace("'","",$cbo_order_type);
	$cbo_wo_status=str_replace("'","",$cbo_wo_status);
	$type=str_replace("'","",$cbo_wo_status);
	
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


	$txt_job_id=str_replace("'","",$txt_job_id);
	if($txt_job_id!="" || $txt_job_id!=0){ $jobcond="and a.id in($txt_job_id)";$jobcond2="and e.job_id in($txt_job_id)";}else{ $jobcond="";$jobcond2="";}
	if($cbo_order_type!="" || $cbo_order_type!=0) $order_typecond="and d.is_confirmed=$cbo_order_type"; else $order_typecond="";

	// if($cbo_wo_status!="" || $cbo_wo_status!=0){
	// 	if($cbo_wo_status==1){
	// 			$approve_cond="and c.approved=3";
	// 			$approve_cond2="and g.approved=3";
	// 		}
	// 		else if($cbo_wo_status==2){
	// 			$approve_cond="and c.approved=1";
	// 			$approve_cond2="and g.approved=1";
	// 		}
	// 		else if($cbo_wo_status==3){
	// 			$approve_cond="and c.approved=2";
	// 			$approve_cond2="and g.approved=2";
	// 		} 
	// 		else {
	// 			$approve_cond="";
	// 			$approve_cond2="";
	// 		}
	// 	}


	if($embType!="" || $embType!=0) $emb_typecond="and b.emb_name=$embType"; else $emb_typecond="";
	if(str_replace("'","",$txt_style_ref)!="") $style_ref_cond=" and a.style_ref_no like '%".str_replace("'","",$txt_style_ref)."%'"; else $style_ref_cond="";
	if(str_replace("'","",$txt_style_ref)!="") $style_ref_cond2=" and c.style_ref_no like '%".str_replace("'","",$txt_style_ref)."%'"; else $style_ref_cond2="";

	if ($txt_job_no=="") $job_num_mst=""; else $job_num_mst=" and c.job_no_prefix_num=$txt_job_no";
	if ($txt_job_no=="") $job_num_mst2=""; else $job_num_mst2=" and a.job_no_prefix_num=$txt_job_no";
	if ($txt_job_no=="") $job_num_mst3=""; else $job_num_mst3=" and a.job_no=$txt_job_no";


	

	$date_type=str_replace("'","",$cbo_date_type);

	$date_cond='';
	
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		$start_date=(str_replace("'","",$txt_date_from));
		$end_date=(str_replace("'","",$txt_date_to));

		if($cbo_date_type==1){
			$date_cond="and to_date(to_char(b.shipment_date, 'DD-MON-YYYY')) BETWEEN '$start_date' AND '$end_date'";
			$date_cond2="and to_date(to_char(e.shipment_date, 'DD-MON-YYYY')) BETWEEN '$start_date' AND '$end_date'";
		}else if($cbo_date_type==2){
			$date_cond="and to_date(to_char(b.pub_shipment_date, 'DD-MON-YYYY')) BETWEEN '$start_date' AND '$end_date'";
			$date_cond2="and to_date(to_char(e.pub_shipment_date, 'DD-MON-YYYY')) BETWEEN '$start_date' AND '$end_date'";
		}else if($cbo_date_type==3){
			$date_cond="and to_date(to_char(d.country_ship_date, 'DD-MON-YYYY')) BETWEEN '$start_date' AND '$end_date'";
			$date_cond2="and to_date(to_char(f.country_ship_date, 'DD-MON-YYYY')) BETWEEN '$start_date' AND '$end_date'";
		}else if($cbo_date_type==4){
			$date_cond="and to_date(to_char(b.pack_handover_date, 'DD-MON-YYYY')) BETWEEN '$start_date' AND '$end_date'";
			$date_cond2="and to_date(to_char(e.pack_handover_date, 'DD-MON-YYYY')) BETWEEN '$start_date' AND '$end_date'";
		}else{
			$date_cond="";	$date_cond2="";
		}
	}

	if($type==2) $approved_cond=" and g.approved=1";
	elseif($type==1) $approved_cond=" and g.approved=3";
	else $approved_cond=" and g.approved in (0,2)";

	 
	$cbo_team_leader=str_replace("'","",$cbo_team_leader);
	if ($cbo_team_leader >0) {
		$team_leader_cond = " and a.team_leader=$cbo_team_leader";
	}else{
		$team_leader_cond ="";
	}

	$cbo_year=str_replace("'","",$cbo_year);
	
	if(trim($cbo_year)!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year"; else $year_cond="";
	if(trim($cbo_year)!=0) $year_cond2=" and to_char(c.insert_date,'YYYY')=$cbo_year"; else $year_cond2="";

		
			$approved=array(0=>"No",1=>"Yes",2=>"No",3=>"Yes");
			$supplier_arr=return_library_array( "select a.id, a.supplier_name from lib_supplier a,lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(23) and a.is_deleted=0 and a.status_active=1 group by a.id, a.supplier_name order by a.supplier_name", "id", "supplier_name");
			$brand_name_arr=return_library_array( "select id, brand_name from  lib_buyer_brand where status_active=1 and is_deleted=0",'id','brand_name');
		

			$booking_sql ="SELECT a.id as booking_dtls_id, d.id as booking_id,d.booking_no,d.supplier_id,d.pay_mode,b.id,b.job_no,b.emb_name,b.emb_type,b.rate,b.sourcing_rate,b.sourcing_amount,b.sourcing_nominated_supp,a.amount,a.wo_qnty,e.job_id,g.approved 
			FROM wo_booking_mst d, wo_booking_dtls a left join wo_po_break_down e on a. po_break_down_id=e.id left join wo_po_color_size_breakdown f on e.id=f.po_break_down_id, wo_pre_cost_embe_cost_dtls b,wo_po_details_master c,wo_pre_cost_mst g

			WHERE b.id=a.pre_cost_fabric_cost_dtls_id and d.booking_no=a.booking_no and c.job_no = e.job_no_mst and e.job_no_mst=g.job_no and  a.booking_type=6 and a.status_active=1 and  g.status_active=1 and c.garments_nature = 3 and c.status_active=1
			and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.entry_form=201  $emb_typecond $date_cond2 $jobcond2 $year_cond2 $style_ref_cond2 $job_num_mst $approve_cond2
			group by a.id , d.id ,d.booking_no,d.supplier_id,d.pay_mode,b.id,b.job_no,b.emb_name,b.emb_type,
			b.rate,b.sourcing_rate,b.sourcing_amount,b.sourcing_nominated_supp,a.amount,a.wo_qnty,e.job_id,g.approved";
			//echo $booking_sql;

			$booking_data = sql_select($booking_sql);
			$w=1;
			foreach ($booking_data as $row) {
				if ($row[csf("pay_mode")] == 3 || $row[csf("pay_mode")] == 5) {
						if($w==1){
						$wash_comp.=$company_lib[$row[csf("supplier_id")]];
						$w++;
						}else{
							$wash_comp.=",".$company_lib[$row[csf("supplier_id")]];
						}
					
				}else {
					
					if($w==1){
						$wash_comp.=$lib_supplier_arr[$row[csf("supplier_id")]];
						$w++;
						}else{
							$wash_comp.=",".$lib_supplier_arr[$row[csf("supplier_id")]];
						}

				}
				$job_wise_data_arr[$row[csf('job_no')]]['booking_wash_company']=$wash_comp;
				$job_wise_data_arr[$row[csf('job_no')]]['rate']=$row[csf('rate')];
				$jobIdArr[$row[csf('job_id')]]=$row[csf('job_id')];
			}
			$jobCond="";
			// if(count($jobIdArr)>0){
			// 	$jobIds=implode(",",$jobIdArr);
			// 	$jobCond="and a.id in ($jobIds)";
			// }
			

			$wo_sql ="SELECT a.id as booking_dtls_id,b.id,b.job_no,b.emb_name,b.emb_type,b.rate,b.sourcing_rate,b.sourcing_amount,b.sourcing_nominated_supp,a.amount,a.wo_qnty,e.job_id,g.approved   
			FROM  wo_booking_dtls a left join wo_po_break_down e on a. po_break_down_id=e.id left join wo_po_color_size_breakdown f on e.id=f.po_break_down_id, wo_pre_cost_embe_cost_dtls b,wo_po_details_master c,wo_pre_cost_mst g
			WHERE b.id=a.pre_cost_fabric_cost_dtls_id and c.job_no = e.job_no_mst and e.job_no_mst=g.job_no and  a.booking_type=6 and a.status_active=1 and  g.status_active=1 and c.garments_nature = 3 and c.status_active=1 and c.company_name in ($company_name)
			and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $emb_typecond $date_cond2 $jobcond2 $year_cond2 $style_ref_cond2 $job_num_mst $approve_cond2
			group by a.id , b.id,b.job_no,b.emb_name,b.emb_type,
			b.rate,b.sourcing_rate,b.sourcing_amount,b.sourcing_nominated_supp,a.amount,a.wo_qnty,e.job_id,g.approved";
	//echo $wo_sql;
			$wo_data = sql_select($wo_sql);
			foreach ($wo_data as $row) {
	
				$job_wise_data_arr[$row[csf('job_no')]]['wo_qty']+=$row[csf('wo_qnty')];
			}


			$sourcing_data_sql=sql_select("select c.insert_date, a.style_ref_no, c.sourcing_approved, c.approved,a.job_no,c.sourcing_date,c.costing_date from wo_po_details_master a, wo_po_break_down b ,wo_pre_cost_mst c where a.garments_nature=3 and a.job_no=b.job_no_mst and b.job_no_mst=c.job_no  and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and b.status_active=1 and a.company_name in ($company_name)  $year_cond order by a.id DESC");
		
		

			foreach ($sourcing_data_sql as $row) {
				$sourcing_cost_arr[$row[csf('job_no')]]['insert_date'] =change_date_format($row[csf('sourcing_date')],'','');
				$sourcing_cost_arr[$row[csf('job_no')]]['approved'] =$approved[$row[csf('sourcing_approved')]];
				$pre_cost_arr[$row[csf('job_no')]]['insert_date'] =change_date_format($row[csf('costing_date')],'','');
				$pre_cost_arr[$row[csf('job_no')]]['approved'] =$row[csf('approved')];
			}

			// 	echo "<pre>";
			// print_r($sourcing_cost_arr);

			$sql="SELECT c.insert_date, a.style_ref_no,a.job_no_prefix_num,a.brand_id, a.order_uom,a.gmts_item_id,a.job_no,to_char(a.insert_date,'YYYY') as year, a.company_name, a.buyer_name, a.currency_id,a.style_description,a.team_leader,a.season_buyer_wise,a.body_wash_color, c.sourcing_approved,c.approved,a.job_no,c.sourcing_date,c.costing_date, c.exchange_rate , b.id as po_id, b.po_number, b.po_quantity,b.pack_handover_date,b.shipment_date ,b.plan_cut 
			FROM wo_po_details_master a,
				 wo_po_break_down b  
			left join wo_po_color_size_breakdown d on b.id=d.po_break_down_id,
				 wo_pre_cost_mst c 
			WHERE a.garments_nature=3 and 
					a.job_no=b.job_no_mst and 
					b.job_no_mst=c.job_no and 
					c.status_active=1 and 
					c.is_deleted=0 and 
					a.is_deleted=0 and 
					b.is_deleted=0 and 
					a.status_active=1 and 
					b.status_active=1 and 
					d.status_active=1 and 
					d.is_deleted=0 and
					a.company_name in ($company_name) 
					$jobCond
			 $style_ref_cond $buyer_id_cond $date_cond $year_cond $jobcond $order_typecond $team_leader_cond $job_num_mst2 $approve_cond
			 GROUP BY c.insert_date, a.style_ref_no,a.job_no_prefix_num,a.brand_id, a.order_uom,a.gmts_item_id,a.job_no,a.insert_date, a.company_name, a.buyer_name, a.currency_id,a.style_description,a.team_leader,a.season_buyer_wise,a.body_wash_color, c.sourcing_approved,c.approved,a.job_no,c.sourcing_date,c.costing_date, c.exchange_rate , b.id , b.po_number, b.po_quantity,b.pack_handover_date,b.shipment_date ,b.plan_cut";
			// echo $sql;
			$data_array=sql_select($sql);
			



			foreach($data_array as $row){

				$job_wise_data_arr[$row[csf('job_no')]]['plan_cut']+=$row[csf('plan_cut')];
				$job_wise_data_arr[$row[csf('job_no')]]['po_quantity']+=$row[csf('po_quantity')];
				$job_wise_data_arr[$row[csf('job_no')]]['po_number']=$row[csf('po_number')];
				$job_wise_data_arr[$row[csf('job_no')]]['style_ref_no']=$row[csf('style_ref_no')];
				$job_wise_data_arr[$row[csf('job_no')]]['gmts_item']=$row[csf('gmts_item_id')];
				$job_wise_data_arr[$row[csf('job_no')]]['company']=$row[csf('company_name')];
				$job_wise_data_arr[$row[csf('job_no')]]['style_description']=$row[csf('style_description')];
				$job_wise_data_arr[$row[csf('job_no')]]['buyer_name']=$row[csf('buyer_name')];
				$job_wise_data_arr[$row[csf('job_no')]]['brand_id']=$row[csf('brand_id')];
				$job_wise_data_arr[$row[csf('job_no')]]['team_leader']=$row[csf('team_leader')];
				$job_wise_data_arr[$row[csf('job_no')]]['season_buyer_wise']=$row[csf('season_buyer_wise')];
				$job_wise_data_arr[$row[csf('job_no')]]['body_wash_color']=$row[csf('body_wash_color')];
				$job_wise_data_arr[$row[csf('job_no')]]['order_uom']=$row[csf('order_uom')];
				$job_wise_data_arr[$row[csf('job_no')]]['php_date']=$row[csf('pack_handover_date')];
				$job_wise_data_arr[$row[csf('job_no')]]['ship_date']=$row[csf('shipment_date')];
			
				// $job_wise_data_arr[$row[csf('job_no')]]['wo_qty']+=$row[csf('cu_woq')];
				// $job_wise_data_arr[$row[csf('job_no')]]['amount']+=$row[csf('amount')];
				$job_wise_data_arr[$row[csf('job_no')]]['exchange_rate']=$row[csf('exchange_rate')];
				$job_wise_data_arr[$row[csf('job_no')]]['year']=$row[csf('year')];
				$job_wise_po_id[$row[csf('job_no')]][$row[csf('po_id')]]=$row[csf('po_id')];
				$job_arr[$row[csf('job_no')]]=$row[csf('job_no')];


			}
			
			
			
			


				$wash_data=sql_select("SELECT id, job_no, emb_name, emb_type, country,nominated_supp_multi, cons_dzn_gmts, rate, amount, status_active, budget_on,sourcing_nominated_supp,sourcing_rate,sourcing_amount,is_synchronized	
				from wo_pre_cost_embe_cost_dtls 
				where emb_name=$embType ".where_con_using_array($job_arr,1,'job_no')."  order by id");

			$j=1;
				foreach($job_arr as $val){

					if($j==1){
						$jobNo .="'".$val."'";
						$j++;
					}else{
						$jobNo .=",'".$val."'";
					}
					
				};
			//	echo $jobNo;
				$emblishment_wash_qtyArr=array();
				$condition= new condition();
				$condition->job_no("in ($jobNo)");
				$condition->init();
				$wash= new wash($condition);
				$emblishment= new emblishment($condition);
				// $wash_qtyArr=$wash->getQtyArray_by_jobAndEmblishmentid();
				if($embType==3){
					$emblishment_wash_qtyArr=$wash->getQtyArray_by_jobAndEmblishmentid();
				}else{
					$emblishment_wash_qtyArr=$emblishment->getQtyArray_by_jobAndEmblishmentid();
				}
				//  echo "<pre>";
				//  print_r($emblishment_wash_qtyArr);
			
			$e=1;
				foreach($wash_data as $row){

						$nominated_supp_str="";
						$exnominated_supp=explode(",",$row[csf("sourcing_nominated_supp")]);
						foreach($exnominated_supp as $supp)
						{
							if($nominated_supp_str=="") $nominated_supp_str .=$lib_supplier_arr[$supp]; else $nominated_supp_str.=','.$lib_supplier_arr[$supp];
						}

						if($row[csf('emb_name')]==1)
						{
							$emb_type_name=$emblishment_print_type[$row[csf('emb_type')]];
						}
						if($row[csf('emb_name')]==2)
						{
							$emb_type_name=$emblishment_embroy_type[$row[csf('emb_type')]];
						}
						if($row[csf('emb_name')]==3)
						{
							$emb_type_name=$emblishment_wash_type[$row[csf('emb_type')]];
						}
						if($row[csf('emb_name')]==4)
						{
							$emb_type_name=$emblishment_spwork_type[$row[csf('emb_type')]];
						}
						if($row[csf('emb_name')]==5)
						{
							$emb_type_name=$emblishment_gmts_type[$row[csf('emb_type')]];
						}
						$job_wise_data_arr[$row[csf('job_no')]]['emb_req_qty']+=$emblishment_wash_qtyArr[$row[csf('job_no')]][$row[csf('id')]];
						if($e==1){
							
							$job_wise_data_arr[$row[csf('job_no')]]['emb_type'].=$emb_type_name;
							
							 
							$e++;
						}else{
							$job_wise_data_arr[$row[csf('job_no')]]['emb_type'].=",".$emb_type_name;
							 
						}
						$job_wise_data_arr[$row[csf('job_no')]]['wash_company'].=$nominated_supp_str.",";
					
				}
				// $main_data_arr=array();
				// foreach ($job_wise_data_arr as $key => $value){
				// 	if(($value['emb_req_qty'] == $value['wo_qty']) && 0 < $value['emb_req_qty']){
				// 		$main_data_arr[3]=$job_wise_data_arr;
				// 	 echo $value['emb_req_qty']."==".$value['emb_req_qty']."==3<br>";
				// 	}
				// 	if($value['emb_req_qty'] > $value['wo_qty'] && 0 < $value['emb_req_qty']){
					
				// 		$main_data_arr[2]=$job_wise_data_arr;
				// 		echo $value['emb_req_qty']."==".$value['emb_req_qty']."==2<br>";
				// 	}
				// 	if($value['wo_qty']<=0){
						
				// 		$main_data_arr[1]=$job_wise_data_arr;
				// 		echo $value['emb_req_qty']."==".$value['emb_req_qty']."==1<br>";
				// 	}
					
					
				// }
				
	// echo "<pre>";
	// print_r($main_data_arr);
	ob_start();
	?>
	<br>
	<div style="width:2600px">


    <fieldset style="width:2540px; float:left;">
            <legend>Report Details Part</legend>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2540" class="rpt_table" align="left">
                <thead>
				<tr>
					<th width="30">SL</th>
					<th width="100">LC Company</th>
					<th width="100">Wash.Company</th>
					<th width="100">Team Name</th>
					<th width="100">Buyer/Brand</th>
					<th width="100">Job No</th>
					<th width="100">Year</th>
					<th width="100">Season</th>
					<th width="100">Item</th>
					<th width="110">Style Ref</th>
					<th width="100">Order No</th>
					<th width="100">Style Description</th>
					<th width="100">Body/Wash Color</th>
					<th width="100">Wash Type</th>
					<th width="100">Order Qty</th>
					<th width="100">Uom</th>
					<th width="100">Budget. Req. Qty.</th>
					<th width="100">Wash Rate(pcs)</th>
					<th width="100">Total Wash Value[USD]</th>
					<th width="100">Wo Qty[pcs]</th>
					<th width="100">Wo Balance</th>
					<th width="100">Party Wash Company </th>
					<th width="100">PHD. Date</th>
					<th width="100">Ship Date</th>
					<th width="100">Approval Status </th>
					<th width="100">Shipping Status</th>

				</tr>
                </thead>
            </table>
			<div style="max-height:425px; overflow-y:scroll; width:2560px;" id="scroll_body">
                <table border="1" class="rpt_table" width="2540" rules="all" id="table_body" align="left">
                    <tbody>
					<? $sl=1;  
				$tmi=0;
				$i=0;
				$typeStatus=0;
				foreach ($job_wise_data_arr as $job_id => $row) {
					
						// $Budget_req_qty=$row['emb_req_qty']*12;
						$Budget_req_qty=number_format($row['emb_req_qty']*12, 4,'.','');
						$wo_qnty_pcs=number_format($row['wo_qty']*12, 4,'.','');
						$budget_qty_balance=$Budget_req_qty-$wo_qnty_pcs;
				 
						if($wo_qnty_pcs <= 0 ){
							$typeStatus=1;
							$status="Pending";
						}
						if($wo_qnty_pcs > 0 &&  0 < $budget_qty_balance){
							$typeStatus=2;
							$status="Partial";
						}
						if($wo_qnty_pcs >0 && $budget_qty_balance <= 0){
							$typeStatus=3;
							$status="Full";
						}

						$job_emb_wise_data[$typeStatus][$job_id]=$row;
						$job_emb_wise_data[$typeStatus][$job_id]['shipping_status']=$status;

				}
			
					// echo "<pre>";
					// print_r($job_emb_wise_data);

		
					foreach ($job_emb_wise_data[$cbo_wo_status] as $job_id => $row) {
								
					
					$job_no=$row['job_no'];
					$company=$row['company'];
					$mst_id=return_field_value("id", "wo_pre_cost_mst", "job_no='$job_no'  and status_active=1 and is_deleted=0");
					$emble_budget_id=return_field_value("embellishment_budget_id", "variable_order_tracking", "company_name='$company'  and embellishment_id=3 and variable_list=56 and status_active=1 and is_deleted=0");
					
					$bgcolor = ($sl%2==0) ? "#ffffff" : "#f6faff";
					  $wo_rate_pcs=$row['rate']/12;
					  $wash_value_pcs=($row['plan_cut']/12)*$wo_rate_pcs;

					  	if($emble_budget_id==1){
							$req_qnty=$row['po_quantity']/12;
						  }else{
							$req_qnty=$row['plan_cut']/12;
						  }
					//   

					$Budget_req_qty=$row['emb_req_qty']*12;
					$wo_qnty_pcs=$row['wo_qty']*12;
					$budget_qty_balance=$Budget_req_qty-$wo_qnty_pcs;
					?>

				<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
					<td width="30"><?= $sl; ?></td>
					<td width="100"><?= $company_lib[$row['company']]; ?></td>
					<td width="100"><div style="word-wrap:break-word; width:100px"><?= implode(",",array_unique(explode(",",rtrim($row['wash_company'],",")))) ?></div></td>
					<td width="100"><?= $team_leader_name[$row['team_leader']]?></td>
					<td width="100"><div style="word-wrap:break-word; width:100px"><?
					
						  if($buyer_short_name_lib[$row['buyer_name']] !="" && $brand_name_arr[$row['brand_id']] !=""){
							echo $buyer_short_name_lib[$row['buyer_name']]."/".$brand_name_arr[$row['brand_id']];
						  }elseif($buyer_short_name_lib[$row['buyer_name']] !=""){
							echo $buyer_short_name_lib[$row['buyer_name']];
						  }elseif($brand_name_arr[$row['brand_id']] !=""){ echo $brand_name_arr[$row['brand_id']]; }
					
					 ?></div></td>
					<td width="100"><?= $job_id ?></td>
					<td width="100"><?= $row['year'] ?></td>
					<td width="100"><?= $lib_season_arr[$row['season_buyer_wise']] ?></td>				
					<td width="100"><?= $garments_item[$row['gmts_item']] ?></td>					
					<td width="110"><div style="word-wrap:break-word; width:100px"><?= $row['style_ref_no'] ?></div></td>
					<td width="100"><a href='#report_details' onClick="openmypage_order_details('<? echo $job_id; ?>','<? echo implode(',',$job_wise_po_id[$job_id]); ?>','<? echo $embType; ?>','order_details');">view</a></td>
					<td width="100" style="word-wrap:break-word; width:100px"><?=$row['style_description'] ?></td>
					<td width="100"><?=$color_arr[$row['body_wash_color']]; ?></td>
					<td width="100"><div style="word-wrap:break-word; width:100px"><?= $row['emb_type']; ?></div></td>
					<td width="100"><?=number_format($row['po_quantity'], 4,'.','')	 ?>	</td>
					<td width="100"><?= $unit_of_measurement[$row['order_uom']]  ?></td>
					<td width="100"><?=number_format($Budget_req_qty, 4,'.','');  ?></td>
					<td width="100"><?= number_format($wo_rate_pcs, 4,'.',''); ?></td>
					<td width="100" title="plan_cut=<?=$row['plan_cut']?>exchange=<?=$row['exchange_rate'];?>"><? echo number_format($Budget_req_qty*$wo_rate_pcs, 4,'.','') // echo number_format($wash_value_pcs/$row['exchange_rate'], 4,'.','') ; ?></td>				
					<td width="100"><a href='#report_details' onClick="openmypage_order_details('<? echo $job_id; ?>','<? echo implode(',',$job_wise_po_id[$job_id]); ?>','<? echo $embType; ?>','wo_details');"><?= number_format($wo_qnty_pcs, 4,'.','');   ?></a></td>
					<td width="100"><?=	number_format($budget_qty_balance, 4,'.','');?></td>
					<td width="100"><div style="word-wrap:break-word; width:100px"><?= implode(",",array_unique(explode(",",$row['booking_wash_company']))); ?></div></td>
					<td width="100"><?= change_date_format($row['php_date'],'',''); ?></td>
					<td width="100"><?= change_date_format($row['ship_date'],'',''); ?></td>
					<td width="100"  align="center"><?= $sourcing_cost_arr[$job_id]['approved']; ?></td>
					<td width="100" align="center" title="req_qnty=<?=$req_qnty;?>"><?	$status=$req_qnty-$row['wo_qty'];
								//if($status==0){ echo "Full";}elseif($row['wo_qty']>0){echo "Partial";}else{ echo "Pending";}	
								echo $row['shipping_status'];
						?>
					</td>
				
					</tr>
				
				<?
						$sl++;
				
					
				  
				}
				?>
					</tbody>
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
if($action=="order_details")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<!--<div style="width:880px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
	<fieldset style="width:870px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="left">
				<caption align="center">Order Details</caption>
				<thead>
                    <th width="30">Sl</th>
                    <th width="50">Order No</th>
                    <th width="90">PO Received Date</th>
                    <th width="100">Shipment Date</th>
                    <th width="80">Pub. Ship Date</th>
                    <th width="150">PHD Date</th>
                    <th width="80">Country Ship Date</th>
                    <th width="80">Order QTY</th>                  
				</thead>
                <tbody>
                <?
				
					$country_name_lib=return_library_array( "select id, country_name from lib_country", "id", "country_name"  );
					


					$po_data=sql_select("select a.job_no,b.id as po_id, b.po_number,b.shipment_date,b.pub_shipment_date,b.po_received_date,b.pack_handover_date,b.country_name,b.po_quantity,c.country_ship_date from wo_po_details_master a, wo_po_break_down  b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and c.po_break_down_id=b.id and a.job_no='$job_id' and b.id in ($po_id) and a.status_active=1 and a.is_deleted=0 group by a.job_no,b.id , b.po_number,b.shipment_date,b.pub_shipment_date,b.po_received_date,b.pack_handover_date,b.country_name,b.po_quantity,c.country_ship_date");
					

		
					


					$i=1;
					foreach($po_data as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";

						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="50"><p><? echo $row[csf('po_number')]; ?></p></td>
                            <td width="90" align="center"><p><? echo change_date_format($row[csf('po_received_date')]);; ?></p></td>
                            <td width="100" align="center"><p><? echo change_date_format($row[csf('shipment_date')]);;; ?></p></td>
                            <td width="80" align="center"><p><? echo  change_date_format($row[csf('pub_shipment_date')]); ?></p></td>
                            <td width="150" align="center"><p><? echo change_date_format($row[csf('pack_handover_date')]); ?></p></td>
                            <td width="80" align="right"><p><? echo change_date_format($row[csf('country_ship_date')]); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row[csf('po_quantity')],2); ?></p></td>
                            
                        </tr>
						<?
					
						$i++;
					}
				?>
                </tbody>
                <!-- <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="7" align="right">Total</td>                                  
                        <td><? echo number_format($tot_rej_qty,2); ?></td>
                    </tr>
                </tfoot> -->
            </table>

            
        </div>
    </fieldset>
    <?
	exit();
}

if($action=="wo_details")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<!--<div style="width:880px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
	<fieldset style="width:870px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="left">
				<caption align="center">Wo Summary</caption>
				<thead>
                    <th width="30">Sl</th>
                    <th width="50">	Booking Type </th>
                    <th width="90">Wo NO</th>
                    <th width="100">Booking Date</th>
                    <th width="80">Wo Qty[pcs]</th>
                    <th width="150">Wash.Company</th>
                    <th width="80">Wo Insert Date</th>
                    <th width="80">Insert User Name</th>                  
				</thead>
                <tbody>
                <?
				
					$country_name_lib=return_library_array( "select id, country_name from lib_country", "id", "country_name"  );
					$company_library=return_library_array("select id, company_name from lib_company", "id", "company_name");
					$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
					$user_lib_name=return_library_array("SELECT id,user_name from user_passwd", "id", "user_name");
					
					$booking_data=sql_select("select a.id as booking_dtls_id, d.id as booking_id,d.booking_no,d.supplier_id,d.pay_mode,d.booking_date,d.insert_date,d.inserted_by from wo_booking_mst d,wo_booking_dtls a where  d.booking_no=a.booking_no and a.booking_type=6 and a.status_active=1 and a.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 	and a.job_no='$job_id' and a.po_break_down_id in ($po_id) order by d.id ");

					foreach($booking_data as $row)
					{
						$booking=$row[csf('booking_no')];
						$supplier_id=$row[csf('supplier_id')];
						$pay_mode=$row[csf('pay_mode')];
						$booking_date=$row[csf('booking_date')];
						$insert_date=$row[csf('insert_date')];
						$inserted_by=$row[csf('inserted_by')];
					}

					$wo_data=sql_select("select a.id as booking_dtls_id,b.id,b.job_no,b.emb_name,b.emb_type, b.rate,b.sourcing_rate
					,b.sourcing_amount,b.sourcing_nominated_supp,a.amount,a.wo_qnty from wo_booking_dtls a,wo_pre_cost_embe_cost_dtls b 
					where b.id=a.pre_cost_fabric_cost_dtls_id and a.booking_type=6 and a.status_active=1 and a.is_deleted=0
					 and b.status_active=1 and b.is_deleted=0 and b.job_no='$job_id' and b.emb_name=$emb_type and a.po_break_down_id in ($po_id) order by a.id ");
					 
					$i=1;
					foreach($wo_data as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";

							if($row[csf('is_short')]==1 ){
								$type="Short";
							}else {
								$type="Main";
							}



						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="50"><p><? echo $type; ?></p></td>
							<td width="90" align="center"><p><? echo $booking; ?></p></td>
                            <td width="100" align="center"><p><? echo change_date_format($booking_date);; ?></p></td>                           
                            <td width="80" align="center"><p><? echo number_format($row[csf('wo_qnty')]*12,2); ; ?></p></td>
                            <td width="150" align="center"><p><?
							 if($pay_mode==5 || $pay_mode==3){
								echo $company_library[$supplier_id];
								}
								else{
								echo $supplier_name_arr[$supplier_id];
								}
							 ?></p></td>
                            <td width="80" align="right"><p><? echo change_date_format($insert_date);; ?></p></td>
                            <td width="80" align="right"><p><? echo $user_lib_name[$inserted_by]; ?></p></td>
                            
                        </tr>
						<?
					
						$i++;
					}
				?>
                </tbody>
                <!-- <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="7" align="right">Total</td>                                  
                        <td><? echo number_format($tot_rej_qty,2); ?></td>
                    </tr>
                </tfoot> -->
            </table>

            
        </div>
    </fieldset>
    <?
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
        <div><? echo create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "-- Searching Type --" ); ?></div>
		<?
			//$arr=array(0=>$buyer_arr,1=>$season_arr);
            echo create_list_view("list_view", "Style Ref No,Job No,Year","300,60,40","500","400",0, $sql , "js_set_value", "id,job_no_prefix_num,style_ref_no", "", 1, "0,0,0", "", "style_ref_no,job_no_prefix_num,job_year", "","setFilterGrid('list_view',-1)","0","","") ;
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