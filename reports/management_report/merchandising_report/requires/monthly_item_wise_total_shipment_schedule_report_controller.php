<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");


require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$date=date('Y-m-d');

if($action=="item_report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_garments_item=str_replace("'","",$cbo_garments_item);
	$cbo_date_cat_id=str_replace("'","",$cbo_date_cat_id);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);

	$cbo_month=str_replace("'","",$cbo_month_start);
	//$cbo_end_year_name=str_replace("'","",$cbo_year_end);
	$cbo_month_end=str_replace("'","",$cbo_month_end);
	$cbo_date_cat_id=str_replace("'","",$cbo_date_cat_id);
 		
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	if($db_type==2)
	{
		$s_date	= change_date_format($txt_date_from,'yyyy-mm-dd','-',1);
		$e_date	= change_date_format($txt_date_to,'yyyy-mm-dd','-',1);
	}
	$tot_month = datediff( 'm', $s_date,$e_date);
	for($i=0; $i<= $tot_month; $i++ )
	{
		$next_month	= month_add($s_date,$i);
		$month_arr[]= date("Y-m",strtotime($next_month));
	}

	if (str_replace("'","",$cbo_date_cat_id) ==1 && $s_date!='' && $e_date!='') $shipment_date_con = "and b.pub_shipment_date between '".$s_date."' and '".$e_date."'"; else $shipment_date_con ="";
	if (str_replace("'","",$cbo_date_cat_id) ==2 && $s_date!='' && $e_date!='') $shipment_date_con = "and c.country_ship_date between '".$s_date."' and '".$e_date."'"; else $shipment_date_con ="";
	if (str_replace("'","",$cbo_date_cat_id) ==3 && $s_date!='' && $e_date!='') $shipment_date_con = "and b.shipment_date between '".$s_date."' and '".$e_date."'"; else $shipment_date_con ="";
	if ($cbo_garments_item !=0) $garments_item_con = "and c.item_number_id in ($cbo_garments_item)"; else $garments_item_con ="";


	if($cbo_date_cat_id==1)//pub_shipment_date
	{
		$sql_po="SELECT a.job_no,a.order_uom,a.set_break_down,  b.pub_shipment_date,c.item_number_id,sum( c.order_quantity) as po_quantity
		FROM wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c
		WHERE a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and a.company_name in($cbo_company_id)  and b.pub_shipment_date between '$s_date' and '$e_date' and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 $garments_item_con $shipment_date_con group by   a.job_no,a.order_uom,a.set_break_down, b.pub_shipment_date,c.item_number_id order by b.pub_shipment_date ASC";
	}
	else if($cbo_date_cat_id==2)//country_ship_date
	{
		$sql_po="SELECT a.job_no,a.order_uom,a.set_break_down,c.country_ship_date,c.item_number_id,sum( c.order_quantity) as po_quantity
		FROM wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c
		WHERE a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and a.company_name in($cbo_company_id)  and c.country_ship_date between '$s_date' and '$e_date' and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 $garments_item_con $shipment_date_con group by a.job_no,a.order_uom,a.set_break_down,c.country_ship_date,c.item_number_id order by c.country_ship_date ASC";
	}
	else //shipment_date
	{
		$sql_po="SELECT a.job_no,a.set_break_down,a.order_uom,b.shipment_date,c.item_number_id,sum( c.order_quantity) as po_quantity
		FROM wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c
		WHERE a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and a.company_name in($cbo_company_id)  and b.shipment_date between '$s_date' and '$e_date' and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 $garments_item_con $shipment_date_con group by  a.job_no,a.set_break_down,a.order_uom,b.shipment_date,c.item_number_id order by b.shipment_date";
	}
	//echo $sql_po;die;
	$sql_data=sql_select($sql_po);
	$item_data_array=array();$date_array=array();$order_qty_array=array();$set_break_down_arr=array();
	foreach( $sql_data as $row)
	{
		$uom=$row[csf('order_uom')];
		
		if($cbo_date_cat_id==1)//pub_shipment_date
		{
			$shipdate=$row[csf("pub_shipment_date")];
			$shipdate_row=$row[csf("pub_shipment_date")];
		}
		if($cbo_date_cat_id==2)//country_ship_date
		{
			$shipdate=$row[csf("country_ship_date")];
			$shipdate_row=$row[csf("country_ship_date")];
			 
		}
		if($cbo_date_cat_id==3)//shipment_date
		{
			$shipdate=$row[csf("shipment_date")];
			$shipdate_row=$row[csf("shipment_date")];
		}
		$shipdate=strtotime($shipdate);
		$shipdate_cal=date('F-Y',$shipdate);

		$item_data_array[$shipdate_cal][$row[csf('item_number_id')]]['item']=$row[csf('item_number_id')];
		$date_array[$shipdate_cal][$shipdate]=$shipdate_row;
		$set_break_down_set_arr=array();
		$set_break_down_arr=explode('__',$row[csf("set_break_down")]);
		/*foreach($set_break_down_arr as $set_break_down){
			//list($item_id,$set,$smv)=explode('_',$set_break_down);
			$ex_set_data=explode('_',$set_break_down);
			$ex_item_ratio=$ex_set_data[1];
			
			$confirm_qty=$row[csf('po_quantity')]*$set;
		}*/
		for($i=0; $i<count($set_break_down_arr); $i++){
			$set_break_down_arr2=explode('_',$set_break_down_arr[$i]);
			$set_break_down_set_arr[$set_break_down_arr2[0]]=$set_break_down_arr2[1];

		}
		//echo $ex_item_ratio;
	//	if($row[csf('order_uom')]!=1){
			$item_data_date_array[$shipdate_cal][$shipdate][$row[csf('item_number_id')]]['qty']+=$row[csf('po_quantity')];
			//*$set_break_down_set_arr[$row[csf('item_number_id')]]
	//	}else{
				
			//$item_data_date_array[$shipdate_cal][$shipdate][$row[csf('item_number_id')]]['qty']=$row[csf('po_quantity')];
					
	//	}
		
	}
	///echo $set_break_down_arr[2];die;
	//echo "<pre>";print_r($set_break_down_set_arr);die;
	//unset($sql_data);
	
	
	foreach($item_data_array as $year_month=>$item_data) // for row span
	{
		$mon_row_span=0;
		 foreach($item_data as $item_id=>$buyer_data)
		 {
			$item_row_span=0;
			//foreach($buyer_data as $buyer_id=>$conf_qty)
			///{
				$mon_row_span++;
				$item_rowspan_arr++;
				$summary_confirm_qty_array[$year_month]+=$order_data_array_qty[$year_month][$item_id]['confQty'];
				$summary_projected_qty_array[$year_month]+=$projected_qty_array[$year_month][$item_id];

			//}
			$item_rowspan_arr[$year_month][$item_id]=$item_row_span;
			$mon_rowspan_arr[$year_month]=$mon_row_span;
		 }
	}
//echo "<pre>";	print_r($item_data_array);die;
//echo count($item_data_array);die;
 $width=200+$item_rowspan_arr*100;
 $colspan=2+$item_rowspan_arr;
 
	ob_start();
	?>
		<div style="margin:0 auto; width:<?=$width?>px; margin-left:10px;" align="right">
	    <table width="<?=$width?>" border="0" cellpadding="2" cellspacing="0">
            <thead>
                <tr class="form_caption">
                    <td colspan="<?=$colspan?>" align="center" style="font-size:16px; font-weight:bold" ><? echo "Monthly Total Shipment Schedule"; ?></td>
                </tr>
                <tr class="form_caption">
                    <td colspan="<?=$colspan?>" align="center" style="font-size:14px;">
                       <b><? echo $companyArr[str_replace("'","",$cbo_company_id)]; ?></b>
                    </td>
                </tr>
            </thead>
        </table>
		</div>
		<?
		foreach($month_arr as $month){
			list($y,$m)=explode('-',$month); $m=$m*1;
			?>
			<div style="margin:0 auto; width:<?=$width?>px; margin-left:10px;" align="right" id="scroll_body">
				<table width="<?=$width?>" border="0" cellpadding="2" cellspacing="0" id="table_body">
				<tr class="form_caption">
					<td colspan="<?=$colspan?>" align="left" style="font-size:16px; font-weight:bold">
							<b><? echo $months[$m].'-'.$y; ?></b>
					</td>
				</tr>
				<br />
				<div style="width:<?=$width?>px;" >
					<table cellspacing="0" width="<?=$width?>" border="1" rules="all" class="rpt_table" id="table_body">
						<thead>
							<tr>
								<th width="100">Ship Date</th>
								<?
										foreach($item_data_array[$months[$m].'-'.$y]  as $key=>$item)
										{
											echo "<th width='100'>". $garments_item[$item['item']]."</th>";
										}
								?>
								<th width="100">Total Quantity</th>
							</tr>
						</thead>
						<tbody>
								<?
								$i=1;$item_po_qty_arr=array();
								foreach($date_array[$months[$m].'-'.$y] as $date=>$val){
									//$shipdate=$row[csf("pub_shipment_date")];
									
									$bgcolor			=($i%2==0)?"#E9F3FF":"#FFFFFF";
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td width="100" align="left"><? echo date("d-m-y",$date); ?></td>
										<?
										$total_po_qty=0;
										foreach($item_data_array[$months[$m].'-'.$y]  as $item=>$val)
										{//$shipdate=strtotime($shipdate);
										$qty_total=$item_data_date_array[$months[$m].'-'.$y][$date][$item]['qty'];?>
											<td width="100" align="left"><? if($item!="") echo $qty_total; else echo ""; ?></td><?
											$total_po_qty+=$qty_total;
											$item_po_qty_arr[$months[$m].'-'.$y][$item]+=$qty_total;
											
										}?>
											<td width="100" align="left"><?=$total_po_qty; ?></td>
										
									</tr>
									<?
									$i++;
								}

								?>
									<tr>
										<td>Sub Total</td>
										<?$grandtot=0;
										foreach($item_data_array[$months[$m].'-'.$y]  as $item=>$val)
										{?>
										<td><?=$item_po_qty_arr[$months[$m].'-'.$y][$item];?></td>
										<?$grandtot+=$item_po_qty_arr[$months[$m].'-'.$y][$item];}?>
										<td><?$grandtot;?></td>
									</tr>
						</tbody>
					</table>
					<br>
					<table cellspacing="0" width="250" border="1" rules="all" class="rpt_table" id="table_body" align="left">
						<tr>
							<td width="100"><b>Total Of Month<b></td>
							<td width="100"><?=$grandtot; ?></td>
							<td width="50"><?echo "PCS"; ?></td>
						</tr>
					</table>
					<br>
				</div>
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
	//$filename=$user_id."_".$name.".xls";
	echo "****$filename****show";
	ob_end_flush();
	exit();
}
?>