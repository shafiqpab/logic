<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_name=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
    echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond  group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "" );     	 
	exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_allocation_company_id=str_replace("'","",$cbo_allocation_company_id);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);

	
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$location_library=return_library_array( "select id, location_name from lib_location",'id','location_name');
	//$line_name_library=return_library_array( "select id, line_name from lib_sewing_line",'id','line_name');
	//--------------------------------------------------------------------------------------------------------------------
	
	if(str_replace("'","",$cbo_buyer_name)==0)
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
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}
	
	$allc_comp="  and company_id='$cbo_allocation_company_id'";
	if( $cbo_company_id!=0 )
	{
		$sql_exe=sql_select("select a.style_ref_no,b.id,a.buyer_name, b.unit_price,b.po_number,b.po_quantity,a.ship_mode,c.cutup_date from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c  where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and c.cutup_date between '$txt_date_from' and '$txt_date_to'  and a.company_name=$cbo_company_id $buyer_id_cond");
		foreach($sql_exe as $row)
		{
			$order_inf[$row[csf("id")]]['po_number']= $row[csf("po_number")];
			$order_inf[$row[csf("id")]]['style_ref_no']= $row[csf("style_ref_no")];
			$order_inf[$row[csf("id")]]['unit_price']= $row[csf("unit_price")];
			$order_inf[$row[csf("id")]]['po_quantity']= $row[csf("po_quantity")];
			$order_inf[$row[csf("id")]]['ship_mode']= $row[csf("ship_mode")];
			$order_inf[$row[csf("id")]]['cutup_date']= $row[csf("cutup_date")];
			$bid[$row[csf("id")]]=$row[csf("id")];
			$buyer_powise[$row[csf("id")]]=$row[csf("buyer_name")];
			
			
		}
		//echo '<pre>';
		//print_r($row[csf("buyer_name")]); //die;
		$poid=implode(",",$bid);
		if( $cbo_allocation_company_id==0 ) $allc_comp="";
		$po_cond=" and po_no in ($poid)";
	}
	
	 $sql_exe=sql_select("select cut_off_date from ppl_order_allocation_mst where cut_off_date between '$txt_date_from' and '$txt_date_to' $allc_comp $po_cond");
	
	foreach($sql_exe as $row)
	{
		$cutoff[date("d",strtotime($row[csf("cut_off_date")]))]= $row[csf("cut_off_date")];
	}
	ksort($cutoff);
	$sql="select job_no,po_no,company_id,location_name, ";
	$i=0;
	foreach($cutoff as $cadys=>$cdate)
	{
		$i++;
		if(count($cutoff)==$i)
			$sql.=" sum(CASE WHEN cut_off_date='$cdate' THEN allocated_qty END) AS sum".$cadys." ";
		else
			$sql.=" sum(CASE WHEN cut_off_date='$cdate' THEN allocated_qty END) AS sum".$cadys.", ";
	}

	$sql.="  from ppl_order_allocation_mst where cut_off_date between '$txt_date_from' and '$txt_date_to' $allc_comp $po_cond group by company_id,location_name,job_no,po_no";
	
	$sql_exe=sql_select($sql);
	foreach($sql_exe as $rows)
	{
		$bids=$buyer_powise[$rows[csf("po_no")]];
		$all_data[$rows[csf("company_id")]][$rows[csf("location_name")]][$bids][$rows[csf("po_no")]]['job_no']=$rows[csf("job_no")];
		
		foreach($cutoff as $cadys=>$cdate)
		{
			$all_data[$rows[csf("company_id")]][$rows[csf("location_name")]][$bids][$rows[csf("po_no")]]['all_qnty'] +=$rows[csf("sum".$cadys)];
			$all_data[$rows[csf("company_id")]][$rows[csf("location_name")]][$bids][$rows[csf("po_no")]]['qnty'.$cadys]=$rows[csf("sum".$cadys)];
			
			$summery_comp[$rows[csf("company_id")]]['qnty'.$cadys] +=$rows[csf("sum".$cadys)];
			$summery_loca[$rows[csf("company_id")]][$rows[csf("location_name")]]['qnty'.$cadys] +=$rows[csf("sum".$cadys)];
			$summery_buyer[$rows[csf("company_id")]][$rows[csf("location_name")]][$bids]['qnty'.$cadys] +=$rows[csf("sum".$cadys)];
		}
		//$ponos[$rows[csf("po_no")]]=$rows[csf("po_no")];	
		$ponos[$rows[csf("po_no")]]=$rows[csf("po_no")];
	}
	if($poid=='')
	{
		$sql_exe=sql_select("select a.style_ref_no,b.id,a.buyer_name, b.unit_price,b.po_number,b.po_quantity,a.ship_mode,c.cutup_date from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c  where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and b.id in (".implode(",",$ponos).")");
		foreach($sql_exe as $row)
		{
			$order_inf[$row[csf("id")]]['po_number']= $row[csf("po_number")];
			$order_inf[$row[csf("id")]]['style_ref_no']= $row[csf("style_ref_no")];
			$order_inf[$row[csf("id")]]['unit_price']= $row[csf("unit_price")];
			$order_inf[$row[csf("id")]]['po_quantity']= $row[csf("po_quantity")];
			$order_inf[$row[csf("id")]]['ship_mode']= $row[csf("ship_mode")];
			$order_inf[$row[csf("id")]]['cutup_date']= $row[csf("cutup_date")];
			$buyer_powise[$row[csf("id")]]=$row[csf("buyer_name")];
		}
	}
	
	
	
	$tbl_wid=790+(count($cutoff)*80 );
	$tbl_widSer=790+(count($cutoff)*80 );
	
	$col=8+(count($cutoff));
	
	ob_start();
	?>
	<div style="width:100%" align="center">
		<table width="<? echo $tbl_wid; ?>" cellpadding="0" border="1" cellspacing="0" rules="all" class="rpt_table">
			<thead>
				<tr>
					<th colspan="<? echo $col;?>" align="center" valign="middle" >Factory wise Order Allocation Report for the month of <? echo date("M-Y",strtotime($txt_date_from)); ?></th>
				</tr>
				<tr>
					<th width="50">SL No</th>
					<th width="80">Job No</th>
					<th width="80">PO Number</th>
					<th width="100">Style No</th>
					<th width="60">Rate</th>
					<th width="100">Ship Mode</th>
					<th width="100">Total PO Qnty</th>
					<?
					foreach($cutoff as $cadys=>$cdate)
					{
						?>
						<th width="80"><? echo $cadys; ?></th>
						<?
					}
					?>
					<th>Remarks</th>

				</tr>
			</thead>
		</table>
<div style="width:<? echo $tbl_wid; ?>px; max-height:320px; overflow-y:scroll" id="scroll_body" align="left">
<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_widSer; ?>" class="rpt_table" id="tbl_list_search">  
	<tbody>
		<?
		$i=1;

		foreach($all_data as $comp=>$rdatas)
		{
			?>
			<tr>
				<td colspan="<? echo $col;?>" bgcolor="#EFEFEF">
					<strong><? echo "Company Name: ".$company_library[$comp];?> </strong>
				</td>	
			</tr>
			<?
			foreach($rdatas as $loca=>$rdatap)
			{
				?>
				<tr bgcolor="#EFEFEF">
					<td colspan="<? echo $col;?>" bgcolor="#EFEFEF">
						<? echo "Location Name: ".$location_library[$loca];?> 
					</td>	
				</tr>
				<?
				foreach($rdatap as $buyer=>$rdatab)
				{
					?>
					<tr>
						<td colspan="<? echo $col;?>" bgcolor="#EFEFEF">
							<? echo "Buyer Name: ".$buyer_arr[$buyer];?> 
						</td>	
					</tr>
					<?
					foreach($rdatab as $pono=>$rdata)
					{
						?>
						<tr>
							<td width="50"><? echo $i++; ?></td>
							<td width="80" style="word-break: break-all;"><? echo $rdata['job_no']; ?></p></td>
							<td width="80" style="word-break: break-all;"><? echo $order_inf[$pono]['po_number']; ?></p></td>
							<td width="100"><? echo $order_inf[$pono]['style_ref_no']; ?></td>
							<td align="right" width="60"><? echo $order_inf[$pono]['unit_price']; ?></td>
							<td width="100"><? echo $shipment_mode[$order_inf[$pono]['ship_mode']]; ?></td>
							<td align="right" width="100"><? echo number_format($rdata['all_qnty']); ?></td>
							<?
							foreach($cutoff as $cadys=>$cdate)
							{
								?>
								<td  align="right" width="80"><? echo $rdata['qnty'.$cadys]; ?></td>
								<?
							}
							?>
							<td></td>
						</tr>
						<?
					}
					?>
					<tr>
						<td colspan="7" bgcolor="#EFEFEF">
							<? echo "Buyer Summary: ".$buyer_arr[$buyer];?> 
						</td>
						<?
						foreach($cutoff as $cadys=>$cdate)
						{
							?>
							<td  align="right" bgcolor="#EFEFEF" width="80"><? echo $summery_buyer[$comp][$loca][$buyer]['qnty'.$cadys]; ?></td>
							<?
						}
						?>	
					</tr>
					<?
					
					$i=1;
				}
				?>
				<tr>
					<td colspan="7" bgcolor="#EFEFEF">
						<? echo "Location Summary: ";?> 
					</td>
					<?
					foreach($cutoff as $cadys=>$cdate)
					{
						?>
						<td  align="right" bgcolor="#EFEFEF" width="80"><? echo $summery_loca[$comp][$loca]['qnty'.$cadys]; ?></td>
						<?
					}
					?>	
				</tr>
				<?

				$i=1;

			}
			?>
			<tr>
				<td colspan="7" bgcolor="#EFEFEF">
					<? echo "Company Summary: ";?> 
				</td>
				<?
				foreach($cutoff as $cadys=>$cdate)
				{
					?>
					<td  align="right" bgcolor="#EFEFEF" width="80"><? echo $summery_comp[$comp]['qnty'.$cadys]; ?></td>
					<?
				}
				?>	
			</tr>
			<?
		}
		?>
	</tbody>
</table>
</div>
</div>

	<?
	
	foreach (glob("$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename="".$user_name."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
	
}


?>

