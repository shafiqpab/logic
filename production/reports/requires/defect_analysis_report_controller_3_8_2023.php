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
	echo create_drop_down( "cbo_location", 80, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data[1]' order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/defect_analysis_report_controller', $data[1]+'_'+this.value+'_'+$('#cbo_production_type').val(), 'load_drop_down_floor', 'floor_td' );",0 );
	exit();     	
}

if ($action=="load_drop_down_floor")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_floor", 70, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and company_id=$data[0] and production_process='$data[2]' and location_id='$data[1]' order by floor_name","id,floor_name", 1, "-- Select --", $selected,  "load_drop_down( 'requires/defect_analysis_report_controller', $data[0]+'_'+$data[1]+'_'+this.value, 'load_drop_down_line', 'line_td' );",0); 
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
	$lineArr = return_library_array("select id,line_name from lib_sewing_line order by id","id","line_name"); 
	$prod_reso_line_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$sewing_floor_arr=return_library_array( "select id, floor_name from lib_prod_floor",'id','floor_name');	
	$operation_name_arr=return_library_array( "select id, OPERATION_NAME from lib_sewing_operation_entry",'id','OPERATION_NAME');	
	
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
	$fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
	$toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );

	$sql_cond = "";
	$sql_cond .= ($company_id !=0) ? " and a.company_id=$company_id" : "";
	$sql_cond .= ($wo_company_id !=0) ? " and a.serving_company=$wo_company_id" : "";
	$sql_cond .= ($buyer_id !=0) ? " and e.buyer_name=$buyer_id" : "";
	$sql_cond .= ($location_id !=0) ? " and a.location=$location_id" : "";
	$sql_cond .= ($floor_id !=0) ? " and a.floor_id=$floor_id" : "";
	$sql_cond .= ($line_id !=0) ? " and a.sewing_line=$line_id" : "";
	$sql_cond .= ($job_no !="") ? " and e.job_no='$job_no'" : "";
	$sql_cond .= ($int_ref !="") ? " and d.grouping='$int_ref'" : "";
	if(str_replace("'","",trim($txt_date_from)) !="" && str_replace("'","",trim($txt_date_to)) !="")
	{
		$sql_cond .= " and a.production_date between $txt_date_from and $txt_date_to";
	}

	/* ===================================== query ===================================== */
	/*$sql = "SELECT b.sewing_line,b.production_date,b.prod_reso_allo,a.defect_type_id,a.defect_point_id,a.defect_qty,e.operation_id,e.production_qnty,e.reject_qty,e.replace_qty from pro_garments_production_dtls e, pro_garments_production_mst b left join pro_gmts_prod_dft a on b.id=a.mst_id and a.status_active=1 and a.defect_type_id in(1,2),wo_po_break_down c, wo_po_details_master d where e.id=a.dtls_id  and b.po_break_down_id=c.id and c.job_id=d.id and b.production_type=5 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 $sql_cond";*/

	$sql = "SELECT A.SEWING_LINE,A.PRODUCTION_DATE,A.PROD_RESO_ALLO,C.OPERATION_ID,SUM(C.PRODUCTION_QNTY) PRODUCTION_QNTY,SUM(C.REJECT_QTY) REJECT_QTY,SUM(C.REPLACE_QTY) REPLACE_QTY,
	B.DEFECT_TYPE_ID,B.DEFECT_POINT_ID,SUM(B.DEFECT_QTY) AS DEFECT_QTY 
	FROM wo_po_details_master e, wo_po_break_down d,pro_garments_production_mst a, pro_garments_production_dtls c 
	LEFT JOIN pro_gmts_prod_dft b on c.id=b.dtls_id and b.status_active=1 and c.operation_id>0
	WHERE e.id=d.job_id and d.id=a.po_break_down_id and a.id=c.mst_id $sql_cond and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and a.PRODUCTION_TYPE=5
	group by A.SEWING_LINE,A.PRODUCTION_DATE,A.PROD_RESO_ALLO,C.OPERATION_ID,B.DEFECT_TYPE_ID,B.DEFECT_POINT_ID";

	// echo $sql; die;
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

		if($lineSerialArr[$line_id]=="")
		{
			$lastSlNo++;
			$slNo=$lastSlNo;
		}
		else $slNo=$lineSerialArr[$line_id];

		$data_array[$v['PRODUCTION_DATE']][$slNo][$line_name]['qc_qty'] += $v['PRODUCTION_QNTY']+$v['REPLACE_QTY']; //+$v['REPLACE_QTY']
		// if($v['DEFECT_POINT_ID']!="")
		// {
			$data_array[$v['PRODUCTION_DATE']][$slNo][$line_name][$v['DEFECT_TYPE_ID']][$v['DEFECT_POINT_ID']][$v['OPERATION_ID']]['qty'] += $v['DEFECT_QTY'];
			$data_array[$v['PRODUCTION_DATE']][$slNo][$line_name][$v['DEFECT_TYPE_ID']][$v['DEFECT_POINT_ID']][$v['OPERATION_ID']]['opid'] = $v['OPERATION_ID'];
		// }
		
		if($v['OPERATION_ID'] > 0)
		{
			$operation_array[$v['OPERATION_ID']] = $v['OPERATION_ID'];
		}
			
	}

	$rowspan_arr = array();
	foreach ($data_array as $datekey => $date_value) 
	{
		foreach ($date_value as $sl => $sl_value) 
		{
			foreach ($sl_value as $l_key => $l_value) 
			{
				foreach ($l_value as $type_key => $type_value) 
				{
					foreach ($type_value as $point_key => $row) 
					{
						$rowspan_arr[$datekey][$sl][$l_key]++;
					}
				}
			}
		}
	}
	// echo "<pre>";
	// print_r($data_array);
	// echo "</pre>";
	$tbl_width=(count($operation_array)*70)+610;

	ob_start();
	?>
	<fieldset style="margin: 0 auto;width:<? echo $tbl_width+20; ?>px;">
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
		<table width="<? echo $tbl_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" id="" align="left"  >
			<thead>
				<tr>
					<th width="70">Date</th>
					<th width="70">Department</th>
					<th width="70">Line NO</th>
					<th width="130">Issue Name</th>
					<?
					foreach ($operation_array as $op_id => $r) 
					{
						?>
						<th width="70"><?=$operation_name_arr[$op_id];?></th>
						<?
					}
					?>
					<th width="60">Total Defect</th>
					<th width="60">Checked Pcs</th>
					<th width="60">DHU</th>
				</tr>
			</thead>
			<tbody>
					<?
					$gr_dft_array = array();
					$gr_tot_qc_qty = 0;
					foreach ($data_array as $datekey => $date_value) 
					{
						ksort($date_value);
						foreach ($date_value as $sl => $sl_value) 
						{
							foreach ($sl_value as $l_key => $l_value) 
							{
								$l=0;
								$line_dft_array = array();
								$line_tot_qc_qty = 0;
								foreach ($l_value as $type_key => $type_value) 
								{
									foreach ($type_value as $point_key => $row) 
									{
										$issue_name = ($type_key==1) ? $sew_fin_alter_defect_type_for[$point_key] : $sew_fin_spot_defect_type_for[$point_key];
										if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										?>									
										<tr bgcolor="<? echo $bgcolor;?>" id="tr_<?= $i;?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')">
											<?if($l==0){?>
											<td rowspan="<?=$rowspan_arr[$datekey][$sl][$l_key];?>" width="70"><?=change_date_format($datekey);?></td>
											<td rowspan="<?=$rowspan_arr[$datekey][$sl][$l_key];?>" width="70">Sewing</td>
											<td rowspan="<?=$rowspan_arr[$datekey][$sl][$l_key];?>" width="70"><?=$l_key;?></td>
											<?}?>
											<td width="130"><?=$issue_name;?></td>
											<?
											$tot_dft = 0;
											foreach ($operation_array as $op_id => $r) 
											{
												?>
												<td align="right" width="70"><?=$row[$op_id]['qty'];?></td>
												<?
												$tot_dft += $row[$op_id]['qty'];
												$line_dft_array[$datekey][$sl][$l_key][$op_id] += $row[$op_id]['qty'];
												$gr_dft_array[$op_id] += $row[$op_id]['qty'];
											}
											?>
											<td align="right" width="60"><?=number_format($tot_dft,0);?></td>
											<?if($l==0)
											{
												?>
												
												<td rowspan="<?=$rowspan_arr[$datekey][$sl][$l_key];?>" align="right" width="60"><?=number_format($l_value['qc_qty'],0);?></td>
												<? $l++;
												$line_tot_qc_qty += $l_value['qc_qty'];
												$gr_tot_qc_qty += $l_value['qc_qty'];
											}
											
											$dhu = ($l_value['qc_qty']>0) ? ($tot_dft/$l_value['qc_qty'])*100 : 0;
											$dhu_title = $tot_dft." / ".$l_value['qc_qty']." X 100";
											?>
											<td align="right" width="60" title="<? echo $dhu_title; ?>"><?=number_format($dhu,2);?></td>
										</tr>
										<?
										$i++;
									}
								}
								?>
								<tr style="text-align: right;background:#cddcdc;font-weight:bold">
									<td></td>
									<td></td>
									<td></td>
									<td>Sub Total</td>
									<?
									$tot_dft = 0;
									foreach ($operation_array as $op_id => $r) 
									{
										?>
										<td align="right" width="150"><?=$line_dft_array[$datekey][$sl][$l_key][$op_id];?></td>
										<?
										$tot_dft += $line_dft_array[$datekey][$sl][$l_key][$op_id];
									}
									$line_dhu = ($line_tot_qc_qty>0) ? ($tot_dft/$line_tot_qc_qty)*100 : 0;
									?>
									<td><?=number_format($tot_dft,0);?></td>
									<td><?=number_format($line_tot_qc_qty,0);?></td>
									<td><?=number_format($line_dhu,2);?></td>
								</tr>
								<?
							}
						}
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<th width="100"></th>
						<th width="100"></th>
						<th width="100"></th>
						<th width="130">Grand Total</th>
						<?
						$tot_dft = 0;
						foreach ($operation_array as $op_id => $r) 
						{
							?>
							<th width="150"><?=$gr_dft_array[$op_id];?></th>
							<?
							$tot_dft += $gr_dft_array[$op_id];
						}
						$gr_dhu = ($gr_tot_qc_qty>0) ? ($tot_dft/$gr_tot_qc_qty)*100 : 0;
						?>
						<th width="60"><?=number_format($tot_dft,0);?></th>
						<th width="60"><?=number_format($gr_tot_qc_qty,0);?></th>
						<th width="60"><?=number_format($gr_dhu,2);?></th>
					</tr>
				</tfoot>
		</table>
	</fieldset>
	<?
    exit();
}