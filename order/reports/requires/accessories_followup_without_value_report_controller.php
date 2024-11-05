<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.trims.php');
$user_id = $_SESSION['logic_erp']["user_id"];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$fabric_nature = $_SESSION['fabric_nature'];
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');

//--------------------------------------------------------------------------------------------------------------------

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "load_drop_down( 'requires/accessories_followup_without_value_report_controller', this.value, 'load_drop_down_season', 'season_td');" );
	exit();
}

if ($action=="load_drop_down_team_member")
{
if($data!=0)
	{
        echo create_drop_down( "cbo_team_member", 150, "select id,team_member_name 	 from lib_mkt_team_member_info  where team_id='$data' and status_active=1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-Select Team Member-", $selected, "" ); 
	}
 else
   {
		 echo create_drop_down( "cbo_team_member", 150, $blank_array,"", 1, "-Select Team Member- ", $selected, "" );
   }
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

	$company_name=str_replace("'","",$cbo_company_name);
	$serch_by=str_replace("'","",$cbo_search_by);
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
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}

	if(str_replace("'","",$cbo_team_name)>0){
		$team_cond=" and a.team_leader=$cbo_team_name";
	}
	if(str_replace("'","",$cbo_team_member)>0){
		$team_member_cond=" and a.dealing_marchant=$cbo_team_member";
	}

	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_job_no=trim($txt_job_no);
	if($txt_job_no !="" || $txt_job_no !=0) $jobcond="and a.job_no_prefix_num='".$txt_job_no."'"; else $jobcond="";

	if(str_replace("'","",$cbo_item_group)=="") $item_group_cond="";
	else $item_group_cond="and e.trim_group in(".str_replace("'","",$cbo_item_group).")";

	$date_type=str_replace("'","",$cbo_date_type);

	$date_cond='';
	if($date_type==2)
	{
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			$start_date=(str_replace("'","",$txt_date_from));
			$end_date=(str_replace("'","",$txt_date_to));
			$date_cond="and c.country_ship_date between '$start_date' and '$end_date'";
		}
	}
	else
	{
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			$start_date=(str_replace("'","",$txt_date_from));
			$end_date=(str_replace("'","",$txt_date_to));
			$date_cond="and b.pub_shipment_date between '$start_date' and '$end_date'";
		}
	}

	if (str_replace("'","",$txt_job_no)=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in (".str_replace("'","",$txt_job_no).") ";

	if(str_replace("'","",$txt_style_ref)!="") $style_ref_cond=" and a.style_ref_no = '".str_replace("'","",$txt_style_ref)."'"; else $style_ref_cond="";
	if(str_replace("'","",$txt_order_no)!="") $ordercond=" and b.po_number like '%".str_replace("'","",$txt_order_no)."%'"; else $ordercond="";
	$file_no=str_replace("'","",$txt_file_no);
	$internal_ref=str_replace("'","",$txt_internal_ref);
	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='".trim($file_no)."' ";
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='".trim($internal_ref)."' ";
	$cbo_year=str_replace("'","",$cbo_year);
	if($db_type==0)
	{
		if(trim($cbo_year)!=0) $year_cond=" and YEAR(a.insert_date)=$cbo_year"; else $year_cond="";
	}
	else if($db_type==2)
	{
		if(trim($cbo_year)!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year"; else $year_cond="";
	}

	//echo $file_no_cond.'=='.$internal_ref_cond;die;
	//echo "select format_id from lib_report_template where template_name ='".$company_name."' and module_id=2 and report_id=22 and is_deleted=0 and status_active=1"; die;
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$company_name."' and module_id=2 and report_id=22 and is_deleted=0 and status_active=1","format_id");
	//echo "gg"; die;
	"print_report_button_setting('$print_report_format');\n";
	//$print_report_button_setting=print_report_button_setting('$print_report_format');
	//echo "gg"; die;
	if(str_replace("'","",$cbo_search_by)==1)
	{
		if($template==1)
		{
			ob_start();
			?>
            <div style="width:2660px">
            <fieldset style="width:100%;">
			<table width="2650">
				<tr class="form_caption">
					<td colspan="35" align="center"><? echo $report_title; ?></td>
				</tr>
				<tr class="form_caption">
					<td colspan="35" align="center"><? echo $company_library[$company_name]; ?></td>
				</tr>
			</table>
			<table class="rpt_table" width="2550" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<th width="30">SL</th>
					<th width="50">Buyer</th>
					<th width="100">Job No</th>
                    <th width="100">Style Ref</th>
					<th width="100">Internal Ref</th>
                    <th width="100">File No</th>
					<th width="90">Order No</th>
					<th width="80">Order Qty</th>
					<th width="50">UOM</th>
					<th width="80">Qty (Pcs)</th>
					<th width="80">Shipment Date</th>
					<th width="100">Trims Name</th>
                    <th width="140">Item Description</th>
                    <th width="100">Remark</th>
					<th width="100">Brand/Sup Ref</th>
					<th width="60">Appr Req.</th>
					<th width="80">Approve Status</th>
                    <th width="100">Item Entry Date</th>
                    <th width="80">Avg. Cons</th>
					<th width="100">Req Qty</th>					
					<th width="90">WO Qty</th>
                    <th width="60">Trims UOM</th>
					<th width="100">WO Qty Balance</th>
                    <th width="150">Supplier</th>
                    <th width="70">WO Delay Days</th>
                    <th width="90">PI No</th>
					<th width="90">In-House Qty</th>
					<th width="90">Issue to Prod.</th>
					<th width="90">Left Over/Balance</th>
				</thead>
			</table>
			<div style="width:2570px; max-height:400px; overflow-y:scroll" id="scroll_body">
				<table class="rpt_table" width="2550" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
				<?
                $conversion_factor_array=array();$item_arr=array();
				$conversion_factor=sql_select("select id, trim_uom, order_uom, conversion_factor from lib_item_group where status_active=1  ");
				foreach($conversion_factor as $row_f)
				{
					$conversion_factor_array[$row_f[csf('id')]]['con_factor']=$row_f[csf('conversion_factor')];
					$conversion_factor_array[$row_f[csf('id')]]['cons_uom']=$row_f[csf('trim_uom')];
					$item_arr[$row_f[csf('id')]]['order_uom']=$row_f[csf('order_uom')];
				}
				unset($conversion_factor);

				$conversion_factor=array();
				$app_sql=sql_select("select job_no_mst,accessories_type_id,approval_status from wo_po_trims_approval_info");
				$app_status_arr=array();
				foreach($app_sql as $row)
				{
					$app_status_arr[$row[csf("job_no_mst")]][$row[csf("accessories_type_id")]]=$row[csf("approval_status")];
				}
				unset($app_sql);

				$sql_po_qty_country_wise_arr=array();
				$po_job_arr=array();
				$sql_po_country_data=sql_select("select  b.id, b.job_no_mst, c.country_id, sum(c.order_quantity/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst  and b.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $team_cond $team_member_cond group by   b.id,b.job_no_mst,c.country_id order by b.id,b.job_no_mst,c.country_id");
				foreach( $sql_po_country_data as $sql_po_country_row)
				{
					$sql_po_qty_country_wise_arr[$sql_po_country_row[csf('id')]][$sql_po_country_row[csf('country_id')]]=$sql_po_country_row[csf('order_quantity_set')];
					$po_job_arr[$sql_po_country_row[csf('id')]]=$sql_po_country_row[csf('job_no_mst')];
				}
				unset($sql_po_country_data);

				$po_data_arr=array();
				$po_id_string="";
				$today=date("Y-m-d");

				$sql_pos=("select a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, a.order_uom, b.file_no, b.grouping, b.id, b.po_number, sum(c.order_quantity) as order_quantity, sum(c.order_quantity/a.total_set_qnty) as order_quantity_set, b.pub_shipment_date
				from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
				where
				a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.company_name=$company_name $buyer_id_cond ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $date_cond $style_ref_cond $jobcond $ordercond $file_no_cond $internal_ref_cond $year_cond $team_cond $team_member_cond 
				group by a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, a.order_uom, a.total_set_qnty, b.file_no, b.grouping, b.id, b.po_number, b.pub_shipment_date order by b.id ASC");
				//echo $sql_pos; die;
				$sql_po=sql_select($sql_pos);
				$po_arr=array(); $tot_rows=0;
				foreach($sql_po as $row)
				{
					$tot_rows++;
					$po_arr[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
					$po_arr[$row[csf('id')]]['job_no']=$row[csf('job_no')];
					$po_arr[$row[csf('id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
					$po_arr[$row[csf('id')]]['style_ref']=$row[csf('style_ref_no')];
					$po_arr[$row[csf('id')]]['order_uom']=$row[csf('order_uom')];
					$po_arr[$row[csf('id')]]['file_no']=$row[csf('file_no')];
					$po_arr[$row[csf('id')]]['grouping']=$row[csf('grouping')];
					$po_arr[$row[csf('id')]]['po_number']=$row[csf('po_number')];
					$po_arr[$row[csf('id')]]['order_quantity']=$row[csf('order_quantity')];
					$po_arr[$row[csf('id')]]['order_quantity_set']=$row[csf('order_quantity_set')];
					$po_arr[$row[csf('id')]]['pub_shipment_date']=$row[csf('pub_shipment_date')];
					$po_id_string.=$row[csf('id')].",";
				}
				unset($sql_po);
				$po_id_string=rtrim($po_id_string,",");
				if($po_id_string=="")
				{
					echo "<strong style='color:red;font-size:30px'>No Data found</strong>";
					die;
				}

				$poIds=chop($po_id_string,','); $order_cond=""; $order_cond1=""; $order_cond2=""; $precost_po_cond="";
				if($db_type==2 && $tot_rows>1000)
				{
					$order_cond=" and (";
					$order_cond1=" and (";
					$order_cond2=" and (";
					$precost_po_cond=" and (";
					$poIdsArr=array_chunk(explode(",",$poIds),999);
					foreach($poIdsArr as $ids)
					{
						$ids=implode(",",$ids);
						//$poIds_cond.=" po_break_down_id in($ids) or ";
						$order_cond.=" b.po_break_down_id in($ids) or";
						$order_cond1.=" b.po_breakdown_id in($ids) or";
						$order_cond2.=" d.po_breakdown_id in($ids) or";
						$precost_po_cond.=" c.po_break_down_id in($ids) or";
					}
					$order_cond=chop($order_cond,'or ');
					$order_cond.=")";
					$order_cond1=chop($order_cond1,'or ');
					$order_cond1.=")";
					$order_cond2=chop($order_cond2,'or ');
					$order_cond2.=")";
					$precost_po_cond=chop($precost_po_cond,'or ');
					$precost_po_cond.=")";
				}
				else
				{
					$order_cond=" and b.po_break_down_id in($poIds)";
					$order_cond1=" and b.po_breakdown_id in($poIds)";
					$order_cond2=" and d.po_breakdown_id in($poIds)";
					$precost_po_cond=" and c.po_break_down_id in($poIds)";
				}

				$condition= new condition();
				if(str_replace("'","",$txt_job_no) !=''){
					$condition->job_no_prefix_num("=$txt_job_no");
				}
				if(str_replace("'","",$txt_order_no)!='')
				{
					//$condition->po_number("=$txt_order_no");
					$order_nos=str_replace("'","",$txt_order_no);
					$condition->po_number(" like '%$order_nos%'");
				}

				if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
				{
					$start_date=(str_replace("'","",$txt_date_from));
					$end_date=(str_replace("'","",$txt_date_to));
				}
				if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
				{
					  $condition->country_ship_date(" between '$start_date' and '$end_date'");
				}
				/*if(str_replace("'",'',$txt_po_breack_down_id) !="")
				{
					$condition->po_id("in($txt_po_breack_down_id)");
				}*/

				$condition->init();
				$trim= new trims($condition);
				//echo $trim->getQuery(); die;
				$trim_qty=$trim->getQtyArray_by_orderCountryAndPrecostdtlsid();
				//print_r($trim_qty);
				$trim= new trims($condition);
				$trim_amount=$trim->getAmountArray_by_orderAndPrecostdtlsid();

				$sql_pre_cost=sql_select("select a.costing_per, a.costing_date, b.id as trim_dtla_id, b.trim_group, b.remark, b.description, b.brand_sup_ref, b.cons_uom, b.cons_dzn_gmts, b.rate, b.amount, b.apvl_req, b.nominated_supp, b.insert_date, c.cons, c.pcs, c.country_id, c.cons as cons_cal, c.po_break_down_id
			from wo_pre_cost_mst a, wo_pre_cost_trim_cost_dtls b, wo_pre_cost_trim_co_cons_dtls c
			where c.job_no=b.job_no and b.id=c.wo_pre_cost_trim_cost_dtls_id and a.job_no=b.job_no and c.cons>0 $trm_group_pre_cost_cond $precost_po_cond
			group by a.costing_per, a.costing_date, b.id, b.trim_group, b.remark, b.description, b.brand_sup_ref, b.cons_uom, b.cons_dzn_gmts, b.rate, b.amount, b.apvl_req, b.nominated_supp, b.insert_date, c.cons, c.pcs, c.country_id, c.po_break_down_id order by b.trim_group ASC");

				$tot_rows=count($sql_pre_cost);
				$i=1;
				if(count($sql_pre_cost)>0)
				{
					foreach($sql_pre_cost as $rowp)
					{
						$dzn_qnty=0;
						if($rowp[csf('costing_per')]==1) $dzn_qnty=12;
						else if($rowp[csf('costing_per')]==3) $dzn_qnty=12*2;
						else if($rowp[csf('costing_per')]==4) $dzn_qnty=12*3;
						else if($rowp[csf('costing_per')]==5) $dzn_qnty=12*4;
						else $dzn_qnty=1;

						$poId=0;
						$poId=$rowp[csf('po_break_down_id')];

						$po_qty=0; $req_qnty=0; $req_value=0;
						if($rowp[csf('country_id')]==0)
						{
							$po_qty=$po_arr[$poId]['order_quantity'];
							$req_qnty+=$trim_qty[$poId][$rowp[csf('country_id')]][$rowp[csf('trim_dtla_id')]];
						}
						else
						{
							$country_id= explode(",",$rowp[csf('country_id')]);
							for($cou=0; $cou<=count($country_id); $cou++)
							{
								$po_qty+=$sql_po_qty_country_wise_arr[$poId][$country_id[$cou]];
								$req_qnty+=$trim_qty[$poId][$country_id[$cou]][$rowp[csf('trim_dtla_id')]];
							}
						}

						//$req_qnty=($rowp[csf('cons_cal')]/$dzn_qnty)*$po_qty;
						//$req_value= $rowp[csf('rate')]*$req_qnty;

						$req_value=$trim_amount[$poId][$rowp[csf('trim_dtla_id')]];

						$po_data_arr[$poId]['trim_dtla_id'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('trim_dtla_id')];// for rowspan
						$po_data_arr[$poId]['trim_group'][$rowp[csf('trim_group')]]=$rowp[csf('trim_group')];
						$po_data_arr[$poId][$rowp[csf('trim_group')]][$rowp[csf('trim_dtla_id')]]=$rowp[csf('trim_dtla_id')]; // for rowspannn
						$po_data_arr[$poId]['trim_group_dtls'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('trim_group')];
						$po_data_arr[$poId]['remark'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('remark')];

						$po_data_arr[$poId]['brand_sup_ref'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('brand_sup_ref')];
						$po_data_arr[$poId]['apvl_req'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('apvl_req')];
						$po_data_arr[$poId]['insert_date'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('insert_date')];
						$po_data_arr[$poId]['req_qnty'][$rowp[csf('trim_dtla_id')]]+=$req_qnty;
						$po_data_arr[$poId]['req_value'][$rowp[csf('trim_dtla_id')]]+=$req_value;
						$po_data_arr[$poId]['cons_uom'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('cons_uom')];

						$po_data_arr[$poId]['trim_group_from'][$rowp[csf('trim_dtla_id')]]="Pre_cost";
						$po_data_arr[$poId]['rate'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('rate')];
						$po_data_arr[$poId]['description'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('description')];
						$po_data_arr[$poId]['country_id'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('country_id')];

						$po_data_arr[$poId]['costing_per'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('costing_per')];
						$po_data_arr[$poId]['costing_date'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('costing_date')];
						$po_data_arr[$poId]['avg_cons'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('cons_dzn_gmts')];
					}
				}
				else
				{
					echo "<strong style='color:red;font-size:30px'>No Data found</strong>";
					die;
				}
				unset($sql_pre_cost);

				if($db_type==2)
				{
					$sql_without_precost=sql_select("select min(a.booking_date) as booking_date, b.job_no, LISTAGG(CAST(a.booking_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as booking_no, LISTAGG(CAST( a.supplier_id || '**' || a.pay_mode AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.booking_no) as supplier_id, b.po_break_down_id, b.trim_group, b.pre_cost_fabric_cost_dtls_id, sum(b.wo_qnty) as wo_qnty, sum(b.amount/b.exchange_rate) as amount,sum(b.rate) as rate from wo_booking_mst a, wo_booking_dtls b where a.item_category=4 and a.booking_no=b.booking_no  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name $order_cond group by b.po_break_down_id, b.trim_group, b.job_no, b.pre_cost_fabric_cost_dtls_id");//and item_from_precost=2
				}
				else if($db_type==0)
				{
					$sql_without_precost=sql_select("select min(a.booking_date) as booking_date, b.job_no, group_concat(a.booking_no) as booking_no, group_concat( concat_ws('**',a.supplier_id, a.pay_mode)) as supplier_id, b.po_break_down_id, b.trim_group, b.pre_cost_fabric_cost_dtls_id, sum(b.wo_qnty) as wo_qnty, sum(b.amount/b.exchange_rate) as amount, sum(b.rate) as rate from wo_booking_mst a, wo_booking_dtls b where a.item_category=4 and a.booking_no=b.booking_no  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name $order_cond  group by b.po_break_down_id,b.trim_group,b.job_no,b.pre_cost_fabric_cost_dtls_id");//and item_from_precost=2
				}

				$style_data_arr1=array();
				foreach($sql_without_precost as $wo_row)
				{
					$conversion_factor_rate=$conversion_factor_array[$wo_row[csf('trim_group')]]['con_factor'];
					//$cons_uom=$conversion_factor_array[$wo_row_without_precost[csf('trim_group')]]['cons_uom'];
					$cons_uom=$item_arr[$wo_row[csf('trim_group')]]['order_uom'];
					$booking_no=$wo_row[csf('booking_no')];
					$supplier_id=$wo_row[csf('supplier_id')];
					$wo_qnty=$wo_row[csf('wo_qnty')];
					$amount=$wo_row[csf('amount')];
					$wo_date=$wo_row[csf('booking_date')];

					$poId=0;
					$poId=$wo_row[csf('po_break_down_id')];

					if($wo_row[csf('pre_cost_fabric_cost_dtls_id')] =="" || $wo_row[csf('pre_cost_fabric_cost_dtls_id')] ==0)
					{
						$trim_dtla_id=max($po_data_arr[$poId]['trim_dtla_id'][$trim_dtla_id])+1;
						$po_data_arr[$poId]['trim_dtla_id'][$trim_dtla_id]=$trim_dtla_id;// for rowspan
						$po_data_arr[$poId]['trim_group'][$wo_row[csf('trim_group')]]=$wo_row[csf('trim_group')];
						$po_data_arr[$poId][$wo_row[csf('trim_group')]][$trim_dtla_id]=$trim_dtla_id;// for rowspannn
						$po_data_arr[$poId]['trim_group_dtls'][$trim_dtla_id]=$wo_row[csf('trim_group')];
						$po_data_arr[$poId]['cons_uom'][$trim_dtla_id]=$cons_uom;

						$po_data_arr[$poId]['trim_group_from'][$trim_dtla_id]="Booking Without Pre_cost";
					}
					else
					{
						$trim_dtla_id=$wo_row[csf('pre_cost_fabric_cost_dtls_id')];
					}

					$po_data_arr[$poId]['wo_qnty'][$trim_dtla_id]+=$wo_qnty;
					$po_data_arr[$poId]['amount'][$trim_dtla_id]+=$amount;
					$po_data_arr[$poId]['wo_date'][$trim_dtla_id]=$wo_date;
					$po_data_arr[$poId]['wo_qnty_trim_group'][$wo_row[csf('trim_group')]]+=$wo_qnty;
					$po_data_arr[$poId]['booking_no'][$trim_dtla_id]=$booking_no;
					$po_data_arr[$poId]['supplier_id'][$trim_dtla_id]=$supplier_id;
					$po_data_arr[$poId]['conversion_factor_rate'][$trim_dtla_id]=$conversion_factor_rate;
				}
				unset($sql_without_precost);
				//echo "select b.po_breakdown_id, a.item_group_id, sum(b.quantity) as quantity from  inv_receive_master c, product_details_master d, inv_trims_entry_dtls a, order_wise_pro_details b where a.mst_id=c.id and a.trans_id=b.trans_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $order_cond1  group by b.po_breakdown_id, a.item_group_id order by a.item_group_id ";
			$sql_rec_data=sql_select("select b.po_breakdown_id, a.item_group_id, c.receive_basis, a.booking_id, b.quantity as quantity, a.rate, c.exchange_rate, (b.quantity*d.avg_rate_per_unit) as amount from inv_receive_master c,product_details_master d, inv_trims_entry_dtls a, order_wise_pro_details b where a.mst_id=c.id and a.trans_id=b.trans_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_cond1 $trm_group_rec_cond order by a.item_group_id ");

				foreach($sql_rec_data as $row)
				{
					$poId=0; $poId=$row[csf('po_breakdown_id')];
					if($po_data_arr[$row[csf('po_breakdown_id')]]['trim_group'][$row[csf('item_group_id')]]=="" || $po_data_arr[$row[csf('po_breakdown_id')]]['trim_group'][$row[csf('item_group_id')]]==0)
					{
						$cons_uom=$conversion_factor_array[$row[csf('item_group_id')]]['cons_uom'];
						$trim_dtla_id=max($po_data_arr[$poId]['trim_dtla_id'])+1;
						$po_data_arr[$poId]['trim_dtla_id'][$trim_dtla_id]=$trim_dtla_id;// for rowspan
						$po_data_arr[$poId]['trim_group'][$row[csf('item_group_id')]]=$row[csf('item_group_id')];
						$po_data_arr[$poId][$row[csf('item_group_id')]][$trim_dtla_id]=$trim_dtla_id;// for rowspannn
						$po_data_arr[$poId]['trim_group_dtls'][$trim_dtla_id]=$row[csf('item_group_id')];
						$po_data_arr[$poId]['cons_uom'][$trim_dtla_id]=$cons_uom;
						$po_data_arr[$poId]['trim_group_from'][$trim_dtla_id]="Trim Receive";
						//echo $trim_dtla_id.'==';
					}
					$po_data_arr[$poId]['inhouse_qnty'][$row[csf('item_group_id')]]+=$row[csf('quantity')];
					$amount=0;  $amount=($row[csf('quantity')]*$row[csf('rate')]);//*$row[csf('exchange_rate')];
					$po_data_arr[$poId]['inhouse_amount'][$row[csf('item_group_id')]]+=$amount;
					$po_data_arr[$poId]['basis_piwono'][$row[csf('item_group_id')]].=$row[csf('receive_basis')].'_'.$row[csf('booking_id')].',';
				}
				unset($sql_rec_data);

				$sql_wo_pi=sql_select("select a.pi_number, b.work_order_no from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=4 and a.status_active=1 and a.importer_id=$company_name and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pi_number, b.work_order_no");
				$pi_arr=array();
				foreach($sql_wo_pi as $rowPi)
				{
					$pi_arr[$rowPi[csf('work_order_no')]].=$rowPi[csf('pi_number')].'**';
				}
				unset($sql_wo_pi);

				/*$receive_rtn_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id, sum(d.quantity) as quantity from inv_issue_master a,inv_transaction b, product_details_master c, order_wise_pro_details d,inv_receive_master e  where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and e.id=a.received_id and b.transaction_type=3 and a.entry_form=49 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $order_cond2 $trm_group_recrtn_cond  group by d.po_breakdown_id, c.item_group_id order by c.item_group_id");
				//echo $receive_rtn_qty_data; die;
				foreach($receive_rtn_qty_data as $row)
				{
					$ord_uom_qty=0;
					$ord_uom_qty=$row[csf('quantity')]/$item_arr[$row[csf('item_group_id')]]['order_uom'];
					$po_data_arr[$row[csf('po_breakdown_id')]]['receive_rtn_qty'][$row[csf('item_group_id')]]+=$ord_uom_qty;
				}
				unset($receive_rtn_qty_data);	*/

				$receive_rtn_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id, d.quantity as quantity, c.avg_rate_per_unit as rate from product_details_master c,order_wise_pro_details d where d.prod_id=c.id and d.trans_type=3 and d.entry_form=49 and d.status_active=1 and d.is_deleted=0 $order_cond2 order by c.item_group_id ASC");
				foreach($receive_rtn_qty_data as $row)
				{
					$ord_uom_qty=0; $receive_rtn_amt=0;
					//$ord_uom_qty=$row[csf('quantity')]/$conversion_factor_array[$row[csf('item_group_id')]]['con_factor'];
					$ord_uom_qty=$row[csf('quantity')];
					$receive_rtn_amt=$ord_uom_qty*$row[csf('rate')];
					$po_data_arr[$row[csf('po_breakdown_id')]]['receive_rtn_qty'][$row[csf('item_group_id')]]+=$ord_uom_qty;
					$po_data_arr[$row[csf('po_breakdown_id')]]['receive_rtn_amt'][$row[csf('item_group_id')]]+=$receive_rtn_amt;
				}
				//echo "<pre>";print_r($style_data_arr);
				unset($receive_rtn_qty_data);

				$transfer_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id, sum((case when d.trans_type=5 then d.quantity else 0 end)-(case when d.trans_type=6 then d.quantity else 0 end)) as quantity,
				sum(case when d.trans_type=5 then d.quantity else 0 end) as in_qty,
				sum(case when d.trans_type=6 then d.quantity else 0 end) as out_qty,
				sum(case when d.trans_type=5 then (d.quantity*c.avg_rate_per_unit) else 0 end) as in_amount,
				sum(case when d.trans_type=6 then (d.quantity*c.avg_rate_per_unit) else 0 end) as out_amount
				from product_details_master c,order_wise_pro_details d
				where d.prod_id=c.id and d.trans_type in(5,6) and d.entry_form=78 and d.status_active=1 and d.is_deleted=0 $order_cond2 group by d.po_breakdown_id, c.item_group_id order by c.item_group_id");
				foreach($transfer_qty_data as $row)
				{
					$transfe_amount=0;
					$transfe_amount=$row[csf('in_amount')]-$row[csf('out_amount')];
					$po_data_arr[$row[csf('po_breakdown_id')]]['transfe_qty'][$row[csf('item_group_id')]]+=$row[csf('quantity')];
					$po_data_arr[$row[csf('po_breakdown_id')]]['transfe_in'][$row[csf('item_group_id')]]+=$row[csf('in_qty')];
					$po_data_arr[$row[csf('po_breakdown_id')]]['transfe_out'][$row[csf('item_group_id')]]+=$row[csf('out_qty')];

					$po_data_arr[$row[csf('po_breakdown_id')]]['transfe_amount'][$row[csf('item_group_id')]]+=$transfe_amount;
					$po_data_arr[$row[csf('po_breakdown_id')]]['transfe_in_amount'][$row[csf('item_group_id')]]+=$row[csf('in_amount')];
					$po_data_arr[$row[csf('po_breakdown_id')]]['transfe_out_amount'][$row[csf('item_group_id')]]+=$row[csf('out_amount')];
				}
				unset($transfer_qty_data);


				$issue_qty_data=sql_select("select b.po_breakdown_id, p.item_group_id,sum(b.quantity) as quantity, sum(b.quantity*b.order_rate) as issue_amount
			from inv_issue_master d, product_details_master p, inv_trims_issue_dtls a, order_wise_pro_details b
			where a.mst_id=d.id and a.prod_id=p.id and a.trans_id=b.trans_id and b.trans_type=2 and d.entry_form=25 and b.entry_form=25 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_cond1 $trm_group_iss_cond group by b.po_breakdown_id, p.item_group_id");
				//echo $issue_qty_data; die;
				foreach($issue_qty_data as $row)
				{
					$po_data_arr[$row[csf('po_breakdown_id')]]['issue_qty'][$row[csf('item_group_id')]]+=$row[csf('quantity')];
					$po_data_arr[$row[csf('po_breakdown_id')]]['issue_amount'][$row[csf('item_group_id')]]+=$row[csf('amount')];
				}
				unset($issue_qty_data);


				$issue_rtn_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id, d.quantity as quantity, (d.quantity*c.avg_rate_per_unit) as amount from product_details_master c,order_wise_pro_details d where d.prod_id=c.id and d.trans_type=4 and d.entry_form=73 and d.status_active=1 and d.is_deleted=0 $order_cond2 order by c.item_group_id");
				foreach($issue_rtn_qty_data as $row)
				{
					$po_data_arr[$row[csf('po_breakdown_id')]]['issue_rtn_qty'][$row[csf('item_group_id')]]+=$row[csf('quantity')];
					$po_data_arr[$row[csf('po_breakdown_id')]]['issue_rtn_amt'][$row[csf('item_group_id')]]+=$row[csf('amount')];
				}

				unset($issue_rtn_qty_data);


				/*$sql_rec_rtn_data=sql_select("select d.po_breakdown_id, c.item_group_id,sum(d.quantity) as quantity   from  inv_issue_master a,inv_transaction b, product_details_master c, order_wise_pro_details d,inv_receive_master e  where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and e.id=a.received_id   and b.transaction_type=3 and a.entry_form=49 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $order_cond2  group by d.po_breakdown_id, c.item_group_id order by c.item_group_id");

				foreach($sql_rec_rtn_data as $row)
				{
					$po_data_arr[$row[csf('po_breakdown_id')]]['receive_rtn_qty'][$row[csf('item_group_id')]]+=$row[csf('quantity')];
				}
				unset($sql_rec_rtn_data);*/

				/*$sql_issue_data=sql_select("select b.po_breakdown_id, a.item_group_id,sum(b.quantity) as quantity  from  inv_issue_master d,product_details_master p,inv_trims_issue_dtls a , order_wise_pro_details b where a.mst_id=d.id and a.prod_id=p.id and a.trans_id=b.trans_id and b.trans_type=2 and b.entry_form=25 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $order_cond1  group by b.po_breakdown_id, a.item_group_id");
				foreach($sql_issue_data as $row)
				{
					$po_data_arr[$row[csf('po_breakdown_id')]]['issue_qty'][$row[csf('item_group_id')]]+=$row[csf('quantity')];
				}
				unset($sql_issue_data);*/
				$bookingNoArr=return_library_array( "select id, booking_no from  wo_booking_mst", "id", "booking_no");
				$piArr=return_library_array("select id, pi_number from com_pi_master_details", "id", "pi_number");
				$total_pre_costing_value=0; $total_wo_value=0; $total_left_over_balanc=0;$total_issue_amount=0;$total_rec_bal_qnty=0;
				$summary_array=array();
				$i=1; $x=0;
				foreach($po_data_arr as $key=>$value)
				{
				    $z=1;
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					foreach($value['trim_group'] as $key_trim=>$value_trim)
					{   $y=1;
						$summary_array['trim_group'][$key_trim]=$key_trim;
						foreach($value[$key_trim] as $key_trim1=>$value_trim1)
						{
							if($z==1) $style_color=''; else $style_color=$bgcolor."; border: none";
							$z++;

							if($y==1) $style_colory=''; else $style_colory=$bgcolor."; border: none";
							$x++; $y++;
							$po_qty=0; $po_qty_set=0; $buyer_name=''; $job_no=''; $job_no_prefix_num=''; $style_ref_no=''; $grouping=''; $file_no=''; $order_uom=''; $po_number=''; $pub_shipment_date='';
							$po_qty=$po_arr[$key]['order_quantity'];
							$po_qty_set=$po_arr[$key]['order_quantity_set'];
							$buyer_name=$po_arr[$key]['buyer'];
							$job_no=$po_arr[$key]['job_no'];
							$job_no_prefix_num=$po_arr[$key]['job_no_prefix_num'];
							$style_ref_no=$po_arr[$key]['style_ref'];
							$grouping=$po_arr[$key]['grouping'];
							$file_no=$po_arr[$key]['file_no'];
							$order_uom=$po_arr[$key]['order_uom'];
							$po_number=$po_arr[$key]['po_number'];
							$pub_shipment_date=$po_arr[$key]['pub_shipment_date'];
						?>
						<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $x; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $x; ?>">
							<td width="30" style="color:<? echo $style_color; ?>" title="<? echo $po_qty; ?>"  ><? echo $i; ?></td>
							<td width="50" style="color:<? echo $style_color; ?>"><p><? echo $buyer_short_name_library[$buyer_name]; ?></p></td>
							<td width="100" style="color:<? echo $style_color; ?>" align="center" ><p><? echo $job_no_prefix_num; ?></p></td>
							<td width="100" style="word-break: break-all;color:<? echo $style_color ?>"><p><? echo $style_ref_no; ?></p></td>
							<td width="100" style="word-break: break-all;color:<? echo $style_color ?>"><p><? echo $grouping; ?></p></td>
							<td width="100" style="word-break: break-all;color:<? echo $style_color ?>"><p><? echo $file_no; ?></p></td>
							<td width="90" style="word-break: break-all;color:<? echo $style_color ?>"><p>
								<a style="word-break: break-all;color: <? echo $style_color ?>" href='#report_details' onclick="generate_report('<? echo $company_name; ?>','<? echo $job_no; ?>','<? echo $buyer_name; ?>','<? echo $style_ref_no; ?>','<? echo change_date_format($value['costing_date'][$key_trim1]); ?>','<? echo $key; ?>','<? echo $value['costing_per'][$key_trim1]; ?>','preCostRpt');"><? echo $po_number; ?></a></p></td>
                            <td width="80" style="word-break: break-all;color:<? echo $style_color ?>" align="right"><p><a style="word-break: break-all;color: <? echo $style_color ?>" href='#report_details' onclick="order_qty_popup('<? echo $company_name; ?>','<? echo $job_no; ?>','<? echo $key; ?>', '<? echo $buyer_name; ?>',<? echo $txt_date_from; ?>,<? echo $txt_date_to; ?>,'order_qty_data');"><? echo number_format($po_qty_set,0,'.',''); ?></a></p></td>

							<td width="50" align="center" style="word-break:break-all;color:<? echo $style_color; ?>"><p><? echo $unit_of_measurement[$order_uom]; ?></p></td>
							<td width="80" align="right" style="word-break: break-all;color:<? echo $style_color ?>"><p><? echo number_format($po_qty,0,'.',''); ?></p></td>
							<td width="80" align="center" style="word-break: break-all;color:<? echo $style_color ?>"><? echo change_date_format($pub_shipment_date); ?></td>
							<td width="100" title="<? echo $value['trim_group_from'][$key_trim1]; ?>" style="word-break: break-all;"><p><? echo $item_library[$value[trim_group_dtls][$key_trim1]]; ?></p></td>
                            <td width="140"><p><? echo $value['description'][$key_trim1]; ?></p></td>
							<td width="100"><p><? echo $value['remark'][$key_trim1]; ?></p></td>
							<td width="100"><p><? echo $value['brand_sup_ref'][$key_trim1]; ?></p></td>
							<td width="60" align="center"><p><? if($value['apvl_req'][$key_trim1]==1) echo "Yes"; else echo ""; ?></p></td>
							<td width="80" align="center"><?
								if($value['apvl_req'][$key_trim1]==1)
								{
									$app_status=$app_status_arr[$job_no][$value['trim_group_dtls'][$key_trim1]];
									$approved_status=$approval_status[$app_status];
									$summary_array[item_app][$key_trim][all]+=1;
									if($app_status==3)
									{
										$summary_array[item_app][$key_trim][app]+=1;
									}
								}
								else
								{
									$approved_status="";
								}
								echo $approved_status; ?></td>
                          	<td width="100"><p><? $insert_date=explode(" ",$value['insert_date'][$key_trim1]); echo change_date_format($insert_date[0],'','',''); ?></p></td>
                            <td width="80" align="right"><?=number_format($value['avg_cons'][$key_trim1],4); ?></td>
							<td width="100" align="right"><p>
                                <a href='#report_details' onclick="order_req_qty_popup('<? echo $company_name; ?>','<? echo $job_no; ?>','<? echo $key; ?>','<? echo $buyer_name; ?>','<? echo $value['rate'][$key_trim1]; ?>','<? echo $value['trim_group_dtls'][$key_trim1];?>' ,'<? echo $value['booking_no'][$key_trim1] ;?>','<? echo $value['description'][$key_trim1];?>','<? echo $value['country_id'][$key_trim1]; ?>','<? echo $value['trim_dtla_id'][$key_trim1]; ?>','<? echo $start_date ?>','<? echo $end_date ?>','order_req_qty_data');"><? $req_qty=number_format($value['req_qnty'][$key_trim1],2,'.',''); echo $req_qty;
								$summary_array[req_qnty][$key_trim]+=$value['req_qnty'][$key_trim1]; ?></a></p></td>
                                <?
							    $wo_qnty=number_format($value['wo_qnty'][$key_trim1],2,'.','');


								if(($value['conversion_factor_rate'][$key_trim1]*$wo_qnty)==$req_qty) $color_wo="green";
								else if(($value['conversion_factor_rate'][$key_trim1]*$wo_qnty) > $req_qty) $color_wo="red";
								else if(($value['conversion_factor_rate'][$key_trim1]*$wo_qnty) < $req_qty ) $color_wo="yellow";
								else $color_wo="";

								$supplier_name_string="";
								$supplier_id_arr=array_unique(explode(',',$value['supplier_id'][$key_trim1]));
								//print_r($supplier_id_arr);
								foreach($supplier_id_arr as $supplier_id_arr_key=>$supplier_id_arr_value)
								{
									$ex_sup_data=explode("**",$supplier_id_arr_value);
									if($ex_sup_data[1]==3 || $ex_sup_data[1]==5) $suplier_name_arr=$company_library; else $suplier_name_arr=$lib_supplier_arr;
									$supplier_name_string.=$suplier_name_arr[$ex_sup_data[0]].",";
								}

								$booking_no_arr=array_unique(explode(',',$value['booking_no'][$key_trim1]));
								$main_booking_no_large_data=""; $piWoNo='';
								foreach($booking_no_arr as $booking_no1)
								{
									if($main_booking_no_large_data=="") $main_booking_no_large_data=$booking_no1; else $main_booking_no_large_data.=",".$booking_no1;
									if($booking_no1!="")
									{
										if($piWoNo=="") $piWoNo=implode(',',array_filter(array_unique(explode("**",$pi_arr[$booking_no1])))); else $piWoNo.=",".implode(',',array_filter(array_unique(explode("**",$pi_arr[$booking_no1]))));//
									}
								}
								?>
							<td width="90" align="right" title="<? echo 'conversion_factor='.$value['conversion_factor_rate'][$key_trim1];?>" bgcolor="<? echo $color_wo;?>"><p><a href='#report_details' onclick="openmypage('<? echo $key; ?>','<? echo $value['trim_group_dtls'][$key_trim1]; ?>','<? echo $job_no; ?>','<? echo $main_booking_no_large_data;?>','<? echo $value['trim_dtla_id'][$key_trim1];?>','booking_info');">
								<? echo number_format($value['wo_qnty'][$key_trim1],2,'.',''); $summary_array[wo_qnty][$key_trim]+=$value['wo_qnty'][$key_trim1]; ?></a></p></td>
                            <td width="60"><p><? echo $unit_of_measurement[$item_arr[$value['trim_group_dtls'][$key_trim1]]['order_uom']];//    $item_arr[$key['trim_group_dtls'][$key_trim1]]['order_uom'];
								$summary_array[cons_uom][$key_trim]=$item_arr[$value['trim_group_dtls'][$key_trim1]]['order_uom']; ?></p></td>

							<td width="100"><p><? echo number_format($wo_qnty-$req_qty,2,'.','');; ?></p></td>
                            <td width="150"><p><? echo rtrim($supplier_name_string,","); ?></p></td>
                            <td width="70" title="<? echo change_date_format($value['wo_date'][$key_trim1]);?>"><p><?
								$tot=change_date_format($insert_date[0]);
								if($value['wo_qnty'][$key_trim1]<=0 )
									$daysOnHand = datediff('d',$tot,$today);
								else
								{
									$wo_date=$value['wo_date'][$key_trim1];
									$wo_date=change_date_format($wo_date);
									$daysOnHand = datediff('d',$tot,$wo_date);;
								}
								echo $daysOnHand; ?></p></td>
                                <?
								$transfe_out=number_format($value['transfe_out'][$key_trim],2,'.','');
								$transfe_in=number_format($value['transfe_in'][$key_trim],2,'.','');
								$transfe_in_out=$transfe_in.' & '.$transfe_out;

								$transfe_out_amt=number_format($value['transfe_out_amount'][$key_trim],2,'.','');
								$transfe_in_amt=number_format($value['transfe_in_amount'][$key_trim],2,'.','');
								$transfe_in_out_amt=$transfe_in_amt.' & '.$transfe_out_amt;
								$inhouse_amount=0;
								$inhouse_qnty=($value['inhouse_qnty'][$key_trim]+$value['transfe_qty'][$key_trim])-$value['receive_rtn_qty'][$key_trim];
								$inhouse_amount=($value['inhouse_amount'][$key_trim]+$value['transfe_amount'][$key_trim])-$value['receive_rtn_amt'][$key_trim];
								$total_inhouse_value+=$inhouse_amount;
								$balance=$value['wo_qnty_trim_group'][$key_trim]-$inhouse_qnty;
								$issue_qnty=$value['issue_qty'][$key_trim]-$value['issue_rtn_qty'][$key_trim];
								$issue_amount=$value['issue_amount'][$key_trim]-$value['issue_rtn_amt'][$key_trim];
								$left_overqty=$inhouse_qnty-$issue_qnty;
								$left_overamt=$inhouse_amount-$issue_amount;

								$summary_array[inhouse_qnty][$key_trim]+=$inhouse_qnty;
								$summary_array[inhouse_qnty_bl][$key_trim]+=$balance;
								$summary_array[issue_qty][$key_trim]+=$issue_qnty;
								$summary_array[left_overqty][$key_trim]+=$left_overqty;
								?>
                             <td width="90" rowspan="<? echo $rowspannn; ?>"><p><? echo $piWoNo; ?></p></td>
                            <td width="90" align="right" style="color:<? echo $style_colory ?>" title="<? echo "Inhouse-Qty: ".number_format($value['inhouse_qnty'][$key_trim]-$value['receive_rtn_qty'][$key_trim],2,'.','')."\n Transfer In & Out Qty: ".$transfe_in_out."\n Return Qty: ".number_format($value['receive_rtn_qty'][$key_trim],2,'.',''); ?>"><a style="color:<? echo $style_colory ?>" href='#report_details' onclick="openmypage_inhouse('<? echo $key; ?>','<? echo $value['trim_group_dtls'][$key_trim1]; ?>','booking_inhouse_info');"><? echo number_format($inhouse_qnty,2,'.',''); ?></a></td>
							<td width="90" align="right" title="<? echo "Issue-Qty: ".number_format($value['issue_qty'][$key_trim],2,'.','')."\n Issue Return Qty: ".number_format($value['issue_rtn_qty'][$key_trim],2,'.',''); ?>" style="color:<? echo $style_colory ?>"><a style="color:<? echo $style_colory ?>" href='#report_details' onclick="openmypage_issue('<? echo $key; ?>','<? echo $value['trim_group_dtls'][$key_trim1]; ?>','booking_issue_info');"><? echo number_format($issue_qnty,2,'.',''); ?></a></td>
							<td width="90" align="right" style="color:<? echo $style_colory ?>"><? echo number_format($left_overqty,2,'.',''); ?></td>

						</tr>
						<?
					}// end  foreach($value[$key_trim] as $key_trim1=>$value_trim1)
				}
				$i++;
			}
			unset($po_data_arr);
			?>
			</table>
            </div>
            <table class="rpt_table" width="2550" cellpadding="0" cellspacing="0" border="1" rules="all">
                <tfoot>
                    <th width="30">&nbsp;</th>
                    <th width="50">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="90">&nbsp;</th>
                    <th width="80" align="right" id="total_order_qnty"><? //echo number_format($total_order_qnty,0); ?></th>
                    <th width="50">&nbsp;</th>
                    <th width="80" align="right" id="total_order_qnty_in_pcs"><? //echo number_format($total_order_qnty_in_pcs,0); ?></th>
                    <th width="80">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="140">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="100" align="right" id="value_req_qnty"><? //echo number_format($total_req_qnty,2); ?></th>
                    <th width="90" align="right" id=""><? //echo number_format($total_wo_qnty,2); ?></th>
                    <th width="60" align="right" ></th>
                    <th width="100" align="right" id="value_wo_qty"><? //echo number_format($total_wo_value,2); ?></th>
                    <th width="100" align="right" id="value_wo_balance"><? //echo number_format($total_wo_value,2); ?></th>
					<th width="100">&nbsp;</th>
                    <th width="150" align="right" id=""></th>
                    <th width="70" align="right"><p><? //echo number_format($req_value,2,'.',''); ?>&nbsp;</p></th>
                    <th width="90">&nbsp;</th>
                    <th width="90" align="right" id="value_in_qty"><? //echo number_format($total_in_qnty,2); ?></th>
                    <th width="90" align="right" id="value_in_amount"><? //echo number_format($total_inhouse_value,2); ?></th>
                    <th width="90" align="right" id="value_rec_qty"><? //echo number_format($total_rec_bal_qnty,2); ?></th>
                    <th width="90" align="right" id="value_issue_qty"><? //echo number_format($total_issue_qnty,2); ?></th>
                    <th width="90" align="right" id="value_issue_amount"><? //echo number_format($total_issue_amount,2); ?></th>
                    <th width="90" align="right" id="value_leftover_qty"><? //echo number_format($total_leftover_qnty,2); ?></th>
                    <th align="right" id="value_leftover_amount"><? //echo number_format($total_left_over_balanc,2); ?></th>
                </tfoot>
            </table>
            <table>
                <tr><td height="17"></td></tr>
            </table>
			<u><b>Summary</b></u>
            <table class="rpt_table" width="1200" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <th width="30">SL</th>
                    <th width="110">Item</th>
                    <th width="60">UOM</th>
                    <th width="80">Approved %</th>
                    <th width="110">Req Qty</th>
                    <th width="110">WO Qty</th>
                    <th width="80">WO %</th>
                    <th width="110">In-House Qty</th>
                    <th width="80">In-House %</th>
                    <th width="110">In-House Balance Qty</th>
                    <th width="110">Issue Qty</th>
                    <th width="80">Issue %</th>
                    <th>Left Over</th>
                </thead>
					<?
					$z=1; $tot_req_qnty_summary=0;
					foreach($summary_array[trim_group] as $key_trim=>$value)
					{
						if($z%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
						<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr2_<? echo $z; ?>','<? echo $bgcolor;?>')" id="tr2_<? echo $z; ?>">
							<td width="30"><? echo $z; ?></td>
							<td width="110"><p><? echo $item_library[$key_trim]; ?></p></td>
							<td width="60" align="center"><? echo $unit_of_measurement[$summary_array[cons_uom][$key_trim]]; ?></td>
							<td width="80" align="right"><?
							//$app_perc=($summary_array[item_app][$key_trim][app]*100)/$summary_array[item_app][$key_trim][all]; echo number_format($app_perc,2);
							$app_perc=($summary_array[item_app][$key_trim][app]*100)/$summary_array[item_app][$key_trim][all]; if ($app_perc>=0) echo $app_perc; ?></td>
							<td width="110" align="right"><? echo number_format($summary_array[req_qnty][$key_trim],2); ?></td>
							<td width="110" align="right"><? echo number_format( $summary_array[wo_qnty][$key_trim],2); ?></td>
							<td width="80" align="right"><? $wo_per= $summary_array[wo_qnty][$key_trim]/$summary_array[req_qnty][$key_trim]*100; echo number_format($wo_per,2).'%'; ?></td>
							<td width="110" align="right"><? echo number_format($summary_array[inhouse_qnty][$key_trim],2); ?></td>
							<td width="80" align="right"><? $in_per=$summary_array[inhouse_qnty][$key_trim]/$summary_array[wo_qnty][$key_trim]*100; echo number_format($in_per,2).'%'; ?></td>
							<td width="110" align="right"><? echo number_format($summary_array[inhouse_qnty_bl][$key_trim],2); $in_house_bal+=($value['wo']-$value['in']); ?></td>
							<td width="110" align="right"><? echo number_format($summary_array[issue_qty][$key_trim],2); ?></td>
							<td width="80" align="right"><? $wo_per=$summary_array[issue_qty][$key_trim]/$summary_array[wo_qnty][$key_trim]*100; echo number_format($wo_per,2).'%'; ?></td>
							<td align="right"><? echo number_format($summary_array[left_overqty][$key_trim],2); ?></td>
						</tr>
					<?
					$z++;
					}
					unset($summary_array);
				?>
				<tfoot>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th align="right"><? //echo number_format($tot_req_qnty_summary,2); ?>&nbsp;</th>
					<th align="right"><? //echo number_format($tot_wo_qnty_summary,2); ?>&nbsp;</th>
					<th>&nbsp;</th>
					<th align="right"><? //echo number_format($tot_in_qnty_summary,2); ?>&nbsp;</th>
					<th>&nbsp;</th>
					<th align="right"><? //echo number_format($in_house_bal,2); ?>&nbsp;</th>
					<th align="right"><? //echo number_format($tot_issue_qnty_summary,2); ?>&nbsp;</th>
					<th>&nbsp;</th>
					<th align="right"><? //echo number_format($tot_leftover_qnty_summary,2); ?>&nbsp;</th>
				</tfoot>
			</table>
			</fieldset>
		</div>
		<?
        }
	}
	//===========================================================================================================================================================
 	else if(str_replace("'","",$cbo_search_by)==2)
	{
		if($template==1)
		{
			ob_start();
			?>
			<div style="width:2490px">
			<fieldset style="width:100%;">
			<table width="2590">
                <tr class="form_caption"><td colspan="32" align="center"><? echo $report_title; ?></td></tr>
                <tr class="form_caption"><td colspan="32" align="center"><? echo $company_library[$company_name]; ?></td></tr>
			</table>
			<table class="rpt_table" width="2550" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <th width="30">SL</th>
                    <th width="50">Buyer</th>
                    <th width="100">Job No</th>
                    <th width="100">Style Ref</th>
                    <th width="100">Internal Ref</th>
                    <th width="100">File No</th>
                    <th width="90">Order No</th>
                    <th width="80">Order Qty</th>
                    <th width="50">UOM</th>
                    <th width="80">Qty (Pcs)</th>
                    <th width="80">Shipment Date</th>
                    <th width="100">Trims Name</th>
                    <th width="140">Item Description</th>
                    <th width="100">Brand/Sup Ref</th>
                    <th width="60">Appr Req.</th>
                    <th width="80">Approve Status</th>
                    <th width="100">Item Entry Date</th>
                    <th width="80">Avg. Cons</th>
                    <th width="100">Req Qty</th>
                    <th width="90">WO Qty</th>
                    <th width="60">Trims UOM</th>
					<th width="100">WO Qty Balance</th>
                    <th width="150">Supplier</th>
                    <th width="70">WO Delay Days</th>
                    <th width="90">PI No.</th>
                    <th width="90">In-House Qty</th>
                    <th width="90">Issue to Prod.</th>
                    <th width="90">Left Over/Balance Qty</th>
                </thead>
			</table>
            <div style="width:2570px; max-height:400px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="2550" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
                <?
				$conversion_factor_array=array(); $item_arr=array();
				$conversion_factor=sql_select("select id ,trim_uom,order_uom,conversion_factor from  lib_item_group  where status_active=1 and item_category=4");
				foreach($conversion_factor as $row_f)
				{
					$conversion_factor_array[$row_f[csf('id')]]['con_factor']=$row_f[csf('conversion_factor')];
					$conversion_factor_array[$row_f[csf('id')]]['cons_uom']=$row_f[csf('trim_uom')];
					$item_arr[$row_f[csf('id')]]['order_uom']=$row_f[csf('order_uom')];
				}
				unset($conversion_factor);

				$app_sql=sql_select("select job_no_mst,accessories_type_id,approval_status from wo_po_trims_approval_info");
				$app_status_arr=array();
				foreach($app_sql as $row)
				{
					$app_status_arr[$row[csf("job_no_mst")]][$row[csf("accessories_type_id")]]=$row[csf("approval_status")];
				}
				unset($app_sql);

				$sql_po_qty_country_wise_arr=array();
				$po_job_arr=array(); $style_po_qty_arr=array();
				$sql_po_qty_country_wise=sql_select("select  b.id, b.job_no_mst, c.country_id, sum(c.order_quantity/a.total_set_qnty) as order_quantity_set, sum(c.order_quantity) as order_quantity  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst  and b.id=c.po_break_down_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $year_cond group by b.id, b.job_no_mst, c.country_id order by b.id, b.job_no_mst, c.country_id");
				foreach( $sql_po_qty_country_wise as $sql_po_qty_country_wise_row)
				{
					$sql_po_qty_country_wise_arr[$sql_po_qty_country_wise_row[csf('id')]][$sql_po_qty_country_wise_row[csf('country_id')]]=$sql_po_qty_country_wise_row[csf('order_quantity_set')];
					$po_job_arr[$sql_po_qty_country_wise_row[csf('id')]]=$sql_po_qty_country_wise_row[csf('job_no_mst')];
					$style_po_qty_arr[$sql_po_qty_country_wise_row[csf('job_no_mst')]]['order_qty_set']+=$sql_po_qty_country_wise_row[csf('order_quantity_set')];
					$style_po_qty_arr[$sql_po_qty_country_wise_row[csf('job_no_mst')]]['po_qty']+=$sql_po_qty_country_wise_row[csf('order_quantity')];
				}
				//print_r($style_po_qty_arr);
				unset($sql_po_qty_country_wise);

				$style_data_arr=array();
				$po_id_string="";
				$today=date("Y-m-d");
				$sql_pos=("select a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, a.order_uom, b.file_no, b.grouping, b.id, b.po_number, sum(c.order_quantity) as order_quantity, sum(c.order_quantity/a.total_set_qnty) as order_quantity_set, b.pub_shipment_date
				from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
				where
				a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.company_name=$company_name $buyer_id_cond ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $date_cond $style_ref_cond $jobcond  $ordercond $file_no_cond $internal_ref_cond  $year_cond
				group by a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, a.order_uom, a.total_set_qnty, b.file_no, b.grouping, b.id, b.po_number, b.pub_shipment_date order by b.id");
				//echo $sql_pos; die;//and a.job_no='FAL-16-00179'
				$sql_po=sql_select($sql_pos);
				$tot_rows=0;  $style_data_all=array();
				foreach($sql_po as $row)
				{
					$tot_rows++;

					$style_data[$row[csf('job_no')]]['job_data']=$row[csf("buyer_name")]."##".$row[csf("job_no_prefix_num")]."##".$row[csf("style_ref_no")]."##".$row[csf("order_uom")];

					$style_data_all[$row[csf('job_no')]].=$row[csf("file_no")]."__".$row[csf("grouping")]."__".$row[csf("po_number")]."__".$row[csf("pub_shipment_date")]."__".$row[csf("shiping_status")]."__".$row[csf("id")]."***";

					$po_arr[$row[csf('job_no')]]['order_quantity']+=$row[csf('order_quantity')];
					$po_arr[$row[csf('job_no')]]['order_quantity_set']+=$row[csf('order_quantity_set')];
					$po_id_string.=$row[csf('id')].",";
				}
				//print_r($style_data_all); die;
				unset($sql_po);
				$po_id_string=rtrim($po_id_string,",");
				if($po_id_string=="")
				{
					echo "<strong style='color:red;font-size:30px'>No Data found</strong>";
					die;
				}

				$poIds=chop($po_id_string,','); $order_cond=""; $order_cond1=""; $order_cond2=""; $precost_po_cond="";
				if($db_type==2 && $tot_rows>1000)
				{
					$order_cond=" and (";
					$order_cond1=" and (";
					$order_cond2=" and (";
					$precost_po_cond=" and (";
					$poIdsArr=array_chunk(explode(",",$poIds),999);
					foreach($poIdsArr as $ids)
					{
						$ids=implode(",",$ids);
						//$poIds_cond.=" po_break_down_id in($ids) or ";
						$order_cond.=" b.po_break_down_id in($ids) or";
						$order_cond1.=" b.po_breakdown_id in($ids) or";
						$order_cond2.=" d.po_breakdown_id in($ids) or";
						$precost_po_cond.=" c.po_break_down_id in($ids) or";
					}
					$order_cond=chop($order_cond,'or ');

					$order_cond.=")";
					$order_cond1=chop($order_cond1,'or ');
					$order_cond1.=")";
					$order_cond2=chop($order_cond2,'or ');
					$order_cond2.=")";
					$precost_po_cond=chop($precost_po_cond,'or ');
					$precost_po_cond.=")";
				}
				else
				{
					$order_cond=" and b.po_break_down_id in($poIds)";
					$order_cond1=" and b.po_breakdown_id in($poIds)";
					$order_cond2=" and d.po_breakdown_id in($poIds)";
					$precost_po_cond=" and c.po_break_down_id in($poIds)";
				}

				$condition= new condition();
				if(str_replace("'","",$txt_job_no) !=''){
					$condition->job_no_prefix_num("=$txt_job_no");
				}
				if(str_replace("'","",$txt_order_no)!='')
				{
					//$condition->po_number("=$txt_order_no");
					$order_nos=str_replace("'","",$txt_order_no);
					$condition->po_number(" like '%$order_nos%'");
				}

				if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
				{
					$start_date=(str_replace("'","",$txt_date_from));
					$end_date=(str_replace("'","",$txt_date_to));
				}

				if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
				{
					  $condition->country_ship_date(" between '$start_date' and '$end_date'");
				}
				$condition->init();
				$trim= new trims($condition);
				//$trim_qty=$trim->getQtyArray_by_orderAndPrecostdtlsid();
				$trim_qty=$trim->getQtyArray_by_jobAndPrecostdtlsid();
				//print_r($trim_qty);
				$trim= new trims($condition);
				$trim_amount=$trim->getAmountArray_by_jobAndPrecostdtlsid();
				$costing_arr=array();
				$sql_pre_cost=sql_select("select a.costing_per, a.costing_date, b.id as trim_dtla_id, b.trim_group, b.remark, b.description, b.brand_sup_ref, b.cons_uom, b.cons_dzn_gmts, b.rate, b.amount, b.apvl_req, b.nominated_supp, b.insert_date, c.cons, c.country_id, c.cons as cons_cal, c.po_break_down_id, b.job_no
				from wo_pre_cost_mst a, wo_pre_cost_trim_cost_dtls b, wo_pre_cost_trim_co_cons_dtls c
				where c.job_no=b.job_no and b.id=c.wo_pre_cost_trim_cost_dtls_id and a.job_no=b.job_no and c.cons>0 $trm_group_pre_cost_cond $precost_po_cond
				group by a.costing_per, a.costing_date, b.id, b.trim_group, b.remark, b.description, b.brand_sup_ref, b.cons_uom, b.cons_dzn_gmts, b.rate, b.amount, b.apvl_req, b.nominated_supp, b.insert_date, c.cons, c.pcs, c.country_id, c.po_break_down_id, b.job_no order by b.trim_group");

				if(count($sql_pre_cost)>0)
				{
					foreach($sql_pre_cost as $rowp)
					{
						$dzn_qnty=0;

						if($rowp[csf('costing_per')]==1) $dzn_qnty=12;
						else if($rowp[csf('costing_per')]==3) $dzn_qnty=12*2;
						else if($rowp[csf('costing_per')]==4) $dzn_qnty=12*3;
						else if($rowp[csf('costing_per')]==5) $dzn_qnty=12*4;
						else $dzn_qnty=1;

						$po_qty=0; $req_qnty=0; $req_value=0;
						if($rowp[csf('country_id')]==0)
						{
							$po_qty=$po_arr[$rowp[csf('job_no')]]['order_quantity'];
							//$po_qty=$po_arr[$rowp[csf('job_no')]]['order_quantity'];
							//$req_qnty+=$trim_qty[$rowp[csf('po_break_down_id')]][$rowp[csf('country_id')]][$rowp[csf('trim_dtla_id')]];
						}
						else
						{
							$country_id= explode(",",$rowp[csf('country_id')]);
							for($cou=0;$cou<=count($country_id); $cou++)
							{
								$po_qty+=$sql_po_qty_country_wise_arr[$rowp[csf('po_break_down_id')]][$country_id[$cou]];
								//$po_qty+=$sql_po_qty_country_wise_arr[$rowp[csf('po_break_down_id')]][$country_id[$cou]];
								//$req_qnty+=$trim_qty[$rowp[csf('po_break_down_id')]][$country_id[$cou]][$rowp[csf('trim_dtla_id')]];
							}
						}
						$req_qnty=$trim_qty[$rowp[csf('job_no')]][$rowp[csf('trim_dtla_id')]];
						$req_value=$trim_amount[$rowp[csf('job_no')]][$rowp[csf('trim_dtla_id')]];

						//$req_value=$trim_amount[$rowp[csf('po_break_down_id')]][$rowp[csf('trim_dtla_id')]];

						$style_data_arr[$rowp[csf('job_no')]]['trim_dtla_id'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('trim_dtla_id')];// for rowspan
						$style_data_arr[$rowp[csf('job_no')]]['trim_group'][$rowp[csf('trim_group')]]=$rowp[csf('trim_group')];
						$style_data_arr[$rowp[csf('job_no')]][$rowp[csf('trim_group')]][$rowp[csf('trim_dtla_id')]]=$rowp[csf('trim_dtla_id')]; // for rowspannn
						$style_data_arr[$rowp[csf('job_no')]]['trim_group_dtls'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('trim_group')];
						$style_data_arr[$rowp[csf('job_no')]]['remark'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('remark')];
						$style_data_arr[$rowp[csf('job_no')]]['brand_sup_ref'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('brand_sup_ref')];
						$style_data_arr[$rowp[csf('job_no')]]['apvl_req'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('apvl_req')];
						$style_data_arr[$rowp[csf('job_no')]]['insert_date'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('insert_date')];
						$style_data_arr[$rowp[csf('job_no')]]['req_qnty'][$rowp[csf('trim_dtla_id')]]=$req_qnty;
						$style_data_arr[$rowp[csf('job_no')]]['req_value'][$rowp[csf('trim_dtla_id')]]=$req_value;
						$style_data_arr[$rowp[csf('job_no')]]['cons_uom'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('cons_uom')];
						$style_data_arr[$rowp[csf('job_no')]]['trim_group_from'][$rowp[csf('trim_dtla_id')]]="Pre_cost";
						$style_data_arr[$rowp[csf('job_no')]]['rate'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('rate')];
						$style_data_arr[$rowp[csf('job_no')]]['description'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('description')];
						$style_data_arr[$rowp[csf('job_no')]]['country_id'][$rowp[csf('trim_dtla_id')]].=$rowp[csf('country_id')].',';
						$style_data_arr[$rowp[csf('job_no')]]['avg_cons'][$rowp[csf('trim_dtla_id')]]=$rowp[csf('cons_dzn_gmts')];

						$costing_arr[$rowp[csf('job_no')]]['costing_per']=$rowp[csf('costing_per')];
						$costing_arr[$rowp[csf('job_no')]]['costing_date']=$rowp[csf('costing_date')];
					}
				}
				else
				{
					echo "<strong style='color:red;font-size:30px'>No Data found</strong>";
					die;
				}
				unset($sql_pre_cost);

				if($db_type==2)
				{
					$sql_without_precost=sql_select("select min(a.booking_date) as booking_date ,b.job_no,LISTAGG(CAST(a.booking_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as booking_no, LISTAGG(CAST( a.supplier_id || '**' || a.pay_mode AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.booking_no) as supplier_id, b.po_break_down_id, b.trim_group,b.pre_cost_fabric_cost_dtls_id, sum(b.wo_qnty) as wo_qnty,sum(b.amount/b.exchange_rate) as amount,sum(b.rate) as rate from wo_booking_mst a, wo_booking_dtls b where a.item_category=4 and a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name $order_cond group by b.po_break_down_id,b.trim_group,b.job_no,b.pre_cost_fabric_cost_dtls_id");//and item_from_precost=2
				}
				else if($db_type==0)
				{
					$sql_without_precost=sql_select("select min(a.booking_date) as booking_date ,b.job_no,group_concat(a.booking_no) as booking_no, group_concat(concat_ws('**',a.supplier_id,a.pay_mode)) as supplier_id, b.po_break_down_id, b.trim_group,b.pre_cost_fabric_cost_dtls_id,sum(b.wo_qnty) as wo_qnty,sum(b.amount/b.exchange_rate) as amount,sum(b.rate) as rate from wo_booking_mst a, wo_booking_dtls b where a.item_category=4 and a.booking_no=b.booking_no  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name $order_cond  group by b.po_break_down_id,b.trim_group,b.job_no,b.pre_cost_fabric_cost_dtls_id");//and item_from_precost=2
				}
				$style_data_arr1=array();
				foreach($sql_without_precost as $row_precost)
				{
					$conversion_factor_rate=$conversion_factor_array[$row_precost[csf('trim_group')]]['con_factor'];
					//$cons_uom=$item_arr[$wo_row_without_precost[csf('trim_group')]]['order_uom'];
					$cons_uom=$conversion_factor_array[$row_precost[csf('trim_group')]]['cons_uom'];
					$booking_no=$row_precost[csf('booking_no')];
					$supplier_id=$row_precost[csf('supplier_id')];
					$wo_qnty=$row_precost[csf('wo_qnty')];//*$conversion_factor_rate;
					$amount=$row_precost[csf('amount')];
					$wo_date=$row_precost[csf('booking_date')];

					$job_no=''; $job_no=$row_precost[csf('job_no')];

					if($row_precost[csf('pre_cost_fabric_cost_dtls_id')] =="" || $row_precost[csf('pre_cost_fabric_cost_dtls_id')] ==0)
					{
						//echo $wo_row_without_precost[csf('trim_group')];
						$trim_dtla_id=max($style_data_arr[$row_precost[csf('job_no')]]['trim_dtla_id'])+1;
						$style_data_arr[$job_no]['trim_dtla_id'][$trim_dtla_id]=$trim_dtla_id;// for rowspan
						$style_data_arr[$job_no]['trim_group'][$row_precost[csf('trim_group')]]=$row_precost[csf('trim_group')];
						$style_data_arr[$job_no][$row_precost[csf('trim_group')]][$trim_dtla_id]=$trim_dtla_id;// for rowspannn
						$style_data_arr[$job_no]['trim_group_dtls'][$trim_dtla_id]=$row_precost[csf('trim_group')];
						$style_data_arr[$job_no]['cons_uom'][$trim_dtla_id]=$cons_uom;

						$style_data_arr[$job_no]['trim_group_from'][$trim_dtla_id]="Booking Without Pre_cost";
					}
					else
					{
						$trim_dtla_id=$row_precost[csf('pre_cost_fabric_cost_dtls_id')];
					}
					$style_data_arr[$job_no]['wo_qnty'][$trim_dtla_id]+=$wo_qnty;
					$style_data_arr[$job_no]['amount'][$trim_dtla_id]+=$amount;
					$style_data_arr[$job_no]['wo_date'][$trim_dtla_id]=$wo_date;
					$style_data_arr[$job_no]['wo_qnty_trim_group'][$row_precost[csf('trim_group')]]+=$wo_qnty;

					$style_data_arr2[$job_no]['booking_no'][$trim_dtla_id].=$booking_no.",";
					$style_data_arr[$job_no]['booking_no'][$trim_dtla_id].=$booking_no.",";
					$style_data_arr[$job_no]['supplier_id'][$trim_dtla_id].=$supplier_id.",";
					$style_data_arr[$job_no]['conversion_factor_rate'][$trim_dtla_id]=$conversion_factor_rate;
				}
				unset($sql_without_precost);

				$sql_rec_data=sql_select("select b.po_breakdown_id, a.item_group_id, c.receive_basis, a.booking_id, b.quantity as quantity, a.rate, c.exchange_rate, (b.quantity*d.avg_rate_per_unit) as amount from inv_receive_master c, product_details_master d, inv_trims_entry_dtls a, order_wise_pro_details b where a.mst_id=c.id and a.trans_id=b.trans_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_cond1 $trm_group_rec_cond order by a.item_group_id ");

				foreach($sql_rec_data as $row)
				{
					$poId=0; $poId=$row[csf('po_breakdown_id')];
					if($style_data_arr[$po_job_arr[$poId]]['trim_group'][$row[csf('item_group_id')]]=="" || $style_data_arr[$po_job_arr[$poId]]['trim_group'][$row[csf('item_group_id')]]==0)
					{
						$cons_uom=$conversion_factor_array[$row[csf('item_group_id')]]['cons_uom'];
						$trim_dtla_id=max($style_data_arr[$po_job_arr[$poId]]['trim_dtla_id'])+1;
						$style_data_arr[$po_job_arr[$poId]]['trim_dtla_id'][$trim_dtla_id]=$trim_dtla_id;// for rowspan
						$style_data_arr[$po_job_arr[$poId]]['trim_group'][$row[csf('item_group_id')]]=$row[csf('item_group_id')];
						$style_data_arr[$po_job_arr[$poId]][$row[csf('item_group_id')]][$trim_dtla_id]=$trim_dtla_id;// for rowspannn
						$style_data_arr[$po_job_arr[$poId]]['trim_group_dtls'][$trim_dtla_id]=$row[csf('item_group_id')];
						$style_data_arr[$po_job_arr[$poId]]['cons_uom'][$trim_dtla_id]=$cons_uom;

						$style_data_arr[$po_job_arr[$poId]]['trim_group_from'][$trim_dtla_id]="Trim Receive";
					}
					$style_data_arr[$po_job_arr[$poId]]['inhouse_qnty'][$row[csf('item_group_id')]]+=$row[csf('quantity')];
					$amount=0;  $amount=($row[csf('quantity')]*$row[csf('rate')]);//*$row[csf('exchange_rate')];
					$style_data_arr[$po_job_arr[$poId]]['inhouse_amount'][$row[csf('item_group_id')]]+=$amount;
				}
				unset($sql_rec_data);

				$receive_rtn_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id, d.quantity as quantity, c.avg_rate_per_unit as rate
				from product_details_master c,order_wise_pro_details d
				where d.prod_id=c.id and d.trans_type=3 and d.entry_form=49 and d.status_active=1 and d.is_deleted=0 $order_cond2");
				foreach($receive_rtn_qty_data as $row)
				{
					$receive_rtn_amt=0;
					//$conv_quantity=$row[csf('quantity')]/$conversion_factor_array[$row[csf('item_group_id')]]['con_factor'];
					$conv_quantity=$row[csf('quantity')];
					$receive_rtn_amt=$conv_quantity*$row[csf('rate')];
					$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['receive_rtn_qty'][$row[csf('item_group_id')]]+=$conv_quantity;
					$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['receive_rtn_amt'][$row[csf('item_group_id')]]+=$receive_rtn_amt;
				}
				//echo "<pre>";print_r($style_data_arr);
				unset($receive_rtn_qty_data);

				$transfer_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id, sum((case when d.trans_type=5 then d.quantity else 0 end)-(case when d.trans_type=6 then d.quantity else 0 end)) as quantity,
				sum(case when d.trans_type=5 then d.quantity else 0 end) as in_qty,
				sum(case when d.trans_type=6 then d.quantity else 0 end) as out_qty,
				sum(case when d.trans_type=5 then (d.quantity*c.avg_rate_per_unit) else 0 end) as in_amount,
				sum(case when d.trans_type=6 then (d.quantity*c.avg_rate_per_unit) else 0 end) as out_amount
				from product_details_master c,order_wise_pro_details d
				where d.prod_id=c.id and d.trans_type in(5,6) and d.entry_form=78 and d.status_active=1 and d.is_deleted=0 $order_cond2 group by d.po_breakdown_id, c.item_group_id");
				foreach($transfer_qty_data as $row)
				{
					$transfe_amount=0;
					$transfe_amount=$row[csf('in_amount')]-$row[csf('out_amount')];
					$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['transfe_qty'][$row[csf('item_group_id')]]+=$row[csf('quantity')];
					$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['transfe_in'][$row[csf('item_group_id')]]+=$row[csf('in_qty')];
					$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['transfe_out'][$row[csf('item_group_id')]]+=$row[csf('out_qty')];

					$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['transfe_amount'][$row[csf('item_group_id')]]+=$transfe_amount;
					$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['transfe_in_amt'][$row[csf('item_group_id')]]+=$row[csf('in_amount')];
					$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['transfe_out_amt'][$row[csf('item_group_id')]]+=$row[csf('out_amount')];
				}
				unset($transfer_qty_data);

				$issue_qty_data=sql_select("select b.po_breakdown_id, p.item_group_id,sum(b.quantity) as quantity, sum(b.quantity*b.order_rate) as issue_amount
			from inv_issue_master d, product_details_master p, inv_trims_issue_dtls a, order_wise_pro_details b
			where a.mst_id=d.id and a.prod_id=p.id and a.trans_id=b.trans_id and b.trans_type=2 and d.entry_form=25 and b.entry_form=25 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_cond1 $trm_group_iss_cond group by b.po_breakdown_id, p.item_group_id");
				foreach($issue_qty_data as $row)
				{
					$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['issue_qty'][$row[csf('item_group_id')]]+=$row[csf('quantity')];
					$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['issue_amount'][$row[csf('item_group_id')]]+=$row[csf('amount')];
				}

				unset($issue_qty_data);


				$issue_rtn_qty_data=sql_select("select d.po_breakdown_id, c.item_group_id, d.quantity as quantity, (d.quantity*c.avg_rate_per_unit) as amount
				from product_details_master c, order_wise_pro_details d
				where d.prod_id=c.id and d.trans_type=4 and d.entry_form=73 and d.status_active=1 and d.is_deleted=0 $order_cond2");
				foreach($issue_rtn_qty_data as $row)
				{
					$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['issue_rtn_qty'][$row[csf('item_group_id')]]+=$row[csf('quantity')];
					$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['issue_rtn_amt'][$row[csf('item_group_id')]]+=$row[csf('amount')];
				}

				unset($issue_rtn_qty_data);
				$sql_wo_pi=sql_select("select a.pi_number, b.work_order_no from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=4 and a.status_active=1 and a.importer_id=$company_name and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pi_number, b.work_order_no");
				$pi_arr=array();
				foreach($sql_wo_pi as $rowPi)
				{
					$pi_arr[$rowPi[csf('work_order_no')]].=$rowPi[csf('pi_number')].'**';
				}
				unset($sql_wo_pi);

				/*$sql_rec_rtn_data=sql_select("select d.po_breakdown_id, c.item_group_id,sum(d.quantity) as quantity   from  inv_issue_master a,inv_transaction b, product_details_master c, order_wise_pro_details d,inv_receive_master e  where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and e.id=a.received_id   and b.transaction_type=3 and a.entry_form=49 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $order_cond2  group by d.po_breakdown_id, c.item_group_id order by c.item_group_id");
				foreach($sql_rec_rtn_data as $row)
				{
					$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]]['receive_rtn_qty'][$row[csf('item_group_id')]]+=$row[csf('quantity')];
				}
				unset($sql_rec_rtn_data);

				$sql_issue_data=sql_select("select b.po_breakdown_id, a.item_group_id,sum(b.quantity) as quantity  from  inv_issue_master d,product_details_master p,inv_trims_issue_dtls a , order_wise_pro_details b where a.mst_id=d.id and a.prod_id=p.id and a.trans_id=b.trans_id and b.trans_type=2 and b.entry_form=25 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $order_cond1  group by b.po_breakdown_id, a.item_group_id");
				foreach($sql_issue_data as $row)
				{
					$style_data_arr[$po_job_arr[$row[csf('po_breakdown_id')]]][issue_qty][$row[csf('item_group_id')]]+=$row[csf('quantity')];
				}
				unset($sql_issue_data);*/

				$total_pre_costing_value=0;	$total_wo_value=0;$total_left_over_balanc=0;$total_issue_amount=0;$total_rec_bal_qnty=0;
				$summary_array=array();
				$i=1; $x=0;
				foreach($style_data_arr as $key=>$value)
				{
					$z=1;
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					foreach($value['trim_group'] as $key_trim=>$value_trim)
					{
						$y=1;
						$summary_array[trim_group][$key_trim]=$key_trim;
						foreach($value[$key_trim] as $key_trim1=>$value_trim1)
						{
							if($z==1) $style_color=''; else $style_color=$bgcolor."; border: none";
							$z++;

							if($y==1) $style_colory=''; else $style_colory=$bgcolor."; border: none";
							$x++; $y++;

							$job=$key; $buyer_name=''; $job_no_prefix_num=''; $style_ref_no=''; $order_uom='';
							$job_data=explode('##',$style_data[$job]['job_data']);
							$buyer_name=$job_data[0];
							$job_no_prefix_num=$job_data[1];
							$style_ref_no=$job_data[2];
							$order_uom=$job_data[3];

							$style_po_data=explode('***',$style_data_all[$job]);

							$file_no_all=""; $grouping_all=""; $po_no_all=""; $ship_date_all=""; $ship_status_all=""; $po_id_all='';
							foreach($style_po_data as $po_data)
							{
								$ex_po_data=explode('__',$po_data);

								if($file_no_all=="") $file_no_all=$ex_po_data[0]; else $file_no_all.=','.$ex_po_data[0];
								if($grouping_all=="") $grouping_all=$ex_po_data[1]; else $grouping_all.=','.$ex_po_data[1];
								if($po_no_all=="") $po_no_all=$ex_po_data[2]; else $po_no_all.=','.$ex_po_data[2];
								if($ship_date_all=="") $ship_date_all=change_date_format($ex_po_data[3]); else $ship_date_all.=','.change_date_format($ex_po_data[3]);
								if($ship_status_all=="") $ship_status_all=$ex_po_data[4]; else $ship_status_all.=','.$ex_po_data[4];
								if($po_id_all=="") $po_id_all=$ex_po_data[5]; else $po_id_all.=','.$ex_po_data[5];
							}

							$file_no=implode(',',array_filter(array_unique(explode(',',$file_no_all))));
							$grouping=implode(',',array_filter(array_unique(explode(',',$grouping_all))));
							$po_no=implode(',',array_filter(array_unique(explode(',',$po_no_all))));
							$ship_date=implode(',',array_filter(array_unique(explode(',',$ship_date_all))));
							$ship_status=implode(',',array_filter(array_unique(explode(',',$ship_status_all))));
							$poId_all=implode(',',array_filter(array_unique(explode(',',$po_id_all))));

							$po_qty=0; $po_qty_set=0;
							$po_qty=$po_arr[$job]['order_quantity'];
							$po_qty_set=$po_arr[$job]['order_quantity_set'];

							?>
							<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $x; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $x; ?>">
								<td width="30" style="word-break: break-all;color:<? echo $style_color; ?>"title="<? echo $po_qty; ?>" ><? echo $i; ?></td>
								<td width="50" style="word-break: break-all;color:<? echo $style_color; ?>"><? echo $buyer_short_name_library[$buyer_name]; ?></td>
								<td width="100" style="word-break: break-all;color:<? echo $style_color; ?>"align="center" ><? echo $job_no_prefix_num; ?></td>
								<td width="100" style="word-break: break-all;color:<? echo $style_color; ?>"><? echo $style_ref_no; ?></td>
								<td width="100" style="word-break: break-all;color:<? echo $style_color; ?>"><? echo $grouping; ?></td>
								<td width="100" style="word-break: break-all;color:<? echo $style_color; ?>"><? echo $file_no; ?></td>
								<td width="90" style="word-break: break-all;color:<? echo $style_color; ?>">
									<a style="word-break: break-all;color: <? echo $style_color; ?>" href='#report_details' onclick="generate_report('<? echo $company_name; ?>','<? echo $job; ?>','<? echo $buyer_name; ?>','<? echo $style_ref_no; ?>','<? echo change_date_format($costing_arr[$job]['costing_date']); ?>','<? echo $poId_all; ?>','<? echo $costing_arr[$job]['costing_per']; ?>','preCostRpt2');"><? $po_number=$po_no; $po_id=$poId_all; echo $po_number; ?></a></td>
								<td width="80" style="word-break: break-all;color: <? echo $style_color; ?>"align="right">
									<a style="word-break: break-all;color:<? echo $style_color; ?>" href='#report_details' onclick="order_qty_popup('<? echo $company_name; ?>','<? echo $job; ?>','<? echo $poId_all; ?>','<? echo $buyer_name; ?>',<? echo $txt_date_from; ?>,<? echo $txt_date_to; ?>,'order_qty_data');"><? echo number_format($po_qty_set,0,'.',''); ?></a></td>
								<td width="50" align="center" style="word-break: break-all;color:<? echo $style_color; ?>"><? echo $unit_of_measurement[$order_uom]; ?></td>
								<td width="80" align="right" style="word-break: break-all;color:<? echo $style_color; ?>"><? echo number_format($po_qty,0,'.',''); ?></td>
								<td width="80" style="word-break: break-all;color:<? echo $style_color; ?>"><? $pub_shipment_date=$ship_date; echo $pub_shipment_date; ?></td>
								<td width="100" title="<? echo $value['trim_group_from'][$key_trim1]; ?>"><p><? echo $item_library[$value['trim_group_dtls'][$key_trim1]]; ?></p></td>
                                <td width="140"><p><? echo $value['description'][$key_trim1]; ?></p></td>
								<td width="100"><p><? echo $value['brand_sup_ref'][$key_trim1]; ?></p></td>
								<td width="60" align="center"><p><? if($value['apvl_req'][$key_trim1]==1) echo "Yes"; else echo "&nbsp;"; ?></p></td>
								<td width="80" align="center"><p><?
									if($value['apvl_req'][$key_trim1]==1)
									{
										$app_status=$app_status_arr[$job][$value['trim_group_dtls'][$key_trim1]];
										$approved_status=$approval_status[$app_status];
										$summary_array[item_app][$key_trim][all]+=1;
										if($app_status==3)
										{
											$summary_array[item_app][$key_trim][app]+=1;
										}
									}
									else
									{
										$approved_status="";
									}
									echo $approved_status;
									$country_id=implode(',',array_filter(array_unique(explode(',',$value['country_id'][$key_trim1]))));
									?></p></td>
								<td width="100" align="center"><p><? $insert_date=explode(" ",$value['insert_date'][$key_trim1]); echo change_date_format($insert_date[0],'','',''); ?></p></td>
                                <td width="80" align="right"><?=number_format($insert_date[0],4); ?></td>
								<td width="100" align="right"><p>
									<a href='#report_details' onclick="order_req_qty_popup('<? echo $company_name; ?>','<? echo $job; ?>','<? echo $po_id; ?>', '<? echo $buyer_name; ?>','<? echo $value['rate'][$key_trim1]; ?>','<? echo $value['trim_group_dtls'][$key_trim1];?>','<? echo $value['booking_no'][$key_trim1];?>','<? echo $value['description'][$key_trim1] ;?>','<? echo rtrim($country_id,",");?>','<? echo $value['trim_dtla_id'][$key_trim1];?>','<? echo $start_date ?>','<? echo $end_date ?>','order_req_qty_data');">
									<? $req_qty=number_format($value['req_qnty'][$key_trim1],2,'.',''); echo $req_qty; $summary_array[req_qnty][$key_trim]+=$value['req_qnty'][$key_trim1]; ?></a></p></td>
									<?
									$wo_qnty=number_format($value['wo_qnty'][$key_trim1],2,'.','');


									if(($value['conversion_factor_rate'][$key_trim1]*$wo_qnty)==$req_qty) $color_wo="green";
									else if(($value['conversion_factor_rate'][$key_trim1]*$wo_qnty) > $req_qty) $color_wo="red";
									else if(($value['conversion_factor_rate'][$key_trim1]*$wo_qnty) < $req_qty ) $color_wo="yellow";
									else $color_wo="";

									$supplier_name_string="";
									$supplier_id_arr=array_unique(explode(',',rtrim($value['supplier_id'][$key_trim1],",")));

									foreach($supplier_id_arr as $supplier_id_arr_key=>$supplier_id_arr_value)
									{
										$ex_sup_data=explode("**",$supplier_id_arr_value);
										if($ex_sup_data[1]==3 || $ex_sup_data[1]==5) $suplier_name_arr=$company_library; else $suplier_name_arr=$lib_supplier_arr;
										$supplier_name_string.=$suplier_name_arr[$ex_sup_data[0]].",";
									}
									$booking_no_arr=array_unique(explode(',',rtrim($value['booking_no'][$key_trim1],",")));

									$main_booking_no_large_data=""; $piWoNo='';
									foreach($booking_no_arr as $booking_no1)
									{
										if($main_booking_no_large_data=="") $main_booking_no_large_data=$booking_no1; else $main_booking_no_large_data.=",".$booking_no1;
										if($booking_no1!="")
										{
											if($piWoNo=="") $piWoNo=implode(',',array_filter(array_unique(explode("**",$pi_arr[$booking_no1])))); else $piWoNo.=",".implode(',',array_filter(array_unique(explode("**",$pi_arr[$booking_no1]))));//
										}
									}

									?>
								<td width="90" align="right" title="<? echo 'conversion_factor='.$value['conversion_factor_rate'][$key_trim1];?>" bgcolor="<? echo $color_wo;?>"><p><a href='#report_details' onclick="openmypage('<? echo $po_id; ?>','<? echo $value['trim_group_dtls'][$key_trim1]; ?>','<? echo $job; ?>','<? echo $main_booking_no_large_data;?>','<? echo $value['trim_dtla_id'][$key_trim1];?>','booking_info');">
									<? echo number_format($value['wo_qnty'][$key_trim1],2,'.',''); $summary_array[wo_qnty][$key_trim]+=$value['wo_qnty'][$key_trim1]; ?></a></p></td>
								<td width="60" align="center"><p><? echo $unit_of_measurement[$item_arr[$value['trim_group_dtls'][$key_trim1]]['order_uom']]; $summary_array[cons_uom][$key_trim]= $item_arr[$value['trim_group_dtls'][$key_trim1]]['order_uom']; ?></p></td>

								<td width="100"><p><? echo number_format($req_qty-$wo_qnty,2,'.',''); ?></p></td>
								<td width="150"><p><? echo rtrim($supplier_name_string,','); ?></p></td>
								<td width="70" align="right" title="<? echo change_date_format($value['wo_date'][$key_trim1]);?>"><p>
									<? $tot=change_date_format($insert_date[0]);
									if($value['wo_qnty'][$key_trim1]<=0 )
									{
										$daysOnHand = datediff('d',$tot,$today);
									}
									else
									{
										$wo_date=$value['wo_date'][$key_trim1];
										$wo_date=change_date_format($wo_date);
										$daysOnHand = datediff('d',$tot,$wo_date);;
									}
									echo $daysOnHand; ?></p></td>
									<?
									$transfe_out=number_format($value['transfe_out'][$key_trim],2,'.','');
									$transfe_in=number_format($value['transfe_in'][$key_trim],2,'.','');
									$transfe_in_out=$transfe_in.' & '.$transfe_out;

									$transfe_out_amt=number_format($value['transfe_out'][$key_trim],2,'.','');
									$transfe_in_amt=number_format($value['transfe_in'][$key_trim],2,'.','');
									$transfe_in_out_amt=$transfe_in_amt.' & '.$transfe_out_amt;

									$inhouse_qnty=($value['inhouse_qnty'][$key_trim]+$value['transfe_qty'][$key_trim])-$value['receive_rtn_qty'][$key_trim];
									$inhouse_amount=($value['inhouse_amount'][$key_trim]+$value['transfe_amount'][$key_trim])-$value['receive_rtn_amt'][$key_trim];
									$total_inhouse_value+=$inhouse_amount;
									$balance=$value['wo_qnty_trim_group'][$key_trim]-$inhouse_qnty;
									$issue_qnty=$value['issue_qty'][$key_trim]-$value['issue_rtn_qty'][$key_trim];
									$issue_amount=$value['issue_amount'][$key_trim]-$value['issue_rtn_amt'][$key_trim];
									$left_overqty=$inhouse_qnty-$issue_qnty;
									$left_overamt=$inhouse_amount-$issue_amount;
									$summary_array['inhouse_qnty'][$key_trim]+=$inhouse_qnty;
									$summary_array['inhouse_qnty_bl'][$key_trim]+=$balance;
									$summary_array['issue_qty'][$key_trim]+=$issue_qnty;
									$summary_array['left_overqty'][$key_trim]+=$left_overqty;
									?>
                                <td width="90" tyle="word-break: break-all;color: <? echo $style_colory ?>"><p><? echo $piWoNo; ?> </p></td>
								<td width="90" style="word-break: break-all;color: <? echo $style_colory ?>" align="right" title="<? echo "Inhouse-Qty: ".number_format($value['inhouse_qnty'][$key_trim]-$value['receive_rtn_qty'][$key_trim],2,'.','')."\n Transfer In & Out Qty: ".$$transfe_in_out."\n Return Qty: ".number_format($value['receive_rtn_qty'][$key_trim],2,'.',''); ?>"><a  style="word-break: break-all;color: <? echo $style_colory ?>" href='#report_details' onclick="openmypage_inhouse('<? echo $po_id; ?>','<? echo $value['trim_group_dtls'][$key_trim1]; ?>','booking_inhouse_info');"><? echo number_format($inhouse_qnty,2,'.',''); ?></a></td>
								<td width="90" title="<? echo "Issue-Qty: ".number_format($value['issue_qty'][$key_trim],2,'.','')."\n Issue Return Qty: ".number_format($value['issue_rtn_qty'][$key_trim],2,'.',''); ?>" style="word-break: break-all;color: <? echo $style_colory ?>" align="right" ><a  style="word-break: break-all;color: <? echo $style_colory ?>" href='#report_details' onclick="openmypage_issue('<? echo $po_id; ?>','<? echo $value['trim_group_dtls'][$key_trim1]; ?>','booking_issue_info');"><? echo number_format($issue_qnty,2,'.',''); ?></a></td>
								<td width="90" align="right" style="word-break: break-all;color: <? echo $style_colory ?>"><? echo number_format($left_overqty,2,'.',''); ?></td>
							</tr>
						<?
						}// end  foreach($value[$key_trim] as $key_trim1=>$value_trim1)
					}
					$i++;
				}
			?>
			</table>
            </div>
			<table class="rpt_table" width="2550" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tfoot>
					<th width="30">&nbsp;</th>
					<th width="50">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="90">&nbsp;</th>
					<th width="80" align="right" id="total_order_qnty"><? //echo number_format($total_order_qnty,0); ?></th>
					<th width="50">&nbsp;</th>
					<th width="80" align="right" id="total_order_qnty_in_pcs"><? //echo number_format($total_order_qnty_in_pcs,0); ?></th>
					<th width="80">&nbsp;</th>
					<th width="100">&nbsp;</th>
                    <th width="140">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="60">&nbsp;</th>
					<th width="80">&nbsp;</th>
					<th width="100">&nbsp;</th>
                    <th width="80">&nbsp;</th>
					<th width="100" align="right" id="value_req_qnty"><? //echo number_format($total_req_qnty,2); ?></th>
					<th width="90" align="right" id=""><? //echo number_format($total_wo_qnty,2); ?></th>
					<th width="60" align="right" ></th>
					<th width="100">&nbsp;</th>
					<th width="150" align="right" id=""></th>
					<th width="70" align="right"><p><? //echo number_format($req_value,2,'.',''); ?>&nbsp;</p></th>
                    <th width="90">&nbsp;</th>
					<th width="90" align="right" id="value_in_qty"><? //echo number_format($total_in_qnty,2); ?></th>
                    
					<th width="90" align="right" id="value_issue_qty"><? //echo number_format($total_issue_qnty,2); ?></th>
                    
					<th width="90" align="right" id="value_leftover_qty"><? //echo number_format($total_leftover_qnty,2); ?></th>
                
				</tfoot>
			</table>

			<table>
				<tr><td height="15"></td></tr>
			</table>
			<u><b>Summary</b></u>
			<table class="rpt_table" width="1200" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<th width="30">SL</th>
					<th width="110">Item</th>
					<th width="60">UOM</th>
					<th width="80">Approved %</th>
					<th width="110">Req Qty</th>
					<th width="110">WO Qty</th>
					<th width="80">WO %</th>
					<th width="110">In-House Qty</th>
					<th width="80">In-House %</th>
					<th width="110">In-House Balance Qty</th>
					<th width="110">Issue Qty</th>
					<th width="80">Issue %</th>
					<th>Left Over</th>
				</thead>
				<?
				$z=1; $tot_req_qnty_summary=0;
				foreach($summary_array[trim_group] as $key_trim=>$value)
				{
					if($z%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr2_<? echo $z; ?>','<? echo $bgcolor;?>')" id="tr2_<? echo $z; ?>">
						<td width="30"><? echo $z; ?></td>
						<td width="110"><p><? echo $item_library[$key_trim]; ?></p></td>
						<td width="60" align="center"><? echo $unit_of_measurement[$summary_array[cons_uom][$key_trim]]; ?></td>
						<td width="80" align="right"><? $app_perc=($summary_array[item_app][$key_trim][app]*100)/$summary_array[item_app][$key_trim][all];  if ($app_perc>=0)echo number_format($app_perc,2); ?></td>
						<td width="110" align="right"><? echo number_format($summary_array[req_qnty][$key_trim],2); ?></td>
						<td width="110" align="right"><? echo number_format( $summary_array[wo_qnty][$key_trim],2); ?></td>
						<td width="80" align="right"><? $wo_per= $summary_array[wo_qnty][$key_trim]/$summary_array[req_qnty][$key_trim]*100; echo number_format($wo_per,2).'%'; ?></td>
						<td width="110" align="right"><? echo number_format($summary_array[inhouse_qnty][$key_trim],2); ?></td>
						<td width="80" align="right"><? $in_per=$summary_array[inhouse_qnty][$key_trim]/$summary_array[wo_qnty][$key_trim]*100; echo number_format($in_per,2).'%'; ?></td>
						<td width="110" align="right"><? echo number_format($summary_array[inhouse_qnty_bl][$key_trim],2); $in_house_bal+=($value['wo']-$value['in']); ?></td>
						<td width="110" align="right"><? echo number_format($summary_array[issue_qty][$key_trim],2); ?></td>
						<td width="80" align="right"><? $wo_per=$summary_array[issue_qty][$key_trim]/$summary_array[wo_qnty][$key_trim]*100; echo number_format($wo_per,2).'%'; ?></td>
						<td align="right"><? echo number_format($summary_array[left_overqty][$key_trim],2); ?></td>
					</tr>
					<?
					$z++;
				}
				?>
				<tfoot>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th align="right"><? //echo number_format($tot_req_qnty_summary,2); ?>&nbsp;</th>
					<th align="right"><? //echo number_format($tot_wo_qnty_summary,2); ?>&nbsp;</th>
					<th>&nbsp;</th>
					<th align="right"><? //echo number_format($tot_in_qnty_summary,2); ?>&nbsp;</th>
					<th>&nbsp;</th>
					<th align="right"><? //echo number_format($in_house_bal,2); ?>&nbsp;</th>
					<th align="right"><? //echo number_format($tot_issue_qnty_summary,2); ?>&nbsp;</th>
					<th>&nbsp;</th>
					<th align="right"><? //echo number_format($tot_leftover_qnty_summary,2); ?>&nbsp;</th>
				</tfoot>
			</table>
			</fieldset>
			</div>
		<?
		}
	}

	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****$filename****$tot_rows****2";
	exit();
}


if($action=="booking_info")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

 <script>
	function generate_trim_report(action,txt_booking_no,cbo_company_name,id_approved_id,cbo_isshort)
	{
			var show_comment='';
			var r=confirm("Press  \"Cancel\"  to hide  comments\nPress  \"OK\"  to Show comments");
			if (r==true) show_comment="1"; else show_comment="0";
			var report_title="";
			var fabric_nature = <? echo $fabric_nature ?>;
			if(cbo_isshort==1)
			{
				report_title="Short Trims Booking [Multiple Order]";
			}
			else
			{
				report_title="Multi Job Wise Trim Booking";
			}
			//var report_title='';
			var data="action="+action+'&report_title='+"'"+report_title+'&txt_booking_no='+"'"+txt_booking_no+"'"+'&cbo_company_name='+cbo_company_name+'&id_approved_id='+id_approved_id+'&report_type=1&link=1';
			//freeze_window(5);
			if(fabric_nature == 3)
			{
				if(cbo_isshort==1)
				{
					http.open("POST","../../woven_gmts/requires/short_trims_booking_multi_job_controllerurmi.php",true);
				}
				else
				{
					http.open("POST","../../woven_gmts/requires/trims_booking_multi_job_controllerurmi.php",true);
				}
			}
			else
			{
				if(cbo_isshort==1)
				{
					http.open("POST","../../woven_order/requires/short_trims_booking_multi_job_controllerurmi.php",true);
				}
				else
				{
					http.open("POST","../../woven_order/requires/trims_booking_multi_job_controllerurmi.php",true);
				}
			}
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_trim_report_reponse;

	}


	function generate_trim_report_reponse()
	{
		if(http.readyState == 4)
		{
			$('#data_panel').html( http.responseText );
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
			d.close();
		}
	}


 </script>
	<!--<div style="width:880px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
	<fieldset style="width:870px; margin-left:3px">
		<div id="scroll_body" align="center">
        <table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
        <tr>
        <td align="center" colspan="9"><strong>WO Summary</strong> </td>
         </tr>
        </table>
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="20">Sl</th>
                    <th width="100">Wo No</th>
                    <th width="60">Wo Type</th>
                    <th width="60">Wo Date</th>
                    <th width="100">Country</th>
                    <th width="200">Item Description</th>
                    <th width="80">Wo Qty</th>
                    <th width="60">UOM</th>
                    <th>Supplier</th>
				</thead>
                <tbody>
                <?
				$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
				$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );

				$conversion_factor_array=array();

				$conversion_factor=sql_select("select id ,conversion_factor from  lib_item_group ");
				foreach($conversion_factor as $row_f)
				{
					$conversion_factor_array[$row_f[csf('id')]]['con_factor']=$row_f[csf('conversion_factor')];
				}

				$i=1;
				$country_arr_data=array();
				$sql_data=sql_select("select c.country_id,c.po_break_down_id,c.job_no_mst from wo_po_color_size_breakdown c  where c.po_break_down_id in($po_id) and c.status_active=1 and c.is_deleted=0 group by c.country_id,c.po_break_down_id,c.job_no_mst  ");
				foreach($sql_data as $row_c)
				{
					$country_arr_data[$row_c[csf('po_break_down_id')]][$row_c[csf('job_no_mst')]]['country']=$row_c[csf('country_id')];
				}

				$item_description_arr=array();
				$wo_sql_trim=sql_select("select b.id,b.item_color,b.job_no, b.po_break_down_id, b.description,b.brand_supplier,b.item_size from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id=b.wo_trim_booking_dtls_id and a.pre_cost_fabric_cost_dtls_id=$trim_dtla_id and a.is_deleted=0 and a.status_active=1 and a.job_no=b.job_no  group by b.id,b.po_break_down_id,b.job_no,b.description,b.brand_supplier,b.item_size,b.item_color");
				foreach($wo_sql_trim as $row_trim)
				{
					$item_description_arr[$row_trim[csf('po_break_down_id')]][$row_trim[csf('job_no')]][$trim_dtla_id]['description']=$row_trim[csf('description')];
				}

				$boking_cond="";
				$booking_no= explode(',',$book_num);
				foreach($booking_no as $book_row)
				{
					if($boking_cond=="") $boking_cond="and a.booking_no in('$book_row'"; else  $boking_cond .=",'$book_row'";

				}
				if($boking_cond!="")$boking_cond.=")";
				$wo_sql="select a.is_short, a.is_approved as is_approved, a.booking_no, a.booking_date, a.pay_mode, a.supplier_id, b.job_no, b.country_id_string, b.po_break_down_id, sum(b.wo_qnty) as wo_qnty, b.uom from wo_booking_mst a, wo_booking_dtls b
				where a.item_category=4 and a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1
				and b.status_active=1 and b.is_deleted=0 and  b.job_no='$job_no' and b.trim_group=$item_name and b.po_break_down_id in($po_id) and b.pre_cost_fabric_cost_dtls_id=$trim_dtla_id $boking_cond group by a.is_short, a.is_approved, b.po_break_down_id, b.job_no, a.booking_no, a.booking_date, a.pay_mode, a.supplier_id, b.uom, b.country_id_string";
				$dtlsArray=sql_select($wo_sql);

				$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$cbo_company_name."' and module_id=2 and report_id in(5,6) and is_deleted=0 and status_active=1");

				$report= max(explode(',',$print_report_format));

				if($report==13){$reporAction="show_trim_booking_report";}
				elseif($report==14){$reporAction="show_trim_booking_report1";}
				elseif($report==15){$reporAction="show_trim_booking_report2";}
				elseif($report==16){$reporAction="show_trim_booking_report3";}

				foreach($dtlsArray as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$description=$item_description_arr[$row[csf('po_break_down_id')]][$row[csf('job_no')]][$trim_dtla_id]['description'];
					$conversion_factor_rate=$conversion_factor_array[$item_name]['con_factor'];
					$country_arr_data=explode(',',$row[csf('country_id_string')]);
					$country_name_data="";
					foreach($country_arr_data as $country_row)
					{
						if($country_name_data=="") $country_name_data=$country_name_library[$country_row]; else $country_name_data.=",".$country_name_library[$country_row];
					}
					$wo_type=''; $action_name="";
					if($fabric_nature == 3)
					{
						if($row[csf('is_short')]==1)
						{
							$wo_type="Short";
							$action_name="show_trim_booking_report";
						}
						else
						{
							$wo_type="Main";
							$action_name="show_trim_booking_report";
						}
					}
					else
					{
						if($row[csf('is_short')]==1)
						{
							$wo_type="Short";
							$action_name="show_trim_booking_report2";
						}
						else
						{
							$wo_type="Main";
							$action_name="show_trim_booking_report2";
						}
					}
					$supplier_name_str="";
					if($row[csf('pay_mode')]==3 || $row[csf('pay_mode')]==5) $supplier_name_str=$company_arr[$row[csf('supplier_id')]]; else $supplier_name_str=$supplier_arr[$row[csf('supplier_id')]];
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td width="20"><p><? echo $i; ?></p></td>
						<td width="100"><p><a href="#" onClick="generate_trim_report('<? echo $action_name; ?>','<? echo $row[csf('booking_no')]; ?>',<? echo $cbo_company_name; ?>,<? echo $row[csf('is_approved')]; ?>,<? echo $row[csf('is_short')]; ?>)"><? echo $row[csf('booking_no')]; ?></a></p></td>
						<td width="60"><p><? echo $wo_type; ?></p></td>
						<td width="60"><p><? echo change_date_format($row[csf('booking_date')]); ?></p></td>
						<td width="100"><p><? echo $country_name_data; ?></p></td>
						<td width="200"><p><?  echo $description; ?></p></td>
						<td width="80" align="right" title="<? echo 'conversion_factor='.$conversion_factor_rate; ?>"><p><? echo number_format($row[csf('wo_qnty')],2); ?></p></td>
						<td width="60" align="center" ><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
						<td><p><? echo $supplier_name_str; ?></p></td>
					</tr>
					<?
					$tot_qty+=$row[csf('wo_qnty')];
					$i++;
				}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                   		 <td colspan="6" align="right">Total</td>
                    	<td  align="right"><? echo number_format($tot_qty,2); ?></td>
                        <td align="right">&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div style="display:none" id="data_panel"></div>
    </fieldset>
    <script type="text/javascript" src="../../../js/jquery.js"></script>
    <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
    <?
	exit();
}
//disconnect($con);
if($action=="booking_inhouse_info")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<!--<div style="width:880px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
	<fieldset style="width:870px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="30">Sl</th>
                    <th width="60">Prod. ID</th>
                    <th width="100">Recv. ID</th>
                    <th width="100">Wo/Pi No</th>
                    <th width="100">Chalan No</th>
                    <th width="70">Recv. Date</th>
                    <th width="150">Item Description.</th>
                    <th width="80">Recv. Qty.</th>
                    <th>Reject Qty.</th>
				</thead>
                <tbody>
                <?
					$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
					$bookingNoArr=return_library_array( "select id, booking_no from  wo_booking_mst", "id", "booking_no");
					$piArr=return_library_array("select id, pi_number from com_pi_master_details", "id", "pi_number");
					$i=1;

					$item_arr=array();
					$conversion_factor=sql_select("select id,conversion_factor,order_uom from lib_item_group where status_active=1  ");
					foreach($conversion_factor as $row_f)
					{
						$item_arr[$row_f[csf('id')]]['order_uom']=$row_f[csf('conversion_factor')];
					}
					unset($conversion_factor);

					$receive_rtn_data=array();
					//echo "select a.issue_number, a.issue_date, e.id, d.po_breakdown_id, c.item_group_id, sum(d.quantity) as quantity from  inv_issue_master a,inv_transaction b, product_details_master c, order_wise_pro_details d,inv_receive_master e  where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and e.id=a.received_id   and b.transaction_type=3 and a.entry_form=49 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and d.po_breakdown_id in($po_id) and c.item_group_id='$item_name' group by a.issue_number,a.issue_date,e.id,d.po_breakdown_id, c.item_group_id order by c.item_group_id";die;



					$receive_qty_data="select a.id, c.po_breakdown_id, a.receive_basis, b.booking_id, b.item_group_id, b.prod_id as prod_id, a.challan_no, b.item_description, a.recv_number, a.receive_date, SUM(c.quantity) as quantity, sum(reject_receive_qnty) as reject_receive_qnty
					from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c , product_details_master d
					where a.id=b.mst_id  and a.entry_form=24 and  a.item_category=4  and b.id=c.dtls_id and b.prod_id=d.id and b.trans_id=c.trans_id and c.trans_type=1 and  c.po_breakdown_id in($po_id)  and b.item_group_id='$item_name' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by  c.po_breakdown_id, b.item_group_id, a.receive_basis, b.booking_id, b.prod_id, a.id, b.item_description, a.recv_number, a.challan_no, a.receive_date";

					$dtlsArray=sql_select($receive_qty_data);

					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$qty=0;
						$qty=$row[csf('quantity')];

						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="60"><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="100" align="center"><p><? $piwo_no='';
							if($row[csf('receive_basis')]==1)
							{
								$piwo_no=$piArr[$row[csf('booking_id')]];
							}
							else if($row[csf('receive_basis')]==2)
							{
								$piwo_no=$bookingNoArr[$row[csf('booking_id')]];
							}
							echo $piwo_no; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="70" align="center"><p><? echo  change_date_format($row[csf('receive_date')]); ?></p></td>
                            <td width="150" align="center"><p><? echo $row[csf('item_description')]; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($qty,2); ?></p></td>
                            <td align="right"><p><? echo number_format($row[csf('reject_receive_qnty')],2); ?></p></td>
                        </tr>
						<?
						$tot_qty+=$qty;
						$tot_rej_qty+=$row[csf('reject_receive_qnty')];
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="6" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_qty,2); ?></td>
                         <td><? echo number_format($tot_rej_qty,2); ?></td>
                    </tr>
                </tfoot>
            </table>
            <?
			$transfer_qty_data=sql_select("select a.transfer_system_id, a.transfer_date, d.po_breakdown_id, c.item_group_id, d.trans_type, d.quantity as quantity, b.prod_id, c.item_description
					from  inv_item_transfer_mst a, inv_transaction b, product_details_master c, order_wise_pro_details d
					where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and a.transfer_criteria=4 and a.item_category=4 and b.item_category=4 and b.transaction_type in(5,6) and d.trans_type in(5,6) and d.entry_form=78 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.po_breakdown_id in($po_id) and c.item_group_id='$item_name'");
			?>
            <table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="30">Sl</th>
                    <th width="80">Prod. ID</th>
                    <th width="100">Transfer. ID</th>
                    <th width="100">Transfer Type</th>
                    <th width="100">Transfer Date</th>
                    <th width="80">Item Description.</th>
                    <th width="160">Return Qty.</th>
				</thead>
                <tbody>
                <?
					/*echo "select a.transfer_system_id, a.transfer_date, d.po_breakdown_id, c.item_group_id, sum((case when b.transaction_type=5 and d.trans_type=5 then d.quantity else 0 end)-(case when b.transaction_type=6 and d.trans_type=6 then d.quantity else 0 end)) as quantity, b.prod_id, c.item_description
					from  inv_item_transfer_mst a, inv_transaction b, product_details_master c, order_wise_pro_details d
					where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and a.transfer_criteria=4 and a.item_category=4 and b.item_category=4 and b.transaction_type in(5,6) and d.trans_type in(5,6) and d.entry_form=78 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.po_breakdown_id in($po_id) and c.item_group_id='$item_name'
					group by a.transfer_system_id, a.transfer_date, d.po_breakdown_id, c.item_group_id, b.prod_id, c.item_description";die;*/


					foreach($transfer_qty_data as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";

						$qty=0;
						$qty=$row[csf('quantity')];

						if($row[csf('trans_type')]==5)
						{
							$trans_type="Transfer In";
							$trans_in_qnty+=$qty;

						}
						else
						{
							$trans_type="Transfer Out";
							$trans_out_qnty+=$qty;
						}

						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="80"><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $trans_type; ?></p></td>
                            <td width="100" align="center"><p><? echo  change_date_format($row[csf('transfer_date')]); ?></p></td>
                            <td width="80" align="center"><p><? echo $row[csf('item_description')]; ?></p></td>
                            <td width="160" align="right"><p><? echo number_format($qty,2); ?></p></td>
                        </tr>
						<?
						$tot_trans_qty+=$qty;
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="5" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_trans_qty,2); ?></td>
                    </tr>
            </table>

            <table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
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
                        <td align="right">Balance</td>
                        <td><? $balance_qnty=($tot_qty+$trans_in_qnty)-($tot_rtn_qty+$trans_out_qnty); echo number_format($balance_qnty,2); ?></td>
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
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
<!--	<div style="width:880px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
-->	<fieldset style="width:870px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="30">Sl</th>
                    <th width="80">Prod. ID</th>
                    <th width="100">Issue. ID</th>
                     <th width="100">Chalan No</th>
                     <th width="100">Issue. Date</th>
                    <th width="80">Item Description.</th>
                    <th width="100">Issue. Qty.</th>
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
					//$wo_sql="select a.item_group_id,a.prod_id,b.recv_number,b.receive_date,a.item_description,sum(a.cons_qnty) as cons_qnty  from inv_receive_master b, inv_trims_entry_dtls a where b.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category=4 group by a.item_group_id,a.prod_id,b.recv_number,b.receive_date,a.item_description";

				 $mrr_sql=("select a.id, a.issue_number,a.challan_no,p.item_group_id,b.prod_id, a.issue_date,b.item_description,SUM(c.quantity) as quantity
					from  inv_issue_master a,inv_trims_issue_dtls b, order_wise_pro_details c,product_details_master p
					where a.id=b.mst_id  and a.entry_form=25 and p.id=b.prod_id and b.id=c.dtls_id and b.trans_id=c.trans_id and c.trans_type=2 and a.is_deleted=0 and a.status_active=1 and
					b.status_active=1 and b.is_deleted=0 and  c.po_breakdown_id in($po_id) and p.item_group_id='$item_name' group by c.po_breakdown_id,p.item_group_id,b.item_description,a.issue_number,a.id,a.issue_date,b.prod_id,a.challan_no ");

					$dtlsArray=sql_select($mrr_sql);

					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$conv_fact=$conversion_factor_array[$row[csf('item_group_id')]]['con_factor'];
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="80" align="center"><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td width="100"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="100" align="center"><p><? echo  change_date_format($row[csf('issue_date')]); ?></p></td>
                            <td width="80" align="center"><p><? echo $row[csf('item_description')]; ?></p></td>
                            <td width="100" align="right"><p><? echo number_format($row[csf('quantity')],2); //echo number_format($row[csf('quantity')]/$conv_fact,2); ?></p></td>
                        </tr>
						<?
						$tot_qty+=$row[csf('quantity')];
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

            <table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
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
					$issue_rtn_qty_data=sql_select("select a.recv_number, a.receive_date , d.po_breakdown_id, c.item_group_id, d.quantity as quantity, b.prod_id, c.item_description
					from inv_receive_master a, inv_transaction b, product_details_master c, order_wise_pro_details d
					where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and b.transaction_type=4 and d.trans_type=4 and a.entry_form=73 and d.entry_form=73 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and d.po_breakdown_id in($po_id) and c.item_group_id='$item_name'");

					foreach($issue_rtn_qty_data as $row)
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
                            <td width="100" align="center"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="100" align="center"><p><? echo  change_date_format($row[csf('receive_date')]); ?></p></td>
                            <td width="80" align="center"><p><? echo $row[csf('item_description')]; ?></p></td>
                            <td width="160" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                        </tr>
						<?
						$tot_rtn_qty+=$row[csf('quantity')];
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
                        <td align="right">Balance</td>
                        <td><? $balance_qnty=($tot_qty-$tot_rtn_qty); echo number_format($balance_qnty,2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}

if($action=="order_qty_data")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$country_name_library=return_library_array( "select id, country_name from lib_country", "id", "country_name"  );
	//echo $po_id; die;
	?>
<!--	<div style="width:780px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
-->	<fieldset style="width:770px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="750" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="30">Sl</th>
                    <th width="80">Buyer Name</th>
                    <th width="100">Order No</th>
                   <th width="100">Country</th>
                    <th width="80">Order Qty. (PCS)</th>

				</thead>
                <tbody>
                <?
					$date_cond='';
					if(str_replace("'","",$from_date)!="" && str_replace("'","",$to_date)!="")
					{
						$start_date=(str_replace("'","",$from_date));
						$end_date=(str_replace("'","",$to_date));
						$date_cond="and c.country_ship_date between '$start_date' and '$end_date'";
					}
					$i=1;
					$order_arr=return_library_array( "select id, po_number from wo_po_break_down where id in($po_id)", "id", "po_number"  );

				 	$gmt_item_id=return_field_value("item_number_id", "wo_po_color_size_breakdown", "po_break_down_id='$po_id'");
					$country_id=return_field_value("country_id", "wo_po_color_size_breakdown", "po_break_down_id='$po_id'");
					 //echo $gmt_item_id;
					$sql_po_qty=sql_select("select sum(c.order_quantity) as order_quantity,c.country_id  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id in($po_id) and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,c.country_id ");
					list($sql_po_qty_row)=$sql_po_qty;
					$po_qty=$sql_po_qty_row[csf('order_quantity')];
					//echo "select sum(c.order_quantity) as order_quantity,c.country_id  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id in($po_id) and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,c.country_id ";

					$sql=" select sum( c.order_quantity) as po_quantity, c.country_id, c.po_break_down_id from wo_po_color_size_breakdown c where c.po_break_down_id in($po_id) and c.status_active=1 and c.is_deleted=0 $date_cond group by c.country_id,c.po_break_down_id";
					//echo $sql;
					$dtlsArray=sql_select($sql);

					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="80" align="center"><p><? echo $buyer_short_name_library[$buyer]; ?></p></td>
                            <td width="100"><p><? echo $order_arr[$row[csf('po_break_down_id')]]; ?></p></td>
                             <td width="100" align="center"><p><? echo $country_name_library[$row[csf('country_id')]]; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row[csf('po_quantity')],2); ?></p></td>
                        </tr>
						<?
						$tot_qty+=$row[csf('po_quantity')];
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="3" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_qty,2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}
//disconnect($con);

if($action=="order_req_qty_data")
{
	/*echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$country_name_library=return_library_array( "select id, country_name from lib_country", "id", "country_name"  );
	?>
<!--	<div style="width:680px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
-->	<fieldset style="width:670px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="30">Sl</th>
                    <th width="80">Buyer Name</th>
                    <th width="100">Order No</th>
                     <th width="100">Item Description</th>
                     <th width="100">Country</th>
                    <th width="80">Req. Qty.</th>
                    <th width="">Req. Rate</th>
				</thead>
                <tbody>
                <?

					// $gmt_item_id=return_field_value("item_number_id", "wo_po_color_size_breakdown", "po_break_down_id='$po_id'");
					 //$country_id=return_field_value("country_id", "wo_po_color_size_breakdown", "po_break_down_id='$po_id'");
					 //$sql_po_qty=sql_select("select sum(c.order_quantity) as order_quantity,c.country_id  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id='".$po_id."' and c.item_number_id=' $gmt_item_id' and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,c.country_id ");
					//list($sql_po_qty_row)=$sql_po_qty;
					//$po_qty=$sql_po_qty_row[csf('order_quantity')];


					$order_arr=return_library_array( "select id, po_number from wo_po_break_down where id in($po_id)", "id", "po_number"  );
					$req_arr=array();
					$red_data=sql_select("select a.id,a.job_no,a.cons, a.po_break_down_id  from wo_pre_cost_trim_co_cons_dtls a , wo_pre_cost_trim_cost_dtls b where b.id=a.wo_pre_cost_trim_cost_dtls_id and b.trim_group=$item_group and a.job_no='$job_no' and a.po_break_down_id in($po_id) and b.id=$trim_dtla_id");
					foreach($red_data as $row_data)
					{
					$req_arr[$row_data[csf('po_break_down_id')]][$row_data[csf('job_no')]]['cons']=$row_data[csf('cons')];
					}
					//print_r($req_arr);

					$wo_sql_trim=sql_select("select b.id,b.job_no, b.po_break_down_id, b.description from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id=b.wo_trim_booking_dtls_id and a.is_deleted=0 and a.status_active=1 and a.job_no=b.job_no  group by b.id,b.po_break_down_id,b.job_no,b.description ");
					foreach($wo_sql_trim as $row_trim)
					{
					$item_description_arr[$row_trim[csf('po_break_down_id')]][$row_trim[csf('job_no')]]['job_no']=$row_trim[csf('job_no')];
					$item_description_arr[$row_trim[csf('po_break_down_id')]][$row_trim[csf('job_no')]]['description']=$row_trim[csf('description')];
					}

				/*$fabriccostArray=sql_select("select costing_per_id, trims_cost, embel_cost, cm_cost, commission, common_oh, lab_test, inspection, freight, comm_cost from wo_pre_cost_dtls where job_no='".$job_no."' and status_active=1 and is_deleted=0");

                       	$costing_per_id=return_field_value( "costing_per", "wo_pre_cost_mst","job_no ='$job_no'");
						if($start_date !="" && $end_date!="")
						{
						$date_cond="and c.country_ship_date between '$start_date' and '$end_date'";
						}
						else
						{
						$date_cond="";
						}

					   $dzn_qnty=0;
                        if(	$costing_per_id==1)
                        {
                            $dzn_qnty=12;
                        }
                        else if($costing_per_id==3)
                        {
                            $dzn_qnty=12*2;
                        }
                        else if($costing_per_id==4)
                        {
                            $dzn_qnty=12*3;
                        }
                        else if($costing_per_id==5)
                        {
                            $dzn_qnty=12*4;
                        }
                        else
                        {
                            $dzn_qnty=1;
                        }


					$i=1;

					if($country_id_string==0)
					{
						$contry_cond="";
					}
					else
					{
						$contry_cond="and c.country_id in(".$country_id_string.")";
					}

				 // $sql=" select  sum(c.order_quantity) as po_quantity ,c.country_id as country_id from wo_po_color_size_breakdown c  where   c.job_no_mst='$job_no' and c.po_break_down_id=$po_id $contry_cond  and c.status_active=1 and c.is_deleted=0 group by c.country_id ";
			      $sql="select  b.id,b.job_no_mst,c.country_id, sum(c.order_quantity/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst  and b.id=c.po_break_down_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and  c.job_no_mst='$job_no' and c.po_break_down_id in($po_id) $contry_cond  $date_cond  group by   b.id,b.job_no_mst,c.country_id order by b.id,b.job_no_mst,c.country_id";

					$dtlsArray=sql_select($sql);
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
							$cons=$req_arr[$row[csf('id')]][$job_no]['cons'];
							$req_qty=($row[csf('order_quantity_set')]/$dzn_qnty)*$cons;
							//$descript=$item_description_arr[$po_id][$job_no]['description'];
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="80" align="center"><p><? echo $buyer_short_name_library[$buyer]; ?></p></td>
                            <td width="100"><p><? echo $order_arr[$row[csf('id')]]; ?></p></td>
                            <td width="100"><p><? echo $description; ?></p></td>
                            <td width="100" align="center"><p><? echo  $country_name_library[$row[csf('country_id')]]; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($req_qty,2); ?></p></td>
                            <td width="" align="right"><p><? echo number_format($rate,4); ?></p></td>

                        </tr>
						<?
						$tot_qty+=$req_qty;
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td  align="right"></td>
                    	<td colspan="4" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty,2); ?> </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();*/
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$buyer_short_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$country_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name");
	$po_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number");

	?>
	<!--<div style="width:680px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px" class="formbutton"/></div>-->
	<fieldset style="width:670px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="30">Sl</th>
                    <th width="80">Buyer Name</th>
                    <th width="100">Order No</th>
                    <th width="100">Item Description</th>
                    <th width="100">Country</th>
                    <th width="80">Req. Qty.</th>
					<th width="60">Uom</th>
                    <th>Req. Rate</th>
				</thead>
                <tbody>
                <?
				//echo $po_id;
				$condition= new condition();
				$condition->job_no("='$job_no'");

				$condition->po_id("in($po_id)");

				if(str_replace("'","",$start_date)!="" && str_replace("'","",$end_date)!="")
				{
					$condition->country_ship_date(" between '$start_date' and '$end_date'");
				}

				$condition->init();
				$trim= new trims($condition);
				$trim_qty=$trim->getQtyArray_by_orderCountryAndPrecostdtlsid();


				//print_r($trim_qty);
				//$trim= new trims($condition);
				//$trim_amount=$trim->getAmountArray_by_orderAndPrecostdtlsid();

				//$trim_qty=$trim->getQtyArray_by_jobAndPrecostdtlsid();
			//print_r($trim_qty);
				//$trim= new trims($condition);
			//$trim_amount=$trim->getAmountArray_by_orderAndPrecostdtlsid();
				//$trim_amount=$trim->getAmountArray_by_jobAndPrecostdtlsid();

				$country_id_str="";
				if($start_date=="" && $end_date=="") $date_cond=""; else $date_cond="and country_ship_date between '$start_date' and '$end_date'";
				$sql_color_size="select id, country_id from wo_po_color_size_breakdown where po_break_down_id in ($po_id) and job_no_mst='$job_no' and status_active=1 and is_deleted=0 $date_cond";
				$sql_color_size_res=sql_select($sql_color_size);
				foreach($sql_color_size_res as $row)
				{
					if($country_id_str=="") $country_id_str=$row[csf('id')]; else $country_id_str.=','.$row[csf('id')];
				}
				$excountry_id=array_filter(array_unique(explode(",",$country_id_str)));
				if($excountry_id!="") $country_idcond= "and c.color_size_table_id in ($excountry_id)"; else $country_idcond= "";

				$sql="select  b.id as trim_dtla_id, b.description,b.cons_uom, b.rate, b.amount,  c.cons, c.country_id, c.po_break_down_id, b.job_no
					from wo_pre_cost_mst a, wo_pre_cost_trim_cost_dtls b, wo_pre_cost_trim_co_cons_dtls c
					where c.job_no=b.job_no and b.id=c.wo_pre_cost_trim_cost_dtls_id and a.job_no=b.job_no and a.job_no='$job_no' and c.po_break_down_id in ($po_id) and b.id=$trim_dtla_id and c.cons>0
					group by  b.id, b.description, b.rate, b.amount,b.cons_uom,  c.cons, c.country_id, c.po_break_down_id, b.job_no order by b.trim_group";

				$dtlsArray=sql_select($sql);
				$pre_cost_data_arr=array();
				foreach($dtlsArray as $row)
				{
					$excountry_id=array_unique(explode(",",$row[csf('country_id')])); $req_qty=0;
					foreach($excountry_id as $country_id)
					{

						//$req_qty=$trim_qty[$row[csf('po_break_down_id')]][$country_id][$row[csf('trim_dtla_id')]];
						$pre_cost_data_arr[$row[csf('po_break_down_id')]][$country_id][$row[csf('trim_dtla_id')]]=$req_qty;
						$pre_cost_uom_arr[$row[csf('po_break_down_id')]][$country_id][$row[csf('trim_dtla_id')]]=$row[csf('cons_uom')];
					}
				}
				unset($dtlsArray);
				$i=1;
				foreach($pre_cost_data_arr as $po_id=>$po_data)
				{
					foreach($po_data as $country_id=>$country_data)
					{
						foreach($country_data as $description=>$req_qty)
						{
							//if(in_array($country_id,$excountry_id))
							//{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								//echo $po_id.'='.$country_id.'='.$description.', ';
								$trim_req_qty=$trim_qty[$po_id][$country_id][$description];
								$uom_id=$pre_cost_uom_arr[$po_id][$country_id][$description];

								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
									<td width="30"><p><? echo $i; ?></p></td>
									<td width="80" align="center"><p><? echo $buyer_short_arr[$buyer]; ?></p></td>
									<td width="100"><p><? echo $po_arr[$po_id]; ?></p></td>
									<td width="100"><p><? //echo $description;//$description; ?></p></td>
									<td width="100" align="center"><p><? echo $country_arr[$country_id]; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($trim_req_qty,2); ?></p></td>
									<td width="60" align="right"><p><? echo $unit_of_measurement[$uom_id]; ?></p></td>
									<td align="right"><p><? echo number_format($rate,4); ?></p></td>
								</tr>
								<?
								$tot_qty+=$trim_req_qty;
								$i++;
							//}
						}
					}
				}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td align="right">&nbsp;</td>
                    	<td align="right" colspan="4">Total</td>
                        <td align="right"><? echo number_format($tot_qty,2); ?></td>
                        <td>&nbsp;</td>
						<td>&nbsp;</td>
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