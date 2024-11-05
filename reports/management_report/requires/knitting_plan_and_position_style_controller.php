<?php
//--------------------------------------------------------------------------------------------------------------------
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header('location:login.php');
include('../../../includes/common.php');
include('../../../includes/class4/class.conditions.php');
include('../../../includes/class4/class.reports.php');
include('../../../includes/class4/class.yarns.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$user_level=$_SESSION['logic_erp']["user_level"];
//--------------------------------------------------------------------------------------------------------------------

if($action == 'load_drop_down_buyer') {
	echo create_drop_down('cbo_buyer_name', 163, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name", 'id,buyer_name', 1, '-- Select Buyer --', $data[2], '');
		exit();
}

if($action=="search_popup") {
	echo load_html_head_contents('Search', '../../../', 1, 1, '', '', '');
	extract($_REQUEST);
?>
     
	<script>
		var searchType = <?php echo $searchType; ?>;

		function js_set_value(values) {			
			var values=values.split("_");

			document.getElementById('hdnJobNo').value = values[0];
			document.getElementById('hdnOrderNo').value = values[1];
			document.getElementById('hdnYear').value = values[2];

			parent.searchWindow.hide();
		}

		/**
		 * change search by title after the popup is loaded
		 */
		window.addEventListener('load', function() {
		    if(searchType == 1) {
				document.getElementById('search_by_td_up').innerHTML = 'Please Enter Job No';
			}
		})
	
    </script>

</head>

<body>
<div align="center">
	<form name="searchForm" id="searchForm">
		<fieldset style="width:780px;">
            <table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Order No</th>
                    <th>Shipment Date</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
                    <input type="hidden" name="hdnJobNo" id="hdnJobNo" />
                    <input type="hidden" name="hdnOrderNo" id="hdnOrderNo" />
                    <input type="hidden" name="hdnYear" id="hdnYear" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <?php
								echo create_drop_down( 'cbo_buyer_name', 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (2,3)) order by buy.buyer_name", 'id,buyer_name', 1, '-- All Buyer--', 0, '' );
							?>
                        </td>
                        <td align="center">
                    	<?php
                       		$search_by_arr=array(1=>'Job No',2=>'Order No');
                       		if($searchType == 1) {
                       			$selected_index = 1;
                       		} else {
                       			$selected_index = 2;
                       		}
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down('cbo_search_by', 110, $search_by_arr, '', 0, '', $selected_index, $dd, 0);
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<?php echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('cbo_year_selection').value, 'create_search_list_view', 'search_div', 'knitting_plan_and_position_style_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

if($action == 'create_search_list_view') {
	$data=explode('**', $data);

	$company_id=$data[0];
	$search_by=$data[2];
	$year_to_search = trim($data[6]);
	$txt_date_from =trim($data[4]);
	$txt_date_to =trim($data[5]);
	$start_date = '01-Jan-'.$year_to_search;
	$end_date = '31-Dec-'.$year_to_search;
	$conditions = '';
	$buyer_arr=return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$company_arr=return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
	$list_arr=array(0=>$company_arr,1=>$buyer_arr);
	
	if($data[1]!=0) {
		$conditions=" and a.buyer_name=$data[1]";
	}

	if(trim($data[3]) != '') {

		if($search_by==1) {
			$search_field='and a.job_no';
		}
		else {
			$search_field='and b.po_number';
		}

		$conditions .= $search_field;
		$conditions .= " like '%$data[3]%'";
	}

	if(trim($data[4]) != '') {
		$start_date = trim($data[4]);
	}

	if(trim($data[5]) != '') {
		$end_date = trim($data[5]);
	}

	if($db_type==0) {
		if ($txt_date_from!="" &&  $txt_date_to!="") {
			$date_cond = "and b.pub_shipment_date between '".change_date_format($txt_date_from, "yyyy-mm-dd", "-")."' and '".change_date_format($txt_date_to, "yyyy-mm-dd", "-")."'";
			} else {
				$date_cond = "and b.pub_shipment_date between '".$start_date."' and '".$end_date."'";
			}
	}
	else if($db_type==2) {
		if ($txt_date_from!="" &&  $txt_date_to!="") {
			$date_cond = "and b.pub_shipment_date between '".change_date_format($txt_date_from, "yyyy-mm-dd", "-",1)."' and '".change_date_format($txt_date_to, "yyyy-mm-dd", "-",1)."'";
		} else {
			$date_cond = "and b.pub_shipment_date between '".$start_date."' and '".$end_date."'";
		}
	}
			
	$sql = "select a.company_name, a.buyer_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, b.po_number, b.pub_shipment_date, extract(year from b.pub_shipment_date) as year
		from wo_po_details_master a, wo_po_break_down b, tna_process_mst c 
		where a.job_no = b.job_no_mst and c.job_no = a.job_no and a.company_name=$company_id $date_cond $conditions
		group by a.company_name, a.buyer_name, a.job_no, a.style_ref_no, b.po_number, b.pub_shipment_date, a.job_no_prefix_num";
	// echo $sql;die;
	echo create_list_view('tbl_list_search', 'Company, Buyer Name, Year, Job No, Style Ref. No, Po No, Shipment Date', '80,80,50,130,130,130', '760','265', 0, $sql , 'js_set_value', 'job_no,po_number,year', '', 1, 'company_name,buyer_name,0,0,0,0,0', $list_arr , 'company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,pub_shipment_date', '','', '0,0,0,0,0,0,3', '');
	
   exit();
}

if($action == 'generate_report') {
	/**
	 * pending tasks
	 * *************
	 * write query for both oracle and mysql
	 */
	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$html_res='';
	$po_date_cond;
	$sl = 1;
	$conditions='';
	$wo_dtls_arr = array();
	$job_cond;
	$tna_all_task;
	$year_to_search = str_replace("'", "", $cbo_year_selection);
	$company_id = str_replace("'", "", $cbo_company_name);
	$buyer_id = str_replace("'", "", $cbo_buyer_name);
	$ref_no = str_replace("'", "", $txt_ref_no);
	$file_no = str_replace("'", "", $txt_file_no);
	$job_no = str_replace("'", "", $txt_job_no);
	$ord_no = str_replace("'", "", $txt_order_no);
	$start_date = '01-Jan-'.$year_to_search;
	$end_date = '31-Dec-'.$year_to_search;
	$wo_po_details_master = array();
	$po_no_arr = array();
	$tna_mst_arr = array();
	$labdip_arr = array();
	$prod_dtls_arr = array();
	$fab_cons_arr = array();
	$yarn_data_array = array();
	$company_name;
	$condition= new condition();

	if($search_type == 1) {
		$po_date_cond = "and b.po_received_date between '".'01-Jan-'.$year_to_search."' and '".'31-Dec-'.$year_to_search."'";
	} else {
		if($db_type==0) {
				if ($txt_date_from!="" &&  $txt_date_to!="") {
					$po_date_cond = "and b.pub_shipment_date between '".change_date_format($txt_date_from, "yyyy-mm-dd", "-")."' and '".change_date_format($txt_date_to, "yyyy-mm-dd", "-")."'";
				} else {
					$po_date_cond = "and b.pub_shipment_date between '".$start_date."' and '".$end_date."'";
				}
			}

			else if($db_type==2) {
				if ($txt_date_from!="" &&  $txt_date_to!="") {
					$po_date_cond = "and b.pub_shipment_date between '".change_date_format($txt_date_from, "yyyy-mm-dd", "-",1)."' and '".change_date_format($txt_date_to, "yyyy-mm-dd", "-",1)."'";
				} else {
					$po_date_cond = "and b.pub_shipment_date between '".$start_date."' and '".$end_date."'";
				}
			}
	}
	

	$merchandiser_arr = return_library_array("select id, team_member_name from lib_mkt_team_member_info where is_deleted=0  and status_active=1", 'id', 'team_member_name');

	$company_name = sql_select("select company_name from lib_company where id=$company_id");
	$company_name = $company_name[0][csf('company_name')];


	if($ref_no!="") {
		$conditions .= " and b.grouping like '%$ref_no%'";
	}
	if($file_no!="") {
		$conditions .= " and b.file_no like '%$file_no%'";
	}
	if($job_no!="") {
		$conditions .= " and b.job_no_mst like '%$job_no%'";
		$job_cond .= " and job_no_mst like '%$job_no%'";
	}
	if($ord_no!="") {
		$conditions .= " and b.po_number like '%$ord_no%'";
	}
	if($buyer_id!=0) {
		$conditions .= " and a.buyer_name = $buyer_id";
	}
	
	$sql_wo_dtls = "select a.dealing_marchant, a.job_no, a.style_ref_no, b.id as po_id, b.po_received_date, b.po_number, b.grouping, b.po_quantity, b.plan_cut, b.pub_shipment_date
		from wo_po_details_master a, wo_po_break_down b
		where a.company_name=$company_id and a.job_no = b.job_no_mst and a.is_deleted=0 and a.status_active=1 $po_date_cond $conditions order by b.grouping";

	// echo $sql_wo_dtls.'<br>';

   	$result_wo_dtls = sql_select($sql_wo_dtls) ;
	
	foreach($result_wo_dtls as  $row) {
		$po_no_arr[]=$row[csf('po_id')];

		if(isset($wo_dtls_arr[$row[csf('job_no')]])) {
			$wo_dtls_arr[$row[csf('job_no')]]['po_quantity']+=$row[csf('po_quantity')];
			$wo_dtls_arr[$row[csf('job_no')]]['plan_cut']+=$row[csf('plan_cut')];

			if($wo_dtls_arr[$row[csf('job_no')]]['po_number'] != $row[csf('po_number')]) {
				$wo_dtls_arr[$row[csf('job_no')]]['po_number'] .= ', ' . $row[csf('po_number')];
			}
		} else {
			$wo_dtls_arr[$row[csf('job_no')]]['merchandiser']=$row[csf('dealing_marchant')];
			$wo_dtls_arr[$row[csf('job_no')]]['job_no']=$row[csf('job_no')];
			$wo_dtls_arr[$row[csf('job_no')]]['po_number']=$row[csf('po_number')];
			$wo_dtls_arr[$row[csf('job_no')]]['style_ref_no']=$row[csf('style_ref_no')];
			$wo_dtls_arr[$row[csf('job_no')]]['intr_ref']=$row[csf('in_ref_no')];
			$wo_dtls_arr[$row[csf('job_no')]]['grouping']=$row[csf('grouping')];
			$wo_dtls_arr[$row[csf('job_no')]]['pub_shipment_date']=$row[csf('pub_shipment_date')];
			$wo_dtls_arr[$row[csf('job_no')]]['po_quantity']=$row[csf('po_quantity')];
			$wo_dtls_arr[$row[csf('job_no')]]['plan_cut']=$row[csf('plan_cut')];
			$wo_dtls_arr[$row[csf('job_no')]]['po_received_date']=$row[csf('po_received_date')];
		}
	}

	$po_no_str = implode(',', $po_no_arr);

	$condition->po_id("in($po_no_str)"); 
	$condition->init();

	$yarn= new yarn($condition);
	$yarn_data_array=$yarn->getJobWiseYarnQtyArray();

	if($search_type == 1) {
		if($db_type==0) {
			if ($txt_date_from!="" &&  $txt_date_to!="") {
				$tna_date_con = "and task_finish_date between '".change_date_format($txt_date_from, "yyyy-mm-dd", "-")."' and '".change_date_format($txt_date_to, "yyyy-mm-dd", "-")."'";
			} else {
				$tna_date_con = "and task_finish_date between '".$start_date."' and '".$end_date."'";
			}
		}

		else if($db_type==2) {
			if ($txt_date_from!="" &&  $txt_date_to!="") {
				$tna_date_con = "and task_finish_date between '".change_date_format($txt_date_from, "yyyy-mm-dd", "-",1)."' and '".change_date_format($txt_date_to, "yyyy-mm-dd", "-",1)."'";
			} else {
				$tna_date_con = "and task_finish_date between '".$start_date."' and '".$end_date."'";
			}
		}
	}

	$sql_tna_mst = "select a.template_id, a.job_no, a.po_number_id, a.target_date, a.task_start_date, a.task_finish_date, b.grouping, c.job_no_prefix_num
  					from tna_process_mst a, wo_po_break_down b, wo_po_details_master c
					where (task_number = 212 or task_number = 178 or task_number = 60) and a.is_deleted=0 and a.status_active=1 and a.job_no=b.job_no_mst and a.po_number_id = b.id and c.job_no=a.job_no
						$tna_date_con and po_number_id in ($po_no_str)";

	// echo $sql_tna_mst;

	$result_tna_mst = sql_select($sql_tna_mst);
	
	foreach ($result_tna_mst as $row) {
		if($row[csf('grouping')] != '') {
			$tna_mst_arr[$row[csf('grouping')]][$row[csf('job_no')]]['po_id'] = $row[csf('po_number_id')];
			$tna_mst_arr[$row[csf('grouping')]][$row[csf('job_no')]]['grouping'] = $row[csf('grouping')];
			$tna_mst_arr[$row[csf('grouping')]][$row[csf('job_no')]]['job_no_prefix_num'] = $row[csf('job_no_prefix_num')];
			$tna_mst_arr[$row[csf('grouping')]][$row[csf('job_no')]]['job_no'] = $row[csf('job_no')];
			$tna_mst_arr[$row[csf('grouping')]][$row[csf('job_no')]]['target_date'] = $row[csf('target_date')];
			$tna_mst_arr[$row[csf('grouping')]][$row[csf('job_no')]]['task_start_date'] = $row[csf('task_start_date')];
			$tna_mst_arr[$row[csf('grouping')]][$row[csf('job_no')]]['task_finish_date'] = $row[csf('task_finish_date')];
			//$tna_mst_arr[$row[csf('grouping')]][$row[csf('job_no')]]['po_receive_date'] = $row[csf('po_receive_date')];
		} else {
			$tna_mst_arr['no_grouping'][$row[csf('job_no')]]['po_id'] = $row[csf('po_number_id')];
			$tna_mst_arr['no_grouping'][$row[csf('job_no')]]['grouping'] = 'no_grouping';
			$tna_mst_arr['no_grouping'][$row[csf('job_no')]]['job_no_prefix_num'] = $row[csf('job_no_prefix_num')];
			$tna_mst_arr['no_grouping'][$row[csf('job_no')]]['job_no'] = $row[csf('job_no')];
			$tna_mst_arr['no_grouping'][$row[csf('job_no')]]['target_date'] = $row[csf('target_date')];
			$tna_mst_arr['no_grouping'][$row[csf('job_no')]]['task_start_date'] = $row[csf('task_start_date')];
			$tna_mst_arr['no_grouping'][$row[csf('job_no')]]['task_finish_date'] = $row[csf('task_finish_date')];
			//$tna_mst_arr['no_grouping'][$row[csf('po_number_id')]]['po_receive_date'] = $row[csf('po_receive_date')];
		}
	}

	
	$sql_labdip = "select po_break_down_id, job_no_mst, lapdip_target_approval_date, approval_status_date, approval_status, color_name_id
					from wo_po_lapdip_approval_info
					where status_active=1 and is_deleted=0 and po_break_down_id in ($po_no_str)
					group by po_break_down_id, job_no_mst, lapdip_target_approval_date, approval_status_date, approval_status, color_name_id";

	// echo $sql_labdip;

	$result_labdip = sql_select($sql_labdip);

	foreach($result_labdip as $row) {
		if( $labdip_arr[$row[csf('job_no_mst')]] ) {

			// if there is multiple color, finding the earliest date for Target Approval Date
			if($labdip_arr[$row[csf('job_no_mst')]]['target'] > $row[csf('lapdip_target_approval_date')]) {
				$labdip_arr[$row[csf('job_no_mst')]]['target'] = $row[csf('lapdip_target_approval_date')];
			}

			// if there is multiple color, finding the latest date for Target Approval Date
			if($labdip_arr[$row[csf('job_no_mst')]]['received'] < $row[csf('approval_status_date')]) {
				$labdip_arr[$row[csf('job_no_mst')]]['received'] = $row[csf('approval_status_date')];
			}

		} else {
			$labdip_arr[$row[csf('job_no_mst')]]['po_id'] = $row[csf('po_break_down_id')];
			$labdip_arr[$row[csf('job_no_mst')]]['job_no_mst'] = $row[csf('job_no_mst')];
			$labdip_arr[$row[csf('job_no_mst')]]['target'] = $row[csf('lapdip_target_approval_date')];
			$labdip_arr[$row[csf('job_no_mst')]]['received'] = $row[csf('approval_status_date')];
			$labdip_arr[$row[csf('job_no_mst')]]['approval_status'] = $row[csf('approval_status')];
			$labdip_arr[$row[csf('job_no_mst')]]['color_id'] = $row[csf('color_name_id')];
		}
	}

    $sql_prod_dtls = "select a.po_breakdown_id, a.quantity as grey_qnty, b.receive_date, c.grey_receive_qnty, b.booking_no
  						from order_wise_pro_details a, inv_receive_master b, pro_grey_prod_entry_dtls c
 						where a.status_active=1 and a.is_deleted=0 and a.dtls_id=c.id and c.mst_id=b.id and a.entry_form=2 and b.entry_form=2 and a.po_breakdown_id in ($po_no_str)
                        group by a.po_breakdown_id, a.quantity, b.receive_date, c.grey_receive_qnty, b.booking_no";

	// echo $sql_prod_dtls;

	$result_prod_dtls = sql_select($sql_prod_dtls);
	// print_r($result_prod_dtls);

	foreach($result_prod_dtls as $row) {
		$days = datediff('d', date('Y-m-d', strtotime(change_date_format($row[csf('receive_date')]))), date('Y-m-d'));
		
		if(isset($prod_dtls_arr[$row[csf('po_breakdown_id')]])) {
			//echo "10**po_breakdown_id found";
			if($prod_dtls_arr[$row[csf('po_breakdown_id')]]['po_id'] == $row[csf('po_breakdown_id')]) {
				$prod_dtls_arr[$row[csf('po_breakdown_id')]]['total_knitting'] += $row[csf('grey_receive_qnty')];
			}
		} else {
			$prod_dtls_arr[$row[csf('po_breakdown_id')]]['po_id'] = $row[csf('po_breakdown_id')];
			$prod_dtls_arr[$row[csf('po_breakdown_id')]]['grey_qnty'] = $row[csf('grey_qnty')];
			$prod_dtls_arr[$row[csf('po_breakdown_id')]]['date'] = $row[csf('receive_date')];
			$prod_dtls_arr[$row[csf('po_breakdown_id')]]['today_knitting'] = 0;
			$prod_dtls_arr[$row[csf('po_breakdown_id')]]['total_knitting'] = $row[csf('grey_receive_qnty')];
		}		

		if($days == 1) {
			$prod_dtls_arr[$row[csf('po_breakdown_id')]]['today_knitting'] += $row[csf('grey_qnty')];
		}
	}
	 
	$fab_cons_sql = "select pre_cost_fabric_cost_dtls_id, po_break_down_id, job_no, requirment, pcs
					from wo_pre_cos_fab_co_avg_con_dtls
					where po_break_down_id in($po_no_str)
					group by pre_cost_fabric_cost_dtls_id, po_break_down_id, job_no, requirment, pcs";
	$result_fab_cons = sql_select($fab_cons_sql);

	// echo $fab_cons_sql;

	foreach($result_fab_cons as $row) {
		if(isset($fab_cons_arr[$row[csf('po_break_down_id')]])) {
			$fab_cons_arr[$row[csf('po_break_down_id')]]['requirment'] .= ','.$row[csf('requirment')];
			$fab_cons_arr[$row[csf('po_break_down_id')]]['pcs'] .= ','.$row[csf('pcs')];
		} else {
			$fab_cons_arr[$row[csf('po_break_down_id')]]['po_id'] = $row[csf('po_break_down_id')];
			$fab_cons_arr[$row[csf('po_break_down_id')]]['job_no_mst'] = $row[csf('job_no')];
			$fab_cons_arr[$row[csf('po_break_down_id')]]['requirment'] = $row[csf('requirment')];
			$fab_cons_arr[$row[csf('po_break_down_id')]]['pcs'] = $row[csf('pcs')];
		}
	}

	if( !count($tna_mst_arr) ) {	// if result is 0
		ob_start();	// start output buffer to show the html part
?>
	<div class="heading-area" style="text-align: center;">
		<h3>No Knitting Plan Found</h3>
	</div>
<?php
		ob_end_flush();	// exit and flush output buffer
		exit();
	}
	else {	// if result is more then 0
		ob_start();	// start output buffer to show the html part
?>
<div class="btn-container" style="text-align: center; margin-bottom: 15px;">
	<a href="#" id="btnExcel" download><input type="button" value="Excel Preview" name="excel" class="formbutton" style="width:100px;"/></a>
	<a href="#" id="btnPrint" onclick="printPreview()"><input type="button" value="HTML Preview" name="print" class="formbutton" style="width:100px"/></a>
</div>
<div id="rptTableArea">
	<div class="heading-area" style="text-align: center;">
		<h3>Knitting Plan and Position Report as per Style</h3>
		<h4>Company: <?php echo $company_name; ?></h4>
	</div>
<table width="100%" cellspacing="0" border="1" class="rpt_table" rules="all" id="rpt_table">
    <thead>
        <tr style="vertical-align:middle; word-break:break-word">
            <th rowspan="2" width="1%" align="center">SL</th>
            <th rowspan="2" width="5%" align="center">Merchandiser</th>
            <th rowspan="2" width="4%" align="center">Job</th>
            <th rowspan="2" width="6%" align="center">PO</th>
            <th rowspan="2" width="5%" align="center">Style Ref.</th>
            <th rowspan="2" width="4%" align="center">Internal Ref.</th>
            <th rowspan="2" width="6%" align="center">Shipment</th>
            <th rowspan="2" width="5%" align="center">GMT Qty</th>
            <th rowspan="2" width="5%" align="center">Yarn Required Qty(KG)</th>
            <th colspan="2" width="6%" align="center">Approve</th>
            <th colspan="2" width="6%" align="center">Target</th>
            <th rowspan="2" width="4%" align="center">Required Days</th>
            <th rowspan="2" width="4%" align="center">Days Passed<p style="font-size: .8em; margin-top: 3px;">(From PO)</p></th>
            <th rowspan="2" width="4%" align="center">Previous Knitting</th>
            <th rowspan="2" width="4%" align="center">Plan to Knitting</th>
            <th rowspan="2" width="4%" align="center">Today Knitting</th>
            <th rowspan="2" width="4%" align="center">Total Knitting</th>
            <th rowspan="2" width="4%" align="center">Knitting Balance</th>
        </tr>
        <tr>
            <th width="5%" align="center">Target</th>
            <th width="5%" align="center">Rcvd</th>
            <th width="5%" align="center">Start</th>
            <th width="5%" align="center">End</th>
        </tr>
    </thead>
    <tbody id="report-container" style="vertical-align:middle; word-break:break-word">
<?php
	foreach($tna_mst_arr as $groupings) {
		$total_gmt=0;
		$total_yarn_rqrd=0;
		$total_prev_knit=0;
		$total_plan_knit=0;
		$total_today_knit=0;
		$total_knit_grouping=0;
		$total_knit_balance=0;
		
		foreach($groupings as $tna) {
			$bgcolor = '';
			$strt_date = $tna['task_start_date'];
	        $end_date = $tna['task_finish_date'];
	        $today = date('Y-m-d');
	        $tmpReqrd = 0;
	        $tmpPcs = 0;
	        $reqrd_qty = $yarn_data_array[$tna['job_no']];

	        $strt_date = date('Y-m-d',strtotime(change_date_format($strt_date)));
	        $end_date = date('Y-m-d',strtotime(change_date_format($end_date)));

	        $target_date = ($labdip_arr[$tna['job_no']]['target']) ? $labdip_arr[$tna['job_no']]['target'] : '-';
        	$received_date = ($labdip_arr[$tna['job_no']]['received']) ? $labdip_arr[$tna['job_no']]['received'] : '-';

        	$tempFabArr = explode(',', $fab_cons_arr[$tna['po_id']]['requirment']);
        	$tempPcsArr = explode(',', $fab_cons_arr[$tna['po_id']]['pcs']);        
	        
	        // if start and end date set then find the datedifference otherwise blank
			$rqrd_days = datediff('d', $strt_date, $end_date);
			$days_passed = datediff('d', $wo_dtls_arr[$tna['job_no']]['po_received_date'], date('Y-m-d')) - 1;

			$total_knitting = ($prod_dtls_arr[$tna['po_id']]['total_knitting'] != '') ? $prod_dtls_arr[$tna['po_id']]['total_knitting'] : 0;
			$today_knitting = ($prod_dtls_arr[$tna['po_id']]['today_knitting'] != '') ? $prod_dtls_arr[$tna['po_id']]['today_knitting'] : 0;
			$knit_balance = $reqrd_qty - $total_knitting;

			$days = datediff('d', $today, $end_date);

			if($today > $end_date) {
	        	// tna knitting date over
	        	$plan_to_knit = $knit_balance;
	        } else if($today < $strt_date) {
	        	// before tna knitting start date
	        	$plan_to_knit = $knit_balance/datediff('d', $today, $strt_date);
	        } else {
	        	// between tna knitting date
	        	$plan_to_knit = $knit_balance/$days;
	        }

			if($sl%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

			$total_gmt += $wo_dtls_arr[$tna['job_no']]['plan_cut'];
			$total_yarn_rqrd += $reqrd_qty;
			$total_prev_knit += ($total_knitting - $today_knitting);
			$total_plan_knit += $plan_to_knit;
			$total_today_knit += $today_knitting;
			$total_knit_grouping += $total_knitting;
			$total_knit_balance += $knit_balance;
?>
			<tr bgcolor="<?php echo $bgcolor; ?>">
				<td><?php echo $sl; ?></td>
				<td><?php echo $merchandiser_arr[$wo_dtls_arr[$tna['job_no']]['merchandiser']]; ?></td>
				<td><?php echo $tna['job_no_prefix_num']; ?></td>
				<td><?php echo $wo_dtls_arr[$tna['job_no']]['po_number']; ?></td>
				<td><?php echo $wo_dtls_arr[$tna['job_no']]['style_ref_no']; ?></td>
				<td><?php echo $wo_dtls_arr[$tna['job_no']]['grouping']; ?></td>
				<td><?php echo $wo_dtls_arr[$tna['job_no']]['pub_shipment_date']; ?></td>
				<td><?php echo number_format($wo_dtls_arr[$tna['job_no']]['plan_cut'], 2); ?></td>
				<td><?php echo number_format($reqrd_qty, 2); ?></td>
				<td><?php echo $target_date; ?></td>
				<td><?php echo $received_date; ?></td>
				<td><?php echo $tna['task_start_date']; ?></td>
				<td><?php echo $tna['task_finish_date']; ?></td>
				<td><?php echo $rqrd_days; ?></td>
				<td><?php echo $days_passed; ?></td>
				<td><?php echo number_format(($total_knitting - $today_knitting), 2); ?></td>
				<td><?php echo number_format($plan_to_knit, 2); ?></td>
				<td><?php echo number_format($today_knitting, 2); ?></td>
				<td><?php echo number_format($total_knitting, 2); ?></td>
				<td><?php echo number_format($knit_balance, 2); ?></td>
			</tr>
			
<?php
				$sl++;
		}
?>
			<tr bgcolor="#8AABD7">
				<td colspan="7" style="text-align: right;"><b>Total:</b></td>
				<td><b><?php echo number_format($total_gmt, 2); ?></b></td>
				<td><b><?php echo number_format($total_yarn_rqrd, 2); ?></b></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td><b><?php echo number_format($total_prev_knit, 2); ?></b></td>
				<td><b><?php echo number_format($total_plan_knit, 2); ?></b></td>
				<td><b><?php echo number_format($total_today_knit, 2); ?></b></td>
				<td><b><?php echo number_format($total_knit_grouping, 2); ?></b></td>
				<td><b><?php echo number_format($total_knit_balance, 2); ?></b></td>
			</tr>
<?php
	}
?>
	</tbody>
</table>
</div>

<?php
	$user_id = $_SESSION['logic_erp']['user_id'];

	// first delete all the previous .xls
	foreach (glob($user_id."_*.xls") as $filename) {       
        @unlink($filename);
    }

	$fileName=$user_id.'_'.time().'.xls';
    $create_new_excel = fopen($fileName, 'w');
    $is_created = fwrite($create_new_excel, ob_get_contents());

    echo '####requires/'.$fileName;
?>

<?php
	ob_end_flush();	// exit and flush output buffer
	exit();
	}
}

?>