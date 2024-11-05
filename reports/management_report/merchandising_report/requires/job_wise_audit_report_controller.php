<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
 

include('../../../../includes/class4/class.conditions.php');
include('../../../../includes/class4/class.reports.php');
include('../../../../includes/class4/class.fabrics.php');
include('../../../../includes/class4/class.yarns.php');
include('../../../../includes/class4/class.conversions.php');
include('../../../../includes/class4/class.trims.php');
include('../../../../includes/class4/class.emblishments.php');
include('../../../../includes/class4/class.washes.php');
include('../../../../includes/class4/class.commercials.php');
include('../../../../includes/class4/class.commisions.php');
include('../../../../includes/class4/class.others.php');

//----------------------------------------
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];
$permission = $_SESSION['page_permission'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name   order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
	exit();
}

if ($action=="load_drop_down_team_member")
{
	echo create_drop_down( "cbo_team_member", 110, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active=1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "- Team Member-", $selected, "" ); 
	exit();
}

if ($action=="load_variable_settings")
{
	echo "$('#cbo_date_type').val(0);\n";
	$sql_result = sql_select("select report_date_catagory from variable_order_tracking where company_name='$data' and variable_list in (42) order by id");
 	foreach($sql_result as $result)
	{
		echo "$('#cbo_date_type').val(".$result[csf("report_date_catagory")].");\n";
		echo "search_by(".$result[csf("report_date_catagory")].",1);\n";
	}
 	exit();
}

if($action=="job_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data);
	?>	
    <script>
	/*var selected_id = new Array, selected_name = new Array(); selected_style_name = new Array();
	 
	function toggle( x, origColor ) {
		var newColor = 'yellow';
		if ( x.style ) {
			x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}
	
	function js_set_value( strcon)
	{
		//alert(strcon);
		$('#txt_job_no').val( strcon );
		parent.emailwindow.hide();
	}*/
	

		var selected_id = new Array; var selected_name = new Array; var selected_style = new Array;

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
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
			//alert(str)
			if (str!="") str=str.split("_");

			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );

			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
				selected_name.push( str[2] );
				//selected_style.push( str[3] );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				//selected_style.splice( i, 1 );
			}
			var id = ''; var name = ''; var style = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				//style += selected_style[i] + '**';
			}

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			//style = style.substr( 0, style.length - 1 );

			$('#txt_job_id').val( id );
			$('#txt_job_no').val( name );
			//$('#txt_style').val( name );
		}

    </script>
    <input type="hidden" id="txt_job_id" />
    <input type="hidden" id="txt_job_no" />
    <input type="hidden" id="txt_style" />
 <?
	if ($data[0]==0) $company_id=""; else $company_id=" and company_name=$data[0]";
	if ($data[1]==0) $buyer_id=""; else $buyer_id=" and buyer_name=$data[1]";
	//if ($data[2]==0) $year_id=""; else $year_id=" and buyer_name=$data[2]";
	if(str_replace("'","",$data[2])!=0) $year_cond=" and to_char(insert_date,'YYYY')=".str_replace("'","",$data[2]).""; else $year_cond="";
	
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$teamMemberArr = return_library_array("select id,team_leader_name from lib_marketing_team ","id","team_leader_name");
	
	$sql= "select id, job_no, job_no_prefix_num, style_ref_no, product_dept, dealing_marchant, team_leader from wo_po_details_master where status_active=1 and is_deleted=0 $company_id $buyer_id $year_cond group by id, job_no, job_no_prefix_num, style_ref_no, product_dept, dealing_marchant, team_leader order by id DESC";
	
	//echo $sql;die;
	
	$arr=array(2=>$product_dept,3=>$marchentrArr,4=>$teamMemberArr);
	echo  create_list_view("list_view", "Job No,Style Ref.,Prod. Dept.,Marchant,Team Name", "100,110,110,150,150","680","360",0, $sql , "js_set_value", "id,job_no_prefix_num,style_ref_no", "", 1, "0,0,product_dept,dealing_marchant,team_leader", $arr , "job_no_prefix_num,style_ref_no,product_dept,dealing_marchant,team_leader", "",'setFilterGrid("list_view",-1);','0,0,0,0,0','',1) ;
	exit();
}

$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
//$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
//$yarn_count_library=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count"  );
$dealing_merchant_array = return_library_array("select id, team_member_name from lib_mkt_team_member_info","id","team_member_name");
$team_library=return_library_array( "select id, team_name from lib_marketing_team", "id", "team_name");

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	$company_name=str_replace("'","",$cbo_company_name);
	$cbo_type=str_replace("'","",$cbo_type);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$cbo_team_name=str_replace("'","",$cbo_team_name);
	$cbo_team_member=str_replace("'","",$cbo_team_member);
	$cbo_order_status=str_replace("'","",$cbo_order_status);
	$cbo_shipment_status=str_replace("'","",$cbo_shipment_status);
	$date_type = str_replace("'","",$cbo_date_type);
	$start_date=str_replace("'","",trim($txt_date_from));
	$end_date=str_replace("'","",trim($txt_date_to));

	// echo $template;die;

	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
	
	if(str_replace("'","",$cbo_year)!=0) $yearCond=" and to_char(a.insert_date,'YYYY')=".str_replace("'","",$cbo_year).""; else $yearCond="";
	
	$jobcond="";
	if(str_replace("'","",$hide_job_id)!="" || str_replace("'","",$hide_job_id)!=0)
	{
		$jobcond=" and a.id in(".str_replace("'","",$hide_job_id).")";
	}
	else if(str_replace("'","",$txt_job_no)!="" || str_replace("'","",$txt_job_no)!=0) 
	{
		$jobcond=" and a.job_no_prefix_num in(".str_replace("'","",$txt_job_no).")";
	}
	else $jobcond="";
	
	if(trim($txt_style_ref)!="") $stylerefCond=" and a.style_ref_no='$txt_style_ref'"; else $stylerefCond="";
	if($cbo_team_name==0) $team_name_cond=""; else $team_name_cond=" and a.team_leader='$cbo_team_name'";
	if($cbo_team_member==0) $team_member_cond=""; else $team_member_cond=" and a.dealing_marchant='$cbo_team_member'";
	if($cbo_order_status==0) $orderStatusCond=""; else $orderStatusCond=" and b.is_confirmed='$cbo_order_status'";
	if($cbo_shipment_status==0) $shipStatusCond=""; else $shipStatusCond=" and b.shiping_status='$cbo_shipment_status'";
	$date_cond="";
	if ($start_date=="" && $end_date=="") $date_cond="";
	else
	{
		if($date_type==1) $date_cond="and c.country_ship_date between '$start_date' and '$end_date'";
		else if($date_type==2) $date_cond="and b.pub_shipment_date between '$start_date' and '$end_date'";
		else if($date_type==3) $date_cond="and b.shipment_date between '$start_date' and '$end_date'";
		else if($date_type==4) $date_cond="and a.insert_date between '".$start_date."' and '".$end_date."'";
 
		if($date_type == 5){
			$factory_mst_sql = "SELECT a.id, a.po_break_down_id as POID FROM PRO_EX_FACTORY_MST a where a.ex_factory_date between '".$start_date."' and '".$end_date."' and a.status_active=1 and a.is_deleted=0";
			//echo $factory_mst_sql; die;
			$sql_result = sql_select($factory_mst_sql);
			$po_id_arr = array();
			foreach($sql_result as $row){
				$po_id_arr[$row['POID']] = $row['POID'];
			}
			$date_cond="and b.id in (".implode(",",$po_id_arr).")";
		}
		else{
			$date_cond = $date_cond;
		} 
	}
	$pre_Cost_approv_status = array(0 =>'No',1=>'Yes',2=>'No',3=>'Yes');
	ob_start();
	?>
    <div style="width:7800px">
        <fieldset style="width:100%;">
            <table width="7800">
              	<tr class="form_caption">
                    <td colspan="104" align="center"><?=$report_title; ?></td>
                </tr>
                <tr class="form_caption">
                    <td colspan="104" align="center"><?=$company_library[$company_name]; ?></td>
                </tr>
            </table>
            <table id="table_header_1" class="rpt_table" width="7800" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <th width="30">SL</th>
					<th width="70">Buyer</th>
                    <th width="70">Job No</th>
                    <th width="50">Job Year</th>
                    <th width="110">Garments Item</th>
                    <th width="110">Style Ref.</th>
                    <th width="110">PO No</th>
                    <th width="50">Approval Status</th>
                    <th width="40">UOM</th>
					<th width="80">PO Qty</th>
                    <th width="80">PO Qty [Pcs]</th>
                    <th width="80">Plan Qty</th>
                    <th width="80">Plan Qty [Pcs]</th>
                    <th width="50">Avg. Unit Price</th>
                    <th width="90">PO Value</th>
                    <th width="70">Commission</th>
                    <th width="90">Net PO Value</th>
                    <th width="70">Min. Shipment Date</th>
                    <th width="70">Last Ex-Factory Date</th><!--1560-->
                    <th width="70">Yarn Budget [Qty]</th>
                    
					<th width="70">Yarn WO [Qty]</th>
                    <th width="70">Yarn Rcv [Qty]</th>
                    <th width="70">Yarn Alloc. [Qty]</th>
                    <th width="70">Alloc. BL [Qty]</th>
                    <th width="70">Yarn Budget [Amt.]</th>
                    <th width="70">Yarn Alloc. [Amt.]</th>
                    <th width="70">Alloc. BL [Amt.]</th>
                    <th width="70">Dyed Yarn Budget [Qty]</th>
                    <th width="70">Dyed Yarn WO [Qty]</th>
					<th width="70">YD WO BL [Qty]</th>
                    <th width="70">Dyed Yarn Budget [Amt.]</th>
                    <th width="70">Dyed Yarn WO [Amt.]</th>
                    <th width="70">YD WO BL [Amt.]</th>
                    <th width="70">Grey Req. [Qty]</th>
                    <th width="70">Grey Prod [Qty]</th>
                    <th width="70">Grey Prod BL [Qty]</th><!--1190-->
                    <th width="70">Knit Budget Cost</th>
                    <th width="70">Knit Prod. Cost</th>
                    <th width="70">Knitting BL Cost</th>
					<th width="70">AOP Req. [Qty]</th>
                    
					<th width="70">AOP WO [Qty]</th>
                    <th width="70">AOP WO BL [Qty]</th>
                    <th width="70">AOP Budget [Amt.]</th>
                    <th width="70">AOP WO [Amt.]</th>
                    <th width="70">AOP WO BL [Amt.]</th>
					<th width="70">Knit Fin Fab Budget [Qty]</th>
                    <th width="70">Knit Fin Fab Rec. [Qty]</th>
                    <th width="70">Knit Fin Fab BL [Qty]</th>
                    <th width="70">Knit Fin Fab Budget [Amt.]</th>
					<th width="70">Knit Fin Fab [Amt.]</th>
                    <th width="70">Knit Fin Fab BL [Amt.]</th>
					<th width="70">Woven Fin Fab Budget[Qty]</th>
                    <th width="70">Woven Fin Fab Rec. [Qty]</th>
                    <th width="70">Woven Fin Fab BL [Qty]</th>
                    <th width="70">Woven Fin Fab Budget [Amt.]</th>
					<th width="70">Woven Fin Fab [Amt.]</th>
                    <th width="70">Woven Fin Fab BL [Amt.]</th>
                    <th width="70">Trims Budget [Amt.]</th>
                    <th width="70">Trims WO [Amt.]</th>
                    <th width="70">Trims Recv. [Amt.]</th>
                    
					<th width="70">Trims WO BL [Amt.]</th>
                    <th width="70">Trims Rcv. BL [Amt.]</th>
                    <th width="70">Embel. Budget [Amt.]</th>
                    <th width="70">Embel. WO [Amt.]</th>
                    <th width="70">Embel. WO BL [Amt.]</th>
                    
                    <th width="70">Wash Budget [Amt.]</th>
                    <th width="70">Wash WO [Amt.]</th>
                    <th width="70">Wash WO BL [Amt.]</th>
                    
                    <th width="70">Lab Test Budget [Amt.]</th>
                    <th width="70">Lab Test WO [Amt.]</th>
                    <th width="70">Lab Test WO BL [Amt.]</th>
                    <th width="70">Inspection [Amt.]</th>
                    <th width="70">Other [Amt.]</th>
                    <th width="70">Commicial [Amt.]</th>
                    <th width="100">Total Budget [Amt.]</th>
                    <th width="100">Total W/O [Amt.]</th><!--2650-->
                    <th width="70">Contribution Margin</th>
                    <th width="70">Cutting Production</th>
					<th width="70">Cutting BL</th>
                    <th width="70">Sewing Input</th>
                    <th width="70">Sewing Output</th>
                    <th width="70">Sew WIP</th>
                    <th width="70">Packing & Finishing</th>
                    
					<th width="70">Finish WIP</th>
                    <th width="70">Curr. Ex-Fact [Qty]</th>
                    <th width="70">Total Ex-Fact [Qty]</th>
                    <th width="70">Ex-Fact BL To Fin. [Qty]</th>
                    <th width="70">Ex-Fact BL To Ord [Qty]</th>
                    <th width="70">Short Ship [Qty]</th>
                    <th width="70">Excess Ship [Qty]</th>
                    <th width="100">Shipment Status</th>
                    <th width="70">Total Ship [Amt.]</th>
                    <th width="70">CM Cost/Dzn</th>
                    <th width="70">CM Cost /Pcs</th>
                    <th width="70">Total CM Cost</th>
                    <th width="70">CM Value /Dzn</th>
                    <th width="70">CM Value /Pcs</th>
                    <th width="70">Total CM Value</th>
                    <th width="70">Curr Sales CM</th>
                    <th width="80">Total CM as per Invoice Qty</th>
                    <th width="100">Team</th>
                    <th width="110">Dealing Merchant</th>
                    <th width="50">Invoice Qnty.</th>
                    <th width="50">Invoice Amount</th>
                    <th>Export CI Statement</th>
                </thead>
            </table>
            <div style="width:7800px; max-height:400px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="7780" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
					<tbody>
                <? 
				// echo "<pre>";80*70;
				// print_r($jobAmountArr); 
				// echo "<pre>";
				// die;
				// echo $sql_order_info_dtls;die;
				$sql_job="SELECT a.id as JOB_ID, a.job_no AS JOB_NO, a.buyer_name as BUYER_NAME, to_char(a.insert_date,'YYYY') as JOBYEAR, a.gmts_item_id as GMTS_ITEM_ID, a.style_ref_no as STYLE_REF_NO, a.order_uom as ORDER_UOM, a.total_set_qnty as TOTAL_SET_QNTY, a.team_leader as TEAM_LEADER, a.dealing_marchant as DEALING_MARCHENT, a.avg_unit_price, b.id AS ID,  b.po_quantity AS ord_qty, b.po_number as PO_NUMBER, b.pub_shipment_date as PUBSHIPDATE, b.shiping_status as SHIPING_STATUS, c.item_number_id AS ITEM_NUMBER_ID, c.country_id AS COUNTRY_ID, c.color_number_id AS COLOR_NUMBER_ID, c.size_number_id AS SIZE_NUMBER_ID, c.order_quantity AS ORDER_QUANTITY, c.plan_cut_qnty AS PLAN_CUT_QNTY, c.order_total as ORDER_TOTAL, c.country_ship_date AS COUNTRY_SHIP_DATE, d.costing_per as COSTING_PER, d.exchange_rate as EXCHANGE_RATE, e.commission as COMMISSION, e.lab_test as LAB_TEST, e.inspection as INSPECTION, e.design_cost as DESIGN_COST, e.studio_cost as STUDIO_COST, e.freight as FREIGHT, e.currier_pre_cost as CURRIER_COST, e.certificate_pre_cost as CERTIFICATE_COST, e.comm_cost as COMML_COST, e.cm_cost as CM_COST, e.DEFFDLC_COST, e.interest_cost, e.incometax_cost, e.deffdlc_percent, e.interest_percent, e.incometax_percent, e.depr_amor_po_price, e.DEPR_AMOR_PRE_COST, e.COMMON_OH, d.approved as APPROVED
				from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_mst d, wo_pre_cost_dtls e
				where a.id=b.job_id and b.id=c.po_break_down_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and d.job_id=e.job_id and a.company_name='$company_name' and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and a.GARMENTS_NATURE=$cbo_type $buyer_id_cond $yearCond $jobcond $stylerefCond $team_name_cond $team_member_cond $orderStatusCond $shipStatusCond $date_cond order by a.id ASC";
			    //echo $sql_job; die;
				
			   // wo_po_details_master a.avg_unit_price
				$sql_jobData=sql_select($sql_job);
				$jobIDArr =array();
				$jobDataArr=array(); $po_arr=array(); $poCountryArr=array(); $reqQtyAmtArr=array(); $jobidArr=array(); $poidArr=array(); $costingPerArr=array(); $poidwisejobArr=array();
				
				foreach($sql_jobData as $jrow)
				{
					$costingPerqty=0;
					
					if($jrow["COSTING_PER"]==1) $costingPerqty=12;
					else if($jrow["COSTING_PER"]==2) $costingPerqty=1;
					else if($jrow["COSTING_PER"]==3) $costingPerqty=24;
					else if($jrow["COSTING_PER"]==4) $costingPerqty=36;
					else if($jrow["COSTING_PER"]==5) $costingPerqty=48;
					else $costingPerqty=0;
					
					$jobDataArr[$jrow['JOB_ID']]['buyer']=$jrow['BUYER_NAME'];
					$jobDataArr[$jrow['JOB_ID']]['job_no']=$jrow['JOB_NO'];
					$jobDataArr[$jrow['JOB_ID']]['job_id']=$jrow['JOB_ID'];
					$jobDataArr[$jrow['JOB_ID']]['job_year']=$jrow['JOBYEAR'];
					$jobDataArr[$jrow['JOB_ID']]['gmts_item']=$jrow['GMTS_ITEM_ID'];
					$jobDataArr[$jrow['JOB_ID']]['style_ref_no']=$jrow['STYLE_REF_NO'];
					$jobDataArr[$jrow['JOB_ID']]['order_uom']=$jrow['ORDER_UOM'];
					$jobDataArr[$jrow['JOB_ID']]['approved']=$jrow['APPROVED'];
					$jobDataArr[$jrow['JOB_ID']]['teamleader']=$jrow['TEAM_LEADER'];
					$jobDataArr[$jrow['JOB_ID']]['dealing_marchant']=$jrow['DEALING_MARCHENT'];
					$jobDataArr[$jrow['JOB_ID']]['poid'].=','.$jrow['ID'];
					$jobDataArr[$jrow['JOB_ID']]['po_number'].=','.$jrow['PO_NUMBER'];
					$jobDataArr[$jrow['JOB_ID']]['setratio']=$jrow['TOTAL_SET_QNTY'];
					$jobDataArr[$jrow['JOB_ID']]['poqty']+=$jrow['ORDER_QUANTITY'];
					$jobDataArr[$jrow['JOB_ID']]['poqamt']+=$jrow['ORDER_TOTAL'];
					$jobDataArr[$jrow['JOB_ID']]['planqty']+=$jrow['PLAN_CUT_QNTY'];
					$jobDataArr[$jrow['JOB_ID']]['pubshipdate'].=','.$jrow['PUBSHIPDATE'];
					$jobDataArr[$jrow['JOB_ID']]['shipstatus'].=','.$jrow['SHIPING_STATUS'];
					$jobDataArr[$jrow['JOB_ID']]['exchange_rate']=$jrow['EXCHANGE_RATE'];


					$jobIDArr[$jrow['JOB_ID']] = $jrow['JOB_ID'];
					$jobDataArr[$jrow['JOB_ID']]['total_cm'] += $jrow['ORD_QTY'];
					$jobDataArr[$jrow['JOB_ID']]['order_value'] += $jrow['ORDER_TOTAL'];//*$jrow['AVG_UNIT_PRICE'];


					$jobDataArr[$jrow['JOB_ID']]['comm_cost'] += $jrow['COMML_COST'];
					$jobDataArr[$jrow['JOB_ID']]['cm_cost'] += $jrow['CM_COST'];
					$jobDataArr[$jrow['JOB_ID']]['deffdlc_cost'] += $jrow['DEFFDLC_COST'];
					$jobDataArr[$jrow['JOB_ID']]['interest_cost'] += $jrow['INTEREST_COST'];
					$jobDataArr[$jrow['JOB_ID']]['incometax_cost'] += $jrow['INCOMETAX_COST'];
					$jobDataArr[$jrow['JOB_ID']]['deffdlc_percent'] += $jrow['DEFFDLC_PERCENT'];
					$jobDataArr[$jrow['JOB_ID']]['interest_percent'] += $jrow['INTEREST_PERCENT'];
					$jobDataArr[$jrow['JOB_ID']]['incometax_percent'] += $jrow['INCOMETAX_PERCENT'];
					$jobDataArr[$jrow['JOB_ID']]['depr_amor_po_price'] += $jrow['DEPR_AMOR_PO_PRICE'];
					$jobDataArr[$jrow['JOB_ID']]['depr_amor_pre_cost'] += $jrow['DEPR_AMOR_PRE_COST'];


					$job_no = $jrow['JOB_NO'];
					/*$condition= new condition();
					$condition->job_no("='$job_no'");
					
					$condition->init();
					$commission= new commision($condition);
					$commission_costing_arr=$commission->getAmountArray_by_job();

					$fabric = new fabric($condition);
					// echo $fabric->getQuery();die;

					$yarn= new yarn($condition);
					$yarn_costing_arr = $yarn->getJobWiseYarnAmountArray();
					$fabric_costing_arr2 = $fabric->getAmountArray_by_job_knitAndwoven_greyAndfinish();
					$fabric_qty_arr = $fabric->getQtyArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
					$fabric_amount_arr = $fabric->getAmountArray_by_Fabriccostid_knitAndwoven_greyAndfinish();


					$conversion= new conversion($condition);
					$conversion_costing_arr_process=$conversion->getAmountArray_by_job();
					$trims= new trims($condition);
					//echo $trims->getQuery(); die;
					$trims_costing_arr=$trims->getAmountArray_by_job();
					$emblishment= new emblishment($condition);
					$emblishment_costing_arr=$emblishment->getAmountArray_by_job();
					$wash= new wash($condition);
					//echo $wash->getQuery(); die;
					$emblishment_costing_arr_wash=$wash->getAmountArray_by_job();

					$commercial= new commercial($condition);
					$commercial_costing_arr=$commercial->getAmountArray_by_job();

					$commission= new commision($condition);
					$commission_costing_arr=$commission->getAmountArray_by_job();

					$other= new other($condition);
					$other_costing_arr=$other->getAmountArray_by_job();*/
					
					$commisionAmt=$labTestAmt=$inspectionAmt=$designAmt=$studioAmt=$freightAmt=$courierAmt=$certificateAmt=$commercialAmt=$commohAmt=$dLcCost==$depAmortAmt=$cmcost=$cmdzn=$cmpcs=$interestamt=$incometexamt=0;
					$commisionAmt=($jrow['ORDER_QUANTITY']/$jrow['TOTAL_SET_QNTY'])*($jrow['COMMISSION']/$costingPerqty);
					$labTestAmt=($jrow['ORDER_QUANTITY']/$jrow['TOTAL_SET_QNTY'])*($jrow['LAB_TEST']/$costingPerqty);
					$inspectionAmt=($jrow['ORDER_QUANTITY']/$jrow['TOTAL_SET_QNTY'])*($jrow['INSPECTION']/$costingPerqty);
					$designAmt=($jrow['ORDER_QUANTITY']/$jrow['TOTAL_SET_QNTY'])*($jrow['DESIGN_COST']/$costingPerqty);
					$studioAmt=($jrow['ORDER_QUANTITY']/$jrow['TOTAL_SET_QNTY'])*($jrow['STUDIO_COST']/$costingPerqty);
					$freightAmt=($jrow['ORDER_QUANTITY']/$jrow['TOTAL_SET_QNTY'])*($jrow['FREIGHT']/$costingPerqty);
					$courierAmt=($jrow['ORDER_QUANTITY']/$jrow['TOTAL_SET_QNTY'])*($jrow['CURRIER_COST']/$costingPerqty);
					$certificateAmt=($jrow['ORDER_QUANTITY']/$jrow['TOTAL_SET_QNTY'])*($jrow['CERTIFICATE_COST']/$costingPerqty);
					$commercialAmt=($jrow['ORDER_QUANTITY']/$jrow['TOTAL_SET_QNTY'])*($jrow['COMML_COST']/$costingPerqty);
					$cmcost=($jrow['ORDER_QUANTITY']/$jrow['TOTAL_SET_QNTY'])*($jrow['CM_COST']/$costingPerqty);
					$commohAmt=($jrow['ORDER_QUANTITY']/$jrow['TOTAL_SET_QNTY'])*($jrow['COMMON_OH']/$costingPerqty);
					$depAmortAmt=($jrow['ORDER_QUANTITY']/$jrow['TOTAL_SET_QNTY'])*($jrow['DEPR_AMOR_PRE_COST']/$costingPerqty);
					$dLcCost=($jrow['ORDER_QUANTITY']/$jrow['TOTAL_SET_QNTY'])*($jrow['DEFFDLC_COST']/$costingPerqty);
					$interestamt=($jrow['ORDER_QUANTITY']/$jrow['TOTAL_SET_QNTY'])*($jrow['INTEREST_COST']/$costingPerqty);
					$incometexamt=($jrow['ORDER_QUANTITY']/$jrow['TOTAL_SET_QNTY'])*($jrow['INCOMETAX_COST']/$costingPerqty);
					
					$cmpcs=$jrow['CM_COST']/$costingPerqty;
					$cmdzn=$cmpcs*12;

					$cmpcs=$jrow['CM_COST']/$costingPerqty;

					// $jobAmountArr[$jrow['JOB_NO']] += $jrow['AMOUNT'];
					
					// echo $jrow['CM_COST'].'='.$costingPerqty.'='.$cmpcs.'='.$cmdzn.'<br>';
					
					$jobDataArr[$jrow['JOB_ID']]['commission']+=$commisionAmt;
					$jobDataArr[$jrow['JOB_ID']]['labtest']+=$labTestAmt;
					$jobDataArr[$jrow['JOB_ID']]['inspection']+=$inspectionAmt;
					$jobDataArr[$jrow['JOB_ID']]['design']+=$designAmt;
					$jobDataArr[$jrow['JOB_ID']]['studio']+=$studioAmt;
					$jobDataArr[$jrow['JOB_ID']]['freight']+=$freightAmt;
					$jobDataArr[$jrow['JOB_ID']]['courier']+=$courierAmt;
					$jobDataArr[$jrow['JOB_ID']]['certificate']+=$certificateAmt;
					$jobDataArr[$jrow['JOB_ID']]['commercial']+=$commercialAmt;
					$jobDataArr[$jrow['JOB_ID']]['comm_oh']+=$commohAmt;
					$jobDataArr[$jrow['JOB_ID']]['depamrtamt']+=$depAmortAmt;
					$jobDataArr[$jrow['JOB_ID']]['lccost']+=$dLcCost;
					$jobDataArr[$jrow['JOB_ID']]['cmcost']+=$cmcost;
					$jobDataArr[$jrow['JOB_ID']]['cmpcs']=$cmpcs;
					$jobDataArr[$jrow['JOB_ID']]['cmdzn']=$cmdzn;
					$jobDataArr[$jrow['JOB_ID']]['interest']+=$interestamt;
					$jobDataArr[$jrow['JOB_ID']]['incometex']+=$incometexamt;
					

					$po_arr[$jrow['JOB_ID']][$jrow['ID']][$jrow['ITEM_NUMBER_ID']][$jrow['COLOR_NUMBER_ID']][$jrow['SIZE_NUMBER_ID']]['poqty']+=$jrow['ORDER_QUANTITY'];
					$po_arr[$jrow['JOB_ID']][$jrow['ID']][$jrow['ITEM_NUMBER_ID']][$jrow['COLOR_NUMBER_ID']][$jrow['SIZE_NUMBER_ID']]['planqty']+=$jrow['PLAN_CUT_QNTY'];
					$po_arr[$jrow['JOB_ID']][$jrow['ID']][$jrow['ITEM_NUMBER_ID']][$jrow['COLOR_NUMBER_ID']][$jrow['SIZE_NUMBER_ID']]['county_id'].=$jrow['COUNTRY_ID'].',';
					$poCountryArr[$jrow['JOB_ID']][$jrow['ID']][$jrow['ITEM_NUMBER_ID']][$jrow['COUNTRY_ID']][$jrow['COLOR_NUMBER_ID']][$jrow['SIZE_NUMBER_ID']]['poqty']+=$jrow['ORDER_QUANTITY'];
					$poCountryArr[$jrow['JOB_ID']][$jrow['ID']][$jrow['ITEM_NUMBER_ID']][$jrow['COUNTRY_ID']][$jrow['COLOR_NUMBER_ID']][$jrow['SIZE_NUMBER_ID']]['planqty']+=$jrow['PLAN_CUT_QNTY'];
					$jobidArr[$jrow['JOB_ID']]=$jrow['JOB_ID'];
					$poidArr[$jrow['ID']]=$jrow['ID'];
					$costingPerArr[$jrow['JOB_ID']]=$costingPerqty;
					$poidwisejobArr[$jrow['ID']]=$jrow['JOB_ID'];
				}
				unset($sql_jobData);
				// wo_pre_cost_dtls

				// echo "<pre>";
				// print_r($jobDataArr); 
				// echo "<pre>";
				// die;
				// echo $order_values;die;

				$jobAmountArr =array();
                $sql="SELECT a.JOB_ID, a.JOB_NO, a.AMOUNT FROM WO_NON_ORDER_INFO_DTLS a where a.JOB_ID in (".implode(",", $jobIDArr).") and STATUS_ACTIVE=1 and IS_DELETED=0";

				$sql_order_info_dtls=sql_select($sql);
				foreach($sql_order_info_dtls as $row){
					$jobAmountArr[$row['JOB_NO']] += $row['AMOUNT'];
				}
				// echo "<pre>";
				// print_r($jobDataArr); 
				// echo "<pre>";
				// die;

				$con = connect();
				execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2,3,4) and ENTRY_FORM=4");//1=Job, 2=Po, 3=PI, 4=WO
				oci_commit($con);
				fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 4, 1, $jobidArr, $empty_arr);
				fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 4, 2, $poidArr, $empty_arr);
				disconnect($con);
				
				$gmtsitemRatioSql="select a.job_id AS JOB_ID, a.gmts_item_id AS GMTS_ITEM_ID, a.set_item_ratio AS SET_ITEM_RATIO from wo_po_details_mas_set_details a,gbl_temp_engine c where  1=1 and a.job_id=c.ref_val and c.entry_form=4 and c.ref_from=1 and c.USER_ID = ".$user_id."";
				//echo $gmtsitemRatioSql; die;
				$gmtsitemRatioSqlRes = sql_select($gmtsitemRatioSql);
				$jobItemRatioArr=array();
				foreach($gmtsitemRatioSqlRes as $row)
				{
					$jobItemRatioArr[$row['JOB_ID']][$row['GMTS_ITEM_ID']]=$row['SET_ITEM_RATIO'];
				}
				unset($gmtsitemRatioSqlRes);
				
				$sqlContrast="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.gmts_color_id as COLOR_NUMBER_ID, a.contrast_color_id AS CONTRAST_COLOR_ID from wo_pre_cos_fab_co_color_dtls a,gbl_temp_engine c where 1=1 and  a.job_id=c.ref_val and c.entry_form=4 and c.ref_from=1 and a.status_active=1 and a.is_deleted=0 and c.USER_ID = ".$user_id."";
				//echo $sqlContrast; die;
				$sqlContrastRes = sql_select($sqlContrast);
				$sqlContrastArr=array();
				foreach($sqlContrastRes as $row)
				{
					$sqlContrastArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]=$row['CONTRAST_COLOR_ID'];
				}
				unset($sqlContrastRes);
				//Stripe Details
				$sqlStripe="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.po_break_down_id as POID, a.item_number_id AS ITEM_NUMBER_ID, a.color_number_id as COLOR_NUMBER_ID, a.stripe_color as STRIPE_COLOR, a.size_number_id as SIZE_NUMBER_ID, a.fabreq as FABREQ, a.yarn_dyed as YARN_DYED from wo_pre_stripe_color a, gbl_temp_engine c where 1=1 and  a.job_id=c.ref_val and c.entry_form=4 and c.ref_from=1 and c.USER_ID = ".$user_id." and a.status_active=1 and a.is_deleted=0 ";
				//echo $sqlStripe; die;
				$sqlStripeRes = sql_select($sqlStripe);
				$sqlStripeArr=array();
				foreach($sqlStripeRes as $row)
				{
					$sqlStripeArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['strip'][$row['STRIPE_COLOR']]=$row['STRIPE_COLOR'];
					$sqlStripeArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['fabreq'][$row['STRIPE_COLOR']]=$row['FABREQ'];
				}
				unset($sqlStripeRes);
				
				$sqlfab="select a.job_id AS JOB_ID, a.id AS ID, a.body_part_id as BODY_PART_ID, a.item_number_id AS ITEM_NUMBER_ID, a.fab_nature_id AS FAB_NATURE_ID,  a.lib_yarn_count_deter_id as YARNDETAID, a.color_type_id AS COLOR_TYPE_ID, a.fabric_source as FABRIC_SOURCE, a.color_size_sensitive AS COLOR_SIZE_SENSITIVE, a.construction AS CONSTRUCTION, a.gsm_weight AS GSM_WEIGHT, a.uom AS UOM, b.po_break_down_id AS POID, b.color_number_id AS COLOR_NUMBER_ID, b.gmts_sizes AS SIZE_NUMBER_ID, b.cons AS CONS, b.requirment AS REQUIRMENT, b.rate as RATE
				from wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b, gbl_temp_engine c
				where 1=1 and a.id=b.pre_cost_fabric_cost_dtls_id and b.cons!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_id=c.ref_val and c.entry_form=4 and c.ref_from=1 and c.USER_ID = ".$user_id.""; 
				//echo $sqlfab; die;
				$sqlfabRes = sql_select($sqlfab);
				$fabIdWiseGmtsDataArr=array();
				$tot_purchfin_amt=$purchgrey_amt=0;
				foreach($sqlfabRes as $row)
				{
					$poQty=$planQty=$costingPer=$itemRatio=$finReq=$greyReq=$finAmt=$greyAmt=0;
					
					$fabIdWiseGmtsDataArr[$row['ID']]['item']=$row['ITEM_NUMBER_ID'];
					$fabIdWiseGmtsDataArr[$row['ID']]['fnature']=$row['FAB_NATURE_ID'];
					$fabIdWiseGmtsDataArr[$row['ID']]['sensitive']=$row['COLOR_SIZE_SENSITIVE'];
					$fabIdWiseGmtsDataArr[$row['ID']]['color_type']=$row['COLOR_TYPE_ID'];
					$fabIdWiseGmtsDataArr[$row['ID']]['uom']=$row['UOM'];
					$fabIdWiseGmtsDataArr[$row['ID']]['body_part_id']=$row['BODY_PART_ID'];
					$fabIdWiseGmtsDataArr[$row['ID']]['DETAID']=$row['YARNDETAID'];
					
					$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
					$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
					$costingPer=$costingPerArr[$row['JOB_ID']];
					$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$row['ITEM_NUMBER_ID']];
					
					$finReq=($planQty/$itemRatio)*($row['CONS']/$costingPer);
					$greyReq=($planQty/$itemRatio)*($row['REQUIRMENT']/$costingPer);
					
					$finAmt=$finReq*$row['RATE'];
					$greyAmt=$greyReq*$row['RATE'];

					$jobDataArr[$row['JOB_ID']]['FAB_NATURE_ID'] = $row['FAB_NATURE_ID'];
					
					if($row['FAB_NATURE_ID']==2)
					{
						$jobDataArr[$row['JOB_ID']]['greyreqqty'] += $greyReq;
						$jobDataArr[$row['JOB_ID']]['greyreqamt'] += $greyAmt;
						
						$jobDataArr[$row['JOB_ID']]['finreqqty'] += $finReq;
						$jobDataArr[$row['JOB_ID']]['finreqamt'] += $finAmt;
					}
					else if($row['FAB_NATURE_ID']==3)
					{
						$jobDataArr[$row['JOB_ID']]['wvnGreyReqQty'] += $greyReq;
						$jobDataArr[$row['JOB_ID']]['wvnGreyReqAmt'] += $greyAmt;
						
						$jobDataArr[$row['JOB_ID']]['wvnFinReqQty'] += $finReq;
						$jobDataArr[$row['JOB_ID']]['wvnFinReqAmt'] += $finAmt;
					}
					//echo $planQty.'='.$itemRatio.'='.$row['CONS'].'='.$row['REQUIRMENT'].'='.$costingPer.'='.$finReq.'='.$greyReq.'<br>';
				}
				//echo $tot_purchfin_amt."=";die;
				/*echo "<pre>";
				print_r($jobDataArr); die;*/
				unset($sqlfabRes); 
				
				
				$sqlYarn="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id as PRECOSTID, a.po_break_down_id as POID, a.color_number_id as COLOR_NUMBER_ID, a.gmts_sizes as SIZE_NUMBER_ID, a.cons AS CONS, a.requirment AS REQUIRMENT, b.id AS YARN_ID, b.count_id AS COUNT_ID, b.copm_one_id AS COPM_ONE_ID, b.percent_one AS PERCENT_ONE, b.type_id AS TYPE_ID, b.color AS COLOR, b.cons_ratio AS CONS_RATIO, b.cons_qnty AS CONS_QNTY, b.avg_cons_qnty AS AVG_CONS_QNTY, b.rate AS RATE, b.amount AS AMOUNT  
	
				from wo_pre_cos_fab_co_avg_con_dtls a, wo_pre_cost_fab_yarn_cost_dtls b, gbl_temp_engine c where 1=1 and a.job_id=b.job_id and a.pre_cost_fabric_cost_dtls_id=b.fabric_cost_dtls_id and a.cons!=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.job_id=c.ref_val and c.entry_form=4 and c.ref_from=1 and c.USER_ID = ".$user_id."";
				//echo $sqlYarn; die; 
				$sqlYarnRes = sql_select($sqlYarn);
				foreach($sqlYarnRes as $row)
				{
					$poQty=$planQty=$costingPer=$itemRatio=$consQnty=$yarnReq=$yarnAmt=0;
					
					$gmtsItem=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['item'];
					
					$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$gmtsItem][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
					$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$gmtsItem][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
					$costingPer=$costingPerArr[$row['JOB_ID']];
					$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$gmtsItem];
					
					$consQnty=$row['REQUIRMENT']*($row['CONS_RATIO']/100);
					
					$yarnReq=($planQty/$itemRatio)*($consQnty/$costingPer);
					
					$yarnAmt=$yarnReq*$row['RATE'];
					//echo $consQnty.'-'.$planQty.'-'.$itemRatio.'-'.$costingPer.'<br>';
					$a[$row['YARN_ID']]+=$yarnReq;
					$jobDataArr[$row['JOB_ID']]['yarnqty']+=$yarnReq;
					$jobDataArr[$row['JOB_ID']]['yarnamt']+=$yarnAmt;
				}
				unset($sqlYarnRes);
				//echo "<pre>";
				//print_r($a); die;
				
				$sqlConv="select a.job_id AS JOB_ID, a.pre_cost_fabric_cost_dtls_id AS PRECOSTID, a.po_break_down_id as POID, a.color_number_id as COLOR_NUMBER_ID, a.gmts_sizes as SIZE_NUMBER_ID, a.dia_width AS DIA_WIDTH, a.cons AS CONS, a.requirment AS REQUIRMENT, b.id AS CONVERTION_ID, b.cons_process AS CONS_PROCESS, b.req_qnty AS REQ_QNTY, b.process_loss AS PROCESS_LOSS, b.avg_req_qnty AS AVG_REQ_QNTY, b.charge_unit AS CHARGE_UNIT, b.amount as AMOUNT, b.color_break_down AS COLOR_BREAK_DOWN
				from wo_pre_cos_fab_co_avg_con_dtls a, wo_pre_cost_fab_conv_cost_dtls b, gbl_temp_engine g where 1=1 and a.pre_cost_fabric_cost_dtls_id=b.fabric_description and a.cons!=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.po_break_down_id =g.ref_val and g.user_id = ".$user_id." and g.entry_form=4 and g.ref_from=2";
				//echo $sqlConv; die;
				$sqlConvRes = sql_select($sqlConv);
				$convConsRateArr=array(); $convFabArr=array();
				foreach($sqlConvRes as $row)
				{
					$id=$row['CONVERTION_ID'];
					$colorBreakDown=$row['COLOR_BREAK_DOWN'];
					if($colorBreakDown !="")
					{
						$arr_1=explode("__",$colorBreakDown);
						for($ci=0;$ci<count($arr_1);$ci++)
						{
							$arr_2=explode("_",$arr_1[$ci]);
							$convConsRateArr[$id][$arr_2[0]][$arr_2[3]]['rate']=$arr_2[1];
							$convConsRateArr[$id][$arr_2[0]][$arr_2[3]]['cons']=$arr_2[4];
							$convConsRateArr[$id][$arr_2[0]][$arr_2[3]]['rate']=$arr_2[1];
						}
					}
				}
				//echo "ff"; die;
				$process_id_fabricfinishing_array = array(25,26,31,32,33,34,38,39,61,62,63,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,100,125,127,128,129,135,136,137,138,139,141,142,143,144,145,146,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,190,191,192,194,195,196,197,198,199,200,202,204,205,206,207,208,210,211,212,218,219,220,221,222,223,224,225,227,229,230,232,234,238,240,243,244,245,246,247,248,249,250,251,252,254,255,256,257,258,259,260,261,262,263,264,265,266,267,268,269,277,278,279,281,282,283,284,285,286,287,288,290,291,292,293,294,295,296,297,298,299,300,303,304,305,306,307,308,309,310,311,312,313,314,315,316,317,318,319,320,321,322,323,324,325,326,327,328,329,330,331,332,333,334,335,336,337,338,339,340,341,342,343,344,345,346,347,348,349,350,351,352,353,354,355,356,357,358,359,360,361,362,363,364,365,366,368,369,370,371,372,373,374,375,376,377,378,379,380,381,382,383,385,386,387,388,390,391,398,399,400,401,402,403,404,405,406,407,408,409,410,411,412,413,414,415,416,417,418,419,420,421,422,423,424,425,426,427,428,429,430,432,433,434,435,436,437,440,441,442,443,453,454,455,456,457,458,459,460);
				$convReqQtyAmtArr=array(); $convRateArr=array();
				foreach($sqlConvRes as $row)
				{
					$poQty=$planQty=$costingPer=$itemRatio=$consQnty=$reqqnty=$convAmt=0;
					$gmtsItem=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['item'];
					
					$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$gmtsItem][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
					$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$gmtsItem][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
					$costingPer=$costingPerArr[$row['JOB_ID']];
					$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$gmtsItem];
					
					$colorTypeId=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['color_type']; 
					$colorSizeSensitive=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['sensitive'];
					$libYarnDetaid=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['DETAID'];
					$consProcessId=$row['CONS_PROCESS'];
					$body_part_id=$fabIdWiseGmtsDataArr[$row['PRECOSTID']]['body_part_id'];
					$stripe_color=$sqlStripeArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]['strip'];
					$convRateArr[$row['CONVERTION_ID']]['fab']=$fabDescArr[$row['PRECOSTID']]['fab'];
					$convRateArr[$row['CONVERTION_ID']]['bodypart']=$body_part_id;
					$convRateArr[$row['CONVERTION_ID']]['libyarndetaid']=$libYarnDetaid;
					//if(($colorTypeId==2 || $colorTypeId==3 || $colorTypeId==4 || $colorTypeId==6 || $colorTypeId==31 || $colorTypeId==32 || $colorTypeId==33 || $colorTypeId==34) && $consProcessId==30 && count($stripe_color)>0)
					if( $consProcessId==30)
					{
						$reqqnty=$qnty=0; $convrate=0;
						foreach($stripe_color as $stripe_color_id)
						{
							$stripe_color_cons_dzn = $convConsRateArr[$row['CONVERTION_ID']][$row['COLOR_NUMBER_ID']][$stripe_color_id]['cons'];
							$convrate = $convConsRateArr[$row['CONVERTION_ID']][$row['COLOR_NUMBER_ID']][$stripe_color_id]['rate'];
							$requirment = $stripe_color_cons_dzn-($stripe_color_cons_dzn*$row['PROCESS_LOSS'])/100;
							$qnty=($planQty/$itemRatio)*($requirment/$costingPer);
				
							if($convrate>0){
								$reqqnty=$qnty;
								$convAmt=$qnty*$convrate;
							}
							
							$jobDataArr[$row['JOB_ID']]['ydyingqty']+=$reqqnty;
							$jobDataArr[$row['JOB_ID']]['ydyingamt']+=$convAmt;
						}
					}
					else
					{
						$convrate = $requirment=$reqqnty=0;
						$rateColorId = $row['COLOR_NUMBER_ID'];
						if($colorSizeSensitive==3) $rateColorId = $sqlContrastArr[$row['JOB_ID']][$row['PRECOSTID']][$row['COLOR_NUMBER_ID']]; else $rateColorId=$row['COLOR_NUMBER_ID'];
				
						if($row['COLOR_BREAK_DOWN']!="") $convrate = $convConsRateArr[$row['CONVERTION_ID']][$row['COLOR_NUMBER_ID']][$rateColorId]['rate']; else $convrate = $row['CHARGE_UNIT'];
						
						if($convrate>0){
							$requirment = $row['REQUIRMENT']-($row['REQUIRMENT']*$row['PROCESS_LOSS'])/100;
							$qnty = ($planQty/$itemRatio)*($requirment/$costingPer);
							$reqqnty = $qnty;
							$convAmt = $qnty*$convrate;
						}
						if($consProcessId==1 || $consProcessId==33)
						{
							$convrate=$row['CHARGE_UNIT'];
							$requirment=$row['REQUIRMENT']-($row['REQUIRMENT']*$row['PROCESS_LOSS'])/100;
							$qnty=($planQty/$itemRatio)*($requirment/$costingPer);
							$reqqnty=$qnty;
							$convAmt=$qnty*$convrate;
							$jobDataArr[$row['JOB_ID']]['knitreqamt']+=$convAmt;
						}
						
						//echo $convrate.'='.$row['CHARGE_UNIT'].'='.$itemRatio.'='.$requirment.'='.$costingPer."<br>";
						if($consProcessId==134)
						{
							$convReqQtyAmtArr['yd'][$row['POID']][$consProcessId]['yarn']['yqty']+=$reqqnty;
							$convReqQtyAmtArr['yd'][$row['POID']][$consProcessId]['yarn']['yamt']+=$convAmt;
						}
						if($consProcessId==1)
						{
							$convReqQtyAmtArr['knit'][$row['POID']][$body_part_id][$libYarnDetaid]['kqty']+=$reqqnty;
							$convReqQtyAmtArr['knit'][$row['POID']][$body_part_id][$libYarnDetaid]['kamt']+=$convAmt;
							//echo $row['POID'].'='.$body_part_id.'='.$libYarnDetaid.'<br>';
						}
						/*if($consProcessId==31)
						{
							$jobDataArr[$row['JOB_ID']]['fabdyingqty']+=$reqqnty;
							$jobDataArr[$row['JOB_ID']]['fabdyingamt']+=$convAmt;
							
						}*/
						
						if (in_array($consProcessId, $process_id_fabricfinishing_array))// Fabric Finishing
						{
							$convReqQtyAmtArr['finish'][$row['POID']][$body_part_id][$libYarnDetaid]['fqty']+=$reqqnty;
							$convReqQtyAmtArr['finish'][$row['POID']][$body_part_id][$libYarnDetaid]['famt']+=$convAmt;
							
							$jobDataArr[$row['JOB_ID']]['finreqqty']+=$reqqnty;
							$jobDataArr[$row['JOB_ID']]['finreqamt']+=$convAmt;
						}
						
						if($consProcessId==67 || $consProcessId==68 || $consProcessId==35)
						{
							$jobDataArr[$row['JOB_ID']]['aopqty']+=$reqqnty;
							$jobDataArr[$row['JOB_ID']]['aopamt']+=$convAmt;
						}
						$convRateArr[$row['POID']][$consProcessId][$rateColorId][$libYarnDetaid]['fdrate']=$convrate;
					}
					$jobDataArr[$row['JOB_ID']]['convamt']+=$convAmt;
					//echo $planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'='.$yarnReq.'='.$yarnAmt.'<br>';
					//$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['conv_qty']+=$reqqnty;
					//$reqQtyAmtArr[$row['JOB_ID']][$row['POID']]['conv_amt']+=$convAmt;
				}
				//echo "<pre>";
				//print_r($convReqQtyAmtArr['knit']);
				unset($sqlConvRes);
				
				$sqlTrim="select a.SEQ, a.job_id AS JOB_ID, a.id AS TRIMID, a.trim_group AS TRIM_GROUP, a.description AS DESCRIPTION, a.COUNTRY, a.cons_uom AS CONS_UOM, a.cons_dzn_gmts CONS_DZN_GMTS, a.rate AS RATEMST, a.amount AS AMOUNT, b.po_break_down_id as POID, b.item_number_id as ITEM_NUMBER_ID, b.color_number_id as COLOR_NUMBER_ID, b.size_number_id as SIZE_NUMBER_ID, b.cons AS TOT_CONS, b.tot_cons AS CONS, b.rate AS RATE, b.country_id AS COUNTRY_ID_TRIMS, b.color_size_table_id as COLOR_SIZE_ID
				from wo_pre_cost_trim_cost_dtls a, wo_pre_cost_trim_co_cons_dtls b, gbl_temp_engine c
				where 1=1 and a.id=b.wo_pre_cost_trim_cost_dtls_id and b.cons>0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.job_id =c.ref_val and c.user_id = ".$user_id." and c.entry_form=4 and c.ref_from=1";
				//echo $sqlTrim; die;
				$sqlTrimRes = sql_select($sqlTrim);
				//$a=array();
				foreach($sqlTrimRes as $row)
				{
					$poQty=$planQty=$costingPer=$itemRatio=$consQnty=$consTotQnty=$consAmt=$consTotAmt=0;
					
					$costingPer=$costingPerArr[$row['JOB_ID']];
					$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$row['ITEM_NUMBER_ID']];
					$poCountryId=array();
					$poCountryId=array_filter(array_unique(explode(",",$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['county_id'])));
					//print_r($poCountryId);
					/*if($row['COUNTRY']=="" || $row['COUNTRY']==0)
					*/
					if($row['COUNTRY_ID_TRIMS']=="" || $row['COUNTRY_ID_TRIMS']==0)
					{
						$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
						$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
						
						//$consQnty=($poQty/$itemRatio)*($row['CONS']/$costingPer);
						$consTotQnty=($poQty/$itemRatio)*($row['TOT_CONS']/$costingPer);
						
						//$consAmt=$consQnty*$row['RATE'];
						$consTotAmt=$consTotQnty*$row['RATE'];
					}
					else
					{
						$countryIdArr=array();
						$countryIdArr=explode(",",$row['COUNTRY_ID_TRIMS']);
						$consQnty=$consTotQnty=$consAmt=$consTotAmt=0;
						foreach($poCountryId as $countryId)
						{
							if(in_array($countryId, $countryIdArr))
							{
								$poQty=0;
								$poQty=$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
								//$planQty=$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
								$consQty=$consTotQty=$consAmt=0;
								
								//$consQty=($poQty/$itemRatio)*($row['CONS']/$costingPer);
								$consTotQty=($poQty/$itemRatio)*($row['TOT_CONS']/$costingPer);
								
								//$consTotQnty+=$poQty;
								/*if($row['SEQ']==0)
								{echo $poQty.'-'.$row['CONS'].'-'.$consTotQty.'-'.$row['RATE'].'<br>';}*/
								$consAmt=$consTotQty*$row['RATE'];
								//$consTotAmt+=$consTotQty*$row['RATE'];
								$consTotAmt+=$consAmt;
							}
						}
					}
					//echo $planQty.'='.$itemRatio.'='.$row['REQUIRMENT'].'='.$costingPer.'='.$yarnReq.'='.$yarnAmt.'<br>';
					$jobDataArr[$row['JOB_ID']]['trimsamt']+=$consTotAmt;
					//$a[$row['SEQ']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]+=$consTotAmt;
				}
				unset($sqlTrimRes);
				
				//echo "<pre>"; print_r($a[0]['43511']['126'][54843][7]); 
				//die;
				
				$sqlEmb="select a.job_id AS JOB_ID, a.id AS EMB_ID, a.emb_name AS EMB_NAME, a.emb_type AS EMB_TYPE, a.cons_dzn_gmts AS CONS_DZN_GMTS_MST, a.rate AS RATE_MST, a.amount AS AMOUNT_MST, a.budget_on AS BUDGET_ON, b.po_break_down_id as POID, b.item_number_id as ITEM_NUMBER_ID, b.color_number_id as COLOR_NUMBER_ID, b.size_number_id as SIZE_NUMBER_ID, b.requirment AS CONS_DZN_GMTS, b.rate AS RATE, b.amount AS AMOUNT, b.country_id AS COUNTRY_ID_EMB 
				from wo_pre_cost_embe_cost_dtls a, wo_pre_cos_emb_co_avg_con_dtls b, gbl_temp_engine c 
				where 1=1 and a.id=b.pre_cost_emb_cost_dtls_id and a.job_id=b.job_id and b.requirment>0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.job_id =c.ref_val and c.user_id = ".$user_id." and c.entry_form=4 and c.ref_from=1";
				//echo $sqlEmb; die; 
				$sqlEmbRes = sql_select($sqlEmb);
				
				foreach($sqlEmbRes as $row)
				{
					$poQty=$planQty=$costingPer=$itemRatio=$consQnty=$consTotQnty=$consAmt=$consTotAmt=0;
					
					$costingPer=$costingPerArr[$row['JOB_ID']];
					$itemRatio=$jobItemRatioArr[$row['JOB_ID']][$row['ITEM_NUMBER_ID']];
					$budget_on=$row['BUDGET_ON'];
					
					$poCountryId=array_filter(array_unique(explode(",",$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['county_id'])));
					//print_r($poCountryId);
					$calPoPlanQty=0;
					
					if($row['COUNTRY_ID_EMB']=="" || $row['COUNTRY_ID_EMB']==0)
					{
						$poQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
						$planQty=$po_arr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
						
						if($budget_on==1) $calPoPlanQty=$poQty; else $calPoPlanQty=$planQty;
						$consQty=0;
						$consQty=($calPoPlanQty/$itemRatio)*($row['CONS_DZN_GMTS']/$costingPer);
						$consQnty+=$consQty;
						
						$consAmt=$consQty*$row['RATE'];
					}
					else
					{
						$countryIdArr=explode(",",$row['COUNTRY_ID_EMB']);
						$consQnty=$consAmt=0;
						foreach($poCountryId as $countryId)
						{
							if(in_array($countryId, $countryIdArr))
							{
								$poQty=$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['poqty'];
								$planQty=$poCountryArr[$row['JOB_ID']][$row['POID']][$row['ITEM_NUMBER_ID']][$countryId][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']]['planqty'];
								
								if($budget_on==1) $calPoPlanQty=$poQty; else $calPoPlanQty=$planQty;
								$consQty=0;
								$consQty=($calPoPlanQty/$itemRatio)*($row['CONS_DZN_GMTS']/$costingPer);
								$consQnty+=$consQty;
								// echo $poQty.'-'.$itemRatio.'-'.$row['CONS_DZN_GMTS'].'-'.$costingPer.'<br>';
								$consAmt+=$consQty*$row['RATE'];
							}
						}
					}
					if($row['EMB_NAME']!=3)
					{
						$jobDataArr[$row['JOB_ID']]['emblamt']+=$consAmt;
					}
					else if($row['EMB_NAME']==3)
					{
						$jobDataArr[$row['JOB_ID']]['washamt']+=$consAmt;
					}
					//$jobDataArr[$row['JOB_ID']]['emblamt']+=$consAmt;
				}
				unset($sqlEmbRes);
				
				$yarnPurOrderSql="select a.job_id as JOBID, a.req_quantity as YARNPOQTY, a.amount as YARNPOAMT from wo_non_order_info_dtls a, gbl_temp_engine c where 1=1 and a.is_deleted=0 and a.status_active=1 and a.job_id =c.ref_val and c.user_id = ".$user_id." and c.entry_form=4 and c.ref_from=1";
				//echo $yarnPurOrderSql; die;
				$yarnPurOrderSqlRes = sql_select($yarnPurOrderSql);
				foreach($yarnPurOrderSqlRes as $yprow)
				{
					$jobDataArr[$yprow['JOBID']]['yarnpuroqty']+=$yprow['YARNPOQTY'];
					$jobDataArr[$yprow['JOBID']]['yarnpuroamt']+=$yprow['YARNPOAMT'];
				}
				unset($yarnPurOrderSqlRes);
				
				$yarnwoSql="select a.job_no_id as JOBID, a.yarn_wo_qty as YARNWOQTY, a.amount as YARNWOAMT, b.currency as CURRENCY from wo_yarn_dyeing_dtls a, wo_yarn_dyeing_mst b, gbl_temp_engine c where 1=1 and a.mst_id=b.id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.job_no_id =c.ref_val and c.user_id = ".$user_id." and c.entry_form=4 and c.ref_from=1";
				//echo $yarnwoSql; die;
				$yarnwoSqlRes = sql_select($yarnwoSql);
				foreach($yarnwoSqlRes as $ywrow)
				{
					$convAmt=0;
					if($ywrow['CURRENCY']==2) $convAmt=$ywrow['YARNWOAMT'];
					else $convAmt=($ywrow['YARNWOAMT']/$jobDataArr[$ywrow['JOBID']]['exchange_rate']);
					$jobDataArr[$ywrow['JOBID']]['yarnwoqty']+=$ywrow['YARNWOQTY'];
					$jobDataArr[$ywrow['JOBID']]['yarnwoamt']+=$convAmt;
				}
				unset($yarnwoSqlRes);
				
				$bookingSql="select a.pre_cost_fabric_cost_dtls_id as BOMDTLSID, a.booking_type as BOOKINGTYPE, a.is_short as ISSHORT, a.EMBLISHMENT_NAME, a.process as PROCESS, a.po_break_down_id as POID, a.wo_qnty as WOQTY, a.amount as WOAMT, b.currency_id as CURRENCY_ID from WO_BOOKING_DTLS a, wo_booking_mst b, gbl_temp_engine c where 1=1 and a.booking_no=b.booking_no and a.booking_type in (2,3,6) and b.booking_type in (2,3,6) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.po_break_down_id =c.ref_val and c.user_id = ".$user_id." and c.entry_form=4 and c.ref_from=2";
				//echo $bookingSql; die;
				$bookingSqlRes = sql_select($bookingSql); $knitingOutServiceRateArr=array();
				foreach($bookingSqlRes as $bookrow)
				{
					$jobmstid="";
					$jobmstid=$poidwisejobArr[$bookrow['POID']];
					
					$bookiingAmt=0;
					if($bookrow['CURRENCY_ID']==2) $bookiingAmt=$bookrow['WOAMT'];
					else $bookiingAmt=($bookrow['WOAMT']/$jobDataArr[$jobmstid]['exchange_rate']);
					
					if($bookrow['BOOKINGTYPE']==3 && $bookrow['PROCESS']==1)
					{
						$bodypartbom=$convRateArr[$bookrow['BOMDTLSID']]['bodypart'];
						$libyarndetaid=$convRateArr[$bookrow['BOMDTLSID']]['libyarndetaid'];
						
						$convReqQtyAmtArr['knitwo'][$bookrow['POID']][$bodypartbom][$libyarndetaid]['kqtywo']+=$bookrow['WOQTY'];
						$convReqQtyAmtArr['knitwo'][$bookrow['POID']][$bodypartbom][$libyarndetaid]['kamtwo']+=$bookiingAmt;
						
						$jobDataArr[$jobmstid]['knitwoqty']+=$bookrow['WOQTY'];
						$jobDataArr[$jobmstid]['knitwoamt']+=$bookiingAmt;
					}
					if($bookrow['BOOKINGTYPE']==3)// Fabric Finishing
					{
						if (in_array($bookrow['PROCESS'], $process_id_fabricfinishing_array))
						{
							$convReqQtyAmtArr['finishwo'][$row['POID']][$body_part_id][$libYarnDetaid]['fqtywo']+=$bookrow['WOQTY'];
							$convReqQtyAmtArr['finishwo'][$row['POID']][$body_part_id][$libYarnDetaid]['famtwo']+=$bookiingAmt;
						}
					}
					/*if($bookrow['BOOKINGTYPE']==3 && $bookrow['PROCESS']==31)
					{
						$jobDataArr[$jobmstid]['dyingwoqty']+=$bookrow['WOQTY'];
						$jobDataArr[$jobmstid]['dyingwoamt']+=$bookiingAmt;
					}*/
					if($bookrow['BOOKINGTYPE']==3 && $bookrow['PROCESS']==35)
					{
						$jobDataArr[$jobmstid]['aopwoqty']+=$bookrow['WOQTY'];
						$jobDataArr[$jobmstid]['aopwoamt']+=$bookiingAmt;
					}
					if($bookrow['BOOKINGTYPE']==2)
					{
						$jobDataArr[$jobmstid]['trimwoamt']+=$bookiingAmt;
					}
					if($bookrow['BOOKINGTYPE']==6 && $bookrow['EMBLISHMENT_NAME']!=3)
					{
						$jobDataArr[$jobmstid]['emblwoamt']+=$bookiingAmt;
					}
					if($bookrow['BOOKINGTYPE']==6 && $bookrow['EMBLISHMENT_NAME']==3)
					{
						$jobDataArr[$jobmstid]['washwoamt']+=$bookiingAmt;
					}
				}
				unset($bookingSqlRes);
				
				/*$sql_batch="select b.po_id as POID, b.batch_qnty as BATCHQTY from pro_batch_create_mst a, pro_batch_create_dtls b, gbl_temp_engine c where a.id=b.mst_id and a.entry_form not in (36,74,17,7,37,14) and a.batch_against!=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_id=c.ref_val and c.user_id = ".$user_id." and c.entry_form=4 and c.ref_from=2";
				//and a.entry_form!=36
				
				$resultBatch=sql_select($sql_batch); 
				foreach($resultBatch as $batchRow) 
				{
					$jobmstid="";
					$jobmstid=$poidwisejobArr[$batchRow['POID']];
					$jobDataArr[$jobmstid]['dyeingProQty']+=$batchRow['BATCHQTY'];
				}
				unset($resultBatch);*/
				
				$sqlinvyarn="SELECT A.ENTRY_FORM, A.ITEM_CATEGORY, A.RECEIVE_BASIS, A.BOOKING_ID, B.TRANSACTION_TYPE as TRANS_TYPE, B.CONS_QUANTITY FROM INV_RECEIVE_MASTER A, INV_TRANSACTION B WHERE A.ID=B.MST_ID AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND A.ITEM_CATEGORY=1 AND A.ENTRY_FORM IN (1) AND B.TRANSACTION_TYPE=1";
				//echo $sqlinvyarn; die;
				$sqlinvyarnRes = sql_select($sqlinvyarn); $piarr=array(); $woArr=array();
				foreach($sqlinvyarnRes as $invrow)
				{
					if($invrow['ENTRY_FORM']==1 && $invrow['TRANS_TYPE']==1)
					{
						if($invrow['RECEIVE_BASIS']==1)//PI
						{
							$piarr[$invrow['BOOKING_ID']]=$invrow['BOOKING_ID'];
						}
						else if($invrow['RECEIVE_BASIS']==2)//WO
						{
							$woArr[$invrow['BOOKING_ID']]=$invrow['BOOKING_ID'];
						}
					}
				}
				
				$con = connect();
				oci_commit($con);
				fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 4, 3, $piarr, $empty_arr);//PI
				$piInWoIdArr=array();
				if(count($piarr)>0)
				{
					$sqlPi="select a.pi_id as PIID, a.work_order_id as WOMSTID from com_pi_item_details a, gbl_temp_engine c where a.STATUS_ACTIVE=1 AND A.IS_DELETED=0 and a.pi_id =c.ref_val and c.user_id = ".$user_id." and c.entry_form=4 and c.ref_from=3";
					$sqlPiRes = sql_select($sqlPi);
					
					foreach($sqlPiRes as $pirow)
					{
						$woArr[$pirow['WOMSTID']]=$pirow['WOMSTID'];
						$piInWoIdArr[$pirow['PIID']]=$pirow['WOMSTID'];
					}
					unset($sqlPiRes);
				}
				
				fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 4, 4, $woArr, $empty_arr);//WO
				disconnect($con);
				
				$jobInWoArr=array();
				if(count($woArr)>0)
				{
					$sqlWo="select a.mst_id as WOID, a.job_id as JOBID from wo_non_order_info_dtls a, gbl_temp_engine c where a.STATUS_ACTIVE=1 AND A.IS_DELETED=0 and a.mst_id =c.ref_val and c.user_id = ".$user_id." and c.entry_form=4 and c.ref_from=4";
					$sqlWoRes = sql_select($sqlWo);
					
					foreach($sqlWoRes as $worow)
					{
						$jobInWoArr[$worow['WOID']]=$worow['JOBID'];
					}
					unset($sqlWoRes);
				}
				
				foreach($sqlinvyarnRes as $invrow)
				{
					$jobmstid="";
					
					if($invrow['RECEIVE_BASIS']==1)//PI
					{
						$jobmstid=$jobInWoArr[$piInWoIdArr[$invrow['BOOKING_ID']]]; 
					}
					else if($invrow['RECEIVE_BASIS']==2)//WO
					{
						$jobmstid=$jobInWoArr[$invrow['BOOKING_ID']]; 
					}
					if($jobidArr[$jobmstid]!="")
					{
						//echo $jobmstid.'='.$invrow['ENTRY_FORM'].'='.$invrow['CONS_QUANTITY'].'<br>';
						if($invrow['ENTRY_FORM']==1 && $invrow['TRANS_TYPE']==1)
						{
							$jobDataArr[$jobmstid]['yarn_rec_qty']+=$invrow['CONS_QUANTITY'];
						}
					}
				}
				unset($sqlinvyarnRes);
				
				$sqlinvtrim="select a.trans_type as TRANS_TYPE, a.entry_form as ENTRY_FORM, a.po_breakdown_id as PO_BREAKDOWN_ID, a.quantity as QUANTITY, a.order_amount as ORDER_AMOUNT from order_wise_pro_details a, gbl_temp_engine c where a.status_active=1 and a.is_deleted=0 and a.entry_form in (24) and a.trans_type in (1) and a.po_breakdown_id=c.ref_val and c.user_id = ".$user_id." and c.entry_form=4 and c.ref_from=2";
				//echo $sqlinvtrim; die;
				$sqlinvtrimRes = sql_select($sqlinvtrim);
				foreach($sqlinvtrimRes as $invrow)
				{
					$jobmstid="";
					$jobmstid=$poidwisejobArr[$invrow['PO_BREAKDOWN_ID']];
					//Grey
					if($invrow['ENTRY_FORM']==1 && $invrow['TRANS_TYPE']==1)
					{
						//$jobDataArr[$jobmstid]['yarn_rec_qty']+=$invrow['QUANTITY'];
					}
					if($invrow['ENTRY_FORM']==24 && $invrow['TRANS_TYPE']==1)
					{
						$jobDataArr[$jobmstid]['trimRecamt']+=$invrow['ORDER_AMOUNT'];
					}
				}
				unset($sqlinvtrimRes);
				
				$sqlYarnAll="SELECT a.po_break_down_id as PO_BREAKDOWN_ID, a.qnty as QUANTITY, a.ITEM_ID, a.ALLOCATION_DATE 
				from inv_material_allocation_dtls a, gbl_temp_engine c 
				where a.status_active=1 and a.is_deleted=0 and a.item_category in (1) and a.po_break_down_id=c.ref_val and c.user_id = ".$user_id." and c.entry_form=4 and c.ref_from=2";
				//echo $sqlYarnAll; die;
				$sqlYarnAllRes = sql_select($sqlYarnAll); $jobmstid=""; $prod_wise_allo_qnty = array();
				$prod_wise_allo_date = array(); $allprodid='';
				foreach($sqlYarnAllRes as $allrow)
				{
					$jobmstid=$poidwisejobArr[$allrow['PO_BREAKDOWN_ID']];
					$jobDataArr[$jobmstid]['yarn_all_qty']+=$allrow['QUANTITY'];
					$jobDataArr[$jobmstid]['all_item'].=$allrow['ITEM_ID'].",";
					$prod_wise_allo_qnty[$jobmstid][$allrow['ITEM_ID']]+=$allrow['QUANTITY'];
					$prod_wise_allo_date[$allrow['ITEM_ID']]=$allrow['ALLOCATION_DATE'];
					
					if($allprodid=="") $allprodid=$allrow['ITEM_ID']; else $allprodid.=','.$allrow['ITEM_ID'];
				}
				unset($sqlYarnAllRes);
				//print_r($prod_wise_allo_date); //die;
				$alloprodidex=array_unique(array_filter(explode(",",$allprodid)));
				
				$trans_sql="SELECT b.id as PROD_ID, a.transaction_date as TRANSACTION_DATE, a.transaction_type as TRANSACTION_TYPE, a.cons_quantity as CONS_QUANTITY, a.cons_amount as CONS_AMOUNT, b.lot as LOT_NO, a.receive_basis as RECEIVE_BASIS  
				from inv_transaction a, product_details_master b
				where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.prod_id in(".implode(",",$alloprodidex).")
				order by b.id, a.id";
				//echo $trans_sql;die;
				$trans_sql_result=sql_select($trans_sql);
				$trans_data = array();
				foreach($trans_sql_result as $row_p)
				{
					if(strtotime($row_p["TRANSACTION_DATE"])<=strtotime($prod_wise_allo_date[$row_p['PROD_ID']]))
					{
						if($row_p["TRANSACTION_TYPE"]==1 || $row_p["TRANSACTION_TYPE"]==4 || $row_p["TRANSACTION_TYPE"]==5)
						{
							$trans_data[$row_p["PROD_ID"]]["qnty"]+=$row_p["CONS_QUANTITY"];
							$trans_data[$row_p["PROD_ID"]]["amt"]+=$row_p["CONS_AMOUNT"];
						}
						else{
							$trans_data[$row_p["PROD_ID"]]["qnty"]-=$row_p["CONS_QUANTITY"];
							$trans_data[$row_p["PROD_ID"]]["amt"]-=$row_p["CONS_AMOUNT"];
						}
					}
				}
				 
				$sqlLab="select a.po_id as PO_ID, a.amount as LABWOAMT, b.currency as CURRENCY from wo_labtest_dtls a, wo_labtest_mst b, gbl_temp_engine c where a.mst_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.po_id=c.ref_val and c.user_id = ".$user_id." and c.entry_form=4 and c.ref_from=2";
				//echo $sqlLab; die;
				$sqlLabRes = sql_select($sqlLab);
				foreach($sqlLabRes as $labrow)
				{
					$jobmstid="";
					$jobmstid=$poidwisejobArr[$labrow['PO_ID']];
					
					$bookiingAmt=0;
					if($labrow['CURRENCY']==2) $bookiingAmt=$labrow['LABWOAMT'];
					else $bookiingAmt=($labrow['LABWOAMT']/$jobDataArr[$jobmstid]['exchange_rate']);
					
					$jobDataArr[$jobmstid]['labwoamt']+=$bookiingAmt;
				}
				unset($sqlLabRes);
				
				$sqlGarmentProd="select a.po_break_down_id as POID, a.production_type as PRODUCTION_TYPE, a.production_quantity as PRODQTY from pro_garments_production_mst a, gbl_temp_engine c where 1=1 and a.status_active=1 and a.is_deleted=0 and a.po_break_down_id=c.ref_val and c.user_id = ".$user_id." and c.entry_form=4 and c.ref_from=2 ";
				//echo $sqlGarProd; die;
				$sqlGarProdRes = sql_select($sqlGarmentProd);
				foreach($sqlGarProdRes as $garprow)
				{
					$jobmstid="";
					$jobmstid=$poidwisejobArr[$garprow['POID']];
					if($garprow['PRODUCTION_TYPE']==1)
					{
						$jobDataArr[$jobmstid]['cutting']+=$garprow['PRODQTY'];
					}
					if($garprow['PRODUCTION_TYPE']==4)
					{
						$jobDataArr[$jobmstid]['sewinginput']+=$garprow['PRODQTY'];
					}
					if($garprow['PRODUCTION_TYPE']==5)
					{
						$jobDataArr[$jobmstid]['sewingoutput']+=$garprow['PRODQTY'];
					}
					if($garprow['PRODUCTION_TYPE']==8)
					{
						$jobDataArr[$jobmstid]['packingfinish']+=$garprow['PRODQTY'];
					}
				}
				unset($sqlGarProdRes);
				
				$sql_ship="select a.po_break_down_id as POID, a.ex_factory_date as EXFACTORYDATE, a.ex_factory_qnty as SHIPQTY from pro_ex_factory_mst a, gbl_temp_engine b where a.po_break_down_id=b.ref_val and b.entry_form=4 and b.ref_from=2 and b.user_id = ".$user_id." and a.is_deleted=0 and a.status_active=1";
				//echo $sql_ship; die;
				$sql_shipRes = sql_select($sql_ship);
				foreach($sql_shipRes as $exrow)
				{
					$jobmstid="";
					$jobmstid=$poidwisejobArr[$exrow['POID']];
					$jobDataArr[$jobmstid]['shipqty']+=$exrow['SHIPQTY'];
					if($date_type == 5 && $start_date!="" && $end_date!=""){
						if(strtotime($start_date)<=strtotime($exrow['EXFACTORYDATE']) && strtotime($end_date)>=strtotime($exrow['EXFACTORYDATE']))
						{
							$jobDataArr[$jobmstid]['curr_shipqty']+=$exrow['SHIPQTY'];
							$jobDataArr[$jobmstid]['exfactorydate'].=','.strtotime($exrow['EXFACTORYDATE']);
						}
					}
					else
					{
						$jobDataArr[$jobmstid]['curr_shipqty']+=$exrow['SHIPQTY'];
						$jobDataArr[$jobmstid]['exfactorydate'].=','.strtotime($exrow['EXFACTORYDATE']);
					}
					//$jobDataArr[$jobmstid]['exfactorydate'].=','.strtotime($exrow['EXFACTORYDATE']);
				}
				unset($sql_shipRes);
				
				// $sqlGray="SELECT a.id as ID, a.knitting_source as KNITTINGSOURCE, b.id as DTLSID, b.body_part_id as BODYPART, b.febric_description_id as FEBRIC_DESCRIPTION_ID, c.po_breakdown_id as PO_BREAKDOWN_ID, c.quantity as QNTY,c.TRANS_TYPE from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, gbl_temp_engine g
				// where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in (2) and c.entry_form in (2) and c.trans_id!=0 and c.po_breakdown_id=g.ref_val and g.user_id = ".$user_id." and g.entry_form=4 and g.ref_from=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";

				$sqlGray="SELECT a.id as ID, a.knitting_source as KNITTINGSOURCE, b.id as DTLSID, b.body_part_id as BODYPART, b.febric_description_id as FEBRIC_DESCRIPTION_ID, c.po_breakdown_id as PO_BREAKDOWN_ID, c.quantity as QNTY,c.TRANS_TYPE from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, gbl_temp_engine g
				where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in (2) and c.entry_form in (2) and c.po_breakdown_id=g.ref_val and g.user_id = ".$user_id." and g.entry_form=4 and g.ref_from=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
				//echo $sqlGray; die;
				$sqlGrayRec=sql_select($sqlGray);
				foreach($sqlGrayRec as $grrow)
				{
					$jobmstid="";
					$jobmstid=$poidwisejobArr[$grrow['PO_BREAKDOWN_ID']];
					$jobDataArr[$jobmstid]['greyProdQty']+=$grrow['QNTY'];
					$greyrate=$greyamt=0;
					// $po_brek .= $grrow['PO_BREAKDOWN_ID'].",";
					// echo $grrow['PO_BREAKDOWN_ID'].'='.$grrow['BODYPART'].'='.$grrow['FEBRIC_DESCRIPTION_ID'].'<br>';
					
					if($grrow['KNITTINGSOURCE']==1) 
					$greyrate=$convReqQtyAmtArr['knit'][$grrow['PO_BREAKDOWN_ID']][$grrow['BODYPART']][$grrow['FEBRIC_DESCRIPTION_ID']]['kamt']/$convReqQtyAmtArr['knit'][$grrow['PO_BREAKDOWN_ID']][$grrow['BODYPART']][$grrow['FEBRIC_DESCRIPTION_ID']]['kqty'];
					else $greyrate=$convReqQtyAmtArr['knitwo'][$grrow['PO_BREAKDOWN_ID']][$grrow['BODYPART']][$grrow['FEBRIC_DESCRIPTION_ID']]['kamtwo']/$convReqQtyAmtArr['knitwo'][$grrow['PO_BREAKDOWN_ID']][$grrow['BODYPART']][$grrow['FEBRIC_DESCRIPTION_ID']]['kqtywo'];
					
					/*if($grrow['KNITTINGSOURCE']==1) echo $grrow['BODYPART'].'='.$grrow['QNTY'].'='.$grrow['KNITTINGSOURCE'].'='.$convReqQtyAmtArr['knit'][$grrow['PO_BREAKDOWN_ID']][$grrow['BODYPART']][$grrow['FEBRIC_DESCRIPTION_ID']]['kamt'].'='.$convReqQtyAmtArr['knit'][$grrow['PO_BREAKDOWN_ID']][$grrow['BODYPART']][$grrow['FEBRIC_DESCRIPTION_ID']]['kqty'].'='.$greyrate.'<br>';
					else echo $grrow['BODYPART'].'='.$grrow['QNTY'].'='.$grrow['KNITTINGSOURCE'].'='.$convReqQtyAmtArr['knitwo'][$grrow['PO_BREAKDOWN_ID']][$grrow['BODYPART']][$grrow['FEBRIC_DESCRIPTION_ID']]['kamtwo'].'='.$convReqQtyAmtArr['knitwo'][$grrow['PO_BREAKDOWN_ID']][$grrow['BODYPART']][$grrow['FEBRIC_DESCRIPTION_ID']]['kqtywo'].'='.$greyrate.'<br>';*/
					if(($greyrate*1)>0) $greyamt=$grrow['QNTY']*$greyrate;
					
					// $jobDataArr[$jobmstid]['greyProdQty']+=$grrow['QNTY'];
					$jobDataArr[$jobmstid]['greyProdAmt']+=$greyamt;
					$a[$grrow['KNITTINGSOURCE']]['qty']+=$grrow['QNTY'];
					$a[$grrow['KNITTINGSOURCE']]['amt']+=$greyamt;
				}
				unset($sqlGrayRec);
				//print_r($a[3]); die;
				

				//$gray_po_bre_ids= implode(",",array_unique(explode(",",chop($po_brek,','))));
				$gray_item_trans_sql = "SELECT A.ID,a.PO_BREAKDOWN_ID,a.TRANS_TYPE,a.ENTRY_FORM ,A.QUANTITY FROM ORDER_WISE_PRO_DETAILS A ,gbl_temp_engine g
				WHERE A.PO_BREAKDOWN_ID = g.ref_val  and g.user_id = ".$user_id." and g.entry_form=4 and g.ref_from=2 AND a.TRANS_TYPE in (5,6) AND a.ENTRY_FORM in (14,82) and a.status_active =1 and a.is_deleted = 0";
				//echo $gray_item_trans_sql;
				$gray_item_result=sql_select($gray_item_trans_sql);
				foreach($gray_item_result as $row)
				{
					$jobmstid="";
					$jobmstid=$poidwisejobArr[$row['PO_BREAKDOWN_ID']];

					if($row['TRANS_TYPE']==5 && $row['ENTRY_FORM']==82)
					{
						$jobDataArr[$jobmstid]['gry_trns_in']  += $row['QUANTITY'];
					}
					elseif($row['TRANS_TYPE']==6 &&  $row['ENTRY_FORM']==82)
					{
						$jobDataArr[$jobmstid]['gry_trns_out'] += $row['QUANTITY'];
					}
					elseif($row['TRANS_TYPE']==5 &&  $row['ENTRY_FORM']==14)
					{
						$jobDataArr[$jobmstid]['knit_fin_trns_in'] += $row['QUANTITY'];	
					}
					elseif($row['TRANS_TYPE']==6 &&  $row['ENTRY_FORM']==14)
					{
						$jobDataArr[$jobmstid]['knit_fin_trns_out'] += $row['QUANTITY'];
					}
				}

				//echo $knit_fin_trns_in."--".$knit_fin_trns_out;
				$sqlFinish="SELECT a.id as ID, a.knitting_source as KNITTINGSOURCE, a.entry_form as ENTRY_FORM, b.id as DTLSID, b.body_part_id as BODYPART, b.fabric_description_id as FEBRIC_DESCRIPTION_ID, c.po_breakdown_id as PO_BREAKDOWN_ID, c.quantity as QNTY from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, gbl_temp_engine g
				where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in (7,17,37) and c.entry_form in (7,17,37) and c.trans_id!=0 and c.po_breakdown_id=g.ref_val and g.user_id = ".$user_id." and g.entry_form=4 and g.ref_from=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
				//echo $sqlFinish; die;
				$sqlFinishRec=sql_select($sqlFinish);
				foreach($sqlFinishRec as $fnrow)
				{
					$jobmstid="";
					$jobmstid=$poidwisejobArr[$fnrow['PO_BREAKDOWN_ID']];
					//$jobDataArr[$jobmstid]['greyProdQty']+=$fnrow['QNTY'];
					$finishrate=$finishamt=0;
					if($fnrow['KNITTINGSOURCE']==1) 
						$finishrate=$convReqQtyAmtArr['finish'][$fnrow['PO_BREAKDOWN_ID']][$fnrow['BODYPART']][$fnrow['FEBRIC_DESCRIPTION_ID']]['famt']/$convReqQtyAmtArr['finish'][$fnrow['PO_BREAKDOWN_ID']][$fnrow['BODYPART']][$fnrow['FEBRIC_DESCRIPTION_ID']]['fqty'];
					else $finishrate=$convReqQtyAmtArr['finishwo'][$fnrow['PO_BREAKDOWN_ID']][$fnrow['BODYPART']][$fnrow['FEBRIC_DESCRIPTION_ID']]['famtwo']/$convReqQtyAmtArr['finishwo'][$fnrow['PO_BREAKDOWN_ID']][$fnrow['BODYPART']][$fnrow['FEBRIC_DESCRIPTION_ID']]['fqtywo'];
					
					if(($finishrate*1)>0) $finishamt=$fnrow['QNTY']*$finishrate;
					if($fnrow['ENTRY_FORM']==17)
					{
						$jobDataArr[$jobmstid]['wfinishProdQty']+=$fnrow['QNTY'];
						$jobDataArr[$jobmstid]['wfinishProdAmt']+=$finishamt;
					}
					else
					{
						$jobDataArr[$jobmstid]['finishProdQty']+=$fnrow['QNTY'];
						$jobDataArr[$jobmstid]['finishProdAmt']+=$finishamt;
					}
				}
				unset($sqlFinishRec);
				
				$sqlInvoice="select a.po_breakdown_id as POID, a.current_invoice_qnty as INVQTY, a.current_invoice_value as INVAMT from com_export_invoice_ship_dtls a, gbl_temp_engine c, com_export_invoice_ship_mst d where d.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and a.current_invoice_qnty>0 and a.po_breakdown_id=c.ref_val and c.entry_form=4 and c.ref_from=2 and c.user_id = ".$user_id."";
			    // echo $sqlInvoice;die;
				$sqlInvoice_res=sql_select($sqlInvoice);
				$invoice_qunty_arr = array();
				foreach($sqlInvoice_res as $irow)
				{
					$jobmstid="";
					$jobmstid=$poidwisejobArr[$irow['POID']];
					
					$cmpcs=$jobDataArr[$jobmstid]['cmpcs'];
					$invoiceqtyCm=$irow['INVQTY']*$cmpcs;
					$jobDataArr[$jobmstid]['invoiceqtyCm']+=$invoiceqtyCm;

					$invoice_qunty_arr[$jobmstid]['INVQTY'] += $irow['INVQTY'];
					$invoice_qunty_arr[$jobmstid]['INVO_VALUE'] += $irow['INVAMT'];
				}
				// echo "<pre>";
				// print_r($jobDataArr);
				// die;

				unset($sqlInvoice_res);

				//com_export_invoice_ship_dtls

				// com_export_invoice_ship_mst
				
				$con = connect();
				execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ENTRY_FORM=4");
				oci_commit($con);
				disconnect($con);

				$others_cost_value=0; $fabric_cost=0; $trims_cost=0; $embel_cost=0; $comm_cost=0; $commission=0; $lab_test=0; $inspection=0; $cm_cost=0; $freight=0; $currier_pre_cost=0; $certificate_pre_cost=0; $common_oh=0; $totalfinFabQty = 0; $order_values = 0; $wvnFinReqAmt = 0; $finishProdAmt = 0; $wvnFinReqQty = 0; $finreqqty = 0; $WfinishProdAmt= 0; $finFabQty = 0; 
				$i=1;
				foreach($jobDataArr as $jobid=>$datarow)
				{
					if($datarow['poqty']>0)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$ponostr=$poids=$pubshipdate=$exfactorydate="";
						
						$poqty=$poqtypcs=$poamt=$pounitprice=$planQty=$planQtyPcs=$avgunitprice=$netpoamt=$yarnAllocationBalance=$yarnDyeingWoQtyBal=$yarnDyeingWoAmtBal=$greyProdQtyBal=$greyProdAmtBal=$dyingwoqtyBal=$aopWoQtyBal=$aopWoAmtBal=$trimWoAmtBal=$trimRecAmtBal=$finishProdQtyBal=$finishProdAmtBal=$emblWoAmtBal=$labWoAmtBal=$otherBomAmt=$totalBudgetCost=$totalWoCost=$cuttingBal=$sewingwip=$finishwip=$salesCm=$exfactfinishBal=$exfactOrderBal=$shortShipQty=$excessShipQty=$shipamt=$interestamt=$incometexamt =0;
						$WvnGreyProdQtyBal=0;  
						$WvnFinishProdQtyBal=0;
						$WvnFinishProdAmtBal=$exchange_rate=0;
						$exgmtsitemid=explode(",",$datarow['gmts_item']);
						$gmtsitemname="";
						foreach($exgmtsitemid as $gmtsitemid)
						{
							if($gmtsitemname=="") $gmtsitemname=$garments_item[$gmtsitemid]; else $gmtsitemname.=','.$garments_item[$gmtsitemid];
						}
						
						$exchange_rate=$jobDataArr[$jobid]['exchange_rate'];
 
						$fab_purchase_knit2  = $datarow['greyreqamt'];//array_sum($fabric_costing_arr2['knit']['grey'][$datarow['job_no']]);
						$fab_purchase_woven2 = $datarow['wvnGreyReqAmt'];//array_sum($fabric_costing_arr2['woven']['grey'][$datarow['job_no']]);
 
						$yarn_costing=$datarow['yarnamt'];//$yarn_costing_arr[$datarow['job_no']];
						$tot_fabric_cost=$fab_purchase_knit2+$fab_purchase_woven2;
						$conversion_cost=$datarow['convamt'];//array_sum($conversion_costing_arr_process[$datarow['job_no']]);
						$freight_cost=$datarow['freight'];//$other_costing_arr[$datarow['job_no']]['freight'];
						$inspection_cost=$datarow['inspection'];//$other_costing_arr[$datarow['job_no']]['inspection'];
						$certificate_cost=$datarow['certificate'];//$other_costing_arr[$datarow['job_no']]['certificate_pre_cost'];
						$common_oh=$datarow['comm_oh'];//$other_costing_arr[$datarow['job_no']]['common_oh'];
						$currier_cost=$datarow['courier'];//$other_costing_arr[$datarow['job_no']]['currier_pre_cost'];
						$cm_cost=$datarow['cmcost'];//$other_costing_arr[$datarow['job_no']]['cm_cost'];
						$lab_test_cost=$datarow['labtest'];//$other_costing_arr[$datarow['job_no']]['lab_test'];
						$depr_amor_pre_cost=$datarow['depamrtamt'];//$other_costing_arr[$datarow['job_no']]['depr_amor_pre_cost'];
						$deffdlc_cost=$datarow['lccost'];//$other_costing_arr[$datarow['job_no']]['deffdlc_cost'];
						$fabric_cost=$tot_fabric_cost;
						$trims_cost=$datarow['trimsamt'];//$trim_job_amountArr[$datarow['job_no']];
						$embel_cost=$datarow['emblamt'];//$emblishment_costing_arr[$datarow['job_no']];
						$wash=$datarow['washamt'];//$emblishment_costing_arr_wash[$datarow['job_no']];
						$commercial_cost=$datarow['commercial'];//$commercial_costing_arr[$datarow['job_no']];
						$commission = $datarow['commission'];
						
						$interestamt=$datarow['interest'];//$commercial_costing_arr[$datarow['job_no']];
						$incometexamt = $datarow['incometex'];

						$comm_cost              = $datarow["comm_cost"];
						$cm_cost_dzn            = $datarow["cm_cost"];
						$deffdlc_cost_dzn       = $datarow["deffdlc_cost"];
						$interest_cost          = $datarowd["interest_cost"];
						$incometax_cost         = $datarow["incometax_cost"];
						$deffdlc_percent        = $datarow["deffdlc_percent"];
						$interest_percent       = $datarow["interest_percent"];
						$incometax_percent      = $datarow["incometax_percent"];
						$depr_amor_po_price     = $datarow["depr_amor_po_price"];
						$depr_amor_pre_cost_dzn = $datarow["depr_amor_pre_cost"];

						
						$lab_test = $lab_test_cost;
						$inspection = $inspection_cost;
						$cm_cost = $cm_cost;
						$freight = $freight_cost;
						$currier_pre_cost = $currier_cost;
						$certificate_pre_cost = $certificate_cost;
						$common_oh = $common_oh;

						$all_total_cost = $tot_fabric_cost+$yarn_costing+$conversion_cost+$trims_cost+$embel_cost+$wash+$commercial_cost+$commission+$lab_test_cost+$cm_cost+$currier_pre_cost+$inspection_cost+$freight+$common_oh+$certificate_pre_cost+$depr_amor_pre_cost+$interestamt+$incometexamt+$deffdlc_cost; 
						
						$others_cost_value = $all_total_cost-$cm_cost-$freight-$commercial_cost-$commission;

					    $order_values = $datarow['order_value'];
						$order_net_value = $order_values-($commission+$commercial_cost+$freight);
						$otherCost = $others_cost_value;
						$cmValue = $order_net_value-$otherCost; 
						//echo $all_total_cost.'=='.$tot_fabric_cost.'=='.$yarn_costing.'=='.$conversion_cost.'=='.$trims_cost.'=='.$embel_cost.'=='.$wash.'=='.$commercial_cost.'=='.$commission.'=='.$lab_test_cost.'=='.$cm_cost.'=='.$currier_pre_cost.'=='.$inspection_cost.'=='.$freight.'=='.$common_oh.'=='.$certificate_pre_cost.'=='.$depr_amor_pre_cost.'=='.$interestamt.'=='.$incometexamt.'=='.$deffdlc_cost;// die;
						
						$ponostr = implode(",",array_unique(array_filter(explode(",", $datarow['po_number']))));
						$poids = implode(",",array_unique(array_filter(explode(",", $datarow['poid']))));
						$pubshipdate = min(array_unique(array_filter(explode(",", $datarow['pubshipdate']))));
						$exfactorydate = max(array_unique(array_filter(explode(",", $datarow['exfactorydate']))));
						
						$poqty = $datarow['poqty']/$datarow['setratio'];
						$poqtypcs = $datarow['poqty'];
						$planQty = $datarow['planqty']/$datarow['setratio'];
						$planQtyPcs = $datarow['planqty'];
						$avgunitprice = $datarow['poqamt']/$poqty;
						
						$netpoamt = $datarow['poqamt']-$datarow['commission'];
						$yarnAllocationBalance = $datarow['yarnqty']-$datarow['yarn_all_qty'];
						$yarnDyeingWoQtyBal = $datarow['ydyingqty']-$datarow['yarnwoqty'];
						$yarnDyeingWoAmtBal = $datarow['ydyingamt']-$datarow['yarnwoamt'];
						
						$greyProdAmtBal = $datarow['knitreqamt']-$datarow['greyProdAmt'];
						//$dyingwoqtyBal=$datarow['fabdyingqty']-$datarow['dyingwoqty'];
						$aopWoQtyBal = $datarow['aopqty']-$datarow['aopwoqty'];
						$aopWoAmtBal = $datarow['aopamt']-$datarow['aopwoamt'];
						$trimWoAmtBal = $datarow['trimsamt']-$datarow['trimwoamt'];
						$trimRecAmtBal = $datarow['trimsamt']-$datarow['trimRecamt'];

						// echo $datarow['FAB_NATURE_ID'];die;
						$greyProdQtyBal=$finishProdQtyBal=$finishProdAmtBal=$finreqqty=$finFabQty=$knit_fin_trns_in=$knit_fin_trns_out=$finishProdQty=$finreqamt=$finishProdAmt=0;
						$WvnGreyProdQtyBal=$WvnFinishProdQtyBal=$WvnFinishProdAmtBal=$wfinFabQty=$totalfinFabQty=$wvnFinReqAmt=$WfinishProdAmt=$wvnFinReqQty=0;
						if($datarow['FAB_NATURE_ID'] ==2){
							$greyProdQtyBal   = $datarow['greyreqqty']-$datarow['greyProdQty'];
							$finishProdQtyBal = $datarow['finreqqty']-$datarow['finishProdQty'];
							$finishProdAmtBal = $datarow['finreqamt']-$datarow['finishProdAmt'];

							$finreqqty = fn_number_format($datarow['finreqqty'], 4);
							$finFabQty = $datarow['finishProdQty']+$datarow['knit_fin_trns_in']-$datarow['knit_fin_trns_out'];
							$knit_fin_trns_in = $datarow['knit_fin_trns_in'];
							$knit_fin_trns_out = $datarow['knit_fin_trns_out'];
							$finishProdQty= $datarow['finishProdQty'];
							$finreqamt = fn_number_format($datarow['finreqamt'], 4);
							$finishProdAmt = fn_number_format($datarow['finishProdAmt'], 4);
						}
						else if($datarow['FAB_NATURE_ID'] ==3){
							$WvnGreyProdQtyBal   = $datarow['wvnGreyReqQty']-$datarow['greyProdQty'];
							$WvnFinishProdQtyBal = $datarow['wvnFinReqQty']-$datarow['wfinishProdQty'];
							$WvnFinishProdAmtBal = $datarow['wvnFinReqAmt']-$datarow['wfinishProdAmt'];

							$wfinFabQty = $datarow['wfinishProdQty']+$datarow['knit_fin_trns_in']-$datarow['knit_fin_trns_out'];
							$totalfinFabQty = fn_number_format($wfinFabQty, 4);
							$wvnFinReqAmt = fn_number_format($datarow['wvnFinReqAmt'], 4);
							$WfinishProdAmt = fn_number_format($datarow['wfinishProdAmt'], 4);
							$wvnFinReqQty = fn_number_format($datarow['wvnFinReqQty'], 4);
						}
 
						$emblWoAmtBal = $datarow['emblamt']-$datarow['emblwoamt'];
						$washWoAmtBal = $datarow['washamt']-$datarow['washwoamt'];
						$labWoAmtBal = $datarow['labtest']-$datarow['labwoamt'];
						$otherBomAmt = $datarow['design']+$datarow['studio']+$datarow['freight']+$datarow['courier']+$datarow['certificate'];
						
						$totalBudgetCost=$datarow['commission']+$datarow['yarnamt']+$datarow['ydyingamt']+$datarow['greyreqamt']+$datarow['knitreqamt']+$datarow['fabdyingamt']+$datarow['aopamt']+$datarow['trimsamt']+$datarow['emblamt']+$datarow['labtest']+$datarow['inspection']+$otherBomAmt+$datarow['commercial']+$datarow['washamt'];
						//echo $datarow['commission'].'=commission+'.$datarow['yarnamt'].'=yarnamt+'.$datarow['ydyingamt'].'=ydyingamt+'.$datarow['greyreqamt'].'=greyreqamt+'.$datarow['knitreqamt'].'=knitreqamt+'.$datarow['fabdyingamt'].'=fabdyingamt+'.$datarow['aopamt'].'=aopamt+'.$datarow['trimsamt'].'=trimsamt+'.$datarow['emblamt'].'=emblamt+'.$datarow['labtest'].'=labtest+'.$datarow['inspection'].'=inspection+'.$otherBomAmt.'=otherBomAmt+'.$datarow['commercial'].'=commercial';
						
						$totalWoCost=$datarow['yarnwoamt']+$datarow['aopwoamt']+$datarow['trimwoamt']+$datarow['labwoamt']+$datarow['emblwoamt']+$datarow['washwoamt'];
						
						$cuttingBal=$planQtyPcs-$datarow['cutting'];
						$sewingwip=$datarow['sewinginput']-$datarow['sewingoutput'];
						$finishwip=$datarow['sewingoutput']-$datarow['packingfinish'];
						$exfactfinishBal=$datarow['packingfinish']-$datarow['shipqty'];
						$exfactOrderBal=$poqtypcs-$datarow['shipqty'];
						$salesCm=$datarow['curr_shipqty']*($cmValue/$poqtypcs);
						
						$exshipstatus=array_filter(array_unique(explode(",",$datarow['shipstatus'])));
						//print_r($shipstatus); die;
						$shipstatusstr="";
						foreach($exshipstatus as $sdata)
						{
							if($sdata==3 && $shipstatusstr=="") $shipstatusstr="Full Shipment";
							else if($sdata==2) $shipstatusstr="Partial Shipment";
							else if($shipstatusstr=="") $shipstatusstr="Pending";
						}
						if($shipstatusstr=="Full Shipment" && $poqtypcs>$datarow['shipqty'])
						{
							$shortShipQty=$poqtypcs-$datarow['shipqty'];
						}
						else if($shipstatusstr=="Full Shipment" && $poqtypcs<$datarow['shipqty'])
						{
							$excessShipQty=$datarow['shipqty']-$poqtypcs;
						}
						
						$shipamt=$datarow['shipqty']*$avgunitprice;
						
						?>
						<tr bgcolor="<?=$bgcolor; ?>" onclick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');" id="tr_<?=$i; ?>">
						    <td width="30" align="center"><?=$i; ?></td>
							<td width="70" style="word-break:break-all"><?=$buyer_library[$datarow['buyer']];?></td>
							<td width="70"  title="<?php echo 'PO ID:'. $jobid;?>" style="word-break:break-all"><?=$datarow['job_no']; ?></td>
							<td width="50" align="center"><?=$datarow['job_year'];?></td>
							<td width="110" style="word-break:break-all"><?=$gmtsitemname; ?></td>
							<td width="110" style="word-break:break-all"><?=$datarow['style_ref_no'];?></td>
							<td width="110" style="word-break:break-all"><?=$ponostr; ?></td>
							<td width="50" style="word-break:break-all"><? echo $pre_Cost_approv_status[$datarow['approved']];?></td>
							<td width="40"><?=$unit_of_measurement[$datarow['order_uom']]; ?></td>
							<td width="80" align="right"><?= fn_number_format($poqty,0); ?></td>
							<td width="80" align="right"><?= fn_number_format($poqtypcs,0);?></td>
							<td width="80" align="right"><?= fn_number_format($planQty,0);?></td>
							<td width="80" align="right"><?= fn_number_format($planQtyPcs,0);?></td>
							<td width="50" style="word-break:break-all" align="right"><?=fn_number_format($avgunitprice,4);?></td>
							<td width="90" style="word-break:break-all" align="right"><?=fn_number_format($datarow['poqamt'],4);?></td>
							<td width="70" align="right"><?= fn_number_format($datarow['commission'],4);?></td>
							<td width="90" style="word-break:break-all" align="right"><?=fn_number_format($netpoamt,4);?></td>
							<td width="70"><?=change_date_format($pubshipdate); ?></td>
							<td width="70"><?=change_date_format(date("d-M-Y",$exfactorydate)); ?><!--1560--></td>
							<td width="70" align="right"><?= fn_number_format($datarow['yarnqty'],4);?></td>
							
							
							<td width="70" align="right"><?= fn_number_format($datarow['yarnpuroqty'],4);?></td>
							<td width="70" align="right"><?= fn_number_format($datarow['yarn_rec_qty'],4);?></td>
							<td width="70" align="right"><?= fn_number_format($datarow['yarn_all_qty'],4);?></td>
							<td width="70" align="right"><?= fn_number_format($yarnAllocationBalance,4);?></td>
							<td width="70" align="right"><?= fn_number_format($datarow['yarnamt'],4);?></td>
							<td width="70" align="right" title="<?=$exchange_rate; ?>">
								<? 
								$all_item_data_arr=array_unique(explode(',',chop($datarow['all_item'],',')));
								$allc_amnt = 0;
								foreach($all_item_data_arr as $item_id)
								{
									$item_rate=0;
									if($trans_data[$item_id]["amt"]!=0 && $trans_data[$item_id]["qnty"]!=0){
										$item_rate = $trans_data[$item_id]["amt"]/$trans_data[$item_id]["qnty"];
									}
									$allc_amnt += $prod_wise_allo_qnty[$jobid][$item_id]*($item_rate/$exchange_rate);
									//echo $prod_wise_allo_qnty[$jobid][$item_id].'='.$item_rate.'='.$item_id.'='.$exchange_rate.'<br>';
								}
								echo number_format($allc_amnt,2);
								?>
							</td>
							<td width="70" align="right"><? $all_bl_amnt = $datarow['yarnamt']-$allc_amnt ;  echo number_format($all_bl_amnt,2) ; ?></td>
							<td width="70" align="right"><?= fn_number_format($datarow['ydyingqty'],4);?></td>
							<td width="70" align="right"><?= fn_number_format($datarow['yarnwoqty'],4);?></td>
							<td width="70" align="right"><?= fn_number_format($yarnDyeingWoQtyBal,4);?></td>
							<td width="70" align="right"><?= fn_number_format($datarow['ydyingamt'],4);?></td>
							<td width="70" align="right"><?= fn_number_format($datarow['yarnwoamt'],4);?></td>
							<td width="70" align="right"><?= fn_number_format($yarnDyeingWoAmtBal,4);?></td>
							<td width="70" align="right"><?= fn_number_format($datarow['greyreqqty'],4);?></td>
							<td width="70" align="right">
								<?
								$gryProQty = $datarow['greyProdQty']+$datarow['gry_trns_in']-$datarow['gry_trns_out'];
								?>
								<a href="##" onclick="fnc_gray_view('gray_popup','Gray Production',<?=$datarow['gry_trns_in'];?>,'<?=$datarow['gry_trns_out'];?>','<?=$datarow['greyProdQty'];?>')"><?= fn_number_format($gryProQty,4);?> </a>
							</td>
							<td width="70" align="right"><?= fn_number_format($greyProdQtyBal,4);?></td>
							<td width="70" align="right"><?= fn_number_format($datarow['knitreqamt'],4);?></td>
							<td width="70" align="right"><?= fn_number_format($datarow['greyProdAmt'],4);?></td>
							<td width="70" align="right"><?= fn_number_format($greyProdAmtBal,4);?></td>
							<td width="70" align="right"><?= fn_number_format($datarow['aopqty'],4);?></td>
							
							
							<td width="70" align="right"><?= fn_number_format($datarow['aopwoqty'],4);?></td>
							<td width="70" align="right"><?= fn_number_format($aopWoQtyBal,4);?></td>
							<td width="70" align="right"><?= fn_number_format($datarow['aopamt'],4);?></td>
							<td width="70" align="right"><?= fn_number_format($datarow['aopwoamt'],4);?></td>
							<td width="70" align="right"><?= fn_number_format($aopWoAmtBal,4);?></td>


							<td width="70" align="right"><?= $finreqqty;?></td>
							<td width="70" align="right">
								<a href="##" onclick="fnc_gray_view('gray_popup','Knit Finish Fabric','<?=$knit_fin_trns_in;?>','<?=$knit_fin_trns_out;?>','<?=$finishProdQty;?>')"><?= fn_number_format($finFabQty, 4);?> </a>
							</td>
							<td width="70" align="right"><?= fn_number_format($finishProdQtyBal, 4); ?></td>
							<td width="70" align="right"><?= $finreqamt; ?></td>
							<td width="70" align="right"><?= $finishProdAmt; ?></td>
							<td width="70" align="right"><?= fn_number_format($finishProdAmtBal, 4); ?></td>

							<td width="70" align="right"><?= $wvnFinReqQty;?></td>
							<td width="70" align="right"><?= $totalfinFabQty;?></td>
							<td width="70" align="right"><?= fn_number_format($WvnFinishProdQtyBal, 4); ?></td>
							<td width="70" align="right"><?= $wvnFinReqAmt; ?></td>
							<td width="70" align="right"><?= $WfinishProdAmt; ?></td>
							<td width="70" align="right"><?= fn_number_format($WvnFinishProdAmtBal, 4); ?></td>


							<td width="70" align="right"><?= fn_number_format($datarow['trimsamt'], 4); ?></td>
							<td width="70" align="right"><?= fn_number_format($datarow['trimwoamt'], 4 ); ?></td>
							<td width="70" align="right"><?= fn_number_format($datarow['trimRecamt'], 4); ?></td>
							
							<td width="70" align="right"><?= fn_number_format($trimWoAmtBal, 4); ?></td>
							<td width="70" align="right"><?= fn_number_format($trimRecAmtBal, 4); ?></td>
							<td width="70" align="right"><?= fn_number_format($datarow['emblamt'], 4); ?></td>
							<td width="70" align="right"><?= fn_number_format($datarow['emblwoamt'], 4); ?></td>
							<td width="70" align="right"><?= fn_number_format($emblWoAmtBal ,4); ?></td>
                            
                            <td width="70" align="right"><?= fn_number_format($datarow['washamt'], 4); ?></td>
							<td width="70" align="right"><?= fn_number_format($datarow['washwoamt'], 4); ?></td>
							<td width="70" align="right"><?= fn_number_format($washWoAmtBal ,4); ?></td>
                            
							<td width="70" align="right"><?= fn_number_format($datarow['labtest'], 4); ?></td>
							<td width="70" align="right"><?= fn_number_format($datarow['labwoamt'], 4); ?></td>
							<td width="70" align="right"><?= fn_number_format($labWoAmtBal, 4); ?></td>
							<td width="70" align="right"><?= fn_number_format($datarow['inspection'],4); ?></td>
							<td width="70" align="right"><?= fn_number_format($otherBomAmt, 4); ?></td>
							<td width="70" align="right"><?= fn_number_format($datarow['commercial'], 4); ?></td>
							<td width="100" align="right"><?= fn_number_format($totalBudgetCost, 4); ?></td>
							<td width="100" align="right"><?= fn_number_format($totalWoCost, 4); ?></td>
							<td width="70" align="right"><?= fn_number_format($datarow['cmcost'], 4); ?></td>
							<td width="70" align="right"><?=  fn_number_format($datarow['cutting'], 0); ?></td>
							<td width="70" align="right"><?=fn_number_format($cuttingBal, 0); ?></td>
							<td width="70" align="right"><?=fn_number_format($datarow['sewinginput'], 0); ?></td>
							<td width="70" align="right"><?= fn_number_format($datarow['sewingoutput'], 0); ?></td>
							<td width="70" align="right"><?= fn_number_format($sewingwip, 0); ?></td>
							<td width="70" align="right"><?= fn_number_format($datarow['packingfinish'],0); ?></td>
							
							<td width="70" align="right"><?= fn_number_format($finishwip, 0); ?></td>
                            <td width="70" align="right"><?= fn_number_format($datarow['curr_shipqty'], 0); ?></td> 
							<td width="70" align="right"><?= fn_number_format($datarow['shipqty'], 0); ?></td>
							<td width="70" align="right"><?= fn_number_format($exfactfinishBal, 0); ?></td>
							<td width="70" align="right"><?= fn_number_format($exfactOrderBal, 0); ?></td>
							<td width="70" align="right"><?= fn_number_format($shortShipQty, 0); ?></td>
							<td width="70" align="right"><?= fn_number_format($excessShipQty, 0); ?></td>
							<td width="100"><?= $shipstatusstr; ?></td>
							<td width="70" align="right"><?= fn_number_format($shipamt, 0); ?></td>
							<td width="70" align="right"><?= fn_number_format($datarow['cmdzn'], 4); ?></td>
							<td width="70" align="right"><?= fn_number_format($datarow['cmpcs'], 4); ?></td>
							<td width="70" align="right"><?= fn_number_format($datarow['cmcost'], 2); ?></td>
							<td width="70" align="right"><?= fn_number_format(($cmValue/$poqtypcs)*12, 4); ?></td>
							<td width="70" align="right"><?= fn_number_format($cmValue/$poqtypcs, 4); ?></td>
							<td width="70" align="right"><?= fn_number_format($cmValue, 2); ?> </td>
                            <td width="70" align="right"><?= fn_number_format($salesCm, 2); ?> </td>
							<td width="80" align="right"><?= fn_number_format($datarow['invoiceqtyCm'], 0); ?></td>
							<td width="100" style="word-break:break-all"><?=$team_library[$datarow['teamleader']];?></td>
							<td width="110" style="word-break:break-all"><?=$dealing_merchant_array[$datarow['dealing_marchant']];?></td>
							<td width="50" align="right"><?=fn_number_format($invoice_qunty_arr[$datarow['job_id']]['INVQTY'], 0); ?></td>
							<td width="50" align="right"><?=fn_number_format($invoice_qunty_arr[$datarow['job_id']]['INVO_VALUE'], 2); ?></td>
							<td align="center" onClick="fnc_open_view('export_popup','Export CI Statement',<?=$cbo_company_name; ?>,'<?=$datarow['job_id']; ?>','<?=$datarow['job_no'];?>','<?=$poids;?>')"><a href="##" >View </a>
							</td> 
						</tr>
						<?
						$i++;
					}
				}
				?>
				</tbody>
            </table>
        </div>
        <table width="7800" cellspacing="0" border="1" class="rpt_table" rules="all" id="report_table_footer" >
            <tfoot>
                <th width="30"><!--SL--></th>
                <th width="70"><!--Buyer--></th>
                <th width="70"><!--Job No--></th>
                <th width="50"><!--Job Year--></th>
                <th width="110"><!--Garments Item--></th>
                <th width="110"><!--Style Ref.--></th>
                <th width="110"><!--PO No--></th>
                <th width="50"><!--Approval Status--></th>
                <th width="40"><!--UOM--></th>
                <th width="80"><!--PO Qty--></th>
                <th width="80"><!--PO Qty [Pcs]--></th>
                <th width="80"><!--Plan Qty--></th>
                <th width="80"><!--Plan Qty [Pcs]--></th>
                <th width="50"><!--Avg. Unit Price--></th>
                <th width="90"><!--PO Value--></th>
                <th width="70"><!--Commission--></th>
                <th width="90"><!--Net PO Value--></th>
                <th width="70"><!--Shipment Date--></th>
                <th width="70"><!--Ex-Factory Date--><!--1560--></th>
                <th width="70"><!--Yarn Budget [Qty]--></th>
                
                <th width="70"><!--Yarn WO [Qty]--></th>
                <th width="70"><!--Yarn Rcv [Qty]--></th>
                <th width="70"><!--Yarn Alloc. [Qty]--></th>
                <th width="70"><!--Alloc. BL [Qty]--></th>
                <th width="70"><!--Yarn Budget [Amt.]--></th>
                <th width="70"><!--Yarn Alloc. [Amt.]--></th>
                <th width="70"><!--Alloc. BL [Amt.]--></th>
                <th width="70"><!--Dyed Yarn Budget [Qty]--></th>
                <th width="70"><!--Dyed Yarn WO [Qty]--></th>
                <th width="70"><!--YD WO BL [Qty]--></th>
                <th width="70"><!--Dyed Yarn Budget [Amt.]--></th>
                <th width="70"><!--Dyed Yarn WO [Amt.]--></th>
                <th width="70"><!--YD WO BL [Amt.]--></th>
                <th width="70"><!--Grey Req. [Qty]--></th>
                <th width="70"><!--Grey Prod [Qty]--></th>
                <th width="70"><!--Grey Prod BL [Qty]--><!--1190--></th>
                <th width="70"><!--Knit Budget Cost--></th>
                <th width="70"><!--Knit Prod. Cost--></th>
                <th width="70"><!--Knitting BL Cost--></th>
                <th width="70"><!--AOP Req. [Qty]--></th>
                
                <th width="70"><!--AOP WO [Qty]--></th>
                <th width="70"><!--AOP WO BL [Qty]--></th>
                <th width="70"><!--AOP Budget [Amt.]--></th>
                <th width="70"><!--AOP WO [Amt.]--></th>
                <th width="70"><!--AOP WO BL [Amt.]--></th>
                <th width="70"><!--Knit Fin Fab Req.[Qty]--></th>
                <th width="70"><!--Knit Fin Fab Rec. [Qty]--></th>
                <th width="70"><!--Knit Fin Fab BL [Qty]--></th>
                <th width="70"><!--Knit Fin Fab Req. [Amt.]--></th>
                <th width="70"><!--Knit Fin Fab [Amt.]--></th>
                <th width="70"><!--Knit Fin Fab BL [Amt.]--></th>
                <th width="70"><!--Woven Fin Fab Req.[Qty]--></th>
                <th width="70"><!--Woven Fin Fab Rec. [Qty]--></th>
                <th width="70"><!--Woven Fin Fab BL [Qty]--></th>
                <th width="70"><!--Woven Fin Fab Req. [Amt.]--></th>
                <th width="70"><!--Woven Fin Fab [Amt.]--></th>
                <th width="70"><!--Woven Fin Fab BL [Amt.]--></th>
                <th width="70"><!--Trims Budget [Amt.]--></th>
                <th width="70"><!--Trims WO [Amt.]--></th>
                <th width="70"><!--Trims Recv. [Amt.]--></th>
                
                <th width="70"><!--Trims WO BL [Amt.]--></th>
                <th width="70"><!--Trims Rcv. BL [Amt.]--></th>
                <th width="70"><!--Embel. Budget [Amt.]--></th>
                <th width="70"><!--Embel. WO [Amt.]--></th>
                <th width="70"><!--Embel. WO BL [Amt.]--></th>
                
                <th width="70"><!--Wash Budget [Amt.]--></th>
                <th width="70"><!--Wash WO [Amt.]--></th>
                <th width="70"><!--Wash WO BL [Amt.]--></th>
                
                <th width="70"><!--Lab Test Budget [Amt.]--></th>
                <th width="70"><!--Lab Test WO [Amt.]--></th>
                <th width="70"><!--Lab Test WO BL [Amt.]--></th>
                <th width="70"><!--Inspection [Amt.]--></th>
                <th width="70"><!--Other [Amt.]--></th>
                <th width="70"><!--Commicial [Amt.]--></th>
                <th width="100"><!--Total Budget [Amt.]--></th>
                <th width="100"><!--Total W/O [Amt.]--><!--2650--></th>
                <th width="70"><!--Contribution Margin--></th>
                <th width="70"><!--Cutting Production--></th>
                <th width="70"><!--Cutting BL--></th>
                <th width="70"><!--Sewing Input--></th>
                <th width="70"><!--Sewing Output--></th>
                <th width="70"><!--Sew WIP--></th>
                <th width="70"><!--Packing & Finishing--></th>
                
                <th width="70">Total:<!--Finish WIP--></th>
                <th width="70" id="value_currexqty"><!--Curr Ex-Fact [Qty]--></th>
                <th width="70" id="value_exqty"><!--Total Ex-Fact [Qty]--></th>
                <th width="70"><!--Ex-Fact BL To Fin. [Qty]--></th>
                <th width="70"><!--Ex-Fact BL To Ord [Qty]--></th>
                <th width="70"><!--Short Ship [Qty]--></th>
                <th width="70"><!--Excess Ship [Qty]--></th>
                <th width="100"><!--Shipment Status--></th>
                <th width="70" id="value_totalShipamt"><!--Total Ship [Amt.]--></th>
                <th width="70"><!--CM/Dzn Cost--></th>
                <th width="70"><!--CM/Pcs Cost--></th>
                <th width="70" id="value_totalcmcost"><!--Total CM Cost--></th>
                <th width="70"><!--CM DZN--></th>
                <th width="70"><!--CM Pcs--></th>
                <th width="70" id="value_totalcm"><!--Total CM--></th>
                <th width="70" id="value_salescm"><!--Sales CM--></th>
                <th width="80"><!--Total CM as per Invoice Qty--></th>
                <th width="100"><!--Team--></th>
                <th width="110"><!--Dealing Merchant--></th>
                <th width="50" id="value_invoiceqty"><!--Invoice Qnty.--></th>
                <th width="50" id="value_invoiceamt"><!--Net Invoice Amount--></th>
                <th><!--Export CI Statement--></th>
            </tfoot>
        </table>
    <?
	//die;
	
	if($template==1)
	{
	?>
        <div style="width:3050px">
        <fieldset style="width:100%;">
          <table width="3030">
              <tr class="form_caption">
                    <td colspan="34" align="center">Cost Breakdown Report</td>
                </tr>
                <tr class="form_caption">
                    <td colspan="34" align="center"><? echo $company_library[$company_name]; ?></td>
                </tr>
            </table>
            <table id="table_header_1" class="rpt_table" width="3030" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <th width="40">SL</th>
                    <th width="70">Job No</th>
                    <th width="50">Year</th>
                    <th width="70">Buyer</th>
                    <th width="70">File No</th>
                    <th width="80">Ref. No</th>
                    <th width="100">Team</th>
                    <th width="110">Dealing Merchant</th>
                    <th width="110">Order No</th>
                    <th width="110">Style Ref.</th>
                    <th width="110">Garments Item</th>
                    <th width="90">Order Qnty</th>
                    <th width="50">UOM</th>
                    <th width="90">Qnty (Pcs)</th>
                    <th width="80">Shipment Date</th>
                    <th width="220">Fabric Description</th>
                    <th width="70">Knit Fab. Cons</th>
                    <th width="60">Knit Fab. Rate</th>
                    <th width="70">Woven Fab. Cons</th>
                    <th width="65">Woven Fab. Rate</th>
                    <th width="80">Fab. Cost</th>
                    <th width="80">Trims cost</th>
                    <th width="80">Print/Emb cost</th>
                    <th width="80">CM cost</th>
                    <th width="85">Commission</th>
                    <th width="80">Other Cost</th>
                    <th width="80">Tot. cost</th>
                    <th width="100">Total CM cost</th>
                    <th width="100">Total Cost</th>
                    <th width="65">Cost Per unit</th>
                    <th width="65">Order Price</th>
                    <th width="100">Order Value</th>
                    <th width="100">Margin</th>
                    <th width="100">Total Trims Cost</th>
                    <th>Total Emb/Print Cost</th>
                </thead>
            </table>
            <div style="width:3050px; max-height:400px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="3030" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
                <?
                $i=1; $total_order_qnty=0; $total_order_qnty_in_pcs=0; $grand_tot_cm_cost=0; $grand_tot_cost=0; $tot_order_value=0; $tot_margin=0; $grand_tot_trims_cost=0; $grand_tot_embell_cost=0; $tot_knit_charge=0; $tot_yarn_dye_charge=0; $tot_dye_finish_charge=0; $yarn_desc_array=array(); $fabriccostArray=array(); $trims_cons_cost_array=array();
				$prodcostArray=array(); $fabricArray=array(); $yarncostArray=array();
                
				$fabricDataArray=sql_select("select a.job_no, a.fab_nature_id, a.fabric_description, a.fabric_source, a.rate, a.avg_cons as avg_finish_cons, b.yarn_amount, b.conv_amount from wo_pre_cost_fabric_cost_dtls a, wo_pre_cost_sum_dtls b where a.job_no=b.job_no and a.fabric_source!=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0"); //UG-19-00084 wo_pre_cos_fab_co_avg_con_dtls
				foreach($fabricDataArray as $fabricRow)
				{
					$fabricArray[$fabricRow[csf('job_no')]].=$fabricRow[csf('fab_nature_id')]."**".$fabricRow[csf('fabric_description')]."**".$fabricRow[csf('fabric_source')]."**".$fabricRow[csf('rate')]."**".$fabricRow[csf('avg_finish_cons')]."**".$fabricRow[csf('yarn_amount')]."**".$fabricRow[csf('conv_amount')].",";
				}
				
				$yarncostDataArray=sql_select("select job_no, count_id, type_id, sum(cons_qnty) as qnty, sum(avg_cons_qnty) as cons_qnty, sum(amount) as amnt, sum(rate*avg_cons_qnty) as amount from wo_pre_cost_fab_yarn_cost_dtls where status_active=1 and is_deleted=0 group by job_no, count_id, type_id");
				foreach($yarncostDataArray as $yarnRow)
				{
				   $yarncostArray[$yarnRow[csf('job_no')]].=$yarnRow[csf('count_id')]."**".$yarnRow[csf('type_id')]."**".$yarnRow[csf('cons_qnty')]."**".$yarnRow[csf('amount')].",";
				}
				
				$fabriccostDataArray=sql_select("select job_no, costing_per_id, trims_cost, embel_cost, cm_cost, commission, common_oh, lab_test, inspection, freight, comm_cost from wo_pre_cost_dtls where status_active=1 and is_deleted=0");
				foreach($fabriccostDataArray as $fabRow)
				{
					 $fabriccostArray[$fabRow[csf('job_no')]]['costing_per_id']=$fabRow[csf('costing_per_id')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['trims_cost']=$fabRow[csf('trims_cost')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['embel_cost']=$fabRow[csf('embel_cost')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['cm_cost']=$fabRow[csf('cm_cost')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['commission']=$fabRow[csf('commission')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['common_oh']=$fabRow[csf('common_oh')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['lab_test']=$fabRow[csf('lab_test')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['inspection']=$fabRow[csf('inspection')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['freight']=$fabRow[csf('freight')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['comm_cost']=$fabRow[csf('comm_cost')];
				}
				
				//$trimscostDataArray=sql_select("select a.job_no, b.po_break_down_id, sum(b.cons*a.rate) as total,sum(b.amount) as amount_dzn from wo_pre_cost_trim_cost_dtls a, wo_pre_cost_trim_co_cons_dtls b where a.id=b.wo_pre_cost_trim_cost_dtls_id and a.status_active=1 and a.is_deleted=0 by a.job_no,a.trim_group");
				$trimscostDataArray=sql_select("select a.job_no,sum(a.amount) as amount_dzn from wo_pre_cost_trim_cost_dtls a where  a.status_active=1 and a.is_deleted=0 group by a.job_no");
				//echo "select a.job_no,sum(a.amount) as amount_dzn from wo_pre_cost_trim_cost_dtls a where  a.status_active=1 and a.is_deleted=0 group by a.job_no";
				foreach($trimscostDataArray as $trimsRow)
				{
					 //$trims_cons_cost_array[$trimsRow[csf('job_no')]][$trimsRow[csf('po_break_down_id')]]=$trimsRow[csf('total')];
					 $trims_po_cost_array[$trimsRow[csf('job_no')]]=$trimsRow[csf('amount_dzn')];
				}
				 
				$prodcostDataArray=sql_select("select job_no, sum(CASE WHEN cons_process=1 THEN amount END) AS knit_charge, sum(CASE WHEN cons_process=30 THEN amount END) AS yarn_dye_charge, sum(CASE WHEN cons_process not in(1,2,30) THEN amount END) AS dye_finish_charge from wo_pre_cost_fab_conv_cost_dtls where status_active=1 and is_deleted=0 group by job_no");
				foreach($prodcostDataArray as $prodRow)
				{
					$prodcostArray[$prodRow[csf('job_no')]]['knit_charge']=$prodRow[csf('knit_charge')];
					$prodcostArray[$prodRow[csf('job_no')]]['yarn_dye_charge']=$prodRow[csf('yarn_dye_charge')];
					$prodcostArray[$prodRow[csf('job_no')]]['dye_finish_charge']=$prodRow[csf('dye_finish_charge')];
				}					  
				 
				if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
				else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
				else $year_field="";//defined Later
				
              	$sql="select a.job_no_prefix_num, a.job_no, $year_field, a.company_name, a.buyer_name, a.team_leader, a.dealing_marchant, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, b.id, b.po_number, 
				b.grouping,b.file_no,b.pub_shipment_date, b.po_quantity, b.unit_price, b.po_total_price, b.plan_cut from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $jobcond $yearCond $date_cond $buyer_id_cond $team_name_cond $team_member_cond $ref_cond $file_no_cond order 
				by b.pub_shipment_date, b.id";// b.id, b.pub_shipment_date
			
                $nameArray=sql_select($sql);
                $tot_rows=count($nameArray);
                foreach($nameArray as $row )
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					$costing_date=$costing_library[$row[csf('job_no')]];
                ?>
                    <tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="70" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                        <td width="50" align="center"><? echo $row[csf('year')]; ?></td>
                        <td width="70"><? echo $buyer_short_name_library[$row[csf('buyer_name')]]; ?></td>
                        <td width="70" align="center"><? echo $row[csf('file_no')]; ?></td>
                        <td width="80" align="center"><? echo $row[csf('grouping')]; ?></td>
                        <td width="100"><p><? echo $team_library[$row[csf('team_leader')]]; ?></p></td>
                        <td width="110"><p><? echo $dealing_merchant_array[$row[csf('dealing_marchant')]]; ?></p></td>
                        <td width="110"><p><a href="##" onclick="generate_pre_cost_report('<? echo $row[csf('id')]; ?>','<? echo $row[csf('job_no')]; ?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','<? echo $costing_date; ?>')"><? echo $row[csf('po_number')]; ?></a></p></td>
                        <td width="110"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                        <td width="110">
                            <p>
                                <?
                                    $gmts_item='';
                                    $gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
                                    foreach($gmts_item_id as $item_id)
                                    {
                                        if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
                                    }
                                    echo $gmts_item;
                                ?>
                            </p>
                        </td>
                        <td width="90" align="right" >
                            <? 
                                echo fn_number_format($row[csf('po_quantity')],0,'.',''); 
                                $total_order_qnty+=$row[csf('po_quantity')];
                            ?>
                        </td>
                        <td width="50" align="center"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
                        <td width="90" align="right">
                        <? 
                            $order_qnty_in_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
							$plan_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];
                            echo fn_number_format($order_qnty_in_pcs,0,'.',''); 
                            $total_order_qnty_in_pcs+=$order_qnty_in_pcs;
                        ?>
                        </td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
                        <?
                        $fabric_desc=''; $fabric_cost_per_dzn=0; $knit_fabric_rate=0; $knit_fabric_amnt=0; $yarn_cost=0;$conversion_cost=0;
						$knit_fabric_purc_amnt=0; $woven_fabric_cons=0; $woven_fabric_rate=0; $woven_fabric_amnt=0; $other_cost=0;
                        $tot_cost_per_dzn=0; $tot_cm_cost=0; $tot_cost=0; $tot_trims_cost=0; $tot_embell_cost=0; $cost_per_unit=0; $margin=0;

						$fabricData=explode(",",substr($fabricArray[$row[csf('job_no')]],0,-1));
                        foreach($fabricData as $fabricRow)
                        {
							$knit_fabric_cons=0; 
							$fabricRow=explode("**",$fabricRow);
							$fab_nature_id=$fabricRow[0];
							$fabric_description=$fabricRow[1];
							$fabric_source=$fabricRow[2];
							$rate=$fabricRow[3];
							$avg_finish_cons=$fabricRow[4];
						//	echo $avg_finish_cons.'=='.$rate;
							$yarn_amount=$fabricRow[5];
							$conv_amount=$fabricRow[6];
							
                            if($fabric_desc=="") $fabric_desc=$fabric_description; else $fabric_desc.=",".$fabric_description;
                            if($fab_nature_id==2)
                            {
                                $knit_fabric_cons+=$avg_finish_cons;
                                if($fabric_source==2)
                                {
                                    $knit_fabric_purc_amnt+=$avg_finish_cons*$rate;	
                                }
                            }
                            else if($fab_nature_id==3)
                            {
								$woven_fabric_cons+=$avg_finish_cons;
                                if($fabric_source==2)
                                { 
                                    $woven_fabric_amnt+=$avg_finish_cons*$rate;
                                }
                            }
                            
                            $yarn_cost=$yarn_amount;
                            $conversion_cost=$conv_amount;
                        }
						
                        $knit_fabric_amnt=$knit_fabric_purc_amnt+$yarn_cost+$conversion_cost;
                        $knit_fabric_rate=$knit_fabric_amnt/$knit_fabric_cons;
                        $woven_fabric_rate=$woven_fabric_amnt/$woven_fabric_cons;
                        $fabric_cost_per_dzn=$knit_fabric_amnt+$woven_fabric_amnt;

					 	$dzn_qnty=0;
						$costing_per_id=$fabriccostArray[$row[csf('job_no')]]['costing_per_id'];
                        if($costing_per_id==1)
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
						
						$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
						
						
						if($costing_per_id==2) //Pcs
						{
							//$fabric_cost_per_dzn=($fabric_cost_per_dzn*12)*$row[csf('ratio')];
							//$fabric_cost_per_dzn=($fabric_cost_per_dzn/12)*$row[csf('ratio')];
							$cost_per_qnty='Pcs';
						}
						else
						{
							$cost_per_qnty='Dzn';
						}
						//else $fabric_cost_per_dzn=$fabric_cost_per_dzn;
						//echo $dzn_qnty.'='.$fabric_cost_per_dzn.'='.$costing_per_id.',';
						
						$other_cost=$fabriccostArray[$row[csf('job_no')]]['common_oh']+$fabriccostArray[$row[csf('job_no')]]['lab_test']+$fabriccostArray[$row[csf('job_no')]]['inspection']+$fabriccostArray[$row[csf('job_no')]]['freight']+$fabriccostArray[$row[csf('job_no')]]['comm_cost'];
                        
						$trims_cons_cost=$trims_po_cost_array[$row[csf('job_no')]];//$trims_cons_cost_array[$row[csf('job_no')]][$row[csf('id')]];
						//$trims_dzn_cost=$trims_po_cost_array[$row[csf('id')]];

                        $tot_cost_per_dzn=$fabric_cost_per_dzn+$trims_cons_cost+$fabriccostArray[$row[csf('job_no')]]['cm_cost']+$fabriccostArray[$row[csf('job_no')]]['commission']+$fabriccostArray[$row[csf('job_no')]]['embel_cost']+$other_cost;
                        $cost_per_unit=$tot_cost_per_dzn/$dzn_qnty;
                        
                        $tot_cm_cost=($order_qnty_in_pcs/$dzn_qnty)*$fabriccostArray[$row[csf('job_no')]]['cm_cost'];
                        $tot_cost=($order_qnty_in_pcs/$dzn_qnty)*$tot_cost_per_dzn;
                        $tot_trims_cost=($order_qnty_in_pcs/$dzn_qnty)*$trims_cons_cost;
                        $tot_embell_cost=($order_qnty_in_pcs/$dzn_qnty)*$fabriccostArray[$row[csf('job_no')]]['embel_cost'];
                        $margin=$row[csf('po_total_price')]-$tot_cost;
						
						$yarnData=explode(",",substr($yarncostArray[$row[csf('job_no')]],0,-1));
						foreach($yarnData as $yarnRow)
						{
							$yarnRow=explode("**",$yarnRow);
							$count_id=$yarnRow[0];
							$type_id=$yarnRow[1];
							$cons_qnty=$yarnRow[2];
							$amount=$yarnRow[3];
													
							$yarn_desc=$yarn_count_library[$count_id]."**".$yarn_type[$type_id];
							$req_qnty=($plan_cut_qnty/$dzn_qnty)*$cons_qnty;
							$req_amnt=($plan_cut_qnty/$dzn_qnty)*$amount;
							 
							$yarn_desc_array[$yarn_desc]['qnty']+=$req_qnty;
							$yarn_desc_array[$yarn_desc]['amnt']+=$req_amnt;
						}
						 
						$tot_knit_charge+=($order_qnty_in_pcs/$dzn_qnty)*$prodcostArray[$row[csf('job_no')]]['knit_charge'];
						$tot_yarn_dye_charge+=($order_qnty_in_pcs/$dzn_qnty)*$prodcostArray[$row[csf('job_no')]]['yarn_dye_charge']; 
						$tot_dye_finish_charge+=($order_qnty_in_pcs/$dzn_qnty)*$prodcostArray[$row[csf('job_no')]]['dye_finish_charge'];			  
                        ?>
                        <td width="220">
                            <p>
                                <? $fabric_desc=explode(",",$fabric_desc); echo join(",<br>",array_unique($fabric_desc)); ?>
                            </p>
                        </td>
                        <td width="70" align="right"><? echo fn_number_format($knit_fabric_cons,2,'.',''); ?></td>
                        <td width="60" align="right"><? echo fn_number_format($knit_fabric_rate,2,'.',''); ?></td>
                        <td width="70" align="right"><? echo fn_number_format($woven_fabric_cons,2,'.',''); ?></td>
                        <td width="65" align="right"><? echo fn_number_format($woven_fabric_rate,2,'.',''); ?></td>
                        <?
							if($fabric_cost_per_dzn>0) $td_color=""; else $td_color="#FF0000";
						?>
                        <td width="80" align="right" title="<? echo $costing_per[$costing_per_id];?>" bgcolor="<? echo $td_color; ?>">
                        <? 
                            if($fabric_cost_per_dzn) echo fn_number_format($fabric_cost_per_dzn,2,'.','').' '.$cost_per_qnty;else echo '0'; 
                            $fabric_cost_summary+=($order_qnty_in_pcs/$dzn_qnty)*$fabric_cost_per_dzn;
                        ?>
                        </td>
                        <?
						 	if($trims_cons_cost>0) $trims_td_color=""; else $trims_td_color="#FF0000";
							
							$po_id=$row[csf('id')]; $po_qnty=$order_qnty_in_pcs; $po_no=$row[csf('po_number')];  $job_no=$row[csf('job_no')];
						?>
                        <td width="80" align="right" bgcolor="<? echo $trims_td_color; ?>"><? echo "<a href='#report_details' onclick= \"openmypage($po_id,'$po_qnty','$po_no','$job_no','trims_cost','Trims Cost Info');\">".fn_number_format($trims_cons_cost,2,'.','').' '.$cost_per_qnty."</a>"; ?></td>
                        <td width="80" align="right"><?  if($fabriccostArray[$row[csf('job_no')]]['embel_cost']) echo fn_number_format($fabriccostArray[$row[csf('job_no')]]['embel_cost'],2,'.','').' '.$cost_per_qnty;else echo '0'; ?></td>
                        <?
							if($fabriccostArray[$row[csf('job_no')]]['cm_cost']>0) $cm_td_color=""; else $cm_td_color="#FF0000";
						?>
                        <td width="80" align="right" bgcolor="<? echo $cm_td_color; ?>"><? if($fabriccostArray[$row[csf('job_no')]]['cm_cost']) echo fn_number_format($fabriccostArray[$row[csf('job_no')]]['cm_cost'],2,'.','').' '.$cost_per_qnty;else echo '0'; ?></td>
                        <td width="85" align="right">
							<? 
                                echo fn_number_format($fabriccostArray[$row[csf('job_no')]]['commission'],2,'.',''); 
                                $comm_cost_summary+=($order_qnty_in_pcs/$dzn_qnty)*$fabriccostArray[$row[csf('job_no')]]['commission'];
                            ?>
                        </td>
                        <td width="80" align="right">
							<? 
                                echo "<a href='#report_details' onclick= \"openmypage($po_id,'$po_qnty','$po_no','$job_no','other_cost','Other Cost Info');\">".fn_number_format($other_cost,2,'.','')."</a>";
                                $other_cost_summary+=($order_qnty_in_pcs/$dzn_qnty)*$other_cost;
                            ?>
                        </td>
                        <td width="80" align="right"><? echo fn_number_format($tot_cost_per_dzn,2,'.',''); ?></td>
                        <td width="100" align="right">
                            <? 
                                echo fn_number_format($tot_cm_cost,2,'.',''); 
                                $grand_tot_cm_cost+=$tot_cm_cost;
                            ?>
                        </td>
                        <td width="100" align="right">
                            <? 
                                echo fn_number_format($tot_cost,2,'.','');
                                $grand_tot_cost+=$tot_cost; 
                            ?>
                        </td>
                        <td width="65" align="right"><? echo fn_number_format($cost_per_unit,2,'.',''); ?></td>
                        <td width="65" align="right"><? echo fn_number_format($row[csf('unit_price')],2); ?></td>
                        <td width="100" align="right">
                            <? 
                                echo fn_number_format($row[csf('po_total_price')],2,'.',''); 
                                $tot_order_value+=$row[csf('po_total_price')];
                            ?>
                        </td>
                        <td width="100" align="right">
                            <? 
                                echo fn_number_format($margin,2,'.','');
                                $tot_margin+=$margin; 
                            ?>
                        </td>
                        <td width="100" align="right">
                            <? 
                                echo fn_number_format($tot_trims_cost,2,'.',''); 
                                $grand_tot_trims_cost+=$tot_trims_cost;
                            ?>
                        </td>
                        <td align="right">
                            <? 
                                echo fn_number_format($tot_embell_cost,2,'.','');
                                $grand_tot_embell_cost+=$tot_embell_cost; 
                            ?>
                        </td>
                    </tr>
                <?
                $i++;
                }
                ?>
                </table>
                <table class="rpt_table" width="3030" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <tfoot>
                        <th width="40"></th>
                        <th width="70"></th>
                        <th width="50"></th>
                        <th width="70"></th>
                        <th width="70"></th>
                        <th width="80"></th>
                        <th width="100"></th>
                        <th width="110"></th>
                        <th width="110"></th>
                        <th width="110"></th>
                        <th width="110" align="right">Total</th>
                        <th width="90" align="right" id="total_order_qnty"><? echo fn_number_format($total_order_qnty,0); ?></th>
                        <th width="50"></th>
                        <th width="90" align="right" id="total_order_qnty_in_pcs"><? echo fn_number_format($total_order_qnty_in_pcs,0); ?></th>
                        <th width="80"></th>
                        <th width="220"></th>
                        <th width="70"></th>
                        <th width="60"></th>
                        <th width="70"></th>
                        <th width="65"></th>
                        <th width="80"></th>
                        <th width="80"></th>
                        <th width="80"></th>
                        <th width="80"></th>
                        <th width="85"></th>
                        <th width="80"></th>
                        <th width="80"></th>
                        <th width="100" align="right" id="value_tot_cm_cost"><? echo fn_number_format($grand_tot_cm_cost,2); ?></th>
                        <th width="100" align="right" id="value_tot_cost"><? echo fn_number_format($grand_tot_cost,2); ?></th>
                        <th width="65"></th>
                        <th width="65"></th>
                        <th width="100" align="right" id="value_order"><? echo fn_number_format($tot_order_value,2); ?></th>
                        <th width="100" align="right" id="value_margin"><? echo fn_number_format($tot_margin,2); ?></th>
                        <th width="100" align="right" id="value_tot_trims_cost"><? echo fn_number_format($grand_tot_trims_cost,2); ?></th>
                        <th align="right" id="value_tot_embell_cost"><? echo fn_number_format($grand_tot_embell_cost,2); ?></th>
                    </tfoot>
                </table>
            </div>
            <table>
                <tr><td height="15"></td></tr>
            </table>
            <table style="margin-left:20px" width="1500">
                <tr>
                    <td width="400" valign="top"><b><u>Cost Summary</u></b>
                        <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
                            <thead>
                                <th width="140">Particulars</th>
                                <th width="160">Amount</th>
                                <th>Percentage</th>
                            </thead>
                            <tr bgcolor="#E9F3FF">
                                <td>Fabric Cost</td>
                                <td align="right"><? echo fn_number_format($fabric_cost_summary,2); ?>
                                </td>
                                <td align="right"><? echo fn_number_format((($fabric_cost_summary*100)/$tot_order_value),2); ?></td>
                            </tr>
                            <tr bgcolor="#FFFFFF">
                                <td>Trims Cost</td>
                                <td align="right"><? echo fn_number_format($grand_tot_trims_cost,2); ?></td>
                               <td align="right"><? echo fn_number_format((($grand_tot_trims_cost*100)/$tot_order_value),2); ?></td>
                            </tr>
                            <tr bgcolor="#E9F3FF">
                                <td>Embellish Cost</td>
                                <td align="right"><? echo fn_number_format($grand_tot_embell_cost,2); ?></td>
                                <td align="right"><? echo fn_number_format((($grand_tot_embell_cost*100)/$tot_order_value),2); ?></td>
                            </tr>
                            <tr bgcolor="#FFFFFF">
                                <td>Commision Cost</td>
                                <td align="right"><? echo fn_number_format($comm_cost_summary,2); ?></td>
                                <td align="right"><? echo fn_number_format((($comm_cost_summary*100)/$tot_order_value),2); ?></td>
                            </tr>
                            <tr bgcolor="#E9F3FF">
                                <td>Other Cost</td>
                                <td align="right"><? echo fn_number_format($other_cost_summary,2); ?></td>
                                <td align="right"><? echo fn_number_format((($other_cost_summary*100)/$tot_order_value),2); ?></td>
                            </tr>
                            <tr bgcolor="#FFFFFF">
                                <td>Total Cost</td>
                                <td align="right"><? $total_cost_summ=$grand_tot_cost-$grand_tot_cm_cost; echo fn_number_format($total_cost_summ,2); ?></td>
                                <td align="right"><? echo fn_number_format((($total_cost_summ*100)/$tot_order_value),2); ?></td>
                            </tr>
                            <tr bgcolor="#E9F3FF">
                                <td>Total Order Value</td>
                                <td align="right"><? echo fn_number_format($tot_order_value,2); ?></td>
                                <td align="right"><? echo fn_number_format((($tot_order_value*100)/$tot_order_value),2); ?></td>
                            </tr>
                            <tr bgcolor="#FFFFFF">
                                <td>CM Value</td>
                                <td align="right">
                                    <? 
                                        $cm_value=$tot_order_value-$total_cost_summ;
                                        echo fn_number_format($cm_value,2); 
                                    ?>
                                </td>
                                <td align="right"><? echo fn_number_format((($cm_value*100)/$tot_order_value),2); ?></td>
                            </tr>
                            <tr bgcolor="#E9F3FF">
                                <td>CM Cost</td>
                                <td align="right"><? echo fn_number_format($grand_tot_cm_cost,2); ?></td>
                                <td align="right"><? echo fn_number_format((($grand_tot_cm_cost*100)/$tot_order_value),2); ?></td>
                            </tr>
                            <tr bgcolor="#FFFFFF">
                                <td>Margin</td>
                                <td align="right">
                                    <? 
                                        $margin_value=$cm_value-$grand_tot_cm_cost;
                                        echo fn_number_format($margin_value,2); 
                                    ?>
                                </td>
                                <td align="right"><? echo fn_number_format((($margin_value*100)/$tot_order_value),2); ?></td>
                            </tr>
                        </table>
                    </td>
                    <td width="50"></td>
                    <td width="570" valign="top"><b><u>Yarn Summary</u></b>
                    	<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
                            <thead>
                            	<th width="30">SL</th>
                                <th width="80">Yarn Count</th>
                                <th width="120">Type</th>
                                <th width="120">Req. Qnty</th>
                                <th width="80">Avg. rate</th>
                                <th>Amount</th>
                            </thead>
                            <?
							$s=1; $tot_yarn_req_qnty=0; $tot_yarn_req_amnt=0;
							
							foreach($yarn_desc_array as $key=>$value)
							{
								
								if($s%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$yarn_desc=explode("**",$key);
								
								if($yarn_desc[0]!="")
								{
									$tot_yarn_req_qnty+=$yarn_desc_array[$key]['qnty']; 
									$tot_yarn_req_amnt+=$yarn_desc_array[$key]['amnt'];
								?>
									<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr3_<? echo $s; ?>','<? echo $bgcolor; ?>')" id="tr3_<? echo $s;?>">
										<td><? echo $s; ?></td>
										<td align="center"><? echo $yarn_desc[0]; ?></td>
										<td><? echo $yarn_desc[1]; ?></td>
										<td align="right"><? echo fn_number_format($yarn_desc_array[$key]['qnty'],2); ?></td>
										<td align="right"><? echo fn_number_format($yarn_desc_array[$key]['amnt']/$yarn_desc_array[$key]['qnty'],2); ?></td>
										<td align="right"><? echo fn_number_format($yarn_desc_array[$key]['amnt'],2); ?></td>
									</tr>
								<?	
								$s++;
								}
							}
							?>
                            <tfoot>
                            	<th colspan="3" align="right">Total</th>
                                <th align="right"><? echo fn_number_format($tot_yarn_req_qnty,2); ?></th>
                                <th align="right"><? echo fn_number_format($tot_yarn_req_amnt/$tot_yarn_req_qnty,2); ?></th>
                                <th align="right"><? echo fn_number_format($tot_yarn_req_amnt,2); ?></th>
                            </tfoot>
                    	</table>  
                    </td>
                    <td width="50"></td>
                    <td width="450" valign="top"><b><u>Fabric Production Charge</u></b>
                    	<?
							$tot_prod_charge=$tot_knit_charge+$tot_yarn_dye_charge+$tot_dye_finish_charge;	  
						?>
                    	<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
                            <thead>
                           		<th width="30">SL</th>
                                <th width="160">Particulars</th>
                                <th width="140">Amount</th>
                                <th>Percentage</th>
                            </thead>
                            <tr bgcolor="#E9F3FF">
                            	<td>1</td>
                                <td>Knitting Charge</td>
                                <td align="right"><? echo fn_number_format($tot_knit_charge,2); ?></td>
                                <td align="right"><? echo fn_number_format((($tot_knit_charge*100)/$tot_prod_charge),2); ?></td>
                            </tr>
                            <tr bgcolor="#FFFFFF">
                            	<td>2</td>
                                <td>Yarn Dyeing Charge</td>
                                <td align="right"><? echo fn_number_format($tot_yarn_dye_charge,2); ?></td>
                               <td align="right"><? echo fn_number_format((($tot_yarn_dye_charge*100)/$tot_prod_charge),2); ?></td>
                            </tr>
                            <tr bgcolor="#E9F3FF">
                            	<td>3</td>
                                <td>Dyeing & Finishing Charge</td>
                                <td align="right"><? echo fn_number_format($tot_dye_finish_charge,2); ?></td>
                                <td align="right"><? echo fn_number_format((($tot_dye_finish_charge*100)/$tot_prod_charge),2); ?></td>
                            </tr>
                            <tfoot>
                            	<th colspan="2" align="right">Total</th>
                                <th align="right"><? echo fn_number_format($tot_prod_charge,2); ?></th>
                                <th align="right"><? echo fn_number_format((($tot_prod_charge*100)/$tot_prod_charge),2); ?></th>
                            </tfoot>
                    	</table>        
                    </td>
                </tr>
            </table>
            <br />
            <table>
                <tr>
                	<?
					$tot_order_value=fn_number_format($tot_order_value,2,'.','');
					$fabric_cost_summary=fn_number_format($fabric_cost_summary,2,'.','');
					$grand_tot_trims_cost=fn_number_format($grand_tot_trims_cost,2,'.','');
					$grand_tot_embell_cost=fn_number_format($grand_tot_embell_cost,2,'.','');
					$comm_cost_summary=fn_number_format($comm_cost_summary,2,'.','');
					$other_cost_summary=fn_number_format($other_cost_summary,2,'.','');
					$grand_tot_cm_cost=fn_number_format($grand_tot_cm_cost,2,'.','');
					$margin_value=fn_number_format($margin_value,2,'.','');

					$chart_data_qnty="Order Value;".$tot_order_value."\nFabric Cost;".$fabric_cost_summary."\nTrims Cost;".$grand_tot_trims_cost."\nEmbellishment Cost;".$grand_tot_embell_cost."\nCommission Cost;".$comm_cost_summary."\nOthers Cost;".$other_cost_summary."\nCM Cost;".$grand_tot_cm_cost."\nMargin;".$margin_value."\n";
					 
					?>
                    <input type="hidden" id="graph_data" value="<? echo substr($chart_data_qnty,0,-1); ?>"/>
                    <td colspan="5" id="chartdiv"></td>
                </tr>
            </table>
        </fieldset>
        </div>
       <?
	}

	//echo "$total_data****requires/$filename****$tot_rows";
	$html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
        @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
	//print_r($filename);exit;
    echo "$html****$filename****1****$type";
	exit();	
}

if($action=="trims_cost")
{
	echo load_html_head_contents("Report Info","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
?>
    <div>
        <fieldset style="width:600px;">
        <div style="width:600px" align="center">	
            <table class="rpt_table" width="470" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <th width="120">Job No</th>
                    <th width="200">Order No</th>
                    <th>Order Qnty</th>
                </thead>
                <tr bgcolor="#FFFFFF">
                	<td align="center"><? echo $job_no; ?></td>
                    <td><? echo $po_no; ?></td>
                    <td align="right"><? echo fn_number_format($po_qnty,0); ?></td>
                </tr>
            </table>
            <table style="margin-top:10px" class="rpt_table" width="600" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                	<th width="40">SL</th>
                    <th width="130">Item Name</th>
                    <th width="90">Cons/Dzn</th>
                    <th width="80">Rate</th>
                    <th width="110">Trims Cost/Dzn</th>
                    <th>Total Trims Cost</th>
                </thead>
            </table>
            </div>
            <div style="width:620px; max-height:250px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="600" cellpadding="0" cellspacing="0" border="1" rules="all">
                	<?
					$costing_per=return_field_value("costing_per","wo_pre_cost_mst","job_no='$job_no' and status_active=1 and is_deleted=0");
                        
					$dzn_qnty=0;
					if($costing_per==1)
					{
						$dzn_qnty=12;
					}
					else if($costing_per==3)
					{
						$dzn_qnty=12*2;
					}
					else if($costing_per==4)
					{
						$dzn_qnty=12*3;
					}
					else if($costing_per==5)
					{
						$dzn_qnty=12*4;
					}
					else
					{
						$dzn_qnty=1;
					}
					//and b.po_break_down_id='$po_id' 
					$sql="select a.trim_group, a.amount,a.rate, a.cons_dzn_gmts as cons from wo_pre_cost_trim_cost_dtls a where   a.job_no='$job_no' and a.status_active=1 and a.is_deleted=0";
					$trimsArray=sql_select($sql);
					$i=1;
					foreach($trimsArray as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td width="40"><? echo $i; ?></td>
							<td width="130"><div style="width:130px; word-wrap:break-word"><? echo $item_library[$row[csf('trim_group')]]; ?></div></td>
							<td width="90" align="right"><? echo fn_number_format($row[csf('cons')],2); ?></td>
							<td width="80" align="right"><? echo fn_number_format($row[csf('rate')],2); ?></td>
							<td width="110" align="right">
								<?
                                    $trims_cost_per_dzn=$row[csf('cons')]*$row[csf('rate')]; 
                                    echo fn_number_format($trims_cost_per_dzn,2);
									$tot_trims_cost_per_dzn+=$trims_cost_per_dzn; 
                                ?>
                            </td>
							<td align="right">
								<?
                                	$trims_cost=($po_qnty/$dzn_qnty)*$trims_cost_per_dzn;
									echo fn_number_format($trims_cost,2);
									$tot_trims_cost+=$trims_cost;
                                ?>
                            </td>
						</tr>
					<?
					$i++;
					}
					?>
                	<tfoot>
                        <th colspan="4">Total</th>
                        <th><? echo fn_number_format($tot_trims_cost_per_dzn,2); ?></th>
                        <th><? echo fn_number_format($tot_trims_cost,2); ?></th>
                    </tfoot>    
                </table>
            </div>
        </fieldset>
    </div>
<?
}

if($action=="other_cost")
{
	echo load_html_head_contents("Report Info","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	?>
    <div align="center">
        <fieldset style="width:600px;">
            <table class="rpt_table" width="470" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <th width="120">Job No</th>
                    <th width="200">Order No</th>
                    <th>Order Qnty</th>
                </thead>
                <tr bgcolor="#FFFFFF">
                	<td align="center"><? echo $job_no; ?></td>
                    <td><? echo $po_no; ?></td>
                    <td align="right"><? echo fn_number_format($po_qnty,0); ?></td>
                </tr>
            </table>
            <table style="margin-top:10px" class="rpt_table" width="470" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                    <th width="200">Particulars</th>
                    <th width="90">Cost/Dzn</th>
                    <th>Total Cost</th>
                </thead>
				<?
                $costing_per=return_field_value("costing_per","wo_pre_cost_mst","job_no='$job_no' and status_active=1 and is_deleted=0");
                    
                $dzn_qnty=0;
                if($costing_per==1)
                {
                    $dzn_qnty=12;
                }
                else if($costing_per==3)
                {
                    $dzn_qnty=12*2;
                }
                else if($costing_per==4)
                {
                    $dzn_qnty=12*3;
                }
                else if($costing_per==5)
                {
                    $dzn_qnty=12*4;
                }
                else
                {
                    $dzn_qnty=1;
                }
                    
                $sql="select common_oh, lab_test, inspection, freight, comm_cost from wo_pre_cost_dtls where job_no='$job_no' and status_active=1 and is_deleted=0";
                $fabriccostArray=sql_select($sql);
                ?>
                <tr bgcolor="#E9F3FF">
                    <td>Commercial Cost</td>
                    <td align="right"><? echo fn_number_format($fabriccostArray[0][csf('comm_cost')],2); ?></td>
                    <td align="right">
                        <?
                            $comm_cost=($po_qnty/$dzn_qnty)*$fabriccostArray[0][csf('comm_cost')]; 
                            echo fn_number_format($comm_cost,2);
                        ?>
                    </td>
                </tr>
                <tr bgcolor="#FFFFFF">
                    <td>Lab Test Cost</td>
                    <td align="right"><? echo fn_number_format($fabriccostArray[0][csf('lab_test')],2); ?></td>
                    <td align="right">
                        <?
                            $lab_cost=($po_qnty/$dzn_qnty)*$fabriccostArray[0][csf('lab_test')]; 
                            echo fn_number_format($lab_cost,2);
                        ?>
                    </td>
                </tr>
                 <tr bgcolor="#E9F3FF">
                    <td>Inspection Cost</td>
                    <td align="right"><? echo fn_number_format($fabriccostArray[0][csf('inspection')],2); ?></td>
                    <td align="right">
                        <?
                            $inspection_cost=($po_qnty/$dzn_qnty)*$fabriccostArray[0][csf('inspection')]; 
                            echo fn_number_format($inspection_cost,2);
                        ?>
                    </td>
                </tr>
                <tr bgcolor="#FFFFFF">
                    <td>Freight Cost</td>
                    <td align="right"><? echo fn_number_format($fabriccostArray[0][csf('freight')],2); ?></td>
                    <td align="right">
                        <?
                            $freight_cost=($po_qnty/$dzn_qnty)*$fabriccostArray[0][csf('freight')]; 
                            echo fn_number_format($freight_cost,2);
                        ?>
                    </td>
                </tr>
                <tr bgcolor="#E9F3FF">
                    <td>Common OH Cost</td>
                    <td align="right"><? echo fn_number_format($fabriccostArray[0][csf('common_oh')],2); ?></td>
                    <td align="right">
                        <?
                            $common_oh_cost=($po_qnty/$dzn_qnty)*$fabriccostArray[0][csf('common_oh')]; 
                            echo fn_number_format($common_oh_cost,2);
							
							$tot_cost_per_dzn=$fabriccostArray[0][csf('comm_cost')]+$fabriccostArray[0][csf('lab_test')]+$fabriccostArray[0][csf('inspection')]+$fabriccostArray[0][csf('freight')]+$fabriccostArray[0][csf('common_oh')];
							$tot_cost=$comm_cost+$lab_cost+$inspection_cost+$freight_cost+$common_oh_cost;
                        ?>
                    </td>
                </tr>
                <tfoot>
                    <th>Total</th>
                    <th><? echo fn_number_format($tot_cost_per_dzn,2); ?></th>
                    <th><? echo fn_number_format($tot_cost,2); ?></th>
                </tfoot>    
            </table>
        </fieldset>
    </div>
	<?
}

if($action=="export_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents($title, "../../../../", 1, 1,$unicode,'','');

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");

	$sub_sql=sql_select("SELECT b.invoice_id, a.courier_date, a.submit_date, a.bnk_to_bnk_cour_no, a.bank_ref_no, a.possible_reali_date 
	from com_export_doc_submission_mst a,com_export_doc_submission_invo b 
	where a.id=b.doc_submission_mst_id and a.company_id=$company_id and a.entry_form=40 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	$bank_sub_data=array();

	foreach($sub_sql as $row)
	{
		$bank_sub_data[$row[csf("invoice_id")]]["courier_date"]=$row[csf("courier_date")];
		$bank_sub_data[$row[csf("invoice_id")]]["submit_date"]=$row[csf("submit_date")];
		$bank_sub_data[$row[csf("invoice_id")]]["bnk_to_bnk_cour_no"]=$row[csf("bnk_to_bnk_cour_no")];
		$bank_sub_data[$row[csf("invoice_id")]]["bank_ref_no"]=$row[csf("bank_ref_no")];
		$bank_sub_data[$row[csf("invoice_id")]]["possible_reali_date"]=$row[csf("possible_reali_date")];
	}

	$buyer_submit_date_arr=return_library_array("SELECT b.invoice_id,a.submit_date from com_export_doc_submission_mst a,com_export_doc_submission_invo b where a.id=b.doc_submission_mst_id and a.company_id=$company_id and a.entry_form=39","invoice_id","submit_date");

	$rlz_date_arr=return_library_array(" SELECT a.invoice_id,b.received_date,b.is_invoice_bill
	from com_export_doc_submission_invo a, com_export_proceed_realization b
	where a.doc_submission_mst_id=b.invoice_bill_id and b.is_invoice_bill=1 and b.benificiary_id=$company_id
	union all
	select b.invoice_bill_id as invoice_id, b.received_date , b.is_invoice_bill
	from  com_export_proceed_realization b
	where  b.is_invoice_bill  = 2
	order by invoice_id","invoice_id","received_date");	

	$rlz_date_res = sql_select("SELECT  a.invoice_id,b.received_date,b.is_invoice_bill ,c.type,   sum( c.document_currency) as document_currency
	from com_export_doc_submission_invo a, com_export_proceed_realization b , com_export_proceed_rlzn_dtls c
	where a.doc_submission_mst_id=b.invoice_bill_id and b.is_invoice_bill=1 and b.id = c.mst_id and b.benificiary_id=$company_id
	group by  a.invoice_id,b.received_date,b.is_invoice_bill , c.type
	union all
	select b.invoice_bill_id as invoice_id, b.received_date , b.is_invoice_bill, c.type,  sum(c.document_currency) as document_currency
	from  com_export_proceed_realization b , com_export_proceed_rlzn_dtls c
	where  b.is_invoice_bill  = 2 and b.id = c.mst_id and b.benificiary_id=$company_id
	group by b.invoice_bill_id, b.received_date , b.is_invoice_bill, c.type
	order by invoice_id");

	$rlzdtlsChk =array();// $rlz_date_arr=array();
	foreach ($rlz_date_res as $val)
	{
		if($val[csf("type")]==0)
		{
			$rlz_invoice_deduc_dist[$val[csf("invoice_id")]]["deduct"] += $val[csf("document_currency")];
		}
		else
		{
			$rlz_invoice_deduc_dist[$val[csf("invoice_id")]]["dist"] += $val[csf("document_currency")];
		}
			$rlz_invoice_deduc_dist[$val[csf("invoice_id")]]["total"] += $val[csf("document_currency")];

	}

	$exfact_qnty_arr=return_library_array(" SELECT invoice_no,
	sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END)-sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty
	from pro_ex_factory_mst where status_active=1 and is_deleted=0 and invoice_no>0 group by invoice_no","invoice_no","ex_factory_qnty");
	$variable_standard_arr=return_library_array(" select monitor_head_id,monitoring_standard_day from variable_settings_commercial where status_active=1 and is_deleted=0 and company_name=$company_id and variable_list=19","monitor_head_id","monitoring_standard_day");

	$sql_order_set=sql_select("SELECT a.mst_id, a.po_breakdown_id, a.current_invoice_qnty, (a.current_invoice_qnty*c.total_set_qnty) as invoice_qnty_pcs, c.total_set_qnty from com_export_invoice_ship_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_breakdown_id=b.id and b.job_id=c.id and c.id=$job_id and b.id in ($po_id) and a.status_active=1 and a.is_deleted=0");
	$inv_qnty_pcs_arr=array();
	foreach($sql_order_set as $row)
	{
		$inv_qnty_pcs_arr[$row[csf("mst_id")]]+=$row[csf("invoice_qnty_pcs")];
	}

	$find_lc_sc_all=sql_select("SELECT a.id as LC_SC_ID, 1 as TYPE
	FROM com_export_lc a, com_export_lc_order_info b
	WHERE a.beneficiary_name=$company_id and a.id=b.com_export_lc_id and b.wo_po_break_down_id in($po_id) and a.status_active=1 and b.status_active=1 
	UNION ALL
	SELECT  a.id as LC_SC_ID, 2 as TYPE
	FROM com_sales_contract a, com_sales_contract_order_info b
	WHERE a.beneficiary_name=$company_id and a.id=b.com_sales_contract_id and b.wo_po_break_down_id in($po_id) and a.status_active=1 and b.status_active=1 ");
	$lc_id=$sc_id="";
	foreach($find_lc_sc_all as $row)
	{
		if($row["TYPE"]==1)
		{
			$lc_id.=$row["LC_SC_ID"].",";
		}
		else
		{
			$sc_id.=$row["LC_SC_ID"].",";
		}
	}
	// print_r($find_lc_sc_all);die;
	$lc_id=implode(",",array_unique(explode(",",chop($lc_id,','))));
	$sc_id=implode(",",array_unique(explode(",",chop($sc_id,','))));
	$sql="";
	
	// print_r($lc_id);die;
	if($lc_id!="")
	{
		$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date, a.discount_ammount, a.bonus_ammount, a.claim_ammount, a.commission, a.other_discount_amt, a.upcharge, a.net_invo_value, a.total_carton_qnty, a.actual_shipment_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n, a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode,b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, b.doc_presentation_days, a.co_date as co_date, d.current_invoice_qnty as invoice_quantity, d.current_invoice_value as invoice_value, 1 as type
		FROM com_export_invoice_ship_mst a, com_export_lc b, com_export_invoice_ship_dtls d
		WHERE a.benificiary_id=$company_id and b.id in ($lc_id) and a.is_lc=1 and a.lc_sc_id=b.id and d.mst_id=a.id and d.po_breakdown_id in ($po_id) and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and d.status_active=1";
	}
	if($sc_id!="")
	{
		if($sql!=""){$sql.=" UNION ALL ";}
		$sql.="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date, a.discount_ammount, a.bonus_ammount, a.claim_ammount, a.commission, a.other_discount_amt, a.upcharge, a.net_invo_value, a.total_carton_qnty, a.actual_shipment_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, c.doc_presentation_days, a.co_date as co_date, d.current_invoice_qnty as invoice_quantity, d.current_invoice_value as invoice_value, 2 as type
		FROM com_export_invoice_ship_mst a, com_sales_contract c, com_export_invoice_ship_dtls d
		WHERE a.benificiary_id=$company_id and c.id in ($sc_id) and a.is_lc=2 and a.lc_sc_id=c.id and d.mst_id=a.id and d.po_breakdown_id in ($po_id) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and d.status_active=1";
	}
	
	//echo $sql;die;
	$sql_re=sql_select($sql);
	?>
	<fieldset style="width:1840px;" >
	<legend>EXPORT CI STATEMENT</legend>
	<div style="width:1840px">
		<br />
		<table width="1820" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1" align="left">
			<thead>
				<tr>
					<th width="50">Sl</th>
					<th width="100">Invoice No.</th>
					<th width="70">Invoice Date</th>
					<th width="70">SC/LC</th>
					<th width="100">SC/LC No.</th>
					<th width="70">Buyer Name</th>
					<th width="100">Ex-factory Qnty</th>
					<th width="100">Invoice Qnty.</th>
					<th width="100">Invoice Qnty. Pcs</th>
					<th width="100">Invoice value</th>
					<th width="100">Net Invoice Amount</th>
					<th width="80">Currency</th>
					<th width="70">Ex-Factory Date</th>
					<th width="100">Bank Bill No.</th>
					<th width="70">Bank Bill Date</th>
					<th width="80">Pay Term</th>
					<th width="70">Actual Realized Date</th>
					<th width="70">Realization Amount</th>
					<th width="100">Distributions</th>
					<th width="100">Deduction at source</th>
					<th>Remarks</th>
				</tr>
			</thead>
		</table>
		<div style="width:1840px; overflow-y:scroll; max-height:290px;font-size:12px; overflow-x:hidden;" id="scroll_body" align="left">
			<table width="1820" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
				<tbody>
				<?
				
				$k=1;$gb=1;
				foreach($sql_re as $row_result)
				{
					if ($k%2==0)
					$bgcolor="#E9F3FF";
					else
					$bgcolor="#FFFFFF";
					$id=$row_result[csf('id')];
					$exfact_date_calculate=$row_result[csf('ex_factory_date')];//$variable_standard_arr
					$bl_date_calculate=$row_result[csf('bl_date')];
					$variable_standard_bl_day=$possiable_bl_date=$realization_sub_day=$doc_presentation_days=$possiable_bank_sub_date=$variable_standard_gsp_day=$possiable_gsp_date=$variable_standard_co_day=$possiable_co_date="";
					if($exfact_date_calculate!="" && $exfact_date_calculate!='0000-00-00')
					{
						$variable_standard_bl_day=$variable_standard_arr[1]*60*60*24;
						$possiable_bl_date=date('d-m-Y',strtotime($exfact_date_calculate)+$variable_standard_bl_day);

					}
					if($bl_date_calculate!="" && $bl_date_calculate!='0000-00-00')
					{
						if($row_result[csf("type")]==1)
						{
							$realization_sub_day=$bank_sub_data[$row_result[csf('id')]]["submit_date"];
							$doc_presentation_days=$row_result[csf("doc_presentation_days")]*60*60*24;
							$possiable_bank_sub_date=date('d-m-Y',strtotime($bl_date_calculate)+$doc_presentation_days);
						}
						if($row_result[csf("type")]==2)
						{
							$realization_sub_day=$buyer_submit_date_arr[$row_result[csf('id')]];
							$doc_presentation_days=$row_result[csf("doc_presentation_days")]*60*60*24;
							$possiable_buyer_sub_date=date('d-m-Y',strtotime($bl_date_calculate)+$doc_presentation_days);
						}
						$variable_standard_gsp_day=$variable_standard_arr[2]*60*60*24;
						$possiable_gsp_date=date('d-m-Y',strtotime($bl_date_calculate)+$variable_standard_gsp_day);
						$variable_standard_co_day=$variable_standard_arr[3]*60*60*24;
						$possiable_co_date=date('d-m-Y',strtotime($bl_date_calculate)+$variable_standard_co_day);

					}
					if($group_buyer[$row_result[csf('buyer_id')]]=="")
					{
						$group_buyer[$row_result[csf('buyer_id')]]=$row_result[csf('buyer_id')];
						if($gb!=1)
						{
							?>
							<tr bgcolor="#EFEFEF">
								<th width="50">&nbsp;</th>
								<th width="100">&nbsp;</th>

								<th width="70">&nbsp;</th>
								<th width="70">&nbsp;</th>
								<th width="100">&nbsp;</th>
								<th width="70">Sub Total:</th>

								<th width="100" align="right"><? echo number_format($sub_ex_fact_qnty,2); ?></th>
								<th width="100" align="right"><? echo number_format($sub_invoice_qty,2); ?></th>
								<th width="100" align="right"><? echo number_format($sub_invoice_qty_pcs,2); ?></th>
								<th width="100" align="right"><?  ?></th>
								<th width="100"  align="right"><? echo number_format($sub_order_qnty,2);  ?></th>
								<th width="80">&nbsp;</th>

								<th width="70">&nbsp;</th>
								<th width="100">&nbsp;</th>
								<th width="70">&nbsp;</th>


								<th width="80">&nbsp;</th>
								<th width="70">&nbsp;</th>
								<th width="70" align="right"><? echo number_format($sub_rlz_amt,2); ?></th>
								<th width="100" align="right"><? echo fn_number_format($sub_rlz_dist,2); ?></th>
								<th width="100" align="right"><? echo fn_number_format($sub_rlz_deduct,2); ?></th>
								<th >&nbsp;</th>
							</tr>
							<?
							$sub_ex_fact_qnty=$sub_invoice_qty=$sub_invoice_qty_pcs=$sub_order_qnty=$sub_rlz_amt=$sub_rlz_dist=$sub_rlz_deduct=$distribution_amt=$diductiontion_amt=0;
						}
						?>
						<tr bgcolor="#EFEFEF">
							<td colspan="21"><b><? echo $buyer_arr[$row_result[csf('buyer_id')]];?></b></td>
						</tr>
						<?
						$gb++;
					}
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
						<td width="50"><? echo $k;//$row_result[csf('id')];?></th>
						<td width="100"><? echo $row_result[csf('invoice_no')];?></td>
						<td width="70" align="center">
						<? if($row_result[csf('invoice_date')]!="0000-00-00" && $row_result[csf('invoice_date')]!="") {echo change_date_format($row_result[csf('invoice_date')]);} else {echo "&nbsp;";}?>
						</td>
						<td width="70"  align="center"><? if($row_result[csf('type')] == 1) echo "LC"; else echo "SC"; ?></td>
						<td width="100"><? echo $row_result[csf('lc_sc_no')];?></td>
						<td width="70"><? echo  $buyer_arr[$row_result[csf('buyer_id')]];?></td>
						<td width="100" align="right">
							<? echo  number_format($exfact_qnty_arr[$row_result[csf('id')]],2);
							$total_ex_fact_qnty+=$exfact_qnty_arr[$row_result[csf('id')]];
							$sub_ex_fact_qnty+=$exfact_qnty_arr[$row_result[csf('id')]];
							?>
						</td>
						<td width="100" align="right">
							<? 
							echo number_format($row_result[csf('invoice_quantity')],2); 
							$total_invoice_qty +=$row_result[csf('invoice_quantity')]; 
							$sub_invoice_qty +=$row_result[csf('invoice_quantity')];
							?>
						</td>
						<td width="100" align="right"><? echo number_format($inv_qnty_pcs_arr[$row_result[csf('id')]],2); $total_invoice_qty_pcs +=$inv_qnty_pcs_arr[$row_result[csf('id')]]; $sub_invoice_qty_pcs +=$inv_qnty_pcs_arr[$row_result[csf('id')]];?></td>


					
						<td width="100" align="right">
							<? 
							echo number_format($row_result[csf('invoice_value')],2,'.',''); 
						    $sub_total_grs_value +=$row_result[csf('invoice_value')];
						    $total_grs_value +=$row_result[csf('invoice_value')];
							?>
						</td>
 
						<td width="100" align="right">
							<? 
							echo number_format($row_result[csf('net_invo_value')],2,'.','');
							$total_order_qnty +=$row_result[csf('net_invo_value')];
							$sub_order_qnty +=$row_result[csf('net_invo_value')];
							?>
						</td>
						<td width="80" align="center"><? echo $currency[$row_result[csf('currency_name')]];?></td>
						<td width="70"  align="center"><? if($row_result[csf('ex_factory_date')]!="0000-00-00" && $row_result[csf('ex_factory_date')]!="") {echo change_date_format($row_result[csf('ex_factory_date')]);} else {echo "&nbsp;";} ?></td>
						<td width="100"><? echo $bank_sub_data[$row_result[csf('id')]]["bank_ref_no"]; ?></td>
						<td width="70"   align="center">
						<?
						if(!(trim($bank_sub_data[$row_result[csf('id')]]["submit_date"])=="0000-00-00" || trim($bank_sub_data[$row_result[csf('id')]]["submit_date"])==""))
						{
							echo change_date_format($bank_sub_data[$row_result[csf('id')]]["submit_date"]);
						}
						else
						{
							echo "&nbsp;";
						}
						?></td>
						<td width="80"><? echo $pay_term[$row_result[csf('pay_term')]];?></td>
						<td width="70"   align="center">
						<?
						if(!(trim($rlz_date_arr[$row_result[csf('id')]])=="0000-00-00" || trim($rlz_date_arr[$row_result[csf('id')]])==""))
						{
							echo change_date_format($rlz_date_arr[$row_result[csf('id')]]);
						}
						else
						{
							echo "&nbsp;";
						}
						?>
						</td>
						<td width="70" align="right"><? if(!(trim($rlz_date_arr[$row_result[csf('id')]])=="0000-00-00" || trim($rlz_date_arr[$row_result[csf('id')]])==""))
						{
							echo number_format($row_result[csf('net_invo_value')],2,'.','');
							$total_rlz_amt+=$row_result[csf('net_invo_value')]; $sub_rlz_amt+=$row_result[csf('net_invo_value')];
						}
						else
						{
							echo "";
						}
						?></td>
						<td width="100" align="right" title="invoice Distribution share =  Distribution X ( invoice Realization / total Realization)">
						<?
						$distribution_amt=$rlz_invoice_deduc_dist[$row_result[csf('id')]]["dist"] * ($row_result[csf('net_invo_value')]/($rlz_invoice_deduc_dist[$row_result[csf('id')]]["total"]));
							
								echo fn_number_format($distribution_amt,2,".","");
								//echo $distribution_amt;
								if(fn_number_format($distribution_amt,2,".","")!='')
								{
									$sub_rlz_dist += $distribution_amt;
									$total_rlz_dist += $distribution_amt;
								}
								
							
						?>
						</td>
						<td width="100" align="right" title="invoice Deduction share =  Deduction X ( invoice Realization / total Realization)">
						<?
							$diductiontion_amt=$rlz_invoice_deduc_dist[$row_result[csf('id')]]["deduct"] * ($row_result[csf('net_invo_value')]/$rlz_invoice_deduc_dist[$row_result[csf('id')]]["total"]);
							echo fn_number_format($diductiontion_amt,2,".","");
							if(fn_number_format($diductiontion_amt,2,".","")!='')
							{
								$total_rlz_deduct += $diductiontion_amt;
								$sub_rlz_deduct += $diductiontion_amt;
							}
						?>
						</td>
						<td><? echo $row_result[csf('remarks')];?></td>

					</tr>
					<?
					$k++;
				}
				?>
				</tbody>
			</table>
			<table width="1820" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="report_table_footer" align="left">
				<tfoot>
					<tr class="tbl_bottom">
						<th width="50">&nbsp;</th>
						<th width="100">&nbsp;</th>

						<th width="70">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="70">Sub Total:</th>

						<th width="100" align="right"><? echo number_format($sub_ex_fact_qnty,2); ?></th>
						<th width="100" align="right"><? echo number_format($sub_invoice_qty,2); ?></th>
						<th width="100" align="right"><? echo number_format($sub_invoice_qty_pcs,2); ?></th>
						<th width="100" align="right"><?= $sub_total_grs_value; ?></th>
						<th width="100"  align="right"><? echo number_format($sub_order_qnty,2);  ?></th>
						<th width="80">&nbsp;</th>

						<th width="70">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="70">&nbsp;</th>


						<th width="80">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="70" align="right"><? echo number_format($sub_rlz_amt,2); ?></th>
						<th width="100" align="right"><? echo fn_number_format($sub_rlz_dist,2); ?></th>
						<th width="100" align="right"><? echo fn_number_format($sub_rlz_deduct,2); ?></th>
						<th >&nbsp;</th>
					</tr>
					<tr>
						<th width="50">&nbsp;</th>
						<th width="100">&nbsp;</th>

						<th width="70">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="70">Total:</th>

						<th width="100" id="value_total_ex_fact_qnty" align="right"><? echo number_format($total_ex_fact_qnty,2); ?></th>
						<th width="100" id="value_total_invoice_qty" align="right"><? echo number_format($total_invoice_qty,2); ?></th>
						<th width="100" id="value_total_invoice_qty_pcs" align="right"><? echo number_format($total_invoice_qty_pcs,2); ?></th>
						<th width="100"  align="right"><?= $total_grs_value; ?></th>
						<th width="100"  id="value_total_net_invo_value"  align="right"><? echo number_format($total_order_qnty,2);  ?></th>
						<th width="80">&nbsp;</th>

						<th width="70">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="70">&nbsp;</th>


						<th width="80">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="70"  id="value_total_rlz_amt"  align="right"><? echo number_format($total_rlz_amt,2);?></th>
						<th width="100" id="value_total_rlz_dist" align="right"><? echo fn_number_format($total_rlz_dist,2)?></th>
						<th width="100" id="value_total_rlz_deduct" align="right"><? echo fn_number_format($total_rlz_deduct,2);?></th>
						<th >&nbsp;</th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
	</fieldset>
	<?

	exit();
}

if($action=="gray_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents($title, "../../../../", 1, 1,$unicode,'','');
	?>
	<fieldset style="width:400px;" >
	<div style="width:400px">
		<br />
		<table width="390" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1" align="left">
			<thead>
				<tr>
					<th width="50">Sl</th>
					<th width="80">Production Qnty</th>
					<th width="80">Transfer in Qnty</th>
					<th width="80">Transfer Out Qnty</th>
					<th width="100">Total</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>1</td>
					<td align="right"><? if($trans_amnt>0) echo number_format($trans_amnt,2); else echo "0.00";?></td>
					<td align="right"><? if($trans_in>0) echo number_format($trans_in,2); else echo "0.00";?></td>
					<td align="right"><? if($trans_out>0) echo number_format($trans_out,2); else echo "0.00";?></td>
					<td align="right"><? $total_amnt = $trans_amnt+$trans_in-$trans_out; 
					echo number_format($total_amnt,2);
					?></td>
				</tr>
			</tbody>
		</table>
	</div>
	</fieldset>
	<?
}

function fnc_tempengine2($table_name="", $user_id="", $entry_form="", $ref_from="", $ref_id_arr="")
{
	global $con ;
	
	$numeless=count($ref_id_arr);
	//echo $con.'='.$user_id.'='.$entry_form.'='.$ref_from.'='.$ref_id_arr;
	//print_r($ref_id_arr);
	$psql = "BEGIN PRC_TEMPENGINE(:in_user_id,:in_ref_from,:in_entry_form,:in_ref_id_arr, :in_ref_table); END;";//:in_ref_str_arr, 
	$stmt = oci_parse($con,$psql);
	oci_bind_by_name($stmt,":in_user_id",$user_id);
	oci_bind_by_name($stmt,":in_entry_form",$entry_form);
	oci_bind_by_name($stmt,":in_ref_from",$ref_from);
	
	oci_bind_array_by_name($stmt, ":in_ref_id_arr", $ref_id_arr, $numeless, -1, SQLT_INT);
	//oci_bind_array_by_name($stmt, ":in_ref_str_arr", $ref_str_arr, $numeless, -1, SQLT_CHR);
	
	oci_bind_by_name($stmt,":in_ref_table",$table_name);
	oci_execute($stmt); 
	//echo "jahid";
	oci_commit($con);
	disconnect($con);
}

disconnect($con);
?>