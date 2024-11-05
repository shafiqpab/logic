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
		$date_cond="and a.insert_date between '$start_date' and '$end_date'";
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
	
	

	$main_data_sql=sql_select("SELECT a.id, a.job_no, a.buyer_name, a.job_no_prefix_num, a.season_buyer_wise, a.style_ref_no, a.job_quantity, b.id as po_id,c.order_quantity,a.insert_date,a.team_leader,b.po_received_date,a.company_name,a.inserted_by,b.pack_handover_date,a.set_smv,b.unit_price,b.doc_sheet_qty,a.gmts_item_id,b.sc_lc,a.order_uom,d.id as mst_id from wo_po_details_master a , wo_po_break_down b ,wo_po_color_size_breakdown c,wo_pre_cost_mst d where  a.id=b.job_id and a.id=c.job_id and b.job_no_mst=d.job_no  and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_name in ($company_name) $year_cond $style_ref_cond $jobcond $buyer_id_cond $date_cond $team_leader_cond  group by  a.id, a.job_no, a.buyer_name, a.job_no_prefix_num, a.season_buyer_wise, a.style_ref_no, a.job_quantity, b.id , c.order_quantity,a.insert_date,a.team_leader,b.po_received_date,a.company_name,a.inserted_by,b.pack_handover_date,a.set_smv,b.unit_price,b.doc_sheet_qty,a.gmts_item_id,b.sc_lc,a.order_uom,d.id order by a.style_ref_no");



	foreach ($main_data_sql as $row) {
		$style_wise_data_arr[$row[csf('style_ref_no')]]['company']=$company_library[$row[csf('company_name')]];
		$style_wise_data_arr[$row[csf('style_ref_no')]]['team_leader']=$team_leader_name[$row[csf('team_leader')]];
		// $style_wise_data_arr[$row[csf('style_ref_no')]]['team_member']=$team_member[$row[csf('team_leader')]];
		$style_wise_data_arr[$row[csf('style_ref_no')]]['team_member_id']=$row[csf('team_leader')];
		$style_wise_data_arr[$row[csf('style_ref_no')]]['job_no']=$row[csf('job_no')];
		$style_wise_data_arr[$row[csf('style_ref_no')]]['order_quantity']=$row[csf('order_quantity')];
		$style_wise_data_arr[$row[csf('style_ref_no')]]['insert_date']=change_date_format($row[csf('insert_date')],'','');
		$style_wise_data_arr[$row[csf('style_ref_no')]]['po_received_date']=change_date_format($row[csf('po_received_date')],'','');;
		$style_wise_data_arr[$row[csf('style_ref_no')]]['buyer_name']=$buyer_short_name_library[$row[csf('buyer_name')]];
		$style_wise_data_arr[$row[csf('style_ref_no')]]['inserted_by']=$user_name_arr[$row[csf('inserted_by')]];
		$style_wise_data_arr[$row[csf('style_ref_no')]]['mst_id']=$row[csf('mst_id')];
		$style_wise_data_arr[$row[csf('style_ref_no')]]['approved']=$row[csf('approved')];
		$style_wise_data_arr[$row[csf('style_ref_no')]]['phd']=change_date_format($row[csf('pack_handover_date')],'','');;
		$style_wise_data_arr[$row[csf('style_ref_no')]]['set_smv']=$row[csf('set_smv')];
		$style_wise_data_arr[$row[csf('style_ref_no')]]['unit_price']=$row[csf('unit_price')];
		$style_wise_data_arr[$row[csf('style_ref_no')]]['style_ref_no']=$row[csf('style_ref_no')];
		$style_wise_data_arr[$row[csf('style_ref_no')]]['order_qty']=$row[csf('doc_sheet_qty')];
		$style_wise_data_arr[$row[csf('style_ref_no')]]['uom']=$row[csf('order_uom')];
		$style_wise_data_arr[$row[csf('style_ref_no')]]['gmts_item_id']=$garments_item[$row[csf('gmts_item_id')]];	
		$style_wise_data_arr[$row[csf('style_ref_no')]]['sc_lc']=$row[csf('sc_lc')];	

	}
 	// echo "<pre>";
    // print_r($style_wise_data_arr);

			// $wo_data_sql=sql_select("select   a.style_ref_no, c.approved,c.insert_date from wo_po_details_master a, wo_po_break_down b left join wo_pre_cost_mst c on b.job_no_mst=c.job_no and c.status_active=1 and c.is_deleted=0 where a.garments_nature=3 and a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and a.company_name=$company_name $year_cond order by a.id DESC");

			// foreach ($wo_data_sql as $row) {
				
			// }
		
	
			$sourcing_data_sql=sql_select("select c.insert_date, a.style_ref_no, c.sourcing_approved, c.approved from wo_po_details_master a, wo_po_break_down b ,wo_pre_cost_mst c where a.garments_nature=3 and a.job_no=b.job_no_mst and b.job_no_mst=c.job_no and c.approved in(1) and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and b.status_active=1 and a.company_name in ($company_name)  $year_cond order by a.id DESC");

			foreach ($sourcing_data_sql as $row) {
				
				$sourcing_cost_arr[$row[csf('style_ref_no')]]['insert_date'] =change_date_format($row[csf('insert_date')],'','');
				$sourcing_cost_arr[$row[csf('style_ref_no')]]['approved'] =$row[csf('sourcing_approved')];

				$pre_cost_arr[$row[csf('style_ref_no')]]['insert_date'] =change_date_format($row[csf('insert_date')],'','');
				$pre_cost_arr[$row[csf('style_ref_no')]]['approved'] =$row[csf('approved')];
			


			}


			$fabric_trims_booking_data=sql_select("select  a.booking_no_prefix_num,c.style_ref_no,a.entry_form,a.booking_type from wo_booking_mst a,wo_booking_dtls b, wo_po_details_master c,wo_po_break_down d where a.booking_no=b.booking_no and b.job_no=c.job_no and b.job_no=d.job_no_mst and b.po_break_down_id=d.id and a.booking_type in (1,2,6) and a.entry_form in (271,272,201) and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.company_id in ($company_name) group by a.booking_no_prefix_num,c.style_ref_no,a.entry_form ,a.booking_type order by c.style_ref_no asc");
			$ti=0;
			$wi=0;
			$fb=0;
			foreach ($fabric_trims_booking_data as $row) {
				if($row[csf('booking_type')]==2 && $row[csf('entry_form')]==272){
					
					if($ti==0){
						$trims_cost_arr[$row[csf('style_ref_no')]]['booking_no'] .=$row[csf('booking_no_prefix_num')].",";
						$ti++;
					}else{
						$trims_cost_arr[$row[csf('style_ref_no')]]['booking_no'] .=$row[csf('booking_no_prefix_num')].",";
					}
				}else if($row[csf('booking_type')]==1 && $row[csf('entry_form')]==271){
					
					if($fb==0){
						$fabric_cost_arr[$row[csf('style_ref_no')]]['booking_no'] .=$row[csf('booking_no_prefix_num')].",";
						$fb++;
					}else{
						$fabric_cost_arr[$row[csf('style_ref_no')]]['booking_no'] .=$row[csf('booking_no_prefix_num')].",";
					}
				}else{
					if($wi==0){
						$wash_cost_arr[$row[csf('style_ref_no')]]['booking_no'] .=$row[csf('booking_no_prefix_num')].",";
						$wi++;
					}else{
						$wash_cost_arr[$row[csf('style_ref_no')]]['booking_no'] .=$row[csf('booking_no_prefix_num')].",";
					}
				}
			}

	
			$pi_number_data=sql_select("SELECT f.style_ref_no,d.pi_number,d.item_category_id from  wo_booking_dtls b 
			,com_pi_item_details c , com_pi_master_details d ,wo_po_details_master f where  b.booking_no=c.work_order_no and b.job_no = f.job_no and  d.id=c.pi_id  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.item_category_id in (3,4) and d.status_active=1 and d.is_deleted=0 group by f.style_ref_no,d.pi_number,d.item_category_id"); 
			$pi_data_arr=array();
			$fi=0;
			$ti=0;
			foreach ($pi_number_data as $row) {
			
				if($row[csf('item_category_id')]==4){
					if($ti==0){
						$trims_pi_arr[$row[csf('style_ref_no')]]['pi_no'] .=$row[csf('pi_number')].",";
						$ti++;
					}else{
						$trims_pi_arr[$row[csf('style_ref_no')]]['pi_no'] .=$row[csf('pi_number')].",";
					}
				}else{
					if($fi==0){
						$fabric_pi_arr[$row[csf('style_ref_no')]]['pi_no'] .=$row[csf('pi_number')].",";
						$fi++;
					}else{
						$fabric_pi_arr[$row[csf('style_ref_no')]]['pi_no'] .=$row[csf('pi_number')].",";
					}

				}
				
			}

			$btb_lc_number_data=sql_select("SELECT f.style_ref_no,d.pi_number,g.lc_number from  wo_booking_dtls b 
			,com_pi_item_details c , com_pi_master_details d ,wo_po_details_master f,com_btb_lc_master_details g,com_btb_lc_pi h
			where  b.booking_no=c.work_order_no and g.id=h.com_btb_lc_master_details_id and h.pi_id=d.id and b.job_no = f.job_no and  d.id=c.pi_id  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and g.item_basis_id in (1,2) and d.status_active=1 and d.is_deleted=0  and g.importer_id in ($company_name)
			group by f.style_ref_no,d.pi_number,g.lc_number"); 
			$bi=0;
			foreach ($btb_lc_number_data as $row){
					if($bi==0){
						$btb_lc_arr[$row[csf('style_ref_no')]]['lc_no'] .=$row[csf('lc_number')].",";
						$bi++;
					}else{
						$btb_lc_arr[$row[csf('style_ref_no')]]['lc_no'] .=$row[csf('lc_number')].",";
					}
			}

				$fabric_trims_rcv_number_data=sql_select("SELECT f.style_ref_no,g.recv_number_prefix_num,g.item_category, g.entry_form from  wo_booking_dtls b 
				,com_pi_item_details c , com_pi_master_details d ,wo_po_details_master f,inv_receive_master g where  b.booking_no=c.work_order_no and b.job_no = f.job_no and  d.id=c.pi_id and g.booking_no=d.pi_number  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and g.entry_form in (17,24) and  g.item_category  in (3,4) and d.status_active=1 and d.is_deleted=0  and g.company_id in ($company_name)  group by f.style_ref_no,g.recv_number_prefix_num,g.item_category, g.entry_form order by g.recv_number_prefix_num asc"); 

				$tr=0;
				$fr=0;
				foreach ($fabric_trims_rcv_number_data as $row) {
					if($row[csf('item_category')]==4 && $row[csf('entry_form')]==24){
						if($tr==0){
							$trims_rcv_arr[$row[csf('style_ref_no')]]['rcv_no'] .=$row[csf('recv_number_prefix_num')].",";
							$tr++;
						}else{
							$trims_rcv_arr[$row[csf('style_ref_no')]]['rcv_no'] .=$row[csf('recv_number_prefix_num')].",";
						}
					}else{
						if($fr==0){
							$fabric_rcv_arr[$row[csf('style_ref_no')]]['rcv_no'] .=$row[csf('recv_number_prefix_num')].",";
							$fr++;
						}else{
							$fabric_rcv_arr[$row[csf('style_ref_no')]]['rcv_no'] .=$row[csf('recv_number_prefix_num')].",";
						}
					}
				}


			

	//  echo'<pre>';
	//  print_r($p_c_approval_first_date); 

	ob_start();
	?>
	<br>
	<div style="width:3480px">


    <fieldset style="width:3480px; float:left;">
            <legend>Report Details Part</legend>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="3450" class="rpt_table" align="left">
                <thead>
				<tr>
					<th width="30">SL</th>
					<th width="100">Company Name</th>
					<th width="100">Team Name</th>
					<th width="100">Team Member</th>
					<th width="100">Buyer</th>
					<th width="100">Style</th>
					<th width="100">Job No</th>
					<th width="100">Item Description</th>
					<th width="100">Insert Date</th>
					<th width="100">OPD Date</th>
					<th width="100">PHD Date</th>
					<th width="100">Pre-Costing <br>Insert Date</th>
					<th width="100">First Pre-Costting <br>Approval Date</th>
					<th width="100">Last Pre-Costting <br>Approval Date</th>
					<th width="100">Pre Costing <br>Approval status</th>
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
			<div style="max-height:425px; overflow-y:scroll; width:3480px;" id="scroll_body">
                <table border="1" class="rpt_table" width="3450" rules="all" id="table_body" align="left">
                    <tbody>
					<? $sl=1;  
				$tmi=0;
				$i=0;
				foreach ($style_wise_data_arr as $style_id => $row) {
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
		
				foreach ($style_wise_data_arr as $style_id => $row) {

					
					$mst_id=$row['mst_id'];
				
					$p_c_approval_first_date=sql_select("select  id,mst_id,approved_date,entry_form,approved_no from approval_history where  current_approval_status=1 and entry_form=15 and mst_id=$mst_id group by mst_id,approved_date,id,entry_form,approved_no order by mst_id asc");
					$p_c_approval_last_date=sql_select("select  id,mst_id,approved_date,entry_form,approved_no from approval_history where  current_approval_status=1 and entry_form=15 and mst_id=$mst_id group by mst_id,approved_date,id,entry_form,approved_no order by mst_id desc");
					$s_c_approval_first_date=sql_select("select  id,mst_id,approved_date,entry_form,approved_no from approval_history where  current_approval_status=1 and entry_form=47 and mst_id=$mst_id group by mst_id,approved_date,id,entry_form,approved_no order by mst_id asc");
					$s_c_approval_lsat_date=sql_select("select  id,mst_id,approved_date,entry_form,approved_no from approval_history where  current_approval_status=1 and entry_form=47 and mst_id=$mst_id group by mst_id,approved_date,id,entry_form,approved_no order by mst_id desc");		
					
					?>

				<tr>
					<td width="30"><?= $sl; ?></td>
					<td width="100"><?= $row['company']; ?></td>
					<td width="100"><?= $row['team_leader'] ?></td>
					<td width="100"><?= implode(", ",array_unique(explode(",",$member_name_arr[$row['team_member_id']])))?></td>
					<td width="100"><?= $row['buyer_name'] ?></td>
					<td width="100"><?= $row['style_ref_no'] ?></td>
					<td width="100"><?= $row['job_no'] ?></td>
					<td width="100"><?= $row['gmts_item_id'] ?></td>				
					<td width="100"><?= $row['insert_date'] ?></td>					
					<td width="100"><?= $row['po_received_date'] ?></td>
					<td width="100"><?= $row['phd'] ?></td>
					<td width="100"><?=$pre_cost_arr[$style_id]['insert_date'] ?></td>
					<td width="100"><?=change_date_format($p_c_approval_first_date[0]['APPROVED_DATE'],'',''); ?></td>
					<td width="100"><?= change_date_format($p_c_approval_last_date[0]['APPROVED_DATE'],'',''); ?></td>
					<td width="100"><?
							if($pre_cost_arr[$style_id]['approved']==1){
									echo 'Yes';
							}else{
								echo 'No';
							}
					     ?>
					</td>
					<td width="100"><?= $sourcing_cost_arr[$style_id]['insert_date'] ?></td>
					<td width="100"><?= change_date_format($s_c_approval_first_date[0]['APPROVED_DATE'],'',''); ?></td>
					<td width="100"><?= change_date_format($s_c_approval_lsat_date[0]['APPROVED_DATE'],'',''); ?></td>
					<td width="100"><? 
							if($sourcing_cost_arr[$style_id]['approved']==1){
									echo 'Yes';
							}else{
								echo 'No';
							}
					     ?>
					</td>
					<td width="100"><?=	implode(", ",array_unique(explode(",",$fabric_cost_arr[$style_id]['booking_no'])))?></td>
					<td width="100"><?= implode(", ",array_unique(explode(",",$trims_cost_arr[$style_id]['booking_no']))) ?></td>
					<td width="100"><?= implode(", ",array_unique(explode(",",$wash_cost_arr[$style_id]['booking_no']))) ?></td>
					<td width="100"><?= implode(", ",array_unique(explode(",",$fabric_pi_arr[$style_id]['pi_no']))) ?></td>
					<td width="100"><?= implode(", ",array_unique(explode(",",$trims_pi_arr[$style_id]['pi_no']))) ?></td>
					<td width="100"><?= $row['sc_lc'] ?></td>
					<td width="100"><?= implode(", ",array_unique(explode(",",$btb_lc_arr[$style_id]['lc_no']))) ?></td>
					<td width="100"><?= implode(", ",array_unique(explode(",",$fabric_rcv_arr[$style_id]['rcv_no']))) ?></td>
					<td width="100"><?= implode(", ",array_unique(explode(",",$trims_rcv_arr[$style_id]['rcv_no']))) ?></td>
					<td width="100"><?= $row['set_smv'] ?></td>
					<td width="100"><?= $row['order_qty'] ?></td>
					<td width="100"><?= $unit_of_measurement[$row['uom']]  ?></td>
					<td width="100"><?= $row['order_qty']*$row['set_smv'] ?></td>
					<td width="100"><?= $row['unit_price'] ?></td>
					<td width="100"><?= $row['order_qty']*$row['unit_price'] ?></td>
					<td width="100"><?= $row['inserted_by'] ?></td>
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