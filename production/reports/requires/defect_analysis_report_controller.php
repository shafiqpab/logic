<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------------------------------
if(!function_exists("fn_sort_by_decending"))
{
	function fn_sort_by_decending (&$array, $key) 
	{
		$sorter=array();
		$ret=array();
		reset($array);
		foreach ($array as $ii => $va) {
			$sorter[$ii]=$va[$key];
		}
		arsort($sorter);
		foreach ($sorter as $ii => $va) {
			$ret[$ii]=$array[$ii];
		}
		$array=$ret;
	}	
}
// fn_decending_sort($buyerDataArray,"qty");

if ($action=="load_drop_down_location")
{
	$data=explode("_",$data);
	// echo $data[1];//die;
	//echo "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name";die;
	echo create_drop_down( "cbo_location", 80, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data[1]' order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/defect_analysis_report_controller', $data[1]+'_'+this.value+'_'+$('#cbo_production_type').val(), 'load_drop_down_floor', 'floor_td' );fn_floor_multiselect()",0 );
	exit();     	
}

if ($action=="load_drop_down_floor")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_floor", 100, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and company_id=$data[0] and production_process='$data[2]' and location_id='$data[1]' order by floor_name","id,floor_name", 0, "-- Select --", $selected,  "load_drop_down( 'requires/defect_analysis_report_controller', $data[0]+'_'+$data[1]+'_'+this.value, 'load_drop_down_line', 'line_td' );",0); 
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
	$sql_cond .= ($floor_id !="") ? " and a.floor_id in($floor_id)" : "";
	$sql_cond .= ($line_id !=0) ? " and a.sewing_line=$line_id" : "";
	$sql_cond .= ($job_no !="") ? " and e.job_no='$job_no'" : "";
	$sql_cond .= ($int_ref !="") ? " and d.grouping='$int_ref'" : "";
	if(str_replace("'","",trim($txt_date_from)) !="" && str_replace("'","",trim($txt_date_to)) !="")
	{
		$sql_cond .= " and a.production_date between $txt_date_from and $txt_date_to";
	}

	/* ===================================== query ===================================== */
	/*$sql = "SELECT b.sewing_line,b.production_date,b.prod_reso_allo,a.defect_type_id,a.defect_point_id,a.defect_qty,e.operation_id,e.production_qnty,e.reject_qty,e.replace_qty from pro_garments_production_dtls e, pro_garments_production_mst b left join pro_gmts_prod_dft a on b.id=a.mst_id and a.status_active=1 and a.defect_type_id in(1,2),wo_po_break_down c, wo_po_details_master d where e.id=a.dtls_id  and b.po_break_down_id=c.id and c.job_id=d.id and b.production_type=5 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 $sql_cond";*/
	/*$sql = "SELECT A.SEWING_LINE,A.PRODUCTION_DATE,A.PROD_RESO_ALLO,a.floor_id,B.OPERATION_ID,SUM(C.PRODUCTION_QNTY) as PRODUCTION_QNTY,SUM(C.REJECT_QTY) as REJECT_QTY,SUM(C.REPLACE_QTY) as REPLACE_QTY,SUM(C.ALTER_QTY) as ALTER_QTY,SUM(C.SPOT_QTY) as SPOT_QTY,
	B.DEFECT_TYPE_ID,B.DEFECT_POINT_ID,SUM(B.DEFECT_QTY) AS DEFECT_QTY,e.buyer_name,e.style_ref_no,f.color_number_id as color_id,f.item_number_id as item_id,c.id as dtls_id 
	FROM wo_po_details_master e, wo_po_break_down d,WO_PO_COLOR_SIZE_BREAKDOWN f,pro_garments_production_mst a, pro_garments_production_dtls c 
	LEFT JOIN pro_gmts_prod_dft b on c.id=b.dtls_id and b.status_active=1 and b.is_deleted=0  and b.defect_type_id in(1,2)  and B.DEFECT_QTY>0 
	WHERE e.id=d.job_id and d.id=f.po_break_down_id and f.id=c.color_size_break_down_id and d.id=a.po_break_down_id and a.id=c.mst_id $sql_cond and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and e.status_active=1 and d.status_active=1 and f.status_active=1 and a.PRODUCTION_TYPE=5
	group by A.SEWING_LINE,A.PRODUCTION_DATE,A.PROD_RESO_ALLO,a.floor_id,b.OPERATION_ID,B.DEFECT_TYPE_ID,B.DEFECT_POINT_ID,e.buyer_name,e.style_ref_no,f.color_number_id,f.item_number_id,c.id order by A.PRODUCTION_DATE";// and c.operation_id>0 */

	$sql = "select X.SEWING_LINE,X.PRODUCTION_DATE,X.PROD_RESO_ALLO,X.floor_id,X.PRODUCTION_QNTY,X.REJECT_QTY,X.REPLACE_QTY,X.RECTIFIED_QTY,X.ALTER_QTY,X.SPOT_QTY,X.buyer_name,X.style_ref_no,X.color_id,X.item_id,X.dtls_id,b.OPERATION_ID,B.DEFECT_TYPE_ID,B.DEFECT_POINT_ID,SUM(B.DEFECT_QTY) AS DEFECT_QTY from (SELECT A.SEWING_LINE,A.PRODUCTION_DATE,A.PROD_RESO_ALLO,a.floor_id,SUM(C.PRODUCTION_QNTY) as PRODUCTION_QNTY,SUM(C.REJECT_QTY) as REJECT_QTY,SUM(C.REPLACE_QTY) as REPLACE_QTY,SUM(C.RECTIFIED_QTY) as RECTIFIED_QTY,SUM(C.ALTER_QTY) as ALTER_QTY,SUM(C.SPOT_QTY) as SPOT_QTY,e.buyer_name,e.style_ref_no,f.color_number_id as color_id,f.item_number_id as item_id,c.id as dtls_id FROM wo_po_details_master e, wo_po_break_down d,WO_PO_COLOR_SIZE_BREAKDOWN f,pro_garments_production_mst a, pro_garments_production_dtls c WHERE e.id=d.job_id and d.id=f.po_break_down_id and f.id=c.color_size_break_down_id and d.id=a.po_break_down_id and a.id=c.mst_id  $sql_cond and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and e.status_active=1 and d.status_active=1 and f.status_active=1 and a.PRODUCTION_TYPE=5 group by A.SEWING_LINE,A.PRODUCTION_DATE,A.PROD_RESO_ALLO,a.floor_id,e.buyer_name,e.style_ref_no,f.color_number_id,f.item_number_id,c.id ) x LEFT JOIN pro_gmts_prod_dft b on x.dtls_id=b.dtls_id and b.status_active=1 and b.is_deleted=0 and b.defect_type_id in(1,2) and B.DEFECT_QTY>0 group by X.SEWING_LINE,X.PRODUCTION_DATE,X.PROD_RESO_ALLO,X.floor_id,X.PRODUCTION_QNTY,X.REJECT_QTY,X.REPLACE_QTY,X.RECTIFIED_QTY,X.ALTER_QTY,X.SPOT_QTY,X.buyer_name,X.style_ref_no,X.color_id,X.item_id,X.dtls_id,b.OPERATION_ID,B.DEFECT_TYPE_ID,B.DEFECT_POINT_ID order by X.PRODUCTION_DATE";// and c.operation_id>0

	// echo $sql; die;
	$res = sql_select($sql); 
	if(count($res)<1)
	{
		echo "<div style='color:red;text-align:center;font-size:18px;'>Data Not Found!</div>";die;
	}
	$data_array = array();
	$operation_array = array();
	$chk_array = array();
	$floor_name_array = array();
	$dtls_id_array = array();
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
		if($dtls_id_array[$v['DTLS_ID']]=="")
		{
			$data_array[$v['PRODUCTION_DATE']][$slNo][$line_name]['qc_qty'] += $v['PRODUCTION_QNTY'];//+$v['REPLACE_QTY'];
			$data_array[$v['PRODUCTION_DATE']][$slNo][$line_name]['chk_qty'] += ($v['PRODUCTION_QNTY']+$v['REJECT_QTY']+$v['ALTER_QTY']+$v['SPOT_QTY']);
			$data_array[$v['PRODUCTION_DATE']][$slNo][$line_name]['reject_qty'] += $v['REJECT_QTY'];
			$data_array[$v['PRODUCTION_DATE']][$slNo][$line_name]['replace_qty'] += $v['REPLACE_QTY'];
			$data_array[$v['PRODUCTION_DATE']][$slNo][$line_name]['rectified_qty'] += $v['RECTIFIED_QTY'];
			$dtls_id_array[$v['DTLS_ID']] = $v['DTLS_ID'];
		}
		
		$data_array[$v['PRODUCTION_DATE']][$slNo][$line_name]['buyer_name'] .= $v['BUYER_NAME'].",";
		$data_array[$v['PRODUCTION_DATE']][$slNo][$line_name]['style'] .= $v['STYLE_REF_NO'].",";
		$data_array[$v['PRODUCTION_DATE']][$slNo][$line_name]['item_id'] .= $v['ITEM_ID'].",";
		$data_array[$v['PRODUCTION_DATE']][$slNo][$line_name]['color_id'] .= $v['COLOR_ID'].",";
		if($v['DEFECT_POINT_ID']>0)
		{
			if($v['DEFECT_TYPE_ID']==1)// alter
			{
				
				$data_array[$v['PRODUCTION_DATE']][$slNo][$line_name][0][$sew_fin_alter_defect_type[$v['DEFECT_POINT_ID']]][$v['OPERATION_ID']]['qty'] += $v['DEFECT_QTY'] - $v['REPLACE_QTY'];
				$data_array[$v['PRODUCTION_DATE']][$slNo][$line_name][0][$sew_fin_alter_defect_type[$v['DEFECT_POINT_ID']]][$v['OPERATION_ID']]['opid'] = $v['OPERATION_ID'];
			}
			else // spot
			{
				$data_array[$v['PRODUCTION_DATE']][$slNo][$line_name][0][$sew_fin_spot_defect_type[$v['DEFECT_POINT_ID']]][$v['OPERATION_ID']]['qty'] += $v['DEFECT_QTY'] - $v['REPLACE_QTY'];
				$data_array[$v['PRODUCTION_DATE']][$slNo][$line_name][0][$sew_fin_spot_defect_type[$v['DEFECT_POINT_ID']]][$v['OPERATION_ID']]['opid'] = $v['OPERATION_ID'];
			}
			
		}

		$data_array2[$v['PRODUCTION_DATE']][$slNo][$line_name][$v['DEFECT_TYPE_ID']][$v['DEFECT_POINT_ID']][$v['OPERATION_ID']]['qty'] += $v['DEFECT_QTY'] - $v['REPLACE_QTY'];
		$data_array2[$v['PRODUCTION_DATE']][$slNo][$line_name][$v['DEFECT_TYPE_ID']][$v['DEFECT_POINT_ID']][$v['OPERATION_ID']]['opid'] = $v['OPERATION_ID'];

		
		if($v['OPERATION_ID'] > 0 && ($v['PRODUCTION_QNTY']+$v['REJECT_QTY']+$v['DEFECT_QTY'])>0)
		{
			$operation_array[$v['OPERATION_ID']] = $v['OPERATION_ID'];
		}
		$floor_name_array[$sewing_floor_arr[$v['FLOOR_ID']]] = $sewing_floor_arr[$v['FLOOR_ID']];
			
	}
	// echo "<pre>";print_r($data_array);	echo "</pre>";die;
	$rowspan_arr = array();
	$dft_count_arr = array();
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
						foreach ($operation_array as $op_id => $r) 
						{
							$dft_count_arr[$datekey][$l_key] += $row[$op_id]['qty'];
							$data_array[$datekey][$sl][$l_key][0][$point_key]['tot_dft_qty'] += $row[$op_id]['qty'];
							$chk_dft_array[$datekey][$sl][$l_key]['tot_dft_qty'] += $row[$op_id]['qty'];
						}
					}
				}
			}
		}
	}

	// This loop for when defect qty is empty
	foreach ($data_array as $datekey => $date_value) 
	{
		foreach ($date_value as $sl => $sl_value) 
		{
			foreach ($sl_value as $l_key => $l_value) 
			{
				// print_r($l_value);
				if($chk_dft_array[$datekey][$sl][$l_key]['tot_dft_qty']<1)
				{
					$data_array[$datekey][$sl][$l_key][0][0]['tot_dft_qty'] = 0;
				}

			}
		}
	}

	// echo "<pre>";print_r($data_array);	echo "</pre>";die;

	foreach ($data_array2 as $datekey => $date_value) 
	{
		foreach ($date_value as $sl => $sl_value) 
		{
			foreach ($sl_value as $l_key => $l_value) 
			{
				foreach ($l_value as $type_key => $type_value) 
				{
					foreach ($type_value as $point_key => $row) 
					{
						// $rowspan_arr[$datekey][$sl][$l_key]++;
						foreach ($operation_array as $op_id => $r) 
						{
							$sew_defect_qty_arr[$type_key][$point_key] += $row[$op_id]['qty'];
						}
					}
				}
			}
		}
	}

	// ========================= prepare graph data =========================
	$alter_defect_point=0;
	foreach($sew_defect_qty_arr as $typeId=>$typeData)
	{
		foreach($typeData as $pointId=>$val_alter)
		{
			if($typeId==1) //Alter check
			{
				$defect_point=$sew_fin_alter_defect_type[$pointId];
				$alter_defect_point+=$val_alter;
			}
			elseif($typeId==2) //Spot check
			{
				$defect_point=$sew_fin_spot_defect_type[$pointId];
			}
			$defect_check_arr[$defect_point]+=$val_alter;
			$defect_check_arr2[$defect_point]+=$val_alter;
		}
	}
	asort($defect_check_arr);
	$defect_check_arrNew = array_slice($defect_check_arr, -10);
	$t=1;
	$top_ten_percent_graph=array();
	$sewing_defect_top_qty=0;
	foreach($defect_check_arrNew as $key_type=>$top_defect_qty)
	{
		$top_ten_percent_graph[] = array('y'=>$top_defect_qty,'label'=>$key_type);
		
	}

	$pareto_graph_data = array();
	asort($defect_check_arr2);
	$defect_check_arrNew = array_slice($defect_check_arr2, -10);
	arsort($defect_check_arrNew);
	foreach($defect_check_arrNew as $key_type=>$top_defect_qty)
	{
		$pareto_graph_data[] = array('y'=>$top_defect_qty,'label'=>$key_type);
		
	}

	// $top_ten_percent_graphArr[]= array_slice($top_ten_percent_graph, 0, 10);
	// $top_ten_percent_graphArr_chk = array_shift($top_ten_percent_graphArr);

	// echo "<pre>";print_r($data_array);	echo "</pre>";

	$tbl_width=(count($operation_array)*70)+1430;

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
					<th width="100">Buyer</th>
					<th width="100">Style Ref.</th>
					<th width="100">Item</th>
					<th width="100">Color</th>
					<th width="70">Check Qty</th>
					<th width="70">OK Qty. (FTT)</th>
					<th width="70">Count Of Defect (Alt+Sp)</th>
					<th width="70" title="(Number of Gmts Defect Qty / Check Qty)*100">Gmts Defect %</th>
					<th width="70">Reject Qty</th>
					<th width="70" title="(Number of Gmts Reject Qty / Check Qty)*100">Gmts Reject %</th>
					<th width="130">Issue Name</th>
					<?
					foreach ($operation_array as $op_id => $r) 
					{
						?>
						<th width="70"><?=$operation_name_arr[$op_id];?></th>
						<?
					}
					?>
					<th width="60">Total Count of Defect</th>
					<!-- <th width="60">Checked Pcs</th> -->
					<th width="60" title="(Number of Defect Count  / Check Qty)*100">DHU%</th>
				</tr>
			</thead>
		</table>
		<div style="width:<?= $tbl_width+20;?>px; max-height:300px; overflow-y:auto" id="scroll_body">
			<table width="<? echo $tbl_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" id="" align="left">
				<tbody>
					<?
					$gr_dft_array = array();
					$gr_tot_qc_qty = 0;
					$gr_chk_qty = 0;
					$gr_ok_qty = 0;
					$gr_dft_count_qty = 0;
					$gr_dft_prsnt = 0;
					$gr_rej_qty = 0;
					$gr_rej_prsnt = 0;
					$gr_replace_qty = 0;
					foreach ($data_array as $datekey => $date_value) 
					{
						ksort($date_value);
						foreach ($date_value as $sl => $sl_value) 
						{
							foreach ($sl_value as $l_key => $l_value) 
							{
								$l_chk_qty = 0;
								$l_ok_qty = 0;
								$l_replace_qty = 0;
								$l_dft_count_qty = 0;
								$l_dft_prsnt = 0;
								$l_rej_qty = 0;
								$l_rej_prsnt = 0;

								$buyer_name = "";
								$b_arr = array_unique(array_filter(explode(",",$l_value['buyer_name'])));
								foreach ($b_arr as $v) 
								{
									$buyer_name .= ($buyer_name=="") ? $buyer_library[$v] : ", ".$buyer_library[$v];
								}
								$style_name = "";
								$b_arr = array_unique(array_filter(explode(",",$l_value['style'])));
								foreach ($b_arr as $v) 
								{
									$style_name .= ($style_name=="") ? $v : ", ".$v;
								}
								$item_name = "";
								$b_arr = array_unique(array_filter(explode(",",$l_value['item_id'])));
								foreach ($b_arr as $v) 
								{
									$item_name .= ($item_name=="") ? $garments_item[$v] : ", ".$garments_item[$v];
								}
								$color_name = "";
								$b_arr = array_unique(array_filter(explode(",",$l_value['color_id'])));
								foreach ($b_arr as $v) 
								{
									$color_name .= ($color_name=="") ? $color_library[$v] : ", ".$color_library[$v];
								}

								$reject_prsnt = ($l_value['chk_qty']>0) ? ($l_value['reject_qty']/$l_value['chk_qty'])*100 : 0;
								$dft_count = $dft_count_arr[$datekey][$l_key];
								$dft_prsnt = ($l_value['chk_qty']>0) ? ($dft_count/$l_value['chk_qty'])*100 : 0;

								$l=0;
								$line_dft_array = array();
								$line_tot_qc_qty = 0;
								// array_multisort(array_map(function($element) {
								// 	return $element['tot_dft_qty'];
								// }, $l_value), SORT_DESC, $l_value);
								foreach ($l_value as $type_key => $type_value) 
								{
									// $data_array[$datekey][$sl][$l_key][$type_key][$point_key]['tot_dft_qty'];
									fn_sort_by_decending($type_value,"tot_dft_qty");
									foreach ($type_value as $point_key => $row) 
									{
										// $issue_name = ($type_key==1) ? $sew_fin_alter_defect_type_for[$point_key] : $sew_fin_spot_defect_type_for[$point_key];
										$issue_name = $point_key;
										if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										?>									
										<tr bgcolor="<? echo $bgcolor;?>" id="tr_<?= $i;?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')">
											<?if($l==0){?>
											<td valign="middle" rowspan="<?=$rowspan_arr[$datekey][$sl][$l_key];?>" width="70"><?=change_date_format($datekey);?></td>
											<td valign="middle" rowspan="<?=$rowspan_arr[$datekey][$sl][$l_key];?>" width="70">Sewing</td>
											<td valign="middle" rowspan="<?=$rowspan_arr[$datekey][$sl][$l_key];?>" width="70"><?=$l_key;?></td>
											<td valign="middle" rowspan="<?=$rowspan_arr[$datekey][$sl][$l_key];?>" width="100"><p><?=$buyer_name;?></p></td>
											<td valign="middle" rowspan="<?=$rowspan_arr[$datekey][$sl][$l_key];?>" width="100"><p><?=$style_name;?></p></td>
											<td valign="middle" rowspan="<?=$rowspan_arr[$datekey][$sl][$l_key];?>" width="100"><p><?=$item_name;?></p></td>
											<td valign="middle" rowspan="<?=$rowspan_arr[$datekey][$sl][$l_key];?>" width="100"><p><?=$color_name;?></p></td>
											<td align="right" valign="middle" rowspan="<?=$rowspan_arr[$datekey][$sl][$l_key];?>" width="70"><?=number_format($l_value['chk_qty'],0);?></td>
											<td align="right" valign="middle" rowspan="<?=$rowspan_arr[$datekey][$sl][$l_key];?>" width="70"><?=number_format($l_value['qc_qty'],0);?></td>
											<td align="right" valign="middle" rowspan="<?=$rowspan_arr[$datekey][$sl][$l_key];?>" width="70"><?=number_format($dft_count,0);?></td>
											<td align="right" valign="middle" rowspan="<?=$rowspan_arr[$datekey][$sl][$l_key];?>" width="70"><?=number_format($dft_prsnt,4);?>%</td>
											<td align="right" valign="middle" rowspan="<?=$rowspan_arr[$datekey][$sl][$l_key];?>" width="70"><?=number_format($l_value['reject_qty'],0);?></td>
											<td align="right" valign="middle" rowspan="<?=$rowspan_arr[$datekey][$sl][$l_key];?>" width="70"><?=number_format($reject_prsnt,4);?>%</td>
											<?}?>
											<td width="130" title="<?=$point_key;?>"><?=$issue_name;?></td>
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
											<? if($l==0)
											{
												?>
												
												<!-- <td valign="middle" rowspan="<?//=$rowspan_arr[$datekey][$sl][$l_key];?>" align="right" width="60"><?//=number_format($l_value['qc_qty'],0);?></td> -->
												<? //$l++;
												$line_tot_qc_qty += $l_value['qc_qty'];
												$gr_tot_qc_qty += $l_value['qc_qty'];

												$l_chk_qty += $l_value['chk_qty'];
												$l_ok_qty += $l_value['qc_qty'];
												$l_replace_qty += $l_value['replace_qty'];
												$l_dft_count_qty += $dft_count;
												$l_dft_prsnt += ($l_value['chk_qty']>0) ? ($dft_count/$l_value['chk_qty'])*100 : 0;
												$l_rej_qty += $l_value['reject_qty'];
												$l_rej_prsnt += $reject_prsnt = ($l_value['chk_qty']>0) ? ($l_value['reject_qty']/$l_value['chk_qty'])*100 : 0;

												$gr_chk_qty += $l_value['chk_qty'];
												$gr_ok_qty += $l_value['qc_qty'];
												$gr_replace_qty += $l_value['replace_qty'];
												$gr_dft_count_qty += $dft_count;
												// $gr_dft_prsnt += ($l_value['chk_qty']>0) ? ($dft_count/$l_value['chk_qty'])*100 : 0;
												$gr_rej_qty += $l_value['reject_qty'];
												// $gr_rej_prsnt += $reject_prsnt = ($l_value['chk_qty']>0) ? ($l_value['reject_qty']/$l_value['chk_qty'])*100 : 0;

											} 
											
											$dhu = ($l_value['chk_qty']>0) ? ($tot_dft/$l_value['chk_qty'])*100 : 0;
											$dhu_title = "(".$tot_dft." / ".$l_value['chk_qty'].") X 100";
											?>
											<td align="right" width="60" title="<? echo $dhu_title; ?>"><?=number_format($dhu,4);?>%</td>
										</tr>
										<?
										$i++;
										$l++;
									}
								}
								if($line_tot_qc_qty>0)
								{
									// echo"(".$l_chk_qty.">0) ? (".$l_dft_count_qty."/".$l_chk_qty.")*100<br>";
									?>
									<tr style="text-align: right;background:#cddcdc;font-weight:bold">
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td><?=number_format($l_chk_qty,0);?></td>
										<td><?=number_format($l_ok_qty,0);?></td>
										<td><?=number_format($l_dft_count_qty,0);?></td>
										<td><?=number_format($l_dft_prsnt,4);?>%</td>
										<td><?=number_format($l_rej_qty,0);?></td>
										<td><?=number_format($l_rej_prsnt,4);?>%</td>
										<td>Sub Total</td>
										<?
										$tot_dft = 0;
										foreach ($operation_array as $op_id => $r) 
										{
											?>
											<td align="right" width="70"><?=$line_dft_array[$datekey][$sl][$l_key][$op_id];?></td>
											<?
											$tot_dft += $line_dft_array[$datekey][$sl][$l_key][$op_id];
										}
										$line_dhu = ($l_chk_qty>0) ? ($tot_dft/$l_chk_qty)*100 : 0;
										?>
										<td><?=number_format($tot_dft,0);?></td>
										<!-- <td><?//=number_format($line_tot_qc_qty,0);?></td> -->
										<td><?=number_format($line_dhu,4);?>%</td>
									</tr>
									<?
								}
							}
						}
					}
					// echo"(".$gr_chk_qty.">0) ? (".$gr_dft_count_qty."/".$gr_chk_qty.")*100<br>";
					$gr_dft_prsnt = ($gr_chk_qty>0) ? ($gr_dft_count_qty/$gr_chk_qty)*100 : 0;
					$gr_rej_prsnt = ($gr_chk_qty>0) ? ($gr_rej_qty/$gr_chk_qty)*100 : 0;
					// echo"(".$gr_chk_qty.">0) ? (".$gr_rej_qty."/".$gr_chk_qty.")*100<br>";
					unset($data_array);
					unset($data_array2);
					?>
				</tbody>
			</table>
		</div>	
		<table width="<? echo $tbl_width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" id="" align="left">
			<tfoot>
				<tr>
					<th width="70"></th>
					<th width="70"></th>
					<th width="70"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>					
					<th width="70"><?=number_format($gr_chk_qty,0);?></th>
					<th width="70"><?=number_format($gr_ok_qty,0);?></th>
					<th width="70"><?=number_format($gr_dft_count_qty,0);?></th>
					<th width="70"><?=number_format($gr_dft_prsnt,4);?>%</th>
					<th width="70"><?=number_format($gr_rej_qty,0);?></th>
					<th width="70"><?=number_format($gr_rej_prsnt,4);?>%</th>
					<th width="130">Grand Total</th>
					<?
					$tot_dft = 0;
					foreach ($operation_array as $op_id => $r) 
					{
						?>
						<th width="70"><?=$gr_dft_array[$op_id];?></th>
						<?
						$tot_dft += $gr_dft_array[$op_id];
					}
					$gr_dhu = ($gr_chk_qty>0) ? ($tot_dft/$gr_chk_qty)*100 : 0;
					// echo"(".$gr_tot_qc_qty.">0) ? (".$tot_dft."/".$gr_tot_qc_qty.")*100<br>";
					?>
					<th width="60"><?=number_format($tot_dft,0);?></th>
					<!-- <th width="60"><?//=number_format($gr_tot_qc_qty,0);?></th> -->
					<th width="60"><?=number_format($gr_dhu,4);?>%</th>
				</tr>
			</tfoot>
		</table>
	</fieldset>
	<script>
    function showChart() {
    //   alert(data);
     var chart = new CanvasJS.Chart("chartContainer", {
         animationEnabled: true,
         title:{
             text: "Sewing Top 10 Defects Ranked by Number of Defect on Style",
			 fontSize: 16
         },
         axisY: {
             title: "Defects",
             includeZero: true,
             prefix: "",
             suffix:  ""
         },
         data: [{
             type: "bar",
             yValueFormatString: "",//"$#,##0K",
             indexLabel: "{y}",
             indexLabelPlacement: "inside",
             indexLabelFontWeight: "bolder",
             indexLabelFontColor: "white",
             dataPoints: <?php echo json_encode($top_ten_percent_graph, JSON_NUMERIC_CHECK); ?>
         }]
     });
     chart.render();
      
     }

	 
	 function showChart2() 
	 {
			//   alert(data);
			var chart = new CanvasJS.Chart("paretoChartContainer", {
			title:{
				text: "Floor wise(<?=implode(',',$floor_name_array);?>)",
				fontSize: 16
			},
			axisY: {
				title: "Number of Sewing Defect",
				lineColor: "#4F81BC",
				tickColor: "#4F81BC",
				labelFontColor: "#4F81BC",
				gridThickness: 0
			},
			axisY2: {
				title: "Defect %",
				suffix: "%",
				gridThickness: 0,
				lineColor: "#C0504E",
				tickColor: "#C0504E",
				labelFontColor: "#C0504E"
			},
			data: [{
				type: "column",
				dataPoints: <?php echo json_encode($pareto_graph_data, JSON_NUMERIC_CHECK); ?>
			}]
		});
		chart.render();
		createPareto();    
		function createPareto()
		{
			var dps = [];
			var yValue, yTotal = 0, yPercent = 0;

			for(var i = 0; i < chart.data[0].dataPoints.length; i++)
				yTotal += chart.data[0].dataPoints[i].y;

			for(var i = 0; i < chart.data[0].dataPoints.length; i++) {
				yValue = chart.data[0].dataPoints[i].y;
				yPercent += (yValue / yTotal * 100);
				dps.push({label: chart.data[0].dataPoints[i].label, y: yPercent });
			}
			chart.addTo("data", {type:"line", axisYType: "secondary", yValueFormatString: "0.##\"%\"", indexLabel: "{y}", indexLabelFontColor: "#C24642", dataPoints: dps});
			// chart.axisY[0].set("maximum", yTotal, false);
			chart.axisY[0].set("maximum", yTotal, false);
			chart.axisY2[0].set("maximum", 100, false );
			chart.axisY2[0].set("interval", 10 );
		}  
    }
	
    </script>
	<?
	echo "****";
    exit();
}