<?
include('../../../../includes/common.php');
session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');

$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "","" );     	 
	exit();
}
if($action=="load_drop_down_team_leader")
{
	echo create_drop_down( "cbo_team_leader", 120, "select id,team_leader_name from lib_marketing_team  where id in('$data') and status_active=1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-Team Leader-", $selected, "" );
	exit();
}
if ($action=="load_drop_down_dealing_merchant")
{
	echo create_drop_down( "cbo_dealing_merchant", 120, "select id,team_member_name from lib_mkt_team_member_info where team_id in ('$data') and status_active =1 and is_deleted=0 and data_level_security=1 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
	exit();	
}
if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');

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
    <div align="center" >
        <form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:580px;">
            <table width="470" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
					<th> Company Name</th>
					<th> Year</th>
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

					<td>
                        <?
	                       echo create_drop_down( "cbo_working_company_name", 142, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select  --", $selected, "" );
	                     ?>
                        </td>

						<td align="center"><? echo create_drop_down( "cbo_year_selection", 100, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>

                        <td align="center">
                        	 <?
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>
                        <td align="center">
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Order No");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>
                        <td align="center" id="search_by_td">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
                        </td>
                        <td align="center">
							<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view(document.getElementById('cbo_working_company_name').value + '**' + document.getElementById('cbo_buyer_name').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_common').value + '**' + '<?php echo $sytle_ref_no; ?>' + '**' + document.getElementById('cbo_year_selection').value, 'create_job_no_search_list_view', 'search_div', 'shipment_schedule_cpd_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                        </td>
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

if($action=="create_job_no_search_list_view")
{
	// echo $data;
	$data=explode('**',$data);
	// echo "<pre>";
	// print_r($data);die;
	// echo "</pre>";
    $company_id=$data[0];
	$year_id=$data[5];
	$buyer_arr	= return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr = return_library_array( "select id, company_name from lib_company",'id','company_name');

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

	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";
	if($search_by==2) $search_field="b.po_number"; else $search_field="a.job_no";
	$year="year(a.insert_date)";
	if($db_type==0) $year_field="YEAR(a.insert_date) as year";
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";

	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(a.insert_date)=$year_id"; else $year_cond="";
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(a.insert_date,'YYYY')";
		if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";
	}

	//if($month_id!=0) $month_cond=" and month(insert_date)=$month_id"; else $month_cond="";
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	// $sql= "SELECT b.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, $year_field from wo_po_details_master a,wo_po_break_down b where a.id=b.job_id and a.status_active=1 and a.is_deleted=0 and company_name=$company_id and  $search_field  like '$search_string' $buyer_id_cond $year_cond order by a.job_no";

	$sql= "SELECT a.id, a.job_no,  a.company_name, a.buyer_name, a.style_ref_no,b.po_number, $year_field from wo_po_details_master a join wo_po_break_down b on a.id=b.job_id where  a.status_active=1 and a.is_deleted=0 and company_name=$company_id and  $search_field  like '$search_string' $buyer_id_cond $year_cond order by a.job_no";
    // echo $sql;die();
	?>
	<div style="text-align:center;" class="search_type"><? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --",4,"","","1,2,3,4" ); ?></div>
	<?

	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Order No", "120,130,80,60","600","240",0, $sql , "js_set_value", "id,job_no", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no,year,po_number", "",'','','') ;
	exit();
} // Job Search end



if($action=="report_generate")
{
	$company_name=str_replace("'","",$cbo_company_name);
	$working_company=str_replace("'","",$cbo_working_company);
	$buyer_name=str_replace("'","",$cbo_buyer_name);
	$team_name=str_replace("'","",$cbo_team_name);
	$cbo_team_leader=str_replace("'","",$cbo_team_leader);
	$cbo_dealing_merchant=str_replace("'","",$cbo_dealing_merchant);
	$product_dept_id=str_replace("'","",$cbo_dept);
	$gmts_item=str_replace("'","",$cbo_item);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_job_no_id=str_replace("'","",$txt_job_no_id);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	if($cbo_team_leader>0) $team_leader_cond="and a.team_leader in ($cbo_team_leader)"; else $team_leader_cond="";
	if($team_name>0) $team_name_cond="and a.team_leader in ($team_name)"; else $team_name_cond="";
	if($cbo_dealing_merchant>0) $dealing_merchant_cond="and a.dealing_marchant in ($cbo_dealing_merchant)"; else $dealing_merchant_cond="";
	if(trim($txt_style_ref)!="") $style_ref_cond="and a.style_ref_no='$txt_style_ref'"; else $style_ref_cond="";
	if(trim($product_dept_id)>0) $product_dept_cond="and a.product_dept in ($product_dept_id)"; else $product_dept_cond="";

	if(trim($txt_job_no_id)!="") $cnd_job_id="and a.id='$txt_job_no_id'"; else $cnd_job_id="";

	
	if($buyer_name==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
				$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
				$buyer_id_cond2=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else {
				$buyer_id_cond=""; $buyer_id_cond2="";
			}
		}
		else
		{
			$buyer_id_cond="";$buyer_id_cond2="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name in ($buyer_name) ";
		$buyer_id_cond2=" and a.buyer_id in ($buyer_name)";
	}

	if(trim($date_from)!="") $start_date=$date_from;
	if(trim($date_to)!="") $end_date=$date_to;

	if($db_type==0)
	{
		$start_date=change_date_format($date_from,'yyyy-mm-dd','-');
		$end_date=change_date_format($date_to,'yyyy-mm-dd','-');
	}
	else if($db_type==2)
	{
		$start_date=change_date_format($date_from,'yyyy-mm-dd','-',1);
		$end_date=change_date_format($date_to,'yyyy-mm-dd','-',1);
	}

	if ($start_date!="" && $end_date!="")
	{
		if($db_type==0) $date_cond=" and b.shipment_date between '$start_date' and '$end_date' ";
		else if($db_type==2) $date_cond=" and b.shipment_date between '$start_date' and '$end_date' ";
	}
	else $date_cond="";

	$user_name_arr=return_library_array( "select id, user_name from user_passwd",'id','user_name');	
	$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$color_name_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$bank_name_arr=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');
	$company_short_name_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
	$company_name_arr=return_library_array( "select id,company_name from lib_company",'id','company_name');
	$buyer_wise_season_arr=return_library_array( "select id, season_name from lib_buyer_season where status_active =1 and is_deleted=0",'id','season_name');
	$team_leader_name_arr=return_library_array( "select id,team_leader_name from lib_marketing_team",'id','team_leader_name');
	$team_name_arr=return_library_array( "select id,team_name from lib_marketing_team",'id','team_name');
	$bodypart_name_arr=return_library_array( "select id,body_part_full_name from lib_body_part",'id','body_part_full_name');
	$dealing_marchant_arr=return_library_array( "select id,team_member_name from  lib_mkt_team_member_info",'id','team_member_name');
	$factory_merchant_arr=return_library_array("select a.id, a.team_member_name from lib_mkt_team_member_info a, lib_marketing_team b where a.team_id=b.id  and a.status_active =1 and a.is_deleted=0 order by a.team_member_name",'id','team_member_name');
	$imge_arr=return_library_array( "select master_tble_id, image_location from common_photo_library where file_type=1",'master_tble_id','image_location');
	$supplier_arr=return_library_array( "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id  and a.status_active =1 and a.is_deleted=0 order by supplier_name",'id','supplier_name');
	$lib_item_group_arr=return_library_array( "select item_name,id from lib_item_group where item_category=4 order by item_name", "id", "item_name");

	$buycost_arr=return_library_array( "select job_id,job_id from qc_confirm_mst",'job_id','job_id');
	 $bank_name_arr=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');




		ob_start();

		$partial_report_data=sql_select( "select format_id,template_name from  lib_report_template where module_id=2 and report_id=35 and  template_name in($company_name)");
		$trims_report_data=sql_select( "select format_id,template_name from  lib_report_template  where  module_id=2 and report_id=26 and template_name in($company_name)");
		
		
		// print_r($partial_report_id_arr);//269 28
		
			foreach($partial_report_data as $val){
				$partial_report_id_arr[$val[csf('template_name')]]=$val[csf('format_id')];
			}
			foreach($trims_report_data as $vals){
				$trims_report_id_arr[$vals[csf('template_name')]]=$vals[csf('format_id')];
			}

			// echo "<pre>";
			// print_r($partial_reportId);



		// $trims_reportId=explode(",",$trims_report_id_arr);
			// echo "<pre>";
			// print_r($partial_reportId);
	
		$main_sql=sql_select("SELECT a.id as job_id, b.id as po_id, a.company_name, a.job_no, a.job_no_prefix_num, a.style_ref_no, a.buyer_name, a.job_quantity, a.team_leader, a.dealing_marchant, a.working_company_id, a.order_uom, a.product_dept, a.season_buyer_wise, a.gmts_item_id, a.season_year, b.po_number, b.pub_shipment_date, b.shipment_date, b.po_quantity,b.is_confirmed,b.insert_date,a.quotation_id  from wo_po_details_master a join wo_po_break_down b on a.id=b.job_id where a.company_name in ($company_name) and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 $product_dept_cond $date_cond  $job_cond $style_ref_cond $team_leader_cond $dealing_merchant_cond $buyer_id_cond $team_name_cond $cnd_job_id order by a.id ASC");
		$main_data_arr=array();
		$attributes=array('job_id', 'po_id', 'company_name', 'working_company_id','buyer_name', 'season_buyer_wise', 'job_no','team_leader','dealing_marchant', 'style_ref_no', 'po_number', 'order_uom', 'po_quantity', 'shipment_date', 'gmts_item_id', 'season_year', 'is_confirmed', 'insert_date','a.quotation_id');
		foreach ($main_sql as $row) {
			foreach ($attributes as $attr) {
				$main_data_arr[$row[csf('job_id')]][$row[csf('po_id')]][$attr]=$row[csf($attr)];
			}
			$main_data_arr[$row[csf('job_id')]][$row[csf('po_id')]]['product_dept_id']=$row[csf('product_dept')];
			$order_id_array[$row[csf('po_id')]]=$row[csf('po_id')];
			$job_id_arr[$row[csf('job_id')]]=$row[csf('job_id')];
			$job_arr[$row[csf('job_no')]]=$row[csf('job_no')];
		}




		//==========================================================================================
		$precost_sql="select ready_to_approved,job_no,costing_date,costing_per from wo_pre_cost_mst where status_active = 1 and  is_deleted = 0 ".where_con_using_array($job_arr,1,'job_no')." ";				
						
		$precost_data=sql_select($precost_sql);
		foreach($precost_data as $pcval){
			$precost_arr[$pcval[csf('job_no')]]['ready_to_app']=$pcval[csf('ready_to_approved')];
			$precost_arr[$pcval[csf('job_no')]]['costing_date']=$pcval[csf('costing_date')];
			$precost_arr[$pcval[csf('job_no')]]['costing_per']=$pcval[csf('costing_per')];
		}


		$set_item_sql=sql_select("SELECT gmts_item_id , set_item_ratio, job_id from wo_po_details_mas_set_details where 0=0 ".where_con_using_array($job_id_arr,0,'job_id')."");
		foreach ($set_item_sql as $row) {
			$set_item_arr[$row[csf('job_id')]][$row[csf('gmts_item_id')]]=$row[csf('set_item_ratio')];
		}
	
	/*echo '<pre>';
	print_r($main_data_arr); die;*/
	$wo_data_sql=sql_select("SELECT a.id as fabric_id, a.body_part_id, a.composition, a.construction,  a.lib_yarn_count_deter_id, a.job_id, b.id as booking_id, b.booking_no, c.fin_fab_qnty, c.amount, c.fabric_color_id, c.po_break_down_id, a.uom, c.gsm_weight, c.dia_width ,a.avg_process_loss,b.item_category,b.fabric_source,b.is_approved  FROM wo_pre_cost_fabric_cost_dtls a join wo_booking_dtls c on a.id=c.pre_cost_fabric_cost_dtls_id join wo_booking_mst b on b.booking_no=c.booking_no where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ".where_con_using_array($order_id_array,0,'c.po_break_down_id')." and c.fin_fab_qnty is not null");

	
	
	foreach ($wo_data_sql as $row) {
		//yarn_count_id*gsm*dia*color*booking_no
		$fabric_description=$bodypart_name_arr[$row[csf('body_part_id')]].','.$row[csf('composition')].','.$row[csf('construction')].','.$row[csf('gsm_weight')].','.$row[csf('dia_width')];
		$key=$row[csf('lib_yarn_count_deter_id')].'*'.$row[csf('gsm_weight')].'*'.$row[csf('dia_width')].'*'.$row[csf('fabric_color_id')].'*'.$row[csf('booking_id')];
		$main_data_arr[$row[csf('job_id')]][$row[csf('po_break_down_id')]]['booking_data'][$key]['fabric_color_id'] = $row[csf('fabric_color_id')];
		$main_data_arr[$row[csf('job_id')]][$row[csf('po_break_down_id')]]['booking_data'][$key]['fabric_description'] = $fabric_description;
		$main_data_arr[$row[csf('job_id')]][$row[csf('po_break_down_id')]]['booking_data'][$key]['booking_no'] = $row[csf('booking_no')];
		$main_data_arr[$row[csf('job_id')]][$row[csf('po_break_down_id')]]['booking_data'][$key]['uom'] = $row[csf('uom')];
		$main_data_arr[$row[csf('job_id')]][$row[csf('po_break_down_id')]]['booking_data'][$key]['item_category'] = $row[csf('item_category')];
		$main_data_arr[$row[csf('job_id')]][$row[csf('po_break_down_id')]]['booking_data'][$key]['fabric_source'] = $row[csf('fabric_source')];
		$main_data_arr[$row[csf('job_id')]][$row[csf('po_break_down_id')]]['booking_data'][$key]['is_approved'] = $row[csf('is_approved')];
		$main_data_arr[$row[csf('job_id')]][$row[csf('po_break_down_id')]]['booking_data'][$key]['fin_fab_qnty'] = $row[csf('fin_fab_qnty')]-( $row[csf('fin_fab_qnty')]*$row[csf('avg_process_loss')])/110;
		$main_data_arr[$row[csf('job_id')]][$row[csf('po_break_down_id')]]['booking_data'][$key]['amount'] = $row[csf('amount')];
	}

	$receive_qty_data=sql_select("SELECT f.booking_id, d.detarmination_id, f.gsm, f.width, d.color, c.quantity as order_qnty, b.order_amount, e.job_id, e.id as po_id, b.order_rate from inv_receive_master a join inv_transaction b on a.id = b.mst_id join order_wise_pro_details c on b.id = c.trans_id join product_details_master d on d.id=c.prod_id join wo_po_break_down e on e.id=c.po_breakdown_id join pro_finish_fabric_rcv_dtls f on a.id=f.mst_id where a.entry_form=37 and a.receive_basis=10 and a.status_active=1 and a.is_deleted=0 and b.receive_basis=10 and b.transaction_type=1 and b.status_active=1 and b.is_deleted=0 and c.trans_type=1 and c.entry_form=37 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 ".where_con_using_array($order_id_array,0,'e.id')."");
	$receive_qty_arr=array();
	foreach ($receive_qty_data as $row) {
		//yarn_count_id*gsm*dia*color*booking_no
		$key=$row[csf('detarmination_id')].'*'.$row[csf('gsm')].'*'.$row[csf('width')].'*'.$row[csf('color')].'*'.$row[csf('booking_id')];
		$main_data_arr[$row[csf('job_id')]][$row[csf('po_id')]]['booking_data'][$key]['rcv_data'] = $row[csf('order_qnty')];
	}

	$trim_wo_data_sql=sql_select("SELECT a.id as trim_id, a.job_id, b.id as booking_id, c.wo_qnty, c.po_break_down_id, a.trim_group, a.cons_uom, b.supplier_id, b.booking_no,b.item_category,b.fabric_source,b.is_approved,b.pay_mode  FROM wo_pre_cost_trim_cost_dtls a join wo_booking_dtls c on a.id=c.pre_cost_fabric_cost_dtls_id join wo_booking_mst b on b.booking_no=c.booking_no join wo_pre_cost_trim_co_cons_dtls d on a.id=d.wo_pre_cost_trim_cost_dtls_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.booking_type=2 and c.is_short=2 ".where_con_using_array($order_id_array,0,'c.po_break_down_id')." group by a.id , a.job_id, b.id , c.wo_qnty, c.po_break_down_id, a.trim_group, a.cons_uom, b.supplier_id, b.booking_no,b.item_category,b.fabric_source,b.is_approved,b.pay_mode ");




	foreach ($trim_wo_data_sql as $row) {
		$key=$row[csf('trim_group')].'*'.$row[csf('booking_id')];

	
		if($row[csf('pay_mode')]==1 || $row[csf('pay_mode')]==2){
		$trim_description=$lib_item_group_arr[$row[csf('trim_group')]].'-Supplier: '.$supplier_arr[$row[csf('supplier_id')]];
		}else{
			$trim_description=$lib_item_group_arr[$row[csf('trim_group')]].'-Supplier: '.$company_name_arr[$row[csf('supplier_id')]];
		}

		$main_data_arr[$row[csf('job_id')]][$row[csf('po_break_down_id')]]['trims_booking_data'][$key]['wo_qnty'] += $row[csf('wo_qnty')];
		$main_data_arr[$row[csf('job_id')]][$row[csf('po_break_down_id')]]['trims_booking_data'][$key]['description'] = $trim_description;
		$main_data_arr[$row[csf('job_id')]][$row[csf('po_break_down_id')]]['trims_booking_data'][$key]['uom'] = $row[csf('cons_uom')];
		$main_data_arr[$row[csf('job_id')]][$row[csf('po_break_down_id')]]['trims_booking_data'][$key]['item_category'] = $row[csf('item_category')];
		$main_data_arr[$row[csf('job_id')]][$row[csf('po_break_down_id')]]['trims_booking_data'][$key]['fabric_source'] = $row[csf('fabric_source')];
		$main_data_arr[$row[csf('job_id')]][$row[csf('po_break_down_id')]]['trims_booking_data'][$key]['is_approved'] = $row[csf('is_approved')];
		$main_data_arr[$row[csf('job_id')]][$row[csf('po_break_down_id')]]['trims_booking_data'][$key]['booking_no'] = $row[csf('booking_no')];
		$main_data_arr[$row[csf('job_id')]][$row[csf('po_break_down_id')]]['trims_booking_data'][$key]['booking_id'] = $row[csf('booking_id')];
		$main_data_arr[$row[csf('job_id')]][$row[csf('po_break_down_id')]]['trims_booking_data'][$key]['trim_group'] = $row[csf('trim_group')];
	}
	$trims_receive_qty_data=sql_select("SELECT b.po_breakdown_id, a.item_group_id, b.quantity as cons_qnty, a.rate, e.job_id, a.booking_id  from inv_receive_master c,product_details_master d,inv_trims_entry_dtls a , order_wise_pro_details b, wo_po_break_down e where a.mst_id=c.id and a.trans_id=b.trans_id and a.id=b.dtls_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and e.id=b.po_breakdown_id ".where_con_using_array($order_id_array,0,'b.po_breakdown_id')."");



	foreach ($trims_receive_qty_data as $row) {
		$key=$row[csf('item_group_id')].'*'.$row[csf('booking_id')];
		$main_data_arr[$row[csf('job_id')]][$row[csf('po_breakdown_id')]]['trims_booking_data'][$key]['rcv_qty'] += $row[csf('cons_qnty')];
		$main_data_arr[$row[csf('job_id')]][$row[csf('po_breakdown_id')]]['trims_booking_data'][$key]['item_group_id'] = $row[csf('item_group_id')];
	}
	$sample_info=sql_select("SELECT a.po_break_down_id as po_id, c.sample_type, a.approval_status, b.job_id from wo_po_sample_approval_info a join wo_po_break_down b on b.id=a.po_break_down_id join lib_sample c on a.sample_type_id=c.id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.approval_status=3 and  c.sample_type in (2,4) ".where_con_using_array($order_id_array,0,'a.po_break_down_id')."");

	

	foreach ($sample_info as $row) {
		$main_data_arr[$row[csf('job_id')]][$row[csf('po_id')]]['sample_data'][$row[csf('sample_type')]]=$row[csf('approval_status')];
	}


		$sample_app_data=sql_select("SELECT id, po_break_down_id as po_id, sample_type_id, approval_status from wo_po_sample_approval_info where  sample_type_id in (99,18) and approval_status >0 and is_deleted=0 and status_active=1 ".where_con_using_array($order_id_array,0,'po_break_down_id')."");

		foreach($sample_app_data as $row){
			$sample_app_status[$row[csf('po_id')]][$row[csf('sample_type_id')]]['approval_status']=$row[csf('approval_status')];
		}



	// echo '<pre>';
	// print_r($sample_app_status); 

	ob_start();	
	 ?>
		<div align="center">
			<div align="center">
			<style>
				tr.noBorder td {
					border: 0;
					font-weight: bold;
					font-size: 20px;
				}
			</style>
		<!-- <h3 style="width:100%;" align="left" id="accordion_h4" class="accordion_h" onClick="accordion_menu( this.id,'content_report_panel', '')"> -Report Panel</h3> -->

		<div id="content_report_panel" style="margin-top: 20px"> 
		<table  width="2460" >
			<thead>
				<tr class="noBorder">
					<td >  <div style="text-align:center;" class="search_type"><? echo create_drop_down( "cbo_string_search_type", 150,  $string_search_type,'', 1, "-- Searching Type --",4,"","","2,3,4" ); ?></div>
					</td>				 
				</tr>
			     <tr class="noBorder">
							<td colspan="30" align="center" >SHIPMENT SCHEDULE STATUS</td>
				</tr>
				 <tr class="noBorder">
							<td colspan="30" align="center" >FROM SHIPMENT DATE RANGE: <?= change_date_format($start_date,'',''); ?> TO <?= change_date_format($end_date,'','');  ?></td>
				</tr>
			</thead>
		</table>   
				<table width="2460"  border="1" class="rpt_table" rules="all" align="left">
				
					<thead>
						<tr>
							<th width="20">SL</th>
							<th width="80">Company</th>
							<th width="80">Buyer</th>
							<th width="80">Season</th>
							<th width="80">Job NO.</th>
							<th width="80">Team Name</th>
							<th width="80">TL Name</th>
							<th width="80">Deling Mer</th>
							<th width="100">Image</th>
							<th width="80">Style</th>
							<th width="80">PO#</th>
							<th width="80">PO Status</th>
							<th width="80">Qty in Pack</th>
							<th width="80">Qty in Pcs</th>
							<th width="80">Qty in Dzn</th>
							<th width="80">PO Insert Date</th>
							<th width="80" title="Shipment Date">SBD date</th>
							<th width="80">Prod. Dept</th>
							<th width="80">Item</th>
							<th width="80">Material</th>
							<th width="80">Fab. Color</th>
							<th width="80">Fabric & Trims</th>					
							<th width="80">Booking</th>
							<th width="80">Required Qty.</th>
							<th width="80">Fabric & Material In Hand Status</th>
							<th width="80">UOM</th>			                       
							<th width="80">Size Set</th>
							<th width="80">P.P</th>
							<th width="80">File Submit Status</th>
							<th width="80">Remarks</th>	
												
						</tr>
					</thead>
				</table>
				<div style="max-height:400px; overflow-y:scroll; float:left; width:2540px;"  id="scroll_body">
					<table width="2460" border="1" class="rpt_table" rules="all" id="table_body"  align="left">
						<tbody>
							<?php
							$i=1;
							foreach($main_data_arr as $job_id=>$po_data_arr){
								foreach($po_data_arr as $po_id=>$row){
										$fabric_row=count($row['booking_data']);
										$trims_row=count($row['trims_booking_data']);	
										if($fabric_row==0 && $trims_row==0){
											$total_row=2;
										}
										elseif ($fabric_row>0 && $trims_row==0) {
											$total_row=$fabric_row+1;
										}
										elseif ($fabric_row==0 && $trims_row>0) {
											$total_row=$trims_row+1;
										}
										else{
											$total_row=$fabric_row+$trims_row;
										}						
										$job_item_gmts=explode(",", $row['gmts_item_id']);
										$item_ratio=0;
										$gmts_item_str=array();
										foreach ($job_item_gmts as $gmts_item_id) {
											$item_ratio+=$set_item_arr[$job_id][$gmts_item_id];
											$gmts_item_str[$gmts_item_id]=$garments_item[$gmts_item_id];
										}
										if($row['order_uom']==57 || $row['order_uom']==58){
											$pack_qty=$row['po_quantity'];					
											$pcs_qty=$row['po_quantity']*$item_ratio;				
											
											$dzn_qty=number_format($pcs_qty/12,4,".","");		
										}
										else{
											$pack_qty=$row['po_quantity'];					
											$pcs_qty=$row['po_quantity'];				
											$dzn_qty=number_format($pcs_qty/12,4,".","");
										}
										$job_no=$row['job_no'];
										$style_ref=$row['style_ref_no'];
					
										$variable="'".$row['company_name'].'_'.$row['buyer_name'].'_'.$style_ref.'_'.$job_no.'_'.$row['job_id'].'_'.$row['quotation_id'].'_'.$precost_arr[$row['job_no']]['costing_date'].'_'.$po_id.'_'.$precost_arr[$row['job_no']]['costing_per']."'";




										$k=1; $j=1;
										if($total_row>0) $trowspan="rowspan=".$total_row.""; else $trowspan="";
										if($fabric_row>0) $frowspan="rowspan=".$fabric_row.""; else $frowspan="";
										if($trims_row>0) $trrowspan="rowspan=".$trims_row.""; else $trrowspan="";
										?>
										<tr bgcolor="<? echo $bgcolor;?>" style="vertical-align:middle" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
											<? if($k==1){ ?>
											<td  width="20" <?= $trowspan; ?> align="center" valign="top"><? echo $i; ?></td>
											<td  width="80"style="word-break: break-all;" <?= $trowspan; ?> valign="top"><? echo $company_name_arr[$row['company_name']];?></td>
											<td  width="80" style="word-break: break-all;" <?= $trowspan; ?> valign="top"><? echo $buyer_short_name_arr[$row['buyer_name']];?></td>
											<td  width="80" <?= $trowspan; ?> valign="top"><? echo $buyer_wise_season_arr[$row['season_buyer_wise']].'-'.$row['season_year'];?></td>
											<td  width="80" <?= $trowspan; ?> valign="top"><a href="#" onClick="precost_bom_pop('materialSheet',<?=$variable;?>);"><?=$row['job_no']?></a></td>
											<td  width="80" <?= $trowspan; ?> valign="top"><? echo $team_name_arr[$row['team_leader']];?> </td>
											<td  width="80" <?= $trowspan; ?> valign="top"><? echo $team_leader_name_arr[$row['team_leader']];?> </td>
											<td  width="80" <?= $trowspan; ?> valign="top"><? echo $dealing_marchant_arr[$row['dealing_marchant']];?></td>
											<td  width="100" <?= $trowspan; ?>  valign="top">
											<? if($imge_arr[$row['job_no']]!=''){ ?><img src='<? echo "../../../".$imge_arr[$row['job_no']]; ?>' height='100' width="100" /><? } ?>
											</td>
											<td  width="80"style="word-break: break-all;" <?= $trowspan; ?> valign="top"><? echo $row['style_ref_no'];?></td>
											<td  width="80"style="word-break: break-all;" <?= $trowspan; ?> valign="top"><?=$row['po_number']?></td>
											<td  width="80"style="word-break: break-all;" <?= $trowspan; ?> valign="top"><?=$order_status[$row['is_confirmed']];?></td>
											<td  width="80" style="word-break: break-all;" <?= $trowspan; ?> valign="top"><?=$pack_qty?></td>
											<td  width="80" style="word-break: break-all;" <?= $trowspan; ?> valign="top"><?=$pcs_qty?></td>
											<td  width="80" style="word-break: break-all;" <?= $trowspan; ?> valign="top"><?=$dzn_qty; ?></td>
											<td  width="80" style="word-break: break-all;" <?= $trowspan; ?> valign="top"><?=change_date_format($row['insert_date'],'',''); ?></td>
											<td  width="80" <?= $trowspan; ?> valign="top"><?= change_date_format($row['shipment_date'],'','')  ?></td>
											<td  width="80" <?= $trowspan; ?> valign="top"><? echo $product_dept[$row['product_dept_id']]  ?></td>
											<td  width="80" <?= $trowspan; ?> valign="top"><? echo implode(",", $gmts_item_str);  ?></td>
											<td  width="80" <?= $frowspan; ?> valign="top"  style="text-align: center;"><strong>Fabric</strong></td>	                        
											<?
												}
												if(count($row['booking_data'])>0){
													foreach ($row['booking_data'] as $fabric_data) { 
															if($k!=1) echo '<tr>';
													
															$partial_reportId=explode(",",$partial_report_id_arr[$row["company_name"]]);
															// echo "<pre>";
															// print_r($partial_reportId);
														$row_id=$partial_reportId[0];
												

														if($row_id==269){

														$variable="<a href='##' onClick=\"generate_worder_report('".$fabric_data['booking_no']."','".$row["company_name"]."','".$po_id."','".$fabric_data["item_category"]."','".$fabric_data["fabric_source"]."','".$row['job_no']."','".$fabric_data['is_approved']."','print_booking_12','fabric_booking')\"> ".$fabric_data['booking_no']." <a/>";

														}else if($row_id==160){

														$variable="<a href='##' onClick=\"generate_worder_report('".$fabric_data['booking_no']."','".$row["company_name"]."','".$po_id."','".$fabric_data["item_category"]."','".$fabric_data["fabric_source"]."','".$row['job_no']."','".$fabric_data['is_approved']."','print_booking_5','fabric_booking')\"> ".$fabric_data['booking_no']." <a/>";
														
														}else{
															$variable=$fabric_data['booking_no'];
														}
														?>	                        		
														<td  width="80" style="word-break: break-all;"><?= $color_name_arr[$fabric_data['fabric_color_id']] ?></td>
														<td  width="80" style="word-break: break-all;"><?= $fabric_data['fabric_description'] ?></td>
														<td  width="80" style="word-break: break-all;"><?= $variable;  ?></td>
														<td  width="80"><?= number_format($fabric_data['fin_fab_qnty'],2) ?></td>
														<td  width="80"><?= number_format($fabric_data['rcv_data'],4) ?></td>
														<td  width="80"><?= $unit_of_measurement[$fabric_data['uom']];  ?></td>
														<? if($k==1){ ?>
															<td  width="80" <?= $trowspan; ?>><?= $approval_status[$row['sample_data'][4]] ?></td>
															<td  width="80" <?= $trowspan; ?>><?= $approval_status[$row['sample_data'][2]] ?></td>
															<td  width="80" <?= $trowspan; ?>></td>
															<td  width="80" <?= $trowspan; ?>></td>
														<? }		                        			
														$k++;
													}
												}
												else{ ?>
													
													<td  width="80"></td>
													<td  width="80"></td>
													<td  width="80"></td>
													<td  width="80"></td>
													<td  width="80"></td>
													<td  width="80"></td>
													<td  width="80" <?= $trowspan; ?>><? 
													// $sample_app_status[$po_id][18]['approval_status']; 
													if($sample_app_status[$po_id][18]['approval_status']>0){
														echo "Approved";
													}
													?></td>
													<td  width="80" <?= $trowspan; ?>><?
													if($sample_app_status[$po_id][99]['approval_status']>0){
														echo "Approved";
													}
													// $approval_status[$row['sample_data'][7]] 
													?></td>
													<td  width="80" <?= $trowspan; ?>></td>
													<td  width="80" <?= $trowspan; ?>></td>
												<? }
											?>	                        
										</tr>
										<tr>
											<td width="80" <?= $trrowspan; ?>  style="text-align: center;" valign="top"><strong>Trims</strong></td>
											<?
											$row_id=0;
												if(count($row['trims_booking_data'])>0){
													foreach ($row['trims_booking_data'] as $trim_data) { 
															if($j!=1) echo '<tr>';

															$trims_reportId=explode(",",$trims_report_id_arr[$row["company_name"]]);
															$row_id=$trims_reportId[0];
															
															if($row_id==28){

															$variable="<a href='##' onClick=\"generate_worder_report('".$trim_data['booking_no']."','".$row["company_name"]."','".$po_id."','".$trim_data["item_category"]."','".$trim_data["fabric_source"]."','".$row['job_no']."','".$trim_data['is_approved']."','show_trim_booking_report13','trims_booking')\"> ".$trim_data['booking_no']." <a/>";
														
															}else{
																$variable=$trim_data['booking_no'];
															}
														?>	                        		
														<td  width="80"></td>
														<td  width="80" style="word-break: break-all;"><?= $trim_data['description'] ?></td>
														<td  width="80" style="word-break: break-all;"><?= $variable ?></td>
														<td  width="80" style="word-break: break-all;"><?= number_format($trim_data['wo_qnty'],2) ?></td>
														<td  width="80"><a href='##' onClick="generate_popup('<?=$job_id;?>','<?=$trim_data['booking_id'];?>','<?=$po_id;?>','<?=$trim_data["item_group_id"];?>','trims_rcv_popup')"><?= number_format($trim_data['rcv_qty'],4) ?></a></td>
														<td  width="80"><?= $unit_of_measurement[$trim_data['uom']] ?></td>
														<?	
														$j++;
													}
												}
												else{ ?>
													<td  width="80"></td>
													<td  width="80"></td>
													<td  width="80"></td>
													<td  width="80"></td>
													<td  width="80"></td>
													<td  width="80"></td>
												<? }
											?>
										</tr>					
										<?
										$i++;
									//}
								}
							}
								?>
						</tbody>
					</table>
				</div>
		
	    </div>
		<?
	
			

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
    echo "$html****$filename****1****$type";
    exit();
	disconnect($con);
	exit();
}


if($action=="trims_rcv_popup")
{
	//require_once('../../../../includes/common.php');
 	echo load_html_head_contents("Trims Description Details", "../../../../", 1, 1,$unicode,'','');

	extract($_REQUEST);
	//echo $id;//$job_no;
	
	

	$sql="SELECT b.po_breakdown_id, a.item_group_id, b.quantity as cons_qnty, a.rate, e.job_id, a.booking_id,c.recv_number,c.challan_no,c.receive_date	  	from inv_receive_master c,product_details_master d,inv_trims_entry_dtls a , order_wise_pro_details b, wo_po_break_down e 
	where a.mst_id=c.id and a.trans_id=b.trans_id and a.id=b.dtls_id and a.prod_id=d.id and a.booking_id=$booking_id and e.job_id=$job_id and b.po_breakdown_id in ($po_id) and a.item_group_id=$item_id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 	and b.is_deleted=0 and c.status_active=1 and e.id=b.po_breakdown_id  and a.rate>0";

	$sql_data=sql_select($sql);
	?>
    
	<div style="width:100%" align="center">
		<fieldset style="width:480px">
        <div class="form_caption" align="center"><strong>Trims Rcv Details</strong></div><br />
            <div style="width:100%">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>                	
                    <tr>
						<th width="35">SL</th>
                        <th width="100">Recv. ID</th>                       
						<th width="100">Chalan No</th>
						<th width="100">Recv. Date</th>
						<th width="100">Recv. Qty.</th>						
                     </tr>
                </thead>
            </table>
        </div>
        <div style="width:100%; max-height:400px;">
            <table cellpadding="0" width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
                <?
                $i=1;
   ;

 
                foreach($sql_data as $row)
                {
                    if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_l<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
					    <td width="35"><? echo $i; ?></td>                     
						<td width="100"  align="left"><?=$row[csf("recv_number")];?></td>
						<td width="100"  align="left"><?=$row[csf("challan_no")];?></td>
						<td width="100"  align="left"><?=change_date_format($row[csf("receive_date")],'','');?></td>
						<td width="100" align="right"><?=number_format($row[csf("cons_qnty")],2);?></td>
                    </tr>
                    <?
                    $tot_rec_qnty+=$row[csf("cons_qnty")];
					
					
                    $i++;
                }
                ?>
                <tfoot>
                   
                    <tr>
						<th  width="35" align="right"></th>
						<th  width="100" align="right"></th>						
						<th  width="100" align="right"></th>
                        <th width="100" align="right">Total</th>                     
						<th  width="100" align="right"><?=number_format($tot_rec_qnty,2);;?></th>

                    </tr>
                </tfoot>
            </table>
        </div>
		</fieldset>
	</div>
    <div style="display:none" id="data_panel"></div>
    <script type="text/javascript" src="../../../../js/jquery.js"></script>
    <script type="text/javascript" src="../../../../js/jquerybarcode.js"></script>
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	<?
    exit();
}