<?
	header('Content-type:text/html; charset=utf-8');
	session_start();
	include('../../../includes/common.php');
	if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
	$permission=$_SESSION['page_permission'];
	$user_id = $_SESSION['logic_erp']["user_id"];
	$data=$_REQUEST['data'];
	$action=$_REQUEST['action'];

	if($action=="generate_report")
	{

		

		
		// $sql_result=sql_select("select gsd_dtls_id,smv,target_hundred_perc,cycle_time,theoritical_mp,layout_mp,work_load,weight,worker_tracking from ppl_balancing_dtls_entry where status_active=1  and is_deleted=0");
		// foreach($sql_result as $row)
		// {
		// 	$resourceArray[$row[csf('LIB_SEWING_ID')]]['THEORITICAL_MP']=$row;
		// } 
		// prod_resource_mst
		// prod_resource_dtls
		

		$process = array(&$_POST);
		extract(check_magic_quote_gpc($process));

		$rptType=str_replace("'","",$rptType);
		$cbo_company_name=str_replace("'","",$cbo_company_name);
		$txt_order_no=str_replace("'","",$txt_order_no);
		$txt_barcode_no=str_replace("'","",$txt_barcode_no);
		$txt_worker_id=str_replace("'","",$txt_worker_id);
		$txt_date_from=str_replace("'","",$txt_date_from);
		$txt_date_to=str_replace("'","",$txt_date_to);
		$report_title=str_replace("'","",$report_title);

		$company_libray_arr = return_library_array("SELECT id, company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
		$worker_libray_arr = return_library_array("SELECT emp_code, first_name from lib_employee where status_active=1 and is_deleted=0","emp_code","first_name");
		 
		$gmt_items_libray_arr = return_library_array("SELECT id, item_name from lib_garment_item where status_active=1 and is_deleted=0","id","item_name");
		$operation_libray_arr = return_library_array("SELECT id, operation_name from lib_sewing_operation_entry where status_active=1 and is_deleted=0","id","operation_name");

		$theoritical_mp_arr = return_library_array("SELECT ID,THEORITICAL_MP from PPL_BALANCING_DTLS_ENTRY where STATUS_ACTIVE=1 and IS_DELETED=0","ID","THEORITICAL_MP");

		$prod_resource_mst_arr = return_library_array("SELECT a.company_id,a.location_id,a.floor_id,a.line_number,b.id, b.mst_id, b.from_date, b.to_date, b.man_power, b.operator, b.helper,b.iron_man,b.qi, b.line_chief, b.active_machine, b.target_per_hour, b.working_hour, b.po_id, smv_adjust, smv_adjust_type, b.capacity,b.target_efficiency FROM prod_resource_mst a, prod_resource_dtls_mast b where a.id=b.mst_id and a.company_id=$cbo_company_name", "MST_ID", "TARGET_PER_HOUR");
		
		// echo "<pre>";
		// print_r($prod_resource_mst_arr);
		// die;

		

		// $resourceArray=array();
		// $resource_sql = "SELECT  a.company_id,a.location_id,a.floor_id,a.line_number,b.id, b.mst_id, b.from_date, b.to_date, b.man_power, b.operator, b.helper,b.iron_man,b.qi, b.line_chief, b.active_machine, b.target_per_hour, b.working_hour, b.po_id, smv_adjust, smv_adjust_type, b.capacity,b.target_efficiency FROM prod_resource_mst a, prod_resource_dtls_mast b where a.id=b.mst_id and b.is_deleted=0 and a.company_id=$cbo_company_name";
		// echo $resource_sql;die;
		// $result_resource_sql = sql_select($resource_sql);
		// foreach($result_resource_sql as $row)
		// {
		// 	$resourceArray[$row[csf('location_id')]][$row[csf('floor_id')]][$row[csf('line_number')]]=$row[csf('target_per_hour')];
		// 	$resourceArray[]=$row[csf('target_per_hour')];
		// } 

		// echo "<pre>";
		// print_r($resourceArray);
		// die;

		
		//print_r($theoritical_mp);die;

		

		$company_cond="";
		if($cbo_company_name!="") $company_cond=" and a.company_id=$cbo_company_name";
		$order_cond="";
		if($txt_order_no!="") $order_cond=" and b.po_number=$txt_order_no";
		$bundle_cond="";
		if($txt_barcode_no!="") $bundle_cond=" and a.barcode_no=$txt_barcode_no";
		$worker_cond="";
		if($txt_worker_id!="") $worker_cond=" and a.operator_id=$txt_worker_id";
		
		if($txt_date_from!="" && $txt_date_to!="") $date_cond="and a.insert_date between '".date("j-M-Y",strtotime($txt_date_from))."  01:00:01 AM' and '".date("j-M-Y",strtotime($txt_date_to))."  11:59:59 PM' "; else $date_cond="";

		if ($rptType==1) // Show
		{
			$main_sql = "SELECT a.barcode_no, a.operator_id as worker_id,a.LINE_ID, a.po_break_down_id, a.job_no, a.job_id, a.operator_id, a.item_number_id, a.lib_operation_id, a.operation_start, a.operation_end, a.BUNDLE_NO, b.po_number as order_no, c.style_ref_no
			from PRO_GMTS_OPERATION_TRACKING a, wo_po_break_down b, wo_po_details_master c
			where a.po_break_down_id=b.id and a.job_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_cond $order_cond $bundle_cond $worker_cond";

            //echo $main_sql;die;
			$main_result=sql_select($main_sql);
			// echo "<pre>";print_r($main_result);die;
			
			ob_start();	
			?>
			<div align="center" style="height:auto; margin:0 auto; padding:0; width:1450px">
				<table width="1450" cellpadding="0" cellspacing="0" id="caption" align="left">
					<thead>
						<tr style="border:none;">
							<td colspan="14" align="center" class="form_caption" style="border:none;font-size:16px; font-weight:bold" ><?=$report_title;?></td> 
						</tr>
						<tr style="border:none;">
							<td colspan="14" class="form_caption" align="center" style="border:none; font-size:14px;"><b>Company Name : <?=$company_libray_arr[$cbo_company_name];?></b></td>
						</tr>
					</thead>
				</table>
				<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="1450" rules="all" id="rpt_table_header" align="left">
					<thead>
						<tr>
							<th width="40">SL</th>
							<th width="120">Employee ID</th>
							<th width="120">Company Name</th>
							<th width="120">Floor Name</th>
							<th width="120">Line No</th>
							<th width="120">Bundle No</th>
							<th width="120">Operation No</th>
							<th width="120">Operation Name</th>
							<th width="120">Operator Target (%)</th>
							<th width="120">Operator Achievement (%)</th>
						</tr> 					
					</thead>
					<tbody>
						<?
						$i=1;
						foreach($main_result as $val)
						{
							if($i%2==0) $bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td align="center"><?= $i;?></td>
								<td align="center"><p><? echo $worker_libray_arr[$val["WORKER_ID"]]; ?>&nbsp;</p></td>
								<td align="center"><p><?= $company_libray_arr[$cbo_company_name];?>&nbsp;</p></td>
								<td align="center"><p><?= $floor_libray_arr[$val["LINE_ID"]]; ?>&nbsp;</p></td>
								<td align="center"><p><? echo $val["LINE_ID"]; ?>&nbsp;</p></td>
								<td align="center"><p><? echo $val["BUNDLE_NO"]; ?>&nbsp;</p></td> 
								<td align="center"><p><? echo $gmt_items_libray_arr[$val["ITEM_NUMBER_ID"]]; ?>&nbsp;</p></td>
								<td align="center"><p><? echo $operation_libray_arr[$val["LIB_OPERATION_ID"]]; ?>&nbsp;</p></td>
								<td align="center"><p><?= $prod_resource_mst_arr[$val["LINE_ID"]].'-'.$theoritical_mp_arr[$val["LINE_ID"]]; ?>&nbsp;</p></td>
								<td align="center"><p><? '0'; ?>&nbsp;</p></td>
							</tr>
							<?
							$i++;		
						}
						?>
					</tbody>
				</table>
	        </div>
	        <?
	    }

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
	    echo "$html**$filename**$report_type"; 
	    exit();	
	}
?>