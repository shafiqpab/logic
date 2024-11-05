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
	
$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=7 and report_id=59 and is_deleted=0 and status_active=1");
	echo $print_report_format; 	
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
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('company_id').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_common').value + '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value+'**'+<?echo $style; ?>, 'create_job_no_search_list_view', 'search_div', 'style_wise_delivery_to_kniting_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
    $wo_company_id 	= str_replace("'", "", $cbo_wo_company_id);
    $buyer_id 		= str_replace("'", "", $cbo_buyer_id);
    $job_no 		= str_replace("'", "", $txt_job_no);
    $style_ref_no 	= str_replace("'", "", $txt_style_ref_no);
    $hide_job_id 	= str_replace("'", "", $hide_job_id);
    $shipment_status= str_replace("'", "", $cbo_shipment_status);
    $date_from 		= str_replace("'", "", $txt_date_from);    
    $date_to 		= str_replace("'", "", $txt_date_to);    
    $type 			= str_replace("'", "", $type);

    $sql_cond = "";
    $sql_cond .= ($company_id!=0) ? " and a.company_name=$company_id" : "";
    $sql_cond .= ($wo_company_id!=0) ? " and d.serving_company=$wo_company_id" : "";
    $sql_cond .= ($location_id!=0) ? " and a.location_name=$location_id" : "";
    $sql_cond .= ($buyer_id!=0) ? " and a.buyer_name=$buyer_id" : "";
    $sql_cond .= ($shipment_status!=0) ? " and b.shiping_status=$shipment_status" : "";
    $sql_cond .= ($hide_job_id!="") ? " and a.id in($hide_job_id)" : "";
    $sql_cond .= ($date_from!="") ? " and d.production_date between '$date_from' and '$date_to'" : "";
    $qc_date .= ($date_from!="") ? " and a.cutting_qc_date between '$date_from' and '$date_to'" : "";

    $sql = "SELECT a.buyer_name,a.job_no,a.style_ref_no as style,a.gauge,d.production_type,e.bundle_no,e.production_qnty as qc_pass_qty,e.defect_qty,e.reject_qty from wo_po_details_master a,wo_po_break_down b, wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e where a.id=b.job_id and b.id=c.po_break_down_id and a.id=c.job_id and b.id=d.po_break_down_id and c.id=e.color_size_break_down_id and d.id=e.mst_id and d.production_type in(76) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.is_rescan=0 $sql_cond order by a.job_no,a.style_ref_no";
    // 76 52, 53, 54
    // echo $sql;
    $res = sql_select($sql);
    if(count($res)==0)
    {
    	echo "<div style='text-align:center;color:red;font-size:20px;'>Data not found!</div>";
    	die();
    }
    $data_array = array();
    $date_wise_data_array = array();
    $style_array = array();
    $style_job_array = array();
    $job_array = array();
    $particular_data_array = array();
    $bundle_array = array();
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
    	$data_array[$val['STYLE']]['buyer_name'] = $val['BUYER_NAME'];
    	$data_array[$val['STYLE']]['job_no'] = $val['JOB_NO'];
    	$data_array[$val['STYLE']]['gauge'] = $gauge;
    	$data_array[$val['STYLE']][$val['PRODUCTION_TYPE']]['qc_pass_qty'] += $val['QC_PASS_QTY'];
    	$data_array[$val['STYLE']][$val['PRODUCTION_TYPE']]['defect_qty'] += $val['DEFECT_QTY'];
    	$data_array[$val['STYLE']][$val['PRODUCTION_TYPE']]['reject_qty'] += $val['REJECT_QTY'];

    	$style_array[$val['STYLE']] = $val['STYLE'];
    	$job_array[$val['JOB_NO']] = $val['JOB_NO'];
    	$style_job_array[$val['STYLE']] = $val['JOB_NO'];
    	$bundle_array[$val['BUNDLE_NO']] = $val['BUNDLE_NO'];

    	/*if($type==2)
    	{
    		$particular_data_array[$val['STYLE']]['qc_pass_qty'] += $val['QC_PASS_QTY'];
    		$particular_data_array[$val['STYLE']]['defect_qty'] += $val['DEFECT_QTY'];
    		$particular_data_array[$val['STYLE']]['reject_qty'] += $val['REJECT_QTY'];
    	}*/
    }
   	// echo "<pre>";print_r($data_array);echo "</pre>";

    $style_cond = where_con_using_array($style_array,1,"a.style_ref");
    $job_cond = where_con_using_array($job_array,1,"a.job_no");
    $bundle_cond = where_con_using_array($bundle_array,1,"e.bundle_no");

    // ========================= others prod data ========================
     $sql = "SELECT a.style_ref_no as style ,d.production_type,e.production_qnty as qc_pass_qty,e.defect_qty,e.reject_qty from wo_po_details_master a,wo_po_break_down b, wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e where a.id=b.job_id and b.id=c.po_break_down_id and a.id=c.job_id and b.id=d.po_break_down_id and c.id=e.color_size_break_down_id and d.id=e.mst_id and d.production_type in(52,53,54,55) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.is_rescan=0 $job_cond $bundle_cond order by a.style_ref_no";
     // echo $sql;die();
     $sqlRes = sql_select($sql);
     $others_prod_data = array();
     foreach ($sqlRes as $val) 
     {
    	$others_prod_data[$val['STYLE']][$val['PRODUCTION_TYPE']]['qc_pass_qty'] += $val['QC_PASS_QTY'];
    	$others_prod_data[$val['STYLE']][$val['PRODUCTION_TYPE']]['defect_qty'] += $val['DEFECT_QTY'];
    	$others_prod_data[$val['STYLE']][$val['PRODUCTION_TYPE']]['reject_qty'] += $val['REJECT_QTY'];   
    	if($type==2 && $val['PRODUCTION_TYPE']==52)
    	{
    		$particular_data_array[$val['STYLE']]['qc_pass_qty'] += $val['QC_PASS_QTY'];
    		$particular_data_array[$val['STYLE']]['defect_qty'] += $val['DEFECT_QTY'];
    		$particular_data_array[$val['STYLE']]['reject_qty'] += $val['REJECT_QTY'];
    	}  	
     }

    // ========================= getting smv ===========================
    $smv_arr = return_library_array( "SELECT a.style_ref, b.total_smv from ppl_gsd_entry_mst a, ppl_gsd_entry_dtls b where a.id=b.mst_id and b.lib_sewing_id=20 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $style_cond",'style_ref','total_smv'); // lib_sewing_id 20 for live, 591 for dev
    // print_r($smv_arr);

    // ========================= getting bundle qty ===========================
    $bundle_cond = where_con_using_array($bundle_array,1,"b.bundle_no");
    $bundle_sql = sql_select( "SELECT a.job_no,a.cutting_qc_date as qc_date,a.loss_min, sum(b.bundle_qty) as bundle_qty from pro_gmts_cutting_qc_mst a, pro_gmts_cutting_qc_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.is_rescan=0 $job_cond $bundle_cond group by a.job_no,a.cutting_qc_date,a.loss_min");//$qc_date
    $bundle_qty_arr = array();
    $bundle_qty_arr2 = array();
    $date_wise_bundle_qty_arr = array();
    foreach ($bundle_sql as $val) 
    {
    	$bundle_qty_arr[$val['JOB_NO']] += $val['BUNDLE_QTY'];
    }
    

   // ========================== for chart =======================
    if($type==2)
    {
	    $style_name_arr = array();
	    $style_total_defect = array();
	    $style_total_reject = array();
	    foreach ($particular_data_array as $key => $value) 
	    {
	    	$qcQty = $bundle_qty_arr[$style_job_array[$key]];
	    	$style_name_arr[$key] = $key;
	    	$style_total_defect[] = ($value['defect_qty']) ? number_format((($value['defect_qty']/$qcQty)*100),2) : 0;
	    	$style_total_reject[] = ($value['reject_qty']) ? number_format((($value['reject_qty']/$qcQty)*100),2) : 0;
	    	// echo $value['defect_qty']."/".$qcQty."dfgfdgd<br>";
	    }

	     // echo "<pre>";print_r($style_name_arr);die();
	}

	ob_start();
	if($type==1)
	{
		?>
		<fieldset style="width: 1230px;margin: 0 auto;">
			<div class="title-part" style="margin: 0 auto;text-align: center;font-size: 20px;">
				<h2>Style Wise Delivery to Linking Report</h2>
				<h2>Company : <?=$company_arr[$company_id]; ?></h2>
				<h2>Date : <?=change_date_format($date_from); ?>To<?=change_date_format($date_to); ?> </h2>
			</div>
			<div class="report-container-part">
				<table cellspacing="0" border="1" class="rpt_table"   rules="all" width="1210"  align="center">
	             	<thead>
	             		<tr>
	             			<th width="30">Sl.</th>
	             			<th width="100">Buyer Name</th>
	             			<th width="80">Style</th>
	             			<th width="70">Job</th>
	             			<th width="80">GG</th>
	             			<th width="80">SMV</th>
	             			<th width="70">Prod min</th>
	             			<th width="70">Knitted Qty.</th>
	             			<th width="70">QC Qty.</th>
	             			<th width="70">QC Balance</th>
	             			<th width="70">Alter Qty.</th>
	             			<th width="70">Alter%</th>
	             			<th width="70">Damage Qty.</th>
	             			<th width="70">Damage%</th>
	             			<th width="70">QC Pass Qty.</th>
	             			<th width="70">Linking Del. Qty</th>
	             			<th width="70">Delivery Bal. Qty.</th>
	             		</tr>
	             	</thead>
	             </table>
	             <div style=" max-height:300px; width:1230px; overflow-y:scroll;" id="scroll_body">
					<table cellspacing="0" border="1" class="rpt_table"   rules="all" width="1210"  align="center" id="table_body">
		             	<tbody>
		             		<?
		             		$i=1;
		             		$tot_prod_min 	= 0;
		             		$tot_kniting_qty= 0;
		             		$tot_qc_bal 	= 0;
		             		$tot_qc_qty 	= 0;
		             		$tot_defect_qty = 0;
		             		$tot_reject_qty = 0;
		             		$tot_qc_pass_qty= 0;
		             		$tot_deli_qty 	= 0;
		             		$tot_deli_bal 	= 0;
		             		foreach ($data_array as $style => $row) 
		             		{		             			
	             				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	             				$qc_qty = $bundle_qty_arr[$style_job_array[$style]];
	             				$qc_balance = $qc_qty - $row[76]['qc_pass_qty'];
	             				$deli_balance = $qc_qty - $others_prod_data[$style][55]['qc_pass_qty'];
	             				$prod_min = $qc_qty*$smv_arr[$style];
	             				$defect_prsnt = ($qc_qty) ? ($others_prod_data[$style][52]['defect_qty']/$qc_qty)*100 : 0;
	             				$reject_prsnt = ($qc_qty) ? ($others_prod_data[$style][52]['reject_qty']/$qc_qty)*100 : 0;
			             		?>
			             		<tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $i;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">
			             			<td width="30"><?=$i;?></td>
			             			<td width="100" title="<?=$row['buyer_name'];?>"><p><?=$buyer_arr[$row['buyer_name']];?></p></td>
			             			<td width="80"><?=$style;?></td>
			             			<td width="70" align="center"><?=$row['job_no'];?></td>
			             			<td width="80"><?=$row['gauge'];?></td>
			             			<td width="80" align="center"><?=number_format($smv_arr[$style],2);?></td>
			             			<td width="70" align="right"><?=number_format($prod_min,2);?></td>
			             			<td width="70" align="right"><?=number_format($row[76]['qc_pass_qty'],0);;?></td>
			             			<td width="70" align="right"><?=number_format($qc_qty,0);;?></td>
			             			<td width="70" align="right"><?=number_format($qc_balance,0);;?></td>
			             			<td width="70" align="right"><?=number_format($others_prod_data[$style][52]['defect_qty'],0);?></td>
			             			<td width="70" align="right"><?=number_format($defect_prsnt,2);?></td>
			             			<td width="70" align="right"><?=number_format($others_prod_data[$style][52]['reject_qty'],0);?></td>
			             			<td width="70" align="right"><?=number_format($reject_prsnt,2);?></td>
			             			<td width="70" align="right"><?=number_format($others_prod_data[$style][52]['qc_pass_qty'],0);?></td>
			             			<td width="70" align="right"><?=number_format($others_prod_data[$style][55]['qc_pass_qty'],0);?></td>
			             			<td width="70" align="right"><?=number_format($deli_balance,0);?></td>
			             		</tr>
			             		<?
			             		$i++;
			             		$tot_prod_min 	+= $prod_min;
			             		$tot_kniting_qty+= $row[76]['qc_pass_qty'];
			             		$tot_qc_qty 	+=$qc_qty;
			             		$tot_qc_bal 	+= $qc_balance;
			             		$tot_defect_qty +=$others_prod_data[$style][52]['defect_qty'];
			             		$tot_reject_qty +=$others_prod_data[$style][52]['reject_qty'];
			             		$tot_qc_pass_qty+= $others_prod_data[$style][52]['qc_pass_qty'];
			             		$tot_deli_qty 	+= $others_prod_data[$style][53]['qc_pass_qty'];
			             		$tot_deli_bal 	+= $deli_balance;
				             	
				            }
			             	$tot_dft_prsnt = ($tot_qc_qty) ? $tot_defect_qty/$tot_qc_qty : 0;
			             	$tot_rej_prsnt = ($tot_reject_qty) ? $tot_defect_qty/$tot_reject_qty : 0;
				            ?>
		             	</tbody>
		            </table>	             	
	            </div>
				<table cellspacing="0" border="1" class="rpt_table"   rules="all" width="1210"  align="center">
	             	<tfoot>
	             		<tr>
	             			<th width="30">.</th>
	             			<th width="100"></th>
	             			<th width="80"></th>
	             			<th width="70"></th>
	             			<th width="80"></th>
	             			<th width="80">Grand Total</th>
	             			<th width="70"><?=number_format($tot_prod_min,0); ?></th>
	             			<th width="70"><?=number_format($tot_kniting_qty,0); ?></th>
	             			<th width="70"><?=number_format($tot_qc_qty,0); ?></th>
	             			<th width="70"><?=number_format($tot_qc_bal,0); ?></th>
	             			<th width="70"><?=number_format($tot_defect_qty,0); ?></th>
	             			<th width="70"><?=number_format($tot_dft_prsnt,2); ?></th>
	             			<th width="70"><?=number_format($tot_reject_qty,0); ?></th>
	             			<th width="70"><?=number_format($tot_rej_prsnt,2); ?></th>
	             			<th width="70"><?=number_format($tot_qc_pass_qty,0); ?></th>
	             			<th width="70"><?=number_format($tot_deli_qty,0); ?></th>
	             			<th width="70"><?=number_format($tot_deli_bal,0); ?></th>
	             		</tr>
	             	</tfoot>
	            </table>	 
			</div>
		</fieldset>
		<?
	}
	
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
	echo "$total_data####$filename####$type####".implode("__",$style_name_arr)."####".implode("__",$style_total_defect)."####".implode("__",$style_total_reject);
	exit(); 
}




