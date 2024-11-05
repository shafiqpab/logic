<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
if (!function_exists('pre'))
{
  function pre($array)
  {
    echo "<pre>";
    print_r($array);
    echo "</pre>";
  }
}
if (!function_exists('num_format'))
{
  function is_num($num)
  {
    return (is_infinite($num) || is_nan($num)) ? 0 : $num;
  }
}
$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action']; 

if ($action == "load_drop_down_buyer") {

	echo create_drop_down("cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
} 

if ($action=='report_generate')
{
  $process = array(&$_POST);
  extract(check_magic_quote_gpc($process));

  $company_id  = str_replace("'","",$cbo_company_id);
  $buyer_id    = str_replace("'","",$cbo_buyer_name); 
  $form_date   = str_replace("'","",$txt_date_from);
  $to_date     = str_replace("'","",$txt_date_to);

  // ============================================================================================================
  //												Library
  // ============================================================================================================

  if ($type==1) //Show
  {

    // =========================================================================================================
                                            //ORDER ENTRY DATA
    // =========================================================================================================
    $sql_cond ="";
    $sql_cond .= $company_id  ? " and a.company_name in ($company_id)" : "";
    $sql_cond .= $buyer_id    ? " and a.buyer_name in ($buyer_id)"     : "";
    $sql_cond .= ($form_date && $to_date) ?" and b.pub_shipment_date between '".change_date_format($form_date,'dd-mm-yyyy','-',1)."' and '".change_date_format($to_date,'dd-mm-yyyy','-',1)."'"  : "";
    $order_sql="SELECT a.buyer_name, a.company_name,b.po_quantity as po_qty,b.id as po_id from wo_po_details_master a,wo_po_break_down b where a.id=b.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond";

    $order_sql_res = sql_select($order_sql); 
    $main_data_array = array(); 
    $po_id_array = array(); 
    foreach ($order_sql_res as $v) 
    {
        $po_id_array[$v['PO_ID']] = $v['PO_ID'];
        $main_data_array[$v['BUYER_NAME']]['PO_QTY'] += $v['PO_QTY'];
    }
    // pre($po_id_array); die;
    // echo $order_sql; die;
    // =========================================================================================================
                                            //EX FACTORY DATA
    // =========================================================================================================
    $ex_fact_sql_cond ="";
    $ex_fact_sql_cond .= $company_id  ? " and a.company_id in ($company_id)" : "";
    $ex_fact_sql_cond .= $buyer_id    ? " and a.buyer_id in ($buyer_id)"     : "";
    $ex_fact_sql_cond .= ($form_date && $to_date) ?" and b.ex_factory_date between '".change_date_format($form_date,'dd-mm-yyyy','-',1)."' and '".change_date_format($to_date,'dd-mm-yyyy','-',1)."'"  : "";

    $sql_ex_fact = "SELECT a.buyer_id,b.po_break_down_id as po_id,b.ex_factory_qnty,b.shiping_status
    FROM pro_ex_factory_delivery_mst a,pro_ex_factory_mst b
    WHERE  a.id=b.delivery_mst_id $ex_fact_sql_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
    // echo $sql_ex_fact; die; 
    $ex_fact_res = sql_select($sql_ex_fact); 
    foreach ($ex_fact_res as $v) 
    { 
        $po_id_array[$v['PO_ID']] = $v['PO_ID'];
        $main_data_array[$v['BUYER_ID']]['EX_FACT_QTY'] += $v['EX_FACTORY_QNTY'];
        $main_data_array[$v['BUYER_ID']]['EX_FACT'][$v['SHIPING_STATUS']] += $v['EX_FACTORY_QNTY'];
    }

    //=================================== LIBRARY ARRAY ====================================
    $buyer_array=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');


    // pre($main_data_array); die;
    //=================================== CLEAR TEMP ENGINE ====================================
    $con = connect();
    execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form = 176 and ref_from in(1)");
    oci_commit($con);

    //=================================== INSERT LINE ID INTO TEMP ENGINE ====================================
    fnc_tempengine("gbl_temp_engine", $user_id, 176, 1,$po_id_array, $empty_arr);
    /* 
    $po_qty_sql = "select c.po_break_down_id as po_id,c.order_quantity as po_qty,c.order_total as po_price,item_number_id as item,order_rate  from wo_po_color_size_breakdown c,gbl_temp_engine tmp where  c.po_break_down_id=tmp.ref_val and tmp.user_id=$user_id and tmp.entry_form=176 and tmp.ref_from=1 and c.status_active=1 and c.is_deleted=0 ";
    
    //  pre($fob_arr); die;
     */
    //=================================== CLEAR TEMP ENGINE ====================================
    execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form = 176 and ref_from in(1)");
    oci_commit($con);
    disconnect($con); 

    $width = 2400;  
    ob_start();
    // pre($prod_arr); die;
    ?>  
        <style>
            #summary_data td,th{
                word-break: break-all;
            }
        </style>
        <fieldset> 
			<div width="100%" id="report_container3"> 
				<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="tbl_caption" align="center">
					<thead class="form_caption" > 
						<tr>
							<td colspan="28" align="center"> <h3 style="font-size:18px; font-weight:bold; padding:10px 0"> Yearly Summary Report </h3> </td>
						</tr>  
					</thead>
				</table>	
				<div align="center" style="height:auto; width:<? echo $width+20;?>px; margin:0 auto; padding:0;" id="summary_data">  
					<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
						<thead class="form_caption" >	  
							<tr>
								<th width="30">Sl.</th>
								<th width="80">Buyer</th>
								<th width="120" title="Total order qty (Pcs Qty) (date range- Published Ship date)">Scheduled Ex-Factory Qty</th>
								<th width="120" title="Total shipment quantity (date range of listed oreders shipment)">Actual Ex-Factory Qty</th>
								<th width="100" title="If  Full shipped/closed order have some balace qty then show this qty.">Short Shipped Qty</th>
								<th width="100" title="Extra shipped Qty for Closed order">Extra Shipped Qty</th> 
								<th width="80" title="Value will be the difference between shot shipment & extra Shipment qty. extra ship qty will be (+) qty & shot qty will be (-)">Total</th>
								<th width="120">Pending Shipment</th>
								<th width="120">No of Files Received</th>
								<th width="100">No of On-time Files Received</th>
								<th width="100">On-Time %</th>
								<th width="120">No of Overdue Files  Upto that Week</th>
								<th width="120">Planned Production Qty as Per Loading</th>
								<th width="120">Actual Production Qty</th>
								<th width="120">Over/Under Achieved Qty </th>
								<th width="100">No of Shipment</th>
								<th width="80">No of On-Time Shipment</th>
								<th width="80">No of Delay Shipment</th>
								<th width="100">Pending Shipment</th>
								<th width="100">On-Time %</th>
								<th width="100">No of Final Inspection</th>
								<th width="100">No of Inspection Failed</th>
								<th width="100">Passed %</th>
								<th width="100">Airfrieght Qty</th>
								<th width="100">Discount Qty</th>
								<th width="100">Airfrieght USD</th>
								<th width="100">Discount USD</th>
								<th width="100">Remarks</th>
							</tr>
						</thead>
					</table>
					<div style="width:<?= $width+20;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
						<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body" width="<?= $width; ?>" rules="all" align="left">
							<tbody>
								<?
								$i = 0 ; 
								$total_qc_qty=$total_ex_fact_qty=$total_full_delivery_qty = 0 ;  
								foreach ($main_data_array as $buyer_id => $v) 
								{  
                                    $po_qty                 = $v['PO_QTY'];
                                    $ex_fact_qty            = $v['EX_FACT_QTY'];
                                    $full_pending_qty       = $v['EX_FACT'][1];
                                    $partial_delivery_qty   = $v['EX_FACT'][2];
                                    $full_delivery_qty      = $v['EX_FACT'][3];
                                    $short_ship_qty         = $full_delivery_qty - $po_qty;
                                    $short_ship_qty         = $short_ship_qty<0 ? $short_ship_qty : "";
                                    $extra_ship_qty         = $ex_fact_qty - $po_qty;
                                    $short_ship_qty         = $extra_ship_qty > 0 ? $extra_ship_qty : "";
                                    $total_balance          = $short_ship_qty + $extra_ship_qty;

                                    // TOTAL CALCULATION
                                    $total_po_qty               += $po_qty;
                                    $total_ex_fact_qty          += $ex_fact_qty;
                                    $total_full_delivery_qty    += $full_delivery_qty;


									if ($i % 2 == 0)  $bgcolor = "#E9F3FF";  else $bgcolor = "#FFFFFF";
									?>
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
											<td width="30"> <?= ++$i; ?> </td>
											<td width="80"><?= $buyer_array[$buyer_id] ?></td>
                                            <td width="120" align="right"><?= number_format($po_qty) ?></td> 
                                            <td width="120" align="right"><?= number_format($ex_fact_qty) ?></td>  
                                            <td width="100" align="right"><?= number_format($short_ship_qty) ?></td> 
                                            <td width="100" align="right"><?= number_format($extra_ship_qty) ?></td> 
                                            <td width="80" align="right"><?= number_format($total_balance )?></td> 
                                            <td width="120"></td>
                                            <td width="120"></td>
                                            <td width="100"></td>
                                            <td width="100"></td>
                                            <td width="120"></td>
                                            <td width="120"></td>
                                            <td width="120"></td>
                                            <td width="120"></td>
                                            <td width="100"></td>
                                            <td width="80"></td>
                                            <td width="80"></td>
                                            <td width="100"></td>
                                            <td width="100"></td>
                                            <td width="100"></td>
                                            <td width="100"></td>
                                            <td width="100"></td>
                                            <td width="100"></td>
                                            <td width="100"></td>
                                            <td width="100"></td>
                                            <td width="100"></td>
                                            <td width="100"></td>
										</tr> 
									<? 
								}
								?>
							</tbody> 
						</table> 
					</div>
					<div style="width:<?= $width+20;?>px;float:left;">
						<table style="float:left;" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" width="<?= $width;?>">
							<tfoot>
								<tr>
                                    <th width="30"></th>
                                    <th width="80">TOTAL:</th>
                                    <th width="120" align="right"><?= number_format($total_po_qty) ?></th> 
                                    <th width="120" align="right"><?= number_format($total_ex_fact_qty) ?></th> 
                                    <th width="100" align="right"><?= number_format($total_full_delivery_qty) ?></th> 
                                    <th width="100"></th>
                                    <th width="80"></th>
                                    <th width="120"></th>
                                    <th width="120"></th>
                                    <th width="100"></th>
                                    <th width="100"></th>
                                    <th width="120"></th>
                                    <th width="120"></th>
                                    <th width="120"></th>
                                    <th width="120"></th>
                                    <th width="100"></th>
                                    <th width="80"></th>
                                    <th width="80"></th>
                                    <th width="100"></th>
                                    <th width="100"></th>
                                    <th width="100"></th>
                                    <th width="100"></th>
                                    <th width="100"></th>
                                    <th width="100"></th>
                                    <th width="100"></th>
                                    <th width="100"></th>
                                    <th width="100"></th>
                                    <th width="100"></th>  
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</fieldset>
    <?
  }

  foreach (glob($user_id."_*.xls") as $filename)
	{
		@unlink($filename);
	}
	$name=$user_id."_".time().".xls";
	$create_new_excel = fopen($name, 'w');
	$is_created = fwrite($create_new_excel,ob_get_contents());
	//$new_link=create_delete_report_file( $html, 1, 1, "../../../" );
	echo "####".$name;
	exit();
}  
?>