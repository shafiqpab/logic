<?

use PhpOffice\PhpSpreadsheet\Reader\Xml\Style\NumberFormat;

header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.yarns.php');
require_once('../../../includes/class4/class.trims.php');
require_once('../../../includes/class4/class.emblishments.php');
require_once('../../../includes/class4/class.washes.php');
require_once('../../../includes/class4/class.fabrics.php');
require_once('../../../includes/class4/class.conversions.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];
$permission=$_SESSION['page_permission'];


if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "load_style(this.value);",0 );
	exit();
}

// if ($action=="load_drop_down_style")
// {
// 	$data=explode("_",$data);
// 	$company_id = $data[0];
// 	$buyer_id = $data[1];
// 	echo create_drop_down( "cbo_style_name", 150, "SELECT id, style_ref_no  from wo_po_details_master where company_name = '$company_id' and buyer_name='$buyer_id' and status_active =1 and is_deleted=0 ","id,style_ref_no", 1, "-- All Style --", $selected, "load_job(this.value);",0 );
// 	exit();
// }

if($action == 'style_search_popup') 
{
	extract($_REQUEST);
	echo load_html_head_contents('Popup Info', '../../../', 1, 1, $unicode);
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

		/*echo $company;
		echo $buyer;*/
		// $job_year=str_replace("'","",$job_year);
		if($company!=0) $company_cond=" and a.company_name in($company)"; else $company_cond="";
		if($buyer!=0) $buyer_cond=" and a.buyer_name in($buyer)"; else $buyer_cond="";
	
		// $sql = " SELECT a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num,b.file_no ,c.status_active from wo_po_details_master a , wo_po_break_down b ,LIB_FILE_CREATION c
		// where a.status_active=1 $company_cond $buyer_cond  and a.job_no = b.JOB_NO_MST and b.file_no = c.file_name and b.status_active=1 and b.is_deleted=0 
		// group by a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num,b.file_no,c.status_active order by job_no_prefix_num desc";
		$sql = " SELECT a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num,b.file_no ,c.status_active from wo_po_details_master a , wo_po_break_down b ,LIB_FILE_CREATION c
		where a.status_active=1 $company_cond $buyer_cond  and a.job_no = b.JOB_NO_MST and b.file_no = c.file_no and b.status_active=1 and b.is_deleted=0 
		group by a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num,b.file_no,c.status_active order by job_no_prefix_num desc";
		// echo $sql;
		echo create_list_view("list_view", "Style Ref No,Job No,File NO","120,100,130","450","300",0, $sql , "js_set_value", "id,job_no,style_ref_no,file_no,status_active", "", 1, "0", $arr, "style_ref_no,job_no,file_no", "","setFilterGrid('list_view',-1)","0","","") ;	
		echo "<input type='hidden' id='txt_selected_no' />";
		?>
	    <script language="javascript" type="text/javascript">
		var style_no='<?php echo $txt_style_no;?>';
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
    <?
	exit();
}
?>
<script>
	$('#content_search_style_details_with_file').hide(500);
	$('#content_search_order_with_file').hide(500);
	$('#content_search_yarn_requisition_with_file').hide(500);
	$('#content_search_yarn_wo_with_file').hide(500);
	$('#content_search_yarn_pi_with_file').hide(500);
	$('#content_search_woven_fabric_pi_with_file').hide(500);
	$('#content_search_accessories_pi_with_file').hide(500);
	$('#content_search_yarn_recv_with_file').hide(500);
	$('#content_search_yarn_issue_with_file').hide(500);
	$('#content_search_yarn_allocation_with_file').hide(500);
	$('#content_search_general_accessories_issue_with_file').hide(500);
	$('#content_search_general_accessories_receive_with_file').hide(500);
	$('#content_search_yarn_transfer_from_another_file').hide(500);
	$('#content_search_yarn_transfer_out_with_another_file').hide(500);
	$('#content_search_dyes_chemical_consumption_cost').hide(500);
	$('#content_search_file_wise_service_cost').hide(500);
	$('#content_search_file_wise_embllishment_cost').hide(500);
	$('#content_search_file_wise_textile_production').hide(500);
	$('#content_search_file_wise_garments_production').hide(500);
	$('#content_search_file_wise_import').hide(500);
	$('#content_search_file_wise_export').hide(500);
	$('#content_search_file_wise_analysis').hide(500);
         	    

	function fnc_close(str)
	{
		if(str!='content_search_style_details_with_file')$('#content_search_style_details_with_file').hide(500);
		if(str!='content_search_order_with_file')$('#content_search_order_with_file').hide(500);
		if(str!='content_search_yarn_requisition_with_file')$('#content_search_yarn_requisition_with_file').hide(500);
		if(str!='content_search_yarn_wo_with_file')$('#content_search_yarn_wo_with_file').hide(500);
		if(str!='content_search_yarn_pi_with_file')$('#content_search_yarn_pi_with_file').hide(500);
		if(str!='content_search_woven_fabric_pi_with_file')$('#content_search_woven_fabric_pi_with_file').hide(500);
		if(str!='content_search_accessories_pi_with_file')$('#content_search_accessories_pi_with_file').hide(500);
		if(str!='content_search_yarn_recv_with_file')$('#content_search_yarn_recv_with_file').hide(500);
		if(str!='content_search_yarn_issue_with_file')$('#content_search_yarn_issue_with_file').hide(500);
		if(str!='content_search_yarn_allocation_with_file')$('#content_search_yarn_allocation_with_file').hide(500);
		if(str!='content_search_general_accessories_issue_with_file')$('#content_search_general_accessories_issue_with_file').hide(500);
		if(str!='content_search_general_accessories_receive_with_file')$('#content_search_general_accessories_receive_with_file').hide(500);
		if(str!='content_search_yarn_transfer_from_another_file')$('#content_search_yarn_transfer_from_another_file').hide(500);
		if(str!='content_search_yarn_transfer_out_with_another_file')$('#content_search_yarn_transfer_out_with_another_file').hide(500);
		if(str!='content_search_dyes_chemical_consumption_cost')$('#content_search_dyes_chemical_consumption_cost').hide(500);
		if(str!='content_search_file_wise_service_cost')$('#content_search_file_wise_service_cost').hide(500);
		if(str!='content_search_file_wise_embllishment_cost')$('#content_search_file_wise_embllishment_cost').hide(500);
		if(str!='content_search_file_wise_textile_production')$('#content_search_file_wise_textile_production').hide(500);
		if(str!='content_search_file_wise_garments_production')$('#content_search_file_wise_garments_production').hide(500);
		if(str!='content_search_file_wise_import')$('#content_search_file_wise_import').hide(500);
		if(str!='content_search_file_wise_export')$('#content_search_file_wise_export').hide(500);
		if(str!='content_search_file_wise_analysis')$('#content_search_file_wise_analysis').hide(500);
	}

	// function fnc_hide_show()
	// {
	// 	accordion_menu( 'accordion_h1','content_search_style_details_with_file', '');
	// 	accordion_menu( 'accordion_h2','content_search_order_with_file', '');
	// 	accordion_menu( 'accordion_h3','content_search_yarn_requisition_with_file', '');
	// 	accordion_menu( 'accordion_h4','content_search_yarn_wo_with_file', '');
	// 	accordion_menu( 'accordion_h5','content_search_yarn_pi_with_file', '');
	// 	accordion_menu( 'accordion_h6','content_search_woven_fabric_pi_with_file', '');
	// 	accordion_menu( 'accordion_h7','content_search_accessories_pi_with_file', '');
	// 	accordion_menu( 'accordion_h8','content_search_yarn_recv_with_file', '');
	// }
</script>
<?
if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_name=str_replace("'","",$cbo_company_name);
	$buyer_name=str_replace("'","",$cbo_buyer_name);
	$style_ref=str_replace("'","",$txt_style_no);
	$job_no=str_replace("'","",$txt_job_no);
	$file_no=str_replace("'","",$txt_file_no);
	$file_status=str_replace("'","",$cbo_file_status);

	//echo $company_name."***".$buyer_name."***".$style_ref."***".$job_no."***".$file_no."***".$file_status;
	$companyArr = return_library_array("SELECT ID,COMPANY_NAME FROM LIB_COMPANY ","ID","COMPANY_NAME");
	$buyerArr = return_library_array("SELECT ID,BUYER_NAME FROM LIB_BUYER ","ID","BUYER_NAME");
	$count_arr=return_library_array( "SELECT ID, YARN_COUNT FROM LIB_YARN_COUNT",'ID','YARN_COUNT');
	$itemArr = return_library_array("SELECT ID,ITEM_NAME FROM LIB_ITEM_GROUP ","ID","ITEM_NAME");

	if($cbo_company_name!='') $company_name_cond="AND A.COMPANY_NAME IN ($company_name) ";else $company_name_cond="";
	if($cbo_company_name!='') $yarn_com_cond="AND A.COMPANY_ID IN ($company_name) ";else $yarn_com_cond="";
	if($cbo_buyer_name!=0) $buyer_name_cond="AND A.BUYER_ID IN ($buyer_name) ";else $buyer_name_cond="";
	if($txt_job_no!='') $job_no_cond="AND A.JOB_NO LIKE '%$job_no' ";else $job_no_cond="";
	if($txt_style_no!='') $style_no_cond="AND A.STYLE_REF_NO LIKE  '%$style_ref%' ";else $style_no_cond="";
	if($txt_file_no!='')  $file_cond="AND B.FILE_NO LIKE  '%$file_no' ";else $file_cond="";
	if($txt_file_no!='') $file_cond_lib="AND A.FILE_NO LIKE  '%$file_no' ";else $file_cond_lib="";
	if($txt_file_no!='') $lc_file_cond="AND E.INTERNAL_FILE_NO LIKE  '%$file_no' ";else $lc_file_cond="";
	
	//---01----style details with file start ---

	// $file_sql = "SELECT C.ID ,A.COMPANY_NAME,A.BUYER_NAME, A.STYLE_REF_NO,A.JOB_NO,A.JOB_NO_PREFIX_NUM,B.FILE_NO ,
	// C.FILE_VALUE,C.YARN_QNTY,C.FILE_QTY,C.STATUS_ACTIVE FROM WO_PO_DETAILS_MASTER A , WO_PO_BREAK_DOWN B ,LIB_FILE_CREATION C
	// WHERE A.STATUS_ACTIVE=1 $company_name_cond $buyer_name_cond $file_cond and A.JOB_NO = B.JOB_NO_MST AND B.FILE_NO = C.FILE_NO AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 
	// ORDER BY JOB_NO_PREFIX_NUM DESC";

	$file_sql = " SELECT A.ID AS JOB_ID, C.ID AS FILE_ID ,B.ID AS PO_ID , A.COMPANY_NAME,A.BUYER_NAME, A.STYLE_REF_NO,A.JOB_NO,A.JOB_NO_PREFIX_NUM,B.FILE_NO ,B.PO_NUMBER,B.PO_QUANTITY,B.UNIT_PRICE,B.PO_TOTAL_PRICE,B.SHIPMENT_DATE,C.FILE_VALUE,C.YARN_QNTY,C.FILE_QTY,C.STATUS_ACTIVE
	FROM WO_PO_DETAILS_MASTER A , WO_PO_BREAK_DOWN B ,LIB_FILE_CREATION C
	WHERE A.STATUS_ACTIVE=1 $company_name_cond $buyer_name_cond $file_cond AND A.ID = B.JOB_ID AND B.FILE_NO = C.FILE_NAME AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0";
	
	//echo $file_sql;
	$file_sql_result = sql_select($file_sql);
	$row_count = array();
	$span = 0;
	foreach($file_sql_result as $row_file)
	{
		$row_count[$row_file['FILE_ID']]++;
		$file_qty = $row_file['FILE_QTY'];
		$yarn_qty = $row_file['YARN_QNTY'];
		$file_value = $row_file['FILE_VALUE'];
		$job_no = $row_file['JOB_NO'];
	}
    ?>
	
	
	<div style="width:100%; margin-left:20px;" align="left">
		<form name="requisitionApproval_1" id="requisitionApproval_1"> 
			<h3 style="width:810px; margin-top:10px;" align="left" id="accordion_h22" class="accordion_h" onClick="accordion_menu(this.id,'content_search_style_details_with_file','fnc_close(this.id)')">
			<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tr>
					<td width="305" ><h3>+ Style Details With File (<?echo $file_no;?>)</h3></td>
					<td width="150"><h3>Total:</h3></td>
					<td width="80" align="center"><p><? echo $file_qty;?></p></td>
					<td width="80" align="center"><p><? echo $yarn_qty;?></p></td>
					<td width="80" align="left"><p></p></td>
					<td width="120" align="right"><p><?  echo "$".number_format($file_value,2);?></p></td>
				</tr>
			</table>
			</h3> 
			<div id="content_search_style_details_with_file">      
				<fieldset style="width:810px;">
					<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
						<thead>
							<tr>
								<th width="100"><p>Company</p> </th>
								<th width="100"><p>Buyer</p></th>
								<th width="100"><p>Style Name</p></th>
								<th width="150"><p>File No</p></th>
								<th width="80"><p>File Qty</p></th>
								<th width="80"><p>Yarn Qty</p></th>
								<th width="80"><p>File Status</p></th>
								<th width="120"><p>File Amount</p></th>
							</tr>
						</thead>
						<tbody>
							<?
							$i =1;
							$span = 0;
							foreach($file_sql_result as $result)
							{
								$rowspan = $row_count[$result['FILE_ID']];
								if ($i % 2 == 0) $bgcolor = "#DFDFDF";
								else $bgcolor = "#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>">
									<td width="100"><p><? echo $companyArr[$result['COMPANY_NAME']];?></p></td>
									<td width="100"><p><? echo $buyerArr[$result['BUYER_NAME']];?></p></td>
									<td width="100"><p><? echo $result['STYLE_REF_NO'];?></p></td>
									<td width="150"><p><? echo $result['FILE_NO'];?></p></td>
									<?
									if($span == 0)
									{?>
									<td rowspan="<?= $rowspan; ?>"  valign="middle" width="80" align="center"><p><? echo $result['FILE_QTY']; $total_qty += $result['FILE_QTY'];?></p></td>
									<td rowspan="<?= $rowspan; ?>"  valign="middle" width="80" align="center"><p><? echo $result['YARN_QNTY']; $total_yarn_qty += $result['YARN_QNTY'];?></p></td>
									<td rowspan="<?= $rowspan; ?>"  valign="middle" width="80" align="center"><p><? 
									echo $result['STATUS_ACTIVE']==1 ? "Running" : "Inactive";?></p></td>
									<td rowspan="<?= $rowspan; ?>"  valign="middle" width="120" align="right"><p><? echo "$".number_format($result['FILE_VALUE'],2);
									$total_file_value += $result['FILE_VALUE'];
									?></p></td>
									<?}?>
								</tr>
								<?
								$i++;
								$span++;
							}
							?>

							<tr>
								<td width="100" colspan="3"><p></p></td>
								<td width="150" align="right"><p><h3>Total:</h3></p></td>
								<td width="80" align="center"><p><b><? echo $total_qty;?></b></p></td>
								<td width="80" align="center"><p><b><? echo $total_yarn_qty;?></b></p></td>
								<td width="80" align="left"><p></p></td>
								<td width="120" align="right"><p><b><? echo "$".number_format($total_file_value,2);?></b></p></td>
							</tr>
						</tbody>
					</table>
				</fieldset>
			</div>
		</form>
	</div>
    
	<!--02--Order With File start : --> 
	<?
	
		// $order_sql = " SELECT C.ID AS FILE_ID ,B.ID AS PO_ID , A.COMPANY_NAME,A.BUYER_NAME, A.STYLE_REF_NO,A.JOB_NO_PREFIX_NUM,B.FILE_NO ,B.PO_NUMBER,B.PO_QUANTITY,B.UNIT_PRICE,B.PO_TOTAL_PRICE,B.SHIPMENT_DATE,C.FILE_VALUE,C.YARN_QNTY,C.FILE_QTY,C.STATUS_ACTIVE
		// FROM WO_PO_DETAILS_MASTER A , WO_PO_BREAK_DOWN B ,LIB_FILE_CREATION C
		// WHERE A.STATUS_ACTIVE=1 $company_cond $buyer_name_cond $file_cond AND A.JOB_NO = B.JOB_NO_MST AND B.FILE_NO = C.FILE_NO AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 ORDER BY JOB_NO_PREFIX_NUM DESC";

		//echo $order_sql;
		//$order_sql_result = sql_select($order_sql);

		$span = 0;
		$po_id_arr=array();
		$job_id_arr=array();
		$row_count = array();
		foreach($file_sql_result as $row_order)
		{
			$row_count[$row_order['FILE_ID']]++;
			//$po_ids .= $row_order['PO_ID'].',';
			$po_id_arr[$row_order['PO_ID']] = $row_order['PO_ID'];
			$job_id_arr[$row_order['JOB_ID']] = $row_order['JOB_ID'];
			$po_total_amnt += $row_order['PO_TOTAL_PRICE'];
		}
		//$all_po_id = ltrim(implode(",", array_unique(explode(",", chop($po_ids, ",")))), ',');

		$con = connect();
		//$rID=execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form in (16) and ref_from in(1,2,3,4,5,6,7,8,9,10,11)");
		$rID=execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form in (16)");
		if ($rID) oci_commit($con);
		if(!empty($po_id_arr))
		{
			fnc_tempengine("gbl_temp_engine", $user_id, 16, 1, $po_id_arr, $empty_arr);

			$cntry_shp_sql = "SELECT MIN(A.COUNTRY_SHIP_DATE) AS FIRST_DATE,MAX(A.COUNTRY_SHIP_DATE) AS LAST_DATE, MAX(A.PO_BREAK_DOWN_ID) AS PO_ID 
			FROM WO_PO_COLOR_SIZE_BREAKDOWN A , GBL_TEMP_ENGINE B
			WHERE A.PO_BREAK_DOWN_ID =B.REF_VAL  AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.USER_ID= $user_id AND B.ENTRY_FORM=16 AND B.REF_FROM=1
			GROUP BY A.PO_BREAK_DOWN_ID";
			//echo $cntry_shp_sql;
			$cntry_shp_sql_result = sql_select($cntry_shp_sql);
			foreach($cntry_shp_sql_result as $row_cntry_shp)
			{
				$country_shp_arr[$row_cntry_shp['PO_ID']]['FIRST_DATE']= $row_cntry_shp['FIRST_DATE'];
				$country_shp_arr[$row_cntry_shp['PO_ID']]['LAST_DATE']= $row_cntry_shp['LAST_DATE'];
			}
		
			//sales contact 
			$sc_order_sql = "SELECT B.WO_PO_BREAK_DOWN_ID, A.CONTRACT_NO , A.CONTRACT_VALUE 
			FROM COM_SALES_CONTRACT A, COM_SALES_CONTRACT_ORDER_INFO B  , GBL_TEMP_ENGINE C
			WHERE  B.WO_PO_BREAK_DOWN_ID =C.REF_VAL AND B.COM_SALES_CONTRACT_ID = A.ID AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND C.USER_ID= $user_id AND C.ENTRY_FORM=16 AND C.REF_FROM=1";
			//echo $sc_order_sql;
			$sc_order_sql_result = sql_select($sc_order_sql);
			foreach($sc_order_sql_result as $row_sc)
			{
				$sc_order_arr[$row_sc['WO_PO_BREAK_DOWN_ID']]['CONTRACT_NO']= $row_sc['CONTRACT_NO'];
				$sc_order_arr[$row_sc['WO_PO_BREAK_DOWN_ID']]['CONTRACT_VALUE'] += $row_sc['CONTRACT_VALUE'];
			}

		}

	?>

	<div style="width:100%; margin-left:20px;" align="left">
		<form name="requisitionApproval_1" id="requisitionApproval_1"> 
			<h3 style="width:920px;margin-top:10px;" align="left" id="accordion_h2" class="accordion_h" onClick="accordion_menu(this.id,'content_search_order_with_file','fnc_close(this.id)')">
				<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
					<tr>
						<td width="325" ><h3>+ Order With File : </h3></td>
						<td width="80"><p>Total:</p></td>
						<td width="100" align="right"><p><?echo "$". $po_total_amnt;?></p></td>
						<td width="80" align="center"><p></p></td>
						<td width="100" align="right"><p></p></td>
						<td width="120" align="right"><p></p></td>
						<td width="120" align="right"><p><?
						$total_order_file_value = $po_total_amnt- $file_value;
						if($total_order_file_value>0)echo "$". number_format($total_order_file_value,2);?></p></td>
					</tr>
				</table>
			</h3> 
			<div id="content_search_order_with_file">      
				<fieldset style="width:920px;">
					<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
				 		<thead>
                            <tr>
                                <th width="100"><p>Order No</p> </th>
                                <th width="100"><p>Order Qty</p></th>
                                <th width="120"><p>Sales Contact No</p></th>
                                <th width="80"><p>FOB</p></th>
                                <th width="100"><p>Order Amount</p></th>
                                <th width="80"><p>Order Ship <br> Date</p></th>
                                <th width="100"><p>Country Ship Date</p></th>
                                <th width="120"><p>Sales Contact Value</p></th>
                                <th width="120"><p>File Balance</p></th>
                            </tr>
                        </thead>
                        <tbody>
						<?
							$i =1;
							$span = 0;
							foreach($file_sql_result as $result)
							{
								$rowspan = $row_count[$result['FILE_ID']];
								if ($i % 2 == 0) $bgcolor = "#DFDFDF";
								else $bgcolor = "#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>">
									<td width="100"><p><?echo $result['PO_NUMBER'];?></p></td>
									<td width="100" align="center"><p><?echo $result['PO_QUANTITY'];?></p></td>
									<td width="120"><p><? echo $sc_order_arr[$result['PO_ID']]['CONTRACT_NO'] ;?></p></td>
									<td width="80" align="center"><p><? echo $result['UNIT_PRICE'];?></p></td>
									<td width="100" align="right"><p><?$sub_total_amnt =  $result['PO_TOTAL_PRICE'];
									echo "$". number_format($sub_total_amnt,2);
									$total_amnt += $sub_total_amnt ; ?></p></td>
									<td width="80" align="center"><p><? echo change_date_format($result['SHIPMENT_DATE']) ?></p></td>
									<td width="100" align="center"><p>First:<?echo change_date_format($country_shp_arr[$result['PO_ID']]['FIRST_DATE']);?> <br> Last: <?echo change_date_format($country_shp_arr[$result['PO_ID']]['LAST_DATE']);?></p></td>
									<?
									if($span == 0)
									{?>
									<td  rowspan="<?= $rowspan; ?>"  valign="middle"  width="120" align="right"><p><? echo number_format($sc_order_arr[$result['PO_ID']]['CONTRACT_VALUE'],2);
									$total_sc_value += $sc_order_arr[$result['PO_ID']]['CONTRACT_VALUE'];
									?></p></td>
									<td rowspan="<?= $rowspan; ?>"  valign="middle"  width="120" align="right"><p><? 
									echo "$". number_format($total_order_file_value,2);
									?></p></td>
									<?}?>
								</tr>
								<?
								$i++;
								$span++;
							}
							?>
							<tr>
								<td width="100" align="right"><p><h3>Total:</h3></p></td>
								<td width="100" align="center"><p></p></td>
								<td width="120"><p></p></td>
								<td width="80" align="center"><p></p></td>
								<td width="100" align="right"><p><b><?echo "$". number_format($total_amnt,2); ?></b></p></td>
								<td width="80" align="center"><p></p></td>
								<td width="100" align="right"><p></p></td>
								<td width="120" align="right"><p><b><?echo "$". number_format($total_sc_value,2);?></b></p></td>
								<td width="120" align="right"><p><b><?echo "$". number_format($total_order_file_value,2);?></b></p></td>
							</tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>




	<!-- 03--Yarn Requisition With File : --> 
	<?
	
	if(!empty($job_id_arr))
	{
		fnc_tempengine("gbl_temp_engine", $user_id, 16, 11, $job_id_arr, $empty_arr);

		// $req_sql = " SELECT C.ID AS FILE_ID ,B.ID AS PO_ID ,B.FILE_NO,C.QUANTITY ,C.COUNT_ID,C.COMPOSITION_ID,C.YARN_TYPE_ID,C.RATE,C.AMOUNT,D.REQU_NO,d.IS_APPROVED
		// FROM WO_PO_DETAILS_MASTER A , WO_PO_BREAK_DOWN B ,INV_PURCHASE_REQUISITION_DTLS C, INV_PURCHASE_REQUISITION_MST D, 
		// WHERE A.STATUS_ACTIVE=1 AND A.ID = B.JOB_ID AND A.ID = C.JOB_ID AND C.MST_ID = D.ID  $company_name_cond $buyer_cond $file_cond and B.STATUS_ACTIVE=1 AND B.IS_DELETED=0  AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 ORDER BY D.REQU_NO";

		$req_sql = " SELECT C.ID AS FILE_ID ,C.QUANTITY ,C.COUNT_ID,C.COMPOSITION_ID,C.YARN_TYPE_ID,C.RATE,C.AMOUNT,D.REQU_NO,d.IS_APPROVED
		FROM  GBL_TEMP_ENGINE A,INV_PURCHASE_REQUISITION_DTLS C, INV_PURCHASE_REQUISITION_MST D
		WHERE  A.REF_VAL = C.JOB_ID AND C.MST_ID = D.ID and C.STATUS_ACTIVE=1 AND C.IS_DELETED=0  AND D.STATUS_ACTIVE=1 AND D.IS_DELETED=0 AND A.USER_ID= $user_id AND A.ENTRY_FORM=16 AND A.REF_FROM=11";

		// echo $req_sql;
		$req_sql_result = sql_select($req_sql);
		$row_count = array();
		$span = 0;
		foreach($req_sql_result as $row_req)
		{
			$row_count[$row_req['FILE_NO']]++;
			$yarn_qnty += $row_req['QUANTITY'];
			$yarn_rate += $row_req['RATE'];
			$yarn_amnt += $row_req['AMOUNT'];
		}
	}
	
	 // /*
	?>

	<div style="width:100%; margin-left:20px;" align="left">
		<form name="requisitionApproval_1" id="requisitionApproval_1"> 
			<h3 style="width:920px;margin-top:10px;" align="left" id="accordion_h3" class="accordion_h" onClick="accordion_menu(this.id,'content_search_yarn_requisition_with_file','fnc_close(this.id)')">
				<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
					<tr>
						<td width="325" ><h3>+ Yarn Requisition With File </h3></td>
						<td width="100"><p><h3>Total:</h3></p></td>
						<td width="100" align="right"><p><?echo number_format($yarn_qnty,2);?></td>
						<td width="100" align="right"><p><?echo number_format($yarn_rate,2);?></td>
						<td width="100" align="right"><p><?echo number_format($yarn_amnt,2);?></td>
						<td width="80" align="center"><p></p></td>
						<td width="120" align="right"><p><?
						$total_req_file = $yarn_amnt - $file_value; 
						if($total_req_file>0)echo number_format($total_req_file,2)?></p></td>
					</tr>
				</table>
			</h3> 
			<div id="content_search_yarn_requisition_with_file">      
             	<fieldset style="width:920px;">
                 	<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
				 		<thead>
                            <tr>
                                <th width="120"><p>Req No</p> </th>
                                <th width="80"><p>Yarn Count</p></th>
                                <th width="120"><p>Composition</p></th>
                                <th width="100"><p>Yarn Type</p></th>
                                <th width="100"><p>Req Qty</p></th>
                                <th width="100"><p>Req Rate</p></th>
                                <th width="100"><p>Req Amount</p></th>
                                <th width="80"><p>Req Approved</p></th>
                                <th width="120"><p>File Balance</p></th>
                            </tr>
                        </thead>
                        <tbody>
							<?
							$i =1;
							$span = 0;
							foreach($req_sql_result as $result)
							{
								$rowspan = $row_count[$result['FILE_NO']];
								if ($i % 2 == 0) $bgcolor = "#DFDFDF";
								else $bgcolor = "#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>">
									<td width="120"><p><?echo $result['REQU_NO'];?></p></td>
									<td width="80"><p><?echo $count_arr[$result['COUNT_ID']]?></p></td>
									<td width="120"><p><? echo $composition[$result['COMPOSITION_ID']];?></p></td>
									<td width="100"><p><?echo $yarn_type[$result['YARN_TYPE_ID']];?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['QUANTITY'],2);
									$total_qnty +=$result['QUANTITY'];
									?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['RATE'],2);
									$total_rate +=$result['RATE'];
									?></p></td>
									<td width="100" align="right"><p><?  echo number_format($result['AMOUNT'],2);
									 $total_yarn_amnt += $result['AMOUNT'];
									 ?></p></td>
									<td width="80" align="center"><p><?echo $result['IS_APPROVED']==1 ? "YES" : "NO";?></p></td>
									<?
									if($span == 0)
									{?>
									<td rowspan="<?= $rowspan; ?>"  valign="middle" width="120" align="right"><p><? 
									echo number_format($total_req_file,2);
									?></p></td>
									<?}?>
                            	</tr>
								<?
								$i++;
								$span++;
							}
								?>
								<tr>
									<td width="325" colspan ="3"><h3></h3></td>
									<td width="100" align="right"><p><h3>Total:</h3></p></td>
									<td width="100" align="right"><p><b><?echo number_format($total_qnty,2);?></b></p></td>
									<td width="100" align="right"><p><b><?echo number_format($total_rate,2);?></b></p></td>
									<td width="100" align="right"><p><b><?echo number_format($total_yarn_amnt,2);?></b></p></td>
									<td width="80" align="center"><p><b></b></p></td>
									<td width="120" align="right"><p><b><?echo number_format($total_req_file,2)?></b></p></td>
								</tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>

	<!--04--Yarn WO With File : --> 
	<?
	if(!empty($job_id_arr))
	{
	
		$wo_sql = " SELECT C.ID AS WO_ID,C.SUPPLIER_ORDER_QUANTITY ,C.YARN_COUNT,C.YARN_COMP_TYPE1ST,C.YARN_TYPE
		,C.RATE,C.AMOUNT,D.IS_APPROVED,D.WO_NUMBER
		FROM GBL_TEMP_ENGINE A,WO_NON_ORDER_INFO_DTLS C,WO_NON_ORDER_INFO_MST D
		WHERE A.REF_VAL = C.JOB_ID AND C.MST_ID = D.ID
		AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0  AND D.STATUS_ACTIVE=1 and D.IS_DELETED=0  AND A.USER_ID= $user_id AND A.ENTRY_FORM=16 AND A.REF_FROM=11";

		//echo $wo_sql;
		$wo_sql_result = sql_select($wo_sql);
		$row_count = array();
		$span = 0;
		foreach($wo_sql_result as $row_req)
		{
			$row_count[$row_req['FILE_NO']]++;
			$yarn_wo_qnty += $row_req['SUPPLIER_ORDER_QUANTITY'];
			$yarn_wo_rate += $row_req['RATE'];
			$yarn_wo_amnt += $row_req['AMOUNT'];
		}
	}

	?>
	<div style="width:100%; margin-left:20px;" align="left">
		 <form name="requisitionApproval_1" id="requisitionApproval_1"> 
         <h3 style="width:920px; margin-top:10px;" align="left" id="accordion_h4" class="accordion_h" onClick="accordion_menu(this.id,'content_search_yarn_wo_with_file','fnc_close(this.id)')">
			<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tr>
					<td width="325" ><h3>+ Yarn WO With File </h3></td>
					<td width="100"><p><h3>Total:</h3></p></td>
					<td width="100" align="right"><p><?echo number_format($yarn_wo_qnty,2);?></td>
					<td width="100" align="right"><p><?echo number_format($yarn_wo_rate,2);?></td>
					<td width="100" align="right"><p><?echo number_format($yarn_wo_amnt,2);?></td>
					<td width="80" align="center"><p></p></td>
					<td width="120" align="right"><p><?
					$total_wo_file = $yarn_wo_amnt - $file_value;
					if($total_wo_file>0)echo number_format($total_wo_file,2)?></p></td>
				</tr>
			</table>
		</h3> 
         	<div id="content_search_yarn_wo_with_file">      
            	<fieldset style="width:920px;">
                 	<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
						<thead>
							<tr>
								<th width="120"><p>WO No</p> </th>
								<th width="80"><p>Yarn Count</p></th>
								<th width="120"><p>Composition</p></th>
								<th width="100"><p>Yarn Type</p></th>
								<th width="100"><p>WO Qty</p></th>
								<th width="100"><p>WO Rate</p></th>
								<th width="100"><p>WO Amount</p></th>
								<th width="80"><p>WO Approved</p></th>
								<th width="120"><p>File Balance</p></th>
							</tr>
						</thead>
						<tbody>
						<?
							$i =1;
							$span = 0;
							foreach($wo_sql_result as $result)
							{
								$rowspan = $row_count[$result['FILE_NO']];
								if ($i % 2 == 0) $bgcolor = "#DFDFDF";
								else $bgcolor = "#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>">
									<td width="120"><p><?echo $result['WO_NUMBER'];?></p></td>
									<td width="80"><p><?echo $count_arr[$result['YARN_COUNT']]?></p></td>
									<td width="120"><p><? echo $composition[$result['YARN_COMP_TYPE1ST']];?></p></td>
									<td width="100"><p><?echo $yarn_type[$result['YARN_TYPE']];?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['SUPPLIER_ORDER_QUANTITY'],2);
									$total_wo_qnty +=$result['SUPPLIER_ORDER_QUANTITY'];
									?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['RATE'],2);
									$total_wo_rate +=$result['RATE'];
									?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['AMOUNT'],2);
									$total_wo_amount +=$result['AMOUNT'];
									?></p></td>
									<td width="80" align="center"><p><?echo $result['IS_APPROVED']==1 ? "YES" : "NO";?></p></td>
									<?
									if($span == 0)
									{?>
									<td rowspan="<?= $rowspan; ?>"  valign="middle" width="120" align="right"><p><?
									echo number_format($total_wo_file,2);
									?></p></td>
									<?}?>
								</tr>
								<?
								$i++;
								$span++;
							}
								?>
								<tr>
									<td width="305" colspan ="3"><h3></h3></td>
									<td width="100" align="right"><p><h3>Total:</h3></p></td>
									<td width="100" align="right"><p><b><?echo number_format($total_wo_qnty,2);?></b></p></td>
									<td width="100" align="right"><p><b><?echo number_format($total_wo_rate,2);?></b></p></td>
									<td width="100" align="right"><p><b><?echo number_format($total_wo_amount,2);?></b></p></td>
									<td width="80" align="center"><p><b></b></p></td>
									<td width="120" align="right"><p><b><?echo number_format($total_wo_file,2)?></b></p></td>
								</tr>
						</tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>

	<!--05--Yarn PI With File : --> 
	<?
	if(!empty($job_id_arr))
	{
		
		$pi_sql = "SELECT C.ID AS FILE_ID ,e.QUANTITY ,e.COUNT_NAME,e.YARN_COMPOSITION_ITEM1,e.YARN_TYPE
		,e.NET_PI_RATE,e.NET_PI_AMOUNT,f.APPROVED,f.PI_NUMBER,f.ID as PI_ID,d.ID as WO_NON_ORD_ID
		from GBL_TEMP_ENGINE A ,WO_NON_ORDER_INFO_DTLS c,WO_NON_ORDER_INFO_MST d,
		COM_PI_ITEM_DETAILS e,COM_PI_MASTER_DETAILS f
		where A.REF_VAL = C.JOB_ID AND C.MST_ID = D.ID  and  c.ID = e.WORK_ORDER_DTLS_ID and e.PI_ID = f.ID
		and C.status_active=1 and C.is_deleted=0  and D.status_active=1 and D.is_deleted=0
		AND A.USER_ID= $user_id AND A.ENTRY_FORM=16 AND A.REF_FROM=11";
		//echo $pi_sql;

		$pi_sql_result = sql_select($pi_sql);
		$row_count = array();
		$pi_id_arr = array();
		$span = 0;
		foreach($pi_sql_result as $row_req)
		{
		$row_count[$row_req['FILE_NO']]++;
		//$pi_ids .= $row_req['PI_ID'].',';
		$pi_id_arr[$row_req['PI_ID']] = $row_req['PI_ID'];
		$wo_non_ord_id_arr[$row_req['WO_NON_ORD_ID']] = $row_req['WO_NON_ORD_ID'];
		//$wo_non_ord_id .= $row_req['WO_NON_ORD_ID'].',';
		$pi_qnty += $row_req['QUANTITY'];
		$pi_rate += $row_req['NET_PI_RATE'];
		$pi_amnt += $row_req['NET_PI_AMOUNT'];
		}
		//$all_pi_id = ltrim(implode(",", array_unique(explode(",", chop($pi_ids, ",")))), ',');
		//$all_wo_non_ord_id = ltrim(implode(",", array_unique(explode(",", chop($wo_non_ord_id, ",")))), ',');
		
	}

	if(!empty($pi_id_arr))
	{
		fnc_tempengine("gbl_temp_engine", $user_id, 16, 2, $pi_id_arr, $empty_arr);
		fnc_tempengine("gbl_temp_engine", $user_id, 16, 3, $wo_non_ord_id_arr, $empty_arr);

		//Accepted Payment start
		$payment_sql = "SELECT A.PI_ID AS PI_ID, B.INVOICE_ID , B.ACCEPTED_AMMOUNT 
		FROM COM_IMPORT_INVOICE_DTLS A ,COM_IMPORT_PAYMENT B ,GBL_TEMP_ENGINE C
		WHERE A.PI_ID = C.REF_VAL AND A.IMPORT_INVOICE_ID = B.INVOICE_ID AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0  AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.USER_ID= $user_id AND C.ENTRY_FORM=16 AND C.REF_FROM=2 ";
		//echo $payment_sql;
		$payment_sql_result = sql_select($payment_sql);
		foreach($payment_sql_result as $row)
		{
			$payment_arr[$row['PI_ID']]['ACCEPTED_AMMOUNT'] = $row['ACCEPTED_AMMOUNT'];
		}
	}

	

	?>
	<div style="width:100%; margin-left:20px;" align="left">
		 <form name="requisitionApproval_1" id="requisitionApproval_1"> 
         <h3 style="width:1080px;margin-top:10px;" align="left" id="accordion_h5" class="accordion_h" onClick="accordion_menu(this.id,'content_search_yarn_pi_with_file','fnc_close(this.id)')">
			<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tr>
					<td width="325" ><h3>+Yarn PI With File </h3></td>
					<td width="100"><p><h3>Total:</h3></p></td>
					<td width="100" align="right"><p><?echo number_format($pi_qnty,2);?></td>
					<td width="100" align="right"><p><?echo number_format($pi_rate,2);?></td>
					<td width="100" align="right"><p><?echo number_format($pi_amnt,2);?></td>
					<td width="80" align="center"><p></p></td>
					<td width="120" align="center"><p></p></td>
					<td width="120" align="right"><p><?
					$total_pi_file = $pi_amnt - $file_value;
					if($total_pi_file >0)echo number_format($total_pi_file,2)?></p></td>
				</tr>
			</table></h3> 
         	<div id="content_search_yarn_pi_with_file">      
             	<fieldset style="width:1080px;">
                 	<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
					 	<thead>
							<tr>
								<th width="120"><p>PI No</p> </th>
								<th width="80"><p>Yarn Count</p></th>
								<th width="120"><p>Composition</p></th>
								<th width="100"><p>Yarn Type</p></th>
								<th width="100"><p>PI Qty</p></th>
								<th width="100"><p>PI Rate</p></th>
								<th width="100"><p>PI Amount</p></th>
								<th width="80"><p>PI Approved</p></th>
								<th width="120"><p>Payment</p></th>
								<th width="120"><p>File Balance</p></th>
							</tr>
						</thead>
						<tbody>
						<?
							$i =1;
							$span = 0;
							foreach($pi_sql_result as $result)
							{
								$rowspan = $row_count[$result['FILE_NO']];
								if ($i % 2 == 0) $bgcolor = "#DFDFDF";
								else $bgcolor = "#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>">
									<td width="120"><p><?echo $result['PI_NUMBER'];?></p></td>
									<td width="80"><p><?echo $count_arr[$result['COUNT_NAME']]?></p></td>
									<td width="120"><p><? echo $composition[$result['YARN_COMPOSITION_ITEM1']];?></p></td>
									<td width="100"><p><?echo $yarn_type[$result['YARN_TYPE']];?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['QUANTITY'],2);
									$total_pi_qnty +=$result['QUANTITY'];
									?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['NET_PI_RATE'],2);
									$total_pi_rate +=$result['NET_PI_RATE'];
									?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['NET_PI_AMOUNT'],2);
									$total_pi_amount +=$result['NET_PI_AMOUNT'];
									?></p></td>
									<td width="80" align="center"><p><?echo $result['APPROVED']==1 ? "YES" : "NO";?></p></td>
									<td width="120" align="right"><p><?echo number_format($payment_arr[$result['PI_ID']]['ACCEPTED_AMMOUNT'],2); ?></p></td>							
									<?
									if($span == 0)
									{?>
									<td rowspan="<?= $rowspan; ?>"  valign="middle" width="120" align="right"><p><? 
									echo number_format($total_pi_file,2);
									?></p></td>
									<?}?>
								</tr>
								<?
								$i++;
								$span++;
							}
								?>
									<tr>
									<td width="325" colspan ="3"><h3></h3></td>
									<td width="100" align="right"><p><h3>Total:</h3></p></td>
									<td width="100" align="right"><p><b><?echo number_format($total_pi_qnty,2);?></b></p></td>
									<td width="100" align="right"><p><b><?echo number_format($total_pi_rate,2);?></b></p></td>
									<td width="100" align="right"><p><b><?echo number_format($total_pi_amount,2);?></b></p></td>
									<td width="80" align="center"><p><b></p></td>
									<td width="120" align="center"><p></p></td>
									<td width="120" align="right"><p><b><?echo number_format($total_pi_file,2)?></b></p></td>
								</tr>
						</tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>

	<!--06--Woven Fabric PI With File start : --> 
	<?
	//woven fabric --- item_catgoryId ==3

	$wo_fab_pi_sql = "SELECT e.ID as DTLS_ID,b.FILE_NO, e.QUANTITY ,e.COUNT_NAME,e.YARN_COMPOSITION_ITEM1,e.YARN_TYPE,e.NET_PI_RATE,e.NET_PI_AMOUNT,f.APPROVED,f.PI_NUMBER,f.ID as PI_ID
	FROM WO_PO_DETAILS_MASTER a , WO_PO_BREAK_DOWN b ,WO_BOOKING_DTLS C,COM_PI_ITEM_DETAILS e,
	COM_PI_MASTER_DETAILS f, GBL_TEMP_ENGINE g
	where  a.STATUS_ACTIVE=1 and e.WORK_ORDER_NO=c.BOOKING_NO and c.JOB_NO=a.JOB_NO AND  a.JOB_NO = b.JOB_NO_MST AND  e.PI_ID = f.ID and e.ITEM_CATEGORY_ID=3 AND b.ID = g.REF_VAL 
	and e.status_active=1 and e.is_deleted=0  and f.status_active=1 and f.is_deleted=0
	AND g.USER_ID= $user_id AND g.ENTRY_FORM=16 AND g.REF_FROM=1
	ORDER by e.ID ASC";

	// $wo_fab_pi_sql="SELECT A.ID as DTLS_ID,D.FILE_NO, A.QUANTITY ,A.COUNT_NAME,A.YARN_COMPOSITION_ITEM1,A.YARN_TYPE
	// ,A.NET_PI_RATE,A.NET_PI_AMOUNT,f.APPROVED,f.PI_NUMBER,f.ID as PI_ID
	// FROM COM_PI_ITEM_DETAILS A, WO_BOOKING_DTLS B, WO_PO_DETAILS_MASTER C ,WO_PO_BREAK_DOWN D
	// WHERE A.WORK_ORDER_NO=B.BOOKING_NO AND B.JOB_NO=C.JOB_NO AND C.JOB_NO = D.JOB_NO_MST AND A.IS_DELETED=0 AND A.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND C.STATUS_ACTIVE=1 AND A.ITEM_CATEGORY_ID = 3 AND D.ID IN ($all_po_id)";
	
	//echo $wo_fab_pi_sql;

	$wo_fab_pi_sql_result = sql_select($wo_fab_pi_sql);
	$row_count = array();
	$wo_fab_pi_arr = array();
	$span = 0;
	foreach($wo_fab_pi_sql_result as $row_req)
	{
	$row_count[$row_req['FILE_NO']]++;
	//$wo_fab_pi_ids .= $row_req['PI_ID'].',';
	$wo_fab_pi_arr[$row_req['PI_ID']] = $row_req['PI_ID'];
	$wo_fab_pi_qnty += $row_req['QUANTITY'];
	$wo_fab_pi_rate += $row_req['NET_PI_RATE'];
	$wo_fab_pi_amnt += $row_req['NET_PI_AMOUNT'];
	}
	//$all_fab_pi_id = ltrim(implode(",", array_unique(explode(",", chop($wo_fab_pi_ids, ",")))), ',');
	if(!empty($wo_fab_pi_arr))
	{
		fnc_tempengine("gbl_temp_engine", $user_id, 16, 4, $wo_fab_pi_arr, $empty_arr);
		//Accepted Payment start
		$payment_fab_sql = "SELECT a.PI_ID as PI_ID, b.INVOICE_ID , b.ACCEPTED_AMMOUNT 
		FROM COM_IMPORT_INVOICE_DTLS a ,COM_IMPORT_PAYMENT b ,GBL_TEMP_ENGINE C
		WHERE a.PI_ID =C.REF_VAL
		and a.IMPORT_INVOICE_ID = b.INVOICE_ID and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 AND  C.USER_ID=$user_id AND C.ENTRY_FORM=16 AND C.REF_FROM=4 ";
		//echo $payment_fab_sql;
		$payment_sql_fab_result = sql_select($payment_fab_sql);
		foreach($payment_sql_fab_result as $row)
		{
			$payment_fab_arr[$row['PI_ID']]['ACCEPTED_AMMOUNT'] = $row['ACCEPTED_AMMOUNT'];
		}
	}
	?>
	<div style="width:100%; margin-left:20px;" align="left">
		 <form name="requisitionApproval_1" id="requisitionApproval_1"> 
         <h3 style="width:1080px;margin-top:10px;" align="left" id="accordion_h6" class="accordion_h" onClick="accordion_menu(this.id,'content_search_woven_fabric_pi_with_file','fnc_close(this.id)')">
			<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tr>
					<td width="325" ><h3>+ Woven Fabric PI With File </h3></td>
					<td width="100"><p><h3>Total:</h3></p></td>
					<td width="100" align="right"><p><?echo number_format($wo_fab_pi_qnty,2);?></td>
					<td width="100" align="right"><p><?echo number_format($wo_fab_pi_rate,2);?></td>
					<td width="100" align="right"><p><?echo number_format($wo_fab_pi_amnt,2);?></td>
					<td width="80" align="center"><p></p></td>
					<td width="120" align="center"><p></p></td>
					<td width="120" align="right"><p><?
					$total_wo_fab_amnt = $wo_fab_pi_amnt - $file_value;
					if($total_wo_fab_amnt >0)echo number_format($total_wo_fab_amnt,2)?></p></td>
				</tr>
			</table></h3> 
         	<div id="content_search_woven_fabric_pi_with_file">      
             	<fieldset style="width:1080px;">
                 	<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
					 	<thead>
							<tr>
								<th width="120"><p>PI No</p> </th>
								<th width="80"><p>Yarn Count</p></th>
								<th width="120"><p>Composition</p></th>
								<th width="100"><p>Yarn Type</p></th>
								<th width="100"><p>PI Qty</p></th>
								<th width="100"><p>PI Rate</p></th>
								<th width="100"><p>PI Amount</p></th>
								<th width="80"><p>PI Approved</p></th>
								<th width="120"><p>Payment</p></th>
								<th width="120"><p>File Balance</p></th>
							</tr>
						</thead>
						<tbody>
						<?
							$i =1;
							$span = 0;
							foreach($wo_fab_pi_sql_result as $result)
							{
								$rowspan = $row_count[$result['FILE_NO']];
								if ($i % 2 == 0) $bgcolor = "#DFDFDF";
								else $bgcolor = "#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>">
									<td width="120"><p><?echo $result['PI_NUMBER'];?></p></td>
									<td width="80"><p><?echo $count_arr[$result['COUNT_NAME']]?></p></td>
									<td width="120"><p><? echo $composition[$result['YARN_COMPOSITION_ITEM1']];?></p></td>
									<td width="100"><p><?echo $yarn_type[$result['YARN_TYPE']];?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['QUANTITY'],2);
									$total_fab_pi_qnty +=$result['QUANTITY'];
									?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['NET_PI_RATE'],2);
									$total_fab_pi_rate +=$result['NET_PI_RATE'];
									?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['NET_PI_AMOUNT'],2);
									$total_fab_pi_amount +=$result['NET_PI_AMOUNT'];
									?></p></td>
									<td width="80" align="center"><p><?echo $result['APPROVED']==1 ? "YES" : "NO";?></p></td>
									<td width="120" align="right"><p><?echo number_format($payment_fab_arr[$result['PI_ID']]['ACCEPTED_AMMOUNT'],2); ?></p></td>							
									<?
									if($span == 0)
									{?>
									<td rowspan="<?= $rowspan; ?>"  valign="middle" width="120" align="right"><p><? 
									echo number_format($total_wo_fab_amnt,2);
									?></p></td>
									<?}?>
								</tr>
								<?
								$i++;
								$span++;
							}
								?>
									<tr>
									<td width="325" colspan ="3"><h3></h3></td>
									<td width="100" align="right"><p><h3>Total:</h3></p></td>
									<td width="100" align="right"><p><b><?echo number_format($total_fab_pi_qnty,2);?></b></p></td>
									<td width="100" align="right"><p><b><?echo number_format($total_fab_pi_rate,2);?></b></p></td>
									<td width="100" align="right"><p><b><?echo number_format($total_fab_pi_amount,2);?></b></p></td>
									<td width="80" align="center"><p><b></p></td>
									<td width="120" align="center"><p></p></td>
									<td width="120" align="right"><p><b><?echo number_format($total_wo_fab_amnt,2)?></b></p></td>
								</tr>
						</tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>


	<!--07--Accessories PI With File start : --> 
	<?
	//item_catgoryId ==4 

	$accessories_pi_sql = "SELECT e.ID as DTLS_ID,b.FILE_NO, e.QUANTITY ,e.COUNT_NAME,e.ITEM_GROUP,e.YARN_COMPOSITION_ITEM1,e.YARN_TYPE,e.NET_PI_RATE,e.NET_PI_AMOUNT,f.APPROVED,f.PI_NUMBER,f.ID as PI_ID , f.PI_DATE
	from wo_po_details_master a , wo_po_break_down b ,
	COM_PI_ITEM_DETAILS e,COM_PI_MASTER_DETAILS f,GBL_TEMP_ENGINE g
	where  a.STATUS_ACTIVE=1 and a.JOB_NO = b.JOB_NO_MST AND b.ID = e.ORDER_ID AND  e.PI_ID = f.ID
	AND e.ORDER_ID = g.REF_VAL  and e.ITEM_CATEGORY_ID=4
	and e.status_active=1 and e.is_deleted=0  and f.status_active=1 and f.is_deleted=0
	AND g.USER_ID= $user_id AND g.ENTRY_FORM=16 AND g.REF_FROM=1
	ORDER by e.ID ASC";
	//echo $accessories_pi_sql;

	$acc_pi_sql_result = sql_select($accessories_pi_sql);
	$row_count = array();
	$acc_pi_arr = array();
	$span = 0;
	foreach($acc_pi_sql_result as $row_req)
	{
	$row_count[$row_req['FILE_NO']]++;
	//$acc_pi_ids .= $row_req['PI_ID'].',';
	$acc_pi_arr[$row_req['PI_ID']] = $row_req['PI_ID'];
	$acc_pi_qnty += $row_req['QUANTITY'];
	$acc_pi_rate += $row_req['NET_PI_RATE'];
	$acc_pi_amnt += $row_req['NET_PI_AMOUNT'];
	}
	//$all_pi_id = ltrim(implode(",", array_unique(explode(",", chop($pi_ids, ",")))), ',');

	//Accepted Payment start
	if(!empty($acc_pi_arr))
	{
		fnc_tempengine("gbl_temp_engine", $user_id, 16, 5, $acc_pi_arr, $empty_arr);

		$payment_acc_sql = "SELECT a.PI_ID as PI_ID, b.INVOICE_ID , b.ACCEPTED_AMMOUNT 
		FROM COM_IMPORT_INVOICE_DTLS a ,COM_IMPORT_PAYMENT b ,GBL_TEMP_ENGINE c
		WHERE a.PI_ID = c.REF_VAL
		and a.IMPORT_INVOICE_ID = b.INVOICE_ID and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 AND c.USER_ID=$user_id AND c.ENTRY_FORM=16 AND c.REF_FROM=5";
		//echo $payment_sql;
		$payment_acc_sql_result = sql_select($payment_acc_sql);
		foreach($payment_acc_sql_result as $row)
		{
			$payment_acc_arr[$row['PI_ID']]['ACCEPTED_AMMOUNT'] = $row['ACCEPTED_AMMOUNT'];
		}
	}
	

	?>
	<div style="width:100%; margin-left:20px;" align="left">
		 <form name="requisitionApproval_1" id="requisitionApproval_1"> 
         <h3 style="width:980px;margin-top:10px;" align="left" id="accordion_h7" class="accordion_h" onClick="accordion_menu(this.id,'content_search_accessories_pi_with_file','fnc_close(this.id)')">
			<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tr>
					<td width="225" ><h3>+ Accessories PI With File </h3></td>
					<td width="100"><p><h3>Total:</h3></p></td>
					<td width="100" align="right"><p><?echo number_format($acc_pi_qnty,2);?></td>
					<td width="100" align="right"><p><?echo number_format($acc_pi_rate,2);?></td>
					<td width="100" align="right"><p><?echo number_format($acc_pi_amnt,2);?></td>
					<td width="80" align="center"><p></p></td>
					<td width="120" align="center"><p></p></td>
					<td width="120" align="right"><p><?
					$total_acc_pi_file = $acc_pi_amnt - $file_value;
					if($total_acc_pi_file >0)echo number_format($total_acc_pi_file,2)?></p></td>
				</tr>
			</table></h3> 
         	<div id="content_search_accessories_pi_with_file">      
             	<fieldset style="width:980px;">
                 	<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
					 	<thead>
							<tr>
								<th width="120"><p>PI No</p> </th>
								<th width="100"><p>Item Group</p></th>
								<th width="100"><p>PI Date</p></th>
								<th width="100"><p>PI Qty</p></th>
								<th width="100"><p>PI Rate</p></th>
								<th width="100"><p>PI Amount</p></th>
								<th width="80"><p>PI Approved</p></th>
								<th width="120"><p>Payment</p></th>
								<th width="120"><p>File Balance</p></th>
							</tr>
						</thead>
						<tbody>
						<?
							$i =1;
							$span = 0;
							foreach($acc_pi_sql_result as $result)
							{
								$rowspan = $row_count[$result['FILE_NO']];
								if ($i % 2 == 0) $bgcolor = "#DFDFDF";
								else $bgcolor = "#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>">
									<td width="120"><p><?echo $result['PI_NUMBER'];?></p></td>
									<td width="100"><p><? echo $itemArr[$result['ITEM_GROUP']];?></p></td>
									<td width="100" align="center"><p><?echo change_date_format($result['PI_DATE']);?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['QUANTITY'],2);
									$total_acc_pi_qnty +=$result['QUANTITY'];
									?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['NET_PI_RATE'],2);
									$total_acc_pi_rate +=$result['NET_PI_RATE'];
									?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['NET_PI_AMOUNT'],2);
									$total_acc_pi_amount +=$result['NET_PI_AMOUNT'];
									?></p></td>
									<td width="80" align="center"><p><?echo $result['APPROVED']==1 ? "YES" : "NO";?></p></td>
									<td width="120" align="right"><p><?echo number_format($payment_acc_arr[$result['PI_ID']]['ACCEPTED_AMMOUNT'],2); ?></p></td>							
									<?
									if($span == 0)
									{
										?>
									<td rowspan="<?= $rowspan; ?>"  valign="middle" width="120" align="right"><p><? 
									echo number_format($total_acc_pi_file,2);
									?></p></td>
									<?}?>
								</tr>
								<?
								$i++;
								$span++;
							}
								?>
									<tr>
									<td width="225" colspan ="2"><h3></h3></td>
									<td width="100" align="right"><p><h3>Total:</h3></p></td>
									<td width="100" align="right"><p><b><?echo number_format($total_acc_pi_qnty,2);?></b></p></td>
									<td width="100" align="right"><p><b><?echo number_format($total_acc_pi_rate,2);?></b></p></td>
									<td width="100" align="right"><p><b><?echo number_format($total_acc_pi_amount,2);?></b></p></td>
									<td width="80" align="center"><p><b></p></td>
									<td width="120" align="center"><p></p></td>
									<td width="120" align="right"><p><b><?echo number_format($total_acc_pi_file,2)?></b></p></td>
								</tr>
						</tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>
			
	<!--08--Yarn Recv With File start : --> 
	<?

	// $rcv_pi_sql = "SELECT e.ID as DTLS_ID,b.FILE_NO, e.QUANTITY ,e.COUNT_NAME,e.YARN_COMPOSITION_ITEM1,e.YARN_TYPE
	// ,e.NET_PI_RATE,e.NET_PI_AMOUNT,f.APPROVED,f.PI_NUMBER,f.ID as PI_ID
	// from wo_po_details_master a , wo_po_break_down b ,
	// COM_PI_ITEM_DETAILS e,COM_PI_MASTER_DETAILS f
	// where  a.STATUS_ACTIVE=1 and a.JOB_NO = b.JOB_NO_MST AND b.ID = e.ORDER_ID AND  e.PI_ID = f.ID AND e.ORDER_ID in ($all_po_id) and e.ITEM_CATEGORY_ID=4
	// and e.status_active=1 and e.is_deleted=0  and f.status_active=1 and f.is_deleted=0
	// ORDER by e.ID ASC";


	// $rcv_pi_sql = "SELECT A.ID,A.RECV_NUMBER, A.RECEIVE_PURPOSE, B.ID, A.BOOKING_ID, A.RECEIVE_BASIS,B.PI_WO_BATCH_NO,C.PRODUCT_NAME_DETAILS,C.LOT,B.ORDER_UOM,
	// B.ORDER_QNTY,B.ORDER_RATE,B.ORDER_ILE_COST,B.ORDER_AMOUNT,B.CONS_AMOUNT,B.BOOKING_NO 
	// FROM INV_RECEIVE_MASTER A, INV_TRANSACTION B, PRODUCT_DETAILS_MASTER C, COM_PI_MASTER_DETAILS D
	// WHERE A.ID=B.MST_ID AND B.PROD_ID=C.ID AND
	// A.BOOKING_ID = D.ID AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 
	// AND D.ID IN ($all_pi_id)";


	
	$rcv_pi_sql = "SELECT C.ID AS PI_ID,A.RECV_NUMBER, A.RECEIVE_PURPOSE, B.ID, A.BOOKING_ID, A.RECEIVE_BASIS,B.PI_WO_BATCH_NO,B.ORDER_UOM,
	B.ORDER_QNTY,B.ORDER_RATE,B.ORDER_ILE_COST,B.ORDER_AMOUNT,B.CONS_AMOUNT,B.BOOKING_NO
	FROM INV_RECEIVE_MASTER A, INV_TRANSACTION B,  COM_PI_MASTER_DETAILS C , GBL_TEMP_ENGINE D
	WHERE A.ID=B.MST_ID and A.BOOKING_ID = C.ID AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 
	AND C.ID = D.REF_VAL AND D.USER_ID=$user_id AND D.ENTRY_FORM=16 AND D.REF_FROM=2
	UNION ALL
	SELECT 0 AS PI_ID,A.RECV_NUMBER, A.RECEIVE_PURPOSE, B.ID, A.BOOKING_ID, A.RECEIVE_BASIS,B.PI_WO_BATCH_NO,B.ORDER_UOM,
	B.ORDER_QNTY,B.ORDER_RATE,B.ORDER_ILE_COST,B.ORDER_AMOUNT,B.CONS_AMOUNT,B.BOOKING_NO
	FROM INV_RECEIVE_MASTER A, INV_TRANSACTION B,  WO_NON_ORDER_INFO_MST C , GBL_TEMP_ENGINE D
	WHERE A.ID=B.MST_ID and A.BOOKING_ID = C.ID AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 
	AND C.ID = D.REF_VAL AND D.USER_ID=$user_id AND D.ENTRY_FORM=16 AND D.REF_FROM=3";

	//echo $rcv_pi_sql;

	$rcv_pi_sql_result = sql_select($rcv_pi_sql);
	$row_count = array();
	$rcv_pi_arr = array();
	$span = 0;
	foreach($rcv_pi_sql_result as $row_req)
	{
	$row_count[$row_req['FILE_NO']]++;
	//$rcv_pi_ids .= $row_req['PI_ID'].',';
	$rcv_pi_arr[$row_req['PI_ID']] = $row_req['PI_ID'];
	$rcv_pi_qnty += $row_req['ORDER_QNTY'];
	$rcv_pi_rate += $row_req['ORDER_RATE'];
	$rcv_pi_amnt += $row_req['ORDER_AMOUNT'];
	}

	//$all_rcv_pi_id = ltrim(implode(",", array_unique(explode(",", chop($rcv_pi_ids, ",")))), ',');
	if(!empty($rcv_pi_arr))
	{
		fnc_tempengine("gbl_temp_engine", $user_id, 16, 6, $rcv_pi_arr, $empty_arr);
		$pi_composition = "SELECT A.PI_ID,A.COUNT_NAME,A.YARN_COMPOSITION_ITEM1,A.YARN_TYPE 
		FROM COM_PI_ITEM_DETAILS A ,GBL_TEMP_ENGINE B
		WHERE A.PI_ID = B.REF_VAL AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0  AND B.USER_ID=$user_id AND B.ENTRY_FORM=16 AND B.REF_FROM=6";
		//echo $pi_composition;
		$pi_composition_sql_result = sql_select($pi_composition);
		foreach($pi_composition_sql_result as $row_req)
		{
		$composition_arr[$row_req['PI_ID']]['COUNT_NAME'] = $row_req['COUNT_NAME'];
		$composition_arr[$row_req['PI_ID']]['YARN_COMPOSITION_ITEM1'] = $row_req['YARN_COMPOSITION_ITEM1'];
		$composition_arr[$row_req['PI_ID']]['YARN_TYPE'] = $row_req['YARN_TYPE'];
		}
	}
	
	?>
	<div style="width:100%; margin-left:20px;" align="left">
		 <form name="requisitionApproval_1" id="requisitionApproval_1"> 
         <h3 style="width:880px;margin-top:10px;" align="left" id="accordion_h8" class="accordion_h" onClick="accordion_menu(this.id,'content_search_yarn_recv_with_file','fnc_close(this.id)')">
			<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tr>
					<td width="325" ><h3>+ Yarn Recv With File </h3></td>
					<td width="100"><p><h3>Total:</h3></p></td>
					<td width="100" align="right"><p><?echo number_format($rcv_pi_qnty,2);?></td>
					<td width="100" align="right"><p><?echo number_format($rcv_pi_rate,2);?></td>
					<td width="100" align="right"><p><?echo number_format($rcv_pi_amnt,2);?></td>
					<td width="120" align="right"><p><?
					$total_rcv_pi_file = $rcv_pi_amnt - $file_value;
					if($total_rcv_pi_file >0)echo number_format($total_rcv_pi_file,2)?></p></td>
				</tr>
			</table></h3> 
         	<div id="content_search_yarn_recv_with_file">      
             	<fieldset style="width:880px;">
                 	<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
					 	<thead>
							<tr>
								<th width="120"><p>MRR No</p> </th>
								<th width="80"><p>Yarn Count</p></th>
								<th width="120"><p>Composition</p></th>
								<th width="100"><p>Yarn Type</p></th>
								<th width="100"><p>MRR Qty</p></th>
								<th width="100"><p>MRR Rate</p></th>
								<th width="100"><p>MRR Amount</p></th>
								<th width="120"><p>File Balance</p></th>
							</tr>
						</thead>
						<tbody>
						<?
							$i =1;
							$span = 0;
							foreach($rcv_pi_sql_result as $result)
							{
								$rowspan = $row_count[$result['FILE_NO']];
								if ($i % 2 == 0) $bgcolor = "#DFDFDF";
								else $bgcolor = "#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>">
									<td width="120"><p><?echo $result['RECV_NUMBER'];?></p></td>
									<td width="80"><p><?echo $count_arr[$composition_arr[$row_req['PI_ID']]['COUNT_NAME']]?></p></td>
									<td width="120"><p><? echo $composition[$composition_arr[$row_req['PI_ID']]['YARN_COMPOSITION_ITEM1'] ];?></p></td>
									<td width="100"><p><?echo $yarn_type[$composition_arr[$row_req['PI_ID']]['YARN_TYPE']];?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['ORDER_QNTY'],2);
									$total_rcv_pi_qnty +=$result['ORDER_QNTY'];
									?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['ORDER_RATE'],2);
									$total_rcv_pi_rate +=$result['ORDER_RATE'];
									?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['ORDER_AMOUNT'],2);
									$total_rcv_pi_amount +=$result['ORDER_AMOUNT'];
									?></p></td>							
									<?
									if($span == 0)
									{
										?>
									<td rowspan="<?= $rowspan; ?>"  valign="middle" width="120" align="right"><p><? 
									echo number_format($total_rcv_pi_file,2);
									?></p></td>
									<?}?>
								</tr>
								<?
								$i++;
								$span++;
							}
								?>
									<tr>
									<td width="325" colspan ="3"><h3></h3></td>
									<td width="100" align="right"><p><h3>Total:</h3></p></td>
									<td width="100" align="right"><p><b><?echo number_format($total_rcv_pi_qnty,2);?></b></p></td>
									<td width="100" align="right"><p><b><?echo number_format($total_rcv_pi_rate,2);?></b></p></td>
									<td width="100" align="right"><p><b><?echo number_format($total_rcv_pi_amount,2);?></b></p></td>
									<td width="120" align="right"><p><b><?echo number_format($total_rcv_pi_file,2)?></b></p></td>
								</tr>
						</tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>


	<!--10--Yarn Issue With File : --> 
	<?

	// $yarn_type_sql_test = " SELECT A.YARN_COUNT_ID,A.YARN_TYPE,A.YARN_COMP_TYPE1ST,A.YARN_COMP_PERCENT1ST,A.YARN_COMP_TYPE2ND,A.YARN_COMP_PERCENT2ND,A.LOT, B.BTB_LC_ID,B.PROD_ID as PROD_ID, 
	// SUM(B.CONS_QUANTITY) AS CONS_QUANTITY, B.CONS_RATE, B.CONS_AMOUNT, B.REQUISITION_NO,F.ISSUE_NUMBER,F.ISSUE_PURPOSE ,F.ID
	// FROM PRODUCT_DETAILS_MASTER A,INV_ISSUE_MASTER F, INV_TRANSACTION B,COM_BTB_LC_MASTER_DETAILS  C, COM_BTB_EXPORT_LC_ATTACHMENT D,   COM_SALES_CONTRACT  E
	// WHERE A.ID = B.PROD_ID AND F.ID = B.MST_ID AND B.BTB_LC_ID = C.ID   AND C.ID = D.IMPORT_MST_ID
	// AND D.LC_SC_ID = E.ID   $yarn_com_cond $lc_file_cond
	// AND B.ITEM_CATEGORY = 1 AND B.TRANSACTION_TYPE = 2 AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND B.STATUS_ACTIVE = 1 
	// AND B.IS_DELETED = 0 
	// GROUP BY A.YARN_COUNT_ID,A.YARN_TYPE,A.YARN_COMP_TYPE1ST,A.YARN_COMP_PERCENT1ST,A.YARN_COMP_TYPE2ND,A.YARN_COMP_PERCENT2ND,A.LOT, B.BTB_LC_ID, B.PROD_ID,B.REQUISITION_NO, B.CONS_RATE, B.CONS_AMOUNT ,F.ISSUE_NUMBER,F.ISSUE_PURPOSE,F.ID ORDER BY F.ID ASC ";
	// //echo $yarn_type_sql_test;


	$yarn_issue = "SELECT A.ID as ISSUE_ID
	FROM  INV_ISSUE_MASTER   A,  INV_TRANSACTION  B,  COM_BTB_LC_MASTER_DETAILS  C, COM_BTB_EXPORT_LC_ATTACHMENT D, COM_SALES_CONTRACT  E
	WHERE A.ID = B.MST_ID  AND B.BTB_LC_ID = C.ID   AND C.ID = D.IMPORT_MST_ID
	AND D.LC_SC_ID = E.ID  $yarn_com_cond $lc_file_cond AND B.TRANSACTION_TYPE = 2 AND B.ITEM_CATEGORY = 1 
	AND A.STATUS_ACTIVE = 1  AND A.IS_DELETED = 0    AND B.STATUS_ACTIVE = 1
	AND B.IS_DELETED = 0 GROUP BY  A.ID";

	//echo $yarn_issue;
	$issue_sql_result = sql_select($yarn_issue);
	$issue_id_arr = array();
	foreach($issue_sql_result as $row_req)
	{
		//$issue_ids .= $row_req['ISSUE_ID'].',';
		$issue_id_arr[$row_req['ISSUE_ID']] = $row_req['ISSUE_ID'];
	}
	//$all_issue_id = ltrim(implode(",", array_unique(explode(",", chop($issue_ids, ",")))), ',');
	if(!empty($issue_id_arr))
	{
		fnc_tempengine("gbl_temp_engine", $user_id, 16, 7, $issue_id_arr, $empty_arr);
		$yarn_type_sql = " SELECT A.YARN_COUNT_ID,A.YARN_TYPE,A.YARN_COMP_TYPE1ST,A.YARN_COMP_PERCENT1ST,A.YARN_COMP_TYPE2ND,A.YARN_COMP_PERCENT2ND,A.LOT, B.BTB_LC_ID,B.PROD_ID as PROD_ID, 
		SUM(B.CONS_QUANTITY) AS CONS_QUANTITY, B.CONS_RATE, B.CONS_AMOUNT, B.REQUISITION_NO,F.ISSUE_NUMBER,F.ISSUE_PURPOSE ,F.ID
		FROM PRODUCT_DETAILS_MASTER A,INV_ISSUE_MASTER F, INV_TRANSACTION B , GBL_TEMP_ENGINE C
		WHERE A.ID = B.PROD_ID AND F.ID = B.MST_ID  $yarn_com_cond and F.ID = C.REF_VAL
		AND B.ITEM_CATEGORY = 1 AND B.TRANSACTION_TYPE = 2 AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND B.STATUS_ACTIVE = 1 
		AND B.IS_DELETED = 0 AND C.USER_ID=$user_id AND C.ENTRY_FORM=16 AND C.REF_FROM=7
		GROUP BY A.YARN_COUNT_ID,A.YARN_TYPE,A.YARN_COMP_TYPE1ST,A.YARN_COMP_PERCENT1ST,A.YARN_COMP_TYPE2ND,A.YARN_COMP_PERCENT2ND,A.LOT, B.BTB_LC_ID, B.PROD_ID,B.REQUISITION_NO, B.CONS_RATE, B.CONS_AMOUNT ,F.ISSUE_NUMBER,F.ISSUE_PURPOSE,F.ID ORDER BY F.ID ASC ";

		//echo $yarn_type_sql;

		$yarn_type_sql_result = sql_select($yarn_type_sql);
		$row_count = array();
		$req_id_arr = array();
		$span = 0;
		foreach($yarn_type_sql_result as $row_req)
		{

			$row_count[$row_req['FILE_NO']]++;
			//$req_ids .= $row_req['REQUISITION_NO'].',';
			$req_id_arr[$row_req['REQUISITION_NO']] = $row_req['REQUISITION_NO'];
			$issue_qnty += $row_req['CONS_QUANTITY'];
			$issue_rate += $row_req['CONS_RATE'];
			$issue_amnt += $row_req['CONS_AMOUNT'];
		}
		fnc_tempengine("gbl_temp_engine", $user_id, 16, 8, $req_id_arr, $empty_arr);

		//$all_req_ids = ltrim(implode(",", array_unique(explode(",", chop($req_ids, ",")))), ',');

		$order_file_sql = "SELECT A.REQUISITION_ID,A.ORDER_ID,B.FILE_NO 
		FROM PPL_YARN_REQUISITION_BREAKDOWN A,WO_PO_BREAK_DOWN B ,GBL_TEMP_ENGINE C
		WHERE A.ORDER_ID=B.ID AND A.REQUISITION_ID =  C.REF_VAL AND  A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND  B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0 AND C.USER_ID=$user_id AND C.ENTRY_FORM=16 AND C.REF_FROM=8";
		//echo $order_file_sql;

		$order_file_sql_result = sql_select($order_file_sql);
		$wo_id_arr = array();
		foreach($order_file_sql_result as $row_req)
		{
			$order_file[$row_req['REQUISITION_ID']]['FILE_NO'] = $row_req['FILE_NO'];

			$wo_id_arr[$row_req['ORDER_ID']] = $row_req['ORDER_ID'];

			//$wo_ids .= $row_req['ORDER_ID'].',';
			//$gen_acc_wo_ids .= "'".$row_req['ORDER_ID']."'".',';
		}
		//$all_wo_ids = ltrim(implode(",", array_unique(explode(",", chop($wo_ids, ",")))), ',');
		//$all_gen_acc_wo_idss = ltrim(implode(",", array_unique(explode(",", chop($gen_acc_wo_ids, ",")))), ',');

	}
	

	?>
	<div style="width:100%; margin-left:20px;" align="left">
		 <form name="requisitionApproval_1" id="requisitionApproval_1"> 
         <h3 style="width:1220px;margin-top:10px;" align="left" id="accordion_h10" class="accordion_h" onClick="accordion_menu(this.id,'content_search_yarn_issue_with_file','fnc_close(this.id)')">
			<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tr>
					<td width="665" ><h3>+ Yarn Issue With File </h3></td>
					<td width="100"><p><h3>Total:</h3></p></td>
					<td width="100" align="right"><p><?echo number_format($issue_qnty,2);?></td>
					<td width="100" align="right"><p><?echo number_format($issue_rate,2);?></td>
					<td width="100" align="right"><p><?echo number_format($issue_amnt,2);?></td>
					<td width="120" align="right"><p><?
					$total_issue_file = $issue_amnt - $file_value;
					if($total_issue_file >0)echo number_format($total_issue_file,2)?></p></td>
				</tr>
			</table></h3> 
         	<div id="content_search_yarn_issue_with_file">      
             	<fieldset style="width:1220px;">
                 	<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
					 	<thead>
							<tr>
								<th width="120"><p>Issue No</p> </th>
								<th width="120"><p>Req No</p> </th>
								<th width="100"><p>LOT</p> </th>
								<th width="120"><p>Order File No</p> </th>
								<th width="80"><p>Yarn Count</p></th>
								<th width="120"><p>Composition</p></th>
								<th width="100"><p>Issue Purpose</p></th>
								<th width="100"><p>Issue Qty</p></th>
								<th width="100"><p>Issue Rate</p></th>
								<th width="100"><p>Issue Amount</p></th>
								<th width="120"><p>File Balance</p></th>
							</tr>
						</thead>
						<tbody>
						<?
							$i =1;
							$span = 0;
							foreach($yarn_type_sql_result as $result)
							{
								$rowspan = $row_count[$result['FILE_NO']];
								if ($i % 2 == 0) $bgcolor = "#DFDFDF";
								else $bgcolor = "#FFFFFF";
								
								$composition_string = $composition[$result['YARN_COMP_TYPE1ST']] . " " . $result['YARN_COMP_PERCENT1ST'];
								if ($row['YARN_COMP_TYPE2ND'] != 0)
								$composition_string .= " " . $composition[$result['YARN_COMP_TYPE2ND']] . " " . $result['YARN_COMP_PERCENT2ND'];

								?>
								<tr bgcolor="<? echo $bgcolor; ?>">
									<td width="120"><p><?echo $result['ISSUE_NUMBER'];?></p></td>
									<td width="120"><p><?echo $result['REQUISITION_NO'];?></p></td> 
									<td width="100"><p><? echo $result['LOT']; ?></p></td>
									<td width="120"><p><? echo $order_file[$result['REQUISITION_NO']]['FILE_NO'] ?></p></td>
									<td width="80"><p><? echo $count_arr[$result['YARN_COUNT_ID']];?></p></td>
									<td width="120"><p><? echo $composition_string;?></p></td>
									<td width="100"><p><?  echo $yarn_issue_purpose[$result['ISSUE_PURPOSE']];?></p></td>
									<td width="100" align="right"><p><? echo number_format($result['CONS_QUANTITY'],2);
									$total_issue_qnty +=$result['CONS_QUANTITY'];
									?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['CONS_RATE'],2);
									$total_issue_rate +=$result['CONS_RATE'];
									?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['CONS_AMOUNT'],2);
									$total_issue_amount +=$result['CONS_AMOUNT'];
									?></p></td>							
									<?
									if($span == 0)
									{
										?>
									<td rowspan="<?= $rowspan; ?>"  valign="middle" width="120" align="right"><p><? 
									echo number_format($total_issue_file,2);
									?></p></td>
									<?}?>
								</tr>
								<?
								$i++;
								$span++;
							}
								?>
									<tr>
									<td width="665" colspan ="6"><h3></h3></td>
									<td width="100" align="right"><p><h3>Total:</h3></p></td>
									<td width="100" align="right"><p><b><?echo number_format($total_issue_qnty,2);?></b></p></td>
									<td width="100" align="right"><p><b><?echo number_format($total_issue_rate,2);?></b></p></td>
									<td width="100" align="right"><p><b><?echo number_format($total_issue_amount,2);?></b></p></td>
									<td width="120" align="right"><p><b><?echo number_format($total_issue_file,2)?></b></p></td>
								</tr>
						</tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>


	<!--09--Yarn Allocation With File start : --> 
	<?

	// $allocation_sql = "SELECT A.ID AS SID,A.ID AS ID,A.JOB_NO,A.BOOKING_NO,A.PO_BREAK_DOWN_ID,A.ITEM_ID,A.QNTY,A.ALLOCATION_DATE,A.BOOKING_WITHOUT_ORDER,B.COMPANY_NAME,B.BUYER_NAME,B. 
	// FROM INV_MATERIAL_ALLOCATION_MST A,WO_PO_DETAILS_MASTER B
	// WHERE  A.JOB_NO=B.JOB_NO  AND A.JOB_NO IN ('$job_no')  AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 
	// AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND (A.IS_DYIED_YARN!=1 OR A.IS_DYIED_YARN IS NULL) ";
	// //echo $allocation_sql;
	if(!empty($wo_id_arr))
	{
		fnc_tempengine("gbl_temp_engine", $user_id, 16, 9, $wo_id_arr, $empty_arr);
		$allocation_sql = "SELECT A.ID AS SID,A.ID AS ID,A.JOB_NO,A.BOOKING_NO,A.PO_BREAK_DOWN_ID,A.ITEM_ID,A.QNTY,C.FILE_NO
		FROM INV_MATERIAL_ALLOCATION_MST A,INV_MATERIAL_ALLOCATION_DTLS B,WO_PO_BREAK_DOWN C, GBL_TEMP_ENGINE D
		WHERE A.ID=B.MST_ID  AND B.PO_BREAK_DOWN_ID=C.ID AND B.PO_BREAK_DOWN_ID = D.REF_VAL AND A.ITEM_CATEGORY=1 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND (A.IS_DYIED_YARN!=1 OR A.IS_DYIED_YARN IS NULL) 
		AND D.USER_ID=$user_id AND D.ENTRY_FORM=16 AND D.REF_FROM=9
		ORDER BY A.ID ASC ";
		//echo $allocation_sql;


		$allocation_sql_result = sql_select($allocation_sql);
		$row_count = array();
		$span = 0;
		foreach($allocation_sql_result as $row_req)
		{
		$row_count[$row_req['FILE_NO']]++;
		$all_qnty += $row_req['QNTY'];
		}
	}
	
	
	$prod_data_arr = array();
	$prod_data = sql_select("SELECT ID, YARN_COUNT_ID,YARN_TYPE,YARN_COMP_TYPE1ST,YARN_COMP_PERCENT1ST,YARN_COMP_TYPE2ND,YARN_COMP_PERCENT2ND,LOT FROM PRODUCT_DETAILS_MASTER WHERE ITEM_CATEGORY_ID=1");
	foreach ($prod_data as $row) {
		$prod_data_arr[$row['ID']]['YARN_TYPE'] = $row['YARN_TYPE'];
		$prod_data_arr[$row['ID']]['YARN_COUNT_ID'] = $row['YARN_COUNT_ID'];
		$prod_data_arr[$row['ID']]['LOT'] = $row['LOT'];
		$prod_data_arr[$row['ID']]['YARN_COMP_TYPE1ST'] = $row['YARN_COMP_TYPE1ST'];
		$prod_data_arr[$row['ID']]['YARN_COMP_PERCENT1ST'] = $row['YARN_COMP_PERCENT1ST'];
		$prod_data_arr[$row['ID']]['YARN_COMP_TYPE2ND'] = $row['YARN_COMP_TYPE2ND'];
		$prod_data_arr[$row['ID']]['YARN_COMP_PERCENT2ND'] = $row['YARN_COMP_PERCENT2ND'];
	}

	?>
	<div style="width:100%; margin-left:20px;" align="left">
		 <form name="requisitionApproval_1" id="requisitionApproval_1"> 
         <h3 style="width:880px;margin-top:10px;" align="left" id="accordion_h9" class="accordion_h" onClick="accordion_menu(this.id,'content_search_yarn_allocation_with_file','fnc_close(this.id)')">
			<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tr>
					<td width="445" ><h3>+ Yarn Allocation With File </h3></td>
					<td width="100"><p><h3>Total:</h3></p></td>
					<td width="100" align="right"><p><?echo number_format($all_qnty,2);?></p></td>
					<td width="100" align="right"></td>
					<td width="100" align="right"></td>
					
				</tr>
			</table></h3> 
         	<div id="content_search_yarn_allocation_with_file">      
             	<fieldset style="width:880px;">
                 	<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
					 	<thead>
							<tr>
								<th width="120"><p>Booking No</p> </th>
								<th width="80"><p>Yarn Count</p></th>
								<th width="120"><p>Composition</p></th>
								<th width="100"><p>Issue Type</p></th>
								<th width="120"><p>Lot</p></th>
								<th width="100"><p>Allocation Qty</p></th>
								<th width="100"><p>File No</p></th>
								<th width="100"><p>Another File No</p></th>
								
							</tr>
						</thead>
						<tbody>
						<?
							$i =1;
							$span = 0;
							foreach($allocation_sql_result as $result)
							{
								$rowspan = $row_count[$result['FILE_NO']];
								if ($i % 2 == 0) $bgcolor = "#DFDFDF";
								else $bgcolor = "#FFFFFF";

								$composition_string = $composition[$prod_data_arr[$result['ITEM_ID']]['YARN_COMP_TYPE1ST']] . " " . $prod_data_arr[$result['ITEM_ID']]['YARN_COMP_PERCENT1ST'];
								if ($prod_data_arr[$result['ITEM_ID']['YARN_COMP_TYPE2ND']] != 0)
								$composition_string .= " " . $composition[$prod_data_arr[$result['ITEM_ID']]['YARN_COMP_TYPE2ND']] . " " . $prod_data_arr[$result['ITEM_ID']]['YARN_COMP_PERCENT2ND'];;

								?>
								<tr bgcolor="<? echo $bgcolor; ?>">
									<td width="120"><p><?echo $result['BOOKING_NO'];?></p></td>
									<td width="80"><p><? echo $count_arr[$prod_data_arr[$result['ITEM_ID']]['YARN_COUNT_ID']]?></p></td>
									<td width="120"><p><? echo $composition_string;?></p></td>
									<td width="100"><p><? echo $yarn_type[$prod_data_arr[$result['ITEM_ID']]['YARN_COUNT_ID']];?></p></td>
									<td width="100"><p><? echo $prod_data_arr[$result['ITEM_ID']]['LOT'];?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['QNTY'],2);
									$total_all_qnty +=$result['QNTY'];?></p></td>
									<td width="100" align="right"><p><?echo $result['FILE_NO'];?></p></td>
									<td width="100" align="right"><p><? ?></p></td>							
									
								</tr>
								<?
								$i++;
								$span++;
							}
								?>
									<tr>
									<td width="445" colspan ="4"><h3></h3></td>
									<td width="100" align="right"><p><h3>Total:</h3></p></td>
									<td width="100" align="right"><p><b><?echo number_format($total_all_qnty,2);?></b></p></td>
									<td width="100" align="right"><p><b></b></p></td>
									<td width="100" align="right"><p><b></b></p></td>
									<!-- <td width="120" align="right"><p><b></b></p></td> -->
								</tr>
						</tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>

	
	<!--11--General Accessories Issue With File start : --> 
	<?

	$gen_issue_sql = "SELECT A.ISSUE_QNTY AS QTY,A.RATE AS RATE,A.AMOUNT AS AMOUNT,A.ITEM_GROUP_ID AS TRIMS_GRP
	FROM INV_TRIMS_ISSUE_DTLS A,  GBL_TEMP_ENGINE B
	WHERE A.ORDER_ID = CAST(B.REF_VAL AS NVARCHAR2 (500)) AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.USER_ID=$user_id AND B.ENTRY_FORM=16 AND B.REF_FROM=9
	UNION ALL
	SELECT A.CONS_QUANTITY AS QTY, A.CONS_RATE AS RATE, A.CONS_AMOUNT AS AMOUNT , 0 AS TRIMS_GRP  FROM INV_TRANSACTION A ,GBL_TEMP_ENGINE B 
	WHERE A.ITEM_CATEGORY = 4 AND ORDER_ID = B.REF_VAL AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.USER_ID=$user_id AND B.ENTRY_FORM=16 AND B.REF_FROM=9";
	
	//echo $gen_issue_sql;
	
	$gen_issue_sql_result = sql_select($gen_issue_sql);
	$row_count = array();
	$span = 0;
	foreach($gen_issue_sql_result as $row_req)
	{
	$row_count[$row_req['FILE_NO']]++;
	$gen_acc_qnty += $row_req['QTY'];
	$gen_acc_rate += $row_req['RATE'];
	$gen_acc_amnt += $row_req['AMOUNT'];
	}
	
	?>
	<div style="width:100%; margin-left:20px;" align="left">
		 <form name="requisitionApproval_1" id="requisitionApproval_1"> 
         <h3 style="width:780px;margin-top:10px;" align="left" id="accordion_h11" class="accordion_h" onClick="accordion_menu(this.id,'content_search_general_accessories_issue_with_file','fnc_close(this.id)')">
			<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tr>
					<td width="245"><h3>+ General Accessories Issue With File </h3></td>
					<td width="100" align="right"><p><?echo number_format($gen_acc_qnty,2);?></td>
					<td width="100" align="right"><p><?echo number_format($gen_acc_rate,2);?></td>
					<td width="100" align="right"><p><?echo number_format($gen_acc_amnt,2);?></td>
					<td width="120" align="right"><p><?
					$total_gen_acc_file = $gen_acc_amnt - $file_value;
					if($total_gen_acc_file >0)echo number_format($total_gen_acc_file,2)?></p></td>
				</tr>
			</table></h3> 
         	<div id="content_search_general_accessories_issue_with_file">      
             	<fieldset style="width:780px;">
                 	<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
					 	<thead>
							<tr>
								<th width="120"><p>Issue No</p> </th>
								<th width="120"><p>Trims Group</p></th>
								<th width="100"><p>Issue Qty</p></th>
								<th width="100"><p>Issue Rate</p></th>
								<th width="100"><p>Issue Amount</p></th>
								<th width="120"><p>File Balance</p></th>
							</tr>
						</thead>
						<tbody>
						<?
							$i =1;
							$span = 0;
							foreach($gen_issue_sql_result as $result)
							{
								$rowspan = $row_count[$result['FILE_NO']];
								if ($i % 2 == 0) $bgcolor = "#DFDFDF";
								else $bgcolor = "#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>">
									<td width="120"><p><?echo $result['PI_NUMBER'];?></p></td>
									<td width="120"><p><? echo $itemArr[$result['TRIMS_GRP']];?></p></td>
									
									<td width="100" align="right"><p><?echo number_format($result['QTY'],2);
									$total_gen_acc_qnty +=$result['QTY'];
									?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['RATE'],2);
									$total_gen_acc_rate +=$result['RATE'];
									?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['AMOUNT'],2);
									$total_gen_acc_amount +=$result['AMOUNT'];
									?></p></td>							
									<?
									if($span == 0)
									{
										?>
									<td rowspan="<?= $rowspan; ?>"  valign="middle" width="120" align="right"><p><? 
									echo number_format($total_gen_acc_file,2);
									?></p></td>
									<?}?>
								</tr>
								<?
								$i++;
								$span++;
							}
								?>
									<tr>
									<td width="145" colspan ="1"><h3></h3></td>
									<td width="100" align="right"><p><h3>Total:</h3></p></td>
									<td width="100" align="right"><p><b><?echo number_format($total_gen_acc_qnty,2);?></b></p></td>
									<td width="100" align="right"><p><b><?echo number_format($total_gen_acc_rate,2);?></b></p></td>
									<td width="100" align="right"><p><b><?echo number_format($total_gen_acc_amount,2);?></b></p></td>
									<td width="120" align="right"><p><b><?echo number_format($total_gen_acc_file,2)?></b></p></td>
								</tr>
						</tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>


	<!--22--General Accessories Receive With File start : --> 
	<?

	$gen_rcv_sql = "SELECT A.RECEIVE_QNTY AS QTY,A.RATE AS RATE,A.AMOUNT AS AMOUNT,A.ITEM_GROUP_ID AS TRIMS_GRP 
	FROM INV_TRIMS_ENTRY_DTLS A, GBL_TEMP_ENGINE B
	WHERE A.ORDER_ID  = CAST(B.REF_VAL AS NVARCHAR2 (500)) AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.USER_ID=$user_id AND B.ENTRY_FORM=16 AND B.REF_FROM=9
	UNION ALL
	SELECT A.CONS_QUANTITY AS QTY, A.CONS_RATE AS RATE, A.CONS_AMOUNT AS AMOUNT , 0 AS TRIMS_GRP 
	FROM INV_TRANSACTION A ,GBL_TEMP_ENGINE B
	WHERE A.ITEM_CATEGORY = 4 AND ORDER_ID = B.REF_VAL AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.USER_ID=$user_id AND B.ENTRY_FORM=16 AND B.REF_FROM=9";


	//echo $gen_rcv_sql;

	$gen_rcv_sql_result = sql_select($gen_rcv_sql);
	$row_count = array();
	$span = 0;
	foreach($gen_rcv_sql_result as $row_req)
	{
	$row_count[$row_req['FILE_NO']]++;
	$gen_rcv_qnty += $row_req['QTY'];
	$gen_rcv_rate += $row_req['RATE'];
	$gen_rcv_amnt += $row_req['AMOUNT'];
	}
	

	?>
	<div style="width:100%; margin-left:20px;" align="left">
		 <form name="requisitionApproval_1" id="requisitionApproval_1"> 
         <h3 style="width:780px;margin-top:10px;" align="left" id="accordion_h22" class="accordion_h" onClick="accordion_menu(this.id,'content_search_general_accessories_receive_with_file','fnc_close(this.id)')">
			<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tr>
					<td width="245" ><h3>+ General Accessories Receive With File </h3></td>
					<td width="100" align="right"><p><?echo number_format($gen_rcv_qnty,2);?></td>
					<td width="100" align="right"><p><?echo number_format($gen_rcv_rate,2);?></td>
					<td width="100" align="right"><p><?echo number_format($gen_rcv_amnt,2);?></td>
					<td width="120" align="right"><p><?
					$total_gen_rcv_file = $gen_rcv_amnt - $file_value;
					if($total_gen_rcv_file >0)echo number_format($total_gen_rcv_file,2)?></p></td>
				</tr>
			</table></h3> 
         	<div id="content_search_general_accessories_receive_with_file">      
             	<fieldset style="width:780px;">
                 	<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
					 	<thead>
							<tr>
								<th width="120"><p>Issue No</p> </th>
								<th width="120"><p>Trims Group</p></th>
								<th width="100"><p>Issue Qty</p></th>
								<th width="100"><p>Issue Rate</p></th>
								<th width="100"><p>Issue Amount</p></th>
								<th width="120"><p>File Balance</p></th>
							</tr>
						</thead>
						<tbody>
						<?
							$i =1;
							$span = 0;
							foreach($gen_rcv_sql_result as $result)
							{
								$rowspan = $row_count[$result['FILE_NO']];
								if ($i % 2 == 0) $bgcolor = "#DFDFDF";
								else $bgcolor = "#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>">
									<td width="120"><p><?//echo $result[''];?></p></td>
									<td width="120"><p><? echo $itemArr[$result['TRIMS_GRP']];?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['QTY'],2);
									$total_gen_rcv_qnty +=$result['QTY'];
									?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['RATE'],2);
									$total_gen_rcv_rate +=$result['RATE'];
									?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['AMOUNT'],2);
									$total_gen_rcv_amount +=$result['AMOUNT'];
									?></p></td>							
									<?
									if($span == 0)
									{
										?>
									<td rowspan="<?= $rowspan; ?>"  valign="middle" width="120" align="right"><p><? 
									echo number_format($total_gen_rcv_file,2);
									?></p></td>
									<?}?>
								</tr>
								<?
								$i++;
								$span++;
							}
								?>
									<tr>
									<td width="145"><h3></h3></td>
									<td width="100" align="right"><p><h3>Total:</h3></p></td>
									<td width="100" align="right"><p><b><?echo number_format($total_gen_rcv_qnty,2);?></b></p></td>
									<td width="100" align="right"><p><b><?echo number_format($total_gen_rcv_rate,2);?></b></p></td>
									<td width="100" align="right"><p><b><?echo number_format($total_gen_rcv_amount,2);?></b></p></td>
									<td width="120" align="right"><p><b><?echo number_format($total_gen_rcv_file,2)?></b></p></td>
								</tr>
						</tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>



	<!--12--Yarn Transfer-In from Another File : --> 
	<?

	// $transfer_in_sql = "SELECT e.ID as DTLS_ID,b.FILE_NO, e.QUANTITY ,e.COUNT_NAME,e.YARN_COMPOSITION_ITEM1,e.YARN_TYPE
	// ,e.NET_PI_RATE,e.NET_PI_AMOUNT,f.APPROVED,f.PI_NUMBER,f.ID as PI_ID
	// from wo_po_details_master a , wo_po_break_down b ,
	// COM_PI_ITEM_DETAILS e,COM_PI_MASTER_DETAILS f
	// where  a.STATUS_ACTIVE=1 and a.JOB_NO = b.JOB_NO_MST AND b.ID = e.ORDER_ID AND  e.PI_ID = f.ID AND e.ORDER_ID in ($all_po_id) and e.ITEM_CATEGORY_ID=4
	// and e.status_active=1 and e.is_deleted=0  and f.status_active=1 and f.is_deleted=0
	// ORDER by e.ID ASC";
	//echo $accessories_pi_sql;

	$transfer_in_sql_result = sql_select($transfer_in_sql);
	$row_count = array();
	$span = 0;
	foreach($transfer_in_sql_result as $row_req)
	{

	$acc_pi_qnty += $row_req['QUANTITY'];
	$acc_pi_rate += $row_req['NET_PI_RATE'];
	$acc_pi_amnt += $row_req['NET_PI_AMOUNT'];
	}
	

	?>
	<div style="width:100%; margin-left:20px;" align="left">
		 <form name="requisitionApproval_1" id="requisitionApproval_1"> 
         <h3 style="width:880px;margin-top:10px;" align="left" id="accordion_h12" class="accordion_h" onClick="accordion_menu(this.id,'content_search_yarn_transfer_from_another_file','fnc_close(this.id)')">
			<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tr>
					<td width="325" ><h3>+ Yarn Transfer In from Another File </h3></td>
					<td width="100"><p><h3>Total:</h3></p></td>
					<td width="100" align="right"><p><?//echo number_format($acc_pi_qnty,2);?></td>
					<td width="100" align="right"><p><?//echo number_format($acc_pi_rate,2);?></td>
					<td width="100" align="right"><p><?//echo number_format($acc_pi_amnt,2);?></td>
					<td width="120" align="right"><p><?
					$total_acc_pi_file = $acc_pi_amnt - $file_value;
					if($total_acc_pi_file >0)//echo number_format($total_acc_pi_file,2)?></p></td>
				</tr>
			</table></h3> 
         	<div id="content_search_yarn_transfer_from_another_file">      
             	<fieldset style="width:880px;">
                 	<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
					 	<thead>
							<tr>
								<th width="120"><p>Booking No</p> </th>
								<th width="80"><p>Yarn Count</p></th>
								<th width="120"><p>Composition</p></th>
								<th width="100"><p>Issue Type</p></th>
								<th width="100"><p>Transfer Qty</p></th>
								<th width="100"><p>Transfer Rate</p></th>
								<th width="100"><p>Transfer Amount</p></th>
								<th width="120"><p>File Balance</p></th>
							</tr>
						</thead>
						<tbody>
						<?
							$i =1;
							$span = 0;
							foreach($transfer_in_sql_result as $result)
							{
								$rowspan = $row_count[$result['FILE_NO']];
								if ($i % 2 == 0) $bgcolor = "#DFDFDF";
								else $bgcolor = "#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>">
									<td width="120"><p><?echo $result['PI_NUMBER'];?></p></td>
									<td width="80"><p><?echo $count_arr[$result['COUNT_NAME']]?></p></td>
									<td width="120"><p><? echo $composition[$result['YARN_COMPOSITION_ITEM1']];?></p></td>
									<td width="100"><p><?echo $yarn_type[$result['YARN_TYPE']];?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['QUANTITY'],2);
									$total_acc_pi_qnty +=$result['QUANTITY'];
									?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['NET_PI_RATE'],2);
									$total_acc_pi_rate +=$result['NET_PI_RATE'];
									?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['NET_PI_AMOUNT'],2);
									$total_acc_pi_amount +=$result['NET_PI_AMOUNT'];
									?></p></td>							
									<?
									if($span == 0)
									{
										?>
									<td rowspan="<?= $rowspan; ?>"  valign="middle" width="120" align="right"><p><? 
									echo number_format($total_acc_pi_file,2);
									?></p></td>
									<?}?>
								</tr>
								<?
								$i++;
								$span++;
							}
								?>
									<tr>
									<td width="325" colspan ="3"><h3></h3></td>
									<td width="100" align="right"><p><h3>Total:</h3></p></td>
									<td width="100" align="right"><p><b><?//echo number_format($total_acc_pi_qnty,2);?></b></p></td>
									<td width="100" align="right"><p><b><?//echo number_format($total_acc_pi_rate,2);?></b></p></td>
									<td width="100" align="right"><p><b><?//echo number_format($total_acc_pi_amount,2);?></b></p></td>
									<td width="120" align="right"><p><b><?//echo number_format($total_acc_pi_file,2)?></b></p></td>
								</tr>
						</tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>

	
	<!--13--Yarn Transfer Out With Another File : --> 
	<?

	// $transfer_out_sql = "SELECT e.ID as DTLS_ID,b.FILE_NO, e.QUANTITY ,e.COUNT_NAME,e.YARN_COMPOSITION_ITEM1,e.YARN_TYPE
	// ,e.NET_PI_RATE,e.NET_PI_AMOUNT,f.APPROVED,f.PI_NUMBER,f.ID as PI_ID
	// from wo_po_details_master a , wo_po_break_down b ,
	// COM_PI_ITEM_DETAILS e,COM_PI_MASTER_DETAILS f
	// where  a.STATUS_ACTIVE=1 and a.JOB_NO = b.JOB_NO_MST AND b.ID = e.ORDER_ID AND  e.PI_ID = f.ID AND e.ORDER_ID in ($all_po_id) and e.ITEM_CATEGORY_ID=4
	// and e.status_active=1 and e.is_deleted=0  and f.status_active=1 and f.is_deleted=0
	// ORDER by e.ID ASC";
	//echo $transfer_out_sql;

	$transfer_out_sql_result = sql_select($transfer_out_sql);
	$row_count = array();
	$span = 0;
	foreach($transfer_out_sql_result as $row_req)
	{
	$row_count[$row_req['FILE_NO']]++;
	$acc_pi_qnty += $row_req['QUANTITY'];
	$acc_pi_rate += $row_req['NET_PI_RATE'];
	$acc_pi_amnt += $row_req['NET_PI_AMOUNT'];
	}
	


	?>
	<div style="width:100%; margin-left:20px;" align="left">
		 <form name="requisitionApproval_1" id="requisitionApproval_1"> 
         <h3 style="width:880px;margin-top:10px;" align="left" id="accordion_h13" class="accordion_h" onClick="accordion_menu(this.id,'content_search_yarn_transfer_out_with_another_file','fnc_close(this.id)')">
			<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tr>
					<td width="325" ><h3>+ Yarn Transfer Out With Another File </h3></td>
					<td width="100"><p><h3>Total:</h3></p></td>
					<td width="100" align="right"><p><?//echo number_format($acc_pi_qnty,2);?></td>
					<td width="100" align="right"><p><?//echo number_format($acc_pi_rate,2);?></td>
					<td width="100" align="right"><p><?//echo number_format($acc_pi_amnt,2);?></td>
					<td width="120" align="right"><p><?
					$total_acc_pi_file = $acc_pi_amnt - $file_value;
					if($total_acc_pi_file >0)//echo number_format($total_acc_pi_file,2)?></p></td>
				</tr>
			</table></h3> 
         	<div id="content_search_yarn_transfer_out_with_another_file">      
             	<fieldset style="width:880px;">
                 	<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
					 	<thead>
							<tr>
								<th width="120"><p>Booking No</p> </th>
								<th width="80"><p>Yarn Count</p></th>
								<th width="120"><p>Composition</p></th>
								<th width="100"><p>Yarn Type</p></th>
								<th width="100"><p>Transfer Qty</p></th>
								<th width="100"><p>Transfer Rate</p></th>
								<th width="100"><p>Transfer Amount</p></th>
								<th width="120"><p>File Balance</p></th>
							</tr>
						</thead>
						<tbody>
						<?
							$i =1;
							$span = 0;
							foreach($transfer_out_sql_result as $result)
							{
								$rowspan = $row_count[$result['FILE_NO']];
								if ($i % 2 == 0) $bgcolor = "#DFDFDF";
								else $bgcolor = "#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>">
									<td width="120"><p><?echo $result['PI_NUMBER'];?></p></td>
									<td width="80"><p><?echo $count_arr[$result['COUNT_NAME']]?></p></td>
									<td width="120"><p><? echo $composition[$result['YARN_COMPOSITION_ITEM1']];?></p></td>
									<td width="100"><p><?echo $yarn_type[$result['YARN_TYPE']];?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['QUANTITY'],2);
									$total_acc_pi_qnty +=$result['QUANTITY'];
									?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['NET_PI_RATE'],2);
									$total_acc_pi_rate +=$result['NET_PI_RATE'];
									?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['NET_PI_AMOUNT'],2);
									$total_acc_pi_amount +=$result['NET_PI_AMOUNT'];
									?></p></td>							
									<?
									if($span == 0)
									{
										?>
									<td rowspan="<?= $rowspan; ?>"  valign="middle" width="120" align="right"><p><? 
									echo number_format($total_acc_pi_file,2);
									?></p></td>
									<?}?>
								</tr>
								<?
								$i++;
								$span++;
							}
								?>
									<tr>
									<td width="325" colspan ="3"><h3></h3></td>
									<td width="100" align="right"><p><h3>Total:</h3></p></td>
									<td width="100" align="right"><p><b><?//echo number_format($total_acc_pi_qnty,2);?></b></p></td>
									<td width="100" align="right"><p><b><?//echo number_format($total_acc_pi_rate,2);?></b></p></td>
									<td width="100" align="right"><p><b><?//echo number_format($total_acc_pi_amount,2);?></b></p></td>
									<td width="120" align="right"><p><b><?//echo number_format($total_acc_pi_file,2)?></b></p></td>
								</tr>
						</tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>

    <!--14--Dyes & Chemical Consumption Cost Start : --> 
	<?

	// $dys_chm_sql = "SELECT B.FILE_NO ,B.PO_NUMBER,C.BATCH_NO
	// FROM WO_PO_DETAILS_MASTER A , WO_PO_BREAK_DOWN B ,PRO_BATCH_CREATE_MST C, PRO_BATCH_CREATE_DTLS D,
	// INV_TRANSACTION E,PRODUCT_DETAILS_MASTER F
	// WHERE A.STATUS_ACTIVE=1 $company_name_cond $buyer_name_cond $file_cond AND A.ID = B.JOB_ID 
	// AND B.ID = D.PO_ID AND D.MST_ID = C.ID AND CAST(C.ID AS VARCHAR2 (100))  = E.BATCH_ID AND E.PROD_ID = F.ID AND E.ITEM_CATEGORY IN (5,6,7,23)AND E.TRANSACTION_TYPE IN(2)  AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 ";

	$dys_chm_sql = "SELECT C.ID AS BATCH_ID, C.BATCH_NO , E.TRANS_ID, F.CONS_QUANTITY, F.CONS_RATE, F.CONS_AMOUNT,G.LOT,G.ITEM_DESCRIPTION
	FROM  GBL_TEMP_ENGINE A,  PRO_BATCH_CREATE_MST C, PRO_BATCH_CREATE_DTLS D,DYES_CHEM_ISSUE_DTLS E,INV_TRANSACTION F,PRODUCT_DETAILS_MASTER G
	WHERE  A.REF_VAL  = D.PO_ID AND  C.ID =D.MST_ID AND CAST(C.ID AS VARCHAR2 (100)) = E.BATCH_ID 
	AND E.TRANS_ID = F.ID AND F.PROD_ID = G.ID  AND  F.ITEM_CATEGORY IN (5,6,7,23)AND F.TRANSACTION_TYPE IN(2) AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND A.USER_ID= $user_id AND A.ENTRY_FORM=16 AND A.REF_FROM=1 
	GROUP BY C.ID, C.BATCH_NO , E.TRANS_ID,F.CONS_QUANTITY, F.CONS_RATE, F.CONS_AMOUNT,G.LOT,G.ITEM_DESCRIPTION ";
	
	//echo $dys_chm_sql;

	$dys_chm_sql_result = sql_select($dys_chm_sql);
	$row_count = array();
	$batch_id_arr = array();
	$span = 0;
	foreach($dys_chm_sql_result as $row_req)
	{
	$row_count[$row_req['FILE_NO']]++;
	$dys_trn_qnty += $row_req['CONS_QUANTITY'];
	$dys_trn_rate += $row_req['CONS_RATE'];
	$dys_trn_amnt += $row_req['CONS_AMOUNT'];
	$batch_id_arr[$row_req['BATCH_ID']] = $row_req['BATCH_ID'];
	}

	if(!empty($batch_id_arr))
	{
		fnc_tempengine("gbl_temp_engine", $user_id, 16, 10, $batch_id_arr, $empty_arr);

		$recipe_sql = "SELECT A.ID AS RECIPE_NO, A.BATCH_ID FROM PRO_RECIPE_ENTRY_MST A, GBL_TEMP_ENGINE B 
		WHERE A.BATCH_ID = B.REF_VAL AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0  AND B.USER_ID= $user_id AND B.ENTRY_FORM=16 AND B.REF_FROM=10";
		//echo $recipe_sql;
		$recipe_sql_result = sql_select($recipe_sql);
		
		foreach($recipe_sql_result as $row_req)
		{
			$recipenoArr[$row_req['BATCH_ID']]['RECIPE_NO'] = $row_req['RECIPE_NO'];
		}
	
	}


	?>
	<div style="width:100%; margin-left:20px;" align="left">
		 <form name="requisitionApproval_1" id="requisitionApproval_1"> 
         <h3 style="width:880px;margin-top:10px;" align="left" id="accordion_h14" class="accordion_h" onClick="accordion_menu(this.id,'content_search_dyes_chemical_consumption_cost','fnc_close(this.id)')">
			<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tr>
					<td width="325" ><h3>+ Dyes & Chemical Consumption Cost </h3></td>
					<td width="100"><p><h3>Total:</h3></p></td>
					<td width="100" align="right"><p><?echo number_format($dys_trn_qnty,2);?></td>
					<td width="100" align="right"><p><?echo number_format($dys_trn_rate,2);?></td>
					<td width="100" align="right"><p><?echo number_format($dys_trn_amnt,2);?></td>
					<td width="120" align="right"><p><?
					$total_dys_trn_amnt = $dys_trn_amnt - $file_value;
					if($total_dys_trn_amnt >0)echo number_format($total_dys_trn_amnt,2)?></p></td>
				</tr>
			</table></h3> 
         	<div id="content_search_dyes_chemical_consumption_cost">      
             	<fieldset style="width:880px;">
                 	<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
					 	<thead>
							<tr>
								<th width="120"><p>Batch No</p> </th>
								<th width="120"><p>Items Des</p></th>
								<th width="80"><p>Lot</p></th>
								<th width="100"><p>Recipie No</p></th>
								<th width="100"><p>Consumption Qty</p></th>
								<th width="100"><p>Rate</p></th>
								<th width="100"><p>Amount</p></th>
								<th width="120"><p>File Balance</p></th>
							</tr>
						</thead>
						<tbody>
						<?
							$i =1;
							$span = 0;
							foreach($dys_chm_sql_result as $result)
							{
								$rowspan = $row_count[$result['FILE_NO']];
								if ($i % 2 == 0) $bgcolor = "#DFDFDF";
								else $bgcolor = "#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>">
									<td width="120"><p><?echo $result['BATCH_NO'];?></p></td>
									<td width="120"><p><?echo $result['ITEM_DESCRIPTION']?></p></td>
									<td width="80"><p><? echo $result['LOT'];?></p></td>
									<td width="100"><p><? echo $recipenoArr[$result['BATCH_ID']]['RECIPE_NO'];?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['CONS_QUANTITY'],2);
									$total_trn_qnty +=$result['CONS_QUANTITY'];
									?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['CONS_RATE'],2);
									$total_trn_rate +=$result['CONS_RATE'];
									?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['CONS_AMOUNT'],2);
									$total_trn_amnt +=$result['CONS_AMOUNT'];
									?></p></td>							
									<?
									if($span == 0)
									{
										?>
									<td rowspan="<?= $rowspan; ?>"  valign="middle" width="120" align="right"><p><? 
									echo number_format($total_dys_trn_amnt,2);
									?></p></td>
									<?}?>
								</tr>
								<?
								$i++;
								$span++;
							}
								?>
									<tr>
									<td width="325" colspan ="3"><h3></h3></td>
									<td width="100" align="right"><p><h3>Total:</h3></p></td>
									<td width="100" align="right"><p><b><?echo number_format($total_trn_qnty,2);?></b></p></td>
									<td width="100" align="right"><p><b><?echo number_format($total_trn_rate,2);?></b></p></td>
									<td width="100" align="right"><p><b><?echo number_format($total_trn_amnt,2);?></b></p></td>
									<td width="120" align="right"><p><b><?echo number_format($total_dys_trn_amnt,2)?></b></p></td>
								</tr>
						</tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>

	
    <!--15--File Wise Service Cost Start : --> 
	<?
	
	// */

	if(!empty($job_id_arr))
	{
		$file_wise_service_sql = " SELECT A.JOB_NO AS JOB_NO,0 AS TYPE, SUM(A.YARN_WO_QTY) AS QTY ,SUM(A.AMOUNT) AS AMNT 
		FROM GBL_TEMP_ENGINE G , WO_PO_DETAILS_MASTER W ,WO_YARN_DYEING_DTLS A
		WHERE W.ID = G.REF_VAL AND A.JOB_NO_ID = W.ID AND A.STATUS_ACTIVE =1 AND A.IS_DELETED =0 AND G.USER_ID= 1 AND G.ENTRY_FORM=16 AND G.REF_FROM=11 
		GROUP BY A.JOB_NO
		UNION ALL
		SELECT A.JOB_NO AS JOB_NO,A.BOOKING_TYPE AS TYPE ,SUM(A.WO_QNTY) AS QNTY,SUM(A.AMOUNT) AS AMNT 
		FROM GBL_TEMP_ENGINE G , WO_PO_DETAILS_MASTER W ,WO_BOOKING_DTLS A
		WHERE W.ID = G.REF_VAL AND A.JOB_NO = W.JOB_NO AND A.BOOKING_TYPE =3  AND  A.STATUS_ACTIVE =1 AND A.IS_DELETED =0 AND G.USER_ID= 1 AND G.ENTRY_FORM=16 AND G.REF_FROM=11 
		GROUP BY A.BOOKING_TYPE,A.JOB_NO";
		
		
		//echo $file_wise_service_sql;

		$file_wise_service_sql_result = sql_select($file_wise_service_sql);
		$row_count = array();
		$service_arr = array();
		$span = 0;
		foreach($file_wise_service_sql_result as $row)
		{
		$row_count[$row_req['FILE_NO']]++;
		$service_arr[$row['JOB_NO']][$row['TYPE']]['QTY'] += $row['QTY'];
		$service_arr[$row['JOB_NO']][$row['TYPE']]['AMNT'] += $row['AMNT'];

		}

	}

	?>
	<div style="width:100%; margin-left:20px;" align="left">
		 <form name="requisitionApproval_1" id="requisitionApproval_1"> 
         <h3 style="width:900px;margin-top:10px;" align="left" id="accordion_h15" class="accordion_h" onClick="accordion_menu(this.id,'content_search_file_wise_service_cost','fnc_close(this.id)')">
			<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tr>
					<td width="345" ><h3>+ File Wise Service Cost </h3></td>
					<td width="100"><p></p></td>
					<td width="100" align="right"><p><?//echo number_format($acc_pi_qnty,2);?></td>
					<td width="100" align="right"><p><?//echo number_format($acc_pi_rate,2);?></td>
					<td width="100" align="right"><p><?//echo number_format($acc_pi_amnt,2);?></td>
					<td width="120" align="right"><p><?
					$total_acc_pi_file = $acc_pi_amnt - $file_value;
					if($total_acc_pi_file >0)//echo number_format($total_acc_pi_file,2)?></p></td>
				</tr>
			</table></h3> 
         	<div id="content_search_file_wise_service_cost">      
             	<fieldset style="width:900px;">
                 	<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
					 	<thead>
							<tr>
								<th colspan="2" width="120"><p>YD</p> </th>
								<th colspan="2" width="100"><p>AOP</p></th>
								<th colspan="2" width="120"><p>Knitting</p></th>
								<th colspan="2" width="100"><p>Others</p></th>
								<th colspan="2" width="100"><p>Dyeing</p></th>
								<th colspan="2" width="100"><p>Wash</p></th>
								<th colspan="2" width="100"><p>Lab Test</p></th>
								<th width="120"><p>File Balance</p></th>
							</tr>
							<tr>
								<th width="50">Yarn Qty</th>
								<th width="70">Amount</th>

								<th width="40">AOP Qty</th>
								<th width="60">Amount</th>

								<th width="50"> Qty</th>
								<th width="70">Amount</th>

								<th width="40">Qty</th>
								<th width="60">Amount</th>

								<th width="40">Qty</th>
								<th width="60">Amount</th>

								<th width="40">Qty</th>
								<th width="60">Amount</th>

								<th width="40">Qty</th>
								<th width="60">Amount</th>

								<th width="120"></th>
							</tr>
						</thead>
						<tbody>
						<?
							$i =1;
							$span = 0;
							foreach($file_wise_service_sql_result as $row)
							{
								$rowspan = $row_count[$result['FILE_NO']];
								if ($i % 2 == 0) $bgcolor = "#DFDFDF";
								else $bgcolor = "#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>">
									<td width="50" align="right"><p><?echo number_format($service_arr[$row['JOB_NO']][0]['QTY'],2) ;?></p></td>
									<td width="70" align="right"><p><?echo number_format($service_arr[$row['JOB_NO']][0]['AMNT'],2) ;?></p></td>
									<td width="40" align="right"><p><?echo number_format($service_arr[$row['JOB_NO']][3]['QTY'],2) ;?></p></td>
									<td width="60" align="right"><p><?echo number_format($service_arr[$row['JOB_NO']][3]['AMNT'],2) ;?></p></td>

									<td width="50"><p><?//echo number_format($service_arr[$row['JOB_NO']][0]['QTY']) ;?></p></td>
									<td width="70"><p><?//echo number_format($service_arr[$row['JOB_NO']][0]['AMNT']) ;?></p></td>

									<td width="40"><p><?//echo number_format($service_arr[$row['JOB_NO']][3]['QTY']) ;?></p></td>
									<td width="60"><p><?//echo number_format($service_arr[$row['JOB_NO']][3]['AMNT']) ;?></p></td>

									<td width="40"><p><?//echo number_format($service_arr[$row['JOB_NO']][3]['QTY']) ;?></p></td>
									<td width="60"><p><?//echo number_format($service_arr[$row['JOB_NO']][3]['AMNT']) ;?></p></td>

									<td width="40"><p><?//echo number_format($service_arr[$row['JOB_NO']][3]['QTY']) ;?></p></td>
									<td width="60"><p><?//echo number_format($service_arr[$row['JOB_NO']][3]['AMNT']) ;?></p></td>

									<td width="40"><p><?//echo number_format($service_arr[$row['JOB_NO']][3]['QTY']) ;?></p></td>
									<td width="60"><p><?//echo number_format($service_arr[$row['JOB_NO']][3]['AMNT']) ;?></p></td>
															
									<?
									if($span == 0)
									{
										?>
									<td rowspan="<?= $rowspan; ?>"  valign="middle" width="120" align="right"><p><? 
									//echo number_format($total_acc_pi_file,2);
									?></p></td>
									<?}?>
								</tr>
								<?
								$i++;
								$span++;
							}
								?>
									<tr>
									<td width="345" colspan ="3"><h3></h3></td>
									<td width="100" align="right"><p><h3>Total:</h3></p></td>
									<td width="100" align="right"><p><b><?//echo number_format($total_acc_pi_qnty,2);?></b></p></td>
									<td width="100" align="right"><p><b><?//echo number_format($total_acc_pi_rate,2);?></b></p></td>
									<td width="100" align="right"><p><b><?//echo number_format($total_acc_pi_amount,2);?></b></p></td>
									<td width="120" align="right"><p><b><?//echo number_format($total_acc_pi_file,2)?></b></p></td>
								</tr>
						</tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>
	
	<!--16--File Wise Embllishment Cost : --> 
	<?

	$file_wise_embl_sql = "";
	//echo $accessories_pi_sql;

	$file_wise_embl_sql_result = sql_select($file_wise_embl_sql);
	$row_count = array();
	$span = 0;
	foreach($file_wise_embl_sql_result as $row_req)
	{
	$row_count[$row_req['FILE_NO']]++;
	$acc_pi_qnty += $row_req['QUANTITY'];
	$acc_pi_rate += $row_req['NET_PI_RATE'];
	$acc_pi_amnt += $row_req['NET_PI_AMOUNT'];
	}
	
	?>
	<div style="width:100%; margin-left:20px;" align="left">
		 <form name="requisitionApproval_1" id="requisitionApproval_1"> 
         <h3 style="width:880px;margin-top:10px;" align="left" id="accordion_h16" class="accordion_h" onClick="accordion_menu(this.id,'content_search_file_wise_embllishment_cost','fnc_close(this.id)')">
			<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tr>
					<td width="325" ><h3>+ File Wise Embllishment Cost</h3></td>
					<td width="100"><p><h3>Total:</h3></p></td>
					<td width="100" align="right"><p><?//echo number_format($acc_pi_qnty,2);?></td>
					<td width="100" align="right"><p><?//echo number_format($acc_pi_rate,2);?></td>
					<td width="100" align="right"><p><?//echo number_format($acc_pi_amnt,2);?></td>
					<td width="120" align="right"><p><?
					//$total_acc_pi_file = $acc_pi_amnt - $file_value;
					//if($total_acc_pi_file >0)echo number_format($total_acc_pi_file,2)?></p></td>
				</tr>
			</table></h3> 
         	<div id="content_search_file_wise_embllishment_cost">      
             	<fieldset style="width:880px;">
                 	<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
					 	<thead>
							<tr>
								<th width="120"><p>Printe</p> </th>
								<th width="80"><p>Emb</p></th>
								<th width="120"><p>-</p></th>
								<th width="100"><p>-</p></th>
								<th width="100"><p>-</p></th>
								<th width="100"><p>-</p></th>
								<th width="100"><p>-</p></th>
								<th width="120"><p>File Balance</p></th>
							</tr>
						</thead>
						<tbody>
						<?
							$i =1;
							$span = 0;
							foreach($file_wise_embl_sql_result as $result)
							{
								$rowspan = $row_count[$result['FILE_NO']];
								if ($i % 2 == 0) $bgcolor = "#DFDFDF";
								else $bgcolor = "#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>">
									<td width="120"><p><?echo $result['PI_NUMBER'];?></p></td>
									<td width="80"><p><?echo $count_arr[$result['COUNT_NAME']]?></p></td>
									<td width="120"><p><? echo $composition[$result['YARN_COMPOSITION_ITEM1']];?></p></td>
									<td width="100"><p><?echo $yarn_type[$result['YARN_TYPE']];?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['QUANTITY'],2);
									$total_acc_pi_qnty +=$result['QUANTITY'];
									?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['NET_PI_RATE'],2);
									$total_acc_pi_rate +=$result['NET_PI_RATE'];
									?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['NET_PI_AMOUNT'],2);
									$total_acc_pi_amount +=$result['NET_PI_AMOUNT'];
									?></p></td>							
									<?
									if($span == 0)
									{
										?>
									<td rowspan="<?= $rowspan; ?>"  valign="middle" width="120" align="right"><p><? 
									echo number_format($total_acc_pi_file,2);
									?></p></td>
									<?}?>
								</tr>
								<?
								$i++;
								$span++;
							}
								?>
									<tr>
									<td width="325" colspan ="3"><h3></h3></td>
									<td width="100" align="right"><p><h3>Total:</h3></p></td>
									<td width="100" align="right"><p><b><?//echo number_format($total_acc_pi_qnty,2);?></b></p></td>
									<td width="100" align="right"><p><b><?//echo number_format($total_acc_pi_rate,2);?></b></p></td>
									<td width="100" align="right"><p><b><?//echo number_format($total_acc_pi_amount,2);?></b></p></td>
									<td width="120" align="right"><p><b><?//echo number_format($total_acc_pi_file,2)?></b></p></td>
								</tr>
						</tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>

	<!--17--File Wise Textile Production: --> 
	<?

	$file_wise_tex_sql = "SELECT A.ID , A.QNTY AS ALLOCATION_QTY 
	FROM INV_MATERIAL_ALLOCATION_DTLS A, GBL_TEMP_ENGINE B 
	WHERE A.PO_BREAK_DOWN_ID =B.REF_VAL  AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.USER_ID= $user_id AND B.ENTRY_FORM=16 AND B.REF_FROM=1";
	//echo $file_wise_tex_sql;

	$file_wise_tex_sql_result = sql_select($file_wise_tex_sql);
	$row_count = array();
	$span = 0;
	foreach($file_wise_tex_sql_result as $row_req)
	{
	$row_count[$row_req['FILE_NO']]++;
	$allocation_qty += $row_req['ALLOCATION_QTY'];
	}
	

	?>
	<div style="width:100%; margin-left:20px;" align="left">
		 <form name="requisitionApproval_1" id="requisitionApproval_1"> 
         <h3 style="width:900px;margin-top:10px;" align="left" id="accordion_h17" class="accordion_h" onClick="accordion_menu(this.id,'content_search_file_wise_textile_production','fnc_close(this.id)')">
			<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tr>
					<td width="225" ><h3>+ File Wise Textile Production</h3></td>
					<td width="120"><p><h3>Total:</h3></p></td>
					<td width="100" align="right"><p><?//echo number_format($allocation_qty,2);?></td>
					<td width="100" align="right"><p><?//echo number_format($acc_pi_rate,2);?></td>
					<td width="100" align="right"><p><?//echo number_format($acc_pi_amnt,2);?></td>
					<td width="100" align="right"><p><?//echo number_format($acc_pi_amnt,2);?></td>
					<td width="120" align="right"><p><?
					//$total_acc_pi_file = $acc_pi_amnt - $file_value;
					//if($total_acc_pi_file >0)echo number_format($total_acc_pi_file,2)?></p></td>
				</tr>
			</table></h3> 
         	<div id="content_search_file_wise_textile_production">      
             	<fieldset style="width:900px;">
                 	<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
					 	<thead>
							<tr>
								<th width="120"><p>Yarn Allocate</p> </th>
								<th width="100"><p>Knitting</p></th>
								<th width="120"><p>Grey Recv</p></th>
								<th width="100"><p>Grey Issue</p></th>
								<th width="100"><p>Batch</p></th>
								<th width="100"><p>Dyeing</p></th>
								<th width="100"><p>Dye Finishing</p></th>
								<th width="120"><p>Sent to Cutting</p></th>
							</tr>
						</thead>
						<tbody>
						<?
							$i =1;
							$span = 0;
							foreach($file_wise_tex_sql_result as $result)
							{
								$rowspan = $row_count[$result['FILE_NO']];
								if ($i % 2 == 0) $bgcolor = "#DFDFDF";
								else $bgcolor = "#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>">
									<td width="120" align="right"><p><?echo number_format($result['ALLOCATION_QTY']); ?></p></td>
									<td width="100" align="right"><p><?echo $count_arr[$result['COUNT_NAME']]?></p></td>
									<td width="120" align="right"><p><? echo $composition[$result['YARN_COMPOSITION_ITEM1']];?></p></td>
									<td width="100" align="right"><p><?echo $yarn_type[$result['YARN_TYPE']];?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['QUANTITY'],2);
									$total_acc_pi_qnty +=$result['QUANTITY'];
									?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['NET_PI_RATE'],2);
									$total_acc_pi_rate +=$result['NET_PI_RATE'];
									?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['NET_PI_AMOUNT'],2);
									$total_acc_pi_amount +=$result['NET_PI_AMOUNT'];
									?></p></td>							
									<?
									if($span == 0)
									{
										?>
									<td rowspan="<?= $rowspan; ?>"  valign="middle" width="120" align="right"><p><? 
									//echo number_format($total_acc_pi_file,2);
									?></p></td>
									<?}?>
								</tr>
								<?
								$i++;
								$span++;
							}
								?>
									<tr>
									<td width="120" align="right"><p><b><?echo number_format($allocation_qty,2);?></b></p></td>
									<td width="100" align="right"><p><b><?//echo number_format($total_acc_pi_qnty,2);?></b></p></td>
									<td width="120" align="right"><p><b><?//echo number_format($total_acc_pi_qnty,2);?></b></p></td>
									<td width="100" align="right"><p><b><?//echo number_format($total_acc_pi_qnty,2);?></b></p></td>
									<td width="100" align="right"><p><b><?//echo number_format($total_acc_pi_rate,2);?></b></p></td>
									<td width="100" align="right"><p><b><?//echo number_format($total_acc_pi_amount,2);?></b></p></td>
									<td width="100" align="right"><p><b><?//echo number_format($total_acc_pi_amount,2);?></b></p></td>
									<td width="120" align="right"><p><b><?//echo number_format($total_acc_pi_file,2)?></b></p></td>
								</tr>
						</tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>

	
	<!--18--File Wise Garments Production: --> 
	<?
	//1=Cutting	4=Sewing In 5=Sewing Out	2 =Print Sent	3 = Print Recv 14 =	Finishing	Exfactory

	$file_wise_gar_sql = "SELECT A.PO_BREAK_DOWN_ID , B. PRODUCTION_QNTY, B.PRODUCTION_TYPE FROM PRO_GARMENTS_PRODUCTION_MST A, PRO_GARMENTS_PRODUCTION_DTLS B, GBL_TEMP_ENGINE C
	WHERE A.ID = B.MST_ID AND A.PO_BREAK_DOWN_ID = C.REF_VAL AND B.PRODUCTION_TYPE IN (1,2,3,4,5,14) AND  A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.USER_ID= $user_id AND C.ENTRY_FORM=16 AND C.REF_FROM=1 ";

	//echo $file_wise_gar_sql;
	

	$file_wise_gar_sql_result = sql_select($file_wise_gar_sql);
	$row_count = array();
	$span = 0;
	foreach($acc_pi_sql_result as $row_req)
	{
	$row_count[$row_req['FILE_NO']]++;
	$acc_pi_qnty += $row_req['QUANTITY'];
	$acc_pi_rate += $row_req['NET_PI_RATE'];
	$acc_pi_amnt += $row_req['NET_PI_AMOUNT'];
	}
	

	?>
	<div style="width:100%; margin-left:20px;" align="left">
		 <form name="requisitionApproval_1" id="requisitionApproval_1"> 
         <h3 style="width:880px;margin-top:10px;" align="left" id="accordion_h18" class="accordion_h" onClick="accordion_menu(this.id,'content_search_file_wise_garments_production','fnc_close(this.id)')">
			<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tr>
					<td width="325" ><h3>+ File Wise Garments Production</h3></td>
					<td width="100"><p><h3>Total:</h3></p></td>
					<td width="100" align="right"><p><?//echo number_format($acc_pi_qnty,2);?></td>
					<td width="100" align="right"><p><?//echo number_format($acc_pi_rate,2);?></td>
					<td width="100" align="right"><p><?//echo number_format($acc_pi_amnt,2);?></td>
					<td width="120" align="right"><p><?
					//$total_acc_pi_file = $acc_pi_amnt - $file_value;
					//if($total_acc_pi_file >0)echo number_format($total_acc_pi_file,2)?></p></td>
				</tr>
			</table></h3> 
         	<div id="content_search_file_wise_garments_production">      
             	<fieldset style="width:880px;">
                 	<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
					 	<thead>
							<tr>
								<th width="120"><p>Cutting</p> </th>
								<th width="80"><p>Sewing In</p></th>
								<th width="120"><p>Sewing Out</p></th>
								<th width="100"><p>Print Sent</p></th>
								<th width="100"><p>Print Recv</p></th>
								<th width="100"><p>Finishing</p></th>
								<th width="100"><p>Exfactory</p></th>
								<th width="120"><p>File Balance</p></th>
							</tr>
						</thead>
						<tbody>
						<?
							$i =1;
							$span = 0;
							foreach($file_wise_gar_sql_result as $result)
							{
								$rowspan = $row_count[$result['FILE_NO']];
								if ($i % 2 == 0) $bgcolor = "#DFDFDF";
								else $bgcolor = "#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>">
									<td width="120"><p><?echo $result['PRODUCTION_QNTY'];?></p></td>
									<td width="80"><p><?echo $count_arr[$result['COUNT_NAME']]?></p></td>
									<td width="120"><p><? echo $composition[$result['YARN_COMPOSITION_ITEM1']];?></p></td>
									<td width="100"><p><?echo $yarn_type[$result['YARN_TYPE']];?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['QUANTITY'],2);
									$total_acc_pi_qnty +=$result['QUANTITY'];
									?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['NET_PI_RATE'],2);
									$total_acc_pi_rate +=$result['NET_PI_RATE'];
									?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['NET_PI_AMOUNT'],2);
									$total_acc_pi_amount +=$result['NET_PI_AMOUNT'];
									?></p></td>							
									<?
									if($span == 0)
									{
										?>
									<td rowspan="<?= $rowspan; ?>"  valign="middle" width="120" align="right"><p><? 
									//echo number_format($total_acc_pi_file,2);
									?></p></td>
									<?}?>
								</tr>
								<?
								$i++;
								$span++;
							}
								?>
									<tr>
									<td width="325" colspan ="3"><h3></h3></td>
									<td width="100" align="right"><p><h3>Total:</h3></p></td>
									<td width="100" align="right"><p><b><?//echo number_format($total_acc_pi_qnty,2);?></b></p></td>
									<td width="100" align="right"><p><b><?//echo number_format($total_acc_pi_rate,2);?></b></p></td>
									<td width="100" align="right"><p><b><?//echo number_format($total_acc_pi_amount,2);?></b></p></td>
									<td width="120" align="right"><p><b><?//echo number_format($total_acc_pi_file,2)?></b></p></td>
								</tr>
						</tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>

	
	<!--19--File Wise Import start: --> 
	<?

	$import_start_sql = "";
	//echo $import_start_sql;

	$import_start_sql_result = sql_select($import_start_sql);
	$row_count = array();
	$span = 0;
	foreach($import_start_sql_result as $row_req)
	{
	$row_count[$row_req['FILE_NO']]++;
	$acc_pi_qnty += $row_req['QUANTITY'];
	$acc_pi_rate += $row_req['NET_PI_RATE'];
	$acc_pi_amnt += $row_req['NET_PI_AMOUNT'];
	}


	?>
	<div style="width:100%; margin-left:20px;" align="left">
		 <form name="requisitionApproval_1" id="requisitionApproval_1"> 
         <h3 style="width:880px;margin-top:10px;" align="left" id="accordion_h19" class="accordion_h" onClick="accordion_menu(this.id,'content_search_file_wise_import','fnc_close(this.id)')">
			<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tr>
					<td width="325" ><h3>+ File Wise Import</h3></td>
					<td width="100"><p><h3>Total:</h3></p></td>
					<td width="100" align="right"><p><?//echo number_format($acc_pi_qnty,2);?></td>
					<td width="100" align="right"><p><?//echo number_format($acc_pi_rate,2);?></td>
					<td width="100" align="right"><p><?//echo number_format($acc_pi_amnt,2);?></td>
					<td width="120" align="right"><p><?
					//$total_acc_pi_file = $acc_pi_amnt - $file_value;
					//if($total_acc_pi_file >0)echo number_format($total_acc_pi_file,2)?></p></td>
				</tr>
			</table></h3> 
         	<div id="content_search_file_wise_import">      
             	<fieldset style="width:880px;">
                 	<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
					 	<thead>
							<tr>
								<th width="120"><p>PI NO</p> </th>
								<th width="80"><p>PI Category</p></th>
								<th width="120"><p>PI Qty</p></th>
								<th width="100"><p>PI Rate</p></th>
								<th width="100"><p>PI Amount</p></th>
								<th width="100"><p>Acceptance Amount</p></th>
								<th width="100"><p>Payment</p></th>
								<th width="120"><p>File Balance</p></th>
							</tr>
						</thead>
						<tbody>
						<?
							$i =1;
							$span = 0;
							foreach($import_start_sql_result as $result)
							{
								$rowspan = $row_count[$result['FILE_NO']];
								if ($i % 2 == 0) $bgcolor = "#DFDFDF";
								else $bgcolor = "#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>">
									<td width="120"><p><?echo $result['PI_NUMBER'];?></p></td>
									<td width="80"><p><?echo $count_arr[$result['COUNT_NAME']]?></p></td>
									<td width="120"><p><? echo $composition[$result['YARN_COMPOSITION_ITEM1']];?></p></td>
									<td width="100"><p><?echo $yarn_type[$result['YARN_TYPE']];?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['QUANTITY'],2);
									$total_acc_pi_qnty +=$result['QUANTITY'];
									?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['NET_PI_RATE'],2);
									$total_acc_pi_rate +=$result['NET_PI_RATE'];
									?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['NET_PI_AMOUNT'],2);
									$total_acc_pi_amount +=$result['NET_PI_AMOUNT'];
									?></p></td>							
									<?
									if($span == 0)
									{
										?>
									<td rowspan="<?= $rowspan; ?>"  valign="middle" width="120" align="right"><p><? 
									echo number_format($total_acc_pi_file,2);
									?></p></td>
									<?}?>
								</tr>
								<?
								$i++;
								$span++;
							}
								?>
									<tr>
									<td width="325" colspan ="3"><h3></h3></td>
									<td width="100" align="right"><p><h3>Total:</h3></p></td>
									<td width="100" align="right"><p><b><?//echo number_format($total_acc_pi_qnty,2);?></b></p></td>
									<td width="100" align="right"><p><b><?//echo number_format($total_acc_pi_rate,2);?></b></p></td>
									<td width="100" align="right"><p><b><?//echo number_format($total_acc_pi_amount,2);?></b></p></td>
									<td width="120" align="right"><p><b><?//echo number_format($total_acc_pi_file,2)?></b></p></td>
								</tr>
						</tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>

	<!--20--File Wise Export start: --> 
	<?

	$export_start_sql = "";
	//echo $export_start_sql;

	$export_start_sql_result = sql_select($export_start_sql);
	$row_count = array();
	$span = 0;
	foreach($export_start_sql_result as $row_req)
	{
		$row_count[$row_req['FILE_NO']]++;
		$acc_pi_qnty += $row_req['QUANTITY'];
		$acc_pi_rate += $row_req['NET_PI_RATE'];
		$acc_pi_amnt += $row_req['NET_PI_AMOUNT'];
	}
	

	?>
	<div style="width:100%; margin-left:20px;" align="left">
		 <form name="requisitionApproval_1" id="requisitionApproval_1"> 
         <h3 style="width:880px;margin-top:10px;" align="left" id="accordion_h20" class="accordion_h" onClick="accordion_menu(this.id,'content_search_file_wise_export','fnc_close(this.id)')">
			<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tr>
					<td width="325" ><h3>+ File Wise Export</h3></td>
					<td width="100"><p><h3>Total:</h3></p></td>
					<td width="100" align="right"><p><?//echo number_format($acc_pi_qnty,2);?></td>
					<td width="100" align="right"><p><?//echo number_format($acc_pi_rate,2);?></td>
					<td width="100" align="right"><p><?//echo number_format($acc_pi_amnt,2);?></td>
					<td width="120" align="right"><p><?
					//$total_acc_pi_file = $acc_pi_amnt - $file_value;
					//if($total_acc_pi_file >0)echo number_format($total_acc_pi_file,2)?></p></td>
				</tr>
			</table></h3> 
         	<div id="content_search_file_wise_export">      
             	<fieldset style="width:880px;">
                 	<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
					 	<thead>
							<tr>
								<th width="120"><p>Invoice No</p> </th>
								<th width="80"><p>Invoice Qty</p></th>
								<th width="120"><p>Invoice Rate</p></th>
								<th width="100"><p>Invoice Amount</p></th>
								<th width="100"><p>Discount Amount</p></th>
								<th width="100"><p>Bank Submission</p></th>
								<th width="100"><p>Realization</p></th>
								<th width="120"><p>File Balance</p></th>
							</tr>
						</thead>
						<tbody>
						<?
							$i =1;
							$span = 0;
							foreach($export_start_sql_result as $result)
							{
								$rowspan = $row_count[$result['FILE_NO']];
								if ($i % 2 == 0) $bgcolor = "#DFDFDF";
								else $bgcolor = "#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>">
									<td width="120"><p><?echo $result['PI_NUMBER'];?></p></td>
									<td width="80"><p><?echo $count_arr[$result['COUNT_NAME']]?></p></td>
									<td width="120"><p><? echo $composition[$result['YARN_COMPOSITION_ITEM1']];?></p></td>
									<td width="100"><p><?echo $yarn_type[$result['YARN_TYPE']];?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['QUANTITY'],2);
									$total_acc_pi_qnty +=$result['QUANTITY'];
									?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['NET_PI_RATE'],2);
									$total_acc_pi_rate +=$result['NET_PI_RATE'];
									?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['NET_PI_AMOUNT'],2);
									$total_acc_pi_amount +=$result['NET_PI_AMOUNT'];
									?></p></td>							
									<?
									if($span == 0)
									{
										?>
									<td rowspan="<?= $rowspan; ?>"  valign="middle" width="120" align="right"><p><? 
									echo number_format($total_acc_pi_file,2);
									?></p></td>
									<?}?>
								</tr>
								<?
								$i++;
								$span++;
							}
								?>
									<tr>
									<td width="325" colspan ="3"><h3></h3></td>
									<td width="100" align="right"><p><h3>Total:</h3></p></td>
									<td width="100" align="right"><p><b><?//echo number_format($total_acc_pi_qnty,2);?></b></p></td>
									<td width="100" align="right"><p><b><?//echo number_format($total_acc_pi_rate,2);?></b></p></td>
									<td width="100" align="right"><p><b><?//echo number_format($total_acc_pi_amount,2);?></b></p></td>
									<td width="120" align="right"><p><b><?//echo number_format($total_acc_pi_file,2)?></b></p></td>
								</tr>
						</tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>

	<!-- 21--File Wise Analysis start : --> 
	<?
	//item_catgoryId ==4 

	$analysis_start_sql = "";
	//echo $accessories_pi_sql;

	$analysis_start_sql_result = sql_select($analysis_start_sql);
	$row_count = array();
	$span = 0;
	foreach($analysis_start_sql_result as $row_req)
	{
		$row_count[$row_req['FILE_NO']]++;
		$acc_pi_qnty += $row_req['QUANTITY'];
		$acc_pi_rate += $row_req['NET_PI_RATE'];
		$acc_pi_amnt += $row_req['NET_PI_AMOUNT'];
	}


	?>
	<div style="width:100%; margin-left:20px;" align="left">
		 <form name="requisitionApproval_1" id="requisitionApproval_1"> 
         <h3 style="width:1080px;margin-top:10px;" align="left" id="accordion_h21" class="accordion_h" onClick="accordion_menu(this.id,'content_search_file_wise_analysis','fnc_close(this.id)')">
			<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tr>
					<td width="325" ><h3>+ File Wise Analysis </h3></td>
					<td width="100"><p><h3>Total:</h3></p></td>
					<td width="100" align="right"><p><?//echo number_format($acc_pi_qnty,2);?></td>
					<td width="100" align="right"><p><?//echo number_format($acc_pi_rate,2);?></td>
					<td width="100" align="right"><p><?//echo number_format($acc_pi_amnt,2);?></td>
					<td width="80" align="center"><p></p></td>
					<td width="120" align="center"><p></p></td>
					<td width="120" align="right"><p><?
					//$total_acc_pi_file = $acc_pi_amnt - $file_value;
					//if($total_acc_pi_file >0)echo number_format($total_acc_pi_file,2)?></p></td>
				</tr>
			</table></h3> 
         	<div id="content_search_file_wise_analysis">      
             	<fieldset style="width:1080px;">
                 	<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
					 	<thead>
							<tr>
								<th width="120"><p>File Value</p> </th>
								<th width="80"><p>Order Value</p></th>
								<th width="120"><p>Budget Value</p></th>
								<th width="100"><p>WO Value</p></th>
								<th width="100"><p>PI Value</p></th>
								<th width="100"><p>MRR Value</p></th>
								<th width="100"><p>Issue Value</p></th>
								<th width="80"><p>Sales Contract Value</p></th>
								<th width="120"><p>Shipment Value</p></th>
								<th width="120"><p>File Balance</p></th>
							</tr>
						</thead>
						<tbody>
						<?
							$i =1;
							$span = 0;
							foreach($analysis_start_sql_result as $result)
							{
								$rowspan = $row_count[$result['FILE_NO']];
								if ($i % 2 == 0) $bgcolor = "#DFDFDF";
								else $bgcolor = "#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>">
									<td width="120"><p><?echo $result['PI_NUMBER'];?></p></td>
									<td width="80"><p><?echo $count_arr[$result['COUNT_NAME']]?></p></td>
									<td width="120"><p><? echo $composition[$result['YARN_COMPOSITION_ITEM1']];?></p></td>
									<td width="100"><p><?echo $yarn_type[$result['YARN_TYPE']];?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['QUANTITY'],2);
									$total_acc_pi_qnty +=$result['QUANTITY'];
									?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['NET_PI_RATE'],2);
									$total_acc_pi_rate +=$result['NET_PI_RATE'];
									?></p></td>
									<td width="100" align="right"><p><?echo number_format($result['NET_PI_AMOUNT'],2);
									$total_acc_pi_amount +=$result['NET_PI_AMOUNT'];
									?></p></td>
									<td width="80" align="center"><p><?echo $result['APPROVED']==1 ? "YES" : "NO";?></p></td>
									<td width="120" align="right"><p><?echo number_format($payment_acc_arr[$result['PI_ID']]['ACCEPTED_AMMOUNT'],2); ?></p></td>							
									<?
									if($span == 0)
									{
										?>
									<td rowspan="<?= $rowspan; ?>"  valign="middle" width="120" align="right"><p><? 
									echo number_format($total_acc_pi_file,2);
									?></p></td>
									<?}?>
								</tr>
								<?
								$i++;
								$span++;
							}
								?>
									<tr>
									<td width="325" colspan ="3"><h3></h3></td>
									<td width="100" align="right"><p><h3>Total:</h3></p></td>
									<td width="100" align="right"><p><b><?//echo number_format($total_acc_pi_qnty,2);?></b></p></td>
									<td width="100" align="right"><p><b><?//echo number_format($total_acc_pi_rate,2);?></b></p></td>
									<td width="100" align="right"><p><b><?//echo number_format($total_acc_pi_amount,2);?></b></p></td>
									<td width="80" align="center"><p><b></p></td>
									<td width="120" align="center"><p></p></td>
									<td width="120" align="right"><p><b><?//echo number_format($total_acc_pi_file,2)?></b></p></td>
								</tr>
						</tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>
	
	<?
		
		$rID2=execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form in (16)");
		if ($rID2) oci_commit($con);
		disconnect($con);
	?>
	<!-- -----END-----  -->
	<div style="width:100%; margin-left:20px; margin-left:40px;" align="left">
	<br><br><br><br>
	</div>
 <?
}