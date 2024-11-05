<?
session_start();
include('../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];
$user_level = $_SESSION['logic_erp']['user_level'];
//------------------------------------------------------------------------------------------------------
if (!function_exists("pre")) 
{
	function pre($array){
		echo "<pre>";
		print_r($array);
		echo "</pre>";
	} 	 
}
if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "txt_search_common", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "");
	exit();
}
 
 
if($action=="order_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	?>
		<script>
			$(document).ready(function(e) {
				$("#txt_search_common").focus();
				$("#company_search_by").val(<?php echo $_REQUEST['company']; ?>);

			});  
			function js_set_value(po_id,job_id,po_number,company_id,buyer,style,item,po_qty,plan_cut_qty,total_ex_fact_qty,shipping_status)
			{ 
				let yet_to_ex_fact = (shipping_status == 3) ? 0 : po_qty - total_ex_fact_qty; 
				parent.window.document.getElementById('hidden_po_break_down_id').value = po_id;
				parent.window.document.getElementById('hidden_job_id').value = job_id;
				parent.window.document.getElementById('txt_order_no').value = po_number;
				parent.window.document.getElementById('cbo_company_name').value = company_id;
				parent.window.document.getElementById('cbo_buyer_name').value = buyer;
				parent.window.document.getElementById('txt_style_no').value = style;
				parent.window.document.getElementById('cbo_item_name').value = item;
				parent.window.document.getElementById('txt_order_qty').value = po_qty;
				parent.window.document.getElementById('txt_po_qty').value = po_qty;
				parent.window.document.getElementById('txt_plan_cut_qty').value = plan_cut_qty; 
				parent.window.document.getElementById('txt_cumul_ex_fact_qty').value = total_ex_fact_qty; 
				parent.window.document.getElementById('txt_yet_to_ex_fact').value = yet_to_ex_fact; 
				parent.emailwindow.hide();
			}
		</script>
	</head>
	<body>
		<div align="center" style="width:100%;" >
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table width="780" ellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
					<thead>
						<th width="130" class="must_entry_caption">Company</th>
						<th width="130">Search By</th>
						<th width="130" align="center" id="search_by_th_up">Enter Order Number</th>
						<th width="130" colspan="2">Date Range</th>
						<th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
					</thead>
					<tr class="general">
						<td><? echo create_drop_down( "company_search_by", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "", 0 ); ?></td>
						<td>
							<?
							$searchby_arr=array(0=>"Order No");
							echo create_drop_down( "txt_search_by", 130, $searchby_arr,"", 1, "-- Select Sample --", $selected, "",0 );
							?>
						</td>
						<td id="search_by_td"><input type="text" style="width:120px" class="text_boxes"  name="txt_search_common" id="txt_search_common" onKeyDown="if (event.keyCode == 13) document.getElementById('btn_show').click()" /></td>
						<td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date"></td>
						<td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date"></td>
						<td>
							<input type="button" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('company_search_by').value, 'create_po_search_list_view', 'search_div', 'bh_ex_factory_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td align="center" valign="middle" colspan="6">
							<? echo load_month_buttons(1);  ?> 
						</td>
					</tr>
				</table>
				<div style="margin-top:10px" id="search_div"></div>
			</form>
		</div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="create_po_search_list_view")
{
	
 	$ex_data = explode("_",$data);
	if($ex_data[4]== 0)
	{ 
		echo "Please Select Company First."; die;
	}
	$txt_search_by = trim($ex_data[0]);
	$txt_search_common = trim($ex_data[1]);
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];  
 
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array( "SELECT buy.id, buy.buyer_name FROM lib_buyer buy, lib_buyer_tag_company b WHERE buy.status_active =1 AND buy.is_deleted=0 AND b.buyer_id=buy.id AND b.tag_company IN (".$company.") AND buy.id IN (SELECT buyer_id FROM lib_buyer_party_type WHERE party_type IN (1,3,21,90) ) GROUP BY buy.id, buy.buyer_name ORDER BY buy.buyer_name","id", "buyer_name");

	$sql_cond="";
	if($txt_search_common != "")
	{
		if($txt_search_by==0)
		{
			$sql_cond .= " and b.po_number like '%".$txt_search_common."%'";
		}
		 
 	}
	if($txt_date_from!="" || $txt_date_to!="")
	{
		$sql_cond .= " and b.pub_shipment_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
	}

	if(trim($company)!="") $sql_cond .= " and a.company_name=$company";

	$sql = "SELECT a.id as job_id, a.company_name,a.buyer_name,a.style_ref_no as style,b.id as po_id,b.po_number,b.po_quantity,b.plan_cut,b.shipment_date from bh_wo_po_details_master a, bh_wo_po_break_down b where a.id = b.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.within_group=2 $sql_cond order by b.shipment_date ASC";
	// echo $sql; die;
	$sql_res = sql_select($sql);
	if (count($sql_res) == 0 ) 
	{
		echo "<h1 style='color:red; font-size: 17px;text-align:center;margin-top:20px;'> ** Data Not Found ** </h1>" ;
		die();
	}

	$job_id_arr = $data_array= $po_id_arr = array();
	foreach ($sql_res as $v) 
	{
		 $job_id_arr[$v['JOB_ID']] = $v['JOB_ID'];
		 $po_id_arr[$v['PO_ID']]   = $v['PO_ID'];

		 $data_array[$v['JOB_ID']][$v['PO_ID']]['SHIPMENT_DATE']= $v['SHIPMENT_DATE'];
		 $data_array[$v['JOB_ID']][$v['PO_ID']]['COMPANY_NAME'] = $v['COMPANY_NAME'];
		 $data_array[$v['JOB_ID']][$v['PO_ID']]['BUYER_NAME']   = $v['BUYER_NAME'];
		 $data_array[$v['JOB_ID']][$v['PO_ID']]['STYLE']   		= $v['STYLE'];
		 $data_array[$v['JOB_ID']][$v['PO_ID']]['PO_NUMBER']   	= $v['PO_NUMBER'];
		 $data_array[$v['JOB_ID']][$v['PO_ID']]['PO_QUANTITY']  += $v['PO_QUANTITY'];
		 $data_array[$v['JOB_ID']][$v['PO_ID']]['PLAN_CUT']  	+= $v['PLAN_CUT'];
	}

	// ============================================= ITEM =============================================

	// JOB WISE ITEM FOUND IN DB     NB: THIS QUERY WILL NOT WORK FOR SET'S ORDER 
	$all_job = implode(',',$job_id_arr);
	$item_sql = "SELECT a.job_id,a.gmts_item_id from BH_WO_PO_DETAILS_MAS_SET_DETAILS a where a.job_id in($all_job)";
	$job_wise_item_array = return_library_array( $item_sql, "job_id", "gmts_item_id");


	// =============================================EX FACTORY QTY=============================================
	$all_po = implode(',',$po_id_arr);
	$ex_fact_sql = "SELECT sum(ex_factory_qty) as ex_factory_qty ,po_break_down_id as po_id,max(shiping_status) as shiping_status from bh_pro_ex_factory_mst where po_break_down_id in ($all_po) and status_active=1 and is_deleted=0 group by po_break_down_id ";
	// echo $ex_fact_sql; die;
	$ex_fact_sql_res = sql_select($ex_fact_sql);
	$ex_fact_array = array();
	foreach ($ex_fact_sql_res as  $v) 
	{
		$ex_fact_array[$v['PO_ID']]['EX_FACTORY_QTY'] = $v['EX_FACTORY_QTY']; 
		$ex_fact_array[$v['PO_ID']]['SHIPING_STATUS'] = $v['SHIPING_STATUS'];  
	}
 	// echo pre($ex_fact_array);die();  
	$width = 890;
	?>
    <div style="width:<?= $width+20 ?>px;">
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $width ?>" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="60">Shipment Date</th> 
                <th width="100">Order No</th>
                <th width="80">Buyer</th>
                <th width="100">Style</th> 
                <th width="140">Item</th> 
                <th width="80">Order Qty</th>
                <th width="100">Total Ex-factory Qty</th> 
                <th width="80">Balance</th>
                <th width="120">Company Name</th>
            </thead>
     	</table>
     </div>
     <div style="width:<?= $width+20 ?>px; max-height:240px;overflow-y:scroll;margin-left: 16px;" >
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $width ?>" class="rpt_table" id="tbl_po_list">
			<?
				$i=1;
				foreach( $data_array as $job_id => $job_data_arr )
				{  
					foreach ($job_data_arr as $po_id => $v) 
					{ 
						$grmts_item = $job_wise_item_array[$job_id];
						$po_qty =  $v['PO_QUANTITY'];
						$ex_fact_qty = $ex_fact_array[$po_id]['EX_FACTORY_QTY'];
						$shiping_status = $ex_fact_array[$po_id]['SHIPING_STATUS'];
						$balance = $po_qty - $ex_fact_qty ;
						if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 

						$set_val_str =  "'".$po_id."','".$job_id."','".$v["PO_NUMBER"]."','".$v["COMPANY_NAME"]."','".$v["BUYER_NAME"]."','".$v["STYLE"]."','".$grmts_item."','".$po_qty."','".$v["PLAN_CUT"]."','".$ex_fact_qty."','".$shiping_status."'";
						?>
							<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<?= $set_val_str ?>);" >
								<td width="30" align="center"><?=$i; ?></td>
								<td width="60" align="center"><?=change_date_format($v["SHIPMENT_DATE"]);?></td> 
								<td width="100"style="word-break: break-all;"><?=$v["PO_NUMBER"]; ?></td>
								<td width="80" style="word-break: break-all;"><?=$buyer_arr[$v["BUYER_NAME"]]; ?></td>
								<td width="100" style="word-break: break-all;"><?=$v["STYLE"]; ?></td>
								<td width="140" style="word-break: break-all;"><?=$garments_item[$grmts_item];?></td>
								<td width="80" align="right"><?= $po_qty; ?></td>
								<td width="100" align="right"><?= $ex_fact_qty; ?></td>
								<td width="80" align="right"><?= $balance; ?></td>
								<td width="120" style="word-break: break-all;" align="center"><?=$company_arr[$v["COMPANY_NAME"]];?> </td>
							</tr>
						<?
						$i++; 
					}
					
				}
   			?>
        </table>
    </div>
	<?
	exit();
}

 
if($action=="show_dtls_listview")
{  
	$po_id = $data; 
	// echo $po_id; die;
	ob_start(); 

	$ex_fact_sql = "SELECT id,item_number_id,ex_factory_date,ex_factory_qty,challan_no,ex_factory_qty from bh_pro_ex_factory_mst where po_break_down_id=$po_id and status_active=1 and is_deleted=0"; 
	$ex_fact_sql_res = sql_select($ex_fact_sql); 

	?>
     
	<div style="width:600px;max-height:180px; overflow-y:scroll" id="sewing_production_list_view" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="500px" class="rpt_table" id="tbl_search">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="150">Item Name</th> 
					<th width="100">Ex-Fact. Date</th>
					<th width="100">Ex-Fact. Qty</th> 
					<th width="100">Challan No</th>
					</tr>
			</thead>
			<tbody>
				<?
					$i=1; 
					foreach($ex_fact_sql_res as $v)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
						?>
							<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="fnc_load_from_dtls(<?= $v['ID'] ?>);" >
								<td width="30"><?= $i; ?></td>
								<td width="150"><?= $garments_item[$v['ITEM_NUMBER_ID']] ?></td> 
								<td width="100"><?= $v['EX_FACTORY_DATE'] ?></td>
								<td width="100"><?= $v['EX_FACTORY_QTY'] ?></td> 
								<td width="100"><?= $v['CHALLAN_NO'] ?></td>
							</tr>
						<?
						$i++;
					}
				?>
			</tbody> `
		</table>
        <script>setFilterGrid("tbl_search",-1); </script>
    </div>
	<?
	exit();
}
 
if($action=="populate_issue_form_data")
{ 
	$mst_id = $data;
	$ex_fact_sql = "SELECT id,ex_factory_date,ex_factory_qty,reject_qty,total_carton_qnty,challan_no,carton_qnty,ex_fact_company_id,shiping_status,remarks,item_number_id,po_break_down_id as po_id from bh_pro_ex_factory_mst where id=$mst_id and status_active=1 and is_deleted=0"; 
	$ex_fact_sql_res = sql_select($ex_fact_sql);  

  	// echo $ex_fact_sql;die;
	foreach($ex_fact_sql_res as $v)
	{ 
		$ex_fact_qty= $v['EX_FACTORY_QTY'];
		$po_id  	= $v['PO_ID'];

		echo "$('#txt_mst_id').val('".$v['ID']."');\n";   
		echo "$('#cbo_item_name').val('".$v['ITEM_NUMBER_ID']."');\n";   
		echo "$('#cbo_ex_fact_comp').val('".$v['EX_FACT_COMPANY_ID']."');\n";   
		echo "$('#txt_ex_fact_qty').val('".$v['EX_FACTORY_QTY']."');\n";   
		echo "$('#txt_reject_qty').val('".$v['REJECT_QTY']."');\n";   
		echo "$('#txt_carton_qty').val('".$v['TOTAL_CARTON_QNTY']."');\n";   
		echo "$('#txt_challan').val('".$v['CHALLAN_NO']."');\n";   
		echo "$('#txt_qty_ctn').val('".$v['CARTON_QNTY']."');\n";   
		echo "$('#delivery_status').val('".$v['SHIPING_STATUS']."');\n";   
		echo "$('#txt_remarks').val('".$v['REMARKS']."');\n";   
		echo "$('#txt_issue_date').val('".change_date_format($v['EX_FACTORY_DATE'])."');\n";
		
	}

	

	$po_sql = "SELECT a.id as job_id, a.company_name,a.buyer_name,a.style_ref_no as style,b.id as po_id,b.po_number,b.po_quantity,b.plan_cut,b.shipment_date from bh_wo_po_details_master a, bh_wo_po_break_down b where a.id = b.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id=$po_id";
	// echo $po_sql; die;
	$po_sql_res = sql_select($po_sql);   
	
	foreach($po_sql_res as $v)
	{ 
		$po_qty = $v['PO_QUANTITY'];

		echo "$('#txt_order_no').val('".$v['PO_NUMBER']."');\n";   
		echo "$('#hidden_po_break_down_id').val('".$v['PO_ID']."');\n";   
		echo "$('#hidden_job_id').val('".$v['JOB_ID']."');\n";   
		echo "$('#cbo_company_name').val('".$v['COMPANY_NAME']."');\n";   
		echo "$('#cbo_buyer_name').val('".$v['BUYER_NAME']."');\n";   
		echo "$('#txt_style_no').val('".$v['STYLE']."');\n";   
		echo "$('#txt_order_qty').val('".$po_qty."');\n";   
		echo "$('#txt_po_qty').val('".$po_qty."');\n";    
		echo "$('#txt_plan_cut_qty').val('".$v['PLAN_CUT']."');\n"; 
	}

	$po_wise_exfact_sql = "SELECT ex_factory_qty  from bh_pro_ex_factory_mst where po_break_down_id=$po_id and status_active=1 and is_deleted=0"; 
	$po_wise_exfact_sql_res = sql_select($po_wise_exfact_sql);  

  	//echo "sdfds".$sqlResult;die;
	$total_ex_fact = 0;
	foreach($po_wise_exfact_sql_res as $v)
	{  
		$cumul_ex_fact_qty += $v['EX_FACTORY_QTY'];
	}
	$yet_to_ex_fact_qty = $po_qty - $cumul_ex_fact_qty + $ex_fact_qty;

	echo "$('#txt_po_qty').val('".$po_qty."');\n";
	echo "$('#txt_cumul_ex_fact_qty').val('".$cumul_ex_fact_qty."');\n";
	echo "$('#txt_yet_to_ex_fact').val('".$yet_to_ex_fact_qty."');\n";
	echo "set_button_status(1, permission, 'fnc_issue_print_embroidery_entry',1,1);\n";   
 	exit();
}
  
if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();    
		$id= return_next_id_by_sequence(  "bh_pro_ex_factory_mst_seq", "bh_pro_ex_factory_mst", $con );


		// SAME DATE DUPLICATE DATA CHECK
		$issue_date  = str_replace("'","",$txt_issue_date);
		$date_cond = " and ex_factory_date='".change_date_format($issue_date,'dd-mm-yyyy','-',1)."' ";
		$same_date_entry = sql_select( " SELECT max(shiping_status) from bh_pro_ex_factory_mst where 
		po_break_down_id=$hidden_po_break_down_id  $date_cond ");  
		if(count($same_date_entry))
		{
			echo "99**".str_replace("'","",$hidden_po_break_down_id); die;
		}

		$field_array1="id,company_id, po_break_down_id, buyer_name, style_ref_no, job_id, item_number_id, po_quantity, plan_cut, production_source, ex_fact_company_id, ex_factory_date, ex_factory_qty, reject_qty, total_carton_qnty, challan_no, carton_qnty, shiping_status, remarks, inserted_by, insert_date, status_active, is_deleted";

		$data_array1="(".$id.",".$cbo_company_name.",".$hidden_po_break_down_id.",".$cbo_buyer_name.",".$txt_style_no.", ".$hidden_job_id.",".$cbo_item_name.", ".$txt_order_qty.",".$txt_plan_cut_qty.",".$cbo_source.",".$cbo_ex_fact_comp.",".$txt_issue_date.",".$txt_ex_fact_qty.",".$txt_reject_qty.",".$txt_carton_qty.",".$txt_challan.",".$txt_qty_ctn.",".$delivery_status.",".$txt_remarks.",".$user_id.",'".$pc_date_time."',1,0)";
		
		// echo "10**".str_replace("'","",$hidden_po_break_down_id)."INSERT INTO bh_pro_ex_factory_mst (".$field_array1.") VALUES ".$data_array1."";// die;

		$rID=sql_insert("bh_pro_ex_factory_mst",$field_array1,$data_array1,1); 
		 
		if($rID)
		{
			oci_commit($con);
			echo "0**".str_replace("'","",$hidden_po_break_down_id);
		}
		else
		{
			oci_rollback($con);
			echo "10**".str_replace("'","",$hidden_po_break_down_id);
		}
		disconnect($con);
		die;
	}
  	else if ($operation==1) // Update Here End------------------------------------------------------
	{

 		$con = connect();   
 
		// SAME DATE DUPLICATE DATA CHECK
		$today  = date('d-M-Y');
		$date_cond = " and ex_factory_date <>'".change_date_format($today,'dd-mm-yyyy','-',1)."' ";
		$same_date_entry = sql_select( " SELECT max(shiping_status) from bh_pro_ex_factory_mst where 
		po_break_down_id=$hidden_po_break_down_id  $date_cond ");  
		if(count($same_date_entry))
		{
			echo "98**".str_replace("'","",$hidden_po_break_down_id); die;
		}


 		$field_array_up="updated_by*update_date*status_active*is_deleted"; 
		$data_array_up="".$user_id."*'".$pc_date_time."'*'0'*'1'"; 
		$id= return_next_id_by_sequence(  "bh_pro_ex_factory_mst_seq", "bh_pro_ex_factory_mst", $con );

		$field_array1="id,company_id, po_break_down_id, buyer_name, style_ref_no, job_id, item_number_id, po_quantity, plan_cut, production_source, ex_fact_company_id, ex_factory_date, ex_factory_qty, reject_qty, total_carton_qnty, challan_no, carton_qnty, shiping_status, remarks, inserted_by, insert_date, status_active, is_deleted";

		$data_array1="(".$id.",".$cbo_company_name.",".$hidden_po_break_down_id.",".$cbo_buyer_name.",".$txt_style_no.", ".$hidden_job_id.",".$cbo_item_name.", ".$txt_order_qty.",".$txt_plan_cut_qty.",".$cbo_source.",".$cbo_ex_fact_comp.",".$txt_issue_date.",".$txt_ex_fact_qty.",".$txt_reject_qty.",".$txt_carton_qty.",".$txt_challan.",".$txt_qty_ctn.",".$delivery_status.",".$txt_remarks.",".$user_id.",'".$pc_date_time."',1,0)";
		
		// echo "10**".str_replace("'","",$hidden_po_break_down_id)."INSERT INTO bh_pro_ex_factory_mst (".$field_array1.") VALUES ".$data_array1."";// die;

		$update=sql_update("bh_pro_ex_factory_mst",$field_array_up,$data_array_up,"id","".$txt_mst_id."",1);
		$rID=sql_insert("bh_pro_ex_factory_mst",$field_array1,$data_array1,1); 
		 
		if($update && $rID)
		{
			oci_commit($con);
			echo "1**".str_replace("'","",$hidden_po_break_down_id);
		}
		else
		{
			oci_rollback($con);
			echo "10**".str_replace("'","",$hidden_po_break_down_id);
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)  // Delete Here----------------------------------------------------------
	{
		$con = connect();  

		$rID = sql_delete("bh_pro_ex_factory_mst","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'id ',$txt_mst_id,0); 

		if($rID)
		{
			oci_commit($con);
			echo "2**".str_replace("'","",$hidden_po_break_down_id);
		}
		else
		{
			oci_rollback($con);
			echo "10**".str_replace("'","",$hidden_po_break_down_id);
		}
		disconnect($con);
		die;
	}
} 
