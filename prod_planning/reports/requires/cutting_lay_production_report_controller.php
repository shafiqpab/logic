<?php 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.fabrics.php');
if (!function_exists('pre')) 
{
	 function pre($array){
		echo "<pre>";
		print_r($array);
		echo "</pre>";
	 }
}

$user_id 	= $_SESSION['logic_erp']['user_id'];
$data		= $_REQUEST['data'];
$action		= $_REQUEST['action'];

$company_arr		= return_library_array( "select id, company_name from lib_company",'id','company_name');
$location_arr		= return_library_array( "select id, location_name from lib_location",'id','location_name');
$floor_arr			= return_library_array( "select id, floor_name from lib_prod_floor where status_active=1 and production_process=1",'id','floor_name');
$color_library		= return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
// $order_no_library	= return_library_array( "select id,po_number from wo_po_break_down", "id", "po_number"  );
$buyer_arr			= return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
$country_arr			= return_library_array( "select id, country_name from lib_country",'id','country_name');
$table_arr			= return_library_array( "select id, table_no from lib_cutting_table",'id','table_no');
$size_name_arr		= return_library_array( "select id, size_name from lib_size",'id','size_name');
//--------------------------------------------------------------------------------------------------------------------

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor_id", 130, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data'  order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );
}

if ($action=="load_drop_down_buyer")
{
	// echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "onchange_buyer()" );     	 
	// exit();
	echo create_drop_down( "cbo_buyer_name", 110, "select distinct buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in ($data) and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 120, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' ","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/cutting_lay_production_report_controller',this.value, 'load_drop_down_floor','floor_td');" );     	 
	exit();
}

if($action=="print_button_variable_setting")
{
    $print_report_format_arr = return_library_array("select format_id from lib_report_template where template_name in($data) and module_id=4 and report_id=260 and is_deleted=0 and status_active=1","format_id","format_id");
    echo "print_report_button_setting('".implode(',',$print_report_format_arr)."');\n";
    exit(); 
}

if($action=="party_popup")
{
	echo load_html_head_contents("Company Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
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
				name += selected_name[i] + ',';
			}

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#hide_party_id').val( id );
			$('#hide_party_name').val( name );
		}
    </script>
        <input type="hidden" name="hide_party_name" id="hide_party_name" value="" />
        <input type="hidden" name="hide_party_id" id="hide_party_id" value="" />
        <input type="hidden" name="hidd_type" id="hidd_type" value="<?=$type; ?>" />
	<?

	$sql="select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name";
	echo create_list_view("tbl_list_search", "Company Name", "380","380","270",0, $sql , "js_set_value", "id,company_name", "", 1, "0", $arr , "company_name", "",'setFilterGrid("tbl_list_search",-1);','0','',1) ;

   exit();
}

if($action=="report_generate")
{
	//echo "su..re";
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	//echo $cbo_company_name;die;
	//cbo_company_name*cbo_buyer_name*txt_job_no*txt_file_no*txt_order_no*txt_cutting_no*txt_table_no*txt_date_from*txt_date_to
	$company_name	= str_replace( "'", "", $cbo_company_name);
	$wo_company_name= str_replace( "'", "", $cbo_wo_company_name);
	$location_name	= str_replace( "'", "", $cbo_location_name);
	$floor_id	    = str_replace( "'", "", $cbo_floor_id);
	$buyer_name		= str_replace( "'", "", $cbo_buyer_name);
	$job_no			= str_replace( "'", "", $txt_job_no);
	$file_no		= str_replace( "'", "", $txt_file_no);
	$order_no		= str_replace( "'", "", $txt_order_no);
	$cutting_no		= str_replace( "'", "", $txt_cutting_no);
	$internal_ref_no = str_replace( "'", "", $txt_internal_ref_no);
	$table_no		= str_replace( "'", "", $txt_table_no);
	$manual_cut_no	= str_replace( "'", "", $txt_manual_cut_no);
	$from_date		= str_replace( "'", "", $txt_date_from);
	$to_date		= str_replace( "'", "", $txt_date_to);
	$reportType		= str_replace( "'", "", $reportType);
	$year           = date('Y');

	if($reportType==1) // show button
	{
	
		//id cutting_no table_no job_no entry_date
		// $company_name	== 0  ? $company_name 	= "" : $company_name 	= "AND a.company_id	= '".$company_name."'";
		if($company_name=="") $company_name=""; else $company_name=" and a.company_id in($company_name)";
		$wo_company_name== 0  ? $wo_company_name= "" : $wo_company_name = "AND a.working_company_id	= '".$wo_company_name."'";
		$location_name	== 0  ? $location_name 	= "" : $location_name 	= "AND a.location_id	= '".$location_name."'";
		$floor_id		== 0  ? $floor_id		= "" : $floor_id		= "AND a.floor_id='".$floor_id."'";
		$buyer_name		== 0  ? $buyer_name		= "" : $buyer_name		= "AND c.buyer_name='".$buyer_name."'";
		$job_no			== "" ? $job_no			= "" : $job_no			= "AND c.job_no_prefix_num='".$job_no."'";
		$file_no		== "" ? $file_no		= "" : $file_no			= "AND d.file_no='".$file_no."'";
		$order_no		== "" ? $order_no		= "" : $order_no		= "AND d.po_number='".$order_no."'";
		$cutting_no		== "" ? $cutting_no		= "" : $cutting_no		= "AND a.cut_num_prefix_no='".$cutting_no."'";
		$internal_ref_no== "" ? $internal_ref_no= "" : $internal_ref_no	= "AND d.grouping='".$internal_ref_no."'";
		$table_no		== "" ? $table_no		= "" : $table_no		= "AND a.table_no='".$table_no."'";
		$from_date		!= "" && $to_date	   != "" ? $cutting_date	= "AND a.entry_date between $txt_date_from AND $txt_date_to" : $cutting_date="and to_char(a.entry_date,'YYYY')='$year'";;


		//main query============	
		$sql=sql_select("SELECT a.id as cut_lay_id, a.cutting_no, a.table_no, a.job_no, a.batch_id, a.lay_fabric_wght, a.cad_marker_cons, a.entry_date, a.floor_id, d.id as order_id, b.color_id, (e.size_qty) AS marker_qty,b.marker_qty AS total_marker_qty, c.buyer_name, c.style_ref_no, d.po_number, d.file_no, d.grouping
		FROM ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_bundle e, wo_po_details_master c, wo_po_break_down d 
		WHERE a.id=b.mst_id and b.id=e.dtls_id   AND a.job_no=c.job_no AND c.id=d.job_id AND e.order_id=d.id $company_name $wo_company_name $buyer_name $job_no $file_no $order_no $cutting_no $table_no $cutting_date $floor_id $internal_ref_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and c.status_active=1 and d.status_active=1");
		
	    //  echo "<pre>";
		// print_r($sql);
		// echo $sql; die;
		//===================

		if (count($sql)==0) 
		{
			echo "<div style='color:red;text-align:center;'>Data not Found..<div/>";die;
		}

		$job_arr=array();
		$po_id_arr=array();
		$cut_no_arr=array();
		foreach($sql as $job_for_class){
			$job_arr[$job_for_class[csf('job_no')]]=$job_for_class[csf('job_no')];
			$po_id_arr[$job_for_class[csf('order_id')]]=$job_for_class[csf('order_id')];
			$cut_no_arr[$job_for_class[csf('cut_lay_id')]]=$job_for_class[csf('cut_lay_id')];
		}
		// echo "<pre>";print_r($cut_no_arr);echo "</pre>";die;

		/* =============================================================================== /
		/							DATA SAVE IN GLOBAL TABLE							   /
		/=================================================================================*/
		$con = connect();
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2) and ENTRY_FORM=20");
		oci_commit($con);
		
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 20, 1, $po_id_arr, $empty_arr);//PO ID
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 20, 2, $cut_no_arr, $empty_arr);//CUT ID
		disconnect($con);
		// print_r($po_id_arr);die();
		$allPoIds = implode(",", $po_id_arr); 
		$condition= new condition();     
	    $condition->po_id_in($allPoIds);     
	    $condition->init();
	    $fabric= new fabric($condition);
	    // echo $fabric->getQuery();
		// $fabric=new fabric($job_arr,'job');
		$fab_data=$fabric->getQtyArray_by_orderGmtscolorAndBodypart_knitAndwoven_greyAndfinish();
		//echo "<pre>";print_r($fab_data);echo "</pre>";
		//==============================
		
		$job_order_color_arr=array();
		$subtotal_marker_qty=array();
		foreach($sql as $row)
		{
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['cuting_date'] 	= $row[csf('entry_date')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['cutting_no']		= $row[csf('cutting_no')];
			
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['table_no']		= $row[csf('table_no')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['order_id']		= $row[csf('order_id')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['job_no']			= $row[csf('job_no')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['batch_id']		= $row[csf('batch_id')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['buyer_name']		= $row[csf('buyer_name')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['file_no']			= $row[csf('file_no')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['po_number']		= $row[csf('po_number')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['style_ref_no']	= $row[csf('style_ref_no')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['lay_fabric_wght']	= $row[csf('lay_fabric_wght')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['cad_marker_cons']	= $row[csf('cad_marker_cons')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['marker_qty']	 	+= $row[csf('marker_qty')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['total_marker_qty']	 	+= $row[csf('total_marker_qty')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['floor_id']	 	 = $row[csf('floor_id')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['grouping']	 	 = $row[csf('grouping')];
			$subtotal_marker_qty[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]]['marker_qty'] += $row[csf('marker_qty')];
			$total_marker_qty +=$subtotal_marker_qty[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]]['marker_qty'];
			
		}
	    //   echo "<pre>";
		//     print_r($job_order_color_arr);
		// print_r($job_order_color_arr);
		// echo $sql;

		// =================================== getting roll w8 ===============================
		$roll_sql = "SELECT a.CUTTING_NO,b.ROLL_ID,b.ROLL_WGT from ppl_cut_lay_mst a,ppl_cut_lay_roll_dtls b,gbl_temp_engine c where a.id=b.mst_id and a.id=c.ref_val and c.user_id = $user_id  and c.entry_form=20 and c.ref_from=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		// echo $roll_sql;die;
		$res = sql_select($roll_sql);
		$roll_wgt_arr = array();
		$roll_id_chk_arr = array();
		foreach ($res as $val) 
		{
			if(!in_array($val['ROLL_ID'], $roll_id_chk_arr))
			{
				$roll_wgt_arr[$val['CUTTING_NO']] += $val['ROLL_WGT'];
				$roll_id_chk_arr[$val['ROLL_ID']] = $val['ROLL_ID'];
			}
		}
		
		//   echo "<pre>"; print_r($roll_wgt_arr);die();

		//wo_po_color_size_breakdown
		$sql_order_dtls=sql_select("SELECT a.po_break_down_id, a.color_number_id, (a.plan_cut_qnty) AS plan_cut_qnty, (a.order_quantity) AS rmg_color_qty FROM wo_po_color_size_breakdown a,gbl_temp_engine b WHERE a.po_break_down_id=b.ref_val and b.user_id = $user_id and b.entry_form=20 and b.ref_from=1 and a.status_active=1 AND a.is_deleted=0");
		$order_dtls_arr=array();
		foreach($sql_order_dtls as $row)
		{
			$order_dtls_arr[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]]['plan_cut_qnty'] = $row[csf('plan_cut_qnty')];
			$order_dtls_arr[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]]['rmg_color_qty'] = $row[csf('rmg_color_qty')];
		}
		//print_r($order_dtls_arr);die;

		// ========================= fin fab rcv ========================
		$sql2=sql_select("SELECT a.po_breakdown_id, a.color_id,a.entry_form, a.quantity FROM order_wise_pro_details a,gbl_temp_engine b WHERE a.po_breakdown_id=b.ref_val and b.user_id = $user_id and b.entry_form=20 and b.ref_from=1 and a.entry_form in(7,14,37,18) AND a.trans_id>0 ");
		$fin_rcv_qty_arr=array(); $fin_issue_qty_arr=array();
		foreach($sql2 as $row)
		{
			if($row[csf('entry_form')]==7 || $row[csf('entry_form')]==37)
			{
				$fin_rcv_qty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]+=$row[csf('quantity')];
			}
			else if ($row[csf('entry_form')]==14)
			{
				if ($row[csf('trans_type')]==5)
					$fin_rcv_qty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]+=$row[csf('quantity')];
				else if ($row[csf('trans_type')]==6)
					$fin_rcv_qty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]-=$row[csf('quantity')];
			}
			else if ($row[csf('entry_form')]==18)
			{
				$fin_issue_qty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]+=$row[csf('quantity')];
			}
		}
		
		$sql5=sql_select("SELECT a.order_id,
		a.color_id,
		a.mst_id,
		b.cutting_no,
		 (a.reject_qty)      AS reject_qty,
		 (a.qc_pass_qty)     AS qc_pass_qty,
		 (a.replace_qty)     AS replace_qty
		FROM pro_gmts_cutting_qc_dtls a, pro_gmts_cutting_qc_mst b, gbl_temp_engine c
		WHERE a.mst_id=b.id and a.order_id=c.ref_val and c.user_id = $user_id and c.entry_form=20 and c.ref_from=1 AND a.status_active = 1 AND a.is_deleted = 0 and  b.status_active = 1 AND b.is_deleted = 0");
		//echo $sql5;die;
		$qc_qty_arr=array();
		foreach($sql5 as $row)
		{
			$qc_qty_arr[$row[csf('color_id')]][$row[csf('order_id')]][$row[csf('cutting_no')]]['reject_qty'] += $row[csf('reject_qty')];
			$qc_qty_arr[$row[csf('color_id')]][$row[csf('order_id')]][$row[csf('cutting_no')]]['qc_pass_qty']+= $row[csf('qc_pass_qty')];
			$qc_qty_arr[$row[csf('color_id')]][$row[csf('order_id')]][$row[csf('cutting_no')]]['replace_qty']+= $row[csf('replace_qty')];
		}
		
		// echo"<pre>";
		// print_r($qc_qty_arr);
		$con = connect();
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2) and ENTRY_FORM=20");
		oci_commit($con);
		disconnect($con);
		
		ob_start();
		?>
	    <table class="rpt_table" width="2220" cellpadding="0"  id="" cellspacing="0" border="1" rules="all">
	        <caption style="font-size:20px; font-weight:bold;">
				<?php 
					$com_name = str_replace( "'", "", $cbo_company_name );
	                echo $company_arr[$com_name]."<br/>"."Cutting Lay Production Report";
	            ?>
	            <div style="color:red; text-align:left; font-size:16px;">Group By Job, PO and Color</div>
	        </caption>
	        <thead>
	            <tr>
	                <th width="50">Sl</th>
	                <th width="70">Cutting Date</th>
	                <th width="100">Cutting No.</th>
	                <th width="100">Cutting Floor</th>
	                <th width="100">Color Name</th>
	                <th width="60">Table No</th>
	                <th width="100">Buyer Name</th>
	                <th width="100">Job No</th>
	                <th width="60">Style Reff</th>
	                <th width="100">Internal Ref. Number</th>
	                <th width="60">File No</th>
	                <th width="60">Batch No</th>
	                <th width="60">Order No</th>
	                <th width="60">Fini. Req. Qty.</th>
	                <th width="60">Fini. Rcvd. Qty.</th>
	                <th width="60">Fini. Issue Qty.</th>
	                <th width="60">Balance</th>
	                <th width="60">RMG Color Qty</th>
	                <th width="60">Plan Cut Qty (Color)</th>
	                <th width="60">Yet To Cut</th>
	                <th width="60">Lay Fabric Weight (Kg)</th>
	                <th width="60">CAD Marker Cons/Pcs</th>
	                <th width="60">Marker Qty.</th>
	                <th width="60">QC Pass Qty.</th>
	                <th width="60">Replace Qty.</th>
	                <th width="60">Reject Qty.</th>
	                <th width="60">Net Cons/Pcs</th>
	                <th width="60">QC Pass Cons. Qty.</th>
	                <th width="60">Cons. Variation Qty.</th>
	                <th width="60">Cons. Variation (%)</th>
	                <th width="60">Total Rej. Fab. Qty. (Kg)</th>
	                <th>Total Rej. Fab. Qty. (%)</th>
	            </tr>
	        </thead>
	    </table>
	    <div style=" max-height:350px; width:2220px; overflow-y:scroll;" id="scroll_body">
	        <table class="rpt_table" id="table_body" width="2200" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
	            <tbody>
	            	<?php 
					$sl=0;
					$color_subtot_arr=array();
					$grand_total_fini_req_qty			= 0;
					$grand_total_fini_rcv_qty			= 0;
					$grand_total_rmg_color_qty			= 0;
					$grand_total_plan_cut_qty			= 0;
					$grand_total_yet_to_cut				= 0;
					$grand_total_lay_fabric_weight		= 0;
					$grand_total_cad_marker_cons		= 0;
					$grand_total_marker_qty				= 0;
					$grand_total_qc_pass_qty			= 0;
					$grand_total_replace_qty			= 0;
					$grand_total_reject_qty				= 0;
					$grand_total_cut_cons_qty			= 0;
					$grand_total_qc_pass_cons_qty		= 0;
					$grand_total_cons_variation_qty		= 0;
					$grand_total_cons_variation_percn	= 0;
					$grand_total_reject_kg				= 0;
					$grand_total_reject_percn			= 0;

					foreach($job_order_color_arr as $job_ids=>$job_vals)
					{
						foreach($job_vals as $order_ids=>$order_vals)
						{
						
							foreach($order_vals as $color_ids=>$color_vals)
							{							
								$total_fini_req_qty			= 0;
								$total_fini_rcv_qty			= 0;
								$total_fini_issue_qty		= 0;
								$total_balance				= 0;
								$total_rmg_color_qty		= 0;
								$total_plan_cut_qty			= 0;
								$total_yet_to_cut			= 0;
								$total_lay_fabric_weight	= 0;
								$total_cad_marker_cons		= 0;
								$total_marker_qty			= 0;
								$total_qc_pass_qty			= 0;
								$total_replace_qty			= 0;
								$total_reject_qty			= 0;
								$total_cut_cons_qty			= 0;
								$total_qc_pass_cons_qty		= 0;
								$total_cons_variation_qty	= 0;
								$total_cons_variation_percn	= 0;
								$total_reject_kg			= 0;
								$total_reject_percn			= 0;

								foreach($color_vals as $cutting_ids=>$cutting_vals)
								{
									$sl++;
									$bgcolor=($sl%2==0)?"#E9F3FF":"#FFFFFF";
									$fin_qty=array_sum($fab_data['knit']['finish'][$order_ids][$color_ids][1])+array_sum($fab_data['knit']['finish'][$order_ids][$color_ids][20])+array_sum($fab_data['knit']['finish'][$order_ids][$color_ids][125]);//1,20,125
									//Plan Cut Qty - sum of  Marker Qty.
									//echo"<pre>";print_r($fab_data['knit']['finish'][$order_ids][$color_ids][1]);
									$yet_to_cut=$order_dtls_arr[$order_ids][$color_ids]['plan_cut_qnty'] - $color_vals['marker_qty'];
									// Lay Fabric Weight / Marker Qty.
									$net_cons_per_pcs=$roll_wgt_arr[$cutting_ids]/$cutting_vals['marker_qty'];
									//qc_pass_cons_qty = ((Replace Qty * Marker Cons. Per pcs) + Lay Fabric Weight)/QC pass qty.

									$qc_pass_cons_qty=(($qc_qty_arr[$color_ids][$order_ids]['replace_qty']*$net_cons_per_pcs)+$roll_wgt_arr[$cutting_ids])/$qc_qty_arr[$color_ids][$order_ids][$cutting_ids]['qc_pass_qty'];
									// echo $qc_qty_arr[$color_ids][$order_ids]['replace_qty']."*".$net_cons_per_pcs."+".$roll_wgt_arr[$cutting_ids]."/".$qc_qty_arr[$color_ids][$order_ids][$cutting_ids]['qc_pass_qty']."<br>";

									//cons_variation_qty=QC pass Consum - Net Cons per Pcs
									$cons_variation_qty=$qc_pass_cons_qty-$net_cons_per_pcs;
									//Cons. Variation / QC pass cons. * 100
									$cons_variation_percn=$cons_variation_qty/$qc_pass_cons_qty*100;
									//Reject Qty. * Net Cons Per Pcs
									$reject_kg=$qc_qty_arr[$color_ids][$order_ids][$cutting_ids]['reject_qty']*$net_cons_per_pcs;
									//Total Reject Fab. Qty. / Lay Fabric weight *100
									$reject_percn=$reject_kg/$roll_wgt_arr[$cutting_ids]*100;
									// New Lay Fabric Weight Kg
									$print_report_format_arr=return_library_array( "select template_name, format_id from lib_report_template where  module_id=4 and report_id=118 and is_deleted=0 and status_active=1 and template_name=$com_name", "template_name", "format_id");
									//print_r($print_report_format_arr);die;
	                          $report_id=explode(",",$print_report_format_arr[$com_name]);
							 $first_index =   $report_id[0];
							//  print_r($first_index);die;

   


									?>
									<tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $sl;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')">
										<td width="50"><?php echo $sl; ?></td>
										<td width="70"><p><?php echo change_date_format($cutting_vals['cuting_date']); ?></p></td>
                                        
										
										<?php
											if($first_index==858){?>
												<td width="100"><p><a href="#" onClick="generate_report_lay_chart2('<?php echo $cutting_vals['cutting_no']; ?>'+'*'+'<?php echo $cutting_vals['job_no']; ?>'+'*'+'<? echo $com_name ?>'+'*'+'','cut_lay_entry_report_print_two')"><?php echo $cutting_vals['cutting_no']; ?></a></p></td>
											<? }
											else if($first_index==857){?>
												<td width="100"><p><a href="#" onClick="generate_report_lay_chart1('<?php echo $cutting_vals['cutting_no']; ?>'+'*'+'<?php echo $cutting_vals['job_no']; ?>'+'*'+'<? echo $com_name ?>'+'*'+'','cut_lay_entry_report_print')"><?php echo $cutting_vals['cutting_no']; ?></a></p></td>
											<?} else{ ?>
												<td width="100"><p><?php echo $cutting_vals['cutting_no']; ?></p></td>
										<?	}

										?>
                                             
										
									   
									
										<td width="100"><p><?php echo $floor_arr[$cutting_vals['floor_id']]; ?></p></td>
										<td width="100" title="<?=$color_ids;?>"><p><?php echo $color_library[$color_ids]; ?></p></td>
										<td width="60"><p><?php echo $table_arr[$cutting_vals['table_no']]; ?></p></td>
										<td width="100"><p><?php echo $buyer_arr[$cutting_vals['buyer_name']]; ?></p></td>
										<td width="100"><p><?php echo $cutting_vals['job_no']; ?></p></td>
										<td width="60"><p><?php echo $cutting_vals['style_ref_no']; ?></p></td>
										<td width="100"><p><?php echo $cutting_vals['grouping']; ?></p></td>
										<td width="60"><p><?php echo $cutting_vals['file_no']; ?></p></td>
										<td width="60"><p><?php echo $cutting_vals['batch_id']; ?></p></td>
										<td width="60" title="<?=$order_ids;?>"><p><?php echo $cutting_vals['po_number']; ?></p></td>
	                                   
	                                    <?
										//print_r($color_check_arr);
										if(in_array($color_ids,$color_check_arr))
										{
											?>	
											<td width="60" align="right"><p><?php //echo fn_number_format($fin_qty, 2); ?></p></td><!--Fini. Req. Qty.-->
											<td width="60" align="right"><p><?php //echo fn_number_format($fin_rcv_qty_arr[$order_ids][$color_ids], 2); ?></p></td>
											<td width="60"></td>
											<td width="60"></td>
											<td width="60" align="right"><p><?php //echo $order_dtls_arr[$order_ids][$color_ids]['rmg_color_qty']; ?></p></td><!--RMG Color Qty-->
											<td width="60" align="right"><p><?php //echo $order_dtls_arr[$order_ids][$color_ids]['plan_cut_qnty']; ?></p></td><!--Plan Cut Qty (Color)-->
		                                    <td width="60" align="right"><p><?php //echo $yet_to_cut; ?></p></td><!--Yet To Cut-->	
											<?	
										}
										else
										{
											$total_fini_req_qty			+= $fin_qty;
											$total_fini_rcv_qty			+= $fin_rcv_qty_arr[$order_ids][$color_ids];
											$total_fini_issue_qty		+= $fin_issue_qty_arr[$order_ids][$color_ids];
											$total_balance 				+= $fin_rcv_qty_arr[$order_ids][$color_ids] - $fin_issue_qty_arr[$order_ids][$color_ids];
											$total_rmg_color_qty		+= $order_dtls_arr[$order_ids][$color_ids]['rmg_color_qty'];
											$total_plan_cut_qty			+= $order_dtls_arr[$order_ids][$color_ids]['plan_cut_qnty'];
											?>
		                                    
											<td width="60" align="right"><p><?php echo fn_number_format($fin_qty, 2); ?></p></td>
											<td width="60" align="right"><p><?php echo fn_number_format($fin_rcv_qty_arr[$order_ids][$color_ids], 2); ?></p></td>
											<td width="60" align="right"><p><?php echo fn_number_format($fin_issue_qty_arr[$order_ids][$color_ids], 2); ?></p></td>
											<td width="60" align="right"><p><?php echo fn_number_format(($fin_issue_qty_arr[$order_ids][$color_ids] - $fin_rcv_qty_arr[$order_ids][$color_ids]), 2); ?></p></td>
											<td width="60" align="right"><p><?php echo $order_dtls_arr[$order_ids][$color_ids]['rmg_color_qty']; ?></p></td>
											<td width="60" align="right"><p><?php echo $order_dtls_arr[$order_ids][$color_ids]['plan_cut_qnty']; ?></p></td>
		                                    <td width="60" align="right" title="(Plan Cut Qty (Color)) - (Marker qty)"><p><?php echo $order_dtls_arr[$order_ids][$color_ids]['plan_cut_qnty']-$subtotal_marker_qty[$job_ids][$order_ids][$color_ids]['marker_qty']; ?></p></td>
		                                    <?
										}
										?>
										<td width="60" align="right"><p><?php echo fn_number_format($roll_wgt_arr[$cutting_ids]*( $cutting_vals['marker_qty']/ $cutting_vals['total_marker_qty']), 2); ?></p></td>
										<td width="60" align="right"><p><?php echo fn_number_format($cutting_vals['cad_marker_cons']/12, 4); ?></p></td>
										<td width="60" align="right"><p><?php echo $cutting_vals['marker_qty']; ?></p></td>
										<td width="60" align="right"><p><?php echo $qc_qty_arr[$color_ids][$order_ids][$cutting_ids]['qc_pass_qty']; ?></p></td>
										<td width="60" align="right"><p><?php echo $qc_qty_arr[$color_ids][$order_ids][$cutting_ids]['replace_qty']; ?></p></td>
										<td width="60" align="right"><p><?php echo $qc_qty_arr[$color_ids][$order_ids][$cutting_ids]['reject_qty']; ?></p></td>

										<td width="60" align="right" title="(Lay Fabric Weight (Kg)) / (Marker Qty)"><p><?php echo fn_number_format($net_cons_per_pcs, 4); ?></p></td>
										<td width="60" align="right" title="((Replace Qty * Marker Cons Per pcs) + Lay Fabric Weight (Kg)) / QC pass qty"><p><?php echo fn_number_format($qc_pass_cons_qty, 4); ?></p></td>
										<td width="60" align="right" title="QC Pass Cons Qty - Net Cons per Pcs"><p><?php echo fn_number_format($cons_variation_qty, 4); ?></p></td>
										<td width="60" align="right" title="Cons Variation Qty /  QC Pass Cons Qty * 100"><p><?php echo fn_number_format($cons_variation_percn, 2); ?></p></td>
										<td width="60" align="right" title="Reject Qty. * Net Cons Per Pcs"><p><?php echo fn_number_format($reject_kg, 2); ?></p></td>
										<td align="right" title="Total Reject Fab Qty / Lay Fabric weight (kg)*100"><p><?php echo fn_number_format($reject_percn, 2); ?></p></td>
									</tr>
									<?php 
									$total_yet_to_cut			+= $yet_to_cut;	
									$total_lay_fabric_weight	+= $roll_wgt_arr[$cutting_ids]*( $cutting_vals['marker_qty']/ $cutting_vals['total_marker_qty']);
									$total_cad_marker_cons		+= $cutting_vals['cad_marker_cons']/12;
									$total_marker_qty			+= $cutting_vals['marker_qty'];
									$total_qc_pass_qty			+= $qc_qty_arr[$color_ids][$order_ids][$cutting_ids]['qc_pass_qty'];
									$total_replace_qty			+= $qc_qty_arr[$color_ids][$order_ids][$cutting_ids]['replace_qty'];
									$total_reject_qty			+= $qc_qty_arr[$color_ids][$order_ids][$cutting_ids]['reject_qty'];
									$total_cut_cons_qty			+= $net_cons_per_pcs;
									$total_qc_pass_cons_qty		+= $qc_pass_cons_qty;
									$total_cons_variation_qty	+= $cons_variation_qty;
									$total_cons_variation_percn	+= $cons_variation_percn;
									$total_reject_kg			+= $reject_kg;
									// $total_reject_percn			+= $reject_percn;

									$color_check_arr[]=$color_ids;

								}
								$total_reject_percn = ($total_reject_kg / $total_lay_fabric_weight) * 100;
								// if(!in_array($color_ids,$color_subtot_arr))
								// {
									?>
									<tr bgcolor="#dccdcd"> 
										<td colspan="13" align="right"><strong>Color Qty Total=</strong></td>
										<td align="right"><p><strong><?php echo fn_number_format($total_fini_req_qty, 2); ?></strong></p></td>
										<td align="right"><p><strong><?php echo fn_number_format($total_fini_rcv_qty, 2); ?></strong></p></td>
										<td align="right"><p><strong><?php echo fn_number_format($total_fini_issue_qty, 2); ?></strong></p></td>
										<td align="right"><p><strong><?php echo fn_number_format($total_balance, 2); ?></strong></p></td>
										<td align="right"><p><strong><?php echo $total_rmg_color_qty; ?></strong></p></td>
										<td align="right"><p><strong><?php echo $total_plan_cut_qty; ?></strong></p></td>
										<td align="right"><p><strong><?php echo $total_yet_to_cut; ?></strong></p></td>
										<td align="right"><p><strong><?php echo fn_number_format($total_lay_fabric_weight, 2); ?></strong></p></td>
										<td align="right"><p><strong><?php echo fn_number_format($total_cad_marker_cons, 4); ?></strong></p></td>
										<td align="right"><p><strong><?php echo $total_marker_qty; ?></strong></p></td>
										<td align="right"><p><strong><?php echo $total_qc_pass_qty; ?></strong></p></td>
										<td align="right"><p><strong><?php echo $total_replace_qty; ?></strong></p></td>
										<td align="right"><p><strong><?php echo $total_reject_qty; ?></strong></p></td>
										<td align="right"><p><strong><?php echo fn_number_format($total_cut_cons_qty, 4); ?></strong></p></td>
										<td align="right"><p><strong><?php echo fn_number_format($total_qc_pass_cons_qty, 4); ?></strong></p></td>
										<td align="right"><p><strong><?php echo fn_number_format($total_cons_variation_qty, 4); ?></strong></p></td>
										<td align="right"><p><strong><?php echo fn_number_format($total_cons_variation_percn, 2); ?></strong></p></td>
										<td align="right"><p><strong><?php echo fn_number_format($total_reject_kg, 2); ?></strong></p></td>
										<td align="right"><p><strong><?php echo fn_number_format($total_reject_percn, 2); ?></strong></p></td>
									</tr>
									<?php
									$grand_total_fini_req_qty		+= $total_fini_req_qty;
									$grand_total_fini_rcv_qty		+= $total_fini_rcv_qty;
									$grand_total_fini_issue_qty		+= $total_fini_issue_qty;
									$grand_total_balance			+= $total_balance;
									$grand_total_rmg_color_qty		+= $total_rmg_color_qty;
									$grand_total_plan_cut_qty		+= $total_plan_cut_qty;
									$grand_total_yet_to_cut			+= $total_yet_to_cut;
									$grand_total_lay_fabric_weight	+= $total_lay_fabric_weight;
									$grand_total_cad_marker_cons	+= $total_cad_marker_cons;
									$grand_total_marker_qty			+= $total_marker_qty;
									$grand_total_qc_pass_qty		+= $total_qc_pass_qty;
									$grand_total_replace_qty		+= $total_replace_qty;
									$grand_total_reject_qty			+= $total_reject_qty;
									$grand_total_cut_cons_qty		+= $total_cut_cons_qty;
									$grand_total_qc_pass_cons_qty	+= $total_qc_pass_cons_qty;
									$grand_total_cons_variation_qty	+= $total_cons_variation_qty;
									$grand_total_cons_variation_percn+= $total_cons_variation_percn;
									$grand_total_reject_kg			+= $total_reject_kg;
									$grand_total_reject_percn		= ($grand_total_reject_kg / $grand_total_lay_fabric_weight) * 100;
								// }
								$color_subtot_arr[]=$color_ids;
								unset($color_check_arr);
							}
							
						}
					}
					?>
	            </tbody>
	            <tfoot>            	
	                <tr> 
	                    <th colspan="13" align="right"><strong>Job Total=</strong></th>
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_fini_req_qty, 2); ?></strong></p></th>
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_fini_rcv_qty, 2); ?></strong></p></th>
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_fini_issue_qty, 2); ?></strong></p></th>
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_balance, 2); ?></strong></p></th>
	                    <th align="right"><p><strong><?php echo $grand_total_rmg_color_qty; ?></strong></p></th>
	                    <th align="right"><p><strong><?php echo $grand_total_plan_cut_qty; ?></strong></p></th>
	                    <th align="right"><p><strong><?php echo $grand_total_yet_to_cut; ?></strong></p></th>
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_lay_fabric_weight, 2); ?></strong></p></th>
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_cad_marker_cons, 4); ?></strong></p></th>
	                    <th align="right"><p><strong><?php echo $grand_total_marker_qty; ?></strong></p></th>
	                    <th align="right"><p><strong><?php echo $grand_total_qc_pass_qty; ?></strong></p></th>
	                    <th align="right"><p><strong><?php echo $grand_total_replace_qty; ?></strong></p></th>
	                    <th align="right"><p><strong><?php echo $grand_total_reject_qty; ?></strong></p></th>
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_cut_cons_qty, 4); ?></strong></p></th>
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_qc_pass_cons_qty, 4); ?></strong></p></th>
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_cons_variation_qty, 4); ?></strong></p></th>
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_cons_variation_percn, 2); ?></strong></p></th>
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_reject_kg, 2); ?></strong></p></th>
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_reject_percn, 2); ?></strong></p></th>
	                </tr>
	            </tfoot>
	        </table>
	    </div>
		<?php
	}
	elseif($reportType==2) // show2 button
	{	
		//id cutting_no table_no job_no entry_date
		$company_name	== 0  ? $company_name 	= "" : $company_name 	= "AND a.company_id	= '".$company_name."'";
		$wo_company_name== 0  ? $wo_company_name= "" : $wo_company_name = "AND a.working_company_id	= '".$wo_company_name."'";
		$location_name	== 0  ? $location_name 	= "" : $location_name 	= "AND a.location_id	= '".$location_name."'";
		$floor_id		== 0  ? $floor_id		= "" : $floor_id		= "AND a.floor_id='".$floor_id."'";
		$buyer_name		== 0  ? $buyer_name		= "" : $buyer_name		= "AND c.buyer_name='".$buyer_name."'";
		$job_no			== "" ? $job_no			= "" : $job_no			= "AND c.job_no_prefix_num='".$job_no."'";
		$file_no		== "" ? $file_no		= "" : $file_no			= "AND d.file_no='".$file_no."'";
		$order_no		== "" ? $order_no		= "" : $order_no		= "AND d.po_number='".$order_no."'";
		$cutting_no		== "" ? $cutting_no		= "" : $cutting_no		= "AND a.cut_num_prefix_no='".$cutting_no."'";
		$internal_ref_no== "" ? $internal_ref_no= "" : $internal_ref_no	= "AND d.grouping='".$internal_ref_no."'";
		$table_no		== "" ? $table_no		= "" : $table_no		= "AND a.table_no='".$table_no."'";
		$from_date		!= "" && $to_date	   != "" ? $cutting_date	= "AND a.entry_date between $txt_date_from AND $txt_date_to" : $cutting_date="and to_char(a.entry_date,'YYYY')='$year'";


		//main query============	
		$sql="SELECT a.cutting_no, a.table_no, a.job_no, a.batch_id,a.working_company_id,a.location_id,a.floor_id, a.lay_fabric_wght, a.cad_marker_cons, a.entry_date, a.floor_id,b.order_cut_no,b.roll_data, d.id as order_id, b.color_id,e.size_id, e.size_qty AS marker_qty,c.id as job_id, c.buyer_name, c.style_ref_no, d.po_number, d.file_no, d.grouping,to_char(c.insert_date,'YYYY') as job_year,a.other_fabric_weight
		FROM ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_bundle e, wo_po_details_master c, wo_po_break_down d 
		WHERE a.id=b.mst_id and b.id=e.dtls_id   AND a.job_no=c.job_no AND c.id=d.job_id AND e.order_id=d.id and a.id=e.mst_id $company_name $wo_company_name $buyer_name $job_no $file_no $order_no $cutting_no $table_no $cutting_date $floor_id $internal_ref_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and c.status_active=1 and d.status_active=1 order by a.entry_date";
	   	// echo $sql;die();
	   	$res = sql_select($sql);
		if (count($res)==0) 
		{
			echo "<div style='color:red;text-align:center;font-weight:bold;font-size:18px;'>Data not Found..<div/>";die;
		}

		$job_arr=array();
		$job_id_arr=array();
		$po_id_arr=array();
		$cut_no_arr=array();
		$size_id_arr=array();
		$po_wise_job_arr=array();
		foreach($res as $job_for_class)
		{
			$job_arr[$job_for_class[csf('job_no')]]=$job_for_class[csf('job_no')];
			$job_id_arr[$job_for_class[csf('job_id')]]=$job_for_class[csf('job_id')];
			$po_id_arr[$job_for_class[csf('order_id')]]=$job_for_class[csf('order_id')];
			$cut_no_arr[$job_for_class[csf('cutting_no')]]=$job_for_class[csf('cutting_no')];
			$size_id_arr[$job_for_class[csf('size_id')]]=$job_for_class[csf('size_id')];
			$po_wise_job_arr[$job_for_class[csf('order_id')]]=$job_for_class[csf('job_no')];
		}
		/* $job_id_cond = where_con_using_array($job_id_arr,0,"job_id");
		// $po_id_arr=return_library_array( "select id, id from  wo_po_break_down where status_active=1 $job_id_cond", "id", "id"  );
		$sql_po = "SELECT id, job_no_mst as job_no from wo_po_break_down where status_active=1 $job_id_cond";
		$po_res = sql_select($sql_po);
		foreach ($po_res as $val) 
		{
			$po_id_arr[$val['ID']] = $val['ID'];
			$po_wise_job_arr[$val['ID']] = $val['JOB_NO'];
		} */	

		/* =============================================================================== /
		/							DATA SAVE IN GLOBAL TABLE							   /
		/=================================================================================*/
		$con = connect();
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from=3 and ENTRY_FORM=20");
		oci_commit($con);
		
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 20, 3, $po_id_arr, $empty_arr);//PO ID
		disconnect($con);

		// print_r($po_id_arr);die();
		$allPoIds = implode(",", $po_id_arr); 
		$condition= new condition();     
	    $condition->po_id_in($allPoIds);     
	    $condition->init();
	    $fabric= new fabric($condition);
	    // echo $fabric->getQuery();
		// $fabric=new fabric($job_arr,'job');
		$fab_data=$fabric->getQtyArray_by_orderGmtscolorAndBodypart_knitAndwoven_greyAndfinish();
		// echo "<pre>";print_r($fab_data);echo "</pre>";
		//==============================
		
		$data_array=array();
		$color_total_array=array();
		$grand_total_array=array();
		$job_po_array = array();
		foreach($res as $row)
		{
			// echo  $row[csf('roll_data')]; die;
			$roll_data_ex = explode("**", $row[csf('roll_data')]);
			$roll_weight = 0;
			// print_r($roll_data_ex);die;
			$n =0;
			$batch_no ='';
			foreach ($roll_data_ex as $val) 
			{
				$roll_weight_ex = explode("=", $val);
				$roll_weight += $roll_weight_ex[3];
				$batch_no .= ($n>0 ? ',': '').$roll_weight_ex[5] ;
				$n++;
			}
			$data_array[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['cuting_date'] 	= $row[csf('entry_date')];
			$data_array[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['cutting_no']		= $row[csf('cutting_no')];
			
			$data_array[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['order_cut_no']		= $row[csf('order_cut_no')];
			$data_array[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['working_company_id']		= $row[csf('working_company_id')];
			$data_array[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['location_id']		= $row[csf('location_id')];
			$data_array[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['floor_id']		= $row[csf('floor_id')];
			$data_array[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['other_fabric_weight'] = $row[csf('other_fabric_weight')];
			$data_array[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['roll_weight'] = $roll_weight;
			$data_array[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['batch_no'] = $batch_no;

			$data_array[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('cutting_no')]][$row[csf('size_id')]]['qty'] += $row[csf('marker_qty')];

			$color_total_array[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('size_id')]]['qty'] += $row[csf('marker_qty')];
			$grand_total_array[$row[csf('size_id')]]['qty'] += $row[csf('marker_qty')];

			$data_array[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['buyer_name']		= $row[csf('buyer_name')];
			$data_array[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['file_no']			= $row[csf('file_no')];
			$data_array[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['style_ref_no']	= $row[csf('style_ref_no')];
			$data_array[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['job_no']	= $row[csf('job_no')];
			$data_array[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['job_year']	= $row[csf('job_year')];
			$data_array[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['grouping']	= $row[csf('grouping')];

			$job_po_array[$row[csf('order_id')]] = $row[csf('job_no')];
		}

		// ========================= fin fab rcv ========================
		$po_id_cond = where_con_using_array($po_id_arr,0,'a.po_breakdown_id');
		$sql = "SELECT b.job_no, a.color_id,d.body_part_id, (a.quantity) as fin_rcv FROM order_wise_pro_details a,wo_po_details_master b, wo_po_break_down c, pro_finish_fabric_rcv_dtls d,GBL_TEMP_ENGINE e WHERE a.po_breakdown_id=c.id and b.id=c.job_id and d.id=a.dtls_id and a.po_breakdown_id=e.ref_val and e.user_id = $user_id  and e.entry_form=20 and e.ref_from=3 and a.entry_form in(37,58,68) and a.status_active=1 and b.status_active=1 and c.status_active=1";
		// echo $sql;
		$res = sql_select($sql);
		$fin_rcv_qty_arr=array();
		foreach($res as $row)
		{
			if($row[csf('body_part_id')]==1 || $row[csf('body_part_id')]==20)
			{
				$fin_rcv_qty_arr[$row[csf('job_no')]][$row[csf('color_id')]][1]+=$row[csf('fin_rcv')];
			}
			else
			{
				$fin_rcv_qty_arr[$row[csf('job_no')]][$row[csf('color_id')]][2]+=$row[csf('fin_rcv')];
			}
		}
		// ========================= fin fab rcv and issue ========================
		$sql2=sql_select("SELECT b.job_no, a.color_id,d.body_part_id, (case when a.entry_form in(7) then a.quantity else 0 end) as fin_rcv,(case when a.entry_form in(18) then a.quantity else 0 end) as fin_issue FROM order_wise_pro_details a,wo_po_details_master b, wo_po_break_down c, inv_transaction d,GBL_TEMP_ENGINE e WHERE a.po_breakdown_id=c.id and b.id=c.job_id and d.id=a.trans_id and a.po_breakdown_id=e.ref_val and e.user_id = $user_id  and e.entry_form=20 and e.ref_from=3 and a.entry_form in(7,18) AND a.trans_id>0 and a.status_active=1 and b.status_active=1 and c.status_active=1");
		// $fin_rcv_qty_arr=array();
		$fin_issue_qty_arr=array(); 
		foreach($sql2 as $row)
		{
			if($row[csf('body_part_id')]==1 || $row[csf('body_part_id')]==20)
			{
				$fin_rcv_qty_arr[$row[csf('job_no')]][$row[csf('color_id')]][1]+=$row[csf('fin_rcv')];
				$fin_issue_qty_arr[$row[csf('job_no')]][$row[csf('color_id')]][1]+=$row[csf('fin_issue')];
			}
			else
			{
				$fin_rcv_qty_arr[$row[csf('job_no')]][$row[csf('color_id')]][2]+=$row[csf('fin_rcv')];
				$fin_issue_qty_arr[$row[csf('job_no')]][$row[csf('color_id')]][2]+=$row[csf('fin_issue')];
			}
		}

		// =============================== fin fab transfer =====================
    	$sql = "SELECT a.po_breakdown_id,a.color_id,a.trans_type,a.quantity from order_wise_pro_details a,GBL_TEMP_ENGINE b where a.po_breakdown_id=b.ref_val and b.user_id = $user_id  and b.entry_form=20 and b.ref_from=3 and a.status_active=1 and a.is_deleted=0 and a.entry_form in(14,15,134)";
    	// echo $sql;die(); 
    	$res = sql_select($sql);
    	$transfer_data = array();
    	foreach ($res as $val) 
    	{
    		$transfer_data[$po_wise_job_arr[$val['PO_BREAKDOWN_ID']]][$val['COLOR_ID']][$val['TRANS_TYPE']] += $val['QUANTITY'];
    	}
    	// echo "<pre>";print_r($transfer_data);die();
		// ========================= fin fab req qty ========================
		$sql2=sql_select("SELECT a.job_no, b.gmts_color_id,c.body_part_id as body_part, (b.fin_fab_qnty) as qty FROM wo_booking_mst a, wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c,GBL_TEMP_ENGINE d WHERE a.id=b.booking_mst_id and b.pre_cost_fabric_cost_dtls_id=c.id and b.po_break_down_id=d.ref_val and d.user_id = $user_id  and d.entry_form=20 and d.ref_from=3 and a.status_active=1 and b.status_active=1 and a.item_category=2");
		$fab_req_qty_arr=array();
		foreach($sql2 as $row)
		{
			if($row[csf('body_part')]==1 || $row[csf('body_part')]==20)
			{
				$fab_req_qty_arr[$row[csf('job_no')]][$row[csf('gmts_color_id')]][1]+=$row[csf('qty')];
			}
			else
			{
				$fab_req_qty_arr[$row[csf('job_no')]][$row[csf('gmts_color_id')]][2]+=$row[csf('qty')];
			}
		}
		
		// echo"<pre>";
		// print_r($fab_req_qty_arr);

		$rowspan = array();
		foreach ($data_array as $job_key => $job_value) 
		{
			foreach ($job_value as $color_key => $color_value) 
			{
				foreach ($color_value as $cut_key => $val) 
				{
					$rowspan[$job_key][$color_key]++;
				}
			}
		}

		$tbl_width = 1780 + (count($size_id_arr)*60);
		$con = connect();
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in=3 and ENTRY_FORM=20");
		oci_commit($con);
		disconnect($con);
		
		ob_start();
		?>
	    <table class="rpt_table" width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
	        <caption style="font-size:20px; font-weight:bold;">
				<?php 
					$com_name = str_replace( "'", "", $cbo_company_name );
	                echo $company_arr[$com_name]."<br/>"."Cutting Lay Production Report";
	            ?>
	        </caption>
	        <thead>
	        	<tr>
	                <th rowspan="2" width="50">Sl</th>
	                <th rowspan="2" width="70">Lay Date</th>
	                <th rowspan="2" width="100">Working Company</th>
	                <th rowspan="2" width="100">Location</th>
	                <th rowspan="2" width="100">Floor</th>
	                <th rowspan="2" width="100">Order Cutno</th>
	                <th rowspan="2" width="80">Cutting No</th>

	                <th rowspan="2" width="100">Buyer Name</th>
	                <th rowspan="2" width="100">Int. Ref.</th>
	                <th rowspan="2" width="140">Style Reff</th>
	                <th rowspan="2" width="60">Job No</th>
	                <th rowspan="2" width="40">Job Year</th>
	                <th rowspan="2" width="100">Gmts Color</th>
	                <th rowspan="2" width="100">Batch No</th>

	        		<th colspan="2">Fabric Required</th>
	        		<th colspan="2">Fabric Received By Store</th>
	        		<th colspan="2">Fabric Issued To Cutting</th>
	        		<th colspan="2">Fabric Ussed</th>
	        		<th width="<?=count($size_id_arr)*60+60;?>" colspan="<?=count($size_id_arr)+1;?>">Size Wise Lay Qty Pcs</th>
	        	</tr>
	            <tr>
	                <th width="60">Body Fab.</th>
	                <th width="60">Others Fab.</th>

	                <th width="60">Body Fab.</th>
	                <th width="60">Others Fab.</th>

	                <th width="60">Body Fab.</th>
	                <th width="60">Others Fab.</th>

	                <th width="60">Body Fab.</th>
	                <th width="60">Others Fab.</th>
	                <?
	                foreach ($size_id_arr as $s_key => $s_val) 
	                {
	                	?>
	                	<th width="60"><?=$size_name_arr[$s_key];?></th>
	                	<?
	                }
	                ?>
	                <th width="60">Total</th>
	            </tr>
	        </thead>
	    </table>
	    <div style=" max-height:350px; width:<?=$tbl_width+20;?>px; overflow-y:scroll;" id="scroll_body">
	        <table class="rpt_table" id="table_body" width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
	            <tbody>
	            	<?php 
					$sl=0;
					$grand_tot_req_req_main		= 0;
					$grand_tot_req_req_othr		= 0;
					$grand_tot_fini_rcv_main	= 0;
					$grand_tot_fini_rcv_othr	= 0;
					$grand_tot_fini_issue_main	= 0;
					$grand_tot_fini_issue_othr	= 0;
					$grand_tot_roll_weight		= 0;
					$grand_tot_used_qty			= 0;

					foreach($data_array as $job_ids=>$job_vals)
					{
						foreach($job_vals as $color_ids=>$color_vals)
						{
							$cl=0;							
							$color_tot_req_req_main		= 0;
							$color_tot_req_req_othr		= 0;
							$color_tot_fini_rcv_main	= 0;
							$color_tot_fini_rcv_othr	= 0;
							$color_tot_fini_issue_main	= 0;
							$color_tot_fini_issue_othr	= 0;
							$color_tot_roll_weight		= 0;
							$color_tot_used_qty			= 0;

							foreach($color_vals as $cutting_ids=>$row)
							{
								$sl++;
								$bgcolor=($sl%2==0)?"#E9F3FF":"#FFFFFF";

								// echo $job_ids."*".$color_ids."<br>";
								$fin_trns_in_qty = $transfer_data[$job_ids][$color_ids][5];
								$fin_trns_out_qty = $transfer_data[$job_ids][$color_ids][4];
								
								$fab_req_main 	= $fab_req_qty_arr[$job_ids][$color_ids][1];
								$fab_req_othr 	= $fab_req_qty_arr[$job_ids][$color_ids][2];
								$fin_rcv_main 	= $fin_rcv_qty_arr[$job_ids][$color_ids][1]+$fin_trns_in_qty;
								// echo $fin_rcv_qty_arr[$job_ids][$color_ids][1]."+".$fin_trns_in_qty."<br>";
								$fin_rcv_othr 	= $fin_rcv_qty_arr[$job_ids][$color_ids][2];
								$fin_issue_main = $fin_issue_qty_arr[$job_ids][$color_ids][1]-$fin_trns_out_qty;
								$fin_issue_othr = $fin_issue_qty_arr[$job_ids][$color_ids][2];

								?>
								<tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $sl;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')">
									<td width="50"><?php echo $sl; ?></td>
					                <td width="70" align="center"><?= change_date_format($row['cuting_date']); ?></td>
					                <td width="100"><?=$company_arr[$row['working_company_id']];?></td>
					                <td width="100"><?=$location_arr[$row['location_id']];?></td>
					                <td width="100" title="<?=$row['floor_id'];?>"><?=$floor_arr[$row['floor_id']];?></td>
					                <td width="100"><?=$row['order_cut_no'];?></td>
					                <td width="80"><?=$row['cutting_no'];?></td>

					                <? if($cl==0){ ?>
						                <td rowspan="<?=$rowspan[$job_ids][$color_ids];?>" width="100"><?=$buyer_arr[$row['buyer_name']];?></td>
						                <td rowspan="<?=$rowspan[$job_ids][$color_ids];?>" width="100"><?=$row['grouping'];?></td>
						                <td rowspan="<?=$rowspan[$job_ids][$color_ids];?>" width="140"><?=$row['style_ref_no'];?></td>
						                <td rowspan="<?=$rowspan[$job_ids][$color_ids];?>" width="60"><?=$job_ids;?></td>
						                <td rowspan="<?=$rowspan[$job_ids][$color_ids];?>" width="40" align="center"><?=$row['job_year'];?></td>
						                <td rowspan="<?=$rowspan[$job_ids][$color_ids];?>" width="100" title='<?=$color_ids;?>'><?=$color_library[$color_ids];?></td>
									<? } ?>	
						                <td width="100"><?=  $row['batch_no']; ?></td>
									<? if($cl==0){ ?>		
						                <td rowspan="<?=$rowspan[$job_ids][$color_ids];?>" width="60" align="right"><?=number_format($fab_req_main,0);?></td>
						                <td rowspan="<?=$rowspan[$job_ids][$color_ids];?>" width="60" align="right"><?=number_format($fab_req_othr,0);?></td>

						                <td rowspan="<?=$rowspan[$job_ids][$color_ids];?>" width="60" align="right" title='<?=$fin_rcv_qty_arr[$job_ids][$color_ids][1]."+".$fin_trns_in_qty;?>'>
						                	<?=number_format($fin_rcv_main,0);?>						                		
						                </td>
						                <td rowspan="<?=$rowspan[$job_ids][$color_ids];?>" width="60" align="right"><?=number_format($fin_rcv_othr,0);?></td>

						                <td rowspan="<?=$rowspan[$job_ids][$color_ids];?>" width="60" align="right"><?=number_format($fin_issue_main,0);?></td>
						                <td rowspan="<?=$rowspan[$job_ids][$color_ids];?>" width="60" align="right"><?=number_format($fin_issue_othr,0);?></td>
							            <?						            
							            $cl++;
										$color_tot_req_req_main		+= $fab_req_main;
										$color_tot_req_req_othr		+= $fab_req_othr;
										$color_tot_fini_rcv_main	+= $fin_rcv_main;
										$color_tot_fini_rcv_othr	+= $fin_rcv_othr;
										$color_tot_fini_issue_main	+= $fin_issue_main;
										$color_tot_fini_issue_othr	+= $fin_issue_othr;

										$grand_tot_req_req_main		+= $fab_req_main;
										$grand_tot_req_req_othr		+= $fab_req_othr;
										$grand_tot_fini_rcv_main	+= $fin_rcv_main;
										$grand_tot_fini_rcv_othr	+= $fin_rcv_othr;
										$grand_tot_fini_issue_main	+= $fin_issue_main;
										$grand_tot_fini_issue_othr	+= $fin_issue_othr;
					            	}
					            	?>    

						            <td width="60" align="right"><?=number_format($row['roll_weight'],0);?></td>
						            <td width="60" align="right"><?=$row['other_fabric_weight'];?></td>
						                <?
						                $tot = 0;
					                foreach ($size_id_arr as $s_key => $s_val) 
					                {
					                	?>
					                	<td width="60" align="right"><?=$row[$s_key]['qty'];?></td>
					                	<?
					                	$tot += $row[$s_key]['qty'];
					                }
					                ?>
					                <td width="60" align="right"><?=number_format($tot,0);?></td>
								</tr>
								<?php 
								$color_tot_roll_weight		+= $row['roll_weight'];
								$color_tot_used_qty			+= $a;

								
								$grand_tot_roll_weight		+= $row['roll_weight'];
								$grand_tot_used_qty			+= $a;

							}
							?>
							<tr bgcolor="#cddcdc" style="font-weight: bold;text-align: right;"> 
								<td width="50"></td>
				                <td width="70"></td>
				                <td width="100"></td>
				                <td width="100"></td>
				                <td width="100"></td>
				                <td width="100"></td>
				                <td width="80"></td>

				                <td width="100"></td>
				                <td width="100"></td>
				                <td width="140"></td>
				                <td width="60"></td>
				                <td width="40"></td>
				                <td width="100"></td>
				                <td width="100">Color Total</td>

				                <td width="60"><?=number_format($color_tot_req_req_main,0); ?></td>
				                <td width="60"><?=number_format($color_tot_req_req_othr,0); ?></td>

				                <td width="60"><?=number_format($color_tot_fini_rcv_main,0); ?></td>
				                <td width="60"><?=number_format($color_tot_fini_rcv_othr,0); ?></td>

				                <td width="60"><?=number_format($color_tot_fini_issue_main,0); ?></td>
				                <td width="60"><?=number_format($color_tot_fini_issue_othr,0); ?></td>

				                <td width="60"><?=number_format($color_tot_roll_weight,0); ?></td>
				                <td width="60"><?=number_format($a,0); ?></td>
				                <?
				                $tot = 0;
				                foreach ($size_id_arr as $s_key => $s_val) 
				                {
				                	?>
				                	<td width="60"><?=number_format($color_total_array[$job_ids][$color_ids][$s_key]['qty'],0);?></td>
				                	<?
				                	$tot += $color_total_array[$job_ids][$color_ids][$s_key]['qty'];
				                }
				                ?>
				                <td width="60" align="right"><?=number_format($tot,0);?></td>
							</tr>
							<?php
							$grand_total_fini_req_qty		+= $total_fini_req_qty;
							$grand_total_fini_rcv_qty		+= $total_fini_rcv_qty;
							$grand_total_fini_issue_qty		+= $total_fini_issue_qty;
							$grand_total_balance			+= $total_balance;
							$grand_total_rmg_color_qty		+= $total_rmg_color_qty;
							$grand_total_plan_cut_qty		+= $total_plan_cut_qty;
							$grand_total_yet_to_cut			+= $total_yet_to_cut;
							$grand_total_lay_fabric_weight	+= $total_lay_fabric_weight;
							$grand_total_cad_marker_cons	+= $total_cad_marker_cons;
							$grand_total_marker_qty			+= $total_marker_qty;
							$grand_total_qc_pass_qty		+= $total_qc_pass_qty;
							$grand_total_replace_qty		+= $total_replace_qty;
							$grand_total_reject_qty			+= $total_reject_qty;
							$grand_total_cut_cons_qty		+= $total_cut_cons_qty;
							$grand_total_qc_pass_cons_qty	+= $total_qc_pass_cons_qty;
							$grand_total_cons_variation_qty	+= $total_cons_variation_qty;
							$grand_total_cons_variation_percn+= $total_cons_variation_percn;
							$grand_total_reject_kg			+= $total_reject_kg;
							$grand_total_reject_percn		+= $total_reject_percn;						
							$color_subtot_arr[]=$color_ids;
							unset($color_check_arr);
						}
					}
					?>
	            </tbody>
	            <tfoot>            	
	                <tr> 
	                    <td width="50"></td>
		                <th width="70"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="80"></th>

		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="140"></th>
		                <th width="60"></th>
		                <th width="40"></th>
		                <th width="100">Grand Total</th>

		               	<th width="60"><?=number_format($grand_tot_req_req_main,0); ?></th>
		                <th width="60"><?=number_format($grand_tot_req_req_othr,0); ?></th>

		                <th width="60"><?=number_format($grand_tot_fini_rcv_main,0); ?></th>
		                <th width="60"><?=number_format($grand_tot_fini_rcv_othr,0); ?></th>

		                <th width="60"><?=number_format($grand_tot_fini_issue_main,0); ?></th>
		                <th width="60"><?=number_format($grand_tot_fini_issue_othr,0); ?></th>

		                <th width="60"><?=number_format($grand_tot_roll_weight,0); ?></th>
		                <th width="60"><?=number_format($a,0); ?></th>
		                <?
		                $tot = 0;
		                foreach ($size_id_arr as $s_key => $s_val) 
		                {
		                	?>
		                	<th width="60"><?=number_format($grand_total_array[$s_key]['qty'],0);?></th>
		                	<?
		                	$tot += $grand_total_array[$s_key]['qty'];
		                }
		                ?>
		                <th width="60"><?=number_format($tot,0);?></th>
	                </tr>
	            </tfoot>
	        </table>
	    </div>
		<?php
	}
	elseif($reportType==3) // show3 button
	{	
		//id cutting_no table_no job_no entry_date
		$company_name	== 0  ? $company_name 	= "" : $company_name 	= "AND a.company_id	= '".$company_name."'";
		$wo_company_name== 0  ? $wo_company_name= "" : $wo_company_name = "AND a.working_company_id	= '".$wo_company_name."'";
		$location_name	== 0  ? $location_name 	= "" : $location_name 	= "AND a.location_id	= '".$location_name."'";
		$floor_id		== 0  ? $floor_id		= "" : $floor_id		= "AND a.floor_id='".$floor_id."'";
		$buyer_name		== 0  ? $buyer_name		= "" : $buyer_name		= "AND c.buyer_name='".$buyer_name."'";
		$job_no			== "" ? $job_no			= "" : $job_no			= "AND c.job_no_prefix_num='".$job_no."'";
		$file_no		== "" ? $file_no		= "" : $file_no			= "AND d.file_no='".$file_no."'";
		$order_no		== "" ? $order_no		= "" : $order_no		= "AND d.po_number='".$order_no."'";
		$cutting_no		== "" ? $cutting_no		= "" : $cutting_no		= "AND a.cut_num_prefix_no='".$cutting_no."'";
		$internal_ref_no== "" ? $internal_ref_no= "" : $internal_ref_no	= "AND d.grouping='".$internal_ref_no."'";
		$table_no		== "" ? $table_no		= "" : $table_no		= "AND a.table_no='".$table_no."'";
		$manual_cut_no  == "" ? $manual_cut_no	= "" : $manual_cut_no	= "AND b.order_cut_no='".$manual_cut_no."'";


		//main query============	
		$sql="SELECT a.id,a.cutting_no,a.table_no, a.job_no, b.order_cut_no,d.id as order_id, b.color_id,b.gmt_item_id,e.size_id, e.size_qty AS qty,e.country_id,c.id as job_id, c.buyer_name, c.style_ref_no, d.po_number,d.grouping
		FROM ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_bundle e, wo_po_details_master c, wo_po_break_down d 
		WHERE a.id=b.mst_id and b.id=e.dtls_id   AND a.job_no=c.job_no AND c.id=d.job_id AND e.order_id=d.id and a.id=e.mst_id $company_name $wo_company_name $buyer_name $job_no $file_no $order_no $cutting_no $table_no $manual_cut_no $floor_id $internal_ref_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and c.status_active=1 and d.status_active=1 order by a.entry_date";
	   	// echo $sql;die();
	   	$res = sql_select($sql);
		if (count($res)==0) 
		{
			echo "<div style='color:red;text-align:center;font-weight:bold;font-size:18px;'>Data not Found..<div/>";die;
		}

		$job_id_arr = array();
		$color_id_arr = array();
		$country_id_arr = array();
		$lay_data_arr = array();
		$cut_lay_mst_id_arr = array();
		$mst_id_wise_color = array();
		foreach ($res as $val) 
		{
			$job_id_arr[$val['JOB_ID']] = $val['JOB_ID'];
			$cut_lay_mst_id_arr[$val['ID']] = $val['ID'];
			$mst_id_wise_color[$val['ID']] = $val['COLOR_ID'];
			$mst_id_wise_job[$val['ID']] = $val['JOB_NO'];
			$country_id_arr[$val['COUNTRY_ID']] = $val['COUNTRY_ID'];
			$color_id_arr[$val['COLOR_ID']] = $val['COLOR_ID'];

			$lay_data_arr[$val['JOB_NO']][$val['COUNTRY_ID']][$val['COLOR_ID']][$val['SIZE_ID']]['qty'] += $val['QTY'];
			$lay_data_arr[$val['JOB_NO']][$val['COUNTRY_ID']][$val['COLOR_ID']]['cutting_no'] = $val['CUTTING_NO'];
			$lay_data_arr[$val['JOB_NO']][$val['COUNTRY_ID']][$val['COLOR_ID']]['no_of_cutting'][$val['CUTTING_NO']] = $val['CUTTING_NO'];
			$lay_data_arr[$val['JOB_NO']][$val['COUNTRY_ID']][$val['COLOR_ID']]['order_cut_no'] = $val['ORDER_CUT_NO'];
			$lay_data_arr[$val['JOB_NO']][$val['COUNTRY_ID']][$val['COLOR_ID']]['item_id'] = $val['GMT_ITEM_ID'];
			$lay_data_arr[$val['JOB_NO']]['buyer_name'] = $val['BUYER_NAME'];
			$lay_data_arr[$val['JOB_NO']]['po_number'] .= $val['PO_NUMBER'].",";
			$lay_data_arr[$val['JOB_NO']]['int_ref'] = $val['GROUPING'];
			$lay_data_arr[$val['JOB_NO']]['style'] = $val['STYLE_REF_NO'];
			$lay_data_arr[$val['JOB_NO']]['job_no'] = $val['JOB_NO'];
		}
		// echo "<pre>";print_r($lay_data_arr);die();		
		// pre($cut_lay_mst_id_arr); die;
		/* =============================================================================== /
		/							DATA SAVE IN GLOBAL TABLE							   /
		/=================================================================================*/
		$con = connect();
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in(4,12) and ENTRY_FORM=20");
		oci_commit($con);
		
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 20, 4, $job_id_arr, $empty_arr);//JOB ID
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 20, 12, $cut_lay_mst_id_arr, $empty_arr);//Cut Lay MST ID
		disconnect($con);
		// $job_id_cond = where_con_using_array($job_id_arr,0,"a.job_id");
		// $country_id_cond = where_con_using_array($country_id_arr,0,"a.country_id");
		// $color_id_cond = where_con_using_array($color_id_arr,0,"a.color_number_id");

		// =============================== order data ==============================
		$sql = "SELECT b.po_number, a.job_no_mst,a.item_number_id as item_id, a.country_id,a.color_number_id as color_id,a.size_number_id as size_id,a.order_quantity,a.plan_cut_qnty from wo_po_color_size_breakdown a,wo_po_break_down b,GBL_TEMP_ENGINE C where a.po_break_down_id=b.id AND a.job_id=c.ref_val and c.user_id = $user_id  and c.entry_form=20 and c.ref_from=4 and a.status_active=1 and a.is_deleted=0  order by a.size_order";
		// echo $sql;
		$result = sql_select($sql);
		$size_id_arr = array();
		$data_array = array();

		foreach ($result as $val) 
		{
			$size_id_arr[$val['SIZE_ID']] = $val['SIZE_ID'];
			$order_data_array[$val['JOB_NO_MST']][$val['COUNTRY_ID']][$val['COLOR_ID']][$val['SIZE_ID']]['order_qty'] += $val['ORDER_QUANTITY'];
			$order_data_array[$val['JOB_NO_MST']][$val['COUNTRY_ID']][$val['COLOR_ID']][$val['SIZE_ID']]['plan_cut_qty'] += $val['PLAN_CUT_QNTY'];
			$order_data_array[$val['JOB_NO_MST']][$val['COUNTRY_ID']][$val['COLOR_ID']]['item_id'] = $val['ITEM_ID'];
			$order_data_array[$val['JOB_NO_MST']]['po_number'] .= $val['PO_NUMBER'].",";
		}
		// =============================== Roll DATA ==============================
		$roll_sql="SELECT a.mst_id,a.qnty FROM  pro_roll_details a,gbl_temp_engine tmp where a.mst_id=tmp.ref_val and a.entry_form=509 and tmp.user_id = $user_id  and tmp.entry_form=20 and tmp.ref_from=12 and a.status_active=1 and a.is_deleted=0 ";
		// echo $roll_sql;
		$roll_sql_res = sql_select($roll_sql); 
		$roll_data_arr = array();

		foreach ($roll_sql_res as $v) 
		{ 
			$color_id = $mst_id_wise_color[$v['MST_ID']];
			$job_no = $mst_id_wise_job[$v['MST_ID']];

			$roll_data_arr[$job_no][$color_id]['weight'] += $v['QNTY'];
			$roll_data_arr2[$v['MST_ID']]['weight'] += $v['QNTY'];
		}
		// pre($roll_data_arr2); die;
		// ============================= gmts prod data ==============================
		$job_id_cond = where_con_using_array($job_id_arr,0,"c.job_id");
		$country_id_cond = where_con_using_array($country_id_arr,0,"c.country_id");
		$color_id_cond = where_con_using_array($color_id_arr,0,"c.color_number_id");
		$sql = "SELECT c.job_no_mst,c.country_id,c.color_number_id as color_id,c.size_number_id as size_id, a.production_type, b.production_qnty as qty,b.reject_qty,b.replace_qty from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c,GBL_TEMP_ENGINE d where a.id=b.mst_id and c.id=b.color_size_break_down_id and c.po_break_down_id=a.po_break_down_id and c.job_id=d.ref_val and d.user_id = $user_id  and d.entry_form=20 and d.ref_from=4 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and a.production_type in(1,4)";
		// echo $sql;die();
		$res = sql_select($sql);
		$gmts_data_array = array();
		foreach ($res as $val) 
		{
			$gmts_data_array[$val['JOB_NO_MST']][$val['COUNTRY_ID']][$val['COLOR_ID']][$val['SIZE_ID']][$val['PRODUCTION_TYPE']]['qty'] += $val['QTY'];
			$gmts_data_array[$val['JOB_NO_MST']][$val['COUNTRY_ID']][$val['COLOR_ID']][$val['SIZE_ID']][$val['PRODUCTION_TYPE']]['rej_qty'] += $val['REJECT_QTY'];
			$gmts_data_array[$val['JOB_NO_MST']][$val['COUNTRY_ID']][$val['COLOR_ID']][$val['SIZE_ID']][$val['PRODUCTION_TYPE']]['replace_qty'] += $val['REPLACE_QTY'];
		}
		// pre($gmts_data_array); die;
		$tbl_width = 680 + (count($size_id_arr)*60);
		$con = connect();
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from=4 and ENTRY_FORM=20");
		oci_commit($con);
		disconnect($con);
		
		ob_start();
		?>
	    <table class="rpt_table" width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
	        <caption style="font-size:20px; font-weight:bold;">
				<?php 
					$com_name = str_replace( "'", "", $cbo_company_name );
	                echo $company_arr[$com_name]."<br/>"."Cutting Lay Production Report";
	            ?>
	        </caption>
	        <thead>
	        	<tr>
	                <th rowspan="2" colspan="2" width="400">Order Details</th>
	                <th rowspan="2" width="200">Transection Criteria</th>
	        		<th width="<?=count($size_id_arr)*60;?>" colspan="<?=count($size_id_arr);?>">Size Wise Breakdown</th>
	                <th rowspan="2" width="60">Total</th>
	        	</tr>
	            <tr>
	                <?
	                foreach ($size_id_arr as $s_key => $s_val) 
	                {
	                	?>
	                	<th width="60"><?=$size_name_arr[$s_key];?></th>
	                	<?
	                }
	                ?>
	            </tr>
	        </thead>
	    </table>
	    <div id="scroll_body" style="width: <?=$tbl_width+20;?>px; max-height: 350px;overflow-y: scroll;">
	    	<table class="rpt_table" width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
	            <tbody>
	            	<?php 
					$sl=0;
					foreach($lay_data_arr as $job_no=>$job_data)
					{
						foreach($job_data as $country_id=>$country_data)
						{
							foreach($country_data as $color_id=>$row)
							{
								$sl++;
								$bgcolor=($sl%2==0)?"#E9F3FF":"#FFFFFF";
								$buyer_name = $lay_data_arr[$job_no]['buyer_name'];
								$style = $lay_data_arr[$job_no]['style'];
								$po_number = $job_data['po_number'];
								$cutting_no = $row['cutting_no'];
								$no_of_cutting = count($row['no_of_cutting']);
								$order_cut_no = $row['order_cut_no'];
								$roll_weight = $roll_data_arr[$job_no][$color_id]['weight'];
								

								?>
								<!-- ======================= 1st part ======================= -->
								<tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $sl;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')">
									<td width="150">Buyer</td>
					                <td width="250" align="left"><p><?= $buyer_arr[$buyer_name]; ?></p></td>
					                <td width="200">Gmts. Color /Country Qty</td>
					                
					                <?
					                $tot = 0;
					                foreach ($size_id_arr as $s_key => $s_val) 
					                {
					                	$order_qty = $order_data_array[$job_no][$country_id][$color_id][$s_key]['order_qty'];
					                	?>
					                	<td width="60" align="right"><?=$order_qty;?></td>
					                	<?
					                	$tot += $order_qty;
					                }
					                ?>
					                <td width="60" align="right"><b><?=number_format($tot,0);?></b></td>
								</tr>
								<? $sl++;$bgcolor=($sl%2==0)?"#E9F3FF":"#FFFFFF";?>
								<!-- ======================= 2nd part ======================= -->
								<tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $sl;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')">
									<td width="150">Style</td>
					                <td width="250" align="left"><p><?= $style; ?></p></td>
					                <td width="200">Plan Cut Qty</td>
					                
					                <?
					                $tot = 0;
					                foreach ($size_id_arr as $s_key => $s_val) 
					                {
					                	$plan_cut_qty = $order_data_array[$job_no][$country_id][$color_id][$s_key]['plan_cut_qty'];
					                	?>
					                	<td width="60" align="right"><?=$plan_cut_qty;?></td>
					                	<?
					                	$tot += $plan_cut_qty;
					                }
					                ?>
					                <td width="60" align="right"><b><?=number_format($tot,0);?></b></td>
								</tr>
								<? $sl++;$bgcolor=($sl%2==0)?"#E9F3FF":"#FFFFFF";?>
								<!-- ======================= 3rd part ======================= -->
								<tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $sl;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')">
									<td width="150">Order No</td>
					                <td width="250" align="left"><p><?= implode(", ", array_unique(array_filter(explode(",",$po_number)))); ?></p></td>
					                <td width="200">Cutting [Lay Chart]</td>
					                
					                <?

					                $tot = 0;
					                $lay_chart_tot = 0;
					                foreach ($size_id_arr as $s_key => $s_val) 
					                {
					                	$lay_qty = $lay_data_arr[$job_no][$country_id][$color_id][$s_key]['qty'];
					                	?>
					                	<td width="60" align="right"><?=number_format($lay_qty,0);?></td>
					                	<?
					                	$tot += $lay_qty;
					                	$lay_chart_tot +=$lay_qty;
					                }
					                ?>
					                <td width="60" align="right"><b><?=number_format($tot,0);?></b></td>
								</tr>
								<? $sl++;$bgcolor=($sl%2==0)?"#E9F3FF":"#FFFFFF";?>
								<!-- ======================= 4th part ======================= -->
								<tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $sl;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')">
									<td width="150">Internal Ref.</td>
					                <td width="250" align="left"><p><?= $lay_data_arr[$job_no]['int_ref']; ?></p></td>
					                <td width="200"><b>Cutting Balance</b></td>
					                
					                <?
					                $tot = 0;
					                foreach ($size_id_arr as $s_key => $s_val) 
					                {
					                	$lay_bal = $order_data_array[$job_no][$country_id][$color_id][$s_key]['plan_cut_qty'] - $lay_data_arr[$job_no][$country_id][$color_id][$s_key]['qty'];
					                	?>
					                	<td width="60" align="right"><b><?=$lay_bal;?></b></td>
					                	<?
					                	$tot += $lay_bal;
					                }
					                ?>
					                <td width="60" align="right"><b><?=number_format($tot,0);?></b></td>
								</tr>
								<? $sl++;$bgcolor=($sl%2==0)?"#E9F3FF":"#FFFFFF";?>
								<!-- ======================= 5th part ======================= -->
								<tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $sl;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')">
									<td width="150">Job No</td>
					                <td width="250" align="left"><p><?=$job_no; ?></p></td>
					                <td width="200">Cutting QC</td>
					                
					                <?
					                $tot = 0;
					                foreach ($size_id_arr as $s_key => $s_val) 
					                {
					                	$cut_qc_qty = $gmts_data_array[$job_no][$country_id][$color_id][$s_key][1]['qty'];
					                	?>
					                	<td width="60" align="right"><?=number_format($cut_qc_qty,0);?></td>
					                	<?
					                	$tot += $cut_qc_qty;
					                }
					                ?>
					                <td width="60" align="right"><?=number_format($tot,0);?></td>
								</tr>
								<? $sl++;$bgcolor=($sl%2==0)?"#E9F3FF":"#FFFFFF";?>
								<!-- ======================= 6th part ======================= -->
								<tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $sl;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')">
									<td width="150">Color & Gmts. Item</td>
					                <td width="250" align="left"><p><?=$color_library[$color_id].", ".$garments_item[$row['item_id']]; ?></p></td>
					                <td width="200">Cutting Reject Qty</td>
					                
					                <?
					                $tot = 0;
					                foreach ($size_id_arr as $s_key => $s_val) 
					                {
					                	$cut_rej_qty = $gmts_data_array[$job_no][$country_id][$color_id][$s_key][1]['rej_qty'];
					                	?>
					                	<td width="60" align="right"><?=number_format($cut_rej_qty,0);?></td>
					                	<?
					                	$tot += $cut_rej_qty;
					                }
					                ?>
					                <td width="60" align="right"><b><?=number_format($tot,0);?></b></td>
								</tr>
								<? $sl++;$bgcolor=($sl%2==0)?"#E9F3FF":"#FFFFFF";?>
								<!-- ======================= 7th part ======================= -->
								<tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $sl;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')">
									<td width="150">No of Cutting</td>
					                <td width="250" align="left"><p><?=$no_of_cutting; ?></p></td>
									
					                <td width="200">Cutting Replace Qty</td>
					                
					                <?
					                $tot = 0;
					                foreach ($size_id_arr as $s_key => $s_val) 
					                {
					                	$cut_replace_qty = $gmts_data_array[$job_no][$country_id][$color_id][$s_key][1]['replace_qty'];
					                	?>
					                	<td width="60" align="right"><?=number_format($cut_replace_qty,0);?></td>
					                	<?
					                	$tot += $cut_replace_qty;
					                }
					                ?>
					                <td width="60" align="right"><b><?=number_format($tot,0);?></b></td> 
					                
								</tr>
								<? $sl++;$bgcolor=($sl%2==0)?"#E9F3FF":"#FFFFFF";?>
								<!-- ======================= 8th part ======================= -->
								<tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $sl;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')">
									<td width="150">Lay Fabric Weight</td>
					                <td width="250" align="left"><p><?= $roll_weight ?></p></td>
					                <td width="200"><b>Cutting QC Balance</b></td>
					                
					                <?
					                $tot = 0;
					                foreach ($size_id_arr as $s_key => $s_val) 
					                {
					                	$qc_bal = $order_data_array[$job_no][$country_id][$color_id][$s_key]['plan_cut_qty'] - $gmts_data_array[$job_no][$country_id][$color_id][$s_key][1]['qty'];
					                	?>
					                	<td width="60" align="right"><b><?=number_format($qc_bal,0);?></b></td>
					                	<?
					                	$tot += $qc_bal;
					                }
					                ?>
					                <td width="60" align="right"><b><?=number_format($tot,0);?></b></td> 
					                
								</tr>
								<? $sl++;$bgcolor=($sl%2==0)?"#E9F3FF":"#FFFFFF";?>
								<!-- ======================= 9th part ======================= -->
								<tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $sl;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')">
									<td width="150">Actual Cons./Dzn</td>
									<?
									 	$actual_cons =( $roll_weight /$lay_chart_tot)*12;
									?>
					                <td width="250" align="left"><p><?= number_format($actual_cons,3) ?></p></td> 
									<td width="200">Sewing Input</td>
					                
					                <?
					                $tot = 0;
					                foreach ($size_id_arr as $s_key => $s_val) 
					                {
					                	$input_qty = $gmts_data_array[$job_no][$country_id][$color_id][$s_key][4]['qty'];
					                	?>
					                	<td width="60" align="right"><?=number_format($input_qty,0);?></td>
					                	<?
					                	$tot += $input_qty;
					                }
					                ?>
					                <td width="60" align="right"><b><?=number_format($tot,0);?></b></td>
								</tr>
								<!-- ======================= 10th part ======================= -->
								<tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $sl;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')">
									<td width="150">Country</td>
					                <td width="250" align="left"><p>All Country</p></td> 
					                <td width="200"><b>Input Balance</b></td>
					                <?
					                $tot = 0;
					                foreach ($size_id_arr as $s_key => $s_val) 
					                {
					                	$input_bal = $order_data_array[$job_no][$country_id][$color_id][$s_key]['order_qty'] - $gmts_data_array[$job_no][$country_id][$color_id][$s_key][4]['qty'];
					                	?>
					                	<td width="60" align="right"><b><?=number_format($input_bal,0);?></b></td>
					                	<?
					                	$tot += $input_bal;
					                }
					                ?>
					                <td width="60" align="right"><b><?=number_format($tot,0);?></b></td> 
								</tr>
								<? $sl++;?>
								<tr bgcolor="#cddcdc" style="height: 15px;">
									<td colspan="<?=4+count($size_id_arr);?>"></td>
								</tr>
						
								<?php 						
							}
						}
					}
					?>
	            </tbody>
	        </table>
	    </div>
	    
		<?php
	}

	elseif($reportType==4) // show4 button
	{
		//id cutting_no table_no job_no entry_date
		$company_name	== 0  ? $company_name 	= "" : $company_name 	= "AND a.company_id	= '".$company_name."'";
		$wo_company_name== 0  ? $wo_company_name= "" : $wo_company_name = "AND a.working_company_id	= '".$wo_company_name."'";
		$location_name	== 0  ? $location_name 	= "" : $location_name 	= "AND a.location_id	= '".$location_name."'";
		$floor_id		== 0  ? $floor_id		= "" : $floor_id		= "AND a.floor_id='".$floor_id."'";
		$buyer_name		== 0  ? $buyer_name		= "" : $buyer_name		= "AND c.buyer_name='".$buyer_name."'";
		$job_no			== "" ? $job_no			= "" : $job_no			= "AND c.job_no_prefix_num='".$job_no."'";
		$file_no		== "" ? $file_no		= "" : $file_no			= "AND d.file_no='".$file_no."'";
		$order_no		== "" ? $order_no		= "" : $order_no		= "AND d.po_number='".$order_no."'";
		$cutting_no		== "" ? $cutting_no		= "" : $cutting_no		= "AND a.cut_num_prefix_no='".$cutting_no."'";
		$internal_ref_no== "" ? $internal_ref_no= "" : $internal_ref_no	= "AND d.grouping='".$internal_ref_no."'";
		$table_no		== "" ? $table_no		= "" : $table_no		= "AND a.table_no='".$table_no."'";
		$from_date		!= "" && $to_date	   != "" ? $cutting_date	= "AND a.entry_date between $txt_date_from AND $txt_date_to" : $cutting_date="and to_char(a.entry_date,'YYYY')='$year'";


		//main query============	
		$sql=sql_select("SELECT a.id as cutid, a.cutting_no, a.table_no, a.job_no, a.batch_id, a.lay_fabric_wght, a.cad_marker_cons, a.entry_date, a.floor_id, d.id as order_id, b.color_id, SUM(e.size_qty) AS marker_qty,b.marker_qty AS total_marker_qty, c.buyer_name, c.style_ref_no, d.po_number, d.file_no, d.grouping
		FROM ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_bundle e, wo_po_details_master c, wo_po_break_down d 
		WHERE a.id=b.mst_id and b.id=e.dtls_id   AND a.job_no=c.job_no AND c.id=d.job_id AND e.order_id=d.id $company_name $wo_company_name $buyer_name $job_no $file_no $order_no $cutting_no $table_no $cutting_date $floor_id $internal_ref_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and c.status_active=1 and d.status_active=1
		GROUP BY a.id, a.cutting_no, a.table_no, a.job_no, a.batch_id, a.lay_fabric_wght, a.cad_marker_cons, a.entry_date, a.floor_id, d.id, b.color_id, c.buyer_name, c.style_ref_no, d.po_number, d.file_no, d.grouping,b.marker_qty");
		
	    //  echo "<pre>";
		// print_r($sql);

		//===================

		if (count($sql)==0) 
		{
			echo "<div style='color:red;text-align:center;'>Data not Found..<div/>";die;
		}

		$job_arr=array();
		$po_id_arr=array();
		$cut_no_arr=array();
		foreach($sql as $job_for_class){
			$job_arr[$job_for_class[csf('job_no')]]=$job_for_class[csf('job_no')];
			$po_id_arr[$job_for_class[csf('order_id')]]=$job_for_class[csf('order_id')];
			$cut_no_arr[$job_for_class[csf('cutid')]]=$job_for_class[csf('cutid')];
		}
		// print_r($po_id_arr);die();
		
		$con = connect();
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (5,6) and ENTRY_FORM=20");
		oci_commit($con);
		
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 20, 5, $po_id_arr, $empty_arr);//Po ID
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 20, 6, $cut_no_arr, $empty_arr);//Cut ID
		disconnect($con);
		
		$allPoIds = implode(",", $po_id_arr); 
		$condition= new condition();     
	    $condition->po_id_in($allPoIds);     
	    $condition->init();
	    $fabric= new fabric($condition);
	    // echo $fabric->getQuery();
		// $fabric=new fabric($job_arr,'job');
		$fab_data=$fabric->getQtyArray_by_orderGmtscolorAndBodypart_knitAndwoven_greyAndfinish();
		// echo "<pre>";print_r($fab_data);echo "</pre>";
		//==============================
		
		$job_order_color_arr=array();
		$subtotal_marker_qty=array();
		foreach($sql as $row)
		{
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['cuting_date'] 	= $row[csf('entry_date')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['cutting_no']		= $row[csf('cutting_no')];
			
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['table_no']		= $row[csf('table_no')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['order_id']		= $row[csf('order_id')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['job_no']			= $row[csf('job_no')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['batch_id']		= $row[csf('batch_id')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['buyer_name']		= $row[csf('buyer_name')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['file_no']			= $row[csf('file_no')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['po_number']		= $row[csf('po_number')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['style_ref_no']	= $row[csf('style_ref_no')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['lay_fabric_wght']	= $row[csf('lay_fabric_wght')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['cad_marker_cons']	= $row[csf('cad_marker_cons')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['marker_qty']	 	+= $row[csf('marker_qty')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['total_marker_qty']	 	+= $row[csf('total_marker_qty')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['floor_id']	 	 = $row[csf('floor_id')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['grouping']	 	 = $row[csf('grouping')];
			$subtotal_marker_qty[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]]['marker_qty'] += $row[csf('marker_qty')];
			$total_marker_qty +=$subtotal_marker_qty[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]]['marker_qty'];
			
		}
	    //   echo "<pre>";
		//     print_r($total_job_order_color_arr);
		// print_r($job_order_color_arr);
		// echo $sql;

		// =================================== getting roll w8 ===============================
		$sql = "SELECT a.CUTTING_NO, b.ROLL_ID, b.ROLL_WGT from ppl_cut_lay_mst a, ppl_cut_lay_roll_dtls b, gbl_temp_engine d where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=20 and d.ref_from=6";
		// echo $sql;
		$res = sql_select($sql);
		$roll_wgt_arr = array();
		$roll_id_chk_arr = array();
		foreach ($res as $val) 
		{
			if(!in_array($val['ROLL_ID'], $roll_id_chk_arr))
			{
				$roll_wgt_arr[$val['CUTTING_NO']] += $val['ROLL_WGT'];
				$roll_id_chk_arr[$val['ROLL_ID']] = $val['ROLL_ID'];
			}
		}
		
		
		//   echo "<pre>"; print_r($roll_wgt_arr);die();
		//wo_po_color_size_breakdown
		$sql_order_dtls=sql_select("SELECT a.po_break_down_id, a.color_number_id, (a.plan_cut_qnty) AS plan_cut_qnty, (a.order_quantity) AS rmg_color_qty FROM wo_po_color_size_breakdown a, gbl_temp_engine d WHERE status_active=1 AND is_deleted=0 and a.po_break_down_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=20 and d.ref_from=5");
		$order_dtls_arr=array();
		foreach($sql_order_dtls as $row)
		{
			$order_dtls_arr[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]]['plan_cut_qnty'] += $row[csf('plan_cut_qnty')];
			$order_dtls_arr[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]]['rmg_color_qty'] += $row[csf('rmg_color_qty')];
		}
		//print_r($order_dtls_arr);die;

		// ========================= fin fab rcv ========================
		$sql2=sql_select("SELECT a.po_breakdown_id as PO_BREAKDOWN_ID, a.color_id as COLOR_ID, a.quantity as QUANTITY, a.entry_form as ENTRY_FORM FROM order_wise_pro_details a, gbl_temp_engine d WHERE a.entry_form in(7,18,37) AND a.trans_id>0 and a.po_breakdown_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=20 and d.ref_from=5");
		$fin_rcv_qty_arr=array(); $fin_issue_qty_arr=array();
		foreach($sql2 as $row)
		{
			if($row['ENTRY_FORM']==7 || $row['ENTRY_FORM']==37)
			{
				$fin_rcv_qty_arr[$row['PO_BREAKDOWN_ID']][$row['COLOR_ID']]+=$row['QUANTITY'];
			}
			if($row['ENTRY_FORM']==18)
			{
				$fin_issue_qty_arr[$row['PO_BREAKDOWN_ID']][$row['COLOR_ID']]+=$row['QUANTITY'];
			}
		}

		
		// echo "<pre>";print_r($fin_issue_qty_arr);echo "</pre>";

		// $sql5=sql_select("SELECT order_id, color_id, SUM(reject_qty) AS reject_qty, SUM(qc_pass_qty) AS qc_pass_qty, SUM(replace_qty) AS replace_qty FROM pro_gmts_cutting_qc_dtls where status_active=1 and is_deleted=0 and order_id in($po_ids) GROUP BY order_id, color_id");
		// $qc_qty_arr=array();
		// foreach($sql5 as $row)
		// {
		// 	$qc_qty_arr[$row[csf('color_id')]][$row[csf('order_id')]]['reject_qty']	= $row[csf('reject_qty')];
		// 	$qc_qty_arr[$row[csf('color_id')]][$row[csf('order_id')]]['qc_pass_qty']= $row[csf('qc_pass_qty')];
		// 	$qc_qty_arr[$row[csf('color_id')]][$row[csf('order_id')]]['replace_qty']= $row[csf('replace_qty')];
		// }
		$sql5=sql_select("SELECT a.order_id, a.color_id, a.mst_id, b.cutting_no, (a.reject_qty) AS reject_qty, (a.qc_pass_qty) AS qc_pass_qty, (a.replace_qty) AS replace_qty
								FROM pro_gmts_cutting_qc_dtls a, pro_gmts_cutting_qc_mst b, gbl_temp_engine d
								WHERE a.mst_id=b.id AND a.status_active = 1 AND a.is_deleted = 0 and a.order_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=20 and d.ref_from=5");
		$qc_qty_arr=array();
		foreach($sql5 as $row)
		{
			$qc_qty_arr[$row[csf('color_id')]][$row[csf('order_id')]][$row[csf('cutting_no')]]['reject_qty'] += $row[csf('reject_qty')];
			$qc_qty_arr[$row[csf('color_id')]][$row[csf('order_id')]][$row[csf('cutting_no')]]['qc_pass_qty']+= $row[csf('qc_pass_qty')];
			$qc_qty_arr[$row[csf('color_id')]][$row[csf('order_id')]][$row[csf('cutting_no')]]['replace_qty']+= $row[csf('replace_qty')];
		}
		
		$con = connect();
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (5,6) and ENTRY_FORM=20");
		oci_commit($con);
		disconnect($con);
		
		// echo"<pre>";
		// print_r($qc_qty_arr);
		
		ob_start();
		?>
	    <table class="rpt_table" width="2220" cellpadding="0" cellspacing="0" border="1" rules="all">
	        <caption style="font-size:20px; font-weight:bold;">
				<?php 
					$com_name = str_replace( "'", "", $cbo_company_name );
	                echo $company_arr[$com_name]."<br/>"."Cutting Lay Production Report";
	            ?>
	            <div style="color:red; text-align:left; font-size:16px;">Group By Job, PO and Color</div>
	        </caption>
	        <thead>
	            <tr>
	                <th width="50">Sl</th>
	                <th width="70">Cutting Date</th>
	                <th width="100">Cutting No.</th>
	                <th width="100">Cutting Floor</th>
	                <th width="100">Color Name</th>
	                <th width="60">Table No</th>
	                <th width="100">Buyer Name</th>
	                <th width="100">Job No</th>
	                <th width="60">Style Reff</th>
	                <th width="100">Internal Ref. Number</th>
	                <th width="60">File No</th>
	                <th width="60">Batch No</th>
	                <th width="60">Order No</th>
	                <th width="60">Fini. Req. Qty.</th>
	                <th width="60">Fini. Rcvd. Qty.</th>
	                <th width="60">Fini. Issue Qty.</th>
	                <th width="60">Balance</th>
	                <th width="60">RMG Color Qty</th>
	                <th width="60">Plan Cut Qty (Color)</th>
	                <th width="60">Yet To Cut</th>
	                <th width="60">Lay Fabric Weight (Kg)</th>
	                <th width="60">CAD Marker Cons/Pcs</th>
	                <th width="60">Marker Qty.</th>
	                <th width="60">QC Pass Qty.</th>
	                <th width="60">Replace Qty.</th>
	                <th width="60">Reject Qty.</th>
	                <th width="60">Net Cons/Pcs</th>
	                <th width="60">QC Pass Cons. Qty.</th>
	                <th width="60">Cons. Variation Qty.</th>
	                <th width="60">Cons. Variation (%)</th>
	                <th width="60">Total Rej. Fab. Qty. (Kg)</th>
	                <th>Total Rej. Fab. Qty. (%)</th>
	            </tr>
	        </thead>
	    </table>
	    <div style=" max-height:350px; width:2220px; overflow-y:scroll;" id="scroll_body">
	        <table class="rpt_table" id="table_body" width="2200" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
	            <tbody>
	            	<?php 
					$sl=0;
					$color_subtot_arr=array();
					$grand_total_fini_req_qty			= 0;
					$grand_total_fini_rcv_qty			= 0;
					$grand_total_rmg_color_qty			= 0;
					$grand_total_plan_cut_qty			= 0;
					$grand_total_yet_to_cut				= 0;
					$grand_total_lay_fabric_weight		= 0;
					$grand_total_cad_marker_cons		= 0;
					$grand_total_marker_qty				= 0;
					$grand_total_qc_pass_qty			= 0;
					$grand_total_replace_qty			= 0;
					$grand_total_reject_qty				= 0;
					$grand_total_cut_cons_qty			= 0;
					$grand_total_qc_pass_cons_qty		= 0;
					$grand_total_cons_variation_qty		= 0;
					$grand_total_cons_variation_percn	= 0;
					$grand_total_reject_kg				= 0;
					$grand_total_reject_percn			= 0;

					foreach($job_order_color_arr as $job_ids=>$job_vals)
					{
						foreach($job_vals as $order_ids=>$order_vals)
						{
							//$color_subtot_arr['job_ids']=$job_ids;
							foreach($order_vals as $color_ids=>$color_vals)
							{							
								$total_fini_req_qty			= 0;
								$total_fini_rcv_qty			= 0;
								$total_fini_issue_qty		= 0;
								$total_balance				= 0;
								$total_rmg_color_qty		= 0;
								$total_plan_cut_qty			= 0;
								$total_yet_to_cut			= 0;
								$total_lay_fabric_weight	= 0;
								$total_cad_marker_cons		= 0;
								$total_marker_qty			= 0;
								$total_qc_pass_qty			= 0;
								$total_replace_qty			= 0;
								$total_reject_qty			= 0;
								$total_cut_cons_qty			= 0;
								$total_qc_pass_cons_qty		= 0;
								$total_cons_variation_qty	= 0;
								$total_cons_variation_percn	= 0;
								$total_reject_kg			= 0;
								$total_reject_percn			= 0;

								foreach($color_vals as $cutting_ids=>$cutting_vals)
								{
									$sl++;
									$bgcolor=($sl%2==0)?"#E9F3FF":"#FFFFFF";
									$fin_qty=array_sum($fab_data['knit']['finish'][$order_ids][$color_ids][1])+array_sum($fab_data['knit']['finish'][$order_ids][$color_ids][20])+array_sum($fab_data['knit']['finish'][$order_ids][$color_ids][125]);//1,20,125
									//Plan Cut Qty - sum of  Marker Qty.
									$yet_to_cut=$order_dtls_arr[$order_ids][$color_ids]['plan_cut_qnty'] - $color_vals['marker_qty'];
									// Lay Fabric Weight / Marker Qty.
									$net_cons_per_pcs=$roll_wgt_arr[$cutting_ids]/$cutting_vals['marker_qty'];
									//qc_pass_cons_qty = ((Replace Qty * Marker Cons. Per pcs) + Lay Fabric Weight)/QC pass qty.

									$qc_pass_cons_qty=(($qc_qty_arr[$color_ids][$order_ids]['replace_qty']*$net_cons_per_pcs)+$roll_wgt_arr[$cutting_ids])/$qc_qty_arr[$color_ids][$order_ids][$cutting_ids]['qc_pass_qty'];
									// echo $qc_qty_arr[$color_ids][$order_ids]['replace_qty']."*".$net_cons_per_pcs."+".$roll_wgt_arr[$cutting_ids]."/".$qc_qty_arr[$color_ids][$order_ids][$cutting_ids]['qc_pass_qty']."<br>";

									//cons_variation_qty=QC pass Consum - Net Cons per Pcs
									$cons_variation_qty=$qc_pass_cons_qty-$net_cons_per_pcs;
									//Cons. Variation / QC pass cons. * 100
									$cons_variation_percn=$cons_variation_qty/$qc_pass_cons_qty*100;
									//Reject Qty. * Net Cons Per Pcs
									$reject_kg=$qc_qty_arr[$color_ids][$order_ids][$cutting_ids]['reject_qty']*$net_cons_per_pcs;
									//Total Reject Fab. Qty. / Lay Fabric weight *100
									$reject_percn=$reject_kg/$roll_wgt_arr[$cutting_ids]*100;
									// New Lay Fabric Weight Kg
									
									

									?>
									<tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $sl;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')">
										<td width="50"><?php echo $sl; ?></td>
										<td width="70"><p><?php echo change_date_format($cutting_vals['cuting_date']); ?></p></td>
										<td width="100"><p><a href="#" onClick="generate_report_lay_chart('<?php echo $cutting_vals['cutting_no']."*".$job_ids; ?>')"><?php echo $cutting_vals['cutting_no']; ?></a></p></td>
										<td width="100"><p><?php echo $floor_arr[$cutting_vals['floor_id']]; ?></p></td>
										<td width="100" title="<?=$color_ids;?>"><p><?php echo $color_library[$color_ids]; ?></p></td>
										<td width="60"><p><?php echo $table_arr[$cutting_vals['table_no']]; ?></p></td>
										<td width="100"><p><?php echo $buyer_arr[$cutting_vals['buyer_name']]; ?></p></td><!--Buyer Name-->
										<td width="100"><p><?php echo $cutting_vals['job_no']; ?></p></td><!--Job No-->
										<td width="60"><p><?php echo $cutting_vals['style_ref_no']; ?></p></td><!--Style Reff-->
										<td width="100"><p><?php echo $cutting_vals['grouping']; ?></p></td><!--Internal Ref Number-->
										<td width="60"><p><?php echo $cutting_vals['file_no']; ?></p></td><!--File No-->
										<td width="60"><p><?php echo $cutting_vals['batch_id']; ?></p></td><!--Batch No-->
										<td width="60" title="<?=$order_ids;?>"><p><?php echo $cutting_vals['po_number']; ?></p></td><!--Order No-->
	                                   
	                                    <?
										//print_r($color_check_arr);
										if(in_array($color_ids,$color_check_arr))
										{
											?>	
											<td width="60" align="right"><p><?php //echo fn_number_format($fin_qty, 2); ?></p></td><!--Fini. Req. Qty.-->
											<td width="60" align="right"><p><?php //echo fn_number_format($fin_rcv_qty_arr[$order_ids][$color_ids], 2); ?></p></td>
											<td width="60"></td>
											<td width="60"></td>
											<td width="60" align="right"><p><?php //echo $order_dtls_arr[$order_ids][$color_ids]['rmg_color_qty']; ?></p></td><!--RMG Color Qty-->
											<td width="60" align="right"><p><?php //echo $order_dtls_arr[$order_ids][$color_ids]['plan_cut_qnty']; ?></p></td><!--Plan Cut Qty (Color)-->
		                                    <td width="60" align="right"><p><?php //echo $yet_to_cut; ?></p></td><!--Yet To Cut-->	
											<?	
										}
										else
										{
											$total_fini_req_qty			+= $fin_qty;
											$total_fini_rcv_qty			+= $fin_rcv_qty_arr[$order_ids][$color_ids];
											$total_fini_issue_qty		+= $fin_issue_qty_arr[$order_ids][$color_ids];
											$total_balance 				+= $fin_rcv_qty_arr[$order_ids][$color_ids] - $fin_issue_qty_arr[$order_ids][$color_ids];
											$total_rmg_color_qty		+= $order_dtls_arr[$order_ids][$color_ids]['rmg_color_qty'];
											$total_plan_cut_qty			+= $order_dtls_arr[$order_ids][$color_ids]['plan_cut_qnty'];
											?>
		                                    
											<td width="60" align="right"><p><?php echo fn_number_format($fin_qty, 2); ?></p></td><!--Fini. Req. Qty.-->
											<td width="60" align="right"><p><?php echo fn_number_format($fin_rcv_qty_arr[$order_ids][$color_ids], 2); ?></p></td>
											<td width="60" align="right"><p><?php echo fn_number_format($fin_issue_qty_arr[$order_ids][$color_ids], 2); ?></p></td><!--Fini. Rcvd. Qty.-->
											<td width="60" align="right"><p><?php echo fn_number_format(($fin_issue_qty_arr[$order_ids][$color_ids] - $fin_rcv_qty_arr[$order_ids][$color_ids]), 2); ?></p></td><!--Fini. Rcvd. Qty.-->
											<td width="60" align="right"><p><?php echo $order_dtls_arr[$order_ids][$color_ids]['rmg_color_qty']; ?></p></td><!--RMG Color Qty-->
											<td width="60" align="right"><p><?php echo $order_dtls_arr[$order_ids][$color_ids]['plan_cut_qnty']; ?></p></td><!--Plan Cut Qty (Color)-->
		                                    <td width="60" align="right" title="(Plan Cut Qty (Color)) - (Marker qty)"><p><?php echo $order_dtls_arr[$order_ids][$color_ids]['plan_cut_qnty']-$subtotal_marker_qty[$job_ids][$order_ids][$color_ids]['marker_qty']; ?></p></td><!--Yet To Cut-->
		                                    <?
										}
										?>
										<td width="60" align="right"><p><?php echo fn_number_format($roll_wgt_arr[$cutting_ids]*( $cutting_vals['marker_qty']/ $cutting_vals['total_marker_qty']), 2); ?></p></td><!--Lay Fabric Weight (Kg)-->
										<td width="60" align="right"><p><?php echo fn_number_format($cutting_vals['cad_marker_cons']/12, 4); ?></p></td><!--CAD Marker Cons/Pcs-->
										<td width="60" align="right"><p><?php echo $cutting_vals['marker_qty']; ?></p></td><!--Marker Qty.-->
										<td width="60" align="right"><p><?php echo $qc_qty_arr[$color_ids][$order_ids][$cutting_ids]['qc_pass_qty']; ?></p></td><!--QC Pass Qty.-->
										<td width="60" align="right"><p><?php echo $qc_qty_arr[$color_ids][$order_ids][$cutting_ids]['replace_qty']; ?></p></td><!--Replace Qty.-->
										<td width="60" align="right"><p><?php echo $qc_qty_arr[$color_ids][$order_ids][$cutting_ids]['reject_qty']; ?></p></td><!--Reject Qty.-->

										<td width="60" align="right" title="(Lay Fabric Weight (Kg)) / (Marker Qty)"><p><?php echo fn_number_format($net_cons_per_pcs, 4); ?></p></td><!--Net Cons. Per Pcs-->
										<td width="60" align="right" title="((Replace Qty * Marker Cons Per pcs) + Lay Fabric Weight (Kg)) / QC pass qty"><p><?php echo fn_number_format($qc_pass_cons_qty, 4); ?></p></td><!--QC Pass Cons. Qty.-->
										<td width="60" align="right" title="QC Pass Cons Qty - Net Cons per Pcs"><p><?php echo fn_number_format($cons_variation_qty, 4); ?></p></td><!--Cons. Variation Qty.-->
										<td width="60" align="right" title="Cons Variation Qty /  QC Pass Cons Qty * 100"><p><?php echo fn_number_format($cons_variation_percn, 2); ?></p></td><!--Cons. Variation %-->
										<td width="60" align="right" title="Reject Qty. * Net Cons Per Pcs"><p><?php echo fn_number_format($reject_kg, 2); ?></p></td><!--Reject(Kg)-->
										<td align="right" title="Total Reject Fab Qty / Lay Fabric weight (kg)*100"><p><?php echo fn_number_format($reject_percn, 2); ?></p></td><!--Reject(%)-->
									</tr>
									<?php 
									$total_yet_to_cut			+= $yet_to_cut;	
									$total_lay_fabric_weight	+= $roll_wgt_arr[$cutting_ids]*( $cutting_vals['marker_qty']/ $cutting_vals['total_marker_qty']);
									$total_cad_marker_cons		+= $cutting_vals['cad_marker_cons']/12;
									$total_marker_qty			+= $cutting_vals['marker_qty'];
									$total_qc_pass_qty			+= $qc_qty_arr[$color_ids][$order_ids][$cutting_ids]['qc_pass_qty'];
									$total_replace_qty			+= $qc_qty_arr[$color_ids][$order_ids][$cutting_ids]['replace_qty'];
									$total_reject_qty			+= $qc_qty_arr[$color_ids][$order_ids][$cutting_ids]['reject_qty'];
									$total_cut_cons_qty			+= $net_cons_per_pcs;
									$total_qc_pass_cons_qty		+= $qc_pass_cons_qty;
									$total_cons_variation_qty	+= $cons_variation_qty;
									$total_cons_variation_percn	+= $cons_variation_percn;
									$total_reject_kg			+= $reject_kg;
									$total_reject_percn			+= $reject_percn;

									$color_check_arr[]=$color_ids;

								}
								// if(!in_array($color_ids,$color_subtot_arr))
								// {
									?>
									
									<?php
									$grand_total_fini_req_qty		+= $total_fini_req_qty;
									$grand_total_fini_rcv_qty		+= $total_fini_rcv_qty;
									$grand_total_fini_issue_qty		+= $total_fini_issue_qty;
									$grand_total_balance			+= $total_balance;
									$grand_total_rmg_color_qty		+= $total_rmg_color_qty;
									$grand_total_plan_cut_qty		+= $total_plan_cut_qty;
									$grand_total_yet_to_cut			+= $total_yet_to_cut;
									$grand_total_lay_fabric_weight	+= $total_lay_fabric_weight;
									$grand_total_cad_marker_cons	+= $total_cad_marker_cons;
									$grand_total_marker_qty			+= $total_marker_qty;
									$grand_total_qc_pass_qty		+= $total_qc_pass_qty;
									$grand_total_replace_qty		+= $total_replace_qty;
									$grand_total_reject_qty			+= $total_reject_qty;
									$grand_total_cut_cons_qty		+= $total_cut_cons_qty;
									$grand_total_qc_pass_cons_qty	+= $total_qc_pass_cons_qty;
									$grand_total_cons_variation_qty	+= $total_cons_variation_qty;
									$grand_total_cons_variation_percn+= $total_cons_variation_percn;
									$grand_total_reject_kg			+= $total_reject_kg;
									$grand_total_reject_percn		+= $total_reject_percn;
								// }
								$color_subtot_arr[]=$color_ids;
								unset($color_check_arr);
							}
							
						}
					}
					?>
	            </tbody>
	            <tfoot>            	
	                <tr> 
	                    <th colspan="13" align="right"><strong>Job Total=</strong></th>
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_fini_req_qty, 2); ?></strong></p></th><!--Fini. Req. Qty.-->
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_fini_rcv_qty, 2); ?></strong></p></th><!--Fini. Rcvd. Qty.-->
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_fini_issue_qty, 2); ?></strong></p></th><!--Fini. Rcvd. Qty.-->
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_balance, 2); ?></strong></p></th><!--Fini. Rcvd. Qty.-->
	                    <th align="right"><p><strong><?php echo $grand_total_rmg_color_qty; ?></strong></p></th><!--RMG Color Qty-->
	                    <th align="right"><p><strong><?php echo $grand_total_plan_cut_qty; ?></strong></p></th><!--Plan Cut Qty (Color)-->
	                    <th align="right"><p><strong><?php echo $grand_total_yet_to_cut; ?></strong></p></th><!--Yet To Cut-->
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_lay_fabric_weight, 2); ?></strong></p></th><!--Lay Fabric Weight (Kg)-->
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_cad_marker_cons, 4); ?></strong></p></th><!--CAD Marker Cons/Pcs-->
	                    <th align="right"><p><strong><?php echo $grand_total_marker_qty; ?></strong></p></th><!--Marker Qty.-->
	                    <th align="right"><p><strong><?php echo $grand_total_qc_pass_qty; ?></strong></p></th><!--QC Pass Qty.-->
	                    <th align="right"><p><strong><?php echo $grand_total_replace_qty; ?></strong></p></th><!--Replace Qty.-->
	                    <th align="right"><p><strong><?php echo $grand_total_reject_qty; ?></strong></p></th><!--Reject Qty.-->
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_cut_cons_qty, 4); ?></strong></p></th><!--Net Cons. Per Pcs-->
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_qc_pass_cons_qty, 4); ?></strong></p></th><!--QC Pass Cons. Qty.-->
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_cons_variation_qty, 4); ?></strong></p></th><!--Cons. Variation Qty.-->
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_cons_variation_percn, 2); ?></strong></p></th><!--Cons. Variation %-->
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_reject_kg, 2); ?></strong></p></th><!--Reject(Kg)-->
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_reject_percn, 2); ?></strong></p></th><!--Reject(%)-->
	                </tr>
	            </tfoot>
	        </table>
	    </div>
		<?php
	}

	if($reportType==5) // show5 button
	{
	
		//id cutting_no table_no job_no entry_date
		$company_name	== 0  ? $company_name 	= "" : $company_name 	= "AND a.company_id	= '".$company_name."'";
		$wo_company_name== 0  ? $wo_company_name= "" : $wo_company_name = "AND a.working_company_id	= '".$wo_company_name."'";
		$location_name	== 0  ? $location_name 	= "" : $location_name 	= "AND a.location_id	= '".$location_name."'";
		$floor_id		== 0  ? $floor_id		= "" : $floor_id		= "AND a.floor_id='".$floor_id."'";
		$buyer_name		== 0  ? $buyer_name		= "" : $buyer_name		= "AND c.buyer_name='".$buyer_name."'";
		$job_no			== "" ? $job_no			= "" : $job_no			= "AND c.job_no_prefix_num='".$job_no."'";
		$file_no		== "" ? $file_no		= "" : $file_no			= "AND d.file_no='".$file_no."'";
		$order_no		== "" ? $order_no		= "" : $order_no		= "AND d.po_number='".$order_no."'";
		$cutting_no		== "" ? $cutting_no		= "" : $cutting_no		= "AND a.cut_num_prefix_no='".$cutting_no."'";
		$internal_ref_no== "" ? $internal_ref_no= "" : $internal_ref_no	= "AND d.grouping='".$internal_ref_no."'";
		$table_no		== "" ? $table_no		= "" : $table_no		= "AND a.table_no='".$table_no."'";
		$from_date		!= "" && $to_date	   != "" ? $cutting_date	= "AND a.entry_date between $txt_date_from AND $txt_date_to" : $cutting_date="and to_char(a.entry_date,'YYYY')='$year'";


		//main query============	
		$sql=sql_select("SELECT a.id as cutid, a.cutting_no, a.table_no, a.job_no, a.batch_id, a.lay_fabric_wght, a.cad_marker_cons, a.entry_date, a.floor_id,a.remarks, b.order_cut_no, d.id as order_id, b.color_id, SUM(e.size_qty) AS marker_qty,b.marker_qty AS total_marker_qty, c.buyer_name, c.style_ref_no, d.po_number, d.file_no, d.grouping
		FROM ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_bundle e, wo_po_details_master c, wo_po_break_down d 
		WHERE a.id=b.mst_id and b.id=e.dtls_id   AND a.job_no=c.job_no AND c.id=d.job_id AND e.order_id=d.id $company_name $wo_company_name $buyer_name $job_no $file_no $order_no $cutting_no $table_no $cutting_date $floor_id $internal_ref_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and c.status_active=1 and d.status_active=1
		GROUP BY a.id, a.cutting_no, a.table_no, a.job_no, a.batch_id, a.lay_fabric_wght, a.cad_marker_cons, a.entry_date, a.floor_id, a.remarks,b.order_cut_no, d.id, b.color_id, c.buyer_name, c.style_ref_no, d.po_number, d.file_no, d.grouping,b.marker_qty");
		
	    //  echo "<pre>";
		// print_r($sql);

		//===================

		if (count($sql)==0) 
		{
			echo "<div style='color:red;text-align:center;'>Data not Found..<div/>";die;
		}

		$job_arr=array();
		$po_id_arr=array();
		$cut_no_arr=array();
		foreach($sql as $job_for_class){
			$job_arr[$job_for_class[csf('job_no')]]=$job_for_class[csf('job_no')];
			$po_id_arr[$job_for_class[csf('order_id')]]=$job_for_class[csf('order_id')];
			$cut_no_arr[$job_for_class[csf('cutid')]]=$job_for_class[csf('cutid')];
		}
		// print_r($po_id_arr);die();
		
		$con = connect();
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (7,8) and ENTRY_FORM=20");
		oci_commit($con);
		
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 20, 7, $po_id_arr, $empty_arr);//PO ID
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 20, 8, $cut_no_arr, $empty_arr);//CUT ID
		disconnect($con);
		
		$allPoIds = implode(",", $po_id_arr); 
		$condition= new condition();     
	    $condition->po_id_in($allPoIds);     
	    $condition->init();
	    $fabric= new fabric($condition);
	    // echo $fabric->getQuery();
		// $fabric=new fabric($job_arr,'job');
		$fab_data=$fabric->getQtyArray_by_orderGmtscolorAndBodypart_knitAndwoven_greyAndfinish();
		// echo "<pre>";print_r($fab_data);echo "</pre>";
		//==============================
		
		$job_order_color_arr=array();
		$subtotal_marker_qty=array();
		foreach($sql as $row)
		{
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['cuting_date'] 	= $row[csf('entry_date')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['cutting_no']		= $row[csf('cutting_no')];
			
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['table_no']		= $row[csf('table_no')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['order_id']		= $row[csf('order_id')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['job_no']			= $row[csf('job_no')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['batch_id']		= $row[csf('batch_id')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['buyer_name']		= $row[csf('buyer_name')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['file_no']			= $row[csf('file_no')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['po_number']		= $row[csf('po_number')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['style_ref_no']	= $row[csf('style_ref_no')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['lay_fabric_wght']	= $row[csf('lay_fabric_wght')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['cad_marker_cons']	= $row[csf('cad_marker_cons')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['marker_qty']	 	+= $row[csf('marker_qty')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['total_marker_qty']	 	+= $row[csf('total_marker_qty')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['floor_id']	 	 = $row[csf('floor_id')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['grouping']	 	 = $row[csf('grouping')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['remarks']	 	 = $row[csf('remarks')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['order_cut_no']	 	 .= $row[csf('order_cut_no')].",";
			$subtotal_marker_qty[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]]['marker_qty'] += $row[csf('marker_qty')];
			$total_marker_qty +=$subtotal_marker_qty[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]]['marker_qty'];
			
		}
	    //   echo "<pre>";
		//     print_r($total_job_order_color_arr);
		// print_r($job_order_color_arr);
		// echo $sql;

		// =================================== getting roll w8 ===============================
		$sql = "SELECT a.CUTTING_NO,b.ROLL_ID,b.ROLL_WGT from ppl_cut_lay_mst a,ppl_cut_lay_roll_dtls b, gbl_temp_engine d where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=20 and d.ref_from=8";
		// echo $sql;
		$res = sql_select($sql);
		$roll_wgt_arr = array();
		$roll_id_chk_arr = array();
		foreach ($res as $val) 
		{
			if(!in_array($val['ROLL_ID'], $roll_id_chk_arr))
			{
				$roll_wgt_arr[$val['CUTTING_NO']] += $val['ROLL_WGT'];
				$roll_id_chk_arr[$val['ROLL_ID']] = $val['ROLL_ID'];
			}
		}
		
		
		//   echo "<pre>"; print_r($roll_wgt_arr);die();

		//wo_po_color_size_breakdown
		$sql_order_dtls=sql_select("SELECT a.po_break_down_id, a.color_number_id, (a.plan_cut_qnty) AS plan_cut_qnty, (a.order_quantity) AS rmg_color_qty FROM wo_po_color_size_breakdown a, gbl_temp_engine d WHERE status_active=1 AND is_deleted=0 and a.po_break_down_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=20 and d.ref_from=7");
		$order_dtls_arr=array();
		foreach($sql_order_dtls as $row)
		{
			$order_dtls_arr[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]]['plan_cut_qnty'] += $row[csf('plan_cut_qnty')];
			$order_dtls_arr[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]]['rmg_color_qty'] += $row[csf('rmg_color_qty')];
		}
		//print_r($order_dtls_arr);die;

		// ========================= fin fab rcv, issue ========================
		$sql2=sql_select("SELECT a.po_breakdown_id as PO_BREAKDOWN_ID, a.color_id as COLOR_ID, a.quantity as QUANTITY, a.entry_form as ENTRY_FORM FROM order_wise_pro_details a, gbl_temp_engine d WHERE a.entry_form in(7,18,37) AND a.trans_id>0 and a.po_breakdown_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=20 and d.ref_from=7");
		$fin_rcv_qty_arr=array(); $fin_issue_qty_arr=array();
		foreach($sql2 as $row)
		{
			if($row['ENTRY_FORM']==7 || $row['ENTRY_FORM']==37)
			{
				$fin_rcv_qty_arr[$row['PO_BREAKDOWN_ID']][$row['COLOR_ID']]+=$row['QUANTITY'];
			}
			if($row['ENTRY_FORM']==18)
			{
				$fin_issue_qty_arr[$row['PO_BREAKDOWN_ID']][$row['COLOR_ID']]+=$row['QUANTITY'];
			}
		}


		// $sql5=sql_select("SELECT order_id, color_id, SUM(reject_qty) AS reject_qty, SUM(qc_pass_qty) AS qc_pass_qty, SUM(replace_qty) AS replace_qty FROM pro_gmts_cutting_qc_dtls where status_active=1 and is_deleted=0 and order_id in($po_ids) GROUP BY order_id, color_id");
		// $qc_qty_arr=array();
		// foreach($sql5 as $row)
		// {
		// 	$qc_qty_arr[$row[csf('color_id')]][$row[csf('order_id')]]['reject_qty']	= $row[csf('reject_qty')];
		// 	$qc_qty_arr[$row[csf('color_id')]][$row[csf('order_id')]]['qc_pass_qty']= $row[csf('qc_pass_qty')];
		// 	$qc_qty_arr[$row[csf('color_id')]][$row[csf('order_id')]]['replace_qty']= $row[csf('replace_qty')];
		// }
		
		$sql5=sql_select("SELECT a.order_id, a.color_id, a.mst_id, b.cutting_no, (a.reject_qty) AS reject_qty, (a.qc_pass_qty) AS qc_pass_qty, (a.replace_qty) AS replace_qty
								FROM pro_gmts_cutting_qc_dtls a, pro_gmts_cutting_qc_mst b, gbl_temp_engine d
								WHERE a.mst_id=b.id AND a.status_active = 1 AND a.is_deleted = 0 and a.order_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=20 and d.ref_from=7");
		$qc_qty_arr=array();
		foreach($sql5 as $row)
		{
			$qc_qty_arr[$row[csf('color_id')]][$row[csf('order_id')]][$row[csf('cutting_no')]]['reject_qty'] += $row[csf('reject_qty')];
			$qc_qty_arr[$row[csf('color_id')]][$row[csf('order_id')]][$row[csf('cutting_no')]]['qc_pass_qty']+= $row[csf('qc_pass_qty')];
			$qc_qty_arr[$row[csf('color_id')]][$row[csf('order_id')]][$row[csf('cutting_no')]]['replace_qty']+= $row[csf('replace_qty')];
		}
		
		$con = connect();
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (7,8) and ENTRY_FORM=20");
		oci_commit($con);
		disconnect($con);
		
		// echo"<pre>";
		// print_r($qc_qty_arr);
		
		ob_start();
		?>
	    <table class="rpt_table" width="2480" cellpadding="0" cellspacing="0" border="1" rules="all">
	        <caption style="font-size:20px; font-weight:bold;">
				<?php 
					$com_name = str_replace( "'", "", $cbo_company_name );
	                echo $company_arr[$com_name]."<br/>"."Cutting Lay Production Report";
	            ?>
	            <div style="color:red; text-align:left; font-size:16px;">Group By Job, PO and Color</div>
	        </caption>
	        <thead>
	            <tr>
	                <th width="50">Sl</th>
	                <th width="70">Cutting Date</th>
	                <th width="100">Cutting No.</th>
	                <th width="100">Cutting Floor</th>
	                <th width="100">Color Name</th>
	                <th width="60">Table No</th>
	                <th width="100">Buyer Name</th>
	                <th width="100">Job No</th>
	                <th width="60">Style Reff</th>
	                <th width="100">Internal Ref. Number</th>
	                <th width="100">Job Cut NO</th>
	                <th width="100">Remarks</th>
	                <th width="60">File No</th>
	                <th width="60">Batch No</th>
	                <th width="60">PO No</th>
	                <th width="60">Fabric Require Qty</th>
	                <th width="60">Fabric QC Pass Qty</th>
	                <th width="60">Fini. Issue Qty.</th>
	                <th width="60">Balance</th>
	                <th width="60">Color Qty</th>
	                <th width="60">Plan Cut Qty (Color)</th>
	                <th width="60">Cut Balance</th>
	                <th width="60">Lay Fabric Weight (Kg)</th>
	                <th width="60">CAD Marker Cons/Pcs</th>
	                <th width="60">Total Cutting Qty.</th>
	                <th width="60">Final Cutting</th>
	                <th width="60">Replace Qty.</th>
	                <th width="60">Reject Qty.</th>
	                <th width="60">Net Cons/Pcs</th>
	                <th width="60">QC Pass Cons. Qty.</th>
	                <th width="60">Final Cutting %</th>
	                <th width="60">Cons. Variation Qty.</th>
	                <th width="60">Cons. Variation (%)</th>
	                <th width="60">Total Rej. Fab. Qty. (Kg)</th>
	                <th>Total Rej. Fab. Qty. (%)</th>
	            </tr>
	        </thead>
	    </table>
	    <div style=" max-height:350px; width:2480px; overflow-y:scroll;" id="scroll_body">
	        <table class="rpt_table" id="table_body" width="2460" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
	            <tbody>
	            	<?php 
					$sl=0;
					$color_subtot_arr=array();
					$grand_total_fini_req_qty			= 0;
					$grand_total_fini_rcv_qty			= 0;
					$grand_total_rmg_color_qty			= 0;
					$grand_total_plan_cut_qty			= 0;
					$grand_total_yet_to_cut				= 0;
					$grand_total_lay_fabric_weight		= 0;
					$grand_total_cad_marker_cons		= 0;
					$grand_total_marker_qty				= 0;
					$grand_total_qc_pass_qty			= 0;
					$grand_total_replace_qty			= 0;
					$grand_total_reject_qty				= 0;
					$grand_total_cut_cons_qty			= 0;
					$grand_total_qc_pass_cons_qty		= 0;
					$grand_total_cons_variation_qty		= 0;
					$grand_total_cons_variation_percn	= 0;
					$grand_total_reject_kg				= 0;
					$grand_total_reject_percn			= 0;

					foreach($job_order_color_arr as $job_ids=>$job_vals)
					{
						foreach($job_vals as $order_ids=>$order_vals)
						{
							//$color_subtot_arr['job_ids']=$job_ids;
							foreach($order_vals as $color_ids=>$color_vals)
							{							
								$total_fini_req_qty			= 0;
								$total_fini_rcv_qty			= 0;
								$total_fini_issue_qty		= 0;
								$total_balance				= 0;
								$total_rmg_color_qty		= 0;
								$total_plan_cut_qty			= 0;
								$total_yet_to_cut			= 0;
								$total_lay_fabric_weight	= 0;
								$total_cad_marker_cons		= 0;
								$total_marker_qty			= 0;
								$total_qc_pass_qty			= 0;
								$total_replace_qty			= 0;
								$total_reject_qty			= 0;
								$total_cut_cons_qty			= 0;
								$total_qc_pass_cons_qty		= 0;
								$total_cons_variation_qty	= 0;
								$total_cons_variation_percn	= 0;
								$total_reject_kg			= 0;
								$total_reject_percn			= 0;
								$total_final_cutting		= 0;

								foreach($color_vals as $cutting_ids=>$cutting_vals)
								{
									$sl++;
									$bgcolor=($sl%2==0)?"#E9F3FF":"#FFFFFF";
									$fin_qty=array_sum($fab_data['knit']['finish'][$order_ids][$color_ids][1])+array_sum($fab_data['knit']['finish'][$order_ids][$color_ids][20])+array_sum($fab_data['knit']['finish'][$order_ids][$color_ids][125]);//1,20,125
									//Plan Cut Qty - sum of  Marker Qty.
									$yet_to_cut=$order_dtls_arr[$order_ids][$color_ids]['plan_cut_qnty'] - $color_vals['marker_qty'];
									// Lay Fabric Weight / Marker Qty.
									$net_cons_per_pcs=$roll_wgt_arr[$cutting_ids]/$cutting_vals['marker_qty'];
									//qc_pass_cons_qty = ((Replace Qty * Marker Cons. Per pcs) + Lay Fabric Weight)/QC pass qty.

									$qc_pass_cons_qty=(($qc_qty_arr[$color_ids][$order_ids]['replace_qty']*$net_cons_per_pcs)+$roll_wgt_arr[$cutting_ids])/$qc_qty_arr[$color_ids][$order_ids][$cutting_ids]['qc_pass_qty'];
									// echo $qc_qty_arr[$color_ids][$order_ids]['replace_qty']."*".$net_cons_per_pcs."+".$roll_wgt_arr[$cutting_ids]."/".$qc_qty_arr[$color_ids][$order_ids][$cutting_ids]['qc_pass_qty']."<br>";

									//cons_variation_qty=QC pass Consum - Net Cons per Pcs
									$cons_variation_qty=$qc_pass_cons_qty-$net_cons_per_pcs;
									//Cons. Variation / QC pass cons. * 100
									$cons_variation_percn=$cons_variation_qty/$qc_pass_cons_qty*100;
									//Reject Qty. * Net Cons Per Pcs
									$reject_kg=$qc_qty_arr[$color_ids][$order_ids][$cutting_ids]['reject_qty']*$net_cons_per_pcs;
									//Total Reject Fab. Qty. / Lay Fabric weight *100
									$reject_percn=$reject_kg/$roll_wgt_arr[$cutting_ids]*100;
									// New Lay Fabric Weight Kg
									
									

									?>
									<tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $sl;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')">
										<td width="50"><?php echo $sl; ?></td>
										<td width="70"><p><?php echo change_date_format($cutting_vals['cuting_date']); ?></p></td>
										<td width="100"><p><a href="#" onClick="generate_report_lay_chart('<?php echo $cutting_vals['cutting_no']."*".$job_ids; ?>')"><?php echo $cutting_vals['cutting_no']; ?></a></p></td>
										<td width="100"><p><?php echo $floor_arr[$cutting_vals['floor_id']]; ?></p></td>
										<td width="100" title="<?=$color_ids;?>"><p><?php echo $color_library[$color_ids]; ?></p></td>
										<td width="60"><p><?php echo $table_arr[$cutting_vals['table_no']]; ?></p></td>
										<td width="100"><p><?php echo $buyer_arr[$cutting_vals['buyer_name']]; ?></p></td><!--Buyer Name-->
										<td width="100"><p><?php echo $cutting_vals['job_no']; ?></p></td><!--Job No-->
										<td width="60"><p><?php echo $cutting_vals['style_ref_no']; ?></p></td><!--Style Reff-->
										<td width="100"><p><?php echo $cutting_vals['grouping']; ?></p></td><!--Internal Ref Number-->
										<td width="100"><p><?php echo chop($cutting_vals['order_cut_no'],',');?></p></td><!--JOB NO  CUT-->
										<td width="100"><p><?php echo $cutting_vals['remarks'];?></p></td><!--Remarks No-->
										<td width="60"><p><?php echo $cutting_vals['file_no']; ?></p></td><!--File No-->
										<td width="60"><p><?php echo $cutting_vals['batch_id']; ?></p></td><!--Batch No-->
										<td width="60" title="<?=$order_ids;?>"><p><?php echo $cutting_vals['po_number']; ?></p></td><!--Order No-->
	                                   
	                                    <?
										//print_r($color_check_arr);
										if(in_array($color_ids,$color_check_arr))
										{
											?>	
											<td width="60" align="right"><p><?php //echo fn_number_format($fin_qty, 2); ?></p></td><!--Fini. Req. Qty.-->
											<td width="60" align="right"><p><?php //echo fn_number_format($fin_rcv_qty_arr[$order_ids][$color_ids], 2); ?></p></td>
											<td width="60"></td>
											<td width="60"></td>
											<td width="60" align="right"><p><?php //echo $order_dtls_arr[$order_ids][$color_ids]['rmg_color_qty']; ?></p></td><!--RMG Color Qty-->
											<td width="60" align="right"><p><?php //echo $order_dtls_arr[$order_ids][$color_ids]['plan_cut_qnty']; ?></p></td><!--Plan Cut Qty (Color)-->
		                                    <td width="60" align="right"><p><?php //echo $yet_to_cut; ?></p></td><!--Yet To Cut-->	
											<?	
										}
										else
										{
											$total_fini_req_qty			+= $fin_qty;
											$total_fini_rcv_qty			+= $fin_rcv_qty_arr[$order_ids][$color_ids];
											$total_fini_issue_qty		+= $fin_issue_qty_arr[$order_ids][$color_ids];
											$total_balance 				+= $fin_rcv_qty_arr[$order_ids][$color_ids] - $fin_issue_qty_arr[$order_ids][$color_ids];
											$total_rmg_color_qty		+= $order_dtls_arr[$order_ids][$color_ids]['rmg_color_qty'];
											$total_plan_cut_qty			+= $order_dtls_arr[$order_ids][$color_ids]['plan_cut_qnty'];
											?>
		                                    
											<td width="60" align="right"><p><?php echo fn_number_format($fin_qty, 2); ?></p></td><!--Fini. Req. Qty.-->
											<td width="60" align="right"><p><?php echo fn_number_format($fin_rcv_qty_arr[$order_ids][$color_ids], 2); ?></p></td><!--Fini. Rcvd. Qty.-->
											<td width="60" align="right"><p><?php echo fn_number_format($fin_issue_qty_arr[$order_ids][$color_ids], 2); ?></p></td><!--Fini. Rcvd. Qty.-->
											<td width="60" align="right"><p><?php echo fn_number_format(($fin_issue_qty_arr[$order_ids][$color_ids] - $fin_rcv_qty_arr[$order_ids][$color_ids]), 2); ?></p></td><!--Fini. Rcvd. Qty.-->
											<td width="60" align="right"><p><?php echo $order_dtls_arr[$order_ids][$color_ids]['rmg_color_qty']; ?></p></td><!--RMG Color Qty-->
											<td width="60" align="right"><p><?php echo $order_dtls_arr[$order_ids][$color_ids]['plan_cut_qnty']; ?></p></td><!--Plan Cut Qty (Color)-->
		                                    <td width="60" align="right" title="(Plan Cut Qty (Color)) - (Marker qty)"><p><?php echo $order_dtls_arr[$order_ids][$color_ids]['plan_cut_qnty']-$subtotal_marker_qty[$job_ids][$order_ids][$color_ids]['marker_qty']; ?></p></td><!--Yet To Cut-->
		                                    <?
										}
										?>
										<td width="60" align="right"><p><?php echo fn_number_format($roll_wgt_arr[$cutting_ids]*( $cutting_vals['marker_qty']/ $cutting_vals['total_marker_qty']), 2); ?></p></td><!--Lay Fabric Weight (Kg)-->
										<td width="60" align="right"><p><?php echo fn_number_format($cutting_vals['cad_marker_cons']/12, 4); ?></p></td><!--CAD Marker Cons/Pcs-->
										<td width="60" align="right"><p><?php echo $cutting_vals['marker_qty']; ?></p></td><!--Marker Qty.-->
										<td width="60" align="right"><p><?php echo $qc_qty_arr[$color_ids][$order_ids][$cutting_ids]['qc_pass_qty']; ?></p></td><!--QC Pass Qty.-->
										<td width="60" align="right"><p><?php echo $qc_qty_arr[$color_ids][$order_ids][$cutting_ids]['replace_qty']; ?></p></td><!--Replace Qty.-->
										<td width="60" align="right"><p><?php echo $qc_qty_arr[$color_ids][$order_ids][$cutting_ids]['reject_qty']; ?></p></td><!--Reject Qty.-->

										<td width="60" align="right" title="(Lay Fabric Weight (Kg)) / (Marker Qty)"><p><?php echo fn_number_format($net_cons_per_pcs, 4); ?></p></td><!--Net Cons. Per Pcs-->
										<td width="60" align="right" title="((Replace Qty * Marker Cons Per pcs) + Lay Fabric Weight (Kg)) / QC pass qty"><p><?php echo fn_number_format($qc_pass_cons_qty, 4); ?></p></td><!--QC Pass Cons. Qty.-->
										<td width="60" align="right" title="Final Cutting/Order Qty * 100"><p><?php
										$final_cutting = $qc_qty_arr[$color_ids][$order_ids][$cutting_ids]['qc_pass_qty'];
										$order_qtyP= $order_dtls_arr[$order_ids][$color_ids]['rmg_color_qty'];
										$final_cutting = ($final_cutting/$order_qtyP)*100;
										echo fn_number_format($final_cutting, 2)."%"; ?></p></td><!--Final Cutting %.-->
										<td width="60" align="right" title="QC Pass Cons Qty - Net Cons per Pcs"><p><?php echo fn_number_format($cons_variation_qty, 4); ?></p></td><!--Cons. Variation Qty.-->
										<td width="60" align="right" title="Cons Variation Qty /  QC Pass Cons Qty * 100"><p><?php echo fn_number_format($cons_variation_percn, 2); ?></p></td><!--Cons. Variation %-->
										<td width="60" align="right" title="Reject Qty. * Net Cons Per Pcs"><p><?php echo fn_number_format($reject_kg, 2); ?></p></td><!--Reject(Kg)-->
										<td align="right" title="Total Reject Fab Qty / Lay Fabric weight (kg)*100"><p><?php echo fn_number_format($reject_percn, 2); ?></p></td><!--Reject(%)-->
									</tr>
									<?php 
									$total_yet_to_cut			+= $yet_to_cut;	
									$total_lay_fabric_weight	+= $roll_wgt_arr[$cutting_ids]*( $cutting_vals['marker_qty']/ $cutting_vals['total_marker_qty']);
									$total_cad_marker_cons		+= $cutting_vals['cad_marker_cons']/12;
									$total_marker_qty			+= $cutting_vals['marker_qty'];
									$total_qc_pass_qty			+= $qc_qty_arr[$color_ids][$order_ids][$cutting_ids]['qc_pass_qty'];
									$total_replace_qty			+= $qc_qty_arr[$color_ids][$order_ids][$cutting_ids]['replace_qty'];
									$total_reject_qty			+= $qc_qty_arr[$color_ids][$order_ids][$cutting_ids]['reject_qty'];
									$total_cut_cons_qty			+= $net_cons_per_pcs;
									$total_qc_pass_cons_qty		+= $qc_pass_cons_qty;
									$total_cons_variation_qty	+= $cons_variation_qty;
									$total_cons_variation_percn	+= $cons_variation_percn;
									$total_reject_kg			+= $reject_kg;
									$total_reject_percn			+= $reject_percn;
									$total_final_cutting		+= $final_cutting;

									$color_check_arr[]=$color_ids;

								}
								// if(!in_array($color_ids,$color_subtot_arr))
								// {
									?>
									<tr bgcolor="#dccdcd"> 
										<td colspan="15" align="right"><strong>Color Qty Total=</strong></td>
										<td align="right"><p><strong><?php echo fn_number_format($total_fini_req_qty, 2); ?></strong></p></td><!--Fini. Req. Qty.-->
										<td align="right"><p><strong><?php echo fn_number_format($total_fini_rcv_qty, 2); ?></strong></p></td><!--Fini. Rcvd. Qty.-->
										<td align="right"><p><strong><?php echo fn_number_format($total_fini_issue_qty, 2); ?></strong></p></td><!--Fini. Rcvd. Qty.-->
										<td align="right"><p><strong><?php echo fn_number_format($total_balance, 2); ?></strong></p></td><!--Fini. Rcvd. Qty.-->
										<td align="right"><p><strong><?php echo $total_rmg_color_qty; ?></strong></p></td><!--RMG Color Qty-->
										<td align="right"><p><strong><?php echo $total_plan_cut_qty; ?></strong></p></td><!--Plan Cut Qty (Color)-->
										<td align="right"><p><strong><?php echo $total_yet_to_cut; ?></strong></p></td><!--Yet To Cut-->
										<td align="right"><p><strong><?php echo fn_number_format($total_lay_fabric_weight, 2); ?></strong></p></td><!--Lay Fabric Weight (Kg)-->
										<td align="right"><p><strong><?php echo fn_number_format($total_cad_marker_cons, 4); ?></strong></p></td><!--CAD Marker Cons/Pcs-->
										<td align="right"><p><strong><?php echo $total_marker_qty; ?></strong></p></td><!--Marker Qty.-->
										<td align="right"><p><strong><?php echo $total_qc_pass_qty; ?></strong></p></td><!--QC Pass Qty.-->
										<td align="right"><p><strong><?php echo $total_replace_qty; ?></strong></p></td><!--Replace Qty.-->
										<td align="right"><p><strong><?php echo $total_reject_qty; ?></strong></p></td><!--Reject Qty.-->
										<td align="right"><p><strong><?php echo fn_number_format($total_cut_cons_qty, 4); ?></strong></p></td><!--Net Cons. Per Pcs-->
										<td align="right"><p><strong><?php echo fn_number_format($total_qc_pass_cons_qty, 4); ?></strong></p></td><!--QC Pass Cons. Qty.-->
										<td align="right"><p><strong><?php echo fn_number_format($total_final_cutting, 4); ?></strong></p></td><!--total_final_cutting.-->
										<td align="right"><p><strong><?php echo fn_number_format($total_cons_variation_qty, 4); ?></strong></p></td><!--Cons. Variation Qty.-->
										<td align="right"><p><strong><?php echo fn_number_format($total_cons_variation_percn, 2); ?></strong></p></td><!--Cons. Variation %-->
										<td align="right"><p><strong><?php echo fn_number_format($total_reject_kg, 2); ?></strong></p></td><!--Reject(Kg)-->
										<td align="right"><p><strong><?php echo fn_number_format($total_reject_percn, 2); ?></strong></p></td><!--Reject(%)-->
									</tr>
									<?php
									$grand_total_fini_req_qty		+= $total_fini_req_qty;
									$grand_total_fini_rcv_qty		+= $total_fini_rcv_qty;
									$grand_total_fini_issue_qty		+= $total_fini_issue_qty;
									$grand_total_balance			+= $total_balance;
									$grand_total_rmg_color_qty		+= $total_rmg_color_qty;
									$grand_total_plan_cut_qty		+= $total_plan_cut_qty;
									$grand_total_yet_to_cut			+= $total_yet_to_cut;
									$grand_total_lay_fabric_weight	+= $total_lay_fabric_weight;
									$grand_total_cad_marker_cons	+= $total_cad_marker_cons;
									$grand_total_marker_qty			+= $total_marker_qty;
									$grand_total_qc_pass_qty		+= $total_qc_pass_qty;
									$grand_total_replace_qty		+= $total_replace_qty;
									$grand_total_reject_qty			+= $total_reject_qty;
									$grand_total_cut_cons_qty		+= $total_cut_cons_qty;
									$grand_total_qc_pass_cons_qty	+= $total_qc_pass_cons_qty;
									$grand_total_final_cutting		+= $total_final_cutting;
									$grand_total_cons_variation_qty	+= $total_cons_variation_qty;
									$grand_total_cons_variation_percn+= $total_cons_variation_percn;
									$grand_total_reject_kg			+= $total_reject_kg;
									$grand_total_reject_percn		+= $total_reject_percn;
								// }
								$color_subtot_arr[]=$color_ids;
								unset($color_check_arr);
							}
							
						}
					}
					?>
	            </tbody>
	            <tfoot>            	
	                <tr> 
	                    <th colspan="15" align="right"><strong>Job Total=</strong></th>
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_fini_req_qty, 2); ?></strong></p></th><!--Fini. Req. Qty.-->
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_fini_rcv_qty, 2); ?></strong></p></th><!--Fini. Rcvd. Qty.-->
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_fini_issue_qty, 2); ?></strong></p></th><!--Fini. Rcvd. Qty.-->
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_balance, 2); ?></strong></p></th><!--Fini. Rcvd. Qty.-->
	                    <th align="right"><p><strong><?php echo $grand_total_rmg_color_qty; ?></strong></p></th><!--RMG Color Qty-->
	                    <th align="right"><p><strong><?php echo $grand_total_plan_cut_qty; ?></strong></p></th><!--Plan Cut Qty (Color)-->
	                    <th align="right"><p><strong><?php echo $grand_total_yet_to_cut; ?></strong></p></th><!--Yet To Cut-->
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_lay_fabric_weight, 2); ?></strong></p></th><!--Lay Fabric Weight (Kg)-->
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_cad_marker_cons, 4); ?></strong></p></th><!--CAD Marker Cons/Pcs-->
	                    <th align="right"><p><strong><?php echo $grand_total_marker_qty; ?></strong></p></th><!--Marker Qty.-->
	                    <th align="right"><p><strong><?php echo $grand_total_qc_pass_qty; ?></strong></p></th><!--QC Pass Qty.-->
	                    <th align="right"><p><strong><?php echo $grand_total_replace_qty; ?></strong></p></th><!--Replace Qty.-->
	                    <th align="right"><p><strong><?php echo $grand_total_reject_qty; ?></strong></p></th><!--Reject Qty.-->
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_cut_cons_qty, 4); ?></strong></p></th><!--Net Cons. Per Pcs-->
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_qc_pass_cons_qty, 4); ?></strong></p></th><!--QC Pass Cons. Qty.-->
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_final_cutting, 4); ?></strong></p></th><!--total_final_cutting perce.-->
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_cons_variation_qty, 4); ?></strong></p></th><!--Cons. Variation Qty.-->
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_cons_variation_percn, 2); ?></strong></p></th><!--Cons. Variation %-->
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_reject_kg, 2); ?></strong></p></th><!--Reject(Kg)-->
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_reject_percn, 2); ?></strong></p></th><!--Reject(%)-->
	                </tr>
	            </tfoot>
	        </table>
	    </div>
		<?php
	}

	if($reportType==6) // show6 button
	{
	
		//id cutting_no table_no job_no entry_date
		$company_name	== 0  ? $company_name 	= "" : $company_name 	= "AND a.company_id	= '".$company_name."'";
		$wo_company_name== 0  ? $wo_company_name= "" : $wo_company_name = "AND a.working_company_id	= '".$wo_company_name."'";
		$location_name	== 0  ? $location_name 	= "" : $location_name 	= "AND a.location_id	= '".$location_name."'";
		$floor_id		== 0  ? $floor_id		= "" : $floor_id		= "AND a.floor_id='".$floor_id."'";
		$buyer_name		== 0  ? $buyer_name		= "" : $buyer_name		= "AND c.buyer_name='".$buyer_name."'";
		$job_no			== "" ? $job_no			= "" : $job_no			= "AND c.job_no_prefix_num='".$job_no."'";
		$file_no		== "" ? $file_no		= "" : $file_no			= "AND d.file_no='".$file_no."'";
		$order_no		== "" ? $order_no		= "" : $order_no		= "AND d.po_number='".$order_no."'";
		$cutting_no		== "" ? $cutting_no		= "" : $cutting_no		= "AND a.cut_num_prefix_no='".$cutting_no."'";
		$internal_ref_no== "" ? $internal_ref_no= "" : $internal_ref_no	= "AND d.grouping='".$internal_ref_no."'";
		$table_no		== "" ? $table_no		= "" : $table_no		= "AND a.table_no='".$table_no."'";
		$from_date		!= "" && $to_date	   != "" ? $cutting_date	= "AND a.entry_date between $txt_date_from AND $txt_date_to" : $cutting_date="and to_char(a.entry_date,'YYYY')='$year'";


		//main query============	
		$sql=sql_select("SELECT a.id as cutid, a.cutting_no, a.table_no, a.job_no, a.batch_id, a.lay_fabric_wght, a.cad_marker_cons, a.entry_date, a.floor_id, d.id as order_id, b.color_id, SUM(e.size_qty) AS marker_qty,b.marker_qty AS total_marker_qty, c.buyer_name, c.style_ref_no, d.po_number, d.file_no, d.grouping
		FROM ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_bundle e, wo_po_details_master c, wo_po_break_down d 
		WHERE a.id=b.mst_id and b.id=e.dtls_id   AND a.job_no=c.job_no AND c.id=d.job_id AND e.order_id=d.id $company_name $buyer_name $job_no $file_no $order_no $cutting_no $table_no $cutting_date $floor_id $internal_ref_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and c.status_active=1 and d.status_active=1
		GROUP BY a.id, a.cutting_no, a.table_no, a.job_no, a.batch_id, a.lay_fabric_wght, a.cad_marker_cons, a.entry_date, a.floor_id, d.id, b.color_id, c.buyer_name, c.style_ref_no, d.po_number, d.file_no, d.grouping,b.marker_qty");
		
	    //  echo "<pre>";
		// print_r($sql);

		//===================

		if (count($sql)==0) 
		{
			echo "<div style='color:red;text-align:center;'>Data not Found..<div/>";die;
		}

		$job_arr=array();
		$po_id_arr=array();
		$cut_no_arr=array();
		foreach($sql as $job_for_class){
			$job_arr[$job_for_class[csf('job_no')]]=$job_for_class[csf('job_no')];
			$po_id_arr[$job_for_class[csf('order_id')]]=$job_for_class[csf('order_id')];
			$cut_no_arr[$job_for_class[csf('cutid')]]=$job_for_class[csf('cutid')];
		}
		// print_r($po_id_arr);die();
		
		$con = connect();
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (9,10) and ENTRY_FORM=20");
		oci_commit($con);
		
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 20, 9, $po_id_arr, $empty_arr);//PO ID
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 20, 10, $cut_no_arr, $empty_arr);//CUT ID
		disconnect($con);
		
		$allPoIds = implode(",", $po_id_arr); 
		$condition= new condition();     
	    $condition->po_id_in($allPoIds);     
	    $condition->init();
	    $fabric= new fabric($condition);
	    // echo $fabric->getQuery();
		// $fabric=new fabric($job_arr,'job');
		$fab_data=$fabric->getQtyArray_by_orderGmtscolorAndBodypart_knitAndwoven_greyAndfinish();
		// echo "<pre>";print_r($fab_data);echo "</pre>";
		//==============================
		
		$job_order_color_arr=array();
		$subtotal_marker_qty=array();
		foreach($sql as $row)
		{
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['cuting_date'] 	= $row[csf('entry_date')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['cutting_no']		= $row[csf('cutting_no')];
			
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['table_no']		= $row[csf('table_no')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['order_id']		= $row[csf('order_id')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['job_no']			= $row[csf('job_no')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['batch_id']		= $row[csf('batch_id')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['buyer_name']		= $row[csf('buyer_name')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['file_no']			= $row[csf('file_no')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['po_number']		= $row[csf('po_number')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['style_ref_no']	= $row[csf('style_ref_no')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['lay_fabric_wght']	= $row[csf('lay_fabric_wght')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['cad_marker_cons']	= $row[csf('cad_marker_cons')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['marker_qty']	 	+= $row[csf('marker_qty')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['total_marker_qty']	 	+= $row[csf('total_marker_qty')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['floor_id']	 	 = $row[csf('floor_id')];
			$job_order_color_arr[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]][$row[csf('cutting_no')]]['grouping']	 	 = $row[csf('grouping')];
			$subtotal_marker_qty[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]]['marker_qty'] += $row[csf('marker_qty')];
			$total_marker_qty +=$subtotal_marker_qty[$row[csf('job_no')]][$row[csf('order_id')]][$row[csf('color_id')]]['marker_qty'];
			
		}
	    //   echo "<pre>";
		//     print_r($total_job_order_color_arr);
		// print_r($job_order_color_arr);
		// echo $sql;

		// =================================== getting roll w8 ===============================
		$sql = "SELECT a.CUTTING_NO,b.ROLL_ID,b.ROLL_WGT from ppl_cut_lay_mst a,ppl_cut_lay_roll_dtls b,gbl_temp_engine c where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=c.ref_val and c.user_id = ".$user_id."  and c.entry_form=20 and c.ref_from=10";
		// echo $sql;
		$res = sql_select($sql);
		$roll_wgt_arr = array();
		$roll_id_chk_arr = array();
		foreach ($res as $val) 
		{
			if(!in_array($val['ROLL_ID'], $roll_id_chk_arr))
			{
				$roll_wgt_arr[$val['CUTTING_NO']] += $val['ROLL_WGT'];
				$roll_id_chk_arr[$val['ROLL_ID']] = $val['ROLL_ID'];
			}
		}
		//   echo "<pre>"; print_r($roll_wgt_arr);die();
		//wo_po_color_size_breakdown
		$sql_order_dtls=sql_select("SELECT a.po_break_down_id, a.color_number_id, (a.plan_cut_qnty) AS plan_cut_qnty, (a.order_quantity) AS rmg_color_qty FROM wo_po_color_size_breakdown a,gbl_temp_engine d WHERE a.status_active=1 AND a.is_deleted=0 and a.po_break_down_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=20 and d.ref_from=9");
		$order_dtls_arr=array();
		foreach($sql_order_dtls as $row)
		{
			$order_dtls_arr[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]]['plan_cut_qnty'] += $row[csf('plan_cut_qnty')];
			$order_dtls_arr[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]]['rmg_color_qty'] += $row[csf('rmg_color_qty')];
		}
		//print_r($order_dtls_arr);die;

		// ========================= fin fab rcv,issue ========================
		
		$sql2=sql_select("SELECT a.po_breakdown_id as PO_BREAKDOWN_ID, a.color_id as COLOR_ID, a.quantity as QUANTITY, a.entry_form as ENTRY_FORM FROM order_wise_pro_details a, gbl_temp_engine d WHERE a.entry_form in(7,18,37) AND a.trans_id>0 and a.po_breakdown_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=20 and d.ref_from=9");
		$fin_rcv_qty_arr=array(); $fin_issue_qty_arr=array();
		foreach($sql2 as $row)
		{
			if($row['ENTRY_FORM']==7 || $row['ENTRY_FORM']==37)
			{
				$fin_rcv_qty_arr[$row['PO_BREAKDOWN_ID']][$row['COLOR_ID']]+=$row['QUANTITY'];
			}
			if($row['ENTRY_FORM']==18)
			{
				$fin_issue_qty_arr[$row['PO_BREAKDOWN_ID']][$row['COLOR_ID']]+=$row['QUANTITY'];
			}
		}
		
		$sql5=sql_select("SELECT a.order_id, a.color_id, a.mst_id, b.cutting_no, (a.reject_qty) AS reject_qty, (a.qc_pass_qty) AS qc_pass_qty, (a.replace_qty) AS replace_qty
								FROM pro_gmts_cutting_qc_dtls a, pro_gmts_cutting_qc_mst b, gbl_temp_engine d
								WHERE a.mst_id=b.id AND a.status_active = 1 AND a.is_deleted = 0 and a.order_id=d.ref_val and d.user_id = ".$user_id." and d.entry_form=20 and d.ref_from=9");
		$qc_qty_arr=array();
		foreach($sql5 as $row)
		{
			$qc_qty_arr[$row[csf('color_id')]][$row[csf('order_id')]][$row[csf('cutting_no')]]['reject_qty'] += $row[csf('reject_qty')];
			$qc_qty_arr[$row[csf('color_id')]][$row[csf('order_id')]][$row[csf('cutting_no')]]['qc_pass_qty']+= $row[csf('qc_pass_qty')];
			$qc_qty_arr[$row[csf('color_id')]][$row[csf('order_id')]][$row[csf('cutting_no')]]['replace_qty']+= $row[csf('replace_qty')];
		}
		
		$con = connect();
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (9,10) and ENTRY_FORM=20");
		oci_commit($con);
		disconnect($con);
		
		// echo"<pre>";
		// print_r($qc_qty_arr);
		
		ob_start();
		?>
	    <table class="rpt_table" width="2220" cellpadding="0" cellspacing="0" border="1" rules="all">
	        <caption style="font-size:20px; font-weight:bold;">
				<?php 
					$com_name = str_replace( "'", "", $cbo_company_name );
	                echo $company_arr[$com_name]."<br/>"."Job/Order Wise Cutting Lay Production Report";
	            ?>
	            <div style="color:red; text-align:left; font-size:16px;">Group By Job, PO and Color</div>
	        </caption>
	        <thead>
	            <tr>
	                <th width="50">Sl</th>
	                <th width="70">Cutting Date</th>
	                <th width="100">Cutting No.</th>
	                <th width="100">Cutting Floor</th>
	                <th width="100">Color Name</th>
	                <th width="60">Table No</th>
	                <th width="100">Buyer Name</th>
	                <th width="100">Job No</th>
	                <th width="60">Style Reff</th>
	                <th width="100">Internal Ref. Number</th>
	                <th width="60">File No</th>
	                <th width="60">Batch No</th>
	                <th width="60">Order No</th>
	                <th width="60">Fini. Req. Qty.</th>
	                <th width="60">Fini. Rcvd. Qty.</th>
	                <th width="60">Fini. Issue Qty.</th>
	                <th width="60">Balance</th>
	                <th width="60">RMG Color Qty</th>
	                <th width="60">Plan Cut Qty (Color)</th>
	                <th width="60">Yet To Cut</th>
	                <th width="60">Lay Fabric Weight (Kg)</th>
	                <th width="60">CAD Marker Cons/Pcs</th>
	                <th width="60">Marker Qty.</th>
	                <th width="60">QC Pass Qty.</th>
	                <th width="60">Replace Qty.</th>
	                <th width="60">Reject Qty.</th>
	                <th width="60">Net Cons/Pcs</th>
	                <th width="60">QC Pass Cons. Qty.</th>
	                <th width="60">Cons. Variation Qty.</th>
	                <th width="60">Cons. Variation (%)</th>
	                <th width="60">Total Rej. Fab. Qty. (Kg)</th>
	                <th>Total Rej. Fab. Qty. (%)</th>
	            </tr>
	        </thead>
	    </table>
	    <div style=" max-height:350px; width:2220px; overflow-y:scroll;" id="scroll_body">
	        <table class="rpt_table" id="table_body" width="2200" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
	            <tbody>
	            	<?php 
					$sl=0;
					$color_subtot_arr=array();
					$grand_total_fini_req_qty			= 0;
					$grand_total_fini_rcv_qty			= 0;
					$grand_total_rmg_color_qty			= 0;
					$grand_total_plan_cut_qty			= 0;
					$grand_total_yet_to_cut				= 0;
					$grand_total_lay_fabric_weight		= 0;
					$grand_total_cad_marker_cons		= 0;
					$grand_total_marker_qty				= 0;
					$grand_total_qc_pass_qty			= 0;
					$grand_total_replace_qty			= 0;
					$grand_total_reject_qty				= 0;
					$grand_total_cut_cons_qty			= 0;
					$grand_total_qc_pass_cons_qty		= 0;
					$grand_total_cons_variation_qty		= 0;
					$grand_total_cons_variation_percn	= 0;
					$grand_total_reject_kg				= 0;
					$grand_total_reject_percn			= 0;

					foreach($job_order_color_arr as $job_ids=>$job_vals)
					{
						foreach($job_vals as $order_ids=>$order_vals)
						{
							//$color_subtot_arr['job_ids']=$job_ids;
							foreach($order_vals as $color_ids=>$color_vals)
							{							
								$total_fini_req_qty			= 0;
								$total_fini_rcv_qty			= 0;
								$total_fini_issue_qty		= 0;
								$total_balance				= 0;
								$total_rmg_color_qty		= 0;
								$total_plan_cut_qty			= 0;
								$total_yet_to_cut			= 0;
								$total_lay_fabric_weight	= 0;
								$total_cad_marker_cons		= 0;
								$total_marker_qty			= 0;
								$total_qc_pass_qty			= 0;
								$total_replace_qty			= 0;
								$total_reject_qty			= 0;
								$total_cut_cons_qty			= 0;
								$total_qc_pass_cons_qty		= 0;
								$total_cons_variation_qty	= 0;
								$total_cons_variation_percn	= 0;
								$total_reject_kg			= 0;
								$total_reject_percn			= 0;

								foreach($color_vals as $cutting_ids=>$cutting_vals)
								{
									$sl++;
									$bgcolor=($sl%2==0)?"#E9F3FF":"#FFFFFF";
									$fin_qty=array_sum($fab_data['knit']['finish'][$order_ids][$color_ids][1])+array_sum($fab_data['knit']['finish'][$order_ids][$color_ids][20])+array_sum($fab_data['knit']['finish'][$order_ids][$color_ids][125]);//1,20,125
									//Plan Cut Qty - sum of  Marker Qty.
									$yet_to_cut=$order_dtls_arr[$order_ids][$color_ids]['plan_cut_qnty'] - $color_vals['marker_qty'];
									// Lay Fabric Weight / Marker Qty.
									$net_cons_per_pcs=$roll_wgt_arr[$cutting_ids]/$cutting_vals['marker_qty'];
									//qc_pass_cons_qty = ((Replace Qty * Marker Cons. Per pcs) + Lay Fabric Weight)/QC pass qty.

									$qc_pass_cons_qty=(($qc_qty_arr[$color_ids][$order_ids]['replace_qty']*$net_cons_per_pcs)+$roll_wgt_arr[$cutting_ids])/$qc_qty_arr[$color_ids][$order_ids][$cutting_ids]['qc_pass_qty'];
									// echo $qc_qty_arr[$color_ids][$order_ids]['replace_qty']."*".$net_cons_per_pcs."+".$roll_wgt_arr[$cutting_ids]."/".$qc_qty_arr[$color_ids][$order_ids][$cutting_ids]['qc_pass_qty']."<br>";

									//cons_variation_qty=QC pass Consum - Net Cons per Pcs
									$cons_variation_qty=$qc_pass_cons_qty-$net_cons_per_pcs;
									//Cons. Variation / QC pass cons. * 100
									$cons_variation_percn=$cons_variation_qty/$qc_pass_cons_qty*100;
									//Reject Qty. * Net Cons Per Pcs
									$reject_kg=$qc_qty_arr[$color_ids][$order_ids][$cutting_ids]['reject_qty']*$net_cons_per_pcs;
									//Total Reject Fab. Qty. / Lay Fabric weight *100
									$reject_percn=$reject_kg/$roll_wgt_arr[$cutting_ids]*100;
									// New Lay Fabric Weight Kg
									
									

									?>
									<tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $sl;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')">
										<td width="50"><?php echo $sl; ?></td>
										<td width="70"><p><?php echo change_date_format($cutting_vals['cuting_date']); ?></p></td>
										<td width="100"><p><a href="#" onClick="generate_report_lay_chart('<?php echo $cutting_vals['cutting_no']."*".$job_ids; ?>')"><?php echo $cutting_vals['cutting_no']; ?></a></p></td>
										<td width="100"><p><?php echo $floor_arr[$cutting_vals['floor_id']]; ?></p></td>
										<td width="100" title="<?=$color_ids;?>"><p><?php echo $color_library[$color_ids]; ?></p></td>
										<td width="60"><p><?php echo $table_arr[$cutting_vals['table_no']]; ?></p></td>
										<td width="100"><p><?php echo $buyer_arr[$cutting_vals['buyer_name']]; ?></p></td><!--Buyer Name-->
										<td width="100"><p><?php echo $cutting_vals['job_no']; ?></p></td><!--Job No-->
										<td width="60"><p><?php echo $cutting_vals['style_ref_no']; ?></p></td><!--Style Reff-->
										<td width="100"><p><?php echo $cutting_vals['grouping']; ?></p></td><!--Internal Ref Number-->
										<td width="60"><p><?php echo $cutting_vals['file_no']; ?></p></td><!--File No-->
										<td width="60"><p><?php echo $cutting_vals['batch_id']; ?></p></td><!--Batch No-->
										<td width="60" title="<?=$order_ids;?>"><p><?php echo $cutting_vals['po_number']; ?></p></td><!--Order No-->
	                                   
	                                    <?
										//print_r($color_check_arr);
										if(in_array($color_ids,$color_check_arr))
										{
											?>	
											<td width="60" align="right"><p><?php //echo fn_number_format($fin_qty, 2); ?></p></td><!--Fini. Req. Qty.-->
											<td width="60" align="right"><p><?php //echo fn_number_format($fin_rcv_qty_arr[$order_ids][$color_ids], 2); ?></p></td>
											<td width="60"></td>
											<td width="60"></td>
											<td width="60" align="right"><p><?php //echo $order_dtls_arr[$order_ids][$color_ids]['rmg_color_qty']; ?></p></td><!--RMG Color Qty-->
											<td width="60" align="right"><p><?php //echo $order_dtls_arr[$order_ids][$color_ids]['plan_cut_qnty']; ?></p></td><!--Plan Cut Qty (Color)-->
		                                    <td width="60" align="right"><p><?php //echo $yet_to_cut; ?></p></td><!--Yet To Cut-->	
											<?	
										}
										else
										{
											$total_fini_req_qty			+= $fin_qty;
											$total_fini_rcv_qty			+= $fin_rcv_qty_arr[$order_ids][$color_ids];
											$total_fini_issue_qty		+= $fin_issue_qty_arr[$order_ids][$color_ids];
											$total_balance 				+= $fin_rcv_qty_arr[$order_ids][$color_ids] - $fin_issue_qty_arr[$order_ids][$color_ids];
											$total_rmg_color_qty		+= $order_dtls_arr[$order_ids][$color_ids]['rmg_color_qty'];
											$total_plan_cut_qty			+= $order_dtls_arr[$order_ids][$color_ids]['plan_cut_qnty'];
											?>
		                                    
											<td width="60" align="right"><p><?php echo fn_number_format($fin_qty, 2); ?></p></td><!--Fini. Req. Qty.-->
											<td width="60" align="right"><p><?php echo fn_number_format($fin_rcv_qty_arr[$order_ids][$color_ids], 2); ?></p></td><!--Fini. Rcvd. Qty.-->
											<td width="60" align="right"><p><?php echo fn_number_format($fin_issue_qty_arr[$order_ids][$color_ids], 2); ?></p></td><!--Fini. Rcvd. Qty.-->
											<td width="60" align="right"><p><?php echo fn_number_format(($fin_issue_qty_arr[$order_ids][$color_ids] - $fin_rcv_qty_arr[$order_ids][$color_ids]), 2); ?></p></td><!--Fini. Rcvd. Qty.-->
											<td width="60" align="right"><p><?php echo $order_dtls_arr[$order_ids][$color_ids]['rmg_color_qty']; ?></p></td><!--RMG Color Qty-->
											<td width="60" align="right"><p><?php echo $order_dtls_arr[$order_ids][$color_ids]['plan_cut_qnty']; ?></p></td><!--Plan Cut Qty (Color)-->
		                                    <td width="60" align="right" title="(Plan Cut Qty (Color)) - (Marker qty)"><p><?php echo $order_dtls_arr[$order_ids][$color_ids]['plan_cut_qnty']-$subtotal_marker_qty[$job_ids][$order_ids][$color_ids]['marker_qty']; ?></p></td><!--Yet To Cut-->
		                                    <?
										}
										?>
										<td width="60" align="right"><p><?php echo fn_number_format($roll_wgt_arr[$cutting_ids]*( $cutting_vals['marker_qty']/ $cutting_vals['total_marker_qty']), 2); ?></p></td><!--Lay Fabric Weight (Kg)-->
										<td width="60" align="right"><p><?php echo fn_number_format($cutting_vals['cad_marker_cons']/12, 4); ?></p></td><!--CAD Marker Cons/Pcs-->
										<td width="60" align="right"><p><?php echo $cutting_vals['marker_qty']; ?></p></td><!--Marker Qty.-->
										<td width="60" align="right"><p><?php echo $qc_qty_arr[$color_ids][$order_ids][$cutting_ids]['qc_pass_qty']; ?></p></td><!--QC Pass Qty.-->
										<td width="60" align="right"><p><?php echo $qc_qty_arr[$color_ids][$order_ids][$cutting_ids]['replace_qty']; ?></p></td><!--Replace Qty.-->
										<td width="60" align="right"><p><?php echo $qc_qty_arr[$color_ids][$order_ids][$cutting_ids]['reject_qty']; ?></p></td><!--Reject Qty.-->

										<td width="60" align="right" title="(Lay Fabric Weight (Kg)) / (Marker Qty)"><p><?php echo fn_number_format($net_cons_per_pcs, 4); ?></p></td><!--Net Cons. Per Pcs-->
										<td width="60" align="right" title="((Replace Qty * Marker Cons Per pcs) + Lay Fabric Weight (Kg)) / QC pass qty"><p><?php echo fn_number_format($qc_pass_cons_qty, 4); ?></p></td><!--QC Pass Cons. Qty.-->
										<td width="60" align="right" title="QC Pass Cons Qty - Net Cons per Pcs"><p><?php echo fn_number_format($cons_variation_qty, 4); ?></p></td><!--Cons. Variation Qty.-->
										<td width="60" align="right" title="Cons Variation Qty /  QC Pass Cons Qty * 100"><p><?php echo fn_number_format($cons_variation_percn, 2); ?></p></td><!--Cons. Variation %-->
										<td width="60" align="right" title="Reject Qty. * Net Cons Per Pcs"><p><?php echo fn_number_format($reject_kg, 2); ?></p></td><!--Reject(Kg)-->
										<td align="right" title="Total Reject Fab Qty / Lay Fabric weight (kg)*100"><p><?php echo fn_number_format($reject_percn, 2); ?></p></td><!--Reject(%)-->
									</tr>
									<?php 
									$total_yet_to_cut			+= $yet_to_cut;	
									$total_lay_fabric_weight	+= $roll_wgt_arr[$cutting_ids]*( $cutting_vals['marker_qty']/ $cutting_vals['total_marker_qty']);
									$total_cad_marker_cons		+= $cutting_vals['cad_marker_cons']/12;
									$total_marker_qty			+= $cutting_vals['marker_qty'];
									$total_qc_pass_qty			+= $qc_qty_arr[$color_ids][$order_ids][$cutting_ids]['qc_pass_qty'];
									$total_replace_qty			+= $qc_qty_arr[$color_ids][$order_ids][$cutting_ids]['replace_qty'];
									$total_reject_qty			+= $qc_qty_arr[$color_ids][$order_ids][$cutting_ids]['reject_qty'];
									$total_cut_cons_qty			+= $net_cons_per_pcs;
									$total_qc_pass_cons_qty		+= $qc_pass_cons_qty;
									$total_cons_variation_qty	+= $cons_variation_qty;
									$total_cons_variation_percn	+= $cons_variation_percn;
									$total_reject_kg			+= $reject_kg;
									$total_reject_percn			+= $reject_percn;

									$color_check_arr[]=$color_ids;

								}
								// if(!in_array($color_ids,$color_subtot_arr))
								// {
									?>
								
									<?php
									$grand_total_fini_req_qty		+= $total_fini_req_qty;
									$grand_total_fini_rcv_qty		+= $total_fini_rcv_qty;
									$grand_total_fini_issue_qty		+= $total_fini_issue_qty;
									$grand_total_balance			+= $total_balance;
									$grand_total_rmg_color_qty		+= $total_rmg_color_qty;
									$grand_total_plan_cut_qty		+= $total_plan_cut_qty;
									$grand_total_yet_to_cut			+= $total_yet_to_cut;
									$grand_total_lay_fabric_weight	+= $total_lay_fabric_weight;
									$grand_total_cad_marker_cons	+= $total_cad_marker_cons;
									$grand_total_marker_qty			+= $total_marker_qty;
									$grand_total_qc_pass_qty		+= $total_qc_pass_qty;
									$grand_total_replace_qty		+= $total_replace_qty;
									$grand_total_reject_qty			+= $total_reject_qty;
									$grand_total_cut_cons_qty		+= $total_cut_cons_qty;
									$grand_total_qc_pass_cons_qty	+= $total_qc_pass_cons_qty;
									$grand_total_cons_variation_qty	+= $total_cons_variation_qty;
									$grand_total_cons_variation_percn+= $total_cons_variation_percn;
									$grand_total_reject_kg			+= $total_reject_kg;
									$grand_total_reject_percn		+= $total_reject_percn;
								// }
								$color_subtot_arr[]=$color_ids;
								unset($color_check_arr);
							}
							
						}
					}
					?>
	            </tbody>
	            <tfoot>            	
	                <tr> 
						
	                    <th colspan="13" align="right"><strong>Total=</strong></th>
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_fini_req_qty, 2); ?></strong></p></th><!--Fini. Req. Qty.-->
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_fini_rcv_qty, 2); ?></strong></p></th><!--Fini. Rcvd. Qty.-->
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_fini_issue_qty, 2); ?></strong></p></th><!--Fini. Rcvd. Qty.-->
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_balance, 2); ?></strong></p></th><!--Fini. Rcvd. Qty.-->
	                    <th align="right"><p><strong><?php echo $grand_total_rmg_color_qty; ?></strong></p></th><!--RMG Color Qty-->
	                    <th align="right"><p><strong><?php echo $grand_total_plan_cut_qty; ?></strong></p></th><!--Plan Cut Qty (Color)-->
	                    <th align="right"><p><strong><?php echo $grand_total_yet_to_cut; ?></strong></p></th><!--Yet To Cut-->
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_lay_fabric_weight, 2); ?></strong></p></th><!--Lay Fabric Weight (Kg)-->
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_cad_marker_cons, 4); ?></strong></p></th><!--CAD Marker Cons/Pcs-->
	                    <th align="right"><p><strong><?php echo $grand_total_marker_qty; ?></strong></p></th><!--Marker Qty.-->
	                    <th align="right"><p><strong><?php echo $grand_total_qc_pass_qty; ?></strong></p></th><!--QC Pass Qty.-->
	                    <th align="right"><p><strong><?php echo $grand_total_replace_qty; ?></strong></p></th><!--Replace Qty.-->
	                    <th align="right"><p><strong><?php echo $grand_total_reject_qty; ?></strong></p></th><!--Reject Qty.-->
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_cut_cons_qty, 4); ?></strong></p></th><!--Net Cons. Per Pcs-->
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_qc_pass_cons_qty, 4); ?></strong></p></th><!--QC Pass Cons. Qty.-->
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_cons_variation_qty, 4); ?></strong></p></th><!--Cons. Variation Qty.-->
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_cons_variation_percn, 2); ?></strong></p></th><!--Cons. Variation %-->
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_reject_kg, 2); ?></strong></p></th><!--Reject(Kg)-->
	                    <th align="right"><p><strong><?php echo fn_number_format($grand_total_reject_percn, 2); ?></strong></p></th><!--Reject(%)-->
	                </tr>
	            </tfoot>
	        </table>
	    </div>
		<?php
	}

	foreach (glob("*.xls") as $filename)
	{		
		@unlink($filename);
	}
	$name=time().".xls";
	$create_new_excel = fopen($name, 'w');	
	$report_data=ob_get_contents();
	ob_clean();
	$is_created = fwrite($create_new_excel,$report_data);
	echo $report_data."####".$name;
	exit();
}

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');

	extract($_REQUEST);
	?>
	<script>
	
		function js_set_value(str)
		{
			$("#hide_job_no").val(str); 
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:580px;">
            <table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Job No</th>
                    <th>
                        <input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"> 					
                        <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                    </th>
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value, 'create_job_no_search_list_view', 'search_div', 'cutting_lay_production_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:5px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}

if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	//$year_id=$data[4];
	//$month_id=$data[5];
	//echo $month_id;
	
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and buyer_name=$data[1]";
	}
	if($data[3]=="")
	{
		echo "<div>PLease enter search field value.</div>";
		die;
	}
	
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";
	if($search_by==2) $search_field="style_ref_no"; else $search_field="job_no";
	//$year="year(insert_date)";
	if($db_type==0) $year_field="YEAR(insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year";
	else $year_field="";
	
	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(insert_date)=$year_id"; else $year_cond="";	
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(insert_date,'YYYY')";
		if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";	
	}
	//if($month_id!=0) $month_cond=" and month(insert_date)=$month_id"; else $month_cond="";
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_field from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond order by job_no";
	
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","600","240",0, $sql , "js_set_value", "job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','','') ;
	exit(); 
} // Job Search end

if($action=="cutting_number_popup")
{
  	echo load_html_head_contents("Batch Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_cutting_value(strCon ) 
		{
			document.getElementById('hdn_cut_no').value=strCon;
			parent.emailwindow.hide();
		}
    </script>
</head>
<body>
<div align="center" style="width:100%; overflow-y:hidden;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table width="950" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
            <thead>
                <tr>                	 
                    <th width="140">Company name</th>
                    <th width="130">Cutting No</th>
                    <th width="130">Job No</th>
                    <th width="130">Order No</th>
                    <th width="250">Date Range</th>
                    <th width="120"><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                  <tr>                    
                        <td>
                              <? 
                              echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0  order by company_name","id,company_name", 0, "-- Select Company --",$company_id, "",1);
                             ?>
                        </td>
                      
                        <td align="center" >
                                <input type="text" id="txt_cut_no" name="txt_cut_no" style="width:120px"  class="text_boxes"/>
                                <input type="hidden" id="hdn_cut_no" name="hdn_cut_no" />
                        </td>
                        <td align="center">
                               <input name="txt_job_search" id="txt_job_search" class="text_boxes" style="width:120px"  />
                        </td>
                        <td align="center">
                               <input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:120px"  />
                        </td>
                        <td align="center" width="250">
                               <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px" placeholder="From Date" />
                               <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px" placeholder="To Date" />
                        </td>
                        <td align="center">
                               <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_cut_no').value+'_'+document.getElementById('txt_job_search').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_order_search').value, 'create_cutting_search_list_view', 'search_div', 'cutting_lay_production_report_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
                        </td>
                 </tr>
        		 <tr>                  
                        <td align="center" height="40" valign="middle" colspan="6">
                            <? echo load_month_buttons(1);  ?>
                        </td>
                </tr>   
            </tbody>
         </tr>         
      </table> 
     <div align="center" valign="top" id="search_div"> </div>  
  </form>
</div>    
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_cutting_search_list_view")
{
    $ex_data = explode("_",$data);
	$company = $ex_data[0];	
	$cutting_no = $ex_data[1];
	$job_no = $ex_data[2];
	$from_date = $ex_data[3];
	$to_date = $ex_data[4];
	$cut_year= $ex_data[5];
	$order_no= $ex_data[6];
	
	if($cutting_no=="" && $job_no=="" && $job_no==""  && $from_date==""  && $to_date==""  && $order_no==""  )
	{
		echo "<div>PLease enter search field value.</div>";
		die;
	}
    if($db_type==2) { $year_cond=" and extract(year from a.insert_date)=$cut_year"; $year=" extract(year from a.insert_date) as year ";}
    if($db_type==0) {$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$cut_year"; $year=" SUBSTRING_INDEX(a.insert_date, '-', 1) as year ";}
	if(str_replace("'","",$company)==0) $conpany_cond=""; else $conpany_cond="and a.company_id=".str_replace("'","",$company)."";
	if(str_replace("'","",$cutting_no)=="") $cut_cond=""; else $cut_cond="and a.cut_num_prefix_no='".str_replace("'","",$cutting_no)."'  $year_cond";
	if(str_replace("'","",$job_no)=="") $job_cond=""; else $job_cond="and b.job_no_prefix_num='".str_replace("'","",$job_no)."'";
	if(str_replace("'","",$order_no)=="") $order_cond=""; else $order_cond="and c.po_number like '%".trim($order_no)."%' ";
	if( $from_date!="" && $to_date!="" )
	{
		if($db_type==0)
	       {
			      $sql_cond= " and entry_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
		   }
	  if($db_type==2)
	       {
			      $sql_cond= " and entry_date  between '".change_date_format($from_date,'yyyy-mm-dd','-',1)."' and '".change_date_format($to_date,'yyyy-mm-dd','-',1)."'";
		   }
	}
	
	$sql_order="SELECT a.id,a.cut_num_prefix_no, a.table_no, a.job_no, a.batch_id, a.entry_date, a.marker_length, a.marker_width, a.fabric_width,d.order_id,d.color_id,$year FROM ppl_cut_lay_mst a,ppl_cut_lay_dtls d,wo_po_details_master b,wo_po_break_down c where  a.id=d.mst_id and a.job_no=b.job_no and b.id=c.job_id and a.entry_form=76 and c.id=d.order_id $conpany_cond  $cut_cond $job_cond $sql_cond $order_cond order by id";
	// echo $sql_order;
	$res = sql_select($sql_order);
	$po_id_arr = array();
	foreach ($res as $val) 
	{
		$po_id_arr[$val['ORDER_ID']] = $val['ORDER_ID'];
	}

	$po_cond = where_con_using_array($po_id_arr,0,'id');

	$order_no_library	= return_library_array( "SELECT id,po_number from wo_po_break_down where status_active=1 and is_deleted=0 $po_cond", "id", "po_number"  );

	$arr=array(2=>$table_arr,4=>$order_no_library,5=>$color_library);
	echo create_list_view("list_view", "Cut No,Year,Table No,Job No,Order NO,Color,Marker Length,Markar Width,Fabric Width,Entry Date","90,50,60,120,120,100,80,80,80,120","950","270",0, $sql_order , "js_set_cutting_value", "cut_num_prefix_no", "", 1, "0,0,table_no,0,order_id,color_id,0,0,0,0,0", $arr, "cut_num_prefix_no,year,table_no,job_no,order_id,color_id,marker_length,marker_width,fabric_width,entry_date", "","setFilterGrid('list_view',-1)") ;
}

if($action=="order_no_search_popup")
{
	echo load_html_head_contents("Order No Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
     
	<script>
		function js_set_value( str ) 
		{
			$('#hide_order_no').val(str);
			parent.emailwindow.hide();
		}
    </script>
</head>

<body>
<div align="center">
	<form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:780px;">
            <table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Order No</th>
                    <th>Shipment Date</th>
                    <th>
                        <input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;">
                        <input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
                    </th> 
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",0,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Order No",2=>"Style Ref",3=>"Job No");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 110, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
                        </td>	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_order_no_search_list_view', 'search_div', 'cutting_lay_production_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    	</td>
                    </tr>
                    <tr>
                        <td colspan="5" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit(); 
}

if($action=="create_order_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$data[1]";
	}
	
	if($data[3]=="")
	{
		echo "<div>PLease enter search field value.</div>";
		die;
	}
	
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";

	if($search_by==1) 
		$search_field="b.po_number"; 
	else if($search_by==2) 
		$search_field="a.style_ref_no"; 	
	else 
		$search_field="a.job_no";
		
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
	else
	{
		$date_cond="";
	}
	
	$arr=array (0=>$company_library,1=>$buyer_arr);
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	$sql= "SELECT b.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond order by b.id, b.pub_shipment_date";
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date", "80,80,50,70,140,170","760","220",0, $sql , "js_set_value", "po_number","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,pub_shipment_date","",'','0,0,0,0,0,0,3') ;
   exit(); 
}



if($action=="cut_lay_entry_report_print_two")
{
    // extract($_REQUEST);
	$data=explode('*',$data);
	// print_r($data);

	$sql=sql_select("select a.id,a.job_no,a.cut_num_prefix_no,a.table_no,a.marker_length,a.marker_width,a.fabric_width,a.gsm,a.width_dia,a.start_time,a.end_time,a.cad_marker_cons,a.batch_id,a.company_id, b.grouping,d.color_id,d.order_cut_no,d.roll_data,d.gmt_item_id,e.roll_id from ppl_cut_lay_mst a,wo_po_break_down b,wo_po_color_size_breakdown c,ppl_cut_lay_dtls d,ppl_cut_lay_bundle e  where a.job_no=b.job_no_mst and a.id=d.mst_id and b.id=c.po_break_down_id and a.id=e.mst_id and cutting_no='".$data[0]."' ");

//  $sql_result="select a.id,a.job_no,a.cut_num_prefix_no,a.table_no,a.marker_length,a.marker_width,a.fabric_width,a.gsm,a.width_dia,a.start_time,a.end_time,a.cad_marker_cons,a.batch_id,a.company_id, b.grouping,d.color_id,d.order_cut_no,d.roll_data,d.gmt_item_id,e.roll_id from ppl_cut_lay_mst a,wo_po_break_down b,wo_po_color_size_breakdown c,ppl_cut_lay_dtls d,ppl_cut_lay_bundle e  where a.job_no=b.job_no_mst and a.id=d.mst_id and b.id=c.po_break_down_id and a.id=e.mst_id and cutting_no='".$data[0]."' ";
// 	 echo $sql_result;


	$batchsql="select a.id, a.batch_no from pro_roll_details a, ppl_cut_lay_bundle b,ppl_cut_lay_mst c where a.id=b.roll_id and b.mst_id=c.id and a.entry_form=509 and cutting_no='".$data[0]."' ";
	// echo $batchsql;
	$main_batch_sql=sql_select($batchsql);
	$batch_no_arr=array();

	foreach($main_batch_sql as $row)
	{
		$batch_no_arr[$row[csf('id')]]['batch_no']=$row[csf('batch_no')].",";

	}
	// echo '<pre>';
	// print_r($batch_no_arr);
	// echo '</pre>';



	foreach($sql as $val)
		{
			$mst_id=$val[csf('id')];
			$company_id=$val[csf('company_id')];
			$cut_prifix=$val[csf('cut_num_prefix_no')];
		    $table_no=$val[csf('table_no')];
			$marker_length=$val[csf('marker_length')];
			$marker_with=$val[csf('marker_width')];
			$fabric_with=$val[csf('fabric_width')];
			$gsm=$val[csf('gsm')];
			$dia_width=$val[csf('width_dia')];
			$txt_batch=$val[csf('batch_id')];
			$cad_marker_cons=$val[csf('cad_marker_cons')];
			$job_no=$val[csf('job_no')];
			$grouping=$val[csf('grouping')];
			$color=$val[csf('color_id')];
			$item=$val[csf('gmt_item_id')];
			$order_cut_no=$val[csf('order_cut_no')];
			$start_time=$val[csf('start_time')];
			$end_time=$val[csf('end_time')];
			// $batch_no=$val[csf("roll_id")];

		}



	$costing_per=return_field_value("costing_per","wo_pre_cost_mst", "job_no='$job_no'");
	if($costing_per==1)
	{
		$costing_per_qty=12;
	}
	else if($costing_per==2)
	{
		$costing_per_qty=1;
	}
	else if($costing_per==3)
	{
		$costing_per_qty=24;
	}
	else if($costing_per==4)
	{
		$costing_per_qty=36;
	}
	else if($costing_per==5)
	{
		$costing_per_qty=48;
	}

	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$comp_name=return_field_value("company_short_name","lib_company", "id=$company_id");
	$sql_buyer_arr=sql_select("select buyer_name,style_ref_no from  wo_po_details_master where job_no='$data[1]'");
	$sql_order=sql_select("select order_ids,gmt_item_id,order_qty from ppl_cut_lay_dtls where mst_id='$mst_id'");
	$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	$color_arr=return_library_array( "select id,color_name  from  lib_color", "id", "color_name"  );
	$order_number_arr=return_library_array( "select id, po_number from wo_po_break_down where job_no_mst='$data[1]'",'id','po_number');
	$table_no_library=return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );

	 $table_lib= return_library_array("SELECT id,table_no FROM LIB_CUTTING_TABLE WHERE is_deleted = 0 and status_active=1 and company_id='$company_id'  order by table_no","id","table_no");
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	// $batchNo_arr=return_library_array( "select a.id, a.batch_no from pro_roll_details a, ppl_cut_lay_bundle b where a.id=b.roll_id and mst_id='$mst_id' and a.entry_form=509","id","batch_no" );

	//print_r($sql_order);
	// $table_name=sql_select($table_lib);
	// // $table_arr=array();
	// foreach($table_name as $val)
	// {
	// 	$table_no=$val[csf('table_name')];
	// }
	// echo $table_no;
	//  echo '<pre>';print_r($table_arr);echo'</pre>';
	$order_number=""; $order_id='';
	foreach($sql_order as $order_val)
	{
		$item_name=$order_val[csf('gmt_item_id')];
		$order_qty+=$order_val[csf('order_qty')];
		if($order_id!="")
		{
			$order_id.=",".$order_val[csf('order_ids')];
		}
		else
		{
			$order_id=$order_val[csf('order_ids')];
		}
	}


	$order_ids=array_unique(explode(",",$order_id));
	foreach($order_ids as $poId)
	{
		if($order_number!="")
		{
			$order_number.=", ".$order_number_arr[$poId];
		}
		else
		{
			$order_number=$order_number_arr[$poId];
		}
	}


	?>
    <div style="width:1100px; position:relative">
    <div style=" width:500; height:200px; position:absolute; left:300px; top:0; ">
        <table width="500" cellspacing="0" align="center">
            <tr>
                <td  align="center" style="font-size:22px; font-weight:bold;"><strong><? echo $company_library[$company_id]; ?></strong></td>
            </tr>
            <tr>
                <td  align="center" style="font-size:18px; font-weight:bold;"><strong>LAY CHART & CONSUMPTION REPORT</strong></td>
            </tr>
       </table>

    </div>
    <div style=" width:200; height:40px; position:absolute; right:0; top:50px; ">Date:  ......../......../.......... </div>
    <div style=" width:200; height:60px; position:absolute; right:0; top:90px; " id="barcode_img_id"> </div>
    <div style=" top:80px; width:270; height:200px; position:absolute; left:0; ">
	<table border="1"  cellspacing="0"  width="260"class="rpt_table" rules="all">
         <tr>
              <td width="80">Buyer</td><td width="180" align="center"><? echo $buyer_arr[$sql_buyer_arr[0][csf('buyer_name')]]; ?></td>
         </tr>
         <tr>
              <td>Int B No</td><td align="center"> <? echo $grouping; ?></td>
         </tr>
         <tr>
              <td>Style</td><td align="center"> <? echo $sql_buyer_arr[0][csf('style_ref_no')]; ?></td>
         </tr>
         <tr>
              <td>Item</td> <td align="center"><? echo $garments_item[$item_name]; ?></td>
        </tr>
		<tr>
              <td>Garments Color</td> <td align="center"><? echo $color_arr[$color]; ?></td>
        </tr>
         <tr>
              <td>Order Qty</td><td align="right"><? echo $order_qty; ?></td>
         </tr>

    </table>
    </div>
    <div  style="width:270; position:absolute; height:30px; top:80px; left:280px">
	<table border="1"  cellspacing="0"  width="260"class="rpt_table" rules="all">
	    <tr>
              <td width="80">CAD Marker Length</td><td width="80" align="center" ><? echo $marker_length;  ?></td>
         </tr>
         <tr>
              <td>CAD Marker Width</td><td align="center" ><? echo $marker_with;  ?></td>
         </tr>
         <tr>
		    <td> CAD Fabric Width/Dia</td><td align="center"><? echo $fabric_with; ?></td>
         </tr>
         <tr>
              <td>CAD GSM</td> <td align="center"><? echo $gsm; ?></td>
        </tr>

    </table>
    </div>


    <div  style="width:505; position:absolute;  top:250px; left:0px">
      <table border="1" cellpadding="1" cellspacing="1"   width="480"class="rpt_table" rules="all">
          <tr>
          <td width="80">Table No</td>
          <td width="75" align="center"><? echo $table_lib[$table_no_library[$table_no]]; ?></td>
          <td width="75" align="center">Batch No </td>
		  <td width="75" align="center">  <? foreach($batch_no_arr as $id=>$val){ echo $val['batch_no']; }; ?></td>
          <td width="80" align="center">Dia(Tube<br>/Open)</td>
          <td >Start Time</td>
		  <td align="center"><? echo $start_time; ?></td>
		  <td align="center" width="100"><strong>Total Time Taken</strong></td>

         </tr>
         <tr>
          <td>Sys Cutting No</td> <td align="center"><? echo $comp_name."-".$cut_prifix; ?></td>

		  <td width="80">M.Cutting No</td>
		 <td width="75" align="center"><?echo $order_cut_no;?></td>
         <td width="80" align="center"> <? echo $fabric_typee[$dia_width]; ?></td>
		 <td >End  Time</td>
		 <td align="center"><? echo $end_time; ?></td>
		 <td align="center" width="100"><? $total_time_taken=$end_time-$start_time; echo $total_time_taken; ?></td>
         </tr>
      </table>
    </div>

     <div  style="width:200; position:absolute; height:400px; top:80px; left:580px">
       <table border="1" cellpadding="1" cellspacing="1"   width="200"class="rpt_table" rules="all">
          <tr height="30">
          <td width="90">Sperading Operators</td>
          <td width="100" align="center"></td>
         </tr>
         <tr height="30">
          <td width="">Checked by Marker Man</td>
          <td width="" align="center"></td>
         </tr>
           <tr height="30">
          <td width="90">Cutter Man-1</td>
          <td width="100" align="center"></td>
         </tr>
         <tr height="43">
          <td width="">Cutter Man-2</td>
          <td width="" align="center"></td>
         </tr>
          <tr height="30">
          <td width="">Cutter Man-3</td>
          <td width="" align="center"></td>
         </tr>
      </table>
    </div>




     <script type="text/javascript" src="../../../js/jquery.js"></script>
     <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
     <script>

	function generateBarcode( valuess ){

			var value = valuess;//$("#barcodeValue").val();
		// alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();

			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 30,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			 value = {code:value, rect: false};

			$("#barcode_img_id").show().barcode(value, btype, settings);

		}
	   generateBarcode('<? echo $data[0]; ?>');
	 </script>


 <div style=" width:1100px; position:absolute; top:385px; ">
   <style type="text/css">
            .block_div {
                    width:auto;
                    height:auto;
                    text-wrap:normal;
					font-size:10.5px;
                    vertical-align:bottom;
                    display: block;

                    -webkit-transform: rotate(-90deg);
                    -moz-transform: rotate(-90deg);
            }

        </style>
   <?

     $sql_size_ration=sql_select("select a.id,b.size_id,b.size_ratio from
     ppl_cut_lay_size_dtls b,ppl_cut_lay_dtls a where a.id=b.dtls_id  and a.mst_id=$mst_id and b.status_active=1  and  b.is_deleted=0 and a.status_active=1
     and a.is_deleted=0 ");
	 $detali_data_arr=array();
	 $plice_data_arr=array();
	 $size_id_arr=array();
	 $total_gmt_qty=array();
	 $grand_total=0;
	 $size_qty=0;
	 $size_ratio_arr=array();
	 foreach($sql_size_ration as $size_val)
	 {
	  	$size_ratio_arr[$size_val[csf('id')]][$size_val[csf('size_id')]]=$size_val[csf('size_ratio')];
	 }
	//  echo '<pre>';
	//  print_r($size_ratio_arr);
	//  echo '</pre>';

	$sql_main_qry=sql_select("select c.id,a.id,a.color_id,c.size_id,c.roll_no,sum(c.roll_wgt) as roll_weight,c.plies, sum(c.size_qty) as size_qty,c.roll_id
	 from  ppl_cut_lay_roll_dtls c,ppl_cut_lay_dtls a
	 where  a.id=c.dtls_id and a.mst_id=$mst_id and c.status_active=1  and  c.is_deleted=0 and a.status_active=1 and a.is_deleted=0
	 group by c.id,a.id,a.color_id,c.size_id,c.roll_no,c.plies,c.roll_id
	 order by a.id,c.id");
	 $detali_data_arr=array();
	 $plice_data_arr=array();
	 $size_id_arr=array();
	 $total_gmt_qty=array();
	 $grand_total=0;
	 $size_qty=0;
	 foreach($sql_main_qry as $main_val)
	 {
	    $size_id_arr[$main_val[csf('size_id')]]=$main_val[csf('size_id')];
		$plice_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]]['plies']=$main_val[csf('plies')];
		$detali_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]][$main_val[csf('size_id')]]['size']=$main_val[csf('size_id')];
		$detali_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]][$main_val[csf('size_id')]]['marker_qty']=$main_val[csf('size_qty')];
		$total_gmt_qty[$main_val[csf('id')]][$main_val[csf('roll_id')]]['gmt_qty']+=$main_val[csf('size_qty')];
		$grand_total+=$main_val[csf('marker_qty')];
		$size_qty=return_field_value("size_qty","ppl_cut_lay_bundle","mst_id =$mst_id AND dtls_id =".$main_val[csf('id')]." ");
		$plice_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]]['bundle_qty']=$size_qty;
		$plice_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]]['color']=$main_val[csf('color_id')];
		$plice_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]]['roll_no']=$main_val[csf('roll_no')];
		$plice_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]]['roll_weight']=$main_val[csf('roll_weight')];
	 }
 //print_r($plice_data_arr);die;
   $col_span=count($size_id_arr);
   $td_width=450/$col_span;

  // echo $td_width;die;

   ?>
    <table border="1" cellpadding="1" cellspacing="1"   width="970px position:absolute" top="450px" class="rpt_table" rules="all">
          <tr height="30" >
          <td width="30">SL</td>
          <td width="60" align="center">Roll No </td>
          <td width="60" align="center">Roll Kgs </td>
          <td width="50" align="center">Cuttable End Bit.</td>
		  <td width="50" align="center">Unusable End Bit</td>
		  <td width="50" align="center">Unusable Wastage</td>
          <td width="70"> Plies & Pcs/Bundle</td>
           <td width="80">Particulars</td>
          <td width="470" align="center" colspan="<? echo $col_span; ?>">Size, Ratio and Garments Qty.</td>
          <td width="50" align="center">Total Gmts</td>
         </tr>
        <?
		 $i=1; $tot_gmts=0; $tot_roll_wght=0;
		  foreach($plice_data_arr as $dtls_id=>$dtls_val)
			  {
				foreach($dtls_val as $plice_id=>$plice_val)
				 {
					 $tot_roll_wght+=$plice_val['roll_weight'];
				 ?>
                 <tr height="20">
                      <td width="" rowspan="4"><? echo $i;  ?></td>
                      <td width="" align="center" rowspan="4"><? echo $plice_val['roll_no']; ?> </td>
                      <td width="" align="center" rowspan="4"><? echo $plice_val['roll_weight']; ?></td>
                      <td width="" align="center" rowspan="4" style="vertical-align:middle"><div class="block_div"><? ?></div></td>
					  <td width="" align="center" rowspan="4" style="vertical-align:middle"><div class="block_div"><? ?></div></td>
					  <td width="" align="center" rowspan="4" style="vertical-align:middle"><div class="block_div"><? ?></div></td>

                      <td width="" align="left" rowspan="2"><? echo $plice_val['plies']." Plies";  ?></td>
                       <td width="">Size</td>

                   <?
					  foreach($size_id_arr as $size_id=>$size_val)
						{

					 ?>
						   <td width="<? echo $td_width; ?>" align="center" ><? echo $size_arr[$detali_data_arr[$dtls_id][$plice_id][$size_id]['size']]; ?>	</td>

					 <?
						 }
					 ?>
                      <td width="" align="right" valign="bottom" ></td>

                   </tr>
                   <tr height="20">
                     <td width="">CAD Ratio</td>
               	 <?
				  foreach($size_id_arr as $size_id=>$size_val)
				    {

				 ?>
                       <td width="<? echo $td_width; ?>" align="center" ><? echo $size_ratio_arr[$dtls_id][$size_id]; $total_size_ratio+=$size_ratio_arr[$dtls_id][$size_id];  ?></td>
                 <?
				     }
                 ?>
                      <td width="" align="right" valign="bottom" ><? echo $total_size_ratio; $total_size_ratio=0;  ?></td>

                   </tr>
                     <tr height="20">
                       <td width="" align="left" rowspan="2"><?  echo $plice_val['bundle_qty']."/Bundle";  ?></td>
                       <td width=""> Gmts Qty.
</td>
                         <?
						  foreach($size_id_arr as $size_id=>$size_val)
							{
								$total_gmt_qty_roll+=$detali_data_arr[$dtls_id][$plice_id][$size_id]['marker_qty'];
						 ?>
							   <td width="<? echo $td_width; ?>" align="center" ><? echo $detali_data_arr[$dtls_id][$plice_id][$size_id]['marker_qty'];  ?>	</td>

						 <?
							 }
							 $tot_gmts+=$total_gmt_qty_roll;
						 ?>
                      <td width="" align="right" valign="bottom" ><? echo $total_gmt_qty_roll;$total_gmt_qty_roll=0;  ?></td>


                   </tr>
              </tr>
                     <tr height="20">
                       <td width="">Bundle Qty.</td>
                         <?
						  foreach($size_id_arr as $size_id=>$size_val)
						  {
						 ?>
							   <td width="<? echo $td_width; ?>" align="center"  style="font-size:14px;">
							   <?
							   $bdl_qty=floor($detali_data_arr[$dtls_id][$plice_id][$size_id]['marker_qty']/$plice_val['bundle_qty']);
							    $extra_bdl=($detali_data_arr[$dtls_id][$plice_id][$size_id]['marker_qty']%$plice_val['bundle_qty']);
								if($extra_bdl!=0) $bdl_qty=$bdl_qty." Full & one $extra_bdl  pcs";

							    echo $bdl_qty;
							    ?>
                               </td>
						 <?
						 }
						 ?>

                   </tr>

              <?
				 $i=$i+1;
				 }
			  }

		     ?>


      </table>
      <?
      $table_height=30+($i+1)*20;
	//echo $table_height;die;
	$div_position=$table_height+420;

	$color_size_qty_arr=array();
	$color_size_sql=sql_select ("SELECT po_break_down_id,item_number_id,size_number_id,color_number_id,sum(CAST(plan_cut_qnty as INT)) as plan_cut_qnty from wo_po_color_size_breakdown
	where is_deleted=0 and status_active=1 and po_break_down_id in (".$order_id.") group by po_break_down_id,item_number_id,size_number_id,color_number_id");
	foreach($color_size_sql as $s_id)
	{
		$color_size_qty_arr[$s_id[csf('po_break_down_id')]][$s_id[csf('item_number_id')]][$s_id[csf('color_number_id')]][$s_id[csf('size_number_id')]]+=$s_id[csf('plan_cut_qnty')];
		//$tot_plan_qty+=$s_id[csf('plan_cut_qnty')];
	}

   $sql_sewing=sql_select("SELECT b.po_break_down_id,a.item_number_id,a.body_part_id,b.color_number_id,b.gmts_sizes,sum( b.cons ) AS conjumction
   FROM wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a
   WHERE a.id = b.pre_cost_fabric_cost_dtls_id  AND b.po_break_down_id in (".$order_id.") and b.cons!=0 and a.body_part_id in (1,20,125)
   GROUP BY b.po_break_down_id,a.item_number_id,a.body_part_id,b.color_number_id,b.gmts_sizes");
   $con_per_dzn=array();
   $po_item_qty_arr=array();
   $color_size_conjumtion=array();
   foreach($sql_sewing as $row_sew)
   {
		$color_size_conjumtion[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]][$row_sew[csf('body_part_id')]]['conjum']=str_replace("'","",$row_sew[csf("conjumction")]);

		$color_size_conjumtion[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]][$row_sew[csf('body_part_id')]]['plan_cut_qty']=$color_size_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]];
		$po_item_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('body_part_id')]]['plan_cut_qty']+=$color_size_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]];

		$tot_plan_qty+=$color_size_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]];
   }
   //print_r($color_size_conjumtion);
	$con_qnty=0;
	foreach($color_size_conjumtion as $p_id=>$p_value)
	{
		foreach($p_value as $i_id=>$i_value)
		{
			foreach($i_value as $c_id=>$c_value)
			{
			foreach($c_value as $s_id=>$s_value)
				{
					foreach($s_value as $b_id=>$b_value)
					{
						$order_color_size_qty=$b_value['plan_cut_qty'];
						// $order_qty=$po_item_qty_arr[$p_id][$i_id][$c_id][$b_id]['plan_cut_qty'];
						$order_qty=$tot_plan_qty;
						$order_color_size_qty_per= ($order_color_size_qty/$order_qty)*100;
						$conjunction_per= ($b_value['conjum']*$order_color_size_qty_per/100);
						$con_per_dzn[$p_id][$c_id]+=$conjunction_per;
						$con_qnty+=$conjunction_per;
					}
				}
			}
		}
	}

	$con_qnty=($con_qnty/$costing_per_qty)*12;
	$net_cons=($tot_roll_wght/$tot_gmts)*12;
	$loss_gain='&nbsp;'; $gain='&nbsp;'; $loss='&nbsp;';
	$cons_balance=$net_cons-$cad_marker_cons;
	// if($cad_marker_cons>$net_cons)
	// {
	// 	$loss_gain='Gain';
	// 	$gain=number_format($cons_balance,4);
	// }
	if($cons_balance>0)
	{
	 	$loss_gain='Loss';
	   	$loss=number_format($cons_balance,2);

	}
	else if($cons_balance<0)
	{
		$loss_gain='Gain';
		$gain=number_format(abs($cons_balance),2);
	}

	// $cons_balance=$con_qnty-$net_cons;
	// if($con_qnty>$net_cons)
	// {
	// 	$loss_gain='Gain';
	// 	$gain=number_format($cons_balance,4);
	// }
	// else if($con_qnty<$net_cons)
	// {
	// 	$loss_gain='Loss';
	// 	$loss=number_format(abs($cons_balance),4);
	// }
	?>


       <div style=" width:160px; position:absolute; margin-top:20px;   ">
          <table border="1" cellpadding="1" cellspacing="1"   width="200" class="rpt_table" rules="all">
                  <tr  height="30">
                       <td width="100">Booking<br>Consumption <br>Per Dzn</td>
                       <td width="100" align="center" ><? echo number_format($con_qnty,4); ?></td>
                  </tr>
            </table>
       </div>

        <div style=" width:160px; position:absolute; left:220px; margin-top:20px;   ">
          <table border="1" cellpadding="1" cellspacing="1"   width="200"class="rpt_table" rules="all">
                  <tr  height="30" >
                       <td width="100" >CAD Marker<br>Consumption <br>Per Dzn</td>
                       <td width="100" align="center" ><? echo $cad_marker_cons; ?></td>
                  </tr>
            </table>
       </div>
         <div style=" width:180px; position:absolute; left:440px; margin-top:20px;   ">
          <table border="1" cellpadding="1" cellspacing="1" width="180"class="rpt_table" rules="all">
                  <tr  height="30">
                       <td width="40" rowspan="2">Net<br>KGS <br>Used</td>
                       <td width="70" align="center" >KGs</td>
                       <td width="70" align="center" >G.Qty</td>
                  </tr>
                   <tr  height="30">

                       <td width="70" align="center" ><? echo $tot_roll_wght; ?></td>
                       <td width="70" align="center" ><? echo $tot_gmts; ?></td>
                  </tr>
            </table>
       </div>

        <div style=" width:450px; position:absolute; right:10px; margin-top:20px;">
          <table border="1" cellpadding="1" cellspacing="1" width="430"class="rpt_table" rules="all">
                  <!--<tr height="20">
                       <td width="80" rowspan="2">Net<br>Composition <br>Per Dzn</td>
                       <td width="70" align="center" >Net</td>
                       <td width="70" align="center" ></td>
                  </tr>
                   <tr height="20">
                       <td width="70" align="center" ><?echo number_format($net_cons,4); ?></td>
                       <td width="70" align="center" ></td>
                  </tr>-->
                  <tr height="20">
                       <td width="80">Actual Consumption Per Dzn </td>
                       <td width="100" align="center">Loss From Booking Consumption</td>
                       <td width="100" align="center">Gain From Booking Consumption</td>
                       <td width="100" align="center">Loss From Cad Consumption</td>
					   <td width="100" align="center">Gain From Cad Consumption</td>
                  </tr>
                   <tr height="20">
				       <td width="80"><? echo fn_number_format($net_cons,2);  ?></td>
                       <td width="100" align="center" ><?   ?></td>
                       <td width="100" align="center" ><? ?></td>
                       <td width="100" align="center"><? echo $loss; ?></td>
                       <td width="100" align="center"><? echo $gain; ?></td>

                  </tr>
            </table>
       </div>
	   <br><br><br>
	   <!-- Query For Total Size -->

       <!-- <div style="width:180px; position:absolute; right:0; margin-top:20px;   ">
          <table border="1" cellpadding="1" cellspacing="1" width="180" class="rpt_table" rules="all">
                  <tr>
                       <td width="100">Lay<br>Loss/Gain</td>
                       <td width="80" align="center" ><? echo $loss_gain; ?></td>
                  </tr>
            </table>
       </div> -->
       <br><br><br>
  <?


//    $sql_cut=sql_select("select a.size_number_id,a.order_quantity,a.plan_cut_qnty,a.excess_cut_perc from wo_po_color_size_breakdown a where job_no_mst='".$data[1]."' ");

   $sql_cut=sql_select("select a.id, a.color_number_id,a.size_number_id,a.item_number_id,a.order_quantity,a.plan_cut_qnty from wo_po_color_size_breakdown a where a.po_break_down_id in (".$order_id.") and a.color_number_id='$color' and a.item_number_id='$item' and a.status_active=1 and a.is_deleted=0 order by a.id,size_number_id asc");

//    echo $sql_cut_one="select a.color_number_id,a.size_number_id,sum(a.order_quantity),a.plan_cut_qnty,a.excess_cut_perc from wo_po_color_size_breakdown a where po_break_down_id in (".$order_id.") group by a.color_number_id,a.size_number_id,a.plan_cut_qnty,a.excess_cut_perc";

   $excesscutsql=sql_select("select a.job_no,a.excess_input_per from wo_booking_mst a  where job_no='".$data[1]."' and a.status_active=1 and a.is_deleted=0 ");
   $excessinputarr=array();
   foreach($excesscutsql as $v)
   {
	   $excessinputarr[$v[csf('job_no')]]['excess_input_per']=$v['EXCESS_INPUT_PER'];

   }





   $sql_size=sql_select("select  a.size_qty,a.size_id from ppl_cut_lay_bundle a,ppl_cut_lay_mst b where b.id=a.mst_id and  job_no='".$data[1]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
//    $sql_size="select sum(a.size_qty),a.size_id from ppl_cut_lay_bundle a,ppl_cut_lay_mst b where b.id=a.mst_id and  cutting_no='".$data[0]."'  group by a.size_id";
//   echo $sql_size;
  $size_id_arr=array();
   $order_arr=array();
   foreach($sql_cut as $row)
   {
	     $size_id_arr[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		// $order_arr[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
	    $order_arr[$row[csf('size_number_id')]]['order_quantity']+=$row[csf('order_quantity')];

   }
    //  print_r($size_id_arr);


   $order_size_arr=array();
   foreach($sql_size as $value)
   {
	     $order_size_arr[$value[csf('size_id')]]['size_qty']+=$value[csf('size_qty')];
   }
//    echo "<br>";
//    print_r($order_size_arr);

//    echo $sql_size;
$tbl_width=450+(count($size_id_arr)*50);


?>

   <div style="width:<?=$tbl_width+20;?>px;" align="center">
       <table width="<?=$tbl_width;?>" cellspacing="0" border="1" class="rpt_table" rules="all" >

		 <tr>
		  <th width="50">Size</th>
				<?
				foreach($size_id_arr as $size_id=>$size_val)
					{
				?>
						<td width="<? echo $td_width; ?>" align="center" ><? echo $size_library[$size_id]; ?></td>

				<?
					}
                 ?>
				 <th width="50">Total</th>
			</tr>
			<tr>
            <td width="80">Order Qty,Pcs</td>
				<?
				foreach($order_arr as $size_id=>$size_val)
					{
				?>
						<td width="<? echo $td_width; ?>" align="center" ><? echo $size_val['order_quantity']; ?>	</td>
						<? $totalorderquantity+=$size_val['order_quantity']?>

				<?
					}
                 ?>
				 <td width=""><? echo $totalorderquantity; ?></td>
			</tr>
			<tr>
			<td width="80">Plan cut qty,pcs</td>
				<?
				foreach($order_arr as $size_id=>$size_val)
					{
				?>
						<td width="<? echo $td_width; ?>" align="center" ><? $totalorder=$size_val['order_quantity']*$v[csf('excess_input_per')]/100 ; $sumorder=0; $sumorder+=$size_val['order_quantity']+$totalorder; echo round($sumorder); ?>	</td>
						<? $totalplanquantity+=$sumorder;?>


				<?
					}
                 ?>
				 <td width=""><? echo round($totalplanquantity); ?></td>
			</tr>
			<tr>
			<td width="80">Total cut qty,Pcs</td>
				<?
				foreach($size_id_arr as $size_id=>$value)
					{
				?>
						<td width="<? echo $td_width; ?>" align="center" ><? echo $order_size_arr[$size_id]['size_qty']; ?></td>
						<? $totalcutquantity+=$order_size_arr[$size_id]['size_qty'];?>
				<?
					}
                 ?>
				  <td width="50"><? echo $totalcutquantity; ?></td>
			</tr>

			<tr>
			 <td width="80">Balance,pcs</td>
			 <?
			    // $mainorder=0;
				foreach($order_arr as $size_id=>$size_val)
				{
				    $totalorder=$size_val['order_quantity']*$v[csf('excess_input_per')]/100;
					$mainorder=$size_val['order_quantity']+$totalorder;
					//  echo $mainorder;
					$totalmainorder=$mainorder-$order_size_arr[$size_id]['size_qty'];
					// echo $totalmainorder;

				?>
				<td width="<? echo $td_width; ?>" align="center" ><? echo round($totalmainorder); ?>	</td>
					<?$totalrow+=$totalmainorder;?>
				<?
					}
                 ?>

                 <td width=""><? echo round($totalrow); ?></td>
			</tr>
			<tr>

			  <td width="80">Next Ratio</td>
			  <?
				foreach($order_arr as $size_id=>$size_val)
					{
				?>
						<td width="<? echo $td_width; ?>" align="center" ><? ?>	</td>
						<? ?>

				<?
					}
                 ?>
				 <td width="" align="right"><?  ?></td>

			</tr>



	   </table>
</div>





<?

//    $sql_cut=sql_select("select a.size_number_id,a.order_quantity,a.plan_cut_qnty from wo_po_color_size_breakdown a,ppl_cut_lay_mst b where a.job_no_mst=b.job_no and cutting_no='".$data[0]."' ");
//    echo $sql_cut="select a.size_number_id,a.order_quantity,a.plan_cut_qnty from wo_po_color_size_breakdown a,ppl_cut_lay_mst b where a.job_no_mst=b.job_no and cutting_no='".$data[0]."' ";
?>
       <? echo signature_table(58, $company_id, "1100px"); ?>
	</div>
<?
   exit();
}
?>
<?

if($action=="cut_lay_entry_report_print")
{
    // extract($_REQUEST);
	$data=explode('*',$data);
	//print_r($data);
	$sql=sql_select("select id,job_no,cut_num_prefix_no,table_no,marker_length,marker_width,fabric_width,gsm,width_dia,cad_marker_cons,batch_id,company_id from ppl_cut_lay_mst where cutting_no='".$data[0]."' ");
	foreach($sql as $val)
		{
			$mst_id=$val[csf('id')];
			$company_id=$val[csf('company_id')];
			$cut_prifix=$val[csf('cut_num_prefix_no')];
			$table_no=$val[csf('table_no')];
			$marker_length=$val[csf('marker_length')];
			$marker_with=$val[csf('marker_width')];
			$fabric_with=$val[csf('fabric_width')];
			$gsm=$val[csf('gsm')];
			$dia_width=$val[csf('width_dia')];
			$txt_batch=$val[csf('batch_id')];
			$cad_marker_cons=$val[csf('cad_marker_cons')];
			$job_no=$val[csf('job_no')];
		}

	$costing_per=return_field_value("costing_per","wo_pre_cost_mst", "job_no='$job_no'");
	if($costing_per==1)
	{
		$costing_per_qty=12;
	}
	else if($costing_per==2)
	{
		$costing_per_qty=1;
	}
	else if($costing_per==3)
	{
		$costing_per_qty=24;
	}
	else if($costing_per==4)
	{
		$costing_per_qty=36;
	}
	else if($costing_per==5)
	{
		$costing_per_qty=48;
	}

	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$comp_name=return_field_value("company_short_name","lib_company", "id=$company_id");
	$sql_buyer_arr=sql_select("select buyer_name,style_ref_no from  wo_po_details_master where job_no='$data[1]'");
	$sql_order=sql_select("select order_ids,gmt_item_id,order_qty from ppl_cut_lay_dtls where mst_id='$mst_id'");
	$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	$color_arr=return_library_array( "select id,color_name  from  lib_color", "id", "color_name"  );
	$order_number_arr=return_library_array( "select id, po_number from wo_po_break_down where job_no_mst='$data[1]'",'id','po_number');
	$table_no_library=return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );
	//print_r($sql_order);
	$order_number=""; $order_id='';
	foreach($sql_order as $order_val)
	{
		$item_name=$order_val[csf('gmt_item_id')];
		$order_qty+=$order_val[csf('order_qty')];
		if($order_id!="")
		{
			$order_id.=",".$order_val[csf('order_ids')];
		}
		else
		{
			$order_id=$order_val[csf('order_ids')];
		}
	}
	$order_ids=array_unique(explode(",",$order_id));
	foreach($order_ids as $poId)
	{
		if($order_number!="")
		{
			$order_number.=", ".$order_number_arr[$poId];
		}
		else
		{
			$order_number=$order_number_arr[$poId];
		}
	}

	?>
    <div style="width:1100px; position:relative">
    <div style=" width:500; height:200px; position:absolute; left:300px; top:0; ">
        <table width="500" cellspacing="0" align="center">
            <tr>
                <td  align="center" style="font-size:22px; font-weight:bold;"><strong><? echo $company_library[$company_id]; ?></strong></td>
            </tr>
            <tr>
                <td  align="center" style="font-size:18px; font-weight:bold;"><strong>LAY CHART & CONSUMPTION REPORT</strong></td>
            </tr>
       </table>

    </div>
    <div style=" width:200; height:40px; position:absolute; right:0; top:50px; ">Date:  ......../......../.......... </div>
    <div style=" width:200; height:60px; position:absolute; right:0; top:90px; " id="barcode_img_id"> </div>
    <div style=" top:80px; width:270; height:200px; position:absolute; left:0; ">
	<table border="1"  cellspacing="0"  width="260"class="rpt_table" rules="all">
         <tr>
              <td width="80">Buyer</td><td width="180" align="center"><? echo $buyer_arr[$sql_buyer_arr[0][csf('buyer_name')]]; ?></td>
         </tr>
         <tr>
              <td>Job No</td><td align="center"> <? echo $data[1]; ?></td>
         </tr>
         <tr>
              <td>Style</td><td align="center"> <? echo $sql_buyer_arr[0][csf('style_ref_no')]; ?></td>
         </tr>
         <tr>
              <td>Item Name</td> <td align="center"><? echo $garments_item[$item_name]; ?></td>
        </tr>
         <tr>
              <td>Order No</td><td align="center"><p> <? echo $order_number; ?></p></td>
         </tr>
         <tr>
              <td>Order Qty</td><td align="right"><? echo $order_qty; ?></td>
         </tr>

    </table>
    </div>
	<div  style="width:550; position:absolute; height:30px; top:70px; left:280px">
    	<table>
        	<tr>
            	<td><b>Working Company: </b></td>
                <td width="260"><? echo $company_library[$data[2]]; ?> </td>
                <td><b>Location: </b></td>
                <td><? echo $location_arr[$data[3]]; ?> </td>
            </tr>
        </table>


	 </div>
    <div  style="width:250; position:absolute; height:30px; top:118px; left:280px">
          <table border="1" cellpadding="1" cellspacing="1"   width="220"class="rpt_table" rules="all">
              <tr >
              <td width="170"> CAD Fabric Width/Dia</td><td width="50" align="center" colspan="2"><? echo $fabric_with; ?></td>
             </tr>
          </table>
    </div>


   <div  style="width:250; position:absolute; height:30px; top:160px; left:280px">
      <table border="1" cellpadding="1" cellspacing="1"   width="220"class="rpt_table" rules="all">
          <tr >
          <td width="170">CAD GSM</td><td width="50" align="center" colspan="2"><? echo $gsm; ?></td>
         </tr>
      </table>
    </div>
    <div  style="width:300; position:absolute; height:100px; top:280px; left:280px">
      <table border="1" cellpadding="1" cellspacing="1"   width="300"class="rpt_table" rules="all">
          <tr height="20">
          <td width="80">Table No</td>
          <td width="75" align="center"><? echo $table_no_library[$table_no]; ?></td>
          <td width="75" align="center">Batch No </td>
          <td width="80" align="center">Dia(Tube<br>/Open)</td>
         </tr>
         <tr height="30">
          <td width="80">Cutting No</td>
          <td width="75" align="center"><? echo $comp_name."-".$cut_prifix; ?></td>
          <td width="75" align="center">  <? echo $txt_batch; ?></td>
          <td width="80" align="center"> <? echo $fabric_typee[$dia_width]; ?></td>
         </tr>
      </table>
    </div>

     <div  style="width:200; position:absolute; height:400px; top:164px; left:580px">
       <table border="1" cellpadding="1" cellspacing="1"   width="200"class="rpt_table" rules="all">
          <tr height="30">
          <td width="90">Sperading Operators</td>
          <td width="100" align="center"></td>
         </tr>
         <tr height="30">
          <td width="">Checked by Marker Man</td>
          <td width="" align="center"></td>
         </tr>
           <tr height="30">
          <td width="90">Cutter Man-1</td>
          <td width="100" align="center"></td>
         </tr>
         <tr height="43">
          <td width="">Cutter Man-2</td>
          <td width="" align="center"></td>
         </tr>
          <tr height="30">
          <td width="">Cutter Man-3</td>
          <td width="" align="center"></td>
         </tr>
      </table>
    </div>


    <div style=" width:300; position:absolute; top:175px; right:0px; ">
	<table border="1" cellpadding="1" cellspacing="1"   width="300"class="rpt_table" rules="all">
         <tr height="30">
              <td width="100"><strong>Line Q.I</strong></td><td width="200" align="center" colspan="2"></td>
         </tr>
         <tr height="30">
              <td><strong>Jr. DQ.C</strong></td><td align="center" colspan="2"></td>
         </tr>
         <tr height="30">
              <td><strong>Checked By Q.C</strong></td> <td align="center" colspan="2"></td>
        </tr >
         <tr height="30">
              <td>Start Time</td><td align="center" width="100"></td><td align="center" width="100"><strong>Total Time Taken</strong></td>
         </tr >
         <tr height="30">
              <td>End Time</td><td align="center" width="100"></td><td align="center" width="100"></td>
         </tr>

    </table>
    </div>
    <div style=" width:270; position:absolute; top:250px;  ">
	<div style=" float:left; text-align:center; margin-top:20px; width:80px;"><Strong>STEP LAY DETAILS</Strong></div>
    <div style=" float:right;width:190px;">
         <div style="  width:90px; background-color:#666666; color:white;"><Strong>Step-1</Strong></div>
        <table border="1" cellpadding="1" cellspacing="1"   width="180"class="rpt_table" rules="all">
         <tr height="30">
              <td width="80">CAD Marker Length</td><td width="80" align="center" ><? echo $marker_length;  ?></td>
         </tr>
         <tr height="30"  >
              <td>CAD Marker Width</td><td align="center" ><? echo $marker_with;  ?></td>
         </tr>


    </table>
    </div>
    </div>
 </div>

     <script type="text/javascript" src="../../../js/jquery.js"></script>
     <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
     <script>

	function generateBarcode( valuess ){

			var value = valuess;//$("#barcodeValue").val();
		// alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();

			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 30,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			 value = {code:value, rect: false};

			$("#barcode_img_id").show().barcode(value, btype, settings);

		}
	   generateBarcode('<? echo $data[0]; ?>');
	 </script>


 <div style=" width:1100px; position:absolute; top:385px; ">
   <style type="text/css">
            .block_div {
                    width:auto;
                    height:auto;
                    text-wrap:normal;
					font-size:10.5px;
                    vertical-align:bottom;
                    display: block;

                    -webkit-transform: rotate(-90deg);
                    -moz-transform: rotate(-90deg);
            }

        </style>
   <?

     $sql_size_ration=sql_select("select a.id,b.size_id,b.size_ratio from
     ppl_cut_lay_size_dtls b,ppl_cut_lay_dtls a where a.id=b.dtls_id  and a.mst_id=$mst_id and b.status_active=1  and  b.is_deleted=0 and a.status_active=1
     and a.is_deleted=0 ");
	 $detali_data_arr=array();
	 $plice_data_arr=array();
	 $size_id_arr=array();
	 $total_gmt_qty=array();
	 $grand_total=0;
	 $size_qty=0;
	 $size_ratio_arr=array();
	 foreach($sql_size_ration as $size_val)
	 {
	  	$size_ratio_arr[$size_val[csf('id')]][$size_val[csf('size_id')]]=$size_val[csf('size_ratio')];
	 }

	$sql_main_qry=sql_select("select c.id,a.id,a.color_id,c.size_id,c.roll_no,sum(c.roll_wgt) as roll_weight,c.plies, sum(c.size_qty) as size_qty,c.roll_id
	 from  ppl_cut_lay_roll_dtls c,ppl_cut_lay_dtls a
	 where  a.id=c.dtls_id and a.mst_id=$mst_id and c.status_active=1  and  c.is_deleted=0 and a.status_active=1 and a.is_deleted=0
	 group by c.id,a.id,a.color_id,c.size_id,c.roll_no,c.plies,c.roll_id
	 order by a.id,c.id");
	 $detali_data_arr=array();
	 $plice_data_arr=array();
	 $size_id_arr=array();
	 $total_gmt_qty=array();
	 $grand_total=0;
	 $size_qty=0;
	 foreach($sql_main_qry as $main_val)
	 {
	    $size_id_arr[$main_val[csf('size_id')]]=$main_val[csf('size_id')];
		$plice_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]]['plies']=$main_val[csf('plies')];
		$detali_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]][$main_val[csf('size_id')]]['size']=$main_val[csf('size_id')];
		$detali_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]][$main_val[csf('size_id')]]['marker_qty']=$main_val[csf('size_qty')];
		$total_gmt_qty[$main_val[csf('id')]][$main_val[csf('roll_id')]]['gmt_qty']+=$main_val[csf('size_qty')];
		$grand_total+=$main_val[csf('marker_qty')];
		$size_qty=return_field_value("size_qty","ppl_cut_lay_bundle","mst_id =$mst_id AND dtls_id =".$main_val[csf('id')]." ");
		$plice_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]]['bundle_qty']=$size_qty;
		$plice_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]]['color']=$main_val[csf('color_id')];
		$plice_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]]['roll_no']=$main_val[csf('roll_no')];
		$plice_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]]['roll_weight']=$main_val[csf('roll_weight')];
	 }
 //print_r($plice_data_arr);die;
   $col_span=count($size_id_arr);
   $td_width=450/$col_span;

  // echo $td_width;die;

   ?>
    <table border="1" cellpadding="1" cellspacing="1"   width="1100"class="rpt_table" rules="all">
          <tr height="30" >
          <td width="30">SL</td>
          <td width="60" align="center">Roll No </td>
          <td width="60" align="center">Roll Kgs </td>
          <td width="50" align="center">Color </td>
          <td width="70"> Plies & Pcs/Bundle</td>
           <td width="80">Particulars</td>
          <td width="470" align="center" colspan="<? echo $col_span; ?>">Size, Ratio and Garments Qty.</td>
          <td width="50" align="center">Total Gmts</td>
          <td width="70" align="center">Per Roll Cons</td>
           <td width="60">Cut Out Faults</td>
          <td width="60" align="center">End of Roll Length</td>
          <td width="60" align="center">Total Unused Length </td>
         </tr>
        <?
		 $i=1; $tot_gmts=0; $tot_roll_wght=0;
		  foreach($plice_data_arr as $dtls_id=>$dtls_val)
			  {
				foreach($dtls_val as $plice_id=>$plice_val)
				 {
					 $tot_roll_wght+=$plice_val['roll_weight'];
				 ?>
                 <tr height="20">
                      <td width="" rowspan="4"><? echo $i;  ?></td>
                      <td width="" align="center" rowspan="4"><? echo $plice_val['roll_no']; ?> </td>
                      <td width="" align="center" rowspan="4"><? echo $plice_val['roll_weight']; ?></td>
                      <td width="" align="center" rowspan="4" style="vertical-align:middle"><div class="block_div"><? echo $color_arr[$plice_val['color']];  ?></div></td>
                      <td width="" align="left" rowspan="2"><? echo $plice_val['plies']." Plies";  ?></td>
                       <td width="">Size</td>

                   <?
					  foreach($size_id_arr as $size_id=>$size_val)
						{
					 ?>
						   <td width="<? echo $td_width; ?>" align="center" ><? echo $size_arr[$detali_data_arr[$dtls_id][$plice_id][$size_id]['size']];  ?>	</td>

					 <?
						 }
					 ?>
                      <td width="" align="right" valign="bottom" ></td>
                      <td width="" align="center"></td>
                      <td width=""></td>
                      <td width="" align="center"></td>
                      <td width="" align="center"> </td>
                   </tr>
                   <tr height="20">
                     <td width="">CAD Ratio</td>
               	 <?
				  foreach($size_id_arr as $size_id=>$size_val)
				    {
						$total_size_ratio+=$size_ratio_arr[$dtls_id][$size_id]['size_ratio'];
				 ?>
                       <td width="<? echo $td_width; ?>" align="center" ><? echo $size_ratio_arr[$dtls_id][$size_id]['size_ratio'];  ?>	</td>
                 <?
				     }
                 ?>
                      <td width="" align="right" valign="bottom" ><? echo $total_size_ratio; $total_size_ratio=0;  ?></td>
                      <td width="" align="center"></td>
                      <td width=""></td>
                      <td width="" align="center"></td>
                      <td width="" align="center"> </td>
                   </tr>
                     <tr height="20">
                       <td width="" align="left" rowspan="2"><?  echo $plice_val['bundle_qty']."/Bundle";  ?></td>
                       <td width=""> Gmts Qty.
</td>
                         <?
						  foreach($size_id_arr as $size_id=>$size_val)
							{
								$total_gmt_qty_roll+=$detali_data_arr[$dtls_id][$plice_id][$size_id]['marker_qty'];
						 ?>
							   <td width="<? echo $td_width; ?>" align="center" ><? echo $detali_data_arr[$dtls_id][$plice_id][$size_id]['marker_qty'];  ?>	</td>

						 <?
							 }
							 $tot_gmts+=$total_gmt_qty_roll;
						 ?>
                      <td width="" align="right" valign="bottom" ><? echo $total_gmt_qty_roll;$total_gmt_qty_roll=0;  ?></td>
					  <td width="" align="center"></td>
                      <td width=""></td>
                      <td width="" align="center"></td>
                      <td width="" align="center"> </td>
                   </tr>
              </tr>
                     <tr height="20">
                       <td width="">Bundle Qty.</td>
                         <?
						  foreach($size_id_arr as $size_id=>$size_val)
						  {
						 ?>
							   <td width="<? echo $td_width; ?>" align="center"  style="font-size:14px;">
							   <?
							   $bdl_qty=floor($detali_data_arr[$dtls_id][$plice_id][$size_id]['marker_qty']/$plice_val['bundle_qty']);
							    $extra_bdl=($detali_data_arr[$dtls_id][$plice_id][$size_id]['marker_qty']%$plice_val['bundle_qty']);
								if($extra_bdl!=0) $bdl_qty=$bdl_qty." Full & one $extra_bdl  pcs";

							    echo $bdl_qty;
							    ?>
                               </td>
						 <?
						 }
						 ?>
					  <td width="" align="center"></td>
                      <td width=""></td>
                      <td width="" align="center"></td>
                      <td width="" align="center"> </td>
                   </tr>

              <?
				 $i=$i+1;
				 }
			  }

		     ?>


      </table>
      <?
      $table_height=30+($i+1)*20;
	//echo $table_height;die;
	$div_position=$table_height+420;

	$color_size_qty_arr=array();
	$color_size_sql=sql_select ("SELECT po_break_down_id,item_number_id,size_number_id,color_number_id,sum(CAST(plan_cut_qnty as INT)) as plan_cut_qnty from wo_po_color_size_breakdown
	where is_deleted=0 and status_active=1 and po_break_down_id in (".$order_id.") group by po_break_down_id,item_number_id,size_number_id,color_number_id");
	foreach($color_size_sql as $s_id)
	{
		$color_size_qty_arr[$s_id[csf('po_break_down_id')]][$s_id[csf('item_number_id')]][$s_id[csf('color_number_id')]][$s_id[csf('size_number_id')]]+=$s_id[csf('plan_cut_qnty')];
		//$tot_plan_qty+=$s_id[csf('plan_cut_qnty')];
	}

   $sql_sewing=sql_select("SELECT b.po_break_down_id,a.item_number_id,a.body_part_id,b.color_number_id,b.gmts_sizes,sum( b.cons ) AS conjumction
   FROM wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a
   WHERE a.id = b.pre_cost_fabric_cost_dtls_id  AND b.po_break_down_id in (".$order_id.") and b.cons!=0 and a.body_part_id in (1,20,21,25,125)
   GROUP BY b.po_break_down_id,a.item_number_id,a.body_part_id,b.color_number_id,b.gmts_sizes");
	//echo  $sql_sewing;die;
   $con_per_dzn=array();
   $po_item_qty_arr=array();
   $color_size_conjumtion=array();
   foreach($sql_sewing as $row_sew)
   {
		$color_size_conjumtion[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]][$row_sew[csf('body_part_id')]]['conjum']=str_replace("'","",$row_sew[csf("conjumction")]);

		$color_size_conjumtion[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]][$row_sew[csf('body_part_id')]]['plan_cut_qty']=$color_size_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]];
		$po_item_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('body_part_id')]]['plan_cut_qty']+=$color_size_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]];

		$tot_plan_qty+=$color_size_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]];
   }
   //print_r($color_size_conjumtion);
	$con_qnty=0;
	foreach($color_size_conjumtion as $p_id=>$p_value)
	{
		foreach($p_value as $i_id=>$i_value)
		{
			foreach($i_value as $c_id=>$c_value)
			{
			foreach($c_value as $s_id=>$s_value)
				{
					foreach($s_value as $b_id=>$b_value)
					{
						$order_color_size_qty=$b_value['plan_cut_qty'];
						// $order_qty=$po_item_qty_arr[$p_id][$i_id][$c_id][$b_id]['plan_cut_qty'];
						$order_qty=$tot_plan_qty;
						$order_color_size_qty_per= ($order_color_size_qty/$order_qty)*100;
						$conjunction_per= ($b_value['conjum']*$order_color_size_qty_per/100);
						$con_per_dzn[$p_id][$c_id]+=$conjunction_per;
						$con_qnty+=$conjunction_per;
					}
				}
			}
		}
	}

	$con_qnty=($con_qnty/$costing_per_qty)*12;
	$net_cons=($tot_roll_wght/$tot_gmts)*12;
	$loss_gain='&nbsp;'; $gain='&nbsp;'; $loss='&nbsp;';
	/*$cons_balance=$cad_marker_cons-$net_cons;
	if($cad_marker_cons>$net_cons)
	{
		$loss_gain='Gain';
		$gain=number_format($cons_balance,4);
	}
	else if($cad_marker_cons<$net_cons)
	{
		$loss_gain='Loss';
		$loss=number_format(abs($cons_balance),4);
	}*/

	$cons_balance=$con_qnty-$net_cons;
	if($con_qnty>$net_cons)
	{
		$loss_gain='Gain';
		$gain=number_format($cons_balance,4);
	}
	else if($con_qnty<$net_cons)
	{
		$loss_gain='Loss';
		$loss=number_format(abs($cons_balance),4);
	}
	?>


       <div style=" width:160px; position:absolute; margin-top:20px;   ">
          <table border="1" cellpadding="1" cellspacing="1"   width="200" class="rpt_table" rules="all">
                  <tr  height="30">
                       <td width="100">Booking<br>Consumption <br>Per Dzn</td>
                       <td width="100" align="center" ><? echo number_format($con_qnty,4); ?></td>
                  </tr>
            </table>
       </div>

        <div style=" width:160px; position:absolute; left:220px; margin-top:20px;   ">
          <table border="1" cellpadding="1" cellspacing="1"   width="200"class="rpt_table" rules="all">
                  <tr  height="30" >
                       <td width="100" >CAD Marker<br>Consumption <br>Per Dzn</td>
                       <td width="100" align="center" ><? echo $cad_marker_cons; ?></td>
                  </tr>
            </table>
       </div>
         <div style=" width:180px; position:absolute; left:440px; margin-top:20px;   ">
          <table border="1" cellpadding="1" cellspacing="1" width="180"class="rpt_table" rules="all">
                  <tr  height="30">
                       <td width="40" rowspan="2">Net<br>KGS <br>Used</td>
                       <td width="70" align="center" >KGs</td>
                       <td width="70" align="center" >G.Qty</td>
                  </tr>
                   <tr  height="30">

                       <td width="70" align="center" ><? echo $tot_roll_wght; ?></td>
                       <td width="70" align="center" ><? echo $tot_gmts; ?></td>
                  </tr>
            </table>
       </div>

        <div style=" width:230px; position:absolute; right:191px; margin-top:20px;   ">
          <table border="1" cellpadding="1" cellspacing="1" width="220"class="rpt_table" rules="all">
                  <!--<tr height="20">
                       <td width="80" rowspan="2">Net<br>Composition <br>Per Dzn</td>
                       <td width="70" align="center" >Net</td>
                       <td width="70" align="center" ></td>
                  </tr>
                   <tr height="20">
                       <td width="70" align="center" ><?echo number_format($net_cons,4); ?></td>
                       <td width="70" align="center" ></td>
                  </tr>-->
                  <tr height="20">
                       <td width="80" rowspan="2">Net<br>Consumption <br>Per Dzn</td>
                       <td width="70" align="center">Net</td>
                       <td width="70" align="center">Loss</td>
                       <td width="70" align="center">Gain</td>
                  </tr>
                   <tr height="20">
                       <td width="70" align="center" ><? echo number_format($net_cons,4); ?></td>
                       <td width="70" align="center" ><? echo $loss; ?></td>
                       <td width="70" align="center"><? echo $gain; ?></td>
                  </tr>
            </table>
       </div>
       <div style="width:180px; position:absolute; right:0; margin-top:20px;   ">
          <table border="1" cellpadding="1" cellspacing="1" width="180" class="rpt_table" rules="all">
                  <tr>
                       <td width="100">Lay<br>Loss/Gain</td>
                       <td width="80" align="center" ><? echo $loss_gain; ?></td>
                  </tr>
            </table>
       </div>
       <br><br><br>
       <? echo signature_table(58, $company_id, "1100px"); ?>
	</div>
<?
   exit();
}