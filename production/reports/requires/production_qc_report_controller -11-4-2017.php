<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------------------------------

if ($action=="load_drop_down_location")
{
	//echo "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name";die;
	echo create_drop_down( "cbo_location", 110, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/production_qc_report_controller', $data+'_'+this.value, 'load_drop_down_floor', 'floor_td' );",0 );
	exit();     	
}

if ($action=="load_drop_down_floor")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_floor", 110, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and company_id=$data[0] and location_id='$data[1]' order by floor_name","id,floor_name", 1, "-- Select --", $selected, "",0 ); 
	exit();    	 
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 110, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" );     	 
	exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$buyerArr = return_library_array("select id,short_name from lib_buyer where status_active=1 and is_deleted=0","id","short_name"); 
	$lineArr = return_library_array("select id,line_name from lib_sewing_line order by id","id","line_name"); 
	$prod_reso_line_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	
	if($db_type==2)
	{
		$prod_reso_arr=return_library_array( "select a.id, b.line_name from prod_resource_mst a, lib_sewing_line b where  REGEXP_SUBSTR( a.line_number, '[^,]+', 1)=b.id order by b.floor_name, b.sewing_line_serial","id","line_name");
	}
	else if($db_type==0)
	{
		$prod_reso_arr=return_library_array( "select a.id, b.line_name from prod_resource_mst a, lib_sewing_line b where  SUBSTRING_INDEX( a.line_number, ' , ', 1)=b.id order by b.floor_name, b.sewing_line_serial","id","line_name");
	}
	
	$location = str_replace("'","",$cbo_location);
	
	if(str_replace("'","",$cbo_company_name)==0)$company_name=""; else $company_name=" and a.company_id=$cbo_company_name";
	if(str_replace("'","",$cbo_buyer_name)==0)$buyer_name="";else $buyer_name=" and b.buyer_name=$cbo_buyer_name";
	if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="") $txt_date="";
	else $txt_date=" and a.production_date between $txt_date_from and $txt_date_to";
	$fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
	$toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );
	if ($location==0) $location_cond=""; else $location_cond=" and a.location=".$location." "; 
	if(str_replace("'","",$cbo_floor)==0) $floor_name="";else $floor_name=" and a.floor_id=$cbo_floor";
	$prod_type=str_replace("'","",$cbo_production_type);
	if($type==1)
	{	$colspan=36;
		if ($prod_type==5)
		{
			$defect_type=3;
			$sew_fin_alter_defect_type=$sew_fin_alter_defect_type;
			$tbl_width=(count($sew_fin_alter_defect_type)*80)+1580;
		}
		elseif($prod_type==1)
		{
			$defect_type=3;
			$sew_fin_alter_defect_type=$cutting_qc_reject_type;
			$tbl_width=(count($sew_fin_alter_defect_type)*80)+1480;
		}
		elseif($prod_type==8)
		{
			$defect_type=1;
			$sew_fin_alter_defect_type=$sew_fin_alter_defect_type;
			$tbl_width=(count($sew_fin_alter_defect_type)*80)+1480;
		}
		elseif($prod_type==11)
		{
			$defect_type=1;
			$sew_fin_alter_defect_type=$sew_fin_alter_defect_type;
			$tbl_width=(count($sew_fin_alter_defect_type)*80)+1580;
		}
		
		
		ob_start();
		?>
		<div>
		<table width="<? echo $tbl_width; ?>" cellspacing="0">
			<tr class="form_caption" style="border:none;">
				<td colspan="<? echo $colspan; ?>" align="center" style="border:none;font-size:14px; font-weight:bold" ><? echo $report_title; ?> (<? echo $production_type[$prod_type]; ?>)</td>
			</tr>
			<tr style="border:none;">
				<td colspan="<? echo $colspan; ?>" align="center" style="border:none; font-size:16px; font-weight:bold">
				Company Name:<? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>                                
				</td>
			</tr>
			<tr style="border:none;">
				<td colspan="<? echo $colspan; ?>" align="center" style="border:none;font-size:12px; font-weight:bold">
				<? echo "From $fromDate To $toDate" ;?>
				</td>
			</tr>
		</table>
		<br /> 
		<table width="<? echo $tbl_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
			<thead>
				<tr style="font-size:13px">
					<th width="30" rowspan="2">Sl.</th>    
					<th width="100" rowspan="2">Buyer Name</th>
					<th width="100" rowspan="2">Order No</th>
					<th width="100" rowspan="2">Order Qty</th>
					<? if ($prod_type==5 || $prod_type==11) {?><th width="100" rowspan="2">Line No</th><? }?>
					<th colspan="19">Defect Counting <? if($defect_type==3){echo "Reject";}else{ echo "Alter";}?></th>
					<th colspan="2">Defect Counting Spot</th>
					
					<th width="100" rowspan="2">Total Gmts Alter</th>
					<th width="90" rowspan="2">Alter % To Total QC</th>
					<th width="100" rowspan="2">Total Gmts Spot</th>
					<th width="90" rowspan="2">Spot % To Total QC</th>
					<th width="100" rowspan="2">Total Gmts Reject</th>
					
					<th width="90" rowspan="2">Reject % To Total QC</th>
					<th width="100" rowspan="2">Total Gmts Defect</th>
					<th width="90" rowspan="2">Defect % To Total QC</th>
					<th width="100" rowspan="2">Total Qc Passed Pcs.</th>
					<th rowspan="2">Total QC Pcs.</th>
				 </tr>
				 <tr style="font-size:13px">
					<? foreach($sew_fin_alter_defect_type as $id=>$val) { ?>
					<th width="80"><? echo $val; ?></th>
					<? } foreach($sew_fin_spot_defect_type as $key=>$value) {?>
					<th width="80"><? echo $value; ?></th>
					<? }?>
				 </tr>
			</thead>
		</table>
		<div style="width:<? echo $tbl_width+17; ?>px; max-height:350px; overflow-y:scroll" id="scroll_body" > 
		<table width="<? echo $tbl_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id=""><!--table_body-->
		<?
		
		if(str_replace("'","",$cbo_company_name)==0) $company_qc_cond=""; else $company_qc_cond=" and d.company_id=$cbo_company_name";
		
		if(str_replace("'","",$cbo_working_company_id)==0) $company_working_cond=""; else $company_working_cond=" and d.serving_company=$cbo_working_company_id";
		
		
		if(str_replace("'","",$cbo_buyer_name)==0)$buyer_cond=""; else $buyer_cond=" and b.buyer_name=$cbo_buyer_name";
		if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="") $prod_date_qc_cond="";
			else $prod_date_qc_cond=" and d.production_date between $txt_date_from and $txt_date_to";
		$fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
		$toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );
		if ($location==0) $location_qc_cond=""; else $location_qc_cond=" and d.location=".$location." "; 
		if(str_replace("'","",$cbo_floor)==0) $floor_name_cond="";else $floor_name_cond=" and d.floor_id=$cbo_floor";
		
		$prod_qty_arr=array(); $defect_line_arr=array(); $order_colspan_arr=array();
		$sql = "select b.buyer_name, c.po_number, c.po_quantity as po_quantity, d.id, d.po_break_down_id, d.sewing_line, d.prod_reso_allo, d.production_quantity as qs_pass_qty, d.alter_qnty as alter_qnty, d.reject_qnty as reject_qnty, d.spot_qnty as spot_qnty ";
		foreach($sew_fin_alter_defect_type as $id=>$val) {
			$sql.= ", sum(case when a.defect_type_id=$defect_type and a.defect_point_id=$id then a.defect_qty else 0 end) as altdefect_qty_$id";
		}
		foreach($sew_fin_spot_defect_type as $key=>$val) {
			if($prod_type==5){
			$sql.= ", sum(case when a.defect_type_id in(2,4) and a.defect_point_id=$key then a.defect_qty else 0 end) as sptdefect_qty_$key";
			}
			else
			{
			$sql.= ", sum(case when a.defect_type_id=2 and a.defect_point_id=$key then a.defect_qty else 0 end) as sptdefect_qty_$key";
			}
		}
		$sql.= " from pro_gmts_prod_dft a, wo_po_details_master b, wo_po_break_down c, pro_garments_production_mst d where d.id=a.mst_id and a.po_break_down_id=c.id and b.job_no=c.job_no_mst and a.production_type='$prod_type' and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_qc_cond $company_working_cond $buyer_cond  $location_qc_cond $floor_name_cond $prod_date_qc_cond group by b.buyer_name, c.po_number, c.po_quantity, d.id, d.po_break_down_id, d.sewing_line, d.prod_reso_allo, d.production_quantity, d.alter_qnty, d.reject_qnty, d.spot_qnty";
		
		
		 //echo $sql;
		
		$result = sql_select($sql);
		foreach($result as $row)
		{
			$prod_qty_arr[$row[csf('po_break_down_id')]][$row[csf('sewing_line')]]['po_data']=$row[csf('buyer_name')].'**'.$row[csf('po_number')].'**'.$row[csf('po_quantity')];
			$prod_qty_arr[$row[csf('po_break_down_id')]][$row[csf('sewing_line')]]['prod_reso_allo']=$row[csf('prod_reso_allo')];
			$prod_qty_arr[$row[csf('po_break_down_id')]][$row[csf('sewing_line')]]['qc']+=$row[csf('qs_pass_qty')];
			$prod_qty_arr[$row[csf('po_break_down_id')]][$row[csf('sewing_line')]]['alt']+=$row[csf('alter_qnty')];
			$prod_qty_arr[$row[csf('po_break_down_id')]][$row[csf('sewing_line')]]['spt']+=$row[csf('spot_qnty')];
			$prod_qty_arr[$row[csf('po_break_down_id')]][$row[csf('sewing_line')]]['rjt']+=$row[csf('reject_qnty')];
			foreach($sew_fin_alter_defect_type as $id=>$val) {
				$defect_line_arr[$row[csf('po_break_down_id')]][$row[csf('sewing_line')]][$id][1]+=$row[csf('altdefect_qty_'.$id)];
			}
			foreach($sew_fin_spot_defect_type as $key=>$val) {
				$defect_line_arr[$row[csf('po_break_down_id')]][$row[csf('sewing_line')]][$key][2]+=$row[csf('sptdefect_qty_'.$key)];
			}
			$order_colspan_arr[$row[csf('buyer_name')]][$row[csf('po_break_down_id')]]+=1;
		}
		unset($result);
		
		//var_dump($sew_fin_alter_defect_type);
		
		
		
		$i=1;
		$alt_dft_qty=array(); $spt_dft_qty=array();
		//echo $sql;
		foreach($prod_qty_arr as $po_id=> $po_val)
		{
			$k=1;
			foreach($po_val as $line_id=> $line_val)
			{
				if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
				$qc_pass_qty=0; $alter_qty=0; $spot_qty=0; $reject_qty=0; $prod_reso_allo=0;
				$qc_pass_qty=$line_val['qc'];
				$alter_qty=$line_val['alt'];
				$spot_qty=$line_val['spt'];
				$reject_qty=$line_val['rjt'];
				$prod_reso_allo=$line_val['prod_reso_allo'];
				$ex_po_data=explode('**',$prod_qty_arr[$po_id][$line_id]['po_data']);
				$buyer_id=$ex_po_data[0];
				$po_number=$ex_po_data[1];
				$po_qty=$ex_po_data[2];
				
				$ord_colspan=0;
				$ord_colspan=$order_colspan_arr[$buyer_id][$po_id];
				
				if($prod_reso_allo==1)
				{
					$line_resource_mst_arr=explode(",",$prod_reso_line_arr[$line_id]);
					$line_name="";
					foreach($line_resource_mst_arr as $resource_id)
					{
						$line_name.=$lineArr[$resource_id].", ";
					}
					$line_name=chop($line_name," , ");
				}
				else
				{
					$line_name=$lineArr[$line_id];
				}
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>" style="font-size:13px">
                	<? if($k==1) { ?>
					<td width="30"><? echo $i; $i++; ?></td>
					<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $buyerArr[$buyer_id]; ?></div></td>
					<td width="100"><div style="word-wrap:break-word; width:100px">&nbsp;<? echo $po_number; ?></div></td>
					<td width="100" align="right"><? if($po_qty>0) echo number_format($po_qty,2); $tot_po_qty+=$po_qty; ?></td>
                    <? } else { ?>
					<td width="30">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100" align="right">&nbsp;</td>
					<? } $k++; if ($prod_type==5 || $prod_type==11) {?><td width="100" align="center"><div style="word-wrap:break-word; width:100px">&nbsp;<? echo $line_name; ?></div></td><? }?>
					<? 
					$row_tot_alt=0; $row_tot_spt=0;
					foreach($sew_fin_alter_defect_type as $id=>$val) {
						$alt_dft_value=0;
						$alt_dft_value=$defect_line_arr[$po_id][$line_id][$id][1];
						$alt_dft_qty[$id]+=$alt_dft_value;
					?>
					<td width="80" align="right">&nbsp;<? if($alt_dft_value>0) echo number_format($alt_dft_value,2); 
					$row_tot_alt+=$alt_dft_value;
					?></td>
					<? } foreach($sew_fin_spot_defect_type as $key=>$value) {
						$spt_dft_value=0;
						$spt_dft_value=$defect_line_arr[$po_id][$line_id][$key][2];
						$spt_dft_qty[$key]+=$spt_dft_value;
						?>
					<td width="80" align="right">&nbsp;<? if($spt_dft_value>0) echo number_format($spt_dft_value,2); $row_tot_spt+=$spt_dft_value; ?></td>
					<? }?>
					<td width="100" align="right">&nbsp;<?
						$pcs_inspaction=0;
						$row_tot_defect=0; 
						$row_tot_defect=$alter_qty+$spot_qty+$reject_qty;
						$pcs_inspaction=$qc_pass_qty+$row_tot_defect;
						
						if($alter_qty>0) echo number_format($alter_qty,2); ?></td>
					<td width="90" align="right">&nbsp;<? $row_tot_alt_per=0; $row_tot_alt_per=($alter_qty/$pcs_inspaction)*100; if($row_tot_alt_per>0) echo number_format($row_tot_alt_per,2).'%'; ?></td>
					<td width="100" align="right">&nbsp;<? if($spot_qty>0) echo number_format($spot_qty,2); ?></td>
					<td width="90" align="right">&nbsp;<? $row_tot_spt_per=0; $row_tot_spt_per=($spot_qty/$pcs_inspaction)*100; if($row_tot_spt_per>0) echo number_format($row_tot_spt_per,2).'%'; ?></td>
					<td width="100" align="right">&nbsp;<? if($reject_qty>0) echo number_format($reject_qty,2); ?></td>
					<td width="90" align="right">&nbsp;<? $reject_qnty_per=0; $reject_qnty_per=($reject_qty/$pcs_inspaction)*100; if($reject_qnty_per>0) echo number_format($reject_qnty_per,2).'%'; ?></td>
					<td width="100" align="right">&nbsp;<? echo number_format($row_tot_defect,2); ?></td>
					<td width="90" align="right">&nbsp;<? $row_tot_defect_per=0; $row_tot_defect_per=($row_tot_defect/$pcs_inspaction)*100; if($row_tot_defect_per>0) echo number_format($row_tot_defect_per,2).'%'; ?></td>
					<td width="100" align="right">&nbsp;<?  if($qc_pass_qty>0) echo number_format($qc_pass_qty,2); ?></td>
					<td align="right">&nbsp;<? if($pcs_inspaction>0) echo number_format($pcs_inspaction,2); ?></td>
				</tr>
				<?
				$tot_reject_qty+=$reject_qty;
				$tot_alter_qty+=$alter_qty;
				$tot_spot_qty+=$spot_qty;
				$tot_defect_qty+=$row_tot_defect;
				$tot_pcs_inspaction+=$pcs_inspaction;
				$tot_qc_pass_qty+=$qc_pass_qty;
			}
		}
		?>
		</table>
        </div>
		<table width="<? echo $tbl_width; ?>" border="1" cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all">
			<tr>
				<td width="30">&nbsp;</td>
				<td width="100">&nbsp;</td>
				<td width="100">Total</td>
				<td width="100" align="right"><? echo number_format($tot_po_qty,2); ?></td>
				<? if ($prod_type==5 || $prod_type==11) {?><td width="100">&nbsp;</td><? }
				foreach($sew_fin_alter_defect_type as $id=>$val) { 
				?>
				<td width="80" align="right"><? echo number_format($alt_dft_qty[$id],2); 
				?></td>
				<? } foreach($sew_fin_spot_defect_type as $key=>$value) {
					?>
				<td width="80" align="right"><? echo number_format($spt_dft_qty[$key],2); ?></td>
				<? }?>
				<td width="100" align="right"><? echo number_format($tot_alter_qty,2); ?></td>
				
				<td width="90" align="right">&nbsp;</td>
				<td width="100" align="right"><? echo number_format($tot_spot_qty,2); ?></td>
				<td width="90" align="right">&nbsp;</td>
				<td width="100" align="right"><? echo number_format($tot_reject_qty,2); $reject_qty_arr=array(); $reject_qty_arr[]=$tot_reject_qty; ?></td>
				<td width="90" align="right">&nbsp;</td>
				<td width="100" align="right"><? echo number_format($tot_defect_qty,2); ?></td>
				<td width="90" align="right">&nbsp;</td>
				<td width="100" align="right"><? echo number_format($tot_qc_pass_qty,2); ?></td>
				<td align="right"><? echo number_format($tot_pcs_inspaction,2); ?></td>
			</tr>
			<tr>
				<td width="30">&nbsp;</td>
				<td width="100">&nbsp;</td>
				<td width="200" colspan="2" style="font-size:12px">Defect Counting % To Total QC</td>
				<? if ($prod_type==5 || $prod_type==11) {?><td width="100">&nbsp;</td><? }?>
				<? 
				$alt_dft_qty_per=array(); $spt_dft_qty_per=array();
				foreach($sew_fin_alter_defect_type as $id=>$val) { 
				$alt_dft_qty_per[$id]=($alt_dft_qty[$id]/$tot_pcs_inspaction)*100;
				?>
				<td width="80" align="right"><? echo number_format($alt_dft_qty_per[$id],2)."%"; 
				?></td>
				<? } foreach($sew_fin_spot_defect_type as $key=>$value) {
					$spt_dft_qty_per[$key]=($spt_dft_qty[$key]/$tot_pcs_inspaction)*100;
					?>
				<td width="80" align="right"><? echo number_format($spt_dft_qty_per[$key],2).'%'; ?></td>
				<? }?>
				<td width="100" align="right">&nbsp;</td>
				
				<td width="90" align="right">&nbsp;</td>
				<td width="100" align="right">&nbsp;</td>
				<td width="90" align="right">&nbsp;</td>
				<td width="100" align="right">&nbsp;</td>
				<td width="90" align="right">&nbsp;</td>
				<td width="100" align="right">&nbsp;</td>
				<td width="90" align="right">&nbsp;</td>
				<td width="100" align="right">&nbsp;</td>
				<td align="right">&nbsp;</td>
			</tr>
		</table>
		</div>
		&nbsp;
		<div align="left" style="width:100%;">
			<div align="center" style="width:1450px; height:500px;  margin-left:20px; border:solid 1px">
				<table style="margin-left:60px; font-size:12px" align="left">
				<tr>
					<td align="left" bgcolor="red" width="10"></td>
					<td><? echo $production_type[$prod_type]; ?> QC CHART</td>
				</tr>
				</table>
				<canvas id="canvas" height="400" width="1400"></canvas>
			</div>
		</div>
		<?
		$reject_qty_type=array(0=>"Reject");
		$bar_arr=array();$val_arr=array();
		foreach($sew_fin_alter_defect_type as $kk=>$vv)
		{
			$bar_arr[]=$vv;
			$val_arr[]=$alt_dft_qty[$kk];
		}
		
		foreach($sew_fin_spot_defect_type as $key=>$val)
		{
			$bar_arr[]=$val;
			$val_arr[]=$spt_dft_qty[$key];
		}
		
		foreach($reject_qty_type as $keyr=>$valr)
		{
			$bar_arr[]=$valr;
			$val_arr[]=$reject_qty_arr[$keyr];
		}
		
		$bar_arr= json_encode($bar_arr);
		$val_arr= json_encode($val_arr);
		?>
	   <script>
			var barChartData = {
			labels : <? echo $bar_arr; ?>,
			datasets : [
					{
						fillColor : "red",
						//strokeColor : "rgba(220,220,220,0.8)",
						//highlightFill: "rgb(255,99,71)",
						//highlightStroke: "rgba(220,220,220,1)",
						data : <? echo $val_arr; ?>
					}
				]
			}
			
			var ctx = document.getElementById("canvas").getContext("2d");
			window.myBar = new Chart(ctx).Bar(barChartData, {
			responsive : true
			});
	   </script>
		<?
	}
	else if($type==2)
	{
		
		
		if ($prod_type==5)
		{
			$defect_type=3;
			$sew_fin_alter_defect_type=$sew_fin_alter_defect_type;
			//$tbl_width=(count($sew_fin_alter_defect_type)*80)+1580;
		}
		elseif($prod_type==1)
		{
			$defect_type=3;
			$sew_fin_alter_defect_type=$cutting_qc_reject_type;
			//$tbl_width=(count($sew_fin_alter_defect_type)*80)+1480;
		}
		elseif($prod_type==8)
		{
			$defect_type=1;
			$sew_fin_alter_defect_type=$sew_fin_alter_defect_type;
			//$tbl_width=(count($sew_fin_alter_defect_type)*80)+1480;
		}
		elseif($prod_type==11)
		{
			$defect_type=1;
			$sew_fin_alter_defect_type=$sew_fin_alter_defect_type;
			//$tbl_width=(count($sew_fin_alter_defect_type)*80)+1580;
		}
		
		
		
		$colspanm=33;
		$tbl_widthm=2800;
		ob_start();
		?>
		<div>
		<table width="<? echo $tbl_widthm; ?>" cellspacing="0">
			<tr class="form_caption" style="border:none;">
				<td colspan="<? echo $colspanm; ?>" align="center" style="border:none;font-size:14px; font-weight:bold" ><? echo $report_title; ?> Monthly, (<? echo $production_type[$prod_type]; ?>)</td>
			</tr>
			<tr style="border:none;">
				<td colspan="<? echo $colspanm; ?>" align="center" style="border:none; font-size:16px; font-weight:bold">Company Name:<? echo $company_library[str_replace("'","",$cbo_company_name)]; ?></td>
			</tr>
			<tr style="border:none;">
				<td colspan="<? echo $colspanm; ?>" align="center" style="border:none;font-size:12px; font-weight:bold"><? echo "From $fromDate To $toDate" ;?></td>
			</tr>
		</table>
		<table width="<? echo $tbl_widthm; ?>" cellspacing="0" border="1" class="rpt_table" rules="all">
			<thead>
				<tr>
					<th width="30" rowspan="2">SL.</th>    
					<th width="100" rowspan="2">Date</th>
					<th colspan="19">Defect Counting Alter</th>
					<th colspan="2">Defect Counting Spot</th>
					
					<th width="100" rowspan="2">Total Gmts Alter</th>
					<th width="90" rowspan="2">Alter % To Total QC</th>
					<th width="100" rowspan="2">Total Gmts Spot</th>
					<th width="90" rowspan="2">Spot % To Total QC</th>
					<th width="100" rowspan="2">Total Gmts Reject</th>
					
					<th width="90" rowspan="2">Reject % To Total QC</th>
					<th width="100" rowspan="2">Total Gmts Defect</th>
					<th width="90" rowspan="2">Defect % To Total QC</th>
					<th width="100" rowspan="2">Total Qc Passed Pcs.</th>
					<th rowspan="2">Total QC Pcs.</th>
				 </tr>
				 <tr>
					<? foreach($sew_fin_alter_defect_type as $id=>$val) { ?>
					<th width="80"><? echo $val; ?></th>
					<? } foreach($sew_fin_spot_defect_type as $key=>$value) {?>
					<th width="80"><? echo $value; ?></th>
					<? }?>
				 </tr>
			</thead>
		</table>
		<div style="width:<? echo $tbl_widthm+17; ?>px; max-height:350px; overflow-y:scroll" id="scroll_body" > 
		<table width="<? echo $tbl_widthm; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body"><!--table_body-->
		<?
		
		if(str_replace("'","",$cbo_company_name)==0) $company_qc_cond=""; else $company_qc_cond=" and d.company_id=$cbo_company_name";
		if(str_replace("'","",$cbo_buyer_name)==0)$buyer_cond=""; else $buyer_cond=" and b.buyer_name=$cbo_buyer_name";
		if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="") $prod_date_qc_cond="";
			else $prod_date_qc_cond=" and d.production_date between $txt_date_from and $txt_date_to";
		$fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
		$toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );
		if ($location==0) $location_qc_cond=""; else $location_qc_cond=" and d.location=".$location." "; 
		if(str_replace("'","",$cbo_floor)==0) $floor_name_cond="";else $floor_name_cond=" and d.floor_id=$cbo_floor";
		
		$defect_line_arr=array(); $prod_qty_arr=array();
		$sql = "select d.id, d.production_date, d.production_quantity as qs_pass_qty, d.alter_qnty as alter_qnty, d.reject_qnty as reject_qnty, d.spot_qnty as spot_qnty ";
		foreach($sew_fin_alter_defect_type as $id=>$val) {
			$sql.= ", sum(case when a.defect_type_id=$defect_type and a.defect_point_id=$id then a.defect_qty else 0 end) as altdefect_qty_$id";
		}
		foreach($sew_fin_spot_defect_type as $key=>$val) {
			if($prod_type==5){
			$sql.= ", sum(case when a.defect_type_id in(2,4) and a.defect_point_id=$key then a.defect_qty else 0 end) as sptdefect_qty_$key";
			}
			else
			{
			$sql.= ", sum(case when a.defect_type_id=2 and a.defect_point_id=$key then a.defect_qty else 0 end) as sptdefect_qty_$key";
			}
		}
		$sql.= " from pro_gmts_prod_dft a, wo_po_details_master b, wo_po_break_down c, pro_garments_production_mst d where d.id=a.mst_id and a.po_break_down_id=c.id and b.job_no=c.job_no_mst and a.production_type='$prod_type' and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_qc_cond $buyer_cond  $location_qc_cond $floor_name_cond $prod_date_qc_cond group by d.id, d.production_date, d.production_quantity, d.alter_qnty, d.reject_qnty, d.spot_qnty";
		$result = sql_select($sql);
		foreach($result as $row)
		{
			$prod_qty_arr[change_date_format($row[csf('production_date')])]['qc']+=$row[csf('qs_pass_qty')];
			$prod_qty_arr[change_date_format($row[csf('production_date')])]['alt']+=$row[csf('alter_qnty')];
			$prod_qty_arr[change_date_format($row[csf('production_date')])]['spt']+=$row[csf('spot_qnty')];
			$prod_qty_arr[change_date_format($row[csf('production_date')])]['rjt']+=$row[csf('reject_qnty')];
			foreach($sew_fin_alter_defect_type as $id=>$val) {
				$defect_line_arr[change_date_format($row[csf('production_date')])][$id][1]+=$row[csf('altdefect_qty_'.$id)];
			}
			foreach($sew_fin_spot_defect_type as $key=>$val) {
				$defect_line_arr[change_date_format($row[csf('production_date')])][$key][2]+=$row[csf('sptdefect_qty_'.$key)];
			}
		}
		unset($result);
		
		$alt_dft_qty=array(); $spt_dft_qty=array(); $i=1; 
		for($j=0;$j<$datediff;$j++)
		{
			if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
			
			$date_all=add_date(str_replace("'","",$txt_date_from),$j);
			$newdate =change_date_format($date_all);
			
			$qc_pass_qty=0; $alter_qty=0; $spot_qty=0; $reject_qty=0;
			$qc_pass_qty=$prod_qty_arr[$newdate]['qc'];
			$alter_qty=$prod_qty_arr[$newdate]['alt'];
			$spot_qty=$prod_qty_arr[$newdate]['spt'];
			$reject_qty=$prod_qty_arr[$newdate]['rjt'];
			
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
				<td width="30"><? echo $i; ?></td>
				<td width="100"><div style="word-wrap:break-word; width:100px">&nbsp;<? echo $newdate; ?></div></td>
				<? 
				$row_tot_alt=0; $row_tot_spt=0; 
				foreach($sew_fin_alter_defect_type as $id=>$val) 
				{ 
					$alter_defect_val=0;
					$alter_defect_val=$defect_line_arr[$newdate][$id][1];
					$alt_dft_qty[$id]+=$alter_defect_val;
				?>
				<td width="80" align="right"><? if($alter_defect_val>0) echo number_format($alter_defect_val,2); $row_tot_alt+=$alter_defect_val; ?></td>
				<? } foreach($sew_fin_spot_defect_type as $key=>$value)
				{
					$spot_defect_val=0;
					$spot_defect_val=$defect_line_arr[$newdate][$key][2];
					$spt_dft_qty[$key]+=$spot_defect_val;
					?>
				<td width="80" align="right"><? if($spot_defect_val>0) echo number_format($spot_defect_val,2); $row_tot_spt+=$spot_defect_val; ?></td>
				<? }?>
				<td width="100" align="right"><?
					$pcs_inspaction=0;
					$row_tot_defect=0; 
					$row_tot_defect=$alter_qty+$spot_qty+$reject_qty;
					$pcs_inspaction=$qc_pass_qty+$row_tot_defect;
					
					if($alter_qty>0) echo number_format($alter_qty,2); ?></td>
				<td width="90" align="right"><? $row_tot_alt_per=0; $row_tot_alt_per=($alter_qty/$pcs_inspaction)*100; if($row_tot_alt_per>0) echo number_format($row_tot_alt_per,2).'%'; ?></td>
				<td width="100" align="right"><? if($spot_qty>0) echo number_format($spot_qty,2); ?></td>
				<td width="90" align="right"><?  $row_tot_spt_per=0; $row_tot_spt_per=($spot_qty/$pcs_inspaction)*100; if($row_tot_spt_per>0) echo number_format($row_tot_spt_per,2).'%'; ?></td>
				<td width="100" align="right"><? if($reject_qty>0) echo number_format($reject_qty,2); ?></td>
				<td width="90" align="right"><? $reject_qnty_per=0; $reject_qnty_per=($reject_qty/$pcs_inspaction)*100; if($reject_qnty_per>0) echo number_format($reject_qnty_per,2).'%'; ?></td>
				<td width="100" align="right"><? echo number_format($row_tot_defect,2); ?></td>
				<td width="90" align="right"><? $row_tot_defect_per=0; $row_tot_defect_per=($row_tot_defect/$pcs_inspaction)*100; if($row_tot_defect_per>0) echo number_format($row_tot_defect_per,2).'%'; ?></td>
				<td width="100" align="right"><? if($qc_pass_qty>0) echo number_format($qc_pass_qty,2); ?></td>
				<td align="right"><?  if($pcs_inspaction>0) echo number_format($pcs_inspaction,2); ?></td>
			</tr>
			<?
			$tot_reject_qty+=$reject_qty;
			$tot_alter_qty+=$alter_qty;
			$tot_spot_qty+=$spot_qty;
			$tot_defect_qty+=$row_tot_defect;
			$tot_pcs_inspaction+=$pcs_inspaction;
			$tot_qc_pass_qty+=$qc_pass_qty;
			$i++;
		}
		?>
		</table>
        </div>
		<table width="<? echo $tbl_widthm; ?>" border="1" cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all">
			<tr>
				<td width="30">&nbsp;</td>
				<td width="100">Total</td>
				<? 
				foreach($sew_fin_alter_defect_type as $id=>$val) { 
				?>
				<td width="80" align="right"><? echo number_format($alt_dft_qty[$id],2); ?></td>
				<? } foreach($sew_fin_spot_defect_type as $key=>$value) {
					?>
				<td width="80" align="right"><? echo number_format($spt_dft_qty[$key],2); ?></td>
				<? }?>
				<td width="100" align="right"><? echo number_format($tot_alter_qty,2); ?></td>
				
				<td width="90" align="right">&nbsp;</td>
				<td width="100" align="right"><? echo number_format($tot_spot_qty,2); ?></td>
				<td width="90" align="right">&nbsp;</td>
				<td width="100" align="right"><? echo number_format($tot_reject_qty,2); $reject_qty_arr=array(); $reject_qty_arr[]=$tot_reject_qty; ?></td>
				<td width="90" align="right">&nbsp;</td>
				<td width="100" align="right"><? echo number_format($tot_defect_qty,2); ?></td>
				<td width="90" align="right">&nbsp;</td>
				<td width="100" align="right"><? echo number_format($tot_qc_pass_qty,2); ?></td>
				<td align="right"><? echo number_format($tot_pcs_inspaction,2); ?></td>
			</tr>
			<tr>
				<td width="30">&nbsp;</td>
				<td width="100"><div style="word-wrap:break-word; width:100px">Defect Counting % To Total QC</div></td>
				<? 
				$alt_dft_qty_per=array(); $spt_dft_qty_per=array();
				foreach($sew_fin_alter_defect_type as $id=>$val) { 
				$alt_dft_qty_per[$id]=($alt_dft_qty[$id]/$tot_pcs_inspaction)*100;
				?>
				<td width="80" align="right"><? echo number_format($alt_dft_qty_per[$id],2)."%"; ?></td>
				<? } foreach($sew_fin_spot_defect_type as $key=>$value) {
					$spt_dft_qty_per[$key]=($spt_dft_qty[$key]/$tot_pcs_inspaction)*100;
					?>
				<td width="80" align="right"><? echo number_format($spt_dft_qty_per[$key],2).'%'; ?></td>
				<? }?>
				<td width="100" align="right">&nbsp;</td>
				
				<td width="90" align="right">&nbsp;</td>
				<td width="100" align="right">&nbsp;</td>
				<td width="90" align="right">&nbsp;</td>
				<td width="100" align="right">&nbsp;</td>
				<td width="90" align="right">&nbsp;</td>
				<td width="100" align="right">&nbsp;</td>
				<td width="90" align="right">&nbsp;</td>
				<td width="100" align="right">&nbsp;</td>
				<td align="right">&nbsp;</td>
			</tr>
		</table>
		</div>
		&nbsp;
		<div align="left" style="width:100%;">
			<div align="center" style="width:1450px; height:500px;  margin-left:20px; border:solid 1px">
				<table style="margin-left:60px; font-size:12px" align="left">
				<tr>
					<td align="left" bgcolor="red" width="10"></td>
					<td><? echo $production_type[$prod_type]; ?> QC CHART</td>
				</tr>
				</table>
				<canvas id="canvas" height="400" width="1400"></canvas>
			</div>
		</div>
		<?
		$reject_qty_type=array(0=>"Reject");
		$bar_arr=array();$val_arr=array();
		foreach($sew_fin_alter_defect_type as $kk=>$vv)
		{
			$bar_arr[]=$vv;
			$val_arr[]=$alt_dft_qty[$kk];
		}
		
		foreach($sew_fin_spot_defect_type as $key=>$val)
		{
			$bar_arr[]=$val;
			$val_arr[]=$spt_dft_qty[$key];
		}
		
		foreach($reject_qty_type as $keyr=>$valr)
		{
			$bar_arr[]=$valr;
			$val_arr[]=$reject_qty_arr[$keyr];
		}
		
		$bar_arr= json_encode($bar_arr);
		$val_arr= json_encode($val_arr);
		?>
	   <script>
			var barChartData = {
			labels : <? echo $bar_arr; ?>,
			datasets : [
					{
						fillColor : "red",
						//strokeColor : "rgba(220,220,220,0.8)",
						//highlightFill: "rgb(255,99,71)",
						//highlightStroke: "rgba(220,220,220,1)",
						data : <? echo $val_arr; ?>
					}
				]
			}
			
			var ctx = document.getElementById("canvas").getContext("2d");
			window.myBar = new Chart(ctx).Bar(barChartData, {
			responsive : true
			});
	   </script>
		<?
	}
	$html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename**$type"; 
    exit();
}