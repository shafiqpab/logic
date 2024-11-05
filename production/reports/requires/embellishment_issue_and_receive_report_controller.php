<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
$user_name=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//------------------------------------------------------------------------------------------
if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 100, "SELECT id,location_name from lib_location where company_id='$data' and status_active=1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/embellishment_issue_and_receive_report_controller',this.value, 'load_drop_down_floor', 'floor_td' );" );
	exit();
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_cut_floor_name", 100, "SELECT id,floor_name from lib_prod_floor where location_id='$data' and status_active=1 and is_deleted=0 and production_process in(1) order by floor_name","id,floor_name", 1, "-- Select --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_emb_floor")
{
	$data_ex = explode("_", $data);
	$embel_type = $data_ex[0];
	$company_id = $data_ex[1];
	$location_id = $data_ex[2];
	$prod_process = ($embel_type==1) ? 8 : 9;
	$location_cond = ($location_id==0) ? "" : " and location_id=$location_id";
	echo create_drop_down( "cbo_embel_floor", 100, "SELECT id,floor_name from lib_prod_floor where company_id=$company_id  $location_cond and status_active=1 and is_deleted=0 and production_process in($prod_process) order by floor_name","id,floor_name", 1, "-- Select --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" );
	exit();
}

if($action=="report_generate")
{

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_id 		= str_replace("'", "", $cbo_company_name);
	$wo_company_id 		= str_replace("'", "", $cbo_wo_company_name);
	$location_name 		= str_replace("'", "", $cbo_location_name);
	$cut_floor_name 	= str_replace("'", "", $cbo_cut_floor_name);
	$buyer_id 			= str_replace("'", "", $cbo_buyer_name);
	$txt_style_no 		= str_replace("'", "", $txt_style_no);
	$txt_internal_ref 	= str_replace("'", "", $txt_internal_ref);
	$txt_job_no 		= str_replace("'", "", $txt_job_no);
	$txt_order_no 		= str_replace("'", "", $txt_order_no);
	$embel_type 		= str_replace("'", "", $cbo_embel_type);
	$embel_floor 		= str_replace("'", "", $cbo_embel_floor);
	$txt_date_from 		= str_replace("'", "", $txt_date_from);
	$txt_date_to 		= str_replace("'", "", $txt_date_to);

	$lib_buyer=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	$lib_supplier=return_library_array( "select id, supplier_name from  lib_supplier",'id','supplier_name');
	$lib_company=return_library_array( "select id, company_name from  lib_company",'id','company_name');
	$lib_location=return_library_array( "select id, location_name from  lib_location",'id','location_name');
	$lib_floor=return_library_array( "select id, floor_name from  lib_prod_floor",'id','floor_name');
	$lib_color=return_library_array( "select id, color_name from  lib_color",'id','color_name');

	if($rptType==1) // issue
	{

		/* =================================================================================/
	    / 										SQL Condition								/
	    /================================================================================= */

		$sql_cond = "";
		$sql_cond .= ($company_id==0) ? "": " and d.company_name=$company_id";
		// $sql_cond .= ($wo_company_id==0) ? "": " and g.working_company_id=$wo_company_id";
		// $sql_cond .= ($location_name==0) ? "": " and g.location_id=$location_name";
		// $sql_cond .= ($cut_floor_name==0) ? "": " and g.floor_id=$cut_floor_name";
		$sql_cond .= ($buyer_id==0) ? "": " and d.buyer_name=$buyer_id";
		$sql_cond .= ($txt_style_no=="") ? "": " and d.style_ref_no like '%$txt_style_no%'";
		$sql_cond .= ($txt_internal_ref=="") ? "": " and e.grouping like '%$txt_internal_ref%'";
		$sql_cond .= ($txt_job_no=="") ? "": " and d.job_no_prefix_num=$txt_job_no";
		$sql_cond .= ($txt_order_no=="") ? "": " and e.po_number like '%$txt_order_no%'";
		$sql_cond .= ($embel_type==0) ? "": " and a.embel_name=$embel_type";
		$sql_cond .= ($embel_floor==0) ? "": " and a.floor_id=$embel_floor";

		$lay_cond = "";
		$lay_cond .= ($wo_company_id==0) ? "": " and working_company_id=$wo_company_id";
		$lay_cond .= ($location_name==0) ? "": " and location_id=$location_name";
		$lay_cond .= ($cut_floor_name==0) ? "": " and floor_id=$cut_floor_name";

		if($lay_cond!="")
		{
			$job_arr=return_library_array( "select job_no,job_no from  ppl_cut_lay_mst where status_active=1 and is_deleted=0 $lay_cond",'job_no','job_no');
			// print_r($job_arr);die();
			$lay_job_cond = where_con_using_array($job_arr,1,"d.job_no");
		}


		if(str_replace("'", "", $txt_date_from) !="")
		{
			if($db_type==0)
			{
				$date_cond="and a.production_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."' ";
			}
			else
			{
				$date_cond="and a.production_date between '".change_date_format($txt_date_from, "", "",1)."' and '".change_date_format($txt_date_to, "", "",1)."' ";
			}
		}

		/* =================================================================================/
	    / 										Main Query									/
	    /================================================================================= */

		$sql="SELECT f.SYS_NUMBER,a.po_break_down_id as PO_ID,a.LOCATION,a.FLOOR_ID, b.production_qnty  as QTY,d.COMPANY_NAME, d.BUYER_NAME, d.JOB_NO, d.style_ref_no as STYLE, e.PO_NUMBER, e.GROUPING,color_number_id as COLOR_ID,c.item_number_id as ITEM_ID from pro_garments_production_mst a, pro_garments_production_dtls b,pro_gmts_delivery_mst f, wo_po_color_size_breakdown c, wo_po_details_master d, wo_po_break_down e where a.id=b.mst_id and a.production_type=2 and b.production_type=2 and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and d.job_no=e.job_no_mst and d.job_no=c.job_no_mst and e.id=a.po_break_down_id and e.id=c.po_break_down_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.id=a.delivery_mst_id and f.status_active=1 and f.is_deleted=0 $sql_cond $date_cond $lay_job_cond";
		//echo $sql;die();
		$sql_res = sql_select($sql);
		if(count($sql_res)==0)
		{
			echo '<div style="text-align:center;color:red;font-weight:bold;font-size:18px;">Data not found.</div>';die();
		}
		$dataArray = array();
		$qtyArray = array();
		$po_id_array = array();
		foreach ($sql_res as $val)
		{
			$dataArray[$val['SYS_NUMBER']][$val['LOCATION']][$val['FLOOR_ID']][$val['JOB_NO']][$val['PO_ID']][$val['ITEM_ID']][$val['COLOR_ID']]=$val;
			$qtyArray[$val['SYS_NUMBER']][$val['LOCATION']][$val['FLOOR_ID']][$val['JOB_NO']][$val['PO_ID']][$val['ITEM_ID']][$val['COLOR_ID']]['qty'] += $val['QTY'];
			$qtyArray[$val['SYS_NUMBER']][$val['LOCATION']][$val['FLOOR_ID']][$val['JOB_NO']][$val['PO_ID']][$val['ITEM_ID']][$val['COLOR_ID']]['no_of_bundle']++;
			$po_id_array[$val['PO_ID']] = $val['PO_ID'];
		}
		// echo "<pre>"; print_r($qtyArray);die();
		unset($sql_res);

		$po_id_list_arr=array_chunk($po_id_array,999);
		$poCond = " and ";
		$p=1;
		foreach($po_id_list_arr as $poids)
	    {
	    	if($p==1)
			{
				$poCond .="  ( c.order_id in(".implode(',',$poids).")";
			}
	        else
	        {
	          $poCond .=" or c.order_id in(".implode(',',$poids).")";
	      	}
	        $p++;
	    }
	    $poCond .=")";
		unset($po_id_array);

		/* =================================================================================/
	    / 									Cut and Lay Data								/
	    /================================================================================= */
	    $sql="SELECT a.JOB_NO,a.CUTTING_NO,b.COLOR_ID, c.ORDER_ID,a.LOCATION_ID,a.FLOOR_ID,a.ENTRY_DATE,b.GMT_ITEM_ID as ITEM_ID from ppl_cut_lay_mst a, ppl_cut_lay_dtls b,ppl_cut_lay_bundle c where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $poCond";
		// echo $sql;die();
		$sql_res = sql_select($sql);
		$layDataArray = array();
		foreach ($sql_res as $val)
		{
			$layDataArray[$val['JOB_NO']][$val['ORDER_ID']][$val['ITEM_ID']][$val['COLOR_ID']]['cutting_no'] = $val['CUTTING_NO'];
			$layDataArray[$val['JOB_NO']][$val['ORDER_ID']][$val['ITEM_ID']][$val['COLOR_ID']]['entry_date'] = $val['ENTRY_DATE'];
			$layDataArray[$val['JOB_NO']][$val['ORDER_ID']][$val['ITEM_ID']][$val['COLOR_ID']]['floor_id'] = $val['FLOOR_ID'];
			$layDataArray[$val['JOB_NO']][$val['ORDER_ID']][$val['ITEM_ID']][$val['COLOR_ID']]['no_of_bundle']++;
		}
		// echo "<pre>"; print_r($layDataArray);echo "</pre>";;

		ob_start();
		?>
	 	<fieldset style="width:1230px;">
	 		<style type="text/css">
	 			h2{font-size: 20px;font-weight: bold;}
	 		</style>
	 		<div>
	 			<h2><? echo ucfirst($lib_company[$company_id]);?></h2>
	 			<h2><? echo ucfirst($lib_location[$location_name]);?></h2>
	 			<h2>Print Issue Report</h2>
	 			<h2>Issue Date : <? echo change_date_format(str_replace("'", "", $txt_date_from));?> To <? echo change_date_format(str_replace("'", "", $txt_date_to));?></h2>
	 		</div>
	 		<!-- ========================== table heading ========================== -->
	        <table width="1210" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" align="left">
	            <thead>
	               <tr>
		                <th width="30">SL</th>
		                <th width="80">Company</th>
		                <th width="80">Location</th>
		                <th width="80">Cutting Floor</th>
		                <th width="60">Cutting Date</th>
		                <th width="100">System Cut No.</th>
		                <th width="100">Buundle Issued No</th>
		                <th width="80">Buyer Name</th>
		                <th width="80">Job No</th>
		                <th width="80">Style Ref.</th>
						<th width="80">Internal Ref.</th>
		                <th width="80">PO</th>
		                <th width="80">Gmts Item</th>
		             	<th width="80">Color Name</th>
		                <th width="80">Floor</th>
		                <th width="60">No of Bundle Issue</th>
		                <th width="60">Issue Qty(Pcs)</th>
					</tr>
	            </thead>
	        </table>
	        <!-- ========================== table body ========================== -->
	        <div id="scroll_body" style="width:1230px;max-height:300px;overfllow-y:auto;">
	        	<table width="1210" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body" align="left">
		    		<tbody id="tbl_list_search" align="center">
		        		<?
		        		$i=1;
		        		$tot_no_of_bundle = 0;
		        		$tot_qty = 0;
		        		foreach ($dataArray as $sys_no => $sys_data)
		        		{
		        			foreach ($sys_data as $loc_id => $loc_data)
		        			{
		        				foreach ($loc_data as $floor_id => $floor_data)
		        				{
		        					foreach ($floor_data as $job_no => $job_data)
		        					{
		        						foreach ($job_data as $po_id => $po_data)
		        						{
		        							foreach ($po_data as $item_id => $item_data)
		        							{
		        								foreach ($item_data as $color_id => $row)
		        								{
		        									$cutting_no = $layDataArray[$job_no][$po_id][$item_id][$color_id]['cutting_no'];
		        									$cut_floor_id = $layDataArray[$job_no][$po_id][$item_id][$color_id]['floor_id'];
		        									$cutting_date = $layDataArray[$job_no][$po_id][$item_id][$color_id]['entry_date'];

		        									$no_of_bundle = $qtyArray[$sys_no][$loc_id][$floor_id][$job_no][$po_id][$item_id][$color_id]['no_of_bundle'];
		        									$qty = $qtyArray[$sys_no][$loc_id][$floor_id][$job_no][$po_id][$item_id][$color_id]['qty'];
		        									$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;
		        									?>
		        									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
		        										<td width="30" align="left"><? echo $i;?></td>
		        										<td width="80" align="left"><? echo $lib_company[$row['COMPANY_NAME']];?></td>
		        										<td width="80" align="left"><? echo $lib_location[$row['LOCATION']];?></td>
		        										<td width="80" align="left"><? echo $lib_floor[$cut_floor_id];?></td>
		        										<td width="60" align="center"><? echo change_date_format($cutting_date);?></td>
		        										<td width="100" align="left"><? echo $cutting_no;?></td>
		        										<td width="100" align="left"><? echo $sys_no;?></td>
		        										<td width="80" align="left"><? echo $lib_buyer[$row['BUYER_NAME']];?></td>
		        										<td width="80" align="left"><? echo $job_no;?></td>
		        										<td width="80" align="left"><? echo $row['STYLE'];?></td>
														<td width="80" align="left"><? echo $row['GROUPING'];?></td>
		        										<td width="80" align="left"><? echo $row['PO_NUMBER'];?></td>
		        										<td width="80" align="left"><? echo $garments_item[$row['ITEM_ID']];?></td>
		        										<td width="80" align="left"><? echo $lib_color[$row['COLOR_ID']];?></td>
		        										<td width="80" align="left"><? echo $lib_floor[$row['FLOOR_ID']];?></td>
		        										<td width="60" align="right"><? echo $no_of_bundle;?></td>
		        										<td width="60" align="right"><? echo $qty;?></td>
		        									</tr>
		        									<?
		        									$i++;
									        		$tot_no_of_bundle += $no_of_bundle;
									        		$tot_qty += $qty;
		        								}
		        							}
		        						}
		        					}
		        				}
		        			}
		        		}
		        		?>
		        	</tbody>
		        </table>
		    </div>
		    <!-- ========================== table footer ========================== -->
	        <table width="1210" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" align="left">
	            <tfoot>
	               <tr>
		                <th width="30"></th>
		                <th width="80"></th>
		                <th width="80"></th>
		                <th width="80"></th>
		                <th width="60"> </th>
		                <th width="100"></th>
		                <th width="100"></th>
		                <th width="80"></th>
		                <th width="80"></th>
		                <th width="80"></th>
		                <th width="80"></th>
		                <th width="80"></th>
		             	<th width="80"></th>
		                <th width="80">Total</th>
		                <th width="60"><? echo $tot_no_of_bundle?></th>
		                <th width="60"><? echo $tot_qty?></th>
					</tr>
	            </tfoot>
	        </table>
	    </fieldset>
		<?
		unset($dataArray);
		unset($qtyArray);
		$html = ob_get_contents();
	    ob_clean();
	    foreach (glob("*.xls") as $filename) {
	    @unlink($filename);
	    }
	    //---------end------------//
	    $name=time();
	    $filename=$user_id."_".$name.".xls";
	    $create_new_doc = fopen($filename, 'w');
	    $is_created = fwrite($create_new_doc, $html);
	    echo "$html####$filename";
	    exit();
	}
	else // receive
	{

		/* =================================================================================/
	    / 										SQL Condition								/
	    /================================================================================= */

		$sql_cond = "";
		$sql_cond .= ($company_id==0) ? "": " and d.company_name=$company_id";
		$sql_cond .= ($wo_company_id==0) ? "": " and a.serving_company=$wo_company_id";
		$sql_cond .= ($location_name==0) ? "": " and a.location=$location_name";
		// $sql_cond .= ($cut_floor_name==0) ? "": " and a.floor_id=$cut_floor_name";
		$sql_cond .= ($buyer_id==0) ? "": " and d.buyer_name=$buyer_id";
		$sql_cond .= ($txt_style_no=="") ? "": " and d.style_ref_no like '%$txt_style_no%'";
		$sql_cond .= ($txt_internal_ref=="") ? "": " and e.grouping like '%$txt_internal_ref%'";
		$sql_cond .= ($txt_job_no=="") ? "": " and d.job_no like '%$txt_job_no%'";
		$sql_cond .= ($txt_order_no=="") ? "": " and e.po_number like '%$txt_order_no%'";
		$sql_cond .= ($embel_type==0) ? "": " and a.embel_name=$embel_type";
		$sql_cond .= ($embel_floor==0) ? "": " and a.floor_id=$embel_floor";

		if(str_replace("'", "", $txt_date_from) !="")
		{
			if($db_type==0)
			{
				$date_cond="and a.production_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."' ";
			}
			else
			{
				$date_cond="and a.production_date between '".change_date_format($txt_date_from, "", "",1)."' and '".change_date_format($txt_date_to, "", "",1)."' ";
			}
		}

		/* =================================================================================/
	    / 										Main Query									/
	    /================================================================================= */

		$sql="SELECT f.SYS_NUMBER,a.po_break_down_id as PO_ID,a.LOCATION,a.FLOOR_ID, b.production_qnty  as QTY,d.COMPANY_NAME, d.BUYER_NAME, d.JOB_NO, d.style_ref_no as STYLE, e.PO_NUMBER,e.GROUPING,color_number_id as COLOR_ID,c.item_number_id as ITEM_ID from pro_garments_production_mst a, pro_garments_production_dtls b,pro_gmts_delivery_mst f, wo_po_color_size_breakdown c, wo_po_details_master d, wo_po_break_down e where a.id=b.mst_id and a.production_type=3 and b.production_type=3 and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and d.job_no=e.job_no_mst and d.job_no=c.job_no_mst and e.id=a.po_break_down_id and e.id=c.po_break_down_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.id=a.delivery_mst_id and f.status_active=1 and f.is_deleted=0 $sql_cond $date_cond";
		// echo $sql;die();
		$sql_res = sql_select($sql);
		if(count($sql_res)==0)
		{
			echo '<div style="text-align:center;color:red;font-weight:bold;font-size:18px;">Data not found.</div>';die();
		}
		$dataArray = array();
		$qtyArray = array();
		foreach ($sql_res as $val)
		{
			$dataArray[$val['SYS_NUMBER']][$val['LOCATION']][$val['FLOOR_ID']][$val['JOB_NO']][$val['PO_ID']][$val['ITEM_ID']][$val['COLOR_ID']]=$val;
			$qtyArray[$val['SYS_NUMBER']][$val['LOCATION']][$val['FLOOR_ID']][$val['JOB_NO']][$val['PO_ID']][$val['ITEM_ID']][$val['COLOR_ID']]['qty'] += $val['QTY'];
			$qtyArray[$val['SYS_NUMBER']][$val['LOCATION']][$val['FLOOR_ID']][$val['JOB_NO']][$val['PO_ID']][$val['ITEM_ID']][$val['COLOR_ID']]['no_of_bundle']++;
		}
		// echo "<pre>"; print_r($po_id_array);
		unset($sql_res);

		ob_start();
		?>
	 	<fieldset style="width:990px;">
	 		<style type="text/css">
	 			h2{font-size: 20px;font-weight: bold;}
	 		</style>
	 		<div>
	 			<h2><? echo ucfirst($lib_company[$company_id]);?></h2>
	 			<h2><? echo ucfirst($lib_location[$location_name]);?></h2>
	 			<h2>Print Receive Report</h2>
	 			<h2>Receive Date : <? echo change_date_format(str_replace("'", "", $txt_date_from));?> To <? echo change_date_format(str_replace("'", "", $txt_date_to));?></h2>
	 		</div>
	 		<!-- ========================== table heading ========================== -->
	        <table width="1070" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" align="left">
	            <thead>
	               <tr>
		                <th width="30">SL</th>
		                <th width="80">Company</th>
		                <th width="80">Location</th>
		                <th width="100">Buundle Rcv No</th>
		                <th width="80">Buyer Name</th>
		                <th width="80">Job No</th>
		                <th width="80">Style Ref.</th>
						<th width="80">Internal Ref.</th>
		                <th width="80">PO</th>
		                <th width="80">Gmts Item</th>
		             	<th width="80">Color Name</th>
		                <th width="80">Floor</th>
		                <th width="60">No of Bundle Rcv</th>
		                <th width="60">Rcv Qty(Pcs)</th>
					</tr>
	            </thead>
	        </table>
	        <!-- ========================== table body ========================== -->
	        <div id="scroll_body" style="width:1070px;max-height:300px;overfllow-y:auto;">
	        	<table width="1070" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body" align="left">
		    		<tbody id="tbl_list_search" align="center">
		        		<?
		        		$i=1;
		        		$tot_no_of_bundle = 0;
		        		$tot_qty = 0;
		        		foreach ($dataArray as $sys_no => $sys_data)
		        		{
		        			foreach ($sys_data as $loc_id => $loc_data)
		        			{
		        				foreach ($loc_data as $floor_id => $floor_data)
		        				{
		        					foreach ($floor_data as $job_no => $job_data)
		        					{
		        						foreach ($job_data as $po_id => $po_data)
		        						{
		        							foreach ($po_data as $item_id => $item_data)
		        							{
		        								foreach ($item_data as $color_id => $row)
		        								{
		        									$no_of_bundle = $qtyArray[$sys_no][$loc_id][$floor_id][$job_no][$po_id][$item_id][$color_id]['no_of_bundle'];
		        									$qty = $qtyArray[$sys_no][$loc_id][$floor_id][$job_no][$po_id][$item_id][$color_id]['qty'];
		        									$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;
		        									?>
		        									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
		        										<td width="30" align="left"><? echo $i;?></td>
		        										<td width="80" align="left"><? echo $lib_company[$row['COMPANY_NAME']];?></td>
		        										<td width="80" align="left"><? echo $lib_location[$row['LOCATION']];?></td>
		        										<td width="100" align="left"><? echo $sys_no;?></td>
		        										<td width="80" align="left"><? echo $lib_buyer[$row['BUYER_NAME']];?></td>
		        										<td width="80" align="left"><? echo $job_no;?></td>
		        										<td width="80" align="left"><? echo $row['STYLE'];?></td>
														<td width="80" align="left"><? echo $row['GROUPING'];?></td>
		        										<td width="80" align="left"><? echo $row['PO_NUMBER'];?></td>
		        										<td width="80" align="left"><? echo $garments_item[$row['ITEM_ID']];?></td>
		        										<td width="80" align="left"><? echo $lib_color[$row['COLOR_ID']];?></td>
		        										<td width="80" align="left"><? echo $lib_floor[$row['FLOOR_ID']];?></td>
		        										<td width="60" align="right"><? echo $no_of_bundle;?></td>
		        										<td width="60" align="right"><? echo $qty;?></td>
		        									</tr>
		        									<?
		        									$i++;
									        		$tot_no_of_bundle += $no_of_bundle;
									        		$tot_qty += $qty;
		        								}
		        							}
		        						}
		        					}
		        				}
		        			}
		        		}
		        		?>
		        	</tbody>
		        </table>
		    </div>
		    <!-- ========================== table footer ========================== -->
	        <table width="970" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" align="left">
	            <tfoot>
	               <tr>
		                <th width="30"></th>
		                <th width="80"></th>
		                <th width="80"></th>
		                <th width="100"></th>
		                <th width="80"></th>
		                <th width="80"></th>
		                <th width="80"></th>
		                <th width="80"></th>
		                <th width="80"></th>
		             	<th width="80"></th>
		                <th width="80">Total</th>
		                <th width="60"><? echo $tot_no_of_bundle?></th>
		                <th width="60"><? echo $tot_qty?></th>
					</tr>
	            </tfoot>
	        </table>
	    </fieldset>
		<?
		unset($dataArray);
		unset($qtyArray);
		$html = ob_get_contents();
	    ob_clean();
	    foreach (glob("*.xls") as $filename) {
	    @unlink($filename);
	    }
	    //---------end------------//
	    $name=time();
	    $filename=$user_id."_".$name.".xls";
	    $create_new_doc = fopen($filename, 'w');
	    $is_created = fwrite($create_new_doc, $html);
	    echo "$html####$filename";
	    exit();
	}
}
?>
