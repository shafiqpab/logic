<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

$buyer_arr=return_library_array("select id,short_name from  lib_buyer","id","short_name");
$buyer_ref_arr=return_library_array("select id,exporters_reference from  lib_buyer","id","exporters_reference");
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$lib_sub_dept_array=return_library_array( "select id,sub_department_name from lib_pro_sub_deparatment", "id", "sub_department_name"  );
$lib_buyer_season=return_library_array( "select id,season_name from lib_buyer_season", "id", "season_name"  );
$lib_color=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "load_drop_down( 'requires/order_summary_report_controller2', this.value, 'load_drop_down_sub_dep', 'sub_department_td' );" );
	exit();
}

if ($action=="load_drop_down_sub_dep")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_sub_department", 120, "select id,sub_department_name from lib_pro_sub_deparatment where buyer_id in($data[0]) and status_active =1 and is_deleted=0 order by sub_department_name","id,sub_department_name", 1, "-- Select --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_buyer_ref")
{
	$sql = "select distinct EXPORTERS_REFERENCE from lib_buyer  where status_active =1 and is_deleted=0 and id in($data) and exporters_reference is not null order by exporters_reference";
    $res = sql_select($sql);
    $ref_array = array();
    foreach ($res as $val) 
    {
    	$ref_array[$val['EXPORTERS_REFERENCE']] = $val['EXPORTERS_REFERENCE'];
    }

	echo create_drop_down( "cbo_buyer_ref", 120, $ref_array, "",1, "-- Select --", $selected, "" );
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	extract($_REQUEST);
	$company_name 	=str_replace("'","",$cbo_company_name);
	$buyer_name 	=str_replace("'","",$cbo_buyer_name);
	$buyer_ref 		=str_replace("'","",$cbo_buyer_ref);
	$sub_department =str_replace("'","",$cbo_sub_department);
	$shipping_status_id=str_replace("'","",$cbo_shipping_status);
	$date_type 		=str_replace("'","",$cbo_date_type);
	$reportType 	=str_replace("'","",$cbo_report_type);
	$show_value 	=str_replace("'","",$show_value);
	$order_status 	=str_replace("'","",$cbo_order_status);
	$cbo_shipping_status=str_replace("'","",$cbo_shipping_status);


	// =========================== MAKING QUERY COND ============================
	// echo cal_days_in_month(CAL_GREGORIAN, $to_month, $to_year); 
	// $dt = $from_year."-".$from_month;
	// echo date('d-m-Y',strtotime($dt));die();
	$shipping_status_chk=array(1,2);
	if(in_array($shipping_status_id,$shipping_status_chk))
	{
	$shipping_status_id="1,2";
	}
	if($cbo_shipping_status==4){
		$shipping_status_id="";
	}
	$sql_cond = "";
	$sql_cond .= ($company_name !=0) ? " and a.company_name = $company_name" : "";
	$sql_cond .= ($buyer_name !=0) ? " and a.buyer_name in($buyer_name)" : "";
	$sql_cond .= ($sub_department !=0) ? " and a.pro_sub_dep in($sub_department)" : "";
	$sql_cond .= ($shipping_status_id !="") ? " and b.shiping_status in($shipping_status_id)" : "";
	$sql_cond .= ($order_status !=0) ? " and b.is_confirmed=$order_status" : " and b.is_confirmed in (1,2)";
	if($buyer_ref !=0)
	{
		$buyer_id_array = return_library_array("select id,id as ids from  lib_buyer where status_active=1 and exporters_reference='$buyer_ref'","id","ids");
	}
	// print_r($buyer_id_array);die();
	if(count($buyer_id_array)>0)
	{
		$buyer_id = implode(",", $buyer_id_array);
		$sql_cond .= " and a.buyer_name in($buyer_id)";
	}

	if($db_type==0)
	{
		$from_date=change_date_format(str_replace("'","",$txt_date_from),'yyyy-mm-dd');
		$to_date=change_date_format(str_replace("'","",$txt_date_to),'yyyy-mm-dd');
	}
	elseif($db_type==2)
	{
		$from_date=change_date_format(str_replace("'","",$txt_date_from),'','',1);
		$to_date=change_date_format(str_replace("'","",$txt_date_to),'','',1);
	}
	if($date_type==1)
	{
		$sql_cond .= " and b.PUB_SHIPMENT_DATE between '$from_date' and '$to_date'";
	}
	else if($date_type==2)
	{
		$sql_cond .= " and b.SHIPMENT_DATE between '$from_date' and '$to_date'";
	}
	if($company_name!="") $company_name="and a.company_id=$company_name"; else $company_name="";

	

if($reportType==1)
{

	
	// =============================================== MAIN QUERY =========================================
	  $sql = "SELECT a.JOB_NO,a.STYLE_REF_NO,a.BUYER_NAME,a.TOTAL_SET_QNTY,a.PRO_SUB_DEP,a.season_buyer_wise as SEASON,a.ORDER_UOM,a.REMARKS,b.id as PO_ID,b.PUB_SHIPMENT_DATE,b.PO_NUMBER,b.PO_RECEIVED_DATE,b.IS_CONFIRMED,b.UNIT_PRICE,b.TXT_ETD_LDD,b.PO_TOTAL_PRICE,b.po_quantity as ORDER_QTY, (c.ORDER_QUANTITY*a.TOTAL_SET_QNTY) AS QTY_PCS,c.ORDER_QUANTITY as QTY_SET,c.item_number_id as ITEM_ID,c.color_number_id as COLOR_ID,b.po_quantity,a.total_set_qnty
	 from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id  $sql_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $sql_cond";
	  //echo $sql;

	$sql_res = sql_select($sql);
	if (count($sql_res) < 1)
	{
		echo '<div style="text-align: center;color: red;font-weight: bold;font-size: 20px;">Data not found.</div>';
		disconnect($con);
		die();
	}
	$ex_factory_qnty = sql_select("SELECT po_break_down_id,ex_factory_qnty from pro_ex_factory_mst where status_active=1");
	foreach($ex_factory_qnty as $row){

		$ex_factory_qty_arr[$row[csf('po_break_down_id')]]=$row[csf('ex_factory_qnty')];
	}
	// ======================================= generate array for report =============================
	$data_array = array();
	$job_qty_array = array();
	$po_qty_array = array();
	$item_qty_array = array();
	$color_qty_array = array();
	$job_array = array();
	$po_array = array();
	$po_check_array = array();
	foreach ($sql_res as $val) 
	{
		$data_array[$val['JOB_NO']][$val['ITEM_ID']][$val['COLOR_ID']][$val['PO_ID']]['style'] = $val['STYLE_REF_NO'];
		$data_array[$val['JOB_NO']][$val['ITEM_ID']][$val['COLOR_ID']][$val['PO_ID']]['pro_sub_dep'] = $val['PRO_SUB_DEP'];
		$data_array[$val['JOB_NO']][$val['ITEM_ID']][$val['COLOR_ID']][$val['PO_ID']]['season'] = $val['SEASON'];
		$data_array[$val['JOB_NO']][$val['ITEM_ID']][$val['COLOR_ID']][$val['PO_ID']]['order_uom'] = $val['ORDER_UOM'];
		$data_array[$val['JOB_NO']][$val['ITEM_ID']][$val['COLOR_ID']][$val['PO_ID']]['remarks'] = $val['REMARKS'];
		$data_array[$val['JOB_NO']][$val['ITEM_ID']][$val['COLOR_ID']][$val['PO_ID']]['pub_shipment_date'] = $val['PUB_SHIPMENT_DATE'];
		$data_array[$val['JOB_NO']][$val['ITEM_ID']][$val['COLOR_ID']][$val['PO_ID']]['po_number'] = $val['PO_NUMBER'];
		$data_array[$val['JOB_NO']][$val['ITEM_ID']][$val['COLOR_ID']][$val['PO_ID']]['po_quantity'] = $val[csf('po_quantity')];
		$data_array[$val['JOB_NO']][$val['ITEM_ID']][$val['COLOR_ID']][$val['PO_ID']]['total_set_qnty'] = $val[csf('total_set_qnty')];
		$data_array[$val['JOB_NO']][$val['ITEM_ID']][$val['COLOR_ID']][$val['PO_ID']]['po_received_date'] = $val['PO_RECEIVED_DATE'];
		$data_array[$val['JOB_NO']][$val['ITEM_ID']][$val['COLOR_ID']][$val['PO_ID']]['buyer_name'] = $val['BUYER_NAME'];
		$data_array[$val['JOB_NO']][$val['ITEM_ID']][$val['COLOR_ID']][$val['PO_ID']]['is_confirmed'] = $val['IS_CONFIRMED'];
		$data_array[$val['JOB_NO']][$val['ITEM_ID']][$val['COLOR_ID']][$val['PO_ID']]['total_set_qnty'] = $val['TOTAL_SET_QNTY'];
		$data_array[$val['JOB_NO']][$val['ITEM_ID']][$val['COLOR_ID']][$val['PO_ID']]['txt_etd_ldd'] = $val['TXT_ETD_LDD'];
		$data_array[$val['JOB_NO']][$val['ITEM_ID']][$val['COLOR_ID']][$val['PO_ID']]['unit_price'] = $val['UNIT_PRICE'];
		$data_array[$val['JOB_NO']][$val['ITEM_ID']][$val['COLOR_ID']][$val['PO_ID']]['po_total_price'] += $val['PO_TOTAL_PRICE'];

		$po_qty_array[$val['JOB_NO']][$val['ITEM_ID']][$val['COLOR_ID']][$val['PO_ID']] += $val['QTY_PCS'];
		$color_qty_array[$val['JOB_NO']][$val['ITEM_ID']][$val['COLOR_ID']] += $val['QTY_PCS'];
		$item_qty_array[$val['JOB_NO']][$val['ITEM_ID']] += $val['QTY_PCS']-$ex_factory_qty_arr[$val['PO_ID']];;
		$job_qty_array[$val['JOB_NO']]['qty_pcs'] += $val['QTY_PCS']-$ex_factory_qty_arr[$val['PO_ID']];;
		$job_qty_array[$val['JOB_NO']]['qty_set'] += $val['QTY_SET']-$ex_factory_qty_arr[$val['PO_ID']];;
		$buyer_qty_array[$val['BUYER_NAME']]['qty_pcs'] += $val['QTY_PCS']-$ex_factory_qty_arr[$val['PO_ID']];;
		$job_array[$val['JOB_NO']] = $val['JOB_NO'];
		$po_array[$val['PO_ID']] = $val['PO_ID'];
	}
	unset($sql_res);
	// echo "<pre>";
	//  print_r($buyer_qty_array);
	$job_nos = "'".implode("','", $job_array)."'";
	$po_ids = implode(",", $po_array);

	// =====================check kniting status ======================
	$kniting_status_arr = return_library_array("SELECT po_breakdown_id,1 as status from order_wise_pro_details where status_active=1 and po_breakdown_id in($po_ids)","po_breakdown_id", "status");

	// =====================check dyeing status ======================
	$dyeing_status_arr = return_library_array("SELECT a.po_id,1 as status from pro_batch_create_dtls a, pro_fab_subprocess b where a.mst_id=b.batch_id and a.status_active=1 and b.status_active=1 and a.po_id in($po_ids)","po_id", "status");

	// =====================check kniting status ======================
	$prod_status_arr = return_library_array("SELECT po_break_down_id,production_type from pro_garments_production_mst where status_active=1 and po_break_down_id in($po_ids) and production_type in(1,4)","po_break_down_id", "production_type");

	// =====================check kniting status ======================
	$ex_status_arr = return_library_array("SELECT po_break_down_id,1 as status from pro_ex_factory_mst where status_active=1 and po_break_down_id in($po_ids)","po_break_down_id", "status");


	$lib_fab_desc=return_library_array( "select job_no,fabric_description from wo_pre_cost_fabric_cost_dtls where job_no in($job_nos) and status_active=1 order by id desc", "job_no", "fabric_description"  );

	$lib_image=return_library_array( "select master_tble_id,image_location from common_photo_library where master_tble_id in($job_nos) order by id desc", "master_tble_id", "image_location");
	// print_r($lib_image);
	
	
	//================================ for rowspan ===============================
	$rowspan_job = array();
	$rowspan_item = array();
	$rowspan_color = array();
	foreach ($data_array as $job_no => $job_data) 
	{
		foreach ($job_data as $item_id => $item_data) 
		{
			foreach ($item_data as $color_id => $color_data) 
			{
				foreach ($color_data as $po_id => $row) 
				{
					$rowspan_job[$job_no]++;
					$rowspan_item[$job_no][$item_id]++;
					$rowspan_color[$job_no][$item_id][$color_id]++;
				}
			}
		}
	}


	$tbl_width = 2250;
	ob_start();
	?>
	<fieldset style="width:100%;">
        <table width="<? echo $tbl_width;?>;" cellspacing="0"  align="center">
            <tr>
                <td colspan="26" align="center" class="form_caption">
                    <strong style="font-size:16px;">Company Name:<? echo  $company_library[$company_name] ;?></strong>
                </td>
            </tr>
            <tr class="form_caption">
                <td colspan="26" align="center" class="form_caption"> <strong style="font-size:15px;">Order Summary Report V2</strong></td>
            </tr>
            <tr align="center">
                <td colspan="26" align="center" class="form_caption"> <strong style="font-size:15px;">Date Type : <? echo ($date_type==1) ? "Shipment Date (RFI)": "Original Ship Date";?></strong></td>
            </tr>
       	</table>

       	<!-- =============================== table header start ================================ -->
       	<div>
	        <table width="<? echo $tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header" align="left">
	        	<thead>
	        		<tr>
	        			<th width="30">Sl</th>
	        			<th width="80">Buyer Ref.</th>
	        			<th width="80">Buyer</th>
	        			<th width="80">Job No.</th>
	        			<th width="80">Style Ref.</th>
	        			<th width="80">Season</th>
	        			<th width="80">Sub Dept.</th>
	        			<th width="50">UOM</th>
	        			<th width="50">Image</th>
	        			<th width="100">Item</th>
	        			<th width="100">Fabric Description</th>
	        			<th width="100">Color/Qnty (Pcs)</th>
	        			<th width="100">PO No./ Qnty(Pcs)</th>
	        			<th width="50">UOM</th>
	        			<th width="80">Item Qnty (Pcs)</th>
	        			<th width="80">Job Qnty (Pcs)</th>
	        			<th width="80">Job Qnty (UOM)</th>
	        			<th width="70">PO Receive Date</th>
	        			<th width="70">Ship Date</th>
						<?
						if($cbo_shipping_status==4){?>
						<th width="70">Ex-Fac Qnty</th>
						<th width="70">Short Qnty</th>
						<th width="70">Excess Qnty</th>
						<?}?>
	        			<th width="70">ETD/LDD</th>
	        			<th width="70">FOB Rate</th>
	        			<th width="70">FOB Value</th>
	        			<th width="70">Price Status</th>
	        			<th width="70">Order Status</th>
	        			<th width="70">Order Position</th>
	        			<th width="100">Remarks</th>
	        		</tr>
	        	</thead>
	        </table>
	        <!-- =============================== table body start ================================ -->
	        <div style="width:<? echo $tbl_width+20;?>px;overflow-y:scroll;max-height:350px;" id="scroll_body">
	        	<table width="<? echo $tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header" align="left">
	        		<tbody>
	        			<?
	        			$i=1;
	        			$sl=1;
	        			$tot_item_qty = 0;
	        			$tot_job_qty_pc = 0;
	        			$tot_job_qty = 0;
	        			$tot_fob_val = 0;$tot_po_qty=0;$tot_po_color_qty=0;
	        			foreach ($data_array as $job_no => $job_data) 
						{
							$j=0;
							foreach ($job_data as $item_id => $item_data) 
							{
								$itm=0;
								foreach ($item_data as $color_id => $color_data) 
								{
									$c=0;
									foreach ($color_data as $po_id => $row) 
									{
										$order_position = "";
										if(isset($kniting_status_arr[$po_id])) $order_position = "Kniting";
										if(isset($dyeing_status_arr[$po_id])) $order_position = "Dyeing";
										if($prod_status_arr[$po_id]==1) $order_position = "Cutting";
										if($prod_status_arr[$po_id]==4) $order_position = "Sewing";
										if(isset($ex_status_arr[$po_id])) $order_position = "Shipment";

										if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					        			?>
					        			<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" style="font-size:11px">
					        				<? if($j==0){?>
					        				<td valign="top" rowspan="<? echo $rowspan_job[$job_no];?>" width="30"><p><? echo $sl;?></p></td>
						        			<td valign="top" rowspan="<? echo $rowspan_job[$job_no];?>" width="80"><p><? echo $buyer_ref_arr[$row['buyer_name']];?></p></td>
						        			<td valign="top" rowspan="<? echo $rowspan_job[$job_no];?>" width="80"><p><? echo $buyer_arr[$row['buyer_name']];?></p></td>
						        			<td valign="top" rowspan="<? echo $rowspan_job[$job_no];?>" width="80"><p><? echo $job_no;?></p></td>
						        			<td valign="top" rowspan="<? echo $rowspan_job[$job_no];?>" width="80"><p>&nbsp;<? echo $row['style'];?></p></td>
						        			<td valign="top" rowspan="<? echo $rowspan_job[$job_no];?>" width="80"><p><? echo $lib_buyer_season[$row['season']];?></p></td>
						        			<td valign="top" rowspan="<? echo $rowspan_job[$job_no];?>" width="80"><p><? echo $lib_sub_dept_array[$row['pro_sub_dep']];?></p></td>
						        			<td valign="top" rowspan="<? echo $rowspan_job[$job_no];?>" width="50"><p><? echo $unit_of_measurement[$row['order_uom']];?></p></td>
						        			<td valign="top" valign="middle" rowspan="<? echo $rowspan_job[$job_no];?>" width="50"><p>
						        				<img src="../../../<? echo $lib_image[$job_no];?>" width="40" height="30" alt="image">
						        			</p></td>
						        			<? $sl++;} if($itm==0){?>
						        			<td valign="top" rowspan="<? echo $rowspan_item[$job_no][$item_id];?>" width="100"><p><? echo $garments_item[$item_id];?></p></td>
						        			<td valign="top" rowspan="<? echo $rowspan_item[$job_no][$item_id];?>" width="100"><p><? echo $lib_fab_desc[$job_no];?></p></td>
						        			<? } if($c==0){?>
						        			<td valign="top" rowspan="<? echo $rowspan_color[$job_no][$item_id][$color_id];?>" width="100"><p><? echo $lib_color[$color_id];?>/<? echo $color_qty_array[$job_no][$item_id][$color_id];?></p></td>
						        			<? $c++;}?>
						        			<td width="100"><p><? echo $row['po_number'];?>/<? echo $po_qty_array[$job_no][$item_id][$color_id][$po_id];?></p></td>
						        			<td width="50"><p><? echo $unit_of_measurement[$row['order_uom']];?></p></td>
						        			<? if($itm==0)
						        			{?>
						        			<td valign="top" align="right" rowspan="<? echo $rowspan_item[$job_no][$item_id];?>" width="80"><p><? echo $item_qty_array[$job_no][$item_id];?></p></td>
						        			<? $tot_item_qty += $item_qty_array[$job_no][$item_id];$itm++;} if($j==0){?>					        			<td valign="top" rowspan="<? echo $rowspan_job[$job_no];?>" align="right" width="80"><p><? echo $job_qty_array[$job_no]['qty_pcs'];?></p></td>
						        			<td valign="top" rowspan="<? echo $rowspan_job[$job_no];?>" align="right" width="80"><p><? echo $job_qty_array[$job_no]['qty_set'];?></p></td>
						        			<? $j++;
						        			$tot_job_qty_pc += $job_qty_array[$job_no]['qty_pcs'];
						        			$tot_job_qty += $job_qty_array[$job_no]['qty_set'];
						        			}?>
						        			<td width="70" align="center"><p><? echo change_date_format($row['po_received_date']);?></p></td>
											<td width="70" align="center"><p><? echo change_date_format($row['pub_shipment_date']);?></p></td>
											<?	if($cbo_shipping_status==4){?>
											<td width="70" align="center"><p><?	echo $ex_factory_qty_arr[$po_id];?></p></td>
											<td width="70" align="center"><p><? 											
											$short_qnty=($job_qty_array[$job_no]['qty_pcs']-$ex_factory_qty_arr[$po_id]);
										
											if($short_qnty<0){
												echo " ";
											}else{
												echo $short_qnty;
											}
											?></p></td>
											<td width="70" align="center"><p><?
												$access_qnty=($ex_factory_qty_arr[$po_id]-$job_qty_array[$job_no]['qty_pcs']);
												if($access_qnty<0){
													echo " ";
												}else{
													echo $access_qnty;
												}
										?></p></td>
											<?}?>
						        			<td width="70"><p><? echo $row['txt_etd_ldd'];?></p></td>
						        			<td align="right" width="70"><p><? echo $row['unit_price'];?></p></td>
						        			<td align="right" width="70"><p><? echo number_format($row['po_total_price'],2);?></p></td>
						        			<td width="70"><p><? //echo $a;?></p></td>
						        			<td width="70"><p><? echo $row['is_confirmed']==1 ? "Confirmed" : "Projection" ;?></p></td>
						        			<td width="70"><p><? echo $order_position;?></p></td>
						        			<td width="100"><p><? echo $row['remarks'];?></p></td>
					        			</tr>
					        			<?
					        			$i++;
					        			$tot_fob_val += $row['po_total_price'];	
										//$tot_po_qty += $po_qty_array[$job_no][$item_id][$color_id][$po_id];
										//$tot_po_color_qty += $color_qty_array[$job_no][$item_id][$color_id];	
					        		}
					        	}
					        }
					    }
					    ?>
	        		</tbody>
	        	</table>
	        </div>
	        <!-- =============================== table footer start ================================ -->
	        <table width="<? echo $tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header" align="left">
	        		<tfoot>
	        			<tr>
	        				<th width="30"></th>
		        			<th width="80"></th>
		        			<th width="80"></th>
		        			<th width="80"></th>
		        			<th width="80"></th>
		        			<th width="80"></th>
		        			<th width="80"></th>
		        			<th width="50"></th>
		        			<th width="50"></th>
		        			<th width="100"></th>
		        			<th width="100"><? echo $tot_po_color_qty;?></th>
		        			<th width="100"></th>
		        			<th width="100" title="POQty=<? echo $tot_po_qty;?>">Total</th>
		        			<th width="50"></th>
		        			<th width="80"><? echo number_format($tot_item_qty,0);?></th>
		        			<th width="80"><? echo number_format($tot_job_qty_pc,0);?></th>
		        			<th width="80"><? echo number_format($tot_job_qty,0);?></th>
		        			<th width="70"></th>
		        			<th width="70"></th>
		        			<th width="70"></th>
							<? if($cbo_shipping_status==4){?>
								<th width="70"></th>
		        			    <th width="70"></th>
		        			    <th width="70"></th>
							<?}?>
		        			<th width="70"></th>
		        			<th width="70"><? echo number_format($tot_fob_val,2); ?></th>
		        			<th width="70"></th>
		        			<th width="70"></th>
		        			<th width="70"></th>
		        			<th width="100"></th>
	        			</tr>
	        		</tfoot>
	        	</table>
    </fieldset>
	<?
}
else
{
	// =============================================== MAIN QUERY =========================================
	  $sql = "SELECT a.JOB_NO,a.STYLE_REF_NO,a.BUYER_NAME,a.TOTAL_SET_QNTY,a.PRO_SUB_DEP,a.season_buyer_wise as SEASON,a.ORDER_UOM,a.REMARKS,b.id as PO_ID,b.PUB_SHIPMENT_DATE,b.PO_NUMBER,b.PO_RECEIVED_DATE,b.grouping as  GROUPING,b.IS_CONFIRMED,b.UNIT_PRICE,b.TXT_ETD_LDD,b.PO_TOTAL_PRICE,b.po_quantity as ORDER_QTY, (c.ORDER_QUANTITY*a.TOTAL_SET_QNTY) AS QTY_PCS,c.ORDER_QUANTITY as QTY_SET,c.item_number_id as ITEM_ID,c.color_number_id as COLOR_ID,b.po_quantity,a.total_set_qnty
	 from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c where a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id  $sql_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $sql_cond";
	  //echo $sql;

	$sql_res = sql_select($sql);
	foreach ($sql_res as $val) 
	{
		$PO_idArr[$val['PO_ID']]= $val['PO_ID'];
	}
	if (count($sql_res) < 1)
	{
		echo '<div style="text-align: center;color: red;font-weight: bold;font-size: 20px;">Data not found.</div>';
		disconnect($con);
		die();
	}
	
	$poid_cond_for_in=where_con_using_array($PO_idArr,0,"po_break_down_id");
	$poid_cond_for_in2=where_con_using_array($PO_idArr,0,"b.po_break_down_id"); 
	
	$ex_factory_qnty = sql_select("SELECT po_break_down_id,ex_factory_qnty from pro_ex_factory_mst where status_active=1 $poid_cond_for_in");
	foreach($ex_factory_qnty as $row){

		$ex_factory_qty_arr[$row[csf('po_break_down_id')]]=$row[csf('ex_factory_qnty')];
	}
	// ======================================= generate array for report =============================
	$data_array = array();
	$job_qty_array = array();
	$po_qty_array = array();
	$item_qty_array = array();
	$color_qty_array = array();
	$job_array = array();
	$po_array = array();
	$po_check_array = array();
	foreach ($sql_res as $val) 
	{
		$data_array[$val['JOB_NO']][$val['ITEM_ID']][$val['COLOR_ID']][$val['PO_ID']]['style'] = $val['STYLE_REF_NO'];
		$data_array[$val['JOB_NO']][$val['ITEM_ID']][$val['COLOR_ID']][$val['PO_ID']]['ref_no'] = $val['GROUPING'];
		$data_array[$val['JOB_NO']][$val['ITEM_ID']][$val['COLOR_ID']][$val['PO_ID']]['pro_sub_dep'] = $val['PRO_SUB_DEP'];
		$data_array[$val['JOB_NO']][$val['ITEM_ID']][$val['COLOR_ID']][$val['PO_ID']]['season'] = $val['SEASON'];
		$data_array[$val['JOB_NO']][$val['ITEM_ID']][$val['COLOR_ID']][$val['PO_ID']]['order_uom'] = $val['ORDER_UOM'];
		$data_array[$val['JOB_NO']][$val['ITEM_ID']][$val['COLOR_ID']][$val['PO_ID']]['remarks'] = $val['REMARKS'];
		$data_array[$val['JOB_NO']][$val['ITEM_ID']][$val['COLOR_ID']][$val['PO_ID']]['pub_shipment_date'] = $val['PUB_SHIPMENT_DATE'];
		$data_array[$val['JOB_NO']][$val['ITEM_ID']][$val['COLOR_ID']][$val['PO_ID']]['po_number'] = $val['PO_NUMBER'];
		$data_array[$val['JOB_NO']][$val['ITEM_ID']][$val['COLOR_ID']][$val['PO_ID']]['po_quantity'] = $val[csf('po_quantity')];
		$data_array[$val['JOB_NO']][$val['ITEM_ID']][$val['COLOR_ID']][$val['PO_ID']]['total_set_qnty'] = $val[csf('total_set_qnty')];
		$data_array[$val['JOB_NO']][$val['ITEM_ID']][$val['COLOR_ID']][$val['PO_ID']]['po_received_date'] = $val['PO_RECEIVED_DATE'];
		$data_array[$val['JOB_NO']][$val['ITEM_ID']][$val['COLOR_ID']][$val['PO_ID']]['buyer_name'] = $val['BUYER_NAME'];
		$data_array[$val['JOB_NO']][$val['ITEM_ID']][$val['COLOR_ID']][$val['PO_ID']]['is_confirmed'] = $val['IS_CONFIRMED'];
		$data_array[$val['JOB_NO']][$val['ITEM_ID']][$val['COLOR_ID']][$val['PO_ID']]['total_set_qnty'] = $val['TOTAL_SET_QNTY'];
		$data_array[$val['JOB_NO']][$val['ITEM_ID']][$val['COLOR_ID']][$val['PO_ID']]['txt_etd_ldd'] = $val['TXT_ETD_LDD'];
		$data_array[$val['JOB_NO']][$val['ITEM_ID']][$val['COLOR_ID']][$val['PO_ID']]['unit_price'] = $val['UNIT_PRICE'];
		$data_array[$val['JOB_NO']][$val['ITEM_ID']][$val['COLOR_ID']][$val['PO_ID']]['po_total_price'] += $val['PO_TOTAL_PRICE'];

		$po_qty_array[$val['JOB_NO']][$val['ITEM_ID']][$val['COLOR_ID']][$val['PO_ID']] += $val['QTY_PCS'];
		$color_qty_array[$val['JOB_NO']][$val['ITEM_ID']][$val['COLOR_ID']] += $val['QTY_PCS'];
		$item_qty_array[$val['JOB_NO']][$val['ITEM_ID']] += $val['QTY_PCS']-$ex_factory_qty_arr[$val['PO_ID']];;
		$job_qty_array[$val['JOB_NO']]['qty_pcs'] += $val['QTY_PCS']-$ex_factory_qty_arr[$val['PO_ID']];;
		$job_qty_array[$val['JOB_NO']]['qty_set'] += $val['QTY_SET']-$ex_factory_qty_arr[$val['PO_ID']];;
		$buyer_qty_array[$val['BUYER_NAME']]['qty_pcs'] += $val['QTY_PCS']-$ex_factory_qty_arr[$val['PO_ID']];;
		$job_array[$val['JOB_NO']] = $val['JOB_NO'];
		$po_array[$val['PO_ID']] = $val['PO_ID'];
	}
	unset($sql_res);
	// echo "<pre>";
	//  print_r($buyer_qty_array);
	$job_nos = "'".implode("','", $job_array)."'";
	$po_ids = implode(",", $po_array);

	// =====================check kniting status ======================
	$kniting_status_arr = return_library_array("SELECT po_breakdown_id,1 as status from order_wise_pro_details where status_active=1 and po_breakdown_id in($po_ids)","po_breakdown_id", "status");

	// =====================check dyeing status ======================
	$dyeing_status_arr = return_library_array("SELECT a.po_id,1 as status from pro_batch_create_dtls a, pro_fab_subprocess b where a.mst_id=b.batch_id and a.status_active=1 and b.status_active=1 and a.po_id in($po_ids)","po_id", "status");

	// =====================check kniting status ======================
	$prod_status_arr = return_library_array("SELECT po_break_down_id,production_type from pro_garments_production_mst where status_active=1 and po_break_down_id in($po_ids) and production_type in(1,4)","po_break_down_id", "production_type");

	// =====================check kniting status ======================
	$ex_status_arr = return_library_array("SELECT po_break_down_id,1 as status from pro_ex_factory_mst where status_active=1 and po_break_down_id in($po_ids)","po_break_down_id", "status");


	$lib_fab_desc=return_library_array( "select job_no,fabric_description from wo_pre_cost_fabric_cost_dtls where job_no in($job_nos) and status_active=1 order by id desc", "job_no", "fabric_description"  );

	$lib_image=return_library_array( "select master_tble_id,image_location from common_photo_library where master_tble_id in($job_nos) order by id desc", "master_tble_id", "image_location");
	// print_r($lib_image);
	
	 $sql_wo_fab="select b.job_no as JOB_NO,b.gmts_color_id as COLOR_ID,c.item_number_id as ITEM_ID,b.po_break_down_id as PO_ID,b.fin_fab_qnty as FIN_FAB_QNTY,b.grey_fab_qnty as GREY_FAB_QNTY
			 from wo_booking_mst a,wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and c.id=pre_cost_fabric_cost_dtls_id  and a.booking_type in(1,4)  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $poid_cond_for_in2"; 
			$result_fab=sql_select( $sql_wo_fab );
			foreach ($result_fab as $row)
			{
				$fab_qty_array[$row['JOB_NO']][$row['ITEM_ID']][$row['COLOR_ID']][$row['PO_ID']]['FIN_FAB_QNTY'] += $row['FIN_FAB_QNTY'];
				$fab_qty_array[$row['JOB_NO']][$row['ITEM_ID']][$row['COLOR_ID']][$row['PO_ID']]['GREY_FAB_QNTY'] += $row['GREY_FAB_QNTY'];
			}
			unset($result_fab);
			//print_r($fab_qty_array);
			
	
	
	//================================ for rowspan ===============================
	$rowspan_job = array();
	$rowspan_item = array();
	$rowspan_color = array();
	foreach ($data_array as $job_no => $job_data) 
	{
		foreach ($job_data as $item_id => $item_data) 
		{
			foreach ($item_data as $color_id => $color_data) 
			{
				foreach ($color_data as $po_id => $row) 
				{
					$rowspan_job[$job_no]++;
					$rowspan_item[$job_no][$item_id]++;
					$rowspan_color[$job_no][$item_id][$color_id]++;
				}
			}
		}
	}


	$tbl_width = 2580;
	ob_start();
	?>
	<fieldset style="width:100%;">
        <table width="<? echo $tbl_width;?>;" cellspacing="0"  align="center">
            <tr>
                <td colspan="26" align="center" class="form_caption">
                    <strong style="font-size:16px;">Company Name:<? echo  $company_library[$company_name] ;?></strong>
                </td>
            </tr>
            <tr class="form_caption">
                <td colspan="26" align="center" class="form_caption"> <strong style="font-size:15px;">Order Summary Report V2</strong></td>
            </tr>
            <tr align="center">
                <td colspan="26" align="center" class="form_caption"> <strong style="font-size:15px;">Date Type : <? echo ($date_type==1) ? "Shipment Date (RFI)": "Original Ship Date";?></strong></td>
            </tr>
       	</table>

       	<!-- =============================== table header start ================================ -->
       	<div>
	        <table width="<? echo $tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header" align="left">
	        	<thead>
	        		<tr>
	        			<th width="30">Sl</th>
	        			<th width="80">Buyer Ref.</th>
	        			<th width="80">Buyer</th>
	        			<th width="80">Job No.</th>
	        			<th width="80">Style Ref.</th>
	        			<th width="80">Season</th>
	        			<th width="80">Sub Dept.</th>
	        			<th width="50">UOM</th>
	        			<th width="50">Image</th>
	        			<th width="70">Int. Ref. No</th>
                        
                        <th width="100">Item</th>
	        			<th width="100">Fabric Description</th>
                        
                        <th width="100">Color</th>
	        			<th width="100">Qnty (Pcs)</th>
                        
	        			<th width="100">PO No./ Qnty(Pcs)</th>
	        			<th width="50">UOM</th>
	        			<th width="80">Item Qnty (Pcs)</th>
	        			<th width="80">Job Qnty (Pcs)</th>
	        			<th width="80">Job Qnty (UOM)</th>
                        
                        <th width="80">Fin. Fab Qty</th>
                        <th width="80">Grey Fab Qty</th>
                        
	        			<th width="70">PO Receive Date</th>
	        			<th width="70">Ship Date</th>
						<?
						if($cbo_shipping_status==4){?>
						<th width="70">Ex-Fac Qnty</th>
						<th width="70">Short Qnty</th>
						<th width="70">Excess Qnty</th>
						<?}?>
	        			<th width="70">ETD/LDD</th>
	        			<th width="70">FOB Rate</th>
	        			<th width="70">FOB Value</th>
	        			<th width="70">Price Status</th>
	        			<th width="70">Order Status</th>
	        			<th width="70">Order Position</th>
	        			<th width="100">Remarks</th>
	        		</tr>
	        	</thead>
	        </table>
	        <!-- =============================== table body start ================================ -->
	        <div style="width:<? echo $tbl_width+20;?>px;overflow-y:scroll;max-height:350px;" id="scroll_body">
	        	<table width="<? echo $tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header" align="left">
	        		<tbody>
	        			<?
	        			$i=1;
	        			$sl=1;
	        			$tot_item_qty = 0;
	        			$tot_job_qty_pc = 0;$tot_fin_fab_qty = 0;$tot_grey_fab_qty = 0;
						
						 
											
	        			$tot_job_qty = 0;
	        			$tot_fob_val = 0;$tot_po_qty=0;$tot_po_color_qty=0;
	        			foreach ($data_array as $job_no => $job_data) 
						{
							$j=0;
							foreach ($job_data as $item_id => $item_data) 
							{
								$itm=0;
								foreach ($item_data as $color_id => $color_data) 
								{
									$c=0;
									foreach ($color_data as $po_id => $row) 
									{
										$order_position = "";
										if(isset($kniting_status_arr[$po_id])) $order_position = "Kniting";
										if(isset($dyeing_status_arr[$po_id])) $order_position = "Dyeing";
										if($prod_status_arr[$po_id]==1) $order_position = "Cutting";
										if($prod_status_arr[$po_id]==4) $order_position = "Sewing";
										if(isset($ex_status_arr[$po_id])) $order_position = "Shipment";

										if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										
								$fin_fab_qty=$fab_qty_array[$job_no][$item_id][$color_id][$po_id]['FIN_FAB_QNTY'];
								$grey_fab_qty=$fab_qty_array[$job_no][$item_id][$color_id][$po_id]['GREY_FAB_QNTY'];
				
					        			?>
					        			<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" style="font-size:11px">
					        				<? if($j==0){?>
					        				<td valign="top" rowspan="<? echo $rowspan_job[$job_no];?>" width="30"><p><? echo $sl;?></p></td>
						        			<td valign="top" rowspan="<? echo $rowspan_job[$job_no];?>" width="80"><p><? echo $buyer_ref_arr[$row['buyer_name']];?></p></td>
						        			<td valign="top" rowspan="<? echo $rowspan_job[$job_no];?>" width="80"><p><? echo $buyer_arr[$row['buyer_name']];?></p></td>
						        			<td valign="top" rowspan="<? echo $rowspan_job[$job_no];?>" width="80"><p><? echo $job_no;?></p></td>
						        			<td valign="top" rowspan="<? echo $rowspan_job[$job_no];?>" width="80"><p>&nbsp;<? echo $row['style'];?></p></td>
						        			<td valign="top" rowspan="<? echo $rowspan_job[$job_no];?>" width="80"><p><? echo $lib_buyer_season[$row['season']];?></p></td>
						        			<td valign="top" rowspan="<? echo $rowspan_job[$job_no];?>" width="80"><p><? echo $lib_sub_dept_array[$row['pro_sub_dep']];?></p></td>
						        			<td valign="top" rowspan="<? echo $rowspan_job[$job_no];?>" width="50"><p><? echo $unit_of_measurement[$row['order_uom']];?></p></td>
						        			<td valign="top" valign="middle" rowspan="<? echo $rowspan_job[$job_no];?>" width="50"><p>
						        				<img src="../../../<? echo $lib_image[$job_no];?>" width="40" height="30" alt="image">
						        			</p></td>
						        			<? $sl++;} if($itm==0){?>
						        			<td valign="top" rowspan="<? echo $rowspan_item[$job_no][$item_id];?>" width="70"><p><? 
											echo $row['ref_no'];?></p></td>
                                            <td valign="top" rowspan="<? echo $rowspan_item[$job_no][$item_id];?>" width="100"><p><? echo $garments_item[$item_id];?></p></td>
						        			<td valign="top" rowspan="<? echo $rowspan_item[$job_no][$item_id];?>" width="100"><p><? echo $lib_fab_desc[$job_no];?></p></td>
						        			<? } if($c==0){?>
						        			<td valign="top" rowspan="<? echo $rowspan_color[$job_no][$item_id][$color_id];?>" width="100"><p><? echo $lib_color[$color_id];?></p></td>
                                            <td valign="top" rowspan="<? echo $rowspan_color[$job_no][$item_id][$color_id];?>" width="100"><p><? echo $color_qty_array[$job_no][$item_id][$color_id];?></p></td>
						        			<? $c++;}?>
						        			<td width="100"><p><? echo $row['po_number'];?>/<? echo $po_qty_array[$job_no][$item_id][$color_id][$po_id];?></p></td>
						        			<td width="50"><p><? echo $unit_of_measurement[$row['order_uom']];?></p></td>
						        			<? if($itm==0)
						        			{?>
						        			<td valign="top" align="right" rowspan="<? echo $rowspan_item[$job_no][$item_id];?>" width="80"><p><? echo $item_qty_array[$job_no][$item_id];?></p></td>
						        			<? $tot_item_qty += $item_qty_array[$job_no][$item_id];$itm++;} if($j==0){?>					        			<td valign="top" rowspan="<? echo $rowspan_job[$job_no];?>" align="right" width="80"><p><? echo $job_qty_array[$job_no]['qty_pcs'];?></p></td>
						        			<td valign="top" rowspan="<? echo $rowspan_job[$job_no];?>" align="right" width="80"><p><? echo $job_qty_array[$job_no]['qty_set'];?></p></td>
                                            <td valign="top" rowspan="<? echo $rowspan_job[$job_no];?>" align="right" width="80"><p><? echo number_format($fin_fab_qty,2);?></p></td>
                                            <td valign="top" rowspan="<? echo $rowspan_job[$job_no];?>" align="right" width="80"><p><? echo number_format($grey_fab_qty,2);?></p></td>
                                            
						        			<? $j++;
						        			$tot_job_qty_pc += $job_qty_array[$job_no]['qty_pcs'];
						        			$tot_job_qty += $job_qty_array[$job_no]['qty_set'];
											
											$tot_fin_fab_qty += $fin_fab_qty;
						        			$tot_grey_fab_qty += $grey_fab_qty;
						        			}?>
                                            
						        			<td width="70" align="center"><p><? echo change_date_format($row['po_received_date']);?></p></td>
											<td width="70" align="center"><p><? echo change_date_format($row['pub_shipment_date']);?></p></td>
											<?	if($cbo_shipping_status==4){?>
											<td width="70" align="center"><p><?	echo $ex_factory_qty_arr[$po_id];?></p></td>
											<td width="70" align="center"><p><? 											
											$short_qnty=($job_qty_array[$job_no]['qty_pcs']-$ex_factory_qty_arr[$po_id]);
										
											if($short_qnty<0){
												echo " ";
											}else{
												echo $short_qnty;
											}
											?></p></td>
											<td width="70" align="center"><p><?
												$access_qnty=($ex_factory_qty_arr[$po_id]-$job_qty_array[$job_no]['qty_pcs']);
												if($access_qnty<0){
													echo " ";
												}else{
													echo $access_qnty;
												}
										?></p></td>
											<?}?>
						        			<td width="70"><p><? echo $row['txt_etd_ldd'];?></p></td>
						        			<td align="right" width="70"><p><? echo $row['unit_price'];?></p></td>
						        			<td align="right" width="70"><p><? echo number_format($row['po_total_price'],2);?></p></td>
						        			<td width="70"><p><? //echo $a;?></p></td>
						        			<td width="70"><p><? echo $row['is_confirmed']==1 ? "Confirmed" : "Projection" ;?></p></td>
						        			<td width="70"><p><? echo $order_position;?></p></td>
						        			<td width="100"><p><? echo $row['remarks'];?></p></td>
					        			</tr>
					        			<?
					        			$i++;
					        			$tot_fob_val += $row['po_total_price'];	
										//$tot_po_qty += $po_qty_array[$job_no][$item_id][$color_id][$po_id];
										//$tot_po_color_qty += $color_qty_array[$job_no][$item_id][$color_id];	
					        		}
					        	}
					        }
					    }
					    ?>
	        		</tbody>
	        	</table>
	        </div>
	        <!-- =============================== table footer start ================================ -->
	        <table width="<? echo $tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header" align="left">
	        		<tfoot>
	        			<tr>
	        				<th width="30"></th>
		        			<th width="80"></th>
		        			<th width="80"></th>
		        			<th width="80"></th>
		        			<th width="80"></th>
		        			<th width="80"></th>
		        			<th width="80"></th>
		        			<th width="50"></th>
		        			<th width="50"></th>
                            <th width="70"></th>
		        			<th width="100"></th>
		        			<th width="100"><? //echo $tot_po_color_qty;?></th>
                            <th width="100"><? echo $tot_po_color_qty;?></th>
		        			<th width="100"></th>
		        			<th width="100" title="POQty=<? echo $tot_po_qty;?>">Total</th>
		        			<th width="50"></th>
		        			<th width="80"><? echo number_format($tot_item_qty,0);?></th>
		        			<th width="80"><? echo number_format($tot_job_qty_pc,0);?></th>
		        			<th width="80"><? echo number_format($tot_job_qty,0);?></th>
                            <th width="80"><? echo number_format($tot_fin_fab_qty,0);?></th>
                            <th width="80"><? echo number_format($tot_grey_fab_qty,0);?></th>
                           
		        			<th width="70"></th>
		        			<th width="70"></th>
		        			<th width="70"></th>
							<? if($cbo_shipping_status==4){?>
								<th width="70"></th>
		        			    <th width="70"></th>
		        			    <th width="70"></th>
							<?}?>
		        			<th width="70"></th>
		        			<th width="70"><? echo number_format($tot_fob_val,2); ?></th>
		        			<th width="70"></th>
		        			<th width="70"></th>
		        			<th width="70"></th>
		        			<th width="100"></th>
	        			</tr>
	        		</tfoot>
	        	</table>
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
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename####$reportType";
	exit();
}
disconnect($con);
?>
