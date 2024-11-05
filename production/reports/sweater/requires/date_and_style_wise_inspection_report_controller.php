<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$colorname_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
$country_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name");
$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_short_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );

if ($action=="load_drop_down_buyer")
{	
	echo create_drop_down( "cbo_buyer_id", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );   	 
} 

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 120, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' ","id,location_name", 1, "-- Select --", $selected, "" );     	 
	exit();
}

if ($action=="print_report_button_setting")
{
	//$sql="SELECT id,template_name,module_id,report_id FROM lib_report_template WHERE template_name ='".$data."' and module_id=7 and report_id=59 and is_deleted=0 and status_active=1";
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=7 and report_id=211 and is_deleted=0 and status_active=1");
	echo "print_report_format('".$print_report_format."')"; 	
} 


if($db_type==0) $insert_year="SUBSTRING_INDEX(a.insert_date, '-', 1)";
if($db_type==2) $insert_year="extract( year from b.insert_date)";


if ($action == "job_no_search_popup") 
{
	echo load_html_head_contents("Order No Info", "../../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>

	<script>

		var selected_id = new Array;
		var selected_name = new Array;

		function check_all_data()
		{
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
			tbl_row_count = tbl_row_count - 1;

			for (var i = 1; i <= tbl_row_count; i++)
			{
				$('#tr_' + i).trigger('click');
			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}

		function js_set_value_job(str) {
			

			if (str != "")
				str = str.split("_");

			toggle(document.getElementById('tr_' + str[0]), '#FFFFCC');

			if (jQuery.inArray(str[1], selected_id) == -1) {
				selected_id.push(str[1]);
				selected_name.push(str[2]);

			} else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == str[1])
						break;
				}
				selected_id.splice(i, 1);
				selected_name.splice(i, 1);
			}
			var id = '';
			var name = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}

			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);

			$('#hide_job_id').val(id);
			$('#hide_job_no').val(name);
		}

	</script>

	</head>

	<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:780px;">
				<table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th>Company</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="170">Please Enter Job No</th>
						<th> Date</th>
						<th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
						<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
						<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
					</thead>
					<tbody>
						<tr>
							<td align="center">
								<?
								echo create_drop_down("company_id", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0  order by company_name", "id,company_name", 1, "--Select--", $company, "", 0);
								?>
							</td>                 
							<td align="center">	
								<?
								$search_by_arr = array(1 => "Job No", 2 => "Style Ref");
								$dd = "change_search_event(this.value, '0*0', '0*0', '../../') ";
								echo create_drop_down("cbo_search_by", 110, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
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
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('company_id').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_common').value + '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value+'**'+<?echo $style; ?>, 'create_job_no_search_list_view', 'search_div', 'date_and_style_wise_inspection_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action == "create_job_no_search_list_view") 
{
	$data = explode('**', $data);
	$company_id = $data[0];
	$cbo_year = "";

	$company_con='';
	if(empty($company_id))
	{
		echo "Select Company First";die;
	}else{
		$company_con=" and b.company_name=$company_id";
	}

	$search_by = $data[1];
	$search_string = "'%" . trim($data[2]) . "%'";
	$search_field='';
	if(!empty($data[2]))
	{
		if ($search_by == 1)
			$search_field = " and b.job_no_prefix_num =$data[2]";
		else if ($search_by == 2)
			$search_field = " and b.style_ref_no like ".$search_string;
	}
	

	$start_date = $data[3];
	$end_date = $data[4];

	if ($start_date != "" && $end_date != "") {
		if ($db_type == 0) {
			$date_cond = " and a.insert_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd") . "'";
		} else {
			$date_cond = " and a.insert_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
		}
	} else {
		$date_cond = "";
	}

	$arr = array(0 => $company_arr, 1 => $buyer_short_library);
	if ($db_type == 0)
	{
		$year_field = "YEAR(a.insert_date) as year";
    //$year_cond = " and YEAR(a.insert_date) = $cbo_year ";
	}
	else if ($db_type == 2)
	{
		$year_field = "to_char(a.insert_date,'YYYY') as year";
    //$year_cond = " and to_char(a.insert_date,'YYYY') = $cbo_year ";
	}
	else
	{$year_field = "";
   // $year_cond = "";
    } //defined Later
    
  

	$sql = "SELECT  b.id ,b.job_no,b.style_ref_no,b.job_no_prefix_num, b.company_name, b.buyer_name
		    from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and  a.is_deleted=0 and a.status_active=1 and 
	        b.status_active=1 and b.is_deleted=0 $company_con $date_cond   $search_field group by  b.id ,b.job_no,b.style_ref_no,b.job_no_prefix_num, b.company_name, b.buyer_name order by job_no";

	       // echo $sql;

	$conclick="id,job_no";
	 $style=$data[5];
	if($style==1)
	{
		$conclick="id,style_ref_no";
	}

    echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "150,130,140,100", "760", "320", 0, $sql, "js_set_value_job", $conclick, "", 1, "company_name,buyer_name,0,0,0", $arr, "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "", '', '0,0,0,0,0,0,3', '',1);
    exit();
}

if($action=="report_generate")
{ 
	extract($_REQUEST);
    
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0",'id','buyer_name');

    $company_id 	= str_replace("'", "", $cbo_company_id);
    $location_id 	= str_replace("'", "", $cbo_location_id);
    $buyer_id 		= str_replace("'", "", $cbo_buyer_id);
    $job_no 		= str_replace("'", "", $txt_job_no);
    $style_ref_no 	= str_replace("'", "", $txt_style_ref_no);
    $hide_job_id 	= str_replace("'", "", $hide_job_id);
    $date_from 		= str_replace("'", "", $txt_date_from);    
    $date_to 		= str_replace("'", "", $txt_date_to);    
    $type 			= str_replace("'", "", $type);

    $sql_cond = "";
    $sql_cond .= ($company_id!=0) ? " and a.company_name=$company_id" : "";
    $sql_cond .= ($location_id!=0) ? " and a.location_name=$location_id" : "";
    $sql_cond .= ($buyer_id!=0) ? " and a.buyer_name=$buyer_id" : "";
    $sql_cond .= ($hide_job_id!="") ? " and a.id in($hide_job_id)" : "";
    $sql_date_cond .= ($date_from!="") ? " and f.cutting_qc_date between '$date_from' and '$date_to'" : "";
    $qc_date .= ($date_from!="") ? " and b.cutting_qc_date between '$date_from' and '$date_to'" : "";

    $sql = "SELECT a.buyer_name,a.job_no,a.style_ref_no as style,a.gauge,f.cutting_qc_date as pdate,e.production_qnty as qc_pass_qty,e.replace_qty,e.defect_qty,e.reject_qty,f.inspector_id 
	from wo_po_details_master a,wo_po_break_down b, wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e, pro_gmts_cutting_qc_mst f 
	where a.id=b.job_id and b.id=c.po_break_down_id and a.id=c.job_id and b.id=d.po_break_down_id and c.id=e.color_size_break_down_id and d.id=e.mst_id and f.id=d.delivery_mst_id and d.production_type=52 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and e.is_rescan=0 $sql_cond $sql_date_cond order by f.cutting_qc_date";
    // echo $sql;
    $res = sql_select($sql);
    if(count($res)==0)
    {
    	echo "<div style='text-align:center;color:red;font-size:20px;'>Data not found!</div>";
    	die();
    }
    $data_array = array();
    $date_wise_data_array = array();
    $style_wise_data_array = array();
    $style_array = array();
    $style_job_array = array();
    $job_array = array();
    $particular_data_array = array();
    $job_chk_arr = array();
    foreach ($res as $val) 
    {
    	$gauge = "";
    	/*if($val['GAUGE']==2 || $val['GAUGE']==3 || $val['GAUGE']==4)
    	{
    		$gauge = "Course Guage";
    	}
    	elseif ($val['GAUGE']==6) 
    	{
    		$gauge = "Fine Guage";
    	}*/

    	if($val['GAUGE']==2 || $val['GAUGE']==3 || $val['GAUGE']==4 || $val['GAUGE']==1 || $val['GAUGE']==5 || $val['GAUGE']==8 || $val['GAUGE']==9 || $val['GAUGE']==10)
    	{
    		$gauge = "Course Guage";
    	}
    	elseif ($val['GAUGE']==6 || $val['GAUGE']==7 || $val['GAUGE']==11) 
    	{
    		$gauge = "Fine Guage";
    	}
    	$data_array[$val['PDATE']][$val['STYLE']]['buyer_name'] = $val['BUYER_NAME'];
    	$data_array[$val['PDATE']][$val['STYLE']]['gauge'] = $gauge;
    	$data_array[$val['PDATE']][$val['STYLE']]['qc_pass_qty'] += $val['QC_PASS_QTY'];
    	$data_array[$val['PDATE']][$val['STYLE']]['replace_qty'] += $val['REPLACE_QTY'];
    	$data_array[$val['PDATE']][$val['STYLE']]['defect_qty'] += $val['DEFECT_QTY'];
    	$data_array[$val['PDATE']][$val['STYLE']]['reject_qty'] += $val['REJECT_QTY'];

    	$style_array[$val['STYLE']] = $val['STYLE'];
    	$job_array[$val['JOB_NO']] = $val['JOB_NO'];
    	if(!in_array($val['JOB_NO'], $job_chk_arr))
    	{
    		$style_job_array[$val['STYLE']] .= ($style_job_array[$val['STYLE']]=="") ? $val['JOB_NO'] : ",".$val['JOB_NO'];
    		$job_chk_arr[$val['JOB_NO']] = $val['JOB_NO'];
    	}

    	if($type==2)
    	{
    		$particular_data_array[$val['STYLE']]['qc_pass_qty'] += $val['QC_PASS_QTY'];
    		$particular_data_array[$val['STYLE']]['defect_qty'] += $val['DEFECT_QTY'];
    		$particular_data_array[$val['STYLE']]['reject_qty'] += $val['REJECT_QTY'];
    	}
    	elseif ($type==4) 
    	{
    		$particular_data_array[$val['PDATE']]['qc_pass_qty'] += $val['QC_PASS_QTY'];
    		$particular_data_array[$val['PDATE']]['defect_qty'] += $val['DEFECT_QTY'];
    		$particular_data_array[$val['PDATE']]['reject_qty'] += $val['REJECT_QTY'];
    	}
		elseif($type==5)
		{

			$style_wise_data_array[$val['BUYER_NAME']][$val['STYLE']]['buyer_name'] = $val['BUYER_NAME'];
			$style_wise_data_array[$val['BUYER_NAME']][$val['STYLE']]['gauge'] = $gauge;
			$style_wise_data_array[$val['BUYER_NAME']][$val['STYLE']]['pdate'] = $val['PDATE'];
			$style_wise_data_array[$val['BUYER_NAME']][$val['STYLE']]['job_no'] = $val['JOB_NO'];
			$style_wise_data_array[$val['BUYER_NAME']][$val['STYLE']]['qc_pass_qty'] += $val['QC_PASS_QTY'];
			$style_wise_data_array[$val['BUYER_NAME']][$val['STYLE']]['replace_qty'] += $val['REPLACE_QTY'];
			$style_wise_data_array[$val['BUYER_NAME']][$val['STYLE']]['defect_qty'] += $val['DEFECT_QTY'];
			$style_wise_data_array[$val['BUYER_NAME']][$val['STYLE']]['reject_qty'] += $val['REJECT_QTY'];
		}
    }

    //  echo "<pre>";print_r($style_job_array);//die();
   

    $style_cond = where_con_using_array($style_array,1,"a.style_ref");
    $job_cond = where_con_using_array($job_array,1,"a.job_no");

    // ========================= getting smv ===========================
	$style_sql="SELECT a.style_ref, b.total_smv from ppl_gsd_entry_mst a, ppl_gsd_entry_dtls b where a.id=b.mst_id and b.lib_sewing_id=20 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $style_cond";
	//echo $style_sql;
    $smv_arr = return_library_array( $style_sql,'style_ref','total_smv'); // lib_sewing_id 20 for live, 591 for dev
    // print_r($smv_arr);

    // ========================= getting qc qty ===========================
	$sql_bndle="SELECT B.JOB_NO,B.CUTTING_QC_DATE AS QC_DATE, SUM(C.BUNDLE_QTY) AS BUNDLE_QTY,B.LOSS_MIN 
	FROM WO_PO_DETAILS_MASTER A, PRO_GMTS_CUTTING_QC_MST B, PRO_GMTS_CUTTING_QC_DTLS C
   	WHERE     B.ID = C.MST_ID
         AND B.STATUS_ACTIVE = 1
         AND B.IS_DELETED = 0
         AND C.STATUS_ACTIVE = 1
         AND C.IS_DELETED = 0
     
         AND A.JOB_NO = B.JOB_NO 
		 $sql_cond $qc_date 
	group by b.job_no,B.LOSS_MIN,B.CUTTING_QC_DATE";

	// echo $sql_bndle;
    $bundle_sql = sql_select($sql_bndle );

    $bundle_qty_arr = array();
    $bundle_qty_arr2 = array();
    $date_wise_bundle_qty_arr = array();
    foreach ($bundle_sql as $val) 
    {
    	$bundle_qty_arr[$val['QC_DATE']][$val['JOB_NO']] += $val['BUNDLE_QTY'];

    	$bundle_qty_arr2[$val['JOB_NO']] += $val['BUNDLE_QTY'];

    	$date_wise_bundle_qty_arr[$val['QC_DATE']]['qty'] += $val['BUNDLE_QTY'];
    	$date_wise_bundle_qty_arr[$val['QC_DATE']]['loss_min'] += $val['LOSS_MIN'];

    	// echo $val['JOB_NO'] ."=". $val['BUNDLE_QTY']."<br>";
    }

     //echo "<pre>"; print_r($bundle_qty_arr2);

    $date_wise_operator_arr = array();
    foreach ($res as $val) 
    {
    	$date_wise_data_array[$val['PDATE']]['qc_pass_qty'] += $val['QC_PASS_QTY'];
    	$date_wise_data_array[$val['PDATE']]['replace_qty'] += $val['REPLACE_QTY'];
    	$date_wise_data_array[$val['PDATE']]['defect_qty'] += $val['DEFECT_QTY'];
    	$date_wise_data_array[$val['PDATE']]['reject_qty'] += $val['REJECT_QTY'];
    	$date_wise_data_array[$val['PDATE']]['prod_min'] += $val['QC_PASS_QTY']*$smv_arr[$val['STYLE']];

    	$date_wise_operator_arr[$val['PDATE']] .= $val['INSPECTOR_ID']."**";
    }
    // echo "<pre>";print_r($date_wise_operator_arr);die();

    $response = file_get_contents('http://182.160.125.188:8081/hrm/api/api_data.php?company_id='.$company_id.'&from_date='.change_date_format($date_from).'&to_date='.change_date_format($date_to));
    $response = json_decode($response,true);
    // echo "<pre>"; print_r($response);die();
    $api_data_array = array();
    foreach ($response as $att_key => $att_value) 
    {
    	foreach ($att_value as $at_date => $date_value) 
    	{
    		foreach ($date_value as $key => $val) 
    		{
    			$api_data_array[strtotime($at_date)][$val['ID_CARD_NO']] += $val['WORKING_HOURS_WITHOUT_BREAK'];
    		}
    	}
    }
    // echo "<pre>";print_r($api_data_array);die();
    $date_wise_op_wo_hour = array();
    $date_wise_op_wo_hour2 = array();
    foreach ($date_wise_operator_arr as $date_key => $op_val) 
    {
    	$ex_op = array_filter(array_unique(explode("**", $op_val)));
    	foreach ($ex_op as $key => $op_id) 
    	{
    		// echo $op_id;
    		$date_wise_op_wo_hour[$date_key] += $api_data_array[strtotime($date_key)][$op_id];
    		$date_wise_op_wo_hour2[$date_key][$op_id] += $api_data_array[strtotime($date_key)][$op_id];
    	}
    }
    // echo "<pre>";print_r($style_job_array);die();


   // ========================== for chart =======================
    if($type==2 || $type==4)
    {
	    $style_name_arr = array();
	    $style_total_defect = array();
	    $style_total_reject = array();
	    foreach ($particular_data_array as $key => $value) 
	    {
	    	$qcQty = 0;
	    	if($type==4)
	    	{
	    		$qcQty = $date_wise_bundle_qty_arr[$key]['qty'];
	    	}
	    	else
	    	{
	    		$style_job_ex = explode(",", $style_job_array[$key]);
	    		foreach ($style_job_ex as $value2) 
	    		{
	    			$qcQty += $bundle_qty_arr2[$value2];
	    		}
	    	}
	    	$style_name_arr[$key] = $key;
	    	$style_total_defect[] = ($value['defect_qty']) ? number_format((($value['defect_qty']/$qcQty)*100),2) : 0;
	    	$style_total_reject[] = ($value['reject_qty']) ? number_format((($value['reject_qty']/$qcQty)*100),2) : 0;
	    	// echo $value['defect_qty']."/".$qcQty."dfgfdgd<br>";
	    }

	     // echo "<pre>";print_r($style_job_array);die();
	}

	ob_start();
	if($type==1)
	{
		?>
		<fieldset style="width: 1020px;margin: 0 auto;">
			<div class="title-part" style="margin: 0 auto;text-align: center;font-size: 20px;">
				<h2>Date and Style wise Inspection Report</h2>
				<h2>Company : <?=$company_arr[$company_id]; ?></h2>
				<h2>Date : <?=change_date_format($date_from); ?> To <?=change_date_format($date_to); ?> </h2>
			</div>
			<div class="report-container-part">
				<table cellspacing="0" border="1" class="rpt_table"   rules="all" width="1000"  align="center">
	             	<thead>
	             		<tr>
	             			<th width="30">Sl.</th>
	             			<th width="70">Date</th>
	             			<th width="100">Buyer Name</th>
	             			<th width="80">Style</th>
	             			<th width="80">GG</th>
	             			<th width="80">SMV</th>
	             			<th width="80">Prod min</th>
	             			<th width="80">QC Qty.</th>
	             			<th width="80">QC Pass Qty.</th>
	             			<th width="80">Alter Qty.</th>
	             			<th width="80">Alter%</th>
	             			<th width="80">Damage Qty.</th>
	             			<th width="80">Damage%</th>
	             		</tr>
	             	</thead>
	             </table>
	             <div style=" max-height:300px; width:1020px; overflow-y:scroll;" id="scroll_body">
					<table cellspacing="0" border="1" class="rpt_table"   rules="all" width="1000"  align="center" id="table_body">
		             	<tbody>
		             		<?
		             		$i=1;
		             		$tot_prod_min = 0;
		             		$tot_qc_pass_qty = 0;
		             		$tot_qc_qty = 0;
		             		$tot_defect_qty = 0;
		             		$tot_reject_qty = 0;
		             		foreach ($data_array as $pdate => $date_val) 
		             		{		      
								       			
			             		$date_wise_prod_min = 0;
			             		$date_wise_qc_pass_qty = 0;
			             		$date_wise_qc_qty = 0;
			             		$date_wise_defect_qty = 0;
			             		$date_wise_reject_qty = 0;
		             			foreach ($date_val as $style => $row) 
		             			{
									 $qc_pass_rplc_qty=$row['qc_pass_qty']-$row["replace_qty"];
									 //echo $qc_pass_rplc_qty;
		             				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		             				// $qc_qty = $bundle_qty_arr[$pdate][$style_job_array[$style]];
		             				$qc_qty = 0;							    	
						    		$style_job_ex = explode(",", $style_job_array[$style]);
									
						    		foreach ($style_job_ex as $key2 => $value2) 
						    		{
										
						    			$qc_qty += $bundle_qty_arr[$pdate][$value2];
										//echo "<pre>";print_r($bundle_qty_arr);echo "</pre>";
						    		}
							    	// echo "<pre>";print_r($bundle_qty_arr);echo "</pre>";
		             				// echo $style_job_array[$style]."=".$bundle_qty_arr[$pdate][$style_job_array[$style]]."<br>";
		             				$prod_min = $qc_qty*$smv_arr[$style];
		             				$defect_prsnt = ($row['defect_qty']/$qc_qty)*100;
		             				$reject_prsnt = ($row['reject_qty']/$qc_qty)*100;
				             		?>
				             		<tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $i;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">
				             			<td width="30"><?=$i;?></td>
				             			<td width="70" align="center"><?=change_date_format($pdate);?></td>
				             			<td width="100" title="<?=$row['buyer_name'];?>"><p><?=$buyer_arr[$row['buyer_name']];?></p></td>
				             			<td width="80"><?=$style;?></td>
				             			<td width="80"><?=$row['gauge'];?></td>
				             			<td width="80" align="center"><?=number_format($smv_arr[$style],2);?></td>
				             			<td width="80" align="right"><?=number_format($prod_min,2);?></td>
				             			<td width="80" align="right"><?=number_format($qc_qty,0);?></td>
				             			<td width="80" align="right"><?=number_format($qc_pass_rplc_qty,0);?></td>
				             			<td width="80" align="right"><?=number_format($row['defect_qty'],0);?></td>
				             			<td width="80" align="right"><?=number_format($defect_prsnt,2);?></td>
				             			<td width="80" align="right"><?=number_format($row['reject_qty'],0);?></td>
				             			<td width="80" align="right"><?=number_format($reject_prsnt,2);?></td>
				             		</tr>
				             		<?
				             		$i++;

				             		$date_wise_prod_min += $prod_min;
				             		$date_wise_qc_qty +=$qc_qty;
				             		$date_wise_qc_pass_qty += $qc_pass_rplc_qty;
				             		$date_wise_defect_qty +=$row['defect_qty'];
				             		$date_wise_reject_qty +=$row['reject_qty'];

				             		$tot_prod_min += $prod_min;
				             		$tot_qc_qty +=$qc_qty;
				             		$tot_qc_pass_qty += $qc_pass_rplc_qty;
				             		$tot_defect_qty +=$row['defect_qty'];
				             		$tot_reject_qty +=$row['reject_qty'];
				             	}
				             	$date_wise_dft_prsnt = ($date_wise_qc_qty) ? $date_wise_defect_qty/$date_wise_qc_qty : 0;
				             	// $date_wise_rej_prsnt = ($date_wise_reject_qty) ? $date_wise_defect_qty/$date_wise_reject_qty : 0;
								 $date_wise_rej_prsnt = ($date_wise_reject_qty) ? $date_wise_reject_qty/$date_wise_qc_qty : 0;
				             	?>
				             	<tr style="background: #cddcdc;font-weight: bold;text-align: right;">
			             			<td width="30">.</td>
			             			<td width="70"></td>
			             			<td width="100"></td>
			             			<td width="80"></td>
			             			<td width="80"></td>
			             			<td width="80">Date Wise Total</td>
			             			<td width="80"><?=number_format($date_wise_prod_min,2); ?></td>
			             			<td width="80"><?=number_format($date_wise_qc_qty,0); ?></td>
			             			<td width="80"><?=number_format($date_wise_qc_pass_qty,0); ?></td>
			             			<td width="80"><?=number_format($date_wise_defect_qty,0); ?></td>
			             			<td width="80"><?=number_format($date_wise_dft_prsnt*100,2); ?></td>
			             			<td width="80"><?=number_format($date_wise_reject_qty,0); ?></td>
			             			<td width="80"><?=number_format($date_wise_rej_prsnt*100,2); ?></td>
			             		</tr>
				             	<?
				            }
			             	$tot_dft_prsnt = ($tot_qc_qty) ? $tot_defect_qty/$tot_qc_qty : 0;
			             	// $tot_rej_prsnt = ($tot_reject_qty) ? $tot_defect_qty/$tot_reject_qty : 0;
							 $tot_rej_prsnt = ($tot_reject_qty) ? $tot_reject_qty/$tot_qc_qty : 0;
				            ?>
		             	</tbody>
		            </table>	             	
	            </div>
				<table cellspacing="0" border="1" class="rpt_table"   rules="all" width="1000"  align="center">
	             	<tfoot>
	             		<tr>
	             			<th width="30">.</th>
	             			<th width="70"></th>
	             			<th width="100"></th>
	             			<th width="80"></th>
	             			<th width="80"></th>
	             			<th width="80">Grand Total</th>
	             			<th width="80"><?=number_format($tot_prod_min,0); ?></th>
	             			<th width="80"><?=number_format($tot_qc_qty,0); ?></th>
	             			<th width="80"><?=number_format($tot_qc_pass_qty,0); ?></th>
	             			<th width="80"><?=number_format($tot_defect_qty,0); ?></th>
	             			<th width="80"><?=number_format($tot_dft_prsnt*100,2); ?></th>
	             			<th width="80"><?=number_format($tot_reject_qty,0); ?></th>
	             			<th width="80"><?=number_format($tot_rej_prsnt*100,2); ?></th>
	             		</tr>
	             	</tfoot>
	            </table>	 
			</div>
		</fieldset>
		<?
	}
	elseif ($type==2) 
	{
		$show_chart = "show_style_wise";
	}
	elseif ($type==3) 
	{		
		?>
		<fieldset style="width: 920px;margin: 0 auto;">
			<div class="title-part" style="margin: 0 auto;text-align: center;font-size: 20px;">
				<h2>Date and Style wise Inspection Report</h2>
				<h2>Company : <?=$company_arr[$company_id]; ?></h2>
				<h2>Date : <?=change_date_format($date_from); ?>To<?=change_date_format($date_to); ?> </h2>
			</div>
			<div class="report-container-part">
				<table cellspacing="0" border="1" class="rpt_table"   rules="all" width="900"  align="center">
	             	<thead>
	             		<tr>
	             			<th width="30">Sl.</th>
	             			<th width="70">Date</th>
	             			<th width="80">QC Qty.</th>
	             			<th width="80">Prod min</th>
	             			<th width="80">Working Min</th>
	             			<th width="80">Loss Min</th>
	             			<th width="80">Effi%</th>
	             			<th width="80">QC Pass Qty.</th>
	             			<th width="80">Alter Qty.</th>
	             			<th width="80">Alter%</th>
	             			<th width="80">Damage Qty.</th>
	             			<th width="80">Damage%</th>
	             		</tr>
	             	</thead>
	             </table>
	             <div style=" max-height:300px; width:920px; overflow-y:scroll;" id="scroll_body">
					<table cellspacing="0" border="1" class="rpt_table"   rules="all" width="900"  align="center" id="table_body_1">
		             	<tbody>
		             		<?
		             		$i=1;
		             		$tot_prod_min = 0;
		             		$tot_working_min = 0;
		             		$tot_loss_min = 0;
		             		$tot_qc_pass_qty = 0;
		             		$tot_qc_qty = 0;
		             		$tot_defect_qty = 0;
		             		$tot_reject_qty = 0;
		             		// $style_total_efii = array();
							
		             		foreach ($date_wise_data_array as $pdate => $row) 
		             		{
								
								 $qc_pass_rplc_qty=$row['qc_pass_qty']-$row["replace_qty"];
	             				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

	             				$qc_qty = $date_wise_bundle_qty_arr[$pdate]['qty'];

								
							

	             				$loss_min = $date_wise_bundle_qty_arr[$pdate]['loss_min'];
	             				$prod_min = $qc_qty*$smv_arr[$style];
	             				$defect_prsnt = ($row['defect_qty']/$qc_qty)*100;
	             				$reject_prsnt = ($row['reject_qty']/$qc_qty)*100;
	             				$working_min = $date_wise_op_wo_hour[$pdate];
	             				$effi = $row['prod_min'] / ($working_min - $loss_min)*100;
	             				// $style_total_efii[] = number_format($effi,2);
			             		?>
			             		<tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $i;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">
			             			<td width="30"><?=$i;?></td>
			             			<td width="70" align="center"><?=change_date_format($pdate);?></td>
			             			<td width="80" align="right"><?=number_format($qc_qty,0);?></td>
			             			<td width="80" align="right"><?=number_format($row['prod_min'],2);?></td>
			             			<td width="80" align="right"><?=number_format($working_min,2);?></td>
			             			<td width="80" align="right"><?=number_format($loss_min,2);?></td>
			             			<td width="80" align="right"><?=number_format($effi,2);?></td>
			             			<td width="80" align="right"><?=number_format($qc_pass_rplc_qty,0);?></td>
			             			<td width="80" align="right"><?=number_format($row['defect_qty'],0);?></td>
			             			<td width="80" align="right"><?=number_format($defect_prsnt,2);?></td>
			             			<td width="80" align="right"><?=number_format($row['reject_qty'],0);?></td>
			             			<td width="80" align="right"><?=number_format($reject_prsnt,2);?></td>
			             		</tr>
			             		<?
			             		$i++;

			             		$tot_prod_min += $row['prod_min'];
		             			$tot_working_min += $working_min;
		             			$tot_loss_min += $loss_min;
			             		$tot_qc_qty +=$qc_qty;
			             		$tot_qc_pass_qty += $qc_pass_rplc_qty;
			             		$tot_defect_qty +=$row['defect_qty'];
			             		$tot_reject_qty +=$row['reject_qty'];
				            }
			             	$tot_dft_prsnt = ($tot_qc_qty) ? $tot_defect_qty/$tot_qc_qty : 0;
			             	// $tot_rej_prsnt = ($tot_reject_qty) ? $tot_defect_qty/$tot_reject_qty : 0;
							 	// $tot_rej_prsnt = ($tot_reject_qty) ? $tot_defect_qty/$tot_reject_qty : 0;
							 $tot_rej_prsnt = ($tot_reject_qty) ? $tot_reject_qty/$tot_qc_qty : 0;
				            ?>
		             	</tbody>
		            </table>	             	
	            </div>
				<table cellspacing="0" border="1" class="rpt_table"   rules="all" width="900"  align="center">
	             	<tfoot>
	             		<tr>
	             			<th width="30">.</th>
	             			<th width="70">Grand Total</th>
	             			<th width="80"><?=number_format($tot_qc_qty,0); ?></th>
	             			<th width="80"><?=number_format($tot_prod_min,2); ?></th>
	             			<th width="80"><?=number_format($tot_working_min,2);?></th>
	             			<th width="80"><?=number_format($tot_loss_min,2);?></th>
	             			<th width="80"><?=number_format($a,2);?></th>
	             			<th width="80"><?=number_format($tot_qc_pass_qty,0); ?></th>
	             			<th width="80"><?=number_format($tot_defect_qty,0); ?></th>
	             			<th width="80"><?=number_format($tot_dft_prsnt*100,2); ?></th>
	             			<th width="80"><?=number_format($tot_reject_qty,0); ?></th>
	             			<th width="80"><?=number_format($tot_rej_prsnt*100,2); ?></th>
	             		</tr>
	             	</tfoot>
	            </table>	 
			</div>
		</fieldset>
		<?
	}
	elseif ($type==4) 
	{
		$show_chart = "show_date_wise";
		$style_total_efii = array();
 		foreach ($date_wise_data_array as $pdate => $row) 
 		{
 			$qc_qty = $date_wise_bundle_qty_arr[$pdate]['qty'];
			$loss_min = $date_wise_bundle_qty_arr[$pdate]['loss_min'];
			$prod_min = $qc_qty*$smv_arr[$style];
			$working_min = $date_wise_op_wo_hour[$pdate];
			$effi = $row['prod_min'] / ($working_min - $loss_min)*100;
			$style_total_efii[] = number_format($effi,2);
		}
	}elseif($type==5)
	{
		?>
		<fieldset style="width: 968px;margin: 0 auto;">
			<div class="title-part" style="margin: 0 auto;text-align: center;font-size: 20px;">
				<h2>Date and Style wise Inspection Report</h2>
				<h2>Company : <?=$company_arr[$company_id]; ?></h2>
				<h2>Date : <?=change_date_format($date_from); ?>To<?=change_date_format($date_to); ?> </h2>
			</div>
			<div class="report-container-part">
				<table cellspacing="0" border="1" class="rpt_table"   rules="all" width="950"  align="center">
	             	<thead>
	             		<tr>
	             			<th width="30" >Sl.</th>
	             			<th width="120">Buyer Name</th>
	             			<th width="80" >Style</th>
	             			<th width="80" >GG</th>
	             			<th width="80" >SMV</th>
	             			<th width="80" >Prod min</th>
	             			<th width="80" >QC Qty.</th>
	             			<th width="80" >QC Pass Qty.</th>
	             			<th width="80" >Alter Qty.</th>
	             			<th width="80" >Alter%</th>
	             			<th width="80" >Damage Qty.</th>
	             			<th width="80" >Damage%</th>
	             		</tr>
	             	</thead>
	            </table>
	            <div style=" max-height:300px; width:968px; overflow-y:scroll;" id="scroll_body">
					<table cellspacing="0" border="1" class="rpt_table"   rules="all" width="950"  align="center" id="table_body_1">
		             	<tbody>
							<?  
							$i=1;

							$tot_prod_min = 0;
		             		$tot_qc_pass_qty = 0;
		             		$tot_qc_qty = 0;
		             		$tot_defect_qty = 0;
		             		$tot_reject_qty = 0;
							foreach ($style_wise_data_array as $buyer_key => $buyer_data) 
							{
								foreach ($buyer_data as $style_key => $row) 
								{
									$qc_pass_rplc_qty=$row['qc_pass_qty']-$row["replace_qty"];
									
									$qc_qty = 0;							    	
						    		$style_job_ex = explode(",", $style_job_array[$style_key]);
									
						    		foreach ($style_job_ex as $key2 => $value2) 
						    		{
						    			$qc_qty += $bundle_qty_arr2[$value2];
						    		}
							    	// echo "<pre>";
									// print_r($qc_qty);
									// echo "</pre>";
									$prod_min = $qc_qty*$smv_arr[$style_key];
		             				$defect_prsnt = ($row['defect_qty']/$qc_qty)*100;
		             				$reject_prsnt = ($row['reject_qty']/$qc_qty)*100;
									?>
									<tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $i;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">
										<td width="30" ><?=$i;?></td>
										<td width="120"><?=$buyer_arr[$row["buyer_name"]];?></td>
										<td width="80" style="word-break: break-all;"><?=$style_key;?></td>
										<td width="80"  style="word-break: break-all;"><?=$row["gauge"];?></td>
										<td width="80" align="right"><?=number_format($smv_arr[$style_key],2);?></td>

										<td width="80" align="right"><?=number_format($prod_min,2);?></td>
										<td width="80" align="right"><?=number_format($qc_qty,0);?></td>
				             			<td width="80" align="right"><?=number_format($qc_pass_rplc_qty,0);?></td>
				             			<td width="80" align="right"><?=number_format($row['defect_qty'],0);?></td>
				             			<td width="80" align="right"><?=number_format($defect_prsnt,2);?></td>
				             			<td width="80" align="right"><?=number_format($row['reject_qty'],0);?></td>
				             			<td width="80" align="right"><?=number_format($reject_prsnt,2);?></td>
									</tr>
									<?
									$i++;
									$tot_prod_min += $prod_min;
									$tot_qc_pass_qty += $qc_pass_rplc_qty;
									$tot_qc_qty += $qc_qty;
									$tot_defect_qty += $row['defect_qty'];
									$tot_reject_qty += $row['reject_qty'];
								}
								$tot_dft_prsnt = ($tot_qc_qty) ? $tot_defect_qty/$tot_qc_qty : 0;
			             	// $tot_rej_prsnt = ($tot_reject_qty) ? $tot_defect_qty/$tot_reject_qty : 0;
							 $tot_rej_prsnt = ($tot_reject_qty) ? $tot_reject_qty/$tot_qc_qty : 0;
							}
							
							?>
						</tbody>
					</table>
				</div>
				<table cellspacing="0" border="1" class="rpt_table"   rules="all" width="950"  align="center">
	             	<tfoot>
	             		<tr >
						 	<th width="30" >&nbsp;</th>
							<th width="120">&nbsp;</th>
							<th width="80" >&nbsp;</th>
							<th width="80" >&nbsp;</th>
				
							<th width="80"  style="text-align: right;"><strong>Total:</strong></th>
							<th width="80" style="text-align: right;"><?=number_format($tot_prod_min,0); ?></th>
	             			<th width="80" style="text-align: right;"><?=number_format($tot_qc_qty,0); ?></th>
	             			<th width="80" style="text-align: right;"><?=number_format($tot_qc_pass_qty,0); ?></th>
	             			<th width="80" style="text-align: right;"><?=number_format($tot_defect_qty,0); ?></th>
	             			<th width="80" style="text-align: right;"><?=number_format($tot_dft_prsnt*100,2); ?></th>
	             			<th width="80" style="text-align: right;"><?=number_format($tot_reject_qty,0); ?></th>
	             			<th width="80" style="text-align: right;"><?=number_format($tot_rej_prsnt*100,2); ?></th>
						</tr>
					<tfoot>
				</table>
			</div>
			
		</fieldset>
		<?
		




	}

	$particular_name = implode(',', $particular_name_arr);
	$particular_value = implode(',', $fparticular_value_arr);
	
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	//$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename####$type####".implode("__",$style_name_arr)."####".implode("__",$style_total_defect)."####".implode("__",$style_total_reject)."####".implode("__",$style_total_efii);
	exit(); 
}




