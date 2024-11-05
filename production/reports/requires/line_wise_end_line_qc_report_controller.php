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
	$data=explode("_",$data);
	// echo $data[1];//die;
	//echo "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name";die;
	echo create_drop_down( "cbo_location", 80, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data[1]' order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/line_wise_end_line_qc_report_controller', $data[1]+'_'+this.value+'_'+$('#cbo_production_type').val(), 'load_drop_down_floor', 'floor_td' );",0 );
	exit();     	
}

if ($action=="load_drop_down_floor")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_floor", 70, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and company_id=$data[0] and production_process='$data[2]' and location_id='$data[1]' order by floor_name","id,floor_name", 1, "-- Select --", $selected,  "load_drop_down( 'requires/line_wise_end_line_qc_report_controller', $data[0]+'_'+$data[1]+'_'+this.value, 'load_drop_down_line', 'line_td' );",0); 
	exit();    	 
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 80, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" );     	 
	exit();
}

if($action=="load_drop_down_line")
{
	extract($_REQUEST);
	$data = explode("_",$data);
	$company_id = $data[0];
	$location_id = $data[1];
	$floor_id = $data[2];
	$txt_sewing_date = $data[3];

	$cond="";
	$prod_reso_allocation=return_field_value("auto_update","variable_settings_production","company_name=$company_id and variable_list=23 and is_deleted=0 and status_active=1");
	if($prod_reso_allocation==1)
	{
		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
		$line_array=array();
		if($txt_sewing_date=="")
		{
			if( $location_id ) $cond.= " and location_id= $location_id";
			if( $floor_id ) $cond.= " and floor_id in($floor_id)";

			$line_data=sql_select("select id, line_number from prod_resource_mst where is_deleted=0 $cond");
		}
		else
		{
			if( $location_id) $cond.= " and a.location_id= $location_id";
			if( $floor_id) $cond.= " and a.floor_id in($floor_id)";

			if($db_type==0)
			{
				$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and b.pr_date='".change_date_format($txt_sewing_date,'yyyy-mm-dd')."' and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id  order by a.prod_resource_num");
			}
			else if($db_type==2 || $db_type==1)
			{
				$line_data=sql_select("select a.id, a.line_number,a.prod_resource_num from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and b.pr_date='".date("j-M-Y",strtotime($txt_sewing_date))."' and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id,a.prod_resource_num, a.line_number  order by a.prod_resource_num");
			}
		}
		$line_merge=9999;
		foreach($line_data as $row)
		{
			$line='';
			$line_number=explode(",",$row[csf('line_number')]);
			foreach($line_number as $val)
			{
				if(count($line_number)>1)
				{
					$line_merge++;
					$new_arr[$line_merge]=$row[csf('id')];
				}
				else
					$new_arr[$val]=$row[csf('id')];
				if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
			}
			$line_array[$row[csf('id')]]=$line;
		}

		ksort($new_arr);
		foreach($new_arr as $key=>$v)
		{
			$line_array_new[$v]=$line_array[$v];
		}
		//echo create_drop_down( "cbo_sewing_line", 110,$line_array_new,"", 1, "--- Select ---", $selected, "",0,0 );
		echo create_drop_down( "cbo_line_id", 70,$line_array_new,"", 1, "--- Select ---", $selected, "",0,0 );
	}
	else
	{
		if( $floor_id == 0 && $location_id != 0 ) $cond = " and location_name= $location_id";
		if( $floor_id!=0 ) $cond = " and floor_name in($floor_id)";

		echo create_drop_down( "cbo_line_id", 70, "select id,line_name from lib_sewing_line where  is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name","id,line_name", 0, "--- Select ---", $selected, "",0,0 );
	}
	exit();
}


if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"); 
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"); 
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"); 
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"); 
	$lineArr = return_library_array("select id,line_name from lib_sewing_line order by id","id","line_name"); 
	$prod_reso_line_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$sewing_floor_arr=return_library_array( "select id, floor_name from lib_prod_floor",'id','floor_name');	
	$operation_name_arr=return_library_array( "select id, OPERATION_NAME from lib_sewing_operation_entry",'id','OPERATION_NAME');	

	if($type==1)
	{
		$lineDataArr = sql_select("select id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1
		order by sewing_line_serial");
		foreach($lineDataArr as $lRow)
		{
			$lineArr[$lRow[csf('id')]]=$lRow[csf('line_name')];
			$lineSerialArr[$lRow[csf('id')]]=$lRow[csf('sewing_line_serial')];
			$lastSlNo=$lRow[csf('sewing_line_serial')];
		}
		
		$company_id = str_replace("'","",$cbo_company_name);
		$wo_company_id = str_replace("'","",$cbo_working_company_id);
		$prod_type = str_replace("'","",$cbo_production_type);
		$buyer_id = str_replace("'","",$cbo_buyer_name);
		$location_id = str_replace("'","",$cbo_location);
		$floor_id = str_replace("'","",$cbo_floor);
		$job_no = str_replace("'","",$txt_job_no);
		$int_ref = str_replace("'","",$txt_int_ref);
		$line_id = str_replace("'","",$cbo_line_id);
		$po_no = str_replace("'","",$txt_po_no);
		$fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
		$toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );

		$sql_cond = "";
		$sql_cond .= ($company_id !=0) ? " and b.company_id=$company_id" : "";
		$sql_cond .= ($wo_company_id !=0) ? " and b.serving_company=$wo_company_id" : "";
		$sql_cond .= ($buyer_id !=0) ? " and d.buyer_name=$buyer_id" : "";
		$sql_cond .= ($location_id !=0) ? " and b.location=$location_id" : "";
		$sql_cond .= ($floor_id !=0) ? " and b.floor_id=$floor_id" : "";
		$sql_cond .= ($line_id !=0) ? " and b.sewing_line=$line_id" : "";
		$sql_cond .= ($job_no !="") ? " and d.job_no like '%$job_no%' " : "";
		$sql_cond .= ($int_ref !="") ? " and c.grouping='$int_ref'" : "";
		$sql_cond .= ($po_no !="") ? " and c.po_number='$po_no'" : "";
		if(str_replace("'","",trim($txt_date_from)) !="" && str_replace("'","",trim($txt_date_to)) !="")
		{
			$sql_cond .= " and b.production_date between $txt_date_from and $txt_date_to";
		}

		/* ===================================== query ===================================== */
		// $sql = "SELECT d.buyer_name,d.style_ref_no,c.po_number,c.id as po_id,f.item_number_id as item_id,f.color_number_id as color_id,f.size_number_id as size_id, b.sewing_line,b.production_date,b.prod_reso_allo,a.defect_type_id,a.defect_point_id,a.defect_qty,e.operation_id,e.production_qnty,e.reject_qty,e.alter_qty,e.spot_qty,e.replace_qty from pro_garments_production_dtls e, pro_garments_production_mst b left join pro_gmts_prod_dft a on b.id=a.mst_id and a.status_active=1 and a.defect_type_id in(1,2),wo_po_break_down c, wo_po_details_master d, wo_po_color_size_breakdown  f where e.id=a.dtls_id  and b.po_break_down_id=c.id and c.job_id=d.id and c.id=f.po_break_down_id and e.color_size_break_down_id=f.id and b.production_type=5 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 $sql_cond order by b.production_date,f.SIZE_ORDER";

		$deffect_sql = "SELECT d.buyer_name,d.style_ref_no,c.po_number,c.id as po_id,f.item_number_id as item_id,f.color_number_id as color_id,f.size_number_id as size_id, b.sewing_line,b.production_date,b.prod_reso_allo,a.defect_qty
		from pro_garments_production_dtls e left join pro_gmts_prod_dft a on e.id=a.dtls_id and a.status_active=1 and a.defect_type_id in(1,2), pro_garments_production_mst b ,wo_po_break_down c, wo_po_details_master d, wo_po_color_size_breakdown  f
		where e.mst_id=b.id  and b.po_break_down_id=c.id and c.job_id=d.id and c.id=f.po_break_down_id and e.color_size_break_down_id=f.id and b.production_type=5 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 $sql_cond";// and e.operation_id>0
		$res_def = sql_select($deffect_sql); 
		$deffect_data_array = array();
		foreach ($res_def as $v) 
		{
			$line_id = "";
			if($v['PROD_RESO_ALLO']==1)
			{
				$line_id = $prod_reso_line_arr[$v['SEWING_LINE']];
				$line_resource_mst_arr=explode(",",$prod_reso_line_arr[$v['SEWING_LINE']]);
				$line_name="";
				foreach($line_resource_mst_arr as $resource_id)
				{
					$line_name.=$lineArr[$resource_id].", ";
				}
				$line_name=chop($line_name," , ");
			}
			else
			{
				$line_id = $v['SEWING_LINE'];
				$line_name=$lineArr[$v['SEWING_LINE']];
			}
			// echo $line_id;
			if($lineSerialArr[$line_id]=="")
			{
				$lastSlNo++;
				$slNo=$lastSlNo;
			}
			else $slNo=$lineSerialArr[$line_id];
			$deffect_data_array[$v['PRODUCTION_DATE']][$slNo][$line_name][$v['PO_ID']][$v['ITEM_ID']][$v['COLOR_ID']][$v['SIZE_ID']]['defect_qty'] += $v['DEFECT_QTY'];
		}
		// echo "<pre>";
		// print_r($deffect_data_array);
		// echo "</pre>";
		$sql = "SELECT d.buyer_name,d.style_ref_no,c.po_number,c.id as po_id,f.item_number_id as item_id,f.color_number_id as color_id,f.size_number_id as size_id, b.sewing_line,b.production_date,b.prod_reso_allo,e.operation_id,e.production_qnty,e.reject_qty,e.alter_qty,e.spot_qty,e.replace_qty,e.rectified_qty 
		from pro_garments_production_dtls e, pro_garments_production_mst b ,wo_po_break_down c, wo_po_details_master d, wo_po_color_size_breakdown  f
		where e.mst_id=b.id  and b.po_break_down_id=c.id and c.job_id=d.id and c.id=f.po_break_down_id and e.color_size_break_down_id=f.id and b.production_type=5 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 $sql_cond order by b.production_date,f.SIZE_ORDER";

		// echo $sql;die;
		$res = sql_select($sql); 
		$data_array = array();
		$operation_array = array();
		foreach ($res as $v) 
		{
			$line_id = "";
			if($v['PROD_RESO_ALLO']==1)
			{
				$line_id = $prod_reso_line_arr[$v['SEWING_LINE']];
				$line_resource_mst_arr=explode(",",$prod_reso_line_arr[$v['SEWING_LINE']]);
				$line_name="";
				foreach($line_resource_mst_arr as $resource_id)
				{
					$line_name.=$lineArr[$resource_id].", ";
				}
				$line_name=chop($line_name," , ");
			}
			else
			{
				$line_id = $v['SEWING_LINE'];
				$line_name=$lineArr[$v['SEWING_LINE']];
			}
			// echo $line_id;
			if($lineSerialArr[$line_id]=="")
			{
				$lastSlNo++;
				$slNo=$lastSlNo;
			}
			else $slNo=$lineSerialArr[$line_id];
			// echo $slNo."<br>";

			$data_array[$v['PRODUCTION_DATE']][$slNo][$line_name][$v['PO_ID']][$v['ITEM_ID']][$v['COLOR_ID']][$v['SIZE_ID']]['buyer_name'] = $v['BUYER_NAME'];
			$data_array[$v['PRODUCTION_DATE']][$slNo][$line_name][$v['PO_ID']][$v['ITEM_ID']][$v['COLOR_ID']][$v['SIZE_ID']]['style'] = $v['STYLE_REF_NO'];
			$data_array[$v['PRODUCTION_DATE']][$slNo][$line_name][$v['PO_ID']][$v['ITEM_ID']][$v['COLOR_ID']][$v['SIZE_ID']]['po_number'] = $v['PO_NUMBER'];

			$data_array[$v['PRODUCTION_DATE']][$slNo][$line_name][$v['PO_ID']][$v['ITEM_ID']][$v['COLOR_ID']][$v['SIZE_ID']]['chk_qty'] += $v['PRODUCTION_QNTY'];
			$data_array[$v['PRODUCTION_DATE']][$slNo][$line_name][$v['PO_ID']][$v['ITEM_ID']][$v['COLOR_ID']][$v['SIZE_ID']]['output_qty'] += $v['PRODUCTION_QNTY'];
			$data_array[$v['PRODUCTION_DATE']][$slNo][$line_name][$v['PO_ID']][$v['ITEM_ID']][$v['COLOR_ID']][$v['SIZE_ID']]['alter_qty'] += $v['ALTER_QTY'];
			$data_array[$v['PRODUCTION_DATE']][$slNo][$line_name][$v['PO_ID']][$v['ITEM_ID']][$v['COLOR_ID']][$v['SIZE_ID']]['spot_qty'] += $v['SPOT_QTY'];
			$data_array[$v['PRODUCTION_DATE']][$slNo][$line_name][$v['PO_ID']][$v['ITEM_ID']][$v['COLOR_ID']][$v['SIZE_ID']]['reject_qty'] += $v['REJECT_QTY'];
			
			$data_array[$v['PRODUCTION_DATE']][$slNo][$line_name][$v['PO_ID']][$v['ITEM_ID']][$v['COLOR_ID']][$v['SIZE_ID']]['replace_qty'] += $v['REPLACE_QTY'];
			$data_array[$v['PRODUCTION_DATE']][$slNo][$line_name][$v['PO_ID']][$v['ITEM_ID']][$v['COLOR_ID']][$v['SIZE_ID']]['rectified_qty'] += $v['RECTIFIED_QTY'];
		}	
		// echo "<pre>";print_r($data_array);die;

		$rowspan_arr = array();
		foreach ($data_array as $datekey => $date_data) 
		{
			foreach ($date_data as $slkey => $sl_data) 
			{
				foreach ($sl_data as $linekey => $line_data) 
				{
					foreach ($line_data as $pokey => $po_data) 
					{
						foreach ($po_data as $itemkey => $item_data) 
						{
							foreach ($item_data as $colorkey => $color_data) 
							{
								foreach ($color_data as $sizekey => $r) 
								{
									$rowspan_arr[$datekey][$slkey][$linekey][$pokey][$itemkey][$colorkey]++;
								}
							}
						}
					}
				}
			}
		}
		// echo "<pre>";print_r($rowspan_arr);die;			
		$tbl_width=1670;
		

		/* 
		@defect type for gross level entry(sewing output)
		@alter = 1
		@spot = 2
		@reject = 3
		*/
		ob_start();
		?>
		<fieldset>
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
			<table width="<? echo $tbl_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" id="" align="left">
				<thead>
					<tr>
						<th width="100">Date</th>
						<th width="100">Department</th>
						<th width="100">Line NO</th>
						<th width="130">Buyre</th>
						<th width="130">PO</th>
						<th width="130">Style</th>
						<th width="130">Item</th>
						<th width="130">Color</th>
						<th width="60">Size</th>
						<th width="60">Checked Pcs( Output)</th>
						<th width="60">Total Spot Qty</th>
						<th width="60">Total Spot % </th>
						<th width="60">FTT</th>
						<th width="60">Defectives </th>
						<th width="60">Defects</th>
						<th width="60">Rejects</th>
						<th width="60">Reworked</th>
						<th width="60">Actual Pcs</th>
						<th width="60">DHU</th>
						<th width="60">Defective Rate</th>
					</tr>
				</thead>
			</table>
			<div style="width:<? echo $tbl_width+20; ?>px; max-height:350px; overflow-y:scroll" id="scroll_body" > 
				<table width="<? echo $tbl_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="" align="left"><!--table_body-->
					<tbody>
						<?
						$i = 1;
						$gr_chk_qty = 0;
						$gr_ftt_qty = 0;
						$gr_defectives_qty = 0;
						$gr_defect_qty = 0;
						$gr_reject_qty = $gr_alter_qty =  $gr_spot_qty = 0;
						$gr_rework_qty = 0;
						$gr_actual_qty = 0;
						$gr_dhu = 0;
						$gr_act_rate = 0;
						foreach ($data_array as $datekey => $date_data) 
						{						
							$dt_chk_qty = 0;
							$dt_spot_qty = 0;
							$dt_ftt_qty = 0;
							$dt_defectives_qty = 0;
							$dt_defect_qty = 0;
							$dt_reject_qty = 0;
							$dt_rework_qty = 0;
							$dt_actual_qty = 0;
							$dt_dhu = 0;
							$dt_act_rate = 0;
							foreach ($date_data as $slkey => $sl_data) 
							{
								foreach ($sl_data as $linekey => $line_data) 
								{
									foreach ($line_data as $pokey => $po_data) 
									{
										foreach ($po_data as $itemkey => $item_data) 
										{
											foreach ($item_data as $colorkey => $color_data) 
											{
												$clr = 0;
												foreach ($color_data as $sizekey => $r) 
												{
													if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
													$rspan = $rowspan_arr[$datekey][$slkey][$linekey][$pokey][$itemkey][$colorkey];
													
													$chk_qty = $r['output_qty']+$r['reject_qty']+$r['alter_qty']+$r['spot_qty'];
													$check_title = $r['output_qty']."+".$r['reject_qty']."+".$r['alter_qty']."+".$r['spot_qty'];
													$ftt_qty = $r['output_qty'];
													
													$defect_qty = $deffect_data_array[$datekey][$slkey][$linekey][$pokey][$itemkey][$colorkey][$sizekey]['defect_qty'];//$r['defect_qty'];
													$reject_qty = $r['reject_qty'];
													$alter_qty = $r['alter_qty'];
													$spot_qty = $r['spot_qty'];
													$spot_per = ($chk_qty>0) ? ($spot_qty/$chk_qty)*100 : 0;
													$spot_per_title = "(".$spot_qty."/".$chk_qty.") X 100";
													$rework_qty = $r['rectified_qty'];
													$actual_qty = $r['output_qty']+$r['rectified_qty'];
													$defectives_qty = $chk_qty - $ftt_qty;
													$dhu = ($chk_qty>0) ? ($defect_qty/$chk_qty)*100 : 0;
													$dhu_title = "(".$defect_qty."/".$actual_qty.") X 100";
													$act_rate = ($actual_qty>0) ? ($defectives_qty/$actual_qty)*100 : 0;
													?>
													<tr bgcolor="<? echo $bgcolor;?>" id="tr_<?= $i;?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')">
													<? if($clr==0){?>
														<td rowspan="<?=$rspan;?>" width="100"><?=change_date_format($datekey);?></td>
														<td rowspan="<?=$rspan;?>" width="100">Sewing</td>
														<td rowspan="<?=$rspan;?>" width="100"><?=$linekey;?></td>
														<td rowspan="<?=$rspan;?>" width="130"><?=$buyer_library[$r['buyer_name']];?></td>
														<td rowspan="<?=$rspan;?>" width="130"><?=$r['po_number'];?></td>
														<td rowspan="<?=$rspan;?>" width="130"><?=$r['style'];?></td>
														<td rowspan="<?=$rspan;?>" width="130"><?=$garments_item[$itemkey];?></td>
														<td rowspan="<?=$rspan;?>" width="130"><?=$color_library[$colorkey];?></td>
														<?$clr++;}?>
														<td width="60"><?=$size_library[$sizekey];?></td>
														<td width="60" align="right" title="<?=$check_title;?>"><?=number_format($chk_qty,0);?></td>
														<td width="60" align="right"><?=number_format($spot_qty,0);?></td>
														<td width="60" align="right" title="<?= $spot_per_title; ?>"><?=number_format($spot_per,0);?></td>
														<td width="60" align="right"><?=number_format($ftt_qty,0);?></td>
														<td width="60" align="right" title="Actual Pcs <?= $actual_qty; ?> - FTT <?= $ftt_qty; ?>"><?=number_format($defectives_qty,0);?></td>
														<td width="60" align="right"><?=number_format($defect_qty,0);?></td>
														<td width="60" align="right"><?=number_format($reject_qty,0);?></td>
														<td width="60" align="right"><?=number_format($rework_qty,0);?></td>
														<td width="60" align="right"><?=number_format($actual_qty,0);?></td>
														<td width="60" align="right" title="<? echo $dhu_title; ?>"><?=number_format($dhu,2);?></td>
														<td width="60" align="right"><?=number_format($act_rate,2);?></td>
													</tr>
													<?
													$i++;

													$dt_chk_qty += $chk_qty;
													$dt_spot_qty += $spot_qty;
													$dt_ftt_qty += $ftt_qty;
													$dt_defectives_qty += $defectives_qty;
													$dt_defect_qty += $defect_qty;
													$dt_reject_qty += $reject_qty;
													$dt_rework_qty += $rework_qty;
													$dt_actual_qty += $actual_qty;
													// $dt_dhu += $dhu;
													$dt_act_rate += $act_rate;
													
													$gr_chk_qty += $chk_qty;
													$gr_ftt_qty += $ftt_qty;
													$gr_defectives_qty += $defectives_qty;
													$gr_defect_qty += $defect_qty;
													$gr_reject_qty += $reject_qty;
													$gr_rework_qty += $rework_qty;
													$gr_alter_qty += $alter_qty;
													$gr_spot_qty += $spot_qty;
													$gr_actual_qty += $actual_qty;
													$gr_dhu += $dhu;
													$gr_act_rate += $act_rate;
												}
											}
										}
									}
								}
							}
							// $dt_dhu = ($dt_actual_qty>0) ? ($dt_defect_qty/$dt_actual_qty)*100 : 0;
							$dt_dhu = ($dt_chk_qty>0) ? ($dt_defect_qty/$dt_chk_qty)*100 : 0;
							$dt_act_rate = ($dt_actual_qty>0) ? ($dt_defectives_qty/$dt_actual_qty)*100 : 0;
							$dt_spot_per = ($dt_chk_qty>0) ? ($dt_spot_qty/$dt_chk_qty)*100 : 0;
							$gr_check_title = $gr_ftt_qty."+".$gr_reject_qty."+".$gr_alter_qty."+".$gr_spot_qty;
							?>
							<tr style="background-color: #cddcdc;font-weight:bold;text-align:right;">
								<td colspan="9">Sub Total</td>
								<td><?=number_format($dt_chk_qty,0);?></td>
								<td><?=number_format($dt_spot_qty,0);?></td>
								<td><?=number_format($dt_spot_per,0);?></td>
								<td><?=number_format($dt_ftt_qty,0);?></td>
								<td><?=number_format($dt_defectives_qty,0);?></td>
								<td><?=number_format($dt_defect_qty,0);?></td>
								<td><?=number_format($dt_reject_qty,0);?></td>
								<td><?=number_format($dt_rework_qty,0);?></td>
								<td><?=number_format($dt_actual_qty,0);?></td>
								<td><?=number_format($dt_dhu,2);?></td>
								<td><?=number_format($dt_act_rate,2);?></td>
							</tr>
							<?	
						}
						// $gr_dhu = ($gr_actual_qty>0) ? ($gr_defect_qty/$gr_actual_qty)*100 : 0;
						$gr_dhu = ($gr_chk_qty>0) ? ($gr_defect_qty/$gr_chk_qty)*100 : 0;					
						$dt_act_rate = ($gr_actual_qty>0) ? ($gr_defectives_qty/$gr_actual_qty)*100 : 0;
						$gr_spot_per = ($gr_chk_qty>0) ? ($gr_spot_qty/$gr_chk_qty)*100 : 0; 
						$gr_spot_per_title = "(".$gr_spot_qty."/".$gr_chk_qty.") X 100";
						?>
					</tbody>
				</table>
			</div>
			<table width="<? echo $tbl_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" align="left">
				<tfoot>
					<tr>
						<th width="100"></th>
						<th width="100"></th>
						<th width="100"></th>
						<th width="130"></th>
						<th width="130"></th>
						<th width="130"></th>
						<th width="130"></th>
						<th width="130"></th>
						<th width="60">Grand Total</th>
						<th width="60" title="<?=$gr_check_title;?>"><?=number_format($gr_chk_qty,0);?></th>
						<th width="60" ><?=number_format($gr_spot_qty,0);?></th>
						<th width="60" title="<?=$gr_spot_per_title;?>"><?=number_format($gr_spot_per,0);?></th>
						<th width="60"><?=number_format($gr_ftt_qty,0);?></th>
						<th width="60"><?=number_format($gr_defectives_qty,0);?></th>
						<th width="60"><?=number_format($gr_defect_qty,0);?></th>
						<th width="60"><?=number_format($gr_reject_qty,0);?></th>
						<th width="60"><?=number_format($gr_rework_qty,0);?></th>
						<th width="60"><?=number_format($gr_actual_qty,0);?></th>
						<th width="60"><?=number_format($gr_dhu,2);?></th>
						<th width="60"><?=number_format($dt_act_rate,2);?></th>
					</tr>
				</tfoot>
			</table>
		</fieldset>
		<?		

	}

	if($type==2)
	{
		$lineDataArr = sql_select("select id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1
		order by sewing_line_serial");
		foreach($lineDataArr as $lRow)
		{
			$lineArr[$lRow[csf('id')]]=$lRow[csf('line_name')];
			$lineSerialArr[$lRow[csf('id')]]=$lRow[csf('sewing_line_serial')];
			$lastSlNo=$lRow[csf('sewing_line_serial')];
		}
		
		$company_id = str_replace("'","",$cbo_company_name);
		$wo_company_id = str_replace("'","",$cbo_working_company_id);
		$prod_type = str_replace("'","",$cbo_production_type);
		$buyer_id = str_replace("'","",$cbo_buyer_name);
		$location_id = str_replace("'","",$cbo_location);
		$floor_id = str_replace("'","",$cbo_floor);
		$job_no = str_replace("'","",$txt_job_no);
		$int_ref = str_replace("'","",$txt_int_ref);
		$line_id = str_replace("'","",$cbo_line_id);
		$po_no = str_replace("'","",$txt_po_no);
		$fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
		$toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );

		$sql_cond = "";
		$sql_cond .= ($company_id !=0) ? " and b.company_id=$company_id" : "";
		$sql_cond .= ($wo_company_id !=0) ? " and b.serving_company=$wo_company_id" : "";
		$sql_cond .= ($buyer_id !=0) ? " and d.buyer_name=$buyer_id" : "";
		$sql_cond .= ($location_id !=0) ? " and b.location=$location_id" : "";
		$sql_cond .= ($floor_id !=0) ? " and b.floor_id=$floor_id" : "";
		$sql_cond .= ($line_id !=0) ? " and b.sewing_line=$line_id" : "";
		$sql_cond .= ($job_no !="") ? " and d.job_no like '%$job_no%' " : "";
		$sql_cond .= ($int_ref !="") ? " and c.grouping='$int_ref'" : "";
		$sql_cond .= ($po_no !="") ? " and c.po_number='$po_no'" : "";
		if(str_replace("'","",trim($txt_date_from)) !="" && str_replace("'","",trim($txt_date_to)) !="")
		{
			$sql_cond .= " and b.production_date between $txt_date_from and $txt_date_to";
		}

		/* ===================================== query ===================================== */
		// $sql = "SELECT d.buyer_name,d.style_ref_no,c.po_number,c.id as po_id,f.item_number_id as item_id,f.color_number_id as color_id,f.size_number_id as size_id, b.sewing_line,b.production_date,b.prod_reso_allo,a.defect_type_id,a.defect_point_id,a.defect_qty,e.operation_id,e.production_qnty,e.reject_qty,e.alter_qty,e.spot_qty,e.replace_qty from pro_garments_production_dtls e, pro_garments_production_mst b left join pro_gmts_prod_dft a on b.id=a.mst_id and a.status_active=1 and a.defect_type_id in(1,2),wo_po_break_down c, wo_po_details_master d, wo_po_color_size_breakdown  f where e.id=a.dtls_id  and b.po_break_down_id=c.id and c.job_id=d.id and c.id=f.po_break_down_id and e.color_size_break_down_id=f.id and b.production_type=5 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 $sql_cond order by b.production_date,f.SIZE_ORDER";

		$deffect_sql = "SELECT d.buyer_name,d.style_ref_no,c.po_number,c.id as po_id,f.item_number_id as item_id,f.color_number_id as color_id,f.size_number_id as size_id, b.sewing_line,b.production_date,b.prod_reso_allo,a.defect_qty,a.defect_type_id
		from pro_garments_production_dtls e left join pro_gmts_prod_dft a on e.id=a.dtls_id and a.status_active=1 and a.defect_type_id in(1,2), pro_garments_production_mst b ,wo_po_break_down c, wo_po_details_master d, wo_po_color_size_breakdown  f
		where e.mst_id=b.id  and b.po_break_down_id=c.id and c.job_id=d.id and c.id=f.po_break_down_id and e.color_size_break_down_id=f.id and b.production_type=5 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 $sql_cond";// and e.operation_id>0
		$res_def = sql_select($deffect_sql); 
		$deffect_data_array = array();
		foreach ($res_def as $v) 
		{
			$line_id = "";
			if($v['PROD_RESO_ALLO']==1)
			{
				$line_id = $prod_reso_line_arr[$v['SEWING_LINE']];
				$line_resource_mst_arr=explode(",",$prod_reso_line_arr[$v['SEWING_LINE']]);
				$line_name="";
				foreach($line_resource_mst_arr as $resource_id)
				{
					$line_name.=$lineArr[$resource_id].", ";
				}
				$line_name=chop($line_name," , ");
			}
			else
			{
				$line_id = $v['SEWING_LINE'];
				$line_name=$lineArr[$v['SEWING_LINE']];
			}
			// echo $line_id;
			if($lineSerialArr[$line_id]=="")
			{
				$lastSlNo++;
				$slNo=$lastSlNo;
			}
			else $slNo=$lineSerialArr[$line_id];
			$deffect_data_array[$v['PRODUCTION_DATE']][$slNo][$line_name][$v['PO_ID']][$v['ITEM_ID']][$v['COLOR_ID']]['defect_qty'][$v['DEFECT_TYPE_ID']] += $v['DEFECT_QTY'];
		}
		// echo "<pre>";
		// print_r($deffect_data_array);
		// echo "</pre>";die;
		$sql = "SELECT d.buyer_name,d.style_ref_no,c.po_number,c.id as po_id,f.item_number_id as item_id,f.color_number_id as color_id,f.size_number_id as size_id, b.sewing_line,b.production_date,b.prod_reso_allo,e.operation_id,e.production_qnty,e.reject_qty,e.alter_qty,e.spot_qty,e.replace_qty,e.rectified_qty 
		from pro_garments_production_dtls e, pro_garments_production_mst b ,wo_po_break_down c, wo_po_details_master d, wo_po_color_size_breakdown  f
		where e.mst_id=b.id  and b.po_break_down_id=c.id and c.job_id=d.id and c.id=f.po_break_down_id and e.color_size_break_down_id=f.id and b.production_type=5 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 $sql_cond order by b.production_date,f.SIZE_ORDER";

		// echo $sql;die;
		$res = sql_select($sql); 
		$data_array = array();
		$operation_array = array();
		foreach ($res as $v) 
		{
			$line_id = "";
			if($v['PROD_RESO_ALLO']==1)
			{
				$line_id = $prod_reso_line_arr[$v['SEWING_LINE']];
				$line_resource_mst_arr=explode(",",$prod_reso_line_arr[$v['SEWING_LINE']]);
				$line_name="";
				foreach($line_resource_mst_arr as $resource_id)
				{
					$line_name.=$lineArr[$resource_id].", ";
				}
				$line_name=chop($line_name," , ");
			}
			else
			{
				$line_id = $v['SEWING_LINE'];
				$line_name=$lineArr[$v['SEWING_LINE']];
			}
			// echo $line_id;
			if($lineSerialArr[$line_id]=="")
			{
				$lastSlNo++;
				$slNo=$lastSlNo;
			}
			else $slNo=$lineSerialArr[$line_id];
			// echo $slNo."<br>";

			$data_array[$v['PRODUCTION_DATE']][$slNo][$line_name][$v['PO_ID']][$v['ITEM_ID']][$v['COLOR_ID']]['buyer_name'] = $v['BUYER_NAME'];
			$data_array[$v['PRODUCTION_DATE']][$slNo][$line_name][$v['PO_ID']][$v['ITEM_ID']][$v['COLOR_ID']]['style'] = $v['STYLE_REF_NO'];
			$data_array[$v['PRODUCTION_DATE']][$slNo][$line_name][$v['PO_ID']][$v['ITEM_ID']][$v['COLOR_ID']]['po_number'] = $v['PO_NUMBER'];

			$data_array[$v['PRODUCTION_DATE']][$slNo][$line_name][$v['PO_ID']][$v['ITEM_ID']][$v['COLOR_ID']]['chk_qty'] += $v['PRODUCTION_QNTY'];
			$data_array[$v['PRODUCTION_DATE']][$slNo][$line_name][$v['PO_ID']][$v['ITEM_ID']][$v['COLOR_ID']]['output_qty'] += $v['PRODUCTION_QNTY'];
			$data_array[$v['PRODUCTION_DATE']][$slNo][$line_name][$v['PO_ID']][$v['ITEM_ID']][$v['COLOR_ID']]['alter_qty'] += $v['ALTER_QTY'];
			$data_array[$v['PRODUCTION_DATE']][$slNo][$line_name][$v['PO_ID']][$v['ITEM_ID']][$v['COLOR_ID']]['spot_qty'] += $v['SPOT_QTY'];
			$data_array[$v['PRODUCTION_DATE']][$slNo][$line_name][$v['PO_ID']][$v['ITEM_ID']][$v['COLOR_ID']]['reject_qty'] += $v['REJECT_QTY'];
			
			$data_array[$v['PRODUCTION_DATE']][$slNo][$line_name][$v['PO_ID']][$v['ITEM_ID']][$v['COLOR_ID']]['replace_qty'] += $v['REPLACE_QTY'];
			$data_array[$v['PRODUCTION_DATE']][$slNo][$line_name][$v['PO_ID']][$v['ITEM_ID']][$v['COLOR_ID']]['rectified_qty'] += $v['RECTIFIED_QTY'];
		}	
		// echo "<pre>";print_r($data_array);die;

		// echo "<pre>";print_r($rowspan_arr);die;			
		$tbl_width=1730;
		

		/* 
		@defect type for gross level entry(sewing output)
		@alter = 1
		@spot = 2
		@reject = 3
		*/
		ob_start();
		?>
		<fieldset>
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
			<table width="<? echo $tbl_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" id="" align="left">
				<thead>
					<tr>
						<th width="100">Date</th>
						<th width="100">Department</th>
						<th width="100">Line NO</th>
						<th width="130">Buyre</th>
						<th width="130">PO</th>
						<th width="130">Style</th>
						<th width="130">Item</th>
						<th width="130">Color</th>
					
						<th width="60">Checked Pcs( Output)</th>
						<th width="60">Alter Qty</th>
						<th width="60">Alter % </th>
						<th width="60">Total Spot Qty</th>
						<th width="60">Total Spot % </th>
						<th width="60">FTT</th>
						<th width="60">Defectives </th>
						<th width="60">Defects</th>
						<th width="60">Rejects</th>
						<th width="60">Reworked</th>
						<th width="60">Actual Pcs</th>
						<th width="60">DHU</th>
						<th width="60">Defective Rate</th>
					</tr>
				</thead>
			</table>
			<div style="width:<? echo $tbl_width+20; ?>px; max-height:350px; overflow-y:scroll" id="scroll_body" > 
				<table width="<? echo $tbl_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="" align="left"><!--table_body-->
					<tbody>
						<?
						$i = 1;
						$gr_chk_qty = 0;
						$gr_ftt_qty = 0;
						$gr_defectives_qty = 0;
						$gr_defect_qty = 0;
						$gr_reject_qty = $gr_alter_qty =  $gr_spot_qty = $gr_alter_qty = 0;
						$gr_rework_qty = 0;
						$gr_actual_qty = 0;
						$gr_dhu = 0;
						$gr_act_rate = 0;
						foreach ($data_array as $datekey => $date_data) 
						{						
							$dt_chk_qty = 0;
							$dt_alter_qty = 0;
							$dt_spot_qty = 0;
							$dt_ftt_qty = 0;
							$dt_defectives_qty = 0;
							$dt_defect_qty = 0;
							$dt_reject_qty = 0;
							$dt_rework_qty = 0;
							$dt_actual_qty = 0;
							$dt_dhu = 0;
							$dt_act_rate = 0;
							foreach ($date_data as $slkey => $sl_data) 
							{
								foreach ($sl_data as $linekey => $line_data) 
								{
									foreach ($line_data as $pokey => $po_data) 
									{
										foreach ($po_data as $itemkey => $item_data) 
										{
											foreach ($item_data as $colorkey => $r) 
											{
												
													if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
													$chk_qty = $r['output_qty']+$r['reject_qty']+$r['alter_qty']+$r['spot_qty'];
													$check_title = $r['output_qty']."+".$r['reject_qty']."+".$r['alter_qty']."+".$r['spot_qty'];
													$ftt_qty = $r['output_qty'];
													
													$defect_qty_arr = $deffect_data_array[$datekey][$slkey][$linekey][$pokey][$itemkey][$colorkey]['defect_qty'];//$r['defect_qty'];
													$reject_qty = $r['reject_qty'];

													$alter_qty = $defect_qty_arr[1];
													$spot_qty = $defect_qty_arr[2];

													$alter_per = ($chk_qty>0) ? ($alter_qty/$chk_qty)*100 : 0;
													$alter_per_title = "(Alter qty/chk qty) X 100 = (".$alter_qty."/".$chk_qty.") X 100";

													$spot_per = ($chk_qty>0) ? ($spot_qty/$chk_qty)*100 : 0;
													$spot_per_title = "(spot qty/chk qty) X 100 = (".$spot_qty."/".$chk_qty.") X 100";
													$rework_qty = $r['rectified_qty'];
													$actual_qty = $r['output_qty']+$r['rectified_qty'];
													$defectives_qty = $chk_qty - $ftt_qty;
													$dhu = ($chk_qty>0) ? ($defect_qty/$chk_qty)*100 : 0;
													$dhu_title = "(".$defect_qty."/".$actual_qty.") X 100";
													$act_rate = ($actual_qty>0) ? ($defectives_qty/$actual_qty)*100 : 0;
													?>
													<tr bgcolor="<? echo $bgcolor;?>" id="tr_<?= $i;?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')">
													
														<td width="100"><?=change_date_format($datekey);?></td>
														<td width="100">Sewing</td>
														<td width="100"><?=$linekey;?></td>
														<td width="130"><p><?=$buyer_library[$r['buyer_name']];?></p></td>
														<td width="130"><p><?=$r['po_number'];?></p></td>
														<td width="130"><p><?=$r['style'];?></p></td>
														<td width="130"><p><?=$garments_item[$itemkey];?></p></td>
														<td width="130"><p><?=$color_library[$colorkey];?></p></td>	
														<td width="60" align="right" title="<?=$check_title;?>"><?=number_format($chk_qty,0);?></td>
														<td width="60" align="right"><?=number_format($alter_qty,0);?></td>
														<td width="60" align="right" title="<?= $alter_per_title; ?>"><?=number_format($alter_per,0);?></td>
														<td width="60" align="right"><?=number_format($spot_qty,0);?></td>
														<td width="60" align="right" title="<?= $spot_per_title; ?>"><?=number_format($spot_per,0);?></td>
														<td width="60" align="right"><?=number_format($ftt_qty,0);?></td>
														<td width="60" align="right" title="Actual Pcs <?= $actual_qty; ?> - FTT <?= $ftt_qty; ?>"><?=number_format($defectives_qty,0);?></td>
														<td width="60" align="right"><?=number_format($defect_qty,0);?></td>
														<td width="60" align="right"><?=number_format($reject_qty,0);?></td>
														<td width="60" align="right"><?=number_format($rework_qty,0);?></td>
														<td width="60" align="right"><?=number_format($actual_qty,0);?></td>
														<td width="60" align="right" title="<? echo $dhu_title; ?>"><?=number_format($dhu,2);?></td>
														<td width="60" align="right"><?=number_format($act_rate,2);?></td>
													</tr>
													<?
													$i++;

													$dt_chk_qty += $chk_qty;
													$dt_spot_qty += $spot_qty;
													$dt_alter_qty += $alter_qty;
													$dt_ftt_qty += $ftt_qty;
													$dt_defectives_qty += $defectives_qty;
													$dt_defect_qty += $defect_qty;
													$dt_reject_qty += $reject_qty;
													$dt_rework_qty += $rework_qty;
													$dt_actual_qty += $actual_qty;
													// $dt_dhu += $dhu;
													$dt_act_rate += $act_rate;
													
													$gr_chk_qty += $chk_qty;
													$gr_ftt_qty += $ftt_qty;
													$gr_defectives_qty += $defectives_qty;
													$gr_defect_qty += $defect_qty;
													$gr_reject_qty += $reject_qty;
													$gr_rework_qty += $rework_qty;
													$gr_alter_qty += $alter_qty;
													$gr_spot_qty += $spot_qty;
													$gr_actual_qty += $actual_qty;
													$gr_dhu += $dhu;
													$gr_act_rate += $act_rate;
												
											}
										}
									}
								}
							}
							// $dt_dhu = ($dt_actual_qty>0) ? ($dt_defect_qty/$dt_actual_qty)*100 : 0;
							$dt_dhu = ($dt_chk_qty>0) ? ($dt_defect_qty/$dt_chk_qty)*100 : 0;
							$dt_act_rate = ($dt_actual_qty>0) ? ($dt_defectives_qty/$dt_actual_qty)*100 : 0;
							$dt_spot_per = ($dt_chk_qty>0) ? ($dt_spot_qty/$dt_chk_qty)*100 : 0;
							$dt_alter_per = ($dt_chk_qty>0) ? ($dt_alter_qty/$dt_chk_qty)*100 : 0;
							$gr_check_title = $gr_ftt_qty."+".$gr_reject_qty."+".$gr_alter_qty."+".$gr_spot_qty;
							?>
							<tr style="background-color: #cddcdc;font-weight:bold;text-align:right;">
								<td ></td>
								<td></td>
								<td></td>
								<td></td>
								<td ></td>
								<td></td>
								<td></td>
								<td>Sub Total</td>
								<td><?=number_format($dt_chk_qty,0);?></td>
								<td><?=number_format($dt_alter_qty,0);?></td>
								<td><?=number_format($dt_alter_per,0);?></td>
								<td><?=number_format($dt_spot_qty,0);?></td>
								<td><?=number_format($dt_spot_per,0);?></td>
								<td><?=number_format($dt_ftt_qty,0);?></td>
								<td><?=number_format($dt_defectives_qty,0);?></td>
								<td><?=number_format($dt_defect_qty,0);?></td>
								<td><?=number_format($dt_reject_qty,0);?></td>
								<td><?=number_format($dt_rework_qty,0);?></td>
								<td><?=number_format($dt_actual_qty,0);?></td>
								<td><?=number_format($dt_dhu,2);?></td>
								<td><?=number_format($dt_act_rate,2);?></td>
							</tr>
							<?	
						}
						// $gr_dhu = ($gr_actual_qty>0) ? ($gr_defect_qty/$gr_actual_qty)*100 : 0;
						$gr_dhu = ($gr_chk_qty>0) ? ($gr_defect_qty/$gr_chk_qty)*100 : 0;					
						$dt_act_rate = ($gr_actual_qty>0) ? ($gr_defectives_qty/$gr_actual_qty)*100 : 0;

						$gr_alter_per = ($gr_chk_qty>0) ? ($gr_alter_qty/$gr_chk_qty)*100 : 0; 
						$gr_alter_per_title = "(".$gr_alter_qty."/".$gr_chk_qty.") X 100";

						$gr_spot_per = ($gr_chk_qty>0) ? ($gr_spot_qty/$gr_chk_qty)*100 : 0; 
						$gr_spot_per_title = "(".$gr_spot_qty."/".$gr_chk_qty.") X 100";
						?>
					</tbody>
				</table>
			</div>
			<table width="<? echo $tbl_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" align="left">
				<tfoot>
					<tr>
						<th width="100"></th>
						<th width="100"></th>
						<th width="100"></th>
						<th width="130"></th>
						<th width="130"></th>
						<th width="130"></th>
						<th width="130"></th>
						<th width="130">Grand Total</th>
						<th width="60" title="<?=$gr_check_title;?>"><?=number_format($gr_chk_qty,0);?></th>
						<th width="60" ><?=number_format($gr_alter_qty,0);?></th>
						<th width="60" title="<?=$gr_alter_per_title;?>"><?=number_format($gr_alter_per,0);?></th>
						<th width="60" ><?=number_format($gr_spot_qty,0);?></th>
						<th width="60" title="<?=$gr_spot_per_title;?>"><?=number_format($gr_spot_per,0);?></th>
						<th width="60"><?=number_format($gr_ftt_qty,0);?></th>
						<th width="60"><?=number_format($gr_defectives_qty,0);?></th>
						<th width="60"><?=number_format($gr_defect_qty,0);?></th>
						<th width="60"><?=number_format($gr_reject_qty,0);?></th>
						<th width="60"><?=number_format($gr_rework_qty,0);?></th>
						<th width="60"><?=number_format($gr_actual_qty,0);?></th>
						<th width="60"><?=number_format($gr_dhu,2);?></th>
						<th width="60"><?=number_format($dt_act_rate,2);?></th>
					</tr>
				</tfoot>
			</table>
		</fieldset>
		<?		

	}
	
	exit();	
}