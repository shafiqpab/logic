<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];


if($action=="load_drop_down_buyer")
{
	if(str_replace("'","",$data)>0) $com_cond=" and c.tag_company=$data ";
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.buyer_name, buy.id from lib_buyer buy, lib_buyer_tag_company c where buy.id=c.buyer_id and  buy.status_active =1 and buy.is_deleted=0 $com_cond $buyer_cond group by buy.buyer_name, buy.id order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );   	 
	exit();
}
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$date_category=str_replace("'","",$cbo_date_category);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$reportType=str_replace("'","",$reportType);

	$shipdate_cond = "";
	$shipdate = "";
	if($txt_date_from!="")
	{
		if($date_category==1)
		{
			$shipdate_cond = " and c.country_ship_date between '$txt_date_from' and '$txt_date_to'";
			$shipdate = "c.country_ship_date";
		}
		elseif ($date_category==1) 
		{
			$shipdate_cond = " and b.pub_shipment_date between '$txt_date_from' and '$txt_date_to'";
			$shipdate = "b.pub_shipment_date";
		}
		else
		{
			$shipdate_cond = " and b.shipment_date between '$txt_date_from' and '$txt_date_to'";
			$shipdate = "b.shipment_date";
		}
		
	}
	
	
	$day_diff=datediff( 'd', $txt_date_from, $txt_date_to);
	//echo $day_diff;die;
	for($i=1;$i<=$day_diff;$i++)
	{
		if($i==1) $new_date=date('Y-m-d',strtotime($txt_date_from)); else $new_date=add_date($new_date,1);
		$month_arr[date("Y-m",strtotime($new_date))]=date("Y-m",strtotime($new_date));
		$month_year_arr[ date("Y-m",strtotime($new_date))]['month']=date("F",strtotime($new_date));
		$month_year_arr[ date("Y-m",strtotime($new_date))]['year']=date("Y",strtotime($new_date));
	}
	//echo "<pre>";print_r($month_year_arr);die;
	
	$sql_cond="";
	if($cbo_company_name>0) $sql_cond=" and a.company_id=$cbo_company_name ";
	if($cbo_buyer_name>0) $sql_cond.=" and a.buyer_id=$cbo_buyer_name "; 
	
	$sql_sales="SELECT a.company_id, a.buyer_id, b.sales_target_date ,b.sales_target_qty, b.sales_target_value 
	from wo_sales_target_mst  a,wo_sales_target_dtls b where a.id=b.sales_target_mst_id and a.status_active=1 and a.is_deleted=0 and b.sales_target_date between '$txt_date_from' and  '$txt_date_to' $sql_cond";
	//echo $sql_sales;die;
	$sql_sales_result=sql_select($sql_sales);
	$sale_order_data_arr=array();
	foreach($sql_sales_result as $row)
	{
		$sale_order_data_arr[$row[csf("company_id")]][date("Y-m",strtotime($row[csf("sales_target_date")]))]['sales_target_qty']+=$row[csf("sales_target_qty")];
		$sale_order_data_arr[$row[csf("company_id")]][date("Y-m",strtotime($row[csf("sales_target_date")]))]['sales_target_value']+=$row[csf("sales_target_value")];
		$sale_order_data_arr[$row[csf("company_id")]][date("Y-m",strtotime($row[csf("sales_target_date")]))]['buyer_id']=$row[csf("buyer_id")];
	}
	
	$sql_cond="";
	if($cbo_company_name>0) $sql_cond=" and a.company_name=$cbo_company_name ";
	if($cbo_buyer_name>0) $sql_cond.=" and a.buyer_name=$cbo_buyer_name "; 
	
	/*$sql_order= "select a.company_name, a.buyer_name, b.id as po_id, b.shipment_date, c.order_quantity as po_quantity, c.order_total as po_amount
	from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c 
	where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.job_no_mst=b.job_no_mst and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and b.shipment_date between '$txt_date_from' and  '$txt_date_to' $sql_cond";*/
	
	$sql_order= "SELECT a.company_name, a.buyer_name, b.id as po_id, $shipdate as shipment_date, c.ORDER_QUANTITY as po_quantity, c.ORDER_TOTAL as po_amount
	from wo_po_details_master a, wo_po_break_down b, WO_PO_COLOR_SIZE_BREAKDOWN c
	where a.id=b.job_id and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.is_confirmed=1 $shipdate_cond $sql_cond";
	
	// echo $sql_order;
	$sql_order_result=sql_select($sql_order);$po_id_arr=array();
	
	foreach ($sql_order_result as $row)
	{
		$po_id_arr[$row[csf("po_id")]]=$row[csf("po_id")];
		$sale_order_data_arr[$row[csf("company_name")]][date("Y-m",strtotime($row[csf("shipment_date")]))]['po_quantity']+=$row[csf("po_quantity")];
		$sale_order_data_arr[$row[csf("company_name")]][date("Y-m",strtotime($row[csf("shipment_date")]))]['po_amount']+=$row[csf("po_amount")];
		$sale_order_data_arr[$row[csf("company_name")]][date("Y-m",strtotime($row[csf("shipment_date")]))]['buyer_name']=$row[csf("buyer_name")];
		$sale_order_data_arr[$row[csf("company_name")]][date("Y-m",strtotime($row[csf("shipment_date")]))]['po_id'].=$row[csf("po_id")].",";
	}
	
	$sql_prod= "SELECT a.company_name, a.buyer_name, $shipdate as shipment_date, e.production_qnty as production_quantity, (e.production_qnty*c.ORDER_RATE) as production_amount
	from wo_po_details_master a, wo_po_break_down b, WO_PO_COLOR_SIZE_BREAKDOWN c, pro_garments_production_mst d, pro_garments_production_dtls e
	where a.id=b.job_id and b.id=c.po_break_down_id and b.id=d.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and d.production_type=5 and b.is_confirmed=1 $shipdate_cond $sql_cond";
	// echo $sql_prod;
	$sql_prod_result=sql_select($sql_prod);
	foreach($sql_prod_result as $row)
	{
		$sale_order_data_arr[$row[csf("company_name")]][date("Y-m",strtotime($row[csf("shipment_date")]))]['production_quantity']+=$row[csf("production_quantity")];
		$sale_order_data_arr[$row[csf("company_name")]][date("Y-m",strtotime($row[csf("shipment_date")]))]['production_amount']+=$row[csf("production_amount")];
	}
	
	$sql_exfact= "SELECT a.company_name, a.buyer_name, $shipdate as shipment_date, e.PRODUCTION_QNTY as ex_factory_qnty, (e.PRODUCTION_QNTY*c.ORDER_RATE) as ex_factory_amount
	from wo_po_details_master a, wo_po_break_down b, WO_PO_COLOR_SIZE_BREAKDOWN c, pro_ex_factory_mst d , pro_ex_factory_dtls e
	where a.id=b.job_id and b.id=c.po_break_down_id and b.id=d.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and b.is_confirmed=1 $shipdate_cond $sql_cond";
	// echo $sql_exfact;
	$sql_exfact_result=sql_select($sql_exfact);
	foreach($sql_exfact_result as $row)
	{
		$sale_order_data_arr[$row[csf("company_name")]][date("Y-m",strtotime($row[csf("shipment_date")]))]['ex_factory_qnty']+=$row[csf("ex_factory_qnty")];
		$sale_order_data_arr[$row[csf("company_name")]][date("Y-m",strtotime($row[csf("shipment_date")]))]['ex_factory_amount']+=$row[csf("ex_factory_amount")];
	}
	
	ob_start();
	$table_width=200+count($month_arr)*500;
	$div_width=220+count($month_arr)*500;
	
	
	if($reportType==1)
	{
		?>
        <div style="width:<? echo $div_width;?>px;">
                <table width="<? echo $table_width;?>"  cellspacing="0"  align="center">
                     <tr align="center">
                        <td colspan="10" align="center" class="form_caption"> <strong style="font-size:15px;">Total Summary</strong></td>
                     </tr>
                </table>
                <table width="<? echo $table_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1">
                    <thead>
                        <tr>
                            <th width="50" rowspan="2">SL No.</th>
                            <th width="150" rowspan="2">Company</th>
                            <?
                            foreach($month_arr as $row)
                            {
                                ?>
                                <th colspan="5"><? echo $month_year_arr[$row][month].",".$month_year_arr[$row][year];?></th>
                                <?
                            }
                            ?>
                        </tr>
                        <tr>
                        <?
                        foreach($month_arr as $row)
                        {
                            ?>
                            <th width="100">Forecasting Qnty.</th>
                            <th width="100">Order Qnty.</th>
                            <th width="100">Sew. Production Qnty.</th>
                            <th width="100">Ex-Factory Qnty.</th>
                            <th width="100">Pending Qnty.</th>
                            <?
                        }
                        ?>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$i=1;
					foreach($sale_order_data_arr as $com_id=>$value)
					{
						if ($ii%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td align="center"><? echo $i; ?></td>
                            <td><p><? echo $company_library[$com_id]; ?>&nbsp;</p></td>
                            <?
							$pending_qnty=0;
							foreach($month_arr as $row)
                        	{
								?> 
                                <td align="right"><? echo number_format($sale_order_data_arr[$com_id][$row]["sales_target_qty"],2); ?></td>
                                <td align="right"><? echo number_format($sale_order_data_arr[$com_id][$row]["po_quantity"],2); ?></td>
                                <td align="right"><? echo number_format($sale_order_data_arr[$com_id][$row]["production_quantity"],2); ?></td>
                                <td align="right"><? echo number_format($sale_order_data_arr[$com_id][$row]["ex_factory_qnty"],2); ?></td>
                                <td align="right"><? $pending_qnty=$sale_order_data_arr[$com_id][$row]["po_quantity"]-$sale_order_data_arr[$com_id][$row]["ex_factory_qnty"]; echo number_format($pending_qnty,2);  ?></td>
                                <?
								$month_wise_data[$row]["sales_target_qty"]+=$sale_order_data_arr[$com_id][$row]["sales_target_qty"];
								$month_wise_data[$row]["po_quantity"]+=$sale_order_data_arr[$com_id][$row]["po_quantity"];
								$month_wise_data[$row]["production_quantity"]+=$sale_order_data_arr[$com_id][$row]["production_quantity"];
								$month_wise_data[$row]["ex_factory_qnty"]+=$sale_order_data_arr[$com_id][$row]["ex_factory_qnty"];
								$month_wise_data[$row]["pending_qnty"]+=$pending_qnty;
							}
							?>
                        </tr>
                        <?
						$i++;
					}
					?>
                    </tbody>
                    <tfoot>
                    	<tr>
                        	<th></th>
                            <th align="right">Grand Total :</th>
                            <?
							foreach($month_arr as $row)
                        	{
								?> 
                                <th align="right"><? echo number_format($month_wise_data[$row]["sales_target_qty"],2); ?></th>
                                <th align="right"><? echo number_format($month_wise_data[$row]["po_quantity"],2); ?></th>
                                <th align="right"><? echo number_format($month_wise_data[$row]["production_quantity"],2); ?></th>
                                <th align="right"><? echo number_format($month_wise_data[$row]["ex_factory_qnty"],2); ?></th>
                                <th align="right"><? echo number_format($month_wise_data[$row]["pending_qnty"],2); ?></th>
                                <?
							}
							?>
                        </tr>
                    </tfoot>
             	</table>
        </div>
        <?
	}
	else if($reportType==2)
	{
		?>
        <div style="width:<? echo $div_width;?>px;">
                <table width="<? echo $table_width;?>"  cellspacing="0"  align="center">
                     <tr align="center">
                        <td colspan="10" align="center" class="form_caption"> <strong style="font-size:15px;">Total Summary</strong></td>
                     </tr>
                </table>
                <table width="<? echo $table_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1">
                    <thead>
                        <tr>
                            <th width="50" rowspan="2">SL No.</th>
                            <th width="150" rowspan="2">Company</th>
                            <?
                            foreach($month_arr as $row)
                            {
                                ?>
                                <th colspan="5"><? echo $month_year_arr[$row][month].",".$month_year_arr[$row][year];?></th>
                                <?
                            }
                            ?>
                        </tr>
                        <tr>
                        <?
                        foreach($month_arr as $row)
                        {
                            ?>
                            <th width="100">Forecasting Value</th>
                            <th width="100">Order Value</th>
                            <th width="100">Sew. Production Value</th>
                            <th width="100">Ex-Factory Value</th>
                            <th width="100">Pending Value</th>
                            <?
                        }
                        ?>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$i=1;
					foreach($sale_order_data_arr as $com_id=>$value)
					{
						if ($ii%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td align="center"><? echo $i; ?></td>
                            <td><p><? echo $company_library[$com_id]; ?>&nbsp;</p></td>
                            <?
							$pending_qnty=0;
							foreach($month_arr as $row)
                        	{
								?> 
                                <td align="right"><? echo number_format($sale_order_data_arr[$com_id][$row]["sales_target_value"],2); ?></td>
                                <td align="right"><? echo number_format($sale_order_data_arr[$com_id][$row]["po_amount"],2); ?></td>
                                <td align="right"><? echo number_format($sale_order_data_arr[$com_id][$row]["production_amount"],2); ?></td>
                                <td align="right"><? echo number_format($sale_order_data_arr[$com_id][$row]["ex_factory_amount"],2); ?></td>
                                <td align="right"><? $pending_amt=$sale_order_data_arr[$com_id][$row]["po_amount"]-$sale_order_data_arr[$com_id][$row]["ex_factory_amount"]; echo number_format($pending_amt,2);  ?></td>
                                <?
								$month_wise_data[$row]["sales_target_value"]+=$sale_order_data_arr[$com_id][$row]["sales_target_value"];
								$month_wise_data[$row]["po_amount"]+=$sale_order_data_arr[$com_id][$row]["po_amount"];
								$month_wise_data[$row]["production_amount"]+=$sale_order_data_arr[$com_id][$row]["production_amount"];
								$month_wise_data[$row]["ex_factory_amount"]+=$sale_order_data_arr[$com_id][$row]["ex_factory_amount"];
								$month_wise_data[$row]["pending_amt"]+=$pending_amt;
							}
							?>
                        </tr>
                        <?
						$i++;
					}
					?>
                    </tbody>
                    <tfoot>
                    	<tr>
                        	<th></th>
                            <th align="right">Grand Total :</th>
                            <?
							foreach($month_arr as $row)
                        	{
								?> 
                                <th align="right"><? echo number_format($month_wise_data[$row]["sales_target_value"],2); ?></th>
                                <th align="right"><? echo number_format($month_wise_data[$row]["po_amount"],2); ?></th>
                                <th align="right"><? echo number_format($month_wise_data[$row]["production_amount"],2); ?></th>
                                <th align="right"><? echo number_format($month_wise_data[$row]["ex_factory_amount"],2); ?></th>
                                <th align="right"><? echo number_format($month_wise_data[$row]["pending_amt"],2); ?></th>
                                <?
							}
							?>
                        </tr>
                    </tfoot>
             	</table>
        </div>
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
?>
