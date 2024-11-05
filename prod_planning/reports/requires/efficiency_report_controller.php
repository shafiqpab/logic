<?php
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
if (!function_exists('pre')) 
{
	function pre($array){
		echo "<pre>";
		print_r($array);
		echo "</pre>";
	}
}

if($action == 'load_drop_down_working_company') {
	echo create_drop_down('cbo_working_company_name', 142, "select id, company_name from lib_company comp where status_active=1 and is_deleted=0 order by company_name", 'id,company_name', 0, '-- Select  --', $selected, '');
	exit();
}

if($action == 'load_drop_down_location') {
	echo create_drop_down('cbo_location_name', 142, "select id, location_name from lib_location where is_deleted=0 and status_active=1 and company_id in($data) order by location_name", 'id,location_name', 0, '', 0, '');
	exit();
}

if($action == 'load_drop_down_working_floor') {
	echo create_drop_down('cbo_working_floor_id', 142, "select id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0 and location_id in($data) and production_process=5 order by floor_name", 'id,floor_name', 0, '', $selected, '');
	exit();	
}
if($action == 'load_drop_down_buyer') {
	echo create_drop_down( "cbo_buyer_name", 100, "select distinct buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name", 'id,buyer_name', 1, "-- All Buyer --", $selected, '' ,0); 
	exit();
}

if($action=='load_drop_down_sewing_output_line')
{
	echo create_drop_down('cbo_sewing_line', 110, "select distinct id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and location_name in ($data) order by line_name", 'id,line_name', 1, 'Select Line', $selected, '');
	exit();
}

if($action == 'style_search_popup') {
	extract($_REQUEST);
	echo load_html_head_contents('Popup Info', '../../../', 1, 1, $unicode);
	//echo $style_id;die;

	?>
   <script>
		function js_set_value( str ) {
			$('#txt_selected_no').val(str);
			parent.emailwindow.hide();
		}
    </script>

	<?php
		$buyer=str_replace("'","",$buyer);
		$company=str_replace("'","",$company);
		$job_year=str_replace("'","",$job_year);
		$company_cond="";
		$buyer_cond="";

		if($company!=0) $company_cond=" and a.company_name = $company";
		if($buyer!=0) $buyer_cond=" and a.buyer_name in($buyer)";
		if($db_type==0) {
			if($job_year!=0) $job_year_cond=" and year(a.insert_date)=$job_year"; else $job_year_cond="";
			$select_date=" year(a.insert_date)";
		}
		else if($db_type==2) {
			if($job_year!=0) $job_year_cond=" and to_char(a.insert_date,'YYYY')=$job_year"; else $job_year_cond="";
			$select_date=" to_char(a.insert_date,'YYYY')";
		}
		
		$sql = "select a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num,$select_date as year
			from wo_po_details_master a
			where a.status_active=1 $company_cond $buyer_cond $job_year_cond and is_deleted=0
			order by job_no_prefix_num desc, $select_date";
		// echo $sql;
		echo create_list_view("list_view", "Style Ref No,Job No,Year","160,90,100","410","300",0, $sql , "js_set_value", "id,job_no,style_ref_no", "", 1, "0", $arr, "style_ref_no,job_no_prefix_num,year", "","setFilterGrid('list_view',-1)","0","","") ;	
		//echo "<input type='hidden' id='txt_selected_id' />";
		//echo "<input type='hidden' id='txt_selected' />";
		echo "<input type='hidden' id='txt_selected_no' />";
		
	?>
	    <script language="javascript" type="text/javascript">
			var style_no='<?php echo $txt_ref_no;?>';
			var style_id='<?php echo $txt_style_ref_id;?>';
			var style_des='<?php echo $txt_style_ref_no;?>';
			//alert(style_id);
			if(style_no!="")
			{
				style_no_arr=style_no.split(",");
				style_id_arr=style_id.split(",");
				style_des_arr=style_des.split(",");
				var str_ref="";
				for(var k=0;k<style_no_arr.length; k++)
				{
					str_ref=style_no_arr[k]+'_'+style_id_arr[k]+'_'+style_des_arr[k];
					js_set_value(str_ref);
				}
			}
		</script>
    
    <?php
	
	exit();
}

if($action=='order_search_popup')
{
	echo load_html_head_contents('Order No Info', '../../../', 1, 1,'','','');
	extract($_REQUEST);
	?>
     
	<script>		
		function js_set_value( orderNo ) {
			$('#hdnOrderNo').val( orderNo );
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
                    <th>Date Range</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
                    <input type="hidden" name="hdnOrderNo" id="hdnOrderNo" value="" />
                    <input type="hidden" name="hdnOrderId" id="hdnOrderId" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <?php
								echo create_drop_down('cbo_buyer_name', 140, "select distinct buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($companyID) $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buy.buyer_name", 'id,buyer_name', 1, '-- All Buyer--', 0, '', 0);
							?>
                        </td>                 
                        <td align="center">	
                    	<?php
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<?php echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+'<?php echo $job_no.'**'.$job_year; ?>', 'create_order_no_search_list_view', 'search_div', 'efficiency_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    	</td>
                    </tr>
                    <tr>
                        <td colspan="5" height="20" valign="middle"><?php echo load_month_buttons(1); ?></td>
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
<?php
	exit(); 
}

if($action == 'create_order_no_search_list_view') {
	$data=explode('**',$data);
	$working_company_id=$data[0];
	$buyer_name = $data[1];
	$job_no=$data[6];
	$job_year=$data[7];
	/*$txt_date_from
	$txt_date_to*/
	$year = date('Y');

	if ($txt_date_from == '' && $txt_date_to == '') {
		$txt_date_from = "01-Jan-$year";
		$txt_date_to = "31-Dec-$year";
	}

	if($job_no!='') $job_no_cond="and a.job_no='$job_no'";else $job_no_cond="";

	if($buyer_name != 0)
		$buyer_id_cond=" and a.buyer_name=$buyer_name";
	
	
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
	$company_library=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$arr=array (0=>$company_library,1=>$buyer_arr);
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later	

	$sql = "select b.id, a.buyer_name, a.company_name, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.pub_shipment_date, b.po_number, b.po_number_acc, b.po_quantity, b.grouping, b.file_no, c.po_break_down_id
		from wo_po_details_master a, wo_po_break_down_vw b, pro_garments_production_mst c
		where  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no=b.job_no_mst and b.id=c.po_break_down_id  and a.company_name in($working_company_id) and $search_field like '$search_string' $buyer_id_cond $job_no_cond $date_cond
		group by b.id, a.buyer_name, a.company_name, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.pub_shipment_date, b.po_number, b.po_number_acc, b.po_quantity, b.grouping, b.file_no, c.po_break_down_id
		order by b.pub_shipment_date asc";

	echo create_list_view('tbl_list_search', 'Company,Buyer Name,Job No,Style Ref. No,Order No,Shipment Date', '80,80,100,100,140', '760', '220', 0, $sql, 'js_set_value', 'po_number', '', 1, 'company_name,buyer_name,0,0,0,0', $arr, 'company_name,buyer_name,job_no,style_ref_no,po_number,pub_shipment_date', '', '', '0,0,0,0,0,3');
   exit(); 
}

if ($action == 'generate_report') 
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($type == 1)
	{
		function get_date_range($first, $last, $step = '+1 day', $output_format = 'd-M-Y') {
			$dates = array();
			$current = strtotime($first);
			$last = strtotime($last);

			while( $current <= $last ) {
				$dates[] = date($output_format, $current);
				$current = strtotime($step, $current);
			}

			return $dates;
		}
		$cbo_company_name = str_replace("'", '', $cbo_company_name);
		$cbo_working_company_name = str_replace("'", '', $cbo_working_company_name);
		$cbo_location_name = str_replace("'", '', $cbo_location_name);
		$cbo_buyer_name = str_replace("'", '', $cbo_buyer_name);
		$txt_style_no = str_replace("'", '', $txt_style_no);
		$txt_job_no = str_replace("'", '', $txt_job_no);
		$txt_order_no = str_replace("'", '', $txt_order_no);
		$cbo_sewing_line = str_replace("'", '', $cbo_sewing_line);
		$txt_date_from = str_replace("'", '', $txt_date_from);
		$txt_date_to = str_replace("'", '', $txt_date_to);
		$cbo_year_selection = str_replace("'", '', $cbo_year_selection);

		$buyer_library = return_library_array( "select id, buyer_name from lib_buyer", 'id', 'buyer_name');
		$company_library = return_library_array( "select id, company_name from lib_company", 'id', 'company_name');
		$location_library = return_library_array("select id, location_name from lib_location where status_active = 1", 'id', 'location_name');
		$line_library = return_library_array("select id,line_name from lib_sewing_line where status_active=1 and is_deleted=0", 'id', 'line_name');
		$prod_reso_library = return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');

		$sql_cond = '';
		$sql_resource_cond = '';

		$wo_result = array();
		$production_arr = array();
		$resource_arr = array();
		$unit_summery_arr = array();
		$buyer_summery_arr = array();
		$smv_arr = array();

		if ($txt_date_from == '' && $txt_date_to == '') {
			$txt_date_from = "01-Jan-$cbo_year_selection";
			$txt_date_to = "31-Dec-$cbo_year_selection";
		}

		if($db_type==0)
		{
			$date_cond="and a.production_date between '".change_date_format(trim($txt_date_from),"yyyy-mm-dd")."' and '".change_date_format(trim($txt_date_to),"yyyy-mm-dd")."'";
			$resource_date_cond="and b.from_date between '".change_date_format(trim($txt_date_from),"yyyy-mm-dd")."' and '".change_date_format(trim($txt_date_to),"yyyy-mm-dd")."'";
		}
		else
		{
			$date_cond="and a.production_date between '".change_date_format(trim($txt_date_from),'','',1)."' and '".change_date_format(trim($txt_date_to),'','',1)."'";
			$resource_date_cond="and b.to_date between '".change_date_format(trim($txt_date_from),'','',1)."' and '".change_date_format(trim($txt_date_to),'','',1)."'";
		}

		if($cbo_buyer_name != 0) {
			$sql_cond .= " and c.buyer_name in($cbo_buyer_name)";
		}

		if($cbo_company_name != 0) {
			$sql_cond .= " and a.company_id in($cbo_company_name)";
		}

		if($cbo_working_company_name != '') {
			$sql_cond .= " and a.serving_company in($cbo_working_company_name)";
			$sql_resource_cond .= " and a.company_id in($cbo_working_company_name)";
		}

		if($txt_style_no != '') {
			$sql_cond .= " and c.style_ref_no = '$txt_style_no'";
		}

		if($txt_job_no != '') {
			$sql_cond .= " and c.job_no = '$txt_job_no'";
		}

		if($txt_order_no != '') {
			$sql_cond .= " and d.po_number = '$txt_order_no'";
		}

		if($cbo_sewing_line != 0) {
			$sql_cond .= " and a.sewing_line = $cbo_sewing_line";
		}

		/*$production_sql = "select distinct a.company_id, a.location, a.po_break_down_id, a.item_number_id, a.production_date, a.sewing_line, a.production_quantity, c.buyer_name, c.company_name, c.location_name, c.style_ref_no, c.job_no, c.job_quantity, d.po_number, d.po_quantity, c.set_smv
			from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_details_master c, wo_po_break_down d
			where a.production_type in (5) and a.id = b.mst_id $date_cond and c.job_no=d.job_no_mst and d.id=a.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and d.status_active=1 and d.is_deleted=0 $sql_cond";*/
		$production_sql = "select distinct a.id as prod_mst_id, a.company_id, a.serving_company, a.location, a.po_break_down_id, a.item_number_id, a.production_date, a.sewing_line, a.production_quantity, b.production_type, c.buyer_name, c.company_name, c.location_name, c.style_ref_no, c.job_no, c.job_quantity, d.po_number, d.po_quantity, c.set_smv, a.prod_reso_allo, c.set_break_down
			from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_details_master c, wo_po_break_down d
			where a.production_type in (5) and a.id = b.mst_id $date_cond and c.job_no=d.job_no_mst and d.id=a.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and d.status_active=1 and d.is_deleted=0 $sql_cond";
		// echo $production_sql;

		$production_result = sql_select($production_sql);

		foreach ($production_result as $row) {
			$set_arr = explode('__', $row[csf('set_break_down')]);

			if($set_arr[0]=='') {
				$set_arr=array();
			}
			if ( count($set_arr)>0) {
				foreach( $set_arr as $set) {
					$data=explode('_',$set);
					$smv_arr[$row[csf('buyer_name')]][$row[csf('serving_company')]][$row[csf('location')]][$row[csf('style_ref_no')]][$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('production_date')]][$row[csf('sewing_line')]][$data[0]]['set_smv'] = $data[2];
					// $smv_arr[$buyer_name][$company][$location][$style][$jobNo][$poNo][$productionDate][$sewingLine][$itemNumber]['set_smv']
				}
			}

			$production_arr[$row[csf('buyer_name')]][$row[csf('serving_company')]][$row[csf('location')]][$row[csf('style_ref_no')]][$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('item_number_id')]][$row[csf('production_date')]][$row[csf('sewing_line')]]['production_quantity'] += $row[csf('production_quantity')];
			$production_arr[$row[csf('buyer_name')]][$row[csf('serving_company')]][$row[csf('location')]][$row[csf('style_ref_no')]][$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('item_number_id')]][$row[csf('production_date')]][$row[csf('sewing_line')]]['job_quantity'] = $row[csf('job_quantity')];
			$production_arr[$row[csf('buyer_name')]][$row[csf('serving_company')]][$row[csf('location')]][$row[csf('style_ref_no')]][$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('item_number_id')]][$row[csf('production_date')]][$row[csf('sewing_line')]]['po_quantity'] = $row[csf('po_quantity')];
			$production_arr[$row[csf('buyer_name')]][$row[csf('serving_company')]][$row[csf('location')]][$row[csf('style_ref_no')]][$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('item_number_id')]][$row[csf('production_date')]][$row[csf('sewing_line')]]['smv'] = $row[csf('set_smv')];
			$production_arr[$row[csf('buyer_name')]][$row[csf('serving_company')]][$row[csf('location')]][$row[csf('style_ref_no')]][$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('item_number_id')]][$row[csf('production_date')]][$row[csf('sewing_line')]]['prod_reso_allo'] = $row[csf('prod_reso_allo')];

			$line_arr[] = $row[csf('sewing_line')];
		}
		unset($production_result);

		$line_arr = array_unique($line_arr);
		$line_no_str = implode(',', $line_arr);

		$resource_sql = "select a.id as mst_id, a.company_id, a.location_id, a.floor_id, a.line_number, b.id, b.from_date, b.to_date, b.man_power, b.operator, b.helper, b.line_chief, b.active_machine, b.target_per_hour, b.working_hour, b.po_id, smv_adjust, smv_adjust_type, b.capacity, b.target_efficiency
		from prod_resource_mst a, prod_resource_dtls_mast b
		where a.id in($line_no_str) and a.id=b.mst_id and b.is_deleted=0 $sql_resource_cond $resource_date_cond";

		/*$resource_sql = "select a.company_id, a.location_id, a.floor_id, a.line_number, c.id, c.mst_id, c.from_date, c.to_date, c.man_power, c.operator, c.helper, c.line_chief, c.active_machine, c.target_per_hour, c.working_hour, c.po_id, b.smv_adjust, b.smv_adjust_type, c.capacity, c.target_efficiency
			from prod_resource_mst a, prod_resource_dtls b, prod_resource_dtls_mast c
			where a.id = b.mst_id and b.mast_dtl_id = c.id and a.id in ($line_no_str)";*/

		$resource_result = sql_select($resource_sql);

		foreach ($resource_result as $row) {
			$tmpLine = $row[csf('line_number')];
			$lineArr = explode(',', $tmpLine);

			// make key for each line
			foreach ($lineArr as $line) {
				$daterange = array();
				$begin = $row[csf('from_date')];
				$end = $row[csf('to_date')];

				if($begin != $end) {
					$daterange = get_date_range($begin, $end);
				} else {
					$daterange[] = date('d-M-y', strtotime($begin));
				}

				// make key for each date from Actual Production Resource Entry page
				for($i=0; $i<count($daterange); $i++) {
					$date = strtoupper( date( 'd-M-y', strtotime($daterange[$i]) ) );
					$manPower = $row[csf('man_power')];
					$workingHour = $row[csf('working_hour')];
					$resource_arr[$row[csf('company_id')]][$row[csf('location_id')]][$date][$line]['line'] = $line;
					$resource_arr[$row[csf('company_id')]][$row[csf('location_id')]][$date][$line]['operator'] = $row[csf('operator')];
					$resource_arr[$row[csf('company_id')]][$row[csf('location_id')]][$date][$line]['helper'] = $row[csf('helper')];
					$resource_arr[$row[csf('company_id')]][$row[csf('location_id')]][$date][$line]['man_power'] = $manPower;
					$resource_arr[$row[csf('company_id')]][$row[csf('location_id')]][$date][$line]['working_hour'] = $workingHour;
					/*$resource_arr[$row[csf('company_id')]][$row[csf('location_id')]][$line][strtoupper($date->format("d-M-y"))]['from_date'] = $row[csf('from_date')];
					$resource_arr[$row[csf('company_id')]][$row[csf('location_id')]][$line][strtoupper($date->format("d-M-y"))]['to_date'] = $row[csf('to_date')];*/
				}
			}
		}
		unset($resource_result);

		ob_start();
		?>
		<style>
			#report_1 .rpt_table th, #report_1 .rpt_table td {
				padding: 5px;
				text-align: center;
			}
			#summery1.rpt_table th, #summery1.rpt_table td,
			#summery2.rpt_table th, #summery2.rpt_table td {
				padding: 5px;
				text-align: center;
			}
			#report_1 tr.fltrow td {
				padding: 0;
			}
			tr.summery-footer th {
				border: 1px solid #B0B0B0;
				color: #444;
				font-size: 13px;
				text-align: right;
				font-weight: bold;
				background-image: -moz-linear-gradient( rgb(194,220,255) 10%, rgb(136,170,214) 96%);
				background-image: -webkit-linear-gradient( rgb(194,220,255) 10%, rgb(136,170,214) 96%);
				background: -moz-linear-gradient(top, #F0F0F0 0, #DBDBDB 100%);
				background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #F0F0F0), color-stop(100%, #DBDBDB));
			}
		</style>
		<div id="report_1">
			<h3>Efficiency Report 1</h3>

			<table border="1" rules="all" class="rpt_table" style="width: 99%">
				<thead>
					<tr>
						<th style="width: 5%; word-break: break-word;">Buyer</th>
						<th style="width: 7%; word-break: break-word;">Working Company</th>
						<th style="width: 5%; word-break: break-word;">Location</th>
						<th style="width: 7%; word-break: break-word;">Style</th>
						<th style="width: 6%; word-break: break-word;">Job No</th>
						<th style="width: 5%; word-break: break-word;">Job Qty</th>
						<th style="width: 5%; word-break: break-word;">Order</th>
						<th style="width: 4%; word-break: break-word;">Order Qty</th>
						<th style="width: 7%; word-break: break-word;">Item</th>
						<th style="width: 3%; word-break: break-word;">SMV</th>
						<th style="width: 6%; word-break: break-word;">Production Date</th>
						<th style="width: 5%; word-break: break-word;">Line</th>
						<th style="width: 4%; word-break: break-word;">Prod Qty</th>
						<th style="width: 5%; word-break: break-word;" title="SMV * Production Qty">Produce Minutes</th>
						<th style="width: 4%; word-break: break-word;">Operator</th>
						<th style="width: 3%; word-break: break-word;">Helper</th>
						<th style="width: 4%; word-break: break-word;">Manpower</th>
						<th style="width: 4%; word-break: break-word;">W Hour</th>
						<th style="width: 5%; word-break: break-word;">Available Minutes</th>
						<th style="width: 4%; word-break: break-word;">Efficiency %</th>
					</tr>
				</thead>
			</table>
			<table border="1" rules="all" class="rpt_table" id="report1_body" style="width: 99%">
				<?php
					$sl = 1;
					foreach ($production_arr as $buyer_name => $companyArr) {
						foreach ($companyArr as $company => $locationArr) {
							foreach ($locationArr as $location => $styleArr) {
								foreach ($styleArr as $style => $jobNoArr) {
									foreach ($jobNoArr as $jobNo => $poNoArr) {
										foreach ($poNoArr as $poNo => $itemNumberArr) {
											foreach ($itemNumberArr as $itemNumber => $productionDateArr) {
												foreach ($productionDateArr as $productionDate => $sewingLineArr) {
													foreach ($sewingLineArr as $sewingLine => $value) {

														/*$smv = $smv_arr[$buyer_name][$company][$location][$style][$jobNo][$poNo][$itemNumber][$productionDate][$sewingLine]['set_smv'] = $data[2];*/
														$smv = $smv_arr[$buyer_name][$company][$location][$style][$jobNo][$poNo][$productionDate][$sewingLine][$itemNumber]['set_smv'];
														$productionQty = $value['production_quantity'];
														$produceMinutes = ($smv * $productionQty);
														/*$operator = $resource_arr[$company][$location][$sewingLine][$productionDate]['operator'];
														$helper = $resource_arr[$company][$location][$sewingLine][$productionDate]['helper'];
														$manPower = $resource_arr[$company][$location][$sewingLine][$productionDate]['man_power'];
														$workingHour = $resource_arr[$company][$location][$sewingLine][$productionDate]['working_hour'];*/
														
														$bgcolor = "#FFFFFF";

														$prod_reso_allo = $value['prod_reso_allo'];

														if($prod_reso_allo == 1) {
															$line = $line_library[$prod_reso_library[$sewingLine]];
															$lineId = $prod_reso_library[$sewingLine];
														} else {
															$line = $line_library[$sewingLine];
															$lineId = $sewingLine;
														}

														$operator = $resource_arr[$company][$location][$productionDate][$lineId]['operator'];
														$helper = $resource_arr[$company][$location][$productionDate][$lineId]['helper'];
														$manPower = $resource_arr[$company][$location][$productionDate][$lineId]['man_power'];
														$workingHour = $resource_arr[$company][$location][$productionDate][$lineId]['working_hour'];
														$availableMinutes = ($manPower * $workingHour * 60);
														$effeciency = (($produceMinutes/$availableMinutes) * 100);
														$effeciency = is_infinite($effeciency) || is_nan($effeciency) ? 0 : $effeciency;

														$unit_summery_arr[$location][$buyer_name]['total_produce_minutes'] += $produceMinutes;
														$unit_summery_arr[$location][$buyer_name]['total_available_minutes'] += $availableMinutes;
														$unit_summery_arr[$location][$buyer_name]['total_efficiency'] += $effeciency;

														$buyer_summery_arr[$buyer_name]['total_produce_minutes'] += $produceMinutes;
														$buyer_summery_arr[$buyer_name]['total_available_minutes'] += $availableMinutes;
														$buyer_summery_arr[$buyer_name]['total_efficiency'] += $effeciency;

														if ($sl % 2 == 0) {
															$bgcolor = "#E9F3FF";
														}
									
														?>

														<tr bgcolor="<?php echo $bgcolor; ?>" onClick="change_color('tr_<?php echo $sl; ?>', '<?php echo $bgcolor; ?>')" id="tr_<?php echo $sl; ?>">
															<td style="word-break: break-all; width: 5%;"><?php echo $buyer_library[$buyer_name]; ?></td>
															<td style="word-break: break-all; width: 7%;"><?php echo $company_library[$company]; ?></td>
															<td style="word-break: break-all; width: 5%;"><?php echo $location_library[$location]; ?></td>
															<td style="word-break: break-all; width: 7%;"><?php echo $style; ?></td>
															<td style="word-break: break-all; width: 6%;"><?php echo $jobNo; ?></td>
															<td style="word-break: break-all; width: 5%;"><?php echo $value['job_quantity']; ?></td>
															<td style="word-break: break-all; width: 5%;"><?php echo $poNo; ?></td>
															<td style="word-break: break-all; width: 4%;"><?php echo $value['po_quantity']; ?></td>
															<td style="word-break: break-all; width: 7%;"><?php echo $garments_item[$itemNumber]; ?></td>
															<td style="word-break: break-all; width: 3%;"><?php echo $smv; ?></td>
															<td style="word-break: break-all; width: 6%;"><?php echo $productionDate; ?></td>
															<td style="word-break: break-all; width: 5%;"><?php echo $line; ?></td>
															<td style="word-break: break-all; width: 4%;"><?php echo $productionQty; ?></td>
															<td style="word-break: break-all; width: 5%;"><?php echo number_format($produceMinutes, 1); ?></td>
															<td style="word-break: break-all; width: 4%;"><?php echo $operator; ?></td>
															<td style="word-break: break-all; width: 3%;"><?php echo $helper; ?></td>
															<td style="word-break: break-all; width: 4%;"><?php echo $manPower; ?></td>
															<td style="word-break: break-all; width: 4%;"><?php echo $workingHour; ?></td>
															<td style="word-break: break-all; width: 5%;" title="Manpower * Working Hour * 60"><?php echo number_format($availableMinutes); ?></td>
															<td style="word-break: break-all; width: 4%"><?php echo $effeciency; ?></td>
														</tr>
													<?php
													$sl++;
													$tot_produce_mins += $produceMinutes;
													$tot_available_mins += $availableMinutes;
													$total_manpower += $manPower;
													}
												}
											}
										}
									}
								}
							}
						}
					}
					$totalEffeciency = ($tot_produce_mins/$tot_available_mins)*100;
					$totalEffeciency = is_infinite($totalEffeciency) || is_nan($totalEffeciency) ? 0 : $totalEffeciency;

					$avg_working_hour = $tot_available_mins/($total_manpower*60);
					// $avg_working_hour = is_infinite($avg_working_hour) || is_nan($avg_working_hour) ? 0 : $avg_working_hour;
				?>
			</table>
			<table border="1" rules="all" class="rpt_table" style="width: 99%">
				<tfoot>
					<th colspan="9" style="width: 51%; text-align: right; word-break: break-word;">Total Efficiency :</th>
					<th style="width: 3%; word-break: break-word;"></th>
					<th style="width: 6%; word-break: break-word;"></th>
					<th style="width: 5%; word-break: break-word;"></th>
					<th style="width: 4%; word-break: break-word;" id="total_production_qty"></th>
					<th style="width: 5%; word-break: break-word;" id="total_produce_minutes"></th>
					<th style="width: 4%; word-break: break-word;" id="total_operator"></th>
					<th style="width: 3%; word-break: break-word;" id="total_helper"></th>
					<th style="width: 4%; word-break: break-word;" id="total_manpower"></th>
					<th style="width: 4%; word-break: break-word;" title="Total Available Minutes / (Total Manpower * 60)"><?php echo $avg_working_hour; ?></th>
					<th style="width: 5%; word-break: break-word;" id="total_available_minutes"></th>
					<th style="width: 4%; word-break: break-word;" title="(Produce Minutes / Available Minutes) * 100"><?php echo $totalEffeciency; ?></th>
				</tfoot>
			</table>
		</div>

		<?php
			$mainReportData=ob_get_contents();
			ob_clean();

			ob_start();
		?>

		<div class="left">
			<h2 style="text-align: center;">Summery(Unit Wise)</h2>
			<table width="90%" border="1" rules="all" class="rpt_table" id="summery1">
				<thead>
					<th>Location</th>
					<th>Buyer</th>
					<th title="SMV * Production Qty">Produce Minutes</th>
					<th>Available Minutes</th>
					<th>Efficiency %</th>
				</thead>
				<tbody>
					<?php
						$tot_produce_mins = 0;
						$tot_available_mins = 0;
						$tot_efficiency = 0;
						foreach ($unit_summery_arr as $location => $locationArr) {
							foreach ($locationArr as $buyerId => $value) {
									$produceMinutes = $value['total_produce_minutes'];
									$availableMinutes = $value['total_available_minutes'];
									$effeciency = ($produceMinutes / $availableMinutes) * 100;
									$effeciency = is_infinite($effeciency) || is_nan($effeciency) ? 0 : $effeciency;
								?>
									<tr>
										<td><?php echo $location_library[$location]; ?></td>
										<td><?php echo $buyer_library[$buyerId]; ?></td>
										<td><?php echo number_format($produceMinutes, 2); ?></td>
										<td><?php echo number_format($availableMinutes, 2); ?></td>
										<td><?php echo number_format($effeciency, 4); ?></td>
									</tr>
								<?php
								$tot_produce_mins += $produceMinutes;
								$tot_available_mins += $availableMinutes;
								// $tot_efficiency += $effeciency;
							}
						}
						$tot_efficiency = ($tot_produce_mins/$tot_available_mins)*100;
						$tot_efficiency = is_infinite($tot_efficiency) || is_nan($tot_efficiency) ? 0 : $tot_efficiency;
					?>
				</tbody>
				<tfoot>
					<th colspan="2">Total</th>
					<th><?php echo number_format($tot_produce_mins, 2); ?></th>
					<th><?php echo number_format($tot_available_mins, 2); ?></th>
					<th><?php echo number_format($tot_efficiency, 4); ?></th>
				</tfoot>
			</table>
		</div>

		<div class="right">
			<h2 style="text-align: center;">Summery(Buyer Wise)</h2>
			<table width="90%" border="1" rules="all" class="rpt_table" id="summery2">
				<thead>
					<th>Buyer</th>
					<th title="SMV * Production Qty">Produce Minutes</th>
					<th>Available Minutes</th>
					<th>Efficiency %</th>
				</thead>
				<tbody>
					<?php
						$tot_produce_mins = 0;
						$tot_available_mins = 0;
						$tot_efficiency = 0;
						foreach ($buyer_summery_arr as $buyerId => $value) {
							$produceMinutes = $value['total_produce_minutes'];
							$availableMinutes = $value['total_available_minutes'];
							$effeciency = ($produceMinutes / $availableMinutes) * 100;
							$effeciency = is_infinite($effeciency) || is_nan($effeciency) ? 0 : $effeciency;
							?>
								<tr>
									<td><?php echo $buyer_library[$buyerId]; ?></td>
									<td><?php echo number_format($produceMinutes, 2); ?></td>
									<td><?php echo number_format($availableMinutes, 2); ?></td>
									<td><?php echo $effeciency; ?></td>
								</tr>
							<?php
								$tot_produce_mins += $produceMinutes;
								$tot_available_mins += $availableMinutes;
								// $tot_efficiency += $effeciency;
						}
						$tot_efficiency = ($tot_produce_mins/$tot_available_mins)*100;
						$tot_efficiency = is_infinite($tot_efficiency) || is_nan($tot_efficiency) ? 0 : $tot_efficiency;
					?>
				</tbody>
				<tfoot>
					<th>Total</th>
					<th><?php echo number_format($tot_produce_mins, 2); ?></th>
					<th><?php echo number_format($tot_available_mins, 2); ?></th>
					<th><?php echo number_format($tot_efficiency, 4); ?></th>
				</tfoot>
			</table>
		</div>
		<?php 	 
	}

	if ($type == 2) 
	{
		function get_date_range($first, $last, $step = '+1 day', $output_format = 'd-M-Y') {
			$dates = array();
			$current = strtotime($first);
			$last = strtotime($last);

			while( $current <= $last ) {
				$dates[] = date($output_format, $current);
				$current = strtotime($step, $current);
			}

			return $dates;
		} 
		$cbo_company_name = str_replace("'", '', $cbo_company_name);
		$cbo_working_company_name = str_replace("'", '', $cbo_working_company_name);
		$cbo_location_name = str_replace("'", '', $cbo_location_name);
		$cbo_buyer_name = str_replace("'", '', $cbo_buyer_name);
		$txt_style_no = str_replace("'", '', $txt_style_no);
		$txt_job_no = str_replace("'", '', $txt_job_no);
		$txt_order_no = str_replace("'", '', $txt_order_no);
		$cbo_sewing_line = str_replace("'", '', $cbo_sewing_line);
		$txt_date_from = str_replace("'", '', $txt_date_from);
		$txt_date_to = str_replace("'", '', $txt_date_to);
		$cbo_year_selection = str_replace("'", '', $cbo_year_selection);

		$buyer_library = return_library_array( "select id, buyer_name from lib_buyer", 'id', 'buyer_name');
		$company_library = return_library_array( "select id, company_name from lib_company", 'id', 'company_name');
		$location_library = return_library_array("select id, location_name from lib_location where status_active = 1", 'id', 'location_name');
		$line_library = return_library_array("select id,line_name from lib_sewing_line where status_active=1 and is_deleted=0", 'id', 'line_name');

		$sql_cond = '';
		$sql_resource_cond = '';

		$wo_result = array();
		$production_arr = array();
		$resource_arr = array();
		$unit_summery_arr = array();
		$buyer_summery_arr = array();

		if ($txt_date_from == '' && $txt_date_to == '') {
			$txt_date_from = "01-Jan-$cbo_year_selection";
			$txt_date_to = "31-Dec-$cbo_year_selection";
		}

		if($db_type==0)
		{
			$date_cond="and a.production_date between '".change_date_format(trim($txt_date_from),"yyyy-mm-dd")."' and '".change_date_format(trim($txt_date_to),"yyyy-mm-dd")."'";
			$resource_date_cond="and b.from_date between '".change_date_format(trim($txt_date_from),"yyyy-mm-dd")."' and '".change_date_format(trim($txt_date_to),"yyyy-mm-dd")."'";
		}
		else
		{
			$date_cond="and a.production_date between '".change_date_format(trim($txt_date_from),'','',1)."' and '".change_date_format(trim($txt_date_to),'','',1)."'";
			$resource_date_cond="and b.to_date between '".change_date_format(trim($txt_date_from),'','',1)."' and '".change_date_format(trim($txt_date_to),'','',1)."'";
		}

		if($cbo_buyer_name != 0) {
			$sql_cond .= " and c.buyer_name in($cbo_buyer_name)";
		}

		if($cbo_company_name != 0) {
			$sql_cond .= " and a.company_id in($cbo_company_name)";
		}

		if($cbo_working_company_name != '') {
			$sql_cond .= " and a.serving_company in($cbo_working_company_name)";
			$sql_resource_cond .= " and a.company_id in($cbo_working_company_name)";
		}

		if($cbo_location_name != '') {
			$sql_cond .= " and a.location in($cbo_location_name)";
		}

		if($txt_style_no != '') {
			$sql_cond .= " and c.style_ref_no = '$txt_style_no'";
		}

		if($txt_job_no != '') {
			$sql_cond .= " and c.job_no = '$txt_job_no'";
		}

		if($txt_order_no != '') {
			$sql_cond .= " and d.po_number = '$txt_order_no'";
		}

		if($cbo_sewing_line != 0) {
			$sql_cond .= " and a.sewing_line = $cbo_sewing_line";
		}

		$production_sql = "select distinct a.id as prod_mst_id, a.company_id, a.serving_company, a.location, a.po_break_down_id, a.item_number_id, a.production_date, a.sewing_line, a.production_quantity, b.production_type, c.buyer_name, c.company_name, c.location_name, c.style_ref_no, c.job_no, c.job_quantity, d.po_number, d.po_quantity, c.set_smv, c.set_break_down
			from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_details_master c, wo_po_break_down d
			where a.production_type in (5) and a.id = b.mst_id $date_cond and c.job_no=d.job_no_mst and d.id=a.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and d.status_active=1 and d.is_deleted=0 $sql_cond";
		
		// echo $production_sql;

		$production_result = sql_select($production_sql);
		$line_arr = array();
		$smv_arr = array();

		foreach ($production_result as $row) {
			$location = $row[csf('location')];

			$set_arr = explode('__', $row[csf('set_break_down')]);

			if($set_arr[0]=='') {
				$set_arr=array();
			}
			if ( count($set_arr)>0) {
				foreach( $set_arr as $set) {
					$data=explode('_',$set);
					$smv_arr[$row[csf('buyer_name')]][$row[csf('serving_company')]][$location][$row[csf('style_ref_no')]][$row[csf('job_no')]][$row[csf('po_number')]][$data[0]]['smv'] = $data[2];
				}
			}

			$production_arr[$row[csf('buyer_name')]][$row[csf('serving_company')]][$location][$row[csf('style_ref_no')]][$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('item_number_id')]]['production_quantity'] += $row[csf('production_quantity')];		
			$production_arr[$row[csf('buyer_name')]][$row[csf('serving_company')]][$location][$row[csf('style_ref_no')]][$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('item_number_id')]]['job_quantity'] = $row[csf('job_quantity')];
			$production_arr[$row[csf('buyer_name')]][$row[csf('serving_company')]][$location][$row[csf('style_ref_no')]][$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('item_number_id')]]['po_quantity'] = $row[csf('po_quantity')];
			
			/*$production_arr[$row[csf('buyer_name')]][$row[csf('serving_company')]][$location][$row[csf('style_ref_no')]][$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('item_number_id')]]['sewing_line'] = $row[csf('sewing_line')];*/
			$production_arr[$row[csf('buyer_name')]][$row[csf('serving_company')]][$location][$row[csf('style_ref_no')]][$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('item_number_id')]]['sewing_line'][$row[csf('sewing_line')]][] = $row[csf('production_date')];
			$production_arr[$row[csf('buyer_name')]][$row[csf('serving_company')]][$location][$row[csf('style_ref_no')]][$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('item_number_id')]]['prod_mst_id'] = $row[csf('prod_mst_id')];
			$production_arr[$row[csf('buyer_name')]][$row[csf('serving_company')]][$location][$row[csf('style_ref_no')]][$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('item_number_id')]]['prod_mst_ids_str'] .= $row[csf('prod_mst_id')] . ',';

			$line_arr[] = $row[csf('sewing_line')];
		}
		unset($production_result);

		$line_arr = array_unique($line_arr);
		$line_no_str = implode(',', $line_arr);

		$resource_sql = "select a.id as mst_id, a.company_id, a.location_id, a.floor_id, a.line_number, b.id, b.from_date, b.to_date, b.man_power, b.operator, b.helper, b.line_chief, b.active_machine, b.target_per_hour, b.working_hour, b.po_id, smv_adjust, smv_adjust_type, b.capacity, b.target_efficiency
		from prod_resource_mst a,prod_resource_dtls_mast b
		where a.id in($line_no_str) and a.id=b.mst_id and b.is_deleted=0 $sql_resource_cond $resource_date_cond";

		// echo $resource_sql;
		$resource_result = sql_select($resource_sql);

		foreach ($resource_result as $row) {
			$daterange = array();
			$begin = $row[csf('from_date')];
			$end = $row[csf('to_date')];

			if($begin != $end) {
				$daterange = get_date_range($begin, $end);
			} else {
				$daterange[] = date('d-M-y', strtotime($begin));
			}

			// make key for each date from Actual Production Resource Entry page
			for($i=0; $i<count($daterange); $i++) {
				$date = strtoupper( date( 'd-M-y', strtotime($daterange[$i]) ) );
				$manPower = $row[csf('man_power')];
				$workingHour = $row[csf('working_hour')];
				$availableMinutes = $manPower * $workingHour * 60;

				$resource_arr[$row[csf('company_id')]][$row[csf('location_id')]][$row[csf('mst_id')]][$date]['line'] = $row[csf('mst_id')];
				$resource_arr[$row[csf('company_id')]][$row[csf('location_id')]][$row[csf('mst_id')]][$date]['operator'] = $row[csf('operator')];
				$resource_arr[$row[csf('company_id')]][$row[csf('location_id')]][$row[csf('mst_id')]][$date]['helper'] = $row[csf('helper')];
				$resource_arr[$row[csf('company_id')]][$row[csf('location_id')]][$row[csf('mst_id')]][$date]['man_power'] = $manPower;
				$resource_arr[$row[csf('company_id')]][$row[csf('location_id')]][$row[csf('mst_id')]][$date]['working_hour'] = $workingHour;
				$resource_arr[$row[csf('company_id')]][$row[csf('location_id')]][$row[csf('mst_id')]][$date]['availableMinutes'] = $availableMinutes;
				$resource_arr[$row[csf('company_id')]][$row[csf('location_id')]][$row[csf('mst_id')]][$date]['prod_resource_mst_id'] = $row[csf('mst_id')];
			}
		}

		unset($resource_result);
		ob_start();
		?>

		<style>
			#report_1 .rpt_table th, #report_1 .rpt_table td {
				padding: 5px;
				text-align: center;
			}
			#summery1.rpt_table th, #summery1.rpt_table td,
			#summery2.rpt_table th, #summery2.rpt_table td {
				padding: 5px;
				text-align: center;
			}
			#report_1 tr.fltrow td {
				padding: 0;
			}
			tr.summery-footer th {
				border: 1px solid #B0B0B0;
				color: #444;
				font-size: 13px;
				text-align: right;
				font-weight: bold;
				background-image: -moz-linear-gradient( rgb(194,220,255) 10%, rgb(136,170,214) 96%);
				background-image: -webkit-linear-gradient( rgb(194,220,255) 10%, rgb(136,170,214) 96%);
				background: -moz-linear-gradient(top, #F0F0F0 0, #DBDBDB 100%);
				background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #F0F0F0), color-stop(100%, #DBDBDB));
			}
		</style>
		<div id="report_1">
			<h3>Efficiency Report 2</h3>

			<table border="1" rules="all" class="rpt_table" style="width: 99%">
				<thead>
					<tr>
						<th style="width: 5%; word-break: break-word;">Buyer</th>
						<th style="width: 7%; word-break: break-word;">Working Company</th>
						<th style="width: 5%; word-break: break-word;">Location</th>
						<th style="width: 7%; word-break: break-word;">Style</th>
						<th style="width: 6%; word-break: break-word;">Job No</th>
						<th style="width: 5%; word-break: break-word;">Job Qty</th>
						<th style="width: 5%; word-break: break-word;">Order</th>
						<th style="width: 4%; word-break: break-word;">Order Qty</th>
						<th style="width: 7%; word-break: break-word;">Item</th>
						<th style="width: 3%; word-break: break-word;">SMV</th>
						<th style="width: 4%; word-break: break-word;">Prod Qty</th>
						<th style="width: 5%; word-break: break-word;" title="SMV * Production Qty">Produce Minutes</th>
						<th style="width: 4%; word-break: break-word;">Operator</th>
						<th style="width: 3%; word-break: break-word;">Helper</th>
						<th style="width: 4%; word-break: break-word;">Manpower</th>
						<th style="width: 4%; word-break: break-word;">W Hour</th>
						<th style="width: 5%; word-break: break-word;">Available Minutes</th>
						<th style="width: 4%; word-break: break-word;">Efficiency %</th>
					</tr>
				</thead>
			</table>
			<table border="1" rules="all" class="rpt_table" id="report1_body" style="width: 99%">
				<?php
					$sl = 1;
					foreach ($production_arr as $buyerName => $companyArr) {
						$total_efficiency = 0;
						$allStyleArr = array();
						$allOrderArr = array();

						$buyerWorkingHour = 0;
						$buyerEff = 0;
						$buyerProdMin = 0;
						$buyerOperator = 0;
						$buyerHelper = 0;
						$buyerManpower = 0;
						$buyerAvlMin = 0;
						foreach ($companyArr as $company => $locationArr) {
							foreach ($locationArr as $location => $styleArr) {
								foreach ($styleArr as $style => $jobNoArr) {

									$styleEff = 0;
									$styleProdMin = 0;
									$styleOperator = 0;
									$styleHelper = 0;
									$styleManpower = 0;
									$styleAvlMin = 0;
								
									foreach ($jobNoArr as $jobNo => $poNoArr) {

										$orderEff = 0;
										$orderProdMin = 0;
										$orderOperator = 0;
										$orderHelper = 0;
										$orderManpower = 0;
										$orderAvlMin = 0;

										foreach ($poNoArr as $poNo => $itemNumberArr) {
											$orderProdMin = 0;
											foreach ($itemNumberArr as $itemNumber => $value) {
												$sewingLineArr = $value['sewing_line'];
												
												$smv = $smv_arr[$buyerName][$company][$location][$style][$jobNo][$poNo][$itemNumber]['smv'];

												$productionQty = $value['production_quantity'];
												$produceMinutes = ($smv * $productionQty);
												$operator = 0;
												$helper = 0;
												$manPower = 0;
												$workingHour = 0;
												$availableMinutes = 0;

												$prodMstIds = rtrim($value['prod_mst_ids_str'], ',');
												/*$productionDate = $value['production_date'];
												$productionDateArr = array_unique($value['production_date']);*/

												foreach ($sewingLineArr as $sewingLine => $prodDate) {
													$productionDateArr = array_unique($prodDate);
													foreach ($productionDateArr as $prodDate) {
														$operator += $resource_arr[$company][$location][$sewingLine][$prodDate]['operator'];
														$helper += $resource_arr[$company][$location][$sewingLine][$prodDate]['helper'];
														$manPower += $resource_arr[$company][$location][$sewingLine][$prodDate]['man_power'];
														$workingHour += $resource_arr[$company][$location][$sewingLine][$prodDate]['working_hour'];
														$availableMinutes += $resource_arr[$company][$location][$sewingLine][$prodDate]['availableMinutes'];
													}
												}

												$workingHour = $availableMinutes/($manPower*60);

												$buyer_summery_arr[$buyerName]['total_produce_minutes'] += $produceMinutes;
												$buyer_summery_arr[$buyerName]['total_available_minutes'] += $availableMinutes;
												// $buyer_summery_arr[$buyerName]['total_efficiency'] += $effeciency;

												$unit_summery_arr[$location][$buyerName]['total_produce_minutes'] += $produceMinutes;
												$unit_summery_arr[$location][$buyerName]['total_available_minutes'] += $availableMinutes;
												// $unit_summery_arr[$location][$buyerName]['total_efficiency'] += $effeciency;
												
												//$resourceId = $resource_arr[$company][$location][$sewingLine][$value['production_date']]['prod_resource_mst_id'];
												$resourceId = $resourceId ? $resourceId : 0;
												// $availableMinutes = ($manPower * $workingHour * 60);
												$effeciency = (($produceMinutes/$availableMinutes) * 100);
												$effeciency = is_infinite($effeciency) || is_nan($effeciency) ? 0 : $effeciency;
												$bgcolor = "#FFFFFF";

												if ($sl % 2 == 0) {
													$bgcolor = "#E9F3FF";
												}
							
												?>

												<tr bgcolor="<?php echo $bgcolor; ?>" onClick="change_color('tr_<?php echo $sl; ?>', '<?php echo $bgcolor; ?>')" id="tr_<?php echo $sl; ?>">
													<td style="word-break: break-all; width: 5%;"><?php echo $buyer_library[$buyerName]; ?></td>
													<td style="word-break: break-all; width: 7%;"><?php echo $company_library[$company]; ?></td>
													<td style="word-break: break-all; width: 5%;"><?php echo $location_library[$location]; ?></td>
													<td style="word-break: break-all; width: 7%;"><?php echo $style; ?></td>
													<td style="word-break: break-all; width: 6%;"><?php echo $jobNo; ?></td>
													<td style="word-break: break-all; width: 5%;"><?php echo $value['job_quantity']; ?></td>
													<td style="word-break: break-all; width: 5%;"><?php echo $poNo; ?></td>
													<td style="word-break: break-all; width: 4%;"><?php echo $value['po_quantity']; ?></td>
													<td style="word-break: break-all; width: 7%;"><?php echo $garments_item[$itemNumber]; ?></td>
													<td style="word-break: break-all; width: 3%;"><?php echo $smv; ?></td>
													<td style="word-break: break-all; width: 4%;"><?php echo $productionQty; ?></td>
													<td style="word-break: break-all; width: 5%;"><?php echo number_format($produceMinutes, 2); ?></td>
													<td style="word-break: break-all; width: 4%;"><?php echo $operator; ?></td>
													<td style="word-break: break-all; width: 3%;"><?php echo $helper; ?></td>
													<td style="word-break: break-all; width: 4%;"><?php echo $manPower; ?></td>
													<td style="word-break: break-all; width: 4%;"><?php echo number_format($workingHour); ?></td>
													<td style="word-break: break-all; width: 5%;"><?php echo number_format($availableMinutes); ?></td>
													<td style="word-break: break-all; width: 4%">
														<a href="##" onClick="fnc_efc_details('<?php echo $poNo; ?>', '<?php echo $prodMstIds; ?>')">
															<p><?php echo number_format($effeciency, 4); ?></p>
														</a>
													</td>
												</tr>
												<?php
												$allOrderArr[] = $poNo;
												$allStyleArr[] = $style;
												$allBuyerArr[] = $buyerName;
												$total_efficiency += $effeciency;
												$sl++;
												$order_count = count(array_unique($allOrderArr));
												$style_count = count(array_unique($allStyleArr));
												$buyer_count = count(array_unique($allBuyerArr));
												// $orderEff = ($total_efficiency / $order_count);
												$orderProdMin += $produceMinutes;
												$orderOperator += $operator;
												$orderHelper += $helper;
												$orderManpower += $manPower;
												$orderAvlMin += $availableMinutes;
												$orderWorkingHour = $orderAvlMin / ($orderManpower*60);

												// $styleEff = ($total_efficiency / $style_count);
												$styleProdMin += $produceMinutes;
												$styleOperator += $operator;
												$styleHelper += $helper;
												$styleManpower += $manPower;
												$styleAvlMin += $availableMinutes;

												// $buyerEff = ($total_efficiency / $buyer_count);
												$buyerProdMin += $produceMinutes;
												$buyerOperator += $operator;
												$buyerHelper += $helper;
												$buyerManpower += $manPower;
												$buyerAvlMin += $availableMinutes;
											}

											$orderEff = ($orderProdMin / $orderAvlMin) * 100;
											?>
											<tr class="summery-footer">
												<th colspan="11" style="width: 51%; word-break: break-word; text-align: right;">Order Wise Efficiency :</th>
												<th style="width: 4%; word-break: break-word;"><?php echo number_format($orderProdMin, 2); ?></th>
												<th style="width: 4%; word-break: break-word;"><?php echo $orderOperator; ?></th>
												<th style="width: 4%; word-break: break-word;"><?php echo $orderHelper; ?></th>
												<th style="width: 4%; word-break: break-word;"><?php echo $orderManpower; ?></th>
												<th style="width: 4%; word-break: break-word;" title="Total Available Minutes / (Total Manpower * 60)"><?php echo $orderWorkingHour; ?></th>
												<th style="width: 4%; word-break: break-word;"><?php echo number_format($orderAvlMin, 2); ?></th>
												<th style="width: 4%; word-break: break-word;" title="(Total Produce Minutes / Total Available Minutes) * 100"><?php echo number_format($orderEff, 4); ?></th>
											</tr>
											<?php
										}
										$styleWorkingHour = $styleAvlMin / ($styleManpower*60);
										$styleEff = ($styleProdMin / $styleAvlMin) * 100;
									}
									?>
									<tr class="summery-footer">
										<th colspan="11" style="width: 51%; word-break: break-word; text-align: right;">Style Wise Efficiency :</th>
										<th style="width: 4%; word-break: break-word;"><?php echo number_format($styleProdMin, 2); ?></th>
										<th style="width: 4%; word-break: break-word;"><?php echo $styleOperator; ?></th>
										<th style="width: 4%; word-break: break-word;"><?php echo $styleHelper; ?></th>
										<th style="width: 4%; word-break: break-word;"><?php echo $styleManpower; ?></th>
										<th style="width: 4%; word-break: break-word;" title="Total Available Minutes / (Total Manpower * 60)"><?php echo $styleWorkingHour; ?></th>
										<th style="width: 4%; word-break: break-word;"><?php echo number_format($styleAvlMin, 2); ?></th>
										<th style="width: 4%; word-break: break-word;" title="(Total Produce Minutes / Total Available Minutes) * 100"><?php echo number_format($styleEff, 4); ?></th>
									</tr>
									<?php
								}
							}
							$buyerWorkingHour = $buyerAvlMin / ($buyerManpower*60);
							$buyerEff = ($buyerProdMin / $buyerAvlMin) * 100;
						}
						?>
							<tr class="summery-footer">
								<th colspan="11" style="width: 51%; word-break: break-word; text-align: right;">Buyer Wise Efficiency :</th>
								<th style="width: 4%; word-break: break-word;"><?php echo number_format($buyerProdMin, 2); ?></th>
								<th style="width: 4%; word-break: break-word;"><?php echo $buyerOperator; ?></th>
								<th style="width: 4%; word-break: break-word;"><?php echo $buyerHelper; ?></th>
								<th style="width: 4%; word-break: break-word;"><?php echo $buyerManpower; ?></th>
								<th style="width: 4%; word-break: break-word;" title="Total Available Minutes / (Total Manpower * 60)"><?php echo $buyerWorkingHour; ?></th>
								<th style="width: 4%; word-break: break-word;"><?php echo number_format($buyerAvlMin, 2); ?></th>
								<th style="width: 4%; word-break: break-word;" title="(Total Produce Minutes / Total Available Minutes) * 100"><?php echo number_format($buyerEff, 4); ?></th>
							</tr>
						<?php
					}
				?>
			</table>
		</div>

		<?php
			$mainReportData=ob_get_contents();
			ob_clean();

			ob_start();
		?>

		<div class="left">
			<h2 style="text-align: center;">Summery(Unit Wise)</h2>
			<table width="90%" border="1" rules="all" class="rpt_table" id="summery1">
				<thead>
					<th>Location</th>
					<th>Buyer</th>
					<th title="SMV * Production Qty">Produce Minutes</th>
					<th>Available Minutes</th>
					<th>Efficiency %</th>
				</thead>
				<tbody>
					<?php
						$tot_produce_mins = 0;
						$tot_available_mins = 0;
						$tot_efficiency = 0;
						foreach ($unit_summery_arr as $location => $locationArr) {
							foreach ($locationArr as $buyerId => $value) {
								$produceMinutes = $value['total_produce_minutes'];
								$availableMinutes = $value['total_available_minutes'];
								$effeciency = ($produceMinutes / $availableMinutes) * 100;
								$effeciency = is_infinite($effeciency) || is_nan($effeciency) ? 0 : $effeciency;
								?>
									<tr>
										<td><?php echo $location_library[$location]; ?></td>
										<td><?php echo $buyer_library[$buyerId]; ?></td>
										<td><?php echo number_format($produceMinutes, 2); ?></td>
										<td><?php echo number_format($availableMinutes, 2); ?></td>
										<td><?php echo $effeciency; ?></td>
									</tr>
								<?php
								$tot_produce_mins += $produceMinutes;
								$tot_available_mins += $availableMinutes;
								$tot_efficiency += $effeciency;
							}
						}
					?>
				</tbody>
				<tfoot>
					<th colspan="2">Total</th>
					<th><?php echo number_format($tot_produce_mins, 2); ?></th>
					<th><?php echo number_format($tot_available_mins, 2); ?></th>
					<th><?php echo number_format(($tot_produce_mins/$tot_available_mins)*100, 4); ?></th>
				</tfoot>
			</table>
		</div>

		<div class="right">
			<h2 style="text-align: center;">Summery(Buyer Wise)</h2>
			<table width="90%" border="1" rules="all" class="rpt_table" id="summery2">
				<thead>
					<th>Buyer</th>
					<th title="SMV * Production Qty">Produce Minutes</th>
					<th>Available Minutes</th>
					<th>Efficiency %</th>
				</thead>
				<tbody>
					<?php
						/*echo '<pre>';
						print_r($buyer_summery_arr);
						echo '</pre>';*/
						$tot_produce_mins = 0;
						$tot_available_mins = 0;
						$tot_efficiency = 0;
						foreach ($buyer_summery_arr as $buyerId => $value) {
							$produceMinutes = $value['total_produce_minutes'];
							$availableMinutes = $value['total_available_minutes'];
							$effeciency = ($produceMinutes / $availableMinutes) * 100;
							$effeciency = is_infinite($effeciency) || is_nan($effeciency) ? 0 : $effeciency;
							?>
								<tr>
									<td><?php echo $buyer_library[$buyerId]; ?></td>
									<td><?php echo number_format($produceMinutes, 2); ?></td>
									<td><?php echo number_format($availableMinutes, 2); ?></td>
									<td><?php echo $effeciency; ?></td>
								</tr>
							<?php
								$tot_produce_mins += $produceMinutes;
								$tot_available_mins += $availableMinutes;
								$tot_efficiency += $effeciency;
						}
					?>
				</tbody>
				<tfoot>
					<th>Total</th>
					<th><?php echo number_format($tot_produce_mins, 2); ?></th>
					<th><?php echo number_format($tot_available_mins, 2); ?></th>
					<th><?php echo number_format(($tot_produce_mins/$tot_available_mins)*100, 4); ?></th>
				</tfoot>
			</table>
		</div>
		<?php 	
	}

	if ($type == 3)
	{
		
		$cbo_company_name = str_replace("'", '', $cbo_company_name);
		$cbo_working_company_name = str_replace("'", '', $cbo_working_company_name);
		$cbo_location_name = str_replace("'", '', $cbo_location_name);
		$cbo_buyer_name = str_replace("'", '', $cbo_buyer_name);
		$txt_style_no = str_replace("'", '', $txt_style_no);
		$txt_job_no = str_replace("'", '', $txt_job_no);
		$txt_order_no = str_replace("'", '', $txt_order_no);
		$cbo_sewing_line = str_replace("'", '', $cbo_sewing_line);
		$txt_date_from = str_replace("'", '', $txt_date_from);
		$txt_date_to = str_replace("'", '', $txt_date_to);
		$cbo_year_selection = str_replace("'", '', $cbo_year_selection);

		$buyer_library = return_library_array( "select id, buyer_name from lib_buyer", 'id', 'buyer_name');
		$company_library = return_library_array( "select id, company_name from lib_company", 'id', 'company_name');
		$location_library = return_library_array("select id, location_name from lib_location where status_active = 1", 'id', 'location_name');
		$line_library = return_library_array("select id,line_name from lib_sewing_line where status_active=1 and is_deleted=0", 'id', 'line_name');
		$prod_reso_library = return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');

		$sql_cond = '';
		$sql_resource_cond = '';

		$wo_result = array();
		$production_arr = array();
		$resource_arr = array();
		$unit_summery_arr = array();
		$buyer_summery_arr = array();
		$smv_arr = array();

		if ($txt_date_from == '' && $txt_date_to == '') {
			$txt_date_from = "01-Jan-$cbo_year_selection";
			$txt_date_to = "31-Dec-$cbo_year_selection";
		}

		$date_cond="and a.production_date between '".change_date_format(trim($txt_date_from),'','',1)."' and '".change_date_format(trim($txt_date_to),'','',1)."'"; 
		$resource_date_cond="and b.pr_date between '".change_date_format(trim($txt_date_from),'','',1)."' and '".change_date_format(trim($txt_date_to),'','',1)."'";
		

		if($cbo_buyer_name != 0) {
			$sql_cond .= " and c.buyer_name in($cbo_buyer_name)";
		}

		if($cbo_company_name != 0) {
			$sql_cond .= " and a.company_id in($cbo_company_name)";
		}

		if($cbo_working_company_name != '') {
			$sql_cond .= " and a.serving_company in($cbo_working_company_name)";
			$sql_resource_cond .= " and a.company_id in($cbo_working_company_name)";
		}

		if($txt_style_no != '') {
			$sql_cond .= " and c.style_ref_no = '$txt_style_no'";
		}

		if($txt_job_no != '') {
			$sql_cond .= " and c.job_no = '$txt_job_no'";
		}

		if($txt_order_no != '') {
			$sql_cond .= " and d.po_number = '$txt_order_no'";
		}

		if($cbo_sewing_line != 0) {
			$sql_cond .= " and a.sewing_line = $cbo_sewing_line";
		}
		if($cbo_location_name) 
		{
			$sql_cond .= " and a.location in ($cbo_location_name)";
		}
			
		
		//=================================== PRODUCTION SQL ====================================
		$production_sql = "select a.company_id, TO_CHAR(a.production_hour, 'YYYY-MM-DD') as acc_prod_date, TO_CHAR(a.production_hour, 'HH24') as acc_prod_hour, a.serving_company, a.location, a.po_break_down_id, a.item_number_id,TO_CHAR(a.production_date, 'YYYY-MM-DD') as production_date, a.sewing_line, b.production_qnty as prod_qty, b.production_type, c.buyer_name, c.company_name, c.location_name, c.style_ref_no as style, c.job_no, c.job_quantity, d.po_number, d.po_quantity, c.set_smv, a.prod_reso_allo, c.set_break_down,d.job_id from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_details_master c, wo_po_break_down d where a.id = b.mst_id and c.id=d.job_id and d.id=a.po_break_down_id and a.production_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and d.status_active=1 and d.is_deleted=0 $date_cond $sql_cond order by a.production_date";
		// echo $production_sql; die;

		$production_result = sql_select($production_sql); 
		if (count($production_result)==0) 
		{
			echo "<h1 style='color:red; font-size: 17px;'> Sewing Out Data Not Found </h1>" ;
			die();
		}
		$job_id_arr 	= array();
		$$production_arr= array();
		foreach ($production_result as $v) {
			$set_arr = explode('__', $v['SET_BREAK_DOWN']);

			if($set_arr[0]=='') {
				$set_arr=array();
			}
			if ( count($set_arr)>0) {
				foreach( $set_arr as $set) {
					$data=explode('_',$set);
					$smv_arr[$v['BUYER_NAME']][$v['SERVING_COMPANY']][$v['LOCATION']][$v['PO_NUMBER']][$v['PRODUCTION_DATE']][$v['SEWING_LINE']][$data[0]]['SET_SMV'] = $data[2];
					
				}
			}
			$job_id_arr[$v['JOB_ID']]=$v['JOB_ID'];
			$production_arr[$v['BUYER_NAME']][$v['SERVING_COMPANY']][$v['LOCATION']][$v['PO_NUMBER']][$v['ITEM_NUMBER_ID']][$v['PRODUCTION_DATE']][$v['SEWING_LINE']]['JOB_NO'] 				= $v['JOB_NO'];
			$production_arr[$v['BUYER_NAME']][$v['SERVING_COMPANY']][$v['LOCATION']][$v['PO_NUMBER']][$v['ITEM_NUMBER_ID']][$v['PRODUCTION_DATE']][$v['SEWING_LINE']]['JOB_ID'] 				= $v['JOB_ID'];
			$production_arr[$v['BUYER_NAME']][$v['SERVING_COMPANY']][$v['LOCATION']][$v['PO_NUMBER']][$v['ITEM_NUMBER_ID']][$v['PRODUCTION_DATE']][$v['SEWING_LINE']]['STYLE'] 					= $v['STYLE'];
			$production_arr[$v['BUYER_NAME']][$v['SERVING_COMPANY']][$v['LOCATION']][$v['PO_NUMBER']][$v['ITEM_NUMBER_ID']][$v['PRODUCTION_DATE']][$v['SEWING_LINE']]['PRODUCTION_QUANTITY'] 	+= $v['PROD_QTY'];
			$production_arr[$v['BUYER_NAME']][$v['SERVING_COMPANY']][$v['LOCATION']][$v['PO_NUMBER']][$v['ITEM_NUMBER_ID']][$v['PRODUCTION_DATE']][$v['SEWING_LINE']]['JOB_QUANTITY'] 			= $v['JOB_QUANTITY'];
			$production_arr[$v['BUYER_NAME']][$v['SERVING_COMPANY']][$v['LOCATION']][$v['PO_NUMBER']][$v['ITEM_NUMBER_ID']][$v['PRODUCTION_DATE']][$v['SEWING_LINE']]['PO_QUANTITY'] 			= $v['PO_QUANTITY'];
			$production_arr[$v['BUYER_NAME']][$v['SERVING_COMPANY']][$v['LOCATION']][$v['PO_NUMBER']][$v['ITEM_NUMBER_ID']][$v['PRODUCTION_DATE']][$v['SEWING_LINE']]['SMV'] 					= $v['SET_SMV'];
			$production_arr[$v['BUYER_NAME']][$v['SERVING_COMPANY']][$v['LOCATION']][$v['PO_NUMBER']][$v['ITEM_NUMBER_ID']][$v['PRODUCTION_DATE']][$v['SEWING_LINE']]['PROD_RESO_ALLO'] 		= $v['PROD_RESO_ALLO'];
			// $production_arr[$v['BUYER_NAME']][$v['SERVING_COMPANY']][$v['LOCATION']][$v['PO_NUMBER']][$v['ITEM_NUMBER_ID']][$v['PRODUCTION_DATE']][$v['SEWING_LINE']]['ACC_PROD_DATE'] 		= $v['ACC_PROD_DATE'];
			$production_arr[$v['BUYER_NAME']][$v['SERVING_COMPANY']][$v['LOCATION']][$v['PO_NUMBER']][$v['ITEM_NUMBER_ID']][$v['PRODUCTION_DATE']][$v['SEWING_LINE']]['ACC_PROD_HOUR'][$v['ACC_PROD_HOUR']] = $v['ACC_PROD_HOUR'];

			$line_arr[$v['SEWING_LINE']] = $v['SEWING_LINE'];
		} 
		// pre($line_arr);die;
		//=================================== CLEAR TEMP ENGINE ====================================
		$con = connect();
		execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form = 87 and ref_from in(1,2,3)");
    	oci_commit($con);  
		//=================================== INSERT JOB ID INTO TEMP ENGINE ====================================

		fnc_tempengine("gbl_temp_engine", $user_id, 87, 1,$job_id_arr, $empty_arr);

		//=================================== INSERT LINE ID INTO TEMP ENGINE ====================================
		fnc_tempengine("gbl_temp_engine", $user_id, 87, 2,$line_arr, $empty_arr);

		//=================================== PRODUCTION RESOURCE SQL ====================================
		$resource_sql="select a.id,b.id as dtls_id, a.company_id, a.location_id,a.floor_id, a.line_number, b.man_power, b.operator, b.helper, b.working_hour,b.target_per_hour,b.smv_adjust,b.smv_adjust_type,TO_CHAR(b.pr_date, 'YYYY-MM-DD') as pr_date,TO_CHAR(d.prod_start_time, 'YYYY-MM-DD') as prod_start_date,TO_CHAR(d.prod_start_time, 'HH24') as prod_start_hour,TO_CHAR(d.prod_end_time, 'YYYY-MM-DD') as prod_end_date,TO_CHAR(d.prod_end_time, 'HH24') as prod_end_hour,TO_CHAR(d.lunch_start_time, 'YYYY-MM-DD') as lunch_start_date,TO_CHAR(d.lunch_start_time, 'HH24') as lunch_start_hour,TO_CHAR(d.lunch_end_time, 'YYYY-MM-DD') as lunch_end_date,TO_CHAR(d.lunch_end_time, 'HH24') as lunch_end_hour from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_time d ,gbl_temp_engine tmp where a.id=tmp.ref_val and a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and d.shift_id=1 and a.is_deleted=0 and b.is_deleted=0 and tmp.entry_form=87 and tmp.ref_from=2 and tmp.user_id=$user_id $sql_resource_cond $resource_date_cond";
		// 'HH24:MI'
		// echo $resource_sql ; die;
		$resource_dtls_arr		= array();
		$dtls_wise_resource_arr	= array();
		$resource_result 	= sql_select($resource_sql);
		foreach ($resource_result as $v) 
		{
			$resource_dtls_arr [$v['DTLS_ID']]	= $v['DTLS_ID'];

			$tmpLine = $v['LINE_NUMBER'];
			$lineArr = explode(',', $tmpLine);
			
			// make key for each line
			foreach ($lineArr as $line) 
			{
				$dtls_wise_resource_arr[$v['DTLS_ID']]['COMPANY_ID']	= $v['COMPANY_ID'];
				$dtls_wise_resource_arr[$v['DTLS_ID']]['LOCATION_ID']	= $v['LOCATION_ID'];
				$dtls_wise_resource_arr[$v['DTLS_ID']]['PR_DATE']		= $v['PR_DATE']; 
				$dtls_wise_resource_arr[$v['DTLS_ID']]['LINE']			= $line; 

				// $resource_arr[$v['COMPANY_ID']][$v['LOCATION_ID']][$v['PR_DATE']][$line]['line'] = $line;
				$resource_arr[$v['COMPANY_ID']][$v['LOCATION_ID']][$v['PR_DATE']][$line]['MAN_POWER'] 		= $v['MAN_POWER'];
				$resource_arr[$v['COMPANY_ID']][$v['LOCATION_ID']][$v['PR_DATE']][$line]['OPERATOR'] 		= $v['OPERATOR'];
				$resource_arr[$v['COMPANY_ID']][$v['LOCATION_ID']][$v['PR_DATE']][$line]['HELPER'] 			= $v['HELPER'];
				// $resource_arr[$v['COMPANY_ID']][$v['LOCATION_ID']][$v['PR_DATE']][$line]['WORKING_HOUR'] 	= $v['WORKING_HOUR']; 
				$resource_arr[$v['COMPANY_ID']][$v['LOCATION_ID']][$v['PR_DATE']][$line]['HOUR'] 			= $v['TARGET_PER_HOUR']; 
				$resource_arr[$v['COMPANY_ID']][$v['LOCATION_ID']][$v['PR_DATE']][$line]['TOTAL_LINE_HOUR']	= $v['MAN_POWER']*$v['WORKING_HOUR']; 
				$resource_arr[$v['COMPANY_ID']][$v['LOCATION_ID']][$v['PR_DATE']][$line]['SMV_ADJUST']		= $v['SMV_ADJUST']; 
				$resource_arr[$v['COMPANY_ID']][$v['LOCATION_ID']][$v['PR_DATE']][$line]['SMV_ADJUST_TYPE']	= $v['SMV_ADJUST_TYPE']; 
				$resource_arr[$v['COMPANY_ID']][$v['LOCATION_ID']][$v['PR_DATE']][$line]['PROD_START_DATE']	= $v['PROD_START_DATE']; 
				$resource_arr[$v['COMPANY_ID']][$v['LOCATION_ID']][$v['PR_DATE']][$line]['PROD_START_HOUR']	= $v['PROD_START_HOUR']; 
				$resource_arr[$v['COMPANY_ID']][$v['LOCATION_ID']][$v['PR_DATE']][$line]['PROD_END_DATE']	= $v['PROD_END_DATE']; 
				$resource_arr[$v['COMPANY_ID']][$v['LOCATION_ID']][$v['PR_DATE']][$line]['PROD_END_HOUR']	= $v['PROD_END_HOUR']; 
				$resource_arr[$v['COMPANY_ID']][$v['LOCATION_ID']][$v['PR_DATE']][$line]['LUNCH_START_DATE']= $v['LUNCH_START_DATE']; 
				$resource_arr[$v['COMPANY_ID']][$v['LOCATION_ID']][$v['PR_DATE']][$line]['LUNCH_END_HOUR']	= $v['LUNCH_END_HOUR']; 
				
			}
		} 
		// pre($resource_arr); die;
		//=================================== INSERT LINE ID INTO TEMP ENGINE ====================================
		fnc_tempengine("gbl_temp_engine", $user_id, 87, 3,$resource_dtls_arr, $empty_arr);

		//=================================== PROD RESOURCE SMV ADJ SQL ====================================
		$smv_adj_sql = "select a.dtl_id,a.number_of_emp,a.adjust_hour,a.adjustment_source as adj_src,a.total_smv from prod_resource_smv_adj a, gbl_temp_engine tmp where a.dtl_id=tmp.ref_val and a.status_active=1 and a.is_deleted=0 and tmp.entry_form=87 and tmp.ref_from=3 and tmp.user_id=$user_id and a.adjustment_source in (3,4,5,6,7)";
		// echo $smv_adj_sql; die;
		$smv_adj_sql_res = sql_select($smv_adj_sql); 
		foreach ($smv_adj_sql_res as $v) 
		{ 
			$a = $dtls_wise_resource_arr[$v['DTL_ID']];
			 
			if ($v['ADJ_SRC'] == 7 ) //NPT
			{
				$resource_arr [$a['COMPANY_ID']][$a['LOCATION_ID']][$a['PR_DATE']][$a['LINE']]['NUMBER_OF_EMP'] = $v['NUMBER_OF_EMP']; 
				$resource_arr [$a['COMPANY_ID']][$a['LOCATION_ID']][$a['PR_DATE']][$a['LINE']]['ADJUST_HOUR'] 	= $v['ADJUST_HOUR']; 
			}
			else //SICK OUT,LEAVE OUT,LATE IN,GENERAL OUT
			{
				$resource_arr [$a['COMPANY_ID']][$a['LOCATION_ID']][$a['PR_DATE']][$a['LINE']][$v['ADJ_SRC']] = $v['TOTAL_SMV'];
			}
		} 

		// pre($resource_arr); die;
		//=================================== PRE COSTING SQL ====================================
		$pre_cost_sql = "select  a.job_id,a.total_cost from wo_pre_cost_dtls a, gbl_temp_engine tmp where a.job_id=tmp.ref_val and a.status_active=1 and a.is_deleted=0 and tmp.entry_form=87 and tmp.ref_from=1 and tmp.user_id=$user_id";
		// echo $pre_cost_sql; die;
		$pre_cost_result = sql_select($pre_cost_sql);
		// pre($pre_cost_result); die;
		$pre_cost_arr = array();
		foreach ($pre_cost_result as $v) 
		{ 
			$pre_cost_arr[$v['JOB_ID']]['TOTAL_COST'] = $v['TOTAL_COST'];
		} 

		$line_wise_ttl_arr = array();
		foreach ($production_arr as $buyer_name => $companyArr) 
		{
			foreach ($companyArr as $company => $locationArr)
			{
				foreach ($locationArr as $location => $poNoArr) 
				{
					foreach ($poNoArr as $poNo => $itemNumberArr) 
					{
						foreach ($itemNumberArr as $itemNumber => $productionDateArr) 
						{
							foreach ($productionDateArr as $productionDate => $sewingLineArr) 
							{
								foreach ($sewingLineArr as $sewingLine => $v) 
								{ 
									$prod_reso_allo = $v['PROD_RESO_ALLO']; 
									if($prod_reso_allo == 1) {
										$line 	= $line_library[$prod_reso_library[$sewingLine]];
										$lineId = $prod_reso_library[$sewingLine];
									} else {
										$line 	= $line_library[$sewingLine];
										$lineId = $sewingLine;
									}
									// echo $sewingLine;die;
									$resource_data_arr 	= $resource_arr[$company][$location][$productionDate][$lineId]; 
									$prod_start_hour  	= $resource_data_arr['PROD_START_HOUR'];  
									$prod_end_hour  	= $resource_data_arr['PROD_END_HOUR'];  
									$manPower 			= $resource_data_arr['MAN_POWER']; 

									$acc_prod_hour_array= $v['ACC_PROD_HOUR'];
									$general_work_hour	= 0;
									$ot_work_hour		= 0;
									foreach ($acc_prod_hour_array as $pr_hr) 
									{
										 if($prod_start_hour <= $pr_hr && $prod_end_hour >= $pr_hr)
										 { 
											$general_work_hour++;
										 }else
										 { 
											$ot_work_hour++;
										 }
									}  

									$resource_arr[$company][$location][$productionDate][$lineId][$poNo][$itemNumber]['GEN_WORK_HR'] = $general_work_hour; 
									$resource_arr[$company][$location][$productionDate][$lineId][$poNo][$itemNumber]['OT_WORK_HR'] 	= $ot_work_hour; 
									$line_wise_ttl_arr[$v['JOB_NO']][$sewingLine] 	+= ($manPower * $general_work_hour * 60);
									
								}
							}	
						}
					}
				}
			}
		}
		// pre($resource_arr2); die;
		//=================================== CLEAR TEMP ENGINE ====================================
		execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form = 87 and ref_from in(1,2,3)");
    	oci_commit($con); 
		disconnect($con);
		$width = 2040;
		ob_start();
		?>
		<style> 
			table tr th,td{
				word-break: break-word !important;
			}
		</style>
		<fieldset>
			<div id="report_1">
				<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="tbl_caption" align="center" style="padding:10px 0;">
					<thead class="form_caption" > 
						<tr>
							<td colspan="24" align="center" style="font-size:18px; font-weight:bold" >Efficiency Report</td>
						</tr>
						<?
						if($txt_date_from && $txt_date_to) 
						{
							?>
							<tr>
								<td colspan="24" align="center" style="font-size:18px; font-weight:bold;">
									Date: <?= $txt_date_from ?> To <?= $txt_date_to ?>
								</td>
							</tr> 
							<? 
						} ?>  
					</thead>
				</table>	
				<div align="center" style="height:auto; width:<? echo $width+20;?>px; margin:0 auto; padding:0;">  
					<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
						<thead class="form_caption" >
							<tr>
								<th width="30">SL</th>
								<th width="120">Working Company</th>
								<th width="90">Buyer</th>
								<th width="110">Location</th>
								<th width="90">Style</th>
								<th width="80">Job No</th>
								<th width="80">Job Qty</th>
								<th width="80">PO/Order</th>
								<th width="90">PO/Order Qty</th>
								<th width="70">Item</th>
								<th width="50">SMV/Pcs</th>
								<th width="90">Prod. Date</th>
								<th width="50">Line</th>
								<th width="90">Prod. Qty</th>
								<th width="50">CM /Pcs</th>
								<th width="80">Total CM</th>
								<th width="80">Produce min</th>
								<th width="80">Operator</th>
								<th width="50">Helper</th>
								<th width="90">Total Manpower</th>
								<th width="90">General Min Work</th> 
								<th width="90">Overtime Min Work</th> 
								<th width="90">Work Min Red.</th> 
								<th width="50">Loss Time</th> 
								<th width="90">Total Available Min</th> 
								<th width="60">Efficiency</th>
							</tr>
						</thead>
					</table>
				</div>	
				<div style="width:<?= $width+20;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
					<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="report1_body" width="<?= $width; ?>" rules="all" align="left">
						<?php 
							$ttl_total_cm = $ttl_prod_qty = $ttl_gen_prod_min = $ttl_over_time_work = $ttl_work_min_read = $ttl_loss_time =  $ttl_produceMinutes = 0;
							$sl =1;
							foreach ($production_arr as $buyer_name => $companyArr) 
							{
								foreach ($companyArr as $company => $locationArr)
								{
									foreach ($locationArr as $location => $poNoArr) 
									{
										foreach ($poNoArr as $poNo => $itemNumberArr) 
										{
											foreach ($itemNumberArr as $itemNumber => $productionDateArr) 
											{
												foreach ($productionDateArr as $productionDate => $sewingLineArr) 
												{
													foreach ($sewingLineArr as $sewingLine => $v) 
													{
														$style 		= $v['STYLE'];
														$job_no 	= $v['JOB_NO'];
														$smv_array 	= $smv_arr[$buyer_name][$company][$location][$poNo][$productionDate][$sewingLine];
														$ttl_po_smv = 0;

														foreach ($smv_array as  $val) {
															$ttl_po_smv += $val['SET_SMV'];
														}

														$smv 			= $smv_array[$itemNumber]['SET_SMV'];
														$productionQty 	= $v['PRODUCTION_QUANTITY'];
														$produceMinutes = ($smv * $productionQty);
																
														
														$prod_reso_allo = $v['PROD_RESO_ALLO'];
														
														if($prod_reso_allo == 1) {
															$line 	= $line_library[$prod_reso_library[$sewingLine]];
															$lineId = $prod_reso_library[$sewingLine];
														} else {
															$line 	= $line_library[$sewingLine];
															$lineId = $sewingLine;
														}
														
														$resource_data_arr 	= $resource_arr[$company][$location][$productionDate][$lineId];
														$general_work_hour	= $resource_data_arr[$poNo][$itemNumber]['GEN_WORK_HR'];
														$ot_work_hour		= $resource_data_arr[$poNo][$itemNumber]['OT_WORK_HR']; 
														$job_qty 			= $v['JOB_QUANTITY'];
														$po_qty 			= $v['PO_QUANTITY'];
														$total_cost 		= $pre_cost_arr[$v['JOB_ID']]['TOTAL_COST'];
														$item_smv			= ($po_qty*$smv);
														$po_smv				= ($po_qty*$ttl_po_smv);
														$cm_pcs 			= ($item_smv/$po_smv)*$total_cost;
														$cm_pcs 			= is_infinite($cm_pcs) || is_nan($cm_pcs) ? 0 : $cm_pcs;
														$total_cm 			= $productionQty * $cm_pcs;
														$operator 			= $resource_data_arr['OPERATOR'];
														$helper 			= $resource_data_arr['HELPER'];
														$manPower 			= $resource_data_arr['MAN_POWER']; 
														$sick_out 			= $resource_data_arr[3]; 
														$leave_out 			= $resource_data_arr[4]; 
														$late_in 			= $resource_data_arr[5]; 
														$general_out 		= $resource_data_arr[6]; 
														$npt_num_of_emp 	= $resource_data_arr['NUMBER_OF_EMP'];
														$npt_hr 			= $resource_data_arr['ADJUST_HOUR'];
														$npt				= $npt_num_of_emp * $npt_hr;
														$line_ttl_gen_min	= $line_wise_ttl_arr[$job_no][$sewingLine];

														$smv_adj_ttl		= ($sick_out + $leave_out + $late_in + $general_out); 
														$general_prod_min 	= ($manPower * $general_work_hour * 60);
														$over_time_work 	= $manPower * $ot_work_hour * 60;
														$over_time_work 	= is_infinite($over_time_work) || is_nan($over_time_work) ? 0 : $over_time_work;
														$work_min_read		= ($smv_adj_ttl / $line_ttl_gen_min) * $general_prod_min;
														$work_min_read 		= is_infinite($work_min_read) || is_nan($work_min_read) ? 0 : $work_min_read;
														$loss_time			= (($npt * 60) / $line_ttl_gen_min) * $general_prod_min;
														$loss_time 			= is_infinite($loss_time) || is_nan($loss_time) ? 0 : $loss_time;
														$available_min		= $general_prod_min + $over_time_work - $work_min_read - $loss_time ;
														$effeciency 		= (($produceMinutes/$available_min) * 100);
														$effeciency 		= is_infinite($effeciency) || is_nan($effeciency) ? 0 : $effeciency;
														
														// TOTAL CALCULATION 
														$ttl_total_cm		+= $total_cm;
														$ttl_produceMinutes	+= $produceMinutes;
														$ttl_prod_qty		+= $productionQty;
														$ttl_gen_prod_min	+= $general_prod_min;
														$ttl_over_time_work	+= $over_time_work;
														$ttl_work_min_read	+= $work_min_read;
														$ttl_loss_time		+= $loss_time;

														$resrc_arr[$productionDate][$lineId]['OPERATOR']    = $resource_data_arr['OPERATOR'];
														$resrc_arr[$productionDate][$lineId]['HELPER']    = $resource_data_arr['HELPER'];
														$resrc_arr[$productionDate][$lineId]['MAN_POWER']    = $resource_data_arr['MAN_POWER'];

														$bgcolor = ($sl % 2 == 0) ? "#E9F3FF" : "#FFFFFF";
														?>

														<tr bgcolor="<?php $bgcolor; ?>" onClick="change_color('tr_<?= $sl; ?>', '<?= $bgcolor; ?>')" id="tr_<?= $sl; ?>">
															<td width="30"><?= $sl; ?></td>
															<td width="120"><?= $company_library[$company]; ?></td>
															<td width="90"><?= $buyer_library[$buyer_name]; ?></td>
															<td width="110"><?= $location_library[$location]; ?></td>
															<td width="90"><?= $style; ?></td>
															<td width="80"><?= $job_no; ?></td>
															<td width="80" align="right"><?= $job_qty; ?></td>
															<td width="80"><?= $poNo; ?></td>
															<td width="90" align="right"><?= $po_qty; ?></td> 
															<td width="70"><?= $garments_item[$itemNumber]; ?></td>
															<td width="50" align="right"><?=  number_format($smv,2); ?></td>
															<td width="90" align="center"><?= $productionDate; ?></td>
															<td width="50"><?= $line; ?></td>
															<td width="90" align="right"><?= $productionQty; ?></td>
															<td width="50" align="right" title="CM/PCS = ((PO QTY * ITEM SMV)/(PO QTY * PO SMV))*Total Cost;"><?= number_format($cm_pcs,2) ?></td>
															<td width="80" align="right" title="Total CM = Production Qty * CM PCS"><?= number_format($total_cm,2) ?></td>
															<td width="80" align="right" title="produceMinutes = (SMV * productionQty)"><?= number_format($produceMinutes, 2); ?></td>
															<td width="80" align="right"><?= $operator; ?></td>
															<td width="50" align="right"><?= $helper; ?></td>
															<td width="90" align="right"><?= $manPower; ?></td>
															<td width="90" align="right" title="Manpower  * Working Hour(<?=$general_work_hour?>) * 60"><?= number_format($general_prod_min,2); ?></td>
															<td width="90" align="right" title ="<?= " Manpower * OT Hour($ot_work_hour) * 60)" ?>"><?= number_format($over_time_work,2) ?></td> 
															<td width="90" align="right" title="<?= "(Sick Out($sick_out) + Leave Out($leave_out) + Late In ($late_in)+ Gen. Out($general_out)) / Total Line Gen. Min. Work($line_ttl_gen_min) * Gen. Min. Work " ?>"><?=number_format( $work_min_read,2) ?></td>
															<td width="50" align="right" title="<?= "(NPT NUM OF EMP($npt_num_of_emp) * NPT HR($npt_hr) * 60 )/ Total Line Gen. Min. Work($line_ttl_gen_min) * Gen. Min. Work " ?>"><?= number_format($loss_time,2) ?></td>
															<td width="90" align="right" title="Gen. Min. Work + Overtime Min Work - Work Min Red - Loss Time"><?= number_format($available_min,2) ?></td>
															<td width="60" align="right" title=" Total Produce Min/ Sum of Total Available Min"><?= number_format($effeciency,2) ; ?>%</td>
														</tr>
														<?php
															$sl++; 
													}
												}	
											}
										}
									}
								}
							}
						?>
					</table>
				</div>	
				<div style="width:<?= $width+20;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
					<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body" width="<?= $width; ?>" rules="all" align="left">
						<tfoot>
							<?
								foreach ($resrc_arr as  $date_arr) {
									 foreach ($date_arr as $v) {
										$ttl_operator += $v['OPERATOR'];
										$ttl_helper += $v['HELPER'];
										$ttl_man_power += $v['MAN_POWER'];
									 }
								}

								$total_cm_pcs 	= $ttl_total_cm / $ttl_prod_qty;
								$total_smv 		= $ttl_produceMinutes / $ttl_prod_qty;
								$ttl_avail_min	= $ttl_gen_prod_min + $ttl_over_time_work - $ttl_work_min_read - $ttl_loss_time ;
								$ttl_effi		=  (($ttl_produceMinutes/$ttl_avail_min) * 100);
							?>
							<th width="30"></th>
							<th width="120"></th>
							<th width="90"></th>
							<th width="110"></th>
							<th width="90"></th>
							<th width="80"></th>
							<th width="80" ></th>
							<th width="80"></th>
							<th width="90">Total/Avg.</th> 
							<th width="70"></th>
							<th width="50" align="right" title="Total Produce min / Total Prod. Qty"><?= number_format($total_smv,2); ?></th>
							<th width="90"></th>
							<th width="50"></th>
							<th width="90" align="right"><?= $ttl_prod_qty; ?></th>
							<th width="50" align="right" title="Sum of Total CM / Total Prod. Qty "><?= number_format($total_cm_pcs,2) ?></th>
							<th width="80" align="right" title="Sum of Total CM "><?= number_format($ttl_total_cm,2) ?></th>
							<th width="80" align="right" title="Sum of Produce min"><?= number_format($ttl_produceMinutes,2); ?></th>
							<th width="80" align="right" title="Sum of Date and Line Wise Operator "><?= $ttl_operator ?></th>
							<th width="50" align="right" title="Sum of Date and Line Wise Helper "><?= $ttl_helper ; ?></th>
							<th width="90" align="right" title="Sum of Date and Line Wise Manpower "><?= $ttl_man_power; ?></th>
							<th width="90" align="right" title="Sum of Gen. Min Work"><?= number_format($ttl_gen_prod_min,2); ?></th>
							<th width="90" align="right" title="Sum of OT Min Work"><?= number_format($ttl_over_time_work,2); ?></th>
							<th width="90" align="right" title="Sum of Work Min Red."><?= number_format($ttl_work_min_read,2) ?></th>
							<th width="50" align="right" title="Sum of Loss Time"><?= number_format($ttl_loss_time,2) ?></th> 
							<th width="90" align="right" title="Total Gen. Min. Work + Total OT Min Work - Total Work Min Red - Total Loss Time"><?= number_format($ttl_avail_min,2) ?></th>
							<th width="60" align="right"><?= number_format($ttl_effi,2) ; ?>%</th>
						</tfoot>
					</table>
				</div>	
			</div>
		</fieldset>			
		<?php 
	}
	
	$summeryDate=ob_get_contents();
	ob_clean();
	foreach (glob("$user_id*.xls") as $filename) {
		if (@filemtime($filename) < (time() - $seconds_old)) @unlink($filename);
	}
	//---------end------------//
	$name = time();
	$filename = $user_id . "_" . $name . ".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $summeryDate . $mainReportData);
	$filename = $user_id . "_" . $name . ".xls";
	echo $mainReportData.'**'.$summeryDate.'**'.$filename;
	// echo $mainReportData.'**'.$summeryDate;

	exit();
}
if($action == 'production_details') 
{
	function get_date_range($first, $last, $step = '+1 day', $output_format = 'd-M-Y') {
	    $dates = array();
	    $current = strtotime($first);
	    $last = strtotime($last);

	    while( $current <= $last ) {
	        $dates[] = date($output_format, $current);
	        $current = strtotime($step, $current);
	    }

	    return $dates;
	}
	echo load_html_head_contents('Popup Info', '../../../', 1, 1, $unicode);
	extract($_REQUEST);

	$resourceId = str_replace("'", '', $resourceId);
	$production_arr = array();
	$line_arr = array();

	$company_library = return_library_array( "select id, company_name from lib_company", 'id', 'company_name');
	$location_library = return_library_array("select id, location_name from lib_location where status_active = 1", 'id', 'location_name');
	$line_library = return_library_array("select id,line_name from lib_sewing_line where status_active=1 and is_deleted=0", 'id', 'line_name');
	$prod_reso_library = return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');

	/*$production_sql = "select distinct a.company_id, a.location, a.po_break_down_id, a.item_number_id, a.production_date, a.sewing_line, a.production_quantity, c.buyer_name, c.company_name, c.location_name, c.style_ref_no, c.job_no, c.job_quantity, d.po_number, d.po_quantity, c.set_smv
  		from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_details_master c, wo_po_break_down d
 		where a.production_type in (5) and a.id = b.mst_id $date_cond and c.job_no=d.job_no_mst and d.id=a.po_break_down_id and d.po_number = '$poNo'";*/

 	$prodMstId = str_replace("'", '', $prodMstId);
 	$production_sql = "select distinct a.id as prod_mst_id, a.serving_company, a.location, a.po_break_down_id, a.item_number_id, a.production_date, a.sewing_line, a.production_quantity, b.production_type, c.buyer_name, c.company_name, c.location_name, c.style_ref_no, c.job_no, c.job_quantity, d.po_number, d.po_quantity, c.set_smv, a.prod_reso_allo, c.set_break_down
  		from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_details_master c, wo_po_break_down d
 		where a.production_type in (5) and a.id = b.mst_id $date_cond and c.job_no=d.job_no_mst and d.id=a.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and d.status_active=1 and d.is_deleted=0 and a.id in($prodMstId)";

 	// echo $production_sql;

	$production_result = sql_select($production_sql);

	$smv_arr = array();

	foreach ($production_result as $row) {
		$production_arr[$row[csf('buyer_name')]][$row[csf('serving_company')]][$row[csf('location')]][$row[csf('style_ref_no')]][$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('item_number_id')]][$row[csf('production_date')]][$row[csf('sewing_line')]]['production_quantity'] += $row[csf('production_quantity')];
		$production_arr[$row[csf('buyer_name')]][$row[csf('serving_company')]][$row[csf('location')]][$row[csf('style_ref_no')]][$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('item_number_id')]][$row[csf('production_date')]][$row[csf('sewing_line')]]['job_quantity'] = $row[csf('job_quantity')];
		$production_arr[$row[csf('buyer_name')]][$row[csf('serving_company')]][$row[csf('location')]][$row[csf('style_ref_no')]][$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('item_number_id')]][$row[csf('production_date')]][$row[csf('sewing_line')]]['po_quantity'] = $row[csf('po_quantity')];
		$production_arr[$row[csf('buyer_name')]][$row[csf('serving_company')]][$row[csf('location')]][$row[csf('style_ref_no')]][$row[csf('job_no')]][$row[csf('po_number')]][$row[csf('item_number_id')]][$row[csf('production_date')]][$row[csf('sewing_line')]]['smv'] = $row[csf('set_smv')];

		$set_arr = explode('__', $row[csf('set_break_down')]);

		if($set_arr[0]=='') {
            $set_arr=array();
        }
        if ( count($set_arr)>0) {
            foreach( $set_arr as $set) {
                $data=explode('_',$set);
                $smv_arr[$row[csf('buyer_name')]][$row[csf('serving_company')]][$row[csf('location')]][$row[csf('style_ref_no')]][$row[csf('job_no')]][$row[csf('po_number')]][$data[0]][$row[csf('production_date')]][$row[csf('sewing_line')]]['smv'] = $data[2];
            }
        }

		// $line_arr[] = $row[csf('sewing_line')];

		/*if($row[csf('prod_reso_allo')]==1) {
			$line_number = explode(",",$prod_reso_library[$row[csf('sewing_line')]]);
			foreach($line_number as $val)
			{
				// if($sewing_line=='') $sewing_line=$sewing_line_arr[$val]; else $sewing_line.=",".$sewing_line_arr[$val];
				$line_arr[] = $val;
			}
		} else {
			$line_arr[] = $row[csf('sewing_line')];
		}*/

		$prod_reso_allo = $row[csf('prod_reso_allo')];

		$line_arr[] = $row[csf('sewing_line')];
	}
	unset($production_result);

	$line_arr = array_unique($line_arr);
	$sewingLines = implode(',', $line_arr);

	/*if ($prod_reso_allo == 1) {
		$resource_sql = "select a.company_id, a.location_id, a.floor_id, a.line_number, b.id, b.mst_id, b.from_date, b.to_date, b.man_power, b.operator, b.helper, b.line_chief, b.active_machine, b.target_per_hour, b.working_hour, b.po_id, smv_adjust, smv_adjust_type, b.capacity, b.target_efficiency
		from prod_resource_mst a, prod_resource_dtls_mast b
		where a.id in ($sewingLines) and a.id=b.mst_id and b.is_deleted=0";
	} else {
		$resource_sql = "select a.company_name as company_id, a.location_name as location_id, a.floor_name as floor_id, a.line_name as line_number, b.id, b.mst_id, b.from_date, b.to_date, b.man_power, b.operator, b.helper, b.line_chief, b.active_machine, b.target_per_hour, b.working_hour, b.po_id, smv_adjust, smv_adjust_type, b.capacity, b.target_efficiency
		from lib_sewing_line a, prod_resource_dtls_mast b
		where a.id in ($sewingLines) and a.id=b.mst_id and b.is_deleted=0";
	}*/

	$resource_sql = "select a.company_id, a.location_id, a.floor_id, a.line_number, c.id, c.mst_id, c.from_date, c.to_date, c.man_power, c.operator, c.helper, c.line_chief, c.active_machine, c.target_per_hour, c.working_hour, c.po_id, b.smv_adjust, b.smv_adjust_type, c.capacity, c.target_efficiency
  		from prod_resource_mst a, prod_resource_dtls b, prod_resource_dtls_mast c
 		where a.id = b.mst_id and b.mast_dtl_id = c.id and a.id in ($sewingLines)";

	// echo $prod_resource_query;

	$resource_result = sql_select($resource_sql);

	foreach ($resource_result as $row) {
		$daterange = array();
		$begin = $row[csf('from_date')];
		$end = $row[csf('to_date')];

		if($begin != $end) {
			$daterange = get_date_range($begin, $end);
		} else {
			$daterange[] = date('d-M-y', strtotime($begin));
		}

		// make key for each date from Actual Production Resource Entry page
		for($i=0; $i<count($daterange); $i++) {
			$date = strtoupper( date( 'd-M-y', strtotime($daterange[$i]) ) );
			// echo $daterange[$i]."<br>";
		    $resource_arr[$row[csf('company_id')]][$row[csf('location_id')]][$row[csf('mst_id')]][$date]['line'] = $row[csf('mst_id')];
			$resource_arr[$row[csf('company_id')]][$row[csf('location_id')]][$row[csf('mst_id')]][$date]['operator'] = $row[csf('operator')];
			$resource_arr[$row[csf('company_id')]][$row[csf('location_id')]][$row[csf('mst_id')]][$date]['helper'] = $row[csf('helper')];
			$resource_arr[$row[csf('company_id')]][$row[csf('location_id')]][$row[csf('mst_id')]][$date]['man_power'] = $row[csf('man_power')];
			$resource_arr[$row[csf('company_id')]][$row[csf('location_id')]][$row[csf('mst_id')]][$date]['working_hour'] = $row[csf('working_hour')];
			$resource_arr[$row[csf('company_id')]][$row[csf('location_id')]][$row[csf('mst_id')]][$date]['prod_resource_mst_id'] = $row[csf('mst_id')];
		}
	}
	unset($resource_result);

	?>
	<table border="1" rules="all" class="rpt_table" style="width: 99%">
		<thead>
			<tr>
				<th style="width: 7%; word-break: break-word;">Working Company</th>
				<th style="width: 5%; word-break: break-word;">Location</th>
				<th style="width: 6%; word-break: break-word;">Date</th>
				<th style="width: 5%; word-break: break-word;">Line</th>
				<th style="width: 3%; word-break: break-word;">SMV</th>
				<th style="width: 4%; word-break: break-word;">Prod Qty</th>
				<th style="width: 5%; word-break: break-word;" title="SMV * Production Qty">Produce Minutes</th>
				<th style="width: 4%; word-break: break-word;">Operator</th>
				<th style="width: 3%; word-break: break-word;">Helper</th>
				<th style="width: 4%; word-break: break-word;">Manpower</th>
				<th style="width: 4%; word-break: break-word;">Avg Working Hour</th>
				<th style="width: 5%; word-break: break-word;">Available Minutes</th>
				<th style="width: 4%; word-break: break-word;">Efficiency %</th>
			</tr>
		</thead>
	</table>
	<table border="1" rules="all" class="rpt_table" id="report_popup_body" style="width: 99%">
		<?php
			$sl = 1;
			$totalProduceMinutes = 0;
			$totalAvailableMinutes = 0;
			$totalManpower = 0;
			foreach ($production_arr as $buyer_name => $companyArr) {
				foreach ($companyArr as $company => $locationArr) {
					foreach ($locationArr as $location => $styleArr) {
						foreach ($styleArr as $style => $jobNoArr) {
							foreach ($jobNoArr as $jobNo => $poNoArr) {
								foreach ($poNoArr as $poNo => $itemNumberArr) {
									foreach ($itemNumberArr as $itemNumber => $productionDateArr) {
										foreach ($productionDateArr as $productionDate => $sewingLineArr) {
											foreach ($sewingLineArr as $sewingLine => $value) {

												$smv = $smv_arr[$buyer_name][$company][$location][$style][$jobNo][$poNo][$itemNumber][$productionDate][$sewingLine]['smv'];
												$productionQty = $value['production_quantity'];
												$produceMinutes = ($smv * $productionQty);
												$operator = $resource_arr[$company][$location][$sewingLine][$productionDate]['operator'];
												$helper = $resource_arr[$company][$location][$sewingLine][$productionDate]['helper'];
												$manPower = $resource_arr[$company][$location][$sewingLine][$productionDate]['man_power'];
												$workingHour = $resource_arr[$company][$location][$sewingLine][$productionDate]['working_hour'];
												$availableMinutes = ($manPower * $workingHour * 60);
												$effeciency = (($produceMinutes/$availableMinutes) * 100);
												$effeciency = is_infinite($effeciency) || is_nan($effeciency) ? 0 : $effeciency;
												$bgcolor = "#FFFFFF";

												$totalProduceMinutes += $produceMinutes;
												$totalAvailableMinutes += $availableMinutes;
												$totalManpower += $manPower;

												/*$unit_summery_arr[$location][$buyer_name]['total_produce_minutes'] += $produceMinutes;
												$unit_summery_arr[$location][$buyer_name]['total_available_minutes'] += $availableMinutes;
												$unit_summery_arr[$location][$buyer_name]['total_efficiency'] += $effeciency;

												$buyer_summery_arr[$buyer_name]['total_produce_minutes'] += $produceMinutes;
												$buyer_summery_arr[$buyer_name]['total_available_minutes'] += $availableMinutes;
												$buyer_summery_arr[$buyer_name]['total_efficiency'] += $effeciency;*/

												if ($sl % 2 == 0) {
													$bgcolor = "#E9F3FF";
												}

												if($prod_reso_allo == 1) {
													$line = $line_library[$prod_reso_library[$sewingLine]];
												} else {
													$line = $line_library[$sewingLine];
												}
							
												?>

												<tr bgcolor="<?php echo $bgcolor; ?>" onClick="change_color('tr_<?php echo $sl; ?>', '<?php echo $bgcolor; ?>')" id="tr_<?php echo $sl; ?>">
													<td style="word-break: break-all; width: 7%;"><?php echo $company_library[$company]; ?></td>
													<td style="word-break: break-all; width: 5%;"><?php echo $location_library[$location]; ?></td>
								    				<td style="word-break: break-all; width: 6%;"><?php echo $productionDate; ?></td>
								    				<td style="word-break: break-all; width: 5%;"><?php echo $line; ?></td>
								    				<td style="word-break: break-all; width: 3%;"><?php echo $smv; ?></td>
								    				<td style="word-break: break-all; width: 4%;"><?php echo $productionQty; ?></td>
								    				<td style="word-break: break-all; width: 5%;"><?php echo number_format($produceMinutes, 1); ?></td>
								    				<td style="word-break: break-all; width: 4%;"><?php echo $operator; ?></td>
								    				<td style="word-break: break-all; width: 3%;"><?php echo $helper; ?></td>
								    				<td style="word-break: break-all; width: 4%;"><?php echo $manPower; ?></td>
								    				<td style="word-break: break-all; width: 4%;"><?php echo $workingHour; ?></td>
								    				<td style="word-break: break-all; width: 5%;"><?php echo $availableMinutes; ?></td>
								    				<td style="word-break: break-all; width: 4%;"><?php echo $effeciency; ?></td>
								    			</tr>
						    				<?php
						    				$sl++;
											}
										}
									}
								}
							}
						}
					}
				}
			}
		?>
	</table>
	<table border="1" rules="all" class="rpt_table" style="width: 99%">
		<tfoot>
			<th colspan="3" style="width: 18%; text-align: right; word-break: break-word;">Total:</th>
			<th style="width: 5%; word-break: break-word;"></th>
			<th style="width: 3%; word-break: break-word;"></th>
			<th style="width: 4%; word-break: break-word;"></th>
			<th style="width: 5%; word-break: break-word;" id="value_produce_minutes"></th>
			<th style="width: 4%; word-break: break-word;" id="value_operators"></th>
			<th style="width: 3%; word-break: break-word;" id="value_helpers"></th>
			<th style="width: 4%; word-break: break-word;" id="value_man_power"></th>
			<th style="width: 4%; word-break: break-word;" title="Total Available Minutes / (Total Manpower * 60)"><?php echo $totalAvailableMinutes/($totalManpower*60); ?></th>
			<th style="width: 5%; word-break: break-word;" id="value_available_minutes"></th>
			<th style="width: 4%; word-break: break-word;" title="(Total Produce Minutes / Total Available Minutes) * 100"><?php echo ($totalProduceMinutes/$totalAvailableMinutes) * 100; ?></th>
		</tfoot>
	</table>

	<script>
		var tableFilters = {
		   	col_operation:
		   {
		   	id: ["value_produce_minutes","value_operators","value_helpers","value_man_power","value_available_minutes"],
		   	col: [6,7,8,9,11],
		   	operation: ["sum","sum","sum","sum","sum"],
		   	write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
			}
		}
		setFilterGrid("report_popup_body", -1, tableFilters);
	</script>
	<?php

	exit();
}

?>