<?
session_start();
//ini_set('memory_limit','3072M');
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.yarns.php');
require_once('../../../../includes/class4/class.conversions.php');
require_once('../../../../includes/class4/class.trims.php');
require_once('../../../../includes/class4/class.emblishments.php');
require_once('../../../../includes/class4/class.commisions.php');
require_once('../../../../includes/class4/class.commercials.php');
require_once('../../../../includes/class4/class.others.php');
require_once('../../../../includes/class4/class.washes.php');
require_once('../../../../includes/class4/class.fabrics.php');


/*require_once('../../../../includes/class.reports.php');
require_once('../../../../includes/class.yarns.php');*/

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
//--------------------------------------------------------------------------------------------------------------------

$user_name = $_SESSION['logic_erp']["user_id"];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
	exit();
}

if($action=="order_no_search_popup")
{
	echo load_html_head_contents("Order No Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$exdata=explode("_",$data);
	$companyID=$exdata[0];
	$type=$exdata[1];
	$buyer_id=$exdata[2];
	//echo $buyer_id.'DSDSD';
	$buy_conds="";
	if($buyer_id>0) $buy_conds=" and buy.id in ($buyer_id)";
	?>
	<script>
		var selected_id = new Array; var selected_name = new Array;
		
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

			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
				selected_name.push( str[2] );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hide_order_id').val( id );
			$('#hide_order_no').val( name );
		}
	</script>
</head>
<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:750px;">
				<table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th width="140">Buyer</th>
						<th width="110">Search By</th>
						<th width="120" id="search_by_td_up">Please Enter Order No</th>
						<th width="130" colspan="2">Shipment Date</th>
						<th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:70px;"></th> 
						<input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
						<input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
						<input type="hidden" name="hide_pre_cost_ver_id" id="hide_pre_cost_ver_id" value="<?=$cbo_pre_cost_class;?>" />
					</thead>
					<tbody>
						<tr class="general">
							<td><?=create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buy_conds $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",0,"",0 ); ?></td>                 
							<td>	
								<?


								$search_by_arr=array(1=>"Order No",2=>"Style Ref",3=>"Job No");
								$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
								echo create_drop_down( "cbo_search_by", 110, $search_by_arr,"",0, "--Select--", "",$dd,0 );
								?>
							</td>     
							<td id="search_by_td"><input type="text" style="width:110px" class="text_boxes" name="txt_search_common" id="txt_search_common" /></td> 
							<td>
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" readonly></td>
								<td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date" readonly></td>	
								<td>
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<?=$companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('hide_pre_cost_ver_id').value+'**'+'<?=$type; ?>', 'create_order_no_search_list_view', 'search_div', 'cost_analysis_sheet_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:70px;" />
								</td>
							</tr>
							<tr>
								<td colspan="6" valign="middle"><?=load_month_buttons(1); ?></td>
							</tr>
						</tbody>
					</table>
					<div style="margin-top:5px" id="search_div"></div>
				</fieldset>
			</form>
		</div>
	</body>           
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit(); 
}

if($action=="create_order_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$pre_cost_ver_id=$data[6];
	$type=$data[7];

	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond=" and a.buyer_name=$data[1]";
	
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";

	if($search_by==1) $search_field="b.po_number"; else if($search_by==2) $search_field="a.style_ref_no"; else $search_field="a.job_no";

	$start_date =$data[4];
	$end_date =$data[5];	
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format(trim($start_date),"yyyy-mm-dd")."' and '".change_date_format(trim($end_date),"yyyy-mm-dd")."'";
		}
		else
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";	
		}
	}
	else $date_cond="";
	
	$company_short_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$arr=array (0=>$company_short_arr,1=>$buyer_arr);
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later

	$entry_form_cond="and c.entry_from in(111,158)";
	if($type==1)
	{
		$sql="select a.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no and  b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond $entry_form_cond group by a.id, a.job_no, a.insert_date, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no order by a.id Desc";

		echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No", "70,70,50,70,150","760","210",0, $sql , "js_set_value", "id,style_ref_no","",1,"company_name,buyer_name,0,0,0",$arr,"company_name,buyer_name,year,job_no_prefix_num,style_ref_no","",'','0,0,0,0,0','',1) ;
	}
	else if($type==2)
	{
		$sql="select b.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no and  b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond $entry_form_cond order by b.id, b.pub_shipment_date DESC";

		echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date", "70,70,50,70,150,180","760","210",0, $sql , "js_set_value", "id,po_number","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,pub_shipment_date","",'','0,0,0,0,0,0,3','',1) ;
	}
	exit(); 
}

if($action=="check_conversion_rate")
{
	$data=explode("**",$data);
	if($db_type==0)
	{
		$conversion_date=change_date_format(date('Y-m-d'), "Y-m-d", "-",1);
	}
	else
	{
		$conversion_date=change_date_format(date('Y-m-d'), "d-M-y", "-",1);
	}
	$currency_rate=set_conversion_rate( $data[0], $conversion_date, $data[1] );
	echo "1"."_".$currency_rate;
	exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$brand_library=return_library_array( "select id, brand_name from lib_buyer_brand", "id", "brand_name");
	$season_library=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name");
	
	$report_type=str_replace("'","",$report_type);
	$company_name=str_replace("'","",$cbo_company_name);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$txt_exchange_rate=str_replace("'","",$txt_exchange_rate);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$hide_order_id=str_replace("'","",$hide_order_id);
	$hide_job_id=str_replace("'","",$hide_job_id);
	$cbo_date_type=str_replace("'","",$cbo_date_type);
	$exchange_rate=$txt_exchange_rate; 
	
	$style_ref_cond="";
	if(trim(str_replace("'","",$txt_style_ref))!="")
	{
		if(str_replace("'","",$hide_job_id)!="")
		{
			$style_ref_cond= where_con_using_array(array_unique(explode(",", str_replace("'","",$hide_job_id))),0,"a.id");
		}
		else
		{
			$style_ref_cond=" and LOWER(a.style_ref_no) like LOWER('%".trim(str_replace("'","",$txt_style_ref))."%')";
		}
	}
	
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
	
	$date_cond='';
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{

		if($cbo_date_type==2){
			$date_cond2=" and b.ex_factory_date between $txt_date_from and $txt_date_to";
			$date_cond3=" and d.ex_factory_date between $txt_date_from and $txt_date_to";
			$ex_cond3=" and b.id=d.po_break_down_id and d.status_active=1";
			$ex_table=",pro_ex_factory_mst d";
		}else{
			$date_cond=" and b.shipment_date between $txt_date_from and $txt_date_to";
		}

		
	}
	
	$po_id_cond="";
	if(trim(str_replace("'","",$txt_order_no))!="")
	{
		if(str_replace("'","",$hide_order_id)!="")
		{
			$po_id_cond= where_con_using_array(array_unique(explode(",", str_replace("'","",$hide_order_id))),0,"b.id");
		}
		else
		{
			$po_id_cond=" and LOWER(b.po_number) like LOWER('%".trim(str_replace("'","",$txt_order_no))."%')";
		}
	}
	
	//Show button	
	if($report_type==1) 
	{

		if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
		else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
		else $year_field="";
		//defined Later
		$sql="SELECT a.job_no_prefix_num, a.job_no, $year_field, a.company_name, a.season_buyer_wise as season, a.brand_id, a.season_year, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, a.set_smv, b.id, b.po_number, b.grouping, b.file_no, b.pub_shipment_date, b.po_quantity, (b.po_quantity*a.total_set_qnty) as poqtypcs, b.plan_cut, b.unit_price, b.po_total_price, b.shiping_status, c.costing_date from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c $ex_table where a.job_no=b.job_no_mst  and a.job_no=c.job_no and c.job_no=b.job_no_mst $ex_cond3 and c.entry_from=158 and a.company_name='$company_name'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond3  $buyer_id_cond $date_cond $style_ref_cond $po_id_cond group by a.job_no_prefix_num, a.job_no,a.insert_date,a.company_name, a.season_buyer_wise, a.brand_id, a.season_year, a.buyer_name, a.style_ref_no, 
		a.order_uom, a.gmts_item_id, a.total_set_qnty, a.set_smv, b.id, b.po_number, b.grouping, b.file_no, b.pub_shipment_date,b.po_quantity, b.shipment_date,b.plan_cut, b.unit_price, b.po_total_price, b.shiping_status, c.costing_date order by b.shipment_date asc";//b.pub_shipment_date, a.job_no_prefix_num, b.id
		//echo $sql; //die;
		$result=sql_select($sql); 
		$all_po_id="";  $all_jobs='';
		$po_ids=array();
		foreach($result as $row)
		{
		//array_push($po_ids, $row[csf('id')]);
			if($all_po_id=="") $all_po_id=$row[csf("id")]; else $all_po_id.=",".$row[csf("id")];
			if($all_jobs=="") $all_jobs="'".$row[csf("job_no")]."'"; else $all_jobs.=",'".$row[csf("job_no")]."'";
		}
		$all_jobs=implode(",",array_unique(explode(",",$all_jobs)));
		$po_ids=array_filter(array_unique(explode(",",$all_po_id)));
		$po_cond_for_in=where_con_using_array($po_ids,0,"b.po_break_down_id"); 
		$po_cond_for_in2=where_con_using_array($po_ids,0,"po_id"); 
		$po_cond_for_in3=where_con_using_array($po_ids,0,"b.order_id");
		$po_cond_for_in4=where_con_using_array($po_ids,0,"b.po_breakdown_id"); 
		$po_cond_for_in5=where_con_using_array($po_ids,0,"c.po_break_down_id"); 

	/*$ex_factory_arr=return_library_array( "select b.po_break_down_id, 
	sum(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END)-sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as qnty 
	from pro_ex_factory_mst b where b.status_active=1 and b.is_deleted=0 $po_cond_for_in $date_cond2 group by b.po_break_down_id ", "po_break_down_id", "qnty");*/
	
	
	


	$sql_2="SELECT a.job_no_prefix_num, a.job_no, $year_field, a.company_name, a.season_buyer_wise as season, a.brand_id, a.season_year, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, a.set_smv, b.id, b.po_number, b.grouping, b.file_no, b.pub_shipment_date, b.po_quantity, (b.po_quantity*a.total_set_qnty) as poqtypcs, b.plan_cut, b.unit_price, b.po_total_price, b.shiping_status, c.costing_date from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no and c.job_no=b.job_no_mst and c.entry_from=158 and a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_id_cond  $style_ref_cond  order by b.shipment_date asc";
	$result_2=sql_select($sql_2); 

	foreach($result_2 as $row){

		$style_job_wise_qty[$row[csf("job_no")]]+=$row[csf("poqtypcs")];
		$style_job_wise_val[$row[csf("job_no")]]+=$row[csf("po_total_price")];
		$style_job_po_wise_arr[$row[csf("id")]]['poQty']=$row[csf("poqtypcs")];
		$style_job_po_wise_arr[$row[csf("id")]]['poVal']=$row[csf("po_total_price")];
	}

	ob_start();
	?>
	<fieldset>
		<table width="2720">
			<tr class="form_caption">
				<td colspan="28" align="center"><strong>Cost Analysis Sheet Report</strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="28" align="center"><strong><?=$company_arr[$company_name];?></strong>
					<br>
					<strong><?=change_date_format(str_replace("'","",$txt_date_from)) .' To '. change_date_format(str_replace("'","",$txt_date_to)); ?></strong>
				</td>
			</tr>
		</table>
		<table id="table_header_1" class="rpt_table" width="2720" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
				<tr style="font-size:12px">
					<th rowspan="3" width="30">SL</th>
					<th rowspan="3" width="120">Buyer</th>
					<th rowspan="3" width="110">Style Name</th>
					<th rowspan="3" width="180">PO No</th>
					<th rowspan="3" width="100">Style Shipping Status</th>
					<th rowspan="3" width="110">JOB Qty (Pcs)</th>
					<th rowspan="3" width="110">JOB Value ($)</th>
					<th rowspan="3" width="110">PO Qty (Pcs)</th>
					<th rowspan="3" width="110">PO Value ($)</th>
					<th rowspan="3" width="100">Ex-Factory Qty</th>

					<th colspan="7">Budget Cost Breakdown</th>
					<th colspan="7">Actual Cost Breakdown</th>

					<th rowspan="2" width="100" title="FOB * Ex-Factory Qty">FOB</th>
					<th rowspan="2" width="80" title="FOB-Total Budget Cost">Budgeted Profit/ [Loss]</th>
					<th rowspan="2" width="80" title="FOB-Total Actual Cost">Actual Profit/ [Loss]</th>
					<th rowspan="2" width="80" title="Total Budget Cost-Total Actual Cost">Variance [BOM Vs Actual]</th>
					<th rowspan="2" width="80" title="Ex-Factory Value ($)-Total Actual Cost">Margin Value</th>
					<th rowspan="2" title="(Total Actual Cost/Total Budget Cost)*100">Margin %</th>
				</tr>
				<tr style="font-size:12px">
					<th width="80">Fabrics Cost</th>
					<th width="80">Acce. Cost</th>
					<th width="80">Emb. Cost</th>
					<th width="80">Wash Cost</th>
					<th width="80">CM Cost</th>
					<th width="80" >Oparational<br>Cost</th>
					<th width="80">Total Cost<br></th>

					<th width="80" title="Finish Fabric Received Cost">Fabrics Cost</th>
					<th width="80" title="Trims Booking Cost">Acce. Cost</th>
					<th width="80" title="Embl Booking Cost">Emb. Cost</th>
					<th width="80" title="Embl Booking Cost">Wash Cost</th>
					<th width="80">CM Cost</th>
					<th width="80" >Oparational Cost</th>
					<th width="80">Total Cost</th>
				</tr>
				<tr style="font-size:12px">
					<th>USD</th>
					<th>USD</th>
					<th>USD</th>
					<th>USD</th>
					<th>USD</th>
					<th>USD</th>
					<th>USD</th>

					<th>USD</th>
					<th>USD</th>
					<th>USD</th>
					<th>USD</th>
					<th>USD</th>
					<th>USD</th>
					<th>USD</th>

					<th>USD</th>
					<th>USD</th>
					<th>USD</th>
					<th>USD</th>
					<th>USD</th>
					<th>USD</th>
				</tr>
			</thead>
		</table>
		<div style="width:2740px; max-height:400px; overflow-y:scroll" id="scroll_body">
			<table class="rpt_table" width="2720" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
				<?
				$fabriccostArray=array(); $yarncostArray=array(); $trimsCostArray=array(); $prodcostArray=array(); $actualCostArray=array(); $actualTrimsCostArray=array(); 
				$subconCostArray=array(); $embellCostArray=array(); $washCostArray=array(); $aopCostArray=array(); $yarnTrimsCostArray=array(); 
				$yarncostDataArray=sql_select("select job_no, sum(amount) as amnt, sum(rate*avg_cons_qnty) as amount from wo_pre_cost_fab_yarn_cost_dtls where status_active=1 and is_deleted=0 group by job_no");
				foreach($yarncostDataArray as $yarnRow)
				{
					$yarncostArray[$yarnRow[csf('job_no')]]=$yarnRow[csf('amount')];
				}
				unset($yarncostDataArray);
				
				
				$fabriccostDataArray=sql_select("select job_no, costing_per_id, embel_cost, wash_cost, cm_cost, commission, currier_pre_cost, lab_test, inspection, freight, comm_cost from wo_pre_cost_dtls where status_active=1 and is_deleted=0");
				foreach($fabriccostDataArray as $fabRow)
				{
					$fabriccostArray[$fabRow[csf('job_no')]]['costing_per_id']=$fabRow[csf('costing_per_id')];
					$fabriccostArray[$fabRow[csf('job_no')]]['embel_cost']=$fabRow[csf('embel_cost')];
					$fabriccostArray[$fabRow[csf('job_no')]]['wash_cost']=$fabRow[csf('wash_cost')];
					$fabriccostArray[$fabRow[csf('job_no')]]['cm_cost']=$fabRow[csf('cm_cost')];
					$fabriccostArray[$fabRow[csf('job_no')]]['commission']=$fabRow[csf('commission')];
					$fabriccostArray[$fabRow[csf('job_no')]]['currier_pre_cost']=$fabRow[csf('currier_pre_cost')];
					$fabriccostArray[$fabRow[csf('job_no')]]['lab_test']=$fabRow[csf('lab_test')];
					$fabriccostArray[$fabRow[csf('job_no')]]['inspection']=$fabRow[csf('inspection')];
					$fabriccostArray[$fabRow[csf('job_no')]]['freight']=$fabRow[csf('freight')];
					$fabriccostArray[$fabRow[csf('job_no')]]['comm_cost']=$fabRow[csf('comm_cost')];
				}
				unset($fabriccostDataArray);
				
				$trimscostDataArray=sql_select("select b.po_break_down_id, sum(b.cons*a.rate) as total from wo_pre_cost_trim_cost_dtls a, wo_pre_cost_trim_co_cons_dtls b where a.id=b.wo_pre_cost_trim_cost_dtls_id and a.status_active=1 and a.is_deleted=0 group by b.po_break_down_id");
				foreach($trimscostDataArray as $trimsRow)
				{
					$trimsCostArray[$trimsRow[csf('po_break_down_id')]]=$trimsRow[csf('total')];
				}
				unset($trimscostDataArray);

				$prodcostDataArray=sql_select("select job_no, 
					sum(CASE WHEN cons_process=1 THEN amount END) AS knit_charge,
					sum(CASE WHEN cons_process=30 THEN amount END) AS yarn_dye_charge,
					sum(CASE WHEN cons_process=35 THEN amount END) AS aop_charge,
					sum(CASE WHEN cons_process not in(1,2,30,35) THEN amount END) AS dye_finish_charge
					from wo_pre_cost_fab_conv_cost_dtls where status_active=1 and is_deleted=0 group by job_no");
				foreach($prodcostDataArray as $prodRow)
				{
					$prodcostArray[$prodRow[csf('job_no')]]['knit_charge']=$prodRow[csf('knit_charge')];
					$prodcostArray[$prodRow[csf('job_no')]]['yarn_dye_charge']=$prodRow[csf('yarn_dye_charge')];
					$prodcostArray[$prodRow[csf('job_no')]]['aop_charge']=$prodRow[csf('aop_charge')];
					$prodcostArray[$prodRow[csf('job_no')]]['dye_finish_charge']=$prodRow[csf('dye_finish_charge')];
				}	
				unset($prodcostDataArray);
				/*if($cbo_date_type==2){
					
				}
				else
				{
					
				}*/
				$actualCostDataArray=sql_select("select cost_head,po_id,applying_period_date,applying_period_to_date,(amount_usd) as amount_usd from wo_actual_cost_entry where company_id=$company_name and status_active=1 and is_deleted=0 $po_cond_for_in2 order by cost_head,po_id");
				$actualCostDateArray=array();
				foreach($actualCostDataArray as $actualRow)
				{
					$actualCostArray[$actualRow[csf('cost_head')]][$actualRow[csf('po_id')]]+=$actualRow[csf('amount_usd')];

					$applying_period_date=change_date_format($actualRow[csf('applying_period_date')],'','',1);
					$applying_period_to_date=change_date_format($actualRow[csf('applying_period_to_date')],'','',1);
					$diff=datediff('d',$applying_period_date,$applying_period_to_date);
					for($j=0;$j<$diff;$j++)
					{
						$date_all=add_date(str_replace("'","",$applying_period_date),$j);
						$newdate =change_date_format($date_all,'','',1);
						// echo $newdate.'<br>';
						
						$actualCostDateArray[$actualRow[csf('cost_head')]][$newdate][$actualRow[csf('po_id')]]=$actualRow[csf('amount_usd')];
					}	

				}
				//print_r($actualCostDateArray);
				unset($actualCostDataArray);
				
				$sql_exf="select b.po_break_down_id,b.ex_factory_date,
				(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END)-(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as qnty 
				from pro_ex_factory_mst b where b.status_active=1 and b.is_deleted=0 $po_cond_for_in $date_cond2 order by b.po_break_down_id ";
				$result_sql_exf=sql_select($sql_exf); 
				foreach($result_sql_exf as $row)
				{
					$ex_factory_date=$row[csf("ex_factory_date")];
					$newdate =change_date_format($ex_factory_date,'','',1);
					$ex_factory_arr[$row[csf("po_break_down_id")]]+=$row[csf("qnty")];
					$ex_factory_date_arr[$row[csf("po_break_down_id")]].=$row[csf("ex_factory_date")].',';
					$commercial=$actualCostDateArray[6][$newdate][$row[csf('po_break_down_id')]];
					$freight_cost=$actualCostDateArray[2][$newdate][$row[csf('po_break_down_id')]];
					$inspection=$actualCostDateArray[3][$newdate][$row[csf('po_break_down_id')]];
					$currier_pre_cost=$actualCostDateArray[4][$newdate][$row[csf('po_break_down_id')]];
					$design_cost=$actualCostDateArray[7][$newdate][$row[csf('po_break_down_id')]];
					$cm_cost=$actualCostDateArray[5][$newdate][$row[csf('po_break_down_id')]];
				//echo $commercial.'D';
					$actualCostArray2[6][$row[csf('po_break_down_id')]]+=$commercial;
					$actualCostArray2[5][$row[csf('po_break_down_id')]]+=$cm_cost;
					$actualCostArray2[2][$row[csf('po_break_down_id')]]+=$freight_cost;
					$actualCostArray2[3][$row[csf('po_break_down_id')]]+=$inspection;
					$actualCostArray2[4][$row[csf('po_break_down_id')]]+=$currier_pre_cost;
					$actualCostArray2[7][$row[csf('po_break_down_id')]]+=$design_cost;

				//
				}
					/*$freight_cost_actual=$actualCostArray[2][$row[csf('id')]];
					$inspection_actual=$actualCostArray[3][$row[csf('id')]];
					$currier_pre_cost_actual=$actualCostArray[4][$row[csf('id')]];
					//$comm_cost_actual=$actualCostArray[6][$row[csf('id')]];
					$design_cost_actual=$actualCostArray[7][$row[csf('id')]];*/
					
				//die;
					$subconInBillDataArray=sql_select("select b.order_id, 
						sum(CASE WHEN a.process_id=2 THEN b.amount END) AS knit_bill,
						sum(CASE WHEN a.process_id=4 THEN b.amount END) AS dye_finish_bill
						from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.party_source=1 and a.process_id in(2,4) $po_cond_for_in3  group by b.order_id");
					foreach($subconInBillDataArray as $subRow)
					{
						$subconCostArray[$subRow[csf('order_id')]]['knit_bill']=$subRow[csf('knit_bill')];
						$subconCostArray[$subRow[csf('order_id')]]['dye_finish_bill']=$subRow[csf('dye_finish_bill')];
					}
					unset($subconInBillDataArray);	

					$subconOutBillDataArray=sql_select("select b.order_id, 
						sum(CASE WHEN a.process_id=2 THEN b.amount END) AS knit_bill,
						sum(CASE WHEN a.process_id=4 THEN b.amount END) AS dye_finish_bill
						from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.process_id in(2,4) $po_cond_for_in3 group by b.order_id");
					foreach($subconOutBillDataArray as $subRow)
					{
						$subconCostArray[$subRow[csf('order_id')]]['knit_bill']+=$subRow[csf('knit_bill')];
						$subconCostArray[$subRow[csf('order_id')]]['dye_finish_bill']+=$subRow[csf('dye_finish_bill')];
					}
					unset($subconOutBillDataArray);	

					$embell_type_arr=return_library_array( "select id, emb_name from wo_pre_cost_embe_cost_dtls", "id", "emb_name");	

					$bookingDataArray=sql_select("select a.booking_type, a.item_category, a.currency_id, a.exchange_rate, b.po_break_down_id, b.process, b.amount, b.pre_cost_fabric_cost_dtls_id from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_name and a.item_category in(4,12,25) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in");
					foreach($bookingDataArray as $woRow)
					{
						$amount=0; $trimsAmnt=0;
						if($woRow[csf('currency_id')]==1) { $amount=$woRow[csf('amount')]/$exchange_rate; } else { $amount=$woRow[csf('amount')]; }

						if($woRow[csf('item_category')]==25 && ($woRow[csf('booking_type')]==3 || $woRow[csf('booking_type')]==6)) 
						{ 
							if($embell_type_arr[$woRow[csf('pre_cost_fabric_cost_dtls_id')]]==3)
							{
								$washCostArray[$woRow[csf('po_break_down_id')]]+=$amount; 
							}
							else
							{
								$embellCostArray[$woRow[csf('po_break_down_id')]]+=$amount; 
							}
						}
						else if($woRow[csf('item_category')]==12 && $woRow[csf('process')]==35 && ($woRow[csf('booking_type')]==3 || $woRow[csf('booking_type')]==6)) 
						{ 
							$aopCostArray[$woRow[csf('po_break_down_id')]]+=$amount; 
						}
						else if($woRow[csf('item_category')]==4)
						{
							if($woRow[csf('currency_id')]==1) { $trimsAmnt=$woRow[csf('amount')]/$woRow[csf('exchange_rate')]; } else { $trimsAmnt=$woRow[csf('amount')]; }
							$actualTrimsCostArray[$woRow[csf('po_break_down_id')]]+=$trimsAmnt; 
						}
					}
					unset($bookingDataArray);		

					$avg_rate_array=return_library_array( "select id, avg_rate_per_unit from product_details_master where item_category_id=1", "id", "avg_rate_per_unit"  );
					$receive_array=array();
					$sql_receive="select a.currency_id,a.receive_purpose,b.prod_id, (b.order_qnty) as qty, (b.order_amount) as amnt,b.cons_quantity,b.cons_amount from inv_receive_master a, inv_transaction b where  a.id=b.mst_id and b.transaction_type=1 and b.item_category=1 and b.status_active=1 and b.is_deleted=0";
					$resultReceive = sql_select($sql_receive);
					foreach($resultReceive as $invRow)
					{
					if($invRow[csf('currency_id')]==1)//Taka
					{
						$avg_rate=$invRow[csf('cons_amount')]/$invRow[csf('cons_quantity')];
						$receive_array[$invRow[csf('prod_id')]]=$avg_rate/$exchange_rate;
					}
					else if($invRow[csf('currency_id')]==2)//USD
					{
						$avg_rate=$invRow[csf('amnt')]/$invRow[csf('qty')];
						$receive_array[$invRow[csf('prod_id')]]=$avg_rate;
					}
					else
					{
						
						$receive_array[$invRow[csf('prod_id')]]=$avg_rate;
					}
				}
				unset($resultReceive);
				
				$yarnTrimsDataArray=sql_select("select b.po_breakdown_id, b.prod_id, a.item_category, 
					sum(CASE WHEN a.transaction_type=2 and b.entry_form ='3' and b.trans_type=2 and b.issue_purpose!=2 THEN b.quantity ELSE 0 END) AS yarn_iss_qty,
					sum(CASE WHEN a.transaction_type=4 and b.entry_form ='9' and b.trans_type=4 THEN b.quantity ELSE 0 END) AS yarn_iss_return_qty,
					sum(CASE WHEN a.transaction_type=5 and b.entry_form ='11' and b.trans_type=5 THEN b.quantity ELSE 0 END) AS trans_in_qty_yarn,
					sum(CASE WHEN a.transaction_type=6 and b.entry_form ='11' and b.trans_type=6 THEN b.quantity ELSE 0 END) AS trans_out_qty_yarn,
					sum(CASE WHEN a.transaction_type=2 and b.entry_form ='25' and b.trans_type=2 THEN a.cons_rate*b.quantity ELSE 0 END) AS trims_issue_amnt
					from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.item_category in(1,4) and a.transaction_type in(2,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 $po_cond_for_in4  group by b.po_breakdown_id, b.prod_id, a.item_category");
				foreach($yarnTrimsDataArray as $invRow)
				{
					if($invRow[csf('item_category')]==1)
					{
						$iss_qty=$invRow[csf('yarn_iss_qty')]+$invRow[csf('trans_in_qty_yarn')]-$invRow[csf('yarn_iss_return_qty')]-$invRow[csf('trans_out_qty_yarn')];
						$rate='';
						if($receive_array[$invRow[csf('prod_id')]]>0)
						{
							$rate=$receive_array[$invRow[csf('prod_id')]]/$exchange_rate;
						}
						else
						{
							$rate=$avg_rate_array[$invRow[csf('prod_id')]]/$exchange_rate;
						}
						
						$iss_amnt=$iss_qty*$rate;
						$yarnTrimsCostArray[$invRow[csf('po_breakdown_id')]][1]+=$iss_amnt;
					}
					else
					{
						$yarnTrimsCostArray[$invRow[csf('po_breakdown_id')]][4]+=$invRow[csf('trims_issue_amnt')];
					}
				}
				unset($yarnTrimsDataArray);
				$ex_rate=$txt_exchange_rate;
				$pi_number_check=array();
				
				$sqlPi=sql_select("select c.po_break_down_id, sum(b.quantity) as quantity from  com_pi_master_details a, com_pi_item_details b,wo_booking_dtls c where a.id=b.pi_id and b.work_order_no=c.booking_no  and a.item_category_id=2 and a.status_active=1 and c.status_active=1 and a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0 $po_cond_for_in5  group by c.po_break_down_id");
				
				foreach($sqlPi as $rowPi){
					$pi_number_check[$rowPi[csf('po_break_down_id')]]=$rowPi[csf('po_break_down_id')];
				}
				unset($sqlPi);
				
				$sql_fin_purchase="select b.po_breakdown_id, sum(a.cons_rate*b.quantity) as finish_purchase_amnt from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.receive_basis in(1,2) and a.item_category=2 and a.transaction_type=1 and b.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in4 group by b.po_breakdown_id";
				$dataArrayFinPurchase=sql_select($sql_fin_purchase);
				foreach($dataArrayFinPurchase as $finRow)
				{
					if($pi_number_check[$finRow[csf('po_breakdown_id')]]==$finRow[csf('po_breakdown_id')])
					{
						$finish_purchase_amnt_arr[$finRow[csf('po_breakdown_id')]]=$finRow[csf('finish_purchase_amnt')]/$ex_rate;
					}
				}
				unset($dataArrayFinPurchase);
				
				$sql_fin_purchase_wv="select b.po_breakdown_id, sum(a.cons_rate*b.quantity) as woven_purchase_amnt from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.item_category=3 and a.transaction_type=1 and b.entry_form=17 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $po_cond_for_in4 group by b.po_breakdown_id";

				$dataArrayFinPurchaseW=sql_select($sql_fin_purchase_wv);
				foreach($dataArrayFinPurchaseW as $finRow)
				{
					$finish_purchase_amnt_arr[$finRow[csf('po_breakdown_id')]]+=$finRow[csf('woven_purchase_amnt')]/$ex_rate;
				}
				unset($dataArrayFinPurchaseW);
				$LabtestcostArray=array();
				$labtestcostData=sql_select("select b.order_id as po_id, (a.wo_value) as amnt from wo_labtest_dtls a,wo_labtest_order_dtls b where a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.entry_form=274 $po_cond_for_in3");
				//echo "select b.order_id as po_id, (b.wo_value) as amnt from wo_labtest_dtls a,wo_labtest_order_dtls b where a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.entry_form=274 $po_cond_for_in3";die;
				foreach($labtestcostData as $row)
				{
					$LabtestcostArray[$row[csf('po_id')]]=$row[csf('amnt')];
				}
				unset($labtestcostData);
				
				$i=1; $tot_po_qnty=0; $tot_po_value=0; $tot_ex_factory_qnty=0; $tot_ex_factory_val=0; $tot_yarn_cost_mkt=0; $tot_knit_cost_mkt=0; $tot_dye_finish_cost_mkt=0; $tot_yarn_dye_cost_mkt=0; $tot_aop_cost_mkt=0; $tot_trims_cost_mkt=0; $tot_embell_cost_mkt=0; $tot_wash_cost_mkt=0; $tot_commission_cost_mkt=0; $tot_comm_cost_mkt=0; $tot_freight_cost_mkt=0; $tot_test_cost_mkt=0; $tot_inspection_cost_mkt=0; $tot_currier_cost_mkt=0; $tot_cm_cost_mkt=0; $tot_mkt_all_cost=0; $tot_mkt_margin=0; $tot_yarn_cost_actual=0; $tot_knit_cost_actual=0; $tot_dye_finish_cost_actual=0; $tot_yarn_dye_cost_actual=0; $tot_aop_cost_actual=0; $tot_trims_cost_actual=0; $tot_embell_cost_actual=0; $tot_wash_cost_actual=0; $tot_commission_cost_actual=0; $tot_comm_cost_actual=0; $tot_freight_cost_actual=0; $tot_test_cost_actual=0; $tot_inspection_cost_actual=0; $tot_currier_cost_actual=0; $tot_cm_cost_actual=0; $tot_actual_all_cost=0; $tot_actual_margin=0; $tot_fabric_purchase_cost_mkt=0; $tot_fabric_purchase_cost_actual=0;
				
				
				/*$JobArr=array();
				foreach($result as $row_yarn){
					$JobArr[]=$row_yarn[csf('job_no')];
				}
				$yarn= new yarn($JobArr,'job');
				$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
				$yarn->unsetDataArray();*/
				
				$condition= new condition();
				$condition->company_name("=$company_name");
				 //$all_po_ids=implode(",",array_unique(explode(",",$all_po_id)));
				if(isset($po_ids))
				{
					//$condition->job_no("in($all_jobs)");
					$condition->po_id_in(implode(",",$po_ids));
				}
				
				if(str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
				{
					$start_date=str_replace("'","",$txt_date_from);
					$end_date=str_replace("'","",$txt_date_to);

					if($cbo_date_type==1){
						$condition->pub_shipment_date(" between '$start_date' and '$end_date'");
					}
					 //and b.po_received_date between '$start_date' and '$end_date' 
					// echo 'FFGG';
				}
				$condition->init();
				
				$yarn= new yarn($condition);
				
				$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
				$conversion= new conversion($condition);
				$conversion_costing_arr_process=$conversion->getAmountArray_by_orderAndProcess();
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
				$fabric= new fabric($condition);
				// echo $fabric->getQuery();die;
				$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
				
				//print_r($fabric_costing_arr);die;
				$not_yarn_dyed_cost_arr=array(1,2,30,35);

				$style_wise_data=array();
				$i=0;
				foreach($result as $row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					$style_job=$row[csf('style_ref_no')];
					$po_ids_array[]=$row[csf('id')];
					$gmts_item='';
					$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
					foreach($gmts_item_id as $item_id)
					{
						if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
					}
					//echo $row[csf('id')]."D";
					$order_qnty_in_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
					$plan_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];
					$unit_price=$row[csf('unit_price')]/$row[csf('ratio')];
					$po_value=$order_qnty_in_pcs*$unit_price;

					$tot_po_qnty+=$order_qnty_in_pcs; 
					$tot_po_value+=$po_value;
					
					$ex_factory_qty=$ex_factory_arr[$row[csf('id')]];
					$ex_factory_date=$ex_factory_date_arr[$row[csf('id')]];
					//echo $row[csf('id')].'='.$ex_factory_date.'<br>';
					//$ex_factory_date_arr[$row[csf("po_break_down_id")]]
					$ex_factory_value=$ex_factory_qty*$unit_price;
					$tot_ex_factory_qnty+=$ex_factory_qty; 
					$tot_ex_factory_val+=$ex_factory_value; 
					
					$dzn_qnty=0;
					$costing_per_id=$fabriccostArray[$row[csf('job_no')]]['costing_per_id'];
					if($costing_per_id==1) $dzn_qnty=12;
					else if($costing_per_id==3) $dzn_qnty=12*2;
					else if($costing_per_id==4) $dzn_qnty=12*3;
					else if($costing_per_id==5) $dzn_qnty=12*4;
					else $dzn_qnty=1;

					$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
					$trims_cost_mkt=$trims_costing_arr[$row[csf('id')]];
					
					$print_amount=$emblishment_costing_arr_name[$row[csf('id')]][1];
					$embroidery_amount=$emblishment_costing_arr_name[$row[csf('id')]][2];
					$special_amount=$emblishment_costing_arr_name[$row[csf('id')]][4];
					$wash_cost=$emblishment_costing_arr_name_wash[$row[csf('id')]][3];
					$other_amount=$emblishment_costing_arr_name[$row[csf('id')]][5];
					$foreign_cost=$commission_costing_arr[$row[csf('id')]][1];
					$local_cost=$commission_costing_arr[$row[csf('id')]][2];
					
					$comm_cost_mkt=$commercial_costing_arr[$row[csf('id')]];
					
					$test_cost=$other_costing_arr[$row[csf('id')]]['lab_test'];
					$freight_cost=$other_costing_arr[$row[csf('id')]]['freight'];
					$inspection_cost=$other_costing_arr[$row[csf('id')]]['inspection'];
					$certificate_cost=$other_costing_arr[$row[csf('id')]]['certificate_pre_cost'];
					$currier_cost=$other_costing_arr[$row[csf('id')]]['currier_pre_cost'];
					$design_cost=$other_costing_arr[$row[csf('id')]]['design_cost'];
					
					$mkt_other_cost=$test_cost+$freight_cost+$inspection_cost+$currier_cost+$design_cost;
					$style_wise_data[$row[csf('style_ref_no')]]['commercial']+=$comm_cost_mkt;
					$style_wise_data[$row[csf('style_ref_no')]]['lab_test']+=$test_cost;
					$style_wise_data[$row[csf('style_ref_no')]]['freight']+=$freight_cost;
					$style_wise_data[$row[csf('style_ref_no')]]['inspection']+=$inspection_cost;
					$style_wise_data[$row[csf('style_ref_no')]]['currier_pre_cost']+=$currier_cost;
					$style_wise_data[$row[csf('style_ref_no')]]['design_cost']+=$design_cost;
					
					$cm_cost=$other_costing_arr[$row[csf('id')]]['cm_cost'];
					$comm_cost_actual=$actualCostArray[6][$row[csf('id')]];
					
					if($i==0){
						$ac_id .=$row[csf('id')];
						$i=1;
					}else{
						$ac_id .=','.$row[csf('id')];
					}

					
					$freight_cost_actual=$actualCostArray2[2][$row[csf('id')]];
					$inspection_actual=$actualCostArray2[3][$row[csf('id')]];
					$currier_pre_cost_actual=$actualCostArray2[4][$row[csf('id')]];
					$comm_cost_actual=$actualCostArray2[6][$row[csf('id')]];
					$design_cost_actual=$actualCostArray2[7][$row[csf('id')]];
					$lab_test=$LabtestcostArray[$row[csf('id')]];
					$other_cost_actual=$freight_cost_actual+$inspection_actual+$currier_pre_cost_actual+$design_cost_actual+$lab_test;
					//echo $comm_cost_actual.'A'.$freight_cost_actual.'='.$inspection_actual.'='.$currier_pre_cost_actual.'='.$design_cost_actua.'='.$lab_test."<br>";
					$style_wise_data[$row[csf('style_ref_no')]]['a_commercial']+=$comm_cost_actual;
					$style_wise_data[$row[csf('style_ref_no')]]['a_lab_test']+=$lab_test;
					$style_wise_data[$row[csf('style_ref_no')]]['a_freight']+=$freight_cost_actual;
					$style_wise_data[$row[csf('style_ref_no')]]['a_inspection']+=$inspection_actual;
					$style_wise_data[$row[csf('style_ref_no')]]['a_currier_pre_cost']+=$currier_pre_cost_actual;
					$style_wise_data[$row[csf('style_ref_no')]]['a_design_cost']+=$design_cost_actual;


					
					$embell_cost_mkt=$print_amount+$embroidery_amount+$special_amount+$other_amount;
					$wash_cost_mkt=$wash_cost;
					$commission_cost_mkt=$foreign_cost+$local_cost;
					
					$cm_cost_mkt=$cm_cost;
					$fabric_purchase_cost_mkt=0;
					$fabric_purchase_cost_mkt=array_sum($fabric_costing_arr['knit']['grey'][$row[csf('id')]])+array_sum($fabric_costing_arr['woven']['grey'][$row[csf('id')]]);
					//$fabric_purchase_cost_mkt=array_sum($fabric_costing_arr['woven']['grey'][$row[csf('id')]]);
					
					$mkt_all_cost=$cm_cost_mkt+$fabric_purchase_cost_mkt+$trims_cost_mkt+$embell_cost_mkt+$wash_cost_mkt+$comm_cost_mkt+$mkt_other_cost;
					$mkt_margin=$po_value-$mkt_all_cost;
					$mkt_margin_perc=($mkt_margin/$po_value)*100;
					$trims_cost_actual=$actualTrimsCostArray[$row[csf('id')]];
					
					$embell_cost_actual=$embellCostArray[$row[csf('id')]];
					$wash_cost_actual=$washCostArray[$row[csf('id')]];
					$commission_cost_actual=($ex_factory_qty/$dzn_qnty)*$fabriccostArray[$row[csf('job_no')]]['commission'];
					$cm_cost_actual=$actualCostArray2[5][$row[csf('id')]];
					$fabric_purchase_cost_actual=$finish_purchase_amnt_arr[$row[csf('id')]];
					$actual_all_cost=$cm_cost_actual+$fabric_purchase_cost_actual +$trims_cost_actual+$embell_cost_actual+$wash_cost_actual+$comm_cost_actual+$other_cost_actual;
					
					$style_wise_data[$row[csf('style_ref_no')]]['buyer_name'].=$buyer_arr[$row[csf('buyer_name')]]."***";
					$style_wise_data[$row[csf('style_ref_no')]]['ex_factory_qty']+=$ex_factory_qty;
					$style_wise_data[$row[csf('style_ref_no')]]['ex_factory_val']+=$ex_factory_qty*$row[csf('unit_price')];
					
					$poQty=$style_job_po_wise_arr[$row[csf("id")]]['poQty'];
					$poVal=$style_job_po_wise_arr[$row[csf("id")]]['poVal'];

					$style_wise_data[$row[csf('style_ref_no')]]['budget_fabrics_cost_per_pcs']+=$fabric_purchase_cost_mkt;
					$style_wise_data[$row[csf('style_ref_no')]]['budget_accessories_cost_per_pcs']+=$trims_cost_mkt;
					$style_wise_data[$row[csf('style_ref_no')]]['budget_embellishment_cost']+=$embell_cost_mkt;
					$style_wise_data[$row[csf('style_ref_no')]]['budget_washing_cost']+=$wash_cost_mkt;
					$style_wise_data[$row[csf('style_ref_no')]]['budget_cm_per_pcs']+=$cm_cost_mkt;
					$style_wise_data[$row[csf('style_ref_no')]]['budget_oparational_cost_per_pcs']+=$comm_cost_mkt+$mkt_other_cost;
					$style_wise_data[$row[csf('style_ref_no')]]['budget_total_cost_per_cost']+=$mkt_all_cost;
					//echo $comm_cost_actual.'='.$other_cost_actual.', ';;
					$style_wise_data[$row[csf('style_ref_no')]]['actual_fabrics_cost_per_pcs']+=$fabric_purchase_cost_actual;
					$style_wise_data[$row[csf('style_ref_no')]]['actual_accessories_cost_per_pcs']+=$trims_cost_actual;
					$style_wise_data[$row[csf('style_ref_no')]]['actual_embellishment_cost']+=$embell_cost_actual;
					$style_wise_data[$row[csf('style_ref_no')]]['actual_washing_cost']+=$wash_cost_actual;
					$style_wise_data[$row[csf('style_ref_no')]]['actual_cm_per_pcs']+=$cm_cost_actual;
					$style_wise_data[$row[csf('style_ref_no')]]['actual_oparational_cost_per_pcs']+=$comm_cost_actual+$other_cost_actual;

					$style_wise_data[$row[csf('style_ref_no')]]['actual_total_cost_per_cost']+=$actual_all_cost;
					$style_wise_data[$row[csf('style_ref_no')]]['actual_sales_price_per_pcs']+=$unit_price;
					$style_wise_data[$row[csf('style_ref_no')]]['job_no']=$row[csf('job_no')];
					$style_wise_data[$row[csf('style_ref_no')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
					$style_wise_data[$row[csf('style_ref_no')]]['po_id'].=$row[csf('id')].",";
					$style_wise_data[$row[csf('style_ref_no')]]['costing_date']=$row[csf('costing_date')];
					$style_wise_data[$row[csf('style_ref_no')]]['pono'].=$row[csf('po_number')].",";
					$style_wise_data[$row[csf('style_ref_no')]]['styleQtyPcs']+=$poQty;
					$style_wise_data[$row[csf('style_ref_no')]]['styleVal']+=$poVal;
					// $style_wise_data[$row[csf('style_ref_no')]]['styleVal']+=$row[csf('po_total_price')];
					$style_wise_data[$row[csf('style_ref_no')]]['id']=$row[csf('id')];
					$style_wise_data[$row[csf('style_ref_no')]]['unit_price'] +=$row[csf('unit_price')];
					if($row[csf('shiping_status')]==1 || $row[csf('shiping_status')]==0)
					{
						$style_wise_data[$row[csf('style_ref_no')]]['pending']='Pending';
					}
					elseif($row[csf('shiping_status')]==2)
					{
						$style_wise_data[$row[csf('style_ref_no')]]['partial']='Partial Delivery';
					}
					elseif($row[csf('shiping_status')]==3)
					{
						$style_wise_data[$row[csf('style_ref_no')]]['full']='Full Delivery';
					}
				}	
			//print_r($style_wise_data);
				foreach ($style_wise_data as $style_ref_no => $val) 
				{
					$job_no=$val['job_no'];
					$job_no_prefix_num=$val['job_no_prefix_num'];
					$po_id=chop($val['po_id'],",");
					$costing_date=change_date_format($val['costing_date']);
					$buyer=implode(",", array_unique(explode("***", chop($val['buyer_name'],"***"))));
					$shippingStatus="";
					if($val['partial']!="") $shippingStatus="Partial Delivery";
					if($val['pending']!="" && $shippingStatus=="") $shippingStatus="Pending";
					if($val['full']!="" && $shippingStatus=="") $shippingStatus="Full Delivery";
					$stylePrice=$val['styleVal']/$val['styleQtyPcs'];
					$fob=$val['ex_factory_qty']*$stylePrice;
					$budgetedProfitLoss=$fob-$val['budget_total_cost_per_cost'];
					$actualProfitLoss=$fob-$val['actual_total_cost_per_cost'];
					$variance=$val['budget_total_cost_per_cost']-$val['actual_total_cost_per_cost'];
					$marginVal=$val['ex_factory_val']-$val['actual_total_cost_per_cost'];
					$marginPer=($val['actual_total_cost_per_cost']/$val['budget_total_cost_per_cost'])*100;
					//if($val['ex_factory_qty'] !==0){
					?>
					<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');" id="tr_<?=$i; ?>" style="font-size:12px">
						<td width="30" align="center"><?=$i; ?></td>
						<td width="120" style="word-break:break-all;"><?=$buyer; ?></td>
						<td width="110" style="word-break:break-all;" title="Job=<?=$job_no;?>,Costing Date=<?=$costing_date;?>"><?=$style_ref_no; ?></td>
						<td width="180" title="<? //echo $po_id;?>" style="word-break:break-all;" align="center"><p><?=chop($val['pono'],","); ?>&nbsp;</p><!--<a href="##" onClick="generate_po_popup('po_popup','<?//=$po_id; ?>','<?//=chop($val['pono'],","); ?>','650px')"></a>--></td>
						<td width="100" style="word-break:break-all;"><?=$shippingStatus; ?></td>
						<td width="110" style="word-break:break-all;" align="right"><?=number_format($style_job_wise_qty[$val['job_no']],0,'.',''); ?></td>
						<td width="110" style="word-break:break-all;" align="right"><?=number_format($style_job_wise_val[$val['job_no']],2,'.',''); ?></td>
						<td width="110" style="word-break:break-all;" align="right"><?=number_format($val['styleQtyPcs'],0,'.',''); ?></td>
						<td width="110" style="word-break:break-all;" align="right"><?=number_format($val['styleVal'],2,'.',''); ?></td>
						<td width="100" style="word-break:break-all;" align="right"><a href="##" onClick="generate_ex_factory_popup('ex_factory_popup','<?=$job_no_prefix_num;?>','<?=$po_id; ?>','<?=str_replace("'","",$txt_date_from) ?>','<?=str_replace("'","",$txt_date_to) ?>','650px')"><?=number_format($val['ex_factory_qty'],0,'.',''); ?></a></td>

						<td width="80" title="<? echo $val['budget_fabrics_cost_per_pcs'];?>" style="word-break:break-all;" align="right"><?=number_format($val['budget_fabrics_cost_per_pcs'],2,'.',''); ?></td>
						<td width="80" title="<? echo $val['budget_accessories_cost_per_pcs'];?>" style="word-break:break-all;" align="right"><?=number_format($val['budget_accessories_cost_per_pcs'],2,'.',''); ?></td>
						<td width="80" title="<? echo $val['budget_embellishment_cost'];?>" style="word-break:break-all;" align="right"><?=number_format($val['budget_embellishment_cost'],2,'.',''); ?></td>
						<td width="80" title="<? echo $val['budget_washing_cost'];?>" style="word-break:break-all;" align="right"><?=number_format($val['budget_washing_cost'],2,'.',''); ?></td>
						<td width="80" title="<? echo $val['budget_cm_per_pcs'];?>" style="word-break:break-all;" align="right"><?=number_format($val['budget_cm_per_pcs'],2,'.',''); ?></td>
						<td width="80" title="<? echo $val['budget_oparational_cost_per_pcs'];?>" style="word-break:break-all;" align="right"><a href="##" onClick="generate_budget_op_cost_popup('budget_oparational_cost_popup','<?=$val['commercial'];?>','<?=$val['lab_test'];?>','<?=$val['freight'];?>','<?=$val['inspection']; ?>','<?=$val['currier_pre_cost']; ?>','<?=$val['design_cost']; ?>','650px')"><?=number_format($val['budget_oparational_cost_per_pcs'],2,'.','');
						


					?></a></td>
					<td width="80" title="<? echo $val['budget_total_cost_per_cost'];?>" style="word-break:break-all;" align="right"><?=number_format($val['budget_total_cost_per_cost'],2,'.',''); ?></td>

					<td width="80" title="<? echo $val['actual_fabrics_cost_per_pcs'];?>" style="word-break:break-all;" align="right" ><a href="#report_details" onClick="openmypage_actual('<?=$po_id; ?>','fabric_purchase_cost_actual','Fabric Purchase Cost Details','900px')"><?=number_format($val['actual_fabrics_cost_per_pcs'],2,'.',''); ?></a></td>
					<td width="80" title="<? echo $val['actual_accessories_cost_per_pcs'];?>" style="word-break:break-all;" align="right"><a href="#report_details" onClick="openmypage_actual('<?=$po_id; ?>','trims_cost_actual','Trims Cost Details','800px')"><?=number_format($val['actual_accessories_cost_per_pcs'],2,'.',''); ?></a></td>
					<td width="80" title="<? echo $val['actual_embellishment_cost'];?>" style="word-break:break-all;" align="right"><a href="#report_details" onClick="openmypage_actual('<?=$po_id; ?>','embell_cost_actual','Embellishment Cost Details','800px')"><?=number_format($val['actual_embellishment_cost'],2,'.',''); ?></a></td>
					<td width="80" title="<? echo $val['actual_washing_cost'];?>" style="word-break:break-all;" align="right"><a href="#report_details" onClick="openmypage_actual('<?=$po_id; ?>','wash_cost_actual','Wash Cost Details','800px')"><?=number_format($val['actual_washing_cost'],2,'.',''); ?></a></td>
					<td width="80" title="<? echo $val['actual_cm_per_pcs'];?>" style="word-break:break-all;" align="right"><?=number_format($val['actual_cm_per_pcs'],2,'.',''); ?></td>
					<td width="80" title="Opt.Cost=<? echo $val['actual_oparational_cost_per_pcs'];?>" style="word-break:break-all;" align="right"><a href="##" onClick="generate_actual_op_cost_popup('actual_oparational_cost_popup','<?=$val['a_commercial'];?>','<?=$val['a_lab_test'];?>','<?=$val['a_freight'];?>','<?=$val['a_inspection']; ?>','<?=$val['a_currier_pre_cost']; ?>','<?=$val['a_design_cost']; ?>','650px')"><?=number_format($val['actual_oparational_cost_per_pcs'],2,'.',''); ?></a></td>
					<td width="80" style="word-break:break-all;" align="right"><?=number_format($val['actual_total_cost_per_cost'],2,'.',''); ?></td>

					<td width="100" style="word-break:break-all;" align="right" title="<?=$val['ex_factory_qty'].'*('.$val['styleVal'].'/'.$val['styleQtyPcs'].')'; ?>"><?=number_format($fob,2,'.','');?></td>
					<td width="80" style="word-break:break-all;" align="right" title="<?=$fob.'-'.$val['budget_total_cost_per_cost']; ?>"><?=number_format($budgetedProfitLoss,2,'.','');?></td>
					<td width="80" style="word-break:break-all;" align="right" title="<?=$fob.'-'.$val['actual_total_cost_per_cost']; ?>"><?=number_format($actualProfitLoss,2,'.','');?></td>
					<td width="80" style="word-break:break-all;" align="right" title="<?=$val['budget_total_cost_per_cost'].'-'.$val['actual_total_cost_per_cost']; ?>"><?=number_format($variance,2,'.','');?></td>
					<td width="80" style="word-break:break-all;" align="right" title="<?=($val['ex_factory_val']).'-'.$val['actual_total_cost_per_cost']; ?>"><?=number_format(($val['ex_factory_val'])-$val['actual_total_cost_per_cost'],2,'.','');?></td>
					<td style="word-break:break-all;" align="right" title="<?='('.$val['actual_total_cost_per_cost'].'/'.$val['budget_total_cost_per_cost'].')*100'; ?>"><?=number_format($marginPer,2,'.',''); ?></td>
				</tr>
				<?
				
				$i++;
				$gStyleQty+=$val['styleQtyPcs'];
				$gStyleValue+=$val['styleVal'];
				$gExQty+=$val['ex_factory_qty'];
				$gbFabCost+=$val['budget_fabrics_cost_per_pcs'];
				$gbAccCost+=$val['budget_accessories_cost_per_pcs'];
				$gbEmbCost+=$val['budget_embellishment_cost'];
				$gbWashCost+=$val['budget_washing_cost'];
				$gbCmCost+=$val['budget_cm_per_pcs'];
				$gbOpeCost+=$val['budget_oparational_cost_per_pcs'];
				$gbTotCost+=$val['budget_total_cost_per_cost'];

				$gaFabCost+=$val['actual_fabrics_cost_per_pcs'];
				$gaAccCost+=$val['actual_accessories_cost_per_pcs'];
				$gaEmbCost+=$val['actual_embellishment_cost'];
				$gaWashCost+=$val['actual_washing_cost'];
				$gaCmCost+=$val['actual_cm_per_pcs'];
				$gaOpeCost+=$val['actual_oparational_cost_per_pcs'];
				$gaTotCost+=$val['actual_total_cost_per_cost'];

				$gFob+=$fob;
				$gbudgetedProfitLoss+=$budgetedProfitLoss;
				$gactualProfitLoss+=$actualProfitLoss;
				$gvariance+=$variance;
				$gmarginVal+=$marginVal;

				}//}
				$avgMarginPer=($gaTotCost/$gbTotCost)*100;
				?>
			</table>
		</div>
		<table class="tbl_bottom" width="2720" cellpadding="0" cellspacing="0" border="1" rules="all">
			<tr style="font-size:12px">
				<td width="30">&nbsp;</td>
				<td width="120">&nbsp;</td>
				<td width="110">&nbsp;</td>
				<td width="180">&nbsp;</td>
				<td width="100" align="right">Total:</td>
				<td width="110" align="right" style="word-break:break-all;" id="td_jobQty"><?=number_format($gStyleQty,0,'.',''); ?></td>
				<td width="110" align="right" style="word-break:break-all;" id="value_jobVal"><?=number_format($gStyleValue,2,'.',''); ?></td>
				<td width="110" align="right" style="word-break:break-all;" id="td_styleQty"><?=number_format($gStyleQty,0,'.',''); ?></td>
				<td width="110" align="right" style="word-break:break-all;" id="value_styleVal"><?=number_format($gStyleValue,2,'.',''); ?></td>
				<td width="100" align="right" style="word-break:break-all;" id="td_exQty"><?=number_format($gExQty,0,'.',''); ?></td>

				<td width="80" align="right" style="word-break:break-all;" id="value_fabbom"><?=number_format($gbFabCost,2,'.',''); ?></td>
				<td width="80" align="right" style="word-break:break-all;" id="value_accbom"><?=number_format($gbAccCost,2,'.',''); ?></td>
				<td width="80" align="right" style="word-break:break-all;" id="value_embbom"><?=number_format($gbEmbCost,2,'.',''); ?></td>
				<td width="80" align="right" style="word-break:break-all;" id="value_washbom"><?=number_format($gbWashCost,2,'.',''); ?></td>
				<td width="80" align="right" style="word-break:break-all;" id="value_cmbom"><?=number_format($gbCmCost,2,'.',''); ?></td>
				<td width="80" align="right" style="word-break:break-all;" id="value_opbom"><?=number_format($gbOpeCost,2,'.',''); ?></td>
				<td width="80" align="right" style="word-break:break-all;" id="value_totbom"><?=number_format($gbTotCost,2,'.',''); ?></td>

				<td width="80" align="right" style="word-break:break-all;" id="value_fabact"><?=number_format($gaFabCost,2,'.',''); ?></td>
				<td width="80" align="right" style="word-break:break-all;" id="value_accact"><?=number_format($gaAccCost,2,'.',''); ?></td>
				<td width="80" align="right" style="word-break:break-all;" id="value_embact"><?=number_format($gaEmbCost,2,'.',''); ?></td>
				<td width="80" align="right" style="word-break:break-all;" id="value_washact"><?=number_format($gaWashCost,2,'.',''); ?></td>
				<td width="80" align="right" style="word-break:break-all;" id="value_cmact"><?=number_format($gaCmCost,2,'.',''); ?></td>
				<td width="80" align="right" style="word-break:break-all;" id="value_opeact"><?=number_format($gaOpeCost,2,'.',''); ?></td>
				<td width="80" align="right" style="word-break:break-all;" id="value_totact"><?=number_format($gaTotCost,2,'.',''); ?></td>

				<td width="100" align="right" style="word-break:break-all;" id="value_fob"><?=number_format($gFob,2,'.',''); ?></td>
				<td width="80" align="right" style="word-break:break-all;" id="value_bprofitloss"><?=number_format($gbudgetedProfitLoss,2,'.',''); ?></td>
				<td width="80" align="right" style="word-break:break-all;" id="value_aprofitloss"><?=number_format($gactualProfitLoss,2,'.',''); ?></td>
				<td width="80" align="right" style="word-break:break-all;" id="value_variance"><?=number_format($gvariance,2,'.',''); ?></td>
				<td width="80" align="right" style="word-break:break-all;" id="value_marginval"><?=number_format($gmarginVal,2,'.',''); ?></td>
				<td align="right" style="word-break:break-all;" id="value_marginper"><?=number_format($avgMarginPer,2,'.',''); ?></td>
			</tr>
		</table>
	</fieldset>
	<?
}
	else if($report_type==2) //Style 2 button
	{  
		
		$date_cond='';$date_cond2='';$date_cond3='';
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{

			if($cbo_date_type==2){
				$date_cond2=" and b.ex_factory_date between $txt_date_from and $txt_date_to";
				$date_cond3=" and d.ex_factory_date between $txt_date_from and $txt_date_to";

			}else{
				$date_cond=" and b.shipment_date between $txt_date_from and $txt_date_to";
			}
		}
	
		//print_r($ArrayArr);die;

	//$ex_cond3=" and b.id=d.po_break_down_id and d.status_active=1";
	//$ex_table=",pro_ex_factory_mst d";


		if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
		else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";
	//defined Later
	
	$sql="SELECT a.job_no_prefix_num, a.job_no, $year_field, a.company_name, a.season_buyer_wise as season, a.brand_id, a.season_year, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, a.set_smv,b.job_id, b.id, b.po_number, b.grouping, b.file_no, b.pub_shipment_date, b.po_quantity, (b.po_quantity*a.total_set_qnty) as poqtypcs, b.plan_cut, b.unit_price, b.po_total_price,b.is_confirmed, b.shiping_status, c.costing_date,d.ex_factory_date from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c, pro_ex_factory_mst d where a.job_no=b.job_no_mst  and a.job_no=c.job_no and c.job_no=b.job_no_mst and b.id=d.po_break_down_id and d.status_active=1 and c.entry_from=158 and a.company_name='$company_name' and b.is_confirmed in(1,2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $date_cond3  $buyer_id_cond $date_cond $style_ref_cond $po_id_cond group by a.job_no_prefix_num, a.job_no,a.insert_date,a.company_name, a.season_buyer_wise, a.brand_id, a.season_year, a.buyer_name, a.style_ref_no, 
	a.order_uom, a.gmts_item_id, a.total_set_qnty, a.set_smv, b.job_id,b.id, b.po_number, b.grouping, b.file_no, b.pub_shipment_date,b.po_quantity, b.shipment_date,b.plan_cut, b.unit_price, b.po_total_price, b.shiping_status,b.is_confirmed, c.costing_date,d.ex_factory_date order by d.ex_factory_date desc";

	$result=sql_select($sql); 
	$all_po_id="";  $all_jobs='';
	$po_ids=array();$exfact_conf_arr==$ship_status_arr = array();
	foreach($result as $row)
	{
		$job_id_arr[$row[csf("job_id")]]=$row[csf("job_id")];
		if($row[csf("is_confirmed")]==1)
		{
			$job_po_id_arr[$row[csf("job_no")]].=$row[csf("id")].',';
		}
		
		$job_max_ex_fact_arr[$row[csf("job_id")]].=$row[csf("ex_factory_date")].'.';
		$ship_po_arr[$row[csf("id")]]=$row[csf("shiping_status")];
		$poJob_wise_qty[$row[csf("id")]]=$row[csf("job_no")];
		$ship_status_arr[$row[csf("job_id")]][] = $row[csf("shiping_status")];
	}

	$job_cond_for_in8=where_con_using_array($job_id_arr,0,"job_id");
	$job_cond_for_in4=where_con_using_array($job_id_arr,0,"a.id"); 
	$job_cond_for_in3=where_con_using_array($job_id_arr,0,"b.job_id");
	$job_cond_for_in11=where_con_using_array($job_id_arr,0,"c.job_id");
	
	$ex_rate=$txt_exchange_rate;

	/*$ex_factory_arr=return_library_array( "select b.po_break_down_id, 
	sum(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END)-sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as qnty 
	from pro_ex_factory_mst b where b.status_active=1 and b.is_deleted=0 $po_cond_for_in $date_cond2 group by b.po_break_down_id ", "po_break_down_id", "qnty");*/
	
	$sql_exf_last="select c.job_id,max(b.ex_factory_date) as ex_factory_date,
	sum(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END)-sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as qnty 
	from pro_ex_factory_mst b,wo_po_break_down c,wo_po_details_master a where c.id=b.po_break_down_id and a.id=c.job_id and  b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 $job_cond_for_in4  group by c.job_id  ";
	$result_sql_exf_last=sql_select($sql_exf_last); 
	foreach($result_sql_exf_last as $row)
	{
		$last_ex_factory_date_arr[$row[csf("job_id")]]=$row[csf("ex_factory_date")];

	}
	unset($result_sql_exf_last);
	$actualCostDataArray=sql_select("select a.cost_head,a.po_id,a.applying_period_date,a.applying_period_to_date,(a.amount_usd) as amount_usd from wo_actual_cost_entry a,wo_po_break_down b where b.id=a.po_id and a.company_id=$company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $job_cond_for_in3 order by a.cost_head,a.po_id");
	//echo "select a.cost_head,a.po_id,a.applying_period_date,a.applying_period_to_date,(a.amount_usd) as amount_usd from wo_actual_cost_entry a,wo_po_break_down b where b.id=a.po_id and a.company_id=$company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $job_cond_for_in3 order by a.cost_head,a.po_id";
		$actualCostDateArray=array();
		foreach($actualCostDataArray as $actualRow)
		{
			$actualCostArray[$actualRow[csf('cost_head')]][$actualRow[csf('po_id')]]+=$actualRow[csf('amount_usd')];

			$applying_period_date=change_date_format($actualRow[csf('applying_period_date')],'','',1);
			$applying_period_to_date=change_date_format($actualRow[csf('applying_period_to_date')],'','',1);
			$diff=datediff('d',$applying_period_date,$applying_period_to_date);
			for($j=0;$j<$diff;$j++)
			{
				$date_all=add_date(str_replace("'","",$applying_period_date),$j);
				$newdate =change_date_format($date_all,'','',1);
				// echo $newdate.'<br>';
				
				$actualCostDateArray[$actualRow[csf('cost_head')]][$newdate][$actualRow[csf('po_id')]]=$actualRow[csf('amount_usd')];
			}	

		}
		//print_r($actualCostDateArray);
		unset($actualCostDataArray);
				

	 $sql_exf="select b.po_break_down_id,b.ex_factory_date,
	(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END)-(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as qnty 
	from pro_ex_factory_mst b,wo_po_break_down c where b.po_break_down_id=c.id and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 $job_cond_for_in11  order by b.po_break_down_id ";
	$result_sql_exf=sql_select($sql_exf); 
	foreach($result_sql_exf as $row)
	{
		$ex_factory_date=$row[csf("ex_factory_date")];
		$newdate =change_date_format($ex_factory_date,'','',1);
		$ex_factory_arr[$row[csf("po_break_down_id")]]+=$row[csf("qnty")];
		$ex_factory_date_arr[$row[csf("po_break_down_id")]].=$row[csf("ex_factory_date")].',';

		$poJob=$poJob_wise_qty[$row[csf("po_break_down_id")]];
		if($poJob)
		{
			//$ex_factory_po_arr[$poJob].=$row[csf("po_break_down_id")].',';
			$exfact_conf_arr[$poJob][$row[csf("po_break_down_id")]]=$row[csf("ex_factory_date")];
		}

		$commercial=$actualCostDateArray[6][$newdate][$row[csf('po_break_down_id')]];
		$freight_cost=$actualCostDateArray[2][$newdate][$row[csf('po_break_down_id')]];
		$inspection=$actualCostDateArray[3][$newdate][$row[csf('po_break_down_id')]];
		$currier_pre_cost=$actualCostDateArray[4][$newdate][$row[csf('po_break_down_id')]];
		$design_cost=$actualCostDateArray[7][$newdate][$row[csf('po_break_down_id')]];
		$cm_cost=$actualCostDateArray[5][$newdate][$row[csf('po_break_down_id')]];

		$actualCostArray2[6][$row[csf('po_break_down_id')]]+=$commercial;
		$actualCostArray2[5][$row[csf('po_break_down_id')]]+=$cm_cost;
		$actualCostArray2[2][$row[csf('po_break_down_id')]]+=$freight_cost;
		$actualCostArray2[3][$row[csf('po_break_down_id')]]+=$inspection;
		$actualCostArray2[4][$row[csf('po_break_down_id')]]+=$currier_pre_cost;
		$actualCostArray2[7][$row[csf('po_break_down_id')]]+=$design_cost;
	}

	$sql_3="SELECT a.job_no,a.style_ref_no,a.id as job_id,b.id,b.shiping_status,b.is_confirmed,b.po_number from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no and c.job_no=b.job_no_mst and c.entry_from=158 and b.is_confirmed in(1,2) and a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_id_cond  $style_ref_cond  $job_cond_for_in3 group by a.style_ref_no,a.job_no,a.id,b.id,b.shiping_status,b.po_number,b.is_confirmed order by a.id";
	$result_3=sql_select($sql_3); 
	foreach($result_3 as $row)
	{
		$po_id_arr[$row[csf("id")]]=$row[csf("id")];
	}
	$po_cond_for_in6=where_con_using_array($po_id_arr,0,"b.po_breakdown_id");
	$po_cond_for_in7=where_con_using_array($po_id_arr,0,"b.po_break_down_id"); 
	
	$embell_type_arr=return_library_array( "select id, emb_name from wo_pre_cost_embe_cost_dtls  where status_active=1 $job_cond_for_in8", "id", "emb_name");	
	
	$sql_fin_purchase="select b.po_breakdown_id, sum(a.cons_rate*b.quantity) as finish_purchase_amnt from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.receive_basis in(1,2) and a.item_category=2 and a.transaction_type=1 and b.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in6 group by b.po_breakdown_id";
	$dataArrayFinPurchase=sql_select($sql_fin_purchase);
	foreach($dataArrayFinPurchase as $finRow)
	{
		if($pi_number_check[$finRow[csf('po_breakdown_id')]]==$finRow[csf('po_breakdown_id')])
		{
			$finish_purchase_amnt_arr[$finRow[csf('po_breakdown_id')]]=$finRow[csf('finish_purchase_amnt')]/$ex_rate;
		}
	}
	unset($dataArrayFinPurchase);
	
	$sql_fin_purchase_wv="select b.po_breakdown_id, sum(a.cons_rate*b.quantity) as woven_purchase_amnt from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.item_category=3 and a.transaction_type=1 and b.entry_form=17 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $po_cond_for_in6 group by b.po_breakdown_id";

	$dataArrayFinPurchaseW=sql_select($sql_fin_purchase_wv);
	foreach($dataArrayFinPurchaseW as $finRow)
	{
		$finish_purchase_amnt_arr[$finRow[csf('po_breakdown_id')]]+=$finRow[csf('woven_purchase_amnt')]/$ex_rate;
	}
	unset($dataArrayFinPurchaseW);
	$bookingDataArray=sql_select("select a.booking_type, a.item_category, a.currency_id, a.exchange_rate, b.po_break_down_id, b.process, b.amount, b.pre_cost_fabric_cost_dtls_id from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_name and a.item_category in(4,12,25) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in7");

	foreach($bookingDataArray as $woRow)
	{
		$amount=0; $trimsAmnt=0;
		if($woRow[csf('currency_id')]==1) { $amount=$woRow[csf('amount')]/$exchange_rate; } else { $amount=$woRow[csf('amount')]; }

		if($woRow[csf('item_category')]==25 && ($woRow[csf('booking_type')]==3 || $woRow[csf('booking_type')]==6)) 
		{ 
			if($embell_type_arr[$woRow[csf('pre_cost_fabric_cost_dtls_id')]]==3)
			{
				$washCostArray[$woRow[csf('po_break_down_id')]]+=$amount;
			}
			else
			{
				$embellCostArray[$woRow[csf('po_break_down_id')]]+=$amount; 
			}
		}
		else if($woRow[csf('item_category')]==12 && $woRow[csf('process')]==35 && ($woRow[csf('booking_type')]==3 || $woRow[csf('booking_type')]==6)) 
		{ 
			$aopCostArray[$woRow[csf('po_break_down_id')]]+=$amount; 
		}
		else if($woRow[csf('item_category')]==4)
		{
			if($woRow[csf('currency_id')]==1) { $trimsAmnt=$woRow[csf('amount')]/$woRow[csf('exchange_rate')]; } else { $trimsAmnt=$woRow[csf('amount')]; }
			$actualTrimsCostArray[$woRow[csf('po_break_down_id')]]+=$trimsAmnt; 
		}
	}
	unset($bookingDataArray);		

	foreach($result_3 as $row)
	{
		if($row[csf("is_confirmed")]==1)
		{
			$shipId_arr[$row[csf("id")]]=$row[csf("shiping_status")];
		}

		$style_wise_data2[$row[csf('job_no')]]['po_id'].=$row[csf('id')].",";
		$style_wise_data2[$row[csf('job_no')]]['pono'].=$row[csf('po_number')].",";
		//Aditional Array
		$style_act_fab_cost_arr[$row[csf('job_no')]]+=$finish_purchase_amnt_arr[$row[csf('id')]];
		$style_act_trims_cost_arr[$row[csf('job_no')]]+=$actualTrimsCostArray[$row[csf('id')]];

		$style_act_aop_cost_arr[$row[csf('job_no')]]+=$aopCostArray[$row[csf('id')]];
		$style_act_embl_cost_arr[$row[csf('job_no')]]+=$embellCostArray[$row[csf('id')]];
		$style_act_wash_cost_arr[$row[csf('job_no')]]+=$washCostArray[$row[csf('id')]];
	}
	
	$from_date=strtotime(str_replace("'","",$txt_date_from));
	$to_date=strtotime(str_replace("'","",$txt_date_to));
	$sql_2="SELECT a.job_no_prefix_num, a.job_no,a.id as job_id, $year_field, a.company_name, a.season_buyer_wise as season, a.brand_id, a.season_year, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, a.set_smv, b.id,b.shiping_status, b.po_number, b.grouping, b.file_no, b.pub_shipment_date, b.po_quantity, (b.po_quantity*a.total_set_qnty) as poqtypcs, b.plan_cut, b.unit_price, b.po_total_price, b.shiping_status, c.costing_date from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no and c.job_no=b.job_no_mst and c.entry_from=158 and b.is_confirmed in(1) and a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_id_cond  $style_ref_cond  $job_cond_for_in3 order by b.id,b.shiping_status desc";
	$result_2=sql_select($sql_2); 
	$partialship_job_count = array();
	foreach($result_2 as $row)
	{
		if($all_po_id=="") $all_po_id=$row[csf("id")]; else $all_po_id.=",".$row[csf("id")];
		if($all_jobs=="") $all_jobs="'".$row[csf("job_no")]."'"; else $all_jobs.=",'".$row[csf("job_no")]."'";
		
		$style_job_wise_qty[$row[csf("job_no")]]+=$row[csf("poqtypcs")];
		$style_job_wise_val[$row[csf("job_no")]]+=$row[csf("po_total_price")];
		$style_job_po_wise_arr[$row[csf("id")]]['poQty']=$row[csf("poqtypcs")];
		$style_job_po_wise_arr[$row[csf("id")]]['poVal']=$row[csf("po_total_price")];
		$shipIdJob=$shipId_arr[$row[csf("id")]][3];
		
		$conf_chk_id=$conf_arr[$row[csf("id")]];
		if($row[csf("shiping_status")]!=3)
		{
			$ship_chk_arr[$row[csf("job_id")]]=$row[csf("shiping_status")];
		}
		$shiping_statusId=$ship_po_arr[$row[csf("id")]];

		$exfact_date=strtotime($exfact_conf_arr[$row[csf("job_no")]][$row[csf("id")]]);
		//echo $row[csf("id")] . "**".$exfact_conf_arr[$row[csf("job_no")]][$row[csf("id")]]."=".$last_ex_factory_date_arr[$row[csf("job_id")]]."==<br />";
		$last_ex_factory_date=strtotime($last_ex_factory_date_arr[$row[csf("job_id")]]);
		
		$ex_factoryQTY=$ex_factory_arr[$row[csf("id")]];

		$shipId=$shipId_arr[$row[csf("id")]];
		if($exfact_date=='' && $shipId!=3)
		{
			unset($fullship_job_po_chk_arr[$row[csf("job_no")]]);
		}
		else
		{
			/*
			po1 - ex full
			po2 - ex partial
			po3 - ex full 
			*/
			if($from_date!="" && $to_date !="")
			{
				if($shipId==3 && ($last_ex_factory_date>=$from_date && $last_ex_factory_date<=$to_date))
				{
					if($partialship_job_count[$row[csf("job_no")]]!=1)
					{	
						$fullship_job_po_chk_arr[$row[csf("job_no")]]=$last_ex_factory_date;
						$partialship_job_count[$row[csf("job_no")]]=0;
					}
					
				}
				else
				{
					unset($fullship_job_po_chk_arr[$row[csf("job_no")]]);
					$partialship_job_count[$row[csf("job_no")]]++;
				}
			}
			else
			{
				$fullship_job_po_chk_arr[$row[csf("job_no")]]=$last_ex_factory_date;
			}
		}
	}

	$all_jobs=implode(",",array_unique(explode(",",$all_jobs)));
	$po_ids=array_filter(array_unique(explode(",",$all_po_id)));
	$po_cond_for_in=where_con_using_array($po_ids,0,"b.po_break_down_id"); 
	$po_cond_for_in2=where_con_using_array($po_ids,0,"po_id"); 
	$po_cond_for_in3=where_con_using_array($po_ids,0,"b.order_id");
	$po_cond_for_in4=where_con_using_array($po_ids,0,"b.po_breakdown_id"); 
	$po_cond_for_in5=where_con_using_array($po_ids,0,"c.po_break_down_id");

	$job_cond_for_in=where_con_using_array($job_id_arr,0,"job_id"); 
	$job_cond_for_in2=where_con_using_array($job_id_arr,0,"a.job_id");

	ob_start();
	$width=2820;
	?>
	<fieldset>
		<table width="<? echo $width;?>">
			<tr class="form_caption">
				<td colspan="28" align="center"><strong>Cost Analysis Sheet Report</strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="28" align="center"><strong><?=$company_arr[$company_name];?></strong>
					<br>
					<strong><?=change_date_format(str_replace("'","",$txt_date_from)) .' To '. change_date_format(str_replace("'","",$txt_date_to)); ?></strong>
				</td>
			</tr>
		</table>
		<table id="table_header_1" class="rpt_table" width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
			<caption> <b style="color:#FF6600; font-size:20px; float:left">Ex-factory Date Against PO Close Style: </b> </caption>
			<thead>
				<tr style="font-size:12px">
					<th rowspan="3" width="30">SL</th>
					<th rowspan="3" width="120">Buyer</th>
					<th rowspan="3" width="110">Style Name</th>
					<th rowspan="3" width="180">PO No</th>
					<th rowspan="3" width="100">Style Shipping Status</th>
					<th rowspan="3" width="110">JOB Qty (Pcs)</th>
					<th rowspan="3" width="110">JOB Value ($)</th>
					<th rowspan="3" width="110">PO Qty (Pcs)</th>
					<th rowspan="3" width="110">PO Value ($)</th>
					<th rowspan="3" width="100">Ex-Factory Qty</th>
					<th rowspan="3" width="100">Last Ex-Factory Date</th>

					<th colspan="7">Budget Cost Breakdown</th>
					<th colspan="7">Actual Cost Breakdown</th>

					<th rowspan="2" width="100" title="FOB * Ex-Factory Qty">FOB</th>
					<th rowspan="2" width="80" title="FOB-Total Budget Cost">Budgeted Profit/ [Loss]</th>
					<th rowspan="2" width="80" title="FOB-Total Actual Cost">Actual Profit/ [Loss]</th>
					<th rowspan="2" width="80" title="Total Budget Cost-Total Actual Cost">Variance [BOM Vs Actual]</th>
					<th rowspan="2" width="80" title="Ex-Factory Value ($)-Total Actual Cost">Margin Value</th>
					<th rowspan="2" title="(Total Actual Cost/Total Budget Cost)*100">Margin %</th>
				</tr>
				<tr style="font-size:12px">
					<th width="80">Fabrics Cost</th>
					<th width="80">Acce. Cost</th>
					<th width="80">Emb. Cost</th>
					<th width="80">Wash Cost</th>
					<th width="80">CM Cost</th>
					<th width="80" >Oparational<br>Cost</th>
					<th width="80">Total Cost<br></th>

					<th width="80" title="Finish Fabric Received Cost">Fabrics Cost</th>
					<th width="80" title="Trims Booking Cost">Acce. Cost</th>
					<th width="80" title="Embl Booking Cost">Emb. Cost</th>
					<th width="80" title="Embl Booking Cost">Wash Cost</th>
					<th width="80">CM Cost</th>
					<th width="80" >Oparational Cost</th>
					<th width="80">Total Cost</th>
				</tr>
				<tr style="font-size:12px">
					<th>USD</th>
					<th>USD</th>
					<th>USD</th>
					<th>USD</th>
					<th>USD</th>
					<th>USD</th>
					<th>USD</th>

					<th>USD</th>
					<th>USD</th>
					<th>USD</th>
					<th>USD</th>
					<th>USD</th>
					<th>USD</th>
					<th>USD</th>

					<th>USD</th>
					<th>USD</th>
					<th>USD</th>
					<th>USD</th>
					<th>USD</th>
					<th>USD</th>
				</tr>
			</thead>
		</table>
		<div style="width:<? echo $width+20;?>px; max-height:400px; overflow-y:scroll" id="scroll_body">
			<table class="rpt_table" width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
				<?
				$fabriccostArray=array(); $yarncostArray=array(); $trimsCostArray=array(); $prodcostArray=array(); //$actualCostArray=array(); //$actualTrimsCostArray=array(); 
				$subconCostArray=array(); //$embellCostArray=array(); $washCostArray=array(); $aopCostArray=array();
				$yarnTrimsCostArray=array(); 
				$yarncostDataArray=sql_select("select job_no, sum(amount) as amnt, sum(rate*avg_cons_qnty) as amount from wo_pre_cost_fab_yarn_cost_dtls where status_active=1 and is_deleted=0 $job_cond_for_in group by job_no");
				foreach($yarncostDataArray as $yarnRow)
				{
					$yarncostArray[$yarnRow[csf('job_no')]]=$yarnRow[csf('amount')];
				}
				unset($yarncostDataArray);
				
				
				$fabriccostDataArray=sql_select("select job_no, costing_per_id, embel_cost, wash_cost, cm_cost, commission, currier_pre_cost, lab_test, inspection, freight, comm_cost from wo_pre_cost_dtls where status_active=1 and is_deleted=0 $job_cond_for_in ");
				foreach($fabriccostDataArray as $fabRow)
				{
					$fabriccostArray[$fabRow[csf('job_no')]]['costing_per_id']=$fabRow[csf('costing_per_id')];
					$fabriccostArray[$fabRow[csf('job_no')]]['embel_cost']=$fabRow[csf('embel_cost')];
					$fabriccostArray[$fabRow[csf('job_no')]]['wash_cost']=$fabRow[csf('wash_cost')];
					$fabriccostArray[$fabRow[csf('job_no')]]['cm_cost']=$fabRow[csf('cm_cost')];
					$fabriccostArray[$fabRow[csf('job_no')]]['commission']=$fabRow[csf('commission')];
					$fabriccostArray[$fabRow[csf('job_no')]]['currier_pre_cost']=$fabRow[csf('currier_pre_cost')];
					$fabriccostArray[$fabRow[csf('job_no')]]['lab_test']=$fabRow[csf('lab_test')];
					$fabriccostArray[$fabRow[csf('job_no')]]['inspection']=$fabRow[csf('inspection')];
					$fabriccostArray[$fabRow[csf('job_no')]]['freight']=$fabRow[csf('freight')];
					$fabriccostArray[$fabRow[csf('job_no')]]['comm_cost']=$fabRow[csf('comm_cost')];
				}
				unset($fabriccostDataArray);
				
				$trimscostDataArray=sql_select("select b.po_break_down_id, sum(b.cons*a.rate) as total from wo_pre_cost_trim_cost_dtls a, wo_pre_cost_trim_co_cons_dtls b where a.id=b.wo_pre_cost_trim_cost_dtls_id and a.status_active=1 and a.is_deleted=0 $job_cond_for_in2  group by b.po_break_down_id");
				foreach($trimscostDataArray as $trimsRow)
				{
					$trimsCostArray[$trimsRow[csf('po_break_down_id')]]=$trimsRow[csf('total')];
				}
				unset($trimscostDataArray);

				$prodcostDataArray=sql_select("select job_no, 
					sum(CASE WHEN cons_process=1 THEN amount END) AS knit_charge,
					sum(CASE WHEN cons_process=30 THEN amount END) AS yarn_dye_charge,
					sum(CASE WHEN cons_process=35 THEN amount END) AS aop_charge,
					sum(CASE WHEN cons_process not in(1,2,30,35) THEN amount END) AS dye_finish_charge
					from wo_pre_cost_fab_conv_cost_dtls where status_active=1 and is_deleted=0 job_cond_for_in group by job_no");
				foreach($prodcostDataArray as $prodRow)
				{
					$prodcostArray[$prodRow[csf('job_no')]]['knit_charge']=$prodRow[csf('knit_charge')];
					$prodcostArray[$prodRow[csf('job_no')]]['yarn_dye_charge']=$prodRow[csf('yarn_dye_charge')];
					$prodcostArray[$prodRow[csf('job_no')]]['aop_charge']=$prodRow[csf('aop_charge')];
					$prodcostArray[$prodRow[csf('job_no')]]['dye_finish_charge']=$prodRow[csf('dye_finish_charge')];
				}	
				unset($prodcostDataArray);
				/*if($cbo_date_type==2){
					
				}
				else
				{
					
				}*/
				
				
				
					/*$freight_cost_actual=$actualCostArray[2][$row[csf('id')]];
					$inspection_actual=$actualCostArray[3][$row[csf('id')]];
					$currier_pre_cost_actual=$actualCostArray[4][$row[csf('id')]];
					//$comm_cost_actual=$actualCostArray[6][$row[csf('id')]];
					$design_cost_actual=$actualCostArray[7][$row[csf('id')]];*/
					
				//die;
					$subconInBillDataArray=sql_select("select b.order_id, 
						sum(CASE WHEN a.process_id=2 THEN b.amount END) AS knit_bill,
						sum(CASE WHEN a.process_id=4 THEN b.amount END) AS dye_finish_bill
						from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.party_source=1 and a.process_id in(2,4) $po_cond_for_in3  group by b.order_id");
					foreach($subconInBillDataArray as $subRow)
					{
						$subconCostArray[$subRow[csf('order_id')]]['knit_bill']=$subRow[csf('knit_bill')];
						$subconCostArray[$subRow[csf('order_id')]]['dye_finish_bill']=$subRow[csf('dye_finish_bill')];
					}
					unset($subconInBillDataArray);	

					$subconOutBillDataArray=sql_select("select b.order_id, 
						sum(CASE WHEN a.process_id=2 THEN b.amount END) AS knit_bill,
						sum(CASE WHEN a.process_id=4 THEN b.amount END) AS dye_finish_bill
						from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.process_id in(2,4) $po_cond_for_in3 group by b.order_id");
					foreach($subconOutBillDataArray as $subRow)
					{
						$subconCostArray[$subRow[csf('order_id')]]['knit_bill']+=$subRow[csf('knit_bill')];
						$subconCostArray[$subRow[csf('order_id')]]['dye_finish_bill']+=$subRow[csf('dye_finish_bill')];
					}
					unset($subconOutBillDataArray);	




					$avg_rate_array=return_library_array( "select id, avg_rate_per_unit from product_details_master where item_category_id=1", "id", "avg_rate_per_unit"  );
					$receive_array=array();
					$sql_receive="select a.currency_id,a.receive_purpose,b.prod_id, (b.order_qnty) as qty, (b.order_amount) as amnt,b.cons_quantity,b.cons_amount from inv_receive_master a, inv_transaction b where  a.id=b.mst_id and b.transaction_type=1 and b.item_category=1 and b.status_active=1 and b.is_deleted=0";
					$resultReceive = sql_select($sql_receive);
					foreach($resultReceive as $invRow)
					{
					if($invRow[csf('currency_id')]==1)//Taka
					{
						$avg_rate=$invRow[csf('cons_amount')]/$invRow[csf('cons_quantity')];
						$receive_array[$invRow[csf('prod_id')]]=$avg_rate/$exchange_rate;
					}
					else if($invRow[csf('currency_id')]==2)//USD
					{
						$avg_rate=$invRow[csf('amnt')]/$invRow[csf('qty')];
						$receive_array[$invRow[csf('prod_id')]]=$avg_rate;
					}
					else
					{
						
						$receive_array[$invRow[csf('prod_id')]]=$avg_rate;
					}
				}
				unset($resultReceive);
				
				$yarnTrimsDataArray=sql_select("select b.po_breakdown_id, b.prod_id, a.item_category, 
					sum(CASE WHEN a.transaction_type=2 and b.entry_form ='3' and b.trans_type=2 and b.issue_purpose!=2 THEN b.quantity ELSE 0 END) AS yarn_iss_qty,
					sum(CASE WHEN a.transaction_type=4 and b.entry_form ='9' and b.trans_type=4 THEN b.quantity ELSE 0 END) AS yarn_iss_return_qty,
					sum(CASE WHEN a.transaction_type=5 and b.entry_form ='11' and b.trans_type=5 THEN b.quantity ELSE 0 END) AS trans_in_qty_yarn,
					sum(CASE WHEN a.transaction_type=6 and b.entry_form ='11' and b.trans_type=6 THEN b.quantity ELSE 0 END) AS trans_out_qty_yarn,
					sum(CASE WHEN a.transaction_type=2 and b.entry_form ='25' and b.trans_type=2 THEN a.cons_rate*b.quantity ELSE 0 END) AS trims_issue_amnt
					from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.item_category in(1,4) and a.transaction_type in(2,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 $po_cond_for_in4  group by b.po_breakdown_id, b.prod_id, a.item_category");
				foreach($yarnTrimsDataArray as $invRow)
				{
					if($invRow[csf('item_category')]==1)
					{
						$iss_qty=$invRow[csf('yarn_iss_qty')]+$invRow[csf('trans_in_qty_yarn')]-$invRow[csf('yarn_iss_return_qty')]-$invRow[csf('trans_out_qty_yarn')];
						$rate='';
						if($receive_array[$invRow[csf('prod_id')]]>0)
						{
							$rate=$receive_array[$invRow[csf('prod_id')]]/$exchange_rate;
						}
						else
						{
							$rate=$avg_rate_array[$invRow[csf('prod_id')]]/$exchange_rate;
						}
						
						$iss_amnt=$iss_qty*$rate;
						$yarnTrimsCostArray[$invRow[csf('po_breakdown_id')]][1]+=$iss_amnt;
					}
					else
					{
						$yarnTrimsCostArray[$invRow[csf('po_breakdown_id')]][4]+=$invRow[csf('trims_issue_amnt')];
					}
				}
				unset($yarnTrimsDataArray);
				
				$pi_number_check=array();
				
				$sqlPi=sql_select("select c.po_break_down_id, sum(b.quantity) as quantity from  com_pi_master_details a, com_pi_item_details b,wo_booking_dtls c where a.id=b.pi_id and b.work_order_no=c.booking_no  and a.item_category_id=2 and a.status_active=1 and c.status_active=1 and a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0 $po_cond_for_in5  group by c.po_break_down_id");
				
				foreach($sqlPi as $rowPi){
					$pi_number_check[$rowPi[csf('po_break_down_id')]]=$rowPi[csf('po_break_down_id')];
				}
				unset($sqlPi);
				
				
				$LabtestcostArray=array();
				$labtestcostData=sql_select("select b.order_id as po_id, (a.wo_value) as amnt from wo_labtest_dtls a,wo_labtest_order_dtls b where a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.entry_form=274 $po_cond_for_in3");
				//echo "select b.order_id as po_id, (b.wo_value) as amnt from wo_labtest_dtls a,wo_labtest_order_dtls b where a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.entry_form=274 $po_cond_for_in3";die;
				foreach($labtestcostData as $row)
				{
					$LabtestcostArray[$row[csf('po_id')]]=$row[csf('amnt')];
				}
				unset($labtestcostData);
				
				$i=1; $tot_po_qnty=0; $tot_po_value=0; $tot_ex_factory_qnty=0; $tot_ex_factory_val=0; $tot_yarn_cost_mkt=0; $tot_knit_cost_mkt=0; $tot_dye_finish_cost_mkt=0; $tot_yarn_dye_cost_mkt=0; $tot_aop_cost_mkt=0; $tot_trims_cost_mkt=0; $tot_embell_cost_mkt=0; $tot_wash_cost_mkt=0; $tot_commission_cost_mkt=0; $tot_comm_cost_mkt=0; $tot_freight_cost_mkt=0; $tot_test_cost_mkt=0; $tot_inspection_cost_mkt=0; $tot_currier_cost_mkt=0; $tot_cm_cost_mkt=0; $tot_mkt_all_cost=0; $tot_mkt_margin=0; $tot_yarn_cost_actual=0; $tot_knit_cost_actual=0; $tot_dye_finish_cost_actual=0; $tot_yarn_dye_cost_actual=0; $tot_aop_cost_actual=0; $tot_trims_cost_actual=0; $tot_embell_cost_actual=0; $tot_wash_cost_actual=0; $tot_commission_cost_actual=0; $tot_comm_cost_actual=0; $tot_freight_cost_actual=0; $tot_test_cost_actual=0; $tot_inspection_cost_actual=0; $tot_currier_cost_actual=0; $tot_cm_cost_actual=0; $tot_actual_all_cost=0; $tot_actual_margin=0; $tot_fabric_purchase_cost_mkt=0; $tot_fabric_purchase_cost_actual=0;
				
				
				/*$JobArr=array();
				foreach($result as $row_yarn){
					$JobArr[]=$row_yarn[csf('job_no')];
				}
				$yarn= new yarn($JobArr,'job');
				$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
				$yarn->unsetDataArray();*/
				
				$condition= new condition();
				$condition->company_name("=$company_name");
				 //$all_po_ids=implode(",",array_unique(explode(",",$all_po_id)));
				if(isset($po_ids))
				{
					//$condition->job_no("in($all_jobs)");
					$condition->po_id_in(implode(",",$po_ids));
				}
				
				if(str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
				{
					$start_date=str_replace("'","",$txt_date_from);
					$end_date=str_replace("'","",$txt_date_to);

					if($cbo_date_type==1){
						$condition->pub_shipment_date(" between '$start_date' and '$end_date'");
					}
					 //and b.po_received_date between '$start_date' and '$end_date' 
					// echo 'FFGG';
				}
				$condition->init();
				
				$yarn= new yarn($condition);
				
				$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
				$conversion= new conversion($condition);
				$conversion_costing_arr_process=$conversion->getAmountArray_by_orderAndProcess();
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
				$fabric= new fabric($condition);
				// echo $fabric->getQuery();die;
				$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
				
				//print_r($fabric_costing_arr);die;
				$not_yarn_dyed_cost_arr=array(1,2,30,35);$not_ship_chk_arr=array(3);

				$style_wise_data=array();
				$i=0;
				foreach($result_2 as $row)
				{
					$last_ex_factory_date=$last_ex_factory_date_arr[$row[csf("job_id")]];
					$shipId=$shipId_arr[$row[csf("id")]];
					$last_ex_factory_date=strtotime($last_ex_factory_date_arr[$row[csf("job_id")]]);
					$ex_factoryQty=$ex_factory_arr[$row[csf("id")]];
					
					$style_jobData=$row[csf('style_ref_no')].'**'.$row[csf('job_no')];
					
					if($fullship_job_po_chk_arr[$row[csf("job_no")]] && $shipId==3 && $ex_factoryQty>0)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$po_ids_array[]=$row[csf('id')];
						$gmts_item='';
						$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
						foreach($gmts_item_id as $item_id)
						{
							if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
						}

						$order_qnty_in_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
						$plan_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];
						$unit_price=$row[csf('unit_price')]/$row[csf('ratio')];
						$po_value=$order_qnty_in_pcs*$unit_price;

						$tot_po_qnty+=$order_qnty_in_pcs; 
						$tot_po_value+=$po_value;

						$ex_factory_qty=$ex_factory_arr[$row[csf("id")]];
						$ex_factory_date=$ex_factory_date_arr[$row[csf('id')]];
						$ex_factory_value=$ex_factory_qty*$unit_price;
						$tot_ex_factory_qnty+=$ex_factory_qty; 
						$tot_ex_factory_val+=$ex_factory_value; 

						$dzn_qnty=0;
						$costing_per_id=$fabriccostArray[$row[csf('job_no')]]['costing_per_id'];
						if($costing_per_id==1) $dzn_qnty=12;
						else if($costing_per_id==3) $dzn_qnty=12*2;
						else if($costing_per_id==4) $dzn_qnty=12*3;
						else if($costing_per_id==5) $dzn_qnty=12*4;
						else $dzn_qnty=1;

						$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
						$trims_cost_mkt=$trims_costing_arr[$row[csf('id')]];

						$print_amount=$emblishment_costing_arr_name[$row[csf('id')]][1];
						$embroidery_amount=$emblishment_costing_arr_name[$row[csf('id')]][2];
						$special_amount=$emblishment_costing_arr_name[$row[csf('id')]][4];
						$wash_cost=$emblishment_costing_arr_name_wash[$row[csf('id')]][3];
						$other_amount=$emblishment_costing_arr_name[$row[csf('id')]][5];
						$foreign_cost=$commission_costing_arr[$row[csf('id')]][1];
						$local_cost=$commission_costing_arr[$row[csf('id')]][2];

						$comm_cost_mkt=$commercial_costing_arr[$row[csf('id')]];

						$test_cost=$other_costing_arr[$row[csf('id')]]['lab_test'];
						$freight_cost=$other_costing_arr[$row[csf('id')]]['freight'];
						$inspection_cost=$other_costing_arr[$row[csf('id')]]['inspection'];
						$certificate_cost=$other_costing_arr[$row[csf('id')]]['certificate_pre_cost'];
						$currier_cost=$other_costing_arr[$row[csf('id')]]['currier_pre_cost'];
						$design_cost=$other_costing_arr[$row[csf('id')]]['design_cost'];

						$mkt_other_cost=$test_cost+$freight_cost+$inspection_cost+$currier_cost+$design_cost;
					//$ship_chk_arr[$row[csf("style_ref_no")]]

						$style_wise_data[$style_jobData]['commercial']+=$comm_cost_mkt;
						$style_wise_data[$style_jobData]['lab_test']+=$test_cost;
						$style_wise_data[$style_jobData]['freight']+=$freight_cost;
						$style_wise_data[$style_jobData]['inspection']+=$inspection_cost;
						$style_wise_data[$style_jobData]['currier_pre_cost']+=$currier_cost;
						$style_wise_data[$style_jobData]['design_cost']+=$design_cost;

						$cm_cost=$other_costing_arr[$row[csf('id')]]['cm_cost'];
						$comm_cost_actual=$actualCostArray[6][$row[csf('id')]];

						if($i==0){
							$ac_id .=$row[csf('id')];
							$i=1;
						}else{
							$ac_id .=','.$row[csf('id')];
						}


						$freight_cost_actual=$actualCostArray2[2][$row[csf('id')]];
						$inspection_actual=$actualCostArray2[3][$row[csf('id')]];
						$currier_pre_cost_actual=$actualCostArray2[4][$row[csf('id')]];
						$comm_cost_actual=$actualCostArray2[6][$row[csf('id')]];
						$design_cost_actual=$actualCostArray2[7][$row[csf('id')]];
						$lab_test=$LabtestcostArray[$row[csf('id')]];
						$other_cost_actual=$freight_cost_actual+$inspection_actual+$currier_pre_cost_actual+$design_cost_actual+$lab_test;
					//echo $comm_cost_actual.'A'.$freight_cost_actual.'='.$inspection_actual.'='.$currier_pre_cost_actual.'='.$design_cost_actua.'='.$lab_test."<br>";
						$style_wise_data[$style_jobData]['a_commercial']+=$comm_cost_actual;
						$style_wise_data[$style_jobData]['a_lab_test']+=$lab_test;
						$style_wise_data[$style_jobData]['a_freight']+=$freight_cost_actual;
						$style_wise_data[$style_jobData]['a_inspection']+=$inspection_actual;
						$style_wise_data[$style_jobData]['a_currier_pre_cost']+=$currier_pre_cost_actual;
						$style_wise_data[$style_jobData]['a_design_cost']+=$design_cost_actual;

						$style_wise_data[$style_jobData]['last_ex_fact_date'].=$last_ex_factory_date_arr[$row[csf("job_id")]].',';

						$embell_cost_mkt=$print_amount+$embroidery_amount+$special_amount+$other_amount;
						$wash_cost_mkt=$wash_cost;
						$commission_cost_mkt=$foreign_cost+$local_cost;

						$cm_cost_mkt=$cm_cost;
						$fabric_purchase_cost_mkt=0;
						$fabric_purchase_cost_mkt=array_sum($fabric_costing_arr['knit']['grey'][$row[csf('id')]])+array_sum($fabric_costing_arr['woven']['grey'][$row[csf('id')]]);
					//$fabric_purchase_cost_mkt=array_sum($fabric_costing_arr['woven']['grey'][$row[csf('id')]]);

						$mkt_all_cost=$cm_cost_mkt+$fabric_purchase_cost_mkt+$trims_cost_mkt+$embell_cost_mkt+$wash_cost_mkt+$comm_cost_mkt+$mkt_other_cost;
						$mkt_margin=$po_value-$mkt_all_cost;
						$mkt_margin_perc=($mkt_margin/$po_value)*100;
					$trims_cost_actual=$style_act_trims_cost_arr[$row[csf('job_no')]];//$actualTrimsCostArray[$row[csf('id')]];
					
					$embell_cost_actual=$style_act_embl_cost_arr[$row[csf('job_no')]];//$embellCostArray[$row[csf('id')]];
					$wash_cost_actual=$style_act_wash_cost_arr[$row[csf('job_no')]];//$washCostArray[$row[csf('id')]];
					$commission_cost_actual=($ex_factory_qty/$dzn_qnty)*$fabriccostArray[$row[csf('job_no')]]['commission'];
					$cm_cost_actual=$actualCostArray2[5][$row[csf('id')]];
					$fabric_purchase_cost_actual=$style_act_fab_cost_arr[$row[csf('job_no')]];//$finish_purchase_amnt_arr[$row[csf('id')]];
					$actual_all_cost=$cm_cost_actual+$fabric_purchase_cost_actual+$trims_cost_actual+$embell_cost_actual+$wash_cost_actual+$comm_cost_actual+$other_cost_actual;
					
					$style_wise_data[$style_jobData]['buyer_name'].=$buyer_arr[$row[csf('buyer_name')]]."***";
					$style_wise_data[$style_jobData]['ex_factory_qty']+=$ex_factory_qty;
					$style_wise_data[$style_jobData]['ex_factory_val']+=$ex_factory_qty*$row[csf('unit_price')];
					
					$poQty=$style_job_po_wise_arr[$row[csf("id")]]['poQty'];
					$poVal=$style_job_po_wise_arr[$row[csf("id")]]['poVal'];
					//echo $embell_cost_actual.'D';
					$style_wise_data[$style_jobData]['budget_fabrics_cost_per_pcs']+=$fabric_purchase_cost_mkt;
					$style_wise_data[$style_jobData]['budget_accessories_cost_per_pcs']+=$trims_cost_mkt;
					$style_wise_data[$style_jobData]['budget_embellishment_cost']+=$embell_cost_mkt;
					$style_wise_data[$style_jobData]['budget_washing_cost']+=$wash_cost_mkt;
					$style_wise_data[$style_jobData]['budget_cm_per_pcs']+=$cm_cost_mkt;
					$style_wise_data[$style_jobData]['budget_oparational_cost_per_pcs']+=$comm_cost_mkt+$mkt_other_cost;
					$style_wise_data[$style_jobData]['budget_total_cost_per_cost']+=$mkt_all_cost;
					//echo $comm_cost_actual.'='.$other_cost_actual.', ';;
					$style_wise_data[$style_jobData]['actual_fabrics_cost_per_pcs']=$fabric_purchase_cost_actual;
					$style_wise_data[$style_jobData]['actual_accessories_cost_per_pcs']=$trims_cost_actual;
					$style_wise_data[$style_jobData]['actual_embellishment_cost']=$embell_cost_actual;
					$style_wise_data[$style_jobData]['actual_washing_cost']=$wash_cost_actual;
					$style_wise_data[$style_jobData]['actual_cm_per_pcs']+=$cm_cost_actual;
					$style_wise_data[$style_jobData]['actual_oparational_cost_per_pcs']+=$comm_cost_actual+$other_cost_actual;
					
					$tot_opCostArr[$style_jobData]+=$cm_cost_actual;

					$style_wise_data[$style_jobData]['actual_total_cost_per_cost']+=$actual_all_cost;
					$style_wise_data[$style_jobData]['actual_sales_price_per_pcs']+=$unit_price;
					$style_wise_data[$style_jobData]['job_no']=$row[csf('job_no')];
					$style_wise_data[$style_jobData]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
					//$style_wise_data[$row[csf('style_ref_no')]]['po_id'].=$row[csf('id')].",";
					$style_wise_data[$style_jobData]['costing_date']=$row[csf('costing_date')];
					//$style_wise_data[$row[csf('style_ref_no')]]['pono'].=$row[csf('po_number')].",";
					$style_wise_data[$style_jobData]['styleQtyPcs']+=$poQty;
					$style_wise_data[$style_jobData]['styleVal']+=$poVal;
					$style_wise_data[$style_jobData]['full']='Full Delivery';
					// $style_wise_data[$row[csf('style_ref_no')]]['styleVal']+=$row[csf('po_total_price')];
					$style_wise_data[$style_jobData]['id']=$row[csf('id')];
					$style_wise_data[$style_jobData]['unit_price'] +=$row[csf('unit_price')];
					 
					} //full ship check end
				}
				//echo $tot_opCost;	
			//print_r($tot_opCostArr);
			$gaTotCost=0;
				foreach ($style_wise_data as $style_ref_no_job => $val) 
				{
					$style_ref_noArr=explode("**",$style_ref_no_job);
					$style_ref_no=$style_ref_noArr[0];
					$job_no=$style_ref_noArr[1];
					
					//$job_no=$val['job_no'];
					$job_no_prefix_num=$val['job_no_prefix_num'];
					//$po_id=$style_wise_data2[$job_no]['po_id'];
					$pono=$style_wise_data2[$job_no]['pono'];
					$ponos=implode(",",array_unique(explode(",",$pono)));
					$po_id=chop($style_wise_data2[$job_no]['po_id'],",");
					$ex_fact_dates=chop($val['last_ex_fact_date'],",");
					$ex_fact_datesArr=array_unique(explode(",",$ex_fact_dates));
					$last_ex_fact_date=max($ex_fact_datesArr);
					$costing_date=change_date_format($val['costing_date']);
					$buyer=implode(",", array_unique(explode("***", chop($val['buyer_name'],"***"))));
					//$shippingStatus="";
					//if($val['partial']!="") $shippingStatus="Partial Delivery";
					////if($val['pending']!="" && $shippingStatus=="") $shippingStatus="Pending";
					//if($val['full']!="" && $shippingStatus=="") $shippingStatus="Full Delivery";
					$shippingStatus="Full Delivery";
					$stylePrice=$val['styleVal']/$val['styleQtyPcs'];
					$fob=$val['ex_factory_qty']*$stylePrice;
					$val['actual_total_cost_per_cost']=0;
					
					$val['actual_total_cost_per_cost']=$val['actual_fabrics_cost_per_pcs']+$val['actual_accessories_cost_per_pcs']+$val['actual_embellishment_cost']+$val['actual_washing_cost']+$val['actual_cm_per_pcs']+$val['actual_oparational_cost_per_pcs'];
					
					$budgetedProfitLoss=$fob-$val['budget_total_cost_per_cost'];
					$actualProfitLoss=$fob-$val['actual_total_cost_per_cost'];
					$variance=$val['budget_total_cost_per_cost']-$val['actual_total_cost_per_cost'];//val['ex_factory_val'])-$val['actual_total_cost_per_cost']
					$marginVal=$val['ex_factory_val']-$val['actual_total_cost_per_cost'];
					$marginPer=($val['actual_total_cost_per_cost']/$val['budget_total_cost_per_cost'])*100;
					//if($val['ex_factory_qty'] !==0){
					?>
					<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');" id="tr_<?=$i; ?>" style="font-size:12px">
						<td width="30" align="center"><?=$i; ?></td>
						<td width="120" style="word-break:break-all;"><?=$buyer; ?></td>
						<td width="110" style="word-break:break-all;" title="Job=<?=$job_no;?>,Costing Date=<?=$costing_date;?>"><?=$style_ref_no; ?></td>
						<td width="180" title="<? //echo $po_id;?>" style="word-break:break-all;" align="center"><p><?=$ponos; ?>&nbsp;</p><!--<a href="##" onClick="generate_po_popup('po_popup','<?//=$po_id; ?>','<?//=chop($val['pono'],","); ?>','650px')"></a>--></td>
						<td width="100" style="word-break:break-all;"><?=$shippingStatus; ?></td>
						<td width="110" style="word-break:break-all;" align="right"><?=number_format($style_job_wise_qty[$val['job_no']],0,'.',''); ?></td>
						<td width="110" style="word-break:break-all;" align="right"><?=number_format($style_job_wise_val[$val['job_no']],2,'.',''); ?></td>
						<td width="110" style="word-break:break-all;" align="right"><?=number_format($val['styleQtyPcs'],0,'.',''); ?></td>
						<td width="110" style="word-break:break-all;" align="right"><?=number_format($val['styleVal'],2,'.',''); ?></td>
						<td width="100" style="word-break:break-all;" align="right"><a href="##" onClick="generate_ex_factory_popup('style_ex_factory_popup','<?=$job_no_prefix_num;?>','<?=$po_id; ?>','<?=str_replace("'","",$txt_date_from) ?>','<?=str_replace("'","",$txt_date_to) ?>','650px')"><?=number_format($val['ex_factory_qty'],0,'.',''); ?></a></td>
						<td width="100" style="word-break:break-all;" align="right"><?=change_date_format($last_ex_fact_date); ?></td>

						<td width="80" title="<? echo $val['budget_fabrics_cost_per_pcs'];?>" style="word-break:break-all;" align="right"><?=number_format($val['budget_fabrics_cost_per_pcs'],2,'.',''); ?></td>
						<td width="80" title="<? echo $val['budget_accessories_cost_per_pcs'];?>" style="word-break:break-all;" align="right"><?=number_format($val['budget_accessories_cost_per_pcs'],2,'.',''); ?></td>
						<td width="80" title="<? echo $val['budget_embellishment_cost'];?>" style="word-break:break-all;" align="right"><?=number_format($val['budget_embellishment_cost'],2,'.',''); ?></td>
						<td width="80" title="<? echo $val['budget_washing_cost'];?>" style="word-break:break-all;" align="right"><?=number_format($val['budget_washing_cost'],2,'.',''); ?></td>
						<td width="80" title="<? echo $val['budget_cm_per_pcs'];?>" style="word-break:break-all;" align="right"><?=number_format($val['budget_cm_per_pcs'],2,'.',''); ?></td>
						<td width="80" title="<? echo $val['budget_oparational_cost_per_pcs'];?>" style="word-break:break-all;" align="right"><a href="##" onClick="generate_budget_op_cost_popup('budget_oparational_cost_popup','<?=$val['commercial'];?>','<?=$val['lab_test'];?>','<?=$val['freight'];?>','<?=$val['inspection']; ?>','<?=$val['currier_pre_cost']; ?>','<?=$val['design_cost']; ?>','650px')"><?=number_format($val['budget_oparational_cost_per_pcs'],2,'.','');
						


					?></a></td>
					<td width="80" title="<? echo $val['budget_total_cost_per_cost'];?>" style="word-break:break-all;" align="right"><?=number_format($val['budget_total_cost_per_cost'],2,'.',''); ?></td>

					<td width="80" title="<? echo $val['actual_fabrics_cost_per_pcs'];?>" style="word-break:break-all;" align="right" ><a href="#report_details" onClick="openmypage_actual('<?=$po_id; ?>','fabric_purchase_cost_actual','Fabric Purchase Cost Details','900px')"><?=number_format($val['actual_fabrics_cost_per_pcs'],2,'.',''); ?></a></td>
					<td width="80" title="<? echo $val['actual_accessories_cost_per_pcs'];?>" style="word-break:break-all;" align="right"><a href="#report_details" onClick="openmypage_actual('<?=$po_id; ?>','trims_cost_actual','Trims Cost Details','800px')"><?=number_format($val['actual_accessories_cost_per_pcs'],2,'.',''); ?></a></td>
					<td width="80" title="<? echo $val['actual_embellishment_cost'];?>" style="word-break:break-all;" align="right"><a href="#report_details" onClick="openmypage_actual('<?=$po_id; ?>','embell_cost_actual','Embellishment Cost Details','800px')"><?=number_format($val['actual_embellishment_cost'],2,'.',''); ?></a></td>
					<td width="80" title="<? echo $val['actual_washing_cost'];?>" style="word-break:break-all;" align="right"><a href="#report_details" onClick="openmypage_actual('<?=$po_id; ?>','wash_cost_actual','Wash Cost Details','800px')"><?=number_format($val['actual_washing_cost'],2,'.',''); ?></a></td>
					<td width="80" title="<? echo $val['actual_cm_per_pcs'];?>" style="word-break:break-all;" align="right"><?=number_format($val['actual_cm_per_pcs'],2,'.',''); ?></td>
					<td width="80" title="Opt.Cost=<? echo $val['actual_oparational_cost_per_pcs'];?>" style="word-break:break-all;" align="right"><a href="##" onClick="generate_actual_op_cost_popup('actual_oparational_cost_popup','<?=$val['a_commercial'];?>','<?=$val['a_lab_test'];?>','<?=$val['a_freight'];?>','<?=$val['a_inspection']; ?>','<?=$val['a_currier_pre_cost']; ?>','<?=$val['a_design_cost']; ?>','650px')"><?=number_format($val['actual_oparational_cost_per_pcs'],2,'.',''); ?></a></td>
					<td width="80" style="word-break:break-all;" align="right"><?=number_format($val['actual_total_cost_per_cost'],2,'.',''); ?></td>

					<td width="100" style="word-break:break-all;" align="right" title="<?=$val['ex_factory_qty'].'*('.$val['styleVal'].'/'.$val['styleQtyPcs'].')'; ?>"><?=number_format($fob,2,'.','');?></td>
					<td width="80" style="word-break:break-all;" align="right" title="<?=$fob.'-'.$val['budget_total_cost_per_cost']; ?>"><?=number_format($budgetedProfitLoss,2,'.','');?></td>
					<td width="80" style="word-break:break-all;" align="right" title="<?=$fob.'-'.$val['actual_total_cost_per_cost']; ?>"><?=number_format($actualProfitLoss,2,'.','');?></td>
					<td width="80" style="word-break:break-all;" align="right" title="<?=$val['budget_total_cost_per_cost'].'-'.$val['actual_total_cost_per_cost']; ?>"><?=number_format($variance,2,'.','');?></td>
					<td width="80" style="word-break:break-all;" align="right" title="<?=($val['ex_factory_val']).'-'.$val['actual_total_cost_per_cost']; ?>"><?=number_format(($val['ex_factory_val'])-$val['actual_total_cost_per_cost'],2,'.','');?></td>
					<td style="word-break:break-all;" align="right" title="<?='('.$val['actual_total_cost_per_cost'].'/'.$val['budget_total_cost_per_cost'].')*100'; ?>"><?=number_format($marginPer,2,'.',''); ?></td>
				</tr>
				<?
				
				$i++;
				$gStyleQty+=$val['styleQtyPcs'];
				$gStyleValue+=$val['styleVal'];
				$gExQty+=$val['ex_factory_qty'];
				$gbFabCost+=$val['budget_fabrics_cost_per_pcs'];
				$gbAccCost+=$val['budget_accessories_cost_per_pcs'];
				$gbEmbCost+=$val['budget_embellishment_cost'];
				$gbWashCost+=$val['budget_washing_cost'];
				$gbCmCost+=$val['budget_cm_per_pcs'];
				$gbOpeCost+=$val['budget_oparational_cost_per_pcs'];
				$gbTotCost+=$val['budget_total_cost_per_cost'];

				$gaFabCost+=$val['actual_fabrics_cost_per_pcs'];
				$gaAccCost+=$val['actual_accessories_cost_per_pcs'];
				$gaEmbCost+=$val['actual_embellishment_cost'];
				$gaWashCost+=$val['actual_washing_cost'];
				$gaCmCost+=$val['actual_cm_per_pcs'];
				$gaOpeCost+=$val['actual_oparational_cost_per_pcs'];
				$gaTotCost+=$val['actual_total_cost_per_cost'];
				$gaTotActCost+=$val['actual_total_cost_per_cost'];

				$gFob+=$fob;
				$gbudgetedProfitLoss+=$budgetedProfitLoss;
				$gactualProfitLoss+=$actualProfitLoss;
				$gvariance+=$variance;
				$gmarginVal+=$marginVal;

				}//}
				$avgMarginPer=($gaTotCost/$gbTotCost)*100;
				?>
			</table>
		</div>
		<table class="tbl_bottom" width="<? echo $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
			<tr style="font-size:12px">
				<td width="30">&nbsp;</td>
				<td width="120">&nbsp;</td>
				<td width="110">&nbsp;</td>
				<td width="180">&nbsp;</td>
				<td width="100" align="right">Total:</td>
				<td width="110" align="right" style="word-break:break-all;" id="td_jobQty"><?=number_format($gStyleQty,0,'.',''); ?></td>
				<td width="110" align="right" style="word-break:break-all;" id="value_jobVal"><?=number_format($gStyleValue,2,'.',''); ?></td>
				<td width="110" align="right" style="word-break:break-all;" id="td_styleQty"><?=number_format($gStyleQty,0,'.',''); ?></td>
				<td width="110" align="right" style="word-break:break-all;" id="value_styleVal"><?=number_format($gStyleValue,2,'.',''); ?></td>
				<td width="100" align="right" style="word-break:break-all;" id="td_exQty"><?=number_format($gExQty,0,'.',''); ?></td>
				<td width="100" align="right" style="word-break:break-all;" id=""><? //number_format($gExQty,0,'.',''); ?></td>

				<td width="80" align="right" style="word-break:break-all;" id="value_fabbom"><?=number_format($gbFabCost,2,'.',''); ?></td>
				<td width="80" align="right" style="word-break:break-all;" id="value_accbom"><?=number_format($gbAccCost,2,'.',''); ?></td>
				<td width="80" align="right" style="word-break:break-all;" id="value_embbom"><?=number_format($gbEmbCost,2,'.',''); ?></td>
				<td width="80" align="right" style="word-break:break-all;" id="value_washbom"><?=number_format($gbWashCost,2,'.',''); ?></td>
				<td width="80" align="right" style="word-break:break-all;" id="value_cmbom"><?=number_format($gbCmCost,2,'.',''); ?></td>
				<td width="80" align="right" style="word-break:break-all;" id="value_opbom"><?=number_format($gbOpeCost,2,'.',''); ?></td>
				<td width="80" align="right" style="word-break:break-all;" id="value_totbom"><?=number_format($gbTotCost,2,'.',''); ?></td>

				<td width="80" align="right" style="word-break:break-all;" id="value_fabact"><?=number_format($gaFabCost,2,'.',''); ?></td>
				<td width="80" align="right" style="word-break:break-all;" id="value_accact"><?=number_format($gaAccCost,2,'.',''); ?></td>
				<td width="80" align="right" style="word-break:break-all;" id="value_embact"><?=number_format($gaEmbCost,2,'.',''); ?></td>
				<td width="80" align="right" style="word-break:break-all;" id="value_washact"><?=number_format($gaWashCost,2,'.',''); ?></td>
				<td width="80" align="right" style="word-break:break-all;" id="value_cmact"><?=number_format($gaCmCost,2,'.',''); ?></td>
				<td width="80" align="right" style="word-break:break-all;" id="value_opeact"><?=number_format($gaOpeCost,2,'.',''); ?></td>
				<td width="80" align="right" style="word-break:break-all;" id="value_totact"><?=number_format($gaTotActCost,2,'.',''); ?></td>

				<td width="100" align="right" style="word-break:break-all;" id="value_fob"><?=number_format($gFob,2,'.',''); ?></td>
				<td width="80" align="right" style="word-break:break-all;" id="value_bprofitloss"><?=number_format($gbudgetedProfitLoss,2,'.',''); ?></td>
				<td width="80" align="right" style="word-break:break-all;" id="value_aprofitloss"><?=number_format($gactualProfitLoss,2,'.',''); ?></td>
				<td width="80" align="right" style="word-break:break-all;" id="value_variance"><?=number_format($gvariance,2,'.',''); ?></td>
				<td width="80" align="right" style="word-break:break-all;" id="value_marginval"><?=number_format($gmarginVal,2,'.',''); ?></td>
				<td align="right" style="word-break:break-all;" id="value_marginper"><?=number_format($avgMarginPer,2,'.',''); ?></td>
			</tr>
		</table>
	</fieldset>

<? }
$html = ob_get_contents();
ob_clean();
	//$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
}
	//---------end------------//
$name=time();
$filename=$user_name."_".$name.".xls";
$create_new_doc = fopen($filename, 'w');	
$is_created = fwrite($create_new_doc, $html);
echo "$html****$filename****$report_type"; 
	//echo "$total_data****$filename";
exit();
}



if($action=="fabric_purchase_cost_actual")
{
	echo load_html_head_contents("Fabric Purchase Cost Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);
	//$ex_rate=80;
	?>
	<script>

		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

			d.close();
		}

	</script>
	<div style="width:860px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:860px; margin-left:7px">
		<div id="report_container">
			<u><b>Woven Fabric Purchase</b></u>
			<table class="rpt_table" width="855" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<th width="40">SL</th>
					<th width="110">Receive Id</th>
					<th width="80">Receive Date</th>
					<th width="280">Fabric Description</th>
					<th width="110">Receive Qty.</th>
					<th width="110">Avg. Rate (USD)</th>
					<th>Cost ($)</th>
				</thead>
				<?
				$i=1; $total_recv_qnty_w=0; $total_recv_cost_w=0;



				$sql="select a.id, a.recv_number, a.receive_date, sum(b.quantity) as recv_qnty, sum(d.cons_rate*b.quantity) as amnt, c.id, c.product_name_details,d.cons_rate from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=1 and d.item_category=3 and c.item_category_id=3 and d.id=b.trans_id and b.trans_type=1 and b.entry_form=17 and a.entry_form=17  and b.po_breakdown_id in($po_id)  and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.id, a.recv_number, a.receive_date, c.product_name_details,d.cons_rate";
					//echo $sql;
				$result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";

					$total_recv_qnty_w+=$row[csf('recv_qnty')];

					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td width="40"><? echo $i; ?></td>
						<td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
						<td width="80" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
						<td width="280"><p><? echo $row[csf('product_name_details')]; ?></p></td>
						<td align="right" width="110">
							<?
							echo number_format($row[csf('recv_qnty')],2);
							?>
						</td>
						<td align="right" width="110">
							<?
							$avg_rate=($row[csf('amnt')]/$row[csf('recv_qnty')])/$ex_rate;
							echo number_format($avg_rate,2);
						?>&nbsp;
					</td>
					<td align="right">
						<?
						$cost=$row[csf('recv_qnty')]*$avg_rate;
									//$cost=$row[csf('amnt')]/$ex_rate;
						$total_recv_cost_w+=$cost;
						echo number_format($cost,2);
						?>
					</td>
				</tr>
				<?
				$i++;
			}
			?>
			<tfoot>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th align="right">Total</th>
				<th align="right"><? echo number_format($total_recv_qnty_w,2); ?></th>
				<th>&nbsp;</th>
				<th align="right"><? echo number_format($total_recv_cost_w,2); ?></th>
			</tfoot>
		</table>
		<table class="tbl_bottom" width="855" cellpadding="0" cellspacing="0" border="1" rules="all">
			<tr>
				<td width="40">&nbsp;</td>
				<td width="110">&nbsp;</td>
				<td width="80">&nbsp;</td>
				<td width="280">&nbsp;</td>
				<td width="110">&nbsp;</td>
				<td width="110">Grand Total</td>
				<td align="right"><? echo number_format($total_recv_cost+$total_recv_cost_w,2); ?></td>
			</tr>
		</table>
	</div>
</fieldset>
<?
exit();
}


if($action=="trims_cost_actual")
{
	echo load_html_head_contents("AOP Cost Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);

	?>
	<fieldset style="width:760px; margin-left:7px">
		<table class="rpt_table" width="755" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
				<th width="40">SL</th>
				<th width="110">WO NO.</th>
				<th width="80">WO Date</th>
				<th width="80">Currency</th>
				<th width="120">Amount (Taka)</th>
				<th width="120">Conversion rate</th>
				<th>Amount (USD)</th>
			</thead>
		</table>
		<div style="width:755px; max-height:330px; overflow-y:scroll" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="735" cellpadding="0" cellspacing="0">
				<?

				$sql_ex=sql_select("SELECT conversion_rate,con_date,currency,company_id FROM currency_conversion_rate WHERE status_active = 1 AND is_deleted = 0 ORDER BY con_date DESC");
				$currency_data=array();
				foreach ($sql_ex as $row) 
				{
					$currency_data[$row[csf('company_id')]][$row[csf('currency')]][$row[csf('con_date')]]=$row[csf('conversion_rate')];
				}



				$i=1; $total_trims_cost=0; $avg_rate=76;
				$sql="select a.booking_no, a.booking_date, a.currency_id, a.exchange_rate,a.company_id, sum(b.amount) as amount from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id in ($po_id) and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.booking_no, a.booking_date, a.currency_id,a.company_id, a.exchange_rate";
				$result=sql_select($sql);
				foreach($result as $row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$currency_id=$row[csf('currency_id')];
					$company_id=$row[csf('company_id')];

					$cur_data=$currency_data[$company_id][$currency_id];
					$ex_rate=0;
					foreach ($cur_data as $date => $cur) {
						if($date<=$row[csf('booking_date')])
						{
							$ex_rate=$cur;
							break;
						}
					}

					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td width="40"><? echo $i; ?></td>
						<td width="110"><p><? echo $row[csf('booking_no')]; ?></p></td>
						<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
						<td width="80"><p><? echo $currency[$row[csf('currency_id')]]; ?></p></td>
						<td align="right" width="120">
							<?

							if($row[csf('exchange_rate')]>0)
							{
								$amnt_tk=$row[csf('amount')]*$row[csf('exchange_rate')];
							}
							else{
								$amnt_tk=$row[csf('amount')]*$ex_rate;
							}
							echo number_format($amnt_tk,2);
							?>
						</td>
						<td align="right" width="120"><? 
						if($row[csf('exchange_rate')]>0)
						{
							echo number_format($row[csf('exchange_rate')],2);
						}
						else{
							echo number_format($ex_rate,2);
						} ?>&nbsp;</td>
						<td align="right">
							<?
							if($row[csf('currency_id')]==1)
							{
								$amount=$row[csf('amount')]/$row[csf('exchange_rate')];
							}
							else
							{
								$amount=$row[csf('amount')];
							}
							echo number_format($amount,2);
							?>
						</td>
					</tr>
					<?
					$i++;
					$total_trims_cost+=$amount;
				}
				?>
				<tfoot>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>Total</th>
					<th align="right"><? echo number_format($total_trims_cost,2); ?></th>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<?
	exit();
}

if($action=="embell_cost_actual")
{
	echo load_html_head_contents("Embellishment Cost Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);

	?>
	<fieldset style="width:760px; margin-left:7px">
		<table class="rpt_table" width="755" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
				<th width="40">SL</th>
				<th width="110">WO NO.</th>
				<th width="80">WO Date</th>
				<th width="80">Currency</th>
				<th width="120">Amount (Taka)</th>
				<th width="120">Conversion rate</th>
				<th>Amount (USD)</th>
			</thead>
		</table>
		<div style="width:755px; max-height:330px; overflow-y:scroll" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="735" cellpadding="0" cellspacing="0">
				<?

				$sql_ex=sql_select("SELECT conversion_rate,con_date,currency,company_id FROM currency_conversion_rate WHERE status_active = 1 AND is_deleted = 0 ORDER BY con_date DESC");
				$currency_data=array();
				foreach ($sql_ex as $row) 
				{
					$currency_data[$row[csf('company_id')]][$row[csf('currency')]][$row[csf('con_date')]]=$row[csf('conversion_rate')];
				}
				$i=1; $total_aop_cost=0; $avg_rate=76;




				$sql="select a.booking_no, a.booking_date, a.currency_id, a.exchange_rate,a.company_id, sum(b.amount) as amount from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_embe_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and c.emb_name!=3 and b.po_break_down_id in ($po_id) and a.item_category=25 and a.booking_type in(3,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.booking_no, a.booking_date, a.currency_id, a.exchange_rate, a.company_id";

				$result=sql_select($sql);
				foreach($result as $row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$currency_id=$row[csf('currency_id')];
					$company_id=$row[csf('company_id')];

					$cur_data=$currency_data[$company_id][$currency_id];
					$ex_rate=0;
					foreach ($cur_data as $date => $cur) {
						if($date<=$row[csf('booking_date')])
						{
							$ex_rate=$cur;
							break;
						}
					}

					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td width="40"><? echo $i; ?></td>
						<td width="110"><p><? echo $row[csf('booking_no')]; ?></p></td>
						<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
						<td width="80"><p><? echo $currency[$row[csf('currency_id')]]; ?></p></td>
						<td align="right" width="120">
							<?

							if($row[csf('exchange_rate')]>0)
							{
								$amnt_tk=$row[csf('amount')]*$row[csf('exchange_rate')];
							}
							else{
								$amnt_tk=$row[csf('amount')]*$ex_rate;
							}
							echo number_format($amnt_tk,2);
							?>
						</td>
						<td align="right" width="120"><?
						if($row[csf('exchange_rate')]>0)
						{
							echo number_format($row[csf('exchange_rate')],2);
						}
						else{
							echo number_format($ex_rate,2);
						}

					?>&nbsp;</td>
					<td align="right">
						<?
						if($row[csf('currency_id')]==1)
						{
							$amount=$row[csf('amount')]/$row[csf('exchange_rate')];
						}
						else
						{
							$amount=$row[csf('amount')];
						}
						echo number_format($amount,2);
						?>
					</td>
				</tr>
				<?
				$i++;
				$total_aop_cost+=$amount;
			}
			?>
			<tfoot>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>Total</th>
				<th align="right"><? echo number_format($total_aop_cost,2); ?></th>
			</tfoot>
		</table>
	</div>
</fieldset>
<?
exit();
}


if($action=="wash_cost_actual")
{
	echo load_html_head_contents("Wash Cost Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);

	?>
	<fieldset style="width:760px; margin-left:7px">
		<table class="rpt_table" width="755" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
				<th width="40">SL</th>
				<th width="110">WO NO.</th>
				<th width="80">WO Date</th>
				<th width="80">Currency</th>
				<th width="120">Amount (Taka)</th>
				<th width="120">Conversion rate</th>
				<th>Amount (USD)</th>
			</thead>
		</table>
		<div style="width:755px; max-height:330px; overflow-y:scroll" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="735" cellpadding="0" cellspacing="0">
				<?
				$i=1; $total_aop_cost=0; $avg_rate=76;
				$sql_ex=sql_select("SELECT conversion_rate,con_date,currency,company_id FROM currency_conversion_rate WHERE status_active = 1 AND is_deleted = 0 ORDER BY con_date DESC");
				$currency_data=array();
				foreach ($sql_ex as $row) 
				{
					$currency_data[$row[csf('company_id')]][$row[csf('currency')]][$row[csf('con_date')]]=$row[csf('conversion_rate')];
				}



				$sql="select a.booking_no, a.booking_date, a.currency_id, a.exchange_rate,a.company_id, sum(b.amount) as amount from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_embe_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and c.emb_name=3 and b.po_break_down_id in($po_id) and a.item_category=25 and a.booking_type in(3,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.booking_no, a.booking_date, a.currency_id, a.exchange_rate,a.company_id";
				$result=sql_select($sql);
				foreach($result as $row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$currency_id=$row[csf('currency_id')];
					$company_id=$row[csf('company_id')];

					$cur_data=$currency_data[$company_id][$currency_id];
					$ex_rate=0;
					foreach ($cur_data as $date => $cur) {
						if($date<=$row[csf('booking_date')])
						{
							$ex_rate=$cur;
							break;
						}
					}

					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td width="40"><? echo $i; ?></td>
						<td width="110"><p><? echo $row[csf('booking_no')]; ?></p></td>
						<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
						<td width="80"><p><? echo $currency[$row[csf('currency_id')]]; ?></p></td>
						<td align="right" width="120">
							<?
							if($row[csf('exchange_rate')]>0)
							{
								$amnt_tk=$row[csf('amount')]*$row[csf('exchange_rate')];
							}
							else{
								$amnt_tk=$row[csf('amount')]*$ex_rate;
							}	


							echo number_format($amnt_tk,2);
							?>
						</td>
						<td align="right" width="120"><?

						if($row[csf('exchange_rate')]>0)
						{
							echo number_format($row[csf('exchange_rate')],2);
						}
						else{
							echo number_format($ex_rate,2);
						}	



					?>&nbsp;</td>
					<td align="right">
						<?
						if($row[csf('currency_id')]==1)
						{
							$amount=$row[csf('amount')]/$row[csf('exchange_rate')];
						}
						else
						{
							$amount=$row[csf('amount')];
						}
						echo number_format($amount,2);
						?>
					</td>
				</tr>
				<?
				$i++;
				$total_aop_cost+=$amount;
			}
			?>
			<tfoot>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>Total</th>
				<th align="right"><? echo number_format($total_aop_cost,2); ?></th>
			</tfoot>
		</table>
	</div>
</fieldset>
<?
exit();
}

if($action=="ex_factory_popup")
{
	echo load_html_head_contents("Ex-Factory Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	//echo $id;//$job_no;

	$unit_price_arr=return_library_array( "select id, unit_price from wo_po_break_down where status_active=1 and is_deleted=0 and id in ($id)","id","unit_price");
	//echo "select id, unit_price from wo_po_break_down where status_active=1 and is_deleted=0 and id in ($id)";
	?>
	<script>

		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

			d.close();
		}

	</script>
	<div style="width:100%" align="center" id="report_container">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<fieldset style="width:550px">
			<div class="form_caption" align="center"><strong>Ex-Factory Details</strong></div><br />
			<div style="width:100%">
				<table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
					<thead>
						<tr>
							<th width="35">SL</th>
							<th width="90">Ex-fac. Date</th>
							<th width="120">System /Challan no</th>
							<th width="100">Ex-Fact. Del.Qty.</th>
							<th width="100">Ex-Fact. Return Qty.</th>
							<th width="">Ex-Fact. Value</th>

						</tr>
					</thead>
				</table>
			</div>
			<div style="width:100%; max-height:400px;">
				<table cellpadding="0" width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
					<?
					$i=1;
					if($cbo_date_type==2)
					{
						if($start_date !=='' & $end_date !==''){
							$date_cond2=" and b.ex_factory_date between '$start_date' and '$end_date'";
						}
					}

					$exfac_sql="select b.challan_no,b.ex_factory_date,b.po_break_down_id as po_id,
					CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_qnty,
					CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_return_qnty
					from  pro_ex_factory_mst b where  b.status_active=1 and b.is_deleted=0 and b.po_break_down_id in($id) $date_cond2";

					$sql_dtls=sql_select($exfac_sql);




					foreach($sql_dtls as $row_real)
					{
						if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
						$tot_exfact_qty=$row_real[csf("ex_factory_qnty")]-$row_real[csf("ex_factory_return_qnty")];
						/*$expoid="";
						$expoid=explode(",",$id);
						$tot_exfact_val=0;
						foreach($expoid as $pid)
						{
							$unit_price=0;
							$unit_price=$unit_price_arr[$pid];
							$tot_exfact_val+=$tot_exfact_qty*$unit_price;
						}*/
						$tot_exfact_val=$tot_exfact_qty*$unit_price_arr[$row_real[csf("po_id")]];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_l<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="35"><? echo $i; ?></td>
							<td width="90"><? echo change_date_format($row_real[csf("ex_factory_date")]); ?></td>
							<td width="120"><? echo $row_real[csf("challan_no")]; ?></td>
							<td width="100" align="right"><? echo $row_real[csf("ex_factory_qnty")]; ?></td>
							<td width="100" align="right"><? echo $row_real[csf("ex_factory_return_qnty")]; ?></td>
							<td width="" align="right" title="Rate =<? echo $unit_price_arr[$row_real[csf("po_id")]];?>"><? echo number_format($tot_exfact_val,2); ?></td>
						</tr>
						<?
						$rec_qnty+=$row_real[csf("ex_factory_qnty")];
						$rec_return_qnty+=$row_real[csf("ex_factory_return_qnty")];
						$total_exfact_val+=$tot_exfact_val;
						$i++;
					}
					?>
					<tfoot>
						<tr>
							<th colspan="3">Total</th>
							<th><? echo number_format($rec_qnty,2); ?></th>
							<th><? echo number_format($rec_return_qnty,2); ?></th>
							<th><? echo number_format($total_exfact_val,2); ?></th>
						</tr>
						<tr>
							<th colspan="3">Total Balance</th>
							<th colspan="2" align="right"><? echo number_format($rec_qnty-$rec_return_qnty,2); ?></th>
							<th><? echo number_format($total_exfact_val,2); ?></th>
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
	</div>
	<?
	exit();
}
if($action=="style_ex_factory_popup")
{
	echo load_html_head_contents("Ex-Factory Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	//echo $id;//$job_no;

	$unit_price_arr=return_library_array( "select id, unit_price from wo_po_break_down where status_active=1 and is_deleted=0 and id in ($id)","id","unit_price");
	//echo "select id, unit_price from wo_po_break_down where status_active=1 and is_deleted=0 and id in ($id)";
	?>
	<script>

		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

			d.close();
		}

	</script>
	<div style="width:100%" align="center" id="report_container">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<fieldset style="width:550px">
			<div class="form_caption" align="center"><strong>Ex-Factory Details</strong></div><br />
			<div style="width:100%">
				<table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
					<thead>
						<tr>
							<th width="35">SL</th>
							<th width="90">Ex-fac. Date</th>
							<th width="120">System /Challan no</th>
							<th width="100">Ex-Fact. Del.Qty.</th>
							<th width="100">Ex-Fact. Return Qty.</th>
							<th width="">Ex-Fact. Value</th>

						</tr>
					</thead>
				</table>
			</div>
			<div style="width:100%; max-height:400px;">
				<table cellpadding="0" width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
					<?
					$i=1;
					if($cbo_date_type==2)
					{
						if($start_date !=='' & $end_date !==''){
							$date_cond2=" and b.ex_factory_date between '$start_date' and '$end_date'";
						}
					}

					$exfac_sql="select b.challan_no,b.ex_factory_date,b.po_break_down_id as po_id,
					CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_qnty,
					CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_return_qnty
					from  pro_ex_factory_mst b where  b.status_active=1 and b.is_deleted=0 and b.po_break_down_id in($id) order by b.ex_factory_date asc ";

					$sql_dtls=sql_select($exfac_sql);




					foreach($sql_dtls as $row_real)
					{
						if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
						$tot_exfact_qty=$row_real[csf("ex_factory_qnty")]-$row_real[csf("ex_factory_return_qnty")];
						/*$expoid="";
						$expoid=explode(",",$id);
						$tot_exfact_val=0;
						foreach($expoid as $pid)
						{
							$unit_price=0;
							$unit_price=$unit_price_arr[$pid];
							//echo $tot_exfact_qty.'='.$unit_price.'<br>';
							
						}*/
						$tot_exfact_val=$tot_exfact_qty*$unit_price_arr[$row_real[csf("po_id")]];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_l<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="35"><? echo $i; ?></td>
							<td width="90"><? echo change_date_format($row_real[csf("ex_factory_date")]); ?></td>
							<td width="120"><? echo $row_real[csf("challan_no")]; ?></td>
							<td width="100" align="right"><? echo $row_real[csf("ex_factory_qnty")]; ?></td>
							<td width="100" align="right"><? echo $row_real[csf("ex_factory_return_qnty")]; ?></td>
							<td width="" align="right" title="Unit Rate=<? echo $unit_price_arr[$row_real[csf("po_id")]];?>"><? echo number_format($tot_exfact_val,2); ?></td>
						</tr>
						<?
						$rec_qnty+=$row_real[csf("ex_factory_qnty")];
						$rec_return_qnty+=$row_real[csf("ex_factory_return_qnty")];
						$total_exfact_val+=$tot_exfact_val;
						$i++;
					}
					?>
					<tfoot>
						<tr>
							<th colspan="3">Total</th>
							<th><? echo number_format($rec_qnty,2); ?></th>
							<th><? echo number_format($rec_return_qnty,2); ?></th>
							<th><? echo number_format($total_exfact_val,2); ?></th>
						</tr>
						<tr>
							<th colspan="3">Total Balance</th>
							<th colspan="2" align="right"><? echo number_format($rec_qnty-$rec_return_qnty,2); ?></th>
							<th><? echo number_format($total_exfact_val,2); ?></th>
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
	</div>
	<?
	exit();
}

if($action=="budget_oparational_cost_popup")
{
	echo load_html_head_contents("Ex-Factory Details", "../../../../", 1, 1,$unicode,'','');
	
	extract($_REQUEST);
	echo $id;//$job_no;

	$all_other_data=sql_select("select a.job_no AS job_no,a.total_set_qnty AS total_set_qnty,b.id AS id,c.item_number_id AS item_number_id,c.country_id AS country_id,c.color_number_id AS color_number_id,c.size_number_id AS size_number_id,c.order_quantity AS order_quantity ,c.plan_cut_qnty AS plan_cut_qnty ,d.id AS pre_cost_dtls_id,d.lab_test AS lab_test,d.inspection AS inspection ,d.cm_cost AS cm_cost,d.freight AS freight,d.currier_pre_cost AS currier_pre_cost,d.certificate_pre_cost AS certificate_pre_cost,d.common_oh AS common_oh,d.depr_amor_pre_cost AS depr_amor_pre_cost, d.design_cost AS design_cost, d.studio_cost AS studio_cost,d.deffdlc_cost AS deffdlc_cost, d.interest_cost AS interest_cost, d.incometax_cost AS incometax_cost, d.margin_pcs_set AS margin_pcs_set,e.amount as commercial_amount from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_dtls d ,wo_pre_cost_comarci_cost_dtls e where 1=1   and  a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and  a.id=e.job_id and b.id=c.po_break_down_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.status_active=1 and b.id='$id'");
	
	//print_r($all_other_data);

	$unit_price_arr=return_library_array( "select id, unit_price from wo_po_break_down where status_active=1 and is_deleted=0 and id in ($id)","id","unit_price");
	//echo "select id, unit_price from wo_po_break_down where status_active=1 and is_deleted=0 and id in ($id)";
	?>
	<script>

		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

			d.close();
		}

	</script>
	<div style="width:100%" align="center" id="report_container">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<fieldset style="width:350px">
			<div class="form_caption" align="center"><strong>Oparational Cost</strong></div><br />
			<div style="width:100%">
				<table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">

					<thead>
						<tr>

							<th width="120" align="left">Particulars</th>
							<th width="90">Amount</th>


						</tr>

					</thead>
					<tr>
						<td width="120">Commercial Cost</td>
						<td width="90"><?
					//    $total=$all_other_data[0][csf("commercial_amount")]+$all_other_data[0][csf("lab_test")]+$all_other_data[0][csf("inspection")]+$all_other_data[0][csf("freight")]+$all_other_data[0][csf("currier_pre_cost")]+$all_other_data[0][csf("design_cost")];
						echo $commercial; 
					?></td>
				</tr>
				<tr>
					<td width="120">Lab Test</td>
					<td width="90"><? echo $lab_test; ?></td>
				</tr>
				<tr>
					<td width="120">Inspection Cost</td>
					<td width="90"><? echo $inspection; ?></td>
				</tr>
				<tr>
					<td width="120">Freight Cost</td>
					<td width="90"><? echo $freight; ?></td>
				</tr>
				<tr>
					<td width="120">Courier Cost</td>
					<td width="90"><? echo $currier_pre_cost; ?></td>
				</tr>
				<tr>
					<td width="120">Design Cost</td>
					<td width="90"><? echo $design_cost; ?></td>
				</tr>
				<tr>
					<td width="120"><b>Total</b></td>
					<td width="90"><b><? echo $commercial+$lab_test+$inspection+$freight+$currier_pre_cost+$design_cost; ?></b></td>
				</tr>
			</table>
		</div>

	</fieldset>
</div>
<?
exit();
}



if($action=="actual_oparational_cost_popup")
{
	echo load_html_head_contents("Ex-Factory Details", "../../../../", 1, 1,$unicode,'','');
	
	extract($_REQUEST);
//	echo $id;
	//$job_no;

	
	// $labtest_other_data=sql_select("select b.order_id as po_id, (a.wo_value) as amnt from wo_labtest_dtls a,wo_labtest_order_dtls b where a.id=b.dtls_id 
	// and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=274 and b.order_id in ($id)");

	// $other_cost_data=sql_select("select cost_head,po_id,sum(amount_usd) as amount_usd from wo_actual_cost_entry where company_id=6 and status_active=1 and is_deleted=0 and po_id in ($id) group by cost_head,po_id ");
	//  echo "select b.order_id as po_id, (a.wo_value) as amnt from wo_labtest_dtls a,wo_labtest_order_dtls b where a.id=b.dtls_id 
	//  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=274 and b.order_id in ($id)";
	
	print_r($other_cost_data);

	// foreach($other_cost_data as $row){
	// 	if($row[csf("cost_head")]==2){
	// 		$freight_cost +=$row[csf("amount_usd")];
	// 	}elseif($row[csf("cost_head")]==3){
	// 		$inspection +=$row[csf("amount_usd")];
	// 	}elseif($row[csf("cost_head")]==4){
	// 		$currier_pre_cost +=$row[csf("amount_usd")];
	// 	}elseif($row[csf("cost_head")]==7){
	// 		$design_cost +=$row[csf("amount_usd")];
	// 	}elseif($row[csf("cost_head")]==6){
	// 		$comm_cost +=$row[csf("amount_usd")];
	// 	}

	// }


	// $unit_price_arr=return_library_array( "select id, unit_price from wo_po_break_down where status_active=1 and is_deleted=0 and id in ($id)","id","unit_price");
	//echo "select id, unit_price from wo_po_break_down where status_active=1 and is_deleted=0 and id in ($id)";
	?>
	<script>

		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');

			d.close();
		}

	</script>
	<div style="width:100%" align="center" id="report_container">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<fieldset style="width:250px">
			<div class="form_caption" align="center"><strong>Oparational Cost</strong></div><br />
			<div style="width:100%">
				<table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">

					<thead>
						<tr align="left">

							<th width="120" align="left">Particulars</th>
							<th width="90">Amount</th>


						</tr>

					</thead>
					<tbody>
						<tr>
							<td width="120">Commercial Cost</td>
							<td width="90"><?  echo number_format($commercial,2,'.','');  ?></td>
						</tr>
						<tr>
							<td width="120">Lab Test</td>
							<td width="90"><? echo $lab_test; ?></td>
						</tr>
						<tr>
							<td width="120">Inspection Cost</td>
							<td width="90"><? echo $inspection;?></td>
						</tr>
						<tr>
							<td width="120">Freight Cost</td>
							<td width="90"><? echo number_format($freight,2,'.',''); ?></td>
						</tr>
						<tr>
							<td width="120">Courier Cost</td>
							<td width="90"><? echo $currier_pre_cost; ?></td>
						</tr>
						<tr>
							<td width="120">Design Cost</td>
							<td width="90"><? echo $design_cost; ?></td>
						</tr>
						<tr>
							<td width="120"><b>Total</b></td>
							<td width="90"><b><? echo number_format($commercial+$lab_test+$inspection+$freight+$currier_pre_cost+$design_cost,2,'.',''); ?></b></td>
						</tr>
					</tbody>
				</table>
			</div>

		</fieldset>
	</div>
	<?
	exit();
}

?>
